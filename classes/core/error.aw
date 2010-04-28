<?php

// simple error class to replace core::raise_error. why? well, because this will have a *static* method
// that can throw errors, so you can throw errors from objects that do not derive from core

namespace automatweb;

class error
{
	/** throws an error
		@attrib api=1 params=name

		@param id required type=string
		Error id, unique string, prefixed by ERR_ that identifies the error

		@param msg required type=string
		Error message to show

		@param fatal optional type=bool
			if set, aborts execution defaults to true

		@param exception optional type=Exception
		If error id is ERR_UNCAUGHT_EXCEPTION, this contains the exception object

		@param show optional type=bool
			if true, error is shown to user, error is always logged and sent to the mailinglist, defaults to true

		@errors
			this is the error handler. what do you think.

		@returns
			none

		@examples
			if (!$very_important_parameter)
			{
				error::raise(array(
					"id" => "ERR_NO_PARAM",
					"msg" => sprintf(t("class::function(): parameter %s is not set!"), $param_name)
				));
			}
	**/
	public static function raise($arr)
	{
		if (!isset($arr["id"]) || !$arr["id"])
		{
			$arr["id"] = "ERR_GENERIC";
		}

		if (!isset($arr["msg"]))
		{
			$arr["msg"] = "";
		}

		if (!isset($arr["fatal"]))
		{
			$arr["fatal"] = true;
		}

		if (!isset($arr["show"]))
		{
			$arr["show"] = true;
		}

		$inst = new core();
		$inst->init();

		if (isset($arr["exception"]) and is_object($arr["exception"]))
		{
			$inst->raise_error_exception = $arr["exception"];
		}

		$inst->raise_error($arr["id"], $arr["msg"], $arr["fatal"], !$arr["show"]);
	}

	public static function throw_acl($arr)
	{
		$sct = isset($arr["access"]) ? "can_".$arr["access"] : t("not specified");
		$objn = isset($arr["oid"]) ? $arr["oid"] : t("not specified");
		$func = isset($arr["func"]) ? $arr["func"] : t("not specified");
		error::raise(array(
			"id" => "ERR_ACL",
			"msg" => sprintf(t("Acl error, access %s was denied for object %s in function %s"), $sct,$objn, $func),
			"fatal" => true,
			"show" => true
		));
	}

	/** throws an error if a condition is true
		@attrib api=1 params=pos

		@param cond required type=bool
			if this is true, error is thrown

		@param arr required type=array
			parameters to the #error::raise method

		@errors
			none

		@returns
			none

		@examples
			error::raise_if(!$very_important_parameter, array(
				"id" => "ERR_NO_PARAM",
				"msg" => sprintf(t("class::function(): parameter %s is not set!"), $param_name)
			));
	**/
	public static function raise_if($cond, $arr)
	{
		if ($cond)
		{
			self::raise($arr);
		}
	}

	/** checks if the current user has view access to the given oid and if not, redirects the user to the error page or gives a 404 error. does NOT send an error e-mail to the list

		@attrib api=1

		@param oid required type=oid
			The oid to check view access for

		@examples
			function foo($oid)
			{
				error::view_check($oid);	// this should only be calles for objects that you absolutely cannot do without
				$o = obj($oid);		// this is safe to do now.
				echo  $o->name();
			}
	**/
	public static function view_check($oid)
	{
		$t = new acl_base;
		$t->init();
		if (!$t->can("view", $oid))
		{
			$i = get_instance("menuedit");
			$i->do_error_redir($oid);
		}
	}
}
?>
