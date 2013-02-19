<?php

class shop_product_obj extends shop_warehouse_item_obj implements price_component_interface, crm_offer_row_interface
{
	const CLID = 295;

	/** Get units that can be used to measure product quantity
		@attrib api=1
		@returns object_list
	**/
	public function get_units()
	{
		$unit_oids =  $this->_get_units();

		if (count($unit_oids))
		{
			$ol = new object_list(array(
				"class_id" => CL_UNIT,
				"oid" => $unit_oids,
			));
		}
		else
		{
			$ol = new object_list();
		}
		return $ol;
	}

	function delete($full_delete = false)
	{
		$this->delete_product_show_cache();

		return parent::delete($full_delete);
	}

	public function save($check_state = false)
	{
		$this->delete_product_show_cache();

		return parent::save($check_state);
	}

	/** Sets the price for the product by currency
		@attrib api=1 params=pos

		@param currency required type=cl_currency
			The currency object to set the price for

		@param price required type=double
			The price

		@comment
			You will need to save the object yourself after this
	**/
	public function price_set_by_currency(object $currency, $price)
	{
		$curp = safe_array($this->meta("cur_prices"));
		$curp[$currency->id()] = (double)$price;
		$this->set_meta("cur_prices", $curp);
	}

	/** Returns the price as a double for the given currency
		@attrib api=1 params=pos

		@param currency required type=cl_currency
			The currency object to return the price for

		@returns
			the price of the product for the given currency
	**/
	public function price_get_by_currency($currency)
	{
		$curp = safe_array($this->meta("cur_prices"));
		return (double)$curp[$currency->id()];
	}

	/** Returns the price as a double for shop or warehouse currency
		@attrib api=1
		@returns double
			the price of the product
	**/
	public function get_shop_price($shop = null, $currency = null)
	{
		enter_function("shop_product_obj::get_shop_price");
		$price_by_currency = $this->get_price(array(
			"shop" => $shop
		));
		if(is_oid($shop) && $currency === NULL)
		{
			$currency = obj($shop)->default_currency;
		}
		if(!empty($price_by_currency) && !empty($price_by_currency[$currency]))
		{
			$retval = (float)$price_by_currency[$currency];
		}
		elseif($this->prop("price"))
		{
			$retval = (float)$this->get_price_value();
		}
		else
		{
			$retval = 0;
		}
		exit_function("shop_product_obj::get_shop_price");
		return $retval;
	}

//whata??????
	function get_shop_special_price()
	{
		return $this->get_special_price_value();
	}

	function get_special_price_value()
	{
		return 0;
	}

