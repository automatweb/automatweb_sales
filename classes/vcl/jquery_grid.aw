<?php

class jquery_grid extends aw_template
{
	const TYPE_DEFAULT = 0;
	const TYPE_SUBGRID = 1;

	protected $id;
	protected $parent;
	protected $type;
	protected $name = "jqGrid";
	protected $json;
	protected $css_paths;
	protected $configuration = array();
	protected $columns = array();
	protected $data = array();
	protected $subgrid;
	protected $subgrid_data_index;
	protected $editable = false;

	public function __construct(array $prms = array())
	{
		$this->id = uniqid("dataTable");
		$this->set_type(self::TYPE_DEFAULT);

		/* For JSON encoding, using this instead of json_encode to deal with non-UTF-8 encoded data. */
		$this->json = new json(0, aw_global_get("charset"));
		
		$this->init(array(
			"tpldir" => "vcl/jquery_grid",
		));
		$this->css_paths = array(
			"css/jquery/ui/themeroller/excite-bike/jquery-ui-1.8.16.custom.css",
			"css/jquery/ui/ui.jqgrid.css",
		);

		$this->__process_parameters($prms);
	}

	public function get_id()
	{
		return $this->id;
	}

	public function get_parent()
	{
		return $this->parent;
	}

	public function set_parent($parent)
	{
		$this->parent = $parent;
	}

	public function set_type($type)
	{
		if ($type !== self::TYPE_DEFAULT and $type !== self::TYPE_SUBGRID)
		{
			throw new awex_jquery_grid_type("Given type is not a valid jquery_grid type!");
		}

		$this->type = $type;
	}

	public function get_type()
	{
		return $this->type;
	}

	public function get_name()
	{
		return $this->name;
	}

	public function set_name($name)
	{
		$this->name = $name;
	}

	public function set_columns($columns)
	{
		$this->columns = $columns;
		
		$this->__refresh_columns();
	}

	public function set_subgrid(jquery_grid $subgrid)
	{
		$subgrid->set_type(self::TYPE_SUBGRID);
		$subgrid->set_parent($this->get_id());

		$this->subgrid = $subgrid;
	}

	/**
		@attrib api=1
		@param data required type=array|string
			Can be either array of rows or a string. String can be used to use a custom JS object as data.
		@examples
			$t->set_data("js:JavaScriptDataArray");
	**/
	public function set_data($data)
	{
		$this->data = $data;
	}

	public function get_data()
	{
		return $this->data;
	}

	public function enable_editing()
	{
		$this->editable = true;

		/*	Beware, there are inline-editing and form editing in addition to cell-editing!
			You might want to be able to use those at some point, too.
		 */
		$this->configuration["cellEdit"] = true;
		$this->configuration["cellsubmit"] = "clientArray";

		$this->__refresh_columns();
	}

	public function disable_editing()
	{
		$this->editable = false;
		
		$this->configuration["cellEdit"] = false;

		$this->__refresh_columns();
	}

	/*

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
	*/

	public function render()
	{
		$this->__load_required_javascripts();
		$this->__load_required_stylesheets();

		$this->read_template($this->__get_html_template_name());

		return $this->__parse();
	}

	protected function __parse()
	{
		$this->vars(array(
			"id" => $this->get_id(),
			"parent" => $this->get_parent(),
			"name" => $this->get_name(),
			"configuration" => $this->json->encode($this->configuration)
		));

		if ($this->is_template("DATA"))
		{
			$this->vars(array(
				"DATA" => $this->__parse_data(),
			));
		}
		if ($this->is_template("EDITABLE"))
		{
			$this->vars(array(
				"EDITABLE" => $this->__parse_editable(),
			));
		}
		if ($this->is_template("SUBGRID"))
		{
			$this->vars(array(
				"SUBGRID" => $this->__parse_subgrid(),
			));
		}

		return $this->parse();
	}

	protected function __parse_editable()
	{
		if (!$this->editable)
		{
			return "";
		}

		return $this->parse("EDITABLE");
	}

	protected function __parse_subgrid()
	{
		if (!($this->subgrid instanceof jquery_grid) or strtolower(pathinfo($this->template_filename, PATHINFO_EXTENSION)) !== "js")
		{
			return "";
		}

		$this->subgrid->vars(array(
			"data_index" => $this->subgrid_data_index,
		));

		return $this->subgrid->generate_javascript();
	}

