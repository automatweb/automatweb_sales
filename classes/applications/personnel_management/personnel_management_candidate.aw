<?php
// personnel_management_candidate.aw - Kandidatuur
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_CRM_PERSON, on_connect_person_to_candidate)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_PERSONNEL_MANAGEMENT_JOB_OFFER, on_connect_job_offer_to_candidate)

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_PERSONNEL_MANAGEMENT_JOB_OFFER, on_disconnect_job_offer_from_candidate)

@classinfo syslog_type=ST_PERSONNEL_MANAGEMENT_CANDIDATE relationmgr=yes r2=yes no_comment=1 no_status=1 allow_rte=2 maintainer=instrumental

@default group=general
@default table=objects
@default field=meta

@property person type=relpicker reltype=RELTYPE_PERSON store=connect
@caption Isik

@property job_offer type=relpicker reltype=RELTYPE_JOB_OFFER store=connect
@caption T&ouml;&ouml;pakkumine

@property intro_file type=releditor reltype=RELTYPE_FILE rel_id=first props=file,filename method=serialize
@caption Kaaskiri failina

@property intro type=textarea field=comment cols=80 rows=40 richtext=1
@caption Kaaskiri tekstina

@property addinfo type=textarea field=meta method=serialize
@caption Lisainfo

@property recommendations type=relpicker reltype=RELTYPE_RECOMMENDATION multiple=1 store=connect no_edit=1
@caption Soovitajad

@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption Kandideerja

@reltype FILE value=2 clid=CL_FILE
@caption Kaaskiri failina

@reltype JOB_OFFER value=3 clid=CL_PERSONNEL_MANAGEMENT_JOB_OFFER
@caption T&ouml;&ouml;pakkumine

@reltype RECOMMENDATION value=4 clid=CL_CRM_RECOMMENDATION
@caption Soovitus

*/

class personnel_management_candidate extends class_base
{
	function personnel_management_candidate()
	{
		// change this to the folder under the templates folder, where this classes templates will be,
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"clid" => CL_PERSONNEL_MANAGEMENT_CANDIDATE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch ($prop["name"])
		{
			case "recommendations":
				if($this->can("view", $arr["obj_inst"]->prop("person")))
				{
					$p_obj = obj($arr["obj_inst"]->prop("person"));
					foreach($p_obj->connections_from(array("type" => "RELTYPE_RECOMMENDATION")) as $conn)
					{
						$to = $conn->to();
						$ops[$to->id()] = $to->prop("person.name");
					}
					$prop["options"] = $ops;
				}
				break;

			case "job_offer":
				if(is_oid($arr["request"]["alias_to"]) && $arr["request"]["reltype"] == 1)
				{
					$jo = obj($arr["request"]["alias_to"]);
					$prop["options"][$arr["request"]["alias_to"]] = $jo->name();
					$prop["value"] = $arr["request"]["alias_to"];
				}
				break;

			case "person":
				if ($arr["new"])
				{
					$p = get_current_person();
					$prop["options"] = array("" => t("--vali--"), $p->id() => parse_obj_name($p->name()));
					$prop["value"] = $p->id();
				}
				break;

			case "name":
				if ($arr["new"])
				{
					$p = get_current_person();
					$offer = obj($arr["request"]["parent"]);
					$prop["value"] = sprintf(t("%s kandidatuur kohale %s"), $p->name(), $offer->name());
				}
		}
		return $retval;
	}

	function callback_pre_save($arr)
	{
		if($this->can("edit", aw_global_get("candidate_obj_id_for_candidate")))
		{
			$arr["new"] = "";
			$arr["request"]["new"] = "";
			$o = obj(aw_global_get("candidate_obj_id_for_candidate"));
			$arr["id"] = $o->id();
			$props = array("intro_file", "intro", "addinfo", "recommendations");
			foreach($props as $prop)
			{
				$v = $arr["obj_inst"]->prop($prop);
				$o->set_prop($prop, $v);
			}
			$arr["obj_inst"] = $o;
			aw_session_set("candidate_obj_id_for_candidate", "");
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

	/**
		@attrib name=view_intro
		@param id required type=int
	**/
	function view_intro($arr)
	{
		$obj = obj($arr["id"]);
		$intro = $obj->prop("intro");
		if(!empty($intro))
		{
			die(nl2br($intro));
		}
		die("kaaskiri puudub");
	}

	/**
		@attrib name=new nologin=1 all_args=1
	**/
	function pnew($arr)
	{
		return parent::change($arr);
	}

	/**
		@attrib name=change nologin=1 all_args=1
	**/
	function pchange($arr)
	{
		return parent::change($arr);
	}

	/**
		@attrib name=submit all_args=1 nologin=1
	**/
	function psubmit($arr)
	{
		return parent::submit($arr);
	}

	function on_connect_person_to_candidate($arr)
	{
		$conn = $arr['connection'];
		$target_obj = $conn->to();
		$source_obj = $conn->from();
		$pm = get_instance(CL_PERSONNEL_MANAGEMENT);
		if($source_obj->class_id() == CL_PERSONNEL_MANAGEMENT_CANDIDATE && $this->can("view", $pm->get_sysdefault()))
		{
			$pmo = obj($pm->get_sysdefault());
			if($target_obj->parent != $pmo->prop("persons_fld"))
			{	// If the person's a candidate, one must be found from the personnel management system.
				$target_obj->connect(array(
					"to" => $pmo->id(),
					"reltype" => "RELTYPE_PERSONNEL_MANAGEMENT",
				));
			}
		}
	}

	function on_connect_job_offer_to_candidate($arr)
	{
		$conn = $arr['connection'];
		$target_obj = $conn->to();
		if($target_obj->class_id() == CL_PERSONNEL_MANAGEMENT_CANDIDATE)
		{
			$target_obj->connect(array(
				'to' => $conn->prop('from'),
				'reltype' => "RELTYPE_JOB_OFFER",
			));
		}
	}

	function on_disconnect_job_offer_from_candidate($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_PERSONNEL_MANAGEMENT_CANDIDATE)
		{
			$target_obj->disconnect(array(
				"from" => $conn->prop("from"),
			));
		};
	}
}
?>
