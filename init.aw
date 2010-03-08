<?php

/* DEPRECATED FILE */
// ! UUSI ARENDUSI SIIA POLE M6TET TEHA
// see on ainult selleks alles veel, et tagasiulatuvat yhilduvust
// vana startup skriptiga saitide const.aw-dele pakkuda.

if (!defined("AW_DIR"))
{
ini_set("memory_limit", "900M");
	// script for old site startup
	function init_config($args)
	{
		$cache_file = $args["cache_file"];
		$cfg_files = $args["ini_files"];
		
		foreach ($cfg_files as $file)
		{
			$file = dirname($file) . "/automatweb.aw";
			if (is_readable($file))
			{
				require_once($file);
				break;
			}
		}

		automatweb::start();
		automatweb::$instance->bc();
		automatweb::$instance->load_config_files($cfg_files, $cache_file);
		$request = aw_request::autoload();
		if(!empty($_GET["debug"]))
		{
			automatweb::$instance->mode(automatweb::MODE_DBG);
		}
		automatweb::$instance->set_request($request);
		automatweb::$instance->exec();
		automatweb::$result->send();
		automatweb::shutdown();
		exit;
	}
}

?>
