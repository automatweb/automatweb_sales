<?php

define("GCHART_LINE_CHART", "lc");
define("GCHART_LINE_CHARTXY", "lxy");
define("GCHART_SPARKLINE", "ls");
define("GCHART_BAR_H", "bhs");
define("GCHART_BAR_V", "bvs");
define("GCHART_BAR_GH", "bhg");
define("GCHART_BAR_GV", "bvg");
define("GCHART_PIE", "p");
define("GCHART_PIE_3D", "p3");
define("GCHART_VENN", "v");
define("GCHART_SPLATTER", "s");
define("GCHART_RADAR", "r");
define("GCHART_MAP", "t");
define("GCHART_GOM", "gom");
define("GCHART_QR", "qr");

define("GCHART_FILL_SOLID", "s");
define("GCHART_FILL_GRADIENT", "lg");
define("GCHART_FILL_BACKGROUND", "bg");
define("GCHART_FILL_STRIPES", "ls");
define("GCHART_FILL_CHART", "c");
define("GCHART_FILL_TRANSPARENCY", "a");

define("GCHART_POSITION_TOP", "t");
define("GCHART_POSITION_BOTTOM", "b");
define("GCHART_POSITION_LEFT", "l");
define("GCHART_POSITION_RIGHT", "r");

define("GCHART_MARKER_ARROW", "a");
define("GCHART_MARKER_CROSS", "c");
define("GCHART_MARKER_DIAMOND", "d");
define("GCHART_MARKER_CIRCLE", "o");
define("GCHART_MARKER_SQUARE", "s");
define("GCHART_MARKER_TEXT", "t");
define("GCHART_MARKER_LINE_FROM_X", "v");
define("GCHART_MARKER_LINE_FROM_TOP", "V");
define("GCHART_MARKER_LINE_HORIZONTAL", "h");
define("GCHART_MARKER_X", "x");
define("GCHART_ORDER_BOTTOM", "-1");
define("GCHART_ORDER_DEFAULT", "0");
define("GCHART_ORDER_TOP", "1");

define("GCHART_RANGE_MARKER_V", "r");
define("GCHART_RANGE_MARKER_H", "R");

define("GCHART_AXIS_TOP", "t");
define("GCHART_AXIS_BOTTOM", "x");
define("GCHART_AXIS_LEFT", "y");
define("GCHART_AXIS_RIGHT", "r");
define("GCHART_ALIGN_LEFT", -1);
define("GCHART_ALIGN_CENTER", 0);
define("GCHART_ALIGN_RIGHT", 1);

define("GCHART_SITE", "http://chart.apis.google.com/chart?");
define("GCHART_DEF_WIDTH", 200);
define("GCHART_DEF_HEIGHT", 200);

class google_chart extends aw_template
{
	var $data = array();
	var $fills = array();
	var $linestyles = array();
	var $markers = array();
	var $range_markers = array();
	var $axis_labels = array();

	var $chart_type;
	var $width;
	var $height;
	var $colors;
	var $title;
	var $legend;
	var $barsizes;
	var $zeroline;
	var $labels;
	var $grid;
	var $axis;
	var $axis_positions;
	var $axis_ranges;
	var $axis_styles;

	var $using_cache;
	var $uniq_id;

	function google_chart()
	{
		$this->init (array (
			"tpldir" => "vcl/google_chart",
		));
	}

	function init_vcl_property($arr)
	{
		$this->set_id($arr["request"]["class"].".".$arr["prop"]["name"].".".$arr["obj_inst"]->id());

		$pr = &$arr["property"];
		$pr["vcl_inst"] = $this;
		return array($pr["name"] => $pr);
	}

	/**
	@attrib name=set_type api=1
	@param type required
	@comment
		Set chart's type. Required to display a chart.
		Valid options:
		GCHART_LINE_CHART - Line chart
		GCHART_LINE_CHARTXY - Line chart, a pair of data set required for each line
		GCHART_SPARKLINE - Sparkline chart (line chart without axis)
		GCHART_BAR_H - Bar chart, horizontal bars, data sets are stacked
		GCHART_BAR_V - Bar chart, vertical bars, data sets are stacked
		GCHART_BAR_GH - Bar chart, horizontal, data sets are grouped
		GCHART_BAR_GV - Bar chart, vertical, data sets are grouped
		GCHART_PIE - Pie chart
		GCHART_PIE_3D - 3D pie chart
		GCHART_VENN - Venn diagram
		GCHART_SPLATTER - Scatter plot
		GCHART_RADAR - Radar chart
		GCHART_MAP - Map
		GCHART_GOM - Google-o-meter chart
		GCHART_QR - QR codes
	**/
	function set_type($type)
	{
		$this->chart_type = $type;
	}

