<?php

/** Generic automatweb exception **/
class aw_exception extends Exception
{
	protected $forwarded_exceptions = array();

	public function set_forwarded_exception(Exception $e)
	{
		$this->forwarded_exceptions[] = $e;
	}

	public function get_forwarded_exceptions()
	{
		return $this->forwarded_exceptions;
	}
}

/////////////////// general purpose classes for common exception conditions ///////////////////

/** Indicates that instruction has already been carried out **/
class awex_redundant_instruction extends aw_exception
{
}

/** Function or method parameter error **/
class awex_param extends aw_exception
{
	public $param_name;

	public function set($param_name, $given_value)
	{
		$this->param_name = $param_name;
		return $this;
	}
}

/** Function or method parameter type mismatch/error **/
class awex_param_type extends awex_param
{
	public $given_value;

	public function set($param_name, $given_value)
	{
		parent::set($param_name);
		$this->given_value = $given_value;
		return $this;
	}
}

/** Feature is not available **/
class awex_not_available extends aw_exception
{
}

/** Feature is not implemented **/
class awex_not_implemented extends aw_exception
{
}
