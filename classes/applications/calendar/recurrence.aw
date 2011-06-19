<?php
// recurrence.aw - Kordus
/*

@classinfo syslog_type=ST_RECURRENCE relationmgr=yes no_status=1

@default table=objects
@default group=general

// form=+emb syntax means, that this thing should be in all the default forms +
// the emb form. The latter I can then use for embedding cases
@property start type=date_select table=calendar2recurrence form=+emb
@caption Alates

@property time type=textbox size=5 field=meta method=serialize form=+emb
@caption Kellaaeg (tund:minut)

@property length type=textbox size=5 field=meta method=serialize form=+emb
@caption Pikkus (h)

@property recur_type type=select field=meta method=serialize form=+emb
@caption Korduse t&uuml;&uuml;p

@property interval_minutely type=textbox size=2 field=meta method=serialize form=+emb
@caption Iga X minuti j&auml;rel

@property interval_hourly type=textbox size=2 field=meta method=serialize form=+emb
@caption Iga X tunni j&auml;rel

@property interval_daily type=textbox size=2 field=meta method=serialize form=+emb
@caption Iga X p&auml;eva j&auml;rel

@property interval_weekly type=textbox size=2 field=meta method=serialize form=+emb
@caption Iga X n&auml;dala j&auml;rel

@property interval_monthly type=textbox size=2 field=meta method=serialize form=+emb
@caption Iga X kuu j&auml;rel

@property interval_yearly type=textbox size=2 field=meta method=serialize form=+emb
@caption Iga X aasta j&auml;rel

@property weekdays type=chooser multiple=1 field=meta method=serialize form=+emb
@caption Nendel p&auml;evadel

@property month_days type=textbox field=meta method=serialize form=+emb
@caption Kindlatel p&auml;evadel

@property month_rel_weekdays type=chooser multiple=1 field=meta method=serialize form=+emb
@caption Valitud n&auml;dalap&auml;evadel

@property month_weekdays type=chooser multiple=1 field=meta method=serialize form=+emb
@caption N&auml;dalap&auml;evad

// l6ppu per-se ei ole. Kuigi selle v6ib m22rata. Igal juhul on see optional
@property end type=date_select table=calendar2recurrence form=+emb
@caption Kuni


@tableinfo calendar2recurrence index=obj_id master_table=objects master_index=brother_of

// the reason I need a separate table for saving recurrence information is search.
// Searching events should not read in all the existing events and then do some math
// on those. It needs a way to gather events in the requested range only.

*/
define("RECUR_DAILY",1);
define("RECUR_WEEKLY",2);
define("RECUR_MONTHLY",3);
define("RECUR_YEARLY",4);
define("RECUR_HOURLY",5);
define("RECUR_MINUTELY",6);

class recurrence extends class_base
{
	const RECUR_DAILY = 1;
	const RECUR_WEEKLY = 2;
	const RECUR_MONTHLY = 3;
	const RECUR_YEARLY = 4;
	const RECUR_HOURLY = 5;
	const RECUR_MINUTELY = 6;

	function recurrence()
	{
		$this->init(array(
			"clid" => CL_RECURRENCE
		));
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = class_base::PROP_OK;
		$filtered = array("interval_hourly","interval_minutely","interval_daily","weekdays","interval_weekly","interval_monthly","interval_yearly","month_weekdays","month_rel_weekdays", "month_days");
		$prop_filter = array(
			RECUR_MINUTELY => array("interval_minutely"),
			RECUR_HOURLY => array("interval_hourly"),
			RECUR_DAILY => array("interval_daily"),
			RECUR_WEEKLY => array("weekdays","interval_weekly"),
			RECUR_MONTHLY => array("interval_monthly","month_weekdays","month_rel_weekdays","month_days"),
			RECUR_YEARLY => array("interval_yearly"),
		);
		$type = false;
		if (is_object($arr["obj_inst"]))
		{
			$type = $arr["obj_inst"]->prop("recur_type");
		};
		if (empty($type))
		{
			$type = RECUR_DAILY;
		};
		$cur_filter = $prop_filter[$type];
		if (in_array($data["name"],$filtered) && !in_array($data["name"],$cur_filter))
		{
			return class_base::PROP_IGNORE;
		}

		switch($data["name"])
		{
			case "weekdays":
				// php date functions give sunday an index of 0,
				// so I'm doing the same
				$data["options"] = array(
					"1" => "E",
					"2" => "T",
					"3" => "K",
					"4" => "N",
					"5" => "R",
					"6" => "L",
					"0" => "P"
				);
				break;

			case "month_weekdays":
				// php date functions give sunday an index of 0,
				// so I'm doing the same
				$data["options"] = array(
					"1" => "E",
					"2" => "T",
					"3" => "K",
					"4" => "N",
					"5" => "R",
					"6" => "L",
					"0" => "P"
				);
				break;

			case "month_rel_weekdays":
				$data["options"] = array(
					"1" => t("esimesel"),
					"2" => t("teisel"),
					"3" => t("kolmandal"),
					"4" => t("neljandal"),
					"-1" => t("viimasel")
				);
				break;

			case "recur_type":
				$data["options"] = array(
					RECUR_DAILY => t("p&auml;ev"),
					RECUR_WEEKLY => t("n&auml;dal"),
					RECUR_MONTHLY => t("kuu"),
					RECUR_YEARLY => t("aasta"),
					RECUR_MINUTELY => t("minut"),
					RECUR_HOURLY => t("tund"),
				);
				break;

			case "length":
				$data["value"] = isset($data["value"]) ? abs (aw_math_calc::string2float($data["value"])) : 0;
				break;
		}

		return $retval;
	}

