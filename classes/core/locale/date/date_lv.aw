<?php

namespace automatweb;

class awlc_date_lv implements awlc_date
{
	protected static $month = array("janvaris", "februaris", "marts", "aprilis", "maijs", "junijs", "julijs", "augusts", "septembris", "oktobris", "novemberis", "decemberis");

	public static function get_lc_date($timestamp, $format)
	{
		switch ($format)
		{
			case 1:
				$newdate=date("d.m.y", $timestamp);
				return $newdate;
			case 2:

				$newdate=date("d.m.Y", $timestamp);
				return $newdate;

			case 3:
				$newdate=date("d. ", $timestamp).self::$month[date("m", $timestamp)-1].date(" y",$timestamp);
				return $newdate;

			case 4:
				$newdate=date("d. ", $timestamp).self::$month[date("m", $timestamp)-1].date(" Y",$timestamp);
				return $newdate;

			case 5:
				$rv = ucfirst(self::$month[date("m",$timestamp)-1]) . " " . date("d",$timestamp);
				return $rv;

			case 6:
				$rv = date("d.",$timestamp).self::$month[date("m",$timestamp)-1] . date(".Y",$timestamp);
				return $rv;
			case 7:
				$newdate=date("H:i d.m.y", $timestamp);
				return $newdate;
		}
	}

	public static function get_lc_month($num)
	{
		return self::$month[$num-1];
	}

	public static function get_lc_weekday()
	{
		return "";
	}
}
?>
