<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_shop_purveyors_webview master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_purveyors_webview
@default group=general

*/

class shop_purveyors_webview extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_purveyors_webview",
			"clid" => shop_purveyors_webview_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_shop_purveyors_webview" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_shop_purveyors_webview` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_shop_purveyors_webview", array(
					"name" => "",
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
