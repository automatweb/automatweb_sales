<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_goal.aw,v 1.2 2007/12/06 14:33:32 kristo Exp $
// project_goal.aw - Verstapost 
/*

@classinfo syslog_type=ST_PROJECT_GOAL relationmgr=yes no_comment=1 no_status=1 maintainer=markop

@default table=objects
@default group=general

@tableinfo aw_project_goals index=aw_oid

@property start1 type=datetime_select table=aw_project_goals field=aw_start
@caption Algus

@property end type=datetime_select table=aw_project_goals field=aw_end
@caption L&otilde;pp

@property content type=textarea rows=20 cols=50 table=aw_project_goals field=aw_content
@caption Sisu

*/

class project_goal extends class_base
{
	const AW_CLID = 938;

	function project_goal()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_goal",
			"clid" => CL_PROJECT_GOAL
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
