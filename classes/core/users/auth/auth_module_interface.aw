<?php

interface authentication_module_interface
{
	public static function authenticate($identity, $tokens);
}
