<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_field_accommodation.aw,v 1.6 2007/12/06 14:33:17 kristo Exp $
// crm_field_accommodation.aw - Majutusettev&otilde;te (valdkond) 
/*

INFO ABOUT CRM_FIELD_* CLASSES:
Creating a new class: 
  oh: make sure it's name starts with 'crm_field_'
 one: you should copy the common properties from one of the existing classes
 two: register the class in following places:
	crm_company properties definition for reltype FIELD
	crm_company_webview::_get_company_show_html variable definition for $crm_field_titles

Modifing classes:
 one: keep the common properties in sync over all crm_field_* classes





@classinfo syslog_type=ST_CRM_FIELD_ACCOMMODATION no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default field=meta
@default method=serialize

@default group=general

	@property type type=select
	@caption Liik

	@property location type=chooser
	@caption Asukoht
	
	@property loc_fromcity type=textbox default=0 size=5
	@caption Kaugus linnast (km)

	@property languages type=chooser multiple=1
	@caption Teeninduskeeled

	@property price_level type=chooser multiple=1
	@caption Hinnaklass
	
	@property price_txt type=textbox size=5
	@caption Hinnavahemik

	@property num_rooms type=textbox size=5
	@caption Tubade arv

	@property num_beds type=textbox size=5
	@caption Voodikohtade arv

	@property has_showers type=checkbox
	@caption Pesemisv&otilde;imalus toas

	@property has_tv type=checkbox 
	@caption Teler toas
	
	@property has_sat_tv type=checkbox 
	@caption SAT-TV

	@property has_phone type=checkbox 
	@caption Telefon toas
	
	@property has_phone_service type=checkbox 
	@caption Telefoniteenus

	@property has_spneeds_rooms type=checkbox
	@caption Invatoad

	@property has_family_rooms type=checkbox
	@caption Peretoad

	@property has_allergy_rooms type=checkbox
	@caption Allergikute toad

	@property has_nonsmoker_rooms type=checkbox
	@caption Mittesuitsetajate toad

	@property has_ccards type=checkbox
	@caption Aktsepteeritakse krediitkaarte

	@property has_pets type=checkbox
	@caption Lemmikloomad lubatud

	@property has_parking type=checkbox
	@caption Parkla

	@property has_parking_safe type=checkbox
	@caption Valvega parkla

	@property has_garage type=checkbox
	@caption Garaa&#158;

	@property has_seminar_rooms type=checkbox
	@caption Seminari- ja/v&otilde;i konverentsiruumid


@default group=services

	@property has_extra_beds type=checkbox
	@caption Lisavoodi v&otilde;imalus

	@property has_baby_beds type=checkbox
	@caption Beebivoodi v&otilde;imalus

	@property has_cur_xch type=checkbox
	@caption Valuutavahetus

	@property has_wifi type=checkbox
	@caption WiFi leviala

	@property has_internet type=checkbox
	@caption Interneti kasutamise v&otilde;imalus

	@property has_safety_boxes type=checkbox
	@caption Hoiulaekad

	@property has_safe type=checkbox
	@caption Seif

	@property has_sauna type=checkbox
	@caption Saun

	@property has_services_beauty type=checkbox
	@caption Iluteenused

	@property has_services_heal type=checkbox
	@caption Raviteenused

	@property has_washing type=checkbox
	@caption Pesu pesemisv&otilde;imalus

	@property has_services_carrental type=checkbox
	@caption Autorent

	@property has_services_transport type=checkbox
	@caption Transporditeenus

	@property has_services_guides type=checkbox
	@caption Giiditeenus

	@property ign_spacer type=text store=no
	@caption 

	@property has_grill type=checkbox
	@caption L&otilde;kkeplats/grill

	@property has_camping_tent type=checkbox
	@caption Telkimisv&otilde;imalus

	@property has_camping_trailer type=checkbox
	@caption Haagissuvilaga peatumise v&otilde;imalus

	@property has_camping_caravan type=checkbox
	@caption Karavanikohad

	@property has_camping_rentatent type=checkbox
	@caption Telkide laenutamise v&otilde;imalus


@default group=catering

	@property food_breakfast_included type=checkbox
	@caption Hommikus&ouml;&ouml;k hinna sees

	@property food_breakfast_canorder type=checkbox
	@caption Hommikus&ouml;&ouml;k tellimisel

	@property food_restaurant type=checkbox
	@caption Restoran

	@property food_cafe type=checkbox
	@caption Kohvik

	@property food_bar type=checkbox
	@caption Lobby baar

	@property food_use_kitchen type=checkbox
	@caption Toidu valmistamise v&otilde;imalus


@default group=active_vacation

	@property has_playground type=checkbox
	@caption Laste m&auml;nguv&auml;ljak

	@property has_sporting_ground type=checkbox
	@caption Spordiv&auml;ljak

	@property has_tennis type=checkbox
	@caption Tennisev&auml;ljak

	@property has_ballgames type=checkbox
	@caption Pallim&auml;ngud

	@property has_horseriding type=checkbox
	@caption Ratsutamine

	@property has_rentabike type=checkbox
	@caption Jalgrattalaenutus

	@property has_rentafloatingvehicle type=checkbox
	@caption Vees&otilde;iduki laenutus

	@property has_swimming_out type=checkbox
	@caption Ujumisv&otilde;imalus (v&auml;litingimustes)

	@property has_fishing type=checkbox
	@caption Kalastamine

	@property has_rentarod type=checkbox
	@caption Kalastustarvete rent

	@property has_hiking type=checkbox
	@caption Matkarajad

	@property has_skiing type=checkbox
	@caption Suusarajad

	@property has_renttwoskis type=checkbox
	@caption Suusalaenutus

@default group=cedit

	@property phone_id type=relmanager reltype=RELTYPE_PHONE props=name override_parent=this
	@caption Telefon

	@property telefax_id type=relmanager reltype=RELTYPE_TELEFAX props=name override_parent=this
	@caption Faks

	@property email_id type=relmanager reltype=RELTYPE_EMAIL props=mail override_parent=this
	@caption E-posti aadressid

@default group=images

	@property images type=releditor reltype=RELTYPE_IMAGE field=meta method=serialize mode=manager props=name,ord,status,file,file2,new_w,new_h,new_w_big,new_h_big,comment table_fields=name,ord table_edit_fields=ord override_parent=this direct_links=1 
	@caption Pildid

@default group=transl
	
	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

@groupinfo products caption="Tooted" submit=no

@groupinfo services caption="Lisateenused"
@groupinfo catering caption="Toitlustamine" 
@groupinfo active_vacation caption="Aktiivne puhkus" 
@groupinfo cedit caption="Kontaktandmed" 
@groupinfo images caption="Pildid" submit=no
@groupinfo transl caption=T&otilde;lgi

@reltype EMAIL value=1 clid=CL_ML_MEMBER
@caption E-post

@reltype PHONE value=2 clid=CL_CRM_PHONE
@caption Telefon

@reltype TELEFAX value=3 clid=CL_CRM_PHONE
@caption Fax

@reltype IMAGE value=4 clid=CL_IMAGE
@caption Pilt


*/

