<?php

// calendar.aw - VCL calendar

class vcalendar extends aw_template
{
	var $container_template = "container.tpl"; // access?
	var $cal_tpl_dir = "calendar"; // access?
	var $output_initialized = false; // access?
	public /*TODO: scope?*/ $full_weeks;
	public /*TODO: scope?*/ $month_week;
	public/*TODO: scope?*/ $adm_day;
	public/*TODO: scope?*/ $overview_func;
	public/*TODO: scope?*/ $filt_views;
	public/*TODO: scope?*/ $target_section;
	public/*TODO: scope?*/ $random;
	public/*TODO: scope?*/ $years;
	public/*TODO: scope?*/ $past;
	public/*TODO: scope?*/ $skip_empty;
	public/*TODO: scope?*/ $first_event;
	public/*TODO: scope?*/ $events_sorted;
	public/*TODO: scope?*/ $overview_items_oids;
	public /*TODO: scope?*/ $past_limit;
	public /*TODO: scope?*/ $last_event;
	public /*TODO: scope?*/ $future;
	public /*TODO: scope?*/ $future_limit;

	protected $items = array();
	protected $overview_items = array();
	protected $show_days_with_events = false;


	/**
		@attrib params=name api=1
		@param tpldir optional type=string
		Sets the alternate template directory
		@comment
		Initlializes template class
		@examples
		$vcal = new vcalendar(array(
			"tpldir" => "alternate_template_dir",
		));
	**/
	function vcalendar($arr = array())
	{
		// calendar_view class needs templates from its own directory
		if (isset($arr["tpldir"]))
		{
			$this->cal_tpl_dir = $arr["tpldir"];
		}

		$this->init(array(
			"tpldir" => $this->cal_tpl_dir,
		));

		lc_site_load("vcl_calendar", $this);
	}

	/**
		@attrib params=pos api=1
		@param feature required
		Features name to check [first_image|project_media].
		@comment
		Checks if current instance has desired feature
		@returns
		Boolean value depending on the features existance
	**/
	function has_feature($feature)
	{
		$retval = false;
		// call this after configure ..
		if (is_object($this->evt_tpl) && $feature === "first_image")
		{
			if ($this->evt_tpl->template_has_var("first_image"))
			{
				$retval = true;
			}
		}

		if (is_object($this->evt_tpl) && $feature === "project_media")
		{
			if ($this->evt_tpl->is_template("project_media"))
			{
				$retval = true;
			}
		}

		return $retval;
	}

	/**
		@attrib params=name api=1
		@param event_template optional type=string
		Sets event template to given one. There are 3 default templates, forweek and relative viewtpes + everything else.
		@comment
		Sets the vcalendars events template.
		@examples
		$vcal = new vcalendar();
		$vcal->init_output(array("event_template" => "my_new_templ.tpl"));
		print $vcal->get_html();
		// shows the calendar with new event template
	**/
	function init_output($arr)
	{
		$this->evt_tpl = new aw_template();
		$this->evt_tpl->tpl_init($this->cal_tpl_dir);
		if ($this->overview_func)
		{
			$o_inst = $this->overview_func[0];
			if(is_object($o_inst->obj_inst) and $o_inst->obj_inst->is_a(CL_CALENDAR_VIEW))
			{
				$sub_tpl = $o_inst->obj_inst->prop("sub_event_template");
			}
		}

		$tpl = $this->range["viewtype"] === "relative" ? "sub_event2.tpl" : (!empty($sub_tpl) ? $sub_tpl : "sub_event.tpl");
		$better_template = "";

		if (!empty($arr["event_template"]))
		{
			$tpl = $arr["event_template"];
		}
		elseif ($this->range["viewtype"] === "week")
		{
			$better_template = "week_event.tpl";
		}

		$got_it = false;
		if ($better_template)
		{
			$got_it = $this->evt_tpl->read_template($better_template,1);
		}

		if (!$got_it)
		{
			$this->evt_tpl->read_template($tpl);
		}

		lc_site_load("vcl_calendar", $this->evt_tpl);
		$this->output_initialized = true;
	}

	// whats for is that here ??(taiu)
	function init_calendar($arr)
	{
		// this is the place where I need to calculate the range
	}

	/**
		@attrib params=name api=1

		@param tasklist_func optional type=array
		array contents:
		array(class_inst, method_name);
		Where 'method_name' is the methods name in class instance 'class_inst' which returns tasklist.

		@param overview_func optional type=array
		array(class_inst, method_name)
		Where 'method_name' is the methods name in class instance 'class_inst' which returns overview events. Method is used to define the precence of the quick navigator.

		@param overview_range optional type=int
		If overview function is set, sets time period for overview. Can be 1(shows one month) or 3(shows prev and next month also). Default is 3.

		@param container_template optional type=string
		Overwrites container template filename. Default is set during initsialization.

		@param show_days_with_events optional type=int
		if set to 1, week and month show only the days with events in it.

		@param skip_empty optional type=int
		If set to 1, week view shows only days with events in it.
		By default isn't set.

		@param full_weeks optional type=int
		If set to 1, whole week is shown (including weekend:))
		By default isn't set.

		@param target_section optional type=string
		Adds an url parameter named section valued with 'target_section' into overview mini-calendars day link.

		@param day_start optional type=array
		array(start_hour, start_minute), where both start_hour && start_minute are integers.
		Defines start of a day.

		@param day_end optional type=array
		array(end_hour, end_minute), where both end_hour && end_minute are integers.
		Defines end of a day.

		@param filt_views optional type=array
		array(viewtype[,viewtype]...)
		Filters out viewtypes that don't exist in 'filt_views' array. Options are[today|day|week|month|relative]. If not set, all viewtypes will be displayed

		@param fix_links optional type=int
		If set to 1 and $this->event_sources has any events in it(set from somewhere outside world) does something:). Used in bonbon site, shows only events in a specific way.

		@param month_week optional type=int
		If set to 1, shows month viewtype like week's viewtype, just weeks below eachother.
		By default not set.

		@comment
		Configures the calendar view in all possible and non-possible ways.

		@examples
		$cal = get_instance("vcl/calendar");
		$cal = new vcalendar();
		// initialize calendar
		$conf = array(
			"overview_func" => array(&$this, "_get_overview"),
			// sets overview function, if that function returns zip(nothing).. then just draws a calendar overview.
			// If function returns events, then these are displayed on that minicalendar
			"overview_range" => 3,
			// sets the overiview calendar to show 3 months (prev, active, next)
			"show_days_with_events" => 1,
			// main calendar shows only the days which have events in int.
			"full_weeks" => 1,
			// main calendar shows saturday and sunday also
			"month_week" => 1,
			// the month is shown as series of weeks below eachother
		);
		$cal->add_item(array(
			"item_start" => time(),
			"data"=>array(
				"comment" => "commentaarium",
				"utextarea1" => "blahh",
			)
		));
		$cal->get_range();
		// calculates the calendar timeperiod (viewtype is set to month by default)
		$cal->configure($conf);
		// configure items are set
		print $cal->get_html();
		// echoes a calendar with month viewtype(shown as weeks below eachother), shows only one day(has event in it) and even if it's a weekend day.
		// overview mini-calendar shows previous and next month also(in a regular month viewtype).

	**/
	function configure($arr = array())
	{
		$attribs = array("tasklist_func", "overview_func", "overview_range", "container_template", "show_days_with_events", "skip_empty", "full_weeks", "target_section", "day_start", "day_end", "filt_views", "fix_links", "month_week");
		foreach($attribs as $attrib)
		{
			if (!empty($arr[$attrib]))
			{
				$this->$attrib = $arr[$attrib];
			};
		};
	}

