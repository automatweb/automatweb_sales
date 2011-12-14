<?php

class acl_manager extends aw_template implements orb_public_interface
{
	function acl_manager()
	{
		$this->init("vcl/acl_manager");
	}

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	public function init_vcl_property($arr)
	{
		$tp = $arr["prop"];
		$tp["type"] = "text";

		$content = $this->_get_cur_acl_desc($arr["obj_inst"]);

		if ($this->can("admin", $arr["obj_inst"]->id()))
		{
			$content .= "<br>";
			$url = $this->mk_my_orb("disp_manager", array("id" => $arr["obj_inst"]->id(), "in_popup" => "1"));
			$content .= html::href(array(
				"caption" => t("Muuda"),
				"url" => "javascript:void(0)",
				"onclick" => "aw_popup_scroll(\"{$url}\", \"acl_manager\", 800, 500);"
			));
		}

		$tp["value"] = $content;
		return array($tp["name"] => $tp);
	}

	public function process_vcl_property($arr)
	{
	}

	function _get_cur_acl_desc($o)
	{
		$t = new aw_table();
		$t->define_field(array(
			"name" => "grp",
			"caption" => t("Grupp"),
			"align" => "right"
		));
		foreach(aw_ini_get("acl.names") as $id => $name)
		{
			$t->define_field(array(
				"name" => $id,
				"caption" => $name,
				"align" => "center"
			));
		}
		foreach($o->acl_get() as $gid => $inf)
		{
			try
			{
				$group = obj($gid, array(), CL_GROUP);
				$dat = array("grp" => html::obj_change_url($gid));
				foreach(aw_ini_get("acl.names") as $id => $name)
				{
					$dat[$id] = ($inf[$id] ? t("Jah") : t("Ei"));
				}
				$t->define_data($dat);
			}
			catch (Exception $e)
			{
			}
		}
		return $t->draw();
	}


