<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_TIME_MANAGEMENT_INSTALLER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_time_management_installer master_index=brother_of master_table=objects index=aw_oid

@default table=aw_time_management_installer
@default group=general

*/

class time_management_installer extends class_base
{
	const AW_CLID = 1479;

	function time_management_installer()
	{
		$this->init(array(
			"tpldir" => "applications/spa_bookings/time_management_installer",
			"clid" => CL_TIME_MANAGEMENT_INSTALLER
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
			$this->db_query("CREATE TABLE aw_time_management_installer(aw_oid int primary key)");
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
