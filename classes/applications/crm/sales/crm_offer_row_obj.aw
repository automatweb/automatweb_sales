<?php

class crm_offer_row_obj extends crm_offer_price_component_handler
{
	/**	Returns an array of applicable class IDs
		@attrib api=1
		@returns int[]
	**/
	public static function get_applicable_clids()
	{
		$possible_classes = class_index::get_classes_by_interface("crm_offer_row_interface");
		$possible_clids = array();
		foreach($possible_classes as $possible_class)
		{
			$constant_name = $possible_class."::AW_CLID";
			if (defined($constant_name))
			{
				$possible_clids[] = constant($constant_name);
			}
		}

		return $possible_clids;
	}
}

?>
