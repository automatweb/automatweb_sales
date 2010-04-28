<?php

namespace automatweb;


class shop_packet_obj extends _int_object
{
	const AW_CLID = 297;

	/** returns 3 same category packets
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
//			"limit" => 3,
		));
		$ol2 = new object_list();
		$array = $ol->names();
		$rnd = min(array(3 , sizeof($array)));
		if($rnd)
		{
			$ol2->add(array_rand($array, $rnd));
		}
		return $ol2;
	}

	public function get_products()
	{
		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_SHOP_PRODUCT.RELTYPE_PRODUCT(CL_SHOP_PACKET)" => $this->id()
		));
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
		$ol = new object_list();
		foreach($this->connections_from(array(
			"type" => "RELTYPE_CATEGORY",

		)) as $c)
		{
			$ol->add($c->prop("to"));;
		}
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
		if(!sizeof($params) || isset($params["min_price"])) $data["min_price"] = $this->get_min_price(!empty($GLOBALS["order_center"]) ? $GLOBALS["order_center"] : null); //$data["min_price"] = min($data["prices"]);
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
			$ret[$packaging->id()] = $packaging->get_shop_price($shop, $currency);
		}
		exit_function("packet_obj::get_prices");
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
			return number_format(reset($prices) , 2);
		}
		return $min;
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
		$menus = array();
		foreach($ol->arr() as $o)
		{
			$menus[] = $o->parent();
		}
		return $menus;
	}

}

?>