	/** edaspidi kasutaks vaid seda, et saaks igale ajahetkele erinevaid hindu panna ja loodetavasti ka hinnaobjektiga mitte metast
		@attrib name=get_price api=1
		@param shop optional type
			The OID of the shop_order_center object the prices are asked for. If not given, no price list will be applied!
		@param product_category optional type=array/int acl=view
			OIDs of product categories
		@param amount optional type=float default=1
			The amount of the product prices are asked for
		@param uid optional type=oid
			user id
		@param time optional type=int
			timestamp - order created
		@param from optional type=int
			timestamp - order start time
		@param customer_category optional type=array/int acl=view
			OIDs of customer categories.
		@param customer_data optional type=int acl=view
			OID of customer_data object
		@param location optional type=array/int acl=view
			OIDs of locations
		@param ocn optional type=int acl=view
			Order center oid
		@param structure optional type=bool default=false
			If set, the structure of the prices will be returned, otherwise only the final prices will be returned
	**/
	function get_price($arr)
	{
		$prices = safe_array($this->meta("cur_prices"));
		if(isset($arr["shop"]) && is_oid($arr["shop"]))// $this->can() IS NOT VALID! && $this->can("view", $arr["shop"]))
		{
			$shop = obj($arr["shop"]);
			// If no prices are set for any currencies, we presume the price property is set for default currency
			if(is_oid($shop->default_currency) && $this->prop("price"))
			{
				$no_price_set_for_any_currency = true;
				foreach($prices as $currency => $price)
				{
					if(strlen(trim($price)))
					{
						$no_price_set_for_any_currency = false;
						break;
					}
				}
				if($no_price_set_for_any_currency)
				{
					$prices[$shop->default_currency] = $this->prop("price");
				}
			}
			return shop_price_list_obj::price(array(
				"shop" => $arr["shop"],
				"product" => $this->id(),
				"product_packet" => $packet = $this->get_packets(),
				"product_category" => array_merge($this->get_categories(), !empty($packet) ? shop_packet_obj::get_categories_for_id($packet)->ids() : array()),
				"amount" => isset($arr["amount"]) ? $arr["amount"] : 1,
				"prices" => $prices,
				//	Need tuleb e-poe k2est kysida, kui ette ei anta (ja yldiselt ei anta)
				"customer_category" => isset($arr["customer_category"]) ? $arr["customer_category"] : array(),
				"customer_data" => isset($arr["customer_data"]) ? $arr["customer_data"] : array(),
				//	Kliendi juurest, kui ette ei anta? (yldiselt ei anta)
				"location" => isset($arr["location"]) ? $arr["location"] : array(),
				"timespan" => array(
					"start" => isset($arr["from"]) ? $arr["from"] : (isset($arr["time"]) ? $arr["time"] : time()),
					"end" => isset($arr["time"]) ? $arr["time"] : time(),
				),
				"structure" => !empty($arr["structure"]),
			));
		}
		else
		{
			return $prices;
		}
	}

	/** This should probably be something similar to the same function in shop_product_packaging_obj class, but there are much more different kind of prices in shop_product class so I just leave it be for now until there is a concrete need to start using the price objects.

		@attrib name=get_price_value api=1

		@returns Price property value as float.
	**/
	public function get_price_value()
	{
		return (float)$this->prop('price');
	}

	function set_prop($k, $v)
	{
		if($k == "price" || $k == "purchase_price")
		{
			$tzeros = strrpos($v, ".") !== false ? strlen($v) - strrpos($v, ".") - 1 : 0;
			parent::set_meta($k."_trailing_zeros", $tzeros);
		}
		return parent::set_prop($k, $v);
	}

	function prop($k)
	{
		if($k == "price" || $k == "purchase_price")
		{
			if(parent::meta($k."_trailing_zeros") > 0)
			{
				return sprintf("%.".parent::meta($k."_trailing_zeros")."f", parent::prop($k));
			}
		}
		return parent::prop($k);
 	}

	protected function _get_units()
	{
		if($meta_units = $this->meta("units"))
		{
			$units = $meta_units;
		}
		else
		{
			$cato = false;
			if($dc = $this->meta("def_cat"))
			{
				$cato = obj($dc);
			}
			elseif($dco = $this->get_first_obj_by_reltype("RELTYPE_CATEGORY"))
			{
				$cato = $dco;
			}
			if($cato && $meta_units = $cato->meta("units"))
			{
				$units = $meta_units;
			}
		}

		if(empty($units))
		{
			$units = array();
		}

		return $units;
	}

