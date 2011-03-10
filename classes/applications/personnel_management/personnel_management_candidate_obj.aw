<?php

class personnel_management_candidate_obj extends _int_object
{
	function set_prop($k, $v)
	{
		$html_allowed = array();
		if(!in_array($k, $html_allowed) && !is_array($v))
		{
			$v = htmlspecialchars($v);
		}
		return parent::set_prop($k, $v);
	}

	function save($exclusive = false, $previous_state = null)
	{
		if (!$this->is_saved()) // new applications are always active, old ones can be archived etc.
		{
			$this->set_status(object::STAT_ACTIVE);
		}
		parent::save();
	}
}
