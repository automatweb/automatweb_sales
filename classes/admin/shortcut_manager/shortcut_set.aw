<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHORTCUT_SET relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=hannes

@default table=objects
@default group=general

@property name type=textbox field=name
@caption Nimi

@reltype SHORTCUT value=1 clid=CL_SHORTCUT
@caption Shortcut

*/

class shortcut_set extends class_base
{
	const AW_CLID = 1472;

	function shortcut_set()
	{
		$this->init(array(
			"tpldir" => "admin/shortcut_manager/shortcut_set",
			"clid" => CL_SHORTCUT_SET
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
