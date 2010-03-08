<?php

$aw_dir = "{VAR:aw_dir}";
$site_dir = "{VAR:site_dir}";
$cache_file = $site_dir . "/pagecache/ini.cache";
$cfg_files = array($site_dir . "/aw.ini");

require_once($aw_dir . "/automatweb.aw");

automatweb::start();
// automatweb::$instance->mode(automatweb::MODE_DBG);
automatweb::$instance->bc();
automatweb::$instance->load_config_files($cfg_files, $cache_file);
$request = aw_request::autoload();
automatweb::$instance->set_request($request);
automatweb::$instance->exec();
automatweb::$result->send();
automatweb::shutdown();

?>
