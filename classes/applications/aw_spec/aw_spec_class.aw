<?php
/*
@classinfo syslog_type=ST_AW_SPEC_CLASS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo allow_rte=2
@tableinfo aw_spec_class master_index=brother_of master_table=objects index=aw_oid

@default table=aw_spec_class
@default group=general

	@property desc type=textarea rows=10 cols=50 field=aw_desc  richtext=1
	@caption Kirjeldus

	@property pri type=select field=aw_pri
	@caption Prioriteet

	@property ini_settings type=textarea rows=10 cols=50 field=aw_ini_settings  richtext=1
	@caption INI settingud

	@property errors_and_messages type=textarea rows=10 cols=50 field=aw_errors_and_messages  richtext=1
	@caption Teated ja vead

@default group=use_cases

	@property use_cases type=releditor mode=manager reltype=RELTYPE_USE_CASE no_caption=1 store=connect props=name table_fields=name direct_links=1

@groupinfo use_cases caption="Kasutuslood"

@reltype USE_CASE value=10 clid=CL_AW_SPEC_USE_CASE 
@caption Kasutuslugu
*/

class aw_spec_class extends class_base
{
	function aw_spec_class()
	{
		$this->init(array(
			"tpldir" => "applications/aw_spec/aw_spec_class",
			"clid" => CL_AW_SPEC_CLASS
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function get_embed_prop($o, $arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_table($t);
		$t->set_caption(sprintf(t("Sisesta klassi %s grupid"), $o->name()));

		$data = $o->spec_group_list();
		$data[-1] = obj();
		$data[-2] = obj();
		$data[-3] = obj();
		$data[-4] = obj();
		$data[-5] = obj();

		$group_picker = $this->_get_group_picker($o);		

		foreach($data as $idx => $g_obj)
		{
			$t->define_data(array(
				"group_name" => html::textbox(array(
					"name" => "gp_data[".$idx."][group_name]",
					"value" => $g_obj->name(),
				)),
				"parent_group_name" => html::select(array(
					"name" => "gp_data[".$idx."][parent_group_name]",
					"value" => $g_obj->comment(),
					"options" => $group_picker
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

	private function _get_group_picker($o)
	{
		$rv = array("" => t("--vali--"));
		foreach($o->spec_group_list() as $idx => $g_obj)
		{
			$rv[$idx] = $g_obj->name();
		}
		return $rv;
	}

	private function _init_table($t)
	{
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
		));
		$t->define_field(array(
			"name" => "group_name",
			"caption" => t("Grupi nimi"),
		));
		$t->define_field(array(
			"name" => "parent_group_name",
			"caption" => t("Parent grupp"),
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));
	}


	function set_embed_prop($o, $arr)
	{
		$o->set_spec_group_list($arr["request"]["gp_data"]);
	}

	function set_embed_rels($o, $arr)
	{
		$o->set_spec_relation_list($arr["request"]["gp_data"]);
	}


	function get_tree_items($tree, $o, $pt, $g_pt = "")
	{
		foreach($o->spec_group_list() as $cl_oid => $cl)
		{
			$has_cb = false;
			$t = obj($cl_oid);
			if (method_exists($t->instance(), "get_tree_items"))
			{
				$has_cb = true;
			}

			if ($cl->comment() == $g_pt)
			{
				$id = $pt."_".$cl_oid;
				$tree->add_item($pt, array(
					"id" => $id,
					"url" => aw_url_change_var("disp", "classes_classes", aw_url_change_var("disp2", $cl_oid)),
					"name" => $_GET["disp2"] == $cl_oid ? "<b>".$cl->name()."</b>" : $cl->name(),
					"iconurl" => aw_ini_get("baseurl")."/automatweb/images/icons/archive.gif"
				));

				if ($cl->comment() == "")
				{
					$this->get_tree_items($tree, $o, $id, $cl_oid);
				}
				else
				if ($has_cb)
				{
					$t->instance()->get_tree_items($tree, $t, $id);
				}
			}
		}
	}

	function get_overview($o, $t, $prnt_num)
	{
		$group_picker = $this->_get_group_picker($o);		

		$num = 0;
		foreach($o->spec_group_list() as $idx => $g_obj)
		{
			$np = aw_spec::format_chapter_num($prnt_num, ++$num);

			$str .= aw_spec::format_doc_entry(
				$np, 
				sprintf(t("Grupp: %s"), $g_obj->name()),
				$g_obj->comment() ? sprintf(t("Parent grupp: %s"), $group_picker[$g_obj->comment()]) : ""
			);

			if (($val = $g_obj->instance()->get_overview($g_obj, $t, $np)) !== null)
			{
				$str .= $val;
			}
		}
		return $str;
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_spec_class(aw_oid int primary key, aw_desc text)");
			return true;
		}

		switch($f)
		{
			case "aw_pri":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_ini_settings":
			case "aw_errors_and_messages":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "mediumtext"
				));
				return true;
		}
	}

	function get_embed_rels($o, $arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_rels_table($t);
		$t->set_caption(sprintf(t("Sisesta klassi %s seosed"), $o->name()));

		$data = $o->spec_relation_list();
		$data[-1] = obj();
		$data[-2] = obj();
		$data[-3] = obj();
		$data[-4] = obj();
		$data[-5] = obj();

		$class_picker = aw_spec::get_class_picker(obj($o->parent()));

		foreach($data as $idx => $g_obj)
		{
			$t->define_data(array(
				"rel_name" => html::textbox(array(
					"name" => "gp_data[".$idx."][rel_name]",
					"value" => $g_obj->name(),
				)),
				"rel_to" => html::select(array(
					"name" => "gp_data[".$idx."][rel_to]",
					"value" => $g_obj->prop("rel_to"),
					"options" => $class_picker
				)),
				"jrk" => html::textbox(array(
					"name" => "gp_data[".$idx."][jrk]",
					"value" => $g_obj->ord(),
					"size" => 5
				)),
				"sort_jrk" => is_oid($g_obj->id()) ? $g_obj->ord() : 1000000000
			));
		}
		$t->set_default_sortby("sort_jrk");
		$t->set_numeric_field("sort_jrk");
	}

	private function _init_rels_table($t)
	{
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
		));
		$t->define_field(array(
			"name" => "rel_name",
			"caption" => t("Soese nimi"),
		));
		$t->define_field(array(
			"name" => "rel_to",
			"caption" => t("Seos klassiga"),
		));
	}

	function _get_pri($arr)
	{
		$arr["prop"]["options"] = aw_spec_obj::get_priority_options();
	}
}

?>