<?php

// project_strat_goal.aw - Strateegiline edutegur
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

@property ord type=textbox size=5 table=objects field=jrk
@caption J&auml;rjekord
*/

class project_strat_goal extends class_base
{
	function project_strat_goal()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_strat_goal",
			"clid" => CL_PROJECT_STRAT_GOAL
		));
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
}

