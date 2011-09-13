<?php

interface crm_customer_interface
{
	/** returns customer relation object
		@attrib api=1 params=pos
		@param my_co type=CL_CRM_COMPANY default=null
			By default current company used
		@param crea_if_not_exists type=bool default=false
			if no customer relation object, make one
		@returns CL_CRM_COMPANY_CUSTOMER_DATA
	**/
	public function find_customer_relation($my_co = null, $crea_if_not_exists = false);

	/** returns customer relation creator
		@attrib api=1
		@returns string
	**/
	public function get_cust_rel_creator_name();

	/** Returns default address as a string
		@attrib api=1
	**/
	public function get_address_string();

	/** Returns customer's all phone numbers as array
		@attrib api=1 params=pos
	**/
	public function get_phones();

	/**
		@attrib api=1 params=pos
		@param type type=string default="" set=""|"all"|"invoice"|"general"
		@returns object_list(CL_ML_MEMBER)
		@errors
			throws awex_obj_state_new
	**/
	public function get_email_addresses($type = "");

	/**
		@attrib api=1 params=pos
		@returns CL_ML_MEMBER|NULL
		@errors
			throws awex_obj_state_new
	**/
	public function get_email_address();
}
