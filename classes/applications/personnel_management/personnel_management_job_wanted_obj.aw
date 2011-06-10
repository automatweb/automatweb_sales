<?php

class personnel_management_job_wanted_obj extends _int_object
{
	const CLID = 351;

	function set_prop($k, $v)
	{
		$html_allowed = array();
		if(!in_array($k, $html_allowed) && !is_array($v))
		{
			$v = htmlspecialchars($v);
		}
		return parent::set_prop($k, $v);
	}

	function prop($k)
	{
		if($k == "load2" && count((array)parent::prop($k)) == 0 && (is_array(parent::prop("load")) || strlen(parent::prop("load")) > 0))
		{
			return (array)parent::prop("load");
		}
		return parent::prop($k);
	}
}

?>
