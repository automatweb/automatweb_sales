<?php

require_once(AW_DIR . "classes/common/address/as_header.aw");

class address_object extends _int_object
{
	const CLID = 948;

	public static $unit_classes = array (
		CL_COUNTRY_ADMINISTRATIVE_UNIT,
		CL_COUNTRY_CITY,
		CL_COUNTRY_CITYDISTRICT
	);

	public function awobj_get_administrative_structure()
	{
		return new object(parent::prop("administrative_structure"));
	}

	public function awobj_set_administrative_structure(object $administrative_structure)
	{
		if (!$administrative_structure->is_a(CL_COUNTRY_ADMINISTRATIVE_STRUCTURE))
		{
			throw new awex_obj_type("Value must be CL_COUNTRY_ADMINISTRATIVE_STRUCTURE object");
		}

		if (!$administrative_structure->is_saved())
		{
			throw new awex_obj_type("Administrative structure must be an existing object");
		}

		$country = new object($administrative_structure->prop ("country"), array(), CL_COUNTRY);

		if (!$country->is_saved())
		{
			throw new awex_as_address_country("Country not defined in administrative structure");
		}

		parent::set_prop("country", $country->id());
		return parent::set_prop("administrative_structure", $administrative_structure->id());
	}

	public function set_name($name)
	{
		return;
	}

	public function awobj_set_country($country)
	{
		if (is_oid ($country))
		{
			$country = obj ($country, array(), CL_COUNTRY);
		}
		elseif (!$country->is_saved())
		{
			throw new awex_obj_type("Country must be an existing object");
		}

		if (!$country->is_a(CL_COUNTRY))
		{
			throw new awex_obj_type("Value must be CL_COUNTRY object");
		}

		$administrative_structure = $country->get_current_admin_structure();
		parent::set_prop("administrative_structure", $administrative_structure->id());
		return parent::set_prop("country", $country->id());
	}

	public function awobj_set_street($name)
	{
		$this->set_prop("street", ucfirst($name));
	}

	public function save($check_state = false)
	{
		$address_text = "";

		// street house etc.
		if (strlen($this->prop("street")))
		{
			$address_text = $this->prop("street") . " ";
		}

		if (strlen($this->prop("house")))
		{
			$address_text .= trim($this->prop("house"));

			if (strlen($this->prop("apartment")))
			{
				$address_text .= "-" . trim($this->prop("apartment"));
			}
		}
		
		if (strlen($this->prop("details")))
		{
			$address_text .= sprintf(t(" (%s)"), $this->prop("details"));
		}

		// location
		$location = array();
		$ancestor_unit = new object($this->parent());
		while (in_array($ancestor_unit->class_id(), self::$unit_classes))
		{
			$location[] = $ancestor_unit->name();
			$ancestor_unit = new object($ancestor_unit->parent());
		}
		$address_text .= (empty($address_text) ? "" : " ") . implode(", ", $location);

		// ...
		parent::set_name($address_text);
		return parent::save($check_state);
	}

    /**
	@attrib api=1 params=pos
	@param unit type=CL_COUNTRY_ADMINISTRATIVE_UNIT
		Administrative unit object or id
	@comment Sets address location.
	**/
	public function set_location($unit)
	{
		### get&validate admin division
		if (is_oid ($unit))
		{
			$unit = obj ($unit, null, country_administrative_division_obj::CLID);
		}
		elseif (!is_object($unit) or !$unit->is_a(CL_COUNTRY_ADMINISTRATIVE_DIVISION))
		{
			throw new awex_as_address("Invalid administrative unit given as address location.");
		}

		$division = obj ($unit->subclass (), array(), CL_COUNTRY_ADMINISTRATIVE_DIVISION);

		### check if all specified unit is in the same admin structure as others
		$admin_structure = $this->awobj_get_administrative_structure();

		if ($admin_structure->id() != $division->prop ("administrative_structure"))
		{
			throw new awex_as_admin_structure(sprintf ("division [%s] admin structure [%s] different from current [%s]. division var: [%s]", $division->id (), $division->prop ("administrative_structure"), $admin_structure->id()));
		}

		$this->set_parent($unit->id());
	}

