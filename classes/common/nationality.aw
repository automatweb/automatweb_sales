<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_NATIONALITY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

*/

class nationality extends class_base
{
	const AW_CLID = 1372;

	function nationality()
	{
		$this->init(array(
			"tpldir" => "applications/crm/nationality",
			"clid" => CL_NATIONALITY
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
