<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_WAREHOUSE_MOVEMENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_shop_warehouse_movement master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_warehouse_movement
@default group=general

@property from_wh type=relpicker reltype=RELTYPE_FROM_WAREHOUSE
@caption Laost

@property to_wh type=relpicker reltype=RELTYPE_TO_WAREHOUSE
@caption Lattu

@property product type=relpicker reltype=RELTYPE_PRODUCT
@caption Artikkel

@property single type=relpicker reltype=RELTYPE_SINGLE
@caption &Uuml;ksiktoode

@property amount type=textbox datatype=int
@caption Kogus

@property unit type=relpicker reltype=RELTYPE_UNIT
@caption &Uuml;hik

@property currency type=relpicker reltype=RELTYPE_CURRENCY
@caption Valuuta

@property price type=textbox datatype=int
@caption Hind

@property base_price type=textbox datatype=int
@caption Hind p&otilde;hivaluutas

@property transport type=textbox datatype=int
@caption Transport

@property customs type=textbox datatype=int
@caption Toll

@property date type=date_select
@caption Kuup&auml;ev

@property delivery_note type=relpicker reltype=RELTYPE_DELIVERY_NOTE
@caption Saateleht

@reltype FROM_WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype TO_WAREHOUSE value=2 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype PRODUCT value=3 clid=CL_SHOP_PRODUCT
@caption Artikkel

@reltype SINGLE value=4
@caption &Uuml;ksiktoode

@reltype UNIT value=5 clid=CL_UNIT
@caption &Uuml;hik

@reltype DELIVERY_NOTE value=6 clid=CL_SHOP_DELIVERY_NOTE
@caption Saateleht

@reltype CURRENCY value=7 clid=CL_CURRENCY
@caption Valuuta
*/

class shop_warehouse_movement extends class_base
{
	const AW_CLID = 1461;

	function shop_warehouse_movement()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_movement",
			"clid" => CL_SHOP_WAREHOUSE_MOVEMENT
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
			$this->db_query("CREATE TABLE aw_shop_warehouse_movement(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "from_wh":
			case "to_wh":
			case "product":
			case "single":
			case "unit":
			case "date":
			case "delivery_note":
			case "currency":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
				break;
			case "amount":
			case "price":
			case "transport":
			case "customs":
			case "base_price":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
				break;
		}
	}
}

?>