	/**
	@attrib name=add_data api=1
	@param data required type=array
	@comment
		Adds a data set to the chart. Required to display a chart.
	@examples
		$ch->add_data(array(
			10, 30, 25,
		));
		$ch->add_data(array(
			40, 20,
		));
	**/
	function add_data($data)
	{
		$this->data[] = $data;
	}

	/**
	@attrib name=set_size api=1
	@param width required type=int
	@param height required type=int
	@comment
		Set chart's size in pixels. Required to display a chart.
		Chart height must be at most 1,000 pixels.
		Chart width must be at most 1,000 pixels.
		Chart may contain at most 300,000 pixels.
	@examples
		$ch->set_size(array(
			"width" => 250,
			"height" => 350,
		));
	**/
	function set_size($size)
	{
		$this->width = $size["width"];
		$this->height = $size["height"];
	}

	/**
	@attrib name=set_labels api=1
	@param labels required type=array
	@comment
		Sets a pie chart's or a google-o-meter's labels
	@examples
		$ch->set_labels(array(
			t("foo"),
			t("bar"),
		));
	**/
	function set_labels($labels)
	{
		$this->labels = $labels;
	}

	/**
	@attrib name=set_colors api=1
	@param colors required type=array
	@comment
		Sets a color for each data set
	@examples
		$ch->add_data(array(10,20));
		$ch->add_data(array(12,14));
		$ch->set_colors(array("ff0000", "00ff00"));
		//This would make the data set with 10 & 20 red and the one with 12 & 14 green
	**/
	function set_colors($colors)
	{
		$this->colors = $colors;
	}

	/**
	@attrib name=add_fill api=1
	@param area required
	@param type required
	@param angle required type=int
	@param colors required type=array
	@comment
		Adds a fill to the chart

		Valid options for area are:
		GCHART_FILL_BACKGROUND - fills the whole background
		GCHART_FILL_CHART - fills the chart area
		GCHART_FILL_TRANSPARENCY - adds transparency to the whole chart

		Valid options for type are:
		GCHART_FILL_SOLID - solid fill
		GCHART_FILL_GRADIENT - linear gradient fill
		GCHART_FILL_STRIPES - linear stripes (define a number of stripes, that are repeated all over the chart)

		angle specifies the angle in degrees for gradient fills or stripes

		color is an array of:
			- "color", for the hex rgb value
			- "param" (value in range 0.0 - 1.0), in case of a gradient fill the color's offset value, in case of stripes, the stripe's width
	@examples
		$c->add_fill(array(
			"area" => GCHART_FILL_BACKGROUND,
			"type" => GCHART_FILL_GRADIENT,
			"colors" => array(
				array(
					"color" => "ffffff",
					"param" => 0.2,
				),
				array(
					"color" => "dddddd",
					"param" => 1,
				),
			),
		));
	**/
	function add_fill($fill)
	{
		$this->fills[] = $fill;
	}

	/**
	@attrib name=set_title api=1
	@param text required
	@param color optional
	@param size optional
	@comment
		Set the chart's title
		color defines the text's color, size defines the text size in pixels
	@examples
		$ch->set_title(array(
			"text" => t("This is my chart"),
			"color" => "cccc33",
			"size" => 12,
		));
	**/
	function set_title($title)
	{
		$this->title = $title;
	}

	/**
	@attrib name=set_legend api=1
	@param labels required type=array
	@param position optional
	@comment
		Set a legend for chart's data sets. Doesn't work with pie charts

		Options for position are:
		GCHART_POSITION_TOP
		GCHART_POSITION_BOTTOM
		GCHART_POSITION_LEFT
		GCHART_POSITION_RIGHT
	@examples
		$c2->set_legend(array(
			"labels" => array(
				t("First"),
				t("Second"),
				t("Third"),
			),
			"position" => GCHART_POSITION_LEFT,
		));
	**/
	function set_legend($legend)
	{
		$this->legend = $legend;
	}

