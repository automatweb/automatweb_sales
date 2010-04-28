<?php

namespace automatweb;

/*

@classinfo maintainer=markop

*/
class budgeting_tax_folder_relation_obj extends _int_object
{
	const AW_CLID = 1319;

	function prop($prop)
	{
		switch($prop)
		{
			case "amount_final":
			case "amount":
			case "max_deviation_minus":
			case "max_deviation_plus":
			case "term":
				if(!$this->prop("use_different_settings"))
				{
					if(is_oid($this->prop("use_used_settings")))
					{
						$source = obj($this->prop("use_used_settings"));
						if($source->prop("use_different_settings"))
						{
							return $source->prop($prop);
						}
					}
					if(is_oid($this->prop("tax")))
					{
						$tax = obj($this->prop("tax"));
						if($tax->is_property($prop))
						{
							return $tax->prop($prop);
						}
					}
				}
				break;
		}
		return parent::prop($prop);
	}

	function set_prop($pn, $pv)
	{
		switch($pn)
		{
			case "use_different_settings":
			if($pv)
			{
				$this->set_prop("use_used_settings" , 0);
			}
		}

		return parent::set_prop($pn, $pv);
	}

	function set_props_from($id)
	{
		$other_object = obj($id);
		$this -> set_prop("amount_final"  , $other_object -> prop("amount_final"));
		$this -> set_prop("amount"  , $other_object -> prop("amount"));
		$this -> set_prop("max_deviation_minus"  , $other_object -> prop("max_deviation_minus"));
		$this -> set_prop("max_deviation_plus"  , $other_object -> prop("max_deviation_plus"));
		$this -> set_prop("term"  , $other_object -> prop("term"));
		$this -> set_prop("pri"  , $other_object -> prop("pri"));
		$this->set_prop("use_different_settings" , 1);
		$this->save();
	}

	function get_transfer_amount($sum = 0)
	{
		if($this->prop("amount_final"))
		{
			return $this->prop("amount_final");
		}
		else
		{
			return $this->prop("amount") * $sum / 100;
		}
	}

	function get_needed_amount($sum = 0)
	{
		if($this->prop("amount_final"))
		{
			return $sum + $this->prop("amount_final");
		}
		else
		{
			if($this->prop("amount") < 100 && $this->prop("amount") > 0)
			{
				return 100 * $sum / (100 - $this->prop("amount"));
			}
			else return $sum;
		}

	}

}
?>
