<?php
/*
@classinfo  maintainer=voldemar
*/

require_once(aw_ini_get("basedir") . "/classes/common/address/as_header.aw");

class country_administrative_structure_object extends _int_object
{
	public static $unit_classes = array (
		CL_COUNTRY_ADMINISTRATIVE_UNIT,
		CL_COUNTRY_CITY,
		CL_COUNTRY_CITYDISTRICT
	);

	var $as_unit_classes = array (
		CL_COUNTRY_ADMINISTRATIVE_UNIT,
		CL_COUNTRY_CITY,
		CL_COUNTRY_CITYDISTRICT,
	);
	var $as_address_classes = array (
		CL_COUNTRY_ADMINISTRATIVE_UNIT,
		CL_COUNTRY_CITY,
		CL_COUNTRY_CITYDISTRICT,
		CL_ADDRESS_STREET,
	);
	var $as_structure_array;

	const CACHE_FOLDER = "aw_address_system";

	public function prop ($param)
	{
		if (is_array ($param))
		{
			$name = $param["prop"];

			switch ($name)
			{
				case "unit_by_name":
					return $this->as_get_unit_by_name ($param);

				case "units_by_division":
					return $this->as_get_units_by_division ($param);

				case "units_by_country":
					return $this->as_get_units_by_country ($param);

				case "addresses_by_unit":
					return $this->as_get_addresses_by_unit ($param["unit"]);
			}
		}
		else
		{
			switch ($param)
			{
				case "structure_array":
					return $this->as_get_structure ();

				default:
					return parent::prop ($param);
			}
		}
	}

	public function set_prop ($name, $param)
	{
		switch ($name)
		{
			case "unit_by_name":
				return $this->as_add_adminunit ($param);

			case "unit_index":
				return $this->as_index_unit ($param);

			case "structure_array":
			case "units_by_division":
				return;

			default:
				return parent::set_prop ($name, $param);
		}
	}

	public function save($exclusive = false, $previous_state = null)
	{
		$this->as_save();
		$this->invalidate_cache();
		return parent::save($exclusive, $previous_state);
	}

	private function invalidate_cache()
	{
		cache::file_clear_pt_oid(self::CACHE_FOLDER, $this->id());
	}

	/**
		@attrib api=1 params=pos
		@returns object_list
			All administrative divisions in this structure definition
		@errors
	**/
	public function get_divisions()
	{
		$divisions = new object_list($this->connections_from(array("type" => "RELTYPE_ADMINISTRATIVE_DIVISION")));
		return $divisions;
	}

	/**
		@attrib api=1 params=pos
		@param unit type=oid
		@comment
		@returns array
			All ancestor units as object id array up to before country itself
		@errors
	**/
	public function get_ancestor_unit_ids($unit)
	{
		$index = (array) $this->meta("unit_hierarchy_index");
		$parent_unit_ids = array();
		while (isset($index[$unit]))
		{
			$unit = $index[$unit];

			if ($unit !== $this->id())
			{
				$parent_unit_ids[] = $unit;
			}
		}
		array_pop($parent_unit_ids);// remove country
		return $parent_unit_ids;
	}

	/**
		@attrib api=1 params=pos
		@param parent type=oid
		@comment
		@returns array
			All descendant units as object id array
		@errors
	**/
	public function get_descendant_unit_ids($parent)
	{
		$index = (array) $this->meta("unit_hierarchy_index");
		$parents = array($parent);
		$descendants = array();

		while (count($parents))
		{
			$children = array();

			foreach ($parents as $parent)
			{
				$children = array_merge($children, array_keys($index, $parent));
			}

			$parents = $children;
			$descendants = array_merge($descendants, $children);
		}

		return $descendants;
	}

	// returns object list of all addresses under $unit and its subunits
	private function as_get_addresses_by_unit($unit)
	{
		if (!is_object($unit))
		{
			error::raise(array(
				"msg" => sprintf(t("administrative_structure::as_get_addresses_by_unit(): invalid unit parameter."))
			));
		}

		$parent_unit_ids = $this->get_descendant_unit_ids($unit->id());
		$list = new object_list(array(
			"class_id" => CL_ADDRESS,
			"parent" => $parent_unit_ids
		));

		return $list;
	}

	private function as_index_unit($unit)
	{
		$unit_index = (array) $this->meta("unit_hierarchy_index");
		$unit_index[$unit->id()] = $unit->parent();
		$this->set_meta("unit_hierarchy_index", $unit_index);
	}

    // @attrib name=as_get_structure
	// @returns
	private function as_get_structure ()
	{
		if (!isset($this->as_structure_array))
		{
			$this->as_structure_array = array ();

			foreach ($this->connections_from (array ("type" => "RELTYPE_ADMINISTRATIVE_DIVISION")) as $connection)
			{
				$division = $connection->to ();
				$this->as_structure_array[$division->ord ()] = $division;
			}

			ksort ($this->as_structure_array);
		}

		return $this->as_structure_array;
	}

