<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_textbox.aw,v 1.4 2007/12/06 14:33:04 kristo Exp $
// property_textbox.aw - Element - tekstikast 
/*

@classinfo syslog_type=ST_PROPERTY_TEXTBOX no_comment=1 maintainer=kristo

@default table=objects
@default group=general

@property ord type=textbox size=2 field=jrk
@caption Jrk

@default field=meta
@default method=serialize

@property size type=textbox size=2 datatype=int
@caption Pikkus

@property maxlength type=textbox size=2 datatype=int
@caption Max. pikkus

*/

class property_textbox extends class_base
{
	const AW_CLID = 881;

	function property_textbox()
	{
		$this->init(array(
			"clid" => CL_PROPERTY_TEXTBOX
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
