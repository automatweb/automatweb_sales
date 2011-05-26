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

@reltype SHOW_SHARED value=1 clid=CL_EXTLINK,CL_MENU,CL_USER_BOOKMARKS
@caption Jagatud j&auml;rjehoidja
*/

class user_bookmarks extends class_base
{
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
		$arr["tf"] = $request["tf"];
		$arr["user"] = $request["user"];
		if($arr["group"] === "bms")
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
		$arr["args"]["tf"] = $arr["request"]["tf"];
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
			$o->set_name(sprintf(t("%s j&auml;rjehoidja"), $p->name()));
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
					$inf[$nm] = ($dat["parent"] != "" ? "&nbsp;&nbsp;&nbsp;&nbsp;" : "").$dat["caption"];
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
					"checked" => $shared[$o->id()]?1:0,
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

		$pm = new popup_menu();
		$pm->begin_menu("user_bookmarks");
		$time = 5*24*60*60;
		if (!($cd = cache::file_get_ts("bms".$bm->id(), time() - $time)))
		{
			$parents = array();
			$list = array();
			$this->get_user_bms($bm, $list, $parents);
			$listids = array();
			$i = 0;
			foreach($list->arr() as $li)
			{
				$listids[$i] = $li->id();
				$i++;
			}
			$cd = array(
				"listids" => $listids,
				"parents" => $parents,
			);
			cache::file_set("bms".$bm->id(), aw_serialize($cd));
		}

		if($cd && !is_array($cd))
		{
			$cd = aw_unserialize($cd);
		}

		if($cd)
		{
			$oids = $cd["listids"];
			$parents = $cd["parents"];
		}

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
			//arr($li->id().". ".$li->name()." ".$pt);
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
					"text" => $li->meta("user_text") != "" ? $li->meta("user_text") : $li->name(),
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
					"text" => $li->meta("user_text") != "" ? $li->meta("user_text") : $li->name().$ga,
					"link" => html::get_change_url($li->id(), array("return_url" => $arr["url"], "group" => $grp)),
					"parent" => $pt
				));
			}
		}

		$uri = new aw_uri($arr["url"]);
		$arr["url"] = $uri->get_query();

		$pm->add_separator();
		$pm->add_item(array(
			"emphasized" => true,
			"text" => t("Pane j&auml;rjehoidjasse"),
			"link" => $this->mk_my_orb("add_to_bm", array("url" => $arr["url"]))
		));
		$pm->add_item(array(
			"emphasized" => true,
			"text" => t("Eemalda j&auml;rjehoidjast"),
			"link" => $this->mk_my_orb("remove_from_bm", array("url" => $arr["url"]))
		));
		$pm->add_item(array(
			"emphasized" => true,
			"text" => t("Toimeta j&auml;rjehoidjat"),
			"link" => html::get_change_url($bm->id(), array("return_url" => $arr["url"], "group" => "bms"))
		));

		header("Content-type: text/html; charset=".aw_global_get("charset"));
		die($pm->get_menu(array(
					"text" => '<img src="/automatweb/images/aw06/ikoon_jarjehoidja.gif" alt="" width="16" height="14" border="0" class="ikoon" />'.t("J&auml;rjehoidja")
		)));
	}

	function get_user_bms($bm, $list, &$parents)
	{
		// now, add items from the bum
		$ot = new object_tree(array(
			"parent" => $bm->id(),
			"sort_by" => "objects.jrk"
		));
		$list = $ot->to_list();
		$ol = array();
		$this->fetch_shared_bms($bm, $ol, $parents);
		$list->add($ol);
		$list->sort_by(array(
			"prop" => "ord",
			"order" => "asc",
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
		@param url optional
	**/
	function add_to_bm($arr)
	{
		$bm = $this->init_bm();
		$lo = obj();
		$lo->set_class_id(CL_EXTLINK);
		$lo->set_parent($bm->id());

		// parse id from url and get object and stuff
		$bits = parse_url($arr["url"]);
		$q = $bits["query"];
		parse_str($q, $td);
		if ($this->can("view", $td["id"]))
		{
			$t = obj($td["id"]);
			$nm = $t->name();
			if ($td["group"] != "")
			{
				$gl = $t->get_group_list();
				$nm .= " - ".$gl[$td["group"]]["caption"];
			}
			$lo->set_name($nm);
		}
		$lo->set_prop("url", $arr["url"]);
		$lo->save();
		$this->clear_cache($bm);
		if(substr($arr["url"], 0, 1) === "?")
		{
			$arr["url"] = $_SERVER["SCRIPT_URI"].$arr["url"];
		}
		return $arr["url"];
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
			$arr["url"] = $_SERVER["SCRIPT_URI"].$arr["url"];
		}
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
		$ol = array();
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
			"type" => "RELTYPE_SHOW_SHARED",
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
				if($paths[$po->id()] && $po->id() != $bmid)
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
