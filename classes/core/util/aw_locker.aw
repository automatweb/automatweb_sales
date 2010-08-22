<?php

class aw_locker extends aw_locker_file
{
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

