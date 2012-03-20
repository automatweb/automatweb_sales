<?php

class users extends users_user implements request_startup, orb_public_interface
{
	public $hfid = 0;

	protected $req;

	function users()
	{
		$this->init("automatweb/users");
	}

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	/** generates the form for changing the current users password
		@attrib name=change_pwd params=name is_public="1" caption="Change password"

		@param error optional
	**/
	function change_pwd($arr)
	{
		$uo = obj(aw_global_get("uid_oid"));
		$this->read_template("changepwd.tpl");
		$this->vars(array(
			"email" => $uo->prop("email"),
			"error" => $arr["error"],
			"reforb" => $this->mk_reforb("submit_change_pwd")
		));
		return $this->parse();
	}

	/** Generates the form for changing any user's password while being logged out
		@attrib name=change_password_not_logged nologin=1 is_public="1"

		@param uid required
			The user's oid whose password to change

		@param error optional
	**/
	function change_password_not_logged($arr)
	{
		$uo = obj($arr["uid"]);
		$this->read_template("changepwdnotlogged.tpl");
		$this->vars(array(
			"username" => $uo->prop('uid'),
			"user_oid" => $arr["uid"],
			"error" => $arr["error"],
		));
		return $this->parse();
	}


	/**
		@attrib name=submit_change_password_not_logged nologin=1 is_public="1"
		@param username optional
		@param old_pass optional
		@param new_pass optional
		@param new_pass_repeat optional
	**/
	function submit_change_password_not_logged($arr)
	{
		extract($arr);
		if(!$username || !$old_pass || !$new_pass || !$new_pass_repeat)
		{
			$error = t("K&otilde;ik v&auml;ljad peavad olema t&auml;idetud");
		}
		elseif($new_pass != $new_pass_repeat)
		{
			$error = t("Uus parool ja parooli kordus ei ole samad");
		}
		elseif($new_pass == $old_pass)
		{
			$error =  t("Te ei tohi panna uuesti sama vana parooli");
		}
		elseif(!is_valid("password", $old_pass))
		{
			$error = t("Vigane v&otilde;i vale parool");
		}
		else
		{
			$auth = new auth_server_local();
			$uo = obj($username);
			list($success, $error) = $auth->check_auth(NULL, array(
				"uid" =>  $uo->prop("uid"),
				"password" => $old_pass,
				"pwdchange" => 1
			));
			if(!$success)
			{
				$error = t("Vana parool on vale");
			}
		}

		if($error)
		{
			return $this->mk_my_orb("change_password_not_logged", array(
				"error" => $error,
				"uid" => $username,
			), "users");
		}
		elseif ($success)
		{
			$user_obj = obj($username);
			$logins = $user_obj->prop("logins") + 1;
			$user_obj->set_prop("logins", $logins);
			$user_obj->set_password($new_pass);
			$user_obj->save();

			return $this->login(array(
				"uid" => $user_obj->prop("uid"),
				"password" => $new_pass,
			));
		}
	}

	/** saves the users changed password
		@attrib name=submit_change_pwd params=name
	**/
	function submit_change_pwd($arr)
	{
		extract($arr);
		if ($arr["pwd"] != $arr["pwd2"])
		{
			return $this->mk_my_orb("change_pwd", array("error" => t("Paroolid peavad olema samad!")));
		}

		if (!is_valid("password",$pwd))
		{
			return $this->mk_my_orb("change_pwd", array("error" => t("Uus parool sisaldab lubamatuid m&auml;rke<br />")));
		}

		$oid = aw_global_get("uid_oid");
		$o = obj($oid);
		$o->set_password($arr["pwd"]);
		$o->set_prop("email",$arr["email"]);
		$o->save();

		if ($send_welcome_mail)
		{
			// send him some email as well if the users selected to do so
			$this->send_welcome_mail(array(
				"u_uid" => $oid,
				"pass" => $arr["pwd"]
			));
		}

		$this->_log(ST_USERS, SA_CHANGE_PWD, $o->prop("uid"));
		header("Refresh: 2;url=".aw_ini_get("baseurl"));
		die(t("Parool on edukalt vahetatud"));
	}

	/** Generates an unique hash, which when used in a url can be used to let the used change his/her password
		@attrib name=send_hash params=name nologin="1"
	**/
	function send_hash($args = array())
	{
		if (!aw_ini_get("auth.md5_passwords"))
		{
			return t("<font color=red>This site does not use encrypted passwords and therefore this function does not work</font>");
		}

		$this->read_template("send_hash.tpl");

		$this->vars(array(
			"webmaster" => aw_ini_get("users.webmaster_mail"),
			"reforb" => $this->mk_reforb("submit_send_hash",array("section" => aw_global_get("section"))),
		));
		unset($_SESSION["status_msg"]);
		return $this->parse();
	}

