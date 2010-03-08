<?php
/*
@classinfo syslog_type=ST_AW_SPEC_VERSION relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_aw_spec_version master_index=brother_of master_table=objects index=aw_oid

@default table=aw_aw_spec_version
@default group=general

@property version_content field=aw_spec_content type=text
@caption Sisu
*/

class aw_spec_version extends class_base
{
	function aw_spec_version()
	{
		$this->init(array(
			"tpldir" => "applications/aw_spec/aw_spec_version",
			"clid" => CL_AW_SPEC_VERSION
		));
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
			$this->db_query("CREATE TABLE aw_aw_spec_version(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_spec_content":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "mediumtext"
				));
				return true;
		}
	}
}

?>
