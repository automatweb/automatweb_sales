<?php
/*
@classinfo syslog_type=ST_SHOP_ORDER_DISPLAY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_shop_order_display master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_order_display
@default group=general

	@property warehouse_list type=relpicker reltype=RELTYPE_WAREHOUSE multiple=1 store=connect
	@caption Laod, kust tellimusi n&auml;idatakse

@reltype WAREHOUSE value=1 clid=CL_SHOP_WAREHOUSE
@caption Ladu

*/

class shop_order_display extends class_base
{
	function shop_order_display()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_order_display",
			"clid" => CL_SHOP_ORDER_DISPLAY
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

		$ol = new object_list(array(
			"class_id" => CL_SHOP_SELL_ORDER,
			"purchaser" => "%",
			"limit" => 10,
			"lang_id" => array(),
			"site_id" => array()
		));

		$t = new aw_table();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center"
		));

		foreach($ol->arr() as $item)
		{
			$sum = 0;
			foreach($item->connections_from(array("type" => "RELTYPE_ROW")) as $c)
			{
				$tp = $c->to();
				$sum += $tp->price * $tp->prop("amount");
			}
			$t->define_data(array(
				"name" => html::href(array(
					"url" => $this->mk_my_orb("show", array("id" => $item->id()), "shop_sell_order"),
					"caption" => $item->name()
				)),
				"date" => $item->date,
				"sum" => $sum
			));
		}

		$this->vars(array(
			"table" => $t->draw(),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_order_display(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
