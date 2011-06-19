<?php
// site_seaarch_content_grp.aw - Saidi sisu otsingu grupp
/*

- when a menu is saved, search groups are scanned to see if any of them should display the just-saved menu
  if so, then the menu is added to the list in meta[search_menus], else it is removed

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE,CL_MENU, on_save_menu)


- when a search group is saved, the list of menus for it's display is regenerated

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE,CL_SITE_SEARCH_CONTENT_GRP, on_save_grp)

- when a menu is deleted, the list of menud needs to be regenerated

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE,CL_DOCUMENT, on_delete_menu)


@classinfo syslog_type=ST_SITE_SEARCH_CONTENT_GRP relationmgr=yes

@default table=objects
@default group=general

@property users_only type=checkbox ch_value=1 field=meta method=serialize
@caption Ainult sisse logitud kasutajatele

@property menus type=table editonly=1
@caption Vali men&uuml;&uuml;d

@property seach_notactive type=checkbox ch_value=1 field=meta method=serialize
@caption Otsi ka mitteaktiivsetest dokumentidest

@property search_only_kws type=relpicker multiple=1 reltype=RELTYPE_KW field=meta method=serialize
@caption Otsi ainult m&auml;rks&otilde;nadest

@reltype SEARCH_LOCATION value=1 clid=CL_MENU
@caption Otsingu l&auml;tekoht

@reltype KW value=2 clid=CL_KEYWORD
@caption M&auml;rks&otilde;na

*/

class site_search_content_grp extends class_base
{
	function site_search_content_grp()
	{
		$this->init(array(
			"clid" => CL_SITE_SEARCH_CONTENT_GRP
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "menus":
				$this->do_submenus($arr);
				break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "menus":
				$arr["obj_inst"]->set_meta("section_include_submenus", $arr["request"]["include_submenus"]);
				$arr["obj_inst"]->set_meta("notact", $arr["request"]["notact"]);
				break;
		}
		return $retval;
	}

	function do_submenus($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$prop = $arr["prop"];
		$obj = $arr["obj_inst"];
		$section_include_submenus = $obj->meta("section_include_submenus");
		$notact = $obj->meta("notact");
		// now I have to go through the process of setting up a generic table once again
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "class",
			"caption" => t("Klass"),
		));
		$t->define_field(array(
			"name" => "oid",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center"
		));
		$t->define_field(array(
			"name" => "check",
			"caption" => t("k.a. alammen&uuml;&uuml;d"),
			"talign" => "center",
			"width" => 80,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "check_na",
			"caption" => t("mitteaktiivsed"),
			"talign" => "center",
			"width" => 80,
			"align" => "center"
		));

