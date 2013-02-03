<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_join_site_customer_relation master_index=brother_of master_table=objects index=aw_oid

@default table=aw_join_site_customer_relation
@default group=general

@property join_site type=hidden field=aw_join_site

@property organisation type=objpicker clid=CL_CRM_COMPANY field=aw_organisation
@caption Organisatsioon

@property customer_groups type=select table=objects field=meta method=serialize
@caption Kliendigrupid

@property type type=select field=aw_type
@caption Tüüp

*/

class join_site_customer_relation extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/join_site/join_site_customer_relation",
			"clid" => join_site_customer_relation_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_join_site_customer_relation" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_join_site_customer_relation` (
					`aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY	(`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "aw_organisation":
					case "aw_type":
					case "aw_join_site":
						$this->db_add_col($table, array(
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
