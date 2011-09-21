<?php

class crm_sales_offers_templates_view
{
	public static function _get_offer_templates_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(crm_offer_template_obj::CLID), $arr["obj_inst"]->prop("offers_folder"));
		$t->add_delete_button();

		return PROP_OK;
	}

	public static function _get_offer_templates_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->set_caption(t("Pakkumuste &scaron;abloonid"));

		$t->define_chooser();
		$t->add_fields(array(
			"name" => t("Nimi"),
			"sum" => t("Summa"),
		));

		$templates = $arr["obj_inst"]->get_offer_templates();

		if ($templates->count() > 0)
		{
			$template = $templates->begin();
			do
			{
				$t->define_data(array(
					"oid" => $template->id(),
					"name" => html::obj_change_url($template),
					"sum" => $template->sum_with_currency(),
				));
			}
			while ($template = $templates->next());
		}

		return PROP_OK;
	}
}