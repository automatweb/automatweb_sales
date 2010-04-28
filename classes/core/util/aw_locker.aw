<?php

namespace automatweb;

class aw_locker
{
	const LOCK_FILE = "files/aw_lock_data"; // relative to AW_DIR

	// boundary constant values are in order of scope width
	const BOUNDARY_PROCESS = 1;	// locks only apply to the current process. any other processes or servers will not see them. this is the quickest.
	const BOUNDARY_SERVER = 2;	// locks only apply within the current server - objects can be accessed from other hosts. this is relatively quick. locks are released when process ends
	const BOUNDARY_SESSION = 3;	// same as BOUNDARY_SERVER but has a lifetime for current session


	const LOCK_FULL = 1;		// LOCK_FULL means that all calls to the object will block/except until the lock is freed
	const LOCK_WRITE = 2;	// LOCK_WRITE only means that write calls will block/except until the lock is freed

	const WAIT_BLOCK = 1;		// if a lock is on, the call will block until the lock is released
	const WAIT_EXCEPTION = 2;	// if a lock is on, the call will throw an exception and thus return immediately.

	// whenever locks are released, the object in all other processes will immediately be reloaded from the database to ensure that changes are propagated properly.


	const OPERATION_READ = 1;
	const OPERATION_WRITE = 2;

	private static $instance = null;
	private static $lock_file_pointer = false;
	private static $lock_data = array();
	private static $inprocess_locks = array();

	private function __construct()
	{
		;
	}

	/** singleton instance maker
		@attrib api=1
	**/
	public static function instance()
	{
		if (self::$instance === null)
		{
			self::$instance = new aw_locker();
		}
		return self::$instance;
	}

	/** locks the specified resource
		@attrib api=1 params=pos

		@param class required type=string
			Class of the resource to lock

		@param id required type=int
			Id of the resource to lock

		@param type required type=int
			One of aw_locker::LOCK_FULL (blocks reading AND writing) or aw_locker::LOCK_WRITE (blocks just writing)

		@param boundary required type=int
			Sets the lock boundary - aw_locker::BOUNDARY_SERVER (just this server) aw_locker::BOUNDARY_PROCESS (just this process on this server) aw_locker::BOUNDARY_SESSION -- this server for current session

		@param wait_type required type=int
			Default waiting type on the lock - aw_locker::WAIT_BLOCK (blocks until the lock is released) or aw_locker::WAIT_EXCEPTION (throws exception aw_lock_exception if the lock is held)

		@param release_time type=float default=0
			UNIX "microtimestamp" time the lock is held until. Default 0 means indefinitely or until lock is explicitly released with unlock()

		@comment
			Locks the resource with the given identifier (class and id).

		@examples
			process one:
				aw_set_exec_time(AW_LONG_PROCESS);
				$locker = aw_locker::instance();
				$locker->lock("object", 12, aw_locker::LOCK_WRITE, aw_locker::BOUNDARY_SERVER, aw_locker::WAIT_BLOCK);

				sleep(5);

				$locker->unlock("object", 12);

			process two:
				try {
					aw_locker::try_operation("object", 12, aw_locker::OPERATION_READ, aw_locker::WAIT_EXCEPTION);
				}
				catch(aw_lock_exception $e)
				{
					echo "Resource is locked!";
				}
				echo "Resource available!";

			if run in parallel, the second process throws an exception
	**/
	public static function lock($class, $id, $type, $boundary, $wait_type, $release_time = 0)
	{
		self::_block();
		$ident = self::get_ident($class, $id);
		$data = self::get_lock_data($ident);
		settype($release_time, "int");

		if (false === $data or $data[2] < $boundary)
		{
			$locker_id = null;
			if (self::BOUNDARY_SESSION === $boundary)
			{
				$locker_id = session_id();
				self::$lock_data[$ident] = array($type, $wait_type, $boundary, $locker_id, $release_time);
			}
			elseif (self::BOUNDARY_SERVER === $boundary)
			{
				// pid is fine here, cause locks are released at end of process anyway
				$locker_id = getmypid();
				self::$lock_data[$ident] = array($type, $wait_type, $boundary, $locker_id, $release_time);
			}
			elseif (self::BOUNDARY_PROCESS === $boundary)
			{
				self::$inprocess_locks[$ident] = array($type, $wait_type, $boundary, $locker_id, $release_time);
			}
		}
		self::_unblock();
	}

