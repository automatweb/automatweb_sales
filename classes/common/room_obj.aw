<?php
 
class room_obj extends _int_object
{
	const CLID = 1162;

	/** Returns the color for the given setting, based on the current settings
		@attrib api=1 params=pos

		@param var required type=string
			The setting to return the value for. 

	**/
	function get_color($var)
	{
		$default = null;
		switch($var)
		{
			case "available":
				$default = "#E1E1E1";
			default:
				if($color = $this->get_setting("col_".$var))
				{
					return "#".$color;
				}
				else
				{
					return $default;
				}
		}
	}

	/** Returns the current active settings for the room
		@attrib api=1 

		@returns
			The cl_room_settings object active for the current user or null if none found
	**/
	function get_settings()
	{
		enter_function("room::get_settings_for_room");
		$si = get_instance(CL_ROOM_SETTINGS);
		$rv = $si->get_current_settings($this);
		exit_function("room::get_settings_for_room");
		return $rv;
	}

	/** Returns a setting from the current active room settings
		@attrib api=1 params=pos

		@param setting required type=string
			A setting property name from the room_settings class

		@returns
			The value for the setting in the currently active settings or "" if no settings are active
	**/
	function get_setting($setting)
	{
		if(!is_object($this->settings))
		{
			$this->settings = $this->get_settings();
		}
		if(!is_object($this->settings))
		{
			return "";
		}
		if(!$this->settings->is_property($setting))
		{
			return "";
		}
		return $this->settings->prop($setting);
	}

	/** Returns a setting from the current active room settings
		@attrib api=1 params=pos

		@param setting required type=string
			A setting property name from the room_settings class

		@returns
			The value for the setting in the currently active settings or "" if no settings are active
	**/
	function get_group_setting($setting)
	{
		if(!$this->load_settings())
		{
			return "";
		}
		$grp_settings = $this->settings->meta("grp_settings");
		$gl = aw_global_get("gidlist_pri_oid");
		asort($gl);
		$gl = array_keys($gl);
		$grp = $gl[1];
		if (count($gl) == 1)
		{
			$grp = $gl[0];
		}
		if (is_array($grp_settings) && $grp_settings[$grp][$setting])
		{
			return $grp_settings[$grp][$setting];
		}
		return "";
	}

	private function load_settings()
	{
		if(!is_object($this->settings))
		{
			$this->settings = $this->get_settings();
		}
		if(!is_object($this->settings))
		{
			return "";
		}
		return 1;
	}