	/**
		@attrib params=name api=1
		@param viewtype optional type=string
		Sets the timeperiod to be shown.Options are [today|day|week|month|relative].
		By default month is set.
		Doesn't check if set to viewtype which was filtered out by configure methods filt_views param (forces selected viewtype, even it was filtered out).
		@param date optional type=int
		@comment
		Calculates the dates between based on the viewtype.
		Default is current time.
		@returns
		Array in format : array(start,end,start_wd,m,y,wd,prev,next,timestamp,viewtype,overview_start,overview_end);
		@examples
		$cal = get_instance("vcl/calendar");
		$cal = new vcalendar();
		$range = $cal->get_range(array("viewtype"=>"month");
		// $range contains calculated timeperiod. From current monday to sunday.
	**/
	function get_range($arr = array())
	{
		// called from get_property to determine the range of events to be shown
		$viewtype = !empty($arr["viewtype"]) ? $arr["viewtype"] : "month";
		classload("core/date/date_calc");
		$range_args = array(
			"type" => $viewtype,
		);

		if (empty($arr["date"]))
		{
			$range_args["time"] = time();
		}
		else
		{
			$range_args["date"] = $arr["date"];
		}

		if (empty($this->overview_range))
		{
			$this->overview_range = 3;
		}
		// this should also return overview range. no?
		// depending on amount of months we have in the overview.
		// yees. For starters, let's assume that we have 3 of them

		$this->past_limit = 15;
		$this->future_limit = 5;
		$range = get_date_range($range_args);
		$m = date("m",$range["timestamp"]);
		$y = date("Y",$range["timestamp"]);
		$range["viewtype"] = $viewtype;

		if (!empty($arr["limit_events"]))
		{
			$range["limit_events"] = $arr["limit_events"];
		}

		if ($this->overview_range == 3)
		{
			// start of the previous month
			$range["overview_start"] = mktime(0,0,0,$m-1,1,$y);
			// end of the next month
			$range["overview_end"] = mktime(23,59,59,$m+2,0,$y);
		}
		elseif ($this->overview_range == 1)
		{
			// start of the this month
			$range["overview_start"] = mktime(0,0,0,$m,1,$y);
			// end of the this month
			$range["overview_end"] = mktime(23,59,59,$m+1,0,$y);
		}

		if ($viewtype === "relative")
		{
			$range["past"] = $this->past_limit;
			$range["future"] = $this->future_limit;
			$range["end"] += 86400 * 60;
		}

		$this->el_count = 0;
		$this->evt_list = array();
		if (!empty($arr["show_ec"]))
		{
			$range = $arr["show_ec"] + $range;
		}
		$this->range = $range;
		return $range;
	}

	// I need methods for adding item AND for drawing
	// timestamp
	// data - arr
	/**
		@attrib params=name api=1
		@param item_start optional type=int
			Event start time(unix timestamp). Default is current time
		@param item_end optional type=int
			Event end time(unix timestamp).
		@param bgcolor optional type=string
			Background colour for this event item
		@param data required type=array
			Data for the event, array elements:
			- id
			- comment (comment about event)
		@comment
			For adding events into calendar.
		@examples
			$vcal->add_item(array(
				"item_start" => time(),
				"data"=>array(
					"id" => integer
					"comment" => "commentaarium",
				)
			));
	**/
	function add_item($arr)
	{
		if (!empty($arr["item_start"]))
		{
			$arr["timestamp"] = $arr["item_start"];
			if (empty($arr["item_end"]))
			{
				$arr["item_end"] = $arr["item_start"];
			}
			// experimental support for multiday events
			if ($arr["item_start"] > $arr["item_end"])
			{
				// hehe, textbook problem .. swap variables
				// but I don't do that, since I'm afraid that there are too many
				// events where the start date is later than end. But maybe
				// I shouldn't care
				$arr["item_end"] = $arr["item_start"];
			}
		}

		if (empty($arr["timestamp"]))
		{
			$arr["timestamp"] = time();
//			throw new awex_vcl_calendar_time("Timestamp for item is required.");
		}

		// convert timestamp to day, since calendar is usually day based
		$use_date = date("Ymd", $arr["timestamp"]);
		$this->el_count++;
		$data = $arr["data"];
		$data["timestamp"] = $arr["timestamp"];
		$start_tm = $end_tm = 0;

		if (!empty($arr["item_start"]))
		{
			$data["item_start"] = $arr["item_start"];
			$start_tm = (int)($data["item_start"] / 86400);
		}

		if (!empty($arr["item_end"]))
		{
			$data["item_end"] = $arr["item_end"];
			$end_tm = (int)($data["item_end"] / 86400);
			list($ed,$em,$ey) = explode("/",date("d/m/Y",$arr["item_end"]));
		}

		$data["_id"] = $this->el_count;
		$data["id"] = isset($arr["data"]["id"]) ? $arr["data"]["id"] : "";
		$data["comment"] = isset($arr["data"]["comment"]) ? $arr["data"]["comment"] : "";
		$data["bgcolor"] = empty($arr["bgcolor"]) ? "" : $arr["bgcolor"];

		if ($end_tm > $start_tm)
		{
			$data["item_end"] = mktime($this->day_end["hour"],$this->day_end["minute"],59,$em,$ed,$ey);
			//$data["time"] = "Algab: " . date("H:i",$data["item_start"]);
		}

		$this->evt_list[$this->el_count] = $data;
		$this->items[$use_date][] = &$this->evt_list[$this->el_count];

		// deal with passed recurrence information

		// aga 22ki peaks seda yldse recurrence abil tegema? Sest kalendri komponent
		// ei tea ju midagi kalendrip2eva algusest ja l6pust. Ja ei saagi teada

		// actually, I can pass that information to the component through the configure method
		// so it is not really a problem
		if (isset($arr["recurrence"]) && is_array($arr["recurrence"]))
		{
			$this->recur_info[$this->el_count] = $arr["recurrence"];
			foreach($arr["recurrence"] as $tm)
			{
				$use_date = date("Ymd",$tm);
				$this->items[$use_date][] = &$this->evt_list[$this->el_count];
			};
		};

		// this will deal with multi-day events and will add events on other days
		// besides the first one
		if (isset($arr["item_end"]))
		{
			// this is a tricky one - we should not add a whole day to the start time,
			// because this will not make it work for events that start one day at 18:00 and end the next day
			// at 16:00  - we must only make sure we are just behind 00:00
			$arr["item_start"] = get_day_start($arr["item_start"]) + 86400;
			//$arr["item_end"]+= 85399;
			//$arr["item_start"] += 86400;
			// okey .. first day needs to end at the specified time
			// second (and all the remainders start at specified time)
			$days_between = $end_tm - $start_tm;
			$day_counter = 0;
			// XXX: process only the required range to save memory
			for ($i = $arr["item_start"]; $i <= $arr["item_end"]; $i = $i + 86400)
			{
				$day_counter++;
				$use_date = date("Ymd",$i);
				// but .. I do not need to use those references .. yees?
				$tmp = $this->evt_list[$this->el_count];
				// aga siin on mul vaja teada p2eva algust
				$tmp["item_start"] = mktime($this->day_start["hour"],$this->day_start["minute"],0,1,1,2005);
				//$tmp["item_start"] = mktime(0,0,0,1,1,2005);

				// siin tuleb kuidagi kindlaks teha .. et kui tegemist on viimase
				// p2evaga, siis n2itame ka 6iget kellaaega
				if ($day_counter == $days_between)
				{
					//$tmp["time"] = "L&otilde;peb: " . date("H:i",$arr["item_end"]);
					$tmp["item_end"] =  $arr["item_end"];
				}

				$tmp["hide_times"] = 1;

				if (!empty($arr["bgcolor"]))
				{
					$tmp["bgcolor"] = $arr["bgcolor"];
				}

				$this->items[$use_date][] = $tmp;
			}
		}
		// this is used for relational view
		//if ($data["timestamp"] < $this->range["timestamp"])
		if ($data["timestamp"] < time())
		{
			$this->past[] = &$this->items[$use_date][sizeof($this->items[$use_date])-1];
		}
		else
		{
			$this->future[] = &$this->items[$use_date][sizeof($this->items[$use_date])-1];
		};
	}

