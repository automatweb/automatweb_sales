<?php

class event_time_obj extends _int_object
{
	const CLID = 1322;

	function set_prop($name,$value)
	{
		switch($name)
		{
			case "event":
				if($value)
				{
					$ev = obj($value);
					$ev->connect(array("to" => $this->id(), "reltype" => "RELTYPE_EVENT_TIME"));
				}
		}
		parent::set_prop($name,$value);
	}

	public function save($check_state = false)
	{
		if($this->prop("event"))
		{
			$event = obj($this->prop("event"));
			$event->set_start_end();
		}
		return parent::save($check_state);
	}

	function prop($k)
	{
		if(is_oid(parent::id()) && ($k == "location" || substr($k, 0, 9) == "location.") && !is_oid(parent::prop("location")) && $this->can("view", parent::prop("event")) && is_oid(parent::prop("event")))
		{
			// If the location is always the same, there's no need to copy it into event_time object.
			return obj(parent::prop("event"))->prop($k);
		}

		return parent::prop($k);
	}

	function get_locations()
	{
		$ol = new object_list(array(
			"class_id" => CL_CALENDAR_EVENT,
			"lang_id" => array(),
			"CL_CALENDAR_EVENT.RELTYPE_EVENT_TIMES" => $this->id(),
		));
		$o = reset($ol->arr());
		if(is_object($o))
		{
			return $o->get_locations();
		}
		else
		{
			return array();
		}
	}
}

?>
