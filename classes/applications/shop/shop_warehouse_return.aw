<?php
/*
@classinfo syslog_type=ST_SHOP_WAREHOUSE_RETURN relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_shop_warehouse_return master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_warehouse_return
@default group=general


@property number type=textbox field=aw_number
@caption Number

@property buyer type=relpicker reltype=RELTYPE_BUYER field=aw_buyer
@caption Tellija

@property related_orders type=relpicker multiple=1 reltype=RELTYPE_PURCHASE_ORDER store=connect
@caption Seotud ostutellimused

@property date type=date_select field=aw_date
@caption Kuup&auml;ev

@property currency type=relpicker reltype=RELTYPE_CURRENCY automatic=1 field=aw_currency
@caption Valuuta

@property warehouse type=relpicker reltype=RELTYPE_WAREHOUSE automatic=1 field=aw_warehouse
@caption Ladu

@property status type=chooser default=0 field=aw_status
@caption Staatus


@reltype BUYER value=1 clid=CL_CRM_COMPANY
@caption Ostja

@reltype PURCHASE_ORDER value=2 clid=CL_SHOP_PURCHASE_ORDER
@caption Ostutellimus

@reltype CURRENCY value=3 clid=CL_CURRENCY
@caption Valuuta

@reltype WAREHOUSE value=4 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype ROW value=5 clid=CL_SHOP_WAREHOUSE_RETURN_ROW
@caption Rida

*/

class shop_warehouse_return extends class_base
{
	function shop_warehouse_return()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_warehouse_return",
			"clid" => CL_SHOP_WAREHOUSE_RETURN
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
			$this->db_query("CREATE TABLE aw_shop_warehouse_return(aw_oid int primary key, aw_number varchar(255), aw_buyer int, related_purcahse_orders int, aw_date int, aw_currency int, aw_warehouse int)");
			return true;
		}
		switch($f)
		{
			case "aw_status":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
				break;
		}
	}

}

?>
