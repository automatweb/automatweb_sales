<?php
// range VCL component
/*
@classinfo maintainer=dragut
*/
class range extends class_base
{
	var $name;
	var $from;
	var $to;
	var $from_size;
	var $to_size;

	function range()
	{
	// if there is a need fo templates and database manipulation functionsm then 
	// $this->init() initializes those
	//	$this->init("vcl/range");

		$this->set_textbox_size(array(
			'from' => 10,
			'to' => 10
		));
		
	}

	function init_vcl_property($arr)
	{
		$saved_value = $arr['obj_inst']->prop($arr['property']['name']);

		if (is_array($saved_value))
		{
			$this->set_range($saved_value);
		}

		$this->name = $arr["property"]["name"];
		$vcl_inst = $this;
		$res = $arr["property"];
		$res["vcl_inst"] = &$vcl_inst;

		return array($this->name => $res);
	}

	function process_vcl_property($arr)
	{
	
	}

	function get_html()
	{

		$from_params = array(
			'name' => $this->name.'[from]',
			'value' => $this->from,
		);
		if (!empty($this->from_size))
		{
			$from_params['size'] = $this->from_size;
		}

		$str = html::textbox($from_params);

		$str .= ' - ';

		$to_params = array(
			'name' => $this->name.'[to]',
			'value' => $this->to,
		);
		if (!empty($this->to_size))
		{
			$to_params['size'] = $this->to_size;
		}

		$str .= html::textbox($to_params);
		return $str;
	}

	/** Sets the size of the range textboxes
		@attrib name=set_textbox_size params=name api=1
		@param from type=int optional default=10
			The size of the from (first) textbox (used as input (type="text") size property value)
		@param to type=int optional default=10
			The size of the to (second) textbox (used as input (type="text") size property value
		@errors none
		@returns none
		@examples none
			$r = $arr['prop']['vcl_inst'];
			$r->set_textbox_size(array(
				'from' => 12,
				'to' => 30
			));
			// if you want to change, for example, only the first textbox size:
			$r->set_textbox_size(array(
				'from' => 20
			)); 
			
	**/
	function set_textbox_size($arr)
	{
		if (!empty($arr['from']))
		{
			$this->from_size = (int)$arr['from'];
		}

		if (!empty($arr['to']))
		{
			$this->to_size = (int)$arr['to'];
		}
	}

	/** Set the values of the range
		@attrib name=set_range params=name api=1
		@param from type=int optional default=10
			The 'from' value of the range
		@param to type=int optional default=10
			The 'to' value of the range
		@errors none
		@returns none
		@examples none
			$r = $arr['prop']['vcl_inst'];
			$r->set_range(array(
				'from' => 10,
				'to' => 30
			));
			// if you want to set, for example, only 'from' value of the range:
			$r->set_range(array(
				'from' => 20
			)); 
			
	**/
	function set_range($arr)
	{

		if (is_numeric($arr['from']) || empty($arr['from']))
		{
			$this->from = $arr['from'];
		}

		if (is_numeric($arr['to']) || empty($arr['to']))
		{
			$this->to = $arr['to'];
		}
	}

}
?>
