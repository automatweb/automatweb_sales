<?php

namespace automatweb;
// gantt_chart.aw - Gantti diagramm
/*

@classinfo syslog_type=ST_GANTT_CHART relationmgr=yes maintainer=voldemar

@default table=objects
@default field=meta
@default method=serialize
@default group=general

*/

class gantt_chart extends class_base
{
	const AW_CLID = 869;

	protected $data = array ();
	protected $rows = array ();
	protected $pending_bars = array ();
	protected $navigation = "";
	protected $chart_caption = ""; // caption text (string)
	protected $chart_footer = ""; // footer text (string)
	protected $start = 0;
	protected $end = 0;
	protected $column_length = 0;
	protected $columns = array();
	protected $subdivisions = 1;
	protected $chart_width = 1000; // pixels
	protected $row_height = 12; // pixels
	protected $chart_id = "vcl_gantt_chrart";
	protected $style = "default";
	protected $row_dfn = "";
	protected $timespans = 0;
	protected $timespan_range = 0;
	protected $row_anchors = "noanchors";
	protected $bar_anchors = "noanchors";
	protected $pixel_length = 1;
	protected $cell_length = 0;
	protected $cell_width = 0;

	function gantt_chart ()
	{
		$this->init (array (
			"tpldir" => "gantt_chart",
			"clid" => CL_GANTT_CHART,
		));
	}

	function init_vcl_property ($arr)
	{
		$property = $arr["property"];
		$this->chart_id = $property["name"];
		$property["vcl_inst"] = $this;
		return array($property["name"] => $property);
	}

	/**  Configure chart
		@attrib api=1 params=name

		@param chart_id optional type=string
			Then name of the chart

		@param start optional type=timestamp
			Chart start time. defaults to start of week back from current time.

		@param columns optional type=int
			Number of divisions in chart (e.g. 7 days for a chart depicting one week). Default is 7.

		@param subdivisions optional type=int
			Number of subdivisions in column (e.g. 24 hours for a column depicting one day). Default is 1 (meaning subdivision & column coincide).

		@param column_length optional type=int
			Length of one division in seconds. Default is 86400.

		@param width optional type=int
			Chart width in pixels. Default is 1000.

		@param row_height optional type=int
			Row height in pixels. Default is 12.

		@param row_dfn optional type=string
			Title for row-titles column. Default is "Ressurss".

		@param style optional type=string
			Style to use (default| ... ).

		@param row_anchors optional type=bool
			Make hyperlinks of row names ("false" not impl. yet).

		@param bar_anchors optional type=bool
			Make hyperlinks of bars ("false" not impl. yet).

		@param timespans optional type=int
			Number of time stops at the top.

		@param caption optional type=string
			Chart caption text.

		@param navigation optional type=string
			Chart navigation part.

		@param footer optional type=string
			Chart footer text.

		@errors none
		@returns none

		@examples
			$chart = get_instance("vcl/gantt_chart");
			$chart->configure_chart (array (
				"chart_id" => "bt_gantt",
				"style" => "aw",
				"start" => get_week_start(),
				"end" => get_week_start() + 7*24*3600,
				"width" => 850,
				"row_height" => 10,
			));

	**/
	function configure_chart ($arr)
	{
		$this->start = empty ($arr["start"]) ? (time () - 302400) : (int) $arr["start"];
		$this->column_length = empty ($arr["column_length"]) ? 86400 : (int) $arr["column_length"];
		$this->columns = empty ($arr["columns"]) ? range (1, 7) :  range (1, (int) $arr["columns"]);
		$this->subdivisions = empty ($arr["subdivisions"]) ? 1 :  (int) $arr["subdivisions"];
		$this->chart_width = empty ($arr["width"]) ? 1000 : (int) $arr["width"];
		$this->chart_id = empty ($arr["chart_id"]) ? "0" : (string) $arr["chart_id"];
		$this->style = empty ($arr["style"]) ? "default" : (string) $arr["style"];
		$this->row_dfn = empty ($arr["row_dfn"]) ? "Ressurss" : (string) $arr["row_dfn"];
		$this->row_height = empty ($arr["row_height"]) ? 12 : (int) $arr["row_height"];
		$this->timespans = empty ($arr["timespans"]) ? 0 : (int) $arr["timespans"];
		$this->timespan_range = empty ($arr["timespan_range"]) ? 86400 : (int) $arr["timespan_range"];
		$this->end = (int) ($this->start + count ($this->columns) * $this->column_length);
		// $this->row_anchors = $arr["row_anchors"] ? "anchors" : "noanchors";
		// $this->bar_anchors = $arr["bar_anchors"] ? "anchors" : "noanchors";

		$this->pixel_length = ($this->end - $this->start) / $this->chart_width;
		$this->cell_length = (int) ($this->column_length / $this->subdivisions);
		$this->cell_width = ceil ($this->cell_length / $this->pixel_length);

		if (isset($arr["caption"]))
		{
			$this->chart_caption = (string) $arr["caption"];
		}

		if (isset($arr["footer"]))
		{
			$this->chart_footer = (string) $arr["footer"];
		}

		if (isset($arr["navigation"]))
		{
			$this->navigation = (string) $arr["navigation"];
		}
	}

