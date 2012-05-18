<?php

/*

This message will get posted when we are showing the site.
parameters:
	inst - instance to which to import the variables
	params - array of parameters passed to site_show
EMIT_MESSAGE(MSG_ON_SITE_SHOW_IMPORT_VARS);

*/

/** all classes that wish to handle subtemplates in main.tpl must implement this **/
interface main_subtemplate_handler
{
	/** This gets called, when the sub by the name defined in the ini file in subtpl_handler exists in main.tpl and the class implements this interface
		@attrib api=1 params=name

		@param inst required type=object
			The aw_template instance to insert the value for the sub in

		@param content_for required type=string
			The name of the sub, content is requested for. Since one class can be a handler for several subs, this can be used to tell which one it is

		@param request required type=array
			The request parameters

		@comment
			This callback must fill in the contents of the template SUB, in the given instance.

		@examples
			class foo implements main_subtemplate_handler
			{
				function on_get_subtemplate_content($arr)
				{
					$arr["inst"]->vars(array(
						$arr["content_for"] => "allah"
					));
				}
			}
	**/
	public function on_get_subtemplate_content($arr);
}

class site_show extends aw_template
{
	var $path;				// the path to the selected section
	private $section;		// requested section
	var $section_obj;		// the object instance for $section
	var $sel_section;		// the MENU that is selected - section can point to any object below it
	var $sel_section_real;	// the MENU that is selected - section can point to any object below it -
							// this is the real object if translation is active - damn, it seems I can't make the translation
							// thing COMPLETELY transparent after all :((((((((
	var $sel_section_obj;	// the MENU OBJECT that is selected - section can point to any object below it
	var $properties;		// the properties gathered from menus in the path
	var $left_pane;			// whether to show LEFT_PANE sub
	var $right_pane;		// whether to show RIGHT_PANE sub
	var $active_doc;		// if only a single document is shownm this will contain the docid
	var $site_title;		// the title for the site should be put in here
	var $brother_level_from;
	var $current_login_menu_id;
	var $title_yah_arr;

	var $cache;				// cache class instance

	var $image;				// image class instance

	var $add_url;
	var $_is_in_document_list;
	var $path_brothers = array();

	function site_show()
	{
		$this->init("automatweb/menuedit");
		$this->image = new image();
	}

	////
	// !generates the whole site content thingie
	// parameters:
	// text - kui see != "" , siis n2idatakse dokude asemel seda
	// docid - millist dokumenti selle sektsiooni alt naidata?
	// s.t. kui on dokumentide nimekiri ntx.
	// strip_img - kas imaged maha strippida dokudest
	// template - mis template ga menyysid n2idataxe
	// vars - array kuhu saab sisu kirjutada, et seal olevad muutujad pannaxe menyyediti template sisse
	// sub_callbacks - array template_name => funxiooninimi neid kutsutakse siis v2lja kui vastav sub on template sees olemas
	// format - the format to generate the output in
	// no_left_pane - if true, the LEFT_PANE sub is by default not shown,
	// no_right_pane - if true, the RIGHT_PANE sub is by default not shown
	// tpldir - if set, templates are read from $tpldir/automatweb/menuedit folder
	function show($arr)
	{
		if (!empty($arr["type"]) && $arr["type"] !== "html")
		{
			return $this->_show_type($arr);
		}

		if (automatweb::$request->arg("set_doc_content_type"))
		{
			aw_session::set("doc_content_type", automatweb::$request->arg("set_doc_content_type"));
		}

		if (automatweb::$request->arg("clear_doc_content_type"))
		{
			aw_session::del("doc_content_type");
		}

		// init left pane/right pane
		$this->left_pane = (isset($arr["no_left_pane"]) && $arr["no_left_pane"] == true) ? false : true;
		$this->right_pane = (isset($arr["no_right_pane"]) && $arr["no_right_pane"] == true) ? false : true;

		$this->_init_path_vars($arr);
		// figure out the menu that is active
		$this->sel_section = $this->_get_sel_section();
		$this->sel_section_real = $this->sel_section;
		$this->sel_section_obj = $this->sel_section ? new object($this->sel_section) : obj(null, array(), CL_MENU);

		//redirect to frontpage if inactive menu
		if (
			$this->section == $this->sel_section &&
			$this->sel_section_obj &&
			!$this->sel_section_obj->is_frontpage() &&
			$this->sel_section_obj->status() != object::STAT_ACTIVE &&
			!aw_global_get("uid") &&
			!automatweb::$request->arg("class") //TODO: muul viisil. kalevatravelis menyys item "otsing", mitteaktiivne. see on sectioniks otsingul, mida ei n2ita kui !class rida pole.
		)
		{
			header("Location: " . aw_ini_get("baseurl"));
			exit;
		}

		$this->site_title = $this->sel_section_obj->trans_get_val("name");

		// read the left/right pane props from the sel menu
		if (!$this->sel_section_obj->prop("left_pane"))
		{
			$this->left_pane = false;
		}

		if (!$this->sel_section_obj->prop("right_pane"))
		{
			$this->right_pane = false;
		}

		$this->do_check_properties($arr);

		$apd = new active_page_data();
		$rv = $this->do_show_template($arr);

		$apd->on_shutdown_get_styles($rv);
		return $rv;
	}

	private function _show_type($arr)
	{
		switch($arr["type"])
		{
			case "rss":
				$rss = new rss();
				return $rss->gen_rss_feed(array(
					"period" => aw_global_get("act_per_id"),
					"parent" => $this->section
				));
		}
	}

	function import_class_vars($arr)
	{
		if (isset($arr["vars"]) && is_array($arr["vars"]))
		{
			$this->vars($arr["vars"]);
		}

		$request_uri = aw_ini_get("baseurl").aw_global_get("REQUEST_URI");
		$printlink = aw_url_change_var(array(
			"print" => 1,
		), false, $request_uri);

		$this->vars(array(
			"sel_menu_id" => $this->sel_section,
			"sel_menu_comment" => isset($arr["comment"]) ? $arr["comment"] : "",
			"site_title" => strip_tags($this->site_title),
			"printlink" => $printlink
		));

		$p = new period();
		$p->on_site_show_import_vars(array("inst" => $this));
		$p = get_instance(CL_SITE_STYLES);
		$p->on_site_show_import_vars(array("inst" => $this));
	}

	private function _get_sel_section()
	{
		$last_menu = 0;
		$cnt = count($this->path);
		for ($i = 0; $i < $cnt; $i++)
		{
			if ($this->path[$i]->class_id() == menu_obj::CLID)
			{
				$last_menu = $this->path[$i]->id();
			}
		}
		return $last_menu;
	}

	////
	// !Fetches the menu chain for the current object from the menu cache for further use
	// XXX: this should be moved to core - because at least one other class - document
	// uses this to figure out whether a cfgform based template should be shown -- duke
	function build_menu_chain($section)
	{
		// we will this with properties from the first element in chain who
		// has those
		$this->properties = array(
			"tpl_dir"  => "", // prop!
			"users_only" => 0, // prop!
			"comment" => "", // prop!
			"tpl_view" => "", // prop!
			"tpl_lead" => "",// prop!
			"show_layout" => "",
			"ip_allowed" => array(),
			"ip_denied" => array(),
			"images" => array(),
			"has_ctx" => 0,
			"keywords" => "",
		);

		$ni = aw_ini_get("menu.num_menu_images");
		$cnt = count($this->path);
		for ($i = $cnt-1; $i > -1; $i--)
		{
			$obj = $this->path[$i];

			foreach($this->properties as $key => $val)
			{
				if ($key === "ip_allowed")
				{
					$tipa = $obj->meta("ip_allow");
					if (is_array($tipa) && count($tipa) > 0)
					{
						$this->properties[$key] = $tipa;
					}
				}
				elseif ($key === "ip_denied")
				{
					$tipa = $obj->meta("ip_deny");
					if (is_array($tipa) && count($tipa) > 0)
					{
						$this->properties[$key] = $tipa;
					}
				}
				elseif ($key === "images")
				{
					$im = $obj->meta("menu_images");
					for($imn = 0; $imn < $ni; $imn++)
					{
						if (!isset($this->properties["images"]) or !is_array($this->properties["images"]))
						{
							$this->properties["images"] = array();
						}
						if (!isset($im[$imn]) or !is_array($im[$imn]))
						{
							$im[$imn] = array();
						}
						if (!isset($this->properties["images"][$imn]) and isset($im[$imn]["image_id"]) and is_oid($im[$imn]["image_id"]))
						{
							$this->properties["images"][$imn] = $im[$imn]["image_id"];
						}
					}
				}
				elseif ($key === "tpl_view")
				{
					if ($i == 0 || !$obj->is_property("tpl_view_no_inherit") || !$obj->prop("tpl_view_no_inherit"))
					{
						$this->properties[ "tpl_view"] = $obj->is_property( "tpl_view") ? $obj->prop( "tpl_view") : "";
					}
				}
				elseif ($key === "tpl_lead")
				{
					if ($i == 0 || !$obj->is_property("tpl_lead_no_inherit") || !$obj->prop("tpl_lead_no_inherit"))
					{
						$this->properties["tpl_lead"] = $obj->is_property("tpl_lead") ? $obj->prop("tpl_lead") : "";
					}
				}
				elseif ($key === "tpl_dir")
				{
					if (empty($this->properties[$key]) && $obj->class_id() == menu_obj::CLID && $obj->prop("tpl_dir"))
					{
						$this->properties["tpl_dir"] = $obj->prop("tpl_dir") . "/";// tpl_dir menu property is a template set name selected from tpl sets defined in ini. Append slash
					}
				}
				elseif ($key === "keywords" && !isset($this->properties[$key]) && strlen($_t = $obj->trans_get_val($key)))
				{
					$this->properties[$key] = $_t;
				}
				else
				{
					// check whether this object has any properties that
					// none of the previous ones had
					if (empty($this->properties[$key]) && $obj->class_id() == menu_obj::CLID && $obj->prop($key))
					{
						$this->properties[$key] = $obj->prop($key);
					}
				}
			}
		}
	}

	function do_check_properties(&$arr)
	{
		$this->build_menu_chain($this->sel_section);
		if ($this->properties["show_layout"])
		{
			return $this->do_show_layout($this->properties["show_layout"]);
		}

		// if the remote user is not logged in and the users_only property is set,
		// redirect the him to the correspondending error page
		if ( (aw_global_get("uid") == "") && ($this->properties["users_only"]) )
		{
			$this->users_only_redir();
		}

		// if the tpl_dir property is set, reinitialize the template class
		if (!isset($arr["tpldir"]))
		{
			$arr["tpldir"] = $this->properties["tpl_dir"];
		}

		// hook for site specific gen_site_html initialization
		// feel free to add other stuff here, but make sure this
		// stays _before_ the tpl_init below
		$si = __get_site_instance();
		if (is_object($si) && method_exists($si,"init_gen_site_html"))
		{
			$si->init_gen_site_html(array(
				"tpldir" => &$arr["tpldir"],
				"template" => &$arr["template"],
				"inst" => $this
			));
		}

		if ($this->properties["comment"])
		{
			$arr["comment"] = $this->properties["comment"];
		}

		if (count($this->properties["ip_allowed"]) > 0 || count($this->properties["ip_denied"]))
		{
			$this->do_check_ip_access(array(
				"allowed" => $this->properties["ip_allowed"],
				"denied" => $this->properties["ip_denied"]
			));
		}

		if ($this->sel_section_obj->prop("has_ctx"))
		{
			$use_ctx = NULL;
			if (count($_SESSION["menu_context"]))
			{
				$use_ctx = $_SESSION["menu_context"];
			}
			elseif (is_oid($_ctx = $this->sel_section_obj->prop("default_ctx")) && acl_base::can("view", $_ctx))
			{
				$_ctx = obj($_ctx);
				$use_ctx = $_ctx->name();
			}

			if ($use_ctx)
			{
				// check if we need to redirect, based on current context
				// find the first submenu with the correct context
				$ol = new object_list(array(
					"parent" => $this->sel_section,
					"site_id" => aw_ini_get("site_id"),
					"lang_id" => AW_REQUEST_CT_LANG_ID,
					"class_id" => menu_obj::CLID,
					"CL_MENU.RELTYPE_CTX.name" => $use_ctx,
					"limit" => 1
				));
				if (!$ol->count())
				{
					// get the first submenu
					$ol = new object_list(array(
						"class_id" => menu_obj::CLID,
						"site_id" => aw_ini_get("site_id"),
						"lang_id" => AW_REQUEST_CT_LANG_ID,
						"parent" => $this->sel_section,
						"sort_by" => "objects.jrk",
						"limit" => 1
					));
				}

				if ($ol->count())
				{
					$o = $ol->begin();
					header("Location: ".obj_link($o->id()));
					die();
				}
			}
		}
	}

