<?php
/*
@classinfo syslog_type=ST_CRM_OFFER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=SYSTEM
@tableinfo aw_crm_offer master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_offer
@default group=general

*/

class crm_offer extends class_base
{
	function crm_offer()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_offer",
			"clid" => CL_CRM_OFFER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
		}

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

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_offer(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => ""
				));
				return true;
		}
	}
}

?>
