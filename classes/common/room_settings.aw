<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_ROOM_SETTINGS relationmgr=yes no_comment=1 no_status=1 prop_cb=1  maintainer=markop

@default table=objects
@default group=general
@default field=meta

	@property hold_not_verifyed type=textbox field=meta method=serialize
	@caption Kinnitamata broneeringut tuleb hoida kinni x minutit

	@property related_room_folder type=relpicker multiple=1 field=meta method=serialize reltype=RELTYPE_RELATED_ROOM_FOLDER
	@caption Seotud ruumide kaust

	@property related_rooms type=relpicker multiple=1 field=meta method=serialize store=connect reltype=RELTYPE_RELATED_ROOMS
	@caption Ruumid mida samal ajal broneerida

	@property cal_refresh_time type=textbox size=5 field=meta method=serialize 
	@caption Mitme minuti tagant kalendrit refreshida
	
	@property customer_menu type=relpicker field=meta method=serialize reltype=RELTYPE_MENU
	@caption Kataloog kuhu kliendid salvestada
	
	@property projects_menu type=relpicker field=meta method=serialize reltype=RELTYPE_MENU
	@caption Kataloog kuhu projektid salvestada

	@property min_price_to_all type=checkbox ch_value=1 field=meta method=serialize 
	@caption Miinimumhind m&otilde;jub k&otilde;igile

@groupinfo bron caption="Broneeringuobjekt"
@default group=bron

	@property no_cust_arrived_pop type=checkbox ch_value=1  method=serialize
	@caption Kliendi saabumise kinnitust pole vaja k&uuml;sida

	@property use_existing_person type=checkbox ch_value=1  method=serialize
	@caption Samanimeliste isikute puhul v&otilde;etakse aluseks olemasolev isikuobjekt

	@property dont_ask_close_reason type=checkbox ch_value=1  method=serialize
	@caption Ei k&uuml;si sulgemise p&otilde;hjust

	@property bron_required_fields type=table store=no  method=serialize
	@caption Broneeringuobjekti kohustuslikud v&auml;ljad

@groupinfo calendar_view caption="Kalendrivaade"
@default group=calendar_view

	@property comment_pos type=select  method=serialize
	@caption Kuva kommentaar

	@property buffer_time_string type=textbox method=serialize
	@caption Puhveraja string

	@property closed_time_string type=textbox  method=serialize
	@caption Pausi string

	@property closed__time_string type=textbox  method=serialize
	@caption Suletud aja string

	@property available_time_string type=textbox  method=serialize
	@caption Vaba aja string

	@property reserved_time_string type=textbox ch_value=1 method=serialize
	@caption Broneeritud aja string

	@property bron_popup_detailed type=checkbox ch_value=1 method=serialize
	@caption Broneerimisaken on detailse sisuga

	@property bron_popup_immediate type=checkbox ch_value=1 method=serialize
	@caption Broneerimisaken avaneb kohe kui ajale klikkida

	@property bron_no_popups type=checkbox ch_value=1 method=serialize
	@caption Broneerimiseks ei avata popup aknaid
	
	@property cal_from_today type=checkbox ch_value=1 method=serialize
	@caption Ruumide kalendrid algavad t&auml;nasest, mitte n&auml;dala algusest

	@property show_workers_in_calander type=checkbox ch_value=1 method=serialize
	@caption N&auml;ita t&ouml;&ouml;tajaid kalendrivaates

	@property dont_show_day_sum_in_calander type=checkbox ch_value=1 method=serialize
	@caption &Auml;ra n&auml;ita p&auml;eva summat kalendrivaates

	@property show_only_my_graphs type=checkbox ch_value=1 method=serialize
	@caption N&auml;ita ainult enda t&ouml;&ouml;graafikuid

	@property max_times_per_day type=textbox field=meta method=serialize
	@caption Maksimaalne aegade arv samal p&auml;eval

	@property show_unverified type=checkbox ch_value=1 method=serialize
	@caption N&auml;ita kalendris kinnitamata broneeringuid

	@property cal_show_prods type=checkbox ch_value=1 method=serialize
	@caption Kuva valitud tooteid kalendrivaates

	@property cal_show_prod_img type=checkbox ch_value=1 method=serialize
	@caption Kuva tootepilte kalendrivaates

	@property cal_show_prod_img_ord type=textbox size=5 method=serialize
	@caption Tootepildi j&auml;rjekorranumber, mida kuvada
	
	@property disp_bron_len type=checkbox ch_value=1 method=serialize
	@caption &Auml;ra kuva aja pikkust kalendris

	@property bron_props type=table save=no no_caption=1
	@caption Broneeringu omaduste tabel


