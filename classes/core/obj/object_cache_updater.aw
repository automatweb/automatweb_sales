<?php

class object_cache_updater extends core
{
	function object_cache_updater()
	{
		$this->init();
	}

	/**
		@attrib name=handle_remote_update nologin=1
		@param data required
	**/
	function handle_remote_update($arr)
	{
		$f = fopen(aw_ini_get("site_basedir")."/files/handler.log", "a");
		fwrite($f, date("d.m.Y H:i:s").": update starting\n");
		foreach(safe_array($arr["data"]) as $oid => $types)
		{
			foreach($types as $type)
			{
				$fn = $type."_cache_update";
				fwrite($f, "call $fn with param $oid \n");
				fflush($f);
				$GLOBALS["object_loader"]->ds->$fn($oid, true);
			}
		}
		fwrite($f, date("d.m.Y H:i:s").": update done\n");
		fclose($f);
	}
}
