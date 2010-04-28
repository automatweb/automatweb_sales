<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_analysis_row.aw,v 1.3 2008/11/18 10:38:53 robert Exp $
// project_analysis_row.aw - Projekti anal&uuml;&uuml;si rida 
/*

@classinfo syslog_type=ST_PROJECT_ANALYSIS_ROW relationmgr=yes no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@property ord type=textbox field=jrk size=5
@caption J&auml;rjekord

@reltype OBJECT value=1 clid=
@caption Objekt
*/

class project_analysis_row extends class_base
{
	const AW_CLID = 1112;

	function project_analysis_row()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_analysis_row",
			"clid" => CL_PROJECT_ANALYSIS_ROW
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