@groupinfo whom caption="Kellele kehtib"
@default group=whom
	@property users type=relpicker multiple=1 store=connect reltype=RELTYPE_USER field=meta method=serialize
	@caption Kasutajad

	@property groups type=relpicker multiple=1 store=connect reltype=RELTYPE_GROUP field=meta method=serialize
	@caption Grupid

	@property persons type=relpicker multiple=1 store=connect reltype=RELTYPE_PERSON field=meta method=serialize
	@caption Isikud

	@property cos type=relpicker multiple=1 store=connect reltype=RELTYPE_COMPANY field=meta method=serialize
	@caption Organisatsioonid

	@property sects type=relpicker multiple=1 store=connect reltype=RELTYPE_SECTION field=meta method=serialize
	@caption Osakonnad

	@property profs type=relpicker multiple=1 store=connect reltype=RELTYPE_PROFESSION field=meta method=serialize
	@caption Ametinimetused

	@property everyone type=checkbox ch_value=1 table=objects field=flags
	@caption K&otilde;ik


@groupinfo colours caption="V&auml;rvid"
@default group=colours
@default field=meta 
@default method=serialize

	@property col_buffer type=colorpicker 
	@caption Puhveraeg kalendris

	@property col_available type=colorpicker 
	@caption Vaba aja v&auml;rv

	@property col_closed type=colorpicker 
	@caption Kinnise aja v&auml;rv

	@property unverified_color_sub type=text subtitle=1 store=no
	@caption Kinnitamata broneering
	
		@property col_web_halfling type=colorpicker 
		@caption Veebis pooleliolev tellimus

		@property col_sent type=colorpicker
		@caption Saadetud RFP tellimus

		@property col_on_hold type=colorpicker
		@caption T&auml;psustamisel RFP tellimus

		@property col_back type=colorpicker
		@caption Tagasi l&uuml;katud RFP tellimus

		@property col_unverified type=colorpicker
		@caption T&uuml;histatud RFP tellimus

	@property verified_color_sub type=text subtitle=1 store=no
	@caption Kinnitatud broneering

		@property col_recent type=colorpicker 
		@caption Hiljuti muudetud reserveering

		@property col_slave type=colorpicker 
		@caption Alamreserveering

		@property col_food type=colorpicker 
		@caption Toitlustusreserveering

@groupinfo calendar_view caption="Kalendrivaade"
@default group=calendar_view

	@property bron_props type=table save=no no_caption=1
	@caption Broneeringu omaduste tabel


@groupinfo settings caption="Muud seaded"
	@groupinfo settings_gen caption="Muud seaded" parent=settings
	

