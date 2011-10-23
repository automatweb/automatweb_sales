<?php
// auth_server_nds.aw - Autentimisserver NDS
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

@property ad_base_dn type=textbox
@caption Baas-DN kasutajate otsimiseks

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

@reltype GROUP value=2 clid=CL_GROUP
@caption grupp

*/

class auth_server_nds extends class_base
{
	function auth_server_nds()
	{
		$this->init(array(
			"tpldir" => "core/users/auth/auth_server_nds",
			"clid" => CL_AUTH_SERVER_NDS
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

	public function check_auth($server, &$credentials, auth_config $conf)
	{
		if (!extension_loaded("ldap"))
		{
			error::raise(array(
				"id" => "ERR_NO_LDAP",
				"msg" => t("auth_server_ldap::check_auth(): The LDAP module for PHP is not installed, but the auth configuration specifies a LDAP server to authenticate against!"),
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

		$res = ldap_connect($srv, $server->prop("server_port"));
		if (!$res)
		{
			return array(false, sprintf(t("Ei saanud &uuml;hendust LDAP serveriga %s"), $server->prop("server")));
		}
		ldap_set_option($res, LDAP_OPT_PROTOCOL_VERSION, 3);

		$uid = $credentials["uid"];

		$break = false;
		$bind = @ldap_bind($res, "cn=$uid,".$server->prop("ad_base_dn"), $credentials["password"]);
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
		$res = ldap_connect($srv, $o->prop("server_port"));

		if (!$res)
		{
			$this->last_error = t("Ei saanud serveriga &uuml;hendust!");
			return PROP_ERROR;
		}
		ldap_set_option($res, LDAP_OPT_PROTOCOL_VERSION, 3);


		$uid = $o->prop("ad_uid");
		if ($uid)
		{
			if (!ldap_bind($res, "cn=$uid,".$o->prop("ad_base_dn"), $o->prop("ad_pwd")))
			{
				return array();
			}
		}

		$sr=ldap_search($res, $o->prop("ad_base_dn"), "(objectClass=groupOfNames)");
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

		$sr=ldap_search($res, $o->prop("ad_base_dn"), "(objectClass=groupOfNames)");
		$info = ldap_get_entries($res, $sr);
		$ret = false;
		for ($i = 0; $i < $info["count"]; $i++)
		{
			list($cn) = explode(",", $info[$i]["dn"]);
			list(,$igrp) = explode("=", $cn);
			if ($grp == $igrp)
			{
				// check members
				for($a = 0; $a < $info[$i]["member"]["count"]; $a++)
				{
					list($un) = explode(",", $info[$i]["member"][$a]);
					if ($un == "cn=".$cred["uid"])
					{
						return true;
					}
				}
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

		$sr = ldap_search($res, $dn, "objectclass=user", array("mail","fullname", "givenname", "sn"));

		$fdn = trim(strtolower("cn=".$cred["uid"].",".$dn));
		$a_info = ldap_get_entries($res, $sr);
		$info = NULL;
		for($tmp = 0; $tmp < $a_info["count"]; $tmp++)
		{
			if (trim(strtolower($a_info[$tmp]["dn"])) == $fdn)
			{
				$info = array(0 => $a_info[$tmp],"count" => 1);
				break;
			}
		}

		if (!$info)
		{
			return;
		}

		if ($info["count"] > 0 && $info[0]["mail"]["count"] > 0)
		{
			$cred["mail"] = $info[0]["mail"][0];
		}
		if ($info["count"] > 0 && $info[0]["fullname"]["count"] > 0)
		{
			$cred["name"] = $info[0]["fullname"]["0"];
		}

		if ($info["count"] > 0 && $info[0]["givenname"]["count"] > 0)
		{
			$cred["name"] = $info[0]["givenname"][0]." ".$info[0]["sn"][0];
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
			"site_id" => array(),
			"lang_id" => array(),
			"brother_of" => new obj_predicate_prop("id")
		));

		if ($ol->count())
		{
			$u = $ol->begin();
			$g = get_instance(CL_GROUP);
			aw_disable_acl();
			$g->remove_user_from_group($u, obj($grp));
			aw_restore_acl();
		}
	}

	function user_info($uid)
	{
		$sl = new object_list(array("class_id" => CL_AUTH_SERVER_NDS, "lang_id" => array(), "site_id" => array(), "sort_by" => "objects.name"));
		foreach($sl->arr() as $server)
		{
			// echo "server ".$server->name()." <br>";
			$srv = $server->prop("server");
			if ($server->prop("server_ldaps"))
			{
				$srv = "ldaps://".$srv;
			}

			$dn = $server->prop("ad_base_dn");
			$res = ldap_connect($srv, $server->prop("server_port"));
			ldap_set_option($res, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_bind($res, "cn=".$server->prop("ad_uid").",".$server->prop("ad_base_dn"), $server->prop("ad_pwd"));

			// echo "kasutaja info otsing: dn = '$dn' , parameeter = 'uid=$uid' <br>";
			$sr=ldap_search($res, $dn, "uid=".$uid);
			$info = ldap_get_entries($res, $sr);
			// echo dbg::dump($info);

			$this->_proc_credentials($res, $server, $cred);
		}
		// echo "kasutaja info: ".(dbg::dump($cred));
	}
}
