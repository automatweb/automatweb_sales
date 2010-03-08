<?php

/**
@classinfo  maintainer=voldemar
Localisation utilities class.
**/
class aw_locale
{
	const DATE_SHORT = 1; // For example: 20.06.88 or 05.12.98
	const DATE_SHORT_FULLYEAR = 2; // For example: 20.06.1999 or 05.12.1998
	const DATE_LONG = 3; // For example: 20. juuni 99
	const DATE_LONG_FULLYEAR = 4; // For example: 20. juuni 1999
	const DATETIME_SHORT = 10; // For example: 20.06.88 15:25 or 05.12.98 15:25
	const DATETIME_SHORT_FULLYEAR = 11; // For example: 20.06.1999 15:25 or 05.12.1998 15:25
	const DATETIME_LONG = 12; // For example: 20. juuni 99 15:25
	const DATETIME_LONG_FULLYEAR = 13; // For example: 20. juuni 1999 15:25

	const TIME_STD = 1; // 20h 6min 8s
	const TIME_SHORT_WORDS = 2; // 20t 6min 8sek
	const TIME_LONG_WORDS = 3; // 10 hours 15 minutes and 23 seconds


	const DATE_DEFAULT_FORMAT = "[\E] m.d Y H:i";

	protected static $lc_data = array( // locale data by locale code
		"de" => array(),
		"en" => array(),
		"es" => array(),
		"et" => array(),
		"fi" => array(),
		"fr" => array(),
		"lt" => array(),
		"lv" => array(),
		"ru" => array()
	);

	private static $default_locale = "en";
	private static $current_locale = false;


	/** returns the name of the weekday in the current language
		@attrib api=1 params=pos

		@param num required type=int
			The number of the weekday to return.

		@param short optional type=bool
			If true, the short name of the weekday is returned. Defaults to false

		@param ucfirst optional type=bool
			If true, the first characters are uppercased. ddefaults to false

		@comment
			The number of the weekday is 0-7 inclusive. 0 and 7 both are for sunday.
	**/
	public static function get_lc_weekday($num, $short = false, $ucfirst = false)
	{
		$lc = self::get_lc();
		$method = array("awlc_date_{$lc}", "get_lc_weekday");
		$weekday = "";
		if (is_readable(AW_DIR . "classes/core/locale/date/date_{$lc}" . AW_FILE_EXT))
		{
			$weekday = call_user_func($method, $num, $short = false, $ucfirst = false);
		}
		return $weekday;
	}

	/** returns the name of the month in the current language
		@attrib api=1 params=pos

		@param num required type=int
			The number of the month to return the name for

	**/
	public static function get_lc_month($num)
	{
		$lc = self::get_lc();
		$method = array("awlc_date_{$lc}", "get_lc_month");
		$month = "";
		if (is_readable(AW_DIR . "classes/core/locale/date/date_{$lc}" . AW_FILE_EXT))
		{
			$month = call_user_func($method, $num);
		}
		return $month;
	}

	/** returns a localized date in the current language
		@attrib api=1 params=pos

		@param timestamp optional type=int
			The unix timestamp to return the date for. If NULL current time is used.

		@param format required type=int
			One of the defined date formats

		@comment
			The date formats are:
				aw_locale::DATE_SHORT = For example: 20.06.88 or 05.12.98
				aw_locale::DATE_SHORT_FULLYEAR = For example: 20.06.1999 or 05.12.1998
				aw_locale::DATE_LONG = For example: 20. juuni 99
				aw_locale::DATE_LONG_FULLYEAR = For example: 20. juuni 1999
				aw_locale::DATETIME_SHORT = For example: 20.06.88 21:45 or 05.12.98 21:45
				aw_locale::DATETIME_SHORT_FULLYEAR = For example: 20.06.1999 21:45 or 05.12.1998 21:45
				aw_locale::DATETIME_LONG = For example: 20. juuni 99 21:45
				aw_locale::DATETIME_LONG_FULLYEAR = For example: 20. juuni 1999 21:45

		@returns string
	**/
	public static function get_lc_date($timestamp, $format)
	{
		if (empty($timestamp)) //!!! teha et saaks siin olla NULL === $timestamp
		{
			$timestamp = time();
		}

		$date = "";
		settype($timestamp, "int");

		if (PHP_OS === "WINNT" && $timestamp < 0)
		{
			$date = t("n/a");
		}

		$lc = self::get_lc();
		$method = array("awlc_date_{$lc}", "get_lc_date");
		if (is_readable(AW_DIR . "classes/core/locale/date/date_{$lc}" . AW_FILE_EXT))
		{
			$date = call_user_func($method, $timestamp, $format);
		}

		if (empty($date))
		{
			$date = date(self::DATE_DEFAULT_FORMAT, $timestamp);
		}

		return $date;
	}

