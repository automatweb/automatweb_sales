<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/vcl/multi_calendar.aw,v 1.6 2007/12/06 14:33:00 kristo Exp $
/*
@classinfo maintainer=kristo
*/
class multi_calendar extends aw_template
{
	function multi_calendar()
	{
		$this->init("calendar/vcl");
	}


	function init_vcl_property($arr)
	{
		$rv = array();
		$prop = $arr["prop"];

		// mul on vaja teada neid _teisi_ kalendreid, et saaks otsida asju, mida vaja
		// need on selle kalendri küljes, mille seest ma seda sündmust vaatan

		// mis loogiliselt võttes viib sellele, et see otsing peaks asuma kalendri
		// küljes ja mitte sündmuse klassis. Aga ma testin seda asja siin

		$search_calendars = aw_global_get($prop["name"] . "_calendars");
		$search_duration = aw_global_get($prop["name"] . "_duration");
		$search_from = aw_global_get($prop["name"] . "_from");
		$search_to = aw_global_get($prop["name"] . "_to");

		//aw_session_del($prop["name"] . "_calendars");
		//aw_session_del($prop["name"] . "_duration");

		$pl = get_instance(CL_PLANNER);
		$cal_id = $pl->get_calendar_for_user();

		if (!is_oid($cal_id))
		{
			// siin peaks mingi vea tagastama tegelikult
			return false;
		};

		$cal_obj = new object($cal_id);
		$other_conns = $cal_obj->connections_from(array(
			"type" => "RELTYPE_OTHER_CALENDAR",
		));

		load_vcl("date_edit");
		$search_from_tm = date_edit::get_timestamp($search_from);
		$search_to_tm = date_edit::get_timestamp($search_to);

		$options = array();

		foreach($other_conns as $conn)
		{
			$options[$conn->prop("to")] = $conn->prop("to.name");
		};

		$name = $prop["name"] . "_calendars";
		$rv[$name] = array(
			"name" => $name,
			"caption" => t("Kalendrid, millest otsida"),
			"type" => "chooser",
			"orient" => "vertical",
			"multiple" => 1,
			"options" => $options,
			"edit_links" => 1,
			"value" => $search_calendars,
		);
		
		// set default search range
		if (!$search_from)
		{
			$search_from = time();
		};
		
		if (!$search_to)
		{
			$search_to = $search_from + 7 * 86400;
		};

		$name = $prop["name"] . "_from";

		$rv[$name] = array(
			"name" => $name,
			"caption" => t("Alates"),
			"type" => "date_select",
			"value" => $search_from,
		);
		
		$name = $prop["name"] . "_to";

		$rv[$name] = array(
			"name" => $name,
			"caption" => t("Kuni"),
			"type" => "date_select",
			"value" => $search_to,
		);


		$name = $prop["name"] . "_duration";
		if (empty($search_duration))
		{
			$search_duration = array("hour" => 2,"minute" => 0);
		};
		$rv[$name] = array(
			"name" => $name,
			"caption" => t("Otsitava aja pikkus (hh:mm)"),
			"type" => "time_select",
			"value" => $search_duration,
			"minute_step" => 15,
		);
		
		$name = $prop["name"] . "_sbt";
		$rv[$name] = array(
			"name" => $name,
			"caption" => t("Otsi"),
			"type" => "submit",
		);

		// search only of there were any calendars chosen
		if (sizeof($search_calendars) > 0)
		{
			$ol = new object_list(array(
				"class_id" => CL_PLANNER,
				"oid" => $search_calendars,
				"lang_id" => array(),
				"site_id" => array(),
			));

			$min_day_start = 0;
			$max_day_end = (23*3600) + 59;

			$parents = array();
			foreach($ol->arr() as $o)
			{
				$event_folders = $pl->get_event_folders(array("id" => $o->id()));

				$parents = array_merge($parents,$event_folders);
				$day_start = $o->prop("day_start");
				$ds = $day_start["hour"] * 3600 + $day_start["minute"];
				$day_end = $o->prop("day_end");
				$de = $day_end["hour"] * 3600 + $day_end["minute"];
				if ($ds > $min_day_start)
				{
					$min_day_start = $ds;
				};
				if ($de < $max_day_end)
				{
					$max_day_end = $de;
				};
			};

			if (sizeof($parents) > 0)
			{
				// and of course only if we have any valid event folders
				$parents = array_merge($parents,$pl->get_event_folders(array("id" => $cal_obj->id())));

				// start from midnight today
				list($d,$m,$y) = explode("-",date("d-m-Y"));

				$start_tm = $search_from_tm;
				$end_tm = $search_to_tm;

				// and look ahead for the next 7 days
				//$start_tm = date("U",mktime(0,0,0,$m,$d,$y));
				//$end_tm = time() + (7 * 86400);

				$hour = $search_duration["hour"];
				$minute = $search_duration["minute"];

				//list($hour,$minute) = explode(":",$search_duration);

				$diff = ($hour * 3600) + ($minute * 60);
				$slices = array();

				// create a list of all possible time slices
				// 3600 is the interval for finding vacancies
				for ($i = $start_tm; $i <= $end_tm; $i = $i + 3600)
				{
					// but exclude those outside of the day time range
					$lim = date("H",$i) * 3600 + date("i",$i);
					$slice_end = $i + ($diff);
					$lim_end = date("H",$slice_end) * 3600 + date("i",$slice_end);
					$slicen_end -= 1;
					// $lim_end can overflow to the next day, so deal with that too
					if ($lim >= $min_day_start && $lim_end <= $max_day_end && $lim_end > $lim)
					{
						$slices[$i] = $slice_end;
					};
				};


				$ol_args = array(
					"parent" => $parents,
					"sort_by" => "planner.start",
					"class_id" => array(CL_CRM_MEETING,CL_TASK,CL_CRM_CALL,CL_CALENDAR_EVENT),
					"CL_CALENDAR_EVENT.start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $start_tm, $end_tm),
				);

				$ol = new object_list($ol_args);

				// delete slices that have events in them
				foreach($ol->arr() as $event)
				{
					$e_start = $event->prop("start1");
					$e_end = $event->prop("end");
					foreach($slices as $skey => $sval)
					{
						if (between($skey,$e_start,$e_end))
						{
							unset($slices[$skey]);
						};

						if (between($sval,$e_start,$e_end))
						{
							unset($slices[$sval]);
						};
					};

				};

				// now we should have a list of available time slices, which we return

				$opts = array();

				/*
				load_vcl("table");
				$this->t = new aw_table(array("layout" => "generic"));

				$this->t->define_field(array(
					"name" => "calendar",
					"caption" => t("Kalender"),
				));

				$this->t->define_field(array(
					"name" => "from",
					"caption" => t("Alates"),
				));

				$this->t->define_field(array(
					"name" => "to",
					"caption" => t("Kuni"),
				));

				$this->t->define_chooser(array(
					"field" => "from",
					"caption" => t("XX"),
				));
				*/

				foreach($slices as $skey => $sval)
				{
					$m = aw_locale::get_lc_date($skey,5);
					$w = strtoupper(aw_locale::get_lc_weekday(date("w",$skey),true));
					$opts[$skey] = "${w}, ${m}" . date(" H:i",$skey) . " - " . date("H:i",$sval);
					/*
					$this->t->define_data(array(
						"from" => date("d-m-Y H:i",$skey),
						"to" => date("d-m-Y H:i",$sval),
					));
					*/
				};

				$rname = $prop["name"] . "_select_date";

				$rv[$rname] = array(
					"name" => $rname,
					"type" => "chooser",
					"orient" => "vertical",
					"options" => $opts,
					"caption" => t("Leitud ajad"),
				);
		
				$name = $prop["name"] . "_sbt_confirm";
				$rv[$name] = array(
					"name" => $name,
					"caption" => t("Kinnita"),
					"type" => "submit",
				);


				/*
				$rv["table"] = array(
					"name" => "table",
					"type" => "table",
					"vcl_inst" => &$this->t,
				);
				*/
			};

		};

		return $rv;
	}

	function process_vcl_property($arr)
	{
		$name = $arr["prop"]["name"];

		$calendars = $arr["request"]["${name}_calendars"];
		$duration = $arr["request"]["{$name}_duration"];
		$selected_date = $arr["request"]["{$name}_select_date"];
		$search_from = $arr["request"]["{$name}_from"];
		$search_to = $arr["request"]["{$name}_to"];

		if (sizeof($calendars) > 0)
		{
			if ($selected_date)
			{
				$event_obj = $arr["obj_inst"];
				foreach($calendars as $calendar)
				{
					$cal_obj = new object($calendar);
					$parent = $cal_obj->prop("event_folder");
					$event_brother = $event_obj->create_brother($parent);
					$event_bo = new object($event_brother);
					$event_bo->set_prop("start1",$selected_date);
					$event_bo->save();
				};
				$event_obj->set_prop("start1",$selected_date);
				$event_obj->save();


			};

		}

		// XXX: that sucks.
		aw_session_set("${name}_calendars",$calendars);
		aw_session_set("${name}_duration",$duration);
		aw_session_set("${name}_from",$search_from);
		aw_session_set("${name}_to",$search_to);
			
	}


};
?>
