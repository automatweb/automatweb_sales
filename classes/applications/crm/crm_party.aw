<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_PARTY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_crm_party master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_party
@default group=general


@property participant type=relpicker reltype=RELTYPE_PARTICIPANT
@caption Osaleja

@property task type=relpicker reltype=RELTYPE_TASK
@caption Tegevus

@property percentage type=textbox
@caption Osaluse %

@property hours type=textbox
@caption Tundides

#RELTYPES
@reltype PARTICIPANT value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON,CL_PROJECT
@caption Osaleja

@reltype TASK value=2 clid=CL_TASK,CL_CRM_MEETING,CL_CRM_CALL,CL_BUG
@caption Tegevus

*/

class crm_party extends class_base
{
	const AW_CLID = 1490;

	function crm_party()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_party",
			"clid" => CL_CRM_PARTY
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_party(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "participant":
			case "task":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
			case "percentage":
			case "hours":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "double"
				));
				return true;
		}
	}
}

?>
