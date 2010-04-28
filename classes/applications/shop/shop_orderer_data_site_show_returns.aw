<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_ORDERER_DATA_SITE_SHOW_RETURNS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_shop_orderer_data_site_show master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_orderer_data_site_show
@default group=general

@property template type=select
@caption Template
*/

class shop_orderer_data_site_show_returns extends shop_orderer_data_site_show
{
	const AW_CLID = 1571;

	function shop_orderer_data_site_show_returns()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_orderer_data_site_show",
			"clid" => CL_SHOP_ORDERER_DATA_SITE_SHOW_RETURNS
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

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$tpl = "show.tpl";
		if($arr["tpl"])
		{
			$tpl = $arr["tpl"];
		}
		$this->read_template($tpl);

		$vars = array(
			"name" => $ob->prop("name"),
		);
		$bills = new object_list();
		$rows = "";
		$co = get_current_company();
		$co = obj(2818612);
		if(is_object($co))
		{
			$orders = $co->get_warehouse_returns();
		}

		foreach($orders->arr() as $order)
		{
			$order_vars = array();
			$order_vars["id"] = $order->id();
			$order_vars["name"] = $order->name();
			$order_vars["number"] = $order->prop("number");
			$order_vars["currency"] = $order->prop("currency.name");
			$order_vars["date"] = date("d.m.Y" , $order->prop("date"));
			$order_vars["status"] = date("d.m.Y" , $order->prop("order_status"));
			$this->vars($order_vars);
			$rows.=$this->parse("ROW");
		}
		$vars["ROW"] = $rows;
		$this->vars($vars);
		return $this->parse();
	}
}

?>
