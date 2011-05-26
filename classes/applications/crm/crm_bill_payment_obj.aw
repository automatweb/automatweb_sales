<?php

class crm_bill_payment_obj extends _int_object
{
	const CLID = 1383;

	function set_prop($name,$value)
	{
/*		if($name == "sum")
		{
			parent::set_prop($name,$value);
			if(!$this->id())
			{
				$this->save();//kui id'd pole , siis l2heb lolliks
			}
			$ol = new object_list(array(
				"class_id" => CL_CRM_BILL,
				"lang_id" => array(),
				"CL_CRM_BILL.RELTYPE_PAYMENT.id" => $this->id(),
			));
			$bi = get_instance(CL_CRM_BILL);
			foreach($ol -> arr() as $o)
			{
				$bill_sum = $bi->get_bill_sum($o);
				$sum = 0;
				foreach($o->connections_from(array("type" => "RELTYPE_PAYMENT")) as $conn)
				{
					$p = $conn->to();
					$sum = $sum + $p->get_free_sum($o);
				}
				if($bill_sum < $sum)
				{
					$sum = $bill_sum;
				}
				$o->set_prop("partial_recieved", $sum);
				$o->save();
			}
			return;
		}
*/
		if($name === "currency_rate")
		{
			if(!$value && $this->prop("currency"))
			{
				$ci = get_instance(CL_CURRENCY);
				$value = $ci->convert(array("sum" => 1, "from" => $this->prop("currency"), "date" => $this->prop("date") ? $this->prop("date") : time()));
			}
		}
	//	if($name == "currency")
	//	{
	//		$ol = new object_list(array(
	//			"class_id" => CL_CRM_BILL,
	//			"lang_id" => array(),
	//			"CL_CRM_BILL.RELTYPE_PAYMENT.id" => $this->id(),
	//		));
	//		$o = reset($ol->arr());
	//		$bi = get_instance(CL_CRM_BILL);
	//		if(is_object($o) && $bi->get_bill_currency_id($o) != $value)
	//		{
	//			return;
	//		}
	//	}
		parent::set_prop($name,$value);
	}

	/**
		@attrib api=1 all_args=1
	@returns double
	@param b optional type=oid
		bill object , if you want free sum for bill
	@comment
		returns available payment sum (not connected with bills or with given bill)
	**/
	function get_free_sum($b)
	{
		$sum = $this->prop("sum");
		$ol = new object_list(array(
			"class_id" => CL_CRM_BILL,
			"lang_id" => array(),
			"CL_CRM_BILL.RELTYPE_PAYMENT.id" => $this->id(),
		));
		foreach($ol->arr() as $o)
		{
			//$bill_sum = $bi->get_bill_sum($o);
			$bill_sum = $o->get_bill_needs_payment(array(
				"payment" => $this->id()
			));
			if($b && $b == $o->id())
			{
				return min($bill_sum , $sum);
			}
			$sum = $sum - $bill_sum;
		}
		if($sum < 0)
		{
			$sum = 0;
		}
		return $sum;
	}

	/**
	@attrib api=1 all_args=1
	@returns double
	@param b optional type=oid
		bill object , if you want free sum for bill
	@comment
		returns available payment sum (not connected with bills or with given bill)
	**/
	function get_connected_bills_sum()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_BILL,
			"CL_CRM_BILL.RELTYPE_PAYMENT.id" => $this->id()
		));
		$sum = 0;

		foreach($ol->arr() as $o)
		{
			$sum+= $this->get_bill_sum($o->id());
		}

		return $sum;
	}

	//bill, sum
	function set_bill_sum($arr)
	{
		$bill_sums = $this->meta("sum_for_bill");
		extract($arr);
		if(is_object($bill))
		{
			$bill = $bill->id();
		}
		$bill_sums[$bill] = $sum;
		$this->set_meta("sum_for_bill" , $bill_sums);
		//uuendab kogusumma ka 2ra
		$this->set_prop("sum" , array_sum($bill_sums));
		$this->save();
	}

	//bill
	function get_bill_sum($bill)
	{
		$bill_sums = $this->meta("sum_for_bill");
		if(is_object($bill))
		{
			$bill = $bill->id();
		}
		return $bill_sums[$bill];
	}

	/**
		@attrib api=1 all_args=1
		@param o required type=oid/object
			bill object you want to add
		@param sum optional type=int
			sum paid for bill
		@returns string error

		@comment
			adds bill to payment or returns error message if cant
	**/
	function add_bill($arr)
	{
		extract($arr);
		//kui in id, siis objektiks
		if(!is_object($o) && object_loader::can("view", $o))
		{
			$o = obj($o);
		}

		//m6ned asjad mis v6ivad saada operatsiooni takistuseks
		//seda esimest pole vaja t6en2oliselt, sest summa laekumisel on tegelikult selline s6ltuv suurus
//		if(!$this->get_free_sum())
//		{
//			return t("Laekumisel juba piisava summa eest areveid");
//		}
		if($this->prop("currency") && $this->prop("currency") != $o->get_bill_currency_id())
		{
			return t("Laekumise valuuta erineb arve omast");
		}
		$ol = new object_list(array(
			"class_id" => CL_CRM_BILL,
			"CL_CRM_BILL.RELTYPE_PAYMENT.id" => $this->id()
		));
		$eb = $ol->begin();
		if(is_object($eb) && $eb->prop("customer") != $o->prop("customer"))
		{
			return t("laekumine ei saa olla erinevate klientidega arvetele");
		}
		if(!is_object($eb) || !$eb->prop("customer"))
		{
			$this->set_prop("customer" , $o->prop("customer"));
			$this->save();
		}

		//vigu pole, siis teeb 2ra
		$o->connect(array(
			"to" => $this->id(),
			"type" => "RELTYPE_PAYMENT"
		));

		if(!$this->prop("currency"))
		{
			$curr = $o->get_bill_currency_id();
			$this->set_prop("currency" , $curr);

			$ci = new currency();
			$rate = 1;
			if(($default_c = $ci->get_default_currency()) != $curr)
			{
				$rate = $ci->convert(array(
					"sum" => 1,
					"from" => $curr,
					"to" => $default_c,
					"date" => time(),
				));
			}
			$this -> set_prop("currency_rate", $rate);
			$this->save();
		}
		if($sum)
		{
			$this->set_bill_sum(array(
				"bill" => $o->id(),
				"sum" => $sum,
			));
		}

		return "";
	}

	function get_currency()
	{
		if($this->prop("currency"))
		{
			return obj($this->prop("currency"));
		}
		$ol = new object_list(array(
			"class_id" => CL_CRM_BILL,
			"CL_CRM_BILL.RELTYPE_PAYMENT.id" => $this->id(),
		));
		foreach($ol -> arr() as $o)
		{
			if($o->get_bill_currency_id())
			{
				$this->set_prop("currency" , $o->get_bill_currency_id());
				return obj($o->get_bill_currency_id());
			}
		}
		return "";
	}

	function get_currency_id()
	{
		if($this->prop("currency"))
		{
			return ($this->prop("currency"));
		}
		$co = $this->get_currency();
		if($co)
		{
			return $co->id();
		}
		return "";
	}

	function get_currency_name()
	{
		$co = $this->get_currency();
		if($co)
		{
			return $co->name();
		}
		return "";
	}

}

?>
