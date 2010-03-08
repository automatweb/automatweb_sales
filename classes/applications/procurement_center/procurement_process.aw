<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/procurement_center/procurement_process.aw,v 1.2 2007/12/06 14:33:50 kristo Exp $
// procurement_process.aw - N&otilde;ude protsess 
/*

@classinfo syslog_type=ST_PROCUREMENT_PROCESS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

*/

class procurement_process extends class_base
{
	function procurement_process()
	{
		$this->init(array(
			"tpldir" => "applications/procurement_center/procurement_process",
			"clid" => CL_PROCUREMENT_PROCESS
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
