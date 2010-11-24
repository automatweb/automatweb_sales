<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo __table_name master_index=brother_of master_table=objects index=aw_oid

@default table=__table_name
@default group=general

*/

class __classname extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "__tplfolder",
			"clid" => __classdef
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("__table_name" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `__table_name` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("__table_name", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