	/** Handles hash sender submit
		@attrib name=submit_send_hash params=name nologin=1
	**/
	function submit_send_hash($args = array())
	{
		extract($args);
		if (($type === "uid") && !is_valid("uid",$uid))
		{
			aw_session_set("status_msg",t("Vigane kasutajanimi"));
			return $this->mk_my_orb("send_hash",array());
		}

		if (($type === "email") && !is_email($email))
		{
			aw_session_set("status_msg",t("Vigane e-posti aadress"));
			return $this->mk_my_orb("send_hash",array());
		}

		$filt = array(
			"class_id" => CL_USER,
			"blocked" => new obj_predicate_not(1),
			"brother_of" => new obj_predicate_prop("id")
		);

		if ($type === "uid")
		{
			$filt["uid"] = $uid;
		}
		else
		{
			$filt["email"] = $email;
		}

		$ol = new object_list($filt);
		foreach($ol->arr() as $o)
		{
			if (!is_email($o->prop("email")))
			{
				$status_msg .= sprintf(t("Kasutajal %s puudub korrektne e-posti aadress. Palun p&ouml;&ouml;rduge veebisaidi haldaja poole"), $o->prop("uid"));
				aw_session_set("status_msg", $status_msg);
				return $this->mk_my_orb("send_hash",array());
			};

			$this->read_template("hash_send.tpl");
			$this->vars(array(
				"churl" => $this->get_change_pwd_hash_link($o->id()),
				"email" => aw_ini_get("users.webmaster_mail"),
				"name_wm" => aw_ini_get("users.webmaster_name"),
				"uid" => $o->prop("uid"),
				"host" => aw_global_get("HTTP_HOST"),
			));
			$msg = $this->parse();
			$from = sprintf("%s <%s>", aw_ini_get("users.webmaster_name"), aw_ini_get("users.webmaster_mail"));
			send_mail(
				$o->prop("email"),
				sprintf(t("Paroolivahetus saidil %s"), aw_global_get("HTTP_HOST")),
				$msg,
				"From: $from"
			);
			aw_session_set(
				"status_msg",
				sprintf(t("Parooli muutmise link saadeti  aadressile <b>%s</b>. Vaata oma postkasti<br />T&auml;name!<br />"), $o->prop("email"))
			);
		}

		exit;
		// return $this->mk_my_orb("send_hash",array("section" => $args["section"]));
	}

	/** Allows the user to change his/her password from the link in the email sent by submit_send_hash
		@attrib name=pwhash params=name nologin="1"

		@param k required
		@param u required
	**/
	function password_hash($args = array())
	{
		extract($args);
		$uid = $u;
		$key = $k;
		if (!(is_valid("uid",$uid)))
		{
			$this->read_adm_template("hash_results.tpl");
			$this->vars(array(
				"msg" => t("Vigane kasutajanimi"),
			));
			return $this->parse();
		}

		$filt = array(
			"class_id" => CL_USER,
			"blocked" => new obj_predicate_not(1),
			"uid" => $uid
		);
		$ol = new object_list($filt);
		if (!$ol->count())
		{
			$this->read_adm_template("hash_results.tpl");
			$this->vars(array(
				"msg" => t("Sellist kasutajat pole registreeritud"),
			));
			return $this->parse();
		}

		$uo = $ol->begin();

		$pwhash = $uo->meta("password_hash");
		if ($pwhash != $key)
		{
			$this->read_adm_template("hash_results.tpl");
			$this->vars(array(
				"msg" => t("Sellist v&otilde;tit pole v&auml;ljastatud"),
			));
			return $this->parse();
		}

		$ts = $uo->meta("password_hash_timestamp");

		// default expiration time is 1 hour (3600 seconds)
		if (($ts + (3600*24*400)) < time())
		{
			$this->read_adm_template("hash_results.tpl");
			$this->vars(array(
				"msg" => t("See v&otilde;ti on juba aegunud")." <a href='".$this->mk_my_orb('send_hash')."'>".t("Telli uusi v&otilde;ti")."</a>"
			));
			return $this->parse();
		}

		$this->read_adm_template("hash_change_password.tpl");
		$this->vars(array(
			"uid" => $uid,
			"reforb" => $this->mk_reforb("submit_password_hash",array("uid" => $uo->id(),"pwhash" => $pwhash)),
		));
		unset($_SESSION["status_msg"]);
		return $this->parse();
	}

