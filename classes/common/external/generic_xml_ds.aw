<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_GENERIC_XML_DS relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property location type=textbox
@caption URL/asukoht

*/

class generic_xml_ds extends class_base
{
	const AW_CLID = 1391;

	function __construct()
	{
		$this->init(array(
			"tpldir" => "import/generic_xml_ds",
			"clid" => CL_GENERIC_XML_DS
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}
}

?>