    // @attrib name=set_unit_by_name
	// @param division required
	// @param name required
	// @comment Sets administrative unit corresponding to given division (admin division object, oid or ADDRESS_STREET_TYPE)
	private function set_unit_by_name ($arr) // unused at the moment
	{
		### validate name. validation needed here to give a chance to avoid corruptions in address structure -- spot errors before any changes made.
		if (empty ($arr["name"]))
		{
			return false;
		}

		### get&validate admin division
		if (is_object ($arr["division"]))
		{
			$division = $arr["division"];

			if ($division->class_id () != CL_COUNTRY_ADMINISTRATIVE_DIVISION)
			{
				return false;
			}
		}
		elseif (is_oid ($arr["division"]))
		{
			$division = obj ($arr["division"]);

			if ($division->class_id () != CL_COUNTRY_ADMINISTRATIVE_DIVISION)
			{
				return false;
			}
		}
		elseif (ADDRESS_STREET_TYPE == (string) $arr["division"])
		{
			$division = ADDRESS_STREET_TYPE;
		}
		else
		{
			return false;
		}

		### check if all specified unit is in the same admin structure as others
		$this->as_load_structure ();

		if (is_object ($division) and $this->as_administrative_structure->id () != $division->prop ("administrative_structure"))
		{
			return false;
		}

		### ...
		$ord = is_object ($division) ? $division->ord() : $division;
		$division_id = is_object ($division) ? $division->id() : $division;
		$this->as_address_data[$division_id] = array (
			"ord" => $ord,
			"name" => (string) $arr["name"]
		);
	}

    // @attrib name=as_get_unit_encoded
	// @param division required
	// @param encoding required
	// @returns String encoded value for unit of $division.
	private function as_get_unit_encoded ($arr)
	{
		### get&validate admin division
		if (is_object ($arr["division"]))
		{
			$division = $arr["division"];

			if ($division->class_id () != CL_COUNTRY_ADMINISTRATIVE_DIVISION)
			{
				return false;
			}
		}
		elseif (is_oid ($arr["division"]))
		{
			$division = obj ($arr["division"]);

			if ($division->class_id () != CL_COUNTRY_ADMINISTRATIVE_DIVISION)
			{
				return false;
			}
		}
		elseif (ADDRESS_STREET_TYPE == (string) $arr["division"])
		{
			$division = ADDRESS_STREET_TYPE;
		}
		else
		{
			return false;
		}

		### get&validate encoding
		if (is_object ($arr["encoding"]))
		{
			$encoding = $arr["encoding"];
		}
		elseif (is_oid ($arr["encoding"]))
		{
			$encoding = obj ($arr["encoding"]);
		}
		else
		{
			return false;
		}

		if ($encoding->class_id () != CL_COUNTRY_ADMINISTRATIVE_STRUCTURE_ENCODING)
		{
			return false;
		}

		$division_id = is_object ($division) ? $division->id() : $division;
		$param = array (
			"prop" => "encoding_by_unit",
			"unit" => $this->as_address_data[$division_id]["id"],
		);
		$encoded_value = $encoding->prop ($param);
		return $encoded_value;
	}

	/**	Returns the the object in JSON
		@attrib api=1
	**/
	public function json($encode = true)
	{
		$city = $county = $vald = null;
		$parent = obj($this->parent());
		$grandparent = obj($parent->parent());
		$greatgrandparent = obj($grandparent->parent());

		if ($parent->is_a(CL_COUNTRY_CITY))
		{
			$city = $parent;
		}
		else
		{
			$vald = $parent;
		}

		if($greatgrandparent->is_a(CL_COUNTRY_ADMINISTRATIVE_UNIT))
		{
			$county = $greatgrandparent;
		}
		elseif($grandparent->is_a(CL_COUNTRY_ADMINISTRATIVE_UNIT))
		{
			$county = $grandparent;
		}
		elseif($parent->is_a(CL_COUNTRY_ADMINISTRATIVE_UNIT))
		{
			$county = $parent;
		}

		$data = array(
			"id" => $this->id(),
			"name" => $this->prop("name"),
			"country" => array("id" => $this->prop("country"), "name" => $this->prop("country.name")),
			"county" => $county !== null ? array("id" => $county->id, "name" => $county->name) : null,
			"city" => $city !== null ? array("id" => $city->id, "name" => $city->name) : null,
			"vald" => $vald !== null ? array("id" => $vald->id, "name" => $vald->name) : null,
			"street" => $this->prop("street"),
			"house" => $this->prop("house"),
			"apartment" => $this->prop("apartment"),
			"postal_code" => $this->prop("postal_code"),
			"coord_x" => $this->prop("coord_x"),
			"coord_y" => $this->prop("coord_y"),
			"details" => $this->prop("details"),
		);

		$json = new json();
		return $encode ? $json->encode($data, aw_global_get("charset")) : $data;
	}
}

/** Generic address error **/
class awex_as_address extends awex_as {}

/** Country related error **/
class awex_as_address_country extends awex_as_address {}

