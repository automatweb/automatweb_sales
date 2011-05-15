<?php

class shop_packet_obj extends _int_object
{

	function delete($full_delete = false)
	{
		$this->delete_product_show_cache();

		$ws = $this->get_warehouse_settings();
		if(is_object($ws) && $ws->prop("delete_all_lower_products"))
		{
			foreach($this->get_products()->arr() as $product)
			{
				$product -> delete();
			}
		}

		parent::delete($full_delete);
	}

	public function save($exclusive = false, $previous_state = null)
	{
		$this->delete_product_show_cache();

		return parent::save($exclusive, $previous_state);
	}

	private function get_warehouse_settings()
	{
		$ol = new object_list(array(
			"site_id" => array(),
			"lang_id" => array(),
			"class_id" => CL_SHOP_WAREHOUSE_CONFIG,
		));
		return $ol->begin();
	}

	/** returns 4 same category packets
		@attrib api=1
		 @returns object list
			packet object list
	**/
	public function get_same_cat_packets()
	{
		$cat = $this->get_first_obj_by_reltype("RELTYPE_CATEGORY");
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PACKET,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_SHOP_PACKET.RELTYPE_CATEGORY" => $cat->id(),
			"oid" => new obj_predicate_not($this->id()),
			"status" => 2,
//			"limit" => 3,
		));
		$ol2 = new object_list();
		$array = $ol->names();
		$rnd = min(array(4 , sizeof($array)));
		if($rnd)
		{
			$ol2->add(array_rand($array, $rnd));
		}
		return $ol2;
	}

	public function get_products()
	{
		enter_function("packet_obj::get_products");
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_SHOP_PRODUCT.RELTYPE_PRODUCT(CL_SHOP_PACKET)" => $this->id()
		));
		exit_function("packet_obj::get_products");
		return $ol;
	}

	public function get_first_caregory_id()
	{
		foreach($this->connections_from(array(
			"type" => "RELTYPE_CATEGORY",

		)) as $c)
		{
			return $c->prop("to");
		}
		return null;
	}
	
	public function get_categories()
	{
		enter_function("packet_obj::get_cat");
/*		$ol = new object_list();
		foreach($this->connections_from(array(
			"type" => "RELTYPE_CATEGORY",

		)) as $c)
		{
			$ol->add($c->prop("to"));;
		}
		*/
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_SHOP_PRODUCT_CATEGORY.RELTYPE_CATEGORY(CL_SHOP_PACKET)" => $this->id(),
		));
		exit_function("packet_obj::get_cat");
		return $ol;
	}
	
	public static function get_categories_for_id($id)
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"CL_SHOP_PRODUCT_CATEGORY.RELTYPE_CATEGORY(CL_SHOP_PACKET)" => $id,
			"lang_id" => array(),
			"site_id" => array(),
		));
		return $ol;
	}

	private function random_product_id()
	{
		$product = $this->get_first_obj_by_reltype("RELTYPE_PRODUCT");
		if(is_object($product))
		{
			return $product->id();
		}
		return null;
	}

	public function get_data($params = array())
	{
		enter_function("packet_obj::get_data");
		$data = $this->properties();
		$data["id"] = $this->id();

		if(!sizeof($params)|| isset($params["product_id"])) $data["product_id"] = $this->random_product_id();
		if(!sizeof($params)) $data["image"] = $this->get_image();
		if(!sizeof($params) || isset($params["image_url"])) $data["image_url"] = $this->get_image_url();

		if(!sizeof($params)) $data["big_image_url"] = $this->get_big_image_url();
		if(!sizeof($params)) $data["big_image"] = $this->get_big_image();
		if(!sizeof($params)) $data["image_urls"] = $this->get_image_urls();
		if(!sizeof($params)) $data["big_image_urls"] = $this->get_big_image_urls();
		if(!sizeof($params)) $data["colors"] = $this->get_colors();
		if(!sizeof($params)) $data["packages"] = $this->get_packagings();
		if(!sizeof($params) || isset($params["prices"])) $data["prices"] = $this->get_prices(!empty($GLOBALS["order_center"]) ? $GLOBALS["order_center"] : null);
		if(!sizeof($params) || isset($params["min_price"]))
		{
			$data["unformated_min_price"] = $this->get_min_price(!empty($GLOBALS["order_center"]) ? $GLOBALS["order_center"] : null); //$data["min_price"] = min($data["prices"]);
			$data["min_price"] = number_format($data["unformated_min_price"] , 2, "." , "");
		}
		if(!sizeof($params) || isset($params["special_prices"])) $data["special_prices"] = $this->get_special_prices(!empty($GLOBALS["order_center"]) ? $GLOBALS["order_center"] : null);
		if(!sizeof($params) || isset($params["min_special_price"])) $data["min_special_price"] = $this->get_min_special_price();
	//	if(!sizeof($params) || isset($params["min_special_price"])) $data["min_special_price"] = $this->get_min_special_price(!empty($GLOBALS["order_center"]) ? $GLOBALS["order_center"] : null); //$data["min_price"] = min($data["prices"]);
		if(!sizeof($params)) $data["sizes"] = $this->get_sizes();
		if(!sizeof($params)) $data["descriptions"] = $this->get_descriptions();
		if(!sizeof($params)) $data["brand_image"] = $this->get_brand_image();
		if(!sizeof($params) || isset($params["brand_name"])) $data["brand"] = $this->get_brand();
		if(!sizeof($params) || isset($params["crap"]))
		{
			$data["crap"] = $this->get_crap();
			$data["cat_comments"] = array();
			$data["cat_images"] = array();
			foreach($data["crap"] -> arr() as $crap)
			{
				$data["cat_comments"][$crap->id()] = $crap->prop("desc");
				$data["cat_images"][$crap->id()] = $crap->get_image_url();
			}
		}
//		$data["min_price"] = $this->get_min_price($shop, $currency);
		if(!sizeof($params))
		{
			$data["image_size"] = $this->get_image_size();
			$data["image_width"] = $data["image_size"][0];
			$data["image_height"] = $data["image_size"][1];
			//$data["image_height"] = $this->get_image_width();
		}
		exit_function("packet_obj::get_data");
		return $data;
	}

	public function get_brand_image($original = null)
	{
		enter_function("packet_obj::get_brand_image");
		$ret = "";
/*		if(!$original)
		{
			$product = $this->get_first_obj_by_reltype("RELTYPE_PRODUCT");
			if(is_object($product))
			{
				$filter = array(
					"code" => $product->prop("code"),
					"site_id" => $this->site_id(),
					"prop" => "brand_image",
				);

				$ret = $GLOBALS["object_loader"]->cache->do_orb_method_call(array(
					"class" => "shop_packet",
					"action" => "get_data",
					"method" => "xmlrpc",
					"server" => "otto-suomi.dev.automatweb.com",
					"no_errors" => true,
					"params" => $filter,
				));

				if($ret)
				{exit_function("packet_obj::get_brand_image");
					return $ret;
				}

			}
		}
*/

		$brand = $this->get_first_obj_by_reltype("RELTYPE_BRAND");
		if(is_object($brand))
		{
			$ret = $brand->get_logo_html();
		}
exit_function("packet_obj::get_brand_image");
		return $ret;
	}

	public function get_brand()
	{
		$brand = $this->get_first_obj_by_reltype("RELTYPE_BRAND");
		if(is_object($brand))
		{
			return $brand->name();
		}
		else
		{
			return null;
		}
	}

	private function _set_image_object()
	{
		if(empty($this->image_object))
		{
			foreach($this->connections_from(array(
				"type" => "RELTYPE_IMAGE",

			)) as $c)
			{
				$this->image_object = $c->to();
				return;
			}
			foreach($this->connections_from(array(
				"type" => "RELTYPE_PRODUCT",
				"sort_by_num" => "to.jrk",
				"sort_dir" => "asc"
			)) as $c)
			{
				$product = $c->to();
				foreach($product->connections_from(array(
					"type" => "RELTYPE_IMAGE",

				)) as $c)
				{
					$this->image_object = $c->to();
					return;
				}
			}		
			return "";
		}
	}

	//makes var product_objects usable for everyone
	private function _set_products()
	{ 
		enter_function("packet_obj::_set_products");
		if(empty($this->product_objects))
		{
			$this->product_objects = new object_list();
			foreach($this ->connections_from(array(
				"type" => "RELTYPE_PRODUCT",
				"sort_by_num" => "to.jrk",
				"sort_dir" => "asc"
			)) as $c)
			{
				$this->product_objects->add($c->prop("to"));
			}
		}
		exit_function("packet_obj::_set_products");
	}

	private function get_image_urls()
	{
		$ret = array();
		$this->_set_products();
		foreach($this->product_objects->arr() as $product)
		{
			$ret[$product->id()] = $product->get_product_image_url();
		}
		return $ret;
	}
	
	private function get_big_image_urls()
	{
		$ret = array();
		$this->_set_products();
		foreach($this->product_objects->arr() as $product)
		{
			$ret[$product->id()] = $product->get_product_big_image_url();
		}
		return $ret;
	}


	//makes var packaging_objects usable for everyone
	private function _set_packagings()
	{
		if(empty($this->packaging_objects))
		{
			$this->_set_products();
			$ret = array();
			$this->packaging_objects = new object_list();
			foreach($this->product_objects->arr() as $product)
			{
				$this->packaging_objects->add($product->get_packagings());
			}
		}
	}

	private function get_image_url()
	{
		$this->_set_image_object();
		if(!empty($this->image_object) && is_object($this->image_object))
		{
			return $this->image_object->get_url();
		}		
		return "";
	}

	private function get_image_size()
	{
		$this->_set_image_object();
		if(!empty($this->image_object) && is_object($this->image_object))
		{
			return $this->image_object->get_size();
		}		
		return "";
	}

	private function get_image()
	{
		$this->_set_image_object();
		if(!empty($this->image_object) && is_object($this->image_object))
		{
			return $this->image_object->get_html();
		}		
		return "";
	}

	private function get_big_image()
	{
		$this->_set_image_object();
		if(!empty($this->image_object) && is_object($this->image_object))
		{
			return $this->image_object->get_big_html();
		}		
		return "";
	}
	private function get_big_image_url()
	{
		$this->_set_image_object();
		if(!empty($this->image_object) && is_object($this->image_object))
		{
			return $this->image_object->get_big_url();
		}		
		return "";
	}
