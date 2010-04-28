<?php

namespace automatweb;

/*

@classinfo syslog_type=ST_ACTION maintainer=kristo
@classinfo relationmgr=yes

@groupinfo general caption=Üldine

@default table=objects
@default group=general

@property name type=textbox
@caption Nimi

@property description type=textarea field=meta method=serialize
@caption Kirjeldus

@property goal type=textarea field=meta method=serialize
@caption Eesmärk

@property entity_actor type=relpicker rel=1 reltype=RELTYPE_ACTOR field=meta method=serialize
@caption Tegija

@reltype INSTRUCTION clid=CL_IMAGE,CL_FILE value=1
@caption instruktsioon

@reltype ACTOR clid=CL_ACTOR value=2
@caption tegija

*/

class action extends class_base
{
	const AW_CLID = 173;

	function action()
	{
		$this->init(array(
			"tpldir" => "action",
			"clid" => CL_ACTION
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
