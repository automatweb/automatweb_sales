<?php

class pot_scanner extends core
{
	function pot_scanner()
	{
		$this->init();
	}

	function full_scan()
	{
		echo "scanning translation strings from classes\n\n";

		// gather list of classes
		$classes = $this->_get_class_list();

		// gather list of trans files
		$trans = $this->_get_trans_list();
		// for each class that is newer than trans file, update trans
		foreach($classes as $class => $tm)
		{
			$potf = aw_ini_get("transdir")."/".basename($class,".aw").".pot";
			if ($trans[$potf] < $tm)
			{
				//echo "scanning file $class \n";
				$this->scan_file($class, $potf);
			}
		}

		$this->_scan_ini_file();
		echo "all done \n\n";
	}

	function _get_class_list()
	{
		$ret = array();
		$this->_files_from_folder(aw_ini_get("classdir"), "aw", $ret);
		return $ret;
	}

	function _get_trans_list()
	{
		$ret = array();
		$this->_files_from_folder(aw_ini_get("transdir"), "pot", $ret);
		return $ret;
	}

	function _files_from_folder($dir, $ext, &$ret)
	{
		if ($dh = @opendir($dir))
		{
			while (false !== ($file = readdir($dh)))
			{
				if ($file == "converters.aw")
				{
					continue;
				}
				$fn = $dir . "/" . $file;
				if (is_file($fn))
				{
					if (substr($file, -strlen($ext)) == $ext)
					{
						$ret[$fn] = filemtime($fn);
					}
				}
				else
				if (is_dir($fn) && $file != "." && $file != "..")
				{
					$this->_files_from_folder($fn, $ext, $ret);
				}
			}
			closedir($dh);
		}
	}

	function scan_file($file_from, $file_to)
	{
		// tokenizer extension?
		// echo dbg::dump(token_get_all($this->get_file(array("file" => $file_from))));
		// no line numbers

		// regex?
		// preg_match_all("/t\([\"|'](.*)[\"|']\)/imsU", $this->get_file(array("file" => $file_from)), $mt);
		// regex would work, but we need the damn line numbers

		// manual scanner :(
		$strings = $this->_scan_file_props($file_from);

		$meth_name_chars = "\"1234567890qwertyuiopasdfghjklzxcvbnm_QWERTYUIOPASDFGHJKLZXCVBNM";

		$fc = $this->get_file(array("file" => $file_from));

		$len = strlen($fc);
		$line = 1;
		for($i = 0; $i < $len; $i++)
		{
			if ($fc{$i} == "t" && strpos($meth_name_chars, $fc{$i-1}) === false)
			{
				$i++;

				// skip spaces
				while ($fc{$i} == " ")
				{
					$i++;
				}

				if ($fc{$i} == "(")
				{
					// we got a real t() call, scan parameter
					// skip spaces
					$i++;
					while ($fc{$i} == " ")
					{
						$i++;
					}

					// get separator
					$sep = $fc{$i};
					if ($sep != "\"" && $sep != "'")
					{
						$i--;
						continue;
					}

					$i++;
					$param = "";
					// scan until end of separator, also check for escapes
					while ($fc{$i} != $sep || ($fc{$i} == $sep && $fc{$i-1} == "\\"))
					{
						$param .= $fc{$i};
						$i++;
					}
					if (trim($param) != "")
					{
						$strings[] = array(
							"line" => $line,
							"str" => trim(str_replace("\n", "", str_replace("\r", "", $param))),
						);
					}
				}
			}

			if ($fc{$i} == "\n")
			{
				$line++;
			}
		}

		if (count($strings))
		{
			echo "scanned file $file_from \n";

			$this->_cond_write_file($file_to, $strings, $file_from);

			// now, for all languages, check of the .po file exists and if not, copy the new .pot over to that
			$this->_do_update_po($file_to);
		}
	}

	function warning_scan()
	{
		echo "scanning files for places that should have translation strings\n\n";

		// gather list of classes
		$classes = $this->_get_class_list();

		foreach($classes as $class => $tm)
		{
			$this->scan_file_warn($class);
		}
		if (!$this->warn_cnt)
		{
			echo "no translation warnings found!\n\n";
		}
		else
		{
			echo "finished with ".$this->warn_cnt." warnings \n\n";
		}
	}

