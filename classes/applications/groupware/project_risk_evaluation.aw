<?php

// project_risk_evaluation.aw - Projekti riskide hindamine
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_risk_evals index=aw_oid master_table=objects master_index=brother_of

@default group=general

	@property proj type=relpicker reltype=RELTYPE_PROJECT table=aw_risk_evals field=aw_proj
	@caption Projekt

	@property evaluator type=relpicker reltype=RELTYPE_EVAL table=aw_risk_evals field=aw_eval
	@caption Hindaja

@reltype PROJECT value=1 clid=CL_PROJECT
@caption Projekt

@reltype EVAL value=2 clid=CL_CRM_PERSON
@caption Hindaja
*/

class project_risk_evaluation extends class_base
{
	function project_risk_evaluation()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_risk_evaluation",
			"clid" => CL_PROJECT_RISK_EVALUATION
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_risk_evals(aw_oid int primary key, aw_proj int, aw_eval int)");
			return true;
		}
	}
}
