<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/class_designer_relation.aw,v 1.4 2007/12/06 14:33:03 kristo Exp $
// class_designer_relation.aw - Seos 
/*

@classinfo syslog_type=ST_CLASS_DESIGNER_RELATION relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property r_class_id type=select multiple=1
@caption Seostatav klass

@property value type=textbox
@caption Number

*/

class class_designer_relation extends class_base
{
	function class_designer_relation()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/class_designer_relation",
			"clid" => CL_CLASS_DESIGNER_RELATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "r_class_id":
				$prop["options"] = get_class_picker();
				break;
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
}
?>
