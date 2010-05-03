<?php

class crm_sales_settings_offers_view
{
	protected static function define_cfgf_offers_price_components_tbl_header(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Pakkumistes kasutatavad hinnakomponendid"));

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
	}

	public static function _get_cfgf_offers_price_components_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$price_component_inst = new crm_sales_price_component();
		$price_component_types = $price_component_inst->type_options;

		self::define_cfgf_offers_price_components_tbl_header($arr);
		$price_components = automatweb::$request->get_application()->get_price_component_list();
		foreach($price_components->arr() as $price_component)
		{
			$t->define_data(array(
				"oid" => $price_component->id(),
				"name" => html::obj_change_url($price_component),
				"type" => $price_component_types[$price_component->prop("type")],
				"value" => $price_component->prop_str("value")
			));
		}

		return PROP_OK;
	}

	public static function _get_cfgf_offers_toolbar(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$t = $arr["prop"]["vcl_inst"];

		if ($this_o->is_saved() && is_oid($this_o->prop("price_components_folder")))
		{
			$t->add_new_button(array(CL_CRM_SALES_PRICE_COMPONENT), $this_o->prop("price_components_folder"), NULL, array("application" => $this_o->id()));
		}
		$t->add_delete_button();

		return PROP_OK;
	}
}

?>
