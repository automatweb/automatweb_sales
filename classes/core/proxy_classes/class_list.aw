<?php

class __aw_proxy_class_list extends core
{
	function __aw_proxy_class_list($rs)
	{
		$this->init();
		$this->remote_server = $rs;
	}

	function register_new_class_id($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "class_list",
			"action" => "register_new_class_id",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function update_class_def($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "class_list",
			"action" => "update_class_def",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function get_list($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "class_list",
			"action" => "get_list",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}
}

?>