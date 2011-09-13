<?php
// conference_planning.aw - Konverentsi planeerimine
/*
@classinfo syslog_type=ST_CONFERENCE_PLANNING relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general

@property jrk type=textbox field=jrk
@caption Jrk

@property single_count type=textbox field=meta method=serialize
@caption &Uuml;hekohaliste tubade arv

@property double_count type=textbox field=meta method=serialize
@caption Kahekohaliste tubade arv

@property suite_count type=textbox field=meta method=serialize
@caption Sviitide arv

@property meeting_pattern_max_days type=textbox field=meta method=serialize
@caption Koosoleku aja mustri p&auml;evade arv

@property search_result_max type=textbox field=meta method=serialize default=3
@caption Maksimaalne tulemuste arv

@property search_from type=relpicker reltype=RELTYPE_LOCATION field=meta method=serialize multiple=1
@caption Otsitavad asukohad

@property countries type=relpicker multiple=1 field=meta method=serialize reltype=RELTYPE_COUNTRIES
@caption Riigid

@property redir_doc type=relpicker field=meta method=serialize reltype=RELTYPE_REDIR_DOC
@caption Edasisuunamise dokument

@property send_email type=checkbox field=meta method=serialize ch_value=1 default=0
@caption Saada email hotellidele

@property email type=relpicker field=meta method=serialize reltype=RELTYPE_EMAIL
@caption Saatja E-mail

@property subject type=textbox field=meta method=serialize
@caption Hotelliteavituse e-maili teema

@property submission_dir type=relpicker field=meta method=serialize reltype=RELTYPE_SUBMISSION_DIR
@caption RFP kataloog

@property document type=relpicker reltype=RELTYPE_WEBSHOW_DOCUMENT field=meta method=serialize
@caption Konverentsiplaneerija dokument

// metadata for views
@property help_views type=hidden field=meta method=serialize no_caption=1
@property help_props type=hidden field=meta method=serialize no_caption=1

@groupinfo webform caption="Veebivorm"
@default group=webform

	@groupinfo webform_detail caption="Vaated" parent=webform
	@default group=webform_detail
		@property views_tb type=toolbar no_caption=1
		@layout hsplit type=hbox width=15%:85%
			@layout treeview type=vbox closeable=1 area_caption=Vaated parent=hsplit
				@property views_tree type=treeview no_caption=1 parent=treeview

			@layout rpane type=vbox parent=hsplit

				@layout trans type=vbox closeable=1 area_caption=T&otilde;lked parent=rpane
					@property trans type=text no_caption=1 parent=trans

				@layout controller type=vbox closeable=1 area_caption=Konfiguratsioon parent=rpane
					@property element_wid type=textbox field=meta method=serialize captionside=top parent=controller store=no
					@caption Elemendi ID veebis

					@property meta_chooser type=select store=no captionside=top parent=controller
					@caption Valikud

					@property show_controller type=relpicker field=meta reltype=RELTYPE_SHOW_CONTROLLER method=serialize captionside=top parent=controller
					@caption N&auml;itamise kontroller

					@property save_controller type=relpicker field=meta reltype=RELTYPE_SAVE_CONTROLLER method=serialize captionside=top parent=controller
					@caption Salvestamise kontroller

	@groupinfo webform_sub caption="Seaded" parent=webform
	@default group=webform_sub
		@property metamgr type=relpicker reltype=RELTYPE_METAMANAGER field=meta method=serialize
		@caption Muutujate haldus

		@property submit_controller type=relpicker reltype=RELTYPE_SUBMIT_CONTROLLER field=meta method=serialize
		@caption Vormi l&otilde;petamise kontroller



@groupinfo mails caption="E-mailid"
@default group=mails

	@property mails type=text no_caption=1 store=no

	@property city_mails type=table no_caption=1 store=no

@groupinfo users_notification caption="Kasutaja teavitus"
@default group=users_notification

	@property usr_send_mail type=checkbox ch_value=1 default=0 field=meta method=serialize
	@caption Saada kasutajale teavitus

	@property usr_subject type=textbox field=meta method=serialize
	@caption Kasutajateavituse e-maili teema

	@property usr_contents type=textarea field=meta method=serialize
	@caption Kasutajateavituse e-maili sisu

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

@reltype REDIR_DOC value=2 clid=CL_DOCUMENT
@caption Edasisuunamise dokument

@reltype COUNTRIES value=1 clid=CL_CRM_COUNTRY
@caption Riik

@reltype LOCATION value=3 clid=CL_LOCATION
@caption Otsitav asukoht

@reltype EMAIL value=4 clid=CL_ML_MEMBER
@caption E-Mail

@reltype SHOW_CONTROLLER value=5 clid=CL_CFGCONTROLLER
@caption N&auml;itamise kontroller

@reltype SAVE_CONTROLLER value=6 clid=CL_CFGCONTROLLER
@caption Salvestamise kontroller

@reltype METAMANAGER value=7 clid=CL_METAMGR
@caption Muutujate haldus

@reltype SUBMIT_CONTROLLER value=8 clid=CL_CFGCONTROLLER
@caption Vormi l&otilde;petamise kontroller

@reltype SUBMISSION_DIR value=9 clid=CL_MENU
@caption RFP kataloog

@reltype WEBSHOW_DOCUMENT value=10 clid=CL_DOCUMENT
@caption Konverentsiplaneerija dokument

*/

define(CP_DEFAULT_LANG, "en");
define(CONFIRM_ID, "confirm_submit_checkbox");
define(TYPE_SEPARATOR, 1);
define(TYPE_ELEMENT, 2);
define(TYPE_TEXT, 3);

class conference_planning extends class_base
{
	function conference_planning()
	{
		$this->init(array(
			"tpldir" => "applications/conference_planning_webview",
			"clid" => CL_CONFERENCE_PLANNING
		));

		lc_site_load("conference_planning_new", $this);

		$this->wd = array(
			0 => t("Monday"),
			1 => t("Tuesday"),
			2 => t("Wednesday"),
			3 => t("Thursday"),
			4 => t("Friday"),
			5 => t("Saturday"),
			6 => t("Sunday"),
		);

		$this->table_forms = array(
			0 => t("Theatre"),
			1 => t("Diplomat"),
			2 => t("Banquet"),
			3 => t("School"),
			4 => t("Fishbone"),
			5 => t("U-Shape"),
			6 => t("Cabaret"),
			7 => t("Dinner"),
			8 => t("Coctail"),
		);

		$this->tech_equip = array(
			0 => t("Data projector"),
			1 => t("TV/DVD"),
			2 => t("Slide projector"),
			3 => t("Mircophones"),
			4 => t("OHP"),
		);

		$this->catering_types = array(
			0 => t("Morning coffe break"),
			1 => t("Lunch"),
			2 => t("Afternoon coffe break"),
			3 => t("Fruit assortment in the room"),
			4 => t("Sodas"),
			5 => t("Non-stop coffee"),
		);

		$this->countrys = array(
			1 => t("Estionia"),
			2 => t("Latvia"),
			3 => t("Lietuva"),
		);

		$this->required_fields_error = array(
			1 => array(
				"function_name" => t("&Uuml;rituse nimi on puudu"),
			),
			3 => array(
				"main_arrival_date" => t("Saabumisaeg on puudu"),
				"main_departure_date" => t("Lahkumisaeg on puudu"),
			),
			6 => array(
				"billing_phone_number" => t("Telefoninumber on puudu"),
				"billing_email" => t("E-mail on puudu"),
			),
		);

		$this->required_fields = array(
			"1" => array(
				"function_name",
			),
			"3" => array(
				"main_arrival_date",
				"main_departure_date",
			),
			"6" => array(
				"billing_phone_number",
				"billing_email",
			),
		);


		$this->lc_load("conference_planning", "lc_conference_planning");
		lc_site_load("conference_planning", $this);


		$this->trans_props = array(
			"subject", "usr_subject", "usr_contents"
		);


		/* wheee, totally new conference planning
			anyway, these here are diffent type of form types, most of them can be separately cofigured from admin
		*/
		$this->form_types = array(
			"textbox",
			"date_textbox",
			"datetime_textbox",
			"event_type",
			"checkbox",
			"textarea",
			"radio_chooser",
			"textarea",
			"meeting_patten",
			"select",
			"table",
			"technical_equipment",
			"search_result",
			"separator",
		);

		$this->gen_langs();
	}

	function gen_langs()
	{

		$l = get_instance("languages");
		$ll = $l->get_list(array(
			//"ignore_status" => true,
			"all_data" => true,
		));
		foreach($ll as $data)
		{
			$this->lang_charset[$data["acceptlang"]] = $data["charset"];
		}
	}

	function get_form_elements_data($prop = false)
	{
		if(!$prop)
		{
			return false;
		}
		$prop = (substr($prop, 0, 5) == "data_")?substr($prop, 5):$prop;
		switch($prop)
		{
			case "gen_multi_day":
				$ret = array(
					"form" => "radio_chooser",
				);
				break;

			case "gen_response_date":
			case "gen_decision_date":
			case "gen_arrival_date":
			case "gen_departure_date":

			case "mf_start_date":
			case "mf_end_date":
			case "gen_acc_start":
			case "gen_acc_end":
				$ret = array(
					"form" => "date_textbox",
				);
				break;

			case "gen_alternative_dates":
				$ret = array(
					"form" => "mixed",
					"add_rows" => "manual",
					"fields" => array(
						"date_type" => array(
						),
						"arrival_date" => array(
						),
						"departure_date" => array(
						),
						"remove" => array(
						),
					),
				);
				break;
			case "mf_table":
				$ret["fields"] = array(
					"name" => t("&Uuml;rituse nimi"),
					"type" => t("&Uuml;rituse t&uuml;&uuml;p"),
					"persons" => t("Osalejate arv"),
					"door_sign" => t("Uksesilt"),
					"table_form" => t("Ruumi paigutus"),
					"24h" => t("Hoia ruumi &ouml;&ouml;p&auml;ev"),
				);
			case "mf_catering":
				$ret["fields"] = array(
					"data_mf_catering_type" => t("&Uuml;rituse t&uuml;&uuml;p"),
					"data_mf_catering_start" => t("Algusaeg"),
					"data_mf_catering_end" => t("L&otilde;puaeg"),
					"data_mf_catering_attendees_no" => t("Osalejate arv"),
				);
			case "af_table":
			case "af_catering":

			case "search_results":
			case "search_selected":
				$ret["form"] = "table";
				$ret["edit_link"] = true;
				$ret["remove_link"] = true;
				break;


			case "gen_open_for_alternative_dates":
			case "gen_accommodation_requirements":
			case "gen_dates_are_flexible":
			case "mf_breakout_rooms":
			case "mf_24h":
				$ret = array(
					"form" => "checkbox",
				);
				break;

			case "mf_tech":
				$ret = array(
					"form" => "checkboxes", // not implemented
				);
				break;

			case "gen_meeting_pattern":
				$ret = array(
					"form" => "meeting_pattern",
				);
				break;

			case "mf_additional_catering":
			case "mf_additional_entertainment":
			case "mf_additional_decorations":
			case "mf_additional_tech":
			case "mf_breakout_room_additional_tech":
			case "gen_date_comments":
				$ret = array(
					"form" => "textarea",
				);
				break;

			case "search_result":
				$ret = array(
					"form" => "search_result",
				);
				break;


			case "mf_catering_type":
			case "mf_event_type":
				$ret = array(
					"form" => "event_type",
				);
				break;

			case "subm_name":
			case "subm_organisation":
			case "subm_organizer":
			case "subm_country":
			case "subm_email":
			case "subm_phone":

			case "gen_function_name":
			case "gen_attendees_no":

			case "mf_door_sign":
			case "mf_attendees_no":
			case "mf_catering_start":
			case "mf_catering_end":
			case "mf_catering_attendees_no":

			case "billing_email":
			case "billing_phone":
			case "billing_name":
			case "billing_zip":
			case "billing_city":
			case "billing_street":
			case "billing_contact":
			case "billing_company":
				$ret = array(
					"form" => "textbox",
				);
				break;

			case "subm_contact_preference":

			case "gen_single_rooms":
			case "gen_double_rooms":
			case "gen_suites":
			case "gen_city":
			case "gen_hotel":
			case "gen_package":

			case "billing_country":
			case "mf_table_form":
			case "mf_breakout_room_setup":
				$ret = array(
					"form" => "select",
				);
				break;

			case "text":
				$ret = array(
					"form" => "text",
					"no_store" => 1,
				);
				break;

			case "separator":
				$ret = array(
					"form" => "separator",
					"no_store" => 1,
				);
				break;

			default:
				$ret = false;
				break;
		}
		return $ret;
	}

