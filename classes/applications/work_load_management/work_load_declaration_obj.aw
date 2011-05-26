<?php

class work_load_declaration_obj extends _int_object
{
	const CLID = 1773;

	protected $cache;

	public function get_declaration_entry_for_user()
	{
		if(!isset($this->cache))
		{
			$this->cache = $this->__get_declaration_entry_for_user();
		}

		return $this->cache;
	}

	public function get_declaration_entry_by_id($id)
	{
		if(is_oid($id))
		{
			$o = obj($id);
		}
		else
		{
			$o = new object();
			$o->set_class_id(CL_WORK_LOAD_DECLARATION_ENTRY);
			$o->set_parent($this->id());
			$o->name = sprintf(t("'%s' sisestus"), $this->name());
		}

		return $o;
	}

	protected function __get_declaration_entry_for_user()
	{
		$ol = new object_list(array(
			"class_id" => CL_WORK_LOAD_DECLARATION_ENTRY,
			"user" => aw_global_get("uid_oid"),
			"parent" => $this->id(),
		));

		if($ol->count() > 0)
		{
			$o = $ol->begin();
		}
		else
		{
			$o = new object();
			$o->set_class_id(CL_WORK_LOAD_DECLARATION_ENTRY);
			$o->set_parent($this->id());
			$o->user = aw_global_get("uid_oid");
			$o->name = sprintf(t("'%s' kasutajale %s"), $this->name(), aw_global_get("uid"));
		}
		return $o;
	}
}
