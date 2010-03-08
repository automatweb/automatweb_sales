<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/calendar/conference.aw,v 1.10 2007/12/06 14:32:55 kristo Exp $
// conference.aw - Konverents 
/*

@classinfo syslog_type=ST_CONFERENCE relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=tarvo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

	@property topic_comment type=textarea cols=50 rows=6
	@caption L&uuml;hikirjeldus/teema

	@property type type=select
	@caption T&uuml;&uuml;p

	@property organizers type=relpicker reltype=RELTYPE_ORGANIZER multiple=1
	@caption Korraldajad

	@property webform type=relpicker reltype=RELTYPE_WEBFORM
	@caption Veebivorm

	@property start_time type=datetime_select
	@caption Algusaeg

	@property end_time type=datetime_select
	@caption L&otilde;puaeg

	@property place type=relpicker reltype=RELTYPE_LOCATION
	@caption Toimumiskoht

	@property conference_plan type=textarea cols=50 rows=6
	@caption Konverentsi kava

	@property conference_plan_file type=relpicker reltype=RELTYPE_FILE
	@caption Konverenrtsikava failina

	@property extra_info type=textarea cols=50 rows=6
	@caption Lisainfo

# TAB ESINEJAD
@groupinfo presenters caption="Esinejad"
@default group=presenters

	@property presenters_tb type=toolbar no_caption=1
	@property presenters_tbl type=table no_caption=1

# TAB OSALEJAD
@groupinfo participants caption="Osalejad"
@default group=participants

	@property participants type=releditor mode=manager reltype=RELTYPE_PARTICIPANT props=firstname,lastname table_fields=firstname,lastname

# TAB RESSURSID
@groupinfo resources caption="Ressursid"

	@groupinfo rooms caption="Ruumid" parent=resources
	@default group=rooms

		@property rooms_tb type=toolbar no_caption=1
		@property rooms_tbl type=table no_caption=1
		@property room_search_results store=no type=hidden no_caption=1

	@groupinfo room_resources caption="Ruumide ressursid" parent=resources
	@default group=room_resources

		@property room_resources type=table no_caption=1
		@caption Ruumide ressursid

	@groupinfo catering_resources caption="Toitlustuse ressursid" parent=resources
	@default group=catering_resources

		@property catering_resources_tbl type=table no_caption=1
		@caption Toitlustuse ressursid

	@groupinfo other_resources caption="Teised ressursid" parent=resources
	@default group=other_resources

		@property accommondation type=textarea cols=50 rows=6
		@caption Majutus

		@property transport type=textarea cols=50 rows=6
		@caption Transport

# TAB SPONSORID
@groupinfo sponsors caption="Sponsorid"
@default group=sponsors

	@property sponsors type=releditor mode=manager reltype=RELTYPE_SPONSOR props=name table_fields=name


@reltype ORGANIZER value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Korraldaja

@reltype LOCATION value=2 clid=CL_LOCATION
@caption Toimumiskoht

@reltype PRESENTER value=3 clid=CL_CRM_PERSON
@caption Esineja

@reltype PARTICIPANT value=4 clid=CL_CRM_PERSON
@caption Osaleja

@reltype SPONSOR value=5 clid=CL_CRM_PERSON,CL_CRM_COMPANY
@caption Sponsor

@reltype WEBFORM value=6 clid=CL_WEBFORM
caption Veebivorm

@reltype RESERVATION value=7 clid=CL_RESERVATION
@caption Ruumi reservatsioon

@reltype FILE value=8 clid=CL_FILE
@caption Konverentsikava
*/