class crm_field_accommodation extends class_base
{
	function crm_field_accommodation()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/crm/crm_field_accommodation",
			"clid" => CL_CRM_FIELD_ACCOMMODATION
		));

		$this->trans_props = array(
			"name", "comment"
		);

	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case 'type':
				$prop["options"] = array(
					'tp_hotel' => t("Hotell"),
					'tp_motel' => t("Motell"),
					'tp_guesthouse' => t("K&uuml;lalistemaja"),
					'tp_hostel' => t("Hostel"),
					'tp_camp' => t("Puhkek&uuml;la ja -laager"),
					'tp_wayhouse' => t("Puhkemaja"),
					'tp_apartment' => t("K&uuml;laliskorter"),
					'tp_homestay' => t("Kodumajutus"),
				);
			break;
			case 'location':
				$prop["options"] = array(
					'loc_city' => t("Kesklinnas"),
					'loc_outside' => t("V&auml;ljaspool kesklinna"),
					'loc_country' => t("V&auml;ljaspool linna"),
				);
			break;
			case 'price_level':
				$prop["options"] = array(
					'price_A' => t("A"),
					'price_B' => t("B"),
					'price_C' => t("C"),
					'price_D' => t("D"),
					'price_E' => t("E"),
				);
			break;
			case 'languages':
				$langs = aw_ini_get('languages.list');
				$prop["options"] = array();
				foreach ($langs as $lang)
				{
					$prop["options"][$lang['acceptlang']] = t($lang['name']);
				}
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
			//-- set_property --//
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

		}
		return $retval;
	}	

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
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
}
?>
