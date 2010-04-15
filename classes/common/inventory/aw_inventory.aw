<?php

/*
@classinfo relationmgr=yes no_status=1 prop_cb=1 maintainer=voldemar confirm_save_data=1

*/

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

?>
