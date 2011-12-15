<?php

class shop_purveyors_webview_obj extends _int_object
{
	const CLID = 1801;

	public function awobj_get_template()
	{
		$tpl = parent::prop("template");
		return $tpl ? $tpl : "show.tpl";
	}

	public function get_purveyors()
	{
		$categories = $this->prop("categories");
		//	TODO: This should probably ask the purveyors from shop_warehouse_obj or smth. -kaarel
		return count($categories) > 0 ? new object_list(array(
			"class_id" => crm_company_obj::CLID,
			"CL_CRM_COMPANY.RELTYPE_PURVEYOR(CL_SHOP_PRODUCT_CATEGORY).id" => $categories
		)) : new object_list();
	}
}

/** Generic shop_purveyors_webview_obj exception **/
class awex_shop_purveyors_webview_obj extends awex_obj {}
