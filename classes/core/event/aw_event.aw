<?php

class aw_event
{
	public function __construct()
	{
	}

	/**
		@attrib api=1 params=pos
		@comment
		@returns
		@errors
	**/
	final public function trigger()
	{
		aw_event_manager::call_registered_handlers($this);
	}
}
