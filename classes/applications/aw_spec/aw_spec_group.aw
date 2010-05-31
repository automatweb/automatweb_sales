<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_AW_SPEC_GROUP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo allow_rte=2

@default table=objects
@default group=general

@default group=use_cases

	@property use_cases type=releditor mode=manager reltype=RELTYPE_USE_CASE no_caption=1 store=connect props=name table_fields=name direct_links=1

@groupinfo use_cases caption="Kasutuslood"

@reltype USE_CASE value=10 clid=CL_AW_SPEC_USE_CASE 
@caption Kasutuslugu
*/

class aw_spec_group extends class_base
{
	const AW_CLID = 1426;

	function aw_spec_group()
	{
		$this->init(array(
			"tpldir" => "applications/aw_spec/aw_spec_group",
			"clid" => CL_AW_SPEC_GROUP
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function get_embed_prop($o, $arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_table($t);
		$t->set_caption(sprintf(t("Sisesta klassi %s grupi %s layoudid"), obj($o->parent())->name(), $o->name()));

		$data = $o->spec_layout_list();
		$data[-1] = obj();
		$data[-2] = obj();
		$data[-3] = obj();
		$data[-4] = obj();
		$data[-5] = obj();

		$layout_picker = $this->_get_layout_picker($o);	
		$layout_type_picker = $o->spec_layout_type_picker();	

		foreach($data as $idx => $g_obj)
		{
			$t->define_data(array(
				"layout_name" => html::textbox(array(
					"name" => "gp_data[".$idx."][layout_name]",
					"value" => $g_obj->name(),
				)),
				"parent_layout_name" => html::select(array(
					"name" => "gp_data[".$idx."][parent_layout_name]",
					"value" => $g_obj->prop("parent_layout_name"),
					"options" => $layout_picker
				)),
				"layout_type" => html::select(array(
					"name" => "gp_data[".$idx."][layout_type]",
					"value" => $g_obj->prop("layout_type"),
					"options" => $layout_type_picker
				)),
				"jrk" => html::textbox(array(
					"name" => "gp_data[".$idx."][jrk]",
					"value" => $g_obj->ord(),
					"size" => 5
				)),
				"sort_jrk" => is_oid($g_obj->id()) ? $g_obj->ord() : 1000000000,
				"change" => is_oid($g_obj->id()) ? html::href(array(
					"url" => html::get_change_url($g_obj->id(), array("return_url" => get_ru())),
					"caption" => t("Muuda")
				)) : ""
			));
		}
		$t->set_default_sortby("sort_jrk");
		$t->set_numeric_field("sort_jrk");
	}

	private function _init_table($t)
	{
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
		));
		$t->define_field(array(
			"name" => "layout_name",
			"caption" => t("Layoudi nimi"),
		));
		$t->define_field(array(
			"name" => "layout_type",
			"caption" => t("Layoudi t&uuml;&uuml;p"),
		));
		$t->define_field(array(
			"name" => "parent_layout_name",
			"caption" => t("Layoudi parent"),
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));
	}

	private function _get_layout_picker($o)
	{
		$rv = array("" => t("--vali--"));
		foreach($o->spec_layout_list() as $idx => $g_obj)
		{
			$rv[$idx] = $g_obj->name();
		}
		return $rv;
	}

	function set_embed_prop($o, $arr)
	{
		$o->set_spec_layout_list($arr["request"]["gp_data"]);
	}


	function get_tree_items($tree, $o, $pt, $g_pt = "")
	{
		$has_items = false;
		foreach($o->spec_layout_list() as $cl_oid => $cl)
		{
			$has_cb = false;
			$t = obj($cl_oid);
			if (method_exists($t->instance(), "get_tree_items"))
			{
				$has_cb = true;
			}
			if ((int)$cl->prop("parent_layout_name") == (int)$g_pt)
			{
				$has_items = true;
				$id = $pt."_".$cl_oid;
				$tree->add_item($pt, array(
					"id" => $id,
					"url" => aw_url_change_var("disp", "classes_classes", aw_url_change_var("disp2", $cl_oid)),
					"name" => $_GET["disp2"] == $cl_oid ? "<b>".$cl->name()."</b>" : $cl->name(),
					"iconurl" => $cl->layout_type == "hbox" ? 
						aw_ini_get("baseurl")."/automatweb/images/split_cell_down.gif"
					:
						aw_ini_get("baseurl")."/automatweb/images/split_cell_left.gif"
				));
				if (!$this->get_tree_items($tree, $o, $id, $cl_oid))
				{
					if ($has_cb)
					{
						$t->instance()->get_tree_items($tree, $t, $id);
					}
				}
			}
		}
		return $has_items;
	}

	function get_overview($o, $t, $prnt_num)
	{
		$type_picker = $o->spec_layout_type_picker();
		$layout_picker = $this->_get_layout_picker($o);	

		$num = 0;
		foreach($o->spec_layout_list() as $idx => $g_obj)
		{
			$np = aw_spec::format_chapter_num($prnt_num,++$num);

			$str .= aw_spec::format_doc_entry(
				$np,
				sprintf(t("Layout: %s"), $g_obj->name()),
				sprintf(t("Parent: %s , t&uuml;&uuml;p: %s"), $layout_picker[$g_obj->prop("parent_layout_name")], $type_picker[$g_obj->prop("layout_type")])
			);
			
			$str .= $g_obj->instance()->get_overview($g_obj, $t, $np);
		}
		return $str;
	}
}

?>
