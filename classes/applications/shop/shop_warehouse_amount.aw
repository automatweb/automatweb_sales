<?php
/*
@classinfo syslog_type=ST_SHOP_WAREHOUSE_AMOUNT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_shop_warehouse_amount master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_warehouse_amount
@default group=general

@property product type=relpicker reltype=RELTYPE_PRODUCT
@caption Artikkel

@property single type=relpicker reltype=RELTYPE_SINGLE
@caption &Uuml;ksiktoode

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE
@caption Ladu

@property amount type=textbox datatype=int
@caption Kogus

@property unit type=relpicker reltype=RELTYPE_UNIT
@caption &Uuml;hik

@property is_default type=hidden

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT
@caption Artikkel

@reltype WAREHOUSE value=2 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype UNIT value=3 clid=CL_UNIT
@caption &Uuml;hik

@reltype SINGLE value=4 clid=CL_SHOP_PRODUCT_SINGLE
@caption &Uuml;ksiktoode
*/

class shop_warehouse_amount extends class_base
{
	function shop_warehouse_amount()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_amount",
			"clid" => CL_SHOP_WAREHOUSE_AMOUNT
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
			$this->db_query("CREATE TABLE aw_shop_warehouse_amount(aw_oid int primary key)");
			return true;
		}
		$ret = false;
		switch($f)
		{
			case "unit":
			case "product":
			case "warehouse":
			case "single":
			case "is_default":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$ret = true;
				break;
			case "amount":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				$ret = true;
				break;
		}

		switch($f)
		{
			case "product":
			case "unit":
			case "warehouse":
			case "single":
				$this->db_query("ALTER TABLE aw_shop_warehouse_amount ADD INDEX(".$f.")");
		}
		return $ret;
	}
}

?>
