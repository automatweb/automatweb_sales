<?php
/*
@classinfo syslog_type=ST_DB_LOGIN relationmgr=yes no_status=1 no_comment=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property db_server type=relpicker reltype=RELTYPE_SERVER_LOGIN automatic=1
	@caption Server

	@property db_base type=textbox 
	@caption Andmebaas

	@property db_user type=textbox 
	@caption Kasutajanimi

	@property db_pass type=password
	@caption Parool

	@property db_create_ifnexist type=checkbox ch_value=1
	@caption Loo kui olemas pole

@reltype SERVER_LOGIN value=1 clid=CL_DB_SERVER_LOGIN
@caption db serveri login
*/

class db_login extends class_base
{
	function db_login()
	{
		$this->init(array(
			'tpldir' => 'awmyadmin/db_login',
			'clid' => CL_DB_LOGIN
		));
	}

	function callback_post_save($args)
	{
		extract($args);
		$ob = obj($id);
		if ($ob->prop('db_create_ifnexist') && $ob->prop('db_server'))
		{
			$server = get_instance(CL_DB_SERVER_LOGIN);
			if (!$server->login_as($ob->prop('db_server')))
			{
				error::raise(array(
					"id" => ERR_DB_ADMIN_NOT_SET, 
					"msg" => t('The admin user for the database server has not been set!')
				));
			}
			$found = false;
			$server->db_list_databases();
			while ($db = $server->db_next_database())
			{
				if ($db['name'] == $ob->prop('db_base'))
				{
					$found = true;
				}
			}

			if (!$found)
 			{
				$server->db_create_database(array(
					'name' => $ob->prop('db_base'),
					'user' => $ob->prop('db_user'),
					'pass' => $ob->prop('db_pass'),
					'host' => aw_ini_get('server.hostname')
				));
			}
		}
	}

	/** Switches the default database connection to the one defined in the given database login object
		@attrib api=1 params=pos

		@param oid required type=oid
			The database login object to use

		@returns
			true if the connection was successfully changed
			false if not
	**/
	function login_as($oid)
	{
		if (!$this->can("view", $oid) || !is_oid($oid))
		{
			return false;
		}
		$server = get_instance(CL_DB_SERVER_LOGIN);
		$ob = obj($oid);
		if ($ob->prop('db_server') && $ob->prop('db_base') != '' && $ob->prop('db_user') != '')
		{
			$this->db_connect(array(
				'driver' => $server->get_host_driver($ob->prop('db_server')),
				'server' => $server->get_host($ob->prop('db_server')),
				'base' => $ob->prop('db_base'),
				'username' => $ob->prop('db_user'),
				'password' => $ob->prop('db_pass')
			));
		}
		else
		{
			return false;
		}
		return true;
	}
}
?>
