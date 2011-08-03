<?php

class aw_locker_sysv
{
	const SEM_ID = 15000;
	const SHM_KEY = 15000;
	const SHM_VAR = 15;

	private static $sem_id = null;
	private static $shm_id = null;

	public static function lock($class, $id, $type, $scope, $wait_type)
	{
		self::_block();
		if (!self::_is_locked($class, $id))
		{
			$ident  = self::get_ident($class, $id);
			switch($scope)
			{
				case aw_locker::SCOPE_PROCESS:
					if (self::$shm_id === null)
					{
						self::$shm_id = shm_attach(self::SHM_KEY, 50 * 1024, 0666);
					}
					$locks = shm_get_var(self::$shm_id, self::SHM_VAR);
					if (!is_array($locks))
					{
						$locks = array();
					}
					// pid is fine here, cause locks are released at end of process anyway
					$locks[$ident] = array($type, $wait_type, $scope, getmypid());
					shm_put_var(self::$shm_id, self::SHM_VAR, $locks);
					break;
			}
		}
		self::_unblock();
	}

	public static function unlock($class, $id)
	{
		self::_block();
		if (($data = self::_is_locked($class, $id, true)))
		{
			$ident  = self::get_ident($class, $id);

			$scope = $data[2];

			switch($scope)
			{
				case aw_locker::SCOPE_PROCESS:
					if (self::$shm_id === null)
					{
						self::$shm_id = shm_attach(self::SHM_KEY, 50 * 1024, 0666);
					}
					$locks = shm_get_var(self::$shm_id, self::SHM_VAR);
					if (!is_array($locks))
					{
						$locks = array();
					}
					unset($locks[$ident]);
					shm_put_var(self::$shm_id, self::SHM_VAR, $locks);
					break;
			}
		}
		self::_unblock();
	}

	public static function try_operation($class, $id, $try_type = aw_locker::OPERATION_READ, $wait_type = null)
	{
		self::_block();
		if (($data = self::_is_locked($class, $id)))
		{
			$type = $data[0];
			if ($wait_type === null)
			{
				$wait_type = $data[1];
			}

			if ($type == self::LOCK_READ_ONLY && $try_type == aw_locker::OPERATION_READ)
			{
				self::_unblock();
				return;
			}

			switch($wait_type)
			{
				case aw_locker::WAIT_BLOCK:
					self::_unblock();
					do {
						usleep(500);
						self::_block();
						$data = self::_is_locked($class, $id);
						self::_unblock();
					} while($data);
					break;

				case aw_locker::WAIT_EXCEPTION:
					self::_unblock();
					throw new aw_lock_exception($class, $id, $try_type, $type, $wait_type);
					break;
			}
		}
		else
		{
			self::_unblock();
		}
	}

	private static function _is_locked($class, $id, $all_proc = false)
	{
		// go over all boundaries and check them, fastest first
		$ident = self::get_ident($class,$id);

		// SCOPE_SERVER
		if (self::$shm_id === null)
		{
			self::$shm_id = shm_attach(self::SHM_KEY, 50 * 1024, 0666);
		}
		// this will generate a warning. once per server reboot. and I can't help it.
		$locks = shm_get_var(self::$shm_id, self::SHM_VAR);
		if (!is_array($locks))
		{
			$locks = array();
			shm_put_var(self::$shm_id, self::SHM_VAR, $locks);
		}
		// process locks are only for other processes, not the locker
		if (isset($locks[$ident]) && ($all_proc || $locks[$ident][3] != getmypid()))
		{
			return $locks[$ident];
		}

		return false;
	}

	private static function get_ident($class, $id)
	{
		return "{$class}::{$id}";
	}

	private static function _block()
	{
		if (!self::$sem_id)
		{
			self::$sem_id = sem_get(self::SEM_ID);
		}
		sem_acquire(self::$sem_id);
	}

	private static function _unblock()
	{
		sem_release(self::$sem_id);
	}
}
