<?php

class cfgform_obj extends _int_object
{
	/**
		@attrib params=pos
		@param property required type=string/array
			Property/properties to be removed
	**/
	public function remove_property($properties)
	{
		$removed_properties = safe_array($this->meta("removed_properties"));
		foreach((array)$properties as $property)
		{
			$removed_properties[$property] = true;
		}
		$this->set_meta("removed_properties", $removed_properties);
	}

	/**
		@attrib params=pos
		@param property required type=string/array
			Property/properties to be restored
	**/
	public function restore_property($properties)
	{
		$removed_properties = safe_array($this->meta("removed_properties"));
		foreach((array)$properties as $property)
		{
			if(isset($removed_properties[$property]))
			{
				unset($removed_properties[$property]);
			}
		}
		$this->set_meta("removed_properties", $removed_properties);
	}

	/**
		@attrib params=pos
		@param group required type=string/array
			Group/groups to be hidden
	**/
	public function hide_group($groups)
	{
		$this->_showhide_group($groups, 1);
	}

	/**
		@attrib params=pos
		@param group required type=string/array
			Group/groups to be shown
	**/
	public function show_group($groups)
	{
		$this->_showhide_group($groups, 0);
	}

	/**
		@attrib params=pos
		@param group required type=string/array
	**/
	public function group_is_hidden($group)
	{
		switch($group)
		{
			case "relationmgr":
				return (bool)$this->prop("classinfo_disable_relationmgr");

			default:
				$cfg_groups = safe_array($this->meta("cfg_groups"));
				return !empty($cfg_groups[$group]["grphide"]);
		}
	}

	protected function _showhide_group($groups, $value)
	{
		$cfg_groups = safe_array($this->meta("cfg_groups"));
		foreach((array)$groups as $group)
		{
			switch($group)
			{
				case "relationmgr":
					$this->set_prop("classinfo_disable_relationmgr", $value);
					break;

				default:
					if(isset($cfg_groups[$group]))
					{
						$cfg_groups[$group]["grphide"] = $value;
					}
					break;
			}
		}
		$this->set_meta("cfg_groups", $cfg_groups);
	}

	public function meta($k = false)
	{
		$retval = parent::meta($k);

		if($k === "cfg_proplist")
		{
			$removed_properties = safe_array($this->meta("removed_properties"));
			foreach(array_keys($removed_properties) as $k)
			{
				if(isset($retval[$k]))
				{
					unset($retval[$k]);
				}
			}
		}

		return $retval;
	}
}

?>