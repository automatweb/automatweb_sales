<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_UNIT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property unit_code type=textbox
@caption &Uuml;hiku kood

@property unit_sort type=select
@caption &Uuml;hiku liik

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi
*/

class unit extends class_base
{
	const AW_CLID = 1126;

	function unit()
	{
		$this->init(array(
			"tpldir" => "common//unit",
			"clid" => CL_UNIT
		));
		$this->trans_props = array(
			"name", "unit_code"
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "unit_sort":
				$prop["options"] = array(0 => "" , 1 => t("pikkus&uuml;hik"), 2 => t("massi&uuml;hik"), 3 => t("koguse&uuml;hik"), 4 => t("mahu&uuml;hik"), 5 => t("aja&uuml;hik"));
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}

	function callback_mod_tab($arr)
	{
		$trc = aw_ini_get("user_interface.trans_classes");

		if ($arr["id"] == "transl" && (aw_ini_get("user_interface.content_trans") != 1 && !$trc[$this->clid]))
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function get_unit_list($choose = null)
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"lang_id" => array(),
			"site_id" => array(),
		));
		if($choose)
		{
			return array(0=>t("--vali--")) + $ol->names();
		}
		return $ol->names();
	}
}
?>
