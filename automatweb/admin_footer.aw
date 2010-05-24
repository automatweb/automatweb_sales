<?php
// siin imporditakse muutujad saidi raami sisse
// ja v2ljastatakse see
$site_title = isset($GLOBALS["site_title"]) ? $GLOBALS["site_title"] : "AutomatWeb";
$sf->read_template("index.tpl");

$i = new admin_if();
$i->insert_texts($sf);

$ta = aw_global_get("title_action");
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
$apd = new active_page_data();
$txt = "";
$styles = $apd->on_shutdown_get_styles($txt);
$styles_done = false;
// check the url for classes and if any of those are in a prod family, then set that
$pf = "";
$clss = aw_ini_get("classes");
if (!empty($_GET["class"]))
{
	$clid = clid_for_name($_GET["class"]);
	if (!empty($clss[$clid]["prod_family"]))
	{
		$pf = $clss[$clid]["prod_family"];
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
		if (!empty($clss[$clid]["prod_family"]))
		{
			$pf = $clss[$clid]["prod_family"];
			$pf_url = $ru;
		}
	}
	$ru = isset($vals["return_url"]) ? $vals["return_url"] : NULL;
}
aw_disable_acl();
$p = get_current_person();
$co = get_current_company();
if (!$co)
{
	$co = obj();
}
aw_restore_acl();
if (!empty($_GET["id"]) and $sf->can("view", $_GET["id"]))
{
	$cur_obj = obj($_GET["id"]);
}
else
{
	$cur_obj = obj();
}
// do not display the YAH bar, if site_title is empty
$bmb = new popup_menu();
$bmb->begin_menu("settings_pop");
$bml = new popup_menu();
$bml->begin_menu("lang_pop");

$l = new languages();
if (aw_ini_get("user_interface.full_content_trans"))
{
	$ld = $l->fetch(aw_global_get("ct_lang_id"));
	$page_charset = $charset = $ld["charset"];
}
else
{
	$ld = $l->fetch(aw_global_get("lang_id"));
	$page_charset = $charset = aw_global_get("charset");
}

if (empty($pf_url))
{
	$pf_url = aw_global_get("REQUEST_URI");
}

