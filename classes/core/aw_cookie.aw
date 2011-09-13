<?php

class aw_cookie
{
	/** Returns cookie variable value by $name
		@attrib api=1 params=pos
		@param name type=string
		@returns string|NULL
		@errors none
	**/
	public static function get($name)
	{
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	}

	/** Sets cookie variable by $name
		@attrib api=1 params=pos
		@param name type=string
		@param value type=string
		@param lifetime type=int default=0
			Default means until browser closed
		@returns bool
		@errors none
	**/
	public static function set($name, $value, $lifetime = 0)
	{
		$expire = $lifetime ? time() + $lifetime : 0;
		$path = "/";
		$domain = automatweb::$request->get_uri()->get_host();
		return setcookie($name, $value, $expire, $path, $domain, false, true);
	}
}
