<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/object_treeview.aw,v 1.53 2008/01/31 13:52:14 kristo Exp $

/*

@classinfo syslog_type=ST_OBJECT_ relationmgr=yes maintainer=kristo

@groupinfo folders caption=Kaustad
@groupinfo clids caption=Objektit&uuml;&uuml;bid
@groupinfo styles caption=Stiilid
@groupinfo columns caption=Tulbad

@default table=objects
@default group=general

@property status type=status field=status
@caption Staatus

@default field=meta
@default method=serialize

@property folders type=text store=no group=folders callback=callback_get_menus
@caption Kataloogid

@property show_folders type=checkbox ch_value=1 
@caption N&auml;ita katalooge

@property show_add type=checkbox ch_value=1 
@caption N&auml;ita toolbari

@property tree_type type=chooser default=1
@caption Puu n&auml;itamise meetod

@property groupfolder_acl type=checkbox ch_value=1 
@caption &Otilde;igused piiratud grupi kataloogide j&auml;rgi

@property show_notact type=checkbox ch_value=1 
@caption N&auml;ita mitteaktiivseid objekte

@property sort_by type=select 
@caption Objekte sorteeritakse

@property tree_on_left type=checkbox ch_value=1 
@caption Puu vasakul

@property clids type=callback callback=get_clids group=clids store=no
@caption Klassid

@default group=styles
@property style_donor type=relpicker reltype=RELTYPE_STYLE_DONOR 
@caption Stiilide doonor

@property title_bgcolor type=colorpicker 
@caption Pealkirja taustav&auml;rv

@property even_bgcolor type=colorpicker 
@caption Paaris rea taustav&auml;rv

@property odd_bgcolor type=colorpicker 
@caption Paaritu rea taustav&auml;rv

@property header_css type=relpicker reltype=RELTYPE_CSS 
@caption Pealkirja stiil

@property line_css type=relpicker reltype=RELTYPE_CSS 
@caption a stiil

@default group=columns
@property columns type=callback callback=callback_get_columns 
@caption Tulbad

@reltype FOLDER value=1 clid=CL_MENU,CL_SERVER_FOLDER
@caption kataloog

@reltype ADD_TYPE value=2 clid=CL_OBJECT_TYPE
@caption lisatav objektit&uuml;&uuml;p

@reltype ALL_ACSS_GRP value=3 clid=CL_GROUP
@caption projekti haldaja grupp

@reltype CSS value=4 clid=CL_CSS
@caption css stiil

@reltype STYLE_DONOR value=5 clid=CL_OBJECT_VIEW
@caption stiilide doonor

*/