	/** Submits the password change form based on hash
		@attrib name=submit_password_hash params=name nologin="1"
	**/
	function submit_password_hash($args = array())
	{
		extract($args);
		$uo = obj($args["uid"]);
		if ($uo->class_id() != CL_USER && $uo->prop("blocked") != 1)
		{
			aw_session_set("status_msg",t("Sellist kasutajat pole registreeritud"));
			return $this->mk_my_orb("send_hash",array());
		}

		$pwhash1 = $uo->meta("password_hash");
		if ($pwhash1 !== $pwhash)
		{
			aw_session_set("status_msg",t("Sellist v&otilde;tit pole v&auml;ljastatud"));
			return $this->mk_my_orb("pwhash",array("u" => $uo->prop("uid"),"k" => $pwhash));
		}

		if (!is_valid("password",$pass1))
		{
			aw_session_set("status_msg",t("Parool sisaldab keelatud m&auml;rke"));
			return $this->mk_my_orb("pwhash",array("u" => $uo->prop("uid"),"k" => $pwhash));
		}

		if ($pass1 !== $pass2)
		{
			aw_session_set("status_msg",t("Paroolid peavad olema &uuml;hesugused"));
			return $this->mk_my_orb("pwhash",array("u" => $uo->prop("uid"),"k" => $pwhash));
		}
		$uo->set_password($pass1);
		$uo->save();

		$this->_log(ST_USERS, SA_CHANGE_PWD, $uo->prop("uid"));
		aw_session_set("status_msg","<b><font color=green>".t("Parool on edukalt vahetatud.")."</font></b>");
		return $this->login(array("uid" => $uo->prop("uid"), "password" => $newpass));
	}

	private function create_gidlists($u_oid)
	{
		$gidlist = array();
		$gidlist_pri = array();
		$gidlist_pri_oid = array();
		$gidlist_oid = array();

		if (!empty($_SESSION["nliug"]) && !is_admin())
		{
			// get gid for oid
			$nliug_o = obj($_SESSION["nliug"]);
			$gidlist[$nliug_o->prop("gid")] = $nliug_o->prop("gid");
			$gidlist_pri[$nliug_o->prop("gid")] = $nliug_o->prop("priority");
			$gidlist_oid[$nliug_o->id()] = $nliug_o->id();
			$gidlist_pri_oid[(int)$nliug_o->id()] = (int)$nliug_o->prop("priority");
		}
		elseif ($u_oid)
		{
			$u_obj = obj($u_oid, array(), user_obj::CLID);
			$gl = $u_obj->get_groups_for_user();

			foreach($gl as $g_oid => $g_obj)
			{
				$gid = $g_obj->prop("gid");
				$gidlist[(int)$gid] = (int)$gid;
				$gidlist_pri[(int)$gid] = (int)$g_obj->prop("priority");
				$gidlist_pri_oid[(int)$g_oid] = (int)$g_obj->prop("priority");
				$gidlist_oid[(int)$g_oid] = (int)$g_oid;
			}
		}

		aw_global_set("gidlist", $gidlist);
		aw_global_set("gidlist_pri", $gidlist_pri);
		aw_global_set("gidlist_pri_oid", $gidlist_pri_oid);
		aw_global_set("gidlist_oid", $gidlist_oid);
	}

