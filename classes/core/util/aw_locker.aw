<?php

class aw_locker extends aw_locker_file
{
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
}

interface aw_lock_interface
{
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
	public static function lock($class, $id, $type, $boundary, $wait_type, $release_time = 0);

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
	public static function unlock($class, $id, $boundary = null);

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
	public static function try_operation($class, $id, $try_type = aw_locker::OPERATION_READ, $wait_type = null);

	/** Checks if given resource is locked in current context (process, session, ...)
		@attrib api=1 params=pos

		@param class required type=string
			Class of the resource to check

		@param id required type=int
			Id of the resource to check

		@returns int
			0 - not locked or one of aw_locker::LOCK_... constants to indicate lock type
	**/
	public static function is_locked($class, $id);
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

