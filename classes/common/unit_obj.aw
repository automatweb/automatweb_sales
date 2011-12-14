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
			"class_id" => self::CLID
		));
	}

	/** Returns an array of selectable/active units data
		@attrib api=1 params=pos
		@param lang_id type=int default=AW_REQUEST_UI_LANG_ID
		@returns array
			int unit_oid => string translated_name
	**/
	public static function get_selection($lang_id = AW_REQUEST_UI_LANG_ID)
	{
		static $unit_names = array();

		if (!isset($unit_names[$lang_id]))
		{
			$units = new object_list(array(
				"class_id" => self::CLID,
				"status" => object::STAT_ACTIVE,
				new obj_predicate_sort(array("jrk" => obj_predicate_sort::ASC))
			));

			$unit_names[$lang_id] = array();
			if($units->count())
			{
				$unit = $units->begin();

				do
				{
					$unit_names[$lang_id][$unit->id()] = $unit->trans_get_val("name", $lang_id);
				}
				while ($unit = $units->next());
			}
		}

		return $unit_names[$lang_id];
	}

	/** Returns form of unit name with value as specified in unit_name_morphology_spec
		@attrib api=1 params=pos
		@param value type=string
		@comment
		@returns string
			Value plus space plus unit name if correct form not found
		@errors none
	**/
	public function get_string_for_value($value, $lang_id = AW_LANGUAGES_DEFAULT_CT_LID)
	{
		settype($value, "string");
		$str = aw_locale::get_unit_string($value, $this->trans_get_val("unit_name_morphology_spec", $lang_id));

		if ($str === $value)
		{
			$str = $value . " " . $this->name();
		}

		return $str;
	}

	public function save($check_state = false)
	{
		$name = $this->name();
		if (!$this->is_saved() and !$this->set_prop("unit_name_morphology_spec") and $name)
		{ // predefine morphology spec
			if (languages::LC_EST == aw_global_get("lang_id"))
			{
				$guessed_plural_suffix = "t";
			}
			elseif (languages::LC_ENG == aw_global_get("lang_id"))
			{
				$guessed_plural_suffix = "s";
			}
			else
			{
				$guessed_plural_suffix = "";
			}
			$this->set_prop("unit_name_morphology_spec", "1 {$name}; * {$name}{$guessed_plural_suffix}");
		}

		return parent::save($check_state);
	}
}