class object_treeview extends class_base
{
	function object_treeview()
	{
		$this->all_cols = array(
			"icon" => t("Ikoon"),
			"name" => t("Nimi"),
			"size" => t("Suurus"),
			"class_id" => t("T&uuml;&uuml;p"),
			"modified" => t("Muutmise kuup&auml;ev"),
			"modifiedby" => t("Muutja"),
			"change" => t("Muuda"),
			"select" => t("Vali")
		);

		$this->init(array(
			'tpldir' => 'contentmgmt/object_tree',
			'clid' => CL_OBJECT_TREE
		));
		$this->sub_merge = 1;
	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($args)
	{
		$this->reset();
		$this->_init_vars();
		$this->first_folder = NULL;
		return $this->show(array('id' => $args['alias']['target']));
	}

	/**  
		
		@attrib name=show params=name default="0"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function show($arr)
	{
		extract($arr);
		if (!is_oid($id))
		{
			return "";
		}
		$ob = obj($id);

		if ($ob->prop("tree_on_left"))
		{
			$this->read_template('show_left.tpl');
		}
		else
		{
			$this->read_template('show.tpl');
		}

		$this->_insert_row_styles($ob);

		// returns an array of object id's that are folders that are in the object
		$fld = $this->_get_folders($ob);

		// get all objects to show
		$ol = $this->_get_objects($ob, $fld);

		// make folders
		$this->vars(array(
			"FOLDERS" => $this->_draw_folders($ob, $ol, $fld)
		));

		// get all related object types
		// and their cfgforms
		// and make a nice little lut from them.
		$class2cfgform = array();
		foreach($ob->connections_from(array("type" => "RELTYPE_ADD_TYPE")) as $c)
		{
			$addtype = $c->to();
			if ($addtype->prop("use_cfgform"))
			{
				$class2cfgform[$addtype->prop("type")] = $addtype->prop("use_cfgform");
			}
		}

		$this->cnt = 0;
		$c = "";
		$sel_cols = $ob->meta("sel_columns");

		$classlist = aw_ini_get("classes");
		foreach($ol as $oid)
		{
			$od = obj($oid);
			$target = "";
			
			if ($od->class_id() == CL_EXTLINK)
			{
				$li = get_instance("contentmgmt/links_display");
				list($url,$target,$caption) = $li->draw_link($oid);
			}
			else
			if ($od->class_id() == CL_FILE)
			{
				$fi = get_instance(CL_FILE);
				$fd = $fi->get_file_by_id($oid);
				$url = $fi->get_url($oid,$fd["name"]);
				if ($fd["newwindow"])
				{
					$target = "target=\"_blank\"";
				}
				$fileSizeBytes = number_format(@filesize($od->prop('file')),2);
				$fileSizeKBytes = number_format(@filesize($od->prop('file'))/(1024),2);
				$fileSizeMBytes = number_format(@filesize($od->prop('file'))/(1024*1024),2);
			}
			else
			if ($od->class_id() == CL_MENU)
			{
				$url = $this->mk_my_orb("show", array(
					"section" => $od->id(),
					"id" => $ob->id(),
					"tv_sel" => $od->id()
				));
			}
			else
			if ($od->class_id() == CL_SERVER_FOLDER)
			{
				$sf = get_instance(CL_SERVER_FOLDER);
				$fl = $sf->get_contents($od);
				$section = aw_global_get("section");

				foreach($fl as $_file)
				{
					$fid = $od->id().":".$_file;

					$fqfn = $od->prop("folder")."/".$_file;
					$fileSizeBytes = number_format(@filesize($fqfn),0);
					$fileSizeKBytes = number_format(@filesize($fqfn)/(1024),2);
					$fileSizeMBytes = number_format(@filesize($fqfn)/(1024*1024),2);
				
					$fowner = posix_getpwuid(fileowner($fqfn));

					$act = "";
					if (is_writable($fqfn))
					{
						$act = html::href(array(
							"url" => $this->mk_my_orb("change_file", array(
								"fid" => $fid,
								"section" => $section, 
							), "server_folder"),
							"caption" => html::img(array(
								"border" => 0,
								"url" =>  aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif"
							))
						));
					}
					$c .= $this->_do_parse_file_line(array(
						"name" => $_file,
						"oid" => $fid,
						"url" => $this->mk_my_orb("show_file", array("fid" => $fid, "section" => $section), "server_folder"),
						"target" => $target,
						"fileSizeBytes" => $fileSizeBytes,
						"fileSizeKBytes" => $fileSizeKBytes,
						"fileSizeMBytes" => $fileSizeMBytes,
						"comment" => "",
						"type" => $classlist[CL_FI]["name"],
						"add_date" => $this->time2date(filemtime($fqfn), 2),
						"mod_date" => $this->time2date(filemtime($fqfn), 2),
						"adder" => $fowner["name"],
						"modder" => $fowner["name"],
						"icon" => image::make_img_tag(icons::get_icon_url(CL_FI, $_file)),
						"bgcolor" => $this->_get_bgcolor($ob, $this->cnt),
						"acl_obj" => $od,
						"tree_obj" => $ob,
						"sel_cols" => $sel_cols,
						"act" => $act
					));
				}
				continue;
			}
			else
			{
				$url = $this->cfg["baseurl"]."/".$oid;
			}
			classload("core/icons", "image");
			$act = "";
			if ($this->can("edit", $od->id()))
			{
				$fl = $classlist[$od->class_id()]["file"];
				if ($fl == "document")
				{
					$fl = "doc";
				}
				$act .= html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $od->id(), 
						"section" => $od->parent(),
						"cfgform" => $class2cfgform[$od->class_id()]
					), $fl),
					"caption" => html::img(array(
						"border" => 0,
						"url" =>  aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif"
					))
				));
			}
			if ($this->can("delete", $od->id()))
			{
				$delete = html::href(array(
					"url" => $this->mk_my_orb("delete", array("id" => $od->id(), "return_url" => get_ru())),
					"caption" => html::img(array(
						"border" => 0,
						"url" =>  aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif"
					))
				));
			}

			$c .= $this->_do_parse_file_line(array(
				"name" => parse_obj_name($od->name()),
				"oid" => $od->id(),
				"url" => $url,
				"target" => $target,
				"fileSizeBytes" => $fileSizeBytes,
				"fileSizeKBytes" => $fileSizeKBytes,
				"fileSizeMBytes" => $fileSizeMBytes,
				"comment" => $od->comment(),
				"type" => $classlist[$od->class_id()]["name"],
				"add_date" => $this->time2date($od->created(), 2),
				"mod_date" => $this->time2date($od->modified(), 2),
				"adder" => $od->createdby(),
				"modder" => $od->modifiedby(),
				"icon" => image::make_img_tag(icons::get_icon_url($od->class_id(), $od->name())),
				"bgcolor" => $this->_get_bgcolor($ob, $this->cnt),
				"acl_obj" => $od,
				"tree_obj" => $ob,
				"sel_cols" => $sel_cols,
				"act" => $act
			));
		}

		$tb = "";
		$no_tb = "";
		if ($ob->prop("show_add"))
		{
			$tb = $this->parse("HEADER_HAS_TOOLBAR");
		}
		else
		{
			$no_tb = $this->parse("HEADER_NO_TOOLBAR");
		}
		$this->vars(array(
			"FI" => $c,
			"HEADER_HAS_TOOLBAR" => $tb,
			"HEADER_NO_TOOLBAR" => $no_tb,
			"reforb" => $this->mk_reforb("submit_show", array(
				"return_url" => aw_global_get("REQUEST_URI"),
				"subact" => "0"
			))
		));

		// columns
		foreach($this->all_cols as $colid => $coln)
		{
			$str = "";
			if ($sel_cols[$colid] == 1)
			{
				$str = $this->parse("HEADER_".$colid);
			}
			$this->vars(array(
				"HEADER_".$colid => $str
			));
		}

		$res = $this->parse();
		if ($ob->prop("show_add"))
		{
			$res = $this->_get_add_toolbar($ob).$res;
		}
		return $res;
	}

	/**  
		
		@attrib name=submit_show params=name 
		
		
		@returns
		
		
		@comment

	**/
	function submit_show($arr)
	{
		extract($arr);

		if ($subact == "delete")
		{
			$tt = array();
			$awa = new aw_array($sel);
			foreach($awa->get() as $oid)
			{
				list($_oid, $_fn) = explode(":", $oid);
				if (is_oid($_oid) && $_fn != "")
				{
					$sf = get_instance(CL_SERVER_FOLDER);
					$sf->del_file($oid);
				}
				else
				if ($this->can("view", $oid))
				{
					$o = obj($oid);
					$o->delete();
				}
			}
		}

		return $return_url;
	}

	function _get_objects($ob, $folders)
	{
		$ret = array();

		// if the folder is specified in the url, then show that
		if ($GLOBALS["tv_sel"])
		{
			$parent = $GLOBALS["tv_sel"];
		}
		else
		// right. if the user has said, that no tree should be shown
		// then get files in all selected folders
		if (!$ob->meta('show_folders') && $_GET["tv_sel"])
		{
			$parent = $folders;
		}

		if (!is_oid($ob->id()))
		{
			return;
		}

		if (!$parent)
		{
			// if parent can't be found. then get the objects from all the root folders
			$con = $ob->connections_from(array(
				"type" => "RELTYPE_FOLDER"
			));

			$ignoreself = $ob->meta("ignoreself");

			$parent = array();
			foreach($con as $c)
			{
				// but only those that are to be ignored!
				if ($ignoreself[$c->prop("to")])
				{
					$parent[$c->prop("to")] = $c->prop("to");
				}
			}
		}

		if (!is_array($ob->meta('clids')) || count($ob->meta('clids')) < 1)
		{
			return array();
		}

		$awa = new aw_array($parent);
		if (count($awa->get()) < 1)
		{
			$parent = $this->first_folder;
		}

		$sby = "objects.modified DESC";
		if ($ob->prop("sort_by") != "")
		{
			$sby = $ob->prop("sort_by");
		}

		$ol = new object_list(array(
			"parent" => $parent,
			"status" => $ob->prop("show_notact") ? array(STAT_ACTIVE, STAT_NOTACTIVE) : STAT_ACTIVE,
			"class_id" => $ob->meta('clids'),
			"sort_by" => $sby,
			"lang_id" => array()
		));
		aw_global_set("ot_sort_by", $ob->prop("sort_by"));
		$ol->sort_by_cb(array(&$this, "_obj_list_sorter"));
		if ($ob->prop("groupfolder_acl"))
		{
			$r_ol = new object_list();
			// if groupfolder acl, remove all folders that are not in folder list
			for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				if ($o->class_id() == CL_MENU)
				{
					if ($folders[$o->id()])
					{
						$r_ol->add($o);
					}
				}
				else
				{
					$r_ol->add($o);
				}
			}
			$ol = $r_ol;
		}

		$awa = new aw_array($parent);
		foreach($awa->get() as $p_id)
		{
			$p_o = obj($p_id);
			if ($p_o->class_id() == CL_SERVER_FOLDER)
			{
				$ol->add($p_o);
			}
		}

		return $this->make_keys($ol->ids());
	}

