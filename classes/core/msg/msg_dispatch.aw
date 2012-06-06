<?php

/**
AW message dispatcher
Accepts messages and delivers them to all listeners
**/
class msg_dispatch
{
	/** Delivers posted messages
		@attrib api=1 params=name
		@param msg type=string
			Message identifier
		@param params type=array
			Message parameters
		@comment
		@returns void
		@errors
			throws awex_msg_id if no message id specified
			throws awex_msg_handler if handler is not valid or not found
	**/
	public static function post_message($arr)
	{
		if (empty($arr["msg"])) throw new awex_msg_id("No message posted!");

		$handlers = msg_index::get_handlers($arr["msg"]);
		foreach($handlers as $handler)
		{
			$class = $handler["class"];
			$method = $handler["method"];
			$inst = new $class;
			if(!method_exists($inst, $method)) throw new awex_msg_handler(sprintf("No handler function (%s) in class (%s) for message %s!", $method, $class, $arr["msg"]));
			$inst->$method($arr["params"]);
		}

		$si = __get_site_instance();
		if (method_exists($si, "aw_message_handler"))
		{
			$si->aw_message_handler($arr);
		}
	}

	/** Delivers posted messages with a parameter
		@attrib api=1 params=name
		@param msg type=string
			Message identifier
		@param param type=array
			Parameter by which message receivers are filtered
		@param params type=array
			Message parameters
		@comment
		@returns void
		@errors
			throws awex_msg_id if no message id specified
			throws awex_msg_param if no message parameter specified
			throws awex_msg_handler if handler is not valid or not found
	**/
	public static function post_message_with_param($arr)
	{
		if (empty($arr["msg"])) throw new awex_msg_id("No message posted!");
		if (empty($arr["param"])) throw new awex_msg_param(sprintf("No parameter for message %s posted!", $arr["msg"]));

		$handlers = msg_index::get_handlers($arr["msg"]);
		foreach($handlers as $handler)
		{
			if (!empty($handler["param"]) && defined($handler["param"]))
			{
				$handler["param"] = constant($handler["param"]);
			}

			if (empty($handler["param"]) || $handler["param"] == $arr["param"])
			{
				$class = $handler["class"];
				$method = $handler["method"];
				$inst = new $class;
				if(!method_exists($inst, $method)) throw new awex_msg_handler(sprintf("No handler function (%s) in class (%s) for message %s!", $method, $class, $arr["msg"]));
				$inst->$method($arr["params"]);
			}
		}

		$si = __get_site_instance();
		if (method_exists($si, "aw_message_handler"))
		{
			$si->aw_message_handler($arr);
		}
	}
}

/** Message identifier error **/
class awex_msg_id extends awex_msg {}

/** Message handler error **/
class awex_msg_handler extends awex_msg {}

/** Message parameter error **/
class awex_msg_param extends awex_msg {}
