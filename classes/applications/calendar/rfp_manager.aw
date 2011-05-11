<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/rfp_manager.aw,v 1.105 2009/05/14 14:04:17 robert Exp $
// rfp_manager.aw - RFP Haldus 
/*

@classinfo syslog_type=ST_RFP_MANAGER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo allow_rte=2

@tableinfo aw_rfp_manager index=aw_oid master_index=oid master_table=objects

@default table=objects
@default group=general

@property default_currency type=relpicker reltype=RELTYPE_DEFAULT_CURRENCY store=connect
@caption Vaikevaluuta

@property default_language type=select field=meta method=serialize
@caption Vaikekeel

@property default_conference_planner type=relpicker reltype=RELTYPE_DEFAULT_WEBFORM field=meta method=serialize
@caption Vaike tellimuse vorm

@property copy_redirect type=relpicker reltype=RELTYPE_REDIR_DOC field=meta method=serialize
@caption Edasisuunamisdokument

@property room_folder type=relpicker multiple=1 reltype=RELTYPE_ROOM_FOLDER field=meta method=serialize
@caption Ruumide kaust

@property catering_room_folder type=relpicker multiple=1 reltype=RELTYPE_CATERING_ROOM_FOLDER field=meta method=serialize
@caption Toitlustuse ruumide kaust

@property clients_folder type=relpicker reltype=RELTYPE_CLIENTS_FOLDER field=meta method=serialize
@caption Klientide kaust

@property prod_vars_folder type=relpicker reltype=RELTYPE_PROD_VARS_FOLDER field=meta method=serialize
@caption Toitlustust&uuml;&uuml;pide kaust

@property meta_folder type=relpicker reltype=RELTYPE_META_OBJECT_FOLDER store=connect
@caption Toat&uuml;&uuml;pide kaust

@property table_form_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
@caption Lauaasetuste kaust

@property theme_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
@caption Teemade kaust

@property event_type_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
@caption &Uuml;rituse t&uuml;&uuml;pide kaust

@property country_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
@caption Riikide kaust

@property resources_price_rooms type=relpicker multiple=1 reltype=RELTYPE_RESOURCE_ROOMS field_meta method=serialize
@caption Tellimuste ruumid

@property contact_preference_folder type=relpicker multiple=1 reltype=RELTYPE_FOLDER field=meta method=serialize
@caption Kontakteerumise eelistuste kaust

property city_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize
caption Linnade kaust

@property hotels type=relpicker multiple=1 reltype=RELTYPE_LOCATION field=meta method=serialize
@caption Hotellid

@property rv_cfgmanager type=relpicker reltype=RELTYPE_RV_CFGMANAGER field=meta method=serialize
@caption Reserveeringute seadete haldur

@property default_table type=table no_caption=1 store=no

@groupinfo settings caption="Seaded"

	@groupinfo packages caption="Paketid" parent=settings
		@default group=packages

		@property packages_tb type=toolbar store=no no_caption=1

		@property packages_folder type=relpicker reltype=RELTYPE_PACKAGE_FOLDER field=meta method=serialize
		@caption Pakettide kaust

		@property packages_tbl type=table store=no no_caption=1
	
	@groupinfo packages_products caption="Pakettide toitlustus" parent=settings
		@default group=packages_products

		@property pk_products_folder type=relpicker reltype=RELTYPE_PACKAGE_PRODUCT_FOLDER field=meta method=serialize
		@caption Toodete kaust

		@property pk_products_table type=table store=no no_caption=1

	@groupinfo rooms caption="Ruumid" parent=settings
		@property rooms_table type=table store=no no_caption=1 group=rooms

	@groupinfo resources caption="Ressursid" parent=settings
		@property resources_table type=table store=no no_caption=1 group=resources

@groupinfo raports caption="Raportid"
@groupinfo raport caption="Raport" parent=raports
@default group=raport
	@property raports_tb type=toolbar no_caption=1 store=no

	@layout raports_hsplit type=hbox group=raport width=25%:75%
		@layout raports_search type=vbox closeable=1 area_caption="Otsing" parent=raports_hsplit
			@property raports_search_rfp_name type=textbox parent=raports_search captionside=top store=no
			@caption &Uuml;rituse nimi

			@property raports_search_from_date type=date_select parent=raports_search captionside=top store=no
			@caption Alates

			@property raports_search_until_date type=date_select parent=raports_search captionside=top store=no
			@caption Kuni

			@property raports_search_covering type=chooser multiple=1 orient=vertical parent=raports_search captionside=top store=no
			@caption Mille l&otilde;ikes

			@property raports_search_with_products type=chooser multiple=1 parent=raports_search captionside=top store=no
			@caption Koos toodetega

			@property raports_search_group type=chooser parent=raports_search captionside=top store=no
			@caption Grupeeri

			@property raports_search_rooms type=select multiple=1 parent=raports_search captionside=top store=no
			@caption Ruumid

			@property raports_search_rfp_catering_type type=select multiple=1 parent=raports_search captionside=top store=no
			@caption Toitlustuse t&uuml;&uuml;p

			@property raports_search_rfp_status type=select parent=raports_search captionside=top store=no
			@caption Staatus

			@property raports_search_rfp_submitter type=textbox parent=raports_search captionside=top store=no
			@caption Organisatsioon

			@property raports_search_rfp_city type=select parent=raports_search captionside=top store=no
			@caption Linn

			@property raports_search_rfp_hotel type=select parent=raports_search captionside=top store=no
			@caption Hotell

			@property raports_search_submit type=submit parent=raports_search store=no no_caption=1
			@caption Otsi

		@layout raports_table type=vbox closeable=1 area_caption="Raportid" parent=raports_hsplit
			/@property raports_table type=table no_caption=1 store=no parent=raports_table
			@property raports_table type=text no_caption=1 store=no parent=raports_table

@groupinfo stats parent=raports caption="Statistika" submit=no
@default group=stats

	@property stats_tb type=toolbar no_caption=1

	@layout stats_filt_lay type=hbox width=35%:35%:30% group=stats closeable=1 area_caption=Statistika&nbsp;filter

		 @layout stats_filt_left type=vbox parent=stats_filt_lay

			@property stats_filt_start1 type=date_select store=no parent=stats_filt_left
			@caption &Uuml;rituse algus alates
		
			@property stats_filt_end1 type=date_select store=no parent=stats_filt_left
			@caption &Uuml;rituse algus kuni

			@property stats_filt_currency type=select store=no parent=stats_filt_left
			@caption Valuuta


		@layout stats_filt_middle type=vbox parent=stats_filt_lay

			@property stats_filt_start2 type=date_select store=no parent=stats_filt_middle default=-1
			@caption &Uuml;rituse sisestus alates
		
			@property stats_filt_end2 type=date_select store=no parent=stats_filt_middle default=-1
			@caption &Uuml;rituse sisestus kuni

			@property stats_filt_hotel type=select store=no parent=stats_filt_middle
			@caption Hotell
	
		@layout stats_filt_right type=vbox parent=stats_filt_lay
			
			@property stats_filt_confirmed type=select multiple=1 store=no parent=stats_filt_right
			@caption Staatus

	@layout stats_filt_charts type=hbox closeable=1 area_caption=Graafikud

			@property stats_money_chart type=google_chart no_caption=1 parent=stats_filt_charts

			@property stats_chart type=google_chart no_caption=1 parent=stats_filt_charts
			
			@property stats_chart_filt type=select captionside=top parent=stats_filt_charts
			@caption Graafiku t&uuml;&uuml;p

	@property stats_tbl type=table no_caption=1

	@property stats_sbt type=submit store=no
	@caption Otsi

@groupinfo rfps caption="Tellimused"
@groupinfo rfps_active caption="Aktiivsed" parent=rfps
@groupinfo rfps_archive caption="Arhiiv" parent=rfps
@default group=rfps_active,rfps_archive

	@property rfps_tb type=toolbar store=no no_caption=1

	@layout hsplit type=hbox
		@layout searchbox closeable=1 area_caption=Otsing type=vbox parent=hsplit
			@property s_name type=textbox parent=searchbox size=15 store=no captionside=top
			@caption &Uuml;rituse nimi

			@property s_org type=textbox parent=searchbox size=15 store=no captionside=top
			@caption Organisatsioon

			@property s_contact type=textbox parent=searchbox size=15 store=no captionside=top
			@caption Kontaktisik

			@property s_city type=select parent=searchbox store=no captionside=top
			@caption Linn

			@property s_hotel type=select parent=searchbox store=no captionside=top
			@caption Hotell

			@property s_time_from type=date_select parent=searchbox store=no captionside=top
			@caption Alates

			@property s_time_to type=date_select parent=searchbox store=no captionside=top
			@caption Kuni

			@property s_from_planner type=checkbox parent=searchbox ch_value=1 store=no no_caption=1
			@caption Veebist sisestatud
	
			@property s_submit type=submit parent=searchbox store=no no_caption=1
			@caption Otsi

		@layout main type=vbox parent=hsplit
			@property rfps type=table parent=main no_caption=1


@groupinfo terms caption="Tingimused"
@default group=terms

	@property cancel_and_payment_terms type=textarea richtext=1 table=objects field=meta method=serialize
	@caption Konvererntside annuleerimis- ja maksetingimused

	@property accomondation_terms type=textarea richtext=1 table=objects field=meta method=serialize
	@caption Majutuse annuleerimis- ja maksetingimused

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

@reltype REDIR_DOC value=1 clid=CL_DOCUMENT
@caption Konverentsiplaneerija

@reltype ROOM_FOLDER value=2 clid=CL_MENU
@caption Ruumide kaust

@reltype PACKAGE_FOLDER value=3 clid=CL_MENU,CL_META
@caption Pakettide kaust

@reltype PROD_VARS_FOLDER value=4 clid=CL_MENU,CL_META
@caption Tootepakettide kaust

@reltype CATERING_ROOM_FOLDER value=5 clid=CL_MENU
@caption Toitluste ruumide kaust

@reltype DEFAULT_CURRENCY clid=CL_CURRENCY value=6
@caption RFP vaikevaluuta

@reltype META_OBJECT_FOLDER clid=CL_MENU value=7
@caption Muutujate kaust

@reltype DEFAULT_WEBFORM clid=CL_CONFERENCE_PLANNING value=8
@caption RFP Veebivorm

@reltype FOLDER clid=CL_MENU,CL_META value=9
@caption Kaust

@reltype PACKAGE_PRODUCT_FOLDER clid=CL_MENU value=10
@caption Toodete kaust

@reltype RV_CFGMANAGER clid=CL_CFGMANAGER value=11
@caption Reserveeringute seadete haldur

@reltype CLIENTS_FOLDER clid=CL_MENU value=12
@caption Klientide kaust

@reltype LOCATION clid=CL_LOCATION value=13
@caption Hotell
*/


define("RFP_RAPORT_TYPE_ROOMS", 1);
define("RFP_RAPORT_TYPE_HOUSING", 2);
define("RFP_RAPORT_TYPE_RESOURCES", 3);
define("RFP_RAPORT_TYPE_CATERING", 4);
define("RFP_RAPORT_TYPE_ADDITIONAL_SERVICES", 5);

