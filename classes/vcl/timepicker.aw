<?php

class timepicker extends core implements vcl_interface
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

		@param disabled type=bool default=false
			Element is disabled/not disabled

		@returns string
			The HTML of the date picker.
	**/
	public static function create($arr)
	{
		if (empty($arr["name"]) or !is_string($arr["name"]))
		{
			throw new awex_timepicker_param("Name is required and must be a string.");
		}

		load_javascript("jquery/plugins/ptTimeSelect/jquery.ptTimeSelect.js");

		$time_textbox = html::textbox(array(
			"name" => $arr["name"],
			"value" => isset($arr["value"]) ? self::get_time($arr["value"]) : "",
			"disabled" => isset($arr["disabled"]) ? $arr["disabled"] : false,
			"size" => 3,
			"textsize" => !empty($arr["textsize"]) ? $arr["textsize"] : null
		));
		$timepicker = <<<EOS
<script type="text/javascript">
$("input[name='{$arr["name"]}']").ptTimeSelect();
</script>
EOS;
		return $time_textbox . $timepicker;
	}

	public function init_vcl_property($arr)
	{
		$prop = $arr["property"];
		$name = $prop["name"];
		$prop["value"] = $arr["obj_inst"]->prop($name);
		$prop["value"] = $this->create($prop);
		return array($prop["name"] => $prop);
	}

	public function process_vcl_property(&$arr)
	{
		$timestamp = self::get_timestamp($arr["prop"]["value"]);

		$arr["prop"]["value"] = $timestamp;
		$arr["obj_inst"]->set_prop($arr["prop"]["name"], $timestamp);
	}

	/** Converts timepicker value to UNIX timestamp, setting date to 1/1/1970
		@attrib api=1 params=pos
		@param value type=string
			hh:mm
		@returns int
	**/
	public static function get_timestamp($value)
	{
		if (strpos($value, ":"))
		{
			list($h, $m) = explode(":", $value);
			return mktime($h, $m, 0, 1, 1, 1970);
		}
		else
		{
			return null;
		}
	}

	/** Converts UNIX timestamp to timepicker value (hh:mm)
		@attrib api=1 params=pos
		@param value type=int
			UNIX timestamp
		@returns int
	**/
	public static function get_time($timestamp)
	{
		return is_numeric($timestamp) ? date("H:i", $timestamp) : "";
	}
}

class awex_timepicker extends awex_vcl {}
class awex_timepicker_param extends awex_timepicker {}
