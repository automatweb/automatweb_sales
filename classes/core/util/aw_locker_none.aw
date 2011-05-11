<?php

class aw_locker_none implements aw_lock_interface
{
	private static $instance = null;//TODO get rid of

	public static function instance()//TODO: get rid of
	{
		if (self::$instance === null)
		{
			self::$instance = new aw_locker();
		}
		return self::$instance;
	}

	public static function lock($class, $id, $type, $boundary, $wait_type, $release_time = 0)
	{
	}

	public static function unlock($class, $id, $boundary = null)
	{
	}

	public static function try_operation($class, $id, $try_type = self::OPERATION_READ, $wait_type = null)
	{
	}

	public static function is_locked($class, $id)
	{
		return false;
	}
}