	function _obj_list_sorter($a, $b)
	{
		$sort_by = aw_global_get("ot_sort_by");
		if ($a->class_id() == CL_MENU && $b->class_id() != CL_MENU)
		{
			return -1;
		}
		else
		if ($a->class_id() != CL_MENU && $b->class_id() == CL_MENU)
		{
			return 1;
		}
		else
		if ($a->class_id() != CL_MENU && $b->class_id() != CL_MENU)
		{
			if(strpos($sort_by, "jrk") !== false)
			{
				return $a->ord() > $b->ord();
			}
			return $a->modified() < $b->modified();
		}
		else
		if ($a->class_id() == CL_MENU && $b->class_id() == CL_MENU)
		{
			return $a->modified() < $b->modified();
		}
	}

	function _get_folders($ob)
	{
		// go over all related menus and add subtree id's together if the user has so said. 
		$ret = array();
		
		$sub = $ob->meta("include_submenus");
   		$igns = $ob->meta("ignoreself");

		$glo = aw_global_get("gidlist_oid");

		// check if the user is an admin
		if (!$ob->prop("groupfolder_acl"))
		{
			$is_admin = true;
		}
		else
		{
			$is_admin = false;
		}

		if (!is_oid($ob->id()))
		{
			return;
		}
		
		$adm_c = $ob->connections_from(array(
			"type" => "RELTYPE_ALL_ACSS_GRP"
		));
		foreach($adm_c as $adm_conn)
		{
			$adm_g = $adm_conn->prop("to");
			if (isset($glo[$adm_g]))
			{
				$is_admin = true;
			}
		}

		// this used to give access to subfolders of given folders
		$access_by_parent = array();

		$conns = $ob->connections_from(array(
			"type" => "RELTYPE_FOLDER"
		));
		foreach($conns as $conn)
		{
			$c_o = $conn->to();
			if (!isset($this->first_folder))
			{
				$this->first_folder = $c_o->id();
			}
			
			$cur_ids = array();

			if ($sub[$c_o->id()])
			{
				$_ot = new object_tree(array(
					"class_id" => CL_MENU,
					"parent" => $c_o->id(),
					"status" => array(STAT_ACTIVE,STAT_NOTACTIVE),
					"lang_id" => array()
				));
				$cur_ids = $_ot->ids();
			}

			if (!$igns[$c_o->id()])
			{
				$cur_ids[] = $c_o->id();
			}

			foreach($cur_ids as $c_id)
			{
				$add = $is_admin;
				if (!$is_admin)
				{
					$c_id_o = obj($c_id);
					$c_id_gr = $c_id_o->connections_from(array(
						"type" => "RELTYPE_ACL_GROUP"
					));
					foreach($c_id_gr as $c_id_gr_c)
					{
						if (isset($glo[$c_id_gr_c->prop("to")]))
						{
							$add = true;
							break;
						}
					}
					if ($access_by_parent[$c_id_o->parent()])
					{
						$add = true;
					}
				}

				if ($add)
				{
					$ret[$c_id] = $c_id;
					$access_by_parent[$c_id] = true;
				}
			}
		}
		return $ret;
	}

