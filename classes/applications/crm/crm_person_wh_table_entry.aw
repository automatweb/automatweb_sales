<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_PERSON_WH_TABLE_ENTRY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_crm_person_wh_table_entry master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_person_wh_table_entry
@default group=general

	@property wh_table type=relpicker reltype=RELTYPE_TABLE field=aw_wh_table automatic=1
	@caption T&ouml;&ouml;laud

	@property year type=select field=aw_year
	@caption Aasta

	@property month type=select field=aw_month
	@caption Kuu

@default group=table

	@property entry_table type=table store=no no_caption=1

@groupinfo table caption="Tabel"

@reltype TABLE value=1 clid=CL_CRM_PERSON_WH_TABLE
@caption T&ouml;&ouml;laud

*/

class crm_person_wh_table_entry extends class_base
{
	const AW_CLID = 1510;

	function crm_person_wh_table_entry()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_person_wh_table_entry",
			"clid" => CL_CRM_PERSON_WH_TABLE_ENTRY
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
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
			$this->db_query("CREATE TABLE aw_crm_person_wh_table_entry(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_wh_table":
			case "aw_person":
			case "aw_year":
			case "aw_month":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	function _get_year($arr)
	{
		$arr["prop"]["options"] = array();
		for($i = date("Y")-10; $i < date("Y")+10; $i++)
		{
			$arr["prop"]["options"][$i] = $i;
		}
	}

	function _get_month($arr)
	{
		$arr["prop"]["options"] = array();
		for($i = 1; $i < 12; $i++)
		{
			$arr["prop"]["options"][$i] = aw_locale::get_lc_month($i);
		}
	}

	function _get_wh_table($arr)
	{	
		if (empty($arr["prop"]["valiue"]) && !empty($arr["request"]["workspace"]))
		{
			$arr["prop"]["value"] = $arr["request"]["workspace"];
		}
	}

	private function _init_entry_table($t)
	{
		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"align" => "right",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "hours_cust",
			"caption" => t("Muutuvtunnid"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "hours_other",
			"caption" => t("P&uuml;&uuml;situnnid"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "hours_total",
			"caption" => t("Kokku tunnid"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
	}

	function _get_entry_table($arr)
	{
		$req = obj($arr["obj_inst"]->wh_table)->get_current_required_hours();
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_entry_table($t);

		$data = $arr["obj_inst"]->get_entry_data();

		foreach($req as $row)
		{
			$hours_cust = 0;
			$hours_other = 0;
			$hours_total = 0;
			if (isset($data[$row->person]))
			{
				$hours_cust = $data[$row->person]->hours_cust;
				$hours_other = $data[$row->person]->hours_other;
				$hours_total = $data[$row->person]->hours_total;
			}
			$t->define_data(array(
				"person" => html::obj_change_url($row->person),
				"hours_cust" => html::textbox(array("name" => "matrix[".$row->person."][hours_cust]", "size" => 5, "value" => $hours_cust)),
				"hours_other" => html::textbox(array("name" => "matrix[".$row->person."][hours_other]", "size" => 5, "value" => $hours_other)),
				"hours_total" => html::textbox(array("name" => "matrix[".$row->person."][hours_total]", "size" => 5, "value" => $hours_total)),
			));
		}
	}

	function _set_entry_table($arr)
	{	
		$req = obj($arr["obj_inst"]->wh_table)->get_current_required_hours();
		$data = $arr["obj_inst"]->get_entry_data();

		foreach($req as $row)
		{
			if (!isset($data[$row->person]))
			{
				$data[$row->person] = obj();
				$data[$row->person]->set_class_id(CL_CRM_PERSON_WH_TABLE_ENTRY_ROW);
				$data[$row->person]->set_parent($arr["obj_inst"]->id());
				$data[$row->person]->set_name(sprintf(t("Isiku %s t&ouml;&ouml;tundide sisestus %s/%s"), 
					obj($row->person)->name,
					$arr["obj_inst"]->year,
					$arr["obj_inst"]->month
				));
				$data[$row->person]->wh_table_entry = $arr["obj_inst"]->id();
				$data[$row->person]->person = $row->person;
			}

			$data[$row->person]->hours_cust = $arr["request"]["matrix"][$row->person]["hours_cust"];
			$data[$row->person]->hours_other = $arr["request"]["matrix"][$row->person]["hours_other"];
			$data[$row->person]->hours_total = $arr["request"]["matrix"][$row->person]["hours_total"];
			$data[$row->person]->save();
		}
	}
}

?>
