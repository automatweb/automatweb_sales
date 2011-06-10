<?php

class add_tree_conf_obj extends _int_object
{
	const CLID = 118;

	const BEHAVIOUR_RESTRICTIVE = 1;
	const BEHAVIOUR_PERMISSIVE = 2;

	public function awobj_get_behaviour()
	{
		$value = (int) parent::prop("behaviour");
		if (empty($value))
		{
			$value = self::BEHAVIOUR_RESTRICTIVE;
		}
		return $value;
	}

	public function cb_access($class, $method)
	{
	}
}

?>
