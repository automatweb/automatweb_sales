<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_table_column.aw,v 1.5 2007/12/06 14:33:03 kristo Exp $
// property_table_column.aw - Tabeli veerg 
/*

@classinfo syslog_type=ST_PROPERTY_TABLE_COLUMN relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

@property ord field=jrk size=2
@caption Jrk

@default field=meta
@default method=serialize

@property sortable type=checkbox ch_value=1
@caption Sorteeritav

@property width type=textbox datatype=int size=2
@caption Laius

@property nowrap type=checkbox ch_value=1
@caption Poolitamine keelatud

@property align type=select
@caption Joondamine

@property c_parent type=relpicker reltype=RELTYPE_PARENT
@caption &Uuml;lemtulp

@reltype PARENT value=1 clid=CL_PROPERTY_TABLE_COLUMN
@caption &uuml;lemtulp

*/

class property_table_column extends class_base
{
	const AW_CLID = 887;

	function property_table_column()
	{
		$this->init(array(
			"clid" => CL_PROPERTY_TABLE_COLUMN
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "align":
				$prop["options"] = array("" => "", "left" => "Vasakul", "center" => "Keskel", "right" => "Paremal");
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
