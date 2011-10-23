<?php
// auth_server_local.aw - Autentimsserver Kohalik
/*

@classinfo relationmgr=yes no_comment=1 no_status=1

@default table=objects
@default group=general

*/

class auth_server_local extends class_base
{
	function auth_server_local()
	{
		$this->init(array(
			"tpldir" => "core/users/auth/auth_server_local",
			"clid" => CL_AUTH_SERVER_LOCAL
		));
	}

	public function check_auth($server, &$credentials, auth_config $conf)
	{
		// by default eeldame, et kasutaja on jobu ja ei saa
		// sisse logida
		$success = false;
		$failure_msg = auth_config::get_login_fail_msg();
		$udata = null;
		$_uid = $credentials["uid"];

		if (!is_valid("password", $credentials["password"]))
		{
			return array(false, $failure_msg, false);
		}
		elseif (!is_valid("uid", $_uid))
		{
			return array(false, $failure_msg, false);
		}

		$msg = "";
		$this->quote($_uid);
		$q = "SELECT * FROM users WHERE uid = '{$_uid}' AND (blocked = 0 OR blocked IS NULL)";
		$this->db_query($q);
		while ($row = $this->db_next())
		{
			if ($row["uid"] === $_uid)
			{
				$udata = $row;
			}
		}

		if (is_array($udata))
		{
			if (aw_ini_get("auth.md5_passwords"))
			{
				if (md5($credentials["password"]) == $udata["password"])
				{
					$success = true;
				}
			}
			elseif ($credentials["password"] == $udata["password"])
			{
				$success = true;
			}
			else
			{
				$msg = $failure_msg;
			}
		}
		else
		{
			$msg = $failure_msg;
		}

		// check ip address
		if (is_oid($udata["oid"]) && $this->can("view", $udata["oid"]))
		{
			$u_o = obj($udata["oid"]);
			$conns = $u_o->connections_from(array("type" => "RELTYPE_ACCESS_FROM_IP"));
			if (count($conns))
			{
				$allow = false;
				$ipi = new ipaddress();
				$cur_ip = inet::is_ip($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
				foreach($conns as $c)
				{
					$ipa = $c->to();
					if ($ipa->prop("range") != "" && $ipi->match_range($ipa->prop("range"), $cur_ip))
					{
						$allow = true;
					}
					if ($ipi->match($ipa->prop("addr"), $cur_ip))
					{
						$allow = true;
					}
				}

				if (!$allow)
				{
					return array(false, $failure_msg, false);
				}
			}
		}

		if($success && user::require_password_change($udata["uid"]) && user::is_first_login($udata["uid"]) && !$credentials["pwdchange"])
		{
			Header("Location: ".$this->mk_my_orb("change_password_not_logged", array("uid" => $udata["oid"]), "users"));
			exit;
		}

		return array($success, $msg, false);
	}
}
