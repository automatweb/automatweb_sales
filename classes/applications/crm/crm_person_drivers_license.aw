<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_person_drivers_license master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_person_drivers_license
@default group=general

@property person type=objpicker clid=CL_CRM_PERSON
@caption Isik

@property category type=textbox
@caption Kategooria

@property year type=textbox
@caption Aasta

@property experience type=textbox
@caption Praktiseeritud kogemust

*/

class crm_person_drivers_license extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_person_drivers_license",
			"clid" => crm_person_drivers_license_obj::CLID
		));
	}

	public static function categories()
	{
		$arr = array("A" => "A", "B" => "B", "C" => "C", "D" => "D" ,"BE" => "BE", "CE" => "CE", "DE" => "DE", "A1" => "A1", "B1" => "B1", "C1" => "C1", "D1" => "D1", "C1E" => "C1E", "D1E" => "D1E", "T" => "T" , "R" => "R");
asort($arr);
		return $arr;
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_person_drivers_license" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_person_drivers_license` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "year":
					case "person":
						$this->db_add_col("aw_crm_person_drivers_license", array(
							"name" => $field,
							"type" => "INT"
						));
						$r = true;
						break;
					case "category":
						$this->db_add_col("aw_crm_person_drivers_license", array(
							"name" => $field,
							"type" => "VARCHAR(4)"
						));
						$r = true;
						break;
					case "experience":
						$this->db_add_col("aw_crm_person_drivers_license", array(
							"name" => $field,
							"type" => "varchar(20)"
						));
						$r = true;
						break;
				}
			}
		}

		return $r;
	}

	function javascript_add_new($arr)
	{
		$o = obj();
		$o->set_class_id(crm_person_drivers_license_obj::CLID);
		$o->set_parent($_POST["person"]);
		$o->set_name("Kategooria " . $_POST["category"] . " juhiluba");
		$o->set_prop("year" , $_POST["year"]);
		$o->set_prop("experience" , $_POST["experience"]);
		$o->set_prop("category" , $_POST["category"]);
		$o->set_prop("person" , $_POST["person"]);
		$o->save();
		print $o->id();
		exit();
	}

}