	/**
		@attrib params=name api=1
		@param timestamp required type=int
		Unix timestamp for the event.
		@param url optional type=string
		Special url for that event.
		@comment
		Adds an overview item to specified time.
		@examples
		$vcal->add_overview_item(array(
			"timestamp"=>time()+86400,
			"url"=> "http://www.neti.ee",
		));
		// Adds an event to overview mini-calendar's next day which links to Estonians most popular search engine.
	**/
	function add_overview_item($arr)
	{
		$use_date = date("Ymd",$arr["timestamp"]);
		$this->overview_items[$use_date] = true;
		$this->overview_items_oids[$use_date][$arr['event_oid']] = $arr['event_oid'];
		if (!empty($arr["url"]))
		{
			$this->overview_urls[$use_date] = $arr["url"];
		};
	}

	/**
		@attrib params=name api=1
		@param style optional type=array
		array elements(passed by name):
			-minical_day_with_events (by default 'minical_cellact')
			-minical_day_without_events (by default 'minical_cell')
			-minical_day_active (by default 'minical_cellselected')
			-minical_day_deactive (by default 'minical_cell_deact')
			-minical_day_today (by default 'minical_cell_today')
			-minical_title (by default 'minical_table')
			-minical_background (by default 'minical_table')

			All these can have common values(styles):

			[minical_cell] : usual cell with no events - day_without_events
			[minical_cellact] : usual cell with events - day_with_events
			[minical_cellselected] : selected (active) cell - day_active
			[minical_cell_today] : day_today
			[minical_cell_deact] : deactiv (outside teh current range) - day_deactive
			[minical_table] : minical_title's and minical_background's default style

		@param text optional type=string
		An already generated calendar content from draw_[month|day|MY_OWN_FUNC etc...] function

		@comment
		Finalizes the calendar and returns html source
		@returns
		HTML source of the calendar
		@examples
		print $cal->get_html(array(
			"style" => array(
				"minical_day_with_events" => "minical_cell",
				// sets days's with events style like it hasn't any event.
				"minical_background" => "minical_cell_today",
				// sets overview calendars background to one of the day's style.
				// Actually that style wasn't ment for that, but it produces pretty nice green calendar(check out yourself:)).
			)
		));
		// finalises and prints the calendar to the page

		$cal = get_instance("vcl/calendar");
		$cal = new vcalendar();
		print $cal->get_html(array(
			"text" => $cal->MY_OWN_VIEW_FUNC(); // can be calendars own draw_month() etc..
		));
		// finalizes and prints the calendar to the page with specified view-type function content.

	**/
	function get_html($arr = array())
	{
		$fo = $lo = false;
		$this->aliasmgr = get_instance("alias_parser");
		$this->styles = array();

		if (isset($arr["style"]) and is_array($arr["style"]))
		{
			$this->styles = $arr["style"];
		}

		classload("core/date/date_calc");
		if (!is_array($this->range))
		{
			$this->range = get_date_range(array(
				"time" => time(),
				"viewtype" => "day",
			));
			$this->range["viewtype"] = "day";
		}

		if (!$this->output_initialized)
		{
			$this->init_output($arr);
		}

		$this->event_counter = 0;

		// turns out a lot of the time the calendar has no var for content and drawing it
		// can take a SIGNIFICANT amount of time
		$this->read_template($this->container_template);
		if ($this->template_has_var_full("content"))
		{
			if (!empty($arr["text"]))
			{
				$content = $arr["text"];
			}
			else
			{
				switch($this->range["viewtype"])
				{
					case "month":
						$content = $this->draw_month();
						$caption = aw_locale::get_lc_month(date("m",$this->range["timestamp"]));
						$caption .= " ";
						$caption .= date("Y",$this->range["timestamp"]);
						break;

					case "week":
					case "last_events":
						$content = $this->draw_week();
						$ms = aw_locale::get_lc_month(date("m",$this->range["start"]));
						$me = aw_locale::get_lc_month(date("m",$this->range["end"]));
						$caption = date("j. ",$this->range["start"]) . "$ms - " . date("j. ",$this->range["end"]) . " " . $me;
						break;

					case "relative":
						$content = $this->draw_relative();
						$caption = date("j. ",$this->range["timestamp"]) . aw_locale::get_lc_month(date("m",$this->range["timestamp"]));
						break;

					case "year":
						$content = $this->draw_year();
						$caption = "";
						break;

					default:
						$content = $this->draw_day($arr);
						$caption = date("j. ",$this->range["timestamp"]) . aw_locale::get_lc_month(date("m",$this->range["timestamp"])) . date(" Y",$this->range["timestamp"]);
				}
			}
		}
		else
		{
			switch($this->range["viewtype"])
			{
				case "month":
					$content = $this->draw_month();
					$caption = aw_locale::get_lc_month(date("m",$this->range["timestamp"]));
					$caption .= " ";
					$caption .= date("Y",$this->range["timestamp"]);
					break;

				case "week":
				case "last_events":
					$content = $this->draw_week();
					$ms = aw_locale::get_lc_month(date("m",$this->range["start"]));
					$me = aw_locale::get_lc_month(date("m",$this->range["end"]));
					$caption = date("j. ",$this->range["start"]) . "$ms - " . date("j. ",$this->range["end"]) . " " . $me;
					break;

				case "relative":
					$content = $this->draw_relative();
					$caption = date("j. ",$this->range["timestamp"]) . aw_locale::get_lc_month(date("m",$this->range["timestamp"]));
					break;

				case "year":
					$content = $this->draw_year();
					$caption = "";
					break;

				default:
					$content = $this->draw_day($arr);
					$caption = date("j. ",$this->range["timestamp"]) . aw_locale::get_lc_month(date("m",$this->range["timestamp"])) . date(" Y",$this->range["timestamp"]);
			}
		}

		classload("core/date/date_calc");
		$m = date("m",$this->range["timestamp"]);
		$y = date("Y",$this->range["timestamp"]);

		$mn = "";
		// this one draws overview months... teheheheeee
		// if overview_func is not defined, then no overview thingie will be drawn
		// it's that easy.
		$this->read_template($this->container_template);
		if ($this->overview_func && $this->template_has_var_full("overview"))
		{
			$ostart = $this->range["overview_start"];
			$oend = $this->range["overview_end"];

			$inst = $this->overview_func[0];
			$meth = $this->overview_func[1];
			if (method_exists($inst,$meth))
			{
				$overview_items = $inst->$meth(array(
					"start" => $ostart,
					"end" => $oend,
				));
			};


			if (is_array($overview_items))
			{
				foreach($overview_items as $tm => $tmp)
				{
					if (is_array($tmp))
					{
						$this->add_overview_item(array(
							"timestamp" => $tmp["timestamp"],
							"url" => isset($tmp["url"]) ? $tmp["url"] : null,
							"event_oid" => $tmp["event_oid"]
						));
					}
					else
					{
						$this->add_overview_item(array(
							"timestamp" => $tm,
							"event_oid" => $tmp["event_oid"]
						));
					}
				}
			}


			// I need to figure out how many months should be shown.
			// actually, it should be set from the configure item

			// I also need to figure out which days to show, so that
			// holidays will get be excluded
			if ($this->overview_range == 3)
			{
				$ri =  -1;
				$re = 1;
			}
			else
			{
				$ri = 0;
				$re = 0;
			};

			for ($i = $ri; $i <= $re; $i++)
			{
				$range = get_date_range(array(
					"date" => sprintf("%d-%d-%d",1,$m+$i,$y),
					"type" => "month",
				));
				$mn .= $this->draw_s_month($range);
			};
		}

		$this->read_template($this->container_template);
		$types = array(
			"today" => t("T&auml;na"),
			"day" => t("P&auml;ev"),
			"week" => t("N&auml;dal"),
			"month" => t("Kuu"),
			"relative" => t("&Uuml;levaade"),
		);
		$ts = "";
		if(count($this->filt_views) > 0)
		{
			$funcs = $this->filt_views;
		}
		else
		{
			$funcs = array_keys($types);
		}

		foreach($types as $type => $name)
		{
			if(!in_array($type, $funcs))
			{
				continue;
			}
			$link = aw_url_change_var("viewtype",$type);
			$this->vars(array(
				"link" => aw_url_change_var("viewtype",$type),
				"today_url" => aw_url_change_var(array("viewtype" => "day", "date" => date("d-m-Y"))),
				"text" => $name,
			));
			if($type == "today")
			{
				$ts .= $this->parse("TODAY");
			}
			else
			{
				$ts .= $this->parse(($type == $this->range["viewtype"]) ? "SEL_PAGE" : "PAGE");
			}
		}


		$tasks = array();
		if (isset($this->tasklist_func))
		{
			$inst = $this->tasklist_func[0];
			$meth = $this->tasklist_func[1];
			if (method_exists($inst,$meth))
			{
				$tasks = $inst->$meth();
			}
		}

		$tstr = "";
		foreach($tasks as $task)
		{
			$this->vars(array(
				"task_url" => $task["url"],
				"task_name" => parse_obj_name($task["name"]),
			));
			$tstr .= $this->parse("TASK");
		}

		if (!empty($tstr))
		{
			$this->vars(array(
				"TASK" => $tstr,
				"tasks_title" => t("Toimetused"),
			));

			$this->vars(array(
				"TASKS" => $this->parse("TASKS"),
			));
		}

		for ($i = 1; $i <= 12; $i++)
		{
			$mnames[$i] = aw_locale::get_lc_month($i);
		}
		$yto = date("Y") + 8;
		$yfr = date("Y") - 12;

		for ($i = 2003; $i <= $yto; $i++)
		{
			$years[$i] = $i;
			foreach($mnames as $monyear_key => $monyear_val)
			{
				$monyears[$monyear_key."-".$i] = $monyear_val." ".$i;
			}
		}

		// I'm trying to get the javascript function inside the template to generate
		// a correct url
		$urlsufix = strpos(aw_global_get("REQUEST_URI"),"?") === false ? "?" : "";
		$prevlink = aw_url_change_var(array(
			"date" => $this->range["prev"],
			"section" => $this->target_section,
		));

		$nextlink = aw_url_change_var(array(
			"date" => $this->range["next"],
			"section" => $this->target_section,
		));

		$prev = $next = "";

		if($this->show_days_with_events && !empty($this->event_sources) && isset($this->fix_links) && $this->fix_links == 1)
		{
			if(!empty($this->first_event))
			{
				$f = obj($this->first_event["id"]);
				$objs = new object_list(array(
					"parent" => $this->event_sources,
					"class_id" => $this->event_entry_classes,
					"start1" => new obj_predicate_compare(OBJ_COMP_LESS, $this->first_event["start1"]),
					//"brother_of" => new obj_predicate_prop("id"),
					"oid" => new obj_predicate_not($f->brother_of()),
					"status" => $this->obj_status,
					"limit" => $this->limit_events ? $this->limit_events : 1,
					"sort_by" => "planner.start DESC",
					"site_id" => array(),
				));
				if($this->range["viewtype"] == "relative")
				{
					if($obj = $objs->begin())
					{
						$fo = true;
					}
				}
				elseif($obj = $objs->last())
				{
					$fo = true;
				}
				if($fo)
				{
					$this->vars(array(
						"prevlink" => aw_url_change_var(array(
							"date" => date("d-m-Y", $obj->prop("start1")),
							"section" => $this->target_section,
						))
					));
					$prev = $this->parse("PREV");
				}
			}

			if(!empty($this->last_event))
			{
				$l = obj($this->last_event["id"]);
				$objs = new object_list(array(
					"parent" => $this->event_sources,
					"class_id" => $this->event_entry_classes,
					"start1" => new obj_predicate_compare(OBJ_COMP_GREATER, $this->last_event["start1"]),
					"oid" => new obj_predicate_not($l->brother_of()),
					"status" => $this->obj_status,
					"limit" => $this->limit_events ? $this->limit_events : 1,
					"sort_by" => "planner.start ASC",
					"site_id" => array(),
				));

				if($this->range["viewtype"] == "relative")
				{
					if($obj = $objs->last())
					{
						$lo = true;
					}
				}
				elseif($obj = $objs->begin())
				{
					$lo = true;
				}

				if($lo)
				{
					$this->vars(array(
						"nextlink" => aw_url_change_var(array(
							"date" => date("d-m-Y", $obj->prop("start1")),
							"section" => $this->target_section,
						)),
					));
					$next = $this->parse("NEXT");
				}
			}
		}

		if ($this->template_has_var("prevweek_link"))
		{
			$weekrange = get_date_range(array(
				"type" => "week",
				"time" => $this->range["timestamp"],
			));

			$this->vars(array(
				"prevweek_link" => aw_url_change_var(array(
					"viewtype" => "week",
					"date" => $weekrange["prev"],
					"target_section" => $this->target_section,
				)),
				"nextweek_link" => aw_url_change_var(array(
					"viewtype" => "week",
					"date" => $weekrange["next"],
					"target_section" => $this->target_section,
				)),
			));
		}

		$this->vars_safe(array(
			"RANDOM" => $this->random,
			"YEARS" => $this->years,
			"PAGE" => $ts,
			"PREV" => $prev,
			"NEXT" => $next,
			"mininaviurl" => aw_url_change_var("date","") . $urlsufix,
			"naviurl" => aw_url_change_var("date",""),
			"mnames" => aw_template::picker((int)$m,$mnames),
			"years" => aw_template::picker($y,$years),
			"monyears" => aw_template::picker((int)$m."-".$y, $monyears),
			"content" => $content,
			"caption" => $caption,
			"prevlink" => $prevlink,
			"nextlink" => $nextlink,
			"overview" => $mn,
			"today_url" => aw_url_change_var(array("viewtype" => "day","date" => date("d-m-Y"))),
			"today_date" => date("d.m.Y"),
			"act_day_tm" => $this->range["timestamp"],
		));

		$n = isset($inst) && is_object($inst) && is_object($inst->obj_inst) && $inst->obj_inst->is_a(CL_CALENDAR_VIEW) && $inst->obj_inst->prop("monyear_2in1") ? "1" : "2";
		$this->vars_safe(array(
			"MINIMONYEAR_2IN".$n => $this->parse("MINIMONYEAR_2IN".$n),
			"MINIMONYEAR_2IN".$n."_JS" => $this->parse("MINIMONYEAR_2IN".$n."_JS"),
		));

		$rv = $this->parse();
		return $rv;
	}