	////
	// !checks if the current IP has access
	// parameters:
	//	allowed - array of addresses allowed
	//	denied - array of addresses denied
	//
	// algorithm:
	// if count(allowed) > 0 , then deny everything else, except allowed
	// if count(denied) > 0, then allow everyone, except denied
	function do_check_ip_access($arr)
	{
		extract($arr);
		$cur_ip = aw_global_get("REMOTE_ADDR");
		$ipa = new ipaddress();

		if (count($allowed) > 0)
		{
			$deny = true;
			$has_ip = false;
			foreach($allowed as $ipid => $t)
			{
				if (is_oid($ipid) && acl_base::can("view", $ipid))
				{
					$has_ip = true;
					$ipo = obj($ipid);

					if ($ipa->match($ipo->prop("addr"), $cur_ip))
					{
						$deny = false;
					}
				}
			}

			if ($deny && $has_ip)
			{
				$this->no_ip_access_redir($this->section_obj->id());
			}
			else
			{
				return;
			}
		}

		if (count($denied) > 0)
		{
			$deny = false;
			$has_ip = false;
			foreach($denied as $ipid => $t)
			{
				if (is_oid($ipid) && acl_base::can("view", $ipid))
				{
					$ipo = obj($ipid);
					$has_ip = true;

					if ($ipa->match($ipo->prop("addr"), $cur_ip))
					{
						$deny = true;
					}
				}
			}

			if ($deny && $has_ip)
			{
				$this->no_ip_access_redir($this->section_obj->id());
			}
			else
			{
				return;
			}
		}
	}

	////
	// !Checks whether the section or one of it's parents is marked as "users_only
	function users_only_redir()
	{
		$url = $this->get_cval("orb_err_mustlogin_".aw_global_get("LC"));
		if (!$url)
		{
			$url = $this->get_cval("orb_err_mustlogin");
		}
		aw_session_set("request_uri_before_auth",aw_global_get("REQUEST_URI"));
		header("Location: ".aw_ini_get("baseurl").$url);
		exit;
	}

	function do_show_layout($lid)
	{
		$li = get_instance(CL_LAYOUT);
		return $li->show(array(
			"id" => $lid
		));
	}

	function do_sub_callbacks($sub_callbacks, $after = false)
	{
		if ($after)
		{
			$sub_callbacks = false;
			if (function_exists("__get_site_instance"))
			{
				$si = __get_site_instance();
				if (is_object($si))
				{
					if (method_exists($si, "get_sub_callbacks_after"))
					{
						$sub_callbacks = $si->get_sub_callbacks_after($this);
					}
				}
			}
		}

		if (is_array($sub_callbacks))
		{
			// ok, check if the new and better OO (TM) way exists
			if (function_exists("__get_site_instance"))
			{
				$si = __get_site_instance();
				if (is_object($si))
				{
					foreach($sub_callbacks as $sub => $fun)
					{
						if ($this->is_template($sub))
						{
							if (method_exists($si, $fun))
							{
								$si->$fun($this);
							}
							else
							{
								if (function_exists($fun))
								{
									$fun($this);
								}
							}
						}
					}
				}
				else
				{
					foreach($sub_callbacks as $sub => $fun)
					{
						if ($this->is_template($sub))
						{
							$fun($this);
						}
					}
				}
			}
			else
			{
				foreach($sub_callbacks as $sub => $fun)
				{
					if ($this->is_template($sub))
					{
						$fun($this);
					}
				}
			}
		}
	}

