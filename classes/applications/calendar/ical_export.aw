<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/ical_export.aw,v 1.13 2008/04/28 13:59:24 kristo Exp $
/*

@classinfo syslog_type=ST_ICAL_EXPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert

@default table=objects
@default group=general
	
	@property export_tb type=toolbar no_caption=1 store=no	

	@property name type=textbox table=objects
	@caption Nimi

	@property calendar type=relpicker reltype=RELTYPE_CALENDAR field=meta method=serialize store=connect
	@caption Kalender

	@property startdate type=datetime_select default=-1 field=meta method=serialize
	@caption Ajavahemiku algus

	@property enddate type=datetime_select default=-1 field=meta method=serialize
	@caption Ajavahemiku l&otilde;pp

	@property personal_not type=checkbox ch_value=1 field=meta method=serialize
	@caption &Auml;ra ekspordi isiklikke s&uuml;ndmusi

	@property url type=text store=no
	@caption Faili url

@groupinfo google_calendar caption="Google kalender"
@default group=google_calendar

	@groupinfo google_calendar_settings caption="Seaded" parent=google_calendar
	@default group=google_calendar_settings
	
		@property google_calendar_settings_calendar type=select field=meta method=serialize
		@caption Vali Google kalender
		
		@property google_calendar_settings_newcalname type=textbox store=no
		@caption Uue kalendri nimi
		
		@property google_calendar_settings_color type=select store=no
		@caption V&auml;rv
		
		@property google_calendar_settings_calendar_url type=hidden store=no
	
	@groupinfo google_calendar_user caption="Kasutaja" parent=google_calendar
	@default group=google_calendar_user
	
		@property google_calendar_uid type=textbox field=meta method=serialize
		@caption Kasutaja
		
		@property google_calendar_password_1 type=password field=meta method=serialize
		@caption Parool
		
		@property google_calendar_password_2 type=password field=meta method=serialize
		@caption Parool uuesti
		
		
@reltype CALENDAR value=1 clid=CL_PLANNER
@caption Kalender
*/

class ical_export extends class_base
{
	const AW_CLID = 1348;

	// todo
	// vaatamis6igused kalendrile ning kalendriexpordi objektile
	// muutmis6igused, vaatamis jne 6igused kataloogie kus syndmused on
	// automaatseks

