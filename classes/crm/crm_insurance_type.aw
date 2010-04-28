<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_insurance_type.aw,v 1.2 2007/11/23 11:42:58 markop Exp $
// crm_insurance_type.aw - Insurance type 
/*

@classinfo syslog_type=ST_CRM_INSURANCE_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

*/

class crm_insurance_type extends class_base
{
	const AW_CLID = 1326;

	function crm_insurance_type()
	{
		$this->init(array(
			"tpldir" => "crm/crm_insurance_type",
			"clid" => CL_CRM_INSURANCE_TYPE
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
}
?>
