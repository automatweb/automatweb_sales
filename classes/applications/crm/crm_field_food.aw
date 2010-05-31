<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_field_food.aw,v 1.4 2007/12/06 14:33:17 kristo Exp $
// crm_field_food.aw - Toitlustus (valdkond) 
/*

@classinfo syslog_type=ST_CRM_FIELD_FOOD no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default field=meta
@default method=serialize

@default group=general

	@property national_cuisine type=select
	@caption Rahvusk&ouml;&ouml;k
	
	@property location type=chooser
	@caption Asukoht
	
	@property loc_fromcity type=textbox default=0 size=5
	@caption Kaugus linnast (km)
	
	@property languages type=chooser multiple=1
	@caption Teeninduskeeled

	@property num_places type=textbox size=6
	@caption Kohtade arv

	@property num_tables type=textbox size=6
	@caption Laudade arv

	@property num_rooms type=textbox size=6
	@caption Ruumide arv

	@property has_ccards type=checkbox
	@caption Aktsepteeritakse krediitkaarte

	@property ign_spacer type=text store=no
	@caption

	@property for_disabled type=checkbox
	@caption Sobib liikumispuudega inimestele

	@property for_groups type=checkbox
	@caption Sobib gruppidele (alates 10 inimest)

	@property for_kids type=checkbox
	@caption Sobib lastele (lastemen&uuml;&uuml;, lastetoolid)
	
	@property has_nonsmoker_rooms type=checkbox
	@caption Suitsuvabad ruumid 
	
	@property ign_spacer2 type=text store=no
	@caption
	
	@property has_parking_places type=checkbox
	@caption Parkimiskohad
	
	@property has_parking type=checkbox
	@caption Parkla

@default group=extrainfo

	@property has_veg type=checkbox
	@caption Men&uuml;&uuml; taimetoitlastele

	@property has_catering type=checkbox
	@caption Catering teenus

	@property has_takeaway type=checkbox
	@caption Toidu kaasa ostmise v&otilde;imalus

	@property has_delivery type=checkbox
	@caption Toidu koju tellimise v&otilde;imalus
	
	@property has_live_music type=checkbox
	@caption Elav muusika
	
	@property has_tv type=checkbox
	@caption TV
	
	@property has_wifi type=checkbox
	@caption WiFi leviala
	
	@property has_internet type=checkbox
	@caption Interneti kasutamise v&otide;imalus
	
	@property has_outside_seating type=checkbox
	@caption V&auml;likohvik (hooajaliselt)
	
	@property has_terrace type=checkbox
	@caption Terrass

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

@groupinfo extrainfo caption="Lisainfo"
@groupinfo cedit caption="Kontaktandmed" 
@groupinfo images caption="Pildid" submit=no

@reltype EMAIL value=1 clid=CL_ML_MEMBER
@caption E-post

@reltype PHONE value=2 clid=CL_CRM_PHONE
@caption Telefon

@reltype TELEFAX value=3 clid=CL_CRM_PHONE
@caption Fax

@reltype IMAGE value=4 clid=CL_IMAGE
@caption Pilt

*/

class crm_field_food extends class_base
{
	const AW_CLID = 1030;

	function crm_field_food()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/crm/crm_field_food",
			"clid" => CL_CRM_FIELD_FOOD
		));
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
			case 'national_cuisine':
				$prop['options'] = array(
					'' => t("-- vali --"),
					'est' => t("Eesti"),
					'rus' => t("Vene"),
					'gru' => t("Gruusia"),
					'chi' => t("Hiina"),
					'ita' => t("Itaalia"),
					'tai' => t("Tai"),
				);
			break;
			case 'location':
				$prop["options"] = array(
					'loc_city' => t("Kesklinnas"),
					'loc_outside' => t("V&auml;ljaspool kesklinna"),
					'loc_country' => t("V&auml;ljaspool linna"),
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

		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
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
