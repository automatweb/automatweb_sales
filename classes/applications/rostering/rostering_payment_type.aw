<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/rostering/rostering_payment_type.aw,v 1.3 2007/12/06 14:34:03 kristo Exp $
// rostering_payment_type.aw - Tasu liik 
/*

@classinfo syslog_type=ST_ROSTERING_PAYMENT_TYPE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@tableinfo aw_rostering_payment_type index=aw_oid master_index=brother_of master_table=objects
@default table=aw_rostering_payment_type
@default group=general

	@property hr_price type=textbox size=10 field=aw_hr_price
	@caption Tunnihind

	@property apply_from_hr type=select field=aw_apply_from_hr 
	@caption Kehtib alates

	@property apply_to_hr type=select field=aw_apply_to_hr 
	@caption Kehtib kuni

	@property apply_day_types type=chooser multiple=1 table=objects field=meta method=serialize
	@caption Mis p&auml;evadel kehtib

	@property apply_overtime type=checkbox ch_value=1 field=aw_apply_overtime
	@caption Kehtib &uuml;letunnit&ouml;&ouml; jaoks
*/

class rostering_payment_type extends class_base
{
	function rostering_payment_type()
	{
		$this->init(array(
			"tpldir" => "applications/rostering/rostering_payment_type",
			"clid" => CL_ROSTERING_PAYMENT_TYPE
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
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

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_rostering_payment_type (aw_oid int primary key, aw_hr_price double)");
			return true;
		}

		switch($f)
		{
			case "aw_apply_from_hr":
			case "aw_apply_to_hr":
			case "aw_apply_overtime":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
				break;
		}
	}

	function _get_apply_from_hr($arr)
	{
		$arr["prop"]["options"] = array("" => t("--vali--"));
		for ($i = 0; $i < 24; $i++)
		{
			$arr["prop"]["options"][$i] = sprintf("%02d:00", $i);
		}
	}

	function _get_apply_to_hr($arr)
	{
		$arr["prop"]["options"] = array("" => t("--vali--"));
		for ($i = 0; $i < 24; $i++)
		{
			$arr["prop"]["options"][$i] = sprintf("%02d:00", $i);
		}
	}

	function _get_apply_day_types($arr)
	{
		$arr["prop"]["options"] = array(
			"work" => t("T&ouml;&ouml;p&auml;evadel"),
			"weekend" => t("N&auml;dalavahetustel"),
			"holiday" => t("P&uuml;hadel")
		);
	}
}
?>
