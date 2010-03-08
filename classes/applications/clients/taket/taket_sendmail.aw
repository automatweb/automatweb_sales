<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/taket/taket_sendmail.aw,v 1.1 2008/10/01 14:17:40 markop Exp $
// taket_sendmail.aw - Taket Sendmail 
/*

@classinfo syslog_type=ST_TAKET_SENDMAIL relationmgr=yes

@default table=objects
@default group=general

@reltype ADDRESS value=1 clid=CL_ML_MEMBER
@caption Aadress

@property recurrence type=releditor reltype=RELTYPE_RECURRENCE props=address mode=manager

*/

class taket_sendmail extends class_base
{
	function taket_sendmail()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "taket/taket_sendmail",
			"clid" => CL_TAKET_SENDMAIL
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
