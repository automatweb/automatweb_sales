<?php

namespace automatweb;


class shop_order_center_filter_entry_obj extends _int_object
{
	const AW_CLID = 1412;

	function filter_get_selected_values($field_name)
	{
		$cache = $this->meta("filter_value_cache");
		$v = $cache[$field_name];
		$rv = array();
		foreach($v as $val => $val_capt)
		{
			$rv[$val] = $val_capt;
		}
		return $rv;
	}

	function filter_set_selected_values($field_name, $field_values)
	{
		$cache = $this->meta("filter_value_cache");
		$cache[$field_name] = $field_values;
		$this->set_meta("filter_value_cache", $cache);
	}

	/** Returns user-defined filter column captions
		@attrib api=1 

		@returns
			array { internal_field_name => userdefined field_caption, ... } for all fields
	**/
	function filter_get_user_captions()
	{
		return safe_array($this->meta("user_filter_captions"));
	}

	/** Sets the user-defined filter table captions
		@attrib api=1 params=pos

		@param capt_arr required type=array
			array { internal_field_name => userdefined field name, ... } for all fields
	**/
	function filter_set_user_captions($capt_arr)
	{
		$this->set_meta("user_filter_captions", $capt_arr);
	}
}

?>
