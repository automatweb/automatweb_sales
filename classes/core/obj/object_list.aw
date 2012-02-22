<?php

// object_list.aw - with this you can manage object lists

class object_list extends _int_obj_container_base
{
	var $list = array();	// array of objects in the current list
	var $list_names = array();
	var $list_objdata = array();
	var $iter_index = 0;
	var $iter_lut = array();
	var $iter_lut_count = 0;

	public $ds_query_string = ""; // database query executed to retrieve list data. use for debugging only

	protected $filter = array();
	protected $object_id_property = "oid";

	private $__filter_by_user_access_uid = "";
	private $__filter_by_user_access_action = "";

	/** creates the object list, can also initialize it with objects
		@attrib api=1

		@param param optional type=array
			array of filter parameters, by what the object list is filtered. if class is
			specified in the filter, the filter can also include properties, otherwise properties
			are ignored, only object table parameters are used, optional

		@comment
		In addition to the member functions described here, this class also implements all the  object class mutator functions, only in this case they apply fot all objects in the list, for instance #php# $ol->set_name("fish"); #/php# will change all the objects names in the list to "fish". You must also call $ol->save() to save the objects, because they are not saved automatically

		@errors
			constructors return no errors

		@returns
			none

		@examples
			$ol = new object_list;
			$ol2 = new object_list(array(
				"name" => "%foo%",
				"class_id" => CL_BAR,
				"cool_property" => true
			));

			// it is possible to add sorting rules to object_list parameters
			// they should be "table_name.field_name ASC|DESC"

			// limit is used as in mysql queries, so refer to mysql manual how this should be used

			$ol3 = new object_list(array(
				"name" => "%foo%",
				"class_id" => CL_BAR,
				"sort_by" => "objects.created DESC",
				"limit" => "2,6"
			));
	**/
	function object_list($param = NULL, $load_param = array())
	{
		$this->object_id_property = isset($load_param["object_id_property"]) ? $load_param["object_id_property"] : "oid";

		if ($param != NULL)
		{
			$this->filter($param);
		}
	}

	/** creates a new list from the filter, basically same as the constructor
		@attrib api=1

		@errors
			none

		@comment
			- array of filter parameters, according to which to filter the object list, required
			filter parameters for bitmask properties/fields can be array("mask" => OBJ_FLAG_ALL, "flags" => OBJ_NEEDS_TRANSLATION)
			mask specifies the mask to compare against and flags specify the flags that must be present or not
			after the mask is applied to the bits
			parameters are documented in $AWROOT/docs/tutorials/object_list.txt

		@returns
			number of objects in the list after filtering

		@examples
			// creates a list of objects that are of type CL_BAR, name ontains foo and cool_property equals true
			$ol = new object_list(array(
				"name" => "%foo%",
				"class" => CL_BAR,
				"cool_property" => true
			));

			// creates a list of all objects in the system, that have status = STAT_ACTIVE, the previous list is discarded
			$ol->filter(array(
				"status" => STAT_ACTIVE
			));
			// returns all active objects in the system
	**/
	function filter($param)
	{
		if (!empty($GLOBALS["OBJ_TRACE"]))
		{
			echo "object_list::filter(".join(",", map2('%s => %s', $param)).") <br>";
		}

		if (!is_array($param))
		{
			throw new awex_obj_param("Parameter must be an array");
		}

		if (isset($param["oid"]) && is_array($param["oid"]) && sizeof($param["oid"]) == 0)
		{
			throw new awex_obj_param("OID parameter cannot be an empty array");
		}

		// check if param is an array of connection objects. if so, then get the object id's from that
		if (is_array($param))
		{
			$tmp = reset($param);
			if (is_object($tmp) && get_class($tmp) === "connection")
			{
				// rewrite filter to get all objects from conn, so we use magic from ->begin() to get them all at once
				$arr = array();
				foreach($param as $c)
				{
					$arr[] = $c->prop("to");
				}
				if (!count($arr))
				{
					return;
				}
				$param = array(
					"class_id" => array(),
					"oid" => $arr
				);
			}
		}

		// we should check the individual arguments as well .. if "oid" is an object
		// (aw_array) .. then this thingie will return absurd results .. like
		// for id-s of all documents in the database.. yehh ...it happened
		// in UT --duke

		$this->_int_filter($param);
	}

