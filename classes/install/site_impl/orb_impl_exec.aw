<?php

$vars = automatweb::$request->get_args();
$class = empty($vars["class"]) ? "" : $vars["class"];
$action = empty($vars["action"]) ? "" : $vars["action"];

include(aw_ini_get("classdir").aw_ini_get("site_impl_dir")."site_header".AW_FILE_EXT);

$orb = new orb();
$orb->process_request(array(
	"class" => $class,
	"action" => $action,
	"reforb" => isset($vars["reforb"]) ? $vars["reforb"] : null,
	"user"	=> 1,
	"vars" => $vars,
	"silent" => false
));
$content = $orb->get_data();


// et kui orb_data on link, siis teeme ymbersuunamise
// see ei ole muidugi parem lahendus. In fact, see pole yldse
// mingi lahendus
if (substr($content,0,5) === "http:" || !empty($vars["reforb"]) || substr($content,0,6) === "https:")
{
	$return_url = $content;
}
elseif (is_oid($content))
{
	$return_url = aw_ini_get("baseurl") . "{$content}";
}

if (headers_sent())
{
	print html::href(array(
		"url" => $return_url,
		"caption" => t("Kliki siia j&auml;tkamiseks")
	));
}
else
{
	header("Location: {$return_url}");
}
exit;
