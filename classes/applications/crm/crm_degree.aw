<?php

namespace automatweb;
// crm_degree.aw - Kraad
/*
 
@classinfo syslog_type=ST_CRM_DEGREE relationmgr=yes no_comment=1 no_status=1 prop_cb=1
 

@default table=objects
@default group=general

@property name type=textbox field=name
@caption Kraad

@property subject type=textbox field=meta method=serialize
@caption Haru

*/

class crm_degree extends class_base
{
	const AW_CLID = 1385;

	function crm_degree()
	{
		$this->init(array(
			"tpldir" => "applications/crm//crm_degree",
			"clid" => CL_CRM_DEGREE
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
		arr($arr);
		exit;
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
}

?>
