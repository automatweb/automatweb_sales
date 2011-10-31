<?php

class user_obj extends _int_object
{
	const CLID = 197;

	private static $uid_charset = '1234567890qwertyuiopasdfghjklzxcvbnm_QWERTYUIOPASDFGHJKLZXCVBNM.@-';
	private static $uid_max_length = 100;
	private static $uid_min_length = 2;

	public function awobj_set_ui_language($aw_lid)
	{
		languages::set_active_ui_lang($aw_lid, true);
		return $this->set_prop("ui_language", $aw_lid);
	}

	public function awobj_get_password()
	{
		return "";
	}

	public function awobj_get_history_size()
	{
		$rv = parent::prop("history_size");
		if(!($rv > "-1"))
		{
			$rv = 25;
		}
		return $rv;
	}

	public function awobj_set_password($value)
	{
		return "";
	}

	/** Checks if given string is a valid user id string
		@attrib api=1 params=pos
		@param uid type=string
		@param info type=bool|string
			Return user readable and translated info why given string is not valid or empty string if is valid.
		@comment
		@returns bool
		@errors none
	**/
	public static function is_uid($uid, $info = false)
	{
		$r = true;

		// check lenght
		if (strlen($uid) < self::$uid_min_length or strlen($uid) > self::$uid_max_length)
		{
			$r = false;
		}

		// check characters
		if (strspn($uid, self::$uid_charset) !== strlen($uid))
		{
			$r = false;
		}

		return $r;
	}

	/**
		@attrib api=1 params=pos
		@param value type=string
			Uid.
		@comment
			This method can be called once per object only. (uid can't be reset)
		@return void
		@errors
			throws awex_obj_type when given uid doesn't meet requirements
			throws awex_user_exists
			throws awex_obj_state when trying to set user name on a user object that already has uid defined
	**/
	public function awobj_set_uid($value)
	{
		if ($this->is_saved() or $this->prop("uid"))
		{
			throw new awex_obj_state("User ".$this->prop("uid")." already has uid");
		}

		if (!self::is_uid($value))
		{
			throw new awex_obj_type("Invalid user id value");
		}

		if (strtolower(object_loader::instance()->ds->db_fetch_field("SELECT uid FROM users WHERE uid = '{$value}'", "uid")) === strtolower($value))
		{
			throw new awex_user_exists("User {$value} exists");
		}

		parent::set_prop("uid", $value);
	}

	public function save($check_state = false)
	{
		if (!$this->prop("uid"))
		{
			throw new awex_obj_prop("Can't save user without uid");
		}

		if ("root" === $this->prop("uid") and "root" !== aw_global_get("uid"))
		{
			throw new awex_obj_acl("Access denied");
		}

		$new = !$this->is_saved();

		$person_oid = $this->meta("person");
		$this->set_meta("person", "");

		$rv = parent::save($check_state);

		if ($new)
		{
			$this->_handle_user_create();
		}

		if ($person_oid)
		{
			$this->connect(array(
				"to" => $person_oid,
				"reltype" => "RELTYPE_PERSON"
			));
		}

		// create or update email object
		$umail = $this->prop("email");
		$uname = $this->prop("real_name");

		if ($umail)
		{
			if($mail = $this->get_first_obj_by_reltype("RELTYPE_EMAIL"))
			{
				if (!$mail->is_a(CL_ML_MEMBER)) //XXX: connection error fixing for unknown reasons
				{
					$this->disconnect(array("from" => $mail->id(), "type" => "RELTYPE_EMAIL"));
					$mail = false;
				}
				elseif ($mail->prop("mail") !== $umail or $mail->name() !== $uname)
				{
					$mail->set_prop("mail", $umail);
					$mail->set_name($uname." &lt;".$umail."&gt;");
					$mail->save();
				}
			}

			if (!$mail)
			{
				$mail = new object();
				$mail->set_class_id(CL_ML_MEMBER);
				$p = obj($this->get_person_for_user($o));

				$mail->set_parent($p->id());
				$mail->set_prop("mail", $umail);
				$mail->set_prop("name", $uname);
				$mail->set_name($uname." &lt;".$umail."&gt;");//FIXME: html entiteid pole m6tet andmebaasi salvestada
				$mail->save();
				$this->connect(array(
					"to" => $mail->id(),
					"reltype" => "RELTYPE_EMAIL"
				));
			}
		}

		return $rv;
	}

