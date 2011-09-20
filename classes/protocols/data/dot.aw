<?php

// dot.aw - DOT

define("DOT_RATIO_FILL", "fill");
define("DOT_RATIO_COMPRESS", "compress");
define("DOT_RATIO_AUTO", "auto");
define("DOT_NONAME", "NONAME");

class dot
{
	function dot()
	{
		$this->nodes = array();
		$this->edges = array();
		$this->subgraphs = array();
		$this->graph = "digraph";
		$this->graph_name = "directed_graph";
		$this->attributes = array();
		$this->_init_colors();
		$this->nodes_fillcolor = false;
	}


// API

	/**
		@attrib params=pos api=1
		@param name required type=string
		@param attributes optional type=array
			Dot language node attributes formatted:
			array(
				attribute => value,
				attribute_2 => value,
			)
		@param subgraph optional type=string
		@comment
			Add's a node.
		@example
			$inst->add_node("A", array(
				"label" => "Node A",
				"height" => 4,
			));
	**/
	function add_node($node, $attributes = array())
	{
		if(!$node)
		{
			return false;
		}
		if($this->nodes_fillcolor)
		{
			$attributes["style"] = "filled";
			$attributes["fillcolor"] = $this->nodes_fillcolor;
			$attributes["fontcolor"] = $this->_inverse_color($this->nodes_fillcolor);
		}

		if(!$attributes["label"] && $this->_fix_name($node) != $node)
		{
			$attributes["label"] = $node;
		}
		//$attributes["label"] .= "\\n".$attributes["fillcolor"]."(".$attributes["fontcolor"].")";

		$this->nodes[] = array(
			"node" => strlen($n = $this->_fix_name($node))?$n:DOT_NONAME,
			"attributes" => $attributes,
		);
	}

	/**
		@attrib params=pos api=1
		@comment
			Fill's node's with color. When called first time, set's fillcolor. Every next call changes color.
		@todo
			allow users to set their specific color after first call.
			allow user to disable fillcolor.

	**/
	function fillcolor($color = false)
	{
		if($this->nodes_fillcolor) // fillcolor already set, change the color
		{
			if(($no = array_search($this->nodes_fillcolor, $this->colors_dark)))
			{
				$this->nodes_fillcolor = ($no < count($this->colors_dark))?$this->colors_dark[++$no]:current($this->colors_light);
			}
			elseif(($no = array_search($this->nodes_fillcolor, $this->colors_light)))
			{
				$this->nodes_fillcolor = ($no < count($this->colors_light))?$this->colors_light[++$no]:current($this->colors_dark);
			}
			else
			{
				$this->nodes_fillcolor = current($this->colors_dark);
			}
		}
		else
		{
			$this->nodes_fillcolor = in_array($color, $this->colors())?$color:current($this->colors());
		}
	}

	/**
		@attrib params=pos api=1
		@param color optional type=bool
			Set's the color to be used. if not set, random is used
		@param clear optional type=bool
		@comment
			Set's edge color. First time call set's color, every other call changes it.
	**/
	function edgecolor($color = false, $clear = false)
	{
		if($clear)
		{
			unset($this->edge_color);
			return true;
		}
		if($this->edge_color)
		{
			if($no = array_search($this->edge_color, $this->colors_dark))
			{
				$this->edge_color = ($no < count($this->colors_dark))?$this->colors_dark[++$no]:current($this->colors_dark);
			}
			else
			{
				$this->edge_color = current($this->colors_dark);
			}
		}
		else
		{
			$this->edge_color = current($this->colors_dark);
		}
	}


	/**
		@comment
			returns array of colornames used.
	**/
	function colors()
	{
		return array_merge($this->colors_dark, $this->colors_light);
	}

	/**
		@attrib params=pos api=1
		@param from required type=string
		@param to required type=string
		@param attributes optional type=array
			Dot language edge attributes formatted:
			array(
				attribute => value,
			)

		@comment
			Add's an edge.
	**/
	function add_edge($from, $to, $attributes = array())
	{
		if($this->edge_color)
		{
			$attributes["color"] = $this->edge_color;
		}
		$this->edges[] = array(
			"from" => strlen($n = $this->_fix_name($from))?$n:DOT_NONAME,
			"to" => strlen($n = $this->_fix_name($to))?$n:DOT_NONAME,
			"attributes" => $attributes,
		);
	}

	/**
		@attrib params=pos api=1
		@param width required type=int
		@param height required type=int
		@comment
			Set's graphs size attribute.
	**/
	function set_size($width, $height)
	{
		$this->attributes["size"] = array(
			"width" => $width,
			"height" => $height,
		);
	}

