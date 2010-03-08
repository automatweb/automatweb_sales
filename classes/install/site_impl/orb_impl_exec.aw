<?php

$vars = automatweb::$request->get_args();
if (!empty($vars["class"]))
{
	$class = $vars["class"];
}
else
{
	$class = "";
}

// I'll burn in hell for this
if (!$class)
{
	$class = $vars["alias"];
}

if (!empty($vars["action"]))
{
	$action = $vars["action"];
}

if (isset($vars['fastcall']) && $vars["fastcall"] == 1)
{
	session_name("automatweb");
	session_start();
	// loadime klassi
	classload("core/fastcall_base");
	if (!class_exists("class_base"))
	{
		classload("class_base");
	}
	// instantseerime
	classload($class);
	$inst = new $class;
	// ja ongi k6ik
	die($inst->$action($vars));
}

include(aw_ini_get("classdir")."/".aw_ini_get("site_impl_dir")."/site_header.".aw_ini_get("ext"));
register_shutdown_function("log_pv", $GLOBALS["awt"]->timers["__global"]["started"]);
enter_function("orb_impl_exec::process_request");
classload("core/orb/orb");
$orb = new orb();
$orb->process_request(array(
	"class" => isset($_POST["class"]) ? $_POST["class"] : $_GET["class"],
	"action" => isset($_POST["action"]) ? $_POST["action"] : $_GET["action"],
	"reforb" => isset($vars["reforb"]) ? $vars["reforb"] : null,
	"user"	=> 1,
	"vars" => $vars,
	"silent" => false,
));
$content = $orb->get_data();
exit_function("orb_impl_exec::process_request");

// et kui orb_data on link, siis teeme ymbersuunamise
// see ei ole muidugi parem lahendus. In fact, see pole yldse
// mingi lahendus
if (substr($content,0,5) == "http:" || !empty($vars["reforb"]) || substr($content,0,6) == "https:")
{
	if (headers_sent())
        {
                print html::href(array(
                        "url" => $content,
                        "caption" => t("Kliki siia j&auml;tkamiseks"),
                ));
        }
        else
        {
                header("Location: $content");
                print "\n\n";
        }
        exit;
}

?>
