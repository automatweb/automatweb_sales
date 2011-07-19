<?php

/*
@classinfo syslog_type=ST_ADDRESS_STREET relationmgr=yes no_comment=1 no_status=1

@default table=objects
@default field=meta
@default method=serialize
@default group=general
	@property administrative_structure type=hidden

*/

require_once(AW_DIR . "classes/common/address/as_header.aw");

class address_street extends class_base
{
	function address_street()
	{
		$this->init(array(
			"tpldir" => "common/address",
			"clid" => CL_ADDRESS_STREET
		));
	}
}