	public function request_startup()
	{
		if (isset($_GET["set_group"]) && acl_base::can("", $_GET["set_group"]))//XXX: mis see on?
		{
			// fetch thegroup and check if non logged users can switch to it
			$setg_o = obj($_GET["set_group"]);
			if ($setg_o->prop("for_not_logged_on_users") == 1)
			{
				$_SESSION["nliug"] = $_GET["set_group"];
				$_COOKIE["nliug"] = $_GET["set_group"];
			}
		}

		if (!empty($_GET["clear_group"]))
		{
			unset($_SESSION["nliug"]);
			unset($_COOKIE["nliug"]);
		}

		if ((!empty($_COOKIE["nliug"]) || !empty($_SESSION["nliug"])) && $_COOKIE["nliug"] != $_SESSION["nliug"] && $_COOKIE["nliug"])
		{
			$_SESSION["nliug"] = $_COOKIE["nliug"];
		}

		if (!isset($_SESSION["nliug"]))
		{
			$_SESSION["nliug"] = null;
		}

		if ($uid = aw_session::get("uid"))
		{
			if(!aw_session::get("uid_oid"))
			{
				aw_session::set("uid_oid", $this->get_oid_for_uid(aw_global_get("uid")));
			}

			$this->create_gidlists(aw_session::get("uid_oid"));
			$gidlist_pri_oid = aw_global_get("gidlist_pri_oid");
			if (count($gidlist_pri_oid) < 1)
			{
				$this->logout();
			}
			// get highest priority group
			$hig = 0;
			$hig_o = null;
			$hig_p = -1;
			$hig_w_u = 0;
			$hig_w_u_p = -1;
			foreach($gidlist_pri_oid as $g_oid => $_pri)
			{
				if ($_pri > $hig_p && $_pri != 100000000)
				{
					$hig_p = $_pri;
					$hig_o = $g_oid;
				}
				if ($_pri > $hig_w_u_p)
				{
					$hig_w_u_p = $_pri;
					$hig_w_u = $g_oid;
				}
			}

			if ($hig_o)
			{
				$this->_init_group_settings($hig_o, true);
			}

			if ($hig_w_u)
			{
				$this->_init_group_settings($hig_w_u, false);
			}

			if (aw_ini_get("groups.multi_group_admin_rootmenu"))
			{
				$admr = array();
				foreach($gidlist_pri_oid as $g_oid => $_pri)
				{
					$o = obj($g_oid);
					$ar2 = $this->_get_admin_rootmenu_from_group($o, true);
					if ($ar2 !== null)
					{
						$awa = new aw_array($ar2);
						foreach($awa->get() as $k => $v)
						{
							$admr[] = $v;
						}
					}
					$admr = array_unique($admr);
				}
				if (count($admr))
				{
					aw_ini_set("admin_rootmenu2",$admr);
				}
			}
		}
		else
		{
			// no user is logged in. what we need to do here is check if a not-logged-in user group exists
			// and if it does, then set the gidlist accordingly
			// if not, then create a group for them under the groups folder
			// now the only problem is how do I identify the group.
			// that's gonna be a problem, but I guess the only way is the config table.

			$nlg_o = user_manager_obj::get_not_logged_in_group();
			$nlg = $nlg_o->prop("gid");

			$gidlist = array($nlg => $nlg);
			$gidlist_pri = array($nlg => $nlg_o->prop("priority"));
			$gidlist_oid = array($nlg_o->id() => $nlg_o->id());
			$gidlist_pri_oid[(int)$nlg_o->id()] = (int)$nlg_o->prop("priority");
			if (!empty($_SESSION["nliug"]))
			{
				// get gid for oid
				$nliug_o = obj($_SESSION["nliug"]);
				$gidlist[$nliug_o->prop("gid")] = $nliug_o->prop("gid");
				$gidlist_pri[$nliug_o->prop("gid")] = $nliug_o->prop("priority");
				$gidlist_oid[$nliug_o->id()] = $nliug_o->id();
				$gidlist_pri_oid[(int)$nliug_o->id()] = (int)$nliug_o->prop("priority");
			}

			aw_global_set("gidlist", $gidlist);
			aw_global_set("gidlist_pri", $gidlist_pri);
			aw_global_set("gidlist_oid", $gidlist_oid);
			aw_global_set("gidlist_pri_oid", $gidlist_pri_oid);
		}

		if (!is_array(aw_global_get("gidlist")))
		{
			aw_global_set("gidlist", array());
			aw_global_set("gidlist_pri", array());
		}
	}

	/** Returns a link that allows the user to change his/her password
		@attrib api=1 params=pos

		@param u_oid required type=oid
			The user object oid whose password can be changed
	**/
	function get_change_pwd_hash_link($u_oid)
	{
		$ts = time();
		$hash = substr(gen_uniq_id(),0,15);

		$uo = obj($u_oid);
		$uo->set_meta("password_hash",$hash);
		$uo->set_meta("password_hash_timestamp",$ts);
		if ($uo->parent())
		{
			$uo->save();
		}

		$host = aw_global_get("HTTP_HOST");
		return str_replace("orb.aw", "index.aw", str_replace("/automatweb", "", $this->mk_my_orb("pwhash",array(
			"u" => $uo->prop("uid"),
			"k" => $hash,
			"section" => $this->get_cval("join_hash_section".aw_global_get("LC"))
		),"users",0,0)));
	}

