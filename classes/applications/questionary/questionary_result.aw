<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/questionary/questionary_result.aw,v 1.3 2007/12/06 14:33:53 kristo Exp $
// questionary_result.aw - K&uuml;simustiku vastus 
/*

@classinfo syslog_type=ST_QUESTIONARY_RESULT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize
	@property questionary type=relpicker reltype=RELTYPE_QUESTIONARY
	@caption K&uuml;simustik

	@property question_group type=relpicker reltype=RELTYPE_QUESTION_GROUP
	@caption K&uuml;simustegrupp
	
	@property question type=relpicker reltype=RELTYPE_QUESTION
	@caption K&uuml;simus

	@property question_topic type=relpicker reltype=RELTYPE_TOPIC
	@caption K&uuml;simuse teema

	@property answer type=textbox
	@caption Vastus
		
	@property relation_id type=hidden no_caption=1

@reltype QUESTIONARY value=1 clid=CL_QUESTINOARY
@caption K&uuml;simustik

@reltype QUESTION_GROUP value=2 clid=CL_QUESTION_GROUP
@caption K&uuml;simustegrupp

@reltype QUESTION value=3 clid=CL_QUESTION
@caption K&uuml;simus

@reltype TOPIC value=4 clid=CL_QUESTION
@caption K&uuml;simuste teema

*/

class questionary_result extends class_base
{
	const AW_CLID = 1158;

	function questionary_result()
	{
		$this->init(array(
			"tpldir" => "applications/questionary/questionary_result",
			"clid" => CL_QUESTIONARY_RESULT
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

	function callback_mod_reforb($arr)
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

	/**
		@attrib params=name api=1
		@param questionary required type=oid
			questionary objects oid
		@param group optional type=oid
		@param question required type=oid
			question objects oid
		@param topic optional type=oid
		@param answer required type=string
		@param answerer required type=int
			answerer's id.. to that this answer connected.. 
		
		@comment
			adds new answer to specified questionary and question.
			
	**/
	function add_answer($arr)
	{
	
		if(!$arr["answerer"] || !$arr["questionary"] || !$arr["question"])
		{
			return false;
		}

		$obj = obj();
		$q_obj = obj($arr["question"]);
		$obj->set_name(t("Vastus k&uuml;simusele: \"".$q_obj->name()."\""));
		$obj->set_parent($arr["questionary"]);
		$obj->set_class_id(CL_QUESTIONARY_RESULT);
		$obj->set_prop("questionary", $arr["questionary"]);
		$obj->set_prop("question", $arr["question"]);
		$obj->set_prop("question_group", $arr["group"]);
		$obj->set_prop("question_topic", $arr["topic"]);
		$obj->set_prop("answer", $arr["answer"]);
		$id = $obj->save();
		$answerer = obj($arr["answerer"]);
		$answerer->connect(array(
			"to" => $id,
			"type" => "RELTYPE_ANSWER",
		));
		$answerer->save();
		return $id;
	}
}
?>
