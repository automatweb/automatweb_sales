<?php
/*
@classinfo syslog_type=ST_SHOP_PRICE_LIST_CONDITION_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_shop_price_list_condition_type master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_price_list_condition_type
@default group=general

	@property source_code type=textarea field=aw_source_code
	@caption Kood

*/

class shop_price_list_condition_type extends class_base
{
	function shop_price_list_condition_type()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_price_list_condition_type",
			"clid" => CL_SHOP_PRICE_LIST_CONDITION_TYPE
		));
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_price_list_condition_type(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_source_code":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
		}
	}
}

?>