	function _draw_folders($ob, $ol, $folders)
	{
		if (!$ob->meta('show_folders'))
		{
			return;
		}

		classload("core/icons");
		// use treeview widget
		$tv = get_instance("vcl/treeview");
		$tv->start_tree(array(
			"root_name" => "",
			"root_url" => "",
			"root_icon" => "",
			"tree_id" => "objtr" . $ob->id(),
			"type" => _DHTML, //$ob->meta('tree_type'),
			"persist_state" => 1
		));

		// now, insert all folders defined
		foreach($folders as $fld)
		{
			$i_o = obj($fld);
			$parent = 0;
			if (in_array($i_o->parent(),$folders))
			{
				$parent = $i_o->parent();
			}

			// find modification time
			$tm = $i_o->modified();
			foreach($ol as $o_oid)
			{
				$o_o = obj($o_oid);

				if ($o_o->parent() == $fld && $o_o->modified() > $tm)
				{
					$tm = $o_o->modified();
				}
			}

			$tv->add_item($parent, array(
				"id" => $fld,
				"name" => $i_o->name(),
				"url" => aw_url_change_var("tv_sel", $fld),
				"icon" => icons::get_icon_url($i_o->class_id(), ""),
				"comment" => $i_o->comment(),
				"data" => array(
					"changed" => $this->time2date($tm, 2)
				)
			));
		}
		$tv->set_selected_item($GLOBALS["tv_sel"]);

		$pms = array();
		// here's the trick. if the treeview is set to show_as_treeview for any section and we got here via an orb action in the url
		// then show the tree from the current section
		// 
		// hehe, heuristics rule ;)
		$t_c = $ob->connections_to(array(
			"type" => 8,	// RELTYPE_OBJ_ from menu
			"from.class_id" => CL_MENU
		));
		
		if (isset($GLOBALS["class"]) && count($t_c) > 0)
		{
			$pms["rootnode"] = aw_global_get("section");
		}
		
		$tmp = $tv->finalize_tree($pms);
		return $tmp;
	}

