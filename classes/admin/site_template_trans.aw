<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SITE_TEMPLATE_TRANS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

@default group=t

	@property bm_tb type=toolbar store=no no_caption=1 

	@layout bm_tt type=hbox width=30%:70% 

		@layout bm_tree type=vbox parent=bm_tt closeable=1 area_caption=Puu

			@property bm_tree type=treeview store=no no_caption=1 parent=bm_tree

		@property bm_table type=table store=no no_caption=1 parent=bm_tt

@groupinfo t caption=T&otilde;lkimine submit=no

*/

class site_template_trans extends class_base
{
	const AW_CLID = 1170;

	function site_template_trans()
	{
		$this->init(array(
			"tpldir" => "admin/site_template_trans",
			"clid" => CL_SITE_TEMPLATE_TRANS
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["tf"] = $_GET["tf"];
		$arr["tfl"] = $_GET["tfl"];
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["tf"] = $arr["request"]["tf"];
		$arr["args"]["tfl"] = $arr["request"]["tfl"];
	}

	function _get_bm_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "saveb",
			"action" => "",
			"img" => "save.gif",
			"tooltip" => t("Salvesta")
		));
	}

	function _get_bm_tree($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		// list all files and under those, list all languages
		$dir = aw_ini_get("site_basedir")."/lang/";
		$files = array();
		$dh = opendir($dir);
		$langs = array();
		while (false !== ($file = readdir($dh))) 
		{
			$fn = $dir . "/" . $file;
			if (is_dir($fn) && $file != "." && $file != "..")
			{
				if ($ndir == "")
				{
					$ndir = $fn;
				}
				if (strlen($file) == 2)
				{
					$langs[] = $file;
				}
			}
		}
		closedir($DH);

		$fs = $this->get_directory(array("dir" => $ndir));
		$lang_i = new languages();
		$lv = "set_lang_id";
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$lv = "set_ct_lang_id";
		}
		foreach($fs as $file)
		{
			$bn = basename($file, ".aw");
			$t->add_item(0, array(
				"id" => $file,
				"parent" => 0,
				"name" => $arr["request"]["tf"] == $bn ? "<b>".$bn."</b>" : $bn,
				"url" => aw_url_change_var(array("tf" => $bn, "tfl" => null))
			));

			foreach($langs as $lang)
			{
				$lid = $lang_i->get_langid_for_code($lang);
				$t->add_item($file, array(
					"id" => $file."_".$lang,
					"parent" => $file,
					"name" => $arr["request"]["tfl"] == $file."_".$lang ? "<b>".$lang."</b>" : $lang,
					"url" => aw_url_change_var(array("tfl" =>  $file."_".$lang, "tf" => null, $lv => $lid))
				));
			}
		}		
	}

	private function _init_bm_table(&$t)
	{
		$t->define_field(array(
			"name" => "expl",
			"caption" => t("Selgitus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "const",
			"caption" => t("Konstant"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "tr",
			"caption" => t("T&otilde;lge"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "const",
		));
	}

	function _get_bm_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_bm_table(&$t);

		if (!$arr["request"]["tfl"])
		{
			return;
		}

		list($file, $lang) = explode("aw_", $arr["request"]["tfl"]);
		$file .= "aw";
		$fp = aw_ini_get("site_basedir")."/lang/".$lang."/".str_replace("..", "", str_replace("/", "", $file));
		// slurp file
		$file_data = $this->read_trans_file($fp);

		foreach($file_data as $idx => $row)
		{
			$t->define_data(array(
				"const" => $row["const"],
				"expl" => $row["expl"],
				"tr" => html::textbox(array(
					"name" => "tr[".$row["const"]."]",
					"value" => htmlspecialchars($row["tr"])
				))
			));
		}
		$t->define_data(array(
			"const" => html::textbox(array(
				"name" => "new[const]",
				"size" => 20
			)),
			"expl" => "" /*html::textbox(array(
				"name" => "new[expl]",
				"size" => 20
			))*/,
			"tr" => html::textbox(array(
				"name" => "new[value]",
			))
		));
		$t->set_sortable(false);
	}

	/** returns the parsed content of a translation file, that is in format $lc_foo["BLA"] = "translation"; // comment
		@attrib api=1 params=pos

		@param fn required type=string
			The file to read, full path

		@returns
			array { index number => array { "const" => the translation constant name, "tr" => translated text, "expl" => explanation text, after // in the file } }
	**/
	function read_trans_file($fn)
	{
		$ls = file($fn);
		
		$array_name = "\$lc_".basename($fn, ".aw");
		$res = array();
		foreach($ls as $line)
		{
			if (strpos($line, $array_name) !== false)
			{
				$row = array();
				list($arn, $str) = explode("=", $line, 2);
		
				// parse array name
				if (preg_match("/".preg_quote($array_name)."\[\"(.*)\"\]/", $arn, $mt))
				{
					$row["const"] = $mt[1];

					// parse the string part - count quotes
					$str = trim($str);
					$len = strlen($str);			
					$txt = "";
					for ($i = 1; $i < $len; $i++)
					{
						if ($str[$i] == "\"" && $str[$i-1] != "\\")
						{
							break;
						}
						else
						{
							$txt .= $str[$i];
						}
					}

					$row["tr"] = eval("return \"$txt\";");

					// now the rest is the comment, except for ;
					list(,$row["expl"]) = explode("//", trim(substr($str, strpos($str, ";", $i)+1)));
					$row["expl"] = trim($row["expl"]);
					$res[] = $row;
				}
			}
		}
		return $res;
	}

	function _set_bm_table($arr)
	{
		if (!$arr["request"]["tfl"])
		{
			return;
		}

		list($file, $lang) = explode("aw_", $arr["request"]["tfl"]);
		$fp = aw_ini_get("site_basedir")."/lang/".$lang."/".str_replace("..", "", str_replace("/", "", $file))."aw";
		if (!empty($arr["request"]["new"]["const"]))
		{
			$arr["request"]["tr"][$arr["request"]["new"]["const"]] = $arr["request"]["new"]["value"];
		}

		if (is_array($arr["request"]["sel"]))
		{
			foreach($arr["request"]["sel"] as $item)
			{
				unset($arr["request"]["tr"][$item]);
			}
		}

		$this->write_trans_file($fp, $arr["request"]["tr"]);
	}

	/** writes translation file from the given data to the given folder
		@attrib api=1 params=pos

		@param fn required type=string
			The full path of the file to write

		@param dat required type=array
			The data to write to the file, same format as read_trans_file return value
	**/
	function write_trans_file($fn, $dat)
	{
		$prev_data = $this->read_trans_file($fn);

		$array_name = "\$lc_".basename($fn, ".aw");
		$c = "<?php\nglobal $array_name;\n";
		foreach($dat as $const => $value)
		{
			// get the comment text from prev file
			$comm = "";
			foreach($prev_data as $row)
			{
				if ($row["const"] == $const)
				{
					$comm = $row["expl"];
					break;
				}
			}

			$c .= $array_name."[\"".$const."\"] = \"".$this->code_quote($value)."\"; ".($comm != "" ? " // ".$comm : "")."\n";
		}
		$c .= "?>";
		$this->put_file(array(
			"file" => $fn,
			"content" => $c
		));
	}

	private function code_quote($value)
	{
		return str_replace("\"", "\\\"", str_replace("\n", "\\n", str_replace("\\", "\\\\", $value)));
	}
}
?>
