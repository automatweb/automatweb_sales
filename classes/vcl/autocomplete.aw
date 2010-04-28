<?php

namespace automatweb;

class autocomplete
{
	function autocomplete()
	{
	}

	function get_ac_params($arr)
	{
		foreach($arr as $k => $v)
		{
			$arr[$k] = iconv("UTF-8", aw_global_get("charset"), $v);
		}
		return $arr;
	}

	function finish_ac($autocomplete_options)
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = get_instance("protocols/data/json");

		$errorstring = "";
		$error = false;

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		foreach($autocomplete_options as $k => $v)
		{
			$autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
		}
		exit ($cl_json->encode($option_data));
	}
}
?>
