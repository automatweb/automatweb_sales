<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default group=general

template
object

*/

class object_webview extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/object_webview/object_webview",
			"clid" => object_webview_obj::CLID
		));
	}

	function parse_alias($args = array())
	{
	}
}
