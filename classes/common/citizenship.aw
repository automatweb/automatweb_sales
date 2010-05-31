<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CITIZENSHIP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@property start type=date_select field=meta method=serialize
@caption Alguskuup&auml;ev

@property end type=date_select field=meta method=serialize
@caption L&otilde;ppkuup&auml;ev

@property country type=relpicker store=connect reltype=RELTYPE_COUNTRY
@caption Riik

@reltype COUNTRY value=1 clid=CL_CRM_COUNTRY
@caption Riik

*/

class citizenship extends class_base
{
	const AW_CLID = 1377;

	function citizenship()
	{
		$this->init(array(
			"tpldir" => "applications/crm/citizenship",
			"clid" => CL_CITIZENSHIP
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
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
}
?>
