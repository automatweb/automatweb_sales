<?php
// $Header: /home/cvs/automatweb_dev/classes/mrp/mrp_resource_operator.aw,v 1.4 2009/05/07 13:19:31 kristo Exp $
// mrp_resource_operator.aw - Operaator 
/*

@classinfo syslog_type=ST_MRP_RESOURCE_OPERATOR relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar

@default table=objects
@default group=general

@tableinfo aw_mrp_resource_operator index=id master_table=objects master_index=brother_of
@default table=aw_mrp_resource_operator

@property profession type=relpicker reltype=RELTYPE_PROFESSION 
@caption Ametinimetus

@property unit type=relpicker reltype=RELTYPE_UNIT
@caption &Uuml;ksus

@property resource type=relpicker multiple=1 reltype=RELTYPE_RESOURCE store=connect
@caption Ressurss

@property all_resources type=checkbox field=aw_all_resources
@caption N&auml;ita k&otilde;ikide ressurside t&ouml;id

@property all_section_resources type=checkbox field=aw_all_section_resources
@caption N&auml;ita osakonna ressurside t&ouml;id

@reltype PROFESSION value=1 clid=CL_CRM_PROFESSION
@caption Ametinimetus

@reltype RESOURCE value=2 clid=CL_MRP_RESOURCE
@caption Ressurss

@reltype UNIT value=3 clid=CL_CRM_SECTION
@caption Osakond

*/

class mrp_resource_operator extends class_base
{
	function mrp_resource_operator()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "mrp/mrp_resource_operator",
			"clid" => CL_MRP_RESOURCE_OPERATOR
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

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ("aw_mrp_resource_operator" === $table)
		{
			ini_set("ignore_user_abort", "1");

			switch($field)
			{
				case "aw_all_resources":
				case "aw_all_section_resources":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(1) UNSIGNED"
					));
					return true;
			}
		}
	}
}
?>
