<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHORTCUT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes

@default table=objects
@default group=general

@property name type=textbox field=name
@caption Nimi

@property keycombo type=textbox field=meta method=serialize
@caption Klahvikombinatsioon

@property type type=select field=meta method=serialize
@caption T&uuml;&uuml;p

@property url type=textbox field=meta method=serialize size=100
@caption URL

@property custom type=textarea field=meta method=serialize
@caption Skript


*/

class shortcut extends class_base
{
	const AW_CLID = 1468;

	function shortcut()
	{
		$this->init(array(
			"tpldir" => "admin/shortcut_manager",
			"clid" => CL_SHORTCUT
		));
	}
	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "type":
					$prop["options"] = array(
						"" => t("Vali t&uuml;&uuml;p"),
						"go_to_url" => t("Mine URL'ile"),
						"custom" => t("Kasutaja skript"),
					);
				break;
			case "url":
				$o = $arr["obj_inst"];
				if ($o->prop("type") != "go_to_url")
				{
					$retval = PROP_IGNORE;
				}
			break;
			case "custom":
				$o = $arr["obj_inst"];
				if ($o->prop("type") != "custom")
				{
					$retval = PROP_IGNORE;
				}
			break;
			case "keycombo":
				$prop["value"] = str_replace  ("alt+ctrl", "ctrl+alt", $prop["value"]);
				$prop["value"] = strtoupper($prop["value"]);
			break;
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "keycombo":
				$prop["value"] = trim(strtolower($prop["value"]),"+");
				$prop["value"] = str_replace  ("ctrl+alt", "alt+ctrl",  $prop["value"]);
			break;
		}

		return $retval;
	}
	
	/**
		@attrib name=get_action params=id
	**/
	function get_action($arr)
	{
		$o = obj($_GET["id"]);
		
		$this->read_template("shortcut.tpl");
		
		if ($o->prop("type")=="go_to_url")
		{
			$this->vars(array(
				"url" =>$o->prop("url"),
			));
			
			$tmp = $this->parse("GO_TO_URL");
			
			$search = array(
				"{VAR:baseurl}",
				"{VAR:parent}",
			);
			$replace = array(
				aw_ini_get("baseurl"),
				$_GET["parent"],
			);
			
			$tmp = str_replace($search, $replace, $tmp);
			
			$this->vars(array(
				"GO_TO_URL" => $tmp,
			));
		}
		else if ($o->prop("type")=="custom")
		{
			$this->vars(array(
				"custom" =>$o->prop("custom"),
			));
			
			$tmp = $this->parse("CUSTOM_JS");
			
			$this->vars(array(
				"CUSTOM_JS" => $tmp,
			));
		}
		echo $this->parse();
		
		//ob_start ("ob_gzhandler");
		header ("Content-type: text/javascript; charset: UTF-8");
		die();
	}
	
	function callback_generate_scripts($arr)
	{
		$s_out = '
		$.getScript("'.aw_ini_get("baseurl").'/automatweb/js/jquery/plugins/jquery_catch_keycombo.js", function(){
			$("#keycombo").catch_keycombo();
		});
		';
		return $s_out;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
}

?>
