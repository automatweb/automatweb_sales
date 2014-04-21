<?php
 
class openhours_obj extends _int_object
{
	const CLID = 1014;
	
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 4;
	const THURSDAY = 8;
	const FRIDAY = 16;
	const SATURDAY = 32;
	const SUNDAY = 64;

	public function awobj_set_days($days) {
		$value = 0;
		$days = (array)$days;
		if (in_array("E", $days)) {
			$value += self::MONDAY;
		}
		if (in_array("T", $days)) {
			$value += self::TUESDAY;
		}
		if (in_array("K", $days)) {
			$value += self::WEDNESDAY;
		}
		if (in_array("N", $days)) {
			$value += self::THURSDAY;
		}
		if (in_array("R", $days)) {
			$value += self::FRIDAY;
		}
		if (in_array("L", $days)) {
			$value += self::SATURDAY;
		}
		if (in_array("P", $days)) {
			$value += self::SUNDAY;
		}
		return parent::set_prop("days", $value);
	}

	public function awobj_get_days() {
		$value = parent::prop("days");
		$days = array();
		if ($value & self::MONDAY) {
			$days[] = "E";
		}
		if ($value & self::TUESDAY) {
			$days[] = "T";
		}
		if ($value & self::WEDNESDAY) {
			$days[] = "K";
		}
		if ($value & self::THURSDAY) {
			$days[] = "N";
		}
		if ($value & self::FRIDAY) {
			$days[] = "R";
		}
		if ($value & self::SATURDAY) {
			$days[] = "L";
		}
		if ($value & self::SUNDAY) {
			$days[] = "P";
		}
		return $days;
	}
	
	public function awobj_set_open($value) {
		list($hour, $minute) = explode(":", $value . ":00");
		return parent::set_prop("open", mktime($hour, $minute));
	}
	
	public function awobj_set_close($value) {
		list($hour, $minute) = explode(":", $value . ":00");
		return parent::set_prop("close", mktime($hour, $minute));
	}
	
	public function awobj_get_open() {
		return date("H:i", parent::prop("open"));
	}
	
	public function awobj_get_close() {
		return date("H:i", parent::prop("close"));
	}
	
	/**	Returns the the object in JSON
		@attrib api=1
	**/
	public function json($encode = true) {
		return array("id" => $this->id(),
					 "days" => $this->awobj_get_days(),
					 "open" => $this->awobj_get_open(),
					 "close" => $this->awobj_get_close(),
					 "valid_from" => $this->prop("valid_from"),
					 "valid_to" => $this->prop("valid_to"));
	}
}