	function get_clids($arr)
	{
		$clids = $arr["obj_inst"]->meta("clids");

		$ret = array();
		$a = get_class_picker();
		foreach($a as $clid => $clname)
		{
			$rt = "clid_".$clid;
			$ret[$rt] = array(
				'name' => $rt,
				'caption' => $clname,
				'type' => 'checkbox',
				'ch_value' => 1,
				'store' => 'no',
				'group' => 'clids',
				'value' => ($clids[$clid] == $clid)
			);
		}
		return $ret;
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];
		if ($prop['name'] == 'clids')
		{
			$_clids = array();
			$a = get_class_picker();
			foreach($a as $clid => $clname)
			{
				$rt = "clid_".$clid;
				if (isset($arr["request"][$rt]) && $arr["request"][$rt] == 1)
				{
					$_clids[$clid] = $clid;
				}
			}
			$arr["obj_inst"]->set_meta("clids", $_clids);
		}

		if ($prop["name"] == "columns")
		{
			$arr["obj_inst"]->set_meta("sel_columns", $arr["request"]["column"]);
		}

		if ($prop["name"] == "folders")
		{
			$arr['obj_inst']->set_meta("include_submenus",$arr["request"]["include_submenus"]);
			$arr['obj_inst']->set_meta("ignoreself",$arr["request"]["ignoreself"]);
		};

