<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/rfp.aw,v 1.191 2009/08/24 08:54:38 instrumental Exp $
// rfp.aw - Pakkumise saamise palve 
/*

@classinfo syslog_type=ST_RFP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo allow_rte=2


@tableinfo rfp index=aw_oid master_index=brother_of master_table=objects


@default table=objects
@default group=general

	@property conference_planner type=relpicker reltype=RELTYPE_WEBFORM field=meta method=serialize
	@caption Tellimuse vorm

	@property confirmed type=select table=rfp field=confirmed
	@caption Staatus

	@property from_planner type=hidden field=meta method=serialize

@default table=rfp
@default group=general

	@groupinfo data caption="Andmed"

		@groupinfo submitter_info caption="Tellija kontaktandmed" parent=data
		@default group=submitter_info

			@property data_subm_organisation type=textbox group=submitter_info,final_client parent=client_info autocomplete_class_id=129 option_is_tuple=1
			@caption Organisatsioon

			@property data_subm_name type=textbox group=submitter_info,final_client parent=client_info autocomplete_class_id=145 option_is_tuple=1
			@caption Tellija kontaktisik

			@property data_subm_organizer type=textbox group=submitter_info,final_client parent=client_info autocomplete_class_id=145,129 option_is_tuple=1
			@caption Organisaator

			@property data_subm_street type=textbox group=submitter_info,final_client parent=client_info
			@caption T&auml;nav

			@property data_subm_city type=textbox group=submitter_info,final_client parent=client_info
			@caption Linn

			@property data_subm_zip type=textbox group=submitter_info,final_client parent=client_info
			@caption Indeks

			@property data_subm_state type=textbox group=submitter_info,final_client parent=client_info
			@caption Maakond

			@property data_subm_country type=textbox group=submitter_info,final_client parent=client_info
			@caption Riik
			
			@property data_subm_phone type=textbox group=submitter_info,final_client parent=client_info
			@caption Telefon

			@property data_subm_fax type=textbox group=submitter_info,final_client parent=client_info
			@caption Faks

			@property data_subm_email type=textbox group=submitter_info,final_client parent=client_info
			@caption E-mail
			

			@property data_subm_contact_preference type=relpicker reltype=RELTYPE_PREFERENCE group=submitter_info,final_client parent=client_info
			@caption Kontakteerumise eelistus

		@groupinfo general_function_info caption="&Uuml;ldine &uuml;rituse info" parent=data
		@default group=general_function_info

			@property data_gen_package type=relpicker reltype=RELTYPE_PACKAGE group=general_function_info,final_general
			@caption Pakett

			@property data_gen_package_price type=select group=general_function_info,final_general
			@caption Paketi hind

			@property data_gen_function_name type=textbox group=general_function_info,final_general
			@caption &Uuml;rituse nimi

			@property data_gen_attendees_no type=textbox group=general_function_info,final_general
			@caption Osalejate arv kokku

			@property data_gen_response_date type=hidden table=objects field=meta method=serialize
			@caption Tagasiside aeg

			@property data_gen_decision_date type=hidden table=objects field=meta method=serialize
			@caption Otsuse aeg

			@property data_gen_response_date_admin type=datetime_select
			@caption Tagasiside aeg

			@property data_gen_decision_date_admin type=datetime_select
			@caption Otsuse aeg

			@property data_gen_arrival_date type=hidden table=objects field=meta method=serialize
			@caption Saabumise aeg

			@property data_gen_departure_date type=hidden table=objects field=meta method=serialize
			@caption Lahkumise aeg

			@property data_gen_arrival_date_admin type=datetime_select group=general_function_info,final_general
			@caption Saabumise aeg

			@property data_gen_departure_date_admin type=datetime_select group=general_function_info,final_general
			@caption Lahkumise aeg

			@property data_gen_open_for_alternative_dates type=checkbox ch_value=1 default=0
			@caption N&ouml;us alternatiivsete kuup&auml;evadega

			@property data_gen_accommodation_requirements type=checkbox ch_value=1 default=0
			@caption Majutusvajadused

			@property data_gen_multi_day type=textbox
			@caption &Uuml;rituse kestus

			@property data_gen_single_rooms type=textbox
			@caption &Uuml;hekohalised toad

			@property data_gen_double_rooms type=textbox
			@caption Kahekohalised toad

			@property data_gen_suites type=textbox
			@caption Sviidid

			@property data_gen_acc_start type=hidden table=objects field=meta method=serialize
			@caption Majutuse algusaeg

			@property data_gen_acc_end type=hidden table=objects field=meta method=serialize
			@caption Majutuse l&otilde;puaeg

			@property data_gen_acc_start_admin type=datetime_select
			@caption Majutuse algusaeg

			@property data_gen_acc_end_admin type=datetime_select
			@caption Majutuse l&otilde;puaeg

			@property data_gen_dates_are_flexible type=checkbox ch_value=1 default=0
			@caption Kuup&auml;evad on paindlikud
			
			@property data_gen_meeting_pattern type=hidden
			@caption Kuup&auml;evade muster

			@property data_gen_date_comments type=textbox
			@caption Kuup&auml;evade kommentaar

			@property data_gen_city type=relpicker field=data_gen_city reltype=RELTYPE_TOWN
			@caption Soovitud linn

			@property archived type=checkbox ch_value=1 default=0
			@caption Arhiveeritud

			@property urgent type=checkbox ch_value=1 default=0
			@caption Kiire

			@property data_gen_alternative_dates type=hidden
			@caption Alternatiivsed kuup&auml;evad

		@groupinfo main_fun caption="P&otilde;hi&uuml;ritus" parent=data
		@default group=main_fun

			@property data_mf_table type=textbox
			@caption Pea&uuml;ritus

			@property data_mf_event_type type=relpicker reltype=RELTYPE_EVENT_TYPE store=connect group=main_fun,final_general
			@caption &Uuml;rituse t&uuml;&uuml;p

			@property data_mf_table_form type=relpicker reltype=RELTYPE_TABLES group=main_fun,final_general
			@caption Laudade asetus

			@property data_mf_tech type=textbox
			@caption Tehniline varustus

			@property data_mf_additional_tech type=textbox
			@caption Tehnilise varustuse erisoov

			@property data_mf_additional_decorations type=textbox
			@caption Dekoratsioonid

			@property data_mf_additional_entertainment type=textbox
			@caption Meelelahutus

			@property data_mf_additional_catering type=textbox
			@caption Erisoovid toitlustuse kohta

			@property data_mf_breakout_rooms type=checkbox ch_value=1 default=0
			@caption Puhkeruumide soov

			@property data_mf_breakout_room_setup type=textbox
			@caption Puhkeruumide asetus

			@property data_mf_breakout_room_additional_tech type=textbox
			@caption Puhkeruumide eri tehnikavajadused

			@property data_mf_door_sign type=textbox
			@caption Uksesilt

			@property data_mf_attendees_no type=textbox
			@caption Osalejate arv

			@property data_mf_start_date type=hidden field=meta method=serialize table=objects
			@caption Algusaeg

			@property data_mf_end_date type=hidden field=meta method=serialize table=objects
			@caption L&otilde;puaeg

			@property data_mf_start_date_admin type=datetime_select
			@caption Algusaeg

			@property data_mf_end_date_admin type=datetime_select
			@caption L&otilde;puaeg

			@property data_mf_24h type=textbox
			@caption Hoia 24 tundi kinni

			@property data_mf_catering type=text group=main_fun
			@caption Pea&uuml;rituse toitlustus
			
			@property data_mf_catering_type type=textbox
			@caption Pea&uuml;rituse toitlustuse t&uuml;&uuml;p
			
			@property data_mf_catering_attendees_no type=textbox
			@caption Pea&uuml;rituse toitlustuse osalejate arv

			@property data_mf_catering_start type=hidden field=meta method=serialize table=objects
			@caption Pea&uuml;rituse toitlustuse algusaeg

			@property data_mf_catering_end type=hidden field=meta method=serialize table=objects
			@caption Pea&uuml;rituse toitlustuse l&otilde;puaeg

			@property data_mf_catering_start_admin type=datetime_select
			@caption Pea&uuml;rituse toitlustuse algusaeg

			@property data_mf_catering_end_admin type=datetime_select
			@caption Pea&uuml;rituse toitlustuse l&otilde;puaeg

		@groupinfo billing caption="Arve info" parent=data
		@default group=billing,final_client
			
			@property data_billing_company type=textbox parent=billing_info autocomplete_class_id=129 option_is_tuple=1
			@caption Organisatsioon

			@property data_billing_contact type=textbox parent=billing_info autocomplete_class_id=145 option_is_tuple=1
			@caption Kontaktisik

			@property data_billing_street type=textbox parent=billing_info
			@caption T&auml;nav

			@property data_billing_city type=textbox parent=billing_info
			@caption Linn

			@property data_billing_zip type=textbox parent=billing_info
			@caption Indeks

			@property data_billing_state type=textbox parent=billing_info
			@caption Maakond

			@property data_billing_country type=textbox parent=billing_info
			@caption Riik

			@property data_billing_name type=hidden parent=billing_info group=billing
			@caption Nimi

			@property data_billing_phone type=textbox parent=billing_info
			@caption Telefon

			@property data_billing_fax type=textbox parent=billing_info
			@caption Faks

			@property data_billing_email type=textbox parent=billing_info
			@caption E-mail

			@property data_billing_comment type=textarea rows=4 parent=billing_info
			@caption Kommentaar

		@groupinfo files caption="Failid" parent=data
		@default group=files
			@property files_tb type=toolbar store=no no_caption=1
			
			@property files_tbl type=table store=no no_caption=1


	@groupinfo final_info caption="Tellimuskirjeldus"
		
		@groupinfo final_general caption="&Uuml;ldine" parent=final_info
		@default group=final_general

			@property default_currency type=relpicker reltype=RELTYPE_DEFAULT_CURRENCY store=connect
			@caption Valuuta

			@property default_language type=select table=rfp field=default_language
			@caption Keel

			@property final_rooms type=relpicker multiple=1 reltype=RELTYPE_ROOM table=objects field=meta method=serialize
			@caption Ruumid
			@comment Konverentsi jaoks kasutatavad ruumid

			@property final_catering_rooms type=relpicker multiple=1 reltype=RELTYPE_CATERING_ROOM table=objects field=meta method=serialize
			@caption Toitlustuse ruumid
			@comment Toitlustuse jaoks kasutatavad ruumid

			@property data_gen_hotel type=relpicker field=data_gen_hotel reltype=RELTYPE_LOCATION
			@caption Soovitud hotell

			@property final_theme type=relpicker reltype=RELTYPE_THEME
			@caption Teema
			@comment Konverentsi valdkond(&uml;ldteema)

			@property final_international type=checkbox ch_value=1 default=0
			@caption Rahvusvaheline
			@comment Kas &uuml;ritus on rahvusvaheline

			@property final_native_guests type=textbox
			@caption Kohalike k&uuml;laliste arv
			@comment Konverentsil viibivate kohalike k&uuml;laliste arv

			@property final_foreign_guests type=textbox
			@caption V&auml;lisk&uuml;laliste arv
			@comment Konverentsil viibivate v&auml;lisk&uuml;aliste arv

			@property final_foreign_countries type=relpicker reltype=RELTYPE_COUNTRY store=connect multiple=1 size=3 store=connect
			@caption V&auml;lisriigid

			@property additional_information type=textarea rows=20
			@caption Lisainfo

			@property additional_admin_information type=textarea rows=20
			@caption Administraatori lisainfo

		@groupinfo final_client caption="Klient" parent=final_info
		@default group=final_client
			@layout client_hsplit type=hbox width=50%:50%

				@layout client_info type=vbox closeable=1 area_caption=Tellija&nbsp;kontaktandmed parent=client_hsplit
		
				@layout billing_info type=vbox closeable=1 area_caption=Arve&nbsp;info parent=client_hsplit

		@groupinfo final_prices caption="Ruumid" parent=final_info
		@default group=final_prices

			@property final_add_reservation_tb group=final_prices,final_resource,final_catering no_caption=1 type=toolbar


			@layout prs_hsplit type=hbox width=30%:70%

				@layout prs_left parent=prs_hsplit type=vbox closeable=1 area_caption=Ruumid&nbsp;ja&nbsp;reserveeringud
					@property prices_tree parent=prs_left type=treeview store=no no_caption=1

				@layout prs_right parent=prs_hsplit type=vbox closeable=1 area_caption=Hinnad
					@property prices_tbl parent=prs_right type=text store=no no_caption=1

			@layout add_inf_room type=vbox closeable=1 area_caption="Lisainfo"
				
				@property additional_room_information type=textarea parent=add_inf_room rows=7 cols=100 no_caption=1
				@caption Ruumide lisainfo


		@groupinfo final_catering caption="Toitlustus" parent=final_info
		@default group=final_catering

			@layout cat_hsplit type=hbox width=25%:75%

				@layout cat_leftsplit parent=cat_hsplit type=vbox

					@layout cat_left parent=cat_leftsplit type=vbox closeable=1 area_caption=Ruumid&nbsp;ja&nbsp;reserveeringud
						@property products_tree parent=cat_left type=treeview store=no no_caption=1

					@layout cat_left2 parent=cat_leftsplit type=vbox closeable=1 area_caption=Reserveeringute&nbsp;lisamine

						@property add_catering_bron type=text store=no parent=cat_left2 no_caption=1

						@property add_catering_sbt type=submit store=no parent=cat_left2 no_caption=1
						@caption Lisa

				@layout cat_rightsplit parent=cat_hsplit type=vbox

					@layout cat_right_top parent=cat_rightsplit type=vbox closeable=1 area_caption=Reserveeringute&nbsp;lisamine
	
						@property products_add_bron_tbl parent=cat_right_top type=table store=no no_caption=1

					@layout cat_right parent=cat_rightsplit type=vbox closeable=1 area_caption=Tooted
	
						@property products_tbl parent=cat_right type=text store=no no_caption=1
		
			@layout add_inf_catering type=vbox closeable=1 area_caption="Lisainfo"
				
				@property additional_catering_information type=textarea parent=add_inf_catering rows=7 cols=100 no_caption=1
				@caption Toitlustuse lisainfo




		@groupinfo final_resource caption="Ressursid" parent=final_info
		@default group=final_resource

			@layout res_hsplit type=hbox width=30%:70%

				@layout res_left parent=res_hsplit type=vbox closeable=1 area_caption=Ruumid&nbsp;ja&nbsp;reserveeringud
					@property resources_tree parent=res_left type=treeview store=no no_caption=1

				@layout res_right parent=res_hsplit type=vbox closeable=1 area_caption=Ressursid
					@property resources_tbl parent=res_right type=table store=no no_caption=1

			@layout add_inf_resource type=vbox closeable=1 area_caption="Lisainfo"
				
				@property additional_resource_information type=textarea parent=add_inf_resource rows=7 cols=100 no_caption=1
				@caption Ressursid lisainfo
					

                @groupinfo final_housing caption="Majutus" parent=final_info
                @default group=final_housing

			@property housing_tb type=toolbar store=no no_caption=1

                        @property housing_tbl type=table store=no no_caption=1

			@layout add_inf_housing type=vbox closeable=1 area_caption="Lisainfo"
				
				@property additional_housing_information type=textarea parent=add_inf_housing rows=7 cols=100 no_caption=1
				@caption Majutuse lisainfo


                @groupinfo additional_services caption="Lisateenused" parent=final_info
                @default group=additional_services

			@property services_tb type=toolbar store=no no_caption=1

                        @property additional_services_tbl type=table store=no no_caption=1

			@layout add_inf_services type=vbox closeable=1 area_caption="Lisainfo"
				
				@property additional_services_information type=textarea parent=add_inf_services rows=7 cols=100 no_caption=1
				@caption Lisateenuste lisainfo


		@groupinfo terms caption="Tingimused" parent=final_info
		@default group=terms
'
			@property show_payment_terms type=checkbox ch_value=1 default=1
			@caption Kuva konverentside tingimusi

			@property cancel_and_payment_terms type=textarea richtext=1
			@caption Konverentside annuleerimis- ja maksetingimused

			@property show_acommondation_terms type=checkbox ch_value=1 default=1
			@caption Kuva majutuse tingimusi

			@property accomondation_terms type=textarea richtext=1
			@caption Majutuse annuleerimis- ja maksetingimused


		@groupinfo final_offer caption="Pakkumine" parent=final_info
		@default group=final_offer
	
			@property offer_pdf type=text store=no
			@caption Lae PDF

			@property offer_expire_date type=date_select
			@caption Pakkumise aegumist&auml;htaeg

			@property offer_price_comment type=textarea cols=70 rows=5
			@caption Hinna kommentaar

			@property offer_preface type=textarea cols=70 rows=20
			@caption Pakkumise eess&otilde;na
			
		@groupinfo final_submission caption="Kinnitamine" parent=final_info
		@default group=final_submission

			@property data_contactperson type=textbox table=objects field=meta method=serialize
			@caption Meie kontaktisik

			@property data_send_date type=date_select table=objects field=meta method=serialize
			@caption Saatmise kuup&auml;ev

			@property data_pointer_text type=textarea rows=3 table=objects field=meta method=serialize
			@caption Suunaviidad

			@property data_payment_method type=textbox table=objects field=meta method=serialize
			@caption Maksmisviis

			@property pdf type=text store=no
			@caption Lae PDF

			@property submission type=text no_caption=1 store=no
	
#reltypes

@reltype ROOM clid=CL_ROOM value=1
@caption Konverentsi toimumiskoht

@reltype CATERING_ROOM clid=CL_ROOM value=10
@caption Konveretsi toitlustuse ruumid

@reltype WEBFORM clid=CL_CONFERENCE_PLANNING value=2
@caption Tellimuse vorm

@reltype RESERVATION clid=CL_RESERVATION value=3
@caption Ruumi broneering

@reltype TOWN clid=CL_META value=4
@caption Linn

@reltype LOCATION clid=CL_LOCATION value=5
@caption Asukoht

@reltype PREFERENCE clid=CL_META value=6
@caption Kontakti eelistus

@reltype PACKAGE clid=CL_META value=7
@caption Pakett

@reltype EVENT_TYPE clid=CL_META value=8
@caption S&uuml;ndmuse t&uuml;&uuml;p

@reltype TABLES clid=CL_META value=9
@caption Laudade paigutus

@reltype CATERING_RESERVATION clid=CL_RESERVATION value=12
@caption Toitlustuse broneering

@reltype DEFAULT_CURRENCY clid=CL_CURRENCY value=11
@caption Arvutuste vaikevaluuta

@reltype THEME clid=CL_META value=13
@caption Konverentsi teema

@reltype DATA_SUBM_NAME clid=CL_CRM_PERSON value=14
@caption Tellija kontaktisik

@reltype DATA_SUBM_ORGANISATION clid=CL_CRM_COMPANY value=15
@caption Organisatsioon

@reltype DATA_SUBM_ORGANIZER clid=CL_CRM_PERSON,CL_CRM_COMPANY value=16
@caption Organisaator

@reltype DATA_BILLING_COMPANY clid=CL_CRM_COMPANY value=17
@caption Arve saaja organisatsioon

@reltype DATA_BILLING_CONTACT clid=CL_CRM_PERSON value=18
@caption Arve saaja kontaktisik

@reltype COUNTRY clid=CL_CRM_COUNTRY value=19
@caption Riik
*/

