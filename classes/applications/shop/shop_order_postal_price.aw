<?php
// shop_order_postal_price.aw - Poe postikulu
/*

@classinfo syslog_type=ST_SHOP_ORDER_POSTAL_PRICE relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@tableinfo aw_shop_postal_prices index=aw_oid master_index=brother_of master_table=objects

@default table=aw_shop_postal_prices
@default group=general

@property price_from type=textbox field=price_from
@caption Hind alates

@property price_to type=textbox field=price_to
@caption Hind kuni

@property date_from type=date_select field=date_from
@caption Kuup&auml;ev alates

@property date_to type=date_select field=date_to
@caption Kuup&auml;ev kuni

@property country type=relpicker multiple=1 field=country reltype=RELTYPE_COUNTRY
@caption Riik

@property postal type=callback callback=callback_postal_currencies
@caption Postikulu

@reltype COUNTRY value=1 clid=CL_CRM_COUNTRY
@caption Riik
*/

class shop_order_postal_price extends class_base
{
	function shop_order_postal_price()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_order_postal_price",
			"clid" => CL_SHOP_ORDER_POSTAL_PRICE
		));
	}

	function callback_postal_currencies($arr)
	{
		$retval = array();
		$ol = new object_list(array(
			"class_id" => CL_CURRENCY,
		));
		$vals = $arr["obj_inst"]->meta("postal");
		foreach($ol->arr() as $o)
		{
			$retval["c".$o->id()] = array(
				"type" => "textbox",
				"caption" => $o->name(),
				"name" => "c_".$o->id(),
				"value" => $vals[$o->id()],
			);
		}
		return $retval;
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
		if ($table === "aw_shop_postal_prices")
		{
			switch($field)
			{
				case "date_from":
				case "date_to":
				case "price_from":
				case "price_to":
				case "country":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "int"
					));
					return true;
					break;
				case "":
					$this->db_query("create table aw_shop_postal_prices (`aw_oid` int primary key)");
					return true;
					break;
			}
		}
	}
}

?>
