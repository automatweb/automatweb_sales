<?php

class shop_product_popup_search extends popup_search
{
	function _insert_form_props($htmlc, $arr)
	{
		parent::_insert_form_props($htmlc, $arr);
		$htmlc->add_property(array(
			"name" => "s[code]",
			"type" => "textbox",
			"value" => empty($arr["s"]["code"]) ? "" : $arr["s"]["code"],
			"caption" => t("Kood")
		));
	}

	function _get_filter_props(&$filter, $arr)
	{
		parent::_get_filter_props($filter, $arr);

		if (!empty($arr["s"]["code"]))
		{
			$filter["code"] = "%".$arr["s"]["code"]."%";
		}
	}
}
