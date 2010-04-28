<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/messageboard.aw,v 1.3 2008/01/31 13:52:14 kristo Exp $
// messageboard.aw - Teadete tahvel 
/*

@classinfo syslog_type=ST_MESSAGEBOARD  maintainer=kristo

@default table=objects
@default group=general

@property comments type=comments group=comments store=no
@caption Kommentaarid

@groupinfo comments caption=Kommentaarid

*/

class messageboard extends class_base
{
	const AW_CLID = 322;

	function messageboard()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "contentmgmt/messageboard",
			"clid" => CL_MESSAGEBOARD
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		};
		return $retval;
	}

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/

	function parse_alias($arr)
	{
		// aw, shait, I need to load a different template from different directory.
		// how on earth am I going to do that?
		$ar = $arr;
		$ar["group"] = "comments";
		$ar["id"] = $arr["alias"]["target"];
		aw_global_set("msg_embedded",true);
		aw_global_set("msg_aliasfrom",$arr["alias"]["from"]);
		$this->set_classinfo(array("name" => "hide_tabs", "value" => 1));
		return $this->change($ar);
	}

	function callback_mod_reforb($arr)
	{
		$embed = aw_global_get("msg_embedded");
		if ($embed)
		{
			$arr["embedded"] = true;
			$arr["section"] = aw_global_get("msg_aliasfrom");
		};
	}

	function callback_mod_retval($arr)
	{
		if ($arr["embedded"])
		{
			return aw_global_get("baseurl") . "/" . $arr["section"];
		};
	}

}
?>
