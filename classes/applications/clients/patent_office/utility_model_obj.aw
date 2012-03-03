<?php

class utility_model_obj extends intellectual_property_obj
{
	const CLID = 1433;

	const APPLICANT_REG_AUTHOR = 1;
	const APPLICANT_REG_AUTHOR_SUCCESOR = 2;
	const APPLICANT_REG_EMPLOYEE = 5;
	const APPLICANT_REG_OTHER_CONTRACT = 6;

	public static function get_applicant_reg_options()
	{
		return array(
			self::APPLICANT_REG_AUTHOR => t("autor"),
			self::APPLICANT_REG_AUTHOR_SUCCESOR => t("autori &otilde;igusj&auml;rglane"),
			self::APPLICANT_REG_EMPLOYEE => t("isik vastavalt t&ouml;&ouml;lepingule"),
			self::APPLICANT_REG_OTHER_CONTRACT => t("isik vastavalt lepingule, v&auml;lja arvatud t&ouml;&ouml;lepingule")
		);
	}
}