	function get_default_document($arr = array())
	{
		$docid = null;
		if (isset($arr["docid"]) && $arr["docid"] > 0)
		{
			return $arr["docid"];
		}

		if (isset($arr["obj"]))
		{
			$obj = $arr["obj"];
		}
		else
		{
			$obj = $this->section_obj;
		}

		if (!is_oid($obj->id()))
		{
			return false;
		}

		// if it is a document, use this one.
		if (($obj->class_id() == CL_DOCUMENT) || ($obj->class_id() == CL_PERIODIC_SECTION) || $obj->class_id() == CL_BROTHER_DOCUMENT)
		{
			return $obj->id();	// most important not to change this, it is!
		}

		if ($obj->is_brother())
		{
			$obj = $obj->get_original();
		}
		// if any keywords for the menu are set, we must show all the documents that match those keywords under the menu
		if ($obj->class_id() != CL_PROMO && ($obj->meta("has_kwd_rels") || !empty($_GET["set_kw"])))
		{
			// list all documents that have the same kwywords as this menu.
			// so first, get this menus keywords
			$m = get_instance(menu_obj::CLID);
			if ($_GET["set_kw"])
			{
				$kwlist = array($_GET["set_kw"]);
			}
			else
			{
				$kwlist = $m->get_menu_keywords($obj->id());
			}
			$c = new connection();
			$doclist = $c->find(array(
				"to" => $kwlist,
			));
			$docid = array();
			$non_docid = array();
			foreach($doclist as $con)
			{
				if ($con["from.class_id"] == CL_DOCUMENT)
				{
					if ($con["from.status"] == object::STAT_ACTIVE && $con["reltype"] == 28)
					{
						$docid[$con["from"]] = $con["from"];
					}
				}
				else
				{
					$non_docid[$con["from"]] = $con["from"];
				}
			}

			if (count($non_docid))
			{
				// fetch docs connected to THOSE
				$doclist = $c->find(array(
					"from.class_id" => CL_DOCUMENT,
					"to" => $non_docid
				));
				foreach($doclist as $con)
				{
					if ($con["from.status"] == object::STAT_ACTIVE)
					{
						$docid[$con["from"]] = $con["from"];
					}
				}
			}

			// if we have doc ct type set, then filter by that as well16.04.2006
			if ($_SESSION["doc_content_type"] && count($docid) > 0)
			{
				$ol = new object_list(array(
					"class_id" => CL_DOCUMENT,
					"site_id" => aw_ini_get("site_id"),
					"lang_id" => AW_REQUEST_CT_LANG_ID,
					"oid" => $docid,
					"doc_content_type" => $_SESSION["doc_content_type"]
				));
				$docid = $this->make_keys($ol->ids());
			}

			if ($obj->prop("use_target_audience") == 1)
			{
				if (is_array($obj->prop("select_target_audience")) && count($obj->prop("select_target_audience")))
				{
					$ta_list = new object_list();
					$ta_list->add($obj->prop("select_target_audience"));
				}
				else
				{
					// get all current target audiences
					$ta_list = new object_list(array(
						"class_id" => CL_TARGET_AUDIENCE,
						"ugroup" => aw_global_get("gidlist_oid")
					));
				}
				if ($ta_list->count() && count($docid))
				{
					$ol = new object_list(array(
						"class_id" => CL_DOCUMENT,
						"site_id" => aw_ini_get("site_id"),
						"lang_id" => AW_REQUEST_CT_LANG_ID,
						"oid" => $docid,
						"target_audience" => $ta_list->ids()
					));
					$docid = $this->make_keys($ol->ids());
				}
			}

			if (count($docid) == 1)
			{
				$docid = reset($docid);
			}
			return $docid;
		}

		if ($docid > 0)
		{
			$check = obj($docid);
			$ok = $check->class_id() == CL_DOCUMENT && $check->status() == object::STAT_ACTIVE;
			if (aw_ini_get("lang_menus") == 1)
			{
				$ok &= $check->lang() == AW_REQUEST_CT_LANG_CODE;
			}
			if (!$ok)
			{
				$docid = 0;
			}
		}

		aw_set_exec_time(AW_LONG_PROCESS);//FIXME: saidis sageli kasutatav meetod ei saa olla long process. uurida miks arvati et kaua v6tab

		$skipfirst = 0;

		$get_inact = false;
		$no_in_promo = false;

		if ($obj->class_id() == CL_PROMO)
		{
			$ndocs = $obj->prop("ndocs");
			$start_ndocs = $obj->prop("start_ndocs");
			if ($obj->prop("separate_pages"))
			{
				$cur_page = (int)$_GET["promo_".$obj->id()."_page"];
				$ndocs = ($cur_page+1) * $obj->prop("docs_per_page");
				$start_ndocs = $cur_page * $obj->prop("docs_per_page");
			}
		}
		elseif ($obj->class_id() == menu_obj::CLID)
		{
			$ndocs = $obj->prop("ndocs");
			$start_ndocs = 0;
		}
		else
		{
			$ndocs = $start_ndocs = 0;
		}

		$filt_lang_id = AW_REQUEST_CT_LANG_ID;
		$filter = array();
		// no default, show list
		if ($docid < 1)
		{
			if ($obj->prop("content_all_langs"))
			{
				$filt_lang_id = array();
			}

			if ($obj->class_id() == CL_PROMO && $obj->prop("docs_from_current_menu") && acl_base::can("view", menu_obj::get_active_section_id()))
			{
				$so = new object(menu_obj::get_active_section_id());
				$sections = array(
					$so->class_id() == menu_obj::CLID ? $so->id() : $so->parent()
				);
			}
			elseif (!empty($arr["include_submenus"]))
			{
				$ot = new object_tree(array(
					"class_id" => menu_obj::CLID,
					"site_id" => aw_ini_get("site_id"),
					"lang_id" => $filt_lang_id,
					"parent" => $obj->id(),
					"status" => array(STAT_NOTACTIVE, STAT_ACTIVE),
					"sort_by" => "objects.parent"
				));
				$sections += $this->make_keys($ot->ids());
			}
			elseif ($obj->class_id() == CL_PROMO)
			{
				if ($obj->prop("show_inact") == 1)
				{
					$get_inact = true;
				}
				$skipfirst = $start_ndocs;
				$lm = $obj->meta("last_menus");

				$lm = array();
				$ilm = array();

				if (isset($arr["dsdi_cache"]))
				{
					$ilm = safe_array(ifset($arr, "dsdi_cache", 6));
					$lm = safe_array(ifset($arr, "dsdi_cache", 2));
				}
				else
				{
					foreach($obj->connections_from(array("type" => array(6,2))) as $c)	// doc source, doc ignore
					{
						if ($c->prop("reltype") == 6)
						{
							$ilm[$c->prop("to")] = $c->prop("to");
						}
						else
						{
							$lm[$c->prop("to")] = $c->prop("to");
						}
					}
				}

				$lm_sub = $obj->meta("src_submenus");
				if (!empty($lm) && is_array($lm) && (!isset($lm[0]) || $lm[0] !== 0))
				{
					$sections = $lm;
					foreach($sections as $_sm)
					{
						if (!empty($lm_sub[$_sm]))
						{
							// include submenus in document sources
							$ot = new object_tree(array(
								"class_id" => menu_obj::CLID,
								"site_id" => aw_ini_get("site_id"),
								"lang_id" => AW_REQUEST_CT_LANG_ID,
								"parent" => $_sm,
								"status" => array(STAT_NOTACTIVE, STAT_ACTIVE),
								"sort_by" => "objects.parent"
							));
							$sections = $sections + $this->make_keys($ot->ids());
						}
					}
				}
				else
				{
					$sections = array($obj->id());
				}

				foreach($ilm as $ilm_item)	// ilm contains menus that the user wants not to get docs from
				{
					unset($sections[$ilm_item]);
				}

				$no_in_promo = 1;

				// get kws from promo
				if (acl_base::can("view", ifset($_GET, "set_kw")))
				{
					$filter["CL_DOCUMENT.RELTYPE_KEYWORD"] = $_GET["set_kw"];
				}
				elseif ($obj->prop("use_menu_keywords") && $this->sel_section_obj)
				{
					//$promo_kws = $this->sel_section_obj->connections_from(array("to.class_id" => CL_KEYWORD, "type" => "RELTYPE_KEYWORD"));
					$mi = get_instance(menu_obj::CLID);
					$kwns = $mi->get_menu_keywords($this->sel_section_obj->id());
				}
				else
				{
					if (isset($arr["dsdi_cache"]))
					{
						$kwns = safe_array(ifset($arr, "dsdi_cache", 5));
					}
					else
					{
						$promo_kws = $obj->connections_from(array("to.class_id" => CL_KEYWORD, "type" => "RELTYPE_KEYWORD"));
						$kwns = array();
						foreach($promo_kws as $promo_kw)
						{
							$kwns[] = $promo_kw->prop("to");
						}
					}
				}

				if (count($kwns))
				{
					// limit by objs with those kws
					$filter["CL_DOCUMENT.RELTYPE_KEYWORD"] = $kwns;
				}

				if ($obj->prop("use_doc_content_type") && $_SESSION["doc_content_type"])
				{
					$filter["doc_content_type"] = $_SESSION["doc_content_type"];
				}

			}
			else
			{
				$gm_subs = $obj->meta("section_include_submenus");
				$gm_c = $obj->connections_from(array(
					"type" => array("RELTYPE_DOCS_FROM_MENU","RELTYPE_NO_DOCS_FROM_MENU")
				));

				if (!empty($_SESSION["doc_content_type"]))
				{
					$filter["doc_content_type"] = $_SESSION["doc_content_type"];
				}

				foreach($gm_c as $gm)
				{
					if ($gm->prop("reltype") == 24)//XXX: ?
					{
						continue;
					}

					$gm_id = $gm->prop("to");
					$sections[$gm_id] = $gm_id;
					if ($gm_subs[$gm_id])
					{
						$ot = new object_tree(array(
							"class_id" => menu_obj::CLID,
							"site_id" => aw_ini_get("site_id"),
							"lang_id" => $filt_lang_id,
							"parent" => $gm_id,
							"status" => array(STAT_NOTACTIVE, STAT_ACTIVE),
							"sort_by" => "objects.parent"
						));
						$sections += $this->make_keys($ot->ids());
					}
				}

				$gm_subs = $obj->meta("section_no_include_submenus");
				foreach($gm_c as $gm)
				{
					if ($gm->prop("reltype") != 24)
					{
						continue;
					}
					$gm_id = $gm->prop("to");
					unset($sections[$gm_id]);
					if ($gm_subs[$gm_id])
					{
						$ot = new object_tree(array(
							"class_id" => menu_obj::CLID,
							"site_id" => aw_ini_get("site_id"),
							"lang_id" => $filt_lang_id,
							"parent" => $gm_id,
							"status" => array(STAT_NOTACTIVE, STAT_ACTIVE),
							"sort_by" => "objects.parent"
						));
						foreach($ot->ids() as $_id)
						{
							unset($sections[$_id]);
						}
					}
				}
			}

			if ($obj->meta("all_pers"))
			{
				$period_instance = new period();
				$periods = $this->make_keys(array_keys($period_instance->period_list(false)));
			}
			else
			{
				$periods = $obj->meta("pers");
			}

			$has_rand = false;

			if (isset($sections) && is_array($sections) && (!isset($sections[0]) || $sections[0] !== 0) && count($sections) > 0)
			{
				$nol = true;
				$filter["parent"] = $sections;
				if (aw_ini_get("config.use_last"))
				{
					$filter["no_last"] = new obj_predicate_not(1);
				}
			}
			else
			{
				$filter["parent"] = $obj->id();
			}

			if ($ndocs > 0)
			{
				$filter["limit"] = $ndocs;
			}
			if ($ndocs == -1)
			{
				$filter["oid"] = -1;
			}

			$docid = array();
			$cnt = 0;
			if (empty($ordby))
			{
				if ($obj->meta("sort_by"))
				{
					$ordby = $obj->meta("sort_by");
					if ($obj->meta("sort_by") === "RAND()")
					{
						$has_rand = true;
					}
					if ($obj->meta("sort_ord"))
					{
						$ordby .= " ".$obj->meta("sort_ord");
					}
					if ($obj->meta("sort_by") === "documents.modified")
					{
						$ordby .= ", objects.created DESC";
					}
				}
				else
				{
					$ordby = aw_ini_get("menuedit.document_list_order_by");
				}

				if ($obj->meta("sort_by2"))
				{
					if ($obj->meta("sort_by2") === "RAND()")
					{
						$has_rand = true;
					}

					$ordby .= ($ordby != "" ? " , " : " ").$obj->meta("sort_by2");
					if ($obj->meta("sort_ord2") != "")
					{
						$ordby .= " ".$obj->meta("sort_ord2");
					}

					if ($obj->meta("sort_by2") === "documents.modified")
					{
						$ordby .= ", objects.created DESC";
					}
				}

				if ($obj->meta("sort_by3"))
				{
					if ($obj->meta("sort_by3") === "RAND()")
					{
						$has_rand = true;
					}

					$ordby .= ($ordby != "" ? " , " : " ").$obj->meta("sort_by3");
					if ($obj->meta("sort_ord3") != "")
					{
						$ordby .= " ".$obj->meta("sort_ord3");
					}

					if ($obj->meta("sort_by3") === "documents.modified")
					{
						$ordby .= ", objects.created DESC";
					}
				}
			}

			if ($ordby == "")
			{
				$ordby = "objects.jrk";
			}

			$no_fp_document = aw_ini_get("menuedit.no_fp_document");

			if (strpos($ordby,"planner.start") !== false)
			{
				$filter[] = new object_list_filter(array(
					"non_filter_classes" => CL_DOCUMENT
				));
			}

			// if we are in full content trans, then we need to get all documents
			// that are either active in original lang OR active in the current tr lang
			if ($get_inact || aw_ini_get("user_interface.full_content_trans"))
			{
				$filter["status"] = array(STAT_ACTIVE,STAT_NOTACTIVE);
			}
			else
			{
				$filter["status"] = STAT_ACTIVE;
			}

			$filter["class_id"] = array(CL_DOCUMENT, CL_PERIODIC_SECTION, CL_BROTHER_DOCUMENT);

			if (!aw_ini_get("user_interface.full_content_trans") and empty($arr["all_langs"]))
			{
				$filter["lang_id"] = $filt_lang_id;
			}

			$filter["sort_by"] = $ordby;

			// if target audience is to be used, then limid docs by that
			if ($obj->is_a(menu_obj::CLID) and $obj->prop("use_target_audience") == 1)
			{
				// get all current target audiences
				$ta_list = new object_list(array(
					"class_id" => CL_TARGET_AUDIENCE,
					"ugroup" => aw_global_get("gidlist_oid")
				));
				if ($ta_list->count())
				{
					$filter["target_audience"] = $ta_list->ids();
				}
			}

			if ($no_in_promo)
			{
				$filter["no_show_in_promo"] = new obj_predicate_not(1);
			}

			if ($obj->is_a(CL_PROMO) and $obj->prop("auto_period") == 1)
			{
				$filter["period"] = aw_global_get("act_per_id");
			}

			if ($has_rand)
			{
				$noc_val = obj_set_opt("no_cache", 1);
			}

			if (isset($arr["date_filter"]) and is_array($arr["date_filter"]))
			{
				$df = $arr["date_filter"];
				if ($df["day"])
				{
					$s_tm = mktime(0, 0, 0, $df["month"], $df["day"], $df["year"]);
					$s_tm = ($s_tm === false ? -1 : $s_tm);
					$filter["doc_modified"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $s_tm-1, $s_tm+24*3600);
				}
				else
				if ($df["week"])
				{
					$s_tm = mktime(0, 0, 0, 1, 1, $df["year"]) + $df["week"] * 24*3600*7;
					$s_tm = $s_tm === false ? -1 : $s_tm;
					$filter["doc_modified"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $s_tm-1, $s_tm+24*3600*7);
				}
				elseif ($df["month"])
				{
					$s_tm = mktime(0, 0, 0, $df["month"], 1, $df["year"]);
					$e_tm = mktime(0, 0, 0, $df["month"]+1, 1, $df["year"]);
					$s_tm = $s_tm === false ? -1 : $s_tm;
					$e_tm = $e_tm === galse ? -1 : $e_tm;
					$filter["doc_modified"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $s_tm-1, $e_tm);
				}
				elseif ($df["year"])
				{
					$s_tm = mktime(0, 0, 0, 1, 1, $df["year"]);
					$e_tm = mktime(0, 0, 0, 1, 1, $df["year"]+1);
					$s_tm = $s_tm === false ? -1 : $s_tm;
					$e_tm = $e_tm === galse ? -1 : $e_tm;
					$filter["doc_modified"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $s_tm-1, $e_tm);
				}
			}

			if (isset($arr["periodic_content"]) && $arr["periodic_content"] == 1)
			{
				if (is_array($periods))
				{
					$filter["period"] = $periods;
				}
				else
				{
					$filter["period"] = aw_global_get("act_per_id");
				}
			}

			// what is going on here: we can't limit the list as the user said, if
			// we are going to flter the list later, because it will get less items as requested
			// so, if we are not filtering the list, limit the query, else just read the ids
			// and count the objects that should be added manually. it seems the best
			// possible saolution, although it reads too many objects that might be inactive, but it is the only way I see
			if (!$get_inact && aw_ini_get("user_interface.full_content_trans"))
			{
				unset($filter["limit"]);
				$documents = new object_list($filter);
				$nd = $ndocs;
				// filter the list for both-inactive docs
				$doc_ol = new object_list();
				$_tmp_cnt = 0;
				foreach($documents->ids() as $__doc_id)
				{
					$__doc_o = obj($__doc_id);
					if ($__doc_o->status() == STAT_ACTIVE || $__doc_o->meta("trans_".aw_global_get("ct_lang_id")."_status"))
					{
						$doc_ol->add($__doc_o);
						if ($nd > 0 && ++$_tmp_cnt >= $nd)
						{
							break;
						}
					}
				}
				$documents = $doc_ol;
				$filter[] = new object_list_filter(array(
					"non_filter_classes" => CL_DOCUMENT
				));
			}
			else
			{
				$documents = new object_list($filter);
			}

			if ($has_rand)
			{
				obj_set_opt("no_cache", $noc_val);
			}

			$rsid = aw_ini_get("site_id");

			$tc = 0;
			$done_oids = array();
			for ($o = $documents->begin(); !$documents->end(); $o = $documents->next())
			{
				if (!empty($done_oids[$o->brother_of()]))
				{
					continue;
				}

				$done_oids[$o->brother_of()] = 1;
				if ($o->site_id() != $rsid && !$o->is_brother() && !aw_ini_get("menuedit.objects_from_other_sites"))
				{
					continue;
				}

				if (aw_ini_get("user_interface.hide_untranslated") && !$o->prop_is_translated("title"))
				{
					continue;
				}

				if ($skipfirst > 0 && $tc < $skipfirst)
				{
					$tc++;
					continue;
				}

				//dokumentide mitte n2itamine yleliigsetest riikidest tulevatele p2ringutele
				if($o->class_id() == CL_DOCUMENT && !$o->is_visible_to())
				{
					return t("Ei ole &otilde;igust n&auml;ha");
				}

				if (!($no_fp_document && $o->prop("esilehel") == 1))
				{
					// oh. damn. this is sneaky. what if the brother is not active - we gits to check for that and if it is, then
					// use the brother
					if ($o->class_id() != CL_DOCUMENT && acl_base::can("view", $o->brother_of()))
					{
						$bo = obj($o->brother_of());
						if ($bo->status() != STAT_ACTIVE)
						{
							$docid[$cnt++] = $o->id();
						}
						else
						{
							$docid[$cnt++] = $o->brother_of();
						}
					}
					else
					{
						$docid[$cnt++] = $o->id();
					}
				}
			}

			if ($cnt > 1)
			{
				// a list of documents
				return $docid;
			}
			elseif ($cnt == 1)
			{
				// the correct id
				return $docid[0];
			}
			else
			{
				return false;
			}
		}
		return $docid;
	}

	function detect_country()
	{
		$ipl = new ip_locator();
		$v = $ipl->search(get_ip());
		if ($v == false)
		{
			$adr = inet::gethostbyaddr(get_ip());
			$domain = strtoupper(substr($adr, strrpos($adr, ".")));
			return $domain;
		}
		return $v["country_code2"];
	}

	function show_periodic_documents(&$arr)
	{
		if ($this->section_obj->class_id() == CL_PERIODIC_SECTION || $this->section_obj->class_id() == CL_DOCUMENT)
		{
			$docid = $this->section_obj->id();
		}
		else
		{
			$docid = $this->get_default_document(array(
				"periodic_content" => true,
				"obj" => $this->section_obj
			));
		}
		return isset($docid) ? $this->_int_show_documents($docid) : "";
	}

	function get_default_document_list()
	{
		return $this->default_document_list;
	}

