<?php

$basedir = realpath(".");
include($basedir . "/automatweb.aw");

automatweb::start();
//automatweb::$instance->mode(automatweb::MODE_DBG);
automatweb::$instance->bc();
$awt = new aw_timer();
aw_global_set("no_db_connection", 1);
aw_ini_set("baseurl", "automatweb");

if (in_array("--dbg", $argv))
{
	$GLOBALS["mk_dbg"] = 1;
}

$i = new pot_scanner();
if (in_array("--list-untranslated-strings", $argv))
{
	$i->list_untrans_strings();
}
elseif (in_array("--warn-only", $argv))
{
	$i->warning_scan();
}
elseif (in_array("--make-aw", $argv))
{
	$i->make_aw();
}
else
{
	$i->full_scan();
}

automatweb::shutdown();

?>
