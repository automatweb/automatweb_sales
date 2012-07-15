<?php

// obj_quick_add.aw - Kiirlisamine
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=bms

	@property bm_tb type=toolbar store=no no_caption=1

	@layout bm_tt type=hbox width=30%:70%

		@layout bm_tree type=vbox parent=bm_tt closeable=1 area_caption=Puu

			@property bm_tree type=treeview store=no no_caption=1 parent=bm_tree

		@property bm_table type=table store=no no_caption=1 parent=bm_tt

@groupinfo bms caption="Kiirmen&uuml;&uuml;" submit=no

@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption Omanik
*/

class obj_quick_add extends class_base
{
	function obj_quick_add()
	{
		$this->init(array(
			"tpldir" => "applications/customer_satisfaction_center/obj_quick_add",
			"clid" => CL_OBJ_QUICK_ADD
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
				break;
		};
		return $retval;
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
			"text" => t("Objekti t&uuml;&uuml;p"),
			"link" => html::get_new_url(CL_OBJECT_TYPE, $pt, array("return_url" => get_ru()))
		));
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
		$tb->add_separator();
		$tb->add_button(array(
			"name" => "copy_quick_adds",
			"action" => "copy_quick_adds",
			"img" => "copy.gif",
			"tooltip" => t("Kopeeri")
		));

		$tb->add_button(array(
			"name" => "cut_quick_adds",
			"action" => "cut_quick_adds",
			"img" => "cut.gif",
			"tooltip" => t("L&otilde;ika")
		));