$class_names = array(
	"doc" => t("Dokument"),
	"config" => t("Seaded"),
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
$sf->vars(array(
	"prod_family" => $pf,
	"prod_family_href" => $pf_url,
	"cur_p_name" => $p->prop_xml("name"),
	"cur_p_url" => html::get_change_url($p->id(), array('return_url' => get_ru())),
	"cur_co_url" => html::get_change_url($co->id(), array('return_url' => get_ru())),
	"cur_co_url_view" => $sf->mk_my_orb("view", array("id" => $co->id(), 'return_url' => get_ru()), CL_CRM_COMPANY),
	"cur_co_name" => $co->prop_xml("name"),
	"cur_class" => $cur_class,
	"cur_obj_name" => $cur_obj->prop_xml("name"),
	"site_title" => $site_title,
	"stop_pop_url_add" => $sf->mk_my_orb("stopper_pop", array(
		"s_action" => "start",
		"new" => 1,
	), CL_TASK),
	"stop_pop_url_quick_add" => $sf->mk_my_orb("stopper_pop", array(
		"source" => isset($_GET["class"]) ? $_GET["class"] : null,
		"source_id" => isset($_GET["id"]) ? $_GET["id"] : null,
		"s_action" => "start",
		"new" => 1,
	), CL_TASK),
	"stop_pop_url_qw" => $sf->mk_my_orb("stopper_pop", array(), CL_TASK),
/*	"ui_lang" => $pm->get_menu(array(
		"text" => t("[Liidese keel]")
	)),*/
	"settings_pop" => $bmb->get_menu(array(
		"load_on_demand_url" => $sf->mk_my_orb("settings_lod", array("url" => get_ru()), "user"),
		"text" => '<img src="/automatweb/images/aw06/ikoon_seaded.gif" alt="seaded" width="17" height="17" border="0" align="left" style="margin: -1px 5px -3px -2px" />'.t("Seaded")//.' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" class="nool" />'
	)),
	"lang_pop" => $bml->get_menu(array(
		"load_on_demand_url" => $sf->mk_my_orb("lang_pop", array("url" => get_ru()), "language"),
		"text" => $ld["name"]//.' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" class="nool" />'
	)),
	"parent" => $parent,
	"random" => rand(100000,1000000),
	"session_end_msg" => t("Teie AutomatWeb'i sessioon aegub 5 minuti p&auml;rast!"),
	"btn_session_end_continue" => html_entity_decode(t("J&auml;tkan")),
	"btn_session_end_cancel" => html_entity_decode(t("L&otilde;petan")),
	"session_length" => ini_get("session.gc_maxlifetime")*1000,
	"ajax_loader_msg" => t("T&ouml;&ouml;tlen.<br />&Uuml;ks hetk, palun.")
));


if (!automatweb::$request->arg("in_popup"))
{
	if ($sf->prog_acl("view", "disp_person"))
	{
		$sf->vars(array(
			"SHOW_CUR_P" => $sf->parse("SHOW_CUR_P")
		));
	}

	if ($sf->prog_acl("view", "disp_person_view") and !$sf->prog_acl("view", "disp_person"))
	{
		$sf->vars(array(
			"SHOW_CUR_P_VIEW" => $sf->parse("SHOW_CUR_P_VIEW")
		));
	}

	if ($sf->prog_acl("view", "disp_person_text") and !$sf->prog_acl("view", "disp_person") and !$sf->prog_acl("view", "disp_person_view"))
	{
		$sf->vars(array(
			"SHOW_CUR_P_TEXT" => $sf->parse("SHOW_CUR_P_TEXT")
		));
	}

	if ($sf->prog_acl("view", "disp_co_edit"))
	{
		$sf->vars(array(
			"SHOW_CUR_CO" => $sf->parse("SHOW_CUR_CO")
		));
	}

	if ($sf->prog_acl("view", "disp_co_view") && !$sf->prog_acl("view", "disp_co_edit"))
	{
		$sf->vars(array(
			"SHOW_CUR_CO_VIEW" => $sf->parse("SHOW_CUR_CO_VIEW")
		));
	}

	if ($sf->prog_acl("view", "disp_co_text") && !$sf->prog_acl("view", "disp_co_edit") && !$sf->prog_acl("view", "disp_co_view"))
	{
		$sf->vars(array(
			"SHOW_CUR_CO_TEXT" => $sf->parse("SHOW_CUR_CO_TEXT")
		));
	}

	if ($sf->prog_acl("view", "disp_object_type"))
	{
		$sf->vars(array(
			"SHOW_CUR_CLASS" => $sf->parse("SHOW_CUR_CLASS")
		));
	}

	if ($sf->prog_acl("view", "disp_object_link"))
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


$tmp = array();
if (!empty($site_title))	// weird, but lots of places rely on the yah line being empty and thus having no height.
{
	// do the language selecta
	$baseurl = aw_ini_get("baseurl");
	$lang_id = aw_global_get("lang_id");
	$li = $l->get_list();
	foreach($li as $lid => $ln)
	{
		$url = $baseurl."/automatweb/index.aw?set_lang_id=".$lid;
		$target = "_top";
		$tmp[] = html::href(array(
			"url" => $url,
			"target" => $target,
			"caption" => ($lid == $lang_id ? "<b><font color=\"#FF0000\">".$ln."</font></b>" : $ln)
		));
	}

	$sf->vars(array(
		"lang_string" => join("|", $tmp),
		"header_text" => aw_call_header_text_cb()
	));
	$sf->vars(array(
		"LANG_STRING" => $sf->parse("LANG_STRING")
	));
}




// if you set this global variable in your code, then the whole page will be converted and shown
// in the requested charset. This will be handy for translation forms .. and hey .. perhaps one
// day we are going to move to unicode for the whole interface

$output_charset = aw_global_get("output_charset");

if (!empty($output_charset))
{
	$charset = $output_charset;
}

// compose html title
$html_title = aw_ini_get("stitle");
$html_title_obj = (CL_ADMIN_IF == $cur_obj->class_id()) ? aw_global_get("site_title_path_obj_name") : $cur_obj->prop_xml("name");

if (!empty($html_title))
{
	$html_title .= " - " . $cur_class;
}

if (!empty($html_title_obj))
{
	$html_title .=  ": " . $html_title_obj;
}

$cache = new cache();

$sf->vars(array(
	"content"	=> $content,
	"charset" => $charset,
	"title_action" => $ta,
	"html_title" => $html_title,
	"MINIFY_JS_AND_CSS" => aw_ini_get("site_id") != 477 ? (minify_js_and_css::parse_admin_header($sf->parse("MINIFY_JS_AND_CSS"))) : $sf->parse("MINIFY_JS_AND_CSS"),
	"POPUP_MENUS" => $cache->file_get("aw_toolbars_".aw_global_get("uid")),
));
$cache->file_set("aw_toolbars_".aw_global_get("uid"), "");

if ($sf->is_template("aw_styles"))
{
	$sf->vars(array("aw_styles" => $styles));
	$styles_done = true;
}

// include javascript files which are loaded from code:
$sf->vars(array("javascript" => $apd->get_javascript()));
$sf->vars(array("javascript_bottom" => $apd->get_javascript("bottom")));
$str= $sf->parse();

if (!$styles_done)
{
	$str .= $styles;
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
		$st = $o->name();
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

	if ($st != "")
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
				if (empty($_bits["id"]))
				{
					$_bits["id"] = "";
				}
				if (empty($_bits["class"]))
				{
					$_bits["class"] = "";
				}
				if (empty($_bits["action"]))
				{
					$_bits["action"] = "";
				}

				if (($_bits["class"] == $bits["class"] && $_bits["id"] == $bits["id"] && $_bits["group"] == $bits["group"]))
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
?>