	function calc_range2($arr)
	{
		$wd = $arr["weekdays"];
		// bail out, if no weekdays are specified
		if (empty($wd))
		{
			return false;
		};
		$rv = "";
		for ($i = $arr["start"]; $i <= $arr["end"]; $i = $i + 86400)
		{
			$w = date("w",$i);
			// only show days with matches
			if ($wd[$w])
			{
				//$rv .= date("d.m.Y",$i);
				$rv .= date("r",$i);
				$rv .= "<br>";
			};
		};
		return $rv;
	}

	function calc_range_weekly($arr)
	{
		$wd = $arr["weekdays"];
		// bail out, if no weekdays are specified
		if (empty($wd))
		{
			return false;
		};
		// Need to calculate the time shift from the start of the day
		// 3:10 should become 3 * 3600 + 10 * 60

		$start_hour = $arr["start_hour"];
		$start_min = $arr["start_min"];

		$end_hour = $arr["end_hour"];
		$end_min = $arr["end_min"];

		$interval = (int)$arr["interval"];
		if ($interval == 0)
		{
			$interval = 1;
		};

		$rv = array();
		// can I calculate the start day, end day and then go from there instead?
		$int_interval = 0;
		for ($i = $arr["start"]; $i <= $arr["end"]; $i = $i + 86400)
		{
			$int_interval++;
			if (0 != ($int_interval % $interval))
			{
				continue;
			};
			$w = date("w",$i);
			if (!$wd[$w])
			{
				continue;
			};
			list($d,$m,$y) = explode("-",date("d-m-Y",$i));
			$day_start = mktime(0,0,0,$m,$d,$y);
			$evt_start = $day_start + (3600 * $start_hour) + (60 * $start_min);
			$evt_end = $day_start + (3600 * $end_hour) + (60 * $end_min);
			$rv[$evt_start] = $evt_end;
		};
		return $rv;
	}

	function calc_range_daily($arr)
	{
		// Need to calculate the time shift from the start of the day
		// 3:10 should become 3 * 3600 + 10 * 60

		$interval = (int)$arr["interval"];
		if ($interval == 0)
		{
			$interval = 1;
		};

		$start_hour = $arr["start_hour"];
		$start_min = $arr["start_min"];

		$end_hour = $arr["end_hour"];
		$end_min = $arr["end_min"];

		$rv = array();
		// can I calculate the start day, end day and then go from there instead?
		for ($i = $arr["start"]; $i <= $arr["end"]; $i = $i + ($interval * 86400))
		{
			list($d,$m,$y) = explode("-",date("d-m-Y",$i));
			$day_start = mktime(0,0,0,$m,$d,$y);
			$evt_start = $day_start + (3600 * $start_hour) + (60 * $start_min);
			$evt_end = $day_start + (3600 * $end_hour) + (60 * $end_min);
			$rv[$evt_start] = $evt_end;
		};
		return $rv;
	}

	function calc_range_minutely($arr)
	{
		$interval = (int)$arr["interval"];
		if ($interval == 0)
		{
			$interval = 1;
		};

		$rv = array();
		list($sd,$sm,$sy) = explode("-",date("d-m-Y",$arr["start"]));
		list($ed,$em,$ey) = explode("-",date("d-m-Y",$arr["end"]));
		$range_start = mktime(0,0,0,$sm,$sd,$sy);
		$range_end = mktime(23,59,59,$em,$ed,$ey);
		for ($i = $range_start; $i <= $range_end; $i = $i + ($interval * 60))
		{
			$rv[$i] = $i;

		};
		return $rv;
	}