	/** Returns the current workers for the room
		@attrib api=1 
		@returns
			array(person id => person name)
	**/
	function get_all_workers()
	{
		$pro = array();
		if(is_array($this->prop("professions")))
		{
			$pro = $this->prop("professions");
		}
		
		if(!sizeof($pro))
		{
			return array();
		}

		$ol2 = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_RANK" => $pro,
					"oid" => $pro,
				)
			)),
		));
		return $ol2->names();
	}

	/** Returns the current sellers for the room
		@attrib api=1 
		@returns
			array(person id => person name)
	**/
	function get_all_sellers()
	{
		$pro = array();
		if(is_array($this->prop("seller_professions")))
		{
			$pro = $this->prop("seller_professions");
		}
		if(!sizeof($pro))
		{
			return array();
		}

		$ol2 = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_RANK" => $pro,
					"oid" => $pro,
				)
			)),
		));
		return $ol2->names();
	}

	/** removes reservations without customer
		@attrib api=1 params=name
		@param start required
			person id
		@param end required
			person id
		@returns boolean
	**/
	function remove_reservations_without_customer($arr)
	{
		$res = $this->get_reservations($arr);
		foreach($res->arr() as $object)
		{
			if(!$object->prop("customer"))
			{
				$object->delete();
			}
		}
		return 1;
	}

	/** extends person current work graph
		@attrib api=1 params=name
		@param person required type=oid
			person id
		@param start required
			person id
		@param end required
			person id
		@returns boolean
			true if success
	**/
	function extend_work_graph($arr)
	{
		$start = date_edit::get_timestamp($arr["start"]);
		$end = date_edit::get_timestamp($arr["end"]) + 3600 * 24;//kaasaarvatud viimane p2ev
		$person = $arr["person"];

		$this->remove_reservations_without_customer(array(
			"start" => $start,
			"end" => $end,
		));

		$last_reservation = $this->get_last_reservation($start);
		if(is_oid($last_reservation))
		{
			$r = obj($last_reservation);
			$last_end = $r->prop("end");
			$patterns = $this->get_reservations(array(
				"start" => $last_end - 7*24*3600 ,
				 "end" => $last_end
			));
			$p = array();
			foreach($patterns->arr() as $pat)
			{
				$p[date("N" , $pat->prop("start1"))][] = array(
					"start" => array(
						"hour" => date("H" , $pat->prop("start1")),
						"minute" => date("i" , $pat->prop("start1")),
						"second" => date("s" , $pat->prop("start1")),
					),
					"end" => array(
						"hour" => date("H" , $pat->prop("end")),
						"minute" => date("i" , $pat->prop("end")),
						"second" => date("s" , $pat->prop("end")),
					),
				);
			}
			while($start < $end)
			{
				$ex_res = $this->get_reservations(array(
					"start" => get_day_start($start),
					"end" => (get_day_start($start) + 24*3600),
				));
				if(!sizeof($ex_res->ids()))
				{
					foreach($p[date("N" , $start)] as $data)
					{
						$res = $this->add_reservation(array(
							"start" => mktime($data["start"]["hour"] , $data["start"]["minute"],$data["start"]["second"],date("m" , $start) , date("d" , $start), date("Y" , $start)),
							"end" => mktime($data["end"]["hour"] , $data["end"]["minute"],$data["end"]["second"],date("m" , $start) , date("d" , $start), date("Y" , $start)),
						));


						if(is_oid($res))
						{
							$reservation = obj($res);
							$reservation->set_prop("people", $person);
							$reservation->save();
						}


					//	print "broneering ".date("d.m.Y" , $start)." kell: ".$data["start"]["hour"].":".$data["start"]["minute"].":".$data["start"]["second"]." kuni ".$data["end"]["hour"].":".$data["end"]["minute"].":".$data["end"]["second"]."<br>";
					}
				}
				$start = $start + 24*3600;
			}
		}
//		die();
	}

	/** returns room last reservation oid
		@attrib api=1
		@param time optional type=int
		@returns oid
	**/
	function get_last_reservation($time = null)
	{
		$filter = array(
			"class_id" => CL_RESERVATION,
			"lang_id" => array(),
			"resource" => $this->id(),
			"limit" => 1,
			"sort_by" => "planner.end DESC",
//			"end" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
		);
		if($time)
		{
			$filter["end"] = new obj_predicate_compare(OBJ_COMP_LESS, $time);
		}
		$ol = new object_list($filter);
		
		return reset($ol->ids());
	}

	/** returns extra reservation / other type
		@attrib api=1 params=pos
		@param start required type=int
			start timestamp
		@param end required type=int
			end timestamp
	**/
	public function get_extra_res($start , $end)
	{
		$filter = array(
			"class_id" => CL_RESERVATION,
			"lang_id" => array(),
			"resource" => $this->id(),
			"limit" => 1,
			"type" => "%food%",
		);
		$filter["end"] = new obj_predicate_compare(OBJ_COMP_GREATER, $start);
		$filter["start1"] = new obj_predicate_compare(OBJ_COMP_LESS, $end);
		$ol = new object_list($filter);
		return reset($ol->arr());

	}

	/** returns extra reservations / other type
		@attrib api=1 params=pos
		@param start required type=int
			start timestamp
		@param end required type=int
			end timestamp
	**/
	public function get_extra_reservations($start , $end)
	{
		$filter = array(
			"class_id" => CL_RESERVATION,
			"lang_id" => array(),
			"resource" => $this->id(),
//			"limit" => 1,
			"type" => "%food%",
		);
		$filter["end"] = new obj_predicate_compare(OBJ_COMP_GREATER, $start);
		$filter["start1"] = new obj_predicate_compare(OBJ_COMP_LESS, $end);
		$ol = new object_list($filter);//arr($ol); arr($filter); arr(date("d.m.Y h:i" , $start)); arr(date("d.m.Y h:i" , $end));
		return $ol;

	}

	/** returns reservation list
		@attrib api=1
		@param start optional type=int
			start timestamp
		@param end required type=int
			end timestamp
		@param worker optional
			person id
		@param active optional type=bool
			shows only verified or just made reservations
		@param type optional type=string
			reservation type
		@returns object list
	**/
	function get_reservations($arr = array())
	{
		$filter = array(
			"class_id" => CL_RESERVATION,
			"lang_id" => array(),
			"resource" => $this->id(),
		);
		if($arr["start"])
		{
			$filter["end"] = new obj_predicate_compare(OBJ_COMP_GREATER, $arr["start"]);
		}
		if($arr["end"])
		{
			$filter["start1"] = new obj_predicate_compare(OBJ_COMP_LESS, $arr["end"]);
		}
		if($arr["end"] || $arr["start"])
		{
			$filter["sort_by"] = "planner.end DESC"; // kui seda planneri tabelit sisse ei loeta, siis ei hakka jamama
		}
		if($arr["worker"])
		{
			$filter["people"] = $arr["worker"];
		}
		if($arr["type"])
		{
			$filter["type"] = $arr["type"];
		}
		if($arr["active"])
		{
			$filter["verified"] = 1;
		}
		$ol = new object_list($filter);
		return $ol;
	}

	/** returns one day reservation list
		@attrib api=1 params=pos
		@param time optional type=int
			timestamp
		@param end optional type=int
			timestamp
		@param active optional type=bool
			shows only verified reservations
		@returns object list
	**/
	function get_day_reservations($time, $end=null,  $act = 0)
	{
		$arr = array();
		$arr["start"] = mktime(date("h" , $time), date("i" , $time), 0, date("m" , $time), date("d" , $time), date("Y" , $time));
		if(!$end)
		{
			$arr["end"] = mktime(0, 0, 0, date("m" , $time), (date("d" , $time)+1), date("Y" , $time));
		}
		else
		{
			$arr["end"] = mktime(date("h" , $end), date("i" , $end), 0, date("m" , $end), date("d" , $end), date("Y" , $end));
		}
		if($act)
		{
			$arr["active"] =1;
		}
		return $this->get_reservations($arr);
	}

	/** returns one day reservation sum
		@attrib api=1 params=pos
		@param time optional type=int
			start timestamp
		@returns array
			summ in different currencys
	**/
	function get_day_sum($time)
	{
		$reserv = $this->get_day_reservations($time);
		$sum = array();
		foreach($reserv->arr() as $r)
		{
			$rs = $r->get_sum();
			foreach($rs as $key => $val)
			{
				$sum[$key]+=$val;
			}
		}
		return $sum;
	}

	function get_person_day_sum($time,$worker)
	{
		$arr = array();
		$arr["start"] = mktime(0, 0, 0, date("m" , $time), date("d" , $time), date("Y" , $time));
		$arr["end"] = mktime(0, 0, 0, date("m" , $time), (date("d" , $time)+1), date("Y" , $time));
		$arr["worker"] = $worker;
 		$reserv = $this->get_reservations($arr);
		$sum = array();
		foreach($reserv->arr() as $r)
		{
			$rs = $r->get_sum();
			foreach($rs as $key => $val)
			{
				$sum[$key]+=$val;
			}
		}
		return $sum;
	}

	/** adds reservation
		@attrib api=1
		@param start required type=int
			start timestamp
		@param end required type=int
			end timestamp
		@returns oid
			reservation oid
	**/
	function add_reservation($arr = array())
	{
		$start = (int)$arr["start"];
		$end = (int)$arr["end"];
		if(!$this->is_available(array(
			"start" => $start,
			"end" => $end,
		)))
		{
			return "";
		}
		if(is_object($this->get_first_obj_by_reltype("RELTYPE_CALENDAR")))
		{
			$cal_obj = $this->get_first_obj_by_reltype("RELTYPE_CALENDAR");
			$cal = $cal_obj->id();
			$parent = $cal_obj->prop("event_folder");
			if (!$parent)
			{
				$parent = $cal_obj->id();
			}
		}
		else
		{
			$parent = $this->id();
		}
		$reservation = new object();
		$reservation->set_class_id(CL_RESERVATION);
		$reservation->set_name($this->name()." bron ".date("d:m:Y" ,$start));
		$reservation->set_parent($parent);
		$reservation->set_prop("deadline", (time() + 15*60));
		$reservation->set_prop("resource" , $this->id());
		$reservation->set_prop("start1" , $start);
		$reservation->set_prop("end" , $end);
		$reservation->save();
		return $reservation->id();
	}

	//selle funktsionaalsuse peaks kunagi siia sisse t6stma
	/** checks if the room is available 
		@attrib params=name api=1
		@param start required type=int
		@param end required type=int
		@param ignore_booking optional type=int
			If given, the booking with this id will be ignored in the checking - this can be used for changing booking times for instance
		@return boolean
			true if available
			false if not available
	**/
	public function is_available($arr)
	{
		if($this->prop("allow_multiple"))
		{
			return true;
		}
		$arr["room"] = $this->id();
		$room_inst = get_instance(CL_ROOM);

		if(is_oid($arr["ignore_booking"]))
		{
			$to_ignore = obj($arr["ignore_booking"]);
			//$arr["type"] = $to_ignore->prop("type");
		}

		$ret = $room_inst->check_if_available($arr);
		//ruumi instantsist saab kinnise aja ka siis kui on peale pandud et n2idata kalendris kinnitamata vaateid
		//global $this->last_bron_id;
		//$last_bron_id = $room_inst->last_bron_id;
		if(!$ret && is_oid($room_inst->last_bron_id))
		{
			$bron = obj($room_inst->last_bron_id);
			if($bron->is_dead())
			{
				$ret = 1;
			}
		}
		//arr($this->last_bron_id); arr($room_inst->last_bron_id);
		$GLOBALS["last_bron_id"] = $room_inst->last_bron_id;
		return $ret;
	}

	public function has_extra_row($start , $end)
	{
		$filter = array(
			"class_id" => CL_RESERVATION,
			"site_id" => array(),
			"lang_id" => array(),
			"type" => "%food%",
			"start1" => new obj_predicate_compare(OBJ_COMP_LESS, $end),
			"end" => new obj_predicate_compare(OBJ_COMP_GREATER, $start),
			"limit" => 1,
			"resource" => $this->id(),
		);
		$ol = new object_list($filter);
		if(sizeof($ol->ids()))
		{
			return 1;
		}
		return 0;
	}

	/** Returns room's resouces
		@attrib api=1
		@returns
			Array of room's resources.
			array(
				resource_oid => resource_obj
			)
	 **/
	function get_resources()
	{
		$i = $this->instance();
		return $i->get_room_resources($this->id());
	}

	//leiab p2eva reserveeringud massiivi...sorteeritult jne
	private function get_day_res_data($time,$to,$active)
	{
		if($this->day_reservations[get_day_start($time)])
		{
			return $this->day_reservations[get_day_start($time)];
		}
		$reservations = $this->get_day_reservations($time, $to, $active);
		$data = array();
		foreach($reservations->arr() as $key =>  $res)
		{
			if(!$res->prop("time_closed"))
			{
				$data[$res->id()] = array("start" => $res->prop("start1") , "end" => $res->prop("end") , "id" => $res->id());
			}
		}
		uasort($data, array(&$this, "do_res_sort"));
		$max = 0;

		$this->day_reservations[get_day_start($time)] = $data;//j2tab selle krpi meelde, et hiljem kasutada

		return $this->day_reservations[get_day_start($time)];
	}

	private function do_res_sort($a, $b)
	{
		return $b["start"] - $a["start"];
	}

	/** Returns number of teservations at the same time
		@attrib api=1
		@param start required type=int
			start time
		@param end optional type=int
			end time
		@returns int
			max reservations at the same time
	 **/
	function get_max_reservations_atst($time,$to,$active)
	{
		$data = $this->get_day_res_data($time,$to,$active);

		//teeb massiivi mille elemendil on kirjas aeg ja see kas on l6pp v6i algus
		//sorteerib aja j2rgi 2ra ja liidab alguseid ja lahutab l6ppe, mis iganes maksimum summa tuleb ongi tulemus

		$d = array();
		foreach($data as $dat)
		{
			$d[] = array(
				"start" => 1,
				"time" =>  $dat["start"],
			);
			$d[] = array(
				"start" => 0,
				"time" =>  $dat["end"],
			);
		}

		uasort($d, array(&$this, "max_brons_atst_sort"));

		$count = 0;
		$max = 0;
		foreach($d as $element)
		{
			if($element["start"])
			{
				$count++;
			}
			else
			{
				$count--;
			}
			if($count > $max)
			{
				$max = $count;
			}
		}
	
		return $max;

/*
		foreach($data as $key => $dat)
		{
			$this_max = 1;
			foreach($data as $key2 => $c_data)
			{
				if($key != $key2 && $dat["start"] < $c_data["end"] && $dat["end"]  > $c_data["start"])
				{
					$this_max++;
				}
			}
			if($this_max > $max)
			{
				$max = $this_max;
			}
		}
		return $max;*/
	}
	
	private function max_brons_atst_sort($a, $b)
	{
		if($a["time"] == $b["time"])
		{
			return $a["start"] - $b["start"];//l6puajad j2rjekorras tahapoole
		}
		return $a["time"] - $b["time"];
	}

	//kaustamata tulba leidmiseks
	private function get_unused_column()
	{
		$x = 0;
		while($x < 100)//yle selle vast ei l2he, ehh
		{
			if(!$this->used_columns[$x])
			{
				$this->used_columns[$x] = 1;
				return $x;
			}
			$x++;
		}
	}

	function get_time_reservations($start, $end)
	{
		$data = $this->get_day_res_data($start, $end, 1);
		$res = array();
		//suht keeruline osa nyyd see, et aru saaks mitmendasse tulpa on ta enne l2inud
		//korjab need variandid kokku, kuhu juba on m6ni m2rgitud
		$this->used_columns = array();
		foreach($data as $dat)
		{
			if($dat["end"] > $start && $end > $dat["start"])
			{
				$this->used_columns[$dat["col"]] = 1;
			}
		}

		foreach($data as $dat)
		{
			if($dat["end"] > $start && $end > $dat["start"])
			{
				if(!isset($this->day_reservations[get_day_start($start)][$dat["id"]]["col"]))
				{
					$dat["col"] = $this->day_reservations[get_day_start($start)][$dat["id"]]["col"] = $this->get_unused_column();
				}
				$res[] = $dat;
			}
		}
		return $res;
	}

	/**
		@attrib api=1
		@returns array
	 **/
	function get_other_rooms_selection()
	{
		$st = $this->get_settings();
		$res = array();
		if($st)
		{
			foreach($st->connections_from(array(
				"type" => "RELTYPE_RELATED_ROOMS",
			)) as $c)
			{
				$res[$c->prop("to")] = $c->prop("to.name");
			}
		}
		return $res;
	}

	/**
		@attrib api=1 params=pos
		@param start optional type=int
			start time
		@param end optional type=int
			end time
		@returns array
			array(start => .. , end =>)
	 **/
	function get_calendar_visible_time($start = null , $end = null)
	{
		$s = 3600*48;
		$e = 0;	
		if($ohs = $this->get_current_openhours($start , $end))
		{
			foreach($ohs as $oh)
			{
				foreach($oh->meta("openhours") as $data)
				{
					if($data["h1"]*3600 + $data["m1"]*60 < $s)
					{
						$s = $data["h1"]*3600 + $data["m1"]*60;
					}
					if($data["h2"]*3600 + $data["m2"] > $e)
					{
						$e = $data["h2"]*3600 + $data["m2"]*60;
					}
					if(($data["h1"]*3600 + $data["m1"]*60) > ($data["h2"]*3600 + $data["m2"]*60) && (($data["h2"]+24)*3600 + $data["m2"]*60) > $e)
					{
						$e = ($data["h2"]+24)*3600 + $data["m2"]*60;
					}
				}
			}
		}
		else
		{
			$s = 0;
			$e = 24*3600;
		}
		return array(
			"start" => $s,
			"end" =>  $e,
		);
	}

	/** Returns the openhours object for the current user, or null if none applies
		@attrib api=1 params=pos
		@param start optional type=int default=time()
			start time
		@param end optional type=int default=time()
			end time
		@returns Array
	**/
	function get_current_openhours($start = 0 , $end = 0)
	{
		$rv = array();
		$gl = aw_global_get("gidlist_oid");
		if(is_oid($this->prop("inherit_oh_from")) && $this->can("view" , $this->prop("inherit_oh_from")))
		{
			$room = obj($this->prop("inherit_oh_from"));
		}
		else
		{
			$room = $this;
		}
		if(!$start)
		{
			$start = time();
		}
		if(!$end)
		{
			$start = time();
		}
		foreach($room->connections_from(array("type" => "RELTYPE_OPENHOURS")) as $c)
		{
			$oh = $c->to();
			if ($oh->prop("date_from") > 100 && $end < $oh->prop("date_from"))
			{
				continue;
			}
			if ($oh->prop("date_to") > 100 && $start > $oh->prop("date_to"))
			{
				continue;
			}
			if (!is_array($oh->prop("apply_group")) || !count($oh->prop("apply_group")) || count(array_intersect($gl, safe_array($oh->prop("apply_group")))))
			{
				$rv[] = $oh;
			}
		}
		return count($rv) ? $rv : null;
	}

	/** Returns available currency object list for the room
		@attrib api=1
		@returns object list
	**/
	public function get_currency_ol()
	{
		$curs = $this->prop("currency");
		$ol = new object_list();
		$ol -> add($curs);
		$ol->sort_by(array(
			"prop" => "name",
			"order" => "desc"
		));
		return $ol;
	}

	/** Creates new room with same parameters and connections, and name ++;
		@attrib api=1
	**/
	public function create_copy()
	{
		$connections = $this->connections_from();
		$new = $this->save_new();
		$no = obj($new);
		$name = $this->name();
		$number = "";
		$x = strlen($name)-1;
		while($x > 0)
		{
			if(is_numeric($name[$x]))
			{
				$number = $name[$x].$number;
				$name = substr($name , 0 , $x);
			}
			else
			{
				break;
			}
			$x--;
		}
		$n = (int)$number;
//arr($name.($number+1));
		$no->set_name($name.($number+1));
		$no->save();
		foreach($connections  as $c)
		{
			if(!$no->is_connected_to(array("type" => $c->prop("reltype"), "to" => $c->prop("to"))))
			{
				$no->connect(array(
					"type" => $c->prop("reltype"),
					"to" => $c->prop("to")
				));
			}
		}
		return $no->id();
	}

}

?>