	/**
		@attrib name=get_discount api=1

		@param oid optional type=oid
		@param group optional type=oid
		@param uid optional type=oid
			user id
		@param crm_category optional type=oid
		@param org optional type=oid
		@param person optional type=oid
		@param warehouse optional type=oid
		@param prod_category optional type=oid
		@param time optional type=int
			timestamp - order made or now
		@param from optional type=int
			timestamp - order start time
	**/
	public function get_discount($oid = null, $params)
	{
		extract($params);
		if(!$oid)
		{
			$params = array(
				"class_id" => CL_SHOP_PRICE_LIST,
				"site_id" => array(),
				"lang_id" => array(),
				"sort_by" => "jrk asc",
			);
			foreach(array("group", "org", "crm_categories" => "crm_category", "person", "warehouse") as $var1 => $var2)
			{
				if($$var2)
				{
					$params[is_string($var1) ? $var1 : $var2."s"] = $$var2;
				}
			}
			$ol = new object_list($params);
			$o = $ol->begin();
		}
		elseif($this->can("view", $oid))
		{
			$o = obj($oid);
		}
		if($o)
		{
			if($prod_category && $crm_category)
			{
				$ol = new object_list(array(
					"class_id" => CL_SHOP_PRICE_LIST_CUSTOMER_DISCOUNT,
					"site_id" => array(),
					"lang_id" => array(),
					"pricelist" => $o->id(),
					"crm_category" => $crm_category,
					"prod_category" => $prod_category,
				));
				$do = $ol->begin();
				if($do)
				{
					return $do->prop("discount");
				}
			}
			return $o->prop("discount");
		}


		$f = array();
		$f["object"] = $this->id();
		$f["time"] = $params["time"];
		$f["uid"] = $params["uid"];
		$f["from"] = $params["from"];
		return discount_obj::get_valid_discount($f);
	}

	/**
		@attrib api=1
	**/
	public function get_all_covers_for_material()
	{
		$ol = new object_list(array(
			"class_id" => CL_MRP_ORDER_COVER,
			"lang_id" => array(),
			"site_id" => array(),
			"status" => object::STAT_ACTIVE,
			"CL_MRP_ORDER_COVER.RELTYPE_APPLIES_PROD" => $this->id()
		));
		return $ol->arr();
	}

	/** Returns the list of replacement products
		@attrib name=get_replacement_products api=1
	**/
	public function get_replacement_products($arr)
	{
		// replacement products via type_code
		$ol = new object_list(array(
			'class_id' => CL_SHOP_PRODUCT,
			'type_code' => $this->prop('type_code'),
		));

		// add replacement products via connections
		$conns = $this->connections_from(array(
			'type' => 'RELTYPE_REPLACEMENT_PROD'
		));
		foreach ($conns as $conn)
		{
			$ol->add($conn->to());
		}
		return $ol->arr();
	}

	/**
		@attrib api=1 params=name
		@param odl optional type=bool
		@param odl_2nd_param optional type=array
	**/
	public function get_packagings($arr = array())
	{
		$params = array(
			"class_id" => CL_SHOP_PRODUCT_PACKAGING,
			"CL_SHOP_PRODUCT_PACKAGING.RELTYPE_PACKAGING(CL_SHOP_PRODUCT)" => $this->id(),
			new obj_predicate_sort(array("jrk" => "ASC")),
		);

		if(!empty($arr["odl"]))
		{
			$odl_2nd_param = !empty($arr["odl_2nd_param"]) ? $arr["odl_2nd_param"] : array(
				CL_SHOP_PRODUCT_PACKAGING => array("name", "jrk", "price")
			);
			$ol = new object_data_list(
				$params,
				$odl_2nd_param
			);
		}
		else
		{
			$ol = new object_list($params);
		}

		return $ol;
	}

	public static function get_packagings_for_id($id)
	{
		$prms = array(
			"class_id" => CL_SHOP_PRODUCT_PACKAGING,
			"CL_SHOP_PRODUCT_PACKAGING.RELTYPE_PACKAGING(CL_SHOP_PRODUCT)" => $id,
			"site_id" => array(),
			"lang_id" => array(),
			new obj_predicate_sort(array("jrk" => "ASC")),
		);
		if(is_array($id))
		{
			$ols = array();
			$odl = new object_data_list(
				$prms,
				array(
					CL_SHOP_PRODUCT_PACKAGING => array("CL_SHOP_PRODUCT_PACKAGING.RELTYPE_PACKAGING(CL_SHOP_PRODUCT).oid" => "products"),
				)
			);
			foreach($odl->arr() as $oid => $odata)
			{
				foreach((array)$odata["products"] as $product)
				{
					if(!isset($ols[$product]))
					{
						$ols[$product] = new object_list;
					}
					$ols[$product]->add($oid);
				}
			}
			return $ols;
		}
		else
		{
			$ol = new object_list($prms);
			return $ol;
		}
	}

