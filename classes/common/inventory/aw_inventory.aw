<?php

/*

@classinfo relationmgr=yes prop_cb=1
@tableinfo aw_inventory_values index=aw_oid master_index=brother_of master_table=objects
@tableinfo aw_inventory_quantities index=aw_oid master_index=brother_of master_table=objects
@tableinfo aw_inventory_transactions index=aw_oid master_index=brother_of master_table=objects

@property record_transactions type=hidden default=1 field=meta method=serialize
@comment Transactions will be stored

@property record_values type=hidden default=1 field=meta method=serialize
@comment Items' values will be stored

@property record_quantities type=hidden default=1 field=meta method=serialize
@comment Item quantities will be stored

@reltype SUB_INVENTORY value=1 clid=CL_AW_INVENTORY
@caption Sub-inventory

*/

// andmevajadused:
// palju on x-i hetkel y (t2na, eile, ...) omaduse z j2rgi (raha, kogus (meetrid, tykki, ...))
// salvestada iga kirjega palju sel hetkel selle artikli kogus on
// transactions:
// item id
// date
// src inventory?
// dest inventory?
// requester
// qty
// value?
// status


class aw_inventory extends class_base
{
	public function __construct()
	{
		// ...
		$this->init(array(
			"tpldir" => "common/aw_inventory",
			"clid" => CL_AW_INVENTORY
		));
	}
}
