<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/cb_form_chain/cb_form_chain.aw,v 1.50 2007/11/23 07:06:27 dragut Exp $
// cb_form_chain.aw - Vormiahel 
/*

@classinfo syslog_type=ST_CB_FORM_CHAIN relationmgr=yes no_comment=1 no_status=1 maintainer=dragut

@default table=objects
@default group=general
@default field=meta 
@default method=serialize

	@property confirm_sep_page type=checkbox ch_value=1
	@caption Kinnitusvaade enne saatmist


	@property entry_finder_controller type=relpicker reltype=RELTYPE_ENTRY_FINDER_CONTOLLER
	@caption Olemasoleva sisestuse leidmise kontroller

	@property redir_to type=callback callback=callback_get_redir
	@caption P&auml;rast t&auml;itmist suuna

	@property confirm_view_ctl type=relpicker reltype=RELTYPE_CONTROLLER
	@caption Kinnitusevaate kontroller

	@property disp_view_ctl type=relpicker reltype=RELTYPE_CONTROLLER
	@caption V&auml;ljundi kontroller 

	@property confirm_view_tpl type=select 
	@caption Kinnitusvaate template

	@property show_view_tpl type=select 
	@caption L&otilde;puvaate template

	@property print_view_tpl type=select 
	@caption Printvaate template

@default group=cfs_tbl

	@property webforms_toolbar type=toolbar no_caption=1
	@caption Vormide t&ouml;&ouml;riistariba

	@property cfs type=table no_caption=1

@default group=cfs_headers

	@property cfs_headers type=table no_caption=1

@default group=cfs_entry_tbl

	@property cfs_entry_tbl type=table no_caption=1

@default group=mail_settings_general

	@property mail_to type=textbox 
	@caption Kellele

	@property mail_to_form type=relpicker reltype=RELTYPE_CF
	@caption Vorm, milles on saaja aadress

	@property mail_to_prop type=select 
	@caption Element, milles on saaja aadress	

	@property mail_from_addr type=textbox
	@caption Kellelt (aadress)

	@property mail_from_name type=textbox
	@caption Kellelt (nimi)

	@property mail_subj type=textbox
	@caption Teema

	@property mail_add_pdf type=checkbox ch_value=1
	@caption Lisa meilile pdf

	@property mail_content_ctr type=relpicker reltype=RELTYPE_CONTROLLER
	@caption Meili sisu kontroller

@default group=mail_settings_confirm

	@property send_confirm_mail type=checkbox ch_value=1
	@caption Saada tellijale kinnitusmeil

	@property confirm_mail_subj type=textbox
	@caption Kinnitusmeili subjekt

	@property confirm_mail type=textarea rows=20 cols=50
	@caption Kinnitusmeili sisu

	@property confirm_mail_to_form type=relpicker reltype=RELTYPE_CF
	@caption Vorm, milles on saaja aadress

	@property confirm_mail_to_prop type=select 
	@caption Element, milles on saaja aadress	

	@property confirm_mail_content_ctr type=relpicker reltype=RELTYPE_CONTROLLER
	@caption Kinnitusmeili sisu kontroller

@default group=entry_settings

	@property entry_folder type=relpicker reltype=RELTYPE_ENTRY_FOLDER
	@caption Andmete kataloog
	@comment Vaikimisi salvestatakse seadete objekti alla

	@property entry_name_form type=relpicker reltype=RELTYPE_CF
	@caption Andmete nime vorm

	@property entry_name_el type=select multiple=1
	@caption Andmete nime elemendid

@default group=entries_unc,entries_con

	@property entry_tb type=toolbar no_caption=1
	@caption Andmete toolbar

	@property entry_table type=table no_caption=1
	@caption Andmed

@default group=entries_src

	@property search_cb type=callback callback=callback_get_search 
	@property search_res type=table no_caption=1 store=no

@groupinfo cfs caption="Vormid"
	@groupinfo cfs_tbl caption="Vormid" parent=cfs
	@groupinfo cfs_headers caption="Pealkirjad" parent=cfs
	@groupinfo cfs_entry_tbl caption="Andmete tabel" parent=cfs

@groupinfo mail_settings caption="Meiliseaded"
	@groupinfo mail_settings_general caption="Tellimuse meil" parent=mail_settings
	@groupinfo mail_settings_confirm caption="Kinnitusmeil" parent=mail_settings

@groupinfo entry_settings caption="Andmete seaded"

@groupinfo entries caption="Andmed"
	@groupinfo entries_unc caption="Kinnitamata" parent=entries submit=no
	@groupinfo entries_con caption="Kinnitatud" parent=entries submit=no
	@groupinfo entries_src caption="Otsing" parent=entries submit=no submit_method=get


@reltype CF value=1 clid=CL_WEBFORM
@caption veebivorm

@reltype REP_CTR value=2 clid=CL_CFGCONTROLLER
@caption vormide kordamise kontroller

@reltype ENTRY_FOLDER value=3 clid=CL_MENU
@caption andmete kataloog

@reltype DEF_CTR value=4 clid=CL_CFGCONTROLLER
@caption default andmete kontroller

@reltype GEN_CTR value=5 clid=CL_CFGCONTROLLER
@caption info kontroller

@reltype ENTRY_FINDER_CONTOLLER value=6 clid=CL_CFGCONTROLLER
@caption Sisestuse leidmise kontroller

@reltype DOC value=7 clid=CL_DOCUMENT
@caption Dokument kuhu suunata

@reltype CONTROLLER value=8 clid=CL_FORM_CONTROLLER
@caption Kontroller

*/

