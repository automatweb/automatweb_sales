<?php

class crm_sales_settings_offers_view
{
	protected static function define_cfgf_offers_price_components_tbl_header(&$arr)
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
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "value",
			"caption" => t("Summa v&otilde;i protsent"),
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "category",
			"caption" => t("Kategooria"),
			"sortable" => true
		));
		$t->define_field(array(
			"name" => "show_in_statistics",
			"caption" => t("Kuva eraldi tulbana '&Uuml;levaated' kaardil"),
			"align" => "center",
			"callback" => array("crm_sales_settings_offers_view", "callback_cfgf_offers_price_components_table_show_in_statistics"),
			"callb_pass_row" => true,
			"sortable" => true
		));
	}

	public static function _get_cfgf_offers_price_components_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$price_component_inst = new crm_sales_price_component();
		$price_component_types = $price_component_inst->type_options;
		$not_available_str = html::italic(t("M&auml;&auml;ramata"));

		self::define_cfgf_offers_price_components_tbl_header($arr);
		$price_components = automatweb::$request->get_application()->get_price_component_list();
		$categories = automatweb::$request->get_application()->get_price_component_category_list()->names();
		$show_in_statistics = $arr["obj_inst"]->get_price_components_and_categories_shown_in_statistics();
		foreach($price_components->arr() as $price_component)
		{
			$t->define_data(array(
				"oid" => $price_component->id(),
				"name" => html::obj_change_url($price_component),
				"type" => $price_component_types[$price_component->prop("type")],
				"value" => $price_component->prop_str("value"),
				"category" => isset($categories[$price_component->prop("category")]) ? $categories[$price_component->prop("category")] : $not_available_str,
				"show_in_statistics" => in_array($price_component->id(), $show_in_statistics),
			));
		}

		return PROP_OK;
	}

	protected static function define_cfgf_offers_price_component_categories_tbl_header(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Hinnakomponendide kategooriad"));

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
			"callback" => array("crm_sales_settings_offers_view", "callback_cfgf_offers_price_component_categories_table_show_in_statistics"),
			"callb_pass_row" => true,
			"sortable" => true
		));
	}

	public static function callback_cfgf_offers_price_component_categories_table_show_in_statistics($row)
	{
		return html::checkbox(array(
			"name" => "offers_price_component_categories[{$row["oid"]}][show_in_statistics]",
			"checked" => $row["show_in_statistics"],
		));
	}

	public static function callback_cfgf_offers_price_components_table_show_in_statistics($row)
	{
		return self::callback_cfgf_offers_price_component_categories_table_show_in_statistics($row);
	}

	public static function _get_cfgf_offers_price_component_categories_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		self::define_cfgf_offers_price_component_categories_tbl_header($arr);

		$show_in_statistics = $arr["obj_inst"]->get_price_components_and_categories_shown_in_statistics();
		$categories = automatweb::$request->get_application()->get_price_component_category_list();
		foreach($categories->arr() as $category)
		{
			$t->define_data(array(
				"oid" => $category->id(),
				"name" => html::obj_change_url($category),
				"show_in_statistics" => in_array($category->id(), $show_in_statistics),
			));
		}

		return PROP_OK;
	}

	public static function _set_cfgf_offers_price_component_categories_table(&$arr)
	{
		$show_in_statistics = array();
		$data = automatweb::$request->arg("offers_price_component_categories");
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

	public static function _get_cfgf_offers_toolbar(&$arr)
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
