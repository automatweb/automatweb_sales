<?php

class crm_offer_row_obj extends _int_object
{
	protected $price_components;

	/**
		@attrib api=1
	**/
	public function price_component_is_applied($price_component)
	{
		$price_component_id = is_object($price_component) ? $price_component->id() : $price_component;

		if (!isset($this->price_components))
		{
			$this->load_price_components();
		}

		return isset($this->price_components[$price_component_id]);
	}
	
	/**
		@attrib api=1
	**/
	public function apply_price_component($price_component, $value, $price_change)
	{
		$price_component_id = is_object($price_component) ? $price_component->id() : $price_component;

		if (!is_oid($price_component_id))
		{
			throw new awex_crm_offer_row_price_component(sprintf(t("Price component OID must be valid OID! Received: '%s'."), $price_component_id));
		}
		
		if (!$this->is_saved())
		{
			throw new awex_crm_offer_row(t("Offer row must be saved before applied price components can be added!"));
		}

		if (!isset($this->price_components))
		{
			$this->load_price_components();
		}

		$value = aw_math_calc::string2float($value);
		$price_change = aw_math_calc::string2float($price_change);
		if (!$this->price_component_is_applied($price_component_id))
		{
			$q = sprintf("
				INSERT INTO aw_crm_offer_row_price_components
				(aw_row_id, aw_price_component_id, aw_value, aw_price_change) 
				VALUES
				(%u, %u, %f, %f)
			", $this->id(), $price_component_id, $value, $price_change);
			$this->instance()->db_query($q);
		}
		elseif ($this->price_components[$price_component_id]["value"] !== $value || $this->price_components[$price_component_id]["price_change"] !== $price_change)
		{
			$q = sprintf("
				UPDATE aw_crm_offer_row_price_components
				SET aw_value = %f, aw_price_change = %f
				WHERE aw_row_id = %u AND aw_price_component_id = %u
			", $value, $price_change, $this->id(), $price_component_id);
			$this->instance()->db_query($q);
		}
	}
	
	/**
		@attrib api=1
	**/
	public function remove_price_component($price_component)
	{
		$price_component_id = is_object($price_component) ? $price_component->id() : $price_component;

		if (!is_oid($price_component_id))
		{
			throw new awex_crm_offer_row_price_component(sprintf(t("Price component OID must be valid OID! Received: '%s'."), $price_component_id));
		}
		
		if (!$this->is_saved())
		{
			throw new awex_crm_offer_row(t("Offer row must be saved before applied price components can be removed!"));
		}

		$q = sprintf("
			DELETE FROM aw_crm_offer_row_price_components 
			WHERE aw_row_id = %u AND aw_price_component_id = %u
		", $this->id(), $price_component_id);
		$this->instance()->db_query($q);
	}

	/**
		@attrib api=1
	**/
	public function get_value_for_price_component($price_component)
	{
		$price_component_id = is_object($price_component) ? $price_component->id() : $price_component;

		if (!is_oid($price_component_id))
		{
			throw new awex_crm_offer_row_price_component(sprintf(t("Price component OID must be valid OID! Received: '%s'."), $price_component_id));
		}

		if (!isset($this->price_components))
		{
			$this->load_price_components();
		}
		if (!isset($this->price_components[$price_component_id]))
		{
			throw new awex_crm_offer_row_price_component(sprintf(t("No crm_sales_price_component_obj with OID %u applied to crm_offer_row_obj with OID %u!"), $price_component_id, $this->id()));
		}

		return $this->price_components[$price_component]["value"];
	}

	protected function load_price_components()
	{
		if (!$this->is_saved())
		{
			throw new awex_crm_offer_row(t("Offer row must be saved before applied price components can be loaded!"));
		}

		$q = sprintf("SELECT * FROM aw_crm_offer_row_price_components WHERE aw_row_id = %u", $this->id());
		$price_components = $this->instance()->db_fetch_array($q);
		
		$this->price_components = array();
		foreach($price_components as $price_component)
		{
			if (is_oid($price_component["aw_price_component_id"]))
			{
				$this->price_components[$price_component["aw_price_component_id"]] = array(
					"value" => aw_math_calc::string2float($price_component["aw_value"]),
					"price_change" => aw_math_calc::string2float($price_component["aw_price_change"]),
				);
			}
		}
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
}

/** Generic crm offer error **/
class awex_crm_offer_row extends awex_crm {}

/** Error concerning price components applied to row **/
class awex_crm_offer_row_price_component extends awex_crm_offer_row {}

?>