	/** Deletes all objects that are in the current list and removes them from the list
		@attrib api=1 params=pos

		@param final_delete optional type=bool
			If set to true, the objects are completely deleted. see object::delete for more.

		@errors
			- acl error is thrown if the user has no delete access for any of the objects in the list

		@returns
			number of objects deleted

		@examples
			$ol = new object_list(array(
				"status" => STAT_ACTIVE
			));
			$ol->delete();
	**/
	function delete($param = null)
	{
		return parent::delete($param);
	}

	/** adds the specified object to the current list
		@attrib api=1

		@param param required type=oid|array[oid]
			the object(s) to add to the list. type: integer (object id), string (object alias), object class instance, object list instance

		@errors
			throws awex_obj_acl if the user has no view access to an object specified

		@returns
			the number of object in the list after addition

		@examples
			$ol = new object_list();
			$ol->add(obj(56));
			$ol->add("alias/foo");
			$ol->add(67);

			$ol2 = new object_list(array("name" => "foo"));
			$ol->add($ol2);
	**/
	function add($param)
	{
		$this->_int_add_to_list(object_loader::instance()->param_to_oid_list($param));
	}

	/** removes the specified object(s) from the current list
		@attrib api=1

		@param param required
			the objects to remove from the list
			type: integer (object id), string (alias), object class instance, object list instance

		@errors
			none

		@returns
			the number of objects in the list after the specified objects are removed

		@examples
			$ol = new object_list();
			$ol->add(67);
			$ol->remove(67);
	**/
	function remove($param)
	{
		$this->_int_remove_from_list(object_loader::instance()->param_to_oid_list($param));
	}

	/** removes all objects from the list
		@attrib api=1

		@errors
			none

		@returns
			none

		@examples
			$ol = new object_list(array(
				"name" => "%"
			));
			$ol->remove_all();
	**/
	function remove_all()
	{
		$this->_int_remove_from_list($this->ids());
	}

	/** returns the object at the specified index
		@attrib api=1

		@param param required type=oid
			the oid of the object to return, required.

		@errors
			none

		@returns
			the object instance at the specified position, or NULL if none is found in the list

		@examples
			$ol = new object_list(array(
				"id" => "90"
			));
			$ob_90 = $ol->get_at(90);
	**/
	function get_at($param)
	{
		return $this->_int_get_at(object_loader::instance()->param_to_oid($param));
	}

	/** tells if object is in list
		@attrib api=1

		@param object required type=oid,object
			the oid or object instance of the object to be checked for.

		@errors
			none

		@returns bool

		@examples
			$ol = new object_list(array(
				"id" => "90"
			));
			$is_ob_90_in_list = $ol->in_list(90);
	**/
	function in_list($param)
	{
		$object_id = ($object instanceof object) ? $param->id() : $param;
		return isset($this->list[$object_id]);
	}

	/** calls the save method on all the members of the list
		@attrib api=1

		@errors
			- throws acl error, if user has no write access to any object in the list

		@returns
			count of objects saved

		@examples
			$ol = new object_list(array(
				"class" => CL_FILE
			));
			$ol->foreach_o(array(
				"func" => "set_parent",
				"params" => array(888),
				"save" => false,
			));
			$ol->save();
	**/
	function save()
	{
		return parent::save();
	}

