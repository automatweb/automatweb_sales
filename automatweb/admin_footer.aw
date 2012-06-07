<?php
// siin imporditakse muutujad saidi raami sisse
// ja v2ljastatakse see

automatweb::$result->set_charset(languages::USER_CHARSET);

$site_title = isset($GLOBALS["site_title"]) ? $GLOBALS["site_title"] : "AutomatWeb";
$sf->read_template("index.tpl");

$i = new admin_if();
$i->insert_texts($sf);

$ta = aw_global_get("title_action");//TODO: iconv
if ($ta != "")
{
	$ta.=" / ";
}

// I need the styles in the HEADER of the page, otherwise I get dump whitespace
// at the bottom of the page which will bite me, if I'm using iframe based layout
// thingie, and styles have to be in the page header anyway according to the W3C specs

// so this code checks whether aw_styles subtemplate exists and if so, replaces
// it with the style definition .. otherwise it will add them at the bottom of the page
// as before
$txt = "";
$styles = active_page_data::on_shutdown_get_styles($txt);
$styles_done = false;

// check the url for classes and if any of those are in a product family, then set that
$pf = "";
if (!empty($_GET["class"]))
{
	$clid = clid_for_name($_GET["class"]);
	if (aw_ini_isset("classes.{$clid}.prod_family"))
	{
		$pf = aw_ini_get("classes.{$clid}.prod_family");
		$pf_url = aw_global_get("REQUEST_URI");
	}
}
$ru = isset($_GET["return_url"]) ? $_GET["return_url"] : null;
while (!empty($ru) && empty($pf))
{
	$url_bits = parse_url($ru);
	$vals = array();
	if (isset($url_bits["query"]))
	{
		parse_str($url_bits["query"], $vals);
	}

	if (!empty($vals["class"]))
	{
		$clid = clid_for_name($vals["class"]);
		if (aw_ini_isset("classes.{$clid}.prod_family"))
		{
			$pf = aw_ini_get("classes.{$clid}.prod_family");
			$pf_url = $ru;
		}
	}
	$ru = isset($vals["return_url"]) ? $vals["return_url"] : NULL;
}

$co = get_current_company();
if (!$co)
{
	$co = obj();
}

$p = get_current_person();
if (!$p)
{
	$p = obj();
}

if (!empty($_GET["id"]) and acl_base::can("view", $_GET["id"]))
{
	$cur_obj = obj($_GET["id"]);
	$cur_obj_name = $cur_obj->prop_xml("name");
}
else
{
	$cur_obj = obj();
	$cur_obj_name = "";
}

// do not display the YAH bar, if site_title is empty
$bmb = new popup_menu();
$bmb->begin_menu("settings_pop");

// language selection menu
$ld = languages::fetch(AW_REQUEST_CT_LANG_ID);
if (languages::count() > 1)
{
	$languages_menu = new popup_menu();
	$languages_menu->begin_menu("lang_pop");
	$languages_menu = $languages_menu->get_menu(array(
		"load_on_demand_url" => $sf->mk_my_orb("lang_pop", array("url" => automatweb::$request->get_uri()->get()), "language"),
		"text" => $ld["name"] . ' <img src="' . aw_ini_get("baseurl") . 'automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" class="nool" />'
	));
}
else
{
	$languages_menu = $ld["name"];
}

if (empty($pf_url))
{
	$pf_url = aw_global_get("REQUEST_URI");
}

$class_names = array(
	"doc" => t("Dokument"),
	"config" => t("Seaded")
);

$cur_class = "";
if (!empty($_GET["class"]))
{
	try
	{
		$cur_class = aw_ini_get("class_lut.{$_GET["class"]}");
		$cur_class = aw_ini_get("classes.{$cur_class}.name");
	}
	catch (Exception $e)
	{
		if (isset($class_names[$_GET["class"]]))
		{
			$cur_class = $class_names[$_GET["class"]];
		}
	}
}

try
{
	$locked = false;
	$parent = max(1, (empty($_GET["parent"]) ? $cur_obj->parent() : $_GET["parent"]));
}
catch (aw_lock_exception $e)
{
	$locked = true;
	$cur_obj = obj();
	$parent = 0;
}

			$bm = new popup_menu();
			$bm->begin_menu("user_bookmarks");
			$bmq = new popup_menu();
			$bmq->begin_menu("user_qa");
			$bmb = new popup_menu();
			$bmb->begin_menu("settings_pop");
			$bm_h = new popup_menu();
			$bm_h->begin_menu("history_pop");



