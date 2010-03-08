<?php

class shop_warehouse_obj extends _int_object
{
	const STATCALC_FIFO = 1;
	const STATCALC_WEIGHTED_AVERAGE = 2;

	function get_status_calc_options()
	{
		return array(
			self::STATCALC_FIFO => t("FIFO"),
			self::STATCALC_WEIGHTED_AVERAGE => t("Kaalutud keskmine")
		);
	}

	/** Gets warehouse product movements
		@attrib api=1 params=name
		@param category optional type=oid
			Product category id
		@param from optional type=int
			timestamp
		@param to optional type=int
			timestamp
		@param after_time optional type=int
		@returns object list
	**/
	public function get_movements($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_SHOP_WAREHOUSE_MOVEMENT;
		$filter["lang_id"] = array();
		$filter["site_id"] = array();

		if(is_oid($arr["category"]))
		{
			$ot = new object_tree(array(
				"parent" => $arr["category"],
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"sort_by" => "objects.jrk"
			));
			$cat_ids = $ot->ids();
			if(is_array($cat_ids) && sizeof($cat_ids))
			{
				$cat_ids[] = $arr["category"];
				$arr["category"] = $cat_ids;
			}
			$filter["product.RELTYPE_CATEGORY"] = $arr["category"];
		}
		
		if($arr["after_time"])
		{
			$arr["from"] = $arr["to"]+1;
			$arr["to"] = time()*2;
		}

		if($arr["from"] && $arr["to"])
		{
			$filter["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $arr["from"], $arr["to"]);
		}
		else
		{
			if($arr["from"])
			{
				$filter["date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $arr["from"]);
			}
			if($arr["to"])
			{
				$filter["date"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $arr["to"]);

			}
		}
;
		return new object_list($filter);
	}

	/** Gets warehouse delivery notes
		@attrib api=1 params=name
		@returns object list
	**/
	public function get_delivery_note_rows($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_SHOP_DELIVERY_NOTE;
		$filter["lang_id"] = array();
		$filter["site_id"] = array();
/*
		if(is_oid($arr["category"]))
		{
			$ot = new object_tree(array(
				"parent" => $arr["category"],
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"sort_by" => "objects.jrk"
			));
			$cat_ids = $ot->ids();
			if(is_array($cat_ids) && sizeof($cat_ids))
			{
				$cat_ids[] = $arr["category"];
				$arr["category"] = $cat_ids;
			}
			$filter["product.RELTYPE_CATEGORY"] = $arr["category"];
		}

		if($arr["from"] && $arr["to"])
		{
			$filter["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $arr["from"], $arr["to"]);
		}
		else
		{
			if($arr["from"])
			{
				$filter["date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $arr["from"]);
			}
			if($arr["to"])
			{
				$filter["date"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $arr["to"]);

			}
		}*/

		return new object_list($filter);
	}

	/** Gets all warehouse products
		@attrib api=1 params=name
		@param category optional type=oid
			Product category id
		@returns object list
	**/
	public function get_products($arr)
	{
		$arr["recursive"] = 1;
		return $this->_get_products($arr);
	}

	private function _get_products($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_SHOP_PRODUCT;
		$filter["lang_id"] = array();
		$filter["site_id"] = array();

		if(isset($arr["category"]))
		{
			if(is_oid($arr["category"]))
			{
				if($arr["recursive"])
				{
					$ot = new object_tree(array(
						"parent" => $arr["category"],
						"class_id" => CL_SHOP_PRODUCT_CATEGORY,
						"sort_by" => "objects.jrk"
					));
					$cat_ids = $ot->ids();
					if(is_array($cat_ids) && sizeof($cat_ids))
					{
						$cat_ids[] = $arr["category"];
						$arr["category"] = $cat_ids;
					}
				}
				$filter["CL_SHOP_PRODUCT.RELTYPE_CATEGORY"] = $arr["category"];
			}
			elseif(is_array($arr["category"]) && sizeof($arr["category"]))
			{
				if($arr["cat_condition"] == "and")
				{//----------------------------------------------------
					$filter["CL_SHOP_PRODUCT.RELTYPE_CATEGORY"] = $arr["category"];
				}
				else
				{
					$filter["CL_SHOP_PRODUCT.RELTYPE_CATEGORY"] = $arr["category"];
				}
			}
		}
		if(isset($arr["name"]))
		{
			$filter["name"] = "%".$arr["name"]."%";
		}

		if(isset($arr["parent"]))
		{
			$filter["parent"] = $arr["parent"];
		}

		if(isset($arr["code"]))
		{
			$filter["code"] = $arr["code"]."%";
		}

		return new object_list($filter);
	}

//3 viimast vaja alles t88le panna
	/** Searches warehouse products
		@attrib api=1 params=name
		@param category optional type=oid
			Product category id
		@param name optional type=string
			Product name
		@param parent optional type=oid/array
			Product name
		@param code optional type=string
			Product code
		@param barcode optional type=string//
			Product barcode
		@param price_from optional type=double//
			Minimum product price
		@param price_to optional type=double//
			Maximum product price
		@returns object list
	**/
	public function search_products($arr = array())
	{
		$arr["cat_condition"] = "and";
		return $this->_get_products($arr);
	}

	/** Gets all warehouse product packagings
		@attrib api=1 params=name
		@param category optional type=oid
			Product category id
		@returns object list
	**/
	public function get_packagings($arr)
	{
		$filter = array();
		$filter["class_id"] = CL_SHOP_PRODUCT_PACKAGING;
		$filter["lang_id"] = array();
		$filter["site_id"] = array();

		if(is_oid($arr["category"]))
		{
			$ot = new object_tree(array(
				"parent" => $arr["category"],
				"class_id" => CL_SHOP_PRODUCT_CATEGORY,
				"sort_by" => "objects.jrk"
			));
			$cat_ids = $ot->ids();
			if(is_array($cat_ids) && sizeof($cat_ids))
			{
				$cat_ids[] = $arr["category"];
				$arr["category"] = $cat_ids;
			}
			$filter["CL_SHOP_PRODUCT.RELTYPE_CATEGORY"] = $arr["category"];
		}
		return new object_list($filter);
	}

	function get_inventories($arr)
	{
		extract($arr);
		if(!$arr["warehouses"])
		{
			$arr["warehouses"] = array($this->id());
		}
		$params = array(
			"class_id" => CL_SHOP_WAREHOUSE_INVENTORY,
			"lang_id" => array(),
			"site_id" => array(),
			"warehouse" => $arr["warehouses"],
		);
//		$group = $this->get_search_group($arr);
//		if($n = $arr["request"][$group."_s_name"])
//		{
//			$params["name"] = "%".$n."%";
//		}
//		if($s = $arr["request"][$group."_s_status"])
//		{
//			if($s == STORAGE_FILTER_CONFIRMED)
//			{
//				$params["confirmed"] = 1;
//			}
//			elseif($s == STORAGE_FILTER_UNCONFIRMED)
//			{
//				$params["confirmed"] = new obj_predicate_not(1);
//			}
//		}
//		$from = date_edit::get_timestamp($arr["request"][$group."_s_from"]);
//		$to = date_edit::get_timestamp($arr["request"][$group."_s_to"]);
		if($from > 0 && $to > 0)
		{
			$to += 24 * 60 * 60 -1;
			$params["date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $from, $to);
		}
		elseif($from > 0)
		{
			$params["date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
		}
		elseif($to > 0)
		{
			$to += 24 * 60 * 60 -1;
			$params["date"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
		}
		$ol = new object_list($params);
		return $ol;
	}

	/** get product's warehouse amounts
		@attrib api=1
		@param prod optional type=int
		@param single optional type=int
		@param unit optional type=int
		@param singlecode optional type=int
		@returns object list of shop_warehouse_amount objects
	**/
	function get_amounts($arr)
	{
		if(isset($arr["prod"]))
		{
			$params["product"] = $arr["prod"];
		}
		if(isset($arr["single"]))
		{
			$params["single"] = $arr["single"];
		}
		elseif($this->can("view", $arr["prod"]))
		{
			$po = obj($arr["prod"]);
			if(!$po->prop("serial_number_based") && !$po->prop("order_based"))
			{
				$params["single"] = null;
			}
		}
		if(count($params))
		{
			if($arr["unit"])
			{
				$params["unit"] = $arr["unit"];
			}
			$params["warehouse"] = $this->id();
			if($arr["singlecode"])
			{
				$params["CL_SHOP_WAREHOUSE_AMOUNT.single.code"] = $arr["singlecode"];
			}
			$params["class_id"] = CL_SHOP_WAREHOUSE_AMOUNT;
			$params["lang_id"] = array();
			$params["site_id"] = array();
			$ol = new object_list($params);
			return $ol;
		}
		return false;
	}

	/** get product's warehouse amount
		@attrib api=1
		@param prod optional type=int
		@param single optional type=int
		@param unit optional type=int
		@param singlecode optional type=int
		@returns double
	**/
	function get_amount($arr)
	{
		$amounts = $this->get_amounts(array(
			"prod" => $arr["prod"],
		));
		$count = 0;
		if($amounts->count())
		{
			$amount = reset($amounts->arr());
			$count = $amount->prop("amount");
		}
		return $count;
	}

	/**returns all warehouse product category types
		@attrib api=1
		@returns object list
	**/
	public function get_product_category_types()
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY_TYPE,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "jrk asc, name asc",
		));
		return $ol;
	}

	/**adds new product to warehouse
		@attrib api=1
		@param name required type=string
		@param parent optional type=int
		@param category optional type=oid/array
		@returns oid
			new object id
	**/
	public function new_product($arr)
	{
		$o = new object();
		$parent = $this->id();
		if(isset($arr["parent"]) && is_oid($arr["parent"]))
		{
			$parent = $arr["parent"];
		}
		elseif($this->get_conf("prod_fld"))
		{
			$parent = $this->get_conf("prod_fld");
		}
		$o->set_class_id(CL_SHOP_PRODUCT);
		$o->set_parent($parent);
		$o->set_name($arr["name"]);
		$o->save();
		if(isset($arr["category"]))
		{
			if(is_oid($arr["category"]))
			{
				$arr["category"] = array($arr["category"]);
			}
			if(is_array($arr["category"]))
			{
				foreach($arr["category"] as $cat)
				{
					$o->add_category($cat);
				}
			}
		}
		return $o->id();
	}

	/**return all brands
		@attrib api=1
		@returns object_list
	**/
	public function get_brands()
	{
		$ol = new object_list(array("class_id" => CL_SHOP_BRAND));
		return $ol;
	}

	/**return all channels
		@attrib api=1
		@returns object_list
	**/
	public function get_channels()
	{
		$ol = new object_list(array("class_id" => CL_WAREHOUSE_SELL_CHANNEL));
		return $ol;
	}

	/** returns all mail addresses
		@attrib api=1
		@returns array
			mail addresses array
	**/
	public function get_order_mails()
	{
		$ret = array();
		foreach($this->connections_from(array("type" => "RELTYPE_EMAIL")) as $con)
		{
			$eml = $con->to();
			$ret[$eml->prop("mail")] = $eml->prop("mail");
		};
		return $ret;
	}

	/** Returns a list of packets/products in the warehouse $id, optionally under folder $parent
		@attrib param=name api=1
		@param parent optional type=var
			Parent folder id or array of parent folders
		@param only_active optional type=bool
			To get only active packets/products
		@param no_subitems optional type=bool
			If true, sub-products are not requested
		@returns Array of packet/product objects
	**/
	function get_packet_list($arr = array())
	{
		enter_function("shop_warehouse::get_packet_list");

		$conf = obj($this->prop("conf"));

		$status = array(STAT_ACTIVE, STAT_NOTACTIVE);
		if (!empty($arr["only_active"]))
		{
			$status = STAT_ACTIVE;
		}

		$ret = new object_list();

		if($conf->prop("no_packets") != 1 && !(isset($arr['parent']) &&  is_array($arr['parent'])))
		{
			$po = obj((!empty($arr["parent"]) ? $arr["parent"] : $conf->prop("pkt_fld")));
			if ($po->is_brother())
			{
				$po = $po->get_original();
			}

			$ol = new object_list(array(
				"parent" => $po->id(),
				"class_id" => CL_SHOP_PACKET,
				"status" => $status
			));
			$ret = $ol;
		}

		if (isset($arr['parent']) && is_array($arr['parent']))
		{
			$parent = $arr['parent'];
		}
		else
		{
			$po = obj((!empty($arr["parent"]) ? $arr["parent"] : $conf->prop("prod_fld")));
			if ($po->is_brother())
			{
				$po = $po->get_original();
			}
			$parent = $po->id();
		}

		enter_function("warehouse::object_list");
		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => CL_SHOP_PRODUCT,
			"status" => $status
		));
		$ret->add($ol);
		exit_function("warehouse::object_list");
		if(!$conf->prop("sell_prods") && empty($arr["no_subitems"]))
		{
			//seda peaks parandama
			// now, let the classes add sub-items to the list
//			$tmp = array();
//			foreach($ret as $o)
//			{
//				$inst = $o->instance();
//				foreach($inst->get_contained_products($o) as $co)
//				{
//					$tmp[] = $co;
//				}
//			}
//			$ret = $tmp;
		}
		exit_function("shop_warehouse::get_packet_list");
		return $ret;
	}

	private function set_config_object()
	{
		if(!isset($this->config_object))
		{
			$this->config_object = obj($this->prop("conf"));
		}
	}

	/** Returns config object property value
		@attrib param=pos api=1
		@param property required type=string
			conf object property name
	**/
	public function get_conf($prop)
	{
		$this->set_config_object();
		return $this->config_object->prop($prop);
	}

	public function get_root_categories()
	{
		$prod_folder = $this->get_conf("prod_cat_fld");

		$cats = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"parent" => $prod_folder,
			"lang_id" => array(),
			"site_id" => array(),
		));

		return $cats;

	}

}