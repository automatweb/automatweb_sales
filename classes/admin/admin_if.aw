<?php
/*
@classinfo no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=o

	@property o_tb type=toolbar no_caption=1 store=no

	@layout o_bottom type=hbox width=30%:80%

		@layout o_bot_left type=vbox parent=o_bottom closeable=1 area_caption=Kataloogid

			@property o_tree type=treeview no_caption=1 store=no parent=o_bot_left

		@layout o_bot_right type=vbox parent=o_bottom

			@property o_tbl type=table no_caption=1 store=no parent=o_bot_right


@default group=fu

	@property info_text type=text store=no
	@caption Info

	@property zip_upload type=fileupload
	@caption Laadi ZIP fail

	@property uploader type=text store=no
	@caption Lae faile

@groupinfo o caption="Objektid" save=no submit=no
@groupinfo fu caption="Failide &uuml;leslaadimine"

*/

class admin_if extends class_base
{
	const NEW_MENU_CACHE_KEY = "aw_adminif_newbtn_menu_cache_";

	var $use_parent;
	var $period;
	var $curl;
	var $selp;
	var $force_0_parent;

	/** this stores a list of {clid => class name } for each class that implements the admin if modifier interface **/
	private $modifiers_by_clid;

	private $pm;
	private $url_template;
	private $url_template_reforb;

	function admin_if()
	{
		$this->init(array(
			"tpldir" => "workbench",
			"clid" => CL_ADMIN_IF
		));

		$this->data_list_ot_flds = array(
			"" => array(
				"modifiedby" => "modifiedby",
				"modified" => "modified",
				"oid" => "oid",
				"class_id" => "class_id",
				"lang_id" => "lang_id",
				"comment" => "comment",
				"name" => "name",
				"brother_of" => "brother_of",
				"jrk" => "ord",
				"status" => "status"
			)
		);

		// init get_popup_data stuff
		$this->pm = new popup_menu();

		$this->post_ru_append = "&return_url=".urlencode(get_ru());
		$this->change_url_template = str_replace("__", "%s", $this->mk_my_orb("change", array(
			"id" => "__",
			"parent" => "__",
			"period" => "__"
		), "__",true,true));


		$this->if_cut_url_template = str_replace("__", "%s", $this->mk_my_orb("if_cut", array(
			"reforb" => 1,
			"id" => "__",
			"parent" => "__",
			"sel[__]" => "1",
		), "admin_if",true,true));


		$this->if_copy_template = str_replace("__", "%s", $this->mk_my_orb("if_copy", array(
			"reforb" => 1,
			"id" => "__",
			"parent" => "__",
			"sel[__]" => "1",
			"period" => "__"
		), "admin_if",true,true));

		$this->if_delete_template = str_replace("__", "%s", $this->mk_my_orb("if_delete", array(
			"ret_id" => "__",
			"reforb" => 1,
			"id" => "__",
			"parent" => "__",
			"sel[__]" => "1",
			"period" => "__"
		), "admin_if",true,true));
	}

	function _get_info_text($arr)
	{
		if (!empty($_SESSION["fu_tm_text"]))
		{
			$arr["prop"]["value"] = $_SESSION["fu_tm_text"];
			unset($_SESSION["fu_tm_text"]);
		}
		else
		{
			return PROP_IGNORE;
		}
	}

	function callback_mod_reforb(&$arr, $request)
	{
		if (isset($request["parent"]))
		{
			$arr["parent"] = $request["parent"];
		}
	}

	function callback_mod_retval(&$arr)
	{
		if (isset($arr["request"]["parent"]))
		{
			$arr["args"]["parent"] = $arr["request"]["parent"];
		}
	}

