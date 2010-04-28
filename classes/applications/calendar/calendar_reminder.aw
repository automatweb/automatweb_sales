<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/calendar_reminder.aw,v 1.5 2007/12/06 14:32:55 kristo Exp $
// calendar_reminder.aw - Kalendri meeldetuletus 
/*

@classinfo syslog_type=ST_CALENDAR_REMINDER relationmgr=yes maintainer=kristo

@default table=objects
@default group=general


@default table=reminders

@property user_id type=textbox datatype=int
@caption Kasutaja id


@property event_id type=textbox datatype=int
@caption Eventi id

@property reminder_rule_id type=textbox datatype=int
@caption Meeldetuletuse reegli id

@property remind_at type=datetime_select 
@caption Meeldetuletuse aeg

@property reminder_sent type=checkbox ch_value=1 default=0 datatype=int
@caption Saadetud

@property email type=textbox
@caption E-post

@tableinfo reminders index=reminder_id master_table=objects master_index=brother_of

	mysql> describe reminders;
	+------------------+---------------------+------+-----+---------+-------+
	| Field            | Type                | Null | Key | Default | Extra |
	+------------------+---------------------+------+-----+---------+-------+
	| event_id         | bigint(20) unsigned |      |     | 0       |       |
	| user_id          | bigint(20) unsigned |      |     | 0       |       |
	| reminder_rule_id | bigint(20) unsigned |      |     | 0       |       |
	| remind_at        | bigint(20) unsigned |      |     | 0       |       |
	| reminder_sent    | tinyint(4)          | YES  |     | 0       |       |
	| reminder_id      | bigint(20) unsigned |      | PRI | 0       |       |
	+------------------+---------------------+------+-----+---------+-------+
	6 rows in set (0.00 sec)
*/

// we use this class to create/edit reminders

// thats object orienter approach for you, baby!

class calendar_reminder extends class_base
{
	const AW_CLID = 484;

	function calendar_reminder()
	{
		$this->init(array(
			"clid" => CL_CALENDAR_REMINDER
		));

	}


	////
	// !Returns a reminder object for an user or an event or both (match)
	function get_reminder_for($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CALENDAR_REMINDER,
			"event_id" => $arr["event_id"],
			"user_id" => $arr["user_id"],
		));

		// by current design something is very wrong if we get multiple reminder objects .. 
		// lets ignore that scenario for now
		return $ol->count() > 0 ? $ol->begin() : false;
	}

	/** the thing that will be invoked from scheduler and will send out any pending reminders
		
		@attrib name=process_pending_reminders nologin=1
	**/
	function process_pending_reminders($arr)
	{
		$sched = get_instance("scheduler");
		$sched->add(array(
			"event" => $this->mk_my_orb("process_pending_reminders", array(), "", false, true),
			"time" => time()+119,   // every 2 minutes
		));

		$ol = new object_list(array(
			"class_id" => CL_CALENDAR_REMINDER,
			"reminder_sent" => 0,
			"status" => STAT_ACTIVE,
			// XX: only ask for reminders in the future
		));
		$now = time();
		$c = 0;
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			// if remind_at is in the past, then send the message out
			$remind_at = $o->prop("remind_at");
			if ($remind_at < $now && $remind_at >= ($now - 1 - (2*60)))
			{
				print "n = $now, rat = " . $o->prop("remind_at") . "<br>";
				$event_obj = new object($o->prop("event_id"));
				$event_name = $event_obj->name();
				$event_start = $this->time2date($event_obj->prop("start1"),2);
				// XXX: see teade tuleks kuidagi kalendri juurde panna
				$msg = sprintf(t("Hei, varsti algab %s, täpsemalt kell %s, vaata et sa ära ei unusta!"), $event_name, $event_start);
				$c++;

				// now I need an e-mail address for the user
				//$user_obj = new object($o->prop("user_id"));
				$email = $o->prop("email");
				if (is_email($email))
				{
					send_mail($email,$event_name,$msg,"From: automatweb@automatweb.com");
				};
				// mark it as done
				//	$o->set_prop("reminder_sent",1);
				//	$o->save();
			};
		};
		print "Sent $c reminders";
		// re-set the scheduler
	}


	/*
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		};
		return $retval;
	}
	*/

	/*
	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{

		}
		return $retval;
	}	
	*/

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
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
