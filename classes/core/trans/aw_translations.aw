<?php

class aw_translations
{
	/** Loads translations
		@attrib api=1 params=pos
		@param set_name type=string default=""
			Translations set name to load
		@param lang_id type=int default=AW_LANGUAGES_DEFAULT_UI_LID
		@comment
			If translation set not found, loads nothing.
		@returns void
		@errors
	**/
	public static function load($set_name, $lang_id = AW_LANGUAGES_DEFAULT_UI_LID)
	{
		$lc = languages::get_default_code_for_id($lang_id);
		$set_name = basename($set_name);
		$trans_fn = AW_DIR . "lang/trans/{$lc}/aw/{$set_name }" . AW_FILE_EXT;
		if (file_exists($trans_fn))
		{
			require_once($trans_fn);
		}
	}
}