	/** Configure chart navigation
		@attrib api=1 params=name

		@param show optional type=bool
			If set to "false", navigation won't be shown, default is "true".

	**/
	function configure_navigation ($arr)
	{
		$this->navigation = (bool) (empty ($arr["show"]) ? true : $arr["show"]);
	}

	/** Adds one row.
		@attrib api=1 params=name

		@param type optional type=string
			Row type data|separator. Default is data.

		@param expanded optional type=bool
			Whether to initially show consequent rows after separator or not. applicable when row type is "separator". Default is TRUE.

		@param name required type=string
			Identifier for the row.

		@param title required type=string
			Title for the row.

		@param uri optional type=string
			URI for row title. Applies if row_anchors property is set to true for chart.

		@param target optional type=string
			URI target for row title. Applies if row_anchors property is set to true for chart.

		@returns none
		@errors none

		@examples
			$gt_list = $this->get_undone_bugs_by_p($p);
			foreach($gt_list as $gt)
			{
				$chart->add_row (array (
					"name" => $gt->id(),
					"title" => $gt->name(),
					"uri" => html::get_change_url(
						$gt->id(),
						array("return_url" => get_ru())
					)
				));
			}
	**/
	function add_row ($arr)
	{
		$row_name_class = empty ($arr["row_name_class"]) ? "VclGanttRowName" : $arr["row_name_class"];
		$row_name = $arr["name"];
		$row_title = $arr["title"];
		$row_type = empty ($arr["type"]) ? "data" : $arr["type"];
		$expanded = (bool) (empty ($arr["expanded"]) ? true : $arr["expanded"]);
		$row_title_uri = empty ($arr["uri"]) ? false : $arr["uri"];
		$row_title_uri_target = empty ($arr["target"]) ? "_self" : $arr["target"];

		$this->rows[$row_name] = array (
			"type" => $row_type,
			"expanded" => $expanded,
			"name" => $row_name,
			"title" => $row_title,
			"uri" => $row_title_uri,
			"target" => $row_title_uri_target,
			"id" => ++$this->row_id_counter,
			"name_class" => $row_name_class,
		);
	}
	
	/** Get protected rows. Used in bug_gracker.aw -> aw_firefoxtools_gantt
		@errors none
		@returns array
	**/
	public function get_rows ($arr)
	{
		$rows = array();
		$this->sort_data ();
		foreach ($this->rows as $row)
		{
			$rows[] = $row;
		}
		return $rows;
	}

