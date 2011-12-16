<?php

/**
Date and time calculation
**/
class date_calc
{
	/** returns timestamps for the beginning and end of the given date range

		@attrib api=1 params=name

		@param time optional type=int
			unix timestamp of the time to start from

		@param date optional type=string
			date (format: d-m-y) to start from

		@param range_start optional type=int
			unix timestamp too start from

		@param type optional type=string
			defaults to "day", specifies the type of range to return. possible values:
			"day", "month", "year", "3month", "week", "relative", "last_events"


		@errors none

		@returns
			an array containing the date range, like this:
			array(
			"start" => $start_ts,                      - start timestamp for range
			"end" => $end_ts,                          - end timestamp for range
			"start_wd" => $start_wd,                   - start weekday for range
			"end_wd" => $end_wd,                       - end weekday for range
			"m" => $m,                                 - month for start
			"y" => $y,                                 - year for start
			"wd" => $wd,                               - weekday for start
			"prev" => $prev_d,                         - d-m-Y string for previous range
			"next" => $next_d,                         - d-m-Y string for next range
			"timestamp" => $timestamp,                 - timestamp for range
		);

		@examples
			classload("core/date/date_calc");
			$r = get_date_range(array("time" => time(), "type" => "week"));
			echo date("d.m.Y H:i:s", $r["start"]);	// echoes 00:00:00 the previous monday
			echo date("d.m.Y H:i:s", $r["end"]);	// echoes 23:59:59 on the next sunday


		@comment
			oe of the three parameters time, date, range_start must be given
	**/
	public static function get_date_range($args = array())
	{
		extract($args);
		if (!empty($date))
		{
			list($d,$m,$y) = explode("-",$date);
			if ($d && $m && !$y)    // 09-2009 format
			{
				$y = $m;
				$m = $d;
				$d = 1;
			}
			else
			if (!$y)
			{
				list($d,$m,$y) = explode(".",$date);
			}

			// deal with 2 part url-s
			if (empty($y))
			{
				$y = $m;
				$m = $d;
				$d = 1;
			};
		}
		else
		{
			list($d,$m,$y) = explode("-",date("d-m-Y",$time));
		}

		$timestamp = mktime(0,0,0,$m,$d,$y);
		$timestamp2 = mktime(23,59,59,$m,$d,$y);

		// if a range is specified then use that as the base for our calculations
		$range_start = isset($args["range_start"]) ? $args["range_start"] : 0;
		if ($range_start > 0)
		{
			$timestamp = $range_start;
			list($d,$m,$y) = explode("-",date("d-m-Y",$timestamp));
		}

		// current = 0, backward = 1, forward = 2
		// current - start or end from/at the timestamp
		if (!isset($args["direction"]) or $args["direction"] == 0)
		{
			$rg_start = $timestamp;
			// this will be calculated from the range type
			$rg_end = 0;
		}
		elseif (isset($args["direction"]) and $args["direction"] == 1)
		{
			// this time, this will be calculated from the range type
			$rg_start = 0;
			$rg_end = $timestamp2;
		}
		else
		{
			$rg_start = $timestamp;
			$rg_end = $timestamp2;
		}

		if (empty($type))
		{
			$type = "day";
		}

		$diff = 0;

		$eti = isset($args["event_time_item"]) ? $args["event_time_item"] : 0;

		if (!empty($eti) && is_numeric($eti))
		{
			if ($type === "day")
			{
				$diff = $eti;
			}
			elseif ($type === "week")
			{
				$diff = $eti * 7;
			}
		}

		// if range start is 0 and we know how many days we want, then base the calculations on that
		if ($rg_start == 0)
		{
			$rg_start = $timestamp - (86400 * $diff);
		}

		if ($rg_end == 0)
		{
			$rg_end = $timestamp2 + (86400 * $diff);
		}


		switch($type)
		{
			case "month":
				$start_ts = mktime(0,0,0,$m,1,$y);
				$end_ts = mktime(23,59,59,$m+1,0,$y);

				// special flag - fullweeks, if set we return dates from
				// the first monday of the month to the last sunday of the month

				// siin on next ja prev-i arvutamine monevorra special
				// kui p2ev on suurem, kui j2rgmises kuus p2evi kokku
				// j2rgmise kuu viimase p2eva. Sama kehtib eelmise kohta
				$next_mon = date("d",mktime(0,0,0,$m+2,0,$y));
				$prev_mon = date("d",mktime(0,0,0,$m,0,$y));

				if ($d > $next_mon)
				{
					$next = mktime(0,0,0,$m+1,$next_mon,$y);
				}
				else
				{
					$next = mktime(0,0,0,$m+1,$d,$y);
				}

				if ($d > $prev_mon)
				{
					$prev = mktime(0,0,0,$m-1,$prev_mon,$y);
				}
				else
				{
					$prev = mktime(0,0,0,$m-1,$d,$y);
				}
				break;

			case "year":
				$start_ts = mktime(0,0,0,1,1,$y);
				$end_ts = mktime(23,59,59,12,31,$y);

				$prev = mktime(0,0,0,$m,$d,$y-1);
				$next = mktime(23,59,59,$m,$d,$y+1);
				break;

			case "3month":
				$start_ts = mktime(0,0,0,$m-1,1,$y);
				$end_ts = mktime(23,59,59,$m+1,0,$y);
				break;


			case "week":
				$next = mktime(0,0,0,$m,$d+7,$y);
				$prev = mktime(0,0,0,$m,$d-7,$y);
				$daycode = self::convert_wday(date("w",$timestamp));
				// aga meil siin algab n2dal siiski esmasp2evast
				$monday = $d - $daycode + 1;
				$start_ts = mktime(0,0,0,$m,$monday,$y);
				$end_ts = mktime(23,59,59,$m,$monday+6,$y);
				break;

			case "relative":
							$next = mktime(0,0,0,0,0,0);
							$prev = mktime(0,0,0,0,0,0);
				// if we are supposed to show future events, then set the start range to
				// this same day
				// forward = 0, backward = 1
				if ($args["direction"] == "0")
				{
					if (!empty($args["event_time_item"]))
					{
						$d2 = $d + $args["event_time_item"];
					}
					else
					{
						$d2 = $d;
					}
					$start_ts = mktime(0,0,0,$m,$d,$y);
					$end_ts = mktime(0,0,0,$m,$d2,$y);
				}
				elseif (($args["direction"] == 1) && (isset($args["time"])) || (isset($args["date"])))
				{
					if (!empty($args["event_time_item"]))
					{
						$d2 = $d - $args["event_time_item"];
					}
					else
					{
						$d2 = $d;
					}
					$end_ts = mktime(0,0,0,$m,$d,$y);
					$start_ts = mktime(0,0,0,$m,$d2,$y-1);
				}
				else
				{
					$start_ts = mktime(0,0,0,1,1,2003);
				}

				if (empty($end_ts))
				{
					$end_ts = mktime(23,59,59,12,31,2003);
				};
				break;

			case "day":
				$start_ts = $rg_start;
				$end_ts = $rg_end;

				$next = $end_ts + 1;
				$prev = $start_ts - 1;
				break;

			case "relative":
				$next = mktime(0,0,0,0,0,0);
				$prev = mktime(0,0,0,0,0,0);
				$start_ts = mktime(0,0,0,1,1,2003);
				$end_ts = mktime(23,59,59,12,31,2003);
				break;

			case "last_events":
				$start_ts = $rg_start;
				$end_ts = time()+24*3600*200; // far enough methinks

				$next = $end_ts + 1;
				$prev = $start_ts - 1;
				break;
		}

		$start_wd = self::convert_wday(date("w",$start_ts));
		$end_wd = self::convert_wday(date("w",$end_ts));

		if (isset($args["fullweeks"]) and $args["fullweeks"] == 1)
		{
			if ($start_wd > 1)
			{
				$tambov = $start_wd - 1;
				$start_ts = $start_ts - ($tambov * 86400);
			}

			if ($end_wd < 7)
			{
				$tambov = 7 - $end_wd;
				$end_ts = $end_ts + ($tambov * 86400);
			}
		}

		$arr = array(
			"start" => $start_ts,
			"end" => $end_ts,
			"start_wd" => $start_wd,
			"end_wd" => $end_wd,
			"m" => $m,
			"y" => $y,
			"wd" => self::convert_wday(date("w",$timestamp)),
			"prev" => date("d-m-Y",$prev),
			"next" => date("d-m-Y",$next),
			"timestamp" => $timestamp,
		);
		return $arr;
	}