	/**
		@attrib params=pos api=1
		@param attribute required type=string
		@param value required type=string
		@comment
			Set's graphs node's attributes.
	**/
	function set_node_attribute($attrib, $val)
	{
		$this->nodes[] = array("node", array(
			$attrib => $val,
		));
	}

	/**
		@attrib params=pos api=1
		@comment
			Clears graphs node's current attributes.
	**/
	function clear_node_attribute()
	{
		$this->nodes[] = array("node", array());
	}

	/**
		@attrib params=pos api=1
		@param ratio required type=string
		@comment
			Set's graphs ratio attribute.
	**/
	function set_ratio($ratio)
	{
		$this->attributes["ratio"] = $ratio;
	}

	/**
		@attrib params=pos api=1
	**/
	function get_dot($format = "html")
	{
		$dot = array();
		$attributes = $this->_gen_attributes($this->attributes, true);
		$nodes = $this->_gen_nodes();
		$edges = $this->_gen_edges();

		$dot[] = sprintf("%s %s {", $this->graph_type(), $this->graph_name());
		$dot = array_merge($dot, $attributes, $nodes, $edges);
		$dot[] = sprintf("}");
		return join((($format=="html")?"<br>":"\n"), $dot);
	}

	/**
		@attrib params=pos api=1
		@comment
			Fetches graph name.
	**/
	function graph_name()
	{
		return $this->graph_name;
	}

	/**
		@attrib params=pos api=1
		@comment
			Fetches graph type.
	**/
	function graph_type()
	{
		return $this->graph;
	}

// END API






	function _gen_nodes()
	{
		$out = array();
		foreach($this->nodes as $nid => $data)
		{
			$out[] = sprintf("%s [%s];", $data["node"], join(", ", $this->_gen_attributes($data["attributes"])));
		}
		return $out;
	}

	function _gen_edges()
	{
		$out = array();
		foreach($this->edges as $eid => $data)
		{
			$out[] = sprintf("%s -> %s [%s];", $data["from"], $data["to"], join(", ", $this->_gen_attributes($data["attributes"])));
		}
		return $out;
	}

	function _gen_attributes($attributes, $graph_attrib = false)
	{
		$out = array();
		foreach($attributes as $attribute => $value)
		{
			switch($attribute)
			{
				case "size":
					$value = sprintf("%s,%s", $value["width"], $value["height"]);
					break;
			}
			$out[] = sprintf("%s = \"%s\"%s", $attribute, $value, ($graph_attrib?";":""));
		}
		return $out;
	}

	function _init_colors()
	{
		$this->colors_dark = array(
			1 => "Black",
			2 => "Blue",
			3 => "BlueViolet",
			4 => "Brown",
			5 => "CadetBlue",
			6 => "Chocolate",
			7 => "CornflowerBlue",
			8 => "Crimson",
			9 => "Navy",
			10 => "SteelBlue",
			11 => "DimGray",
			12 => "DarkGreen",
			13 => "Indigo",
			14 => "DeepPink",
			15 => "ForestGreen",
		);
		$this->colors_light = array(
			1 => "White",
			2 => "AntiqueWhite",
			3 => "Aquamarine",
			4 => "Chartreuse",
			5 => "Cyan",
			6 => "DeepSkyBlue",
			7 => "HotPink",
			8 => "Gainsboro",
			9 => "Gold",
			10 => "Goldenrod",
			11 => "GreenYellow",
			12 => "Lavender",
			13 => "LightBlue",
			14 => "LightPink",
			15 => "Violet",
		);
	}

	function _inverse_color($color)
	{
		return ($_no = array_search($color, $this->colors_dark))?$this->colors_light[$_no]:(($_no = array_search($color, $this->colors_light))?$this->colors_dark[$_no]:false);
	}

	/**
		@comment
			fixes umlauts etc in name.
	**/
	function _fix_name($name, $space = "_")
	{
		$name = strtolower($name);
		$name = trim($name);
		$to_replace = array("&auml;","&ouml;","&uuml;","&otilde;", " ");
		$replace_with = array("a","o","u","o", $space);
		$name = str_replace($to_replace, $replace_with, $name);
		$str = "!\"@#.$%&/()[]={}?\+-`'|,;";
		$name = str_replace(preg_split("//", $str, -1 , PREG_SPLIT_NO_EMPTY), "", $name);
		return $name;
	}

}
