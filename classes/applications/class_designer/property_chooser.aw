<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_chooser.aw,v 1.4 2007/12/06 14:33:03 kristo Exp $
// property_chooser.aw - Element - valik 
/*

@classinfo syslog_type=ST_PROPERTY_CHOOSER no_comment=1 maintainer=kristo

@default table=objects
@default group=general

@property ord type=textbox size=2 field=jrk
@caption Jrk

@default field=meta
@default method=serialize

@property multiple type=checkbox ch_value=1
@caption Saab teha mitu valikut

@property orient type=checkbox ch_value=1
@caption Elemendid on vertikaalis

@property options type=textarea
@caption Valikud (iga valik eraldi real)

*/

class property_chooser extends class_base
{
	function property_chooser()
	{
		$this->init(array(
			"clid" => CL_PROPERTY_CHOOSER
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

	function generate_get_property($arr)
	{
		$el = new object($arr["id"]);
		$sys_name = $arr["name"];
		$options = explode("\n",$el->prop("options"));
		$gpblock = "";
		if (sizeof($options) > 0)
		{
			$gpblock .= "\t\t\tcase \"${sys_name}\":\n";
			$gpblock .= "\t\t\t\t\$prop[\"options\"] = array(\n";
			foreach($options as $key => $val)
			{
				$val = trim($val);
				$gpblock .= "\t\t\t\t\t" . $key  . " => " . "\"${val}\",\n";
			};
			$gpblock .= "\t\t\t\t);\n";
			$gpblock .= "\t\t\t\tbreak;\n";
		};

		return array(
			"get_property" => $gpblock,
		);
	}

}
?>
