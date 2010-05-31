<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_field_other.aw,v 1.3 2007/12/06 14:33:17 kristo Exp $
// crm_field_other.aw - Muu (valdkond) 
/*

@classinfo syslog_type=ST_CRM_FIELD_OTHER relationmgr=yes no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

	@property location type=chooser field=meta method=serialize
	@caption Asukoht

	@property languages type=chooser multiple=1 field=meta method=serialize
	@caption Teeninduskeeled

	@property price_level type=chooser multiple=1 field=meta method=serialize
	@caption Hinnaklass

	@property comment type=textarea rows=5 cols=50
	@caption Kirjeldus

	@property crm_field type=select field=meta method=serialize
	@caption Tegevusala

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

class crm_field_other extends class_base
{
	const AW_CLID = 1114;

	function crm_field_other()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_field_other",
			"clid" => CL_CRM_FIELD_OTHER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
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
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}
}
?>
