<?php

// this class just defines the protocol for obj data sources
// all of them should derive from this one. if php5 would
// be here, this would be an abstract class
//TODO: see on interface. teha selleks
class _int_obj_ds_base extends acl_base
{
	function init($args = array())
	{
		parent::init($args);
		// grmbl - acl_base class needs this.
		// normally it gets inited in aw_template::init
		// but since this class hooks into the inheritance tree before that,
		// we gots to init it here. and we don't want all the ini settings, cause they are not needed and take up much memory
		$this->cfg["acl"] = $GLOBALS["cfg"]["acl"];
	}

	////
	// !returns the oid that has the specified alias
	// parameters:
	//	alias - required
	//	site_id - optional
	//	parent - optional
	function get_oid_by_alias($arr)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::get_oid_by_alias")
		));
	}

	////
	// !returns all the object tabel data for the specified object
	// metadata must be unserialized
	function get_objdata($oid, $param = array())
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::get_objdata")
		));
	}

	////
	// !reads property data from the database
	// parameters:
	//	properties - property array
	//	tableinfo - tableinfo from propreader
	//	objdata - result of this::get_objdata
	function read_properties($arr)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::read_properties")
		));
	}

	////
	// !creates new object, returns object id
	// params:
	//	properties - prop array from propreader
	//	objdata - object data from objtable
	//	tableinfo - tableinfo from prop reader
	// returns:
	//	new oid
	function create_new_object($arr)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::create_new_object")
		));
	}

	////
	// !saves object properties, including all object table fields,
	// just stores the data, does not update or check it in any way,
	// except for db quoting of course
	// params:
	//	properties - prop array from propreader
	//	objdata - object data from objtable
	//	tableinfo - tableinfo from prop reader
	//	propvalues - property values
	function save_properties($arr)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::save_properties")
		));
	}

	////
	// !returns all data for connection $id
	function read_connection($id)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::read_connection")
		));
	}

	////
	// !saves connection
	function save_connection($data)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::save_connection")
		));
	}

	////
	// !deletes connection $id
	function delete_connection($id)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::delete_connection")
		));
	}


	////
	// !returns all connections that match filter
	function find_connections($arr)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::find_connections")
		));
	}

	////
	// !searches the database
	// params:
	//	array of filter parameters
	// if class id is present, properties can also be filtered, otherwise only object table fields
	function search($params)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::search")
		));
	}

	////
	// !deletes object $oid
	function delete_object($oid)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::delete_object")
		));
	}

	////
	// !make bro
	function create_brother($oid)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::create_brother")
		));
	}

	////
	// !fetch list of objects data given oid's and class id's
	function fetch_list($list)
	{
		error::raise(array(
			"id" => ERR_ABSTRACT,
			"msg" => t("called abstract function ds_base::fetch_list")
		));
	}
}
