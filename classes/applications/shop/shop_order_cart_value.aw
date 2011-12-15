<?php

// shop_order_cart_value.aw - Poe ostukorvi v22rtus
/*

@classinfo relationmgr=yes no_status=1 no_comment=1

*/

class shop_order_cart_value extends class_base
{
	function shop_order_cart_value()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_order_cart_value",
			"clid" => CL_SHOP_ORDER_CART_VALUE
		));
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !shows the object
	function show($arr)
	{
		$soc = get_instance(CL_SHOP_ORDER_CART);

		$count = 0;
		foreach($soc->get_items_in_cart() as $dat)
		{
			$dat = new aw_array($dat);
			foreach($dat->get() as $dat2)
			{
				$count += $dat2["items"];
			}
		}

		list($t1, $t2) = $soc->get_cart_value(true);
		$this->read_template("show.tpl");

		$this->vars(array(
			"value" => number_format($t1, 2),
			"prod_value" => number_format($t2, 2),
			"count" => $count
		));

		if ($count > 0)
		{
			$this->vars(array(
				"HAS_ITEMS" => $this->parse("HAS_ITEMS")
			));
		}
		else
		{
			$this->vars(array(
				"NO_ITEMS" => $this->parse("NO_ITEMS")
			));
		}
		$ret = $this->parse();
		return $ret;
	}
}