	public static function get_packets_for_id($id)
	{
		$prms = array(
			"class_id" => CL_SHOP_PACKET,
			"CL_SHOP_PACKET.RELTYPE_PRODUCT" => $id,
			"site_id" => array(),
			"lang_id" => array(),
			new obj_predicate_sort(array("jrk" => "ASC")),
		);
		if(is_array($id))
		{
			$ols = array();
			$odl = new object_data_list(
				$prms,
				array(
					CL_SHOP_PACKET => array("CL_SHOP_PACKET.RELTYPE_PRODUCT.oid" => "products"),
				)
			);
			foreach($odl->arr() as $oid => $odata)
			{
				foreach((array)$odata["products"] as $product)
				{
					if(!isset($ols[$product]))
					{
						$ols[$product] = new object_list;
					}
					$ols[$product]->add($oid);
				}
			}
			return $ols;
		}
		else
		{
			$ol = new object_list($prms);
			return $ol;
		}
	}

	public function get_amount($warehouse_id)
	{
		$sql = "SELECT amount FROM aw_shop_warehouse_amount WHERE warehouse = '$warehouse_id' AND product = '".$this->id()."'";
		return $GLOBALS["object_loader"]->cache->db_fetch_field($sql, "amount");
	}

	public function get_availability_time($wh_id)
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
			"product" => $this->id(),
			"warehouse" => $wh_id
		));
		if (!$ol->count())
		{
			return "-";
		}
		$o = $ol->begin();
		if ($o->prop("days") > 0)
		{
			return date("d.m.Y", time() + ($o->prop("days") * 24*3600));
		}
		if ($o->prop("weekday") != "")
		{
			$ws = date_calc::get_week_start();
			$ws += 24 * 3600 * 7;
			$ws += ($o->prop("weekday") * 24 * 3600);
			return date("d.m.Y", $ws);
		}
		if ($o->prop("date1"))
		{
			return date("d.m.Y", $o->prop("date1"));
		}
		if ($o->prop("date2"))
		{
			return date("d.m.Y", $o->prop("date2"));
		}
		return "-";
	}

	public function get_categories()
	{
		if(!isset($this->categories))
		{
			$this->categories = $this->get_categories_for_id($this->id());
		}
		return $this->categories;
	}

	public function get_packets()
	{
		if(!isset($this->packets))
		{
			$this->packets = $this->get_packets_for_id($this->id())->ids();
		}
		return $this->packets;
	}

	/**
		@attrib api=1 params=pos
		@param id required type=int/array
			The OID(s) of products to get the categories for
		@returns Array of category OIDs for given product(s) OIDs
	**/
	public static function get_categories_for_id($id)
	{
		if(empty($id))
		{
			return array();
		}

		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"CL_SHOP_PRODUCT_CATEGORY.RELTYPE_CATEGORY(CL_SHOP_PRODUCT)" => $id,
			"lang_id" => array(),
			"site_id" => array(),
		));
		return $ol->ids();
	}

	public function get_first_category_id()
	{
		$ids = $this->get_categories();
		if(is_array($ids))
		{
			return reset($ids);
		}
		else
		{
			return null;
		}
	}

	/** removes product from all categories
		@attrib api=1
		@returns true
	**/
	public function remove_categories()
	{
		$conns = $this->connections_from(array(
			'type' => 'RELTYPE_CATEGORY'
		));
		foreach ($conns as $conn)
		{
			$conn->delete();
		}
		return true;
	}

	/** adds product to category
		@attrib api=1 params=pos
		@param cat optional type=oid
		@returns true
	**/
	public function add_category($cat)
	{
		if(is_oid($cat))
		{
			$this->connect(array(
				"to" => $cat,
				"reltype" => "RELTYPE_CATEGORY",
			));
		}
		return true;
	}

	public function get_size_vals()
	{
		$ret = array();
		$packs =  $this->get_packagings();
		foreach($packs->arr() as $pack)
		{
			$ret[$pack->prop("size")] = $pack->prop("size");
		}
		return $ret;
	}

	public function get_size_price($size = null,$oc = null)
	{
		$packs =  $this->get_packagings();
		foreach($packs->arr() as $pack)
		{
			if(!$size || $pack->prop("size") == $size)
			{
				return $pack->get_special_price($oc);
			}
		}
		return null;
	}

	public function get_package_by_size($size = null)
	{
		$packs =  $this->get_packagings();
		foreach($packs->arr() as $pack)
		{
			if(!$size || $pack->prop("size") == $size)
			{
				return $pack;
			}
		}
		return null;
	}

	public function get_special_price($oc = null)
	{
		if(is_oid($this->prop("special_price_object")))
		{
			return $this->prop("special_price_object.price");
		}
		else
		return $this->get_shop_price($oc);
	}

	public function get_formated_price()
	{
		$sum = $this->get_special_price();
		return number_format($sum,2,".","");
	}

	/**
		@attrib api=1
		@param prefix optional type=string
	**/
	public function get_data($args = array())
	{
		$data = $this->properties_translated();
		$data["size"] = isset($data["size"]) ? str_replace('"' , '' , $data["size"]) : null;
		$data["id"] = $this->id();
		$data["image"] = $this->get_product_image();
		$data["image_url"] = $this->get_product_image_url();
		$data["big_image_url"] = $this->get_product_big_image_url();
		$data["purveyance"] = $this->get_purveyance_comment();
		$data["min_price"] = $this->get_min_price();
		if($this->class_id() == CL_SHOP_PRODUCT_PACKAGING)
		{
			$product = $this->get_product();
			$data["description"] = $product->trans_get_val("description");
			$data["color"] =  str_replace('"' , "" , $product->get_color_name());
			$data["code"] =  $product->prop("code");
			if (!empty($data['special_price_object']))
			{
				$special_price_obj = new object($data['special_price_object']);
				$data["special_price"] = $special_price_obj->prop('price');
			}
		}
		else
		{
			$data["code"] =  $this->prop("code");
		}

		$data["min_price_width_special"] = empty($data["special_price"]) ? $data["min_price"] : ($data["min_price"] > $data["special_price"] ? $data["special_price"] : $data["min_price"]);

		$packet = $this->get_packet();
		if(is_object($packet))
		{
			$data["packet_name"] = $packet->trans_get_val("name");
			$data["brand_name"] = $packet->get_brand();
		}

		// add prefix if requested
		if(isset($args["prefix"]))
		{
			foreach($data as $k => $v)
			{
				$data["{$args["prefix"]}{$k}"] = $v;
				unset($data[$k]);
			}
		}

		return $data;
	}

	public function get_min_price()
	{
		$min = $this->prop("price");
		$t = new object_data_list(
			array(
				"class_id" => CL_SHOP_PRODUCT_PACKAGING,
				"CL_SHOP_PRODUCT_PACKAGING.RELTYPE_PACKAGING(CL_SHOP_PRODUCT)" => $this->id(),
				"CL_SHOP_PRODUCT_PACKAGING.price" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			),
			array(
				CL_SHOP_PRODUCT_PACKAGING =>  array(
					new obj_sql_func(OBJ_SQL_MIN, "price","aw_shop_packaging.aw_price"),
					new obj_sql_func(OBJ_SQL_MIN, "special_price","aw_shop_packaging.aw_special_price")
				)
			)
		);
		$prices = $t->get_element_from_all("price");
		$special_prices = $t->get_element_from_all("special_price");
		if(is_array($prices) and sizeof($prices) > 0)
		{
			$price = reset($prices);
			$special_price = reset($special_prices);

			if (is_numeric($price) and is_numeric($special_price))
			{
				return min($price, $special_price);
			}
			elseif (is_numeric($price))
			{
				return $price;
			}
			elseif (is_numeric($special_price))
			{
				return $special_price;
			}
		}
		return $min;
	}

	private function get_purveyance_comment()
	{
		return t("Tarneinfo puudub", AW_REQUEST_CT_LANG_ID);

		// FIXME: this is broken after making purveyance universal - e.g. no reltype, only one objpicker.
		$ret = array();
		$conns = connection::find(array(
			"to" => $this->id(),
			"from.class_id" => CL_SHOP_PRODUCT_PURVEYANCE,
			"type" => "RELTYPE_PACKAGING"
		));
		foreach($conns as $conn)
		{
			$o = obj($conn["from"]);
			return $o->comment();
		}

		return t("Tarneinfo puudub", AW_REQUEST_CT_LANG_ID);
	}

	public function get_packet_name()
	{
		$packet = $this->get_packet();
		if(is_object($packet))
		{
			$data["packet_name"] = $packet->name();
			$data["brand_name"] = $packet->get_brand();
		}
		return $data["packet_name"];

	}
	/** returns product color name
		@attrib api=1
		@returns string
	**/
	public function get_color_name()
	{
		foreach($this->connections_from(array("type" => "RELTYPE_COLOR")) as $c)
		{
			return $c->prop("to.name");
		}
		return "";
	}



