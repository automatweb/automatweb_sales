<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_DELIVERY_NOTE_ROW relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert
@tableinfo aw_shop_delivery_note_row master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_delivery_note_row
@default group=general

@property product type=relpicker reltype=RELTYPE_PRODUCT
@caption Artikkel

@property serial_no type=textbox
@caption Seerianumber

@property set_no type=textbox
@caption Partiinumber

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE
@caption Ladu

@property price type=textbox datatype=int
@caption Hind

@property unit type=relpicker reltype=RELTYPE_UNIT
@caption &Uuml;hik

@property amount type=textbox datatype=int
@caption Kogus

@reltype PRODUCT value=1 clid=CL_SHOP_PRODUCT
@caption Artikkel

@reltype WAREHOUSE value=2 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype UNIT value=3 clid=CL_UNIT
*/

class shop_delivery_note_row extends class_base
{
	const AW_CLID = 1470;

	function shop_delivery_note_row()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_delivery_note_row",
			"clid" => CL_SHOP_DELIVERY_NOTE_ROW
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
			$this->db_query("CREATE TABLE aw_shop_delivery_note_row(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "unit":
			case "product":
			case "warehouse":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$ret = true;
				break;
			case "amount":
			case "price":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				$ret = true;
				break;
			case "serial_no":
			case "set_no":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				$ret = true;
				break;
		}
		return $ret;
	}
}

?>
