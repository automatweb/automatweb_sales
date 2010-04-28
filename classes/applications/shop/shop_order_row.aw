<?php

namespace automatweb;
// shop_order_row.aw - Tellimuse rida
/*

@classinfo syslog_type=ST_SHOP_ORDER_ROW relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_order_rows index=aw_oid master_index=brother_of master_table=objects
@tableinfo aw_shop_order_rows_amount index=aw_oid master_index=brother_of master_table=objects

@default group=general
@default table=aw_shop_order_rows

@property prod_name type=textbox field=aw_prod_name
@caption Toote nimi

@property prod type=relpicker reltype=RELTYPE_PRODUCT field=aw_product
@caption Toode

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE field=aw_warehouse
@caption Ladu

@property date type=datetime_select field=aw_date
@caption Aeg

@property unit type=relpicker reltype=RELTYPE_UNIT
@caption &Uuml;hik

@property price type=textbox field=aw_prod_price
@caption &Uuml;hiku hind

@property items type=textbox field=aw_items
@caption Kogus t&uuml;kkides

@property required type=textbox
@caption Vajadus

@property amount type=textbox datatype=int table=aw_shop_order_rows_amount
@caption Kogus

@property real_amount type=text datatype=int
@caption Reaalne kogus

@property tax_rate type=relpicker reltype=RELTYPE_TAX_RATE
@caption Maksum&auml;&auml;r

@property other_code type=textbox
@caption Teine artiklikood

@property reservation type=checkbox ch_value=1 field=aw_reservation
@caption Broneering

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT
@caption Toode

@reltype UNIT value=2 clid=CL_UNIT
@caption &Uuml;hik

@reltype WAREHOUSE value=3 clid=CL_SHOP_WAREHOUSE
@caption Ladu
*/

class shop_order_row extends class_base
{
	const AW_CLID = 1415;

	function shop_order_row()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_order_row",
			"clid" => CL_SHOP_ORDER_ROW
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

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ($table === "aw_shop_order_rows")
		{
			if($field=="")
			{
				$this->db_query("CREATE TABLE aw_shop_order_rows (`aw_oid` int primary key)");
				return true;
			}
			switch($field)
			{
				case "aw_prod_name":
				case "other_code":
				case "required":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "VARCHAR(255)"
					));
					return true;
					break;
				case "aw_product":
				case "aw_items":
				case "unit":
				case "tax_rate":
				case "aw_warehouse":
				case "aw_date":
				case "aw_reservation":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "int"
					));
					return true;
					break;
				case "aw_prod_price":
				case "amount":
				case "real_amount":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "double"
					));
					return true;
					break;
			}
		}
		elseif($table == "aw_shop_order_rows_amount")
		{
			
			if($field=="")
			{
				$this->db_query("CREATE TABLE aw_shop_order_rows_amount (`aw_oid` int primary key)");
				return true;
			}
			switch($field)
			{
				case "amount":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "double"
					));
					$this->db_query("INSERT INTO aw_shop_order_rows_amount SELECT aw_oid, amount FROM aw_shop_order_rows");
					return true;
					break;
			}
					
		}
	}
}
?>