	function scan_file_warn($from_file)
	{
		$fc = file($from_file);

		foreach($fc as $ln => $line)
		{
			if (preg_match("/\"caption\"(\s*)=>(\s*)['|\"](.*)['|\"]/imsU", $line))
			{
				if (strpos($line, "t(") === false)
				{
					echo "$from_file:".($ln+1)." / untranslated caption ->\n".trim($line)."\n";
					$this->warn_cnt++;
				}
			}
			else
			if (preg_match("/die(\s*)\(['|\"](.*)['|\"]\)/imsU", $line))
			{
				echo "$from_file:".($ln+1)." / die() with untranslated string ->\n".trim($line)."\n";
				$this->warn_cnt++;
			}
			else
			if (preg_match("/raise_error\((.*),['|\"](.*)['|\"]/imsU", $line))
			{
				echo "$from_file:".($ln+1)." / error message with untranslated string ->\n".trim($line)."\n";
				$this->warn_cnt++;
			}
			else
			if (preg_match("/\"msg\"(\s*)=>(\s*)['|\"](.*)['|\"]/imsU", $line))
			{
				echo "$from_file:".($ln+1)." / untranslated message ->\n".trim($line)."\n";
				$this->warn_cnt++;
			}
			else
			if (preg_match("/\"tooltip\"(\s*)=>(\s*)['|\"](.*)['|\"]/imsU", $line))
			{
				echo "$from_file:".($ln+1)." / untranslated tooltip ->\n".trim($line)."\n";
				$this->warn_cnt++;
			}
			else
			if (preg_match("/\"text\"(\s*)=>(\s*)['|\"](.*)['|\"]/imsU", $line))
			{
				echo "$from_file:".($ln+1)." / untranslated menu item text ->\n".trim($line)."\n";
				$this->warn_cnt++;
			}
			else
			if (preg_match("/\"confirm\"(\s*)=>(\s*)['|\"](.*)['|\"]/imsU", $line))
			{
				echo "$from_file:".($ln+1)." / untranslated confirm ->\n".trim($line)."\n";
				$this->warn_cnt++;
			}
			else
			if (preg_match("/\"title\"(\s*)=>(\s*)['|\"](.*)['|\"]/imsU", $line))
			{
				echo "$from_file:".($ln+1)." / untranslated title ->\n".trim($line)."\n";
				$this->warn_cnt++;
			}
		}
	}

	function _scan_file_props($file_from)
	{
		$strings = array();

		// get filename and make the prop file from that
		$propf = aw_ini_get("basedir")."/xml/properties/".basename($file_from,".aw").".xml";
		if (!file_exists($propf))
		{
			return;
		}

		$cu = get_instance("cfg/cfgutils");
		$props = $cu->load_properties(array(
			"file" => basename($propf, ".xml"),
			"clid" => clid_for_name(basename($propf, ".xml"))
		));

		$clss = aw_ini_get("classes");

		$strings[] = array(
			"line" => "class_".clid_for_name(basename($propf, ".xml"))."_help",
			"str" => "Klassi ".$clss[clid_for_name(basename($propf, ".xml"))]["name"]." help"
		);

		// generate strings for
		//  1) property captions
		//  2) property comments
		//  3) property help
		foreach($props as $pn => $pd)
		{
			if ((($pn == "name" && $pd["caption"] == "Nimi") || ($pn == "comment" && $pd["caption"] == "Kommentaar") || ($pn == "status" && $pd["caption"] == "Aktiivne")) && basename($file_from) != "class_base.aw")
			{
				continue;
			}
			$strings[] = array(
				"line" => "prop_".$pn,
				"str" => "Omaduse ".$pd["caption"]." ($pn) caption",
			);
			$strings[] = array(
				"line" => "prop_".$pn."_comment",
				"str" => "Omaduse ".$pd["caption"]." ($pn) kommentaar",
			);
			$strings[] = array(
				"line" => "prop_".$pn."_help",
				"str" => "Omaduse ".$pd["caption"]." ($pn) help",
			);
		}

		//  4) group captions
		$grps = $cu->get_groupinfo();
		foreach($grps as $gn => $gd)
		{
			if (($gn == "general" && $gd["caption"] == "&Uuml;ldine") && basename($file_from) != "class_base.aw")
			{
				continue;
			}
			$strings[] = array(
				"line" => "group_".$gn,
				"str" => "Grupi ".$gd["caption"]." ($gn) pealkiri",
			);

			$strings[] = array(
				"line" => "group_".$gn."_help",
				"str" => "Grupi ".$gd["caption"]." ($gn) help",
			);
		}

		//  5) relation captions
		$ri = $cu->get_relinfo();
		foreach($ri as $gn => $gd)
		{
			if (substr($gn, 0, 8) == "RELTYPE_")
			{
				$strings[] = array(
					"line" => "rel_".$gn,
					"str" => "Seose ".$gd["caption"]." ($gn) tekst",
				);
			}
		}

		// 6) layout area_captions
		$li = $cu->get_layoutinfo();
		foreach($li as $gn => $gd)
		{
			if(strlen($gd["area_caption"]))
			{
				$strings[] = array(
					"line" => "layout_".$gn,
					"str" => "Kujundusosa ".$gd["area_caption"]." (".$gn.") pealkiri",
				);
			}
		}

		return $strings;
	}

