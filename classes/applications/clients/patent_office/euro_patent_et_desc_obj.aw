<?php

namespace automatweb;


class euro_patent_et_desc_obj extends intellectual_property_obj
{
	const AW_CLID = 1453;

	public function awobj_set_epat_date($value)
	{
		if ($value > 0 and $value < mktime(0, 0, 0, 7, 1, 2002))
		{
			throw new aw_exception("Date can't be before July 1st 2002.");
		}

		parent::set_prop("epat_date", $value);
	}
}

?>
