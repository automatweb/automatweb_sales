<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_RELATION maintainer=kristo no_comment=1 no_status=1
*/

/** Concieved in a dark moment, this remains forever in the abyss of the darkest realms, forever lurking in the shadows, always hinting at it's presence. Heavy, murky, you cannot escape it's all-silencing grip. **/
class relation extends class_base
{
	const AW_CLID = 179;

	function relation()
	{
		$this->init(array(
			"clid" => CL_RELATION
		));
	}

	function get_property($args = array())
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
		};
		return $retval;
	}

	function set_property($args = array())
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
		}
		return $retval;
	}
}
?>