<?php

// crm_section.aw - &Uuml;ksus
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_CRM_COMPANY, on_disconnect_org_from_section)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_CRM_PERSON, on_connect_person_to_section)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_CRM_PERSON, on_disconnect_person_from_section)

@classinfo relationmgr=yes
@tableinfo aw_crm_section index=aw_oid master_table=objects master_index=oid

@property organization type=hidden table=aw_crm_section
@property parent_section type=hidden table=aw_crm_section

@default table=objects
@default group=general

@property description type=textarea rows=7 resize_height=-1 field=meta method=serialize
@caption Kirjeldus

@property code type=textbox size=4 field=meta method=serialize
@caption Kood

@property jrk type=textbox size=4
@caption J&auml;rk

@property ext_id type=textbox size=4 field=subclass
@caption Siduss&uuml;steemi ID

@property grp_crea type=chooser store=no multiple=1
@caption Kas teen grupid ja kasutajad

@property has_group type=checkbox ch_value=1 field=meta method=serialize
@caption Kas tehakse kasutajagrupp

@property has_group_subs type=checkbox ch_value=1 field=meta method=serialize
@caption Kas tehakse kasutajagrupp alamsektsioonidele

@property has_group_subs_prof type=checkbox ch_value=1 field=meta method=serialize
@caption Kas tehakse kasutajagrupp alamametinimetustele

@property link_document type=relpicker reltype=RELTYPE_LINK_DOCUMENT field=meta method=serialize
@caption Dokument

@property public type=checkbox ch_value=1 field=meta method=serialize
@caption Avalik


@groupinfo Kontaktid caption="Kontaktid"
@default group=Kontaktid
	//DEPRECATED
	@property contact type=hidden field=meta method=serialize

	layout main_container type=hbox width=50%:50%
	@layout other_contact_data_container type=vbox area_caption=Muud&nbsp;kontaktandmed parent=Kontaktid



	@property address_edit type=releditor mode=manager delete_objects=1 props=country,location_data,location,street,house,apartment,postal_code,po_box table_fields=name reltype=RELTYPE_LOCATION parent=address_editor_container no_caption=1

	@property phone_id type=relpicker reltype=RELTYPE_PHONE field=meta method=serialize parent=other_contact_data_container
	@caption Telefon

	@property telefax_id type=relpicker reltype=RELTYPE_TELEFAX field=meta method=serialize parent=other_contact_data_container
	@caption Faks

	@property email_id type=relpicker reltype=RELTYPE_EMAIL field=meta method=serialize parent=other_contact_data_container
	@caption E-posti aadress

	@property url type=relpicker reltype=RELTYPE_URL field=meta method=serialize parent=other_contact_data_container
	@caption Veebiaadress

	@property address type=hidden field=meta method=serialize no_caption=1


	@layout address_editor_container type=vbox area_caption=Aadressid parent=Kontaktid

		@property address_toolbar type=toolbar store=no no_caption=1 parent=address_editor_container

		@property cedit_adr_tbl type=table store=no no_caption=1 parent=address_editor_container




@groupinfo wpls caption="T&ouml;&ouml;kohad"
@default group=wpls
	@property wpls type=relpicker reltype=RELTYPE_WORKPLACE multiple=1 store=connect automatic=1
	@caption T&ouml;&ouml;kohad



@groupinfo open_hrs caption="Avamisajad"
@default group=open_hrs

	@property oh_tb type=toolbar no_caption=1 store=no
	@property oh_t type=table store=no no_caption=1


@groupinfo transl caption=T&otilde;lgi
@default group=transl
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi




@reltype JOB_OFFER value=4 clid=CL_PERSONNEL_MANAGEMENT_JOB_OFFER
@caption T&ouml;&ouml;pakkumine

@reltype GROUP value=5 clid=CL_GROUP
@caption grupp

@reltype EMAIL value=7 clid=CL_ML_MEMBER
@caption E-post

@reltype PHONE value=8 clid=CL_CRM_PHONE
@caption Telefon

@reltype TELEFAX value=9 clid=CL_CRM_PHONE
@caption Fax

@reltype LINK_DOCUMENT value=10 clid=CL_DOCUMENT
@caption Dokument

@reltype WORKPLACE value=11 clid=CL_ROSTERING_WORKPLACE
@caption T&ouml;&ouml;koht

@reltype URL value=12 clid=CL_EXTLINK
@caption Veebiaadress

@reltype LOCATION value=13 clid=CL_ADDRESS
@caption Asukoht

@reltype OPENHOURS value=14 clid=CL_OPENHOURS
@caption Avamisajad

