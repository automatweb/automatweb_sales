<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/groupware/project_risk.aw,v 1.4 2007/12/06 14:33:32 kristo Exp $
// project_risk.aw - Projekti risk 
/*

@classinfo syslog_type=ST_PROJECT_RISK relationmgr=yes no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_project_risks index=aw_oid master_index=brother_of master_table=objects
@default group=general

	@property owner type=relpicker reltype=RELTYPE_OWNER table=aw_project_risks field=aw_owner
	@caption Omanik

	@property comment type=textarea rows=5 cols=30 field=comment table=objects
	@caption Kirjeldus

	@property type type=select table=aw_project_risks field=aw_type
	@caption T&uuml;&uuml;p

	@property identification_date type=date_select table=aw_project_risks field=aw_identification_date default=-1
	@caption Tuvastamise kuup&auml;ev

	@property last_eval_date type=date_select table=aw_project_risks field=aw_last_eval_date default=-1
	@caption Viimase hindamise kuup&auml;ev

	@property countermeasure type=textarea rows=5 cols=30 table=aw_project_risks field=aw_countermeasure
	@caption Vastumeede


@reltype OWNER value=1 clid=CL_CRM_PERSON
@caption Omanik
*/

class project_risk extends class_base
{
	const AW_CLID = 1079;

	function project_risk()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/project_risk",
			"clid" => CL_PROJECT_RISK
		));

		$this->types = array(
			"1" => t("&Auml;ririsk"),
			"2" => t("Projekti risk"),
			"3" => t("Etapi risk")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "owner":
				if (!$this->can("view", $prop["value"]))
				{
					$cp = get_current_person();
					$prop["value"] = $cp->id();
					$prop["options"][$cp->id()] = $cp->name();
				}
				break;

			case "type":
				$prop["options"] = array("" => t("--vali--")) + $this->types;
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
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_project_risks(aw_oid int primary key, aw_owner int, aw_countermeasure text)");
			return true;
		}

		switch($f)
		{
			case "aw_type":
			case "aw_identification_date":
			case "aw_last_eval_date":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
				break;
		}
	}
}
?>