class cb_form_chain extends class_base
{
	function cb_form_chain()
	{
		$this->init(array(
			"tpldir" => "applications/cb_form_chain",
			"clid" => CL_CB_FORM_CHAIN
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "search_res":
				$this->_search_res($arr);
				break;

			case "webforms_toolbar":
				$this->draw_webforms_toolbar($arr);
				break;

			case "cfs":
				$this->_cfs($arr);
				break;

			case "cfs_headers":
				$this->_cfs_headers($arr);
				break;

			case "confirm_mail_to_prop":
				if (!$arr["obj_inst"]->prop("confirm_mail_to_form"))
				{
					return PROP_IGNORE;
				}

				$prop["options"] = $this->get_el_picker_from_wf(obj($arr["obj_inst"]->prop("confirm_mail_to_form")));
				break;

			case "mail_to_prop":
				if (!$arr["obj_inst"]->prop("mail_to_form"))
				{
					return PROP_IGNORE;
				}

				$prop["options"] = $this->get_el_picker_from_wf(obj($arr["obj_inst"]->prop("mail_to_form")));
				break;

			case "entry_name_el":
				if (!$arr["obj_inst"]->prop("entry_name_form"))
				{
					return PROP_IGNORE;
				}

				$prop["options"] = $this->get_el_picker_from_wf(obj($arr["obj_inst"]->prop("entry_name_form")));
				break;

			case "entry_tb";
				$this->_entry_tb($arr);
				break;

			case "entry_table":
				$this->_entry_table($arr);
				break;

			case "cfs_entry_tbl":
				$this->_cfs_entry_tbl($arr);
				break;
	
			case "confirm_view_tpl":
			case "show_view_tpl":
			case "print_view_tpl":
				$t = get_instance("templatemgr");
				$prop["options"] = array("" => t("--vali--")) + $t->template_picker(array("folder" => "applications/cb_form_chain"));
				break;	
		};
		return $retval;
	}

	function draw_webforms_toolbar($arr)
	{
		$t = $arr['prop']['vcl_inst'];
		$t->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Uus veebivorm'),
			'url' => $this->mk_my_orb('new', array(
				'parent' => $arr['obj_inst']->parent(),
				'reltype' => 1, // CF (webform)
				'alias_to' => $arr['obj_inst']->id(),
				'return_url' => get_ru()
			), CL_WEBFORM),
		));
	}

	function get_el_picker_from_wf($wf)
	{
		$ot = $wf->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");

		$cf = get_instance(CL_CFGFORM);
		$props = $cf->get_props_from_ot(array(
			"ot" => $ot->id()
		));
		$ps = array("" => t("--vali--"));
		foreach($props as $pn => $pd)
		{
			$ps[$pn] = $pd["caption"];
		}
		return $ps;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "redir_to":
				$this->_save_redir($arr);
				break;

			case "cfs":
				$arr["obj_inst"]->set_meta("d", $arr["request"]["d"]);
				break;

			case "cfs_headers":
				$arr["obj_inst"]->set_meta("cfs_headers", $arr["request"]["hdrs"]);
				break;

			case "cfs_entry_tbl":
				$arr["obj_inst"]->set_meta("entry_tbl", $arr["request"]["t"]);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function _init_cfs_t($t)
	{
		$t->define_field(array(
			"name" => "form",
			"caption" => t("Vorm"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "page",
			"caption" => t("Lehek&uuml;lg"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "repeat",
			"caption" => t("Korduv"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "repeat_fix",
			"caption" => t("Fikseeritud ridade arv"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "el_table",
			"caption" => t("Elemendid tabelina"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "data_table",
			"caption" => t("Andmed tabelina"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "data_table_confirm_vert",
			"caption" => t("Kinnituse vaates tabel &uuml;levalt alla"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "repeat_ctr",
			"caption" => t("Korduste kontroller"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "def_ctr",
			"caption" => t("Default andmete kontroller"),
			"align" => "center"
		));
	}

	function _cfs($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		if (method_exists($t, "set_caption"))
		{
			$t->set_caption(t('Vormiahela veebivormid'));
		}
		$this->_init_cfs_t($t);

		$rep_ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_REP_CTR")));
		$reps = array("" => "") + $rep_ol->names();

		$def_ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_DEF_CTR")));
		$defs = array("" => "") + $def_ol->names();

		$d = safe_array($arr["obj_inst"]->meta("d"));
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CF")) as $c)
		{
			$o = $c->to();
			$rc = "";
			if ($d[$o->id()]["repeat"] == 1)
			{
				$rc = html::select(array(
					"name" => "d[".$o->id()."][rep_ctr]",
					"options" => $reps,
					"selected" => $d[$o->id()]["rep_ctr"]
				));
			}
			$t->define_data(array(
				"form" => html::get_change_url($o->id(), array("return_url" => get_ru()), parse_obj_name($c->prop("to.name"))),
				"page" => html::textbox(array(
					"size" => 5,
					"name" => "d[".$o->id()."][page]",
					"value" => $d[$o->id()]["page"]
				)),
				"ord" => html::textbox(array(
					"size" => 5,
					"name" => "d[".$o->id()."][ord]",
					"value" => $d[$o->id()]["ord"]
				)),
				"repeat" => html::checkbox(array(
					"name" => "d[".$o->id()."][repeat]",
					"value" => 1,
					"checked" => $d[$o->id()]["repeat"] == 1 
				)),
				"repeat_fix" => html::checkbox(array(
					"name" => "d[".$o->id()."][repeat_fix]",
					"value" => 1,
					"checked" => $d[$o->id()]["repeat_fix"] == 1 
				)),
				"el_table" => html::checkbox(array(
					"name" => "d[".$o->id()."][el_table]",
					"value" => 1,
					"checked" => $d[$o->id()]["el_table"] == 1 
				)),
				"data_table" => html::checkbox(array(
					"name" => "d[".$o->id()."][data_table]",
					"value" => 1,
					"checked" => $d[$o->id()]["data_table"] == 1 
				)),
				"data_table_confirm_vert" => html::checkbox(array(
					"name" => "d[".$o->id()."][data_table_confirm_vert]",
					"value" => 1,
					"checked" => $d[$o->id()]["data_table_confirm_vert"] == 1 
				)),
				"repeat_ctr" => $rc,
				"def_ctr" => html::select(array(
					"name" => "d[".$o->id()."][def_ctr]",
					"options" => $defs,
					"selected" => $d[$o->id()]["def_ctr"]
				))
			));
		}
		$t->set_sortable(false);
	}

	function _init_cfs_headers_t($t)
	{
		$t->define_field(array(
			"name" => "pg",
			"caption" => t("Lehek&uuml;lg"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "title",
			"caption" => t("Pealkiri"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "has_save",
			"caption" => t("N&auml;ita salvesta nuppu"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "gen_ctr",
			"caption" => t("Info kontroller"),
			"align" => "center"
		));
	}

	function _cfs_headers($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_cfs_headers_t($t);

		$hdrs = safe_array($arr["obj_inst"]->meta("cfs_headers"));

		$def_ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_GEN_CTR")));
		$defs = array("" => "") + $def_ol->names();
		
		$pgs = $this->_get_page_list($arr["obj_inst"]);
		foreach($pgs as $pg)
		{
			$t->define_data(array(
				"pg" => $pg,
				"title" => html::textbox(array(
					"name" => "hdrs[$pg][name]",
					"value" => $hdrs[$pg]["name"]
				)),
				"has_save" => html::checkbox(array(
					"name" => "hdrs[$pg][has_save]",
					"value" => 1,
					"checked" => $hdrs[$pg]["has_save"]
				)),
				"gen_ctr" => html::select(array(
					"name" => "hdrs[$pg][gen_ctr]",
					"options" => $defs,
					"selected" => $hdrs[$pg]["gen_ctr"]
				))
			));
		}
		$t->set_sortable(false);
	}

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);

		if ($_GET["del_num"])
		{
			unset($_SESSION["cbfc_data"][$_GET["del_wf"]][$_GET["del_num"]-1]);
			$_SESSION["cbfc_data"][$_GET["del_wf"]] = array_values($_SESSION["cbfc_data"][$_GET["del_wf"]]);
			header("Location: ".aw_url_change_var(array("del_num" => null, "del_wf" => null)));
			die();
		}

		if ($_GET["display"])
		{
			if ($this->can("view", $ob->prop("disp_view_ctl")))
			{
				$fc = get_instance(CL_FORM_CONTROLLER);
				return $fc->eval_controller(
					$ob->prop("disp_view_ctl"),
					$_SESSION["cbfc_last_entry"],
					$arr["id"],
					$arr["id"]
				);
			}
			$i = get_instance(CL_CB_FORM_CHAIN_ENTRY);
			return $i->show(array(
				"id" => $_SESSION["cbfc_last_entry"],
			));
		}

		if ($_GET["do_confirm"] == 1)
		{
			return $this->_do_confirm_view($ob);
		}

		$page = $this->_get_page($ob);
		if ($page == 1 && !$_SESSION["cbfc_data"])
		{
			// see if we need to get another entry
			if ($this->can("view", $ob->prop("entry_finder_controller")))
			{
				$ctr = obj($ob->prop("entry_finder_controller"));
				$ctr_i = $ctr->instance();

				$load_entry_id = $ctr_i->check_property($ctr->id(), $ctr->id(), $ctr, $_GET, $ctr, $ob);
				if ($load_entry_id)
				{
					$_SESSION["cbfc_current_entry_id"] = $load_entry_id;
					$this->_load_entry($load_entry_id);
				}
			}
		}

		$forms = $this->_get_forms_for_page($ob, $page);

		$html = $this->_draw_forms($ob, $forms);
		unset($_SESSION["cbfc_errors"]);
		return $html;
	}

	function _get_page_list($o)
	{
		$d = safe_array($o->meta("d"));
		$pgs = array();
		foreach($d as $form => $dat)
		{
			$pgs[$dat["page"]] = $dat["page"];
		}
		asort($pgs);
		return $pgs;
	}

	function _get_page($o)
	{
		$pgs = $this->_get_page_list($o);
		if (!empty($_GET["cbfc_pg"]) && isset($pgs[$_GET["cbfc_pg"]]))
		{
			return $_GET["cbfc_pg"];
		}
		return reset($pgs);
	}

	function _get_forms_for_page($o, $page)
	{
		$d = safe_array($o->meta("d"));
		$forms = array();
		foreach($d as $form => $dat)
		{
			if ($dat["page"] == $page)
			{
				$fd = array(
					"form" => $form,
					"rep" => $dat["repeat"],
					"rep_cnt" => 1,
					"el_table" => $dat["el_table"],
					"data_table" => $dat["data_table"],
					"repeat_fix" => $dat["repeat_fix"]
				);
				if ($dat["repeat"] && is_oid($dat["rep_ctr"]) && $this->can("view", $dat["rep_ctr"]))
				{
					$ci = get_instance(CL_CFGCONTROLLER);
					$fd["rep_cnt"] = $ci->check_property($dat["rep_ctr"], $form, $form, $form, $form, $form);
				}
				else
				if ($dat["repeat"])
				{
					$fd["rep_cnt"] = $this->get_current_entry_count($fd)+1;
				}

				if (is_oid($dat["def_ctr"]) && $this->can("view", $dat["def_ctr"]))
				{
					$fd["def_ctr"] = $dat["def_ctr"];
				}
				$forms[] = $fd;
			}
		}
		return $forms;
	}

	function _draw_forms($o, $forms)
	{
		$cf = get_instance(CL_CFGFORM);
		$this->read_template("show_form.tpl");

		$this->_draw_page_titles($o);
		foreach($forms as $form_dat)
		{
			$wf = obj($form_dat["form"]);
			$ot = $wf->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");

			$props = $cf->get_props_from_ot(array(
				"ot" => $ot->id()
			));
			$this->_apply_view_controllers($props, $wf, $o);

			$this->vars(array(
				"form_name" => $wf->name()
			));
			$html .= $this->parse("FORM_HEADER");

			if ($form_dat["data_table"])
			{
				$this->_display_entry_data_table($form_dat, $props, $wf, $o);

				if (($idx = $this->_can_show_edit_form($form_dat)))
				{
					$html .= $this->_html_from_props($form_dat, $props, $ot, $wf, $o, $idx > 0 ? $idx-1 : NULL);
				}
			}
			else
			{
				if ($form_dat["el_table"] == 1)
				{
					$html .= $this->_html_table_from_props($form_dat, $props, $ot, $wf, $o);
				}
				else
				{
					$html .= $this->_html_from_props($form_dat, $props, $ot, $wf, $o);
				}
			}
		}
		$gen_ctr_res = $this->_do_gen_ctr($o);
		$this->vars(array(
			"form" => $html,
			"reforb" => $this->mk_reforb("submit_data", array("id" => $o->id(), "ret" => post_ru(), "cbfc_pg" => $this->_get_page($o), "edit_num" => $_GET["edit_num"])),
			"gen_ctr_res" => $gen_ctr_res,
		));
		// i have to remember the result of the controller, if i want to make it available after cb_form_chain_entry saving
		// too
		$_SESSION['gen_ctr_res_value'] = (empty($gen_ctr_res)) ? $_SESSION['gen_ctr_res_value'] : $gen_ctr_res;

		$this->_do_prev_next_pages($o);

		return $this->parse();
	}

	function _do_gen_ctr($o)
	{
		$page = $this->_get_page($o);
		$hdrs = safe_array($o->meta("cfs_headers"));
		if (is_oid($hdrs[$page]["gen_ctr"]) && $this->can("view", $hdrs[$page]["gen_ctr"]))
		{
			$ctr_o = obj($hdrs[$page]["gen_ctr"]);
			$ctr_i = $ctr_o->instance();
			$val = array();
			$ctr_i->check_property($ctr_o->id(), $o, $val);
			return $val["value"];
		}
	}

	function _do_prev_next_pages($o)
	{
		$pgs = array_values($this->_get_page_list($o));
		$cur_pg = $this->_get_page($o);

		$hd = safe_array($o->meta("cfs_headers"));
		if ($hd[$cur_pg]["has_save"] == 1)
		{
			$this->vars(array(
				"SAVE_BUTTON" => $this->parse("SAVE_BUTTON")
			));
		}

		$np = false;
		for($i = 0; $i < count($pgs); $i++)
		{
			if ($pgs[$i+1] == $cur_pg)
			{
				$this->vars(array(
					"prev_link" => aw_url_change_var(array("cbfc_pg" => $pgs[$i], "do_confirm" => NULL, "display" => NULL, "edit_num" => NULL))
				));
				$this->vars(array(
					"PREV_PAGE" => $this->parse("PREV_PAGE")
				));
			}
			if ($pgs[$i-1] == $cur_pg)
			{
				$this->vars(array(
					"next_link" => aw_url_change_var(array("cbfc_pg" => $pgs[$i], "display" => NULL, "edit_num" => NULL))
				));
				$this->vars(array(
					"NEXT_PAGE" => $this->parse("NEXT_PAGE")
				));
				$np = true;
			}
		}
		
		$fd = $this->_get_forms_for_page($o, $cur_pg);
		$ed = max(1, $_GET["edit_num"]);
		if ($fd[0]["repeat_fix"] == 1 && $ed < $fd[0]["rep_cnt"])
		{
			$this->vars(array(
				"next_link" => aw_url_change_var(array("edit_num" => $ed+1))
			));
			$this->vars(array(
				"NEXT_PAGE" => $this->parse("NEXT_PAGE")
			));
			$np = true;
		}

		if (!$np)
		{
			if ($o->prop("confirm_sep_page") == 1)
			{
				$this->vars(array(
					"next_link" => aw_url_change_var(array("do_confirm" => 1, "display" => NULL, "edit_num" => NULL))
				));
				$this->vars(array(
					"NEXT_PAGE" => $this->parse("NEXT_PAGE"),
				));
			}
			else
			{
				$this->vars(array(
					"CONFIRM" => $this->parse("CONFIRM"),
				));
			}
		}
	}

	/**

		@attrib name=submit_data nologin="1"

	**/
	function submit_data($arr)
	{
		// save data to session during the form filling
		// then only when the user clicks confirm, save to objects

		// but we gots to check submit controllers here :(
		$ps = array();

		$ctr_i = get_instance("cfg/cfgcontroller");

		$errors = array();

		$_SESSION["no_cache"] = 1;
		foreach(safe_array($arr) as $k => $data)
		{
			if ($k{0} == "f" && $k{1} == "_")
			{
				// this is form entry
				list($tmp, $wf_id, $num) = explode("_", $k);

				$wf = obj($wf_id);
				if (!isset($ps[$wf_id]))
				{
					$wf_i = $wf->instance();
					$ps[$wf_id] = $wf_i->get_props_from_wf(array("id" => $wf_id));
				}
				foreach($ps[$wf_id] as $pn => $pd)
				{
					$ctr = safe_array($pd["controllers"]);
					if (count($ctr))
					{
						$ok = true;
						foreach($ctr as $ctr_id)
						{
							$pd["value"] = &$data[$pn];
							if ($ctr_i->check_property($ctr_id, 0, $pd, $arr, $data, $wf) != PROP_OK)
							{
								$ok = false;
								$co = obj($ctr_id);

								$errmsg = str_replace("%caption", $pd["caption"], $co->prop("errmsg"));
								if ($errmsg == "")
								{
									$errmsg = "Viga";
								}
								$errors[$wf_id][$num][$pn] = $errmsg;
							}
						}
					}

					// if the property is a damn file upload, then we are sorta fucked. so try to come up with some sort of a clever way
					// to get around that
					if (strpos($pd["name"], "userfile") !== false)
					{
						// get the uploaded file from the files array and store it ... somewhere. try to store it in the temp 
						// folder for now
						$fn = tempnam(aw_ini_get("server.tmpdir"), "cbfc_f");
						if (is_uploaded_file($_FILES[$k]["tmp_name"][$pd["name"]]["file"]))
						{
							move_uploaded_file($_FILES[$k]["tmp_name"][$pd["name"]]["file"], $fn);
							$data[$pd["name"]] = $fn;
							$_SESSION["cbfc_file_data"][$wf_id][$num][$pd["name"]]["name"] = $_FILES[$k]["name"][$pd["name"]]["file"];
							$_SESSION["cbfc_file_data"][$wf_id][$num][$pd["name"]]["mtype"] = $_FILES[$k]["type"][$pd["name"]]["file"];
						}
						else
						{
							$data[$pd["name"]] = $_SESSION["cbfc_data"][$wf_id][$num][$pd["name"]];
						}
					}
				}

				if (is_oid($_eid = $_SESSION["cbfc_data"][$wf_id][$num]["__entry_id"]))
				{
					$this->_update_entry_data_obj(obj($wf_id), $_eid, $data);
					$data["__entry_id"] = $_eid;
				}
				$_SESSION["cbfc_data"][$wf_id][$num] = $data;
			}
		}
		if (count($errors))
		{
			$_SESSION["cbfc_errors"] = $errors;
			return $arr["ret"];
		}
		if ($arr["confirm"] != "")
		{
			return $this->submit_confirm($arr);
		}
		if ($arr["goto_next"] != "")
		{
			$fd = $this->_get_forms_for_page(obj($arr["id"]), $arr["cbfc_pg"]);
			$ed = max(1, $arr["edit_num"]);
			if ($fd[0]["repeat_fix"] == 1 && $ed < $fd[0]["rep_cnt"])
			{
				return aw_url_change_var("edit_num", $ed+1, $arr["ret"]);
			}

			$pgs = $this->_get_page_list(obj($arr["id"]));
			$prev = false;
			foreach($pgs as $pg)
			{
				if ($prev == $arr["cbfc_pg"])
				{
					return aw_url_change_var("edit_num", NULL, aw_url_change_var("cbfc_pg", $pg, $arr["ret"]));
				}
				$prev = $pg;
			}
			return aw_url_change_var("edit_num", NULL, aw_url_change_var("do_confirm", 1, $arr["ret"]));
		}
		else
		if ($arr["goto_prev"] != "")
		{
			$fd = $this->_get_forms_for_page(obj($arr["id"]), $arr["cbfc_pg"]);
			$ed = max(1, $arr["edit_num"]);
			if ($fd[0]["repeat_fix"] == 1 && $ed > 1)
			{
				return aw_url_change_var("edit_num", $ed-1, $arr["ret"]);
			}

			$pgs = $this->_get_page_list(obj($arr["id"]));
			$prev = false;
			foreach($pgs as $pg)
			{
				if ($pg == $arr["cbfc_pg"])
				{
					return aw_url_change_var("edit_num", NULL, aw_url_change_var("cbfc_pg", $prev, $arr["ret"]));
				}
				$prev = $pg;
			}
		}

		// if this is an unbounded repeating form, advance to the next entry after save
		$fd = $this->_get_forms_for_page(obj($arr["id"]), $arr["cbfc_pg"]);
		$ed = max(1, $arr["edit_num"]);
		if ($fd[0]["rep"] == 1 && $fd[0]["repeat_fix"] == 0)
		{
			return aw_url_change_var("edit_num", $ed+1, $arr["ret"]);
		}
		
		return $arr["ret"];
	}

	function _do_confirm_view($o)
	{
		$this->read_template("show_confirm.tpl");

		if ($this->can("view", $o->prop("confirm_view_ctl")))
		{
			$fc = get_instance(CL_FORM_CONTROLLER);
			$form_str = $fc->eval_controller(
				$o->prop("confirm_view_ctl"),
				$o
			);
		}
		else
		{
			$form_str = "";
			// for each page
			$pgs = $this->_get_page_list($o);
			foreach($pgs as $pg)
			{
				// for each form on page
				$forms = $this->_get_forms_for_page($o, $pg);
				foreach($forms as $form_dat)
				{
					if ($form_dat["rep_cnt"] > 1 || ($this->is_template("FORM_MUL") && $form_dat["rep"] == 1))
					{
						$form_str .= $this->_display_data_table($o, $form_dat);
					}
					else
					{
						$form_str .= $this->_display_data($o, $form_dat);
					}
				}
			}
		}

		$this->vars(array(
			"forms" => $form_str,
			"reforb" => $this->mk_reforb("submit_confirm", array("id" => $o->id(), "ret" => post_ru(), "cbfc_pg" => $this->_get_page($o))),
			"prev_link" => aw_url_change_var(array("display" => NULL, "do_confirm" =>  NULL)),
			"gen_ctr_res" => $_SESSION['gen_ctr_res_value'],
		));

		return $this->parse();
	}

	/**

		@attrib name=submit_confirm nologin="1"

	**/
	function submit_confirm($arr)
	{
		$o = obj($arr["id"]);

		// save data from session to objects

		$_SESSION["no_cache"] = 1;
		
		// first, entry object
		if ($this->can("view", $_SESSION["cbfc_current_entry_id"]))
		{
			$entry = obj($_SESSION["cbfc_current_entry_id"]);
			$entry->set_name($this->_get_entry_name($o));
			$entry->save();
			// do the crappy, but quicker and safer method - delete all connected entries and create new ones
			foreach($entry->connections_from(array("type" => "RELTYPE_ENTRY")) as $c)
			{
				$eo = $c->to();
				$eo->delete(true);
			}
		}
		else
		{
			$entry = obj();
			$entry->set_parent($this->_get_parent($o));
			$entry->set_class_id(CL_CB_FORM_CHAIN_ENTRY);
			$entry->set_name($this->_get_entry_name($o));
			$entry->set_prop("cb_form_id", $o->id());
			$entry->save();
		}

		// then for each form, data objects in entry object
		// for each page
		$pgs = $this->_get_page_list($o);
		foreach($pgs as $pg)
		{
			// for each form on page
			$forms = $this->_get_forms_for_page($o, $pg);
			foreach($forms as $form_dat)
			{
				$wf = obj($form_dat["form"]);

				for($i = 0; $i < $form_dat["rep_cnt"]; $i++)
				{
					$dat = $_SESSION["cbfc_data"][$form_dat["form"]][$i];

					if ($this->_is_empty($dat))
					{
						continue;
					}

					$this->_create_entry_data_obj($wf, $entry, $dat);
				}
			}
		}
		
		// send confirm and order mails
		$this->_send_order_mail($o, $entry);
		$this->_send_confirm_mail($o, $entry);

		unset($_SESSION["cbfc_data"]);

		$_SESSION["cbfc_last_entry"] = $entry->id();

		$rd = $o->meta("redir");
		if ($this->can("view", $rd[aw_global_get("lang_id")]))
		{
			return obj_link($rd[aw_global_get("lang_id")]);
		}

		return aw_url_change_var(
			"cbfc_pg", 
			NULL, 
			aw_url_change_var(
				"do_confirm", 
				NULL, 
				aw_url_change_var(
					"display", 
					1, 
					$arr["ret"]
				)
			)
		);
	}

	function _get_entry_data_name($wf, $data)
	{
		$name = array();
		foreach(safe_array($wf->prop("obj_name")) as $p)
		{
			$name[] = $data[$p];
		}
		return join(" ", $name);
	}

	function _get_entry_name($o)
	{
		$name = array();

		$f = $o->prop("entry_name_form");
		if (!$f)
		{
			return "";
		}

		foreach(safe_array($o->prop("entry_name_el")) as $el)
		{
			$name[] = $_SESSION["cbfc_data"][$f][0][$el];
		}

		return join(" ", $name);
	}

	function _is_empty($arr)
	{
		foreach($arr as $k => $v)
		{
			if ($v != "")
			{
				return false;
			}
		}
		return true;
	}

	function _create_entry_data_obj($wf, $entry, $dat)
	{
		$o = obj();
		$o->set_class_id(CL_REGISTER_DATA);
		$o->set_parent($entry->id());

		// set cfgform_id and object type to meta
		$cf = $wf->get_first_obj_by_reltype("RELTYPE_CFGFORM");
		$o->set_meta("cfgform_id", $cf->id());

		$ot = $wf->get_first_obj_by_reltype("RELTYPE_OBJECT_TYPE");
		$o->set_meta("object_type", $ot->id());
	
		$o->set_meta("webform_id", $wf->id());

		$o->set_name($this->_get_entry_data_name($wf, $dat));

		$props = $o->get_property_list();

		$metaf = array();
		$file_ids = array();
		foreach($dat as $k => $v)
		{
			if ($props[$k]["type"] == "date_select")
			{
				$v = date_edit::get_timestamp($v);
			}
			else
			if ($props[$k]["type"] == "text")
			{
				$metaf[$k] = $v;
			}
			else
			if ($props[$k]["type"] == "releditor" && strpos($k, "userfile") !== false && $_SESSION["cbfc_data"][$wf->id()][0][$k] != "")
			{
				// handle file upload save
				$f = get_instance(CL_FILE);
				$file_ids[$props[$k]["reltype"]] = $f->save_file(array(
					"name" => $_SESSION["cbfc_file_data"][$wf->id()][0][$k]["name"],
					"type" => $_SESSION["cbfc_file_data"][$wf->id()][0][$k]["mtype"],
					"content" => $this->get_file(array("file" => $_SESSION["cbfc_data"][$wf->id()][0][$k])),
					"parent" => $o->parent(),
				));
			}

			if ($o->is_property($k))
			{
				$o->set_prop($k, $v);
			}
		}
		$o->set_meta("metaf", $metaf);
		$o->save();

		foreach($file_ids as $_rt => $_fid)
		{
			$o->connect(array("to" => $_fid, "type" => $_rt));
		}

		$entry->connect(array(
			"to" => $o->id(),
			"reltype" => "RELTYPE_ENTRY"
		));
	}

	function _send_order_mail($o, $entry)
	{
		if (!$o->prop("mail_to"))
		{
			return;
		}

		$i = $entry->instance();
		$html = $i->show(array(
			"id" => $entry->id(),
			"from" => "mail"
		));

		$to_arr = array();
		if ($o->prop("mail_to") != "")
		{
			$to_arr = explode(",", $o->prop("mail_to"));
		}

		if ($o->prop("mail_to_form"))
		{
			foreach($entry->connections_from(array("type" => "RELTYPE_ENTRY")) as $c)
			{
				$do = $c->to();
				if ($do->meta("webform_id") == $o->prop("mail_to_form"))
				{
					break;
				}
				$do = NULL;
			}
			if ($do)
			{
				$d_props = $do->get_property_list();
				$to_prop = $d_props[$o->prop("mail_to_prop")];
				if ($to_prop["type"] == "classificator")
				{
					$v = $do->prop($to_prop["name"]);
					if (is_oid($v) && $this->can("view", $v))
					{
						$v = obj($v);
						if ($v->comment() != "")
						{
							$to_arr[] = $v->comment();
						}
					}
				}
				else
				{
					$to_arr[] = $do->prop_str($to_prop["name"]);
				}
			}
		}

		$mailer = get_instance("protocols/mail/aw_mail");
		foreach($to_arr as $to)
		{
			$mailer->clean();
			$mailer->create_message(array(
				"froma" => $o->prop("mail_from_addr"),
				"fromn" => $o->prop("mail_from_name"),
				"subject" => $o->prop("mail_subj"),
				"to" => $to,
				"body" => "see on html kiri",
			));

			$mailer->htmlbodyattach(array(
				"data" => $html,
			));

			if ($o->prop("mail_add_pdf"))
			{
				$pdf_c = get_instance("core/converters/html2pdf");
				$pdf_content = $pdf_c->convert(array("source" => $html));
				$mailer->fattach(array(
					"content" => $pdf_content,
					"contenttype" => "application/pdf",
					"name" => "andmed.pdf"
				));
			}

			if ($this->can("view", $o->prop("mail_content_ctr")))
			{
				$fc = get_instance(CL_FORM_CONTROLLER);
				$fc->eval_controller(
					$o->prop("mail_content_ctr"),
					$mailer,
					$o,
					$entry
				);
			}

			$mailer->gen_mail();
		}
	}

	function _send_confirm_mail($o, $entry)
	{
		if (!$o->prop("send_confirm_mail"))
		{
			return;
		}

		$form = $o->prop("confirm_mail_to_form");
		if (!is_oid($form) || !$this->can("view", $form))
		{
			return;
		}
		
		foreach($entry->connections_from(array("type" => "RELTYPE_ENTRY")) as $c)
		{
			$d = $c->to();
			if ($d->meta("webform_id") == $form)
			{
				break;
			}
			$d = false;
		}

		if (!$d)
		{
			return;
		}

		$from = $o->prop("mail_from_addr");
		if ($o->prop("mail_from_name") != "")
		{
			$from = $o->prop("mail_from_name")." <$from>";
		}

		$d_props = $d->get_property_list();
		$to_prop = $d_props[$o->prop("confirm_mail_to_prop")];
		if ($to_prop["type"] == "classificator")
		{
			$v = $d->prop($to_prop["name"]);
			if (is_oid($v) && $this->can("view", $v))
			{
				$v = obj($v);
				$to = $v->comment();
			}
		}
		else
		{
			$to = $d->prop_str($to_prop["name"]);
		}
		if ($to != "")
		{

			if ($this->can("view", $o->prop("confirm_mail_content_ctr")))
			{
				$fc = get_instance(CL_FORM_CONTROLLER);
				return $fc->eval_controller(
					$o->prop("confirm_mail_content_ctr"),
					array("to" => $to, "from" => $from),
					$o,
					$entry
				);
			}

			send_mail(
				$to,	// to
				$o->prop("confirm_mail_subj"), // subj 
				$o->prop("confirm_mail"), // msg
				"From: $from\n" // headers
			);
		}
	}

	function _display_data_table($o, $fd)
	{
		if ($this->is_template("FORM_MUL"))
		{
			return $this->_display_data_table_tpl($o, $fd);
		}
		// make table via component
		
		$t = new aw_table(array("layout" => "generic"));

		$wf = get_instance(CL_WEBFORM);
		$props = $wf->get_props_from_wf(array(
			"id" => $fd["form"]
		));

		foreach($props as $pn => $pd)
		{
			$t->define_field(array(
				"name" => $pn,
				"caption" => $pd["caption"],
				"align" => "center"
			));
		}
		// go over all datas
		for($i = 0; $i < $fd["rep_cnt"]; $i++)
		{
			$inf = $_SESSION["cbfc_data"][$fd["form"]][$i];
			if (!$this->_is_empty($inf))
			{
				$row = array();
				foreach($props as $pn => $pd)
				{
					$row[$pn] = $this->_value_from_data($pd, $inf[$pn]);
				}
				$t->define_data($row);
			}
		}

		$ret = $t->draw();
		return $ret;
	}

	function _display_data_table_tpl($o, $fd)
	{
		$wf = get_instance(CL_WEBFORM);
		$props = $wf->get_props_from_wf(array(
			"id" => $fd["form"]
		));
		$fe_str = "";
		for($i = 0; $i < $fd["rep_cnt"]; $i++)
		{
			$pr_str = "";
			$inf = $_SESSION["cbfc_data"][$fd["form"]][$i];
			if (!$this->_is_empty($inf))
			{
				foreach($props as $pn => $pd)
				{
					$this->vars(array(
						"caption" => $pd["caption"],
						"value" => $this->_value_from_data($pd,$inf[$pn]) 
					));
					$pr_str .= $this->parse("E_PROPERTY");
				}
				$this->vars(array(
					"E_PROPERTY" => $pr_str
				));
				$fe_str .= $this->parse("FORM_ENTRY");
			}
		}
		$form_obj = obj($fd["form"]);
		$this->vars(array(
			"form_name" => $form_obj->name(),
			"FORM_ENTRY" => $fe_str
		));
		$ret = $this->parse("FORM_MUL");

		$ap = get_instance("alias_parser");
		$ap->parse_oo_aliases($fd["form"], $ret);

		$this->vars(array("FORM_MUL" => ""));
		return $ret;
	}

	function _display_data($o, $fd)
	{
		$wf = get_instance(CL_WEBFORM);
		$props = $wf->get_props_from_wf(array(
			"id" => $fd["form"]
		));
		$this->_apply_view_controllers($props, obj($fd["form"]), $o);
		
		$inf = $_SESSION["cbfc_data"][$fd["form"]][0];

		foreach($props as $pn => $pd)
		{
			if ($props[$pn]["type"] == "date_select" && ($inf[$pn] == 0  || $inf[$pn] == -1))
			{
				continue;
			}
			$val = $this->_value_from_data($pd,$inf[$pn]);

			if (strpos($pn, "userfile") !== false)
			{
				$val = html::href(array(
					"url" => $this->mk_my_orb("show_up_file", array("wfid" => $fd["form"], "i" => 0, "rpn" => $pn)),
					"caption" => $_SESSION["cbfc_file_data"][$fd["form"]][0][$pn]["name"]
				));
			}

			$this->vars(array(
				"caption" => $pd["caption"],
				"value" => $val == "" ? "&nbsp;" : $val
			));
	
			$ret .= $this->parse("PROPERTY");
		}

		$form_obj = obj($fd["form"]);
		$this->vars(array(
			"PROPERTY" => $ret,
			"form_name" => $form_obj->name()
		));
		return $this->parse("FORM");
	}

	function _entry_tb($arr)
	{
		$tb =& $arr["prop"]["toolbar"];

		if ($arr["request"]["group"] != "entries_con")
		{
			$tb->add_button(array(
				"name" => "confirm",
				"img" => "save.gif",
				"tooltip" => t("Kinnita"),
				"action" => "confirm_entries"
			));
		}

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"confirm" => t("Oled kindel et soovid sisestusi kustutada?"),
			"action" => "delete_entries"
		));

		$tb->add_button(array(
			"name" => "export",
			"img" => "export.gif",
			"tooltip" => t("Ekspordi"),
			"action" => "export_entries"
		));
	}

	function _init_entry_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y / H:i"
		));

		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
                        "name" => "createdby_ip",
                        "caption" => t("IP"),
                        "align" => "center",
                        "sortable" => 1
                ));

		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _entry_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_entry_table($t);

		$ol = new object_list(array(
			"parent" => $this->_get_parent($arr["obj_inst"]),
			"class_id" => CL_CB_FORM_CHAIN_ENTRY,
			"confirmed" => $arr["request"]["group"] == "entries_con" ? 1 : new obj_predicate_not(1),
			"site_id" => array(),
			"lang_id" => array()
		));
//		$t->data_from_ol($ol, array("change_col" => "name"));
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => $o->name(),
				"created" => $o->created(),
				"createdby" => $o->createdby(),
				"oid" => $o->id(),
				"view" => html::href(array(
					"url" => $this->mk_my_orb("show", array("id" => $o->id(), "return_url" => get_ru()), CL_CB_FORM_CHAIN_ENTRY),
					"caption" => t("Vaata")
				)) ." | ".html::href(array(
					"url" => str_replace("/automatweb", "", $this->mk_my_orb("show_pdf", array("id" => $o->id(), "return_url" => get_ru()), CL_CB_FORM_CHAIN_ENTRY)),
					"caption" => t("PDF")
				)),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("showe", array("id" => $o->id(), array("return_url" => get_ru()))),
					"caption" => t("Change")
				)),
				"createdby_ip" => $this->db_fetch_field("SELECT ip FROM syslog WHERE oid = ".$o->id()." LIMIT 1", "ip")
			));
		}
		$t->set_default_sortby("created");
		$t->set_default_sorder("desc");
	}

	/**

		@attrib name=confirm_entries

	**/
	function confirm_entries($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array(
				"oid" => $arr["sel"]
			));
			$ol->set_prop("confirmed", 1);
		}
		return $arr["post_ru"];
	}

	/**

		@attrib name=delete_entries

	**/
	function delete_entries($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array(
				"oid" => $arr["sel"],
				"lang_id" => array(),
				"site_id" => array()
			));
			$ol->delete();
		}
		return $arr["post_ru"];
	}

	function _get_parent($o)
	{
		if (is_oid($o->prop("entry_folder")) && $this->can("view", $o->prop("entry_folder")))
		{
			return $o->prop("entry_folder");
		}
		return $o->id();
	}

	function _value_from_data($pd, $val)
	{
		if ($pd["type"] == "classificator")
		{
			if (is_array($val))
			{
				if (count($val))
				{
					$ol = new object_list(array("oid" => $val, "lang_id" => array(), "site_id" => array()));
					$val = join(", ", $ol->names());
				}
				else
				{
					$val = "";
				}
			}
			if (is_oid($val) && $this->can("view", $val))
			{
				$tmp = obj($val);
				$val = $tmp->name();
			}	
		}
		if ($pd["type"] == "date_select")
		{
			if (date_edit::get_timestamp($val) == -1 || date_edit::get_timestamp($val) == 0)
			{
				return "";
			}
			$val = date("d.m.Y", date_edit::get_timestamp($val));
		}

		return $val;
	}

	function _apply_view_controllers(&$props, $wf, $o)
	{
		$fd = $this->_get_forms_for_page($o, $this->_get_page($o));
		foreach($fd as $e)
		{
			if ($e["form"] == $wf->id())
			{
				$fd = $e;
				break;
			}
		}

		foreach($props as $k => $v)
		{
			if (is_array($v["view_controllers"]) && count($v["view_controllers"]))
			{
				$ci = get_instance(CL_CFG_VIEW_CONTROLLER);
				foreach($v["view_controllers"] as $ctr_id)
				{
					for($i = 0; $i < $fd["rep_cnt"]; $i++)
					{
						$tmp = $_SESSION["cbfc_data"][$wf->id()][$i];
						$tmp["__entry_num"] = $i;
						$cpv = $ci->check_property($v, $ctr_id, $tmp);
						if ($cpv == PROP_IGNORE)
						{
							unset($props[$k]);
							continue;
						}
					}
				}
			}
		}
	}

	function _html_from_props($form_dat, $props, $ot, $wf, $o, $num_to_show = NULL)
	{
		$i = 0;
		if ($num_to_show !== NULL)
		{
			$i = $num_to_show;
			$form_dat["rep_cnt"] = $i+1;
		}
		$def_caption_style = $wf->prop('def_caption_style');
		$def_prop_style = $wf->prop('def_prop_style');

		classload('layout/active_page_data');
		active_page_data::add_site_css_style($def_caption_style);
		active_page_data::add_site_css_style($def_prop_style);

		for(; $i < $form_dat["rep_cnt"]; $i++)
		{
			if ((!is_array($_SESSION["cbfc_data"][$wf->id()][$i])) && $form_dat["def_ctr"])
			{
				$ci = get_instance(CL_CFGCONTROLLER);
				$ci->check_property($form_dat["def_ctr"], $wf->id(), $_SESSION["cbfc_data"][$wf->id()][$i], $_REQUEST, $i, $o);
			}

			$nps = array();
			// insert values as well
			foreach($props as $k => $v)
			{
				if ($v["invisible"] == 1)
				{
					continue;
				}
				if (($_err = $_SESSION["cbfc_errors"][$wf->id()][$i][$k]) != "")
				{
					$nps[$k."_err"] = array(
						"name" => $k."_err",
						"type" => "text",
						"no_caption" => 1,
						"value" => "<span class=\"cbfcerror\">".$_err."</span>",
						"store" => "no"
					);
				}
				if ($v["subtitle"] != 1 && $v["type"] != "text" && $v["store"] != "no")
				{
					$v["value"] = $_SESSION["cbfc_data"][$wf->id()][$i][$k];
				}
				else
				{
					$v["value"] = nl2br($v["value"]);
				}
				if ($v['type'] == 'date_select' && empty($v['value']))
				{
					$v['value'] = $v['defaultx'];
				}
				// if it is a text type property, insert a hidden element after the text so that the value gets submitted
				if ($v["type"] == "text")
				{
					$v["value"] .= html::hidden(array(
						"name" => "f_".$wf->id()."_".$i."[$k]",
						"value" => $_SESSION["cbfc_data"][$wf->id()][$i][$k] //$v["value"]
					)) .$_SESSION["cbfc_data"][$wf->id()][$i][$k];
				}
				else
				{
					$v["value"] = $_SESSION["cbfc_data"][$wf->id()][$i][$k];
				}

				unset($v["subtitle"]);

				if ($v["type"] == "text" && $v["caption"] == "")
				{
					//$v["value"] = $v["caption"];
					$v["no_caption"] = 1;
				}

				if ($v["type"] == "date_select" && !$v["value"] && $v["defaultx"])
				{
					$v["value"] = $v["defaultx"];
				}

				if ($v["invisible_name"] == 1)
				{
					$v["no_caption"] = 1;
				}
				$nps[$k] = $v;
			}

			$props = $nps;
			$rd = get_instance(CL_REGISTER_DATA);
			$els = $rd->parse_properties(array(
				"properties" => $props,
				"name_prefix" => "f_".$wf->id()."_".$i,
				"object_type_id" => $ot->id()
			));
		//	$htmlc = get_instance("cfg/htmlclient");
			classload('cfg/htmlclient');
			$htmlc = new htmlclient(array(
				'template' => 'real_webform.tpl',
			//	'styles' => safe_array($wf->meta('xstyles'))
			));


			
			// XXX
			// Adding webform style rules into page.
			// Actually, I am not sure if form_chain should know _anything_ about how webform looks like and how elements are placed
			// maybe i should use here a method that gives me webforms html and if i have to change any data (form element names etc.)
			// then webform has methods for that --dragut
			$styles = array();
			foreach ( safe_array( $wf->meta('xstyles') ) as $key => $value )
			{
				$styles['f_'.$wf->id().'_'.$i.'_'.$key] = $value;
				foreach ( $value as $style_id )
				{
					active_page_data::add_site_css_style($style_id);
				}
			}
			$htmlc->start_output();
			foreach($els as $pn => $pd)
			{
				// rewrite file does not exist thingie if it is so
				if (strpos($pd["name"], "f_".$wf->id()."_".$i."[userfile") !== false && strpos($pd["name"], "filename") !== false)
				{
					list(,,,$r_pn) = explode("_", $pn);
					if ($_SESSION["cbfc_data"][$wf->id()][$i][$r_pn] != "")
					{
						$pd["value"] = html::href(array(
							"url" => $this->mk_my_orb("show_up_file", array("wfid" => $wf->id(), "i" => $i, "rpn" => $r_pn)),
							"caption" => $_SESSION["cbfc_file_data"][$wf->id()][$i][$r_pn]["name"]
						));
					}
					else
					{
						$pd["value"] = "";
					}

					if ($this->is_template("FN_CAPTION"))
					{
						$pd["caption"] = $this->parse("FN_CAPTION");
						$pd["comment"] = "";
					}
				}
				else
				if (strpos($pd["name"], "f_".$wf->id()."_".$i."[userfile") !== false)
				{
					if ($this->is_template("FU_CAPTION"))
					{
						$pd["caption"] = $this->parse("FU_CAPTION");
						$pd["comment"] = "";
					}
					else
					{

						$element_name_parts = explode('_', $pn);
						$pd['caption'] = $props[$element_name_parts[3]]['caption'];
					}
				}

				// XXX
				// Add elements layout info and styles to htmlclient so it knows how to draw them
				// Actually, I am not sure if form_chain should know _anything_ about how webform looks like and how elements are placed
				// maybe i should use here a method that gives me webforms html and if i have to change any data (form element names etc.)
				// then webform has methods for that --dragut
				if (empty($styles[$pn]['caption']))
				{
					$styles[$pn]['caption'] = $def_caption_style;
				}
				if (empty($styles[$pn]['prop']))
				{
					$styles[$pn]['prop'] = $def_prop_style;
				}
				$pd['style'] = $styles[$pn];
				$pd['capt_ord'] = $pd['wf_capt_ord'];
				if ($pd['type'] == 'textbox' || $pd['type'] == 'textarea')
				{
					if (!empty($pd['width']))
					{
						if ($pd['type'] == 'textbox')
						{
							$pd['size'] = $pd['width'];
						}
						else
						{
							$pd['cols'] = $pd['width'];
						}
					}

					if (!empty($pd['height']) && $pd['type'] == 'textarea')
					{
						$pd['rows'] = $pd['height'];
					}
				}

				$htmlc->add_property($pd);
			}
			$htmlc->finish_output();

			$html .= $htmlc->get_result(array(
				"raw_output" => 1
			));
		}

		$ap = get_instance("alias_parser");
		$ap->parse_oo_aliases($wf->id(), $html);

		return $html;
	}

	function _html_table_from_props($form_dat, $props, $ot, $wf, $o)
	{
		// header
		$h = "";
		foreach($props as $pn => $pd)
		{
			$this->vars(array(
				"caption" => $pd["caption"]
			));
			$h .= $this->parse("HEADER");
		}

		for($i = 0; $i < $form_dat["rep_cnt"]; $i++)
		{
			$prefix = "f_".$wf->id()."_".$i;
			$els = "";

			foreach($props as $k => $v)
			{
				if (!empty($v["defaultx"]) && !isset($_SESSION["cbfc_data"][$wf->id()][$i][$k]))
				{
					$_SESSION["cbfc_data"][$wf->id()][$i][$k] = $v["defaultx"];
				}
				$props[$k]["value"] = $_SESSION["cbfc_data"][$wf->id()][$i][$k];
			}

			if ($this->_has_errors($wf->id(), $i))
			{
				$forms .= $this->_display_table_errors($props, $wf->id(), $i);
			}

			$rd = get_instance(CL_REGISTER_DATA);
			$pels = $rd->parse_properties(array(
				"properties" => $props,
				"name_prefix" => "f_".$wf->id()."_".$i,
				"object_type_id" => $ot->id()
			));

			foreach($pels as $pn => $pd)
			{
				switch($pd["type"])
				{
					case "date_select":
						$el = html::date_select($pd);
						break;

					case "select":
						$el = html::select($pd);
						break;

					default:
						$pd["size"] = 20;
						$el = html::textbox($pd);
						break;
				}

				$this->vars(array(
					"element" => $el
				));
				$els .= $this->parse("ELEMENT");
			}

			$this->vars(array(
				"ELEMENT" => $els
			));
			$tmp = $this->parse("FORM");
			$forms .= $tmp;
		}

		$ap = get_instance("alias_parser");
		$ap->parse_oo_aliases($wf->id(), $h);

		$this->vars(array(
			"HEADER" => $h,
			"FORM" => $forms
		));

		return $this->parse("TABLE_FORM");
	}

	function _get_titles($o)	
	{
		$hdrs = safe_array($o->meta("cfs_headers"));

		$ret = array();
		foreach($hdrs as $pg => $i)
		{
			$ret[$pg] = $i["name"];
		}
		ksort($ret);
		return $ret;
	}
	
	function _draw_page_titles($o)
	{
		$titles = $this->_get_titles($o);
		$page = $this->_get_page($o);
		$ts = array();
		foreach($titles as $pg => $title)
		{
			$this->vars(array(
				"title" => $title,
				"title_url" => aw_url_change_var("cbfc_pg", $pg)
			));

			if ($pg == $page)
			{
				$ts[] = $this->parse("TITLE_SEL");
			}
			else
			if ($this->is_template("TITLE_NO_LINK") && !$this->_page_is_filled($o, $pg))
			{
				$ts[] = $this->parse("TITLE_NO_LINK");
			}
			else
			{
				$ts[] = $this->parse("TITLE");
			}
		}

		$this->vars(array(
			"TITLE" => join($this->parse("TITLE_SEP"), $ts),
			"TITLE_SEL" => "",
			"TITLE_SEP" => ""
		));
	}

	function _display_table_errors($pels, $wf_id, $i)
	{
		$els = "";
		foreach($pels as $pn => $pd)
		{
			$this->vars(array(
				"element" => "<font color=\"red\">".$_SESSION["cbfc_errors"][$wf_id][$i][$pn]."</font>"
			));
			$els .= $this->parse("ELEMENT");
		}
		$this->vars(array(
			"ELEMENT" => $els
		));
		return $this->parse("FORM");
	}

	function _has_errors($wf_id, $i)
	{
		foreach($_SESSION["cbfc_errors"][$wf_id][$i] as $k => $v)
		{
			if ($v != "")
			{
				return true;
			}
		}
		return false;
	}

	function callback_get_redir($arr)
	{
		$l = get_instance("languages");
		$ll = $l->get_list();
		$ret = array();
		$vals = $arr["obj_inst"]->meta("redir");
		foreach($ll as $lid => $ln)
		{
			$nm = "rd[$lid]";
			$ret[$nm] = array(
				"name" => $nm,
				"type" => "relpicker",
				"reltype" => "RELTYPE_DOC",
				"table" => "objects",
				"field" => "meta",
				"method" => "serialize",
				"value" => $vals[$lid],
				"caption" => sprintf(t("Kuhu suunata p&auml;rast sisestust (%s)"), $ln)
			);
		}

		return $ret;
	}

	function _save_redir($arr)
	{
		foreach(safe_array($arr["request"]["rd"]) as $lid => $selecta)
		{
			if ($this->can("view", $selecta))
			{
				$arr["obj_inst"]->connect(array(
					"to" => $selecta,
					"type" => "RELTYPE_DOC"
				));
			}
		}
		$arr["obj_inst"]->set_meta("redir", $arr["request"]["rd"]);
	}

	/**
		@attrib name=export_entries
	**/
	function export_entries($arr)
	{
		$res = "";
		foreach(safe_array($arr["sel"]) as $item)
		{
			$io = obj($item);
			$ii = $io->instance();
			list($header, $content) = $ii->show_csv($io);
			if ($res == "")
			{
				$res = $header;
			}
			$res .= $content;
		}
		header("Content-type: text/csv");
		header("Content-disposition: inline; filename=entries.csv;");
		die($res);
	}

	function _display_entry_data_table($form_dat, $props, $wf, $o)
	{
		$dat = safe_array($o->meta("entry_tbl"));

		$nprops = array();
		// for all entries for this form
		$row = "";
		for($i = 0; $i < $form_dat["rep_cnt"]; $i++)
		{
			// show row
			$col = "";
			$col_vals = array();
			foreach($props as $pn => $pd)
			{
				$col_inf = $dat[$wf->id()][$pn];
				if ($col_inf["show"] == 1)
				{
					if (!isset($col_vals[$col_inf["col_num"]]))
					{
						$nprops[$pn] = $pd;
					}
					$col_vals[$col_inf["col_num"]] .= 
						$col_inf["sep_before"].
						$this->_value_from_data($pd, $_SESSION["cbfc_data"][$wf->id()][$i][$pn]).
						$col_inf["sep_after"];
//echo "add to col $col_inf[col_num] prop $pn str ".$this->_value_from_data($pd, $_SESSION["cbfc_data"][$wf->id()][$i][$pn])." <br>";
				}
		
			}

			foreach($col_vals as $col_val)
			{
				$this->vars(array(
					"content" => $col_val
				));
				$col .= $this->parse("DT_COL");
			}

			$cht = $this->is_template("CHANGE_TEXT") ? $this->parse("CHANGE_TEXT") : t("Muuda");
			if (!$form_dat["repeat_fix"] && $i == ($form_dat["rep_cnt"] -1 ))
			{
				$cht = $this->is_template("NEW_TEXT") ? $this->parse("NEW_TEXT") : t("Uus");
			}
			$this->vars(array(
				"content" => html::href(array(
					"url" => aw_url_change_var("edit_num", $i+1),
					"caption" => $cht
				))
			));
			$col .= $this->parse("DT_COL");

			if (!$form_dat["repeat_fix"] && $i < ($form_dat["rep_cnt"] -1 ))
			{
				$cht = $this->is_template("DEL_TEXT") ? $this->parse("DEL_TEXT") : t("Kustuta");
				$this->vars(array(
					"content" => html::href(array(
						"url" => aw_url_change_var(array("del_num" =>  $i+1, "del_wf" => $wf->id())),
						"caption" => $cht
					))
				));
				$col .= $this->parse("DT_COL");
			}

			$this->vars(array(
				"DT_COL" => $col
			));
			$row .= $this->parse("DT_ROW");
		}
		

		$this->vars(array(
			"DT_HEADER" => $this->_get_data_table_header($nprops, $form_dat),
			"DT_ROW" => $row
		));

		$this->vars(array(
			"DATA_TABLE" => $this->parse("DATA_TABLE")
		));
	}

	function _get_data_table_header($props, $form_dat = NULL)
	{
		// show header
		$header = "";
		foreach($props as $pn => $pd)
		{
			$this->vars(array(
				"col_name" => $pd["caption"]
			));
			$header .= $this->parse("DT_HEADER");
		}

		$this->vars(array(
			"col_name" => $this->is_template("CHANGE_TEXT") ? $this->parse("CHANGE_TEXT") : t("Muuda")
		));
		$header .= $this->parse("DT_HEADER");

		if (!$form_dat["repeat_fix"])
		{
			$this->vars(array(
				"col_name" => $this->is_template("DEL_TEXT") ? $this->parse("DEL_TEXT") : t("Kustuta")
			));
		}
		$header .= $this->parse("DT_HEADER");

		return $header;
	}

	function _can_show_edit_form($form_dat)
	{
		return max($_GET["edit_num"], 1);
	}

	function _init_cfs_entry_tbl($t)
	{
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "col_num",
			"caption" => t("Mitmes tulp"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sep_before",
			"caption" => t("Eraldaja enne"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sep_after",
			"caption" => t("Eraldaja p&auml;rast"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "in_tbl",
			"caption" => t("N&auml;ita tabelis"),
			"align" => "center"
		));
	}

	function _cfs_entry_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_cfs_entry_tbl($t);

		$dat = safe_array($arr["obj_inst"]->meta("entry_tbl"));
		$d = safe_array($arr["obj_inst"]->meta("d"));

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CF")) as $c)
		{
			if ($d[$c->prop("to")]["data_table"] == 1)
			{
				$els = $this->get_el_picker_from_wf($c->to());
				foreach($els as $pn => $pc)
				{
					if ($pn != "")
					{
						$t->define_data(array(
							"prop" => $pc,
							"in_tbl" => html::checkbox(array(
								"name" => "t[".$c->prop("to")."][$pn][show]",
								"value" => 1,
								"checked" => ($dat[$c->prop("to")][$pn]["show"] == 1)
							)),
							"col_num" => html::textbox(array(
								"name" => "t[".$c->prop("to")."][$pn][col_num]",
								"value" => $dat[$c->prop("to")][$pn]["col_num"],
								"size" => 5
							)),
							"sep_before" => html::textbox(array(
								"name" => "t[".$c->prop("to")."][$pn][sep_before]",
								"value" => $dat[$c->prop("to")][$pn]["sep_before"],
								"size" => 5
							)),
							"sep_after" => html::textbox(array(
								"name" => "t[".$c->prop("to")."][$pn][sep_after]",
								"value" => $dat[$c->prop("to")][$pn]["sep_after"],
								"size" => 5
							)),
						));
					}
				}
			}
		}

		$t->set_sortable(false);
	}

	function get_current_entry_count($fd)
	{
		$count = 0;
		foreach(safe_array($_SESSION["cbfc_data"][$fd["form"]]) as $entry)
		{
			if (!$this->_is_empty($entry))
			{
				$count++;
			}
		}
		return $count;
	}

	/**
		@attrib name=show_up_file nologin="1"
		@param wfid required
		@param i optional
		@param rpn required
	**/
	function show_up_file($arr)
	{
		header("Content-type: ".$_SESSION["cbfc_file_data"][$arr["wfid"]][$arr["i"]][$arr["rpn"]]["mtype"]);
		readfile($_SESSION["cbfc_data"][$arr["wfid"]][$arr["i"]][$arr["rpn"]]);
		die();
	}

	function _load_entry($eid)
	{
		$e = obj($eid);
		$_SESSION["cbfc_data"] = array();
		foreach($e->connections_from(array("RELTYPE_ENTRY")) as $c)
		{
			$rd = $c->to();
			$wf_id = $rd->meta("webform_id");
			$cur_cnt = ((int)$counts_by_wf[$wf_id]++);
			$_SESSION["cbfc_data"][$wf_id][$cur_cnt] = $rd->properties();
			$_SESSION["cbfc_data"][$wf_id][$cur_cnt]["__entry_id"] = $rd->id();

			for ($i = 1; $i < 6; $i++)
			{
				$pn = "userfile".$i;
				$fo = $rd->get_first_obj_by_reltype("RELTYPE_FILE".$i);
				if ($fo)
				{
					$_SESSION["cbfc_file_data"][$wf_id][$cur_cnt][$pn]["name"] = $fo->name();
					$_SESSION["cbfc_file_data"][$wf_id][$cur_cnt][$pn]["mtype"] = $fo->prop("type");
					$_SESSION["cbfc_data"][$wf_id][$cur_cnt][$pn] = $fo->prop("file");
				}

				$pn = "userdate".$i;
				$v = $rd->prop($pn);
				if ($v > 300)
				{
					$_SESSION["cbfc_data"][$wf_id][$cur_cnt][$pn] = array(
						"year" => date("Y", $v),
						"month" => date("m", $v),
						"day" => date("d", $v),
					);
				}
			}
		}
	}

	function callback_get_search($arr)
	{
		$ret = array();
		// get all defined searches from webforms
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CF")) as $c)
		{
			$wf = $c->to();
			$reg = $wf->get_first_obj_by_reltype("RELTYPE_REGISTER");
			if ($reg && $this->can("view", $reg->prop("search_o")))
			{
				$srch = obj($reg->prop("search_o"));
				$srch_i = $srch->instance();

				$req = $arr["request"];
				foreach($req as $k => $v)
				{
					if ($k == $wf->id()."_rsf")
					{
						$req["rsf"] = $v;
					}
				}
				foreach($srch_i->get_sform_properties($srch, $req) as $pn => $pd)
				{
					if ($pd["type"] == "submit")
					{
						$sbt = array($pn, $pd);
					}
					else
					{
						$pn = $wf->id()."_".$pn;
						$pd["name"] = $wf->id()."_".$pd["name"];
						$ret[$pn] = $pd;
					}
				}
			}
		}
		$sbt[1]["caption"] = t("Otsi");
		$ret[$sbt[0]] = $sbt[1];
		return $ret;
	}

	function _init_search_res_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"sortable" => 1,
			"align" => "center",
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"sortable" => 1,
			"align" => "center",
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));

		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "view",
			"caption" => t("Vaata"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"sortable" => 1,
			"align" => "center"
		));
	}

	function _search_res($arr)
	{
		$matches = array();
		// get search results from all webforms, then get all matching 
		$cfs = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CF"));
		$cfs_cnt = count($cfs);
		foreach($cfs as $c)
		{
			$wf = $c->to();
			$reg = $wf->get_first_obj_by_reltype("RELTYPE_REGISTER");
			if ($reg && $this->can("view", $reg->prop("search_o")))
			{
				$srch = obj($reg->prop("search_o"));
				$srch_i = $srch->instance();

				$arr["request"]["search_butt"] = 1;
				$req = $arr["request"];
				foreach($req as $k => $v)
				{
					if ($k == $wf->id()."_rsf")
					{
						$req["rsf"] = $v;
						// if the thing is empty, then don't search from that form
						$has = false;
						foreach(safe_array($v) as $k => $v)
						{
							if ($v != "")
							{
								$has = true;
							}
						}
					}
				}

				list($res_ol, $res_ol_cnt) = $srch_i->get_search_results($srch, $req, array());

				// get all cbf entries from that list
				if ($res_ol->count())
				{
					$c = new connection();
					$cs = $c->find(array(
						"to" => $res_ol->ids(),
						"from.class_id" => CL_CB_FORM_CHAIN_ENTRY
					));
					$tmp = array();
					foreach($cs as $c)
					{
						$tmp[$c["from"]] = $c["from"];
					}
					
					foreach($tmp as $c_id)
					{
						$matches[$c_id] = ((int)$matches[$c_id]) + 1;
					}
				}
			}
		}
	
		// leave all that have matches count that equals number of forms-1
		$tm = array();
		foreach($matches as $m_oid => $m_cnt)
		{
			if ($m_cnt == ($cfs_cnt))
			{
				$tm[$m_oid] = $m_oid;
			}
		}
		$matches = $tm;

		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_search_res_t($t);

		if (count($matches))
		{
			$ol = new object_list(array("oid" => $matches));
		}
		else
		{
			$ol = new object_list();
		}
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => $o->name(),
				"created" => $o->created(),
				"createdby" => $o->createdby(),
				"modified" => $o->modified(),
				"modifiedby" => $o->modifiedby(),
				"view" => html::href(array(
					"url" => $this->mk_my_orb("show", array("id" => $o->id(), "return_url" => get_ru()), CL_CB_FORM_CHAIN_ENTRY),
					"caption" => t("Vaata")
				))." | ".html::href(array(
					"url" => $this->mk_my_orb("show_pdf", array("id" => $o->id(), "return_url" => get_ru()), CL_CB_FORM_CHAIN_ENTRY),
					"caption" => t("PDF")
				)),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("showe", array("id" => $o->id(), array("return_url" => get_ru()))),
					"caption" => t("Change")
				))
			));
		}
	}

	/**
		@attrib name=showe
		@param id required
	**/
	function showe($arr)
	{
		$_SESSION["cbfc_current_entry_id"] = $arr["id"];
		$this->_load_entry($arr["id"]);
		$o = obj($arr["id"]);
		return $this->show(array(
			"id" => $o->prop("cb_form_id")
		));
	}

	function _page_is_filled($o, $page)
	{
		if ($_SESSION["cbfc_current_entry_id"])
		{
			return true;
		}

		$ret = false;
		$forms = $this->_get_forms_for_page($o, $page);
		foreach($forms as $form_dat)
		{
			$entries = $_SESSION["cbfc_data"][$form_dat["form"]];
			if (count($entries))
			{
				$ret = true;
				break;
			}
		}
		return $ret;
	}

	function _update_entry_data_obj($wf, $entry_id,  $dat)
	{
		$o = obj($entry_id);
		$o->set_name($this->_get_entry_data_name($wf, $dat));

		$props = $o->get_property_list();

		$metaf = array();
		$file_ids = array();
		foreach($dat as $k => $v)
		{
			if ($props[$k]["type"] == "date_select")
			{
				$v = date_edit::get_timestamp($v);
			}
			else
			if ($props[$k]["type"] == "text")
			{
				$metaf[$k] = $v;
			}
			else
			if ($props[$k]["type"] == "releditor" && strpos($k, "userfile") !== false && $_SESSION["cbfc_data"][$wf->id()][0][$k] != "")
			{
				// handle file upload save
				$f = get_instance(CL_FILE);
				$file_ids[$props[$k]["reltype"]] = $f->save_file(array(
					"name" => $_SESSION["cbfc_file_data"][$wf->id()][0][$k]["name"],
					"type" => $_SESSION["cbfc_file_data"][$wf->id()][0][$k]["mtype"],
					"content" => $this->get_file(array("file" => $_SESSION["cbfc_data"][$wf->id()][0][$k])),
					"parent" => $o->parent(),
				));
			}

			if ($o->is_property($k))
			{
				$o->set_prop($k, $v);
			}
		}
		$o->set_meta("metaf", $metaf);
		$o->save();

		foreach($file_ids as $_rt => $_fid)
		{
			$o->connect(array("to" => $_fid, "type" => $_rt));
		}
	}
}
?>
