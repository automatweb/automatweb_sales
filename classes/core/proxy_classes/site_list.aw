<?php

namespace automatweb;

class __aw_proxy_site_list extends core
{
	function __aw_proxy_site_list($rs)
	{
		$this->init();
		$this->remote_server = $rs;
	}

	function orb_list($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "site_list",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function get_site_list($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_site_list",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function _get_server_stats($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "_get_server_stats",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function _get_cver_stats($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "_get_cver_stats",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function orb_server_list($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "server_list",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function orb_update_site($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "update_site",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function orb_update_server($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "update_server",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function orb_get_site_list($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_site_list",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function server_picker($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "server_picker",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function orb_get_server_list($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "orb_get_server_list",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function get_server_id_by_ip($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_server_id_by_ip",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function get_site_id_by_url($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_site_id_by_url",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function get_site_data($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_site_data",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function get_server_data($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_server_data",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function change_site($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "change_site",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function submit_change_site($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "submit_change_site",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function change_server($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "change_server",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function submit_change_server($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "submit_change_server",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function get_site_info($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "get_site_info",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}

	function fetch_site_data($args)
	{
		//$args = func_get_args();
		return $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "fetch_site_data",
			"method" => "xmlrpc",
			"server" => $this->remote_server,
			"params" => $args
		));
	}
}

?>