//DEPRECATED
@reltype SECTION value=1 clid=CL_CRM_SECTION
@caption (pole kasutusel)
// @caption Alam&uuml;ksus
//DEPRECATED
@reltype WORKERS value=2 clid=CL_CRM_PERSON
@caption (pole kasutusel)
// @caption Liige
//DEPRECATED
@reltype PROFESSIONS value=3 clid=CL_CRM_PROFESSION
@caption (pole kasutusel)
// @caption Roll
//DEPRECATED
@reltype ADDRESS value=6 clid=CL_CRM_ADDRESS
// @caption Kontaktaadress
@caption (pole kasutusel)

*/

class crm_section extends class_base
{
	protected $trans_props = array(
		"name"
	);

	function crm_section()
	{
		$this->init(array(
			"clid" => CL_CRM_SECTION
		));
	}

	function get_folders_as_object_list($o, $level, $parent)
	{
		// I need all objects that target this one
		// $o - is the sector object
		$conns = $o->connections_to(array(
			"from.class_id" => CL_CRM_PERSON
		));
		$ol = new object_list();
		foreach($conns as $conn)
		{
			$ol->add($conn->prop("from"));
		}
		return $ol;
	}

	function make_menu_link($o)
	{
		// right, now I need to implement the proper code
		// need to figure out the section!
		$sect = $o->prop("sect");
		return $this->mk_my_orb("show",array("id" => $o->id(),"section" => aw_global_get("section")), "crm_person");
	}


	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "address_edit":
				return PROP_IGNORE;
			case "address_toolbar":
				$tb =&$arr["prop"]["toolbar"];

