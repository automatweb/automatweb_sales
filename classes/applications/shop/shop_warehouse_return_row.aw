<?php
/*
@classinfo syslog_type=ST_SHOP_WAREHOUSE_RETURN_ROW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=smeedia
@tableinfo aw_shop_warehouse_return_row master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_warehouse_return_row
@default group=general


@property prod type=relpicker reltype=RELTYPE_PRODUCT field=aw_product
@caption Toode

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE field=aw_warehouse
@caption Ladu

@property date type=datetime_select field=aw_date
@caption Aeg

@property unit type=relpicker reltype=RELTYPE_UNIT field=aw_unit
@caption &Uuml;hik

@property price type=textbox field=aw_price
@caption &Uuml;hiku hind

@property amount type=textbox table=aw_amount
@caption Kogus

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT,CL_SHOP_PRODUCT_PACKAGING
@caption Toode

@reltype UNIT value=2 clid=CL_UNIT
@caption &Uuml;hik

@reltype WAREHOUSE value=3 clid=CL_SHOP_WAREHOUSE
@caption Ladu

*/

class shop_warehouse_return_row extends class_base
{
	function shop_warehouse_return_row()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_return_row",
			"clid" => CL_SHOP_WAREHOUSE_RETURN_ROW
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
			$this->db_query("CREATE TABLE aw_shop_warehouse_return_row(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_product":
			case "aw_warehouse":
			case "aw_date":
			case "aw_unit":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "aw_amount":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}

?>
