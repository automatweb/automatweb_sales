<?php

// crm_document.aw - CRM Dokument
/*

@classinfo syslog_type=ST_CRM_DOCUMENT relationmgr=yes no_status=1 prop_cb=1

@default table=objects
@tableinfo aw_crm_document index=aw_oid master_index=brother_of master_table=objects


@default group=general

	@property project type=popup_search clid=CL_PROJECT table=aw_crm_document field=aw_project
	@caption Projekt

	@property task type=popup_search clid=CL_TASK table=aw_crm_document field=aw_task
	@caption &Uuml;lesanne

	@property customer type=popup_search clid=CL_CRM_COMPANY table=aw_crm_document field=aw_customer
	@caption Klient

	@property creator type=relpicker reltype=RELTYPE_CREATOR table=aw_crm_document field=aw_creator
	@caption Koostaja

	@property reader type=relpicker reltype=RELTYPE_READER table=aw_crm_document field=aw_reader
	@caption Lugeja

	@property state type=select table=aw_crm_document field=aw_state
	@caption Staatus

	@property reg_date type=date_select table=aw_crm_document field=aw_reg_date
	@caption Reg kuup&auml;ev

	@property make_date type=date_select table=aw_crm_document field=aw_make_date
	@caption Koostamise kuup&auml;ev

	@property reg_nr type=textbox table=aw_crm_document field=aw_reg_nr
	@caption Registreerimisnumber

	@property comment type=textarea rows=5 cols=50 table=objects field=comment
	@caption Kirjeldus

@default group=files

	@property files type=releditor reltype=RELTYPE_FILE field=meta method=serialize mode=manager props=filename table_fields=filename
	@caption Failid

@default group=parts

	@property parts_tb type=toolbar no_caption=1

	@property acts type=table store=no no_caption=1
	@caption Tegevused

@groupinfo acl caption=&Otilde;igused
@default group=acl

	@property acl type=acl_manager store=no
	@caption &Otilde;igused

@default group=notify

	@property sp_tb type=toolbar store=no no_caption=1

	@property sp_table type=table store=no
	@caption Valitud isikud

	@property sp_p_name type=textbox store=no
	@caption Isik

	@property sp_p_co type=textbox store=no
	@caption Organisatsioon

	@property sp_sbt type=submit
	@caption Otsi

	@property sp_s_res type=table store=no
	@caption Otsingu tulemused


@groupinfo files caption="Failid"
@groupinfo parts_main caption="Osalejad"

	@groupinfo parts caption="Osalejad" parent=parts_main
	@groupinfo notify caption="Teavitamine" parent=parts_main

@reltype FILE value=1 clid=CL_FILE
@caption fail

@reltype CREATOR value=2 clid=CL_CRM_PERSON
@caption looja

@reltype READER value=3 clid=CL_CRM_PERSON
@caption lugeja

@reltype ACTION value=8 clid=CL_CRM_DOCUMENT_ACTION
@caption Tegevus
*/

