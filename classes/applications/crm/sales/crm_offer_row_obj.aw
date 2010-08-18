<?php

class crm_offer_row_obj extends crm_offer_price_component_handler
{
	public function awobj_get_name()
	{
		$name = parent::name();
		return strlen($name) > 0 ? $name : $this->prop("object.name");
	}

	public function awobj_get_comment()
	{
		$comment = parent::comment();
		return strlen($comment) > 0 ? $comment : $this->prop("object.comment");
	}

	public function awobj_get_amount()
	{
		return aw_math_calc::string2float(parent::prop("amount"));
	}

	/**
		@attrib api=1
		@returns float
		@errors Throws awex_crm_offer if this offer is not saved
	**/
	public function get_price()
	{
		if(!$this->is_saved())
		{
			throw new awex_crm_offer("Offer row must be saved before price can be calculated!");
		}

		$total = 0;

		foreach($this->offer()->get_price_components_for_row(new object($this->id()))->ids() as $price_component_id)
		{
			if($this->price_component_is_applied($price_component_id))
			{
				$total += $this->get_price_for_price_component($price_component_id);
			}
		}

		return $total;
	}

	/**	Returns an array of applicable class IDs
		@attrib api=1
		@returns int[]
	**/
	public static function get_applicable_clids()
	{
		$possible_classes = class_index::get_classes_by_interface("crm_offer_row_interface");
		$possible_clids = array();
		foreach($possible_classes as $possible_class)
		{
			$constant_name = $possible_class."::AW_CLID";
			if (defined($constant_name))
			{
				$possible_clids[] = constant($constant_name);
			}
		}

		return $possible_clids;
	}

	public function offer()
	{
		return new object($this->prop("offer"));
	}
}

?>
