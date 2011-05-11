<?php

class group_obj extends _int_object
{
	const CLID = 37;

	/** Ordinary, user added group **/
	const TYPE_REGULAR = 0;

	/** User's default group **/
	const TYPE_DEFAULT = 1;

	/** Dynamic group **/
	const TYPE_DYNAMIC = 2;

	/** Not logged in users' group. Special system group. Singular in site scope **/
	const TYPE_NOT_LOGGED_IN = 3;

	/** Group where all users are members always. Special system group. More than one may exist which means all users are added to all of them **/
	const TYPE_ALL_USERS = 4;

	function name()
	{
		$rv =  parent::name();
		if ($rv == "")
		{
			$rv = parent::prop("gp_name");
		}
		return $rv;
	}

	function set_prop($k, $v)
	{
		if ($k === "name" || $k === "gp_name")
		{
			$this->set_name($v);
		}
		return parent::set_prop($k, $v);
	}

	function set_name($v)
	{
		parent::set_prop("name", $v);
		parent::set_prop("gp_name", $v);
		return parent::set_name($v);
	}

	/** Returns member count
		@attrib api=1 params=pos
		@comment
		@returns int
		@errors
	**/
	public function get_member_count()
	{
		if (!is_oid($this->id()))
		{
			return 0;
		}
		$ol = $this->get_group_members();
		return $ol->count();
	}

	/** Returns objlist of persons in this group
		@attrib api=1 params=pos
		@comment
		@returns object_list
		@errors
	**/
	public function get_group_persons()
	{
		$persons = new object_list();
		$user_inst = get_instance(user_obj::CLID);

		foreach(get_instance(self::CLID)->get_group_members($this) as $o)
		{
			$persons->add($user_inst->get_person_for_user($o));
		}
		return $persons;
	}

	/** Members in this group
		@attrib api=1 params=pos
		@comment
		@returns object_list
		@errors
	**/
	public function get_group_members()
	{
		if(aw_ini_get("users.use_group_membership"))
		{
			$ol = new object_list(array(
				"class_id" => user_obj::CLID,
				"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).RELTYPE_GROUP" => $this->id(),
				"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).status" => object::STAT_ACTIVE,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).membership_forever" => 1,
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).date_start" => new obj_predicate_compare(obj_predicate_compare::LESS_OR_EQ, time()),
								"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).date_end" => new obj_predicate_compare(obj_predicate_compare::GREATER, time()),
							),
						)),
					),
				)),
			));
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => user_obj::CLID,
				"parent" => $this->id()
			));
		}

		return $ol;
	}

	public function save($exclusive = false, $previous_state = null)
	{
		if ($this->prop("type") == self::TYPE_NOT_LOGGED_IN)
		{ // check if the system group already exists
			$nli_group = user_manager_obj::get_not_logged_in_group(false);
			if ($nli_group and $nli_group->id() !== $this->id())
			{
				throw new awex_obj_system(sprintf("System group for not logged in users already exists (oid: %s), can't create another.", $nli_group->id()));
			}

			$parent = aw_ini_get("groups.tree_root");
			$this->set_parent($parent);
			$this->set_prop("priority", 0);
			$this->set_status(object::STAT_ACTIVE);
			$this->set_name(t("Sisselogimata kasutajad"));
			return parent::save($exclusive, $previous_state);
		}
		elseif ($this->prop("type") == self::TYPE_ALL_USERS)
		{ // check if the system group already exists
			$all_users_group = user_manager_obj::get_all_users_group(false);
			if ($all_users_group and $all_users_group->id() !== $this->id())
			{
				throw new awex_obj_system(sprintf("System group for all users already exists (oid: %s), can't create another.", $all_users_group->id()));
			}

			$parent = aw_ini_get("groups.tree_root");
			$this->set_parent($parent);
			$this->set_prop("priority", 1);
			$this->set_status(object::STAT_ACTIVE);
			$this->set_name(t("K&otilde;ik kasutajad"));
			return parent::save($exclusive, $previous_state);
		}
		else
		{
			return parent::save($exclusive, $previous_state);
		}
	}

	public function delete($full_delete = false)
	{
		// group objects can never be deleted from database, to avoid object id reuse and security issues thereof
		return parent::delete();
	}
}
