<?php
/*
@classinfo syslog_type=ST_SHOP_ORDERER_DATA_SITE_SHOW_ORDERS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_shop_orderer_data_site_show master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_orderer_data_site_show
@default group=general

@property template type=select
@caption Template
*/

class shop_orderer_data_site_show_orders extends shop_orderer_data_site_show
{
	function shop_orderer_data_site_show_orders()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_orderer_data_site_show",
			"clid" => CL_SHOP_ORDERER_DATA_SITE_SHOW_ORDERS
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

		if(is_object($co))
		{
			$orders = $co->get_sell_orders();
		}

		$person = get_current_person();
		if(is_object($person))
		{
			if(empty($orders))
			{
				$orders = new object_list();
			}
			$person_orders =  $person->get_sell_orders();
			if($person_orders->count())
			{
				$orders->add($person_orders);
			}
		}

		//selle kasutajaga thetud tellimused ka k6ik n2htavale
		if(aw_global_get("uid"))
		{
			$ol = new object_list(array(
				"class_id" => CL_SHOP_SELL_ORDER,
				"lang_id" => array(),
				"site_id" => array(),
				"createdby" => aw_global_get("uid"),
			));
			if($ol->count())
			{
				$orders->add($ol);
			}
		}



		$order_inst = get_instance(CL_SHOP_SELL_ORDER);
		
		foreach($orders->arr() as $order)
		{
			$order_vars = array();
			$order_vars["id"] = $order->id();
			$order_vars["name"] = $order->name();
			$order_vars["number"] = $order->prop("number");
			$order_vars["currency"] = $order->prop("currency.name");
			$order_vars["date"] = date("d.m.Y" , $order->prop("date"));
			$order_vars["status"] = $order_inst->states[$order->prop("order_status")];
			$order_vars["sum"] = $order->get_sum();
			$order_vars["url"] = $order_inst->mk_my_orb("show" , array("id" => $order->id()));
			$this->vars($order_vars);
			$rows .= $this->parse("ROW");
		}

		$vars["ROW"] = $rows;
		$this->vars($vars);
		return $this->parse();
	}
}

?>