class rfp_manager extends class_base
{
	function rfp_manager()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/rfp_manager",
			"clid" => CL_RFP_MANAGER
		));

		$this->trans_props = array(
			"cancel_and_payment_terms", "accomondation_terms"
		);

		$this->raport_types = array(
			1 => t("Ruumid"),
			2 => t("Majutus"),
			3 => t("Ressursid"),
			4 => t("Toitlustus"),
			5 => t("Lisateenused"),
		);
		$this->tpl_subs = array(
			1 => "ROOMS",
			2 => "HOUSING",
			3 => "RESOURCES",
			4 => "CATERING",
			5 => "ADDITIONAL_SERVICES",
		);

		$this->search_param_covering = array(
			1 => t("K&otilde;ik"),
			2 => t("Ruumid"),
			3 => t("Toitlustus"),
			4 => t("Majutus"),
			5 => t("Ressursid"),
			6 => t("Lisateenused"),
		);
		$this->rfpm = obj($this->get_sysdefault());
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		// little hack, but hey.. nothing to do..
		if(substr($prop["name"], 0, 15) == "raports_search_")
		{
			$prop["value"] = $arr["request"][$prop["name"]];
		}
		switch($prop["name"])
		{
			//-- get_property --//
			case "default_language":
				$l = get_instance("core/trans/pot_scanner");
				$tl = $l->get_langs();
				foreach(aw_ini_get("languages.list") as $key => $ldat)
				{
					if(!in_array($ldat["acceptlang"], $tl))
					{
						continue;
					}
					$opts[$key] = $ldat["name"];
				}
				$prop["options"] = $opts;
			break;
			// search
			case "raports_search_covering":
				$prop["options"] = $this->search_param_covering;
				break;
			case "raports_search_with_products":
				$prop["options"] = array(
					"1" => t("Koos toodetega"),
				);
				break;
			case "raports_search_group":
				$prop["options"] = array(
					"1" => t("Ajaliselt"),
					"2" => t("Klientide l&otilde;ikes"),
				);
				break;
			case "raports_search_rooms":
				$ol = $arr["obj_inst"]->get_rooms_from_room_folder();
				$ol->add($arr["obj_inst"]->get_rooms_from_catering_room_folder());
				foreach($ol->arr() as $oid => $obj)
				{
					$prop["options"][$oid] = $obj->name(); 
				}
				break;
			case "raports_search_rfp_tables":
				$ol = new object_list(array(
					"parent" => $arr["obj_inst"]->prop("table_form_folder"),
					"class_id" => CL_META,
				));
				foreach($ol->arr() as $oid => $o)
				{
					$prop["options"][$oid] = $o->name();
				}
				break;
			case "raports_search_rfp_status":
				$rfp = get_instance(CL_RFP);
				$prop["options"] = array(
					"0" => t("K&otilde;ik"),
				);
				$prop["options"] += $rfp->get_rfp_statuses();
				break;
			case "s_city":
				$prop["selected"] = $arr["request"]["s_city"];
			case "raports_search_rfp_city":
				$prop["options"][0] = t("-- K&otilde;ik --");
				$hs = $this->rfpm->prop("hotels");
				if(is_array($hs) && count($hs))
				{
					$ol = new object_list(array(
						"class_id" => CL_LOCATION,
						"oid" => $hs,
					));
					foreach($ol->arr() as $obj)
					{
						$cid = $obj->prop("address.linn");
						if($this->can("view", $cid))
						{
							$prop["options"][$cid] = obj($cid)->name();
						}
					}
				}
				break;
			case "s_hotel":
				$prop["selected"] = $arr["request"]["s_hotel"];
			case "raports_search_rfp_hotel":
				$prop["options"][0] = t("-- K&otilde;ik --");
				$hs = $this->rfpm->prop("hotels");
				if(is_array($hs) && count($hs))
				{
					$ol = new object_list(array(
						"class_id" => CL_LOCATION,
						"oid" => $hs,
					));
					foreach($ol->arr() as $obj)
					{
						$prop["options"][$obj->id()] = $obj->name();
					}
				}
				break;

			case "raports_search_rfp_catering_type":
				$ol = new object_list(array(
					"class_id" => CL_META,
					"parent" => $this->rfpm->prop("prod_vars_folder"),
				));
				foreach($ol->arr() as $oid => $o)
				{
					$prop["options"][$oid] = $o->name();
				}
				break;

			// search end
			case "default_currency":
				if($prop["value"] == "")
				{
					$ol = new object_list(array(
						"class_id" => CL_CURRENCY,
						"lang_id" => array(),
					));
					$cur = reset($ol->arr());
					if($cur)
					{
						$prop["options"] = array(
							$cur->id() => $cur->name(),
						);
						$prop["selected"] = $cur->id();
					}
				}
				break;
			case "s_name":
			case "s_org":
			case "s_contact":
			case "s_from_planner":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "stats_filt_hotel":
				$prop["options"][0] = t("-- K&otilde;ik --");
				$hs = $this->rfpm->prop("hotels");
				if(is_array($hs) && count($hs))
				{
					$ol = new object_list(array(
						"class_id" => CL_LOCATION,
						"oid" => $hs,
					));
					foreach($ol->arr() as $obj)
					{
						$prop["options"][$obj->id()] = $obj->name();
					}
				}
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "stats_filt_currency":
				$ol = new object_list(array(
					"class_id" => CL_CURRENCY,
					"lang_id" => array(),
				));
				$prop["options"] = $ol->names();
				$prop["value"] = $arr["request"][$prop["name"]] ? $arr["request"][$prop["name"]] : $arr["obj_inst"]->prop("default_currency");
				break;
			case "stats_filt_confirmed":
				$prop["options"] = get_instance(CL_RFP)->get_rfp_statuses();
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "stats_filt_start1":
			case "stats_filt_start2":
			case "stats_filt_end1":
			case "stats_filt_end2":
				$prop["value"] = date_edit::get_timestamp($arr["request"][$prop["name"]]);
				break;
			case "stats_chart_filt":
				$prop["options"] = array(
					"theme" => t("Teema"),
					"event_type" => t("&Uuml;rituse t&uuml;&uuml;p"),
					"status" => t("Staatus"),
					"international" => t("Rahvusvaheline / kohalik"),
					"rooms" => t("Ruumid"),
				);
				$prop["value"] = $arr["request"][$prop["name"]];
				break;
			case "s_time_to":
			case "s_time_from":
				$_t = &$arr["request"][$prop["name"]];
				if($_t)
				{
					$time = mktime(0,0,0,$_t["month"], $_t["day"], $_t["year"]);
					$prop["value"] = $time;
				}
				else
				{
					$prop["value"] = -1;
				}
				break;
			case "rfps":
				$act = ($arr["request"]["group"] == "rfps_active" || $arr["request"]["group"] == "rfps")?true:false;
				$t = $prop["vcl_inst"];
				$t->define_field(array(
					"name" => "get_pdf",
					"caption" => t("PDF"),
					"chgbgcolor" => "urgent_col",
				));
				$t->define_field(array(
					"name" => "function",
					"caption" => t("&Uuml;ritus"),
					"chgbgcolor" => "urgent_col",
				));
				if(!$arr["request"]["s_from_planner"])
				{
					$t->define_field(array(
						"name" => "org",
						"caption" => t("Organisatsioon"),
						"chgbgcolor" => "urgent_col",
					));
				}
				/*$t->define_field(array(
					"name" => "response_date",
					"caption" => t("Tagasiside aeg"),
					"chgbgcolor" => "urgent_col",
				));*/
				$t->define_field(array(
					"name" => "date_period",
					"caption" => t("Ajavahemik"),
					"chgbgcolor" => "urgent_col",
				));
				if(!$arr["request"]["s_from_planner"])
				{
					$t->define_field(array(
						"name" => "acc_need",
						"caption" => t("Majutus"),
						"chgbgcolor" => "urgent_col",
					));
				}
				$t->define_field(array(
					"name" => "delegates",
					"caption" => t("Inimeste arv"),
					"chgbgcolor" => "urgent_col",
				));
				$t->define_field(array(
					"name" => "contact_pers",
					"caption" => t("Kontaktisik"),
					"chgbgcolor" => "urgent_col",
				));
				$t->define_field(array(
					"name" => "contacts",
					"caption" => t("Kontaktandmed"),
					"chgbgcolor" => "urgent_col",
				));
				if($arr["request"]["s_from_planner"])
				{
					$t->define_field(array(
						"name" => "city",
						"caption" => t("Linn"),
						"chgbgcolor" => "urgent_col",
					));
					$t->define_field(array(
						"name" => "hotel",
						"caption" => t("Hotell"),
						"chgbgcolor" => "urgent_col",
					));
					$t->define_field(array(
						"name" => "evtype",
						"caption" => t("&Uuml;rituse t&uuml;&uuml;p"),
						"chgbgcolor" => "urgent_col",
					));
					$t->define_field(array(
						"name" => "rooms",
						"caption" => t("Hotellitubasid"),
						"chgbgcolor" => "urgent_col",
					));
				}
				$t->define_field(array(
					"name" => "created",
					"caption" => t("Loodud"),
					"type" => "time",
					"numeric" => 1,
					"format" => "d.m.Y",
					"chgbgcolor" => "urgent_col",
				));
				$t->define_field(array(
					"name" => "popup",
					"caption" => t("Tegevus"),
					"align" => "center",
					"chgbgcolor" => "urgent_col",
				));
				$t->set_default_sortby("created");
				$t->set_default_sorder("desc");
				$rrr = get_instance(CL_RFP);
				$rfps = $this->get_rfps($act);
				$rfps = $this->do_filter_rfps($rfps, $arr["request"]);
				$uss = get_instance(CL_USER);
				foreach($rfps as $oid => $obj)
				{
					// end search filter
					$sres = aw_unserialize($obj->prop("search_result"));
					unset($places);
					foreach($sres as $res)
					{
						$places[] = $res["location"];
					}
					$c = array("data_billing_phone", "data_billing_email");
					unset($contacts);
					foreach($c as $e)
					{
						if(strlen(($cnt = $obj->prop($e))))
						{
							$contacts[] = $cnt;
						}
					}
					$urgent_col = ($obj->prop("urgent") == 1)?"#CC3333":"";
					$rooms = array();
					if($_t = $obj->prop("single_rooms"))
					{
						$rooms[] = $_t." ".t("Si");
					}
					if($_t = $obj->prop("double_rooms"))
					{
						$rooms[] = $_t." ".t("Do");
					}
					if($_t = $obj->prop("suites"))
					{
						$rooms[] = $_t." ".t("Su");
					}
					$acc_rooms = join(", ", $rooms);
					if(($sd = $obj->prop("data_gen_arrival_date_admin"))>1)
					{
						$date_period = date('d.m.Y, H:i', $sd);
					}
					if(($ed = $obj->prop("data_gen_departure_date_admin"))>1)
					{
						$date_period .= " - ".date('d.m.Y, H:i', $ed);
					}
					$t->define_data(array(
						"get_pdf" => html::href(array(
							"url" => $this->mk_my_orb("get_pdf_file", array(
								"id" => $obj->id(),
								"pdf" => "pdf",
							), CL_RFP),
							"caption" => html::img(array(
								"border" => 0,
								"url" => "images/icons/ftype_pdf.gif",
							)),
						)),
/*						"function" => html::href(array(
							"caption" => ($_t = $obj->prop("data_gen_function_name"))?$_t:(($_n = $obj->name())?$_n:t('(Nimetu)')),
							"url" => $this->mk_my_orb("change", array(
								"id" => $oid,
								"return_url" => get_ru(),
							),CL_RFP),
						)),*/
						"function" => html::get_change_url($oid, array(
							"return_url" => get_ru(),
						) , $obj->name()),
						"org" => is_oid($obj->prop("data_subm_organisation")) ? $obj->prop("data_subm_organisation.name") : $obj->prop("data_subm_organisation"),
						"response_date" => (($rd = $obj->prop("data_gen_response_date_admin"))>1)?date('d.m.Y, H:i', $rd):"-",
						"date_period" => $date_period,
						"acc_need" => ($obj->prop("data_gen_accommodation_requirements") == 1)?t("Jah"):t("Ei"),
						"delegates" => $obj->prop("data_gen_attendees_no"),
						"contact_pers" => is_oid($obj->prop("data_subm_name")) ? $obj->prop("data_subm_name.name") : $obj->prop("data_subm_name"),
						"contacts" => join(", ", $contacts),
						"created" => $obj->created(),
						"popup" => $this->gen_popup($oid),
						"urgent_col" => $urgent_col,
						"oid" => $oid,
						"city" => $obj->prop("data_gen_city.name"),
						"hotel" => $obj->prop("data_gen_hotel.name"),
						"evtype" => $obj->prop("data_mf_event_type.name"),
						"rooms" => $obj->prop("data_gen_single_rooms") + $obj->prop("data_gen_double_rooms") + $obj->prop("data_gen_suites"),
					));
				}
				$t->sort_by();
				break;
		};
		return $retval;
	}

	function _init_pk_products_table($t)
	{
		$t->define_field(array(
			"name" => "var",
			"caption" => t("Nimetus"),
		));
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "prod",
			"caption" => t("Toode"),
			"align" => "center",
		));
	}

	function _get_pk_products_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_pk_products_table($t);
		$rfs = $arr["obj_inst"]->prop("prod_vars_folder");
		if($this->can("view", $rfs))
		{
			$ol = new object_list(array(
				"class_id" => CL_META,
				"parent" => $rfs,
			));
			foreach($ol->arr() as $o)
			{
				$prodvars[$o->id()] = $o->name();
			}
		}
		$cat_rf = $arr["obj_inst"]->prop("catering_room_folder");
		$rooms = array(0 => t("--vali--"));
		if(is_array($cat_rf) && count($cat_rf))
		{
			$ol = new object_list(array(
				"class_id" => CL_ROOM,
				"parent" => $cat_rf,
			));
			foreach($ol->arr() as $o)
			{
				$rooms[$o->id()] = $o->name();
			}
		}
		$prod_f = $arr["obj_inst"]->prop("pk_products_folder");
		if($this->can("view", $prod_f))
		{
			$ol = new object_list(array(
				"parent" => $prod_f,
				"class_id" => CL_MENU,
				"status" => STAT_ACTIVE,
			));
			$choose_set = false;
			foreach($ol->arr() as $o)
			{
				$p_ol = new object_list(array(
					"parent" => $o->id(),
					"class_id" => CL_SHOP_PRODUCT,
					"status" => STAT_ACTIVE,
				));
				if(!$choose_set)
				{
					$choose_set = true;
				}
				$flds[$o->id()] = $o->name();
				foreach($p_ol->arr() as $o2)
				{
					$prod[$o->id()][$o2->id()] = $o2->name();
				}
			}
			$prods["optgnames"] = $flds;
			$prods["optgroup"] = $prod;
		}
		$pk_prods = $arr["obj_inst"]->meta("pk_prods");
		$ol = new object_list(array(
			"class_id" => CL_META,
			"parent" => $arr["obj_inst"]->prop("packages_folder"),
			"lang_id" => array(),
			"site_id" => array(),
		));
		$pk_counts = $arr["obj_inst"]->meta("pk_counts");
		foreach($ol->arr() as $pkid => $pko)
		{
			$rowc = 0;
			$name = html::textbox(array(
				"name" => "pk_counts[".$pkid."]",
				"value" => $pk_counts[$pkid],
				"size" => 2,
			)).html::obj_change_url($pko->id(), parse_obj_name($pko->name()));
			for($i = 0; $i < $pk_counts[$pkid]; $i++)
			{
				$rowc++;
				$t->define_data(array(
					"name" => $name,
					"var" => html::select(array(
						"name" => "pk_prods[".$pko->id()."][".$i."][var]",
						"options" => $prodvars,
						"value" => $pk_prods[$pko->id()][$i]["var"],
					)),
					"room" => html::select(array(
						"name" => "pk_prods[".$pko->id()."][".$i."][room]",
						"options" => $rooms,
						"value" => $pk_prods[$pko->id()][$i]["room"],
					)),
					"prod" => html::select(array(
						"name" => "pk_prods[".$pko->id()."][".$i."][prod]",
						"options" => array(t("--vali--")),
						"optgroup" => $prods["optgroup"],
						"optgnames" => $prods["optgnames"],
						"size" => 5,
						"value" => $pk_prods[$pko->id()][$i]["prod"],
						"multiple" => 1,
					)),
				));
			}
			if(!$rowc)
			{
				$t->define_data(array(
					"name" => $name,
					"var" => t("(Toitlustuskordade arv on sisestamata)"),
				));
			}
		}
		$t->set_rgroupby(array("name" => "name"));
	}

	function _set_pk_products_table($arr)
	{
		$arr["obj_inst"]->set_meta("pk_counts", $arr["request"]["pk_counts"]);
		if(is_array($arr["request"]["pk_prods"]))
		{
			$arr["obj_inst"]->set_meta("pk_prods", $arr["request"]["pk_prods"]);
		}
		$arr["obj_inst"]->save();
	}

	function _get_raports_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "print",
			"url" => aw_url_change_var("action", "search_result_print_output"),
			"img" => "print.gif",
		));
		$tb->add_button(array(
			"name" => "pdf",
			"url" => aw_url_change_var("action", "search_result_export_pdf"),
			"img" => "ftype_pdf.gif",
		));
	}

	function _get_raports_table(&$arr)
	{
		$from = mktime(0, 0, 0, $arr["request"]["raports_search_from_date"]["month"], $arr["request"]["raports_search_from_date"]["day"], $arr["request"]["raports_search_from_date"]["year"]);
		$to = mktime(0, 0, 0, $arr["request"]["raports_search_until_date"]["month"], $arr["request"]["raports_search_until_date"]["day"], $arr["request"]["raports_search_until_date"]["year"]);
		$res = $this->search_rfp_raports(array(
			"from" => $from,
			"to" => $to,
			"search" => $arr["request"]["raports_search_covering"],
			"rooms" => (is_array($arr["request"]["raports_search_rooms"]) AND count($arr["request"]["raports_search_rooms"]))?$arr["request"]["raports_search_rooms"]:NULL,
			"rfp_status" => $arr["request"]["raports_search_rfp_status"],
			"client" => $arr["request"]["raports_search_rfp_submitter"],
			"tables" => $arr["request"]["raports_search_rfp_tables"],
			"rfp_city" => $arr["request"]["raports_search_rfp_city"],
			"rfp_hotel" => $arr["request"]["raports_search_rfp_hotel"],
			"rfp_name" => $arr["request"]["raports_search_rfp_name"],
			"rfp_catering_type" => $arr["request"]["raports_search_rfp_catering_type"],
		));

		$filters = false;
		if($arr["set_filters"])
		{
		// set what filters where used
			$filters = array();
			$filters[t("Kuup&auml;evavahemik")] = sprintf("%s kuni %s", date("d.m.Y", $from), date("d.m.Y", $to));
			if(is_array($arr["request"]["raports_search_covering"]))
			{
				unset($arr["request"]["raports_search_covering"][1]);
			}
			else
			{
				unset($arr["request"]["raports_search_covering"]);
			}
			if(count($arr["request"]["raports_search_covering"]))
			{
				$cov = array_intersect_key($this->search_param_covering, $arr["request"]["raports_search_covering"]);
				$filters[t("Mille l&otilde;ikes")] = join(", ", $cov);
			}
			if(is_array($arr["request"]["raports_search_rooms"]) and count($arr["request"]["raports_search_rooms"]))
			{
				foreach($arr["request"]["raports_search_rooms"] as $room)
				{
					$rms[] = obj($room)->name();
				}
				$filters[t("Ruumid")] = join(", ", $rms);
			}
			if($arr["request"]["raports_search_rfp_status"])
			{
				$rfp = get_instance(CL_RFP);
				$stats = $rfp->get_rfp_statuses();
				$filters[t("Staatus")] = $stats[$arr["request"]["raports_search_rfp_status"]];
			}
			if(strlen($arr["request"]["raports_search_rfp_submitter"]))
			{
				$filters[t("Klient")] = $arr["request"]["raports_search_rfp_submitter"];
			}
		}

		$arr["prop"]["value"] = $this->display_search_result($res, ($arr["request"]["raports_search_group"] == 2)?true:false, ($arr["request"]["raports_search_with_products"])?true:false, $filters, $arr["with_header_and_footer"]);
	}

	/** Returns nicely formatted search result 
		@attrib api=1
		@param result type=array
			Search result from search_rfp_raports()
		@returns
			Parsed html
	 **/
	public function display_search_result($result, $gr_by_client = false, $with_products = false, $show_used_filters = false, $with_header_and_footer = false)
	{
		$this->read_template("search_result.tpl");
		if(is_array($result) && count($result))
		{
			$cfgu = get_instance("cfg/cfgutils");
			$rfp_props = $cfgu->load_properties(array(
				"clid" => CL_RFP,
			));
			$min_time = mktime(0,0,0,0,0, 2000);
			$cur_time = time();
			if($gr_by_client)
			{
				$row_html = array();
			}
			foreach($result as $data)
			{
				$rfp = $data["rfp"]?obj($data["rfp"]):false;
				$room = $data["room"]?obj($data["room"]):false;
				$row_vars = array(
					"from_date" => date("d.m.Y", $data["start1"]),
					"from_time" => date("H:i", $data["start1"]),
					"to_date" => date("d.m.Y", $data["end"]),
					"to_time" => date("H:i", $data["end"]),
					"room" => $room?$room->name():t("-"),
					"people_count" => $data["people_count"],
					"raport_type" => ($data["result_type"] and !$data["empty_result_type"])?$this->raport_types[$data["result_type"]]:t("-"),
				);
				if($rfp)
				{
					foreach($rfp_props as $prop => $propdata)
					{
						if(substr($prop, 0, 5) == "data_")
						{
							$pval = $rfp->prop($prop);
							if($this->can("view", $pval))
							{
								$_t = obj($pval);
								$pval = $_t->name();
							}
							elseif($pval > $min_time AND $pval < $cur_time)
							{
								$rfp_prop_values[$prop."_date"] = date("d.m.Y", $pval);
								$rfp_prop_values[$prop."_time"] = date("H:i", $pval);
							}

							$rfp_prop_values[$prop] = ($prop == "data_subm_organisation") ? html::obj_change_url($rfp, $pval ? $pval : t("-")) : $pval;
							$rfp_prop_captions[$prop."_caption"] = $propdata["caption"];
							$rfp_prop_empty[$prop."_date"] = "";
							$rfp_prop_empty[$prop."_time"] = "";
						}

						if($prop == "confirmed")
						{
							$inst = $rfp->instance();
							$st = $inst->get_rfp_statuses();
							$rfp_prop_values["confirmed_str"] = $st[$rfp->prop($prop)];
							$rfp_prop_captions["confirmed_caption"] = $propdata["caption"];
							$rfp_prop_empty["confirmed_str"] = "";
							$rfp_prop_empty["confirmed_caption"] = "";
						}
					}
					$ui = get_instance(CL_USER);
					$cper = $ui->get_person_for_uid($rfp->createdby());
					$mper = $ui->get_person_for_uid($rfp->modifiedby());
					$this->vars(array(
						"rfp_createdby_uid" => $rfp->createdby(),
						"rfp_modifiedby_uid" => $rfp->modifiedby(),
						"rfp_createdby_name" => $cper->name(),
						"rfp_modifiedby_name" => $mper->name(),
						"tables" => ($this->can("view", $tbl = obj($data["reservation"])->meta("tables"))) ? obj($tbl)->name() : $rfp->prop("data_mf_table_form.name"),
					));

					$this->vars($rfp_prop_values);
					$this->vars($rfp_prop_captions);
				}
				else
				{
					$rv = obj($data["reservation"]);
					$ui = get_instance(CL_USER);
					$cper = $ui->get_person_for_uid($rv->createdby());
					$mper = $ui->get_person_for_uid($rv->modifiedby());

					$d_name = $d_org = "";
					foreach($rv->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
					{
						$o = $c->to();
						if($o->class_id() == CL_CRM_PERSON)
						{
							$d_name = $o->name();
						}
						elseif($o->class_id() == CL_CRM_COMPANY)
						{
							$d_org = $o->name();
						}
					}
					$rfp_prop_empty = array(  // ugly hack for no-rfp cases
						"rfp_createdby_uid" => $rv->createdby(),
						"rfp_modifiedby_uid" => $rv->modifiedby(),
						"rfp_createdby_name" => $cper->name(),
						"rfp_modifiedby_name" => $mper->name(),
						"data_subm_organisation" => html::obj_change_url($rv, $d_org?$d_org:t("-")),
						"data_subm_name" => $d_name,
						"tables" => t("-"),
					);
					$rfp_prop_empty["confirmed_caption"] = t("Staatus");
					if($rv->prop("verified"))
					{
						$rfp_prop_empty["confirmed_str"] = t("Kinnitatud");
					}
					else
					{
						$rfp_prop_empty["confirmed_str"] = t("T&auml;psustamisel");
					}
					$this->vars($rfp_prop_empty);
				}
				$this->vars($row_vars);
				
				$row_type_var = "ROW_TYPE_".$this->tpl_subs[$data["result_type"]];
				if($gr_by_client)
				{
					if($rfp)
					{
						$clients[$rfp->prop("data_subm_name").".".$rfp->prop("data_subm_organisation")] = array(
							"data_subm_name" => $rfp->prop("data_subm_name"),
							"data_subm_organisation" => $rfp->prop("data_subm_organisation"),
						);
						$key = $rfp->prop("data_subm_name").".".$rfp->prop("data_subm_organisation");
					}
					else
					{
						$key = "unknown";
					}
					$row_type_html = array();
					$row_type_html[$row_type_var] = $this->parse($row_type_var);
					$this->vars($row_type_html);
					$row_html[$key] .= $this->parse("ROW");
					//$row_html .= $this->parse("ROW");
				}
				else
				{
					$row_type_html = array();
					$row_type_html[$row_type_var] = $this->parse($row_type_var);
					$this->vars($row_type_html);
					$row_html .= $this->parse("ROW");
				}
				// now lets empty the row
				$empty[$row_type_var] = "";
				$this->vars($empty);

				if($with_products AND ($data["result_type"] == RFP_RAPORT_TYPE_CATERING OR $data["result_type"] ==  RFP_RAPORT_TYPE_RESOURCES))
				{
					$type_ext = array(
						RFP_RAPORT_TYPE_CATERING => "CATERING",
						RFP_RAPORT_TYPE_RESOURCES => "RESOURCES",
					);
					$current_type = $type_ext[$data["result_type"]];
					$loopdata = ($data["result_type"] == RFP_RAPORT_TYPE_CATERING)?$data["products"]:$data["resources"];
					$cont = false;
					if($data["result_type"] == RFP_RAPORT_TYPE_CATERING)
					{
						if($filt = $_GET["raports_search_rfp_catering_type"])
						{
							$cont = true;
							foreach($filt as $f)
							{
								foreach($loopdata as $subrow_data)
								{
									if($f == $subrow_data["var"])
									{
										$cont = false;
									}
								}
							}
						}
					}
					if($data["result_type"] == RFP_RAPORT_TYPE_RESOURCES)
					{
						$cont = true;
						foreach($loopdata as $subrow_data)
						{
							if($subrow_data["count"])
							{
								$cont = false;
							}
						}
					}
					if(count($loopdata) && !$cont)
					{
						$_row_html = "";
						$_tmp = "";
						$this->vars(array(
							"ROW_TYPE_".$current_type."_HAS_PRODUCTS" => "",
							"ROW_TYPE_".$current_type."_PRODUCT" => "",
						));
						foreach($loopdata as $subrow_key => $subrow_data)
						{
							if($data["result_type"] == RFP_RAPORT_TYPE_RESOURCES && !$subrow_data["count"])
							{
								continue;
							}
							switch($data["result_type"])
							{
								case RFP_RAPORT_TYPE_CATERING:
									$subrow_data["price"] = (double)$subrow_data["price"];
									if($this->can("view", $subrow_data["room"]))
									{
										$subrow_data["room_name"] = obj($subrow_data["room"])->name();
									}
									$subrow_data["product_name"] = obj($subrow_key)->name();
									$subrow_data["product_from_time"] = date("H:i", $subrow_data["start1"]);
									$subrow_data["product_to_time"] = date("H:i", $subrow_data["end"]);
									if(!$subrow_data["sum"] or $subrow_data["sum"] == 0)
									{
										$subrow_data["sum"] = $subrow_data["price"] * $subrow_data["amount"];
										if($subrow_data["discount"])
										{
											$subrow_data["sum"] = $subrow_data["sum"] * ((100 - $subrow_data["discount"]) / 100);
										}
									}
									$subrow_data["sum"] = (strstr($subrow_data["sum"], ","))?$subrow_data["sum"]:number_format($subrow_data["sum"], 2);
									$subrow_data["product_event"] = $subrow_data["product_event"]?$subrow_data["product_event"]:t("Toitlustus");
									if($this->can("view", $subrow_data["var"]))
									{
										$subrow_data["product_event"] = obj($subrow_data["var"])->name();
									}
									if($this->can("view", $subrow_data["rfp"]))
									{
										$subrow_data["comments"] = obj($subrow_data["rfp"])->prop("additional_catering_information");
										$subrow_data["currency"] = obj($subrow_data["rfp"])->prop("default_currency.name");
									}
									$subrow_data["discount"] = $subrow_data["discount"] ? $subrow_data["discount"]."%" : "-";
									break;
								case RFP_RAPORT_TYPE_RESOURCES:
									$subrow_data["resource_name"] = obj($subrow_data["real_resource"])->name();
									$subrow_data["resource_from_time"] = date("H:i", $subrow_data["start1"]);
									$subrow_data["resource_to_time"] = date("H:i", $subrow_data["end"]);
									if($rfp)
									{
										$default_currency = $rfp->prop("default_currency");
									}
									$subrow_data["price"] = $subrow_data["prices"][$default_currency];
									$subrow_data["sum"] = $subrow_data["price"] * $subrow_data["count"];
									break;
							}
							$this->vars($subrow_data);

							$prod_type_var = "ROW_TYPE_".$current_type."_PRODUCT";
							$prod_type_has_var = "ROW_TYPE_".$current_type."_HAS_PRODUCTS";
							$row_type_html = array();
							if($gr_by_client)
							{
								$row_type_html[$prod_type_has_var] .= $this->parse($prod_type_var);
								$this->vars($row_type_html);
								//$row_html[$rfp->prop("data_subm_name").".".$rfp->prop("data_subm_organisation")] .= $this->parse("ROW");
								$_row_html .= $this->parse("ROW");
							}
							else
							{
								$row_type_html[$prod_type_has_var] .= $this->parse($prod_type_var);
								$this->vars($row_type_html);
								//$row_html .= $this->parse("ROW");
								$_row_html .= $this->parse("ROW");
							}
							$row_type_html[$prod_type_var] = "";
							$row_type_html[$prod_type_has_var] = "";
							$this->vars($row_type_html);
						}
						// here we take the rendered product rows and put them inside has_products sub, after what whole table gets the products crap as one single row
						$this->vars(array(
							"ROW_TYPE_".$current_type."_PRODUCT" => $_row_html,
						));
						$_tmp = $this->parse("ROW_TYPE_".$current_type."_HAS_PRODUCTS");
						$this->vars(array(
							"ROW_TYPE_".$current_type."_HAS_PRODUCTS" => $_tmp,
						));
						if($gr_by_client)
						{
							$row_html[$key] .= $this->parse("ROW");
						}
						else
						{
							$row_html .= $this->parse("ROW");
						}
						$row_type_html[$prod_type_var] = "";
						$row_type_html[$prod_type_has_var] = "";
						$this->vars($row_type_html);
					}
				}
			}

				
			if($gr_by_client)
			{
				foreach($clients as $key => $data)
				{
					$this->vars($data);
					$row_html_tmp .= $this->parse("CLIENT_ROW");
					$row_html_tmp .= $row_html[$key];
				}
				$row_html = $row_html_tmp;

			}
			$this->vars(array(
				"HEADER" => $this->parse("HEADER"),
				"ROW" => $row_html,
			));
			$html = $this->parse("HAS_RESULT");
		}
		else
		{
			$no_html = $this->parse("HAS_NO_RESULT");
		}

		if($show_used_filters)
		{
			foreach($show_used_filters as $caption => $value)
			{
				$this->vars(array(
					"filter_caption" => $caption,
					"filter_value" => $value,
				));
				$filt_html .= $this->parse("FILTER");
			}
			$this->vars(array(
				"FILTER" => $filt_html,
			));
			$filt = $this->parse("HAS_FILTERS_USED");
		}
		$this->vars(array(
			"current_date" => date("d.m.Y"),
			"current_time" => date("H:i"),
		));
		$this->vars(array(
			"HAS_NO_RESULT" => $no_html,
			"HAS_RESULT" => $html,
			"HAS_FILTERS_USED" => $filt,
			"PRINT_HEADER" => $this->parse("PRINT_HEADER"),
			"PRINT_FOOTER" => $this->parse("PRINT_FOOTER"),
		));

		return $this->parse();
	}

	function _init_rooms_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
		));
		$t->define_field(array(
			"name" => "min_hours",
			"caption" => t("Min. tunde"),
		));
		/*
		$t->define_field(array(
			"name" => "min_add_price",
			"caption" => t("Lisahind"),
		));
		foreach($this->rfp_currencies() as $oid => $obj)
		{
			$t->define_field(array(
				"name" => "min_price[".$oid."]",
				"caption" => $obj->name(),
				"parent" => "min_add_price",
			));
		}
		 */
		$t->define_field(array(
			"name" => "max_hours",
			"caption" => t("Max. tunde"),
		));
		$t->define_field(array(
			"name" => "max_add_price",
			"caption" => t("Lisahind &uuml;letunnile"),
		));
		foreach($this->rfp_currencies() as $oid => $obj)
		{
			$t->define_field(array(
				"name" => "max_price[".$oid."]",
				"caption" => $obj->name(),
				"parent" => "max_add_price",
			));
		}
	}
	function _get_rooms_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_rooms_table($arr);
		$extra_data = $arr["obj_inst"]->get_extra_hours_prices();
		foreach($arr["obj_inst"]->get_rooms_from_room_folder("room_fld")->arr() as $room_oid => $room)
		{
			$room = obj($room);
			$data = array(
				"room" => html::obj_change_url($room),
				"min_hours" => html::textbox(array(
					"name" => "rooms_table[".$room_oid."][min_hours]",
					"size" => "10",
					"value" => $extra_data[$room_oid]["min_hours"],
				)),
				"max_hours" => html::textbox(array(
					"name" => "rooms_table[".$room_oid."][max_hours]",
					"size" => "10",
					"value" => $extra_data[$room_oid]["max_hours"],
				)),
			);

			foreach($this->rfp_currencies() as $oid => $obj)
			{
				/*
				$data["min_price[".$oid."]"] = html::textbox(array(
					"value" => $extra_data[$room->id()]["min_prices"][$oid],
					"name" => "rooms_table[".$room_oid."][min_prices][".$oid."]",
					"size" => 10,
				));
				 */
				$data["max_price[".$oid."]"] = html::textbox(array(
					"value" => $extra_data[$room->id()]["max_prices"][$oid],
					"name" => "rooms_table[".$room_oid."][max_prices][".$oid."]",
					"size" => 10,
				));
			}

			$t->define_data($data);
		}
	}

	function _set_rooms_table($arr)
	{
		if(is_array($arr["request"]["rooms_table"]))
		{
			$arr["obj_inst"]->set_extra_hours_prices($arr["request"]["rooms_table"]);
		}
	}

	function _init_resources_table(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "resource",
			"caption" => t("Ressurss"),
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
		));
		foreach($this->rfp_currencies() as $currency_oid => $currency)
		{
			$t->define_field(array(
				"name" => "price_".$currency_oid,
				"caption" => $currency->trans_get_val("name"),
				"parent" => "price",
				"align" => "center",
			));
		}

		$t->set_rgroupby(array(
			"room" => "room"
		));
	}

	function _get_resources_table($arr)
	{
		$this->_init_resources_table($arr);
		$t =& $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$prices = $arr["obj_inst"]->get_resource_default_prices();
		foreach($arr["obj_inst"]->get_rooms_from_room_folder("room_fld")->arr() as $room_oid => $room)
		{
			foreach($room->get_resources() as $resource_oid => $resource)
			{
				$d = array(
					"room" => html::obj_change_url($room),
					"resource" => html::obj_change_url($resource),
				);

				foreach($this->rfp_currencies() as $currency_oid => $currency)
				{
					$d["price_".$currency_oid] = html::textbox(array(
						"name" => sprintf("resource_prices[%s][%s][%s]", $room_oid, $resource_oid, $currency_oid),
						"value" => $prices[$room_oid][$resource_oid][$currency_oid],
						"size" => 10,
					));
				}
				$t->define_data($d);
			}
		}
	}

	function _set_resources_table($arr)
	{
		$arr["obj_inst"]->set_resource_default_prices($arr["request"]["resource_prices"]);
	}


	function _get_rfps_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_RFP), $arr["obj_inst"]->parent());
	}

	function _get_default_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_header(t("S&uuml;steemi vaikimisi objekt"));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Objekt"),
		));
		$ol = new object_list(array(
			"class_id" => $this->clid,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$active = $this->get_sysdefault();
		foreach($ol->arr() as $oid=>$o)
		{
			$t->define_data(array(
				"select" => html::radiobutton(array(
					"name" => "default",
					"value" => $oid,
					"checked" => ($oid == $active)?1:0,
				)),
				"name" => html::get_change_url($oid, array(), $o->name()),
			));
		}
	}

	function _set_default_table($arr)
	{
		$ol = new object_list(array(
			"class_id" => $this->clid,
			"site_id" => array(),
			"lang_id" => array(),
		));
		foreach ($ol->arr() as $item)
		{
			if ($item->flag(OBJ_FLAG_IS_SELECTED) && $item->id() != $arr["request"]["default"])
			{
				$item->set_flag(OBJ_FLAG_IS_SELECTED, false);
				$item->save();
			}
			elseif ($item->id() == $arr["request"]["default"] && !$item->flag(OBJ_FLAG_IS_SELECTED))
			{
				$item->set_flag(OBJ_FLAG_IS_SELECTED, true);
				$item->save();
			};
		};
	}


	function _get_packages_tb(&$arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"image" => "new.gif",
			"tooltip" => t("Lisa uus hind"),
		));
		foreach($arr["obj_inst"]->get_packages() as $mt_oid => $pck_data)
		{
			$meta = obj($mt_oid);
			$tb->add_menu_item(array(
				"parent" => "new",
				"name" => "add_under_".$mt_oid,
				"url" => html::get_new_url(CL_ROOM_PRICE, $mt_oid, array(
					"return_url" => get_ru(),
					"pseh" => aw_register_ps_event_handler(
						CL_RFP_MANAGER,
						"handle_new_room_price",
						array(
							"rfp_manager_oid" => $arr["obj_inst"]->id(),
							"rfp_package_oid" => $mt_oid,
						),
						CL_ROOM_PRICE
					),
				)),
				"text" => sprintf(t("'%s' juurde"), $meta->name()),
			));
		}

		$tb->add_button(array(
			"name" => "archive_prcs",
			"img" => "archive_small.gif",
			"action" => "archive_prices",
			"tooltip" => t("Arhiveeri valitud hinnad"),
		));

		$tb->add_button(array(
			"name" => "rem_prcs",
			"img" => "delete.gif",
			"action" => "remove_prices",
			"tooltip" => t("Eemalda valitud hinnad"),
		));
	}

	/** Invoked by pseh, writes newly created room_price id to packages data. for internal use.
		@attrib params=name name=create_new_room_price api=1
	 **/
	function handle_new_room_price($room_price, $arr)
	{
		$rfp_man = obj($arr["rfp_manager_oid"]);
		$packages = $rfp_man->get_packages();
		$packages[$arr["rfp_package_oid"]]["prices"][$room_price->id()] = array();
		$rfp_man->set_packages($packages);
		$rfp_man->save();
	}

	function _init_packages_tbl(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		/*
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		 */
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Kehtib"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind/in"),
			"align" => "center",
		));
		foreach($this->rfp_currencies() as $cur)
		{
			$t->define_field(array(
				"name" => "price".$cur->id(),
				"caption" => $cur->name(),
				"align" => "center",
				"parent" => "price",
			));
		}
		$t->set_rgroupby(array(
			"name" => "name",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "prices_sel",
		));

	}

	function _get_packages_tbl($arr)
	{
		$this->_init_packages_tbl($arr);
		$t = $arr["prop"]["vcl_inst"];
		$pk_fld = $arr["obj_inst"]->prop("packages_folder");
		$prices = $arr["obj_inst"]->get_packages();
		$room_price_inst = get_instance(CL_ROOM_PRICE);
		foreach($prices as $meta_oid => $package_data)
		{
			$meta_obj = obj($meta_oid);
			$data = array(
				"name" => $meta_obj->name(),
			);
			$_ent = false;
			foreach(array_reverse(safe_array($package_data["prices"]), true) as $room_price => $currencies) // dont mind the array reverse here
			{
				$_ent = true;
				$room_price = obj($room_price);
				$_date = date("Y.m.d", $room_price->prop("date_from")). " - ".date("Y.m.d", $room_price->prop("date_to"));
				$time_from = $room_price->prop("time_from");
				$time_to = $room_price->prop("time_to");
				$_time = date("H:i", mktime($time_from["hour"], $time_from["minute"], 0, 0, 0, 0)). " - ".date("H:i", mktime($time_to["hour"], $time_to["minute"], 0, 0, 0, 0));
				$weekd = $room_price->prop("weekdays");
				$_weekd = array();
				foreach($weekd as $wd)
				{
					$_weekd[] = $room_price_inst->weekdays[$wd];
				}
				$time = sprintf("%s / %s / %s / %s", html::obj_change_url($room_price), $_time, $_date, join(", ", $_weekd));
				foreach($this->rfp_currencies() as $cur)
				{
					$data["price".$cur->id()] = html::textbox(array(
						"name" => "prices[".$meta_obj->id()."][prices][".$room_price->id()."][".$cur->id()."]",
						"value" => $currencies[$cur->id()],
						"size" => 5,
					));
					$data["time"] = $time;
					$data["prices_sel"] = $meta_oid."][".$room_price->id();
				}
				$t->define_data($data);
			}
			!$_ent?$t->define_data($data):NULL;
		}
	}

	function _set_packages_tbl($arr)
	{
		
		$arr["obj_inst"]->set_packages($arr["request"]["prices"]);
		$arr["obj_inst"]->save();
	}

	function get_sysdefault()
	{
		$active = false;
		$ol = new object_list(array(
			"class_id" => $this->clid,
			"site_id" => array(),
			"lang_id" => array(),
			"flags" => array(
				"mask" => OBJ_FLAG_IS_SELECTED,
				"flags" => OBJ_FLAG_IS_SELECTED
			)
		));
		if (sizeof($ol->ids()) > 0)
		{
			$first = $ol->begin();
			$active = $first->id();
		}
		else
		{
			$ol2 = new object_list(array(
				"class_id" => $this->clid,
				"site_id" => array(),
				"lang_id" => array(),
			));
		}
		if ($ol2 && sizeof($ol2->ids()) > 0)
		{
			$first = $ol2->begin();
			$active = $first->id();
		}
		elseif($ol2)
		{
			$rfpm = obj();
			$rfpm->set_class_id(CL_RFP_MANAGER);
			$rfpm->set_name(t("RFP halduskeskkond"));
			$rfpm->set_parent(aw_ini_get("document.default_cfgform"));
			$rfpm->set_status(STAT_ACTIVE);
			$rfpm->save();
			$active = $rfpm->id();
		}
		return $active;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		if($arr["group"] == "stats")
		{
			$stats_filt = array("stats_filt_start1", "stats_filt_start2", "stats_filt_end1", "stats_filt_end2", "stats_filt_hotel", "stats_filt_confirmed", "stats_filt_currency");
			foreach($stats_filt as $var)
			{
				if($_GET[$var])
				{
					$arr["h_".$var] = $_GET[$var];
				}
			}
		}
	}

	function callback_mod_retval($arr)
	{
		$todo = array("s_name", "s_org", "s_contact", "s_time_from", "s_time_to", "s_city", "s_hotel", "s_from_planner");
		foreach($todo as $do)
		{
			$arr["args"][$do] = $arr["request"][$do];
		}

		// raports search 
		$todo = array("from_date", "until_date","with_products", "group", "covering", "rooms", "rfp_status", "rfp_submitter", "rfp_city", "rfp_hotel", "rfp_name", "rfp_catering_type", "rfp_tables");
		foreach($todo as $do)
		{
			if($arr["request"]["raports_search_".$do])
			{
				$arr["args"]["raports_search_".$do] = $arr["request"]["raports_search_".$do];
			}
		}
		$stats_filt = array("stats_filt_start1", "stats_filt_start2", "stats_filt_end1", "stats_filt_end2", "stats_filt_hotel", "stats_filt_confirmed", "stats_filt_currency", "stats_chart_filt");
		foreach($stats_filt as $var)
		{
			if($arr["request"][$var])
			{
				$arr["args"][$var] = $arr["request"][$var];
			}
		}
	}


	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("rfp_history.tpl");
		$rfps = $this->get_rfps(true, true);
		foreach($rfps as $oid => $obj)
		{
			$hotel = $obj->prop("data_gen_hotel");
			$this->vars(array(
				"name" => ($_t = $obj->prop("data_gen_function_name"))?$_t:$obj->name(),
				"time" => $obj->prop("data_gen_arrival_date")." - ".$obj->prop("data_gen_departure_date"),
				"attendees" => $obj->prop("data_gen_attendees_no"),
				"hotel" => $hotel,
				//"copy_url" => $this->mk_my_orb("reload_rfp", array(
				"copy_url" => $this->mk_my_orb("rfp_reload", array(
					"rfp" => $obj->id(),
					"oid" => $arr["id"],
					"return_url" => aw_ini_get("baseurl"),
				)),
				"remove_url" => $this->mk_my_orb("archive", array(
					"rfp" => $obj->id(),
					"return_url" => get_ru(),
				)),
			));
			$html .= $this->parse("RFP");
		}

		$this->vars(array(
			"RFP" => $html,	
		));
		return $this->parse();
	}

