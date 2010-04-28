<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/intranet/intranet.aw,v 1.4 2008/01/31 13:54:48 kristo Exp $
// intranet.aw - Intranet 
/*

@classinfo syslog_type=ST_INTRANET relationmgr=yes maintainer=kristo

@default table=objects
@default group=general

@property showlink type=text store=no editonly=1
@caption Näita

@property tpldata type=text store=no editonly=1
@caption alamtemplated

*/

class intranet extends class_base
{
	const AW_CLID = 233;

	function intranet()
	{
		$this->init(array(
			"tpldir" => "intranet",
			"clid" => CL_INTRANET
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "showlink":
				$data["value"] = html::href(array(
					"url" => $this->mk_my_orb("show",array("id" => $arr["obj_inst"]->id())),
					"caption" => $data["caption"],
				));
				break;

			case "tpldata":
				$this->read_template("main.tpl");
				$subs = $this->get_subtemplates_regex(".*");
				/*
				print "<pre>";
				print_r($subs);
				print "</pre>";
				*/
				break;


		};
		return $retval;
	}

	/*
	function set_property($args = array())
	{
		$data = &$args["prop"];
		$retval = PROP_OK;
		switch($data["name"])
                {

		}
		return $retval;
	}	
	*/

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($args)
	{
		extract($args);
		return $this->show(array("id" => $alias["target"]));
	}

	/** this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias 
		
		@attrib name=show params=name default="0"
		
		@param id required type=int
		
		@returns
		
		
		@comment

	**/
	function show($arr)
	{
		extract($arr);
		$ob = new object($id);

		$this->read_template("main.tpl");

		$this->vars(array(
			"name" => $ob->prop("name"),
		));

		return $this->parse();
	}
}
?>
