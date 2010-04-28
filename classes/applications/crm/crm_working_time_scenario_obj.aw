<?php

namespace automatweb;


class crm_working_time_scenario_obj extends _int_object
{
	const AW_CLID = 1392;

	function set_prop($name,$value)
	{
		parent::set_prop($name,$value);
	}

	function set_weekdays($data)
	{
		$this->set_meta("weekdays" , $data);
	}

	function set_free_times($data)
	{
		$this->set_meta("free_times" , $data);
	}

	function get_weekdays()
	{
		return $this->meta("weekdays");
	}
	
	function get_free_times()
	{
		return $this->meta("free_times");
	}

	function set_time($data)
	{
		$this->set_meta("time" , $data);
	}
	
	function get_time()
	{
		return $this->meta("time");
	}

	function get_scenario_data()
	{
		return $this->meta("scenario_data");
	}
	
	function set_scenario_data($data)
	{
		$this->set_meta("scenario_data" , $data);
	}

	function set_room($room)
	{
		if(is_oid($room) && $this->can("view" , $room))
		{
			$this->room = $room;
		}
	}

	function get_date_options($d)
	{
		$ret = "";
		$sd = $this->get_scenario_data();
		$weekday = date("w" , $d);
		$weekday--;
		if($weekday < 0)
		{
			$weekday = 6;
		}
		//arr(date("w" , $d));
		$room_inst = get_instance(CL_ROOM);
		foreach($sd[$weekday] as $opt)
		{
			$time = mktime($opt["start"]["hour"],$opt["start"]["minute"],0,date("m",$d),date("d",$d),date("Y",$d));
			$na = 0;
			if($this->room)
			{
				$na = !$room_inst->check_if_available(array(
					"room" => $this->room,
					"start" => mktime($opt["start"]["hour"],$opt["start"]["minute"],0,date("m",$d),date("d",$d),date("Y",$d)),
					"end" => mktime($opt["end"]["hour"],$opt["end"]["minute"],0,date("m",$d),date("d",$d),date("Y",$d)),
					"ignore_deadline" => 1,
				));
			}
			$ret.= html::checkbox(array("name" => "bron_times[".$time."][accept]" , "checked" => $na?0:1));
			$ret.= "";
			$ret.= html::time_select(array("name" => "bron_times[".$time."][start]" , "value" => $opt["start"]));
			$ret.= "-";
			$ret.= html::time_select(array("name" => "bron_times[".$time."][end]" , "value" => $opt["end"]));

			if($opt["is_pause"])
			{
				$ret.= html::hidden(array("name" => "bron_times[".$time."][is_pause]" , "value" => 1));
				$ret.= html::hidden(array("name" => "bron_times[".$time."][pause_reason]" , "value" => $opt["pause_reason"]));
				$ret.= $opt["pause_reason"];
			}
			if($na)
			{
				$ret.= t("Sellel ajal on juba broneering");
			}
			$ret.= "\n<br>";
		}
		if($ret)
		{
			return "<table WIDTH=240>".$ret."</table>";
		}

		return $ret;
	}
}

?>