	/**
	@attrib name=set_bar_zeroline api=1
	@param zeros required type=array
	@comment
		Sets the zero line position for each data set of a bar chart. Values are between 0.0 and 1.0
		You can define just one value, that will then be used for each data set
	@examples
		$ch->set_bar_zeroline(array(
			0.5, 0.8
		));
	**/
	function set_bar_zeroline($zeros)
	{
		$this->zeroline = $zeros;
	}

	/**
	@attrib name=set_data_scales api=1
	@param data_scales required type=array
	@comment
		Sets a pair of data ranges for each data set of bar chart.
		You can define just one pair, that will then be used for each data set.
	@examples
		$ch->set_data_scales(array(
			array(100, 0), array(100, -100)
		));
	**/
	function set_data_scales($data_scales)
	{
		$this->data_scales = $data_scales;
	}

	/**
	@attrib name=set_bar_sizes api=1
	@param width required type=int
	@param bar_spacing optional type=int
	@param bar_group_spacing optional type=int
	@comment
		Changes the look of the bars in a bar chart.
		width defines a bar's width
		bar_spacing defines the spacing between bars in a group
		bar_group_spacing defines the spacing between bar groups
	@examples
		$ch->set_bar_sizes(array(
			"width" => 10,
			"bar_spacing" => 3,
			"bar_group_spacing" => 8,
		));
	**/
	function set_bar_sizes($sizes)
	{
		$this->barsizes = $sizes;
	}

	/**
	@attrib name=add_line_style api=1
	@param thickness required type=int
	@param segments required type=array
	@comment
		Set the line style for a line chart's data set
		thickness - in pixels
		segments - array, first value for the line segment's width in pixels and second for the spaces. for a solid line, set the second value as 0
	@examples
		$ch->add_line_style(array(
			"thickness" => 2,
			"segments" => array(
				5,3,
			)),
		));
	**/
	function add_line_style($style)
	{
		$this->linestyles[] = $style;
	}

	/**
	@attrib name=add_marker api=1
	@param type required type=string
	@param text optional type=string
	@param color required type=string
	@param dataset required type=int
	@param datapoint required type=int
	@param size required type=int
	@param order required type=int
	@comment
		Adds a marker to the chart (bar & line chart mostly)

		valid options for type are:
		GCHART_MARKER_ARROW
		GCHART_MARKER_CROSS
		GCHART_MARKER_DIAMOND
		GCHART_MARKER_CIRCLE
		GCHART_MARKER_SQUARE
		GCHART_MARKER_TEXT
		GCHART_MARKER_LINE_FROM_X
		GCHART_MARKER_LINE_FROM_TO
		GCHART_MARKER_LINE_HORIZONTAL
		GCHART_MARKER_X

		dataset is the data set's id, first data set has an id of 0
		datapoint is the data point's id, first data point has an id of 0. you can use -1 for all points

		valid options for order are:
		GCHART_ORDER_BOTTOM - marker is placed behind everything else
		GCHART_ORDER_DEFAULT - marker is drawn behind other markers, but in front of bars and lines
		GCHART_ORDER_TOP - marker is placed on top
	@examples
		$c->add_marker(array(
			"type" => GCHART_MARKER_TEXT,
			"text" => t("This here"),
			"color" => "ff0000",
			"dataset" => 1,
			"datapoint" => 2,
			"size" => 11,
		));
	**/
	function add_marker($marker)
	{
		$this->markers[] = $marker;
	}

	/**
	@attrib name=add_range_marker api=1
	@param type required type=string
	@param color required type=string
	@param start required type=int
	@param end required type=int
	@comment
		Adds a range marker to the chart. A range marker is basically a means for highlighting some area of the chart

		type - GCHART_RANGE_MARKER_V for a vertical range, or GCHART_RANGE_MARKER_H for a horizontal one
		start, end - value is between 0.0 and 1.0; 0.0 is the beginning of the chart and 1.0 the end
	@examples
		$ch->add_range_marker(array(
			"type" => GCHART_RANGE_MARKER_V,
			"color" => "eeeeee",
			"start" => 0.4,
			"end" => 0.5,
		));
	**/
	function add_range_marker($marker)
	{
		$this->range_markers[] = $marker;
	}

