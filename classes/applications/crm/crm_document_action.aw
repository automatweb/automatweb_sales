<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_document_action.aw,v 1.4 2007/12/06 14:33:17 kristo Exp $
// crm_document_action.aw - CRM Dokumendi tegevus 
/*

@classinfo syslog_type=ST_CRM_DOCUMENT_ACTION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_crm_doc_actions index=aw_oid master_table=objects master_index=brother_of
@default table=aw_crm_doc_actions

@default group=general

	@property date type=datetime_select field=aw_date
	@caption Kuup&auml;ev

	@property actor type=relpicker reltype=RELTYPE_ACTOR field=aw_actor
	@caption Tegija

	@property aw_action type=textbox field=aw_action
	@caption Tegevus

	@property ord type=textbox size=5 table=objects field=jrk
	@caption Jrk

	@property predicate type=relpicker reltype=RELTYPE_PRED field=aw_predicate multiple=1 store=connect
	@caption Eeldustegevus

	@property is_done type=checkbox ch_value=1 field=aw_is_done
	@caption Tehtud

	@property document type=relpicker reltype=RELTYPE_DOC field=aw_document
	@caption Dokument

@reltype ACTOR value=1 clid=CL_CRM_PERSON
@caption Tegija

@reltype PRED value=2 clid=CL_CRM_DOCUMENT_ACTION
@caption Eeldustegevus

@reltype DOC value=3 clid=CL_CRM_MEMO,CL_CRM_DEAL,CL_CRM_OFFER,CL_CRM_DOCUMENT
@caption Dokument
*/

class crm_document_action extends class_base
{
	function crm_document_action()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_document_action",
			"clid" => CL_CRM_DOCUMENT_ACTION
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
