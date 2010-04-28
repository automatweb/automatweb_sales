<?php

namespace automatweb;


class site_copy_obj extends _int_object
{
	const AW_CLID = 1487;

	const SITE_DOESNT_EXIST = 1;
	const SITE_INPROGRESS = 2;
	const SITE_EXISTS = 3;

	public function prop($k)
	{
		if(!is_oid(parent::prop($k)) && $k == "dir_sites")
		{
			return parent::id();
		}
		return parent::prop($k);
	}

	public function add_site_to_todolist($arr)
	{
		$o = obj();
		$o->set_class_id(CL_SITE_COPY_TODO);
		$o->set_parent(parent::id());
		$o->set_prop("sc_status", site_copy_todo::STAT_COPY);
		$o->set_prop("url", $arr["prop"]["value"]);
		$o->set_prop("local", $arr["request"]["local_copy"]);
		$o->set_prop("local_site", in_array("site", $arr["request"]["local_copy_prms"]));
		$o->set_prop("local_code", in_array("code", $arr["request"]["local_copy_prms"]));
		$o->set_prop("local_base", in_array("base", $arr["request"]["local_copy_prms"]));
		$o->save();
		// Set the mail to notify of the progress.
		$ml = obj();
		$ml->set_class_id(CL_ML_MEMBER);
		$ml->set_parent($o->id());
		$ml->set_prop("mail", $arr["request"]["email"]);
		$ml->save();
		$o->connect(array(
			"to" => $ml->id(),
			"type" => "RELTYPE_MAIL",
		));
		if(isset($arr["request"]["cvs_copy"]) && $arr["request"]["cvs_copy"])
		{
			$o2 = obj($o->save_new());
			$o2->sc_status = site_copy_todo::STAT_CVS;
			$o2->set_prop("url", $arr["prop"]["value"]);
			$o2->save();
			$o2->connect(array(
				"to" => $ml->id(),
				"type" => "RELTYPE_MAIL",
			));
		}
	}

	protected function report_status($message)
	{
		$receiver = $this->get_receiver();
		$message = t("Omk!\n\n").$message."\nSkript: ".__FILE__." @ sitecopy.struktuur.ee".t("\n\nCheers,\nSinu AW Site Copy");
		$from = 'From: sitecopy@automatweb.com';
		send_mail($receiver, 'AW Site Copy', $message, $from);
	}