		$clinf = aw_ini_get("classes");

		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_SEARCH_LOCATION"
		));


		foreach($conns as $c)
		{
			$c_o = $c->to();
			$cid = $c_o->id();
			$clid = $c_o->class_id();

			$el_arr = array(
				"oid" => $cid,
				"name" => $c_o->path_str(array(
					"max_len" => 3
				)),
				"class" => aw_ini_get("classes.{$clid}.name"),
				"check" => html::checkbox(array(
					"name" => "include_submenus[{$cid}]",
					"value" => $cid,
					"checked" => isset($section_include_submenus[$cid]) ? $section_include_submenus[$cid] : ""
				)),
				"check_na" => html::checkbox(array(
					"name" => "notact[{$cid}]",
					"value" => $cid,
					"checked" => isset($notact[$cid]) ? $notact[$cid] : ""
				)),
			);
			$t->define_data($el_arr);
		}
	}

	////
	// !returns all the menus that are a part of this search group
	// params
	//	id - group id
	function get_menus($arr)
	{
		if (!is_oid($arr["id"]) || !$this->can("view", $arr["id"]))
		{
			return array();
		}
		$o = obj($arr["id"]);
		if ($o->meta("version") == 2)
		{
		//	return safe_array($o->meta("grp_menus"));
		}
		return $this->_get_menus($arr);
	}

	function _get_menus($arr)
	{
		$o = obj($arr["id"]);

		$conns = $o->connections_from(array(
			"reltype" => "RELTYPE_SEARCH_LOCATION",
		));
		$se = array();
		foreach($conns as $conn)
		{
			$se[] = $conn->prop("to");
		}

		$sub = $o->meta("section_include_submenus");
		$notact = $o->meta("notact");

		// bloody hell .. this thing should differentiate menus and event searches ..
		// and possibly other objects as well. HOW?

		$ret = array();
		foreach($se as $m)
		{
			if (!empty($sub[$m]))
			{
				$ret[$m] = $m;
				$ot = new object_tree(array(
					"class_id" => array(CL_MENU, CL_PROMO, CL_CRM_SECTOR),
					"parent" => $m,
					"status" => (!empty($notact[$m]) ? array(object::STAT_ACTIVE, object::STAT_NOTACTIVE) : object::STAT_ACTIVE),
					"sort_by" => "objects.parent",
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"lang_id" => aw_global_get("lang_id"),
							"type" => MN_CLIENT
						)
					)),
					"sort_by" => "objects.parent, objects.jrk"
				));
				$ids = $ot->ids();

				foreach($ids as $id)
				{
					$ret[$id] = $id;
				}
			}
			else
			{
				$ret[$m] = $m;
			}
		}
		$gidlist = aw_global_get("gidlist");
		if (count($ret))
		{
			$ol = new object_list(array(
				"class_id" => array(CL_PROMO),
				"oid" => $ret
			));
			foreach($ol->arr() as $o)
			{
				// filter list by groups to whom the promo can be shown
				$found = false;
				$groups = $o->meta("groups");
				if (!is_array($groups) || count($groups) < 1)
				{
					$found = true;
				}
				else
				{
					foreach($groups as $gid)
					{
						if (isset($gidlist[$gid]) && $gidlist[$gid] == $gid)
						{
							$found = true;
						}
					}
				}

				if (!$found)
				{
					unset($ret[$o->id()]);
				}
			}
		}

		// if no user is logged on, then filter the list by "users_only"
		if (aw_global_get("uid") == "")
		{
			$ol = new object_list(array(
				"class_id" => array(CL_MENU, CL_PROMO, CL_CRM_SECTOR),
				"oid" => $ret
			));
			$ret = array();
			foreach($ol->arr() as $o)
			{
				if (!$o->prop("users_only"))
				{
					$ret[$o->id()] = $o->id();
				}
			}
		}
		return $ret;
	}

	function on_save_menu($arr)
	{
		// this should run only if the status of the menu object
		// changes and not every time a menu is saved. --duke
		$o = obj($arr["oid"]);
		$path = $o->path();

		$oid = $o->id();

		$grps = new object_list(array(
			"class_id" => CL_SITE_SEARCH_CONTENT_GRP
		));

		foreach($grps->arr() as $grp)
		{
			$fld = $this->_get_folders_for_grp($grp);
			$is_in_grp = false;
			foreach($fld as $f => $subs)
			{
				if ($f == $oid || ($subs && $this->_is_in_path($path, $f)))
				{
					$is_in_grp = true;
					break;
				}
			}

			if ($o->status() == STAT_NOTACTIVE)
			{
				// check if the menu is searchable when notactive
				$na_s = $grp->meta("notact");
				$p = $o->path();
				foreach($p as $p_o)
				{
					if (isset($fld[$p_o->id()]))
					{
						if (!$na_s[$p_o->id()])
						{
							$is_in_grp = false;
							break;
						}
					}
				}
			}

			if ($is_in_grp)
			{
				$mt = safe_array($grp->meta("grp_menus"));
				$mt[$oid] = $oid;
				$grp->set_meta("grp_menus", $mt);
				$grp->save();
			}
			else
			{
				$mt = safe_array($grp->meta("grp_menus"));
				if (isset($mt[$oid]))
				{
					unset($mt[$oid]);
					$grp->set_meta("grp_menus", $mt);
					$grp->save();
				}
			}
		}
	}

	function _get_folders_for_grp($grp)
	{
		$ret = array();
		$subs = safe_array($grp->meta("section_include_submenus"));
		foreach($grp->connections_from(array("type" => "RELTYPE_SEARCH_LOCATION")) as $c)
		{
			$c_to = $c->prop("to");
			$ret[$c_to] = $subs[$c_to] == $c_to;
		}

		if (!count($ret))
		{
			return array();
		}

		return $ret;
	}

	function _is_in_path($path, $f)
	{
		foreach($path as $o)
		{
			if ($o->id() == $f)
			{
				return true;
			}
		}
		return false;
	}

	function _regen_grp($grp)
	{
		$grp->set_meta("grp_menus", $this->_get_menus(array("id" => $grp->id())));
		$grp->set_meta("version", 2);
		$grp->save();
	}

	function on_save_grp($arr)
	{
		$o = obj($arr["oid"]);
		$this->_regen_grp($o);
	}

	function on_delete_menu($arr)
	{
		$o = obj($arr["oid"]);

		$grps = new object_list(array(
			"class_id" => CL_SITE_SEARCH_CONTENT_GRP
		));
		foreach($grps->arr() as $grp)
		{
			$mt = safe_array($grp->meta("grp_menus"));
			if (isset($mt[$o->id()]))
			{
				$this->_regen_grp($grp);
			}
		}
	}
}
