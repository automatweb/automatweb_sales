<?php
// group types:
// 0 - ordinary, user added group
// 1 - user's default group
// 2 - dynamic group

define("GROUP_LEVEL_PRIORITY", 100000);
define("USER_GROUP_PRIORITY", GROUP_LEVEL_PRIORITY*1000);	// max 1000 levels of groups

/*

EMIT_MESSAGE(MSG_USER_LOGIN);
EMIT_MESSAGE(MSG_USER_LOGOUT);

*/

class users_user extends aw_template
{
	function users_user()
	{
		$this->init("");
	}

	/** authenticates the user and logs them in if auth is successful
		@attrib api=1 params=name

		@param uid required type=string
			User id

		@param password required type=string
			Password

		@param server optional type=string
			The authentication server object to use. Not the oid, but the server's internal name set in the auth config. If set, only that server is checked, if not, the normal authentication chain is followed. Defaults to null

		@param remote_auth optional type=int
			If set to true, the only output is 1 for a successful login and 0 for unsuccessful.
	**/
	function login($params = array())
	{
		$uid = $params["uid"];
		$password = $params["password"];
		$success = false;

		$server = isset($params["server"]) ? $params["server"] : "";
		$remote_auth = isset($params["remote_auth"]) ? $params["remote_auth"] : false;
		$auth = new auth_config();

		if (!empty($uid))
		{
			if ("root" === $uid)
			{ // user is attempting to log in as root
				// get root password from aw.ini
				$root_password = aw_ini_get("users.root_password");

				// check if it meets requirements
				if (strlen($root_password) < 7)
				{
					throw new awex_auth_pw("Root password not set or doesn't meet requirements");
				}

				// compare with what user submitted
				if ($password === $root_password)
				{
					// let in
					$success = true;
					$msg = "";

					// create root user if not found
					if (!strtolower($this->db_fetch_field("SELECT uid FROM users WHERE uid = 'root'", "uid")))
					{//TODO: tmp lahendus. user::add_USER korda teha ja viia user_manager_obj-i.
						$_SESSION["uid"] = "root";
						aw_global_set("uid", "root");
						$root_user = obj(null, array(), user_obj::CLID);
						$root_user->set_parent(1);
						$root_user->set_prop("uid", "root");
						$root_user->save();
					}
				}
				else
				{
					// deny access
					$success = false;
					$msg = auth_config::get_login_fail_msg();
				}
			}
			else
			{
				$auth_id = $auth->has_config();
				if (is_oid($auth_id))
				{
					list($success, $msg) = $auth->check_auth($auth_id, array(
						"uid" => &$uid,
						"password" => $password,
						"server" => $server
					));

					if ($success && !empty($server))
					{
						$uid .= ".".$server;
					}
				}
				else
				{
					$auth = new auth_server_local();
					list($success, $msg) = $auth->check_auth(NULL, array(
						"uid" => $uid,
						"password" => $password
					));
				}
			}
		}

		// all checks complete, result in $success, process it
		if (!$success)
		{
			$this->_handle_failed_login($params, $msg);
		}

		//If user logs on first time and there is setting in .ini file then he/she must change password before login is completed
		$u_oid = users::get_oid_for_uid($uid);
		if (!$u_oid)
		{
			error::raise(array(
				"id" => "ERR_NO_UID_OID",
				"msg" => sprintf(t("oid for uid is empty uid = '%s'!"), $uid)
			));
		}
		$user_obj = obj($u_oid);
		$_SESSION["user_history_count"] = $user_obj->prop("history_size") ? $user_obj->prop("history_size") : 25;
		$_SESSION["user_history_has_folders"] = $user_obj->prop("history_has_folders");
		if (aw_ini_get("users.count_logins"))
		{
			$logins = $user_obj->prop("logins") + 1;
			$user_obj->set_prop("logins", $logins);
			$user_obj->set_prop("lastaction", time());
			$user_obj->save();
		}

		aw_session_set("user_adm_ui_lc", $user_obj->prop("ui_language"));

		setcookie("nocache",1);
		$_SESSION["uid"] = $uid;
		$_SESSION["uid_oid"] = $u_oid;
		aw_global_set("uid_oid", $u_oid);
		aw_global_set("uid", $uid);

		if ($user_obj->prop("cfg_admin_mode") == 1)
		{
			$_SESSION["cfg_admin_mode"] = 1;
		}

		// init acl
		$this->request_startup();
		// The above certainly doesn't initiate acl, but maybe it has some other purpose... -kaarel 28.11.2008
		// $this->init_acl();// called in aclbase on msguserlogin message

		// notify listeners
		post_message("MSG_USER_LOGIN", array("uid" => $uid));

		if (!empty($params["remote_auth"]))
		{
			die("1");
		}

		if (isset($_SESSION["auth_redir_post"]) && is_array($_SESSION["auth_redir_post"]))
		{
			header("Location: ".aw_ini_get("baseurl")."automatweb/orb.aw");
			die();
		}

		$si = __get_site_instance();
		if (is_object($si) && method_exists($si, "on_login"))
		{
			$si->on_login();
		}

		$url = $this->_find_post_login_url($params, $uid, $user_obj);
		return $url;
	}

