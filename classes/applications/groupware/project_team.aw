<?php

// project_team.aw - Projekti tiim
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}
}
?>