	function _int_show_documents($docid)
	{
		$d = new document();
		$d->set_opt("parent", $this->sel_section);
		aw_register_default_class_member("document", "parent", $this->sel_section);
		$ct = "";


		// oleks vaja teha voimalus feedbacki tegemiseks. S.t. doku voib
		// lisaks enda sisule tekitada veel mingeid datat, mida siis menuedit
		// voiks paigutada saidi raami sisse. Related links .. voi nimekiri
		// mingitest artiklis esinevatest asjadest. You name it.
		$blocks = array();

		$section_id = $this->section_obj->id();
		$tplmgr = new templatemgr();
		if (is_array($docid))
		{
			$template = $tplmgr->get_lead_template($section_id);
			// I need to  know that for the public method menus
			// christ, this sucks ass, we really should put that somewhere else! - terryf
			$d->set_opt("cnt_documents",sizeof($docid));
			aw_register_default_class_member("document", "cnt_documents", sizeof($docid));

			$template = $template == "" ? "plain.tpl" : $template;
			$template2 = file_exists(aw_ini_get("site_tpldir")."automatweb/documents/".$template."2") ? $template."2" : $template;

			$this->vars(array("DOCUMENT_LIST" => $this->parse("DOCUMENT_LIST")));
			$this->_is_in_document_list = 1;


			$_numdocs = count($docid);
			$_curdoc = 1;
			$no_strip_lead = aw_ini_get("document.no_strip_lead");

			foreach($docid as $dk => $did)
			{
				// resets the template
				$d->_init_vars();
				aw_global_set("shown_document", $did);
				$ct .= $d->gen_preview(array(
					"docid" => $did,
					"tpl" => ($dk & 1 ? $template2 : $template),
					"leadonly" => 1,
					"section" => $section_id,
					"strip_img" => false,
					"keywords" => 1,
					"no_strip_lead" => $no_strip_lead,
					"not_last_in_list" => ($_curdoc < $_numdocs)
				));
				$_curdoc++;
			}
		}
		else
		{
			if (!empty($_GET["only_document_content"]) && $_GET["templ"] != "")
			{
				$template = $_GET["templ"];
			}
			else
			{
				$template = $tplmgr->get_long_template($section_id);
			}

			if ($docid)
			{
				// if full_content_trans is set, and this document is not translated
				// to the current ct lang and redirect is set, then do the redirect
				if (aw_ini_get("user_interface.full_content_trans") &&
					aw_ini_get("user_interface.ct_notact_redirect") != "")
				{
					$doc_o = obj($docid);
					if (aw_global_get("ct_lang_id") != $doc_o->lang_id() &&
						$doc_o->meta("trans_".aw_global_get("ct_lang_id")."_status") != 1)
					{
						header("Location: ".aw_ini_get("user_interface.ct_notact_redirect"));
						die();
					}
				}
				$this->active_doc = $docid;
				$d->set_opt("cnt_documents",1);
				aw_register_default_class_member("document", "cnt_documents", 1);
				$d->set_opt("shown_document",$docid);
				aw_register_default_class_member("document", "shown_document", $docid);
				aw_global_set("shown_document", $docid);

				$ct = $d->gen_preview(array(
					"docid" => $docid,
					"section" => $section_id,
					"no_strip_lead" => aw_ini_get("document.no_strip_lead"),
					"notitleimg" => 0,
					"tpl" => $template,
					"keywords" => 1,
					"boldlead" => aw_ini_get("document.boldlead"),
					"no_acl_checks" => aw_ini_get("menuedit.no_view_acl_checks"),
				));

				if ($d->no_left_pane)
				{
					$this->left_pane = false;
				}

				if ($d->no_right_pane)
				{
					$this->right_pane = false;
				}

				$this->vars(array(
					"docid" => $docid,
					"section" => $section_id,
				));
				$this->vars(array(
					"PRINTANDSEND" => $this->parse("PRINTANDSEND")
				));

				if ($d->title != "")
				{
					$this->site_title = $d->title;
				}

				if (is_array($d->blocks))
				{
					$blocks = $blocks + $d->blocks;
				}
			}
		}

		$vars = array();
		if ( (is_array($blocks)) && (sizeof($blocks) > 0) )
		{
			while(list(,$blockdata) = each($blocks))
			{
				$this->vars(array(
					"title" => $blockdata["title"],
					"content" => $blockdata["content"],
				));
				$vars[$blockdata["template"]] .= $this->parse($blockdata["template"]);
			}
		}
		$this->vars($vars);

		return $ct;
	}

	function show_documents(&$arr)
	{
		$p = array();
		if (!empty($_GET["year"]) || !empty($_GET["month"]) || !empty($_GET["day"]) || !empty($_GET["week"]))
		{
			$p = array("date_filter" => array(
				"year" => $_GET["year"],
				"month" => $_GET["month"],
				"day" => $_GET["day"],
				"week" => $_GET["week"]
			));
		}

		// Vaatame, kas selle sektsiooni jaoks on "default" dokument
		if (!isset($arr["docid"]) || $arr["docid"] < 1)
		{
			$docid = $this->get_default_document($p);
		}
		else
		{
			$docid = $arr["docid"];
		}
		$si = __get_site_instance();
		if (method_exists($si, "handle_default_document_list"))
		{
			$si->handle_default_document_list($docid);
		}

		$this->default_document_list = $docid;

		return $this->_int_show_documents($docid);
	}

	function do_show_documents(&$arr)
	{
		$disp = !empty($GLOBALS["real_no_menus"]) || !empty($_REQUEST["only_document_content"]) || ($this->sel_section_obj->prop("no_menus") == 1 || !empty($GLOBALS["print"]) || !empty($arr["content_only"]));

		if (!$disp)
		{
			$disp |= $this->template_has_var_full("doc_content");
		}

		if (!$disp)
		{
			return;
		}

		if ($this->sel_section_obj->prop("periodic") && $arr["text"] == "")
		{
			$docc = $this->show_periodic_documents($arr);
		}
		elseif ($arr["text"] == "")
		{
			$docc = $this->show_documents($arr);
		}
		else
		{
			$docc = $arr["text"];
		}

		if (aw_global_get("real_no_menus"))
		{
			die($docc);
		}

		if (!empty($_REQUEST["only_document_content"]))
		{
			$this->read_template("main_only_document_content.tpl");
			$this->vars(array(
				"doc_content" => $docc,
				"charset" => aw_global_get("charset")
			));
			$this->do_sub_callbacks(isset($arr["sub_callbacks"]) ? $arr["sub_callbacks"] : array());
			return $this->parse();
		}

		if ($this->sel_section_obj->prop("no_menus") == 1 || !empty($_GET["print"]) || !empty($arr["content_only"]))
		{
			if (aw_ini_get("menuedit.print_template"))
			{
				$this->read_template(aw_ini_get("menuedit.print_template"));
				$this->vars_safe(array(
					"doc_content" => $docc,
				));
				$this->make_yah();
				aw_global_set("no_cache", 1);

				return $this->parse();
			}
			return $docc;
		}

		if ($docc == "")
		{
			$this->vars(array(
				"empty_doc_add_menu" => $this->_get_empty_doc_menu()
			));
		}

		$this->vars_safe(array(
			"doc_content" => $docc
		));

		if ($docc != "")
		{
			$this->vars(array(
				"HAS_DOC_CONTENT" => $this->parse("HAS_DOC_CONTENT")
			));
		}
		else
		{
			$this->vars(array(
				"NO_DOC_CONTENT" => $this->parse("NO_DOC_CONTENT")
			));
		}
	}

	function do_menu_images()
	{
		$si_parent = $this->sel_section;
		$imgs = false;
		$smi = "";
		$sel_image = "";
		$sel_image_url = "";
		$sel_image_link = "";
		$sel_menu_o_img_url = "";


		if ($this->is_template("SEL_MENU_IMAGE") || $this->template_has_var_full("sel_menu_image", true))
		{
		$cnt = count($this->path);
		for($i = $cnt-1; $i > -1; $i--)
		{
			$o = $this->path[$i];
			if ($o->prop("images_from_menu"))
			{
				$o = obj($o->prop("images_from_menu"));
			}

			if (is_array($o->meta("menu_images")) && count($o->meta("menu_images")) > 0)
			{
				$imgs = true;
				break;
			}

			$img_act_url = "";
			if ($o->meta("img_act"))
			{
				$img_act_url = $this->image->get_url_by_id($o->meta("img_act"));
			}

			if ($img_act_url == "" && $o->meta("img_act_url") != "")
			{
				$img_act_url = $o->meta("img_act_url");
			}

			if ($img_act_url != "")
			{
				$sel_image_url = image::check_url($img_act_url);
				$sel_image = "<img name='sel_menu_image' src='".$sel_image_url."' border='0'>";
				$tmp = obj($o->meta("img_act"));
				$sel_image_link = $tmp->prop("link");
				$this->vars(array(
					"url" => $sel_image_url
				));
				$smi .= $this->parse("SEL_MENU_IMAGE");
				break;
			}
		}

		$sius = array();
		if ($imgs)
		{
			$imgar = $o->meta("menu_images");
			$imgsact = $o->meta("active_menu_images");//aktiivse menyy jaoks pilt
			foreach($imgsact as $nr => $dat)
			{
				if ($dat["image_id"])
				{
					$dat["url"] = $this->image->get_url_by_id($dat["image_id"]);
				}
				else
				{
					continue;
				}
				$this->vars(array(
					"sel_menu_active_image_".$nr => "<img src='".$dat["url"]."' alt='sel_menu_active_image_".$nr."' />",
					"sel_menu_active_image_".$nr."_url" => $dat["url"]
				));
			}
			$smi = "";
			foreach($imgar as $nr => $dat)
			{
				if ($dat["image_id"])
				{
					$dat["url"] = $this->image->get_url_by_id($dat["image_id"]);
				}

				if (empty($dat["url"]))
				{
					continue;
				}

				if ($smi == "")
				{
					$sel_image = "<img name='sel_menu_image' src='".image::check_url($dat["url"])."' border='0'>";
					$sel_image_url = $dat["url"];
					$tmp = obj($dat["image_id"]);
					$sel_image_link = $tmp->prop("link");
				}
				$this->vars(array(
					"url" => image::check_url($dat["url"])
				));
				$smi .= $this->parse("SEL_MENU_IMAGE");
				if ($dat["url"] != "")
				{
					$this->vars(array(
						"sel_menu_image_".$nr => "<img name='sel_menu_image_".$nr."' src='".$dat["url"]."' border='0'>",
						"sel_menu_image_".$nr."_url" => $dat["url"]
					));
				}
				$sius[$nr] = $dat["url"];
			}
		}
		}

		$smn = $this->sel_section_obj->trans_get_val("name");
		$smc = $this->sel_section_obj->comment();
		if (aw_ini_get("menuedit.strip_tags"))
		{
			$smn = strip_tags($smn);
		}

		$smn_nodoc = $smn;
		if ($this->active_doc)
		{
			$smn_nodoc = "";
		}

		$sel_menu_timing = 6;
		if ($imgs)
		{
			if ($this->sel_section_obj->meta("img_timing"))
			{
				$sel_menu_timing = $this->sel_section_obj->meta("img_timing");
			}
		}

		$this->vars(array(
			"SEL_MENU_IMAGE" => $smi,
			"sel_menu_name" => $smn,
			"sel_menu_name_no_doc" => $smn_nodoc,
			"sel_menu_image" => $sel_image,
			"sel_menu_image_url" => $sel_image_url,
			"sel_menu_image_link" => $sel_image_link,
			"sel_menu_comment" => $smc,
			"sel_menu_o_img_url" => $sel_menu_o_img_url,
			"sel_menu_timing" => $sel_menu_timing
		));

		if ($smc == "")
		{
			$this->vars(array(
				"HAS_SEL_MENU_COMMENT" => "",
				"NO_SEL_MENU_COMMENT" => $this->parse("NO_SEL_MENU_COMMENT")
			));
		}
		else
		{
			$this->vars(array(
				"HAS_SEL_MENU_COMMENT" => $this->parse("HAS_SEL_MENU_COMMENT"),
				"NO_SEL_MENU_COMMENT" => ""
			));
		}
		for($i = 0; $i < aw_ini_get("menu.num_menu_images"); $i++)
		{
			if (!empty($sius[$i]))
			{
				$this->vars(array(
					"HAS_SEL_MENU_IMAGE_URL_".($i) => $this->parse("HAS_SEL_MENU_IMAGE_URL_".($i))
				));
			}
			else
			{
				$this->vars(array(
					"NO_SEL_MENU_IMAGE_URL_".($i) => $this->parse("NO_SEL_MENU_IMAGE_URL_".($i))
				));
			}
		}

		$has_smu = $no_smu = "";
		if ($sel_image_url != "")
		{
			$has_smu = $this->parse("HAS_SEL_MENU_IMAGE_URL");
		}
		else
		{
			$no_smu = $this->parse("NO_SEL_MENU_IMAGE_URL");
		}
		$this->vars(array(
			"HAS_SEL_MENU_IMAGE_URL" => $has_smu,
			"NO_SEL_MENU_IMAGE_URL" => $no_smu
		));

		if ($this->is_template("HAS_SEL_MENU_IMAGE_URL") || $this->is_template("NO_SEL_MENU_IMAGE_URL"))
		{
		// menu img addon (sel_menu_image_skin_url)
		$ss = get_instance(CL_SITE_STYLES);
		$ol = new object_list(array(
			"class_id" => CL_SITE_STYLES,
			"status" => STAT_ACTIVE
		));
		$ar = $ol->arr();
		$style_ord = $ss->selected_style_ord(array(
			"oid" => key($ar)
		));
		$obj = obj(key($ar));
		$menu_img_nrs = $obj->meta("menupic_nrs");
		$menupic_nr = $menu_img_nrs[$style_ord];
		$menu_pic_final_id = false;
		foreach(array_reverse($this->path) as $menu)
		{
			$menu_obj = obj($menu);
			$loop_menu_pics = $menu_obj->prop("menu_images");
			if($loop_menu_pics[$menupic_nr]["image_id"])
			{
				$menu_pic_final_id = $loop_menu_pics[$menupic_nr]["image_id"];
				break;
			}
		}
		if($menu_pic_final_id)
		{
			$pic_obj = obj($menu_pic_final_id);
			$img_inst = get_instance(CL_IMAGE);
			$this->vars(array(
				"sel_menu_image_skin_url" => $img_inst->get_url($pic_obj->prop("file")),
			));
			$this->parse("HAS_SEL_MENU_IMAGE_URL");
		}
		else
		{
			$this->parse("NO_SEL_MENU_IMAGE_URL");
		}

		}
	}

