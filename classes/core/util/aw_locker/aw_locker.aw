<?php

class aw_locker extends aw_locker_none
{
	// boundary constant values are in ascending order of scope
	const SCOPE_PROCESS = 2;	// lock applies within the current server process. locks are released when process ends
	const SCOPE_SESSION = 3;	// locks apply during current session scope

	const LOCK_FULL = 1;		// LOCK_FULL means that all calls to the object will block/except until the lock is freed
	const LOCK_WRITE = 2;	// LOCK_WRITE only means that write calls will block/except until the lock is freed

	const WAIT_BLOCK = 1;		// if a lock is on, the call will block until the lock is released
	const WAIT_EXCEPTION = 2;	// if a lock is on, the call will throw an exception and thus return immediately.

	// whenever locks are released, the object in all other processes will immediately be reloaded from the database to ensure that changes are propagated properly.
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

		@param scope required type=int
			Sets the lock scope - aw_locker::SCOPE_PROCESS (process scope), aw_locker::SCOPE_SESSION -- for current session

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
				$locker->lock("object", 12, aw_locker::LOCK_WRITE, aw_locker::SCOPE_PROCESS, aw_locker::WAIT_BLOCK);

				sleep(5);

				$locker->unlock("object", 12);

			process two:
				if(aw_locker::is_locked("object", 12))
				{
					echo "Resource is locked!";
				}
				else
				{
					echo "Resource available!";
				}

			if run in parallel, the second process throws an exception
	**/
	public static function lock($class, $id, $type, $scope, $wait_type, $release_time = 0);

	/** Unlocks the specified resource
		@attrib api=1 params=pos

		@param class required type=string
			Class of the resource to unlock

		@param id required type=int
			Id of the resource to unlock

		@param scope type=int default=NULL
			Scope in which to unlock. Default unlocks in all boundaries

		@errors
			Triggers E_USER_NOTICE when attempting to unlock a free resource
	**/
	public static function unlock($class, $id, $scope = null);

	/** Checks if given resource is locked in current context (server, session, ...)
		@attrib api=1 params=pos

		@param class type=string
			Class of the resource to check

		@param id type=int
			Id of the resource to check

		@param operation type=int
			Lock level to check (write or full)

		@returns bool
	**/
	public static function is_locked($class, $id, $operation = self::LOCK_WRITE);
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

