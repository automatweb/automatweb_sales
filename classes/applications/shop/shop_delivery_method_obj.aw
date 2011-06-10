<?php

class shop_delivery_method_obj extends shop_matrix_obj
{
	const CLID = 1558;

	public function prop($k)
	{
		switch($k)
		{
			case "enabling_type":
			case "type":
				return ($val = aw_math_calc::string2float(parent::prop($k))) > 0 ? $val : 1;

			default:
				return parent::prop($k);
		}
	}

	/**
		@attrib name=price params=name

		@param product_packaging optional type=int/array acl=view
			The OID(s) of the product packagings
		@param product optional type=int/array acl=view
			The OID(s) of the product
		@param product_packet optional type=array/int acl=view
			OIDs of product packets
		@param amount optional type=float default=1
			The amount of the product prices are asked for
		@param product_category optional type=array/int acl=view
			OIDs of product categories
		@param prices optional type=float
			The before prices by currencies
		@param bonuses optional type=float default=0
			The before points
		@param customer_data optional type=int acl=view
			OID of customer_data object
		@param customer_category optional type=array/int acl=view
			OIDs of customer categories.
		@param location optional type=array/int acl=view
			OIDs of locations
	**/
	public function valid($arr)
	{
		try
		{
			self::valid_validate_arguments($arr);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		enter_function("shop_delivery_method_obj::valid");

		// Prepare the arguments for price evaluation code
		$args = self::handle_arguments($arr);
		$retval = $this->run_validation_evaluation_code($args);
		exit_function("shop_delivery_method_obj::valid");
		return $retval;
	}

	protected static function valid_validate_arguments($arr)
	{
	}

	protected static function handle_arguments($arr)
	{
		$args = array(
			"amount" => isset($arr["amount"]) ? $arr["amount"] : 1,
			"customers" => safe_array(ifset($arr, "customer_category")),
			"locations" => safe_array(ifset($arr, "location")),
			"default" => array(0),
			"rows" => array(),
		);

		if(!isset($arr["product"]) && !empty($arr["product_packaging"]))
		{
			$arr["product"] = shop_product_packaging_obj::get_products_for_id($arr["product_packaging"]);
		}
		elseif(!empty($arr["product_packaging"]))
		{
			$arr["product"] = array_merge((array)$arr["product"], shop_product_packaging_obj::get_products_for_id($arr["product_packaging"]));
		}

		if(!empty($arr["product"]))
		{
			$arr["product_packet"] = isset($arr["product_packet"]) ? $arr["product_packet"] : array();
			foreach(shop_product_obj::get_packets_for_id((array)$arr["product"]) as $packet_ol)
			{
				$arr["product_packet"] = array_merge($arr["product_packet"], $packet_ol->ids());
			}
		}

		$arr["product_category"] = isset($arr["product_category"]) ? $arr["product_category"] : array();
		if(!empty($arr["product"]))
		{
			$arr["product_category"] = array_merge($arr["product_category"], shop_product_obj::get_categories_for_id($arr["product"]));
		}
		if(!empty($arr["product_packet"]))
		{
			$arr["product_category"] = array_merge($arr["product_category"], shop_packet_obj::get_categories_for_id($arr["product_packet"])->ids());
		}

		if(!empty($arr["product"]))
		{
			$args["rows"] = array_merge($args["rows"], (array)$arr["product"]);
		}
		if(!empty($arr["product_packet"]))
		{
			$args["rows"] = array_merge($args["rows"], (array)$arr["product_packet"]);
		}
		if(!empty($arr["product_category"]))
		{
			$args["rows"] = array_merge($args["rows"], (array)$arr["product_category"]);
		}
		if(!empty($arr["product_packaging"]))
		{
			$args["rows"] = array_merge($args["rows"], (array)$arr["product_packaging"]);
		}

		// Currently the matrix is only configurable for product categories anyway
		// DO NOT change this unless you're aware that if enabling_mode == 2 it currently only works like this! -kaarel 25.08.2009
		$args["rows"] = !empty($arr["product_category"]) ? $arr["product_category"] : array();

		return $args;
	}

	public function set_price($arr)
	{
		// LATER ON SHOULD BE BUILT ON PRICE OBJECTS. I CAN'T UNDERSTAND THE LOGIC BEHIND THOSE AT THE MOMENT -kaarel 30.07.2009
		$this->set_meta("prices", $arr);
	}

	public function get_price()
	{
		static $prices;
		if(!isset($prices[$this->id()]))
		{
			// LATER ON SHOULD BE BUILT ON PRICE OBJECTS. I CAN'T UNDERSTAND THE LOGIC BEHIND THOSE AT THE MOMENT -kaarel 30.07.2009
			$prices[$this->id()] = $this->meta("prices");
		}

		
		if(aw_ini_get("site_id") == 484)// keyword
		{
			$discount = 0;
			if($_GET["class"] == "orders_form")
			{
				foreach($_SESSION["order"] as $key => $data)
				{
					if(substr(trim($data["product_code"]) , 0 , 1) == "V" || substr(trim($data["product_code"]) , 0 , 1) == "v")
					{
						$discount = 1;
					}
				}
			}
			else
			{
				foreach($_SESSION["cart"]["items"] as $key => $items)
				{
					if(is_oid($key) && $items[0]["items"])
					{
						$ol = new object_list(array(
							"class_id" => CL_SHOP_PRODUCT,
							"CL_SHOP_PRODUCT.RELTYPE_PACKAGING" => $key,
							"lang_id" => array(),
							"site_id" => array(),
						));
						foreach($ol->arr() as $o)
						{
							if(substr(trim($o->prop("code")) , 0 , 1) == "V" || substr(trim($o->prop("code")) , 0 , 1) == "v")
							{
								$discount = 1;
							}
						}
					}
				}
			}

			if($discount)
			{
				foreach($prices as $id => $asd)
				{
					foreach($asd as $curr => $val)
					{
						$prices[$id][$curr] = 0;
					}
				}
			}
		//	arr($prices);
		}
		return $prices[$this->id()];
	}

	//oc - order center object
	public function get_shop_price($oc)
	{
		static $price;
		if(!isset($price[$this->id()]))
		{
			// LATER ON SHOULD BE BUILT ON PRICE OBJECTS. I CAN'T UNDERSTAND THE LOGIC BEHIND THOSE AT THE MOMENT -kaarel 30.07.2009
			$prices[$this->id()] = $this->get_price();
		}
		return $prices[$this->id()][$oc->prop("default_currency")];
	}

	//oc - order center object
	public function get_curr_price($curr)
	{
		if(!isset($prices))
		{
			// LATER ON SHOULD BE BUILT ON PRICE OBJECTS. I CAN'T UNDERSTAND THE LOGIC BEHIND THOSE AT THE MOMENT -kaarel 30.07.2009
			$prices = $this->get_price();
		}
		return $prices[$curr];
	}

	public function update_code()
	{
		$this->prioritize();

		$i = $this->instance();
		$i->read_template("code.aw");
		$i->vars(array(
			"enabled_by_default" => $this->prop("enabled") ? "true" : "false",
			"passing_order" => "'".implode("','", array_merge(array_keys(safe_array($this->meta("matrix_col_order"))), array("default")))."'",			
		));

		$matrix_structure = $this->get_matrix_structure($this);
		$this->cells = array();
		foreach($matrix_structure["rows"]["products"] as $row => $subrows)
		{
			$this->update_code_add_cell($row, 0);
			foreach($matrix_structure["cols"]["customers"] as $col => $subcols)
			{
				$this->update_code_add_cell($row, $col, array("row" => $row, "col" => 0), $subrows, $subcols);
			}
		}
		$odl = new object_data_list(
			array(
				"class_id" => CL_SHOP_DELIVERY_METHOD_CONDITIONS,
				"delivery_method" => $this->id(),
				"lang_id" => array(),
				"site_id" => array(),
			),
			array(
				CL_SHOP_DELIVERY_METHOD_CONDITIONS => array("row", "col", "enable"),
			)
		);
		foreach($odl->arr() as $cond)
		{
			$matrix[$cond["row"]][$cond["col"]] = $cond["enable"] ? 1 : 2;
			$this->cells[$cond["row"]][$cond["col"]]["enable"] = $cond["enable"] ? 1 : 2;
		}

		$PRIORITIES = "";
		foreach($matrix_structure["priorities"] as $id => $priority)
		{
			$i->vars(array(
				"id" => $id,
				"priority" => $priority,
			));
			$PRIORITIES .= $i->parse("PRIORITIES");
		}

		$PARENTS = "";
		foreach($matrix_structure["parents"] as $id => $parents)
		{
			$i->vars(array(
				"id" => $id,
				"parents" => count($parents) ? "'".implode("','", $parents)."'" : "",
			));
			$PARENTS .= $i->parse("PARENTS");
		}

		$HANDLE_CELL = "";
		foreach($this->cells as $row => $cols)
		{
			foreach($cols as $col => $cell_data)
			{
				if(isset($cell_data["enable"]))
				{
					$i->vars(array(
						"row" => $row,
						"col" => $col,
						"enable" => $cell_data["enable"] == 2 ? "false" : "true",
					));
					if($this->prop("enabling_type") == 2)
					{
						$i->vars(array(
							"ENABLING_TYPE_1_HANDLE_CELL" => "",
							"ENABLING_TYPE_2_HANDLE_CELL" => rtrim($i->parse("ENABLING_TYPE_2_HANDLE_CELL"), "\t"),
						));
					}
					else
					{
						$i->vars(array(
							"ENABLING_TYPE_1_HANDLE_CELL" => rtrim($i->parse("ENABLING_TYPE_1_HANDLE_CELL"), "\t"),
							"ENABLING_TYPE_2_HANDLE_CELL" => "",
						));
					}
					$HANDLE_CELL .= rtrim($i->parse("HANDLE_CELL"), "\t");
				}
			}
		}

		$i->vars(array(
			"PARENTS" => $PARENTS,
			"PRIORITIES" => $PRIORITIES,
			"HANDLE_CELL" => $HANDLE_CELL,
		));
		$i->vars(array(
			"ENABLING_TYPE_2_INITIALIZE" => $this->prop("enabling_type") == 2 ? rtrim($i->parse("ENABLING_TYPE_2_INITIALIZE"), "\t") : "",
			"ENABLING_TYPE_1_RETURN" => $this->prop("enabling_type") == 1 ? rtrim($i->parse("ENABLING_TYPE_1_RETURN"), "\t") : "",
			"ENABLING_TYPE_2_RETURN" => $this->prop("enabling_type") == 2 ? rtrim($i->parse("ENABLING_TYPE_2_RETURN"), "\t") : "",
		));

		$this->set_prop("code", $i->parse());
		$this->save();
	}

	public function run_validation_evaluation_code($args)
	{
		$f = create_function('$args', $this->prop("code"));
		return $f($args);
	}

	public function get_vars($order_data)
	{
		$vars = array();
		if($order_data["smartpost_sell_place"])
		{
			$sm = smart_post_obj::get_smart_post();
			$vars["smartpost_sell_place_name"] = $sm->get_place_name_by_id($order_data["smartpost_sell_place"]);

		}
		elseif($order_data["post_office_sell_place"])
		{
			$vars["smartpost_sell_place_name"] = obj($order_data["post_office_sell_place"])->name();

		}
		return $vars;
	}
}

/* Generic price list exception */
class awex_shop_delivery_method extends aw_exception {}

/* Indicates invalid argument */
class awex_shop_delivery_method_parameter extends awex_price_list {}


?>
