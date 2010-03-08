<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/class_designer/class_designer_manager.aw,v 1.29 2009/08/19 08:11:27 dragut Exp $
// class_designer_manager.aw - Klasside brauser

//			automatweb::$instance->mode(automatweb::MODE_DBG);

/*

@classinfo syslog_type=ST_CLASS_DESIGNER_MANAGER relationmgr=yes no_comment=1 no_status=1 maintainer=kristo prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@default group=mgr

	@property mgr_tb type=toolbar no_caption=1

	@layout mgr_hbox type=hbox width=20%:80%

		@layout mgr_tree_box type=vbox closeable=1 area_caption=Klasside&nbsp;kaustad parent=mgr_hbox

			@property mgr_tree type=treeview no_caption=1 parent=mgr_tree_box

	@property mgr_tbl type=table no_caption=1 parent=mgr_hbox

@default group=rels

	@property rels_tb type=toolbar no_caption=1

	@layout rels_hbox type=hbox width=20%:80%

		@layout rels_tree_box type=vbox closeable=1 area_caption=Klasside&nbsp;kaustad parent=rels_hbox

			@property rels_tree type=treeview no_caption=1 parent=rels_tree_box

	@property rels_tbl type=table no_caption=1 parent=rels_hbox

@default group=classes_classes

	@property toolbar type=toolbar no_caption=1 store=no

	@layout classes_split type=hbox

		@layout classes_left type=vbox parent=classes_split

			@layout classes_tree type=vbox area_caption=Klasside&nbsp;puu closeable=1 parent=classes_left

				@property classes_tree type=treeview no_caption=1 parent=classes_tree store=no

			@layout classes_groups type=vbox area_caption=Klasside&nbsp;grupid closeable=1 parent=classes_left

				@property classes_groups type=treeview no_caption=1 parent=classes_groups store=no

		@property classes_list type=table no_caption=1 store=no parent=classes_split

@default group=classes_props

	@property props_toolbar type=toolbar no_caption=1 store=no

	@layout classes_props_split type=hbox

		@layout classes_props_left type=vbox parent=classes_props_split

			@layout classes_props_tree type=vbox area_caption=Klasside&nbsp;puu closeable=1 parent=classes_props_left

				@property classes_props_tree type=treeview no_caption=1 parent=classes_props_tree store=no

			@layout classes_props_groups type=vbox area_caption=Omaduste&nbsp;grupid closeable=1 parent=classes_props_left

				@property classes_props_groups type=treeview no_caption=1 parent=classes_props_groups store=no

		@property classes_props_list type=table no_caption=1 store=no parent=classes_props_split


@default group=cl_usage_stats_clids

	@layout cl_usage_stats_split type=hbox width=20%:80%

		@layout cl_usage_stats_left type=vbox parent=cl_usage_stats_split

			@layout cl_usage_stats_tree type=vbox area_caption=Klasside&nbsp;puu closeable=1 parent=cl_usage_stats_left

				@property cl_usage_stats_tree type=treeview no_caption=1 parent=cl_usage_stats_tree store=no

			@layout cl_usage_stats_groups type=vbox area_caption=Klasside&nbsp;grupid closeable=1 parent=cl_usage_stats_left

				@property cl_usage_stats_groups type=treeview no_caption=1 parent=cl_usage_stats_groups store=no

		@layout cl_usage_stats_right type=vbox parent=cl_usage_stats_split

			@layout cl_usage_stats_right_split type=hbox parent=cl_usage_stats_right

				@layout cl_usage_stats_right_split_1 type=vbox parent=cl_usage_stats_right_split

					@layout cl_usage_stats_right_split_1_v type=vbox parent=cl_usage_stats_right_split_1 area_caption=Graafikud closeable=1

						@property cl_usage_stats_list_gpx type=google_chart no_caption=1 store=no parent=cl_usage_stats_right_split_1_v

					@property cl_usage_stats_list type=table no_caption=1 store=no parent=cl_usage_stats_right_split_1

				@layout cl_usage_stats_right_split_2 type=vbox parent=cl_usage_stats_right_split

					@layout cl_usage_stats_right_split_2_v type=vbox parent=cl_usage_stats_right_split_2 area_caption=Graafikud closeable=1

						@property site_usage_stats_list_gpx type=google_chart no_caption=1 store=no parent=cl_usage_stats_right_split_2_v

					@property site_usage_stats_list type=table no_caption=1 store=no parent=cl_usage_stats_right_split_2

@default group=cl_usage_stats_props

	@layout cl_usage_props_stats_split type=hbox width=20%:80%

		@layout cl_usage_props_stats_left type=vbox parent=cl_usage_props_stats_split

			@layout cl_usage_props_stats_tree type=vbox area_caption=Klasside&nbsp;puu closeable=1 parent=cl_usage_props_stats_left

				@property cl_usage_props_stats_tree type=treeview no_caption=1 parent=cl_usage_props_stats_tree store=no

			@layout cl_usage_props_stats_groups type=vbox area_caption=Klasside&nbsp;grupid closeable=1 parent=cl_usage_props_stats_left

				@property cl_usage_props_stats_groups type=treeview no_caption=1 parent=cl_usage_props_stats_groups store=no

		@layout cl_usage_props_stats_right type=vbox parent=cl_usage_props_stats_split

			@layout cl_usage_props_stats_right_split type=vbox parent=cl_usage_props_stats_right

				@layout cl_usage_props_stats_right_split_v type=vbox parent=cl_usage_props_stats_right area_caption=Graafikud closeable=1

					@property cl_usage_props_stats_list_gpx type=google_chart no_caption=1 store=no parent=cl_usage_props_stats_right_split_v

				@layout cl_usage_props_stats_right_split_b type=vbox parent=cl_usage_props_stats_right

					@property cl_usage_props_stats_list type=table no_caption=1 store=no parent=cl_usage_props_stats_right_split_b

@default group=cl_usage_stats_tms

	@layout cl_usage_stats_tms_split type=hbox width=20%:80%

		@layout cl_usage_stats_tms_left type=vbox parent=cl_usage_stats_tms_split

			@layout cl_usage_stats_tms_tree type=vbox area_caption=Saidid closeable=1 parent=cl_usage_stats_tms_left

				@property cl_usage_stats_tms_tree type=treeview no_caption=1 parent=cl_usage_stats_tms_tree store=no

		@layout cl_usage_stats_tms_right type=vbox parent=cl_usage_stats_tms_split

			@layout cl_usage_stats_tms_right_split type=hbox parent=cl_usage_stats_tms_right

				@property cl_usage_stats_tms_list type=table no_caption=1 store=no parent=cl_usage_stats_tms_right_split

@default group=errors

	@layout errors_split type=hbox width=20%:80%

		@layout errors_left type=vbox parent=errors_split

			@layout errors_tree type=vbox area_caption=Saidid closeable=1 parent=errors_left

				@property errors_tree type=treeview no_caption=1 parent=errors_tree store=no

		@layout errors_right type=vbox parent=errors_split

			@layout errors_right_split type=vbox parent=errors_right

				@layout errors_right_split_gp type=vbox parent=errors_right_split closeable=1 area_caption=Graafikud
					@layout errors_right_split_gp_v type=hbox parent=errors_right_split_gp
						@property errors_list_grapx type=google_chart no_caption=1 store=no parent=errors_right_split_gp_v
						@property errors_list_grapx_days type=google_chart no_caption=1 store=no parent=errors_right_split_gp_v

				@property errors_list type=table no_caption=1 store=no parent=errors_right_split

@default group=sites_sites

	@property sites_sites_toolbar type=toolbar no_caption=1 store=no

	@layout sites_sites_split type=hbox width=20%:80%

		@layout sites_sites_left type=vbox parent=sites_sites_split

			@layout sites_sites_tree type=vbox area_caption=Grupid closeable=1 parent=sites_sites_left

				@property sites_sites_grp_tree type=treeview no_caption=1 parent=sites_sites_tree store=no

		@layout sites_sites_right type=vbox parent=sites_sites_split

			@layout sites_sites_right_split type=vbox parent=sites_sites_right

				@layout sites_sites_right_split_t type=vbox parent=sites_sites_right_split closeable=1 area_caption=Graafikud

					@property sites_list_grapx  type=google_chart store=no no_caption=1 parent=sites_sites_right_split_t

				@property sites_list type=table store=no no_caption=1 parent=sites_sites_right_split



@default group=sites_servers

	@layout sites_servers_split type=hbox width=20%:80%

		@layout sites_servers_left type=vbox parent=sites_servers_split

			@layout sites_servers_tree type=vbox area_caption=Filter closeable=1 parent=sites_servers_left

				@property sites_servers_grp_tree type=treeview no_caption=1 parent=sites_servers_tree store=no

		@layout sites_servers_right type=vbox parent=sites_servers_split

			@layout sites_servers_right_split type=vbox parent=sites_servers_right

				@layout sites_servers_right_split_g type=vbox parent=sites_servers_right_split area_caption=1 area_caption=Graafikud

					@property sites_servers_grapx  type=google_chart store=no no_caption=1 parent=sites_servers_right_split_g

				@property sites_servers type=table store=no no_caption=1 parent=sites_servers_right_split


@default group=notifications_rules

	@property notif_tb type=toolbar store=no no_caption=1

	@property notifications_rules type=table store=no no_caption=1
	@caption Reeglid

@default group=notifications_sent


	@layout notifications_sent_split type=hbox width=20%:80%

		@layout notifications_sent_left type=vbox parent=notifications_sent_split

			@layout notifications_sent_tree type=vbox area_caption=Saitide&nbsp;grupid closeable=1 parent=notifications_sent_left

				@property notifications_sent_site_tree type=treeview no_caption=1 parent=notifications_sent_tree store=no

			@layout notifications_sent_tree_rules type=vbox area_caption=Teavituse&nbsp;reeglid closeable=1 parent=notifications_sent_left

				@property notifications_sent_site_tree_rules type=treeview no_caption=1 parent=notifications_sent_tree_rules store=no

		@layout notifications_sent_right type=vbox parent=notifications_sent_split

			@layout notifications_sent_right_split type=vbox parent=notifications_sent_right

				@layout notifications_sent_grapx  type=vbox area_caption=Teavituste&nbsp;TOP&nbsp;10 closeable=1 parent=notifications_sent_right_split

					@property notifications_sent_grapx  type=google_chart store=no no_caption=1 parent=notifications_sent_grapx

				@property notifications_sent type=table store=no no_caption=1 parent=notifications_sent_right_split


@default group=notifications_status

	@property ns_next type=text store=no
	@caption J&auml;rgmine k&auml;ivitus

	@property ns_manual type=text store=no
	@caption K&auml;ivita kohe

@groupinfo classes caption="Klassid"
	@groupinfo classes_classes caption="Klassid" parent=classes
	@groupinfo classes_props caption="Omadused" parent=classes

	@groupinfo mgr caption="Lisamise puu" submit=no parent=classes
	@groupinfo rels caption="Seosed" submit=no parent=classes

@groupinfo cl_usage_stats caption="Statistika"
	@groupinfo cl_usage_stats_clids caption="Klasside TOP" parent=cl_usage_stats submit=no
	@groupinfo cl_usage_stats_props caption="Omadused" parent=cl_usage_stats submit=no
	@groupinfo cl_usage_stats_tms caption="Ajad" parent=cl_usage_stats submit=no
@groupinfo errors caption="Vead"
@groupinfo sites caption="Saidid"
	@groupinfo sites_sites parent=sites caption="Saidid"
	@groupinfo sites_servers parent=sites caption="Serverid" submit=no

@groupinfo notifications caption="Teavitus"
	@groupinfo notifications_sent caption="Saadetud teavitused" parent=notifications save=no submit=no
	@groupinfo notifications_rules caption="Reeglid" parent=notifications submit=no
	@groupinfo notifications_status caption="Staatus" parent=notifications save=no submit=no


@reltype NOTIFICATION_RULE value=1 clid=CL_SITE_NOTIFICATION_RULE
@caption Teavituse reegel

*/

class class_designer_manager extends class_base
{
	private $sc = array(".. - 100", "100-50", "49-10", "9-5", "4-0");

	function class_designer_manager()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/class_designer_manager",
			"clid" => CL_CLASS_DESIGNER_MANAGER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "mgr_tb":
				$this->_mgr_tb($arr);
				break;

			case "mgr_tree":
				$this->_mgr_tree($arr);
				break;

			case "mgr_tbl":
				$this->_mgr_tbl($arr);
				break;

			case "rels_tb":
				$this->_rels_tb($arr);
				break;

			case "rels_tree":
				$this->_mgr_tree($arr);
				break;

