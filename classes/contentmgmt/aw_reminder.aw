<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/aw_reminder.aw,v 1.1 2009/06/09 10:36:29 instrumental Exp $
// aw_reminder.aw - Meeldetuletus 
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_DOCUMENT, on_rdisconnect_from)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_TO, CL_DOCUMENT, on_rdisconnect_to)

@classinfo syslog_type=ST_REMINDER relationmgr=yes no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property remind type=datetime_select year_from=2004 year_to=2010
@caption Millal meelde tuletab

@property sender type=textbox
@caption Saatja e-mail 

@property emails_text type=textarea cols=30 rows=5
@caption Saaja e-mail(id) (eraldada komaga)

@property subject type=textbox
@caption Teade

@groupinfo objects caption="Seotud objektid" submit=no

@property objects_toolbar type=toolbar no_caption=1 group=objects
@caption Objektide toolbar

@property objects type=table no_caption=1 group=objects
@caption Seotud objektid

@reltype REMINDER_OBJECT value=1 clid=CL_DOCUMENT,CL_MENU
@caption Seotud objekt

*/

class aw_reminder extends class_base
{
	function aw_reminder()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/reminder",
			"clid" => CL_REMINDER
		));
	}
	
	function on_rconnect_to($arr)
	{
		$con = &$arr["connection"];
		if($con->prop("from.class_id") == CL_REMINDER)
		{
			$to = $con->to();
			$to->connect(array(
				"to" => $con->prop("from"),
				"reltype" => "RELTYPE_REMINDER",
			));
		}
	}
	
	function on_rconnect_from($arr)
	{
		$con = &$arr["connection"];
		if($con->prop("to.class_id") == CL_REMINDER)
		{
			$to = $con->to();
			$to->connect(array(
				"to" => $con->prop("from"),
				"reltype" => "RELTYPE_REMINDER_OBJECT",
			));
		}
	}
	
	function on_rdisconnect_to($arr)
	{
		$con = &$arr["connection"];
		if($con->prop("from.class_id") == CL_REMINDER)
		{
			$to = $con->to();
			$to->disconnect(array(
				"from" => $con->prop("from"),
				"reltype" => "RELTYPE_REMINDER",
				"errors" => false,
			));
		}
	}
	
	function on_rdisconnect_from($arr)
	{
		$con = &$arr["connection"];
		if($con->prop("to.class_id") == CL_REMINDER)
		{
			$to = $con->to();
			$to->disconnect(array(
				"from" => $con->prop("from"),
				"reltype" => "RELTYPE_REMINDER_OBJECT",
				"errors" => false,
			));
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "remind":
				if($arr["new"])
				{
					$prop["value"] = -1;
				}
				break;
			case "emails_text":
				if($arr["new"])
				{
					$user = obj(aw_global_get("uid_oid"));
					if($mail = $user->get_first_obj_by_reltype("RELTYPE_EMAIL"))
					{
						$prop["value"] = $mail->prop("mail");
					}
				}
				break;
			case "sender":
				if($arr["new"])
				{
					$user = obj(aw_global_get("uid_oid"));
					if($mail = $user->get_first_obj_by_reltype("RELTYPE_EMAIL"))
					{
						$prop["value"] = $mail->prop("mail");
					}
				}
				break;
			case "objects_toolbar":
				$this->objects_toolbar($arr);
				break;
			case "objects":
				$this->objects_table($arr);
				break;
		};
		return $retval;
	}
	
	function callback_post_save($arr)
	{
		$scheduler = get_instance("scheduler");
		$rtrue = true;
		$rem = $arr["request"]["remind"];
		foreach(safe_array($rem) as $value)
		{
			if($value == "---")
			{
				$rtrue = false;
			}
		}
		if($rtrue)
		{
			$event = $this->mk_my_orb("init_action", array(
				"id" => $arr["obj_inst"]->id(),
			));
			$scheduler->remove(array("event" => $event));
			$scheduler->add(array(
				"time" => mktime($rem["hour"], $rem["minute"], 0, $rem["month"], $rem["day"], $rem["year"]), 
				"event" => $event,
				"uid" => aw_global_get("uid"),
				"auth_as_local_user" => true,
			));
		}
	}
	
	function objects_toolbar($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Eemalda valitud objektid"),
			"action" => "disconnect",
			"confirm" => t("Oled kindel, et soovid valitud objektid ajastamiselt eemaldada?"),
			"img" => "delete.gif",
		));
	}
	
	function objects_table($arr)
	{
		$classes = aw_ini_get("classes");
		$t = &$arr["prop"]["vcl_inst"];
		$var = array(
			"id" => t("ID"),
			"name" => t("Nimi"),
			"type" => t("Tüüp"),
		);
		foreach($var as $key => $val)
		{
			$t->define_field(array(
				"name" => $key,
				"caption" => $val,
			));
		}
		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
		$objs = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_REMINDER_OBJECT",
		));
		foreach($objs as $obj)
		{
			$t->define_data(array(
				"id" => $obj->prop("to"),
				"name" => html::get_change_url($obj->prop("to"), array(), $obj->prop("to.name")),
				"type" => $classes[$obj->prop("to.class_id")]["name"],
			));
		}
	}

	/**
		@attrib name=disconnect
		
		@param id required type=int acl=edit
		@param group optional
		@param sel required
	**/
	function disconnect($arr)
	{
		$obj_inst = obj($arr["id"]);
		foreach(safe_array($arr["sel"]) as $key => $value)
		{
			$obj = obj($value);
			$obj_inst->disconnect(array(
				"from" => $value,
				"reltype" => "RELTYPE_REMINDER_OBJECT",
				"errors" => false,
			));
		}
		return html::get_change_url($arr["id"], array("group" => $arr["group"]));
	}
	
	/**
		@attrib name=init_action
		
		@param id required type=int acl=view
	**/
	function init_action($arr)
	{
		$obj_inst = obj($arr["id"]);
		$msg = $obj_inst->prop("subject");
		$subject = !empty($msg) ? $msg : "Meeldetuletus saidilt:\n";
		$awm = get_instance("protocols/mail/aw_mail");
		$objs = $obj_inst->connections_from(array(
			"type" => "RELTYPE_REMINDER_OBJECT",
		));
		$emls = array();
		$emails = explode(",", $obj_inst->prop("emails_text"));
		foreach(safe_array($emails) as $key)
		{
			$key = trim($key);
			$emls[] = $key;
		}
		foreach($objs as $obz)
		{
			$body = $subject." ".$this->mk_my_orb("change", array("id" => $obz->prop("to")), $obz->prop("to.class_id"), true);
			foreach($emls as $addr)
			{
				$froma = $obj_inst->prop("sender");
				$awm->create_message(array(
					"froma" => (!empty($froma) ? $froma : t("aw").str_replace("http://", "@", aw_ini_get("baseurl"))),
					"subject" => t("Meeldetuletus").": ".$obj_inst->name(),
					"to" => $addr,
					"body" => $body,
				));
				$awm->gen_mail();
			}
		}
		return "done";
	}
}
?>
