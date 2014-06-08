<?php

class planner_obj extends _int_object
{
	const CLID = 126;
	
	private static $default_day_start = "08:00";
	private static $default_day_end = "18:00";
	
	public function awobj_get_day_start() {
		$val = parent::prop("day_start");
		return $val ? $val : timepicker::get_timestamp(self::$default_day_start);
	}
	
	public function awobj_get_day_end() {
		$val = parent::prop("day_end");
		return $val ? $val : timepicker::get_timestamp(self::$default_day_end);
	}
}
