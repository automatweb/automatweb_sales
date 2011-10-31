<?php

class awlc_date_et implements awlc_date
{
	protected static $month = array("jaanuar", "veebruar", "m&auml;rts", "aprill", "mai", "juuni", "juuli", "august", "september", "oktoober", "november", "detsember");

	public static function get_lc_date($timestamp, $format)
	{
		$rv = "";

		switch ($format)
		{
			case aw_locale::DATE_SHORT:
				$rv = date("j.m.y", $timestamp);
				break;

			case aw_locale::DATE_SHORT_FULLYEAR:
				$rv = date("j.m.Y", $timestamp);
				break;

			case aw_locale::DATE_LONG:
				$rv = date("j. ", $timestamp).self::$month[date("m", $timestamp)-1].date(" y",$timestamp);
				break;

			case aw_locale::DATE_LONG_FULLYEAR:
				$rv = date("j. ", $timestamp).self::$month[date("m", $timestamp)-1].date(" Y",$timestamp);
				break;

			case 5:
				$rv = date("j. ",$timestamp).self::$month[date("m",$timestamp)-1];
				break;

			case 6:
				$rv = date("j. ",$timestamp).self::$month[date("m",$timestamp)-1] . date(" Y",$timestamp);
				break;

			case 7:
				$rv = date("H:i j.m.Y", $timestamp);
				break;

			case aw_locale::DATETIME_SHORT:
				$rv = date("j.m.y H:i", $timestamp);
				break;

			case aw_locale::DATETIME_SHORT_FULLYEAR:
				$rv = date("j.m.Y H:i", $timestamp);
				break;

			case aw_locale::DATETIME_LONG:
				$rv = date("j. ", $timestamp).self::$month[date("m", $timestamp)-1].date(" y H:i",$timestamp);
				break;

			case aw_locale::DATETIME_LONG_FULLYEAR:
				$rv = date("j. ", $timestamp).self::$month[date("m", $timestamp)-1].date(" Y H:i",$timestamp);
				break;
		}

		return $rv;
	}

	public static function get_lc_weekday($num, $short = false, $ucfirst = false)
	{
		// date("w") returns 0 for sunday, but for historical reasons should also work with 7
		$names = array("p&uuml;hap&auml;ev","esmasp&auml;ev","teisip&auml;ev","kolmap&auml;ev","neljap&auml;ev","reede","laup&auml;ev","p&uuml;hap&auml;ev");
		$name = ($ucfirst) ? ucfirst($names[$num]) : $names[$num];
		return $short ? substr($name,0,1) : $name;
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
				$parts[] = "{$duration_info["hours"]}t";
			}

			if ($duration_info["minutes"])
			{
				$parts[] = "{$duration_info["minutes"]}min";
			}

			if ($duration_info["seconds"])
			{
				$parts[] = "{$duration_info["seconds"]}sek";
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
				$parts[] = $duration_info["hours"] . ($duration_info["hours"] === 1 ? " tund" : " tundi");
			}

			if ($duration_info["minutes"])
			{
				$parts[] = $duration_info["minutes"] . ($duration_info["minutes"] === 1 ? " minut" : " minutit");
			}

			if ($duration_info["seconds"])
			{
				$parts[] = $duration_info["seconds"] . ($duration_info["seconds"] === 1 ? " sekund" : " sekundit");
			}

			$r = implode(" ", $parts);
		}
		return $r;
	}
}

