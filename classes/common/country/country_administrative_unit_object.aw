<?php
/*
@classinfo  maintainer=voldemar
*/

class country_administrative_unit_object extends _int_object
{
	const CLID = 960;

	const CLID = 959;

	const CLID = 958;

	const CLID = 953;

	function save($exclusive = false, $previous_state = null)
	{
		if (!is_oid($this->prop("administrative_structure")))
		{
			throw new awex_as_admin_structure("Administrative structure not defined");
		}

		// save this unit object
		return parent::save($exclusive, $previous_state);
	}
}

?>