	function make_aw()
	{
		echo "creating aw files from translated po files\n\n";
		// for each language dir
		$langs = $this->get_langs();

		foreach($langs as $lang)
		{
			echo "scanning language $lang \n";

			// get .po files
			$po_files = array();
			$po_fld = aw_ini_get("basedir")."/lang/trans/".$lang."/po";
			$this->_files_from_folder($po_fld, "po", $po_files);

			// get .aw files
			$aw_files = array();
			$this->_files_from_folder(aw_ini_get("basedir")."/lang/trans/".$lang."/aw", "aw", $aw_files);

			// compare times
			foreach($po_files as $fn => $tm)
			{
				$awfn = aw_ini_get("basedir")."/lang/trans/".$lang."/aw/".basename($fn, ".po").".aw";
				// if .po is newer
				if (!isset($aw_files[$awfn]) || $aw_files[$awfn] < $tm)
				{
					// make new .aw file
					$this->_make_aw_from_po($fn, $awfn);
				}
			}
		}

		echo "all done\n";
	}

	function _make_aw_from_po($from_file, $to_file)
	{
		$a = $this->parse_po_file($from_file);
		$f = array();
		foreach($a as $line)
		{
			if ($line["msgstr"] != "")
			{
				$f[] = "\$GLOBALS[\"TRANS\"][\"".$this->_code_quote($line["msgid"])."\"] = \"".$this->_nl2br($this->_code_quote($line["msgstr"]))."\";\n";
			}
		}
		if (count($f))
		{
			$fp = fopen($to_file, "w");
			if (!$fp)
			{
				mkdir(dirname($to_file), 0777);
				$fp = fopen($to_file, "w");
			}
			fwrite($fp, "<?php\n");
			foreach($f as $e)
			{
				fwrite($fp, $e);
			}
			fwrite($fp, "?>");
			fclose($fp);
			//echo "wrote file $to_file \n";
		}
	}

	function parse_po_file($from_file)
	{
		//clearstatcache();
		$lines = file($from_file);
		$cnt = count($lines);
		$first_msg = true;
		$f = array();
		for($i = 0; $i < $cnt;  $i++)
		{
			$line = $lines[$i];

			if(substr($line, 0, 1) == "#")
			{
				$entry_header[] = $line;
			}
			else
			if (substr($line, 0, 5) == "msgid")
			{
				$msgid = substr($line, 7, strlen($line)-9);
				while (substr(trim($lines[$i+1]), 0, 6) !== "msgstr")
				{
					$i++;
					$line = $lines[$i];
					$tmp = substr(trim($line), 1, strlen($line)-3);
					if (trim($tmp) != "")
					{
						$msgid .= $tmp;
					}
				}
			}
			else
			if (substr($line, 0, 6) === "msgstr")
			{
				$str = substr(trim($line), 8, strlen($line)-10);
				while (trim($lines[$i+1]) != "")
				{
					$i++;
					$line = $lines[$i];
					$tmp = substr(trim($line), 1, strlen($line)-3);
					if (trim($tmp) != "")
					{
						$str .= $tmp;
					}
				}

				// write msgid/msgstr pair
				if (!$first_msg)
				{
					if (strlen($str) and $str{strlen($str)-1} === "\"")
					{
						$str = substr($str, 0, strlen($str)-1);
					}

					$f[] = array(
						"headers" => $entry_header,
						"msgid" => $msgid,
						"msgstr" => $this->_br2nl($str),
					);

					unset($entry_header);
				}
				else
				{
					// skip the po header
					$first_msg = false;
				}
			}
		}

		return $f;
	}

