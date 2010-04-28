<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/core/abstract_datasource.aw,v 1.11 2008/01/31 13:52:49 kristo Exp $
// abstract_datasource.aw - Andmeallikas 
/*

@classinfo syslog_type=ST_ABSTRACT_DATASOURCE relationmgr=yes maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property ds type=relpicker reltype=RELTYPE_DS 
@caption Andmed objektist

@property row_cnt type=text store=no
@caption Mitu rida andmeid

@property name_field type=select 
@caption Nime tulp

@property id_field type=select 
@caption ID tulp

------------------- CSV FILE class options
@property file_has_header type=checkbox ch_value=1
@caption Esimesel real on pealkirjad

@property file_separator type=textbox size=5
@caption Eraldaja

@property max_lines type=textbox size=5
@caption Mitu rida maksimaalselt importida

@property controller type=relpicker reltype=RELTYPE_CTR
@caption Kontroller


@reltype DS value=1 clid=CL_FILE,CL_OTV_DS_POSTIPOISS,CL_OTV_DS_OBJ,CL_DB_TABLE_CONTENTS
@caption andmed objektist

@reltype CTR value=2 clid=CL_FORM_CONTROLLER
@caption Kontroller
*/

class abstract_datasource extends class_base
{
	const AW_CLID = 339;

	function abstract_datasource()
	{
		$this->init(array(
			"tpldir" => "core/abstract_datasource",
			"clid" => CL_ABSTRACT_DATASOURCE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "file_has_header":
			case "file_separator":
				if (!is_oid($arr["obj_inst"]->prop("ds")))
				{
					return PROP_IGNORE;
				}
				$dso = obj($arr["obj_inst"]->prop("ds"));
				if ($dso->class_id() != CL_FILE)
				{
					$retval = PROP_IGNORE;
				}

				if ($prop["name"] == "file_separator" && $prop["value"] == "")
				{
					$prop["value"] = ",";
				}
				break;

			case "row_cnt":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					return PROP_INGORE;
				}
				$prop["value"] = count($this->get_objects($arr["obj_inst"]));
				break;

			case "name_field":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					return PROP_INGORE;
				}
				$prop["options"] = $this->get_fields($arr["obj_inst"]);
				break;

			case "id_field":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					return PROP_INGORE;
				}
				$prop["options"] = $this->get_fields($arr["obj_inst"]);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	

	// submenus-from-object support functions

	function get_folders_as_object_list($object, $level, $parent_o)
	{
		$folders = $this->get_folders($object);
		$ol = new object_list();
		foreach($folders as $fld)
		{		
			$i_o = obj();	//obj($fld["id"]);
			$i_o->set_parent($parent_o->id());
			$i_o->set_class_id(CL_ABSTRACT_DATA_CLASS);
			$i_o->set_name($fld[$object->prop("name_field")]);
			
			$ol->add($i_o);
		}

		echo dbg::dump($ol);
		return $ol;
	}

	////////////////////////////////////////////////////////////
	// data access interface
	////////////////////////////////////////////////////////////
	
	/** returns an array of field id => example text for all the fileds in the datasource
	**/
	function get_fields($o, $sel_file = NULL)
	{
		if (!$o->prop("ds"))
		{
			return array();
		}
		// get the real datasource 	
		$l = $o->prop("ds");
		if (!$this->can("view",$l))
		{
			return array();
		};
			
		$ds_o = obj($o->prop("ds"));
		$ds_i = $ds_o->instance();

		$params = array();
		if ($ds_o->class_id() == CL_FILE)
		{
			$params["separator"] = $o->prop("file_separator");
			if (is_oid($sel_file) && $this->can("view", $sel_file))
			{
				$ds_o = obj($sel_file);
			}
		}

		return $ds_i->get_fields($ds_o, $params);
	}

	/** returns an array of data rows

		@comment
			data rows are arrays, keys are the same as keys returned from get_fields()
	**/
	function get_objects($o, $sel_file = NULL)
	{
		if (!$o->prop("ds"))
		{
			return array();
		}
		$ds_o = obj($o->prop("ds"));
		$ds_i = $ds_o->instance();

		$params = array();
		if ($ds_o->class_id() == CL_FILE)
		{
			$params["separator"] = $o->prop("file_separator");
			$params["file_has_header"] = $o->prop("file_has_header");

			if (is_oid($sel_file) && $this->can("view", $sel_file))
			{
				$ds_o = obj($sel_file);
			}
		}

		$ret = $ds_i->get_objects($ds_o, $params);

		if ($o->prop("max_lines"))
		{
			$cnt = 0;
			$tmp = array();
			foreach($ret as $k => $v)
			{
				$tmp[$k] = $v;
				if (++$cnt > $o->prop("max_lines"))
				{
					break;
				}
			}
			$ret = $tmp;
		}

		if ($this->can("view", $o->prop("controller")))
		{
			$tmp = array();
			$ci = get_instance(CL_FORM_CONTROLLER);
			$c_id = $o->prop("controller");
			foreach($ret as $k => $v)
			{
				if ($ci->eval_controller($c_id, $v, $o))
				{
					$tmp[$k] = $v;
				}
			}
			$ret = $tmp;
		}

		return $ret;
	}

	/** returns an array of folders from the datasource
	**/
	function get_folders($o)
	{
		if (!$o->prop("ds"))
		{
			return array();
		}
		$ds_o = obj($o->prop("ds"));
		$ds_i = $ds_o->instance();

		$ret = $ds_i->get_objects($ds_o);
		return $ret;
	}
}
?>
