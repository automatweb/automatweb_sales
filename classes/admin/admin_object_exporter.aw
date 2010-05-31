<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_ADMIN_OBJECT_EXPORTER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_admin_object_exporter master_index=brother_of master_table=objects index=aw_oid

@default table=aw_admin_object_exporter
@default group=general

@property folder type=relpicker reltype=RELTYPE_FOLDER multiple=1 store=connect
@caption Kaust

@property download type=text store=no 
@caption Laadi alla

@reltype FOLDER value=1 clid=CL_MENU
@caption Kaust

*/

class admin_object_exporter extends class_base
{
	const AW_CLID = 1554;

	function admin_object_exporter()
	{
		$this->init(array(
			"tpldir" => "admin/admin_object_exporter",
			"clid" => CL_ADMIN_OBJECT_EXPORTER
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

	function _get_download($arr)
	{
		$arr["prop"]["value"] = html::href(array(
			"caption" => t("Lae alla"),
			"url" => $this->mk_my_orb("mkdload", array("id" => $arr["obj_inst"]->id(), "ru" => get_ru()))
		));
	}

	/**
		@attrib name=mkdload
		@param id required
		@param ru required
	**/
	function mkdload($arr)
	{
		$o = obj($arr["id"]);
		$d = array();
		ini_set("memory_limit", "1000M");
		aw_set_exec_time(AW_LONG_PROCESS);
		foreach(safe_array($o->prop("folder")) as $fld_id)
		{
			$d[$fld_id] = obj($fld_id)->get_xml(array(
				"copy_subobjects" => 1,
				"copy_rels" => 1
			));
		}
		$s = serialize($d);
		header("Content-type: application/octet-stream");
		header("Content-length: ".strlen($s));
		die($s);
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
			$this->db_query("CREATE TABLE aw_admin_object_exporter(aw_oid int primary key)");
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
