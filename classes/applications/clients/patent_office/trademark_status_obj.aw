<?php

class trademark_status_obj extends _int_object
{
	const CLID = 1195;

	public function awobj_set_verified($value)
	{
		if (!empty($value))
		{
			parent::set_prop("verified_date", time());
		}

		return parent::set_prop("verified", $value);
	}
}

