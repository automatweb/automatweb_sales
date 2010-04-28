<?php

namespace automatweb;


class crm_party_obj extends _int_object
{
	const AW_CLID = 1490;


	function set_prop($pn, $pv)
	{
		switch($pn)
		{
			case "percentage":
			case "hours":
				$pv = str_replace("," , "." , $pv);
				break;
			
		}
		return parent::set_prop($pn,$pv);

	}
	

}

?>
