<?php

class shop_orderer_data_site_show_bills_obj extends shop_orderer_data_site_show_obj
{
	function prop($k)
	{

		$rv = parent::prop($k);

		if ($k === "state")
		{
			if(strlen($rv))
			return explode("," , $rv);
		}

		return $rv;
	}

	function set_prop($k, $v)
	{
		if ($k == "state")
		{
			if(is_array($v))
			{
				$v = join("," , $v);
			}
		}
		parent::set_prop($k,$v);
	}

}

?>
