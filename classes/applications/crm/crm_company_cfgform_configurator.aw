<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CRM_COMPANY_CFGFORM_CONFIGURATOR relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_crm_company_cfgform_configurator master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_company_cfgform_configurator
@default group=general

@property org_cfgform type=relpicker reltype=RELTYPE_ORG_CFGFORM store=connect
@caption Organisatsiooni seadete vorm

@property cfgview_action type=select store=no
@caption Lisamine/muutmine

@property ca_change type=chooser field=aw_ca_change
@caption Millist organisatsiooni muutmisvaates kuvada?

@property ca_change_org type=relpicker reltype=RELTYPE_CHANGE_ORG store=connect
@caption Muutmisvaates kuvatav organisatsioon

@property org_parent type=relpicker reltype=RELTYPE_ORG_PARENT store=connect
@caption Kaust, kuhu organisatsioon salvestatakse

@property make_user type=checkbox field=aw_make_user
@caption Tee organisatsioonile kasutaja

@property mu_add_to_groups type=relpicker reltype=RELTYPE_ADD_TO_GROUP multiple=1 store=connect
@caption Grupid, millega organisatsioonile loodud kasutaja liitub

@property mu_send_welcome_mail type=checkbox field=aw_mu_send_welcome_mail
@caption Saada kasutajale logimisandmetega e-kiri

@property mu_welcome_mail_to type=select field=aw_mu_welcome_mail_to
@caption Omadus, kust v&otilde;etakse e-postiaadress, millele logimisandmed saata

@property mu_welcome_mail_from type=textbox field=aw_mu_welcome_mail_from
@caption Logimisandmetega e-kirja saatja

@property mu_welcome_mail_subject type=textbox field=aw_mu_welcome_mail_subject
@caption Logimisandmetega e-kirja pealkiri

@property mu_welcome_mail_content type=textarea field=aw_mu_welcome_mail_content
@caption Logimisandmetega e-kirja sisu
@comment Kirja sisus saab kasutada %user ja %passwd. Nende asemele parsitakse kirja saatmisel vastavalt kasutajanimi ja parool.

@property mu_login type=checkbox field=aw_mu_login
@caption Logi p&auml;rast organisatsiooni salvestamist sisse

@property mu_generate_username type=select field=aw_mu_generate_username
@caption Kasutajanime genereerimise meetod

# @property subscribe_to_mailinglists type=checkbox field=aw_subscribe_to_mailinglists
# @caption Lisa organisatsiooni e-post mailinglisti(desse)

# @property stm_lists type=relpicker reltype=RELTYPE_STM_LISTS multiple=1 store=connect
# @caption Mailinglistid, millega liituda

# @property org_em_to_user type=checkbox field=aw_org_em_to_user
# @caption Pane organisatsiooni meiliaadress ka sisseloginud kasutaja meiliaadressiks

# @property org_em_to_person type=checkbox field=aw_org_em_to_person
# @caption Pane organisatsiooni meiliaadress ka sisseloginud isiku meiliaadressiks

# @property org_ph_to_person type=checkbox field=aw_org_ph_to_person
# @caption Pane organisatsiooni telefon ka sisseloginud isiku telefoniks

@property check_email type=checkbox field=aw_check_email default=1
@caption Kontrolli e-postiaadressi &otilde;igsust

@property sector_limit type=textbox field=aw_sector_limit
@caption Tegevusalade maksimaalne arv
@comment 0 - Kasutaja v&otilde;ib valida piiramatult tegevusalasid

@property create_customer_data type=checkbox field=aw_create_customer_data default=1
@caption Loo kliendisuhe

@property ccd_sample_object type=relpicker reltype=RELTYPE_CUSTOMER_DATA store=connect
@caption N&auml;idiskliendisuhe

###

@property controller_add_user type=hidden field=aw_controller_add_user
@caption Organisatsioonile kasutaja tegemise kontroller

@property controller_check_email type=hidden field=aw_controller_check_email
@caption E-postiaadressi &otilde;igsust kontrolliv kontroller

@property controller_sector_limit type=hidden field=aw_controller_sector_limit
@caption Tegevusalade arvu piirang

@property controller_customer_data type=hidden field=aw_controller_customer_data
@caption Kliendisuhte kontroller

###

@reltype ORG_CFGFORM value=1 clid=CL_CFGFORM
@caption Organisatsiooni seadete vorm

@reltype ORG_PARENT value=2 clid=CL_MENU
@caption Kaust, kuhu organisatsioon salvestatakse

