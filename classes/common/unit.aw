<?php
/*
@classinfo syslog_type=ST_UNIT relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property name_for_1 type=textbox
@comment &Uuml;hiku nimi k&auml;&auml;ndes kui v&auml;&auml;rtus on 1
@caption V&auml;&auml;rtuse j&auml;rel (1 ...)

@property name_for_2 type=textbox
@comment &Uuml;hiku nimi k&auml;&auml;ndes kui v&auml;&auml;rtus on 2
@caption V&auml;&auml;rtuse j&auml;rel (2 ...)

@property name_for_n type=textbox
@comment &Uuml;hiku nimi k&auml;&auml;ndes kui v&auml;&auml;rtus on muu
@caption V&auml;&auml;rtuse j&auml;rel (n ...)

@property unit_code type=textbox
@caption &Uuml;hiku t&auml;his

@property unit_sort type=select
@comment Suurus, mida &uuml;hik m&otilde;&otilde;dab
@caption Suurus


@groupinfo transl caption=T&otilde;lgi
@default group=transl
	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

*/

class unit extends class_base
{
	function unit()
	{
		$this->init(array(
			"tpldir" => "common/unit",
			"clid" => CL_UNIT
		));
		$this->trans_props = array(
			"name", "unit_code"
		);
	}

	function _get_unit_sort(&$arr)
	{
		$retval = PROP_OK;
		$arr["prop"]["options"] = array(0 => "") + unit_obj::quantity_names();
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

		if ($arr["id"] === "transl" && (aw_ini_get("user_interface.content_trans") != 1 && empty($trc[$this->clid])))
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function get_unit_list($choose = null)
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT
		));
		if($choose)
		{
			return array(0=>t("--vali--")) + $ol->names();
		}
		return $ol->names();
	}
}
