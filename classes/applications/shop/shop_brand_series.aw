<?php

namespace automatweb;
// shop_brand_series.aw - Lao br&auml;ndiseeria
/*

@classinfo syslog_type=ST_SHOP_BRAND_SERIES relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

*/

class shop_brand_series extends class_base
{
	const AW_CLID = 1444;

	function shop_brand_series()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_brand_series",
			"clid" => CL_SHOP_BRAND_SERIES
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}
}

?>
