<?php

class aw_spec_group_obj extends _int_object
{
	const CLID = 1426;

	/**
		@attrib api=1
		@returns
			array { oid => group_object }
	**/
	public function spec_layout_list()
	{
		$ol = new object_list(array(
			"class_id" => CL_AW_SPEC_LAYOUT,
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
			array { oid => array { layout_name => text, parent_layout_name => text, layout_type => [vbox|hbox] } }
	**/
	public function set_spec_layout_list($group_data)
	{
		$cur_list = $this->spec_layout_list();
		
		$group_data = $this->_get_layout_data($group_data);

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

	/** filter data for empty entries **/
	private function _get_layout_data($class_data)
	{
		$rv = array();
		foreach(safe_array($class_data) as $idx => $cle)
		{
			if (trim($cle["layout_name"]) != "")
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
		$o->set_class_id(CL_AW_SPEC_LAYOUT);
		$o->set_parent($this->id());
		$this->_upd_class_obj($o, $cle);
		return $o;
	}

	private function _upd_class_obj($o, $cle)
	{
		$o->set_name($cle["layout_name"]);
		$o->set_prop("layout_type", $cle["layout_type"]);
		$o->set_prop("parent_layout_name", $cle["parent_layout_name"]);
		$o->set_ord($cle["jrk"]);
		$o->save();
	}

	/** 
		@attrib api=1
		@returns
			array { layout type => layout type desc }
	**/
	static public function spec_layout_type_picker()
	{
		return array(
			"hbox" => t("Horisontaalne kast"),
			"vbox" => t("Vertikaalne kast")
		);
	}
}

?>
