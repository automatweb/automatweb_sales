<?php

include(aw_ini_get("classdir").aw_ini_get("site_impl_dir")."site_header".AW_FILE_EXT);

if (($port = aw_ini_get("auth.display_over_ssl_port")) > 0)
{
	if (!$_SERVER["HTTPS"])
	{
		$bits = parse_url(aw_ini_get("baseurl"));
		header("Location: https://".$bits["host"].":".$port.aw_global_get("REQUEST_URI"));
		die();
	}
}

$te = new aw_template();
$te->init("");
$te->read_template("login.tpl");
lc_site_load("login", $te);
// if there is an auth config then get the list of servers to add
$ac = get_instance(CL_AUTH_CONFIG);

$te->vars(array(
	"uid" => isset($_GET["uid"]) ? $_GET["uid"] : ""
));

if (is_oid($ac_id = auth_config::has_config()))
{
	$sl = $ac->get_server_ext_list($ac_id);


	$te->vars(array(
		"servers" => $te->picker(-1, $sl),
	));
	if (count($sl))
	{
		$te->vars(array(
			"SERVER_PICKER" => $te->parse("SERVER_PICKER")
		));
	}
}

$m = new site_cache();

$si = __get_site_instance();
$tfl = "";

if(!empty($_SESSION["text_for_login"]))
{
	$te->vars(array(
		"logintext" => $_SESSION["text_for_login"],
	));
	$tfl = $te->parse("TEXT_FOR_LOGIN");
}
$te->vars(array(
	"uid" => empty($_SESSION["uid_for_login"]) ? (empty($_GET["uid"]) ? "" : $_GET["uid"]) : $_SESSION["uid_for_login"],
	"TEXT_FOR_LOGIN" => $tfl,
));

$content = $m->show(array(
	"vars" => $si ? $si->on_page() : "",
	"text" => $te->parse(),
	"no_right_pane" => (int) !empty($content),
	"sub_callbacks" => $si ? $si->get_sub_callbacks() : ""
));
aw_session_set("text_for_login", "");
aw_session_set("uid_for_login", "");

if (file_exists(aw_ini_get("site_basedir")."public/site_footer.aw"))
{
	include(aw_ini_get("site_basedir")."public/site_footer.aw");
}
else
{
	include(aw_ini_get("classdir").aw_ini_get("site_impl_dir")."site_footer".AW_FILE_EXT);
}
