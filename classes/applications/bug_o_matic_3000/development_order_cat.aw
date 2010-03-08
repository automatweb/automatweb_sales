<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/bug_o_matic_3000/development_order_cat.aw,v 1.3 2007/12/06 14:32:52 kristo Exp $
// development_order_cat.aw - Arendustellimuste kataloog 
/*

@classinfo syslog_type=ST_DEVELOPMENT_ORDER_CAT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert

@default table=objects
@default group=general

*/

class development_order_cat extends class_base
{
	function development_order_cat()
	{
		$this->init(array(
			"tpldir" => "applications/bug_o_matic_3000/development_order_cat",
			"clid" => CL_DEVELOPMENT_ORDER_CAT
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
}
?>
