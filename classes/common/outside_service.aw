<?php
/*
@classinfo syslog_type=ST_OUTSIDE_SERVICE relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property url type=textbox
@caption Aadress kuhu suunatakse peale makset

*/

class outside_service extends class_base
{
	function outside_service()
	{
		$this->init(array(
			"tpldir" => "common/outside_service",
			"clid" => CL_OUTSIDE_SERVICE
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

	/**
		@param id required type=int acl=view
	**/
	function bank_return($arr)
	{
		if(!(is_oid($arr["id"]) && $this->can("view" , $arr["id"])))
		{
			return aw_ini_get("room_reservation.unsuccessful_bank_payment_url");
		}
		aw_disable_acl();
		$o = obj($arr["id"]);
		$url = $o->prop("url");
		aw_restore_acl();
		header("Location:".$url);
		die();
	}
}

?>