	/** modifies the day of week returned by date("w") for european standard

		@attrib api=1

		@param daycode required type=int
			the day number returned by date("w")

		@returns
			the real day number of the week, assuming that the week starts on monday

		@example
			echo "day of week currently in estonia: ".convert_wday(date("w"));

	**/
	public static function convert_wday($daycode)
	{
		return ($daycode == 0) ? 7 : $daycode;
	}

	/** Takes 2 timestamps and calculates the difference between them in days

		@attrib api=1 params=pos

		@param time1 required type=int
			start of range

		@param time2 required type=int
			end of range

		@returns length of the given range in days

		@example

			echo get_day_diff(time(), time() + 24*3600*5); // echos 5
	**/
	public static function get_day_diff($time1,$time2)
	{
		$diff = $time2 - $time1;
		$days = (int)($diff / 86400);
		return $days;
	}


	/** returns the timestamp for 00:00 on the 1st of the current month

		@attrib api=1

		@returns the timestamp for 00:00 on the 1st of the current month

		@example

			echo date("d.m.Y", get_month_start()); // echos the date for 1st of the current month
	**/
	public static function get_month_start()
	{
		return mktime(0,0,0, date("m"), 1, date("Y"));
	}


	/** returns the timestamp for 00:00 the given day

		@attrib api=1 params=name

		@param tm optional type=int
			the timestamp for the day to calculate, optional, defaults to the current time

		@returns the timestamp for 00:00 the given day

		@example

			echo date("d.m.Y H:i:s", get_day_start(time() - 24*3600)); // echos the date and time for yesterday 00:00:00
	**/
	public static function get_day_start($tm = NULL)
	{
		if ($tm === NULL)
		{
			$tm = time();
		}
		return mktime(0,0,0, date("m",$tm), date("d",$tm), date("Y",$tm));
	}

