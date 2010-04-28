<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_ADMIN_OBJECT_IMPORTER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_admin_object_importer master_index=brother_of master_table=objects index=aw_oid

@default table=aw_admin_object_importer
@default group=general

@property folder type=relpicker reltype=RELTYPE_FOLDER field=aw_folder
@caption Kaust kuhu importida

@property fupload type=fileupload store=no
@caption Vali fail

@property log type=text store=no
@caption Logi

@reltype FOLDER value=1 clid=CL_MENU
@caption Kaust
*/

class admin_object_importer extends class_base
{
	const AW_CLID = 1555;

	function admin_object_importer()
	{
		$this->init(array(
			"tpldir" => "admin/admin_object_importer",
			"clid" => CL_ADMIN_OBJECT_IMPORTER
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

	function _set_fupload($arr)
	{
		if (is_uploaded_file($_FILES["fupload"]["tmp_name"]))
		{
			$d = unserialize(file_get_contents($_FILES["fupload"]["tmp_name"]));
			if (is_array($d))
			{
				foreach($d as $xml)
				{
					$o = obj();
					$o->from_xml($xml, $arr["obj_inst"]->prop("folder"));
				}
			}
		}
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
			$this->db_query("CREATE TABLE aw_admin_object_importer(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_folder":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
