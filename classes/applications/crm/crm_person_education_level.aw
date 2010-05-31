<?php

namespace automatweb;
// crm_person_education_level.aw - Haridustase
/*

@classinfo syslog_type=ST_CRM_PERSON_EDUCATION_LEVEL relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

*/

class crm_person_education_level extends class_base
{
	const AW_CLID = 1398;

	function crm_person_education_level()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_person_education_level",
			"clid" => CL_CRM_PERSON_EDUCATION_LEVEL
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