	public function _find_post_login_url($params, $uid, $user_obj)
	{
		// if force password change is set in the ini file, and the current user last changed password
		// earlier than the limit, then redirect to password change form
		if (($iv = aw_ini_get("users.change_password_interval")) > 0 && ($user_obj->meta("password_change_time") < (time() - $iv)))
		{
			$rv = $this->mk_my_orb("change", array("id" => $user_obj->id(), "group" => "chpwd"), "user", true);
			return $rv;
		}
		// now that we got the whether he can log in bit cleared, try to find an url to redirect to
		// 1st is the url that was requested before the user was forced to login.
		// 2nd if the request says go to that url after or if not, group login url
		// 3nd try to find the language based url and if that fails, then the everyone's url and then just the baseurl.
		// wow. is this graceful degradation or what!
		if($url = $this->find_group_login_redirect($user_obj))
		{
			;
		}
		elseif (aw_global_get("request_uri_before_auth") != "")
		{
			$url = aw_global_get("request_uri_before_auth");
		}
		elseif (!empty($params["return"]))
		{
			$url = $params["return"];
		}


		if (!$url)
		{
			$la = get_instance("languages");
			$ld = $la->fetch(aw_global_get("lang_id"));

			$url = $this->get_cval("after_login_".$ld["acceptlang"]);
			if (!$url)
			{
				$url = $this->get_cval("after_login");
			}
		}

		if ($user_obj->prop("after_login_redir") != "")
		{
			$url = $user_obj->prop("after_login_redir");
		}

		if (!$url)
		{
			$url = aw_ini_get("baseurl");
		}

		if ($url[0] === "/")
		{
			$bits = parse_url(aw_ini_get("baseurl"));
			if (!empty($bits["path"]))
			{
				$url = str_replace($bits["path"], "", $url);
			}

			$url = aw_ini_get("baseurl").substr($url, 1);
		}

		return $url;
	}

	private function _handle_failed_login($params, $msg)
	{
		unset($_SESSION["uid"]);
		aw_global_set("uid", "");

		if (isset($params["remote_auth"]) and $params["remote_auth"] == 1)
		{
			die("0");
		}

		$msg = t("Vigane kasutajanimi v&otilde;i parool");

		$_msg = aw_ini_get("users.login_failed_msg");
		if ($_msg != "")
		{
			$msg = $_msg;
		}

			if (!empty($params["failed_url"]))
			{
				$redir_url = urldecode($params["failed_url"]);
			}
			else
			{
				$redir_url = aw_ini_get("users.redir_on_failed_login");
			}

			if (!$redir_url)
			{
				$redir_url = aw_ini_get("baseurl")."login" . AW_FILE_EXT;
			}

		$si = __get_site_instance();
		if (method_exists($si, "handle_failed_login"))
		{
			$si->handle_failed_login($params, $msg, $redir_url);
		}
		header("Refresh: 1;url=".$redir_url);
		print $msg;
		exit;
	}

	/** logs the current user out and destroys the session
		@attrib api=1 params=name

		@param redir_to optional type=string
			The url where to redirect the user after logout
	**/
	function logout($arr = array())
	{
		$uid = aw_global_get("uid");
		$ma = -1;
		session_cache_limiter("must-revalidate, max-age=".$ma);
		header("Cache-Control: must-revalidate, max-age=".$ma);
		header("Expires: ".gmdate("D, d M Y H:i:s",time()+$ma)." GMT");
		aw_global_set("uid", "");
		aw_global_set("user_history_count", "");
		aw_global_set("uid_oid", "");
		unset($_SESSION["uid"]);
		unset($_SESSION["uid_oid"]);
		unset($_SESSION["user_history_count"]);
		unset($_SESSION["auth_redir_post"]);
		post_message("MSG_USER_LOGOUT", array("uid" => $uid));

		$url = !empty($arr["redir_to"]) ? $arr["redir_to"] : aw_ini_get("baseurl") . "automatweb/";
		return $url;
	}

	private function find_group_login_redirect($user_obj)
	{
		$c = new config();
		$ra = $c->get_grp_redir();

		// since the user is not logged in already, we must read the gidlist, cause it is not loaded yet
		$gidlist = $user_obj->get_groups_for_user();
		$d_gid = 0;
		$d_pri = 0;
		$d_url = "";
		foreach($gidlist as $g_obj)
		{
			$gid = $g_obj->prop("gid");
			if (isset($ra[$gid]["pri"]) && $ra[$gid]["pri"] >= $d_pri && !empty($ra[$gid]["url"]))
			{
				$d_gid = $gid;
				$d_pri = $ra[$gid]["pri"];
				$d_url = $ra[$gid]["url"];
			}
		}
		if ($d_url != "")
		{
			return $d_url;
		}
		return false;
	}

	/** returns the oid for the given uid. but this is deprecated, because you should be using user oid's, not uids.
	**/
	function get_oid_for_uid($uid)
	{
		if (!($ret = aw_cache_get("get_oid_for_uid", $uid)))
		{
			$ret = $this->db_fetch_field("SELECT oid FROM users WHERE uid = '$uid'", "oid");
			aw_cache_set("get_oid_for_uid", $uid, $ret);
		}
		return $ret;
	}
}

