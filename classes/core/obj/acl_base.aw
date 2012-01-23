<?php

/*
HANDLE_MESSAGE(MSG_USER_LOGIN, on_user_login)
*/

// call static constructor
acl_base::construct();

class acl_base extends aw_core_module
{
	protected static $acl_ids = array();

	private static $__aw_acl_cache = array();

	public static function construct()
	{
		self::$acl_ids = aw_ini_get("acl.ids");
		parent::construct();
	}

	public static function sql_unpack_string()
	{
		// oi kakaja huinja, bljat.
		// the point is, that php can only handle 32-bit integers, but mysql can handle 64-bit integers
		// and so, we do the packing/unpacking to integer in the database. whoop-e
		// of course, now that we only have 5 acl settings, we don't have to do this in the db no more.
		// anyone wanna rewrite it? ;) - terryf
		$s = '';
		$qstr = array();

		if(strtolower(aw_ini_get('db.driver') === 'mssql'))
		{
			reset(self::$acl_ids);
			while (list($bitpos, $name) = each(self::$acl_ids))
			{
				$qstr[] = " ( cast ( (acl / ".pow(2,$bitpos).") as int ) & 3) AS {$name}";
			}
		}
		else
		{
			reset(self::$acl_ids);
			while (list($bitpos, $name) = each(self::$acl_ids))
			{
				$qstr[] = " ((acl >> {$bitpos}) & 3) AS {$name}";
			}
		}
		$s =  join(",",$qstr);
		return $s;
	}

	public static function get_acl_groups_for_obj($oid)
	{
		if (aw_ini_get("acl.use_new_acl"))
		{
			$ret = safe_array(aw_unserialize(object_loader::ds()->db_fetch_field("SELECT acldata FROM objects WHERE oid = '{$oid}'", "acldata"), false, true));
		}
		else
		{
			$ret = array();
			$acls = aw_ini_get("acl.names");
			$q = "SELECT *,groups.name as name,".self::sql_unpack_string()."
						FROM acl LEFT JOIN groups ON groups.gid = acl.gid
						WHERE acl.oid = $oid";
			object_loader::ds()->db_query($q);
			while ($row = object_loader::ds()->db_next())
			{
				//$ret[$row["gid"]] = $row;
				$inf = array();
				foreach($acls as $id => $nm)
				{
					$inf[$id] = $row[$id];
				}
				$ret[$row["oid"]] = $inf;
			}
		}

		return $ret;
	}

