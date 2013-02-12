<?php

class crm_bill_webview_obj extends _int_object
{
	const CLID = 1823;
	
	function get_bills()
	{
		if (!users::is_logged_in())
		{
			return new object_list();
		}
		
		$states = array();
		foreach($this->prop("states_displayed") as $state)
		{
			$states[] = substr($state, 1);
		}
		
		return new object_list(array(
			"class_id" => crm_bill_obj::CLID,
			"customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer" => $this->__get_buyer(),
			"state" => $states,
		));
	}
	
	private function __get_buyer()
	{
		$buyer_type = $this->prop("buyer_type");
		$buyer = array();
		if (!empty($buyer_type[crm_person_obj::CLID]))
		{
			$buyer[] = user::get_current_person();
		}
		if (!empty($buyer_type[crm_company_obj::CLID]))
		{
			$buyer[] = user::get_current_company();
		}
		return $buyer;
	}
	
	function awobj_get_buyer_type()
	{
		$value = parent::prop("buyer_type");
		if(empty($value))
		{
			$value = array(
				crm_person_obj::CLID => true,
				crm_company_obj::CLID => true,
			);
		}
		return $value;
	}
}
