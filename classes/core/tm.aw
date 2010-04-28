<?php

namespace automatweb;

class tm
{
	private static $r_start;
	private static $tms = array();

	public static function s($class, $func, $desc = "")
	{
		if (!isset(self::$tms[$class][$func][$desc]) || !is_array(self::$tms[$class][$func][$desc]))
		{
			self::$tms[$class][$func][$desc] = array(
				"tm" => 0,
				"start" => microtime(true),
				"end" => 0
			);
		}
		else
		{
			self::$tms[$class][$func][$desc]["start"] = microtime(true);
		}
	}

	public static function e($class, $func, $desc = "")
	{
		self::$tms[$class][$func][$desc]["end"] = microtime(true);
		self::$tms[$class][$func][$desc]["tm"] += self::$tms[$class][$func][$desc]["end"] - self::$tms[$class][$func][$desc]["start"];
	}

	public static function request_start()
	{
		self::$r_start = microtime(true);
		register_shutdown_function("tm::request_end");
	}

	public static function request_end()
	{
		$r_end = microtime(true);
		$r_len = $r_end - self::$r_start;

		$pt = aw_ini_get("site_basedir")."/files/timers";
		if (!is_dir($pt))
		{
			mkdir($pt);
			chmod($pt, 0777);
		}
		$log = fopen($pt."/tm-".date("Y-m-d").".log", "a");
		fwrite($log, date("d.m.Y H:i:s").": request ".$r_len."\n");
		foreach(self::$tms as $class => $cld)
		{
			foreach($cld as $func => $data)
			{
				foreach($data as $desc => $row)
				{
					fwrite($log, $class."|".$func."|".$desc."|".$row["tm"]."\n");
				}
			}
		}
		fclose($log);
	}
}
