<?php
/*
@classinfo syslog_type=ST_PRICE_COMPONENT_CATEGORY relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_price_component_category master_index=brother_of master_table=objects index=aw_oid

@default table=aw_price_component_category
@default group=general

*/

class price_component_category extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/price_component_category",
			"clid" => price_component_category_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_price_component_category" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_price_component_category` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_price_component_category", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}

?>