	function ical_export()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/ical_export",
			"clid" => CL_ICAL_EXPORT
		));
		
		ini_set ("include_path", ".:".aw_ini_get("basedir")."/addons/ZendGdata-1.0.3/library/");
		include_once("Zend/Loader.php");
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');

		define ("GOOGLE_CALENDAR_PASSWORD_1_HASH", "X1X1X1X1X1");
		define ("GOOGLE_CALENDAR_PASSWORD_2_HASH", "X2X2X2X2X2");
	}
	
	function _get_google_calendar_settings_newcalname($arr)
	{
		$o = & $arr["obj_inst"];
		
		if ($o->prop("google_calendar_settings_calendar") ==  "new_cal")
		{
			
		}
		else
		{
			return PROP_IGNORE;
		}
	}
	
	function _set_google_calendar_settings_newcalname($arr)
	{
		$o = & $arr["obj_inst"];
		$prop = & $arr["prop"];
		
		if (strlen($prop["value"]) > 0 )
		{
			$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; // predefined service name for calendar
			$client = Zend_Gdata_ClientLogin::getHttpClient($o->prop("google_calendar_uid")."@gmail.com",$o->prop("google_calendar_password_1"),$service);
			
			$this->create_google_calendar(array(
				"obj_inst" => & $o,
				"client" => $client,
				"title" => $o->prop("google_calendar_settings_title"),
				//"summary" => $o->prop("google_calendar_settings_summary"),
				//"location" => $o->prop("google_calendar_settings_location"),
				"color" => $o->prop("google_calendar_settings_color"),
			));
		}
	}
	
	function _get_google_calendar_settings_calendar($arr)
	{
		$prop = & $arr["prop"];
		$o = & $arr["obj_inst"];
		
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; // predefined service name for calendar
		$client = Zend_Gdata_ClientLogin::getHttpClient($o->prop("google_calendar_uid")."@gmail.com",$o->prop("google_calendar_password_1"),$service);
		
		$gdata_cal = new Zend_Gdata_Calendar($client);
		//$cal_feed = $gdata_cal->getCalendarListFeed();
		$cal_feed = $gdata_cal->getCalendarEventFeed("http://www.google.com/calendar/feeds/default/owncalendars/full");

		$a_calendars["new_cal"] = t("Loo uus kalender...");
		foreach ($cal_feed as $calendar)
		{
			$a_calendars[$calendar->link[0]->href] = utf8_decode($calendar->title->text);
		}
		
		
		$prop["options"] = $a_calendars;
		$prop["selected"] = $prop["value"];
		$prop["size"] = count($a_calendars);
	}
	
	function timestamp_to_google_date($timestamp, $timezone="GMT")
	{
		if ($timezone=="GMT")
		{
			return gmdate  ("Y-m-d\TH:i:s.000\Z", $timestamp);
		}
		else if(strpos($timezone, "GMT+")===0)
		{
			$hourplus = substr($timezone, 4, 2);
			return gmdate  ("Y-m-d\TH:i:s.000+".$hourplus.":00", $timestamp);
		}
	}
	
	// i've found 2 different types of date strings in gcal:
	// a) 2008-02-06T10:00:00.000Z
	// b) 2008-02-06T10:00:00.000+02:00 ... where hours are ofc user defined timestamps from google cal settings
	// this function turns both types to greenwich timestamp
	function google_date_to_timestamp($s_date)
	{
		$year = substr($s_date, 0, 4);
		$month = substr($s_date, 5, 2);
		$day = substr($s_date, 8, 2);
		$hour = (int)substr($s_date, 11, 2);
		
		// if not greenwich time, turn it to 1
		if (substr ($s_date, -1,1) !="Z")
		{
			$hourminus = (int)substr($s_date, -5,2);
			$hour = $hour-$hourminus;
		}
		
		$minute = substr($s_date, 14, 2);
		$second = substr($s_date, 17, 2);
		
		$ts = gmmktime  ($hour, $minute, $second, $month, $day, $year);
		return $ts;
	}
	
	function _set_automatic_sync($arr)
	{
		$prop = & $arr["prop"];
		$o = & $arr["obj_inst"];
		
		if ($prop["value"] == 1)
		{
			$this->google_calendar_sync($arr);
			
			$sc = get_instance("scheduler");
			$sc->add(array(
            	"event" => $this->mk_my_orb("sync_google", array("id" => $o->id())),
				"time" => time()+30,
			));
		}
		return PROP_OK;
	}
	
	// codes from  http://www.mail-archive.com/google-calendar-help-dataapi@googlegroups.com/msg04033.html
	function _get_google_calendar_settings_color($arr)
	{
		$o = & $arr["obj_inst"];
		
		if ($o->prop("google_calendar_settings_calendar") !=  "new_cal")
		{
			return PROP_IGNORE;
		}
		
	    $property =& $arr["prop"];
	    $property["options"]  = array(
				"choose" => t("Vali..."),
	           	"#A32929" => "#A32929",	
				"#B1365F" => "#B1365F",
				"#7A367A" => "#7A367A",
				"#5229A3" => "#5229A3",
				"#29527A" => "#29527A",
				"#2952A3" => "#2952A3",
				"#1B887A" => "#1B887A",
				"#28754E" => "#28754E",
				"#0D7813" => "#0D7813",
				"#528800" => "#528800",
				"#88880E" => "#88880E",
				"#AB8B00" => "#AB8B00",
				"#BE6D00" => "#BE6D00",
				"#B1440E" => "#B1440E",
				"#865A5A" => "#865A5A",
				"#705770" => "#705770",
				"#4E5D6C" => "#4E5D6C",
				"#5A6986" => "#5A6986",
				"#4A716C" => "#4A716C",
				"#6E6E41" => "#6E6E41",
				"#8D6F47" => "#8D6F47",
	    );
		
	    $property["options_styles"]  = array(
				"choose" => "",
	           	"#A32929" => "background: #A32929; color: white;",	
				"#B1365F" => "background: #B1365F; color: white;",
				"#7A367A" => "background: #7A367A; color: white;",
				"#5229A3" => "background: #5229A3; color: white;",
				"#29527A" => "background: #29527A; color: white;",
				"#2952A3" => "background: #2952A3; color: white;",
				"#1B887A" => "background: #1B887A; color: white;",
				"#28754E" => "background: #28754E; color: white;",
				"#0D7813" => "background: #0D7813; color: white;",
				"#528800" => "background: #528800; color: white;",
				"#88880E" => "background: #88880E; color: white;",
				"#AB8B00" => "background: #AB8B00; color: white;",
				"#BE6D00" => "background: #BE6D00; color: white;",
				"#B1440E" => "background: #B1440E; color: white;",
				"#865A5A" => "background: #865A5A; color: white;",
				"#705770" => "background: #705770; color: white;",
				"#4E5D6C" => "background: #4E5D6C; color: white;",
				"#5A6986" => "background: #5A6986; color: white;",
				"#4A716C" => "background: #4A716C; color: white;",
				"#6E6E41" => "background: #6E6E41; color: white;",
				"#8D6F47" => "background: #8D6F47; color: white;",
	    );
	}
	
	
	function _set_do_import($arr)
	{
		$prop = & $arr["prop"];
		if ($prop["value"] == 1)
		{
			$this->google_calendar_sync($arr);
		}
	}
	
	 /**
		@attrib name=sync_google all_args=1 nologin=1
		@param id required type=int acl=view
		@param cal_parent required type=int
	**/
	function sync_google($arr)
	{
		$this->google_calendar_sync($arr);
		echo "valma";
		die();
	}
	
	function google_calendar_sync($arr)
	{
		$o = new object($arr["id"]);
		ini_set ("include_path", ".:".aw_ini_get("basedir")."/addons/ZendGdata-1.0.3/library/");
		include_once("Zend/Loader.php");
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
		
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; // predefined service name for calendar
		$client = Zend_Gdata_ClientLogin::getHttpClient($o->prop("google_calendar_uid")."@gmail.com",$o->prop("google_calendar_password_1"),$service);
		
		// todo timezone to aw admin -- it should be dropdown
		//$this->create_google_calendar(array(
		//		"obj_inst" => & $o,
		//		"client" => $client,
		//		"title" => $o->prop("google_calendar_settings_title"),
		//		"summary" => $o->prop("google_calendar_settings_summary"),
		//		"location" => $o->prop("google_calendar_settings_location"),
		//		"color" => $o->prop("google_calendar_settings_color"),
		//));
		$this->sync_events(array(
				"obj_inst" => & $o,
				"client" => $client,
				"cal_parent" => $arr["cal_parent"]
		));
	}
	
	function sync_events($arr)
	{
		$a_aw_events = $this->get_aw_calendar_events_array($arr);
		$a_google_events = $this->get_google_calendar_events_array($arr);
		
		$b_created_new_aw_events = false;
		foreach ($a_google_events as $event)
		{
			$this->db_query("SELECT id FROM aw_google_calendar_event_relations_to_aw_events WHERE aw_calexport_id = ".$arr["obj_inst"]->id()." AND google_id = '".$event["google_id"]."';");
			$row = $this->db_next();
			if (!isset($row["id"]))
			{
				$b_created_new_aw_events =true;
				$o = new object(array(
					"class_id" => CL_CALENDAR_EVENT,
					"parent" => $arr["cal_parent"],
					"name" => $event["title"],
				));
				$o->set_class_id(CL_CALENDAR_EVENT);
				$o->set_prop("start1", $event["start"]);
				$o->set_prop("end", $event["end"]);
				$o->set_prop("description", $event["description"]);
				$o->set_status(STAT_ACTIVE);
				$o->save();
				//$this->db_query("update objects set modified=".$event["modified"]." WHERE oid=".$o->id());
				//$this->db_query("insert into aw_google_calendar_event_relations_to_aw_events (aw_calexport_id, aw_id,aw_modified, google_id, google_modified) values (".$arr["obj_inst"]->id().", ".$o->id().",".$event["modified"].", '".$event["google_id"]."', ".$event["modified"].")");
				$i_time = time();
				$this->db_query("insert into aw_google_calendar_event_relations_to_aw_events (aw_calexport_id, aw_id,google_id, modified) values (".$arr["obj_inst"]->id().", ".$o->id().", '".$event["google_id"]."', ".$i_time.")");
				
			}
		}
		
		// now fetch new events from aw calendar to google calendar
		$b_created_new_google_events = false;
		foreach ($a_aw_events as $event)
		{
			$this->db_query("SELECT id FROM aw_google_calendar_event_relations_to_aw_events WHERE aw_calexport_id = ".$arr["obj_inst"]->id()." AND aw_id = ".$event["aw_id"]);
			$row = $this->db_next();
			if (!isset($row["id"]))
			{
				$title = utf8_encode ($event["title"]);
				$start = $event["start"];
				$end = $event["end"];
				$description = $event["description"];
				// todo: uses GMT+02... even if u set otherwise from google
				$google_id = $this->create_google_event ($arr["client"],$arr["obj_inst"]->prop("google_calendar_settings_calendar"), $title,$description,'', $start,$end, "GMT+02:00");
				//$this->db_query("insert into aw_google_calendar_event_relations_to_aw_events (aw_calexport_id, aw_id, aw_modified,  google_id, google_modified) values (".$arr["obj_inst"]->id().", ".$event["aw_id"].", ".$event["modified"].", '".$google_id."', ".$event["modified"].")");
				$i_time = time();
				$this->db_query("insert into aw_google_calendar_event_relations_to_aw_events (aw_calexport_id, aw_id, google_id, modified) values (".$arr["obj_inst"]->id().", ".$event["aw_id"].", '".$google_id."', ".$i_time.")");
			}
		}
		
		// fetch events again if needed
		if ($b_created_new_aw_events)
		{
			$a_aw_events = $this->get_aw_calendar_events_array($arr);
		}
		if ($b_created_new_google_events)
		{
			$a_google_events = $this->get_google_calendar_events_array($arr);
		}
		
		$this->db_query("SELECT id, aw_id, google_id, modified from aw_google_calendar_event_relations_to_aw_events");
		$a_modified = array();
		while($row = $this->db_next())
		{
			$b_newer_is_google = $b_newer_is_aw =false;
			if ($a_aw_events[$row["aw_id"]]["modified"] > $a_google_events[$row["google_id"]]["modified"] )
			{
				$i_newest_time = $a_aw_events[$row["aw_id"]]["modified"];
				$b_newer_is_aw = true;
			}
			else if ( $a_aw_events[$row["aw_id"]]["modified"] < $a_google_events[$row["google_id"]]["modified"] )
			{
				$i_newest_time = $a_google_events[$row["google_id"]]["modified"];
				$b_newer_is_google = true;
			}
			else
			{
				continue;
			}
			
			// and finally now we know from where to copy
			if ($i_newest_time > $row["modified"])
			{
				if ($b_newer_is_aw)
				{
					$a_googleid = explode ("/", $row["google_id"]);
					$s_userid = str_replace ("%40", "@", $a_googleid[5]);
					$s_eventid = end (explode ("/", $row["google_id"]));
					$this->update_google_event(array(
						"client" => $arr["client"],
						"event_id" => $s_eventid,
						"user_id" => $s_userid,
						"new_title" => $a_aw_events[$row["aw_id"]]["title"],
						"new_description" => $a_aw_events[$row["aw_id"]]["description"],
					));
				}
				else if ($b_newer_is_google)
				{
					$this->update_aw_event(array(
						"oid" => $row["aw_id"],
						"title" => $a_google_events[$row["google_id"]]["title"],
						"start" => $a_google_events[$row["google_id"]]["start"],
						"end" => $a_google_events[$row["google_id"]]["end"],
						"description" => $a_google_events[$row["google_id"]]["description"],
					));
					$a_modified[] = array(
						"id" => $row["id"],
					);
				}
			}
		}
		
		// finalize
		// change modified date in aw_google_calendar_event_relations_to_aw_events
		// cuz we can't do it in 'while' cycle above
		foreach ($a_modified as $mod)
		{
			$this->db_query("update aw_google_calendar_event_relations_to_aw_events set modified=".time()." WHERE id=".$mod["id"]);
		}
		
	}
	
	// todo: c if some fields are not the same on other event types
	function update_aw_event($arr)
	{
		$o = obj($arr["oid"]);
		$o->set_name($arr["title"]);
		$o->set_prop("start1", $arr["start"]);
		$o->set_prop("end", $arr["end"]);
		$i_class = $o->class_id();
		$o->set_prop("content", $arr["description"]);
		$o->save();
	}
	
	function update_google_event ($arr) 
	{
		extract($arr);
		$gdataCal = new Zend_Gdata_Calendar($client);
		if ($eventOld = $this->getEvent($client, $user_id, $event_id))
		{
			$eventOld->title = $gdataCal->newTitle($new_title);
			$eventOld->content = $gdataCal->newContent($new_description);
			try {
				$eventOld->save();
			} catch (Zend_Gdata_App_Exception $e) {
				arr($e);
				return false;
			}
			return true;
		} else
		{
			return false;
		}
		
		//$event->delete($event_id);
		//$gdataCal = new Zend_Gdata_Calendar($client);
		//$gdataCal->delete($event_id); 
	}
	
	function getEvent($client, $userid, $eventId) 
	{
	  $gdataCal = new Zend_Gdata_Calendar($client);
	  $query = $gdataCal->newEventQuery();
	  //$query->setUser('default');
	  $query->setUser($userid);
	  $query->setVisibility('private');
	  $query->setProjection('full');
	  $query->setEvent($eventId);
	
	  try {
	    $eventEntry = $gdataCal->getCalendarEventEntry($query);
	    return $eventEntry;
	  } catch (Zend_Gdata_App_Exception $e) {
	    var_dump($e);
	    return null;
	  }
	}
	
	function get_google_calendar_events_array($arr)
	{
		$a_events = array();
		$client = $arr["client"];
		$obj = $arr["obj_inst"];
		$gdataCal = new Zend_Gdata_Calendar($client);
		$eventFeed = $gdataCal->getCalendarEventFeed($obj->prop("google_calendar_settings_calendar"));
		foreach ($eventFeed as $event) {
			$a_events[$event->id->text] = array(
				"google_id" => $event->id->text,
				"title" => utf8_decode ($event->title->text),
				"description" => utf8_decode ($event->content->text),
				"where" => utf8_decode ($event->where->text),
				"modified" => $this->google_date_to_timestamp($event->updated->text),
				"modified_raw" =>$event->updated->text,
			);
			foreach ($event->when as $when)
			{
				$start = $when->startTime;
				$a_events[$event->id->text]["start_raw"] = $start;
				$a_events[$event->id->text]["start"] = $this->google_date_to_timestamp($start);
				$end = $when->endTime;
				$a_events[$event->id->text]["end"] = $this->google_date_to_timestamp($end);
				$a_events[$event->id->text]["end_raw"] = $end;
			}
			
			foreach ($event->where as $where) {
				$a_events[$event->id->text]["where"] = $where->valueString;
			}
		}
		return $a_events;
	}

	function get_aw_calendar_events_array($arr)
	{
		$a_events = array();
		$obj = $arr["obj_inst"];
		if($calid = $obj->prop("calendar"))
		{
			$cal = obj($calid);
			$ef = $cal->get_first_obj_by_reltype("RELTYPE_EVENT_FOLDER");
			$filters = array(
				"class_id" => array(CL_TASK, CL_CRM_CALL, CL_CRM_MEETING, CL_CALENDAR_EVENT),
				"parent" => $ef->id()
			);
			$events = new object_list($filters);
			
			foreach($events->ids() as $oid)
			{
				$event = obj($oid);
				
				switch($event->class_id())
				{
					case CL_TASK:
						$a_events[$event->id()] = array(
							"aw_id" => $event->id(),
							"title" => $event->prop("name"),
							"description" => $event->prop("content"),
							"start" => $event->prop("start1")+(60*60*2),
							"end" => $event->prop("end")+(60*60*2),
							"where" => $event->prop(""),
							"modified" => $event->modified(),
							"modified2" =>$this->timestamp_to_google_date($event->modified(), "GMT+00:00"),
						);
					break;
					case CL_CRM_CALL:
						$a_events[$event->id()] = array(
							"aw_id" => $event->id(),
							"title" => $event->prop("name"),
							"description" => $event->prop("content"),
							"start" => $event->prop("start1")+(60*60*2),
							"end" => $event->prop("end")+(60*60*2),
							"where" => $event->prop(""),
							"modified" => $event->modified(),
							"modified2" =>$this->timestamp_to_google_date($event->modified(), "GMT+00:00"),
						);
					break;
					case CL_CALENDAR_EVENT:
						$a_events[$event->id()] = array(
							"aw_id" => $event->id(),
							"title" => $event->prop("name"),
							"description" => $event->prop("content"),
							"start" => $event->prop("start1")+(60*60*2),
							"end" => $event->prop("end")+(60*60*2),
							"where" => $event->prop(""),
							"modified" => $event->modified(),
							"modified2" =>$this->timestamp_to_google_date($event->modified(), "GMT+00:00"),
						);
					break;
					case CL_CRM_MEETING:
						$a_events[$event->id()] = array(
							"aw_id" => $event->id(),
							"title" => $event->prop("name"),
							"description" => $event->prop("content"),
							"start" => $event->prop("start1")+(60*60*2),
							"end" => $event->prop("end")+(60*60*2),
							"where" => $event->prop(""),
							"modified" => $event->modified(),
							"modified2" =>$this->timestamp_to_google_date($event->modified(), "GMT+02:00"),
						);
					break;
				}
			}
		}
		return $a_events;
	}
	
	function _get_google_calendar_settings_title($arr)
	{
		$prop = & $arr["prop"];
		$o = & $arr["obj_inst"];
		if ($o->prop("google_calendar_settings_title") == "")
		{
			$o->set_prop("google_calendar_settings_title", $o->prop("name"));
			$o->save();
			$prop["value"] = $o->prop("name");
		}
		$prop["post_append_text"] = t(" &uuml;le 15 s&uuml;mboli (ka t&uuml;hikud) ei soovita nimeks, kuna pikem tekst j&auml;&auml;b Google kalendris paani taha peitu.");
	}
	
	// creating new calendar is hack for now cuz Google Calendar APIs don't support this
	// todo calendar name, color and stuff should be synced but right now we look at events
	function create_google_calendar($arr)
	{
		$obj = & $arr["obj_inst"];
		
		if ($obj->prop("google_calendar_url") == "")
		{
			$client = $arr["client"];
			$s_title = strlen($arr["title"])>0 ?  utf8_encode($arr["title"]) : t("aw kalender");
			$s_summary = utf8_encode($arr["summary"]);
			$s_location = utf8_encode($arr["location"]);
			$s_timezone = strlen($arr["timezone"])>0 ?  $arr["timezone"] : "Europe/Tallinn";
			$s_color = strlen($arr["color"])>0 ?  $arr["color"] : "#A32929";
			
			
			$xml = "<entry xmlns='http://www.w3.org/2005/Atom'
						xmlns:gd='http://schemas.google.com/g/2005'
						xmlns:gCal='http://schemas.google.com/gCal/2005'>
						<title type='text'>[TITLE]</title>
						<summary type='text'>[SUMMARY]</summary>
						<gCal:timezone value='[TIMEZONE]'></gCal:timezone>
						<gCal:hidden value='false'></gCal:hidden>
						<gCal:color value='[COLOR]'></gCal:color>
						<gd:where rel='' label='' valueString='[LOCATION]'></gd:where>
						</entry> ";
			
			// todo: summary aw kalendrisse
			// 
			$gdataCal = new Zend_Gdata_Calendar($client);
			$uri = 'http://www.google.com/calendar/feeds/default/owncalendars/full';
			$xml = str_replace('[TITLE]', $s_title, $xml);
			$xml = str_replace('[SUMMARY]', $s_summary, $xml);
			$xml = str_replace('[LOCATION]', $s_location, $xml);
			$xml = str_replace('[TIMEZONE]', $s_timezone, $xml);
			$xml = str_replace('[COLOR]', $s_color, $xml);
			$o_gcal_post = $gdataCal->post($xml, $uri);
			$s_cal_url = str_replace ("default/owncalendars/full/", "", $o_gcal_post->headers["Location"]) . "/private/full";
			$obj->set_prop("google_calendar_settings_calendar_url", $s_cal_url);
			$obj->save();
		}
	}
	
	function _get_google_calendar_uid($arr)
	{
		$prop = & $arr["prop"];
		$prop["post_append_text"] = " @gmail.com";
	
		//header('Content-Type: text/html; charset=utf-8');
		//error_reporting(E_ALL);
		/*
		ini_set ("include_path", ".:".aw_ini_get("basedir")."/addons/ZendGdata-1.0.3/library/");
		include_once("Zend/Loader.php");
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
		*/
		
		//$user = 'hkirsman@gmail.com';
		//$pass = 'm77cIQ';
		//$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; // predefined service name for calendar
		
		//$client = Zend_Gdata_ClientLogin::getHttpClient($user,$pass,$service);
		
		//$this->create_google_calendar($client);
		
		//$this->outputCalendarList($client);
		//$this->outputCalendar($client);
		//$this->createQuickAddEvent($client, "Dinner at Joe's on Friday at 8 PM");
		//$this->createEvent($client);
		//die();
	}
	
	function _get_google_calendar_settings_location($arr)
	{
		$prop = & $arr["prop"];
		$prop["post_append_text"] = t(" n&auml;iteks Tallinn v&otilde;i Haapsalu. Kui Google kalender on avalik, siis see aitab inimestel Sinu s&uuml;ndmusi paremini leida.");
	}
	
	function _get_google_calendar_password_1($arr)
	{
		$prop = & $arr["prop"];
		$prop["value"] = GOOGLE_CALENDAR_PASSWORD_1_HASH;
	}
	
	function _set_google_calendar_password_1($arr)
	{
		$prop = & $arr["prop"];
		if ($prop["value"] == GOOGLE_CALENDAR_PASSWORD_1_HASH)
		{
			return PROP_IGNORE;
		}
		return PROP_OK;
	}
	
	function _get_google_calendar_password_2($arr)
	{
		$prop = & $arr["prop"];
		$prop["value"] = GOOGLE_CALENDAR_PASSWORD_2_HASH;
	}
	
	function _set_google_calendar_password_2($arr)
	{
		$prop = & $arr["prop"];
		$o = & $arr["obj_inst"];
		
		if ($prop["value"] == GOOGLE_CALENDAR_PASSWORD_2_HASH)
		{
			return PROP_IGNORE;
		}
		
		if ($prop["value"] != $o->prop("google_calendar_password_1") )
		{
			$prop["error"] = t("Paroolid ei &uuml;hti");
			return PROP_FATAL_ERROR;
		}
		
		return PROP_OK;
	}
	
	function create_google_event ($client, $s_url, $title = 'nimetu', $desc='', $where = '',
    $start, $end, $tzOffset = 'GMT+02:00')
	{
		$gdataCal = new Zend_Gdata_Calendar($client);
		$newEvent = $gdataCal->newEventEntry();
		
		$newEvent->title = $gdataCal->newTitle($title);
		$newEvent->where = array($gdataCal->newWhere($where));
		$newEvent->content = $gdataCal->newContent($desc);
		
		$when = $gdataCal->newWhen();
		$when->startTime = $this->timestamp_to_google_date($start,  $tzOffset);
		$when->endTime = $this->timestamp_to_google_date($end,  $tzOffset);
		$newEvent->when = array($when);
		
		// Upload the event to the calendar server
		// A copy of the event as it is recorded on the server is returned
		// todo... why isn't second parameter working???
		//$createdEvent = $gdataCal->insertEvent($newEvent, $s_url);
		$createdEvent = $gdataCal->insertEvent($newEvent, $s_url);
		return $createdEvent->id->text;
	}
	
	function outputCalendar($client) 
	{
		$three_months_in_seconds = 60 * 60 * 24 * 28 * 3;
		$three_months_ago = date("Y-m-d\Th:i:sP", time() - $three_months_in_seconds);
		$three_months_from_today = date("Y-m-d\Th:i:sP", time() + $three_months_in_seconds);
		
		$gdataCal = new Zend_Gdata_Calendar($client);
		$query = $gdataCal->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$calID = 'default';
		$query->setUser($calID); 
		$query->setProjection('full');
		$query->setOrderby('starttime');
		$query->setFutureevents(true); 
		$query->setOrderby('starttime');
		$query->setStartMin($three_months_ago);
		$query->setStartMax($three_months_from_today);
	  	
		//arr($query->getQueryUrl(),1);
		//arr($query->getQueryUrl(),1);
		
		// Retrieve the event list from the calendar server
		try {
			//$eventFeed = $gdataCal->getCalendarEventFeed("http://www.google.com/calendar/feeds/484heptdg36fotti5fg7tnqrmc%40group.calendar.google.com/private/full");
			//$eventFeed = $gdataCal->getCalendarEventFeed("http://www.google.com/calendar/feeds/default/0hfd6vcbncn6p5hn1t4d2b2k4s%40group.calendar.google.com/private/full");
			
			//$eventFeed = $gdataCal->getCalendarEventFeed($query);
		} catch (Zend_Gdata_App_Exception $e) {
			echo "Error: " . $e->getResponse();
		}

	  echo "<ul>\n";
	  foreach ($eventFeed as $event) {
	    echo "\t<li>" . $event->title->text .  " (" . $event->id->text . ")\n";
	    echo "\t\t<ul>\n";
	    foreach ($event->when as $when) {
	      echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
	    }
	    echo "\t\t</ul>\n";
	    echo "\t</li>\n";
	  }
	  echo "</ul>\n";
	}
	
	function createQuickAddEvent ($client, $quickAddText) {
		$gdataCal = new Zend_Gdata_Calendar($client);
		$event = $gdataCal->newEventEntry();
		$event->content = $gdataCal->newContent($quickAddText);
		$event->quickAdd = $gdataCal->newQuickAdd('true');
		$newEvent = $gdataCal->insertEvent($event);
	}
	
	function get_google_calendar_url_by_name($client, $s_name)
	{
		$gdataCal = new Zend_Gdata_Calendar($client);
		$calFeed = $gdataCal->getCalendarListFeed();
		foreach ($calFeed as $calendar)
		{
			if ($calendar->title->text == $s_name )
			{
				return $calendar->link[0]->href;
			}
	  	}
	}
	
	function outputCalendarList($client) 
	{
		$gdataCal = new Zend_Gdata_Calendar($client);
		$calFeed = $gdataCal->getCalendarListFeed();
		echo '<h1>' . $calFeed->title->text . '</h1>';
		echo '<ul>';
		//arr($calFeed,1);
		foreach ($calFeed as $calendar) {
			echo '<li>' . $calendar->title->text . '</li>';
			arr($calendar->link[0]);
			
			try {
				//$eventFeed = $gdataCal->getCalendarEventFeed($calendar->id->text."/private/full");
				//$eventFeed = $gdataCal->getCalendarEventFeed($query);
			} catch (Zend_Gdata_App_Exception $e) {
				echo "Error: " . $e->getResponse();
			}
			/*
			echo "<ul>\n";
			foreach ($eventFeed as $event) {
			 echo "\t<li>" . $event->title->text .  " (" . $event->id->text . ")\n";
			 echo "\t\t<ul>\n";
			 foreach ($event->when as $when) {
			   echo "\t\t\t<li>Starts: " . $when->startTime . "</li>\n";
			 }
			 echo "\t\t</ul>\n";
			 echo "\t</li>\n";
			}
			echo "</ul>\n";
			*/
		
	  }
	  echo '</ul>';
	}
	
	/**
	@attrib name=export all_args=1 nologin=1
	**/
	function export($arr)
	{
		if(is_oid($arr["id"]))
		{
			$obj = obj($arr["id"]);
			$events = 0;
			if($arr["basket"])
			{
				$basket = obj($arr["basket"]);
				$bi = get_instance(CL_OBJECT_BASKET);
				$objs = $bi->get_basket_content($basket);
				$oids = array();
				foreach($objs as $o)
				{
					$oids[$o["oid"]] = $o["oid"];
				}
				$events = new object_list(array(
					"oid" => $oids
				));
			}
			elseif($calid = $obj->prop("calendar"))
			{
				$cal = obj($calid);
				$ef = $cal->get_first_obj_by_reltype("RELTYPE_EVENT_FOLDER");
				$filters = array(
					"class_id" => array(CL_TASK, CL_CRM_CALL, CL_CRM_MEETING, CL_CALENDAR_EVENT),
					"parent" => $ef->id()
				);
				if($arr["start"])
				{
					$filters["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $arr["start"]);
				}
				if($arr["end"])
				{
					$filters["end"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $arr["end"]);
				}
				$events = new object_list($filters);
			}
			if($events)
			{
				require_once(aw_ini_get("basedir").'/addons/ical/iCalcreator.aw');

				$c = new vcalendar();
				$c->setConfig("lang" ,"ee");

				foreach($events->ids() as $oid)
				{
					if($obj->prop("personal_not") && $event->prop("is_personal"))
					{
						continue;
					}
					$event = obj($oid);
					$this->setevent($event, $c);
				}
				header('Content-type: text/calendar; charset=UTF-8');
				header('Content-Disposition: attachment; filename="export.ics"');

				$str = $c->createCalendar();
				die(iconv(aw_global_get("charset"), "UTF-8", $str));
			}
		}
		return $this->mk_my_orb("change", array("id" => $arr["id"]));
	}

	function setevent($event, &$c)
	{
		switch($event->class_id())
		{
			case CL_TASK:
				$types = array(10,8);
				$e = new vtodo();
				$e->setProperty("priority", $event->prop("priority"));
				$e->setProperty("due", 
					date("Y", $event->prop("deadline")),
					date("m", $event->prop("deadline")),
					date("d", $event->prop("deadline")),
					date("H", $event->prop("deadline")),
					date("i", $event->prop("deadline")),
					date("s", $event->prop("deadline"))
				);
				break;
			case CL_CALENDAR_EVENT:
				$types = 0;
				$e = new vevent();
				$e->setProperty("dtend",
					date("Y", $event->prop("end")),
					date("m", $event->prop("end")),
					date("d", $event->prop("end")),
					date("H", $event->prop("end")),
					date("i", $event->prop("end")),
					date("s", $event->prop("end"))
				);
				break;
			case CL_CRM_CALL:
				$types = 9;
				$e = new vevent();
				$e->setProperty("dtend",
					date("Y", $event->prop("end")),
					date("m", $event->prop("end")),
					date("d", $event->prop("end")),
					date("H", $event->prop("end")),
					date("i", $event->prop("end")),
					date("s", $event->prop("end"))
				);
				break;
			case CL_CRM_MEETING:
				$types = 8;
				$e = new vevent();
				$e->setProperty("dtend",
					date("Y", $event->prop("end")),
					date("m", $event->prop("end")),
					date("d", $event->prop("end")),
					date("H", $event->prop("end")),
					date("i", $event->prop("end")),
					date("s", $event->prop("end"))
				);
				break;
		}
		if($types)
		{
			foreach($event->connections_to(array("type" => $types)) as $co)
			{
				$p = obj($co->conn["from"]);
				$email = obj($p->prop("email"));
				$e->setProperty("attendee", $email->name(), array(
					"PARTSTAT" => "NEEDS_ACTION",
					"RSVP" => "FALSE",
					"CN" => $p->name(),
					"ROLE" => "OPT-PARTICIPANT"
				));
			}
		}
		$e->setProperty("summary", $event->name());
		$e->setProperty("description", $event->comment());
		$e->setProperty("dtstart",
			date("Y", $event->prop("start1")),
			date("m", $event->prop("start1")),
			date("d", $event->prop("start1")),
			date("H", $event->prop("start1")),
			date("i", $event->prop("start1")),
			date("s", $event->prop("start1"))
		);
		$c->setComponent($e);
	}

	function get_property($arr)
	{
		$obj = $arr["obj_inst"];
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "export_tb":
				$tb = &$arr["prop"]["vcl_inst"];
				$tb->add_button(array(
					"name" => "submit",
					"img" => "save.gif",
					"tooltip" => "Salvesta failina",
					"url" => $this->mk_my_orb("export",array("id"=>$arr["obj_inst"]->id()))
				));
				break;

			case "url":
				$url = $this->mk_my_orb("export", array(
					"id" => $arr["obj_inst"]->id(),
					"start" => $obj->prop("startdate"), 
					"end"=> $obj->prop("enddate"),
				));
				$url = str_replace(array ("automatweb/", "?", "&"), array("", "/", "/"), $url)."/export.ics";
				$prop["value"] = html::href(array(
					"url" => $url,
					"caption" => $url
				));
				break;
			case "url_google":
				$url = $this->mk_my_orb("export_to_google_calendar", array(
					"id" => $arr["obj_inst"]->id(),
					"start" => $obj->prop("startdate"), 
					"end"=> $obj->prop("enddate"),
				));
				$url = str_replace(array ("automatweb/", "?", "&"), array("", "/", "/"), $url)."/export.ics";
				$prop["value"] = html::href(array(
					"url" => $url,
					"caption" => $url
				));
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
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_post_save($arr)
	{
		$obj = $arr["obj_inst"];
		if(!$obj->prop("calendar"))
		{
			$conn = $obj->connections_to(array(
				"type" => "RELTYPE_ICAL_EXPORT",
				"from.class_id" => CL_PLANNER
			));
			foreach($conn as $c)
			{
				$calendar = obj($c->conn["from"]);
			}
			if($calendar)
			{
				$obj->set_prop("calendar", $calendar->id());
			}
		}
	}
	
	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
	
	function callback_mod_tab($parm)
	{
		$obj = $parm["obj_inst"];
		$id = $parm['id'];
		
		if ($id == "google_calendar")
		{
			if ($obj->prop("calendar")==0)
			{
				return false;
			}
		}
		
		// this hides google settings tab before google account settings are not set
		{
			if ($id == "google_calendar")
			{
				if ($obj->prop("google_calendar_uid")=="" && $obj->prop("google_calendar_password_1") == "")
				{
					$parm["link"] = str_replace  ( "group=google_calendar", "group=google_calendar_user"  , $parm["link"]);
				}
			}
			
			if ($id == "google_calendar_settings")
			{
				if ($obj->prop("google_calendar_uid")== "" && $obj->prop("google_calendar_password_1") == "")
				{
					return false;
				}
			}
		}
		return true;
	}
//-- methods --//
}
?>
