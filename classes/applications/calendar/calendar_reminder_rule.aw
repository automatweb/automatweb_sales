<?php
// calendar_reminder_rule.aw - Meeldetuletuse reegel
/*

@classinfo syslog_type=ST_CALENDAR_REMINDER_RULE relationmgr=yes

@default table=objects
@default group=general

*/

class calendar_reminder_rule extends class_base
{
	function calendar_reminder_rule()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "calendar/calendar_reminder_rule",
			"clid" => CL_CALENDAR_REMINDER_RULE
		));
	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr = array())
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
}
