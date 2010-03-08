<?php
// $Header: /home/cvs/automatweb_dev/classes/vcl/reminder.aw,v 1.5 2008/01/31 13:55:36 kristo Exp $
// reminder UI VCL component
/*
@classinfo  maintainer=kristo
*/
class reminder extends core
{
	function reminder()
	{
		$this->init("");
		// so I have to somehow check, whether the table I need for saving my things, exists.
	}

	function init_vcl_property($arr)
	{
		$prop = &$arr["property"];
		$this->obj = $arr["obj_inst"];
		$user = new object(aw_global_get("uid_oid"));
		// always use id, even if it is a brother
		$event_id = $arr["obj_inst"]->id();
		// now figure out some kind of date
		
		$rinst = get_instance(CL_CALENDAR_REMINDER);
		
		$old_evt = $rinst->get_reminder_for(array(
			"event_id" => $event_id,
			"user_id" => $user->id(),
		));
		
		$name = $prop["name"];
		$email = $old_evt ? $old_evt->prop("email") : $user->prop("email");

		// this needs to return an array of elements
		$rv = array();
		$rv["set_reminder"] = array(
			"type" => "checkbox",
			"ch_value" => 1,
			"name" => "${name}[set_reminder]",
			"caption" => t("Meeldetuletus e-postiga"),
			"value" => ($old_evt && $old_evt->status() == STAT_ACTIVE) ? 1 : 0,
		);

		if ($old_evt)
		{
			$delay = $arr["obj_inst"]->prop("start1") - $old_evt->prop("remind_at");
			$remind_time = $delay / 60;
		}
		else
		{
			$remind_time = 5; // sensible default
		};

		$rv["email_addr"] = array(
			"type" => "textbox",
			"name" => "${name}[email_addr]",
			"caption" => t("E-posti aadress"),
			"value" => $email,
		);

		$rv["remind_time"] = array(
			"type" => "select",
			"name" => "${name}[remind_time]",
			"caption" => t("Minutid"),
			"options" => array(
				5 => 5,
				10 => 10,
				15 => 15,
				30 => 30,
				60 => 60,
				120 => 120,
			),
			"value" => $remind_time,
		);

		$rv["remind_status"] = array(
			"type" => "text",
			"name" => "${name}[remind_status]",
			"caption" => t("Saadetud?"),
			"value" => ($old_evt && $old_evt->prop("reminder_sent") == 1) ? "jah" : "ei",
		);
		return $rv;
	}

	function process_vcl_property($arr)
	{
		$prop = $arr["prop"];
		$rdata = $prop["value"];

		// figure out user object id
		$users = get_instance("users");
		$uid = aw_global_get("uid");
		$user = new object($users->get_oid_for_uid($uid));
		// always use id, even if it is a brother
		$event_id = $arr["obj_inst"]->id();
		// now figure out some kind of date

		$rinst = get_instance(CL_CALENDAR_REMINDER);
		$old_evt = $rinst->get_reminder_for(array(
			"event_id" => $event_id,
			"user_id" => $user->id(),
		));

		if (empty($rdata["set_reminder"]) && $old_evt)
		{
			//print "I have to delete the bloody object<br>";
			$old_evt->set_status(STAT_NOTACTIVE);
			$old_evt->save();
			//$old_evt->delete();
		}
		else
		{
			$start_tm = $arr["obj_inst"]->prop("start1");
			$diff = $rdata["remind_time"];
			$remind_start = $start_tm - ($diff * 60);

			if (!$old_evt)
			{
				// lets create one then
				$reminder_obj = new object();
				$reminder_obj->set_parent($arr["obj_inst"]->id());
				$reminder_obj->set_class_id(CL_CALENDAR_REMINDER);
				$reminder_obj->set_status(STAT_ACTIVE);
				$reminder_obj->set_prop("user_id",$user->id());
				$reminder_obj->set_prop("event_id",$event_id);
			}
			else
			{
				// update start date
				$reminder_obj = $old_evt;
			};

			// riiiight, but how do I create the table that I need?

			$reminder_obj->set_status(STAT_ACTIVE);
			$reminder_obj->set_prop("email",$rdata["email_addr"]);
			$reminder_obj->set_prop("remind_at",$remind_start);
			$reminder_obj->save();

			// I'm hoping this doesn't re-add the event
			$sched = get_instance("scheduler");
			$evt_url = $this->mk_my_orb("process_pending_reminders",array(),CL_CALENDAR_REMINDER,false,true);
		
			$sched->add(array(
				"event" => $this->mk_my_orb("process_pending_reminders", array(), "calendar_reminder", false, true),
				"time" => time()+90,   // every 2 minutes
			));

		};
	}

};
?>
