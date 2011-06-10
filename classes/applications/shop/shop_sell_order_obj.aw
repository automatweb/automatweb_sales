<?php

class shop_sell_order_obj extends _int_object
{
	const CLID = 1431;

	static $gcr_cache;

	public static function get_purchaser_data_by_ids($ids, $properties = array())
	{
		$odl = count($ids) > 0 ? new object_data_list(
			array(
				"class_id" => array(CL_CRM_PERSON, CL_CRM_COMPANY),
				"oid" => $ids,
			),
			array(
				CL_CRM_PERSON => array("external_id", "firstname", "lastname", "birthday", "personal_id"),
				CL_CRM_COMPANY => array("name"),
			)
		) : new object_data_list();
		return $odl;
	}

	public static function get_rows_by_ids($ids)
	{
		if(count($ids) === 0)
		{
			return array();
		}

		$rows = array();
		//	Initialize
		foreach($ids as $id)
		{
			$sums[$id] = array();
		}

		$odl = new object_data_list(
			array(
				"class_id" => CL_SHOP_ORDER_ROW,
				"lang_id" => array(),
				"site_id" => array(),
				"CL_SHOP_ORDER_ROW.RELTYPE_ROW(CL_SHOP_SELL_ORDER)" => $ids,
			),
			array(
				CL_SHOP_ORDER_ROW => array("amount" , "price", "prod_name", "prod(CL_SHOP_PRODUCT_PACKAGING).size", "prod(CL_SHOP_PRODUCT_PACKAGING).product(CL_SHOP_PRODUCT).name", "prod(CL_SHOP_PRODUCT).name", "prod(CL_SHOP_PRODUCT_PACKAGING).product(CL_SHOP_PRODUCT).code", /* Now the worst part! -> */ "metadata"),
			)
		);
		//	This part could be optimized if CL_SHOP_ORDER_ROW knew which order it belongs to. -kaarel 8.04.2010
		//	----------	OPTIMIZEABLE PART OF CODE
		$conns = connection::find(array(
			"from.class_id" => CL_SHOP_SELL_ORDER,
			"to.class_id" => CL_SHOP_ORDER_ROW,
			"from" => $ids,
			"to" => $odl->ids(),
			"reltype" => "RELTYPE_ROW",
		));
		//	Assume every order_row has exactly one order
		$order_by_order_row = array();
		foreach($conns as $conn)
		{
			$order_by_order_row[$conn["to"]] = $conn["from"];
		}
		//	----------	END OF OPTIMIZEABLE PART OF CODE

		foreach($odl->arr() as $oid => $odata)
		{
			$meta = aw_unserialize($odata["metadata"]);
			unset($odata["metadata"]);
			foreach($meta as $meta_key => $meta_value)
			{
				$odata["meta"][$meta_key] = trim($meta_value);
			}
			$rows[$order_by_order_row[$oid]][] = $odata;
		}
		return $rows;
	}

	public static function get_sums_by_ids($ids)
	{
		if(count($ids) === 0)
		{
			return array();
		}

		$sums = array();
		//	Initialize
		foreach($ids as $id)
		{
			$sums[$id] = 0;
		}

		$odl = new object_data_list(
			array(
				"class_id" => CL_SHOP_ORDER_ROW,
				"lang_id" => array(),
				"site_id" => array(),
				"CL_SHOP_ORDER_ROW.RELTYPE_ROW(CL_SHOP_SELL_ORDER)" => $ids,
			),
			array(
				CL_SHOP_ORDER_ROW => array("amount" , "price"),
			)
		);
		//	This part could be optimized if CL_SHOP_ORDER_ROW knew which order it belongs to. -kaarel 8.04.2010
		//	----------	OPTIMIZEABLE PART OF CODE
		$conns = connection::find(array(
			"from.class_id" => CL_SHOP_SELL_ORDER,
			"to.class_id" => CL_SHOP_ORDER_ROW,
			"from" => $ids,
			"to" => $odl->ids(),
			"reltype" => "RELTYPE_ROW",
		));
		//	Assume every order_row has exactly one order
		$order_by_order_row = array();
		foreach($conns as $conn)
		{
			$order_by_order_row[$conn["to"]] = $conn["from"];
		}
		//	----------	END OF OPTIMIZEABLE PART OF CODE

		foreach($odl->arr() as $oid => $odata)
		{
			$sums[$order_by_order_row[$oid]] += $odata["amount"] * $odata["price"];
		}
		return $sums;
	}