	function _get_o_tb($arr)
	{
		$parent = !empty($arr["request"]["parent"]) ? $arr["request"]["parent"] : aw_ini_get("rootmenu");

		$tb = $arr["prop"]["vcl_inst"];
		// add button only visible if the add privilege is set
		$can_add = $this->can("add", $parent);
		if ($can_add)
		{
			$tb->add_menu_button(array(
				"name" => "new",
				"tooltip" => t("Lisa"),
				"icon" => "add"
			));

			$this->generate_new($tb, $parent, (isset($arr["request"]["period"]) ? $arr["request"]["period"] : null));
		}

		$tb->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "save_if",
			"icon" => "disk"
		));

		$tb->add_separator();

		$tb->add_button(array(
			"name" => "cut",
			"tooltip" => t("L&otilde;ika"),
			"action" => "if_cut",
			"icon" => "cut"
		));

		$tb->add_button(array(
			"name" => "copy",
			"tooltip" => t("Kopeeri"),
			"action" => "if_copy",
			"icon" => "copy"
		));

		if (count($this->get_cutcopied_objects()) && $can_add)
		{
			$tb->add_button(array(
				"name" => "paste",
				"tooltip" => t("Kleebi"),
				"action" => "if_paste",
				"icon" => "paste"
			));
		}

		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"confirm" => t("Kustutada valitud objektid?"),
			"action" => "if_delete",
			"icon" => "delete"
		));
		$tb->add_separator();

		$tb->add_button(array(
			"name" => "refresh",
			"tooltip" => t("Uuenda"),
			"url" => "javascript:window.location.reload()",
			"img" => "refresh.gif"
		));

		$tb->add_menu_button(array(
			"name" => "import",
			"tooltip" => t("Impordi"),
			"img" => "import.gif"
		));

		if ($can_add)
		{
			$tb->add_menu_item(array(
				"parent" => "import",
				"text" => t("Impordi kaustu"),
				"title" => t("Impordi kaustu"),
				"name" => "import_menus",
				"tooltip" => t("Impordi kaustu"),
				"link" => $this->mk_my_orb("import",array("parent" => $parent))
			));

			$tb->add_menu_item(array(
				"parent" => "import",
				"text" => t("Impordi faile"),
				"title" => t("Impordi faile"),
				"name" => "import_files",
				"tooltip" => t("Impordi faile"),
				"link" => aw_url_change_var("group", "fu")
			));
		}

		$tb->add_button(array(
			"name" => "preview",
			"tooltip" => t("Eelvaade"),
			"target" => "_blank",
			"url" => obj_link($parent),
			"img" => "preview.gif",
		));
		$file_manager = new file_manager();
		$file_manager->add_zip_button(array("tb" => $tb));

		if (aw_ini_get("per_oid"))
		{
			$tb->add_separator();
			$tb->add_menu_button(array(
				"name" => "set_period",
				"img" => "periods.gif",
				"tooltip" => t("Vali periood")
			));
			$this->_init_period_dropdown($tb);
			if ($tmp = aw_global_get("period"))
			{
				$dbp = get_instance(CL_PERIOD);
				$pd = $dbp->get($tmp);
				$tb->add_cdata(sprintf(t("Valitud periood: %s"), $pd["name"]." ".(aw_global_get("act_per_id") == $tmp ? t("(A)") : "")));
			}
		}
	}

	private function get_cutcopied_objects()
	{
		$sel_objs = aw_global_get("cut_objects");
		if (!is_array($sel_objs))
		{
			$sel_objs = array();
		}
		$t = aw_global_get("copied_objects");
		if (!is_array($t))
		{
			$t = array();
		}
		$sel_objs+=$t;
		return $sel_objs;
	}

	function _get_o_tree($arr)
	{
		$tree = $arr["prop"]["vcl_inst"];

		$rn = empty($this->use_parent) ? aw_ini_get("admin_rootmenu2") : $this->use_parent;

		$this->period = isset($arr["request"]["period"]) ? $arr["request"]["period"] : null;
		$admrm = aw_ini_get("admin_rootmenu2");
		if (is_array($admrm))
		{
			$admrm = reset($admrm);
		}
		$this->curl = isset($arr["request"]["curl"]) ? $arr["request"]["curl"] : get_ru();
		$this->selp = isset($arr["request"]["selp"]) ? $arr["request"]["selp"] : (isset($arr["request"]["parent"]) ? $arr["request"]["parent"] : null);

		$tree->start_tree(array(
			"type" => TREE_DHTML,
			"has_root" => $this->use_parent ? 0 : 1,
			"tree_id" => "admin_if",
			"persist_state" => 1,
			"root_name" => html::bold(t("AutomatWeb")),
			"root_url" => aw_url_change_var("parent", $admrm, $this->curl),
			"get_branch_func" => $this->mk_my_orb("gen_folders",array("selp" => $this->selp, "curl" => $this->curl, "period" => $this->period, "parent" => "0")),
		));

		$has_items = array();
		if (is_array($rn) && count($rn) >1)
		{
			foreach($rn as $rn_i)
			{
				if (isset($has_items[$rn_i]) && $this->can("view", $rn_i))
				{
					continue;
				}
				$has_items[$rn_i] = 1;
				$rn_o = obj($rn_i);
				$tree->add_item(0,array(
					"id" => $rn_i,
					"parent" => 0,
					"name" => parse_obj_name($rn_o->trans_get_val("name")),
					"iconurl" => icons::get_icon_url($rn_o),
					"url" => aw_url_change_var("parent", $rn_o->id(), $this->curl)
				));
			}
			$this->force_0_parent= true;
		}
		else
		{
			if (is_array($rn))
			{
				$rn = reset($rn);
			}
		}
		$filt = array(
			"class_id" => array(CL_MENU, CL_BROTHER, CL_GROUP),
			"parent" => $rn,
			"CL_MENU.type" => new obj_predicate_not(array(MN_FORM_ELEMENT, MN_HOME_FOLDER)),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
						"lang_id" => aw_global_get("lang_id"),
						"CL_MENU.type" => array(MN_CLIENT, MN_ADMIN1)
				)
			)),
			"sort_by" => "objects.parent,objects.jrk,objects.created"
		);
		$ol = new object_data_list(
			$filt,
			$this->data_list_ot_flds
		);

		$second_level_parents = array();
		foreach($ol->arr() as $menu)
		{
			if (isset($has_items[$menu["oid"]]))
			{
				continue;
			}
			$rs = $this->resolve_item_new_arr($menu);
			if ($rs !== false)
			{
				$tree->add_item($rs["parent"], $rs);
				$has_items[$rs["oid"]] = 1;
				// also, gather all id's of objects that were inserted in the tree, so that
				// we can also get their submenus so that the tree know is they have subitems
				$second_level_parents[$rs["id"]] = $rs["id"];
			}
		}

		if (count($second_level_parents))
		{
			$ol = new object_data_list(array(
				"class_id" => array(CL_MENU, CL_BROTHER, CL_GROUP),
				"parent" => $second_level_parents,
				"CL_MENU.type" => new obj_predicate_not(array(MN_FORM_ELEMENT, MN_HOME_FOLDER)),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
							"lang_id" => aw_global_get("lang_id"),
							"CL_MENU.type" => array(MN_CLIENT, MN_ADMIN1)
					)
				)),
				"sort_by" => "objects.parent,objects.jrk,objects.created"
			), $this->data_list_ot_flds);
			foreach($ol->arr() as $menu)
			{
				if (isset($has_items[$menu["oid"]]))
				{
					continue;
				}
				$rs = $this->resolve_item_new_arr($menu);
				if ($rs !== false)
				{
					$tree->add_item($rs["parent"], $rs);
					$has_items[$rs["id"]] = 1;
				}
			}
		}

		$this->tree = $tree;
		if (empty($this->use_parent))
		{
			$this->mk_home_folder_new();

			// shortcuts for the programs
			$this->mk_admin_tree_new();
		}

		if (!isset($set_by_p))
		{
			$set_by_p = null;
		}
		$tree->set_rootnode($this->force_0_parent || (empty($this->use_parent) && $set_by_p) ? 0 : $rn);
	}

	/** Branch func for main tree
		@attrib name=gen_folders
		@param period optional
		@param parent optional
		@param curl optional
		@param selp optional
	**/
	function gen_folders($arr)
	{
		$t = new treeview();
		$this->use_parent = (int)$arr["parent"];
		$this->_get_o_tree(array(
			"prop" => array(
				"vcl_inst" => $t
			),
			"request" => $arr
		));
		header("Content-type: text/html; charset=" . languages::USER_CHARSET);
		die($t->finalize_tree());
	}

	private function resolve_item_new_arr($m, $adminf = null)
	{
		if ($this->period > 0 && $m["periodic"] != 1)
		{
			return false;
		}

		$iconurl = "";
		if ($m["class_id"] == CL_BROTHER)
		{
			$iconurl = icons::get_icon_url("brother","");
		}
		elseif ($adminf > 0)
		{
			$iconurl = icons::get_feature_icon_url($adminf);
		}

		// if all else fails ..
		$m["iconurl"] = $iconurl;

		if ($adminf)
		{
			$prog = aw_ini_get("programs");
			$m["url"] = $prog[$adminf]["url"];

			if (empty($m["url"]))
			{
				$m["url"] = "about:blank";
			}
		}
		else
		{
			$m["url"] = aw_url_change_var("parent", $m["oid"], $this->curl);
		}

		$m["name"] = parse_obj_name($this->_fake_trans_get_val_name($m));
		if ($this->selp == $m["oid"])
		{
			$m["name"] = html::bold($m["name"]);
		}

		$m["id"] = $m["oid"];
		return $m;
	}

	private function mk_home_folder_new()
	{
		$ucfg = new object(aw_global_get("uid_oid"));
		if (!$this->can("view", $ucfg->prop("home_folder")))
		{
			return;
		}
		$hf = new object($ucfg->prop("home_folder"));
		// add home folder
		$rn = empty($this->use_parent) ? aw_ini_get("admin_rootmenu2") : $this->use_parent;
		$this->tree->add_item(is_array($rn) ? reset($rn) : $rn,array(
			"id" => $hf->id(),
			"parent" => $this->force_0_parent ? 0 : (is_array($rn) ? reset($rn) : $rn),
			"name" => parse_obj_name($hf->trans_get_val("name")),
			"iconurl" => icons::get_icon_url("homefolder",""),
			"url" => aw_url_change_var("parent",$hf->id(), $this->curl),
		));
		$ol = new object_data_list(array(
			"class_id" => array(CL_MENU, CL_BROTHER, CL_GROUP),
			"parent" => $hf->id(),
			"CL_MENU.type" => new obj_predicate_not(array(MN_HOME_FOLDER)),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
						"lang_id" => aw_global_get("lang_id"),
						"CL_MENU.type" => array(MN_CLIENT, MN_ADMIN1)
				)
			)),
			"site_id" => array(),
			"sort_by" => "objects.parent,objects.jrk,objects.created"
		), $this->data_list_ot_flds);
		foreach($ol->arr() as $menu)
		{
			$rs = $this->resolve_item_new_arr($menu);
			if ($rs !== false)
			{
				$this->tree->add_item($rs["parent"], $rs);
			}
		}

	}

	private function mk_admin_tree_new()
	{
		// make this one level only, so we save a lot on the headaches
		$tmp = $this->data_list_ot_flds;
		$tmp[CL_MENU]["admin_feature"] = "admin_feature";
		$ol = new object_data_list(array(
			"class_id" => CL_MENU,
			"parent" => aw_ini_get("amenustart"),
			"status" => STAT_ACTIVE,
			"CL_MENU.type" => MN_ADMIN1,
			"sort_by" => "objects.parent,objects.jrk,objects.created"
		), $tmp);
		$rn = empty($this->use_parent) ? aw_ini_get("admin_rootmenu2") : $this->use_parent;
		$rn = is_array($rn) ? reset($rn) : $rn;
		if ($this->force_0_parent)
		{
			$rn = 0;
		}
		$tmp = $this->period;
		$this->period = null;
		foreach($ol->arr() as $menu)
		{
			$rs = $this->resolve_item_new_arr($menu, $menu["admin_feature"]);
			if ($rs !== false)
			{
				$rs["id"] .= "ad";
				$rs["parent"] = $rn;
				$this->tree->add_item($rs["parent"], $rs);
			}
		}
		$this->period = $tmp;
	}

	private function setup_rf_table($t, $row_count, $per_page)
	{
		$t->define_field(array(
			"name" => "icon",
			"align" => "center",
			"chgbgcolor" => "cutcopied" ,
			"width" => "22"
		));

		$t->define_field(array(
			"name" => "name",
			"align" => "left",
			"talign" => "center",
			"sortable" => 1,
			"chgbgcolor" => "cutcopied",
			"caption" => t("Nimi")
		));

		$t->define_field(array(
			"name" => "jrk",
			"align" => "center",
			"width" => 10,
			"talign" => "center",
			"chgbgcolor" => "cutcopied",
			"caption" => t("Jrk"),
			"numeric" => "yea",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "status",
			"caption" => t("Aktiivne"),
			"width" => 10,
			"align" => "center",
			"talign" => "center",
			"chgbgcolor" => "cutcopied",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"width" => 50,
			"align" => "center",
			"talign" => "center",
			"sortable" => 1,
			"chgbgcolor" => "cutcopied"
		));

		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"width" => 100,
			"align" => "center",
			"talign" => "center",
			"type" => "time",
			"format" => "d-M-y / H:i",
			"sortable" => 1,
			"numeric" => 1,
			"chgbgcolor" => "cutcopied"
		));

		$t->define_field(array(
			"name" => "class_id",
			"caption" => t("T&uuml;&uuml;p"),
			"width" => 100,
			"align" => "center",
			"talign" => "center",
			"sortable" => 1,
			"chgbgcolor" => "cutcopied"
		));

		$t->define_field(array(
			"name" => "java",
			"width" => 30,
			"align" => "center",
			"talign" => "center",
			"chgbgcolor" => "cutcopied"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"chgbgcolor" => "cutcopied",
			"field" => "oid"
		));

		// make pageselector.
		$t->d_row_cnt = $row_count;
		if ($t->d_row_cnt > $per_page)
		{
			$t->define_pageselector(array(
				"type" => "lbtxt",
				"records_per_page" => $per_page,
				"d_row_cnt" => $t->d_row_cnt
			));
			$pageselector = $t->draw_lb_pageselector(array(
				"records_per_page" => $per_page
			));
		}
	}

	function _get_o_tbl($arr)
	{
		aw_global_set("date","");
		$per_page = 100;
		$period = !empty($arr["request"]["period"]) ? $arr["request"]["period"] : null;
		$parent = $this->_resolve_tbl_parent($arr);
		$containers = get_container_classes();
		$clss = aw_ini_get("classes");
		$sel_objs = $this->get_cutcopied_objects();

		$filt = $this->_get_object_list_filter($parent, $period);
		$ob_cnt = new object_data_list(
			$filt,
			array(
				"" => array(new obj_sql_func(OBJ_SQL_COUNT, "cnt", "*"))
			)
		);
		$tmp = $ob_cnt->arr();
		$object_count = $tmp[0]["cnt"];

		$ft_page = isset($arr["request"]["ft_page"]) ? $arr["request"]["ft_page"] : null;
		$filt[] = new obj_predicate_limit($per_page, $ft_page * $per_page);

		$ob = new object_data_list(
			$filt,
			$this->data_list_ot_flds
		);

		$t = $arr["prop"]["vcl_inst"];
		$this->setup_rf_table($t, $object_count, $per_page);

		$this->_init_admin_modifier_list();
		foreach($ob->arr() as $row_d)
		{
			$row = array(
				"modifiedby" => $row_d["modifiedby"],
				"modified" => $row_d["modified"],
				"oid" => $row_d["oid"]
			);
			$can_change = $this->can("edit", $row_d["oid"]);

			$row["is_menu"] = 0;
			if (in_array($row_d["class_id"],$containers))
			{
				$chlink = aw_url_change_var("parent", $row_d["oid"]);
				$row["is_menu"] = 1;
			}
			else
			{
				if ($can_change)
				{
					$chlink = $this->mk_my_orb("change", array("id" => $row_d["oid"], "period" => $period),$row_d["class_id"]);
				}
				else
				{
					$chlink = $this->mk_my_orb("view", array("id" => $row_d["oid"], "period" => $period),$row_d["class_id"]);
				}
			}

			$row["name"] = html::href(array(
				"url" => $chlink,
				"title" => strip_tags($row_d["comment"]),
				"caption" => parse_obj_name($this->_fake_trans_get_val_name($row_d))
			));

			$row["cutcopied"] = isset($sel_objs[$row_d["oid"]]) ? "#E2E2DB" : "#FCFCF4";

			$row["java"] = $this->get_popup_data(array(
				"obj" => $row_d,
				"period" => $period
			));
			$title = sprintf(t("Objekti id on %s"), $row_d["oid"]);
			$row["icon"] = html::img(array(
				"url" => icons::get_icon_url($row_d["class_id"],$row_d["name"]),
				"alt" => $title,
				"title" => $title,
			));

			try
			{
				$row["class_id"] = aw_ini_get("classes." . $row_d["class_id"] . ".name");
			}
			catch (Exception $e)
			{
				$row["class_id"] = t("[klassi ei leitud]");
			}

			if ($row["oid"] != $row_d["brother_of"])
			{
				$row["class_id"] .= " (vend)";
			}

			$row["hidden_jrk"] = $row_d["ord"];
			$row["status_val"] = $row_d["status"];

			if ($can_change)
			{
				$row["jrk"] = html::hidden(array(
					"name" => "old[jrk][".$row_d["oid"]."]",
					"value" => $row_d["ord"]
				)).html::textbox(array(
					"name" => "new[jrk][".$row_d["oid"]."]",
					"value" => $row_d["ord"],
					"class" => "formtext",
					"size" => "3"
				));
				$row["status"] = html::hidden(array(
					"name" =>  "old[status][".$row_d["oid"]."]",
					"value" => $row_d["status"]
				)).html::checkbox(array(
					"name" => "new[status][".$row_d["oid"]."]",
					"value" => "2",
					"checked" => ($row_d["status"] == STAT_ACTIVE)
				));
				$row["select"] = html::checkbox(array(
					"name" => "sel[".$row_d["oid"]."]",
					"value" => "1"
				));
			}
			else
			{
				$row["status"] = $row_d["status"] == STAT_NOTACTIVE ? t("Mitteaktiivne") : t("Aktiivne");
				$row["select"] = "&nbsp;";
			}

			$this->_call_admin_modifier_for_row($row_d["class_id"], $row);

			$t->define_data($row);
		}
		$this->_do_o_tbl_sorting($t, $parent);
	}

	private function _do_o_tbl_sorting($t, $parent)
	{
		$sortby = empty($_GET["sortby"]) ? "hidden_jrk" : $_GET["sortby"];

		if($sortby === "status")
		{
			$sortby = "status_val";
		}

		if (isset($sortby) && $sortby === "jrk")
		{
			$sortby = "hidden_jrk";
		};

		if (empty($_GET["sort_order"]))
		{
			$_GET["sort_order"] = "asc";
		};

		$t->set_default_sortby(array("is_menu", "name"));
		$t->set_default_sorder("desc");
		$t->set_numeric_field("hidden_jrk");

		if($sortby == "name")
		{
			$t->sort_by(array(
				"field" => array("is_menu", "name"),
				"sorder" => array("is_menu" => "desc", $sortby => $_GET["sort_order"]),
			));
		}
		else
		{
			// if document order is set from folder then use it
			$menu_obj = obj($parent);
			if ($menu_obj->prop("doc_ord_apply_to_admin")==1 && !isset($_GET["sort_order"])  )
			{
				$a_sort_fields = new aw_array($menu_obj->meta("sort_fields"));
				$a_sort_order = new aw_array($menu_obj->meta("sort_order"));

				$a_fields = array("is_menu");
				foreach($a_sort_fields->get() as $key => $val)
				{
					$a_field = split  ( "\.", $val);
					$a_fields[] = $a_field[1];
				}

				$a_sorder = array("is_menu" => "desc");
				$i=1;
				foreach($a_sort_order->get() as $key => $val)
				{
					$a_sorder[$a_fields[$i]] = strtolower($val);
					$i++;
				}

				$t->sort_by(array(
					"field" => $a_fields,
					"sorder" => $a_sorder
				));
			}
			else
			{
				$t->sort_by(array(
					"field" => array("is_menu", $sortby, "name"),
					"sorder" => array("is_menu" => "desc", $sortby => $_GET["sort_order"],"name" => "asc")
				));
			}
		}
		$t->set_sortable(false);
	}

	private function get_popup_data($args = array())
	{
		$obj = $args["obj"];
		$id = $obj["oid"];
		$parent = $obj["parent"];
		$clid = $obj["class_id"];
		$period = $args["period"];

		$this->pm->begin_menu("aif_".$obj["oid"]);

		$this->pm->add_item(array(
			"text" => t("Ava"),
			"link" => aw_url_change_var("parent", $id)
		));

		try
		{
			$class = basename(aw_ini_get("classes.{$clid}.file"));
			if ($this->can("edit", $id))
			{
				$this->pm->add_item(array(
					"link" => sprintf($this->change_url_template, $class, $id, $parent, $period).$this->post_ru_append,
					"text" => t("Muuda")
				));

				$this->pm->add_item(array(
					"link" => sprintf($this->if_cut_url_template, $id, $parent, $id).$this->post_ru_append,
					"text" => t("L&otilde;ika")
				));
			}
		}
		catch (Exception $e)
		{
		}

		$this->pm->add_item(array(
			"link" => sprintf($this->if_copy_template, $id, $parent, $id, $period).$this->post_ru_append,
			"text" => t("Kopeeri")
		));

		if ($this->can("delete", $id))
		{
			$delurl = sprintf($this->if_delete_template, $_GET["id"], $id, $parent, $id, $period);
			$delurl = "javascript:if(confirm('".t("Kustutada valitud objektid?")."')){window.location='$delurl';};";

			$this->pm->add_item(array(
				"link" => $delurl,
				"text" => t("Kustuta")
			));
		}

		return $this->pm->get_menu();
	}

	private function generate_new($tb, $i_parent, $period)
	{
		$cache_key = self::NEW_MENU_CACHE_KEY . aw_global_get("uid");
		$atc = new add_tree_conf();

		// although fast enough allready .. caching makes it 3 times as fast
		if(aw_ini_get("admin_if.cache_toolbar_new"))
		{
			// $tree = cache::file_get($cache_key);
			// $tree = unserialize($tree);
		}

		if(!isset($tree) or !is_array($tree))
		{
			$tree = $atc->get_class_tree(array(
				"az" => 1,
				"docforms" => 1,
				// those are for docs menu only
				"parent" => "--pt--",
				"period" => "--pr--"
			));
			cache::file_set($cache_key, serialize($tree));//XXX: tundub m6ttetu, get_class_tree v6iks ise cacheda kui ainult seda vaja cacheda
		}

		$new_url_template = str_replace("__", "%s", core::mk_my_orb("new", array("parent" => $i_parent), "__"));

		foreach($tree as $item_id => $item_collection)
		{
			foreach($item_collection as $el_id => $el_data)
			{
				$parnt = ($item_id === "root" ? "new" : $item_id);

				if (!empty($el_data["clid"]))
				{
					$url = sprintf($new_url_template, basename($el_data["file"]));
					$tb->add_menu_item(array(
						"name" => (empty($el_data["id"]) ? $el_id : $el_data["id"]),
						"parent" => $parnt,
						"text" => $el_data["name"],
						"url" => $url
					));
				}
				elseif (!empty($el_data["link"]))
				{
					$url =  str_replace(array("--pt--", "--pr--"), array($i_parent, $period), $el_data["link"]);

					// docs menu has links ..
					$tb->add_menu_item(array(
						"name" => (empty($el_data["id"]) ? $el_id : $el_data["id"]),
						"parent" => $parnt,
						"text" => $el_data["name"],
						"url" => $url
					));
				}
				else
				{
					$tb->add_sub_menu(array(
						"name" => (empty($el_data["id"]) ? $el_id : $el_data["id"]),
						"parent" => $parnt,
						"text" => $el_data["name"]
					));
				}

				if (!empty($el_data["separator"]))
				{
					$tb->add_menu_separator(array(
						"parent" => $parnt
					));
				}
			}
		}
	}

	/**
		@attrib name=save_if
	**/
	function save_if($arr)
	{
		extract($arr);
		if (is_array($old))
		{
			foreach($old as $column => $coldat)
			{
				foreach($coldat as $oid => $oval)
				{
					$val = isset($new[$column][$oid]) ? $new[$column][$oid] : 0;
					if ($column === "status" && $val == 0)
					{
						$val = 1;
					}

					if ($val != $oval)
					{
						if ($this->can("edit", $oid) && $this->can("view", $oid))
						{
							$o = obj($oid);
							if ($column === "jrk")
							{
								$o->set_ord((int)$val);
							}
							else
							{
								$o->set_prop($column, $val);
							}

							if($all_trans_status != 0 && $column === "status")
							{
								$languages_in_use = languages::list_translate_targets();
								if($languages_in_use->count())
								{
									$lang_o = $languages_in_use->begin();

									do
									{
										$lid = $lang_o->prop("aw_lang_id");
										$o->set_meta("trans_".$lid."_status", ($val - 1));
									}
									while ($lang_o = $languages_in_use->next());
								}
								$langs = aw_ini_get("languages");
								/*
								foreach($o->meta("translations") as $lid => $ldata)
								{
									$o->set_meta("trans_".$lid."_status", ($val - 1));
								}
								*/
							}
							$o->save();
						}
					}
				}
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=if_cut all_args=1
	**/
	function if_cut($arr)
	{
		extract($arr);

		$cut_objects = array();
		if (is_array($sel))
		{
			foreach($sel as $oid => $one)
			{
				$cut_objects[$oid] = $oid;
			}
		}

		aw_session_set("cut_objects",$cut_objects);

		if (!empty($arr['return_url']))
		{
			return $arr['return_url'];
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=if_copy all_args=1
	**/
	function if_copy($arr)
	{
		extract($arr);

		if (aw_ini_get("admin_if.no_copy_feedback"))
		{
			return $this->mk_my_orb("submit_copy_feedback", array("ret_to_orb" => 1, "reforb" => 1, "ser_type" => 2, "ser_rels" => 1, "parent" => $parent, "period" => $period, "sel" => $sel, "return_url" => $return_url, "login" => $login));
		}

		return $this->mk_my_orb("copy_feedback", array("parent" => $parent, "period" => $period, "sel" => $sel, "return_url" => $return_url, "login" => $login));
	}

	/**

		@attrib name=copy_feedback params=name default="0"

		@param parent optional
		@param period optional
		@param sel optional
		@param return_url optional
		@param login optional

		@returns


		@comment

	**/
	function copy_feedback($arr)
	{
		extract($arr);
		$this->mk_path($parent, t("Vali kuidas objekte kopeerida"));

		$hc = get_instance("cfg/htmlclient", array(
			"tabs" => true,
		));
		$hc->add_tab(array(
			"active" => true,
			"caption" => t("Objekti kopeerimine"),
		));
		$hc->start_output();
		$hc->add_property(array(
			"name" => "objects",
			"type" => "text",
			"caption" => "&nbsp;",
			"value" => "<b>".t("Objektid")."</b>",
		));
		$hc->add_property(array(
			"name" => "ser_type",
			"type" => "chooser",
			"orient" => "vertical",
			"options" => array(
				"2" => t("Kopeeri alamobjektid"),
				"1" => t("Kopeeri alammen&uuml;&uuml;d"),
				"3" => t("Kopeeri dokumendid"),
			),
			"value" => 2
		));
		$hc->add_property(array(
			"name" => "rels",
			"type" => "text",
			"caption" => "&nbsp;",
			"value" => "<b>".t("Seosed")."</b>",
		));
		$hc->add_property(array(
			"name" => "ser_rels",
			"type" => "chooser",
			"orient" => "vertical",
			"options" => array(
				"1" => t("Seosta samade objektidega"),
				"2" => t("Loo uued seotud objektid"),
			),
			"value" => 1
		));
		$hc->add_property(array(
			"name" => "submit_override",
			"type" => "submit",
			"caption" => t("Kopeeri"),
		));
		$hc->finish_output(array(
			"data" => array(
				"action" => "submit_copy_feedback",
				"login" => $login,
				"parent" => $parent,
				"period" => $period,
				"sel" => $sel,
				"orb_class" => "admin_if",
				"return_url" => $return_url,
			),
		));

		$props = $hc->get_result(array(
			//"form_only" => 1,
		));

		return $props;
	}

	/**

		@attrib name=submit_copy_feedback params=name default="0"


		@returns


		@comment

	**/
	function submit_copy_feedback($arr)
	{
		extract($arr);
		$params = array(
			"copy_subobjects" => $ser_type == 2 ? 1 : 0,
			"copy_subfolders" => $ser_type == 1 ? 1 : 0,
			"copy_subdocs" => $ser_type == 3 ? 1 : 0,
			"copy_rels" => $ser_rels == 1 ? 1 : 0,
			"new_rels" => $ser_rels == 2 ? 1 : 0
		);
		$copied_objects = array();
		if (is_array($sel))
		{
			if (!empty($login))
			{
				$rels = array();
				if (is_array($sel))
				{
					// ok, so how do I add objects to here?
					foreach($sel as $oid => $one)
					{
	//					aw_global_set("xmlrpc_dbg",1);
						$r = $this->_search_mk_call(array("oid" => $oid, "encode" => 1),$login);
						$r = base64_decode($r);
						if ($r !== false)
						{
							if (is_array($r["connections"]))
							{
								$rels = $rels + $r["connections"];
							};
							$copied_objects[$oid] = $r;
							$ra = aw_unserialize($r);
	//					echo "r = $r <br />ra = <pre>", var_dump($ra),"</pre> <br />";
						}
					}
				}
				foreach($rels as $rel_id)
				{
					$r = $this->_search_mk_call(array("oid" => $rel_id["to"], "encode" => 1), $login);
					$r = base64_decode($r);
					if ($r !== false)
					{
						$copied_objects[$rel_id["to"]] = $r;
					};
				};
			}
			else
			{
				foreach($sel as $oid => $one)
				{
					$o = obj($oid);
					$copied_objects[$oid] = $o->get_xml($params);
				}
			}
		}
		aw_session_set("copied_objects", $copied_objects);
		return !empty($return_url) ? $return_url : self::get_link_for_obj($parent,$period);
	}

	function _search_mk_call($params, $login = null)
	{
		$_parms = array(
			"class" => "objects",
			"action" => "get_xml",
			"params" => $params
		);
		if (!empty($login))
		{
			$_parms["method"] = "xmlrpc";
			$_parms["login_obj"] = $login;
		}
		$ret =  $this->do_orb_method_call($_parms);
		return $ret;
	}

	/** pastes the cut objects
		@attrib name=if_paste params=name default="0" all_args=1
	**/
	function if_paste($arr)
	{
		if (!$arr["parent"])
		{
			$arr["parent"] = aw_ini_get("rootmenu");
			if (is_array($arr["parent"]))
			{
				$arr["parent"] = reset($arr["parent"]);
			}
		}

		foreach(safe_array(aw_global_get("cut_objects")) as $oid)
		{
			if ($oid != $arr["parent"])
			{
				$o = obj($oid);
				$this->_do_cut_one_obj($o, $arr["parent"], $arr["period"]);
			}
		}
		$_SESSION["cut_objects"] = false;

		foreach(safe_array(aw_global_get("copied_objects")) as $oid => $xml)
		{
			$o = new object();
			$oid = $o->from_xml($xml, $arr["parent"]);
		}
		$_SESSION["copied_objects"] = false;

		if (!empty($arr['return_url']))
		{
			return $arr['return_url'];
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=if_delete params=name default="0" all_args=1
	**/
	function if_delete($arr)
	{
		extract($arr);
		if (is_array($sel))
		{
			$ol = new object_list(array(
				"oid" => array_keys($sel)
			));
			for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				if ($this->can("delete", $o->id()))
				{
					$o->delete();
				}
			}
		}

		if (!empty($arr["post_ru"]))
		{
			return $arr["post_ru"];
		}

		return $this->mk_my_orb("change", array(
			"id" => $arr["ret_id"],
			"parent" => $arr["parent"],
			"period" => $arr["period"],
			"group" => "o"
		));
	}

	/**
		@attrib name=redir
		@param parent optional
	**/
	function redir($arr)
	{
		return html::get_change_url(self::find_admin_if_id(), array("group" => "o", "parent" => isset($arr["parent"]) ? $arr["parent"] : ""));
	}

	/** returns the admin if id
		@attrib api=1
	**/
	public static function find_admin_if_id()
	{
		if (!empty($_SESSION["cur_admin_if"]))
		{
			return $_SESSION["cur_admin_if"];
		}
		$ol = new object_list(array(
			"class_id" => CL_ADMIN_IF
		));
		if ($ol->count())
		{
			$o = $ol->begin();
		}
		else
		{
			$o = obj();
			$o->set_parent(aw_ini_get("amenustart"));
			$o->set_class_id(CL_ADMIN_IF);
			$o->set_name(t("Administreerimisliides"));
			aw_disable_acl();
			$o->save();
			aw_restore_acl();
		}

		$_SESSION["cur_admin_if"] = $o->id();
		return $o->id();
	}

	function callback_mod_tab($arr)
	{
		if (isset($arr["request"]["integrated"]) && $arr["request"]["integrated"] == 1 && $arr["id"] == "o")
		{
			return false;
		}
		if (isset($arr["request"]["group"]) && $arr["request"]["group"] == "fu" && $arr["id"] == "fu")
		{
			return true;
		}
		if ($arr["id"] != "o")
		{
			return false;
		}
		return true;
	}

	/** Used from admin_footer so that all texts are translatable. this can't be in admin_footer, cause only files under classes folder are translatable. **/
	function insert_texts($t)
	{
		$t->vars(array(
			"logout_text" => t("Logi v&auml;lja"),
			"logged_in_text" => t("Kasutaja:"),
			"location_text" => t("Asukoht:"),
			"footer_l1" => sprintf(t("AutomatWeb&reg; on registreeritud kaubam&auml;rk. K&otilde;ik &otilde;igused kaitstud, &copy; 1999-%s."), date("Y")),
			"footer_l2" => t("Palun k&uuml;lasta meie kodulehek&uuml;lgi:"),
			"st" => t("Seaded")
		));
	}

	function _set_zip_upload($arr)
	{
		if (is_uploaded_file($_FILES["zip_upload"]["tmp_name"]))
		{
			$zip = $_FILES["zip_upload"]["tmp_name"];
			// unzip the damn thing
			if (extension_loaded("zip"))
			{
				$folder = aw_ini_get("server.tmpdir")."/".gen_uniq_id();
				mkdir($folder, 0777);
				$tn = $folder;
				$zip = zip_open($zip);
				while ($zip_entry = zip_read($zip))
				{
					zip_entry_open($zip, $zip_entry, "r");
					$fn = $folder."/".zip_entry_name($zip_entry);
					$files[] = $fn;
					$fc = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					$this->put_file(array(
						"file" => $fn,
						"content" => $fc
					));
				}
			}
			else
			{
				$zf = escapeshellarg($zip);
				$zip = aw_ini_get("server.unzip_path");
				$tn = aw_ini_get("server.tmpdir")."/".gen_uniq_id();
				mkdir($tn,0777);
				$cmd = $zip." -d {$tn} {$zf}";
				$op = shell_exec($cmd);


				$files = array();
				if ($dir = opendir($tn))
				{
					while (($file = readdir($dir)) !== false)
					{
						if (!($file === "." || $file === ".."))
						{
							$files[] = $tn."/".$file;
						}
					}
					closedir($dir);
				}
			}

			foreach($files as $file)
			{
				$fuc = new file_upload_config();
				if (!$fuc->can_upload_file(array("folder" => $arr["request"]["parent"], "file_name" => $file, "file_size" => filesize($file))))
				{
					continue;
				}
				$fi = new file();
				$rv = $fi->save_file(array(
					"name" => basename($file),
					"type" => aw_mime_types::type_for_file($file),
					"content" => file_get_contents($file),
					"parent" => $arr["request"]["parent"]
				));
				$s = sprintf(t("Leidsin faili %s, l&otilde;in AW objekti %s<br>\n"), basename($file), html::obj_change_url($rv));
				echo $s;
				$_SESSION["fu_tm_text"] .= $s;
				flush();
				unlink($fp);
			}

			rmdir($tn);
			echo "<script language=\"javascript\">window.location='".$arr["request"]["post_ru"]."'</script>";
		}
	}

	function _get_uploader($arr)
	{
		if (!$arr["request"]["parent"])
		{
			$arr["request"]["parent"] = aw_ini_get("rootmenu");
		}
		$_SESSION["fu_parent"] = $arr["request"]["parent"];
		$this->read_template("flash_uploader.tpl");
		$this->lc_load("menuedit", "lc_menuedit");
		$this->vars(array(
			"uploadurl" => urlencode($this->mk_my_orb("handle_upload", array("parent" => $arr["request"]["parent"]))),
			"redir_to" =>  urlencode(get_ru()),
		));
		$arr["prop"]["value"] = $this->parse();
	}

	/**
		@attrib name=handle_upload
		@param parent required
	**/
	function handle_upload($arr)
	{
		if (!$arr["parent"])
		{
			$arr["parent"] = aw_ini_get("rootmenu");
		}
		if (is_uploaded_file($_FILES["Filedata"]["tmp_name"]))
		{
			$fuc = new file_upload_config();
			if (!$fuc->can_upload_file(array("folder" => $arr["parent"], "file_name" => $_FILES["Filedata"]["name"], "file_size" => $_FILES["Filedata"]["size"])))
			{
				continue;
			}
			$fi = new file();
			$rv = $fi->save_file(array(
				"name" => $_FILES["Filedata"]["name"],
				"type" => $_FILES["Filedata"]["type"],
				"content" => file_get_contents($_FILES["Filedata"]["tmp_name"]),
				"parent" => $arr["parent"]
			));
			$s = sprintf(t("Leidsin faili %s, l&otilde;in AW objekti %s<br>\n"), $_FILES["Filedata"]["name"], html::obj_change_url($rv));
			$_SESSION["fu_tm_text"] .= $s;
		}
	}

	private function _init_admin_modifier_list()
	{
		$this->modifiers_by_clid = array();
		foreach(class_index::get_classes_by_interface("admin_if_plugin") as $class_name)
		{
			$this->modifiers_by_clid[clid_for_name($class_name)] = $class_name;
		}
	}

	private function _call_admin_modifier_for_row($class_id, $row)
	{
		if (isset($this->modifiers_by_clid[$class_id]))
		{
			$inst = get_instance($this->modifiers_by_clid[$class_id]);
			$inst->admin_if_modify_data($row);
		}
	}

	private function _do_cut_one_obj($o, $parent, $period)
	{
		// so, let the object update itself when it is being cut-pasted, if it so desires
		$inst = $o->instance();
		if (method_exists($inst, "cut_hook"))
		{
			$inst->cut_hook(array(
				"oid" => $o->id(),
				"new_parent" => $parent
			));
		}

		// if site id changes after parent change, then update sub-objects as well
		$old_site_id = $o->site_id();
		$o->set_parent($parent);
		if ($old_site_id != $o->site_id())
		{
			$ot = new object_tree(array(
				"parent" => $o->id(),
				"site_id" => $old_site_id
			));
			$ot->to_list()->foreach_o(array(
				"func" => "set_site_id",
				"params" => array($o->site_id()),
				"save" => true
			));
		}

		if ($period)
		{
			$o->set_period($period);
		}

		if (aw_global_get("lang_id") != $o->lang_id())
		{
			// change all objects lang id's below this one as well on cut from one lang to another
			$ot = new object_tree(array(
				"parent" => $o->id(),
				"lang_id" => $o->lang_id(),
				"site_id" => $old_site_id
			));
			$ot->to_list()->foreach_o(array(
				"func" => "set_lang_id",
				"params" => array(aw_global_get("lang_id")),
				"save" => true
			));

			$o->set_lang_id(aw_global_get("lang_id"));
		}

		if ($o->can("edit"))
		{
			$o->save();
		}
	}

	private function _resolve_tbl_parent($arr)
	{
		$parent = !empty($arr["request"]["parent"]) ? $arr["request"]["parent"] : aw_ini_get("rootmenu");
		if (!$this->can("view", $parent))
		{
			return null;
		}
		$menu_obj = new object($parent);
		if ($menu_obj->is_brother())
		{
			$menu_obj = $menu_obj->get_original();
			$parent = $menu_obj->id();
		}
		return $parent;
	}

	private function _get_object_list_filter($parent, $period)
	{
		$filter = array(
			"parent" => $parent,
			new object_list_filter(array(
				"logic" => "OR",
				"non_filter_classes" => CL_MENU,
				"conditions" => array(
					"lang_id" => aw_global_get("lang_id"),
					"class_id" => array(CL_PERIOD, CL_USER, CL_GROUP, CL_MSGBOARD_TOPIC, CL_LANGUAGE),
					"type" => MN_CLIENT
				)
			)),
			"class_id" => new obj_predicate_not(CL_RELATION),
			"site_id" => array(),
		);
		$menu_obj = obj($parent);
		if (!$menu_obj->prop("all_pers"))
		{
			if (!empty($period))
			{
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"period" => $period,
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"class_id" => CL_MENU,
								"periodic" => 1
							)
						))
					)
				));
			}
			// if no period is set in the url, BUT the menu is periodic, then only show objects from the current period
			// this fucks shit up. basically, a periodic menu can have non-periodic submenus
			// in that case there really is no way of seeing them
			else
			{
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"period" => new obj_predicate_compare(OBJ_COMP_NULL),
							)
						)),
						new object_list_filter(array(
							"logic" => "OR",
							"conditions" => array(
								"period" => new obj_predicate_compare(OBJ_COMP_LESS, 1)
							)
						)),
						"class_id" => CL_USER
					)
				));
			}
		}

		if (!empty($_GET["sortby"]))
		{
			if ($_GET["sortby"] === "hidden_jrk")
			{
				$filter[] = new obj_predicate_sort(array(
					"type" => "desc",	// this makes sure menus are first
					"jrk" => $_GET["sort_order"]
				));
			}
			else
			{
				$filter[] = new obj_predicate_sort(array(
					"type" => "desc",	// this makes sure menus are first
					$_GET["sortby"] => $_GET["sort_order"]
				));
			}
		}
		return $filter;
	}

	/** Returns a link that displays objects under the given oid
		@attrib api=1 params=pos

		@param parent required type=oid
			The object id to display objects under

		@param period optional type=int
			The period to display
	**/
	static public function get_link_for_obj($parent, $period = null)
	{
		return html::get_change_url(self::find_admin_if_id(), array("group" => "o", "parent" => $parent, "period" => $period));
	}

	/** shows menus importing form
		@attrib name=import params=name default="0"

		@param parent required
	**/
	function import($arr)
	{
		extract($arr);
		$this->mk_path($parent,t("Impordi men&uuml;&uuml;sid"));

		$htmlc = new htmlclient();
		$htmlc->start_output();
		$htmlc->add_property(array(
			"name" => "fail",
			"type" => "fileupload",
			"caption" => t("Vali fail"),
		));
		$htmlc->add_property(array(
			"name" => "file_type",
			"type" => "chooser",
			"caption" => t("Vali faili t&uuml;&uuml;p"),
			"options" => array(
				"aw" => t("Eksporditud AW'st"),
				"text" => t("Tekstifail")
			)
		));
		$htmlc->add_property(array(
			"name" => "sbt",
			"type" => "submit",
			"caption" => t("Impordi"),
		));
		$htmlc->finish_output(array("data" => array(
				"class" => get_class($this),
				"action" => "submit_import",
				"id" => $id,
				"parent" => $arr["parent"]
			),
		));

		$html = $htmlc->get_result(array(
			"form_only" => 1
		));

		$tp = new tabpanel();
		$tp->add_tab(array(
			"active" => true,
			"caption" => t("Impordi"),
			"link" => get_ru(),
		));

		return $tp->get_tabpanel(array(
			"content" => $html
		));
	}

	/** does the actual menu importing bit

		@attrib name=submit_import params=name default="0"

		@param parent required

		@returns


		@comment

	**/
	function submit_import($arr)
	{
		extract($arr);

		if ($file_type == "text")
		{
			$this->do_text_import($arr);
		}
		else
		{
			$fail = $_FILES["fail"]["tmp_name"];
			$f = fopen($fail, "r");
			if ($f)
			{
				$d = fread($f,filesize($fail));
				fclose($f);
			}
			else
			{
				return $this->mk_my_orb("import",array("parent" => $parent));
			};

			$menus = unserialize($d);
			$i_p = $menus[0];
			$this->req_import_menus($i_p, $menus, $parent);
		}

		return $this->mk_my_orb("right_frame", array("parent" => $parent));
	}

	private function req_import_menus($i_p, &$menus, $parent)
	{
		if (!is_array($menus[$i_p]))
		{
			return;
		}

		$p_o = obj($parent);
		$mt = $p_o->prop("type");

		reset($menus[$i_p]);
		while (list(,$v) = each($menus[$i_p]))
		{
			$db = $v["db"];

			$icon_id = 0;
			if (is_array($v["icon"]))
			{
				$icon_id = icons::get_icon_by_file($v["icon"]["file"]);
				if (!$icon_id)
				{
					// not in db, must add
					$icon_id = icons::add_array($v["icon"]);
				}
			}

			$o = obj();
			$o->set_parent($parent);
			$o->set_name($db["name"]);
			$o->set_class_id($db["class_id"]);
			$o->set_status($db["status"]);
			$o->set_comment($db["comment"]);
			$o->set_ord($db["jrk"]);
			$o->set_alias($db["alias"]);
			$o->set_periodic($db["periodic"]);

			$ps = $o->properties();
			foreach($ps as $pn => $pv)
			{
				if ($o->is_property($pn))
				{
					$o->set_prop($pn, $db[$pn]);
				}
			}
			$id = $o->save();

			// tegime vanema menyy 2ra, teeme lapsed ka.
			$this->req_import_menus($db["oid"],$menus,$id);
		}
	}

	/** imports menus from text file. file format description is in the docs folder **/
	private function do_text_import($arr)
	{
		$fail = $_FILES["fail"]["tmp_name"];
		if (is_uploaded_file($fail))
		{
			$c = file($fail);
			$cnt = 0;
			$levels = array("" => $parent); // here we keep the info about the numbering of the levels => menu id's
			foreach($c as $row)
			{
				if (substr($row, 0, 1) == "#")
				{
					continue;
				}
				$cnt++;
				// parse row and create menu.
				if (!preg_match("/([0-9\.]+)(.*)\[(.*)\]/",$row,$mt))
				{
					if (!preg_match("/([0-9\.]+)(.*)/",$row,$mt))
					{
						die(sprintf(t("Menyyde importimisel tekkis viga real %s "),$cnt));
					}
				}
				// now parse the position in the structure from the numbers.
				$pos = strrpos($mt[1],".");
				$_pt = substr($mt[1],0,$pos);
				if ($_pt == "")
				{
					$_parent = $arr["parent"];
				}
				else
				{
					$_parent = $levels[$_pt];
				}

				if ($_pt != "" && !$_parent)
				{
					die(sprintf(t("Menyyde importimisel ei leidnud parent menyyd real %s "),$cnt));
				}
				else
				{
					// parse the menu options
					$opts = trim($mt[3]);
					$mopts = array("click" => 1);
					if ($opts != "")
					{
						// whee. do a preg_match for every option.
						$mopts["act"] = preg_match("/\+act/",$opts);
						if (preg_match("/\+comment=\"(.*)\"/",$opts,$mmt))
						{
							$mopts["comment"] = $mmt[1];
						}
						if (preg_match("/\+alias=(.*)/",$opts,$mmt))
						{
							$mopts["alias"] = $mmt[1];
						}
						$mopts["per"] = preg_match("/\+per/",$opts);
						if (preg_match("/\+link=\"(.*)\"/",$opts,$mmt))
						{
							$mopts["link"] = $mmt[1];
						}
						$mopts["click"] = preg_match("/\+click/",$opts);
						$mopts["target"] = preg_match("/\+target/",$opts);
						$mopts["mid"] = preg_match("/\+mid/",$opts);
						$mopts["makdp"] = preg_match("/\+makdp/",$opts);
						if (preg_match("/\+width=\"(.*)\"/",$opts,$mmt))
						{
							$mopts["width"] = $mmt[1];
						}
						$mopts["rp"] = preg_match("/\+rp/",$opts);
						$mopts["lp"] = preg_match("/\+lp/",$opts);
						if (preg_match("/\+fn=\"(.*)\"/",$opts,$mmt))
						{
							$mopts["fn"] = $mmt[1];
						}
						if (preg_match_all("/\+prop=\"(.*)\"/U",$opts, $mmt))
						{
							$mopts["props"] = $mmt[1];
						}

						if (preg_match_all("/\+meta=\"(.*)\"/U",$opts, $mmt))
						{
							$mopts["metas"] = $mmt[1];
						}

					}

					// now create the damn thing.
					$this->quote($mt);
					$this->quote($mopts);

					$o = obj();
					$o->set_parent($_parent);
					$o->set_name(trim($mt[2]));
					$o->set_class_id(CL_MENU);
					$o->set_status(STAT_ACTIVE /*($mopts["act"] ? 2 : 1)*/);
					$o->set_alias($mopts["alias"]);
					$o->set_ord(substr($mt[1],($pos > 0 ? $pos+1 : 0)));

					$o->set_prop("type", MN_CONTENT);
					$o->set_prop("link", $mopts["link"]);
					$o->set_prop("clickable", $mopts["click"]);
					$o->set_prop("target", $mopts["target"]);
					$o->set_prop("mid", $mopts["mid"]);
					$o->set_prop("hide_noact", $mopts["makdp"]);
					$o->set_prop("width", $mopts["width"]);
					$o->set_prop("right_pane", !$mopts["rp"]);
					$o->set_prop("left_pane", !$mopts["lp"]);

					foreach($mopts["props"] as $s_prop)
					{
						preg_match("/(.*)\|/",$s_prop,$a_match);
						$s_prop_name = $a_match[1];
						preg_match("/\|(.*)/",$s_prop,$a_match);
						$s_prop_value = $a_match[1];

						$o->set_prop($s_prop_name, $s_prop_value);
					}

					foreach($mopts["metas"] as $s_meta)
					{
						preg_match("/(.*)\|/",$s_meta,$a_match);
						$s_meta_name = $a_match[1];
						preg_match("/\|(.*)/",$s_meta,$a_match);
						$s_meta_value = $a_match[1];

						$o->set_meta($s_meta_name, $s_meta_value);
					}

					$id = $o->save();
					$levels[$mt[1]] = $id;
				}
			}
		}
	}

	private function _init_period_dropdown($tb)
	{
		$per_oid = aw_ini_get("per_oid");
		$dbp = get_instance(CL_PERIOD, $per_oid);

		$act_per_id = $dbp->get_active_period();
		$pl = array();
		$actrec = 0;
		$rc = 0;
		$period_list = new object_list(array(
			"class_id" => CL_PERIOD,
			"sort_by" => "objects.jrk DESC"
		));

		for ($period_obj = $period_list->begin(); !$period_list->end(); $period_obj = $period_list->next())
		{
			$rc++;
			if ($period_obj->prop("per_id") == $act_per_id)
			{
				$actrec = $rc;
			};
			$pl[$rc] = array(
				"id" => $period_obj->prop("per_id"),
				"name" => $period_obj->name(),
			);
		}

		// leiame praegune +-3
		$ar = array();
		for ($i=$actrec-6; $i <= ($actrec+6); $i++)
		{
			if (isset($pl[$i]))
			{
				if ($pl[$i]["id"] == $act_per_id)
				{
					$ar[$pl[$i]["id"]] = $pl[$i]["name"]." ".t("(A)");
				}
				else
				{
					$ar[$pl[$i]["id"]] = $pl[$i]["name"];
				}
			}
		}
		$ar[0] = t("Mitteperioodilised");
		foreach($ar as $id => $name)
		{
			$tb->add_menu_item(array(
				"parent" => "set_period",
				"text" => $name,
				"title" => $name,
				"name" => "per".$id,
				"tooltip" => $name,
				"link" => aw_url_change_var("period", $id)
			));
		}
	}

	private function _fake_trans_get_val_name($row_d)
	{
		if (empty($GLOBALS["cfg"]["user_interface"]["content_trans"]) && empty($GLOBALS["cfg"]["user_interface"]["full_content_trans"]) && empty($GLOBALS["cfg"]["user_interface"]["trans_classes"]))
		{
			return $row_d["name"];
		}

		if ($row_d["oid"] != $row_d["brother_of"])
		{
			$tmp = obj($row_d["brother_of"]);
			if ($tmp->id() != $row_d["oid"]) // if no view access for original, bro can return the same object
			{
				return $tmp->trans_get_val("name");
			}
		}

		if ($row_d["class_id"] == CL_LANGUAGE)
		{
			$tmp = obj($row_d["oid"]);
			return $tmp->trans_get_val("name");
		}

		$val = $row_d["name"];

		$trans = false;
		$cur_lid = false;
		if (!empty($GLOBALS["cfg"]["user_interface"]["content_trans"]) && ($cur_lid = aw_global_get("lang_id")) != $row_d["lang_id"])
		{
			$trans = true;
		}

		if ((!empty($GLOBALS["cfg"]["user_interface"]["full_content_trans"]) || !empty($GLOBALS["cfg"]["user_interface"]["trans_classes"][$row_d["class_id"]])) &&
			($cl = aw_global_get($GLOBALS["cfg"]["user_interface"]["full_content_trans"] ? "ct_lang_id" : "lang_id")) != $row_d["lang_id"])
		{
			$trans = true;
			$cur_lid = $cl;
		}

		$m = aw_unserialize(ifset($row_d, "metadata"));

		if ($trans)
		{
			if (isset($m["translations"]))
			{
				$trs = $m["translations"];
				if (isset($trs[$cur_lid]) && ($m["trans_{$cur_lid}_status"] == 1 || $ignore_status))
				{
					if (empty($trs[$cur_lid]["name"]))
					{
						return $val;
					}
					$val = $trs[$cur_lid]["name"];
				}
			}
		}
		return $val;
	}

	/**
		@attrib name=install params=name
	**/
	public function install($arr)
	{
		$o = obj(null, array(), CL_ADMIN_IF);
		$o->set_parent(1);
		$o->save();
		$url = core::mk_my_orb("change", array("id" => $o->id(), "group" => "o"), CL_ADMIN_IF);
		aw_redirect($url);
	}
}

/** Implement this interface in your class if you want to apply some special behaviour for your object type in the admin interface **/
interface admin_if_plugin
{
	/** This will be called for each object currently displayed in the admin interface table with the item data
		@attrib api=1 params=pos

		@param data required type=array
			The row data for the object that you can modify
	**/
	function admin_if_modify_data(&$data);
}
