<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_team.aw,v 1.2 2007/12/06 14:33:32 kristo Exp $
// project_team.aw - Projekti tiim 
/*

@classinfo syslog_type=ST_PROJECT_TEAM relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@reltype TEAM_MEMBER value=1 clid=CL_CRM_PERSON
@caption Tiimi liige
*/

class project_team extends class_base
{
	function project_team()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_team",
			"clid" => CL_PROJECT_TEAM
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