	/** Unlocks the specified resource
		@attrib api=1 params=pos

		@param class required type=string
			Class of the resource to unlock

		@param id required type=int
			Id of the resource to unlock

		@param boundary type=int default=NULL
			Boundary in which to unlock. Default unlocks in all boundaries

		@errors
			Triggers E_USER_NOTICE when attempting to unlock a free resource
	**/
	public static function unlock($class, $id, $boundary = null)
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
			self::_unlock($ident, $boundary);
		}
		self::_unblock();
	}

	private static function _unlock($ident, $boundary)
	{
		if (null === $boundary)
		{
			unset(self::$inprocess_locks[$ident]);
			unset(self::$lock_data[$ident]);
		}
		elseif (self::BOUNDARY_PROCESS === $boundary)
		{
			unset(self::$inprocess_locks[$ident]);
		}
		else
		{
			if (isset(self::$lock_data[$ident]))
			{
				if (self::BOUNDARY_SERVER === $boundary and self::BOUNDARY_SERVER === self::$lock_data[$ident][2])
				{
					unset(self::$lock_data[$ident]);
				}
				elseif (self::BOUNDARY_SESSION === $boundary and self::BOUNDARY_SESSION === self::$lock_data[$ident][2])
				{
					unset(self::$lock_data[$ident]);
				}
			}
		}
	}

	/** Checks for a lock on the given resource
		@attrib api=1 params=pos

		@param class required type=string
			Class of the resource to check

		@param id required type=int
			Id of the resource to check

		@param try_type optional type=int
			Type of operation to try, one of aw_locker::OPERATION_READ or aw_locker::OPERATION_WRITE, defaults to read

		@param wait_type optional type=int
			Waiting type for the check, one of aw_locker::WAIT_BLOCK (blocks until lock is released) aw_locker::WAIT_EXCEPTION (throws aw_lock_exception if lock is held)

		@errors
			throws aw_lock_exception if WAIT_BLOCK specified and lock wait limit exceeded
	**/
	public static function try_operation($class, $id, $try_type = self::OPERATION_READ, $wait_type = null)
	{
		self::_block();
		$ident = self::get_ident($class, $id);
		$data = self::get_lock_data($ident);

		if ($data !== null)
		{
			$type = $data[0];

			if (!($type === self::LOCK_WRITE && $try_type === self::OPERATION_READ))
			{
				if ($wait_type === null)
				{
					$wait_type = $data[1];
				}

				if (self::WAIT_BLOCK === $wait_type)
				{
					$retries = 10000; // wait time limit 100 seconds

					do
					{
						if (isset($is_locked))
						{
							usleep(10000);
							self::_unblock();
							self::_block();
						}

						$is_locked = self::_is_locked($class, $id);
						--$retries;
					}
					while($is_locked and $retries);

					if (!$retries)
					{
						self::_unblock();
						$e = new aw_lock_exception("Access blocked. Lock wait limit exceeded for '$ident'.");
						$e->object_class = $class;
						$e->object_id = $id;
						throw $e;
					}
				}
				elseif (self::WAIT_EXCEPTION === $wait_type)
				{
					if (self::_is_locked($class, $id))
					{
						self::_unblock();
						$e = new aw_lock_exception("Access blocked");
						$e->object_class = $class;
						$e->object_id = $id;
						throw $e;
					}
				}
				else
				{
					self::_unblock();
					throw new awex_lock("Invalid wait type identifier '". var_export($wait_type, true). "'");
				}
			}
		}

		self::_unblock();
	}

	/** Checks if given resource is locked in current context (process, session, ...)
		@attrib api=1 params=pos

		@param class required type=string
			Class of the resource to check

		@param id required type=int
			Id of the resource to check

		@returns int
			0 - not locked or one of aw_locker::LOCK_... constants to indicate lock type
	**/
	public static function is_locked($class, $id)
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
		$locked = 0;

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
				$lock_boundary = $lock_data[2];

				if (self::BOUNDARY_SERVER === $lock_boundary)
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
				elseif (self::BOUNDARY_SESSION === $lock_boundary)
				{
					$requester_id = session_id();
				}
				elseif (self::BOUNDARY_PROCESS === $lock_boundary)
				{
					$requester_id = "SCRIPT";
				}
				else
				{
					throw new awex_lock("Invalid lock boundary value '{$lock_boundary}'. Checking '{$ident}'");
				}

				if ($locker_id !== $requester_id)
				{
					$locked = $lock_data[0];
				}
			}
		}

		return $locked;
	}

	private static function get_lock_data($ident)
	{
		// go over all boundaries and check them, fastest first
		// BOUNDARY_PROCESS
		if (isset(self::$inprocess_locks[$ident]))
		{
			$lock_data = self::$inprocess_locks[$ident];
		}
		// BOUNDARY_SERVER
		// BOUNDARY_SESSION
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
		$filename = AW_DIR . self::LOCK_FILE;
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


/** generic lock exception **/
class awex_lock extends aw_exception {}

/** an exclusivity error. e.g. a semaphore couldn't be aquired **/
class awex_lock_ex extends awex_lock {}

/** Indicates that the resource is locked. **/
class aw_lock_exception extends aw_exception
{
	public $object_class;
	public $object_id;
}
