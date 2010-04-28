<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SITE_NOTIFICATION_SENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_site_notification_sent master_index=brother_of master_table=objects index=aw_oid

@default table=aw_site_notification_sent
@default group=general

@property site type=relpicker reltype=RELTYPE_SITE field=aw_site
@caption Sait mille kohta saadeti

@property rule type=relpicker reltype=RELTYPE_RULE field=aw_rule
@caption Teavituse reegel

@property when type=datetime_select field=aw_when
@caption Millal saadeti

@property who type=textbox field=aw_who
@caption Kellele saadeti

@property content type=text field=aw_content
@caption Sisu

@property error type=text field=aw_error
@caption Viga

@reltype SITE value=1 clid=CL_AW_SITE_ENTRY
@caption Sait

@reltype RULE value=2 clid=CL_SITE_NOTIFICATION_RULE
@caption Teavituse reegel
*/

class site_notification_sent extends class_base
{
	const AW_CLID = 1503;

	function site_notification_sent()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/site_notification_sent",
			"clid" => CL_SITE_NOTIFICATION_SENT
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
			$this->db_query("CREATE TABLE aw_site_notification_sent(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_site":
			case "aw_when":
			case "aw_rule":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_who":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;

			case "aw_content":
			case "aw_error":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
		}
	}
}

?>