	private function _handle_user_create()
	{
		if ("root" === $this->prop("uid"))
		{
			$parent = 1;
			$comment = "root home";
		}
		else
		{
			$parent = aw_ini_get("users.home_folders_parent");
			$comment = sprintf(t("%s kodukataloog"), $this->prop("uid"));
		}

		// create home folder
		$o = obj(null, array(), menu_obj::CLID);
		$o->set_parent($parent);
		$o->set_name($this->prop("uid"));
		$o->set_comment($comment);
		$o->set_prop("type", menu_obj::TYPE_HOME_FOLDER);
		$hfid = $o->save();
		$this->set_prop("home_folder", $hfid);
		$this->save();

		// create default group
		$gid = obj(get_instance(group_obj::CLID)->add_group(1, $this->prop("uid"), aw_groups::TYPE_DEFAULT, USER_GROUP_PRIORITY));

		if ("root" !== $this->prop("uid"))
		{
			$i = new menu();

			// give all access to the home folder for this user
			$i->create_obj_access($hfid,$this->prop("uid"));
			// and remove all access from everyone else
			$i->deny_obj_access($hfid);
			// user has all access to itself
			$i->create_obj_access($this->id(),$this->prop("uid"));
		}
	}

	/** sets the user's password
		@attrib api=1 params=pos

		@param pwd required type=string
			The password to set

		@comment
			you can't set the users password via set_prop for security reasons, you must use this method. you will need to save the object after calling this as well
	**/
	function set_password($pwd)
	{
		if ("root" === $this->prop("uid"))
		{
			throw new awex_obj_acl("Access denied");
		}

		if (aw_ini_get("auth.md5_passwords"))
		{
			$pwd = md5($pwd);
		}
		parent::set_prop("password", $pwd);
	}

	/** returns an array of group objects of whom this user is a member of
		@attrib api=1

		@returns
			array { group_oid => group_obj, ... } for all groups that this user is a member of
	**/
	public function get_groups_for_user()
	{
		$ol = get_instance(self::CLID)->get_groups_for_user(parent::prop("uid"));
		$rv = $ol->arr();
		// now, the user's own group is not in this list probably, so we go get that as well
		$ol = new object_list(array(
			"class_id" => group_obj::CLID,
			"name" => $this->name(),
			"type" => aw_groups::TYPE_DEFAULT
		));
		if ($ol->count())
		{
			$mg = $ol->begin();
			$rv[$mg->id()] = $mg;
		}
		uasort($rv, array($this, "_pri_sort"));
		return $rv;
	}

	/** returns the user's default group oid
		@attrib api=1

		@returns
			oid of the user's default group or null if it not found.
	**/
	function get_default_group()
	{
		static $cache;
		if ($cache)
		{
			return $cache;
		}
		// now, the user's own group is not in this list probably, so we go get that as well
		$ol = new object_list(array(
			"class_id" => group_obj::CLID,
			"name" => $this->name(),
			"type" => aw_groups::TYPE_DEFAULT
		));
		if ($ol->count())
		{
			$mg = $ol->begin();
			$cache = $mg->id();
			return $mg->id();
		}
		return null;
	}

	private function _pri_sort($a, $b)
	{
		return $b->prop("priority") - $a->prop("priority");
	}

	/**
	@attrib name=generate_password params=pos api=1

	@param lenght optional type=int
		Default value 8.

	@param use_symbols default=false type=bool
		Use also non alphanumeric characters

	**/
	public static function generate_password($lenght = 8, $use_symbols = false)
	{
		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxy";

		if ($use_symbols)
		{
			$chars .= "(){}[]|!@#\$%^&*_=-+,.:;/<>?";
		}

		$p = "";
		$max = strlen($chars) - 1;
		for($i = 0; $i < $lenght; $i++)
		{
			$rand = mt_rand(0, $max);
			$p .= $chars[$rand];
		}
		return $p;
	}

	public function create_brother($p)
	{
		/* XXX : tekitas probleemi, meenutada, milleks yldse vaja oli
		if ("root" === $this->prop("uid"))
		{ //TODO: vaadata kas see ei tekita kuskil probleemi.
			throw new awex_obj_acl("Access denied");
		}
		*/

		$rv = parent::create_brother($p);
		if(obj($p)->class_id() == group_obj::CLID)
		{
			// If you save user under group, the user must be added into that group!
			get_instance(group_obj::CLID)->add_user_to_group(obj(parent::id()), obj($p), array("brother_done" => true));
		}
		return $rv;
	}

	public function is_group_member($user, $group)
	{
		$user = is_object($user) ? $user : (is_oid($user) ? obj($user) : $this);
		$group = is_object($group) ? array($group->id()) : (array) $group;

		$grps = $user->get_groups_for_user();
		return count(array_intersect($group, array_keys($grps))) > 0;
	}

	/**
	@attrib name=get_user_name api=1
	@returns string
	**/
	public function get_user_name()
	{
		if($this->prop("real_name"))
		{
			return $this->prop("real_name");
		}
		$person_c = $this->connections_from(array(
			"type" => "RELTYPE_PERSON",
		));
		foreach($person_c as $p_c)
		{
			$person = $p_c->to();
			if($person->name())
			{
				return $person->name();
			}
		}
		$u = new user();
		$p = obj($u->get_current_person());
		return $p->name();
	}

	/**
	@attrib name=get_user_mail_address api=1
	@returns string
	**/
	public function get_user_mail_address()
	{
		if($this->prop("email"))
		{
			return $this->prop("email");
		}
		$p = obj($this->get_person_for_user());
		return $p->get_mail();
	}

