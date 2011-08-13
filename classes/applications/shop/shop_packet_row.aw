<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_packet_row master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_packet_row
@default group=general

	@property jrk type=textbox table=objects
	@caption Jrk

	@property packet type=objpicker clid=CL_SHOP_PACKET field=aw_packet
	@caption Pakett

	@property item type=objpicker clid=CL_SHOP_PRODUCT,CL_SHOP_PRODUCT_PACKAGING field=aw_item
	@caption Artikkel

	@property amount type=textbox field=aw_amount
	@caption Kogus

*/

class shop_packet_row extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_packet_row",
			"clid" => shop_packet_row_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_shop_packet_row" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_shop_packet_row` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("aw_packet" === $field or "aw_item" === $field or "aw_amount" === $field)
			{
				$this->db_add_col("aw_shop_packet_row", array(
					"name" => $field,
					"type" => "int"
				));
				$r = true;
			}
		}

		return $r;
	}
}
