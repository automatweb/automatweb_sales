<?php

class aw_event_manager
{
	public $event_handlers = array();

	/**
		@attrib api=1 params=pos
		@param event type=aw_event
		@comment
		@returns
		@errors
	**/
	public static function call_registered_handlers(aw_event $event)
	{
		$event_class = get_class($event);
		if (isset(self::$event_handlers[$event_class]))
		{
			foreach (self::$event_handlers[$event_class] as $key => $value)
			{
			}
		}
	}

}


// registered events:
//TODO: teha iga eventi tekitava klassi jaoks eraldi registreerimisfail, mis laetakse ainult koos klassi endaga? muuta formaati, ...

aw_event_manager::$event_handlers[""][] = array(
	"class" =>
);