				$tb->add_button(array(
					"name" => "new",
					"img" => "new.gif",
					"tooltip" => t("Lisa uus aadress"),
					"url" => $this->mk_my_orb("new", array(
						"alias_to" => $arr["obj_inst"]->id(),
						"reltype" => 13,
						"return_url" => get_ru(),
						"parent" => $arr["obj_inst"]->id()
					), CL_ADDRESS),
				));
				$tb->add_button(array(
					"name" => "delete",
					"img" => "delete.gif",
					"tooltip" => t("Kustuta aadressid"),
					"action" => "delete_selected_objects",
					"confirm" => t("Oled kindel, et kustutada?"),
				));
				break;
			case "cedit_adr_tbl":
				$i = new crm_company_cedit_impl();
				$t = $arr["prop"]["vcl_inst"];
				$fields = array(
					"comment" => t("L&uuml;hinimi"),
					"country" => t("Riik"),
					"county" => t("Maakond"),
					"city" => t("Linn"),
					"vald" => t("Vald"),
					"street" => t("T&auml;nav/k&uuml;la"),
					"house" => t("Maja"),
					"apartment" => t("Korter"),
					"postal_code" => t("Postiindeks"),
					"po_box" => t("Postkast")
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_adr_tbl($t, $arr);
				$t->set_caption(t("Aadressid"));
				break;

			case "oh_t":
				$t =& $arr["prop"]["vcl_inst"];
				$this->_init_oh_t($t);
				$t->set_caption(t("Avamisajad"));
				foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_OPENHOURS")) as $c)
				{
					$oh = $c->to();
					$i = $oh->instance();
					$t->define_data(array(
						"name" => $oh->name(),
						"apply_group" => $oh->prop_str("apply_group"),
						"oh" => $i->show(array("id" => $oh->id())),
						"edit" => html::get_change_url($oh->id(), array("return_url" => get_ru()), t("Muuda")),
						"oid" => $oh->id(),
						"date_from" => $oh->prop("date_from"),
						"date_to" => $oh->prop("date_to"),
					));
				}
				break;
			case "oh_tb":
				$t = $arr["prop"]["vcl_inst"];
				$t->add_menu_button(array(
					"name" => "new",
					"img" => "new.gif"
				));
				$t->add_menu_item(array(
					"parent" => "new",
					"text" => t("Avamisaeg"),
					"link" => html::get_new_url(CL_OPENHOURS, $arr["obj_inst"]->id(), array("alias_to" => $arr["obj_inst"]->id(), "reltype" => 14, "return_url" => get_ru())),
				));

				$t->add_button(array(
					"name" => "remove_oh",
					"tooltip" => t("Eemalda avamisaeg"),
					"img" => "delete.gif",
					"action" => "remove_oh",
				));
				break;
			case "has_group":
			case "has_group_subs":
			case "has_group_subs_prof":
				return PROP_IGNORE;
				break;

			case "grp_crea":
				$prop["options"] = array(
					"has_group" => t("sellele &uuml;ksusele"),
					"has_group_subs" => t("alam&uuml;ksustele"),
					"has_group_subs_prof" => t("ametinimetustele")
				);
				$prop["value"]["has_group"] = $arr["obj_inst"]->prop("has_group");
				$prop["value"]["has_group_subs"] = $arr["obj_inst"]->prop("has_group_subs");
				$prop["value"]["has_group_subs_prof"] = $arr["obj_inst"]->prop("has_group_subs_prof");
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
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "has_group":
			case "has_group_subs":
			case "has_group_subs_prof":
				return PROP_IGNORE;
				break;

			case "grp_crea":
				$arr["obj_inst"]->set_prop("has_group", isset($prop["value"]["has_group"]) ? 1 : 0);
				$arr["obj_inst"]->set_prop("has_group_subs", isset($prop["value"]["has_group_subs"]) ? 1 : 0);
				$arr["obj_inst"]->set_prop("has_group_subs_prof", isset($prop["value"]["has_group_subs_prof"]) ? 1 : 0);
				break;
			case "cedit_adr_tbl":
				static $i;
				if (!$i)
				{
					$i = new crm_company_cedit_impl();
				}
				$fn = "_set_".$prop["name"];
				$i->$fn($arr);
				break;
		}
		return $retval;
	}

	function get_all_org_job_ids($org_oid)
	{
		$obj = obj($org_oid);
		foreach ($obj->connections_from(array("type" => 19)) as $job)
		{
			$job_ids[$job->prop("to")] = "";
		}

		foreach ($obj->connections_from(array("type" => 28)) as $sector)
		{
			$jobs_ids_temp = $this->get_section_job_ids_recursive($sector->prop("to"));
			$professions_temp = $this->get_professions($sector->prop("to"), true);

			if(is_array($jobs_ids_temp))
			{
				foreach ($jobs_ids_temp as $key=>$value)
				{
					$job_ids[$key] = $value;
				}

				foreach ($professions_temp as $key=>$value)
				{
					$professions[$key] = $value;
				}
			}
		}
		return  $job_ids;
	}

	function get_all_org_professions($org_id, $recrusive=false)
	{
		$obj = obj($org_id);
		foreach ($obj->connections_from(array("type" => "RELTYPE_PROFESSIONS")) as $prof_conn)
		{
			$rtrn[$prof_conn->prop('to')] = $prof_conn->prop('to.name');
		}

		if($recrusive)
		{
			foreach ($obj->connections_from(array("type" => 28)) as $sector)
			{
				$temp = $this->get_professions($sector->prop("to"), true);
				foreach ($temp as $key=>$value)
				{
					$rtrn[$key] = $value;
				}
			}
		}
		return $rtrn;
	}

	/*
		$id - object id
	*/
	function get_professions($id, $recursive = false)
	{
		static $rtrn;

		if($recursive == false)
		{
			$obj = new object($id);
			$rtrn = array();
			$conns = $obj->connections_from(array(
				'type' => 'RELTYPE_PROFESSIONS'
			));
			foreach($conns as $conn)
			{
				$rtrn[$conn->prop('to')] = $conn->prop('to.name');
			}
		}
		else
		{	//Case recursion
			$obj = new object($id);
			$conns = $obj->connections_from(array(
				'type' => 'RELTYPE_PROFESSIONS'
			));

			foreach($conns as $conn)
			{
				$rtrn[$conn->prop('to')] = $conn->prop('to.name');
			}

			if($sub_sections = $obj->connections_from(array("type" => 1)))
			{
				foreach ($sub_sections as $sub_section)
				{
					$this->get_professions($sub_section->prop("to"), true);
				}
			}
		}
		return $rtrn;
	}

	// DEPRECATED. use crm_company_obj::get_employees() with section argument
	function get_section_workers($section_id, $recrusive = false)
	{
		static $retval;
		$section = obj($section_id);
		if(!$retval)
		{
			$retval = $section->get_workers();
		}
		else
		{
			$retval->add($section->get_workers());
		}

		if($recrusive)
		{
			foreach ($section->connections_from(array("type" => "RELTYPE_SECTION")) as $subsection)
			{
				$this->get_section_workers($subsection->prop("to"), true);
			}
		}
		else
		{
		//fuck this, im too lazy to lazy to think and do it corretly
			$retval = $section->get_workers();
		}
		return $retval;
	}

	function get_section_job_ids_recursive($unit_id)
	{
		static $jobs_ids;

		$section_obj = obj($unit_id);

		foreach ($section_obj->connections_from(array("type" => "RELTYPE_JOB_OFFER")) as $joboffer)
		{
			$jobs_ids[$joboffer->prop("to")] = $section_obj->name();
		}

		//If section has any subsections...get jobs from there too
		if($sub_sections = $section_obj->connections_from(array("type" => 1)))
		{
			foreach ($sub_sections as $sub_section)
			{
				$this->get_section_job_ids_recursive($sub_section->prop("to"));
			}
		}
		return $jobs_ids;
	}

	function get_section_job_ids($unit_id)
	{
		$section_obj = obj($unit_id);
		foreach ($section_obj->connections_from(array("type" => "RELTYPE_JOB_OFFER")) as $joboffer)
		{
			$jobs_ids[] = $joboffer->prop("to");
		}
		return $jobs_ids;
	}


	// Invoked when a connection from organization to section is removed
	// .. this will then remove the opposite connection as well if one exists
	function on_disconnect_org_from_section($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_SECTION)
		{
			if($target_obj->is_connected_to(array('from' => $conn->prop('from'))))
			{
				$target_obj->disconnect(array(
					"from" => $conn->prop("from"),
					"errors" => false
				));
			}
		}
	}

	// Invoked when a connection is created from person to section
	// .. this will then create the opposite connection.
	function on_connect_person_to_section($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_SECTION)
		{
			$target_obj->connect(array(
				"to" => $conn->prop("from"),
				"reltype" => 2, //crm_section.reltype_section
			));
		}
	}

	function on_disconnect_person_from_section($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_SECTION)
		{
			if($target_obj->is_connected_to(array('to'=>$conn->prop('from'))))
			{
				$target_obj->disconnect(array(
					"from" => $conn->prop("from"),
				));
			}
		}
	}

	function callback_mod_tab($arr)
	{
		$trc = aw_ini_get("user_interface.trans_classes");

		if ($arr["id"] === "transl" && (aw_ini_get("user_interface.content_trans") != 1 && empty($trc[$this->clid])))
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		$ret_val = false;
		$migrate = false;

		if ("aw_crm_section" === $table)
		{
			if (empty($field))
			{
				$this->db_query("
					CREATE TABLE `aw_crm_section` (
						`aw_oid` int(11) UNSIGNED NOT NULL default '0',
						`organization` int(11) UNSIGNED NOT NULL default '0',
						`parent_section` int(11) UNSIGNED NOT NULL default '0',
						PRIMARY KEY (`aw_oid`),
						INDEX (`organization`, `parent_section`)
					);
				");
				$ret_val = true;
			}
		}

		return $ret_val;
	}



	/* avamisajad */
	


	function _init_oh_t(&$t,$pause)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "date_from",
			"caption" => t("Kehtib alates"),
			"align" => "center",
			"width" => "10%",
			"type" => "time",
			"format" => "d.m.Y"
		));
		$t->define_field(array(
			"name" => "date_to",
			"caption" => t("Kehtib kuni"),
			"align" => "center",
			"width" => "10%",
			"type" => "time",
			"format" => "d.m.Y"
		));
		$t->define_field(array(
			"name" => "apply_group",
			"caption" => t("Kehtib gruppidele"),
			"align" => "center",
			"width" => "50%"
		));
		$t->define_field(array(
			"name" => "oh",
			"caption" => $pause ? t("Pausid") : t("Avamisajad"),
			"align" => "center",
			"width" => "50%"
		));
		$t->define_field(array(
			"name" => "edit",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => "100"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"width" => "20"
		));
	}

	/**
		@attrib name=remove_oh params=name all_args=1
	**/
	function remove_oh($arr)
	{
		$o = obj($arr["id"]);
		if(count($arr["sel"]))
		{
			$o->disconnect(array(
				"from" => $arr["sel"]
			));
		}
		return $arr["post_ru"];
	}


	/**
		@attrib name=delete_selected_objects
	**/
	function delete_selected_objects($arr)
	{
		$selected_objects = $errors = array();
		if (!empty($arr["select"]))
		{
			$selected_objects += $arr["select"] ;
		}

		if (!empty($arr["cust_check"]))
		{
			$selected_objects += $arr["cust_check"] ;
		}

		if (!empty($arr["cat_check"]))
		{
			$selected_objects += $arr["cat_check"] ;
		}

		foreach ($selected_objects as $delete_obj_id)
		{
			if (object_loader::can("delete", $delete_obj_id))
			{
				$deleted_obj = obj($delete_obj_id);
				$deleted_obj->delete();
			}
			else
			{
				$errors[] = $delete_obj_id;
			}
		}

		if (count($errors))
		{
			$this->show_error_text(sprintf(t("Objekte %s ei saanud kustutada."), implode(", ", $errors)));
		}

		// return url
		$r = empty($arr["post_ru"]) ? $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"org_id" => isset($arr["offers_current_org_id"]) ? $arr["offers_current_org_id"] : 0),
			$arr["class"]
		) : $arr["post_ru"];
		return $r;
	}


}

