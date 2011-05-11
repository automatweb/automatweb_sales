<?php

/*
HANDLE_MESSAGE(MSG_USER_LOGIN, on_user_login)
*/

lc_load("definition");

class acl_base extends db_connector
{
	private $acl_ids;

	function sql_unpack_string()
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
			reset($this->acl_ids);
			while (list($bitpos, $name) = each($this->acl_ids))
			{
				$qstr[] = " ( cast ( (acl / ".pow(2,$bitpos).") as int ) & 3) AS $name";
			}
		}
		else
		{
			reset($this->acl_ids);
			while (list($bitpos, $name) = each($this->acl_ids))
			{
				$qstr[] = " ((acl >> $bitpos) & 3) AS $name";
			}
		}
		$s =  join(",",$qstr);
		return $s;
	}

	function get_acl_groups_for_obj($oid)
	{
		if (aw_ini_get("acl.use_new_acl"))
		{
			$ret = safe_array(aw_unserialize($this->db_fetch_field("SELECT acldata FROM objects WHERE oid = '{$oid}'", "acldata"), false, true));
		}
		else
		{
			$ret = array();
			$acls = aw_ini_get("acl.names");
			$q = "SELECT *,groups.name as name,".$this->sql_unpack_string()."
						FROM acl LEFT JOIN groups ON groups.gid = acl.gid
						WHERE acl.oid = $oid";
			$this->db_query($q);
			while ($row = $this->db_next())
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

	function add_acl_group_to_obj($gid,$oid,$aclarr = array(), $invd = true)
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
		if (!$this->db_fetch_field("SELECT gid FROM acl WHERE gid = '$gid' AND oid = '$oid'", "gid"))
		{
			$this->db_query("insert into acl(gid,oid) values($gid,$oid)");
		}
		if (sizeof($aclarr) == 0)
		{
			// set default acl if not specified otherwise
			$aclarr = $GLOBALS["cfg"]["acl"]["default"];
		};
		$this->save_acl($oid,$gid,$aclarr, $invd);
		if ($invd)
		{
			aw_session_set("__acl_cache", array());
			$c = get_instance("cache");
			$c->file_clear_pt("acl");
			$c->file_clear_pt_oid_fn("storage_object_data", $oid, "objdata-".$oid);
		}
	}

	function add_acl_group_to_new_obj($g_oid,$oid, $aclarr)
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
		$acl = $this->get_acl_value($aclarr);
		aw_disable_acl();
		$go = obj($g_oid);
		$gid = $go->prop("gid");
		aw_restore_acl();
		$this->db_query("INSERT INTO acl(acl,oid,gid) VALUES($acl,$oid,$gid)");

		if (aw_ini_get("acl.use_new_acl"))
		{
			$ad = safe_array(aw_unserialize($this->db_fetch_field("SELECT acldata FROM objects WHERE oid = '$oid'", "acldata")), false, true);
			// convert gid to oid
			$g_oid = $go->id();
			$ad[$g_oid] = $this->get_acl_value_n($aclarr);
			$ser = aw_serialize($ad, SERIALIZE_NATIVE);
			$this->quote($ser);
			$this->db_query("UPDATE objects SET acldata = '$ser' WHERE oid = $oid");
		}
	}

	function remove_acl_group_from_obj($g_obj,$oid)
	{
		if (!is_oid($oid))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("acl_base::remove_acl_group_from_obj(%s, %s,..): the given oid is incorrect"), $g_obj->id(), $oid)
			));
		}
		$gid = $g_obj->prop("gid");
		$this->db_query("DELETE FROM acl WHERE gid = {$gid} AND oid = {$oid}");

		if (aw_ini_get("acl.use_new_acl"))
		{
			$ad = safe_array(aw_unserialize($this->db_fetch_field("SELECT acldata FROM objects WHERE oid = '{$oid}'", "acldata"), false, true));
			unset($ad[$g_obj->id()]);
			$ser = aw_serialize($ad, SERIALIZE_NATIVE);
			$this->quote($ser);
			$this->db_query("UPDATE objects SET acldata = '{$ser}' WHERE oid = {$oid}");
		}

		aw_session_set("__acl_cache", array());
		$c = get_instance("cache");
		$c->file_clear_pt("acl");
		$c->file_clear_pt_oid_fn("storage_object_data", $oid, "objdata-".$oid);
	}

	function save_acl($oid,$gid,$aclarr, $invd = true)
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
		$acl = $this->get_acl_value($aclarr);
		$this->db_query("UPDATE acl SET acl = $acl WHERE oid = $oid AND gid = $gid");

		if (aw_ini_get("acl.use_new_acl"))
		{
			$ad = safe_array(aw_unserialize($this->db_fetch_field("SELECT acldata FROM objects WHERE oid = '$oid'", "acldata"), false, true));
			// convert gid to oid
			$g_oid = $this->db_fetch_field("SELECT oid FROM groups WHERE gid = '$gid'", "oid");
			$ad[$g_oid] = $this->get_acl_value_n($aclarr);
			$ser = aw_serialize($ad, SERIALIZE_NATIVE);
			$this->quote($ser);
			$this->db_query("UPDATE objects SET acldata = '$ser' WHERE oid = $oid");
			// If we change the ACL we have to change it in the cache also! -kaarel 28.11.2008
			$GLOBALS["__obj_sys_acl_memc"][$oid]["acldata"][$g_oid] = $ad[$g_oid];
		}

		if ($invd)
		{
			aw_session_set("__acl_cache", array());
			$c = get_instance("cache");
			$c->file_clear_pt("acl");
			$c->file_clear_pt_oid_fn("storage_object_data", $oid, "objdata-".$oid);
		}
	}

	function get_acl_for_oid_gid($oid,$gid)
	{
		if (!$oid || !$gid)
		{
			return;
		}
		$q = "SELECT
						*,
						acl.id as acl_rel_id,
						objects.parent as parent,
						".$this->sql_unpack_string().",
						groups.priority as priority,
						acl.oid as oid
					FROM acl
						LEFT JOIN groups ON groups.gid = acl.gid
						LEFT JOIN objects ON objects.oid = acl.oid
					WHERE acl.oid = '$oid' AND acl.gid = '$gid'
				";
		$this->db_query($q);
		$row = $this->db_next();
		return $row;
	}

	function get_acl_for_oid($oid)
	{
		$g_pris = aw_global_get("gidlist_pri");	// this gets made in users::request_startup

		$max_pri = 0;
		$max_row = array("priority" => null);
		$q = "
			SELECT
				acl.id as acl_rel_id,
				objects.parent as parent,
				".$this->sql_unpack_string().",
				objects.oid as oid,
				objects.parent as parent,
				acl.gid as gid ,
				objects.brother_of as brother_of
			FROM
				objects
				LEFT JOIN acl ON objects.oid = acl.oid
			WHERE
				objects.oid = '$oid' AND objects.status != 0
		";
		$this->db_query($q);
		$rv = null;
		while ($row = $this->db_next())
		{
			$max_row["oid"] = $row["oid"];
			$max_row["brother_of"] = $row["brother_of"];
			$max_row["parent"] = $row["parent"];
			if (!$row["gid"] || !isset($g_pris[$row["gid"]]))
			{
				continue;
			}

			if ($g_pris[$row["gid"]] >= $max_pri)
			{
				$max_pri = $g_pris[$row["gid"]];
				$max_row = $row;
				$max_row["priority"] = $max_pri;
			}
		}
		return $max_row;
	}

	function can_aw($access,$oid)
	{
		$access="can_".$access;

		//$this->save_handle();

		$max_priority = -1;
		$max_acl = $GLOBALS["cfg"]["acl"]["default"];
		$max_acl["acl_rel_id"] = "666";
		$cnt = 0;

		$orig_oid = $oid;
		// here we must traverse the tree from $oid to 1, gather all the acls and return the one with the highest priority
		while ($oid > 0)
		{
			$_t = aw_cache_get("aclcache",$oid);
			if (is_array($_t))
			{
				$tacl = $_t;
				$parent = $_t["parent"];
				if (!isset($tacl["oid"]))
				{
					// if we are on any level and we get back no object, return no access
					// cause then we asked about an object that does not exist or an object that is below a deleted object!
					return array();
				}
			}
			else
			{
				$tacl = $this->get_acl_for_oid($oid);

				if (!isset($tacl["oid"]))
				{
					// if we are on any level and we get back no object, return no access
					// cause then we asked about an object that does not exist or an object that is below a deleted object!
					// set the oid's acl cache as not-bloody-anything
					aw_cache_set("aclcache",$oid,array());
					return array();
				}

				if ($tacl)
				{
					// found acl for this object from the database, so check it
					$parent = $tacl["parent"];
					aw_cache_set("aclcache",$oid,$tacl);
				}
				else
				{
					// no acl for this object in the database, find it's parent
					$parent = $this->db_fetch_field("SELECT parent FROM objects WHERE oid = '$oid'","parent");
					$tacl = array("oid" => $oid,"parent" => $parent,"priority" => -1);
					aw_cache_set("aclcache",$oid,$tacl);
				}
			}

			$skip = ($oid == $orig_oid && !empty($tacl["can_subs"]));
			if ($tacl["priority"] > $max_priority && !$skip)
			{
				$max_priority = $tacl["priority"];
				$max_acl = $tacl;
			}
			if (++$cnt > 100)
			{
				error::raise(array(
					"id" => ERR_ACL_EHIER,
					"msg" => sprintf(t("acl_base->can(%s, %s): error in object hierarchy, count exceeded!"), $access,$oid)
				));
			}

			$oid = $parent;
		}

		// now, we have the result. but for brothers we need to do this again for the original object and only use the can_delete privilege from the brother
		$_t = aw_cache_get("aclcache",$orig_oid);
		if ($_t["brother_of"] > 0 && $_t["brother_of"] != $_t["oid"])
		{
			$rv = $this->can_aw($access, $_t["brother_of"]);
			$rv["can_delete"] = isset($max_acl["can_delete"]) ? $max_acl["can_delete"] : 0;
			$max_acl = $rv;
		}

		// if the max_acl does not contain view and no user is logged, return default
		if (!isset($max_acl["can_view"]) && aw_global_get("uid") == "")
		{
			return $GLOBALS["cfg"]["acl"]["default"];
		}

		// and now return the highest found
		return $max_acl;
	}

	function init_acl()
	{
		$GLOBALS["object_loader"]->set___aw_acl_cache();
	}

	// black magic follows
	/** Tells if user can perform operation on object
		@attrib api=1 params=pos
		@param operation_id type=string
		@param object_id type=oid
		@param user_oid type=oid default=NULL
			Defaults to current user if not specified
		@comment
		@returns bool
		@errors none
	**/
	public function can($operation_id, $object_id, $user_oid = null)
	{
		$operation_id = "can_{$operation_id}";

		if (!is_oid($object_id) or !in_array($operation_id, $this->acl_ids))
		{
			return false;
		}

		$uid = aw_global_get("uid");
		//TODO: teostada variant kui antakse root kasutaja user_oid
		if (aw_ini_get("acl.no_check") or "root" === $uid)
		{ // check if object record exists and state isn't 'deleted'
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
		$user_oid = $user_oid ? $user_oid : aw_global_get("uid_oid");

		if (aw_ini_get("acl.use_new_acl"))
		{
			if (!isset($this->__aw_acl_cache[$object_id]) || !($max_acl = $this->__aw_acl_cache[$object_id]))
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
					if ($max_acl === false)
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

			if (!isset($max_acl["can_view"]) && !$user_oid)
			{
				$can = aw_ini_get("acl.default.{$operation_id}") === aw_ini_get("acl.allowed");
			}
			else
			{
				$can = isset($max_acl[$operation_id]) ? (bool) $max_acl[$operation_id] : false;
			}
		}
		else
		{
			$this->save_handle();
			if (!($max_acl = aw_cache_get("__aw_acl_cache", $object_id)))
			{
				$fn = "acl-{$object_id}-uid-{user_oid}";
				if (($str_max_acl = cache::file_get_pt_oid("acl", $object_id, $fn)) == false)
				{
					$max_acl = $this->can_aw($operation_id,$object_id);

					cache::file_set_pt_oid("acl", $object_id, $fn, aw_serialize($max_acl, SERIALIZE_NATIVE));
					aw_cache_set("__aw_acl_cache", $object_id, $max_acl);
				}
				else
				{
					$max_acl = aw_unserialize($str_max_acl, false, true);
				}
			}

			$this->restore_handle();
			$can = isset($max_acl[$operation_id]) ? $max_acl[$operation_id] : null;
		}

		return $can;
	}

	private function _calc_max_acl($oid)
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
				$tmp = object_loader::instance()->ds->get_objdata($cur_oid, array( //XXX: TODO: siin peaks ilma objloaderita saama
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
				return false;
			}

			// status and brother_of are not set when acl data is read from e.g. acl mem cache
			if (isset($tmp["status"]))
			{
				if ($tmp["status"] == 0)
				{
					return false;
				}
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

			if (++$cnt > 100)//TODO: move this limit setting to config? mis count see on yldse
			{
				throw new awex_obj_data_integrity(sprintf("Error in object hierarchy, count exceeded (%s, %s)", $access, $oid));
			}

			// go to parent
			$cur_oid = $tmp["parent"];
		}

		// now, we have the result. but for brothers we need to do this again for the original object and only use the can_delete privilege from the brother
		if ($do_orig !== false)
		{
			$rv = $this->_calc_max_acl($do_orig);
			if ($rv === false)
			{
				return false;   // if the original is deleted, then the brother is deleted as well
			}
			$rv["can_delete"] = isset($max_acl["can_delete"]) ? (int) $max_acl["can_delete"] : 0;
			return $rv;
		}
		return $max_acl;
	}

	function create_obj_access($oid, $uuid = "")
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
			reset($this->acl_ids);
			$aclarr = array();
			while (list(,$k) = each($this->acl_ids))
			{
				if ($k !== "can_subs")
				{
					$aclarr[$k] = aw_ini_get("acl.allowed");
				}
			}

			$gr = $this->get_user_group($uuid);
			if (!$gr)
			{
				if (method_exists($this, "raise_error"))
				{
					$this->raise_error(ERR_ACL_NOGRP,LC_NO_DEFAULT_GROUP,true);
				}
			}

			if ($gr)
			{
				$this->add_acl_group_to_new_obj($gr, $oid, $aclarr);
			}
		}
	}

	////
	// v6tab k6ikide kasutajate grupilt 2ra 6igused sellele objektile
	function deny_obj_access($oid)
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

		reset($this->acl_ids);
		while (list(,$k) = each($this->acl_ids))
		{
			$aclarr[$k] = aw_ini_get("acl.denied");
		}

		$this->add_acl_group_to_obj($all_users_grp, $oid, array(), false);

		// we don't need to flush caches here, because the user that was just created can't have an acl cache anyway
		$this->save_acl($oid,$all_users_grp, $aclarr, false);		// give no access to all users
	}

	////
	// !Wrapper for "prog_acl", used to display the login form if the user is not logged in
	function prog_acl_auth($right,$progid)
	{
		if (aw_global_get("uid"))
		{
			return $this->prog_acl($right,$progid);
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
	function prog_acl($right = "", $progid = "can_admin_interface")
	{
		if (!aw_global_get("uid"))
		{
			return false;
		}

		if (aw_ini_get("acl.check_prog") != true)
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
			// turn off acl checks for this
			//XXX: miks?
			$tmp = $GLOBALS["cfg"]["acl"]["no_check"];
			$GLOBALS["cfg"]["acl"]["no_check"] = 1;
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
			$GLOBALS["cfg"]["acl"]["no_check"] = $tmp;

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
	function acl_list_acls()
	{
		return $this->acl_ids;
	}

	function acl_get_acls_for_grp($gid,$min,$num)
	{
		// damn this thing is gonna be fuckin huge
		$ret= array();
		$this->db_query("SELECT objects.name as name,acl.oid as oid, ".$this->sql_unpack_string()."	FROM acl LEFT JOIN objects ON objects.oid = acl.oid WHERE acl.gid = $gid LIMIT $min,$num");
		return $ret;
	}

	function auth_error()
	{
		header ("HTTP/1.1 404 Not Found");
		echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">";
		echo "<html><head>";
		echo "<title>404 Not Found</title>";
		echo "</head><body>";
		echo "<h1>Not Found</h1>";
		echo "The requested URL ".aw_global_get("REQUEST_URI")." ";
		echo "was not found on this server.<p>";
		echo "<hr />";
		echo "<ADDRESS>Apache/1.3.14 Server at ".aw_global_get("HTTP_HOST");
		echo "Port 80</ADDRESS>";
		echo "</body></html>";
		die();
	}

	////
	// !returns all objects that have acl relations set for the groups
	// parameters
	//	grps - array of groups to return
	function acl_get_acls_for_groups($arr)
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
				".$this->sql_unpack_string()."
			FROM
				acl
				LEFT JOIN objects ON objects.oid = acl.oid
				LEFT JOIN groups ON groups.gid = acl.gid
			WHERE
				acl.gid IN ($gids)
		";
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$ret[] = $row;
		}
		return $ret;
	}

	function init($args = array())
	{
		$this->acl_ids = aw_ini_get("acl.ids");
		parent::init($args);
	}

	////
	// !returns the default group for the user
	function get_user_group($uid)
	{
		static $cache;
		if (!empty($cache[$uid]))
		{
			return $cache[$uid];
		}
		$rv =  $this->db_fetch_field("SELECT oid FROM groups WHERE type=1 AND name='$uid'", "oid");
		$cache[$uid] = $rv;
		return $rv;
	}

	function get_acl_value($aclarr)
	{
		reset($this->acl_ids);
		$nd = array();
		while(list($bitpos,$name) = each($this->acl_ids))
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

	function get_acl_value_n($aclarr)
	{
		reset($this->acl_ids);
		$nd = array();
		while(list($bitpos,$name) = each($this->acl_ids))
		{
			$nd[$name] = (int)ifset($aclarr, $name);
		}
		return $nd;
	}

	function acl_get_default_acl_arr()
	{
		reset($this->acl_ids);
		$aclarr = array();
		while (list(,$k) = each($this->acl_ids))
		{
			if ($k !== "can_subs")
			{
				$aclarr[$k] = aw_ini_get("acl.allowed");
			}
		}
		return $aclarr;
	}

	function on_user_login($arr = array())
	{
		$this->init_acl();
	}
}
