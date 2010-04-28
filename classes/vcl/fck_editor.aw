<?php
// fck_editor.aw - FCKeditor

namespace automatweb;

class fck_editor extends aw_template
{
	function fck_editor()
	{
		$this->init (array (
			"tpldir" => "vcl/fck_editor",
		));
		$this->fck_version = "2.6.4";
	}

	function get_rte_toolbar($arr)
	{
		if (!is_object($arr["toolbar"]))
		{
			return;
		}
		$toolbar = &$arr["toolbar"];
		$toolbar->add_separator();
		if($arr["no_rte"] == 1)
		{
			$toolbar->add_button(array(
				"name" => "source",
				"tooltip" => t("RTE"),
				"target" => "_self",
				"url" => aw_url_change_var("no_rte", ""),
			));
		}
		else
		{
			$toolbar->add_button(array(
				"name" => "source",
				"tooltip" => t("HTML"),
				"target" => "_self",
				"url" => "javascript:oldurl=window.location.href;window.location.href=oldurl + '&no_rte=1';",
			));
		}
	}

	function get_styles_from_site($arr = array())
	{
	//	$contents = file_get_contents(aw_ini_get("site_basedir") . "/public/css/styles.css");
		// now I need to parse things out of this place
	//	print "<pre>";
	//	print $contents;
	//	print "</pre>";

	}

	function draw_editor($arr)
	{
		$this->read_template("fck_editor.tpl");
		$this->submerge=1;

		if (isset ($arr["toolbarset"]) )
		{
			$s_toolbarset = $arr["toolbarset"];
		}
		else
		{
			$s_toolbarset = "aw_doc";
		}

		$tmp='';
		foreach($arr["props"] as $nm)
        {
			$height = "500px";
			if ($nm == "lead")
			{
				$height = "200px";
			}

			// why this?
			//$nm2 = $nm;
			//$nm = str_replace("[","_",$nm);
			//$nm = str_replace("]","_",$nm);

			$strFcklang = !empty($arr["lang"]) ? $arr["lang"] : ($_SESSION["user_adm_ui_lc"] != "" ? $_SESSION["user_adm_ui_lc"] : "et");
			if ($strFcklang == "en")
				$strFcklang = "en-uk";

			if (aw_ini_get("document.site_fck_config_path"))
			{
				$config = 'oFCKeditor.Config["CustomConfigurationsPath"] = "'.aw_ini_get("baseurl").aw_ini_get("document.site_fck_config_path").'";';
			}
			else
			{
				$config = 'oFCKeditor.Config["CustomConfigurationsPath"] = "'.aw_ini_get("baseurl").'/automatweb/js/fckeditor/custom_config.js";';
			}

			$this->vars(array(
					"name" => $nm,
					"width"=> "600px",
					"height"=> $height,
					"lang" => $strFcklang,
					"toolbarset" => $s_toolbarset,
					"fck_version" => $this->fck_version,
					"config" => $config,
				));

			if ($nm != "moreinfo")
			{
				$tmp.= $this->parse("EDITOR_FCK");
			}
			else
			{
				$tmp.= $this->parse("EDITOR_ONDEMAND");
			}
		}

		$this->vars(array(
				"msg_leave" => t("Andmed on salvestamata, kas soovite andmed enne lahkumist salvestada?"),
				"msg_leave_error" => html_entity_decode(t("Andmete salvestamine kahjuks ei &otilde;nnestunud")),
		));

		if ($nm != "moreinfo")
		{
			$this->vars(array(
				"EDITOR_FCK" => $tmp,
			));
		}
		else
		{
			$this->vars(array(
				"EDITOR_ONDEMAND" => $tmp,
			));
		}

		return $this->parse();
	}
}
?>
