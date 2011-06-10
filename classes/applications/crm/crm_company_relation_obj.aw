<?php

class crm_company_relation_obj extends _int_object
{
	const CLID = 1405;

	function set_prop($k, $v)
	{
		if($k == "add_info")
		{
			$v = htmlspecialchars($v);
		}
		return parent::set_prop($k, $v);
	}
}

?>