	/** returns order price
		@attrib api=1 params=pos
		@param id required type=int acl=view
		@returns double
			order rows price sum
	**/
	public static function get_sum_by_id($id)
	{
		$sum = 0;
		$odl = new object_data_list(
			array(
				"class_id" => CL_SHOP_ORDER_ROW,
				"lang_id" => array(),
				"site_id" => array(),
				"CL_SHOP_ORDER_ROW.RELTYPE_ROW(CL_SHOP_SELL_ORDER)" => $id
			),
			array(
				CL_SHOP_ORDER_ROW => array("amount" , "price"),
			)
		);

		foreach($odl->arr() as $od)
		{
			$sum += $od["amount"] * $od["price"];
		}
		return $sum;
	}

	/** returns order price
		@attrib api=1
		@returns double
			order rows price sum
	**/
	public function get_sum()
	{
		return self::get_sum_by_id($this->id());
	}

	/** returns all product names
		@attrib api=1
		@returns array
	**/
	public function get_product_names()
	{
		$ret = array();
		$sum = 0;
		foreach($this->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$row = $c->to();
			$ret[] = $row->prod_name();
		}
		return $ret;
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

	/**
		@attrib name=bank_return nologin=1
	**/
	function bank_return($arr)
	{
		if($this->meta("lang_id"))
		{
			$_SESSION["ct_lang_id"] = $this->meta("lang_id");
			$_SESSION["ct_lang_lc"] = $this->meta("lang_lc");
			aw_global_set("ct_lang_lc", $_SESSION["ct_lang_lc"]);
			aw_global_set("ct_lang_id", $_SESSION["ct_lang_id"]);
		}
		$this->set_prop("order_status" , "0");
		aw_disable_acl();
		$this->save();
		aw_restore_acl();

		$order_data = $this->meta("order_data");

		$order_center = obj($order_data["oc"]);

		// send mail
		if(!$this->meta("mail_sent"))
		{
			$this->set_meta("mail_sent" , 1);
			$order_center->send_confirm_mail($this->id());
			aw_disable_acl();
			$this->save();
			aw_restore_acl();

		}

		if(is_oid($this->meta("bank_payment_id")))
		{
			$p = obj($this->meta("bank_payment_id"));
//			if(!empty($p->prop("bank_return_url")))
//			{
//				return $p->prop("bank_return_url");
//			}
		}
		return $this->mk_my_orb("show", array("id" => $this->id()), "shop_order");
	}

	/** returns array of customer relation IDs indexed by purchaser ID
		@attrib api=1 params=pos
		@param purchaser required type=int[] acl=view
		@param my_co optional
		@param crea_if_not_exists optional
			if no customer relation object, makes one
		@returns object
	**/
	public static function get_customer_relation_ids_for_purchasers($purchasers, $my_co = null, $crea_if_not_exists = false)
	{
		enter_function("shop_sell_order_obj::get_customer_relation_ids_for_purchasers");
		$customer_relation_ids = array();
		if ($my_co === null)
		{
			$my_co = get_current_company();
		}

		if (!is_object($my_co) || !is_oid($my_co->id()))
		{
			exit_function("shop_sell_order_obj::get_customer_relation_ids_for_purchasers");
			return $customer_relation_ids;
		}

		if (!isset(self::$gcr_cache) || !is_array(self::$gcr_cache))
		{
			self::$gcr_cache = array();
		}
		else
		{
			foreach($purchasers as $i => $purchaser)
			{				
				if (isset(self::$gcr_cache[$purchaser][$crea_if_not_exists][$my_co->id()]))
				{
					$customer_relation_ids[$purchaser] = self::$gcr_cache[$purchaser][$crea_if_not_exists][$my_co->id()];
					unset($purchasers[$i]);
				}
			}
		}

		$purchasers_without_customer_relations = array_flip($purchasers);
		if(count($purchasers) > 0)
		{
			$odl = new object_data_list(
				array(
					"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
					"buyer" => $purchasers,
					"seller" => $my_co
				),
				array(
					CL_CRM_COMPANY_CUSTOMER_DATA => array("buyer"),
				)
			);

			foreach($odl->arr() as $oid => $odata)
			{
				self::$gcr_cache[$odata["buyer"]][$crea_if_not_exists][$my_co->id()] = $customer_relation_ids[$odata["buyer"]] = $oid;
				unset($purchasers_without_customer_relations[$odata["buyer"]]);
			}
		}

		if($crea_if_not_exists)
		{
			foreach(array_flip($purchasers_without_customer_relations) as $purchaser)
			{
				$my_co = obj($my_co);
				$o = obj();
				$o->set_class_id(CL_CRM_COMPANY_CUSTOMER_DATA);
				$o->set_name(t("Kliendisuhe ") . $my_co->name() . " => " . obj($purchaser)->prop("name"));
				$o->set_parent($my_co->id());
				$o->set_prop("seller", $my_co->id());
				$o->set_prop("buyer", $purchaser);
				self::$gcr_cache[$purchaser][$crea_if_not_exists][$my_co->id()] = $customer_relation_ids[$purchaser] = $o->save();
			}
		}

		exit_function("shop_sell_order_obj::get_customer_relation_ids_for_purchasers");
		return $customer_relation_ids;
	}

	/** returns customer relation object
		@attrib api=1 params=pos
		@param purchaser required type=int acl=view
		@param my_co optional
		@param crea_if_not_exists optional
			if no customer relation object, makes one
		@returns object
	**/
	public static function get_customer_relation_for_purchaser($purchaser, $my_co = null, $crea_if_not_exists = false)
	{
		if ($my_co === null)
		{
			$my_co = get_current_company();
		}

		if (!is_object($my_co) || !is_oid($my_co->id()) || !is_oid($purchaser))
		{
			return;
		}

		if (!isset(self::$gcr_cache) || !is_array(self::$gcr_cache))
		{
			self::$gcr_cache = array();
		}
		if (isset(self::$gcr_cache[$purchaser][$crea_if_not_exists][$my_co->id()]))
		{
			return self::$gcr_cache[$purchaser][$crea_if_not_exists][$my_co->id()];
		}

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"buyer" => $purchaser,
			"seller" => $my_co
		));
		if ($ol->count())
		{
			self::$gcr_cache[$purchaser][$crea_if_not_exists][$my_co->id()] = $ol->begin();
			return $ol->begin();
		}
		elseif ($crea_if_not_exists)
		{
			$my_co = obj($my_co);
			$o = obj();
			$o->set_class_id(CL_CRM_COMPANY_CUSTOMER_DATA);
			$o->set_name(t("Kliendisuhe ") . $my_co->name() . " => " . obj($purchaser)->prop("name"));
			$o->set_parent($my_co->id());
			$o->set_prop("seller", $my_co->id());
			$o->set_prop("buyer", $purchaser);
			$o->save();
			self::$gcr_cache[$purchaser][$crea_if_not_exists][$my_co->id()] = $o;
			return $o;
		}
	}

	/** returns customer relation object
		@attrib api=1 params=pos
		@param my_co optional
		@param crea_if_not_exists optional
			if no customer relation object, makes one
		@returns object
	**/
	public function get_customer_relation($my_co = null, $crea_if_not_exists = false)
	{
		return self::get_customer_relation_for_purchaser($this->prop("purchaser"), $my_co = null, $crea_if_not_exists = false);
	}



}

?>
