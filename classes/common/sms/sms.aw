<?php

namespace automatweb;
// sms.aw - SMS
/*

@classinfo syslog_type=ST_SMS relationmgr=yes no_status=1 prop_cb=1 maintainer=instrumental

@default table=objects
@default group=general

	@property comment type=textarea field=comment
	@caption S&otilde;num

@groupinfo sent caption="Saatmised"
@default group=sent

	@property sent_tbl type=table store=no no_caption=1

@reltype PHONE value=1 clid=CL_CRM_PHONE
@caption Saaja

@reltype SMS_SENT value=2 clid=CL_SMS_SENT
@caption SMSi saatmine

*/

class sms extends class_base
{
	const AW_CLID = 1417;

	function sms()
	{
		$this->init(array(
			"tpldir" => "common/sms/sms",
			"clid" => CL_SMS
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

	function _get_sent_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "number",
			"caption" => t("Number"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "mobi_answer",
			"caption" => t("Mobi vastus"),
			"align" => "center",
			"sortable" => 1,
		));
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SMS_SENT")) as $conn)
		{
			$to = $conn->to();
			$t->define_data(array(
				"number" => $to->prop("phone.name"),
				"time" => date("Y-m-d H:i:s", $to->created()),
				"timestamp" => $to->created(),
				"mobi_answer" => $to->comment,
			));
		}
		$t->set_default_sortby("timestamp");
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
