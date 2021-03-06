<?php

/**
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

	const BYTES_SHORT = 2;
	const BYTES_LONG = 4;

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

	private static $byte_binary_units_short = array(
		"B",
		"KiB",
		"MiB",
		"GiB",
		"TiB",
		"PiB",
		"EiB",
		"ZiB",
		"YiB"
	);

	private static $byte_decimal_units_short = array(
		"B",
		"kB",
		"MB",
		"GB",
		"TB",
		"PB",
		"EB",
		"ZB",
		"YB"
	);


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
			$weekday = call_user_func($method, $num, $short, $ucfirst);
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

		@param timestamp type=int default=NULL
			The unix timestamp to return the date for. If NULL current time is used.

		@param format type=int default=aw_locale::DATE_SHORT_FULLYEAR
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
	public static function get_lc_date($timestamp = null, $format = self::DATE_SHORT_FULLYEAR)
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

		@param number type=double
			The sum to stringify

		@param currency type=CL_CURRENCY
			The currency object to use for the sum.

		@param lc type=string default=NULL
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

		@param name type=string

		@param lc type=string default=NULL
			The locale code, defaults to the current one

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

	/** Converts number of bytes to human readable string size info
		@attrib api=1 params=pos
		@param bytes type=int
		@param format type=int default=aw_locale::BYTES_SHORT
		@param precision type=int default=2
		@param binary type=bool default=TRUE
			Binary or decimal
		@param lc optional type=string default=null
			The locale code, defaults to the current one
		@comment
		@returns string
		@errors
	**/
	public static function bytes2string ($bytes, $format = self::BYTES_SHORT, $precision = 2, $binary = true, $lc = null)
	{
		settype($bytes, "int");

		if (!$lc or !self::is_valid_lc_code($lc))
		{
			$lc = self::get_lc();
		}

		$units = $binary ? self::$byte_binary_units_short : self::$byte_decimal_units_short;
		$count = count($units) - 2;

		for ($i = 0; $bytes >= 1024 && $i < $count; $i++)
		{
			$bytes /= 1024;
		}

		$str = round($bytes, (int) $precision) . " " . $units[$i];
		return $str;
	}

	/** Returns value with unit name in correct grammatical case
		@attrib api=1 params=pos
		@param value type=string
			Numeric string. Float values can have dot or comma for radix point. Common fractions in from n/m
		@param unit_spec type=string
			AW unit usage specification format string
			Specification items are separated by semicolon. * means any value, other specs are treated as exceptions.
			Trailing and preceding white space is ignored. Float values can be specified (dot as radix point).
			Common fractions can be represented by separating numerator and denominator with / character
			Value part may potentially be any string containing no white space but since replacement is made by direct
			comparison then result may be unexpected
			An example for euro currency: "* euros; 1 euro"
		@returns string
			Return value correctness depends on unit specification validity
		@errors none
	**/
	public static function get_unit_string($value, $unit_spec)
	{
		// standardize radix point and remove white space
		$value_parsed = str_replace(array(",", " ", "\t"), array(".", "", ""), $value);

		// separate fraction/float value parts
		$value_part1 = (int) strtok($value_parsed, "./");
		$value_part2 = strtok("./");

		if (false === $value_part2 or "" === $value_part2)
		{ // integer
			// look for exact cases in spec
			$pattern = "#[;]?([^;]*)([\./])?{$value_parsed}([^;]*)[;]?#";
			preg_match($pattern, $unit_spec, $unit_name);

			if (isset($unit_name[1]) and !in_array(substr($unit_name[1], -1), array(".", "/")))
			{
				$unit_name_prepend = empty($unit_name[1]) ? "" : $unit_name[1];
				$unit_name_append = empty($unit_name[3]) ? "" : $unit_name[3];
			}
		}
		else
		{ // non integer value.
			// look for exact cases in spec
			$pattern = "#[;]?([^;]*){$value_parsed}([^;]*)[;]?#";
			preg_match($pattern, $unit_spec, $unit_name);

			$unit_name_prepend = empty($unit_name[1]) ? "" : $unit_name[1];
			$unit_name_append = empty($unit_name[2]) ? "" : $unit_name[2];

			if (empty($unit_name_prepend) and empty($unit_name_append))
			{
				// look for specific wildcard definitions
				settype($value_part2, "int");
				$separator = false === strpos($value_parsed, ".") ? "\/" : "\.";
				$pattern = "#[;]?([^;]*)(({$value_part1}{$separator}\*)|(\*{$separator}{$value_part2})|(\*{$separator}\*))([^;]*)[;]?#";
				preg_match($pattern, $unit_spec, $unit_name);

				$unit_name_prepend = empty($unit_name[1]) ? "" : $unit_name[1];
				$unit_name_append = empty($unit_name[6]) ? "" : $unit_name[6];
			}
		}

		if (empty($unit_name_prepend) and empty($unit_name_append))
		{ // look for most general wildcard: *
			$pattern = "#[;]?([^;]*)\*([^;]*)[;]?#";
			preg_match($pattern, $unit_spec, $unit_name);

			$unit_name_prepend = empty($unit_name[1]) ? "" : $unit_name[1];
			$unit_name_append = empty($unit_name[2]) ? "" : $unit_name[2];
		}

		return trim($unit_name_prepend . $value . $unit_name_append);
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

/** Generic date error **/
class awex_locale_date extends awex_locale {}

/** Weekday related exception **/
class awex_locale_date_weekday extends awex_locale_date {}



interface awlc_date
{
	// public static function get_months();
	// public static function get_weekdays();
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
