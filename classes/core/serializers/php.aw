<?php
// php.aw - PHP serializer

class php_serializer
{
	var $no_index = false;
	var $arr_name;

	function php_serialize($arr)
	{
		if (!is_array($arr))
		{
			return false;
		}

		$arrname = ($this->arr_name != "" ? $this->arr_name : "arr");

		$dat = "\$".$arrname." = ".$this->req_serialize($arr).";";
		return $dat;
	}

	function set($key,$val)
	{
		$this->$key = $val;
	}

	function req_serialize($arr)
	{
		$str ="array(";
		$td = array();
		foreach($arr as $k => $v)
		{
			if (is_array($v))
			{
				$v = $this->req_serialize($v);
			}
			else
			{
				$v = str_replace("\\","\\\\",$v);
				$v = "'".str_replace("'","\\\'", $v)."'";
			}

			if ($this->no_index)
			{
				$td[] = $v."\n";
			}
			else
			{
				$td[] = "'$k'"."=>".$v;
			}
		}
		return $str.join(",\n",$td).")\n";
	}

	function php_unserialize($str)
	{
		$r = eval($str);
		if (!isset($arr) || !is_array($arr))
		{
			eval(stripslashes($str));
		}
		return $arr;
	}
}
