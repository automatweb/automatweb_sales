<?php

class aw_spec_class_obj extends _int_object
{
	/**
		@attrib api=1
		@returns
			array { oid => group_object }
	**/
	public function spec_group_list()
	{
		$ol = new object_list(array(
			"class_id" => CL_AW_SPEC_GROUP,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $this->id(),
			"sort_by" => "objects.jrk"
		));
		return $ol->arr();
	}

	/**
		@attrib api=1 params=pos
		@param group_data required type=array
			array { oid => array { group_name => text, parent_group_name => text } }
	**/
	public function set_spec_group_list($group_data)
	{
		$cur_list = $this->spec_group_list();
		
		$group_data = $this->_get_group_data($group_data);

		foreach($group_data as $idx => $cle)
		{
			// add new
			if (!is_oid($idx))
			{
				$tmp = $this->_add_class_entry($cle);
				$cur_list[$tmp->id()] = $tmp;
				$group_data[$tmp->id()] = $cle;
			}
			else
			// change old
			{
				$this->_upd_class_obj(obj($idx), $cle);
			}
		}
		
		// remove deleted
		foreach($cur_list as $oid => $obj)
		{
			if (!isset($group_data[$oid]))
			{
				$obj->delete();
			}
		}
	}

	/** Returns a list of relation objects from the current class
		@attrib api=1 

		@returns
			array { relation_oid => relation_object }
	**/
	function spec_relation_list()
	{
		$ol = new object_list(array(
			"class_id" => CL_AW_SPEC_RELATION,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $this->id(),
			"sort_by" => "objects.jrk"
		));
		return $ol->arr();
	}

	/** Sets the relation list for theis spec
		@attrib api=1 params=pos
		@param relation_data required type=array
			array { oid => array { rel_from => class_id, rel_name => text, rel_to => class_id } }
	**/
	public function set_spec_relation_list($relation_data)
	{
		$cur_list = $this->spec_relation_list();
		
		$relation_data = $this->_get_group_data($relation_data, "rel_name");

		foreach($relation_data as $idx => $cle)
		{
			// add new
			if (!is_oid($idx))
			{
				$tmp = $this->_add_relation_entry($cle);
				$cur_list[$tmp->id()] = $tmp;
				$relation_data[$tmp->id()] = $cle;
			}
			else
			// change old
			{
				$this->_upd_relation_obj(obj($idx), $cle);
			}
		}
		
		// remove deleted
		foreach($cur_list as $oid => $obj)
		{
			if (!isset($relation_data[$oid]))
			{
				$obj->delete();
			}
		}
	}

	private function _add_relation_entry($cle)
	{
		$o = obj();
		$o->set_class_id(CL_AW_SPEC_RELATION);
		$o->set_parent($this->id());
		$this->_upd_relation_obj($o, $cle);
		return $o;
	}

	private function _upd_relation_obj($o, $cle)
	{
		$o->set_name($cle["rel_name"]);
		$o->set_prop("rel_from", "new_".$o->id());
		$o->set_prop("rel_to", $cle["rel_to"]);
		$o->set_ord($cle["jrk"]);
		$o->save();
	}

	/** filter data for empty entries **/
	private function _get_group_data($class_data, $field = "group_name")
	{
		$rv = array();
		foreach(safe_array($class_data) as $idx => $cle)
		{
			if (trim($cle[$field]) != "")
			{
				$rv[$idx]  = $cle;
			}
		}
		return $rv;
	}

	/** create new spec class **/
	private function _add_class_entry($cle)
	{
		$o = obj();
		$o->set_class_id(CL_AW_SPEC_GROUP);
		$o->set_parent($this->id());
		$this->_upd_class_obj($o, $cle);
		return $o;
	}

	private function _upd_class_obj($o, $cle)
	{
		$o->set_name($cle["group_name"]);
		$o->set_comment($cle["parent_group_name"]);
		$o->set_ord($cle["jrk"]);
		$o->save();
	}
}

?>
