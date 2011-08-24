<?php

class shop_purveyors_webview_obj extends _int_object
{
	const CLID = 1801;

	public function awobj_get_template()
	{
		$tpl = parent::prop("template");
		return $tpl ? $tpl : "show.tpl";
	}
}

/** Generic shop_purveyors_webview_obj exception **/
class awex_shop_purveyors_webview_obj extends awex_obj {}
