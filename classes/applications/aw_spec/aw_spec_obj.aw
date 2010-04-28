<?php

namespace automatweb;


class aw_spec_obj extends _int_object
{
	const AW_CLID = 1418;

	/**
		@attrib api=1
		@returns
			array { oid => class_object }
	**/
	public function spec_class_list()
	{
		$ol = new object_list(array(
			"class_id" => CL_AW_SPEC_CLASS,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $this->id(),
			"sort_by" => "objects.jrk"
		));
		return $ol->arr();
	}

	/**
		@attrib api=1 params=pos
		@param class_data required type=array
			array { oid => array { class_name => text, class_desc => text } }
	**/
	public function set_spec_class_list($class_data)
	{
		$cur_list = $this->spec_class_list();
		
		$class_data = $this->_get_array_data($class_data, "class_name");

		foreach($class_data as $idx => $cle)
		{
			// add new
			if (!is_oid($idx))
			{
				$tmp = $this->_add_class_entry($cle);
				$cur_list[$tmp->id()] = $tmp;
				$class_data[$tmp->id()] = $cle;
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
			if (!isset($class_data[$oid]))
			{
				$obj->delete();
			}
		}
	}



	public function  save_new_version()
	{
		$v = obj();
		$v->set_class_id(CL_AW_SPEC_VERSION);
		$v->set_parent($this->id());
		$v->set_name(sprintf(t("Spetsifikatsiooni %s versioon %s"), $this->name(), $this->get_current_version_number()));
		$v->set_prop("version_content", $this->instance()->_get_overview(obj($this->id())));
		$v->save();
	
		$this->connect(array(
			"to" => $v->id(),
			"type" => "RELTYPE_VERSION"
		));
		return $v;
	}

	public function get_current_version_number()
	{
		return max(1, (int)$this->meta("version_number"));
	}

	public function increment_version_number()
	{
		$v = $this->get_current_version_number() + 1;
		$this->set_meta("version_number", $v);
		$this->save();
		return $v;
	}


	/** filter data for empty entries **/
	private function _get_array_data($class_data, $key)
	{
		$rv = array();
		foreach(safe_array($class_data) as $idx => $cle)
		{
			if (trim($cle[$key]) != "")
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
		$o->set_class_id(CL_AW_SPEC_CLASS);
		$o->set_parent($this->id());
		$this->_upd_class_obj($o, $cle);
		return $o;
	}

	private function _upd_class_obj($o, $cle)
	{
		$o->set_name($cle["class_name"]);
		$o->set_ord($cle["jrk"]);
		$o->set_prop("desc", $cle["class_desc"]);
		$o->set_prop("pri", $cle["pri"]);
		$o->save();
	}

	public static function get_priority_options()
	{
		return array(
			1 => t("Obligatoorne"),
			2 => t("K&otilde;rge prioriteediga"),
			3 => t("Prioriteetne"),
			4 => t("Informatiivne"),
			5 => t("Oleks hea")
		);
	}
}

?>