			case "rels_tbl":
				$this->_rels_tbl($arr);
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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["tf"] = ifset($_REQUEST, "tf");
		$arr["classf_name"] = "";
		$arr["ch_classf_name"] = "";
		$arr["ch_classf_id"] = "";
		$arr["cls"] = ifset($_GET, "cls");
	}

	function _mgr_tb($arr)
	{
		$t =& $arr["prop"]["toolbar"];

		$t->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
		));

		$t->add_menu_item(array(
			"parent" => "new",
			"text" => t("Lisa klass"),
			"link" => html::get_new_url(
				CL_CLASS_DESIGNER,
				$arr["obj_inst"]->id(),
				array(
					"return_url" => get_ru(),
					"register_under" => $_GET["tf"]
				)
			)
		));

		$t->add_menu_item(array(
			"parent" => "new",
			"text" => t("Lisa perekond"),
			"action" => "create_clf",
			"onClick" => "document.changeform.classf_name.value=prompt('Sisesta nimi', ' ');"
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"caption" => t('Kustuta'),
			"action" => "delete_p",
		));

		$t->add_separator();
		$t->add_button(array(
			"name" => "cut",
			"img" => "cut.gif",
			"caption" => t('L&otilde;ika'),
			"action" => "cut_p",
		));

		$t->add_button(array(
			"name" => "copy",
			"img" => "copy.gif",
			"caption" => t('Kopeeri'),
			"action" => "copy_p",
		));

		$has_cut = count(safe_array($_SESSION["class_designer"]["cut_classes"])) +
				   count(safe_array($_SESSION["class_designer"]["cut_folders"]));
		$has_cop = count(safe_array($_SESSION["class_designer"]["copied_classes"])) +
				   count(safe_array($_SESSION["class_designer"]["copied_folders"]));
		if ($has_cut || $has_cop)
		{
			$t->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"caption" => t('Kleebi'),
				"action" => "paste_p",
			));
		}
	}

	function _mgr_tree($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];

		$t->start_tree(array(
			"tree_id" => "class_mgr_tree",
			"persist_state" => true,
			"type" => TREE_DHTML
		));

		$clsf = aw_ini_get("classfolders");
		foreach($clsf as $id => $inf)
		{
			$t->add_item((int)$inf["parent"], array(
				"name" => $arr["request"]["tf"] == $id ? "<b>".$inf["name"]."</b>" : $inf["name"],
				"id" => $id,
				"url" => aw_url_change_var("tf", $id)
			));
		}
	}

	function _init_mgr_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
			"align" => "center",
			"width" => 1
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "clid_nm",
			"caption" => t("ID"),
			"align" => "center",
			"numeric" => 1
		));

		$t->define_field(array(
			"name" => "size",
			"caption" => t("Suurus"),
			"align" => "center",
			"numeric" => 1
		));

		$t->define_field(array(
			"name" => "menu",
			"caption" => t("Tegevus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sel",
			"caption" => t("Vali"),
			"width" => 1,
			"align" => "center"
		));
	}

	function _mgr_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_mgr_tbl($t);

		$ol = new object_list(array(
			"class_id" => CL_CLASS_DESIGNER,
			"lang_id" => array(),
			"site_id" => array()
		));
		$designed = array();
		foreach($ol->arr() as $designer)
		{
			$designed[$designer->prop("reg_class_id")] = $designer->id();
		}

		$tf = $arr["request"]["tf"];

		$clf = aw_ini_get("classfolders");
		foreach($clf as $clf => $dat)
		{
			if ((int)$dat["parent"] != (int)$tf)
			{
				continue;
			}

			$sel = html::checkbox(array(
				"name" => "sel_fld[]",
				"value" => $clf
			));

			$t->define_data(array(
				"name" => $dat["name"],
				"add" => "",
				"design" => "",
				"clid" => "",
				"size" => $this->get_clf_size($clf),
				"icon" => html::img(array(
					"url" => aw_ini_get("icons.server")."/class_1.gif"
				)),
				"sel" => $sel,
				"menu" => $this->_get_menu("fld", $clf, NULL, $dat["name"])
			));
		}

		$clss = aw_ini_get("classes");
		foreach($clss as $clid => $cld)
		{
			$show = false;
			if ($cld["parents"] == "" && !$tf)
			{
				$show = true;
			}
			else
			{
				$parents = $this->make_keys(explode(",", $cld["parents"]));
				if ($parents[$tf])
				{
					$show = true;
				}
			}

			if (!$show)
			{
				continue;
			}

			$design = "";
			if ($designed[$clid])
			{
				$design = html::get_change_url($designed[$clid], array("return_url" => get_ru()), "Disaini");
			}


			$sel = html::checkbox(array(
				"name" => "sel[]",
				"value" => $clid
			));

			$t->define_data(array(
				"name" => $cld["name"],
				"design" => $design,
				"clid_nm" => $cld["def"],
				"size" => $this->get_class_size($cld["file"]),
				"icon" => html::img(array(
					"url" => aw_ini_get("icons.server")."/class_".$clid.".gif"
				)),
				"sel" => $sel,
				"menu" => $this->_get_menu("cls", $clid, $designed[$clid], $cld["name"], $arr["obj_inst"]->id())
			));
		}
		$t->set_sortable(false);
	}

	function _rels_tb($arr)
	{
		$t =& $arr["prop"]["toolbar"];

		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"caption" => t('Lisa'),
			"url" => html::get_new_url(
				CL_CLASS_DESIGNER,
				$arr["obj_inst"]->id(),
				array(
					"return_url" => get_ru(),
					"register_under" => $_GET["tf"]
				)
			)
		));
	}

	function _init_rels_tree(&$t)
	{
		$t->define_field(array(
			"name" => "class_name",
			"caption" => t("Klassi nimi"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "rel_name",
			"caption" => t("Seose nimi"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "rel_to",
			"caption" => t("Seos klassiga"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sel",
			"caption" => t("Vali"),
			"align" => "center",
		));
	}

	function _rels_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_rels_tree($t);

		$ol = new object_list(array(
			"class_id" => CL_CLASS_DESIGNER,
			"lang_id" => array(),
			"site_id" => array()
		));
		$designed = array();
		foreach($ol->arr() as $designer)
		{
			$designed[$designer->prop("reg_class_id")] = $designer->id();
		}

		$tf = $arr["request"]["tf"];
		$clss = aw_ini_get("classes");
		foreach($clss as $clid => $cld)
		{
			$show = false;
			if ($cld["parents"] == "" && !$tf)
			{
				$show = true;
			}
			else
			{
				$parents = $this->make_keys(explode(",", $cld["parents"]));
				if ($parents[$tf])
				{
					$show = true;
				}
			}

			if (!$show)
			{
				continue;
			}

			$sel = "";
			if ($designed[$clid])
			{
				$sel = html::get_change_url(
					$designed[$clid],
					array(
						"return_url" => get_ru(),
						"group" => "relations",
						"relations_mgr" => "new"
					),
					t("Lisa seos")
				);
			}

			$t->define_data(array(
				"class_name" => $cld["name"],
				"rel_name" => "",
				"rel_to" => "",
				"sel" => $sel
			));

			// rels for class
			if ($designed[$clid])
			{
				$rels = array();
				$d_o = obj($designed[$clid]);
				foreach($d_o->connections_from(array("reltype" => "RELTYPE_RELATION")) as $c)
				{
					$rel_o = $c->to();
					$rels[] = array(
						"caption" => $rel_o->name(),
						"clid" => $rel_o->prop("r_class_id")
					);
				}
			}
			else
			{
				$cu = get_instance("cfg/cfgutils");
				$ps = $cu->load_properties(array("clid" => $clid, "file" => basename($cld["file"])));
				$rels = $cu->get_relinfo();
			}
			foreach($rels as $rel)
			{
				$rel_to = array();
				foreach(safe_array($rel["clid"]) as $r_clid)
				{
					$rel_to[] = $clss[$r_clid]["name"];
				}

				$t->define_data(array(
					"class_name" => "",
					"rel_name" => $rel["caption"],
					"rel_to" => join(", ", $rel_to),
					"sel" => ""
				));
			}
		}
		$t->set_sortable(false);
	}

	function get_class_size($fn)
	{
		$fqfn = aw_ini_get("classdir")."/".$fn.".".aw_ini_get("ext");
		return number_format(filesize($fqfn) / 1024, 2)." kb / ".count(file($fqfn))." rida";
	}

	/**

		@attrib name=check_add_object

		@param id required type=int acl=view
		@param clid required type=int
		@param ru optional
	**/
	function check_add_object($arr)
	{
		$parent = $this->_get_obj_parent_by_clid($arr["id"], $arr["clid"]);
		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => $arr["clid"],
		));

		if (!$ol->count())
		{
			return html::get_new_url($arr["clid"], $parent, array("return_url" => ($arr["ru"])));
		}
		else
		{
			$o = $ol->begin();
			return html::get_change_url($o->id(), array("return_url" => ($arr["ru"])));
		}
	}

	function _get_class_path_in_tree($clid)
	{
		$d = aw_ini_get("classes");
		$inf = $d[$clid];
		$pts = explode(",", $inf["parents"]);

		$fld = $pts[0];

		$pt = array();
		$this->_req_get_pt_in_t($fld, $pt);
		return array_reverse($pt);
	}

	function _req_get_pt_in_t($fld, &$a)
	{
		$d = aw_ini_get("classfolders");
		$a[] = $d[$fld];

		if ($d[$fld]["parent"])
		{
			$this->_req_get_pt_in_t($d[$fld]["parent"], $a);
		}
	}

	/**

		@attrib name=cut_p

		@param sel optional
		@param sel_fld optional
		@param post_ru required
	**/
	function cut_p($arr)
	{
		$_SESSION["class_designer"]["cut_classes"] = safe_array($arr["sel"]);
		$_SESSION["class_designer"]["cut_folders"] = safe_array($arr["sel_fld"]);
		return $arr["post_ru"];
	}

	/**

		@attrib name=copy_p

		@param sel optional
		@param sel_fld optional
		@param post_ru required
	**/
	function copy_p($arr)
	{
		$_SESSION["class_designer"]["copied_classes"] = safe_array($arr["sel"]);
		$_SESSION["class_designer"]["copied_folders"] = safe_array($arr["sel_fld"]);
		return $arr["post_ru"];
	}

	/**

		@attrib name=paste_p

	**/
	function paste_p($arr)
	{
		$clss = aw_ini_get("classes");


		$cut = safe_array($_SESSION["class_designer"]["cut_classes"]);
		foreach($cut as $clid)
		{
			//$this->_set_ini_file_value(aw_ini_get("basedir")."/config/ini/classes.ini", "classes[$clid][parents]", $arr["tf"]);
			//$this->_set_ini_file_value(aw_ini_get("basedir")."/aw.ini", "classes[$clid][parents]", $arr["tf"]);
			$this->_set_ini_file_value(aw_ini_get("site_basedir")."/files/class_designer_cls.ini", "classes[$clid][parents]", $arr["tf"]);
		}
		$_SESSION["class_designer"]["cut_classes"] = array();

		$cut = safe_array($_SESSION["class_designer"]["cut_folders"]);
		foreach($cut as $fld)
		{
			//$this->_set_ini_file_value(aw_ini_get("basedir")."/config/ini/classfolders.ini", "classfolders[$fld][parent]", $arr["tf"]);
			//$this->_set_ini_file_value(aw_ini_get("basedir")."/aw.ini", "classfolders[$fld][parent]", $arr["tf"]);
			$this->_set_ini_file_value(aw_ini_get("site_basedir")."/files/class_designer_clsfld.ini", "classfolders[$fld][parent]", $arr["tf"]);
		}
		$_SESSION["class_designer"]["cut_folders"] = array();

		$cop = safe_array($_SESSION["class_designer"]["copied_classes"]);
		foreach($cop as $clid)
		{
			$np = $arr["tf"];
			if ($clss[$clid]["parents"] != "")
			{
				$curp = $this->make_keys(explode(",", $clss[$clid]["parents"]));
				$curp[$arr["tf"]] = $arr["tf"];
				$np = join(",", array_values($curp));
			}
			//$this->_set_ini_file_value(aw_ini_get("basedir")."/config/ini/classes.ini", "classes[$clid][parents]", $np);
			//$this->_set_ini_file_value(aw_ini_get("basedir")."/aw.ini", "classes[$clid][parents]", $np);
			$this->_set_ini_file_value(aw_ini_get("site_basedir")."/files/class_designer_cls.ini", "classes[$clid][parents]", $np);
		}
		$_SESSION["class_designer"]["copied_classes"] = array();

		$flds = aw_ini_get("classfolders");
		$cop = safe_array($_SESSION["class_designer"]["copied_folders"]);
		foreach($cop as $clid)
		{
			$max_fld = max(array_keys($flds))+1;
			$np = $arr["tf"];
			$ds = $flds[$clid];

			//$this->_add_ini_file_value(aw_ini_get("basedir")."/config/ini/classfolders.ini", "classfolders[$max_fld][name]", $ds["name"]);
			//$this->_add_ini_file_value(aw_ini_get("basedir")."/config/ini/classfolders.ini", "classfolders[$max_fld][parent]", $np);
			$this->_set_ini_file_value(aw_ini_get("site_basedir")."/files/class_designer_clsfld.ini", "classfolders[$max_fld][name]", $ds["name"]);
			$this->_set_ini_file_value(aw_ini_get("site_basedir")."/files/class_designer_clsfld.ini", "classfolders[$max_fld][parent]", $np);

			//$this->_add_ini_file_value(aw_ini_get("basedir")."/aw.ini", "classfolders[$max_fld][name]", $ds["name"]);
			//$this->_add_ini_file_value(aw_ini_get("basedir")."/aw.ini", "classfolders[$max_fld][parent]", $np);
		}
		$_SESSION["class_designer"]["copied_folders"] = array();

		return $arr["post_ru"];
	}

	function _add_ini_file_value($file, $k, $v)
	{
		$ls = file($file);
		$ls[] = $k." = ".$v."\n";

		$this->put_file(array(
			"file" => $file,
			"content" => join("", $ls)
		));
	}

	function _set_ini_file_value($file, $k, $v)
	{
		$ls = file($file);
		foreach($ls as $key => $line)
		{
			if (substr($line, 0, strlen($k)) == $k)
			{
				$ls[$key] = $k." = ".$v."\n";
				$found = true;
			}
		}
		if(!$found)
		{
			$ls[] = $k." = ".$v."\n";
		}
		$this->put_file(array(
			"file" => $file,
			"content" => join("", $ls)
		));
	}

	function _del_ini_file_value($file, $k)
	{
		$ls = file($file);
		foreach($ls as $key => $line)
		{
			if (substr($line, 0, strlen($k)) == $k)
			{
				unset($ls[$key]);
			}
		}
		$this->put_file(array(
			"file" => $file,
			"content" => join("", $ls)
		));
	}

	function get_clf_size($clf)
	{
		$clss = array();
		$this->_get_classes_below($clf, $clss);

		$bytes = $lines = 0;
		foreach($clss as $cld)
		{
			$fqfn = aw_ini_get("classdir")."/".$cld["file"].".".aw_ini_get("ext");
			$bytes += filesize($fqfn);
			$lines += count(file($fqfn));
		}

		return number_format($bytes / 1024, 2)." kb / ".$lines." rida / ".count($clss)." klassi";
	}

	function _get_classes_below($clf, &$arr)
	{
		$fld = aw_ini_get("classfolders");
		foreach($fld as $id => $d)
		{
			if ($d["parent"] == $clf)
			{
				$this->_get_classes_below($id, $arr);
			}
		}

		$c = aw_ini_get("classes");
		foreach($c as $id => $d)
		{
			if (in_array($clf, explode(",", $d["parents"])))
			{
				$arr[] = $d;
			}
		}
	}

	/**

		@attrib name=create_clf

	**/
	function create_clf($arr)
	{
		$flds = aw_ini_get("classfolders");
		$max_fld = max(array_keys($flds))+1;

		//$this->_add_ini_file_value(aw_ini_get("basedir")."/config/ini/classfolders.ini", "classfolders[$max_fld][name]", $arr["classf_name"]);
		//$this->_add_ini_file_value(aw_ini_get("basedir")."/config/ini/classfolders.ini", "classfolders[$max_fld][parent]", $arr["tf"]);
		$this->_add_ini_file_value(aw_ini_get("site_basedir")."/files/class_designer_clsfld.ini", "classfolders[$max_fld][name]", $arr["classf_name"]);
		$this->_add_ini_file_value(aw_ini_get("site_basedir")."/files/class_designer_clsfld.ini", "classfolders[$max_fld][parent]", $arr["tf"]);

		//$this->_add_ini_file_value(aw_ini_get("basedir")."/aw.ini", "classfolders[$max_fld][name]", $arr["classf_name"]);
		//$this->_add_ini_file_value(aw_ini_get("basedir")."/aw.ini", "classfolders[$max_fld][parent]", $arr["tf"]);

		return $arr["post_ru"];
	}

	function _get_menu($tp, $id, $designer = NULL, $nm = NULL, $obj_id = NULL)
	{
		$this->tpl_init("automatweb/menuedit");
		$this->read_template("js_popup_menu.tpl");

		$this->vars(array(
			"menu_id" => $tp.$id,
			"menu_icon" => $this->cfg["baseurl"]."/automatweb/images/blue/obj_settings.gif",
		));

		if ($tp == "fld")
		{
			$items = array(
				"javascript:submit_changeform('change_clf_name',document.changeform.ch_classf_name.value=prompt('Sisesta nimi','$nm'),document.changeform.ch_classf_id.value=$id)" => t("Muuda nime"),
				$this->mk_my_orb("cut_p", array("sel_fld" => array($id), "post_ru" => get_ru())) => t("L&otilde;ika"),
				$this->mk_my_orb("copy_p", array("sel_fld" => array($id), "post_ru" => get_ru())) => t("Kopeeri"),
				$this->mk_my_orb("delete_p", array("sel_fld" => array($id), "post_ru" => get_ru())) => t("Kustuta")
			);
		}
		else
		{
			$add_link = $this->mk_my_orb(
				"check_add_object",
				array(
					"clid" => $id,
					"id" => $obj_id,
					"ru" => get_ru()
				)
			);

			$items = array(
				$add_link => t("Demo objekt"),
				"javascript:submit_changeform('change_class_name',document.changeform.ch_classf_name.value=prompt('Sisesta nimi','$nm'),document.changeform.ch_classf_id.value=$id)" => t("Muuda nime"),
			);
			if ($designer)
			{
				$items[html::get_change_url($designer, array("return_url" => get_ru()))] = t("Disaini");
			}
			else
			{
				$items[$this->mk_my_orb("create_designer_from_class", array("id" => $obj_id, "ru" => get_ru(), "clid" => $id))] = t("Loo disainer");
			}
			$items[$this->mk_my_orb("cut_p", array("sel" => array($id), "post_ru" => get_ru()))] = t("L&otilde;ika");
			$items[$this->mk_my_orb("copy_p", array("sel" => array($id), "post_ru" => get_ru()))] = t("Kopeeri");
			$items[$this->mk_my_orb("delete_p", array("sel" => array($id), "post_ru" => get_ru()))] = t("Kustuta");
		}

		$mi = "";
		foreach($items as $url => $txt)
		{
			$this->vars(array(
				"link" => $url,
				"text" => $txt
			));
			$mi .= $this->parse("MENU_ITEM");
		}

		$this->vars(array(
			"MENU_ITEM" => $mi
		));
		return $this->parse();
	}

	/**

		@attrib name=change_clf_name

	**/
	function change_clf_name($arr)
	{
		//$this->_set_ini_file_value(aw_ini_get("basedir")."/config/ini/classfolders.ini", "classfolders[$arr[ch_classf_id]][name]", $arr["ch_classf_name"]);
		//$this->_set_ini_file_value(aw_ini_get("basedir")."/aw.ini", "classfolders[$arr[ch_classf_id]][name]", $arr["ch_classf_name"]);
		$this->_set_ini_file_value(aw_ini_get("site_basedir")."/files/class_designer_clsfld.ini", "classfolders[$arr[ch_classf_id]][name]", $arr["ch_classf_name"]);
		return $arr["post_ru"];
	}

	/**

		@attrib name=change_class_name

	**/
	function change_class_name($arr)
	{
		//$this->_set_ini_file_value(aw_ini_get("basedir")."/config/ini/classes.ini", "classes[$arr[ch_classf_id]][name]", $arr["ch_classf_name"]);
		//$this->_set_ini_file_value(aw_ini_get("basedir")."/aw.ini", "classes[$arr[ch_classf_id]][name]", $arr["ch_classf_name"]);
		$this->_set_ini_file_value(aw_ini_get("site_basedir")."/files/class_designer_clsfld.ini", "classes[$arr[ch_classf_id]][name]", $arr["ch_classf_name"]);
		return $arr["post_ru"];
	}

	/**

		@attrib name=delete_p

		@param sel optional
		@param sel_fld optional
		@param post_ru required
	**/
	function delete_p($arr)
	{
		$inif1 = aw_ini_get("basedir")."/config/ini/classfolders.ini";
		$inif2 = aw_ini_get("basedir")."/aw.ini";
		$inif3 = aw_ini_get("site_basedir")."/files/class_designer_clsfld.ini";
		foreach(safe_array($arr["sel_fld"]) as $fld_id)
		{
			//$this->_del_ini_file_value($inif1, "classfolders[$fld_id]");
			//$this->_del_ini_file_value($inif2, "classfolders[$fld_id]");

			//$this->_del_ini_file_value($inif3, "classfolders[$fld_id]");
			$this->_set_ini_file_value($inif3, "classfolders[$fld_id]", "__delete");
		}

		//$inif1 = aw_ini_get("basedir")."/config/ini/classes.ini";
		//$inif2 = aw_ini_get("basedir")."/aw.ini";
		$inif3 = aw_ini_get("site_basedir")."/files/class_designer_cls.ini";
		foreach(safe_array($arr["sel"]) as $fld_id)
		{
			//$this->_del_ini_file_value($inif1, "classes[$fld_id]");
			//$this->_del_ini_file_value($inif2, "classes[$fld_id]");

			//$this->_del_ini_file_value($inif3, "classes[$fld_id]");
			$this->_set_ini_file_value($inif3, "classes[$fld_id]", "__delete");
		}
		return $arr["post_ru"];
	}

	/**

		@attrib name=create_designer_from_class

		@param id required type=int acl=view
		@param clid required type=int
		@param ru required
	**/
	function create_designer_from_class($arr)
	{
		$parent = $this->_get_obj_parent_by_clid($arr["id"], $arr["clid"]);

		$clss = aw_ini_get("classes");
		$o = $this->_get_object_by_parent_type_name($parent, CL_CLASS_DESIGNER, $clss[$arr["clid"]]["name"]);
		$o->set_prop("is_registered", 1);
		$o->set_prop("reg_class_id", $arr["clid"]);
		$o->set_prop("can_add", $clss[$arr["clid"]]["can_add"]);
		$o->set_prop("class_folder", $clss[$arr["clid"]]["parents"]);
		$o->save();

		$this->_parse_designer_from_class($o, $arr["clid"], $clss[$arr["clid"]]);


		return html::get_change_url($o->id(), array("return_url" => ($arr["ru"])));
	}

	function _get_obj_parent_by_clid($id, $clid)
	{
		$pt = $this->_get_class_path_in_tree($clid);
		$o = obj($id);
		foreach($pt as $inf)
		{
			$filt = array(
				"parent" => $o->id(),
				"class_id" => CL_MENU,
				"name" => $inf["name"],
				"lang_id" => array(),
				"site_id" => array()
			);
			$ol = new object_list($filt);
			if (!$ol->count())
			{
				$_pt = $o->id();
				$o = obj();
				$o->set_parent($_pt);
				$o->set_class_id(CL_MENU);
				$o->set_name($inf["name"]);
				$o->save();
			}
			else
			{
				$o = $ol->begin();
			}
		}

		return $o->id();
	}

	function _parse_designer_from_class($designer, $clid, $cld)
	{
		$cu = get_instance("cfg/cfgutils");
		$ps = $cu->load_properties(array("clid" => $clid));
		$gp = $cu->get_groupinfo();
		$ci = $cu->get_classinfo();
		$designer->set_prop("relationmgr", ($ci["relationmgr"] == "yes" ? 1 : 0));
		$designer->set_prop("no_comment", ($ci["no_comment"] == "1" ? 1 : 0));
		$designer->set_prop("no_status", ($ci["no_status"] == "1" ? 1 : 0));
		$designer->save();

		$element_ords = array();

		$this->type_map = array(
			"text" => CL_PROPERTY_TEXTBOX,
			"textbox" => CL_PROPERTY_TEXTBOX,
			"relpicker" => CL_PROPERTY_SELECT,
			"callback" => CL_PROPERTY_TEXTBOX,
			"checkbox" => CL_PROPERTY_CHECKBOX,
			"fileupload" => CL_PROPERTY_TEXTBOX,
			"hidden" => CL_PROPERTY_TEXTBOX,
			"date" => CL_PROPERTY_TEXTBOX,
			"select" => CL_PROPERTY_SELECT,
			"date_select" => CL_PROPERTY_TEXTBOX,
			"password" => CL_PROPERTY_TEXTBOX,
			"submit" => CL_PROPERTY_TEXTBOX,
			"status" => CL_PROPERTY_TEXTBOX,
			"textarea" => CL_PROPERTY_TEXTAREA,
			"table" => CL_PROPERTY_TABLE,
			"chooser" => CL_PROPERTY_CHOOSER,
			"releditor" => CL_PROPERTY_TABLE,
			"datetime_select" => CL_PROPERTY_TEXTBOX,
			"aliasmgr" => CL_PROPERTY_TEXTBOX,
			"comments" => CL_PROPERTY_TEXTBOX,
			"toolbar" => CL_PROPERTY_TOOLBAR,
			"treeview" => CL_PROPERTY_TREE,
			"relmanager" => CL_PROPERTY_TEXTBOX,
			"calendar" => CL_PROPERTY_TEXTBOX,
			"objpicker" => CL_PROPERTY_TEXTBOX,
			"classificator" => CL_PROPERTY_CHOOSER,
			"popup_search" => CL_PROPERTY_TEXTBOX,
			"form" => CL_PROPERTY_TEXTBOX,
			"reminder" => CL_PROPERTY_TEXTBOX,
			"generated" => CL_PROPERTY_TEXTBOX,
			"colorpicker" => CL_PROPERTY_TEXTBOX,
			"time_select" => CL_PROPERTY_TEXTBOX,
		);

		// get groups
		$cnt = 0;
		foreach($gp as $gpid => $gpd)
		{
			// create group objs
			$g = $this->_get_object_by_parent_type_name($designer->id(), CL_PROPERTY_GROUP, $gpid);
			$g->set_prop("caption", $gpd["caption"]);
			$element_ords[$g->id()] = ++$cnt;
			if ($gpd["no_submit"])
			{
				$g->set_prop("no_submit", 1);
			}
			$g->save();

			// for each group make default grid
			$grid = $this->_get_object_by_parent_type_name($g->id(), CL_PROPERTY_GRID, "default");
			$element_ords[$grid->id()] = ++$cnt;

			// stick in properties
			foreach($ps as $pn => $pd)
			{
				if ($this->_prop_is_in_group($gpid , $pd["group"]))
				{
					$prop = $this->_get_object_by_parent_type_name($grid->id(), $this->type_map[$pd["type"]], $pn);
					$element_ords[$prop->id()] = ++$cnt;
					$prop_i = $prop->instance();
					if (method_exists($prop_i, "parse_property_from_class"))
					{
						$prop_i->parse_property_from_class($designer, $prop, $pd, $clid);
					}
				}
			}
		}

		$designer->set_meta("element_ords", $element_ords);
		$designer->save();

		// make relations
	}

	function _prop_is_in_group($gpid, $grp)
	{
		if (is_array($grp))
		{
			return in_array($gpid, $grp);
		}
		return $gpid == $grp;
	}

	function _get_object_by_parent_type_name($parent, $type, $name)
	{
		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => $type,
			"name" => $name
		));
		if ($ol->count())
		{
			return $ol->begin();
		}
		$o = obj();
		$o->set_parent($parent);
		$o->set_class_id($type);
		$o->set_name($name);
		$o->save();
		return $o;
	}

	function _get_classes_tree($arr)
	{
		$clf = aw_ini_get("classfolders");
		foreach($clf as $id => $data)
		{
			$arr["prop"]["vcl_inst"]->add_item($data["parent"], array(
				"name" => $data["name"],
				"id" => $id,
				"url" => aw_url_change_var("clf", $id, aw_url_change_var("grp", null))
			));
		}
		if (!empty($arr["request"]["clf"]))
		{
			$arr["prop"]["vcl_inst"]->set_selected_item($arr["request"]["clf"]);
		}
		$arr["prop"]["vcl_inst"]->set_root_name(t("Klassid"));
		$arr["prop"]["vcl_inst"]->set_root_url(aw_url_change_var("clf", null));
	}

	function _get_classes_list($arr)
	{
		$clss = aw_ini_get("classes");
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_classes_list_table($t, $arr["obj_inst"]);
		$this->_filter_class_list($clss, $arr["request"]);

		$ot = new object_tree(array(
			"class_id" => array(CL_SM_CLASS_STATS_GROUP),
			"parent" => $arr["obj_inst"]->id()
		));
		$grp_list = $ot->to_list()->arr();
		$grps = array();
		foreach($grp_list as $grp_id => $grp_obj)
		{
			foreach(safe_array($grp_obj->prop("class_list")) as $class_id => $tmp)
			{
				$grps[$class_id][$grp_id] = 1;
			}
		}

		$sum = array();
		foreach($clss as $class_id => $cld)
		{
			$o = obj();
			$o->set_class_id($class_id);
			$cld["id"] = $class_id;
			$cld["prop_cnt"] = 0;
			$cld["rel_cnt"] = 0;
			$cld["prop_table"] = 0;
			$cld["prop_meta"] = 0;
			foreach($grp_list as $grp_id => $grp_obj)
			{
				$cld[$grp_id] = html::checkbox(array(
					"name" => "grps[$grp_id][$class_id]",
					"value" => 1,
					"checked" => !empty($grps[$class_id][$grp_id])
				));
			}
			foreach($o->get_property_list() as $pn => $pd)
			{
				$cld["prop_cnt"]++;
				if (empty($pd["store"]) || ($pd["store"] != "no" && $pd["store"] != "connect"))
				{
					if (!empty($pd["field"]) && $pd["field"] == "meta")
					{
						$cld["prop_meta"]++;
					}
					else
					{
						$cld["prop_table"]++;
					}

				}
			}
			foreach($o->get_relinfo() as $rid => $rdata)
			{
				if (is_numeric($rid))
				{
					$cld["rel_cnt"]++;
				}
			}

			foreach($cld as $k => $v)
			{
				if (!isset($sum[$k]))
				{
					$sum[$k] = 0;
				}
				if (is_numeric($v))
				{
					$sum[$k] += $v;
				}
			}
			$t->define_data($cld);
		}
		$t->set_default_sortby("name");
		$t->sort_by();
		$t->set_sortable(false);
		$t->define_data(array(
			"def" => html::strong(t("Summa")),
			"prop_cnt" => html::strong($sum["prop_cnt"]),
			"prop_table" => html::strong($sum["prop_table"]),
			"prop_meta" => html::strong($sum["prop_meta"]),
			"rel_cnt" => html::strong($sum["rel_cnt"]),
			"file" => count($clss)
		));
	}

	private function _filter_class_list(&$clss, $r)
	{
		if (empty($r["clf"]) && empty($r["grp"]))
		{
			return;
		}

		if ($this->can("view", $r["grp"]))
		{
			$o = obj($r["grp"]);
			$p = $o->class_list;
			foreach($clss as $clid => $cld)
			{
				if (!isset($p[$clid]))
				{
					unset($clss[$clid]);
				}
			}
			return;
		}
		// get all folders beneath $r["clf"] and then list all classes for those
		$clfs = array($r["clf"] => $r["clf"]);
		$c = aw_ini_get("classfolders");

		$this->_req_fetch_clfs($c, $r["clf"], $clfs);
		foreach($clss as $clid => $cld)
		{
			$pts = $this->make_keys(explode(",", isset($cld["parents"]) ? $cld["parents"] : ""));
			if (!count(array_intersect($clfs, $pts)))
			{
				unset($clss[$clid]);
			}
		}
	}

	private function _req_fetch_clfs($c, $parent, &$list)
	{
		foreach($c as $id => $dat)
		{
			if ($dat["parent"] == $parent)
			{
				$list[$id] = $id;
				$this->_req_fetch_clfs($c, $id, $list);
			}
		}
	}

	private function _init_classes_list_table(&$t, $o)
	{
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id"
		));
		$t->define_field(array(
			"name" => "def",
			"caption" => t("ID"),
			"align" => "left",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Name"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "file",
			"caption" => t("Fail"),
			"align" => "left",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "prop_cnt",
			"caption" => t("Omadusi"),
			"align" => "right",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "rel_cnt",
			"caption" => t("Seoset&uuml;&uuml;pe"),
			"align" => "right",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "prop_table",
			"caption" => t("Omadusi tabelis"),
			"align" => "right",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "prop_meta",
			"caption" => t("Omadusi metadatas"),
			"align" => "right",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "grps",
			"caption" => t("Grupid"),
			"align" => "center",
		));
		$ot = new object_tree(array(
			"class_id" => array(CL_SM_CLASS_STATS_GROUP),
			"parent" => $o->id()
		));

		foreach($ot->to_list()->arr() as $o)
		{
			$t->define_field(array(
				"name" => $o->id(),
				"caption" => $o->name(),
				"align" => "center",
				"parent" => "grps"
			));
		}
	}

	function _get_toolbar($arr)
	{
		$pt = isset($arr["request"]["grp"]) ? $arr["request"]["grp"] : $arr["obj_inst"]->id();
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"onClick" => "len = document.changeform.elements.length;str  = '';
	for(i = 0; i < len; i++)
	{
		if (document.changeform.elements[i].name.indexOf('sel') != -1 && document.changeform.elements[i].checked)
		{
			str += '&sel['+document.changeform.elements[i].value+']='+document.changeform.elements[i].value;
		}
	}

window.location.href='".html::get_new_url(CL_SM_CLASS_STATS_GROUP, $pt, array("return_url" => get_ru()))."&'+str;",
			"url" => "#",
			"tooltip" => "new"
		));
	}

	function _get_classes_groups($arr)
	{
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "smc",
				"persist_state" => true,
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array(CL_SM_CLASS_STATS_GROUP),
				"parent" => $arr["obj_inst"]->id()
			)),
			"var" => "grp"
                ));
		foreach($arr["prop"]["vcl_inst"]->get_item_ids() as $id)
		{
			if ($id == $arr["obj_inst"]->id())
			{
				continue;
			}
			$d = $arr["prop"]["vcl_inst"]->get_item($id);
			$d["name"] .= " ".html::get_change_url($id, array("return_url" => get_ru()), html::img(array("url" => aw_ini_get("baseurl")."/automatweb//images/icons/edit.gif", "border" => "0")));
			$d["name"] .= " ".html::href(array(
				"url" => $this->mk_my_orb("delete", array("id" => $id, "return_url" => get_ru()), CL_SM_CLASS_STATS_GROUP),
				"caption" => html::img(array("url" => aw_ini_get("baseurl")."/automatweb//images/icons/delete.gif", "border" => "0"))
			));
			$arr["prop"]["vcl_inst"]->set_item($d);
		}
	}

	function _get_cl_usage_stats_groups($arr)
	{
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "smc",
				"persist_state" => true,
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array(CL_SM_CLASS_STATS_GROUP),
				"parent" => $arr["obj_inst"]->id()
			)),
			"var" => "grp"
                ));
	}

	function _get_cl_usage_stats_tree($arr)
	{
		$clf = aw_ini_get("classfolders");
		foreach($clf as $id => $data)
		{
			$arr["prop"]["vcl_inst"]->add_item($data["parent"], array(
				"name" => $data["name"],
				"id" => $id,
				"url" => aw_url_change_var("clf", $id, aw_url_change_var("grp", null))
			));
		}
		if (!empty($arr["request"]["clf"]))
		{
			$arr["prop"]["vcl_inst"]->set_selected_item($arr["request"]["clf"]);
		}
		$arr["prop"]["vcl_inst"]->set_root_name(t("Klassid"));
		$arr["prop"]["vcl_inst"]->set_root_url(aw_url_change_var("clf", null));
	}

	function _get_cl_usage_stats_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_cl_usage_stats_list_table($t);

		$clss = aw_ini_get("classes");
		$this->_filter_class_list($clss, $arr["request"]);
		$clids = new aw_array(array_keys($clss));

		$clss = aw_ini_get("classes");
		$this->db_query("SELECT SUM(count) as cnt, class_id FROM aw_site_object_stats WHERE class_id IN (".$clids->to_sql().") GROUP BY class_id ORDER BY cnt desc");
		while($row = $this->db_next())
		{
			$t->define_data(array(
				"class" => $clss[$row["class_id"]]["name"],
				"total_cnt" => $row["cnt"]
			));
		}
		$t->set_default_sortby("total_cnt");
		$t->set_default_sorder("desc");
	}

	function _get_site_usage_stats_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_site_usage_stats_list_table($t);

		$clss = aw_ini_get("classes");
		$this->_filter_class_list($clss, $arr["request"]);
		$clids = new aw_array(array_keys($clss));

		$this->db_query("SELECT SUM(count) as cnt, site_id FROM aw_site_object_stats WHERE class_id IN (".$clids->to_sql().") GROUP BY site_id ORDER BY cnt desc");
		while($row = $this->db_next())
		{
			$this->save_handle();
			$t->define_data(array(
				"site_id" => get_instance("site_list")->get_url_for_site($row["site_id"]),
				"total_cnt" => $row["cnt"]
			));
			$this->restore_handle();
		}
	}

	private function _init_cl_usage_stats_list_table($t)
	{
		$t->define_field(array(
			"name" => "class",
			"caption" => t("Klass"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "total_cnt",
			"caption" => t("Objekte kokku"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->set_caption(t("Klasside kaupa"));
	}

	private function _init_site_usage_stats_list_table($t)
	{
		$t->define_field(array(
			"name" => "site_id",
			"caption" => t("Sait"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "total_cnt",
			"caption" => t("Objekte kokku"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->set_caption(t("Saitide kaupa"));
	}

	function _get_cl_usage_props_stats_groups($arr)
	{
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "smc",
				"persist_state" => true,
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array(CL_SM_CLASS_STATS_GROUP),
				"parent" => $arr["obj_inst"]->id()
			)),
			"var" => "grp"
                ));
	}

	function _get_cl_usage_props_stats_tree($arr)
	{
		$clf = aw_ini_get("classfolders");
		foreach($clf as $id => $data)
		{
			$arr["prop"]["vcl_inst"]->add_item($data["parent"], array(
				"name" => $data["name"],
				"id" => $id,
				"url" => aw_url_change_var("clf", $id, aw_url_change_var("grp", null, aw_url_change_var("class_id", null)))
			));
			// all classes for that folder as well
			$clss = aw_ini_get("classes");
			foreach($clss as $clid => $cld)
			{
				$pts = $this->make_keys(explode(",", ifset($cld, "parents")));
				if (isset($pts[$id]))
				{
					$arr["prop"]["vcl_inst"]->add_item($id, array(
						"name" => $cld["name"],
						"id" => "cl_".$clid,
						"url" => aw_url_change_var("class_id", $clid, aw_url_change_var("clf", $id, aw_url_change_var("grp", null))),
						"iconurl" => icons::get_icon_url($clid)
					));
				}
			}
		}
		// add all classes that are addable but no parens
		$clss = aw_ini_get("classes");
		foreach($clss as $clid => $cld)
		{
			$ps = ifset($cld, "parents");
			if (ifset($cld, "can_add") == 1 && ($ps == "" || $ps == 0))
			{
				$arr["prop"]["vcl_inst"]->add_item(0, array(
					"name" => $cld["name"],
					"id" => "cl_".$clid,
					"url" => aw_url_change_var("class_id", $clid, aw_url_change_var("clf", $id, aw_url_change_var("grp", null))),
					"iconurl" => icons::get_icon_url($clid)
				));
			}
		}

		if (!empty($arr["request"]["class_id"]))
		{
			$arr["prop"]["vcl_inst"]->set_selected_item("cl_".$arr["request"]["class_id"]);
		}
		$arr["prop"]["vcl_inst"]->set_root_name(t("Klassid"));
		$arr["prop"]["vcl_inst"]->set_root_url(aw_url_change_var("clf", null));
	}

	function _get_cl_usage_props_stats_list($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_cl_usage_props_stats_list($t);

		if (empty($arr["request"]["class_id"]))
		{
			return;
		}

		$total_site_count = $this->db_fetch_field("SELECT count(distinct(site_id)) as cnt from aw_site_class_prop_stats", "cnt");
		$class_id = $arr["request"]["class_id"];
		$this->db_query("SELECT prop, count(site_id) as num_sites, sum(set_objs) as num_objects, sum(total_objs) as total_objs  FROM aw_site_class_prop_stats WHERE class_id = $class_id GROUP BY prop ");
		$pl = obj()->set_class_id($class_id)->get_property_list();
		while ($row = $this->db_next())
		{
			$t->define_data(array(
				"caption" => $pl[$row["prop"]]["caption"],
				"prop" => $row["prop"],
				"num_sites" => $row["num_sites"],
				"num_objects" => $row["num_objects"],
				"total_objs" => $row["total_objs"],
				"usage_pct_sites" => number_format((100.0 * $row["num_sites"]) / $total_site_count, 2),
				"usage_pct_objs" => number_format( (100.0 * $row["num_objects"]) / $row["total_objs"], 2)
			));
		}
		$t->set_default_sortby("caption");
		$t->sort_by();
	}

	private function _init_cl_usage_props_stats_list($t)
	{
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Nimi"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "num_sites",
			"caption" => t("Kasutusel saitides"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "total_objs",
			"caption" => t("Kokku objekte"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "num_objects",
			"caption" => t("Kasutusel objektides"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "usage_pct_sites",
			"caption" => t("Kasutuse % saitide kaupa"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "usage_pct_objs",
			"caption" => t("Kasutuse % objektide kaupa"),
			"align" => "left",
			"sortable" => 1,
			"numeric" => 1
		));
	}

	function _get_cl_usage_stats_tms_tree($arr)
	{
		// tree by server / site
		$server_list = $this->do_orb_method_call(array(
			"class" => "site_list",
			"action" => "orb_get_server_list",
			"method" => "xmlrpc",
			"server" => "register.automatweb.com"
		));

		$tv = $arr["prop"]["vcl_inst"];

		$used_servers = array();
		$site_list = get_instance("install/site_list")->get_local_list();
		foreach($site_list as $site)
		{
			if ($site["site_used"] == 1)
			{
				if ($site["server_id"] == 0)
				{
					$site["server_id"] = -1;
				}
				$tv->add_item($site["server_id"], array(
					"id" => "site_".$site["id"],
					"name" => ifset($arr, "request", "site") == $site["id"] ? html::strong($site["url"]) : $site["url"],
					"url" => aw_url_change_var("site", $site["id"])
				));
				$used_servers[$site["server_id"]] = $site["server_id"];
			}
		}

		foreach($used_servers as $server_id)
		{
			if ($server_id == -1)
			{
				$tv->add_item(0, array(
					"id" => $server_id,
					"name" => ifset($arr, "request", "server") == $server_id ? html::strong(t("Tundmatu")) : t("Tundmatu"),
					"url" => aw_url_change_var("server", $server_id)
				));
				continue;
			}

			foreach($server_list as $server)
			{
				if ($server["id"] == $server_id)
				{
					$tv->add_item(0, array(
						"id" => $server_id,
						"name" => ifset($arr, "request", "server") == $server_id ? html::strong($server["name"]) : $server["name"],
						"url" => aw_url_change_var("server", $server_id)
					));
				}
			}
		}
	}

	private function _init_t2($t)
	{
		$t->define_field(array(
			"name" => "server",
			"caption" => t("Server"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "site",
			"caption" => t("Sait"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "pageviews",
			"caption" => t("Lehevaatamisi"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "avg_time",
			"caption" => t("Keskmine lehe aeg"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "max_time",
			"caption" => t("Pikim lehe aeg"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "min_time",
			"caption" => t("V&auml;ikseim lehe aeg"),
			"align" => "center",
			"numeric" => 1,
			"sortable" => 1
		));
	}

	function _get_cl_usage_stats_tms_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_t2($t);

		$wh = array();
		if (!empty($arr["request"]["server"]))
		{
			$this->quote(&$arr["request"]["server"]);
			$wh[] = " server_id = ".$arr["request"]["server"];
		}
		if (!empty($arr["request"]["site"]))
		{
			$this->quote(&$arr["request"]["site"]);
			$wh[] = " site_id = ".$arr["request"]["site"];
		}

		$whs = join(" AND ", $wh);
		if ($whs != "")
		{
			$whs = " WHERE ".$whs;
		}
		$this->db_query("SELECT server_id as server, site_id as site, count(*) as pageviews, AVG(exec_time) as avg_time, max(exec_time) as max_time, min(exec_time) as min_time FROM aw_timing_stats $whs GROUP BY server_id, site_id");
		while ($row = $this->db_next())
		{
			$row["site"] = get_instance("install/site_list")->get_url_for_site($row["site"]);
			$t->define_data($row);
		}
		$t->set_default_sortby("pageviews");
		$t->set_default_sorder("desc");
	}

	/**
		@attrib name=import_logs nologin="1"
	**/
	function import_logs($arr)
	{
		$base = aw_ini_get("site_basedir")."/files/timers/";
		foreach(glob($base."tm-*") as $file)
		{
			echo "process $file <br>\n";
			flush();
			foreach(file($file) as $line)
			{
				if (preg_match("/(.*) request (.*)/", $line, $mt))
				{
					list($d, $tm) = explode(" ", $mt[1]);
					list($d, $m, $y) = explode(".", $d);
					list($h, $i, $s) = explode(":", $tm);

					$q = "INSERT INTO aw_timing_stats(server_id, site_id, exec_time, tm) values(1, ".aw_ini_get("site_id").", '".$mt[2]."', ".mktime($h, $i, $s, $m, $d, $y).")";
					$this->db_query($q);
				}
			}
			unlink($file);
		}
		die("all done");
	}

	function _get_errors_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->db_query("SELECT distinct(site) as site FROM bugtrack_errors");
		$id = 1;
		while ($row = $this->db_next())
		{
			$t->add_item(0, array(
				"id" => ++$id,
				"name" => ifset($arr["request"], "site") == $row["site"] ? html::strong($row["site"]) : $row["site"],
				"url" => aw_url_change_var("site", $row["site"])
			));
		}
	}

	function _get_errors_list_b($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_errors_list_b($t);

		$errors = aw_ini_get("errors");

		$filt_site = ifset($arr["request"], "site");
		$filt_type = ifset($arr["request"], "filt_type");

		$t->set_caption(sprintf(
			t("%s saidil %s t&uuml;&uuml;biga %s"),
			html::href(array(
				"url" => aw_url_change_var("filt_type", null, aw_url_change_var("site", null)),
				"caption" => t("Vead")
			)),
			html::href(array(
				"url" => aw_url_change_var("filt_type", null),
				"caption" => $filt_site
			)),
			html::href(array(
				"url" => get_ru(),
				"caption" => isset($errors[$filt_type]) ? $errors[$filt_type]["name"] : $filt_type)
			))
		);

		$this->db_query("SELECT err_uid, tm, site, type_id, id FROM bugtrack_errors WHERE site = '$filt_site' AND type_id = '$filt_type' ");
		while ($row = $this->db_next())
		{
			$t->define_data(array(
				"uid" => $row["err_uid"],
				"tm" => $row["tm"],
				"site" => $row["site"],
				"type_id" => isset($errors[$row["type_id"]]) ? $errors[$row["type_id"]]["name"] : $row["type_id"],
				"view" => html::href(array(
					"url" => "javascript:void(0)",
					"caption" => t("Vaata"),
					"onClick" => "aw_popup_scroll(\"".$this->mk_my_orb("showe", array("err_id" => $row["id"], "in_popup" => 1))."\", \"erp\", 1000, 900); return false;"
				))
			));
		}
	}

	/**
		@attrib name=showe
		@param err_id required
	**/
	function showe($arr)
	{
		$row = $this->db_fetch_row("SELECT * FROM bugtrack_errors WHERE id = $arr[err_id]");

		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "type_id",
			"type" => "text",
			"caption" => t("T&uuml;&uuml;p"),
			"value" => isset($errors[$row["type_id"]]) ? $errors[$row["type_id"]]["name"] : $row["type_id"],
		));

		$htmlc->add_property(array(
			"name" => "site",
			"type" => "text",
			"caption" => t("Sait"),
			"value" => $row["site"],
		));
		$htmlc->add_property(array(
			"name" => "uid",
			"type" => "text",
			"caption" => t("Kasutaja"),
			"value" => $row["err_uid"],
		));

		$htmlc->add_property(array(
			"name" => "tm",
			"type" => "text",
			"caption" => t("Millal"),
			"value" => date("d.m.Y H:i:s", $row["tm"]),
		));

		$htmlc->add_property(array(
			"name" => "content",
			"type" => "text",
			"caption" => t("Sisu"),
			"value" => nl2br($row["content"]),
		));


		$htmlc->finish_output(array(
			"data" => array(
				"class" => get_class($this),
				"action" => "submit_change_site",
			),
			"submit" => "no"
		));

		$str =  $htmlc->get_result(array(
			"form_only" => 1
		));
		$tp = get_instance("vcl/tabpanel");
		$tp->add_tab(array("active" => true, "caption" => t("Viga")));

		return $tp->get_tabpanel(array("content" => $str));

	}

	private function _init_errors_list_b($t)
	{
		$t->define_field(array(
			"name" => "uid",
			"caption" => t("Kasutaja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "tm",
			"caption" => t("Millal"),
			"align" => "center",
			"type" => "time",
			"sortable" => 1,
			"numeric" => 1,
			"format" => "d.m.Y H:i:s"
		));
		$t->define_field(array(
			"name" => "site",
			"caption" => t("Sait"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "type_id",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
			"align" => "center",
		));
	}

	function _get_errors_list($arr)
	{
		$filt_site = ifset($arr["request"], "site");
		$filt_type = ifset($arr["request"], "filt_type");

		$t = $arr["prop"]["vcl_inst"];

		if ($filt_type != "" && $filt_site != "")
		{
			return $this->_get_errors_list_b($arr);
		}

		$this->_init_errors_list($t);


		$mintm = $this->db_fetch_field("SELECT min(tm) as tm FROM bugtrack_errors", "tm");
		$maxtm = $this->db_fetch_field("SELECT max(tm) as tm FROM bugtrack_errors", "tm");
		$days = ($maxtm - $mintm) / (24*3600);

		$errors = aw_ini_get("errors");


		if ($filt_site != "")
		{
			$this->db_query("SELECT site, type_id, count(*) as total FROM bugtrack_errors WHERE site = '$filt_site' GROUP BY site, type_id");
			$t->set_caption(sprintf(
				t("%s saidil %s"),
				html::href(array(
					"url" => aw_url_change_var("filt_type", null, aw_url_change_var("site", null)),
					"caption" => t("Vead")
				)),
				html::href(array(
					"url" => aw_url_change_var("filt_type", null),
					"caption" => $filt_site
				))
			));
		}
		else
		{
			$this->db_query("SELECT site, type_id, count(*) as total FROM bugtrack_errors GROUP BY site, type_id");
			$t->set_caption(t("Vigade &uuml;levaade"));
		}
		while ($row = $this->db_next())
		{
			if ($filt_site != "")
			{
				$url = aw_url_change_var("site", $row["site"], aw_url_change_var("filt_type", $row["type_id"]));
			}
			else
			{
				$url = aw_url_change_var("site", $row["site"]);
			}
			$t->define_data(array(
				"site" => $row["site"],
				"type_id" => isset($errors[$row["type_id"]]) ? $errors[$row["type_id"]]["name"] : $row["type_id"],
				"total" => $row["total"],
				"per_day" => number_format($row["total"] / $days, 2),
				"view" => html::href(array(
					"url" => $url,
					"caption" => t("Vaata")
				))
			));
		}
		$t->set_default_sortby("total");
		$t->set_default_sorder("desc");
	}

	private function _init_errors_list($t)
	{
		$t->define_field(array(
			"name" => "site",
			"caption" => t("Sait"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "type_id",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "total",
			"caption" => t("Kokku"),
			"align" => "right",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "per_day",
			"caption" => t("P&auml;evas"),
			"align" => "right",
			"numeric" => 1,
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
			"align" => "center",
		));
	}

	private function _init_sites_list_t($t)
	{
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "url",
			"caption" => t("URL"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "server_oid",
			"caption" => t("Server"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "site_used",
			"caption" => t("Kasutusel"),
			"align" => "center",
			"sortable" => 1
		));
/*		$t->define_field(array(
			"name" => "code_branch",
			"caption" => t("Koodiversioon"),
			"align" => "center",
			"sortable" => 1
		));*/
		$t->define_field(array(
			"name" => "last_update",
			"caption" => t("Viimane uuendus"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
/*		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));*/
		$t->define_field(array(
			"name" => "grps",
			"caption" => t("Grupid"),
			"align" => "center",
		));
		$ol = new object_list(array(
			"class_id" => CL_SM_SITE_GROUP,
			"site_id" => array(),
			"lang_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$t->define_field(array(
				"name" => $o->id(),
				"caption" => $o->name(),
				"align" => "center",
				"parent" => "grps"
			));
		}
	}

	function _get_sites_list($arr)
	{
		// convert sites to objects
		$this->_site_convert_check();

		$ol = new object_list(array(
			"class_id" => CL_SM_SITE_GROUP,
			"site_id" => array(),
			"lang_id" => array()
		));
		$grps = $ol->names();
		$grps_sel = array();
		foreach($ol->arr() as $o)
		{
			foreach($o->connections_from(array("type" => "RELTYPE_SITE")) as $c)
			{
				$grps_sel[$c->prop("to")][$o->id()] = 1;
			}
		}



		$t = $arr["prop"]["vcl_inst"];
		$this->_init_sites_list_t($t);

		$ol = new object_list(array(
			"class_id" => CL_AW_SITE_ENTRY,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$d = array(
				"id" => $o->prop("site_id"),
				"name" => html::obj_change_url($o),
				"url" => $o->url,
				"server_oid" => $o->server_oid()->name(),
				"site_used" => $o->site_used ? t("Jah") : t("Ei"),
//				"code_branch" => $o->code_branch,
				"last_update" => $o->last_update,
				"modified" => $o->modified,
			);
			foreach($grps as $gid => $gn)
			{
				$d[$gid] = html::checkbox(array(
					"name" => "g[$gid][".$o->id()."]",
					"value" => 1,
					"checked" => !empty($grps_sel[$o->id()][$gid])
				));
			}
			$t->define_data($d);
		}
		$t->set_default_sortby("site_used");
		$t->set_default_sorder("desc");
	}

	function _set_sites_list($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_SM_SITE_GROUP,
			"site_id" => array(),
			"lang_id" => array()
		));
		$grps = $ol->names();
		$grps_sel = array();
		foreach($ol->arr() as $o)
		{
			foreach($o->connections_from(array("type" => "RELTYPE_SITE")) as $c)
			{
				$grps_sel[$c->prop("to")][$o->id()] = 1;
			}
		}

		$ol = new object_list(array(
			"class_id" => CL_AW_SITE_ENTRY,
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			foreach($grps as $gid => $gn)
			{
				if (empty($arr["request"]["g"][$gid][$o->id()]))
				{
					if (!empty($grps_sel[$o->id()][$gid]))
					{
						obj($gid)->disconnect(array("from" => $o->id()));
					}
				}
				else
				{
					if (empty($grps_sel[$o->id()][$gid]))
					{
						obj($gid)->connect(array("to" => $o->id(), "type" => "RELTYPE_SITE"));
					}
				}
			}
		}
	}

	private function _init_server_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "ip",
			"caption" => t("IP"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "customer_contact",
			"caption" => t("Kliendi kontaktisik"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "manager",
			"caption" => t("Haldaja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "manager_contact",
			"caption" => t("Haldaja kontaktisik"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "site_cnt",
			"caption" => t("Saite"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
	}

	function _get_sites_servers($arr)
	{
		// convert sites to objects
		$this->_server_convert_check();

		$c = array();
		foreach($this->db_fetch_array("SELECT server_id, count(*) as cnt FROM aw_site_list WHERE site_used > 0 GROUP BY server_id") as $row)
		{
			$c[$row["server_id"]] = $row["cnt"];
		}

		$t = $arr["prop"]["vcl_inst"];
		$this->_init_server_table($t);

		$filt = array(
			"class_id" => CL_AW_SERVER_ENTRY,
			"lang_id" => array(),
			"site_id" => array()
		);
		if (!empty($arr["request"]["mgr"]))
		{
			$filt["manager"] = $arr["request"]["mgr"];
		}
		if (!empty($arr["request"]["cust"]))
		{
			$filt["customer"] = $arr["request"]["cust"];
		}
		$ol = new object_list($filt);
		foreach($ol->arr() as $o)
		{
			if (!empty($arr["request"]["sc"]))
			{
				$e = list($from, $to) = explode("-", $this->sc[$arr["request"]["sc"]]);
				if ($from == "...")
				{
					$from = 1000000;
				}
				if ($to == 0)
				{
					$to = -1;
				}

				if ($c[$o->prop("id")] > $from || (int)$c[$o->prop("id")] <= (int)$to)
				{
					continue;
				}
			}
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"ip" => $o->ip,
				"customer" => html::obj_change_url($o->customer),
				"customer_contact" => html::obj_change_url($o->customer_contact),
				"manager" => html::obj_change_url($o->manager),
				"manager_contact" => html::obj_change_url($o->manager_contact),
				"site_cnt" => $c[$o->prop("server_id")]
			));
		}
		$t->set_default_sortby("site_cnt");
		$t->set_default_sorder("desc");
		$t->set_caption(t("AW Serverite nimekiri"));
	}

	function _get_sites_servers_grp_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_item(0, array(
			"id" => "mgr",
			"name" => t("Haldaja"),
			"url" => aw_url_change_var(array("mgr" => null, "cust" => null, "sc" => null))
		));

		// get all different mgrs
		$odl = new object_data_list(
			array(
				"lang_id" => array(),
				"site_id" => array(),
				"class_id" => CL_AW_SERVER_ENTRY,
			),
			array(
				CL_AW_SERVER_ENTRY => array(new obj_sql_func(OBJ_SQL_UNIQUE, "manager", "manager"))
			)
		);
                foreach($odl->arr() as $o)
                {
			if (!$this->can("view", $o["manager"]))
			{
				continue;
			}

			$t->add_item("mgr", array(
				"id" => "mgr_".$o["manager"],
				"name" => obj($o["manager"])->name(),
				"url" => aw_url_change_var(array("mgr" => null, "cust" => $o["manager"], "sc" => null))
			));
                }


		$t->add_item(0, array(
			"id" => "cust",
			"name" => t("Klient"),
			"url" => aw_url_change_var(array("mgr" => null, "cust" => null, "sc" => null))
		));

		// get all different mgrs
		$odl = new object_data_list(
			array(
				"lang_id" => array(),
				"site_id" => array(),
				"class_id" => CL_AW_SERVER_ENTRY,
			),
			array(
				CL_AW_SERVER_ENTRY => array(
					new obj_sql_func(OBJ_SQL_UNIQUE, "customer", "customer")
				)
			)
		);
                foreach($odl->arr() as $o)
                {
			if (!$this->can("view", $o["customer"]))
			{
				continue;
			}

			$t->add_item("cust", array(
				"id" => "cust_".$o["customer"],
				"name" => obj($o["customer"])->name(),
				"url" => aw_url_change_var(array("mgr" => null, "cust" => $o["customer"], "sc" => null))
			));
                }


		$t->add_item(0, array(
			"id" => "site_cnt",
			"name" => t("Saitide arv"),
			"url" => aw_url_change_var(array("mgr" => null, "cust" => null, "sc" => null))
		));

                foreach($this->sc as $idx => $v)
                {
			$t->add_item("site_cnt", array(
				"id" => "sc_".$idx,
				"name" => $v,
				"url" => aw_url_change_var(array("mgr" => null, "cust" => null, "sc" => $idx))
			));
                }

		if (!empty($arr["request"]["sc"]))
		{
			$t->set_selected_item("sc_".$arr["request"]["sc"]);
		}
		else
		{
			$t->set_selected_item($arr["request"]["cust"] ? "cust_".$arr["request"]["cust"] : "mgr_".$arr["request"]["mgr"]);
		}
	}

	private function _site_convert_check()
	{
		$this->_server_convert_check();

		//$n1 = $this->db_fetch_field("SELECT count(*) as cnt FROM aw_site_list", "cnt");
		if ($this->db_query("SELECT count(*) as cnt FROM aw_site_list WHERE aw_oid IS NOT NULL", false))
		{
			return;
		}

		$this->db_query("ALTER TABLE aw_site_list ADD aw_oid int", false);
		$this->db_query("ALTER TABLE aw_site_list ADD server_oid int", false);
		$this->db_query("SELECT * FROM aw_site_list WHERE aw_oid IS NULL");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			$o = obj();
			$o->set_class_id(CL_AW_SITE_ENTRY);
			$o->set_parent(aw_ini_get("amenustart"));
			$o->save();

			$id = $o->id();
			$this->db_query("DELETE FROM aw_site_list WHERE aw_oid = $id");
			$this->db_query("UPDATE aw_site_list SET aw_oid = $id WHERE id = $row[id]");
			$server_oid = $this->db_fetch_field("SELECT aw_oid FROM aw_server_list WHERE id = '$row[server_id]'", "aw_oid");
			$this->db_query("UPDATE aw_site_list SET server_oid = '$server_oid' WHERE id = $row[id]");
			$this->restore_handle();
		}
		$c = get_instance("maitenance");
		$c->cache_clear(array("clear" => 1, "no_die" => 1));
	}

	private function _server_convert_check()
	{
		if ($this->db_query("SELECT count(*) as cnt FROM aw_server_list WHERE aw_oid IS NOT NULL", false))
		{
			// update all added
			$srv = $this->db_fetch_array("SELECT * FROM aw_server_list");
			foreach($srv as $row)
			{
				if (!$row["aw_oid"])
				{
					$o = obj();
					$o->set_class_id(CL_AW_SERVER_ENTRY);
					$o->set_parent(aw_ini_get("amenustart"));
					$o->save();

					$id = $o->id();
					$this->db_query("DELETE FROM aw_server_list WHERE aw_oid = $id");
					$this->db_query("UPDATE aw_server_list SET aw_oid = $id WHERE id = $row[id]");
				}
			}
			return;
		}

		$this->db_query("ALTER TABLE aw_server_list ADD aw_oid int");
		$this->db_query("SELECT * FROM aw_server_list WHERE aw_oid IS NULL");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			$o = obj();
			$o->set_class_id(CL_AW_SERVER_ENTRY);
			$o->set_parent(aw_ini_get("amenustart"));
			$o->save();

			$id = $o->id();
			$this->db_query("DELETE FROM aw_server_list WHERE aw_oid = $id");
			$this->db_query("UPDATE aw_server_list SET aw_oid = $id WHERE id = $row[id]");
			$this->restore_handle();
		}
		$c = get_instance("maitenance");
		$c->cache_clear(array("clear" => 1, "no_die" => 1));
	}

	private function _init_notify_table($t)
	{
		$t->define_field(array(
			"name" => "site",
			"caption" => t("Sait"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "when",
			"caption" => t("Millal"),
			"align" => "left",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i:s"
		));
		$t->define_field(array(
			"name" => "who",
			"caption" => t("Kellele"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
			"align" => "center",
		));
	}

	function _get_notifications_sent($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_notify_table($t);

		$filt = array(
			"class_id" => CL_SITE_NOTIFICATION_SENT,
			"lang_id" => array(),
			"site_id" => array()
		);

		if (!empty($arr["request"]["grp"]))
		{
			if (!empty($arr["request"]["site"]))
			{
				$gp = obj($arr["request"]["site"]);
				$t->set_caption(sprintf(t("Saadetud teavitused saidile %s"), $gp->name()));
				$filt["site"] = $gp->id();
			}
			else
			{
				$gp = obj($arr["request"]["grp"]);
				$sl = array();
				foreach($gp->get_sites_in_group() as $site)
				{
					$sl[] = $site->id();
				}
				$filt["site"] = $sl;

				$t->set_caption(sprintf(t("Saadetud teavitused saitide grupile %s"), $gp->name()));
			}
		}
		else
		if (!empty($arr["request"]["rule"]))
		{
			$gp = obj($arr["request"]["rule"]);
			$filt["rule"] = $arr["request"]["rule"];

			$t->set_caption(sprintf(t("Saadetud teavitused reeglist %s"), $gp->name()));
		}
		else
		{
			$t->set_caption(t("Saadetud teavitused"));
		}

		$ol = new object_list($filt);
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"site" => html::obj_change_url($o->prop("site")),
				"when" => $o->prop("when"),
				"who" => $o->prop("who"),
				"view" => html::obj_change_url($o)
			));
		}
		$t->set_default_sortby("when");
		$t->set_default_sorder("desc");
	}

	function _get_ns_next($arr)
	{
		$h = date("H");
		if (date("i") >= 30)
		{
			$h++;
			$m = 0;
		}
		else
		{
			$m = 30;
		}
		$tm = mktime($h, $m, 0, date("m"), date("d"), date("Y"));

		$arr["prop"]["value"] = date("d.m.Y H:i", $tm);
	}

	function _get_ns_manual($arr)
	{
		$arr["prop"]["value"] = html::href(array(
			"url" => aw_url_change_var("action", "scan_sites"),
			"caption" => t("K&auml;ivita kohe")
		));
	}

	/**
		@attrib name=scan_sites nologin="1"
		@param id required type=int
	**/
	function scan_sites($arr)
	{
		ob_end_clean();
		aw_set_exec_time(AW_LONG_PROCESS);
		echo "testing sites ... <br>\n";
		flush();

		$o = obj($arr["id"]);

		$cnt = $this->db_fetch_field("SELECT count(*) as cnt FROM aw_site_list s INNER JOIN objects o on o.oid=s.aw_oid WHERE o.status > 0 AND s.site_used = 1 AND s.last_update > ".(time() - 24*3600*30), "cnt");

		$errs = array();

		$num = 1;
		$this->db_query("SELECT * FROM aw_site_list s INNER JOIN objects o on o.oid=s.aw_oid WHERE o.status > 0 AND s.site_used = 1 AND s.last_update > ".(time() - 24*3600*30));
		while ($row = $this->db_next())
		{
			if ($row["aw_no_notify"] == 1)
			{
				continue;
			}
			echo sprintf("%03d/%03d", $num, $cnt)." ".$row["url"]." .... \n";
			flush();

			ob_start();
			$fc = strtolower(file_get_contents($row["url"]));
			$ct = ob_get_contents();
			ob_end_clean();

			if (strpos($ct, "401") !== false)
			{
				// auth req, assume site is ok
				$fc = "<html";
				$ar = " (auth required) ";
			}
			else
			{
				echo $ct;
				$ar = "";
			}

			if ((strpos($fc, "<body") !== false || strpos($fc, "<html") !== false || strpos($fc, "<head") !== false || $fc == "") && strpos($fc, "Suhtuge veateadetesse rahulikult") === false)
			{
				echo " <font color=green>Success</font> $ar<br>\n";
				flush();
			}
			else
			{
				echo " <font color=red>Failed</font><br>\n";
				echo "<pre>".htmlentities($fc)."</pre>";
				flush();
				$errs[] = array(
					"site" => $row,
					"content" => "sait $row[url] tundub maas olevat, esilehe sisu: \n".$fc."\n\n",
					"error" => $fc
				);
				$this->save_handle();
				$this->_handle_scan_fail($row, $fc, $o);
				$this->restore_handle();
			}
			$num++;
		}

		if (count($errs) > 0)
		{
			foreach($errs as $entry)
			{
				send_mail("automatweb.com-dev@lists.automatweb.com", "SAIT MAAS!!", $entry["content"], "From: big@brother.ee");
				$this->_save_sent_mail($entry["site"], $entry["content"], $entry["error"], $o, "dev@struktuur.ee");
			}
		}
		die(t("All done"));
	}

	private function _handle_scan_fail($site, $content, $o)
	{
		// go over rules
		foreach($o->connections_from(array("type" => "RELTYPE_NOTIFICATION_RULE")) as $c)
		{
			$t = $c->to();
			if (preg_match("/".preg_quote($t->err_content)."/", $content, $mt))
			{
				$this->_apply_scan_rule($t, $site, $content, $o);
			}
		}

		// check site entry for email
		if ($this->can("view", $site["aw_oid"]))
		{
			$so = obj($site["aw_oid"]);
			if ($so->mail_to != "")
			{
				$mc = "Sait ".$site["url"]." tundub maas olevat!\n\nEsilehel sisu:\n$content";
				send_mail(
					$so->mail_to,
					t("Sait maas!"),
					$mc
				);
				$this->_save_sent_mail($site, $mc, $content, $o, $so->mail_to);
			}
		}
	}

	private function _save_sent_mail($site, $content, $err, $o, $to, $rule = null)
	{
		$s = obj();
		$s->set_parent($o->id());
		$s->set_class_id(CL_SITE_NOTIFICATION_SENT);
		$s->set_name(sprintf(t("Viga saidil %s"), $site["url"]));
		$s->set_prop("site", $site["aw_oid"]);
		$s->set_prop("rule", $rule);
		$s->set_prop("when", time());
		$s->set_prop("who", $to);
		$s->set_prop("content", $mc);
		$s->set_prop("error", $err);
		aw_disable_acl();
		$s->save();
		aw_restore_acl();
	}

	private function _apply_scan_rule($rule, $site, $content, $mgr)
	{
		$c = $rule->mail_content;
		$c = str_replace("%site%", $site["url"], $c);
		send_mail(
			$rule->mail_to,
			$rule->mail_subj,
			$c
		);
		$this->_save_sent_mail($site, $c, $content, $mgr, $rule->mail_to, $rule->id());
	}

	function _get_classes_props_tree($arr)
	{
		$clf = aw_ini_get("classfolders");
		foreach($clf as $id => $data)
		{
			$arr["prop"]["vcl_inst"]->add_item($data["parent"], array(
				"name" => $data["name"],
				"id" => $id,
				"url" => aw_url_change_var("clf", $id, aw_url_change_var("grp", null))
			));
		}
		if (!empty($arr["request"]["clf"]))
		{
			$arr["prop"]["vcl_inst"]->set_selected_item($arr["request"]["clf"]);
		}

		$clf = aw_ini_get("classes");
		foreach($clf as $id => $data)
		{
			foreach(explode(",", ifset($data, "parents")) as $pt)
			{
				$arr["prop"]["vcl_inst"]->add_item($pt, array(
					"name" => $data["name"],
					"id" => "cls_".$id,
					"iconurl" => icons::get_icon_url(CL_AW_SITE_ENTRY),
					"url" => aw_url_change_var("cls", $id, aw_url_change_var("grp", null))
				));
			}
		}
		if (!empty($arr["request"]["cls"]))
		{
			$arr["prop"]["vcl_inst"]->set_selected_item("cls_".$arr["request"]["cls"]);
		}

		$arr["prop"]["vcl_inst"]->set_root_name(t("Klassid"));
		$arr["prop"]["vcl_inst"]->set_root_url(aw_url_change_var("clf", null, aw_url_change_var("cls", null)));
	}

	function _get_classes_props_groups($arr)
	{
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "smc",
				"persist_state" => true,
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array(CL_SM_PROP_STATS_GROUP),
				"parent" => $arr["obj_inst"]->id()
			)),
			"var" => "grp"
                ));
		foreach($arr["prop"]["vcl_inst"]->get_item_ids() as $id)
		{
			if ($id == $arr["obj_inst"]->id())
			{
				continue;
			}
			$d = $arr["prop"]["vcl_inst"]->get_item($id);
			$d["name"] .= " ".html::get_change_url($id, array("return_url" => get_ru()), html::img(array("url" => aw_ini_get("baseurl")."/automatweb//images/icons/edit.gif", "border" => "0")));
			$d["name"] .= " ".html::href(array(
				"url" => $this->mk_my_orb("delete", array("id" => $id, "return_url" => get_ru()), CL_SM_PROP_STATS_GROUP),
				"caption" => html::img(array("url" => aw_ini_get("baseurl")."/automatweb//images/icons/delete.gif", "border" => "0"))
			));
			$arr["prop"]["vcl_inst"]->set_item($d);
		}
	}

	private function _init_clp_list($t, $o)
	{
		$t->define_field(array(
			"name" => "caption",
			"caption" => t("Omadus"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "store",
			"caption" => t("Salvestatav?"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "table",
			"caption" => t("Tabel"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "field",
			"caption" => t("Tulp"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "project",
			"caption" => t("Projekt"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "method",
			"caption" => t("Salvestamise meetod"),
			"align" => "left",
			"sortable" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "id"
		));
		$t->define_field(array(
			"name" => "grps",
			"caption" => t("Grupid"),
			"align" => "center",
		));
		$ot = new object_tree(array(
			"class_id" => array(CL_SM_PROP_STATS_GROUP),
			"parent" => $o->id()
		));

		foreach($ot->to_list()->arr() as $o)
		{
			$t->define_field(array(
				"name" => $o->id(),
				"caption" => $o->name(),
				"align" => "center",
				"parent" => "grps"
			));
		}
	}

	function _get_classes_props_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_clp_list($t, $arr["obj_inst"]);

		if (empty($arr["request"]["cls"]))
		{
			return;
		}

		$cls = aw_ini_get("classes");
		$c = $cls[$arr["request"]["cls"]];
		$t->set_caption(sprintf(t("Klassi %s omadused"), $c["name"]));


		$ot = new object_tree(array(
			"class_id" => array(CL_SM_PROP_STATS_GROUP),
			"parent" => $arr["obj_inst"]->id()
		));
		$grp_list = $ot->to_list()->arr();
		$grps = array();
		foreach($grp_list as $grp_id => $grp_obj)
		{
			foreach($grp_obj->connections_from() as $con)
			{
				list($clid_n, $prop) = explode("::", $con->prop("to.name"));
				if ($c["def"] == $clid_n)
				{
					$grps[$prop][$grp_id] = 1;
				}
			}
		}


		$tmp = obj();
		$tmp->set_class_id($arr["request"]["cls"]);

		foreach($this->_list_props($tmp, $arr["obj_inst"]) as $pn => $pd)
		{

			foreach($grp_list as $grp_id => $grp_obj)
			{
				$pd[$grp_id] = html::checkbox(array(
					"name" => "grps[$grp_id][$pn]",
					"value" => 1,
					"checked" => !empty($grps[$pn][$grp_id])
				));
			}

			$pd["caption"] = html::href(array(
				"url" => html::get_change_url($pd["id"], array("return_url" => get_ru())),
				"caption" => parse_obj_name(ifset($pd, "caption"))
			));
			$pd["customer"] = html::obj_change_url(obj($pd["id"])->customer);
			$pd["project"] = html::obj_change_url(obj($pd["id"])->project);
			$t->define_data($pd);
		}
	}

	function _set_classes_props_list($arr)
	{
		$tmp = obj();
		$tmp->set_class_id($arr["request"]["cls"]);

		$ot = new object_tree(array(
			"class_id" => array(CL_SM_PROP_STATS_GROUP),
			"parent" => $arr["obj_inst"]->id()
		));

		$cls = aw_ini_get("classes");
		$c = $cls[$arr["request"]["cls"]];

		$grp_list = $ot->to_list()->arr();
		$grps = array();
		foreach($grp_list as $grp_id => $grp_obj)
		{
			foreach($grp_obj->connections_from() as $con)
			{
				list($clid_n, $prop) = explode("::", $con->prop("to.name"));

				if ($c["def"] == $clid_n)
				{
					$grps[$prop][$grp_id] = 1;
				}
			}
		}

		foreach($this->_list_props($tmp, $arr["obj_inst"]) as $pn => $pd)
		{
			foreach($grp_list as $grp_id => $grp)
			{
				if (!empty($arr["request"]["grps"][$grp_id][$pn]) && empty($grps[$pn][$grp_id]))
				{
					$grp->connect(array(
						"to" => $pd["id"],
						"type" => "RELTYPE_PROP"
					));
				}
				else
				if (empty($arr["request"]["grps"][$grp_id][$pn]) && !empty($grps[$pn][$grp_id]))
				{
					$grp->disconnect(array(
						"from" => $pd["id"]
					));
				}
			}
		}
	}

	private function _list_props($o, $obj_inst)
	{
		$cls = aw_ini_get("classes");
		$c = $cls[$o->class_id()];
		$pl = $o->get_property_list();
		$nms = map($c["def"]."::%s", array_keys($pl));

		$ol = new object_list(array(
			"class_id" => CL_AW_CLASS_PROPERTY,
			"lang_id" => array(),
			"site_id" => array(),
			"name" => $nms
		));
		foreach($ol->arr() as $o)
		{
			list(, $pn) = explode("::", $o->name());
			$pl[$pn]["id"] = $o->id();
		}

		foreach($pl as $pn => $pd)
		{
			if (!$pd["id"])
			{
				$p = obj();
				$p->set_parent($obj_inst->id());
				$p->set_class_id(CL_AW_CLASS_PROPERTY);
				$p->set_name($c["def"]."::".$pn);
				$pl[$pn]["id"] = $p->save();
			}
		}
		return $pl;
	}

	function _get_props_toolbar($arr)
	{
		$pt = isset($arr["request"]["grp"]) ? $arr["request"]["grp"] : $arr["obj_inst"]->id();
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"onClick" => "len = document.changeform.elements.length;str  = '';
	for(i = 0; i < len; i++)
	{
		if (document.changeform.elements[i].name.indexOf('sel') != -1 && document.changeform.elements[i].checked)
		{
			str += '&sel['+document.changeform.elements[i].value+']='+document.changeform.elements[i].value;
		}
	}

window.location.href='".html::get_new_url(CL_SM_PROP_STATS_GROUP, $pt, array("return_url" => get_ru()))."&'+str;",
			"url" => "#",
			"tooltip" => "new"
		));
	}

	function _set_classes_list($arr)
	{
		$ot = new object_tree(array(
			"class_id" => array(CL_SM_CLASS_STATS_GROUP),
			"parent" => $arr["obj_inst"]->id()
		));

		$grp_list = $ot->to_list()->arr();
		foreach($grp_list as $grp_id => $grp_obj)
		{
			$grp_obj->set_prop("class_list", $this->make_keys(array_keys($arr["request"]["grps"][$grp_id])));
			$grp_obj->save();
		}

	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["cls"] = $arr["request"]["cls"];
	}

	/**
		@attrib name=gather_stats nologin="1"
	**/
	function gather_stats()
	{
		ob_end_clean();
//			automatweb::$instance->mode(automatweb::MODE_DBG);
		$site_list = get_instance("install/site_list")->get_site_list();
//die(dbg::dump($site_list));
	/*	foreach($site_list as $site)
		{
			list($srv, $cp) = explode(":", $site["code_branch"]);
//echo "site = $site[url] srv = $srv , cp $cp <br>";
			if (($cp == "/www/automatweb_cvs" || $cp == "/www/automatweb_cvs/") && $site["id"] != 33)
			{
//echo dbg::dump($site);
				$this->db_query("DELETE FROM aw_site_object_stats WHERE site_id = ".$site["id"]);
				$rv = $this->do_orb_method_call(array(
					"action" => "clid_stats",
					"class" => "sys",
					"params" => array(),
					"method" => "xmlrpc",
					"server" => $site["url"],
					"no_errors" => 1
				));
//echo dbg::dump($rv);
				if (is_array($rv))
	 			{
					foreach($rv as $row)
					{
						$this->db_query("INSERT INTO aw_site_object_stats(".join(",", array_keys($row)).") VALUES(".join(",", map("'%s'", array_values($row))).")");
					}
				}
				echo "did site $site[url] <br>\n";
				flush();
			}
		}*/

		set_time_limit(0);
		aw_global_set("__from_raise_error", 1);
		foreach($site_list as $site)
		{
			list($srv, $cp) = explode(":", $site["code_branch"]);
//echo "site = $site[url] srv = $srv , cp $cp <br>";
			if (($cp == "/www/automatweb_cvs" || $cp == "/www/automatweb_cvs/") && $site["id"] != 33)
			{
//echo dbg::dump($site);
				$this->db_query("DELETE FROM aw_site_class_prop_stats WHERE site_id = ".$site["id"]);
				$rv = $this->do_orb_method_call(array(
					"action" => "prop_stats",
					"class" => "sys",
					"params" => array(),
					"method" => "xmlrpc",
					"server" => $site["url"],
					"no_errors" => 1
				));
//echo dbg::dump($rv);
				if (is_array($rv))
	 			{
					foreach($rv as $row)
					{
						$this->db_query("INSERT INTO aw_site_class_prop_stats(".join(",", array_keys($row)).") VALUES(".join(",", map("'%s'", array_values($row))).")");
					}
				}
				echo "2did site $site[url] res = ".count($rv)." <br>\n";
				flush();
			}
		}
		die("all done");
	}

	function _get_sites_servers_grapx($arr)
	{
		return $this->_get_sites_list_grapx($arr);
	}

	function _get_sites_list_grapx($arr)
	{
		$this->db_query("SELECT s.name as name, s.ip as ip, count(*) as cnt FROM aw_site_list LEFT JOIN aw_server_list s ON s.id = aw_site_list.server_id WHERE site_used = 1 GROUP BY server_id ORDER BY cnt DESC LIMIT 10");
		$d = array();
		$l = array();
		$max = 0;
		while ($row = $this->db_next())
		{
			if ($row["cnt"] < 2)
			{
				continue;
			}
			$d[] = $row["cnt"];
			$l[] = ($row["name"] != "" ? $row["name"] : $row["ip"])." (".$row["cnt"].")";
			$max = max($max, $row["cnt"]);
		}

		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE);
		$c->set_size(array("width" => 600, "height" => 300));
		$c->add_data($d);
		$c->set_labels($l);
		$c->set_title(array("text" => t("Saitide jaotus serverites TOP 10")));
		$c->set_axis(array(
			GCHART_AXIS_LEFT
		));
		$c->add_fill(array("area" => GCHART_FILL_BACKGROUND,"type" => GCHART_FILL_SOLID,"colors" => array("color" => "E9E9E9")));
		$c->set_grid(array("xstep" => 20, "ystep" => 20));
		$c->set_bar_sizes(array("width" => (600 / count($d)) - 5, "bar_spacing" => 5));
		$c->add_axis_range(0, array(0, $max));
	}

	function _get_errors_list_grapx($arr)
	{
		$this->db_query("SELECT site, count(*) as cnt FROM bugtrack_errors  GROUP BY site ORDER BY cnt DESC LIMIT 10");
		$d = array();
		$l = array();
		$max = 0;
		while ($row = $this->db_next())
		{
			if ($row["cnt"] < 2)
			{
				continue;
			}
			$d[] = $row["cnt"];
			$l[] = $row["site"]." (".$row["cnt"].")";
			$max = max($max, $row["cnt"]);
		}

		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE);
		$c->set_size(array("width" => 600, "height" => 300));
		$c->add_data($d);
		$c->set_labels($l);
		$c->set_title(array("text" => t("Vigade jaotus saitides TOP 10")));
		$c->set_axis(array(
			GCHART_AXIS_LEFT
		));

		$c->add_fill(array("area" => GCHART_FILL_BACKGROUND,"type" => GCHART_FILL_SOLID,"colors" => array("color" => "E9E9E9")));

		$c->set_grid(array("xstep" => 20, "ystep" => 20));
		$c->set_bar_sizes(array("width" => (400 / count($d)) - 5, "bar_spacing" => 5));
		$c->add_axis_range(0, array(0, $max));
	}

	function _get_errors_list_grapx_days($arr)
	{
		$tm = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));
		$this->db_query("SELECT tm, count(*) as cnt FROM bugtrack_errors  WHERE tm > $tm GROUP BY DAYOFYEAR(FROM_UNIXTIME(tm)) ORDER BY cnt DESC LIMIT 10");
		$d = array();
		$l = array();
		$max = 0;
		while ($row = $this->db_next())
		{
			if ($row["cnt"] < 2)
			{
				continue;
			}
			$d[] = $row["cnt"];
			$l[] = date("d.m.Y", $row["tm"])." (".$row["cnt"].")";
			$max = max($max, $row["cnt"]);
		}

		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE);
		$c->set_size(array("width" => 600, "height" => 300));
		$c->add_data($d);
		$c->set_labels($l);
		$c->set_title(array("text" => t("Vigade jaotus viimase kuu p&auml;evade l&otilde;ikes TOP 10")));
		$c->set_axis(array(
			GCHART_AXIS_LEFT
		));
		$c->add_fill(array("area" => GCHART_FILL_BACKGROUND,"type" => GCHART_FILL_SOLID,"colors" => array("color" => "E9E9E9")));
		$c->set_grid(array("xstep" => 20, "ystep" => 20));
		$c->set_bar_sizes(array("width" => (400 / count($d)) - 5, "bar_spacing" => 5));
		$c->add_axis_range(0, array(0, $max));
	}

	function _get_cl_usage_stats_list_gpx($arr)
	{
		$tm = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));

		$this->db_query("SELECT SUM(count) as cnt, class_id FROM aw_site_object_stats GROUP BY class_id ORDER BY cnt desc LIMIT 10");
		$d = array();
		$l = array();
		$max = 0;
		$clss = aw_ini_get("classes");
		while ($row = $this->db_next())
		{
			$d[] = $row["cnt"];
			$l[] = $clss[$row["class_id"]]["name"]." (".$row["cnt"].")";
			$max = max($max, $row["cnt"]);
		}

		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE);
		$c->set_size(array("width" => 600, "height" => 300));
		$c->add_data($d);
		$c->set_labels($l);
		$c->set_title(array("text" => t("Objektide kasutuse TOP 10")));
		$c->set_axis(array(
			GCHART_AXIS_LEFT
		));
		$c->add_fill(array("area" => GCHART_FILL_BACKGROUND,"type" => GCHART_FILL_SOLID,"colors" => array("color" => "E9E9E9")));
		$c->set_grid(array("xstep" => 20, "ystep" => 20));
		$c->set_bar_sizes(array("width" => (400 / count($d)) - 5, "bar_spacing" => 5));
		$c->add_axis_range(0, array(0, $max));
	}

	function _get_site_usage_stats_list_gpx($arr)
	{
		$tm = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));

		$this->db_query("SELECT SUM(count) as cnt, site_id FROM aw_site_object_stats GROUP BY site_id ORDER BY cnt desc LIMIT 10");
		$d = array();
		$l = array();
		$max = 0;
		$sl = get_instance("site_list");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			$d[] = $row["cnt"];
			$l[] = $sl->get_url_for_site($row["site_id"])." (".$row["cnt"].")";
			$max = max($max, $row["cnt"]);
			$this->restore_handle();
		}

		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE);
		$c->set_size(array("width" => 600, "height" => 300));
		$c->add_data($d);
		$c->set_labels($l);
		$c->set_title(array("text" => t("Objektide kasutus saitide kaupa TOP 10")));
		$c->set_axis(array(
			GCHART_AXIS_LEFT
		));
		$c->add_fill(array("area" => GCHART_FILL_BACKGROUND,"type" => GCHART_FILL_SOLID,"colors" => array("color" => "E9E9E9")));
		$c->set_grid(array("xstep" => 20, "ystep" => 20));
		$c->set_bar_sizes(array("width" => (400 / count($d)) - 5, "bar_spacing" => 5));
		$c->add_axis_range(0, array(0, $max));
	}

	function _get_cl_usage_props_stats_list_gpx($arr)
	{
		if (empty($arr["request"]["class_id"]))
		{
			return PROP_IGNORE;
		}
		$tm = mktime(0, 0, 0, date("m")-1, date("d"), date("Y"));

		$total_site_count = $this->db_fetch_field("SELECT count(distinct(site_id)) as cnt from aw_site_class_prop_stats", "cnt");
		$class_id = $arr["request"]["class_id"];
		$this->db_query("SELECT prop,  ((100 *  sum(set_objs)) / sum(total_objs)) as cnt  FROM aw_site_class_prop_stats WHERE class_id = $class_id GROUP BY prop ORDER BY cnt desc LIMIT 10");

		$d = array();
		$l = array();
		$max = 0;
		$pl = obj()->set_class_id($class_id)->get_property_list();

		while ($row = $this->db_next())
		{
			$this->save_handle();
			$d[] = $row["cnt"];
			$l[] = $pl[$row["prop"]]["caption"]." (".number_format($row["cnt"], 2)."%)";
			$max = max($max, $row["cnt"]);
			$this->restore_handle();
		}

		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE);
		$c->set_size(array("width" => 600, "height" => 300));
		$c->add_data($d);
		$c->set_labels($l);
		$c->set_title(array("text" => t("Omaduste kasutus TOP 10")));
		$c->set_axis(array(
			GCHART_AXIS_LEFT
		));
		$c->add_fill(array("area" => GCHART_FILL_BACKGROUND,"type" => GCHART_FILL_SOLID,"colors" => array("color" => "E9E9E9")));
		$c->set_grid(array("xstep" => 20, "ystep" => 20));
		$c->set_bar_sizes(array("width" => (400 / count($d)) - 5, "bar_spacing" => 5));
		$c->add_axis_range(0, array(0, $max));
	}

	function _get_sites_sites_toolbar($arr)
	{
		$pt = isset($arr["request"]["grp"]) ? $arr["request"]["grp"] : $arr["obj_inst"]->id();
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"onClick" => "len = document.changeform.elements.length;str  = '';
	for(i = 0; i < len; i++)
	{
		if (document.changeform.elements[i].name.indexOf('sel') != -1 && document.changeform.elements[i].checked)
		{
			str += '&sel['+document.changeform.elements[i].value+']='+document.changeform.elements[i].value;
		}
	}

window.location.href='".html::get_new_url(CL_SM_SITE_GROUP, $pt, array("return_url" => get_ru()))."&'+str;",
			"url" => "#",
			"tooltip" => "new"
		));
	}

	function _get_sites_sites_grp_tree($arr)
	{
		$arr["prop"]["vcl_inst"] = treeview::tree_from_objects(array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "smc",
				"persist_state" => true,
			),
			"root_item" => $arr["obj_inst"],
			"ot" => new object_tree(array(
				"class_id" => array(CL_SM_SITE_GROUP),
				"parent" => $arr["obj_inst"]->id()
			)),
			"var" => "grp"
                ));
		foreach($arr["prop"]["vcl_inst"]->get_item_ids() as $id)
		{
			if ($id == $arr["obj_inst"]->id())
			{
				continue;
			}
			$d = $arr["prop"]["vcl_inst"]->get_item($id);
			$d["name"] .= " ".html::get_change_url($id, array("return_url" => get_ru()), html::img(array("url" => aw_ini_get("baseurl")."/automatweb//images/icons/edit.gif", "border" => "0")));
			$d["name"] .= " ".html::href(array(
				"url" => $this->mk_my_orb("delete", array("id" => $id, "return_url" => get_ru()), CL_SM_PROP_STATS_GROUP),
				"caption" => html::img(array("url" => aw_ini_get("baseurl")."/automatweb//images/icons/delete.gif", "border" => "0"))
			));
			$arr["prop"]["vcl_inst"]->set_item($d);
		}
	}

	function _get_notif_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_SITE_NOTIFICATION_RULE), $arr["obj_inst"]->id(), 1 /* RELTYPE_NOTIFICATION_RULE */);
		$tb->add_delete_button();

	}

	function _get_notifications_rules($arr)
	{
		$arr["prop"]["vcl_inst"]->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_NOTIFICATION_RULE"))),
			array("name","err_content","mail_to","mail_subj","mail_content"),
			CL_SITE_NOTIFICATION_RULE
		);
		$arr["prop"]["vcl_inst"]->set_caption(t("Teavituste saatmise reeglid"));
	}

	public function _get_notifications_sent_site_tree($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		// list groups and sites in groups in tree
		$ol = new object_list(array(
			"class_id" => CL_SM_SITE_GROUP,
			"site_id" => array(),
			"lang_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			$tv->add_item(0, array(
				"id" => $o->id(),
				"name" => $o->name(),
				"url" => aw_url_change_var("grp", $o->id(), aw_url_change_var("rule", null, aw_url_change_var("site", null)))
			));
			foreach($o->get_sites_in_group() as $site)
			{
				$tv->add_item($o->id(), array(
					"id" => $o->id()."_".$site->id(),
					"name" => $site->name(),
					"url" => aw_url_change_var("site", $site->id(), aw_url_change_var("grp", $o->id() , aw_url_change_var("rule", null)))
				));
			}
		}
		if ($arr["request"]["site"])
		{
			$tv->set_selected_item($arr["request"]["grp"]."_".$arr["request"]["site"]);
		}
		else
		{
			$tv->set_selected_item($arr["request"]["grp"]);
		}
	}

	public function _get_notifications_sent_site_tree_rules($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		// list groups and sites in groups in tree
		$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_NOTIFICATION_RULE")));
		foreach($ol->arr() as $o)
		{
			$tv->add_item(0, array(
				"id" => $o->id(),
				"name" => parse_obj_name($o->name()),
				"url" => aw_url_change_var("rule", $o->id(), aw_url_change_var("grp", null, aw_url_change_var("site", null)))
			));
		}
		$tv->set_selected_item($arr["request"]["rule"]);
	}

	public function _get_notifications_sent_grapx($arr)
	{
		$filt = array(
			"class_id" => CL_SITE_NOTIFICATION_SENT,
			"lang_id" => array(),
			"site_id" => array()
		);

		if (!empty($arr["request"]["grp"]))
		{
			$gp = obj($arr["request"]["grp"]);
			$sl = array();
			foreach($gp->get_sites_in_group() as $site)
			{
				$sl[] = $site->id();
			}
			$filt["site"] = $sl;
		}
		else
		if (!empty($arr["request"]["rule"]))
		{
			$gp = obj($arr["request"]["grp"]);
			$filt["rule"] = $arr["request"]["rule"];
		}

		$ol = new object_list($filt);
		$ids = new aw_array($ol->ids());

		$this->db_query("SELECT aw_site, count(*) as cnt FROM aw_site_notification_sent WHERE aw_oid IN (".$ids->to_sql().") GROUP BY aw_site ORDER BY cnt DESC LIMIT 10");
		$d = array();
		$l = array();
		$max = 0;
		while ($row = $this->db_next())
		{
			$this->save_handle();
			$d[] = $row["cnt"];
			$l[] = obj($row["aw_site"])->name();
			$max = max($max, $row["cnt"]);
			$this->restore_handle();
		}

		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE);
		$c->set_size(array("width" => 600, "height" => 300));
		$c->add_data($d);
		$c->set_labels($l);
		$c->set_title(array("text" => t("Teavituste TOP 10 saitide kaupa")));
		$c->set_axis(array(
			GCHART_AXIS_LEFT
		));
		$c->add_fill(array("area" => GCHART_FILL_BACKGROUND,"type" => GCHART_FILL_SOLID,"colors" => array("color" => "E9E9E9")));
		$c->set_grid(array("xstep" => 20, "ystep" => 20));
		$c->set_bar_sizes(array("width" => (500 / count($d)) - 5, "bar_spacing" => 5));
		$c->add_axis_range(0, array(0, $max));
	}
}
?>
