<?php

$s_salt = "this_is_a_salty_string_";
ob_start ("ob_gzhandler");
header ("content-type: text/css; charset: UTF-8");
header("Expires: ".gmdate("D, d M Y H:i:s", time()+43200)." GMT");
header("Cache-Control: max-age=315360000");

$fn = $s_salt.basename($_GET["name"]);
$hash = md5($fn);

$path = aw_ini_get("cache.page_cache").$hash[0]."/".$fn;

exit(file_get_contents(realpath($path)));