	////
	// !build "you are here" links from the path
	function make_yah()
	{
		if(isset($this->load_template)) { $this->read_template("main.tpl"); }/// FIXME: marko taket dev-st saadud, 'load_template' ei esine mujal senises aw koodis, kontrollida yle kui kogu taket mergetud

		$path = $this->path;
		$ya = "";
		$cnt = count($path);

		$this->title_yah = "";
		$alias_path = array();

		// this is used to make sure path starts at rootmenu+1 levels, to not show
		// "left menu" or similar in path
		$show = false;

		$prev = false;
		$show_obj_tree = false;

		$rootmenu = aw_ini_get("rootmenu");
		if (aw_ini_get("ini_rootmenu"))
		{
			$rootmenu = aw_ini_get("ini_rootmenu");
		}

		$sfo = NULL;
		for ($i=0; $i < $cnt; $i++)
		{
			if (!aw_ini_get("menuedit.long_menu_aliases"))
			{
				$alias_path = array();
			}

			$ref = $path[$i];

			if ($ref->alias())
			{
				if (sizeof($alias_path) == 0)
				{
					$use_aliases = true;
				}

				if ($use_aliases)
				{
					array_push($alias_path,$ref->alias());
				}

				if ($use_aliases)
				{
					$linktext = join("/",$alias_path);
				}

				if (aw_ini_get("user_interface.full_content_trans"))
				{
					$link = aw_ini_get("baseurl").aw_global_get("ct_lang_lc")."/".$linktext;
				}
				else
				{
					$link = aw_ini_get("baseurl").$linktext;
				}
			}
			else
			{
				$use_aliases = false;
				$link = $this->make_menu_link($ref);
			}

			if ($ref->is_a(menu_obj::CLID) and $ref->prop("link"))
			{
				$link = $ref->prop("link");
			}

			if ($show_obj_tree)
			{
				$link = $ot_inst->get_yah_link($ot_id, $ref);
			}

			if (is_oid($sfo))
			{
				$sfo_o = obj($sfo);
				$sfo_i = $sfo_o->instance();
				if (method_exists($sfo_i, "make_menu_link"))
				{
					$link = $sfo_i->make_menu_link($ref, $sfo_o);
				}
				else
				{
					$link = $this->make_menu_link($ref, $sfo_o);
				}
			}

			if ($ref->is_a(menu_obj::CLID) && acl_base::can("view", $ref->prop("submenus_from_obj")))
			{
				$sfo = $ref->prop("submenus_from_obj");
			}

			// now. if the object in the path is marked to use site tree as
			// the displayer, then get the link from that
			if ($ref->is_a(menu_obj::CLID) && $ref->prop("show_object_tree"))
			{
				$show_obj_tree = true;
				$ot_inst = get_instance(CL_OBJECT_TREE);
				$ot_id = $ref->prop("show_object_tree");
			}

			$this->vars(array(
				"link" => $link,
				"text" => str_replace("&nbsp;"," ",strip_tags($ref->trans_get_val("name"))),
				"comment" =>  str_replace("&nbsp;"," ",strip_tags($ref->comment())),
				"ysection" => $ref->id(),
//				"end" => (!($i + 1 < $cnt)) ? $GLOBALS["yah_end"] : "",
			));

			$show_always = false;
			if ((($ref->is_a(menu_obj::CLID) && $ref->prop("clickable") == 1) || $ref->class_id() == CL_DOCUMENT || $ref->class_id() == CL_CRM_SECTOR) && $show && $ref->class_id() != CL_DOCUMENT)
			{
				if ($this->is_template("YAH_LINK_BEGIN") && $ya == "")
				{
					$ya .= $this->parse("YAH_LINK_BEGIN");
				}
				else
				if ($this->is_template("YAH_LINK_END") && $i == ($cnt-1))
				{
					$ya .= $this->parse("YAH_LINK_END");
				}
				else
				if ($this->is_template("YAH_LINK_REVERSE"))
				{
					$ya = $this->parse("YAH_LINK_REVERSE").$ya;
				}
				else
				{
					$ya .= $this->parse("YAH_LINK");//.(!($i + 1 < $cnt)) ? $GLOBALS["yah_end"] : "";
				}
				$this->title_yah.=" / ".str_replace("&nbsp;"," ",strip_tags($ref->trans_get_val("name")));
				$this->title_yah_arr[] = str_replace("&nbsp;"," ",strip_tags($ref->trans_get_val("name")));
			}

			if ($prev && $prev->id() == $rootmenu)
			{
				$show = true;
			}
			$prev = $ref;
		}

		// form table yah links get made here.
		// basically the session contains a vriable fg_table_sessions that has all the possible yah links for
		// all shown tables (and yeah, I know it is gonna be friggin huge.
		// and no, I can't remove the old ones, cause the user might have other windows open
		// and if I remove all the other ones from the array, he will lose the yah link in other windows
		if (!empty($GLOBALS["tbl_sk"]))
		{
			$tbld = aw_global_get("fg_table_sessions");
			$ar = new aw_array($tbld[$GLOBALS["tbl_sk"]]);
			foreach($ar->get() as $url)
			{
				preg_match_all("/restrict_search_yah\[\]=([^&$]*)/",$url,$mt);
				$this->vars(array(
					"link" => $url,
					"text" => urldecode($mt[1][count($mt[1])-1])
				));
				if (urldecode($mt[1][count($mt[1])-1]) != "")
				{
					$ya.=$this->parse("YAH_LINK");
				}
			}
		}

		if ($this->site_title == "")
		{
			$this->site_title = strip_tags($this->title_yah);
		}

		if (isset($GLOBALS["yah_end"]))
		{
			$ya.=$GLOBALS["yah_end"];
		}
		$this->vars(array(
			"PRINTANDSEND" => $this->parse("PRINTANDSEND")
		));
		$this->vars(array(
			"YAH_LINK" => $ya,
			"YAH_LINK_END" => "",
			"YAH_LINK_BEGIN" => "",
			"YAH_LINK_REVERSE" => ""
		));

		if ($ya != "")
		{
			$this->vars(array(
				"HAS_YAH" => $this->parse("HAS_YAH")
			));
		}
	}

	private function make_langs()
	{
		$lang_id = AW_REQUEST_CT_LANG_ID;
		$lar = languages::listall();
		$l = array();
		$uid = aw_global_get("uid");

		if (!($sel_lang = languages::fetch(AW_REQUEST_CT_LANG_ID)))
		{
			$sel_lang = languages::fetch(AW_REQUEST_CT_LANG_ID, true);
		}

		if (count($lar) < 2)
		{
			// crap, we need to insert the sel lang acharset here at least!
			$this->vars(array(
				"sel_charset" => languages::USER_CHARSET,
				"charset" => languages::USER_CHARSET,
				"se_lang_id" => AW_REQUEST_CT_LANG_ID,
				"lang_code" => AW_REQUEST_CT_LANG_CODE
			));
			return "";
		}

		$num = 0;
		foreach($lar as $row)
		{
			if (is_oid($row["oid"]) && !acl_base::can("view", $row["oid"]))
			{
				continue;
			}

			$lang_code = languages::lid2lc($row["id"]);

			$num++;
			$grp = isset($row["meta"]["lang_group"]) ? $row["meta"]["lang_group"] : null;
			$grp_spec = $grp;
			if ($grp != "")
			{
				$grp_spec = "_".$grp;
			}

			// if the language has an image
			$sel_img_url = ""; // image for when language is active
			$img_url = ""; // language image
			if (!empty($row["meta"]["lang_img"]))
			{
				if ($lang_id == $row["id"] && $row["meta"]["lang_img_act"])
				{
					$sel_img_url = $this->image->get_url_by_id($row["meta"]["lang_img_act"]);
				}

				$img_url = $this->image->get_url_by_id($row["meta"]["lang_img"]);
			}

			// get language change url
			if (aw_ini_get("menuedit.language_in_url"))
			{
				// make the url
				if (substr(automatweb::$request->arg("class"), 0, 4) === "shop")
				{
					$url = aw_url_change_var("section", "{$lang_code}/" . menu_obj::get_active_section_id());
				}
				else
				{
					// get the current url.
					// check if it has the language set in it
					// if it does, then replace it with the new one
					$cur_url = get_ru();
					$bits = parse_url($cur_url);
					$url = "";
					if (strlen($bits["path"]) > 1 && $bits["path"][0] === "/")
					{
						list($_lang_bit, $_rest) = explode("/", substr($bits["path"], 1), 2);
						if (AW_REQUEST_CT_LANG_CODE === $_lang_bit)
						{
							$new_path = "/{$lang_code}/{$_rest}";
							$url = str_replace($bits["path"], $new_path, $cur_url);
						}
					}

					if (!$url)
					{
						$url = $this->make_menu_link($this->section_obj, $lang_code);
					}
				}
			}
			elseif (!empty($row["meta"]["temp_redir_url"]) && $uid == "")
			{ //XXX: milleks?
				$url = $row["meta"]["temp_redir_url"];
			}
			else
			{ // link to languages module active language change method
				$url = core::mk_my_orb("set_active", array(
					"id" => $row["id"],
					"return_url" => get_ru() ? aw_ini_get("baseurl") : get_ru()
				), "languages");
			}

			$this->vars(array(
				"name" => $row["name"],
				"lang_id" => $row["id"],
				"lang_url" => $url,
				"lang_change_url" => aw_url_change_var("set_lang_id", $row["id"]),
				"link" => $url,
				"target" => "",
				"img_url" => $img_url,
				"sel_img_url" => $sel_img_url,
				"fp_text" => isset($row["meta"]["fp_text"]) ? $row["meta"]["fp_text"] : null
			));

			if (!isset($l[$grp]))
			{
				$l[$grp] = "";
			}

			if ($row["id"] == $lang_id)
			{
				if ($num == count($lar) && $this->is_template("SEL_LANG".$grp_spec."_END"))
				{
					$l[$grp].=$this->parse("SEL_LANG".$grp_spec."_END");
				}
				elseif ($this->is_template("SEL_LANG".$grp_spec."_BEGIN") && $l[$grp] == "")
				{
					$l[$grp].=$this->parse("SEL_LANG".$grp_spec."_BEGIN");
				}
				else
				{
					$l[$grp].=$this->parse("SEL_LANG".$grp_spec);
				}
				$sel_lang = $row;
				$this->vars(array(
					"sel_lang_img_url" => $img_url,
					"sel_lang_sel_img_url" => $sel_img_url,
				));
			}
			else
			{
				if ($num == count($lar) && $this->is_template("LANG".$grp_spec."_END"))
				{
					$l[$grp].=$this->parse("LANG".$grp_spec."_END");
				}
				elseif ($this->is_template("LANG".$grp_spec."_BEGIN") && $l[$grp] == "")
				{
					$l[$grp].=$this->parse("LANG".$grp_spec."_BEGIN");
				}
				else
				{
					$l[$grp].=$this->parse("LANG".$grp_spec);
				}
			}
		}

		foreach($l as $_grp => $_l)
		{
			$app = ($_grp != "" ? "_".$_grp : "");
			$this->vars_safe(array(
				"LANG".$app => $_l,
				"SEL_LANG".$app => "",
				"SEL_LANG".$app."_BEGIN" => "",
				"LANG".$app."_BEGIN" => ""
			));
		}

		$this->vars(array(
			"sel_charset" => languages::USER_CHARSET,
			"charset" => languages::USER_CHARSET,
			"se_lang_id" => AW_REQUEST_CT_LANG_ID,
			"lang_code" => AW_REQUEST_CT_LANG_CODE
		));
	}


	///////////////////////////////////////////////
	// template compiler runtime functions

	////
	// !finds the actual parent from the current path
	// based on the area parent and the menu level
	// this can assume that the level is in the path
	// because that is checked in OP_IF_VISIBLE
	// and we get here only if that returns true
	function _helper_find_parent($a_parent, $level)
	{
		if (!acl_base::can("view", $a_parent))
		{
			$a_parent = aw_ini_get("rootmenu");
		}

		if(acl_base::can("view" , $a_parent))
		{
			$parent_obj = obj($a_parent);
			if($parent_obj->class_id() == menu_obj::CLID && $parent_obj->prop("submenus_from_cb"))
			{
				return $a_parent;
			}
		}

		if ($level == 1)
		{
			return $a_parent;
		}

		$pos = array_search($a_parent, $this->path_ids);
		return isset($this->path_ids[$pos+($level-1)]) ? $this->path_ids[$pos+($level-1)] : 0;
	}

	function _helper_get_act_count($ol)
	{
		return $ol->count();
		$cnt = 0;
		foreach($ol->arr() as $item)
		{
			if ($item->status() == STAT_ACTIVE)
			{
				$cnt++;
			}
		}
		return $cnt;
	}

	function _helper_is_in_path($oid)
	{
		return (array_search($oid, $this->path_ids) !== false || array_search($oid, $this->path_brothers) !== false);
	}