//returns array(product id => color name)
	private function get_colors()
	{
		$colors = array();
		foreach($this ->connections_from(array(
			"type" => "RELTYPE_PRODUCT",
			"sort_by_num" => "to.jrk",
			"sort_dir" => "asc"
		)) as $c)
		{
			$product = $c->to();
			$color = $product->get_color_name();
			if($color)
			{
				$colors[$product->id()] = $color;
			}
		}
		return $colors;
	}

	private function get_packagings()
	{
		$this->_set_products();
		$ret = array();
		foreach($this->product_objects->arr() as $product)
		{
			$ret[$product->id()] = $product->get_packagings()->ids();
		}
		return $ret;
	}

	private function get_prices($shop = null, $currency = null)
	{
		enter_function("packet_obj::get_prices");
		$ret = array();
		$this->_set_packagings();
		foreach($this->packaging_objects->arr() as $packaging)
		{
			$ret[$packaging->id()] = number_format($packaging->get_shop_price($shop, $currency) , 2, '.', '');
		}
		exit_function("packet_obj::get_prices");
		return $ret;
	}

	private function get_special_prices($shop = null, $currency = null)
	{
//		if (aw_global_get("uid") != "markop") return array();

		enter_function("packet_obj::get_special_prices");
		$ret = array();
		$this->_set_packagings();
		foreach($this->packaging_objects->arr() as $packaging)
		{
			$ret[$packaging->id()] = $packaging->get_shop_special_price($shop, $currency);
		}
		exit_function("packet_obj::get_special_prices");
		return $ret;
	}

	private function get_sizes()
	{
		$ret = array();
		$this->_set_packagings();
		foreach($this->packaging_objects->arr() as $packaging)
		{
			$ret[$packaging->id()] = $packaging->prop("size");
		}
		return $ret;
	}

	private function get_min_price()
	{
		enter_function("packet_obj::get_min_price");
		$min = "";
		//$this->_set_products();
		$t = new object_data_list(
			array(
				"class_id" => CL_SHOP_PRODUCT_PACKAGING,
				"site_id" => array(),
				"lang_id" => array(),
				"CL_SHOP_PRODUCT_PACKAGING.RELTYPE_PACKAGING(CL_SHOP_PRODUCT).RELTYPE_PRODUCT(CL_SHOP_PACKET)" => $this->id(),//$this->product_objects->ids(),
//				"product.CL_SHOP_PRODUCT.RELTYPE_PRODUCT(CL_SHOP_PACKET)" => $this->id(),//$this->product_objects->ids(),
				"CL_SHOP_PRODUCT_PACKAGING.price" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			),
			array(
				CL_SHOP_PRODUCT_PACKAGING =>  array(new obj_sql_func(OBJ_SQL_MIN, "price","aw_shop_packaging.aw_price"))
			)
		);
		$prices = $t->get_element_from_all("price");
		exit_function("packet_obj::get_min_price");
		if(is_array($prices) && sizeof($prices))
		{
			return reset($prices);
		}
		return $min;
	}

	/*
		Apparently I don't get this thing at all, so I just don't use it at the moment :/
	*/
	private function get_min_special_price()
	{
	//	if(aw_global_get("uid") != "struktuur.markop") return 0;
		enter_function("packet_obj::get_min_special_price");
		$min = "";
//tyra, miks see ei toimi
/*		$t = new object_data_list(
			array(
				"class_id" => CL_SHOP_ITEM_PRICE,
				"site_id" => array(),
				"lang_id" => array(),
				"price" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			"CL_SHOP_ITEM_PRICE.RELTYPE_PRICE(CL_SHOP_PRODUCT_PACKAGING).RELTYPE_PACKAGING(CL_SHOP_PRODUCT).RELTYPE_PRODUCT(CL_SHOP_PACKET)" => $this->id()
				),
			array(
				CL_SHOP_ITEM_PRICE => array(new obj_sql_func(OBJ_SQL_MIN, 'sum', 'aw_shop_item_prices.price'))
			)
		);
		$prices = $t->get_element_from_all("sum");
*/
		$packets = new object_list(array(
				"class_id" => CL_SHOP_PRODUCT_PACKAGING,
				"site_id" => array(),
				"lang_id" => array(),
				"special_price_object" => new obj_predicate_not(0),
	"CL_SHOP_PRODUCT_PACKAGING.RELTYPE_PACKAGING(CL_SHOP_PRODUCT).RELTYPE_PRODUCT(CL_SHOP_PACKET)" => $this->id(),
		));//var_dump($packets->count());
		if(!$packets->count())
		{exit_function("packet_obj::get_min_special_price");
			return 0;
		}
		$t = new object_data_list(
			array(
				"class_id" => CL_SHOP_ITEM_PRICE,
				"site_id" => array(),
				"lang_id" => array(),
				"price" =>  new obj_predicate_compare(OBJ_COMP_GREATER, 0),
				"CL_PRICE.RELTYPE_PRICE(CL_SHOP_PRODUCT_PACKAGING)" => $packets->ids()
			),
			array(
		//				CL_PRICE => array("sum")

						CL_SHOP_ITEM_PRICE => array(new obj_sql_func(OBJ_SQL_MIN, 'sum', 'aw_shop_item_prices.price'))
			)
		);
		$prices = $t->get_element_from_all("sum");
		
		exit_function("packet_obj::get_min_special_price");//var_dump($prices);
		return number_format($prices[0],2);
	}

	private function get_descriptions()
	{
		$ret = array();
		$this->_set_products();
		foreach($this->product_objects->arr() as $product)
		{
			$ret[$product->id()] = $product->prop("description");
		}
		return $ret;
	}

	private function get_crap()
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"CL_SHOP_PRODUCT_CATEGORY.RELTYPE_CATEGORY(CL_SHOP_PACKET)" => $this->id(),
			"lang_id" => array(),
			"site_id" => array(),
		));
		return $ol;
	}

	public function get_pask()
	{
		$categories = $this->get_crap();
		$ol = new object_list();
		if($categories->count())
		{
			$ol = new object_list(array(
				"class_id" => CL_PRODUCTS_SHOW,
				"CL_PRODUCTS_SHOW.RELTYPE_CATEGORY" => $categories->ids(),
			));
		}

		if(!$ol->count())
		{
			$ol = new object_list(array(
				"class_id" => CL_PRODUCTS_SHOW,
//				"CL_PRODUCTS_SHOW.RELTYPE_CATEGORY" => $categories->ids(),
				"limit" => 1
			));
		}
		
		$menus = array();
		foreach($ol->arr() as $o)
		{
			$menus[] = $o->parent();
		}

		return $menus;
	}

	protected function delete_product_show_cache()
	{
		$cache_dir = aw_ini_get("cache.page_cache")."/product_show/";
		foreach(glob(sprintf($cache_dir."*product=%u&*.tpl*", $this->id())) as $file)
		{
			unlink($file);
		}
	}

}

?>
