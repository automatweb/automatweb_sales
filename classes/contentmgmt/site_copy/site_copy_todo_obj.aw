<?php

class site_copy_todo_obj extends _int_object
{
	function prop($k)
	{
		if($k == "url")
		{
			return parent::name();
		}
		return parent::prop($k);
	}

	function set_prop($k, $v)
	{
		return parent::set_prop($k, $v);
	}
}

?>