	/**
	@attrib name=set_grid api=1
	@param xstep required type=int
	@param ystep required type=int
	@param segments optional type=array
	@comment
		xstep is the horizontal spacing between grid lines
		ystep is the vertical spacing between grid lines
		for segments, see add_line_style
	**/
	function set_grid($grid)
	{
		$this->grid = $grid;
	}

	/**
	@attrib name=set_axis api=1
	@param axis required type=array
	@comment
		sets the chart's axis. the parameter is an array of values, which can be any of:
		GCHART_AXIS_TOP
		GCHART_AXIS_BOTTOM
		GCHART_AXIS_LEFT
		GCHART_AXIS_RIGHT
	@examples
		//define one axis to the left and two to the bottom
		$ch->set_axis(array(
			GCHART_AXIS_LEFT,
			GCHART_AXIS_BOTTOM,
			GCHART_AXIS_BOTTOM,
		));

		//set some labels
		$ch->add_axis_label(0, array("left1", "left2", "left3"));
		$ch->add_axis_label(2, array("bottom11", "bottom12", "bottom13"));

		//set the range and style for one of them
		$ch->add_axis_range(1, array(0, 500));
		$ch->add_axis_style(1, array(
			"color" => "ff0000",
			"font" => 11,
			"align" => GCHART_ALIGN_RIGHT,
		));
	**/
	function set_axis($axis)
	{
		$this->axis = $axis;
	}

	/**
	@attrib name=add_axis_label api=1
	@param id required type=int
	@param labels required type=array
	@comment
		id is the ID of the axis, as defined in set_axis. the first axis has an id of 0
		labels is an array of strings, that will be distributed evenly on the axis
	**/
	function add_axis_label($id, $labels)
	{
		$this->axis_labels[$id] = $labels;
	}

	/**
	@attrib name=add_axis_position api=1
	@param id required type=int
	@param positions required type=array
	@comment
		id - see add_axis_label
		positions - array of integers
	**/
	function add_axis_position($id, $positions)
	{
		$this->axis_positions[$id] = $positions;
	}

	/**
	@attrib name=add_axis_range api=1
	@param id required type=int
	@param range required type=array
	@comment
		range - array of integers that specifies the num range on the axis, eg array(0, 10)
	**/
	function add_axis_range($id, $range)
	{
		$this->axis_ranges[$id] = $range;
	}

	/**
	@attrib name=add_axis_style api=1
	@param id required type=int
	@param style required type=array
	@comment
		style should be like this:
		array(
			"color" => hex rgb value
			"font" => font size in pixels
			"align" => GCHART_ALIGN_LEFT, GCHART_ALIGN_CENTER or GCHART_ALIGN_RIGHT
		);
	**/
	function add_axis_style($id, $style)
	{
		$this->axis_styles[$id] = $style;
	}

	/**
	@attrib name=use_cache api=1
	@comment
		Sets whether to use cache. To save/load a chart from cache, it needs a unique id
	@examples
		$ch->use_cache();
		//look for cache
		if(!$ch->has_cache())
		{
			//build the chart
			$ch->set_type(GCHART_BAR);
			$ch->set_size();
			//if the chart is cached, there's no need to build it again
		}
	**/
	public function use_cache($use_cache = true)
	{
		$this->using_cache = $use_cache;
	}

	/**
	@attrib name=has_cache api=1
	@comment
		Returns true if current chart has a cached value, false otherwise
	**/
	function has_cache()
	{
		$c = get_instance("cache");
		if ($this->uniq_id && $c->file_get("gchart_".$this->uniq_id))
		{
			return true;
		}
		return false;
	}

	/**
	@attrib name=set_id api=1
	@param id required type=string
	@comment
		Set the chart's unique id.
		When the chart's a property, it defaults to classname.propname.obj_id.
		Required to use cache
	**/
	function set_id($id)
	{
		$this->uniq_id = $id;
	}

