<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/rostering/rostering_shift.aw,v 1.4 2007/12/06 14:34:03 kristo Exp $
// rostering_shift.aw - Vahetus 
/*

@classinfo syslog_type=ST_ROSTERING_SHIFT relationmgr=yes no_status=1 prop_cb=1 maintainer=kristo

@tableinfo aw_rostering_shift master_index=brother_of master_table=objects index=aw_oid

@default table=objects
@default group=general

	@property short_name type=textbox  table=aw_rostering_shift field=aw_short_name
	@caption L&uuml;hend

	@property type type=select table=aw_rostering_shift field=aw_type
	@caption T&uuml;&uuml;p

	@property start_time type=textbox size=10 table=aw_rostering_shift field=aw_start_time
	@caption Algusaeg

	@property end_time type=textbox size=10 table=aw_rostering_shift field=aw_end_time
	@caption L&otilde;ppaeg

	@property workplaces type=relpicker multiple=1 automatic=1 store=connect reltype=RELTYPE_WORKPLACE
	@caption T&ouml;&ouml;postid mis peavad olema t&auml;idetud

@reltype WORKPLACE value=1 clid=CL_ROSTERING_WORKPLACE
@caption T&ouml;&ouml;post
*/
define("SHIFT_TYPE_MAIN", 0);
define("SHIFT_TYPE_BACKUP", 1);
define("SHIFT_TYPE_HOME", 2);

class rostering_shift extends class_base
{
	const AW_CLID = 1141;

	function rostering_shift()
	{
		$this->init(array(
			"tpldir" => "applications/rostering/rostering_shift",
			"clid" => CL_ROSTERING_SHIFT
		));
		$this->types = array(
			SHIFT_TYPE_MAIN => t("P&otilde;hivahetus"),
			SHIFT_TYPE_BACKUP => t("Abivahetus"),
			SHIFT_TYPE_HOME => t("Valvevahetus")
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "type":
				$prop["options"] = $this->types;
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
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_rostering_shift (aw_oid int primary key, aw_start_time varchar(10), aw_end_time varchar(10))");
			return true;
		}

		switch($f)
		{
			case "aw_type":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;

			case "aw_short_name":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
				return true;
		}
	}
}
?>
