<?php

namespace automatweb;
/*
@classinfo syslog_type=ST_ROOM_PRICE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property type type=chooser default=1
	@caption Hinna t&uuml;&uuml;p

	@property recur type=checkbox ch_value=1
	@caption Kordub

	@property active type=checkbox ch_value=1
	@caption Kehtib

	@property date_from type=date_select
	@caption Alates

	@property date_to type=date_select
	@caption Kuni

	@property weekdays type=chooser multiple=1 captionside=top
	@caption N&auml;dalap&auml;evad

	@property apply_groups type=relpicker reltype=RELTYPE_GROUP multiple=1
	@caption Kehtib gruppidele

	@property nr type=select
	@caption Mitmes

	@property time_from type=time_select
	@caption Alates

	@property time_to type=time_select
	@caption Kuni

	@property time type=select editonly=1
	@caption Aeg

	@property bron_made_from type=datetime_select default=-1
	@caption Broneering tehtud alates

	@property bron_made_to type=datetime_select default=-1
	@caption Broneering tehtud kuni

	@property bargain_percent type=textbox
	@caption Soodustuse protsent

	@property prices_props type=callback callback=gen_prices_props
	@caption Hinnad
	
	@property priority type=select
	@caption Prioriteet

@reltype GROUP value=1 clid=CL_GROUP
@caption Kehtib grupile
*/

class room_price extends class_base
{
	const AW_CLID = 1164;

