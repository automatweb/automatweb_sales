<?php

namespace automatweb;
// crm_insurance.aw - Kindlustus
/*

@classinfo syslog_type=ST_CRM_INSURANCE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo crm_insurance index=oid master_table=objects master_index=oid

@default table=crm_insurance
@default group=general
	@property expires type=date_select
	@caption Insurance expires

	@property insurance_type type=relpicker reltype=RELTYPE_INSURANCE_TYPE automatic=1
	@caption Insurance type

@default table=objects
	@property certificate type=relpicker field=meta method=serialize reltype=RELTYPE_FILE
	@caption Upload insurance certificate

	@property company type=relpicker field=meta method=serialize reltype=RELTYPE_COMPANY
	@caption Company

	@property broker type=relpicker field=meta method=serialize reltype=RELTYPE_BROKER
	@caption Broker

	@property insurance_sum type=textbox field=meta method=serialize
	@caption Insurance sum


// -------------- RELTYPES -------------------
@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Company

@reltype BROKER value=2 clid=CL_CRM_PERSON,CL_CRM_COMPANY
@caption Broker

@reltype FILE value=3 clid=CL_FILE
@caption File

@reltype INSURANCE_TYPE value=4 clid=CL_CRM_INSURANCE_TYPE
@caption Type

*/

class crm_insurance extends class_base
{
	const AW_CLID = 1324;

	function crm_insurance()
	{
		$this->init(array(
			"tpldir" => "crm/crm_insurance",
			"clid" => CL_CRM_INSURANCE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "insurance_status":
				$prop["options"] = array("","");
				break;
			case "insurance_type":
				$ol = new object_list(array(
					"class_id" => CL_CRM_INSURANCE_TYPE,
					"site_id" => array(),
					"lang_id" => array()
				));

				if(!is_array($prop["options"])) $prop["options"] = array();
				$prop["options"] = $prop["options"] + $ol->names();
				break;
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ("crm_insurance" === $table)
		{
			switch($field)
			{
				case "insurance_type":
				case "expires":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "int",
					));
					return true;

				case "":
					$field_data = array(
						"oid" => array(
							"type" => "int",
							"length" => "11",
							"default" => "0",
							"null" => false
						),
						"insurance_type" => array(
							"type" => "int",
							"length" => "11"
						),
						"expires" => array(
							"type" => "int",
							"length" => "11"
						)
					);
					$ret = $this->db_create_table("crm_insurance", $field_data, "oid");

					if ($ret)
					{
						// move old data to new table
						$list = new object_list(array(
							"class_id" => CL_CRM_INSURANCE,
							"site_id" => array(),
							"lang_id" => array()
						));
						$list->begin();

						while ($o = $list->next())
						{
							$q = "INSERT INTO crm_insurance (oid, insurance_type, expires) VALUES (".$o->id().", " . ($o->meta("insurance_type") ? $o->meta("insurance_type") : "NULL") . ", " . ($o->meta("expires") ? $o->meta("expires") : "NULL") . ")";
							$ret = $this->db_query($q);
						}
					}

					return $ret;
			}
		}

		return false;
	}
}

?>
