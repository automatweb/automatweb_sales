<?php

class weekdays extends core implements vcl_interface
{
	function __construct()
	{
		$this->init("");
	}

	/**
		@attrib params=name api=1

		@param name required type=string
			String to indetify the object picker

		@param value optional type=int
			Integer between 0 and 127, included. See comment.

		@param multiple optional type=bool default=false
			If true, multiple weekdays can be selected

		@returns string
			The HTML of the date picker.

		@comment Weekdays are assigned 0 to 6 (Sun to Mon). For each weekday selected, add 2^[number of weekday] to the sum. For example if Mon, Wed and Fri are selected, the according sum will be 0^2 + 2^2 + 4^2 = 20.
	**/
	public static function create($arr)
	{
		if (empty($arr["name"]) or !is_string($arr["name"]))
		{
			throw new awex_weekdays_param("Name is required and must be a string.");
		}

		$days = self::days2int(isset($arr["value"]) ? (int) $arr["value"] : 0);

		$html = "";

		for ($i = 0; $i < 7; $i++)
		{
			$label = aw_locale::get_lc_weekday($i, true, true);
			if (!empty($arr["multiple"]))
			{
				$html .= html::checkbox(array(
					"name" => "{$name}[$i]",
					"label" => $label,
					"checked" => !empty($days[$i])
				));
			}
			else
			{
				$html .= html::radiobutton(array(
					"name" => "{$name}",
					"value" => pow(2, $i),
					"label" => $label,
					"checked" => !empty($days[$i])
				));
			}
		}

		return $html;
	}

	public function init_vcl_property($arr)
	{
		$prop = $arr["property"];
		$name = $prop["name"];
		$prop["value"] = (isset($prop["value"]) and is_int($prop["value"])) ? $prop["value"] : $arr["obj_inst"]->prop($name);

		$prop["value"] = $this->create($prop);
		return array($name => $prop);
	}

	public function process_vcl_property(&$arr)
	{
		$prop =& $arr["prop"];

		$arr["obj_inst"]->set_prop($prop["name"], self::days2int($prop["value"]));
	}

	/**	Decodes the integer and returns an array of booleans, true for each weekday selected.
		@attrib api=1 params=pos
		@param value required type=int
			Integer between 0 and 127, included.
		@comment Weekdays are assigned 0 to 6 (Sun to Mon). For each weekday selected, add 2^[number of weekday] to the sum. For example if Mon, Wed and Fri are selected, the according sum will be 0^2 + 2^2 + 4^2 = 20. This is the function to get [true, false, false, true, false, true, false] (representing Sun, Wed and Fri being selected) from 20.
		@returns bool[]
	**/
	public static function int2days($val)
	{
		$days = array();

		for ($i = 6; $i >= 0; $i--)
		{
			if ($val >= pow(2, $i))
			{
				$days[$i] = true;
				$val -= pow(2, $i);
			}
			else
			{
				$days[$i] = false;
			}
		}

		return $days;
	}

	/**	Encodes array of booleans (true for each weekday selected) into an integer.
		@attrib api=1 params=pos
		@param days required type=bool[]
			Array keys (0 to 6) represent days (Sun to Mon).
		@comment Weekdays are assigned 0 to 6 (Sun to Mon). For each weekday selected, add 2^[number of weekday] to the sum. For example if Mon, Wed and Fri are selected, the according sum will be 0^2 + 2^2 + 4^2 = 20. This is the function to get 20 from [true, false, false, true, false, true, false] (representing Sun, Wed and Fri being selected).
		@returns bool[]
	**/
	public static function days2int($days)
	{
		$sum = 0;
		for ($i = 0; $i < 7; $i++)
		{
			if (!empty($days[$i]))
			{
				$sum += pow(2, $i);
			}
		}

		return $sum;
	}
}

class awex_weekdays extends awex_vcl {}
class awex_weekdays_param extends awex_weekdays {}
