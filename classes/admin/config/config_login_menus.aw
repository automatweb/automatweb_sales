<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CONFIG_LOGIN_MENUS relationmgr=yes maintainer=kristo
@classinfo no_status=1

@default table=objects
@default group=general

	@property login_menus type=callback callback=callback_get_login_menus store=no

@groupinfo activity caption=Aktiivsus

	@property activity type=table group=activity no_caption=1
	@caption Aktiivsus

@reltype FOLDER value=1 clid=CL_MENU
@caption kataloog

*/

class config_login_menus extends class_base
{
	const AW_CLID = 251;

	function config_login_menus()
	{
		$this->init(array(
			"tpldir" => "admin/config/config_login_menus",
			"clid" => CL_CONFIG_LOGIN_MENUS
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "activity":
				$this->mk_activity_table($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "login_menus":
				foreach($arr["request"]["lm"] as $k => $v)
				{
					$arr["request"]["lm"][$k]["show"] = !empty($arr["request"]["lm"][$k]["show"]) ? 1 : 0;
				}
				$arr["obj_inst"]->set_meta("lm", $arr["request"]["lm"]);
				if ($arr["obj_inst"]->flag(OBJ_FLAG_IS_SELECTED))
				{
					$this->_set_active_menus($arr["obj_inst"]);
				}
				foreach(safe_array($arr["request"]["lm"]) as $d)
				{
					if ($this->can("view", $d["menu"]))
					{
						$arr["obj_inst"]->connect(array(
							"type" => "RELTYPE_FOLDER",
							"to" => $d["menu"]
						));
					}
				}
				break;

			case "activity":
				$ol = new object_list(array(
					"class_id" => CL_CONFIG_LOGIN_MENUS,
				));
				for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
				{
					if ($o->flag(OBJ_FLAG_IS_SELECTED) && $o->id() != $arr["request"]["active"])
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, false);
						$o->save();
					}
					else
					if ($o->id() == $arr["request"]["active"] && !$o->flag(OBJ_FLAG_IS_SELECTED))
					{
						$o->set_flag(OBJ_FLAG_IS_SELECTED, true);
						$o->save();
						$this->_set_active_menus($o);
					}
				}
				break;
		}
		return $retval;
	}	

	private function _set_active_menus($o)
	{
		$o_lm = $o->meta("lm");
		
		$gl = get_instance(CL_GROUP)->get_group_picker(array("type" => array(group_obj::TYPE_DYNAMIC, group_obj::TYPE_REGULAR)));

		$lm = $this->_get_login_menus();

		foreach($gl as $gid => $gname)
		{
			$go = obj($gid);
			$lm[aw_global_get("lang_id")][$go->prop("gid")]["show"] = $o_lm[$go->prop("gid")]["show"];
			$lm[aw_global_get("lang_id")][$go->prop("gid")]["menu"] = $o_lm[$go->prop("gid")]["menu"];
			$lm[aw_global_get("lang_id")][$go->prop("gid")]["pri"] = $o_lm[$go->prop("gid")]["pri"];
		}

		$data = aw_serialize($lm);
		$this->quote($data);
		$this->set_cval("login_menus_".aw_ini_get("site_id"),$data);

		// clear cache 
		$c = get_instance("cache");
		$c->full_flush();		
	}

	function callback_get_login_menus($arr)
	{
		// foreach group add relpicker
		$ret = array();

		$gl = get_instance(CL_GROUP)->get_group_picker(array("type" => array(group_obj::TYPE_DYNAMIC, group_obj::TYPE_REGULAR)));

		$lm = $arr["obj_inst"]->meta("lm");


		foreach($gl as $gid => $gname)
		{
			$go = obj($gid);
			$gid = $go->prop("gid");

			$ret[] = array(
				"type" => "text",
				"subtitle" => 1,
				"caption" => $gname,
				"size" => 4,
				"name" => "lm[$gid][subtitle]",
			);

			$ret[] = array(
				"type" => "checkbox",
				"caption" => t("Kuva men&uuml;&uuml;d"),
				"name" => "lm[$gid][show]",
				"value" => isset($lm[$gid]["show"]) ? $lm[$gid]["show"] : 1,
			);

			$ret[] = array(
				"type" => "textbox",
				"caption" => t("Prioriteet"),
				"size" => 4,
				"name" => "lm[$gid][pri]",
				"value" => $lm[$gid]["pri"]
			);

			$ret[] = array(
				"caption" => t("Men&uuml;&uuml;"),
				"type" => "relpicker",
				"name" => "lm[$gid][menu]",
				"value" => $lm[$gid]["menu"],
				"reltype" => "RELTYPE_FOLDER"
			);
		}

		return $ret;
	}

	private function mk_activity_table($arr)
	{
		// this is supposed to return a list of all active polls
		// to let the user choose the active one
		$table = &$arr["prop"]["vcl_inst"];
		$table->parse_xml_def("activity_list");

		$pl = new object_list(array(
			"class_id" => CL_CONFIG_LOGIN_MENUS
		));	
		for($o = $pl->begin(); !$pl->end(); $o = $pl->next())
		{
			$actcheck = checked($o->flag(OBJ_FLAG_IS_SELECTED));
			$act_html = "<input type='radio' name='active' $actcheck value='".$o->id()."'>";
			$row = $o->arr();
			$row["active"] = $act_html;
			$table->define_data($row);
		};
	}

