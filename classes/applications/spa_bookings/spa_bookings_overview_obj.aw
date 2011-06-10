<?php

class spa_bookings_overview_obj extends _int_object
{
	const CLID = 1187;

	public function get_category_names()
	{
		$ol = new object_list(array(
			"class_id" => CL_ROOM_CATEGORY,
			"site_id" => array(),
			"lang_id" => array(),
			"parent" => $this->id(),
		));
		return $ol->names();
	}
	
	public function get_rooms($arr = array())
	{
		$filter = array(
			"class_id" => CL_ROOM,
			"site_id" => array(),
			"lang_id" => array(),
		);
		if(is_oid($arr["cat"]))
		{
			$filter["CL_ROOM.RELTYPE_CATEGORY"] = $arr["cat"];

		}

		if($arr["name"])
		{
			$filter["name"] = "%".$arr["name"]."%";

		}

//t&uuml;ra , see saast vaja metast v&auml;lja saada
		if(is_numeric($arr["cap_to"]) && is_numeric($arr["cap_from"]))
		{
			$filter["max_capacity"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $arr["cap_from"] , $arr["cap_to"]);
		}
		else
		{
			if(is_numeric($arr["cap_to"]))
			{
				$filter["max_capacity"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, (int)$arr["cap_to"]);
			}
			if(is_numeric($arr["cap_from"]))
			{
				$filter["max_capacity"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, (int)$arr["cap_from"]);
			}
		}
		$ol = new object_list($filter);
		return $ol;
	}

	public function get_reservations($arr = array())
	{
		$filter = array(
			"class_id" => CL_RESERVATION,
			"site_id" => array(),
			"lang_id" => array(),
		);
		if(is_oid($arr["room"]))
		{
			$filter["resource"] = $arr["room"];

		}

		if(is_oid($arr["category"]))
		{
			$filter["CL_RESERVATION.RELTYPE_RESOURCE.RELTYPE_CATEGORY"] = $arr["category"];
		}

		if($arr["name"])
		{
			$filter["name"] = "%".$arr["name"]."%";

		}
		if(isset($arr["from"]) && $arr["from"] > 0 && isset($arr["to"]) && $arr["to"] > 0)
		{
			$filter["start1"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $arr["from"], $arr["to"]);
		}
		elseif(isset($arr["from"]) && $arr["from"] > 0)
		{
			$filter["end"] = new obj_predicate_compare(OBJ_COMP_GREATER, $arr["from"]);
		}
		elseif(isset($arr["to"]) && $arr["to"] > 0)
		{
			$to += 24 * 60 * 60 -1;
			$filter["start1"] = new obj_predicate_compare(OBJ_COMP_LESS, $arr["to"]);
		}

		$ol = new object_list($filter);
		return $ol;
	}

	public function get_max_capacity()
	{
		$filter = array(
			"class_id" => CL_ROOM,
			"lang_id" => array(),
			"site_id" => array(),
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_ROOM => array(
					new obj_sql_func(OBJ_SQL_MAX, "max_capacity", "max_capacity"),
				)
			)
		);
		if(is_array($t->get_element_from_all("max_capacity")))
		{
			$max = reset($t->get_element_from_all("max_capacity"));
			if($max > 10)
			{
				return $max;
			}
		}

		return 10;
	}

	/** Returns currencies in use
		@attrib api=1
		@returns
			array(
				cur_oid => cur_name
			)
		@comment
			Actually what this does is just return all system currencies right now, and all the places even don't use this in reservation obj(but they should).
	 **/
	function get_currencies()
	{
		$ol = new object_list(array(
			"site_id" => array(),
			"lang_id" => array(),
			"class_id" => CL_CURRENCY,
		));
		return $ol->names();
	}


}

?>