	/** Defines column. Columns can be defined only after calling configure_chart.
		@attrib api=1 params=name

		@param col required type=int
			Column number from left, 0 is row definitions column.

		@param title required type=string
			Title for the column.

		@param uri required type=string
			URI for column title.

		@param target optional type=string
			URI target for column title.

		@errors none
		@returns none

		@examples
			$i = 0;
			$days = array ("P", "E", "T", "K", "N", "R", "L");

			while ($i < $columns)
			{
				$day_start = (get_day_start() + ($i * 86400));
				$day = date ("w", $day_start);
				$date = date ("j/m/Y", $day_start);
				$uri = aw_url_change_var ("mrp_chart_length", 1);
				$uri = aw_url_change_var ("mrp_chart_start", $day_start, $uri);
				$chart->define_column (array (
					"col" => ($i + 1),
					"title" => $days[$day] . " - " . $date,
					"uri" => $uri,
				));
				$i++;
			}
	**/
	function define_column ($arr)
	{
		$col = $arr["col"];
		$title = $arr["title"];
		$uri = $arr["uri"];
		$target = empty ($arr["target"]) ? "_self" : $arr["target"];

		$this->columns[($col - 1)] = array (
			"title" => $title,
			"uri" => $uri,
			"target" => $target,
		);
	}

	/** Adds one bar/data object to specified row.
		@attrib api=1 params=name

		@param row required type=string
			Row name to which to add new bar.

		@param start required type=timestamp
			Bar starting place on timeline.

		@param length required type=int
			Bar length in seconds.

		@param layer optional type=int
			Layer to put the bar on. 0 is default. Layers with larger numbers are shown on top.

		@param title required type=string
			Title for the bar.

		@param colour optional type=string
			CSS colour definition, name or rgb value.  Default is "silver".

		@param nostartmark optional type=bool
			Don't show bar start mark. Default is false.

		@param uri optional type=string
			URI for bar hyperlink. Applies if bar_anchors property is set to true for chart.

		@param target optional type=string
			URI target for bar hyperlink. Applies if bar_anchors property is set to true for chart.

		@param id optional type=string
			Bar id.

		@examples
			$wd_end = mktime($day_end, 0, 0, date("m", $start), date("d", $start), date("Y", $start));
			$tot_len = $length;
			$length = $wd_end - $start;
			$remaining_len = $tot_len - $length;
			$title = $gt->name()."<br>( ".date("d.m.Y H:i", $start)." - ".date("d.m.Y H:i", $start + $length)." ) ";

			$bar = array (
				"id" => $gt->id (),
				"row" => $gt->id (),
				"start" => $start,
				"length" => $length,
				"title" => $title,
			);

			$chart->add_bar ($bar);
	**/
	function add_bar ($arr)
	{
		$row = $arr["row"];
		$start = (int) $arr["start"];
		$length = (int) $arr["length"];
		$title = empty ($arr["title"]) ? "" : $arr["title"];
		$layer = empty ($arr["layer"]) ? 0 : (int) $arr["layer"];
		$colour = empty ($arr["colour"]) ? "silver" : $arr["colour"];
		$nostartmark = empty ($arr["nostartmark"]) ? false : true;
		$uri = empty ($arr["uri"]) ? "#" : $arr["uri"];
		$uri_target = empty ($arr["target"]) ? "_self" : $arr["target"];
		$id = isset($arr["id"]) ? $arr["id"] : "";

		$this->data[$row][] = array (
			"id" => $id,
			"start" => $start,
			"length" => $length,
			"title" => $title,
			"colour" => $colour,
			"nostartmark" => $nostartmark,
			"bar_uri" => $uri,
			"bar_uri_target" => $uri_target,
			"row" => $row
		);
	}

