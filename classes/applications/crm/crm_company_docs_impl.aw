<?php

// tf parameter format is $docs_folder_oid|$selected_item_id. $docs_folder_oid|$docs_folder_oid when no subitem selected.

class crm_company_docs_impl extends class_base
{
	function crm_company_docs_impl()
	{
		$this->init();

		$this->adds = array(
			CL_MENU => t("Kaust"),
			CL_CRM_DOCUMENT => t("CRM Dokument"),
			CL_CRM_DEAL => t("Leping"),
			CL_CRM_MEMO => t("Memo"),
			CL_DOCUMENT => t("Sisuhalduse dokument"),
			CL_FILE => t("Fail"),
			CL_CRM_OFFER => t("Pakkumine")
		);
	}

	function _init_docs_fld($o)
	{
		aw_disable_acl();
		$tmp = $o->get_first_obj_by_reltype("RELTYPE_DOCS_FOLDER");
		aw_restore_acl();
		if ($tmp)
		{
			return $tmp;
		}
		$fldo = obj();
		$fldo->set_parent($o->id());
		$fldo->set_class_id(CL_MENU);
		$fldo->set_name($o->name().t(" dokumendid"));
		$fldo->save();

		$o->connect(array(
			"to" => $fldo->id(),
			"reltype" => "RELTYPE_DOCS_FOLDER"
		));

		return $fldo;
	}

	function _init_content_docs_fld($o)
	{
		$fldo = $o->get_first_obj_by_reltype("RELTYPE_CONTENT_DOCS_FOLDER");
		if (!$fldo)
		{
			$fldo = obj();
			$fldo->set_parent($o->id());
			$fldo->set_class_id(CL_MENU);
			$fldo->set_name($o->name().t(" uudised"));
			$fldo->save();

			$o->connect(array(
				"to" => $fldo->id(),
				"reltype" => "RELTYPE_CONTENT_DOCS_FOLDER"
			));
		}

		return $fldo;
	}

	function _get_docs_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		list($fld_oid, $sel_id) = explode("|", $arr["request"]["tf"]);
		$parent = null;

		if ($this->can("view", $sel_id)) // todo: implement checking server and ftp folders add rights
		{
			$parent = $sel_id;
		}
		elseif ($this->can("view", $fld_oid)) // todo: implement checking server and ftp folders add rights
		{
			$parent = $fld_oid;
		}
		else
		{ // default docs folder
			$parent = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_DOCS_FOLDER");

			if (false === $parent)
			{ // no docs folder found. create&connect-to new folder.
				$fld = $this->_init_docs_fld($arr["obj_inst"]);
				$parent = $fld->id();
			}
			else
			{
				$parent = $parent->id();
			}
		}

		if (isset($parent))
		{
			foreach($this->adds as $clid => $nm)
			{
				$tb->add_menu_item(array(
					'parent'=>'add_item',
					'text' => $nm,
					'link' => html::get_new_url($clid, $parent, array("return_url" => get_ru()))
				));
			}
		}

