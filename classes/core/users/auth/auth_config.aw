<?php
// auth_config.aw - Autentimise Seaded
/*

@classinfo syslog_type=ST_AUTH_CONFIG relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@groupinfo servers caption=Autentimine

@property servers type=table group=servers no_caption=1

@groupinfo activity caption=Aktiivsus

@property activity type=table group=activity no_caption=1
@caption Aktiivsus

@reltype AUTH_SERVER value=1 clid=CL_AUTH_SERVER_LDAP,CL_AUTH_SERVER_LOCAL,CL_AUTH_SERVER_NDS,CL_AUTH_SERVER_OPENLDAP,CL_AUTH_SERVER_USERDEFINED
@caption autentimisserver

*/

class auth_config extends class_base
{
	function auth_config()
	{
		$this->init(array(
			"tpldir" => "core/users/auth/auth_config",
			"clid" => CL_AUTH_CONFIG
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "activity":
				$this->mk_activity_table($arr);
				break;

			case "servers":
				$this->do_servers($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "activity":
				$ol = new object_list(array(
					"class_id" => CL_AUTH_CONFIG,
					"lang_id" => array(),
					"site_id" => array()
				));
				for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					if ($o->flag(OBJ_FLAG_IS_SELECTED) && $o->id() != $arr["request"]["active"])
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, false);
						$o->save();
					}
					else
					if ($o->id() == $arr["request"]["active"] && !$o->flag(OBJ_FLAG_IS_SELECTED))
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, true);
						$o->save();
					}
				}
				break;

			case "servers":
				$arr["obj_inst"]->set_meta("auth", $arr["request"]["data"]);
				break;
		}
		return $retval;
	}

	function mk_activity_table($arr)
	{
		// this is supposed to return a list of all authconfigs
		// to let the user choose the active one
		$table = &$arr["prop"]["vcl_inst"];
		$table->parse_xml_def("activity_list");

		$pl = new object_list(array(
			"class_id" => CL_AUTH_CONFIG,
			"site_id" => array(),
			"lang_id" => array()
		));
		for($o = $pl->begin(); !$pl->end(); $o = $pl->next())
		{
			$actcheck = checked($o->flag(OBJ_FLAG_IS_SELECTED));
			$act_html = "<input type='radio' name='active' $actcheck value='".$o->id()."'>";
			$row = $o->arr();
			$row["active"] = $act_html;
			$table->define_data($row);
		}
	}

	function _init_servers_tbl($t)
	{
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Serveri t&uuml;&uuml;p"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "use",
			"caption" => t("Kasuta?"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "int_name",
			"caption" => t("Nimetus"),
			"align" => "center"
		));
	}

	function do_servers($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_servers_tbl($t);

		$clss = aw_ini_get("classes");

		$data = $arr["obj_inst"]->meta("auth");

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_AUTH_SERVER")) as $c)
		{
			$serv = $c->to();
			$t->define_data(array(
				"type" => $clss[$serv->class_id()]["name"],
				"name" => $serv->name(),
				"use" => html::checkbox(array(
					"name" => "data[".$serv->id()."][use]",
					"value" => 1,
					"checked" => ($data[$serv->id()]["use"] == 1)
				)),
				"jrk" => html::textbox(array(
					"name" => "data[".$serv->id()."][jrk]",
					"value" => $data[$serv->id()]["jrk"],
					"size" => 4
				)),
				"hidden_jrk" => $data[$serv->id()]["jrk"],
				"int_name" => html::textbox(array(
					"name" => "data[".$serv->id()."][int_name]",
					"value" => $data[$serv->id()]["int_name"],
					"size" => 10
				)),
			));
		}

		$t->set_default_sortby("hidden_jrk");
		$t->sort_by();
	}

	/** checks if any auth config is active and if so, returns it's id

		@attrib api=1
	**/
	function has_config()
	{
		aw_disable_acl();
		$ol = new object_list(array(
			"class_id" => CL_AUTH_CONFIG,
			"flags" => array(
				"mask" => OBJ_FLAG_IS_SELECTED,
				"flags" => OBJ_FLAG_IS_SELECTED
			),
			"lang_id" => array(),
			"site_id" => array()
		));
		if ($ol->count())
		{
			$tmp = $ol->begin();
			// also check if there are any servers in it. if not, then it is not valid.
			$servers = self::_get_auth_servers($tmp->id());
			aw_restore_acl();
			if (!count($servers))
			{
				return false;
			}
			return $tmp->id();
		}
		aw_restore_acl();
		return false;
	}

	/** authenticates the given user agains the given authentication config

		@attrib api=1

		@comment
			auth_id - the config id to auth against
			credentials - array of uid => username, password => password for the user to be authenticated
	**/
	function check_auth($auth_id, $credentials)
	{
		// get list of servers, sort by order and try each one
		$servers = $this->_get_auth_servers($auth_id);
		$settings = obj($auth_id);
		$dat = $settings->meta("auth");

		// go over servers and all of them have different names then no need to check for empty
		$nms = array();
		$diff = true;
		foreach($servers as $server)
		{
			if (isset($nms[$dat[$server->id()]["int_name"]]))
			{
				$diff = false;
			}
			$nms[$dat[$server->id()]["int_name"]] = 1;
		}

		foreach($servers as $server)
		{
			if ($diff || !empty($credentials["server"]))
			{
				if ($dat[$server->id()]["int_name"] != $credentials["server"])
				{
					continue;
				}
			}

			$server_inst = get_instance($server->class_id());
			list($is_valid, $msg, $break_chain) = $server_inst->check_auth($server, $credentials, $this);

			if ($is_valid)
			{
				return array(true, $msg);
			}
			elseif ($break_chain)
			{
				break;
			}
		}

		if (empty($msg))
		{
			$msg = t("Sellist kasutajat pole!");
		}

		return array(false, $msg);
	}

	/** returns sorted array of server id's
	**/
	function _get_auth_servers($id)
	{
		if (!is_oid($id))
		{
			return array();
		}
		aw_disable_acl();
		$o = obj($id);
		$s = self::_get_server_list($o);
		asort($s);
		$tmp = array_keys($s);
		$ret = array();
		foreach($tmp as $sid)
		{
			$ret[] = obj($sid);
		}
		aw_restore_acl();
		return $ret;
	}

	/** returns array id => jrk
	**/
	function _get_server_list($o)
	{
		$ret = array();
		$s = $o->meta("auth");
		aw_disable_acl();
		foreach($o->connections_from(array("type" => "RELTYPE_AUTH_SERVER")) as $c)
		{
			$to_id = $c->prop("to");
			if (isset($s[$to_id]["use"]) and $s[$to_id]["use"] == 1)
			{
				$ret[$to_id] = $s[$to_id]["jrk"];
			}
		}

		aw_restore_acl();
		return $ret;
	}

	/** checks if the given local user exists and if not, creates it

		@attrib api=1

	**/
	function check_local_user($auth_id, &$cred)
	{
		$confo = obj($auth_id);
		if ($confo->prop("aw_user_prefix"))
		{
			$cred["uid"] = $confo->prop("aw_user_prefix").".".$cred["uid"];
		}

		if (!empty($cred["server"]))
		{
			$cred["uid"] .= ".".$cred["server"];
		}

		$ol = new object_list(array(
			"class_id" => CL_USER,
			"name" => $cred["uid"],
			"brother_of" => new obj_predicate_prop("id")
		));
		$confo = obj($auth_id);

		$has = false;
		$obo = false;
		foreach($ol->arr() as $_o)
		{
			if (strtolower(trim($_o->prop("uid"))) == strtolower(trim($cred["uid"])))
			{
				$has = true;
				$obo = $_o;
				break;
			}
		}

		if ($has)
		{
			// check e-mail and name if present in $cred
			$this->_upd_udata($obo, $cred, $confo);
			return true;
		}

		if (!$confo->prop("auto_create_user"))
		{
			return false;
		}

		$pass = $cred["password"];

		if ($confo->prop("no_save_pwd"))
		{
			$pass = "-";
		}

		// create local user
		$us = new user();
		$new_user = $us->add_user(array(
			"uid" => $cred["uid"],
			"password" => $pass
		));

		$this->_upd_udata($new_user, $cred, $confo);
		return true;
	}

	/** Generates the login form

		@attrib name=show_login params=name nologin="1" default="0"
		@returns
		@comment

	**/
	function show_login($args = array())
	{
		if (($auth_srv = aw_ini_get("auth.central_server")) != "")
		{
			header("Location: ".$auth_srv."/?sid=".aw_ini_get("site_id"));
			die();
		}

		if (($port = aw_ini_get("auth.display_over_ssl_port")) > 0)
		{
			if (!$_SERVER["HTTPS"])
			{
				$bits = parse_url(aw_ini_get("baseurl"));
				header("Location: https://".$bits["host"].":".$port.aw_global_get("REQUEST_URI"));
				die();
			}
		}

		$tpl = "login.tpl";
		if (aw_ini_get("user_interface.default_language") === "en")
		{
			$tpl = "login_en.tpl";
		}

		$this->read_adm_template($tpl);
		// remember the uri used before login so that we can
		// redirect the user back there after (and if) he/she has finally
		// logged in
		if (is_array($_POST) and count($_POST) and "users" !== $_POST["class"] and "login" !== $_POST["action"])
		{
			$_SESSION["auth_redir_post"] = $_POST;
		}
		$_SESSION["request_uri_before_auth"] = aw_global_get("REQUEST_URI");
		$this->vars(array(
			"reforb" => $this->mk_reforb("login",array(),'users')
		));

		if (is_oid($ac_id = auth_config::has_config()))
		{
			$sl = $this->get_server_ext_list($ac_id);
			$this->vars(array(
				"servers" => $this->picker(-1, $sl)
			));
			if (count($sl))
			{
				$this->vars(array(
					"SERVER_PICKER" => $this->parse("SERVER_PICKER")
				));
			}

			if (aw_ini_get("users.id_login_url"))
			{
				$this->vars(array(
					"id_login_url" => str_replace("http:", "https:", aw_ini_get("baseurl"))."/".aw_ini_get("users.id_login_url"),
				));
				$this->vars(array(
					"ID_LOGIN" => $this->parse("ID_LOGIN")
				));
			}
		}

		if (!aw_ini_get("login_box.hide_aw_info"))
		{
			$this->vars(array(
				"aw_info" => $this->parse("aw_info")
			));
		}
		return $this->parse();
	}

	function get_server_ext_list($id)
	{
		$o = obj($id);
		$dat = $o->meta("auth");
		$srvs = $this->_get_auth_servers($id);
		$ret = array();
		$empty = true;
		foreach($srvs as $srv)
		{
			$ret[$dat[$srv->id()]["int_name"]] = $srv->name();
			if ($dat[$srv->id()]["int_name"])
			{
				$empty = false;
			}
		}
		if ($empty)
		{
			return array();
		}
		return $ret;
	}

	/** if the current page requires login, then remember the url, ask for login and put the user back

	**/
	function redir_to_login()
	{
		$_SESSION["request_uri_before_auth"] = aw_global_get("REQUEST_URI");
		header("Location: ".aw_ini_get("baseurl")."/login.".aw_ini_get("ext"));
		die();
	}

	function _upd_udata($u, $cred, $confo)
	{
		$u->set_prop("email", $cred["mail"]);
		$u->set_prop("real_name", $cred["name"]);
		$u->save();
		// get group from auth conf
		if (($grp = $confo->prop("no_user_grp")))
		{
			// add to group
			$gp = new group();
			$gp->add_user_to_group($u, obj($grp));
		}
	}

	public static function get_login_fail_msg()
	{
		return t("Ligip&auml;&auml;s puudub");
	}
}
?>
