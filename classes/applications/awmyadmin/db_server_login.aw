<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_DB_SERVER_LOGIN relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property server_host type=textbox 
	@caption Server

	@property server_admin_user type=textbox 
	@caption Admin kasutaja

	@property server_admin_pass type=password
	@caption Admin parool

	@property server_driver type=select 
	@caption Andmebaasi driver

*/

class db_server_login extends class_base
{
	const AW_CLID = 149;

	function db_server_login()
	{
		$this->init(array(
			'tpldir' => 'awmyadmin/db_server_login',
			'clid' => CL_DB_SERVER_LOGIN
		));
	}

	function get_property($args)
	{
		if ($args['prop']['name'] == 'server_driver')
		{
			$args['prop']['options'] = $this->list_db_drivers();
		}
		return PROP_OK;
	}

	/** Switches the current database connection to the given database server login
		@attrib api=1 params=pos

		@param oid required type=oid
			The database server login object to use

		@returns
			true if the connection was successfully switched
			false if not
	**/
	function login_as($oid)
	{
		$ob = obj($oid);
		if ($ob->prop('server_driver') != '' && $ob->prop('server_host') != '' && $ob->prop('server_admin_user') != '')
		{
//die(dbg::dump($ob->properties()));
			$this->db_connect(array(
				'driver' => $ob->prop('server_driver'),
				'server' => $ob->prop('server_host'),
				'base' => "mysql", //aw_ini_get('db.base'),
				'username' => $ob->prop('server_admin_user'),
				'password' => $ob->prop('server_admin_pass')
			));
		}
		else
		{
			return false;
		}
		return true;
	}

	/** Returns the hostname from the server conf
		@attrib api=1 params=pos

		@param oid required type=string
			The objct to read the host from

		@returns
			hostname of the server
	**/
	function get_host($oid)
	{
		$ob = obj($oid);
		return $ob->prop('server_host');
	}

	/** Returns the database type from the server conf
		@attrib api=1 params=pos

		@param oid required type=string
			The objct to read the db driver from

		@returns
			database driver to the server
	**/
	function get_host_driver($oid)
	{
		$ob = obj($oid);
		return $ob->prop('server_driver');
	}
}
?>
