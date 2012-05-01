<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@extends contentmgmt/object_webview/object_webview

@default group=general

sales_contact_persons

*/

class shop_product_webview extends object_webview
{
	public function __construct()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/object_webview/shop_product_webview",
			"clid" => shop_product_webview_obj::CLID
		));
	}

	public function parse_alias($arr = array())
	{
	}

	public function show($arr)
	{
	}
}
