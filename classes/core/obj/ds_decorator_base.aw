<?php

class _int_obj_ds_decorator
{
	protected $contained;	// the contained data source object

	function _int_obj_ds_decorator($contained)
	{
		$this->contained = $contained;
	}

	////
	// !returns the oid that has the specified alias
	// parameters:
	//	alias - required
	//	site_id - optional
	//	parent - optional
	function get_oid_by_alias($arr)
	{
		return $this->contained->get_oid_by_alias($arr);
	}

	////
	// !returns all the object tabel data for the specified object
	// metadata must be unserialized
	function get_objdata($oid, $param = array())
	{
		return $this->contained->get_objdata($oid, $param);
	}

	////
	// !reads property data from the database
	// parameters:
	//	properties - property array
	//	tableinfo - tableinfo from propreader
	//	objdata - result of this::get_objdata
	function read_properties($arr)
	{
		return $this->contained->read_properties($arr);
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
		return $this->contained->create_new_object($arr);
	}

	////
	// !saves object properties, including all object table fields,
	// params:
	//	properties - prop array from propreader
	//	objdata - object data from objtable
	//	tableinfo - tableinfo from prop reader
	//	propvalues - property values
	function save_properties($arr)
	{
		return $this->contained->save_properties($arr);
	}

	////
	// !returns all data for connection $id
	function read_connection($id)
	{
		return $this->contained->read_connection($id);
	}

	////
	// !saves connection
	function save_connection($data)
	{
		return $this->contained->save_connection($data);
	}

	////
	// !deletes connection $id
	function delete_connection($id)
	{
		return $this->contained->delete_connection($id);
	}


	////
	// !returns all connections that match filter
	function find_connections($arr)
	{
		return $this->contained->find_connections($arr);
	}

	////
	// !searches the database
	// params:
	//	array of filter parameters
	// if class id is present, properties can also be filtered, otherwise only object table fields
	function search($params)
	{
		return $this->contained->search($params);
	}

	function can($a, $b)
	{
		return $this->contained->can($a,$b);
	}

	function delete_object($oid)
	{
		return $this->contained->delete_object($oid);
	}

	function delete_multiple_objects($oid_list)
	{
		return $this->contained->delete_multiple_objects($oid_list);
	}

	function dequote(&$value)
	{
		$this->contained->dequote($value);
	}

	function quote(&$value)
	{
		$this->contained->quote($value);
	}

	function create_brother($arr)
	{
		return $this->contained->create_brother($arr);
	}

	function fetch_list($arr)
	{
		return $this->contained->fetch_list($arr);
	}

	function final_delete_object($arr)
	{
		return $this->contained->final_delete_object($arr);
	}

	function backup_current_version($arr)
	{
		return $this->contained->backup_current_version($arr);
	}

	function originalize($oid)
	{
		return $this->contained->originalize($oid);
	}

	function save_properties_cache_update($oid, $propagate = false)
	{
		if ($propagate)
		{
			return $this->contained->save_properties_cache_update($oid);
		}
	}

	function create_new_object_cache_update($oid, $propagate = false)
	{
		if ($propagate)
		{
			$this->contained->create_new_object_cache_update($oid);
		}
	}

	function create_brother_cache_update($oid, $propagate = false)
	{
		if ($propagate)
		{
			$this->contained->create_brother_object_cache_update($oid);
		}
	}

	function save_connection_cache_update($oid, $propagate = false)
	{
		if ($propagate)
		{
			$this->contained->save_connection_cache_update($oid);
		}
	}

	function delete_connection_cache_update($oid, $propagate = false)
	{
		if ($propagate)
		{
			$this->contained->delete_connection_cache_update($oid);
		}
	}

	function delete_object_cache_update($oid, $propagate = false)
	{
		if ($propagate)
		{
			$this->contained->delete_object_cache_update($oid);
		}
	}

	function originalize_cache_update($oid, $propagate = false)
	{
		if ($propagate)
		{
			$this->contained->originalize_cache_update($oid);
		}
	}

	function compile_oql_query($oql)
	{
		return $this->contained->compile_oql_query($oql);
	}

	function execute_oql_query($oql)
	{
		return $this->contained->execute_oql_query($oql);
	}

	function __call($method, $arguments)
	{
		return call_user_func_array(array($this->contained, $method), $arguments);
	}
}
