<?php

$basedir = realpath(".");
require_once $basedir . "/automatweb.aw";

automatweb::start();
automatweb::$instance->bc();
//automatweb::$instance->mode(automatweb::MODE_DBG);
aw_global_set("no_db_connection", 1);
aw_ini_set("baseurl", "automatweb");
$orb_gen = new orb_gen();
$orb_gen->make_orb_defs_from_doc_comments();
automatweb::shutdown();

?>
