<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/rostering/person_work_cycle.aw,v 1.2 2007/12/06 14:34:03 kristo Exp $
// person_work_cycle.aw - T&ouml;&ouml;ts&uuml;kkel 
/*

@classinfo syslog_type=ST_PERSON_WORK_CYCLE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

	@property ord type=textbox size=5 table=objects field=jrk
	@caption Prioriteet

*/

class person_work_cycle extends class_base
{
	const AW_CLID = 1139;

	function person_work_cycle()
	{
		$this->init(array(
			"tpldir" => "applications/rostering/person_work_cycle",
			"clid" => CL_PERSON_WORK_CYCLE
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