	/** sorts the object list by the specified property
		@attrib api=1 params=name

		@errors
			none

		@param prop required
			the property to sort by
			type: string (list is sorted by that property only), array (list is sorted by each property in the array)

		@param order optional
			the order to sort by, optional, defaults to "asc"
			type: string (asc/desc), array (each entry corresponds to the order to sort by in the sortable properties array)

		@returns
			none

		@examples
			$ol = new object_list(array(
				"name" => "foo%"
			));
			$ol->sort_by(array(
				"prop" => "ord",
				"order" => "desc"
			));
			$ol->sort_by(array(
				"prop" => array("ord", "modified"),
				"order" => array("asc", "desc")
			));
	**/
	function sort_by($param)
	{
		if (!is_array($param))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_list::sort_by(): argument must be an array!")
			));
		}

		if ($param["prop"] == "")
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_list::sort_by(): prop argument must be present!")
			));
		}
		$this->_int_sort_list($param["prop"], ((empty($param["order"]) || $param["order"] === "asc") ? "asc" : "desc"));
	}

	/** sorts the object list with the specified function
		@attrib api=1 params=cb

		@errors
			none

		@param cb required
			can be either the function to sort by,
			or an array that consists of an object instance an the function

		@returns
			none

		@examples
			$ol = new object_list(array(
				"name" => "foo%"
			));
			$ol->sort_by_cb(array($this, "__sorter"));
			$ol->sort_by_cb("func");
	**/
	function sort_by_cb($cb)
	{
		if (is_array($cb))
		{
			error::raise_if(!is_object($cb[0]), array(
				"id" => "ERR_CORE_NO_OBJ",
				"msg" => t("object_list::sort_by_cb(): if parameter is an array, the first entry must be an object instance!")
			));

			error::raise_if(!method_exists($cb[0], $cb[1]), array(
				"id" => "ERR_CORE_NO_OBJ",
				"msg" => t("object_list::sort_by_cb(): if parameter is an array, the first entry must be an object instance and the second a method name from that object!")
			));
		}
		else
		{
			error::raise_if(!function_exists($cb),array(
				"id" => "ERR_CORE_NO_FUNC",
				"msg" => sprintf(t("object_list::sort_by_cb(%s): no function %s exists!"), $cb, $cb)
			));
		}
		$this->_int_sort_list_cb($cb);
	}

	function last()
	{
		return $this->_int_get_at(end(array_keys($this->list)));
	}

	/** resets the internal iterator to the beginning of the list, returns the first object and increments the position by one
		@attrib api=1

		@errors
			none

		@returns
			first object in the object list

		@examples
			$ol = new object_list(array(
				"status" => STAT_ACTIVE
			));
			for ($o = $ol->begin(); !$ol->end(); $o = $ol->next();)
			{
				$o->set_name("kala");
				$o->save();
			}
	**/
	function begin()
 	{
		$this->_int_fetch_full_list();

		// here's how begin/next are supposed to work:
		// begin returns the first item, does not advance iterator
		// next 1st advances the iterator, them returns current item
		// then end will be correct even for 1 element lists!
		$this->iter_index = 0;
		$this->iter_lut = array_keys($this->list);
		$this->iter_lut_count = count($this->iter_lut);

		if (!isset($this->iter_lut[$this->iter_index]))
		{
			return null;
		}
		return $this->_int_get_at($this->iter_lut[$this->iter_index]);
	}

	/** Returns the object at the current internal iterator position and increments the current internal iterator by one
		@attrib api=1

		@errors
			none

		@returns
			instance at the object at the current iterator position, or NULL if iterator is
			after the last element in the list

		@examples
			$ol = new object_list(array(
				"status" => STAT_ACTIVE
			));
			for ($o = $ol->begin(); !$ol->end(); $o = $ol->next();)
			{
				$o->set_name("kala");
				$o->save();
			}
	**/
	function next()
	{
		$this->iter_index++;
		if (!isset($this->iter_lut[$this->iter_index]))
		{
			return null;
		}
		return $this->_int_get_at($this->iter_lut[$this->iter_index]);
	}

	function get_prev()
	{
		return $this->_int_get_at($this->iter_lut[$this->iter_index-1]);
	}

	function get_next()
	{
		return $this->_int_get_at($this->iter_lut[$this->iter_index+1]);
	}

	/** returns true if the internal iterator is at the end of the list
		@attrib api=1

		@errors
			none

		@returns
			true if the internal iterator is past the end of the list, false if the internal iterator is not past the end of the list

		@examples
			$ol = new object_list(array(
				"status" => STAT_ACTIVE
			));
			$ol->begin();

			while (!$ol->end())
			{
				$o = $ol->next();
				$o->set_name("foo");
				$o->save();
			}
	**/
	function end()
	{
		return (($this->iter_index) == ($this->iter_lut_count));
	}

	/** iterates over all objects in the list and calls the specified object member function for each object in the list with the specified parameters. this also resets the internal pointer of the list.
		@attrib api=1 params=name

		@param func required type=string
			object class function name to call for each object in the list, required

		@param params optional
			array of parameters to pass to the member function

		@param save optional type=bool
			whether to also call the save method on all objects, optional, defaults to true

		@errors
			- error is thrown if no member function with the specified name exists in the object class
			- acl error is thrown if the user has no change access for any object in the list

		@returns
			the count of the objects in the list

		@examples
			$ol = new object_list(array(
				"status" => STAT_ACTIVE
			));
			$ol->foreach_o(array(
				"func" => "set_name",
				"params" => array("kala"),
				"save" => true
			));
	**/
	function foreach_o($param)
	{
		if (!is_array($param))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_list::foreach_o(): parameter must be an array")
			));
		}

		if (!isset($param["save"]))
		{
			if ($param["func"] == "delete")
			{
				$param["save"] = false;
			}
			else
			{
				$param["save"] = true;
			}
		}

		$func = $param["func"];

		if (!object_loader::instance()->is_object_member_fun($func))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => sprintf(t("object_list::foreach_o(): %s is not a member function of the object class!"), $param["func"])
			));
		}

		// special-case multiple object set_prop and save so we can do just one query
		if ($func === "set_prop" && $param["save"] && ($single_clid = $this->_is_single_clid()) && object_loader::ds()->property_is_multi_saveable($single_clid, $param["params"][0]))
		{
			return object_loader::ds()->save_property_multiple($single_clid, $param["params"][0], $param["params"][1], $this->ids());
		}

		for($o = $this->begin(), $cnt = 0; !$this->end(); $o = $this->next(), $cnt++)
		{
			if (isset($param["params"]))
			{
				call_user_func_array(array($o, $func), $param["params"]);
			}
			else
			{
				$o->$func();
			}

			if ($param["save"])
			{
				$o->save();
			}
		}

		return $cnt;
	}

	/** iterates over the list, calling the specified user function with each object as the parameter
		@attrib api=1

		@param func required
			the name of the function to call, required. the function gets the object reference as the parameter
			type: text (global function name) , array (array($this, "func") - class member function name)

		@param param optional type=any
			single param that can optionally passed to the function

		@param save optional type=bool
			 whether to also call the save method on all objects, optional, defaults to false

		@errors
			- error is thrown, if the specified user function does not exist
			- acl error is thrown if the user has no change access for any object in the list

		@returns
			the count of objects in the list

		@examples
			function ol_cb($o, $parent)
			{
				$o->set_parent($parent);
			}

			$ol->foreach_cb(array(
				"func" => "ol_cb",
				"param" => 666,
				"save" => true
			));
	**/
	function foreach_cb($param)
	{
		if (!is_array($param))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_list::foreach_cb(): parameter must be an array!")
			));
		}

		if (is_array($param["func"]))
		{
			if (!method_exists($param["func"][0], $param["func"][1]))
			{
				error::raise(array(
					"id" => ERR_PARAM,
					"msg" => sprintf(t("object_list::foreach_cb(): callback method %s does not exist in class %s!"), $param["func"][1], get_class($param["func"][1]))
				));
			}
		}
		else
		if ($param["func"] == "" || !function_exists($param["func"]))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => sprintf(t("object_list::foreach_cb(): callback function %s does not exist!"), $param["func"])
			));
		}

		// why not foreach($this->list as $item)? it works just as well, and is
		// easier on the eyes -- duke
		//
		// because then I will not have to reimplement lazy loading here. ever heard of encapsulation?
		// -- terryf

		for ($o = $this->begin(), $cnt = 0; !$this->end(); $o = $this->next(), $cnt++)
		{
			if (is_array($param["func"]))
			{
				$param["func"][0]->$param["func"][1]($o, $param["param"]);
			}
			else
			{
				$param["func"]($o, $param["param"]);
			}

			if ($param["save"])
			{
				$o->save();
			}
		}

		return $cnt;
	}

	/** returns an array of all the objects in the list
		@attrib api=

		@errors
			none

		@returns
			array of objects in the list, array key is object id, value is object instance

		@examples
			$ol = new object_list(array(
				"class" => CL_FILE
			));
			$files = $ol->arr();
	**/
	function arr()
	{
		$o = $this->begin();
		$ret = array();
		for ($cnt = 0; !$this->end(); $o = $this->next())
		{
			$ret[$o->id()] = $o;
		}
		return $ret;
	}

	/** creates a new object list from an array and returns the new instance
		@attrib api=1

		@param param required type=array
			array, key is object id, value is object instance or array to create object instance from

		@errors
			none

		@returns
			instance of object list, with the passed objects inserted in the list

		@examples
			$ol = new object_list(array(
				"class" => CL_FILE
			));
			$files = $ol->arr();
			$ol2 = object_list::from_arr($arr);
	**/
	function from_arr($param)
	{
		if (!is_array($param))
		{
			error::raise(array(
				"id" => ERR_PARAM,
				"msg" => t("object_list::from_arr(): parameter must be an array!")
			));
		}

		$l = new object_list();
		$l->list = safe_array($param);
		return $l;
	}

	////
	// !Some kind of replacement for core->object_list .. returns id=>name pairs of objects
	// parameters:
	//	$add_folders - if true, objects paths are returned instead of just names
	function names($arr = array())
	{
		if (isset($arr["add_folders"]) && $arr["add_folders"])
		{
			$ret = array();
			for ($o = $this->begin(); !$this->end(); $o = $this->next())
			{
				$ret[$o->id()] = $o->path_str();
			}
			return $ret;
		}
		else
		{
			return $this->list_names;
		}
	}

	/** returns all object id's in the current list
		@attrib api=1

		@errors
			none

		@returns
			array of object id's in the current list

		@examples
			$ol = new object_list(array(
				"name" => "%aa%"
			));
			$ids = $ol->ids();
	**/
	function ids()
	{
		$tmp = array_keys($this->list);
		$ret = array();

		foreach($tmp as $v)
		{
			if ($v != "")
			{
				$ret[] = $v;
			}
		}
		return $ret;
	}

	function brother_ofs()
	{
		$ret = array();
		foreach($this->list_objdata as $oid => $d)
		{
			$ret[$d["brother_of"]] = $d["brother_of"];
		}
		return $ret;
	}

	function ords()
	{
		$ret = array();
		foreach($this->list_objdata as $oid => $d)
		{
			$ret[$oid] = $d["ord"];
		}
		return $ret;
	}

	/** returns number of objects in the current list
		@attrib api=1

		@errors
			none

		@returns
			number of objects in the current list

		@examples
			$ol = new object_list(array(
				"status" => STAT_ACTIVE
			));
			echo $ol->count()." deleted objects in system ";
	**/
	function count()
	{
		return count($this->list);
	}

	/** Works almost the same as array_slice(), except it doesn't return anything, but modifies the object_list it is applied to.
		@attrib api=1 name=slice params=pos

		@param offset required type=int

		@param length optional type=int

		@errors
			none

		@returns
			nothing

		@examples
			$ol = new object_list(array(
				"status" => STAT_ACTIVE
			));
			echo $ol->count();	// 386
			$ol->slice(4,5);
			echo $ol->count();	// 5
	**/
	public function slice($start, $length = false)
	{
		$ids = $this->ids();
		$ids_ = $length ? array_slice($ids, $start, $length) : array_slice($ids, $start);
		$this->_int_remove_from_list(array_diff($ids, $ids_));
	}

	public static function iterate_list($oids, $func, $param1 = null, $param2 = null, $param3 = null)
	{
		if (!is_array($oids) || !count($oids))
		{
			return;
		}
		$ol = new object_list(array(
			"oid" => $oids
		));

		foreach($ol->arr() as $o)
		{
			if ($param1 === null)
			{
				$o->$func();
			}
			elseif ($param2 === null)
			{
				$o->$func($param1);
			}
			elseif ($param3 === null)
			{
				$o->$func($param1, $param2);
			}
			else
			{
				$o->$func($param1, $param2, $param3);
			}

			if ("delete" !== $func)
			{
				$o->save();
			}
		}
	}

	/** returns current filter parameters array
		@attrib api=1
		@errors
			none
		@returns array
	**/
	function get_filter()
	{
		return $this->filter;
	}

	///////////////////////////////////////
	// internal private functions. call these directly and die.

	function _int_filter($filter)
	{
		$this->filter = $filter;
		$this->_int_init_empty();

		foreach ($filter as $constraint)
		{
			if ($constraint instanceof obj_predicate_acl)
			{
				$this->__filter_by_user_access_action = $constraint->get_action();
			}
		}

		if ($this->object_id_property !== "oid")
		{
			if (empty($filter["class_id"]))
			{
				throw new awex_objlist_oid_class("object_id_property without class_id specified");
			}

			$clids = (array) $filter["class_id"];
			$props = array();
			foreach ($clids as $class_id)
			{
				$props[(int) $class_id] = array(
					$this->object_id_property,
					"{$this->object_id_property}.name",
					"{$this->object_id_property}.brother_of",
					"{$this->object_id_property}.status",
					"{$this->object_id_property}.class_id",
					"{$this->object_id_property}.ord"
				);
				$filter[aw_ini_get("classes.{$class_id}.def").".{$this->object_id_property}.status"] = new obj_predicate_not(object::STAT_DELETED);
			}

			list($oids, $meta_filter, $acldata, $parentdata, $objdata, $objdata_extended) = object_loader::ds()->search($filter, $props);
		}
		else
		{
			//TODO: 'greedy loading'. et tehtaks korraga suurem p2ring kui vaja. load_param kaudu peaks seda n6uda saama
			list($oids, $meta_filter, $acldata, $parentdata, $objdata) = object_loader::ds()->search($filter);
		}

		if (method_exists(object_loader::ds(), "last_search_query_string"))
		{
			$this->ds_query_string = object_loader::ds()->last_search_query_string();
		}

		if (!is_array($oids))
		{
			return false;
		}

		// set acldata to memcache
		if (is_array($acldata))
		{
			foreach($acldata as $a_oid => $a_dat)
			{
				$a_dat["status"] = $objdata[$a_oid]["status"];
				$GLOBALS["__obj_sys_acl_memc"][$a_oid] = $a_dat;
			}
		}

		if (count($meta_filter) > 0)
		{
			foreach($oids as $oid => $oname)
			{
				// perform authorization check if requested
				if ($this->__filter_by_user_access_action and !acl_base::can($this->__filter_by_user_access_action, $oid))
				{
					continue;
				}

				if ($this->object_id_property !== "oid")
				{
					if (isset($objdata_extended[$oid][$this->object_id_property]))
					{
						$load_oid = $objdata_extended[$oid][$this->object_id_property];
						$oname = $objdata_extended[$oid]["{$this->object_id_property}.name"];
						$list_objdata = array(
							"brother_of" => $objdata_extended[$oid]["{$this->object_id_property}.brother_of"],
							"status" => $objdata_extended[$oid]["{$this->object_id_property}.status"],
							"class_id" => $objdata_extended[$oid]["{$this->object_id_property}.class_id"],
							"ord" => $objdata_extended[$oid]["{$this->object_id_property}.ord"]
						);
					}
					else
					{
						throw new awex_objlist_oid_prop("Invalid key property '{$this->object_id_property}' specified");
					}
				}
				else
				{
					$load_oid = $oid;
					$list_objdata = $objdata[$oid];
				}

				$add = true;
				$_o = new object($load_oid);
				foreach($meta_filter as $mf_k => $mf_v)
				{
					if (is_object($mf_v))
					{
						error::raise(array(
							"id" => "ERR_META_FILTER",
							"msg" => sprintf(t("object_list::filter(%s => %s): can not complex searches on metadata fields!"), $mf_k, $mf_v)
						));
					}

					if ($mf_v{0} === "%")
					{
						error::raise(array(
							"id" => "ERR_META_FILTER",
							"msg" => sprintf(t("object_list::filter(%s => %s): can not do LIKE searches on metadata fields!"), $mf_k, $mf_v)
						));
					}

					$tmp = $_o->meta($mf_k);
					if (is_numeric($mf_v))
					{
						$tmp = (int)$tmp;
						$mf_v = (int)$mf_v;
					}

					if ($tmp != $mf_v)
					{
						$add = false;
					}
				}

				if ($add)
				{
					$this->list[$load_oid] = $_o;
					$this->list_names[$load_oid] = $oname;
					$this->list_objdata[$load_oid] = $list_objdata;
				}
			}
		}
		else
		{
			foreach($oids as $oid => $oname)
			{
				// perform authorization check if requested
				if ($this->__filter_by_user_access_action and !acl_base::can($this->__filter_by_user_access_action, $oid))
				{
					continue;
				}

				if ($this->object_id_property !== "oid")
				{
					if (isset($objdata_extended[$oid][$this->object_id_property]))
					{
						$load_oid = $objdata_extended[$oid][$this->object_id_property];
						$oname = $objdata_extended[$oid]["{$this->object_id_property}.name"];
						$list_objdata = array(
							"brother_of" => $objdata_extended[$oid]["{$this->object_id_property}.brother_of"],
							"status" => $objdata_extended[$oid]["{$this->object_id_property}.status"],
							"class_id" => $objdata_extended[$oid]["{$this->object_id_property}.class_id"],
							"ord" => $objdata_extended[$oid]["{$this->object_id_property}.ord"]
						);
					}
					else
					{
						throw new awex_objlist_oid_prop("Invalid key property '{$this->object_id_property}' specified");
					}
				}
				else
				{
					$load_oid = $oid;
					$list_objdata = $objdata[$oid];
				}

				$this->list[$load_oid] = new object($load_oid);
				$this->list_names[$load_oid] = $oname;
				$this->list_objdata[$load_oid] = $list_objdata;
			}
		}
	}

	function _int_init_empty()
	{
		$this->list = array();
		$this->list_names = array();
		$this->list_objdata = array();
	}

	function _int_sort_list($prop, $order)
	{
		$this->_sby_prop = $prop;
		$this->_sby_order = $order;

		$this->_int_sort_list_cb(array($this, "_int_sort_list_default_sort"));

		unset($this->_sby_prop);
		unset($this->_sby_order);
	}

	private function _int_sort_list_default_sort_get_val($a, $b, $sb)
	{
		if (object_loader::instance()->is_object_member_fun($sb))
		{
			$val1 = $a->$sb();
			$val2 = $b->$sb();
		}
		else
		{
			$val1 = $a->prop($sb);
			$val2 = $b->prop($sb);
		}
		return array($val1, $val2);
	}

	function _int_sort_list_default_sort($a, $b)
	{
		$sb = $this->_sby_prop;

		if (is_array($sb))
		{
			foreach($sb as $s_prop_name)
			{
				list($val1, $val2) = $this->_int_sort_list_default_sort_get_val($a, $b, $s_prop_name);
				if ($val1 != $val2)
				{
					break;
				}
			}
		}
		else
		{
			list($val1, $val2) = $this->_int_sort_list_default_sort_get_val($a, $b, $sb);
		}

		if ($val1 == $val2)
		{
			return 0;
		}

		if ($val1 < $val2)
		{
			if ($this->_sby_order === "asc")
			{
				return -1;
			}
			else
			{
				return 1;
			}
		}

		if ($val1 > $val2)
		{
			if ($this->_sby_order === "asc")
			{
				return 1;
			}
			else
			{
				return -1;
			}
		}
	}

	protected function _int_add_to_list($oid_arr)
	{
		foreach($oid_arr as $oid)
		{
			$this->list[$oid] = new object($oid);
			$this->list_names[$oid] = $this->list[$oid]->name();
			$this->list_objdata[$oid] = array(
				"brother_of" => $this->list[$oid]->brother_of()
			);
		}
	}

	protected function _int_get_at($oid)
	{
		if ((!isset($this->list[$oid]) || !is_object($this->list[$oid])) && is_oid($oid) && object_loader::can("", $oid))
		{//XXX: et kui list ei sisalda siis lihtsalt laetakse suvaline objekt vastavalt oid parameetrile?
			$this->list[$oid] = new object($oid);
		}
		return $this->list[$oid];
	}

	protected function _int_sort_list_cb($cb)
	{
		// cb is checked before getting here
		$this->cb = $cb;

		// init list
		foreach($this->list as $k => $v)
		{
			$cn = object_loader::ds()->can("", $k);
			if (is_oid($k) && $cn)
			{
				$this->_int_get_at($k);
			}
			else
			{
				unset($this->list[$k]);
			}
		}

		uasort($this->list, array($this, "_int_sort_list_cb_cb"));
		unset($this->cb);
	}

	protected function _int_sort_list_cb_cb($a, $b)
	{
		if (is_array($this->cb))
		{
			$tcb = $this->cb;
			return $tcb[0]->$tcb[1]($a, $b);
		}
		else
		{
			return $this->cb($a, $b);
		}
	}

	protected function _int_remove_from_list($oid_l)
	{
		foreach($oid_l as $oid)
		{
			unset($this->list[$oid]);
			unset($this->list_names[$oid]);
			unset($this->list_objdata[$oid]);
		}
	}

	protected function _int_fetch_full_list()
	{
		// go over list, gather inf on what objects need to be fetched
		$to_fetch = array();

		foreach($this->list as $oid => $obj)
		{
			if (!is_object($obj) && is_oid($oid))
			{
				$to_fetch[$oid] = $this->list_objdata[$oid]["class_id"];
			}
		}

		object_loader::ds()->fetch_list($to_fetch);
	}

	private function _is_single_clid()
	{
		$clid = null;
		foreach($this->arr() as $item)
		{
			if ($clid === null)
			{
				$clid = $item->class_id();
			}
			else
			if ($item->class_id() != $clid)
			{
				return false;
			}
		}
		return $clid;
	}
}

/** Generic objectlist exception. All objectlist extensions should also derive their exception classes from this **/
class awex_objlist extends aw_exception {}

/** Object id property error **/
class awex_objlist_oid_prop extends awex_objlist {}

/** Object id property specified without giving class id(s) **/
class awex_objlist_oid_class extends awex_objlist {}
