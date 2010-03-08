<?php
/*
@classinfo syslog_type=ST_SITE_COPY_TODO relationmgr=yes no_name=1 no_comment=1 no_status=1 prop_cb=1 maintainer=instrumental
@tableinfo aw_site_copy_todo master_index=brother_of master_table=objects index=aw_oid

@default table=aw_site_copy_todo
@default group=general

@property url type=textbox table=objects field=name
@caption URL

@property sc_status type=select
@caption Staatus

@property local type=checkbox
@caption Lokaalne koopia

@property local_site type=checkbox
@caption Lokaalne koopia saidist

@property local_code type=checkbox
@caption Lokaalne koopia koodist

@property local_base type=checkbox
@caption Lokaalne koopia baasist

@property site_copy type=relpicker reltype=RELTYPE_SITE_COPY store=connect
@caption Saitide kopeerimise objekt, mille seadeid kasutada

@property packets_total type=textbox size=4
@caption Pakettide arv

@property packets type=textbox size=4
@caption T&otilde;mbamata pakettide arv

##

@reltype SITE_COPY value=1 clid=CL_SITE_COPY
@caption Saitide kopeerimise objekt, mille seadeid kasutada

@reltype MAIL value=1 clid=CL_ML_MEMBER
@caption E-postiaadress, kuhu saadetakse infot kopeerimise progressist

*/

class site_copy_todo extends class_base
{
	const STAT_COPY = 1;
	const STAT_TRANSFER = 2;
	const STAT_UNPACK = 3;
	const STAT_INSTALL = 4;
	const STAT_DELETE = 5;
	const STAT_CVS = 6;
	const STAT_DIFF = 7;

	function site_copy_todo()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_copy/site_copy_todo",
			"clid" => CL_SITE_COPY_TODO
		));
		$this->sc_status_options = array(
			self::STAT_COPY => t("Copy"),
			self::STAT_TRANSFER => t("Transfer"),
			self::STAT_UNPACK => t("Unpack"),
			self::STAT_INSTALL => t("Install"),
			self::STAT_DELETE => t("Delete"),
			self::STAT_CVS => t("Install on CVS code"),
			self::STAT_DIFF => t("Compare sites"),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "sc_status":
				$prop["options"] = $this->sc_status_options;
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
			$this->db_query("CREATE TABLE aw_site_copy_todo(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "sc_status":
			case "local":
			case "local_site":
			case "local_code":
			case "local_base":
			case "packets":
			case "packets_total":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}

?>