class conference extends class_base
{
	function conference()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/conference",
			"clid" => CL_CONFERENCE
		));

		$this->conference_types = array(
			1 => t("Meeting"),
			2 => t("Seminar"),
			3 => t("Conference"),
			4 => t("Training"),
			5 => t("Product introduction / lunch"),
			6 => t("Info"),
			7 => t("Breakfast"),
			8 => t("Luncheon"),
			9 => t("Dinner"),
			10 => t("Party"),
			11 => t("Wedding"),
			12 => t("Other"),
		);

		$this->additional_conference_types = array(
			1 => t("Break out rooms"),
			2 => t("Show room"),
			3 => t("Office"),
			4 => t("Dinner"),
			5 => t("Other"),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- get_property --//
			case "type":
				$prop["options"] = $this->conference_types();
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			//-- set_property --//
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}
	
	function callback_mod_retval($arr)
	{
		if($arr["request"]["reservations"])
		{
			$this->reserve_room_resources($arr["request"]["reservations"], $arr["args"]["id"]);
		}

		if(is_array($arr["request"]["reservation_times"]))
		{
			$this->_update_reservation_times($arr["request"]["reservation_times"]);
		}

		if(strlen($arr["request"]["room_search_results"]))
		{
			$spl = split(",", $arr["request"]["room_search_results"]);
			$this->_create_new_reservations(array(
				"conference" => $arr["args"]["id"],
				"rooms" => $spl,
			));
		}

		if(is_array($arr["request"]["topic"]))
		{
			$this->set_presenter_topics(array(
				"id" => $arr["args"]["id"],
				"topics" => $arr["request"]["topic"],
			));
		}
		
		if(isset($arr["request"]["order"]["day"]) && $arr["request"]["order"]["amount"] > 0 && $arr["request"]["order"]["product"])
		{
			$this->_handle_new_order($arr);
		}
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

//-- methods --//
	/**
		@comment
			returns reservations connected to given conference
	**/
	function get_reservations($oid)
	{
		if(!is_oid($oid))
		{
			return array();
		}
		$c = new connection();
		$conns = $c->find(array(
			"to.class_id" => CL_RESERVATION,
			"reltype" => "RELTYPE_RESERVATION",
			"from" => $oid,
		));
		$ret = array();
		foreach($conns as $data)
		{
			$ret[$data["to"]] = obj($data["to"]);
		}
		return $ret;
	}

	function get_rooms($oid)
	{
		$res = $this->get_reservations($oid);
		foreach($res as $res_id => $res_obj)
		{
			$room = $res_obj->prop("resource");
			$ret[$room] = obj($room);
		}
		return $ret;
	}

	function _get_room_resources($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_field(array(
			"name" => "book_resources",
			"caption" => t("reserveeri"),
			"align" => "center",
			"width" => "60px",
		));
		$t->define_field(array(
			"name" => "free_resources",
			"caption" => t("vabu ressursse"),
			"align" => "center",
			"width" => "50px",
		));
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
			"width" => "200px",
		));
		$res = $this->get_reservations($arr["obj_inst"]->id());
		$room_inst = get_instance(CL_ROOM);
		$reservation_inst = get_instance(CL_RESERVATION);
		foreach($res as $oid => $obj)
		{
			$reserved_resources = $reservation_inst->resource_info($oid);
			$start = $obj->prop("start1");
			$end = $obj->prop("end");

			$room = $obj->prop("resource");
			$room_obj = obj($room);
			$resources = $room_inst->get_room_resources($room);
			foreach($resources as $res_id => $res_obj)
			{
				$free =  $reservation_inst->resource_availability(array(
					"resource" => $res_id,
					"start" => $obj->prop("start1"),
					"end" => $obj->prop("end"),
				));
				$t->define_data(array(
					"free_resources" => $free,
					"name" => $res_obj->name(),
					"room" => $room_obj->name()." ".$date,
					"book_resources" => html::textbox(array(
						"name" => "reservations[".$oid."][".$res_id."]",
						"size" => 3,
						"value" => $reserved_resources[$res_id],
					)),
				));
			}

		}
		$t->set_rgroupby(array(
			"group" => "room",
		));
	}

	function _get_catering_resources_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$room_inst = get_instance(CL_ROOM);
		$reservation_inst = get_instance(CL_RESERVATION);
		$order_inst = get_instance(CL_SHOP_ORDER);
		$prod_inst = get_instance(CL_SHOP_PRODUCT);
		$rooms = $this->get_rooms($arr["obj_inst"]->id());
		$room = reset($rooms);
		$list = $room_inst->get_prod_list((($tmp = $arr["request"]["tree_filter"])?$tmp:$room->id()));
		// ehh tsiish.. this is going to be way different
		$t->define_field(array(
			"name" => "day",
			"caption" => t("P&auml;ev"),
		));
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
		));
		$t->define_field(array(
			"name" => "product",
			"caption" => t("Toode"),
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
		));


		$res = $this->get_reservations($arr["obj_inst"]->id());


		// generating the extra row for adding an order
		foreach($res as $obj)
		{
			$s = $obj->prop("start1");
			$e = $obj->prop("end");
			if(($tmp = $obj->prop("resource")))
			{
				$rooms[$tmp] = call_user_func(array(obj($tmp), "name"));
				$tprods = $room_inst->get_prod_list($tmp);
				$prods = array_merge($prods, $tprods->arr());
			}
			for($time = $s;$time<$e;$time += 86400)
			{
				$times[$time.".".$obj->id()] = date("d/m/Y",$time).", ruum:".$rooms[$tmp];
			}

		}
		$products = array(
			"0" => t("-- Vali toode --"),
		);
		foreach($prods as $oid => $obj)
		{
			$products[$obj->id()] = $obj->name();
		}
		$times = array_unique($times);
		$times = array_merge(array(0 => t("-- Vali p&auml;ev ja ruum -- ")), $times);
		$day_select = html::select(array(
			"name" => "order[day]",
			"options" => $times,
		));
		/*	
		$rooms = array_merge(array(0 => t("-- Vali ruum --")), $rooms);
		$room_select = html::select(array(
			"name" => "order[room]",
			"options" => $rooms,
		));
		*/
		$prod_select = html::select(array(
			"name" => "order[product]",
			"options" => $products,
		));
		$extra_row = array(
			"day" => $day_select,
			//"room" => $room_select,
			"product" => $prod_select,
			"amount" => html::textbox(array(
				"name" => "order[amount]",
				"size" => 4,
			)),
		);
		$t->define_data($extra_row);


		// defining orders data
		foreach($res as $oid => $obj)
		{
			$ord = $reservation_inst->get_orders($oid);
			foreach($ord as $order => $time)
			{
				$order = obj($order);
				$prods = $order_inst->get_items_from_order($order);
				foreach($prods as $prod_id => $amount)
				{
					$room = obj($obj->prop("resource"));
					$prod = obj($prod_id);
					$t->define_data(array(
						"product" => $prod->name(),
						"day" => date("d/m/Y", $time),
						"amount" => $amount,
						"room" => $room->name(),
						"price" => ($amount * ($pr = $prod_inst->get_price($prod)))." (".$pr.")",
					));
				}
			}
		}
		
	}
	
	function _handle_new_order($arr)
	{
		list($day, $reservation) = split("[.]", $arr["request"]["order"]["day"]);
		$amount = $arr["request"]["order"]["amount"];
		$product = $arr["request"]["order"]["product"];
		$prod_list = array(
			$product => $amount,
		);
		$inst = get_instance(CL_ROOM);
		$inst->order_products($prod_list, $reservation, $day);
	}

	/**
		for now this is isn't used.. mayby in the future somtime
	**/
	function get_js_prods($room)
	{
		$room_inst = get_instance(CL_ROOM);
		$list = $room_inst->get_prod_list($room);
		foreach($list->arr() as $oid => $obj)
		{
			$str .="<select value=\"".$oid."\">".$obj->name()."</select>";
		}
		return $str;
	}

	/**
		for now this is isn't used.. mayby in the future somtime
	**/
	function get_js_rooms($day, $conference)
	{
		$rooms = $this->get_rooms_for_day();
		foreach($rooms as $oid => $obj)
		{
			$str .= "<select value=\"".$oid."\">".$obj->name()."</select>";
		}
		return $str;
	}

	function get_rooms_for_day($day, $conference)
	{
		if(!is_oid($conference))
		{
			return array();
		}
		$res = $this->get_reservations($conference);
		$day = date("Ymd", $day);
		foreach($res as $obj)
		{
			$s = $obj->prop("start1");
			$e = $obj->prop("end");
			if(date("Ymd",$s) <= $day && date("Ymd",$d) >= $day)
			{
				$ret[$oid] = $obj;
			}
		}
		return $ret;

	}

	function reserve_room_resources($resources, $oid)
	{
		/*
			t&auml;is krdi geyymüüse funktsioon !! 
		*/
		if(!is_array($resources) || !is_oid($oid))
		{
			return false;
		}
		$obj = obj($oid);
		$reservation_inst = get_instance(CL_RESERVATION);
		foreach($resources as $reservation => $resources)
		{
			$reservation_obj = obj($reservation);
			$info = $reservation_inst->resource_info($reservation);
			foreach($resources as $resource => $count)
			{
				$available_before = $reservation_inst->resource_availability(array(
					"resource" => $resource,
					"start" => $reservation_obj->prop("start1"),
					"end" => $reservation_obj->prop("end"),
				));
				if(($count-$info[$resource]) <= $available_before && $count >= 0)
				{
					$info[$resource] = $count;
					$reservation_inst->set_resource_info($reservation, $info);
				}
			}
		}
		$obj->save();
	}

	function _get_rooms_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "add_room",
			"tooltip" => t("Lisa ruum"),
			"img" => "new.gif",
			"action" => "create_new_room_and_reservation",
		));

		$popup_search = get_instance("vcl/popup_search");
		$search_butt = $popup_search->get_popup_search_link(array(
			"pn" => "room_search_results",
			"clid" => CL_ROOM,
		));
		$tb->add_cdata($search_butt);

		$tb->add_button(array(
			"name" => "remove_room",
			"tooltip" => t("Eemalda ruum"),
			"img" => "delete.gif",
			"action" => "remove_reservations",
		));
	}

	function _get_rooms_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "reservations_sel",
			"field" => "sel",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Ruum"),
		));
		$t->define_field(array(
			"name" => "date_from",
			"caption" => t("Algusaeg"),
		));
		$t->define_field(array(
			"name" => "date_to",
			"caption" => t("L&otilde;puaeg"),
		));
		$res = $this->get_reservations($arr["obj_inst"]->id());
		$time_select = get_instance("vcl/date_edit");
		foreach($res as $res_id => $res_obj)
		{
			$room = $res_obj->prop("resource");
			$room = obj($room);
			$t->define_data(array(
				"sel" => $res_id,
				"name" => html::href(array(
					"caption" => $room->name(),
					"url" => $this->mk_my_orb("change", array(
						"id" => $room->id(),
						"return_url" => get_ru(),
					), CL_ROOM),
				)),
				"date_from" => $time_select->gen_edit_form("reservation_times[".$res_id."][from]", $res_obj->prop("start1")),
				"date_to" => $time_select->gen_edit_form("reservation_times[".$res_id."][to]", $res_obj->prop("end")),
			));
		}
	}

	function _update_reservation_times($times)
	{
		foreach($times as $res_id => $data)
		{
			$reservation = obj($res_id);
			$f = $data["from"];
			$t = $data["to"];
			$start = mktime($f["hour"], $f["minute"], $f["second"], $f["month"], $f["day"], $f["year"]);
			$end = mktime($t["hour"], $t["minute"], $t["second"], $t["month"], $t["day"], $t["year"]);
			$reservation->set_prop("start1", $start);
			$reservation->set_prop("end", $end);
			$reservation->save();
		}
	}

	/**
		@attrib params=name all_args=1 name=remove_reservations
	**/
	function remove_reservations($arr)
	{
		foreach($arr["reservations_sel"] as $res)
		{
			$obj = obj($res);
			$obj->delete();
		}
		return $arr["post_ru"];
	}

	function _create_new_reservations($arr)
	{
		$conference = obj($arr["conference"]);
		foreach($arr["rooms"] as $room)
		{
			$o = NULL;
			$room_obj = obj($room);
			$o = obj();
			$o->set_class_id(CL_RESERVATION);
			$o->set_parent($arr["conference"]);
			$o->set_name("Ruumi '".$room_obj->name()."' reservatsioon");
			$o->save();
			$o->set_prop("resource", $room);
			$o->set_prop("start1", $conference->prop("start_time"));
			$o->set_prop("end", $conference->prop("end_time"));
			$o->save();
			$conference->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_RESERVATION",
			));
		}
	}

	/**
		@attrib params=name name=create_new_room_and_reservation all_args=1
	**/
	function create_new_room_and_reservation($arr)
	{
		$conference = obj($arr["id"]);
		$room = obj();
		$room->set_parent($conference->id());
		$room->set_class_id(CL_ROOM);
		$room->set_name(t("nimetu"));
		$room->save();
		$this->_create_new_reservations(array(
			"conference" => $conference->id(),
			"rooms" => array(
				$room->id(),
			),
		));

		return $this->mk_my_orb("change", array(
			"return_url" => $arr["post_ru"],
			"id" => $room->id(),
		), CL_ROOM);
	}

	function _get_presenters_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "presenter_sel",
			"field" => "sel",
			"width" => "10px",
		));
		$t->define_field(array(
			"name" => "presenter",
			"caption" => t("Esineja"),
		));
		$t->define_field(array(
			"name" => "topic",
			"caption" => t("Teema"),
			"width" => "100px",
		));
		$pre = $arr["obj_inst"]->connections_from(array(
			"to.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_PRESENTER",
		));
		$topics = $this->presenter_topics($arr["obj_inst"]);
		foreach($pre as $c)
		{
			$per = $c->to();
			$t->define_data(array(
				"sel" => $per->id(),
				"presenter" => $per->name(),
				"topic" => html::textbox(array(
					"name" => "topic[".$per->id()."]",
					"size" => 100,
					"value" => $topics[$per->id()],
				)),
			));
		}
	}

	function presenter_topics($obj)
	{
		return $obj->meta("presenter_topics");
	}
	
	function set_presenter_topics($arr)
	{
		if(!is_oid($arr["id"]))
		{
			return false;
		}
		$o = obj($arr["id"]);
		$o->set_meta("presenter_topics", $arr["topics"]);
		$o->save();
		return true;
	}

	/**
		@attrib params=name name=rem_presenters all_args=1
	**/
	function rem_presenters($arr)
	{
		$obj = obj($arr["id"]);
		foreach($arr["presenters_sel"] as $presenter)
		{
			$obj->disconnect(array(
				"from" => $presenter,
			));
		}
		return $arr["post_ru"];
	}

	function _get_presenters_tb($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "add_presenter",
			"tooltip" => t("Lisa esineja"),
			"img" => "new.gif",
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr["obj_inst"]->id(),
				"parent" => $arr["obj_inst"]->id(),
				"reltype" => 3, 
				"return_url" => get_ru(),
			), CL_CRM_PERSON),
		));
		$tb->add_button(array(
			"name" => "rem_presenter",
			"tooltip" => t("Eemalda esineja"),
			"img" => "delete.gif",
			"action" => "rem_presenters",
		));
	}

	function conference_types()
	{
		return $this->conference_types;
	}

	function additional_conference_types()
	{
		return $this->additional_conference_types;
	}
}
?>
