<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_tree_branch.aw,v 1.6 2007/12/06 14:33:04 kristo Exp $
// property_tree_branch.aw - Puu oks 
/*

@classinfo syslog_type=ST_PROPERTY_TREE_BRANCH relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general

@default group=add

	@property addable_types type=table no_caption=1

	@property apply_level type=checkbox ch_value=1 field=meta method=serialize
	@caption Kehtib tervele tasemele

@default group=filter

	@property filter type=table no_caption=1
	
@groupinfo add caption="Lisatavad objektid"
@groupinfo filter caption="Andmete filter"
*/

class property_tree_branch extends class_base
{
	function property_tree_branch()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/property_tree_branch",
			"clid" => CL_PROPERTY_TREE_BRANCH
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "addable_types":
				$this->_addable_types($arr);
				break;

			case "filter":
				$this->_filter($arr);
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
			case "addable_types":
				$t = array();	
				foreach(safe_array($arr["request"]["addable_types"]) as $r)
				{
					if ($r["clid"])
					{
						$t[] = $r;
					}
				}
				$arr["obj_inst"]->set_meta("addable_types", $t);
				break;

			case "filter":
				$t = array();	
				foreach(safe_array($arr["request"]["prop_filter"]) as $r)
				{
					if ($r["prop"])
					{
						$t[] = $r;
					}
				}
				$arr["obj_inst"]->set_meta("prop_filter", $t);
				break;
		}
		return $retval;
	}	

	function _init_addable_types_t(&$t)
	{
		$t->define_field(array(
			"name" => "clid",
			"caption" => t("Klass"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "rel",
			"caption" => t("Seoset&uuml;&uuml;p"),
			"align" => "center"
		));
	}

	function _get_designer($o)
	{
		$pt = $o->path();
		foreach($pt as $p)
		{
			if ($p->class_id() == CL_CLASS_DESIGNER)
			{
				return $p;
			}
		}
		return NULL;
	}

	function _get_tree($o)
	{
		$pt = $o->path();
		foreach($pt as $p)
		{
			if ($p->class_id() == CL_PROPERTY_TREE)
			{
				return $p;
			}
		}
		return NULL;
	}

	function _addable_types($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_addable_types_t($t);

		$d = $this->_get_designer($arr["obj_inst"]);
		$ol = new object_list($d->connections_from(array("type" => "RELTYPE_RELATION")));
		$rels = array("" => "") + $ol->names();

		$addt = safe_array($arr["obj_inst"]->meta("addable_types")) + array("" => "");
		foreach($addt as $nr => $add)
		{
			$t->define_data(array(
				"clid" => html::select(array(
					"name" => "addable_types[$nr][clid]",
					"options" => array("" => "") + get_class_picker(),
					"value" => $addt[$nr]["clid"]
				)),
				"rel" => html::select(array(
					"name" => "addable_types[$nr][rel]",
					"options" => $rels,
					"value" => $addt[$nr]["rel"]
				)),
			));
		}
		$t->set_sortable(false);
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function get_vis_tree_item(&$tv, $o, $var, $el)
	{
		$oname = $o->name();
		$num = $o->id();

		if ($var && $_GET[$var] == $num)
		{
			$oname = "<b>".$oname."</b>";
		}

		$parent = $o->parent();
		if ($parent == $el->id())
		{
			$parent = 0;
		}

		$tv->add_item($parent,array(
			"name" => $oname,
			"id" => $num,
			"url" => aw_url_change_var ($var, $num),
			"iconurl" => (icons::get_icon_url(CL_MENU,"")),
			"checkbox" => $checkbox_status,
		));
	}

	function do_generate_method($el, $item, $var)
	{
		$ret = "";

		$pt = $item->parent() == $el->id() ? "0" : $item->parent();

		$ret .= "\t\t\$t->add_item($pt, array(\n";
		$ret .= "\t\t\t\"name\" => \$arr[\"request\"][\"$var\"] == ".$item->id()." ? \"<b>".$item->name()."</b>\" : \"".$item->name()."\",\n";
		$ret .= "\t\t\t\"id\" => ".$item->id().",\n";
		$ret .= "\t\t\t\"url\" => aw_url_change_var(\"$var\", ".$item->id()."),\n";
		$ret .= "\t\t\t\"iconurl\" => icons::get_icon_url(CL_MENU, \"\"),\n";
		$ret .= "\t\t));\n";
		$ret .= "\t\t\n";

		return $ret;
	}

	function get_add_menu($o)
	{
		$ret = array();
		$nr = 1;
		$cld = aw_ini_get("classes");
		foreach(safe_array($o->meta("addable_types")) as $item)
		{
			$ret[$nr] = array(
				"name" => $cld[$item["clid"]]["name"],
				"ord" => $nr,
				"url" => html::get_new_url($item["clid"], $_GET["id"], array(
					"return_url" => get_ru(), 
					"alias_to" => $_GET["id"],
					"reltype" => $item["rel"]
				)),
				"parent" => 0,
				"id" => $nr
			);
			$nr++;
		}
		return $ret;
	}

	function _init_filter_t(&$t)
	{
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "value",
			"caption" => t("V&auml;&auml;rtus"),
			"align" => "center",
		));
	}

	function _filter($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_filter_t($t);

		// find the first table in the same tab and get columns from that
		// how to find the table:
		// find tree object
		$tree = $this->_get_tree($arr["obj_inst"]);

		// find any table that has the same parent
		$ol = new object_list(array(
			"parent" => $tree->parent(),
			"class_id" => CL_PROPERTY_TABLE
		));

		if (!$ol->count())
		{
			return;
		}

		$table = $ol->begin();
		do {
			if ($table->prop("demo_data_source") != "rels" || !is_oid($table->prop("demo_data_source_rel")) || !$this->can("view", $table->prop("demo_data_source_rel")))
			{
				$table = $ol->next();
			}
			else
			{
				break;
			}
		} while (!$ol->end());

		if (!$table || $table->prop("demo_data_source") != "rels" || !is_oid($table->prop("demo_data_source_rel")) || !$this->can("view", $table->prop("demo_data_source_rel")))
		{
			return;
		}

		$props = array("" => "");
		$relo = obj($table->prop("demo_data_source_rel"));
		$cfgu = get_instance("cfg/cfgutils");
		$clss = aw_ini_get("classes");
		foreach(safe_array($relo->prop("r_class_id")) as $clid)
		{
			$ps = $cfgu->load_properties(array(
				"clid" => $clid
			));
			foreach($ps as $pn => $pd)
			{
				if ($pd["store"] == "no")
				{
					continue;
				}
				$props[$clid.".".$pn] = $clss[$clid]["name"]." / ".$pd["caption"]." ($pn)";
			}
		}
		$table_i = $table->instance();

		// get the type of object the table contains

		$propf = safe_array($arr["obj_inst"]->meta("prop_filter")) + array("" => "");
		foreach($propf as $k => $v)
		{
			$t->define_data(array(
				"prop" => html::select(array(
					"name" => "prop_filter[$k][prop]",
					"options" => $props,
					"value" => $v["prop"]
				)),
				"value" => html::textbox(array(
					"name" => "prop_filter[$k][value]",
					"value" => $v["value"]
				)),
			));
		}

		$t->set_sortable(false);
	}
}
?>
