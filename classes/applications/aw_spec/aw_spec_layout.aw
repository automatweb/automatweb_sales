<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_AW_SPEC_LAYOUT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo allow_rte=2
@tableinfo aw_spec_layouts master_index=brother_of  master_table=objects index=aw_oid

@default table=aw_spec_layouts
@default group=general

@property layout_type type=select field=aw_layout_type
@caption Layoudi t&uuml;&uuml;p

@property parent_layout_name type=relpicker field=aw_parent_layout_name reltype=RELTYPE_PARENT_LAYOUT
@caption Parent layout

@default group=use_cases

	@property use_cases type=releditor mode=manager reltype=RELTYPE_USE_CASE no_caption=1 store=connect props=name table_fields=name direct_links=1

@groupinfo use_cases caption="Kasutuslood"

@reltype USE_CASE value=10 clid=CL_AW_SPEC_USE_CASE 
@caption Kasutuslugu

@reltype PARENT_LAYOUT value=1 clid=CL_AW_SPEC_LAYOUT
@caption Parent layout
*/

class aw_spec_layout extends class_base
{
	const AW_CLID = 1427;

	function aw_spec_layout()
	{
		$this->init(array(
			"tpldir" => "applications/aw_spec/aw_spec_layout",
			"clid" => CL_AW_SPEC_LAYOUT
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_spec_layouts(aw_oid int primary key, aw_layout_type varchar(255), aw_parent_layout_name int)");
			return true;
		}
	}

	function _get_layout_type($arr)
	{
		$arr["prop"]["options"] = aw_spec_group_obj::spec_layout_type_picker();
	}

	function get_embed_prop($o, $arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_table($t);
		$t->set_caption(sprintf(t("Sisesta klassi %s grupi %s layoudi %s omadused"), obj(obj($o->parent())->parent())->name(), obj($o->parent())->name(), $o->name()));

		$data = $o->spec_property_list();
		$data[-1] = obj();
		$data[-2] = obj();
		$data[-3] = obj();
		$data[-4] = obj();
		$data[-5] = obj();

		$prop_type_picker = $o->spec_prop_type_picker();	

		foreach($data as $idx => $g_obj)
		{
			$t->define_data(array(
				"layout_name" => html::textbox(array(
					"name" => "gp_data[".$idx."][prop_name]",
					"value" => $g_obj->name(),
				)),
				"layout_type" => html::select(array(
					"name" => "gp_data[".$idx."][prop_type]",
					"value" => $g_obj->prop("prop_type"),
					"options" => $prop_type_picker
				)),
				"layout_desc" => html::textarea(array(
					"name" => "gp_data[".$idx."][prop_desc]",
					"value" => $g_obj->prop("prop_desc"),
					"rows" => 3,
					"cols" => 30
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
			"caption" => t("Omaduse nimi"),
		));
		$t->define_field(array(
			"name" => "layout_type",
			"caption" => t("Omaduse t&uuml;&uuml;p"),
		));
		$t->define_field(array(
			"name" => "layout_desc",
			"caption" => t("Omaduse kirjeldus"),
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));
		$t->set_sortable(false);
	}

	function set_embed_prop($o, $arr)
	{
		$o->set_spec_property_list($arr["request"]["gp_data"]);
	}

	function get_overview($o, $t, $prnt_num)
	{
		$prop_type_picker = $o->spec_prop_type_picker();	

		$num = 0;
		foreach($o->spec_property_list() as $idx => $g_obj)
		{
			$np = aw_spec::format_chapter_num($prnt_num, ++$num);

			$str .= aw_spec::format_doc_entry(
				$np,
				sprintf(t("Omadus: %s"), $g_obj->name()),
				sprintf(t("T&uuml;&uuml;p: %s<br>"), $prop_type_picker[$g_obj->prop("prop_type")]).nl2br($g_obj->prop("prop_desc"))
			);
		}
		return $str;
	}
}

?>
