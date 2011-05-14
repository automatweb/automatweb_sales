<?php

class crm_sales_settings_offers_view
{
	public static function _get_hide_mandatory_price_components(&$arr)
	{
		$arr["prop"]["label"] = t("Peida kohustuslikud hinnakomponendid");
		return PROP_OK;
	}

	protected static function define_price_components_tbl_header(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Pakkumustes kasutatavad hinnakomponendid"));

		$t->define_chooser();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
			"callback" => array("crm_sales_settings_offers_view", "callback_price_components_table_type"),
			"callb_pass_row" => true,
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "value",
			"caption" => t("Summa v&otilde;i protsent"),
			"align" => "center",
			"callback" => array("crm_sales_settings_offers_view", "callback_price_components_table_value"),
			"callb_pass_row" => true,
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "category",
			"caption" => t("Kategooria"),
			"align" => "center",
			"callback" => array("crm_sales_settings_offers_view", "callback_price_components_table_category"),
			"callb_pass_row" => true,
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "show_in_statistics",
			"caption" => t("Kuva eraldi tulbana '&Uuml;levaated' kaardil"),
			"align" => "center",
			"callback" => array("crm_sales_settings_offers_view", "callback_price_components_table_show_in_statistics"),
			"callb_pass_row" => true,
			"sortable" => true
		));
	}

	public static function _get_price_components_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$price_component_inst = new crm_sales_price_component();
		$price_component_types = $price_component_inst->type_options;
		$not_available_str = html::italic(t("M&auml;&auml;ramata"));

		self::define_price_components_tbl_header($arr);

		$price_component_predicates = array();
		if(automatweb::$request->arg_isset("crmListCategory"))
		{
			$category = automatweb::$request->arg("crmListCategory");
			// $category is either 'undefined' or 'pc_{$category_oid}'
			$category = "undefined" === $category ? "undefined" : preg_replace("/[^0-9]/", "", $category);
			$price_component_predicates = array("category" => $category);
		}
		$price_components = automatweb::$request->get_application()->get_price_component_list($price_component_predicates);

		$categories = automatweb::$request->get_application()->get_price_component_category_list()->names();
		$show_in_statistics = $arr["obj_inst"]->get_price_components_and_categories_shown_in_statistics();
		foreach($price_components->arr() as $price_component)
		{
			$t->define_data(array(
				"oid" => $price_component->id(),
				"name" => html::obj_change_url($price_component),
				"type" => $price_component->prop("type"),
				"value" => $price_component->prop_str("value"),
				"category" => $price_component->prop("category"),
				"show_in_statistics" => in_array($price_component->id(), $show_in_statistics),
			));
		}

		return PROP_OK;
	}

	public static function callback_price_components_table_type($row)
	{
		static $types;
		
		if(!isset($types))
		{
			$price_component_inst = new crm_sales_price_component();
			$types = $price_component_inst->type_options;
		}

		return html::select(array(
			"name" => "price_components[{$row["oid"]}][type]",
			"options" => $types,
			"value" => $row["type"],
		));
	}

	public static function callback_price_components_table_value($row)
	{
		return html::textbox(array(
			"name" => "price_components[{$row["oid"]}][value]",
			"size" => 10,
			"value" => $row["value"],
		));
	}

	public static function callback_price_components_table_category($row)
	{
		static $options;
		
		if(!isset($options))
		{
			$options = array(t("--Vali--"));
			$price_components = automatweb::$request->get_application()->get_price_component_category_list();
			$options += $price_components->names();
		}

		return html::select(array(
			"name" => "price_components[{$row["oid"]}][category]",
			"options" => $options,
			"value" => $row["category"],
		));
	}

	public static function _set_price_components_table(&$arr)
	{
		$show_in_statistics = array();
		$price_components = automatweb::$request->arg("price_components");
		if(!empty($price_components) && is_array($price_components))
		{
			foreach($price_components as $price_component_id => $price_component_data)
			{
				$price_component = new object($price_component_id, array(), CL_CRM_SALES_PRICE_COMPONENT);
				$price_component->set_prop("type", $price_component_data["type"]);
				$price_component->set_prop("value", $price_component_data["value"]);
				$price_component->set_prop("category", $price_component_data["category"]);
				$price_component->save();
			}
		}

		$arr["obj_inst"]->set_price_components_and_categories_shown_in_statistics($show_in_statistics);
	}

	protected static function define_price_component_categories_tbl_header(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Hinnakomponentide kategooriad"));

		$t->define_chooser();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "show_in_statistics",
			"caption" => t("Kuva eraldi tulbana '&Uuml;levaated' kaardil"),
			"align" => "center",
			"callback" => array("crm_sales_settings_offers_view", "callback_price_component_categories_table_show_in_statistics"),
			"callb_pass_row" => true,
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "edit",
			"caption" => "",
			"align" => "center",
			"callback" => array("crm_sales_settings_offers_view", "callback_price_component_categories_table_edit"),
			"callb_pass_row" => true
		));
	}

	public static function callback_price_component_categories_table_edit($row)
	{
		$menu = new popup_menu();
		$menu->begin_menu("edit_{$row["oid"]}");
		$menu->add_item(array(
				"text" => t("Muuda"),
				"link" => html::get_change_url($row["oid"]),
		));

		return $menu->get_menu();
	}

	public static function callback_price_component_categories_table_show_in_statistics($row)
	{
		return html::checkbox(array(
			"name" => "price_component_categories[{$row["oid"]}][show_in_statistics]",
			"checked" => $row["show_in_statistics"],
		));
	}

	public static function callback_price_components_table_show_in_statistics($row)
	{
		return self::callback_price_component_categories_table_show_in_statistics($row);
	}

	public static function _get_price_component_categories_tree(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		
		$url = automatweb::$request->get_uri();
		$url->unset_arg("crmListCategory");

		$t->add_item(0, array(
			"id" => "all",
			"name" => t("K&otilde;ik hinnakomponendid"),
			"url" => $url->get(),
		));

		$categories = automatweb::$request->get_application()->get_price_component_category_list();
		foreach($categories->arr() as $category)
		{
			$key = "pc_{$category->id()}";
			$url->set_arg("crmListCategory", $key);
			$t->add_item("all", array(
				"id" => $key,
				"name" => $category->name(),
				"url" => $url->get(),
			));
		}
		
		$key = "undefined";
		$url->set_arg("crmListCategory", $key);
		$t->add_item("all", array(
			"id" => $key,
			"name" => html::italic(t("M&auml;&auml;ramata")),
			"url" => $url->get(),
		));

		$t->set_selected_item(crm_sales::$price_components_list_view);

		return PROP_OK;
	}

	public static function _get_price_component_categories_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		self::define_price_component_categories_tbl_header($arr);

		$show_in_statistics = $arr["obj_inst"]->get_price_components_and_categories_shown_in_statistics();
		$categories = automatweb::$request->get_application()->get_price_component_category_list();
		$url = automatweb::$request->get_uri();
		foreach($categories->names() as $category_id => $category_name)
		{
			$url->set_arg("crmListCategory", "pc_{$category_id}");
			$t->define_data(array(
				"oid" => $category_id,
				"name" => html::href(array(
					"caption" => $category_name,
					"url" => $url->get(),
				)),
				"show_in_statistics" => in_array($category_id, $show_in_statistics),
			));
		}

		return PROP_OK;
	}

	public static function _set_price_component_categories_table(&$arr)
	{
		$show_in_statistics = array();
		$data = automatweb::$request->arg("price_component_categories");
		if(!empty($data) && is_array($data))
		{
			foreach($data as $category_id => $row)
			{
				if(!empty($row["show_in_statistics"]))
				{
					$show_in_statistics[] = $category_id;
				}
			}
		}

		$arr["obj_inst"]->set_price_components_and_categories_shown_in_statistics($show_in_statistics);
	}

	public static function _get_price_components_toolbar(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$t = $arr["prop"]["vcl_inst"];
		$add_new_button = false;

		if ($this_o->is_saved() && is_oid($this_o->prop("price_components_folder")))
		{
			$t->add_menu_item(array(
				"parent" => "new",
				"text" => t("Hinnakomponent"),
				"link" => html::get_new_url(CL_CRM_SALES_PRICE_COMPONENT, $this_o->prop("price_components_folder"), array(
					"application" => $this_o->id(),
					"return_url" => get_ru(),
				)),
			));
			$add_new_button = true;
		}
		if ($this_o->is_saved() && is_oid($this_o->prop("price_component_categories_folder")))
		{
			$t->add_menu_item(array(
				"parent" => "new",
				"text" => t("Hinnakomponendi kategooria"),
				"link" => html::get_new_url(CL_CRM_SALES_PRICE_COMPONENT_CATEGORY, $this_o->prop("price_component_categories_folder"), array(
					"application" => $this_o->id(),
					"return_url" => get_ru(),
				)),
			));
			$add_new_button = true;
		}

		if($add_new_button)
		{
			$t->add_menu_button(array(
				"name" => "new",
				"img" => "new.gif",
			));
		}
		$t->add_save_button();
		$t->add_delete_button();

		return PROP_OK;
	}
}

?>
