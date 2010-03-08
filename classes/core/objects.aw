<?php
// $Header: /home/cvs/automatweb_dev/classes/core/objects.aw,v 1.10 2009/04/30 09:51:04 markop Exp $
// objects.aw - objektide haldamisega seotud funktsioonid
/*
@classinfo  maintainer=kristo
*/
class objects extends core
{
	function objects()
	{
		$this->init();
	}

	/** Displays an object. Any object. 
		
		@attrib name=show params=name nologin="1" default="0"
		
		@param id required type=int
	**/
	function show($args = array())
	{
		extract($args);
		$ret = "";

		$o =&obj($id);
		$i = $o->instance();
		if (get_class($i) == "document")
		{
			$ret = $i->gen_preview(array(
				"docid" => $id
			));
		}
		else
		if (method_exists($i, "parse_alias"))
		{
			$ret = $i->parse_alias(array(
				"oid" => $id,
				"alias" => array("target" => $id)
			));
			if (is_array($ret))
			{
				$ret = $ret["replacement"];
			}
		}

		return $ret;
	}

	/**  
		@attrib name=db_query params=name default="0"
		
		@param sql required
	**/
	function orb_db_query($arr)
	{
		extract($arr);
		$ret = array();
		// only SELECT queries
		if (strtoupper(substr(trim($sql), 0, 6)) != "SELECT")
		{
			return NULL;
		}
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			if (isset($row["oid"]))
			{
				$this->save_handle();
				if (!$this->can("view", $row["oid"]))
				{
					$this->restore_handle();
					continue;
				}
				$this->restore_handle();
			}
			$ret[] = $row;
		}
		return $ret;
	}

	/**
		@attrib name=storage_query params=name all_args="1"
		@param name optional
		@param class_id optional type=int
		@param comment optional
		@param site_id optional
		@param createdby optional 
		@param modifiedby optional
		@param status optional type=int
		@param lang_id optional type=int
		@param oid optional type=int
	**/
	function storage_query($arr)
	{
		$arr["site_id"] = array();
		$arr["lang_id"] = array();
		$ol = new object_list($arr);
		$rv = array();
		foreach($ol->arr() as $o)
		{
			$rv[$o->id()] = array(
				"name" => $o->name(),
				"class_id" => $o->class_id(),
				"created" => $o->created(),
				"modified" => $o->modified(),
				"createdby" => $o->createdby(),
				"modifiedby" => $o->modifiedby(),
				"lang_id" => $o->lang(),
				"path_str" => htmlspecialchars($o->path_str()),
			);
		};
		return $rv;
	}

	/**  
		@attrib name=delete_object params=name default="0"
		
		@param oid required
	**/
	function orb_delete_object($arr)
	{
		extract($arr);
		$tmp = obj($oid);
		$tmp->delete();
	}

	/**
		@attrib name=get_bd
	**/
	function get_bd()
	{
		die(aw_ini_get("site_basedir"));
	}

	/**  
		@attrib name=aw_ini_get_mult params=name nologin="1" default="0"
		
		@param vals required
	**/
	function aw_ini_get_mult($arr)
	{
		extract($arr);
		$ret = array();
		foreach($vals as $vn)
		{
			$ret[$vn] = aw_ini_get($vn);
		}
		return $ret;
	}

	/** Object list
		
		@attrib name=get_list params=name default="0" nologin="1" all_args="1"
		
		@param ignore_langmenus optional
		@param empty optional
		@param rootobj optional type=int

		@comment
			returns list of id => name pairs for all menus
	**/
	function orb_get_list($arr)
	{
		if (is_array($arr))
		{
			extract($arr);
		}
		if (!isset($rootobj))
		{
			$rootobj = -1;
		}
		$ret = $this->get_menu_list($ignore_langmenus,$empty,$rootobj);
		return $ret;
	}

	/** serialize
		
		@attrib name=serialize params=name default="0" nologin="1" 
		
		@param oid required
		
		@comment
			serializes an object
	**/
	function orb_serialize($arr)
	{
		return parent::serialize($arr);
	}

	/** returns the object's data in xml
		@attrib api=1 params=name name=get_xml

		@param oid required type=oid
			object id

		@param encode optional type=boolean
			if set, encodes the data  with base64.

		@param copy_subobjects optional type=bool
			If true, all subobjects are also in the xml

		@param copy_subfolders optional type=bool
			If true, all subfolders are in the xml as well

		@param copy_subdocs optional type=bool
			If true, all documnents under the current object are in the xml as well

		@param copy_rels optional type=bool
			If true, connections for the object are copied, but not the objects they point to

		@param new_rels optional type=bool
			If true, connections from the objects are copied, and the objects they point to, are also copied

		@param no_header optional type=bool
			If true, returned xml string will not have xml header. Default false.

		@errors
			none

		@returns
			string containing xml that contains the object data

		@examples:
			$o = obj(1);

			$xml = $obj_inst->get_xml(array(
				"oid" => $o->id(),
				"copy_subobjects" => true,
				"new_rels" => true
			));

			$new_obj = object::from_xml($xml, 6); // copies all objects and their relations from object 1 to object 6
	**/
	function get_xml($options)
	{
		$o = obj($options["oid"]);
		$xml = $o->get_xml($options);
		if($options["encode"])
		{
			return base64_encode($xml);
		}
		return $xml;
	}
}

?>