//-- methods --//

	function get_rfps($act, $cur_user = false)
	{
		$arr = array(
			"class_id" => CL_RFP,
			"archived" => !$act?1:0,
		);
		if($cur_user)
		{
			$arr["createdby"] = aw_global_get("uid");
		}
		$o = new object_list($arr);
		return $o->arr();
	}

	/**
		@attrib params=name name=show_overview all_args=1
		@param oid required type=oid
	**/
	function show_overview($arr, $return_content = false)
	{
		$c_plan= get_instance(CL_CONFERENCE_PLANNING);
		// set data .. this sucks
		$obj = obj($arr["oid"]);
		$data = array(
			"country" => $obj->prop("country"),
			"function_name" => $obj->prop("function_name"),
			"organisation_company" => $obj->prop("organisation"),
			"dates" => array(
				0 => array(
					"response_date" => $obj->prop("response_date"),
					"decision_date" => $obj->prop("decision_date"),
					"arrival_date" => $obj->prop("arrival_date"),
					"departure_date" => $obj->prop("departure_date"),
				),	
			),
			"open_for_alternative_dates" => $obj->prop("open_for_alternative_dates"),
			"accommondation_requirements" => $obj->prop("accommondation_requirements"),
			"flexible_dates" => $obj->prop("flexible_dates"),
			"date_comments" => $obj->prop("date_comments"),
			"needs_rooms" => $obj->prop("needs_rooms"),
			"single_count" => $obj->prop("single_rooms"),
			"double_count" => $obj->prop("double_rooms"),
			"suite_count" => $obj->prop("suites"),
			"event_type_select" => $obj->prop("event_type_select"),
			"event_type_chooser" => $obj->prop("event_type_chooser"),
			"event_type_text" => $obj->prop("event_type_text"),
			"delegates_no" => $obj->prop("delegates_no"),
			"door_sign" => $obj->prop("door_sign"),
			"persons_no" => $obj->prop("person_no"),
			"table_form" => $obj->prop("table_form_raw"),
			"function_start_time" => $obj->prop("start_time_raw"),
			"function_start_date" => $obj->prop("start_date_raw"),
			"function_end_time" => $obj->prop("end_time_raw"),
			"function_end_date" => $obj->prop("end_date_raw"),
			"dates_are_flexible" => $obj->prop("dates_are_flexible"),
			"meeting_pattern" => $obj->prop("meeting_pattern"),
			"pattern_wday_from" => $obj->prop("pattern_wday_from"),
			"pattern_wday_to" => $obj->prop("pattern_wday_to"),
			"pattern_wdays" => $obj->prop("pattern_wdays"),
			"main_catering" => aw_unserialize($obj->prop("main_catering")),
			"tech" => aw_unserialize($obj->prop("tech_equip")),
			"additional_functions" => aw_unserialize($obj->prop("additional_functions_raw")),
			// billing
			"billing_company" => $obj->prop("billing_company"),
			"billing_contact" => $obj->prop("billing_contact"),
			"billing_street" => $obj->prop("billing_street"),
			"billing_city" => $obj->prop("billing_city"),
			"billing_zip" => $obj->prop("billing_zip"),
			"billing_country" => $obj->prop("billing_country"),
			"billing_name" => $obj->prop("billing_name"),
			"billing_phone_number" => $obj->prop("billing_phone_number"),
			"billing_email" => $obj->prop("billing_email"),
			"selected_search_result" => aw_unserialize($obj->prop("selected_search_result")),
			"all_search_results" => aw_unserialize($obj->prop("all_search_results")),
			"main_function" => aw_unserialize($obj->prop("main_function")),
			"multi_day" => aw_unserialize($obj->prop("multi_day")),
		);
		
		$data["dates"] = array_merge($data["dates"], aw_unserialize($obj->prop("additional_dates_raw")));

		$ret = $c_plan->show(array(
			"sub" => 7,
			"sub_contents_only" => true,
			"data" => $data,
		));
		$this->read_template("overview.tpl");
		$this->vars(array(
			"contents" => $ret,
		));
		$content = $this->parse();
		if($return_content)
		{
			return $content;
		}
		else
		{
			die($content);
		}
	}

	function gen_popup($oid)
	{
		$pm = get_instance("vcl/popup_menu");
		$pm->begin_menu("aif_".$oid);
		$obj = obj($oid);
		$act = ($obj->prop("archived") == 1)?false:true;
		$prefix = $act?"":"un";
		$pm->add_item(array(
			"text" => !$act?t("Aktiviseeri"):t("Arhiveeri"),
			"link" => $this->mk_my_orb($prefix."archive", array(
				"rfp" => $oid,
				"return_url" => get_ru(),
			)),
		));
		$pm->add_item(array(
			"text" => t("Kustuta"),
			"link" => $this->mk_my_orb("del_rfp", array(
				"rfp" => $oid,
				"return_url" => get_ru(),
			)),
		));
		return $pm->get_menu();
	}

	/**
		@attrib params=name name=unarchive all_args=1
	**/
	function unarchive($arr)
	{
		$o = obj($arr["rfp"]);
		$o->set_prop("archived", 0);
		$o->save();
		return $arr["return_url"];
	}

	/**
		@attrib params=name name=archive all_args=1
	**/
	function archive($arr)
	{
		$o = obj($arr["rfp"]);
		$o->set_prop("archived", 1);
		$o->save();
		return $arr["return_url"];
	}

	/**
		@attrib params=name name=del_rfp all_args=1
	**/
	function del_rfp($arr)
	{
		$o = obj($arr["rfp"]);
		$o->delete();
		return $arr["return_url"];
	}

	function do_filter_rfps($rfps, $request)
	{
		$_tmp_from = str_replace("---", 0, $request["s_time_from"]);
		$_tmp_to = str_replace("---", 0, $request["s_time_to"]);
		foreach($rfps as $oid => $obj)
		{
			//from conference planner
			if($request["s_from_planner"] && !$obj->prop("from_planner"))
			{
				unset($rfps[$oid]);
			}

			// time
			if($_tmp_from["year"] > 0 && $_tmp_to["year"] > 0)
			{
				$comp = $obj->prop("data_gen_arrival_date_admin");
				$s_f = mktime(0,0,0, $_tmp_from["month"], $_tmp_from["day"], $_tmp_from["year"]);
				$s_t = mktime(23,59,59, $_tmp_to["month"], $_tmp_to["day"], $_tmp_to["year"]);
				if(($s_f != -1 && $s_f >= $comp) || ($s_t != -1 && $s_t <= $comp))
				{
					unset($rfps[$oid]);
				}
				$comp = $obj->prop("data_gen_departure_date_admin");
				if(($s_f != -1 && $s_f >= $comp) || ($s_t != -1 && $s_t <= $comp))
				{
					unset($rfps[$oid]);
				}
			}
	
			// func name
			if(strlen($request["s_name"]) && !stristr($obj->prop("data_gen_function_name") , $request["s_name"]) && !stristr($obj->name() , $request["s_name"]))
			{
				unset($rfps[$oid]);
			}

			// org name
			if($this->can("view", $obj->prop("data_subm_organisation")))
			{
				$orgn = obj($obj->prop("data_subm_organisation"))->name();
			}
			else
			{
				$orgn = $obj->prop("data_subm_organisation");
			}
			if(strlen($request["s_org"]) && !stristr($orgn, $request["s_org"]))
			{
				unset($rfps[$oid]);
			}

			// contact name
			if(strlen($request["s_contact"]))
			{
				$is = false;
				$name = $obj->prop("data_subm_name");
				foreach(split(" ", $request["s_contact"]) as $part)
				{
					$is = stristr($name, $part)?true:$is;
				}
				if($this->can("view", ($name)))
				{
					$is = (stristr(obj($name)->name(), $request["s_contact"]) !== false)?true:$is;
				}
				if(!$is)
				{
					unset($rfps[$oid]);
				}
			}
			if($this->can("view", $request["s_city"]) and $obj->prop("data_gen_city") != $request["s_city"])
			{
				unset($rfps[$oid]);
			}
			if($this->can("view", $request["s_hotel"]) and $obj->prop("data_gen_hotel") != $request["s_hotel"])
			{
				unset($rfps[$oid]);
			}
		}

		return $rfps;
	}

	/**
		@attrib params=name name=rfp_reload all_args=1
	**/
	function rfp_reload($arr)
	{
		$obj = obj($arr["rfp"]);
		$inst = get_instance(CL_CONFERENCE_PLANNING);
		$cfg = get_instance("cfg/cfgutils");
		$list = $cfg->load_properties(array(
			"clid" => CL_RFP
		));
		$grinfo = $cfg->get_groupinfo();
		$list = array_filter($list, array($inst, "__callback_filter_prplist"));
		foreach($list as $prp => $t)
		{
			if($obj->prop($prp))
			{
				$data[$prp] = $obj->prop($prp);
			}
		}
		$inst->store_data($obj->prop("conference_planner"), $data, false);
		$o = $this->can("view", $obj->prop("conference_planner"))?obj($obj->prop("conference_planner")):false;

		return aw_ini_get("baseurl")."/".($o?$o->prop("document"):"");
	}

	/**
		@attrib params=name name=reload_rfp all_args=1
	**/
	function reload_rfp($arr)
	{
		// data = session["tmp_conference_data"]
		$obj = obj($arr["rfp"]);
		$data = array();
		
		$data["function_name"] = $obj->name();
		$data["user_contact_preference"] = $obj->prop("contact_preference");
		$data["country"] = $obj->prop("country");

		$data["organisation_company"] = $obj->prop("organisation");
		$data["attendees_no"]  = $obj->prop("attendees_no");
		// dates = data["dates"]
		$first_date["response_date"] = $obj->prop("response_date");
		$first_date["decision_date"] = $obj->prop("decision_date");
		$first_date["arrival_date"] = $obj->prop("arrival_date");
		$first_date["departure_date"] = $obj->prop("departure_date");
		
		$data["open_for_alternative_dates"] = $obj->prop("open_for_alternative_dates");
		$data["accommondation_requirements"] = $obj->prop("accommondation_requirements");
		$data["needs_rooms"] = $obj->prop("needs_rooms");
		$data["single_count"] = $obj->prop("single_rooms");
		$data["double_count"] = $obj->prop("double_rooms");
		$data["suite_count"] = $obj->prop("suites");
		$data["date_comments"] = $obj->prop("date_comments");
		$data["dates_are_flexible"] = $obj->prop("dates_are_flexible");
		$data["meeting_pattern"] = $obj->prop("meeting_pattern");
		$data["pattern_wday_from"] = $obj->prop("pattern_wday_from");
		$data["pattern_wday_to"] = $obj->prop("pattern_wday_to");
		$data["pattern_days"] = $obj->prop("pattern_days");
		$data["tech"] = aw_unserialize($obj->prop("tech_equip_raw"));
		$data["main_catering"] = aw_unserialize($obj->prop("main_catering"));

		$data["event_type_chooser"] = $obj->prop("event_type_chooser");
		$data["event_type_select"] = $obj->prop("event_type_select");
		$data["event_type_text"] = $obj->prop("event_type_text");
		//
		$data["delegates_no"] = $obj->prop("delegates_no");
		$data["door_sign"] = $obj->prop("door_sign");
		$data["persons_no"] = $obj->prop("person_no");
		$data["table_form"] = $obj->prop("table_form_raw");
		$data["function_start_time"] = $obj->prop("start_time_raw");
		$data["function_start_date"] = $obj->prop("start_date_raw");
		$data["function_end_time"] = $obj->prop("end_time_raw");
		$data["function_end_date"] = $obj->prop("end_date_raw");
		$data["24h"] = $obj->prop("24h");
		$dates = aw_unserialize($obj->prop("additional_dates_raw"));
		$dates[0] = $first_date;
		$data["dates"] = $dates;

		$data["additional_functions"] = aw_unserialize($obj->prop("additional_functions_raw"));

		// billing stuff
		$data["billing_company"] = $obj->prop("billing_company");
		$data["billing_contact"] = $obj->prop("billing_contact");
		$data["billing_street"] = $obj->prop("billing_street");
		$data["billing_city"] = $obj->prop("billing_city");
		$data["billing_zip"] = $obj->prop("billing_zip");
		$data["billing_country"] = $obj->prop("billing_country");
		$data["billing_name"] = $obj->prop("billing_name");
		$data["billing_phone_number"] = $obj->prop("billing_phone_number");
		$data["billing_email"] = $obj->prop("billing_email");
		$data["urgent"] = $obj->prop("urgent");

		$data["all_search_results"] = aw_unserialize($obj->prop("all_search_results"));
		$data["selected_search_result"] = aw_unserialize($obj->prop("selected_search_result"));
		aw_session_set("tmp_conference_data", $data);

		$self = obj($arr["oid"]);
		$c = $self->prop("copy_redirect");

		return aw_ini_get("baseurl")."/".$c."?sub=1";
	}

	/** Finds and returns currencies used by rfp system
		@returns
			array(
				oid => obj
			)
	 **/
	public function rfp_currencies()
	{
		$ol = new object_list(array(
			"class_id" => CL_CURRENCY,
			"lang_id" => array(),
		));
		return $ol->arr();
	}

	/** All mighty rfp raports search engine
		@attrib params=name
		@param rfp_status optional type=int
			Rfp status or 0 for all
		@param rooms optional type=array
			From which rooms to look for (room oid's)
		@param include_products optional type=bool default=false
			Include products in search results if there are any
		@param search optional type=array
			What to search. Options:
			1 => All,
			2 => Rooms,
			3 => Catering,
			4 => Housing,
			5 => Resources,
		@param from optional type=int
			Start time
		@param to optional type=int
			End time
		@param client optional type=string
			searches from rfp submitter name and organisation
	 **/
	public function search_rfp_raports($arr = array())
	{
		$rfps = array(
			"class_id" => CL_RFP,
			"lang_id" => array(),
		);

		$raport_sub_methods = array(
			2 => "rooms",
			3 => "catering",
			4 => "housing",
			5 => "resources",
			6 => "additional_services",
		);

		if($arr["rfp_status"])
		{
			$rfps["CL_RFP.confirmed"] = $arr["rfp_status"];
		}
		if($arr["client"])
		{
			$cl_ol = new object_list(array(
				"class_id" => CL_CRM_COMPANY,
				"name" => "%".$arr["client"]."%",
			));
			if($cl_ol->count())
			{
				$clients = $cl_ol->ids();
			}
			else
			{
				$clients = array(-1);
			}
		}
		$f = $arr["from"];
		$t = mktime(23, 59, 59, date('m', $arr["to"]), date('d', $arr["to"]), date('Y', $arr["to"]));
		if($f > 1 && $t > 1)
		{
			$rfps[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"logic"=> "AND",
						"conditions" => array(
							"CL_RFP.data_gen_arrival_date_admin" => new obj_predicate_compare(OBJ_COMP_GREATER, $f),
							"CL_RFP.data_gen_arrival_date_admin" => new obj_predicate_compare(OBJ_COMP_LESS, $t)
						),
					)),
					new object_list_filter(array(
						"logic"=> "AND",
						"conditions" => array(
							"CL_RFP.data_gen_departure_date_admin" => new obj_predicate_compare(OBJ_COMP_GREATER, $f),
							"CL_RFP.data_gen_departure_date_admin" => new obj_predicate_compare(OBJ_COMP_LESS, $t)
						),
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"CL_RFP.data_gen_arrival_date_admin" => new obj_predicate_compare(OBJ_COMP_LESS, mktime(0, 0, 0, date('m', $f), date('d', $f)+1, date('Y', $f))),
							"CL_RFP.data_gen_departure_date_admin" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, mktime(0, 0, 0, date('m', $f), date('d', $f), date('Y', $f))),
						),
					)),
				),
			));
					
		}
		elseif($f > 1)
		{
			$rfps["CL_RFP.data_gen_arrival_date_admin"] = new obj_predicate_compare(OBJ_COMP_GREATER, $f);
		}
		elseif($t > 1)
		{
			$rfps["CL_RFP.data_gen_departure_date_admin"] = new obj_predicate_compare(OBJ_COMP_LESS, $t);
		}
		$rfps["CL_RFP.data_gen_city"] = $arr["rfp_city"]?$arr["rfp_city"]:array();
		$rfps["CL_RFP.data_gen_hotel"] = $arr["rfp_hotel"]?$arr["rfp_hotel"]:array();
		$rfps["CL_RFP.data_gen_function_name"] = $arr["rfp_name"]?"%".$arr["rfp_name"]."%":array();
		$rfps["CL_RFP.data_mf_table_form"] = $arr["tables"]?$arr["tables"]:array();
		$rfp_ol = new object_list($rfps);
		if(!is_array($arr["search"]) OR in_array(1, $arr["search"]))
		{
			$arr["search"] = array_keys($raport_sub_methods);
		}

		$result = array();
		foreach($arr["search"] as $submethod)
		{
			$method = "_search_rfp_".$raport_sub_methods[$submethod]."_raports";
			if(method_exists($this, $method))
			{
				if(is_array($arr["rooms"]) && $raport_sub_methods[$submethod] == "housing") // housing isn't connected to any rooms.. so, no need to search
				{
					continue;
				}
				if($arr["from"])
				{
					$time = array(
						"start1" => $f,
						"end" => $arr["to"],
					);
				}
				else
				{
					$time = false;
				}
				$result = array_merge($result, $this->$method($rfp_ol, $time));
			}
		}
		$res_inst = get_instance(CL_RESERVATION);
		foreach($result as $k => $data)
		{
			$rfp = null;
			if($arr["from"] && $data["start1"] < $arr["from"])
			{
				unset($result[$k]);
				continue;
			}
			if($arr["to"] && $data["end"] > $t)
			{
				unset($result[$k]);
				continue;
			}
			if($this->can("view", $data["rfp"]))
			{
				$rfp = obj($data["rfp"]);
			}
			if(!$rfp && ($arr["rfp_status"] || $arr["tables"] || $arr["rfp_city"] || $arr["rfp_hotel"] || $arr["rfp_name"]))
			{
				unset($result[$k]);
				continue;
			}
			if(is_array($arr["rooms"]) AND $data["room"] AND !in_array($data["room"], $arr["rooms"]))
			{
				unset($result[$k]);
				continue;
			}
			if(strlen($arr["client"])) // the smartest thing would be to take those props away from meta and use the filter on the rfp obj list..
			{
				if(!$this->can("view", $data["rfp"]) || ((array_search($rfp->prop("data_subm_name"), $clients) === false) AND (array_search($rfp->prop("data_subm_organisation"), $clients) === false)))
				{
					unset($result[$k]);
					continue;
				}
			}
			if($arr["rfp_catering_type"] && array_search($data["var"], $arr["rfp_catering_type"]) === false)
			{
				unset($result[$k]);
				continue;
			}
			if(!$this->can("view", $data["rfp"])) // this is a separate reservation object, came from catering search function. these need to be handled differenctly. here we set the products for them
			{
				$rv = obj($data["reservation"]);
				$prod_list = $res_inst->get_room_products($rv->prop("resource"));
				foreach($prod_list->arr() as $prod_oid => $prod)
				{
					$amount = $rv->get_product_amount();
					if(!$amount[$prod_oid])
					{
						continue;
					}
					$prod_price = $res_inst->get_product_price(array("product" => $prod_oid, "reservation" => $rv->id()));
					$discount = $res_inst->get_product_discount($rv->id());//meta("discount");
					$sum = ($prod_price * $amount[$prod_oid]);
					$sum = ($discount[$prod_oid] > 0 and $discount[$prod_oid])?(((100 - $discount[$prod_oid]) / 100 )* $sum):$sum;
					$result[$k]["products"][$prod_oid] = array(
						"price" => $prod_price,
						"amount" => $amount[$prod_oid],
						"discount" => $discount[$prod_oid],
						"sum" => $sum,
						"room" => $rv->prop("resource"),
						"bronid" => $rv->id(),
						"start1" => $data["start1"],
						"end" => $data["end"],
						"rfp" => false,
					);
				}

			}
		}
		uasort($result, array($this, "_sort_raport_search_result"));
		return $result;
	}
	private function _sort_raport_search_result($a, $b)
	{
		return (($t = $a["start1"] - $b["start1"]) == 0)?$a["end"] - $b["end"]:$t;
	}

	private function _search_rfp_rooms_raports($ol = array(), $time = false)
	{
		$reservations = array();
		foreach($ol->arr() as $oid => $obj)
		{
			$reservations += $obj->get_reservations();
		}
		foreach($reservations as $reservation_id => $data)
		{
			$new = array(
				"room" => $data["resource"],
				"reservation" => $reservation_id,
				"result_type" => RFP_RAPORT_TYPE_ROOMS,
			);
			unset($data["resource"], $data["reservation"]);
			$return[] = $new + $data;
		}
		return $return;
	}

	private function _search_rfp_housing_raports($ol = array(), $time = false)
	{
		$housing = array();
		foreach($ol->arr() as $oid => $obj)
		{
			$_housing[$oid] = $obj->get_housing();
			foreach($_housing[$oid] as $k => $v)
			{
				$v["rfp"] = $oid;
				$housing[] = $v;
			}
		}
		// remapping time params
		$return = array();
		foreach($housing as $k => $data)
		{
			$new = array(
				"start1" => $data["datefrom"],
				"end" => $data["dateto"],
				"people_count" => $data["people"],
				"room" => false,
				"result_type" => RFP_RAPORT_TYPE_HOUSING,
			);
			unset($data["datefrom"], $data["dateto"], $data["people"]);
			$return[] = $new + $data;
		}
		return $return;
	}

	private function _search_rfp_resources_raports($ol = array(), $time = false)
	{
		$resources = array();
		foreach($ol->arr() as $oid => $obj)
		{
			$resources = array_merge($resources, safe_array($obj->get_resources()));
		}
		$return = array();
		foreach($resources as $data)
		{
			$new = array(
				"room" => $data["resource"],
				"result_type" => RFP_RAPORT_TYPE_RESOURCES,
			);
			unset($data["resource"]);
			$return[] = $new + $data;
		}
		return $return;
	}

	private function _search_rfp_catering_raports($ol = array(), $time = false)
	{
		$prods = array();
		foreach($ol->arr() as $oid => $obj)
		{
			$_prods[$oid] = $obj->get_catering();
			foreach($_prods[$oid] as $k => $v)
			{
				$v["rfp"] = $oid;
				$prods[$k] = $v;
			}
		}
		$return = array();
		foreach($prods as $prod_and_rv => $data)
		{
			if($data["amount"] <= 0)
			{
				continue;
			}
			$spl = split("[.]", $prod_and_rv);
			$product_id = $spl[0];
			if(!$this->can("view", $spl[1]) OR !$this->can("view", $spl[0])) // sometimes rel's and objects are removed, but data(oids) remain in metadata.. so we better check first
			{
				continue;
			}
			$reservation = obj($spl[1]);
			$already_used_rvs[] = $reservation->id();
			if(is_array($data["from"]))
			{
				$_from = $reservation->prop("start1");
				$from = mktime($data["from"]["hour"], $data["from"]["minute"], 0, date("m", $_from), date("d", $_from), date("Y", $_from));
			}
			else
			{
				$from = $reservation->prop("start1");
			}
			if(is_array($data["to"]))
			{
				$_to = $reservation->prop("end");
				$to = mktime($data["to"]["hour"], $data["to"]["minute"], 0, date("m", $_to), date("d", $_to), date("Y", $_to));
			}
			else
			{
				$to = $reservation->prop("end");
			}
			$new = array(
				"start1" => $from,
				"end" => $to,
				"room" => $reservation->prop("resource"),
				"people_count" => $reservation->prop("people_count"),
				"reservation" => $reservation->id(),
				"result_type" => RFP_RAPORT_TYPE_CATERING,
				"rfp" => $data["rfp"],
				"var" => $data["var"],
			);
			if(!is_array($return[$reservation->id()]))
			{
				$return[$reservation->id()] = $new;
			}
			$return[$reservation->id()]["products"][$product_id] = $data;
		}
		
		/*$tmplist = new object_list(array(
			"class_id" => CL_ROOM,
			"parent" => $this->rfpm->prop("catering_room_folder"),
		));
		// little silly ugly hack
		$args = array(
			"class_id" => CL_RESERVATION,
			"oid" => new obj_predicate_not($already_used_rvs),
			"resource" => $tmplist->ids(),
		);
		if($time["start1"] and $time["end"])
		{
			$args["start1"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $time["start1"], $time["end"], "int"); 
			$args["end"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $time["start1"], $time["end"], "int");
		}
		$list = new object_list($args);
		foreach($list->arr() as $oid => $obj)
		{
			$return[$oid] = array(
				"start1" => $obj->prop("start1"),
				"end" => $obj->prop("end"),
				"room" => $obj->prop("resource"),
				"people_count" => $obj->prop("people_count"),
				"reservation" => $obj->id(),
				"result_type" => RFP_RAPORT_TYPE_CATERING,
				"empty_result_type" => true,
				"rfp" => false,
			);
			// i dont put the products here right now, most of the reservations get probably filtered out anyway..
		}*/
		foreach($ol->arr() as $oid => $o)
		{
			$conn = $o->connections_from(array(
				"type" => "RELTYPE_CATERING_RESERVATION",
			));
			foreach($conn as $c)
			{
				$rvid = $c->prop("to");
				if(!$return[$rvid])
				{
					$rvo = obj($rvid);
					$return[$rvid] = array(
						"start1" => $rvo->prop("start1"),
						"end" => $rvo->prop("end"),
						"room" => $rvo->prop("resource"),
						"people_count" => $rvo->prop("people_count"),
						"reservation" => $rvid,
						"result_type" => RFP_RAPORT_TYPE_CATERING,
						"rfp" => $oid,
					);
				}
			}
		}
		return $return;
	}
	
	private function _search_rfp_additional_services_raports($ol = array(), $time = false)
	{
		$as = array();
		foreach($ol->arr() as $oid => $obj)
		{
			$tmp = safe_array($obj->get_additional_services());
			foreach($tmp as $k =>  $v)
			{
				$as[] = $v + array(
					"start1" => $v["time"],
					"end" => $v["time"],
					"result_type" => RFP_RAPORT_TYPE_ADDITIONAL_SERVICES,
					"rfp" => $oid,
				);
			}
		}
		return $as;
	}

	function _get_stats_money_chart($arr)
	{
		$arr["request"]["stats_chart_filt"] = "money";
		return $this->_get_stats_chart($arr);
	}

	function _get_stats_chart(&$arr)
	{
		$c = $arr["prop"]["vcl_inst"];
		$c->set_type(GCHART_PIE_3D);
		$c->set_size(array(
			"width" => 500,
			"height" => 150,
		));
		if(!isset($arr["request"]["stats_filt_start1"]))
		{
			return;
		}
		if(!$this->stats_data)
		{
			$data = $this->get_stats_data($arr, false);
		}
		else
		{
			$data = $this->stats_data;
		}
		if(!$arr["request"]["stats_chart_filt"])
		{
			$arr["request"]["stats_chart_filt"] = "theme";
		}
		switch($arr["request"]["stats_chart_filt"])
		{
			case "theme":
			case "international":
			case "status":
			case "event_type":
				$var = $arr["request"]["stats_chart_filt"];
				switch($var)
				{
					case "theme":
						$title = t("&Uuml;rituse teema");
						break;
					case "international":
						$title = t("Rahvusvaheline (r) v&otilde;i kohalik (k)");
						break;
					case "status":
						$title = t("Tellimuse staatus");
						break;
					case "event_type":
						$title = t("&Uuml;rituse t&uuml;&uuml;p");
						break;
				}
				$counts = array();
				foreach($data as $d)
				{
					if($d[$var])
					{
						$counts[$d[$var]] += 1;
					}
				}
				$labels = array();
				foreach($counts as $l => $n)
				{
					$labels[] = $l." (".$n.")";
				}
				$c->add_data($counts);
				$c->set_labels($labels);
				break;
			case "rooms":
				$sums = array_pop($data);
				$title = t("&Uuml;ritustunde ruumides");
				$data = array();
				$labels = array();
				foreach($sums as $k => $s)
				{
					if(!$s)
					{
						continue;
					}
					if(strpos($k, "room") !== false && strpos($k, "rooms") === false)
					{
						$data[] = $s;
						$labels[] = obj(substr($k, 4))->name()." (".$s.")";
					}
				}
				$c->add_data($data);
				$c->set_labels($labels);
				break;
			case "money":
				$sums = array_pop($data);
				$title = t("K&auml;ibe jaotus");
				$fields = array(
					"rooms" => t("Ruumid"),
					"packages" => t("Paketid"),
					"catering" => t("Toitlustus"), 
					"resources" => t("Ressursid"), 
					"housing" => t("Majutus"), 
					"additional_services" => t("Lisateenused"),
				);
				$data = $labels = array();
				foreach($fields as $k => $f)
				{
					if($sums[$k])
					{
						$data[] = $sums[$k];
						$labels[] = $f." (".$sums[$k].")";
					}
				}
				$c->add_data($data);
				$c->set_labels($labels);
				break;
		}
		$c->set_title(array(
			"text" => $title,
			"color" => "666666",
			"size" => 11,
		));
	}

	function _get_stats_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_save_button();
		$tb->add_button(array(
			"name" => "export_stats",
			"tooltip" => t("Ekspordi statistika"),
			"img" => "ftype_xls.gif",
			"action" => "export_stats",
		));
	}

	function _init_stats_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "general",
			"caption" => t("&Uuml;ldinfo"),
		));
		$t->define_field(array(
			"name" => "eas",
			"caption" => t("EAS"),
		));
		$t->define_field(array(
			"name" => "event_hours",
			"caption" => t("&Uuml;ritustunnid"),
		));
		$t->define_field(array(
			"name" => "money",
			"caption" => t("K&auml;ive"),
		));

		$t->define_field(array(
			"name" => "evt_start",
			"caption" => t("Algus"),
			"parent" => "general",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "evt_entry",
			"caption" => t("Sisestus"),
			"parent" => "general",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "realization",
			"caption" => t("Tellimuse realiseerumine"),
			"parent" => "general",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "orderer",
			"caption" => t("Tellija"),
			"parent" => "general",
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "theme",
			"caption" => t("Teema"),
			"parent" => "eas",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "duration",
			"caption" => t("Kestus"),
			"parent" => "eas",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "international",
			"caption" => t("Rahvusvaheline / kohalik"),
			"parent" => "eas",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "countries",
			"caption" => t("Riigid"),
			"parent" => "eas",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "foreign_guests",
			"caption" => t("V&auml;lisk&uuml;lalised"),
			"parent" => "eas",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "local_guests",
			"caption" => t("Kohalikud"),
			"parent" => "eas",
			"align" => "center",
		));

		$rf = $arr["obj_inst"]->prop("room_folder");
		$ol = new object_list(array(
			"class_id" => CL_ROOM,
			"parent" => $rf,
		));
		foreach($ol->names() as $oid => $name)
		{
			$t->define_field(array(
				"name" => "room".$oid,
				"caption" => $name,
				"parent" => "event_hours",
				"align" => "center",
			));
		}

		$t->define_field(array(
			"name" => "rooms",
			"caption" => t("Ruumid"),
			"parent" => "money",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "packages",
			"caption" => t("Paketid"),
			"parent" => "money",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "catering",
			"caption" => t("Toitlustus"),
			"parent" => "money",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "resources",
			"caption" => t("Ressursid"),
			"parent" => "money",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "housing",
			"caption" => t("Majutus"),
			"parent" => "money",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "additional_services",
			"caption" => t("Lisateenused"),
			"parent" => "money",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "total",
			"caption" => t("Kokku"),
			"parent" => "money",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "event_type",
			"caption" => t("&Uuml;rituse t&uuml;&uuml;p"),
			"parent" => "money",
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Tellimuse staatus"),
			"parent" => "money",
			"align" => "center",
		));
		$t->set_sortable(false);
		$t->set_caption(t("Statistika andmed"));
	}

	function _get_stats_tbl($arr)
	{
		if($arr["request"]["stats_filt_start1"] && $arr["request"]["stats_filt_end1"] || $arr["request"]["stats_filt_start2"] && $arr["request"]["stats_filt_end2"])
		{
			$t = $arr["prop"]["vcl_inst"];
			$this->_init_stats_tbl($arr);
			if(!$this->stats_data)
			{
				$data = $this->get_stats_data($arr, false);
			}
			else
			{
				$data = $this->stats_data;
			}
			foreach($data as $row)
			{
				$t->define_data($row);
			}
		}
	}

	/**
	@attrib name=export_stats all_args=1
	**/
	function export_stats($arr)
	{
		$stats_filt = array("stats_filt_start1", "stats_filt_start2", "stats_filt_end1", "stats_filt_end2", "stats_filt_hotel", "stats_filt_confirmed", "stats_filt_currency");
		foreach($stats_filt as $var)
		{
			$arr["request"][$var] = $arr["h_".$var];
		}
		$arr["obj_inst"] = obj($arr["id"]);
		$data = $this->get_stats_data($arr, true);
		$out = array();
		foreach($data as $row)
		{
			foreach($row as $var => $val)
			{
				$row[$var] = sprintf("\"%s\"", str_replace("\"", "\\"."\"", html_entity_decode($val)));
			}
			$out[] = implode(";", $row);
		}
		$out = implode("\r\n", $out);
		header('Content-type: application/octet-stream');
		header("Content-Disposition: root_access; filename=stats.csv");
		die($out);
	}

	function get_stats_data($arr, $is_export)
	{
		$param["class_id"] = CL_RFP;
		$start1 = date_edit::get_timestamp($arr["request"]["stats_filt_start1"]);
		$start2 = date_edit::get_timestamp($arr["request"]["stats_filt_start2"]);
		$end1 = date_edit::get_timestamp($arr["request"]["stats_filt_end1"]);
		$end2 = date_edit::get_timestamp($arr["request"]["stats_filt_end2"]);
		if($start1 > 0 && $end1 > 0)
		{
			$param["data_gen_arrival_date_admin"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start1, $end1);
		}
		if($start2 > 0 && $end2 > 0)
		{
			$param["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start2, $end2);
			
		}
		if($arr["request"]["stats_filt_confirmed"])
		{
			$param["confirmed"] = $arr["request"]["stats_filt_confirmed"];
		}
		$cur = $param["default_currency"] = $arr["request"]["stats_filt_currency"];
		if($arr["request"]["stats_filt_hotel"])
		{
			$param["data_gen_hotel"] = $arr["request"]["stats_filt_hotel"];
		}

		$rvi = get_instance(CL_RESERVATION);
		$ri = get_instance(CL_ROOM);
		$rfpi = get_instance(CL_RFP);

		$ol = new object_list($param);
		$ol->sort_by(array(
			"prop" => "data_gen_arrival_date_admin",
			"sorder" => "asc",
		));
		
		$rf = $arr["obj_inst"]->prop("room_folder");
		$r_ol = new object_list(array(
			"class_id" => CL_ROOM,
			"parent" => $rf,
		));
		$rooms = $r_ol->names();

		$data = array();

		if($is_export)
		{
			$titles = array();
			$titles["evt_start"] = t("&Uuml;ldinfo"); $titles["evt_entry"] = "";
			$titles["realization"] = ""; $titles["orderer"] = "";
			$titles["theme"] = t("EAS"); $titles["duration"] = "";
			$titles["international"] = ""; $titles["countries"] = "";
			$titles["foreign_guests"] = ""; $titles["local_guests"] = "";
			foreach($rooms as $rid => $room)
			{
				$titles["room".$rid] = $r_set ? "" : t("&Uuml;ritustunnid");
				$r_set = 1;
			}
			$titles["rooms"] = t("K&auml;ive"); $titles["packages"] = "";
			$titles["catering"] = "";
			$titles["resources"] = ""; $titles["housing"] = "";
			$titles["additional_services"] = ""; $titles["total"] = "";
			$titles["event_type"] = ""; $titles["status"] = ""; 
			$data[] = $titles;
			$titles = array();
			$titles["evt_start"] = t("Algus");
			$titles["evt_entry"] = t("Sisestus");
			$titles["realization"] = t("Tellimuse realiseerumine");
			$titles["orderer"] = t("Tellija");
			$titles["theme"] = t("Teema");
			$titles["duration"] = t("Kestus");
			$titles["international"] = t("Rahvusvaheline / kohalik");
			$titles["countries"] = t("Riigid");
			$titles["foreign_guests"] = t("V&auml;lisk&uuml;lalised");
			$titles["local_guests"] = t("Kohalikud");
			foreach($rooms as $rid => $room)
			{
				$titles["room".$rid] = $room;
			}
			$titles["rooms"] = t("Ruumid");
			$titles["packages"] = t("Paketid");
			$titles["catering"] = t("Toitlustus");
			$titles["resources"] = t("Ressursid");
			$titles["housing"] = t("Majutus");
			$titles["additional_services"] = t("Lisateenused");
			$titles["total_sum"] = t("Kokku");
			$titles["event_type"] = t("&Uuml;rituse t&uuml;&uuml;p");
			$titles["status"] = t("Tellimuse staatus");
			$data[] = $titles;
		}

		$total_sum = 0;

		foreach($ol->arr() as $o)
		{
			$start = $o->prop("data_gen_arrival_date_admin");
			$end = $o->prop("data_gen_departure_date_admin");
			$row["evt_start"] = date('d.m.Y', $start);
			$row["evt_entry"] = date('d.m.Y', $o->created());
			$row["realization"] = round(($start - $o->created()) / 60 / 60 / 24).(!$is_export ? " ".t("p&auml;eva") : "");
			$orderer = $o->prop("data_subm_organisation");
			$row["orderer"] = $this->can("view", $orderer) ? obj($orderer)->name() : $orderer;
			$row["theme"] = $o->prop("final_theme.name");
			$row["duration"] = (mktime(0,0,0,date('m', $end), date('d', $end), date('Y', $end)) - mktime(0,0,0,date('m', $start), date('d', $start), date('Y', $start))) / 24 / 60 / 60 + 1;
			$duration_sum += $row["duration"];
			$row["international"] = $o->prop("final_international") ? "r" : "k";
			$cids = $o->prop("final_foreign_countries");
			$countries = array();
			foreach($cids as $cid)
			{
				$countries[$cid] = obj($cid)->name();
			}
			$row["countries"] = implode(", ", $countries);
			$row["foreign_guests"] = $o->prop("final_foreign_guests");
			$foreign_guests_sum += $row["foreign_guests"];
			$row["local_guests"] = $o->prop("final_native_guests");
			$local_guests_sum += $row["local_guests"];

			$bron_conn = $o->connections_from(array(
				"type" => "RELTYPE_RESERVATION",
			));
			$res_sum = 0;
			$room_sum = 0;
			$package_sum = 0;
			$roomdata = array();
			$pk_prices = $arr["obj_inst"]->meta("pk_prices");
			$pk_price = $o->prop("data_gen_package_price");
			$people = $o->prop("data_gen_attendees_no");
			$package_id = $o->prop("data_gen_package");
			foreach($bron_conn as $c)
			{
				$bron = $c->to();
				if($bron->prop("verified"))
				{
					$start = $bron->prop("start1");
					$end = $bron->prop("end");
	
					$times = $rfpi->alter_reservation_time_include_extra_min_hours($bron, $arr["obj_inst"]);
					$sum = $ri->cal_room_price(array(
						"room" => $bron->prop("resource"),
						"start" => $times["start1"],
						"end" => $times["end"],
						"people" => $bron->prop("people_count"),
						"products" => $bron->meta("amount"),
						"bron" => $bron,
						"detailed_info" => true
					));
					$sum["room_price"] = $rfpi->alter_reservation_price_include_extra_max_hours($bron, $arr["obj_inst"], $sum["room_price"]);
					$total = $sum["room_price"][$cur];
					$ssum = $bron->get_special_sum();

					if(is_array($pk_prices) && $package_id)
					{
						$price = $pk_prices[$package_id]["prices"][$pk_price][$cur];
						$pk_discount = $o->get_package_custom_discount();
						if($pk_discount)
						{
							$price *= (100 - $pk_discount ) / 100;
						}
						$package_sum += $price * $people;
						$total = 0;
					}

					if($ssum[$cur])
					{
						$total = $ssum[$cur];
					}
					elseif($package_sum)
					{
						$total = $package_sum;
					}

					$room_sum += $total;

					$room = $bron->prop("resource");
					$roomdata[$room] += (mktime(date('H', $end), 0, 0, date('m', $end), date('d', $end), date('Y', $end)) - mktime(date('H', $start), 0, 0, date('m', $start), date('d', $start), date('Y', $start))) /  60 / 60 + 1;
					$res_prices = $bron->get_resources_sum();
					$res_sum += $res_prices[$cur];
				}
			}
			foreach($rooms as $rid => $room)
			{
				$row["room".$rid] = $roomdata[$rid];
				$room_sums[$rid] += $roomdata[$rid];
			}

			$row["packages"] = $package_sum;
			$packages_sum += $package_sum;

			$total = 0;
			$row["rooms"] = $room_sum;
			$rooms_sum += $room_sum;
			$total += $room_sum;

			$product_sum = 0;
			$prods = $o->meta("prods");
			foreach($prods as $prod)
			{
				if($prod["amount"])
				{
					$price = $prod["amount"] * $prod["price"];
					if($prod["discount"])
					{
						$price = round($price - $price * $prod["discount"] / 100, 2);
					}
					$product_sum += $price;
				}
			}
			$row["catering"] = $product_sum;
			$catering_sum += $product_sum;
			$total += $product_sum;

			$row["resources"] = $res_sum;
			$resources_sum += $res_sum;
			$total += $res_sum;

			$housing = $o->meta("housing");
			$sum = 0;
			foreach($housing as $h)
			{
				$sum += $h["sum"];
			}
			$row["housing"] = $sum;
			$housing_sum += $sum;
			$total += $housing_sum;

			$services = $o->get_additional_services();
			$sum = 0;
			foreach($services as $s)
			{
				$sum += $s["sum"];
			}
			$row["additional_services"] = $sum;
			$additional_services_sum = $sum;
			$total += $sum;
			
			$row["total"] = $total;
			$total_sum += $total;

			$row["event_type"] = $o->prop("data_mf_event_type.name");
			$statuses = $o->instance()->get_rfp_statuses();
			$row["status"] = $statuses[$o->prop("confirmed")];
			$data[] = $row;
		}
		$sums = reset($data);
		foreach($sums as $var => $val)
		{
			if(isset(${$var."_sum"}))
			{
				$sums[$var] = ${$var."_sum"};
			}
			else
			{
				$sums[$var] = "";
			}
		}
		foreach($room_sums as $rid => $sum)
		{
			$sums["room".$rid] = $sum;
		}
		$sums["evt_start"] = t("Kokku");
		$data = array_merge($data, array($sums));
		$this->stats_data = $data;
		return $data;
	}

	/** For internal use, removes prices from packages
		@attrib name=remove_prices params=name all_args=1
	 **/
	function remove_prices($arr)
	{
		if($this->can("view", $arr["id"]))
		{
			$rfp_man = obj($arr["id"]);
			$pck = $rfp_man->get_packages();
			foreach($arr["sel"] as $meta => $room_prices)
			{
				foreach(array_keys($room_prices) as $room_price)
				{
					unset($pck[$meta]["prices"][$room_price]);
					$room_price = obj($room_price);
					$room_price->delete();
				}
			}
			$rfp_man->set_packages($pck);
			$rfp_man->save();
		}
		return $arr["post_ru"];
	}

	/** For internal use, archives prices (deactivates them)
		@attrib name=archive_prices params=name all_args=1
	 **/
	function archive_prices($arr)
	{
		foreach($arr["sel"] as $meta => $room_prices)
		{
			foreach(array_keys($room_prices) as $room_price)
			{
				$room_price = obj($room_price);
				$room_price->set_status(1);
				$room_price->save();
			}
		}
		return $arr["post_ru"];
	}

	/** Used for outputting search results for printing
		@attrib params=name all_args=1 name=search_result_print_output
	 **/
	function search_result_print_output($arr)
	{
		foreach($arr as $k => $v)
		{
			if(substr($k, 0, 15) == "raports_search_")
			{
				$arr["request"][$k] = $v;
			}
		}
		$arr["set_filters"] = true;
		$arr["with_header_and_footer"] = true;
		$this->_get_raports_table($arr);
		$print = "<script language=javascript>window.print();</script>";
		die($arr["prop"]["value"].$print);
	}

	/** Used for exporting search results to pdf
		@attrib params=name all_args=1 name=search_result_export_pdf
	 **/
	function search_result_export_pdf($arr)
	{
		foreach($arr as $k => $v)
		{
			if(substr($k, 0, 15) == "raports_search_")
			{
				$arr["request"][$k] = $v;
			}
		}
		$arr["set_filters"] = true;
		$arr["with_header_and_footer"] = true;
		$this->_get_raports_table($arr);
		$pdf_gen = get_instance("core/converters/html2pdf");
		die($pdf_gen->gen_pdf(array(
			"filename" => t("Raportid"),
			"source" => $arr["prop"]["value"],
			"landscape" => true,
		)));
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function do_db_upgrade($t, $f, $err, $sql)
	{
		$fields = array(
			"city_folder" => "int",
			"hotels_folder" => "int",
		);
		
		if($t == "aw_rfp_manager" && !$f)
		{
			$this->db_query("CREATE TABLE aw_rfp_manager (`aw_oid` int primary key)");
			return true;
		}
		if($t and $f)
		{
			foreach($fields as $field => $type)
			{
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => $type
				));
				return true;
			}
		}
	}
}
?>
