<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default group=general
@default table=objects
@default field=meta
@default method=serialize

@property object type=objpicker
@caption Objekt mida kuvada

@property template type=select
@caption Kujundusmall


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

	function _get_template(&$arr)
	{
		$templates = templatemgr::get_list($this->tpldir, array("tpl"));
		$arr["prop"]["options"] = array_combine($templates, $templates);
	}
}
