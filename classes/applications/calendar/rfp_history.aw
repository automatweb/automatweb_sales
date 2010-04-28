<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/rfp_history.aw,v 1.3 2007/12/06 14:32:55 kristo Exp $
// rfp_history.aw - RFP Ajalugu veebis 
/*

@classinfo syslog_type=ST_RFP_HISTORY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general

*/

class rfp_history extends class_base
{
	const AW_CLID = 1194;

	function rfp_history()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/rfp_history",
			"clid" => CL_RFP_HISTORY
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

	function callback_mod_reforb($arr)
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
