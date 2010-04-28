<?php

namespace automatweb;


class personnel_management_candidate_obj extends _int_object
{
	const AW_CLID = 812;

	function set_prop($k, $v)
	{
		$html_allowed = array();
		if(!in_array($k, $html_allowed) && !is_array($v))
		{
			$v = htmlspecialchars($v);
		}
		return parent::set_prop($k, $v);
	}
}

?>
