<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@datasource id clid=CL_CRM_COMPANY_CUSTOMER_DATA
@datasource customer clid=CL_CRM_COMPANY
@datasource contact_ceo clid=CL_CRM_PERSON
@datasource contact_other clid=CL_CRM_PERSON

@default group=general
	@property name type=textbox size=20 ds=customer::name
	@caption Nimi

	@property phone type=textbox size=12 ds=customer::phone_id

*/

class crm_sales_customer_entry_row_if extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_sales_customer_entry_row_if",
			"clid" => CL_CRM_SALES_CUSTOMER_ENTRY_ROW_IF
		));
	}

	function _get_phone(&$arr)
	{
		$arr["prop"]["value"] = is_oid($arr["prop"]["value"]) ? obj($arr["prop"]["value"])->name() : "";
	}

	function _awcb_getds_customer($arr)
	{
		$customer = obj($arr["obj_inst"]->prop("buyer"), array(), CL_CRM_COMPANY);
		return $customer;
	}

	function _awcb_getds_contact_ceo($arr)
	{
		$contact_ceo = obj($arr["obj_inst"]->prop("buyer_contact_person"), array(), CL_CRM_PERSON);
		return $contact_ceo;
	}

	function _awcb_getds_contact_other($arr)
	{
		$contact_other = obj($arr["obj_inst"]->prop("buyer_contact_person2"), array(), CL_CRM_PERSON);
		return $contact_other;
	}
}