	function on_site_init($dbi, $site, &$ini_opts, &$log, &$osi_vars)
	{
		if ($site['site_obj']['use_existing_database'])
		{
			// fetch the neede ini opts from the base site
			$opts = $this->do_orb_method_call(array(
				"class" => "objects",
				"action" => "aw_ini_get_mult",
				"params" => array(
					"vals" => array(
						"groups.tree_root",
						"groups.all_users_grp",
						"auth.md5_passwords",
					)
				)
			));

			$ini_opts["groups.tree_root"] = $opts["groups.tree_root"];
			$ini_opts["groups.all_users_grp"] = $opts["groups.all_users_grp"];
			$ini_opts["auth.md5_passwords"] = $opts["auth.md5_passwords"];
		}
		else
		{
			// create default group
			$this->dc = $dbi->dc;

			echo "adding groups... <br>\n";
			flush();

			$grp_i = new group();
			$aug = obj($grp_i->add_group($ini_opts["groups.tree_root"],"K&otilde;ik kasutajad", aw_groups::TYPE_REGULAR, 1000));
			$ini_opts["groups.all_users_grp"] = $aug->prop("gid");

			$admg = $grp_i->add_group($ini_opts["groups.tree_root"],"Administraatorid", aw_groups::TYPE_REGULAR,10000);
			echo "Administraatorid <br>\n";
			flush();
			$osi_vars["groups.admins"] = $admg;

			$nlg = obj($grp_i->add_group($ini_opts["groups.tree_root"], "Sisse logimata kasutajad", aw_groups::TYPE_REGULAR, 1));
			$this->set_cval("non_logged_in_users_group", $nlg->prop("gid"));
			$osi_vars["groups.not_logged"] = $nlg->id();

			// deny access from aw_obj_priv
			$o = obj($osi_vars["aw_obj_priv"]);
			$o->connect(array(
				"to" => $nlg->id(),
				"reltype" => RELTYPE_ACL,
			));
			acl_base::save_acl($o->id(), $nlg->prop("gid"), array());

			echo "Sisse logimata kasutajad <br>\n";
			flush();


			// give admins access to admin interface
			aw_global_set("__in_post_message", 1);
			$admo = obj($admg);
			$admo->set_prop("can_admin_interface", 1);
			$admo->save();

			$editors = $grp_i->add_group($ini_opts["groups.tree_root"],"Toimetajad", aw_groups::TYPE_REGULAR,5000);
			echo "Toimetajad <br>\n";
			flush();
			$osi_vars["groups.editors"] = $editors;

			// create default user
			$us = new user();
			$user_o = $us->add_user(array(
				"uid" => $site["site_obj"]["default_user"],
				"password" => $site["site_obj"]["default_user_pwd"],
				"all_users_grp" => $aug->prop("gid"),
				"use_md5_passwords" => true,
				"obj_parent" => $ini_opts["users.root_folder"],
				"aug_oid" => $aug->id()
			));
			$user_o->set_parent($ini_opts["users.root_folder"]);
			$user_o->save();
			$last_user_oid = $user_o->id();
			echo "Adding users... <br>\n";
			flush();
			echo "adding user to groups! <br>\n";
			flush();
			$this->_install_create_g_u_o_rel($last_user_oid, $admg);
			echo "administrator <br>\n";
			flush();
			$this->_install_create_g_u_o_rel($last_user_oid, $aug->id());
			echo "all users <br>\n";
			flush();
			aw_global_set("__in_post_message", 0);
			$ini_opts["auth.md5_passwords"] = 1;
		}
	}

	private function _install_create_g_u_o_rel($u_oid, $g_oid)
	{
		// create objects
		$u_o = obj($u_oid);
		$u_o->create_brother($g_oid);
		$u_o->connect(array(
			"to" => $g_oid,
			"reltype" => "RELTYPE_GRP" // from user
		));

		$g_o = obj($g_oid);
		$g_o->connect(array(
			"to" => $u_o->id(),
			"reltype" => "RELTYPE_MEMBER" // from group
		));
	}

	/** sends user welcome mail to user and others
		@attrib api=1 params=name

		@param u_oid required type=oid
			the user oid whose mail to send

		@param pass optional type=string
			if set, #password# is replaced by this, since passwords in db are hashed, we can't read it from there

		@comment
			Mail content is read from join_mail$LC in config table
	**/
	function send_welcome_mail($arr)
	{
		$o = obj($arr["u_uid"]);
		$c = new config();
		$mail = $c->get_simple_config("join_mail".aw_global_get("LC"));
		$mail = str_replace("#parool#", $arr["pass"],$mail);
		$mail = str_replace("#kasutaja#", $o->prop("uid"),$mail);
		$mail = str_replace("#pwd_hash#", $this->get_change_pwd_hash_link($o->id()), $mail);

		send_mail($o->prop("email"),$c->get_simple_config("join_mail_subj".aw_global_get("LC")),$mail,"From: ".$this->cfg["mail_from"]);
		$jsa = $c->get_simple_config("join_send_also");
		if ($jsa != "")
		{
			send_mail($jsa,$c->get_simple_config("join_mail_subj".aw_global_get("LC")),$mail,"From: ".$this->cfg["mail_from"]);
		}
	}

