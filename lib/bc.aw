<?php

require_once AW_DIR . "lib/defs" . AW_FILE_EXT;

/** Loads localization variables
@param file required type=string
**/
function lc_load($file)
{
	$admin_lang_lc = aw_global_get("admin_lang_lc");
	if (!empty($admin_lang_lc))
	{
		$fn = AW_DIR . "lang/{$admin_lang_lc}/{$file}" . AW_FILE_EXT;

		if (!is_readable($fn))
		{
			throw new aw_exception("Language locale file not readable");
		}

		include_once($fn);
	}
}

function aw_redirect(aw_uri $url)
{
	$content = $url->get();
	if (headers_sent())
	{
		exit("\n<br />" . html::href(array(
			"url" => $content,
			"caption" => t("Kliki siia j&auml;tkamiseks"),
		)) . "\n<br />");
	}
	else
	{
		header("Location: {$content}");
		exit;
	}
}