	/**
	@attrib api=1
	@returns string
	**/
	public function get_phone()
	{
		$person = obj($this->get_person_for_user());
		return $person->get_phone();
	}

	public function awobj_set_cfg_admin_mode($v)
	{
		$_SESSION["cfg_admin_mode"] = $v;
		return $this->set_prop("cfg_admin_mode", $v);
	}

	/** adds the user $user to group $group (storage objects)
		@attrib params=pos api=1
		@param group required type=object
			Group object to what the user will be added
		@param args optional type=array
			Array of arguments (start, end, brother_done)
		@comment
		Adds the user to the $group.
	**/
	public function add_to_group($group, $arr = array())
	{
		// for each group in path from the to-add group
		foreach($group->path() as $p_o)
		{
			if ($p_o->class_id() != group_obj::CLID)
			{
				continue;
			}

			if(aw_ini_get("users.use_group_membership") == 1)
			{
				// I can't see why we need two membership objects with EXACTLY the same attributes.
				$ol_args = array(
					"class_id" => CL_GROUP_MEMBERSHIP,
					"status" => array(),	// If it's inactive, we'll activate it! ;)
					"parent" => array(),	// The parent doesn't make a difference here.
					"gms_user" => $this->id(),
					"gms_group" => $group->id(),
				);
				if(isset($arr["start"]) && isset($arr["end"]))
				{
					$ol_args["date_start"] = $arr["start"];
					$ol_args["date_end"] = $arr["end"];
				}
				else
				{
					$ol_args["membership_forever"] = 1;
				}
				$ol = new object_list($ol_args);
				if($ol->count() > 0)
				{
					$gms = $ol->begin();
					$gms->set_status(object::STAT_ACTIVE);
					$gms->save();
				}
				else
				{
					$gms = obj();
					$gms->set_class_id(CL_GROUP_MEMBERSHIP);
					$gms->set_parent($this->id());
					$gms->set_name(sprintf(t("%s kuulub gruppi %s"), $this->uid, $group->name));
					$gms->set_status(object::STAT_ACTIVE);
					$gms->gms_user = $this->id();
					$gms->gms_group = $group->id();
					if(isset($arr["start"]) && isset($arr["end"]))
					{
						$gms->date_start = $arr["start"];
						$gms->date_end = $arr["end"];
					}
					else
					{
						$gms->membership_forever = 1;
					}
					$gms->save();
				}
				$arr["brother_done"] = true;
			}
			else
			{
				// connection from user to group
				$this->connect(array(
					"to" => $p_o->id(),
					"reltype" => "RELTYPE_GRP",
				));

				// connection to group from user
				$p_o->connect(array(
					"to" => $this->id(),
					"reltype" => "RELTYPE_MEMBER",
				));
			}

			// brother under group
			if(empty($arr["brother_done"]))
			{
				$brother_id = $this->create_brother($p_o->id());
			}
		}

		cache::file_clear_pt("acl");
	}


	/**
		@attrib params=pos api=1
		@returns
		Person object id
	**/
	public function get_person_for_user()
	{
		$person_c = $this->connections_from(array(
			"type" => "RELTYPE_PERSON",
		));

		$person_c = reset($person_c);
		if (!$person_c)
		{
			// create new person next to user
			$p = obj();
			$p->set_class_id(CL_CRM_PERSON);
			$p->set_parent($this->id());

			$rn = $this->prop("real_name");

			$uid = $this->prop("uid");

			$p_n = ($rn != "" ? $rn : $uid);
			$p->set_name($p_n);

			if ($rn != "")
			{
				list($fn, $ln) = explode(" ", $rn);
			}
			else
			{
				list($fn, $ln) = explode(".", $uid);
			}

			$p->set_prop("firstname", $fn);
			$p->set_prop("lastname", $ln);
			$p->save();

			if ($uid == aw_global_get("uid"))
			{
				// set acl to the given user
				$p->acl_set(
					obj($this->get_default_group()),
					array("can_edit" => 1, "can_add" => 1, "can_view" => 1, "can_delete" => 1)
				);
			}

			// now, connect user to person
			$this->connect(array(
				"to" => $p->id(),
				"reltype" => 2
			));
			return $p->id();
		}
		else
		{
			if (aw_global_get("uid") === $this->prop("uid") && !object_loader::can("edit", $person_c->prop("to")))
			{
				$p = obj($person_c->prop("to"));
				$p->acl_set(
					obj($this->get_default_group()),
					array("can_edit" => 1, "can_add" => 1, "can_view" => 1, "can_delete" => 1)
				);
			}
			return $person_c->prop("to");
		}
	}

	public function delete($full_delete = false)
	{
		// user objects can never be deleted from database, to avoid object id reuse and security issues thereof
		return parent::delete();
	}
}

/** Generic user exception **/
class awex_user extends awex_obj {}

/** User already exists **/
class awex_user_exists extends awex_user {}
