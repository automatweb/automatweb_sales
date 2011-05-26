<?php

class shop_payment_type_obj extends shop_matrix_obj
{
	const CLID = 1577;

	/**
		@attrib params=name api=1
		@param row optional
		@param col optional
		@param currency optional
	**/
	public function conditions($arr = array())
	{
		$prms = array(
			"class_id" => CL_SHOP_PAYMENT_TYPE_CONDITIONS,
			"payment_type" => $this->id(),
			new obj_predicate_sort(array(
				"min_amt" => "ASC",
			)),
		);
		if(isset($arr["row"]))
		{
			$prms["row"] = is_oid($arr["row"]) ? $arr["row"] : 0;
		}
		if(isset($arr["col"]))
		{
			$prms["col"] = is_oid($arr["col"]) ? $arr["col"] : 0;
		}
		if(isset($arr["currency"]) && is_oid($arr["currency"]))
		{
			$prms["currency"] = $arr["currency"];
		}
		$ol = new object_list($prms);
		return $ol;
	}

	/**
		@attrib params=name api=1

		@param sum required type=float
			The total sum of products
		@param currency required type=int
			The OID of currency
		@param product optional type=array/int acl=view
			OIDs of products
		@param product_packaging optional type=array/int acl=view
			OIDs of products
		@param product_category optional type=array/int acl=view
			OIDs of product categories
		@param customer_data optional type=int acl=view
			OID of customer_data object
		@param customer_category optional type=array/int acl=view
			OIDs of customer categories.
		@param location optional type=array/int acl=view
			OIDs of locations
	**/
	public function valid_conditions($arr)
	{
		try
		{
			self::validate_arguments($arr);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		enter_function("shop_payment_type_obj::valid_conditions");

		// Prepare the arguments for price evaluation code
		$args = self::handle_arguments($arr);
		$retval = $this->run_payment_type_evaluation_code($args);
		exit_function("shop_payment_type_obj::valid_conditions");
		return $retval;
	}

	protected static function validate_arguments($arr)
	{
		if(!isset($arr["currency"]) || !is_oid($arr["currency"]))
		{
			$e = new awex_price_list_parameter(t("Parameter 'currency' must be a valid OID!"));
			throw $e;
		}
		if(!isset($arr["sum"]))
		{
			$e = new awex_price_list_parameter(t("Parameter 'sum' must be given!"));
			throw $e;
		}
	}

	protected static function handle_arguments($arr)
	{
		$args = array(
			"sum" => aw_math_calc::string2float($arr["sum"]),
			"currency" => $arr["currency"],
			"customers" => safe_array(ifset($arr, "customer_category")),
			"locations" => safe_array(ifset($arr, "location")),
			"default" => array(0),
		);

		if(!isset($arr["product"]))
		{
			$arr["product"] = isset($arr["product_packaging"]) ? shop_product_packaging_obj::get_products_for_id($arr["product_packaging"]) : array();
		}

		if(!isset($arr["product_category"]))
		{
			$arr["product_category"] = isset($arr["product"]) ? shop_product_obj::get_categories_for_id($arr["product"]) : array();
		}

		if(!isset($arr["product_packaging"]))
		{
			$arr["product_packaging"] = array();
		}

		$args["rows"] = array_merge((array)$arr["product_packaging"], (array)$arr["product"], (array)$arr["product_category"]);
		return $args;
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
				"class_id" => CL_SHOP_PAYMENT_TYPE_CONDITIONS,
				"payment_type" => $this->id(),
				"lang_id" => array(),
				"site_id" => array(),
			),
			array(
				CL_SHOP_PAYMENT_TYPE_CONDITIONS => array("row", "col", "currency", "min_amt", "max_amt", "ignore_min_amt", "ignore_max_amt"),
			)
		);
		foreach($odl->arr() as $cond)
		{
			$this->cells[$cond["row"]][$cond["col"]]["conditions"][$cond["currency"]][$cond["oid"]] = array(
				"min" => $cond["min_amt"],
				"ignore_min" => $cond["ignore_min_amt"],
				"max" => $cond["max_amt"],
				"ignore_max" => $cond["ignore_max_amt"],
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
						$i->vars(array(
							"condition_id" => $condition_id,
							"minimum_sum" => $cond["min"],
							"maximum_sum" => $cond["max"],
						));
						if($cond["max"] == 0 || $cond["ignore_max"])
						{
							$i->vars(array(
								"HANDLE_CELL_ROW_WITH_MAXIMUM_SUM" => "",
								"HANDLE_CELL_ROW_WITHOUT_MAXIMUM_SUM" => $i->parse("HANDLE_CELL_ROW_WITHOUT_MAXIMUM_SUM"),
							));
						}
						else
						{
							$i->vars(array(
								"HANDLE_CELL_ROW_WITH_MAXIMUM_SUM" => $i->parse("HANDLE_CELL_ROW_WITH_MAXIMUM_SUM"),
								"HANDLE_CELL_ROW_WITHOUT_MAXIMUM_SUM" => "",
							));
						}
						$HANDLE_CELL_ROW .= rtrim($i->parse("HANDLE_CELL_ROW"), "\t");
					}
					if(strlen($HANDLE_CELL_ROW) > 0)
					{
						$i->vars(array(
							"row" => $row,
							"col" => $col,
							"HANDLE_CELL_ROW" => $HANDLE_CELL_ROW,
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

	protected function run_payment_type_evaluation_code($args)
	{
		$f = create_function('$args', $this->prop("code"));
		return $f($args);
	}
}

?>
