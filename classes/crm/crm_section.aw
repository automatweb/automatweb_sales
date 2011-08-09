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

	@layout main_container type=hbox width=50%:50%
	@layout address_editor_container type=vbox area_caption=Aadressid parent=main_container
	@layout other_contact_data_container type=vbox area_caption=Muud&nbsp;kontaktandmed parent=main_container

	@property address_edit type=releditor mode=manager delete_objects=1 props=country,location_data,location,street,house,apartment,postal_code,po_box table_fields=name reltype=RELTYPE_LOCATION parent=address_editor_container no_caption=1

	@property phone_id type=relpicker reltype=RELTYPE_PHONE field=meta method=serialize parent=other_contact_data_container
	@caption Telefon

	@property telefax_id type=relpicker reltype=RELTYPE_TELEFAX field=meta method=serialize parent=other_contact_data_container
	@caption Faks

	@property email_id type=relpicker reltype=RELTYPE_EMAIL field=meta method=serialize parent=other_contact_data_container
	@caption E-posti aadress

	@property url type=relpicker reltype=RELTYPE_URL field=meta method=serialize parent=other_contact_data_container
	@caption Veebiaadress


@groupinfo wpls caption="T&ouml;&ouml;kohad"
@default group=wpls
	@property wpls type=relpicker reltype=RELTYPE_WORKPLACE multiple=1 store=connect automatic=1
	@caption T&ouml;&ouml;kohad


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
}

