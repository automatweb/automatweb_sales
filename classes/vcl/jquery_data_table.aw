<?php

class jquery_data_table extends aw_template
{
	protected $id;
	protected $name;
	protected $json;
	protected $configuration = array();
	protected $columns = array();
	protected $data = array();
	protected $vertical_grouping = false;
	protected $vertical_grouping_columns;
	protected $vertical_grouping_case_sensitive = false;
	protected $editable = false;
	protected $editable_configuration = array();

	public function __construct(array $prms = array())
	{
		$this->id = uniqid("dataTable");
		/* For JSON encoding, using this instead of json_encode to deal with non-UTF-8 encoded data. */
		$this->json = new json(0, aw_global_get("charset"));
		
		$this->init(array(
			"tpldir" => "vcl/jquery_data_tables",
		));

		$this->__process_parameters($prms);
	}

	public function get_id()
	{
		return $this->id;
	}

	public function get_name()
	{
		return $this->name;
	}

	public function set_name($name)
	{
		$this->name = $name;
	}

	/**
		@attrib api=1 params=pos
		@param columns required type=array
			Array of column definitions
		@comment
			Structure of the column definition array:
			array(
				array(
					caption => "column caption",
					[ data_index => "index of a column in the data row (e.g. "name" or "product.name")" ],
					[ children => array("column definitions of child columns") ],
				)
			);
			Column definitions with children do not need 'data_index'.
		@examples
			$table->set_columns(array(
				array("caption" => t("Order number"), "data_index" => "number"),
				array("caption" => t("Purchaser"), "children" => array(
					array("caption" => "Name", "data_index" => "purchaser.name"),
					array("caption" => "E-mail", "data_index" => "purchaser.email"),
				)),
			));
	**/
	public function set_columns(array $columns)
	{
		$this->columns = $columns;
		$this->__set_column_configuration();
	}

	public function set_data(array $data)
	{
		$this->data = $data;
	}

	public function get_data()
	{
		return $this->data;
	}

	public function enable_vertical_grouping(array $columns, bool $case_sensitive = null)
	{
		$this->vertical_grouping = true;
		$this->vertical_grouping_columns = $columns;
	}

	public function disable_vertical_grouping()
	{
		$this->vertical_grouping = false;
	}

	public function enable_pagination()
	{
		unset($this->configuration["bPaginate"]);
	}

	public function disable_pagination()
	{
		$this->configuration["bPaginate"] = false;
	}

	public function enable_filtering()
	{
		unset($this->configuration["bFilter"]);
	}

	public function disable_filtering()
	{
		$this->configuration["bFilter"] = false;
	}

	public function enable_sorting()
	{
		unset($this->configuration["bSort"]);
	}

	public function disable_sorting()
	{
		$this->configuration["bSort"] = false;
	}

	public function enable_info()
	{
		unset($this->configuration["bInfo"]);
	}

	public function disable_info()
	{
		$this->configuration["bInfo"] = false;
	}

	public function render()
	{
		$this->__load_required_javascripts();
		$this->__load_required_stylesheets();

		$this->read_template("default.tpl");

		return $this->__parse();
	}

	protected function __parse()
	{
		$this->vars(array(
			"id" => $this->get_id(),
			"name" => $this->get_name(),
			"configuration" => $this->json->encode($this->configuration)
		));
		

		if ($this->is_template("HEADER"))
		{
			$this->vars(array(
				"HEADER" => $this->__parse_header(),
			));
		}
		if ($this->is_template("DATA"))
		{
			$this->vars(array(
				"DATA" => $this->__parse_data(),
			));
		}
		if ($this->vertical_grouping and $this->is_template("VERTICAL_GROUPING"))
		{
			$this->vars(array(
				"VERTICAL_GROUPING" => $this->__parse_vertical_grouping(),
			));
		}
		if ($this->editable and $this->is_template("EDITABLE"))
		{
			$this->vars(array(
				"EDITABLE" => $this->__parse_editable(),
			));
		}

		return parent::parse();
	}

	protected function __parse_header()
	{
		$this->vars(array(
			"HEADER.ROW" => $this->__parse_header_rows($this->columns),
		));

		return $this->parse("HEADER");
	}

	protected function __parse_header_rows(array $columns)
	{
		$children = array();

		$HEADER_COLUMN = "";
		// FIXME: This is not a good solution. I think we should have classes like html_table, html_tr, html_td etc, to elegantly modify rowspan of a td after creating it.
		$header_height = $this->__columns_depth($columns);
		foreach ($columns as $column)
		{
			$this->vars(array(
				"caption" => $column["caption"],
				"colspan" => empty($column["children"]) ? 1 : $this->__calculate_header_colspan($column["children"]),
				"rowspan" => empty($column["children"]) ? $header_height : 1,
			));
			$HEADER_COLUMN .= $this->parse("HEADER.COLUMN");

			if (!empty($column["children"]))
			{
				$children += $column["children"];
			}
		}
		$this->vars(array(
			"HEADER.COLUMN" => $HEADER_COLUMN,
		));

		return empty($children) ? $this->parse("HEADER.ROW") : $this->parse("HEADER.ROW") . $this->__parse_header_rows($children);
	}