	/**
	@attrib name=get_html api=1
	@returns html of the chart image
	@examples
		classload("vcl/google_chart");
		$ch = new google_chart();
		$ch->set_type(GCHART_PIE);
		$ch->set_size(array(
			"width" => 400,
			"height" => 300,
		));
		$ch->add_data(array(
			10, 20, 30
		));
		echo $ch->get_html();
	**/
	function get_html()
	{
		if($this->using_cache && $this->has_cache())
		{
			return $this->load_from_cache();
		}

		$width = $this->width ? $this->width : GCHART_DEF_WIDTH;
		$height = $this->height ? $this->height : GCHART_DEF_HEIGHT;
		$params["chs"] = $width."x".$height;

		$params["cht"] = $this->chart_type;
		$params["chd"] = $this->process_data();

		if(count($this->colors))
		{
			$params["chco"] = implode(",", $this->colors);
		}

		if(count($this->fills))
		{
			$params["chf"] = $this->process_fills();
		}

		if($this->title)
		{
			$t = $this->title;
			$params["chtt"] = $this->process_text($t["text"]);
			if(isset($t["color"]) && isset($t["size"]))
			{
				$params["chts"] = $t["color"].",".$t["size"];
			}
		}

		if($this->legend)
		{
			$l = $this->legend;
			foreach($l["labels"] as $id => $label)
			{
				$l["labels"][$id] = $this->process_text($label);
			}
			$params["chdl"] = implode("|", $l["labels"]);
			if(isset($l["position"]))
			{
				$params["chdlp"] = $l["position"];
			}
		}

		if($this->barsizes)
		{
			$params["chbh"] = $this->process_barsizes();
		}

		$markers = $this->process_markers();

		if(count($markers))
		{
			$params["chm"] = $this->process_params($markers);
		}

		if($this->zeroline)
		{
			$params["chp"] = implode(",", $this->zeroline);
		}

		if(isset($this->data_scales) && is_array($this->data_scales) && count($this->data_scales) > 0)
		{
			$params["chds"] = $this->process_data_scales($this->data_scales);
		}

		if(count($this->linestyles))
		{
			$params["chls"] = $this->process_linestyles();
		}

		if(count($this->labels))
		{
			$params["chl"] = $this->process_labels();
		}

		if($this->grid)
		{
			$params["chg"] = $this->process_grid();
		}

		if(count($this->axis))
		{
			$params["chxt"] = implode(",", $this->axis);
		}

		if(count($this->axis_labels))
		{
			$params["chxl"] = $this->process_axis_labels();
		}

		if(count($this->axis_positions))
		{
			$params["chxp"] = $this->process_axis_data($this->axis_positions);
		}

		if(count($this->axis_ranges))
		{
			$params["chxr"] = $this->process_axis_data($this->axis_ranges);
		}

		if(count($this->axis_styles))
		{
			$params["chxs"] = $this->process_axis_styles();
		}

		foreach($params as $id => $param)
		{
			$params[$id] = $param;
		}

		$url =  GCHART_SITE.urldecode(http_build_query($params));

		if($this->using_cache && $this->uniq_id)
		{
			$c = get_instance("cache");
			$c->file_set("gchart_".$this->uniq_id, $url);
		}

		return html::img(array(
			"url" => $url,
			"border" => 0,
		));
	}

	private function load_from_cache()
	{
		$c = get_instance("cache");
		$url = $c->file_get("gchart_".$this->uniq_id);
		return html::img(array(
			"url" => $url,
			"border" => 0,
		));
	}

	private function process_barsizes()
	{
		$bs = $this->barsizes;
		$sizes[0] = $bs["width"];
		if(isset($bs["bar_spacing"]))
		{
			$sizes[] = $bs["bar_spacing"];
		}
		if(isset($bs["bar_group_spacing"]))
		{
			$sizes[] = $bs["bar_group_spacing"];
		}
		return implode(",", $sizes);
	}