@groupinfo email caption="Meiliseaded"

	@groupinfo delete_email caption="Kustutamine" parent=email
	@default group=delete_email

		@property send_del_mail type=checkbox ch_value=1
		@caption Saada kustutamise kohta meil


		@property del_mail_to type=textbox
		@caption Kellele kustutamise kohta meil saata

		@property del_mail_from type=textbox 
		@caption Meili from aadress

		@property del_mail_from_name type=textbox
		@caption Meili from nimi

		@property del_mail_subj type=textbox
		@caption Meili subjekt


		@property del_mail_legend type=text
		@caption Meili sisu legend
		
		@property del_mail_ct type=textarea rows=20 cols=50
		@caption Meili sisu

	@groupinfo uv_email caption="Kinnituse eemaldamine" parent=email
	@default group=uv_email

		@property send_uv_mail type=checkbox ch_value=1
		@caption Saada kinnituse kustutamise kohta meil

		@property uv_mail_to type=textbox
		@caption Kellele kustutamise kohta meil saata

		@property uv_mail_from type=textbox 
		@caption Meili from aadress

		@property uv_mail_from_name type=textbox
		@caption Meili from nimi


		@property uv_mail_subj type=textbox
		@caption Meili subjekt
		
		@property uv_mail_legend type=text
		@caption Meili sisu legend
		
		@property uv_mail_ct type=textarea rows=20 cols=50
		@caption Meili sisu


	@groupinfo order_email caption="Tellimusmeil" parent=email
	@default group=order_email

		@property order_mail_from type=textbox 
		@caption Meili from aadress

		@property order_mail_from_name type=textbox
		@caption Meili from nimi

		@property order_mail_subj type=textbox
		@caption Meili subjekt

		@property order_mail_legend type=text
		@caption Meili sisu legend

		@property order_mail_to type=textbox
		@caption Kellele tellimuse kohta meil saata

		@property order_mail_groups type=select multiple=1
		@caption Kasutajagrupid, kelle poolt tehtud broneeringute kohta meil saadetakse

	@groupinfo verify_email caption="Kinnitusmeil" parent=email
	@default group=verify_email
		
		@property send_verify_mail type=checkbox ch_value=1
		@caption Saada kinnituse mail

		@property verify_mail_from type=textbox 
		@caption Meili from aadress

		@property verify_mail_from_name type=textbox
		@caption Meili from nimi

		@property verify_mail_subj type=textbox
		@caption Meili subjekt

		@property verify_mail_legend type=text
		@caption Meili sisu legend

Meili sisu peab saama t6lkida, ilmselt seadetele T6lgi vaade teha lisaks.

@groupinfo grp_settings caption="Gruppide seaded"
@default group=grp_settings

	@property grp_settings type=table store=no no_caption=1

@reltype USER value=1 clid=CL_USER
@caption Kasutaja

@reltype PERSON value=3 clid=CL_CRM_PERSON
@caption Isik

@reltype COMPANY value=4 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype SECTION value=5 clid=CL_CRM_SECTION
@caption Osakond

@reltype PROFESSION value=6 clid=CL_CRM_PROFESSION
@caption Ametinimetus

@reltype RELATED_ROOM_FOLDER value=7 clid=CL_MENU
@caption Seotud ruumide kaust

@reltype GROUP value=8 clid=CL_GROUP
@caption Grupp

@reltype MENU value=9 clid=CL_MENU
@caption Grupp

@reltype RELATED_ROOMS value=10 clid=CL_ROOM
@caption Ruumid mida samaaegselt broneerida

*/

class room_settings extends class_base
{
	const AW_CLID = 1188;

