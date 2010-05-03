<?php

interface crm_offer_row_interface
{
	/** Returns object_list of all applicable units for this object
		@attrib api=1
		@returns object_list
	**/
	public function get_units();
}

?>