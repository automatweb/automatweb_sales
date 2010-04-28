<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/personnel_management/personnel_management_job_wanted.aw,v 1.21 2008/12/18 11:15:20 instrumental Exp $
// personnel_management_job_wanted.aw - T&ouml;&ouml; soov
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_CRM_PERSON, on_connect_person_to_job_wanted)

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_CRM_PERSON, on_disconnect_person_from_job_wanted)

@classinfo syslog_type=ST_PERSONNEL_MANAGEMENT_JOB_WANTED relationmgr=yes r2=yes no_status=1 no_comment=1 maintainer=instrumental
@tableinfo personnel_management_job_wanted master_table=objects master_index=oid index=oid

@default table=personnel_management_job_wanted
@default group=general

@property person type=relpicker reltype=RELTYPE_PERSON store=connect
@caption Isik

@property field type=classificator multiple=1 reltype=RELTYPE_FIELD orient=vertical store=connect sort_callback=CL_PERSONNEL_MANAGEMENT::cmp_function
@caption Tegevusala (muutuja)

@property sector type=relpicker reltype=RELTYPE_SECTOR multiple=1 store=connect
@caption Tegevusala (tegevusala objekt)

@property job_type type=classificator multiple=1 reltype=RELTYPE_JOB_TYPE store=connect sort_callback=CL_PERSONNEL_MANAGEMENT::cmp_function
@caption T&ouml;&ouml; liik

@property professions_rels type=relpicker reltype=RELTYPE_PROFESSION multiple=1 store=connect no_edit=1
@caption Soovitavad ametid

@property professions type=textarea field=ametinimetus
@caption Soovitavad ametid

@property load type=hidden field=koormus
@caption T&ouml;&ouml;koormus

@property load2 type=classificator reltype=RELTYPE_LOAD store=connect
@caption T&ouml;&ouml;koormus

@property pay type=textbox size=5 datatype=int field=palgasoov
@caption Palgasoov (arv)

#@property pay2 type=textbox size=5 datatype=int field=palgasoov2
#@caption Palgasoov kuni (arv)

@property pay_text type=textbox field=palgasoov_txt
@caption Palgasoov (tekst)

@property work_by_schedule type=checkbox ch_value=1
@caption Olen n&otilde;us t&ouml;&ouml;tama graafiku alusel

@property work_at_night type=checkbox ch_value=1
@caption Olen n&otilde;us t&ouml;&ouml;tama &ouml;&ouml;sel

@property ready_for_errand type=checkbox ch_value=1
@caption Olen valmis t&ouml;&ouml;l&auml;hetusteks

@property location type=relpicker multiple=1 orient=vertical store=connect reltype=RELTYPE_LOCATION
@caption T&ouml;&ouml; asukoht
@comment Esimene valik

@property location_2 type=relpicker multiple=1 orient=vertical store=connect reltype=RELTYPE_LOCATION2
@caption T&ouml;&ouml; asukoht
@comment Teine valik

@property location_text type=textbox
@caption T&ouml;&ouml; asukoht
@comment Vajadusel t&auml;psusta

@property start_working type=select
@caption Soovitud t&ouml;&ouml;le asumise aeg

@property additional_skills type=textarea
@caption T&auml;iendavad oskused
@comment Kas Teil on t&auml;iendavaid oskusi, mida te peate vajalikuks &auml;ra m&auml;rkida?

@property handicaps type=textarea
@caption Tegurid, mis ei v&otilde;imalda m&otilde;nda t&ouml;&ouml;&uuml;lesannet t&auml;ita

@property hobbies_vs_work type=textbox
@caption Hobid, mille t&otilde;ttu on vajalik t&ouml;&ouml;lt eemal viibida

@property addinfo type=textarea field=lisainfo
@caption Lisainfo soovitava t&ouml;&ouml; kohta

@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption T&ouml;&ouml;soovija

@reltype FIELD value=2 clid=CL_META
@caption Valdkond

@reltype LOAD value=3 clid=CL_META
@caption T&ouml;&ouml;koormus

@reltype LOCATION value=4 clid=CL_CRM_CITY,CL_CRM_COUNTY,CL_CRM_COUNTRY,CL_CRM_AREA
@caption Asukoht (esimene valik)

@reltype JOB_TYPE value=5 clid=CL_META
@caption T&ouml;&ouml; liik

@reltype PROFESSION value=6 clid=CL_CRM_PROFESSION
@caption Ametinimetus

