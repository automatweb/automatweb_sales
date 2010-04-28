<?php

namespace automatweb;

class server_folder_selector extends core
{
	function server_folder_selector()
	{
		$this->init("vcl/server_folder_selector");
	}

	function init_vcl_property($arr)
	{
		$p = $arr["prop"];
		$p["type"] = "textbox";
		$url = $this->mk_my_orb(
			"select_folder",
			array(
				"pn" => $p["name"],
			)
		);
		$p["post_append_text"] = " ".html::href(array(
			"url" => "#",
			"caption" => t("Vali"),
			"onClick" => "aw_popup_scroll(\"$url&fld=\"+document.changeform.$p[name].value, \"selfold\", 400, 400);"
		));
		return array(
			$p["name"] => $p
		);
	}

	function get_html()
	{
		echo "gethtml <br>";
		return "abba";
	}

	function _init_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Kataloog"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "modifier",
			"caption" => t("Muutja"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i:s",
			"numeric" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sel",
			"caption" => t("Vali"),
			"sortable" => 0
		));
	}

	/**
		@attrib name=select_folder
		@param pn required type=String
		Changeform's input field.
		@param fld optional type=string
		Folder address to show at first.
		@comments
		Shows selected folders contents(a table). Usually used in popup window to select a folder for some form.
		Table has 4 fields:
		*dir/file name, last changed by, last changed when, select link
		Select link fills changeform's input field named $pn, submits form and closes window itself.
		@returns
		The table's html code.
	**/
	function select_folder($arr)
	{
		classload("vcl/table");
		$t = new vcl_table();

		$this->_init_t($t);

		$arr["fld"] = realpath($arr["fld"]);

		$has = false;
		$d = opendir($arr["fld"]);
		while (($file = readdir($d)) !== false)
		{
			if (!is_dir($arr["fld"]."/".$file))
			{
				continue;
			}
			$dd = posix_getpwuid(fileowner($arr["fld"]."/".$file));
			$t->define_data(array(
				"name" => html::href(array(
					"url" => $this->mk_my_orb("select_folder", array("pn" => $arr["pn"], "fld" => $arr["fld"]."/".$file)),
					"caption" => iconv("utf-8", aw_global_get("charset")."//IGNORE",$file)
				)),
				"sel" => html::href(array(
					"url" => "#",
					"onClick" => "el=aw_get_el(\"".$arr["pn"]."\",window.opener.document.changeform); el.value=\"".realpath($arr["fld"]."/".$file)."\";window.opener.document.changeform.submit();window.close()",
					"caption" => t("Vali see")
				)),
				"modifier" => $dd["name"],
				"modified" => filemtime($arr["fld"]."/".$file)
			));
			$has = true;
		}

		$t->set_default_sortby("name");
		$t->sort_by();
		return iconv("utf-8", aw_global_get("charset")."//IGNORE", $arr["fld"])."<br><br>".$t->draw();
	}
}
?>
