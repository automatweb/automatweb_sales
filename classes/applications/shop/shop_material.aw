<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_MATERIAL relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_shop_material master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_material
@default group=general

	@property code type=textbox field=aw_code
	@caption Kood

	@property material_type type=relpicker reltype=RELTYPE_MATERIAL_TYPE field=aw_material_type
	@caption Materjali t&uuml;&uuml;p

	@property desc type=textarea rows=10 cols=50 field=aw_desc
	@caption Kirjeldus

	@property images type=releditor mode=manager2 props=name,file,ord table_fields=name,file,ord  reltype=RELTYPE_IMAGE store=connect
	@caption Pildid


@reltype MATERIAL_TYPE value=1 clid=CL_SHOP_MATERIAL_TYPE
@caption Materjali t&uuml;&uuml;p

@reltype IMAGE value=2 clid=CL_IMAGE
@caption Pilt

*/

class shop_material extends class_base
{
	const AW_CLID = 1436;

	function shop_material()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_material",
			"clid" => CL_SHOP_MATERIAL
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
			$this->db_query("CREATE TABLE aw_shop_material(aw_oid int primary key, aw_code varchar(255), aw_material_type int, aw_desc text)");
			return true;
		}
	}
}

?>
