<?php
/*
@classinfo  maintainer=kristo
*/

class group_obj extends _int_object
{
	const AW_CLID = 37;

	// group types:
	// 0 - ordinary, user added group
	// 1 - user's default group
	// 2 - dynamic group
	const TYPE_REGULAR = 0;
	const TYPE_DEFAULT = 1;
	const TYPE_DYNAMIC = 2;

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
		if ($k == "name" || $k == "gp_name")
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

	function get_member_count()
	{
		if (!is_oid($this->id()))
		{
			return 0;
		}
		$ol = $this->get_group_members();
		return $ol->count();
	}

	function get_group_persons()
	{
		$persons = new object_list();
		$user_inst = get_instance(user_obj::AW_CLID);

		foreach(get_instance(self::AW_CLID)->get_group_members($this) as $o)
		{
			$persons->add($user_inst->get_person_for_user($o));
		}
		return $persons;
	}

	public function get_group_members()
	{
		if(aw_ini_get("users.use_group_membership") == 1)
		{
			$ol = new object_list(array(
				"class_id" => CL_USER,
				"lang_id" => array(),
				"site_id" => array(),
				"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).RELTYPE_GROUP" => $this->id(),
				"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).status" => object::STAT_ACTIVE,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).membership_forever" => 1,
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).date_start" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, time()),
								"CL_USER.RELTYPE_USER(CL_GROUP_MEMBERSHIP).date_end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
							),
						)),
					),
				)),
			));
		}
		else
		{
			$ol = new object_list(array(
				"class_id" => CL_USER,
				"parent" => $this->id(),
				"lang_id" => array(),
				"site_id" => array()
			));
		}

		return $ol;
	}
}

?>