	////
	// !How to I make overview work. It's longer than the usual span ..
	// so I need some way to do define additonal items for the navigator.
	function draw_month()
	{
		$tpl = "month_view.tpl";
		if($this->month_week == 1)
		{
			$tpl = "month_week_view.tpl";
		}
		$this->read_template($tpl);
		$rv = $header = $w = "";

		for ($i = 1; $i <= 7; $i++)
		{
			if ($i >= 6 && !$this->full_weeks)
			{
				continue;
			}
			$dn = aw_locale::get_lc_weekday($i,true);
			$this->vars(array(
				"dayname" => $dn,
			));
			$header .= $this->parse("HEADER_CELL");
		}

		$this->vars(array(
			"HEADER_CELL" => $header,
		));

		$header = $this->parse("HEADER");


		// the idea here is that drawing of month always starts from
		// the first day of the week in which the month starts and ends
		// on the last day of the week in which the month ends
		$realstart = ($this->range["start"] - ($this->range["start_wd"] - 1) * 86400);
		$realend = ($this->range["end"] + (7 - $this->range["end_wd"]) * 86400);

		$now = date("Ymd");

		$calendar_blocks = array();
		$s_parts = unpack("a4year/a2mon/a2day",date("Ymd",$realstart));
		for ($j = $realstart; $j <= $realend; $j = $j + (7*86400))
		{
			for ($i = $j; $i <= $j + (7*86400)-1; $i = $i + 86400)
			{
				$reals = mktime(0,0,0,$s_parts["mon"],$s_parts["day"],$s_parts["year"]);
				$s_parts["day"]++;

				$dstamp = date("Ymd",$reals);
				$events_for_day = "";
				$wn = date("w",$reals);
				if ($wn == 0)
				{
					$wn = 7;
				}

				if (!$this->full_weeks && $wn > 5)
				{
					continue;
				}

				// uh, but we parse day by day. How do I deal with recurring information?
				if (isset($this->items[$dstamp]) and count($this->items[$dstamp]) > 0)
				{
					$events = $this->items[$dstamp];
					uasort($events,array($this,"__asc_sort"));
					if(!$this->first_event)
					{
						$this->first_event = reset($events);

					}
					foreach($events as $event)
					{
						$sday = $this->draw_event($event);
						$events_for_day .= $sday;
					}
				}
				$calendar_blocks[date("Ymd",$reals)] = $events_for_day;
			}
		}

		$last = 0;

		$s_parts = unpack("a4year/a2mon/a2day",date("Ymd",$realstart));
		for ($j = $realstart; $j < $realend; $j = $j + (7*86400))
		{
			$sz = 1;
			for ($i = $j; $i <= $j + (7*86400)-1; $i = $i + 86400)
			{
				$reals = mktime(0,0,0,$s_parts["mon"],$s_parts["day"],$s_parts["year"]);
				$s_parts["day"]++;

				$dstamp = date("Ymd",$reals);
				$events_for_day = "";
				$wn = date("w",$reals);
				if ($wn == 0)
				{
					$wn = 7;
				}

				if($i == $j)
				{
					$d1 = date("j", $reals);
					$mon1 = aw_locale::get_lc_month(date("m", $reals));
				}
				elseif($sz == $wn)
				{
					$d2 = date("j", $reals);
					$mon2 = aw_locale::get_lc_month(date("m", $reals));
				}
				$sz++;
				if (!$this->full_weeks && $wn > 5 && !$this->month_week)
				{
					continue;
				}

				$block_id = date("Ymd",$reals);

				// this will most most likely break templates with grids, but works
				// fine with event lists .. which is the way it should be used anyway
				if (empty($calendar_blocks[$block_id]) && $this->show_days_with_events)
				{
					continue;
				}

				$this->vars_safe(array(
					"EVENT" => $calendar_blocks[$block_id],
					"daynum" => date("j",$reals),
					"monthnum" => date("m",$reals),
					"dayname" => date("F d, Y",$reals),
					"date" => aw_locale::get_lc_date($reals,5),
					"lc_weekday" => aw_locale::get_lc_weekday(date("w",$reals)),
					"lc_month" => aw_locale::get_lc_month(date("m",$reals)),
					"y_num" => date("Y", $reals),
					"m_num" => date("m", $reals),
					"d_num" => date("d", $reals),
					"daylink" => aw_url_change_var(array(
						"viewtype" => "day",
						"date" => date("d-m-Y",$reals),
					)),
				));
				$tpl = $dstamp == $now ? "TODAY" : "DAY";
				$rv .= $this->parse($tpl);
				$last = $dstamp;
			};
			if($mon1 == $mon2)
			{
				$mon1 = "";
			}
			$this->vars_safe(array(
				"DAY" => $rv,
				"monthvar" => "{$d1}. {$mon1} - {$d2}. {$mon2}",
			));
			$rv = "";
			$w .= $this->parse("WEEK");
		}

		$this->last_event = isset($event) ? $event :  null;

		$month_opts = array();
		$cd = empty($_GET["date"]) ? date("d-m-Y") : $_GET["date"];
		list($c_d, $c_m, $c_y) = explode("-", $cd);
		$u = aw_ini_get("baseurl").aw_global_get("REQUEST_URI");
		for($i = 1; $i < 13; $i++)
		{
			$nd = $c_d."-".sprintf("%02d", $i)."-".$c_y;
			$month_opts[aw_url_change_var("date", $nd, $u)] = aw_locale::get_lc_month($i);
		}
		for($i = $c_y-5; $i < $c_y+5; $i++)
		{
			$nd = $c_d."-".$c_m."-".sprintf("%04d", $i);
			$year_opts[aw_url_change_var("date", $nd, $u)] = $i;
		}
		$this->vars_safe(array(
			"HEADER" => $header,
			"WEEK" => $w,
			"month_name" => aw_locale::get_lc_month($this->range["m"]),
			"year" => $this->range["y"],
			"month_options" => $this->picker(get_ru(), $month_opts),
			"year_options" => $this->picker(get_ru(), $year_opts),
		));

		$rv =  $this->parse();
		return $rv;
	}

