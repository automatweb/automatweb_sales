<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_person_language.aw,v 1.12 2009/08/24 08:54:35 instrumental Exp $
// crm_person_language.aw - Keeleoskus
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_CRM_PERSON_LANGUAGE, on_save)

@classinfo syslog_type=ST_CRM_PERSON_LANGUAGE no_name=1 no_comment=1 no_status=1 maintainer=markop
@tableinfo kliendibaas_keeleoskus index=oid master_table=objects master_index=oid

@default group=general
@default table=kliendibaas_keeleoskus

@property language type=relpicker reltype=RELTYPE_LANGUAGE field=language no_edit=1
@caption Keel

@property talk type=select field=sk_talk
@caption R&auml;&auml;gin

@property understand type=select field=sk_understand
@caption Saan aru

@property write type=select field=sk_write
@caption Kirjutan

@property other type=textbox field=other
@caption Muu keel

@property person type=hidden field=meta method=serialize table=objects

@reltype LANGUAGE value=1 clid=CL_CRM_LANGUAGE
@caption Keel

*/

class crm_person_language extends class_base
{
	function crm_person_language()
	{
		$this->init(array(
			"clid" => CL_CRM_PERSON_LANGUAGE
		));
		$this->lang_lvl_options = array(
			1 => t("ei oska"),
			2 => t("napp"),
			3 => t("keskmine"),
			4 => t("hea"),
			5 => t("v&auml;ga hea"),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "language":
				$prop["options"][0] = t("--vali--");
				foreach(get_instance(CL_PERSONNEL_MANAGEMENT)->get_languages() as $lkey => $lname)
				{
					$prop["options"][$lkey] = $lname;
				}
				$prop["options"]["other"] = t("muu keel");
				$prop["onchange"] = "var id = this.id.replace('_language_', '_other_'); alert(id); if(this.value == 'other') { $('#' + id).parent().parent().show(); } else { $('#' + id).parent().parent().hide(); }";
				break;

			case "talk":
			case "understand":
			case "write":
				//$prop["options"][0] = t("--vali--");
				$prop["options"] = $this->lang_lvl_options;
				break;

			case "person":
				$prop["value"] = $arr["request"]["person"];
				break;
		}
		return $retval;
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl == "kliendibaas_keeleoskus" && $field == "")
		{
			$this->db_query("create table kliendibaas_keeleoskus (oid bigint(20) unsigned primary key)");
			return true;
		}

		switch($field)
		{
			case "sk_talk":
			case "sk_understand":
			case "sk_write":
			case "language":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int",
				));
				return true;

			case "other":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "text",
				));
				return true;
		}

		return false;
	}

	function on_save($arr)
	{
		$o = obj($arr["oid"]);
		if(is_oid($o->prop("person")))
		{
			$person = obj($o->prop("person"));
			$person->connect(array(
				"to" => $o->id(),
				"reltype" => "RELTYPE_LANGUAGE_SKILL",
			));
			// Doin' this once is enough.
			$o->set_prop("person", "");
		}
	}
};
?>
