<?php
// sms_sent.aw - SMSi saatmine
/*

@classinfo syslog_type=ST_SMS_SENT relationmgr=yes no_status=1 prop_cb=1

@default table=objects
@default group=general

	@property phone type=relpicker reltype=RELTYPE_PHONE store=connect
	@caption Telefon

@reltype PHONE value=1 clid=CL_CRM_PHONE
@caption Telefon

@reltype MOBI_HANDLER value=2 clid=CL_MOBI_HANDLER
@caption Mobi SMSi haldur

*/

class sms_sent extends class_base
{
	function sms_sent()
	{
		$this->init(array(
			"tpldir" => "common/sms/sms_sent",
			"clid" => CL_SMS_SENT
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
}

?>
