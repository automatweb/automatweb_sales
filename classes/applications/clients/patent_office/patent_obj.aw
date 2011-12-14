<?php

class patent_obj extends intellectual_property_obj
{
	const CLID = 1181;

	public function get_type_options()
	{
		return array(
			t("S&otilde;nam&auml;rk"),
			t("Kujutism&auml;rk"),
			t("Kombineeritud m&auml;rk"),
			t("Ruumiline m&auml;rk")
		);
	}

	public function get_trademark_type_options()
	{
		return array(t("Kollektiivkaubam&auml;rk"),t("Garantiim&auml;rk"));
	}
}
