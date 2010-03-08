<?php
/*
@classinfo  maintainer=kristo
*/
class awlc_date_fi implements awlc_date
{
	protected static $month = array("tammikuu", "helmikuu", "maaliskuu", "huhtikuu", "toukokuu", "kesäkuu", "heinäkuu", "elokuu", "syyskuu", "lokakuu", "marraskuu", "joulukuu");

	public static function get_lc_date($timestamp, $format)
	{
		$mname = self::$month[date("m", $timestamp)-1] . "ta";

		switch ($format)
		{
			case 1:
				$rv = date("d.m.y", $timestamp);
				break;

			case 2:
				$rv = date("d.m.Y", $timestamp);
				break;

			case 3:
				$rv = date("d. ", $timestamp).$mname.date(" y", $timestamp);
				break;

			case 4:
				$rv = date("d. ", $timestamp).$mname.date(" Y", $timestamp);
				break;

			case 5:
				$rv = date("d. ", $timestamp).$mname;
				break;

			case 7:
				$rv = date("H:i j.m.Y", $timestamp);
				break;
		}
		return $rv;
	}

	public static function get_lc_weekday($num, $short = false, $ucfirst = false)
	{
		$names = array("sunnuntai", "maanantai", "tiistai", "keskiviikko", "torstai", "perjantai", "lauantai", "sunnuntai");
		$name = ($ucfirst) ? ucfirst($names[$num]) : $names[$num];
		return $short ? substr($name, 0, 2) : $name;
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
				$parts[] = $duration_info["hours"] . ($duration_info["hours"] === 1 ? " tunti" : " tuntia");
			}

			if ($duration_info["minutes"])
			{
				$parts[] = $duration_info["minutes"] . ($duration_info["minutes"] === 1 ? " minuutti" : " minuuttia");
			}

			if ($duration_info["seconds"])
			{
				$parts[] = $duration_info["seconds"] . ($duration_info["seconds"] === 1 ? " sekunti" : " sekuntia");
			}

			$r = implode(" ", $parts);
		}
		return $r;
	}
}
?>
