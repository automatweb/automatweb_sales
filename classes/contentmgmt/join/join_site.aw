<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/join/join_site.aw,v 1.83 2009/07/03 11:38:51 instrumental Exp $
// join_site.aw - Saidiga Liitumine 
/*

EMIT_MESSAGE(MSG_USER_JOINED)

@classinfo syslog_type=ST_JOIN_SITE relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@groupinfo general_sub parent=general caption="&Uuml;ldine"
@groupinfo general_ctrl parent=general caption="Kontrollerid"

@default table=objects
@default group=general_sub

	@property name type=textbox table=objects field=name
	@caption Nimi

	@property after_join_url type=textbox field=meta method=serialize 
	@caption P&auml;rast liitumist mine aadressile

	@property obj_folder type=relpicker reltype=RELTYPE_OBJ_FOLDER field=meta method=serialize 
	@caption Kataloog, kuhu salvestatakse objektid

	@property autologin type=checkbox ch_value=1 field=meta method=serialize 
	@caption Kas kasutaja logitakse automaatselt sisse liitumisel

	@property send_join_mail type=checkbox ch_value=1 field=meta method=serialize 
	@caption Kas 7.05.2009saadetakse meil

	@property users_blocked_by_default type=checkbox ch_value=1 field=meta method=serialize 
	@caption Kasutajad vaikimisi blokeeritud

@default group=general_ctrl

	@property check_sbt_controller type=relpicker reltype=RELTYPE_VALIDATE_CTR field=meta method=serialize
	@caption Andmete valideerimise kontroller

@groupinfo props caption="Vormid"

@groupinfo sel_props parent=props caption="Vali elemendid"
@groupinfo mk_pages parent=props caption="Koosta lehed"
@groupinfo page_titles parent=props caption="Lehtede Pealkirjad"
@groupinfo seps parent=props caption="Vahepealkirjad"
@groupinfo prop_settings parent=props caption="Elementide seaded"

@property join_properties type=table store=no group=sel_props
@caption Liitumisel k&uuml;sitavad v&auml;ljad

@property generate_password type=checkbox field=meta method=serialize ch_value=1 group=sel_props
@caption Genereeri parool

@property join_sep_pages type=checkbox field=meta method=serialize ch_value=1 group=sel_props
@caption Eraldi lehtedel

@property join_but_text type=textbox field=meta method=serialize group=sel_props
@caption Liitumise nupu tekst

@property save_but_text type=textbox field=meta method=serialize group=sel_props
@caption Salvestamise nupu tekst

@property cancel_but_text type=textbox field=meta method=serialize group=sel_props
@caption T&uuml;hista nupu tekst

@property join_properties_pages type=table store=no group=mk_pages
@caption Liitumisel k&uuml;sitavad v&auml;ljad

@property join_properties_page_titles type=table store=no group=page_titles
@caption Liitumise Lehtede Pealkirjad

@property join_seps type=table store=no group=seps
@caption Vahepealkirjad

@default group=prop_settings

	@property username_element type=select field=meta method=serialize
	@caption Kasutajanime element

	@property prop_settings type=table store=no no_caption=1


@groupinfo rules caption="Reeglid"

@groupinfo rules_show parent=rules caption="Nimekiri reeglitest"
@groupinfo rules_add parent=rules caption="Lisa Reegel"

@property rules_show type=table store=no group=rules_show
@caption Reeglid

@property rule_name type=textbox group=rules_add store=no
@caption Reegli nimi

@property rules_add type=text store=no group=rules_add
@caption Lisa Reegel

@property rule_to_grp type=relpicker reltype=RELTYPE_RULE_GRP group=rules_add store=no
@caption Vali grupp, kuhu reegel kasutaja paneb

@groupinfo preview caption="Eelvaade"

@property preview type=text store=no no_caption=1 group=preview

@groupinfo mails caption="Meilid"

@groupinfo joinmail caption="Meil liitujale" parent=mails

	@property joinmail_legend type=text store=no group=joinmail
	@caption Meili legend

@groupinfo confirm_mail caption="Kinnitamine" parent=mails

	@property join_requires_confirm type=checkbox ch_value=1 group=confirm_mail field=meta method=serialize
	@caption Liitumine n&otilde;uab kinnitust

	@property confirm_mail_legend type=text store=no group=confirm_mail
	@caption Kinnitusmeili legend

	@property confirm_mail_from_name type=textbox group=confirm_mail field=meta method=serialize
	@caption Kinnitusmeili kellelt nimi

	@property confirm_mail_from type=textbox group=confirm_mail field=meta method=serialize
	@caption Kinnitusmeili kellelt

	@property confirm_mail_subj type=textbox group=confirm_mail field=meta method=serialize
	@caption Kinnitusmeili teema

	@property confirm_mail type=textarea rows=10 cols=50 group=confirm_mail field=meta method=serialize
	@caption Kinnitusmeili sisu

	@property confirm_redir type=textbox group=confirm_mail field=meta method=serialize
	@caption Kuhu suunata p&auml;rast liitumist

@groupinfo notify_mail caption="Meil administraatorile" parent=mails
@default group=notify_mail

	@property mf_mail_from_addr type=textbox field=meta method=serialize
	@caption From aadress

	@property mf_mail_from_name type=textbox field=meta method=serialize
	@caption From nimi

	@property mf_mail_to type=relpicker reltype=RELTYPE_MAILADDR field=meta method=serialize multiple=1
	@caption Kellele saata

	@property mf_mail_subj type=textbox  field=meta method=serialize
	@caption Meili teema

	@property mf_mail type=textarea rows=10 cols=50  field=meta method=serialize
	@caption Meili sisu

	@property mf_mail_legend type=text rows=10 cols=50  field=meta method=serialize
	@caption Legend

@property jm_texts type=callback callback=callback_get_jm_texts group=joinmail store=no

@groupinfo trans caption="T&otilde;lgi"
@default group=trans

	@groupinfo trans_eld parent=trans caption="Elemendid"
	@default group=trans_eld
	
		@property trans_tb type=table no_caption=1 store=no

	@groupinfo trans_ttl parent=trans caption="Vahepealkirjad"
	@default group=trans_ttl

		@property trans_ttl_t type=table no_caption=1 store=no

	@groupinfo trans_errs parent=trans caption="Veateated"
	@default group=trans_errs

		@property trans_errs type=table store=no no_caption=1

@reltype JOIN_CLASS value=1 clid=CL_OBJECT_TYPE
@caption liitumise vorm

@reltype JOIN_RULE value=2 clid=CL_JOIN_SITE_RULE
@caption gruppi kuuluvuse reegel

@reltype OBJ_FOLDER value=3 clid=CL_MENU
@caption objektide kataloog

@reltype RULE_GRP value=4 clid=CL_GROUP
@caption reegli grupp

@reltype REL_OBJ value=5 clid=CL_USER,CL_CRM_COMPANY
@caption default seoste objektid

@reltype MAILADDR value=6 clid=CL_ML_MEMBER
@caption Kellele

@reltype VALIDATE_CTR value=7 clid=CL_FORM_CONTROLLER
@caption Valideerimise kontroller
*/

