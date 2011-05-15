<?php

class doc_obj extends _int_object implements crm_sales_price_component_interface, crm_offer_row_interface
{
	const CLID = 7;

	//	Written solely for testing purposes!
	public function get_units()
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"status" => object::STAT_ACTIVE,
		));
		return $ol;
	}

	public function is_visible_to()
	{
		//dokumentide mitte n2itamine yleliigsetest riikidest tulevatele p2ringutele
		if(is_oid($this->id()) && strlen($this->prop("show_to_country")) > 1)
		{
			$aproved_countries = explode("," , $this->prop("show_to_country"));
			if(!in_array(detect_country() , $aproved_countries))
			{
				return false;
			}
		}
		return true;
	}
}
