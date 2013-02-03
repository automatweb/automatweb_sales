<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_order_webview master_index=brother_of master_table=objects index=aw_oid

@default table=aw_order_webview
@default group=general

*/

class order_webview extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/order_webview",
			"clid" => order_webview_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_order_webview" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_order_webview` (
					`aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
					PRIMARY KEY	(`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "":
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
