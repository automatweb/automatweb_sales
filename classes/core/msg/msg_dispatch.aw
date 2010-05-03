<?php

namespace automatweb;

// this is the aw message dispatcher
// it accepts messages and delivers them to all listeners

class msg_dispatch
{
	////
	// !this delivers posted messages
	// parameters:
	//	msg - message
	//	params - array of parameters
	function post_message($arr)
	{
		error::raise_if(!isset($arr["msg"]), array(
			"id" => "ERR_NO_MSG",
			"msg" => t("msg_dispatch::post_message - no message posted!")
		));

		$handlers = $this->_get_handlers_for_message($arr["msg"]);

		foreach($handlers as $handler)
		{
			$class = "automatweb\\" . basename($handler["class"]);
			$func = $handler["func"];
			$inst = new $class();
			error::raise_if(!method_exists($inst, $func), array(
				"id" => 'ERR_NO_HANDLER_FUNC',
				"msg" => sprintf(t("msg_dispatch::post_message - no handler function (%s) in class (%s) for message %s!"), $func, $class, $arr["msg"])
			));
			$inst->$func($arr["params"]);
		}
	}

	////
	// !this delivers posted messages with a parameter
	// parameters:
	//	msg - message
	//	param - the parameter through which message recievers are filtered
	//	params - array of parameters
	function post_message_with_param($arr)
	{
		error::raise_if(!isset($arr["msg"]), array(
			"id" => "ERR_NO_MSG",
			"msg" => t("msg_dispatch::post_message - no message posted!")
		));

		error::raise_if(!isset($arr["param"]), array(
			"id" => "ERR_NO_MSG",
			"msg" => t("msg_dispatch::post_message - no parameter for message posted!")
		));

		$handlers = $this->_get_handlers_for_message($arr["msg"]);
		foreach($handlers as $handler)
		{
			if (!empty($handler["param"]) && defined($handler["param"]))
			{
				$handler["param"] = constant($handler["param"]);
			}
			if (empty($handler["param"]) || $handler["param"] == $arr["param"])
			{
				$class = "automatweb\\" . $handler["class"];
				$func = $handler["func"];
				$inst = new $class();
				error::raise_if(!method_exists($inst, $func), array(
					"id" => 'ERR_NO_HANDLER_FUNC',
					"msg" => sprintf(t("msg_dispatch::post_message - no handler function (%s) in class (%s) for message %s!"), $func, $class, $arr["msg"])
				));
				$inst->$func($arr["params"]);
			}
		}
	}

	function _get_handlers_for_message($msg)
	{
		static $cache;
		if (isset($cache[$msg]))
		{
			return $cache[$msg];
		}
		$msg = str_replace(".", "", $msg);
		$file = aw_ini_get("basedir")."/xml/msgmaps/".$msg.".xml";

		$fc = file_get_contents($file);
		error::raise_if($fc === false, array(
			"id" => "ERR_NO_SUCH_MESSAGE",
			"msg" => sprintf(t("msg_dispatch::post_message - no such message (%s) defined!"), $msg)
		));

		$handlers = new aw_array(aw_unserialize($fc));
		$cache[$msg] = $handlers->get();
		return $cache[$msg];
	}
}
?>
