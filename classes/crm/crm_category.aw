<?php

/* Organization's partner/customer category

@classinfo relationmgr=no prop_cb=1 no_status=1
@tableinfo aw_crm_category index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general
	@property jrk type=textbox size=5 table=objects field=jrk
	@caption J&auml;rjekord

	@property parent_category type=hidden table=aw_crm_category field=aw_parent_category
	@property organization type=hidden table=aw_crm_category field=aw_organization

*/

class crm_category extends class_base
{
	function crm_category()
	{
		$this->init(array(
			"tpldir" => "crm/crm_category",
			"clid" => CL_CRM_CATEGORY
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ("aw_crm_category" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_category` (
				  `aw_oid` int(11) UNSIGNED NOT NULL default '0',
				  `aw_organization` int(11) UNSIGNED default NULL,
				  `aw_parent_category` int(11) UNSIGNED default NULL,
				  PRIMARY KEY  (`aw_oid`)
				)");
			}
			elseif ("aw_organization" === $field)
			{
				$this->db_add_col($table, array(
					"name" => "aw_organization",
					"type" => "int(11) UNSIGNED",
					"default" => "NULL"
				));
			}
			elseif ("aw_parent_category" === $field)
			{
				$this->db_add_col($table, array(
					"name" => "aw_parent_category",
					"type" => "int(11) UNSIGNED default NULL"
				));
			}
		}
	}
}

