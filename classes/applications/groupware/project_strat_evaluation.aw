<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_strat_evaluation.aw,v 1.2 2007/12/06 14:33:32 kristo Exp $
// project_strat_evaluation.aw - Strateegiliste edutegurite hindamine 
/*

@classinfo syslog_type=ST_PROJECT_STRAT_EVALUATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_strat_evals index=aw_oid master_table=objects master_index=brother_of

@default group=general

	@property proj type=relpicker reltype=RELTYPE_PROJECT table=aw_strat_evals field=aw_proj
	@caption Projekt

	@property evaluator type=relpicker reltype=RELTYPE_EVAL table=aw_strat_evals field=aw_eval
	@caption Hindaja

@reltype PROJECT value=1 clid=CL_PROJECT
@caption Projekt

@reltype EVAL value=2 clid=CL_CRM_PERSON
@caption Hindaja
*/

class project_strat_evaluation extends class_base
{
	function project_strat_evaluation()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_strat_evaluation",
			"clid" => CL_PROJECT_STRAT_EVALUATION
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_strat_evals(aw_oid int primary key, aw_proj int, aw_eval int)");
			return true;
		}
	}
}
?>
