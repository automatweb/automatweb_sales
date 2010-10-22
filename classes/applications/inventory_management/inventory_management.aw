<?php

/*
@classinfo syslog_type=ST_INVENTORY_MANAGEMENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1


@groupinfo settings parent=general caption="Seaded"


@default group=settings
	@property managed_warehouses type=relpicker multiple=1 size=5 reltype=RELTYPE_MANAGED_WAREHOUSE
	@caption Hallatavad laod


//////////////////////// RELTYPES /////////////////////////

@reltype MANAGED_WAREHOUSE value=1 clid=CL_WAREHOUSE
@caption Hallatav ladu


*/

class inventory_management extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/inventory_management/inventory_management",
			"clid" => CL_INVENTORY_MANAGEMENT
		));
	}
}

