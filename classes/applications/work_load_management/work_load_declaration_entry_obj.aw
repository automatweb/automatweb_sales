<?php

class work_load_declaration_entry_obj extends _int_object
{
	const CLID = 1775;

	public function val($k)
	{
		$map = array(
			"name" => "wl_name",
			"profession" => "wl_profession",
			"unit" => "wl_unit",
			"salary" => "wl_salary",
		);
		$k = isset($map[$k]) ? $map[$k] : $k;

		return $this->meta($k);
	}
}
