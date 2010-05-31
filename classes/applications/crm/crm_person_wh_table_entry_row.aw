<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_PERSON_WH_TABLE_ENTRY_ROW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_crm_person_wh_table_entry_row master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_person_wh_table_entry_row
@default group=general

	@property wh_table_entry type=relpicker reltype=RELTYPE_TABLE_ENTRY field=aw_wh_table_entry 
	@caption T&ouml;&ouml;aja sisestus

	@property person type=relpicker reltype=RELTYPE_PERSON field=aw_person
	@caption Isik


	@property hours_cust type=textbox size=5 field=aw_hours_cust
	@caption Muutuvtunde

	@property hours_other type=textbox size=5 field=aw_hours_other
	@caption P&uuml;&uuml;situnde

	@property hours_total type=textbox size=5 field=aw_hours_total
	@caption Kokku tunde


@reltype TABLE_ENTRY value=1 clid=CL_CRM_PERSON_WH_TABLE_ENTRY
@caption T&ouml;&ouml;aja sisestus

@reltype PERSON value=2 clid=CL_CRM_PERSON
@caption Isik

*/

class crm_person_wh_table_entry_row extends class_base
{
	const AW_CLID = 1514;

	function crm_person_wh_table_entry_row()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_person_wh_table_entry_row",
			"clid" => CL_CRM_PERSON_WH_TABLE_ENTRY_ROW
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

	function callback_mod_reforb(&$arr)
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
			$this->db_query("CREATE TABLE aw_crm_person_wh_table_entry_row(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_wh_table_entry":
			case "aw_person":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_hours_cust":
			case "aw_hours_other":
			case "aw_hours_total":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}

?>
