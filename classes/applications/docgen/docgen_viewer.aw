<?php

/** aw code analyzer viewer
		displays the data that the docgen analyzer generates

	@author terryf <kristo@struktuur.ee>

**/

/*

@classinfo no_status=1 no_comment=1 relationmgr=yes syslog_type=ST_DOCGEN_VIEWER prop_cb=1
@default table=objects
@default group=general

@property foorum type=relpicker reltype=RELTYPE_FORUM field=meta method=serialize table=objects

@property view type=text store=no

@groupinfo more_options caption="Seaded"
@default group=more_options

@property refresh_text type=text store=no value=Uuenda
@caption Uuenda

@property refresh_properties type=submit store=no value=Uuenda
@caption Uuenda


@default group=class_overview

	@layout ver_split type=hbox width=30%:70%

		@layout tree type=vbox closeable=1 area_caption=Klasside&nbsp;puu parent=ver_split

			@property class_tree type=text parent=tree store=no no_caption=1

		@layout tbl type=hbox closeable=1 area_caption=Klassi&nbsp;info parent=ver_split

			@property class_inf type=text store=no no_caption=1 parent=tbl

@groupinfo class_overview caption="Klasside &uuml;levaade" submit=no
@groupinfo all_classes caption="K&otilde;ik klassid" submit=no
@groupinfo api_classes caption="API Klassid" submit=no
@groupinfo tutorials caption="Eraldi dokumentatsioon" submit=no
@groupinfo cb_tags caption="Classbase tagid" submit=no
@groupinfo forum caption="Foorum" submit=no

@reltype FORUM value=1 clid=CL_FORUM_V2
@caption foorum

*/
class docgen_viewer extends class_base
{
	function docgen_viewer()
	{
		$this->init(array(
			"tpldir" => "core/docgen",
			"clid" => CL_AW_DOCGEN_VIEWER
		));

		$this->cb_callbacks = array(
			"callback_on_load" => 1,
			"callback_pre_edit" => 1,
			"callback_get_add_txt" => 1,
			"callback_mod_layout" => 1,
			"callback_mod_reforb" => 1,
			"callback_generate_scripts" => 1,
			"callback_mod_retval" => 1,
			"callback_get_cfgform" => 1,
			"callback_gen_path" => 1,
			"callback_mod_tab" => 1,
			"get_property" => 1,
			"set_property" => 1,
			"callback_pre_save" => 1,
			"callback_post_save" => 1,
			"callback_get_cfgmanager" => 1,
			"callback_get_group_display" => 1,
			"callback_get_default_group" => 1
		);
	}

	function set_property($arr)
	{
		$prop = &$arr['prop'];

		switch($prop['name'])
		{
			case 'refresh_properties':
				$documenter = new aw_language_documenter;
				$documenter->parse_files(aw_ini_get('classdir'));
				$arr['obj_inst']->set_meta('properties_data',serialize($documenter));
				$arr['obj_inst']->save();
			break;
		}

		return PROP_OK;
	}

