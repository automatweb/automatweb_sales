<?php

class group_membership_obj extends _int_object
{
	const CLID = 1481;

	function set_status($s)
	{
		if($s == object::STAT_ACTIVE)
		{
			// Damn double trouble! Gotta create brother.
			if(is_oid(parent::prop("gms_user")) && is_oid(parent::prop("gms_group")) && (parent::prop("membership_forever") || parent::prop("date_start") <= time() && parent::prop("date_end") > time()) && obj(parent::prop("gms_user"))->has_brother(parent::prop("gms_group")) === false)
			{
				obj(parent::prop("gms_user"))->create_brother(parent::prop("gms_group"));
			}
		}
		else
		{
			// Damn double trouble! Gotta remove brother.
			if(is_oid(parent::prop("gms_user")) && is_oid(parent::prop("gms_group")))
			{
				$ol = new object_list(array(
					"oid" => new obj_predicate_not(parent::prop("gms_user")),
					"brother_of" => parent::prop("gms_user"),
					"parent" => parent::prop("gms_group"),
					"lang_id" => array(),
					"site_id" => array(),
				));
				$ol->delete();
			}
		}
		return parent::set_status($s);
	}

	function set_prop($k, $v)
	{
		if($k == "status")
		{
			return $this->set_status($v);
		}
		if($k == "date_end" && $v <= parent::prop("date_start"))
		{
			// These two can't be the same!
			$v = parent::prop("date_start") + 60;
		}
		return parent::set_prop($k, $v);
	}

	public function save($check_state = false)
	{
		// I need the ID!
		if(!is_oid(parent::id()))
		{
			parent::save($check_state);
		}
		// If it's valid right now, it's handled in $this->set_status()
		if(!parent::prop("membership_forever") && parent::prop("date_start") > time())
		{
			$event = $this->mk_my_orb("brother_creator", array("id" => parent::id()), CL_GROUP_MEMBERSHIP);
			$sche = get_instance("scheduler");
			$sche->remove(array(
				"event" => $event,
			));
			$sche->add(array(
				"event" => $event,
				"time" => parent::prop("date_start"),
			));
		}
		if(!parent::prop("membership_forever") && parent::prop("date_end") > time())
		{
			$event = parent::instance()->mk_my_orb("brother_destroyer", array("id" => parent::id()), CL_GROUP_MEMBERSHIP);
			$sche = get_instance("scheduler");
			$sche->remove(array(
				"event" => $event,
			));
			$sche->add(array(
				"event" => $event,
				"time" => parent::prop("date_end"),
			));
		}
		if(!parent::prop("membership_forever") && parent::prop("date_end") <= time() && is_oid(parent::prop("gms_user")) && is_oid(parent::prop("gms_group")))
		{
			$ol = new object_list(array(
				"oid" => new obj_predicate_not(parent::prop("gms_user")),
				"brother_of" => parent::prop("gms_user"),
				"parent" => parent::prop("gms_group"),
				"lang_id" => array(),
				"site_id" => array(),
			));
			$ol->delete();
		}
		return parent::save($check_state);
	}

	public function brother_destroyer($arr)
	{
		$id = $arr["id"];
		if(is_oid($id))
		{
			$o = obj($id);
			if(is_oid($o->gms_user) && is_oid($o->gms_group) && !$o->is_valid())
			{
				$ol = new object_list(array(
					"oid" => new obj_predicate_not($o->gms_user),
					"brother_of" => $o->gms_user,
					"parent" => $o->gms_group,
					"lang_id" => array(),
					"site_id" => array(),
				));
				$ol->delete();
			}
		}
	}

	public function brother_creator($arr)
	{
		$id = $arr["id"];
		if(is_oid($id))
		{
			$o = obj($id);
			if(is_oid($o->gms_user) && is_oid($o->gms_group) && $o->gms_valid())
			{
				obj($o->gms_user)->create_brother($o->gms_group);
			}
		}
	}

	public function gms_valid()
	{
		if($this->status != object::STAT_ACTIVE)
		{
			return false;
		}
		if(!$this->membership_forever && ($this->date_start > time() || $this->date_end <= time()))
		{
			return false;
		}
		return true;
	}

