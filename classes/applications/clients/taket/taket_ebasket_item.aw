<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/taket/taket_ebasket_item.aw,v 1.1 2008/10/01 14:17:40 markop Exp $
// taket_ebasket_item.aw - Taketi Ostukorvi Rida 
/*
@tableinfo taket_ebasket_item index=id master_table=objects master_index=oid

@property product_code table=taket_ebasket_item
@property product_name table=taket_ebasket_item
@property ebasket_id table=taket_ebasket_item
@property quantity table=taket_ebasket_item
@property price table=taket_ebasket_item
@property discount table=taket_ebasket_item
@property inStock table=taket_ebasket_item
@property supplier_id table=taket_ebasket_item
@property ebasket_name table=taket_ebasket_item

@classinfo syslog_type=ST_TAKET_EBASKET_ITEM relationmgr=yes

@default table=objects
@default group=general

*/

class taket_ebasket_item extends class_base
{
	const AW_CLID = 245;

	function taket_ebasket_item()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_ebasket_item",
			"clid" => CL_TAKET_EBASKET_ITEM
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them

	/*
	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{

		};
		return $retval;
	}
	*/

	/*
	function set_property($arr = array())
	{
		$data = &$arr["prop"];
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
}
?>
