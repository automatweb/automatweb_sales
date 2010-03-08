<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_person_add_education.aw,v 1.7 2008/11/25 12:50:59 instrumental Exp $
// crm_person_education.aw - Haridus 
/*

@classinfo syslog_type=ST_CRM_PERSON_ADD_EDUCATION no_name=1 no_status=1 maintainer=markop
@tableinfo aw_crm_person_add_education master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_person_add_education
@default group=general

@property org type=textbox
@caption Ettev&otilde;te

@property field type=textbox
@caption Teema

@property time type=date_select year_from=1980
@caption Algus

@property time_end type=date_select year_from=1980
@caption L&otilde;pp

@property time_text type=textbox
@caption Aeg

@property length_hrs type=textbox
@caption Kestvus tundides

@property length type=textbox
@caption Kestvus p&auml;evades
@comment &Uuml;le kuuajalise koolituse puhul kestvus kuudes

*/

class crm_person_add_education extends class_base
{
	function crm_person_add_education()
	{
		$this->init(array(
			"clid" => CL_CRM_PERSON_ADD_EDUCATION
		));
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_person_add_education(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "org":
			case "field":
			case "time_text":
			case "length_hrs":
			case "length":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				$this->do_db_upgrade_insert($f);
				return true;

			case "time":
			case "time_end":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				$this->do_db_upgrade_insert($f);
				return true;
		}
	}

	private function do_db_upgrade_insert($f)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON_ADD_EDUCATION,
			"lang_id" => array(),
			"site_id" => array(),
		));
		foreach($ol->arr() as $oid => $o)
		{
			switch($f)
			{
				case "org":
					$value = $o->name();
					break;

				case "field":
					$value = $o->comment();
					break;

				case "time_text":
				case "length_hrs":
				case "length":
				case "time":
				case "time_end":
					$value = $o->meta($f);
					break;
			}
			$this->db_query("INSERT INTO aw_crm_person_add_education (aw_oid, $f) VALUES ('$oid', '".$value."')");
		}
	}
};
?>
