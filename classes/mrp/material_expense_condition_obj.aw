<?php

class material_expense_condition_obj extends _int_object
{

	const USE_DATE = 1;
	const DONT_USE_DATE = 0;

	const MOVE_NEVER = 0;
	const MOVE_AT_PLANNING = 1;
	const MOVE_AT_OPERATION = 2;

	function planning_options()
	{
		return array(
			self::DONT_USE_DATE => t("Tarnetingimust ei arvestata"),
			self::USE_DATE => t("Tarnetingimust arvestatakse"),
		);
	}

	function movement_options()
	{
		return array(
			self::MOVE_NEVER => t("Tootmislattu ei liigutata"),
			self::MOVE_AT_PLANNING => t("Liigutatakse planeerimisel"),
			self::MOVE_AT_OPERATION => t("Liigutatakse tagasiside korral"),
		);
	}
}

?>