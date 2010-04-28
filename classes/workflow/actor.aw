<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_ACTOR
@classinfo relationmgr=yes maintainer=kristo

@groupinfo general caption=Üldine

@default table=objects
@default group=general

@property name type=textbox 
@caption Nimi

@property description type=textarea field=meta method=serialize
@caption Rolli kirjeldus

@reltype INSTRUCTION clid=CL_IMAGE,CL_FILE value=1
@caption instruktsioon
*/

class actor extends class_base
{
	const AW_CLID = 172;

	function actor()
	{
		$this->init(array(
			'clid' => CL_ACTOR
		));
	}

	function get_property($args)
	{
		$data = &$args["prop"];
		$name = $data["name"];
		$retval = PROP_OK;
		if ($name == "comment" || $name == "alias" || $name == "jrk")
		{
			return PROP_IGNORE;
		};
	}
}
?>