	/** draws the chart
		@attrib api=1

		@returns The html for the chart

		@examples
			simple example to draw a gantt chart can be found in CL_PROJECT::_goals_gantt
	**/
	function draw_chart ()
	{
		### parse style
		$this->read_template ("style_" . $this->style . ".tpl");
		$this->vars = array (
			"chart_id" => $this->chart_id,
			"chart_width" => $this->chart_width,
			"row_height" => $this->row_height+2,
			"row_text_height" => ($this->row_height - ceil($this->row_height/10)),
		);
		$style = $this->parse ();

		### compose chart table
		$this->sort_data ();
		$rows = "";
		$collapsed = false;
		$this->pending_bars = array ();
		$this->read_template ("chart_" . $this->style . ".tpl");
		
		foreach ($this->rows as $row)
		{
			$row_contents = "";
			$cell_end = $this->start + $this->cell_length;
			$this->pointer = $this->start;
			$columns = count ($this->columns);

			switch ($row["type"])
			{
				case "data":
					if ($collapsed)
					{
						### go to next row
						continue 2;
					}
					break;

				case "separator":
					if ( ($row["expanded"] == false) or (aw_global_get("aw_gantt_chart_collapsed_" . $row["id"]) === "y") or (isset($_GET["aw_gantt_chart_collapsed_" . $row["id"]]) and $_GET["aw_gantt_chart_collapsed_" . $row["id"]] === "y") )//!!! kust siin _GET asemel v6tta see?
					{
						aw_session_set("aw_gantt_chart_collapsed_" . $row["id"], "y");
						$collapsed = true;
					}
					else
					{
						aw_session_set("aw_gantt_chart_collapsed_" . $row["id"], "n");
						$collapsed = false;
					}

					if ( (aw_global_get("aw_gantt_chart_collapsed_" . $row["id"]) === "n") or (isset($_GET["aw_gantt_chart_collapsed_" . $row["id"]]) and $_GET["aw_gantt_chart_collapsed_" . $row["id"]] === "n") )//!!! kust siin _GET asemel v6tta see?
					{
						aw_session_set("aw_gantt_chart_collapsed_" . $row["id"], "n");
						$collapsed = false;
					}

					$collapse_toggle_value = $collapsed ? "n" : "y";
					$row_state = $collapsed ? "plus" : "minus";
					$expand_collapse_title = $collapsed ? t("N&auml;ita") : t("Peida");
					$this->vars (array (
						"expand_collapse_link" => aw_url_change_var ("aw_gantt_chart_collapsed_" . $row["id"], $collapse_toggle_value),
						"expand_collapse_title" => $expand_collapse_title,
						"row_state" => $row_state,
						"row_title" => $row["title"],
						"colspan" => $columns*$this->subdivisions + 1,
					));
					$rows .= trim ($this->parse ("separator_row"));
					continue 2;
			}

			while ($columns)
			{
				$subdivisions = $this->subdivisions;
				$cell_type = "column";

				while ($subdivisions)
				{
					$cell_contents = "";
					$this->content_length = 0;

					while ($this->pointer < $cell_end)
					{
						if (!isset($this->parsed_data[$row["name"]]) or !is_array ($this->parsed_data[$row["name"]]))
						{
							break;
						}
						else
						{
							$bar = array_shift ($this->parsed_data[$row["name"]]);

							if (!$bar or ((($cell_end - $bar["start"]) / $this->pixel_length) < 0.5))
							{
								### no bars or no bars left in cell
								array_unshift ($this->parsed_data[$row["name"]], $bar);
								break;
							}
						}

						### set bar colour
						if (isset ($bar["force_colour"]))
						{
							$bar_type = "continue";
							$bar_colour = $bar["force_colour"];
							unset ($bar["force_colour"]);
						}
						else
						{
							if ($bar["nostartmark"])
							{
								$bar_type = "continue";
							}
							else
							{
								$bar_type = "start";
							}

							$bar_colour = $bar["colour"];
						}

						### trim bars starting/ending before chart start
						if ($bar["start"] < $this->start)
						{
							if ((($bar["start"] + $bar["length"]) - $this->pixel_length) >= $this->start)
							{
								### trim bar ending after chart start
								$bar["length"] = ($bar["start"] + $bar["length"]) - $this->start;
								$bar["start"] = $this->start;
								$bar_type = "continue";
							}
							else
							{
								### bar ends before chart start, go to next bar for current row
								continue;
							}
						}

						### split bars longer than free space in one cell
						if (($bar["start"] + $bar["length"]) > $cell_end)
						{
							if (($bar["start"] + $bar["length"] - $this->pixel_length) >= $cell_end)
							{
								### push overflow to next cell
								$split_bar = $bar;
								$split_bar["length"] = $bar["length"] - ($cell_end - $bar["start"]);
								$split_bar["start"] = $cell_end;
								$split_bar["force_colour"] = $bar_colour;
								array_unshift ($this->parsed_data[$row["name"]], $split_bar);
							}

							### set length to fill rest of the cell
							$length = $cell_end - $bar["start"];
							$remainder = ($cell_end - $bar["start"]) % $this->pixel_length;
							$bar["length"] = $remainder ? ($length + $this->pixel_length) : $length;
						}

						### parse bar
						if ($bar["length"] < $this->pixel_length)
						{
							### leave decision for bars that don't cross to next pixel to later
							$this->pending_bars[] = $bar;
						}
						else
						{
							$pending_start = false;
							$pending_length = 0;

							while (count($this->pending_bars))
							{
								$pending_bar = array_shift ($this->pending_bars);
								$pending_length += $pending_bar["length"];

								if ($pending_start !== false)
								{
									$pending_start = $pending_bar["start"];
								}

								if ($pending_length >= $this->pixel_length)
								{
									$pending_bar["start"] = $pending_start;
									$pending_bar["length"] = $this->pixel_length;
									$cell_contents .= $this->draw_bar ($pending_bar, $cell_type, $bar_type, $bar_colour);
									$pending_start = false;
									$pending_length = 0;
								}
							}

							$cell_contents .= $this->draw_bar ($bar, $cell_type, $bar_type, $bar_colour);
						}
					}

					### fill remaining empty space
					if ($this->content_length < $this->cell_width)
					{
						$length = $this->cell_width - $this->content_length;
						$this->vars (array (
							"length" => $length,
							"baseurl" => $this->cfg["baseurl"],
						));
						$cell_contents .= trim ($this->parse ("MAIN.data_row.data_cell_" . $cell_type . ".cell_contents.bar_empty"));
						$this->pointer = $cell_end;
					}

					### parse cell
					$this->vars (array (
						"cell_contents" => $cell_contents,
					));
					$row_contents .= trim ($this->parse ("MAIN.data_row.data_cell_" . $cell_type));

					### ...
					$cell_end += $this->cell_length;
					$cell_type = "subdivision";
					$subdivisions--;
				}

				$columns--;
			}

			### parse row
			$this->vars (array (
				"row_name" => $row["title"],
				"row_name_class" => $row["name_class"],
				"row_uri" => $row["uri"],
				"row_uri_target" => $row["target"],
				"data_cell_" . $cell_type => $row_contents,
			));
			$rows .= trim ($this->parse ("data_row"));
		}

		### parse header
		$header_row = "";

		foreach ($this->columns as $nr => $definition)
		{
			if (is_array ($definition))
			{
				if ($definition["uri"])
				{
					$this->vars (array (
						"title" => ($definition["title"] ? $definition["title"] : ($nr + 1)),
						"uri" => $definition["uri"],
						"target" => $definition["target"],
						"column_width" => $this->cell_width,
						"subdivisions" => $this->subdivisions,
					));
					$header_row .= $this->parse ("column_head_link");
				}
				else
				{
					$this->vars (array (
						"title" => $definition["title"],
						"column_width" => $this->cell_width,
						"subdivisions" => $this->subdivisions
					));
					$header_row .= $this->parse ("column_head");
				}
			}
			else
			{
				$this->vars (array (
					"title" => $nr + 1,
					"column_width" => $this->cell_width
				));
				$header_row .= $this->parse ("column_head");
			}
		}

		$caption = $navigation = $timespans = "";

		if ($this->timespans)
		{
			$timespans = $this->get_timespans();
		}

		### caption
		if (strlen($this->chart_caption))
		{
			$this->vars (array (
				"caption" => $this->chart_caption
			));
			$caption = $this->parse ("chart_caption");
		}

		### footer
		if (strlen($this->chart_footer))
		{
			$this->vars (array (
				"footer" => $this->chart_footer
			));
			$footer = $this->parse ("chart_footer");
		}

		### navigation
		if (strlen($this->navigation))
		{
			$this->vars (array (
				"navigation" => $this->navigation
			));
			$navigation = $this->parse ("chart_navigation");
		}

		### parse table
		$this->vars (array (
			"chart_caption" => $caption,
			"chart_footer" => $footer,
			"chart_navigation" => $navigation,
			"chart_id" => $this->chart_id,
			"chart_width" => $this->chart_width,
			"row_dfn" => $this->row_dfn,
			"row_dfn_span" => ($this->timespans ? 2 : 1),
			"columns" => count ($this->columns) * $this->subdivisions,
			"subdivision_row" => $timespans,
			"column_head" => $header_row,
			"data_row" => $rows
		));
		$table = $this->parse ();

		### cat all & return
		$chart = $style . $table;
		return $chart;
	}

