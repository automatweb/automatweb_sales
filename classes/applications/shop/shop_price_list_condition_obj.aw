<?php

class shop_price_list_condition_obj extends _int_object
{
	const CLID = 1573;

	public function save($check_state = false)
	{
		if(strlen($this->name()) === 0)
		{
			$this->set_name(sprintf("Hinnakirja '%s' tingimus", parse_obj_name($this->prop("price_list.name"))));
		}

		return parent::save($check_state);
	}
}

?>
