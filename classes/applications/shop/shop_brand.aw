<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SHOP_BRAND relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_shop_brand master_index=brother_of master_table=objects index=aw_oid

@default table=aw_shop_brand
@default group=general

@property company type=relpicker reltype=RELTYPE_COMPANY field=aw_co
@caption Organisatsioon

@property art_groups type=relpicker reltype=RELTYPE_ARTICLE_GROUPS store=connect multiple=1
@caption Artikligrupid

@property logo type=relpicker reltype=RELTYPE_LOGO field=aw_logo
@caption Logo

@property desc type=textarea rows=10 cols=50 field=aw_desc
@caption Kirjeldus

@property code type=textbox field=aw_code
@caption Kood

@property desc_doc type=relpicker reltype=RELTYPE_DOC  field=aw_desc_doc
@caption Kirjeldav dokument

@property brand_series type=relpicker reltype=RELTYPE_BRAND_SERIES store=connect multiple=1
@caption Seeriad

@reltype COMPANY value=1 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype ARTICLE_GROUPS value=2 clid=CL_ARTICLE_GROUP
@caption Artikligrupp

@reltype LOGO value=3 clid=CL_IMAGE
@caption Logo

@reltype DOC value=4 clid=CL_DOCUMENT
@caption Dokument

@reltype BRAND_SERIES value=5 clid=CL_SHOP_BRAND_SERIES
@caption Br&auml;ndiseeria

*/

class shop_brand extends class_base
{
	const AW_CLID = 1443;

	function shop_brand()
	{
		$this->init(array(
			"tpldir" => "applications/shop/shop_brand",
			"clid" => CL_SHOP_BRAND
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_shop_brand(aw_oid int primary key, aw_co int, aw_logo int, aw_desc text, aw_desc_doc int)");
			return true;
		}
		else
		{
			switch($f)
			{
				case "aw_code":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "varchar(255)"
					));
					return true;
			}
		}
	}
}

?>
