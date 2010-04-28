<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_COLOUR relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_shop_colour master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_colour
@default group=general

	@property aw_code type=textbox field=aw_code
	@caption Kood

	@property col_rgb type=textbox field=aw_col_rgb
	@caption V&auml;rvikood RGB

	@property col_cmyk type=textbox field=aw_col_cmyk
	@caption V&auml;rvikood CMYK

	@property col_www type=textbox field=aw_col_www
	@caption V&auml;rvikood WWW

	@property col_prod type=textbox field=aw_col_prod
	@caption Tootja v&auml;rvikood

	@property image type=relpicker reltype=RELTYPE_IMAGE field=aw_image
	@caption Pilt

	@property file type=relpicker reltype=RELTYPE_FILE field=aw_file
	@caption Fail

@reltype RELTYPE_IMAGE value=1 clid=CL_IMAGE
@caption Pilt

@reltype RELTYPE_FILE value=2 clid=CL_FILE
@caption Fail
*/

class shop_colour extends class_base
{
	const AW_CLID = 1438;

	function shop_colour()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_colour",
			"clid" => CL_SHOP_COLOUR
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
			$this->db_query("CREATE TABLE aw_shop_colour(aw_oid int primary key, aw_code varchar(100), aw_col_rgb varchar(100), aw_col_cmyk varchar(100), aw_col_www varchar(100), aw_col_prod varchar(100), aw_image int, aw_file int)");
			return true;
		}
	}
}

?>