	////
	// !returns the number of levels that are in the path
	// for the menu area beginning at $parent
	function _helper_get_levels_in_path_for_area($parent)
	{
		// why is this here you ask? well, if the user has no access to the area rootmenu
		// then the rootmenu will get rewritten to the group's rootmenu, therefore
		// we need to rewrite it in the path checker functions as well
		if (!acl_base::can("view", $parent))
		{
			$parent = aw_ini_get("rootmenu");
		}

		$pos = array_search($parent, $this->path_ids);

		//umm... peab miski valusa h2ki vahele kirjutama selle jaoks, kui menyyst v6etakse omadus, et tabid tuleks adminniliidese tabidest
		if(acl_base::can("view" , $parent))
		{
			$parent_obj = obj($parent);
			if($parent_obj->class_id() == menu_obj::CLID && $parent_obj->prop("submenus_from_cb"))
			{
				return 1;
			}
		}

		if ($pos === NULL || $pos === false)
		{
			return 0;
		}

		// now, the trick here is, of course that if the menu area parent is in the path
		// all the ones that follow, are also in that menu area so to return the level,
		// we just count the number of things in the path after the start pos of the menu area
		return count($this->path) - ($pos+1);
	}

	function _helper_get_login_menu_id()
	{
		if (!$this->current_login_menu_id)
		{
			if (aw_global_get("uid") == "")
			{
				$this->current_login_menu_id = array_search("LOGGED", aw_ini_get("menuedit.menu_defs"));
			}
			else
			{
				$cfg = get_instance(CL_CONFIG_LOGIN_MENUS);
				$_id = $cfg->get_login_menus();
				if ($_id > 0)
				{
					$this->current_login_menu_id = $_id;
				}
				else
				{
					$this->current_login_menu_id = array_search("LOGGED", safe_array(aw_ini_get("menuedit.menu_defs")));
				}
			}
		}
		return $this->current_login_menu_id;
	}

	function _helper_get_objlastmod()
	{
		static $last_mod;
		if (!$last_mod)
		{
			if (($last_mod = cache::file_get("objlastmod")) === false)
			{
				$add = "";
				if (aw_ini_get("site_show.objlastmod_only_menu"))
				{
					$add = " WHERE class_id = ".menu_obj::CLID;
				}
				$last_mod = $this->db_fetch_field("SELECT MAX(modified) as m FROM objects".$add, "m");
				cache::file_set("objlastmod", $last_mod);
			}
			// also compiled menu template
			$last_mod = max($last_mod, filemtime($this->compiled_filename));
		}
		return $last_mod;
	}

	function do_draw_menus($arr, $filename = NULL, $tpldir = NULL, $tpl = NULL)
	{
		if ($filename == NULL)
		{
			$filename = $this->compiled_filename;
		}
		else
		{
			$this->read_template("../../".$tpldir.$tpl,true);
		}

		if (!$filename)
		{
			error::raise(array(
				"id" => "ERR_NO_COMPILED",
				"msg" => t("site_show::do_draw_menus(): no compiled filename set!")
			));
		}

		// fake paths for default menus
		$path_bak = $this->path_ids;

		$menu_defaults = aw_ini_get("menuedit.menu_defaults");
		if (aw_ini_get("menuedit.lang_defs"))
		{
			$menu_defaults = $menu_defaults[AW_REQUEST_CT_LANG_ID];
		}

		if (is_array($menu_defaults) && menu_obj::get_active_section_id() == aw_ini_get("frontpage"))
		{
			foreach($menu_defaults as $_mar => $_mid)
			{
				if (acl_base::can("view", $_mid))
				{
					$tmp = obj($_mid);
					$this->path = $tmp->path();
					$this->path_ids = array();
					foreach($this->path as $p_obj)
					{
						$this->path_ids[] = $p_obj->id();
					}
				}
			}
		}

		if (file_exists($this->compiled_filename))
		{
			include_once($this->compiled_filename);
		}

		$this->path_ids = $path_bak;
		$this->do_seealso_items();


		if ($filename !== NULL)
		{
			return $this->parse();
		}
	}

	function exec_subtemplate_handlers($arr)
	{
		// go over all class defs and check if that class is the handler for any subtemplates
		$promo_done = false;
		$tmp = aw_ini_get("classes");
		foreach($tmp as $clid => $cldef)//TODO: seda k2ivitatakse igal pageview-l? optimeerida.
		{
			if (!empty($cldef["subtpl_handler"]))
			{
				$handler_for = explode(",", $cldef["subtpl_handler"]);
				$ask_content = array();
				foreach($handler_for as $tpl)
				{
					if ($this->is_template($tpl))
					{
						$ask_content[] = $tpl;
					}
				}

				if (count($ask_content) > 0)
				{
					$inst = get_instance($cldef["file"]);
					if ($cldef["file"] === "contentmgmt/promo_display")
					{
						$promo_done = true;
					}
					$fl = $cldef["file"];
					if (!$inst instanceof main_subtemplate_handler)
					{
						error::raise(array(
							"id" => ERR_NO_SUBTPL_HANDLER,
							"msg" => sprintf(t("site_show::exec_subtemplate_handlers(): could not find subtemplate handler in %s"), $cldef["file"])
						));
					}
					$inst->on_get_subtemplate_content(array(
						"inst" => $this,
						"content_for" => $ask_content,
						"request" => $_REQUEST
					));
				}
			}
		}

		if (!$promo_done)
		{
			// check if there are any promo templates, cause this call makes at least one query and this is faster
			// also, we don't need to check for the default promo templates
			// cause those are checked for earlier.
			$pa = aw_ini_get("promo.areas");
			if (is_array($pa) && count($pa) > 0)
			{
				$has_tpl = false;
				foreach($pa as $pid => $pd)
				{
					if ($this->is_template($pd["def"]."_PROMO"))
					{
						$has_tpl = true;
					}
				}

				if ($has_tpl)
				{
					$inst = new promo();
					$inst->on_get_subtemplate_content(array(
						"inst" => $this,
					));
				}
			}
		}
	}

	function is_in_path($s)
	{
		foreach($this->path as $o)
		{
			if ($o->id() == $s)
			{
				return true;
			}
		}
		return false;
	}

	function make_banners()
	{
		$banner_defs = aw_ini_get("menuedit.banners");

		if (!is_array($banner_defs))
		{
			return;
		}

		$banner_server = aw_ini_get("menuedit.banner_server");
		$uid = aw_global_get("uid");

		reset($banner_defs);
		while (list($name,$gid) = each($banner_defs))
		{
			$htmlf = $banner_server."banner".AW_FILE_EXT."?gid=$gid&ba_html=1";
			if ($uid != "")
			{
				$htmlf.="&aw_uid=".$uid;
			}
			// duhhh!! always check whether fopen succeeds!!
			$f = fopen($htmlf,"r");
			if ($f)
			{
				$fc = fread($f,100000);
				fclose($f);

				$fc = str_replace("[ss]","[ss".$gid."]",$fc);
			}

			$this->vars(array(
				"banner_".$name => $fc
			));
		}
	}

	function make_final_vars()
	{
		$section = $this->section_obj->id();
		$frontpage = aw_ini_get("frontpage");

		$islm = get_instance("site_loginmenu");
		$site_loginmenu = $islm->get_site_loginmenu($this);

		// site_title_rev - shows two levels in reverse order
		$pcnt = count($this->title_yah_arr);
		$site_title_rev = ($pcnt > 0 ? strip_tags($this->title_yah_arr[$pcnt-1])." / " : "").($pcnt > 1 ? strip_tags($this->title_yah_arr[$pcnt-2])." / " : "");
		$site_title_yah = " / ".join(" / ", safe_array($this->title_yah_arr));

		$adt = "";
		if (is_oid($this->active_doc) && acl_base::can("view", $this->active_doc))
		{
			$adt_o = obj($this->active_doc);
			$adt = $adt_o->trans_get_val("title");
		}

		$u = get_instance(CL_USER);
		$tmp = $u->get_current_person();
		if (is_oid($tmp))
		{
			$p = obj($tmp);
		}
		else
		{
			$p = obj();
		}

		$this->vars(array(
			"ss" => gen_uniq_id(),		// bannerite jaox
			"ss2" => gen_uniq_id(),
			"ss3" => gen_uniq_id(),
			"link" => "",
			"uid" => aw_global_get("uid"),
			"user" => $p->name(),
			"date" => $this->time2date(time(), 2),
			"date2" => $this->time2date(time(), 8),
			"date_timestamp" => time(),
			"date3" => date("j").". ".aw_locale::get_lc_month(date("n"))." ".date("Y"),
			"date4" => aw_locale::get_lc_weekday(date("w")).", ".get_lc_date(time(),LC_DATE_FORMAT_LONG_FULLYEAR),
			"date4_uc" => ucwords(aw_locale::get_lc_weekday(date("w"))).", ".get_lc_date(time(),LC_DATE_FORMAT_LONG_FULLYEAR),
			"date5" => date("j").". ".aw_locale::get_lc_month(date("n"))." ".date("Y"),
			"site_title" => strip_tags($this->site_title),
			"site_title_rev" => $site_title_rev,
			"site_title_yah" => $site_title_yah,
			"active_document_title" => $adt,
			"current_period" => aw_global_get("current_period"),
			"cur_section" => $this->section,
			"section_name" => $this->section_obj->name(),
			"meta_description" => $this->section_obj->is_property("description") ? $this->section_obj->trans_get_val("description") : "",
			"meta_keywords" => $this->properties["keywords"], //$this->section_obj->trans_get_val("keywords"), // hell i know if this is the right solution !?!
			"trans_lc" => aw_global_get("ct_lang_lc"),
			"site_loginmenu" => $site_loginmenu,
			"javascript" => active_page_data::get_javascript(),
			"javascript_bottom" => active_page_data::get_javascript("bottom"),
		));

		if ($this->_is_in_document_list)
		{
			$this->vars(array("DOCUMENT_LIST2" => $this->parse("DOCUMENT_LIST2")));
			$this->vars(array("DOCUMENT_LIST3" => $this->parse("DOCUMENT_LIST3")));
		}

		if ($this->is_parent_tpl("logged", "IS_NOT_FRONTPAGE"))
		{
			if (aw_global_get("uid") != "")
			{
				$this->vars(array(
					"logged" => $this->parse("logged")
				));
			}
		}

		// insert sel images
		if ($this->template_has_var_full("path_menu_image", true))
		{
			if (count(safe_array($this->properties["images"])))
			{
				$ol = new object_list(array(
					"oid" => safe_array($this->properties["images"])
				));
				$ol->arr();	// preload at once
			}
			foreach(safe_array($this->properties["images"]) as $nr => $id)
			{
				$url = $this->image->get_url_by_id($id);
				$this->vars(array(
					"path_menu_image_".$nr."_url" => $url,
					"path_menu_image_".$nr => html::img(array(
						"url" => $url,
						"alt" => " ",
						"title" => " "
					))
				));
			}
		}

		$isfp = $section == $frontpage && empty($_GET["class"]);
		$this->vars_safe(array(
			"IS_FRONTPAGE" => ($isfp ? $this->parse("IS_FRONTPAGE") : ""),
			"IS_FRONTPAGE2" => ($isfp ? $this->parse("IS_FRONTPAGE2") : ""),
			"IS_FRONTPAGE3" => ($isfp ? $this->parse("IS_FRONTPAGE3") : ""),
			"IS_FRONTPAGE4" => ($isfp ? $this->parse("IS_FRONTPAGE4") : ""),
			"IS_NOT_FRONTPAGE" => (!$isfp ? $this->parse("IS_NOT_FRONTPAGE") : ""),
			"IS_NOT_FRONTPAGE2" => (!$isfp ? $this->parse("IS_NOT_FRONTPAGE2") : ""),
			"IS_NOT_FRONTPAGE3" => (!$isfp ? $this->parse("IS_NOT_FRONTPAGE3") : ""),
			"IS_NOT_FRONTPAGE4" => (!$isfp ? $this->parse("IS_NOT_FRONTPAGE4") : ""),
			"POPUP_MENUS_SITE" => cache::file_get("aw_toolbars") // toolbar menu button menuitem layer
		));
		cache::file_set("aw_toolbars", "");


		if (aw_global_get("uid") == "")
		{
			$this->vars(array(
				"login" => $this->parse("login"),
				"login2" => $this->parse("login2"),
				"login3" => $this->parse("login3"),
				"logged" => "",
				"logged2" => "",
				"logged3" => "",
			));
			$cd_n = "";
			if ($this->active_doc)
			{
				$cd_n = $this->parse("CHANGEDOCUMENT_NOLOGIN");
			}
			$this->vars(array(
				"CHANGEDOCUMENT_NOLOGIN" => $cd_n
			));
		}
		else
		{
			if ($this->is_template("JOIN_FORM"))
			{
				error::raise(array(
					"id" => "ERR_JF",
					"msg" => t("site_show::make_final_vars(): need JOIN_FORM sub back!")
				));
			}

			$cd = $cd2 = "";
			if (acl_base::can("edit",$section) && $this->active_doc)
			{
				$cd = $this->parse("CHANGEDOCUMENT");
				$cd2 = $this->parse("CHANGEDOCUMENT2");
			};
			$this->vars(array(
				"CHANGEDOCUMENT" => $cd,
				"CHANGEDOCUMENT2" => $cd2,
			));

			$cd = "";
			if (acl_base::can("add",$section))
			{
				$cd = $this->parse("ADDDOCUMENT");
			};
			$this->vars(array(
				"ADDDOCUMENT" => $cd,
			));

			// check if template exists, cause prog_acl makes some queries
			if ($this->is_template("MENUEDIT_ACCESS"))
			{
				// check menuedit access
				if (acl_base::prog_acl("view", PRG_MENUEDIT))
				{
					// so if this is the only document shown and the user has edit right
					// to it, parse and show the CHANGEDOCUMENT sub
					$this->vars(array("MENUEDIT_ACCESS" => $this->parse("MENUEDIT_ACCESS")));
				}
				else
				{
					$this->vars(array("MENUEDIT_ACCESS" => ""));
				}
			}

			// god dammit, this sucks. aga ma ei oska seda kuidagi teisiti lahendada
			// konkreetselt sonnenjetis on logged LEFT_PANE sees,
			// www.kirjastus.ee-s on LEFT_PANE logged-i sees.
			$lp = "";
			$rp = "";

			$this->vars_safe(array(
				"logged" => $this->parse("logged"),
				"logged1" => $this->parse("logged1"),
				"logged2" => $this->parse("logged2"),
				"logged3" => $this->parse("logged3"),
				"login" => "",
			));
		};

		if ($this->left_pane)
		{
			$lp = $this->parse("LEFT_PANE");
		}
		else
		{
			$lp = $this->parse("NO_LEFT_PANE");
		}
		if ($this->right_pane)
		{
			$rp = $this->parse("RIGHT_PANE");
		}
		else
		{
			$rp = $this->parse("NO_RIGHT_PANE");
		}

		$this->vars_safe(array(
			"LEFT_PANE" => $lp,
			"RIGHT_PANE" => $rp,
			"NO_LEFT_PANE" => "",
			"NO_RIGHT_PANE" => "",
		));

		// check if logged is outside LEFT_PANE and if it is, then parse logged again if we are logged in
		if ($this->is_parent_tpl("LEFT_PANE", "logged") && aw_global_get("uid") != "")
		{
			$this->vars_safe(array(
				"logged" => $this->parse("logged")
			));
		}

		if ($this->is_parent_tpl("RIGHT_PANE", "logged") && aw_global_get("uid") != "")
		{
			$this->vars_safe(array(
				"logged" => $this->parse("logged")
			));
		}

		if ($this->active_doc)
		{
			$this->vars(array(
				"HAS_ACTIVE_DOC" => $this->parse("HAS_ACTIVE_DOC")
			));
		}
	}

