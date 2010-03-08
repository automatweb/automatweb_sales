<?php
/*
@classinfo syslog_type=ST_COUNTRY_CITYDISTRICT relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar prop_cb=1
@extends common/country/country_administrative_unit

*/

class country_citydistrict extends country_administrative_unit
{
	function country_citydistrict ()
	{
		$this->init(array(
			"tpldir" => "common/country",
			"clid" => CL_COUNTRY_CITYDISTRICT
		));
	}
}

?>
