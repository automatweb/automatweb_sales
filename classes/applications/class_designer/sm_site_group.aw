<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SM_SITE_GROUP relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=root
@tableinfo aw_sm_site_group master_index=brother_of master_table=objects index=aw_oid

@default table=aw_sm_site_group
@default group=general


@default group=sites

	@property sites_tb type=toolbar no_caption=1 store=no
	@property sites type=table no_caption=1 store=no


@groupinfo sites caption="Saidid"

@reltype SITE value=1 clid=CL_AW_SITE_ENTRY
@caption Sait
*/

class sm_site_group extends class_base
{
	const AW_CLID = 1508;

	function sm_site_group()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/sm_site_group",
			"clid" => CL_SM_SITE_GROUP
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

	function callback_mod_reforb(&$arr)
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
			$this->db_query("CREATE TABLE aw_sm_site_group(aw_oid int primary key)");
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

	function _get_sites_tb($arr)
	{	
		$arr["prop"]["vcl_inst"]->add_delete_rels_button();
	}

	function _get_sites($arr)
	{
		$arr["prop"]["vcl_inst"]->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SITE"))),
			array("id", "name", "url", "site_used", "code_branch", "server_oid"),
			CL_AW_SITE_ENTRY
		);	
	}
}

?>
