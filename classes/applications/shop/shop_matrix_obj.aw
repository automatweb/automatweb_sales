<?php

class shop_matrix_obj extends _int_object
{
	public static function get_matrix_structure($o)
	{
		static $retval;
		if(!isset($retval[$o->id()]))
		{
			$matrix = array(
				"rows" => array(
					"products" => array(),
				),
				"cols" => array(
					"customers" => array(),
					"locations" => array(),
				),
				"ids" => array(),
				"names" => array(),
				"priorities" => array(),
			);
			$company_inst = new crm_company_obj;
			$product_category_inst = new shop_product_category_obj;
			$admin_struct_inst = new country_administrative_structure_object;

			foreach(safe_array($o->prop("matrix_customer_categories")) as $id)
			{
				$matrix["cols"]["customers"][$id] = $company_inst->get_customer_categories_hierarchy($id);
			}

			foreach(safe_array($o->prop("matrix_countries")) as $id)
			{
				$matrix["cols"]["locations"][$id] = $admin_struct_inst->prop(array(
					"prop" => "units_by_country",
					"country" => $id,
				))->ids_hierarchy();
			}

			foreach(safe_array($o->prop("matrix_product_categories")) as $id)
			{
				$matrix["rows"]["products"][$id] = $product_category_inst->get_categories_hierarchy($id);
			}

			$matrix["ids"]["customers"] = self::get_matrix_ids($matrix["cols"]["customers"]);
			$matrix["ids"]["locations"] = self::get_matrix_ids($matrix["cols"]["locations"]);
			$matrix["ids"]["products"] = self::get_matrix_ids($matrix["rows"]["products"]);

			$matrix_rows = safe_array($o->prop("matrix_rows"));
			if(in_array(1, $matrix_rows))
			{
				foreach($product_category_inst->get_products($matrix["ids"]["products"]) as $cat => $ol)
				{
					foreach($ol->ids() as $id)
					{
						self::get_matrix_structure_add_child($id, $cat, $matrix["rows"]["products"]);
						$matrix["ids"]["products"][] = $id;
					}
				}
			}
			if(in_array(2, $matrix_rows))
			{
				foreach(shop_product_obj::get_packagings_for_id($matrix["ids"]["products"]) as $product => $ol)
				{
					foreach($ol->ids() as $id)
					{
						self::get_matrix_structure_add_child($id, $product, $matrix["rows"]["products"]);
						$matrix["ids"]["products"][] = $id;
					}
				}
			}

			if(count($ids = array_merge($matrix["ids"]["customers"], $matrix["ids"]["products"], $matrix["ids"]["locations"])) > 0)
			{
				// Names, clids
				$odl = new object_data_list(
					array(
						"oid" => $ids,
						"lang_id" => array(),
						"site_id" => array(),
					),
					array(
						CL_SHOP_PRODUCT => array("class_id", "name"),
					)
				);
				$matrix["names"] = $odl->get_element_from_all("name");
				$matrix["clids"] = $odl->get_element_from_all("class_id");

				// Priorities
				foreach(connection::find(array("from.class_id" => $o->class_id(), "from" => $o->id(), "to" => $ids, "type" => "RELTYPE_PRIORITY")) as $conn)
				{
					$matrix["priorities"][$conn["to"]] = aw_math_calc::string2float($conn["data"]);
				}
			}

			// Sort rows, cols
			if(count($matrix["priorities"]) > 0)
			{
				$matrix["cols"]["locations"] = self::matrix_sort_lvl($matrix["cols"]["locations"], $matrix["priorities"]);
				$matrix["cols"]["customers"] = self::matrix_sort_lvl($matrix["cols"]["customers"], $matrix["priorities"]);
				$matrix["rows"]["products"] = self::matrix_sort_lvl($matrix["rows"]["products"], $matrix["priorities"]);
			}

			self::get_matrix_structure_parents($matrix["rows"]["products"] + $matrix["cols"]["customers"] + $matrix["cols"]["locations"] + $matrix["rows"]["products"], $matrix["parents"]);

			$retval[$o->id()] = $matrix;
		}

		return $retval[$o->id()];
	}

	protected static function get_matrix_structure_add_child($id, $parent, &$recursive_array)
	{
		foreach($recursive_array as $key => $sub_arrays)
		{
			if($key == $parent)
			{
				$recursive_array[$key][$id] = array();
			}
			self::get_matrix_structure_add_child($id, $parent, $recursive_array[$key]);
		}
	}

	protected static function get_matrix_structure_parents($data, &$retval, $parents = array())
	{
		foreach($data as $k => $v)
		{
			$retval[$k] = array_merge(safe_array(ifset($retval, $k)), $parents);
			self::get_matrix_structure_parents($v, $retval, $parents + array($k => $k));
		}
	}

	protected static function matrix_sort_lvl($data, $priorities)
	{
		$_priorities = array();
		foreach($priorities as $k => $v)
		{
			$_priorities[] = "'$k' => '$v'";
		}

		$cmp = create_function('$a, $b', '$p = array('.implode(",", $_priorities).'); return (float)ifset($p, $b) - (float)ifset($p, $a);');
		uksort($data, $cmp);
		foreach($data as $k => $v)
		{
			$data[$k] = self::matrix_sort_lvl($v, $priorities);
		}
		return $data;
	}

	protected static function get_matrix_ids($d)
	{
		$r = array();
		foreach($d as $k => $v)
		{
			$r[$k] = $k;
			$r = array_merge($r, self::get_matrix_ids($v));
		}
		return $r;
	}

	public function prioritize()
	{
		$this->matrix_structure = $this->get_matrix_structure($this);
		foreach($this->matrix_structure["cols"]["customers"] as $id => $children)
		{
			$this->prioritize_level($id, $children);
		}
		foreach($this->matrix_structure["cols"]["locations"] as $id => $children)
		{
			$this->prioritize_level($id, $children);
		}
		foreach($this->matrix_structure["rows"]["products"] as $id => $children)
		{
			$this->prioritize_level($id, $children);
		}
	}

	protected function prioritize_level($id, $children, $parent_priority = 0)
	{
		if(empty($this->matrix_structure["priorities"][$id]))
		{
			$this->matrix_structure["priorities"][$id] = $parent_priority + 1000;
			$this->connect(array(
				"to" => $id,
				"type" => "RELTYPE_PRIORITY",
				"data" => $this->matrix_structure["priorities"][$id],
			));
		}

		foreach($children as $_id => $_children)
		{
			$this->prioritize_level($_id, $_children, $this->matrix_structure["priorities"][$id]);
		}
	}

	public function update_code()
	{
		$this->prioritize();
	}

	protected function update_code_add_cell($row, $col, $parent = array(), $subrows = array(), $subcols = array())
	{
		$this->cells[$row][$col] = array(
			"row" => $row,
			"col" => $col,
			"parent" => $parent,
		);
		foreach($subcols as $_col => $_subcols)
		{
			$this->update_code_add_cell($row, $_col, array("row" => $row, "col" => $col), array(), $_subcols);
		}
		foreach($subrows as $_row => $_subrows)
		{
			$this->update_code_add_cell($_row, $col, array("row" => $row, "col" => $col), $_subrows, array());
		}
	}
}
