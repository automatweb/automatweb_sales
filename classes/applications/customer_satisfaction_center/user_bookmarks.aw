<?php

// user_bookmarks.aw - Kasutaja j&auml;rjehoidjad
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@tableinfo user_bookmarks index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

	@property sharing type=checkbox ch_value=1 field=sharing table=user_bookmarks
	@caption J&auml;rjehoidjate jagamine

	@property shared_show type=select field=meta method=serialize
	@caption Jagatud j&auml;rjehoidjaid n&auml;idatakse

@default group=bms

	@property bm_tb type=toolbar store=no no_caption=1

	@layout bm_tt type=hbox width=30%:70%

		@layout bm_tree type=vbox parent=bm_tt closeable=1 area_caption=Puu

			@property bm_tree type=treeview store=no no_caption=1 parent=bm_tree

		@property bm_table type=table store=no no_caption=1 parent=bm_tt

@default group=shared

	@property shared_tb type=toolbar store=no no_caption=1

	@layout shared_tt type=hbox width=30%:70%

		@layout shared_tree type=vbox parent=shared_tt closeable=1 area_caption=Puu

			@property shared_tree type=treeview store=no no_caption=1 parent=shared_tree

		@property shared_table type=table store=no no_caption=1 parent=shared_tt

@groupinfo bms caption="J&auml;rjehoidja" submit=no
@groupinfo shared caption="Jagatud j&auml;rjehoidjad" submit=no



@groupinfo apps caption="Rakendusmen&uuml;&uuml;" submit=no
@default group=apps

	@property apps_tb type=toolbar store=no no_caption=1
	@property apps_table type=table store=no no_caption=1

@reltype SHOW_SHARED value=1 clid=CL_EXTLINK,CL_MENU,CL_USER_BOOKMARKS
@caption Jagatud j&auml;rjehoidja
*/

class user_bookmarks extends class_base
{
	const CACHE_KEY_PREFIX_HTML = "aw_bookmarks_menuhtml_";
	const CACHE_KEY_PREFIX_APP_MENU = "aw_bookmarks_appmenuhtml_";

	function user_bookmarks()
	{
		$this->init(array(
			"tpldir" => "applications/customer_satisfaction_center/user_bookmarks",
			"clid" => CL_USER_BOOKMARKS
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "bm_tb":
				$this->_bm_tb($arr);
				break;

			case "bm_tree":
				$this->_bm_tree($arr);
				break;

			case "bm_table":
				$this->_bm_table($arr);
				$this->clear_cache($arr["obj_inst"]);
				break;

			case "shared_tb":
				$this->_shared_tb($arr);
				break;

			case "shared_table":
				$this->_shared_table($arr);
				$this->clear_cache($arr["obj_inst"]);
				break;

			case "shared_tree":
				$this->_shared_tree($arr);
				break;

			case "shared_show":
				$prop["options"] = array(
					0 => t("&Uuml;ksteise all"),
					1 => t("Kasutajate kaupa"),
				);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["post_ru"] = post_ru();
		$arr["objs"] = 0;
		$arr["tf"] = isset($request["tf"]) ? $request["tf"] : "";
		$arr["user"] = isset($request["user"]) ? $request["user"] : "";
		if($this->use_group === "bms")
		{
			$pt = isset($request["tf"]) ? $request["tf"] : $arr["id"];
			$ol = new object_list(array(
				"parent" => $pt,
				"sort_by" => "objects.class_id asc, objects.name asc"
			));
			foreach($ol->arr() as $o)
			{
				$arr["groups".$o->id()] = 0;
			}
		}
	}

	function callback_mod_retval(&$arr)
	{
		if (isset($arr["request"]["tf"])) $arr["args"]["tf"] = $arr["request"]["tf"];
	}

	function init_bm()
	{
		$ol = new object_list(array(
			"class_id" => CL_USER_BOOKMARKS,
			"createdby" => aw_global_get("uid")
		));

		if (!$ol->count())
		{
			$o = obj();
			$o->set_class_id(CL_USER_BOOKMARKS);
			$o->set_parent(aw_ini_get("amenustart"));
			$p = get_current_person();
			$o->set_name(sprintf(t("%s j&auml;rjehoidja"), ($p ? $p->name() : aw_global_get("uid"))));
			$o->save();
			return $o;
		}
		else
		{
			return $ol->begin();
		}
	}

	function _bm_tb($arr)
	{
		$pt = isset($arr["request"]["tf"]) ? $arr["request"]["tf"] : $arr["obj_inst"]->id();
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Uus"),
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Kataloog"),
			"link" => html::get_new_url(CL_MENU, $pt, array("return_url" => get_ru()))
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Link"),
			"link" => html::get_new_url(CL_EXTLINK, $pt, array("return_url" => get_ru()))
		));

		$ps = new popup_search();
		$tb->add_cdata($ps->get_popup_search_link(array(
			"pn" => "objs",
		)));

		$tb->add_button(array(
			"name" => "saveb",
			"action" => "save_bms",
			"img" => "save.gif",
			"tooltip" => t("Salvesta")
		));
		$tb->add_button(array(
			"name" => "delb",
			"action" => "delete_bms",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta")
		));

		$tb->add_button(array(
			"name" => "copy_bms",
			"action" => "copy_bms",
			"img" => "copy.gif",
			"tooltip" => t("Kopeeri")
		));

		$tb->add_button(array(
			"name" => "cut_bms",
			"action" => "cut_bms",
			"img" => "cut.gif",
			"tooltip" => t("L&otilde;ika")
		));

		if ( is_array(aw_global_get('copy_bookmarks')) || is_array(aw_global_get('cut_bookmarks')) )
		{
			$tb->add_button(array(
				"name" => "paste_bms",
				"action" => "paste_bms",
				"img" => "paste.gif",
				"tooltip" => t("Kleebi")
			));
		}
	}


	function _get_apps_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_save_button();

