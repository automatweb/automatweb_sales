<?php

class crm_offer_price_component_handler extends _int_object
{
	protected $applied_price_components;
	protected $applied_price_components_loaded = false;

	/**
		@attrib api=1
	**/
	public function price_component_is_applied($price_component)
	{
		if(!$this->applied_price_components_loaded)
		{
			try
			{
				$this->load_applied_price_components();
			}
			catch(awex_crm_offer_price_component $e)
			{
				throw $e;
			}
		}

		$price_component_id = is_object($price_component) ? $price_component->id() : $price_component;

		return isset($this->applied_price_components[$price_component_id]);
	}
	
	/**
		@attrib api=1
	**/
	public function apply_price_component($price_component, $value, $price_change)
	{
		$price_component_id = is_object($price_component) ? $price_component->id() : $price_component;

		if (!is_oid($price_component_id))
		{
			throw new awex_crm_offer_price_component(sprintf(t("Price component OID must be valid OID! Received: '%s'."), $price_component_id));
		}

		if(!$this->applied_price_components_loaded)
		{
			try
			{
				$this->load_applied_price_components();
			}
			catch(awex_crm_offer_price_component $e)
			{
				throw $e;
			}
		}

		$value = aw_math_calc::string2float($value);
		$price_change = aw_math_calc::string2float($price_change);
		if (!$this->price_component_is_applied($price_component_id))
		{
			$q = sprintf("
				INSERT INTO aw_crm_offer_row_price_components
				(aw_object_id, aw_price_component_id, aw_value, aw_price_change) 
				VALUES
				(%u, %u, %f, %f)
			", $this->id(), $price_component_id, $value, $price_change);
			$this->instance()->db_query($q);
		}
		elseif ($this->applied_price_components[$price_component_id]["value"] !== $value || $this->applied_price_components[$price_component_id]["price_change"] !== $price_change)
		{
			$q = sprintf("
				UPDATE aw_crm_offer_row_price_components
				SET aw_value = %f, aw_price_change = %f
				WHERE aw_object_id = %u AND aw_price_component_id = %u
			", $value, $price_change, $this->id(), $price_component_id);
			$this->instance()->db_query($q);

			$this->applied_price_components[$price_component_id]["value"] = $value; $this->applied_price_components[$price_component_id]["price_change"] = $price_change;
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
			throw new awex_crm_offer_price_component(sprintf(t("Price component OID must be valid OID! Received: '%s'."), $price_component_id));
		}

		if(!$this->applied_price_components_loaded)
		{
			try
			{
				$this->load_applied_price_components();
			}
			catch(awex_crm_offer_price_component $e)
			{
				throw $e;
			}
		}

		if($this->price_component_is_applied($price_component_id))
		{
			$q = sprintf("
				DELETE FROM aw_crm_offer_row_price_components 
				WHERE aw_object_id = %u AND aw_price_component_id = %u
			", $this->id(), $price_component_id);
			$this->instance()->db_query($q);

			unset($this->applied_price_components[$price_component_id]);
		}
	}

	/**
		@attrib api=1
	**/
	public function get_value_for_price_component($price_component)
	{
		$price_component_id = is_object($price_component) ? $price_component->id() : $price_component;

		if (!is_oid($price_component_id))
		{
			throw new awex_crm_offer_price_component(sprintf(t("Price component OID must be valid OID! Received: '%s'."), $price_component_id));
		}

		if(!$this->applied_price_components_loaded)
		{
			try
			{
				$this->load_applied_price_components();
			}
			catch(awex_crm_offer_price_component $e)
			{
				throw $e;
			}
		}

		if (!isset($this->applied_price_components[$price_component_id]))
		{
			throw new awex_crm_offer_price_component(sprintf(t("No crm_sales_price_component_obj with OID %u applied to crm_offer_row_obj with OID %u!"), $price_component_id, $this->id()));
		}

		return $this->applied_price_components[$price_component]["value"];
	}

	protected function load_applied_price_components()
	{
		if (!$this->is_saved())
		{
			throw new awex_crm_offer(t("Object must be saved before applied price components can be loaded!"));
		}

		$q = sprintf("SELECT * FROM aw_crm_offer_row_price_components WHERE aw_object_id = %u", $this->id());
		$price_components = $this->instance()->db_fetch_array($q);
		
		$this->applied_price_components = array();
		foreach($price_components as $price_component)
		{
			if (is_oid($price_component["aw_price_component_id"]))
			{
				$this->applied_price_components[$price_component["aw_price_component_id"]] = array(
					"value" => aw_math_calc::string2float($price_component["aw_value"]),
					"price_change" => aw_math_calc::string2float($price_component["aw_price_change"]),
				);
			}
		}

		$this->applied_price_components_loaded = true;
	}
}

/** Error concerning price components applied to row or offer **/
//	class awex_crm_offer_price_component extends awex_crm_offer {}	// For whatever reason, awex_crm_offer couldn't be found.
class awex_crm_offer_price_component extends awex_crm {}

?>