	/**
		@comment
			checks whater to use given sub's required fields or leave them unrequired
	**/
	function required_fields_conditions($sub, $data)
	{
		$retval = true;
		switch($sub)
		{
			case 3:
				if(!$data[$sub]["needs_rooms"])
				{
					$retval = false;
				}
				break;
		}
		return $retval;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "meta_chooser":
				if(strlen($view = $arr["request"]["view_no"]) && strlen($elem = $arr["request"]["element"]))
				{
					if($this->can("view",($mgr = $arr["obj_inst"]->prop("metamgr"))))
					{
						$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));
						$prop["selected"] = $views[$view]["elements"][$elem]["choices"];
						$list = new object_list(array(
							"class_id" => CL_META,
							"parent" => $mgr,
						));
						$prop["options"][0] = t("-- Vali --");
						foreach($list->arr() as $obj)
						{
							$prop["options"][$obj->id()] = $obj->name();
						}
					}
					else
					{
						return PROP_IGNORE;
					}
				}
			//-- get_property --//
			case "show_controller":
			case "save_controller":
				if(!strlen($arr["request"]["view_no"]) && !strlen($arr["request"]["element"]))
				{
					return PROP_IGNORE;
				}
				break;
			case "views_tb":
				$tb = $prop["vcl_inst"];
				$isv = strlen($view_no = $arr["request"]["view_no"]);
				$ise = strlen($element = $arr["request"]["element"]);
				$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));

				// start add submenu
				$tb->add_menu_button(array(
					"name" => "add",
					"tooltip" => t("Lisa"),
				));
				// add view
				$tb->add_menu_item(array(
					"parent" => "add",
					"text" => t("Vaade"),
					"action" => "add_view",
				));

				// add element to active view
				if($isv) // a view is selected
				{
					$act_view_name = strlen($_t = $views[$view_no]["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$views[$view_no]["trans"][CP_DEFAULT_LANG];
					$to_view = sprintf(t("Vaatesse '%s', elemendi ette"), "<b>".$this->show_cs($act_view_name)."</b>");
					$add_elem_to_view = sprintf(t("Vaatesse '%s'"), "<b>".$this->show_cs($act_view_name)."</b>");

					// add element to view
					$tb->add_sub_menu(array(
						"parent" => "add",
						"name" => "add_elem",
						"text" => t("Element"),
					));
					$tb->add_menu_item(array(
						"parent" => "add_elem",
						"text" => $add_elem_to_view,
						"link" => "#",
					));
					$tb->add_menu_separator(array(
						"parent" => "add_elem",
					));

					$cfg = get_instance("cfg/cfgutils");
					$list = $cfg->load_properties(array(
						"clid" => CL_RFP
					));
					$grinfo = $cfg->get_groupinfo();
					$list = array_filter($list, array($this, "__callback_filter_prplist"));
					uasort($list, array($this, "__callback_sort_prplist"));
					foreach($list as $_data)
					{
						if(is_array($_data["group"]))
						{
							foreach($_data["group"] as $g)
							{
								$groups[$g] = $g;
							}
						}
						else
						{
							$groups[$_data["group"]] = $_data["group"];
						}
					}
					foreach($groups as $group)
					{
						$tb->add_sub_menu(array(
							"parent" => "add_elem",
							"name" => "add_elem_".$group,
							"text" => $this->show_cs($grinfo[$group]["caption"]),
						));
					}

					foreach($list as $prp => $data)
					{
						if(!is_array($data["group"]))
						{
							$data["group"] = array($data["group"]);
						}
						foreach(array_unique($data["group"]) as $g)
						{
							$tb->add_menu_item(array(
								"parent" => "add_elem_".$g,
								"text" => ($data["caption"])?$this->show_cs($data["caption"]):$prp,
								"link" => $this->mk_my_orb("add_element_to_view", array(
									"element" => $prp,
									"view_no" => $view_no,
									"planner" => $arr["obj_inst"]->id(),
									"return_url" => get_ru(),
									"caption" => $data["caption"]?$data["caption"]:$prp,
								)),
							));
						}
					}

					// add separator to view
					$tb->add_sub_menu(array(
						"parent" => "add",
						"name" => "add_separator",
						"text" => t("Eraldaja"),
					));
					$tb->add_menu_item(array(
						"parent" => "add_separator",
						"text" => $to_view,
						"link" => "#",
					));
					$tb->add_menu_separator(array(
						"parent" => "add_separator",
					));

					// add textrow to view
					$tb->add_sub_menu(array(
						"parent" => "add",
						"name" => "add_textrow",
						"text" => t("Tekstirida"),
					));
					$tb->add_menu_item(array(
						"parent" => "add_textrow",
						"text" => $to_view,
						"link" => "#",
					));
					$tb->add_menu_separator(array(
						"parent" => "add_textrow",
					));

					// loop over elements ad add those to separator & textrow submenus
					foreach($views[$view_no]["elements"] as $el_id => $e)
					{
						$name = strlen($_t = $e["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$e["trans"][CP_DEFAULT_LANG];
						$name = $this->show_cs($name);
						$name = ($e["type"] == TYPE_SEPARATOR)?"<b>".$name."</b>":$name;
						$name = ($e["type"] == TYPE_TEXT)?"<i>".$name."</i>":$name;
						$tb->add_menu_item(array(
							"parent" => "add_separator",
							"text" => $name,
							"link" => $this->mk_my_orb("add_separator_to_view", array(
								"element" => $el_id,
								"view_no" => $view_no,
								"planner" => $arr["obj_inst"]->id(),
								"type" => TYPE_SEPARATOR,
								"return_url" => get_ru(),
							)),
						));
						$tb->add_menu_item(array(
							"parent" => "add_textrow",
							"text" => $name,
							"link" => $this->mk_my_orb("add_separator_to_view", array(
								"element" => $el_id,
								"view_no" => $view_no,
								"planner" => $arr["obj_inst"]->id(),
								"type" => TYPE_TEXT,
								"return_url" => get_ru(),
							)),
						));
					}
					$tb->add_menu_separator(array(
						"parent" => "add_separator",
					));
					$tb->add_menu_item(array(
						"parent" => "add_separator",
						"text" => "<b>".t("Viimaseks")."</b>",
						"link" => $this->mk_my_orb("add_separator_to_view", array(
							"element" => "-1",
							"view_no" => $view_no,
							"planner" => $arr["obj_inst"]->id(),
							"type" => TYPE_SEPARATOR,
							"return_url" => get_ru(),
						)),
					));
					$tb->add_menu_separator(array(
						"parent" => "add_textrow",
					));
					$tb->add_menu_item(array(
						"parent" => "add_textrow",
						"text" => "<b/>".t("Viimaseks")."<b/>",
						"link" => $this->mk_my_orb("add_separator_to_view", array(
							"element" => "-1",
							"view_no" => $view_no,
							"planner" => $arr["obj_inst"]->id(),
							"type" => TYPE_TEXT,
							"return_url" => get_ru(),
						)),
					));
				}
				// add submenu ends

				// save page
				$tb->add_button(array(
					"name" => "save",
					"tooltip" => t("Salvesta"),
					"img" => "save.gif",
					"action" => "",
				));



				// start move submenu
				if($isv)
				{
					$tb->add_menu_button(array(
						"name" => "move",
						"tooltip" => t("Liiguta"),
						"img" => "refresh.gif",
					));
					// move view
					$vname = strlen($_t = $views[$view_no]["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$views[$view_no]["trans"][CP_DEFAULT_LANG];
					$tb->add_sub_menu(array(
						"parent" => "move",
						"name" => "move_view",
						"text" => sprintf(t("Vaade '%s'"), $this->show_cs($vname)),
					));
					$tb->add_menu_item(array(
						"parent" => "move_view",
						"text" => "<b>".t("Vaate ette")."</b>",
						"link" => "#",
					));
					$tb->add_menu_separator(array(
						"parent" => "move_view",
					));


					foreach($views as $vid => $v)
					{
						$name = strlen($_t = $v["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$v["trans"][CP_DEFAULT_LANG];
						$name = $this->show_cs($name);
						if($vid == $view_no)
						{
							$name = "<u>".$name."</u>";
							$link = "#";
						}
						else
						{
							$link = $this->mk_my_orb("move_view", array(
								"view_no" => $view_no,
								"planner" => $arr["obj_inst"]->id(),
								"move_to" => $vid,
								"return_url" => get_ru(),
							));
						}

						$tb->add_menu_item(array(
							"parent" => "move_view",
							"text" => $name,
							"url" => $link,
						));
					}
					$tb->add_menu_separator(array(
						"parent" => "move_view",
					));
					$tb->add_menu_item(array(
						"parent" => "move_view",
						"text" => "<b>".t("Viimaseks")."</b>",
						"url" => $this->mk_my_orb("move_view", array(
								"view_no" => $view_no,
								"planner" => $arr["obj_inst"]->id(),
								"move_to" => "-1",
								"return_url" => get_ru(),
							)),
					));

					// move element
					if($ise)
					{
						$ename = strlen($_t = $views[$view_no]["elements"][$element]["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$views[$view_no]["elements"][$element]["trans"][CP_DEFAULT_LANG];
						$tb->add_sub_menu(array(
							"parent" => "move",
							"name" => "move_element",
							"text" => sprintf(t("Element '%s'"), $this->show_cs($ename)),
						));
						$tb->add_menu_item(array(
							"parent" => "move_element",
							"text" => "<b>".t("Elemendi ette")."</b>",
							"link" => "#",
						));
						$tb->add_menu_separator(array(
							"parent" => "move_element",
						));
						foreach($views[$view_no]["elements"] as $eid => $e)
						{

							$self = ($eid == $element);
							$name = strlen($_t = $e["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$e["trans"][CP_DEFAULT_LANG];
							$name = $this->show_cs($name);
							if($self)
							{
								$name = "<u>".$name."</u>";
								$link = "#";
							}
							else
							{
								$link = $this->mk_my_orb("move_element_in_view", array(
									"element" => $element,
									"move_to" => $eid,
									"view_no" => $view_no,
									"planner" => $arr["obj_inst"]->id(),
									"return_url" => get_ru(),
								));
							}
							if($e["type"] == TYPE_SEPARATOR)
							{
								$name = "<b>".$name."</b>";
							}
							elseif($e["type"] == TYPE_TEXT)
							{
								$name = "<i>".$name."</i>";
							}
							$tb->add_menu_item(array(
								"parent" => "move_element",
								"text" => $name,
								"link" => $link,
							));
						}
						$tb->add_menu_separator(array(
							"parent" => "move_element",
						));

						$tb->add_menu_item(array(
							"parent" => "move_element",
							"text" => "<b>".t("Viimaseks")."</b>",
							"link" => $this->mk_my_orb("move_element_in_view", array(
								"element" => $element,
								"move_to" => "-1",
								"view_no" => $view_no,
								"planner" => $arr["obj_inst"]->id(),
								"return_url" => get_ru(),
							)),
						));
					}
				}
				// move submenu ends

				// start remove submenu
				$tb->add_menu_button(array(
					"name" => "remove",
					"tooltip" => t("Kustuta"),
					"img" => "delete.gif",
				));
				$tb->add_sub_menu(array(
					"parent" => "remove",
					"name" => "remove_view",
					"text" => t("Vaade"),
					"link" => "#",
				));
				foreach($views as $vid => $view)
				{
					$vname = strlen($_t = $view["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$view["trans"][CP_DEFAULT_LANG];
					$tb->add_menu_item(array(
						"parent" => "remove_view",
						"text" => $this->show_cs($vname),
						"link" => $this->mk_my_orb("remove_view", array(
								"view_no" => $vid,
								"planner" => $arr["obj_inst"]->id(),
								"return_url" => get_ru(),
						)),
					));
				}
				if($isv)
				{
					$tb->add_sub_menu(array(
						"parent" => "remove",
						"name" => "remove_element",
						"text" => t("Element"),
					));

					$vname = strlen($_t = $views[$view_no]["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$views[$view_no]["trans"][CP_DEFAULT_LANG];
					$tb->add_menu_item(array(
						"parent" => "remove_element",
						"text" => sprintf(t("Vaatest '%s'"), "<b>".$this->show_cs($vname)."</b>"),
						"url" => "#",
					));
					$tb->add_menu_separator(array(
						"parent" => "remove_element",
					));
					foreach($views[$view_no]["elements"] as $eid => $e)
					{
						$name = strlen($_t = $e["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$e["trans"][CP_DEFAULT_LANG];
						$name = $this->show_cs($name);
						if($e["type"] == TYPE_SEPARATOR)
						{
							$name = "<b>".$name."</b>";
						}
						elseif($e["type"] == TYPE_TEXT)
						{
							$name = "<i>".$name."</i>";
						}
						$tb->add_menu_item(array(
							"parent" => "remove_element",
							"text" => $name,
							"link" => $this->mk_my_orb("remove_element_from_view", array(
								"element" => $eid,
								"view_no" => $view_no,
								"planner" => $arr["obj_inst"]->id(),
								"return_url" => get_ru(),
							)),
						));
					}
				}
				// remove submenu ends

				$prop["value"] = $tb->get_toolbar();
				break;
			case "trans":
				$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));
				if(strlen($view = $arr["request"]["view_no"]) && strlen($elem = $arr["request"]["element"]))
				{
					$trans = $views[$view]["elements"][$elem]["trans"];
				}
				elseif(strlen($view))
				{
					$trans = $views[$view]["trans"];
				}
				else
				{
					return PROP_IGNORE;
				}
				$l = get_instance("languages");
				$ll = $l->get_list(array(
					//"ignore_status" => true,
					"all_data" => true,
				));
				foreach($ll as $lid => $lang)
				{

					$html .= $lang["name"]." : ".html::textbox(array(
						"name" => "trans[".$lang["acceptlang"]."]",
						"value" => $this->show_cs($trans[$lang["acceptlang"]], $lang["acceptlang"]),
					))."<br/>";
				}
				$prop["value"] = $html;
				break;
			case "element_wid":
				if(strlen($view = $arr["request"]["view_no"]) && strlen($element = $arr["request"]["element"]))
				{
					$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));
					$prop["value"] = $views[$view]["elements"][$element]["wid"];
				}
				break;

			case "views_tree":
				$t = $prop["vcl_inst"];
				$t->start_tree(array(
					"tree_id" => "views_tree",
					"tree_type" => TREE_DHTML,
				));

				$cfg = get_instance("cfg/cfgutils");
				$list = $cfg->load_properties(array(
					"clid" => CL_RFP
				));
				$list = array_filter($list, array($this, "__callback_filter_prplist"));
				uasort($list, array($this, "__callback_sort_prplist"));

				foreach(aw_unserialize($arr["obj_inst"]->prop("help_views")) as $id => $view)
				{

					// add view node to tree
					$request = $arr["request"];
					unset($request["element"]);
					$request["view_no"] = $id;

					$name = strlen($_t = $view["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$view["trans"][CP_DEFAULT_LANG];
					$name = $this->show_cs($name);
					$name = strlen($name)?$name:t("Nimetu");
					$name = ($arr["request"]["view_no"] == $id && strlen($arr["request"]["view_no"]) && empty($arr["request"]["element"]))?"<b>".$name."</b>":$name;
					$t->add_item(0, array(
						"id" => "view_".$id,
						"name" => $name,
						"url" => $this->mk_my_orb("change", $request),
					));
					// add element nodes to tree
					foreach($view["elements"] as $el_id => $element)
					{
						$request = $arr["request"];
						$request["view_no"] = $id;
						$request["element"] = $el_id;

						$data = $this->get_form_elements_data($element["name"]);
						$name = strlen($_t = $element["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))])?$_t:$element["trans"][CP_DEFAULT_LANG];
						$name = $this->show_cs($name);

						if(!strlen(trim($name)))
						{
							$name = "<font color=\"red\">".t("Nimetu")."</font>";
							$name = ($arr["request"]["view_no"] == $id && strlen($arr["request"]["view_no"]) && $arr["request"]["element"] == $el_id && strlen($arr["request"]["element"]))?"<b>".$name."</b>":$name;
						}
						else
						{
							$name = ($arr["request"]["view_no"] == $id && strlen($arr["request"]["view_no"]) && $arr["request"]["element"] == $el_id && strlen($arr["request"]["element"]))?"<b>".$name."</b>":$name;
						}
						if($element["type"] == TYPE_SEPARATOR)
						{
							$iconurl = aw_ini_get("baseurl")."/automatweb/images/icons/rte_indent.gif";
						}
						else
						{
							$iconurl = aw_ini_get("baseurl")."/automatweb/images/icons/class_86.gif";
						}
						$t->add_item("view_".$id, array(
							"id" => "view_".$id."_elem_".$el_id,
							"name" => $name,
							"url" => $this->mk_my_orb("change", $request),
							"iconurl" => $iconurl,
						));
					}
				}
				break;

			case "mails":
				
				$vcl = new vcl_table();
				$vcl->define_field(array(
					"name" => "hotel",
					"caption" => t("Hotell"),
				));
				$vcl->define_field(array(
					"name" => "mail",
					"caption" => t("E-Mail"),
				));
				foreach($arr["obj_inst"]->prop("search_from") as $loc)
				{
					if(!$this->can("view", $loc))
					{
						continue;
					}
					$loc = obj($loc);
					$vcl->define_data(array(
						"hotel" => $loc->name(),
						"mail" => html::textbox(array(
							"value" => $loc->prop("email.mail"),
							"name" => "loc[".$loc->id()."]",
						)),
					));
				}
				$prop["value"] = $vcl->get_html();
				break;
		};
		return $retval;
	}

	function show_cs($str, $lang_from = false)
	{
		$charset_from = $lang_from?$this->lang_charset[$lang_from]:$this->lang_charset[(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))];
		return strlen($_t = iconv($charset_from, "UTF-8", $str))?$_t:"";
	}

	function save_cs($str, $lang_to = false)
	{
		$charset_to = $lang_to?$this->lang_charset[$lang_to]:$this->lang_charset[(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))];
		return strlen($_t = iconv("UTF-8", $charset_to, $str))?$_t:"";
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
			case "help_views":
				return PROP_IGNORE;
				break;
			case "meta_chooser":
				if(strlen($view = $arr["request"]["view_no"]) && strlen($elem = $arr["request"]["element"]))
				{
					$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));
					$views[$view]["elements"][$elem]["choices"] = $prop["value"];
					$arr["obj_inst"]->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
					$arr["obj_inst"]->save();
				}
				break;
			case "show_controller":
			case "save_controller":
				if(strlen($arr["request"]["view_no"]) && strlen($arr["request"]["element"]))
				{
					$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));
					$views[$arr["request"]["view_no"]]["elements"][$arr["request"]["element"]][$prop["name"]] = $prop["value"];
					$arr["obj_inst"]->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
					$arr["obj_inst"]->save();
				}
				$retval =  PROP_IGNORE;
				break;
			case "element_wid":
				if(strlen($view = $arr["request"]["view_no"]) && strlen($element = $arr["request"]["element"]))
				{
					$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));
					$views[$view]["elements"][$element]["wid"] = $prop["value"];
					$arr["obj_inst"]->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
					$arr["obj_inst"]->save();
				}
				break;
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
			case "trans":
				$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));
				if(strlen($view = $arr["request"]["view_no"]) && strlen($elem = $arr["request"]["element"]))
				{
					$trans = &$views[$view]["elements"][$elem]["trans"];
				}
				elseif(strlen($view))
				{
					$trans = &$views[$view]["trans"];
				}
				else
				{
					return PROP_IGNORE;
				}
				$trans = $prop["value"];
				foreach($trans as $lang => $str)
				{
					$trans[$lang] = $this->save_cs($str, $lang);
				}
				$arr["obj_inst"]->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
				$arr["obj_inst"]->save();
				break;

		}
		return $retval;
	}

	function __callback_filter_prplist($a)
	{
		return (substr($a["name"],0,5) == "data_" && substr($a["name"], -5,5) != "admin");
	}

	function __callback_sort_prplist($a, $b)
	{
		return strcasecmp($a["caption"],$b["caption"]);
	}

	/**
		@attrib name=add_separator_to_view params=name
		@param element required type=string
		@param view_no required type=int
		@param planner required type=int
		@param type required type=int
		@param return_url optional type=string
	**/
	function add_separator_to_view($arr)
	{
		if($this->can("view", $arr["planner"]))
		{
			$obj = obj($arr["planner"]);
			$views = aw_unserialize($obj->prop("help_views"));
			foreach($views[$arr["view_no"]]["elements"] as $el_id => $element)
			{
				if($arr["element"] == $el_id)
				{
					$new[] = array(
						"name" => ($arr["type"] == TYPE_TEXT)?"text":"separator",
						"trans" => array(
							(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC")) => ($arr["type"] == TYPE_TEXT)?t("Tekst"):t("Eraldaja"),
						),
						"type" => $arr["type"],
					);
				}
				$new[] = $element;
			}
			if($arr["element"] < 0)
			{
				$new[] = array(
					"name" => ($arr["type"] == TYPE_TEXT)?"text":"separator",
					"trans" => array(
						(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC")) => ($arr["type"] == TYPE_TEXT)?t("Tekst"):t("Eraldaja"),
					),
					"type" => $arr["type"],
				);
			}

			$views[$arr["view_no"]]["elements"] = $new;

			$obj->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
			$obj->save();
		}
		return $arr["return_url"];
	}

	/**
		@attrib name=move_element_in_view params=name
		@param element required type=string
		@param view_no required type=int
		@param planner required type=int
		@param move_to required type=int
		@param return_url optional type=string
	**/
	function move_element_in_view($arr)
	{
		if($this->can("view", $arr["planner"]))
		{
			$obj = obj($arr["planner"]);
			$views = aw_unserialize($obj->prop("help_views"));
			$els = &$views[$arr["view_no"]]["elements"];

			foreach($els as $elem_id => $elem)
			{
				if($arr["move_to"] > 0 && $elem_id == $arr["move_to"] )
				{
					$new[] = $els[$arr["element"]];
				}
				if($elem_id != $arr["element"])
				{
					$new[] = $elem;
				}
			}
			if($arr["move_to"] < 0)
			{
				$new[] = $els[$arr["element"]];
			}
			$els = $new;
			$els = array_values($els);
			$obj->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
			$obj->save();
		}
		return $arr["return_url"];
	}

	/**
		@attrib name=add_view
	**/
	function add_view($arr)
	{
		if($this->can("view", $arr["id"]))
		{
			$obj = obj($arr["id"]);
			$views = aw_unserialize($obj->prop("help_views"));
			$view = &$views[]; //$views[]["elements"] = array();
			$view["elements"] = array();
			$view["trans"][(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"))] = t("Nimetu vaade");
			$obj->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
			$obj->save();
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=add_element_to_view params=name
		@param element required type=string
		@param view_no required type=int
		@param planner required type=int
		@param caption optional type=string
		@param return_url optional type=string
	**/
	function add_element_to_view($arr)
	{
		if($this->can("view", $arr["planner"]))
		{
			$obj = obj($arr["planner"]);
			$views = aw_unserialize($obj->prop("help_views"));
			$views[$arr["view_no"]]["elements"][] = array(
				"name" => $arr["element"],
				"type" => TYPE_ELEMENT,
				"trans" => array(
					(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC")) => $arr["caption"]?$arr["caption"]:t("Nimetu element"),
				),
			);
			$obj->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
			$obj->save();
		}
		return $arr["return_url"];
	}

	/**
		@attrib name=remove_element_from_view params=name
		@param element required type=int
		@param view_no required type=int
		@param planner required type=int
		@param return_url optional type=string
	**/
	function remove_element_form_view($arr)
	{
		if($this->can("view", $arr["planner"]))
		{
			$obj = obj($arr["planner"]);
			$views = aw_unserialize($obj->prop("help_views"));
			unset($views[$arr["view_no"]]["elements"][$arr["element"]]);
			$obj->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
			$obj->save();
		}
		return $arr["return_url"];
	}

	/**
		@attrib name=move_view params=name
		@param view_no required type=int
		@param move_to required type=int
		@param planner required type=int
		@param return_url optional type=string
	**/
	function move_view($arr)
	{
		if($this->can("view", $arr["planner"]))
		{
			$obj = obj($arr["planner"]);
			$views = aw_unserialize($obj->prop("help_views"));

			foreach($views as $vid => $v)
			{
				if($arr["move_to"] >= 0 && $arr["move_to"] == $vid)
				{
					$new[] = $views[$arr["view_no"]];
				}
				if($arr["view_no"] != $vid)
				{
					$new[] = $v;
				}
			}
			if($arr["move_to"] < 0)
			{
				$new[] = $views[$arr["view_no"]];
			}
			$obj->set_prop("help_views", aw_serialize($new, SERIALIZE_NATIVE));
			$obj->save();
		}
		return $arr["return_url"];
	}

	/**
		@attrib name=remove_view params=name
		@param view_no required type=int
		@param planner required type=int
		@param return_url optional type=string
	**/
	function remove_view($arr)
	{
		if($this->can("view", $arr["planner"]))
		{
			$obj = obj($arr["planner"]);
			$views = aw_unserialize($obj->prop("help_views"));
			unset($views[$arr["view_no"]]);
			$obj->set_prop("help_views", aw_serialize($views, SERIALIZE_NATIVE));
			$obj->save();
		}
		return $arr["return_url"];
	}

	function callback_pre_edit($arr)
	{
		// ugly hack for view element's controllers
		$cntr = array("show_controller", "save_controller");
		if(strlen($view = $arr["request"]["view_no"]) && strlen($element = $arr["request"]["element"]))
		{
			$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));
			foreach($cntr as $c)
			{
				$arr["obj_inst"]->set_prop($c, $views[$view]["elements"][$element][$c]);
			}
			$arr["obj_inst"]->save();
		}
		if($arr["request"]["group"] == "webform_detail" || $arr["request"]["group"] == "webform")
		{
			aw_global_set("output_charset", "UTF-8");
		}
	}

	function callback_pre_save($arr)
	{
		if(is_array($arr["request"]["loc"]) && count($arr["request"]["loc"]))
		{
			foreach($arr["request"]["loc"] as $loc =>$email)
			{
				if(!$this->can("view", $loc))
				{
					continue;
				}
				$loc = obj($loc);
				if(!$this->can("view", $loc->prop("email")))
				{
					continue;
				}
				$em = obj($loc->prop("email"));
				$em->set_prop("mail", $email);
				$em->set_name($email);
				$em->save();
			}
		}
	}


	function callback_mod_layout(&$arr)
	{
		if(!strlen($arr["request"]["element"]) && $arr["name"] == "controller")
		{
			return false;
		}
		elseif($arr["name"] == "controller")
		{
			$views = aw_unserialize($arr["obj_inst"]->prop("help_views"));
			$elname = $views[$arr["request"]["view_no"]]["elements"][$arr["request"]["element"]]["name"];
			$arr["area_caption"] =  sprintf("Elemendi '%s' konfiguratsioon", $elname);
		}

		if(!strlen($arr["request"]["view_no"]) && $arr["name"] == "trans")
		{
			return false;
		}
		return true;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		if(substr($GLOBALS["_GET"]["group"], 0, 7) == "webform")
		{
			if(strlen($GLOBALS["_GET"]["view_no"]))
			{
				$arr["view_no"] = $GLOBALS["_GET"]["view_no"];
			}
			if(strlen($GLOBALS["_GET"]["element"]))
			{
				$arr["element"] = $GLOBALS["_GET"]["element"];
			}
		}
	}
	function callback_mod_retval($arr)
	{
		if(substr($arr["request"]["group"], 0, 7) == "webform")
		{
			$arr["args"]["view_no"] = $arr["request"]["view_no"];
			$arr["args"]["element"] = $arr["request"]["element"];
		}
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function parse_alias($arr)
	{
		$arr["id"] = $arr["oid"];
		$arr["conference_planner"] = $arr["alias"]["to"];
		return $this->show($arr);
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	// HANDLE CHANGEFORM DATA


	/**
		@attrib params=pos
		@param type required type=string
			the element type to be converted (for example: table, event_type, select .. etc)
		@param value required type=string
			the element value to be converted
		@comment
			converts element values to correct form before storing them(for example serializes array's)
		@return
			returns the converted value
	**/
	function pre_store($type, $value)
	{
		switch($type)
		{
			case "table":
			case "event_type":
				$value = aw_serialize($value, SERIALIZE_NATIVE);
			break;
		}
		return $value;
	}

	/**
		@attrib params=pos
		@param type required type=string
			the element type to be converted (for example: table, event_type, select .. etc)
		@param value required type=string
			the element value to be converted
		@comment
			converts element values to correct form before editing them(for example unserializes array's)
		@return
			returns the converted value
	**/
	function pre_edit($type, $value)
	{
		switch($type)
		{
			case "table":
			case "event_type":
				$value = aw_unserialize($value);
			break;
		}
		return $value;
	}

	/**
		@attrib params=pos
		@param id required type=oid
			conference planning object id
		@param data required type=array
			form data to be saved.
			array(
				element_name => element_value
			)
		@comment
			stores the form session data,
	**/
	function store_data($id, $data, $preserve = true)
	{
		if(strlen($id))
		{
			$p_data = $this->get_stored_data($id);
			$data = $preserve?array_merge($p_data, $data):$data;
			aw_session_set("conference_planning_data_".$id, $data);
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
		@attrib params=pos
		@param id required type=oid
			conference planning object id
		@param convert optional type=bool
			if set to true, element values are processed with conference_planning::pre_edit() before returning
		@comment
			Get's the session data for given conference planning object.
	**/
	function get_stored_data($id, $convert = false)
	{

		$data =  is_array($_t = aw_global_get("conference_planning_data_".$id))?$_t:array();
		if($convert)
		{
			foreach($data as $elem => $value)
			{
				$_t = $this->get_form_elements_data($elem);
				$data[$elem] = $this->pre_edit($_t["form"], $value);
			}
		}
		return $data;
	}

	function get_errors($id)
	{
		return is_array($_t = aw_global_get("conference_planning_errors_".$id))?$_t:array();
	}

	function set_errors($id, $errors)
	{
		aw_session_set("conference_planning_errors_".$id, $errors);
	}

	/**
		@comment
			handles form data from conference_planning form (calls out corresponding controllers, saves data etc..)
	**/
	function handle_data($arr)
	{
		$cp = obj($arr["conference_planning"]);
		$this->cp = $cp;
		$views = aw_unserialize($cp->prop("help_views"));
		$i = get_instance(CL_CFGCONTROLLER);
		$view_id = --$arr["current_view"];
		$elements = $arr["elem"];
		unset($arr["elem"]);
		$stored_data = $this->get_stored_data($cp->id(), true);

		$view_errors = array();
		foreach($views[$view_id]["elements"] as $element_id => $data)
		{
			/*
				I need to check the show controller here, don't i? .. i mean, when it returns prop_ignore, then i have to ignore it here also, or else... ka-Boom, and things are messed up.
				For example, there are two same-type elements, both have show controller, which lets one of the elements be shon at once. so, when saving, i've got to ignore the one what isn't show on the web, or otherwise this is saved.. or smth..

				Worst part is, i have to give same parameters to controller that i give in the parse_form_element function..
				also, i can't pass them by reference, because they may alter the data, i we dont want that
				God this sucks ..
				Thing is, i really don't know if this thingie works 100% the way it should
			*/

			$el_form_data = $this->get_form_elements_data($data["name"]);
			$element = &$elements[$view_id][$element_id];
			if($this->can("view", $data["show_controller"]))
			{
				$i = get_instance(CL_CFGCONTROLLER);
				$toprop = array(
					"views" => $views,
					"values" => $stored_data,
					"element" => $data,
					"prop" => $el_form_data,
					"current_view" => $view_id,
					"current_element" => $element_id,
				);
				$show_ctr = $this->can("view", $data["show_controller"])?$i->check_property($data["show_controller"], $this->cp->id() ,$toprop, $GLOBALS["_GET"],"",""):array();
				if($show_ctr == PROP_IGNORE)
				{
					continue;
				}
			}

			$prop = array(
				"views" => &$views,
				"values" => &$elements,
				"value" => &$element,
				"current_view" => $view_id,
				"current_element" => $element_id,
				"pre_stored" => $stored_data,
				"element" => $data,
				"prop" => $el_form_data,
			);
			$controller = $this->can("view", $data["save_controller"])?$i->check_property($data["save_controller"], "", $prop, $arr, "" ,""):array();
			if($controller == PROP_IGNORE)
			{
				continue;
			}
			elseif($controller == PROP_ERROR)
			{
				$view_errors[] = $data["save_controller"];
			}
			$element = $this->pre_store($el_form_data["form"], $element);
			$to_be_saved[$views[$view_id]["elements"][$element_id]["name"]] = $element;
		}
		$errors = $this->get_errors($cp->id());
		$errors[$view_id] = $view_errors;
		$this->set_errors($cp->id(), $errors);
		return $this->store_data($cp->id(), $to_be_saved);
	}



	/**
		@attrib name=forward all_args=1 nologin=1
	**/
	function forward($arr)
	{
		$this->handle_data($arr);
		$obj = obj($arr["conference_planning"]);
		$viewcount = count(aw_unserialize($obj->prop("help_views")));
		$err = $this->get_errors($obj->id());
		if(count($err[($arr["current_view"] - 1)]) || ($arr["current_view"] + 1) > $viewcount)
		{
			$to_view = $arr["current_view"];
		}
		else
		{
			$to_view = ++$arr["current_view"];
		}
		return $this->gen_url($arr["doc"], $to_view);
	}

	/**
		@attrib name=stay all_args=1 nologin=1
	**/
	function stay($arr)
	{
		$this->handle_data($arr);
		return $this->gen_url($arr["doc"], $arr["current_view"]);
	}

	/**
		@attrib name=back all_args=1 nologin=1
	**/
	function back($arr)
	{
		$this->handle_data($arr);
		return $this->gen_url($arr["doc"], ((--$arr["current_view"] > 0)?$arr["current_view"]:++$arr["current_view"]));
	}

	/**
		@attrib name=finalize all_args=1 nologin=1
		@comment
			This method just catches the last requests and does all the close-up thingies. the real save is done by _finalize()
	**/
	function finalize($arr)
	{
		/*
			wheeh.. so, what do i need here?
			* defenetly i need to run all the created data (form data also) thru the submit_controller, to gain some extra features.
			* nothing more?
		*/
		$doc = $arr["doc"];
		$cp = $arr["conference_planning"];
		$ob = $this->can("view", $cp)?obj($cp):false;
		$views = aw_unserialize($ob->prop("help_views"));
		$data = $this->get_stored_data($cp);
		// lets clean up the data
		unset($data["separator"]);
		unset($data["text"]);
		foreach($data as $k => $v)
		{
			if(!strlen($v))
			{
				unset($data[$k]);
			}
		}
		if($this->can("view", ($ctr = $ob->prop("submit_controller"))))
		{
			$i = get_instance(CL_CFGCONTROLLER);
			$toprop = array(
				"views" => &$views,
				"values" => &$data,
			);
			$result = $this->can("view", $ctr)?$i->check_property($ctr, $ob->id(),$toprop, $GLOBALS["_GET"],"",""):array();
			if($result == PROP_IGNORE) // basically this should indicate that that submission is totally incorrect and goes to annulation
			{
				return aw_ini_get("baseurl");
			}
			elseif($result == PROP_ERROR) // this should indicate that something is wrong and can be modified(and submitted correctly after that)
			{
				return $arr["url"]; // this redirects back to the final page.. hmz.. some errors there would be nice in that case
			}
		}
		$parent = $this->can("view", $ob->prop("submission_dir"))?$ob->prop("submission_dir"):$ob->parent();
		$this->create_submit_object(array(
			"clid" => CL_RFP,
			"parent" => $parent,
			"name" => sprintf(t("RFP, %s"), date("d.m.Y H:i")),
			"data" => $data,
			"conference_planner" => $ob->id(),
		));
		$thank_you_so_very_much = $this->can("view", $ob->prop("redir_doc"))?"/".$ob->prop("redir_doc"):"";
		// take the trash out...
		$this->store_data($ob->id(), array(), false);
		return aw_ini_get("baseurl").$thank_you_so_very_much;
	}

	/**
		@param clid required type=int
		@param name required type=string
		@param parent required type=oid
		@param conference_planner required type=oid
		@param data required type=Array
			array(
				property_name => value
			)
		@comment
			creates the real object from the data, from the webform.. finally.
	**/
	function create_submit_object($arr)
	{
		if(is_array($arr["data"]) && $this->can("view", $arr["parent"]) && strlen($arr["name"]) && strlen($arr["name"]) && $this->can("view", $arr["conference_planner"]))
		{
			$obj = new object();
			$obj->set_name($arr["name"]);
			$obj->set_parent($arr["parent"]);
			$obj->set_class_id($arr["clid"]);
			$obj->set_prop("conference_planner", $arr["conference_planner"]);
			$obj->set_prop("from_planner", 1);
			// these are here just in case
			unset($data["conference_planner"]);
			unset($data["name"]);
			unset($data["parent"]);
			unset($data["clid"]);
			foreach($arr["data"] as $property => $value)
			{
				switch($property)
				{
					case "data_mf_attendees_no":
						$value = (int)$value;
						break;
					case "data_gen_open_for_alternative_dates":
					case "data_gen_accommodation_requirements":
						if($value == "on")
						{
							$value = 1;
						}
						break;
					case "data_gen_acc_end":
					case "data_gen_acc_start":
						$tmp = $value;
						$value = array();
						$value["date"] = $tmp;
					case "data_mf_catering_end":
					case "data_mf_catering_start":
					case "data_mf_end_date":
					case "data_mf_start_date":
					case "data_gen_departure_date":
					case "data_gen_arrival_date":
					case "data_gen_decision_date":
					case "data_gen_response_date":
						$day = explode(".", $value["date"]);
						$time = explode(":", $value["time"]);
						$stamp = mktime($time[0]?$time[0]:0, $time[1]?$time[1]:0, 0, $day[1], $day[0], $day[2]);
						$obj->set_prop($property."_admin", $stamp);
					break;
					case "data_mf_event_type":
						$tmp = explode("\"", $value);
						foreach($tmp as $t)
						{
							if(is_oid($t))
							{
								$o = obj($t);
								if($o->class_id() == CL_META)
								{
									$value = $t;
								}
							}
						}
					break;
				}
				$obj->set_prop($property, $value);
			}
			$obj->save();
			return $obj->id();
		}
		else
		{
			return false;
		}
	}

	function gen_url($oid, $view, $extra = array())
	{
		foreach($extra as $name => $val)
		{
			$ext[] = $name."=".$val;
		}
		$ext = join("&", $ext);
		$ext = strlen($ext)?"&".$ext:"";
		return aw_ini_get("baseurl")."/".$oid."?view_no=".$view.$ext;
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$cp = obj($arr["conference_planner"]);
		$this->cp = $cp;
		$active_view = $GLOBALS["_GET"]["view_no"];
		$active_view = (!strlen($active_view) || $active_view < 1) ? 1 : $active_view;

		$html["yah_bar"] = $this->parse_yah_bar($cp, $arr["id"], $active_view);
		$html["active_view"] = $this->parse_active_view($cp, $arr["id"], $active_view);

		//$html["movement"] = $this->parse_movement_buttons($cp->id(), $active_view);
		$html["errors"] = $this->error_html;
		$reforb_arr = array(
			"url" => get_ru(),
			"conference_planning" => $arr["conference_planner"],
			"current_view" => $active_view,
			"doc" => $arr["oid"],
		);
		$html["reforb"] = $this->mk_reforb("forward", array_merge($GLOBALS["_GET"], $reforb_arr));

		$this->read_template("webform.tpl");
		$this->vars($html);
		$html = $this->parse();
		return html::form(array(
			"action" => aw_ini_get("baseurl"),
			"method" => "POST",
			"content" => $html,
			"name" => "changeform",
		));
	}

	function parse_movement_buttons($cp, $view)
	{
		$this->read_template("move.tpl");
		$obj = obj($cp);
		$viewcount = count(aw_unserialize($obj->prop("help_views")));
		//$html .= ($view > 1 && $view < $viewcount)?$this->parse("SEPARATOR"):"";
		$this->vars(array(
			"BACK" => ($view > 1)?$this->parse("BACK"):"",
			"FORWARD" => ($view < $viewcount)?$this->parse("FORWARD"):"",
			"SUBMIT" => ($view == $viewcount)?$this->parse("SUBMIT"):"",
		));
		return $this->parse();
	}

	function parse_form_element(&$el, $view_no, $element, &$views, &$value, &$values, $doc)
	{
		$this->_init_vars();
		lc_site_load("conference_planning_new", $this);
		$prop = $this->get_form_elements_data($el["name"]);
		if(!$prop)
		{
			return "";
		}
		$lang = (aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"));

		// get options
		if($this->can("view", ($ch = $el["choices"])))
		{
			$list = new object_list(array(
				"class_id" => CL_META,
				"parent" => $ch,
				"sort_by" => "objects.jrk ASC",
			));
			foreach($list->arr() as $obj)
			{
				$trans = $obj->meta("tolge");
				//$el["options"][(strlen(trim($obj->prop("comment")))?$obj->prop("comment"):$obj->id())] = $obj->name();
				// oh my god this metamgr trans thingi is stupidly done
				$el["options"][(strlen(trim($obj->prop("comment")))?$obj->prop("comment"):$obj->id())] = $obj->trans_get_val("name");

			}
		}
		// get controller contents
		$i = get_instance(CL_CFGCONTROLLER);
		$toprop = array(
			"views" => &$views,
			"values" => &$values,
			"value" => &$value,
			"element" => &$el,
			"prop" => &$prop,
			"current_view" => $view_no,
			"current_element" => $element,
		);//if($el["show_controller"] == 11440)arr($toprop);
		$controller = $this->can("view", $el["show_controller"])?$i->check_property($el["show_controller"], $this->cp->id(),$toprop, $GLOBALS["_GET"],"",""):array();

		if($controller == PROP_IGNORE)
		{
			return "";
		}
		if(is_array($controller))
		{
			$el = array_merge($el, $controller);
		}
		// this here is for strange cases where data needs to be temporarily altered(in controller) to show on web, but can't be stored that way
		// so, inside the switch statement use $value_to_use instead of $value
		if(!empty($el["value"]))
		{
			$value_to_use = $el["value"];
		}
		else
		{
			$value_to_use = &$value;
		}
		// here we set the caption to angliski if the current lang translation is unset
		$caption = $el["trans"][$lang]?$el["trans"][$lang]:$el["trans"][CP_DEFAULT_LANG];
		switch($prop["form"])
		{
			case "separator":
			case "textarea":
			case "textbox":
				$this->vars(array(
					//"caption" => $el["trans"][$lang]?$el["trans"][$lang]:$prop["caption"],
					"caption" => $caption,
					"value" => $value_to_use,
				));
				break;
			case "checkbox":
				$this->vars(array(
					//"caption" => $el["trans"][$lang]?$el["trans"][$lang]:$prop["caption"],
					"caption" => $caption,
					"checked" => checked($value_to_use),
				));
				break;
			case "date_textbox":
				$this->vars(array(
					//"caption" => $el["trans"][$lang]?$el["trans"][$lang]:$prop["caption"],
					"caption" => $caption,
					"value" => $value_to_use,
					$prop["form"]."_id" => $el["name"]."_id",
					$prop["form"]."_link" => $el["name"]."_link",
					"calendar_icon_url" => aw_global_get("baseurl")."/automatweb/images/ico_calendar.gif",
				));
				break;
			case "datetime_textboxes":
				$this->vars(array(
					//"caption" => $el["trans"][$lang]?$el["trans"][$lang]:$prop["caption"],
					"caption" => $caption,
					$prop["form"]."_id" => $el["name"]."_id",
					$prop["form"]."_link" => $el["name"]."_link",
					"calendar_icon_url" => aw_global_get("baseurl")."/automatweb/images/ico_calendar.gif",
					"date_value" => $value_to_use["date"],
					"time_value" => $value_to_use["time"],
				));
				break;
			case "select":
				foreach($el["options"] as $key => $option)
				{
					$this->vars(array(
						"value" => $key,
						"caption" => $option,
						"selected" => ($key == $value_to_use)?selected(true):"",
					));
					$opts .= $this->parse("OPTION");
				}
				$this->vars(array(
					"OPTION" => $opts,
					"caption" => $caption,
					//"caption" => $el["trans"][$lang]?$el["trans"][$lang]:$prop["caption"],
				));
				break;
			case "event_type":
				foreach($el["options"] as $key => $option)
				{
					$this->vars(array(
						"value" => $key,
						"selected" => ($key == $value_to_use["select"])?selected(true):"",
						"caption" => $option,

					));
					$opts .= $this->parse("EVENT_TYPE_OPTION");
				}
				$this->vars(array(
					"text" => $value_to_use["text"],
					"radio_".$value_to_use["radio"] => checked(true),
					"EVENT_TYPE_OPTION" => $opts,
					"caption" => $caption,
					//"caption" => $el["trans"][$lang]?$el["trans"][$lang]:$prop["caption"],
				));
				break;
			case "text":
				if($el["no_caption"] == true)
				{
					$prop["form"] = "TEXT_NO_CAPTION";
				}

				$this->vars(array(
					"caption" => $caption,
					//"caption" => $el["trans"][$lang],
					"value" => $value_to_use,//$el["value"],
				));
				break;
			case "table":
				foreach($prop["fields"] as $field => $caption)
				{
					$this->vars(array(
						"caption" => $caption,
					));
					$header .= $this->parse("HEADER_COL");
				}
				if($prop["edit_link"] || $prop["remove_link"])
				{
					$this->vars(array(
						"caption" => "&nbsp;",
					));
					$header .= $this->parse("HEADER_COL");
				}

				$this->vars(array(
					"HEADER_COL" => $header,
				));

				$header = $this->parse("HEADER");
				foreach($value_to_use as $rowid => $row)
				{
					// edit remove links
					unset($links_caption);
					if($prop["edit_link"])
					{
						$links_caption[] = html::href(array(
							"caption" => t("Muuda"),
							"url" => $this->gen_url($doc, ($view_no + 1), array(
								"edit" => $rowid,
								"prop" => $el["name"],
							)),
						));
					}
					if($prop["remove_link"])
					{
						$links_caption[] = html::href(array(
							"caption" => t("Kustuta"),
							"url" => $this->gen_url($doc, ($view_no +1), array(
								"remove" => $rowid,
								"prop" => $el["name"],
							)),
						));
					}


					unset($cols);
					foreach($prop["fields"] as $field => $caption)
					{
						$this->vars(array(
							"caption" => $row[$field],
						));
						$cols .= $this->parse("ROW_COL");
					}
					if($prop["edit_link"] || $prop["remove_link"])
					{
						$this->vars(array(
							"caption" => join("&nbsp;", $links_caption),
						));
						$cols .= $this->parse("ROW_COL");
					}
					$this->vars(array(
						"ROW_COL" => $cols,
					));
					$rows .= $this->parse("ROW");
				}
				$this->vars(array(
					"HEADER" => $header,
					"ROW" => $rows
				));
				break;
		}

		$value = $this->pre_store($prop["form"], $value);
		if($el["store_data"] === true)
		{
			$this->store_data = true;
		}
		$this->vars($el["add_vars"]);
		$this->vars(array(
			"wid" => $el["wid"],
			"wid_out" => $el["wid"]."_out",
			//"onChange" => $el["onChange"],
			//"onClick" => $el["onClick"],
			//"el_name" => $el["name"],
			"view_no" => $view_no,
			"element" => $element,
			"pre_element_append" => $el["pre_element_append"],
			"post_element_append" => $el["post_element_append"],
		));
		$ret = $el["pre_append"].$this->parse(strtoupper($prop["form"])).$el["post_append"];
		return $ret;
	}

	function parse_active_view($cp, $doc, $act)
	{
		$this->read_template("elements.tpl");

		$views = aw_unserialize($cp->prop("help_views"));
		$keys = array_keys($views);
		$act = $keys[($act-1)];
		$view = &$views[$act];

		$stored_data = $this->get_stored_data($cp->id(), true);

		$ret = "<table class=\"form\">";
		$this->store_data = false;
		$errors = $this->get_errors($cp->id());

		foreach($view["elements"] as $elem_id => $el)
		{
			$ret .= $this->parse_form_element($view["elements"][$elem_id], $act, $elem_id, $views, $stored_data[$view["elements"][$elem_id]["wid"]], $stored_data, $doc);
		}
		// let's parse errors here as well
		foreach($errors[$act] as $ctr)
		{
			$obj = obj($ctr);
			$this->vars(array(
				"caption" => $obj->trans_get_val("errmsg"),
			));
			$this->error_html .= $this->parse("ERROR");
		}

		if($this->store_data)
		{
			$this->store_data($cp->id(), $stored_data);
			$this->store_data = false;
		}
		$ret .= "</table>";
		return $ret;
	}

	function parse_yah_bar($cp, $doc, $no)
	{
		$this->read_template("views.tpl");

		$views = aw_unserialize($cp->prop("help_views"));
		$fid = reset(array_keys($views));	// Esimese vaate indeks vaadete massiivis
		$lid = end(array_keys($views));		// Viimase vaate indeks vaadete massiivis
		$keys = array_keys($views);			// Vaadete massiivi indeksid
		// $no-1 sellep2rast, et vaated on 0 kuni n-1, mitte 1 kuni n.
		$no_i = array_search($no-1, $keys);	// K2esoleva vaate indeks "vaadete massiivi indeksite" ($keys) massiivis

		foreach($keys as $i => $key)
		{
			$lang_id = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC");

			// $no-1 sellep2rast, et vaated on 0 kuni n-1, mitte 1 kuni n.
			$act = ($i == $no-1) ? "ACT_" : "";
			// $no-1 sellep2rast, et vaated on 0 kuni n-1, mitte 1 kuni n.
			$href = (strlen($no) &&  $i < $no-1) ? "_HREF" : "";
			$caption = $views[$key]["trans"][$lang_id] ? $views[$key]["trans"][$lang_id] : $views[$key]["trans"][CP_DEFAULT_LANG];
			$caption_i_plus_one = $views[$keys[$i+1]]["trans"][$lang_id] ? $views[$keys[$i+1]]["trans"][$lang_id] : $views[$keys[$i+1]]["trans"][CP_DEFAULT_LANG];
			if($i == 0)
			{
				$this->vars(array(
					"step_nr" => $i+1,
					"caption" => $caption,
					"url" => aw_ini_get("baseurl")."/".$doc."?view_no=".$i,
				));
				$yah[] = $this->parse($act."YAH_FIRST_BTN".$href);
			}
			elseif($i <= count($views) && $no_i != ($i-1))
			{
				unset($last);
				if($i == count($views))
				{
					$last = "_LAST";
				}
				$this->vars(array(
					"step_nr" => $i+1,
					"caption" => $caption,
					"url" => aw_ini_get("baseurl")."/".$doc."?view_no=".$i,
				));
				$yah[] = $this->parse($act."YAH".$last."_BTN".$href);
			}
			elseif($no_i == (count($views) - 1))
			{
				$this->vars(array(
					"step_nr" => $i+1,
					"caption" => $caption,
				));
				$yah[] = $this->parse("YAH_LAST_BTN_AFTER");
			}
			elseif($no_i != ($i - 1))
			{
				$this->vars(array(
					"step_nr" => $i+1,
					"caption" => $caption,
				));
				$yah[] = $this->parse($act."YAH_LAST_BTN".$href);
			}
			if(strlen($act) && $no_i != (count($views) - 1) && $i < count($views))
			{
				$this->vars(array(
					"step_nr" => ($i+2),
					"caption" => $caption_i_plus_one,
				));
				$yah[] = $this->parse("YAH_BTN_AFTER");
			}
		}
		$this->vars(array(
			"YAH_BAR" => join("", $yah),
		));
		return $this->parse();
	}

	function __show($arr)
	{
		$_GET = $GLOBALS["_GET"];
		if($_GET["action"])
		{
			if($ret = $this->do_actions($arr, $_GET["action"]))
			{
				header("Location:".$ret);
			}
		}
		$c_obj = obj($arr["conference_planner"]);
		$ob = new object($arr["id"]);
		$this->read_template("conference_planning.tpl");

		$sc = new aw_template();
		$sc->init(array(
			"tpldir" => "applications/conference_planning_webview",
			"clid" => CL_CONFERENCE_PLANNING
		));
		$sc->lc_load("conference_planning", "lc_conference_planning");
		$sd = $this->get_form_data();
		$no = $arr["sub"]?$arr["sub"]:$_GET["sub"];
		$sd = (count($arr["data"]) && is_Array($arr["data"]))?$arr["data"]:$this->get_form_data();
		switch($no)
		{
			case 1:
				$sc->read_template("sub_conference_rfp1.tpl");
				$acc_req = ($sd["single_count"] > 0 || $sd["double_count"] > 0 || $sd["suite_count"] > 0 || $sd["needs_rooms"])?"CHECKED":"";
				$u = get_instance(CL_USER);
				$org = $u->get_current_company();
				$org = $this->can("view", $org)?obj($org):false;
				$sc->vars(array(
					"function_name" => $sd["function_name"],
					"organisation_company" => strlen($_t = $sd["organisation_company"])?$_t:($org?$org->name():""),
					"multi_day_".(strlen($sd["multi_day"])?$sd["multi_day"]:1) => "CHECKED",
					"response_date" => $sd["dates"][0]["response_date"],
					"decision_date" => $sd["dates"][0]["decision_date"],
					"arrival_date" => $sd["dates"][0]["arrival_date"],
					"departure_date" => $sd["dates"][0]["departure_date"],
					"open_for_alternative_dates" => ($sd["open_for_alternative_dates"])?"CHECKED":"",
					"needs_rooms" => $acc_req,
				));

				break;
			case 2:
				$sc->read_template("sub_conference_rfp2.tpl");
				// tablerows
				foreach($sd["dates"] as $row_no => $date)
				{
					$sc->vars(array(
						"date_no" => $row_no,
						"date_type_".(($date["type"] == 0)?"normal":"alternative") => "SELECTED",
						"arrival_date" => $date["arrival_date"],
						"departure_date" => $date["departure_date"],
						"remove_url" => aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$no."&action=remove&id=".$row_no,
					));
					$tablerows .= $sc->parse("ROW");
				}
				// days from
				$sc->vars(array(
					"value" => -1,
					"caption" => t("-"),
				));
				$dayf = $sc->parse("DAY");
				for($i = 0; $i<7 ;$i++)
				{
					$sc->vars(array(
						"value" => $i,
						"caption" => $this->wd[$i],
						"pattern_wday_from" => ($sd["pattern_wday_from"] == $i)?"SELECTED":"",
					));
					$dayf .= $sc->parse("DAY_FROM");
				}
				// days to
				$sc->vars(array(
					"value" => -1,
					"caption" => t("-"),
				));
				$dayt = $sc->parse("DAY");
				for($i = 0; $i<7 ;$i++)
				{
					$sc->vars(array(
						"value" => $i,
						"caption" => $this->wd[$i],
						"pattern_wday_to" => ($sd["pattern_wday_to"] == $i)?"SELECTED":"",
					));
					$dayt .= $sc->parse("DAY_TO");
				}
				for($i = 1; $i <= $c_obj->prop("meeting_pattern_max_days");$i++)
				{
					$sc->vars(array(
						"value" => $i,
						"caption" => $i,
						"pattern_day" => ($sd["pattern_day"] == $i)?"SELECTED":"",
					));
					$day .= $sc->parse("DAY");
				}
				$sc->vars(array(
					"dates_are_flexible" => $sd["dates_are_flexible"]?"CHECKED":"",
					"date_comments" => $sd["date_comments"],
					"pattern_".$sd["meeting_pattern"] => "CHECKED",
					"ROW" => $tablerows,
					"DAY_FROM" => $dayf,
					"DAY_TO" => $dayt,
					"DAY" => $day,
				));
				break;
			case 3:
				$sc->read_template("sub_conference_rfp3.tpl");
				foreach(array("single", "double", "suite") as $loop_item)
				{
					for($i = 0; $i <= $c_obj->prop($loop_item."_count"); $i++)
					{
						$sc->vars(array(
							"value" => $i,
							"caption" => $i,
							$loop_item => ($sd[$loop_item."_count"] == $i)?"SELECTED":"",
						));
						$room_options[strtoupper($loop_item)."_OPTION"] .= $sc->parse(strtoupper($loop_item)."_OPTION");
					}
				}
				$sc->vars($room_options);
				$sc->vars(array(
					"needs_rooms" => $sd["needs_rooms"]?"CHECKED":"",
					"main_arrival_date" => $sd["dates"][0]["arrival_date"],
					"main_departure_date" => $sd["dates"][0]["departure_date"],
					"required" => $sd["needs_rooms"]?t("*"):NULL,
				));
				break;
			case 4:
				$sc->read_template("sub_conference_rfp4.tpl");
				$c_inst = get_instance(CL_CONFERENCE);
				$conference_types = $c_inst->conference_types();
				$_GET["act_evt_no"] = ($sd["multi_day"] == "2")?$_GET["act_evt_no"]:"0";
				// setting active main event and catering id
				if(strlen($_GET["act_evt_no"]))
				{
					$mf = $sd["main_function"][$_GET["act_evt_no"]];
					if(strlen($_GET["act_cat_no"]))
					{
						$act_cat = $mf["main_catering"][$_GET["act_cat_no"]];
					}
				}
				// conference day's table .. when it is needed
				if(($sd["multi_day"] == 2) && is_array($sd["main_function"]) && count($sd["main_function"]))
				{
					unset($days);
					foreach($sd["main_function"] as $id => $data)
					{
						$active_day = !strcmp($id, $_GET["act_evt_no"])?"_ACTIVE":"";
						$sc->vars(array(
							"event_type" => ($data["event_type_chooser"] == 1)?$conference_types[$data["event_type_select"]]:$data["event_type_text"],
							"persons_no" => $data["persons_no"],
							//"delegates_no" => $data["delegates_no"],
							"door_sign" => $data["door_sign"],
							"function_start_date" => $data["function_start_date"],
							"function_start_time" => $data["function_start_time"],
							"function_end_time" => $data["function_end_time"],
							"24h" => $data["24h"]?t("Jah"):t("Ei"),
							"table_form" => $this->table_forms[$data["table_form"]],
							"remove_url" => aw_ini_get("baseurl")."/".$ob->id()."?sub=".$no."&action=remove&evt=".$id,
							"edit_url" => aw_ini_get("baseurl")."/".$ob->id()."?sub=".$no."&act_evt_no=".$id,
						));
						$days .= $sc->parse("DAY".$active_day);
					}
					$sc->vars(array(
						"DAY" => $days,
					));
					$days_table = $sc->parse("DAYS");
				}


				// set different kind of stupid vars

				foreach($conference_types as $k => $capt)
				{
					$sc->vars(array(
						"value" => $k,
						"caption" => $capt,
						"event_type_select" => ($k == $mf["event_type_select"])?selected(true):"",
					));
					$evt_type .= $sc->parse("EVT_TYPE");
				}
				foreach($this->table_forms as $k => $capt)
				{
					$sc->vars(array(
						"value" => $k,
						"caption" => $capt,
						"table_form" => ($k == $mf["table_form"])?selected(true):"",
					));
					$tab_forms .= $sc->parse("TABLE_FORM");
				}
				foreach($this->tech_equip as $k => $capt)
				{
					$sc->vars(array(
						"value" => $k,
						"caption" => $capt,
						"tech" => (in_array($k, array_keys($mf["tech"])))?checked(true):"",
					));
					$tech .= $sc->parse("TECH_EQUIP");
				}


				// catering crap
				$edit = false;
				$tmp = $mf["main_catering"];
				krsort($tmp);
				$catering_no = key($tmp) + 1;
				if($_GET["action"] == "edit" && strlen($_GET["id"]))
				{
					$catering_no = $_GET["id"];
					$edit = true;
				}
				foreach($mf["main_catering"] as $cat_no => $cat_data)
				{
					$active_cat = !strcmp($cat_no, $_GET["act_cat_no"])?"_ACTIVE":"";
					$sc->vars(array(
						"catering_row_type" => ($cat_data["catering_type_chooser"] == 1)?$this->catering_types[$cat_data["catering_type_select"]]:$cat_data["catering_type_text"],
						"catering_row_start_time" => $cat_data["catering_start_time"],
						"catering_row_end_time" => $cat_data["catering_end_time"],
						"remove_url" => aw_ini_get("baseurl")."/".$ob->id()."?sub=".$no."&action=remove&evt=".$_GET["act_evt_no"]."&cat=".$cat_no,
						"edit_url" => aw_ini_get("baseurl")."/".$ob->id()."?sub=".$no."&act_evt_no=".$_GET["act_evt_no"]."&act_cat_no=".$cat_no,
						"catering_row_attendees_no" => $cat_data["catering_attendees_no"],
					));
					$cat_rows .= $sc->parse("MAIN_CATERING_ROW".$active_cat);
				}

				// catering types for dropdown
				foreach($this->catering_types as $k => $capt)
				{
					$sc->vars(array(
						"value" => $k,
						"caption" => $capt,
						"catering_type_select" => ($k == $act_cat["catering_type_select"])?selected(true):"",
					));
					$catering_types .= $sc->parse("CATERING_TYPE");
				}
				if($act_cat)
				{
					$sc->vars(array(
						"catering_id" => $_GET["id"],
						"catering_type_chooser_".$act_cat["catering_type_chooser"] => checked(true),
						"catering_type_text" => $act_cat["catering_type_text"],
						"catering_start_time" => $act_cat["catering_start_time"],
						"catering_end_time" => $act_cat["catering_end_time"],
						"catering_attendees_no" => $act_cat["catering_attendees_no"],
					));
				}

				if(count($sd["main_function"]))
				{
					$e = end($sd["main_function"]);
					$spl = split("[.]", $e["function_start_date"]);
					$date = date("d.m.Y", mktime(0,0,0,$spl[1], ($spl[0]+1), $spl[2]));
				}
				else
				{
					$date = $sd["dates"][0]["arrival_date"];
				}

				$sc->vars(array(
					"catering_attendees_no" => ($_t = $mf["main_catering"][$_GET["id"]]["catering_attendees_no"])?$_t:$mf["persons_no"],
					"EVT_TYPE" => $evt_type,
					"TABLE_FORM" => $tab_forms,
					"TECH_EQUIP" => $tech,
					"CATERING_TYPE" => $catering_types,
					"MAIN_CATERING_ROW" => $cat_rows,
					"event_type_text" => $mf["event_type_text"],
					"event_type_chooser_".(($mf["event_type_chooser"])?$mf["event_type_chooser"]:1) => checked(true),
					"delegates_no" => $mf["delegates_no"],
					"door_sign" => $mf["door_sign"],
					"persons_no" => $mf["persons_no"],
					"function_start_date" => $mf["function_start_date"]?$mf["function_start_date"]:$date,
					"function_start_time" => $mf["function_start_time"],
					"function_end_time" => $mf["function_end_time"],
					"24h" => $mf["24h"]?checked(true):"",
					"catering_no" => $catering_no,
					"DAYS" => $days_table,
					"ADD_DAY" => ($sd["multi_day"] == 2)?$sc->parse("ADD_DAY"):"",
				));
				break;
			case 5:
				$sc->read_template("sub_conference_rfp5.tpl");
				$c_inst = get_instance(CL_CONFERENCE);
				$c_types = $c_inst->additional_conference_types();
				$values = array();
				if(strlen($_GET["act_evt_no"]))
				{
					$values = $sd["additional_functions"][$_GET["act_evt_no"]];
					$cat_values = $values["catering"];
					if(strlen($_GET["act_cat_no"]))
					{
						$act_cat = $cat_values[$_GET["act_cat_no"]];
					}
				}

				foreach($c_types as $k => $capt)
				{
					$sc->vars(array(
						"value" => $k,
						"caption" => $capt,
						"event_type_select" => ($k == $values["event_type_select"])?"SELECTED":"",
					));
					$evt_type .= $sc->parse("EVT_TYPE");
				}
				foreach($this->table_forms as $k => $capt)
				{
					$sc->vars(array(
						"value" => $k,
						"caption" => $capt,
						"table_form" => ($k == $values["table_form"])?"SELECTED":"",
					));
					$tform .= $sc->parse("TABLE_FORM");
				}
				foreach($this->tech_equip as $k => $capt)
				{
					$sc->vars(array(
						"value" => $k,
						"caption" => $capt,
						"tech" => (in_array($k, array_keys($values["tech"])))?"CHECKED":"",
					));
					$tech .= $sc->parse("TECH_EQUIP");
				}
				foreach($this->catering_types as $k => $capt)
				{
					$sc->vars(array(
						"value" => $k,
						"caption" => $capt,
						"catering_type_select" => ($k == $act_cat["catering_type_select"])?"SELECTED":"",
					));
					$catering_form_select .= $sc->parse("CATERING_TYPE");
				}
				// table rows
				//$d = $this->get_form_data();
				foreach($sd["additional_functions"] as $id => $data)
				{
					$row_active = ($id == $_GET["act_evt_no"])?"_ACTIVE":"";
					$sc->vars(array(
						"caption" => ($data["event_type_chooser"] == 1)?$c_types[$data["event_type_select"]]:$data["event_type_text"],
						"remove_url" => aw_ini_get("baseurl")."/".$ob->id()."?sub=".$no."&action=remove&evt=".$id,
						"edit_url" => aw_ini_get("baseurl")."/".$ob->id()."?sub=".$no."&act_evt_no=".$id,
					));
					$rows .= $sc->parse("ROW".$row_active);
				}

				// catering tab
				foreach($cat_values as $cat_no => $catering)
				{
					$cat_active = ($cat_no == $_GET["act_cat_no"])?"_ACTIVE":"";
					$sc->vars(array(
						"cat_type" => ($catering["catering_type_chooser"] == 2)?$catering["catering_type_text"]:$this->catering_types[$catering["catering_type_select"]],
						"cat_starttime" => $catering["catering_start_time"],
						"cat_endtime" => $catering["catering_end_time"],
						"cat_attendee_no" =>$catering["catering_attendee_no"],
						"cat_remove_url" => aw_ini_get("baseurl")."/".$ob->id()."?sub=".$no."&action=remove&evt=".$_REQUEST["act_evt_no"]."&cat=".$cat_no,
						"cat_edit_url" => aw_ini_get("baseurl")."/".$ob->id()."?sub=".$no."&act_evt_no=".$_REQUEST["act_evt_no"]."&act_cat_no=".$cat_no,
					));
					$cat_rows .= $sc->parse("CATERING_ROW".$cat_active);
				}
				$sc->vars(array(
					"CATERING_ROW" => $cat_rows,
				));
				$additional_catering = $sc->parse("ADDITIONAL_CATERING");
				$sc->vars(array(
					"TECH_EQUIP" => $tech,
					"TABLE_FORM" => $tform,
					"CATERING_TYPE" => $catering_form_select,
					"ADDITIONAL_CATERING" => $additional_catering,
					"EVT_TYPE" => $evt_type,
					"ROW" => $rows,
					"event_type_chooser_".$values["event_type_chooser"] => "CHECKED",
					"event_type_text" => $values["event_type_text"],
					"delegates_no" => $values["delegates_no"],
					"persons_no" => $values["persons_no"],
					"door_sign" => $values["door_sign"],
					"function_start_date" => $values["function_start_date"],
					"function_start_time" => $values["function_start_time"],
					"function_end_time" => $values["function_end_time"],
					"24h" => $values["24h"]?"CHECKED":"",
					"catering_type_chooser_".$act_cat["catering_type_chooser"] => "CHECKED",
					"catering_type_text" => $act_cat["catering_type_text"],
					"catering_start_time" => $act_cat["catering_start_time"],
					"catering_end_time" => $act_cat["catering_end_time"],
					"catering_attendee_no" => $act_cat["catering_attendee_no"],
					"ADD_CATERING" => strlen($_GET["act_evt_no"])?$sc->parse("ADD_CATERING"):"",
				));
				break;
			case 6:
				$sc->read_template("sub_conference_rfp6.tpl");
				$addr = get_instance(CL_CRM_ADDRESS);

				$ui = get_instance(CL_USER);
				$logged_in_user = $ui->get_current_person();
				if($this->can("view", $logged_in_user))
				{
					$uo = obj($logged_in_user);
					$c_name = $uo->prop("address.riik.name");
				}
				foreach($addr->get_country_list() as $k => $v)
				{
					$sc->vars(array(
						"value" => $k,
						"caption" => $v,
						"billing_country" => ($k == $sd["billing_country"] || (!strlen($sd["billing_country"]) && stristr($v, $c_name)))?"SELECTED":"",
					));
					$ctr .= $sc->parse("COUNTRY");
				}
				// search !!!
				$altern_dates[] = array(
					"start" => $this->_gen_to_timestamp($sd["function_start_date"], $sd["function_start_time"]),
					"end" => $this->_gen_to_timestamp($sd["function_end_date"], $sd["function_end_time"]),
					"persons" => $sd["persons_no"],
				);
				foreach($sd["additional_functions"] as $fun)
				{
					$altern_dates[] = array(
						"start" => $this->_gen_to_timestamp($fun["function_start_date"], $fun["function_start_time"]),
						"end" => $this->_gen_to_timestamp($fun["function_end_date"], $fun["function_end_time"]),
						"persons" => $fun["persons_no"],
					);
				}
				$res = $this->all_mighty_search_engine(array(
					"single_rooms" => ($sd["needs_rooms"])?$sd["single_count"]:false,
					"double_rooms" => ($sd["neeeds_rooms"])?$sd["double_count"]:false,
					"suites" => ($sd["needs_rooms"])?$sd["suite_count"]:false,
					"attendees_count" => $sd["attendees_no"],
					"dates" => $altern_dates,
					"oid" => $c_obj->id(),
				));
				$loc_inst = get_instance(CL_LOCATION);
				$img_inst = get_instance(CL_IMAGE);
				$overvr = array();
				foreach($res as $loc_id => $data)
				{
					$loc = obj($loc_id);
					if($email = $loc->prop_str("email"))
					{
						$sc->vars(array(
							"email"  => $email,
						));
						$email = $sc->parse("RES_EMAIL");
					}
					$imgs = $loc_inst->get_images($loc_id);
					$sc->vars($overvr);
					foreach($imgs as $img)
					{
						$sc->vars(array(
							"IMG_".$img->ord() => html::img(array(
								"url" => $img_inst->get_url_by_id($img->id()),
							)),
						));
						$overvr = array(
							"IMG_".$img->ord() => "",
						);
					}
					$inf = $loc_inst->get_add_info($loc_id);
					$photo = $loc->prop("photo");
					$map = $loc->prop("map");
					$img_inst = get_instance(CL_IMAGE);
					$sc->vars(array(
						"caption" => $loc->name(),
						"address" => $loc->prop_str("address"),
						"single_count" => $loc->prop("single_count"),
						"double_count" => $loc->prop("double_count"),
						"suite_count" => $loc->prop("suite_count"),
						"phone" => ($ph = $loc->prop_str("phone"))?$ph:t("-"),
						"fax" => ($f = $loc->prop_str("fax"))?$f:t("-"),
						"RES_EMAIL" => $email,
						"photo_uri" => $img_inst->get_url_by_id($photo),
						"map_uri" => $img_inst->get_url_by_id($map),
						"value" => $loc_id,
						"selected" => in_array($loc_id, $sd["selected_search_result"])?"CHECKED":"",
						"urgent" => $sd["urgent"]?"CHECKED":"",
						"info" => $inf,
					));
					$s_results .= $sc->parse("SEARCH_RESULT");
					$hid_rows .= html::hidden(array(
						"name" => "sub[6][all_search][".$loc_id."]",
						"value" => 1,
					));
					foreach($data["errors"] as $err)
					{
						$sc->vars(array(
							"caption" => $err,
						));
						$s_results .= $sc->parse("SEARCH_RESULT_ERROR");
					}
				}
				$u = get_instance(CL_USER);
				$org = $u->get_current_company();
				$per = obj($u->get_current_person());
				$addr = $per->prop("address");
				$addr = $this->can("view", $addr)?obj($addr):false;
				$def_ph = $per->prop("phone");
				$def_ph = $this->can("view", $def_ph)?obj($def_ph):false;
				$def_em = $per->prop("email");
				$def_em = $this->can("view", $def_em)?obj($def_em):false;

				$sc->vars(array(
					"MISSING_ERROR" => $m_err,
					"COUNTRY" => $ctr,
					"SEARCH_RESULT" => $s_results,
					"billing_company" => $sd["billing_company"]?$sd["billing_company"]:call_user_func(array(obj($org), "name")),
					"billing_contact" => $sd["billing_contact"]?$sd["billing_contact"]:$per->name(),
					"billing_street" => $sd["billing_street"]?$sd["billing_street"]:($addr?$addr->prop("aadress"):""),
					"billing_city" => $sd["billing_city"]?$sd["billing_city"]:($addr?$addr->prop_str("linn"):""),
					"billing_zip" => $sd["billing_zip"]?$sd["billing_zip"]:($addr?$addr->prop("postiindeks"):""),
					"billing_name" => $sd["billing_name"]?$sd["billing_name"]:$per->name(),
					"billing_phone_number" => $sd["billing_phone_number"]?$sd["billing_phone_number"]:($def_ph?$def_ph->name():""),
					"billing_email" => $sd["billing_email"]?$sd["billing_email"]:($def_em?$def_em->name():""),
					"all_search_results" => $hid_rows,
				));
				break;
			case "7":
				$sc->read_template("sub_conference_rfp7.tpl");
				// #0
				$tmp = $this->can("view", $sd["country"])?obj($sd["country"]):false;
				$sc->vars(array(
					"country" => $tmp?$tmp->trans_get_val("name"):"",//call_user_func(array(obj($sd["country"]), "name")),
				));
				$sc->vars(array(
					"COUNTRY" => $tmp?$this->parse("COUNTRY"):"",
				));
				// #1
				$sc->vars(array(
					"function_name" => $sd["function_name"],
					"organisation_company" => $sd["organisation_company"],
					"response_date" => $sd["dates"][0]["response_date"],
					"decision_date" => $sd["dates"][0]["decision_date"],
					"arrival_date" => $sd["dates"][0]["arrival_date"],
					"departure_date" => $sd["dates"][0]["departure_date"],
					"open_for_alternative_dates" => ($sd["open_for_alternative_dates"])?t("Yes"):t("No"),
					"multi_day" => ($sd["multi_day"] == 2)?t("Yes"):t("No"),
					"needs_rooms" => ($sd["needs_rooms"])?t("Yes"):t("No"),
				));
				// #2
				// tablerows
				foreach($sd["dates"] as $row_no => $date)
				{
					$sc->vars(array(
						"date_type" => ($date["type"] == 0)?t("Normal"):t("Alternative"),
						"arrival_date" => $date["arrival_date"],
						"departure_date" => $date["departure_date"],
					));
					$dates_rows .= $sc->parse("DATES_ROW");
				}

				// flexible dates
				if($sd["dates_are_flexible"])
				{
					if($sd["meeting_pattern"] == 1)
					{
						$cont = $sc->parse("PATTERN_NO_APP");
					}
					elseif($sd["meeting_pattern"] == 2)
					{
						$sc->vars(array(
							"wday_from" => $this->wd[$sd["pattern_wday_from"]],
							"wday_to" => $this->wd[$sd["pattern_wday_to"]],
						));
						$cont = $sc->parse("PATTERN_WDAY");
					}
					elseif($sd["meeting_pattern"] == 3)
					{
						$sc->vars(array(
							"days" => $sd["pattern_day"],
						));
						$cont = $sc->parse("PATTERN_DAYS");
					}
					$sc->vars(array(
						"PATTERN_NO_APP" => $cont,
					));
					$flexible_dates = $sc->parse("FLEXIBLE_DATES");
				}

				$sc->vars(array(
					"DATES_ROW" => $dates_rows,
					"date_comments" => $sd["date_comments"],
					"FLEXIBLE_DATES" => $flexible_dates,
				));
				// #3
				if($sd["needs_rooms"])
				{
					$sc->vars(array(
						"single_count" => $sd["single_count"],
						"double_count" => $sd["double_count"],
						"suite_count" => $sd["suite_count"],
						"arrival_date" => $sd["dates"][0]["arrival_date"],
						"departure_date" => $sd["dates"][0]["departure_date"],
					));
					$sc->vars(array(
						"NEEDS_ROOMS" => $sc->parse("NEEDS_ROOMS"),
					));
				}
				// # 4
				$c_inst = get_instance(CL_CONFERENCE);
				$conf_types = $c_inst->conference_types();

				$days = ($sd["multi_day"] == 2)?$sd["main_function"]:array(0 => $sd["main_function"][0]);
				foreach($days as $day_id => $day)
				{
					$evt_type = ($day["event_type_chooser"] == 1)?$conf_types[$day["event_type_select"]]:$day["event_type_text"];
					unset($tech_equip);
					foreach($day["tech"] as $k => $capt)
					{
						$sc->vars(array("value" => $this->tech_equip[$k]));
						$tech_equip .= $sc->parse("MAIN_TECH_EQUIP");
					}
					foreach($day["main_catering"] as $k => $data)
					{
						if(!count($data))
						{
							continue;
						}
						$cat_type = ($data["catering_type_chooser"] == 1)?$this->catering_types[$data["catering_type_select"]]:$data["catering_type_text"];
						$sc->vars(array(
							"type" => $cat_type,
							"start_time" => $data["catering_start_time"],
							"end_time" => $data["catering_end_time"],
							"attendee_no" => $data["catering_attendees_no"],
						));
						$rows .= $sc->parse("TIMES_ROW");
					}
					$sc->vars(array("TIMES_ROW" => $rows));
					$main_catering = $sc->parse("MAIN_CATERING");
					$sc->vars(array(
						"main_event_type" => $evt_type,
						//"main_delegates_no" => $day["delegates_no"],
						"main_table_form" => $this->table_forms[$day["table_form"]],
						"MAIN_TECH_EQUIP" => $tech_equip,
						"MAIN_CATERING" => $cats,
						"main_door_sign" => $day["door_sign"],
						"main_person_no" => $day["persons_no"],
						"main_start_date" => $day["function_start_date"],
						"main_start_time" => $day["function_start_time"],
						"main_end_time" => $day["function_end_time"],
						//"main_end" => $day["function_end_date"]." ".$day["function_end_time"],
						"main_24h" => $day["24h"]?t("Yes"):t("No"),
						"MAIN_CATERING" => $main_catering,
					));
					unset($rows);
					$main_days .= $sc->parse("MAIN_FUNCTION_DAY");
				}
				$sc->vars(array(
					"MAIN_FUNCTION_DAY" => $main_days,
				));

				// #5
				//arr($sd["additional_functions"]);
				$c_inst = get_instance(CL_CONFERENCE);
				$conf_types = $c_inst->additional_conference_types();
				foreach($sd["additional_functions"] as $k => $data)
				{
					if(!count($data))
					{
						continue;
					}
					$cat_type = ($data["event_type_chooser"] == 1)?$conf_types[$data["event_type_select"]]:$data["event_type_text"];
					if(count($data["catering"]))
					{
						unset($caterings);
						foreach($data["catering"] as $catering)
						{
							$sc->vars(array(
								"type" => ($_t = ($catering["catering_type_chooser"] == 1)?$this->catering_types[$catering["catering_type_select"]]:$data["catering_type_text"])?$_t:t("-"),
								"start_time" => ($_t = $catering["catering_start_time"])?$_t:t("-"),
								"end_time" => ($_t = $catering["catering_end_time"])?$_t:t("-"),
								"attendee_no" => ($_t = $catering["catering_attendee_no"])?$_t:t("-"),
							));
							$caterings .= $sc->parse("ADD_FUNCTION_CATERING_ROW");
						}
						$sc->vars(array(
							"ADD_FUNCTION_CATERING_ROW" => $caterings,
						));
						$add_fun_cat = $sc->parse("ADD_FUNCTION_CATERING");
					}
					else
					{
						unset($add_fun_cat);
					}
					// tech
					$tech = array();
					unset($tech_html);
					foreach(array_keys($data["tech"]) as $te)
					{
						//$tech[] = $this->tech_equip[$te];
						$sc->vars(array(
							"value" => $this->tech_equip[$te],
						));
						$tech_html .= $sc->parse("ADD_FUN_TECH");
					}

					$sc->vars(array(
						"type" => ($_t = $cat_type)?$_t:t("-"),
						"start_date" => $data["function_start_date"],
						"start_time" => $data["function_start_time"],
						"end_time" => $data["function_end_time"],
						"attendee_no" => ($_t = $data["persons_no"])?$_t:t("-"),
						"delegates_no" => $data["delegates_no"],
						"table_form" => $this->table_forms[$data["table_form"]],
						"ADD_FUN_TECH" => $tech_html,
						"door_sign" => $data["door_sign"],
						"persons_no" => $data["persons_no"],
						"24h" => $data["24h"]?t("Yes"):t("No"),
						"ADD_FUNCTION_CATERING" => $add_fun_cat,
					));
					$rows .= $sc->parse("ADD_FUNCTION_ROW");
				}
				$sc->vars(array("ADD_FUNCTION_ROW" => $rows));
				$add_functions = $sc->parse("ADDITIONAL_FUNCTIONS");
				$sc->vars(array(
					"ADDITIONAL_FUNCTIONS" => $add_functions,
				));

				// #6
				$addr = get_instance(CL_CRM_ADDRESS);
				$countries = $addr->get_country_list();
				$sc->vars(array(
					"billing_company" => $sd["billing_company"],
					"billing_contact" => $sd["billing_contact"],
					"billing_street" => $sd["billing_street"],
					"billing_city" => $sd["billing_city"],
					"billing_zip" => $sd["billing_zip"],
					"billing_country" => $countries[$sd["billing_country"]],
					"billing_name" => $sd["billing_name"],
					"billing_phone_number" => $sd["billing_phone_number"],
					"billing_email" => $sd["billing_email"],
				));
				// search res
				unset($rows);
				$loc_inst = get_instance(CL_LOCATION);
				$img_inst = get_instance(CL_IMAGE);
				foreach($sd["selected_search_result"] as $location)
				{
					$o = obj($location);
					if($email = $o->prop_str("email"))
					{
						$sc->vars(array(
							"email"  => $email,
						));
						$email = $sc->parse("RES_EMAIL");
					}
					$imgs = $loc_inst->get_images($location);
					foreach($imgs as $img)
					{
						$sc->vars(array(
							"IMG_".$img->ord() => html::img(array(
								"url" => $img_inst->get_url_by_id($img->id()),
							)),
						));
					}
					$photo = $o->prop("photo");
					$map = $o->prop("map");
					//$inf = $loc_inst->get_add_info($loc_id);
					$sc->vars(array(
						"caption" => $o->name(),
						"address" => $o->prop_str("address"),
						"single_count" => $o->prop("single_count"),
						"double_count" => $o->prop("double_count"),
						"suite_count" => $o->prop("suite_count"),
						"info" => $loc_inst->get_add_info($location),
						"phone" => ($ph = $o->prop_str("phone"))?$ph:t("-"),
						"fax" => ($f = $o->prop_str("fax"))?$f:t("-"),
						"RES_EMAIL" => $email,
						"photo_uri" => $img_inst->get_url_by_id($photo),
						"map_uri" => $img_inst->get_url_by_id($map),
						//"info" => $inf,
					));
					$rows .= $sc->parse("SEARCH_RESULT");
				}
				$sc->vars(array(
					"confirm_ch_id" => CONFIRM_ID,
				));
				$confirm = $sc->parse("CONFIRMATION");
				$sc->vars(array(
					"SEARCH_RESULT" => $rows,
					"CONFIRMATION" => $arr["sub_contents_only"]?"":$confirm,
				));
				break;
			case "qa":
				$sc->read_template("sub_conference_qa.tpl");
				$sc->vars(array(
					"salutation_".$sd["user_salutation"] => "SELECTED",
					"firstname" => $sd["user_firstname"],
					"lastname" => $sd["user_lastname"],
					"company_assocation" => $sd["user_company_assocation"],
					"title" => $sd["user_title"],
					"phone_number" => $sd["user_phone_number"],
					"fax_number" => $sd["user_fax_number"],
					"email" => $sd["user_email"],
					"contact_preference_".$sd["user_contact_preference"] => "SELECTED",
				));
				break;

			case 0:
			default:
				$sc->read_template("sub_conference.tpl");
				foreach($this->get_countries($c_obj->id()) as $cnt)
				{
					if(!is_oid($cnt))
					{
						break;
					}
					$o = obj($cnt);
					$sc->vars(array(
						"value" => $cnt,
						"caption" => $o->trans_get_val("name"),
						"country" => ($cnt == $sd["country"])?"SELECTED":"",
					));
					$countries .= $sc->parse("COUNTRY");
				}
				foreach(array("single", "double", "suite") as $loop_item)
				{
					for($i = 0; $i <= $c_obj->prop($loop_item."_count"); $i++)
					{
						$sc->vars(array(
							"value" => $i,
							"caption" => $i,
						));
						$room_options[strtoupper($loop_item)."_OPTION"] .= $sc->parse(strtoupper($loop_item)."_OPTION");
					}
				}
				$sc->vars($room_options);
				$sc->vars(array(
					"ATTENDES_JS" => $sc->parse("ATTENDES_JS"),
					"name" => $ob->prop("name"),
					"COUNTRY" => $countries,
				));
				break;
		}

		// this is for wierd cases, only once is this used. from rfp_manager ..
		if($arr["sub_contents_only"])
		{
			return $sc->parse();
		}

		$first = "DUMMY";
		if($no == 1)
		{
			$first = "FIRST";
		}
		elseif($no > 1 && $no < 7)
		{
			$first = "OTHER";
		}
		elseif($no == 7)
		{
			$first = "LAST";
		}
		$yah_caption = array(
			1 => "",
			2 => t("Alternative dates"),
			3 => t("Accomondation"),
			4 => t("Main Event"),
			5 => t("Additional Events"),
			6 => t("Booking Details"),
			7 => t("RFP check"),
		);
		// yah bar
		for($i = 1; $i <= 7; $i++)
		{
			$act = ($i == $no)?"ACT_":"";
			$href = (strlen($no) &&  $i < $no)?"_HREF":"";
			if($i == 1)
			{
				$this->vars(array(
					"step_nr" => $i,
					"caption" => strlen($act)?$yah_caption[$i]:"",
					"url" => aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$i,
				));
				$yah[] = $this->parse($act."YAH_FIRST_BTN".$href);
			}
			elseif($i <= 7 && $no != ($i-1))
			{
				unset($last);
				if($i == 7)
				{
					$last = "_LAST";
				}
				$this->vars(array(
					"step_nr" => $i,
					"caption" => $yah_caption[$i],
					"url" => aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$i,
				));
				$yah[] = $this->parse($act."YAH".$last."_BTN".$href);
			}
			elseif($no == 6)
			{
				$this->vars(array(
					"step_nr" => $i,
					"caption" => strlen($act)?$yah_caption[$i]:"",
				));
				$yah[] = $this->parse("YAH_LAST_BTN_AFTER");
			}
			elseif($no != $i-1)
			{
				$this->vars(array(
					"step_nr" => $i,
					"caption" => strlen($act)?$yah_caption[$i]:"",
				));
				$yah[] = $this->parse($act."YAH_LAST_BTN".$href);
			}
			if(strlen($act) && $no != 6 && $i < 7)
			{
				$this->vars(array(
					"step_nr" => ($i+1),
					"caption" => $yah_caption[$i+1],
				));
				$yah[] = $this->parse("YAH_BTN_AFTER");
			}
		}
		$this->vars(array(
			"YAH_BAR" => join("", $yah),
		));
		$first_for_yah = ($first == "LAST")?"OTHER":$first;
		$yah_bar = $this->parse($first_for_yah."_RFP_YAH");

		$this->vars(array(
			"url" => "orb.aw",
		));
		$submit = $this->parse($first."_RFP_SUBMIT");

		$act_evt_no = strlen($_GET["act_evt_no"])?$_GET["act_evt_no"]:-1;
		$act_cat_no = strlen($_GET["act_cat_no"])?$_GET["act_cat_no"]:-1;

		// error managment
		$error_data = aw_global_get("conference_required_errors");
		if(is_array($error_data[$no]) && count($error_data[$no]))
		{
			foreach($error_data[$no] as $err)
			{
				$this->vars(array(
					"caption" => $this->required_fields_error[$no][$err],
				));
				$err_rows .= $this->parse("MISSING_ERROR");
			}
		}


		$this->vars(array(
			"MISSING_ERROR" => $err_rows,
			"YAH_BAR" => join("", $yah),
			"confirm_ch_id" => CONFIRM_ID,
			"sub_contents" => $sc->parse(),
			$first_for_yah."_RFP_YAH" => $yah_bar,
			$first."_RFP_SUBMIT" => $submit,
			"action" => aw_ini_get("baseurl"),
			"reforb" => $this->mk_reforb("submit_back", array(
				"id" => $ob->id(),
				"current_sub" => $no,
				"act_event_no" => $act_evt_no,
				"act_cat_no" => $act_cat_no,
				"conference_planner" => $c_obj->id(),
			)),
		));
		return $this->parse();
	}

//-- methods --//
	/**
		@attrib params=name name=submit_back all_args=1 nologin=1
	**/
	function submit_back($arr)
	{
		$this->save_form_data($arr);
		$this->handle_required_fields($arr);
		return aw_ini_get("baseurl")."/".$arr["id"]."?sub=".($arr["current_sub"]-1);
	}

	function handle_required_fields($arr)
	{
		$error_data = aw_global_get("conference_required_errors");
		if($this->required_fields_conditions($arr["current_sub"],$arr["sub"]))
		{
			foreach($this->required_fields[$arr["current_sub"]] as $req_el)
			{
				if(!strlen($arr["sub"][$arr["current_sub"]][$req_el]))
				{
					$error_data[$arr["current_sub"]][$req_el] = $req_el;
				}
				else
				{
					unset($error_data[$arr["current_sub"]][$req_el]);
				}
			}
		}
		else
		{
			unset($error_data[$arr["current_sub"]]);
		}
		//aw_session_set("conference_required_errors", $error_data);
		$_SESSION["conference_required_errors"] = $error_data;

		$retval = true;
		if(is_array($error_data[$arr["current_sub"]]) && count($error_data[$arr["current_sub"]]))
		{
			$retval = false;
		}
		return $retval;
	}

	/**
		@attrib params=name name=submit_forward all_args=1 nologin=1
	**/
	function submit_forward($arr)
	{
		$this->save_form_data($arr);
		if($arr["current_sub"] == 0 && strlen(trim(aw_global_get("uid"))) == 0)
		{
			// in case the user hasn't logged in yet..
			return aw_ini_get("baseurl")."/".$arr["id"]."?sub=qa";
		}

		// requirements
		if(!$this->handle_required_fields($arr))
		{
			return aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$arr["current_sub"];
		}
		return aw_ini_get("baseurl")."/".$arr["id"]."?sub=".($arr["current_sub"]+1);
	}


	/**
		@attrib params=name name=submit_final all_args=1 nologin=1
	**/
	function submit_final($arr)
	{
		$data = $this->get_form_data();
		//arr($data);
		//die();
		$obj = new object();
		$obj->set_class_id(CL_RFP);
		$obj->set_parent($arr["conference_planner"]);
		// do not save object here, because then you save it twice. and that means, that on the second save
		// you need the "edit" acl for the object, on the first you only need the "add" acl to the parent
		// and things like these are usually configured with only "add" and "view" access, so that
		// random people can not modify them later
		//$obj->save();
		$users = get_instance("users");
		$obj->set_name($data["function_name"]);
		$obj->set_prop("submitter", aw_global_get("uid_oid"));
		$obj->set_prop("contact_preference", $data["user_contact_preference"]);
		$obj->set_prop("country", $data["country"]);
		$obj->set_prop("organisation", $data["organisation_company"]);
		$obj->set_prop("function_name", $data["function_name"]);
		$obj->set_prop("attendees_no", $data["attendees_no"]);
		$obj->set_prop("response_date", $data["dates"][0]["response_date"]);
		$obj->set_prop("decision_date", $data["dates"][0]["decision_date"]);
		$obj->set_prop("arrival_date", $data["dates"][0]["arrival_date"]);
		$obj->set_prop("departure_date", $data["dates"][0]["departure_date"]);
		$obj->set_prop("open_for_alternative_dates", $data["open_for_alternative_dates"]?1:0);
		//$obj->set_prop("accommondation_requirements", $data["accommondation_requirements"]?1:0); // deprecated
		$obj->set_prop("needs_rooms", $data["needs_rooms"]?1:0);
		$obj->set_prop("single_rooms", $data["single_count"]);
		$obj->set_prop("double_rooms", $data["double_count"]);
		$obj->set_prop("suites", $data["suite_count"]);
		$obj->set_prop("date_comments", $data["date_comments"]);
	/*
	*/
		$obj->set_prop("dates_are_flexible", $data["dates_are_flexible"]?1:0);
		$obj->set_prop("meeting_pattern", $data["meeting_pattern"]);
		$obj->set_prop("pattern_wday_from", $data["pattern_wday_from"]);
		$obj->set_prop("pattern_wday_to", $data["pattern_wday_to"]);
		$obj->set_prop("pattern_days", $data["pattern_days"]);

		if($data["dates_are_flexible"])
		{
			if($data["meeting_pattern"] == 1)
			{
				$flex = t("Not Applicable");
			}
			elseif($data["meeting_pattern"] == 2)
			{
				$flex = sprintf(t("From %s to %s"), $this->wd[$data["pattern_wday_from"]], $this->wd[$data["pattern_wday_to"]]);
			}
			elseif($data["meeting_pattern"] == 3)
			{
				$flex = sprintf(t("%s days"), $data["pattern_days"]);
			}
			else
			{
				$flex = t("No");
			}
		}
		else
		{
			$flex = t("No");
		}

		$obj->set_prop("flexible_dates", $flex);

		// main fun
		/*
		$conf_inst = get_instance(CL_CONFERENCE);
		$evt_type = $conf_inst->conference_types();
		$add_evt_type = $conf_inst->additional_conference_types();
		$tmptech = array();
		foreach($data["tech"] as $k => $pnt)
		{
			$tmptech[$k] = $this->tech_equip[$k];
		}
		$obj->set_prop("tech_equip", aw_serialize($tmptech, SERIALIZE_NATIVE));
		$obj->set_prop("tech_equip_raw", aw_serialize($data["tech"], SERIALIZE_NATIVE));
		$obj->set_prop("main_catering", aw_serialize($data["main_catering"], SERIALIZE_NATIVE));

		$tmpcatering = array();
		foreach($data["main_catering"] as $k => $pnt)
		{
			$tmpcatering[] = array(
				"type" => ($pnt["catering_type_chooser"] == 1)?$this->catering_types[$pnt["catering_type_select"]]:$pnt["catering_type_text"],
				"start" => $this->_gen_to_timestamp(false, $pnt["catering_start_time"]),
				"end" => $this->_gen_to_timestamp(false, $pnt["catering_end_time"]),
				"attendees" => $pnt["catering_attendees_no"],
			);
		}

		$obj->set_prop("event_type", ($data["event_type_chooser"] == 1)?$evt_type[$data["event_type_select"]]:$data["event_type_text"]);
		// data separately also
		$obj->set_prop("event_type_chooser", $data["event_type_chooser"]);
		$obj->set_prop("event_type_select", $data["event_type_select"]);
		$obj->set_prop("event_type_text", $data["event_type_text"]);
		//
		$obj->set_prop("delegates_no", $data["delegates_no"]);
		$obj->set_prop("table_form", $this->table_forms[$data["table_form"]]);
		$obj->set_prop("tech", join(", ", $tmptech));
		$obj->set_prop("door_sign", $data["door_sign"]);
		$obj->set_prop("person_no", $data["persons_no"]);
		// duplicated raw_data again
		$obj->set_prop("table_form_raw", $data["table_form"]);
		$obj->set_prop("start_time_raw", $data["function_start_time"]);
		$obj->set_prop("start_date_raw", $data["function_start_date"]);
		$obj->set_prop("end_time_raw", $data["function_end_time"]);
		$obj->set_prop("end_date_raw", $data["function_end_date"]);

		$obj->set_prop("start_date", $this->_gen_to_timestamp($data["function_start_date"], $data["function_start_time"]));
		$obj->set_prop("end_date", $this->_gen_to_timestamp($data["function_end_date"], $data["function_end_time"]));
		$obj->set_prop("24h", $data["24h"]?1:0);

		$obj->set_prop("catering_for_main", aw_serialize($tmpcatering, SERIALIZE_NATIVE));
		*/
		$obj->set_prop("main_function", aw_serialize($data["main_function"], SERIALIZE_NATIVE));
		$obj->set_prop("multi_day", ($data["multi_day"] == 2)?1:0);

		// additional dates
		unset($data["dates"][0]);
		$obj->set_prop("additional_dates_raw", aw_serialize($data["dates"], SERIALIZE_NATIVE));
		if(count($data["dates"]))
		{
			foreach($data["dates"] as $tmp)
			{
				$tmpdates[] = array(
					"type" => ($tmp["type"] == 0)?t("Normal"):t("Alternative"),
					"start" =>  $this->_gen_to_timestamp($tmp["arrival_date"]),
					"end" => $this->_gen_to_timestamp($tmp["departure_date"]),
				);
			}
			$obj->set_prop("additional_dates", aw_serialize($tmpdates, SERIALIZE_NATIVE));
		}
		// additional functions
		$obj->set_prop("additional_functions_raw", aw_serialize($data["additional_functions"], SERIALIZE_NATIVE));
		foreach($data["additional_functions"] as $tmp)
		{
			$tmptech = array();
			foreach($tmp["tech"] as $k => $pnt)
			{
				$tmptech[] = $this->tech_equip[$k];
			}
			$cats = array();
			foreach($tmp["catering"] as $cat)
			{
				$cats[] = array(
					"catering_type" => ($cat["catering_type_chooser"] == 1)?$this->catering_types[$cat["catering_type_select"]]:$cat["catering_type_text"],
					"catering_start" => $cat["catering_start_time"],
					"catering_end" => $cat["catering_end_time"],
				);
			}
			$tmpfunctions[] = array(
				"type" => ($tmp["event_type_chooser"] == 1)?$add_evt_type[$tmp["event_type_select"]]:$tmp["event_type_text"],
				"delegates_no" => $tmp["delegates_no"],
				"table_form" => $this->table_forms[$tmp["table_form"]],
				"tech" => join(", ", $tmptech),
				"door_sign" => $tmp["door_sign"],
				"persons_no" => $tmp["persons_no"],
				"start" => $tmp["function_start_date"]." ".$tmp["function_start_time"],
				"end" => $tmp["function_end_date"]." ".$tmp["function_end_time"],
				"24h" => ($tmp["24h"])?t("Yes"):t("No"),
				"catering" => $cats,
			);
		}
		$obj->set_prop("additional_functions", aw_serialize($tmpfunctions, SERIALIZE_NATIVE));


		// billing

		$obj->set_prop("billing_company", $data["billing_company"]);
		$obj->set_prop("billing_contact", $data["billing_contact"]);
		$obj->set_prop("billing_street", $data["billing_street"]);
		$obj->set_prop("billing_city", $data["billing_city"]);
		$obj->set_prop("billing_zip", $data["billing_zip"]);
		$obj->set_prop("billing_country", $data["billing_country"]);
		$obj->set_prop("billing_name", $data["billing_name"]);
		$obj->set_prop("billing_phone_number", $data["billing_phone_number"]);
		$obj->set_prop("billing_email", $data["billing_email"]);
		$obj->set_prop("urgent", $data["urgent"]?1:0);

		// search_results
		$tmpsearch = array();
		foreach($data["all_search_results"] as $id)
		{
			$tmpsearch[] = array(
				"location" => call_user_func(array(obj($id), "name")),
				"selected" => in_array($id ,$data["selected_search_result"])?1:0,
			);
		}
		$obj->set_prop("all_search_results", aw_serialize($data["all_search_results"], SERIALIZE_NATIVE));
		$obj->set_prop("selected_search_result", aw_serialize($data["selected_search_result"], SERIALIZE_NATIVE));


		$obj->set_prop("search_result", aw_serialize($tmpsearch, SERIALIZE_NATIVE));
		$obj->save();
		$url = aw_ini_get("baseurl");
		$c_obj = obj($arr["conference_planner"]);
		$url .= ($c_obj->prop("redir_doc"))?"/".$c_obj->prop("redir_doc"):"";
		aw_session_set("tmp_conference_data", array());
		$_SESSION["tmp_conference_data"] = array();
		if($c_obj->prop("send_email") == 1)
		{
			$this->do_send_emails(array(
				"oid" => $obj->id(),
				"emails" => $this->gather_email_addresses($data["selected_search_result"]),
				"c_planner" => $c_obj->id(),
			));
		}
		if($c_obj->prop("usr_send_mail") == 1)
		{
			if(is_email($data["billing_email"]) && is_email($c_obj->prop("email.mail")) && strlen($c_obj->prop_str("usr_contents")) && strlen($c_obj->prop_str("usr_subject")))
			{
				$awm = get_instance("protocols/mail/aw_mail");
				$awm->create_message(array(
					"froma" => !is_email($c_obj->prop("email.mail"))?"example@example.com":$c_obj->prop("email.mail"),
					"fromn" => $c_obj->prop("email.name"),
					"subject" => $c_obj->prop_str("usr_subject"),
					"to" => $data["billing_email"],
					"body" => $c_obj->prop_str("usr_contents"),
				));
			$awm->gen_mail();
			}
		}
		return $url;
	}

	function gather_email_addresses($arr)
	{
		foreach($arr as $loc)
		{
			$loc = obj($loc);
			if(is_email($email = $loc->prop("email.mail")))
			{
				$ret[] = $email;
			}
		}
		return $ret;
	}

	function do_send_emails($arr)
	{
		$manager = get_instance(CL_RFP_MANAGER);
		$mail_content = $manager->show_overview(array(
			"oid" => $arr["oid"]
		), true);
		$conf_planner = obj($arr["c_planner"]);
		foreach($arr["emails"] as $email)
		{
			$awm = get_instance("protocols/mail/aw_mail");
			$awm->create_message(array(
				"froma" => !is_email($conf_planner->prop("email.mail"))?"example@example.com":$conf_planner->prop("email.mail"),
				"fromn" => $conf_planner->prop("email.name"),
				"subject" => $conf_planner->prop("subject"),
				"to" => $email,
				"body" => t("Kahjuks sinu meililugeja ei oska n&auml;idata HTML formaadis kirju"),
			));
			$awm->htmlbodyattach(array(
				"data" => $mail_content,
			));
			$awm->gen_mail();
		}

	}

	/**
		@attrib params=name name=submit_user_data all_args=1 nologin=1
	**/
	function submit_user_data($arr)
	{
		if(strlen(trim(aw_global_get("uid"))))
		{
			return aw_ini_get("baseurl")."/".$arr["id"]."?sub=1";
		}
		$data = $arr["sub"]["qa"];
		$this->save_form_data($arr);
		if(!strlen($data["email"]) || !strstr($data["email"], "@"))
		{
			return aw_ini_get("baseurl")."/".$arr["id"]."?sub=qa";
		}

		$us = get_instance(CL_USER);
		classload("users");
		$password = substr(gen_uniq_id(),0,8);
		$taken = false;
		$taken = $us->username_is_taken($data["email"]);
		if($taken)
		{
			aw_session_set("text_for_login", t("Kasutanimi on juba olemas, palun logige sisse."));
			aw_session_set("uid_for_login", $data["email"]);
			$url = aw_ini_get("baseurl")."/".$arr["id"]."?sub=1";
			$this->set_cval("after_login", $url);
			return aw_ini_get("baseurl")."/login.aw";
		}

		$user = $us->add_user(array(
			"uid" => $data["email"],
			"email" => $data["email"],
			"password" => $password,
			"real_name" => $data["firstname"]." ".$data["lastname"],
		));

		$person_obj = new object();
		$person_obj->set_class_id(145);
		$person_obj->set_parent(2);
		$person_obj->set_name($data["firstname"]." ".$data["lastname"]);
		$person_obj->set_prop("firstname",$data["firstname"]);
		$person_obj->set_prop("lastname",$data["lastname"]);
		$person_obj->set_prop("title", ($data["salutation"]-1));
		$person_obj->save_new();


		$user->connect(array(
			"to" => $person_obj->id(),
			"reltype" => 2
		));
		$user->save();

		if($data["company_assocation"])
		{
			$org = new object();
			$org->set_class_id(CL_CRM_COMPANY);
			$org->set_parent($person_obj->id());
			$org->set_name($data["company_assocation"]);
			$org->save();
			$person_obj->add_work_relation(array("org" => $org->id()));
		}

		if($data["phone_number"])
		{
			$phone = new object();
			$phone->set_class_id(219);
			$phone->set_parent($person_obj->id());
			$phone->set_name($data["phone_number"]);
			$phone->set_prop("type", "work");
			$phone->save();

			$person_obj->connect(array(
				"to" => $phone->id(),
				"type" => "RELTYPE_PHONE",
			));
			$person_obj->set_prop("phone", $phone->id());
		}

		if($data["fax_number"])
		{
			$fax = new object();
			$fax->set_class_id(219);
			$fax->set_parent($person_obj->id());
			$fax->set_name($data["fax_number"]);
			$fax->set_prop("type", "fax");
			$fax->save();

			$person_obj->connect(array(
				"to" => $fax->id(),
				"type" => "RELTYPE_FAX",
			));
			$person_obj->set_prop("fax", $fax->id());
		}

		if($data["email"])
		{
			$email = new object();
			$email->set_class_id(73);
			$email->set_parent($person_obj->id());
			$email->set_name($data["email"]);
			$email->set_prop("mail", $data["email"]);
			$email->save();

			$person_obj->connect(array(
				"to" => $email->id(),
				"type" => "RELTYPE_EMAIL",
			));
			$person_obj->set_prop("email", $email->id());
		}
		$person_obj->save();



		// i have to create company also :S
		// we are logging in
		$hash = gen_uniq_id();
		$q = "INSERT INTO user_hashes (hash, hash_time, uid) VALUES('".$hash."','".(time()+60)."','".$data["email"]."')";
		$res = $this->db_query($q);
		$users =get_instance("users");
		return $users->login(array(
			"hash" => $hash ,
			"uid" => $data["email"],
			"return_url" => aw_ini_get("baseurl")."/".$arr["id"]."?sub=1",
		));
		return aw_ini_get("baseurl")."/".$arr["id"]."?sub=1";
	}


	/**
		@attrib params=name name=add_catering all_args=1 nologin=1
	**/
	function add_catering($arr)
	{
		$retval = $this->save_form_data($arr);
		return aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$arr["current_sub"]."&act_evt_no=".($retval?0:$arr["act_event_no"]);
	}

	/**
		@attrib params=name name=add_fun all_args=1 nologin=1
	**/
	function add_fun($arr)
	{
		$this->save_form_data($arr);
		return aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$arr["current_sub"];
	}

	/**
		@attrib params=name name=add_dates all_args=1 nologin=1
	**/
	function add_dates($arr)
	{
		$this->do_actions($arr, "add_dates");
		$this->save_form_data($arr);
		return aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$arr["current_sub"];
	}

	/**
		@attrib params=name name=add_function all_args=1 nologin=1
	**/
	function add_function($arr)
	{
		$this->save_form_data($arr);
		return aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$arr["current_sub"];
	}

	/**
		@attrib params=name name=add_fun_catering all_args=1 nologin=1
	**/
	function add_fun_catering($arr)
	{
		$this->save_form_data($arr);
		return aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$arr["current_sub"]."&act_evt_no=".$arr["act_event_no"];
	}

	function do_actions($arr, $action = false)
	{
		$_GET = $GLOBALS["_GET"];
		$no = $_GET["sub"]?$_GET["sub"]:$arr["current_sub"];
		$data = aw_global_get("tmp_conference_data");
		$act = $action?$action:$_GET["action"];
		$ret = false;
		switch($no)
		{
			case 2:
				if($act == "remove")
				{
					unset($data["dates"][$_GET["id"]]);
				}
				elseif($act == "add_dates")
				{
					$val = $arr["sub"][$no];
					if(is_numeric($val["no_dates_to_add"]) && $val["no_dates_to_add"] > 0)
					{
						for($i=0;$i<$val["no_dates_to_add"];$i++)
						{
							$data["dates"][] = array(
								"type" => "1"
							);
						}
					}
				}
				break;

			case 4:
				if($act == "remove" && strlen($_GET["evt"]) && strlen($_GET["cat"]))
				{
					unset($data["main_function"][$_GET["evt"]]["main_catering"][$_GET["cat"]]);
				}
				elseif($act == "remove" && strlen($_GET["evt"]))
				{
					unset($data["main_function"][$_GET["evt"]]);
				}
				break;
			case 5:
				if($act == "remove")
				{
					if(strlen($_GET["evt"]) && strlen($_GET["cat"]))
					{
						unset($data["additional_functions"][$_GET["evt"]]["catering"][$_GET["cat"]]);
						$ret = aw_ini_get("baseurl")."/".$arr["id"]."?sub=".$no."&act_evt_no=".$_GET["evt"];
					}
					elseif(strlen($_GET["evt"]) && !strlen($_GET["cat"]))
					{
						unset($data["additional_functions"][$_GET["evt"]]);
					}
				}
				break;
		}
		aw_session_set("tmp_conference_data", $data);
		return $ret;
	}

	function save_form_data($arr)
	{
		$form_data = $arr["sub"];
		$_get = $GLOBALS["_GET"];
		$data = $this->get_form_data();
		$retval = false;
		foreach($form_data as $k => $val)
		{
			// new method
			switch($k)
			{
				case "0":
					$data["country"] = $val["country"];
					$data["delegates_no"] = $val["attendees_no"];
					$data["persons_no"] = $val["attendees_no"];
					$data["single_count"] = $val["single_count"];
					$data["double_count"] = $val["double_count"];
					$data["suite_count"] = $val["suite_count"];
					break;
				case "qa":
					$data["user_firstname"] = $val["firstname"];
					$data["user_lastname"] = $val["lastname"];
					$data["user_salutation"] = $val["salutation"];
					$data["user_company_assocation"] = $val["company_assocation"];
					$data["user_title"] = $val["title"];
					$data["user_phone_number"] = $val["phone_number"];
					$data["user_fax_number"] = $val["fax_number"];
					$data["user_email"] = $val["email"];
					$data["user_contact_preference"] = $val["contact_preference"];
					break;
				case "1":
					$data["function_name"] = $val["function_name"];
					$data["multi_day"] = $val["multi_day"];
					$data["door_sign"] = strlen($data["door_sign"])?$data["door_sign"]:$val["function_name"]; // for 4th sub
					$data["organisation_company"] = $val["organisation_company"];
					$data["billing_company"] = strlen($data["billing_company"])?$data["billing_company"]:$val["organisation_company"];
					$data["dates"][0]["response_date"] = $val["response_date"];
					$data["dates"][0]["decision_date"] = $val["decision_date"];
					$data["dates"][0]["arrival_date"] = $val["arrival_date"];
					$data["function_start_date"] = $val["arrival_date"]; // for 4th view
					$data["dates"][0]["departure_date"] = $val["departure_date"];
					$data["function_end_date"] = $val["arrival_date"]; // for 4th view
					$data["dates"][0]["type"] = 0;
					$data["open_for_alternative_dates"] = $val["open_for_alternative_dates"];
					$data["needs_rooms"] = $val["needs_rooms"];
					break;
				case "2":

					$data["dates_are_flexible"] = $val["dates_are_flexible"];
					$data["meeting_pattern"] = $val["meeting_pattern"];
					$data["pattern_wday_from"] = $val["pattern_wday_from"];
					$data["pattern_wday_to"] = $val["pattern_wday_to"];
					$data["pattern_day"] = $val["pattern_day"];
					$data["date_comments"] = $val["date_comments"];
					foreach($val["table_rows"] as $row_no => $row_data)
					{
						$data["dates"][$row_no]["type"] = $row_data["date_type"];
						$data["dates"][$row_no]["arrival_date"] = $row_data["arrival_date"];
						$data["dates"][$row_no]["departure_date"] = $row_data["departure_date"];
					}
					/*
					if($_SERVER["SERVER_ADDR"] == "62.65.36.186")
					{
						arr($data);
					}
					*/
					break;
				case "3":
					$data["needs_rooms"] = $val["needs_rooms"];
					$data["single_count"] = $val["single_count"];
					$data["double_count"] = $val["double_count"];
					$data["suite_count"] = $val["suite_count"];
					$data["dates"][0]["arrival_date"] = $val["main_arrival_date"];
					$data["dates"][0]["departure_date"] = $val["main_departure_date"];
					break;
				case "4":
					$no = $arr["act_event_no"];
					$cat_no = $arr["act_cat_no"];

					$req_fun = array("event_type_chooser", "persons_no", "door_sign", "function_start_date", "function_start_time", "function_end_time");
					$req_cat = array("catering_type_chooser", "catering_start_time", "catering_end_time", "catering_attendees_no");

					$main_fun["event_type_chooser"] = $val["event_type_chooser"];
					$main_fun["event_type_select"] = $val["event_type_select"];
					$main_fun["event_type_text"] = $val["event_type_text"];
					$main_fun["delegates_no"] = $val["delegates_no"];
					$main_fun["table_form"] = $val["table_form"];
					$main_fun["tech"] = $val["tech"];
					$main_fun["door_sign"] = $val["door_sign"];
					$main_fun["persons_no"] = $val["persons_no"];
					$main_fun["function_start_date"] = $val["function_start_date"];
					$main_fun["function_start_time"] = $val["function_start_time"];
					//$main_fun["function_end_date"] = $val["function_end_date"]; // not needed anymore
					$main_fun["function_end_time"] = $val["function_end_time"];
					$main_fun["24h"] = $val["24h"];

					$main_cat["catering_type_chooser"] = $val["catering_type_chooser"];
					$main_cat["catering_type_select"] = $val["catering_type_select"];
					$main_cat["catering_type_text"] = $val["catering_type_text"];
					$main_cat["catering_start_time"] = $val["catering_start_time"];
					$main_cat["catering_end_time"] = $val["catering_end_time"];
					$main_cat["catering_attendees_no"] = $val["catering_attendees_no"];
					// checking fun requirements
					$main_fun_allow = true;
					foreach($req_fun as $req)
					{
						if(!$main_fun[$req])
						{
							$main_fun_allow = false;
							break;
						}
					}
					$main_cat_allow = true;
					// checking cat requirements
					foreach($req_cat as $req)
					{
						if(!$main_cat[$req])
						{
							$main_cat_allow = false;
							break;
						}
					}

					if($no < 0)
					{
						if($main_fun_allow)
						{
							if($main_cat_allow)
							{
								$main_fun["main_catering"][] = $main_cat;
							}
							$data["main_function"][] = $main_fun;
							$retval = true;
						}
					}
					else
					{
						$main_fun["main_catering"] = $data["main_function"][$no]["main_catering"];
						if($cat_no < 0)
						{
							if($main_cat_allow)
							{
								$main_fun["main_catering"][] = $main_cat;
							}
						}
						else
						{
							$main_fun["main_catering"][$cat_no] = $main_cat;
						}
						$data["main_function"][$no] = $main_fun;
					}

					break;
				case "5":
					$no = $arr["act_event_no"];
					$cat_no = $arr["act_cat_no"];


					$additional_function["event_type_chooser"] = $val["event_type_chooser"];
					$additional_function["event_type_select"] = $val["event_type_select"];
					$additional_function["event_type_text"] = $val["event_type_text"];
					$additional_function["delegates_no"] = $val["delegates_no"];
					$additional_function["table_form"] = $val["table_form"];
					$additional_function["tech"] = $val["tech"];
					$additional_function["door_sign"] = $val["door_sign"];
					$additional_function["persons_no"] = $val["persons_no"];
					$additional_function["function_start_date"] = $val["function_start_date"];
					$additional_function["function_start_time"] = $val["function_start_time"];
					//$additional_function["function_end_date"] = $val["function_end_date"]; // not needed anymore
					$additional_function["function_end_time"] = $val["function_end_time"];
					$additional_function["24h"] = $val["24h"];
					$additional_function["catering"] = ($no < 0)?array():$data["additional_functions"][$no]["catering"];


					$additional_function_catering["catering_type_chooser"] = $val["catering_type_chooser"];
					$additional_function_catering["catering_type_select"] = $val["catering_type_select"];
					$additional_function_catering["catering_type_text"] = $val["catering_type_text"];
					$additional_function_catering["catering_start_time"] = $val["catering_start_time"];
					$additional_function_catering["catering_end_time"] = $val["catering_end_time"];
					$additional_function_catering["catering_attendee_no"] = $val["catering_attendee_no"];

					if($no < 0)
					{
						if($val["door_sign"] || $val["persons_no"] || ($val["function_start_date"] && $val["function_start_time"]) ||  ($val["function_end_date"] && $val["function_end_time"]))
						{
							$t = $additional_function_catering;
							if($t["catering_start_time"] || $t["catering_end_time"] || $t["catering_attendee_no"])
							{
								$additional_function["catering"][] = $additional_function_catering;
							}
							$data["additional_functions"][] = $additional_function;
						}
					}
					else
					{
						if($cat_no < 0)
						{
							$t = $additional_function_catering;
							if($t["catering_start_time"] || $t["catering_end_time"] || $t["catering_attendee_no"])
							{
								array_push($additional_function["catering"], $t);
							}
						}
						else
						{
							$additional_function["catering"][$cat_no] = $additional_function_catering;
						}
						$data["additional_functions"][$no] = $additional_function;
					}
					break;
				case "6":
					$data["billing_company"] = $val["billing_company"];
					$data["billing_contact"] = $val["billing_contact"];
					$data["billing_street"] = $val["billing_street"];
					$data["billing_city"] = $val["billing_city"];
					$data["billing_zip"] = $val["billing_zip"];
					$data["billing_country"] = $val["billing_country"];
					$data["billing_name"] = $val["billing_name"];
					$data["billing_phone_number"] = $val["billing_phone_number"];
					$data["billing_email"] = $val["billing_email"];
					$data["selected_search_result"] = array_keys($val["search_result"]);
					$data["all_search_results"] = array_keys($val["all_search"]);
					$data["urgent"] = $val["urgent"];
					break;
				case "7":
					break;
			}
		}
		aw_session_set("tmp_conference_data", $data);
		return $retval;
	}

	function get_form_data()
	{
		return aw_global_get("tmp_conference_data");
	}

	function get_countries($oid)
	{
		if(!is_oid($oid))
		{
			return false;
		}
		$o = obj($oid);
		return $o->prop("countries");
	}

	/**
		@param country
		@param single_rooms
		@param double_rooms
		@param suites
		@param attendees_count
		@param dates optional type=array
			array(
				start => function start time
				end => function end time
				persons => number of persons in this function
			)
		@param pattern_type
		@param pattern_wday_from
		@param pattern_wday_to
		@param pattern_days

	**/
	function all_mighty_search_engine($arr)
	{
		$room_inst = get_instance(CL_ROOM);
		if($this->can("view", $arr["oid"]))
		{
			$obj = obj($arr["oid"]);
		}
		else
		{
			return array();
		}

		$from = array_unique($obj->prop("search_from"));
		$template = array(
			"class_id" => CL_LOCATION,
			"oid" => $from,
			//"status" => STAT_ACTIVE,
		);
		$tmp = array(
			"single_rooms" => "single_count",
			"double_rooms" => "double_count",
			"suites" => "suite_count",
		);
		$search_crit = false;
		$search = $template;
		foreach($tmp as $type => $type_prop)
		{
			if($arr[$type])
			{
				$search_crit = true;
				$search[$type_prop] = $arr[$type];
			}
		}
		$ol = new object_list($search);

		// well, at first we search locations by the rooms, but if there aren't enough places (and room restrictions were set..
		// then we search some additional places which don't have as much rooms, sort them by room total counts and add as many as needed
		$res = $ol->arr();
		if($ol->count() < $obj->prop("search_result_max") && $search_crit)
		{
			if($ol->count != 0)
			{
				$template["oid"] = new obj_predicate_not($ol->ids());
			}
			$new_ol = new object_list($template);
			$new_ol->sort_by_cb(array($this, "__search_sort"));
			$new_res = $new_ol->arr();
			$need = $obj->prop("search_result_max") - count($res);
			foreach($new_res as $k => $o)
			{
				$res[$k] = $o;
				if(!(--$need))
				{
					break;
				}

			}
		}

		$biggest_event = $arr["attendees_count"];
		foreach($arr["dates"] as $data)
		{
			$biggest_event = ($biggest_event < $data["persons"])?$data["persons"]:$biggest_event;
		}
		$res = array_slice($res, 0, $obj->prop("search_result_max"));
		// loop over locations
		foreach($res as $location)
		{
			$location_oid = $location->id();
			$locations[$location_oid] = array();
			$element = &$locations[$location_oid];
			if($arr["single_rooms"] && $location->prop("single_count") < $arr["single_rooms"])
			{
				$element["errors"][] = t("There aren't enough single rooms");
			}
			if($arr["double_rooms"] && $location->prop("double_count") < $arr["double_rooms"])
			{
				$element["errors"][] = t("There aren't enough double rooms");
			}
			if($arr["suites"] && $location->prop("suite_count") < $arr["suites"])
			{
				$element["errors"][] = t("There aren't enough suites");
			}
			// find rooms
			$rooms = new object_list(array(
				"class_id" => CL_ROOM,
				"location" => $location_oid,
			));
			// loop over location rooms
			$biggest_room = false;
			foreach($rooms->arr() as $room_id => $room)
			{
				/*
					actually this is the place where sould be a really nasty code, which checks if these events can be put into those roooms somehow.
					If they can be put, then when .. etc..
					Congratualations to anyone who's gonna do this.. and i do hope i'm not congratulating myself
						taiu
				*/

				$biggest_room = (!$biggest_room || ($biggest_room < $room->prop("normal_capacity")))?$room->prop("normal_capacity"):$biggest_room;
			}
			if($biggest_event > $biggest_room)
			{
				$element["errors"][] = t("There aren't as big rooms as needed");
				break;
			}

		}
		return $locations;
	}

	function _gen_to_timestamp($date = false, $time = false)
	{
		$spl = $date?split("[.]", $date):array(1,1,1970);
		$splt = $time?split(":", $time):array(0,0);
		return mktime($splt[0], $splt[1], 0, $spl[1], $spl[0], $spl[2]);
	}

	function __search_sort($a, $b)
	{
		$a_sum = $a->prop("single_count") + $a->prop("double_count") + $a->prop("suite_count");
		$b_sum = $b->prop("single_count") + $b->prop("double_count") + $b->prop("suite_count");
		return $b_sum - $a_sum;
	}

	function _get_city_mails($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "city",
			"caption" => t("Linn")
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("Meil")
		));

		$vs = aw_unserialize($arr["obj_inst"]->prop("help_views"));
		$el = $vs[0]["elements"][19];
		$ol = new object_list(array(
			"parent" => $el["choices"],
		));

		$ce = $arr["obj_inst"]->meta("city_emails");

		$from = $arr["obj_inst"]->prop("search_from");
		foreach($from as $oid)
		{
				if($this->can("view", $oid))
				{
					$obj = obj($oid);
					$town = obj($obj->prop("address.linn"));
					$towns[$obj->prop("address.linn")] = $town->name();
				}
		}

		foreach($towns as $town_id => $town_name)
		{
			$t->define_data(array(
				"city" => $town_name,
				"email" => html::textbox(array(
					"name" => "ce[".($town_id)."]",
					"value" => $ce[($town_id)]
				))
			));
		}
	}

	function _set_city_mails($arr)
	{
		$arr["obj_inst"]->set_meta("city_emails", $arr["request"]["ce"]);
	}
}
?>
