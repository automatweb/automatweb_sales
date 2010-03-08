<?php

// phone.aw - Telefon
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_CRM_PERSON_WORK_RELATION, on_connect_work_relation_to_phone)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_CRM_PERSON_WORK_RELATION, on_disconnect_work_relation_from_phone)

@classinfo syslog_type=ST_CRM_PHONE relationmgr=yes maintainer=markop
@tableinfo kliendibaas_telefon master_table=objects master_index=oid index=oid

@default table=objects
@default group=general

@property name type=textbox
@caption Number

@property clean_number type=hidden field=number table=kliendibaas_telefon

@property conn_id type=hidden

@property comment type=textbox
@caption Kommentaar

@property type type=chooser table=kliendibaas_telefon field=aw_phone_type
@caption Numbri t&uuml;&uuml;p

@property country type=relpicker reltype=RELTYPE_COUNTRY store=connect automatic=1
@caption Riik

@property is_public type=hidden table=kliendibaas_telefon field=aw_is_public
@caption Avalik (endine checkbox)

@property is_public_conn type=checkbox ch_value=1 store=no
@caption Avalik

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


@classinfo no_status=1
*/

/*
@reltype BELONGTO value=1 clid=CL_CRM_ADDRESS,CL_CRM_COMPANY,CL_CRM_PERSON,CL_CRM_PERSON_WORK_RELATION
@caption Numbriga seotud objekt

@reltype COUNTRY value=2 clid=CL_CRM_COUNTRY
@caption Riik
*/

class crm_phone extends class_base
{
	function crm_phone()
	{
		$this->init(array(
			"clid" => CL_CRM_PHONE
		));
		$this->phone_types = array(
			"work" => t("t&ouml;&ouml;l"),
			"home" => t("kodus"),
			"short" => t("l&uuml;hinumber"),
			"mobile" => t("mobiil"),
			"fax" => t("faks"),
			"skype" => t("skype"),
			"extension" => t("sisetelefon"),
		);
		$this->trans_props = array(
			"comment"
		);
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "is_public_conn":
				if(isset($arr["request"]["conn_id"]) && is_numeric($arr["request"]["conn_id"]))
				{
					try
					{
						$c = new connection();
						$c->load($arr["request"]["conn_id"]);
						$c->change(array(
							"data" => $prop["value"],
						));
					}
					catch (Exception $e)
					{
					}
				}
				break;

			case "clean_number":
				$prop["value"] = str_replace(array(" ", "-", "(", ")") , "", $arr["request"]["name"]);
				break;

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		};
		return $retval;
	}

	function get_phone_types()
	{
		return $this->phone_types;
	}

	function get_property($arr)
	{
		$retval = PROP_OK;
		$prop = &$arr["prop"];
		switch($prop["name"])
		{
			case "is_public_conn":
				if(isset($arr["request"]["conn_id"]) && is_numeric($arr["request"]["conn_id"]))
				{
					try
					{
						$c = new connection();
						$c->load($arr["request"]["conn_id"]);
						$prop["value"] = $c->prop("data");
					}
					catch (Exception $e)
					{
					}
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "conn_id":
				if(isset($arr["request"]["conn_id"]))
				{
					$prop["value"] = $arr["request"]["conn_id"];
				}
				break;

			case "type":
				$prop["options"] = $this->phone_types;
				break;
		};
		return $retval;
	}

	function on_connect_work_relation_to_phone($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_PHONE)
		{
			$target_obj->connect(array(
				"to" => $conn->prop("from"),
				"reltype" => 1,		// RELTYPE_BELONGTO
			));
		}
	}

	function on_disconnect_work_relation_from_phone($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_PHONE)
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

	// Returns nicer view (formatted, with or without country code)
	//  oid - id of phone object
	//  show_area_code - boolean, default true

	function show($arr)
	{
		$return = "";
		$oid = $arr['oid'];
		if (!is_oid($oid) || !$this->can('view', $oid))
		{
			return;
		}
		$o = obj($oid);
		if ($o->class_id() != CL_CRM_PHONE)
		{
			return;
		}

		$ccode = true;
		if (!empty($arr['show_area_code']) && !$arr['show_area_code'])
		{
			$ccode = false;
		}
		if ($ccode)
		{
			$country = $o->get_first_obj_by_reltype(array(
				'reltype' => 'RELTYPE_COUNTRY',
			));
			if ($country)
			{
				$code = $country->prop('area_code');
				if (strlen($code))
				{
					$return = '+'.$code.' ';
				}
			}
		}
		$return .= $o->name();
		return $return;
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl === "kliendibaas_telefon" && $field == "")
		{
			$this->db_query("create table kliendibaas_telefon (oid int primary key)");
			return true;
		}

		switch($field)
		{
			case "aw_is_public":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int"
				));
				return true;
			case "aw_phone_type":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "char(25)"
				));

				aw_restore_acl();
				// Now let's fill this property for all existing phones.
				$ol = new object_list(array(
					"class_id" => CL_CRM_PHONE,
					"parent" => array(),
					"site_id" => array(),
					"lang_id" => array(),
					"status" => array(),
				));
				foreach($ol->arr() as $o)
				{
					$oid = $o->id();
					$type = $o->meta("type");

					$this->db_query("
						INSERT INTO
							kliendibaas_telefon (oid, aw_phone_type)
						VALUES
							('$oid', '$type')
						ON DUPLICATE KEY UPDATE
							aw_phone_type = '$type'
					");
				}
				return true;

			case "number":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "char(50)"
				));

				aw_restore_acl();
				// Now let's fill this property for all existing phones.
				$ol = new object_list(array(
					"class_id" => CL_CRM_PHONE,
					"parent" => array(),
					"site_id" => array(),
					"lang_id" => array(),
					"status" => array(),
				));
				foreach($ol->arr() as $o)
				{
					$oid = $o->id();
					$number = str_replace(array(" ", "-", "(", ")") , "", $o->name());

					$this->db_query("
						INSERT INTO
							kliendibaas_telefon (oid, number)
						VALUES
							('$oid', '$number')
					");
				}
				return true;
		}

		return false;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function callback_mod_retval($arr)
	{
		if(isset($arr["request"]["conn_id"]))
		{
			$arr["args"]["conn_id"] = $arr["request"]["conn_id"];
		}
	}
};
?>
