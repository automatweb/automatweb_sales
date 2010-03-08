<?php
	// m22ratleme serverid, millel on lubatud koopiat tellida
	$allowed_servers = file("http://sitecopy.struktuur.ee/import/?list_allowed_servers=1");
	$passed = 0;
	$as = explode(" ", $allowed_servers[0]);
	foreach ($as as $server)
	{
		if ($server == $_SERVER['REMOTE_ADDR'])
		{
			$passed = 1;
			$allowed_server = $_SERVER['REMOTE_ADDR'];
		}
	}
	if ($passed == 1)
	{
		// meil teele kui miskit vaja teatada
		function report_status($reciver, $message)
		{
			$message .= "\nSkript: ".__FILE__." @ ".$_SERVER['SERVER_NAME'];
			$from = 'From: sitecopy@automatweb.com';
			mail($reciver, 'AW Site Copy', $message, $from);
		}

		function protect_folder($folder, $allowed)
		{
			$htaccess_content = "Order Deny,Allow\nDeny from All\nAllow from $allowed";
			$fp = fopen($folder."/.htaccess", "w");
			fwrite($fp, $htaccess_content);
			fclose($fp);
		}

		function chmod2($dir)
		{
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if ($file == "." || $file == "..")
					{
						continue;
					}
					if (is_dir($dir."/".$file))
					{
						chmod2($dir."/".$file);
					}
					else
					{
						chmod($dir."/".$file, 0666);
					}
				}
				closedir($dh);
				chmod($dir, 0777);
			}
		}

		function count_files_in_dir($directory, $pattern = "0x0")
		{
			$p = $pattern;
			$dir = $directory;
			$filecount = 0;
			$d = dir($dir);
			if ($pattern != "0x0")
			{
				while ($f = $d->read()) {
					if(substr($f, 0, 8) == $p) {
						$filecount++;
					}
				}
			}
			return $filecount;
		}
		
		// id edasiseseks tegutsemiseks ja teavitamiseks
		if (isset($_REQUEST['uid']))
		{
			$rid = $_REQUEST['uid'];
		}
		// vigadest v6i muudest staatustest teavitatavad
		$reciver = "root@struktuur.ee";
		if (isset($_REQUEST['mailto']))
		{
			$reciver .= ",".$_REQUEST['mailto'];
		}
		// seda saab edaspidi kasutada erinevatel puhkudel, ntx kui root@struktuur.ee omanik pole parasjagu saadaval, et vigu pyyda.
		if (isset($_REQUEST['mailto']))
		{
			$reciver .= "," . $_REQUEST['mailto'];
		}
		$site_dir = explode("/", dirname($_SERVER["SCRIPT_FILENAME"]));
		// Kolm kausta tagasi
		for($i = 0; $i < 3; $i++)
		{
			unset($site_dir[count($site_dir) - 1]);
		}
		$site_dir = implode("/", $site_dir);
		$site_ini = $site_dir."/aw.ini";

		// Since we only need a small handful of ini settings, we can easily fuck the ones that have [ ] [" "] or smth... -kaarel 27.02.2009
		fwrite(fopen($site_dir."/files/tmp_aw.ini", "w"), str_replace(array("[\"", "\"]", "[", "]"), "", file_get_contents($site_ini)));

		$ini = parse_ini_file($site_dir."/files/tmp_aw.ini");

		// siit saab juba kopeerimise 2ra teha
		if (isset($_REQUEST['send_file']))
		{
			$file_name = $site_dir."/files/sitecopy/package/sitecopy".$_REQUEST['send_file'];
			$handle = fopen($file_name, "rb");
			header("Content-Type: application/x-tar");
			while (!feof($handle)) {
				echo fread($handle, 8192);
			}
			$foo = file("http://sitecopy.struktuur.ee/import/?files_count_down=".$_REQUEST['send_file']."&uid=".$rid);
			unlink($file_name);
			die();
		}
		
		// kas saab shell_exec'it kasutada
		if (!function_exists('shell_exec'))
		{
			report_status($reciver, "shell_exec pole kasutatav.");
			die();
		}
		
		//  kas saab skripti pikemalt jooksutada seda pole t6en2oliselt vaja!
		ini_set('max_execution_time', 28800);
		/*if (ini_get('max_execution_time') != 28800)
		{
			$initime = ini_get('max_execution_time')/60;
			report_status($reciver, "Skripti pole v6imalik piisavalt pikalt jooksutada. Lubatud ainult $initime minutit");
			die();
		}*/
		
		// kas kausta saab luua
		mkdir($site_dir.'/files/sitecopy');
		if (!file_exists($site_dir.'/files/sitecopy'))
		{
			report_status($reciver, "Koopia tegemiseks vajalikku kausta ($site_dir/files/sitecopy) polnud v6imalik luua.");
			die();
		}
		else
		{
			$copy_dir = $site_dir.'/files/sitecopy';
		}
		// kaitseme kausta v2liste lugejate eest
		protect_folder($copy_dir, $allowed_server);
				
		// testime, kas tar on olemas ja mis moodi teda kasutada saab
		// bzip2 pakkimisega
		@shell_exec('cd '.getcwd().'; tar -cjf '.$site_dir.'/pagecache/index.tar.bz2 ./index.aw');
		//  gzip pakkimisega
		if (!file_exists($site_dir.'/pagecache/index.tar.bz2'))
		{
			@shell_exec('cd '.getcwd().'; tar -czf '.$site_dir.'/pagecache/index.tar.gz ./index.aw');
			//ilma pakkimiseta
			if (!file_exists($site_dir.'/pagecache/index.tar.gz'))
			{
				@shell_exec('cd '.getcwd().'; tar -cf '.$site_dir.'/pagecache/index.tar ./index.aw');
				//ups, aga tar-i kasutada ei saa :)
				if (!file_exists($site_dir.'/pagecache/index.tar'))
				{
					report_status($reciver, "tar-i ei saa kasutada.");
					die();
				}
				else
				{
					$tar_ext = "";
					$pk = "";
					@shell_exec('rm -f '.$site_dir.'/pagecache/index.tar');
				}
			}
			else
			{
				$tar_ext = ".gz";
				$pk = "z";
				@shell_exec('rm -f '.$site_dir.'/pagecache/index.tar.gz');
			}
		}
		else
		{
			$tar_ext = ".bz2";
			$pk = "j";
			@shell_exec('rm -f '.$site_dir.'/pagecache/index.tar.bz2');
		}
		$tar = "tar -ch".$pk."f";
		
		//mysqldump check
		$dbuser = $ini['db.user'];
		$dbpass = $ini['db.pass'];
		$dbhost = $ini['db.host'];
		$dbbase = $ini['db.base'];
		@shell_exec("mysqldump -u$dbuser --password=$dbpass -h$dbhost $dbbase --no-data --skip-lock-tables > $copy_dir/base.sql");
		$dumpcheck = file("$copy_dir/base.sql");
		if (count($dumpcheck) < 2000)
		{
			report_status($reciver, "Baasist ei 6nnestunud dumpi teha.");
			$dumpcheck = 0;
		}
		else
		{
			$base_file = $copy_dir."/base.sql";
		}
		@shell_exec("rm -f $copy_dir/base.sql");
		
		
		
		
		
		
		//siit maalt peaks olema checkid passitud ja saab hakata kopimisega tegelema
		$site_url = str_replace("http://", "", $ini["baseurl"]);

		$split = 0;
		$local = 0;
		$copy_code = 1;
		$copy_site = 1;
		// kui lokaalne koopia, siis ei spliti ja kontrollime, mida t2pselt soovitakse lokaalsest koopiast
				if (isset($_REQUEST['local_copy']))
		{
			if ($_REQUEST['local_copy'] == 1)
			{
				$local = 1;
				header("Content-type: video/mpeg");
				echo "1";
				// kas soovitakse ka baasi koopiat
				if (!isset($_REQUEST['sql']))
				{
					$dumpcheck = 0;
				}
				if (!isset($_REQUEST['code']))
				{
					$copy_code = 0;
				}
				if (!isset($_REQUEST['site']))
				{
					$copy_site = 0;
				}
			}
			else
			{
				$split = 1;
				header("Content-type: video/mpeg");
				echo "1";
			}
		}
		else
		{
			$split = 1;
		}
		
		//mysqldump
		if ($dumpcheck != 0)
		{
			@shell_exec("mysqldump -u$dbuser --password=$dbpass -h$dbhost $dbbase --skip-lock-tables -Q --ignore-table=$dbbase.syslog --ignore-table=$dbbase.syslog_archive > $base_file");
			@shell_exec("cd $copy_dir; $tar base.sql$tar_ext base.sql");
			@shell_exec("rm -f $base_file");
		}
		
		//nyyd pakima saidi ja koodi
		if ($copy_site)
		{
			$tar_site_exclude = $copy_dir."/site_exclude";
			//k6igepealt teeme nimekirja failidest ja kaustadest, mida me ei paki
			@shell_exec("echo './files/sitecopy\n./pagecache' > $tar_site_exclude");
			@shell_exec("cd $site_dir; find . -type l -iname 'automatweb' -o -type l -iname 'vvfiles' -o -type l -iname 'vv_files' -o -type f -iname '*.sql*' -o -type f -iname '*.tar*'>> $tar_site_exclude");
			//koopia saidist
			$site_file = "$copy_dir/site.tar$tar_ext";
			@shell_exec("cd $site_dir; $tar $site_file -X $tar_site_exclude .");
			unlink($tar_site_exclude);
			//kontrollime, kas sai ka
			if (!file_exists($site_file))
			{
				report_status($reciver, "Saidi osa pakkimine ei 6nnestunud.");
			}
		}
		
		//koopia koodist
		if ($copy_code)
		{
			$code_file = "$copy_dir/code.tar$tar_ext";
			@shell_exec("cd $aw_dir; $tar $code_file .");
			//kontrollime, kas sai ka
			if (!file_exists($code_file))
			{
				report_status($reciver, "Koodi osa pakkimine ei 6nnestunud.");
			}
		}
		
		if (!$local)
		{
			// nyyd proovime vhost faili k2tte saada
			function get_vhost($folder, $site_url)
			{
				if (file_exists($folder))
				{
					$ph_files = shell_exec("grep -rnis 'servername $site_url' $folder");
					$ph_files .= shell_exec("grep -rnis 'serveralias $site_url' $folder");
					$ph_filesa = explode("\n", $ph_files);
					$vhosts = array();
					for ($i = 0; $i <= count($ph_filesa) - 1; $i++)
					{
						$phfile = explode(":", $ph_filesa[$i]);
						$fs = file($phfile[0]);
						//kontrollime, kas ikka vastab vhosti faili tingimustele
						$cf_docroot = 1;
						$cf_errlog = 1;
						$cf_custlog = 1;
						$fsol = "";
						foreach ($fs as $fsl)
						{
							$fsol .= $fsl." ";
						}
						$pos1 = stripos($fsol, "documentroot");
						$pos2 = stripos($fsol, "errorlog");
						$pos3 = stripos($fsol, "customlog");
						if ($pos1 === false)
						{
							$cf_docroot = 0;
						}
						else
						{
							$cf_docroot = 1;
						}
						if ($pos2 === false)
						{
							$cf_errlog = 0;
						}
						if ($pos3 === false)
						{
							$cf_custlog = 0;
						}
						if (($cf_docroot == 1) && ($cf_errlog == 1) && ($cf_custlog == 1))
						{
							$vhosts[] = $phfile[0];
						}
					}
					if (count($vhosts > 0))
					{
						return $vhosts[0];
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			if (get_vhost("/etc", $site_url) == false)
			{
				if (get_vhost("/usr/local/etc", $site_url) == false)
				{
					$vhfile = "";
				}
				else
				{
					$vhfile = get_vhost("/usr/local/etc", $site_url);
				}
			}
			else
			{
				$vhfile = get_vhost("/etc", $site_url);
			}
			if ($vhfile != "")
			{
				if (!copy($vhfile, $copy_dir."/vhost"))
				{
					report_status($reciver, "Vhost faili ei saanud kopeerida.");
				}
			}

			// moodustame vajaliku conffaili kaasa
			$fp = fopen($copy_dir."/config.aw", "w");
			$config_content =
"<?php
	\$site_url = \"" . $site_url . "\";
	\$db_user = \"" . $ini['db.user'] . "\";
	\$db_host = \"" . $ini['db.host'] . "\";
	\$db_pass = \"" . $ini['db.pass'] . "\";
	\$db_base = \"" . $ini['db.base'] . "_sc\";
	\$old_db_base = \"" . $ini['db.base'] . "\";
	\$tar_ext = \"" . $tar_ext . "\";
	\$aw_dir = \"" . $aw_dir . "\";
	\$site_basedir = \"" . $ini['site_basedir'] . "\";
	\$baseurl = \"" . $ini['baseurl'] . "\";
?>";
			fwrite($fp, $config_content);
			fclose($fp);
		}
		
		// pakime k6ik yheks
		mkdir($copy_dir."/package");

		// kas vaja splittida?
		if ($split)
		{
			shell_exec("cd $copy_dir; tar cf /dev/stdout --exclude=package . | split -b 40m - package/sitecopy");
		}
		else
		{
			shell_exec("cd $copy_dir; tar cf package/copy.local.tar --exclude=package .");
		}
		// eemaldame failid, mida enam vaja ei ole
		if (file_exists($copy_dir."/base.sql".$tar_ext))
		{
			unlink($copy_dir."/base.sql".$tar_ext);
		}
		if (file_exists($copy_dir."/code.tar".$tar_ext))
		{
			unlink($copy_dir."/code.tar".$tar_ext);
		}
		if (file_exists($copy_dir."/site.tar".$tar_ext))
		{
			unlink($copy_dir."/site.tar".$tar_ext);
		}
		if (file_exists($copy_dir."/vhost"))
		{
			unlink($copy_dir."/vhost");
		}
		if (file_exists($copy_dir."/config.aw"))
		{
			unlink($copy_dir."/config.aw");
		}
		// muudame 6igused
		chmod2($copy_dir, 0777);

		// teavitame kopeerivat serverit
		if ($local)
		{
			$foo = file("http://sitecopy.struktuur.ee/import/?done=".$rid);
			$status_msg = "Koopia saidist on edukalt loodud.\nKoopia asukoht: ".$copy_dir."/package/copy.local.tar";
			report_status($reciver, $status_msg);
		}
		else
		{
			// loeme kokku kopeeritavate failide arvu
			$filecount = count_files_in_dir($copy_dir."/package", "sitecopy");

			// nimetame failid normaalselt ringi, et erinevates systeemides k2ituks asi sama moodi
			$files_array = array();
			$dir = $copy_dir."/package";
			$d = dir($dir);
			while ($f = $d->read()) {
				if(substr($f, 0, 8) == "sitecopy") {
					$files_array[] = $f;
				}
			}
			$a = 0;
			sort($files_array, SORT_STRING);
			while ($a <= $filecount -1)
			{
				$b = $a + 1;
				rename($copy_dir."/package/".$files_array[$a], $copy_dir."/package/sitecopy".$b);
				$a++;
			}
			$wget_url = "http://sitecopy.struktuur.ee/import/?start_transfer=".$rid."&files_count=".$filecount;
			file($wget_url);
		}
	}
	else
	{
		echo "Saidi koopiate tegemiseks tuleb kasutada selleks vastavat aadressi.";
	}
?>
