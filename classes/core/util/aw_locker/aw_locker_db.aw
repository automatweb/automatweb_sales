<?php

// supports only server and process boundaries
// timed locks not supported
// lock type is only 'full'. write lock requests become full locks.
// currently mysql based
class aw_locker_db extends db_connector implements aw_lock_interface
{
	private static $instance = null;

	private $wait_timeout = 10;
	private $pid;

	public function __construct()
	{
		$this->pid = uniqid(aw_ini_get("site_id"), true);
		$this->wait_timeout = aw_ini_get("aw_locker.wait_timeout");
		$this->init();
	}

	public static function instance()
	{
		if (self::$instance === null)
		{
			self::$instance = new aw_locker_db();
		}
		return self::$instance;
	}

	public static function lock($class, $id, $type, $scope, $wait_type, $release_time = 0)
	{
		if (aw_locker::SCOPE_PROCESS === $scope)
		{
			$instance = self::instance();
			$ident = $instance->get_ident($class, $id);
			$timeout = aw_locker::WAIT_BLOCK === $wait_type ? (string) $instance->wait_timeout : "0";
			$success = (bool) $instance->db_fetch_field("select GET_LOCK('{$ident}', {$timeout}) as success;", "success");
			if (!$success)
			{
				throw new aw_lock_exception("Lock timeout");
			}
		}
	}

	public static function unlock($class, $id, $scope = null)
	{
		if (null === $scope or aw_locker::SCOPE_PROCESS === $scope)
		{
			self::instance()->_unlock($class, $id);
		}
	}

	private function _unlock($class, $id)
	{
		$ident = $this->get_ident($class, $id);
		self::instance()->db_query("SELECT RELEASE_LOCK('{$ident}')");
	}

	public static function is_locked($class, $id, $operation = aw_locker::LOCK_FULL)
	{
		return self::instance()->_is_locked($class, $id);
	}

	private function _is_locked($class, $id)
	{
		$ident = $this->get_ident($class, $id);
		$locked = (bool) self::instance()->db_fetch_field("SELECT IS_USED_LOCK('{$ident}') as locked", "locked");
		return $locked;
	}

	private function get_ident($class, $id)
	{
		return "{$this->pid}::{$class}::{$id}";
	}
}

