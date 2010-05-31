<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_PLACE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

*/

class place extends class_base
{
	const AW_CLID = 1000;

	function place()
	{
		$this->init(array(
			"tpldir" => "common/place",
			"clid" => CL_PLACE
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