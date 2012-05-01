<?php
// mobi_handler.aw - Mobi SMS haldur
/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

	@property service_id type=textbox field=meta method=serialize
	@caption Teenuse ID

	@property mpassword type=password field=meta method=serialize
	@caption Parool

	@property url type=textbox field=meta method=serialize
	@caption Mobi URL

	@property phone_dir type=relpicker reltype=RELTYPE_MENU store=connect
	@caption Saajate kaust

@groupinfo send_sms caption="Saada SMS" submit=no
@default group=send_sms

	@property number type=textbox store=no
	@caption Telefoninumber

	@property message type=textarea rows=7 cols=50 store=no
	@caption S&otilde;num
	@comment Maksimaalselt 160 t&auml;hem&auml;rki.

	@property symbol_count type=textbox store=no size=3 value=0
	@caption T&auml;hem&auml;rke

	@property send type=submit action=presend_sms
	@caption Saada

@groupinfo log caption="Logi"
@default group=log

	@property log_tbl type=table store=no no_caption=1

@reltype MENU value=1 clid=CL_MENU
@caption Saajate kaust

*/

class mobi_handler extends class_base
{
	function mobi_handler()
	{
		$this->init(array(
			"tpldir" => "common/sms/service_provider/mobi_handler",
			"clid" => CL_MOBI_HANDLER
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "message":
				$prop["onkeyup"] = " if(this.value.length > 160) { this.value = this.value.substr(0, 160); } aw_get_el('symbol_count').value = this.value.length;";
				break;
		}

		return $retval;
	}

	function _get_log_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "number",
			"caption" => t("Number"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "message",
			"caption" => t("S&otilde;num"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "mobi_ans",
			"caption" => t("Mobi vastus"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center",
			"sortable" => 1,
		));
		$ol = new object_list(array(
			"class_id" => CL_SMS,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"parent" => $arr["obj_inst"]->id(),
					"CL_SMS.RELTYPE_SMS_SENT.RELTYPE_MOBI_HANDLER" => $arr["obj_inst"]->id(),
				)
			)),
		));
		foreach($ol->arr() as $o)
		{
			foreach($o->connections_from(array("type" => "RELTYPE_SMS_SENT")) as $conn)
			{
				$to = $conn->to();
				$t->define_data(array(
					"number" => $to->prop("phone.name"),
					"message" => $o->comment,
					"mobi_ans" => $to->comment,
					"time" => date("Y-d-m H:i:s", $to->created()),
					"timestamp" => $to->created(),
				));
			}
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

	/**
	@attrib name=presend_sms
	**/
	function presend_sms($arr)
	{
		$nrs = explode(",", $arr["number"]);
		$msg = $arr["message"];
		foreach($nrs as $nr)
		{
			$nr = preg_replace("/[^0-9]/", "", $nr);
			$i = 0;
			// Don't think there's any point in sending an empty message.
			if(!empty($nr) && !empty($msg))
			{
				$sms = $this->send_sms(array(
					"id" => $arr["id"],
					"number" => $nr,
					"message" => $msg,
					"sms" => &$i,
				));
			}
		}
		return $this->mk_my_orb("change", array("id" => $arr["id"], "group" => "log"), CL_MOBI_HANDLER);
		//return $arr["post_ru"];
	}

	/** Sends SMS via Mobi.
		@attrib name=send_sms api=1 params=name

		@param id required type=oid
			The OID of the Mobi handler object, that describes the service ID, password and Mobi URL.

		@param number optional type=string
			The phone number to send the SMS to. Must contain aera code, only digits. (Example: 3725123456)

		@param message optional type=string
			The content of the SMS. No more than 160 symbols.

		@param phone optional type=oid
			The phone object's OID to send the SMS to.

		@param sms optional type=oid
			The oid of SMS object to be sent.

		@example
			get_instance(CL_MOBI_HANDLER)->send_sms(array(
				"id" => 2312,
				"number" => "3725123456",
				"message" => "The quick brown fox jumps over the lazy dog!",
			));

			get_instance(CL_MOBI_HANDLER)->send_sms(array(
				"id" => 2312,
				"sms" => $sms_obj->id(),
				"phone" => $phone_obj->id(),
			));

		@comment
			Either 'sms' or 'message' must be set. If 'sms' attribute is set, 'message' is ignored.
			Either 'phone' or 'number' must be set. If 'phone' attribute is set, 'number' is ignored.

		@returns The sms_sent object.

		@errors Returns error if number contains other symbols beside digits. Returns error if message contains more than 160 symbols.
	**/
	function send_sms($arr)
	{
		if(!preg_match("/\d*/", $arr["number"]))
		{
			error::raise(array(
				"id" => "ERR_PARAM",
				"msg" => t("mobi_handler::send_sms(number => ".$arr['number']."): number must only contain digits!")
			));
		}

		if(strlen($arr["message"]) > 160)
		{
			error::raise(array(
				"id" => "ERR_PARAM",
				"msg" => t("mobi_handler::send_sms(message => ".$arr['message']."): message must be no more than 160 symbols!")
			));
		}
		$o = obj($arr["id"]);
		$service_id = $o->prop("service_id");
		$password = $o->prop("mpassword");
		$url = $o->prop("url");
		$request_id = $o->meta("request_id");
		// This has to be unique every time. So we'll increase it no matter what.
		$o->set_meta("request_id", $request_id + 1);
		$o->save();

		if($this->can("view", $arr["phone"]))
		{
			$phone_obj = obj($arr["phone"]);
			$arr["number"] = $phone_obj->name;
		}
		else
		{
			$phone_obj = obj();
			$phone_obj->set_class_id(CL_CRM_PHONE);
			if($this->can("add", $o->phone_dir))
			{
				$phone_obj->set_parent($o->phone_dir);
			}
			else
			{
				$phone_obj->set_parent($o->id());
			}
			$phone_obj->name = $arr["number"];
			$phone_obj->save();
		}

		if($this->can("view", $arr["sms"]))
		{
			$sms_obj = obj($arr["sms"]);
			$arr["message"] = $sms_obj->comment;
		}
		else
		{
			$sms_obj = obj();
			$sms_obj->set_class_id(CL_SMS);
			$sms_obj->set_parent($o->id());
			$sms_obj->comment = $arr["message"];
			$sms_obj->save();
		}

		$params = array(
			"serviceid" => $service_id,
			"password" => $password,
			"phone" => $arr["number"],
			"text" => $arr["message"],
			"requestid" => $request_id,
		);
		$args = array(
			"http" => array(
				"method" => "POST",
				"header" => "Content-type: application/x-www-form-urlencoded",
				"content" => http_build_query($params),
			)
		);
		$context = stream_context_create($args);
		$mobi_answer = file_get_contents($url, false, $context);

		$sms_sent = obj();
		$sms_sent->set_class_id(CL_SMS_SENT);
		$sms_sent->set_parent($sms_obj->id());
		$sms_sent->comment = $mobi_answer;
		$sms_sent->save();

		$sms_obj->connect(array(
			"to" => $sms_sent->id(),
			"type" => "RELTYPE_SMS_SENT",
		));

		$sms_sent->connect(array(
			"to" => $phone_obj,
			"type" => "RELTYPE_PHONE",
		));

		$sms_sent->connect(array(
			"to" => $o->id(),
			"type" => "RELTYPE_MOBI_HANDLER",
		));

		//return substr($mobi_answer, 0, 2) == "OK";
		return $sms_sent;
	}
}
