<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/taket/taket_ebasket_inst.aw,v 1.1 2008/10/01 14:17:40 markop Exp $
// taket_ebasket_inst.aw - Taketi Ostukorvi Objekt 
/*

@tableinfo taket_ebasket index=id master_table=objects master_index=oid

@property user_id table=taket_ebasket

@classinfo syslog_type=ST_TAKET_EBASKET_INST relationmgr=yes

@default table=objects
@default group=general

@reltype TaketiOstukorviRida value=1 clid=CL_TAKET_EBASKET_ITEM

*/

class taket_ebasket_inst extends class_base
{
	function taket_ebasket_inst()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_ebasket_inst",
			"clid" => CL_TAKET_EBASKET_INST
		));
	}


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
