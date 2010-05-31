<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/bug_o_matic_3000/bug_app_type.aw,v 1.4 2008/12/03 10:14:41 robert Exp $
// bug_app_type.aw - Bugtracki rakendus 
/*

@classinfo syslog_type=ST_BUG_APP_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert

@default table=objects
@default group=general

@reltype TYPE value=1 clid=CL_META
@caption Bugi t&uuml;&uuml;p
*/

class bug_app_type extends class_base
{
	const AW_CLID = 1342;

	function bug_app_type()
	{
		$this->init(array(
			"tpldir" => "applications/bug_o_matic_3000/bug_app_type",
			"clid" => CL_BUG_APP_TYPE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>