	/**  
		@attrib name=find_active_edit params=name default="0"
	**/
	function find_active_edit($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CONFIG_LOGIN_MENUS,
			"flags" => array("mask" => OBJ_FLAG_IS_SELECTED, "flags" => OBJ_FLAG_IS_SELECTED)
		));
		// if none found, go to the old interface
		if ($ol->count() < 1)
		{
			return $this->mk_my_orb("login_menus", array(), "config");
		}
		$o = $ol->begin();
		return $this->mk_my_orb("change", array("id" => $o->id()));
	}

	function on_site_init(&$dbi, &$site, &$ini_opts, &$log, $vars)
	{
		// we are using the new db as the default, so we can create objects
		$oid = $dbi->db_fetch_field("SELECT oid FROM objects WHERE class_id = ".CL_CONFIG_LOGIN_MENUS, "oid");
		$o = obj($oid);
		$o->set_flag(OBJ_FLAG_IS_SELECTED, true);

		$o->connect(array(
			"to" => $vars["logged_users"],
			"reltype" => "RELTYPE_FOLDER",
		));

		$o->connect(array(
			"to" => $vars["logged_admins"],
			"reltype" => "RELTYPE_FOLDER",
		));

		$o->connect(array(
			"to" => $vars["logged_editors"],
			"reltype" => "RELTYPE_FOLDER",
		));

		// and manually set login menu conf as correct
		$data = array();
		for ($lid = 1; $lid < 5; $lid++)
		{
			$data[$lid][2]["menu"] = $vars["logged_admins"];
			$data[$lid][2]["pri"] = 1000;
	
			$data[$lid][1]["menu"] = $vars["logged_users"];
			$data[$lid][1]["pri"] = 100;

			$data[$lid][3]["menu"] = $vars["logged_editors"];
			$data[$lid][3]["pri"] = 120;

			$data[$lid][4]["menu"] = $vars["logged_users"];
			$data[$lid][4]["pri"] = 110;

			$data[$lid][5]["menu"] = $vars["logged_users"];
			$data[$lid][5]["pri"] = 110;

			if ($lid == 1)
			{
				$o->set_meta("lm", $data[$lid]);
			}
		}
		$o->save();

		$str = aw_serialize($data);
		$this->quote(&$str);
		$dbi->db_query("INSERT INTO config(ckey,content) values('login_menus_".$ini_opts["site_id"]."','$str')");
	}

	/** Finds the active set of login menus and returns the menu for the current user
		@attrib api=1 params=name

		@retuns
			-1 if no menus are set for the current user
			object id of the active menu object for the current user
	**/
	function get_login_menus($args = array())
	{
		$_data = $this->_get_login_menus();
		$data = $_data[aw_global_get("lang_id")];
		if (!is_array($data))
		{
			if (is_array($_data))
			{
				foreach($_data as $k => $v)
				{
					if (is_array($v))
					{
						$data = $v;
					}
				}
			}
		};

		if (!is_array($data))
		{
			return;
		}
		$gids = aw_global_get("gidlist");
		$cur_pri = -1;
		$cur_menu = -1;

		if (!is_array($gids))
		{
			return;
		};

		foreach($gids as $gid)
		{
			if (isset($data[$gid]) && ($data[$gid]["pri"] > $cur_pri) && ($data[$gid]["menu"]))
			{
				$cur_pri = $data[$gid]["pri"];
				$cur_menu = $data[$gid]["menu"];
			}
		};

		return $cur_menu;
	}

	private function _get_login_menus($args = array())
	{
		$sid = aw_ini_get("site_id");
		$res = $this->get_cval("login_menus_".$sid);
		if (!$res)
		{
			$res = $this->get_cval("login_menus");
		}
		return aw_unserialize($res);
	}

	/** Sets the login menu for the given group
		@attrib api=1 params=pos
	
		@param group_id required type=int
			The gid of the group to set menus for

		@param menu_id required type=oid
			The menu to set as the login menu for the group
	**/
	function set_login_menu_for_group($grp_id, $menu_id)
	{
		// get active settings
		$lm = $this->_get_login_menus();
		$lm[aw_global_get("lang_id")][$grp_id]["menu"] = $menu_id;

		$str = aw_serialize($lm);
		$this->quote(&$str);
		$this->set_cval("login_menus_".aw_ini_get("site_id"),$str);
	}

	public function show_login_menu()
	{
		$_data = $this->_get_login_menus();
		$data = $_data[aw_global_get("lang_id")];
		if (!is_array($data))
		{
			if (is_array($_data))
			{
				foreach($_data as $k => $v)
				{
					if (is_array($v))
					{
						$data = $v;
					}
				}
			}
		};

		if (!is_array($data))
		{
			return true;
		}
		$gids = aw_global_get("gidlist");
		$cur_pri = -1;
		$cur_menu = -1;

		if (!is_array($gids))
		{
			return true;
		};

		$show = true;
		foreach($gids as $gid)
		{
			if (isset($data[$gid]) && $data[$gid]["pri"] > $cur_pri)
			{
				$cur_pri = $data[$gid]["pri"];
				$show = !isset($data[$gid]["show"]) || !empty($data[$gid]["show"]);
			}
		};
		return $show;
	}
}
?>