//packaging functions

	/** returns product color name for packaging
		@attrib api=1
		@returns string
	**/
	public function get_product_color_name()
	{

		$product = $this->get_product();
		if(is_object($product))
		{
			foreach($product->connections_from(array("type" => "RELTYPE_COLOR")) as $c)
			{
				return $c->prop("to.name");
			}
		}
		return "";
	}

	private function get_product()
	{
		if(!empty($this->product_object) && is_object($this->product_object))
		{
			return $this->product_object;
		}
		elseif($this->class_id() == CL_SHOP_PRODUCT_PACKAGING)
		{
			$ol = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT,
				"CL_SHOP_PRODUCT.RELTYPE_PACKAGING" => $this->id(),
			));
			$this->product_object = $ol->begin();
			return $this->product_object;
		}
		else
		{
			return $this;
		}

	}

	public function get_product_image()
	{
		$product = $this->get_product();
		$pic = $product->get_first_obj_by_reltype("RELTYPE_IMAGE");
		if(is_object($pic))
		{
			return $pic->get_html();

		}
		else
		{
			return "";
		}
	}

	public function get_product_image_url()
	{
		$product = $this->get_product();
		$pic = $product->get_first_obj_by_reltype("RELTYPE_IMAGE");
		if(is_object($pic))
		{
			return $pic->get_url();
		}
		else
		{
			return "";
		}
	}


	public function get_product_big_image_url()
	{
		$product = $this->get_product();
		$pic = $product->get_first_obj_by_reltype("RELTYPE_IMAGE");
		if(is_object($pic))
		{
			return $pic->get_big_url();
		}
		else
		{
			return "";
		}
	}

	public function get_image_popup()
	{
		$product = $this->get_product();
		$pic = $product->get_first_obj_by_reltype("RELTYPE_IMAGE");
		if(is_object($pic))
		{
			return $pic->get_on_click_js();

		}
		else
		{
			return "";
		}
	}

	public function get_pask()
	{
		$packet = $this->get_packet();
		if(is_object($packet))
		{
			$stuff = $packet->get_pask();
			if(is_array($stuff))
			{
				return reset($stuff);
			}
		}
		return null;
	}

	public function get_packet()
	{
		$product = $this->get_product();
		$ol = $product->get_packets_for_id($product->id());
		return $ol->begin();
	}

	public function get_packet_id()
	{
		$product = $this->get_product();
		$ol = $product->get_packets_for_id($product->id());
		$ids = $ol->ids();
		return reset($ids);
	}

	protected function delete_product_show_cache()
	{
		$cache_dir = aw_ini_get("cache.page_cache")."product_show/";
		foreach(glob(sprintf($cache_dir."*product=%u&*.tpl*", $this->id())) as $file)
		{
			unlink($file);
		}
	}
}
