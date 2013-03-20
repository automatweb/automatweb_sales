<?php

class crm_company_webview_obj extends _int_object
{
	const CLID = 1013;
	
	const TYPE_COMPANY_SELECTED = 1;
	const TYPE_COMPANY_FROM_URL = 2;
	
	public function awobj_get_type()
	{
		$type = parent::prop("type");
		if (!in_array($type, array(self::TYPE_COMPANY_SELECTED, self::TYPE_COMPANY_FROM_URL)))
		{
			$type = self::TYPE_COMPANY_SELECTED;
		}
		return $type;
	}
}

/** Generic crm_company_webview_obj exception **/
class awex_crm_company_webview_obj extends awex_obj {}
