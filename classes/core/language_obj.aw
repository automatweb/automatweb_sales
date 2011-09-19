<?php

class language_obj extends _int_object
{
	const CLID = 266;

	public function save($check_state = false)
	{
		$aw_lid = $this->prop("aw_lang_id");
		if (!aw_ini_isset("languages.list.{$aw_lid}.lc"))
		{
			throw new awex_obj_state("Can't save language object that isn't based on any valid language");
		}

		if (!$this->is_saved())
		{
			$this->set_prop("lang_charset", aw_ini_get("languages.list.{$aw_lid}.charset"));
			$this->set_prop("lang_acceptlang", aw_ini_get("languages.list.{$aw_lid}.acceptlang"));
			$this->set_prop("lang_sel_lang", aw_ini_get("languages.list.{$aw_lid}.lc"));

			if (!$this->prop("lang_name"))
			{
				$this->set_prop("lang_name", aw_ini_get("languages.list.{$aw_lid}.name"));
			}
		}

		$this->set_name($this->prop("lang_name"));
		$r = parent::save($check_state);
		languages::init_cache(true);
		return $r;
	}
}
