<?php

class datepicker extends core implements vcl_interface
{
	function __construct()
	{
		$this->init("");
	}

	/**
		@attrib params=name api=1

		@param name required type=string
			String to indetify the object picker

		@param textsize type=string default=null
			Textbox text size. CSS font size expression (e.g. '11px').

		@param from type=string default=""
			ISO-8601 derived format time to allow selecting from. Default means -10 years
			Shorthand functions also available (currently only one):
				'now' - the time when view is requested
			example: 2005-08-15T15:52:01

		@param to type=string default=""
			ISO-8601 derived format time to allow selecting until. Default means +10 years
			example: 2005-08-15T15:52:01

		@param disabled type=bool default=false
			Element is disabled/not disabled

		@param time type=bool default=true
			Enable/disable time selecting

		@returns string
			The HTML of the date picker.
	**/
	public function create($arr)
	{
		if (empty($arr["name"]) or !is_string($arr["name"]))
		{
			throw new awex_datepicker_param("Name is required and must be a string.");
		}

		$enable_time = isset($arr["time"]) ? (bool) $arr["time"] : true;

		load_javascript("jquery/plugins/datepick/jquery.datepick.min.js");
		load_javascript("jquery/plugins/datepick/jquery.datepick-et.js");
		$date_textbox = html::textbox(array(
			"name" => $arr["name"]."[date]",
			"value" => isset($arr["value"]["date"]) ? $arr["value"]["date"] : "",
			"disabled" => isset($arr["disabled"]) ? $arr["disabled"] : false,
			"size" => 10,
			"textsize" => !empty($arr["textsize"]) ? $arr["textsize"] : null
		));

		$from = $to = "";

		if (isset($arr["from"]))
		{
			if ("now" === $arr["from"])
			{
				$from = date("c");
			}
			else
			{
				$from = $arr["from"];
			}

			$from = self::get_js_date_argument($from);
			$from = "minDate : new Date(\"{$from}\"),";
		}

		if (isset($arr["to"]))
		{
			$to = self::get_js_date_argument($arr["to"]);
			$to = "maxDate : new Date(\"{$to}\"),";
		}

		$datepicker = <<<EOS
<script type="text/javascript">
$("input[name='{$arr["name"]}[date]']").datepick({
	{$from}
	{$to}
});
</script>
EOS;

		if ($enable_time)
		{
			load_javascript("jquery/plugins/ptTimeSelect/jquery.ptTimeSelect.js");
			$time_textbox = html::textbox(array(
				"name" => $arr["name"]."[time]",
				"value" => isset($arr["value"]["time"]) ? $arr["value"]["time"] : "",
				"disabled" => isset($arr["disabled"]) ? $arr["disabled"] : false,
				"size" => 5,
				"textsize" => !empty($arr["textsize"]) ? $arr["textsize"] : null
			));
			$timepicker = <<<EOS
<script type="text/javascript">
$("input[name='{$arr["name"]}[time]']").ptTimeSelect();
</script>
EOS;
		}
		else
		{
			$time_textbox = $timepicker = "";
		}

		return $date_textbox . $datepicker . $time_textbox . $timepicker;
	}

	public function init_vcl_property($arr)
	{
		$prop = $arr["property"];
		$name = $prop["name"];
		$prop["value"] = (isset($prop["value"]) and is_int($prop["value"])) ? $prop["value"] : $arr["obj_inst"]->prop($name);

		if ($prop["value"] > 0)
		{
			$prop["value"] = array(
				"date" => date("d.m.Y", $prop["value"]),
				"time" => date("H:i", $prop["value"])
			);
		}
		else
		{
			$prop["value"] = array(
				"date" => "",
				"time" => ""
			);
		}

		$prop["value"] = $this->create($prop);
		return array($name => $prop);
	}

	public function process_vcl_property(&$arr)
	{
		$prop =& $arr["prop"];
		$name = $prop["name"];
		$timestamp = self::get_timestamp($prop["value"]);

		if ($timestamp > 1)
		{
			$prop["value"] = $timestamp;
			$arr["obj_inst"]->set_prop($name, $timestamp);
		}
	}

/** Converts datepicker value to UNIX timestamp
	@attrib api=1 params=pos
	@param value type=array
		array("date" => ddmmyyyy, "time" => hh:mm)
	@returns int
**/
	public static function get_timestamp($value)
	{
		$date = isset($value["date"]) ? $value["date"] : "";
		$time = isset($value["time"]) ? $value["time"] : "";
		$day = $month = $year = $hour = $min = 0;

		if (!empty($date))
		{
			list($day, $month, $year) = explode(".", $date, 3);
		}

		if (!empty($time))
		{
			list($hour, $min) = explode(":", $time, 2);
		}

		$timestamp = $year ? mktime((int)$hour, (int)$min, 0, (int)$month, (int)$day, (int)$year) : 0;
		return $timestamp;
	}

	private static function get_js_date_argument($iso_time_string)
	{
		preg_match("/([0-9]{4})-([0-1][0-9])-([0-3][0-9])T([0-2][0-9]):([0-5][0-9]):([0-5][0-9])/U", $iso_time_string, $date);
		$month = awlc_date_en::get_lc_month($date[2]);
		$date_argument_string = "{$month} {$date[3]}, {$date[1]} {$date[4]}:{$date[5]}:{$date[6]}";

		if (7 !== count($date) or !$month or strlen($date_argument_string) < 15)
		{
			throw new awex_datepicker_param("Invalid date string parameter '{$iso_time_string}'");
		}

		return $date_argument_string;
	}
}

class awex_datepicker extends awex_vcl {}
class awex_datepicker_param extends awex_datepicker {}
