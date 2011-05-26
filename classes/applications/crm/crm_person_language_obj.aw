<?php

class crm_person_language_obj extends _int_object
{
	const CLID = 489;

	function set_prop($k, $v)
	{
		if($k == "other")
		{
			$v = htmlspecialchars($v);
		}
		if(in_array($k, array("talk", "understand", "write")) && ($v > 5 || $v < 1))
		{
			$v = 1;
		}
		return parent::set_prop($k, $v);
	}
}

?>
