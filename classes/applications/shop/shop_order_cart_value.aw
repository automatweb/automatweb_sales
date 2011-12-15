<?php
<<<<<<< .mine
// shop_order_cart_value.aw - Poe ostukorvi v22rtus
=======>>>>>>> .theirs/*

<<<<<<< .mine@classinfo relationmgr=yes no_status=1 no_comment=1
=======@classinfo syslog_type=ST_SHOP_ORDER_CART_VALUE relationmgr=yes no_status=1 no_comment=1
>>>>>>> .theirs
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
<<<<<<< .mine		$soc = get_instance(CL_SHOP_ORDER_CART);
=======		$this->read_template("show.tpl");
>>>>>>> .theirs
		lc_site_load("shop", $this);
		$cart_id = isset($arr["cart"]) ? $arr["cart"] : automatweb::$request->arg("cart");
		$cart = obj($cart_id, array(), shop_order_cart_obj::CLID);
		$order = $cart->get_sell_order();
		$count = $order->get_rows_count();
		$order_total = $order->get_rows_total_price();

<<<<<<< .mine		list($t1, $t2) = $soc->get_cart_value(true);
		$this->read_template("show.tpl");

=======		// FIXME: What should be the difference between value and prod_value?
>>>>>>> .theirs		$this->vars(array(
			"value" => number_format($order_total, 2),
			"prod_value" => number_format($order_total, 2),
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
<<<<<<< .mine		$ret = $this->parse();
		return $ret;
=======		return $this->parse();
>>>>>>> .theirs	}
}
