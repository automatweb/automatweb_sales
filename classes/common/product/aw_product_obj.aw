<?php

class aw_product_obj extends _int_object implements crm_sales_price_component_interface, crm_offer_row_interface
{
	const AW_CLID = 1711;

	//	Written solely for testing purposes!
	public function get_units()
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
		));
		return $ol;
	}
}

?>
