<?php

namespace automatweb;


class event_webview_obj extends _int_object
{
	const AW_CLID = 1475;

	function prop($k)
	{
		if($k == "date_end" && !is_admin())
		{
			return parent::prop($k) + 24*3600; 
		}
		return parent::prop($k);
	}
}

?>