		return PROP_OK;
	}

	function callback_get_menus($args = array())
	{
		$prop = $args["prop"];
		$nodes = array();

		// now I have to go through the process of setting up a generic table once again
		load_vcl("table");
		$this->t = new aw_table(array(
			"prefix" => "ot_menus",
			"layout" => "generic"
		));
		$this->t->define_field(array(
			"name" => "oid",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30",
		));
		$this->t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
		));
		$this->t->define_field(array(
			"name" => "check",
			"caption" => t("k.a. alammenüüd"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));
		$this->t->define_field(array(
			"name" => "ignoreself",
			"caption" => t("&auml;ra n&auml;ita peamen&uuml;&uuml;d"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));

		$include_submenus = $args["obj_inst"]->meta("include_submenus");
		$ignoreself = $args["obj_inst"]->meta("ignoreself");


		$conns = $args["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_FOLDER"
		));

		foreach($conns as $conn)
		{
			$c_o = $conn->to();

			$chk = "";
			if ($c_o->class_id() == CL_MENU)
			{
				$chk = html::checkbox(array(
					"name" => "include_submenus[".$c_o->id()."]",
					"value" => $c_o->id(),
					"checked" => $include_submenus[$c_o->id()],
				));
			}

			$this->t->define_data(array(
				"oid" => $c_o->id(),
				"name" => $c_o->path_str(array(
					"max_len" => 3
				)),
				"check" => $chk,
				"ignoreself" => html::checkbox(array(
					"name" => "ignoreself[".$c_o->id()."]",
					"value" => $c_o->id(),
					"checked" => $ignoreself[$c_o->id()],
				)),
			));
		};
		
		$nodes[$prop["name"]] = array(
			"type" => "text",
			"caption" => $prop["caption"],
			"value" => $this->t->draw(),
		);
		return $nodes;
	}

	function _get_add_toolbar($ob)
	{
		$types_c = $ob->connections_from(array(
			"type" => "RELTYPE_ADD_TYPE"
		));

		$menu = "";
		$classes = aw_ini_get("classes");

		$parent = $GLOBALS["tv_sel"] ? $GLOBALS["tv_sel"] : $this->first_folder;

		$p_o = obj($parent);
		$tb = get_instance("vcl/toolbar");
		$tb->add_menu_button(array(
			"name" => "add",
			"tooltip" => t("Uus"),
		));
		if ($p_o->class_id() == CL_SERR_FOLDER)
		{
			$tb->add_menu_item(array(
				"parent" => "add",
				"url" => $this->mk_my_orb("add_file", array("id" => $p_o->id(), "section" => aw_global_get("section")), "server_folder"),
				"text" => $classes[CL_FI]["name"]
			));
		}
		else
		{
			$ot = get_instance(CL_OBJECT_TYPE);
			foreach($types_c as $c)
			{
				$c_o = $c->to();

				$tb->add_menu_item(array(
					"parent" => "add",
					"url" => $ot->get_add_url(array("id" => $c_o, "parent" => $parent, "section" => $parent)),
					"text" => $c_o->prop("name")
				));
			}
		}


		$tb->add_button(array(
			"name" => "del",
			"tooltip" => t("Kustuta"),
			"url" => "#",
			"onClick" => "if (confirm('".t("Oled kindel et soovid objekte kustutada?")."')) { document.objlist.subact.value='delete';document.objlist.submit(); }",
			"img" => "delete.gif",
			"class" => "menuButton",
		));
		return $tb->get_toolbar();
	}

	/**  
		
		@attrib name=delete params=name default="0"
		
		@param id required
		@param return_url required
		
		@returns
		
		
		@comment

	**/
	function obj_delete($arr)
	{
		error::raise_if(!$arr["id"], array(
			"id" => ERR_PARAM,
			"msg" => t("object_treeview::obj_delete(): no object id specified!")
		));

		$o = obj($arr["id"]);
		$o->delete();
	
		return $arr["return_url"];
	}

	function get_folders_as_object_list($object)
	{
		$t_id = $object->prop("show_object_tree");
		$first_level = true;
		if (!$t_id)
		{
			$pa = $object->path();
			foreach($pa as $p_o)
			{
				$t_id = $p_o->prop("show_object_tree");
				if ($t_id)
				{
					break;
				}
			}
			$first_level = false;
		}

		if (!is_oid($t_id) || !$this->can("view", $t_id))
		{
			return new object_list();
		}

		$this->tree_ob = obj($t_id);
	
		$ol = new object_list();

		$folders = $this->_get_folders($this->tree_ob);
		foreach($folders as $fld)
		{
			$i_o = obj($fld);
			$parent = 0;
			if (in_array($i_o->parent(),$folders))
			{
				$parent = $i_o->parent();
			}
			
			if ($first_level)
			{
				if ($parent == 0)
				{
					$ol->add($fld);
				}
			}
			else
			{
				if ($parent == $object->id())
				{
					$ol->add($fld);
				}
			}
		}

		return $ol;
	}

	function make_menu_link($sect_obj)
	{
		$link = $this->mk_my_orb("show", array("id" => $this->tree_ob->id(), "tv_sel" => $sect_obj->id(), "section" => $sect_obj->id()));;
		return $link;
	}

	function get_yah_link($tree, $cur_menu)
	{
		return $this->mk_my_orb("show", array("id" => $tree, "tv_sel" => $cur_menu->id(), "section" => $cur_menu->id()));
	}

	function get_property($arr)
	{
		$prop =&$arr["prop"];
		if ($prop["name"] == "tree_type")
		{
			$prop["options"] = array(
				_HTML => "HTML",
				_JS => "Javascript",
				_DHTML => "DHTML"
			);
		}
		else
		if ($prop["name"] == "sort_by")
		{
			$prop["options"] = array(
				"objects.modified DESC" => t("Objekti muutmise kuup&auml;eva j&auml;rgi"),
				"objects.jrk" => t("Objektide j&auml;rjekorra j&auml;rgi")
			);
		}
		return PROP_OK;
	}

	function _insert_row_styles($o)
	{
		if (is_oid($o->prop("style_donor")) && $this->can("view", $o->prop("style_donor")))
		{
			$o = obj($o->prop("style_donor"));
		}

		$style = "textmiddle";
		if ($o->prop("line_css"))
		{
			$style = "st".$o->prop("line_css");
			active_page_data::add_site_css_style($o->prop("line_css"));
		}

		$header_css = "textmiddle";
		if ($o->prop("header_css"))
		{
			$header_css = "st".$o->prop("header_css");
			active_page_data::add_site_css_style($o->prop("header_css"));
		}

		$header_bg = "#E0EFEF";
		if ($o->prop("title_bgcolor"))
		{
			$header_bg = "#".$o->prop("title_bgcolor");
		}

		$this->vars(array(
			"css_class" => $style,
			"header_css_class" => $header_css,
			"header_bgcolor" => $header_bg
		));
	}

	function _get_bgcolor($ob, $line)
	{
		if (is_oid($ob->prop("style_donor")) && $this->can("view", $ob->prop("style_donor")))
		{
			$ob = obj($ob->prop("style_donor"));
		}

		$ret = "";
		if (($line % 2) == 1)
		{
			$ret = $ob->prop("odd_bgcolor");
			if ($ret == "")
			{
				$ret = "#EFF7F7";
			}
		}
		else
		{
			$ret = $ob->prop("even_bgcolor");
			if ($ret == "")
			{
				$ret = "#FFFFFF";
			}
		}
		return $ret;
	}

	function callback_get_columns($arr)
	{
		$cols = $arr["obj_inst"]->meta("sel_columns");

		$ret = array();

		foreach($this->all_cols as $colid => $coln)
		{

			$rt = "column[".$colid."]";
			$ret[$rt] = array(
				'name' => $rt,
				'caption' => $coln,
				'type' => 'checkbox',
				'ch_value' => 1,
				'store' => 'no',
				'group' => 'columns',
				'value' => $cols[$colid]
			);
		}
		return $ret;
	}

	function _do_parse_file_line($arr)
	{
		extract($arr);
		$this->vars(array(
			"show" => $url,
			"name" => $name,
			"oid" => $oid,
			"target" => $target,
			"sizeBytes" => $fileSizeBytes,
			"sizeKBytes" => $fileSizeKBytes,
			"sizeMBytes" => $fileSizeMBytes,
			"comment" => $comment,
			"type" => $type,
			"add_date" => $add_date,
			"mod_date" => $mod_date,
			"adder" => $adder,
			"modder" => $modder,
			"icon" => $icon,
			"act" => $act,
			"delete" => $delete,
			"bgcolor" => $bgcolor,
			"size" => ($fileSizeMBytes > 1 ? $fileSizeMBytes."MB" : ($fileSizeKBytes > 1 ? $fileSizeKBytes."kb" : $fileSizeBytes."b"))
		));

		$del = "";
		if ($this->can("delete", $acl_obj->id()))
		{
			$del = $this->parse("DELETE");
		}
		$this->vars(array(
			"DELETE" => $del
		));

		$tb = "";
		$no_tb = "";
		if ($tree_obj->prop("show_add"))
		{
			$tb = $this->parse("HAS_TOOLBAR");
		}
		else
		{
			$no_tb = $this->parse("NO_TOOLBAR");
		}
		$this->vars(array(
			"HAS_TOOLBAR" => $tb,
			"NO_TOOLBAR" => $no_tb
		));

		// columns
		foreach($this->all_cols as $colid => $coln)
		{
			$str = "";
			if ($sel_cols[$colid] == 1)
			{
				$str = $this->parse("FILE_".$colid);
			}
			$this->vars(array(
				"FILE_".$colid => $str
			));
		}
		
		$this->cnt++;

		return $this->parse("FILE");
	}
}
?>
