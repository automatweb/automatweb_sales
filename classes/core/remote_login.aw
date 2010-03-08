<?php
// $Header: /home/cvs/automatweb_dev/classes/core/remote_login.aw,v 1.4 2008/01/31 13:52:49 kristo Exp $
// remote_login.aw - AW remote login

/*
	@classinfo  maintainer=kristo

	@default table=objects
	@default field=meta
	@default method=serialize
	@default group=general

	@property login_uid type=textbox
	@caption Kasutajanimi

	@property login_password type=password
	@caption Parool

	@property server type=textbox
	@caption Server

*/


class remote_login extends class_base
{
	function remote_login($args = array())
	{
		extract($args);
		$this->init(array(
			"tpldir" => "automatweb/remote_login",
			"clid" => CL_AW_LOGIN,
		));
	}

	function login_from_obj($id)
	{
		$ob = new object($id);
		$this->handshake(array(
			"silent" => true,
			"host" => $ob->prop("server"),
		));

		$this->login(array(
			"host" => $ob->prop("server"),
			"uid" => $ob->prop("login_uid"),
			"password" => $ob->prop("login_password"),
			"silent" => true
		));

		return array($ob->prop("server"),$this->cookie);
	}

	function get_server($id)
	{
		$ob = new object($id);
		return $ob->prop("server");
	}

	/**  
		
		@attrib name=getcookie params=name nologin="1" is_public="1" default="0"
		
		
		@returns
		
		
		@comment

	**/
	function getcookie($arr)
	{
		$ma = -1;
		session_cache_limiter("must-revalidate, max-age=".$ma);
		header("Cache-Control: must-revalidate, max-age=".$ma);
		header("Expires: ".gmdate("D, d M Y H:i:s",time()+$ma)." GMT");
				
		die(t("Relax, take a cookie."));
	}
};
?>