	function calc_range_hourly($arr)
	{
		$interval = (int)$arr["interval"];
		if ($interval == 0)
		{
			$interval = 1;
		};

		$rv = array();
		list($sd,$sm,$sy) = explode("-",date("d-m-Y",$arr["start"]));
		list($ed,$em,$ey) = explode("-",date("d-m-Y",$arr["end"]));
		$range_start = mktime(0,0,0,$sm,$sd,$sy);
		$range_end = mktime(23,59,59,$em,$ed,$ey);
		for ($i = $range_start; $i <= $range_end; $i = $i + ($interval * 3600))
		{
			//print date("d.m.Y H:i",$i);
			//print "<br>";
			$rv[$i] = $i;
		}

		return $rv;
	}

	function calc_range_yearly($arr)
	{
		$interval = (int)$arr["interval"];
		if ($interval == 0)
		{
			$interval = 1;
		};

		$start_hour = $arr["start_hour"];
		$start_min = $arr["start_min"];

		$end_hour = $arr["end_hour"];
		$end_min = $arr["end_min"];

		$start_year = date("Y",$arr["start"]);
		$end_year = date("Y",$arr["end"]);


		$rv = array();

		list($d,$m) = explode("-",date("d-m",$arr["start"]));

		for ($i = $start_year; $i <= $end_year; $i = $i + $interval)
		{
			$day_start = mktime(0,0,0,$m,$d,$i);
			$evt_start = $day_start + (3600 * $start_hour) + (60 * $start_min);
			$evt_end = $day_start + (3600 * $end_hour) + (60 * $end_min);
			$rv[$evt_start] = $evt_end;
		};

		return $rv;
	}

	////
	// !Sets a name for the object if one is not specified (embed forms)
	function callback_pre_save($arr)
	{
		$name = $arr["obj_inst"]->name();
		if (empty($name))
		{
			$new_name = date("Y/m/d",$arr["obj_inst"]->prop("start"));
			$new_name .= " - ";
			$new_name .= date("Y/m/d",$arr["obj_inst"]->prop("end"));
			$arr["obj_inst"]->set_name($new_name);
		};
	}

	////
	// !Update recurrence information - calculate timestamps after the object is saved
	function callback_post_save($arr)
	{
		$this->delete_recurrence(array(
			"id" => $arr["obj_inst"]->id(),
		));

		// now I have to somehow figure out the object id that connects to this
		// recurrence
		$conns = $arr["obj_inst"]->connections_to(array());

		if (sizeof($conns) > 0)
		{
			// retrieving only the first connection is intentional!!!
			$first = reset($conns);
			$src_obj = $first->from();
			$start = $src_obj->prop("start1");
			$end = $src_obj->prop("end");
		};

		$rx = array();

		// those come from the recurrence object
		$recur_type = $arr["obj_inst"]->prop("recur_type");
		$recur_start = $arr["obj_inst"]->prop("start");
		$recur_end = $arr["obj_inst"]->prop("end");
		$recur_time = $arr["obj_inst"]->prop("time");

		$recur_start_hour = date("G",$start);
		$recur_start_min = date("i",$start);

		$recur_end_hour = date("G",$end);
		$recur_end_min = date("i",$end);

		// if there is a valid time entered for recurrence then use that instead of the one
		// defined by the event
		if (strpos($recur_time,":") !== false)
		{
			list($recur_time_hour,$recur_time_min) = explode(":",$recur_time);
			if (is_numeric($recur_time_hour) && is_numeric($recur_time_min))
			{
				$recur_start_hour = $recur_time_hour;
				$recur_start_min = $recur_time_min;

				// if end time is added later, then implement processing here
				$recur_end_hour = $recur_start_hour;
				$recur_end_min = $recur_start_min;
			};
		};

		$range_data = array(
			"event_start" => $start,
			"start_hour" => $recur_start_hour,
			"start_min" => $recur_start_min,
			"event_end" => $end,
			"end_hour" => $recur_end_hour,
			"end_min" => $recur_end_min,
			"start" => $recur_start,
			"end" => $recur_end,
		);

		if (RECUR_WEEKLY == $recur_type)
		{
			$range_data["weekdays"] = $arr["obj_inst"]->prop("weekdays");
			$range_data["interval"] = $arr["obj_inst"]->prop("interval_weekly");
			$rx = $this->calc_range_weekly($range_data);
		}
		elseif (RECUR_DAILY == $recur_type)
		{
			$range_data["interval"] = $arr["obj_inst"]->prop("interval_daily");
			$rx = $this->calc_range_daily($range_data);
		}
		elseif (RECUR_YEARLY == $recur_type)
		{
			$range_data["interval"] = $arr["obj_inst"]->prop("interval_yearly");
			$rx = $this->calc_range_yearly($range_data);
		}
		elseif (RECUR_HOURLY == $recur_type)
		{
			$range_data["interval"] = $arr["obj_inst"]->prop("interval_hourly");
			$rx = $this->calc_range_hourly($range_data);

		}
		elseif (RECUR_MINUTELY == $recur_type)
		{
			$range_data["interval"] = $arr["obj_inst"]->prop("interval_minutely");
			$rx = $this->calc_range_minutely($range_data);
		};

		if (is_array($rx) && sizeof($rx) > 0)
		{
			$this->create_recurrence(array(
				"id" => $arr["obj_inst"]->id(),
				"start" => $recur_start,
				"end" => $recur_end,
				"tm_list" => $rx,
			));
		};
	}

