<?php

namespace automatweb;

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
	function get_customer_relation($my_co = null, $crea_if_not_exists = false);

	/** returns customer relation creator
		@attrib api=1
		@returns string
	**/
	function get_cust_rel_creator_name();

	/** Returns default address as a string
		@attrib api=1
	**/
	function get_address_string();

	/** Returns customer's all phone numbers as array
		@attrib api=1 params=pos
	**/
	function get_phones();
}

?>
