<?php

class auth_root implements authentication_module_interface
{
	const AUTH_TYPE = "second";
	const AUTH_FACTOR = "single";

	public static function authenticate($identity, $tokens)
	{
	}
}
