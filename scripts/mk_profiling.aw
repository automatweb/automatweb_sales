<?php

$basedir = realpath(".");
include($basedir . "/automatweb.aw");

automatweb::start();
//automatweb::$instance->mode(automatweb::MODE_DBG);
automatweb::$instance->bc();
$awt = new aw_timer();
aw_global_set("no_db_connection", 1);
aw_ini_set("baseurl", "automatweb");
aw_set_exec_time(AW_LONG_PROCESS);

$anal = new parser();

// create a copy of the code tree and add enters to that
$new_name = dirname(aw_ini_get("basedir"))."/".basename(aw_ini_get("basedir"))."_profiled";
$cmd = "rm -rf $new_name";
echo "removing old profiled code from $new_name \n";
echo $cmd."\n";
$res = `$cmd`;

echo "making a copy of the current code\n";
$cmd = "cp -r ".aw_ini_get("basedir")." $new_name ";
echo $cmd."\n";
$res = `$cmd`;

echo "adding profiling calls to the code\n";
$files = array();
$anal->_get_class_list(&$files, $new_name);

foreach($files as $file)
{
	echo "process $file \n";
	$anal->do_parse($file);
	$anal->add_enter_func($file);
}

automatweb::shutdown();

?>
