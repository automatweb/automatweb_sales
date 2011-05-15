<?php
/*
@classinfo syslog_type=ST_CRM_OFFER_SENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_offer_sent master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_offer_sent
@default group=general

	@property offer type=hidden field=aw_offer
	@caption Pakkumus

*/

class crm_offer_sent extends mail_message
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_offer_sent",
			"clid" => CL_CRM_OFFER_SENT
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_offer_sent" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_offer_sent` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			else
			{
				switch($field)
				{
					case "aw_offer":
						$this->db_add_col($table, array(
							"name" => $field,
							"type" => "int"
						));
						$r = true;
						break;
				}
			}
		}

		return $r;
	}
}

?>