	function draw_year()
	{
		$this->read_template("year.tpl");
		//$rv = "";

		$header = "";
		$rv = "";

		$this->vars(array(
			"year" => $this->range["y"],
		));

		//dbg::p5($this->range);
		list($d,$m,$y) = explode("-",date("d-m-Y",$this->range["timestamp"]));

		for ($i = 1; $i <= 12; $i++)
		{
			$this->vars(array(
				"month_name" => aw_locale::get_lc_month($i),
			));
			$header = $this->parse("HEADER");
			$footer = $this->parse("FOOTER");
			// nyyd on mul vaja iga kuu kohta algust ja l6ppu

			$ms = mktime(0,0,0,$i,1,$y);
			$me = mktime(23,59,59,$i+1,0,$y);

			$et = "";

			$ev_count = 0;

			for ($j = $ms; $j <= $me; $j = $j + 86400)
			{
				$dstamp = date("Ymd",$j);

				$events_for_day = "";
				if (is_array($this->items[$dstamp]))
				{
					$events = $this->items[$dstamp];
					uasort($events,array($this,"__asc_sort"));
					if(!$this->first_event)
					{
						$this->first_event = reset($events);
					}

					foreach($events as $event)
					{
						$sday = $this->draw_event($event);
						$events_for_day .= $sday;
						$ev_count++;
					}
				}

				$et .= $events_for_day;
			}

			// XX: add optional skip_empty argument
			if (!$this->skip_empty || $ev_count > 0)
			{
				$this->vars_safe(array(
					"DAY" => $this->parse("DAY"),
				));
				$this->vars_safe(array(
					"WEEK" => $this->parse("WEEK"),
				));
				$this->vars_safe(array(
					"HEADER" => $header,
					"EVENT" => $et,
					"FOOTER" => $footer,
				));

				$rv .= $this->parse("MONTH");
			}
		}

		$this->last_event = $event;
		$this->vars_safe(array(
			"MONTH" => $rv,
		));

		return $this->parse();
	}

