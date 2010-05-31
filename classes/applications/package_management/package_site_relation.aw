<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_PACKAGE_SITE_RELATION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_package_site_relation master_index=brother_of master_table=objects index=aw_oid

@default table=aw_package_site_relation
@default group=general

@property site type=textbox size=5 table=aw_package_site_relation field=aw_site
@caption Saidi id

*/

class package_site_relation extends class_base
{
	const AW_CLID = 1467;

	function package_site_relation()
	{
		$this->init(array(
			"tpldir" => "applications/package_management/package_site_relation",
			"clid" => CL_PACKAGE_SITE_RELATION
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
			$this->db_query("CREATE TABLE aw_package_site_relation(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_site":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
