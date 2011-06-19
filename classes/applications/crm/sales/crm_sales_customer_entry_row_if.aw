<?php

/*
@classinfo relationmgr=yes no_status=1 prop_cb=1
@datasource id clid=CL_CRM_COMPANY_CUSTOMER_DATA
@datasource customer clid=CL_CRM_COMPANY
@datasource contact_ceo clid=CL_CRM_PERSON
@datasource contact_other clid=CL_CRM_PERSON

@default group=general
@default captionside=top
	@layout main_container type=hbox border=1
		@layout general_data type=vbox parent=main_container
			@layout general_data_first_row type=hbox parent=general_data
			@layout general_data_second_row type=hbox parent=general_data
		@layout contact_data type=vbox parent=main_container
			@layout contact_data_first_row type=hbox parent=contact_data
			@layout contact_data_second_row type=hbox parent=contact_data


	@default parent=general_data_first_row
		@property name type=textbox size=12 ds=customer::name
		@caption Ettev&otilde;tte nimi

		@property form type=select ds=customer::ettevotlusvorm
		@caption Vorm

		@property email type=textbox size=20 ds=customer::fake_email
		@caption E-post

		@property comment type=textbox size=20 ds=id::comment
		@caption Kommentaar


	@default parent=contact_data_first_row
		@property contact_ceo_firstname type=textbox size=10 ds=contact_ceo::name
		@caption Kontakt 1 - Eesnimi

		@property contact_ceo_lastname type=textbox size=10 ds=contact_ceo::name
		@caption Perenimi

		@property contact_ceo_profession type=textbox size=10 ds=contact_ceo::name
		@caption Amet

		@property contact_ceo_email type=textbox size=16 ds=contact_ceo::name
		@caption E-post

		@property contact_ceo_phone type=textbox size=10 ds=contact_ceo::name
		@caption Telefon


	@default parent=general_data_second_row
		@property reg_nr type=textbox size=12 ds=customer::reg_nr
		@caption Reg. nr.

		@property phone type=textbox size=12 ds=customer::fake_phone
		@caption Telefon

		@property salesperson type=textbox size=12 ds=id::salesman
		@caption M&uuml;&uuml;giesindaja


	@default parent=contact_data_second_row
		@property contact_other_firstname type=textbox size=10 ds=contact_other::name
		@caption Kontakt 2 - Eesnimi

		@property contact_other_lastname type=textbox size=10 ds=contact_other::name
		@caption Perenimi

		@property contact_other_profession type=textbox size=10 ds=contact_other::name
		@caption Amet

		@property contact_other_email type=textbox size=16 ds=contact_other::name
		@caption E-post

		@property contact_other_phone type=textbox size=10 ds=contact_other::name
		@caption Telefon







Kontakt 1 (juht) Nimi: 	Amet: 	E-post: 	Tel:
Kontakt 2 Nimi: 	Amet: 	E-post: 	Tel:

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
		$r = class_base::PROP_OK;
		// $arr["prop"]["value"] = is_oid($arr["prop"]["value"]) ? obj($arr["prop"]["value"])->name() : "";
		return $r;
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

	function _get_form(&$arr)
	{
		$r = class_base::PROP_OK;
		$arr["prop"]["options"] = html::get_empty_option() + crm_company_obj::get_company_forms("array_abbreviations");
		return $r;
	}
}