	function _get_class_tree($arr)
	{
		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgclsss",
			"persist_state" => true,
			"root_name" => t("Classes"),
			"url_target" => "list"
		));
		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgclsss",
			"persist_state" => true,
			"root_name" => t("Classes"),
		));

		/* 	lihtsalt nii infi m6ttes - kui keegi hakkab seda puud siin feikima samasuguseks nagu on rohelise nupu puu
			siis juhtub kaks asja:
				- see aptch reverditakse
				- ta j22b cvs commit accessist ilma

			kui tekib selline tahtmine, siis selleks tehke uus puu uude kohta.

			- terryf.
		*/

		$this->_req_mk_clf_tree($tv, aw_ini_get("classdir"));

		$arr["prop"]["value"] = $tv->finalize_tree(array(
			"rootnode" => aw_ini_get("classdir"),
		));

	}


	function _get_class_inf($arr)
	{
		$analyzer = new aw_code_analyzer();
		$data = $analyzer->analyze_file($arr["request"]["tf"]);

		foreach($data["classes"] as $class => $class_data)
		{
			if ($class != "")
			{
				$op .= $this->display_class($class_data, $file, array(
					"api_only" => $api_only,
					"defines" => $data["defines"]
				));
			}
		}
		$arr["prop"]["value"] = $op;
	}

	/**
		@attrib name=iniviewer
	**/
	function iniviewer($arr)
	{
		$this->read_template("classlist.tpl");

		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgini",
			"persist_state" => true,
			"root_name" => t("Ini file"),
			"url_target" => "list"
		));

		$ip = new docgen_ini_file_parser();
		$ks = $ip->get_tree_items();
		ksort($ks);
		foreach($ks as $id => $desc)
		{
			$tv->add_item(0, array(
				"id" => $id,
				"parent" => 0,
				"name" => $desc,
				"url" => $this->mk_my_orb("show_ini_setting", array("setting" => $id))
			));
		}

		$this->vars(array(
			"list" => $tv->finalize_tree(array(
				"rootnode" => 0,
			))
		));

		die($this->finish_with_style($this->parse()));
	}

	/**
		@attrib name=show_ini_setting
		@param setting required
	**/
	function show_ini_setting($arr)
	{
		$this->read_template("show_ini_setting.tpl");

		$ip = new docgen_ini_file_parser();
		$d = $ip->get_setting_data($arr["setting"]);

		$il = "";
		foreach($d as $setting => $info)
		{
			$this->vars(array(
				"setting" => $setting,
				"comment" => $info["comment"],
				"default_value" => $info["default_value"],
			));
			$il .= $this->parse("INI_LINE");
		}

		$this->vars(array(
			"parent" => $arr["setting"],
			"INI_LINE" => $il
		));

		die($this->finish_with_style($this->parse()));
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "view":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("frames", array("id" => $arr["obj_inst"]->id())),
					"caption" => t("Open DocGen")
				));
				break;

			case "class_tree":
				$this->_get_class_tree($arr);
				break;
		}
		return PROP_OK;
	}

	/**
		@attrib name=intro
	**/
	function intro($arr)
	{
		$this->read_template("intro.tpl");
		$fc = file(aw_ini_get("basedir")."/docs/tutorials/overview.txt");

		// parse out descriptions for classes
		foreach($fc as $line)
		{
			$line = trim($line);
			if (isset($line[0]) && $line[0] == "*")
			{
				if (isset($desc) && $desc != "")
				{
					$classes[$l_path][basename($l_class)] = $desc;
				}
				$desc = "";
				$l_class = trim(substr($line, 1, strlen($line)-2));
				$l_path = dirname($l_class);
			}
			else
			if ($line != "")
			{
				$desc .= $line."\n";
			}
		}

		ksort($classes);

		$fc = "";
		foreach($classes as $path_str => $path)
		{
			$fc .= "<div class='folder'>$path_str/</div><table border=0 width='100%' cellpadding=5 cellspacing=20>";
			foreach($path as $class => $desc)
			{
				$cl_url = $this->mk_my_orb("class_info", array("api_only" => 1, "file" => "/".$path_str."/".$class.".".aw_ini_get("ext")));
				$fc .= "<tr><td class='classdesc'><a href='$cl_url'><b>$class:</b></a><br>$desc</td></tr>";
			}
			$fc .="</table>";
		}

		$this->vars(array(
			"content" => nl2br($fc)
		));
		die($this->finish_with_style($this->parse()));
	}

	/**
		@attrib name=class_list params=name default="0"
	**/
	function class_list()
	{
		$this->read_template("classlist.tpl");

		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgclsss",
			"persist_state" => true,
			"root_name" => t("Classes"),
			"url_target" => "list"
		));

		/* 	lihtsalt nii infi m6ttes - kui keegi hakkab seda puud siin feikima samasuguseks nagu on rohelise nupu puu
			siis juhtub kaks asja:
				- see aptch reverditakse
				- ta j22b cvs commit accessist ilma

			kui tekib selline tahtmine, siis selleks tehke uus puu uude kohta.

			- terryf.
		*/

		// gather data about things in files
		$this->db_query("SELECT * from aw_da_classes");
		$classes = array();
		while ($row = $this->db_next())
		{
			$fp = aw_ini_get("basedir").$row["file"];
			$classes[$fp][] = $row;
		}


		$this->_req_mk_clf_tree($tv, aw_ini_get("classdir"), $classes);

		$this->vars(array(
			"list" => $tv->finalize_tree(array(
				"rootnode" => aw_ini_get("classdir"),
			))
		));

		die($this->finish_with_style($this->parse()));
	}

	function _req_mk_clf_tree($tv, $path, $classes)
	{
		$dc = array();
		$fc = array();
		$dh = opendir($path);
		while (($file = readdir($dh)) !== false)
		{
			$fp = $path."/".$file;
			if ($file != "." && $file != ".." && $file != "CVS" && substr($file, 0,2) != ".#" && substr($file, -1) != "~" && substr($file, -4) != "orig" && substr($file, -3) != "rej")
			{
				if (is_dir($fp))
				{
					$dc[] = $file;
				}
				else
				{
					$fc[] = $file;
				}
			}
		}
		closedir($dh);

		sort($dc);
		sort($fc);

		foreach($dc as $file)
		{
			$fp = $path."/".$file;
			$tv->add_item($path, array(
				"name" => $file,
				"id" => $fp,
				"url" => "#",
			));
			$this->_req_mk_clf_tree($tv, $fp, $classes);
		}
		foreach($fc as $file)
		{
			$fp = $path."/".$file;
			$awpath = str_replace(aw_ini_get("classdir"), "", $fp);
			if (automatweb::$request->arg("group") != "")
			{
				$url = aw_url_change_var("tf", str_replace(aw_ini_get("classdir"), "", $fp));
			}
			else
			{
				$url = $this->mk_my_orb("class_info", array("file" => str_replace(aw_ini_get("classdir"), "", $fp)));
			}
			// if the file only has 1 class in it, direct link to that, else split subs
			if (!isset($classes[$fp]) || count($classes[$fp]) < 2)
			{
				$tv->add_item($path, array(
					"name" => $file,
					"id" => $fp,
					"url" => $url,
					"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
					"target" => "classinfo"
				));
			}
			else
			{
				$tv->add_item($path, array(
					"name" => $file,
					"id" => $fp,
					"url" => $url,
					"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
					"target" => "classinfo"
				));
				foreach($classes[$fp] as $clinf)
				{
					$v = $clinf["class_name"];
					if ($v == "")
					{
						$clinf["class_name"] = "__outer";
						$v = t("Functions not in any class");
					}
					else
					{
						switch($clinf["class_type"])
						{
							case "interface":
								$v = t("Interface: ").$v;
								break;

							case "class":
								$v = t("Class: ").$v;
								break;

							case "exception":
								$v = t("Exception: ").$v;
								break;
						}
					}
					$tv->add_item($fp, array(
						"name" => $v,
						"id" => $fp."::".$clinf["class_name"],
						"url" => $this->mk_my_orb("class_info", array("file" => $awpath, "disp" => $clinf["class_name"])),
						"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
						"target" => "classinfo"
					));
				}
			}
		}
	}

	/**

		@attrib name=frames params=name default="1"

		@param id optional type=int

		@returns


		@comment

	**/
	function frameset($arr)
	{
		if (empty($arr["id"]))
		{
			$this->read_template("frameset_initial.tpl");

			$list = new object_list(array(
				"class_id" => CL_AW_DOCGEN_VIEWER
			));


			if ($list->count() === 1)
			{ // one found, redirect
				$id = $list->begin()->id();
				$url = $this->mk_my_orb("frames", array(array("id" => $id)));
				aw_redirect(new aw_uri($url));
			}
			elseif ($list->count() === 0)
			{ // none found, offer to create if logged in
				if (aw_global_get("uid"))
				{
					$user = new object(aw_global_get("uid_oid"));
					$this->vars(array(
						"create_url" => $this->mk_my_orb("new", array(array("parent" => $user->prop("home_folder"))))
					));
					$this->vars(array("CREATE" => $this->parse("CREATE")));
				}
				else
				{
					$auth = new auth_config();
					echo $auth->show_login();
					exit;
				}
			}
			else
			{ // many found, offer selection
				$o = $list->begin();
				$selection = "";
				do
				{
					$this->vars(array(
						"url" => $this->mk_my_orb("frames", array(array("id" => $o->id()))),
						"name" => $o->name(),
						"oid" => $o->id()
					));
					$selection .= $this->parse("LIST_ITEM");
				}
				while ($o = $list->next());
				$this->vars(array("LIST_ITEM" => $selection));
				$this->vars(array("SELECTION_LIST" => $this->parse("SELECTION_LIST")));
			}
		}
		else
		{
			$this->read_template("frameset.tpl");

			$this->vars(array(
				"left" => $this->mk_my_orb("api_class_list"),
				"right" => $this->mk_my_orb("intro"),
				"doclist" => $this->mk_my_orb("doclist"),
				"topf" => $this->mk_my_orb("topf", array("id" => $arr["id"]))
			));
		}

		echo $this->parse();
		exit;
	}

	function display_class($data, $cur_file, $opts = array())
	{
		if ($opts["disp"] == "__outer")
		{
			$this->read_template("function_info.tpl");
		}
		else
		{
			$this->read_template("class_info.tpl");
		}

		$ex_list = array();
		foreach($this->_get_exception_list() as $row)
		{
			$ex_list[$row["class_name"]] = str_replace("/classes", "", $row["file"]);
		}
		uksort($ex_list, create_function('$a,$b', 'return strlen($b) - strlen($a);'));

		$if_meth_list = $this->_get_if_methods_for_class($data);

		$usage_class = ( !empty($data["name"]) ) ? $data["name"] : "";
		// if this is an object override class, then the class you use things from is object
		$clss = aw_ini_get("classes");
		foreach($clss as $cldata)
		{
			if (isset($cldata["object_override"]) && basename($cldata["object_override"]) == $usage_class)
			{
				$usage_class = "object";
				break;
			}
		}

		$cln = ( !empty($data["name"]) ) ? $data["name"] : "";
		if ($cln === "document" || $cln === "document_brother")
		{
			$cln = "doc";
		}
		$cfgu = new cfgutils();
		if ($cfgu->has_properties(array(
			"file" => $cln,
			"clid" => $usage_class
		)))
		{
			$props = $cfgu->load_properties(array(
				"file" => $cln,
				"clid" => $usage_class
			));
		}
		else
		{
			$props = array();
		}

		$f = array(
			"CB" => "",
			"API" => "",
			"ORB" => "",
			"PRIVATE" => "",
			"OTHER" => "",
		);
		$api_count = 0;
		$orb_count = 0;
		$fl = "";
		foreach($data["functions"] as $func => $f_data)
		{
			$arg = "";

			if (!empty($opts["api_only"]) && empty($f_data["doc_comment"]["attribs"]["api"]))
			{
				continue;
			}

			$api_count += !empty($f_data["doc_comment"]["attribs"]["api"]) ? 1 : 0;
			$orb_count += !empty($f_data["doc_comment"]["attribs"]["name"]) ? 1 : 0 ;

			$_ar = new aw_array($f_data["arguments"]);
			foreach($_ar->get() as $a_var => $a_data)
			{
				$this->vars(array(
					"arg_name" => $a_data["name"],
					"def_val" => $a_data["default_val"],
					"is_ref" => ($a_data["is_ref"] ? "X" : "")
				));

				$arg .= $this->parse("ARG");
			}

			$attribs = "";
			if(isset($f_data['doc_comment']['attribs']) && is_array($f_data['doc_comment']['attribs']))
			{
				foreach ($f_data['doc_comment']['attribs'] as $attrib_name => $attrib_value)
				{
					$this->vars(array(
						'attrib_name' => $attrib_name,
						'attrib_value' => $attrib_value
					));
					$attribs .= $this->parse('ATTRIB');
				}
			}

			$params = "";
			$f_data['doc_comment']['params'] = isset($f_data['doc_comment']['params']) ? safe_array($f_data['doc_comment']['params']) : array();
			foreach ($f_data['doc_comment']['params'] as $param_name => $param_data)
			{
				$this->vars(array(
					'param_name' => $param_name,
					'param_required' => $param_data['req'],
					'param_type' => isset($param_data['type']) ? $param_data['type'] : NULL,
					'param_comment' => nl2br(trim($param_data['comment']))
				));
				$params .= $this->parse('PARAM');
			}

			$doc_file = dirname($cur_file)."/".basename($cur_file, ".aw")."/".$data["name"].".".$func.".txt";
			$example_links = "";
			if(isset($f_data["doc_comment"]["examples_links"]) && is_array($f_data["doc_comment"]["examples_links"]))
			{
				foreach($f_data["doc_comment"]["examples_links"] as $match => $url)
				{
					$example_links .= "<a href=\"".$url."\">$match</a><br />";
				}
			}


			$errs = (empty($f_data['doc_comment']['errors'])) ? t('none') : nl2br($f_data['doc_comment']['errors']);

			foreach($ex_list as $ex_class => $ex_file)
			{
				if (strpos($errs, $ex_class) !== false)
				{
					$errs = preg_replace(
						"/(\s)$ex_class(\s)/",
						"\\1".html::href(array(
							"caption" => $ex_class,
							"url" => $this->mk_my_orb("class_info", array("file" => $ex_file, "disp" => $ex_class)),
						))."\\2",
						$errs
					);
				}
			}

			$this->vars(array(
				"proto" => "function $func()",
				"name" => $func,
				"view_func" => aw_global_get("REQUEST_URI")."#fn.$func",
				"start_line" => $f_data["start_line"],
				"start_line_lxr" => sprintf("%03d", $f_data["start_line"]),
				"end_line" => isset($f_data["end_line"]) ? $f_data["end_line"] : NULL,
				"returns_ref" => ($f_data["returns_ref"] ? "X" : "&nbsp;"),
				"ARG" => $arg,
				"short_comment" => (!isset($f_data["doc_comment"]["short_comment"]) || $f_data["doc_comment"]["short_comment"] == "" ? "" : $f_data["doc_comment"]["short_comment"]."<Br>"),
				'ATTRIB' => $attribs,
				'PARAM' => $params,
				'returns' => (empty($f_data['doc_comment']['returns'])) ? t('nothing') : nl2br($f_data['doc_comment']['returns']),
				'errors' => $errs,
				'comment' => isset($f_data['doc_comment']['comment']) ? nl2br($f_data['doc_comment']['comment']) : "",
				'examples' => (empty($f_data['doc_comment']['examples'])) ? t('none') : highlight_string("<?php \n\t\t".$f_data['doc_comment']['examples']."\n?>", true).(isset($example_links) && strlen($example_links) ? "<br>".$example_links : ""),
				"view_source" => $this->mk_my_orb("view_source", array("file" => $cur_file, "v_class" => $data["name"],"func" => $func)),
				"view_usage" => $this->mk_my_orb("doc_search_form", array("search" => $usage_class."::".$func, "from" => array("docgen_search_use_func"), "no_reforb" => 1), "docgen_search"),
				"doc" => $this->show_doc(array("file" => $doc_file), true),
				"file" => $cur_file,
			));
			if (isset($f_data["doc_comment"]["attribs"]["api"]) && $f_data["doc_comment"]["attribs"]["api"] == 1)
			{
				$f["API"] .= $this->parse("API_FUNCTION");
			}
			else
			if (!empty($f_data["doc_comment"]["attribs"]["name"]))
			{
				$f["ORB"] .= $this->parse("ORB_FUNCTION");
			}
			else
			if (isset($this->cb_callbacks[$func]) || ((substr($func, 0, 5) == "_get_" || substr($func, 0, 5) == "_set_") && isset($props[substr($func, 5)])))
			{
				$f["CB"] .= $this->parse("CB_FUNCTION");
			}
			else
			if ($f_data["access"] != "public")
			{
				$f["PRIVATE"] .= $this->parse("PRIVATE_FUNCTION");
			}
			else
			if (isset($if_meth_list[$func]))
			{
				$d = $if_meth_list[$func];
				$clf = class_index::get_file_by_name(basename($d["class"]));
				$clf = str_replace(aw_ini_get("classdir"), "", $clf);
				$if_name = html::href(array(
					"url" => $this->mk_my_orb("class_info", array("file" => $clf, "disp" => $d["class"])),
					"caption" => $d["class"]
				));
				$f_if[$if_name] .= $this->parse("IF_FUNCTION");
			}
			else
			{
				$f["OTHER"] .= $this->parse("OTHER_FUNCTION");
			}
			$fl .= $this->parse("LONG_FUNCTION");
		}
		foreach($f as $_f_type => $_f_str)
		{
			if ($_f_str != "")
			{
				$this->vars(array(
					$_f_type."_FUNCTION" => $_f_str
				));
				$this->vars(array(
					"HAS_".$_f_type => $this->parse("HAS_".$_f_type)
				));
			}
		}
		$hif = "";
		if(isset($f_if) && is_array($f_if))
		{
			foreach($f_if as $if_name => $if_str)
			{
				if ($if_str != "")
				{
					$this->vars(array(
						"IF_FUNCTION" => $if_str,
						"if_name" => $if_name
					));
					$hif .= $this->parse("HAS_IF");
				}
			}
		}
		$this->vars(array("HAS_IF" => $hif));

		if (!empty($data["extends"]))
		{
			$this->_display_extends($data);
		}
		$this->_display_templates($data["functions"]);

		if (isset($data["dependencies"]) && is_array($data["dependencies"]))
		{
			$this->_display_dependencies($data["dependencies"]);
		}

		if (isset($data["implements"]) && is_array($data["implements"]))
		{
			$this->_display_implements($data["implements"]);
		}

		$this->_display_throws($data);
		$this->_display_defines($opts["defines"]);
		$this->_display_member_vars($data);

		// do properties
		$clid = $this->_find_clid_for_name($data["name"]);
		if ($clid)
		{
			$this->_display_properties($clid, $data);
		}

		$this->vars(array(
			"name" => $data["name"],
			"extends" => isset($data["extends"]) ? $data["extends"] : NULL,
			"end_line" => isset($data["end_line"]) ? $data["end_line"] : NULL,
			"start_line" => $data["start_line"],
			"LONG_FUNCTION" => $fl,
			"view_class" => $this->mk_my_orb("view_source", array("file" => $cur_file, "v_class" => $data["name"])),
			"update_url" => $this->mk_my_orb("do_db_update", array("file" => $cur_file), "docgen_db_writer"),
			"maintainer" => isset($data["maintainer"]) ? $data["maintainer"] : NULL,
			"cvs_version" => isset($data["cvs_version"]) ? $data["cvs_version"] : NULL,
			"file" => $cur_file,
			"func_count" => count($data["functions"]),
			"api_func_count" => $api_count,
			"orb_func_count" => $orb_count,
			"type_name" => $data["type"],
			"cvsweb_url" => "http://dev.struktuur.ee/cgi-bin/viewcvs.cgi/automatweb_dev/classes".$cur_file,
			"class_comment" => isset($data["class_comment"]) ? nl2br($data["class_comment"]) : "",
			"file_url" => $this->mk_my_orb("class_info", array("file" => $cur_file, "api_only" => automatweb::$request->arg("api_only"))),
		));

		die($this->finish_with_style($this->parse()));
	}

	private function _display_member_vars($data)
	{
		$tmp = isset($data["tracked_vars"]) ? $data["tracked_vars"] : array();
		if(isset($data["member_var_defs"]) && is_array($data["member_var_defs"]))
		{
			foreach($data["member_var_defs"] as $varn => $d)
			{
				if (!isset($tmp[$varn]))
				{
					$tmp[$varn] = array();
				}
			}
		}
		$str = "";
		$str2 = "";

		foreach($tmp as $var_name => $var_data)
		{
			$used = array();
			$var_data["referenced"] = isset($var_data["referenced"]) ? safe_array($var_data["referenced"]) : array();
			foreach($var_data["referenced"] as $ref_data)
			{
				$used[] = html::href(array(
					"url" => get_ru()."#fn.$ref_data[function]",
					"caption" => $ref_data["function"]
				));
			}
			$def = isset($data["member_var_defs"][$var_name]) ? $data["member_var_defs"][$var_name] : false;
			$this->vars(array(
				"memv_name" => $var_name,
				"memv_used" => join(", ",array_unique($used)),
				"memv_type" => isset($var_data["class"]) ? $var_data["class"] : "",
				"memv_comment" => isset($data["member_var_defs"][$var_name]["comment"]) ? $data["member_var_defs"][$var_name]["comment"] : "",
			));
			if ($def)
			{
				$this->vars(array(
					"memv_access" => $def["access"],
					"memv_defined" => $def["line"],
					"memv_readonly" => $def["read_only"] ? t("Yes") : t("No"),
					"memv_defined_type" => $def["declared_type"],
					"memv_default_value" => $def["default_value"]
				));
				$str .= $this->parse("DEFINED_MEMBER_VAR");
			}
			else
			{
				$str2 .= $this->parse("UNDEFINED_MEMBER_VAR");
			}
		}
		$this->vars(array(
			"UNDEFINED_MEMBER_VAR" => $str,
			"DEFINED_MEMBER_VAR" => $str2
		));
	}

	private function _get_if_methods_for_class($data)
	{
		// get all methods for all interfaces class implements
		$awa = new aw_array(isset($data["implements"]) ? $data["implements"] : NULL);
		$this->db_query("SELECT * FROM aw_da_funcs WHERE class IN (".$awa->to_sql().")");
		$rv = array();
		while ($row = $this->db_next())
		{
			$rv[$row["func"]] = array("class" => str_replace("/classes", "", $row["class"]), "file" => $row["file"]);
		}
		return $rv;
	}

	function _display_implements($impl_arr)
	{
		$p = "";
		foreach($impl_arr as $impl)
		{
			switch ($impl)
			{
				case "aw_exception":
				case "awex_redundant_instruction":
					$clf = "/../lib/errorhandling.aw";
					break;

				default:
					try
					{
						$clf = class_index::get_file_by_name(basename($impl));
					}
					catch (awex_clidx_filesys $e)
					{
						die("'implements' for class $impl ".$e->getMessage());
					}
			}
			$clf = str_replace(aw_ini_get("classdir"), "", $clf);
			$this->vars(array(
				"link" => $this->mk_my_orb("class_info", array("file" => $clf, "api_only" => automatweb::$request->arg("api_only"), "disp" => basename($impl))),
				"name" => $impl
			));
			$p .= $this->parse("IMPLEMENTS");
		}
		$this->vars(array(
			"IMPLEMENTS" => $p
		));
	}

	function _display_templates($funcs)
	{
		// read main tpl folder from init method
		// read template files from read_template methods
		$used_tpls = array();
		$tpl_folder = "";
		foreach($funcs as $f_name => $f_data)
		{
			if(isset($f_data["local_calls"]) && is_array($f_data["local_calls"]))
			{
				foreach($f_data["local_calls"] as $lcall_data)
				{
					if ($lcall_data["func"] == "init")
					{
						// read template folder arg
						if (substr($lcall_data["arguments"], 0, 5) == "array")
						{
							// parse from array
							preg_match("/tpldir['\"]\=\>['\"](.*)['\"]/imsU", $lcall_data["arguments"], $mt);
							$tpl_folder = $mt[1];
						}
						else
						{
							$tpl_folder = $lcall_data["arguments"];
						}
					}
					if ($lcall_data["func"] == "read_template")
					{
						$used_tpls[$f_name][$lcall_data["arguments"]] = $lcall_data["arguments"];
					}
				}
			}
		}


		$p = "";
		foreach($used_tpls as $f_name => $tpls)
		{
			foreach($tpls as $tpl)
			{
				$this->vars(array(
					"func" => $f_name,
					"tpl_file" => $this->strip_quotes($tpl)
				));
				$p .= $this->parse("TEMPLATE");
			}
		}

		$this->vars(array(
			"TEMPLATE" => $p,
			"tpl_folder" => $tpl_folder
		));
	}

	private function strip_quotes($str)
	{
		if ($str[0] == "'" || $str[0] == '"')
		{
			return substr(trim($str), 1, -1);
		}
		return $str;
	}

	function _display_throws($data)
	{
		$throws = array();
		foreach(safe_array($data["functions"]) as $func)
		{
			if(isset($func["throws"]) && is_array($func["throws"]))
			{
				foreach($func["throws"] as $thr)
				{
					$throws[$thr] = $thr;
				}
			}
		}
		$p = "";
		foreach($throws as $impl)
		{
			switch ($impl)
			{
				case "aw_exception":
				case "awex_redundant_instruction":
				case "awex_param":
				case "awex_param_type":
					$clf = "/../lib/exceptions.aw";
					break;

				case "Exception":
					$clf = "::internal";
					break;

				default:
					try
					{
						$clf = class_index::get_file_by_name(basename($impl));
					}
					catch (awex_clidx_filesys $e)
					{
						die("'throws' for class $impl: ".$e->getMessage());
					}
			}

			$clf = str_replace(aw_ini_get("classdir"), "", $clf);
			$this->vars(array(
				"link" => $this->mk_my_orb("class_info", array("file" => $clf, "api_only" => automatweb::$request->arg("api_only"), "disp" => basename($impl))),
				"name" => $impl
			));
			$p .= $this->parse("THROWS");
		}
		if (!empty($data["throws_undefined"]))
		{
			$p .= $this->parse("THROWS_UNSPECIFIC");
		}
		$this->vars(array(
			"THROWS" => $p,
			"THROWS_UNSPECIFIC" => ""
		));
	}

	/**
		@attrib name=prop_info

		@param option required
		@param id required
	**/
	function prop_info($arr)
	{
		echo $this->finish_with_style("");
		$obj = new object($arr['id']);
		$data = unserialize($obj->meta('properties_data'));

		if(isset($data->options[$arr['option']]))
		{
			if ("@property" == $arr['option'])
			{
				foreach($data->prop_attrib_map as $prop_type => $prop_attribs)
				{
					echo "<b>".$arr['option']."</b> ";
					echo " <font color='orange'><b>type=".$prop_type."</b></font> ";
					print aw_language_documenter::format_arr($prop_attribs,array("group","table","field"));
					print "<br>";
					print "<br>";
				};
			}
			else
			{
				echo "<b>".$arr['option']."</b> ";
				print aw_language_documenter::format_arr($data->options[$arr['option']]);
			};
			echo "<br>";
		}
		die;
	}

	/** displays information to the user about a class

		@attrib params=name all_args=0 caption="N&auml;ita klassi infot" default=0 name=class_info

		@param file required
		@param api_only optional
		@param disp optional

		@returns
		html with class info

		@comment
		shows detailed info about a class
	**/
	function class_info($arr)
	{
		extract($arr);

		$op = "";
		$analyzer = new aw_code_analyzer();
		$data = $analyzer->analyze_file($file);

		if (isset($data["classes"]))
		{
			foreach($data["classes"] as $class => $class_data)
			{
				if (!empty($arr["disp"]) && $class != $arr["disp"] && !($arr["disp"] == "__outer" && $class == ""))
				{
					continue;
				}
				$op .= $this->display_class($class_data, $file, array(
					"api_only" => isset($api_only) ? $api_only : NULL,
					"defines" => isset($data["defines"]) ? $data["defines"] : NULL,
					"disp" => isset($arr["disp"]) ? $arr["disp"] : NULL,
				));
			}
		}

		die($this->finish_with_style($op));
	}

	function _find_clid_for_name($name)
	{
		if ($name === "doc")
		{
			return CL_DOCUMENT;
		}
		foreach(aw_ini_get("classes") as $clid => $cld)
		{
			if (isset($cld["file"]) && basename($cld["file"]) == basename($name))
			{
				return $clid;
			}
		}
	}

	function _get_clid_names($ar)
	{
		$tmp = aw_ini_get("classes");
		$ara = array();
		foreach($ar as $clid)
		{
			$ara[] = basename($tmp[$clid]["file"]);
		}

		return join(", ", $ara);
	}

	/** displays function source

		@attrib name=view_source

		@param file required
		@param v_class required
		@param func optional

	**/
	function view_source($arr)
	{
		extract($arr);

		$file = urldecode($file);
		$file = str_replace(".","",dirname($file)) . "/" . basename($file);

		$da = new aw_code_analyzer();
		$data = $da->analyze_file($file);

		if ($func)
		{
			$start_line = $data["classes"][$v_class]["functions"][$func]["start_line"];
			$end_line = $data["classes"][$v_class]["functions"][$func]["end_line"];
		}
		else
		{
			$start_line = 0;
			$end_line = 100000;
		}

		$fd = file(aw_ini_get("basedir")."/classes".$file);
		$line = 1;
		if ($func)
		{
			$str = "<?php\n";
		}
		foreach($fd as $l)
		{
			if ($line >= $start_line && $line <= $end_line)
			{
				$str .= $l;
			}
			$line++;
		}
		if ($func)
		{
			$str .= "?>";
		}

		die($this->finish_with_style(highlight_string($str,true)));
	}

	function _display_dependencies($dependencies)
	{
		// build nice dep array
		$dep = array();
		$has_var = false;
		foreach($dependencies as $d_dat)
		{
			if ($d_dat["is_var"])
			{
				$has_var = true;
			}
			else
			{
				$dep[$d_dat["dep"]]["lines"][] = $d_dat["line"];
			}
		}

		$d_str = "";
		$d_str_var = "";
		if ($has_var)
		{
			$d_str_var = $this->parse("VAR_DEP");
		}

		foreach($dep as $d_class => $d_ar)
		{
			switch ($d_class)//TODO: Replaced variable by name "impl" which wasn't defined anywhere by "d_class". not sure if that's correct
			{
				case "aw_exception":
				case "awex_redundant_instruction":
					$clf = "/../lib/errorhandling.aw";
					break;

				case "defs":
					$clf = "/../lib/defs.aw";
					break;

				case "Exception":
					$clf = "::internal";
					break;

				default:
					try
					{
						$clf = class_index::get_file_by_name(basename($d_class));
					}
					catch (awex_clidx_filesys $e)
					{
						die("'dependency' for class $d_class ".$e->getMessage());
					}
			}
			$clf = str_replace(aw_ini_get("classdir"), "", $clf);
			$this->vars(array(
				"name" => $d_class,
				"lines" => join(",", $d_ar["lines"]),
				"link" => $this->mk_my_orb("class_info", array("file" => $clf, "api_only" => automatweb::$request->arg("api_only"), "disp" => basename($d_class))),
			));
			$d_str .= $this->parse("DEP");
		}

		$this->vars(array(
			"DEP" => $d_str,
			"VAR_DEP" => $d_str_var
		));
	}

	function _display_properties($clid, $data)
	{
		$cln = $data["name"];
		if ($cln === "document" || $cln === "document_brother")
		{
			$cln = "doc";
		}
		$cfgu = new cfgutils();
		$props = $cfgu->load_properties(array(
			"file" => $cln,
			"clid" => $clid
		));

		$p2t = array();
		$p_tbl = "";
		foreach($props as $prop)
		{
			$this->vars(array(
				"name" => $prop["name"],
				"type" => $prop["type"],
				"comment" => isset($prop["caption"]) ? $prop["caption"] : NULL,
			));
			$p_tbl .= $this->parse("PROP");

			$p2t[$prop["table"]][] = $prop["name"];
		}

		$ri = $cfgu->get_relinfo();
		$i_ri = array();
		foreach($ri as $ri_v => $ri_d)
		{
			if (substr($ri_v, 0, strlen("RELTYPE")) === "RELTYPE")
			{
				$i_ri[$ri_d["value"]]["name"] = $ri_v;
			}

			if (!isset($i_ri[$ri_d["value"]]))
			{
				$i_ri[$ri_d["value"]] = $ri_d;
			}
		}

		$s_ri = "";
		foreach($i_ri as $ri_vl => $ri_d)
		{
			$this->vars(array(
				"name" => $ri_d["name"],
				"clids" => $this->_get_clid_names($ri_d["clid"]),
				"comment" => isset($ri_d["caption"]) ? $ri_d["caption"] : ""
			));

			$s_ri .= $this->parse("RELTYPE");
		}

		$t_str = "";
		$awt = new aw_array($cfgu->tableinfo);
		foreach($awt->get() as $tb => $tbd)
		{
			$this->vars(array(
				"name" => $tb,
				"index" => $tbd["index"],
				"properties" => join(", ", $p2t[$tb])
			));
			$t_str .= $this->parse("TABLE");
		}

		$this->vars(array(
			"PROP" => $p_tbl,
			"RELTYPE" => $s_ri,
			"TABLE" => $t_str
		));
	}

	function _display_extends($dat)
	{
		$orb = new orb();
		$that = new aw_code_analyzer();

		// now, do extended classes. we do that by parsing all the extends classes
		// which of course slows us to hell and beyond. these parses should be cached or something
		$level = 0;
		$ex = "";
		do {
			$level++;

			if ($dat["extends"] === "db_connector")
			{
				$_extends = "db";
			}
			else
			{
				$_extends = $dat["extends"];
			}

			// get the file the class is in.
			// for that we have to load it's orb defs to get the folder below the classes folder
			$orb_defs = $orb->load_xml_orb_def($_extends);
			$ex_fname = aw_ini_get("basedir")."/classes/".$orb_defs[$dat["extends"]]["___folder"]."/".$_extends.".".AW_FILE_EXT;

			try
			{
				switch ($dat["extends"])
				{
					case "Exception":
						$clf = "::internal";
						break;

					case "aw_exception":
					case "awex_redundant_instruction":
						$clf = "/../lib/errorhandling.aw";
						break;

					default:
						$clf = class_index::get_file_by_name(basename($dat["extends"]));
						break;
				}

			}
			catch (awex_clidx_filesys $e)
			{
				die("'extends' for class $_extends ".$e->getMessage());
			}
			$clf = str_replace(aw_ini_get("classdir"), "", $clf);

			$this->vars(array(
				"spacer" => str_repeat("&nbsp;", $level * 3),
				"inh_link" => $this->mk_my_orb("class_info", array("file" => $clf, "api_only" => automatweb::$request->arg("api_only"), "disp" => basename($dat["extends"]))),
				"inh_name" => $dat["extends"]
			));
			$ex .= $this->parse("EXTENDER");

			$_dat = $that->analyze_file($ex_fname, true);
			$dat = isset($_dat["classes"][$dat["extends"]]) ? $_dat["classes"][$dat["extends"]] : NULL;
		} while ($dat["extends"] != "");

		$this->vars(array(
			"EXTENDER" => $ex,
		));
	}

	/**

		@attrib name=doclist

		@param type optional default="classes"

	**/
	function doclist($arr)
	{
		extract($arr);
		$this->read_template("doclist.tpl");

		/*if ($type == "classes")
		{
			$list = $this->do_class_doclist();
		}
		else
		{*/
			$list = $this->do_tut_doclist();
		//}

		$this->vars(array(
			"classdoc" => $this->mk_my_orb("doclist", array("type" => "classes")),
			"tutorials" => $this->mk_my_orb("doclist", array("type" => "tutorials")),
			"list" => $list
		));

		die($this->finish_with_style($this->parse()));
	}

	function do_class_doclist()
	{
		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgdoclss",
			"persist_state" => true,
			"root_name" => t("Classes"),
			"url_target" => "list"
		));

		$this->basedir = aw_ini_get("basedir")."/docs/classes";
		$this->_req_mk_clfdoc_tree($tv, $this->basedir);

		$str = $tv->finalize_tree(array(
			"rootnode" => $this->basedir,
		));
		die($this->finish_with_style($str));
	}

	function _req_mk_clfdoc_tree($tv, $path)
	{
		$dc = array();
		$fc = array();
		$dh = opendir($path);
		while (($file = readdir($dh)) !== false)
		{
			$fp = $path."/".$file;
			if ($file != "." && $file != ".." && $file != "CVS" && substr($file, 0,2) != ".#")
			{
				if (is_dir($fp))
				{
					$dc[] = $file;
				}
				else
				{
					$fc[] = $file;
				}
			}
		}
		closedir($dh);

		sort($dc);
		sort($fc);

		foreach($dc as $file)
		{
			$fp = $path."/".$file;
			$tv->add_item($path, array(
				"name" => $file,
				"id" => $fp,
				"url" => "#",
			));
			$this->_req_mk_clfdoc_tree($tv, $fp);
		}
		foreach($fc as $file)
		{
			$fp = $path."/".$file;
			$tv->add_item($path, array(
				"name" => $file,
				"id" => $fp,
				"url" => $this->mk_my_orb("show_doc", array("file" => str_replace($this->basedir, "", $fp))),
				"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
				"target" => "classinfo"
			));
		}
	}

	function do_tut_doclist()
	{
		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgdoclss",
			"persist_state" => true,
			"root_name" => t("Classes"),
			"url_target" => "list"
		));

		$this->basedir = aw_ini_get("basedir")."/docs/tutorials";
		$this->_req_mk_clfdoc_tree($tv, $this->basedir);

		$str = $tv->finalize_tree(array(
			"rootnode" => $this->basedir,
		));
		die($this->finish_with_style($str));
	}

	/** displays the documentation file $file

		@attrib name=show_doc

		@param file required

	**/
	function show_doc($arr, $oh_please_dont_die = false)
	{
		extract($arr);
		$file = preg_replace("/(\.){2,}/", "", $file);
		if (file_exists(aw_ini_get("basedir")."/docs/classes".$file))
		{
			$fp = aw_ini_get("basedir")."/docs/classes".$file;
		}
		else
		{
			$fp = aw_ini_get("basedir")."/docs/tutorials".$file;
		}
		$str = $this->get_file(array(
			"file" => $fp
		));

		$str = preg_replace("/(#code#)(.+?)(#\/code#)/esm","\"<pre>\".htmlspecialchars(stripslashes('\$2')).\"</pre>\"",$str);
		$str = preg_replace("/(#php#)(.+?)(#\/php#)/esm","highlight_string(stripslashes('<'.'?php'.'\$2'.'?'.'>'),true)",$str);

		if($oh_please_dont_die)
		{
			return $this->finish_with_style(nl2br($str));
		}
		else
		{
			die($this->finish_with_style(nl2br($str)));
		}
	}

	function finish_with_style($str)
	{
		$tpl = new docgen_viewer();
		$tpl->read_template("style.tpl");
		$tpl->vars(array(
			"content" => $str
		));
		return $tpl->parse();
	}

	/** displays top frame

		@attrib name=topf

		@param id optional

	**/
	function topf($arr)
	{
		$ret = array();

		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("intro"),
			"target" => "list",
			"caption" => t("Class overview")
		));

		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("class_list"),
			"target" => "classlist",
			"caption" => t("All classes")
		));

		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("api_class_list"),
			"target" => "classlist",
			"caption" => t("API classes")
		));

		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("interface_list"),
			"target" => "classlist",
			"caption" => t("Interfaces")
		));

		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("exception_list"),
			"target" => "classlist",
			"caption" => t("Exceptions")
		));

		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("doclist"),
			"target" => "classlist",
			"caption" => t("Separate documentation")
		));

		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("iniviewer"),
			"target" => "classlist",
			"caption" => t("INI")
		));


		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("proplist", array('id'=>$arr['id'])),
			"target" => "classlist",
			"caption" => t("Classbase tags")
		));

		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("doc_search_form", array(), "docgen_search"),
			"target" => "classlist",
			"caption" => t("Search")
		));

		$ret[] = html::href(array(
			"url" => $this->mk_my_orb("do_db_update",array('id'=>$arr['id']), "docgen_db_writer"),
			"target" => "bott",
			"caption" => t("Renew database")
		));

		if ($arr["id"])
		{
			$o = obj($arr["id"]);
			$f_id = $o->prop("foorum");

			if ($f_id)
			{
				$ret[] = html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $f_id, "group" => "contents"), CL_FORUM_V2),
					"target" => "list",
					"caption" => t("Foorum")
				));
			}
		}


		$this->read_template("style.tpl");
		$this->vars(array(
			"content" => "&nbsp;&nbsp;".join(" | ", $ret)
		));
		die($this->parse());
	}

	/**
		@attrib name=interface_list
	**/
	function interface_list($arr)
	{
		$this->read_template("classlist.tpl");

		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgifaces",
			"persist_state" => true,
			"root_name" => t("Interfaces"),
			"url_target" => "list"
		));

		// gather data about things in files
		$this->db_query("SELECT * from aw_da_classes WHERE class_type = 'interface'");
		while ($row = $this->db_next())
		{
			$tv->add_item(0, array(
				"name" => $row["class_name"],
				"id" => $row["class_name"],
				"url" => $this->mk_my_orb("class_info", array("file" => str_replace("/classes/", "/",$row["file"]), "disp" => $row["class_name"])),
				"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
				"target" => "classinfo"
			));
		}

		$this->vars(array(
			"list" => $tv->finalize_tree(array(
				"rootnode" => 0
			))
		));

		die($this->finish_with_style($this->parse()));
	}

	/**
		@attrib name=maintainer_class_list
	**/
	function maintainer_class_list($arr)
	{
		$this->read_template("classlist.tpl");

		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgmaints",
			"persist_state" => true,
			"root_name" => t("Classes by maintainer"),
			"url_target" => "list"
		));

		// gather data about things in files
		$this->db_query("SELECT * from aw_da_classes ");
		$c = array();
		while ($row = $this->db_next())
		{
			$c[$row["maintainer"]][] = $row;
		}

		foreach($c as $maintainer => $clss)
		{
			if ($maintainer == "")
			{
				$maintainer = "M&auml;&auml;ramata";
			}
			$tv->add_item(0, array(
				"name" => $maintainer,
				"id" => $maintainer,
				"url" => "",
				"iconurl" => icons::get_icon_url(CL_MENU,""),
				"target" => "classinfo"
			));
			foreach($clss as $row)
			{
				$tv->add_item($maintainer, array(
					"name" => $row["class_name"],
					"id" => $row["class_name"],
					"url" => $this->mk_my_orb("class_info", array("file" => str_replace("/classes/", "/",$row["file"]), "disp" => $row["class_name"])),
					"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
					"target" => "classinfo"
				));
			}
		}

		$this->vars(array(
			"list" => $tv->finalize_tree(array(
				"rootnode" => 0
			))
		));

		die($this->finish_with_style($this->parse()));
	}

	/**
		@attrib name=exception_list
	**/
	function exception_list($arr)
	{
		$this->read_template("classlist.tpl");

		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgexception",
			"persist_state" => true,
			"root_name" => t("Exceptions"),
			"url_target" => "list"
		));

		// gather data about things in files
		foreach($this->_get_exception_list() as $row)
		{
			$tv->add_item(0, array(
				"name" => $row["class_name"],
				"id" => $row["class_name"],
				"url" => $this->mk_my_orb("class_info", array("file" => str_replace("/classes/", "/",$row["file"]), "disp" => $row["class_name"])),
				"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
				"target" => "classinfo"
			));
		}

		$this->vars(array(
			"list" => $tv->finalize_tree(array(
				"rootnode" => 0
			))
		));

		die($this->finish_with_style($this->parse()));
	}

	private function _get_exception_list()
	{
		$this->db_query("SELECT * from aw_da_classes WHERE class_type = 'exception'");
		$rv = array();
		while ($row = $this->db_next())
		{
			$rv[] = $row;
		}
		return $rv;
	}

	/**
		@attrib name=proplist
		@param id required
	**/
	function doc_proplist($arr)
	{
		$this->read_template("proplist.tpl");
		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgclsss",
			"persist_state" => true,
			"root_name" => t("Classes"),
			"url_target" => "list"
		));

		$this->_req_mk_prop_tree(array(
			'id' => $arr['id'],
			'tree' => $tv,
			'classdir' => aw_ini_get("classdir"),
		));

		$this->vars(array(
			"list" => $tv->finalize_tree(array(
				"rootnode" => 0,
			))
		));

		die($this->finish_with_style($this->parse()));
	}

	function _req_mk_prop_tree($arr)
	{
		$tree = $arr['tree'];
		$obj = new object($arr['id']);
		$data = unserialize($obj->meta('properties_data'));

		if(!$data)
		{
			//have to generate the data
			$documenter = new aw_language_documenter;
			$documenter->parse_files($arr['classdir']);
			$obj->set_meta('properties_data',serialize($documenter));
			$obj->save();
			$data = $documenter;
		}


		foreach($data->options as $option_name=>$option)
		{
			$tree->add_item(0,array(
				'name' => $option_name,
				'id' => $option_name,
				'url' => $this->mk_my_orb(
					'prop_info',
					array(
						'option' => $option_name,
						'id' => $obj->id(),
					)
				),
				'target' => 'propinfo',
			));
		}
	}

	/**

		@attrib name=api_class_list

	**/
	function api_class_list($arr)
	{
		$this->read_template("classlist.tpl");

		$tv = new treeview();

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"tree_id" => "dcgclsssapi",
			"persist_state" => true,
			"root_name" => t("Classes"),
			"url_target" => "list"
		));

		$this->db_query("SELECT * from aw_da_classes WHERE has_apis=1");
		$api_files = array();
		$classes = array();
		while ($row = $this->db_next())
		{
			$fp = aw_ini_get("basedir").$row["file"];
			$api_files[$fp] = $fp;
			$classes[$fp][] = $row;
		}

		$this->_req_mk_clf_api_tree($tv, aw_ini_get("classdir"), $api_files, $classes);

		$this->vars(array(
			"list" => $tv->finalize_tree(array(
				"rootnode" => aw_ini_get("classdir"),
			))
		));

		die($this->finish_with_style($this->parse()));
	}

	function _req_mk_clf_api_tree($tv, $path, $api_files, $classes)
	{
		$dc = array();
		$fc = array();
		$dh = opendir($path);
		while (($file = readdir($dh)) !== false)
		{
			$fp = $path.$file;
			if ($file !== "." && $file != ".." && $file !== "CVS" && substr($file, 0,2) !== ".#")
			{
				if (is_dir($fp))
				{
					$dc[] = $file;
				}
				else
				{
					if (isset($api_files[$fp]) && $api_files[$fp])
					{
						$fc[] = $file;
					}
				}
			}
		}
		closedir($dh);

		sort($dc);
		sort($fc);

		$hasf = false;
		foreach($dc as $file)
		{
			$fp = $path.$file."/";
			$_hasf = $this->_req_mk_clf_api_tree($tv, $fp, $api_files, $classes);

			if ($_hasf)
			{
				$tv->add_item($path, array(
					"name" => $file,
					"id" => $fp,
					"url" => "#",
				));
				$hasf = true;
			}
		}

		foreach($fc as $file)
		{
			$fp = $path.$file;
			$awpath = str_replace(aw_ini_get("classdir"), "", $fp);

			// if the file only has 1 class in it, direct link to that, else split subs
			if (count($classes[$fp]) < 2)
			{
				$tv->add_item($path, array(
					"name" => $file,
					"id" => $fp,
					"url" => $this->mk_my_orb("class_info", array("file" => $awpath, "api_only" => 1)),
					"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
					"target" => "classinfo"
				));
			}
			else
			{
				$tv->add_item($path, array(
					"name" => $file,
					"id" => $fp,
					"url" => $this->mk_my_orb("class_info", array("file" => $awpath, "api_only" => 1)),
					"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
					"target" => "classinfo"
				));
				foreach($classes[$fp] as $clinf)
				{
					$v = $clinf["class_name"];
					if ($v == "")
					{
						$clinf["class_name"] = "__outer";
						$v = t("Functions not in any class");
					}
					else
					{
						switch($clinf["class_type"])
						{
							case "interface":
								$v = t("Interface: ").$v;
								break;

							case "class":
								$v = t("Class: ").$v;
								break;

							case "exception":
								$v = t("Exception: ").$v;
								break;
						}
					}
					$tv->add_item($fp, array(
						"name" => $v,
						"id" => $fp."::".$clinf["class_name"],
						"url" => $this->mk_my_orb("class_info", array("file" => $awpath, "api_only" => 1, "disp" => $clinf["class_name"])),
						"iconurl" => icons::get_icon_url(CL_OBJECT_TYPE,""),
						"target" => "classinfo"
					));
				}
			}
			$hasf = true;
		}

		return $hasf;
	}


	private function _display_defines($d)
	{
		$s = "";
		foreach(safe_array($d) as $def)
		{
			$this->vars(array(
				"name" => $def["key"],
				"value" => $def["value"],
				"comment" => $def["comment"],
			));
			$s .= $this->parse("DEFINES");
		}
		$this->vars(array(
			"DEFINES" => $s
		));
	}
}


