<?php

class currency_obj extends _int_object
{
	const CLID = 67;

	/**	Returns sum with currency symbol
		@attrib api=1 params=pos
		@param sum optional type=real default=0
			The sum to be formatted.
		@param precision optional type=int default=2
			The number of decimal places to be shown.
	**/
	public function sum_with_currency($sum = 0, $precision = 2)
	{
		return sprintf("%.{$precision}f %s", $sum, $this->prop("symbol"));
	}


	/** Returns form of currency name with value as specified in unit_name_morphology_spec
		@attrib api=1 params=pos
		@param value type=string
		@param lang_id type=int
		@comment
		@returns string
			sum plus space plus unit name if correct form not found
		@errors none
	**/
	public function get_string_for_sum($sum, $lang_id = AW_LANGUAGES_DEFAULT_CT_LID)
	{
		settype($sum, "string");
		$str = aw_locale::get_unit_string($sum, $this->trans_get_val("unit_name_morphology_spec", $lang_id));

		if ($str == $sum)
		{
			$str = $sum . " " . $this->name();
		}

		return $str;
	}

	/** Returns form of currency small unit name with value as specified in small_unit_morphology_spec
		@attrib api=1 params=pos
		@param value type=string
		@param lang_id type=int
		@comment
		@returns string
			sum plus space plus unit name if correct form not found
		@errors none
	**/
	public function get_small_unit_string_for_sum($sum, $lang_id = AW_LANGUAGES_DEFAULT_CT_LID)
	{
		settype($sum, "string");
		$str = aw_locale::get_unit_string($sum, $this->trans_get_val("small_unit_morphology_spec", $lang_id));

		if ($str == $sum)
		{
			$str = $sum . " " . $this->name();
		}

		return $str;
	}

	public function save($check_state = false)
	{
		if (!$this->is_saved())
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

			$name = $this->prop("unit_name");
			if (!$this->set_prop("unit_name_morphology_spec") and $name)
			{
				$this->set_prop("unit_name_morphology_spec", "1 {$name}; * {$name}{$guessed_plural_suffix}");
			}

			$name = $this->prop("small_unit_name");
			if (!$this->set_prop("small_unit_morphology_spec") and $name)
			{
				$this->set_prop("small_unit_morphology_spec", "1 {$name}; * {$name}{$guessed_plural_suffix}");
			}
		}

		return parent::save($check_state);
	}
}
