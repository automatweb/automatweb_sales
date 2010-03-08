<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_toolbar_button.aw,v 1.9 2007/12/06 14:33:04 kristo Exp $
// property_toolbar_button.aw - Taoolbari nupp 
/*

@classinfo syslog_type=ST_PROPERTY_TOOLBAR_BUTTON relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

@property ord type=textbox size=5 table=objects field=jrk
@caption J&auml;rjekord

@default field=meta
@default method=serialize

@property b_type type=select 
@caption Nupu t&uuml;&uuml;p

@default group=but_props

	@property but_action type=textbox 
	@caption Action

	@property but_tooltip type=textbox
	@caption Tooltip

	@property but_img type=textbox
	@caption Pilt

	@property but_confirm type=textbox
	@caption Kinnitus

@default group=men_props 


	@layout hbox1 type=hbox group=men_props
	@property men_toolbar type=toolbar no_caption=1 store=no parent=hbox1

	@layout vbox1 type=vbox group=men_props 
	@layout hbox2 type=hbox group=men_props parent=vbox1 

	@property men_tree type=treeview parent=hbox2 no_caption=1
	@caption Puu

	@property men_list type=table parent=hbox2 no_caption=1
	@caption Tabel
	

@groupinfo but_props caption="Nupu m&auml;&auml;rangud"
@groupinfo men_props caption="Nupu m&auml;&auml;rangud" submit=no

*/

