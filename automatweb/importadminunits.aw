<?php


$aw_dir = "/usr/lib/automatweb/automatweb_sales";
$site_dir = "/var/www/sales.automatweb.com";

// $aw_dir = "E:/htdocs/aw/automatweb";
// $site_dir = "E:/htdocs/aw/site";


$ojs_file = $aw_dir."/scripts/create_scripts/administrative_structures/ee.aw";
// $ojs_file = "/www/aqualife.automatweb.com/eesti_haldusjaotus.aw";

$ext_index_out_file = "/var/www/sales.automatweb.com/eesti_haldusjaotuse_ehakID2awoid_indeks.aw";
// $ext_index_out_file = "/www/aqualife.automatweb.com/eesti_haldusjaotus_ehak_indeks.aw";

// $parent_oid = 340537;
$parent_oid = 222;
// $country_oid = 340221;
$country_oid = 223;


ini_set("max_execution_time", "100000");
ini_set("memory_limit", "-1");
$cache_file = $site_dir . "/pagecache/ini.cache";
$cfg_files = array($aw_dir."/aw.ini", $site_dir."/aw.ini");
require_once($aw_dir."/automatweb.aw");
automatweb::start();
// automatweb::$instance->mode(automatweb::MODE_REASONABLE);
automatweb::$instance->mode(automatweb::MODE_DBG);
automatweb::$instance->bc();
automatweb::$instance->load_config_files($cfg_files, $cache_file);
require_once($ojs_file);
file_put_contents($ext_index_out_file, var_export($ext_index, true));
automatweb::shutdown();

