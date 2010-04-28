<?php

namespace automatweb;
// taket_order.aw - Taketi tellimus
/*
@tableinfo taket_orders index=id master_table=objects master_index=oid
@default table=taket_orders
@property comments type=hidden
@property transport type=hidden
@property timestmp type=hidden
@property price type=hidden
@property contact type=hidden
@property status type=hidden
@property user_id type=hidden
@property location type=hidden

@classinfo relationmgr=yes

@default table=objects
@default group=general

*/

class taket_order extends class_base
{
	const AW_CLID = 243;

	function taket_order()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_order",
			"clid" => CL_TAKET_ORDER
		));
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them


	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{

		};
		return $retval;
	}


	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
                {

		}
		return $retval;
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
}
?>
