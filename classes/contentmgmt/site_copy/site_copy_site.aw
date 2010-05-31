<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SITE_COPY_SITE relationmgr=yes no_name=1 no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_site_copy_site master_index=brother_of master_table=objects index=aw_oid

@default table=aw_site_copy_site
@default group=general

@property url type=textbox field=name table=objects
@caption URL

@property copy_url type=textbox
@caption Saidi koopia URL

@property copy_url_cvs type=textbox
@caption Saidi koopia URL (CVS koodil)

@property site_dir type=textbox
@caption Saidi kaust kettal

@property site_dir_cvs type=textbox
@caption Saidi kaust kettal (CVS koodil)

@property site_diff type=relpicker reltype=RELTYPE_SITE_DIFF store=connect
@caption Saitide v&otilde;rdlus

@reltype SITE_DIFF value=1 clid=CL_SITE_DIFF
@caption Saitide v&otilde;rdlus

*/

class site_copy_site extends class_base
{
	const AW_CLID = 1489;

	function site_copy_site()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_copy/site_copy_site",
			"clid" => CL_SITE_COPY_SITE
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
			$this->db_query("CREATE TABLE aw_site_copy_site(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "copy_url":
			case "copy_url_cvs":
			case "site_dir":
			case "site_dir_cvs":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;
		}
	}
}

?>
