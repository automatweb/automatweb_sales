<?php

// what this script does, is take the starting file from the command line, reads that file
// and replaces all include commands in that file with the contents of the file that is included
$basedir = realpath(".");
require_once($basedir . "/automatweb.aw");
aw_global_set("no_db_connection", 1);
automatweb::start();
automatweb::$instance->bc();

aw_global_set("no_db_connection", 1);
aw_ini_set("baseurl", "automatweb");

$stderr = fopen('php://stderr', 'w');

require_once("$basedir/scripts/ini/parse_config_to_ini.aw");

if ($_SERVER["argc"] < 1 || !file_exists($_SERVER["argv"][1]))
{
	echo "usage: php -q mk_ini.aw aw.ini.root \n\n";
	echo "\toutputs the ini file with the include directives replaced with the file contents\n\n";
	automatweb::shutdown();
	exit(1);
}

$basedir = dirname($_SERVER["argv"][1]);
$GLOBALS["cfg"]["basedir"] = $basedir;
$res = parse_config_to_ini($_SERVER["argv"][1]);

if ($res === false)
{
	automatweb::shutdown();
	exit(1);
}
else
{
	echo $res;
}

automatweb::shutdown();

?>