	// arr(location,data => array(msgid => translated_text))
	function write_aw_lang_file($arr)
	{
		$begin = "<?php";
		$end = "?>";
		$tpl = "\$GLOBALS[\"TRANS\"][\"".$msgid."\"] = \"".addslashes($translated_text)."\";";
		$contents[] = $begin;
		foreach($arr["data"] as $msgid => $translated_text)
		{
			$contents[] = sprintf("\$GLOBALS[\"TRANS\"][\"%s\"] = \"%s\";", $msgid, addslashes($translated_text));
		}
		$contents[] = $end;
		//arr($contents);
		chmod($arr["location"], 0777);
		$fp = fopen($arr["location"], "w");
		foreach($contents as $line)
		{
			fwrite($fp, $line."\n");
		}
		fclose($fp);

	}

	function _br2nl($text)
	{
		return  preg_replace('=<br */?>=i', "\r\n", $text);
	}

	function _nl2br($text)
	{
		return preg_replace("/(\r\n|\n|\r)/", "<br />", $text);
	}

	// arr(location, contents)
	function write_po_file($arr)
	{
		$header[] = "msgid \"\"\n";
		$header[] = "msgstr \"\"\n";
		$header[] = "\"Project-Id-Version: Automatweb 2.0\\n\"\n";
		$header[] = "\"POT-Creation-Date: Wed,  1 Jan 2020 00:00:00 +0200\\n\"\n";
		$header[] = "\"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n\"\n";
		$header[] = "\"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n\"\n";
		$header[] = "\"MIME-Version: 1.0\\n\"\n";
		$header[] = "\"Content-Type: text/plain; charset=ISO-8859-1\\n\"\n";
		$header[] = "\"Content-Transfer-Encoding: 8bit\\n\"\n";
		$header[] = "\"Generated-By: AutomatWeb POT Scanner\\n\"\n";
		$header[] = "\n\n";
		foreach($arr["contents"] as $entry)
		{
			foreach($entry["headers"] as $ent_header)
			{
				$contents[] = trim($ent_header)."\n";
			}
			$contents[] = "msgid \"".$entry["msgid"]."\"\n";
			$contents[] = "msgstr \"".$this->_nl2br($entry["msgstr"])."\"\n\n";
		}
		$contents = array_merge($header, $contents);

		chmod($arr["location"], 0777);
		$fp = fopen($arr["location"], "w");
		foreach($contents as $line)
		{
			fwrite($fp, $line);
		}
		fclose($fp);
	}

	function _code_quote($str)
	{
		return str_replace("\"", "\\\"", str_replace("\\\"", "\"", $str));
	}

	function get_langs()
	{
		$langs = array();
		$dir = aw_ini_get("basedir")."lang/trans";
		if ($dh = @opendir($dir))
		{
			while (false !== ($file = readdir($dh)))
			{
				$fn = $dir."/".$file;
				if (is_dir($fn) && $file != "." && $file != "..")
				{
					if (strlen($file) == 2)
					{
						$langs[$file] = $file;
					}
				}
			}
		}
		return $langs;
	}

