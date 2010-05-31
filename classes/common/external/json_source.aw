<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_JSON_SOURCE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=voldemar

@default table=objects
@default group=general

*/

class json_source extends class_base
{
	const AW_CLID = 1355;

	function json_source()
	{
		$this->init(array(
			"tpldir" => "common/external/json_source",
			"clid" => CL_JSON_SOURCE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}
}

?>