<?php

class shop_payment_type_conditions_obj extends _int_object
{
	const CLID = 1559;

	/**
		Returns array of all possible rent periods
	**/
	public function rent_periods()
	{
		$periods = array();

		for($i = $this->prop("period_min"); $i <= $this->prop("period_max"); $i += max($this->prop("period_step"), 1))
		{
			$periods[$i] = $i;
		}

		return $periods;
	}

	/**
		@attrib name=calculate_rent params=pos

		@param core_sum required type=float

		@param rent_period required type=int

		@param precision optional type=int default=2
	**/
	public function calculate_rent($core_sum, $period, $precision = 2)
	{
		$ret = array();
		if($core_sum < $this->prop("min_amt"))
		{
			if($this->prop("ignore_min_amt"))
			{
				$ret["warning"][] = html_entity_decode(sprintf(t("Minimaalne lubatud summa j&auml;relmaksuks on %s!"), $this->prop("min_amt")), ENT_QUOTES, aw_global_get("charset"));
			}
			else
			{
				return array(
					"error" => html_entity_decode(sprintf(t("Minimaalne lubatud summa j&auml;relmaksuks on %s!"), $this->prop("min_amt")), ENT_QUOTES, aw_global_get("charset")),
				);
			}
		}

		if($core_sum > $this->prop("max_amt") && $this->prop("max_amt") != 0)
		{
			$error = html_entity_decode(sprintf(t("Maksimaalne lubatud summa j&auml;relmaksuks on %s!"), $this->prop("max_amt")), ENT_QUOTES, aw_global_get("charset"));
			if($this->prop("ignore_max_amt"))
			{
				$ret["warning"][] = $error;
			}
			else
			{
				return array(
					"error" => $error,
				);
			}
		}

		if(!in_array($period, $this->rent_periods()))
		{
			$error = html_entity_decode(t("Valitud j&auml;relmaksuperiood ei ole lubatud!"), ENT_QUOTES, aw_global_get("charset"));
			return array(
				"error" => $error,
			);
		}

		$ret["prepayment"] = $prepayment = round($core_sum * $this->prop("prepayment_interest") / 100, $precision);
		$ret["single_payment"] = $single_payment = round(max($core_sum - $prepayment, 0) * (1 + $this->prop("yearly_interest") / 12 / 100 * $period) / $period, $precision);
		$ret["sum_rent"] = $single_payment * $period + $prepayment;

		if($single_payment < $this->prop("min_payment"))
		{
			$error = html_entity_decode(sprintf(t("Minimaalne lubatud summa igakuiseks osamakseks on %s!"), $this->prop("min_payment")), ENT_QUOTES, aw_global_get("charset"));
			if($this->prop("ignore_min_payment"))
			{
				$ret["warning"][] = $error;
			}
			else
			{
				return array(
					"error" => $error,
				);
			}
		}


		return $ret;
	}
	public function prop($k)
	{
		switch($k)
		{
			case "min_amt":
			case "max_amt":
			case "min_payment":
			case "prepayment_interest":
			case "yearly_interest":
			case "period_min":
			case "period_max":
			case "period_step":
				return aw_math_calc::string2float(parent::prop($k));

			default:
				return parent::prop($k);
		}
	}

	public function set_prop($k, $v)
	{
		switch($k)
		{
			case "valid_to":
				$d = explode("-", date("d-m-Y", $v));
				return $v = mktime(23, 59, 59, $d[0], $d[1], $d[2]);

			default:
				return parent::set_prop($k, $v);
		}
	}

	public function description()
	{
		return is_oid($this->id()) ? sprintf(t("%s %s kuni %s %s (min %s %s)<br />Sissemakse %s%%, intress %s%% aastas<br />%s kuni %s kuud (samm %s)"),
			$unit = $this->prop("currency.symbol"),
			$this->prop("min_amt"),
			$unit,
			$this->prop("max_amt") > 0 ? $this->prop("max_amt") : "&#8734;",
			$unit,
			$this->prop("min_payment"),
			$this->prop("prepayment_interest"),
			$this->prop("yearly_interest"),
			$this->prop("period_min"),
			$this->prop("period_max"),
			$this->prop("period_step")
		) : t("M&auml;&auml;ramata");
	}
}

?>
