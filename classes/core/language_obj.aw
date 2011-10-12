<?php

class language_obj extends _int_object
{
	const CLID = 266;

	public function save($check_state = false)
	{
		$aw_lid = $this->prop("aw_lang_id");
		if (!languages::lid2lc($aw_lid))
		{
			throw new awex_obj_state("Can't save language object that isn't based on any valid language");
		}

		if (!$this->is_saved())
		{
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

	/**
		@attrib api=1 params=pos
		@param lid type=int
		@comment
		@returns object(CL_LANGUAGE)
		@errors
			throws awex_obj_na if language for $lid not found
	**/
	public static function get_by_lid($lid)
	{
		$list = new object_list(array(
			"class_id" => self::CLID,
			"aw_lang_id" => $lid
		));

		if (!$list->count()) throw new awex_obj_na("Language for {$lid} not found");

		return $list->begin();
	}
}
