<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_grid.aw,v 1.4 2007/12/06 14:33:03 kristo Exp $
// property_grid.aw - Grid 
/*

@classinfo syslog_type=ST_PROPERTY_GRID relationmgr=yes maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property grid_type type=chooser default=0
@caption Gridi tüüp

*/

class property_grid extends class_base
{
	function property_grid()
	{
		$this->init(array(
			"clid" => CL_PROPERTY_GRID
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "grid_type":
				$prop["options"] = array(
					"0" => "vbox",
					"1" => "hbox",
				);
				break;

		};
		return $retval;
	}

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