	function draw_week()
	{
		$rv = "";

		if ($this->overview_func)
		{
			$o_inst = $this->overview_func[0];
			if(is_object($o_inst->obj_inst) and $o_inst->obj_inst->is_a(CL_CALENDAR_VIEW))
			{
				$sub_tpl = $o_inst->obj_inst->prop("week_event_template");
			}
		}
		$tpl = !empty($sub_tpl) ? $sub_tpl : "week_view.tpl";

		$this->read_template($tpl);
		$now = date("Ymd");
		if ($this->skip_empty)
		{
			$this->show_days_with_events = true;
		}

		$s_parts = unpack("a4year/a2mon/a2day",date("Ymd",$this->range["start"]));

		// alright, aga see saast siis ju eeldab et mul on
		if($this->adm_day)
		{
			$dcheck = $this->parse("DCHECK");
		}

		for ($i = $this->range["start"]; $i <= $this->range["end"]; $i = $i + 86400)
		{
			// XXX: relative view joonistab draw_weeki, but shouldnt
			$reals = mktime(0,0,0,$s_parts["mon"],$s_parts["day"],$s_parts["year"]);
			$s_parts["day"]++;

			// kuidas kurat ma siis kompenseerin seda daylight saving timet?
			$dstamp = date("Ymd",$reals);
			$events_for_day = "";
			if (isset($this->items[$dstamp]) and is_array($this->items[$dstamp]))
			{
				$this->day = date("d-m-Y",$reals);
				$events = $this->items[$dstamp];
				if(is_object($this->overview_func[0]) && is_object($this->overview_func[0]->obj_inst) &&  $this->overview_func[0]->obj_inst->prop("sort_day_events_before"))
				{
					uasort($events, array($this, '__sort_events_starting_today_first'));
				}
				else
				{
					uasort($events,array($this,"__asc_sort"));
				}

				if(!$this->first_event)
				{
					$this->first_event = reset($events);
				}

				foreach($events as $event)
				{
					$events_for_day .= $this->draw_event($event);
				}
			}
			elseif ($this->show_days_with_events)
			{
				continue;
			}

			$wn = date("w",$reals);
			$dt = date("d",$reals);
			$mn = aw_locale::get_lc_month(date("m",$reals));
			$mn2 = $mn . " " . date("H:i",$reals);


			$this->vars_safe(array(
				"DCHECK" => isset($dcheck) ? $dcheck : "",
				"EVENT" => $events_for_day,
				"monthnum" => date("m",$reals),
				"daynum" => date("j",$reals),
				"monthnum" => date("m",$reals),
				"dayname" => date("F d, Y",$reals),
				"lc_weekday" => aw_locale::get_lc_weekday($wn,$reals),
				"lc_month" => $mn,
				"daylink" => aw_url_change_var(array("viewtype" => "day","date" => date("d-m-Y",$reals))),
				"date_and_time" => $dt . ". " . $mn2,
				"day_name" => aw_locale::get_lc_weekday($wn,true),
				"long_day_name" => aw_locale::get_lc_weekday($wn),
				"y_num" => date("Y", $reals),
                                "m_num" => date("m", $reals),
                                "d_num" => date("d", $reals),
				"date" => aw_locale::get_lc_date($reals,5),
			));
			$tpl = $dstamp == $now ? "TODAY" : "DAY";
			$rv .= $this->parse($tpl);
		}

		$this->last_event = isset($event) ? $event : null;
		$this->vars_safe(array(
			"DAY" => $rv,
		));
		return $this->parse();
	}

	function draw_day($arr = array())
	{
		$ct_template = !empty($arr["container_template"]) ? $arr["container_template"] : "day_view.tpl";
		$this->vars(array(
			"EVENT" => "",
		));
		$this->read_template($ct_template);
		$dcheck = "";
		if($this->adm_day)
		{
			$dcheck = $this->parse("DCHECK");
		}

		$dstamp = date("Ymd",$this->range["start"]);
		$events_for_day = "";

		if (isset($this->items[$dstamp]) and is_array($this->items[$dstamp]))
		{
			$events = $this->items[$dstamp];
			if(!$this->events_sorted)
			{
				if (aw_ini_get('vcalendar.sort_events_starting_today_first'))
				{
					uasort($events, array($this, '__sort_events_starting_today_first'));
				}
				else
				{
					uasort($events,array($this,"__asc_sort"));
				}
			}
			if(!$this->first_event)
			{
				$this->first_event = reset($events);
			}

			foreach($events as $event)
			{
				$events_for_day .= $this->draw_event($event);
			}
		}

		$this->last_event = isset($event) ? $event : null;

		$i = $this->range["start"];
		$dt = date("d",$i);
		$mn = aw_locale::get_lc_month(date("m",$i));
		$this->vars(array(
			"DCHECK" => $dcheck,
			"EVENT" => $events_for_day,
			"monthnum" => date("m", $this->range["start"]),
			"daynum" => date("j", $this->range["start"]),
			"monthnum" => date("m", $this->range["start"]),
			"dayname" => date("F d, Y",$this->range["start"]),
			"long_day_name" => aw_locale::get_lc_weekday($this->range["wd"]),
			"date" => aw_locale::get_lc_date($this->range["start"],5),
			"caption" => isset($arr["caption"]) ? $arr["caption"] : "",
			"lc_weekday" => aw_locale::get_lc_weekday(date("w",$this->range["start"])),
		));
		return $this->parse();
	}

	function draw_relative()
	{
		$this->read_template("relative_view.tpl");
		$this->vars(array(
			"last_text" => t("Viimased"),
			"next_text" => t("J&auml;rgmised")
		));
		$ok = false;

		$limit = $this->past_limit;
		$count = 0;

		// do the past
		$past = $future = "";
		if (is_array($this->past))
		{
			uasort($this->past,array($this,"__desc_sort"));
			if(!$this->last_event)
			{
				$this->last_event = reset($this->past);
			}
			foreach($this->past as $event)
			{
				$count++;
				$this->vars(array(
					"PT_EVENT" => $this->draw_event($event),
				));
				$past = $this->parse("PAST_EVENT") . $past;
				if ($count >= $limit)
				{
					break;
				}
			}
		}


		$this->first_event = $event;

		$limit = $this->future_limit;
		$count = 0;

		if (is_array($this->future))
		{
			uasort($this->future,array($this,"__asc_sort"));
			$this->last_event = end($this->future);
			foreach($this->future as $event)
			{
				$count++;
				$this->vars(array(
					"FT_EVENT" => $this->draw_event($event),
				));
				$future = $future . $this->parse("FUTURE_EVENT");

				if ($count >= $limit)
				{
					break;
				}
			}
		}


		$this->vars(array(
			"PAST_EVENT" => $past,
			"FUTURE_EVENT" => $future,
		));
		$past = $this->parse("PAST");
		$future = $this->parse("FUTURE");
		$this->vars(array(
			"PAST" => $past,
			"FUTURE" => $future,
			"PAST_LIMIT" => $this->past_limit,
			"FUTURE_LIMIT" => $this->future_limit,
		));
		return $this->parse();
	}