	protected function draw_bar ($bar, $cell_type, $bar_type, $bar_colour)
	{
		$drawn_content = "";

		### insert preceeding whitespace
		if ($bar["start"] >= ($this->pointer + $this->pixel_length))
		{
			$length = (int) floor (($bar["start"] - $this->pointer) / $this->pixel_length);
			$this->vars (array (
				"length" => $length,
				"baseurl" => $this->cfg["baseurl"],
			));
			$drawn_content .= trim ($this->parse ("MAIN.data_row.data_cell_" . $cell_type . ".cell_contents.bar_empty"));
			$this->pointer += $length * $this->pixel_length;
			$this->content_length += $length;
		}

		### parse bar
		$length = (int) floor ($bar["length"] / $this->pixel_length);
		$this->vars (array (
			"length" => $length,
			"bar_colour" => $bar_colour,
			"title" => $bar["title"],
			"bar_uri" => $bar["bar_uri"],
			"bar_uri_target" => $bar["bar_uri_target"],
			"baseurl" => $this->cfg["baseurl"],
		));
		$drawn_content .= trim ($this->parse ("MAIN.data_row.data_cell_" . $cell_type . ".cell_contents.bar_normal_" . $bar_type));

		### ...
		$this->pointer += $length * $this->pixel_length;
		$this->content_length += $length;
		return $drawn_content;
	}

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	// alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr =  array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$this_object = new object($arr["id"]);
		return $this->draw_chart();
	}

	// draws given amount of times to the top of the graph, based on the start/end
	protected function get_timespans()
	{
		if (!$this->timespans)
		{
			return "";
		}

		$ts = "";
		$step = $this->timespan_range / $this->timespans;
		$time = 0;

		foreach ($this->columns as $column)
		{
			$divisions = "";

			for ($i = 0; $i < $this->timespans; $i++)
			{
				if ($step >= (24*3600))
				{
					$division = date("d", $this->start + $time);
				}
				else
				{
					$division = date("H:i", $this->start + $time);
				}
				$align = "left";
				$this->vars(array(
					"time" => $division,
					"align" => $align
				));
				$divisions .= $this->parse("subdivision");
				$time += $step;
			}

			$this->vars(array(
				"subdivision" => $divisions,
			));
			$ts .= $this->parse("subdivision_head");
		}

		$this->vars(array(
			"subdivision_head" => $ts
		));
		$ts = $this->parse("subdivision_row");
		return $ts;
	}

	protected function sort_data ()
	{
		$this->parsed_data = $this->data;

		foreach ($this->parsed_data as $row => $data)
		{
			### sort bars
			usort ($this->parsed_data[$row], array ($this, "bar_start_sort"));

			### filter bars with same start time
			$keys_to_delete = array ();
			$key = 0;

			while (isset($this->parsed_data[$row][$key]))
			{
				### index bars starting at same time
				$current_key = $key;
				$same_start_index = array ();

				while (isset ($this->parsed_data[$row][$current_key +1]) and $this->parsed_data[$row][$key]["start"] == $this->parsed_data[$row][$current_key + 1]["start"])
				{
					$same_start_index[$this->parsed_data[$row][$current_key]["layer"]][$this->parsed_data[$row][$current_key]["length"]] = $current_key;
					$current_key++;
				}

				### show shorter bars upon longer and upper layers on lower
				ksort ($same_start_index);
				$upper_layer_end = NULL;

				foreach ($same_start_index as $layer => $length_index)
				{
					ksort ($length_index);
					$shorter_bar_end = NULL;
					$this_layer_end = NULL;

					foreach ($length_index as $length => $index_key)
					{
						$start = $this->parsed_data[$row][$index_key]["start"];

						if (isset ($upper_layer_end) and (($start + $length) > $upper_layer_end))
						{
							if (isset ($shorter_bar_end))
							{
								$this->parsed_data[$row][$index_key]["start"] = $shorter_bar_end;
								$this->parsed_data[$row][$index_key]["length"] = ($start + $length) - $shorter_bar_end;
							}

							$shorter_bar_end = $start + $length;
							$this_layer_end = max ($this_layer_end, $shorter_bar_end);
						}
						else
						{
							$keys_to_delete[] = $index_key;
						}
					}

					$upper_layer_end = max ($upper_layer_end, $this_layer_end);
				}

				$key = $current_key + 1;
			}

			if (count ($keys_to_delete))
			{
				foreach ($keys_to_delete as $key)
				{
					unset ($this->parsed_data[$row][$key]);
				}

				### sort bars again
				usort ($this->parsed_data[$row], array ($this, "bar_start_sort"));
			}

			// while (isset($this->parsed_data[$row][$key]))
			// {
				// if ($this->parsed_data[$row][$key]["start"] == $this->parsed_data[$row][$key + 1]["start"])
				// {
					// if ($this->parsed_data[$row][$key]["layer"] == $this->parsed_data[$row][$key + 1]["layer"])
					// {
						// if ($this->parsed_data[$row][$key]["length"] == $this->parsed_data[$row][$key + 1]["length"])
						// {
							// unset ($this->parsed_data[$row][$key]);
						// }
						// else
						// { ### different lengths
							// ### show shorter bars upon longer
							// $same_start_key = $key + 1;
							// $last_end = $this->parsed_data[$row][$key]["start"] + $this->parsed_data[$row][$key]["length"];

							// while ($this->parsed_data[$row][$key]["start"] == $this->parsed_data[$row][$same_start_key]["start"])
							// {
								// $start = $this->parsed_data[$row][$same_start_key]["start"];
								// $length = $this->parsed_data[$row][$same_start_key]["length"];
								// $this->parsed_data[$row][$same_start_key]["length"] = $start + $length - $last_end;
								// $this->parsed_data[$row][$same_start_key]["start"] = $last_end;
								// $this->parsed_data[$row][$same_start_key]["nostartmark"] = true;
								// $last_end = $start + $length;
								// $same_start_key++;
							// }
						// }
					// }
					// else
					// { ### different layers
						// if ($this->parsed_data[$row][$key]["length"] == $this->parsed_data[$row][$key + 1]["length"])
						// { ### same length
							// ### show upper layer bars upon lower layer bars
							// $same_start_key = $key + 1;
							// $current_key = $key;
							// $start = $this->parsed_data[$row][$key]["start"];

							// while ($start == $this->parsed_data[$row][$same_start_key]["start"])
							// {
								// unset ($this->parsed_data[$row][$current_key]);
								// $current_key = $same_start_key;
								// $same_start_key++;
							// }
						// }
						// else
						// { ### different lengths
							// ### show shorter bars upon longer and upper layers on lower
							// #### find keys by layer
							// $layer_keys = array ($this->parsed_data[$row][$key]["layer"] => array ($key));
							// $same_start_key = $key + 1;

							// while ($this->parsed_data[$row][$key]["start"] == $this->parsed_data[$row][$same_start_key]["start"])
							// {
								// $layer_keys[$layer] = $
								// $same_start_key++;
							// }

							// $last_end = $this->parsed_data[$row][$key]["start"] + $this->parsed_data[$row][$key]["length"];

							// while ($this->parsed_data[$row][$key]["start"] == $this->parsed_data[$row][$same_start_key]["start"])
							// {
								// $start = $this->parsed_data[$row][$same_start_key]["start"];
								// $length = $this->parsed_data[$row][$same_start_key]["length"];
								// $this->parsed_data[$row][$same_start_key]["length"] = $start + $length - $last_end;
								// $this->parsed_data[$row][$same_start_key]["start"] = $last_end;
								// $this->parsed_data[$row][$same_start_key]["nostartmark"] = true;
								// $last_end = $start + $length;
								// $same_start_key++;
							// }
						// }
					// }
				// }

				// $key++;
			// }

			// if (isset ($same_start_key))
			// {
				// ### sort bars again
				// usort ($this->parsed_data[$row], array ($this, "bar_start_sort"));
			// }

			### filter overlaps
			$key = 0;

			while (isset($this->parsed_data[$row][$key]))
			{
// /* dbg */ if (isset($_GET["mrp_gantt_dbg_job"]) and $this->parsed_data[$row][$key]["id"] == $_GET["mrp_gantt_dbg_job"]) { $this->ganttdbg = 1;}
// /* dbg */ if ($row == 1337) { $this->ganttdbg = 1;}

				$key2 = $key + 1;
				$overlap_end = NULL;
				$overlap_start = NULL;
				$current_bar_end = $this->parsed_data[$row][$key]["start"] + $this->parsed_data[$row][$key]["length"];

				### find out whether successive bars exist that continuously overlap current. find farthest overlaping bar end.
				while (
					isset($this->parsed_data[$row][$key2]) and
					($this->parsed_data[$row][$key2]["start"] < $current_bar_end) and
					((!isset ($overlap_end)) or ($this->parsed_data[$row][$key2]["start"] <= $overlap_end))
				)
				{ ### next bar exists, next bar starts before current ends, overlap_end is set and next bar starts before it.
					$overlap_start = !isset ($overlap_start) ? $this->parsed_data[$row][$key2]["start"] : $overlap_start;
					$overlap_end = max ($overlap_end, ($this->parsed_data[$row][$key2]["start"] + $this->parsed_data[$row][$key2]["length"]));
					$key2++;

// /* dbg */ if ($this->ganttdbg){
// /* dbg */ echo $key2 . ". overlap search length:" . round ($this->parsed_data[$row][$key2]["length"]/3600, 2) . "<br>";
// /* dbg */ echo $key2 . ". overlap search start:" . date (MRP_DATE_FORMAT, $this->parsed_data[$row][$key2]["start"]) . "<br>";
// /* dbg */ }

				}

// /* dbg */ if ($this->ganttdbg){
// /* dbg */ echo "overlap_end:" . date (MRP_DATE_FORMAT, $overlap_end) . "<br>";
// /* dbg */ echo "overlap_start:" . date (MRP_DATE_FORMAT, $overlap_start) . "<br>";
// /* dbg */ }

				if (isset ($overlap_end))
				{
					if ($overlap_end < $current_bar_end)
					{
						### insert remaining end of current bar after last continuously overlapping bar. see if remainder is overlapped by successive when array pointer gets there.
						$key2--;
						$remainder = $this->parsed_data[$row][$key];
						$remainder["start"] = $overlap_end;
						$remainder["length"] = $current_bar_end - $overlap_end;
						$remainder["nostartmark"] = true;
						array_splice ($this->parsed_data[$row], $key2, 1, array ($this->parsed_data[$row][$key2], $remainder));
					}

					### trim current bar to overlap start.
					$this->parsed_data[$row][$key]["length"] = $overlap_start - $this->parsed_data[$row][$key]["start"];

// /* dbg */ if ($this->ganttdbg){
// /* dbg */ echo "trimmed bar:";
// /* dbg */ arr ($this->parsed_data[$row][$key]);
// /* dbg */ echo "remainder start:" . date (MRP_DATE_FORMAT, $remainder["start"]) . "<br>";
// /* dbg */ $this->ganttdbg = false;}
				}

				$key++;
			}
		}
	}

	protected function bar_start_sort ($a, $b)
	{
		if ($a["start"] == $b["start"])
		{
			### sort by length
			if ($a["length"] == $b["length"])
			{
				### sort by layer
				if ($a["layer"] == $b["layer"])
				{
					return 0;
				}
				else
				{
					return ($a["layer"] > $b["layer"] ? 1 : -1);
				}
			}
			else
			{
				return ($a["length"] > $b["length"] ? 1 : -1);
			}
		}

		return ($a["start"] > $b["start"] ? 1 : -1);
	}
}

?>
