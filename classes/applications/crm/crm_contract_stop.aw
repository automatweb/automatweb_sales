<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_contract_stop.aw,v 1.5 2007/12/06 14:33:17 kristo Exp $
// crm_contract_stop.aw - Töölepingu peatamine 
/*

@classinfo syslog_type=ST_CRM_CONTRACT_STOP relationmgr=yes maintainer=markop

@default group=general

@default table=planner

@property start1 type=datetime_select field=start 
@caption Algab

@property end type=datetime_select field=end 
@caption Lõpeb

@property person_ref type=textbox table=external_reference field=ext_id
@caption Viit isikule

@default table=objects

@property type type=relpicker reltype=RELTYPE_CONTRACT_STOP_TYPE field=meta method=serialize
@caption Peatumise liik

@reltype CONTRACT_STOP_TYPE value=1 clid=CL_META
@caption Peatumise liik

@reltype SUBSTITUTE value=2 clid=CL_CRM_PERSON
@caption Asendaja

@tableinfo planner index=id master_table=objects master_index=oid
@tableinfo external_reference index=aw_id master_table=objects master_index=oid

*/

class crm_contract_stop extends class_base
{
	const AW_CLID = 877;

	function crm_contract_stop()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/crm/crm_contract_stop",
			"clid" => CL_CRM_CONTRACT_STOP
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

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
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
