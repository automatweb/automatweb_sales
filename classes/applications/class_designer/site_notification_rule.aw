<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_SITE_NOTIFICATION_RULE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_site_notification_rule master_index=brother_of master_table=objects index=aw_oid

@default table=aw_site_notification_rule
@default group=general

@property err_content type=textbox field=aw_err_content
@caption Veeateate sisu

@property mail_to type=textbox field=aw_mail_to
@caption Meil kellele

@property mail_subj type=textbox field=aw_mail_subj
@caption Meili teema

@property mail_content_legend type=text store=no
@caption Legend

@property mail_content type=textarea rows=30 cols=80 field=aw_mail_content
@caption Meili sisu


*/

class site_notification_rule extends class_base
{
	const AW_CLID = 1502;

	function site_notification_rule()
	{
		$this->init(array(
			"tpldir" => "applications/class_designer/site_notification_rule",
			"clid" => CL_SITE_NOTIFICATION_RULE
		));
	}

	function _get_mail_content_legend($arr)
	{	
		$arr["prop"]["value"] = t("Teksti sees v&otilde;ib kasutada: %site% - saidi url, kus viga esines. %error% - veateate sisu");
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
			$this->db_query("CREATE TABLE aw_site_notification_rule(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_err_content":
			case "aw_mail_to":
			case "aw_mail_subj":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;

			case "aw_mail_content":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "text"
				));
				return true;
		}
	}
}

?>
