<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/ical_import.aw,v 1.4 2007/12/06 14:32:55 kristo Exp $
// ical_import.aw - Sündmuste import (iCal) 
/*

@classinfo syslog_type=ST_ICAL_IMPORT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=robert

@default table=objects
@default group=general

	@property import_tb type=toolbar submit=no no_caption=1

	@property name type=textbox table=objects
	@caption Nimi

	@property calendar type=relpicker reltype=RELTYPE_CALENDAR field=meta method=serialize store=connect
	@caption Kalender

	@property vevent_type type=select field=meta method=serialize
	@caption VEVENT t&uuml;&uuml;p aw-s

	@property url type=textbox field=meta method=serialize
	@caption Impordi url

	@property file type=fileupload store=no
	@caption Kalendrifail

@reltype CALENDAR value=1 clid=CL_PLANNER
@caption Kalender
*/

class ical_import extends class_base
{
	const AW_CLID = 1349;

	function ical_import()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/ical_import",
			"clid" => CL_ICAL_IMPORT
		));
	}
	
	function _get_import_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "submit",
			"img" => "save.gif",
			"tooltip" => "Impordi URL-ist",
			"url" => $this->mk_my_orb("import",array(
				"id" => $arr["obj_inst"]->id(),
				"ru" => $arr["request"]["post_ru"]
			))
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "vevent_type":
				$prop["options"] = array(
					CL_CRM_MEETING => "Kohtumine",
					CL_CRM_CALL => "Kõne",
					CL_TASK => "Toimetus",
					CL_CALENDAR_EVENT => "Kalendrisündmus"
				);
				if(!$prop["value"])
				{
					$prop["value"] = CL_CRM_MEETING;
				}
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
			case "file":
				$file = $_FILES['file'];
				if($file["tmp_name"])
				{
					$args = array(
						"id" => $arr["obj_inst"]->id(),
						"filename" => $file["tmp_name"],
						"ru" => $arr["request"]["post_ru"]
					);
					$this->import($args);
				}
				break;
		}
		return $retval;
	}	

	/**
	@attrib name=import all_args=1
	**/
	function import($arr)
	{
		if(is_oid($arr["id"]))
		{
			$obj = obj($arr["id"]);
			$cal = obj($obj->prop("calendar"));
			if($cal)
			{
				require_once(aw_ini_get("basedir").'/addons/ical/iCalcreator.aw');
				$c = new vcalendar();
				if($arr["filename"])
				{
					$filename = $arr["filename"];
				}
				elseif($url = $obj->prop("url"))
				{
					$filename = $url;
				}
				if($filename)
				{
					$c->parse($filename);
					$ef = $cal->get_first_obj_by_reltype("RELTYPE_EVENT_FOLDER");
					while($e = $c->getComponent())
					{
						$this->create_event($e, $ef, $obj);
					}
				}
			}
		}
		if($arr["ru"])
		{
			$ru = $arr["ru"];
		}
		else
		{
			$ru = $this->mk_my_orb("change", array("id" => $arr["id"]));
		}
		return $ru;
	}

	function create_event($c, $ef, $obj)
	{
		$type = get_class($c);
		switch($type)
		{
			case "vtodo":
				$e = new object();
				$e->set_class_id(CL_TASK);
				$e->set_parent($ef);
				$d = $c->getProperty("due");
				if($d["tz"] == "Z")
				{
					$add = 3;
				}
				$deadline = mktime($d["hour"]+$add, $d["min"], $d["sec"], $d["month"], $d["day"], $d["year"]);
				$e->set_prop("deadline",$deadline);
				$e->set_prop("end", $deadline);
				break;
			case "vevent":
				$vt = $obj->prop("vevent_type");
				if(!$vt)
				{
					$vt = CL_CRM_MEETING;
				}
				$e = new object();
				$e->set_class_id($vt);
				$e->set_parent($ef);
				$d = $c->getProperty("dtend");
				if($d["tz"] == "Z")
				{
					$add = 3;
				}
				$end= mktime($d["hour"]+$add, $d["min"], $d["sec"], $d["month"], $d["day"], $d["year"]);
				$e->set_prop("end", $end);
				break;
			default:
				return 0;
		}
		$d = $c->getProperty("dtstart");
		if($d["tz"] == "Z")
		{
			$add = 3;
		}
		$start = mktime($d["hour"]+$add, $d["min"], $d["sec"], $d["month"], $d["day"], $d["year"]);
		if($start<1)
		{
			if($deadline)
			{
				$start = $deadline;
			}
			elseif($end)
			{
				$start = $end;
			}
		}
		$e->set_prop("start1", $start);
		$name = $c->getProperty("summary");
		$name = iconv("UTF-8",aw_global_get("charset"), $name);
		if(!strlen($name))
		{
			$name = "Ülesanne";
		}
		$existlist = new object_list(array(
			"name" => $name,
			"class_id" => array(CL_CRM_MEETING, CL_TASK, CL_CRM_CALL, CL_CALENDAR_EVENT),
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $ef->id(),
			"start1" => $start
		));
		foreach($existlist->ids() as $oid)
		{
			$exists = $oid;
		}
		if($exists)
		{
			return 0;
		}
		$e->set_name($name);
		$comment = $c->getProperty("description");
		$comment = iconv("UTF-8",aw_global_get("charset"), $comment);
		$e->set_comment($comment);
		$e->save();
		$attendees = $c->attendee;
		if(count($attendees) && $e->class_id()!=CL_CALENDAR_EVENT)
		{
			foreach($attendees as $at)
			{
				$value = $at["value"];
				$params = $at["params"];
				$name = $params["CN"];
				if($name)
				{
					$persons = new object_list(array(
						"class_id" => CL_CRM_PERSON,
						"name" => $name,
						"lang_id" => array(),
						"site_id" => array()
					));
					foreach($persons->ids() as $person)
					{
						$p_exists = obj($person);
					}
					if($p_exists)
					{
						$person = $p_exists;
					}
					else
					{
						$cur = get_current_company();
						$person = new object();
						$person->set_class_id(CL_CRM_PERSON);
						$person->set_name($name);
						$person->set_parent($cur->id());
						$person->save();
					}
					$inst = get_instance($e->class_id());
					$inst->add_participant($e, $person);
				}
			}
		}
	}

	function callback_post_save($arr)
	{
		$obj = $arr["obj_inst"];
		if(!$obj->prop("calendar"))
		{
			$conn = $obj->connections_to(array(
				"type" => "RELTYPE_ICAL_IMPORT",
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

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
}
?>
