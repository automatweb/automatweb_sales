<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/budgeting/budgeting_scenario.aw,v 1.3 2007/12/06 14:32:51 kristo Exp $
// budgeting_scenario.aw - Eelarve stasenaarium 
/*

@classinfo syslog_type=ST_BUDGETING_SCENARIO relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

*/

class budgeting_scenario extends class_base
{
	const AW_CLID = 1325;

	function budgeting_scenario()
	{
		$this->init(array(
			"tpldir" => "applications/budgeting/budgeting_scenario",
			"clid" => CL_BUDGETING_SCENARIO
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
