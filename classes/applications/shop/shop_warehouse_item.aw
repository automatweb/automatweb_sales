<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_warehouse_item master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_warehouse_item
@default group=general

*/

class shop_warehouse_item extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_item",
			"clid" => shop_warehouse_item_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_shop_warehouse_item" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_shop_warehouse_item` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_shop_warehouse_item", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
