<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/spa_bookings/spa_booking.aw,v 1.11 2008/03/13 16:05:19 markop Exp $
// spa_booking.aw - SPA Reserveering 
/*

@classinfo syslog_type=ST_SPA_BOOKING relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=markop
@tableinfo aw_spa_bookings index=aw_oid master_index=brother_of master_table=objects

@default table=aw_spa_bookings
@default group=general

	@property person type=relpicker reltype=RELTYPE_PERSON field=aw_person
	@caption Isik

	@property start type=date_select field=aw_start
	@caption Algus

	@property end type=date_select field=aw_end
	@caption L&otilde;pp

	@property package type=relpicker reltype=RELTYPE_PACKAGE field=aw_package automatic=1
	@caption Pakett

	@property seller type=relpicker reltype=RELTYPE_SELLER field=aw_seller 
	@caption M&uuml;&uuml;ja


@reltype PERSON value=1 clid=CL_CRM_PERSON
@caption Isik

@reltype PACKAGE value=2 clid=CL_SHOP_PACKET
@caption Pakett

@reltype ROOM_BRON value=3 clid=CL_RESERVATION
@caption Ruumi broneering

@reltype EXTRA_PROD value=4 clid=CL_SHOP_PRODUCT
@caption Lisateenus

@reltype SELLER value=5 clid=CL_CRM_PERSON,CL_CRM_COMPANY
@caption M&uuml;&uuml;ja

@reltype MAIN_PERSON value=6 clid=CL_CRM_PERSON
@caption Perepea
*/

class spa_booking extends class_base
{
	const AW_CLID = 1180;

	function spa_booking()
	{
		$this->init(array(
			"tpldir" => "applications/spa_bookings/spa_booking",
			"clid" => CL_SPA_BOOKING
		));
	}
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		if ($arr["request"]["from_b"])
		{
			$fb = obj($arr["request"]["from_b"]);
			$prop["value"] = $fb->prop($prop["name"]);
		}
		switch($prop["name"])
		{
			case "name":
				$prop["type"] = "text";
				break;

			case "person":
			case "package":
				if (!isset($prop["options"][$prop["value"]]) && $this->can("view", $prop["value"]))
				{
					$tmp = obj($prop["value"]);
					$prop["options"][$prop["value"]] = $tmp->name();
				}
				break;

			case "seller":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					$p = get_current_person();
					$prop["options"][$p->id()] = $p->name();
				}
				break;
		};
		return $retval;
	}

	/**
		@attrib name=bank_return  is_public=1 all_args=1
		@param id required type=int
	**/
	function bank_return($arr)
	{
		$inst = get_instance(CL_SPA_CUSTOMER_INTERFACE);
		return $inst->bank_return($arr);
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "name":
				return PROP_IGNORE;
		}
		return $retval;
	}	

	function callback_pre_save($arr)
	{
		$arr["obj_inst"]->set_name(sprintf("Broneering %s %s - %s", 
			$arr["obj_inst"]->prop("person.name"), 
			date("d.m.Y", $arr["obj_inst"]->prop("start")), 
			date("d.m.Y", $arr["obj_inst"]->prop("end"))
		));
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_spa_bookings (aw_oid int primary key, aw_person int, aw_start int, aw_end int, aw_package int)");
			return true;
		}

		switch($f)
		{
			case "aw_seller":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}

	/** checks if all necessary reservation objects are connected to this booking object and creates empty ones if needed
		@attrib api=1
	**/
	function check_reservation_conns($booking)
	{
		enter_function("spa_booking::check_reservation_conns");
		if (!$this->can("view", $booking->prop("package")))
		{
			return;
		}

		$rv2prod = array();
		foreach($booking->connections_from(array("type" => "RELTYPE_ROOM_BRON")) as $c)
		{
			$room_bron = $c->to();
			$rv2prod[$room_bron->meta("product_for_bron")] = $room_bron;
		}
		$package = obj($booking->prop("package"));
		$pk = $package->instance();
		$entry_inst = get_instance(CL_SPA_BOOKIGS_ENTRY);
		$dates = $entry_inst->get_booking_data_from_booking($booking);
		$pkings = $pk->get_default_packagings_in_packet($package);
		foreach($pk->get_products_for_package($package) as $prod_id => $count)
		{
			$prod = obj($prod_id);
			if (!isset($rv2prod[$prod->id()]))
			{
				$rooms = $entry_inst->get_rooms_for_product($prod->id());
				if (count($rooms))
				{
					$room_inst = get_instance(CL_ROOM);
					for ($i = 0; $i < $count; $i++)
					{
						enter_function("spa_booking::make_reservation");
						$rv_id = $room_inst->make_reservation(array(
							"id" => reset(array_keys($rooms)),
							"data" => array(
								"customer" => $booking->prop("person"),
								"products" => array($pkings[$prod->id()] => 1)
							),
							"meta" => array(
								"product_for_bron" => $prod->id(),
								"product_count_for_bron" => $i,
							)
						));
						$booking->connect(array(
							"to" => $rv_id,
							"type" => "RELTYPE_ROOM_BRON"
						));
						exit_function("spa_booking::make_reservation");
					}
				}
			}
		}
		exit_function("spa_booking::check_reservation_conns");
	}
}
?>
