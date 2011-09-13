<?php
/*
	@classinfo no_comment=1 no_status=1 prop_cb=1

	@default table=objects
	@default field=meta
	@default method=serialize
	@default group=general

	@property behaviour type=chooser options=restrictive,permissive group=general default=restrictive
	@comment Lubav -- lisandunud klasside kohta on vaikimisi k&otilde;ik tegevused lubatud, keelav -- keelatud
	@caption K&auml;itumine

	@property sel type=table store=no group=general no_caption=1

*/

class add_tree_conf extends class_base
{
	private $visible = false;
	private $usable = false;
	private $conf_exists = false;
	private static $system_classes = array(
		"sys",
		"popup_search",
		"config",
		"objpicker",
		"relationmgr",
		"converters",
		"acl_manager"
	);

	private $level = 0;

	function add_tree_conf()
	{
		$this->init(array(
			"clid" => CL_ADD_TREE_CONF
		));
	}

	function _get_behaviour(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["options"] = array(
			add_tree_conf_obj::BEHAVIOUR_RESTRICTIVE => t("Keelav"),
			add_tree_conf_obj::BEHAVIOUR_PERMISSIVE => t("Lubav")
		);
		return $r;
	}

	function _get_sel(&$arr)
	{
		$this->_do_sel_tbl($arr["prop"], $arr["obj_inst"]->meta("visible"), $arr["obj_inst"]->meta("usable"), $arr["obj_inst"]->meta("alias_add"));
		return PROP_OK;
	}

	function _set_sel(&$arr)
	{
		$arr["obj_inst"]->set_meta("visible", is_array($arr["request"]["visible"]) ? $arr["request"]["visible"] : array());
		$arr["obj_inst"]->set_meta("usable", is_array($arr["request"]["usable"]) ? $arr["request"]["usable"] : array());
		$arr["obj_inst"]->set_meta("alias_add", is_array($arr["request"]["alias_add"]) ? $arr["request"]["alias_add"] : array());
		return PROP_OK;
	}

	function callback_pre_save($arr)
	{
		// save folder structure from ini file
		$arr["obj_inst"]->set_meta("folder_structure", aw_ini_get("classfolders"));
		$arr["obj_inst"]->set_meta("class_structure", aw_ini_get("classes"));
	}

