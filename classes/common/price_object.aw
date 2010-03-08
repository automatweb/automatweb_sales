<?php
/*
@classinfo maintainer=markop
*/

class price_object extends _int_object
{
	function name()
	{
		return parent::name();
	}

	function set_prop($name,$value)
	{
		$children = $this->_get_price_children();
		$toallprops = array("date_from" , "name" , "date_to");

		if(is_array($value) && array_key_exists("year" ,$value))
		{
			$value = date_edit::get_timestamp($value);
		}
		if(in_array($name , $toallprops))
		{
			foreach($children->arr() as $c)
			{
				$c->set_prop($name , $value);
			}
		}
		parent::set_prop($name,$value);
	}

	/** Returns main price objects connected to object
		@attrib name=get_price_objects api=1 all_args=1

		@param object required type=oid
			object

		@returns 
			object list
	**/
	function get_price_objects($object)
	{
		$filter = array(
			"class_id" => array(CL_PRICE),
			"site_id" => array(),
			"lang_id" => array(),
			"CL_PRICE.RELTYPE_OBJECT.id" => $object,
//			"CL_PRICE.RELTYPE_.id" => $object,
//			"parent.class_id" => new obj_predicate_not(CL_PRICE),
		);
		$ol = new object_list($filter);
		foreach($ol->arr() as $o)
		{
			$parent = obj($o->parent());
			if($parent->class_id() == CL_PRICE)
			{
				$ol->remove($o->id());
			}
		}
		return $ol;
	}

	private function set_prices($prices)
	{
		$curr_array = $this->_get_curr_objects();
		foreach($prices as $key => $val)
		{
			if($val != "")
			{
				if($curr_array[$key])
				{
					$co = $curr_array[$key];
				}
				else
				{
					$co = $this->add_other($key,$val);
				}
				$coob = obj($co);
				$coob->set_prop("sum" , $val);
				$coob->save();
			}
			else
			{
				if($curr_array[$key])
				{
					$co = obj($curr_array[$key]);
					$co->delete();
				}
			}
		}
	}

	/**
		@attrib name=add api=1 all_args=1

		@param object required type=oid
			object to connect to

		@param name optional type=String
			price object name

		@param parent optional type=oid default=object
			price object parent

		@param currency optional type=oid
			Price object currency oid

		@param sum optional type=double

		@returns 
			oid - Price object oid.

		@comment
			adds new price object.
	**/
	function add($arr)
	{
		$o = obj($arr["object"]);
		$price = new object();
		$price->set_class_id(CL_PRICE);
		$price->set_name($arr["name"] ? $arr["name"] : $o->name()." ".t("hind"));
		$price->set_parent($arr["parent"] ? $arr["parent"] : $arr["object"]);
		$price->set_prop("type" , $o->class_id());
		foreach($arr as $key => $val)
		{
			if($price->is_property($key) && $val)
			{
				$price->set_prop($key , $val);
			}
		}

		$price->save();
		if(is_oid($arr["object"]))
		{
			$price->connect(array(
				"to" => $arr["object"],
				"type" => "RELTYPE_OBJECT",
			));
		}
		return $price->id();
	}


	/** returns the price in different curr uses child objects
		@attrib api=1

		@returns 
			array("currency" => "price")
	**/
	function get_prices()
	{
		$ret = array();
		$ret[$this->prop("currency")] = $this->prop("sum");

		$ol = $this->_get_price_children();

		foreach($ol->arr() as $obj)
		{
			$ret[$obj->prop("currency")] = $obj->prop("sum");
		}
		return $ret;
	}

	/** changes price data.
		@attrib name=set_data api=1 all_args=1
		
		@param data optional type=array
			data to be changed , array("date_from" => value , "name" => value, "date_to" => value , prices => array(currency1 => value1 , currency2 => value2 ...))
	
	**/
	function set_data($arr)
	{
		extract($arr);
		foreach($arr as $prop => $val)
		{
			if($prop == "prices")
			{
				$this->set_prices($val);
			}
			else
			{
				$this->set_prop($prop , $val);
			}
		}
		$this->save();
		return $this->id();
	}

	//object
	//name , parent
	private function add_other($curr,$sum)
	{
		$objs = $this->connections_from(array(
			"type" => "RELTYPE_OBJECT",
		));
		foreach($objs as $obj)
		{
			$object = $obj->prop("to");
		}

		$co = $this->add(array(
			"name" => $this->name(),
			"parent" => $this->id(),
			"date_from" => $this->prop("date_from"),
			"date_to" => $this->prop("date_to"),
			"object" => $object,
			"currency" => $curr,
			"sum" => $sum,
		));

		return $co;
	}

	private function _get_price_children()
	{
		if(!$this->price_children)
		{
			$this->price_children = new object_list(array(
				"lang_id" => array(),
				"site_id" => array(),
				"class_id" => array(CL_PRICE),
				"parent" => $this->id(),
			));
		}
		return $this->price_children;
	}

	private function _get_curr_objects()
	{
		$c = $this->_get_price_children();
		$ret = array();
		foreach($c->arr() as $o)
		{
			$ret[$o->prop("currency")] = $o->id();
		}
		return $ret;
	}
}
?>