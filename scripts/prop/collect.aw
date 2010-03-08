<?php

$basedir = realpath(".");
include($basedir . "/automatweb.aw");

automatweb::start();
automatweb::$instance->bc();

// automatweb::$instance->mode(automatweb::MODE_DBG);
aw_global_set("no_db_connection", 1);
aw_ini_set("baseurl", "automatweb");
$collector = new propcollector();
$collector->run();

automatweb::shutdown();

?>
