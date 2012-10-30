<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_management_base master_index=brother_of master_table=objects index=aw_oid

@default table=aw_management_base
@default group=general

  @property owner type=objpicker clid=CL_CRM_COMPANY
  @caption Omanikorganisatsioon

*/

class management_base extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/management_base/management_base",
			"clid" => management_base_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_management_base" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_management_base` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "":
						$this->db_add_col("", array(
							"name" => $field,
							"type" => "INT"
						));
						break;

				}
				$r = true;
			}
		}

		return $r;
	}
}
