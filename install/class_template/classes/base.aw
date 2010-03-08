<?php
/*
@classinfo syslog_type=__syslog_type relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=__maintainer
@tableinfo __table_name master_index=brother_of master_table=objects index=aw_oid

@default table=__table_name
@default group=general

*/

class __classname extends class_base
{
	function __classname()
	{
		$this->init(array(
			"tpldir" => "__tplfolder",
			"clid" => __classdef
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
			$this->db_query("CREATE TABLE __table_name(aw_oid int primary key)");
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
