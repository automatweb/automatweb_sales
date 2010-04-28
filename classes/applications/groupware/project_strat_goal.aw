<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_strat_goal.aw,v 1.3 2007/12/06 14:33:32 kristo Exp $
// project_strat_goal.aw - Strateegiline edutegur 
/*

@classinfo syslog_type=ST_PROJECT_STRAT_GOAL relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@property ord type=textbox size=5 table=objects field=jrk
@caption J&auml;rjekord
*/

class project_strat_goal extends class_base
{
	const AW_CLID = 950;

	function project_strat_goal()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_strat_goal",
			"clid" => CL_PROJECT_STRAT_GOAL
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
