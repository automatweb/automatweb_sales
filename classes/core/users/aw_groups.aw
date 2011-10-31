<?php

class aw_groups extends aw_core_module
{
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

	public static function add(object $group)
	{
	}

	public static function delete(object $group)
	{
	}

	/** Finds/creates special system group for not logged in users in current site
		@attrib api=1 params=pos
		@param create type=bool default=TRUE
		@comment
		@returns CL_GROUP|NULL
		@errors
			throws awex_obj_invalid_count if more than one active not logged in users' group found
	**/
	public static function get_not_logged_in_group($create = true, $site_id = 0)
	{
		static $group;
		if (null === $group)
		{
			//TODO: group.aw-s oli sessioonis ka nlg_oid, v6ibolla saidi kiiruse t6stmiseks. m6elda.
			$groups = new object_list(array(
				"class_id" => group_obj::CLID,
				"site_id" => aw_ini_get("site_id"),
				"type" => aw_groups::TYPE_NOT_LOGGED_IN,
				new obj_predicate_limit(2) //XXX: seda pole vaja mujal kui intranetis, kus nliug-e on kolm tuhat, see on nende laadimise v2ltimiseks
			));

			if (1 === $groups->count())
			{
				$group = $groups->begin();
			}
			elseif ($groups->count() > 1)
			{
				throw new awex_obj_invalid_count(sprintf("Excessive system groups of type TYPE_NOT_LOGGED_IN (ids: %s).", implode(", ", $groups->ids())));
			}
			else
			{
				// look for legacy configuration nliug
				$c = new config();
				$nlg_oid = $c->get_simple_config("non_logged_in_users_group_oid");

				try
				{
					$group = obj($nlg_oid, array(), group_obj::CLID);
					$group->set_prop("type", aw_groups::TYPE_NOT_LOGGED_IN);
					$group->save();
				}
				catch (Exception $e)
				{
				}

				if (!$group and $create)
				{
					$group = obj(null, array(), group_obj::CLID);
					$group->set_prop("type", aw_groups::TYPE_NOT_LOGGED_IN);
					$group->save();
				}
			}
		}

		return $group;
	}

	/** Finds/creates all users' system group
		@attrib api=1 params=pos
		@param create type=bool default=TRUE
		@comment
		@returns CL_GROUP
		@errors
			throws awex_obj_invalid_count if more than one active all users' group found
			throws awex_users_cfg if all users' groups found but none of them are defined active
	**/
	public static function get_all_users_group($create = true, $site_id = 0)
	{
		static $group;
		if ($group === null)
		{
			$groups = new object_list(array(
				"class_id" => group_obj::CLID,
				"site_id" => aw_ini_get("site_id"),
				"type" => aw_groups::TYPE_ALL_USERS
			));

			if (1 === $groups->count())
			{
				$group = $groups->begin();
			}
			elseif (1 < $groups->count())
			{
				// shouldn't occur at all but jic
				throw new awex_obj_invalid_count(sprintf("Excessive active system groups of type TYPE_ALL_USERS in site %s with gid %s", aw_ini_get("site_id"), aw_ini_get("groups.all_users_grp")));
			}
			else
			{
				// look for legacy groups that have no all_users type specified
				$groups = new object_list(array(
					"class_id" => group_obj::CLID,
					"site_id" => aw_ini_get("site_id"),
					"gid" => aw_ini_get("groups.all_users_grp")
				));

				if (1 === $groups->count())
				{
					$group = $groups->begin();
					$group->set_prop("type", aw_groups::TYPE_ALL_USERS);
					$group->save();
				}
				elseif (1 < $groups->count())
				{
					// shouldn't occur but jic
					throw new awex_obj_invalid_count(sprintf("Excessive active system groups of type TYPE_ALL_USERS in site %s with gid %s", aw_ini_get("site_id"), aw_ini_get("groups.all_users_grp")));
				}
				elseif ($create)
				{
					$group = obj(null, array(), group_obj::CLID);
					$group->set_prop("type", aw_groups::TYPE_ALL_USERS);
					$group->save();
				}
			}
		}

		return $group;
	}
}