	function delete_recurrence($arr)
	{
		// recurrence table contains information about a single recurrence.
		$q = "DELETE FROM recurrence WHERE recur_id = '$arr[id]'";
		$this->db_query($q);
	}

	function create_recurrence($arr)
	{
		extract($arr);
		if (!is_array($tm_list) || sizeof($tm_list) == 0)
		{
			return false;
		};
		$parts = array();
		foreach($tm_list as $recur_start => $recur_end)
		{
			// let's make it 1 hour for starters
			//$recur_end = $recur_start + 3600;
			$parts[] = "($id,$recur_start,$recur_end)";

		};
		// that is needless duplication of data. I need to store start and end dates elsewhere!
		// and I need to able to create some shortcuts for events like .. every day ones.
		// there really is no need to write those into the table
		$sql = "INSERT INTO recurrence (recur_id,recur_start,recur_end) VALUES " . join(",",$parts);
		$this->db_query($sql);
	}


	/*
		This binds object table to recurrence table
		mysql> describe calendar2recurrence;
		+--------+---------------------+------+-----+---------+-------+
		| Field  | Type                | Null | Key | Default | Extra |
		+--------+---------------------+------+-----+---------+-------+
		| obj_id | bigint(20) unsigned |      | PRI | 0       |       |
		| start  | bigint(20) unsigned |      |     | 0       |       |
		| end    | bigint(20) unsigned |      |     | 0       |       |
		+--------+---------------------+------+-----+---------+-------+

		This contains information about every single recurrence out there
		mysql> describe recurrence;
		+-------------+---------------------+------+-----+---------+-------+
		| Field       | Type                | Null | Key | Default | Extra |
		+-------------+---------------------+------+-----+---------+-------+
		| recur_id    | bigint(20) unsigned |      | MUL | 0       |       |
		| recur_start | bigint(20) unsigned |      |     | 0       |       |
		| recur_end   | bigint(20) unsigned |      |     | 0       |       |
		+-------------+---------------------+------+-----+---------+-------+


	*/

	/** returns timestamp for the next event for a specified recurrence object

		@attrib api=1

		@param id required
		@param time optional

	**/
	function get_next_event($arr)
	{
		$time = (!empty($arr["time"])) ? $arr["time"] : time();
		$o = new object($arr["id"]);
		$q = "SELECT recur_start FROM recurrence WHERE recur_id = ". $o->id() . " AND recur_start >= " . $time . " ORDER BY recur_start LIMIT 1";
		$this->db_query($q);
		$row = $this->db_next();
		return is_array($row) ? $row["recur_start"] : false;

	}

	/** returns array of events in the specified time range

		@attrib api=1

		@param id required
		@param start required
		@param end required

	**/
	function get_event_range($arr)
	{
		$q = "SELECT recur_start, recur_end FROM recurrence WHERE recur_id = ". $arr["id"] . " AND recur_start >= " . $arr["start"] . " AND recur_start <= ".$arr["end"]." ORDER BY recur_start";
		$this->db_query($q);
		$ret = array();
		while($row = $this->db_next())
		{
			$ret[$row["recur_start"]] = $row;
		}
		return $ret;
	}

// /* Now that I have to basic functionality working pretty much fine, I need to figure out a
// a way to create that "clone" button.

// 1 - create a connections to the original? Or create a connection from the original to the
// recurring event?
// */
// /*
// If I'm viewing an event then I can display the prev/next links
// */
}