	/** returns the timestamp on the january 1st of the current year
		@attrib api=1 params=name
		@param tm optional type=int
			the timestamp for the year to calculate, optional, defaults to the current time
		@returns the timestamp for 00:00 on the January 1st of the current year
	**/
	public static function get_year_start($tm = NULL)
	{
		if ($tm === NULL)
		{
			$tm = time();
		}
		return mktime(0,0,0, 1, 1, date("Y",$tm));
	}


	/** returns true if the given timespans ($a_from, $a_to) - ($b_from - $b_to) overlap

		@attrib api=1 params=name

		@param a_from required type=int
			the timestamp for the beginning of the first range

		@param a_to required type=int
			the timestamp for the end of the first range

		@param b_from required type=int
			the timestamp for the beginning of the second range

		@param a_to required type=int
			the timestamp for the end of the second range


		@returns true if the given ranges overlap, false if not

		@example

			echos "leopard":

			if (timespans_overlap(time(), time() + 10, time()-100, time() + 100))
			{
				echo "leopard!";
			}

	**/
	public static function timespans_overlap($a_from, $a_to, $b_from, $b_to)
	{
		// test for NOT overlapping, that's simpler.
		// two options here: completely before or completely after
		if ($a_to <= $b_from)
		{
			return false;
		}
		if ($a_from >= $b_to)
		{
			return false;
		}
		return true;
	}

/** returns the timestamp for 00:00 on the last monday

	@attrib api=1

	@param timestamp optional type=int
		Timestamp to return the week start for

	@returns the timestamp for 00:00 on the last monday

	@example

		echo date("d.m.Y", get_week_start()); // echos the date for last monday

**/
	public static function get_week_start($timestamp = null)
	{
		if ($timestamp === null)
		{
			$timestamp = time();
		}
		$wd_lut = array(0 => 6, 1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5);
		$wday = $wd_lut[date("w",$timestamp )];
		return mktime(0,0,0, date("m",$timestamp ), date("d",$timestamp )-$wday, date("Y",$timestamp ));
	}

	/** Attempts to convert a date(time) string representation to UNIX timestamp
		@attrib api=1 params=pos
		@param value type=string
			Any textual date(time) representation
		@comment
			This method accepts similar arguments as PHP strtotime() function
		@returns int
			Returns 0 if all attempts fail according to programmed assessment
		@errors none
	**/
	public function string2time($value)
	{
		$time = 0;
		$t = strtotime($value);
		if (false === $t)
		{
			//TODO
		}
		else
		{
			$time = $t;
		}

		return $time;
	}
}