		if ( is_array(aw_global_get('copy_quick_adds')) || is_array(aw_global_get('cut_quick_adds')) )
		{
			$tb->add_button(array(
				"name" => "paste_quick_adds",
				"action" => "paste_quick_adds",
				"img" => "paste.gif",
				"tooltip" => t("Kleebi")
			));
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

	/**
		@attrib name=copy_quick_adds
	**/
	function copy_quick_adds($arr)
	{
		$_SESSION['copy_quick_adds'] = $arr['sel'];
		return $arr['post_ru'];
	}

	/**
		@attrib name=cut_quick_adds
	**/
	function cut_quick_adds($arr)
	{
		$_SESSION['cut_quick_adds'] = $arr['sel'];
		return $arr['post_ru'];
	}

	/**
		@attrib name=paste_quick_adds
	**/
	function paste_quick_adds($arr)
	{
		$cut_quick_adds = aw_global_get( 'cut_quick_adds' );
		foreach ( safe_array($cut_quick_adds) as $oid )
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
		unset($_SESSION['cut_quick_adds']);

		$copy_quick_adds = aw_global_get( 'copy_quick_adds' );
		foreach ( safe_array($copy_quick_adds) as $oid )
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
		unset($_SESSION['copy_quick_adds']);

		return $arr['post_ru'];
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
				"lang_id" => array(),
				"site_id" => array(),
				"class_id" => CL_MENU
			)),
			"var" => "tf",
			"icon" => icons::get_icon_url(CL_MENU)
		));
	}

	function _init_bm_table($t)
	{
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
		$t->define_field(array(
			"name" => "clid",
			"caption" => t("Objekti t&uuml;&uuml;p"),
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
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Objektid pannakse"),
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
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "objects.class_id asc, objects.name asc"
		));
		$mt = $arr["obj_inst"]->meta("grp_sets");
		$clss = aw_ini_get("classes");
		$ps = new popup_search();
		foreach($ol->arr() as $o)
		{
			$clid = "";
			if ($o->class_id() != CL_MENU)
			{
				$clid = $clss[$o->prop("type")]["name"];
			}
			$t->define_data(array(
				"icon" => html::img(array(
					'url' => icons::get_icon_url($o->class_id() == CL_OBJECT_TYPE ? $o->subclass() : $o->class_id())
				)),
				"name" => html::obj_change_url($o),
				"oid" => $o->id(),
				"clid" => $clid,
				"ord" => html::textbox(array(
					"name" => "dat[".$o->id()."][ord]",
					"size" => 5,
					"value" => $o->ord()
				)),
				"user_text" => html::textbox(array(
					"name" => "dat[".$o->id()."][comment]",
					"value" => $o->comment(),
					"size" => 15
				)),
				"location" =>
					html::obj_change_url($o->meta("object_parent")).
					html::hidden(array(
						"name" => "dat[".$o->id()."][location]",
						"value" => $o->meta("object_parent"),
					)).
					$ps->get_popup_search_link(array(
						"pn" => "dat[".$o->id()."][location]",
//						"clid" => CL_MENU
					))
			));
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "bm_table":
				$this->save_bms($arr["request"]);
				break;
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["post_ru"] = post_ru();
		if (isset($request["tf"]))
		{
			$arr["tf"] = $request["tf"];
		}
	}

	/**
		@attrib name=qa_lod
		@param url optional
	**/
	function qa_lod($arr)
	{
		$bm = $this->init_qa();

		$bits = parse_url($arr["url"]);
		$q = $bits["query"];
		parse_str($q, $td);

		$pm = new popup_menu();
		$pm->begin_menu("user_qa");

		// now, add items from the bum
		$ot = new object_tree(array(
			"parent" => $bm->id(),
			"sort_by" => "objects.jrk"
		));
		$list = $ot->to_list();
		foreach($list->arr() as $li)
		{
			$pt = null;
			if ($li->parent() != $bm->id())
			{
				$pt = "mn".$li->parent();
			}
			if ($li->class_id() == CL_MENU)
			{
				$pm->add_sub_menu(array(
					"name" => "mn".$li->id(),
					"text" => $li->name()
				));
			}
			else
			{
				$pto = $td["id"];
				if ($this->can("add", $li->meta("object_parent")))
				{
					$pto = $li->meta("object_parent");
				}
				$pm->add_item(array(
					"text" => $li->comment() != "" ? $li->comment() : $li->name(),
					"link" => html::get_new_url($li->prop("type"), $pto, array("return_url" => $arr["url"])),
					"parent" => $pt
				));
			}
		}

		$pm->add_separator();
		/*$pm->add_item(array(
			"text" => t("Pane kiirmen&uuml;&uuml;sse"),
			"link" => $this->mk_my_orb("add_to_bm", array("url" => $arr["url"]))
		));
		$pm->add_item(array(
			"text" => t("Eemalda kiirmen&uuml;&uuml;st"),
			"link" => $this->mk_my_orb("remove_from_bm", array("url" => $arr["url"]))
		));*/
		$pm->add_item(array(
			"text" => t("Toimeta kiirmen&uuml;&uuml;d"),
			"link" => html::get_change_url($bm->id(), array("return_url" => $arr["url"], "group" => "bms"))
		));
		$url = parse_url($arr["url"]);
		parse_str($url["query"], $urlvars);
		if(isset($urlvars["class"]) and "admin_if" === $urlvars["class"] and !empty($urlvars["parent"]))
		{
			$ol = new object_list(array(
				"parent" => $urlvars["parent"],
				"site_id" => array(),
				"lang_id" => array(),
			));
			$ol->sort_by_cb(array($this, "_qa_ol_sort"));
			if($ol->count())
			{
				$pm->add_sub_menu(array(
					"name" => "cur",
					"text" => "Muu objekt"
				));
			}
			$set_cls = array();
			$cls = aw_ini_get("classes");
			foreach($ol->arr() as $o)
			{
				$class_id = $o->class_id();
				if($class_id == 179)
				{
					continue;
				}

				if(empty($set_cls[$class_id]))
				{
					$set_cls[$class_id] = 1;
					$cl_name = $cls[$class_id]['file'];
					$cl_title = $cls[$class_id]['name'];
					$pm->add_item(array(
						"text" => $cl_title,
						"link" => html::get_new_url(
							$cl_name,
							$urlvars["parent"],
							array("return_url" => $arr["url"])
						),
						"parent" => "cur"
					));
				}
			}
		}

		header("Content-type: text/html; charset=".aw_global_get("charset"));
		die($pm->get_menu(array(
			"text" => '<img alt="" title="" border="0" src="'.aw_ini_get("baseurl").'/automatweb/images/aw06/ikoon_lisa.gif" id="mb_user_qa" border="0" class="ikoon" /> <span class="menu_text">'.t("Lisa kiiresti").'</span> <img width="5" height="3" border="0" alt="#" src="/automatweb/images/aw06/ikoon_nool_alla.gif" class="down_arrow">'
		)));
	}

	function _qa_ol_sort($a, $b)
	{
		$cls = aw_ini_get("classes");
		$a = $cls[$a->class_id()]['name'];
		$b = $cls[$b->class_id()]['name'];
		if ($a == $b) {
			return 0;
		}
		return ($a < $b) ? -1 : 1;
	}

	function init_qa()
	{
		$p = get_current_person();
		$ol = new object_list(array(
			"class_id" => CL_OBJ_QUICK_ADD,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_OBJ_QUICK_ADD.RELTYPE_PERSON" => $p->id()
		));
		if (!$ol->count())
		{
			$o = obj();
			$o->set_class_id(CL_OBJ_QUICK_ADD);
			$o->set_parent(aw_ini_get("amenustart"));
			$o->set_name(sprintf(t("%s kiirlisamine"), $p->name()));
			$o->save();

			$o->connect(array(
				"to" => $p->id(),
				"type" => "RELTYPE_PERSON"
			));
			return $o;
		}
		else
		{
			return $ol->begin();
		}
	}

	/**
		@attrib name=add_to_bm
		@param url optional
	**/
	function add_to_bm($arr)
	{
		// get the class id from the url
		$bits = parse_url($arr["url"]);
		$q = $bits["query"];
		parse_str($q, $td);
		$clid = clid_for_name($td["class"]);
		if (!$clid)
		{
			return $arr["url"];
		}

		$bm = $this->init_qa();
		$lo = obj();
		$lo->set_class_id(CL_OBJECT_TYPE);
		$lo->set_parent($bm->id());
		$lo->set_prop("type", $clid);
		$clss = aw_ini_get("classes");
		$lo->set_name($clss[$clid]["name"]);
		$lo->save();
		return $arr["url"];
	}

	/**
		@attrib name=remove_from_bm
		@param url optional
	**/
	function remove_from_bm($arr)
	{
		// get the class id from the url
		$bits = parse_url($arr["url"]);
		$q = $bits["query"];
		parse_str($q, $td);
		$clid = clid_for_name($td["class"]);
		if (!$clid)
		{
			return $arr["url"];
		}

		$bm = $this->init_qa();
		$ot = new object_tree(array(
			"parent" => $bm->id()
		));
		$list = $ot->to_list();
		foreach($list->arr() as $item)
		{
			if ($item->class_id() == CL_OBJECT_TYPE && $item->prop("type") == $clid)
			{
				$item->delete();
			}
		}
		return $arr["url"];
	}

	/**
		@attrib name=save_bms
	**/
	function save_bms($arr)
	{
		foreach(safe_array($arr["dat"]) as $oid => $dat)
		{
			$o = obj($oid);
			$mod = false;
			if ($dat["ord"] != $o->ord())
			{
				$o->set_ord($dat["ord"]);
				$mod = true;
			}
			if ($dat["comment"] != $o->comment())
			{
				$o->set_comment($dat["comment"]);
				$mod = true;
			}
			if ($dat["location"] != $o->meta("object_parent"))
			{
				$o->set_meta("object_parent", $dat["location"]);
				$mod = true;
			}
			if ($mod)
			{
				$o->save();
			}
		}
		return $arr["post_ru"];
	}
}
