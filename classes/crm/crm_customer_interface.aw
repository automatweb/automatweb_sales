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

	/** Returns default address as an object
		@attrib api=1
	**/
	public function get_address();

	/** Returns all addresses as an object list
		@attrib api=1
		@returns object_list(CL_ADDRESS)
	**/
	public function get_addresses();

	/** Returns customer's all phone number objects in an object_list
		@attrib api=1 params=pos
		@returns object_list(CL_CRM_PHONE)
	**/
	public function get_phones();
	
	/** Returns a single customer's phone number
		@attrib api=1 params=pos
		@param type type=int optional
			phone number type, possible options: phone_obj::TYPE_WORK, phone_obj::TYPE_HOME, phone_obj::TYPE_SHORT, phone_obj::TYPE_MOBILE, phone_obj::TYPE_FAX, phone_obj::TYPE_SKYPE, phone_obj::TYPE_INTERCOM
		@errors
			throws awex_obj_state_new
	**/
	public function get_phone_number($type = null);
	
	/**
		@attrib api=1 params=pos
		@param type type=int required
			The phone number to be added
		@returns CL_CRM_PHONE
		@errors
			throws awex_obj_state_new
	**/
	public function add_phone_number($number);

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
	
	/**
		@attrib api=1 params=pos
		@param type type=int required
			The e-mail address to be added. Must be a valid e-mail address.
		@returns CL_ML_MEMBER
		@errors
			throws awex_obj_state_new
	**/
	public function add_email_address($address);
}
