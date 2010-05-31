<?php

namespace automatweb;
// questionnaire_answerer.aw - D&uuml;naamilise k&uuml;simustiku vastaja
/*

@classinfo syslog_type=ST_QUESTIONNAIRE_ANSWERER relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

@property person type=relpicker reltype=RELTYPE_PERSON store=connect
@caption Vastaja isik

@property questionnaire type=relpicker reltype=RELTYPE_QUESTIONNAIRE store=connect
@caption Vastatud k&uuml;simustik

#@property correct_ans type=relpicker reltype=RELTYPE_CORRECT multiple=1 store=connect
#@caption &Otilde;igesti vastatud k&uuml;simused

#@property wrong_ans type=relpicker reltype=RELTYPE_WRONG multiple=1 store=connect
#@caption Valesti vastatud k&uuml;simused

@property answers type=relpicker reltype=RELTYPE_ANSWER multiple=1 store=connect
@caption Vastused

@property txtanswers type=relpicker reltype=RELTYPE_TXTANSWER multiple=1 store=connect
@caption Tekstivastused

# RELTYPES

@reltype CORRECT value=1 clid=CL_QUESTIONNAIRE_QUESTION
@caption &Otilde;igesti vastatud k&uuml;simus

@reltype WRONG value=2 clid=CL_QUESTIONNAIRE_QUESTION
@caption Valesti vastatud k&uuml;simused

@reltype PERSON value=3 clid=CL_CRM_PERSON
@caption Vastaja isik

@reltype QUESTIONNAIRE value=4 clid=CL_QUESTIONNAIRE
@caption Vastatud d&uuml;naamiline k&uuml;simustik

@reltype ANSWER value=5 clid=CL_QUESTIONNAIRE_ANSWER
@caption Vastus

@reltype TXTANSWER value=6 clid=CL_QUESTIONNAIRE_TXT
@caption Tekstivastus

*/

class questionnaire_answerer extends class_base
{
	const AW_CLID = 1396;

	function questionnaire_answerer()
	{
		$this->init(array(
			"tpldir" => "applications/questionary/questionnaire_answerer",
			"clid" => CL_QUESTIONNAIRE_ANSWERER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

?>
