<?php

class personnel_management_candidate_obj extends _int_object
{
	const CLID = 812;

	function set_prop($k, $v)
	{
		$html_allowed = array();
		if(!in_array($k, $html_allowed) && !is_array($v))
		{
			$v = htmlspecialchars($v);
		}
		return parent::set_prop($k, $v);
	}

	public function save($check_state = false)
	{
		if (!$this->is_saved()) // new applications are always active, old ones can be archived etc.
		{
			$this->set_status(object::STAT_ACTIVE);
		}
		parent::save();
	}
}
