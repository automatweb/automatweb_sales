<?php

class shop_price_list_obj extends shop_matrix_obj
{
	const CLID = 1457;

	/**
		@attrib params=name
		@param valid optional type=bool
		@param timespan optional type=array
			Array with two elements 'start' and 'end', the start and end of the timespan, respectively. (UNIX timestamps)
	**/
	public static function get_price_lists($arr = array())
	{
		static $retval;
		$hash = serialize($arr);
		if(!isset($retval[$hash]))
		{
			$prms = array(
				"class_id" => CL_SHOP_PRICE_LIST,
				"lang_id" => array(),
				"site_id" => array(),
				new obj_predicate_sort(array(
					"jrk" => "ASC",
				)),
			);

			if(!empty($arr["timespan"]["start"]) && !empty($arr["timespan"]["end"]))
			{	// VALID
				$prms[] = new object_list_filter(array(
					"logic" => "AND",
					"conditions" => array(
						"CL_SHOP_PRICE_LIST.valid_from" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $arr["timespan"]["start"]),
						"CL_SHOP_PRICE_LIST.valid_to" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $arr["timespan"]["end"]),
					)
				));
			}

			if(!empty($arr["valid"]))
			{	// VALID
				$prms[] = new object_list_filter(array(
					"logic" => "AND",
					"conditions" => array(
						"CL_SHOP_PRICE_LIST.valid_from" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, time()),
						"CL_SHOP_PRICE_LIST.valid_to" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, time()),
					)
				));
			}
			elseif(isset($arr["valid"]))
			{	// INVALID
				$prms[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_SHOP_PRICE_LIST.valid_from" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, time()),
						"CL_SHOP_PRICE_LIST.valid_to" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, time()),
					)
				));
			}

			$retval[$hash] = new object_list($prms);
		}
		return $retval[$hash];
	}

	/**
		Returns true if conditions should be stored
	*/
	public static function store_condition($data)
	{
		/*
		if(
			$data["value"] === "0" ||	// The price is set to 0
			aw_math_calc::string2float(trim($data["value"], "+-%")) > 0 ||	// False for something like this: +0% -0 etc
			aw_math_calc::string2float(trim($data["bonus"], "+-%")) > 0	// False for something like this: +0% -0 etc
		)
		*/
		if(strlen(trim($data["value"])) > 0)
		{
			return true;
		}
		return false;
	}

	/**
		@attrib name=price params=name

		@param shop required type=int acl=view
			The OID of the shop_order_center object
		@param product optional type=int acl=view
			The OID of the product. If not given, product_packaging must be given!
		@param product_packet optional type=array/int acl=view
			OIDs of product packets
		@param product_packaging optional type=int acl=view
			OID of product packaging. If not given, product must be given!
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
		@param timespan optional type=array
			Array with two elements 'start' and 'end', the start and end of the timespan, respectively. (UNIX timestamps)
		@param structure optional type=bool default=false
			If set, the structure of the prices will be returned, otherwise only the final prices will be returned.
	**/
	public static function price($arr)
	{
		try
		{
			self::price_validate_arguments($arr);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		enter_function("shop_price_list_obj::price");
		/**
			# STRUCTURE of $retval (if $arr["structure"] is true)
			array(
				[currency OID] => array(
					price => array(
						in => [PRICE_IN]
						out => [PRICE_OUT]
					)
					bonus => array(
						in => [BONUS_IN]
						out => [BONUS_OUT]
					)
					log => array(	// Following will be tracked for every cell row passed
						array(
							type
							diff => array(
								price => [PRICE_DIFF]		// ABS
								bonus => [BONUS_DIFF]		// ABS
							)
						)
					)
				)
			)

		**/
		$retval = array();
		$prices_only_retval = $arr["prices"];
		foreach(array_keys($arr["prices"]) as $currency)
		{
			$bonus = isset($arr["bonuses"][$currency]) ? $arr["bonuses"][$currency] : 0;
			$retval[$currency] = array(
				"price" => array(
					"in" => aw_math_calc::string2float($arr["prices"][$currency]),
					"out" => aw_math_calc::string2float($arr["prices"][$currency]),
				),
				"bonus" => array(
					"in" => aw_math_calc::string2float($bonus),
					"out" => aw_math_calc::string2float($bonus),
				),
				"log" => array(),
			);
		}

		// Find all valid price list objects
		// Later on this should leave out the ones that don't have given customers, products etc..
		$ol = self::get_price_lists(array(
			"shop" => $arr["shop"],
			"timespan" => array(
				"start" => isset($arr["timespan"]["start"]) ? $arr["timespan"]["start"] : time(),
				"end" => isset($arr["timespan"]["end"]) ? $arr["timespan"]["end"] : time(),
			),
		));

		// Prepare the arguments for price evaluation code
		$args = self::handle_arguments($arr);

		foreach($ol->arr() as $o)
		{
			$price_datas = $o->run_price_evaluation_code($args);
			foreach($price_datas as $currency => $price_data)
			{
				$args["prices"][$currency] = $prices_only_retval[$currency] = $retval[$currency]["price"]["out"] = $price_data["price"]["out"];
				$args["bonuses"][$currency] = $retval[$currency]["bonus"]["out"] = $price_data["bonus"]["out"];
				$retval[$currency]["log"] = array_merge($retval[$currency]["log"], safe_array($price_data["log"]));
			}
		}
		exit_function("shop_price_list_obj::price");
		return empty($arr["structure"]) ? $prices_only_retval : $retval;
	}

	protected static function price_validate_arguments($arr)
	{
		if(!isset($arr["shop"]) || !is_oid($arr["shop"]))
		{
			$e = new awex_price_list_parameter(t("Parameter 'shop' must be a valid OID!"));
			throw $e;
		}
		if((!isset($arr["product"]) || !is_oid($arr["product"])) && (!isset($arr["product_packaging"]) || !is_oid($arr["product_packaging"])))
		{
			$e = new awex_price_list_parameter(t("Either parameter 'product' or 'product_packaging' must be a valid OID!"));
			throw $e;
		}
	}

	protected static function handle_arguments($arr)
	{
		$args = array(
			"rows" => array(),
			"amount" => isset($arr["amount"]) ? $arr["amount"] : 1,
			"prices" => $arr["prices"],
			"bonuses" => isset($arr["bonuses"]) ? $arr["bonuses"] : array(),
			"currencies" => array_keys($arr["prices"]),
			"customers" => safe_array(ifset($arr, "customer_category")),
			"locations" => safe_array(ifset($arr, "location")),
			"default" => array(0),
		);

		if(!isset($arr["product"]) && !empty($arr["product_packaging"]))
		{
			$args["product"] = shop_product_packaging_obj::get_products_for_id($arr["product_packaging"]);
		}
		if(!isset($arr["product_packet"]) && !empty($arr["product"]))
		{
			$arr["product_packet"] = array();
			foreach(shop_product_obj::get_packets_for_id((array)$arr["product"]) as $packet_ol)
			{
				$arr["product_packet"] = array_merge($arr["product_packet"], $packet_ol->ids());
			}
		}
		if(!isset($arr["product_category"]))
		{
			$arr["product_category"] = array();
			if(!empty($arr["product"]))
			{
				$arr["product_category"] = shop_product_obj::get_categories_for_id($arr["product"]);
			}
			if(!empty($arr["product_packet"]))
			{
				$arr["product_category"] = shop_packet_obj::get_categories_for_id($arr["product_packet"]);
			}
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

		return $args;
	}

	public function run_price_evaluation_code($args)
	{
		$f = create_function('$args', $this->prop("code"));
		return safe_array($f($args));
	}

	public function update_code()
	{
		$this->prioritize();

		$i = $this->instance();
		$i->read_template("code.aw");

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
				"class_id" => CL_SHOP_PRICE_LIST_CONDITION,
				"price_list" => $this->id(),
				"lang_id" => array(),
				"site_id" => array(),
				"currency" => new obj_predicate_compare(OBJ_COMP_GREATER, 0, false, "int"),
			),
			array(
				CL_SHOP_PRICE_LIST_CONDITION => array("row", "col", "type", "value", "bonus", "quantities", "currency"),
			)
		);
		foreach($odl->arr() as $cond_id => $cond)
		{
			$this->cells[$cond["row"]][$cond["col"]]["conditions"][$cond["currency"]][$cond_id] = array(
				"type" => $cond["type"],
				"value" => $cond["value"],
				"bonus" => $cond["bonus"],
				"quantities" => $cond["quantities"],
			);
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
				if(empty($cell_data["conditions"]))
				{
					continue;
				}
				foreach($cell_data["conditions"] as $currency => $conditions)
				{
					$i->vars(array(
						"currency" => $currency,
					));
					$HANDLE_CELL_ROW = "";
					foreach($conditions as $condition_id => $cond)
					{
						$quantity_conditions = $this->update_code_handle_quantities($cond["quantities"]);
						if(count($quantity_conditions) > 0)
						{
							$QUANTITY_CONDITION = "";
							$quantity_condition_count = 0;
							foreach($quantity_conditions as $quantity_condition)
							{
								$QUANTITY_CONDITION_SINGLE = "";
								$QUANTITY_CONDITION_RANGE = "";
								switch($quantity_condition["type"])
								{
									case "single":
										$i->vars(array(
											"quantity" => $quantity_condition["quantity"],
										));
										$QUANTITY_CONDITION_SINGLE .= rtrim($i->parse("QUANTITY_CONDITION_SINGLE"), "\t");
										break;
										
									case "range":
										$i->vars(array(
											"quantity_from" => $quantity_condition["quantity_from"],
											"quantity_to" => $quantity_condition["quantity_to"],
										));
										$QUANTITY_CONDITION_RANGE .= rtrim($i->parse("QUANTITY_CONDITION_RANGE"), "\t");
										break;
								}
								$i->vars(array(
									"QUANTITY_CONDITION_SINGLE" => $QUANTITY_CONDITION_SINGLE,
									"QUANTITY_CONDITION_RANGE" => $QUANTITY_CONDITION_RANGE,
								));							
								$QUANTITY_CONDITION .= rtrim($i->parse("QUANTITY_CONDITION".(++$quantity_condition_count === 1 ? "_FIRST" : "")), "\t");
							}
							$i->vars(array(
								"QUANTITY_CONDITION_FIRST" => "",
								"QUANTITY_CONDITION" => $QUANTITY_CONDITION,
							));
							$i->vars(array(
								"QUANTITY_CONDITION_START" => rtrim($i->parse("QUANTITY_CONDITION_START"), "\t"),
								"QUANTITY_CONDITION_END" => rtrim($i->parse("QUANTITY_CONDITION_END"), "\t"),
							));
						}
						$i->vars(array(
							"condition_id" => $condition_id,
							"type" => $cond["type"],
							"price_formula" => $cond["value"],
							"bonus_formula" => $cond["bonus"],
						));
						$HANDLE_CELL_ROW .= rtrim($cond["type"] ? $i->parse("HANDLE_CELL_ROW_CUSTOM") : $i->parse("HANDLE_CELL_ROW_AUTO"), "\t");
					}
					if(strlen($HANDLE_CELL_ROW) > 0)
					{
						$i->vars(array(
							"row" => $row,
							"col" => $col,
							"HANDLE_CELL_ROW_CUSTOM" => "",
							"HANDLE_CELL_ROW_AUTO" => $HANDLE_CELL_ROW,
						));
						$HANDLE_CELL .= rtrim($i->parse("HANDLE_CELL"), "\t");
					}
				}
			}
		}
		$i->vars(array(
			"passing_order" => "'".implode("','", array_merge(array_keys(safe_array($this->meta("matrix_col_order"))), array("default")))."'",
			"PARENTS" => $PARENTS,
			"PRIORITIES" => $PRIORITIES,
			"HANDLE_CELL" => $HANDLE_CELL,
		));


		$this->set_prop("code", $i->parse());
		$this->save();
	}

	protected function update_code_handle_quantities($str)
	{
		$retval = array();
		if(strlen($str) > 0)
		{
			foreach(explode(",", $str) as $_str)
			{
				if(($hq = $this->update_code_handle_quantity(trim($_str))) !== false)
				{
					$retval[] = $hq;
				}
			}
		}
		return $retval;
	}

	protected function update_code_handle_quantity($str)
	{
		//	1, 27, 63, 14 etc...
		if(is_numeric($str))
		{
			return array(
				"type" => "single",
				"quantity" => (float)$str,
			);
		}
		// 10-80, 17-19, 100-200 etc...
		elseif(strpos($str, "-") !== false)
		{
			list($from, $to) = explode("-", $str);
			return array(
				"type" => "range",
				"quantity_from" => $from,
				"quantity_to" => $to,
			);
		}
		else
		{
			return false;
		}
	}

	public static function evaluate_price_list_conditions_auto($old_price, $bonus, $price_formula, $bonus_formula)
	{
		$price_formula = trim($price_formula);
		$bonus_formula = trim($bonus_formula);
		$price = $old_price;

		// Handle price formula
		if(substr($price_formula, -1) === "%")		// relative
		{
			$price = $price * (1 + aw_math_calc::string2float($price_formula) / 100);
		}
		elseif(substr($price_formula, 0, 1) === "+" || substr($price_formula, 0, 1) === "-")	// absolute +-
		{
			$price += aw_math_calc::string2float($price_formula);
		}
		elseif(strlen($price_formula) > 0)	// absolute price
		{
			$price = aw_math_calc::string2float($price_formula);
		}

		// Handle bonus formula
		if(substr($bonus_formula, -1) === "%")		// relative
		{
			$bonus += ($price - $old_price) * (aw_math_calc::string2float($bonus_formula) / 100);
		}
		elseif(substr($bonus_formula, 0, 1) === "+" || substr($bonus_formula, 0, 1) === "-")	// absolute +-
		{
			$bonus += aw_math_calc::string2float($bonus_formula);
		}
		elseif(strlen($bonus_formula) > 0)	// absolute bonus
		{
			$bonus = aw_math_calc::string2float($bonus_formula);
		}

		return array($price, $bonus);
	}
}

/* Generic price list exception */
class awex_price_list extends aw_exception {}

/* Indicates invalid argument */
class awex_price_list_parameter extends awex_price_list {}

?>
