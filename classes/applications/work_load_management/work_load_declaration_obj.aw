<?php

class work_load_declaration_obj extends _int_object
{
	protected $cache;

	public function get_declaration_entry_for_user()
	{
		if(!isset($this->cache))
		{
			$this->cache = $this->__get_declaration_entry_for_user();
		}

		return $this->cache;
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
