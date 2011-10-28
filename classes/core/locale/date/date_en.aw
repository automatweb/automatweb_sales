<?php

class awlc_date_en implements awlc_date
{
	protected static $month = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

	// date("w") returns 0 for sunday, but for historical reasons 7 should also be sunday
	protected static $weekdays = array("sunday", "monday", "tueday", "wednesday", "thursday", "friday", "saturday", "sunday");

	public static function get_lc_date($timestamp, $format)
	{
		switch ($format)
		{
			case aw_locale::DATE_SHORT:
				return date("m.d.y", $timestamp);

			case aw_locale::DATE_SHORT_FULLYEAR:
				return date("m.d.Y", $timestamp);

			case aw_locale::DATE_LONG:
				return date("d ", $timestamp).self::$month[date("m", $timestamp)-1].date(" y",$timestamp);

			case aw_locale::DATE_LONG_FULLYEAR:
				return date("d ", $timestamp).self::$month[date("m", $timestamp)-1].date(" Y",$timestamp);

			case 5:
				return ucfirst(self::$month[date("m",$timestamp)-1]) . " " . date("d",$timestamp);

			case 6:
				return ucfirst(self::$month[date("m",$timestamp)-1]) . " " . date("d",$timestamp) . date(" Y",$timestamp);

			case 7:
				return date("H:i d.m.y", $timestamp);

			case aw_locale::DATETIME_SHORT:
				return date("m.d.y g:i a", $timestamp);

			case aw_locale::DATETIME_SHORT_FULLYEAR:
				return date("m.d.Y g:i a", $timestamp);

			case aw_locale::DATETIME_LONG:
				return date("d ", $timestamp).self::$month[date("m", $timestamp)-1].date(" y g:i a",$timestamp);

			case aw_locale::DATETIME_LONG_FULLYEAR:
				return date("d ", $timestamp).self::$month[date("m", $timestamp)-1].date(" Y g:i a",$timestamp);
		}
	}

	public static function get_lc_weekday($num, $short = false, $ucfirst = true)
	{
		if (7 == $num)
		{
			trigger_error("Weekday index 7 is deprecated (for sunday use 0). Called by " . get_caller_str(2), E_USER_DEPRECATED);
		}

		if (isset(self::$weekdays[$num]))
		{
			$name = $ucfirst ? ucfirst(self::$weekdays[$num]) : self::$weekdays[$num];
		}
		else
		{
			throw new awex_locale_date_weekday("No weekday for index '{$num}'");
		}

		return $short ? substr($name,0,3) : $name;
	}

	public static function get_lc_month($num)
	{
		return self::$month[$num-1];
	}

	public static function get_lc_time($duration_info, $format)
	{
		$r = "";
		$parts = array();
		if (aw_locale::TIME_SHORT_WORDS === $format)
		{
			if ($duration_info["sign"])
			{
				$parts[] = $duration_info["sign"];
			}

			if ($duration_info["hours"])
			{
				$parts[] = "{$duration_info["hours"]}h";
			}

			if ($duration_info["minutes"])
			{
				$parts[] = "{$duration_info["minutes"]}min";
			}

			if ($duration_info["seconds"])
			{
				$parts[] = "{$duration_info["seconds"]}sec";
			}

			$r = implode(" ", $parts);
		}
		elseif (aw_locale::TIME_LONG_WORDS === $format)
		{
			if ($duration_info["sign"])
			{
				$parts[] = $duration_info["sign"];
			}

			if ($duration_info["hours"])
			{
				$parts[] = $duration_info["hours"] . ($duration_info["hours"] === 1 ? " hour" : " hours");
			}

			if ($duration_info["minutes"])
			{
				$parts[] = $duration_info["minutes"] . ($duration_info["minutes"] === 1 ? " minute" : " minutes");
			}

			if ($duration_info["seconds"])
			{
				$parts[] = $duration_info["seconds"] . ($duration_info["seconds"] === 1 ? " second" : " seconds");
			}

			$r = implode(" ", $parts);
		}
		return $r;
	}
}
