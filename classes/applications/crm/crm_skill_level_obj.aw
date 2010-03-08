<?php

class crm_skill_level_obj extends _int_object
{
	function set_prop($k, $v)
	{
		if($k == "other")
		{
			$v = htmlspecialchars($v);
		}
		return parent::set_prop($k, $v);
	}
}

?>