	////
	// XXX: this can and should be done without any templates. so it can be faster
	function draw_s_month($arr)
	{
		$this->read_template("minical.tpl");
		// the idea here is that drawing of month always starts from
		// the first day of the week in which the month starts and ends
		// on the last day of the week in which the month ends
		$realstart = ($arr["start"] - ($arr["start_wd"] - 1) * 86400);
		$realend = ($arr["end"] + (7 - $arr["end_wd"]) * 86400);

		$now = date("Ymd");

		$active_day = aw_global_get("date");
		if (empty($active_day))
		{
			$active_day = date("d-m-Y");
		};
		list($d,$m,$y) = explode("-",$active_day);
		if ($d && $m && !$y)
		{
			$y = $m;
			$m = $d;
			$d = 1;
		}
		else
		if (!$y)
		{
			list($d, $m, $y) = explode(".", $active_day);
		}

		// perhaps the date was in dd-mm-YYYY form?
		if (empty($y))
		{
			$y = $m;
			$m = $d;
			$d = 1;
		}
		$act_tm = mktime(0,0,0,$m,$d,$y);
		$act_stamp = date("Ymd",$act_tm);

		// modes
		//  0: day with events
		//  1: day with no events
		//  2: day outside the current range

		// styles
		// minical_cell - usual cell with no events  - day_without_events
		// minical_cellact - usual cell with events  - day_with_events
		// minical_cellselected - selected (active) cell - day_active
		// minical_cell_today - day_today
		// minical_cell_deact  - deactiv (outside teh current range) - day_deactive

		$style_day_with_events = "minical_cellact";
		if (isset($this->styles["minical_day_with_events"]))
		{
			$style_day_with_events = $this->styles["minical_day_with_events"];
		};

		$style_day_without_events = "minical_cell";
		if (isset($this->styles["minical_day_without_events"]))
		{
			$style_day_without_events = $this->styles["minical_day_without_events"];
		};

		$style_day_active = "minical_cellselected";
		if (isset($this->styles["minical_day_active"]))
		{
			$style_day_active = $this->styles["minical_day_active"];
		};

		$style_day_deactive = "minical_cell_deact";
		if (isset($this->styles["minical_day_deactive"]))
		{
			$style_day_deactive = $this->styles["minical_day_deactive"];
		}

		$style_day_today = "minical_cell_today";
		if (isset($this->styles["minical_day_today"]))
		{
			$style_day_today = $this->styles["minical_day_today"];
		};

		$style_title = "minical_table";
		if (isset($this->styles["minical_title"]))
		{
			$style_title = $this->styles["minical_title"];
		};

		$style_background = "minical_table";
		if (isset($this->styles["minical_background"]))
		{
			$style_background = $this->styles["minical_background"];
		}

		$week = "";
		$done_days = array();
		$j = $realstart;
		$s_parts = unpack("a4year/a2mon/a2day",date("Ymd",$realstart));
		while($j <= $realend)
		{
			$i = $j;
			$day = "";
			while($i <= $j + (7*86400)-1)
			{
				$reals = mktime(0,0,0,$s_parts["mon"],$s_parts["day"],$s_parts["year"]);
				$s_parts["day"]++;
				$dstamp = date("Ymd",$reals);
				if (isset($done_days[$dstamp]))
				{
					continue;
				}
				$done_days[$dstamp] = 1;
				$has_events = !empty($this->overview_items[$dstamp]);
				$style = $has_events ? $style_day_with_events : $style_day_without_events;
				if (between($i,$arr["start"],$arr["end"]))
				{
					$mode = 0;
					// if a day has no events and "cell_empty" sub is defined, use it.
					if (!$has_events)
					{
						$mode = 1;
					}

					if ($now == $dstamp)
					{
						$style = $style_day_today;
					}
					elseif ($act_stamp == $dstamp)
					{
						$style = $style_day_active;
					}
				}
				else
				{
					// cells outside the current range will always be drawn with
					// this subtemplate
					$mode = 2;
					$style = $style_day_deactive;
				}

				if (!empty($this->overview_urls[$dstamp]))
				{
					$day_url = $day_url_2 = $this->overview_urls[$dstamp];
				}
				else
				{
					$day_url = aw_ini_get('baseurl').aw_url_change_var(array(
						"viewtype" => "day",
						"event_id" => "",
						"evt_id" => "",
						"date" => date("d-m-Y",$reals),
						"section" => $this->target_section,
						// try to unset the sbt variable in url, so i can check in event_search
						// if it is there or not
						"sbt" => "",
					));
					//miskid n6medad sectionid annab kaasa muidu, m6ni tahab urli sellisena
					$day_url_2 = "/?viewtype=day&date=".date("d-m-Y",$reals)."&section=".$this->target_section;
				};

				// cell_empty has class, doesn't have a link, used to show days with no events
				// cell - has class, has link, used to show days with events
				// cell_deact - has a class, doesn't have a link, used to show days outside the current range

				// and that pretty much is it.

				// I set default styles in the container template and let them be overriden
				$caption = date("j",$reals);
				if($mode == 0)
				{
					$link = "<a href='$day_url'>$caption</a>";
					$link3 = "<a href='$day_url_2'>$caption</a>";
				}
				else
				{
					$link = $link3 = $caption;
				}

				$events_str = "";
				if (isset($this->overview_items_oids[$dstamp]) and is_array($this->overview_items_oids[$dstamp]))
				{
					foreach ($this->overview_items_oids[$dstamp] as $event_oid)
					{
						if ($this->can('view', $event_oid))
						{
							$event_obj = new object($event_oid);
							$this->vars_safe(array(
								'event_id' => $event_oid,
								'event_title' => $event_obj->name(),
								'event_content' => $event_obj->prop('content'),
								'event_comment' => $event_obj->comment()
							));
							$events_str .= $this->parse('EVENT');
						}
					}
				}
				$this->vars_safe(array(
					"today" => mktime(0, 0, 0, date("m"), date("d"), date("Y")) === $i ? 1 : 0,
					"hasevent" => strlen($events_str) > 0 ? 1 : 0,
					"weekend" => date("w", $i) % 6 === 0 ? 1 : 0,
					"style" => $style,
					"link" => $link,
					"link2" => $day_url,
					"link3" => $link3,
					"caption" => $caption,
					"event_title" => "",
					"event_content" => "",
					"event_comment" => "",
					"EVENT" => $events_str,
					"y_num" => date("Y", $i),
                                        "m_num" => date("m", $i),
                                        "d_num" => date("d", $i)

				));

				if($this->is_template("CLICKABLE") && $mode == 0)
				{
					$this->vars_safe(array(
						"link" => $this->parse("CLICKABLE"),
					));
				}
				$day .= $this->parse("DAY");
				$i = $i + 86400;
			};
			$rv = "";
			$this->vars_safe(array(
				"DAY" => $day,
			));
			$week .= $this->parse("WEEK");
			$j = $j + (7*86400);
		}

		// now, how to make those configurable?
		$this->vars_safe(array(
			"WEEK" => $week,
			"style_title" => $style_title,
			"style_background" => $style_background,
			"caption" => aw_locale::get_lc_month(date("m", $arr["timestamp"])) . " " . date("y",$arr["timestamp"]),
			"caption_url" => aw_ini_get('baseurl').aw_url_change_var(array(
				"viewtype" => "month",
				"date" => date("d-m-Y",$arr["timestamp"]),
				"section" => $this->target_section,
			)),
			"prev_date" => date("d-m-Y",mktime(0,0,0,$m-1,$d,$y)),
			"next_date" => date("d-m-Y",mktime(0,0,0,$m+1,$d,$y)),
			"section_id" => $this->target_section,
			"next_url" => aw_ini_get('baseurl').aw_url_change_var(array(
				"viewtype" => "month",
				"date" => date("d-m-Y",mktime(0,0,0,$m+1,$d,$y)),
				"section" => $this->target_section,
			)),
			"prev_url" => aw_ini_get('baseurl').aw_url_change_var(array(
				"viewtype" => "month",
				"date" => date("d-m-Y",mktime(0,0,0,$m-1,$d,$y)),
				"section" => $this->target_section,
			)),
			"month_name" => aw_locale::get_lc_month($m),
			"year" => $y,
		));
		return $this->parse();
	}

