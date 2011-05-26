<?php

class aw_site_entry_obj extends _int_object
{
	const CLID = 1498;

	function set_prop($k, $v)
	{
		if ($k == "name")
		{
			parent::set_name($v);
		}
		return parent::set_prop($k, $v);
	}

	function set_name($v)
	{
		parent::set_prop("name", $v);
		return parent::set_name($v);
	}

	function prop($k)
	{
		$rv = parent::prop($k);
		if ($k == "name" && $rv == "")
		{
			return parent::prop("url");
		}
		return $rv;
	}

	function name()
	{
		$rv = parent::name();
		if ($rv == "")
		{
			return parent::prop("name");
		}
		return $rv;
	}
}

?>
