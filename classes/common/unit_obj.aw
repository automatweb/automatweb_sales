<?php

class unit_obj extends _int_object
{
	const CLID = 1126;

	const QTY_LENGTH = 1;
	const QTY_MASS = 2;
	const QTY_QTY = 3;
	const QTY_VOLUME = 4;
	const QTY_TIME = 5;

	private static $quantity_names = array();

	/** Returns list of quantity names a unit can measure
	@attrib api=1 params=pos
	@param quantity type=int
		quantity constant value to get name for, one of unit_obj::QTY_*
	@returns array
		Format option value => human readable name, if $quantity parameter set, array with one element returned and empty array when that quantity not found.
	**/
	public static function quantity_names($quantity = null)
	{
		if (empty(self::$quantity_names))
		{
			self::$quantity_names = array(
				self::QTY_LENGTH => t("Pikkus"),
				self::QTY_MASS => t("Mass"),
				self::QTY_QTY => t("Hulk"),
				self::QTY_VOLUME => t("Ruumala"),
				self::QTY_TIME => t("Aeg")
			);
		}

		if (isset($quantity))
		{
			if (isset(self::$quantity_names[$quantity]))
			{
				$quantity_names = array($quantity => self::$quantity_names[$quantity]);
			}
			else
			{
				$quantity_names = array();
			}
		}
		else
		{
			$quantity_names = self::$quantity_names;
		}

		return $quantity_names;
	}

	/** Returns an object_list of all unit objects
		@attrib api=1
		@returns object_list
	**/
	public static function get_all_units()
	{
		return new object_list(array(
			"class_id" => CL_UNIT,
		));
	}

	/** Returns form of unit name for given value
		@attrib api=1 params=pos
		@param value type=string
		@comment
		@returns string
		@errors
	**/
	public function get_name_for_value($value)
	{
		settype($value, "string");
		if ("1" === $value)
		{
			$name = $this->prop("name_for_1");
		}
		elseif ("2" === $value)
		{
			$name = $this->prop("name_for_2");
		}
		else
		{
			$name = $this->prop("name_for_n");
		}

		if ($name)
		{
			return $name;
		}
		else
		{
			return $this->name();
		}
	}

	public function save($check_state = false)
	{
		if ($this->name())
		{
			if (!$this->prop("name_for_1"))
			{
				$this->set_prop("name_for_1", $this->name());
			}

			if (!$this->prop("name_for_2"))
			{
				$this->set_prop("name_for_2", $this->name());
			}

			if (!$this->prop("name_for_n"))
			{
				$this->set_prop("name_for_n", $this->name());
			}
		}

		return parent::save($check_state);
	}
}
