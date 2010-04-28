<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_COUNTRY_CITY relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar prop_cb=1
@extends common/country/country_administrative_unit

*/

class country_city extends country_administrative_unit
{
	const AW_CLID = 958;

	function country_city()
	{
		$this->init(array(
			"tpldir" => "common/country",
			"clid" => CL_COUNTRY_CITY
		));
	}
}

?>