	public function invoke($arr)
	{
		$this->login();

		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY_TODO,
			"lang_id" => array(),
			"site_id" => array(),
		));
		foreach($ol->arr() as $o)
		{
			$this->todo_obj = $o;
			switch($o->sc_status)
			{
				case site_copy_todo::STAT_COPY:
					$this->sc_copy($o);
					break;

				case site_copy_todo::STAT_TRANSFER:
					$this->sc_transfer($o);
					break;

				case site_copy_todo::STAT_UNPACK:
					$this->sc_unpack($o);
					break;

				case site_copy_todo::STAT_INSTALL:
					$this->sc_install($o);
					break;

				case site_copy_todo::STAT_INSTALL:
					$this->sc_install($o);
					break;

				case site_copy_todo::STAT_CVS:
					$this->sc_cvs($o);
					break;

				case site_copy_todo::STAT_DIFF:
					$this->sc_diff($o);
					break;

				case site_copy_todo::STAT_DELETE:
					$this->sc_delete($o);
					break;
			}
		}
		$this->logout();
	}

	protected function sc_diff($o)
	{
		// Ainult siis, kui eksisteerib selline kopeeritud sait
		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY_SITE,
			"url" => $o->url,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1,
		));
		if($ol->count() > 0)
		{
			$s = $ol->begin();
			if(strlen($s->copy_url_cvs) > 3 && strlen($s->copy_url) > 3)
			{
				$d = $s->get_first_obj_by_reltype("RELTYPE_SITE_DIFF");
				if($d === false)
				{
					$d = obj();
					$d->set_class_id(CL_SITE_DIFF);
					$d->set_parent($s->id());
				}
				$d->set_name("Saitide v&otilde;rdlus");
				$d->url_1 = $s->copy_url;
				$d->url_2 = $s->copy_url_cvs;
				$d->email = "kaareln@gmail.com";
				$d->use_hack_diff = 1;
				$d->save();

				$s->connect(array(
					"to" => $d->id(),
					"reltype" => "RELTYPE_SITE_DIFF"
				));

				$d->diff();
			}

			$this->todo_obj->delete();
		}
	}

	protected function sc_cvs($o)
	{
		// Ainult siis, kui eksisteerib selline kopeeritud sait
		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY_SITE,
			"url" => $o->url,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1,
		));
		if($ol->count() > 0 && strlen($o->url) > 0)
		{
			$s = $ol->begin();

			// Kui CVSi peal on juba sait olemas, uuendame lihtsalt CVSi.
			if(strlen($s->copy_url_cvs) > 3)
			{
				$this->cvs_checkout($s->site_dir_cvs."/automatweb");
				$this->new_site["unpacked_dir"] = $s->site_dir_cvs;
			}
			else
			{
				$this->new_site = array();

				// conf
				$this->get_install_conf(true);
				$this->conf["site_url"] = str_replace("http://", "", trim($o->prop("url"), "/"));
				$this->conf["ini"] = parse_config($s->site_dir."/aw.ini");

				// loome saidile uue nime
				$this->make_site_name(true);
				
				// teeme saidile kausta
				$this->make_site_folder();
				$this->new_site["unpacked_dir"] = $this->new_site["site_folder"];

				// Kopeerime saidi
				$this->copy_for_cvs($s);

				// Teeme CVS checkouti
				$this->cvs_checkout($this->new_site["site_folder"]."/automatweb");
				$this->link_folders($this->new_site["site_folder"]."/automatweb");

				// Some conf for modify_const_and_ini()
				$site_path = explode("/", trim($s->site_dir, "/"));
				unset($site_path[count($site_path) - 1]);
				$this->conf["aw_dir"] = "/".implode("/", $site_path)."/automatweb";
				$this->conf["site_basedir"] = $this->conf["ini"]["site_basedir"];
				$this->conf["baseurl"] = $this->conf["ini"]["baseurl"];

				// muudame public/const.aw's ja aw.ini's yht, teist
				$this->modify_const_and_ini();

				$this->conf["site_url"] = str_replace(array("d2.struktuur.ee", "d2.automatweb.com"), array("d.struktuur.ee", "d.automatweb.com"), $this->new_site["name"]);

				// installime vhosti ja teeme apache'ile rebu
				$this->install_vhost();
			}

			$this->finish_cvs_install($s);
		}
	}

	protected function sc_copy($o)
	{
		$sc = $this->get_obj_inst();
		$conf = $sc->get_conf(true);

		$wget_url = new aw_uri($o->url."/automatweb/sitecopy/");
		$wget_url->set_arg("local_copy", (int)$o->local);
		$wget_url->set_arg("mailto", "kaareln@gmail.com");
		$wget_url->set_arg("uid", $o->id());

		if($o->local)
		{
			$wget_url->set_arg("site", $o->local_site);
			$wget_url->set_arg("code", $o->local_code);
			$wget_url->set_arg("sql", $o->local_base);
			$wget_task = 'wget -O /tmp/sitecopy -T '.$conf["time_limit"].' "'.$wget_url->get().'"';
			shell_exec($wget_task);
		}
		else
		{
			$wget_task = 'wget -O /tmp/sitecopy -T '.$conf["time_limit"].' "'.$wget_url.'"';
			$curl_task = 'curl -o /tmp/sitecopy_curl -m '.$conf["time_limit"].' "'.$wget_url.'"';
			shell_exec($curl_task);
		}
	}

	protected function sc_transfer($o)
	{
		$uid = $o->id();
		$p = $o->packets;
		$z = "";
		if ($p < 10)
		{
			$z = "0";
		}
		// oleme t88tajate suhtes heatahtlikud 
		//								--sander
		if ((date("G") > 8) && (date("G") < 19))
		{
			$dr = "100";
		}
		else
		{
			$dr = "1500";
		}

		$wget_url = new aw_uri($o->url."/automatweb/sitecopy/");
		$wget_url->set_arg("local_copy", (int)$o->local);
		$wget_url->set_arg("mailto", "kaareln@gmail.com");
		$wget_url->set_arg("uid", $o->id());
		$wget_url->set_arg("send_file", $p);

		$wget_task = 'wget -O /www/site_copy/temp/'.$uid.'/sitecopy'.$z.$p.' -T 0 --limit-rate='.$dr.'k "'.$wget_url.'"';
		if (file_exists("/www/site_copy/temp/".$uid))
		{
			shell_exec($wget_task);
		}
		else
		{
			mkdir("/www/site_copy/temp/".$uid);
			print $wget_task;
			shell_exec($wget_task);
		}
	}

	protected function sc_unpack($o)
	{
		$d = $o->id();
		if (!file_exists("/www/site_copy/temp/".$d."/unpacked"))
		{
			mkdir("/www/site_copy/temp/".$d."/unpacked");
			$tar_command = "cd /www/site_copy/temp/".$d."/unpacked; cat ../sitecopy* >> /dev/stdout | tar -xf /dev/stdin";
			shell_exec($tar_command);

			$o->sc_status = site_copy_todo::STAT_INSTALL;
			$o->save();
		}
	}

	protected function sc_install($o)
	{
		$this->new_site = array();
		$this->new_site["unpacked_dir"] = "/www/site_copy/temp/".$o->id()."/unpacked";

		// conf
		$this->get_install_conf();
		
		// loome saidile uue nime
		$this->make_site_name();
		
		// teeme saidile kausta
		$this->make_site_folder();

		// pakime saidi lahti
		$this->install_site();

		// pakime koodi lahti
		$this->install_code();

		// pakime baasi lahti
		$this->install_base();

		// kui sait olemas, siis parsime selle juurest konf failid ja paneme vhosti t88le
		if ($this->new_site["site_exists"])
		{
			// muudame public/const.aw's ja aw.ini's yht, teist
			$this->modify_const_and_ini();

			$this->install_vhost();
		}

		$this->finish_install();
	}

	protected function sc_delete($o)
	{
		$sc = $this->get_obj_inst();
		$this->conf = $sc->get_conf(true);

		// vhost maha
		// Baas maha
		// Kood maha
		// Saidi kaust maha
		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY_SITE,
			"url" => $o->url,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1,
		));
		if($ol->count() == 0)
		{
			return "We're fucked!";
		}

		$so = $ol->begin();
		$url = trim(substr($so->copy_url, 7), "/");
		$url_cvs = trim(substr($so->copy_url_cvs, 7), "/");

		$this->delete_progress = array(
			$url => array(
				"base_deleted" => 0,
				"sitedir_deleted" => 0,
				"vhost_deleted" => 0,
				"logs_deleted" => 0,
				"cvs_deleted" => strlen($url_cvs) > 4 ? 0 : 1,
			),
			$url_cvs => array(
				"base_deleted" => 0,
				"sitedir_deleted" => 0,
				"vhost_deleted" => 0,
				"logs_deleted" => 0,
			),
		);

		// Site itself

		$this->sc_delete_db($url, $so->site_dir);
		// kui baas maas, siis v6ib ka saidi maha lasta, muidu ei tea enam keegi kurat, miuke see baas oli
		if($this->delete_progress[$url]["base_deleted"])
		{
			$this->sc_delete_sitedir($url, $so->site_dir."/..");
		}
		// Bye, bye, vhost!
		$this->sc_delete_vhost($url);
		// Bye, bye, logs!
		$this->sc_delete_logs($url);


		// CVS opy of the site
		if(!$this->delete_progress[$url]["cvs_deleted"])
		{
			$this->sc_delete_db($url_cvs, $so->site_dir_cvs);
			// kui baas maas, siis v6ib ka saidi maha lasta, muidu ei tea enam keegi kurat, miuke see baas oli
			if($this->delete_progress[$url_cvs]["base_deleted"])
			{
				$this->sc_delete_sitedir($url_cvs, $so->site_dir_cvs."/..");
			}
			// Bye, bye, vhost!
			$this->sc_delete_vhost($url_cvs);
			// Bye, bye, logs!
			$this->sc_delete_logs($url_cvs);

			if(array_sum($this->delete_progress[$url_cvs]) === 4)
			{
				$this->delete_progress[$url]["cvs_deleted"] = 1;
			}
		}


		// kui k6ik on edukalt kustutatud, siis v6ib jooksvate saitide nimekirjast ka maha v6tta
		if(array_sum($this->delete_progress[$url]) === 5)
		{
			$so->set_meta("delete", 1);
			$so->save();
			$so->delete();
		}
		$this->todo_obj->delete();
	}

	protected function sc_delete_logs($url)
	{
		if(file_exists($this->conf["apache_logs"]."/".$url))
		{
			shell_exec("rm -rf ".$this->conf["apache_logs"]."/".$url);
			if(!file_exists($this->conf["apache_logs"]."/".$url))
			{
				$this->delete_progress[$url]["logs_deleted"] = 1;
			}
			else
			{
				print "logid j2id alles";
			}
		}
		else
		{
			$this->delete_progress[$url]["logs_deleted"] = 1;
		}
	}

	protected function sc_delete_vhost($url)
	{
		// nyyd l2heb vhost
		if(file_exists($this->conf["apache_vhosts"]."/".$url))
		{
			shell_exec("rm -f ".$this->conf["apache_vhosts"]."/".$url);
			if(!file_exists($this->conf["apache_vhosts"]."/".$url))
			{
				$this->delete_progress[$url]["vhost_deleted"] = 1;
				shell_exec("/sbin/service httpd restart");
			}
			else
			{
				print "vhostist ei saanud lahti";
			}
		}
		else
		{
			$this->delete_progress[$url]["vhost_deleted"] = 1;
		}
	}

	protected function sc_delete_sitedir($url, $dir)
	{		
		if(file_exists($dir))
		{
			shell_exec("rm -rf ".$dir);
			if(!file_exists($dir))
			{
				$this->delete_progress[$url]["sitedir_deleted"] = 1;
			}
			else
			{
				print "saidi kausta ei saanud maha";
			}
		}
		else
		{
			$this->delete_progress[$url]["sitedir_deleted"] = 1;
		}
	}

	protected function sc_delete_db($url, $dir)
	{
		if(file_exists($dir."/aw.ini"))
		{
			fwrite(fopen($dir."/files/tmp_aw.ini", "w"), str_replace(array("[\"", "\"]", "[", "]"), "", file_get_contents($dir."/aw.ini")));
			$ini = parse_ini_file($dir."/files/tmp_aw.ini");
			unlink($dir."/files/tmp_aw.ini");

			// v6tame aw.ini'st baasi andmed
			$dbase = $ini["db.base"];
			$duser = $ini["db.user"];

			// kusutame baasi ja eemaldame kasutaja 6igused
			$db_link = mysql_connect("localhost", "root", $this->conf["mysql_root_password"]);
			if($db_link)
			{
				$s2 = "DROP DATABASE $dbase";
				mysql_query($s2, $db_link);
				$base_check = mysql_select_db($dbase, $db_link);
				if(!$base_check)
				{
					$this->delete_progress[$url]["base_deleted"] = 1;
				}
				else
				{
					print "baasi ei saanud kustutada, saidi kaust j22b alles";
				}
				$s3 = "DELETE FROM mysql.user WHERE User = '$duser'";
				$s4 = "DELETE FROM mysql.db WHERE User = '$duser' AND Db = '$dbase'";
				$s5 = "FLUSH PRIVILEGES";
				mysql_query($s3, $db_link);
				mysql_query($s4, $db_link);
				mysql_query($s5, $db_link);
			}
			else
			{
				print "root ei saa ligi";
			}
		}
	}

	public function get_conf($apply_conf = false)
	{
		$ret = array();
		$ks = array("time_limit", "apache_logs", "apache_vhosts", "cvs_user", "mysql_host", "mysql_root_password");
		$ok = true;

		foreach($ks as $k)
		{
			if(strlen(trim(parent::prop($k))) > 0)
			{
				$ret[$k] = parent::prop($k);
			}
			else
			{
				$ok = false;
				break;
			}
		}
		if($ok)
		{
			if($apply_conf)
			{
				aw_ini_set("max_execution_time", $ret["time_limit"]);
			}
			return $ret;
		}
		else
		{
			return false;
		}
	}

	public function list_allowed_servers($arr)
	{
		$this->login();

		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1,
		));
		if($ol->count() > 0)
		{
			$sc = $ol->begin();
			$this->sc_die(implode(" ", $sc->meta("allowed_servers")));
		}
		$this->logout();
	}

	public function start_transfer($arr)
	{
		$this->login();

		extract($arr);
		if(is_oid($id))
		{
			$o = obj($id);
			if($o->sc_status == site_copy_todo::STAT_COPY)
			{
				$o->sc_status = site_copy_todo::STAT_TRANSFER;
				$o->packets = $packets;
				$o->packets_total = $packets;
				$o->save();
			}
			else
			{
				$status = get_instance(CL_SITE_COPY_TODO)->sc_status_options[$o->sc_status];
				$this->sc_die(sprintf(t("Can't set status to transfer! Current status is %s."), $status));
			}
		}
		$this->logout();
	}

	public function files_count_down($arr)
	{
		$this->login();

		extract($arr);
		if(is_oid($id))
		{
			$o = obj($id);
			$o->packets = $packets;
			if($packets == 0)
			{
				$o->sc_status = site_copy_todo::STAT_UNPACK;
			}
			$o->save();
		}
		$this->logout();
	}

	protected function make_site_name($cvs = false)
	{
		$new_site_name = "";
		$ru = explode(".", $this->conf["site_url"]);
		$rul = count($ru);
		if (($ru[$rul - 2] != "struktuur") && ($ru[$rul - 2]) != "automatweb")
		{
			if ($ru[0] == "www")
			{
				$a = 1;
			}
			else
			{
				$a = 0;
			}
			for ($a; $a <= $rul - 2; $a++)
			{
				$new_site_name .= $ru[$a] . ".";
			}
			$new_site_name .= $cvs ? "d2.struktuur.ee" : "d.struktuur.ee";
		}
		else
		{
			for ($a = 0; $a <= $rul - 3; $a++)
			{
				$new_site_name .= $ru[$a] . ".";
			}
			if ($ru[$rul - 2] == "struktuur")
			{
				$new_site_name .= $cvs ? "d2.struktuur.ee" : "d.struktuur.ee";
			}
			else
			{
				$new_site_name .= $cvs ? "d2.automatweb.com" : "d.automatweb.com";
			}
		}
		$this->new_site["name"] = $new_site_name;
	}

	protected function cvs_checkout($dir)
	{
		$path = explode("/", $dir);
		$dir_name = array_pop($path);
		$dir = implode("/", $path);

		// Teeme kausta enne tyhjaks
		shell_exec("rm -rf ".$dir."/".$dir_name);

		$uct = "#!/bin/bash\n";
		$uct .= "cd ".$dir."/\n";
		$uct .= "cvs -d:ext:instrumental@dev.struktuur.ee:/home/cvs checkout automatweb_dev\n";
		$f = fopen("/www/site_copy/v6tmed", "w");
		fwrite($f, $uct);
		fclose($f);
		shell_exec("/www/site_copy/cvscheckout");
		shell_exec("cd ".$dir."/; mv automatweb_dev ".$dir_name);
		// "chmod 777 files", kuna uus AW tahab sinna mingeid cache'i faile teha
		shell_exec("cd ".$dir."/".$dir_name."/files; mkdir class_index; chmod 777 class_index; cd ..; chmod 777 files");
	}

	protected static function login()
	{
		// Switch the user to avoid any ACL conflicts.
		$u = get_instance("users");
		$u->login(array(
			"uid" => "site_copy",
			"password" => "ypocetis",
		));

		if(!is_oid(aw_global_get("uid_oid")))
		{
			aw_global_set("uid_oid", $u->get_oid_for_uid("site_copy"));
		}
	}

	protected static function logout()
	{
		$u = get_instance("users");
		$u->logout();
	}

	// Better not to use just PHP's die(), cuz we have to log in for some features to work. And we MUST NOT leave it logged in!
	protected function sc_die($msg = "")
	{
		$this->logout();
		die($msg);
	}

	public static function get_obj_inst()
	{
		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY,
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1,
		));
		return $ol->begin();
	}

	protected function make_site_folder()
	{
		$this->new_site["site_folder"] = "/www/sites/".$this->new_site["name"];
		if(!file_exists($this->new_site["site_folder"]))
		{
			mkdir($this->new_site["site_folder"]);
		}
	}

	protected function install_site()
	{
		$fld = $this->new_site["site_folder"];
		$tar_ext = $this->conf["tar_ext"];

		// kui saidi pakk on olemas, siis pakime selle lahti
		if (file_exists($this->new_site["unpacked_dir"]."/site.tar".$tar_ext))
		{
			if(!file_exists($fld."/site"))
			{
				mkdir($fld."/site");
			}
			$ust = "cd ".$fld."/site; tar -xf ".$this->new_site["unpacked_dir"]."/site.tar".$tar_ext;
			shell_exec($ust);
			if(!file_exists($fld."/site/pagecache"))
			{
				mkdir($fld."/site/pagecache");
			}
			shell_exec("chmod -R 777 ".$fld."/site/pagecache ".$fld."/site/files");
			$this->new_site["site_exists"] = true;
		}
		else
		{
			$this->new_site["site_exists"] = false;
		}
	}

	protected function install_code()
	{
		$fld = $this->new_site["site_folder"];
		$tar_ext = $this->conf["tar_ext"];

		// kui kood on olemas, siis pakime lahti
		if (file_exists($this->new_site["unpacked_dir"]."/code.tar".$tar_ext))
		{
			$code_exists = 1;
			if(!file_exists($fld."/automatweb"))
			{
				mkdir($fld."/automatweb");
			}
			$uct = "cd ".$fld."/automatweb; tar -xf ".$this->new_site["unpacked_dir"]."/code.tar".$tar_ext;
			shell_exec($uct);
			$this->link_folders($fld."/automatweb");
			$this->new_site["code_exists"] = true;
		}
		else
		{
			$this->new_site["code_exists"] = false;
		}
	}

	protected function link_folders($code_fld = "../../automatweb")
	{
		$fld = $this->new_site["site_folder"];

		shell_exec("cd ".$fld."/site/public; ln -s ".$code_fld."/automatweb; ln -s ../files vv_files; ln -s ../files vvfiles");	
	}

	protected function get_install_conf($cvs = false)
	{
		// kui konfimise fail on olemas, siis saab ka midagi teha, muidu pole m6tet yldse alustadagi
		if ((!isset($this->new_site["unpacked_dir"]) || !file_exists($this->new_site["unpacked_dir"]."/config.aw")) && !$cvs)
		{
			$this->report_status($this->todo_obj, sprintf(t("Ei saa installida, konfifail puudu!\nLahti pakitud failid asuvad kaustas %s"), $this->new_site["unpacked_dir"]));
			exit;
		}

		$this->conf = $this->get_obj_inst()->get_conf(true);
		$this->conf["ip"] = "213.219.102.202";		// Fucking $_SERVER["SERVER_ADDR"] won't work here for some reason. -kaarel 27.12.2008

		if(!$cvs)
		{
			include_once($this->new_site["unpacked_dir"]."/config.aw");
			// The values that come from config.aw
			$conf = array(
				"tar_ext",
				"site_url",
				"db_user",
				"db_host",
				"db_pass",
				"db_base",
				"old_db_base",
				"aw_dir",
				"site_basedir",
				"baseurl",
			);
			foreach($conf as $k)
			{
				if(isset($$k))
				{
					$this->conf[$k] = $$k;
				}
			}
		}
	}

	protected function install_base()
	{
		$fld = $this->new_site["site_folder"];
		$tar_ext = isset($this->conf["tar_ext"]) ? $this->conf["tar_ext"] : "";

		// kui baas on olemas, siis yritame selle ka jooksma saada
		if (file_exists($this->new_site["unpacked_dir"]."/base.sql".$tar_ext))
		{
			$db_link = mysql_connect($this->conf["mysql_host"], "root", $this->conf["mysql_root_password"]);
			if (!$db_link)
			{
				print "&Uuml;hendus root kasutajana eba&otilde;nnestus!";
			}
			else
			{
				// kontrollime, kas antud baas on ehk juba olemas, kui mitte, siis on k6ik h2sti
				$db_select = mysql_select_db($this->conf["db_base"]);
				if (!$db_select)
				{
					// loome vajaliku baasi ja dumbime sisu
					$s8 = "CREATE DATABASE ".$this->conf["db_base"];
					mysql_query($s8, $db_link);
					$s9 = "GRANT ALL on ".$this->conf["db_base"].".* to ".$this->conf["db_user"]."@localhost IDENTIFIED BY '".$this->conf["db_pass"]."'";
					mysql_query($s9, $db_link);
					$s10 = "FLUSH PRIVILEGES";
					mysql_query($s10, $db_link);
					$mysql_dump = "mysql -u".$this->conf["db_user"]." -p".$this->conf["db_pass"]." ".$this->conf["db_base"]." < base.sql; mysql -u".$this->conf["db_user"]." -p".$this->conf["db_pass"]." ".$this->conf["db_base"]." < /www/site_copy/create_syslog.sql";
					$tar = strlen($tar_ext) > 0 ? "tar -xf base.sql".$tar_ext.";" : "";
					$ubt = "cd ".$this->new_site["unpacked_dir"]."; $tar ".$mysql_dump.";";
					shell_exec($ubt);
				}
				else
				{
					shell_exec("cp ".$this->new_site["unpacked_dir"]."/base.sql".$tar_ext." ".$fld."/");
					$message = sprintf(t("Baas selle saidi jaoks on juba olemas.\nKui soovid selle siiski paigaldada, siis uue baasi leiad siit: %s/"), $fld);
					$this->report_status($message);
				}
				mysql_close($db_link);
			}
		}
	}

	protected function modify_const_and_ini()
	{
		$fld = $this->new_site["site_folder"];

		$data = array(
			// saidi const.aw korda
			$fld."/site/public/const.aw" => array(
				$this->conf["aw_dir"] => $fld."/automatweb",
			),
			// saidi aw.ini korda
			$fld."/site/aw.ini" => array(
				"site_basedir = ".$this->conf["site_basedir"] => "site_basedir = ".$fld."/site",
				"baseurl = ".$this->conf["baseurl"] => "baseurl = http://".$this->new_site["name"],
				"db.host = ".$this->conf["db_host"] => "db.host = ".$this->conf["mysql_host"],
				"db.base = ".$this->conf["old_db_base"] => "db.base = ".$this->conf["db_base"],
			),
		);

		foreach($data as $file => $replaces)
		{
			$content = explode("\n", file_get_contents($file));
			$new_content = array();

			foreach($content as $line)
			{
				$new_line = $line;
				if(substr(trim($line), 0, 1) != "#" && substr(trim($line), 0, 2) != "//")
				{
					$new_line = str_replace(array_keys($replaces), $replaces, $line);
				}
				$new_content[] = $new_line;
			}
			$f = fopen($file, "w");
			fwrite($f, implode("\n", $new_content));
			fclose($f);
		}
	}

	protected function install_vhost()
	{
		$d = $this->new_site["unpacked_dir"];

		// kui vhost olemas, siis yritame selle ka t88le saada
		if (file_exists($d."/vhost"))
		{
			$raw_vhost = file($d."/vhost");
			$vhost_start = 0;
			$vhost_end = 1;
			$vhost_num = 0;
			$vhost_num2 = 0;
			for ($a = 0; $a <= count($raw_vhost) - 1; $a++)
			{
				$spos = stripos($raw_vhost[$a], "<virtualhost");
				$epos = stripos($raw_vhost[$a], "</virtualhost");
				$fpos1 = stripos($raw_vhost[$a], "#<");
				$fpos2 = stripos($raw_vhost[$a], "# <");
				$fpos3 = stripos($raw_vhost[$a], ":443");
				$snpos = stripos($raw_vhost[$a], "servername");
				$sapos = stripos($raw_vhost[$a], "serveralias");
				$supos = stripos($raw_vhost[$a], $this->conf["site_url"]);
				if ($vhost_end == 1 && $spos !== false && $fpos1 === false && $fpos2 === false && $fpos3 === false)
				{
					// print $raw_vhost[$a];
					$vhost_start = 1;
					$vhost_end = 0;
					$vhost_num++;
				}
				if ($vhost_start == 1 && $epos !== false && $fpos1 === false && $fpos2 === false)
				{
					$vhost_end = 1;
					$vhost_start = 0;
					// print $raw_vhost[$a]."\n";
				}
				if ($vhost_start == 1 && $vhost_end == 0 && $spos === false)
				{
					if (($snpos !== false || $sapos !== false) && $supos !== false && $fpos3 === false)
					{
						$vhost_num2 = $vhost_num;
					}
				}
			}
			
			// teine ring veel, n6me, et topelt, aga eks vbl hiljem parandab
			$vhost_start = 0;
			$vhost_end = 1;
			$vhost_num = 0;
			$new_vhost = array();
			for ($a = 0; $a <= count($raw_vhost) - 1; $a++)
			{
				$spos = stripos($raw_vhost[$a], "<virtualhost");
				$epos = stripos($raw_vhost[$a], "</virtualhost");
				$fpos1 = stripos($raw_vhost[$a], "#<");
				$fpos2 = stripos($raw_vhost[$a], "# <");
				$fpos3 = stripos($raw_vhost[$a], ":443");
				if ($vhost_end == 1 && $spos !== false && $fpos1 === false && $fpos2 === false && $fpos3 === false)
				{
					$vhost_num++;
					if ($vhost_num == $vhost_num2)
					{
						$new_vhost[] = $raw_vhost[$a];
					}
					$vhost_start = 1;
					$vhost_end = 0;
				}
				if ($vhost_start == 1 && $epos !== false && $fpos1 === false && $fpos2 === false)
				{
					$vhost_end = 1;
					$vhost_start = 0;
					if ($vhost_num == $vhost_num2)
					{
						$new_vhost[] = $raw_vhost[$a]."\n";
					}
				}
				if ($vhost_start == 1 && $vhost_end == 0 && $spos === false && $vhost_num == $vhost_num2)
				{
					$new_vhost[] = $raw_vhost[$a];
				}
			}

			$new_vhost2 = array();
			for ($a = 0; $a <= count($new_vhost) - 1; $a++)
			{
				$spos = stripos($new_vhost[$a], "<virtualhost");
				$drpos = stripos($new_vhost[$a], "documentroot");
				$snpos = stripos($new_vhost[$a], "servername");
				$sapos = stripos($new_vhost[$a], "serveralias");
				$elpos = stripos($new_vhost[$a], "errorlog");
				$clpos = stripos($new_vhost[$a], "customlog");
				if ($spos !== false)
				{
					$new_vhost2[] = "<Virtualhost " . $this->conf["ip"] . ">\n";
				}
				elseif ($snpos !== false)
				{
					$new_vhost2[] = "ServerName " . $this->new_site["name"] . "\n";
				}
				elseif ($sapos !== false)
				{
					// do nothing
				}
				elseif ($drpos !== false)
				{
					$new_vhost2[] = "DocumentRoot " . $this->new_site["site_folder"] . "/site/public\n";
				}
				elseif ($elpos !== false)
				{
					$new_vhost2[] = "ErrorLog " . $this->conf["apache_logs"] . $this->new_site["name"] . "/error_log\n";
				}
				elseif ($clpos !== false)
				{
					$new_vhost2[] = "CustomLog " . $this->conf["apache_logs"] . $this->new_site["name"] . "/access_log common\n";
				}
				else
				{
					$new_vhost2[] = $new_vhost[$a];
				}
			}

			// paigutame nyyd uue vhosti ka failina
			$vfp = fopen($d."/new_vhost", "w");
			foreach ($new_vhost2 as $foo)
			{
				fwrite($vfp, $foo);
			}
			fclose($vfp);

			// edasi proovime selle apachele ette s88ta
			mkdir($this->conf["apache_logs"].$this->new_site["name"]);
			shell_exec("cp ".$d."/new_vhost ".$this->conf["apache_vhosts"]."/".$this->new_site["name"]);
			shell_exec("/sbin/service httpd restart");
			shell_exec("/sbin/service httpd status > ".$d."/apache_status");
			$apache_status = file($d."/apache_status");

			// kui apache taask2ivitamine ei 6nnestu, siis kustutame valed j2ljed ja ajame apache uuesti t88le
			if (substr($apache_status[0], -7, 6) != "tab...")
			{
				shell_exec("rm -rf ".$this->conf["apache_vhosts"].$this->new_site["name"]." ".$this->conf["apache_logs"].$this->new_site["name"]."; /sbin/service httpd start");
			}
		}
		else
		{
			$this->report_status(t("Vhosti ei leidnud!"));
		}
	}

	protected function finish_install()
	{
		$sc = $this->get_obj_inst();

		$o = obj();
		$o->set_class_id(CL_SITE_COPY_SITE);
		$o->set_parent($sc->dir_sites);
		$o->url = $this->todo_obj->url;
		$o->copy_url = "http://".$this->new_site["name"]."/";
		$o->site_dir = $this->new_site["site_folder"]."/site/";
		$o->save();

		$message = sprintf(t("Sait on lahti pakitud ning asub kaustas %s. Veebist pääseb ligi aadressilt http://%s\n"), $this->new_site["site_folder"], $this->new_site["name"]);
		$this->report_status($message);

		$this->clean_up();

		$this->todo_obj->delete();
	}

	protected function finish_cvs_install($s)
	{
		$s->url = $this->todo_obj->url;
		$s->copy_url_cvs = "http://".$this->new_site["name"]."/";
		$s->site_dir_cvs = $this->new_site["site_folder"]."/site/";
		$s->save();

		$message = sprintf(t("Saidi CVS koopia on valmis ning asub kaustas %s. Veebist pääseb ligi aadressilt http://%s\n"), $this->new_site["site_folder"], $this->new_site["name"]);
		$this->report_status($message);

		$this->clean_cvs_up();

		$this->todo_obj->delete();
	}

	protected function clean_up()
	{
		shell_exec("rm -rf /www/site_copy/temp/".$this->todo_obj->id());
	}

	protected function clean_cvs_up()
	{
		shell_exec("rm -rf ".$this->new_site["unpacked_dir"]."/base.sql");
		shell_exec("rm -rf ".$this->new_site["unpacked_dir"]."/new_vhost");
		shell_exec("rm -rf ".$this->new_site["unpacked_dir"]."/vhost");
		shell_exec("rm -rf ".$this->new_site["unpacked_dir"]."/apache_status");
	}

	public function get_receiver()
	{
		$r = "";
		foreach($this->todo_obj->connections_from(array("type" => "RELTYPE_MAIL")) as $conn)
		{
			$r .= strlen($r) > 0 ? "," : "";
			$r .= $conn->to()->mail;
		}
		$sc = $this->get_obj_inst();
		$r .= strlen($r) > 0 ? "," : "";
		$r .= $sc->default_mail;
		return $r;
	}

	protected function copy_for_cvs($o)
	{
		$fld = $this->new_site["site_folder"];

		// Copy site folder
		shell_exec("rm -rf ".$fld."/site");
		shell_exec("cp -R ".$o->site_dir." ".$fld."/site");
		shell_exec("chmod -R 777 ".$fld."/site/pagecache");

		// Copy MySQL
		$dbuser = $this->conf["ini"]["db"]["user"];
		$dbpass = $this->conf["ini"]["db"]["pass"];
		$dbhost = $this->conf["ini"]["db"]["host"];
		$dbbase = $this->conf["ini"]["db"]["base"];
		shell_exec("mysqldump -u$dbuser --password=$dbpass -h$dbhost $dbbase > $fld/base.sql");
		$this->conf["db_user"] = $dbuser;
		$this->conf["db_pass"] = $dbpass;
		$this->conf["db_host"] = $this->conf["mysql_host"];
		$this->conf["old_db_base"] = $dbbase;
		$this->conf["db_base"] = $dbbase."_cvs";

		$this->install_base();

		// Copy vhost
		$o_site_dir = explode("/", trim($o->site_dir, "/"));
		shell_exec("cp ".$this->conf["apache_vhosts"]."/".$o_site_dir[count($o_site_dir) - 2]." ".$fld."/vhost");
	}

	public static function add_site($arr)
	{
		$o = self::get_obj_inst();
		if(is_object($o))
		{
			$ips = $o->meta("allowed_servers");
			if(!empty($_SERVER["REMOTE_ADDR"]) && in_array($_SERVER["REMOTE_ADDR"], $ips))
			{
				self::login();

				$arr = array(
					"obj_inst" => $o,
					"prop" => array(
						"value" => $arr["url"],
					),
					"request" => array(
						"local_copy" => 0,
						"local_copy_prms" => array(),
						"email" => ifset($arr, "email"),
						"cvs_copy" => ifset($arr, "cvs"),
					),
				);
				$retval = self::check_add_site_submit($arr);

				self::logout();
			}
		}
	}

	private static function check_add_site_submit(&$arr)
	{
		$retval = PROP_OK;
		$url = $arr["prop"]["value"];
		$url = substr($url, 0, 7) == "http://" ? $url : "http://".$url;
		if(strlen($url) < 11)
		{
			$arr["prop"]["error"] = t("Saidi URL peab kindlasti olemas olema!");
			$retval = PROP_FATAL_ERROR;
		}
		else
		{
			$arr["prop"]["value"] = $url;
			// Let's see if we already have a site copied with this URL
			$ol = new object_list(array(
				"class_id" => CL_SITE_COPY_SITE,
				"url" => array($url, trim($url, "/"), trim($url, "/")."/"),
				"lang_id" => array(),
				"site_id" => array(),
				"limit" => 1,
			));
			if($ol->count() > 0)
			{
				$arr["prop"]["error"] = t("Sellise URLiga sait on juba kopeeritud!");
				$retval = PROP_FATAL_ERROR;
			}
			// Let's see if we already have a site in progress with this URL
			$ol = new object_list(array(
				"class_id" => CL_SITE_COPY_TODO,
				"url" => $url,
				"lang_id" => array(),
				"site_id" => array(),
				"sc_status" => new obj_predicate_not(get_instance(CL_SITE_COPY_TODO)->STAT_DELETE),
				"limit" => 1,
			));
			if($ol->count() > 0)
			{
				$arr["prop"]["error"] = t("Sellise URLiga sait on juba kopeerimisel!");
				$retval = PROP_FATAL_ERROR;
			}
		}

		if(!is_email($arr["request"]["email"]) && empty($arr["request"]["local_copy"]))
		{
			$retval = PROP_FATAL_ERROR;
		}

		if($retval === PROP_OK)
		{
			$arr["obj_inst"]->add_site_to_todolist($arr);
		}
		return $retval;
	}

	public static function check_site($arr)
	{
		$retval = array("msg" => self::SITE_DOESNT_EXIST);
		$url = $arr["url"];
		$url = substr($url, 0, 7) == "http://" ? $url : "http://".$url;
		// Let's see if we already have a site copied with this URL
		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY_SITE,
			"url" => array($url, trim($url, "/"), trim($url, "/")."/"),
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 1,
		));
		if($ol->count() > 0)
		{
			$site = $ol->begin();
			$retval = array(
				"msg" => self::SITE_EXISTS,
				"url" => $site->copy_url,
				"url_cvs" => $site->copy_url_cvs,
			);
		}
		// Let's see if we already have a site in progress with this URL
		$ol = new object_list(array(
			"class_id" => CL_SITE_COPY_TODO,
			"url" => $url,
			"lang_id" => array(),
			"site_id" => array(),
			"sc_status" => new obj_predicate_not(get_instance(CL_SITE_COPY_TODO)->STAT_DELETE),
			"limit" => 1,
		));
		if($ol->count() > 0)
		{
			$retval = array("msg" => self::SITE_INPROGRESS);
		}
		return $retval;
	}
}

?>
