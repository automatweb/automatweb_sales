<?php

// crm_person_work_relation.aw - T&ouml;&ouml;suhe
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_CRM_PHONE, on_connect_phone_to_work_relation)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_ML_MEMBER, on_connect_email_to_work_relation)

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_CRM_PHONE, on_disconnect_phone_from_work_relation)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_ML_MEMBER, on_disconnect_email_from_work_relation)

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1 no_name=1
@tableinfo aw_crm_person_work_relation index=aw_oid master_index=brother_of master_table=objects

@default group=general
@default table=aw_crm_person_work_relation
	@property employer type=objpicker clid=CL_CRM_COMPANY
	@caption T&ouml;&ouml;andja

	@property company_section type=objpicker clid=CL_CRM_SECTION field=section
	@caption &Uuml;ksus

	@property profession type=objpicker clid=CL_CRM_PROFESSION
	@caption Ametikoht

	@property employee type=objpicker clid=CL_CRM_PERSON
	@caption T&ouml;&ouml;v&otilde;tja

	@property state type=text
	@caption T&ouml;&ouml;suhe

	@property start type=datepicker
	@caption Suhte algus

	@property end type=datepicker
	@caption Suhte l&otilde;pp


@default table=objects
@default field=meta
@default method=serialize
	@property room type=textbox
	@caption T&ouml;&ouml;ruum

	@property tasks type=textarea
	@caption &Uuml;lesanded

	@property load type=select
	@caption Koormus

	@property salary type=textbox
	@caption Kuutasu
	@comment Bruto

	@property salary_currency type=relpicker reltype=RELTYPE_CURRENCY store=connect
	@caption Valuuta

	@property benefits type=textarea
	@caption Soodustused ja eritingimused

	@property directive_link type=textbox field=meta method=serialize
	@caption Viit ametijuhendile

	@property directive type=relpicker reltype=RELTYPE_DESC_FILE field=meta method=serialize
	@caption Ametijuhend

	@property contract_stop type=relpicker reltype=RELTYPE_CONTRACT_STOP field=meta method=serialize
	@caption T&ouml;&ouml;lepingu peatumine


// RELTYPES
@reltype SUBSITUTE value=4 clid=CL_CRM_PROFESSION
@caption Asendaja

@reltype DESC_FILE value=5 clid=CL_FILE
@caption Ametijuhend

@reltype CONTRACT_STOP value=6 clid=CL_CRM_CONTRACT_STOP
@caption T&ouml;&ouml;lepingu peatamine

@reltype PHONE value=8 clid=CL_CRM_PHONE
@caption Telefon

@reltype EMAIL value=9 clid=CL_ML_MEMBER
@caption E-post

@reltype FAX value=10 clid=CL_CRM_PHONE
@caption Faks

@reltype CURRENCY value=11 clid=CL_CURRENCY
@caption Valuuta

*/

class crm_person_work_relation extends class_base
{
	function crm_person_work_relation()
	{
		$this->init(array(
			"clid" => CL_CRM_PERSON_WORK_RELATION
		));
	}

	function _get_state($arr)
	{
		$retval = PROP_OK;
		$value = isset($arr["prop"]["value"]) ? crm_person_work_relation_obj::state_names($arr["prop"]["value"]) : crm_person_work_relation_obj::state_names(crm_person_work_relation_obj::STATE_UNDEFINED);
		$arr["prop"]["value"] = array_pop($value);
		return $retval;
	}

	function _get_load(&$arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		$cl = new classificator();
		$r = $cl->get_choices(array(
			"clid" => CL_PERSONNEL_MANAGEMENT,
			"name" => "cv_load",
			"sort_callback" => "CL_PERSONNEL_MANAGEMENT::cmp_function",
		));
		$data["options"] = $r[4]["list_names"];
		return $retval;
	}

	function _set_end($arr)
	{
		$start = isset($arr["request"]["start"]) ? datepicker::get_timestamp($arr["request"]["start"]) : 0;
		$end = datepicker::get_timestamp($arr["prop"]["value"]);
		if ($end and $start >= $end)
		{
			$arr["prop"]["error"] = t("Algus peab olema enne l&otilde;ppu");
			return PROP_FATAL_ERROR;
		}
		return PROP_OK;
	}

	function on_connect_phone_to_work_relation($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_PERSON_WORK_RELATION && $target_obj->prop("type") == "fax")
		{
			$target_obj->connect(array(
				"to" => $conn->prop("from"),
				"reltype" => 10,		// RELTYPE_FAX
			));
		}
		else
		{
			$target_obj->connect(array(
				"to" => $conn->prop("from"),
				"reltype" => 8,		// RELTYPE_PHONE
			));
		}
	}

	function on_connect_email_to_work_relation($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_PERSON_WORK_RELATION)
		{
			$target_obj->connect(array(
				"to" => $conn->prop("from"),
				"reltype" => 9,		// RELTYPE_EMAIL
			));
		}
	}

	function on_disconnect_phone_from_work_relation($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_PERSON_WORK_RELATION)
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

	function on_disconnect_email_from_work_relation($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_PERSON_WORK_RELATION)
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

	function cmp_function($a, $b)
	{
		if($a->ord() == $b->ord())
		{
			return strcmp($a->trans_get_val("name"), $b->trans_get_val("name"));
		}
		else
		{
			return (int)$a->ord() > (int)$b->ord() ? 1 : -1;
		}
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		$ret_val = false;

		if ("aw_crm_person_work_relation" === $table)
		{
			if (empty($field))
			{
				$this->db_query("
					CREATE TABLE `aw_crm_person_work_relation` (
						`aw_oid` int(11) UNSIGNED NOT NULL default '0',
						`employer` int(11) UNSIGNED NOT NULL default '0',
						`section` int(11) UNSIGNED NOT NULL default '0',
						`profession` int(11) UNSIGNED NOT NULL default '0',
						`employee` int(11) UNSIGNED NOT NULL default '0',
						`start` int(11) UNSIGNED NOT NULL default '0',
						`end` int(11) UNSIGNED NOT NULL default '0',
						`state` tinyint UNSIGNED NOT NULL default '".crm_person_work_relation_obj::STATE_UNDEFINED."',
						PRIMARY KEY  (`aw_oid`));
				");
				$this->db_query("CREATE INDEX `state` ON `aw_crm_person_work_relation` (`state`);");
				$ret_val = true;
			}
			elseif ("state" === $field)
			{
				$this->db_add_col("aw_crm_person_work_relation", array(
					"name" => "state",
					"type" => "tinyint UNSIGNED NOT NULL default '0'"
				));

				// populate new column
				$time = time();
				$this->db_query("UPDATE `aw_crm_person_work_relation` SET `state`= (if((`start` > 1 and `start` < {$time} and (`end` > {$time} or `end` = 0 or `end` is null)), ".crm_person_work_relation_obj::STATE_ACTIVE.", ".crm_person_work_relation_obj::STATE_UNDEFINED."))"); // active relations
				$this->db_query("UPDATE `aw_crm_person_work_relation` SET `state`= (if((`end` > 1 and `end` < {$time}), ".crm_person_work_relation_obj::STATE_ENDED.", `state`))"); // ended relations
				$this->db_query("UPDATE `aw_crm_person_work_relation` SET `state`= (if((`start` > {$time}), ".crm_person_work_relation_obj::STATE_NEW.", `state`))"); // starting relations

				// add index
				$this->db_query("CREATE INDEX `state` ON `aw_crm_person_work_relation` (`state`);");

				$ret_val = true;
			}
		}

		return $ret_val;
	}
}