	protected function __parse_data()
	{
		if ($this->is_template("DATA.JSON"))
		{
			$this->vars(array(
				"data.json" => $this->json->encode($this->data),
			));

			$this->vars(array(
				"DATA.JSON" => $this->parse("DATA.JSON"),
			));
		}

		// TODO: Finish it once you actually need it!
		if ($this->is_template("DATA.ROW") and is_array($this->data))
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

	protected function generate_javascript()
	{
		$this->read_template($this->__get_javascript_template_name());

		return $this->__parse();
	}

	protected function __get_javascript_template_name()
	{
		return $this->__get_template_name().".js";
	}

	protected function __get_html_template_name()
	{
		return $this->__get_template_name().".tpl";
	}

	protected function __get_template_name()
	{
		switch ($this->get_type())
		{
			case self::TYPE_SUBGRID:
				return "subgrid";

			default:
				return "default";
		}
	}

	protected function __process_parameters(array $prms)
	{
		/* Default configuration: */
		$this->configuration["datatype"] = "local";
		$this->configuration["height"] = "auto";
		$this->configuration["autowidth"] = true;

		foreach($prms as $key => $val)
		{
			switch ($key)
			{
				case "caption":
				case "width":
					$this->configuration[$key] = $val;
					break;

				case "data":
				case "columns":
				case "name":
				case "subgrid":
					$method = "set_".$key;
					$this->$method($val);
					break;

				case "editable":
					$val ? $this->enable_editing() : $this->disable_editing();
					break;

				case "callback_pre_edit":
					$this->configuration["afterEditCell"] = $val;
					break;

				case "callback_post_edit":
					$this->configuration["afterSaveCell"] = $val;
					break;

				case "subgrid_data_index":
/* TEMPORARY */	default:
					$this->$key = $val;
					break;
			}
		}
	}

	
	protected function __refresh_columns()
	{
		$this->configuration["colNames"] = array();
		$this->configuration["colModel"] = array();
		foreach ($this->columns as $column)
		{
			$model = array(
				"name" => $column["data_index"],
				"index" => $column["data_index"]
			);

			if (!empty($column["hidden"]))
			{
				$model["hidden"] = true;
			}
			if (isset($column["default"]))
			{
				$model["defval"] = $column["default"];
			}
			if (isset($column["width"]))
			{
				$model["width"] = $column["width"];
			}
			if (isset($column["align"]))
			{
				$model["align"] = $column["align"];
			}

			if ($this->editable)
			{
				$model["editable"] = !isset($column["editable"]) or $column["editable"];
			}
			if (!empty($model["editable"]))
			{
				$model["editoptions"] = array();
				if (!empty($column["type"]))
				{
					$model["edittype"] = $column["type"];

					switch ($column["type"])
					{
						case "objpicker":
							$model["edittype"] = "custom";
							$model["formatter"] = "objpicker";
							$model["editoptions"]["custom_element"] = "js:$.fn.jqGrid.createObjPicker";
							$model["editoptions"]["custom_value"] = "js:$.fn.jqGrid.destroyObjPicker";
							$model["editoptions"]["data_index"] = !empty($column["objpicker"]["caption_index"]) ? $column["objpicker"]["caption_index"] : $column["data_index"]."_caption";
							
							if (!empty($args["options_callback"]))
							{
								preg_match("/([a-z0-9_]+)::([a-z0-9_]+)(\((([a-z0-9_]+),?)+\))?/i", $column["options_callback"], $matches);

								if (empty($matches[1]) or empty($matches[2]))
								{
									throw new awex_jquery_grid_arg("Invalid options callback specification for objpicker!");
								}

								$class = $matches[1];
								$method = $matches[2];

								if (!empty($matches[3]))
								{
									$params = explode(",", substr($matches[3], 1, -1));
								}
								else
								{
									$params = array();
								}

								$inst = new objpicker();
								$url = $inst->mk_my_orb($method, $params, $class);
							}
							else
							{
								$clids = isset($column["clid"]) ? (is_array($column["clid"]) ? implode(",", $column["clid"]) : $column["clid"]) : "";

								if (empty($clids))
								{
									throw new awex_jquery_grid_arg("Required parameter 'clid' missing for objpicker!");
								}

								$inst = new objpicker();
								$url = $inst->mk_my_orb("get_options", array("clids" => $clids), "objpicker");
							}

							$model["editoptions"]["url"] = $url;
							break;

						case "select":							
							$model["editoptions"]["value"] = isset($column["options"]) ? $column["options"] : array();
							if (!empty($model["multiple"]))
							{
								$model["editoptions"]["multiple"] = true;
							}
							break;

						case "checkbox":
							$model["editoptions"]["value"] = isset($column["options"]) ? $column["options"] : array();
							break;
					}
				}

				/* If no editoptions set, remove redundant array. */
				if (empty($model["editoptions"]))
				{
					unset($model["editoptions"]);
				}
			}

			$this->configuration["colNames"][] = $column["caption"];
			$this->configuration["colModel"][] = $model;
		}
	}

	protected function __load_required_javascripts()
	{
		active_page_data::add_javascript($this->generate_javascript());

		load_javascript("jquery/plugins/jqGrid/jquery.jqGrid.min.js");

		load_javascript("jquery/plugins/jqGrid/jquery.jqGrid.objpicker.js");
		load_javascript("bsnAutosuggest.js");
	}

	protected function __load_required_stylesheets()
	{
		foreach ($this->css_paths as $css_path)
		{
			active_page_data::load_stylesheet($css_path);
		}
	}
}

/** Generic jquery_grid exception **/
class awex_jquery_grid extends awex_vcl {}
class awex_jquery_grid_type extends awex_jquery_grid {}
class awex_jquery_grid_arg extends awex_jquery_grid {}