	private function _do_sel_tbl(&$arr, $visible, $usable, $alias_add)
	{
		$t = $arr["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi")
		));

		$t->define_field(array(
			"name" => "visible",
			"caption" => "<a href='javascript:void(0)' onClick='aw_sel_chb(document.changeform,\"visible\")'>".t("N&auml;htav")."</a>",
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "usable",
			"caption" => "<a href='javascript:void(0)' onClick='aw_sel_chb(document.changeform,\"usable\")'>".t("Kasutatav")."</a>",
			"align" => "center"
		));


		$t->define_field(array(
			"name" => "alias_add",
			"caption" => "<a href='javascript:void(0)' onClick='aw_sel_chb(document.changeform,\"alias_add\")'>".t("Aliasena lisatav")."</a>",
			"align" => "center"
		));

		if (!is_array($visible) || !is_array($usable))
		{
			// no configuration set yet, set defaults
			$default = !empty($arr["obj_inst"]) && add_tree_conf_obj::BEHAVIOUR_PERMISSIVE === $arr["obj_inst"]->prop("behaviour") ? 1 : 0;
			$visible = array();
			$usable = array();
			$alias_add = array();

			$clsf = aw_ini_get("classfolders");
			foreach($clsf as $id => $d)
			{
				$visible["fld"][$id] = $default;
			}
			$tmp = aw_ini_get("classes");
			foreach($tmp as $id => $d)
			{
				$visible["obj"][$id] = $default;
				$usable[$id] = $default;
				if (!empty($d["alias"]))
				{
					$alias_add[$id] = $default;
				}
			}

			// set system classes default
			foreach (self::$system_classes as $system_class_name)
			{
				$usable[$system_class_name] = $default;
			}
		}

		$t->set_sortable(false);

		$this->level = -1;
		$this->_req_do_table($t, 0, $visible, $usable, $alias_add);

		$t->define_data(array(
			"name" => "<b><i>".t("AW S&uuml;steemsed klassid")."</i></b>"
		));

		foreach (self::$system_classes as $system_class_name)
		{
			$t->define_data(array(
				"name" => "<i>{$system_class_name}</i>",
				"usable" => html::checkbox(array(
					"name" => "usable[{$system_class_name}]",
					"value" => 1,
					"checked" => !empty($usable[$system_class_name])
				))
			));
		}
	}

	private function _req_do_table($t, $parent, $visible, $usable, $alias_add)
	{
		$tmp = aw_ini_get("classes");
		if ($this->level == -1)
		{
			foreach($tmp as $cl_id => $cld)
			{
				if (empty($cld["parents"]))
				{
					$ala = html::checkbox(array(
						"name" => "alias_add[{$cl_id}]",
						"value" => 1,
						"checked" => !empty($alias_add[$cl_id])
					));

					$class_name = empty($cld["name"]) ? (!empty($cld["file"]) ? basename($cld["file"]) : $cl_id) : $cld["name"];

					$t->define_data(array(
						"name" => str_repeat("&nbsp;", ($this->level+1) * 10) . $class_name . " [" . substr(strrchr($cld["file"], "/"), 1) . "]",
						"visible" => html::checkbox(array(
							"name" => "visible[obj][{$cl_id}]",
							"value" => 1,
							"checked" => !empty($visible["obj"][$cl_id])
						)),
						"usable" => html::checkbox(array(
							"name" => "usable[{$cl_id}]",
							"value" => 1,
							"checked" => !empty($usable[$cl_id])
						)),
						"alias_add" => $ala,
					));
				}
			}
		}

		$this->level++;
		$clsf = aw_ini_get("classfolders");
		foreach($clsf as $id => $cfd)
		{
			if ($cfd["parent"] == $parent)
			{
				$t->define_data(array(
					"name" => str_repeat("&nbsp;", $this->level * 10)."<b>".$cfd["name"]."</b>",
					"visible" => html::checkbox(array(
						"name" => "visible[fld][{$id}]",
						"value" => 1,
						"checked" => !empty($visible["fld"][$id])
					))
				));

				foreach($tmp as $cl_id => $cld)
				{
					if (ifset($cld, "parents") == "")
					{
						continue;
					}

					$pss = $this->make_keys(explode(",", $cld["parents"]));
					if (!empty($pss[$id]))
					{
						$ala = "";
						$ala = html::checkbox(array(
							"name" => "alias_add[{$cl_id}]",
							"value" => 1,
							"checked" => !empty($alias_add[$cl_id])
						));

						$t->define_data(array(
							"name" => str_repeat("&nbsp;", ($this->level+1) * 10).$cld["name"] . " [" . substr(strrchr($cld["file"], "/"), 1) . "]",
							"visible" => html::checkbox(array(
								"name" => "visible[obj][{$cl_id}]",
								"value" => 1,
								"checked" => !empty($visible["obj"][$cl_id])
							)),
							"usable" => html::checkbox(array(
								"name" => "usable[{$cl_id}]",
								"value" => 1,
								"checked" => !empty($usable[$cl_id])
							)),
							"alias_add" => $ala,
						));
					}
				}

				$this->_req_do_table($t, $id, $visible, $usable, $alias_add);
			}
		}
		$this->level--;
	}

	// returns the active add_tree_conf oid for the current user, false if none
	//DEPRECATED. use add_tree_conf_obj::get_active_configuration()
	function get_current_conf() { $cfg = add_tree_conf_obj::get_active_configuration(); return $cfg ? $cfg->id() : 0; }

	/** returns the list of usable classes for tree conf $id
		@attrib api=1 params=pos

		@param id required type=oid
			The add_tree_conf object oid to use

		@returns
			array { clid => clid } for all clids that the add tree conf allows to be used
	**/
	public function get_usable_filter($id)
	{
		$o = obj($id);
		$r = $o->meta("usable");
		$v = $o->meta("visible");
		$ret = array();

		$this->get_meta_classes($o, $r, $v, $ret);
		return $ret;
	}

	private function get_meta_classes($o, $r, $v, &$ret)
	{
		$clss = $o->meta("class_structure");
		if (!is_array($clss))
		{
			$clss = aw_ini_get("classes");
		}

		$grps = $o->meta("folder_structure");
		if (!is_array($grps))
		{
			$grps = aw_ini_get("classfolders");
		}

		foreach($r as $clid => $one)
		{
			if ($one == 1)
			{
				// also, if the class is in some groups and for all those groups access has been turned off
				// do not show the alias
				$grp = explode(",", ifset($clss, $clid, "parents"));
				$show = false;
				foreach($grp as $g)
				{
					// must check group parents as well :(
					// but CL_MENU has no parent (g == 0) and we have to deal with it -- duke
					$has_grp = !empty($v["fld"][$g]) || $g == 0;
					if ($has_grp && $g != 0)
					{
						while ($g)
						{
							if (empty($v["fld"][$g]))
							{
								$has_grp = false;
								break;
							}
							$g = $grps[$g]["parent"];
						}
					}

					if ($has_grp)
					{
						$show = true;
					}
				}

				if ($show)
				{
					$ret[$clid] = $clid;
				}
			}
		}
	}

	/** returns the list of alias-addable classes for tree conf $id
		@attrib api=1 params=pos

		@param id required type=oid
			The add_tree_conf objects oid to use

		@returns
			array { clid => clid } for all classes that can be added as aliases
	**/
	function get_alias_filter($id)
	{
		$o = obj($id);
		$r = $o->meta("alias_add");
		$v = $o->meta("visible");
		$ret = array();

		$this->get_meta_classes($o, $r, $v, $ret);
		return $ret;
	}

	/** returns true if the given class can be used in the given conf
		@attrib api=1 params=pos

		@param atc required type=cl_add_tree_conf
			The add tree conf object to use

		@param class required type=clid|class_name
			The class_id or class name to check for access

		@returns
			true if class can be accessed, false if not
	**/
	public static function can_access_class($atc, $class)
	{
		$grps = $atc->meta("folder_structure");
		$behaviour = $atc->prop("behaviour");
		$default = add_tree_conf_obj::BEHAVIOUR_PERMISSIVE === $behaviour;

		if (!is_array($grps))
		{
			$grps = aw_ini_get("classfolders");
		}

		$us = $atc->meta("usable");

		if (is_class_id($class) or in_array($class, self::$system_classes))
		{
			$class_id = $class;
		}
		else
		{
			try
			{
				$class_id = aw_ini_get("class_lut.{$class}");
			}
			catch (awex_cfg_key $e)
			{
				$class_id = null;
			}
		}

		$ret = isset($us[$class_id]) ? (bool) $us[$class_id] : $default;
		if ($class_id != CL_MENU && $ret)
		{
			$v = $atc->meta("visible");
			// also, if the class is in some groups and for all those groups access has been turned off
			// do not show the alias

			if (!isset($clss[$class_id]["parents"]))
			{
				$grp = array();
				$show = true;
			}
			else
			{
				$grp = explode(",", $clss[$class_id]["parents"]);
				$show = false;
			}

			foreach($grp as $g)
			{
				// must check group parents as well :(
				$has_grp = !empty($v["fld"][$g]);
				if ($has_grp)
				{
					while ($g)
					{
						if (!$v["fld"][$g])
						{
							$has_grp = false;
							break;
						}
						$g = $grps[$g]["parent"];
					}
				}

				if ($has_grp)
				{
					$show = true;
				}
			}

			if (!$show)
			{
				$ret = false;
			}
		}
		return $ret;
	}

	/** checks for accessibility of several classes at once
		@attrib api=1 params=pos

		@param atc required type=object
			The add tree conf object to use

		@param classes required type=array
			The list of classes to check Array { key => array { class_id => temp } }

		@param for_aliasmgr optional type=bool
			If set to true, alias access is checked. if false, class access. defaults to false

		@returns
			classes array with unusable classes removed
	**/
	function can_access_classes($atc, $classes, $for_aliasmgr = false)
	{
		$grps = $atc->meta("folder_structure");
		if (!is_array($grps))
		{
			$grps = aw_ini_get("classfolders");
		}
		if ($for_aliasmgr)
		{
			$us = $atc->meta("alias_add");
		}
		else
		{
			$us = $atc->meta("usable");
		}
		$v = $atc->meta("visible");
		$clss = $atc->meta("class_structure");
		if (!is_array($clss))
		{
			$clss = aw_ini_get("classes");
		}
		$tmp = array();
		foreach($classes as $key => $val)
		{
			$tmp[$key] = array();
			foreach($val as $class => $vadeva)
			{
				if(!isset($us[$class]) || $us[$class] != 1)
				{
					continue;
				}
				if ($class != CL_MENU)
				{
					// also, if the class is in some groups and for all those groups access has been turned off
					// do not show the alias
					$grp = explode(",", ifset($clss, $class, "parents"));
					$show = false;
					foreach($grp as $g)
					{
						// must check group parents as well :(
						$has_grp = !empty($v["fld"][$g]);
						if ($has_grp)
						{
							while ($g)
							{
								if (empty($v["fld"][$g]))
								{
									$has_grp = false;
									break;
								}
								$g = $grps[$g]["parent"];
							}
						}
						if ($has_grp)
						{
							$show = true;
						}
					}
					if(!$show)
					{
						continue;
					}
				}
				$tmp[$key][$class] = $vadeva;
			}
		}
		return $tmp;
	}

	function on_site_init($dbi, $site, &$ini_opts, &$log, &$osi_vars)
	{
		$o = obj($osi_vars["add_tree_conf"]);
		$this->adc_set_all($o);

		$clss = aw_ini_get("classes");

		//  Dokumendi seostehalduris kuvatakse vaikimisi j2rgmisi objekte:
		$alias_addable = array(CL_EXTLINK, CL_FILE, CL_IMAGE, CL_LAYOUT, CL_WEBFORM, CL_MINI_GALLERY, CL_DOCUMENT, CL_MENU_TREE, CL_ML_LIST, CL_PROMO, CL_CFGFORM);

		foreach($clss as $clid => $cld)
		{
			$this->adc_set_class($o, $clid, true, true, in_array($clid, $alias_addable));
		}

		// Kohe tuleb v2lja j2tta j2rgmiste programmide kasutamise v6imalus:

		// Otsingud > Saidi otsing (vist mingi vana objekt)
		$this->adc_set_class($o, CL_SITE_SEARCH, false, false, false);

		// Varia & Vanad > Dokument(p), Foorum (vana), Stamp, Mailinglisti seaded
		$this->adc_set_class($o, CL_PERIODIC_SECTION, false, false, false);
		$this->adc_set_class($o, CL_FORUM, false, false, false);
		$this->adc_set_class($o, CL_ML_STAMP, false, false, false);
		$this->adc_set_class($o, CL_ML_LIST_CONF, false, false, false);

		// Systeemi haldus > T88solevad klassid
		$this->adc_set_fld($o, 19, false);

		// Systeemi haldus > Varia & Vanad
		$this->adc_set_fld($o, 4, false);

		$o->save();

		echo "saved add tree conf! <br>\n";
		flush();

		aw_disable_messages();
		// seostada Administraatorid ja Toimetajad grupiga
		$adm_g = obj($osi_vars["groups.admins"]);
		$adm_g->connect(array(
			"to" => $o->id(),
			"reltype" => 5 // RELTYPE_ADD_TREE
		));

		$ed_g = obj($osi_vars["groups.editors"]);
		$ed_g->connect(array(
			"to" => $o->id(),
			"reltype" => 5 // RELTYPE_ADD_TREE
		));
		aw_restore_messages();
	}

	/** Sets all access to all classes
		@attrib api=1 params=pos

		@param o required type=cl_add_tree_conf
			The add tree conf object to modify

		@comment
			does not save the atc object, so you need to do that
	**/
	function adc_set_all($o)
	{
		$visible = array();
		$usable = array();
		$alias_add = array();

		$clsf = aw_ini_get("classfolders");
		foreach($clsf as $id => $d)
		{
			$visible["fld"][$id] = 1;
		}
		$tmp = aw_ini_get("classes");
		foreach($tmp as $id => $d)
		{
			$visible["obj"][$id] = 1;
			$usable[$id] = 1;
			if ($d["alias"] != "")
			{
				$alias_add[$id] = 1;
			}
		}

		$o->set_meta("visible", $visible);
		$o->set_meta("usable", $usable);
		$o->set_meta("alias_add", $alias_add);
	}

	/** sets access to a single class
		@attrib api=1 params=pos

		@param o required type=cl_add_tree_conf
			the atc object to use

		@param clid required type=clid
			the class_id for the class to modify access to

		@param visible required type=bool
			If the class is visible

		@param usable required type=bool
			If the class can be used

		@param alias_add required type=bool
			If the class can be added as an alias

	**/
	function adc_set_class($o, $clid, $visible, $usable, $alias_add)
	{
		$v = $o->meta("visible");
		$u = $o->meta("usable");
		$a = $o->meta("alias_add");

		if (!$visible)
		{
			unset($v["obj"][$clid]);
		}
		else
		{
			$v["obj"][$clid] = $visible;
		}
		if (!$usable)
		{
			unset($u[$clid]);
		}
		else
		{
			$u[$clid] = $usable;
		}
		if (!$alias_add)
		{
			unset($a[$clid]);
		}
		else
		{
			$a[$clid] = $alias_add;
		}

		$o->set_meta("visible", $v);
		$o->set_meta("usable", $u);
		$o->set_meta("alias_add", $a);
	}

	/** Sets access to a class folder
		@attrib api=1 params=pos

		@param o required type=cl_add_tree_conf
			The atc object to modify

		@param fld required type=int
			The classfolder id to set access for

		@param visible required type=bool
			If the folder is visible

	**/
	function adc_set_fld($o, $fld, $visible)
	{
		$v = $o->meta("visible");
		if (!$visible)
		{
			unset($v["fld"][$fld]);
		}
		else
		{
			$v["fld"][$fld] = $visible;
		}
		$o->set_meta("visible", $v);
	}

	/** returns a tree of classes that can be used to construct the add menu
		@attrib api=1 params=name

		@param az optional type=bool
			If set to true, classes a-z item is added with all classes

		@param docforms optional type=bool
			If set to true, document config forms are added to the tree as addable documents

		@returns
			array{ parent => array {class_id => class_entry} } for all folders and classes that can be used
	**/
	function get_class_tree($arr = array())
	{
		$conf_obj_id = $this->get_current_conf();
		$this->conf_exists = false;

		$clss = aw_ini_get("classes");
		if ($conf_obj_id)
		{
			$conf_obj = new object($conf_obj_id);
			$this->visible = $conf_obj->meta("visible");
			$this->usable = $conf_obj->meta("usable");
			$this->conf_exists = true;
		}
		else
		{
			// if no config is available, then assume that all
			// defined classes are visible and usable
			$this->visible["obj"] = $clss;
			$this->visible["fld"] = aw_ini_get("classfolders");
			$this->usable = $clss;
		}

		$collect_az = false;
		if (isset($arr["az"]))
		{
			$collect_az = true;
			$az_classes = array();
		}

		$by_parent = array();

		foreach($clss as $clid => $cldata)
		{
			if (!isset($cldata["parents"]))
			{
				continue;
			};

			if (empty($cldata["can_add"]))
			{
				continue;
			};

			$parens = explode(",", $cldata["parents"]);
			if (!empty($this->visible["obj"][$clid]))
			{
				if (!empty($this->usable[$clid]))
				{
					foreach($parens as $paren)
					{
						if (0 == $paren)
						{
							$paren = "root";
						}
						else
						{
							$paren = "fld_" . $paren;
						};
						$cldat = $clss[$clid];
						$cldat["clid"] = $clid;
						$cldat["id"] = $clid;
						$by_parent[$paren][] = $cldat;

						if ($collect_az)
						{
							$letter = $cldat["name"]{0};
							$cldat["parent"] = "letter_" . $letter;
							$az_classes[$letter][] = $cldat;
						}
					}
				}
			}
		}

		$folders = aw_ini_get("classfolders");

		foreach($folders as $folder_id => $folder_data)
		{
			if (empty($this->visible["fld"][$folder_id]))
			{
				continue;
			}
			$parent = "root";
			if (!empty($folder_data["parent"]))
			{
				$parent = "fld_" . $folder_data["parent"];
			};
			$folder_data["id"] = "fld_" . $folder_id;
			$by_parent[$parent][] = $folder_data;

			if (isset($folder_data["all_objs"]) && $collect_az)
			{
				ksort($az_classes);
				foreach($az_classes as $az_key => $az_val)
				{
					$by_parent[$folder_data["id"]][] = array(
						"name" => $az_key,
						"id" => "letter_" . $az_key,
					);
					uasort($az_val, create_function('$a,$b', 'return strcasecmp($a["name"], $b["name"]);'));
					$by_parent["letter_".$az_key] = $az_val;

				}
			}

			if (isset($arr["docforms"]) && isset($folder_data["docforms"]))
			{
				$d = get_instance("doc");
                        	$docmenu = $d->get_doc_add_menu($arr["parent"],$arr["period"]);

				$by_parent[$folder_data["id"]] = $docmenu;
			}
		}

		return $by_parent;
	}
}
