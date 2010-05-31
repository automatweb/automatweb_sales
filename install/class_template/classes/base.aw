<?php

namespace automatweb;

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($table, $field)
	{
		if ("__table_name" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE __table_name(aw_oid int primary key)");
				return true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => ""
				));
				return true;
			}
		}
	}
}

?>
