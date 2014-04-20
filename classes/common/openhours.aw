<?php
/*
@classinfo syslog_type=ST_OPENHOURS relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_openhours master_index=brother_of master_table=objects index=aw_oid

@default table=aw_openhours
@default group=general

@property days type=text field=aw_days
@caption P&auml;evad

@property open type=text field=aw_open
@caption Avamisaeg

@property close type=text field=aw_close
@caption Sulgemisaeg

@property valid_from type=text field=aw_valid_from
@caption Kehtiv alates

@property valid_to type=text field=aw_valid_to
@caption Kehtiv kuni

*/

class openhours extends class_base
{
	function openhours()
	{
		$this->init(array(
			"tpldir" => "common",
			"clid" => openhours_obj::CLID
		));
	}

	public function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_openhours(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_days":
			case "aw_open":
			case "aw_close":
			case "aw_valid_from":
			case "aw_valid_to":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int(11)"
				));
				return true;
		}
	}
}
	
?>
