<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_ADDRESS_STREET relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar

@default table=objects
@default field=meta
@default method=serialize
@default group=general
	@property administrative_structure type=hidden

*/

require_once(aw_ini_get("basedir") . "/classes/common/address/as_header.aw");

class address_street extends class_base
{
	const AW_CLID = 960;

	function address_street()
	{
		$this->init(array(
			"tpldir" => "common/address",
			"clid" => CL_ADDRESS_STREET
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
}

?>