<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_group.aw,v 1.5 2007/12/06 14:33:03 kristo Exp $
// property_group.aw - Vormi grupp 
/*

@classinfo syslog_type=ST_PROPERTY_GROUP relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

@property ord type=textbox size=2 field=jrk
@caption Jrk

@default field=meta
@default method=serialize

@property no_submit type=checkbox ch_value=1 
@caption Salvesta nuppu pole tarvis

@property caption type=textbox 
@caption Caption

*/

class property_group extends class_base
{
	function property_group()
	{
		$this->init(array(
			"clid" => CL_PROPERTY_GROUP
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	/*
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		};
		return $retval;
	}
	*/

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/

}
?>