	protected function __parse_data()
	{
		if ($this->is_template("DATA.JSON"))
		{
			$this->vars(array(
				"data.json" => $this->json->encode(array_values($this->data)),
			));

			$this->vars(array(
				"DATA.JSON" => $this->parse("DATA.JSON"),
			));
		}

		// TODO: Finish it once you actually need it!
		if ($this->is_template("DATA.ROW"))
		{
			$DATA_ROW = "";

			foreach ($this->data as $row)
			{
				$DATA_ROW .= $this->parse("DATA.ROW");
			}

			$this->vars(array(
				"DATA.ROW" => $DATA_ROW
			));
		}

		return $this->parse("DATA");
	}

	protected function __parse_vertical_grouping()
	{
		$this->vars(array(
			"fnMultiRowspan.aSpannedColumns" => $this->json->encode($this->vertical_grouping_columns),
			"fnMultiRowspan.bCaseSensitive" => $this->vertical_grouping_case_sensitive ? "true" : "false"
		));
		
		return $this->parse("VERTICAL_GROUPING");
	}

	protected function __parse_editable()
	{
		$this->vars(array(
			"editable.configuration" => $this->json->encode($this->editable_configuration)
		));

		return $this->parse("EDITABLE");
	}

	protected function __set_column_configuration($column = null)
	{
		if ($column === null)
		{
			foreach ($this->columns as $column)
			{
				$this->__set_column_configuration($column);
			}
		}
		else
		{
			if(empty($column["children"]))
			{
				$configuration = array(
					"mDataProp" => $column["data_index"]
				);
				if (isset($column["default"]))
				{
					$configuration["sDefaultContent"] = $column["default"];
				}
				if (isset($column["render"]))
				{
					$configuration["fnRender"] = $column["render"];
				}
				$this->configuration["aoColumns"][] = $configuration;

				/* Editable configuration */
				if (isset($column["editable"]) and $column["editable"] === false)
				{
					$this->editable_configuration["aoColumns"][] = null;
				}
				else
				{
					$editable_configuration = array();
					if (isset($column["type"]))
					{
						$editable_configuration["type"] = $column["type"];
						if ("select" === $column["type"])
						{
							$editable_configuration["data"] = !empty($column["options"]) ? $column["options"] : array("F" => "Fan");
						}
					}
					if (isset($column["onedit"]))
					{
						$editable_configuration["onedit"] = $column["onedit"];
					}
					$this->editable_configuration["aoColumns"][] = $editable_configuration;
				}
			}
			else
			{
				foreach ($column["children"] as $child)
				{
					$this->__set_column_configuration($child);
				}
			}
		}
	}

	private function __calculate_header_colspan(array $children)
	{
		$colspan = 0;

		foreach($children as $child)
		{
			$colspan += empty($child["children"]) ? 1 : $this->__calculate_header_colspan($child["children"]);
		}

		return $colspan;
	}

	protected function __process_parameters(array $prms)
	{
		$this->configuration = array();
		foreach($prms as $key => $val)
		{
			switch ($key)
			{
				case "data":
					$this->set_data($val);
					break;

				case "columns":
					$this->set_columns($val);
					break;

				case "name":
					$this->set_name($val);
					break;

/* TEMPORARY */	default:
					$this->$key = $val;
					break;
			}
		}
	}

	protected function __load_required_javascripts()
	{
		active_page_data::add_javascript($this->__generate_javascript());

		load_javascript("jquery/plugins/dataTables/jquery.dataTables.min.js");

		if ($this->editable)
		{
			load_javascript("jquery/plugins/jquery.jeditable.min.js");
			load_javascript("jquery/plugins/dataTables/jquery.dataTables.editable.js");
		}
		if ($this->vertical_grouping)
		{
			load_javascript("jquery/plugins/dataTables/jquery.dataTables.multiRowspan.js");
		}
	}

	protected function __load_required_stylesheets()
	{
		active_page_data::load_stylesheet("css/dataTables.css");
	}

	protected function __generate_javascript()
	{
		$this->read_template("default.js");

		return $this->__parse();
	}

	private function __columns_depth($columns)
	{
		$max_depth = 1;

		foreach($columns as $column)
		{
			if (!empty($column["children"]))
			{
				$max_depth = max($max_depth, 1 + $this->__columns_depth($column["children"]));
			}
		}

		return $max_depth;
	}
}
