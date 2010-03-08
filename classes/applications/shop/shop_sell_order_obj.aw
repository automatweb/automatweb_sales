<?php

class shop_sell_order_obj extends _int_object
{
	/** returns order price
		@attrib api=1
		@returns double
			order rows price sum
	**/
	public function get_sum()
	{
		$sum = 0;
		foreach($this->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$row = $c->to();
			$c_sum = $row->amount * $row->price;
			$sum += $c_sum;
		}
		return $sum;
	}

	function save($exclusive = false, $previous_state = null)
	{
//		if(empty($this->order_status))
//		{
//			$this->set_prop("order_status" , "1");
//		}
		$r =  parent::save($exclusive, $previous_state);
		return $r;
	}

	function prop($name)
	{
		switch($name)
		{
			case "shop_delivery_type":
				if(!parent::prop("shop_delivery_type") && $this->prop("transp_type.class_id") == CL_SHOP_DELIVERY_METHOD)
				{
					$name = "transp_type";
				}
			break;
		}


		return parent::prop($name);
	}


	/** adds new row
		@attrib api=1 params=name
		@param price optional type=double
		@param product optional type=oid
		@param product_name optional type=string
		@param amount optional type=double
		@param code optional type=string
			product code
		@returns oid
			new row id
	**/
	public function add_row($data)
	{
		$o = new object();
		$o->set_class_id(CL_SHOP_ORDER_ROW);
		$o->set_name($this->name()." ".t("rida"));
		$o->set_parent($this->id());
		$o->set_prop("prod" , $data["product"]);
		if(empty($data["product_name"]))
		{
			$o->set_prop("prod_name" , get_name($data["product"]));
		}
		else
		{
			$o->set_prop("prod_name" , $data["product_name"]);
		}
		$o->set_prop("items" , $data["amount"]);
		$o->set_prop("amount" , $data["amount"]);
		$o->set_prop("price" , $data["price"]);
		$o->set_prop("other_code" , $data["code"]);
		$o->set_prop("date", time());
		$o->save();
		$this->connect(array(
			"to" => $o->id(),
			"type" => "RELTYPE_ROW"
		));
		return $o->id();
	}

	/** returns order orderer e-mail address
		@attrib api=1
		@returns string
			mail address
	**/
	public function get_orderer_mail()
	{
		$orderer = $this->prop("purchaser");
		if(is_oid($orderer))
		{
			$o = obj($orderer);
			return $o->get_mail();
		}
		return null;
	}

}

?>
