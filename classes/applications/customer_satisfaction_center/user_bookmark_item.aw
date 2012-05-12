<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_user_bookmark_item master_index=brother_of master_table=objects index=aw_oid

@default table=aw_user_bookmark_item
@default group=general

@property obj type=objpicker
@caption Objekt

@property url type=textbox
@caption Lingi tekst

@property show_group type=textbox
@caption Default grupp

@property link_text_type type=select
@caption Lingi teksti valik

@property link_text type=textbox
@caption Lingi tekst

@property show_groups type=checkbox
@caption Kuva koos omaduste gruppidega

@property group type=checkbox
@caption Grupeeri

@property share type=checkbox
@caption Jaga

@property show_apps_menu type=checkbox
@caption N&auml;ita rakenduste men&uuml;&uuml;s

*/

class user_bookmark_item extends links
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/customer_satisfaction_center/user_bookmark_item",
			"clid" => user_bookmark_item_obj::CLID
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_user_bookmark_item" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_user_bookmark_item` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "url":
						$this->db_add_col("aw_user_bookmark_item", array(
							"name" => $field,
							"type" => "VARCHAR(2064)"
						));
						break;
					case "show_group":
						$this->db_add_col("aw_user_bookmark_item", array(
							"name" => $field,
							"type" => "VARCHAR(64)"
						));
						break;
					case "link_text":
						$this->db_add_col("aw_user_bookmark_item", array(
							"name" => $field,
							"type" => "VARCHAR(64)"
						));
						break;
					case "link_text_type":
					case "show_groups":
					case "group":
					case "share":
					case "show_apps_menu":
					case "obj":
						$this->db_add_col("aw_user_bookmark_item", array(
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

