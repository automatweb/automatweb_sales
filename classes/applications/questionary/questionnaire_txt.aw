<?php
/*
@classinfo syslog_type=ST_QUESTIONNAIRE_TXT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_questionnaire_txt master_index=brother_of master_table=objects index=aw_oid

@default table=aw_questionnaire_txt
@default group=general

@property question type=relpicker reltype=RELTYPE_QUESTION multiple=1 store=connect
@caption K&uuml;simus

@reltype QUESTION value=1 clid=CL_QUESTIONNAIRE_QUESTION
@caption K&uuml;simus

*/

class questionnaire_txt extends class_base
{
	function questionnaire_txt()
	{
		$this->init(array(
			"tpldir" => "applications/questionary/questionnaire_txt",
			"clid" => CL_QUESTIONNAIRE_TXT
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
			$this->db_query("CREATE TABLE aw_questionnaire_txt(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
