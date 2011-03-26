<?php

class user_manager_obj extends _int_object
{
	const CLID = 1003;

	/**
		@attrib api=1 params=pos
		@param uid type=string
			User login
		@comment
		@returns CL_USER
			Created user object
		@errors
	**/
	public static function add_user($uid)
	{return;
		try
		{
			$user = obj(null, array(), $uid);
		}
		catch (Exception $e)
		{
		}
	}

	public static function delete_user()
	{
	}
}

/** Generic users management exception **/
class awex_users extends awex_obj {}