$sf->vars(array(


				"bm_pop" => acl_base::prog_acl("view", "can_bm") ? $bm->get_menu(array(
					"load_on_demand_url" => $bm->mk_my_orb("pm_lod", array("url" => get_ru()), "user_bookmarks"),
					"text" => '<img src="/automatweb/images/aw06/ikoon_jarjehoidja.gif" alt="" width="16" height="14" border="0" class="ikoon" /> <span class="menu_text">'.t("J&auml;rjehoidja").'</span> <img width="5" height="3" border="0" alt="#" src="/automatweb/images/aw06/ikoon_nool_alla.gif" class="down_arrow">'//.' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" style="margin: 0 -3px 1px 0px" />'
				)) : "",
				"history_pop" => acl_base::prog_acl("view", "can_history") ? $bm_h->get_menu(array(
					"load_on_demand_url" => $bm->mk_my_orb("hist_lod", array("url" => get_ru()), "user"),
					"text" => '<img src="/automatweb/images/aw06/ikoon_ajalugu.gif" alt="" width="13" height="13" border="0" class="ikoon" /> <span class="menu_text">'.t("Ajalugu").'</span> <img width="5" height="3" border="0" alt="#" src="/automatweb/images/aw06/ikoon_nool_alla.gif" class="down_arrow">'//.' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" style="margin: 0 -3px 1px 0px" />'
				)) : "",

				"qa_pop" => acl_base::prog_acl("view", "can_quick_add") ? 
				$bmq->get_menu(array(
					"load_on_demand_url" => $bm->mk_my_orb("qa_lod", array("url" => get_ru()), "obj_quick_add"),
					"text" => '<img alt="" title="" border="0" src="'.aw_ini_get("baseurl").'automatweb/images/aw06/ikoon_lisa.gif" id="mb_user_qa" border="0" class="ikoon" /> <span class="menu_text">'.t("Lisa kiiresti").'</span><img width="5" height="3" border="0" alt="#" src="/automatweb/images/aw06/ikoon_nool_alla.gif" class="down_arrow">'//.' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" style="margin: 0 -3px 1px 0px" /></a>'
				)) : "",



	"prod_family" => $pf,
	"prod_family_href" => $pf_url,
	"cur_p_name" => $p->prop_xml("name"),
	"cur_p_url" => html::get_change_url($p->id(), array('return_url' => get_ru())),
	"cur_co_url" => $co->is_saved() ? html::get_change_url($co->id(), array('return_url' => get_ru())) : "",
	"cur_co_url_view" => $sf->mk_my_orb("view", array("id" => $co->id(), 'return_url' => get_ru()), CL_CRM_COMPANY),
	"cur_co_name" => $co->prop_xml("name"),
	"cur_class" => $cur_class,
	"cur_obj_name" => $cur_obj_name,
	"site_title" => $site_title,
	"stop_pop_url_add" => $sf->mk_my_orb("stopper_pop", array(
		"s_action" => "start",
		"new" => 1
	), CL_TASK),
	"stop_pop_url_quick_add" => $sf->mk_my_orb("stopper_pop", array(
		"source" => isset($_GET["class"]) ? $_GET["class"] : null,
		"source_id" => isset($_GET["id"]) ? $_GET["id"] : null,
		"s_action" => "start",
		"new" => 1
	), CL_TASK),
	"stop_pop_url_qw" => $sf->mk_my_orb("stopper_pop", array(), CL_TASK),
/*	"ui_lang" => $pm->get_menu(array(
		"text" => t("[Liidese keel]")
	)),*/
	"settings_pop" => $bmb->get_menu(array(
		"load_on_demand_url" => $sf->mk_my_orb("settings_lod", array("url" => get_ru()), "user"),
		"text" => '<img src="/automatweb/images/aw06/ikoon_seaded.gif" alt="seaded" width="17" height="17" border="0" align="left" style="margin: -1px 5px -3px -2px" />'.t("Seaded") . ' <img src="' . aw_ini_get("baseurl") . 'automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" class="nool" />'
	)),
	"lang_pop" => $languages_menu,
	"parent" => $parent,
	"random" => rand(100000,1000000),
	"session_end_msg" => t("Teie AutomatWeb'i sessioon aegub 5 minuti p&auml;rast!"),
	"btn_session_end_continue" => t("J&auml;tkan"),
	"btn_session_end_cancel" => t("L&otilde;petan"),
	"session_length" => ini_get("session.gc_maxlifetime")*1000,
	"ajax_loader_msg" => t("T&ouml;&ouml;tlen.") . html::linebreak() . t("&Uuml;ks hetk, palun.")
));


