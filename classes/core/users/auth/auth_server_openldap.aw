<?php
// auth_server_openldap.aw - Autentimisserver OpenLDAP
/*

@classinfo relationmgr=yes no_comment=1 no_status=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property server type=textbox
@caption LDAP server

@property server_port type=textbox
@caption Port

@property server_ldaps type=checkbox ch_value=1
@caption SSL &Uuml;hendus

@property server_passwords_md5 type=checkbox ch_value=1
@caption Parool md5 r&auml;sina

@property ad_base_dn type=textbox
@caption Baas-DN kasutajate otsimiseks

@property grp_base_dn type=textbox
@caption Baas-DN gruppide otsimiseks

@property ad_uid type=textbox
@caption Kasutaja (gruppide lugemiseks)

@property ad_pwd type=password
@caption Parool

@property ad_grp type=select
@caption Grupp, kus kasutajad peavad olema

@property ad_grp_txt type=textbox
@caption Grupp, kus kasutajad peavad olema (tekst)

@property no_user_grp type=relpicker reltype=RELTYPE_GROUP
@caption Grupp, kuhu pannakse kasutajad, keda kohalikus s&uuml;steemis pole

@property auto_create_user type=checkbox ch_value=1
@caption Kas lastakse sisse logida kasutajatel, keda kohalikus s&uuml;steemis pole

@property break_chain type=checkbox ch_value=1
@caption Gruppi mitekuuluvus katkestab ahela

@property no_save_pwd type=checkbox ch_value=1
@caption &Auml;ra salvesta AW'sse kasutaja parooli

@property aw_user_prefix type=textbox
@caption Kasutajanime prefiks

@property user_bind_prefix type=textbox
@caption Bind prefix kasutajatele

@reltype GROUP value=2 clid=CL_GROUP
@caption grupp

*/

