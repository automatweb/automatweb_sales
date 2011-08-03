<?php

class country_administrative_unit_object extends _int_object
{
	const CLID = 953;

	public function save($check_state = false)
	{
		if (!is_oid($this->prop("administrative_structure")))
		{
			throw new awex_as_admin_structure("Administrative structure not defined");
		}

		// save this unit object
		return parent::save($check_state);
	}
}
