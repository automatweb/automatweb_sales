<?php
/*
@classinfo  maintainer=kristo
*/
class awlc_date_ru implements awlc_date
{
	protected static $month = array("&#1103;&#1085;&#1074;&#1072;&#1088;&#1100;",
	"&#1092;&#1077;&#1074;&#1088;&#1072;&#1083;&#1100;", "&#1084;&#1072;&#1088;&#1090;",
	"&#1072;&#1087;&#1088;&#1077;&#1083;&#1100;", "&#1084;&#1072;&#1081;", "&#1080;&#1102;&#1085;&#1100;",
	"&#1080;&#1102;&#1083;&#1100;", "&#1072;&#1074;&#1075;&#1091;&#1089;&#1090;",
	"&#1089;&#1077;&#1085;&#1090;&#1103;&#1073;&#1088;&#1100;", "&#1086;&#1082;&#1090;&#1103;&#1073;&#1088;&#1100;",
	"&#1085;&#1086;&#1103;&#1073;&#1088;&#1100;", "&#1076;&#1077;&#1082;&#1072;&#1073;&#1088;&#1100;");

	// vene keeles on 1.jaanuar ja jaanuar 2001 erinevad, siia $month2 sisse tuleb kirjutada kuunimed nii, nagu
	// nad peaksid olema p��ratud kujul.
	protected static $month2 = array("&#1103;&#1085;&#1074;&#1072;&#1088;&#1103;", "&#1092;&#1077;&#1074;&#1088;&#1072;&#1083;&#1103;", "&#1084;&#1072;&#1088;&#1090;&#1072;", "&#1072;&#1087;&#1088;&#1077;&#1083;&#1103;", "&#1084;&#1072;&#1103;", "&#1080;&#1102;&#1085;&#1103;", "&#1080;&#1102;&#1083;&#1103;", "&#1072;&#1074;&#1075;&#1091;&#1089;&#1090;&#1072;", "&#1089;&#1077;&#1085;&#1090;&#1103;&#1073;&#1088;&#1103;", "&#1086;&#1082;&#1090;&#1103;&#1073;&#1088;&#1103;", "&#1085;&#1086;&#1103;&#1073;&#1088;&#1103;", "&#1076;&#1077;&#1082;&#1072;&#1073;&#1088;&#1103;");

	protected static $weekday_names = array("&#1074;&#1086;&#1089;&#1082;&#1088;&#1077;&#1089;&#1077;&#1085;&#1100;&#1077;", "&#1087;&#1086;&#1085;&#1077;&#1076;&#1077;&#1083;&#1100;&#1085;&#1080;&#1082;","&#1074;&#1090;&#1086;&#1088;&#1085;&#1080;&#1082;","&#1089;&#1088;&#1077;&#1076;&#1072;","&#1095;&#1077;&#1090;&#1074;&#1077;&#1088;&#1075;","&#1087;&#1103;&#1090;&#1085;&#1080;&#1094;&#1072;","&#1089;&#1091;&#1073;&#1073;&#1086;&#1090;&#1072;","&#1074;&#1086;&#1089;&#1082;&#1088;&#1077;&#1089;&#1077;&#1085;&#1100;&#1077;");

	protected static $weekday_names_ucfirst = array('&#1042;&#1086;&#1089;&#1082;&#1088;&#1077;&#1089;&#1077;&#1085;&#1100;&#1077;', '&#1055;&#1086;&#1085;&#1077;&#1076;&#1077;&#1083;&#1100;&#1085;&#1080;&#1082;','&#1042;&#1090;&#1086;&#1088;&#1085;&#1080;&#1082;','&#1057;&#1088;&#1077;&#1076;&#1072;','&#1063;&#1077;&#1090;&#1074;&#1077;&#1088;&#1075;','&#1055;&#1103;&#1090;&#1085;&#1080;&#1094;&#1072','&#1057;&#1091;&#1073;&#1073;&#1086;&#1090;&#1072;','&#1042;&#1086;&#1089;&#1082;&#1088;&#1077;&#1089;&#1077;&#1085;&#1100;&#1077;');

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
                                $rv = date("j. ",$timestamp).self::$month2[date("m",$timestamp)-1];
                                return $rv;

                        case 6:
                                $rv = date("j. ",$timestamp).self::$month[date("m",$timestamp)-1] . date(" Y",$timestamp);
				return $rv;




			case 7:
				$newdate=date("H:i d.m.y", $timestamp);
				return $newdate;
		}
	}

	public static function get_lc_weekday($num, $short = false, $ucfirst = false)
	{
		// array starts from 0, estonian weekdays from 1
		if ($ucfirst)
		{
			return $short ? substr(self::$weekday_names_ucfirst[$num],0,1) : self::$weekday_names_ucfirst[$num];
		}
		else
		{
			return $short ? substr(self::$weekday_names[$num],0,1) : self::$weekday_names[$num];
		}
	}

	public static function get_lc_month($num)
	{
		return self::$month[$num-1];
	}

}
?>
