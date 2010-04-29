<?php

class crm_sales_price_component_restriction_obj extends _int_object
{
	public function prop($k)
	{
		switch ($k)
		{
			case "lower_tolerance":
				return $this->has_lower_tolerance() ? aw_math_calc::string2float(parent::prop("lower_tolerance")) : NULL;

			case "upper_tolerance":
				return $this->has_upper_tolerance() ? aw_math_calc::string2float(parent::prop("upper_tolerance")) : NULL;
		}

		return parent::prop($k);
	}

	public function awobj_set_compulsory($v)
	{
		return parent::set_prop("compulsory", $v ? 1 : 0);
	}

	public function awobj_set_lower_tolerance($v)
	{
		$this->set_prop("has_lower_tolerance", strlen($v) > 0 ? 1 : 0);
		$v = aw_math_calc::string2float($v);
		return $this->set_prop("lower_tolerance", $v);
	}

	public function awobj_set_upper_tolerance($v)
	{
		$this->set_prop("has_upper_tolerance", strlen($v) > 0 ? 1 : 0);
		$v = aw_math_calc::string2float($v);
		return $this->set_prop("upper_tolerance", $v);
	}

	/**	Returns true if lower tolerance is defined within this restriction, false otherwise.
		@attrib api=1
		@returns boolean
	**/
	public function has_lower_tolerance()
	{
		return $this->prop("has_lower_tolerance") > 0;
	}

	/**	Returns true if upper tolerance is defined within this restriction, false otherwise.
		@attrib api=1
		@returns boolean
	**/
	public function has_upper_tolerance()
	{
		return $this->prop("has_upper_tolerance") > 0;
	}
}

?>
