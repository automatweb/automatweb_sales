<?php

class aw_translations extends aw_core_module
{
	public static function construct()
	{
		// translate class names
		if (defined("AW_REQUEST_UI_LANG_CODE"))
		{
			$trans_fn = aw_ini_get("translations_dir").AW_REQUEST_UI_LANG_CODE."/aw/aw.ini.aw";
			if (file_exists($trans_fn))
			{
				require_once($trans_fn);

				foreach ($GLOBALS["cfg"]["classes"] as $clid => $cld)
				{
					if (isset($cld["name"]) && ($_tmp = t2("Klassi ".$cld["name"]." ($clid) nimi")) != "")
					{
						$GLOBALS["cfg"]["classes"][$clid]["name"] = $_tmp;
					}

					if(isset($cld["prod_family"]) && ($_tmp = t2("Klassi tooteperekonna ".$cld["prod_family"]." ($clid) nimi")) != "")
					{
						$GLOBALS["cfg"]["classes"][$clid]["prod_family"] = $_tmp;
					}
				}

				foreach ($GLOBALS["cfg"]["classfolders"] as $clid => $cld)
				{
					if ($_tmp = t2("Klassi kataloogi {$cld["name"]} ({$clid}) nimi"))
					{
						$GLOBALS["cfg"]["classfolders"][$clid]["name"] = $_tmp;
					}
				}


				foreach ($GLOBALS["cfg"]["acl"]["names"] as $n => $cap)
				{
					if ($_tmp = t2("ACL tegevuse {$cap} ({$n}) nimi"))
					{
						$GLOBALS["cfg"]["acl"]["names"][$n] = $_tmp;
					}
				}

				foreach ($GLOBALS["cfg"]["languages"]["list"] as $laid => $ad)
				{
					if ($_tmp = t2("languages.list.{$ad["lc"]}"))
					{
						$GLOBALS["cfg"]["languages"]["list"][$laid]["name"] = $_tmp;
					}
				}
			}
		}
	}

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
		$lc = languages::lid2lc($lang_id);
		$set_name = basename($set_name);
		$trans_fn = aw_ini_get("translations_dir") . "{$lc}/aw/{$set_name }" . AW_FILE_EXT;
		if (file_exists($trans_fn))
		{
			require_once($trans_fn);
		}
	}

	/** Returns current translations languages
		@attrib api=1 params=pos
		@comment
		@returns array
			int aw language id => string language name
		@errors
	**/
	public static function lang_selection()
	{
		$lang_selection = array();
		$dir = aw_ini_get("translations_dir");
		if ($dh = opendir($dir))
		{
			while (false !== ($file = readdir($dh)))
			{
				$fn = $dir.$file;
				if (is_dir($fn) && $file !== "." && $file !== "..")
				{
					if ($lang_id = languages::lc2lid($file))
					{
						$lang_selection[$lang_id] = languages::lid2name($lang_id);
					}
				}
			}
		}
		return $lang_selection;
	}
}

aw_translations::construct();
