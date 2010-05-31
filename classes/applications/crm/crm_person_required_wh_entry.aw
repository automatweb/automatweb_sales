<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_PERSON_REQUIRED_WH_ENTRY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_crm_person_required_wh_entry master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_person_required_wh_entry
@default group=general

	@property wh_table type=relpicker reltype=RELTYPE_TABLE field=aw_wh_table
	@caption T&ouml;&ouml;laud

	@property person type=relpicker reltype=RELTYPE_PERSON field=aw_person
	@caption Isik

	@property from type=date_select field=aw_from
	@caption Kehtib alates

	@property to type=date_select field=aw_to
	@caption Kehtib kuni

	@property hours_total type=textbox field=aw_hours_total size=5
	@caption Kokku tunde

	@property hours_cust type=textbox field=aw_hours_cust size=5
	@caption Muutuvtunde

	@property hours_other type=textbox field=aw_hours_other size=5
	@caption P&uuml;situnde

@reltype TABLE value=1 clid=CL_CRM_PERSON_WH_TABLE
@caption T&ouml;&ouml;laud

@reltype PERSON value=2 clid=CL_CRM_PERSON
@caption Isik

*/

class crm_person_required_wh_entry extends class_base
{
	const AW_CLID = 1512;

	function crm_person_required_wh_entry()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_person_required_wh_entry",
			"clid" => CL_CRM_PERSON_REQUIRED_WH_ENTRY
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
			$this->db_query("CREATE TABLE aw_crm_person_required_wh_entry(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_wh_table":
			case "aw_person":
			case "aw_from":
			case "aw_to":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_hours_total":
			case "aw_hours_cust":
			case "aw_hours_other":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}

?>
