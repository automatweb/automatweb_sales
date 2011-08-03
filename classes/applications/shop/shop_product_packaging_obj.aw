<?php

class shop_product_packaging_obj extends shop_product_obj
{
	const CLID = 327;


	function prop($k)
	{

		$rv = parent::prop($k);

		if ($k == "size")
		{
			return str_replace('"' , '' , $rv);
		}

		return $rv;
	}


	public function save($check_state = false)
	{
		$retval = parent::save($check_state);
		// This can't be in set_prop(), cuz set_prop() is not always followed by save() therefore not always the props get set for good! -kaarel 6.08.2009
		if(is_oid($this->prop("product")) && $this->can("view", $this->prop("product")) && count(connection::find(array("from" => $this->prop("product"), "to" => $this->id(), "type" => "RELTYPE_PACKAGING", "from.class_id" => CL_SHOP_PRODUCT))) === 0)
		{
			$product_obj = obj($this->prop("product"));
			$product_obj->connect(array(
				"to" => $this->prop("product"),
				"type" => "RELTYPE_PACKAGING",
			));
		}

		return $retval;
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
		@param structure optional type=bool default=false
			If set, the structure of the prices will be returned, otherwise only the final prices will be returned
	**/
	function get_price($arr)
	{
		$price_value = $this->get_price_value();

		$prices = safe_array($this->meta("cur_prices"));
		if(isset($arr["shop"]) && is_oid($arr["shop"]) && $this->can("view", $arr["shop"]))
		{
			$shop = obj($arr["shop"]);
			// If no prices are set for any currencies, we presume the price property is set for default currency
			if(is_oid($shop->default_currency) && $price_value)
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
					$prices[$shop->default_currency] = $price_value;
				}
			}
			return shop_price_list_obj::price(array(
				"shop" => $arr["shop"],
				"product" => $product = $this->prop("product"),
				"product_packets" => $packet = is_oid($product) ? shop_product_obj::get_packets_for_id($product)->ids() : array(),
				"product_packaging" => $this->id(),
				"product_category" => array_merge((is_oid($product) ? shop_product_obj::get_categories_for_id($product) : array()), (!empty($packet) ? shop_packet_obj::get_categories_for_id($packet)->ids() : array())),
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
	
	/** To handle the transition from regular price property to price object I make a function which returns the price value from object. If there is no price object, then it returns price property value instead
		@attrib name=get_price_value api=1
	**/
	public function get_price_value()
	{
		$value = 0;
		$price_oid = $this->prop('price_object');
		if (!empty($price_oid))
		{
			$price_obj = new object($price_oid);
			$value = $price_obj->prop('price');
		}
		else
		{
			$value = $this->prop('price');
		}

		return (float)$value;
	
	}

	/** Get new price value as float. An packaging can have two prices: One regular and the new one is usually discount price.

		@attrib name=get_special_price api=1

		@returns special_price object's price property value as float
	**/
	public function get_special_price_value()
	{
		$special_price = 0;

		$special_price_oid = $this->prop('special_price_object');
		if (!empty($special_price_oid))
		{
			$special_price_obj = new object($special_price_oid);
			$special_price = $special_price_obj->prop('price');
		}
		return (float)$special_price;
	}

	/** returns product color name
		@attrib api=1
		@returns string
	**/
	public function get_color_name()
	{
		$colors = $this->prop("color");
		if(is_array($colors))
		{
			foreach($colors as $id)
			{
				return get_name($id);
			}
		}
		return "";
	}

	/**
		@attrib api=1 params=pos
		@param id required type=int/array
			The OID(s) of product packagings to get the product(s) for
		@returns Array of product OIDs for given packaging(s) OIDs
	**/
	public static function get_products_for_id($id)
	{
		if(empty($id))
		{
			return array();
		}

		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT,
			"CL_SHOP_PRODUCT.RELTYPE_PACKAGING" => $id,
			"lang_id" => array(),
			"site_id" => array(),
		));
		return $ol->ids();
	}

}
