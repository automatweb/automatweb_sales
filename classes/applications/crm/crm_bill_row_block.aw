<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default field=meta
@default method=serialize
@default group=general

@property parent type=hidden datatype=int default=0

@property heading type=textbox
@caption Pealkiri

@property description type=textarea
@caption Kirjeldus

// RELTYPES
@reltype CHILD value=2 clid=CL_CRM_BILL_ROW,CL_CRM_BILL_ROW_BLOCK
@caption Blokki kuuluv rida/alamblokk

*/

class crm_bill_row_block extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_bill_row_block",
			"clid" => CL_CRM_BILL_ROW_BLOCK
		));
	}
}
