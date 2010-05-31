<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/expp/expp_publication.aw,v 1.6 2007/11/23 07:03:30 dragut Exp $
// expp_publication.aw - V&auml;ljaanne 
/*

@classinfo syslog_type=ST_EXPP_PUBLICATION relationmgr=yes prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

@property description_from_reggy type=textarea field=meta method=serialize
@caption Kirjeldus Reggy-st

@property description type=textarea field=meta method=serialize
@caption Kirjeldus

*/

class expp_publication extends class_base
{
	const AW_CLID = 1011;

	function expp_publication()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/clients/expp/expp_publication",
			"clid" => CL_EXPP_PUBLICATION
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
			case "comment":
				$prop['type'] = "text";
				$prop['caption'] = "Kood";
				$prop['comment'] = "Unikaalne kood";
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

	function callback_mod_reforb(&$arr)
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
