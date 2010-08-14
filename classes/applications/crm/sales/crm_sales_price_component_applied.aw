<?php

class crm_sales_price_component_applied
{
	protected $price_component;
	protected $value = 0;
	protected $price_change = 0;

	public function __construct(object $price_component, $value = 0, $price_change = 0)
	{
		$this->price_component = $price_component;
		$this->value = $value;
		$this->price_change = $price_change;
	}

	public function __get($name)
	{
		return $this->price_component->$name;
	}

	public function __call($name, $args)
	{
		if(is_callable(array($this->price_component, $name)))
		{
			return call_user_func_array(array($this->price_component, $name), $args);
		}
	}

	public function value()
	{
		return $this->value;
	}

	public function price()
	{
		return $this->price_change;
	}
}

?>
