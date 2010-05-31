<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/questionary/question.aw,v 1.3 2007/12/06 14:33:53 kristo Exp $
// question.aw - K&uml;simus 
/*

@classinfo syslog_type=ST_QUESTION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general

@property jrk type=textbox field=jrk size=3
@caption Jrk

*/

class question extends class_base
{
	const AW_CLID = 1155;

	function question()
	{
		$this->init(array(
			"tpldir" => "applications/questionary/question",
			"clid" => CL_QUESTION
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