	public function on_disconnect_from_group_membership($arr)
	{
		if($arr["connection"]->prop("reltype") == RELTYPE_GROUP && $arr["connection"]->prop("to.class_id") == CL_GROUP)
		{
			// If any brothers remained laying around, DESTROY 'em!
			$o = $arr["connection"]->from();
			if(is_oid($o->gms_user))
			{
				$ol = new object_list(array(
					"oid" => new obj_predicate_not($o->gms_user),
					"brother_of" => $o->gms_user,
					"parent" => $arr["connection"]->prop("to"),
					"lang_id" => array(),
					"site_id" => array(),
				));
				$ol->delete();
				if($ol->count() > 0)
				{
					$ol->delete();
				}
			}
			// Disconnect the parallel connection
			$arr["connection"]->to()->disconnect(array(
				"from" => $arr["connection"]->prop("from"),
				"type" => "RELTYPE_MEMBERSHIP",
				"errors" => false,
			));
		}
		elseif($arr["connection"]->prop("reltype") == RELTYPE_USER && $arr["connection"]->prop("to.class_id") == CL_USER)
		{
			// If any brothers remained laying around, DESTROY 'em!
			$o = $arr["connection"]->from();
			if(is_oid($o->gms_group))
			{
				$ol = new object_list(array(
					"oid" => new obj_predicate_not($arr["connection"]->prop("to")),
					"brother_of" => $arr["connection"]->prop("to"),
					"parent" => $o->gms_group,
					"lang_id" => array(),
					"site_id" => array(),
				));
				$ol->delete();
				if($ol->count() > 0)
				{
					$ol->delete();
				}
			}
		}
	}

	function on_disconnect_to_group_membership($arr)
	{
		// reltype MEMBERSHIP value=9 clid=CL_GROUP_MEMBERSHIP
		if($arr["connection"]->prop("reltype") == 9 && $arr["connection"]->prop("to.class_id") == CL_GROUP_MEMBERSHIP)
		{
			// If any brothers remained laying around, DESTROY 'em!
			$o = $arr["connection"]->to();
			if(is_oid($o->gms_user))
			{
				$ol = new object_list(array(
					"oid" => new obj_predicate_not($o->gms_user),
					"brother_of" => $o->gms_user,
					"parent" => $arr["connection"]->prop("from"),
					"lang_id" => array(),
					"site_id" => array(),
				));
				$ol->delete();
				if($ol->count() > 0)
				{
					$ol->delete();
				}
			}
			// Disconnect the parallel connection
			$arr["connection"]->to()->disconnect(array(
				"from" => $arr["connection"]->prop("from"),
				"type" => "RELTYPE_GROUP",
				"errors" => false,
			));
		}
	}

	function on_connect_from_group_membership($arr)
	{
		if ($arr["connection"]->prop("reltype") == RELTYPE_GROUP && $arr["connection"]->prop("to.class_id") == CL_GROUP)
		{
			$arr["connection"]->to()->connect(array(
				"to" => $arr["connection"]->prop("from"),
				"type" => "RELTYPE_MEMBERSHIP",
			));
		}
		// The damn thing won't go through save() on connect.
		$this->brother_creator(array("id" => $arr["connection"]->prop("from")));
	}

	function on_connect_to_group_membership($arr)
	{
		// reltype MEMBERSHIP value=9 clid=CL_GROUP_MEMBERSHIP
		if ($arr["connection"]->prop("reltype") == 9 && $arr["connection"]->prop("to.class_id") == CL_GROUP_MEMBERSHIP)
		{
			$arr["connection"]->to()->connect(array(
				"to" => $arr["connection"]->prop("from"),
				"type" => "RELTYPE_GROUP",
			));
		}
		// The damn thing won't go through save() on connect.
		$this->brother_creator(array("id" => $arr["connection"]->prop("to")));
	}

	/** Returns the object_list of all valid memberships at the moment.

	@attrib name=get_valid_memberships params=name

	@param group optional type=array/oid

	@param user optional type=array/oid

	**/
	public static function get_valid_memberships($arr)
	{
		enter_function("group_membership_obj::get_valid_memberships");

		$group = isset($arr["group"]) ? (array) $arr["group"] : array();
		$user = isset($arr["user"]) ? (array) $arr["user"] : array();

		$r = new object_list(array(
			"class_id" => CL_GROUP_MEMBERSHIP,
			"status" => object::STAT_ACTIVE,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_GROUP_MEMBERSHIP.RELTYPE_USER" => $user,
			"CL_GROUP_MEMBERSHIP.RELTYPE_GROUP" => $group,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"membership_forever" => 1,
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"date_start" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, time()),
							"date_end" => new obj_predicate_compare(OBJ_COMP_GREATER, time()),
						),
					)),
				),
			)),
		));
		exit_function("group_membership_obj::get_valid_memberships");
		return $r;
	}
}

?>