	function _write_file($to_file, $strings, $date, $file_from)
	{
		$fp = fopen($to_file, "w");
		// add special POT header
		fwrite($fp, "msgid \"\"\n");
		fwrite($fp, "msgstr \"\"\n");
		fwrite($fp, "\"Project-Id-Version: Automatweb 2.0\\n\"\n");
		fwrite($fp, "\"POT-Creation-Date: $date\\n\"\n");
		fwrite($fp, "\"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n\"\n");
		fwrite($fp, "\"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n\"\n");
		fwrite($fp, "\"MIME-Version: 1.0\\n\"\n");
		fwrite($fp, "\"Content-Type: text/plain; charset=ISO-8859-1\\n\"\n");
		fwrite($fp, "\"Content-Transfer-Encoding: 8bit\\n\"\n");
		fwrite($fp, "\"Generated-By: AutomatWeb POT Scanner\\n\"\n");
		fwrite($fp, "\n\n");

		// put same strings on one line
		$res = array();
		foreach($strings as $string)
		{
			$str = $string["str"];
			$str = str_replace('"','\"',str_replace("\\\"", "\"", $str));

			if (isset($res[$str]))
			{
				$res[$str]["comment"] .= " ".str_replace(aw_ini_get("basedir")."/","", $file_from).":".$string["line"];
			}
			else
			{
				$res[$str] = array(
					"comment" => "#: ".str_replace(aw_ini_get("basedir")."/","", $file_from).":".$string["line"],
					"msgid" => $str
				);
			}
		}

		foreach($res as $dat)
		{
			fwrite($fp, $dat["comment"]."\n");
			fwrite($fp, "msgid \"".str_replace("\\\\\"", "\\\"", $dat["msgid"])."\"\n");
			fwrite($fp, "msgstr \"\"\n");
			fwrite($fp, "\n");
		}
		fclose($fp);
	}

	function _scan_ini_file()
	{
		// compare mod dates
		$inif = aw_ini_get("basedir")."/aw.ini";
		$inipot = aw_ini_get("transdir")."/aw.ini.pot";
		if (filemtime($inif) > filemtime($inipot))
		{
			// for now, scan all class names
			$clss = aw_ini_get("classes");

			$strings = array();
			foreach($clss as $clid => $cld)
			{
				$strings[] = array(
					"line" => "class_".$clid,
					"str" => "Klassi ".$cld["name"]." ($clid) nimi",
				);
				if(strlen($cld["prod_family"]))
				{
					$strings[] = array(
						"line" => "class_prodfamily_".$clid,
						"str" => "Klassi tooteperekonna ".$cld["prod_family"]." ($clid) nimi",
					);
				}
			}

			$clss = aw_ini_get("classfolders");
			foreach($clss as $clid => $cld)
			{
				$strings[] = array(
					"line" => "classfolder_".$clid,
					"str" => "Klassi kataloogi ".$cld["name"]." ($clid) nimi",
				);
			}

			$acl = aw_ini_get("acl.names");
			foreach($acl as $name => $caption)
			{
				$strings[] = array(
					"line" => "acl_".$name,
					"str" => "ACL tegevuse ".$caption." (".$name.") nimi",
				);
			}

			$sts = aw_ini_get("syslog.types");
			foreach($sts as $stid => $sd)
			{
				$strings[] = array(
					"line" => "syslogtypes_".$stid,
					"str" => "syslog.type.".$sd["def"],
				);
			}

			$sas = aw_ini_get("syslog.actions");
			foreach($sas as $said => $sd)
			{
				$strings[] = array(
					"line" => "syslogactions_".$said,
					"str" => "syslog.action.".$sd["def"],
				);
			}

			$lgs = aw_ini_get("languages.list");
			foreach($lgs as $laid => $sd)
			{
				$strings[] = array(
					"line" => "languages_list_".$laid,
					"str" => "languages.list.".$sd["acceptlang"],
				);
			}

			$this->_cond_write_file($inipot, $strings, $inif);

			$this->_do_update_po($inipot);
			echo "wrote ini file translation\n";
		}
	}

	function _cond_write_file($file_to, $strings, $file_from)
	{
		if (file_exists($file_to))
		{
			$tmpf = tempnam(aw_ini_get("server.tmpdir"), "awtrans");
			$this->_write_file($tmpf, $strings, date("r", mktime(0,0,0,1,1,2020)), $file_from);

			if (md5($this->get_file(array("file" => $tmpf))) != md5($this->get_file(array("file" => $file_to))))
			{
				$this->_write_file($file_to, $strings, date("r",mktime(0,0,0,1,1,2020)), $file_from);
			}
			@unlink($tmpf);
			touch($file_to);
		}
		else
		{
			$this->_write_file($file_to, $strings, date("r",mktime(0,0,0,1,1,2020)), $file_from);
		}
	}

