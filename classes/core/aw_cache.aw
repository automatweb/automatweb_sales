<?php

/*
@comment
	Class for caching arbitrary data. Currently "Non thread safe" and request based (data is lost when script ends)
*/
class aw_cache implements aw_data_cache_provider
{
	private static $data_cache_provider = false;
	private static $data_cache = array();

	public function __construct()
	{
	}

	/** Performs cache system setup
		@attrib params=pos api=1
		@return void
		@qc date=20101027 standard=aw3
	**/
	public static function setup()
	{
		if (false === self::$data_cache_provider)
		{
			self::$data_cache_provider = new aw_cache();
		}
	}

	/** Checks if data identified by class + keys combination is stored in data cache
		@attrib params=pos api=1
		@param class_id type=int
			AutomatWeb class id
		@param key type=int
			Any number between 1 and 5 (inclusive) of identifier key integer arguments
		@return bool
		@qc date=20101027 standard=aw3
	**/
	public static function is_set($class_id, $key)
	{
		$args = func_get_args();
		return call_user_func_array(array(self::$data_cache_provider, "isset_data"), $args);
	}

	/**
		@qc date=20101027 standard=aw3
	**/
	public function isset_data($class_id, $key)
	{
		$args = func_get_args();
		$args = implode("][", $args);
		return eval("return isset(self::\$data_cache[{$args}]);");
	}

	/** Get data identified by class + keys combination
		@attrib params=pos api=1
		@param class_id type=int
			AutomatWeb class id
		@param key type=string/int
			Any number between 1 and 5 (inclusive) of identifier key integer arguments
		@return mixed
		@qc date=20101027 standard=aw3
	**/
	public static function get($class_id, $key)
	{
		$args = func_get_args();
		return call_user_func_array(array(self::$data_cache_provider, "get_data"), $args);
	}

	/**
		@qc date=20101027 standard=aw3
	**/
	public function get_data($class_id, $key)
	{
		$args = func_get_args();
		$args = implode("][", $args);
		return eval("return isset(self::\$data_cache[{$args}]) ? self::\$data_cache[{$args}] : null;");
	}

	/** Set data identified by class + keys combination
		@attrib params=pos api=1
		@param data type=mixed
			Data to store
		@param class_id type=int
			AutomatWeb class id
		@param key type=string/int
			Any number between 1 and 5 (inclusive) of identifier key integer arguments
		@return void
		@qc date=20101027 standard=aw3
	**/
	public static function set($data, $class_id, $key)
	{
		$args = func_get_args();
		call_user_func_array(array(self::$data_cache_provider, "set_data"), $args);
	}

	/**
		@qc date=20101027 standard=aw3
	**/
	public function set_data($data, $class_id, $key)
	{
		$args = func_get_args();
		array_shift($args);
		$args = implode("][", $args);
		eval("self::\$data_cache[{$args}] = \$data;");
	}
}

interface aw_data_cache_provider
{
	public function isset_data($class_id, $key);

	/** Return data identified by class + keys combination
		@attrib params=pos api=1
		@param class_id type=int
			AutomatWeb class id
		@param key type=string/int
			Any number between 1 and 5 (inclusive) of identifier key integer arguments
		@return mixed
		@qc date=20101027 standard=aw3
	**/
	public function get_data($class_id, $key);
	public function set_data($data, $class_id, $key);
}

