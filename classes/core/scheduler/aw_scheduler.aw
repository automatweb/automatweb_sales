<?php

/**
		The scheduler can be used to schedule events to happen at certain times.
		An event is an orb function call that will get called at the time specified.
		You can specify a certain time or a recurrence interval when the event will take place.
**/
class aw_scheduler extends aw_core_module
{
	/**
		@attrib api=1 params=pos
		@comment
		@returns void
		@errors
	**/
	public static function run()
	{
	}

	/**
		@attrib api=1 params=pos
		@param id type=int
		@param time type=array|int
			array(
				"minute" => integer,
				"hour" => integer,
				"weekday" => integer (0 for sunday),
				"day" => integer,
				"month" => integer,
				"year" => integer (four digit)
			)
		@comment
		@returns void
		@errors
	**/
	public static function add_task($id, $time)
	{
	}

	/**
		@attrib api=1 params=pos
		@param id type=int
		@param time type=array|int
			array(
				"minute" => integer,
				"hour" => integer,
				"weekday" => integer (0 for sunday),
				"day" => integer,
				"month" => integer,
				"year" => integer (four digit)
			)
		@comment
		@returns void
		@errors
	**/
	public static function edit_task($id, $time)
	{
	}

	/**
		@attrib api=1 params=pos
		@param id type=int
		@comment
		@returns void
		@errors
	**/
	public static function remove_task($id)
	{
	}

}
