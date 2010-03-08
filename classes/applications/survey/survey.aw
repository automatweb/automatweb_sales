<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/survey/survey.aw,v 1.9 2008/03/12 21:23:17 kristo Exp $
// survey.aw - Ankeet 
/*

@classinfo syslog_type=ST_SURVEY relationmgr=yes maintainer=kristo
@tableinfo survey index=aw_id master_table=objects master_index=brother_of

@default table=objects
@default group=survey
@default table=survey

@property utext1 type=textbox 
@caption Utext1

@property utext2 type=textbox
@caption Utext2

@property utext3 type=textbox
@caption Utext3

@property utext4 type=textarea
@caption Utext4

@property uchoice1 type=classificator
@caption Uchoice1

@property uchoice2 type=classificator
@caption Uchoice2

@property uchoice3 type=classificator orient=vertical
@caption Uchoice3

@property uchoice4 type=classificator orient=vertical
@caption Uchoice4

@property uchoice5 type=classificator orient=vertical
@caption Uchoice5

@property uchoice6 type=classificator orient=vertical
@caption Uchoice6

@property uchoice7 type=classificator orient=vertical
@caption Uchoice7

@property uchoice8 type=classificator orient=vertical
@caption Uchoice8

@property uchoice9 type=classificator orient=vertical
@caption Uchoice9

@property uchoice10 type=classificator orient=vertical
@caption Uchoice10

@property uchoice11 type=classificator orient=vertical
@caption Uchoice11

@property uchoice12 type=classificator orient=vertical
@caption Uchoice12

@property ubigtext1 type=textarea
@caption Ubigtext1

@property ubigtext2 type=textarea
@caption Ubigtext2

@property ubigtext3 type=textarea
@caption Ubigtext3

@property ubigtext4 type=textarea
@caption Ubigtext4

@property ucheckgroup1 type=classificator orient=vertical method=serialize
@caption Ucheckgroup1

@property utext5 type=textbox
@caption Utext5

@property utext6 type=textbox
@caption Utext6

@property utext7 type=textbox
@caption utext7

@property utext8 type=textbox
@caption utext8

@property remote_host type=textbox
@caption Remote host

@property sbt type=submit 
@caption Submit

@reltype OWNER value=1 clid=CL_USER
@caption Omanik

@groupinfo survey caption="Küsimused"


*/

class survey extends class_base
{
	function survey()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/survey/survey",
			"clid" => CL_SURVEY
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	/*
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		};
		return $retval;
	}
	*/

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		//$retval = PROP_FATAL_ERROR;
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	
	function callback_pre_save($arr)
	{
		// create a name for the object
		// XXX: make it configurable which fields make up the object name
		//$newname = $arr["request"]["utext1"] . " " . $arr["request"]["utext2"];
		//$arr["obj_inst"]->set_name($newname);
	}

	function callback_mod_retval($arr)
	{
		//$arr["args"]["goto"] = aw_ini_get("baseurl") . "/" . $arr["args"]["redirect_to"];
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

	function do_db_upgrade($t,$f)
	{
		switch($f)
		{
			case "ucheckgroup1":
			case "utext5":
			case "utext6":
			case "utext7":
			case "utext8":
				$this->db_add_col($t, array("name" => $f, "type" => "text"));
				return true;
		}
	}
}
?>
