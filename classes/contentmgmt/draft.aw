<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_DRAFT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_draft master_index=brother_of master_table=objects index=aw_oid

@default table=aw_draft
@default group=general

@property draft_property type=textbox field=aw_property
@caption Property

@property draft_user type=relpicker reltype=RELTYPE_USER store=connect
@caption Kasutaja

@property draft_object type=relpicker reltype=RELTYPE_OBJECT store=connect
@caption Objekt

@property draft_new type=select field=aw_new
@caption Uus

@property draft_content type=textarea field=aw_content
@caption Sisu

@reltype USER value=1 clid=CL_USER
@caption Kasutaja

@reltype OBJECT value=2
@caption Objekt

*/

class draft extends class_base
{
	const AW_CLID = 1497;

	function draft()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/draft",
			"clid" => CL_DRAFT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "draft_new":
				$prop["options"] = get_class_picker();
				break;
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
			$this->db_query("CREATE TABLE aw_draft(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_content":
			case "aw_property":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;

			case "aw_new":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				break;
		}
	}
}

?>
