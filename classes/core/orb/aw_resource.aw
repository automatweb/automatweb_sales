<?php

class aw_resource
{
	protected $data = array(); // array of any type elements, actual resource data

	// metainformation
	protected $last_modified; // unix timestamp

	protected $data_chunk_separator = "\n";

	/**
	@attrib api=1 params=pos
	@returns array[mixed]
		Raw data as and in order it was set by applications executed.
	**/
	public function data()
	{
		return $this->data;
	}

	/**
	@attrib api=1 params=pos
	@param data type=mixed
	@returns void
	@comment
		Sets resource data. Replaces all previous data
	**/
	public function set_data($data)
	{
		$this->data = array($data);
	}

	/**
	@attrib api=1 params=pos
	@param data type=mixed
	@returns void
	@comment
		Adds resource data. Multiple calls add data not replace old.
	**/
	public function add_data($data)
	{
		$this->data[] = $data;
	}

	/**
	@attrib api=1 params=pos
	@returns void
	@comment
		Clears current resource data.
	**/
	public function clear_data()
	{
		$this->data = array();
	}

	/**
	@attrib api=1 params=pos
	@returns unixtimestamp
		When data was last modified
	**/
	public function last_modified()
	{
		return $this->last_modified;
	}

	/**
	@attrib api=1 params=pos
	@param time required type=unixtimestamp
	@returns void
	@comment
		Updates info about when data was last modified if $time is
		later than current value. Meant to be used from outside class
		to for example indicate to user agents cache statuses etc.
	**/
	public function set_last_modified($time)
	{
		$this->last_modified = max($this->last_modified, $time);
	}

	/**
	@attrib api=1 params=pos
	@returns void
		Output resource data.
	**/
	public function send()
	{
		echo $this;
	}

	public function __toString()
	{
		$line_separator = $this->data_chunk_separator;
		$value = "";
		foreach ($this->data as $data)
		{
			if (is_object($data) and is_callable(array($data, "__toString")))
			{
				$value .= $data->__toString();
			}
			elseif (is_scalar($data))
			{
				$value .= $data;
			}
			$value .= $line_separator;
		}
		return $value;
	}

	/** Attempts to instantly send $message to current application request maker
		@attrib api=1 params=pos
		@param message type=string
			Text to send. May contain line breaks
		@returns void
		@errors none
	**/
	public function sysmsg($message)
	{
		echo $message . $this->data_chunk_separator;
		flush();
	}
}