	function draw_event($evt)
	{
		$m = date("m", $evt["timestamp"]);
		$lc_month = aw_locale::get_lc_month($m);
		if (isset($evt["url"]))
		{
			$evt["link"] = $evt["url"];
		}

		$this->evt_tpl->vars(array(
			"parent_1_name" => "",
			"parent_2_name" => "",
			"parent_3_name" => "",
		));

		if(isset($evt["class_id"]) and $evt["class_id"] == CL_PARTY)
		{
			$fa = safe_array($evt["from_artist"]);
			$obj = obj($evt["id"]);
			$objs = new object_list(array(
				"brother_of" => $obj->brother_of(),
			));
			$proj = array();
			foreach($objs->arr() as $obz)
			{
				$obx = obj($obz->parent());
				if($obx->class_id() == CL_PROJECT)
				{
					$doc_id = $obx->prop("doc_id");
					$nm = $obx->name();
					if(is_oid($doc_id) && $this->can("view", $doc_id))
					{
						$nm = html::href(array(
							"caption" => $nm,
							"url" => obj_link($doc_id),
						));
					}
					$proj[] = $nm;
				}
			}
			$evt["project"] = implode(", ", $proj);
			$meta = $obj->meta("artists");
			unset($fa[0]);
			if(count($fa) > 0 && ($artist = $obj->get_first_obj_by_reltype("RELTYPE_ARTIST")))
			{
				if($fa["content"])
				{
					$evt["content"] = $artist->prop("notes");
				}

				if($fa["image"])
				{
					$evt["image"] = $artist->prop("picture");
				}
			}
			else
			{
				$art = array();
				foreach($artists = $obj->connections_from(array("type" => "RELTYPE_ARTIST")) as $artist)
				{
					$id = $artist->prop("to");
					$art[] = array(
						"id" => $id,
						"name" => $artist->prop("to.name"),
						"ord" => $meta["ord"][$id],
						"profession" => $meta["profession"][$id],
					);
				}
				uasort($art, array($this, "__sort_by_ord"));
				$xz = array();
				foreach($art as $a)
				{
					$x = html::href(array(
						"url" => obj_link($a["id"]),
						"caption" => $a["name"],
					));
					/*
					if(count($a["profession"]) > 0)
					{
						$profs = array();
						foreach($a["profession"] as $prof)
						{
							if(is_oid($prof) && $this->can("view", $prof))
							{
								$ob = obj($prof);
								$profs[] = $ob->name();
							}
						}
						$x .= " - ".implode(", ", $profs);
					}
					*/
					$xz[] = $x;
				}
				$evt["artist"] = implode(", ", $xz);
			}

			if($image = $obj->get_first_obj_by_reltype("RELTYPE_FLYER"))
			{
				$flyer_i = get_instance(CL_FLYER);
				$evt["image"] = $flyer_i->show($image);
				$evt['image_url'] = $flyer_i->image->get_url($image->prop('file1'));
			}
			$evt["content"] = nl2br($evt["content"]);
			$this->aliasmgr->parse_oo_aliases($evt["id"], $evt["content"]);
		}
		$this->evt_tpl->vars($evt);


		$dt = $mn = "";
		if (isset($evt["start1"]))
		{
			$dt = date("d", $evt["start1"]);
			$mn = aw_locale::get_lc_month(date("m", $evt["start1"]));
			$mn .= " " . date("H:i", $evt["start1"]);
		}

		if($this->adm_day)
		{
			$dchecked = $this->evt_tpl->parse("DCHECKED");
		}

		if (!isset($evt["time"]))
		{
			if (!empty($evt["item_start"]) && !empty($evt["item_end"]) && $evt["item_start"] != $evt["item_end"])
			{
				$tm_start = date("H:i",$evt["item_start"]);
				$tm_end = date("H:i",$evt["item_end"]);
				if ($tm_start == "00:00")
				{
					$time = "";
				}
				else if ($tm_end === "00:00")
				{
					$time = date("H:i",$evt["item_start"]);
				}
				else
				{
					$time = date("H:i",$evt["item_start"]) . " - " . date("H:i",$evt["item_end"]);
				}
			}
			else
			{
				$time = date("H:i",$evt["timestamp"]);
			}
		}
		else
		{
			$time = $evt["time"];
		}

		if (!empty($evt["hide_times"]) || $time === "00:00")
		{
			$time = "";
		}

		if (empty($evt["createdby"]) and empty($evt["created"]) and empty($evt["modifiedby"]) and empty($evt["modified"]))
		{
			$title = "";
		}
		else
		{
			$title = sprintf(t("Lisas [%s] %s /  Muutis [%s] %s"), $evt["createdby"], date("d.m.y", $evt["created"]), $evt["modifiedby"], date("d.m.y", $evt["modified"]));
		}

		$this->evt_tpl->vars(array(
			"title" => $title,
			'start_timestamp' => isset($evt['start']) ? $evt['start'] : "",
			'end_timestamp' => isset($evt['end']) ? $evt['end'] : "",
			"odd" => $this->event_counter % 2,
			"time" => $time,
			"date" => date("j-m-Y H:i",$evt["timestamp"]),
			"monthnum" => date("m",$evt["timestamp"]),
			"daynum" => date("j",$evt["timestamp"]),
			"datestamp" => date("d.m.Y",$evt["timestamp"]),
			"aw_date" => date("d-m-Y",$evt["timestamp"]),
			"lc_date" => date("j",$evt["timestamp"]) . ". " . $lc_month . " " . date("Y H:i",$evt["timestamp"]),
			"name" => htmlspecialchars($evt["name"]),
			"id" => isset($evt["id"]) ? $evt["id"] : "",
			"link" => !empty($evt["link"]) ? $evt["link"] : "javascript:void(0)",
			"calendar_view_link" => aw_url_change_var("event_id", $evt["id"]),
			"modifiedby" => isset($evt["modifiedby"]) ? $evt["modifiedby"] : "",
			"iconurl" => !empty($evt["icon"]) ? $evt["icon"] : "/automatweb/images/trans.gif",
			"COMMENT" => "",
			"DCHECKED" => isset($dchecked) ? $dchecked : "",
			"comment" => $evt["comment"],
			"bgcolor" => empty($evt["bgcolor"]) ? "" : "background-color:{$evt["bgcolor"]};",
			"day_name" => isset($evt["start1"]) ? strtoupper(substr(aw_locale::get_lc_weekday(date("w", $evt["start1"])),0,1)) : "",
			"date_and_time" => $dt . ". " . $mn,
			"section" => aw_global_get("section")
		));
		$this->event_counter++;
		
		if ($this->evt_tpl->is_template("PARTICIPANT"))
		{
			$PARTICIPANT = "";
			// FIXME: This (data retrieval) should prolly be done somewhere that $etv is built.
			foreach (obj($evt["id"])->get_participants()->names() as $participant_id => $participant_name)
			{
				$this->evt_tpl->vars(array(
					"participant.id" => $participant_id,
					"participant.name" => $participant_name,
				));
				$PARTICIPANT .= $this->evt_tpl->parse("PARTICIPANT");
			}
			
			$this->evt_tpl->vars_safe(array(
				'PARTICIPANT' => $PARTICIPANT,
			));
		}

		if (!empty($evt["comment"]))
		{
			$this->evt_tpl->vars(array(
				'comment_content' => htmlspecialchars($evt['comment']),
			));

			$this->evt_tpl->vars(array(
				"COMMENT" => $this->evt_tpl->parse("COMMENT"),
			));
		}

		if ($this->evt_tpl->is_template("project_media"))
		{
			// support single items as well as arrays
			$media_items = new aw_array($evt["media"]);
			$media_str = "";
			foreach($media_items->get() as $media_item)
			{
				$this->evt_tpl->vars($media_item);
				$media_str .= $this->evt_tpl->parse("project_media");
			};
			$this->evt_tpl->vars(array(
				"project_media" => $media_str,
			));
		};


		return $this->evt_tpl->parse();
	}

	function __sort_by_ord($el1, $el2)
	{
		return $el1["ord"] - $el2["ord"];
	}

	function __asc_sort($el1,$el2)
	{
		return (int)($el1["timestamp"] - $el2["timestamp"]);
	}

	function __desc_sort($el1,$el2)
	{
		return (int)($el2["timestamp"] - $el1["timestamp"]);
	}

	function __sort_events_by_jrk($el1, $el2)
	{
		$e1_len = $el1["end"] - $el1["start"];
		$e2_len = $el2["end"] - $el2["start"];

		if($e1_len > 86400 && $e2_len <= 86400) return 1;
		if($e1_len <= 86400 && $e2_len > 86400) return -1;
		if($e1_len <= 86400 && $e2_len <= 86400)
		{
			return $el1["start"] -$el2["start"];
		}
		$p1 = obj($el1["parent"]);
		$p2 = obj($el2["parent"]);
		return $p1->ord() - $p2->ord();
	}

	function __sort_events_starting_today_first($el1, $el2)
	{
		$day = $_GET['date'];
		if(!$day)
		{
			$day = $this->day;
		}
		if ($day == date('d-m-Y', $el1['start']) || $day == date('d-m-Y', $el2['start']))
		{
			if ($day == date("d-m-Y", $el1["start"]) && $day != date("d-m-Y", $el2["start"]))
			{
				return -1;
			}
			if ($day == date("d-m-Y", $el1["start"]) && $day == date("d-m-Y", $el2["start"]))
			{
				return (int)($el1['start'] - $el2['start']);
			}
			if ($day != date("d-m-Y", $el1["start"]) && $day == date("d-m-Y", $el2["start"]))
			{
				return 1;
			}
		}
		else
		{
			return (int)($el1['start'] - $el2['start']);
		}
	}
}

class awex_vcl_calendar extends aw_exception {}
class awex_vcl_calendar_time extends awex_vcl_calendar {}
