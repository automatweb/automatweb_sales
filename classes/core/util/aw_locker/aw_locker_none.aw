<?php

class aw_locker_none implements aw_lock_interface
{
	public static function lock($class, $id, $type, $scope, $wait_type, $release_time = 0)
	{
	}

	public static function unlock($class, $id, $scope = null)
	{
	}

	public static function is_locked($class, $id, $operation = aw_locker::LOCK_WRITE)
	{
		return false;
	}
}

