<?php

/*
@classinfo syslog_type=ST_WAREHOUSE relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_warehouse master_index=brother_of master_table=objects index=aw_oid


@groupinfo settings parent=general caption="Seaded"


@default table=aw_warehouse
@default group=settings

@property inventory_engine type=hidden

*/

class warehouse extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/inventory_management/warehouse",
			"clid" => CL_WAREHOUSE
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_warehouse" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_warehouse` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_warehouse", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
