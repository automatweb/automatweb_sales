<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHORTCUT_MANAGER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes

@default table=objects
@default group=general

@property shortcut_sets type=chooser field=meta method=serialize
@caption Aktiivne set

@property shortcuts type=table
@caption Shortcutid

*/

class shortcut_manager extends class_base
{
	const AW_CLID = 1464;

	function shortcut_manager()
	{
		$this->init(array(
			"tpldir" => "admin/shortcut_manager",
			"clid" => CL_SHORTCUT_MANAGER
		));
	}
	
	function _get_shortcuts($arr)
	{
		$o = & $arr["obj_inst"];
		
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Kirjeldus"),
		));
		$t->define_field(array(
			"name" => "keycombo",
			"caption" => t("Shortcut"),
		));
		
		$o_shortcut_set = obj($o->prop("shortcut_sets"));
		$connections = $o_shortcut_set->connections_from(array(
			"type" => "RELTYPE_SHORTCUT",
			"class_id" => CL_SHORTCUT
		));
		foreach ($connections as $connection)
		{
			$o_shortcut = $connection->to ();
			
			$t->define_data(array(
				"name" =>  $o_shortcut->prop("name"),
				"keycombo" =>  $o_shortcut->prop("keycombo"),
			));
		}
	}
	
	/**
		@attrib name=parse_shortcuts_from_xml
	**/
	function parse_shortcuts_from_xml($arr)
	{
		$file = core::get_file(array("file"=>aw_ini_get("basedir")."/xml/shortcuts.xml"));

		$doc = new DOMDocument();
		$doc->loadXML( $file );
		
		$o_items = $doc->getElementsByTagName( "class" );
		$shortcuts_array = array();
		$j=0;
		
		$out = "aw_shortcut_db = new Array();\n";
		$out .= "/* start xml/shortcuts.xml */ \n";


		foreach($o_items as $class)
		{
			$s_class = $class->getAttribute("name");
			$out .= 'aw_shortcut_db["'.$s_class.'"] = new Array();';
			foreach($class->getElementsByTagName( "shortcut" ) as $shortcut)
			{
				$s_function = $shortcut->getAttribute("function");
				$s_shortcut = $shortcut->getAttribute("shortcut");
				$out .= 'aw_shortcut_db["'.$s_class.'"]["'.$s_function.'"] = "'.$s_shortcut.'";';
				$shortcuts_array[$s_class][$s_function] = array(
					"shortcut" => $s_shortcut,
					"url" => $shortcut->getAttribute("url"),
				);
			}
		}

/*		foreach( $o_items as $class )
		{
			//$classes = $item->getElementsByTagName( "shortcut" );
			$s_class = $class->getAttribute("name");
			$out .= 'aw_shortcut_db["'.$s_class.'"] = new Array();';
			foreach($class->getElementsByTagName( "shortcut" ) as $shortcut)
			{
				foreach($shortcut->getElementsByTagName( "function" ) as $function)
				{
					$s_function = $function->getAttribute("name");
					foreach($function->getElementsByTagName( "arguments" ) as $arguments)
					{
						foreach($arguments->getElementsByTagName( "required" ) as $required)
						{
							$s_shortcut = $required->getAttribute("value");
							$out .= 'aw_shortcut_db["'.$s_class.'"]["'.$s_function.'"] = "'.$s_shortcut.'";';
						}
					}
				}
			}
		}*/

		foreach($shortcuts_array as $class => $funct)
		{
			foreach($funct as $function => $sc)
			{
				if($sc["url"])
				{
					$out.='$.hotkeys.add("'.$sc["shortcut"].'", function(){ aw_popup_scroll("'.$sc["url"].'", "'.$class.'_'.$function.'", 800,600);});';
				}
				else
				{
					$url = "/automatweb/orb.aw?class=".$class."&action=".$function."&in_popup=1";
					$out.='$.hotkeys.add("'.$sc["shortcut"].'", function(){ aw_popup_scroll("'.$url.'", "'.$class.'_'.$function.'", 800,600);});';
				}
			}
		}

		$out .= "\n/* end xml/shortcuts.xml */\n";
		ob_start ("ob_gzhandler");
		header ("Content-type: text/javascript; charset: UTF-8");
		die($out);
	}
	
	/**
		@attrib name=parse_shortcuts_from_objects
	**/
	function parse_shortcuts_from_objects($arr)
	{
		
		//$ol = new object_list(array(
			//"class_id" => CL_SHORTCUT_SET,
		//));
	
		$out = "";
		die($out);
	}
	
	/**
		@attrib name=parse_shortcuts
	**/
	function parse_shortcuts($arr)
	{
		$file = core::get_file(array("file"=>aw_ini_get("basedir")."/xml/shortcuts.xml"));

		$doc = new DOMDocument();
		$doc->loadXML( $file );
		$shortcuts_array = array();
		$o_items = $doc->getElementsByTagName( "class" );
		$j=0;
		
		$out = "aw_shortcut_db = new Array();\n";
		$out .= "/* start xml/shortcuts.xml */ \n";


		foreach($o_items as $class)
		{
			$s_class = $class->getAttribute("name");
			$out .= 'aw_shortcut_db["'.$s_class.'"] = new Array();';
			foreach($class->getElementsByTagName( "shortcut" ) as $shortcut)
			{
				$s_function = $shortcut->getAttribute("function");
				$s_shortcut = $shortcut->getAttribute("shortcut");
				$out .= 'aw_shortcut_db["'.$s_class.'"]["'.$s_function.'"] = "'.$s_shortcut.'";';
				$shortcuts_array[$s_class][$s_function] = array(
					"shortcut" => $s_shortcut,
					"url" => $shortcut->getAttribute("url"),
				);
			}
		}

		foreach($shortcuts_array as $class => $funct)
		{
			foreach($funct as $function => $sc)
			{
				if($sc["url"])
				{
					$out.='$.hotkeys.add("'.$sc["shortcut"].'", function(){ aw_popup_scroll("'.$sc["url"].'", "'.$class.'_'.$function.'", 800,600);});';
				}
				else
				{
					$url = "/automatweb/orb.aw?class=".$class."&action=".$function."&in_popup=1";
					$out.='$.hotkeys.add("'.$sc["shortcut"].'", function(){ aw_popup_scroll("'.$url.'", "'.$class.'_'.$function.'", 800,600);});';
				}
			}
		}
		$out .= "\n/* end xml/shortcuts.xml */\n";
		
		$o_user = obj(aw_global_get("uid_oid"));
		if ($o_user->prop("settings_shortcuts_shortcut_sets"))
		{
			$o_shortcut_set = obj($o_user->prop("settings_shortcuts_shortcut_sets"));
			
			$conns = $o_shortcut_set->connections_from();
			$i=0;
			foreach($conns as $con)
			{
				$data = array();
				$o_shortcut = obj($con->prop("to"));
				$shortcut_type = $o_shortcut->prop("type");
				switch ($shortcut_type) {
					case "go_to_url":
						$out .= 'function aw_shortcut_manager_get_action_'.$i.'(){'."\n".'
							$.shortcut_manager.get_action({
								parent : "'.$_GET["parent"].'",
								oid : "'.$o_shortcut->id().'"
							})'."\n".';
						};';
						$out .= "$.hotkeys.add('".$o_shortcut->prop("keycombo")."', aw_shortcut_manager_get_action_".$i.");";
					case "custom":
						$out .= 'function aw_shortcut_manager_get_action_'.$i.'(){'."\n".'
							$.shortcut_manager.get_action({
								parent : "'.$_GET["parent"].'",
								oid : "'.$o_shortcut->id().'"
							})
						};';
						$out .= "$.hotkeys.add('".$o_shortcut->prop("keycombo")."', aw_shortcut_manager_get_action_".$i.");";
					break;
				}
				$i++;
			}
		}
		
		die($out);
	}
	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "shortcut_sets":
				$ol = new object_list(array(
					"class_id" => CL_SHORTCUT_SET,
				));
				
				$a_shortcuts = Array();
				$i=0;
				for ($o = $ol->begin(); !$ol->end(); $o =& $ol->next())
				{
					$a_shortcuts[$o->id()] = html::href(array(
						"url" => $this->mk_my_orb("change", array(
							"id" => $o->id(),
							"return_url" => get_ru(),
							), CL_SHORTCUT_SET),
						"caption" => $o->prop("name"),
						));
				}
				
				$prop["options"] = $a_shortcuts;
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
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			//$this->db_query("CREATE TABLE aw_shortcut_manager(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