@reltype CHANGE_ORG value=3 clid=CL_CRM_COMPANY
@caption Muutmisvaates kuvatav organisatsioon

@reltype STM_LISTS value=4 clid=CL_MENU
@caption Meilinglistid, millega liituda

@reltype ADD_TO_GROUP value=5 clid=CL_GROUP
@caption Kasutajagrupp, kuhu lisatakse loodud kasutaja

@reltype CUSTOMER_DATA value=6 clid=CL_CRM_COMPANY_CUSTOMER_DATA
@caption N&auml;idiskliendisuhe

*/

class crm_company_cfgform_configurator extends class_base
{
	const AW_CLID = 1515;

	function crm_company_cfgform_configurator()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_company_cfgform_configurator",
			"clid" => CL_CRM_COMPANY_CFGFORM_CONFIGURATOR
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		if(!in_array($prop["name"], array("org_cfgform", "name")) && !$this->can("view", $arr["obj_inst"]->org_cfgform))
		{
			$retval = PROP_IGNORE;
		}
		elseif($this->can("view", $arr["obj_inst"]->org_cfgform))
		{
			$o = obj($arr["obj_inst"]->org_cfgform);
			switch($prop["name"])
			{
				case "cfgview_action":
					$prop["options"] = array(
						"new" => t("Lisamine"),
						"change" => t("Muutmine"),
						"cfgview_change_new" => t("Lisamine ja muutmine"),
					);
					$prop["value"] = $o->cfgview_action;
					break;

				case "ca_change":
					$prop["options"] = array(
						1 => t("Kasuta sisseloginud kasutaja organisatsiooni"),
						2 => t("Kasuta m&auml;&auml;ratud organisatsiooni"),
					);
					if(!isset($prop["value"]) || !$prop["value"])
					{
						$prop["value"] = 1;
					}
					break;

				case "mu_generate_username":
					$prop["options"] = array("use_prop.fake_email" => sprintf(t("Kasuta omadust '%s' (%s)"), "Fake e-mail", "fake_email"));
					break;

				case "mu_welcome_mail_to":
					$prop["options"] = array("fake_email" => "asd");
					break;

				case "sector_limit":
					$prop["value"] = isset($prop["value"]) ? (int)$prop["value"] : 0;
					break;
			}
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		if(!in_array($prop["name"], array("org_cfgform", "name")) && !$this->can("view", $arr["obj_inst"]->org_cfgform))
		{
			$retval = PROP_IGNORE;
		}
		elseif($this->can("view", $arr["obj_inst"]->org_cfgform))
		{
			switch($prop["name"])
			{
				case "cfgview_action":
					$o = obj($arr["obj_inst"]->org_cfgform);
					$o->cfgview_action = $prop["value"];
					$o->save();
					break;

				// V22rtustatakse callback_post_save sees
				case "create_customer_data":
				case "ccd_sample_object":
				case "make_user":
				case "mu_send_welcome_mail":
				case "mu_login":
				case "mu_generate_username": 
				case "mu_add_to_groups":
				case "mu_welcome_mail_to":
				case "ca_change":
				case "check_email":
				case "sector_limit":
					return PROP_IGNORE;
					break;
			}
		}

		return $retval;
	}

	public function callback_post_save($arr)
	{
		if($this->can("view", $arr["obj_inst"]->org_cfgform))
		{
			$arr["obj_inst"]->gen_controller("change_params", $arr["request"]);
			$arr["obj_inst"]->gen_controller("add_user", $arr["request"]);
			$arr["obj_inst"]->gen_controller("check_email", $arr["request"]);
			$arr["obj_inst"]->gen_controller("sector_limit", $arr["request"]);
			$arr["obj_inst"]->gen_controller("customer_data", $arr["request"]);
		}
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_company_cfgform_configurator(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_controller_add_user":
			case "aw_controller_check_email":
			case "aw_controller_sector_limit":
			case "aw_controller_customer_data":

			case "aw_ca_change":
			case "aw_subscribe_to_mailinglists":
			case "aw_mu_send_welcome_mail":
			case "aw_make_user":
			case "aw_org_em_to_user":
			case "aw_org_em_to_person":
			case "aw_org_ph_to_person":
			case "aw_check_email":
			case "aw_mu_login":
			case "aw_sector_limit":
			case "aw_create_customer_data":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_mu_welcome_mail_to":
			case "aw_mu_generate_username":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(100)"
				));
				return true;

			case "aw_mu_welcome_mail_from":
			case "aw_mu_welcome_mail_subject":
			case "aw_mu_welcome_mail_content":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
		}

		return false;
	}
}

?>
