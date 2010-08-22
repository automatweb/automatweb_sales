<?php

// reservation.aw - Broneering
/*

@classinfo relationmgr=yes no_status=1 prop_cb=1

@default table=objects
@default group=general
#TAB GENERAL

	@property start1 type=datetime_select store=no
	@caption Algusaeg

	@property end type=datetime_select store=no
	@caption L&otilde;ppaeg

	@property firstname type=textbox store=no
	@caption Eesnimi

	@property lastname type=textbox store=no
	@caption Perenimi

	@property company type=textbox store=no
	@caption Organisatsioon

	@property phone type=textbox store=no
	@caption Telefon

	@property comment type=textarea store=no rows=20
	@caption M&auml;rkused

	@property people type=select store=no
	@caption Meie esindaja

	@property other_rooms type=select multiple=1 store=no
	@caption Broneeri lisaks ruumid

	@property id type=hidden store=no
	@caption ID

	@property parent type=hidden store=no
	@caption Parent

	@property resource type=hidden store=no
	@caption Ruum

	@property product type=hidden store=no
	@caption Toode


*/

class quick_reservation extends class_base
{
	function quick_reservation()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/quick_reservation",
			"clid" => CL_QUICK_RESERVATION
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		if($arr["request"][$prop["name"]])
		{
			$prop["value"] = $arr["request"][$prop["name"]];
		}
		$prop["name"] = "bron[".$prop["name"]."]";
		switch($prop["name"])
		{
			//-- get_property --//
			case "bron[name]":
				if($arr["request"]["error"]) arr($arr["request"]["error"]);
				return PROP_IGNORE;
				$prop["type"] = "text";
				$prop["value"] = $arr["request"]["error"];
				break;
			case "bron[people]":
				$room = obj($arr["request"]["resource"]);
				$professions = $room->prop("professions");
				if(is_array($professions) && sizeof($professions))
				{
					$ol = new object_list(array(
						"class_id" => CL_CRM_PERSON,
						"lang_id" => array(),
						"CL_CRM_PERSON.RELTYPE_RANK" => $professions,
					));
					$people_opts = array("") + $ol->names();
				}
				$prop["options"] = $people_opts;
				break;
			case "bron[other_rooms]":
				if(is_oid($arr["request"]["resource"]) && $this->can("view" , $arr["request"]["resource"]))
				{
					$room = obj($arr["request"]["resource"]);
					$prop["options"] = $room->get_other_rooms_selection();
					if(!sizeof($prop["options"]))
					{
						return PROP_IGNORE;
					}
					$error_rooms = array();
					foreach($prop["options"] as $room => $val)
					{
						if($this->can("view" , $room))
						{
							$oro = obj($room);
							$end = $arr["request"]["end"];
							if($end == $arr["request"]["start1"])
							{
								$end = $end + 3600;
							}
							if(!$oro->is_available(array(
								"start" => $arr["request"]["start1"],
								"end" => $end,
							)))
							{
								$error_rooms[] = $oro->name();
							}
							else
							{
								$prop["value"][] = $room;
							}
						}

					}
					if(sizeof($error_rooms))
					{
						$prop["error"] = t("Sellisele ajale ei saa broneerida ruume:")." ".join("," , $error_rooms);
						return PROP_OK;
					}
				}
				else
				{
					return PROP_IGNORE;
				}
				break;
			case "bron[end]":
				if($prop["value"] == $arr["request"]["start1"]);
				{
					$prop["value"] = $prop["value"] + 3600;
				}
				break;
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$room_inst = get_instance(CL_ROOM);
		$url = $room_inst->mk_my_orb("admin_add_bron_popup", array(
			"bron" => $arr["request"]["bron"],
			"end" => date_edit::get_timestamp($arr["request"]["bron"]["end"]),
			"start1" => date_edit::get_timestamp($arr["request"]["bron"]["start1"]),
			"return_url" => $arr["request"]["return_url"],
			"parent" => $arr["request"]["bron"]["parent"],
			"resource" => $arr["request"]["bron"]["resource"],
			"product" => $arr["request"]["bron"]["product"],
			"post_msg_after_reservation" => $arr["request"]["post_msg_after_reservation"],
			"other_rooms" => $arr["request"]["bron"]["other_rooms"],
		));

		die("<script type='text/javascript'>
			window.location.href='".$url."';
		</script>
		");

		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "resources_price":
			case "resources_discount":
				break;
		}
		return $retval;
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["post_msg_after_reservation"] = $request["post_msg_after_reservation"];
		$arr["bron[people_count]"] = $request["people_count"];
	}
}