	/**
		@attrib params=pos
		@param first required type=string
		@param last required type=string
		@comment
		finds first available uid in format firstname.lastname[.###]
		etc: 'john.smith','johm.smith.051' ...
	**/
	private function _find_username($first, $last, $encoding)
	{
		$uid = $first.".".$last;
		if (!user_obj::is_uid($uid))
		{ // transliterate to ascii and remove symbol chars except dash, replace spaces with underscore
			try
			{
				$first = iconv($encoding, "us-ascii//TRANSLIT//IGNORE", $first);
				$last = iconv($encoding, "us-ascii//TRANSLIT//IGNORE", $last);
			}
			catch (ErrorException $e)
			{
				$encoding = mb_detect_encoding($first.$last);

				try
				{
					$first = iconv($encoding, "us-ascii//TRANSLIT//IGNORE", $first);
					$last = iconv($encoding, "us-ascii//TRANSLIT//IGNORE", $last);
				}
				catch (ErrorException $e)
				{ // all options depleted, try latin1
					$first = iconv("latin1", "us-ascii//TRANSLIT//IGNORE", $first);
					$last = iconv("latin1", "us-ascii//TRANSLIT//IGNORE", $last);
				}
			}

			// trim and replace spaces with dash
			/// replace dashes with spaces
			$first = str_replace("-", " ", $first);
			$last = str_replace("-", " ", $last);

			/// replace white space sequences with single dash
			$first = preg_replace("/\s+/", "-", $first);
			$last = preg_replace("/\s+/", "-", $last);

			// remove all other characters
			$uid_charset = str_replace(".", "", user_obj::UID_CHARSET);
			$first = preg_replace("/[^{$uid_charset}]/", "", $first);
			$last = preg_replace("/[^{$uid_charset}]/", "", $last);

			// combine name
			$uid = $first.".".$last;
		}

		$suffix = "";
		$count = 0;
		$user = new user();
		while(true)
		{
			$user_name = $uid.$suffix;
			if(!$user->username_is_taken($user_name))
			{
				return $user_name;
			}
			$count++;
			$suffix = ".".str_pad ($count, 3, "0", STR_PAD_LEFT);
			if($count > 999)
			{
				return false;
			}
		}
	}

