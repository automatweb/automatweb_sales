<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_MATERIAL_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_shop_material_type master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_material_type
@default group=general

	@property code type=textbox field=aw_code
	@caption Kood
*/

class shop_material_type extends class_base
{
	const AW_CLID = 1437;

	function shop_material_type()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_material_type",
			"clid" => CL_SHOP_MATERIAL_TYPE
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_material_type(aw_oid int primary key, aw_code varchar(255))");
			return true;
		}
	}
}

?>
