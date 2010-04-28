<?php

namespace automatweb\cb;

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=voldemar
@tableinfo aw_product master_index=brother_of master_table=objects index=aw_oid

@default table=aw_product
@default group=general

*/

class awproduct extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "common/product/product",
			"clid" => CL_PRODUCT
		));
	}

	function do_db_upgrade($table, $field)
	{
		if ("aw_product" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE aw_product(aw_oid int primary key)");
				return true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col($table, array(
					"name" => "",
					"type" => ""
				));
				return true;
			}
		}
	}
}

?>