	function room_settings()
	{
		$this->init(array(
			"tpldir" => "common/room_settings",
			"clid" => CL_ROOM_SETTINGS
		));
		
		//muutujad mida saab kasutada ruumi kalendri broneeringute v2ljan2gemise konfimiseks
		//bronni objekti juures get_room_calendar_prop funktsioonis peab neile variandi tegema kui lisada siia
		$this->extra_calendar_bron_props = array(
			"product_image" => array("caption" => t("Toote pilt")),
			"product_code" => array("caption" => t("Toote kood")),
		);

	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "del_mail_legend":
				$prop["value"] = t("#ord# - tellimuse sisu");
				break;

			case "uv_mail_legend":
				$prop["value"] = t("#ord# - tellimuse sisu<br>#reason# - kinnituse eemaldamise p&ouml;hjus");
				break;			

			case "order_mail_groups":
				$ol = new object_list(array(
					"class_id" => CL_GROUP,
					"type" => "0",
					"lang_id" => array(),
					"site_id" => array()
				));
				$prop["options"] = $ol->names();
				break;

			case "order_mail_legend":
			case "verify_mail_legend":
				$prop["value"] = t("sisu tuleb common/room/preview.tpl failist");
				break;

			case "comment_pos":
				$prop["options"] = array("Alt tekstina" , "Broneerija nime j&auml;rele");
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_generate_scripts()
	{
		$cplink = $this->mk_my_orb("colorpicker",array(),"css");
		return	"var element = 0;\n".
			"function set_color(clr) {\n".
			"document.getElementById(element).value=clr;\n".
			"}\n".
			"function colorpicker(el) {\n".
			"element = el;\n".
			"aken=window.open('$cplink','colorpickerw','height=220,width=310');\n".
			"aken.focus();\n".
			"};\n";

	}

	/** Returns the current settings for the room, based on the current user
		@attrib api=1 params=pos

		@param $room optional
			room id to return settings for

		@returns
			cl_room_settings or null if no settings found
	**/
	function get_current_settings($room)
	{
		if(is_oid($room) && $this->can("view" , $room))
		{
			$room = obj($room);
		}
		$oids = safe_array($room->prop("settings"));
		$objs = array();
		foreach($oids as $set_oid)
		{
			if ($this->can("view", $set_oid))
			{
				$objs[] = obj($set_oid);
			}
		}

		foreach($objs as $settings)
		{
			if (in_array(aw_global_get("uid_oid"), $settings->prop("users")))
			{
				return $settings;
			}
		}

		foreach($objs as $settings)
		{
			if (count(array_intersect($settings->prop("groups"), aw_global_get("gidlist_oid"))))
			{
				return $settings;
			}
		}

		foreach($objs as $settings)
		{
			$pers = $settings->prop("persons");
			if (is_array($pers) && count($pers))
			{
				$cur_p = get_current_person();
				if (in_array($cur_p->id(), $pers))
				{
					return $settings;
				}
			}
		}

		foreach($objs as $settings)
		{
			$cos = $settings->prop("cos");
			if (is_array($cos) && count($cos))
			{
				$cur_co = get_current_company();
				if (in_array($cur_co->id(), $cos))
				{
					return $settings;
				}
			}
		}

		foreach($objs as $settings)
		{
			$sects = $settings->prop("sects");
			if (is_array($sects) && count($sects))
			{
				$cd = get_instance("applications/crm/crm_data");
				$cursec = $cd->get_current_section();
				if (in_array($cursec->id(), $sects))
				{
					return $settings;
				}
			}
		}

		foreach($objs as $settings)
		{
			$profs = $settings->prop("profs");
			if (is_array($profs) && count($profs))
			{
				$cd = get_instance("applications/crm/crm_data");
				$curprof = $cd->get_current_profession();
				if (in_array($curprof->id(), $profs))
				{
					return $settings;
				}
			}
		}

		foreach($objs as $settings)
		{
			if ($settings->prop("everyone"))
			{
				return $settings;
			}
		}
		return null;
	}

	private function _init_bron_req_t(&$t)
	{
		$t->define_field(array(
			"caption" => t("Omadus"),
			"align" => "center",
			"name" => "prop"
		));

		$t->define_field(array(
			"caption" => t("N&otilde;utud"),
			"align" => "center",
			"name" => "req"
		));
	}

	function _get_bron_required_fields($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_bron_req_t($t);

		$tmp = obj();
		$tmp->set_class_id(CL_RESERVATION);
		$props = $tmp->get_property_list();
	
		$req = $arr["obj_inst"]->meta("bron_req_fields");
		foreach($props as $pn => $pd)
		{
			if (!is_array($req[$pn]))
			{
				$req[$pn] = array(); 
			}
			$t->define_data(array(
				"prop" => $pd["caption"]." ($pn)",
				"req" => html::checkbox(array(
					"name" => "d[$pn][req]",
					"value" => 1,
					"checked" => $req[$pn]["req"] == 1
				))
			));
		}
	}

	function _set_bron_required_fields($arr)
	{
		$arr["obj_inst"]->set_meta("bron_req_fields", $arr["request"]["d"]);
	}

	private function _init_grp_settings_t(&$t)
	{
		$t->define_field(array(
			"name" => "grp",
			"caption" => t("Grupp"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "bron_tm",
			"caption" => t("Broneerimisaegade seaded"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "from",
			"caption" => t("Alates"),
			"align" => "center",
			"parent" => "bron_tm"
		));
		$t->define_field(array(
			"name" => "from_ts",
			"caption" => t("Aja&uuml;hik"),
			"align" => "center",
			"parent" => "bron_tm"
		));	
		$t->define_field(array(
			"name" => "to",
			"caption" => t("Kuni"),
			"align" => "center",
			"parent" => "bron_tm"
		));
		$t->define_field(array(
			"name" => "to_ts",
			"caption" => t("Aja&uuml;hik"),
			"align" => "center",
			"parent" => "bron_tm"
		));

		$t->define_field(array(
			"name" => "col",
			"caption" => t("Broneeringu tegijate gruppide v&auml;rvid"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "ask_cust_arrived",
			"caption" => t("K&uuml;sida kliendi saabumise kinnitust"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "confirmed_default",
			"caption" => t("Broneeringud vaikimisi kinnitatud"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "no_time_check",
			"caption" => t("Ajakontrollita"),
			"align" => "center"
		));
	}

	function _get_grp_settings($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_grp_settings_t($t);

		$opts = array(
			"min" => t("Minut"),
			"hr" => t("Tund"),
			"day" => t("P&auml;ev")
		);
		$cols = $arr["obj_inst"]->meta("grp_cols");
		$settings = $arr["obj_inst"]->meta("grp_settings");

		$d = $arr["obj_inst"]->meta("grp_bron_tm");
		$ol = new object_list(array(
			"class_id" => CL_GROUP,
			"type" => "0",
			"lang_id" => array(),
			"site_id" => array()
		));
		foreach($ol->arr() as $o)
		{
			if (!is_array($settings[$o->id()]))
			{
				$settings[$o->id()] = array();
			}
			$t->define_data(array(
				"grp" => html::obj_change_url($o),
				"from" => html::textbox(array(
					"name" => "d[".$o->id()."][from]",
					"value" => $d[$o->id()]["from"],
					"size" => 5
				)),
				"to" => html::textbox(array(
					"name" => "d[".$o->id()."][to]",
					"value" => $d[$o->id()]["to"],
					"size" => 5
				)),
				"from_ts" => html::select(array(
					"name" => "d[".$o->id()."][from_ts]",
					"value" => $d[$o->id()]["from_ts"],
					"options" => $opts
				)),
				"to_ts" => html::select(array(
					"name" => "d[".$o->id()."][to_ts]",
					"value" => $d[$o->id()]["to_ts"],
					"options" => $opts
				)),
				"col" => html::textbox(array(
					"name" => "c[".$o->id()."]",
					"size" => 7,
					"value" => $cols[$o->id()],
				))." "."<a href=\"javascript:colorpicker('c_".$o->id()."_')\">".t("Vali")."</a>",
				"ask_cust_arrived" => html::checkbox(array(
					"name" => "e[".$o->id()."][ask_cust_arrived]",
					"value" => 1,
					"checked" => $settings[$o->id()]["ask_cust_arrived"]
				)),
				"confirmed_default" => html::checkbox(array(
					"name" => "e[".$o->id()."][confirmed_default]",
					"value" => 1,
					"checked" => $settings[$o->id()]["confirmed_default"]
				)),
				"no_time_check" => html::checkbox(array(
					"name" => "e[".$o->id()."][no_time_check]",
					"value" => 1,
					"checked" => $settings[$o->id()]["no_time_check"]
				)),
			));
		}
	}

	function _set_grp_settings($arr)
	{
		$arr["obj_inst"]->set_meta("grp_bron_tm", $arr["request"]["d"]);
		$arr["obj_inst"]->set_meta("grp_cols", $arr["request"]["c"]);
		$arr["obj_inst"]->set_meta("grp_settings", $arr["request"]["e"]);
	}

	/** Returns the default value for the verified property based on the given settings and current group
		@attrib api=1 params=pos

		@param settings required type=cl_room_settings
			The settings to read the default from

		@returns
			the default value for the reservation's verified property

	**/
	function get_verified_default_for_group($settings)
	{
		$grp_settings = safe_array($settings->meta("grp_settings"));
		$gl = aw_global_get("gidlist_pri_oid");
		arsort($gl);
		$gl = array_keys($gl);
		$grp = $gl[1];
		
		if (count($gl) == 1)
		{
			$grp = $gl[0];
		}
		
		return $grp_settings[$grp]["confirmed_default"];
	}

	function _set_bron_props($arr)
	{
		$calendar_bron_props = array();
		foreach($arr["request"]["calendar_bron_props"] as $prop => $data)
		{
			if($data["alt"] || $data["text"]) // vaid need mis on m2rgitud vaid kuhugi... l2bu pole vaja
			{
				$calendar_bron_props[$prop] = $data;
			}
		}
		$arr["obj_inst"]->set_meta("calendar_bron_props" , $calendar_bron_props);
	}


	function _get_bron_props($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "prop_name",
			"caption" => t("Omaduse nimi"),
		));
		$t->define_field(array(
			"name" => "text",
			"caption" => t("Tekstina"),
		));
		$t->define_field(array(
			"name" => "alt",
			"caption" => t("Alt-tekstina"),
		));
		$t->define_field(array(
			"name" => "before",
			"caption" => t("Eraldaja enne"),
		));
		$t->define_field(array(
			"name" => "after",
			"caption" => t("Eraldaja p&auml;rast"),
		));			
		$t->define_field(array(
			"name" => "jrk",
			"caption" => t("Jrk"),
		));

		$bol = new object_list(array(
			"class_id" => CL_RESERVATION,
			"lang_id" => array(),
			"limit" => 1,
		));
		$b = reset($bol->arr());
		$cfgu = $GLOBALS["object_loader"]->cfgu;
		$no_types = array("toolbar" , "submit" , "table");
		$calendar_bron_props = $arr["obj_inst"]->meta("calendar_bron_props");

		$props = $cfgu->propdef["property"] + $this->extra_calendar_bron_props;

		foreach($props as $prop => $data)
		{
			if(in_array($data["type"] , $no_types))
			{
				continue;
			}
			$t->define_data(array(
				"prop_name" =>$data["caption"] ? $data["caption"] : $prop,
				"prop" =>  $prop,
				"text" => html::checkbox(array(
					"name" => "calendar_bron_props[".$prop."][text]",
					"value" => 1,
					"checked" => $calendar_bron_props[$prop]["text"],
				)),
				"alt" => html::checkbox(array(
					"name" => "calendar_bron_props[".$prop."][alt]",
					"value" => 1,
					"checked" => $calendar_bron_props[$prop]["alt"],
				)),
				"before" => html::textbox(array(
					"name" => "calendar_bron_props[".$prop."][before]",
					"size" => 3,
					"value" => $calendar_bron_props[$prop]["before"],
				)),
				"after" => html::textbox(array(
					"name" => "calendar_bron_props[".$prop."][after]",
					"size" => 3,
					"value" => $calendar_bron_props[$prop]["after"],
				)),
				"jrk" => html::textbox(array(
					"name" => "calendar_bron_props[".$prop."][jrk]",
					"size" => 3,
					"value" => $calendar_bron_props[$prop]["jrk"],
				)),
				"ord" => $calendar_bron_props[$prop]["jrk"] ? (1000000000 + $calendar_bron_props[$prop]["jrk"]) : (($calendar_bron_props[$prop]["text"] || $calendar_bron_props[$prop]["alt"]) ? 2000000000 : 2000000001),
			));
		}
		$t->set_default_sortby("ord");
		$t->set_default_sorder("asc");
		$t->sort_by();
//		$t->set_sortable(false);
	}
}
?>