	/** Logs user in with id-card over ssl.
		@attrib name=id_pre_login params=name nologin=1
	**/
	function id_pre_login($arr)
	{
		if ($_SERVER["HTTPS"] !== "on")
		{
			return aw_ini_get("baseurl");
		}

		// well.. this is a nice ocsp check. this checks wheater the user's certificate is valid at current point or not
		// when this feature is turned off(ocsp service is provided as a priced service(in id-card situation at least)), the function returs 'all okay'
		$ocsp = new ocsp();
		$ocsp_retval = $ocsp->OCSP_check($_SERVER["SSL_CLIENT_CERT"], $_SERVER["SSL_CLIENT_I_DN_CN"]);
		if($ocsp_retval !== 1)
		{
			return aw_ini_get("baseurl");
		}

		$act_inst = new id_config();

		// this little modafocka is here beacause estonian language has freaking umlauts etc..
		$data = $this->returncertdata($_SERVER["SSL_CLIENT_CERT"]);
		$certdata_pid = isset($data["pid"]) ? $data["pid"] : "";
		$personal_id_obj = new pid_et($certdata_pid);

		// check id card person file and cert data
		if (
			empty($data["f_name"]) or
			empty($data["l_name"]) or
			empty($data["pid"]) or
			!$personal_id_obj->is_valid()
		)
		{
			$tpl = new aw_template();
			$tpl->init(array(
				"tpldir" => "common/digidoc/idErr"
			));
			$tpl->read_template("error.tpl");
			die($tpl->parse());
		}

		$personal_id = $personal_id_obj->get();
		$pid_data_gender = $personal_id_obj->gender(1,2);
		$certdata_firstname = mb_convert_case($data["f_name"], MB_CASE_TITLE);
		$certdata_lastname = mb_convert_case($data["l_name"], MB_CASE_TITLE);

		if ($act_inst->use_safelist())
		{ // only people in safelist can log in with id card
			$sl = $act_inst->get_safelist();
			if(!in_array($personal_id, array_keys($sl)))
			{
				return aw_ini_get("baseurl");
			}
		}

		$q = "select o.brother_of as oid from objects o join kliendibaas_isik k on k.oid=o.oid where o.oid = o.brother_of and o.class_id = 145 and o.status > 0 and k.personal_id = {$personal_id}";
		$this->db_query($q);
		$person_data = $this->db_next();

		if ($this->db_next())
		{
			// log an error since data integrity not intact
			trigger_error(sprintf("Multiple persons found with personal id %s", $personal_id), E_USER_WARNING);
		}

		$this->db_free_result();

		if (empty($person_data["oid"]))
		{
			if (aw_ini_get("users.id_only_existing"))
			{ // only people who have a user object in this system can log in with id card
				return aw_ini_get("baseurl");
			}

			// add new person
			$person_obj = new object();
			$person_obj->set_class_id(CL_CRM_PERSON);
			$person_obj->set_parent(aw_ini_get("users.root_folder"));
			$person_obj->set_prop("personal_id", $personal_id);
			$person_obj->set_prop("firstname", $certdata_firstname);
			$person_obj->set_prop("lastname", $certdata_lastname);
			$person_obj->set_prop("gender", $pid_data_gender);
			$person_obj->save();
		}
		else
		{
			$person_obj = new object($person_data["oid"]);
			$_SESSION["__aw_person_oid"] = $person_obj->id();
		}

		// check person name
		if ($person_obj->prop("firstname") !== $certdata_firstname or $person_obj->prop("lastname") !== $certdata_lastname)
		{
			$person_obj->set_prop("firstname", $certdata_firstname);
			$person_obj->set_prop("lastname", $certdata_lastname);
			$person_obj->save();
		}

		// get user for person, create if not found
		$u_obj = $person_obj->get_user();
		$user_cl = new user();
		if (!$u_obj)
		{
			$uid = $this->_find_username($certdata_firstname, $certdata_lastname, languages::USER_CHARSET);
			$password = substr(gen_uniq_id(),0,8);
			$grs = $act_inst->get_ugroups();

			$u_obj = $user_cl->add_user(array(
				"uid" => $uid,
				"password" => $password,
				"real_name" => $certdata_firstname . " " . $certdata_lastname
			));
			// set new users user groups depending on the active id_config settings
			foreach ($grs as $gr)
			{
				$gr_inst = new group();
				$gr_obj = new object($gr);
				$gr_inst->add_user_to_group($u_obj, $gr_obj);
			}

			$person_obj->set_user($u_obj);

			$u_obj->connect(array(
				"to" => $person_obj->id(),
				"type" => "RELTYPE_PERSON"
			));

			$person_obj->save();
			$u_obj->save();
		}

		$uid = $u_obj->prop("name");
		$hash = gen_uniq_id();
		$q = "INSERT INTO user_hashes (hash, hash_time, uid) VALUES('".$hash."','".(time()+60)."','".$uid."')";
		$res = $this->db_query($q);
		return $this->login(array("hash" => $hash ,"uid" => $uid));
	}

	/** login

		@attrib name=login params=name nologin=1 is_public=1 caption="Logi sisse"

		@param uid required
		@param password optional
		@param remote_ip optional
		@param reforb optional
		@param remote_host optional
		@param return optional
		@param hash optional
		@param server optional
		@param remote_auth optional

		@returns


		@comment
			logs the user in, if all arguments are correct and redirects to the correct url

	**/
	function login($arr = array())
	{
		// if hash is given and it is in the db
		if (!empty($arr["hash"]))
		{
			$hash = $arr["hash"];
			$uid = $arr["uid"];
			$this->quote($hash);
			$this->quote($uid);

			$q = "
				SELECT
					*
				FROM
					user_hashes
				WHERE
					hash = '{$hash}' AND
					hash_time > ".time()." AND
					uid = '{$uid}'
			";
			$row = $this->db_fetch_row($q);
			if ($row["hash"] === $arr["hash"])
			{
				// do quick login
				$oid = $this->get_oid_for_uid($arr["uid"]);

				aw_session::set("uid", $uid);
				aw_session::set("uid_oid", $oid);
				aw_global_set("uid_oid", $oid);
				aw_global_set("uid", $uid);

				if (acl_base::can("", $oid))
				{
					$o = obj($oid);
					$_SESSION["user_history_count"] = $o->prop("history_size") ? $o->prop("history_size") : 25;
					$_SESSION["user_history_has_folders"] = $o->prop("history_has_folders");
				}

				$this->request_startup();

				// remove hash from usable hashes
				$this->db_query("DELETE FROM user_hashes WHERE hash = '{$hash}'");

				// remove stale hash table entries
				$this->db_query("DELETE FROM user_hashes WHERE hash_time < ".(time() - 60*24*3600));

				$url = ($t = urldecode(aw_global_get("request_uri_before_auth"))) ? $t : aw_ini_get("baseurl");
				if ($url === aw_ini_get("baseurl")."login.aw")
				{
					$url = aw_ini_get("baseurl");
				}

				if ($url{0} === "/")
				{
					$url = aw_ini_get("baseurl").substr($url, 1);
				}

				post_message("MSG_USER_LOGIN", array("uid" => $arr["uid"]));
				return $url;
			}
		}

		return parent::login($arr);
	}

