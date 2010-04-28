<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_AUDIO relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

@property author type=textbox field=meta method=serialize
@caption Autor
*/

class audio extends class_base
{
	const AW_CLID = 1019;

	function audio()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/audio",
			"clid" => CL_AUDIO
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
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

	function callback_mod_reforb($arr)
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
