<?php

$basedir = realpath(".");
include($basedir . "/automatweb.aw");

automatweb::start();
//automatweb::$instance->mode(automatweb::MODE_DBG);
automatweb::$instance->bc();
$awt = new aw_timer();
aw_global_set("no_db_connection", 1);
aw_ini_set("baseurl", "automatweb");
$scanner = new msg_scanner();
$scanner->scan();
automatweb::shutdown();

?>