	private function process_markers()
	{
		$markers = array();
		if(count($this->markers))
		{
			$m = $this->markers;
			$markers = array();
			foreach($m as $marker)
			{
				$add = array();
				$add[] = $marker["type"].(($marker["type"] == GCHART_MARKER_TEXT) ? urlencode($marker["text"]) : "");
				$add[] = $marker["color"];
				$add[] = $marker["dataset"];
				$add[] = $marker["datapoint"];
				$add[] = $marker["size"];
				if(isset($marker["order"]))
				{
					$add[] = $marker["order"];
				}
				$markers[] = $add;
			}
		}

		if(count($this->range_markers))
		{
			$m = $this->range_markers;
			foreach($m as $marker)
			{
				$add = array();
				$add[] = $marker["type"];
				$add[] = $marker["color"];
				$add[] = 0;
				$add[] = $marker["start"];
				$add[] = $marker["end"];
				$markers[] = $add;
			}
		}
		return $markers;
	}

	private function process_linestyles()
	{
		$styles = array();
		foreach($this->linestyles as $style)
		{
			$styles[] = array($style["thickness"], $style["segments"][0], $style["segments"][1]);
		}
		return $this->process_params($styles);
	}

	private function process_labels()
	{
		$labels = array();
		foreach($this->labels as $label)
		{
			$labels[] = $this->process_text($label);
		}
		return implode("|", $labels);
	}

	private function process_data_scales($data)
	{
		$data_scales = array();
		foreach($data as $row)
		{
			$data_scales[] = implode(",", $row);
		}
		return implode(",", $data_scales);
	}

	private function process_params($data)
	{
		foreach($data as $rid => $row)
		{
			$rows[] = implode(",", $row);
		}
		return implode("|", $rows);
	}

	private function process_fills()
	{
		$res = array();
		foreach($this->fills as $fill)
		{
			$data = array();
			$data[] = $fill["area"];
			$data[] = $fill["type"];
			if($fill["type"] == "lg" || $fill["type"] == "ls")
			{
				if(!isset($fill["angle"]))
				{
					$fill["angle"] = 0;
				}
				$data[] = $fill["angle"];
				foreach($fill["colors"] as $color)
				{
					$data[] = $color["color"];
					$data[] = $color["param"];
				}
			}
			else
			{
				$data[] = $fill["colors"]["color"];
			}
			$res[] = implode(",", $data);
		}
		return implode("|", $res);
	}

	private function process_axis_labels()
	{
		$labels = array();
		foreach($this->axis_labels as $id => $label)
		{
			foreach($label as $i => $l)
			{
				$label[$i] = $this->process_text($l);
			}
			$labels[] = $id.":|".implode("|", $label);
		}
		return implode("|", $labels);
	}

	private function process_axis_data($data)
	{
		$res = array();
		foreach($data as $id => $pos)
		{
			$res[] = array_merge(array($id), $pos);
		}
		return $this->process_params($res);
	}

	private function process_axis_styles()
	{
		foreach($this->axis_styles as $id => $styles)
		{
			$style = array();
			$style[] = $styles["color"];
			if(isset($styles["font"]))
			{
				$style[] = $styles["font"];
				if(isset($styles["align"]))
				{
					$style[] = $styles["align"];
				}
			}
			$res[$id] = $style;
		}
		return $this->process_axis_data($res);
	}

	private function process_grid()
	{
		$g = $this->grid;
		$grid = array($g["xstep"], $g["ystep"]);
		if(isset($g["segments"]))
		{
			$grid[] = $g["segments"][0];
			$grid[] = $g["segments"][1];
		}
		return implode(",", $grid);
	}

	private function process_data()
	{
		$rows = array();
		$high = 0;
		foreach($this->data as $row)
		{
			foreach(safe_array($row) as $num)
			{
				if($num > $high)
				{
					$high = $num;
				}
			}
		}
		foreach($this->data as $rid => $row)
		{
			if(!is_array($row))
			{
				$rows[] = -1;
			}
			else
			{
				if($high)
				{
					foreach($row as $id => $num)
					{
						$row[$id] = round($num / $high * 100, 1);
					}
				}
				$rows[] = implode(",", $row);
			}
		}
		return "t:".implode("|", $rows);
	}

	private function process_text($text)
	{
		return urlencode(html_entity_decode($text));
	}

	/**
	**/
	public static function generate_colors($count = 5)
	{
		$tmp_colors = array(
			"ffcc00",
			"0000ff",
			"33ffff",
			"ff0000",
			"000000",
			"ff00cc",
			"ffff00",
		);
		$colors = array();
		$colors = array_slice($tmp_colors, 0, $count);
		return $colors;
	}
}