@reltype LOCATION2 value=7 clid=CL_CRM_CITY,CL_CRM_COUNTY,CL_CRM_COUNTRY,CL_CRM_AREA
@caption Asukoht (teine valik)

@reltype SECTOR value=8 clid=CL_CRM_SECTOR
@caption Tegevusvaldkond

*/

class personnel_management_job_wanted extends class_base
{
	const AW_CLID = 351;

	function personnel_management_job_wanted()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"clid" => CL_PERSONNEL_MANAGEMENT_JOB_WANTED
		));

		$this->start_working_options = array(
			 0 => t("--vali--"),
			 1 => t("Kohe"),
			 2 => t("1 kuu jooksul"),
			 3 => t("2 kuu jooksul"),
			 4 => t("Kokkuleppel"),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch ($prop["name"])
		{
			case "sector":
				$prop["options"] = get_instance(CL_PERSONNEL_MANAGEMENT)->get_sectors();
				break;

			case "location":
			case "location_2":
				$pm_inst = get_instance(CL_PERSONNEL_MANAGEMENT);
				$pm_id = $pm_inst->get_sysdefault();
				if($this->can("view", $pm_id))
				{
					$pm = obj($pm_id);
					$conf = $pm->prop($prop["name"]."_conf");
					if(is_array($conf) && count($conf))
					{
						$ops = $pm_inst->get_locations($conf);
						$prop["options"] = safe_array($prop["options"]) + safe_array($ops);
					}
				}
				break;

			case "load":
				$r = get_instance(CL_CLASSIFICATOR)->get_choices(array(
					"clid" => CL_PERSONNEL_MANAGEMENT,
					"name" => "cv_load",
					"sort_callback" => "CL_PERSONNEL_MANAGEMENT::cmp_function",
				));
				$prop["options"] = $r[4]["list_names"];
				break;

			case "start_working":
				$prop["options"] = $this->start_working_options;
				break;

			case "professions_rels":
				$pm_inst = get_instance(CL_PERSONNEL_MANAGEMENT);
				$pm_id = $pm_inst->get_sysdefault();
				if(is_oid($pm_id))
				{
					$pm_obj = obj($pm_id);
					if(is_oid($pm_obj->prop("professions_fld")))
					{
						$ol = new object_list(array(
							"parent" => $pm_obj->prop("professions_fld"),
							"class_id" => CL_CRM_PROFESSION,
							"status" => object::STAT_ACTIVE,
							"lang_id" => array(),
						));
						//$prop["options"] = $ol->names();
						$objs = $ol->arr();
						enter_function("uasort");
						uasort($objs, array(get_instance(CL_PERSONNEL_MANAGEMENT), "cmp_function"));
						exit_function("uasort");
						foreach($objs as $o)
						{
							$prop["options"][$o->id()] = $o->trans_get_val("name");
						}
					}
				}
				break;

			case "sbutton":
				if(is_numeric($_GET["eoid"]))
				{
					$prop["caption"] = "Muuda";
				}
			break;

			case "candidate_toolbar":
				$prop["vcl_inst"]->add_button(array(
					"name" => "add",
					"caption" => t("Lisa"),
					"img" => "new.gif",
				));
				break;

			case "candidate_table":
				$prop["vcl_inst"]->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
				));
				$prop["vcl_inst"]->define_data(array(
					"name" => "test",
				));
				break;
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl == "personnel_management_job_wanted" && $field == "")
		{
			$this->db_query("create table personnel_management_job_wanted (oid int primary key)");
			return true;
		}

		switch($field)
		{
			case "ametinimetus":
			case "lisainfo":
			case "location_text":
			case "hobbies_vs_work":
			case "handicaps":
			case "additional_skills":
			case "palgasoov_txt":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "text"
				));
				return true;

			case "koormus":
			case "palgasoov":
			case "palgasoov2":
			case "work_by_schedule":
			case "work_at_night":
			case "ready_for_errand":
			case "start_working":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int"
				));
				return true;
		}
	}

	function on_connect_person_to_job_wanted($arr)
	{
		$conn = $arr['connection'];
		$target_obj = $conn->to();
		if($target_obj->class_id() == CL_PERSONNEL_MANAGEMENT_JOB_WANTED)
		{
			$target_obj->connect(array(
				'to' => $conn->prop('from'),
				'reltype' => "RELTYPE_PERSON",
			));
		}
	}

	function on_disconnect_person_from_job_wanted($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_PERSONNEL_MANAGEMENT_JOB_WANTED)
		{
			$target_obj->disconnect(array(
				"from" => $conn->prop("from"),
			));
		};
	}
}
?>
