<?php

class crm_company_relation_obj extends _int_object
{
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
