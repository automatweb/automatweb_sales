<?php

/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_customers_websearch master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_customers_websearch
@default group=general

*/

class crm_customers_websearch extends class_base
{
	function __construct()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_customers_websearch",
			"clid" => crm_customers_websearch_obj::CLID
		));
	}

	/**
		@attrib name=show params=name
		@param id required type=int
		@param charset optional type=string
	**/
	public function show($arr)
	{
		$websearch = obj($arr["id"], array(), crm_customers_websearch_obj::CLID);

		$this->read_template("show.tpl");

		return $this->parse();
	}

	function do_db_upgrade($table, $field, $query, $error)
	{
		$r = false;

		if ("aw_crm_customers_websearch" === $table)
		{
			if (empty($field))
			{
				$this->db_query("CREATE TABLE `aw_crm_customers_websearch` (
				  `aw_oid` int(11) UNSIGNED NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`aw_oid`)
				)");
				$r = true;
			}
			elseif ("" === $field)
			{
				$this->db_add_col("aw_crm_customers_websearch", array(
					"name" => $field,
					"type" => ""
				));
				$r = true;
			}
		}

		return $r;
	}
}