	function room_price()
	{
		$this->init(array(
			"tpldir" => "common/room_price",
			"clid" => CL_ROOM_PRICE
		));
		
		$this->weekdays = array(
			1 => t("E"),
			2 => t("T"),
			3 => t("K"),
			4 => t("N"),
			5 => t("R"),
			6 => t("L"),
			7 => t("P"),
		); 
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "weekdays":
				$prop["options"] = $this->weekdays;
				if(!$prop["value"])
				{
				    $prop["value"] = Array(1,1,1,1,1,1,1,1);
				}
				break;

			case "nr":
				if($arr["obj_inst"]->prop("type") == 2)
				{
					return PROP_IGNORE;
				}
				for($i=1;$i<11;$i++)
				{
					$opts[$i] = $i;
				}
				$prop["options"] = $opts;
				break;

			case "time":
				if($arr["obj_inst"]->prop("type") == 2)
				{
					return PROP_IGNORE;
				}
				$prop["options"] = $this->get_time_selections($arr["obj_inst"]->id());
				break;

			case "type":
				$prop["options"] = array(
					1 => t("Hind"),
					2 => t("Soodushind"),
				);
				if(!$arr["obj_inst"]->prop("type"))
				{
					$prop["value"] = ($arr["request"]["ba"]==1)?2:1;
				}
				break;

			// ignore's for normal price
			case "priority":
				$prop["options"] = array("1" , "2" , "3");
			case "recur":
			case "active":
			case "bargain_percent":
				if($arr["obj_inst"]->prop("type") == 1)
				{
					return PROP_IGNORE;
				}
				break;

			// ignore's for bargain price
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "type":
				$prop["value"] = $prop["value"]?$prop["value"]:$prop["default"];
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_mod_retval($arr)
	{
		if(count($arr["request"]["currency"]))
		{
			
			$this->save_prices($arr["request"]["id"], $arr["request"]["currency"]);
		}
	}

	private function get_room($oid)
	{
		if(!is_oid($oid))
		{
			return false;
		}
		$o = obj($oid);
		$cs = $o->connections_to(array(
			"class_id" => CL_ROOM,
			"reltype" => "RELTYPE_ROOM_PRICE",
		));
		$c = count($cs)?reset($cs):false;
		return $this->can("view", ($c?$c->from()->id():false))?$c->from():"";
	}

	private function get_currencys($oid)
	{
		return ($room = $this->get_room($oid))?$room->prop("currency"):$room;
	}

	function gen_prices_props($arr)
	{
		if($arr["obj_inst"]->prop("type") == 2)
		{
			return PROP_IGNORE;
		}

		$curs = $this->get_currencys($arr["obj_inst"]->id());
		$prices = $this->get_prices($arr["obj_inst"]->id());
		$retval = array();
		foreach($curs as $cur)
		{
			if(!is_oid($cur))
			{
				continue;
			}
			$c = obj($cur);
			$retval["currency[".$cur."]"] = array(
				"name" => "currency[".$cur."]",
				"type" => "textbox",
				"caption" => $c->prop("unit_name"),
				"value" => $prices[$cur],
				"editonly" => 1,
			);
		}
		if(!count($retval))
		{
			$retval["currencys"] = array(
				"name" => "currencys",
				"type" => "text",
				"caption" => t("Valuutad"),
				"value" => t("Hind ei ole seotud &uuml;hegi ruumiga v&otilde;i on valuutad m&auml;&auml;ramata"),
				"editonly" => 1,
			);
		}
		return $retval;
	}

	/**
		@param oid type=oid
			room_price objects oid
		@param prices type=array
			array of prices:
			array(
				CL_CURRENCY object oid => price
			)
	**/
	private function save_prices($oid, $prices)
	{
		if(!is_oid($oid) || !is_array($prices))
		{
			return false;
		}
		$o = obj($oid);
		$o->set_meta("prices", $prices);
		$o->save();
		return true;
	}

	/**
		@comment
			Gets time caption from room object, if room is connected
	**/
	private function get_time_caption($oid)
	{
		$room = $this->get_room($oid);
		if(!$room)
		{
			return $room;
		}
		$room_inst = get_instance(CL_ROOM);
		return $room_inst->unit_step[$room->prop("time_unit")];
	}
	
	/**
		@comment
			Generates array of available options for booking the room
			array(
				nr_of_units => nr_of_units unit_caption,
			)
	**/
	private function get_time_selections($oid)
	{
		$data = $this->get_time_step($oid);
		if(!$data)
		{
			return false;
		}
		$caption = $this->get_time_caption($oid);
		for($i = $data["from"]; $i <= $data["to"]; $i += $data["step"])
		{
			$ret["$i"] = $i." ".$caption;
		}
		return $ret;
	}

	/**
		@comment
			gets time_step information from the room that price is connected to

		@returns
			array(
				from => ,
				to => ,
				step => ,
			)
			.. or false if this price isn't connected to any room
	**/
	private function get_time_step($oid)
	{
		if(!($room = $this->get_room($oid)))
		{
			return $room;
		}
		$ret["from"] = $room->prop("time_from");
		$ret["to"] = $room->prop("time_to");
		if($room->prop("selectbox_time_step") > 0)
		{
			$ret["step"] = $room->prop("selectbox_time_step");
		}
		else
		{
			$ret["step"] = $room->prop("time_step");
		}
		return $ret;
	}

	private function get_prices($oid)
	{
		if(!is_oid($oid))
		{
			return false;
		}
		$o = obj($oid);
		return $o->meta("prices");
	}

	/**
		@attrib api=1 params=name
		@param oids type=array required
			Room_price oid's to use. Be careful with the order of this oids, first ones have higher priority.
			Array(oid_1, oid_2, oid_3, ..)
		@param prices type=array optional
			Prices to use in the price calculation. These prices are per hour!!
			Array(
				room_price_oid_1 => price,
				room_price_oid_2 => price,
				...
			)
			This separate array is used instead of room's price connected to room_price, because not all places use room's price. Some complicated locations use room_price for times, and provide their own price data.
		@param start type=int required
			The time to start calculating from (reservation start for example)
		@param end type=int required
			The time to calculate the price to (reservation end for example)
		@returns
			If param prices is set, the total price, otherwise an array:
			Array(
				room_price_oid => total_seconds_for_given_room_price_object,
				...
			)
		@examples
			for example i have an array of room_price oids and prices + reservation start and end times.
			$a = Array(
				room_price_oid_1(sat, sun) => price(200),
				room_price_oid_2(mon - sun) => price(100),
			)
			$start = UNIX_TIMESTAMP;
			$end = UNIX_TIMESTAMP;
			$room_price_instance = get_instance(CL_ROOM_PRICE);
			$final_total_price = $room_price_instance->calculate_room_prices_price(array(
				"oids" => array_keys($a),
				"prices" => $a,
				"start" => $start,
				"end" => $end,
			));

			Well, now, when for example room_price_oid_1 is set to be active saturday and sunday, and oid_2 is et to be active from monday till sunday and the reservation lasts also from monday till sunday, then from monday to friday second room_price price is used and saturday/sunday use _oid_1's price.
			(2 * 24 * 200) + (5 * 24 * 100) = 9600 + 12 000 = 21 600
			... Or, don't give the prices array and calculate the total price yourself(notice that time for every room_price is given in seconds then).

			

	 **/
	function calculate_room_prices_price($arr)
	{
		if(!$arr["start"] OR !$arr["end"])
		{
			return false;
		}
		$arr["oids"] = safe_array($arr["oids"]);

		$time_db = array(); // this is going to hold the times that have been already so-called reserved.. 
		foreach($arr["oids"] as $oid)
		{
			$obj = obj($oid);
			$list[$obj->id()] = $obj;
			$df = $obj->prop("date_from");
			$dt = $obj->prop("date_to");
			$wd = $obj->prop("weekdays");
			$tf = $obj->prop("time_from");
			$tt = $obj->prop("time_to");
			$tf = mktime($tf["hour"], $tf["minute"], 0, 0, 0, 0);
			$tt = mktime($tt["hour"], $tt["minute"], 0, 0, 0, 0);
			if(date("Ymd",$arr["start"]) > date("Ymd", $df) OR date("Ymd", $arr["end"]) < date("Ymd", $dt))
			{ // mingi osa soovitud ajavahemikust mahub siia ruumi hinda
				$start = (date("Ymd", $arr["start"]) > date("Ymd", $df))?$arr["start"]:$df;
				$end = (date("Ymd", $arr["end"]) < date("Ymd", $dt))?$arr["end"]:$dt;
				for($time = $start; $time < $end; $time += 86400) // loop over each day of that time that matched somewhere in the time we wanted
				{
					$day = date("w", $time);
					if(in_array($day, $wd))  // this day exists in the room price days
					{
						// now we have to match time
						$extra = (date("Hi", $tt) == 0)?true:false; // this helps in situation where i need to replace 00:00 with 24:00
						if(date("Hi",$arr["start"]) > date("Hi", $tf) OR date("Hi", $arr["end"]) < (($extra)?2400:date("Hi", $tt)))
						{
							$time_start = (date("Hi", $arr["start"]) > date("Hi", $tf))?$arr["start"]:$tf;
							$time_end = (date("Hi", $arr["end"]) < ($extra)?2400:date("Hi", $tt))?$arr["end"]:($extra?($tt + 86400):$tt);

							$to_overlap_start = mktime(date("H", $time_start), date("i", $time_start), date("s", $time_start), date("m", $start), date("d", $start), date("Y", $start));
							$to_overlap_end = mktime(date("H", $time_end), date("i", $time_end), date("s", $time_end), date("m", $end), date("d", $end), date("Y", $end));
							if($this->_time_overlap($to_overlap_start, $to_overlap_end, $time_db)) // here we figure out if this time is already being used in this price calculation or not.. , if is but partially, time parameters are changed. if no available time.. false is returned
							{
								$tot_time[$obj->id()] += $time_end - $time_start; // well, here we get the hours for the day
							}
						}
					}
				}
			}
		}
		if(is_array($arr["prices"]) && count($arr["prices"]))
		{
			foreach($tot_time as $room_price => $seconds)
			{
				$hours = ($seconds / 60) / 60 ;
				if($arr["prices"][$room_price])
				{
					$return += $arr["prices"][$room_price] * $hours;
				}
			}
			return $return;
		}
		return $tot_time;

	}

	/** Well, this basically.. dohh, i even better dont describe what it does. It's used internally and just don't touch it unless you know exactly what you are doing.
	 **/
	function _time_overlap(&$start, &$end, &$time_db)
	{
		foreach($time_db as $db_entry)
		{
			if($db_entry["end"] > $start && $db_entry["start"] <= $start) // new time starts before old one has ended (so new gets old's end time as a start time)
			{
				$start = $db_entry["end"];
			}
			if($db_entry["start"] < $end && $db_entry["end"] >= $end) // new time... dzzhhhh... brain malfunction 
			{
				$end = $db_entry["start"];
			}
		}
		if(($end - $start) > 0) // all or at least some time was left to include in the price calculcation -- so add this time to db and return true
		{
			$time_db[] = array(
				"start" => $start,
				"end" => $end,
			);
			return true;
		}
		return false; // this time was already used in calculation, so return false and ignore this
	}
}
?>
