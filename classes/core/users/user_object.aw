<?php
/*
@classinfo  maintainer=kristo
*/

class user_object extends _int_object
{
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

	public function awobj_set_uid($value)
	{
		if ("root" === $value)
		{
			throw new awex_obj("The name 'root' is reserved");
		}

		return parent::set_prop("uid", $value);
	}

	public function save($exclusive = false, $previous_state = null)
	{
		$new = !is_oid($this->id());
		$rv = parent::save($exclusive, $previous_state);

		if ($new)
		{
			$this->_handle_user_create();
		}
		return $rv;
	}

	private function _handle_user_create()
	{
		// create home folder
		$o = obj();
		$o->set_class_id(CL_MENU);
		$o->set_parent(1);
		$o->set_name($this->prop("uid"));
		$o->set_comment(sprintf(t("%s kodukataloog"), $this->prop("uid")));
		$o->set_prop("type", MN_HOME_FOLDER);
		$hfid = $o->save();
		$this->set_prop("home_folder", $hfid);
		$this->save();

		// create default group
		// in the bloody eau database the object with oid 1 is the groups folder. bloody hell.
		// this really needs a better solution :(
		$gid = obj(get_instance(CL_GROUP)->add_group((aw_ini_get("site_id") == 65 ? 5 : 1), $this->prop("uid"), group_obj::TYPE_DEFAULT, USER_GROUP_PRIORITY));

		$i = new menu();

		// give all access to the home folder for this user
		$i->create_obj_access($hfid,$this->prop("uid"));
		// and remove all access from everyone else
		$i->deny_obj_access($hfid);
		// user has all access to itself
		$i->create_obj_access($this->id(),$this->prop("uid"));
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
	function get_groups_for_user()
	{
		$ol = get_instance(CL_USER)->get_groups_for_user(parent::prop("uid"));
		$rv = $ol->arr();
		// now, the user's own group is not in this list probably, so we go get that as well
		$ol = new object_list(array(
			"class_id" => array(CL_GROUP, CL_USER_GROUP),
			"name" => $this->name(),
			"lang_id" => array(),
			"site_id" => array(),
			"type" => group_obj::TYPE_DEFAULT
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
			"class_id" => array(CL_GROUP, CL_USER_GROUP),
			"name" => $this->name(),
			"lang_id" => array(),
			"site_id" => array(),
			"type" => group_obj::TYPE_DEFAULT
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
	function generate_password($lenght = 8, $use_symbols = false)
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

	function create_brother($p)
	{
		$rv = parent::create_brother($p);
		if(obj($p)->class_id() == CL_GROUP)
		{
			// If you save user under group, the user must be added into that group!
			get_instance(CL_GROUP)->add_user_to_group(obj(parent::id()), obj($p), array("brother_done" => true));
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
		if($this->prop("phone"))
		{
			return $this->prop("phone");
		}
		$person = obj($this->get_person_for_user());
		return $person->get_phone();
	}

	function awobj_set_cfg_admin_mode($v)
	{
		$_SESSION["cfg_admin_mode"] = $v;
		return $this->set_prop("cfg_admin_mode", $v);
	}

	/** returns lower users
		@attrib api=1
		@returns object list
	**/
	public function get_slaves()
	{
		$ol = new object_list(array(
			"class_id" => CL_USER,
			"lang_id" => array(),
			"parent" => $this->id(),
			"name" => $this->name().".%",
		));
		return $ol;
	}

	/** returns next slave name
		@attrib api=1
		@returns string
	**/
	public function get_new_slave_name()
	{
		$uid = aw_global_get("uid");
		$n = 1;
		while($n < 10000)
		{
			$ol = new object_list(array(
				"class_id" => CL_USER,
				"lang_id" => array(),
				"name" => aw_global_get("uid").".".$n,
			));
			if(!$ol->count())
			{
				return aw_global_get("uid").".".$n;
			}
			$n++;
		}
		return $uid.".0";
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
			if ($p_o->class_id() != CL_GROUP)
			{
				continue;
			}

			if(aw_ini_get("users.use_group_membership") == 1)
			{
				// I can't see why we need two membership objects with EXACTLY the same attributes.
				$ol_args = array(
					"class_id" => CL_GROUP_MEMBERSHIP,
					"status" => array(),	// If it's inactive, we'll activate it! ;)
					"lang_id" => array(),	// The lang_id is never checked for these anyway.
					"site_id" => array(),	// The site_id is never checked for these anyway.
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
			if(!isset($arr["brother_done"]) || !$arr["brother_done"])
			{
				$brother_id = $this->create_brother($p_o->id());
			}
		}
		$c = get_instance("cache");
		$c->file_clear_pt("acl");
	}


	/**
		@attrib params=pos api=1
		@returns
		Person object id
	**/
	public function get_person_for_user()
	{
		aw_disable_acl();
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

			aw_disable_acl();
			$p->save();

			if ($uid == aw_global_get("uid"))
			{
				// set acl to the given user
				$p->acl_set(
					obj($this->get_default_group()),
					array("can_edit" => 1, "can_add" => 1, "can_view" => 1, "can_delete" => 1)
				);
			}

			aw_restore_acl();
			// now, connect user to person
			$this->connect(array(
				"to" => $p->id(),
				"reltype" => 2
			));
			aw_restore_acl();
			return $p->id();
		}
		else
		{
			aw_restore_acl();
			if (aw_global_get("uid") == $this->prop("uid") && !$GLOBALS["object_loader"]->cache->can("edit", $person_c->prop("to")))
			{
				aw_disable_acl();
				$p = obj($person_c->prop("to"));
				$p->acl_set(
					obj($this->get_default_group()),
					array("can_edit" => 1, "can_add" => 1, "can_view" => 1, "can_delete" => 1)
				);
				aw_restore_acl();
			}
			return $person_c->prop("to");
		}
	}
}

?>
