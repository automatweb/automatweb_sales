<?php

class aw_inventory_obj extends _int_object
{
	const AW_CLID = 1710;
}

class inventory_transaction
{
	private $item = 0;
	private $quantity = 0;
	private $from_inventory;
	private $to_inventory;
	private $requester;
	private $date = 0;

	public function __construct($item = 0, $quantity = 0)
	{
		$this->set_item($item);
		$this->set_item($quantity);
	}

	public function set_inventory(object $from, object $to)
	{
		if (!$from->is_a(CL_AW_INVENTORY) )
		{
			throw new awex_obj_type("Inventory required. Invalid 'from' parameter: " . $from->__toString());
		}

		if (!$to->is_a(CL_AW_INVENTORY))
		{
			throw new awex_obj_type("Inventory required. Invalid 'to' parameter: " . $to->__toString());
		}

		if ($id === (int) $id and $id >= 0)
		{
			$this->from_inventory = $from;
			$this->to_inventory = $to;
		}
		else
		{
			throw new awex_obj_type("Invalid item id: " . var_export($id, true));
		}
	}

	public function get_from()
	{
		return $this->from_inventory;
	}

	public function get_to()
	{
		return $this->to_inventory;
	}

	public function set_requester($requester)
	{
		if (!is_scalar($requester))
		{
			throw new awex_obj_type("Requester not a scalar value: " . var_export($id, true));
		}

		$this->requester = $requester;
	}

	public function get_requester()
	{
		return $this->requester;
	}

	public function set_date($unix_timestamp)
	{
		if (!is_int($unix_timestamp) or $unix_timestamp < 2)
		{
			throw new awex_obj_type("Invalid timestamp parameter: " . var_export($unix_timestamp, true));
		}

		$this->date = $unix_timestamp;
	}

	public function get_date()
	{
		return $this->date;
	}

	public function set_item($id)
	{
		if ($id === (int) $id and $id >= 0)
		{
			$this->item = $id;
		}
		else
		{
			throw new awex_obj_type("Invalid item id: " . var_export($id, true));
		}
	}

	public function get_item()
	{
		return $this->item;
	}

	public function set_quantity($value)
	{
		if (is_numeric($value) and !is_string($value) and is_finite($value) and $value != 0)
		{
			$this->quantity = $value;
		}
		else
		{
			throw new awex_obj_type("Invalid quantity value: " . var_export($value, true));
		}
	}

	public function get_quantity()
	{
		return $this->quantity;
	}
}

/** Generic inventory exception **/
class awex_inventory extends awex_obj {}

/** Inventory item related exception **/
class awex_inventory_item extends awex_inventory {}
