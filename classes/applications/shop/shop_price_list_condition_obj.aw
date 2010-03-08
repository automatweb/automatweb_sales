<?php

class shop_price_list_condition_obj extends _int_object
{
	function save($exclusive = false, $previous_state = null)
	{
		if(strlen($this->name()) === 0)
		{
			$this->set_name(sprintf("Hinnakirja '%s' tingimus", parse_obj_name($this->prop("price_list.name"))));
		}

		return parent::save($exclusive, $previous_state);
	}
}

?>
