<?php

// php.aw - PHP serializer
/*
@classinfo  maintainer=kristo
*/
class php_serializer_file
{
	var $no_index = false;

	function php_serialize($arr)
	{
		if (!is_array($arr))
		{
			return false;
		}

		$arrname = (isset($this->arr_name) && $this->arr_name != "" ? $this->arr_name : "arr");

		$dat = "\$".$arrname." = ".$this->req_serialize($arr).";";
		return $dat;
	}

	function set($key,$val)
	{
		$this->$key = $val;
	}

	function req_serialize($arr)
	{
		return var_export($arr, true);
	}

	function php_unserialize($str)
	{
		eval($str);
		if (!is_array($arr))
		{
			eval(stripslashes($str));
		}
		return $arr;
	}
}
?>
