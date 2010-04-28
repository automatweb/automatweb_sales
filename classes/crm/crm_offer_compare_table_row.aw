<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_offer_compare_table_row.aw,v 1.2 2008/01/31 13:54:13 kristo Exp $
// crm_offer_compare_table_row.aw - Pakkumise v&otilde;rdlustabeli rida 
/*

@classinfo syslog_type=ST_CRM_OFFER_COMPARE_TABLE_ROW relationmgr=yes no_comment=1 no_status=1 maintainer=markop

@default table=objects
@default group=general

@property ord type=textbox size=5 table=objects field=jrk 
@caption J&auml;rjekord

@default field=meta 
@default method=serialize

@property divs type=textbox size=5
@caption Jaotusi

@property div_headers type=textbox
@caption Jaotuste pealkirjad (komaga eraldatud)

*/

class crm_offer_compare_table_row extends class_base
{
	const AW_CLID = 926;

	function crm_offer_compare_table_row()
	{
		$this->init(array(
			"tpldir" => "crm/crm_offer_compare_table_row",
			"clid" => CL_CRM_OFFER_COMPARE_TABLE_ROW
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
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
}
?>
