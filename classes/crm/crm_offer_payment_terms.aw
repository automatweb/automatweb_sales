<?php
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_offer_payment_terms.aw,v 1.2 2008/01/31 13:54:13 kristo Exp $
// crm_offer_payment_terms.aw - Pakkumise maksetingimused 
/*

@classinfo syslog_type=ST_CRM_OFFER_PAYMENT_TERMS relationmgr=yes no_comment=1 no_status=1 maintainer=markop

@default table=objects
@default group=general

@property content type=textarea rows=20 cols=80 
@caption Sisu

*/

class crm_offer_payment_terms extends class_base
{
	function crm_offer_payment_terms()
	{
		$this->init(array(
			"tpldir" => "crm/crm_offer_payment_terms",
			"clid" => CL_CRM_OFFER_PAYMENT_TERMS
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

	function generate_html($offer, $item)
	{
		return "<font size=2>".$item->name()."</font><br><br>".nl2br($item->prop("content"))." <br><br>";
	}
}
?>
