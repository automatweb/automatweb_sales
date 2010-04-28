<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/crm/crm_expense.aw,v 1.10 2009/04/14 15:23:53 markop Exp $
// crm_expense.aw - Kulu 
/*

@classinfo syslog_type=ST_CRM_EXPENSE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta

	@property cost type=textbox
	@caption Maksumus

	@property date type=date_select
	@caption Kuup&auml;ev

	@property task type=relpicker store=connect reltype=RELTYPE_TASK
	@caption Toimetus

	@property on_bill type=checkbox ch_value=1
	@caption Arvele

	@property bill_id type=relpicker reltype=RELTYPE_BILL
	@caption Arve

	@property to_bill_date type=date_select
	@caption Arvele m&auml;&auml;ramise kuup&auml;ev

	@property who type=relpicker reltype=RELTYPE_PEOPLE
	@caption Kes tegi

	@property has_tax type=checkbox ch_value=1
	@caption Lisandub k&auml;ibemaks?

	@property currency type=select
	@caption Valuuta

	@reltype BILL value=1 clid=CL_CRM_BILL
	@caption Arve

	@reltype PEOPLE value=2 clid=CL_CRM_PERSON
	@caption Isik

	@reltype TASK value=3 clid=CL_TASK
	@caption Toimetus

*/

class crm_expense extends class_base
{
	const AW_CLID = 1144;

	function crm_expense()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_expense",
			"clid" => CL_CRM_EXPENSE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>