class auth_server_openldap extends class_base
{
	function auth_server_openldap()
	{
		$this->init(array(
			"tpldir" => "core/users/auth/auth_server_openldap",
			"clid" => CL_AUTH_SERVER_OPENLDAP
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "ad_grp":
				if ($arr["obj_inst"]->prop("ad_grp_txt") != "")
				{
					return PROP_IGNORE;
				}
				$grps = $this->_get_ad_grps($arr["obj_inst"]);
				if ($grps === PROP_ERROR)
				{
					$prop["error"] = $this->last_error;
					return PROP_ERROR;
				}
				if (count($grps) == 0)
				{
					return PROP_IGNORE;
				}

				$prop["options"] = $grps;
				break;

			case "ad_pwd":
			case "ad_uid":
				if ($arr["obj_inst"]->prop("ad_grp_txt") != "")
				{
					return PROP_IGNORE;
				}
				break;
		}
		return $retval;
	}

	function check_auth($server, &$credentials, auth_config $conf)
	{
		if (!extension_loaded("ldap"))
		{
			error::raise(array(
				"id" => "ERR_NO_LDAP",
				"msg" => t("auth_server_openldap::check_auth(): The LDAP module for PHP is not installed, but the auth configuration specifies a LDAP server to authenticate against!"),
				"fatal" => false,
				"show" => false
			));
			return array(false, t("LDAP Moodul pole installeeritud!"), false);
		}

		$srv = $server->prop("server");
		if ($server->prop("server_ldaps"))
		{
			$srv = "ldaps://".$srv;
		}

		if ($server->prop("server_port"))
		{
			$res = ldap_connect($srv, $server->prop("server_port"));
		}
		else
		{
			$res = ldap_connect($srv);
		}

		if (!$res)
		{
			return array(false, sprintf(t("Ei saanud &uuml;hendust LDAP serveriga %s"), $server->prop("server")));
		}
		ldap_set_option($res, LDAP_OPT_PROTOCOL_VERSION, 3);
		// ldap_set_option($res, LDAP_OPT_NETWORK_TIMEOUT, 3);

		$uid = $credentials["uid"];
		$password = $server->prop("server_passwords_md5") ? md5($credentials["password"]) : $credentials["password"];
		$bind = ldap_bind($res, "uid=".$uid.",".$server->prop("ad_base_dn"), $password);

		$break = false;
		if ($bind)
		{
			$grp = $server->prop("ad_grp_txt");
			if ($server->prop("ad_grp") != "")
			{
				$grp = $server->prop("ad_grp");
			}

			if ($grp == "" || ($grp != "" && $this->_is_member_of($res, $server, $grp, $credentials)))
			{
				$this->_proc_credentials($res, $server, $credentials);

				if ($conf->check_local_user($server->id(), $credentials))
				{
					return array(true, "", false);
				}
			}
			else
			{
				if ($grp != "")
				{
					$this->remove_user_from_group($server->prop("no_user_grp"), $credentials);
				}

				if ($server->prop("break_chain"))
				{
					$break = true;
				}
			}
		}

		return array(false, t("Sellist kasutajat pole v&otilde;i parool on vale!"), $break);
	}

	function _get_ad_grps($o)
	{
		$srv = $o->prop("server");
		if ($o->prop("server_ldaps"))
		{
			$srv = "ldaps://".$srv;
		}
		if ($o->prop("server_port"))
		{
			$res = ldap_connect($srv, $o->prop("server_port"));
		}
		else
		{
			$res = ldap_connect($srv);
		}
		if (!$res)
		{
			$this->last_error = t("Ei saanud serveriga &uuml;hendust!");
			return PROP_ERROR;
		}
		ldap_set_option($res, LDAP_OPT_PROTOCOL_VERSION, 3);

		$uid = $o->prop("ad_uid");
		if ($uid)
		{
			if (!ldap_bind($res, "uid=".$o->prop("ad_uid").",".$o->prop("ad_base_dn"), $o->prop("ad_pwd")))
			{
				return array();
			}
		}

		$sr=ldap_search($res, $o->prop("grp_base_dn"), "objectclass=posixgroup", array("cn"));
		$info = ldap_get_entries($res, $sr);

		$ret = array("" => "");
		for ($i=0; $i<$info["count"]; $i++)
		{
			list($cn) = explode(",", $info[$i]["dn"]);
			list(,$grp) = explode("=", $cn);
			$ret[$grp] = $grp;
		}

		return $ret;
	}

	function _is_member_of($res, $o, $grp, $cred)
	{
		$dn = $o->prop("ad_base_dn");
		if (!$dn)
		{
			return;
		}

		$sr=ldap_search($res, $o->prop("grp_base_dn"), "cn=".$grp);
		$info = ldap_get_entries($res, $sr);
		$ret = false;

		// check members
		for($a = 0; $a < $info[0]["memberuid"]["count"]; $a++)
		{
			if ($cred["uid"] == $info[0]["memberuid"][$a])
			{
				return true;
			}
		}
		return $ret;
	}

	/** reads users email and name from ad server
	**/
	function _proc_credentials($res, $server, &$cred)
	{
		$dn = $server->prop("ad_base_dn");
		if (!$dn)
		{
			return;
		}

		$sr=ldap_search($res, $dn, "uid=".$cred["uid"]);
		$fdn = trim(strtolower("cn=".$cred["uid"].",".$dn));
		$info = ldap_get_entries($res, $sr);

		if (!$info)
		{
			return;
		}

		if ($info["count"] > 0 && isset($info[0]["displayname"]["count"]) && $info[0]["displayname"]["count"] > 0)
		{
			$cred["name"] = $info[0]["displayname"]["0"];
		}
	}

	function remove_user_from_group($grp, $cred)
	{
		if ($cred["server"] != "")
		{
			$cred["uid"] .= ".".$cred["server"];
		}

		$ol = new object_list(array(
			"class_id" => CL_USER,
			"name" => $cred["uid"],
			"brother_of" => new obj_predicate_prop("id")
		));

		if ($ol->count())
		{
			$u = $ol->begin();
			$g = get_instance(CL_GROUP);
			$g->remove_user_from_group($u, obj($grp));
		}
	}
}

class awex_auth_ldap extends awex_auth {}
class awex_auth_ldap_bind extends awex_auth_ldap {}
