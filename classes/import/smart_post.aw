<?php
/*
@classinfo syslog_type=ST_SMART_POST relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_smart_post master_index=brother_of master_table=objects index=aw_oid

@default table=aw_smart_post
@default group=general

@property xml_source type=textbox field=aw_xml_source
@caption XML allikas

@property cities type=table store=no
@caption Linnade prioriteedid

@property show_inactive type=checkbox field=aw_show_inactive
@caption N&auml;ita mitteaktiivseid

*/

class smart_post extends class_base
{
	function smart_post()
	{
		$this->init(array(
			"tpldir" => "import/smart_post",
			"clid" => CL_SMART_POST
		));
	}

	function _get_cities($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
			"sortable" => 1,
			"sorting_field" => "jrk_int"
		));
		$t->define_field(array(
			"name" => "city",
			"sortable" => 1,
			"caption" => t("Linn"),
		));
		$city_jrk = $arr["obj_inst"]->meta("cities");
		foreach(array_keys($arr["obj_inst"]->get_data_by_cities()) as $city)
		{
			$t->define_data(array(
				"city" => $city,
				"jrk" => html::textbox(array(
					"name" => "cities[".$city."]",
					"value" => isset($city_jrk[$city]) ? (int)$city_jrk[$city] : 0,
					"size" => 2,
				)),
				"jrk_int" => isset($city_jrk[$city]) ? (int)$city_jrk[$city] : 0,
			));
		}
		$t->set_numeric_field("jrk_int");
		$t->set_default_sortby(array("jrk_int","city"));
	}

	function _set_cities($arr)
	{
		$arr["obj_inst"]->set_meta("cities", $arr["prop"]["value"]);
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
			$this->db_query("CREATE TABLE aw_smart_post(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_show_inactive":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_xml_source":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;
		}
	}
}

?>
