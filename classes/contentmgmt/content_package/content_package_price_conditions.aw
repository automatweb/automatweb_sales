<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_CONTENT_PACKAGE_PRICE_CONDITIONS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_content_package_price_conditions master_index=brother_of master_table=objects index=aw_oid

@default table=aw_content_package_price_conditions
@default group=general

	@property price field=aw_price type=textbox datatype=int size=4
	@caption Hind

	@property duration field=aw_duration type=textbox datatype=int size=4
	@caption Paketi kasutamise aeg p&auml;evades

	@property cp_spp type=hidden field=aw_cp_spp
	@caption Pakendi OID

*/

class content_package_price_conditions extends class_base
{
	const AW_CLID = 1543;

	function content_package_price_conditions()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/content_package/content_package_price_conditions",
			"clid" => CL_CONTENT_PACKAGE_PRICE_CONDITIONS
		));
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_content_package_price_conditions(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_price":
			case "aw_duration":
			case "aw_cp_spp":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