		$tb->add_delete_button();
	}

	function _get_apps_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "icon",
			"caption" => t("Ikoon"),
			"width" => "5%",
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Objekti nimi"),
			"align" => "left"
		));
		$t->define_field(array(
			"name" => "class",
			"caption" => t("Klassi nimi"),
			"align" => "left"
		));

		$t->define_field(array(
			"name" => "show_group",
			"caption" => t("Grupp"),
			"align" => "left"
		));
		$t->define_field(array(
			"name" => "link_text_type",
			"caption" => t("Lingi tekst"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "link_text",
			"caption" => t("Kirjutatud tekst"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "group_text",
			"caption" => t("Grupi kirjutatud tekst"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "group",
			"caption" => t("Grupeeri"),
			"align" => "center",
			"width" => 15
		));

		$t->define_field(array(
			"name" => "show_groups",
			"caption" => '<a href="#" alt="'.t("Kuva koos omaduste gruppidega").'">KKOG</a>',
//			"caption" => t("Kuva koos omaduste gruppidega"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "force",
			"caption" => t("Kohustuslik"),
			"align" => "center"
		));
/*		$t->define_field(array(
			"name" => "force_all",
			"parent" => "force",
			"caption" => t("K&otilde;igile"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "force_site",
			"parent" => "force",
			"caption" => t("Saidile"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "force_company",
			"parent" => "force",
			"caption" => t("Organisatsioonile"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "force_unit",
			"parent" => "force",
			"caption" => t("&Uuml;ksusele"),
			"align" => "center"
		));
*/
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"width" => "5%"
		));

		$link_text_types = array("Objekti nimi" , "Klassi nimi" , "Omaduste grupi nimi", "Kirjutatud string");
		$cfg = new cfgutils();

		$bm = $this->init_bm();
		$ol = new object_list(array(
			"parent" => $bm->id(),
			"class_id" => CL_USER_BOOKMARK_ITEM,
			"sort_by" => "objects.jrk"
		));

		$force_opts = array(
			"" ,
			"1" => t("K&otilde;igile"),
		);

		$co = get_current_company();
		if($co)
		{
			$force_opts[$co->id()] = t("Organisatsioonile");
		}
		$person = get_current_person();
		if($person)
		{
			$section = $person->get_org_section();
			if($section)
			{
				$force_opts[$section] = t("&Uuml;ksusele");
			}
		}

		foreach($ol->arr() as $o)
		{
			if(!acl_base::can("view", $o->prop("obj")))
			{
				continue;
			}
			$obj = obj($o->prop("obj"));
			if(empty($GLOBALS["cfg"]["classes"][$obj->class_id()]))
			{
				continue;
			}


			$uprops = $cfg->load_properties(array(
				"clid" => $obj->class_id(),
			));
			$groups = $cfg->get_groupinfo();
			$gopts = array();
			foreach($groups as $gid => $group)
			{
	//			if(empty($group["parent"]))
	//			{
					$gopts[$gid] = $group["caption"];
	//			}
			}

			$t->define_data(array(
				"name" => html::obj_change_url($obj),//$obj->name(),
				"link_text_type" => html::select(array(
					"value" => $o->prop("link_text_type"),
					"options" => $link_text_types,
					"name" => "app_bookmark[".$o->id()."][link_text_type]"
				)),
				"show_group" => html::select(array(
					"value" => $o->prop("show_group"),
					"options" => $gopts,
					"name" => "app_bookmark[".$o->id()."][show_group]"
				)),
				"show_groups" => html::checkbox(array(
					"checked" => $o->prop("show_groups"),
					"name" => "app_bookmark[".$o->id()."][show_groups]"
				)),
				"group" => html::checkbox(array(
					"checked" => $o->prop("group"),
					"name" => "app_bookmark[".$o->id()."][group]"
				)),
				"link_text" => html::textbox(array(
					"value" => $o->prop("link_text"),
					"name" => "app_bookmark[".$o->id()."][link_text]",
					"size" => 15
				)),
				"group_text" => html::textbox(array(
					"value" => $o->prop("group_text"),
					"name" => "app_bookmark[".$o->id()."][group_text]",
					"size" => 15
				)),
				"force" => html::select(array(
					"value" => $o->prop("force"),
					"options" => $force_opts,
					"name" => "app_bookmark[".$o->id()."][force]"
				)),
				"class" => $GLOBALS["cfg"]["classes"][$obj->class_id()]["name"],


/*				"force_all" => html::checkbox(array(
					"checked" => $o->prop("force_all"),
					"name" => "app_bookmark[".$o->id()."][force_all]"
				)),

				"force_site" => html::select(array(
					"value" => $o->prop("force_site"),
//					"options" => $gopts,
					"name" => "app_bookmark[".$o->id()."][force_site]"
				)),
				"force_company" => html::checkbox(array(
					"checked" => $o->prop("force_company"),
					"name" => "app_bookmark[".$o->id()."][force_company]"
				)),
				"force_unit" => html::checkbox(array(
					"checked" => $o->prop("force_unit"),
					"name" => "app_bookmark[".$o->id()."][force_unit]"
				)),*/

				"ord" => html::textbox(array(
					"value" => $o->ord(),
					"name" => "app_bookmark[".$o->id()."][ord]",
					"size" => 4
				)),
				"icon" => html::img(array(
					"url" => icons::get_icon_url($obj->class_id()),
				)),
				"oid" => $o->id(),
			));
		}
	}

	function get_icon($clid)
	{
		$url = $GLOBALS["cfg"]["icons"]["server"]."32/";
		$dir = $GLOBALS["aw_dir"]."automatweb/images/icons/32/";
		if($clid)
		{
			if(file_exists($dir.$clid.".png"))
			{
				$main_icon = $url.$clid.".png";
			}
			else
			{
				$main_icon = $url."default.png";
			}
		}
		else
		{
			$main_icon = $url."default.png";
		}
		return $main_icon;
	}

	function get_application_links()
	{
		$application_links = "";
		$bmobj = $this->init_bm();
		$app_menu = cache::file_get(self::CACHE_KEY_PREFIX_APP_MENU . $bmobj->id());
		if (false && !empty($app_menu))
		{
			return $app_menu;
		}

		$ol = new object_list(array(
			"parent" => $bmobj->id(),
			"class_id" => CL_USER_BOOKMARK_ITEM,
			"sort_by" => "objects.jrk"
		));
		$tmp = aw_ini_get("classes");

		$force_opts = array("1");

		$co = get_current_company();
		if($co)
		{
			$force_opts[] = $co->id();
		}
		$person = get_current_person();
		if($person)
		{
			$section = $person->get_org_section();
			if($section)
			{
				$force_opts[] = $section;
			}
		}

		$ol2 = new object_list(array(
			"force" => $force_opts,
			"class_id" => CL_USER_BOOKMARK_ITEM,
			"sort_by" => "objects.jrk"
		));

		$ol->add($ol2);

		$cfg = new cfgutils();

		$apps = array();

		foreach($ol->arr() as $o)
		{
			if(!acl_base::can("view", $o->prop("obj")))
			{
				continue;
			}
			$obj = obj($o->prop("obj"));
			if(empty($tmp[$obj->class_id()]))
			{
				continue;
			}

			if($o->prop("group"))
			{
				$key = $obj->class_id();
			}
			else
			{
				$key = $obj->class_id()."_".$obj->id();
			}
			if(empty($apps[$key]))
			{
				$apps[$key] = array();
			}

			$nm = "";
			switch($o->prop("link_text_type"))
			{
				case "3":
					$nm = $o->prop("link_text");
					break;
				case "2":
					$uprops = $cfg->load_properties(array(
						"clid" => $obj->class_id(),
					));
					$groups = $cfg->get_groupinfo();
					$nm = $groups[$o->prop("show_group")]["caption"];
					break;
				case "1":
					$nm = $GLOBALS["cfg"]["classes"][$obj->class_id()]["name"];
					break;
				default:
					$nm = $obj->name();
			}

			$url = $o->prop("url");
			if($o->prop("show_group"))
			{
				$url = aw_url_change_var("group" ,$o->prop("show_group") , $url);
			}

			$apps[$key][$obj->id()] = array(
				"url" => $url,
				"name" => $nm,
                "class_id" => $obj->class_id(),
				"show_groups" => $o->prop("show_groups"),
				"group_text" => $o->prop("group_text"),
				"id" => $obj->id()
			);
		}

/*		$apps = safe_array($bmobj->meta("apps"));
arr($apps);*/
		foreach($apps as $key => $app)
		{
			$k = explode("_" , $key);
			$key = $k[0];
			if(empty($tmp[$key]))
			{
				continue;
			}
			$ico = '<span class="icon">'.html::img(array(
				"url" => $this->get_icon($key),
			)).'</span>';

			$uprops = $cfg->load_properties(array(
				"clid" => $key,
			));

			$groups = $cfg->get_groupinfo();
			$gopts = array();
			foreach($groups as $gid => $group)
			{
//				if(empty($group["parent"]))
//				{
					$gopts[$gid] = $group;
//				}
			}
			foreach($gopts as $gid => $group)
			{
				if(!empty($group["parent"]))
				{
					if(empty($gopts[$group["parent"]]["subclasses"]))
					{
						$gopts[$group["parent"]]["subclasses"] = 1;
					}
					else
					{
						$gopts[$group["parent"]]["subclasses"]++;
					}
				}
			}


			if(is_array($app) && sizeof($app) > 1)
			{
				$am = new popup_menu();
				$am->begin_menu("user_applications_".$key);
				foreach($app as $k => $v)
				{
					if($v["show_groups"])
					{
						$params = array(
							"name" => $v["class_id"]."_".$v["id"],
							"text" => $v["name"]
						);

						$am->add_sub_menu($params);



						foreach($gopts as $gid => $group)
						{
							if(!empty($group["parent"]))
							{
								$am->add_item(array(
									"text" => $group["caption"],
									"link" => aw_url_change_var("group" ,$gid , $v["url"]),
									"parent" => $v["class_id"]."_".$v["id"]."_".$group["parent"]
								));
							}
							elseif(empty($group["subclasses"]))
							{
								$am->add_item(array(
									"text" => $group["caption"],
									"link" => aw_url_change_var("group" ,$k , $v["url"]),
									"parent" => $v["class_id"]."_".$v["id"]
								));
							}
							else
							{
								$params = array(
									"name" => $v["class_id"]."_".$v["id"]."_".$gid,
									"text" => empty($group["caption"]) ? "" : $group["caption"],
									"parent" => $v["class_id"]."_".$v["id"]
								);
								$am->add_sub_menu($params);
							}
						}
					}
					else
					{
						$am->add_item(array(
							"text" => empty($v["name"]) ? $k : $v["name"],
							"link" => $v["url"]
						));
					}
				}

				$gn = (empty($GLOBALS["cfg"]["classes"][$key]["plural"]) ? $GLOBALS["cfg"]["classes"][$key]["name"] : $GLOBALS["cfg"]["classes"][$key]["plural"]);

				if($v["group_text"])
				{
					$gn = $v["group_text"];
				}

				$application_links .= $this->__wrap_application_link($am->get_menu(array("text" =>$ico. $gn.(aw_template::bootstrap() ? "" : ('&nbsp;<img class="nool" alt="#" src="'.aw_ini_get("baseurl").'/automatweb/images/aw06/ikoon_nool_alla.gif">')))));
			}
			elseif(is_array($app) && sizeof($app) == 1)
			{
				$a = reset($app);
				if(!is_array($a)) continue;
				$application_links.= " ";

				if($a["show_groups"])
				{
					$am = new popup_menu();
					$am->begin_menu("user_applications_".$key."_".$a["id"]);
/*					foreach($gopts as $k => $v)
					{
						$am->add_item(array(
							"text" => $v["caption"],
							"link" => aw_url_change_var("group" ,$k , $a["url"])
						));
					}*/

					foreach($gopts as $gid => $group)
					{
						if(!empty($group["parent"]))
						{
							$am->add_item(array(
								"text" => $group["caption"],
								"link" => aw_url_change_var("group" ,$gid , $a["url"]),
								"parent" => $key."__".$group["parent"]
							));
						}
						elseif(empty($group["subclasses"]))
						{
							$am->add_item(array(
								"text" => $group["caption"],
								"link" => aw_url_change_var("group" ,$gid , $a["url"]),
					//			"parent" => $v["class_id"]."_".$v["id"]
							));
						}
						else
						{
							$params = array(
								"name" => $key."__".$gid,
								"text" => isset($group["caption"]) ? $group["caption"] : null,
						//		"parent" => $v["class_id"]."_".$v["id"]
							);
							$am->add_sub_menu($params);
						}
					}

					$application_links.= $this->__wrap_application_link($am->get_menu(array("text" => $ico.$a["name"].(aw_template::bootstrap() ? "" : ('&nbsp;<img class="nool" alt="#" src="'.aw_ini_get("baseurl").'/automatweb/images/aw06/ikoon_nool_alla.gif">')))));
				}
				elseif (aw_template::bootstrap())
				{
					$caption = $ico.(empty($a["name"]) ? $key : $a["name"]);
					$application_links .= "<div class=\"btn-group\"><a href=\"{$a["url"]}\" class=\"btn btn-mini\"><span>{$caption}</span></a></div>";
				}
				else
				{
					$application_links.= $this->__wrap_application_link('
						<span style="height:15px;text-align: center; background-color: transparent; " id="menuBar">
						<a id="href_user_applications_1134" title="" alt=""  href="'.$a["url"].'" class="menuButton">
						<span>'.$ico.(empty($a["name"]) ? $key : $a["name"]).'</span>
						</a>
						</span>
					');
				}
			}
		}

		cache::file_set(self::CACHE_KEY_PREFIX_APP_MENU . $bmobj->id(), $application_links);

		return $application_links;
	}
	
	private function __wrap_application_link ($link)
	{
		return aw_template::bootstrap() ? $link : "<div class=\"oneappmenu\">{$link}</div>";
	}

	function _set_apps_table($arr)
	{
	//	arr($arr);die();
		$co_id = 1;
		$unit_id = 1;
		if(!empty($arr["request"]["app_bookmark"]))
		{
			foreach($arr["request"]["app_bookmark"] as $id => $data)
			{
				$o = obj($id);
				$o->set_ord($data["ord"]);
				$o->set_prop("show_group" , $data["show_group"]);
				$o->set_prop("link_text_type" , $data["link_text_type"]);
				$o->set_prop("link_text" , $data["link_text"]);
				$o->set_prop("group_text" , $data["group_text"]);
				$o->set_prop("show_groups" , empty($data["show_groups"]) ? 0: 1);
				$o->set_prop("group" , empty($data["group"]) ? 0:1 );
				$o->set_prop("force" , $data["force"] );
/*				$o->set_prop("force_all" , empty($data["force_all"]) ? 0:1 );
				$o->set_prop("force_site" , $data["force_site"] );
				$o->set_prop("force_unit" , empty($data["force_unit"]) ? 0:$co_id );
				$o->set_prop("force_company" , empty($data["force_cmpany"]) ? 0:$unit_id );*/
				$o->save();
			}
		}
		cache::file_invalidate(self::CACHE_KEY_PREFIX_APP_MENU . $arr["request"]["id"]);
	}

	/**
		@attrib name=save_bms
	**/
	function save_bms($arr)
	{
		$o = obj($arr["id"]);
		$mt = $o->meta("grp_sets");
		if(count($arr["share"]) && !$o->prop("sharing"))
		{
			$o->set_prop("sharing", 1);
			$o->save();
		}
		$shared = $o->meta("shared");
		if($arr["tf"] && $arr["tf"] != $o->id())
		{
			$parent = $arr["tf"];
		}
		else
		{
			$parent = $o->id();
		}
		$ol = new object_list(array(
			"class_id" => array(CL_MENU, CL_EXTLINK),
			"parent" => $parent
		));
		foreach($ol->arr() as $ob)
		{
			if($arr["share"][$ob->id()])
			{
				$shared[$ob->id()] = $ob->id();
				if($ob->class_id() == CL_MENU)
				{
					$this->recur_share_menu($ob->id(), $shared, 1);
				}
			}
			else
			{
				unset($shared[$ob->id()]);
				if($ob->class_id() == CL_MENU)
				{
					$this->recur_share_menu($ob->id(), $shared, 0);
				}
			}
		}
		$o->set_meta("shared", $shared);
		foreach(safe_array($arr["grps"]) as $oid => $gp)
		{
			$mt[$oid] = $gp;
		}
		foreach(safe_array($arr["dat"]) as $oid => $com)
		{
			$do = obj($oid);
			$mod = false;
			if ($com["ord"] != $do->ord())
			{
				$do->set_ord($com["ord"]);
				$mod = true;
			}
			if ($do->comment() != $com["comment"])
			{
				$do->set_meta("user_text", $com["comment"]);
				$mod = true;
			}

			if ($mod)
			{
				$do->save();
			}
		}
		$o->set_meta("grp_sets", $mt);
		$o->save();
		return $arr["post_ru"];
	}

	function recur_share_menu($oid, &$shared, $set)
	{
		$ol = new object_list(array(
			"parent" => $oid,
			"class_id" => array(CL_MENU, CL_EXTLINK)
		));
		foreach($ol->arr() as $o)
		{
			if($set)
			{
				$shared[$o->id()] = $o->id();
			}
			else
			{
				unset($shared[$o->id()]);
			}
			if($o->class_id() == CL_MENU)
			{
				$this->recur_share_menu($o->id(), $shared, $set);
			}
		}
	}

	/**
		@attrib name=delete_bms
	**/
	function delete_bms($arr)
	{
		object_list::iterate_list($arr["sel"], "delete");
		return $arr["post_ru"];
	}

	function _bm_tree($arr)
	{
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"persist_state" => true,
				"tree_id" => "user_bm",
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"parent" => $arr["obj_inst"]->id(),
				"class_id" => CL_MENU
			)),
			"var" => "tf",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	/**
		@attrib name=copy_bms
	**/
	function copy_bms($arr)
	{
		$_SESSION['copy_bookmarks'] = $arr['sel'];
		return $arr['post_ru'];
	}

	/**
		@attrib name=cut_bms
	**/
	function cut_bms($arr)
	{
		$_SESSION['cut_bookmarks'] = $arr['sel'];
		return $arr['post_ru'];
	}

	/**
		@attrib name=paste_bms
	**/
	function paste_bms($arr)
	{
		$cut_bookmarks = aw_global_get( 'cut_bookmarks' );
		foreach ( safe_array($cut_bookmarks) as $oid )
		{
			if ( $this->can('edit', $oid) )
			{
				$o = new object($oid);
				// the object cannot be copied under itself
				if ( $o->id() != $arr['tf'] )
				{
					$o->set_parent($arr['tf']);
					$o->save();
				}
			}
		}
		unset($_SESSION['cut_bookmarks']);

		$copy_bookmarks = aw_global_get( 'copy_bookmarks' );
		foreach ( safe_array($copy_bookmarks) as $oid )
		{
			if ( $this->can('view', $oid) )
			{
				$o = new object($oid);
				$new_oid = $o->save_new();
				$new_o = new object($new_oid);
				$new_o->set_parent($arr['tf']);
				$new_o->save();
			}
		}
		unset($_SESSION['copy_bookmarks']);

		return $arr['post_ru'];
	}

	function _init_bm_table($t)
	{
		$t->define_field(array(
			"name" => "share",
			"caption" => t("Jaga"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "icon",
			"caption" => t("Ikoon"),
			"width" => "5%",
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Objekti nimi"),
			"align" => "center"
		));
/*		$t->define_field(array(
			"name" => "link",
			"caption" => t("Link"),
			"align" => "center"
		));*/
		$t->define_field(array(
			"name" => "group",
			"caption" => t("Grupp"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "groups",
			"caption" => t("Tee kohustuslikuks"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "user_text",
			"caption" => t("Kasutaja tekst"),
			"align" => "center",
			"width" => 15
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"width" => "5%"
		));
	}

	function _bm_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$this->_init_bm_table($t);

		$pt = isset($arr["request"]["tf"]) ? $arr["request"]["tf"] : $arr["obj_inst"]->id();
		$ol = new object_list(array(
			"parent" => $pt,
			"sort_by" => "objects.class_id asc, objects.name asc"
		));
		$mt = $arr["obj_inst"]->meta("grp_sets");
		$shared = $arr["obj_inst"]->meta("shared");
		foreach($ol->arr() as $o)
		{
			if($o->class_id() == CL_USER_BOOKMARK_ITEM)
			{
				continue;
			}
			$link = "";
			$grp = "";
			if ($o->class_id() == CL_EXTLINK)
			{
				$link = $o->prop("url");
			}
			else
			if ($o->class_id() != CL_MENU)
			{
				$tmp = obj();
				$tmp->set_class_id($o->class_id());
				$gl = $tmp->get_group_list();
				$inf = array();
				foreach($gl as $nm => $dat)
				{
					$inf[$nm] = (!empty($dat["parent"]) ? "&nbsp;&nbsp;&nbsp;&nbsp;" : "").$dat["caption"];
				}
				$grp = html::select(array(
					"options" => $inf,
					"name" => "grps[".$o->id()."]",
					"value" => $mt[$o->id()]
				));
			}
			$url = $this->mk_my_orb("do_search", array(
				"pn" => "groups".$o->id(),
				"clid" => array(
					CL_GROUP
				),"multiple"=>1,
			),"popup_search");
			$url = "javascript:aw_popup_scroll(\"".$url."\",\"".t("Otsi")."\",550,500)";
			$grps = html::href(array(
				"caption" => html::img(array(
					"url" => "images/icons/search.gif",
					"border" => 0
				)),
				"url" => $url
			));
			$c = new connection();
			$conn = $c->find(array(
				"to"=>$o->id(),
				"from.class_id" => CL_GROUP,
			));

			if(count($conn))
			{
				$popup_menu = new popup_menu();
				$popup_menu->begin_menu("grp".$o->id());
				foreach($conn as $con)
				{
					$go = obj($con["from"]);
					$popup_menu->add_item(array(
						"text" => $go->name(),
						"link" => $this->mk_my_orb("rem_grp_rels", array(
							"obj" => $o->id(),
							"grp" => $go->id(),
							"ru" => get_ru(),
						)),
					));
				}
				$grps .= " ".$popup_menu->get_menu(array(
					"icon" => "delete.gif",
				));
			}

			$t->define_data(array(
				"icon" => html::img(array(
					'url' => icons::get_icon_url($o->class_id())
				)),
				"share" => html::checkbox(array(
					"name" => "share[".$o->id()."]",
					"value" => 1,
					"checked" => !empty($shared[$o->id()]),
				)),
				"name" => html::obj_change_url($o),
				"oid" => $o->id(),
				"link" => $link,
				"group" => $grp,
				"groups" => $grps,
				"user_text" => html::textbox(array(
					"name" => "dat[".$o->id()."][comment]",
					"value" => $o->meta("user_text"),
					"size" => 15
				)),
				"ord" => html::textbox(array(
					"name" => "dat[".$o->id()."][ord]",
					"size" => 5,
					"value" => $o->ord()
				)),
			));
		}

		$ol = new object_list(array(
			"parent" => $pt,
			"sort_by" => "objects.class_id asc, objects.name asc",
			"class_id" => CL_USER_BOOKMARK_ITEM
		));

	}

	/**
	@attrib name=rem_grp_rels all_args=1
	**/
	function rem_grp_rels($arr)
	{
		if(is_oid($arr["grp"]))
		{
			$grp = obj($arr["grp"]);
			$grp->disconnect(array(
				"from" => $arr["obj"],
			));
		}
		return $arr["ru"];
	}

	function _set_bm_table($arr)
	{
		$pt = $arr["request"]["tf"] ? $arr["request"]["tf"] : $arr["obj_inst"]->id();
		$ol = new object_list(array(
			"parent" => $pt,
			"sort_by" => "objects.class_id asc, objects.name asc"
		));
		foreach($ol->arr() as $o)
		{
			if($grps = $arr["request"]["groups".$o->id()])
			{
				$grpa = explode(",", $grps);
				foreach($grpa as $grp)
				{
					$go = obj($grp);
					$go->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_BOOKMARK",
					));
				}
			}
		}
	}

	function _shared_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "saveb",
			"action" => "save_show_shared",
			"img" => "save.gif",
			"tooltip" => t("Salvesta")
		));
	}

	function _shared_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"has_root" => 0,
			"tree_id" => "company_statuses",
			"persist_state" => 1,
		));
		$ol = new object_list(array(
			"class_id" => CL_USER_BOOKMARKS,
			"sharing" => 1,
		));
		$cur_u = aw_global_get("uid");
		foreach($ol->arr() as $bm)
		{
			if($bm->createdby() == $cur_u)
			{
				continue;
			}
			$shared = $bm->meta("shared");
			if(!count($shared))
			{
				continue;
			}
			$t->add_item(0, array(
				"id" => $bm->id(),
				"name" => $bm->createdby().t(" j&auml;rjehoidja"),
				"iconurl" => icons::get_icon_url(CL_MENU),
				"url" => aw_url_change_var(array(
					"tf"=> $bm->id(),
					"user" => $bm->id(),
				))
			));
			foreach($shared as $sh)
			{
				$o = obj($sh);
				if($o->class_id() == CL_MENU && $o->parent() == $bm->id())
				{
					$t->add_item($bm->id(), array(
						"id" => $o->id(),
						"name" => $o->name(),
						"iconurl" => icons::get_icon_url(CL_MENU),
						"url" => aw_url_change_var(array(
							"tf"=> $o->id(),
							"user" => $bm->id(),
						))
					));
					$this->_shared_tree_recur($t, $o->id(), $bm);
				}
			}
		}
	}

	function _shared_tree_recur($t, $parent, $bm)
	{
		$shared = $bm->meta("shared");
		if(count($shared))
		{
			$ol = new object_list(array(
				"oid" => $shared,
				"parent" => $parent,
				"class_id" => CL_MENU
			));
			foreach($ol->arr() as $o)
			{
				$t->add_item($parent, array(
					"id" => $o->id(),
					"name" => $o->name(),
					"iconurl" => icons::get_icon_url(CL_MENU),
					"url" => aw_url_change_var(array(
						"tf"=> $o->id(),
						"user" => $bm->id(),
					))
				));
				$this->_shared_tree_recur($t, $o->id(), $bm);
			}
		}
	}

	function _init_shared_table($t)
	{
		$t->define_field(array(
			"name" => "display",
			"caption" => t("Kuva endal"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "icon",
			"caption" => t("Ikoon"),
			"width" => "5%",
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Objekti nimi"),
			"align" => "center"
		));
/*		$t->define_field(array(
			"name" => "link",
			"caption" => t("Link"),
			"align" => "center"
		));*/
		$t->define_field(array(
			"name" => "user",
			"caption" => t("Kasutaja"),
			"align" => "center"
		));
	}

	function _shared_table($arr)
	{
		$pt = isset($arr["request"]["tf"]) ? $arr["request"]["tf"] : 0;
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_shared_table($t);
		$cur_u = aw_global_get("uid");
		$bm = isset($arr["request"]["user"])?obj($arr["request"]["user"]):0;
		if($cur_u != $arr["obj_inst"]->createdby())
		{
		}
		elseif($pt == 0)
		{
			$ol = new object_list(array(
				"class_id" => CL_USER_BOOKMARKS,
				"sharing" => 1,
			));
			foreach($ol->arr() as $bm)
			{
				if($bm->createdby() == $cur_u)
				{
					continue;
				}
				$shared = $bm->meta("shared");
				if(!count($shared))
				{
					continue;
				}
				$conn = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_SHOW_SHARED",
					"to.oid" => $bm->id(),
				));
				$checked = 0;
				if(count($conn))
				{
					$checked = 1;
				}
				$t->define_data(array(
					"display" => html::checkbox(array(
						"name" => "show_shared[".$bm->id()."]",
						"value" => 1,
						"checked" => $checked,
					)),
					"icon" => html::img(array(
						'url' => icons::get_icon_url($bm->class_id())
					)),
					"name" => html::obj_change_url($bm->id(),$bm->createdby().t(" j&auml;rjehoidjad")),
					"user" => $bm->createdby(),
				));
			}
		}
		elseif($pt > 0 && $bm && $bm->class_id() == CL_USER_BOOKMARKS && $bm->createdby != $cur_u)
		{
			$shared = $bm->meta("shared");
			if(count($shared))
			{
				$ol = new object_list(array(
					"oid" => $shared,
					"parent" => $pt,
					"class_id" => array(CL_MENU, CL_EXTLINK),
				));
				foreach($ol->arr() as $o)
				{
					foreach($o->path() as $po)
					{
						$pids[$po->id()] = $po->id();
					}
					$conn = $arr["obj_inst"]->connections_from(array(
						"type" => "RELTYPE_SHOW_SHARED",
						"to.oid" => $pids,
					));
					$checked = 0;
					if(count($conn))
					{
						$checked = 1;
					}
					$t->define_data(array(
						"display" => html::checkbox(array(
							"name" => "show_shared[".$o->id()."]",
							"value" => 1,
							"checked" => $checked,
						)),
						"icon" => html::img(array(
							'url' => icons::get_icon_url($o->class_id())
						)),
						"name" => html::obj_change_url($o->id(),$o->name()),
						"user" => $bm->createdby(),
					));
				}
			}
		}

	}

	/**
	@attrib name=save_show_shared all_args=1
	**/
	function save_show_shared($arr)
	{
		$obj_inst = obj($arr["id"]);
		$pt = $arr["tf"] ? $arr["tf"] : 0;
		$cur_u = aw_global_get("uid");
		$bm = isset($arr["user"])?obj($arr["user"]):0;
		if($pt == 0)
		{
			$ol = new object_list(array(
				"class_id" => CL_USER_BOOKMARKS,
				"sharing" => 1,
			));
		}
		elseif($pt > 0 && $bm && $bm->class_id() == CL_USER_BOOKMARKS && $bm->createdby != $cur_u)
		{
			$shared = $bm->meta("shared");
			if(count($shared))
			{
				$ol = new object_list(array(
					"oid" => $shared,
					"parent" => $pt,
					"class_id" => array(CL_MENU, CL_EXTLINK),
				));
			}
		}
		if($ol)
		{
			foreach($ol->arr() as $o)
			{
				$connect = 0;
				if($arr["show_shared"][$o->id()])
				{
					$connect = 1;
				}
				$conn = $obj_inst->connections_from(array(
					"to" => $o->id(),
					"type" => "RELTYPE_SHOW_SHARED",
				));
				if(!count($conn) && $connect)
				{
					$obj_inst->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_SHOW_SHARED",
					));
				}
				elseif(count($conn) && !$connect)
				{
					$obj_inst->disconnect(array(
						"from" => $o->id(),
					));
				}
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=pm_lod
		@param url optional
	**/
	function pm_lod($arr)
	{
		$bm = $this->init_bm();
	//	$bookmarks_menu = cache::file_get(self::CACHE_KEY_PREFIX_HTML . $bm->id());

		if (empty($bookmarks_menu))
		{
			$pm = new popup_menu();
			$pm->begin_menu("user_bookmarks");
			$parents = array();
			$list = new object_list();
			$this->get_user_bms($bm, $list, $parents);
			$listids = array();
			$i = 0;
			if($list->count())
			{
				$o = $list->begin();

				do
				{
					$listids[$i] = $o->id();
					$i++;
				}
				while ($o = $list->next());
			}

			$cd = array(
				"listids" => $listids,
				"parents" => $parents
			);

			$oids = $cd["listids"];
			$parents = $cd["parents"];
			$params["oid"] = -1;
			if(count($oids))
			{
				$params["oid"] = $oids;
			}

			$params["sort_by"] = "objects.jrk ASC";
			$list = new object_list($params);
			$mt = $bm->meta("grp_sets");

			foreach($list->arr() as $li)
			{
				if($li->class_id() == CL_USER_BOOKMARK_ITEM)
				{
					continue;
				}
				$pt = null;
				if(!empty($parents[$li->id()]))
				{
					$pa = $parents[$li->id()];
					$pt = "mn".$pa;
				}
				elseif ($li->parent() != $bm->id() && $li->class_id() != CL_USER_BOOKMARKS && array_search($li->parent(), $oids)!==false)
				{
					$pt = "mn".$li->parent();
				}

				if ($li->class_id() == CL_MENU || $li->class_id() == CL_USER_BOOKMARKS)
				{
					if($li->class_id() == CL_MENU)
					{
						$text = $li->meta("user_text") != "" ? $li->meta("user_text") : $li->name();
					}
					else
					{
						$text = $li->createdby().t(" j&auml;rjehoidja");
					}
					$params = array(
						"name" => "mn".$li->id(),
						"text" => $text
					);
					if($pt)
					{
						$params["parent"] = $pt;
					}
					$pm->add_sub_menu($params);
				}
				elseif ($li->class_id() == CL_EXTLINK)
				{
					$pm->add_item(array(
						"text" => strlen(trim($li->meta("user_text"))) ? $li->meta("user_text") : $li->name(),
						"link" => $li->prop("url"),
						"parent" => $pt
					));
				}
				else
				{
					$grp = $mt[$li->id()];
					$ga = "";
					if ($grp != "")
					{
						$gl = $li->get_group_list();
						$ga = " - ".$gl[$grp]["caption"];
					}
					$pm->add_item(array(
						"text" => strlen(trim($li->meta("user_text"))) ? $li->meta("user_text") : $li->name().$ga,
						"link" => html::get_change_url($li->id(), array("return_url" => $arr["url"], "group" => $grp)),
						"parent" => $pt
					));
				}
			}

			$uri = new aw_uri($arr["url"]);
			$arr["url"] = $uri->get_query();

			$pm->add_separator();
			$pm->add_item(array(
	//			"emphasized" => true,
				"text" => t("Pane j&auml;rjehoidjasse"),
				"link" => $this->mk_my_orb("add_to_bm", array("url" => $arr["url"]))
			));
			$pm->add_item(array(
		//		"emphasized" => true,
				"text" => t("Eemalda j&auml;rjehoidjast"),
				"link" => $this->mk_my_orb("remove_from_bm", array("url" => $arr["url"]))
			));
			$pm->add_item(array(
		//		"emphasized" => true,
				"text" => t("Toimeta j&auml;rjehoidjat"),
				"link" => html::get_change_url($bm->id(), array("return_url" => $arr["url"], "group" => "bms"))
			));

			$pm->add_item(array(
		//		"emphasized" => true,
				"text" => t("Kuva rakenduse men&uuml;&uuml;s"),
				"link" => $this->mk_my_orb("add_to_app", array("url" => $arr["url"]))
			));
			$pm->add_item(array(
		//		"emphasized" => true,
				"text" => t("Eemalda rakenduse men&uuml;&uuml;st"),
				"link" => $this->mk_my_orb("remove_from_app", array("url" => $arr["url"]))
			));
			$bookmarks_menu = $pm->get_menu(array(
				"text" => html::img(array(
					"url" => "/automatweb/images/aw06/ikoon_jarjehoidja.gif",
					"width" => "16" ,
					"height" => "14",
					"border" => "0",
					"class" => "ikoon"
				)) . '<span class="menu_text">'.t("J&auml;rjehoidja").'</span><img width="5" height="3" border="0" alt="#" src="/automatweb/images/aw06/ikoon_nool_alla.gif" class="down_arrow">'
			));




			cache::file_set(self::CACHE_KEY_PREFIX_HTML . $bm->id(), $bookmarks_menu);
		}

		header("Content-type: text/html; charset=".languages::USER_CHARSET);
		exit($bookmarks_menu);
	}

	function handle_update($arr)
	{
		$object = new object($arr["oid"]);

		if (true or $object->meta("is_bookmark")) // handle only bookmark menu links //TODO:!!! et ei toimuks see iga lingi salvestamisel
		{
			$count = 100;
			while (!$object->is_a(CL_USER_BOOKMARKS) and $count)
			{
				if (object_loader::can("view", $object->parent()))
				{
					$object = new object($object->parent());
				}
				else
				{
					$count = 1;
				}

				--$count;
			}

			if ($object->is_a(CL_USER_BOOKMARKS))
			{
				cache::file_invalidate(self::CACHE_KEY_PREFIX_HTML . $object->id());
			}
		}
	}

	function get_user_bms($bm, $list, &$parents)
	{
		// now, add items from the bum
		$ot = new object_tree(array(
			"parent" => $bm->id(),
			"sort_by" => "objects.jrk"
		));
		$list->add($ot->to_list());
		$ol = new object_list();
		$this->fetch_shared_bms($bm, $ol, $parents);
		$list->add($ol);
		$list->sort_by(array(
			"prop" => "ord",
			"order" => "asc"
		));
	}

	function callback_post_save($arr)
	{
		if ($arr["request"]["objs"])
		{
			foreach(explode(",",$arr["request"]["objs"]) as $add)
			{
				$o = obj($add);
				$o->create_brother($arr["request"]["tf"] ? $arr["request"]["tf"] : $arr["obj_inst"]->id());
			}
		}
	}

	/**
		@attrib name=add_to_bm
		@param url required type=string
	**/
	function add_to_bm($arr)
	{
		try
		{
			$url = new aw_uri($arr["url"]);
		}
		catch (Exception $e)
		{
			$this->show_error_text("Vigane aadress antud, ei saa lisada j&auml;rjehoidjasse.");
		}

		$bm = $this->init_bm();
		$lo = obj();
		$lo->set_class_id(CL_EXTLINK);
		$lo->set_parent($bm->id());

		if ($this->can("view", $url->arg("id")))
		{
			$t = obj($url->arg("id"));
			$nm = $t->name();
			if ($url->arg("group"))
			{
				$gl = $t->get_group_list();
				$nm .= " - ".$gl[$url->arg("group")]["caption"];
			}
			$lo->set_name($nm);
		}
		$lo->set_prop("url", $url->get());
		$lo->save();
		$this->clear_cache($bm);

		$return_url = $url->get();
		if ($return_url{0} === "?")
		{
			$return_url = (isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : $_SERVER["SCRIPT_URI"]) . $return_url;
		}

		if (0 !== strpos($return_url, aw_ini_get("baseurl")))
		{
			$return_url = aw_ini_get("baseurl") . ("/" ===  $return_url{0} ? substr($return_url, 1) : $return_url);
		}

		$this->show_success_text("Aadress lisatud j&auml;rjehoidjasse.");
		return $return_url;
	}

	/**
		@attrib name=add_to_app
		@param url required type=string
	**/
	function add_to_app($arr)
	{
		try
		{
			$url = new aw_uri($arr["url"]);
		}
		catch (Exception $e)
		{
			$this->show_error_text("Vigane aadress antud, ei saa lisada rakendusmenüüsse.");
		}

		$bm = $this->init_bm();

		$o = new object();
		$o->set_class_id(CL_USER_BOOKMARK_ITEM);
		$o->set_parent($bm->id());
		$o->set_prop("show_apps_menu" , 1);
		$o->set_prop("url" , $url->get());

		if ($this->can("view", $url->arg("id")))
		{
			$o->set_prop("obj" , $url->arg("id"));
		}

		$o->set_prop("show_group" , $url->arg("group"));
		$o->save();

		cache::file_invalidate(self::CACHE_KEY_PREFIX_APP_MENU . $bm->id());

		$return_url = $url->get();
		if ($return_url{0} === "?")
		{
			$return_url = (isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : $_SERVER["SCRIPT_URI"]) . $return_url;
		}

		if (0 !== strpos($return_url, aw_ini_get("baseurl")))
		{
			$return_url = aw_ini_get("baseurl") . ("/" ===  $return_url{0} ? substr($return_url, 1) : $return_url);
		}

		$this->show_success_text("Aadress lisatud rakendusmenüüsse.");
		return $return_url;
	}

	/**
		@attrib name=remove_from_bm
		@param url optional
	**/
	function remove_from_bm($arr)
	{
		$bm = $this->init_bm();
		$ot = new object_tree(array(
			"parent" => $bm->id()
		));
		$list = $ot->to_list();
		foreach($list->arr() as $item)
		{
			if ($item->class_id() == CL_EXTLINK && $item->prop("url") == $arr["url"])
			{
				$item->delete();
			}
		}
		$this->clear_cache($bm);
		if(substr($arr["url"], 0, 1) === "?")
		{
			$arr["url"] = (isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : $_SERVER["SCRIPT_URI"]) . $arr["url"];
		}
		return $arr["url"];
	}

	/**
		@attrib name=remove_from_app
		@param url optional
	**/
	function remove_from_app($arr)
	{
		try
		{
			$url = new aw_uri($arr["url"]);
		}
		catch (Exception $e)
		{
			$this->show_error_text("Vigane aadress antud, ei saa eemaldada.");
		}
		$bm = $this->init_bm();

		$ol = new object_list(array(
			"parent" => $bmobj->id(),
			"class_id" => CL_USER_BOOKMARK_ITEM,
			"sort_by" => "objects.jrk"
		));

		$changed = false;

		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if(
				$o->prop("obj") == $url->arg("id") ||
				$o->prop("url") == $arr["url"]
			)
			{
				$o->delete();
				$changed = true;
			}

		};

		if($changed)
		{
			cache::file_invalidate(self::CACHE_KEY_PREFIX_APP_MENU . $bm->id());
		}

		if(substr($arr["url"], 0, 1) === "?")
		{
			$arr["url"] = (isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : $_SERVER["SCRIPT_URI"]) . $arr["url"];
		}
		header("location: " . $arr["url"]);
		die();
		return $arr["url"];
	}

	function clear_cache($bm)
	{
		cache::file_invalidate("bms".$bm->id());
	}

	function show($arr)
	{
		if (aw_global_get("uid") == "")
		{
			return "";
		}

		$this->read_template("show.tpl");

		$bm = $this->init_bm();

		// now, add items from the bum
		$ot = new object_tree(array(
			"parent" => $bm->id(),
			"sort_by" => "objects.jrk"
		));
		$mt = $bm->meta("grp_sets");
		$ol = new object_list();
		$parents = array();
		$this->get_user_bms($bm, $ol, $parents);
		$this->_req_show($bm, $ol, $parents, null);
		$this->vars(array(
			"content" => $this->ct
		));
		return $this->parse();
	}

	function _req_show($bm, $ol, $parents, $parent)
	{
		$this->ct .= $this->parse("LEVEL_BEGIN");
		$oids = $ol->ids();
		foreach($ol->arr() as $li)
		{
			$pt = null;
			if($pa = $parents[$li->id()])
			{
				$pt = $pa;
			}
			elseif ($li->parent() != $bm->id() && $li->class_id() != CL_USER_BOOKMARKS && array_search($li->parent(), $oids)!==false)
			{
				$pt = $li->parent();
			}
			if($parent !== $pt)
			{
				continue;
			}
			else
			{
				if ($li->class_id() == CL_MENU || $li->class_id() == CL_USER_BOOKMARKS)
				{
					if($li->class_id() == CL_MENU)
					{
						$text = $li->meta("user_text") != "" ? $li->meta("user_text") : $li->name();
					}
					else
					{
						$text = $li->createdby().t(" j&auml;rjehoidja");
					}
					$this->vars(array(
						"item_text" => $text,
					));
					$this->ct .= $this->parse("ITEM_TEXT");
					$this->_req_show($bm, $ol, $parents, $li->id());
				}
				else
				if ($li->class_id() == CL_EXTLINK)
				{
					$this->vars(array(
						"item_text" => $li->meta("user_text") != "" ? $li->meta("user_text") : $li->name(),
						"item_link" => $li->prop("url")
					));
					$this->ct .= $this->parse("ITEM_LINK");
					$ol->remove($li->id());
				}
				else
				{
					$grp = $mt[$li->id()];
					$ga = "";
					if ($grp != "")
					{
						$gl = $li->get_group_list();
						$ga = " - ".$gl[$grp]["caption"];
					}

					$this->vars(array(
						"item_text" => $li->meta("user_text") != "" ? $li->meta("user_text") : $li->name().$ga,
						"item_link" => html::get_change_url($li->id(), array("return_url" => $arr["url"], "group" => $grp))
					));
					$this->ct .= $this->parse("ITEM_LINK");
				}
			}
		}
		$this->ct .= $this->parse("LEVEL_END");
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{

		if($tbl === "user_bookmarks")
		{
			if($field=="")
			{
				$this->db_query("CREATE TABLE user_bookmarks (`aw_oid` int primary key, `sharing` int)");
				$ol = new object_list(array(
					"class_id" => CL_USER_BOOKMARKS
				));
				foreach($ol->arr() as $o)
				{
					$this->db_query("INSERT INTO user_bookmarks(`aw_oid`,`sharing`) VALUES('".$o->id()."',0)");
				}
				return true;

			}
			switch($field)
			{
				case "sharing":
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => "int",
					));
					return true;
					break;
			}
		}
	}

	function callback_mod_tab($arr)
	{
		if($arr["id"] === "shared" && $arr["obj_inst"]->createdby() != aw_global_get("uid"))
		{
			return false;
		}
		return true;
	}

	function fetch_shared_bms($bm, $ol, &$parents)
	{
		$paths = array();
		$conn = $bm->connections_from(array(
			"type" => "RELTYPE_SHOW_SHARED"
		));
		if(count($conn))
		{
			$ss = $bm->prop("shared_show");
			foreach($conn as $c)
			{
				$o = obj($c->prop("to"));
				$path = $o->path();
				$cont = 0;
				foreach($path as $po)
				{
					if($po->class_id() == CL_USER_BOOKMARKS)
					{
						if($po->id() != $o->id())
						{
							if($po->id() == $bm->id())
							{
								$cont = 1;
							}
							elseif($ss)
							{
								$parents[$o->id()] = $po->id();
								$paths[$po->id()] = array();
							}
						}
						$pbms[$o->id()] = $po->meta("shared");
					}
				}
				if($cont)
				{
					continue;
				}
				$paths[$o->id()] = $o->path();
			}
		}

		$oids = array();
		foreach($paths as $bmid => $pth)
		{
			$set = 1;
			foreach($pth as $po)
			{
				if(!empty($paths[$po->id()]) && $po->id() != $bmid)
				{
					$set = 0;
				}
			}
			if($set)
			{
				$oids[$bmid] = $bmid;
			}
		}

		if(count($oids))
		{
			$ol = new object_list(array(
				"oid" => $oids,
			));
			$olids = $ol->ids();
		}
		$ui = get_instance(CL_USER);
		$uo = $ui->get_obj_for_uid(aw_global_get("uid"));
		$gconn = $uo->connections_from(array(
			"type" => "RELTYPE_GRP",
		));
		$data = array();
		foreach($gconn as $gc)
		{
			$go = obj($gc->prop("to"));
			$bmconn = $go->connections_from(array(
				"type" => "RELTYPE_BOOKMARK",
			));
			foreach($bmconn as $bmc)
			{
				$o = obj($bmc->prop("to"));
				$data[$o->id()] = $o->id();
			}
		}

		if(count($data))
		{
			$ol2 = new object_list(array(
				"oid" => array_keys($data),
				"sort_by" => "objects.jrk"
			));
			foreach($ol2->arr() as $o)
			{
				if($olids[$o->id()])
				{
					$ol2->remove($o->id());
				}
				else
				{
					$path = $o->path();
					foreach($path as $po)
					{
						if($po->class_id() == CL_USER_BOOKMARKS)
						{
							$pbms[$o->id()] = $po->meta("shared");
							if($po->id() == $bm->id())
							{
								$ol2->remove($o->id());
							}
						}
					}
				}
			}

			if($ol)
			{
				$ol->add($ol2);
			}
			else
			{
				$ol = $ol2;
			}
			$olids = $ol->ids();
			foreach($ol->arr() as $o)
			{
				$ot = new object_tree(array(
					"parent" => $o->id(),
					"sort_by" => "objects.jrk"
				));
				$sol = $ot->to_list();
				foreach($sol->arr() as $so)
				{
					if(!$olids[$so->id()] && $pbms[$o->id()][$so->id()])
					{
						$ol->add($so->id());
					}
				}
			}
		}

		if(!$ol)
		{
			$ol = new object_list(array(
				"oid" => -1
			));
		}
		return $ol;
	}
}
