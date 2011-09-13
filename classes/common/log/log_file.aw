<?php

class aw_log_file extends aw_log
{
	private static $fp;
	public static function write(array $fields)
	{
		fputcsv($fp, $fields);
	}
}