	function _do_update_po($file_to)
	{
		$langs = $this->get_langs();
		foreach($langs as $lang)
		{
			$fn = aw_ini_get("basedir")."/lang/trans/$lang/po/".basename($file_to,".pot").".po";
			if (!file_exists($fn))
			{
				copy($file_to, $fn);
			}
			else
			{
				$server_platform = aw_ini_get("server.platform");

				switch ($server_platform)
				{
					case "unix":
						// -U updates file in place, and real men do not need a backup
						if ($GLOBALS["mk_dbg"] == 1)
						{
							echo "msgmerge --no-wrap -N -U --backup=off $fn $file_to\n";
							 shell_exec("msgmerge --no-wrap -N -U --backup=off $fn $file_to ");
						}
						else
						{
							shell_exec("msgmerge --no-wrap -N -U --backup=off $fn $file_to 2>/dev/null");
						}
						//shell_exec("msgmerge -U --backup=off $fn $file_to -o $fn");
						break;

					case "win32":
						$fn_win = str_replace("/", "\\", $fn);
						$file_to_win = str_replace("/", "\\", $file_to);
						$cmd_string = "msgmerge --no-wrap -N -U --backup=off $fn_win $file_to_win";
						// exec($cmd_string);
						`$cmd_string`;
						break;
				}
			}
		}
	}

	function list_untrans_strings($lang = "", $class = "")
	{
		$langs = $this->get_langs();

		if (in_array($lang, $langs))
		{
			$this->_list_untrans_strings_for_lang($lang, $class);
		}
		else
		{
			// go over languages
			foreach($langs as $lang)
			{
				$this->_list_untrans_strings_for_lang($lang, $class);
			}
		}

		exit("\n\nscan completed.");
	}

	function _list_untrans_strings_for_lang($lang, $class = "")
	{
		echo "scanning language $lang ";
		echo empty($class) ? "\n" : " in class {$class}\n";
		// go over po files
		$dir = aw_ini_get("basedir")."/lang/trans/$lang/po";
		$files = array();
		$this->_files_from_folder($dir, "po", $files);

		foreach($files as $file => $ts)
		{
			if (
				(basename($file) == "register_data.po" || basename($file) == "survey.po" || basename($file) == "calendar_registration_form.po") or
				(!empty($class) and (basename($file) != $class . ".po"))
			)
			{
				continue;
			}
			$data = $this->parse_po_file($file);

			$first = true;
			// go over lines
			foreach($data as $line)
			{
				// if msgstr is empty
				if ($line["msgstr"] == "" && $line["msgid"] != "")
				{
					// and the msgid is not for property help/comment
					if (!$this->_is_prop_help_or_comment($line["msgid"]))
					{
						// display it
						if ($first)
						{
							echo "in file $file: \n";
						}
						echo "\tuntranslated msgid $line[msgid] \n";
						$cnt++;
					}
				}
				if (!$this->_is_prop_help_or_comment($line["msgid"]))
				{
					$all_cnt++;
				}
			}
		}

		echo sprintf(t("\n\nnumber of untranslated strings: %s\nnumber of strings: %s\n\n"), (int)$cnt, (int)$all_cnt);
	}

	function _is_prop_help_or_comment($msgid)
	{
		if (preg_match("/Omaduse (.*) \(.*\) caption/imsU", $msgid, $mt))
		{
			if ($mt[1] == "")
			{
				return true;
			}
		}
		if (preg_match("/Omaduse .* \(.*\) kommentaar/imsU", $msgid))
		{
			return true;
		}
		if (preg_match("/Omaduse .* \(.*\) help/imsU", $msgid))
		{
			return true;
		}
		if (preg_match("/Grupi .* \(.*\) help/imsU", $msgid))
		{
			return true;
		}

		if (preg_match("/User-defined/imsU", $msgid))
		{
			return true;
		}
		if (preg_match("/udef/imsU", $msgid))
		{
			return true;
		}
		return false;
	}
}
