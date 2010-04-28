<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_ACTIVITY_STATS_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_crm_activity_stats_type master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_activity_stats_type
@default group=general

*/

class crm_activity_stats_type extends class_base
{
	const AW_CLID = 1465;

	function crm_activity_stats_type()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_activity_stats_type",
			"clid" => CL_CRM_ACTIVITY_STATS_TYPE
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
			$this->db_query("CREATE TABLE aw_crm_activity_stats_type(aw_oid int primary key)");
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
