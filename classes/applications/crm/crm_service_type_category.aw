<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_service_type_category.aw,v 1.2 2007/12/06 14:33:17 kristo Exp $
// crm_service_type_category.aw - Teenuse liigi kategooria 
/*

@classinfo syslog_type=ST_CRM_SERVICE_TYPE_CATEGORY relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

*/

class crm_service_type_category extends class_base
{
	const AW_CLID = 1249;

	function crm_service_type_category()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_service_type_category",
			"clid" => CL_CRM_SERVICE_TYPE_CATEGORY
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

	function callback_mod_reforb(&$arr)
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