	// builds HTML popups
	function build_popups()
	{
		if (true || $_GET["print"] == 1)
		{
			return;
		}
		// that sucks. We really need to rewrite that
		// I mean we always read information about _all_ the popups
		$pl = new object_list(array(
			"status" => STAT_ACTIVE,
			"class_id" => CL_HTML_POPUP
		));
		if (count($pl->ids()) > 0)
		{
			$t = get_instance(CL_HTML_POPUP);
		};
		foreach($pl->arr() as $o)
		{
			$o_id = $o->id();
			if ($o->prop("only_once") && $_SESSION["popups_shown"][$o_id] == 1)
			{
				continue;
			}

			$sh = false;
			foreach($o->connections_from(array("type" => "RELTYPE_FOLDER")) as $c)
			{
				if ($c->prop("to") == $this->sel_section)
				{
					//$popups .= sprintf("window.open('%s','htpopup','top=0,left=0,toolbar=0,location=0,menubar=0,scrollbars=0,width=%s,height=%s');", $url, $o->meta("width"), $o->meta("height"));
					$popups .= $t->get_popup_data($o);
					$sh = true;
					$_SESSION["popups_shown"][$o_id] = 1;
				}
			}

			$inc_submenus = $o->meta("section_include_submenus");

			if (!$sh && is_array($inc_submenus) && count($inc_submenus) > 0)
			{
				$path = obj($this->sel_section);
				$path = $path->path();

				foreach($path as $p_o)
				{
					if ($inc_submenus[$p_o->parent()])
					{
						//$popups .= sprintf("window.open('%s','htpopup','top=0,left=0,toolbar=0,location=0,menubar=0,scrollbars=0,width=%s,height=%s');", $url, $o->meta("width"), $o->meta("height"));
						$popups .= $t->get_popup_data($o);
						$_SESSION["popups_shown"][$o_id] = 1;
					}
				}
			}
		}
		return $popups;
	}

	////
	// !Creates a link for the menu
	function make_menu_link($o, $lc = null)
	{
		$this->skip = false;
		$link = "";
		$link_str = $o->is_property("link") ? $o->trans_get_val("link") : "";
		if (acl_base::can("view", $o->meta("linked_obj")) && $o->meta("linked_obj") != $o->id())
		{
			$linked_obj = obj($o->meta("linked_obj"));
			if ($linked_obj->class_id() == menu_obj::CLID)
			{
				$link_str = $this->make_menu_link($linked_obj);
			}
			else
			{
				$dd = new doc_display();
				$link_str = $dd->get_doc_link($linked_obj);
			}
		}

		if ($o->is_a(menu_obj::CLID) and $o->prop("type") == MN_PMETHOD)
		{
			// I should retrieve orb definitions for the requested class
			// to figure out which arguments it needs and then provide
			// those
			$pclass = $o->meta("pclass");
			if ($pclass)
			{
				list($_cl,$_act) = explode("/",$pclass);
				$orb = new orb();
				if ($_cl === "periods")
				{
					$_cl = "period";
				};
				$pobject = $o->meta("pobject");
				$pgroup = $o->meta("pgroup");
				$meth = $orb->get_public_method(array(
					"id" => $_cl,
					"action" => $_act,
					"obj" => (!empty($pobject) ? $pobject : false),
					"pgroup" =>  (!empty($pgroup) ? $pgroup : false),
				));

				// check acl
				if ($_act === "new" && isset($meth["values"]["parent"]) && acl_base::can("add", $meth["values"]["parent"]))
				{
					$this->skip = true;
				}

				if ($_act == "change" && isset($meth["values"]["id"]) && !acl_base::can("edit", $meth["values"]["id"]))
				{
					$this->skip = true;
				}

				$values = array();
				$err = false;
				if ($_act === "change")
				{
					$meth["required"]["id"] = "required";
				}
				$mv = new aw_array($meth["values"]);
				$mr = new aw_array($meth["required"]);
				foreach($mr->get() as $key => $val)
				{
					if (in_array($key,array_keys($mv->get())))
					{
						$values[$key] = $meth["values"][$key];
					}
					else
					{
						$err = true;
					}
				}

				$mo = new aw_array($meth["optional"]);
				foreach($mo->get() as $key => $val)
				{
					if (in_array($key,array_keys($mv->get())))
					{
						$values[$key] = $meth["values"][$key];
					}
				}

				if ($_cl === "menu")
				{
					$values["parent"] = $this->section;
					if ($_act === "change")
					{
						$values["id"] = $this->sel_section_obj->id();
					}
				}

				if (not($err))
				{
					if (!empty($_REQUEST["section"]))
					{
						$values["section"] = $_REQUEST["section"];
					}
					$link = $this->mk_my_orb($_act,$values,($_cl === "document" ? "doc" : $_cl),$o->meta("pm_url_admin"),!$o->meta("pm_url_menus"));
				}
				else
				{
					$this->skip = true;
				}

				if ($o->meta("pm_extra_params") != "")
				{
					ob_start();
					eval("?>".$o->meta("pm_extra_params"));//XXX: what is this?
					$link .= ob_get_contents();
					ob_end_clean();
				}
			}
			else
			{
				$link = "";
			}
		}
		elseif (!$this->brother_level_from && $link_str != "")
		{
			$link = $link_str;
			if (is_numeric($link)) // link is without preceding /
			{
				$link = obj_link($link);
			}
		}
		else
		{
			if ($lc === null)
			{
				$lc = AW_REQUEST_CT_LANG_CODE;
				$use_trans = true;
			}
			else
			{
				$use_trans = false;
			}

			$link = aw_ini_get("baseurl");
			if (aw_ini_get("menuedit.language_in_url"))
			{
				$link .= $lc."/";
			}

			if (aw_ini_get("menuedit.long_section_url"))
			{
				if (($use_trans ? $o->trans_get_val("alias") : $o->alias()) != "")
				{
					$link .= ($use_trans ? $o->trans_get_val("alias") : $o->alias());
				}
				else
				{
					if (aw_ini_get("menuedit.show_real_location"))
					{
						$link .= "index".AW_FILE_EXT."?section=".$o->brother_of().$this->add_url;
					}
					else
					{
						$link .= "index".AW_FILE_EXT."?section=".$o->id().$this->add_url;
					}
				}
			}
			else
			{
//				if (((!$this->brother_level_from && !$o->is_brother()) || aw_ini_get("menuedit.show_real_location"))&& ($use_trans ? $o->trans_get_val("alias") : $o->alias()) != "")
				if (!$this->brother_level_from && !$o->is_brother() && ($use_trans ? $o->trans_get_val("alias") : $o->alias()) != "")
				{
					if (aw_ini_get("menuedit.long_menu_aliases"))
					{
						if (aw_ini_get("ini_rootmenu"))
						{
							$tmp = aw_ini_get("rootmenu");
							aw_ini_set("rootmenu", aw_ini_get("ini_rootmenu"));
						}
						$_p = $o->path();

						if (aw_ini_get("ini_rootmenu"))
						{
							aw_ini_set("rootmenu", $tmp);
						}
						$alp = array();
						foreach($_p as $p_o)
						{
							if (($use_trans ? $p_o->trans_get_val("alias") : $p_o->alias()) != "")
							{
								$alp[] = str_replace("%2F", "/", urlencode(($use_trans ? $p_o->trans_get_val("alias") : $p_o->alias())));
							}
						}

						$link .= join("/",$alp);
						if (isset($tmp) && sizeof($tmp) > 0)
						{
							$link .= "/";
						}
					}
					else
					{
						//the alias seems to consist only of a space or two
						//i'm gonna show the id, if the strlen(trim(alias)) is 0
						if(!strlen(trim(($use_trans ? $o->trans_get_val("alias") : $o->alias()))))
						{
							$link .= $o->id();
						}
						else
						{
							$link .= str_replace("%2F", "/", urlencode(($use_trans ? $o->trans_get_val("alias") : $o->alias())));
						}
					}
				}
				else
				{
					if (($o->is_brother() || $this->brother_level_from) /*&& !aw_ini_get("menuedit.show_real_location")*/)
					{
						$link .= "?section=".$o->id()."&path=".join(",", $this->_cur_menu_path);
					}
					else
					{
						$oid = ($o->class_id() == 39 || aw_ini_get("menuedit.show_real_location")) ? $o->brother_of() : $o->id();
						$link .= $oid;
					}
				}
			}
		}

		if ($o->is_property("set_doc_content_type") and acl_base::can("view", $o->prop("set_doc_content_type")))
		{
			$so = new object(menu_obj::get_active_section_id());
			$su = ($so->is_frontpage() || $so->class_id() == CL_DOCUMENT  ? $link : aw_global_get("REQUEST_URI"));
			$su = aw_url_change_var("clear_doc_content_type", null, $su);
			$su = aw_url_change_var("docid", null, $su);
			$link = aw_url_change_var("set_doc_content_type", $o->prop("set_doc_content_type"), $su);
		}

		return $link;
	}

	////
	// !returns the file where the generated code for the template is, if it is in the cache
	function get_cached_compiled_filename($arr)
	{
		$tpl = $arr["tpldir"].$arr["template"];

		$what_to_replace = array('/','.','\\',':');
		$str_part = str_replace($what_to_replace, '_', $tpl);

		$fn = cache::get_fqfn("compiled_menu_template-".$str_part."-".AW_REQUEST_CT_LANG_ID);

		if ("unix" === aw_ini_get("server.platform") and $tpl{0} !== "/" or "win32" === aw_ini_get("server.platform") and substr($tpl, 1, 2) !== ":/")
		{
			$tpl = aw_ini_get("site_basedir").$tpl;
		}

		if (file_exists($fn) && is_readable($fn) && filectime($fn) > filectime($tpl))
		{
			return $fn;
		}
		return false;
	}

	////
	// !compiles the template and saves the code in a cache file, returns the cache file
	function cache_compile_template($path, $tpl, $mdefs = NULL, $no_cache = false)
	{
		$co = new site_template_compiler();
		$code = $co->compile($path, $tpl, $mdefs, $no_cache);
		$tpl = $path.$tpl;

		$what_to_replace = array("\\","/",".",":");
		$str_part = str_replace($what_to_replace, "_", $tpl);
		$fn = "compiled_menu_template-".$str_part."-".aw_global_get('lang_id');

		cache::file_set($fn, $code);
		return cache::get_fqfn($fn);
	}