	/** logs the current user out
		@attrib name=logout params=name nologin=1 is_public="1" caption="Logi v&auml;lja"
		@param redir_to optional
	**/
	function orb_logout($arr = array())
	{
		return parent::logout($arr);
	}

	/**	Returns true if user is logged in, false otherwise.
		@attrib api=1
		@returns boolean
	**/
	public static function is_logged_in(object $user = null)
	{
		if (null === $user)
		{
			return is_oid(aw_global_get("uid_oid"));
		}
		else
		{
			throw new aw_exception("Missing implementation!");
		}
	}

	// converts certificates subject value to current system character encoding
	private static function convert_cert_string($str)
	{
		$str = preg_replace("/\\\\x([0-9ABCDEF]{1,2})/e", "chr(hexdec('\\1'))", $str);
		$result = "";
		$encoding = mb_detect_encoding($str,"ASCII, ISO-8859-1, UCS2, UTF8");

		if ("ASCII" === $encoding)
		{
			$result = mb_convert_encoding($str, languages::USER_CHARSET, "ASCII");
		}
		elseif ("ISO-8859-1" === $encoding)
		{
			$result = mb_convert_encoding($str, languages::USER_CHARSET, "ISO-8859-1");
		}
		else
		{
			if (substr_count($str, chr(0)) > 0)
			{
				$result = mb_convert_encoding($str, languages::USER_CHARSET, "UCS2");
			}
			else
			{
				$result = $str;
			}
		}

		return $result;
	}

	/**
		@comment
			Returns certificate info as an array in ISO-8859-1 charset
		@returns
			array(
				f_name => firstname,
				l_name => lastname,
				pid => personal id,
	**/
	private function returncertdata($cert)
	{
		$data = array();
		$certstructure = openssl_x509_parse($cert);

		if (strpos($_SERVER["SSL_VERSION_LIBRARY"], "0.9.6") === false)
		{
			$data['f_name'] = $certstructure["subject"]["GN"];
			$data['l_name'] = $certstructure["subject"]["SN"];
			$data['pid'] = $certstructure["subject"]["serialNumber"];
		}
		else
		{
			$data['f_name'] = $certstructure["subject"]["SN"];
			$data['l_name'] = $certstructure["subject"]["G"];
			$data['pid'] = $certstructure["subject"]["S"];
		}
		return $data;
	}

	private function _init_group_settings($group_oid, $inherit = true)
	{
		$o = obj($group_oid);
		$admin_rootmenu = $this->_get_admin_rootmenu_from_group($o, $inherit);
		if ($admin_rootmenu !== null)
		{
			aw_ini_set("admin_rootmenu2",$admin_rootmenu);
			$inrm = aw_ini_get("ini_rootmenu");
			if (!$inrm)
			{
				$inrm = aw_ini_get("rootmenu");
			}
			aw_ini_set("ini_rootmenu", $inrm);
			aw_ini_set("rootmenu", is_array($admin_rootmenu) ? reset($admin_rootmenu) : $admin_rootmenu);
		}

		$lang_id = aw_global_get("lang_id");
		$gf = $o->meta("grp_frontpage");
		if (is_array($gf) && !empty($gf[$lang_id]))
		{
			aw_ini_set("frontpage",$gf[$lang_id]);
		}
	}

	private function _get_admin_rootmenu_from_group($group, $inherit = true)
	{
		$lang_id = aw_global_get("lang_id");

		if ($inherit)
		{
			$items = $group->path();
		}
		else
		{
			$items = array($group);
		}

		foreach(array_reverse($items) as $o)
		{
			$ar2 = $o->meta("admin_rootmenu2");
			if (is_array($ar2) && !empty($ar2[$lang_id]) && ($o->prop("inherit_rm") || $o->id() == $group->id()))
			{
				return $ar2[$lang_id];
			}
		}
		return null;
	}

	public static function get_oid_for_gid($gid)
	{
		$ol = new object_list(array(
			"class_id" => CL_GROUP,
			"gid" => $gid,
			"limit" => 1
		));
		if($ol->count())
		{
			$ids = $ol->ids();
			return reset($ids);
		}
		else
		{
			return false;
		}
	}
}
