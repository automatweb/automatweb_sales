<?php

class aw_locker_file implements aw_lock_interface
{
	private static $lock_file_pointer = false;
	private static $lock_data = array();
	private static $lock_file = "files/aw_lock_data"; // relative to AW_DIR

	public static function lock($class, $id, $type, $scope, $wait_type, $release_time = 0)
	{
		self::_block();
		$ident = self::get_ident($class, $id);
		$data = self::get_lock_data($ident);
		settype($release_time, "int");

		if (false === $data or $data[2] < $scope)
		{
			$locker_id = null;
			if (aw_locker::SCOPE_SESSION === $scope)
			{
				$locker_id = session_id();
				self::$lock_data[$ident] = array($type, $wait_type, $scope, $locker_id, $release_time);
			}
			elseif (aw_locker::SCOPE_PROCESS === $scope)
			{
				// pid is fine here, cause locks are released at end of process anyway
				$locker_id = getmypid();
				self::$lock_data[$ident] = array($type, $wait_type, $scope, $locker_id, $release_time);
			}
		}
		self::_unblock();
	}

	public static function unlock($class, $id, $scope = null)
	{
		self::_block();
		$ident  = self::get_ident($class, $id);
		$data = self::get_lock_data($ident);
		if (false === $data)
		{
			trigger_error("An attempt to unlock a resource that isn't locked", E_USER_NOTICE);
		}
		else
		{
			self::_unlock($ident, $scope);
		}
		self::_unblock();
	}

	private static function _unlock($ident, $scope)
	{
		if (null === $scope)
		{
			unset(self::$inprocess_locks[$ident]);
			unset(self::$lock_data[$ident]);
		}
		else
		{
			if (isset(self::$lock_data[$ident]))
			{
				if (aw_locker::SCOPE_PROCESS === $scope and aw_locker::SCOPE_PROCESS === self::$lock_data[$ident][2])
				{
					unset(self::$lock_data[$ident]);
				}
				elseif (aw_locker::SCOPE_SESSION === $scope and aw_locker::SCOPE_SESSION === self::$lock_data[$ident][2])
				{
					unset(self::$lock_data[$ident]);
				}
			}
		}
	}

	public static function is_locked($class, $id, $operation = aw_locker::LOCK_WRITE)
	{
		self::_block();
		$ident = self::get_ident($class, $id);
		$locked = self::_is_locked($ident);
		self::_unblock();
		return $locked;
	}

	private static function _is_locked($ident)
	{
		$lock_data = self::get_lock_data($ident);
		$locked = false;

		if (null !== $lock_data)
		{
			// check timed locks first
			$release_time = $lock_data[4];
			if ($release_time > 0 and $release_time < time())
			{ // delete a timed lock if it has expired
				self::_unlock($ident, null);
			}
			else
			{
				// compare lock state requester and locker id-s
				$locker_id = $lock_data[3];
				$lock_scope = $lock_data[2];

				if (aw_locker::SCOPE_PROCESS === $lock_scope)
				{
					$requester_id = getmypid();

					// check if lock pid is still valid
					$platform = aw_ini_get("server.platform");
					if ("win32" === $platform)
					{
						$processes = explode("\n", shell_exec( "tasklist.exe"));
						$pid_exists = false;
						foreach ($processes as $process)
						{
							if (strpos("Image Name", $process) === 0 or strpos("===", $process) === 0)
							{
								continue;
							}

							 $matches = false;
							 preg_match("/(.*)\s+(\d+).*$/", $process);

							 if (((int) $matches[2]) === $locker_id)
							 {
								 $pid_exists = true;
								 break;
							 }
						}

						if (!$pid_exists)
						{ // locker process doesn't exist, lock expired
							self::_unlock($ident, null);
							$locker_id = $requester_id = 0;
						}
					}
					elseif ("unix" === $platform and !file_exists("/proc/{$locker_id}"))
					{ // locker process doesn't exist, lock expired
						if (file_exists("/proc/{$requester_id}"))
						{
							self::_unlock($ident, null);
							$locker_id = $requester_id = 0;
						}
						else
						{
							throw new awex_lock("Process id checker for unix environments not working properly. Checked pid: '{$requester_id}'");
						}
					}
				}
				elseif (aw_locker::SCOPE_SESSION === $lock_scope)
				{
					$requester_id = session_id();
				}
				else
				{
					throw new awex_lock("Invalid lock scope value '{$lock_scope}'. Checking '{$ident}'");
				}

				if ($locker_id !== $requester_id)
				{
					$locked = (bool) $lock_data[0];
				}
			}
		}

		return $locked;
	}

	private static function get_lock_data($ident)
	{
		// go over all boundaries and check them, fastest first
		// SCOPE_PROCESS
		// SCOPE_SESSION
		elseif (isset(self::$lock_data[$ident]))
		{
			$lock_data = self::$lock_data[$ident];
		}
		else
		{
			$lock_data = null;
		}

		return $lock_data;
	}

	private static function get_ident($class, $id)
	{
		return "{$class}::{$id}";
	}

	private static function _block()
	{
		$filename = AW_DIR . self::$lock_file;
		self::$lock_file_pointer = fopen($filename, "r+");

		if (false === self::$lock_file_pointer)
		{
			if (!file_exists($filename))
			{
				self::$lock_file_pointer = fopen($filename, "w+");
			}

			if (false === self::$lock_file_pointer)
			{
				throw new awex_lock_ex("Lock file couldn't be opened");
			}
		}

		$success = flock(self::$lock_file_pointer, LOCK_EX);
		if (!$success)
		{
			throw new awex_lock_ex("Exclusive lock couldn't be obtained");
		}

		$lock_data = fread(self::$lock_file_pointer, 1000000);
		if (false === $lock_data)
		{
			throw new awex_lock_ex("Lock data couldn't be read");
		}
		elseif ("" !== $lock_data)
		{
			$lock_data_parsed = unserialize($lock_data);
			if (false === $lock_data_parsed)
			{
				throw new awex_lock_ex("Lock data is corrupt: '{$lock_data}'");
			}
			elseif (is_array($lock_data_parsed))
			{
				self::$lock_data = $lock_data_parsed;
			}
		}
	}

	private static function _unblock()
	{
		if (!is_resource(self::$lock_file_pointer))
		{
			throw new awex_lock_ex("Lock file pointer not valid");
		}

		$success = rewind(self::$lock_file_pointer);
		if (false === $success)
		{
			throw new awex_lock_ex("Lock file pointer couldn't be reset");
		}

		$success = ftruncate(self::$lock_file_pointer, 0);
		if (!$success)
		{
			throw new awex_lock_ex("Lock file couldn't be emptied");
		}

		$lock_data = serialize(self::$lock_data);
		$success = fwrite(self::$lock_file_pointer, $lock_data, strlen($lock_data));
		if (false === $success)
		{
			throw new awex_lock_ex("Lock data not written");
		}

		fclose(self::$lock_file_pointer);
		self::$lock_file_pointer = null;
		self::$lock_data = array();
	}
}

