<?php

namespace automatweb;
// questionnaire_answer.aw - D&uuml;naamilise k&uuml;simustiku vastus
/*

@classinfo syslog_type=ST_QUESTIONNAIRE_ANSWER relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_questionnaire_answer master_index=brother_of master_table=objects index=aw_oid

@default table=objects
@default group=general

	@property name type=textbox field=name
	@caption Vastus

	@property jrk type=textbox size=4 field=jrk
	@caption J&auml;rjekord

	@property correct type=checkbox field=correct table=aw_questionnaire_answer
	@caption &Otilde;ige vastus

	@property comm type=textbox field=comment
	@caption Kommentaar

*/

class questionnaire_answer extends class_base
{
	const AW_CLID = 1395;

	function questionnaire_answer()
	{
		$this->init(array(
			"tpldir" => "applications/questionary/questionnaire_answer",
			"clid" => CL_QUESTIONNAIRE_ANSWER
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

	function callback_mod_reforb($arr)
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_questionnaire_answer(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "correct":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$ol = new object_list(array(
					"class_id" => CL_QUESTIONNAIRE_ANSWER,
					"site_id" => array(),
					"lang_id" => array(),
				));
				foreach($ol->arr() as $o)
				{
					$correct = $o->meta("correct");
					$this->db_query("INSERT INTO aw_questionnaire_answer (aw_oid, correct) VALUES ('".$o->brother_of()."', '".$correct."')");
				}
				return true;
		}
	}
}

?>