	public static function add_acl_group_to_obj($gid,$oid,$aclarr = array(), $invd = true)
	{
		if ($gid < 1 || !is_numeric($gid))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("acl_base::add_acl_group_to_obj(%s, %s,..): the given gid is incorrect"), $gid, $oid)
			));
		}

		if (!is_oid($oid))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("acl_base::add_acl_group_to_obj(%s, %s,..): the given oid is incorrect"), $gid, $oid)
			));
		}

		if (!object_loader::ds()->db_fetch_field("SELECT gid FROM acl WHERE gid = '$gid' AND oid = '$oid'", "gid"))
		{
			object_loader::ds()->db_query("insert into acl(gid,oid) values($gid,$oid)");
		}

		if (sizeof($aclarr) == 0)
		{
			// set default acl if not specified otherwise
			$aclarr = $GLOBALS["cfg"]["acl"]["default"];
		}

		self::save_acl($oid,$gid,$aclarr, $invd);

		if ($invd)
		{
			aw_session_set("__acl_cache", array());
			cache::file_clear_pt("acl");
			cache::file_clear_pt_oid_fn("storage_object_data", $oid, "objdata-".$oid);
		}
	}

	private static function add_acl_group_to_new_obj($g_oid,$oid, array $aclarr)
	{
		if ($g_oid < 1 || !is_numeric($g_oid))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("acl_base::add_acl_group_to_new_obj(%s, %s,..): the given gid is incorrect"), $g_oid, $oid)
			));
		}
		if (!is_oid($oid))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("acl_base::add_acl_group_to_new_obj(%s, %s,..): the given oid is incorrect"), $g_oid, $oid)
			));
		}
		$acl = self::get_acl_value($aclarr);
		$go = obj($g_oid);
		$gid = $go->prop("gid");
		object_loader::ds()->db_query("INSERT INTO acl(acl,oid,gid) VALUES($acl,$oid,$gid)");

		if (aw_ini_get("acl.use_new_acl"))
		{
			$ad = safe_array(aw_unserialize(object_loader::ds()->db_fetch_field("SELECT acldata FROM objects WHERE oid = '$oid'", "acldata")), false, true);
			// convert gid to oid
			$g_oid = $go->id();
			$ad[$g_oid] = self::get_acl_value_n($aclarr);
			$ser = aw_serialize($ad, SERIALIZE_NATIVE);
			object_loader::ds()->quote($ser);
			object_loader::ds()->db_query("UPDATE objects SET acldata = '$ser' WHERE oid = $oid");
		}
	}

	public static function remove_acl_group_from_obj($g_obj, $oid)
	{
		if (!is_oid($oid))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("acl_base::remove_acl_group_from_obj(%s, %s,..): the given oid is incorrect"), $g_obj->id(), $oid)
			));
		}
		$gid = $g_obj->prop("gid");
		object_loader::ds()->db_query("DELETE FROM acl WHERE gid = {$gid} AND oid = {$oid}");

		if (aw_ini_get("acl.use_new_acl"))
		{
			$ad = safe_array(aw_unserialize(object_loader::ds()->db_fetch_field("SELECT acldata FROM objects WHERE oid = '{$oid}'", "acldata"), false, true));
			unset($ad[$g_obj->id()]);
			$ser = aw_serialize($ad, SERIALIZE_NATIVE);
			object_loader::ds()->quote($ser);
			object_loader::ds()->db_query("UPDATE objects SET acldata = '{$ser}' WHERE oid = {$oid}");
		}

		aw_session_set("__acl_cache", array());
		cache::file_clear_pt("acl");
		cache::file_clear_pt_oid_fn("storage_object_data", $oid, "objdata-".$oid);
	}

	public static function save_acl($oid, $gid, $aclarr, $invd = true)
	{
		if ($gid < 1 || !is_numeric($gid))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("acl_base::save_acl(%s, %s,..): the given gid is incorrect"), $gid, $oid)
			));
		}
		if (!is_oid($oid))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("acl_base::save_acl(%s, %s,..): the given oid is incorrect"), $gid, $oid)
			));
		}
		$acl = self::get_acl_value($aclarr);
		object_loader::ds()->db_query("UPDATE acl SET acl = $acl WHERE oid = $oid AND gid = $gid");

		if (aw_ini_get("acl.use_new_acl"))
		{
			$ad = safe_array(aw_unserialize(object_loader::ds()->db_fetch_field("SELECT acldata FROM objects WHERE oid = '$oid'", "acldata"), false, true));
			// convert gid to oid
			$g_oid = object_loader::ds()->db_fetch_field("SELECT oid FROM groups WHERE gid = '$gid'", "oid");
			$ad[$g_oid] = self::get_acl_value_n($aclarr);
			$ser = aw_serialize($ad, SERIALIZE_NATIVE);
			object_loader::ds()->quote($ser);
			object_loader::ds()->db_query("UPDATE objects SET acldata = '$ser' WHERE oid = $oid");
			// If we change the ACL we have to change it in the cache also! -kaarel 28.11.2008
			$GLOBALS["__obj_sys_acl_memc"][$oid]["acldata"][$g_oid] = $ad[$g_oid];
		}

		if ($invd)
		{
			aw_session_set("__acl_cache", array());
			cache::file_clear_pt("acl");
			cache::file_clear_pt_oid_fn("storage_object_data", $oid, "objdata-".$oid);
		}
	}

	public static function get_acl_for_oid_gid($oid,$gid)
	{
		if (!$oid || !$gid)
		{
			return;
		}
		$q = "SELECT
						*,
						acl.id as acl_rel_id,
						objects.parent as parent,
						".self::sql_unpack_string().",
						groups.priority as priority,
						acl.oid as oid
					FROM acl
						LEFT JOIN groups ON groups.gid = acl.gid
						LEFT JOIN objects ON objects.oid = acl.oid
					WHERE acl.oid = '$oid' AND acl.gid = '$gid'
				";
		object_loader::ds()->db_query($q);
		$row = object_loader::ds()->db_next();
		return $row;
	}

	// black magic follows
	/** Tells if user can perform operation on object
		@attrib api=1 params=pos
		@param operation_id type=string default=""
			Options are acl ids plus "". Empty string doesn't mean an acl operation id but a parameter option
			interpreted as request to only know if object exists and isn't deleted
		@param object_id type=oid
		@param user_oid type=oid default=NULL
			Defaults to current user if not specified
		@comment
		@returns bool
		@errors none
	**/
	public static function can($operation_id = "", $object_id, $user_oid = null)
	{
		//TODO: teostada variant kui antakse root kasutaja user_oid
		if (!is_oid($object_id))
		{
			$can = false;
		}
		elseif ($operation_id)
		{
			if (aw_users::ROOT_UID === aw_global_get("uid"))
			{
				$can = self::_object_exists_and_not_deleted($object_id);
			}
			else
			{
				$operation_id = "can_{$operation_id}";

				if (!is_array(self::$acl_ids) or !in_array($operation_id, self::$acl_ids))
				{
					$can = false;
				}
				else
				{
					$can = false;
					$user_oid = $user_oid ? $user_oid : aw_global_get("uid_oid");

					if (empty(self::$__aw_acl_cache[$object_id]))
					{
						$fn = "acl-{$object_id}-uoid-{$user_oid}";
						$fn .= "-nliug-".(isset($_SESSION["nliug"]) ? $_SESSION["nliug"] : "");//TODO: mitte sessioonist

						if (!object_loader::opt("no_cache") && ($str_max_acl = cache::file_get_pt_oid("acl", $object_id, $fn)) != false)
						{
							$max_acl = aw_unserialize($str_max_acl, false, true);
						}

						if (!isset($max_acl))
						{
							$max_acl = self::_calc_max_acl($object_id);
							if (0 === $max_acl)
							{
								$max_acl = array_combine(self::$acl_ids, array_fill(0, count(self::$acl_ids), false));
							}

							if (!object_loader::opt("no_cache"))
							{
								cache::file_set_pt_oid("acl", $object_id, $fn, aw_serialize($max_acl, SERIALIZE_NATIVE));
							}
						}

						self::$__aw_acl_cache[$object_id] = $max_acl;
					}
					else
					{
						$max_acl = self::$__aw_acl_cache[$object_id];
					}

					if (!isset($max_acl["can_view"]) && !$user_oid)
					{
						$can = aw_ini_get("acl.default.{$operation_id}") === aw_ini_get("acl.allowed");
					}
					else
					{
						$can = isset($max_acl[$operation_id]) ? (bool) $max_acl[$operation_id] : false;
					}
				}
			}
		}
		elseif ("" === $operation_id or aw_users::ROOT_UID === aw_global_get("uid"))
		{ // check if object record exists and state isn't 'deleted'
			$can = self::_object_exists_and_not_deleted($object_id);
		}
		else
		{
			$can = false;
		}

		return $can;
	}

	private static function _object_exists_and_not_deleted($oid)
	{
		$does = false;
		try
		{
			$objdata = object_loader::ds()->get_objdata($oid);
			$does = !empty($objdata["oid"]);
		}
		catch (awex_obj_na $e)
		{
			//TODO: exception pole hea vahend kontrollimiseks
		}
		catch (awex_obj_acl $e)
		{
			//TODO: exception pole hea vahend kontrollimiseks
		}
		return $does;
	}

	// /** Tells if user can perform operation on object
		// @attrib api=1 params=pos
		// @param operation_id type=string
		// @param object_id type=oid
		// @param user_oid type=oid default=0
			// Defaults to current user if not specified
		// @param return type=bool default=TRUE
			// If false, throws exceptions, otherwise returns boolean
		// @comment
		// @returns bool|void
		// @errors
			// throws awex_obj_na if object doesn't exist and $return is FALSE
			// throws awex_obj_acl if no access and $return is FALSE
			// throws awex_obj_deleted if deleted and $return is FALSE
	// **/
	public function can_new($operation_id, $object_id, $user_oid = 0, $return = true) // not used (yet)
	{

		$operation_id = "can_{$operation_id}";

		if (!is_oid($object_id))
		{
			if ($return)
			{
				return false;
			}
			else
			{
				throw new awex_obj_na("Invalid object id '{$object_id}'");
			}
		}

		$uid = aw_global_get("uid");
		//TODO: teostada variant kui antakse root kasutaja user_oid
		if (!$user_oid and (aw_ini_get("acl.no_check") or "root" === $uid))
		{ // check if object record exists and state isn't 'deleted'
			$this->db_query();
			try
			{
				$objdata = object_loader::instance()->ds->get_objdata($object_id);
				return !empty($objdata["oid"]);
			}
			catch (awex_obj_na $e)
			{
				return false;
			}
			catch (awex_obj_acl $e)
			{
				return false;
			}
		}

		$can = false;
		$this->save_handle();

		$user_oid = $user_oid ? $user_oid : aw_global_get("uid_oid");

			if (empty($this->__aw_acl_cache[$object_id]))
			{
				$fn = "acl-{$object_id}-uoid-{$user_oid}";
				$fn .= "-nliug-".(isset($_SESSION["nliug"]) ? $_SESSION["nliug"] : "");//TODO: mitte sessioonist

				if (!object_loader::opt("no_cache") && ($str_max_acl = cache::file_get_pt_oid("acl", $object_id, $fn)) != false)
				{
					$max_acl = aw_unserialize($str_max_acl, false, true);
				}

				if (!isset($max_acl))
				{
					$max_acl = $this->_calc_max_acl($object_id);
					if (0 === $max_acl)
					{
						$max_acl = array_combine($this->acl_ids, array_fill(0, count($this->acl_ids), false));
					}

					if (!object_loader::opt("no_cache"))
					{
						cache::file_set_pt_oid("acl", $object_id, $fn, aw_serialize($max_acl, SERIALIZE_NATIVE));
					}
				}

				$this->__aw_acl_cache[$object_id] = $max_acl;
			}
			else
			{
				$max_acl = $this->__aw_acl_cache[$object_id];
			}

			if (!isset($max_acl["can_view"]) && !$user_oid)
			{
				$can = aw_ini_get("acl.default.{$operation_id}") === aw_ini_get("acl.allowed");
			}
			else
			{
				$can = isset($max_acl[$operation_id]) ? (bool) $max_acl[$operation_id] : false;
			}

		$this->restore_handle();
		return $can;
	}

	private static function _calc_max_acl($oid)
	{
		$max_priority = -1;
		$max_acl = $GLOBALS["cfg"]["acl"]["default"];

		$gl = aw_global_get("gidlist_pri_oid");
		// go through the object tree and find the acl that is of highest priority among the current users group
		$cur_oid = $oid;
		$do_orig = false;
		$cnt = 0;
		while ($cur_oid > 0)
		{
			$tmp = null;
			if (isset($GLOBALS["__obj_sys_acl_memc"][$cur_oid]) && isset($GLOBALS["__obj_sys_acl_memc"][$cur_oid]["acldata"]))
			{
				$tmp = $GLOBALS["__obj_sys_acl_memc"][$cur_oid];
			}
			elseif (isset($GLOBALS["__obj_sys_objd_memc"][$cur_oid]) && isset($GLOBALS["__obj_sys_objd_memc"][$cur_oid]["acldata"]))
			{
				$tmp = $GLOBALS["__obj_sys_objd_memc"][$cur_oid];
			}
			elseif (isset($GLOBALS["objects"][$cur_oid]))
			{
				$tmp = $GLOBALS["objects"][$cur_oid]->get_object_data();
			}
			else
			{
				$tmp = object_loader::instance()->ds->get_objdata($cur_oid, array( //XXX: TODO: siin peaks ilma objloaderita saama?
					"no_errors" => true
				));
				if ($tmp !== NULL)
				{
					$GLOBALS["__obj_sys_objd_memc"][$cur_oid] = $tmp;
				}
			}

			if ($tmp === NULL)
			{
				// if any object above the one asked for is deleted, no access
				return 1;
			}

			// status and brother_of are not set when acl data is read from e.g. acl mem cache
			if (isset($tmp["status"]) and 0 == $tmp["status"])
			{
				return 0;
			}

			if (isset($tmp["brother_of"]) && $cur_oid != $tmp["brother_of"] && $tmp["brother_of"] > 0 && $cur_oid == $oid)
			{
				$do_orig = $tmp["brother_of"];
			}

			$acld = isset($tmp["acldata"]) ? safe_array($tmp["acldata"]) : array();

			// now, iterate over the current acl data with the current gidlist
			// and find the highest priority acl currently
			foreach($acld as $g_oid => $g_acld)
			{
				// this applies the affects subobjects setting - if the first object has this set, then ignore the acls for that
				$skip = false; //$cur_oid == $oid && $g_acld["can_subs"] == 1;

				if (isset($gl[$g_oid]) && $gl[$g_oid] > $max_priority && !$skip)
				{
					$max_acl = $g_acld;
					$max_priority = $gl[$g_oid];
				}
			}

			if (++$cnt > 100)//TODO: move this limit setting to config?
			{
				throw new awex_obj_data_integrity(sprintf("Error in object hierarchy, count exceeded (%s, %s)", $cur_oid, $oid));
			}

			// go to parent
			$cur_oid = $tmp["parent"];
		}

		// now, we have the result. but for brothers we need to do this again for the original object and only use the can_delete privilege from the brother
		if ($do_orig !== false)
		{
			$rv = self::_calc_max_acl($do_orig);
			if ($rv === false)
			{
				return 1;   // if the original is deleted, then the brother is deleted as well
			}
			$rv["can_delete"] = isset($max_acl["can_delete"]) ? (int) $max_acl["can_delete"] : 0;
			return $rv;
		}
		return $max_acl;
	}

	public static function create_obj_access($oid, $uuid = "")
	{
		if (aw_global_get("__is_install"))
		{
			return;
		}

		if ($uuid == "")
		{
			$uuid = aw_global_get("uid");
		}

		if ($uuid != "")
		{
			reset(self::$acl_ids);
			$aclarr = array();
			while (list(,$k) = each(self::$acl_ids))
			{
				if ($k !== "can_subs")
				{
					$aclarr[$k] = aw_ini_get("acl.allowed");
				}
			}

			$gr = self::get_user_group($uuid);
			if (!$gr)
			{
				throw new aw_exception("ERR_ACL_NOGRP");
			}

			if ($gr)
			{
				self::add_acl_group_to_new_obj($gr, $oid, $aclarr);
			}
		}
	}

	////
	// v6tab k6ikide kasutajate grupilt 2ra 6igused sellele objektile
	public static function deny_obj_access($oid)
	{
		if (aw_global_get("__is_install"))
		{
			return;
		}
		$all_users_grp = aw_ini_get("groups.all_users_grp");
		if (!$all_users_grp)
		{
			return;
		}

		reset(self::$acl_ids);
		while (list(,$k) = each(self::$acl_ids))
		{
			$aclarr[$k] = aw_ini_get("acl.denied");
		}

		self::add_acl_group_to_obj($all_users_grp, $oid, array(), false);

		// we don't need to flush caches here, because the user that was just created can't have an acl cache anyway
		self::save_acl($oid,$all_users_grp, $aclarr, false);		// give no access to all users
	}

	////
	// !Wrapper for "prog_acl", used to display the login form if the user is not logged in
	public static function prog_acl_auth($right,$progid)
	{
		if (aw_global_get("uid"))
		{
			return self::prog_acl($right,$progid);
		}
		else
		{
			// show the login form
			$auth = new auth_config();
			print $auth->show_login();
			// dat sucks
			exit;
		}
	}

	////
	// !checks if the user has the $right for program $progid
	public static function prog_acl($right = "", $progid = "can_admin_interface")
	{
		if (!($uid = aw_global_get("uid")))
		{
			return false;
		}

		if (!aw_ini_get("acl.check_prog") or aw_users::ROOT_UID === $uid)
		{
			return true;
		}
		else
		{
			if (is_numeric($progid))
			{
				$progid = "can_admin_interface";
			}
			$can_adm = false;
			$can_adm_max = 0;
			$can_adm_oid = 0;

			$gl = aw_global_get("gidlist_oid");
			foreach($gl as $g_oid)
			{
				$o = obj($g_oid);

				if ($o->prop("type") == 1 || $o->prop("type") == 3)
				{
					continue;
				}
				// idea here is, that for if acls we go through the group list until we find one that has if acls set
				// if none have, then default to all access
				// but for can admin interface we always thake the highest group for bc
				if ($o->prop("priority") > $can_adm_max)
				{
					if ("can_admin_interface" === $progid)
					{
						$can_adm = $o->prop($progid);
						$can_adm_max = $o->prop("priority");
					}
					elseif ($o->prop("if_acls_set") and $o->is_property($progid))
					{
						// all settings except can use admin depend on if_acls_set being true
						$can_adm = $o->prop($progid);
						$can_adm_max = $o->prop("priority");
					}
				}
			}

			if ($can_adm_max == 0 && $progid !== "can_admin_interface")
			{
				$can_adm = false === $can_adm ? true : $can_adm;
			}

			aw_global_set("acl_base::prog_acl_cache", $can_adm+1);
			return $can_adm;
		}
	}

	////
	// !returns an array of acls in the system as array(bitpos => name)
	public static function acl_list_acls()
	{
		return self::$acl_ids;
	}

	////
	// !returns all objects that have acl relations set for the groups
	// parameters
	//	grps - array of groups to return
	public static function acl_get_acls_for_groups($arr)
	{
		extract($arr);
		$gids = join(",", $grps);
		if ($gids == "")
		{
			return array();
		}

		$ret = array();

		$sql = "
			SELECT
				objects.name as obj_name,
				objects.oid,
				objects.status,
				objects.createdby as createdby,
				objects.parent as obj_parent,
				acl.gid,
				groups.name as grp_name,
				".self::sql_unpack_string()."
			FROM
				acl
				LEFT JOIN objects ON objects.oid = acl.oid
				LEFT JOIN groups ON groups.gid = acl.gid
			WHERE
				acl.gid IN ($gids)
		";
		object_loader::ds()->db_query($sql);
		while ($row = object_loader::ds()->db_next())
		{
			$ret[] = $row;
		}
		return $ret;
	}

	////
	// !returns the default group for the user
	private static function get_user_group($uid)
	{//TODO: user-isse viia
		static $cache;
		if (!empty($cache[$uid]))
		{
			return $cache[$uid];
		}
		$rv =  object_loader::ds()->db_fetch_field("SELECT oid FROM groups WHERE type=1 AND name='$uid'", "oid");
		$cache[$uid] = $rv;
		return $rv;
	}

	private static function get_acl_value($aclarr)
	{
		reset(self::$acl_ids);
		$nd = array();
		while(list($bitpos,$name) = each(self::$acl_ids))
		{
			if (isset($aclarr[$name]) && $aclarr[$name] == 1)
			{
				$a = aw_ini_get("acl.allowed");
			}
			else
			{
				$a = aw_ini_get("acl.denied");
			}

			$nd[$name] = isset($aclarr[$name]) ? (int) $aclarr[$name] : 0;
			$qstr[] = " ( $a << $bitpos ) ";
		}
		eval('$acl='.join(" | ",$qstr).";");
		return $acl;
	}

	public static function get_acl_value_n(array $aclarr = array())
	{
		reset(self::$acl_ids);
		$nd = array();
		while(list($bitpos,$name) = each(self::$acl_ids))
		{
			$nd[$name] = (int) ifset($aclarr, $name);
		}
		return $nd;
	}

	public static function acl_get_default_acl_arr()
	{
		reset(self::$acl_ids);
		$aclarr = array();
		while (list(,$k) = each(self::$acl_ids))
		{
			if ($k !== "can_subs")
			{
				$aclarr[$k] = aw_ini_get("acl.allowed");
			}
		}
		return $aclarr;
	}

	public static function on_user_login($arr = array())
	{
		self::flush_acl_cache();
	}

	public static function flush_acl_cache()
	{
		self::$__aw_acl_cache = array();
	}
}
