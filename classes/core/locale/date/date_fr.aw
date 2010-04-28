<?php

namespace automatweb;

class awlc_date_fr implements awlc_date
{
	protected static $month = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

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
				$newdate=date("d ", $timestamp).self::$month[date("m", $timestamp)-1].date(" y",$timestamp);
				return $newdate;

			case 4:
				$newdate=date("d ", $timestamp).self::$month[date("m", $timestamp)-1].date(" Y",$timestamp);
				return $newdate;

			case 5:
				$rv = date("j. ",$timestamp).self::$month[date("m",$timestamp)-1];
				break;

			case 6:
				$rv = date("j. ",$timestamp).self::$month[date("m",$timestamp)-1] . date(" Y",$timestamp);
				break;

			case 7:
				$newdate=date("H:i d.m.y", $timestamp);
				return $newdate;

		}
	}

	public static function get_lc_weekday($num, $short = false, $ucfirst = false)
	{
		// date("w") returns 0 for sunday, but for historical reasons 7 should also be sunday
		$names = array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi","dimanche");
		$name = ($ucfirst) ? ucfirst($names[$num]) : $names[$num];
		return $short ? substr($name,0,3) . "." : $name;
	}

	public static function get_lc_month($num)
	{
		return self::$month[$num-1];
	}
}

?>
