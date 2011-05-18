<?php

class discount_obj extends _int_object
{
	const CLID = 1553;

	public static function get_valid_discount($arr)
	{
		if(!is_oid($arr["object"]))
		{
			return 0;
		}
		$filter = array(
			"class_id" => array(self::CLID),
			"CL_DISCOUNT.object" => $arr["object"],
		);
		
		$ol = new object_list($filter);
		return reset($ol);
	}

	public static function get_valid_discount_coefficient($arr)
	{
		if(!is_oid($arr["object"]))
		{
			return 0;
		}
		$coefficient = 0;	
		$filter = array(
			"class_id" => array(self::CLID),
			"CL_DISCOUNT.object" => $arr["object"],
		);

		$ol = new object_list($filter);

		foreach($ol->arr() as $o)
		{
			$coefficient = $o->prop("discount") / 100.0;
		}
		return $coefficient;
	}

	public static function get_discounts($arr)
	{
		$coefficient = 0;	
		$filter = array(
			"class_id" => array(self::CLID),
		);

		if(!empty($arr["object"]))
		{
			$filter["object"] = $arr["object"];
		}

		if(isset($arr["group"]) and is_oid($arr["group"]))
		{
			$filter["CL_DISCOUNT.RELTYPE_GROUP"] = $arr["group"];
		}

		if(isset($arr["from"]) and $arr["from"] > 1)
		{
			$filter["to"] = new obj_predicate_compare(obj_predicate_compare::GREATER, $arr["from"]);
		}

		if(isset($arr["to"]) and $arr["to"] > 1)
		{
			$filter["from"] = new obj_predicate_compare(obj_predicate_compare::LESS_OR_EQ, $arr["to"]);
		}

		$ol = new object_list($filter);
		$res = array();
		foreach($ol->arr() as $o)
		{
			$res[$o->id()] = $o->properties();
		}

		return $res;
	}

	public static function set_discount($arr)
	{
		$coefficient = 0;	

		if(is_oid($arr["id"]))
		{
			$o = obj($arr["id"]);
		}
		else
		{
			$o = new object();
			if($arr["parent"])
			{
				$o->set_parent($arr["parent"]);
			}
			else
			{
				$o->set_parent($arr["object"]);
			}
			$o->set_class_id(self::CLID);
			$o->set_name($arr["name"] ? $arr["name"] : t("Allahindlus " . $arr["discount"] . "%"));
		}

		$props = array("from" , "to" , "object" , "discount" , "active" , "apply_groups" , "order_from" , "order_to");
		foreach($props as $prop)
		{
			if(isset($arr[$prop]))
			{
				$o->set_prop($prop , $arr[$prop]);
			}
		}

		if(is_oid($arr["object"]))
		{
			$object = obj($arr["object"]);
			$o->set_prop("class" , $object->class_id());
		}

		$o->save();
		return $o->id();
	}

}

?>
