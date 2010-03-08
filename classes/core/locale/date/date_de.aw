<?php
/*
@classinfo  maintainer=kristo
*/
class awlc_date_de implements awlc_date
{
	protected static $month = array("Januar", "Februar", "M�rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");

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

	public static function get_lc_weekday($num, $short = false, $ucfirst = true)
	{
		// date("w") returns 0 for sunday, but for historical reasons 7 should also be sunday
//		$names = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag","Sonntag");
		$names = array("sonntag","montag","dienstag","mittwoch","donnerstag","freitag","samstag","sonntag");
		$name = ($ucfirst) ? ucfirst($names[$num]) : $names[$num];
		return $short ? substr($name,0,3) : $name;
	}

	public static function get_lc_month($num)
	{
		return self::$month[$num-1];
	}


}
?>