if (!automatweb::$request->arg("in_popup") and empty($_GET["in_popup"]))
{
	if (acl_base::prog_acl("view", "disp_person"))
	{
		$person_text = "";
		$person = get_current_person();
		if($person)
		{
			$cfg = new cfgutils();
			$popup_menu = new popup_menu();
			$popup_menu->begin_menu("current_person");

			if(acl_base::can("edit", $person->id()))
			{
				$uprops = $cfg->load_properties(array(
					"clid" => CL_CRM_PERSON,
				));
				$groups = $cfg->get_groupinfo();

				$params = array(
					"name" => "currentpersonmenu",
					"text" => t("Isiku andmed")
				);

				$popup_menu->add_sub_menu($params);


			$gopts = array();
			foreach($groups as $gid => $group)
			{
				$gopts[$gid] = $group;
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


					foreach($gopts as $gid => $group)
					{
						if(!empty($group["parent"]))
						{
							$popup_menu->add_item(array(
								"text" => $group["caption"],
								"link" => $cfg->mk_my_orb("change", array("group" => $gid, "id" => $person->id()), CL_CRM_PERSON),
								"parent" => $group["parent"]."_".$group["parent"]
							));
						
						}
						elseif(empty($group["subclasses"]))
						{
							$popup_menu->add_item(array(
								"text" => $group["caption"],
								"link" => $cfg->mk_my_orb("change", array("group" => $gid, "id" => $person->id()), CL_CRM_PERSON),
								"parent" => "currentpersonmenu"
							));
						}
						else
						{
							$params = array(
								"name" => $gid."_".$gid,
								"text" => $group["caption"],
								"parent" => "currentpersonmenu"
							);
							$popup_menu->add_sub_menu($params);
						}
					}



/*
				foreach($groups as $gid => $group)
				{
					if(empty($group["parent"]))
					{
						$popup_menu->add_item(array(
							"text" => $group["caption"],
							"link" => $cfg->mk_my_orb("change", array("group" => $gid, "id" => $person->id()), CL_CRM_PERSON),
							"parent" => "currentpersonmenu"
						));
					}
				}*/
			}

			if(acl_base::can("edit", aw_global_get("uid_oid")))
			{
				$uprops = $cfg->load_properties(array(
					"clid" => CL_USER,
				));
				$groups = $cfg->get_groupinfo();

				$params = array(
					"name" => "currentusermenu",
					"text" => t("Kasutaja andmed")
				);

				$popup_menu->add_sub_menu($params);

			$gopts = array();
			foreach($groups as $gid => $group)
			{
				$gopts[$gid] = $group;
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


					foreach($gopts as $gid => $group)
					{
						if(!empty($group["parent"]))
						{
							$popup_menu->add_item(array(
								"text" => $group["caption"],
								"link" => $cfg->mk_my_orb("change", array("group" => $gid, "id" => aw_global_get("uid_oid")), CL_USER),
								"parent" => $group["parent"]."_".$group["parent"]
							));
						
						}
						elseif(empty($group["subclasses"]))
						{
							$popup_menu->add_item(array(
								"text" => $group["caption"],
								"link" => $cfg->mk_my_orb("change", array("group" => $gid, "id" => aw_global_get("uid_oid")), CL_USER),
								"parent" => "currentusermenu"
							));
						}
						else
						{
							$params = array(
								"name" => $gid."_".$gid,
								"text" => $group["caption"],
								"parent" => "currentusermenu"
							);
							$popup_menu->add_sub_menu($params);
						}
					}

/*				foreach($groups as $gid => $group)
				{
					if(empty($group["parent"]))
					{
						$popup_menu->add_item(array(
							"text" => $group["caption"],
							"link" => $cfg->mk_my_orb("change", array("group" => $gid, "id" => aw_global_get("uid_oid")), CL_USER),
							"parent" => "currentusermenu"
						));
					}
				}*/
			}

			$popup_menu->add_item(array(
				"text" => t(" V&auml;ljun").'&nbsp;<img src="'.aw_ini_get("baseurl").'automatweb/images/aw06/ikoon_logout.gif" width="26" height="14" border="0" style="left:0px;top:2px;" alt="'.t("Logi v&auml;lja").'">',
				"link" => aw_ini_get("baseurl")."automatweb/orb.aw?class=users&action=logout",
			));
			$person_text = $popup_menu->get_menu(array(
				"text" => $person->name().'<img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" style="margin: 0 -3px 1px 0px" />',
			));
		}

		$sf->vars(array(
			"SHOW_CUR_P" => $person_text,//$sf->parse("SHOW_CUR_P")
		));
	}

	if (acl_base::prog_acl("view", "disp_person_view") and !acl_base::prog_acl("view", "disp_person"))
	{
		$sf->vars(array(
			"SHOW_CUR_P_VIEW" => $sf->parse("SHOW_CUR_P_VIEW")
		));
	}

	if (acl_base::prog_acl("view", "disp_person_text") and !acl_base::prog_acl("view", "disp_person") and !acl_base::prog_acl("view", "disp_person_view"))
	{
		$sf->vars(array(
			"SHOW_CUR_P_TEXT" => $sf->parse("SHOW_CUR_P_TEXT")
		));
	}

	if (acl_base::prog_acl("view", "disp_co_edit"))
	{
		$sf->vars(array(
			"SHOW_CUR_CO" => $sf->parse("SHOW_CUR_CO")
		));
	}

	if (acl_base::prog_acl("view", "disp_co_view") && !acl_base::prog_acl("view", "disp_co_edit"))
	{
		$sf->vars(array(
			"SHOW_CUR_CO_VIEW" => $sf->parse("SHOW_CUR_CO_VIEW")
		));
	}

	if (acl_base::prog_acl("view", "disp_co_text") && !acl_base::prog_acl("view", "disp_co_edit") && !acl_base::prog_acl("view", "disp_co_view"))
	{
		$sf->vars(array(
			"SHOW_CUR_CO_TEXT" => $sf->parse("SHOW_CUR_CO_TEXT")
		));
	}

	if (acl_base::prog_acl("view", "disp_object_type"))
	{
		$sf->vars(array(
			"SHOW_CUR_CLASS" => $sf->parse("SHOW_CUR_CLASS")
		));
	}

	if (acl_base::prog_acl("view", "disp_object_link"))
	{
		$sf->vars(array(
			"SHOW_CUR_OBJ" => $sf->parse("SHOW_CUR_OBJ")
		));
	}
	$shwy =  (empty($site_title) || aw_global_get("hide_yah")) && $_GET["class"] !== "admin_if";

	$sf->vars(array(
		"YAH" => $shwy ? ($site_title != "" ? "&nbsp;" : "") : $sf->parse("YAH"),
		"YAH2" => $shwy ? ($site_title != "" ? "&nbsp;" : "") : $sf->parse("YAH2"),
	));

	$sf->vars(array(
		"HEADER" => $sf->parse("HEADER")
	));
}
else
{
	$sf->vars(array(
		"NO_HEADER" => $sf->parse("NO_HEADER")
	));
	$site_title = "";
	$shwy = true;
}

// compose html title
$html_title = aw_ini_get("stitle");
$html_title_obj = (CL_ADMIN_IF == $cur_obj->class_id()) ? aw_global_get("site_title_path_obj_name") : $cur_obj_name;

if (!empty($html_title))
{
	$html_title .= " - " . $cur_class;
}

if (!empty($html_title_obj))
{
	$html_title .=  ": " . $html_title_obj;
}


$sf->vars(array(
	"content"	=> $content,
	"charset" => languages::USER_CHARSET,
	"title_action" => $ta,
	"html_title" => $html_title,
	"MINIFY_JS_AND_CSS" => minify_js_and_css::parse_admin_header($sf->parse("MINIFY_JS_AND_CSS")),
	"POPUP_MENUS" => cache::file_get("aw_toolbars_".aw_global_get("uid")),
));
cache::file_set("aw_toolbars_".aw_global_get("uid"), "");

if ($sf->is_template("aw_styles"))
{
	$sf->vars(array("aw_styles" => $styles));
	$styles_done = true;
}

// include css loaded from code:
$sf->vars(array("css_styles_head" => active_page_data::get_styles()));

// include javascript loaded from code:
$sf->vars(array("javascript" => active_page_data::get_javascript("head")));
$sf->vars(array("javascript_bottom" => active_page_data::get_javascript("bottom")));
$str= $sf->parse();

if (!$styles_done)
{
	$str .= $styles;
}

if (isset($_GET["TPL"]) and $_GET["TPL"] === "1")
{
	// fix for logged out users - dint show templates after page refresh
	if (!aw_global_get("uid"))
	{
		if (strlen(cache::file_get("tpl_equals_1_cache_".aw_global_get("section")))==0)
		{
			cache::file_set("tpl_equals_1_cache_".aw_global_get("section"), aw_global_get("TPL=1"));
		}
		else
		{
			aw_global_set("TPL=1", cache::file_get("tpl_equals_1_cache_".aw_global_get("section")));
		}
	}
	else
	{
		cache::file_set("tpl_equals_1_cache_".aw_global_get("section"), aw_global_get("TPL=1"));
	}

	$tmp = $sf->template_dir;
	$sf->template_dir = aw_ini_get("tpldir");
	$sf->read_template("debug/tpl_equals_1.tpl");
	$sf->template_dir = $tmp;
	$tmp = "";
	eval (aw_global_get("TPL=1"));
	foreach ($_aw_tpl_equals_1 as $key=>$var)
	{
		$count = 0;
		foreach($_aw_tpl_equals_1_counter as $var2)
		{
			if ($key == $var2)
			{
				$count++;
			}
		}

		$sf->vars(array(
			"text" => $key,
			"link" => $var["link"],
			"count" => $count,
		));
		$tmp .= $sf->parse("TEMPLATE");
	}

	$sf->vars(array(
		"TEMPLATE" => $tmp
	));
	aw_global_set("TPL=1", $sf->parse());
	$str = preg_replace("/<body.*>/imsU", "\\0".aw_global_get("TPL=1"), $str);
}

$content = $str;
aw_shutdown();


if (isset($_SESSION["user_history_count"]) and $_SESSION["user_history_count"] > 0)
{
	if (!isset($_SESSION["user_history"]) or !is_array($_SESSION["user_history"]))
	{
		$_SESSION["user_history"] = array();
		$_SESSION["user_history_sets"] = array();
	}

	$pu = parse_url(get_ru());
	if (isset($pu["query"]))
	{
		parse_str($pu["query"], $bits);
	}
	else
	{
		$bits = array();
	}

	if (empty($bits["id"]))
	{
		$bits["id"] = "";
	}

	if (empty($bits["class"]))
	{
		$bits["class"] = "";
	}

	if (empty($bits["action"]))
	{
		$bits["action"] = "";
	}

	if (!$locked and !empty($bits["id"]))
	{
		$o = obj($bits["id"]);
		$st = $o->prop_xml("name");
	}
	else
	{
		$st = $site_title;
		$o = obj();
	}

	if (!$locked and !empty($bits["group"]))
	{
		$gl = $o->get_group_list();
		$st .= " - ".ifset($gl, $bits["group"], "caption");
	}

	if ($st)
	{
		if ($_SESSION["user_history_has_folders"])
		{
			$has = false;
			if (!isset($_SESSION["user_history"][$bits["class"]]))
			{
				$_SESSION["user_history"][$bits["class"]] = array();
			}

			foreach(safe_array($_SESSION["user_history"][$bits["class"]]) as $_url => $_t)
			{
				$_pu = parse_url($_url);
				parse_str($_pu["query"], $_bits);

				if (empty($_bits["id"])) $_bits["id"] = "";
				if (empty($_bits["class"])) $_bits["class"] = "";
				if (empty($_bits["action"])) $_bits["action"] = "";
				if (empty($_bits["group"])) $_bits["group"] = "";

				if (empty($bits["group"])) $bits["group"] = "";

				if ($_bits["class"] == $bits["class"] && $_bits["id"] == $bits["id"] && $_bits["group"] == $bits["group"])
				{
					$has = true;
					break;
				}
			}

			if (!$has)
			{
				$_SESSION["user_history"][$bits["class"]][get_ru()] = strip_tags($st);
			}

			if (count($_SESSION["user_history"][$bits["class"]]) > $_SESSION["user_history_count"])
			{
				array_shift($_SESSION["user_history"][$bits["class"]]);
			}
		}
		else
		{
			$has = false;
			foreach(safe_array($_SESSION["user_history"]) as $_url => $_t)
			{
				$_pu = parse_url($_url);
				if (isset($_pu["query"]))
				{
					parse_str($_pu["query"], $_bits);
					if (ifset($_bits, "class") == ifset($bits, "class") && ifset($_bits, "id") == ifset($bits, "id") && ifset($_bits, "group") == ifset($bits, "group"))
					{
						$has = true;
						break;
					}
				}
			}

			if (!$has)
			{
				$_SESSION["user_history"][get_ru()] = strip_tags($st);
			}

			if (count($_SESSION["user_history"]) > $_SESSION["user_history_count"])
			{
				array_shift($_SESSION["user_history"]);
			}
		}
	}
}