		$tb->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud'),
			'action' => 'submit_delete_docs',
		));

		$tb->add_button(array(
			'name' => 'cut',
			'img' => 'cut.gif',
			'tooltip' => t('L&otilde;ika'),
			'action' => 'cut_docs',
		));

		if (count(safe_array($_SESSION["crm_cut_docs"])))
		{
			$tb->add_button(array(
				'name' => 'paste',
				'img' => 'paste.gif',
				'tooltip' => t('Kleebi'),
				'action' => 'submit_paste_docs',
			));
		}

		if (isset($parent))
		{
			$tb->add_separator();

			$id = admin_if::find_admin_if_id();
			$tb->add_button(array(
				"name" => "import",
				"tooltip" => t("Impordi faile"),
				"url" => $this->mk_my_orb("change", array("integrated" => 1, "id" => $id, "group" => "fu", "parent" => $parent, "return_url" => get_ru()), CL_ADMIN_IF),
				"img" => "import.gif",
			));
		}
		$file_manager = get_instance("admin/file_manager");
		$file_manager->add_zip_button(array("tb" => &$tb));
	}

	function _get_level_folders_from_fld($fldo, $sel, $get_f)
	{
		$rv = array();
		switch($fldo->class_id())
		{
			case CL_MENU:
				$ol = new object_list(array(
					"parent" => $sel ? $sel : $fldo->id(),
					"lang_id" => array(),
					"class_id" => CL_MENU,
					"sort_by" => "objects.jrk ASC , objects.name ASC",
				));
				foreach($ol->arr() as $o)
				{
					$rv[$o->id()] = array(
						"id" => $o->id(),
						"name" => $o->name(),
						"has_subs" => 0,
						"class_id" => $o->class_id(),
						"createdby" => $o->createdby(),
						"created" => $o->created(),
						"comment" => $o->comment(),
						"modifiedby" => $o->modifiedby(),
						"modified" => $o->modified(),
						"jrk" => $o->ord(),
						"url" => aw_url_change_var("tf" , $fldo->id()."|".$o->id())
					);
				}
				if (count($rv))
				{
					$ol = new object_list(array(
						"parent" => array_keys($rv),
						"lang_id" => array(),
						"class_id" => CL_MENU
					));
					foreach($ol->arr() as $o)
					{
						$rv[$o->parent()]["has_subs"] = 1;
					}
				}

				if ($get_f)
				{
					$applicable_classes = $this->adds;
					unset($applicable_classes[CL_MENU]);
					$applicable_classes = array_keys($applicable_classes);

					$ol3 = new object_list(array(
						"class_id" => $applicable_classes,
						"lang_id" => array(),
						"parent" => $sel ? $sel : $fldo->id(),
						"sort_by" => "objects.name ASC",
					));
					$ol3->sort_by(array(
						"prop" => "ord",
						"order" => "asc"
					));
					$file_inst = get_instance(CL_FILE);
					foreach($ol3->arr() as $o)
					{
						if ($o->class_id() == CL_FILE)
						{
							$url = $file_inst->get_url($o->id()).$o->name();
						}
						else
						{
							$url = html::get_change_url($o->id(), array("return_url" => get_ru()));
						}
						$id = $o->id();
						$rv[$id] = array(
							"id" => $id,
							"name" => $o->name(),
							"type" => "file",
							"has_subs" => 0,
							"url" => $url,
							"class_id" => $o->class_id(),
							"createdby" => $o->createdby(),
							"created" => $o->created(),
							"comment" => $o->comment(),
							"modifiedby" => $o->modifiedby(),
							"modified" => $o->modified(),
							"jrk" => $o->ord(),
						);
					}
				}
				return $rv;

			case CL_FTP_LOGIN:
				$i = $fldo->instance();
				$i->connect(array(
					"host" => $fldo->prop("server"),
					"user" => $fldo->prop("username"),
					"pass" => $fldo->prop("password"),
				));
				$p = trim($sel);
				if ($p == "")
				{
					$p = $fldo->prop("default_folder");
				}
				if ($p[0] != "/")
				{
					$p = "/".$p;
				}
				$files = $i->dir_list($p, true);
				foreach($files as $file)
				{
					$nf = $p."/".$file["name"];
					if ($file["type"] == "dir")
					{
						$rv[$nf] = array(
							"id" => $nf,
							"name" => $file["name"],
							"url" => aw_url_change_var("tf" , $fldo->id()."|".$nf),
						);
					}
					else
					if ($get_f)
					{
						$rv[$nf] = array(
							"id" => $nf,
							"name" => $file["name"],
							"type" => "file",
							"has_subs" => 0,
							"url" => $this->mk_my_orb("get_file", array("id" => $fldo->id(), "file" => $nf), $fldo->class_id())
						);
					}
				}
				return $rv;

			case CL_SERVER_FOLDER:
				if ($sel == "")
				{
					$dir = $fldo->prop("folder");
				}
				else
				{
					$dir = $sel;
				}
				if ($dh = @opendir($dir))
				{

					while (false !== ($file = readdir($dh)))
					{
						if ($file == "." || $file == "..")
						{
							continue;
						}

						$fn = $dir . "/" . $file;
						if (is_dir($fn))
						{
							$has_dirs = false;
							if ($dhs = @opendir($fn))
							{
								while (false !== ($subf = readdir($dhs)))
								{
									if ($subf == "." || $subf == "..")
									{
										continue;
									}

									if (is_dir($fn."/".$subf))
									{
										$has_dirs = true;
										break;
									}
								}
							}

							$d = stat($fn);
							$ud = posix_getpwuid($d[4]);
							$rv[$fn] = array(
								"id" => $fn,
								"name" => iconv("utf-8", aw_global_get("charset"), $file),
								"has_subs" => $has_dirs ? 1 : 0,
								"url" => aw_url_change_var("tf" , $fldo->id()."|".$fn),
								"createdby" => $ud["name"],
								"created" => filemtime($fn),
								"comment" => "",
								"modifiedby" => $ud["name"],
								"modified" => filemtime($fn),
							);
						}
						else
						if ($get_f)
						{
							$server_url = "file://\\".str_replace("/", "\\", $fn);
							$d = stat($fn);
							$ud = posix_getpwuid($d[4]);
							$rv[$fn] = array(
								"id" => $fn,
								"name" => iconv("utf-8", aw_global_get("charset"), $file),
								"type" => "file",
								"has_subs" => 0,
								"change_url" => $this->mk_my_orb("change_file", array("return_url" => get_ru(), "fid" => $fldo->id().":".$fn,"in_popup" => 1, "section" => aw_global_get("section")), "server_folder"),
								"server_url" => $server_url,
								"url" => $this->mk_my_orb("show_file", array("fid" => $fldo->id().":".$fn), $fldo->class_id()),
								"createdby" => $ud["name"],
								"created" => filemtime($fn),
								"comment" => "",
								"modifiedby" => $ud["name"],
								"modified" => filemtime($fn),
							);
						}
					}
				}
				closedir($dh);
				return $rv;
		}
	}

	/**
		@attrib name=get_tree_stuff all_args=1
	**/
	function get_tree_stuff($arr)
	{
		classload("core/icons");
		$seti = get_instance(CL_CRM_SETTINGS);
		$sts = $seti->get_current_settings();

		$tree = get_instance("vcl/treeview");
		$tree->start_tree(array (
			"type" => TREE_DHTML,
			"branch" => 1,
			"tree_id" => "offers_tree",
			// "persist_state" => 1
		));

		// get object we get stuff from
		list($fld_id, $sel) = explode("|", $arr["parent"]);
		$fld_id = trim($fld_id);
		$fldo = obj($fld_id);

		$get_f = $sts && $sts->prop("show_files_and_docs_in_tree");
		$cur_level_folders = $this->_get_level_folders_from_fld($fldo, $sel, $get_f);

		foreach($cur_level_folders as $entry)
		{
			$d = array(
				"id" => $fld_id . "|" . $entry["id"],
				"name" =>  $entry["name"],
				"iconurl" => icons::get_icon_url($entry["type"] === "file" ? CL_FILE : CL_MENU, $entry["name"]),
				"url" => $entry["type"] === "file"
					?
                                		$entry["url"]
					:
						aw_url_change_var("tf", $fld_id."|".$entry["id"] , $arr["set_retu"]),
			);

			$tree->add_item(0,$d);

			if (!isset($entry["has_subs"]) || $entry["has_subs"] == 1)
			{
				$tree->add_item($fld_id."|".$entry["id"], array(
					"id" => $fld_id."|".$entry["id"]."2",
					"name" => "dimi".$fld_id."|".$entry["id"],
					"url" => " ",
				));
			}
		}

		// get selected item
		preg_match ("/tf=([^\&]+)/", $arr["set_retu"], $active_node);

		if ($active_node[1])
		{
			$tree->set_selected_item(urldecode($active_node[1]));
		}

		die($tree->finalize_tree());
	}

	function _get_docs_tree($arr)
	{
		if(count($err = $_SESSION["docs_del_err"]))
		{
			$names = array();
			foreach($err as $oid)
			{
				$o = obj($oid);
				$names[] = $o->name();
			}
			$arr["prop"]["error"] = t("Osasid dokumente ei saanud kustutada, &otilde;igused puuduvad: ").implode(", ", $names);
			unset($_SESSION["docs_del_err"]);
		}
		if ($arr["request"]["do_doc_search"])
		{
			return PROP_IGNORE;
		}

		if (!$arr["request"]["tf"] && $arr["request"]["files_from_fld"] == "")
		{
			$arr["request"]["files_from_fld"] = "/";
		}

		// to open the load-on-demand tree to the selected branch
		$open_path = array();
		list($fld_id, $sel) = explode("|", $arr["request"]["tf"]);

		if ($fld_id !== $sel)
		{
			$o = new object($sel);

			do
			{
				$open_path[] = $fld_id ."|". $o->id();
				$o = new object($o->parent());
			}
			while (is_oid($o->parent()) and $o->id() != $fld_id);

			$open_path = array_reverse($open_path);
		}

		array_unshift($open_path, $fld_id); // top parent item

		classload("core/icons");
		$file_inst = get_instance(CL_FILE);
		$gbf = $this->mk_my_orb("get_tree_stuff",array(
			"set_retu" => aw_url_change_var(),
			"parent" => " ",
		));
		$arr["prop"]["vcl_inst"]->start_tree (array (
			"type" => TREE_DHTML,
			"tree_id" => "crm_docs_t",
			"open_path" => $open_path,
			"get_branch_func" => $gbf,
			"has_root" => 1,
			// "persist_state" => 1,
			"root_name" => "",
			"root_url" => "#",
			"root_icon" => "images/transparent.gif",
		));

		foreach($arr["obj_inst"]->connections_from(array("type" => array("RELTYPE_SERVER_FOLDER", "RELTYPE_DOCS_FOLDER"))) as $c)
		{
			$this->_render_folder_in_tree($arr, $c->to());
		}

		$arr["prop"]["vcl_inst"]->set_selected_item($arr["request"]["tf"]);
	}

	function _init_docs_tbl(&$t, $r = array())
	{
		$t->define_field(array(
			"caption" => "",
			"name" => "icon",
			"align" => "center",
			"sortable" => 0,
			"width" => 1
		));

		$t->define_field(array(
			"caption" => t("Nimi"),
			"name" => "name",
			"align" => "left",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Kommentaar"),
			"name" => "comment",
			"align" => "left",
			"sortable" => 1
		));

		if ($r["files_from_fld"] == "")
		{
			$t->define_field(array(
				"caption" => t("T&uuml;&uuml;p"),
				"name" => "class_id",
				"align" => "center",
				"sortable" => 1
			));
		}

		$t->define_field(array(
			"caption" => t("Looja"),
			"name" => "createdby",
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Loodud"),
			"name" => "created",
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		if ($r["files_from_fld"] == "")
		{
			$t->define_field(array(
				"caption" => t("Muutja"),
				"name" => "modifiedby",
				"align" => "center",
				"sortable" => 1
			));
		}

		$t->define_field(array(
			"caption" => t("Muudetud"),
			"name" => "modified",
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"caption" => "",
			"name" => "pop",
			"align" => "center"
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function get_docs_table_header($sel, $id, $level, $fld)
	{
		// selected item
		if ($this->can("view", $sel))
		{
			$o = new object($sel);
		}
		elseif (is_oid($sel)) // may cause ambiguity when server folder name resembles an aw oid
		{
			return;
		}
		else
		{
			$o = $fld;// temporary. when ftp & server folder path reading implemented, this should be replaced with that.
		}

		$path = html::href(array(
			"url" => $this->mk_my_orb("change", array(
				"id" => $id,
				"return_url" => get_ru(),
				"group" => "documents_all",
				"docs_s_sbt" => $_GET["docs_s_sbt"],
				"docs_s_created_after" => $_GET["docs_s_created_after"],
				"tf" => $fld->id() . "|" . $o->id(),
			),
			 CL_CRM_COMPANY),
			"caption" => $o->name()?$o->name():"",
		));

		if($o->parent() != $id && $level < 3)
		{
			$path = $this->get_docs_table_header($o->parent(), $id, $level+1, $fld).$path.  " / " ;
		}
		elseif($o->parent() != $id)
		{
			$path = $this->get_docs_table_header($o->parent(), $id, $level+1, $fld);
		}
		elseif($o->parent() == $id)
		{
			$yah_caption = (", " . t("folder").": ");
			$path = html::href(array(
					"url" => $this->mk_my_orb("change", array(
					"id" => $id,
					"return_url" => get_ru(),
					"group" => "documents_all",
					"docs_s_sbt" => $_GET["docs_s_sbt"],
					"docs_s_created_after" => $_GET["docs_s_created_after"],
					"tf" => $fld->id() . "|" . $o->id(),
				),
				 CL_CRM_COMPANY),
				"caption" => $o->name() ? $o->name() : "...",
			)) . $yah_caption;
		}
		return $path;
	}

	// function to get level of item in tree
	// as it's needed by js toggle_children(htmlobj,level)
	// although not really sure about it because ajax tree itself has mostly 1 as level... sometimes 2
	function get_element_level_in_docs_table($arr,$oid)
	{
		if (false === $fld = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_DOCS_FOLDER"))
		{ // no docs folder found. create&connect-to new folder.
			$fld = $this->_init_docs_fld($arr["obj_inst"]);
		}

		$i_root = $fld->id();
		$i=0;
		$root_not_found = true;
		$i_parent = $oid;
		while(true)
		{
			$i++;
			if ($i_root==$i_parent)
			{
				return $i;
			}
			$o = obj($i_parent);
			$i_parent = $o->parent();
		}
	}

	function _get_docs_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		list($fld_id, $sel) = explode("|", $arr["request"]["tf"]);

		if (!is_oid($fld_id))
		{
			if (false === $fld = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_DOCS_FOLDER"))
			{ // no docs folder found. create&connect-to new folder.
				$fld = $this->_init_docs_fld($arr["obj_inst"]);
			}
		}
		elseif ($this->can("view", $fld_id))
		{
			$fld = new object($fld_id);
		}
		else
		{
			$arr["prop"]["error"] = t("Dokumentide kataloogile puudub juurdep&auml;&auml;su&otilde;igus");
			return PROP_ERROR;
		}

		$i_root = $fld->id();

		if(!$arr["request"]["tf"])
		{
			$format = t("%s dokumendid");
			$path =  sprintf($format, $arr['obj_inst']->name(), $fld->name());
		}
		else
		{
			$path = $this->get_docs_table_header($sel, $arr['obj_inst']->id(), 0, $fld);
		}

		$t->set_caption($path);
		$this->_init_docs_tbl($t, $arr["request"]);

		// load & apply table configuration if defined
		$default_cfg = true;
		$cl_crm_settings = get_instance(CL_CRM_SETTINGS);

		if ($o = $cl_crm_settings->get_current_settings())
		{
			$cl_crm_company = get_instance(CL_CRM_COMPANY);
			$usecase = $cl_crm_company->get_current_usecase($arr); //$arr["obj_inst"] peab olemas olema.
			$cl_crm_settings->apply_table_cfg($o, $usecase, $arr["prop"]["name"], &$t);
			$visible_fields = $cl_crm_settings->get_visible_fields($o, $usecase, $arr["prop"]["name"]);

			if (!empty($visible_fields))
			{
				$default_cfg = false;
			}
		}

		if (($arr["request"]["do_doc_search"] || $arr["request"]["docs_s_sbt"] != "") && !$arr["request"]["tf"])
		{
			// get all parents to search from
			$parent_tree = new object_tree(array(
				"parent" => $fld->id(),
				"class_id" => CL_MENU
			));
			$parent_ol = $parent_tree->to_list();
			$parents = $parent_ol->ids();
			$parents[] = $fld->id();
			$f = $this->_get_doc_search_f($arr["request"], $parents);
			$f["site_id"] = array();
			$f["lang_id"] = array();
			$ol = new object_list($f);

			$cur_level_folders = array();
			$file_inst = get_instance(CL_FILE);
			foreach($ol->arr() as $o)
			{
				if ($o->class_id() == CL_FILE)
				{
					$url = $file_inst->get_url($o->id()).$o->name();
				}
				else
				{
					$url = html::get_change_url($o->id(), array("return_url" => get_ru()));
				}
				$cur_level_folders[$o->id()] = array(
					"id" => $o->id(),
					"name" => $o->name(),
					"type" => $o->class_id() == CL_MENU ? "" : "file",
					"has_subs" => 0,
					"url" => $url,
					"class_id" => $o->class_id(),
					"createdby" => $o->createdby(),
					"created" => $o->created(),
					"comment" => $o->comment(),
					"modifiedby" => $o->modifiedby(),
					"modified" => $o->modified(),
					"jrk" => $o->ord(),
				);
			}
		}
		else
		{
			$cur_level_folders = $this->_get_level_folders_from_fld($fld, $sel, true);
		}

		classload("core/icons");
		$clss = aw_ini_get("classes");
		get_instance(CL_FILE);

		foreach($cur_level_folders as $entry)
		{
			if (is_oid($entry["id"]))
			{
				$o = obj($entry["id"]);
			}

			$pm = get_instance("vcl/popup_menu");
			$pm->begin_menu("sf".$entry["id"]);

			if ($fld->class_id() == CL_SERVER_FOLDER)
			{
				$pm->add_item(array(
					"text" => t("Ava internetist"),
					"link" => $entry["url"]
				));
				$pm->add_item(array(
					"text" => t("Ava serverist"),
					"link" => $entry["server_url"]
				));
				$pm->add_item(array(
					"text" => t("Laadi uus versioon"),
					"link" => $entry["change_url"]
				));
			}
			else
			if ($entry["type"] == "file")
			{
				$pm->add_item(array(
					"text" => $entry["name"],
					"link" => $entry["url"],
					"target" => 1
				));
			}
			else
			if ($fld->class_id() == CL_MENU)
			{
				foreach($o->connections_from(array("type" => "RELTYPE_FILE")) as $c)
				{
					$pm->add_item(array(
						"text" => $c->prop("to.name"),
						"link" => file::get_url($c->prop("to"), $c->prop("to.name")),
						"target" => 1
					));
				}
			}

			if ($entry["type"] != "file")
			{
				$icon = html::href(array(
					"caption" => "<img border=0 src='".icons::get_icon_url(CL_MENU)."'>" ,
					"url" => aw_url_change_var("tf" , $fld->id()."|".$entry["id"])
				));
			}
			else
			{
				$icon = $pm->get_menu(array(
					"icon" => icons::get_icon_url($entry["class_id"] ? $entry["class_id"] : CL_FILE)
				));
			}
			if (!isset($i_docs_table_level))
			{
				$i_docs_table_level = $this->get_element_level_in_docs_table($arr, $entry["id"]);
			}
			$t->define_data(array(
				"icon" =>
					$o->class_id() == CL_MENU
						?
					html::href(array(
						"caption" => "<img alt=\"\" border=0 src='".icons::get_icon_url($o->class_id())."'>" ,
						"url" => aw_url_change_var("tf" , $fld->id()."|".$o->id()),
						"onclick"=>"toggle_children(document.getElementById(\"".$i_root."|".$o->id()."treenode\"),".$i_docs_table_level.");"
					))
						:
					$pm->get_menu(array("icon" => icons::get_icon_url($o))),
				"name" => html::get_change_url($o->id(), array("return_url" => get_ru()), $entry["name"]),
				"class_id" => $clss[$entry["class_id"]]["name"],
				"createdby" => $entry["createdby"],
				"created" => $entry["created"],
				"comment" => $entry["comment"],
				"modifiedby" => $entry["modifiedby"],
				"modified" => $entry["modified"],
				"oid" => $entry["id"],
                                "oname" => $entry["name"],
                                "jrk" => $entry["jrk"],
				"is_menu" => $entry["type"] != "file" ? 0 : 1
			));
		}

		if(!$arr["request"]["tf"] || $sel == $fld->id())
		{
			$person = $arr["request"]["id"];
			$c = new connection();
			$results = $c->find(array(
				"from.class_id" => CL_CRM_OFFER,
				"to" => $person,
				"type" => RELTYPE_ORDERER
			));
			foreach($results as $result)
			{
				$o2 = obj($result['from']);
				$pm = get_instance("vcl/popup_menu");
				$pm->begin_menu("sf".$o2->id());
				foreach($o2->connections_from(array("class" => CL_FILE)) as $c)
				{
					$pm->add_item(array(
						"text" => $c->prop("to.name"),
						"link" => file::get_url($c->prop("to"), $c->prop("to.name")),
						"target" => 1
					));
				}
				$t->define_data(array(
					"icon" => $o2->class_id() == CL_MENU ? 1 : $pm->get_menu(array(
						"icon" => icons::get_icon_url($o2)
					)),
					"name" => html::obj_change_url($o2),
					"class_id" => $clss[$o2->class_id()]["name"],
					"createdby" => $o2->createdby(),
					"created" => $o2->created(),
					"modifiedby" => $o2->modifiedby(),
					"modified" => $o2->modified(),
					"oid" => $o2->id(),
					"oname" => $o2->name(),
					"jrk" => $o2->ord(),
					"is_menu" => $o2->class_id() == CL_MENU ? 0 : 1
					));
			}
		}
		/*$t->data_from_ol($ol, array(
			"change_col" => "name"
		));*/
//arr($t);
		$t->set_numeric_field("jrk");
		$t->set_default_sortby(array("is_menu","jrk","oname"));
		$t->set_default_sorder("asc");
	}

	function _get_docs_s_type($arr)
	{
		if (!$arr["request"]["do_doc_search"])
		{
			return PROP_IGNORE;
		}
		$arr["prop"]["options"] = array("" => "") + $this->adds;
		$arr["prop"]["value"] = $arr["request"]["docs_s_type"];
	}

	function _get_docs_s_created_after($arr)
	{
		$arr["prop"]["value"] = $arr["request"]["docs_s_created_after"];
	}

	function _get_doc_search_f($req, $parent)
	{
		$res = array(
			"parent" => $parent,
			"class_id" => array_keys($this->adds)
		);

		$has = false;
		if ($req["docs_s_name"] != "")
		{
			$res["name"] = "%".$req["docs_s_name"]."%";
			$has = true;
		}

		if ($req["docs_s_type"] != "")
		{
			$res["class_id"] = $req["docs_s_type"];
			$has = true;
		}

		if ($req["docs_s_created_after"] != "")
		{
			$res["created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, (int) $req["docs_s_created_after"]);
			$has = true;
		}

		if ($req["docs_s_comment"] != "")
		{
			$res["comment"] = "%".$req["docs_s_comment"]."%";
			$has = true;
		}


		if ($req["docs_s_task"] != "")
		{
			$res[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_MEMO.task.name" => "%".$req["docs_s_task"]."%",
					"CL_CRM_DOCUMENT.task.name" => "%".$req["docs_s_task"]."%",
					"CL_CRM_DEAL.task.name" => "%".$req["docs_s_task"]."%",
					"CL_CRM_MEMO.task.content" => "%".$req["docs_s_task"]."%",
					"CL_CRM_DOCUMENT.task.content" => "%".$req["docs_s_task"]."%",
					"CL_CRM_DEAL.task.content" => "%".$req["docs_s_task"]."%",
				)
			));
			$has = true;
		}

		if ($req["docs_s_user"] != "")
		{
			// get all persons whose names match
			$pers = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				"name" => "%".$req["docs_s_user"]."%"
			));
			// get all users for those
			$c = new connection();
			$user_conns = $c->find(array(
				"from.class_id" => CL_USER,
				"type" => "RELTYPE_PERSON",
				"to" => $pers->ids()
			));
			$uids = array();
			foreach($user_conns as $c)
			{
				$u = obj($c["from"]);
				$uids[] = $u->prop("uid");
			}
			// filter by createdby or modifiedby by those users
			$res[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"createdby" => $uids,
					"modifiedby" => $uids
				)
			));
			$has = true;
		}

		if ($req["docs_s_customer"] != "")
		{
			$res[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_MEMO.customer.name" => "%".$req["docs_s_customer"]."%",
					"CL_CRM_DOCUMENT.customer.name" => "%".$req["docs_s_customer"]."%",
					"CL_CRM_DEAL.customer.name" => "%".$req["docs_s_customer"]."%"
				)
			));
			$has = true;
		}

		if (!$has)
		{
			$res["oid"] = -1;
		}
		return $res;
	}

	function _get_docs_news_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$fldo = $this->_init_content_docs_fld($arr["obj_inst"]);

		$tb->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Lisa dokument'),
			'url' => html::get_new_url(CL_DOCUMENT, $fldo->id(), array("return_url" => get_ru())),
		));

	}

	function _init_dn_res_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "lead",
			"caption" => t("Lead"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"sortable" => 1,
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"sortable" => 1,
			"align" => "center",
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"sortable" => 1,
			"align" => "center",
		));
	}

	function _get_dn_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_dn_res_t($t);

		$format = t("%s siseuudised");
		$t->set_caption(sprintf($format, $arr['obj_inst']->name()));

		$ol = $this->_get_news($this->_init_content_docs_fld($arr["obj_inst"]), $arr["request"]);
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::href(array(
					"url" => obj_link($o->id()),//$this->mk_my_orb("view", array("id" => $o->id(), "return_url" => get_ru()), CL_DOCUMENT),
					"caption" => parse_obj_name($o->name())
				)),
				"lead" => nl2br($o->prop("lead")),
				"createdby" => $o->createdby(),
				"created" => $o->created(),
				"modifiedby" => $o->modifiedby(),
				"modified" => $o->modified(),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $o->id(), "return_url" => get_ru()), CL_DOCUMENT),
					"caption" => t("Muuda")
				))
			));
		}
	}

	function _get_news($parent, $r)
	{
		if ($r["dn_s_sbt"] == "")
		{
			$ol = new object_list(array(
				"class_id" => CL_DOCUMENT,
				"created" => new obj_predicate_compare(OBJ_COMP_GREATER, time()- (7*24*3600)),
				"parent" => $parent->id()
			));
		}
		else
		{
			$filt = array(
				"class_id" => CL_DOCUMENT,
				"parent" => $parent->id()
			);

			if ($r["dn_s_name"] != "")
			{
				$filt["name"] = "%".$r["dn_s_name"]."%";
			}

			if ($r["dn_s_lead"] != "")
			{
				$filt["lead"] = "%".$r["dn_s_lead"]."%";
			}

			if ($r["dn_s_content"] != "")
			{
				$filt["content"] = "%".$r["dn_s_content"]."%";
			}

			$ol = new object_list($filt);
		}
		return $ol;
	}

	function _init_docs_lmod_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "files",
			"caption" => t("Failid"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "parent",
			"caption" => t("Asukoht"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muutmisaeg"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
	}

	function _get_documents_lmod($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_docs_lmod_t($t);

		list($fld_id, $sel) = explode("|", $arr["request"]["tf"]);

		if (!is_oid($fld_id))
		{
			if (false === $fld = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_DOCS_FOLDER"))
			{ // no docs folder found. create&connect-to new folder.
				$fld = $this->_init_docs_fld($arr["obj_inst"]);
			}
		}
		else
		{
			$fld = obj($fld_id);
		}


		$ot = new object_tree(array(
			"class_id" => CL_MENU,
			"parent" => $fld->id()
		));
		$ol = $ot->to_list();
		$ol->add($fld);

		// search for 30 last mod docs
		$lm = new object_list(array(
			"parent" => $ol->ids(),
			"sort_by" => "objects.modified desc",
			"limit" => 30,
			"class_id" => array(CL_FILE,CL_CRM_MEMO,CL_CRM_DEAL,CL_CRM_DOCUMENT,CL_CRM_OFFER, CL_FILE)
		));
		//$t->data_from_ol($lm);
		$u = get_instance(CL_USER);
		$us = get_instance("users");
		foreach($lm->arr() as $o)
		{
			$p = obj($u->get_person_for_user(obj($us->get_oid_for_uid($o->modifiedby()))));
			$fs = new object_list($o->connections_from(array("type" => "RELTYPE_FILE")));
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"files" => html::obj_change_url($fs->ids()),
				"parent" => $o->path_str(array("path_only" => true, "max_len" => 2)),
				"modifiedby" => $p->name(),
				"modified" => $o->modified()
			));
		}
		$t->set_default_sortby("modified");
		$t->set_default_sorder("desc");
	}

	function _get_document_source_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_DOCS_FOLDER"))),
			array("name", "class_id", "created"),
			CL_MENU
		);
	}

	function _get_document_source_toolbar($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(
			array(CL_MENU,CL_SERVER_FOLDER,CL_FTP_LOGIN),
			$arr["obj_inst"]->id(),
			40 /* RELTYPE_DOCS_FOLDER */
		);
		$tb->add_search_button(array(
			"pn" => "search_tbl",
			"multiple" => 1,
			"clid" => array(CL_MENU,CL_SERVER_FOLDER,CL_FTP_LOGIN)
		));
		$tb->add_delete_rels_button();
	}

	function _render_folder_in_tree($arr, $fld)
	{
		$arr["prop"]["vcl_inst"]->add_item(0,array(
			"id" => $fld->id(),
			"name" => $fld->name(),
			"iconurl" => icons::get_icon_url($fld->class_id()),
			"url" => aw_url_change_var("tf", $fld->id() . "|" . $fld->id()),
		));
		$arr["prop"]["vcl_inst"]->add_item($fld->id(),array(
			"id" => "nokupan" . $fld->id() . "-" . $fld->id(),
			"name" => "dummy",
			"iconurl" => icons::get_icon_url($fld->class_id()),
			"url" => aw_url_change_var("tf", $fld->id() . "|" . $fld->id()),
		));
	}
}
