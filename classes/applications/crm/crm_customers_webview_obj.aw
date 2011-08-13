<?php

class crm_customers_webview_obj extends _int_object
{
	const CLID = 1799;

	const MODE_USER_COMPANY_CUSTOMERS = 0;

	/**
		NONE                           : 0
		CL_CRM_COMPANY                 : 1
		CL_CRM_PERSON                  : 2
		CL_CRM_COMPANY + CL_CRM_PERSON : 3
	**/
	public function awobj_get_clids()
	{
		$clids = array();

		$code = parent::prop("clids");
		if ($code % 2 === 1)
		{
			$clids[crm_company_obj::CLID] = crm_company_obj::CLID;
		}
		if ($code > 1)
		{
			$clids[crm_person_obj::CLID] = crm_person_obj::CLID;
		}

		return $clids;
	}

	/**
		NONE                           : 0
		CL_CRM_COMPANY                 : 1
		CL_CRM_PERSON                  : 2
		CL_CRM_COMPANY + CL_CRM_PERSON : 3
	**/
	public function awobj_set_clids($clids)
	{
		$code = 0;

		if (is_array($clids))
		{
			if (in_array(crm_company_obj::CLID, $clids))
			{
				$code += 1;
			}
			if (in_array(crm_person_obj::CLID, $clids))
			{
				$code += 2;
			}
		}

		return parent::set_prop("clids", $code);
	}

	/**	Returns object_list of customers, according to the mode of the webview object.
		@attrib api=1
		@returns object_list
	**/
	public function get_customers()
	{
		switch ($this->prop("mode"))
		{
			case self::MODE_USER_COMPANY_CUSTOMERS:
				$current_company_id = user::get_current_company();
				if (!is_oid($current_company_id))
				{
					return new object_list();
				}
				$current_company = obj($current_company_id, array(), crm_company_obj::CLID);
				return $current_company->get_customers_by_customer_data_objs($this->awobj_get_clids());

			default: 
				return new object_list();
		}
	}

}

/** Generic crm_customers_webview_obj exception **/
class awex_crm_customers_webview_obj extends awex_obj {}
