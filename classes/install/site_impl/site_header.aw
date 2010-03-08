<?php

if (empty($_COOKIE["nocache"]) && aw_ini_get("config.use_squid"))
{
/*	$ma = aw_ini_get("config.http_cache_max_age");
    session_cache_limiter("must-revalidate, max-age=".$ma);
	header("Cache-Control: must-revalidate, max-age=".$ma);
	header("Expires: ".gmdate("D, d M Y H:i:s",time()+$ma)." GMT");*/
}


ini_set("session.save_handler", "files");
session_name("automatweb");
session_start();
lc_init();

$awt = new aw_timer();
enter_function("site_header::aw_startup");
aw_startup();
exit_function("site_header::aw_startup");
// oughta put this in aw_startup() as well, but it is used in so many places
// in the code that I just don't have the time do deal with that right now
global $section;

if (!$section)
{
	$section = aw_ini_get("frontpage");
	aw_global_set("section", $section);
}

?>
