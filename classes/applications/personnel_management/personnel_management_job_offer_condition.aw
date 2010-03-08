<?php
/*
@classinfo syslog_type=ST_PERSONNEL_MANAGEMENT_JOB_OFFER_CONDITION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_personnel_management_job_offer_condition master_index=brother_of master_table=objects index=aw_oid

@default table=aw_personnel_management_job_offer_condition
@default group=general

	@property prop type=textbox field=aw_prop
	@caption Omadus

	@property type type=select field=aw_type
	@caption T&uuml;&uuml;p

	@property value type=textbox field=aw_value
	@caption V&auml;&auml;rtus

*/

class personnel_management_job_offer_condition extends class_base
{
	function personnel_management_job_offer_condition()
	{
		$this->init(array(
			"tpldir" => "applications/personnel_management/personnel_management_job_offer_condition",
			"clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER_CONDITION
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
			$this->db_query("CREATE TABLE aw_personnel_management_job_offer_condition(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_type":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_prop":
			case "aw_value":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
		}
	}
}

?>
