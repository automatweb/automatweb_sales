<?php
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_product master_index=brother_of master_table=objects index=aw_oid

@default table=aw_product
@default group=general

@reltype DESCRIPTION_DOCUMENT value=32 clid=CL_DOCUMENT
@caption Tootekirjeldus

@message MSG_STORAGE_ALIAS_ADD_TO handler=setup_description_document param=CL_DOCUMENT

*/

class aw_product extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "common/product/aw_product",
			"clid" => CL_AW_PRODUCT
		));
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		if ("aw_product" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE aw_product(aw_oid int primary key)");
				return true;
			}
		}
	}

	function setup_description_document($arr)
	{
		if (32 == $arr["connection"]->prop("type")) // RELTYPE_DESCRIPTION_DOCUMENT
		{
			$doc = $arr["connection"]->to();
			$doc->connect(array(
				"to" => $arr["connection"]->prop("from")
			));
		}
	}
}
