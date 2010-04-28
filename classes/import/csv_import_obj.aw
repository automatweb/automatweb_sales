<?php

namespace automatweb;


/*
@classinfo maintainer=voldemar
*/

class csv_import_obj extends _int_object
{
	const AW_CLID = 1562;

	public function save($exclusive = false, $previous_state = null)
	{
		if (!is_class_id($this->prop("import_class")))
		{
			throw new awex_csv_import("Imported object class not defined.");
		}
		return parent::save($exclusive, $previous_state);
	}

	public function awobj_get_property_map()
	{
		$property_map = (array) $this->prop("property_map");
		$fields = (int) $this->prop("csv_fields_in_record");
		if (count($property_map) !== $fields)
		{ // number of fields has been changed, old configuration doesn't apply
			$property_map = array();
		}
		return $property_map;
	}

	/**
	@attrib api=1 params=pos
	@param data type=string
		CSV data to be imported
	@returns void
	**/
	public function import($data)
	{
		$record_separator = $this->prop("csv_record_separator");
		$records = explode($record_separator, $data);
		$definition = $this->awobj_get_property_map();
		foreach ($records as $record)
		{
			$record = new csv_record($record);
			$o = $record->import();
			$o->save();
		}
	}
}

class csv_record
{
	private $raw_data = "";
	private $data = array();
	private $cfg;
	private $fields = array();
	private $o;

	public function __construct($data, csv_import_definition $cfg)
	{
		$this->cfg = $cfg;
		$this->raw_data = $data;
		$this->fields = $this->cfg->get_fields();
	}

	public function import()
	{
		$data = explode($this->cfg->field_separator, $this->raw_data);
		$this->data = $data;
		reset($data);
		$o = $this->get_object();

		foreach ($this->fields as $nr => $field)
		{
			$value = current($data);
			$field->set_value($value);
			next($data);
		}
	}

	// returns aw object to import record to, according to current conditions
	// requires processed data to be loaded (self::$data)
	private function get_object()
	{
		if (count($this->data) < 1)
		{
			throw new awex_csv_import("CSV data not loaded, can't load object");
		}

		if (count($this->cfg->unique_fields))
		{ // find if an object with value in those unique field properties exists
			$filter = array(
				"class_id" => $this->cfg->class_id,
				"lang_id" => array(),
				"site_id" => array()
			);

			foreach ($this->cfg->unique_fields as $field_nr)
			{
				$prop = $this->fields[$field_nr]->property();
				$val = $this->data[$field_nr];
				$filter[$prop] = $val;
			}

			$list = new object_list($filter);
			if ($list->count() === 1)
			{
				$o = $list->begin();
			}
			elseif ($list->count() > 1)
			{
				//!!! throw new ... v6i salvesta veateade vms.
			}
		}

		if (!is_object($this->o))
		{
			$o = obj(null, array(), $this->cfg->class_id);
		}

		return $o;
	}
}

class csv_import_definition
{
	public $field_separator = "\t";
	public $class_id;
	public $unique_fields = array();

	private $fields = array();

	public function add_field(csv_field $field)
	{
		$order_nr = count($this->fields);
		$this->fields[$order_nr] = $field;
		return $order_nr;
	}

	public function get_fields()
	{
		return $this->fields;
	}
}

class csv_field
{
	private $property;
	private $controller;
	private $value;

	public function property($name = null)
	{
		if (isset($name))
		{
			$this->property = $name;
		}
		else
		{
			return $this->property;
		}
	}

	public function set_controller(object $controller)
	{
		$this->controller = $controller;
	}

	public function set_value($value)
	{
		if (!is_string($value))
		{
			throw new awex_csv_import("Value not a string");
		}
		$this->value = $value;
	}

	public function import(object $o, csv_record $record)
	{
		if (is_object($this->controller))
		{
			$retval = 0;
			eval($controller_inst->trans_get_val("formula"));
			$retval;
		}
		$value = "";
	}
}

/** Generic csv import error **/
class awex_csv_import extends awex_obj {}

/** Definition and real format mismatch **/
class awex_csv_import_record extends awex_csv_import {}

/** Real field count differs from defined **/
class awex_csv_import_field_count extends awex_csv_import_record {}

?>