	/** returns a localized duration expression in the current language
		@attrib api=1 params=pos

		@param duration type=int
			Duration in seconds to convert to string

		@param format optional type=int default=self::TIME_STD
			One of the defined time formats (TIME_... constants)

		@comment
			The date formats are:
				aw_locale::TIME_STD = 20h 6min 8s
				aw_locale::TIME_SHORT_WORDS = 20t 6min 8sek
				aw_locale::TIME_LONG_WORDS = For example: 10 hours 15 minutes and 23 seconds

		@returns string
	**/
	public static function get_lc_time($duration, $format = 1)
	{
		settype($duration, "int");

		$duration_info = self::get_duration_info($duration);

		if (self::TIME_STD === $format)
		{
			$string = self::get_std_time($duration_info);
		}
		elseif (0 === $duration)
		{
			$string = "0";
		}
		else
		{
			$lc = self::get_lc();
			$method = array("awlc_date_{$lc}", "get_lc_time");
			if (is_readable(AW_DIR . "classes/core/locale/date/date_{$lc}" . AW_FILE_EXT))
			{
				$string = call_user_func($method, $duration_info, $format);
			}

			if (empty($string))
			{
				$string = self::get_std_time($duration_info);
			}
		}

		return $string;
	}

	private static function get_std_time($duration_info)
	{
		$r = "{$duration_info["sign"]}{$duration_info["hours"]}h {$duration_info["minutes"]}min {$duration_info["seconds"]}s";
		return $r;
	}

	private static function get_duration_info($duration)
	{
		$duration_info = array(
			"sign" => $duration < 0 ? "- " : "",
			"weeks" => (int) ($duration / 86400 / 7),
			"days" => $duration / 86400 % 7,
			"hours" => $duration / 3600 % 24,
			"minutes" => $duration / 60 % 60,
			"seconds" => $duration % 60
		);
		return $duration_info;
	}

	/** returns a readable string for the number given
		@attrib api=1 params=pos

		@param number required type=int
			The number to stringify

		@returns
			the text version of the number.

		@examples
			if the language is english, then
				aw_locale::get_lc_number(7);
			returns "seven"
	**/
	public static function get_lc_number($number)
	{
		$lc = self::get_lc();
		$method = array("awlc_number_{$lc}", "get_lc_number");
		if (is_readable(AW_DIR . "classes/core/locale/number/number_{$lc}" . AW_FILE_EXT))
		{
			$number = call_user_func($method, $number);
		}
		return $number;
	}

	/** returns the given amount of money as text with the currency name n the right place
		@attrib api=1 params=pos

		@param number required type=double
			The sum to stringify

		@param currency required type=object
			The currency object to use for the sum.

		@param lc optional type=string
			The locale code, defaults to the current one

		@comment
			Does the same, as get_lc_number, but appends/prepends the currency name and unit names as needed. Used for writing the amount on bills as text.
	**/
	public static function get_lc_money_text($number, $currency, $lc = NULL)
	{
		if (!self::is_valid_lc_code($lc))
		{
			$lc = self::get_lc();
		}

		$method = array("awlc_number_{$lc}", "get_lc_money_text");
		$lc = self::get_lc();
		if (is_readable(AW_DIR . "classes/core/locale/number/number_{$lc}" . AW_FILE_EXT))
		{
			$number = call_user_func($method, $number, $currency);
		}
		return $number;
	}

	/** returns genitive case of a proper name
		@attrib api=1 params=pos

		@param name required type=string

		@param lc optional type=string
			The locale code, defaults to the current one
	**/
	public static function get_genitive_for_name($name, $lc = NULL)
	{
		settype($name, "string");

		if (!self::is_valid_lc_code($lc))
		{
			$lc = self::get_lc();
		}

		$method = array("awlc_cases_{$lc}", "get_genitive_for_name");
		if (is_readable(AW_DIR . "classes/core/locale/cases/cases_{$lc}" . AW_FILE_EXT))
		{
			$name = call_user_func($method, $name);
		}
		return $name;
	}

	/** checks if lc code is valid
		@attrib api=1 params=pos
		@param code required type=string
		@returns bool
	**/
	public static function is_valid_lc_code($code)
	{
		return isset(self::$lc_data[(string) $code]);
	}

	private static function get_lc()
	{
		if (false === self::$current_locale)
		{
			$lc = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC");
			if (!self::is_valid_lc_code($lc))
			{
				$lc = self::$default_locale;
			}
			self::$current_locale = $lc;
		}
		return self::$current_locale;
	}
}

/** Generic locale class error **/
class awex_locale extends aw_exception {}


interface awlc_date
{
	public static function get_lc_month($num);
	public static function get_lc_date($timestamp, $format);
	public static function get_lc_weekday($num, $short = false, $ucfirst = false);
}

interface awlc_number
{
	public static function get_lc_number($number);
	public static function get_lc_money_text($number, $currency);
}

interface awlc_cases
{
	public static function get_genitive_for_name($name);
}

?>
