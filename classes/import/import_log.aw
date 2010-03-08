<?php
// $Header: /home/cvs/automatweb_dev/classes/import/import_log.aw,v 1.1 2008/02/29 11:04:12 instrumental Exp $
// import_log.aw - Impordi logi 
/*

@classinfo syslog_type=ST_IMPORT_LOG relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

@property field type=textbox field=meta method=serialize
@caption Muudetud v&auml;li

@property content type=textarea field=meta method=serialize
@caption Muudatuse sisu

@property timestamp type=textbox field=meta method=serialize
@caption Timestamp

*/

class import_log extends class_base
{
	function import_log()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "import/import_log",
			"clid" => CL_IMPORT_LOG
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

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
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
