<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/property_toolbar.aw,v 1.9 2007/12/06 14:33:04 kristo Exp $
// property_toolbar.aw - Toolbar 
/*

@classinfo syslog_type=ST_PROPERTY_TOOLBAR relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize


@default group=buttons

@property buttons type=releditor reltype=RELTYPE_BUTTON table_fields=ord,name,b_type props=ord,name,b_type no_caption=1 mode=manager table_edit_fields=ord

@groupinfo buttons caption="Nupud"

@reltype BUTTON value=1 clid=CL_PROPERTY_TOOLBAR_BUTTON
@caption nupp

*/

class property_toolbar extends class_base
{
	function property_toolbar()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/property_toolbar",
			"clid" => CL_PROPERTY_TOOLBAR
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "buttons":
				$prop["direct_links"] = 1;
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

	function get_visualizer_prop($el, &$propdata)
	{
		/*
		$t = get_instance("vcl/toolbar");
		
		$buttons = new object_list($el->connections_from(array(
			"type" => "RELTYPE_BUTTON",
			"sort_by" => "jrk"
		)));
		foreach($buttons->arr() as $b)
		{
			$i = $b->instance();
			$i->get_button($b, $t);
		}

		$propdata["type"] = "text";
		$propdata["value"] = $t->get_toolbar();
		$propdata["no_caption"] = 1;
		*/
	}

	function generate_get_property($arr)
	{
		$el = obj($arr["id"]);
		$pn = $arr["name"];

		$ret = array(
			"get_property" => "\t\t\tcase \"$pn\":\n\t\t\t\t\$this->generate_$pn(\$arr);\n\t\t\t\tbreak;\n\n",
			"generate_methods" => array(
				"generate_$pn"
			)
		);

		$buttons = new object_list($el->connections_from(array(
			"type" => "RELTYPE_BUTTON",
			"sort_by" => "jrk"
		)));
		foreach($buttons->arr() as $b)
		{
			$i = $b->instance();
			$i->get_generate_methods($pn, $b, $ret["generate_methods"]);
		}

		return $ret;
	}

	function generate_method($arr)
	{
		$el = obj($arr["id"]);
		$meth = $arr["name"];

		if (substr($meth, 0, 2) == "on")
		{
			// button submit handler
			$ret = "";
			$ret .= "\t/** submit handler for toolbar button  \n";
			$ret .= "\t\n";
			$ret .= "\t\t@attrib name=$meth\n";
			$ret .= "\t\n";
			$ret .= "\t**/\n";
			$ret .= "\tfunction $meth(\$arr)\n";
			$ret .= "\t{\n";
			$ret .= "\t\t/* Handle submitted data here */\n";
			$ret .= "\t\treturn \$arr[\"return_url\"];\n";
			$ret .= "\t}\n";
			$ret .= "\n";
			return $ret;
		}

		$content = "";
		$content .= "\t\t\$t =& \$arr[\"prop\"][\"vcl_inst\"];\n\n";

		$buttons = new object_list($el->connections_from(array(
			"type" => "RELTYPE_BUTTON",
		)));
		foreach($buttons->arr() as $b)
		{
			$i = $b->instance();
			$content .= $i->get_method_contents($b, $meth);
		}

		return "\tfunction $meth(\$arr)\n\t{\n$content\t}\n\n";
	}
}
?>