	public function add_administrative_unit($name, $parent, $division)
	{
		return $this->as_add_adminunit(array(
			"name" => $name,
			"parent" => $parent,
			"division" => $division
		));
	}

    // @attrib name=as_add_adminunit
	// @param name required
	// @param parent required
	// @param division required
	// @returns Created unit object. If existing unit with $name was found that will be returned.
	// @comment division is object or oid of object from class CL_COUNTRY_ADMINISTRATIVE_DIVISION or ADDRESS_STREET_TYPE in case a street is to be added
	private function as_add_adminunit ($arr)
	{
		### validate division object
		if (is_object ($arr["division"]))
		{
			$admin_division = $arr["division"];
		}
		elseif ($this->can ("view", $arr["division"]))
		{
			$admin_division = obj ($arr["division"]);
		}

		### get subclass and class
		if (is_object ($admin_division))
		{
			if ($admin_division->class_id () != CL_COUNTRY_ADMINISTRATIVE_DIVISION)
			{
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo sprintf ("adminstructure::as_add_adminunit: adminunit division class wrong [%s]", $admin_division->class_id ()).AS_NEWLINE; }
				return false;
			}

			$class_id = $admin_division->prop ("type");
			$subclass = $admin_division->id ();
		}
		elseif (ADDRESS_STREET_TYPE == (string) $arr["division"])
		{
			$class_id = CL_ADDRESS_STREET;
			$subclass = 0;
		}
		else
		{
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo "adminstructure::as_add_adminunit: division undefined [{$arr["division"]}]".AS_NEWLINE; }
			return false;
		}

		### search for existing unit by name
		$arr["type"] = $class_id;
		$o = $this->as_get_unit_by_name ($arr);

		if ($o === false)
		{
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo "adminstructure::as_add_adminunit: existing unit search fail".AS_NEWLINE; }
			return false;
		}
		elseif (!is_object ($o))
		{ ### add new
			$parent = is_object ($arr["parent"]) ? $arr["parent"]->id () : $arr["parent"];
			$name = trim ($arr["name"]);

			if (is_oid ($parent))
			{
				$o = new object ();
				$o->set_class_id ($class_id);
				$o->set_parent ($parent);
				$o->set_subclass ($subclass);
				$o->set_name ($name);
				$o->save ();
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo "adminstructure::as_add_adminunit: added object [{$name}] under [{$parent}] with subclass [{$subclass}]".AS_NEWLINE; }
			}
			else
			{
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo "adminstructure::as_add_adminunit: invalid parent [{$parent}]".AS_NEWLINE; }
				return false;
			}
		}

		### ...
		return $o;
	}

    // @attrib name=as_get_unit_by_name
	// @param name required
	// @param parent required
	// @param type required
	// @param calling_address_obj_oid optional for address system internal use
	// @returns Unit object corresponding to name.
	private function as_get_unit_by_name ($arr)
	{
		$name = trim ($arr["name"]);
		$parent = is_object ($arr["parent"]) ? $arr["parent"]->id () : $arr["parent"];
		$class_id = (int) $arr["type"];

		if (empty ($name) or !in_array ($class_id, $this->as_address_classes))
		{
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo "adminstructure::as_get_unit_by_name: name [{$name}] empty or type [{$class_id}] wrong".AS_NEWLINE; }
			return false;
		}

		### switch user because anyone has to be able to see all addresses and delete duplicates
		$admin_user = $this->prop ("address_admin");

		if (empty ($admin_user))
		{
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo "adminstructure::as_get_unit_by_name: admin user not defined for admin structure".AS_NEWLINE; }
			return false;
		}

		aw_switch_user (array ("uid" => $admin_user));//!!! eemaldada. acl-iga peab m22rama 6igusi, mitte siin neist m88da minema.

		### search for existing unit
		$list = new object_list (array (
			"class_id" => $class_id,
			"parent" => $parent,
			"name" => array ($name)
		));

		if ($list->count () == 1)
		{
			$o = $list->begin ();
		}
		elseif ($list->count () > 1)
		{ ### structure contains duplicates
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo "adminstructure::as_get_unit_by_name: duplicates found for name [{$name}] under parent [{$parent}]".AS_NEWLINE; }
			### move everything from under redundant admin units unto one, selected randomly (?)
			$o = $list->begin ();
			$list->remove ($o->id ());
			$redundant_unit = $list->begin ();

			### don't save currently saved address to avoid recursive address::save() call
			if (is_oid ($arr["calling_address_obj_oid"]))
			{
				$oid_constraint = new obj_predicate_not ($arr["calling_address_obj_oid"]);
			}
			else
			{
				$oid_constraint = NULL;
			}

			while (is_object ($redundant_unit))
			{
				$child_list = new object_list (array (
					"oid" => $oid_constraint,
					"parent" => $redundant_unit->id ()
				));
				$child_list->set_parent ($o->id ());
				$child_list->save ();
				$redundant_unit = $list->next ();
			}

			### delete redundant admin units
			$list->delete ();
		}
		else
		{
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo "adminstructure::as_get_unit_by_name: no objects found for name [{$name}]".AS_NEWLINE; }
		}

		### switch user back
		aw_restore_user ();

		### return found unit
		if (isset($o) && is_object ($o))
		{
			return $o;
		}
	}

	/**
		@attrib name=as_get_units_by_country
		@param country required
		@param division optional
		@param depth optional
		@returns AW object tree of admin units of $country
	**/
	protected function as_get_units_by_country ($arr)
	{
		if(!empty($arr["depth"]))
		{
			$divisions = array();
			$division_objs = array_slice($this->prop("structure_array"), 0, $arr["depth"]);
			foreach($division_objs as $division_obj)
			{
				$divisions[] = $division_obj->id();
			}
			$arr["division"] = !empty($arr["division"]) ? array_intersect($arr["division"], $division) : $divisions;
		}

		$args = array(
			"parent" => obj($arr["country"])->prop("administrative_structure"),
			"class_id" => array(CL_COUNTRY_CITY, CL_COUNTRY_CITYDISTRICT, CL_COUNTRY_ADMINISTRATIVE_UNIT),
			"subclass" => ifset($arr, "division")
		);
		$ot = new object_tree($args);
		return $ot;
	}

    // @attrib name=as_get_units_by_division
	// @param division required
	// @param parent optional type=int
	// @returns AW object list of admin units corresponding to $division
	private function as_get_units_by_division ($arr)
	{
		$division = $arr["division"];

		### validate division object
		if (is_object ($division))
		{
			$class = $division->prop ("type");
			$subclass = $division->id ();
		}
		elseif ($this->can ("view", $division))
		{
			$division = obj ($division);
			$class = $division->prop ("type");
			$subclass = $division->id ();
		}
		else
		{
// /* dbg */ if (!empty($_GET[ADDRESS_DBG_FLAG])) { echo "adminstructure::get_units_by_division: division not defined [{$division}]".AS_NEWLINE; }
			return false;
		}

		### get parent
		if ($arr["parent"])
		{
			$parent = $arr["parent"];
		}
		else
		{
			$parent = NULL;
		}

		### get units
		$args = array (
			"class_id" => $class,
			"parent" => $parent,
			"subclass" => $subclass
		);
		$list = new object_list ($args);

		return $list;
	}

	private function as_save()
	{
		//// create division hierarchy sequence
		$division_topology = array();
		$divisions = array();

		// get divisions
		foreach ($this->connections_from (array ("type" => "RELTYPE_ADMINISTRATIVE_DIVISION")) as $connection)
		{
			$division = $connection->to();
			$divisions[$division->id()] = array($division->prop("parent_division"));
		}

		// sort structure topologically
		foreach ($divisions as $division_id => $parent)
		{
			$degree = 0;
			$nodes = array ($division_id);

			// recursively go through all current division's parents
			do
			{
				if ($degree > count ($divisions))
				{
					break;
				}

				$current_nodes = $nodes;

				foreach ($current_nodes as $current_node)
				{
					// add new parent
					if (!empty($divisions[$current_node]))
					{
						$nodes = array_merge ($nodes, $divisions[$current_node]);
					}

					// remove current node from nodes to visit
					$checked_node = array_keys ($nodes, $current_node);
					$checked_node = $checked_node[0];
					unset ($nodes[$checked_node]);
				}

				// increment arc count
				$degree++;
			}
			while (!empty ($nodes));

			$division_topology[$degree][] = $division_id;
		}

		// sort by degree
		ksort ($division_topology);

		// convert topology to sequence
		$sequence = array ();

		foreach ($division_topology as $degree => $degree_divisions)
		{
			$sequence = array_merge ($sequence, $degree_divisions);
		}

		$sequence[] = ADDRESS_STREET_TYPE;
		$this->set_meta("as_division_hierarchy_sequence", $sequence);

		// index current units
		$unit_index = (array) $this->meta("unit_hierarchy_index");
		$units = new object_data_list(
			array(
				"administrative_structure" => $this->id(),
				"indexed" => 0,
				"class_id" => self::$unit_classes
			),
			array(
				CL_COUNTRY_ADMINISTRATIVE_UNIT => array("oid", "parent"),
				CL_COUNTRY_CITY => array("oid", "parent"),
				CL_COUNTRY_CITYDISTRICT => array("oid", "parent")
			)
		);
		$units =  $units->arr();

		foreach ($units as $unit_data)
		{
			$unit_index[$unit_data["oid"]] = $unit_data["parent"];
		}

		$this->set_meta("unit_hierarchy_index", $unit_index);
		return true;
	}
}

?>
