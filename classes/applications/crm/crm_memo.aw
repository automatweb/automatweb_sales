<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_memo.aw,v 1.13 2007/12/06 14:33:17 kristo Exp $
// crm_memo.aw - Memo 
/*

@classinfo syslog_type=ST_CRM_MEMO relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general

@tableinfo aw_crm_memo index=aw_oid master_index=brother_of master_table=objects


@default group=general

	@property project type=popup_search clid=CL_PROJECT table=aw_crm_memo field=aw_project
	@caption Projekt

	@property task type=popup_search clid=CL_TASK table=aw_crm_memo field=aw_task
	@caption &Uuml;lesanne

	@property customer type=popup_search clid=CL_CRM_COMPANY table=aw_crm_memo field=aw_customer
	@caption Klient

	@property creator type=popup_search style=relpicker reltype=RELTYPE_CREATOR table=aw_crm_memo field=aw_creator
	@caption Koostaja

	@property reader type=popup_search style=relpicker reltype=RELTYPE_READER table=aw_crm_memo field=aw_reader
	@caption Lugeja

	@property reg_date type=date_select table=aw_crm_memo field=aw_reg_date
	@caption Reg kuup&auml;ev

	@property comment type=textarea rows=5 cols=50 table=objects field=comment
	@caption Kirjeldus

@default group=files

	@property files type=releditor reltype=RELTYPE_FILE field=meta method=serialize mode=manager props=name,file,type,comment,file_url,newwindow table_fields=name 
	@caption Failid

@default group=parts

	@property parts_tb type=toolbar no_caption=1

	@property acts type=table store=no no_caption=1
	@caption Tegevused

	property srch_txt type=text subtitle=1 
	caption Otsing

	property part_s_person type=textbox store=no
	caption Isiku nimi

	property part_s_co type=textbox store=no
	caption T&ouml;&ouml;koht

	property parts_s_sbt type=submit 
	caption Otsi

@groupinfo files caption="Failid"
@groupinfo parts caption="Osalejad" 
@groupinfo acl caption=&Otilde;igused
@default group=acl
	
	@property acl type=acl_manager store=no
	@caption &Otilde;igused

@reltype FILE value=1 clid=CL_FILE
@caption fail

@reltype CREATOR value=2 clid=CL_CRM_PERSON
@caption looja

@reltype READER value=3 clid=CL_CRM_PERSON
@caption lugeja

@reltype ACTION value=8 clid=CL_CRM_DOCUMENT_ACTION
@caption Tegevus
*/

class crm_memo extends class_base
{
	const AW_CLID = 1008;

	function crm_memo()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_memo",
			"clid" => CL_CRM_MEMO
		));
	}

	function get_property($arr)
	{
		$b = get_instance("applications/crm/crm_document_base");
		$retval = $b->get_property($arr);

		$prop = &$arr["prop"];
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$b = get_instance("applications/crm/crm_document_base");
		$retval = $b->set_property($arr);

		$prop = &$arr["prop"];
		switch($prop["name"])
		{
			case "files":
				$prop["obj_parent"] = $arr["obj_inst"]->id();
				break;
		}
		return $retval;
	}	

	function callback_post_save($arr)
	{
		if($arr["new"]==1 && is_oid($arr["request"]["project"]) && $this->can("view" , $arr["request"]["project"]))
		{
			$arr["obj_inst"]->set_prop("project" , $arr["request"]["project"]);
		}
	}
	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
		if(!$arr["id"])
		{
			$arr["project"] = $_GET["project"];
		}
	}
}
?>
