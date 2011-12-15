<?php

class shop_sell_order_obj extends _int_object
{
	const CLID = 1431;

	const STATUS_INPROGRESS = -1;
	const STATUS_CONFIRMED = 1;
	const STATUS_CANCELLED = 2;
	const STATUS_SENT = 3;
	const STATUS_CLOSED = 4;
	const STATUS_WORKING = 5;

	private $rows = array();
	private $rows_loaded = false;

	static $gcr_cache;

	public static function get_purchaser_data_by_ids($ids, $properties = array())
	{
		$odl = count($ids) > 0 ? new object_data_list(
			array(
				"class_id" => array(crm_person_obj::CLID, crm_company_obj::CLID),
				"oid" => $ids,
			),
			array(
				crm_person_obj::CLID => array("external_id", "firstname", "lastname", "birthday", "personal_id"),
				crm_company_obj::CLID => array("name"),
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
				"class_id" => shop_order_row_obj::CLID,
				"CL_SHOP_ORDER_ROW.RELTYPE_ROW(CL_SHOP_SELL_ORDER)" => $ids,
			),
			array(
				shop_order_row_obj::CLID => array("amount" , "price", "prod_name", "prod(CL_SHOP_PRODUCT_PACKAGING).size", "prod(CL_SHOP_PRODUCT_PACKAGING).product(CL_SHOP_PRODUCT).name", "prod(CL_SHOP_PRODUCT).name", "prod(CL_SHOP_PRODUCT_PACKAGING).product(CL_SHOP_PRODUCT).code", /* Now the worst part! -> */ "metadata"),
			)
		);
		//	This part could be optimized if CL_SHOP_ORDER_ROW knew which order it belongs to. -kaarel 8.04.2010
		//	----------	OPTIMIZEABLE PART OF CODE
		$conns = connection::find(array(
			"from.class_id" => self::CLID,
			"to.class_id" => shop_order_row_obj::CLID,
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
				"class_id" => shop_order_row_obj::CLID,
				"CL_SHOP_ORDER_ROW.RELTYPE_ROW(CL_SHOP_SELL_ORDER)" => $ids,
			),
			array(
				shop_order_row_obj::CLID => array("amount" , "price"),
			)
		);
		//	This part could be optimized if CL_SHOP_ORDER_ROW knew which order it belongs to. -kaarel 8.04.2010
		//	----------	OPTIMIZEABLE PART OF CODE
		$conns = connection::find(array(
			"from.class_id" => self::CLID,
			"to.class_id" => shop_order_row_obj::CLID,
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
				"class_id" => shop_order_row_obj::CLID,
				"CL_SHOP_ORDER_ROW.RELTYPE_ROW(CL_SHOP_SELL_ORDER)" => $id
			),
			array(
				shop_order_row_obj::CLID => array("amount" , "price"),
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

	public function save($check_state = false)
	{
		$r =  parent::save($check_state);

		foreach ($this->rows as $row)
		{
			if (!$row->is_saved())
			{				
				$row->set_prop("order", $this->id());
				$row->set_parent($this->id());
				$row->save();

				$this->connect(array(
					"to" => $row->id(),
					"type" => "RELTYPE_ROW"
				));
			}
		}

		return $r;
	}

	function prop($name)
	{
		switch($name)
		{
			case "shop_delivery_type":
				if(!parent::prop("shop_delivery_type") && $this->prop("transp_type.class_id") == shop_delivery_method_obj::CLID)
				{
					$name = "transp_type";
				}
			break;
		}


		return parent::prop($name);
	}


	/** adds new row
		@attrib api=1 params=pos
		@param data type=array
			@subparam price optional type=double
			@subparam product optional type=oid
			@subparam product_name optional type=string
			@subparam amount optional type=double
			@subparam code optional type=string
				product code
		@param use_existing optional type=boolean default=false
			If true, new row will be created even if an existing one already contains given item.
		@param save optional type=boolean default=true
			If true, row will be instantly saved and connected to order. Order has to be saved for this. If false, rows will be stored temporarily and will be saved and connected once the order is saved.
		@returns oid
			new row id
	**/
	public function add_row($data, $use_existing = false, $save = true)
	{
		if ($use_existing)
		{
			foreach ($this->get_rows() as $o)
			{
				if ($o->prop("prod") == $data["product"])
				{
					if (isset($data["amount"]))
					{
						$o->set_prop("items", $o->prop("items") + $data["amount"]);
					}
					if ($save)
					{
						$o->save();
					}
					return $o->id();
				}
			}
		}

		$o = obj(null, array(), shop_order_row_obj::CLID);
		$o->set_name($this->name()." ".t("rida"));
		$o->set_prop("prod", $data["product"]);
		if (empty($data["product_name"]))
		{
			$o->set_prop("prod_name", get_name($data["product"]));
		}
		if (empty($data["price"]))
		{
			$product = obj($data["product"]);
			// FIXME: Another place waiting for this stupid way of pricing to come to an end! I really ought to do something about it already!
			$price = $product->prop("special_price") ? $product->prop("special_price") : $product->prop("price");
			$o->set_prop("price", $price);
		}
		foreach (array("amount" => "items", "price" => "price", "code" => "other_code", "product_name" => "prod_name") as $key => $prop)
		{
			if (isset($data[$key]))
			{
				$o->set_prop($prop, $data[$key]);
			}
		}
		$o->set_prop("date", time());

		//	TODO: Should it NOT save by default? I need the option to not save the rows, because I don't want to save the order before I add rows. That is necessary to be able to create temporary objects. -kaarel 17.08.2011
		if ($save)
		{
			$o->set_prop("order", $this->id());
			$o->set_parent($this->id());
			$o->save();
			$this->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_ROW"
			));
		}
		$this->rows[] = $o;
		return $o;
	}

	public function remove_row($id, $delete = true)
	{
		if (isset($this->rows[$id]))
		{
			if ($delete)
			{
				$this->rows[$id]->delete();
			}
			unset($this->rows[$id]);
		}
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
					"class_id" => crm_company_customer_data_obj::CLID,
					"buyer" => $purchasers,
					"seller" => $my_co
				),
				array(
					crm_company_customer_data_obj::CLID => array("buyer"),
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
				$o = obj(null, array(), crm_company_customer_data_obj::CLID);
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
			"class_id" => crm_company_customer_data_obj::CLID,
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
			$o = obj(null, array(), crm_company_customer_data_obj::CLID);
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

	public function get_rows()
	{
		if (!$this->rows_loaded)
		{
			$this->__load_rows();
		}
		return $this->rows;
	}

	private function __load_rows()
	{
		$ol = new object_list(array(
			"class_id" => shop_order_row_obj::CLID,
			"CL_SHOP_ORDER_ROW.RELTYPE_ROW(CL_SHOP_SELL_ORDER).id" => $this->id()
		));
		$this->rows = $ol->arr();
		$this->rows_loaded = true;
	}

	/**	Returns the number of rows
	**/
	public function get_rows_count()
	{
		return count($this->get_rows());
	}
	
	/**	Returns the sum of prices of all rows. Delivery costs excluded!
	**/
	public function get_rows_total_price()
	{
		// TODO: This price should be stored not calculated every time!
		$total = 0;

		foreach ($this->get_rows() as $row)
		{
			$total += $row->prop("price") * $row->prop("items");
		}

		return $total;
	}
	
	/**	Returns the the object in JSON
		@attrib api=1
		TODO: Make the data returned configurable for the sake of efficiency!
	**/
	public function json()
	{
		$order_data = array();
		/*
			"rows" => array(
				"id",
				"item" => array(
					"id", "name"
				),
				"amount", "price",
			),
			"items" => array("id", "name", "amount", "price", "total"),
				"purveyances" => array(
					array(
						"company" => array("id", "name", "address"),
						"company_section" => array("id", "name", "address"),
						"weekdays", "time_from", "time_to", "days"
					)
				)
			),
			"purchaser" => array(
				"id", "name",
				"customers" => array(
					"id", "name"
				),
			),
		);
		*/
		
		$purchaser = obj($this->prop("purchaser"));
		if (!$purchaser->is_saved())
		{
			// FIXME: Only temporarily!
			$__purchaser_id = users::is_logged_in() ? user::get_current_person() : null;
			$purchaser = obj($__purchaser_id, array(), crm_person_obj::CLID);
		}
		$order_data["purchaser"] = array(
			"id" => $purchaser->id(),
			"name" => $purchaser->name(),
			"customers" => array(),
		);

		if ($purchaser->is_a(crm_person_obj::CLID) and $purchaser->is_saved())
		{
			$customers = $purchaser->company()->get_customers_by_customer_data_objs(crm_person_obj::CLID);
			$order_data["purchaser"]["company"] = $purchaser->company()->id();
			
		}
		elseif ($purchaser->is_a(crm_company_obj::CLID) and $purchaser->is_saved())
		{
			$customers = $purchaser->get_customers_by_customer_data_objs(crm_person_obj::CLID);
		}
		$customer_count = isset($customers) ? $customers->count() : 0;
		if ($customer_count > 0)
		{
			foreach ($customers->names() as $customer_id => $customer_name)
			{
				$order_data["purchaser"]["customers"][$customer_id] = array(
					"id" => $customer_id,
					"name" => $customer_name,
				);
			}
		}

		$order_data["rows"] = array();
		$order_data["items"] = array();
		foreach ($this->get_rows() as $row)
		{
			$item_data = array(
				"id" => $row->prop("prod"),
				"name" => $row->prop("prod_name"),
			);
			$order_data["rows"][$row->id()] = array(
				"id" => $row->id(),
				"item" => $item_data,
				"amount" => $row->prop("items"),
				"price" => $row->prop("price"),
				"buyer_rep" => $row->prop("buyer_rep"),
				"purveyance_company_section" => $row->prop("buyer_rep"),
				"planned_date" => $row->prop("planned_date"),
				"planned_time" => $row->prop("planned_time"),
			);

			if (!isset($order_data["items"][$item_data["id"]]))
			{
				$item = obj($item_data["id"]);
				$purveyance_data = array();
				$purveyances = $item->get_purveyances();
				if ($purveyances->count() > 0)
				{
					$purveyance = $purveyances->begin();
					do
					{
						$purveyance_data[$purveyance->id()] = array("company" => array());
						if (is_oid($company_id = $purveyance->prop("company")))
						{
							$company = obj($company_id, array(), crm_company_obj::CLID);
							$purveyance_data[$purveyance->id()]["company"] = array(
								"id" => $company->id(),
								"name" => $company->get_title(),
								"address" => $company->get_address_string(),							
								"section" => array(),
							);
							
							if (is_oid($section_id = $purveyance->prop("company_section")))
							{
								$section = obj($section_id, array(), crm_section_obj::CLID);
								$purveyance_data[$purveyance->id()]["company"]["section"] = array(
									"id" => $section->id(),
									"name" => $section->name(),
//									"address" => $section->get_address_string(),
								);
							}
						}
						$purveyance_data[$purveyance->id()] += array(
							"weekdays" => $purveyance->prop("weekdays"),
							"time_from" => $purveyance->prop("time_from"),
							"time_to" => $purveyance->prop("time_to"),
							"days" => $purveyance->prop("days")
						);
					} while ($purveyance = $purveyances->next());
				}
				$order_data["items"][$item_data["id"]] = $item_data + array(
					"price" => $row->prop("price"),
					"amount" => $row->prop("items"),
					"total" => $row->prop("price") * $row->prop("items"),
					"purveyances" => $purveyance_data,
				);
			}
			else
			{
				$order_data["items"][$item_data["id"]]["amount"] += $row->prop("items");
				$order_data["items"][$item_data["id"]]["total"] += $row->prop("price") * $row->prop("items");
			}
		}

		$json = new json();
		return $json->encode($order_data, aw_global_get("charset"));
	}
}