class crm_document extends class_base
{
	function crm_document()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_document",
			"clid" => CL_CRM_DOCUMENT
		));
	}

	function get_property($arr)
	{
		$b = get_instance("applications/crm/crm_document_base");
		$retval = $b->get_property($arr);

		$prop = &$arr["prop"];
		switch($prop["name"])
		{
			case "sp_tb":
				$this->_sp_tb($arr);
				break;

			case "sp_table":
				$this->_sp_table($arr);
				break;

			case "sp_s_res":
				$this->_sp_s_res($arr);
				break;

			case "sp_p_name":
			case "sp_p_co":
				$prop["value"] = $arr["request"][$prop["name"]];
				$prop["autocomplete_source"] = $this->mk_my_orb($prop["name"] == "sp_p_co" ? "co_autocomplete_source" : "p_autocomplete_source");
				$prop["autocomplete_params"] = array($prop["name"]);
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$b = get_instance("applications/crm/crm_document_base");
		$retval = $b->set_property($arr);

		$prop = &$arr["prop"];
		switch($prop["name"])
		{
		}
		return $retval;
	}

	function callback_post_save($arr)
	{
		if($arr["new"]==1 && is_oid($arr["request"]["project"]) && $this->can("view" , $arr["request"]["project"]))
		{
			$arr["obj_inst"]->set_prop("project" , $arr["request"]["project"]);
			$arr["obj_inst"]->save();
		}
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
//		if(!$arr["id"])
//		{
//			$arr["project"] = $_GET["project"];
//		}
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["sp_p_name"] = $arr["request"]["sp_p_name"];
		$arr["args"]["sp_p_co"] = $arr["request"]["sp_p_co"];
	}

	function _sp_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "add_s_res_to_p_list",
			"img" => "save.gif",
		));
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => "remove_p_from_l_list",
		));
		$tb->add_separator();
		$tb->add_button(array(
			"name" => "send_mail",
			"tooltip" => t("Saada teavitusmeil"),
			"img" => "",
			"action" => "send_notify_mail",
		));

	}

	function _init_p_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "co",
			"caption" => t("Organisatsioon"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("E-mail"),
			"align" => "center",
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _sp_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_p_tbl($t);

		foreach($this->get_people_list($arr["obj_inst"]) as $p_id => $p_nm)
		{
			$p = obj($p_id);
			$t->define_data(array(
				"name" => html::obj_change_url($p),
				"co" => html::obj_change_url($p->company_id()),
				"phone" => $p->prop("phone.name"),
				"email" => $p->prop("email.name"),
				"oid" => $p->id()
			));
		}
	}

	function _sp_s_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_p_tbl($t);

		if ($arr["request"]["sp_p_name"] != "" || $arr["request"]["sp_p_co"] != "")
		{
			$param = array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				"name" => "%".$arr["request"]["sp_p_name"]."%"
			);
			if ($arr["request"]["sp_p_co"] != "")
			{
				$param["CL_CRM_PERSON.RELTYPE_CURRENT_JOB.org.name"] = "%".$arr["request"]["sp_p_co"]."%";
			}
			$ol = new object_list($param);
			foreach($ol->arr() as $p)
			{
				$t->define_data(array(
					"name" => html::obj_change_url($p),
					"co" => html::obj_change_url($p->company_id()),
					"phone" => $p->prop("phone.name"),
					"email" => html::href(array("url" => "mailto:".$p->prop("email.mail"),"caption" => $p->prop("email.mail"))),
					"oid" => $p->id()
				));
			}
		}
	}

	/**
		@attrib name=add_s_res_to_p_list
	**/
	function add_s_res_to_p_list($arr)
	{
		$o = obj($arr["id"]);
		$persons = $o->meta("imp_p");
		foreach(safe_array($arr["sel"]) as $p_id)
		{
			$persons[aw_global_get("uid")][$p_id] = $p_id;
		}
		$o->set_meta("imp_p", $persons);
		$o->save();
		return $arr["post_ru"];
	}

	/**
		@attrib name=remove_p_from_l_list
	**/
	function remove_p_from_l_list($arr)
	{
		$o = obj($arr["id"]);
		$persons = $o->meta("imp_p");
		foreach(safe_array($arr["sel"]) as $p_id)
		{
			unset($persons[aw_global_get("uid")][$p_id]);
		}
		$o->set_meta("imp_p", $persons);
		$o->save();
		return $arr["post_ru"];
	}

	/**
		@attrib name=co_autocomplete_source
		@param sp_p_co optional
	**/
	function co_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"name" => $arr["sp_p_co"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 100
		));
		return $ac->finish_ac($ol->names());
	}

	/**
		@attrib name=p_autocomplete_source
		@param sp_p_p optional
	**/
	function p_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"name" => $arr["sp_p_p"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 200
		));
		return $ac->finish_ac($ol->names());
	}

	function get_people_list($bt)
	{
		$ret = array();
		$persons = $bt->meta("imp_p");
		$persons = safe_array($persons[aw_global_get("uid")]);

		if (!count($persons))
		{
			return array();
		}

		$ol = new object_list(array(
			"oid" => $persons,
			"lang_id" => array(),
			"site_id" => array()
		));
		return $ol->names();
	}

	/**
		@attrib name=send_notify_mail
	**/
	function send_notify_mail($arr)
	{
		$o = obj($arr["id"]);
		$ppl = $this->get_people_list($o);
		foreach($ppl as $oid => $nm)
		{
			$p = obj($oid);
			$email = $p->prop("email.mail");
			send_mail(
				$email,
				t("Teavitus muutunud dokumendist"),
				sprintf(t("Uuendati dokumenti \"%s\". Palun kliki siia:\n%s\net dokumenti n&auml;ha!"), $o->name(), html::get_change_url($o->id())),
				"From: ".aw_ini_get("baseurl")
			);
		}
		return $arr["post_ru"];
	}
}
