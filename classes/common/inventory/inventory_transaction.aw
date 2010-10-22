<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_inventory_transaction master_index=brother_of master_table=objects index=aw_oid

@default table=aw_inventory_transaction
@default group=general

@property item type=hidden
@comment Item reference of current transaction's object. Data type integer (length 11). Holds aw object id-s

@property source_inventory type=hidden
@comment Source inventory reference. Data type integer (length 11). Holds aw object id-s

@property destination_inventory type=hidden
@comment Destination inventory reference. Data type integer (length 11). Holds aw object id-s

@property quantity type=hidden
@comment Base quantity to be transferred. Data type float

@property requester type=hidden
@comment Transaction requester identifier. Data type integer (length 11). Should hold aw object id-s

@property type type=hidden
@comment Transaction type identifier. Data type integer (length 11). Should hold aw object id-s

@property result_quantity type=hidden
@comment Quantity of item in source inventoryTransaction requester identifier. Data type integer (length 11). Should hold aw object id-s


*/

class inventory_transaction extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "common/inventory/inventory_transaction",
			"clid" => CL_INVENTORY_TRANSACTION
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;
@property item type=hidden
@property from_inventory type=hidden
@property to_inventory type=hidden
@property quantity type=hidden
@property requester type=hidden

		if ("aw_inventory_transaction" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_inventory_transaction` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  `item` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  `from_inventory` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  `to_inventory` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  `requester` int(11) UNSIGNED DEFAULT NULL,
				  `quantity` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_inventory_transaction", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}

?>
