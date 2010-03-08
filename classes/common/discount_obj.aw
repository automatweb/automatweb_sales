<?php

class discount_obj extends _int_object
{

	public static function get_valid_discount($arr)
	{
		if(!is_oid($arr["object"]))
		{
			return 0;
		}
		$filter = array(
			"class_id" => array(CL_DISCOUNT),
			"site_id" => array(),
			"lang_id" => array(),
			"CL_DISCOUNT.object" => $arr["object"],
		);
		
		$ol = new object_list($filter);
		return $ol;
	}

	public static function get_valid_discount_coefficient($arr)
	{
		if(!is_oid($arr["object"]))
		{
			return 0;
		}
		$coefficient = 0;	
		$filter = array(
			"class_id" => array(CL_DISCOUNT),
			"site_id" => array(),
			"lang_id" => array(),
			"CL_DISCOUNT.object" => $arr["object"],
		);

		$ol = new object_list($filter);

		foreach($ol->arr() as $o)
		{
			$coefficient = $o->prop("discount") / 100.0;
		}
		return $coefficient;
	}
}

?>
