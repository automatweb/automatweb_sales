<?php

$site_basedir = aw_ini_get("site_basedir");
$section = aw_global_get("section");

if (!empty($_REQUEST["class"])  || !empty($_REQUEST["reforb"]))
{
	include(aw_ini_get("classdir")."/".aw_ini_get("site_impl_dir")."/orb_impl_exec.".aw_ini_get("ext"));
}
else
{
	// if no orb call, do a normal pageview
	if (file_exists("{$site_basedir}/public/site_header.aw"))
	{
		include("{$site_basedir}/public/site_header.aw");
	}
	else
	{
		include(aw_ini_get("classdir")."/".aw_ini_get("site_impl_dir")."/site_header.".aw_ini_get("ext"));
	}
}

// get an instance if the site class
$si = __get_site_instance();
// if we are drawing the site's front page
if ((!$section || $section == aw_ini_get("frontpage")) && empty($class))
{
	// then do the right callback
	$content = $si->on_frontpage();
}
else
// and if we should
if (!aw_global_get("no_menus"))
{
	$m = new site_cache();
	$content = $m->show(array(
		"vars" => $si->on_page(),
		"text" => isset($content) ? $content : null,
		"docid" => isset($docid) ? $docid : null,
		"sub_callbacks" => $si->get_sub_callbacks(),
		"type" => isset($type) ? $type : null,
		"template" => $si->get_page_template()
	));
}

// and finish gracefully
if (file_exists("{$site_basedir}/public/site_footer.aw"))
{
	include("{$site_basedir}/public/site_footer.aw");
}
elseif (file_exists($site_basedir."/htdocs/site_footer.aw"))
{
	 include($site_basedir."/htdocs/site_footer.aw");
}
else
{
	include(aw_ini_get("classdir")."/".aw_ini_get("site_impl_dir")."/site_footer.".aw_ini_get("ext"));
}
