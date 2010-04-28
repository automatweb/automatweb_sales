<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/document_content_type.aw,v 1.2 2008/01/31 13:52:14 kristo Exp $
// document_content_type.aw - Sisu t&uuml;&uuml;p 
/*

@classinfo syslog_type=ST_DOCUMENT_CONTENT_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo

@default table=objects
@default group=general

*/

class document_content_type extends class_base
{
	const AW_CLID = 1064;

	function document_content_type()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/document_content_type",
			"clid" => CL_DOCUMENT_CONTENT_TYPE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>
