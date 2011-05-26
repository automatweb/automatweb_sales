<?php

class industrial_design_obj extends intellectual_property_obj
{
	const CLID = 1452;

	const APPLICANT_REG_AUTHOR = 1;
	const APPLICANT_REG_AUTHOR_SUCCESOR = 2;
	const APPLICANT_REG_EMPLOYER = 3;
	const APPLICANT_REG_OTHER = 4;

	const VARIANT_ONE = 1;
	const VARIANT_MANY = 2;
	const VARIANT_COMPLEX = 3;

	public static function get_applicant_reg_options()
	{
		return array(
			self::APPLICANT_REG_AUTHOR => t("autor"),
			self::APPLICANT_REG_AUTHOR_SUCCESOR => t("autori &otilde;igusj&auml;rglane"),
			self::APPLICANT_REG_EMPLOYER => t("t&ouml;&ouml;andja"),
			self::APPLICANT_REG_OTHER => t("muu isik vastavalt lepingule")
		);
	}

	public static function get_industrial_design_variant_options()
	{
		return array(
			self::VARIANT_ONE => t("&uuml;ks t&ouml;&ouml;stusdisainilahendus"),
			self::VARIANT_MANY => t("variant(id)"),
			self::VARIANT_COMPLEX => t("t&ouml;&ouml;stusdisaini komplekt")
		);
	}

	public static function get_industrial_design_variant_count_options()
	{
		$r = range(1, 12);
		$r = array_merge(array("" => ""), $r);
		return array_combine($r, $r);
	}

	public static function get_process_postpone_options()
	{
		$r = range(1, 12);
		$options = array(0 => t("--")) + array_combine($r, $r);
		return $options;
	}

	public function awobj_set_industrial_design_variant($value)
	{
		$options = self::get_industrial_design_variant_options();

		if (!empty($value) and !isset($options[$value]))
		{
			throw new awex_obj_type("Not a valid option.");
		}

		parent::set_prop("industrial_design_variant", $value);
	}

	public function awobj_set_applicant_reg($value)
	{
		$options = self::get_applicant_reg_options();

		if (!empty($value) and !isset($options[$value]))
		{
			throw new awex_obj_type("Not a valid option.");
		}

		parent::set_prop("applicant_reg", $value);
	}

	public function prop_str($param, $is_oid = null)
	{
		if ("applicant_reg" === $param or "industrial_design_variant" === $param)
		{
			$value = $this->prop($param);
			$m = "get_{$param}_options";
			$options = $this->$m();
			$value = isset($options[$value]) ? $options[$value] : "";
			return $value;
		}
		else
		{
			return parent::prop_str($param, $is_oid);
		}
	}
}

?>