class aw_language_documenter
{
	var $options = array();
	var $prop_attrib_map = array();
	var $files_parsed = 0;

	function parse_files($dirname)
	{
		$handle = opendir($dirname);
		while($file = readdir($handle))
		{
			if($file==='.' || $file==='..' || $file==="CVS")
				continue;
			if(is_dir($dirname.'/'.$file))
			{
				$this->parse_files($dirname.'/'.$file);
			}
			else
			{
				if(substr($file,strlen($file)-2)=='aw')
				{
					$this->parse_file($dirname.'/'.$file);
				}
			}
		}
		closedir($handle);
	}

	function parse_file($file)
	{
		$lines = file($file);
		$lines = array_filter($lines,array($this,'is_option'));
		foreach($lines as $line)
		{
			$tmp_arr = explode(' ',trim($line));
			if(sizeof($tmp_arr)==0)
				continue;
			$first_element = $tmp_arr[0];
			unset($tmp_arr[0]);
			if(!array_key_exists($first_element,$this->options))
			{
				$this->options[$first_element] = array();
			}
			$this->generate_option_attributes($first_element,$tmp_arr);
		}
		$lines=array();
	}

	function generate_option_attributes($key,$arr)
	{
		$rtrn = array();
		$attribs = array();
		foreach($arr as $value)
		{
			//if(!in_array($value,$this->options[$key]))
			{
				//if key=value
				$tmp_arr = explode('=',$value);
				if(sizeof($tmp_arr)>1)
				{
					$attribs[$tmp_arr[0]] = $tmp_arr[1];
					//vaatame kas key existib
					if(!array_key_exists($tmp_arr[0],$this->options[$key]))
					{
						$this->options[$key][$tmp_arr[0]] = array();
					}
					//vaatame kas juba selline param v22rtus existeib
					if(!in_array($tmp_arr[1],$this->options[$key][$tmp_arr[0]]))
					{
						$this->options[$key][$tmp_arr[0]][] = $tmp_arr[1];
					}

				}
			}
		}

		if ("@property" === $key && isset($attribs["type"]))
		{
			$type = $attribs["type"];
			unset($attribs["type"]);
			foreach($attribs as $akey => $avalue)
			{
				$this->prop_attrib_map[$type][$akey][$avalue] = $avalue;
			};
		};
		return $rtrn;
	}

	function is_option($string)
	{
		if($string{0}==='@')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function format_arr($source,$hide_keys = array())
	{
		$tmp = "";
		foreach($source as $key2=>$value2)
		{
			$tmp .= "<font color='green'>".$key2."</font>=";
			$tmp .= ' ';
			if(!in_array($key2,$hide_keys) && is_array($value2) && sizeof($value2)<10)
			{
				$tmp.= '<b>{</b> <font color="gray">';
				$tmp.= join(' | ',$value2);
				$tmp.=' </font><b>}</b>&nbsp;';
			}
			else
			{
				$tmp.= '<b>{</b><font color="gray">...</font><b>}</b>&nbsp;';
			}
		}
		return $tmp;
	}
}
