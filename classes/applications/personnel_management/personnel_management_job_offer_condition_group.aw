<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_PERSONNEL_MANAGEMENT_JOB_OFFER_CONDITION_GROUP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_personnel_management_job_offer_condition_group master_index=brother_of master_table=objects index=aw_oid

@default table=aw_personnel_management_job_offer_condition_group
@default group=general

### RELTYPES

@reltype CONDITION value=1 clid=CL_PERSONNEL_MANAGEMENT_JOB_OFFER_CONDITION
@caption Tingimus

*/

class personnel_management_job_offer_condition_group extends class_base
{
	const AW_CLID = 1544;

	function personnel_management_job_offer_condition_group()
	{
		$this->init(array(
			"tpldir" => "applications/personnel_management/personnel_management_job_offer_condition_group",
			"clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER_CONDITION_GROUP
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
			$this->db_query("CREATE TABLE aw_personnel_management_job_offer_condition_group(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