	function do_show_template($arr)
	{
		$tpldir = str_replace(aw_ini_get("site_basedir"), "", aw_ini_get("site_tpldir"));
		$tpldir = str_replace(aw_ini_get("site_basedir"), "", $tpldir)."/automatweb/menuedit/";

		if (!empty($arr["tpldir"]))
		{
			$this->tpl_init(sprintf("../%s/automatweb/menuedit/",$arr["tpldir"]));
			$tpldir = $arr["tpldir"]."automatweb/menuedit/";
		}

		$arr["tpldir"] = $tpldir;
		// right. now, do the template compiler bit

		if (!($this->compiled_filename = $this->get_cached_compiled_filename($arr)))
		{
			$this->compiled_filename = $this->cache_compile_template($tpldir, $arr["template"]);
		}
		$this->read_template($arr["template"]);

		$this->do_sub_callbacks(isset($arr["sub_callbacks"]) ? $arr["sub_callbacks"] : array());

		if ($docc = $this->do_show_documents($arr))
		{
			return $docc;
		}
		$this->import_class_vars($arr);

		// here we must find the menu image, if it is not specified for this menu,
		//then use the parent's and so on.
		$this->do_menu_images();
		$this->make_yah();

		$this->make_langs();

		minify_js_and_css::parse_site_header($this);

		// execute menu drawing code
		$this->do_draw_menus($arr);
		// repeated here, so you can use things both ways
		$this->do_menu_images();
		$this->do_sub_callbacks(isset($arr["sub_callbacks"]) ? $arr["sub_callbacks"] : array(), true);
		$this->exec_subtemplate_handlers($arr);
		$this->make_banners();
		$this->make_final_vars();

		$rv = $this->parse();

		$rv .= $this->build_popups();
		return $rv;
	}

	function no_ip_access_redir($o)
	{
		die(t("Sellelt aadressilt pole lubatud seda lehte vaadata, vabandame.<br>Aadress: ".aw_global_get("REMOTE_ADDR")."<br>Leht: ".$o));
	}

	function _init_path_vars(&$arr)
	{
		$this->section = menu_obj::get_active_section_id();
		if (acl_base::can("view", $this->section))
		{
			$this->section_obj = new object($this->section);
		}
		else
		{
			$this->section_obj = new object();
			$this->section_obj->set_class_id(menu_obj::CLID);
		}

		if (aw_ini_isset("classes." . $this->section_obj->class_id()) && !automatweb::$request->arg("class"))
		{
			if ($this->section_obj->class_id() != menu_obj::CLID) // menu is a large class and this is what it is 99% of the time and it has no handler. so don't load
			{
				$obj_inst = $this->section_obj->instance();
				if (method_exists($obj_inst, "request_execute"))
				{
					$arr["text"] = $obj_inst->request_execute($this->section_obj);
				}
			}
		}

		if ($this->section_obj->is_property("get_content_from"))
		{
			$content_from_obj = $this->section_obj->prop("get_content_from");
			if (acl_base::can("view", $content_from_obj))
			{
				$content_obj = obj($content_from_obj);
				$content_obj_inst = $content_obj->instance();
				if (method_exists($content_obj_inst, "request_execute"))
				{
					$arr["text"] = $content_obj_inst->request_execute($content_obj);
				}
			}
		}

		// until we can have class-static variables, this actually SETS current text content
		$apd = new active_page_data();
		$apd->get_text_content(isset($arr["text"]) ? $arr["text"] : "");

		// save path
		// get path from the real rootmenu so we catch props?
		if (aw_ini_get("ini_rootmenu"))
		{
			$tmp = aw_ini_get("rootmenu");
			aw_ini_set("rootmenu", aw_ini_get("ini_rootmenu"));
		}

		//if (is_object($this->section_obj))
		if (automatweb::$request->arg("path"))
		{
			$p_ids = explode(",", automatweb::$request->arg("path"));
			$this->path = array();
			foreach($p_ids as $p_id)
			{
				if (acl_base::can("view", $p_id))
				{
					$this->path[] = obj($p_id);
				}
			}
		}
		elseif (is_oid($this->section_obj->id()))
		{
			$this->path = $this->section_obj->path();
		}
		else
		{
			$this->path = array();
		}

		if (aw_ini_get("ini_rootmenu"))
		{
			aw_ini_set("rootmenu", $tmp);
		}

		$pfp = aw_ini_get("shop.prod_fld_path");

		$this->path_ids = array();
		foreach($this->path as $p_obj)
		{
			$this->path_ids[] = $p_obj->id();
			if ($pfp && $p_obj->id() == $pfp && !$_REQUEST["class"] && $this->section_obj->class_id() != CL_DOCUMENT)
			{
				// uh-oh. we are in shop menu but not in shop mode. redirect
				$url = $this->mk_my_orb("show_items", array("section" => menu_obj::get_active_section_id(), "id" => aw_ini_get("shop.prod_fld_path_oc")), "shop_order_center");
				header("Location: $url");
				die();
			}
		}
	}

	/** compiles and displays a template containing menu subs

		@comment

			parameters:
				template required - template with path, example: contentmgmt/foo/blah.tpl
				mdefs - optional -	if set, defines new menu areas for the template compiler.
									format is the same as in the ini file

	**/
	function do_show_menu_template($arr)
	{
		extract($arr);
		if (!isset($mdefs))
		{
			$mdefs = NULL;
		}
		$this->_init_path_vars($arr);
		$tpl_dir = dirname($template) . "/";
		$tpl_fn = basename($template);

		$cname = $this->cache_compile_template($tpl_dir, $tpl_fn, $mdefs, true);
		$tmp = $this->do_draw_menus(array(), $cname, $tpl_dir, $tpl_fn);
		return $tmp;
	}

	function do_seealso_items()
	{
		foreach(safe_array(aw_ini_get("menuedit.menu_defs")) as $id => $_name)
		{
			if (!acl_base::can("view", $id))
			{
				continue;
			}

			foreach(explode(",", $_name) as $name)
			{
				$tmp = array();
				$subtpl = "MENU_${name}_SEEALSO_ITEM";
				if (!$this->is_template($subtpl))
				{
					continue;
				}

				$o = obj($id);
				foreach($o->connections_to(array("type" => 5, "to.lang_id" => AW_REQUEST_CT_LANG_ID)) as $c)
				{
					$samenu = $c->from();
					if ($samenu->status() != STAT_ACTIVE || $samenu->lang_id() != AW_REQUEST_CT_LANG_ID)
					{
						continue;
					}

					$link = $this->make_menu_link($samenu);
					$ord = (int)$samenu->meta("seealso_order");

					// the jrk number is in $samenu["meta"]["seealso_order"]

					if (!($samenu->meta("users_only") == 1 && aw_global_get("uid") == ""))
					{
						$this->vars(array(
							"target" => $samenu->prop("target") ? "target=\"_blank\"" : "",
							"link" => $link,
							"text" => str_replace("&nbsp;", " ", $samenu->name()),
						));
						$tmp[$ord] .= $this->parse($subtpl);
					}
				}

				// make sure, they are in correct order
				ksort($tmp);
				$this->vars(array(
					$subtpl => join("",$tmp),
				));
			}
		}
	}

	function __helper_menu_edit($menu)
	{
		if (!acl_base::prog_acl() || !empty($_SESSION["no_display_site_editing"]))
		{
			return;
		}
		if (!acl_base::can("admin", $menu->id()) &&
			!acl_base::can("add", $menu->id()) &&
			!acl_base::can("edit", $menu->id())
		)
		{
			return;
		}
		$pm = new popup_menu();
		$pm->begin_menu("site_edit_".$menu->id());
		if (acl_base::can("add", $menu->parent()))
		{
			$url = $this->mk_my_orb("new", array("parent" => $menu->parent(), "ord_after" => $menu->id(), "return_url" => get_ru(), "is_sa" => 1), menu_obj::CLID, true);
			$pm->add_item(array(
				"text" => t("Lisa uus k&otilde;rvale"),
				"onclick" => "aw_popup_scroll(\"{$url}\", \"aw_doc_edit\", 600, 400);",
				"link" => "javascript:void(0)"
			));
		}

		if (acl_base::can("add", $menu->id()))
		{
			$url = $this->mk_my_orb("new", array("parent" => $menu->id(), "ord_after" => $menu->id(), "return_url" => get_ru(), "is_sa" => 1), menu_obj::CLID, true);
			$pm->add_item(array(
				"text" => t("Lisa uus alamkaust"),
				"onclick" => "aw_popup_scroll(\"{$url}\", \"aw_doc_edit\", 600, 400);",
				"link" => "javascript:void(0)"
			));
		}

		if (acl_base::can("change", $menu->id()))
		{
			$url = $this->mk_my_orb("change", array("id" => $menu->id(), "return_url" => get_ru(), "is_sa" => 1), menu_obj::CLID, true);
			$pm->add_item(array(
				"text" => t("Muuda"),
				"onclick" => "aw_popup_scroll(\"{$url}\", \"aw_doc_edit\", 600, 400);",
				"link" => "javascript:void(0)"
			));
		}

		if (acl_base::can("admin", $menu->id()))
		{
			$url = $this->mk_my_orb("disp_manager", array("id" => $menu->id()), "acl_manager", true);
			$pm->add_item(array(
				"text" => t("Muuda &otilde;igusi"),
				"onclick" => "aw_popup_scroll(\"{$url}\", \"aw_doc_edit\", 800, 500);",
				"link" => "javascript:void(0)"
			));
		}

		if (acl_base::can("edit", $menu->id()))
		{
			$pm->add_item(array(
				"text" => t("Peida"),
				"link" => $this->mk_my_orb("hide_menu", array("id" => $menu->id(), "ru" => get_ru()), "menu_site_admin")
			));
			$pm->add_item(array(
				"text" => t("L&otilde;ika"),
				"link" => $this->mk_my_orb("cut_menu", array("id" => $menu->id(), "ru" => get_ru()), "menu_site_admin")
			));
		}

		if (isset($_SESSION["site_admin"]["cut_menu"]) && acl_base::can("view", $_SESSION["site_admin"]["cut_menu"]))
		{
			$pm->add_item(array(
				"text" => t("Kleebi"),
				"link" => $this->mk_my_orb("paste_menu", array("after" => $menu->id(), "ru" => get_ru()), "menu_site_admin")
			));
		}
		return $pm->get_menu();
	}

	function _get_empty_doc_menu()
	{
		if (!acl_base::prog_acl() || !aw_ini_get("config.site_editing") || !empty($_SESSION["no_display_site_editing"]))
		{
			return;
		}
		$pm = new popup_menu();
		$pm->begin_menu("site_edit_new");

		if (acl_base::can("add", $this->sel_section))
		{
			$url = $this->mk_my_orb("new", array("parent" => $this->sel_section, "return_url" => get_ru(), "is_sa" => 1), CL_DOCUMENT, true);
			$pm->add_item(array(
				"text" => t("Lisa uus"),
				"onclick" => "aw_popup_scroll(\"{$url}\", \"aw_doc_edit\", 800, 600);",
				"link" => "javascript:void(0)"
			));
		}
		else
		{
			return;
		}
		if (isset($_SESSION["site_admin"]["cut_doc"]) && acl_base::can("view", $_SESSION["site_admin"]["cut_doc"]))
		{
			$pm->add_item(array(
				"text" => t("Kleebi"),
				"link" => $this->mk_my_orb("paste_doc", array("ru" => get_ru()), "menu_site_admin")
			));
		}
		return $pm->get_menu();

	}

	function get_folder_document_sort_by($obj)
	{
		if ($obj->meta("sort_by") != "")
		{
			$ordby = $obj->meta("sort_by");
			if ($obj->meta("sort_by") == "RAND()")
			{
				$has_rand = true;
			}
			if ($obj->meta("sort_ord") != "")
			{
				$ordby .= " ".$obj->meta("sort_ord");
			}
			if ($obj->meta("sort_by") == "documents.modified")
			{
				$ordby .= ", objects.created DESC";
			};
		}
		else
		{
			$ordby = aw_ini_get("menuedit.document_list_order_by");
		}
		if ($obj->meta("sort_by2") != "")
		{
			if ($obj->meta("sort_by2") == "RAND()")
			{
				$has_rand = true;
			}
			$ordby .= ($ordby != "" ? " , " : " ").$obj->meta("sort_by2");
			if ($obj->meta("sort_ord2") != "")
			{
				$ordby .= " ".$obj->meta("sort_ord2");
			}
			if ($obj->meta("sort_by2") == "documents.modified")
			{
				$ordby .= ", objects.created DESC";
			};
		}
		if ($obj->meta("sort_by3") != "")
		{
			if ($obj->meta("sort_by3") == "RAND()")
			{
				$has_rand = true;
			}
			$ordby .= ($ordby != "" ? " , " : " ").$obj->meta("sort_by3");
			if ($obj->meta("sort_ord3") != "")
			{
				$ordby .= " ".$obj->meta("sort_ord3");
			}
			if ($obj->meta("sort_by3") == "documents.modified")
			{
				$ordby .= ", objects.created DESC";
			};
		}
		if ($ordby == "")
		{
			$ordby = "objects.jrk";
		}

		return $ordby;
	}
}
