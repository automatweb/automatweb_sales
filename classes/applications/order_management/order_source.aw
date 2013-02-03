<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_order_source master_index=brother_of master_table=objects index=aw_oid

@default table=aw_order_source
@default group=general

*/

class order_source extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/order_management/order_source",
			"clid" => order_source_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_order_source" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_order_source` (
					`aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY	(`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "":
						$this->db_add_col($table, array(
							"name" => $field,
							"type" => "INT"
						));
						break;

				}
				$r = true;
			}
		}

		return $r;
	}
}