define("RFP_STATUS_SENT", 1);
define("RFP_STATUS_CONFIRMED", 2);
define("RFP_STATUS_ON_HOLD", 3);
define("RFP_STATUS_REJECTED", 4);
define("RFP_STATUS_CANCELLED", 5);

class rfp extends class_base
{
	function rfp()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/rfp",
			"clid" => CL_RFP
		));

		$this->rfp_status = array(
			1 => t("Saadetud"),
			2 => t("Kinnitatud"),
			3 => t("T&auml;psustamisel"),
			4 => t("Tagasi l&uuml;katud"),
			5 => t("T&uuml;histatud"),
		);
		$rfpm = get_instance(CL_RFP_MANAGER);
		$this->rfpm = obj($rfpm->get_sysdefault());

		$this->prop_to_relclid = array(
			"data_subm_name" => CL_CRM_PERSON,
			"data_subm_organisation" => CL_CRM_COMPANY,
			"data_subm_organizer" =>  CL_CRM_PERSON,
			"data_billing_company" =>  CL_CRM_COMPANY,
			"data_billing_contact" =>  CL_CRM_PERSON,
		);
	}

	private function date_to_stamp($date)
	{
		$day = explode(".", $date["date"]);
		$time = explode(":", $date["time"]);
		$stamp = mktime($time[0], $time[1], 0, $day[1], $day[0], $day[2]);
		return $stamp;
	}

	private function arr_to_date($date)
	{
		$return["date"] = (is_numeric($date["day"])?$date["day"]:0).".".(is_numeric($date["month"])?$date["month"]:0).".".(is_numeric($date["year"])?$date["year"]:0);
		$return["time"] = $date["hour"].":".$date["minute"];
		return $return;
	}

	function get_property($arr)
	{
		//$this->db_query("DROP TABLE `rfp`");die();
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		/*$ignored_props = array(
			// these are just numeric values, that can't be parsed as an oid
			"data_gen_single_rooms",
			"data_gen_double_rooms",
			"data_gen_suites",
			"data_gen_attendees_no",
			
			// these are props that need special handlig below..
			"data_mf_event_type",
			"data_mf_catering",
			"data_gen_accommondation_requirements",
		);
		if(substr($prop["name"], 0, 5) == "data_" && !in_array($prop["name"], $ignored_props))
		{
			$prop["value"] = $this->_gen_prop_autom_value($prop["value"]);
			if(trim($prop["value"]) == "")
			{
				//return PROP_IGNORE;
			}
			return $retval;
		}*/
		
		// this here deals with props with values to table
		$prop["name"] = (strstr($prop["name"], "ign_") && !strstr($prop["name"], "foreign"))?substr($prop["name"], 4):$prop["name"];
		$props_to_map = array(
			"data_gen_arrival_date_admin" => "data_gen_acc_start_admin",
			"data_gen_departure_date_admin" => "data_gen_acc_end_admin",
			"data_gen_arrival_date" => "data_gen_acc_start",
			"data_gen_departure_date" => "data_gen_acc_end",

			"data_mf_catering_start_admin" => "data_gen_arrival_date_admin",
			"data_mf_catering_start" => "data_gen_arrival_date",
			"data_mf_catering_end_admin" => "data_gen_departure_date_admin",
			"data_mf_catering_end" => "data_gen_departure_date",

			"data_mf_start_date_admin" => "data_gen_arrival_date_admin",
			"data_mf_start_date" => "data_gen_arrival_date",
			"data_mf_end_date_admin" => "data_gen_departure_date_admin",
			"data_mf_end_date" => "data_gen_departure_date",
		);
		switch($prop["name"])
		{
			case "data_subm_name":
			case "data_subm_organisation":
				$prop["ac_filter"] = array("parent" => $this->rfpm->prop("clients_folder"));
			case "data_subm_organizer":
				$prop["selected"] = array(
					$prop["value"] => $arr["obj_inst"]->prop($prop["name"].".name"),
				);
				break;
			case "data_gen_city":
				if($_t = $this->rfpm->prop("hotels"))
				{
					if(is_array($_t) && count($_t))
					{
						$list = new object_list(array(
							"class_id" => CL_LOCATION,
							"oid" => $_t,
						));
						foreach($list->arr() as $obj)
						{
							$cid = $obj->prop("address.linn");
							if($this->can("view", $cid))
							{
								$prop["options"][$cid] = obj($cid)->name();
							}
						}
					}
				}
				break;
			case "data_gen_hotel":
				if($_t = $this->rfpm->prop("hotels"))
				{
					if(is_array($_t) && count($_t))
					{
						$list = new object_list(array(
							"class_id" => CL_LOCATION,
							"oid" => $_t,
						));
						foreach($list->arr() as $obj)
						{
							$prop["options"][$obj->id()] = $obj->name();
						}
					}
				}
				break;
	
			case "final_foreign_countries":
				if($cf = $this->rfpm->prop("country_folder"))
				{
					$ol = new object_list(array(
						"class_id" => CL_CRM_COUNTRY,
						"parent" => $cf,
						"sort_by" => "objects.name asc",
					));
					foreach($ol->arr() as $oid => $o)
					{
						$prop["options"][$oid] = $o->name();
					}
				}
				break;

			case "data_subm_contact_preference":
				foreach($this->rfpm->get_contact_preferences() as $oid => $obj)
				{
					$prop["options"][$oid] = $obj->trans_get_val("name");
				}
				break;
			case "data_gen_package":
				$prc = $this->rfpm->get_packages();
				foreach($prc as $rp => $prices)
				{
					$rp = obj($rp);
					$prop["options"][$rp->id()] = $rp->name();
				}
				break;

			case "data_gen_package_price":
				if(!$arr["obj_inst"]->prop("data_gen_package"))
				{
					return PROP_IGNORE;
				}
				$prc = $this->rfpm->get_packages();
				$dc = obj($this->rfpm->prop("default_currency"));
				$prop["options"][] = t("-- Vali --");
				foreach($prc[$arr["obj_inst"]->prop("data_gen_package")]["prices"] as $rp => $prices)
				{
					$rp = obj($rp);
					$prop["options"][$rp->id()] = $rp->name()." (".$prices[$dc->id()]." ".$dc->name().")";
				}
				if(!$prop["options"][$prop["value"]] && $this->can("view", $prop["value"]))
				{
					$prop["options"][$prop["value"]] = obj($prop["value"])->name();
				}
				break;
			case "data_gen_arrival_date_admin":
			case "data_gen_departure_date_admin":
			case "data_gen_arrival_date":
			case "data_gen_departure_date":
			case "data_mf_catering_start_admin":
			case "data_mf_catering_start":
			case "data_mf_catering_end_admin":
			case "data_mf_catering_end":
			case "data_mf_start_date_admin":
			case "data_mf_start_date":
			case "data_mf_end_date_admin":
			case "data_mf_end_date":
				if(!$prop["value"])
				{
					$prop["value"] = $arr["obj_inst"]->prop($props_to_map[$prop["name"]]);
				}
				break;
			
			
			case "data_billing_company":
				if(!$prop["value"])
				{
					$prop["value"] = $arr["obj_inst"]->prop("data_subm_organisation");
				}
				$prop["selected"] = array(
					$prop["value"] => $arr["obj_inst"]->prop($prop["name"].".name"),
				);
				$prop["ac_filter"] = array("parent" => $this->rfpm->prop("clients_folder"));
				break;
			case "data_billing_contact":
				if(!$prop["value"])
				{
					$prop["value"] = $arr["obj_inst"]->prop("data_subm_name");
				}
				$prop["selected"] = array(
					$prop["value"] => $arr["obj_inst"]->prop($prop["name"].".name"),
				);
				$prop["ac_filter"] = array("parent" => $this->rfpm->prop("clients_folder"));
				break;
			case "data_billing_phone":
			case "data_billing_email":
			case "data_billing_street":
			case "data_billing_city":
			case "data_billing_country":
			case "data_billing_fax":
			case "data_billing_state":
			case "data_billing_zip":
				if(!$prop["value"])
				{
					$prop["value"] = $arr["obj_inst"]->prop("data_subm".substr($prop["name"], 12));
				}
				break;

			case "data_contactperson":
				if(!$prop["value"])
				{
					$prop["value"] = obj(aw_global_get("uid_oid"))->get_first_obj_by_reltype("RELTYPE_PERSON")->name();
				}
				break;

			case "cancel_and_payment_terms":
			case "accomondation_terms":
				$prop["value"] = $arr["obj_inst"]->prop($prop["name"]);
				break;
			case "data_mf_table_form":
				$rfpm = get_instance(CL_RFP_MANAGER);
				$obj = obj($rfpm->get_sysdefault());
				if($this->can("view", $obj->prop("table_form_folder")))
				{
					$ol = new object_list(array(
						"class_id" => CL_META,
						"parent" => $obj->prop("table_form_folder")
					));
					foreach($ol->arr() as $obj)
					{
						$prop["options"][$obj->id()] = $obj->name();
					} 
				}
				break;
			case "final_theme":
				$rfpm = get_instance(CL_RFP_MANAGER);
				$obj = obj($rfpm->get_sysdefault());
				if($this->can("view", $obj->prop("theme_folder")))
				{
					$ol = new object_list(array(
						"class_id" => CL_META,
						"parent" => $obj->prop("theme_folder")
					));
					foreach($ol->arr() as $obj)
					{
						$prop["options"][$obj->id()] = $obj->name();
					} 
				}
				break;
			case "conference_planner":
				$rfpm = get_instance(CL_RFP_MANAGER);
				$obj = obj($rfpm->get_sysdefault());
				if(!$prop["value"] && $this->can("view", $obj->prop("default_conference_planner")))
				{
					$arr["obj_inst"]->connect(array(
						"type" => "RELTYPE_WEBFORM",
						"to" => $obj->prop("default_conference_planner"),
					));
					$prop["options"][$obj->prop("default_conference_planner")] = obj($obj->prop("default_conference_planner"))->name();
					$prop["value"] = $obj->prop("default_conference_planner");
				}
				break;
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
				$rfpm = get_instance(CL_RFP_MANAGER);
				$obj = obj($rfpm->get_sysdefault());
				if(!$prop["value"])
				{
					$prop["value"] = $obj->prop("default_language");
				}
			break;
			case "confirmed":
				$prop["options"] = $this->get_rfp_statuses();
			break;
			case "cancel_and_payment_terms":
			case "accomondation_terms":
				if($prop["value"] == "")
				{
					$inst = get_instance(CL_RFP_MANAGER);
					$rfpm = $inst->get_sysdefault();
					$rfpmo = obj($rfpm);
					$prop["value"] = $rfpmo->prop($prop["name"]);
				}
				break;
			case "default_currency":
				if($prop["value"] == "")
				{
					$inst = get_instance(CL_RFP_MANAGER);
					$rfpm = $inst->get_sysdefault();
					$rfpmo = obj($rfpm);
					$cur = obj($rfpmo->prop($prop["name"]));
					$prop["options"] = array(
						$cur->id() => $cur->name(),
					);
					$prop["selected"] = $cur->id();
				}
				$ol = new object_list(array(
					"class_id" => CL_CURRENCY,
					"lang_id" => array(),
				));
				foreach($ol->arr() as $oid => $o)
				{
					$prop["options"][$oid] = $o->name();
				}
			break;
			case "final_rooms":
			case "final_catering_rooms":
				$prop["selected"] = $arr["obj_inst"]->prop($prop["name"]);
				$type = ($prop["name"] == "final_rooms")?"":"catering_";


				$rfpm = get_instance(CL_RFP_MANAGER);
				$rfpm = $rfpm->get_sysdefault();
				$rfpm = obj($rfpm);
				$conns = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_".strtoupper($type)."ROOM",
				));
				foreach($conns as $conn)
				{
					$to = $conn->to();
					$exist[$to->id()] = $to->id();
				}
				$fun = "get_rooms_from_".$type."room_folder";
				$ol = $rfpm->$fun();
				foreach($ol->arr() as $oid => $o)
				{
					if(in_array($oid, $exist))
					{
						continue;
					}
					$arr["obj_inst"]->connect(array(
						"to" => $oid,
						"type" => "RELTYPE_".strtoupper($type)."ROOM",
					));
					$prop["options"][$oid] = $o->name();
				}
				break;
			// final_data thingies
			case "data_mf_catering_end_admin":
			case "data_mf_catering_start_admin":
			case "data_mf_end_date_admin":
			case "data_mf_start_date_admin":
			case "data_gen_acc_end_admin":
			case "data_gen_acc_start_admin":
			case "data_gen_departure_date_admin":
			case "data_gen_arrival_date_admin":
			case "data_gen_decision_date_admin":
			case "data_gen_response_date_admin":
				//if($prop["value"] < 1)
				//{
					$svar = substr($prop["name"], 0, -6);
					if($ov = $arr["obj_inst"]->prop($svar))
					{
						$prop["value"] = $this->date_to_stamp($ov);
					}
				//}
				break;
			case "final_add_reservation_tb":
				$tb = &$prop["vcl_inst"];
				$tb->add_menu_button(array(
					"name" => "add",
					"img" => "new.gif",
					"tooltip" => t("Reserveering"),
				));
				$rooms = $this->get_rooms($arr);
				$reltypes = array(
					"final_catering" => "RELTYPE_CATERING_RESERVATION",
					"final_prices" => "RELTYPE_RESERVATION",
					"final_resource" => "RELTYPE_RESERVATION",
				);
				
				$data_name = $arr["obj_inst"]->prop("data_subm_name");
				if($this->can("view", $data_name))
				{
					$p = obj($data_name);
					$new_reservation_args["person_rfp_fname"] = $p->prop("firstname");
					$new_reservation_args["person_rfp_lname"] = $p->prop("lastname");
				}
				else
				{
					$data_name = split("[ ]", $data_name); 
					$new_reservation_args["person_rfp_fname"] = $data_name[0];
					unset($data_name[0]);
	                                $new_reservation_args["person_rfp_lname"] = count($data_name)?join(" ", $data_name):"";
				}
				$new_reservation_args["person_rfp_email"] = $arr["obj_inst"]->prop("data_subm_email");
				$new_reservation_args["person_rfp_phone"] = $arr["obj_inst"]->prop("data_subm_phone");
				$new_reservation_args["people_count_rfp"] = $arr["obj_inst"]->prop("data_gen_attendees_no");
				if($arr["obj_inst"]->prop("confirmed") == RFP_STATUS_CONFIRMED)
				{
					$new_reservation_args["ver"] = 1;
				}
				if($arr["request"]["group"] == "final_catering")
				{
					$new_reservation_args["type"] = "food";
				}
				$new_reservation_args["return_url"] = get_ru();
				$new_reservation_args["rfp"] = $arr["obj_inst"]->id();
				$new_reservation_args["rfp_reltype"] = $reltypes[$arr["request"]["group"]];
				$new_reservation_args["rfp_organisation"] = $arr["obj_inst"]->prop("data_subm_organisation");
				// set reservation default time. this depends on a particular tab currently active
				$new_reservation_args["start1"] = (($_t = $arr["obj_inst"]->prop("data_mf_catering_start_admin")) > 1)?$_t:$arr["obj_inst"]->prop("data_gen_arrival_date_admin");
				$new_reservation_args["end"] = (($_t = $arr["obj_inst"]->prop("data_mf_catering_end_admin")) > 1)?$_t:$arr["obj_inst"]->prop("data_gen_departure_date_admin");

				
				foreach($rooms as $room)
				{
					$new_reservation_args["resource"] = $room;
					$new_reservation_args["parent"] = $room;
					$url = $this->mk_my_orb("new", $new_reservation_args, CL_RESERVATION);
					$o = obj($room);
					
					$tb->add_menu_item(array(
						"parent" => "add",
						"text" => sprintf(t("Ruumi '%s'"), $o->name()),
						"url" => $url,
					));
				}
				$tb->add_save_button();
				$tb->add_delete_button();

				$ol = new object_list(array(
					"class_id" => CL_SPA_BOOKINGS_OVERVIEW,
				));
				$o = $ol->begin();
				$spl = split("[ ]", $arr["obj_inst"]->prop("data_subm_name"));
				$_st = $arr["obj_inst"]->prop("data_gen_arrival_date_admin");
				$url = $this->mk_my_orb("show_cals_pop", array(
					//"id" => $o->id(),
					"class" => "spa_bookings_overview",
					"post_msg_after_reservation" => array(
						"rfp_oid" => $arr["obj_inst"]->id(),
						"class_id" => CL_RFP,
						"action" => "handle_new_reservation" 
					),
					"alter_reservation_name" => array(
						"rfp_oid" => $arr["obj_inst"]->id(),
						"class_id" => CL_RFP,
						"action" => "handle_calendar_show_reservation",
						"reltype" => $reltypes[$arr["request"]["group"]],
					),
					"start" => mktime(0,0,0, date("m", $_st), date("d", $_st), date("Y", $_st)),
					"end" => $arr["obj_inst"]->prop("data_gen_departure_date_admin") + 86400,
					"firstname" => $spl[0],
					"lastname" => join(" ", array_slice($spl, 1)),
					"company" => $arr["obj_inst"]->prop("data_subm_organisation"),
					"phone" => $arr["obj_inst"]->prop("data_subm_phone"),
					"people_count" => $arr["obj_inst"]->prop("data_gen_attendees_no"),
					"rooms" => "0",

				));
				$tb->add_button(array(
					"name" => "cal",
					"tooltip" => t("Kalender"),
					"img" => "icon_cal_today.gif",
					"url" => "#",
					"onClick" => "vals='';f=document.changeform.elements;l=f.length;num=0;for(i=0;i<l;i++){ if(f[i].name.indexOf('room_sel') != -1 && f[i].checked || f[i].name=='selected_room_oid') {vals += ','+f[i].value;}};if (vals != '') {aw_popup_scroll('$url'+vals,'mulcal',700,500);} else { alert('".t("Valige palun ruum!")."');} return false;",
				));
				if($this->can("view", $arr["request"]["room_oid"]))
				{
					$tb->add_cdata(html::hidden(array(
						"name" => "selected_room_oid",
						"value" => $arr["request"]["room_oid"],
					)));
				}
				break;
			case "products_tree":
			case "resources_tree":
			case "prices_tree":
				$t = &$prop["vcl_inst"];
				$rooms = $this->get_rooms($arr);
				foreach($rooms as $room)
				{
					if($this->can("view", $room))
					{
						$room_o = obj($room);
						$t->add_item(0, array(
							"id" => "room_".$room,
							"name" => ($room_o->id() == $arr["request"]["room_oid"])?html::strong($room_o->name()):$room_o->name(),
							"url" => aw_url_change_var(array(
								"room_oid" => $room,
								"reservation_oid" => "",
							)),
						));
					}
					
				}
				$reltypes = array(
					"final_catering" => "RELTYPE_CATERING_RESERVATION",
					"final_prices" => "RELTYPE_RESERVATION",
					"final_resource" => "RELTYPE_RESERVATION",
				);
				$conn = $arr["obj_inst"]->connections_from(array(
					"type" => $reltypes[$arr["request"]["group"]],
				));
				foreach($conn as $c)
				{
					$oid = $c->prop("to");
					$obj = obj($oid);
					$room = $obj->prop("resource");
					$date = date("d.m.Y H:i", $obj->prop("start1")). " - " . date("d.m.Y H:i", $obj->prop("end"));
					$t->add_item("room_".$room, array(
						"id" => "reserv_".$oid,
						"name" => ($arr["request"]["reservation_oid"] == $oid)?html::strong($date):$date,
						"url" => aw_url_change_var(array(
							"reservation_oid" => $oid,
							"room_oid" => "",
						)),
					));
				}
				break;
			case "products_tbl":
			case "resources_tbl":
				if($this->can("view", $arr["request"]["reservation_oid"]))
				{
					$obj = obj($arr["request"]["reservation_oid"]);
					$inst = $obj->instance();
					classload("vcl/table");
					$args = array(
						"request" => array(
							"class" => "reservation",
							"action" => "change",
							"id" => $bron,
							"resource_default_prices" => $this->rfpm->get_resource_default_prices(),
						),
						"obj_inst" => $obj,
						"groupinfo" => array(),
						"prop" => array(
							"store" => "no",
							"name" => $arr["prop"]["name"],
							"type" => "table",
							"no_caption" => "1",
							"vcl_inst" => new vcl_table(),
						),
					);
					$function = "_get_".$arr["prop"]["name"];
					$inst->$function(&$args);
					$prop["value"] = $args["prop"]["vcl_inst"]->get_html();
				}
				elseif($prop["name"] == "products_tbl")
				{
					$prop["value"] = $this->get_products_tbl($arr);
				}
				elseif($prop["name"] == "resources_tbl")
				{
					$prop["value"] == $this->get_resources_tbl($arr);
				}
				else
				{
					$prop["value"] = t("Palun valige reserveering");
				}
				break;

			case "prices_tbl":
				classload("vcl/table");
				$args = array(
					"request" => array(
						"class" => "reservation",
						"action" => "change",
						"id" => $bron,
						"default_currency" => $arr["obj_inst"]->prop("default_currency"),
						"define_chooser" => 1,
						//"chooser" => "room",
					),
					"groupinfo" => array(),
					"prop" => array(
						"store" => "no",
						"name" => $arr["prop"]["name"],
						"type" => "table",
						"no_caption" => "1",
						"vcl_inst" => new vcl_table(),
					),
					"rfpo_inst" => $arr["obj_inst"],
				);
				if($this->can("view", $arr["request"]["reservation_oid"]))
				{
					$args["obj_inst"] = obj($arr["request"]["reservation_oid"]);
				}
				else
				{
					if($room = $arr["request"]["room_oid"])
					{
						$rooms[$room] = $room;
					}
					else
					{
						$rooms = $this->get_rooms($arr);
					}
					$conn = $arr["obj_inst"]->connections_from(array(
						"type" => "RELTYPE_RESERVATION",
					));
					foreach($conn as $c)
					{
						if($this->can("view", $c->prop("to")))
						{
							$o = obj($c->prop("to"));
							$room = $o->prop("resource");
							if($rooms[$room])
							{
								$ids[] = $o->id();
							}
						}
					}
					$args["request"]["do_room_separators"] = true;
					$args["request"]["extra_rooms_for_separators"] = $rooms;
					$args["ids"] = $ids;
				}

				// we have to figure out if the reservation is too short/long (considering min & max hours)
				$args["request"]["use_rfp_minmax_hours_pricecalc"] = true;
				$args["request"]["rfp_oid"] = $arr["obj_inst"]->id();
				
				
				$rfpm = get_instance(CL_RFP_MANAGER);
				$rfpm = obj($rfpm->get_sysdefault());
				$extra = $rfpm->get_extra_hours_prices();


				$inst = get_instance(CL_RESERVATION);
				$function = "_get_".$arr["prop"]["name"];
				$inst->$function(&$args);
				$this->_modify_prices_tbl_after(&$arr, &$args["prop"]["vcl_inst"], $inst->prices_tbl_sum_row);
				$prop["value"] = $args["prop"]["vcl_inst"]->get_html();
				break;

			case "tmp4":
				$prop["value"] = "Ruumi hindade/soodustuste & koguhinna/soodustuse m&auml;&auml;ramine";
				break;
			case "tmp5":
				// tmp
				$url  = get_ru();
				$url .= "&pdf=1";
				$pdf = "<a href=\"".$url."\">pdf (kohe &uuml;ldse &uuml;ldse &uuml;ldse ei vungsi)</a><br/><br/>";
				//
				$prop["value"] = $pdf.$this->rfp_reservation_description($arr["obj_inst"]->id(), $arr["request"]["pdf"]?"pdf":"html");
				break;

			// totally new propnames.. gosh

			case "data_gen_accommondation_requirements":
				$prop["value"] = $prop["value"]?1:"";
				break;

			case "data_mf_event_type":
				// wtff???
				$prop["selected"] = $prop["value"];
				$rfpm = get_instance(CL_RFP_MANAGER);
				$def = obj($rfpm->get_sysdefault());
				$types = $def->event_types();
				foreach($types as $type)
				{
					$prop ["options"][$type->id()] = $type->name();
				}
				//$prop["value"] = aw_unserialize($prop["value"]);
			/*case "data_mf_catering_type":
				$prop["value"] = ($prop["value"]["radio"] == 1)?$this->_gen_prop_autom_value($prop["value"]["select"]):$prop["value"]["text"];
				break;*/
				break;

			case "data_mf_catering":
				if(substr($arr["request"]["group"], 0, 5) == "final")
				{
					$prop["no_caption"] = 1;
				}
				$prop["value"] = aw_unserialize($prop["value"]);
				$props = $arr["obj_inst"]->get_property_list();
				classload("vcl/table");
				$t = new aw_table();
				$header = array_keys(reset($prop["value"]));
				foreach($header as $field)
				{
					$t->define_field(array(
						"name" => $field,
						"caption" => $props[$field]["caption"],
					));
				}
				$dummy_arr = $arr;
				unset($dummy_arr["prop"]);
				$dummy_arr["prop"] = $prop;
				foreach($prop["value"] as $data)
				{
					foreach($data as $propname => $value)
					{
						if(is_array($value))
						{
							$oid = $value["select"];
							if($this->can("view", $oid))
							{
								$o = obj($oid);
								$value = $o->name();
							}
						}
						//$data[$propname] = ($value["radio"] == 1)?$this->_gen_prop_autom_value($prop["value"]["select"]):$prop["value"]["text"];
						$dummy_arr["prop"] = array(
							"name" => "ign_".$propname,
							"value" => $value,
						);
						$this->get_property(&$dummy_arr);
						$data[$propname] = $dummy_arr["prop"]["value"];
					}
					$t->define_data($data);
				}
				$prop["value"] = $t->draw();
				break;
			
			case "housing_tb":
			case "services_tb":
				$tb = &$arr["prop"]["vcl_inst"];
				$tb->add_save_button();
				break;

			//-- get_property --//
			case "submitter":
				if(!$prop["value"])
				{
					RETURN PROP_OK;
				}
				$u = get_instance(CL_USER);
				$p = $u->get_person_for_user(obj($prop["value"]));
				$prop["value"] = html::href(array(
					"caption" => call_user_func(array(obj($p), "name")),
					"url" => $this->mk_my_orb("change" ,array(
						"id" => $p,
						"return_url" => get_ru(),
					), CL_CRM_PERSON),
				));
				break;
			case "open_for_alternative_dates":
			case "accommondation_requirements":
			case "needs_rooms":
			case "24h":
				$prop["value"] = ($prop["value"] == 1)?t("Yes"):t("No");
				break;

			case "start_date":
			case "end_date":
				$prop["value"] = date("d.m.Y H:i", $prop["value"]);
				break;
			case "catering_for_main":
				$data = aw_unserialize($prop["value"]);

				classload("vcl/table");
				$t = new vcl_table();
				$t->define_field(array(
					"name" => "catering_type",
					"caption" => t("Type"),
				));
				$t->define_field(array(
					"name" => "start",
					"caption" => t("Start_time"),
				));
				$t->define_field(array(
					"name" => "end",
					"caption" => t("End time"),
				));
				$t->define_field(array(
					"name" => "attendees",
					"caption" => t("Number of attendees"),
				));
				foreach($data as $k => $data)
				{
					$t->define_data(array(
						"catering_type" => $data["type"],
						"start" => date("H:i", $data["start"]),
						"end" => date("H:i", $data["end"]),
						"attendees" => $data["attendees"],
					));
				}
				$prop["value"] = $t->draw();
				break;
			case "additional_dates":
			case "additional_functions":
			case "search_result":
				$data = aw_unserialize($prop["value"]);
				classload("vcl/table");
				$t = new vcl_table();
				$fun = "_gen_table_".$prop["name"];
				$this->$fun($data, &$t);
				$prop["value"] = $t->draw();
				break;
			case "pdf":
			case "offer_pdf":
				$prop["value"] = html::href(array(
					"url" => $this->mk_my_orb("get_pdf_file", array(
						"id" => $arr["obj_inst"]->id(),
						"pdf" => $prop["name"],
					)),
					"caption" => t("Fail"),
				));
				break;
		};
		return $retval;
	}

	function _init_resources_tbl(&$arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "resource",
			"caption" => t("Ressurss"),
			"chgbgcolor" => "split",
		));
		
		$curs = $this->gather_reservation_currencys($arr["obj_inst"]);
		
		foreach($curs as $cur)
		{
			$cur = obj($cur);
			$t->define_field(array(
				"name" => "price[".$cur->id()."]",
				"caption" => $cur->name(),
				"chgbgcolor" => "split",
			));
		}
		$t->define_chooser(array(
			"name" => "sel_resource",
			"field" => "resource_id",
		));
		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Allahindlus %"),
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"chgbgcolor" => "split",
		));

		$t->set_rgroupby(array(
			"reservation" => "reservation",
		));

	}
	
	/** Finds and returns currency oids used by rfp system
		@param obj required
			RFP obj
	 **/
	public function gather_reservation_currencys($obj)
	{
		$cs = $obj->connections_from(array(
			"type" => "RELTYPE_RESERVATION",
		));
		foreach($cs as $c)
		{
			$r = $c->to();
			$ress[$r->id()] = $r;
		}
		return $this->_gather_resources_currencys($ress);
	}
	
	function _gather_resources_currencys($reservations = array())
	{
		$curs = array();
		foreach($reservations as $id => $res)
		{
			$room = obj($res->prop("resource"));
			$curs += $room->prop("currency");
		}
		return array_unique($curs);
	}

	function get_resources_tbl(&$arr)
	{
		$this->_init_resources_tbl($arr);
		$total_price = array();
		$t =& $arr["prop"]["vcl_inst"];
		$conns = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_RESERVATION",
		));

		$currencys_in_use = $this->gather_reservation_currencys($arr["obj_inst"]);
		$rfpm = get_instance(CL_RFP_MANAGER);
		$rfpm = obj($rfpm->get_sysdefault($rfpm));
		//$resource_default_prices = $rfpm->get_resource_default_prices();
		foreach($conns as $c)
		{
			$res = $c->to();
			$res_inst = get_instance(CL_RESERVATION);
			$resources_calculated_price = $res->get_resources_sum();
			$resources_calculated_price_without_special_discount = $res->get_resources_sum(true);

			$resources_data = $res_inst->get_resources_data($res->id());

			$reservation_room = $res->prop("resource");
			$room_inst = get_instance(CL_ROOM);
			$room_resources = $room_inst->get_room_resources($reservation_room);
			// rows for every resource in reservations
			uasort($room_resources, array($this, "__sort_resources"));
			foreach($room_resources as $k => $resource)
			{
				$data = array(
					"resource_room" => $reservation_room,
					"room_resource" => $k,
					"amount" => $resources_data[$k]["count"],
					"resource" => html::obj_change_url($resource),
					"reservation" => html::checkbox(array(
							"name" => "sel_res[".$res->id()."]",
							"value" => $res->id(),
						)).html::obj_change_url($res),
					"discount" => $resources_data[$k]["discount"],
					"comment" => $resources_data[$k]["comment"],
					"time" => date("H:i", $resources_data[$k]["start1"]).t(" - ").date("H:i", $resources_data[$k]["end"]),
					"resource_id" => $resource->id().".".$res->id(),
					/*
					"time" => $this->_gen_time_form(array(
						"varname" => "time[".$k."]",
						"start1" => $resources_data[$k]["start1"],
						"end" => $resources_data[$k]["end"],
					)),
					 */
					//$resources_data[$k]["time"],
				);

				foreach($resources_data[$k]["prices"] as $oid => $price)
				{
					/*
					if($price <= 0 or !$price)
					{
						$price = $resource_default_prices[$res->prop("resource")][$k][$oid];
					}
					*/

					$cur_reservation_price_from_resources[$oid] += $price;
					$data["price[".$oid."]"] = $price;
				}

				$t->define_data($data);
			}
			//totalpricecalc.com
			foreach($currencys_in_use as $cur)
			{
				$total_price[$cur] += $resources_calculated_price[$cur];
			}

			// special row for every resevation
			$data = array(
				"price" => $resources_price[$k],
				"reservation" => html::checkbox(array(
					"name" => "sel_res[".$c->to()->id()."]",
					"value" => $c->to()->id(),
				)).html::obj_change_url($c->to()),
				"resource" => t("Kokku"),
				//"split" => "#CCCCCC",
				"discount" => $resources_discount,
			);
			foreach($resources_calculated_price_without_special_discount as $k => $price)
			{
				$price = $price;
				$data["price[".$k."]"] = $price;
			}
			$t->define_data($data);

		}
		$t->set_sortable(false);
		
		// total-total
		$t->define_data(array(
			"split" => "#CCCCCC",
		));
		$data = array(
			"resource" => html::strong(t("Kokku:")),
		);
		foreach($currencys_in_use as $cur)
		{
			$data["price[".$cur."]"] = $total_price[$cur];
		}
		$t->define_data($data);
	}

	function __sort_resources($a, $b)
	{
		return $a->ord() - $b->ord();
	}

	// well, as far as i can tell, this ins't used any more.. there's a getter method. but i leave it here for a while just in case...
	function update_products_info($rvid, $obj)
	{
		if($this->can("view", $rvid))
		{
			$prods = $obj->meta("prods");
			$rvi = get_instance(CL_RESERVATION);
			$rv = obj($rvid);
			$prod_list = $rvi->get_room_products($rv->prop("resource"));
			$amount = $rv->meta("amount");
			$discount = $rvi->get_product_discount($rv->id());
			foreach($prod_list->arr() as $prod)
			{
				if($count = $amount[$prod->id()])
				{
					$price = $rvi->get_product_price(array("product" => $prod->id(), "reservation" => $rv));
					$disc = $discount[$prod->id()];
					$prod_sum = $price * $count;
					$prod_sum = number_format($prod_sum - ($prod_sum * $disc)/100,2);
					$key = $prod->id().".".$rvid;
					$prods[$key]["price"] = $price;
					$prods[$key]["amount"] = $count;
					$prods[$key]["discount"] = $disc;
					$prods[$key]["sum"] = $prod_sum;
				}
			}
			$obj->set_meta("prods", $prods);
			$obj->save();
		}
	}

	function _get_add_catering_bron($arr)
	{
		$val = array();
		$rooms = $this->get_rooms($arr);
		$prodvars = $this->get_product_vars(true);
		foreach($rooms as $room)
		{
			$ro = obj($room);
			$val[] = $ro->name().":<br />\n".
				html::date_select(array(
					"name" => "add_bron[{$room}][date]",
					"value" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
					"month_as_numbers" => 1,
				))."<br />\n".
				html::select(array(
					"name" => "add_bron[{$room}][count]",
					"value" => -1,
					"options" => range(0, 9),
				)).
				html::select(array(
					"name" => "add_bron[{$room}][var]",
					"value" => -1,
					"options" => $prodvars,
				));
		}
		$arr["prop"]["value"] = implode("<br /><br />\n", $val);
	}

	function _set_add_catering_bron($arr)
	{
		$add_brons = aw_global_get("rfp_add_brons");
		foreach($arr["request"]["add_bron"] as $room => $add)
		{
			if($add["count"])
			{
				$add["room"] = $room;
				$add_brons[] = $add;
			}
		}
		aw_session_set("rfp_add_brons", $add_brons);
	}

	function _get_products_add_bron_tbl($arr)
	{
		if(aw_global_get("rfp_add_brons"))
		{
			$add_brons = aw_global_get("rfp_add_brons");
		}
		elseif($arr["obj_inst"]->meta("pk_catering_set") != $arr["obj_inst"]->prop("data_gen_package") && ($pkid = $arr["obj_inst"]->prop("data_gen_package")) && $this->rfpm)
		{
			$pk_prods = $this->rfpm->meta("pk_prods");
			$pk_data = $pk_prods[$pkid];
			$add_brons = array();
			$s = $arr["obj_inst"]->prop("data_gen_arrival_date_admin");
			$e = $arr["obj_inst"]->prop("data_gen_departure_date_admin");
			$start = mktime(date('H', $s), 0, 0, date('m', $s), date('d', $s), date('Y', $s));
			$end = mktime(date('H', $s), 0, 0, date('m', $e), date('d', $e), date('Y', $e));
			if(($end - $start) < 1)
			{
				$end = $start + 1;
			}
			for($i = $start; $i <= $end; $i += 24 * 60 * 60)
			{
				foreach($pk_data as $pkid => $data)
				{
					$add_brons[] = array(
						"date"  => $i,
						"room" => $data["room"],
						"prod" => $data["prod"],
						"var" => $data["var"],
						"count" => 1,
					);
					$prods = 1;
				}
			}
			$prod_f = $this->rfpm->prop("pk_products_folder");
			if($this->can("view", $prod_f))
			{
				$ol = new object_list(array(
					"parent" => $prod_f,
					"class_id" => CL_MENU,
				));
				$choose_set = false;
				foreach($ol->arr() as $o)
				{
					$p_ol = new object_list(array(
						"parent" => $o->id(),
						"class_id" => CL_SHOP_PRODUCT,
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
				$prodopts["optgnames"] = $flds;
				$prodopts["optgroup"] = $prod;
			}
		}
		if(is_array($add_brons) && count($add_brons))
		{
			$t = &$arr["prop"]["vcl_inst"];
			$this->_init_prod_add_bron_tbl($t, $prods);
			$count = 0;
			$rooms = $this->get_rooms($arr);
			$roomnames[0] = t("--vali--");
			foreach($rooms as $rid)
			{
				$roomnames[$rid] = obj($rid)->name();
			}
			$prod_vars = $this->get_product_vars(true);
			foreach($add_brons as $add)
			{
				for($i = 0; $i < $add["count"]; $i++)
				{
					$count++;
					if($prod)
					{
						$prodhtml = html::select(array(
							"name" => "add_bron_tbl[{$count}][prod]",
							"options" => array(t("--vali--")),
							"optgroup" => $prodopts["optgroup"],
							"optgnames" => $prodopts["optgnames"],
							"size" => 5,
							"value" => $add["prod"],
							"width" => 200,
							"multiple" => 1,
						));
					}
					$time = is_numeric($add["date"]) ? $add["date"] : date_edit::get_timestamp($add["date"]);
					$t->define_data(array(
						"del" => html::checkbox(array(
							"name" => "add_bron_tbl[{$count}][del]",
							"ch_value" => 1,
						)),
						"time" => html::date_select(array(
							"name" => "add_bron_tbl[{$count}][start1]",
							"value" => $time,
							"month_as_numbers" => 1,
						))."<br />\n ".t("Aeg:")."<br />\n".$this->gen_time_form(array(
							"varname" => "add_bron_tbl[{$count}][time]",
							"start1" => $time,
							"end" => $time,
						)),
						"room" => html::select(array(
							"name" => "add_bron_tbl[{$count}][resource]",
							"options" => $roomnames,
							"value" => $add["room"],
						)),
						"var" => html::select(array(
							"name" => "add_bron_tbl[{$count}][prod_var]",
							"options" => $prod_vars,
							"value" => $add["var"],
						)),
						"prod" => $prodhtml,
						"hidden_time" => $time,
						"hidden_var" => $add["var"],
					));
				}
			}
			$t->set_default_sortby(array("hidden_time" => "hidden_time", "hidden_var" => "hidden_var",));
		}
		else
		{
			return PROP_IGNORE;
		}
	}

	function _set_products_add_bron_tbl($arr)
	{
		if($arr["request"]["rfp_add_bron_tbl_ok"])
		{
			$oi_prods = $arr["obj_inst"]->meta("prods");
			$rvi = get_instance(CL_RESERVATION);
			$bron_verified = 0;
			if($arr["obj_inst"]->prop("confirmed") == RFP_STATUS_CONFIRMED)
			{
				$bron_verified = 1;
			}
			foreach($arr["request"]["add_bron_tbl"] as $i => $add)
			{
				if(!$add["del"] && $add["resource"])
				{
					$add["start"] = mktime($add["time"]["from"]["hour"], $add["time"]["from"]["minute"], 0, $add["start1"]["month"], $add["start1"]["day"], $add["start1"]["year"]);
					$add["end"] = mktime($add["time"]["to"]["hour"], $add["time"]["to"]["minute"], 0, $add["start1"]["month"], $add["start1"]["day"], $add["start1"]["year"]);
					$add["start1"] = $add["start"];
					if(!obj($add["resource"])->is_available(array(
						"start" => $add["start1"],
						"end" => $add["end"],
						"type" => "food",
					)))
					{
						continue;
					}
					$o = obj();
					$o->set_class_id(CL_RESERVATION);
					$o->set_parent($arr["obj_inst"]->id());
					$o->set_prop("resource", $add["resource"]);
					$o->set_prop("start1", $add["start1"]);
					$o->set_prop("end", $add["end"]);
					$o->set_meta("rfp_catering_var", $add["prod_var"]);
					$o->set_prop("customer", $arr["obj_inst"]->prop("data_subm_organisation"));
					$o->set_prop("type", "food");
					$o->set_prop("people_count", $arr["obj_inst"]->prop("data_gen_attendees_no"));
					$o->set_prop("verified", $bron_verified);
					$o->save();
					if(is_array($add["prod"]))
					{
						foreach($add["prod"] as $prodid)
						{
							$prod_price = $rvi->get_product_price(array(
								"product" => $prodid,
								"reservation" => $rv,
								"curr" => $arr["obj_inst"]->prop("default_currency"),
							));
							$price = $rvi->_get_admin_price_view(obj($prodid), $prod_price);
							$amt = $arr["obj_inst"]->prop("data_gen_attendees_no");
							$oi_prods[$prodid.".".$o->id()] = array(
								"amount" => $amt,
								"bronid" => $o->id(),
								"room" => $add["room"],
								"var" => $add["var"],
								"start1" => $add["start1"],
								"end" => $add["end"],
								"price" => round($price, 2),
								"sum" => round($price * $amt, 2),
							);
							$amount = $o->meta("amount");
							$params["amount"] = $amount;
							$params["amount"][$prodid] = $amt;
							$params["change_discount"][$prodid] = 0;
							$rvi->set_products_info($o->id(), $params);
						}
					}
					$arr["obj_inst"]->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_CATERING_RESERVATION",
					));
				}
			}
			aw_global_set("rfp_prods_set", 1);
			$arr["obj_inst"]->set_meta("prods", $oi_prods);
			$arr["obj_inst"]->set_meta("pk_catering_set", $arr["obj_inst"]->prop("data_gen_package"));
			$arr["obj_inst"]->save();
			aw_session_del("rfp_add_brons");
		}
	}

	function _init_prod_add_bron_tbl(&$t, $prods)
	{
		$t->set_header(html::hidden(array(
			"name" => "rfp_add_bron_tbl_ok",
			"value" => 1,
		)));
		$t->define_field(array(
			"name" => "del",
			"caption" => t("Eemalda"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "var",
			"caption" => t("Nimetus"),
			"align" => "center",
		));
		if($prods)
		{
			$t->define_field(array(
				"name" => "prod",
				"caption" => t("Tooted"),
				"align" => "center",
			));
		}
	}

	function get_product_vars($chooser = false)
	{
		$rm = get_instance(CL_RFP_MANAGER);
		$def = $rm->get_sysdefault();
		$prodvars = array();
		if($chooser)
		{
			$prodvars = array(0 => t("--vali--"));
		}
		if($def)
		{
			$defo = obj($def);
			$rfs = $defo->prop("prod_vars_folder");
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
		}
		return $prodvars;
	}

	function get_products_tbl($arr)
	{
		$rm = get_instance(CL_RFP_MANAGER);
		$def = $rm->get_sysdefault();
		if($def)
		{
			$defo = obj($def);
			$rf = $defo->prop("catering_room_folder");
			$rooms = array(0=>" ");
			$ol = new object_list(array(
				"class_id" => CL_ROOM,
				"parent" => $rf,
			));
			
			foreach($ol->arr() as $o)
			{
				$rooms[$o->id()] = $o->name();
			}
		}
		$prodvars = $this->get_product_vars(true);
		classload("vcl/table");
		$t = new aw_table;
		$t->define_chooser(array(
			"field" => "product",
			"name" => "prod_sel",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"chgbgcolor" => "color",
			"colspan" => "span",
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Soodus"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "var",
			"caption" => t("Nimetus"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
			"align" => "center",
			"chgbgcolor" => "color",
		));
		$t->set_rgroupby(array(
			"reserv_group" => "reserv_group",
			"prod_parent" => "prod_parent",
		));
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_CATERING_RESERVATION",
		));
		$rvi = get_instance(CL_RESERVATION);
		$prods = $arr["obj_inst"]->meta("prods");
		$currency = $arr["obj_inst"]->prop("default_currency");
		if(!is_array($prods))
		{
			$prods = array();
		}
		foreach($conn as $c)
		{
			if($this->can("view", $arr["request"]["room_oid"]) && $c->to()->prop("resource") != $arr["request"]["room_oid"])
			{
				// a room is selected from tree and we filter out reservations for that room because connection search can't do that :S
				continue;
			}
			$rv = $c->to();
			$rvo = obj($c->to());
			$prod_list = $rvi->get_room_products($rv->prop("resource"));
			$amount = $rv->meta("amount"); // why the hell is this used???
			$rv_amount = $rvo->get_product_amount();
			$discount = $rvi->get_product_discount($rv->id());
			$def_var = $rv->meta("rfp_catering_var");
			$prod_count = 0;
			$prod_skip = false;
			$rv_group = html::checkbox(array(
				"name" => "sel_res[".$rvo->id()."]",
				"value" => $rvo->id(),
			)).html::obj_change_url($rvo)." (".html::href(array(
				"url" => aw_url_change_var("show_all_prods[".$rvo->id()."]", $arr["request"]["show_all_prods"][$rvo->id()] ? 0 : 1),
				"caption" => $arr["request"]["show_all_prods"][$rvo->id()] ? t("N&auml;ita ainult valituid") : t("N&auml;ita k&otilde;iki"),
			)).")";
			foreach($prod_list->arr() as $prod)
			{
				//if($count = $amount[$prod->id()])
				//{
					$count = $rv_amount[$prod->id()];
					if(!$arr["request"]["show_all_prods"][$rv->id()] && !$count)
					{
						$prod_skip = true;
						continue;
					}
					$prod_price = $rvi->get_product_price(array(
						"product" => $prod->id(),
						"reservation" => $rv->id(),
						"curr" => $arr["obj_inst"]->prop("default_currency"),
					));
					$price = $rvi->_get_admin_price_view(obj($prod->id()), $prod_price);
					$disc = $discount[$prod->id()];
					$prod_sum = $price * $count;
					$prod_sum = $prod_sum - ($prod_sum * $disc)/100;
					$prvid = $prods[$prod->id().".".$rv->id()]["bronid"];
					$times = array();
					if($this->can("view", $prvid))
					{
						$take_times = $prvid;
					}
					else
					{
						$take_times = $rv;
					}
					$prv = obj($take_times);
					$elem_id = $prod->id().".".$rv->id();
					$res_start = $prods[$elem_id]["start1"]?$prods[$elem_id]["start1"]:$prv->prop("start1");
					$res_end = $prods[$elem_id]["end"]?$prods[$elem_id]["end"]:$prv->prop("end");

					$room = $prv->prop("resource");
					$data = array(
						"name" => $prod->name(),
						"price" => html::hidden(array(
							"name" => "prods[".$prod->id().".".$rv->id()."][price]",
							"value" => $price,
						)).$price,
						"amount" => html::textbox(array(
							"name" => "prods[".$prod->id().".".$rv->id()."][amount]",
							"value" => $count?$count:$rv_amount[$prod->id()],
							"size" => 5,
						)),//.($count?$count:$rv_amount[$prod->id()]),
						"discount" => html::textbox(array(
							"name" =>  "prods[".$prod->id().".".$rv->id()."][discount]",
							"value" => $disc,
							"size" => 5,
						))."%",//.$disc."%",
						"sum" => html::hidden(array(
							"name" => "prods[".$prod->id().".".$rv->id()."][sum]",
							"value" => $prod_sum,
						)).number_format($prod_sum, 2),
						"time" => $this->gen_time_form(array(
							"varname" => "prods[".$elem_id."]",
							"start1" => $res_start,
							"end" => $res_end,
						)),
						"room" => html::select(array(
							"name" => "prods[".$prod->id().".".$rv->id()."][room]",
							"width" => 70,
							//"value" => $room,
							//"selected" => $room,
							"options" => $rooms,
							"selected" => ($_t = $prods[$prod->id().".".$rv->id()]["room"])?$_t:$room,
						)),
						"var" => html::select(array(
							"name" => "prods[".$prod->id().".".$rv->id()."][var]",
							"width" => 70,
							"value" => ($set = $prods[$prod->id().".".$rv->id()]["var"]) ? $set : ($def_var ? $def_var : 0),
							"options" => $prodvars,
						)),
						"comment" => html::textarea(array(
							"name" => "prods[".$prod->id().".".$rv->id()."][comment]",
							"value" => $prods[$prod->id().".".$rv->id()]["comment"],
							"cols" => 12,
							"rows" => 2,
						)).html::hidden(array(
							"name" => "prods[".$prod->id().".".$rv->id()."][bronid]",
							"value" => strlen($_t = $prods[$prod->id().".".$rv->id()]["bronid"])?$_t:$rv->id(),
						)),
						"reserv_group" => $rv_group,
						"product" => $prod->id().".".$rv->id(),
						"color" => ($count?$count:$rv_amount[$prod->id()])? "#F0F0F0": "",
						"prod_parent" => $arr["request"]["show_all_prods"][$rv->id()] ? $prod->prop("parent.name") : '',
						"prod_ord" => $prod->ord(),
					);
			
					$t->define_data($data);
				//}
				$prod_count++;
			}
			if(!$prod_count && $prod_skip)
			{
				$t->define_data(array(
					"name" => t("Valitud tooted puuduvad"),
					"reserv_group" => $rv_group,
					"span" => 3,
				));
			}
		}
		$t->set_numeric_field("prod_ord");
		$t->set_default_sortby(array("prod_parent" => "prod_parent", "prod_ord" => "prod_ord"));
	//	$t->set_default_sorder(array("asc"));
		$t->set_rgroupby(array(
			"reserv_group" => "reserv_group",
			"prod_parent" => "prod_parent"
		));

		$t->sort_by();
		$t->set_sortable(false);
		return $t->draw();
	}

	function set_products_tbl($arr)
	{
		$prods = $arr["request"]["prods"];
		if(count($prods) && !aw_global_get("rfp_prods_set"))
		{
			$date = $arr["obj_inst"]->prop("data_gen_arrival_date_admin");
			//if($date > 1)
			//{
				foreach($prods as $tmp1 => $data)
				{
					$tmp2 = explode(".", $tmp1);
					if($data["room"] && strlen($data["to"]["hour"]) && strlen($data["to"]["minute"]) && strlen($data["from"]["hour"]) && strlen($data["from"]["minute"]))
					{
						$start1 = mktime($data["from"]["hour"], $data["from"]["minute"], 0, date('m', $date), date('d', $date), date('Y', $date));
						$end = mktime($data["to"]["hour"], $data["to"]["minute"], 0, date('m', $date), date('d', $date), date('Y', $date));
						if(!$data["bronid"])
						{
							$bron = obj();
							$bron->set_class_id(CL_RESERVATION);
							$bron->set_parent($arr["obj_inst"]->id());
							$bron->set_name(date('d.m.Y H:i', $start1)." - ".date('d.m.Y H:i', $end));
							$bron->set_prop("start1", $start1);
							$bron->set_prop("end", $end);
							$bron->set_prop("resource", $data["room"]);
							$bron->set_prop("type", "food");
							$bron->save();
							$prods[$tmp1]["bronid"] = $bron->id();
							$bri = $bron->instance();
							$bri->set_products_info($bron->id(), array(
								"amount" => array(
									$tmp2[0] => $data["amount"],
								),
								"change_discount" => array(
									$tmp2[0] => $data["discount"],
								),
							));
							$arr["obj_inst"]->connect(array(
								"to" => $bron->id(),
								"type" => "RELTYPE_CATERING_RESERVATION",
							));
						}
						elseif($this->can("view", $data["bronid"]))
						{
							$bron = obj($data["bronid"]);
							if(!$bron->prop("start1"))
							{
								$bron->set_prop("start1", $start1);
							}
							$b_start1 = $bron->prop("start1");
							$start1 = mktime(date('H', $start1), date('i', $start1), 0, date('m', $b_start1), date('d', $b_start1), date('Y', $b_start1));
							if(!$bron->prop("end"))
							{
								$bron->set_prop("end", $b_end);
							}
							$b_end = $bron->prop("end");
							$end = mktime(date('H', $end), date('i', $end), 0, date('m', $b_end), date('d', $b_end), date('Y', $b_end));
							$bron->set_prop("resource", $data["room"]);
							$bron->save();
							$bri = $bron->instance();
							$amount = $bron->meta("amount"); // here we ask only bron amount meta because set_products_info can write only discount info so that old data preserves
							$params["amount"] = $amount;
							$params["amount"][$tmp2[0]] = $data["amount"];
							$params["change_discount"][$tmp2[0]] = $data["discount"];
							$bri->set_products_info($bron->id(), $params);
						}
						$prods[$tmp1]["start1"] = $start1;
						$prods[$tmp1]["end"] = $end;
					}
				}
			//}
			$arr["obj_inst"]->set_meta("prods", $prods); // actually this amount and discount things shouldn't be here at all
			$arr["obj_inst"]->save();
		}
	}

	function _get_housing_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		
		$t->define_field(array(
			"name" => "datefrom",
			"caption" => t("Alates"),
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "dateto",
			"caption" => t("Kuni"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Toat&uuml;&uuml;p"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "rooms",
			"caption" => t("Tubade arv"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "people",
			"caption" => t("Inimeste arv"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Soodus"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("M&auml;rkus"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->set_sortable(false);
		
		$rooms = $arr["obj_inst"]->meta("housing");

		if(is_array($rooms))
		{
			foreach($rooms as $id => $room)
			{
				$t->define_data($this->_get_housing_row($t, $room, $id));
			}
		}

		$t->define_data($this->_get_housing_row($t, array(), "new_1", $arr["obj_inst"]));
		$t->define_data($this->_get_housing_row($t, array(), "new_2", $arr["obj_inst"]));
		$t->define_data($this->_get_housing_row($t, array(), "new_3", $arr["obj_inst"]));
		$t->define_data($this->_get_housing_row($t, array(), "new_4", $arr["obj_inst"]));
		$t->define_data($this->_get_housing_row($t, array(), "new_5", $arr["obj_inst"]));

		if(is_array($rooms))
		{
			$totalsum = 0;
			foreach($rooms as $id => $room)
			{
				$totalsum += $room["sum"];
			}
			$t->define_data(array(
				"split" => "#CCCCCC",
			));
			$t->define_data(array(
				"discount" => "<strong>".t("Kokku:")."</strong>",
				"sum" => number_format($totalsum, 2),
			));
		}
	}

	function _set_housing_tbl($arr)
	{
		$housing = $arr["request"]["housing"];
		if(is_array($housing))
		{
			$output = array();
			foreach($housing as $id => $row)
			{
				$key = $id;
				if(substr($id. 0, 3) == "new")
				{
					$key = count($housing) + 1;
				}
				if(!$row["price"] && !$row["rooms"])
				{
					continue;
				}
				$sum = $row["rooms"] * $row["price"];
				$start = mktime(0,0,1, $row["datefrom"]["month"], $row["datefrom"]["day"], $row["datefrom"]["year"]);
				$end = mktime(0,0,2, $row["dateto"]["month"], $row["dateto"]["day"], $row["dateto"]["year"]);
				$days = floor(($end - $start) / (60*60*24));
				$sum = $sum*$days;
				if($dc = $row["discount"])
				{
					$sum = round($sum - ($sum*$dc)/100, 2);
				}
				$row["datefrom"] = $start;
				$row["dateto"] = $end;
				$row["sum"] = $sum;
				$output[] = $row;
			}
			$arr["obj_inst"]->set_meta("housing", $output);
		}
	}

	function _get_housing_row(&$t, $room = array(), $id = "new", $obj = false)
	{
		if($obj)
		{
			$start = $obj->prop("data_gen_acc_start_admin");
			if($start < 1)
			{
				$start = $obj->prop("data_gen_arrival_date_admin");
			}

			$end = $obj->prop("data_gen_acc_end_admin");
			if($end < 1)
			{
				$end = $obj->prop("data_gen_departure_date_admin");
			}
		}
		$data = array(
			"datefrom" => html::date_select(array(
				"name" => "housing[".$id."][datefrom]",
				"value" => (substr($id,0,3) === "new")?$start:$room["datefrom"],
				"size" => 12,
				"month_as_numbers" => true,
			)),
			"dateto" => html::date_select(array(
				"name" => "housing[".$id."][dateto]",
				"value" => (substr($id, 0, 3) === "new")?$end:$room["dateto"],
				"size" => 12,
				"month_as_numbers" => true,
			)),
			"type" => html::select(array(
				"name" => "housing[".$id."][type]",
				"value" => $room["type"],
				"options" => $this->_get_room_types(),
			)),
			"rooms" => html::textbox(array(
				"name" => "housing[".$id."][rooms]",
				"value" => $room["rooms"],
				"size" => 3,
			)),
			"people" => html::textbox(array(
				"name" => "housing[".$id."][people]",
				"value" => $room["people"],
				"size" => 3,
			)),
			"price" => html::textbox(array(
				"name" => "housing[".$id."][price]",
				"value" => $room["price"],
				"size" => 4,
			)),
			"discount" => html::textbox(array(
				"name" => "housing[".$id."][discount]",
				"value" => $room["discount"],
				"size" => 3,
			))."%",
			"comment" =>  html::textbox(array(
				"name" => "housing[".$id."][comment]",
				"value" => $room["comment"],
				"size" => 25,
			)),
			"sum" => html::hidden(array(
				"name" => "housing[".$id."][sum]",
				"value" => $room["sum"],
			)).number_format($room["sum"], 2),
		);
		return $data;
	}


	function _init_additional_services_tbl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
		));
			$t->define_field(array(
				"name" => "time_from",
				"caption" => t("Alates"),
				"parent" => "time",
			));
			$t->define_field(array(
				"name" => "time_to",
				"caption" => t("Kuni"),
				"parent" => "time",
			));
		$t->define_field(array(
			"name" => "service",
			"caption" => t("Teenus"),
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("M&auml;rkus"),
		));
	}

	function _get_additional_services_tbl_row($key, $data)
	{
		$return = array(
			"date" => html::date_select(array(
				"name" => "add_srv[".$key."][date]",
				"value" => array(
					"day" => date("d", $data["time"]),
					"month" => date("m", $data["time"]),
					"year" => date("Y", $data["time"]),
				),
			)),
			"time_from" => html::time_select(array(
				"name" => "add_srv[".$key."][time]",
				"value" => array(
					"hour" => date("H", $data["time"]),
					"minute" => date("i", $data["time"]),
				),
			)),
			"time_to" => html::time_select(array(
				"name" => "add_srv[".$key."][time_to]",
				"value" => array(
					"hour" => date("H", $data["time_to"]),
					"minute" => date("i", $data["time_to"]),
				),
			)),
			"service" => html::textbox(array(
				"name" => "add_srv[".$key."][service]",
				"value" => $data["service"],
				"size" => "20",
			)),
			"price" => html::textbox(array(
				"name" => "add_srv[".$key."][price]",
				"value" => $data["price"],
				"size" => "4",
			)),
			"amount" => html::textbox(array(
				"name" => "add_srv[".$key."][amount]",
				"value" => $data["amount"],
				"size" => "4",
			)),
			"sum" => $data["sum"],
			"comment" => html::textbox(array(
				"name" => "add_srv[".$key."][comment]",
				"value" => $data["comment"],
				"size" => "25",
			)),
		);
		return $return;
	}

	function _get_additional_services_tbl($arr)
	{
		$this->_init_additional_services_tbl(&$arr);
		$t =& $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$data = $arr["obj_inst"]->get_additional_services();

		$totalsum = 0;
		foreach(safe_array($data) as $k => $row)
		{
			$totalsum += $row["sum"];
			$t->define_data($this->_get_additional_services_tbl_row($k, $row));
		}
		//newline
		$t->define_data($this->_get_additional_services_tbl_row("new_1", array(
			"time" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
			"time_to" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
		)));
		$t->define_data($this->_get_additional_services_tbl_row("new_2", array(
			"time" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
			"time_to" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
		)));
		$t->define_data($this->_get_additional_services_tbl_row("new_3", array(
			"time" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
			"time_to" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
		)));
		$t->define_data($this->_get_additional_services_tbl_row("new_4", array(
			"time" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
			"time_to" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
		)));
		$t->define_data($this->_get_additional_services_tbl_row("new_5", array(
			"time" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
			"time_to" => $arr["obj_inst"]->prop("data_gen_arrival_date_admin"),
		)));
		
		$t->define_data(array(
			"amount" => html::strong(t("Kokku:")),
			"sum" => $totalsum,
		));
	}

	function _set_additional_services_tbl($arr)
	{
		for($i = 1; $i <= 5; $i++)
		{
			$new_rows[$i] = $arr["request"]["add_srv"]["new_".$i];
			unset($arr["request"]["add_srv"]["new_".$i]);
		}
		foreach($arr["request"]["add_srv"] as $k => $v)
		{
			$metadata[$k] = array(
				"time" => mktime($v["time"]["hour"], $v["time"]["minute"], 0, $v["date"]["month"], $v["date"]["day"], $v["date"]["year"]),
				"time_to" => mktime($v["time_to"]["hour"], $v["time_to"]["minute"], 0, $v["date"]["month"], $v["date"]["day"], $v["date"]["year"]),
				"service" => $v["service"],
				"price" => $v["price"],
				"amount" => $v["amount"],
				"sum" => $v["price"] * $v["amount"],
				"comment" => $v["comment"],
			);
		}
		foreach($new_rows as $new)
		{
			if($new["service"] || $new["price"] || $new["amount"] || $new["sum"] || $new["comment"])
			{
				$metadata[] = array(
					"time" => mktime($new["time"]["hour"], $new["time"]["minute"], 0, $new["date"]["month"], $new["date"]["day"], $new["date"]["year"]),
					"time_to" => mktime($new["time_to"]["hour"], $new["time_to"]["minute"], 0, $new["date"]["month"], $new["date"]["day"], $new["date"]["year"]),
					"service" => $new["service"],
					"price" => $new["price"],
					"amount" => $new["amount"],
					"sum" => $new["price"] * $new["amount"],
					"comment" => $new["comment"],
				);
			}
		}
		$arr["obj_inst"]->set_additional_services($metadata);
	}


	function _get_room_types($lang = false)
	{
		$rfp_admin = get_instance(CL_RFP_MANAGER);
		$s = $rfp_admin->get_sysdefault();
		if(!$s)
		{
			warning(t("Teil on valimata s&uuml;steemi vaike-RFP Halduskeskkond."));
			return array();
		}
		else
		{
			$o = obj($s);
			if(!$this->can("view", $o->prop("meta_folder")))
			{
				warning("Objektis ".html::obj_change_url($o)." on m&auml;&auml;ramata muutujate kataloog.");
				return array();
			}
			$ol = new object_list(array(
				"class_id" => CL_META,
				"parent" => $o->prop("meta_folder"),
				"lang_id" => array(),
				"sort_by" => "jrk",
			));
			foreach($ol->arr() as $oid => $obj)
			{
				if($lang)
				{
					$obj_trans = $obj->meta("translations");
					$trans_name = $obj_trans[$lang]["name"];
				}
				else
				{
					$trans_name = false;
				}
				$ret[$oid] = $trans_name?$trans_name:$obj->trans_get_val("name");
			}
			return $ret;
		}
	}

	private function get_rooms($arr)
	{
		$type = split("[_]",$arr["request"]["group"]);
		$type = end($type);
		
		if($type == "prices")
		{
			$prop = "final_rooms";
		}
		elseif($type == "catering")
		{
			$prop = "final_catering_rooms";
		}
		else
		{
			$prop = "final_rooms";
		}
		$rm = get_instance(CL_RFP_MANAGER);
		$def = $rm->get_sysdefault();
		if($def)
		{
			$defo = obj($def);
			$rfs = $defo->prop($prop);
		}
		/*
		$rooms = array();
		if(count($rfs))
		{
			$ol = new object_list(array(
				"class_id" => CL_ROOM,
				"lang_id" => array(),
				"parent" => $rfs
			));
			foreach($ol->arr() as $oid=>$o)
			{
				$rooms[$oid] = $oid;
			}
		}
		 */
		$rooms = $arr["obj_inst"]->prop($prop);
		$ol = new object_list(array(
			"class_id" => CL_ROOM,
			"oid" => $rooms,
			"sort_by" => "jrk, name ASC",
		));
		$rooms = $this->make_keys($ol->ids());
		// wtf is this here for??

		/*
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_ROOM",
		));
		foreach($conn as $c)
		{
			$rooms[$c->prop("to")] = $c->prop("to");
		}
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_RESERVATION",
		));
		foreach($conn as $c)
		{
			$rv = obj($c->prop("to"));
			if($room = $rv->prop("resource"))
			{
				$rooms[$room] = $room;
			}
		}
		 */
		return $rooms;
	}

	function _get_submission($arr)
	{
		$prop = &$arr["prop"];
		
		$prop["value"] = $this->_get_submission_data($arr);
	}

	/**
	@attrib name=get_pdf_file all_args=1
	**/
	function get_pdf_file($arr)
	{
		$arr["obj_inst"] = obj($arr["id"]);
		$client_name = strtr($arr["obj_inst"]->prop("data_subm_name"), " ", "_");
		if($this->can("view", $client_name))
		{
			$o = obj($client_name);
			$client_name = $o->name();
		}
		$arrival = $arr["obj_inst"]->prop("data_gen_arrival_date_admin");
		$dep = $arr["obj_inst"]->prop("data_gen_departure_date_admin");
		$date = (date("Ymd", $arrival) == date("Ymd", $dep))?date("dmY",$arrival):date("dmY", $arrival)."-".date("dmY", $dep);
		$orgname = $arr["obj_inst"]->prop("data_subm_organisation");
		if($this->can("view", $orgname))
		{
			$o = obj($orgname);
			$orgname = $o->name();
		}
		$orgname = strtr(htmlentities($orgname), array(
			"&Auml;" => "A",
			"&auml;" => "a",
			"&Uuml;" => "U",
			"&uuml;" => "u",
			"&Ouml;" => "O",
			"&ouml;" => "o",
			"&Otilde;" => "O",
			"&otilde;" => "o",
			" " => "_",
		));
		$fname = sprintf("%s-%s-%s", ($arr["pdf"] == "offer_pdf")?t("Pakkumine"):t("Kinnitus"), ($orgname?$orgname:$client_name), $date);
		$html = $this->_get_submission_data($arr);
		classload("core/converters/html2pdf");
		$i = new html2pdf;
		if($i->can_convert())
		{
			$i->gen_pdf(array(
				"source" => $html,
				"filename" => $fname,
			));
		}
		else
		{
			die(t("Serveris puudub htmldoc. PDF-i ei saa genereerida"));
		}
	}

	private function _reload_lang_trans($lang_to)
	{
		$langs = aw_ini_get("languages.list");
		$lang_f = false;
		if ($lang_to == "")
		{
			$lang_to = 1;
		}
		foreach($langs as $key => $lang)
		{
			if($key == $lang_to)
			{
				$lang_to = $lang;
				$lang_f = true;
				break;
			}
		}
		if(!$lang_f)
		{
			return false;
		}
		
		// these are for obj prop translations
		aw_ini_set("user_interface.content_trans", 1);
		aw_global_set("lang_id", $lang_to["acceptlang"]);
		
		//$GLOBALS["cfg"]["user_interface"]["full_content_trans"]
		aw_ini_set("user_interface.full_content_trans", 1);
		//$GLOBALS["cfg"]["user_interface"]["trans_classes"][$this->class_id()]
		aw_ini_set("user_interface.trans_classes.".CL_RFP, 1);
		aw_global_set("ct_lang_id", $lang_to["acceptlang"]);



		$cur_admin_lang_lc = $GLOBALS["__aw_globals"]["admin_lang_lc"];
		$GLOBALS["__aw_globals"]["admin_lang_lc"] = $lang_to["acceptlang"];
		lc_load("rfp");
		//$this->lc_load("rfp", "lc_rfp");
		$GLOBALS["__aw_globals"]["admin_lang_lc"] = $cur_admin_lang_lc;
		global $lc_rfp;
		$this->vars($lc_rfp);

		$trans_fn = aw_ini_get("basedir")."/lang/trans/".$lang_to["acceptlang"]."/aw/rfp.aw";
		if (file_exists($trans_fn))
		{
			incl_f($trans_fn);
			require_once($trans_fn);
		}
		//$trans_fn = aw_ini_get("basedir")."/lang/trans/$adm_ui_lc/aw/".basename($class).".aw";
	}

	function _get_submission_data($arr)
	{
		$default_lang = $arr["obj_inst"]->prop("default_language");
		// set output language, terrible hack
		$this->_reload_lang_trans($default_lang);

		$pdf = ($arr["pdf"] == "offer_pdf")?"OFFER":"CONFIRMATION";

		$this->read_template("submission.tpl");
		$this->vars(array(
			"data_gen_function_name" => $arr["obj_inst"]->prop("data_gen_function_name"),
			"data_gen_arrival_date" => date("d.m.Y", $arr["obj_inst"]->prop("data_gen_arrival_date_admin")),
			"data_gen_departure_date" => date("d.m.Y", $arr["obj_inst"]->prop("data_gen_departure_date_admin")),
			"send_date" => date('d.m.Y', $arr["obj_inst"]->prop("data_send_date")),
			"contactperson" => $arr["obj_inst"]->prop("data_contactperson"),
			"payment_method" => $arr["obj_inst"]->prop("data_payment_method"),
			"pointer_text" => nl2br($arr["obj_inst"]->prop("data_pointer_text")),
			"title" => $arr["obj_inst"]->trans_get_val("data_mf_event_type.name"),
			"data_billing_contact" => $arr["obj_inst"]->prop("data_billing_contact.name"),
			"data_billing_street" => $arr["obj_inst"]->prop("data_billing_street"),
			"data_billing_city" => $arr["obj_inst"]->prop("data_billing_city"),
			"data_billing_zip" => $arr["obj_inst"]->prop("data_billing_zip"),
			"data_billing_country" => $arr["obj_inst"]->prop("data_billing_country"),
			"data_billing_state" => $arr["obj_inst"]->prop("data_billing_state"),
			"data_billing_name" => $arr["obj_inst"]->prop("data_billing_name"),
			"data_billing_phone" => $arr["obj_inst"]->prop("data_billing_phone"),
			"data_billing_email" => $arr["obj_inst"]->prop("data_billing_email"),
			"data_billing_company" => $arr["obj_inst"]->prop("data_billing_company.name"),
			"data_billing_fax" => $arr["obj_inst"]->prop("data_billing_fax"),
			"data_billing_comment" => $arr["obj_inst"]->prop("data_billing_comment"),
			"data_contact" => $arr["obj_inst"]->prop("data_subm_name.name"),
			"data_company" => $arr["obj_inst"]->prop("data_subm_organisation.name"),
			"data_street" => $arr["obj_inst"]->prop("data_subm_street"),
			"data_city" => $arr["obj_inst"]->prop("data_subm_city"),
			"data_zip" => $arr["obj_inst"]->prop("data_subm_zip"),
			"data_country" => $arr["obj_inst"]->prop("data_subm_country"),
			"data_state" => $arr["obj_inst"]->prop("data_subm_state"),
			"data_phone" => $arr["obj_inst"]->prop("data_subm_phone"),
			"data_email" => $arr["obj_inst"]->prop("data_subm_email"),
			"data_fax" => $arr["obj_inst"]->prop("data_subm_fax"),
			"offer_preface" => nl2br($arr["obj_inst"]->trans_get_val("offer_preface")),
			"offer_price_comment" => $arr["obj_inst"]->trans_get_val("offer_price_comment"),
			"offer_expire_date" => date("d.m.Y", $arr["obj_inst"]->prop("offer_expire_date")),
			"data_currency" => $arr["obj_inst"]->prop("default_currency.name"),
		));

		$cur_p = obj(aw_global_get("uid_oid"))->get_first_obj_by_reltype("RELTYPE_PERSON");
		$mail = $cur_p->get_first_obj_by_reltype("RELTYPE_EMAIL");
		if($mail)
		{
			$this->vars(array(
				"current_email" => $mail->prop("mail"),
			));
		}

		$package_id = $arr["obj_inst"]->trans_get_val("data_gen_package");
		if($this->can("view", $package_id))
		{
			$package_o = obj($package_id);
			$pack_trans = $package_o->meta("translations");
			$package = ($_t = $pack_trans[$default_lang]["name"])?$_t:$package_o->trans_get_val("name");
		}
		$tables = $arr["obj_inst"]->prop("data_mf_table_form");
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_RESERVATION",
		));
		$reservated_rooms = $arr["obj_inst"]->prop("final_rooms");
		$brons = "";
		//$currency = 745;
		$currency = $arr["obj_inst"]->prop("default_currency");
		$resources_total = 0;
		$colspan = 7;
		if($package)
		{
			$ph = $this->parse("HEADERS_PACKAGE");
			$this->vars(array(
				"HEADERS_PACKAGE" => $ph,
			));
			$colspan += 1;
		}
		else
		{
			$nph = $this->parse("HEADERS_NO_PACKAGE");
			$this->vars(array(
				"HEADERS_NO_PACKAGE" => $nph,
			));
		}
		$totalprice = 0;
		$bron_totalprice = 0;
		$mgri = get_instance(CL_RFP_MANAGER);
		$mgrid = $mgri->get_sysdefault();
		$mgro = obj($mgrid);
		$extra_bron_prices = $mgro->get_extra_hours_prices();
		foreach($conn as $c)
		{
			$rv = obj($c->prop("to"));
			if(!in_array($rv->prop("resource"), $reservated_rooms))
			{
				continue;
			}
			// roomcrap
			$start = $rv->prop("start1");
			$end = $rv->prop("end");
			$timefrom = date('H:i', $start);
			$timeto = date('H:i', $end);
			$datefrom = date('d.m.Y', $start);
			$dateto = date('d.m.Y', $end);
			$tot_time = ($end - $start) / 3600;
			$len = round(($end - $start) / (60 * 60));

			$people = $rv->prop("people_count");
			if($roomid = $rv->prop("resource"))
			{
				$ro = obj($roomid);
				$ro_trans = $ro->meta("translations");
				$room = ($_t = $ro_trans[$default_lang]["name"])?$_t:$ro->trans_get_val("name");
				
				
				// lets check for min hours and its extra prices
				$new_times = $this->alter_reservation_time_include_extra_min_hours($rv, $mgro);

				$room_instance = get_instance(CL_ROOM);
				$sum = $room_instance->cal_room_price(array(
					"room" => $roomid,
					"start" => $new_times["start1"],
					"end" => $new_times["end"], // well, this has one setback actually, we need to calculate the minimum hours, but maybe these are already reservated ??? .. what then??
					"people" => $rv->prop("people_count"),
					"products" => $rv->meta("amount"),
					"bron" => $rv,
					"detailed_info" => true
				));
				// lets check for max hours and its extra prices
				$sum = $this->alter_reservation_price_include_extra_max_hours($rv, $mgro, $sum["room_price"]);
				$price = $sum[$currency];
				if($len >= 1 && $len <= 6)
				{
					$unitprice = round($price / $len, 1);
				}
				else
				{
					$unitprice = "-";
				}
			}
			$ssum = $rv->get_special_sum();
			if($ssum[$currency])
			{
				$price = $ssum[$currency];
				$unitprice = "-";
			}
			$comment = $rv->trans_get_val("comment");
			$tables_rv = ($rvt = $rv->meta("tables"))?$rvt:$tables;
			if($this->can("view", $tables_rv))
			{
				$tables_o = obj($tables_rv);
				$tab_trans = $tables_o->meta("translations");
				$tables_rv = ($_t = $tab_trans[$default_lang]["name"])?$_t:$tables_o->trans_get_val("name");
			}
			$room_data = array(
				"datefrom" => $datefrom,
				"timefrom" => $timefrom,
				"timeto" => $timeto,
				"dateto" => $dateto,
				"room" => $room,
				"tables" => $tables_rv,
				"people" => $people,
				"comments" => $comment,
				"colspan" => $colspan,
				"separate_price" => $price,
				"price" => $price,
				"unitprice" => $unitprice,
				"discount" => $rv->prop("special_discount")?sprintf("%s %%", $rv->prop("special_discount")):"-",
			);
			//$this->vars($room_data);
			if($package)
			{
				$price_set = false;
				$ssum = $rv->get_special_sum();
				if($this->can("view", $mgrid) && !$ssum[$currency])
				{
					$mgr = obj($mgrid);
					$pk_prices = $mgr->meta("pk_prices");

					if(is_array($pk_prices))
					{
						foreach($pk_prices[$package_id]["prices"] as $room_price => $curs)
						{
							foreach($curs as $cur => $price)
							{
								$pk_discount = $arr["obj_inst"]->get_package_custom_discount();
								if($pk_discount)
								{
									$price *= (100 - $pk_discount ) / 100;
								}
								if($cur == $currency)
								{
									$prices_for_calculator[$room_price] = $price;
								}
							}
						}
					}
					$room_p = get_instance(CL_ROOM_PRICE);
					if(!$this->can("view", ($room_price_oid = $arr["obj_inst"]->prop("data_gen_package_price"))))
					{
						$room_prices = $room_p->calculate_room_prices_price(array(
							"oids" => array_keys($pk_prices[$package_id]["prices"]),
							"start" => $start,
							"end" => $start + 1, // well, this is here so because we only need to know which room_price is valid at the start of the reservation time.. and we use that price then to calculate the totalprice .. whatever
							//"prices" => $prices_for_calculator,
						));
						$room_price_oid = key($room_prices);
					}
					$unitprice = $prices_for_calculator[$room_price_oid];
					
				}
				elseif($sp = $ssum[$currency])
				{
					$price = $sp;
					$price_set = true;
					$unitprice = "-";
				}
				if(!$price_set)
				{
					$price = $unitprice*$people;
				}
				$room_data["package_data"] = array(
					"unitprice" => $unitprice,
					"package" => $package,
					"price" => $price,
				);
				$room_data["separate_price"] = $price;
				/*
				$this->vars($room_data["package_data"]);
				$tmp = $this->parse("VALUES_PACKAGE");
				$this->vars(array(
					"VALUES_PACKAGE" => $tmp,
				));
				 */
			}
			else
			{
				/*
				$this->vars(array(
					"price" => $price,
				));
				$tmp = $this->parse("VALUES_NO_PACKAGE");
				$this->vars(array(
					"VALUES_NO_PACKAGE" => $tmp,
				));
				 */
			}
			//$bron_totalprice += $price;
			$bronnings[] = $room_data;
			//$brons .= $this->parse("BRON");

			// resources
			$resources_tmp = $rv->meta("resources_info");
			if(count($resources_tmp))
			{
				$resources[$rv->id()]["start1"] = $rv->prop("start1");
				foreach($resources_tmp as $rid => $data)
				{
					$count = $data["count"];
					if($count)
					{
						$r = obj($rid);
						$price = $data["prices"][$currency];
						
						$total = $price*$count;
						$total = $this->_format_price($total - ($data["discount"]/100)*$total); 
						$resources_total += $total;
						$trans_status = $r->meta("trans_".$default_lang."_status");
						$name = null;
						if($trans_status)
						{
							$trans_arr = $r->meta("translations");
							$name = $trans_arr[$default_lang]["name"];
						}
						if(!$name)
						{
							$name = $r->name();
						}
						$resources[$rv->id()]["resources"][] = array(
							"rid" => $rid,
							"name" => $name,
							"price" => $price,
							"count" => $count,
							"total" => $total,
							"from_hour" => date("H", $data["start1"]),
							"from_minute" => date("i", $data["start1"]),
							"to_hour" => date("H", $data["end"]),
							"to_minute" => date("i", $data["end"]),
							"comment" => $data["comment"],
							"reservation" => $rv->id(),
							"discount" => (int)$data["discount"],
						);
					}
				}
			}
		}

		// roomcrap to be sorted and parseod
		uasort($bronnings, array($this, "_sort_submission_rooms"));
		$mgri = get_instance(CL_RFP_MANAGER);
		$mgrid = $mgri->get_sysdefault();
		foreach($bronnings as $dat)
		{
			$this->vars($dat);
			if($package)
			{
				if($this->can("view", $mgrid))
				{
					$mgr = obj($mgrid);
					$pk_prices = $mgr->meta("pk_prices");
					if(is_array($pk_prices))
					{
						$unitprice = $pk_prices[$package_id][$currency];
					}
				}
				$this->vars($dat["package_data"]);
				$tmp = $this->parse("VALUES_PACKAGE");
				$this->vars(array(
					"VALUES_PACKAGE" => $tmp,
				));
			}
			else
			{
				$this->vars(array(
					"price" => $dat["price"],
				));
				$tmp = $this->parse("VALUES_NO_PACKAGE");
				$this->vars(array(
					"VALUES_NO_PACKAGE" => $tmp,
				));
			}
			$bron_totalprice += $dat["separate_price"];
			
			$brons .= $this->parse("BRON");
		}

		if($package)
		{
			$pck_cprice = $arr["obj_inst"]->get_package_custom_price();
			$bron_totalprice = (is_numeric($pck_cprice) && $pck_cprice >= 0)?$pck_cprice:$bron_totalprice;
		}
		$this->vars(array(
			"total_colspan" => $colspan - 1,
			"bron_totalprice" => $bron_totalprice,
			"bron_colspan" => $colspan,
		));
		$totalprice += $bron_totalprice;
		$res_sub = "";

		// brons
		uasort($resources, array($this, "_sort_submission_resources"));
		if(count($resources))
		{
			$res = "";
			foreach($resources as $reservation => $real_resources)
			{
				$res = "";
				foreach($real_resources["resources"] as $r)
				{
					$this->vars(array(
						"res_name" => $r["name"],
						"res_count" => $r["count"],
						"res_price" => $r["price"],
						"res_total" => $r["total"],
						"res_discount" => $r["discount"]?sprintf("%s %%", $r["discount"]):"-",
						"res_from_hour" => $r["from_hour"],
						"res_from_minute" => $r["from_minute"],
						"res_to_hour" => $r["to_hour"],
						"res_to_minute" => $r["to_minute"],
						"res_comment" => $r["comment"],
					));
					$res .= $this->parse("RESOURCE");
				}
				$rv = obj($reservation);
				$this->vars(array(
					"reservation_name" => $rv->name(),
					"RESOURCE" => $res,
				));
				$final_res .= $this->parse("RESOURCE_RESERVATION");
			}
			$this->vars(array(
				"RESOURCE_RESERVATION" => $final_res,
				"res_total" => $resources_total,
			));
			$aip = "additional_resource_information";
			if(strlen($arr["obj_inst"]->prop($aip)))
			{
				$this->vars(array(
					$aip => $arr["obj_inst"]->prop($aip),
				));
				$this->vars(array(
					"HAS_".strtoupper($aip) => $this->parse("HAS_".strtoupper($aip)),
				));
			}
			$res_sub = $this->parse("RESOURCES");
			$totalprice += $resources_total;
		}
		$prod_conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_CATERING_RESERVATION",
		));
		uasort(&$prod_conn, array($this, "_sort_submission_products2"));
		uasort(&$prods, array($this, "_sort_submission_products"));
		$prods = $arr["obj_inst"]->meta("prods");
		$pd_sub = "";

		if(count($prods))
		{
			$prod_total = 0;
			$pdr = "";
			foreach($prods as $oids => $prod)
			{
				$tmp = explode(".", $oids);
				$sorted_meta_prods[$tmp[1]][$tmp[0]] = $prod;
			}
			foreach($prod_conn as $c)
			{
				$sorted_prods[date("d.m.Y", $c->to()->prop("start1"))][$c->prop("to")] = $sorted_meta_prods[$c->prop("to")];
			}
			foreach($sorted_prods as $rvid => $rvs)
			{
				$has_prod = false;
				$pds = "";
				foreach($rvs as $rv_id => $proddata)
				{
					$rv_has_prod = false;
					$rv_o = obj($rv_id);
					foreach($proddata as $prodid => $prod)
					{
						$po = obj($prodid);
						if($prod["amount"] <= 0)
						{
							continue;
						}
						$rv_has_prod = true;
						$has_prod = true;
						$prodprices = $po->meta("cur_prices");
						$prod["price"] = $prodprices[$currency];
						$prod["sum"] = ($prod["price"] * $prod["amount"]);
						if($prod["discount"] > 0)
						{
							$prod["sum"] = $prod["sum"] - ($prod["sum"] * $prod["discount"] / 100);
						}
						$prodcountcheck++;
						$varname = "";
						$varid = $prod["var"];
						if(is_oid($varid))
						{
							$var = obj($varid);
							$var_trans = $var->meta("translations");
							$varname = ($_t = $var_trans[$default_lang]["name"])?$_t:$var->trans_get_val("name");
						}
						$room = obj($prod["room"]);
						//gen nice event/room combo
						$evt_room = array();
						if(strlen($varname))
						{
							$evt_room[] = $varname;
						}
						$product_trans = $po->meta("translations");
						$room_trans = $room->meta("translations");
						if(strlen($room->name()))
						{
							$evt_room[] = ($_t = $room_trans[$default_lang]["name"])?$_t:$room->name();
						}
						$ta_subs = array();
						for($i = 1; $i <= 10; $i++)
						{
							if($this->is_template("PROD_USERTA".$i))
							{
								$val = $product_trans[$default_lang]["userta".$i] ? $product_trans[$default_lang]["userta".$i] : $po->trans_get_val("userta".$i);
								$this->vars(array(
									"prod_userta".$i => nl2br($val),
								));
								$sub = $this->parse("PROD_USERTA".$i);
								$this->vars(array(
									"PROD_USERTA".$i => $sub,
								));
							}
						}
						$this->vars(array(
							"prod_from_date" => date("d.m.Y", $prod["start1"]),
							"prod_to_date" => date("d.m.Y", $prod["end"]),
							"prod_from_hour" => date("H", $prod["start1"]),
							"prod_from_minute" => date("i", $prod["start1"]),
							"prod_to_hour" => date("H", $prod["end"]),
							"prod_to_minute" => date("i", $prod["end"]),
							"prod_event" => $varname,
							"prod_count" => $prod["amount"],
							"prod_prod" => ($_t = $product_trans[$default_lang]["name"])?$_t:$po->trans_get_val("name"),
							"prod_price" => $this->_format_price($prod["price"]),
							"prod_sum" => $this->_format_price($prod["sum"]),
							"prod_comment" => $prod["comment"],
							"prod_description" => $po->prop("description"),
							"prod_discount" => $prod["discount"]?sprintf("%s %%", $prod["discount"]):"-",
							"prod_event_and_room" => join(", ",$evt_room),
							"prod_room_name" => ($_t = $room_trans[$default_lang]["name"])?$_t:$room->trans_get_val("name"),
						));
						$pds .= $this->parse("PRODUCT_".($package?"":"NO_")."PACKAGE");
						$prod_total += $this->_format_price($prod["sum"]);
					}
					if(!$rv_has_prod)
					{
						$room = obj($rv_o->prop("resource"));
						//gen nice event/room combo
						$evt_room = array();
						if(strlen($varname))
						{
							$evt_room[] = $varname;
						}
						$room_trans = $room->meta("translations");
						if(strlen($room->name()))
						{
							$evt_room[] = ($_t = $room_trans[$default_lang]["name"])?$_t:$room->name();
						}
						$this->vars(array(
							"prod_from_date" => date("d.m.Y", $rv_o->prop("start1")),
							"prod_to_date" => date("d.m.Y", $rv_o->prop("end")),
							"prod_from_hour" => date("H", $rv_o->prop("start1")),
							"prod_from_minute" => date("i", $rv_o->prop("start1")),
							"prod_to_hour" => date("H", $rv_o->prop("end")),
							"prod_to_minute" => date("i", $rv_o->prop("end")),
							"prod_event" => $varname,
							"prod_count" => $rv_o->prop("people_count"),
							"prod_event_and_room" => join(", ",$evt_room),
							"prod_room_name" => ($_t = $room_trans[$default_lang]["name"])?$_t:$room->trans_get_val("name"),
							"prod_sum" => "-",
							"prod_price" => "-",
							"prod_comment" => "-",
							"prod_description" => "-",
							"prod_prod" => "-",
						));
						for($i = 1; $i <= 10; $i++)
						{
							if($this->is_template("PROD_USERTA".$i))
							{
								$this->vars(array(
									"prod_userta".$i => "",
								));
								$sub = $this->parse("PROD_USERTA".$i);
								$this->vars(array(
									"PROD_USERTA".$i => $sub,
								));
							}
						}
						$pds .= $this->parse("PRODUCT_".($package?"":"NO_")."PACKAGE");
					}
				}
				$this->vars(array(
					"PRODUCT_".($package?"":"NO_")."PACKAGE" => $pds,
					"reservation_name" => $rvid,
				));
				$pdr .= $this->parse("PRODUCTS_RESERVATION".($package?"":"_NO_PACKAGE"));
			}
			if($prodcountcheck) // there might be a chance that some row's were skipped(maybe all), and then we don't need that table at all
			{
				$aip = "additional_catering_information";
				if(strlen($arr["obj_inst"]->prop($aip)))
				{
					$this->vars(array(
						"prod_a_colspan" => $package ? 5 : 8,
						$aip => $arr["obj_inst"]->prop($aip),
					));
					$this->vars(array(
						"HAS_".strtoupper($aip) => $this->parse("HAS_".strtoupper($aip)),
					));
				}
				$this->vars(array(
					"PRODUCTS_RESERVATION".($package?"":"_NO_PACKAGE") => $pdr,
					"prod_total" => $prod_total,
				));
				$pd_sub = $this->parse("PRODUCTS_".($package?"":"NO_")."PACKAGE");
				if(!$package)
				{
					$totalprice += $prod_total;
				}
			}
		}
		$housing = $arr["obj_inst"]->meta("housing");
		$hs_sub = "";
		uasort($housing, array($this, "_sort_submission_housing"));
		if(count($housing))
		{
			$housing_total = 0;
			$hss = "";
			$types = $this->_get_room_types($default_lang);
			foreach($housing as $rooms)
			{
				$this->vars(array(
					"hs_from" => date('d.m.Y', $rooms["datefrom"]),
					"hs_to" => date('d.m.Y', $rooms["dateto"]),
					"hs_type" => $types[$rooms["type"]],
					"hs_type_name" => $types[$rooms["type"]],
					"hs_rooms" => $rooms["rooms"],
					"hs_people" => $rooms["people"],
					"hs_price" => $rooms["price"],
					"hs_discount" => $rooms["discount"]?sprintf("%s %%", $rooms["discount"]):"-",
					"hs_sum" => $rooms["sum"],
					"hs_comment" => $rooms["comment"]
				));
				$hss .= $this->parse("ROOMS");
				$housing_total += $rooms["sum"];
			}
			$aip = "additional_housing_information";
			if(strlen($arr["obj_inst"]->prop($aip)))
			{
				$this->vars(array(
					$aip => $arr["obj_inst"]->prop($aip),
				));
				$this->vars(array(
					"HAS_".strtoupper($aip) => $this->parse("HAS_".strtoupper($aip)),
				));
			}
			$this->vars(array(
				"ROOMS" => $hss,
				"hs_total" => $housing_total,
			));
			$hs_sub = $this->parse("HOUSING");
			$totalprice += $housing_total;
		}
		
		// additional sevices
		$add_srv = $arr["obj_inst"]->get_additional_services();
		$as_sub = "";
		uasort($add_srv, array($this, "_sort_submission_additional_services"));
		if(count($add_srv))
		{
			$as_total = 0;
			foreach($add_srv as $srv)
			{
				$this->vars(array(
					"as_date" => date("d.m.Y", $srv["time"]),
					"as_time" => sprintf("%s - %s", date("H:i", $srv["time"]), date("H:i", $srv["time_to"])),
				));
				$this->vars($srv);
				$as_row .= $this->parse("SERVICE");
				$as_total += $srv["sum"];
			}
			$aip = "additional_services_information";
			if(strlen($arr["obj_inst"]->prop($aip)))
			{
				$this->vars(array(
					$aip => $arr["obj_inst"]->prop($aip),
				));
				$this->vars(array(
					"HAS_".strtoupper($aip) => $this->parse("HAS_".strtoupper($aip)),
				));
			}
			$this->vars(array(
				"SERVICE" => $as_row,
				"as_total" => $as_total,
			));
			$as_sub = $this->parse("ADDITIONAL_SERVICES");
			$totalprice += $as_total;
		}
		$info_props = array(
			"additional_information",
			"additional_admin_information",
			"additional_room_information",
		);
		foreach($info_props as $prop)
		{
			if(strlen($arr["obj_inst"]->prop($prop)))
			{
				$this->vars(array(
					$prop => $arr["obj_inst"]->prop($prop),
				));
				$this->vars(array(
					"HAS_".strtoupper($prop) => $this->parse("HAS_".strtoupper($prop)),
				));
			}
		}
		$totalprice = number_format($totalprice, 2);
		$this->vars(array(
			"cancel_and_payment_terms" => $arr["obj_inst"]->prop("show_payment_terms") ? $arr["obj_inst"]->prop("cancel_and_payment_terms") : "",
			"accomondation_terms" => $arr["obj_inst"]->prop("show_acommondation_terms") ? $arr["obj_inst"]->prop("accomondation_terms") : "",
			"BRON" => $brons,
			"RESOURCES" => $res_sub,
			"PRODUCTS_".($package?"NO_":"")."PACKAGE" => $pd_sub,
			"HOUSING" => $hs_sub,
			"ADDITIONAL_SERVICES" => $as_sub,
			"totalprice" => $totalprice,
			$pdf."_ONLY" => $this->parse($pdf."_ONLY"),
			$pdf."_ONLY_2" => $this->parse($pdf."_ONLY_2"),
		));
		
		if(count($bronnings))
		{
			$tmp = $this->parse("RESERVATIONS");
			$this->vars(array(
				"RESERVATIONS" => $tmp,
			));
		}

		// set back the language thingie
		$this->_reload_lang_trans(aw_ini_get("user_interface.default_language"));

		return $this->parse();
	}

	private function _sort_submission_rooms($a, $b)
	{
		return (join("", array_reverse(split("[.]", $a["datefrom"]))).join(split(":", $a["timefrom"]))) - (join("", array_reverse(split("[.]", $b["datefrom"]))).join(split(":", $b["timefrom"])));
	}

	private function _sort_submission_housing($a, $b)
	{
		return $a["datefrom"] - $b["datefrom"];
	}

	private function _sort_submission_resources($a, $b)
	{
		return $a["start1"] - $b["start1"];
	}

	private function _sort_submission_products($a, $b)
	{
		//return ($a["from"]["hour"].str_pad($a["from"]["minute"], 2, "0", STR_PAD_LEFT)) - ($b["from"]["hour"].str_pad($b["from"]["minute"], 2, "0", STR_PAD_LEFT));
		return $a["start1"] - $b["start1"];
	}

	private function _sort_submission_products2($a, $b)
	{
		//return ($a["from"]["hour"].str_pad($a["from"]["minute"], 2, "0", STR_PAD_LEFT)) - ($b["from"]["hour"].str_pad($b["from"]["minute"], 2, "0", STR_PAD_LEFT));
		return $a->to()->prop("start1") - $b->to()->prop("start1");
	}

	private function _sort_submission_additional_services($a, $b)
	{
		return $a["time"] - $b["time"];
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "confirmed":
				if(in_array($arr["obj_inst"]->prop("confirmed"), array(4,5)) && !in_array($prop["value"], array(4,5)))
				{
					$prop["post_append_text"] = t("Ruumidega seotud broneeringud tuleb eraldi kinnitada !");
				}
				if(in_array($prop["value"], array(4,5)))
				{
					$arr["obj_inst"]->mark_reservations_unverified();
				}
				break;
			case "final_rooms":
			case "final_catering_rooms":
				break;

			//-- set_property --//
			case "products_tbl":
				if($this->can("view", $arr["request"]["reservation_oid"]))
				{
					$res = get_instance(CL_RESERVATION); 
					$res->set_products_info($arr["request"]["reservation_oid"], $arr["request"]);
					$this->update_products_info($arr["request"]["reservation_oid"], $arr["obj_inst"]);
				}
				else
				{
					$this->set_products_tbl($arr);
				}
			break;
			case "resources_tbl":
				$res = get_instance(CL_RESERVATION);
				if(strlen($arr["request"]["resources_total_discount"]) && $this->can("view", $arr["request"]["reservation_oid"]))
				{
					$res->set_resources_discount(array(
						"reservation" => $arr["request"]["reservation_oid"],
						"discount" => $arr["request"]["resources_total_discount"],
					));
				}
				
				if(count($arr["request"]["resources_total_price"]) && $this->can("view", $arr["request"]["reservation_oid"]))
				{
					$res->set_resources_price(array(
						"reservation" => $arr["request"]["reservation_oid"],
						"prices" => $arr["request"]["resources_total_price"],
					));
				}
				
				if(count($arr["request"]["resources_info"]) && $this->can("view", $arr["request"]["reservation_oid"]))
				{
					foreach($arr["request"]["resources_info"] as $k => $dat)
					{
						$arr["request"]["resources_info"][$k]["start1"] = mktime($dat["from"]["hour"], $dat["from"]["minute"], 0, 0, 0, 0);
						$arr["request"]["resources_info"][$k]["end"] = mktime($dat["to"]["hour"], $dat["to"]["minute"], 0, 0, 0, 0);
					}
					$res->set_resources_data(array(
						"reservation" => $arr["request"]["reservation_oid"],
						"resources_info" => $arr["request"]["resources_info"],
					));
				}
			break;
			case "prices_tbl":
				foreach($arr["request"] as $var=>$val)
				{
					$tmp = explode("_", $var);
					if($tmp[0] == "discount")
					{
						$o = obj($tmp[1]);
						$o->set_prop("special_discount", $val);	
						$o->save();
					}
					elseif($tmp[0] == "custom")
					{
						$o = obj($tmp[1]);
						$o->set_special_sum( array($arr["obj_inst"]->prop("default_currency") => $val));
					}
					elseif($tmp[0] == "tables")
					{
						$o = obj($tmp[1]);
						$o->set_meta("tables", $val);
						$o->save();
					}
					if(in_array($var, array("package_custom_price", "package_custom_discount")))
					{
						$arr["obj_inst"]->{"set_".$var}($val);
					}
				}
			break;
	
			// tsiisas, these date thingies are really shitty
			// this must be the ugliest solution EVER and this may be the ugliest class EVER!!
			case "data_mf_catering_end":
			case "data_mf_catering_start":
			case "data_mf_end_date":
			case "data_mf_start_date":
			case "data_gen_acc_end":
			case "data_gen_acc_start":
			case "data_gen_departure_date":
			case "data_gen_arrival_date":
			case "data_gen_decision_date":
			case "data_gen_response_date":
				return PROP_IGNORE;
				break;
			case "data_mf_catering_end_admin":
			case "data_mf_catering_start_admin":
			case "data_mf_end_date_admin":
			case "data_mf_start_date_admin":
			case "data_gen_acc_end_admin":
			case "data_gen_acc_start_admin":
			case "data_gen_departure_date_admin":
			case "data_gen_arrival_date_admin":
			case "data_gen_decision_date_admin":
			case "data_gen_response_date_admin":
				if(is_array($prop["value"]))
				{
					$new_val = $this->arr_to_date($prop["value"]);
					$svar = substr($prop["name"], 0, -6);
					$arr["obj_inst"]->set_prop($svar, $new_val);
					$arr["obj_inst"]->save();
				}
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		if($arr["group"] == "final_resource" || $arr["group"] == "final_catering")
		{
			$arr["reservation_oid"] = $_GET["reservation_oid"];
		}
		$arr["post_ru"] = post_ru();
	}

	function callback_mod_retval($arr)
	{
		if($arr["request"]["group"] == "final_resource" || $arr["request"]["group"] == "final_catering")
		{
			$arr["args"]["reservation_oid"] = $arr["request"]["reservation_oid"];
		}
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//

	function _gen_prop_autom_value($value)
	{
		if($this->can("view", $value))
		{
			$o = obj($value);
			$value = html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $value,
					"return_url" => get_ru(),
				), $o->class_id()),
				"caption" => $o->name(),
			));
		}
		return $value;
	}
	
	function _gen_table_additional_dates($data, $t)
	{
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Type"),
		));
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Arrival date"),
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("Departure date"),
		));
		foreach($data as $k => $tmp)
		{
			$t->define_data(array(
				"type" => $tmp["type"],
				"start" => date("d.m.Y", $tmp["start"]),
				"end" => date("d.m.Y", $tmp["end"]),
			));
		}
	}
	function _gen_table_additional_functions($data, $t)
	{
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Type"),
		));
		$t->define_field(array(
			"name" => "delegates_no",
			"caption" => t("No. of delegates"),
		));
		$t->define_field(array(
			"name" => "table_form",
			"caption" => t("Table form"),
		));
		$t->define_field(array(
			"name" => "tech",
			"caption" => t("Tech. equipment"),
		));
		$t->define_field(array(
			"name" => "door_sign",
			"caption" => t("Door sign"),
		));
		$t->define_field(array(
			"name" => "persons_no",
			"caption" => t("No. of persons"),
		));
		$t->define_field(array(
			"name" => "24h",
			"caption" => t("24h Hold"),
		));
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Arrival date"),
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("Departure date"),
		));
		$t->define_field(array(
			"name" => "catering_type",
			"caption" => t("Catering type"),
		));
		$t->define_field(array(
			"name" => "catering_start",
			"caption" => t("Catering start"),
		));
		$t->define_field(array(
			"name" => "catering_end",
			"caption" => t("Catering end"),
		));

		foreach($data as $k => $tmp)
		{
			$t->define_data($tmp);
		}
	}
	
	function _gen_table_search_result($data, $t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Location"),
		));
		$t->define_field(array(
			"name" => "selected",
			"caption" => t("Selected by user"),
		));
		foreach($data as $tmp)
		{
			$t->define_data(array(
				"name" => $tmp["location"],
				"selected" => ($tmp["selected"]==1)?t("Yes"):t("No"),
			));
		}
	}

	function rfp_reservation_description($oid, $type = "html")
	{
		if(!$this->can("view", $oid))
		{
			return false;
		}
		switch($type)
		{
			case "html":
				$this->tpl_init("applications/calendar");
				$this->read_template("rfp_reservation_description.tpl");
				return $this->parse();
				break;
			case "pdf":
				$html = $this->rfp_reservation_description($oid, "html");
				$pdf_gen = get_instance("core/converters/html2pdf");
				die($pdf_gen->gen_pdf(array(
					"filename" => t("Tellimuskirjeldus"),
					"source" => $html,
				)));
				break;
		}
	}

	function _get_files_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_FILE), $arr["obj_inst"]->id(), '', array());
		$tb->add_search_button(array(
			"pn" => $arr["obj_inst"]->id(),
			"multiple" => 1,
			"clid" => CL_FILE,
		));
		$tb->add_delete_button();
	}

	function _get_files_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$ol = new object_list(array(
			"class_id" => CL_FILE,
			"parent" => $arr["obj_inst"]->id(),
		));
		$t->table_from_ol($ol, array("name", "modifiedby", "modified"), CL_FILE);
	}


	private function _format_price($price)
	{
		return join("", split("[,]", $price));
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{

		$fields = array(
			array("show_acommondation_terms", "int"),
			array("show_payment_terms", "int"),
			array("data_gen_package_price", "int"),
			array("default_language", "varchar(3)"),
			array("offer_price_comment", "text"),
			array("offer_expire_date", "int"),
			array("offer_preface", "text"),
			array("additional_information", "text"),
			array("additional_admin_information", "text"),
			array("additional_room_information", "text"),
			array("additional_catering_information", "text"),
			array("additional_resource_information", "text"),
			array("additional_housing_information", "text"),
			array("additional_services_information", "text"),
			array("confirmed", "int"),
			array("cancel_and_payment_terms", "text"),
			array("accomondation_terms", "text"),
			array("final_rooms", "text"),
			array("final_catering_rooms", "text"),
			array("final_theme", "int"),
			array("final_international", "int"),
			array("final_native_guests", "int"),
			array("final_foreign_guests", "int"),
			array("final_foreign_countries", "varchar(255)"),
			array("data_subm_name", "varchar(255)"),
			array("data_subm_country", "varchar(255)"),
			array("data_subm_state", "varchar(255)"),
			array("data_subm_organisation", "varchar(255)"),
			array("data_subm_organizer", "varchar(255)"),
			array("data_subm_email", "varchar(255)"),
			array("data_subm_phone", "varchar(255)"),
			array("data_subm_fax", "varchar(255)"),
			array("data_subm_city", "varchar(255)"),
			array("data_subm_street", "varchar(255)"),
			array("data_subm_zip", "varchar(255)"),
			array("data_subm_contact_preference", "varchar(255)"),
			array("data_gen_function_name", "varchar(255)"),
			array("data_gen_attendees_no", "int"),
			array("data_gen_response_date_admin", "int"),
			array("data_gen_decision_date_admin", "int"),
			array("data_gen_departure_date_admin", "int"),
			array("data_gen_arrival_date_admin", "int"),
			array("data_gen_open_for_alternative_dates", "int"),
			array("data_gen_accommodation_requirements", "int"),
			array("data_gen_multi_day", "varchar(255)"),
			array("data_gen_single_rooms", "int"),
			array("data_gen_double_rooms", "int"),
			array("data_gen_suites", "int"),
			array("data_gen_acc_start_admin", "int"),
			array("data_gen_acc_end_admin", "int"),
			array("data_gen_dates_are_flexible", "int"),
			array("data_gen_meeting_pattern", "varchar(255)"),
			array("data_gen_date_comments", "text"),
			array("data_gen_city", "int"),
			array("data_gen_hotel", "int"),
			array("archived", "int"),
			array("urgent", "int"),
			array("data_gen_alternative_dates", "int"),
			array("data_gen_package", "int"),
			array("data_mf_table", "varchar(255)"),
			array("data_mf_event_type", "int"),
			array("data_mf_table_form", "int"),
			array("data_mf_tech", "varchar(255)"),
			array("data_mf_additional_tech", "text"),
			array("data_mf_additional_decorations", "text"),
			array("data_mf_additional_entertainment", "text"),
			array("data_mf_additional_catering", "text"),
			array("data_mf_breakout_rooms", "int"),
			array("data_mf_breakout_room_setup", "text"),
			array("data_mf_breakout_room_additional_tech", "text"),
			array("data_mf_door_sign", "varchar(255)"),
			array("data_mf_attendees_no", "int"),
			array("data_mf_start_date_admin", "int"),
			array("data_mf_end_date_admin", "int"),
			array("data_mf_24h", "varchar(255)"),
			array("data_mf_catering", "text"),
			array("data_mf_catering_type", "varchar(255)"),
			array("data_mf_catering_attendees_no", "int"),
			array("data_mf_catering_start_admin", "int"),
			array("data_mf_catering_end_admin", "int"),
			array("data_billing_company", "varchar(255)"),
			array("data_billing_contact", "varchar(255)"),
			array("data_billing_street", "varchar(255)"),
			array("data_billing_city", "varchar(255)"),
			array("data_billing_zip", "varchar(255)"),
			array("data_billing_country", "varchar(255)"),
			array("data_billing_state", "varchar(255)"),
			array("data_billing_name", "varchar(255)"),
			array("data_billing_phone", "varchar(255)"),
			array("data_billing_email", "varchar(255)"),
			array("data_billing_fax", "varchar(255)"),
			array("data_billing_comment", "varchar(255)"),
		);
		if(strlen($field))
		{
			foreach($fields as $fafa)
			{
				if($field == $fafa[0])
				{
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => $fafa[1],
					));
					return true;

				}
			}
		}
		if($tbl == "rfp")
		{
			if($field=="")
			{


				foreach($fields as $f)
				{
					$cfields[] = "`".$f[0]."` ".$f[1];
					$ifields[] = "`".$f[0]."`";
				}
				
				$cfieldsql = implode(", ", $cfields);
				$ifieldsql = implode(", ", $ifields);

				$this->db_query("CREATE TABLE rfp (`aw_oid` int primary key, ".$cfieldsql.")");

				$ol = new object_list(array(
					"class_id" => CL_RFP,
				));
				foreach($ol->arr() as $o)
				{
					$values = array();
					foreach($fields as $f)
					{
						$values[] = "'".htmlspecialchars($o->meta($f[0]), ENT_QUOTES)."'";
					}
					$valuesql = implode(",", $values);
					$this->db_query("INSERT INTO rfp(`aw_oid`, ".$ifieldsql.") VALUES('".$o->id()."', ".$valuesql.")");
					
				}
				return true;
			}
		}
	}

	/**
		@attirb api=1
		@comment
			generates time select form... resrvation class uses this
	 **/
	function gen_time_form($arr)
	{
		$ret = html::time_select(array(
			"name" => $arr["varname"]."[from]",
			"value" => array(
				"hour" => date("H", $arr["start1"]),
				"minute" => date("i", $arr["start1"]),
			),
		))."<br />".t("kuni")."<br />".html::time_select(array(
			"name" => $arr["varname"]."[to]",
			"value" => array(
				"hour" => date("H", $arr["end"]),
				"minute" => date("i", $arr["end"]),
			),
		));
		return $ret;
	}

	public function get_rfp_statuses()
	{
		return $this->rfp_status;
	}

	/** Callback function. Called when reservation is added through calendar
		@attrib name=handle_new_reservation all_args=1 params=name
	 **/
	function handle_new_reservation($arr)
	{
		$rfp = obj($arr["rfp_oid"]);
		$rfp->connect(array(
			"type" => "RELTYPE_RESERVATION",
			"to" => $arr["reservation"]->id(),
		));
	}

	/** Callback function to alter reservation name in calendar. Called when reservation calendar is shown and a reservation is found. 
		@attrib name=handle_calendar_show_reservation all_args=1 params=name
	 **/
	function handle_calendar_show_reservation(&$arr)
 	{
		if($this->can("view", $arr["reservation"]->id()) && $this->can("view", $arr["rfp_oid"]))
		{
			$rels = $arr["reservation"]->connections_to(array(
				"type" => array(
					3,12
				),
				"from.class_id" => CL_RFP,
			));
			if(!count($rels))
			{
				$url = $this->mk_my_orb("connect_reservation", array(
					"return_url" => get_ru(),
					"rfp" => $arr["rfp_oid"],
					"reservation" => $arr["reservation"]->id(),
					"reltype" => $arr["reltype"],
				));
				$arr["bron_name"] .= html::href(array(
					"url" => "javascript:void();",
					"onClick" => "aw_get_url_contents(\"".$url."\");window.location.reload();",
					"caption" => t("( Seo RFP'ga )"),
				));
			}
		}
	}

	/** Callback function for bron calendar rfp::handle_calendar_show_reservation() uses this function to connect reservations to rfp.
		@attrib name=connect_reservation params=name all_args=1
	 **/
	function connect_reservation($arr)
	{
		if($this->can("view", $arr["rfp"]) and $this->can("view", $arr["reservation"]))
		{
			$rfp = obj($arr["rfp"]);
			$rfp->connect(array(
				"to" => $arr["reservation"],
				"type" => $arr["reltype"]?$arr["reltype"]:"RELTYPE_RESERVATION",
			));
			$rv_v = 0;
			if($rfp->prop("confirmed") == RFP_STATUS_CONFIRMED)
			{
				$rv_v = 1;
			}
			$rvo = obj($arr["reservation"]);
			$rvo->set_prop("verified", $rv_v);
			$rvo->save();
		}
	}

	/**
		@attrib name=delete_objects all_args=1
	 **/
	function delete_objects($arr)
	{
		$o = obj($arr["id"]);
		if($arr["group"] == "final_prices")
		{
			// remove rooms
			if(is_array($arr["room_sel"]) and count($arr["room_sel"]))
			{
				foreach($arr["room_sel"] as $room)
				{
					//$o->remove_room_reservations($room);
				}
			}

			// remove reservations
			if(is_array($arr["sel"]) and count($arr["sel"]))
			{
				foreach($arr["sel"] as $reservation)
				{
					$o->remove_room_reservation($reservation);
				}
			
			}
		}
		elseif($arr["group"] == "final_catering")
		{
			if(is_array($arr["prod_sel"]) and count($arr["prod_sel"]))
			{
				foreach($arr["prod_sel"] as $data)
				{
					list($d_product, $d_reservation) = split("[.]", $data);
					$o->remove_catering_reservation_product($d_reservation, $d_product);
				}
				$o->save();
			}

			if(is_array($arr["sel_res"]) and count($arr["sel_res"]))
			{
				foreach($arr["sel_res"] as $data)
				{
					$rv = obj($data);
					$rv->delete();
				}
			}
		}
		elseif($arr["group"] == "final_resource")
		{

			if(is_array($arr["sel_resource"]) and count($arr["sel_resource"]))
			{
				foreach($arr["sel_resource"] as $data)
				{
					list($d_resource, $d_reservation) = split("[.]", $data);
					$d_reservation = obj($d_reservation);
					$data = $d_reservation->get_resources_data();
					unset($data[$d_resource]);
					$d_reservation->set_resources_data($data);
				}
				$d_reservation->save();
			}
			if(is_array($arr["sel_res"]) and count($arr["sel_res"]))
			{
				foreach($arr["sel_res"] as $data)
				{
					$rv = obj($data);
					$rv->delete();
				}
			}
		}
		return $arr["post_ru"];
	}

	private function _modify_prices_tbl_after($arr, $t, $tbl_sum_row = false)
	{
		if($this->can("view", $arr["obj_inst"]->prop("data_gen_package"))) // theres a package selected, we have to add an extra line to table for package discount
		{
			$t->define_data(array(
				"name" => sprintf(t("Pakett '%s'"), obj($arr["obj_inst"]->prop("data_gen_package"))->name()),
				"custom" => html::textbox(array(
					"name" => "package_custom_price",
					"size" => "5",
					"value" => $arr["obj_inst"]->get_package_custom_price(),
				)),
				"discount" => html::textbox(array(
					"name" => "package_custom_discount",
					"size" => "5",
					"value" => $arr["obj_inst"]->get_package_custom_discount(),
				)),
			));
			if($tbl_sum_row)
			{
				$total_discount = $arr["obj_inst"]->get_package_custom_discount();
				
				$room_price = $this->can("view", ($_t = $arr["obj_inst"]->prop("data_gen_package_price")))?$_t:false;
				$conns = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_RESERVATION",
				));

				$mgr = get_instance(CL_RFP_MANAGER);
				$mgr = obj($mgr->get_sysdefault());
				$pk_prices = $mgr->meta("pk_prices");
				$room_p = get_instance(CL_ROOM_PRICE);

				$currency = $arr["obj_inst"]->prop("default_currency");
				if(is_array($pk_prices))
				{
					foreach($pk_prices[$arr["obj_inst"]->prop("data_gen_package")]["prices"] as $loop_room_price => $curs)
					{
						foreach($curs as $cur => $price)
						{
							$prices_for_calculator[$loop_room_price][$cur] = $price;
						}
					}
				}
				$totprice = 0;
				foreach($conns as $conn)
				{
					$rv = $conn->to();
					unset($tot_add);
					$ssum = $rv->get_special_sum();
					if($sp = $ssum[$currency])
					{
						$tot_add = $sp;
						$rv_prices[$rv->id()]["special"] = $tot_add;
					}
					if(!isset($tot_add))
					{
						if(!$room_price)
						{
							$room_prices = $room_p->calculate_room_prices_price(array(
								"oids" => array_keys($pk_prices[$arr["obj_inst"]->prop("data_gen_package")]["prices"]),
								"start" => $rv->prop("start1"),
								"end" => $rv->prop("start1") + 1,
							));
							$room_price_oid = key($room_prices);
						}
						else
						{
							$room_price_oid = $room_price;
						}
						$tot_add = ($prices_for_calculator[$room_price_oid][$currency] * $rv->prop("people_count"));
						if($total_discount)
						{
							$tot_add *= (100 - $total_discount) / 100;
						}
						foreach($prices_for_calculator[$room_price_oid] as $cur => $price)
						{
							$currencies[$cur] = $cur;
							$rv_prices[$rv->id()][$cur] = $price * $rv->prop("people_count");
						}
					}
					$totprice += $tot_add;
				}
				$data = $t->get_data();
				foreach($data as $key => $values)
				{
					if($rvid = $values["reservation"])
					{
						$tprice = 0;
						foreach($currencies as $cur)
						{
							if($pr = $rv_prices[$rvid]["special"])
							{
								$price = $tprice = $pr;
							}
							else
							{
								$price = $rv_prices[$rvid][$cur];
								if($total_discount)
								{
									$price = ((100 - $total_discount) / 100) * $price;
								}
								if($cur == $currency)
								{
									$tprice = $price;
								}
							}
							$values["price".$cur] = number_format($price, 2);
						}
						$values["total"] = number_format($tprice, 2);
						$t->set_data($key, $values);
					}
				}
				if($custom = $arr["obj_inst"]->get_package_custom_price())
				{
					$totprice = $custom;
					$totprice = ($_t = $total_discount)?(((100-$_t) /100 )* $totprice):$totprice;
				}
				
				$t->set_data($tbl_sum_row, array(
					"total" => number_format($totprice, 2),
					"custom" => html::strong(t("Kokku:")),
				));
			}
		}
	}

	function alter_reservation_time_include_extra_min_hours($rv, $rfpm)
	{
		$start = $rv->prop("start1");
		$end = $rv->prop("end");
		$tot_hours = ($end - $start) / 3600;
		$extra = $rfpm->get_extra_hours_prices();
		$secs_less = 0;
		if($tot_hours < $extra[$rv->prop("resource")]["min_hours"])
		{
			$secs_less = ($extra[$rv->prop("resource")]["min_hours"] - $tot_hours) * 3600;
		}
		return array(
			"start1" => $start,
			"end" => ($end + $secs_less),
		);
	}

	function alter_reservation_price_include_extra_max_hours($rv, $rfpm, $current_sum)
	{
		$start = $rv->prop("start1");
		$end = $rv->prop("end");
		$tot_time = ($end - $start) / 3600;
		$extra = $rfpm->get_extra_hours_prices();
		foreach($current_sum as $cur => $price)
		{
			if($tot_time > $extra[$rv->prop("resource")]["max_hours"])
			{
				$over = floor($tot_time - $extra[$rv->prop("resource")]["max_hours"]);
				$current_sum[$cur] += ($extra[$rv->prop("resource")]["max_prices"][$cur] * $over);
			}
		}
		return $current_sum;
	}

	/**
		@attrib name=_get_ac_data_subm_name params=name all_args=1
	 **/
	function _get_ac_data_subm_name($arr)
	{
		$this->_gen_and_output_ac_data(CL_CRM_PERSON, $arr["data_subm_name"] , $this->rfpm->prop("clients_folder"));
	}

	/**
		@attrib name=_get_ac_data_subm_organisation params=name all_args=1
	 **/
	function _get_ac_data_subm_organisation($arr)
	{//clients_folder
		$this->_gen_and_output_ac_data(CL_CRM_COMPANY, $arr["data_subm_organisation"] , $this->rfpm->prop("clients_folder"));
	}

	/**
		@attrib name=_get_ac_data_subm_organizer params=name all_args=1
	 **/
	function _get_ac_data_subm_organizer($arr)
	{
		$this->_gen_and_output_ac_data(array(
			CL_CRM_PERSON,
			CL_CRM_COMPANY,
		), $arr["data_subm_organizer"]);
	}

	/**
		@attrib name=_get_ac_data_billing_company params=name all_args=1
	 **/
	function _get_ac_data_billing_company($arr)
	{//clients_folder
		$this->_gen_and_output_ac_data(CL_CRM_COMPANY, $arr["data_billing_company"] , $this->rfpm->prop("clients_folder"));
	}

	/**
		@attrib name=_get_ac_data_billing_contact params=name all_args=1
	 **/
	function _get_ac_data_billing_contact($arr)
	{//clients_folder
		$this->_gen_and_output_ac_data(CL_CRM_PERSON, $arr["data_billing_contact"] , $this->rfpm->prop("clients_folder"));
	}

	function _gen_and_output_ac_data($class_id, $value, $menu = null)
	{
		$l = new object_list(array(
			"class_id" => $class_id,
			"name" => "%".$value."%",
		));
		if($menu)
		{
			$l["menu"] = $menu;
		}
		foreach($l->arr() as $obj)
		{
			$res[$obj->id()] = $obj->name();
		}
		$j = get_instance("protocols/data/json");
		die($j->encode($res));
	}
}
?>