	/**
		@attrib name=disp_manager
		@param id required type=int acl=admin
		@param grp_id optional
	**/
	function disp_manager($arr)
	{
		$_GET["in_popup"] = 1;
		$this->read_template("show.tpl");

		$this->_get_tree($arr);

		$this->_get_table($arr);

		$this->_get_tb($arr);

		$arr["post_ru"] = post_ru();
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_mgr", $arr),
			"table_legend" => sprintf(t("Tabeli legend:<Br>M - %s<br>L - %s<Br>ACL - %s<br>K - %s<br>V - %s<br>Alam - %s<br>J - %s"),
					t("Muutmine"),
					t("Lisamine"),
					t("ACL Muutmine"),
					t("Kustutamine"),
					t("Vaatamine"),
					t("Kehtib ainult alamobjektidele"),
					t("Juurkaust")
			)
		));
		return $this->parse();
	}

	function _get_tree($r)
	{
		$t = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"persist_state" => true,
				"tree_id" => "acl_mgr",
			),
			"root_item" => obj(aw_ini_get("groups.tree_root")),
			"ot" => new object_tree(array(
				"class_id" => array(CL_GROUP,CL_MENU),
				"parent" => aw_ini_get("groups.tree_root")
			)),
			"var" => "grp_id",
		));

		$this->vars(array(
			"tree" => $t->finalize_tree()
		));
	}

	function _init_acl_tbl($t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$shorts = array(
			"can_edit" => t("M"),
			"can_add" => t("L"),
			"can_admin" => t("ACL"),
			"can_delete" => t("K"),
			"can_view" => t("V"),
			"can_subs" => t("ALAM")
		);
		foreach(aw_ini_get("acl.names") as $id => $name)
		{
			$t->define_field(array(
				"name" => $id,
				"caption" => html::href(array(
					"url" => "#",
					"caption" => $shorts[$id],
					"title" => $name,
				)),
				"align" => "center"
			));
		}
		$t->define_field(array(
			"name" => "rootf",
			"caption" => html::href(array(
				"url" => "#",
				"caption" => t("J"),
				"title" => t("Juurkaust"),
			)),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "set_acl",
			"caption" => t("M&auml;&auml;ra"),
			"align" => "center"
		));
	}

	function _get_table($r)
	{
		$t = new aw_table();

		$obj = obj($r["id"]);
		$acls = $obj->acl_get();

		$pt = isset($_GET["grp_id"]) ? $_GET["grp_id"] : aw_ini_get("groups.tree_root");
		$this->_init_acl_tbl($t);

		// get all subgroups/users
		$ol = new object_list(array(
			"parent" => $pt,
			"class_id" => array(CL_GROUP, CL_USER),
			"sort_by" => "objects.class_id"
		));
		foreach($ol->arr() as $oid => $o)
		{
			$dat = array(
				"icon" => icons::get_icon($o),
				"name" => html::obj_change_url($o),
			);
			if ($o->class_id() == CL_USER)
			{
				$grp_ol = new object_list(array(
					"class_id" => CL_GROUP,
					"name" => $o->name(),
					"type" => 1
				));
				if (!$grp_ol->count())
				{
					continue;
					error::raise(array(
						"id" => "ERR_NO_GRP",
						"msg" => sprintf(t("no group for user %s!"), $o->name())
					));
				}
				$o = $grp_ol->begin();
				$oid = $o->id();
			}

			$adm_rm = safe_array($o->meta("admin_rootmenu2"));

			$dat["rootf"] = html::checkbox(array(
				"name" => "is_rootmenu[$oid]",
				"value" => 1,
				"checked" => isset($adm_rm[aw_global_get("lang_id")]) and in_array($obj->id(), $adm_rm[aw_global_get("lang_id")])
			));
			$dat["set_acl"] = html::checkbox(array(
				"name" => "set_acl[$oid]",
				"value" => 1,
				"checked" => isset($acls[$oid])
			));
			foreach(aw_ini_get("acl.names") as $id => $name)
			{
				$dat[$id] = html::checkbox(array(
					"name" => "acl_matrix[$oid][$id]",
					"value" => 1,
					"checked" => isset($acls[$o->id()][$id]) and $acls[$o->id()][$id] == 1
				));
			}
			$t->define_data($dat);
		}
		$this->vars(array(
			"table" => $t->draw()
		));
	}

	function _get_tb($r)
	{
		$t = new toolbar();
		$t->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta &otilde;igused"),
			"img" => "save.gif",
			"url" => "javascript:document.changeform.submit()"
		));

		$o = obj($r["id"]);
		$this->vars(array(
			"toolbar" => $t->get_toolbar()
		));
	}

	/**
		@attrib name=submit_mgr
	**/
	function submit_mgr($arr)
	{
		$obj = obj($arr["id"]);
		if (!$this->can("admin", $arr["id"]))
		{
			error::raise(array(
				"id" => "ERR_ACL",
				"msg" => sprintf(t("Teil ei ole &otilde;igust muuta objekti %s &otilde;igusi!"), $arr["id"])
			));
		}
		$acl = $obj->acl_get();
		// get a list of all groups shown
		$pt = isset($arr["grp_id"]) ? $arr["grp_id"] : aw_ini_get("groups.tree_root");
		$ol = new object_list(array(
			"parent" => $pt,
			"class_id" => array(CL_GROUP, CL_USER)
		));
		foreach($ol->arr() as $oid => $o)
		{
			// translate users to their groups
			if ($o->class_id() == CL_USER)
			{
				$grp_ol = new object_list(array(
					"class_id" => array(CL_GROUP),
					"name" => $o->name(),
					"type" => 1
				));
				if (!$grp_ol->count())
				{
					continue;
					error::raise(array(
						"id" => "ERR_NO_GRP",
						"msg" => sprintf(t("no group for user %s!"), $o->name())
					));
				}
				$o = $grp_ol->begin();
				$oid = $o->id();
			}
			// if there is an acl relation for this group, then save the data
			// if not and there are some thingies set, then create it
			if (isset($acl[$o->id()]) && empty($arr["set_acl"][$o->id()]))
			{
				$obj->acl_del($o);
			}
			elseif (isset($acl[$o->id()]) || isset($arr["acl_matrix"][$o->id()]) && count($arr["acl_matrix"][$o->id()]) || !empty($arr["set_acl"][$o->id()]))
			{
				$obj->acl_set($o, isset($arr["acl_matrix"][$o->id()])  ? safe_array($arr["acl_matrix"][$o->id()]) : array());
			}

			if (!empty($arr["is_rootmenu"][$oid]))
			{
				$o->connect(array(
					"to" => $obj->id(),
					"type" => "RELTYPE_ADMIN_ROOT"
				));
				$adm_rm = safe_array($o->meta("admin_rootmenu2"));
				if (!isset($adm_rm[aw_global_get("lang_id")]) or !is_array($adm_rm[aw_global_get("lang_id")]))
				{
					$adm_rm[aw_global_get("lang_id")] = array();
				}
				$adm_rm[aw_global_get("lang_id")][] = $obj->id();
				$o->set_meta("admin_rootmenu2", $adm_rm);
				$o->save();
			}
			else
			{
				$adm_rm = safe_array($o->meta("admin_rootmenu2"));
				if (isset($adm_rm[aw_global_get("lang_id")]) and in_array($obj->id(), $adm_rm[aw_global_get("lang_id")]))
				{
					unset($adm_rm[aw_global_get("lang_id")][array_search($obj->id(), $adm_rm[aw_global_get("lang_id")])]);
					$o->set_meta("admin_rootmenu2", $adm_rm);
					$o->save();
				}
			}
		}
		return $arr["post_ru"];
	}
}