class join_site extends class_base
{
	function join_site()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/join/join_site",
			"clid" => CL_JOIN_SITE
		));

		$this->prop_types = array(
			"checkbox" => 1,
			"textbox" => 1,
			"datetime" => 1,
			"password" => 1,
			"relmanager" => 1,
			"relpicker" => 1,
			"date_select" => 1,
			"chooser" => 1,
			"releditor" => 1,
			"classificator" => 1
		);
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "prop_settings":
				$this->_get_prop_settings($arr);
				break;

			case "mf_mail_legend":
				$data["value"] = t("#edit_link# - link kasutajale")."<br />".t("#user_data# - kasutaja andmed");
				break;

			case "confirm_mail_legend":
				$data["value"] = "#confirm# - kasutaja kinnitamise link";
				break;

			case "join_properties":
				$this->_do_join_props($arr);
				break;

			case "join_properties_pages":
				$this->_do_join_props_pages($arr);
				break;

			case "join_properties_page_titles":
				$this->_do_join_props_pages_titles($arr);
				break;

			case "join_seps":
				$this->_do_join_seps($arr);
				break;

			case "preview":
				$data["value"] = $this->show(array(
					"id" => $arr["obj_inst"]->id()
				));
				break;

			case "rules_show":
				$this->_do_rules_tbl($arr);
				break;

			case "rules_add":
				$data["value"] = $this->_do_add_rule($arr);
				break;

			case "joinmail_legend":
				$data["value"] = t("E-maili sisu, mis saadetakse kasutajale liitumisel (kasutajanime alias #kasutaja#, parooli alias #parool# ja parooli muutmise lingi alias #pwd_hash#).");
				break;

			case "trans_tb":
				$this->_trans_tb($arr);
				break;

			case "username_element":
				$this->_username_element($arr);
				break;

			case "trans_ttl_t":
				$this->_trans_ttl_t($arr);
				break;

			case "trans_errs":
				$this->_trans_errs_t($arr);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "prop_settings":
				$this->_set_prop_settings($arr);
				break;

			case "join_properties":
				$this->_save_join_properties($arr);
				break;	

			case "join_properties_pages":
				$this->_save_join_properties_pages($arr);
				break;	

			case "join_properties_page_titles":
				$this->_save_join_properties_page_titles($arr);
				break;

			case "join_seps":
				$this->_save_join_seps($arr);
				break;

			case "rule_to_grp":
				$this->_save_rule($arr);
				break;

			case "jm_texts":
				$arr["obj_inst"]->set_meta("jm_texts", $arr["request"]["lm_l_tx"]);
				break;

			case "trans_tb":
				$this->_set_trans_tb($arr);
				break;

			case "trans_ttl_t":
				$arr["obj_inst"]->set_meta("lang_seps", $arr["request"]["d"]);
				break;

			case "trans_errs":
				$arr["obj_inst"]->set_meta("lang_errs", $arr["request"]["d"]);
				break;
		}
		return $retval;
	}	

	function _save_rule($arr)
	{
		$ruled = $this->_update_sess_data($arr["request"], true);

		if ($arr["prop"]["value"])
		{
			// save rule
			$o = new object();
			$o->set_class_id(CL_JOIN_SITE_RULE);
			$o->set_parent($arr['obj_inst']->parent());
			$o->set_name($arr["request"]["rule_name"]);
			$o->set_meta("rule_data",$ruled);
			$o->set_prop("rule_to_grp", $arr["prop"]["value"]);
			$o->set_prop("join_conf", $arr["obj_inst"]->id());
			$rid = $o->save();

			$arr["obj_inst"]->connect(array(
				"to" => $rid,
				"reltype" => "RELTYPE_JOIN_RULE",
			));

			$o->connect(array(
				"to" => $arr["obj_inst"]->id(),
				"reltype" => "RELTYPE_JOIN_CONF", // from join_site_rule
			));
		}
	}

	function _do_add_rule($arr)
	{
		$this->read_template("add_rule.tpl");
		$this->vars(array(
			"form" => $this->get_form_from_obj(array(
				"id" => $arr["obj_inst"]->id()
			))
		));
		return $this->parse();
	}


	function _init_rules_table(&$t)
	{
		$t->define_field(array(
			"name" => "rule",
			"caption" => t("Reegel"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "to_grp",
			"caption" => t("Grupp"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "edit",
			"caption" => t("Muuda"),
			"align" => "center",
		));
	}

	function _do_rules_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_rules_table($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_JOIN_RULE")) as $c)
		{
			$to = $c->to();

			$go = obj($to->prop("rule_to_grp"));

			$t->define_data(array(
				"rule" => $to->name(),
				"to_grp" => $go->name(),
				"edit" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $to->id()), $to->class_id()),
					"caption" => t("Muuda")
				))
			));
		}
	}

	function _init_jp_table(&$t)
	{
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus")
		));
		$t->define_field(array(
			"name" => "visible",
			"caption" => t("T&auml;idetav"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "required",
			"caption" => t("N&otilde;utav"),
			"align" => "center"
		));
		$t->set_sortable(false);
	}

	function _init_jp_table_pages(&$t)
	{
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus")
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
		$t->set_sortable(false);
	}

	function _init_pages_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "page_name",
			"caption" => t("Sisestatud number"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "page_title",
			"caption" => t("Lehe nimi"),
			"align" => "center"
		));
	}

	function _do_join_props_pages_titles($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_pages_tbl($t);

		foreach($this->_get_page_picker($arr["obj_inst"]) as $pgid => $pgstr)
		{
			$t->define_data(array(
				"page_name" => $pgid,
				"page_title" => html::textbox(array(
					"name" => "page_str[$pgid]",
					"value" => $pgstr
				))
			));
		}
	}

	function _init_seps_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "sep_name",
			"caption" => t("Tekst"),
			"align" => "center"
		));
	}

	function _do_join_seps($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_seps_tbl($t);
		$t->set_sortable(false);

		$awa = new aw_array($arr["obj_inst"]->meta("join_seps"));
		foreach($awa->get() as $sepid => $sep)
		{
			$t->define_data(array(
				"sep_name" => html::textbox(array(
					"name" => "join_seps[$sepid]",
					"value" => $sep
				))
			));
		}

		$t->define_data(array(
			"sep_name" => html::textbox(array(
				"name" => "join_seps[new]",
				"value" => ""
			))
		));
	}

	function _do_join_props($arr)
	{
		$prop =& $arr["prop"];
		
		$this->_init_jp_table($prop["vcl_inst"]);

		$required = $arr["obj_inst"]->meta("required");
		$visible = $arr["obj_inst"]->meta("visible");

		$clss = aw_ini_get("classes");

		foreach($this->_get_clids($arr["obj_inst"]) as $clid)
		{
			$cln = basename($clss[$clid]["file"]);

			// get properties for clid
			$cfgu = get_instance("cfg/cfgutils");
			$props = $cfgu->load_properties(array(
				"file" => $cln,
				"clid" => $clid
			));

			$prop["vcl_inst"]->define_data(array(
				"prop" => "<b>".$clss[$clid]["name"]."</b>",
				"visible" => "",
				"required" => ""
			));

			foreach($props as $nprop)
			{
				if (empty($this->prop_types[$nprop["type"]]) && ifset($nprop, "name") != "phone")
				{
					continue;
				}

				$req = html::checkbox(array(
					"name" => "required[$clid][".$nprop["name"]."]",
					"value" => 1,
					"checked" => isset($required[$clid][$nprop["name"]]) && $required[$clid][$nprop["name"]] == 1
				));
				if ($clid == CL_USER)
				{
					if ($nprop["name"] == "uid_entry" || $nprop["name"] == "passwd" || $nprop["name"] == "passwd_again")
					{
						$req = "Jah".html::hidden(array(
							"name" => "required[$clid][".$nprop["name"]."]",
							"value" => 1
						));
					}
				}

				$prop["vcl_inst"]->define_data(array(
					"prop" => str_repeat("&nbsp;", 10).ifset($nprop, "caption")." (".ifset($nprop, "name").")",
					"visible" => html::checkbox(array(
						"name" => "visible[$clid][".$nprop["name"]."]",
						"value" => 1,
						"checked" => isset($visible[$clid][$nprop["name"]]) && $visible[$clid][$nprop["name"]] == 1
					)),
					"required" => $req
				));
			}
		}
	}

	function _do_join_props_pages($arr)
	{
		$prop =& $arr["prop"];
		
		$this->_init_jp_table_pages($prop["vcl_inst"]);

		$ord = $arr["obj_inst"]->meta("ord");
		$propn = $arr["obj_inst"]->meta("propn");
		$page = $arr["obj_inst"]->meta("page");
		$visible = $arr["obj_inst"]->meta("visible");

		// insert all separators 
		$prop["vcl_inst"]->define_data(array(
			"prop" => t("<b>Vahepealkirjad</b>"),
			"visible" => "",
			"required" => ""
		));
		$seps = new aw_array($arr["obj_inst"]->meta("join_seps"));
		foreach($seps->get() as $sepid => $sepn)
		{
			$prop["vcl_inst"]->define_data(array(
				"prop" => $sepn,
				"page" => html::textbox(array(
					"name" => "page[sep][$sepid]",
					"value" => $page["sep"][$sepid],
					"size" => 5
				)),
				"ord" => html::textbox(array(
					"name" => "ord[sep][$sepid]",
					"value" => $ord["sep"][$sepid],
					"size" => 5
				)),
			));
		}
		

		foreach($this->_get_clids($arr["obj_inst"]) as $clid)
		{
			$cln = basename($clss[$clid]["file"]);

			// get properties for clid
			$cfgu = get_instance("cfg/cfgutils");
			$props = $cfgu->load_properties(array(
				"file" => $cln,
				"clid" => $clid
			));

			$prop["vcl_inst"]->define_data(array(
				"prop" => "<b>".$clss[$clid]["name"]."</b>",
				"visible" => "",
				"required" => ""
			));

			if (!is_array($propn[$clid]))
			{
				foreach($props as $nprop)
				{
					$propn[$clid][$nprop["name"]] = $nprop["caption"];
				}
			}

			foreach($props as $nprop)
			{
				if ($visible[$clid][$nprop["name"]] == 1)
				{
					$prop["vcl_inst"]->define_data(array(
						"prop" => html::textbox(array(
							"name" => "propn[$clid][".$nprop["name"]."]",
							"value" => ($propn[$clid][$nprop["name"]] == "" ? $nprop["caption"] : $propn[$clid][$nprop["name"]])
						)),
						"page" => html::textbox(array(
							"name" => "page[$clid][".$nprop["name"]."]",
							"value" => $page[$clid][$nprop["name"]],
							"size" => 5
						)),
						"ord" => html::textbox(array(
							"name" => "ord[$clid][".$nprop["name"]."]",
							"value" => $ord[$clid][$nprop["name"]],
							"size" => 5
						)),
					));
				}
			}
		}
	}

	function _save_join_properties($arr)
	{
		$visible = array();
		$required = array();

		foreach($this->_get_clids($arr["obj_inst"]) as $clid)
		{
			$visible[$clid] = $arr["request"]["visible"][$clid];
			$required[$clid] = $arr["request"]["required"][$clid];
		}		
		$arr["obj_inst"]->set_meta("visible", $visible);
		$arr["obj_inst"]->set_meta("required", $required);
	}

	function _save_join_properties_pages($arr)
	{
		$ord = array();
		$propn = array();
		$page = array();

		foreach($this->_get_clids($arr["obj_inst"]) as $clid)
		{
			$ord[$clid] = is_array($arr["request"]["ord"][$clid]) ? $arr["request"]["ord"][$clid] : array();
			$propn[$clid] = is_array($arr["request"]["propn"][$clid]) ? $arr["request"]["propn"][$clid] : array();
			$page[$clid] = is_array($arr["request"]["page"][$clid]) ? $arr["request"]["page"][$clid] : array();
		}		
		$ord["sep"] = is_array($arr["request"]["ord"]["sep"]) ? $arr["request"]["ord"]["sep"] : array();
		$page["sep"] = is_array($arr["request"]["page"]["sep"]) ? $arr["request"]["page"]["sep"] : array();

		$arr["obj_inst"]->set_meta("ord", $ord);
		$arr["obj_inst"]->set_meta("propn", $propn);
		$arr["obj_inst"]->set_meta("page", $page);
	}

	function _save_join_properties_page_titles($arr)
	{
		$arr["obj_inst"]->set_meta("page_str", $arr["request"]["page_str"]);
	}

	function _save_join_seps($arr)
	{
		$awa = new aw_array($arr["request"]["join_seps"]);
		$dat = array();
		$maxid = 0;
		foreach($awa->get() as $sepid => $sept)
		{
			if ($sept != "" && $sepid != "new")
			{
				$dat[$sepid] = $sept;
				$maxid = $sepid;
			}
		}

		if ($arr["request"]["join_seps"]["new"] != "")
		{
			$dat[$maxid+1] = $arr["request"]["join_seps"]["new"];
		}
		$arr["obj_inst"]->set_meta("join_seps", $dat);
	}

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	/** returns the html form from the join object $id, fills it withu data from objects in $data

		@param id 
	**/
	function get_form_from_obj($arr, $sessd = false)
	{
		extract($arr);
		
		$ob = new object($arr["id"]);

		$visible = $ob->meta("visible");
		$required = $ob->meta("required");
		$propn = $ob->meta("propn");
		$el_types = $ob->meta("types");
		$cfgu = get_instance("cfg/cfgutils");

		$prop_langs = $ob->meta("lang_props");
		$langid = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");

		$ret = "";
		$first = false;
		$clss = array();
		if ($sessd == false)
		{
			$sessd = aw_global_get("site_join_status");
		}
		$je = aw_global_get("join_err");
		aw_session_del("join_err");
		$clss = aw_ini_get("classes");
		$breaks = $ob->meta("el_breaks");
		$lang_errs = $ob->meta("lang_errs");
		$fill_msg = "J&auml;rgnev v&auml;li peab olema t&auml;idetud!";
		$lang_id = aw_global_get("lang_id");
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$lang_id = aw_global_get("ct_lang_id");
		}

		if (!empty($lang_errs["next_filled"][$lang_id]))
		{
			$fill_msg = $lang_errs["next_filled"][$lang_id];
		}
		
		list($ue_class, $ue_el) = explode("_", $ob->prop("username_element"), 2);
		
		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();
		$klomp = array();
		// for each cfgform related
		foreach($this->_get_clids($ob) as $clid)
		{
			$cln = basename($clss[$clid]["file"]);

			$clss[$clid] = $clss[$clid]["name"];

			if (!$first)
			{
				$first = $clid;
			}

			if ($ob->prop("join_sep_pages") && !($_GET["join_tab"] == $clid || (!$_GET["join_tab"] && $first == $clid)))
			{
				continue;
			}

			// get properties for clid
			$props = $cfgu->load_properties(array(
				"file" => $cln,
				"clid" => $clid
			));
			$tp = array();
			foreach($props as $pid => $prop)
			{	
				if ($visible[$clid][$prop["name"]])
				{
					if (isset($cf_sd[$prop["name"]]))
					{
						$prop["value"] = $cf_sd[$prop["name"]];
					}
					$tp[$pid] = $prop;
					if ($tp[$pid]["type"] != "password")
					{
						// handle person address separately
						if ($clid == CL_CRM_PERSON && $pid == "address")
						{
							// address has: * Street address: * City: * Zip code: * Country:	
							$adr_inst = get_instance(CL_CRM_ADDRESS);
							$tp["p_adr_ctry"] = array(
								"name" => "p_adr_ctry",
								"caption" => t("Maa"),
								"type" => "select",
								"options" => $adr_inst->get_country_list(),
								"value" => detect_country(),
							);
							$tp["p_adr_zip"] = array(
								"name" => "p_adr_zip",
								"caption" => t("Postiindeks"),
								"type" => "textbox",
							);
							$tp["p_adr_city"] = array(
								"name" => "p_adr_city",
								"caption" => t("Linn"),
								"type" => "textbox",
							);
							$tp["p_adr_county"] = array(
								"name" => "p_adr_county",
								"caption" => t("Maakond"),
								"type" => "textbox"
							);
							$tp["p_adr_str"] = array(
								"name" => "p_adr_str",
								"caption" => t("T&auml;nava nimi"),
								"type" => "textbox",
							);
							unset($tp["address"]);
						}
						else
						if (!empty($el_types[$clid][$pid]))
						{
							if ($tp[$pid]["type"] == "chooser")
							{
								// load options before messing with things
								$tmp_do = obj();
								$tmp_do->set_class_id($clid);
								$tmp_param = array(
									"obj_inst" => &$tmp_do,
									"prop" => &$tp[$pid],
									"request" => array()
								);
								$class_inst = get_instance($clid);
								$class_inst->get_property($tmp_param);
							}
							else
							if ($tp[$pid]["type"] == "classificator")
							{
								$clf_inst = get_instance(CL_CLASSIFICATOR);
								/*$tp[$pid]["options"] = $clf_inst->get_options_for(array(
									"name" => $pid,
									"clid" => $clid
								));*/
								$opt_vals = $clf_inst->get_choices(array(
									"name" => $pid,
									"clid" => $clid
								));
								$tp[$pid]["options"] = $opt_vals[4]["list_names"];
							}
							$tp[$pid]["type"] = $el_types[$clid][$pid];
						}
						else
						{
						//	$tp[$pid]["type"] = "textbox";
						}
						unset($tp[$pid]["size"]);
					}
				}
			}
		
			$wn = "typo_".$clid;

			$i = get_instance($clid);
			$xp = $i->parse_properties(array(
				"properties" => $tp,
				"name_prefix" => $wn
			));

			//miskid valikud tsekituks, kui on liitumise objektis nii m22ratud
			foreach($selected as $clid => $stuff)
			{
				foreach($stuff as $id => $value)
				{
					if($value)
					{
						if(is_array($xp["typo_".$clid."_".$id]["options"]))
						{
							$value = array_keys($xp["typo_".$clid."_".$id]["options"]);
							foreach($value as $asd)
							{
								$xp["typo_".$clid."_".$id]["value"][$asd] = $asd;
							}
						}
					}
				}
			}

			$cf_sd = $sessd[$wn];
			foreach($xp as $xprop)
			{
				$oldn = str_replace($wn."[", "", str_replace("]", "", $xprop["name"]));
				
				if ($clid == CL_USER && ($oldn == "uid_entry" || $oldn == $ue_el) && $je["gen"] != "")
				{
					$ermsg = "<font color='#FF0000'>".$je["gen"]."</font>";
					if ($this->is_template("ERROR_MESSAGE"))
					{
						$this->vars(array(
							"msg" => $je["gen"]
						));
						$ermsg = $this->parse("ERROR_MESSAGE");
					}
					$errp = array(
						"name" => "err_".$clid."_".$oldn,
						"type" => "text",
						"no_caption" => 1,
						"value" => $ermsg
					);
					//$htmlc->add_property($errp);
					$klomp[$errp["name"]] = $errp;
				}
				else
				if ($je["prop"][$clid][$oldn])
				{
					$ermsg = "<font color='#FF0000'>".$fill_msg."</font>";
					if ($this->is_template("ERROR_MESSAGE"))
					{
						$this->vars(array(
							"msg" => "<font color='#FF0000'>".$fill_msg."</font>"
						));
						$ermsg = $this->parse("ERROR_MESSAGE");
					}
					$errp = array(
						"name" => "err_".$clid."_".$oldn,
						"type" => "text",
						"no_caption" => 1,
						"value" => $ermsg,
						"sort_by" => $oldn
					);
					//$htmlc->add_property($errp);
					$klomp[$errp["name"]] = $errp;	
				}

				if (isset($cf_sd[$oldn]))
				{
					$xprop["value"] = $cf_sd[$oldn];
				}
				if ($propn[$clid][$oldn] != "")
				{
					if ($prop_langs[$clid][$oldn][$langid] != "")
					{
						$xprop["caption"] = $prop_langs[$clid][$oldn][$langid];
					}
					else
					{
						$xprop["caption"] = $propn[$clid][$oldn];
					}
				}

				if ($oldn == "comment" && $clid == CL_USER)
				{
					$xprop["type"] = "textarea";
					$xprop["comment"] = "";
					$xprop["rows"] = 5;
					$xprop["cols"] = 30;
				}

				if (is_array($breaks[$clid]) && $breaks[$clid][$oldn])
				{
					foreach(safe_array($xprop["options"]) as $_k => $_v)
					{
						$xprop["options"][$_k] = $_v."<br>";
					}
				}
				if (is_array($xprop["options"]) && $arr["add_empty_vals"])
				{
					$xprop["options"] = array("" => "") + $xprop["options"];
				}
				$klomp[$oldn] = $xprop;
			}
		}
		// add seprator props
		$seps = new aw_array($ob->meta("join_seps"));
		$lang_seps = safe_array($ob->meta("lang_seps"));
		$lang_id = aw_global_get("lang_id");
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$lang_id = aw_global_get("ct_lang_id");
		}
		foreach($seps->get() as $sepid => $sepn)
		{
			$pid = "typo_sep[jsep_".$sepid."]";
			$klomp[$pid] = array(
				"type" => "text",
				"name" => $pid,
				//"no_caption" => 1,
				"subtitle" => 1,
				"value" => !empty($lang_seps[$sepid][$lang_id]) ? $lang_seps[$sepid][$lang_id] : $sepn
			);
		}

		$this->_do_final_sort_props($ob, $klomp);
		foreach($klomp as $xprop)
		{
			$htmlc->add_property($xprop);
		}
		$htmlc->finish_output(array());

		$html .= $htmlc->get_result(array(
			"raw_output" => 1
		));

		if ($ob->prop("join_sep_pages"))
		{
			classload("vcl/tabpanel");
			$tp = tabpanel::simple_tabpanel(array(
				"var" => "join_tab",
				"default" => $first,
				"opts" => $clss
			));

			return $tp->get_tabpanel(array(
				"content" => $html
			));
		}

		return $html;
	}

	function change_data($o)
	{
		$this->read_template("join.tpl");

		$props = $this->get_elements_from_obj($o, array(
			"err_return_url" => post_ru()
		));

		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();
		foreach($props as $xprop)
		{
			$htmlc->add_property($xprop);
		}

		$htmlc->finish_output(array());

		$html .= $htmlc->get_result(array(
			"raw_output" => 1
		));

		$tx = t("Salvesta");
		if ($o->prop("save_but_text"))
		{
			$tx = $o->prop("save_but_text");
		}

		$lang_props = $o->meta("lang_props");
		$langid = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");

		if (!empty($lang_props["bt"]["__save_but"][$langid]))
		{
			$tx = $lang_props["bt"]["__save_but"][$langid];
		}

		$cb = t("T&uuml;hista");
                if ($o->prop("cancel_but_text") != "")
                {
                        $cb = $o->prop("cancel_but_text");
                }
		if (!empty($lang_props["bt"]["__cancel_but"][$langid]))
		{
			$cb = $lang_props["bt"]["__cancel_but"][$langid];
		}
		$this->vars(array(
			"form" => $html,
			"join_but_text" => $tx,
			"cancel_but_text" => $cb,
			"reforb" => $this->mk_reforb(
				"submit_update_form", 
				array(
					"id" => $o->id(), 
					"add" => 0, 
					"section" => aw_global_get("section"),
					"ru" => post_ru()
				)
			)
		));

		return $this->parse();
	}

	function show($arr)
	{
		$this->read_template("join.tpl");

		if (aw_global_get("uid") != "")
		{
			return $this->change_data(obj($arr["id"]));
		}
		
		aw_global_set("no_cache", 1);

		$o = obj($arr["id"]);
		$tx = t("Liitun");
		if ($o->prop("join_but_text"))
		{
			$tx = $o->prop("join_but_text");
		}

		$lang_props = $o->meta("lang_props");
		$langid = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");

		if (!empty($lang_props["bt"]["__join_but"][$langid]))
		{
			$tx = $lang_props["bt"]["__join_but"][$langid];
		}
		
		$cb = t("T&uuml;hista");
		if ($o->prop("cancel_but_text") != "")
		{
			$cb = $o->prop("cancel_but_text");
		}
		if (!empty($lang_props["bt"]["__cancel_but"][$langid]))
		{
			$cb = $lang_props["bt"]["__cancel_but"][$langid];
		}

		$this->vars(array(
			"form" => $this->get_form_from_obj(array(
				"id" => $arr["id"]
			)),
			"join_but_text" => $tx,
			"cancel_but_text" => $cb,
			"reforb" => $this->mk_reforb("submit_join_form", array("id" => $arr["id"], "add" => $add, "section" => aw_global_get("section")))
		));

		return $this->parse();
	}

	function _update_sess_data($arr, $ret = false)
	{
		if ($ret)
		{
			$sessd = array();
		}
		else
		{
			$sessd = aw_global_get("site_join_status");
		}

		
		// set the data to the session
		foreach($arr as $k => $v)
		{
			if (substr($k, 0, strlen("typo_")) == "typo_")
			{
				$sessd[$k] = $v;
			}
		}

		if ($ret)
		{
			return $sessd;
		}
		aw_global_set("site_join_status", $sessd);
		$_SESSION["site_join_status"] = $sessd;
	}

	/** submitting a join form will get you here
	
		@attrib name=submit_join_form nologin="1"

	**/
	function submit_join_form($arr)
	{
		$obj = obj($arr["id"]);

		if (strpos($obj->prop("username_element"), "_") !== false)
		{
			list($clid, $el) = explode("_", $obj->prop("username_element"), 2);
			$arr["typo_".CL_USER]["uid_entry"] = $arr["typo_".$clid][$el];
		} 

		// update session data in sess[site_join_status]
		$this->_update_sess_data($arr);

		// now, check if we are done
		$join_done = false;

		// check if all required fields are filled
		$req = $obj->meta("required");
		$sessd = $_SESSION["site_join_status"];

		if($obj->prop("generate_password"))
		{
			unset($req[CL_USER]["passwd"]);
			unset($req[CL_USER]["passwd_again"]);
			$sessd["typo_".CL_USER]["passwd"] = $sessd["typo_".CL_USER]["passwd_again"] = get_instance("user_object")->generate_password();
		}

		$filled = true;
		$nf = array();
		foreach($req as $clid => $rp)
		{
			$rpa = new aw_array($rp);
			foreach($rpa->get() as $propn => $one)
			{
				if ( $one == 1)
				{
					if ($sessd["typo_".$clid][$propn] == "" || (is_array($sessd["typo_".$clid][$propn]) && ($sessd["typo_".$clid][$propn]["year"] == "---" || $sessd["typo_".$clid][$propn]["month"] == "---" || $sessd["typo_".$clid][$propn]["day"] == "---" )))
					{
						$filled = false;
						$nf["prop"][$clid][$propn] = 1;
					}
				}
			}
		}

		if ($this->can("view", $obj->prop("check_sbt_controller")))
		{
			// if controller returns array, then all props in the array must be filled
			$ctr_i = get_instance(CL_FORM_CONTROLLER);
			$rv = $ctr_i->eval_controller($obj->prop("check_sbt_controller"), $arr, $obj, $obj);
			if (is_array($rv))
			{
				$filled = false;
				foreach($rv as $clid => $props)
				{
					foreach($props as $propn)
					{
						$nf["prop"][$clid][$propn] = 1;
					}
				}
			}
		}
		// if they are , then add the user and go to the after join page
		if ($filled)
		{
			// disable the fucking acl. 
			aw_disable_acl();
			// check if the user can be added
			
			// get the uid and password
			// they are from the user object
			$n_uid = $sessd["typo_".CL_USER]["uid_entry"];
			$n_pass = $sessd["typo_".CL_USER]["passwd"];
			$n_email = $sessd["typo_".CL_USER]["email"];
			$n_pass2 = $sessd["typo_".CL_USER]["passwd_again"];

			$us = get_instance("users");
			if ($this->can_add(array("a_uid" => $n_uid, "pass" => $n_pass, "pass2" => $n_pass2, "sj" => $obj)))
			{
				$join_done = true;
				// add the user
				$cu = get_instance(CL_USER);
				$u_oid = $cu->add_user(array(
					"uid" => $n_uid,
					"email" => $n_email,
					"password" => $n_pass,
					"join_grp" => $obj->id(),
					"real_name" => $sessd["typo_".CL_CRM_PERSON]["firstname"]." ".$sessd["typo_".CL_CRM_PERSON]["lastname"]
				));
	
				// also, create all the objects and do the relations and crap
				$this->_do_create_data_objects($arr, $u_oid->id());

				// apply rules on add
				$this->apply_rules_on_data_change($this->get_rules_from_obj($obj), $u_oid->id());

				$u_oid->connect(array(
					"to" => $obj->id(),
					"reltype" => "RELTYPE_JOIN_SITE"
				));

				if ($obj->prop("users_blocked_by_default"))
				{
					$u_oid->set_prop("blocked", 1);
					$u_oid->save();
				}
				
				aw_restore_acl();

				// if the props say so, log the user in
				if ($obj->prop("autologin"))
				{
					$rv = $us->login(array(
						"uid" => $n_uid,
						"password" => $n_pass
					));
				}

				if ($obj->prop("join_requires_confirm"))
				{
					$u_oid->set_prop("blocked", 1);
					$u_oid->save();
					if (!$this->db_table_exists("user_confirm_hashes"))
					{
						$this->db_query("CREATE table user_confirm_hashes (uid varchar(50), hash char(10))");
					}
					$hash = substr(gen_uniq_id(), 0, 10);
					$this->db_query("INSERT INTO user_confirm_hashes (uid,hash) values('".$u_oid->prop("uid")."', '$hash')");

					$this->_do_send_confirm_mail(array(
						"obj" => $obj,
						"hash" => $hash,
						"email" => $n_email
					));
					aw_session_set("site_join_status", array());
					aw_session_set("join_err", array());
					return $obj->prop("confirm_redir");
				}
				else
				// if needed, send join mail
				if ($obj->prop("send_join_mail"))
				{
					$this->_do_send_join_mail(array(
						"obj" => $obj,
						"uid" => $n_uid,
						"pass" => $n_pass,
						"email" => $n_email,
						"data" => $sessd,
						"u_obj" => $u_oid
					));
				}

				$mfmt = $obj->prop("mf_mail_to");
				if ($this->can("view", $mfmt))
				{
					$mfmt = array($mfmt => $mfmt);
				}
				if (is_array($mfmt) && count($mfmt))
				{
					$from = $obj->prop("mf_mail_from_addr");
					if ($obj->prop("mf_mail_from_name") != "")
					{
						$from = $obj->prop("mf_mail_from_addr")." <$from>";
					}
					foreach($mfmt as $mft)
					{
						if(!$this->can("view", $mft))
						{
							continue;
						}
						$to = obj($mft);
						$tom = $to->prop("mail");
						if ($to->prop("name") != "")
						{
							$tom = $to->prop("name")." <$tom>";
						}
						$content = $obj->prop("mf_mail");
						if(strpos($content, "#user_data#") !== false)
						{
							$els = $this->get_elements_from_obj($obj);
							$jmc = "";
							foreach($els as $eln => $eld)
							{
								list($clid, $propn) = explode("[", $eln);
								$propn = substr($propn, 0, -1);
				
								if ($eld["subtitle"] == 1)
								{
									$jmc .= $eld["value"]."<br>";
								}
								else
								{
									if (is_array($_POST[$clid]))
									{
										$val = $_POST[$clid][$propn];
									}
									else
									{
										$val = $_POST[$eln];
									}
									if (is_array($val))
									{
										if ($eld["type"] == "date_select")
										{
											if ($val["day"] == "---" || $val["month"] == "---" || $val["year"] == "---")
											{
												$val = "";
											}
											else
											{
												$val = sprintf("%02d-%02d-%04d", $val["day"], $val["month"], $val["year"]);
											}
										}
										else
										{
											$tmp = array();
											foreach($val as $oid)
											{
												if ($this->can("view", $oid))
												{
													$val_obj = obj($oid);
													$tmp[] = $val_obj->name();
												}
											}
											$val = join(", ", $tmp);
										}
									}
									else
									if ($eld["type"] != "textbox" && $eld["type"] != "password")
									{
										if ($this->can("view", $val))
										{
											$tmp = obj($val);
											$val = $tmp->name();
										}
									}
				
									$jmc .= $eld["caption"].": ".$val."<br>";
								}
							}
							$content = str_replace("#user_data#", strip_tags(str_replace("<br>", "\n", $jmc)), $content);
						}
						$link = $this->mk_my_orb("change", array("id" => $u_oid->id()), $u_oid->class_id(), true);
						send_mail(
							$tom, 
							$obj->prop("mf_mail_subj"),
							str_replace("#edit_link#", $link, $content),
							"From: ".$from."\n"
						);
					}
				}

				post_message("MSG_USER_JOINED", array(
					"user" => $u_oid
				));
				$_SESSION["last_join_data"] = $_SESSION["site_join_status"];
				// we also gots to clear out all the join data
				aw_session_set("site_join_status", array());
			}
			else
			{
				$nf["gen"] = $_SESSION["add_state"]["error"];
			}
		}
		// if not, then just return to the fill page. we should give the user some error message as well


		if ($join_done)
		{
			$this->join_done = true;
			aw_session_set("join_err", array());
			$langid = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");
			if($langid != $obj->lang_id())
			{
				$lp = $obj->meta("lang_props");
				$rv = $lp["bt"]["__after_join_url"][$langid];
			}
			if(!isset($rv) || strlen($rv) === 0)
			{
				$rv = $obj->prop("after_join_url");
			}
			if ($rv == "")
			{
				$rv = aw_ini_get("baseurl");
			}
			return $rv;
		}
		aw_session_set("join_err", $nf);
		if ($arr["err_return_url"])
		{
			
			return $arr["err_return_url"];
		}

		if (aw_ini_get("menuedit.language_in_url"))
		{
			$arr["section"] = aw_global_get("ct_lang_lc")."/".$arr["section"];
		}

		return aw_ini_get("baseurl")."/".$arr["section"];
	}

	function _do_create_data_objects($arr, $u_oid)
	{
		// in here we gots to assume the identity of the to-be-created user
		// so that the user will get all access to the correct objects
		// we can't do this with create_obj_access, because we can't access relation objects
		$u_o = obj($u_oid);
		aw_switch_user(array(
			"uid" => $u_o->prop("uid")
		));

		$obj = obj($arr["id"]);

		$sessd = aw_global_get("site_join_status");

		if(is_oid($obj->prop("obj_folder")) && $this->can("add", $obj->prop("obj_folder")))
		{
			$p_id = get_instance(CL_USER)->get_person_for_user($u_o);
			if(is_oid($p_id) && $this->can("change", $p_id))
			{
				$person = obj($p_id);
				$person->set_parent($obj->prop("obj_folder"));
				$person->save();
			}
			else
			{
				$person = obj();
				$person->set_class_id(CL_CRM_PERSON);
				$person->set_parent($obj->prop("obj_folder"));
				$person->set_name($sessd["typo_".CL_CRM_PERSON]["firstname"]." ".$sessd["typo_".CL_CRM_PERSON]["lastname"]);
				$p_id = $person->save();

				$u_o->connect(array(
					"to" => $p_id,
					"reltype" => "RELTYPE_PERSON" // from core/users/user
				));
			}
		}


		$com = obj();
		$com->set_class_id(CL_CRM_COMPANY);
		$com->set_parent($obj->prop("obj_folder"));
		$com->set_name($sessd["typo_".CL_CRM_COMPANY]["name"]);
		$c_id = $com->save();
		$com->acl_set(
			obj($u_o->get_default_group()),
			array("can_edit" => 1, "can_add" => 1, "can_view" => 1, "can_delete" => 1)
		);

		$person->add_work_relation(array("org" => $c_id));
		
		$a_objs = array();
		foreach($this->_get_clids($obj) as $clid)
		{
			if ($clid == CL_CRM_PERSON || $clid == CL_USER || $clid == CL_CRM_COMPANY)
			{
				continue;
			}

			$o = new object();
			$o->set_class_id($clid);
			$o->set_parent($obj->prop("obj_folder"));
			$o_id = $o->save();
			$this->create_obj_access($o_id, $u_o->prop("uid"));

			$u_o->connect(array(
				"to" => $o_id,
				"reltype" => "RELTYPE_USER_DATA" // from core/users/user
			));
			$person->connect(array(
				"to" => $o_id,
				"reltype" => "RELTYPE_USER_DATA" // from crm/crm_person
			));
			$a_objs[$o->class_id()] = $o_id;
			
		}

		// also, do update, all complex element crap is in there
		$this->_do_update_data_objects($obj, $u_o, $arr, $a_objs);
		aw_restore_user();
	}

	function _get_clids($ob)
	{
		$ret = array(CL_USER,CL_CRM_PERSON, CL_CRM_COMPANY);
		foreach($ob->connections_from(array("type" => "RELTYPE_JOIN_CLASS")) as $c)
		{
			$cfgf = $c->to();
			$ret[] = $cfgf->prop("type");
		}

		return $ret;
	}

	function _get_page_picker($o)
	{
		$ret = array();

		$page_str = $o->meta("page_str");

		$awa = new aw_array($o->meta("page"));
		foreach($awa->get() as $clid => $clps)
		{
			foreach($clps as $clpid => $pagen)
			{
				if ($pagen != "")
				{
					$ret[$pagen] = $page_str[$pagen];
				}
			}
		}

		return $ret;
	}

	function get_rules_from_obj($o)
	{
		aw_disable_acl();
		$ret = array();
		foreach($o->connections_from(array("to.class_id" => CL_JOIN_SITE_RULE /* RELTYPE_JOIN_RULE */)) as $c)
		{
			$ret[$c->prop("to")] = $c->prop("to");
		}
		aw_restore_acl();
		return $ret;
	}

	function apply_rules_on_data_change($rules, $u_oid)
	{
		aw_disable_acl();
		$user = obj($u_oid);

		$ri = get_instance(CL_JOIN_SITE_RULE);
		$gi = get_instance(CL_GROUP);

		foreach($rules as $rule_oid)
		{
			$rule = obj($rule_oid);

			if ($ri->match_rule_to_user($rule, $user))
			{
				$gi->add_user_to_group($user, obj($rule->prop("rule_to_grp")));
			}
		}
		aw_restore_acl();
	}

	/**
		$params can contain:
			- err_return_url - if set, errored inputs go to that
			- uid - if set, data for that user is returned
	**/
	function get_elements_from_obj($ob, $params)
	{
		$visible = $ob->meta("visible");
		$required = $ob->meta("required");
		$selected = $ob->meta("selected");
		$propn = $ob->meta("propn");
		$je = aw_global_get("join_err");

		$prop_langs = $ob->meta("lang_props");
		$langid = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");

		$cfgu = get_instance("cfg/cfgutils");

		$user = isset($params["uid"]) ? $params["uid"] : aw_global_get("uid");
		$lang_errs = $ob->meta("lang_errs");
		$fill_msg = "J&auml;rgnev v&auml;li peab olema t&auml;idetud!";
		$fill_msg_email = "Palun sisestage korrektne e-maili aadress!";
		$lang_id = aw_global_get("lang_id");
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$lang_id = aw_global_get("ct_lang_id");
		}
		if (!empty($lang_errs["next_filled"][$lang_id]))
		{
			$fill_msg = $lang_errs["next_filled"][$lang_id];
		}
		if (!empty($lang_errs["wrong_email"][$lang_id]))
		{
			$fill_msg_email = $lang_errs["wrong_email"][$lang_id];
		}

		if ($user != "")
		{
			$us = get_instance("users");
			$u_o = obj($us->get_oid_for_uid($user));
			$visible[CL_USER]["uid_entry"] = false;
		}		

		$sessd = aw_global_get("site_join_status");

		$clss = aw_ini_get("classes");

		$set_el_types = $ob->meta("types");
		$breaks = $ob->meta("el_breaks");
		$tp = array();
		// for each cfgform related
		$ipl = get_instance("core/util/ip_locator/ip_locator");
		$v = $ipl->search(get_ip());
		foreach($this->_get_clids($ob) as $clid)
		{
			// get properties for clid
			$props = $cfgu->load_properties(array(
				"file" => basename($clss[$clid]["file"]),
				"clid" => $clid
			));
			$relinfo = $cfgu->relinfo;

			$data_o = obj();
			$data_o->set_class_id($clid);
			// get data object if user is logged
			if ($u_o)
			{
				if ($clid == CL_USER)
				{
					$data_o = $u_o;
				}
				else
				if ($clid == CL_CRM_PERSON)
				{
					$c = reset($u_o->connections_from(array("type" => "RELTYPE_PERSON")));
					if ($c)
					{
						$data_o = $c->to();
					}
				}
				else
				if ($clid == CL_CRM_COMPANY)
				{
					$c = reset($u_o->connections_from(array("type" => "RELTYPE_PERSON")));
					if ($c)
					{
						$tmp = $c->to();
//						$c = reset($tmp->connections_from(array("type" => "RELTYPE_WORK" /* from crm_person */)));
//						if ($c)
//						{
//							$data_o = $c->to();
//						}
						$data_o = $tmp->company();
					}
				}
			}

			$ttp = array();
			foreach($props as $pid => $prop)
			{	
				if ($visible[$clid][$pid])
				{
					if ($prop["name"] == "phone" || $prop["name"] == "fax")
					{
						$prop["type"] = "textbox";
					}
					$ttp[$pid] = $prop;
				}
			}

			$adr = $ttp["address"];
			$class_inst = get_instance($clid);
			$class_inst->init_class_base();
			$ttp = $class_inst->parse_properties(array("properties" => $ttp, "obj_inst" => $data_o));
			if ($adr)
			{
				$ttp["address"] = $adr;
			}
			foreach($ttp as $pid => $prop)
			{
				$cpn = $prop["name"];
				/*if (strpos($cpn, "[") !== false)
				{
					$cpn = substr($cpn, 0, strpos($cpn, "["));
				}*/	
				if ($visible[$clid][$cpn])
				{
					$oldn = str_replace($wn."[", "", str_replace("]", "", $prop["name"]));
					if ($clid == CL_CRM_PERSON && $pid == "address")
					{
						// address has: * Street address: * City: * Zip code: * Country:	
						$adr_inst = get_instance(CL_CRM_ADDRESS);
						$opts = $adr_inst->get_country_list();
						$cntr = array_search($data_o->prop("address.riik.name"), $opts);
						$tp["p_adr_ctry"] = array(
							"name" => "p_adr_ctry",
							"caption" => t("Maa"),
							"type" => "select",
							"options" => $opts,
							"value" => $cntr ? $cntr : $v["country_code2"],
						);
						$tp["p_adr_zip"] = array(
							"name" => "p_adr_zip",
							"caption" => t("Postiindeks"),
							"type" => "textbox",
							"value" => $data_o->prop("address.postiindeks")
						);
						$tp["p_adr_city"] = array(
							"name" => "p_adr_city",
							"caption" => t("Linn"),
							"type" => "textbox",
							"value" => $data_o->prop("address.linn.name")
						);
						$tp["p_adr_county"] = array(
							"name" => "p_adr_county",
							"caption" => t("Maakond"),
							"type" => "textbox",
							"value" => $data_o->prop("address.maakond.name")
						);
						$tp["p_adr_str"] = array(
							"name" => "p_adr_str",
							"caption" => t("T&auml;nava nimi"),
							"type" => "textbox",
							"value" => $data_o->prop("address.aadress")
						);
						unset($ttp[$pid]);
						continue;
					}
					if ($clid == CL_USER && $oldn == "uid_entry")
					{
						if ($je["gen"] != "")
						{
							$ermsg = "<font color='#FF0000'>".$je["gen"]."</font>";
							if ($this->is_template("ERROR_MESSAGE"))
							{
								$this->vars(array(
									"msg" => $je["gen"]
								));
								$ermsg = $this->parse("ERROR_MESSAGE");
							}
							$tp["err_".$clid."_".$oldn] = array(
								"name" => "err_".$clid."_".$oldn,
								"type" => "text",
								"no_caption" => 1,
								"value" => $ermsg
							);
						}
					}
					if ($je["prop"][$clid][$pid])
					{
						if ($pid == "email")
						{
							$ermsg = "<font color='#FF0000'>".$fill_msg_email."</font>";
						}
						else
						{
							$ermsg = "<font color='#FF0000'>".$fill_msg."</font>";
						}
						if ($this->is_template("ERROR_MESSAGE"))
						{
							$this->vars(array(
								"msg" => $je["gen"]
							));
							$ermsg = $this->parse("ERROR_MESSAGE");
						}
						$tp["err_".$clid."_".$oldn] = array(
							"name" => "err_".$clid."_".$oldn,
							"type" => "text",
							"no_caption" => 1,
							"value" => $ermsg
						);
					}
					// if it's a relpicker, get the rels from the default rel object
					// and insert them in there
					if ($oldn == "rank")
					{
						$c = reset($u_o->connections_from(array("type" => "RELTYPE_PERSON")));
                                        	if ($c)
                                        	{
                                                	$tdata_o = $c->to();
							$prop["value"] = $tdata_o->prop("rank.name");
						}
						$prop["type"] = "textbox";
						$prop["post_append_text"] = "";
					}
					else
					if ($props[$pid]["type"] == "relpicker")
					{
						/*$tmp = reset($ob->connections_from(array(
							"type" => "RELTYPE_REL_OBJ", 
							"to.class_id" => $clid
						)));
						if ($tmp)
						{
							$relv = $relinfo[$prop["reltype"]]["value"];
							$relto = $tmp->to();
							$data = array();
							foreach($relto->connections_from(array("type" => $relv)) as $c)
							{
								$data[$c->prop("to")] = $c->prop("to.name");
							}
							$prop["options"] = $data;
						}*/
						$prop["type"] = "textbox";
						$prop["value"] = $ob->prop($oldn);
						$prop["post_append_text"] = "";
					}
					else
					if ($oldn == "phone")
					{
						$c = reset($u_o->connections_from(array("type" => "RELTYPE_PERSON")));
                                        	if ($c)
                                        	{
                                                	$tdata_o = $c->to();
							$prop["value"] = $tdata_o->prop("phone.name");
						}
					}
					else
					if ($oldn == "fax")
					{
						$c = reset($u_o->connections_from(array("type" => "RELTYPE_PERSON")));
                                        	if ($c)
                                        	{
                                                	$tdata_o = $c->to();
							$prop["value"] = $tdata_o->prop("fax.name");
						}
					}
					else
					if ($oldn == "email")
					{
						$c = reset($u_o->connections_from(array("type" => "RELTYPE_PERSON")));
						if ($c)
						{
							$tdata_o = $c->to();
							$prop["value"] = $tdata_o->prop("email.mail");
						}
					}

					// set value in property
					if ($data_o)
					{
						if ($pid == "name")
						{
							$prop["value"] = $data_o->name();
						}
						else
						if ($oldn != "email" && $oldn != "phone" && $oldn != "fax" && $oldn != "rank")
						{
							if ($prop["store"] == "connect")
							{
								$conns = $data_o->connections_from(array("type" => $prop["reltype"]));
								$prop["value"] = array();
								foreach($conns as $con)
								{
									if ($prop["multiple"] == 1)
									{
										$prop["value"][$con->prop("to")] = $con->prop("to");
									}
									else
									{
										$prop["value"] = $con->prop("to");
										break;
									}
								}
							}
							else
							{
								$prop["value"] = $data_o->prop($pid);
							}
						}
					}
					else
					{
						// try to read from sess data
						$wn = "typo_".$clid;
						$cf_sd = $sessd[$wn];
						$prop["value"] = $cf_sd[$oldn];
					}

					$pid = "typo_".$clid."[".$oldn."]";
					$prop["name"] = $pid;
					if ($propn[$clid][$oldn] != "")
					{
						if ($prop_langs[$clid][$oldn][$langid] != "")
						{
							$prop["caption"] = $prop_langs[$clid][$oldn][$langid];
						}
						else
						{
							$prop["caption"] = $propn[$clid][$oldn];
						}
					}
					$prop["comment"] = "";
					if ($oldn == "comment" && $clid == CL_USER)
					{
						$prop["type"] = "textarea";
						$prop["rows"] = 5;
						$prop["cols"] = 30;
					}

					if (!empty($set_el_types[$clid][$oldn]))
					{
						$prop["type"] = $set_el_types[$clid][$oldn];
					}
					unset($prop["size"]);

        	                        if (is_array($breaks[$clid]) && $breaks[$clid][$oldn])
	                                {
                                	        foreach(safe_array($prop["options"]) as $_k => $_v)
                        	                {
                	                                $prop["options"][$_k] = $_v."<br>";
        	                                }
	                                }

					$tp[$pid] = $prop;
				}
			}
		}

		// add seprator props
		$seps = new aw_array($ob->meta("join_seps"));
		$lang_seps = safe_array($ob->meta("lang_seps"));
		$lang_id = aw_global_get("lang_id");
		if (aw_ini_get("user_interface.full_content_trans"))
		{
			$lang_id = aw_global_get("ct_lang_id");
		}
		foreach($seps->get() as $sepid => $sepn)
		{
			$pid = "typo_sep[jsep_".$sepid."]";
			$tp[$pid] = array(
				"type" => "text",
				"name" => $pid,
				//"no_caption" => 1,
				"subtitle" => 1,
				"value" => !empty($lang_seps[$sepid][$lang_id]) ? $lang_seps[$sepid][$lang_id] : $sepn
			);
		}
		
		// now that we got all props, re-order them based on the order on the pages page
		$this->_do_final_sort_props($ob, $tp);

		if ($params["err_return_url"] != "")
		{
			$tp["err_return_url"] = array(
				"name" => "err_return_url",
				"type" => "hidden",
				"store" => "no",
				"value" => $params["err_return_url"]
			);
		}
		
		aw_session_set("join_err", false);
		return $tp;
	}

	/**
		@attrib name=submit_update_form
	**/
	function orb_submit_update_form($arr)
	{
		$this->submit_update_form($arr);
		return $arr["ru"];
	}

	function submit_update_form($arr, $params = array())
	{
		$obj = obj($arr["id"]);

		$us = get_instance("users");

		$nf = array();
		if ($this->can("view", $obj->prop("check_sbt_controller")))
		{
			// if controller returns array, then all props in the array must be filled
			$ctr_i = get_instance(CL_FORM_CONTROLLER);
			$rv = $ctr_i->eval_controller($obj->prop("check_sbt_controller"), $arr, $obj, $obj);
			if (is_array($rv))
			{
				$filled = false;
				foreach($rv as $clid => $props)
				{
					foreach($props as $propn)
					{
						$nf["prop"][$clid][$propn] = 1;
					}
				}
			}
		}
	
		if (count($nf))
		{
			aw_session_set("join_err", $nf);
			if ($arr["err_return_url"])
			{
				return $arr["err_return_url"];
			}
		}

		$user = isset($params["uid"]) ? $params["uid"] : aw_global_get("uid");
		$u_o = obj($us->get_oid_for_uid($user));

		// update data objects
		$this->_do_update_data_objects($obj, $u_o, $arr);

		// apply rules on add
		$this->apply_rules_on_data_change($this->get_rules_from_obj($obj), $u_o->id());
	}

	function _do_update_data_objects($ob, $u_o, $data, $a_objs = array())
	{
		$visible = $ob->meta("visible");
		$cfgu = get_instance("cfg/cfgutils");

		$clss = aw_ini_get("classes");

		// for each cfgform related
		foreach($this->_get_clids($ob) as $clid)
		{
			$cln = basename($clss[$clid]["file"]);

			$clss[$clid] = $clss[$clid]["name"];

			// get properties for clid
			$props = $cfgu->load_properties(array(
				"file" => $cln,
				"clid" => $clid
			));

			// find the correct data object
			$data_o = false;

			// if it's user, then we gots it
			if ($clid == CL_USER)
			{
				$data_o = $u_o;
			}
			elseif ($clid == CL_CRM_PERSON)
			{
				$c = reset($u_o->connections_from(array("type" => "RELTYPE_PERSON")));
				if (!$c)
				{
					// create person
					$data_o = obj();
					$data_o->set_parent($u_o->id());
					$data_o->set_class_id(CL_CRM_PERSON);
					$data_o->save();
					$u_o->connect(array(
						"to" => $data_o->id(),
						"reltype" => "RELTYPE_PERSON"
					));
				}
				else
				{
					$data_o = $c->to();
				}
			}
			elseif ($clid == CL_CRM_COMPANY)
			{
				$c = reset($u_o->connections_from(array("type" => "RELTYPE_PERSON")));
				if (!$c)
				{
					// create person
					$tmp = obj();
					$tmp->set_parent($u_o->id());
					$tmp->set_class_id(CL_CRM_PERSON);
					$tmp->save();
					$u_o->connect(array(
						"to" => $tmp->id(),
						"reltype" => "RELTYPE_PERSON"
					));
				}
				else
				{
					$tmp = $c->to();
				}

				$c = $tmp->company_id();//reset($tmp->connections_from(array("type" => "RELTYPE_WORK" /* from crm_person */)));
				if (!$c)
				{
					// create person
					$data_o = obj();
					$data_o->set_parent($u_o->id());
					$data_o->set_class_id(CL_CRM_COMPANY);
					$data_o->save();	
				
					$tmp->add_work_relation(array("org" => $data_o->id()));

				}
				else
				{
					$data_o = obj($c);
				}
			}
			elseif($this->can("edit", $a_objs[$clid]) && is_oid($a_objs[$clid]))
			{
				$data_o = obj($a_objs[$clid]);
			}
			
			if ($data_o)
			{
				$this->_do_fake_form_submit(array(
					"data_o" => $data_o,
					"props" => $props,
					"visible" => $visible,
					"clid" => $clid,
					"data" => $data
				));
			}
		}
	}

	function _do_fake_form_submit($arr)
	{
		extract($arr);
		$submit_data = array(
			"return" => "id",
			"id" => $data_o->id(),
			"cb_no_groups" => 1
		);

		// get relinfo for class
		$cu = get_instance("cfg/cfgutils");
		$_ps = $cu->load_properties(array(
			"clid" => $data_o->class_id()
		));
		$reli = $cu->get_relinfo();
		foreach($props as $pid => $prop)
		{	
			if ($visible[$clid][$prop["name"]])
			{
				$oldn = str_replace($wn."[", "", str_replace("]", "", $prop["name"]));
				$wn = "typo_".$clid;
				$cf_sd = $data[$wn];
				if ($clid == CL_USER)
				{
					$data_o->set_prop($pid, $cf_sd[$oldn]);
				}
				if ($prop["type"] == "relmanager" || $prop["type"] == "releditor" || $prop["type"] == "relpicker")
				{
					//$submit_data["cb_emb"] = $data["cb_emb"][$wn];
					// damn. we need to make the right thing from textbox
					// so check if the object has an object for this property
					// if so, modify it
					// if not, create new 
					// set the object's id as the submit value
					$p_oid = $data_o->prop($prop["name"]);
					if ($this->can("view", $p_oid) && ($prop["name"] == "address" || $data_o->prop($prop["name"].".name") == $cf_sd[$oldn]))
					{
						$p_obj = obj($p_oid);
						// if this is the address thingamajig, then create the address from the separate props
						if ($prop["name"] == "address")
						{
							$this->_update_address_from_req($p_obj, $_POST);
						}
						$p_obj->set_name($cf_sd[$oldn]);
						aw_disable_acl();
						$p_obj->save();
						aw_restore_acl();
					}
					else
					{
						$p_obj = obj();
						$p_obj->set_parent($data_o->id());
						$p_obj->set_class_id($oldn == "email" ? CL_ML_MEMBER :$reli[$prop["reltype"]]["clid"][0]);
						$p_obj->set_name($cf_sd[$oldn]);
						if ($oldn == "email")
						{
							$p_obj->set_prop("mail", $cf_sd[$oldn]);
						}
						aw_disable_acl();
						$p_obj->save();
						aw_restore_acl();
						if ($prop["name"] == "address")
						{
							$this->_update_address_from_req($p_obj, $_POST);
							$p_obj->save();
						}

						if ($prop["name"] == "picture" && is_uploaded_file($_FILES["typo_145"]["tmp_name"]["picture"]["file"]))
						{
							// manual image upload here
							$image = get_instance(CL_IMAGE);
							$imdata = $image->add_image(array(
								"from" => "file",
								"file" => $_FILES["typo_145"]["tmp_name"]["picture"]["file"],
								"orig_name" => $_FILES["typo_145"]["name"]["picture"]["file"],
								"id" => $data_o->prop($prop["name"]),
								"parent" => $data_o->id()
							));
							$p_obj = obj($imdata["id"]);
						}
						$data_o->connect(array(
							"to" => $p_obj->id(),
							"reltype" => $prop["reltype"]
						));
						$data_o->set_prop($prop["name"] , $p_obj->id());
					}
				}
				else
				if ($prop["type"] == "classificator" || $prop["group"] != "general")
				{
					$data_o->set_prop($prop["name"] , $cf_sd[$oldn]);
					$submit_data[$pid] = $cf_sd[$oldn];
				}
				else
				{
	
					$submit_data[$pid] = $cf_sd[$oldn];
				}
			}
		}
		if ($clid == CL_USER)
		{
			$data_o->save();
			$data_o_inst = $data_o->instance();
			$data_o_inst->callback_post_save(array("obj_inst" => $data_o));
		}
		else
		{
			$data_o_inst = $data_o->instance();
			$data_o_inst->submit($submit_data);
		}
	}

	function __prop_sorter($a, $b)
	{
		$a_diff = 0;
		if ($a["sort_by"] != "")
		{
			$a = $this->__sort_tp[$a["sort_by"]];
			$a_diff = -1;
		}
		$b_diff = 0;
		if ($b["sort_by"] != "")
		{
			$b = $this->__sort_tp[$b["sort_by"]];
			$b_diff = -1;
		}
		// get order from prop name
		if (!preg_match("/typo_(.*)\[(.*)\]/U", $a["name"], $a_mt))
		{
			if (!preg_match("/err_(\d*)_(.*)/", $a["name"], $a_mt))
			{
				$a_mt = array();
				$a_mt[2] = $a["name"];
				$a_mt[1] = CL_CRM_PERSON;
			}
		}
		if (!preg_match("/typo_(.*)\[(.*)\]/U", $b["name"], $b_mt))
		{
			if (!preg_match("/err_(\d*)_(.*)/", $b["name"], $b_mt))
			{
				$b_mt = array();
				$b_mt[2] = $b["name"];
				$b_mt[1] = CL_CRM_PERSON;
			}
		}
		$a_clid = $a_mt[1];
		$a_prop = $a_mt[2];

		if (strpos($a_prop, "p_adr") !== false)
		{
			if ($a_prop == "p_adr_str")
			{
				$a_diff = -0.7+($a_diff / 100);
			}
			else
			if ($a_prop == "p_adr_zip")
			{
				$a_diff = -0.3+($a_diff / 100);
			}
			else
			if ($a_prop == "p_adr_city")
			{
				$a_diff = -0.5+($a_diff / 100);
			}
			else
			if ($a_prop == "p_adr_ctry")
			{
				$a_diff = -0.1+($a_diff / 100);
			}
			else
			if ($a_prop == "p_adr_county")
			{
				$a_diff = -0.4+($a_diff / 100);
			}
			$a_prop = "address";
		}

		$b_clid = $b_mt[1];
		$b_prop = $b_mt[2];
		if (strpos($b_prop, "p_adr") !== false)
		{
                        if ($b_prop == "p_adr_str")
                        {
                                $b_diff = -0.7+($b_diff / 100);
                        }
                        else
                        if ($b_prop == "p_adr_zip")
                        {
                                $b_diff = -0.3+($b_diff / 100);
                        }
                        else
                        if ($b_prop == "p_adr_city")
                        {
                                $b_diff = -0.5+($b_diff / 100);
                        }
                        else
                        if ($b_prop == "p_adr_ctry")
                        {
                                $b_diff = -0.1+($b_diff / 100);
                        }
                        else
                        if ($b_prop == "p_adr_county")
                        {
                                $b_diff = -0.4+($b_diff / 100);
                        }

			$b_prop = "address";
		}

//echo "a _prop = $a_prop , a_diff = $a_diff , bprop = $p_prop , bdiff = $b_diff <br>";
		if ($a_clid == "sep")
		{
			list(, $a_prop) = explode("_", $a_prop);
			$a_ord = $this->__sort_ord[$a_clid][$a_prop]*10;
		}
		else
		{
			$a_ord = $this->__sort_ord[$a_clid][$a_prop]*10;
		}

		if ($b_clid == "sep")
		{
			list(, $b_prop) = explode("_", $b_prop);
			$b_ord = $this->__sort_ord[$b_clid][$b_prop]*10;
		}
		else
		{
			$b_ord = $this->__sort_ord[$b_clid][$b_prop]*10;
		}

		$a_ord += $a_diff;
		$b_ord += $b_diff;
		if ($a_ord == $b_ord)
		{
			return 0;
		}
		return ($a_ord < $b_ord) ? -1 : 1;
	}

	function _do_final_sort_props(&$ob, &$tp)
	{
		$this->__sort_ord = $ob->meta("ord");
		$this->__sort_tp = $tp;
		uasort($tp, array(&$this, "__prop_sorter"));
	}

	function callback_get_jm_texts($arr)
	{
		$ret = array();

		$la = get_instance("languages");
		$ll = $la->listall();

		$jmt = $arr["obj_inst"]->meta("jm_texts");

		foreach($ll as $lid => $ldata)
		{
			$name3 = "lm_l_tx[".$lid."][from]";
			$tmp3 = array(
				"name" => $name3,
				"type" => "textbox",
				"table" => "objects",
				"field" => "meta",
				"method" => "serialize",
				"caption" => sprintf(t("Liitumise meili from aadress (%s)"), $ldata["name"]),
				"value" => $jmt[$lid]["from"]
			);

			$name = "lm_l_tx[".$lid."][subj]";
			$tmp = array(
				"name" => $name,
				"type" => "textbox",
				"table" => "objects",
				"field" => "meta",
				"method" => "serialize",
				"caption" => sprintf(t("Liitumise meili subjekt (%s)"), $ldata["name"]),
				"value" => $jmt[$lid]["subj"]
			);

			$name2 = "lm_l_tx[".$lid."][text]";
			$tmp2 = array(
				"name" => $name2,
				"type" => "textarea",
				"rows" => 10,
				"cols" => 50,
				"table" => "objects",
				"field" => "meta",
				"method" => "serialize",
				"caption" => sprintf(t("Liitumise meil (%s)"), $ldata["name"]),
				"value" => $jmt[$lid]["text"]
			);

			$name4 = "lm_l_tx_".$lid."_sep";
			$tmp4 = array(
				"name" => $name4,
				"type" => "text",
				"store" => "no",
				"no_caption" => 1,
				"value" => "<hr>"
			);

			$ret[$name3] = $tmp3;
			$ret[$name] = $tmp;
			$ret[$name2] = $tmp2;
			$ret[$name4] = $tmp4;
		}

		return $ret;
	}

	function _do_send_join_mail($arr)
	{
		$jms = $arr["obj"]->meta("jm_texts");

		$lid = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");
		$from = $jms[$lid]["from"];
		$subj = $jms[$lid]["subj"];
		$text = $jms[$lid]["text"];

		$us = get_instance("users");
		$cp = $us->get_change_pwd_hash_link($arr["u_obj"]->id());

		$text = str_replace("#parool#", $arr["pass"], $text);
		$text = str_replace("#kasutaja#", $arr["uid"], $text);
		$text = str_replace("#pwd_hash#", $cp, $text);

		send_mail($arr["email"],$subj,$text,"From: ".$from);
	}

	function _do_send_confirm_mail($arr)
	{
		$text = $arr["obj"]->prop("confirm_mail");
		$url = str_replace("automatweb/", "", $this->mk_my_orb("do_confirm_user", array("h" => $arr["hash"])));
		$text = str_replace("#confirm#", $url, $text);

		$subj = $arr["obj"]->prop("confirm_mail_subj");
		$from = $arr["obj"]->prop("confirm_mail_from");
		if ($arr["obj"]->prop("confirm_mail_from_name") != "")
		{
			$from = $arr["obj"]->prop("confirm_mail_from_name")." <$from>";
		}
		send_mail($arr["email"],$subj,$text,"From: ".$from);
	}

	/**

		@attrib name=do_confirm_user nologin="1"

		@param h required
	**/
	function do_confirm_user($arr)
	{
		$this->quote($arr["h"]);
		$row = $this->db_fetch_row("SELECT * FROM user_confirm_hashes where hash = '$arr[h]'");

		if ($row["uid"] == "")
		{
			return t("Sellise koodiga kasutajat pole olemas!");
		}

		$this->db_query("DELETE FROM user_confirm_hashes WHERE hash = '$arr[h]'");		

		$u = get_instance("users");
		$oid = $u->get_oid_for_uid($row["uid"]);
		$o = obj($oid);
		$o->set_prop("blocked", 0);
		aw_disable_acl();
		$o->save();
		aw_restore_acl();

		$pwd = $this->db_fetch_field("SELECT password FROM users WHERE uid = '$row[uid]'", "password");

		aw_ini_set("auth", "md5_passwords", 0);

		return $u->login(array(
			"uid" => $row["uid"],
			"password" => $pwd
		));
	}
	
	function _init_prop_settings(&$t)
	{
		$t->define_field(array(
			"name" => "prop",
			"caption" => t("Omadus"),
			"align" => "left",
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "selected",
			"caption" => t("Vaikimisi valitud"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "break_in_el",
			"caption" => t("Reavahetus sisu vahel"),
			"align" => "center"
		));
	}

	function _get_prop_settings($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_prop_settings($t);
		$selected = $arr["obj_inst"]->meta("selected");
		$clss = aw_ini_get("classes");
		$types = $arr["obj_inst"]->meta("types");
		$breaks = $arr["obj_inst"]->meta("el_breaks");
		$type_list = array(
			"" => t("--vali--"),
			"textbox" => t("Tekstikast"),
			"select" => t("Listbox"),
			"radiobutton" => t("Raadionupp"),
			"checkbox" => t("Checkbox"),
		);
		foreach($arr["obj_inst"]->meta("visible") as $clid => $items)
		{
			$tmp = obj();
			$tmp->set_class_id($clid);
			$property_list = $tmp->get_property_list();

			$t->define_data(array(
				"prop" => $clss[$clid]["name"]
			));

			foreach($items as $pn => $one)
			{
				$t->define_data(array(
					"prop" => str_repeat("&nbsp;", 10).$property_list[$pn]["caption"],
					"type" => html::select(array(
						"value" => $types[$clid][$pn],
						"options" => $type_list,
						"name" => "types[$clid][$pn]"
					)),
					"break_in_el" => $property_list[$pn]["type"] == "classificator" ? html::checkbox(array(
						"name" => "breaks[$clid][$pn]",
						"value" => 1,
						"checked" => $breaks[$clid][$pn] == 1
					)) : "" ,
					"selected" => html::checkbox(array(
						"name" => "selected[$clid][$pn]",
						"value" => 1,
						"checked" => $selected[$clid][$pn] == 1
					)),
				));
			}
		}
		$t->set_sortable(false);
	}

	function _set_prop_settings($arr)
	{
		$arr["obj_inst"]->set_meta("types", $arr["request"]["types"]);
		$arr["obj_inst"]->set_meta("el_breaks", $arr["request"]["breaks"]);
		$arr["obj_inst"]->set_meta("selected", $arr["request"]["selected"]);
	}

	function _update_address_from_req($o, $r)
	{
		$o->set_prop("aadress", isset($r["typo_145"]["p_adr_str"]) ? $r["typo_145"]["p_adr_str"] : $r["p_adr_str"]);
		$o->set_prop("postiindeks", isset($r["typo_145"]["p_adr_zip"]) ? $r["typo_145"]["p_adr_zip"] : $r["p_adr_zip"]);
		$this->set_rel_by_val($o, "linn", isset($r["typo_145"]["p_adr_city"]) ? $r["typo_145"]["p_adr_city"] : $r["p_adr_city"]);
		$this->set_rel_by_val($o, "maakond", isset($r["typo_145"]["p_adr_county"]) ? $r["typo_145"]["p_adr_county"] : $r["p_adr_county"]);
		$adr_i = $o->instance();
		$riiks = $adr_i->get_country_list();
		$this->set_rel_by_val($o, "riik", $riiks[isset($r["typo_145"]["p_adr_ctry"]) ? $r["typo_145"]["p_adr_ctry"] : $r["p_adr_ctry"]]);
		$o->set_name($adr_i->get_name_from_adr($o));
	}

	function set_rel_by_val($o, $prop, $val)
	{
		$pl = $o->get_property_list();
		$reli = $o->get_relinfo();
		$p = $pl[$prop];
		$clid = $reli[$p["reltype"]]["clid"][0];
		$ol = new object_list(array(
			"class_id" => $clid,
			"lang_id" => array(),
			"site_id" => array(),
			"name" => $val
		)); 
		if ($ol->count())
		{
			$fo = $ol->begin();
		}
		else
		{
			$fo = obj();
			$fo->set_class_id($clid);
			$fo->set_parent($o->id());
			$fo->set_name($val);
			$fo->save();
		}
		$o->set_prop($prop, $fo->id());
	}

	function _init_trans_tb(&$t, $o)
	{
		$t->define_field(array(
			"name" => "orig",
			"caption" => t("Omadus"),
			"align" => "center"
		));

		$l = get_instance("languages");
		$ll = $l->get_list();
		foreach($ll as $lid => $lang)
		{
			if ($lid == $o->lang_id())
			{
				continue;
			}
			$t->define_field(array(
				"name" => "l".$lid,
				"caption" => $lang,
				"align" => "center"
			));
		}
	}

	function _trans_tb($arr)
	{
		aw_global_set("output_charset","UTF-8");
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_trans_tb($t, $arr["obj_inst"]);

		$lang_props = $arr["obj_inst"]->meta("lang_props");

		$visible = $arr["obj_inst"]->meta("visible");

		$l = get_instance("languages");
		$ll = $l->get_list(array("all_data" => true));

		$propn = $arr["obj_inst"]->meta("propn");

		$clss = aw_ini_get("classes");
		foreach($visible as $clid => $props)
		{
			$cln = $clss[$clid]["name"];
			foreach($props as $pn => $one)
			{
				$d = array(
					"orig" => $propn[$clid][$pn],
					"class" => "<b>".$cln."</b>"
				);
				foreach($ll as $lid => $lang)
				{
					if ($lid == $arr["obj_inst"]->lang_id())
					{
						continue;
					}
					$d["l".$lid] = html::textbox(array(
						"name" => "d[$clid][$pn][$lid]",
						"value" => iconv($lang["charset"], "utf-8", $lang_props[$clid][$pn][$lid]),
						"size" => 20
					));
				}
				$t->define_data($d);
			}
		}

		$d = array(
			"orig" => t("Liitu nupu tekst"),
			"class" => t("<b>Nuppude tekstid</b>")
		);
		$clid = "bt";
		foreach($ll as $lid => $lang)
		{
			if ($lid == $arr["obj_inst"]->lang_id())
			{
				continue;
			}
			$d["l".$lid] = html::textbox(array(
				"name" => "d[$clid][__join_but][$lid]",
				"value" => iconv($lang["charset"], "utf-8", $lang_props[$clid]["__join_but"][$lid]),
				"size" => 20
			));
		}
		$t->define_data($d);

		$d = array(
			"orig" => t("Salvesta nupu tekst"),
			"class" => t("<b>Nuppude tekstid</b>")
		);
		$clid = "bt";
		foreach($ll as $lid => $lang)
		{
			if ($lid == $arr["obj_inst"]->lang_id())
			{
				continue;
			}
			$d["l".$lid] = html::textbox(array(
				"name" => "d[$clid][__save_but][$lid]",
				"value" => iconv($lang["charset"], "utf-8", $lang_props[$clid]["__save_but"][$lid]),
				"size" => 20
			));
			
		}
		$t->define_data($d);

		$d = array(
			"orig" => t("T&uuml;hista nupu tekst"),
			"class" => t("<b>Nuppude tekstid</b>")
		);
		$clid = "bt";
		foreach($ll as $lid => $lang)
		{
			if ($lid == $arr["obj_inst"]->lang_id())
			{
				continue;
			}
			$d["l".$lid] = html::textbox(array(
				"name" => "d[$clid][__cancel_but][$lid]",
				"value" => iconv($lang["charset"], "utf-8", $lang_props[$clid]["__cancel_but"][$lid]),
				"size" => 20
			));

		}
		$t->define_data($d);


		$d = array(
			"orig" => t("Suunamine"),
			"class" => t("<b>Suuna p&auml;rast registreerumist</b>")
		);
		$clid = "bt";
		foreach($ll as $lid => $lang)
		{
			if ($lid == $arr["obj_inst"]->lang_id())
			{
				continue;
			}
			$d["l".$lid] = html::textbox(array(
				"name" => "d[$clid][__after_join_url][$lid]",
				"value" => iconv($lang["charset"], "utf-8", $lang_props[$clid]["__after_join_url"][$lid]),
				"size" => 20
			));
		}
		$t->define_data($d);


		$t->set_rgroupby(array("class" => "class"));
		$t->set_caption(t("T&otilde;lgi omaduste tekste"));
	}

	function _set_trans_tb($arr)
	{
		$l = get_instance("languages");
		$ll = $l->get_list(array("all_data" => true));
		
		foreach($arr["request"]["d"] as $clid => $d2)
		{
			foreach($d2 as $key => $d3)
			{
				foreach($d3 as $lang_id => $string)
				{
					$arr["request"]["d"][$clid][$key][$lang_id] = iconv("utf-8", $ll[$lang_id]["charset"], $string);
				}
			}
		}
		$arr["obj_inst"]->set_meta("lang_props", $arr["request"]["d"]);
	}

	function _username_element($arr)
	{
		$opts = array("" => t("--vali--"));
		$clss = aw_ini_get("classes");
		foreach($arr["obj_inst"]->meta("visible") as $clid => $items)
		{
			$tmp = obj();
			$tmp->set_class_id($clid);
			$property_list = $tmp->get_property_list();
		
			$opts[$clid] = $clss[$clid]["name"];

			foreach($items as $pn => $one)
			{
				$opts[$clid."_".$pn] = str_repeat("&nbsp;", 10).$property_list[$pn]["caption"];
			}
		}
		$arr["prop"]["options"] = $opts;
	}

	function _init_trans_err_t(&$t, $o)
	{
		$t->define_field(array(
                        "name" => "orig",
                        "caption" => t("Veateade"),
                        "align" => "center"
                ));

                $l = get_instance("languages");
                $ll = $l->get_list();
                foreach($ll as $lid => $lang)
                {
                        $t->define_field(array(
                                "name" => "l".$lid,
                                "caption" => $lang,
                                "align" => "center"
                        ));
                }	
	}

	function _trans_errs_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_trans_err_t($t, $arr["obj_inst"]);

		$l = get_instance("languages");
		$ll = $l->get_list(array("all_data" => true));

		$ermsgs = array(
			"next_filled" => t("J&auml;rgnev v&auml;li peab olema t&auml;idetud!"),
			"user_exists" => t("Sellise kasutajanimega kasutaja on juba olemas!"),
			"pwd_typo" => t("Sisestatud paroolid on erinevad!"),
			"pwd_err" => t("Parool tohib sisaldada ainult numbreid, t&auml;hti ja alakriipsu!"),
			"uid_short" => t("Kasutajanimes peab olema v&auml;hemalt 3 t&auml;hte!"),
			"pwd_short" => t("Paroolis peab olema v&auml;hemalt 3 t&auml;hte!"),
			"wrong_email" => t("Palun sisestage korrektne e-maili aadress!")
		);
		$lang_errs = $arr["obj_inst"]->meta("lang_errs");
		foreach($ermsgs as $id => $msg)
		{
			$d = array(
				"orig" => $msg
			);
			foreach($ll as $lid => $lang)
			{
				$d["l".$lid] = html::textbox(array(
					"name" => "d[$id][$lid]",
					"value" => $lang_errs[$id][$lid],
					"size" => 20
				));
			}
			$t->define_data($d);
		}
		$t->set_caption(t("T&otilde;lgi veateateid"));
	}

	function _init_trans_ttl_t(&$t, $o)
	{
		$t->define_field(array(
			"name" => "orig",
			"caption" => t("Pealkiri"),
			"align" => "center"
		));

		$l = get_instance("languages");
		$ll = $l->get_list();
		foreach($ll as $lid => $lang)
		{
			if ($lid == $o->lang_id())
			{
				continue;
			}
			$t->define_field(array(
				"name" => "l".$lid,
				"caption" => $lang,
				"align" => "center"
			));
		}
	}

	function _trans_ttl_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_trans_ttl_t($t, $arr["obj_inst"]);

                $lang_seps = $arr["obj_inst"]->meta("lang_seps");

                $l = get_instance("languages");
                $ll = $l->get_list(array("all_data" => true));

		$seps = $arr["obj_inst"]->meta("join_seps");
                foreach($seps as $sepid => $sep)
                {
	                $d = array(
	                        "orig" => $sep,
                        );
                        foreach($ll as $lid => $lang)
                        {
                		if ($lid == $arr["obj_inst"]->lang_id())
                                {
                                	continue;
                                }
                                $d["l".$lid] = html::textbox(array(
                                	"name" => "d[$sepid][$lid]",
                                        "value" => $lang_seps[$sepid][$lid],
                                        "size" => 20
                                ));
                        }
                        $t->define_data($d);
                }
                $t->set_caption(t("T&otilde;lgi vahepealkiri"));
	}

	function _handle_images_upload($data_o)
	{
		$imgs = array_values($data_o->connections_from(array("type" => "RELTYPE_IMAGE")));
		$i = array();
		$i[1] = $imgs[0];
		$i[2] = $imgs[1];
		$i[3] = $imgs[2];

		for ($a = 1; $a < 4; $a++)
		{
			if (is_uploaded_file($_FILES["c_img_".$a]["tmp_name"]))
			{
				$img_id = null;
				if ($i[$a])
				{
					$img_id = $i[$a]->prop("to");
				}
				$ii = get_instance(CL_IMAGE);
				$rv = $ii->add_upload_image("c_img_".$a, $data_o->id(), $img_id);
				$data_o->connect(array("to" => $rv["id"], "type" => "RELTYPE_IMAGE"));
			}
		}
	}

	function can_add($arr)
	{
		$reserved = array("system");

		extract($arr);
		if (in_array($a_uid,$reserved))
		{
			return false;
		};
		$q = "SELECT * FROM users WHERE uid = '$a_uid'";
		$this->db_query($q);
		$row = $this->db_next();

		if ($arr["sj"])
		{
			$lang_errs = $arr["sj"]->meta("lang_errs");
			$lang_id = aw_global_get("lang_id");
			if (aw_ini_get("user_interface.full_content_trans"))
			{
				$lang_id = aw_global_get("ct_lang_id");
			}
		}

		if ($row)
		{
			$te = t("Sellise kasutajanimega kasutaja on juba olemas!");
			if (!empty($lang_errs["user_exists"][$lang_id]))
			{
				$te = $lang_errs["user_exists"][$lang_id];
			}
			$_SESSION["add_state"]["error"] = $te;
			return false;
		}

		if (!is_valid("uid",$a_uid))
		{
			$te = t("Kasutajanimes tohivad sisalduda ainult t&auml;hed, numbrid ja alakriips!");
			if (!empty($lang_errs["uid_short"][$lang_id]))
			{
				$te = $lang_errs["uid_short"][$lang_id];
			}
			$_SESSION["add_state"]["error"] = $te;
			return false;
		}

		if ($pass != $pass2)
		{
			$te = t("Sisestatud paroolid on erinevad!");
			if (!empty($lang_errs["pwd_typo"][$lang_id]))
			{
				$te = $lang_errs["pwd_typo"][$lang_id];
			}
			$_SESSION["add_state"]["error"] = $te;
			return false;
		}

		if (!is_valid("password", $pass))
		{
			$te = t("Parool tohib sisaldada ainult numbreid, t&auml;hti ja alakriipsu!");
			if (!empty($lang_errs["pwd_err"][$lang_id]))
			{
				$te = $lang_errs["pwd_err"][$lang_id];
			}
			$_SESSION["add_state"]["error"] = $te;
			return false;
		}

		if (strlen($a_uid) < 3)
		{
			$te = t("Kasutajanimes peab olema v&auml;hemalt 3 t&auml;hte!");
			if (!empty($lang_errs["uid_short"][$lang_id]))
			{
				$te = $lang_errs["uid_short"][$lang_id];
			}
			$_SESSION["add_state"]["error"] = $te;
			return false;
		}

		if (strlen($pass) < 3)
		{
			$te = t("Paroolis peab olema v&auml;hemalt 3 t&auml;hte!");
			if (!empty($lang_errs["pwd_short"][$lang_id]))
			{
				$te = $lang_errs["pwd_short"][$lang_id];
			}
			$_SESSION["add_state"]["error"] = $te;
			return false;
		}
		$_SESSION["add_state"]["error"] = "";
		return true;
	}
}
?>
