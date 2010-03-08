<?php
if (!empty($_REQUEST["class"])  || !empty($_REQUEST["reforb"]))
{
	include(aw_ini_get("classdir")."/".aw_ini_get("site_impl_dir")."/orb_impl_exec.".aw_ini_get("ext"));
}
else
{
	// if no orb call, do a normal pageview
	if (file_exists(aw_ini_get("site_basedir")."/public/site_header.aw"))
	{
		include(aw_ini_get("site_basedir")."/public/site_header.aw");
	}
	else
	{
		include(aw_ini_get("classdir")."/".aw_ini_get("site_impl_dir")."/site_header.".aw_ini_get("ext"));
	}
}
enter_function("index_impl::after_init");
// get an instance if the site class
$si =&__get_site_instance();
// if we are drawing the site's front page
if ((!aw_global_get("section") || aw_global_get("section") == aw_ini_get("frontpage")) && empty($class))
{
	// then do the right callback
	$content = $si->on_frontpage();
}
else
// and if we should
if (!aw_global_get("no_menus"))
{
	$m = get_instance("contentmgmt/site_cache");
	$content = $m->show(array(
		"vars" => $si->on_page(),
		"text" => isset($content) ? $content : null,
		"docid" => isset($docid) ? $docid : null,
		"sub_callbacks" => $si->get_sub_callbacks(),
		"type" => isset($type) ? $type : null,
		"template" => $si->get_page_template()
	));
}

exit_function("index_impl::after_init");
enter_function("index_impl::shutdown");
// and finish gracefully
if (file_exists(aw_ini_get("site_basedir")."/public/site_footer.aw"))
{
	include(aw_ini_get("site_basedir")."/public/site_footer.aw");
}
else
if (file_exists(aw_ini_get("site_basedir")."/htdocs/site_footer.aw"))
{
	 include(aw_ini_get("site_basedir")."/htdocs/site_footer.aw");
}
else
{
	include(aw_ini_get("classdir")."/".aw_ini_get("site_impl_dir")."/site_footer.".aw_ini_get("ext"));
}
?>
