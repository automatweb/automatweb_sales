<?php
class shop_matrix extends class_base
{
	public function callback_pre_edit($arr)
	{
		$this->obj = $arr["obj_inst"];
		$this->matrix_structure = $arr["obj_inst"]->get_matrix_structure($arr["obj_inst"]);
		$this->col_types = array(
			"locations" => t("Asukohad"),
			"customers" => t("Kliendikategooriad"),
		);
		if(!is_oid($this->currency = automatweb::$request->arg("currency")) || !$this->can("view", $this->currency))
		{
			$ol = new object_list(array(
				"class_id" => CL_CURRENCY,
				"lang_id" => array(),
				"site_id" => array(),
				new obj_predicate_sort(array(
					"name" => "ASC",
				)),
				"limit" => 1,
			));
			$ids = $ol->ids();
			$this->currency = reset($ids);
		}
	}

	public function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["currency"] = $this->currency;
	}

	public function callback_mod_retval($arr)
	{
		if(isset($arr["request"]["currency"]))
		{
			$arr["args"]["currency"] = $arr["request"]["currency"];
		}
	}

	public function _get_matrix_col_order($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "order",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
			"sorting_field" => "order_num",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Veeru grupp"),
		));

		$data = $arr["obj_inst"]->meta("matrix_col_order");

		foreach(array_keys($this->matrix_structure["cols"]) as $col)
		{
			$t->define_data(array(
				"order" => html::textbox(array(
					"name" => "matrix_col_order[$col]",
					"value" => ifset($data, $col),
					"size" => 5,
				)),
				"order_num" => ifset($data, $col),
				"name" => $this->col_types[$col],
			));
		}

		$t->set_default_sortby("order");
	}

	public function _set_matrix_col_order($arr)
	{
		asort($arr["prop"]["value"]);
		$arr["obj_inst"]->set_meta("matrix_col_order", $arr["prop"]["value"]);
		if(is_callable(array($arr["obj_inst"], "update_code")))
		{
			$arr["obj_inst"]->update_code();
		}
	}

	public function _get_matrix_rows($arr)
	{
		$arr["prop"]["options"] = array(
			0 => t("Tootegrupid"),
			1 => t("Tooted"),
			2 => t("Pakendid"),
		);
	}

	public function _get_matrix_tlb($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CURRENCY,
			"lang_id" => array(),
			"site_id" => array(),
			new obj_predicate_sort(array(
				"name" => "ASC",
			)),
		));


		$arr["prop"]["vcl_inst"]->add_menu_button(array(
			"name" => "currency",
			"text" => sprintf(t("Vali valuuta, mille maatriksit kuvada (valitud %s)"), obj($this->currency)->name()),
		));
		foreach($ol->names() as $oid => $name)
		{
			$arr["prop"]["vcl_inst"]->add_menu_item(array(
				"parent" => "currency",
				"name" => "currency_".$oid,
				"text" => $oid == $this->currency ? html::bold($name) : $name,
				"title" => $oid == $this->currency ? html::bold($name) : $name,
				"reload" => array(),
				"url" => aw_url_change_var("currency", $oid),
			));
		}
	}

	public static function _init_priorities_tbl($t, $caption)
	{
		$t->define_chooser(array(
			"width" => "75",
		));
		$t->define_field(array(
			"name" => "priority",
			"caption" => t("Prioriteet"),
			"align" => "center",
			"width" => "75",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => $caption,
		));
		$t->set_sortable(false);
	}

	public function _get_priorities_locations_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_priorities_tbl($t, t("Asukoht"));

		$this->priorities_tbl_insert_row($t, "priorities_locations_tbl", 0, $this->matrix_structure["cols"]["locations"]);
	}

	public function _get_priorities_product_categories_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_priorities_tbl($t, t("Tootegrupp"));

		$this->priorities_tbl_insert_row($t, "priorities_product_categories_tbl", 0, $this->matrix_structure["rows"]["products"]);
	}

	public function _get_priorities_customer_categories_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_priorities_tbl($t, t("Kliendigrupp"));

		$this->priorities_tbl_insert_row($t, "priorities_customer_categories_tbl", 0, $this->matrix_structure["cols"]["customers"]);
	}

	public function _set_priorities_customer_categories_tbl($arr)
	{
		return $this->_set_priorities_tbl($arr, CL_CRM_CATEGORY);
	}

	public function _set_priorities_locations_tbl($arr)
	{
		return $this->_set_priorities_tbl($arr, array(CL_COUNTRY_CITY, CL_COUNTRY_CITYDISTRICT, CL_COUNTRY_ADMINISTRATIVE_UNIT));
	}

	public function _set_priorities_product_categories_tbl($arr)
	{
		return $this->_set_priorities_tbl($arr, CL_SHOP_PRODUCT_CATEGORY);
	}

	public function _set_priorities_tbl($arr, $clid)
	{
		$data = $arr["prop"]["value"];
		foreach($arr["obj_inst"]->connections_from(array("to.class_id" => $clid, "type" => "RELTYPE_PRIORITY")) as $conn)
		{
			if(!empty($data[$conn->prop("to")]) && $data[$conn->prop("to")] != $conn->prop("data"))
			{
				$conn->change(array(
					"data" => $data[$conn->prop("to")],
				));
			}
			elseif(empty($data[$conn->prop("to")]))
			{
				$conn->delete();
			}
			unset($data[$conn->prop("to")]);
		}
		foreach($data as $k => $v)
		{
			if($v != 0)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $k,
					"type" => "RELTYPE_PRIORITY",
					"data" => $v,
				));
			}
		}
		if(is_callable(array($arr["obj_inst"], "update_code")))
		{
			$arr["obj_inst"]->update_code();
		}
	}

	protected function priorities_tbl_insert_row($t, $name, $depth, $data)
	{
		foreach($data as $key => $subdata)
		{
			$t->define_data(array(
				"oid" => $key,
				"priority" => html::textbox(array(
					"name" => $name."[".$key."]",
					"value" => aw_math_calc::string2float(ifset($this->matrix_structure["priorities"], $key)),
					"size" => 4,
				)),
				"name" => str_repeat("&nbsp; ", $depth * 5).parse_obj_name($this->matrix_structure["names"][$key]),
			));
			$this->priorities_tbl_insert_row($t, $name, $depth +1, $subdata);
		}
	}

	public function _get_code($arr)
	{
		return PROP_IGNORE;
	}

	public function _set_code($arr)
	{
		return PROP_IGNORE;
	}

	public function callback_generate_scripts($arr)
	{
		if(automatweb::$request->arg("group") === "matrix")
		{
			return "
jQuery(document).ready(function(){
	$('[name^=matrix]').change(function(){
		var val = $(this).val();
		$('[name=\"'+$(this).attr('name')+'\"]').each(function(){
			if($(this).val() != val){
				$(this).val(val);
			}
		});
	});
});
			";
		}
	}

	/**
		@attrib params=name api=1

		@param table_inst required type=object
		@param obj_inst required type=object
		@param column_types required type=array
		@param matrix_data required type=array
		@param field_callback optional type=callback
		@param data_cell_callback optional type=callback
		@param data_callback optional type=callback
	**/
	public static function draw_matrix($arr)
	{
		$arr["matrix"] = shop_price_list_obj::get_matrix_structure($arr["obj_inst"]);

		$t = &$arr["table_inst"];
		$t->set_sortable(false);

		// Otherwise the matrix goes too MAD! -kaarel 13.07.2009
		$depth = 3;

		### COLS

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Toode/tootekategooria"),
		));
		$t->define_field(array(
			"name" => "default",
			"caption" => t("*"),
			"tooltip" => t("Vaikimisi"),
			"align" => "center",
		));
		if(!empty($arr["field_callback"]) && is_callable($arr["field_callback"]))
		{
			$arr["field_callback"][0]->$arr["field_callback"][1](&$t, "default");
		}

		foreach($arr["column_types"] as $name => $caption)
		{
			if(!empty($arr["matrix"]["cols"][$name]))
			{
				$t->define_field(array(
					"name" => $name,
					"caption" => $caption,
					"align" => "center",
				));
			}
		}

		$oids = array();
		foreach($arr["column_types"] as $name => $caption)
		{
			foreach(safe_array($arr["matrix"]["cols"][$name]) as $id => $children)
			{
				self::draw_matrix_add_col(&$t, &$oids, $name, $id, $children, ifset($arr, "field_callback"), array(
					"sufix" => "_self",
					"caption" => t("*"),
					"tooltip" => t("K&otilde;ik \"%s\" kliendid"),
					"align" => "center",
				));
			}
		}

		// Assign col captions
		if(count($oids) > 0)
		{
			$ol = new object_list(array(
				"oid" => $oids,
				"lang_id" => array(),
				"site_id" => array(),
			));
			$names = $ol->names();

			foreach($t->get_defined_fields() as $v)
			{
				$id = substr($v["name"], strrpos($v["name"], "_") +1);
				if(isset($names[$id]))
				{
					$v["caption"] = $names[$id];
					$t->update_field($v);
				}
			}
		}

		#### ROWS
		$arr["matrix_data"] = safe_array($arr["matrix_data"]);
		$arr["matrix"]["names"]["default"] = t("*");
		self::draw_matrix_add_row($t, array("default" => array()), $arr, 0);
		self::draw_matrix_add_row($t, safe_array($arr["matrix"]["rows"]["products"]), $arr, 0);
	}

	protected static function draw_matrix_add_row($t, $rows, $arr, $depth)
	{
		foreach($rows as $row => $subrows)
		{
			$data = array();
			foreach($t->get_defined_fields() as $field)
			{
				$data[$field["name"]] = is_callable($arr["data_cell_callback"]) ? $arr["data_cell_callback"][0]->$arr["data_cell_callback"][1]($row, $field, $arr["matrix_data"]) : "";
			}
			$data["name"] = str_repeat("&nbsp; ", $depth * 2).(isset($arr["matrix"]["clids"][$row]) ? html::img(array(
				"url" => icons::get_icon_url($arr["matrix"]["clids"][$row]),
				"border" => 0
			)) : "")."&nbsp;".parse_obj_name($arr["matrix"]["names"][$row]);
			if(!empty($data_callback) && is_callable($arr["data_callback"]))
			{
				$arr["data_callback"][0]->$arr["data_callback"][1](&$t, &$data);
			}
			$t->define_data($data);
			self::draw_matrix_add_row($t, $subrows, $arr, $depth +1);
		}
	}

	protected static function draw_matrix_add_col($t, $oids, $parent, $id, $children, $callback, $all_field = false)
	{
		$name = $parent."_".$id;
		$t->define_field(array(
			"name" => $name,
			"parent" => $parent,
			"align" => "center",
		));
		$oids[$id] = $id;
		if(count($children))
		{
			if($all_field !== false)
			{
				$all_field["name"] = $name.ifset($all_field, "sufix");
				$all_field["caption"] = sprintf($all_field["caption"], parse_obj_name(obj($id)->name()));
				$all_field["tooltip"] = sprintf($all_field["tooltip"], parse_obj_name(obj($id)->name()));
				$all_field["parent"] = $name;
				$t->define_field($all_field);
				if(is_callable($callback))
				{
					$callback[0]->$callback[1]($t, $name."_self");
				}
			}
			foreach($children as $id => $children)
			{
				self::draw_matrix_add_col(&$t, &$oids, $name, $id, $children, $callback, $all_field);
			}
		}
		elseif(is_callable($callback))
		{
			$callback[0]->$callback[1](&$t, $name);
		}
	}
}
?>