<?php

namespace automatweb;


class crm_person_add_education_obj extends _int_object
{
	const AW_CLID = 645;

	function set_name($v)
	{
		$v = htmlspecialchars($v);
		return parent::set_name($v);
	}

	function set_comment($v)
	{
		$v = htmlspecialchars($v);
		return parent::set_comment($v);
	}

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