class property_toolbar_button extends class_base
{
	function property_toolbar_button()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/property_toolbar_button",
			"clid" => CL_PROPERTY_TOOLBAR_BUTTON
		));

		$this->button_types = array(
			"sep" => t("Eraldaja"),
			"but" => t("Nupp"), 
			"men" => t("Menu&uuml;&uuml;")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "b_type":
				$prop["options"] = $this->button_types;
				break;

			case "men_tree":
				$this->get_men_tree($arr);
				break;

			case "men_list":
				$this->get_men_list($arr);
				break;

			case "men_toolbar":
				$this->get_men_toolbar($arr);
				break;
		};
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

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "general")
		{
			return true;
		}

		if ($arr["id"] == "but_props" && $arr["obj_inst"]->prop("b_type") == "but")
		{
			return true;
		}

		if ($arr["id"] == "men_props" && $arr["obj_inst"]->prop("b_type") == "men")
		{
			return true;
		}

		return false;
	}

	function get_button($but, &$tb)
	{
		switch($but->prop("b_type"))
		{
			case "sep":
				$tb->add_separator();
				break;

			case "men":
				$this->_get_menu_button($but, $tb);
				break;

			case "but":
				$this->_get_but_button($but, $tb);
				break;

			default:
				break;
		}
	}

	function _get_menu_button($but, &$tb)
	{
		$tb->add_menu_button(array(
			"name" => "0",
			"img" => "new.gif",
		));

		$items = safe_array($but->meta("but_items"));

		uasort($items, array(&$this, "__itemsorter"));
		$this->_req_menu_button($tb, $items, 0);
	}

	function _req_menu_button(&$tb, $items, $pt)
	{
		$l_items = $this->_get_items_by_parent($items, $pt);
		foreach($l_items as $nr => $item)
		{
			$sub_c = $this->_req_menu_button($tb, $items, $nr);
			if ($sub_c > 0)
			{
				$tb->add_sub_menu(array(
					"parent" => $pt,
					"name" => $nr,
					"text" => $item["name"]
				));
			}
			else
			if ($item["is_sep"])
			{
				$tb->add_menu_separator(array(
					"parent" => $pt,	
				));
			}
			else
			{
				$tb->add_menu_item(array(
					"parent" => $pt,	
					"text" => $item["name"],
					"link" => $item["url"],
				));
			}
		}

		return count($l_items);
	}

	function _get_items_by_parent($items, $pt)
	{
		$ret = array();
		foreach($items as $k => $item)
		{
			if ($item["parent"] == $pt)
			{
				$ret[$k] = $item;
			}
		}
		return $ret;
	}

	function _get_but_button($but, &$tb)
	{
		$tb->add_button(array(
			"name" => $but->name(),
			"action" => $but->prop("but_action"),
			"tooltip" => $but->prop("but_tooltip"),
			"img" => $but->prop("but_img"),
			"confirm" => $but->prop("but_confirm")
		));
	}

	function __itemsorter($a, $b)
	{
		if ($a["ord"] == $b["ord"])
		{
			return 0;
		}
		return $a["ord"] > $b["ord"] ? 1 : -1;
	}

	function get_men_tree($arr)
	{
		$items = safe_array($arr["obj_inst"]->meta("but_items"));

		$arr["prop"]["vcl_inst"]->add_item(0,array(
			"name" => $arr["obj_inst"]->name(),
			"id" => $arr["obj_inst"]->id(),
			"url" => aw_url_change_var ("t_item", $item["id"]),
		));

		uasort($items, array(&$this, "__itemsorter"));

		$var = "t_item";
		foreach($items as $item)
		{
			$oname = $item["name"];

			if ($arr["request"][$var] == $num)
			{
				$oname = "<b>".$oname."</b>";
			}

			$parent = $item["parent"];
			if ($parent == "")
			{
				$parent = $arr["obj_inst"]->id();
			}

			if ($parent == $item["id"])
			{
				continue;
			}

			$arr["prop"]["vcl_inst"]->add_item($parent,array(
				"name" => $oname,
				"id" => $item["id"],
				"url" => aw_url_change_var ("t_item", $item["id"]),
			));
		}
	}

	function _init_men_list_t(&$t)
	{	
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "url",
			"caption" => t("URL"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "is_sep",
			"caption" => t("Eraldaja?"),
			"align" => "center",
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id"
		));
		$t->set_sortable(false);
	}

	function get_men_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_men_list_t($t);

		$items = safe_array($arr["obj_inst"]->meta("but_items"));
		uasort($items, array(&$this, "__itemsorter"));

		$parent = $arr["request"]["t_item"];

		foreach($items as $nr => $item)
		{
			if ($item["parent"] != $parent)
			{	
				continue;
			}

			$t->define_data(array(
				"name" => html::textbox(array(
					"name" => "items[$nr][name]",
					"value" => $item["name"],
				)),
				"ord" => html::textbox(array(
					"name" => "items[$nr][ord]",
					"value" => $item["ord"],
				)),
				"url" => html::textbox(array(
					"name" => "items[$nr][url]",
					"value" => $item["url"],
				)),
				"is_sep" => html::checkbox(array(
					"name" => "items[$nr][is_sep]",
					"value" => 1,
					"checked" => ($item["is_sep"] == 1 ? true : false)
				)),
				"id" => $nr
			));
		}

		$nr = count($items) ? max(array_keys($items))+1 :  1; 

		$t->define_data(array(
			"name" => html::textbox(array(
				"name" => "items[$nr][name]",
				"value" => "",
			)),
			"ord" => html::textbox(array(
				"name" => "items[$nr][ord]",
				"value" => "",
			)),
			"url" => html::textbox(array(
				"name" => "items[$nr][url]",
				"value" => "",
			)),
			"is_sep" => html::checkbox(array(
				"name" => "items[$nr][is_sep]",
				"value" => 1,
				"checked" => false
			)),
			"id" => $nr
		));
	}

	function get_men_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "save_men",
			"img" => "save.gif"
		));

		$tb->add_button(array(
			"name" => "del",
			"tooltip" => t("Kustuta"),
			"action" => "del_men",
			"img" => "delete.gif"
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["return_url"] = aw_global_get("REQUEST_URI");
		$arr["t_item"] = $_GET["t_item"];
	}

	/**

		@attrib name=save_men

	**/
	function save_men($arr)
	{
		$o = obj($arr["id"]);
		$mens = safe_array($o->meta("but_items"));
		$inf = safe_array($arr["items"]);

		foreach($mens as $nr => $item)
		{
			if ($item["parent"] != $arr["t_item"])
			{
				continue;
			}

			if (!isset($inf[$nr]))
			{
				unset($mens[$nr]);
			}
			else
			{
				if ($inf[$nr]["name"] == "" && $inf[$nr]["ord"] == "" && $inf[$nr]["url"] == "" && $inf[$nr]["is_sep"] == "")
				{
					unset($inf[$nr]);
					unset($mens[$nr]);
				}
				else
				{
					$mens[$nr]["name"] = $inf[$nr]["name"];
					$mens[$nr]["ord"] = $inf[$nr]["ord"];
					$mens[$nr]["url"] = $inf[$nr]["url"];
					$mens[$nr]["is_sep"] = $inf[$nr]["is_sep"];
					unset($inf[$nr]);
				}
			}
		}

		foreach($inf as $nr => $it)
		{
			if (!($inf[$nr]["name"] == "" && $inf[$nr]["ord"] == "" && $inf[$nr]["url"] == "" && $inf[$nr]["is_sep"] == ""))
			{
				$mens[$nr] = $inf[$nr];
				$mens[$nr]["parent"] = $arr["t_item"];
				$mens[$nr]["id"] = $nr;
			}
		}

		foreach($mens as $nr => $item)
		{
			if ($item["parent"] == $item["id"])
			{
				unset($mens[$nr]);
			}
		}

		$o->set_meta("but_items", $mens);
		$o->save();

		return $arr["return_url"];
	}

	/**

		@attrib name=del_men

	**/
	function del_men($arr)
	{
		$o = obj($arr["id"]);
		$mens = safe_array($o->meta("but_items"));

		foreach(safe_array($arr["sel"]) as $nr)
		{
			unset($mens[$nr]);
		}

		$o->set_meta("but_items", $mens);
		$o->save();

		return $arr["return_url"];
	}

	function get_generate_methods($tb_name, $but, &$meths)
	{
		if ($but->prop("b_type") == "but")
		{
			$meths[] = "on_submit_".$tb_name."_".$but->name();
		}
	}

	function get_method_contents($but, $meth)
	{
		switch($but->prop("b_type"))
		{
			case "sep":
				return "\t\t\$t->add_separator();\n";
				break;

			case "men":
				return $this->_get_mc_menu_button($but);
				break;

			case "but":
				return $this->_get_mc_but_button($but);
				break;

			default:
				break;
		}
	}

	function _get_mc_menu_button($but)
	{
		$ret  = "\t\t\$t->add_menu_button(array(\n";
		$ret .=	"\t\t\t\"name\" => \"0\",\n";
		$ret .= "\t\t\t\"img\" => \"new.gif\",\n";
		$ret .= "\t\t));\n";
		$ret .= "\n";

		$items = safe_array($but->meta("but_items"));
		uasort($items, array(&$this, "__itemsorter"));
		$this->_req_mc_menu_button($items, 0, $ret);

		return $ret;
	}

	function _req_mc_menu_button($items, $pt, &$ret)
	{
		$l_items = $this->_get_items_by_parent($items, $pt);
		foreach($l_items as $nr => $item)
		{
			$sub_c = $this->_req_mc_menu_button($items, $nr, &$ret);
			if ($sub_c > 0)
			{
				$ret .= "\t\t\$t->add_sub_menu(array(\n";
				$ret .= "\t\t\t\"parent\" => \"$pt\",\n";
				$ret .= "\t\t\t\"name\" => \"$nr\",\n";
				$ret .= "\t\t\t\"text\" => \"".$item["name"]."\"\n";
				$ret .= "\t\t));\n";
				$ret .= "\n";
			}
			else
			if ($item["is_sep"])
			{
				$ret .= "\t\t\$t->add_menu_separator(array(\n";
				$ret .= "\t\t\t\"parent\" => \"$pt\",\n";
				$ret .= "\t\t));\n";
				$ret .= "\n";
			}
			else
			{
				$ret .= "\t\t\$t->add_menu_item(array(\n";
				$ret .= "\t\t\t\"parent\" => \"$pt\",\n";
				$ret .= "\t\t\t\"text\" => \"".$item["name"]."\",\n";
				$ret .= "\t\t\t\"link\" => \"".$item["url"]."\",\n";
				$ret .= "\t\t));\n";
				$ret .= "\n";
			}
		}

		return count($l_items);
	}

	function _get_mc_but_button($but)
	{
		$ret  = "\t\t\$t->add_button(array(\n";
		$ret .=	"\t\t\t\"name\" => \"".$but->name()."\",\n";
		$ret .= "\t\t\t\"action\" => \"".$but->prop("but_action")."\",\n";
		$ret .= "\t\t\t\"tooltip\" => \"".$but->prop("but_tooltip")."\",\n";
		$ret .= "\t\t\t\"img\" => \"".$but->prop("but_img")."\",\n";
		$ret .= "\t\t\t\"confirm\" => \"".$but->prop("but_confirm")."\"\n";
		$ret .= "\t\t));\n";
		$ret .= "\n";
		return $ret;
	}
}
?>
