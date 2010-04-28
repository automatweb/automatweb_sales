<?php

namespace automatweb;

class shop_warehouse_amount_obj extends _int_object
{
	const AW_CLID = 1458;

	function set_prop($prop, $val)
	{
		if($prop == "amount")
		{
			$val = round($val, 3);
		}
		return parent::set_prop($prop, $val);
	}
}

?>