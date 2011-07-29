<?php

class grid_editor extends aw_template
{
	function grid_editor()
	{
		$this->init(array(
			"tpldir" => "grid_editor"
		));
		$this->sub_merge = 1;
	}

	/** Initializes the grid editor with the data given
		@attrib api=1 params=pos

		@param data type=array
			The grid data

		@errors none
		@returns none

		@comment
			if you pass an empty array as the data, then the grid is initialized to an 1x1 grid with one empty cell.

		@examples
			$ge = new grid_editor();
			$ge->_init_table($obj->meta("grid"));
			$ge->set_cell_content(1,1, t("allah"));
			$obj->set_meta("$ge->_get_table());
	**/
	function _init_table($data)
	{
		$this->arr = safe_array($data);

		// do a sanity check on the table
		if (!isset($this->arr["cols"]) or $this->arr["cols"]  < 1 || !isset($this->arr["rows"]) or $this->arr["rows"]  < 1)
		{
			$this->arr["cols"] =1;
			if (!isset($this->arr["map"]) or !is_array($this->arr["map"]))
			{
				$this->arr["map"] = array();
			}

			if (!isset($this->arr["map"][0]) or !is_array($this->arr["map"][0]))
			{
				$this->arr["map"][0] = array();
			}

			if (!isset($this->arr["map"][0][0]) or !is_array($this->arr["map"][0][0]))
			{
				$this->arr["map"][0][0] = array();
			}
			$this->arr["map"][0][0] = array("row" => 0, "col" => 0);
			$this->arr["rows"] = 1;
		}

		// go over the map and correct all entries with -
		for($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for($col = 0; $col < $this->arr["cols"]; $col++)
			{
				if ($this->arr["map"][$row][$col]["row"] <= 0)
				{
					$this->arr["map"][$row][$col]["row"] = 0;
				}

				if ($this->arr["map"][$row][$col]["col"] <= 0)
				{
					$this->arr["map"][$row][$col]["col"] = 0;
				}
			}
		}

		// go over the map and eliminate empty indexes
		$nm = array();
		foreach($this->arr["map"] as $row => $rowd)
		{
			if ($row !== "")
			{
				foreach($rowd as $col => $cold)
				{
					if ($col !== "")
					{
						$nm[$row][$col]["row"] = (int)$this->arr["map"][$row][$col]["row"];
						$nm[$row][$col]["col"] = (int)$this->arr["map"][$row][$col]["col"];
					}
				}
			}
		}
		$this->arr["map"] = $nm;
	}

	function get_spans($i, $a, $map = -1,$rows = -1, $cols = -1)	// row, col
	{
		$i = (int)$i;
		$a = (int)$a;
		if ($map == -1)
		{
			$map = $this->arr["map"];
		}
		if ($rows == -1)
		{
			$rows = $this->arr["rows"];
		}
		if ($cols == -1)
		{
			$cols = $this->arr["cols"];
		}

		// find if this cell is the top left one of the area
		$topleft = true;
		if ($i > 0)
		{
			if ($map[$i-1][$a]["row"] == $map[$i][$a]["row"])
			{
				$topleft = false;
			}
		}
		if ($a > 0)
		{
			if ($map[$i][$a-1]["col"] == $map[$i][$a]["col"])
			{
				$topleft = false;
			}
		}

		if ($topleft)
		{
			// if it is, then show the correct cell and set the col/rowspan to correct values
			for ($t_row=$i; $t_row < $rows && $map[$t_row][$a]["row"] == $map[$i][$a]["row"]; $t_row++)
				;

			for ($t_col=$a; $t_col < $cols && $map[$i][$t_col]["col"] == $map[$i][$a]["col"]; $t_col++)
				;

			$rowspan = $t_row - $i;
			$colspan = $t_col - $a;

			$this->vars(array("colspan" => $colspan, "rowspan" => $rowspan));
			if ($colspan > 1)
			{
				$r_col = $map[$i][$a]["col"];
			}
			else
			{
				$r_col = $a;
			}

			if ($rowspan > 1)
			{
				$r_row = $map[$i][$a]["row"];
			}
			else
			{
				$r_row = $i;
			}

			return array("colspan" => $colspan, "rowspan" => $rowspan, "r_row" => $r_row, "r_col" => $r_col);
		}
		else
		{
			return false;
		}
	}

	/** displays the main grid editing table
		@attrib api=1 params=pos

		@param data required type=array
			The grid data, returned by the grid editor

		@param oid required type=oid
			The object id of the object the grid is attached to

		@param params optional type=array
			An array of other paramters, can contain:
				cell_content_callback => array(&$object, "method_name", $parameter_to_method)
					if this key is set, then for each cell in the grid, this method will be called with
					the parameter given, the row and the column of the cell and it must return the html
					that will be displayed in the editing view in the cell

		@returns The html for the main grid editing view

		@errors	none

		@comment
			The main grid editing view is the view where you can merge cells and add/remove columns or rows, you can't edit cell content

		@examples
			A part of the get_property method from the CL_LAYOUT class:
			function get_property(&$arr)
			{
				$prop = &$arr['prop'];
				switch($prop["name"])
				{
					case "grid":
						$ge = new grid_editor();
						$prop['value'] = $ge->on_edit($arr['obj_inst']->meta('grid'), $arr['obj_inst']->id());
						break;
				}
				return PROP_OK;
			}

	**/
	function on_edit($data, $oid, $params = array())
	{
		$this->_init_table($data);

		$this->read_template("grid.tpl");

		//$this->debug_map_print();

		$this->_get_edit_toolbar();

		for ($col = 0; $col < $this->arr["cols"]; $col++)
		{
			$fc = "";
			if ($col == 0)
			{
				$this->vars(array(
					"after" => "-1",
					"add_col_caption" => t("Lisa tulp"),
				));
				$fc = $this->parse("FIRST_C");
			}
			$this->vars(array(
				"FIRST_C" => $fc,
				"col" => $col,
				"col" => $col,
				"after" => (int)$col,
				"confirm_col_del" => t("Oled kindel et tahad tulpa kustutada?"),
				"del_col" => t("Kustuta tulp"),
				"add_col" => t("Lisa tulp"),
			));
			$this->parse("DC");
		}

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$col="";
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				$cell = $this->arr["styles"][$i][$a];

				$this->vars(array(
					"col" => $a,
					"row" => $i,
				));

				if (!($spans = $this->get_spans($i, $a)))
				{
					continue;
				}

				$sh = $sv = $eu = $el = $er = $ed = "<img src='".aw_ini_get("baseurl")."automatweb/images/trans.gif' width='8' height='8'>";
				$sh = "<a href='javascript:split_hor($i,$a)'><img alt=\"".t("Jaga pooleks horisontaalselt")."\" title=\"".t("Jaga pooleks horisontaalselt")."\" src='".aw_ini_get("baseurl")."automatweb/images/split_cell_down.gif' border=0></a>";
				$sv = "<a href='javascript:split_ver($i,$a)'><img alt=\"".t("Jaga pooleks vertikaalselt")."\" title=\"".t("Jaga pooleks vertikaalselt")."\" src='".aw_ini_get("baseurl")."automatweb/images/split_cell_left.gif' border=0></a>";

				if ($i != 0)
				{
					$eu = "<a href='javascript:exp_up($i,$a)'><img border=0 alt=\"".t("Kustuta &uuml;lemine cell")."\" title=\"".t("Kustuta &uuml;lemine cell")."\" src='".aw_ini_get("baseurl")."automatweb/images/up_r_arr.gif'></a>";
				}
				if ($a != 0)
				{
					$el = "<a href='javascript:exp_left($i,$a)'><img border=0 alt=\"".t("Kustuta vasak cell")."\" title=\"".t("Kustuta vasak cell")."\" src='".aw_ini_get("baseurl")."automatweb/images/left_r_arr.gif'></a>";
				}
				if (($a+$spans["colspan"]) != $this->arr["cols"])
				{
					$er="<a href='javascript:exp_right($i,$a)'><img border=0 alt=\"".t("Kustuta parem cell")."\" title=\"".t("Kustuta parem cell")."\" src='".aw_ini_get("baseurl")."automatweb/images/right_r_arr.gif'></a>";
				}
				if (($i+$spans["rowspan"]) != $this->arr["rows"])
				{
					$ed = "<a href='javascript:exp_down($i,$a)'><img border=0 alt=\"".t("Kustuta alumine cell")."\" title=\"".t("Kustuta alumine cell")."\" src='".aw_ini_get("baseurl")."automatweb/images/down_r_arr.gif'></a>";
				}

				$map = $this->arr["map"][$i][$a];

				$this->vars(array(
					"SPLIT_HORIZONTAL"	=> $sh,
					"SPLIT_VERTICAL"		=> $sv,
					"EXP_UP"						=> $eu,
					"EXP_LEFT"					=> $el,
					"EXP_RIGHT"					=> $er,
					"EXP_DOWN"					=> $ed,
					"content_text" => htmlspecialchars(substr($this->arr["aliases"][$map["row"]][$map["col"]], 0, 20))
				));

				// do the callback if specified
				$cc = "";
				if (is_array($params) && isset($params['cell_content_callback']))
				{
					$that =& $params['cell_content_callback'][0];
					$fun =& $params['cell_content_callback'][1];
					$parms =& $params['cell_content_callback'][2];
					$cc = $that->$fun($parms, $map['row'], $map['col']);
				}
				else
				{
					$cc = $this->parse("COL_CONTENT");
				}
				$this->vars(array(
					"COL_CONTENT" => $cc,
				));
				$col.=$this->parse("COL");
			}
			$fr = "";
			if ($i == 0)
			{
				$this->vars(array(
					"after" => "-1",
					"add_row" => t("Lisa rida"),
					"del_row" => t("Kustuta rida"),
					"confirm_row_del" => t("Oled kindel et soovid rida kustutada?"),
				));
				$fr = $this->parse("FIRST_R");
			}
			$this->vars(array(
				"COL"	=> $col,
				"FIRST_R" => $fr,
				"row" => $i,
				"after" => $i
			));

			$this->parse("LINE");
		}

		$this->vars(array(
			"selstyle" => $this->mk_my_orb("sel_style", array(), "layout"),
			"cell_count" => t("Mitu lahtrit kustutada"),
			"oid" => $oid
		));
		return $this->parse();
	}

	/** this should be called when the main editing view is submitted, this will handle the submit and return the modified grid data
		@attrib api=1 params=pos

		@param data required type=array
			The grid data

		@param post required type=array
			The contents of the POST request

		@param params optional type=array
			An array of other paramters, can contain:
				cell_content_callback => array(&$object, "method_name", $parameter_to_method)
					if this key is set, then for each cell in the grid, this method will be called with
					the parameter given, the row and the column of the cell and the post data. it must return the data that will be
					stored in that cell

		@returns The updated grid data, that should be stored somewhere by the caller

		@errors none

		@examples
			function set_property(&$arr)
			{
				$prop = &$arr['prop'];
				switch($prop["name"])
				{
					case "grid":
						$ge = new grid_editor();
						$prop['value'] = $ge->on_edit_submit($arr['obj_inst']->meta('grid'), $arr['request']);
						break;
				}
			}
	**/
	function on_edit_submit($data, $post, $params = array())
	{
		$this->_init_table($data);

		// process actions
		$actions = $post["ge_action"];
		$data = explode(";", $actions);

		$cmds = array("fulldata" => $actions);
		foreach($data as $pair)
		{
			list($k, $v) = explode("=", $pair);
			$cmds[$k] = $v;
		}

		$this->_process_command($cmds);

		if ($params['cell_content_callback'])
		{
			// call the save content handler for each cell
			for($_row = 0; $_row < $this->arr["rows"]; $_row++)
			{
				for($_col = 0; $_col < $this->arr["cols"]; $_col++)
				{
					if (!($spans = $this->get_spans($_row, $_col)))
					{
						continue;
					}
					$map = $this->arr["map"][$_row][$_col];
					$that =& $params['cell_content_callback'][0];
					$fun =& $params['cell_content_callback'][1];
					$parms =& $params['cell_content_callback'][2];
					$this->arr['aliases'][$map['row']][$map['col']] =  $that->$fun($parms, $map['row'], $map['col'], $post);
				}
			}
		}

		// delete selected rows/cols
/*		$cdelete = array();
		$rdelete = array();
		foreach($post as $k => $v)
		{
			if (substr($k,0,3) == 'dc_' && $v==1)
			{
				$cdelete[substr($k,3)] = substr($k,3);
			}
			else
			if (substr($k,0,3) == 'dr_' && $v==1)
			{
				$rdelete[substr($k,3)] = substr($k,3);
			}
		}

		// kustutame tagant-ettepoole, niiet numbrid ei muutuks
		krsort($cdelete,SORT_NUMERIC);
		krsort($rdelete,SORT_NUMERIC);

		foreach($cdelete as $k => $v)
		{
			$this->_del_col(array("col" => $v));
		}

		foreach($rdelete as $k => $v)
		{
			$this->_del_row(array("row" => $v));
		}*/

		return $this->arr;
	}

	/** modifies the cell content for a cell in the grid
		@attrib api=1 params=pos

		@param row required type=int
			The row the cell is in

		@param col required type=int
			The column the cell is in

		@param str required type=string
			The content of the cell

		@returns none
		@errors none
		@examples
			Example is in the _init_table documentation

	**/
	function set_cell_content($row, $col, $str)
	{
		$this->arr['aliases'][$row][$col] = $str;
	}

	function _process_command($data)
	{
		$fn = "_".$data["action"];
		unset($data["action"]);
		if (method_exists($this, $fn))
		{
			$this->$fn($data);
		}
	}

	function _delete_rc($data)
	{
		$cdelete = array();
		$rdelete = array();
		foreach(explode("dc_", $data["cols"]) as $k => $v)
		{
			if ($v === "")
			{
				continue;
			}
			$cdelete[$v] = $v;
		}

		foreach(explode("dr_", $data["rows"]) as $k => $v)
		{
			if ($v === "")
			{
				continue;
			}
			$rdelete[$v] = $v;
		}

		// kustutame tagant-ettepoole, niiet numbrid ei muutuks
		krsort($cdelete,SORT_NUMERIC);
		krsort($rdelete,SORT_NUMERIC);

		foreach($cdelete as $k => $v)
		{
			$this->_del_col(array("col" => $v));
		}

		foreach($rdelete as $k => $v)
		{
			$this->_del_row(array("row" => $v));
		}
	}


	function _add_col($arr)
	{
		extract($arr);

		if ($num != 0)
		{
			for ($nnn=0; $nnn < $num; $nnn++)
			{
				for ($row = 0; $row < $this->arr["rows"]; $row++)
				{
					for ($col = $this->arr["cols"]; $col > $after; $col--)
					{
						$this->arr["aliases"][$row][$col] = $this->arr["aliases"][$row][$col-1];
						$this->arr["styles"][$row][$col] = $this->arr["styles"][$row][$col-1];
					}
				}

				if ($after != -1)
				{
					for ($row = 0; $row < $this->arr["rows"]; $row++)
					{
						$this->arr["aliases"][$row][$after+1] = "";
						$this->arr["styles"][$row][$after+1] = "";
					}
				}


				$this->arr["cols"]++;

				$nm = array();
				for ($row =0; $row < $this->arr["rows"]; $row++)
				{
					for ($col=0; $col <= $after; $col++)
					{
						$nm[$row][$col] = $this->arr["map"][$row][$col];		// copy the left part of the map
					}
				}

				$change = array();
				for ($row = 0; $row < $this->arr["rows"]; $row++)
					for ($col=$after+1; $col < ($this->arr["cols"]-1); $col++)
					{
						if ($this->arr["map"][$row][$col]["col"] > $after)
						{
							$nm[$row][$col+1]["col"] = $this->arr["map"][$row][$col]["col"]+1;
							$nm[$row][$col+1]["row"] = $this->arr["map"][$row][$col]["row"];
							$change[] = array("from" => $this->arr["map"][$row][$col], "to" => $nm[$row][$col+1]);
						}
						else
						{
							$nm[$row][$col+1] = $this->arr["map"][$row][$col];
						}
					}

				reset($change);
				while (list(,$v) = each($change))
				{
					for ($row=0; $row < $this->arr["rows"]; $row++)
					{
						for ($col=0; $col <= $after; $col++)
						{
							if ($this->arr["map"][$row][$col] == $v["from"])
							{
								$nm[$row][$col] = $v["to"];
							}
						}
					}
				}

				for ($row = 0; $row < $this->arr["rows"]; $row++)
				{
					if ($this->arr["map"][$row][$after] == $this->arr["map"][$row][$after+1])
					{
						$nm[$row][$after+1] = $nm[$row][$after];
					}
					else
					{
						$nm[$row][$after+1] = array("row" => $row, "col" => $after+1);
					}
				}

				$this->arr["map"] = $nm;
			}
		}
		else
		{
			$this->arr["cols"] ++;

			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				for ($col = $this->arr["cols"]-1; $col > $after; $col--)
				{
					$this->arr["aliases"][$row][$col] = $this->arr["aliases"][$row][$col-1];
				}
			}

			if ($after != -1)
			{
				for ($row = 0; $row < $this->arr["rows"]; $row++)
				{
					$this->arr["aliases"][$row][$after+1] = "";
				}
			}

			$nm = array();
			for ($row =0; $row < $this->arr["rows"]; $row++)
			{
				for ($col=0; $col <= $after; $col++)
				{
					$nm[$row][$col] = $this->arr["map"][$row][$col];		// copy the left part of the map
				}
			}

			$change = array();
			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				for ($col=$after+1; $col < ($this->arr["cols"]-1); $col++)
				{
					if ($this->arr["map"][$row][$col]["col"] > $after)
					{
						$nm[$row][$col+1]["col"] = $this->arr["map"][$row][$col]["col"]+1;
						$nm[$row][$col+1]["row"] = $this->arr["map"][$row][$col]["row"];
						$change[] = array("from" => $this->arr["map"][$row][$col], "to" => $nm[$row][$col+1]);
					}
					else
					{
						$nm[$row][$col+1] = $this->arr["map"][$row][$col];
					}
				}
			}

			reset($change);
			while (list(,$v) = each($change))
			{
				for ($row=0; $row < $this->arr["rows"]; $row++)
				{
					for ($col=0; $col <= $after; $col++)
					{
						if ($this->arr["map"][$row][$col] == $v["from"])
						{
							$nm[$row][$col] = $v["to"];
						}
					}
				}
			}

			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				if ($this->arr["map"][$row][$after] == $this->arr["map"][$row][$after+1])
				{
					$nm[$row][$after+1] = $nm[$row][$after];
				}
				else
				{
					$nm[$row][$after+1] = array("row" => $row, "col" => $after+1);
				}
			}

			$this->arr["map"] = $nm;
		}
	}

	function _add_row($arr)
	{
		extract($arr);
		if ($num != 0)
		{
			for ($nnn=0; $nnn < $num; $nnn++)
			{
				for ($col = 0; $col < $this->arr["cols"]; $col++)
				{
					for ($row = $this->arr["rows"]; $row > $after; $row--)
					{
						$this->arr["aliases"][$row][$col] = $this->arr["aliases"][$row-1][$col];
						$this->arr["styles"][$row][$col] = $this->arr["styles"][$row-1][$col];
					}
				}

				if ($after != -1)
				{
					for ($col = 0; $col < $this->arr["cols"]; $col++)
					{
						$this->arr["aliases"][$after+1][$col] = "";
						$this->arr["styles"][$after+1][$col] = "";
					}
				}


				$this->arr["rows"]++;

				$nm = array();
				for ($row =0; $row <= $after; $row++)
				{
					for ($col=0; $col < $this->arr["cols"]; $col++)
					{
						$nm[$row][$col] = $this->arr["map"][$row][$col];		// copy the upper part of the map
					}
				}

				$change = array();
				for ($row = $after+1; $row < ($this->arr["rows"]-1); $row++)
				{
					for ($col=0; $col < $this->arr["cols"]; $col++)
					{
						if ($this->arr["map"][$row][$col]["row"] > $after)
						{
							$nm[$row+1][$col]["col"] = $this->arr["map"][$row][$col]["col"];
							$nm[$row+1][$col]["row"] = $this->arr["map"][$row][$col]["row"]+1;
							$change[] = array("from" => $this->arr["map"][$row][$col], "to" => $nm[$row+1][$col]);
						}
						else
						{
							$nm[$row+1][$col] = $this->arr["map"][$row][$col];
						}
					}
				}

				reset($change);
				while (list(,$v) = each($change))
				{
					for ($row=0; $row <= $after; $row++)
					{
						for ($col=0; $col < $this->arr["cols"]; $col++)
						{
							if ($this->arr["map"][$row][$col] == $v["from"])
							{
								$nm[$row][$col] = $v["to"];
							}
						}
					}
				}

				for ($col = 0; $col < $this->arr["cols"]; $col++)
				{
					if ( $this->arr["map"][$after][$col] == $this->arr["map"][$after+1][$col])
					{
						$nm[$after+1][$col] = $nm[$after][$col];
					}
					else
					{
						$nm[$after+1][$col] = array("row" => $after+1, "col" => $col);
					}
				}

				$this->arr["map"] = $nm;
			}
		}
		else
		{
			$this->arr["rows"] ++;

			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				for ($row = $this->arr["rows"]-1; $row > $after; $row--)
				{
					$this->arr["aliases"][$row][$col] = $this->arr["aliases"][$row-1][$col];
					$this->arr["styles"][$row][$col] = $this->arr["styles"][$row-1][$col];
				}
			}

			if ($after != -1)
			{
				for ($col = 0; $col < $this->arr["cols"]; $col++)
				{
					$this->arr["aliases"][$after+1][$col] = "";
					$this->arr["styles"][$after+1][$col] = "";
				}
			}

			$nm = array();
			for ($row =0; $row <= $after; $row++)
			{
				for ($col=0; $col < $this->arr["cols"]; $col++)
				{
					$nm[$row][$col] = $this->arr["map"][$row][$col];		// copy the upper part of the map
				}
			}

			$change = array();
			for ($row = $after+1; $row < ($this->arr["rows"]-1); $row++)
			{
				for ($col=0; $col < $this->arr["cols"]; $col++)
				{
					if ($this->arr["map"][$row][$col]["row"] > $after)
					{
						$nm[$row+1][$col]["col"] = $this->arr["map"][$row][$col]["col"];
						$nm[$row+1][$col]["row"] = $this->arr["map"][$row][$col]["row"]+1;
						$change[] = array("from" => $this->arr["map"][$row][$col], "to" => $nm[$row+1][$col]);
					}
					else
					{
						$nm[$row+1][$col] = $this->arr["map"][$row][$col];
					}
				}
			}

			reset($change);
			while (list(,$v) = each($change))
			{
				for ($row=0; $row <= $after; $row++)
				{
					for ($col=0; $col < $this->arr["cols"]; $col++)
					{
						if ($this->arr["map"][$row][$col] == $v["from"])
						{
							$nm[$row][$col] = $v["to"];
						}
					}
				}
			}

			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				if ( $this->arr["map"][$after][$col] == $this->arr["map"][$after+1][$col])
				{
					$nm[$after+1][$col] = $nm[$after][$col];
				}
				else
				{
					$nm[$after+1][$col] = array("row" => $after+1, "col" => $col);
				}
			}

			$this->arr["map"] = $nm;
		}
	}

	function _del_col($arr)
	{
		extract($arr);
		if (!isset($d_col))
		{
			$d_col  = $col;
		}
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($c = $col+1; $c < $this->arr["cols"]; $c++)
			{
				$this->arr["aliases"][$row][$c-1] = $this->arr["aliases"][$row][$c];
				$this->arr["styles"][$row][$c-1] = $this->arr["styles"][$row][$c];
			}
		}

		$nm = array();
		for ($row =0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $d_col; $col++)
			{
				$nm[$row][$col] = $this->arr["map"][$row][$col];	// copy the left part of the map
			}
		}

		$changes = array();
		for ($row =0 ; $row < $this->arr["rows"]; $row++)
		{
			for ($col = $d_col+1; $col < $this->arr["cols"]; $col++)
			{
				if ($this->arr["map"][$row][$col]["col"] > $d_col)
				{
					$nm[$row][$col-1] = array("row" => $this->arr["map"][$row][$col]["row"], "col" => $this->arr["map"][$row][$col]["col"]-1);
					$changes[] = array(
						"from" => $this->arr["map"][$row][$col],
						"to" => array(
							"row" => $this->arr["map"][$row][$col]["row"],
							"col" => $this->arr["map"][$row][$col]["col"]-1
						)
					);
				}
				else
				{
					$nm[$row][$col-1] = $this->arr["map"][$row][$col];
				}
			}
		}
		$this->arr["map"] = $nm;

		reset($changes);
		while (list(,$v) = each($changes))
		{
			for ($row=0; $row < $this->arr["rows"]; $row++)
			{
				for ($col=0; $col < $d_col; $col++)
				{
					if ($this->arr["map"][$row][$col] == $v["from"])
					{
						$this->arr["map"][$row][$col] = $v["to"];
					}
				}
			}
		}

		$this->arr["cols"]--;
	}

	function _del_row($arr)
	{
		extract($arr);
		if (!isset($d_row))
		{
			$d_row = $row;
		}
		for ($col = 0; $col < $this->arr["cols"]; $col++)
		{
			for ($r = $row+1; $r < $this->arr["rows"]; $r++)
			{
				$this->arr["aliases"][$r-1][$col] = $this->arr["aliases"][$r][$col];
				$this->arr["styles"][$r-1][$col] = $this->arr["styles"][$r][$col];
			}
		}

		$nm = array();
		for ($row =0; $row < $d_row; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$nm[$row][$col] = $this->arr["map"][$row][$col];	// copy the upper part of the map
			}
		}

		$changes = array();
		for ($row =$d_row+1 ; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				if ($this->arr["map"][$row][$col]["row"] > $d_row)
				{
					$nm[$row-1][$col] = array("row" => $this->arr["map"][$row][$col]["row"]-1, "col" => $this->arr["map"][$row][$col]["col"]);
					$changes[] = array(
						"from" => $this->arr["map"][$row][$col],
						"to" => array(
							"row" => $this->arr["map"][$row][$col]["row"]-1,
							"col" => $this->arr["map"][$row][$col]["col"]
						)
					);
				}
				else
				{
					$nm[$row-1][$col] = $this->arr["map"][$row][$col];
				}
			}
		}
		$this->arr["map"] = $nm;

		reset($changes);
		while (list(,$v) = each($changes))
		{
			for ($row=0; $row < $d_row; $row++)
			{
				for ($col=0; $col < $this->arr["cols"]; $col++)
				{
					if ($this->arr["map"][$row][$col] == $v["from"])
					{
						$this->arr["map"][$row][$col] = $v["to"];
					}
				}
			}
		}

		$this->arr["rows"]--;
	}

	function _exp_up($arr)
	{
		extract($arr);
		// here we don't need to find the upper bound, because this always is the upper bound
		// first we must find out the colspan of the current cell and set all the cell above that one to the correct values in the map
		for ($c=0; $c < $cnt; $c++)
		{
			if ($row > 0)
			{
				for ($a=0; $a < $this->arr["cols"]; $a++)
				{
					if ($this->arr["map"][$row][$a] == $this->arr["map"][$row][$col])
					{
						$this->arr["map"][$row-1][$a] = $this->arr["map"][$row][$col];		// expand the area
					}
				}
			}
			$row--;
		}
	}

	function _exp_down($arr)
	{
		extract($arr);

		for ($c=0; $c < $cnt; $c++)
		{
			// here we must first find the lower bound for the area being expanded and use that instead the $row, because
			// that is an arbitrary position in the area really.
			for ($i=$row; $i < $this->arr["rows"]; $i++)
			{
				if ($this->arr["map"][$i][$col] == $this->arr["map"][$row][$col])
				{
					$r=$i;
				}
				else
				{
					break;
				}
			}

			if (($r+1) < $this->arr["rows"])
			{
				for ($a=0; $a < $this->arr["cols"]; $a++)
				{
					if ($this->arr["map"][$row][$a] == $this->arr["map"][$row][$col])
					{
						$this->arr["map"][$r+1][$a] = $this->arr["map"][$row][$col];		// expand the area
					}
				}
			}
		}
	}

	function _exp_left($arr)
	{
		extract($arr);

		// again, this is the left bound, so we don't need to find it
		for ($c=0; $c < $cnt; $c++)
		{
			if ($col > 0)
			{
				for ($a =0; $a < $this->arr["rows"]; $a++)
				{
					if ($this->arr["map"][$a][$col] == $this->arr["map"][$row][$col])
					{
						$this->arr["map"][$a][$col-1] = $this->arr["map"][$row][$col];		// expand the area
					}
				}
			}
			$col--;
		}
	}

	function _exp_right($arr)
	{
		extract($arr);
		$col = (int)$col;
		$row = (int)$row;
		// here we must first find the right bound for the area being expanded and use that instead the $row, because
		// that is an arbitrary position in the area really.
		for ($c=0; $c < $cnt; $c++)
		{
			$r = 0;
			for ($i=$col; $i < $this->arr["cols"]; $i++)
			{
				if ($this->arr["map"][$row][$i] == $this->arr["map"][$row][$col])
				{
					$r=$i;
				}
				else
				{
					break;
				}
			}

			if (($r+1) < $this->arr["cols"])
			{
				for ($a =0; $a < $this->arr["rows"]; $a++)
				{
					if ($this->arr["map"][$a][$r] == $this->arr["map"][$row][$r])
					{
						$this->arr["map"][$a][$r+1] = $this->arr["map"][$row][$r];		// expand the area
					}
				}
			}
		}
	}

	function _split_ver($arr)
	{
		extract($arr);

		$lbound = -1;
		for ($i=0; $i < $this->arr["cols"] && $lbound==-1; $i++)
		{
			if ($this->arr["map"][$row][$i] == $this->arr["map"][$row][$col])
			{
				$lbound = $i;
			}
		}

		$rbound = -1;
		for ($i=$lbound; $i < $this->arr["cols"] && $rbound==-1; $i++)
		{
			if ($this->arr["map"][$row][$i] != $this->arr["map"][$row][$col])
			{
				$rbound = $i-1;
			}
		}

		if ($rbound == -1)
		{
			$rbound = $this->arr["cols"]-1;
		}

		$nm = array();
		$center = ($rbound+$lbound)/2;

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				if ($this->arr["map"][$i][$a] == $this->arr["map"][$row][$col])
				{
					if ($this->arr["map"][$i][$a]["col"] < $center)
					{
						// the hotspot of the cell is on the left of the splitter
						if ($a <= $center)
						{
							// and we currently are also on the left side then leave it be
							$nm[$i][$a] = $this->arr["map"][$i][$a];
						}
						else
						{
							// and we are on the right side choose a new one
							$nm[$i][$a] = array("row" => $this->arr["map"][$i][$a]["row"], "col" => floor($center)+1);
						}
					}
					else
					{
						// the hotspot of the cell is on the right of the splitter
						if ($a <= $center)
						{
							// and we are on the left side choose a new one
							$nm[$i][$a] = array("row" => $this->arr["map"][$i][$a]["row"], "col" => $lbound);
						}
						else
						{
							// if we are on the same side, use the current value
							$nm[$i][$a] = $this->arr["map"][$i][$a];
						}
					}
				}
				else
				{
					$nm[$i][$a] = $this->arr["map"][$i][$a];
				}
			}
		}

		$this->arr["map"] = $nm;
	}

	function _split_hor($arr)
	{
		extract($arr);

		$ubound = -1;
		for ($i=0; $i < $this->arr["rows"] && $ubound==-1; $i++)
		{
			if ($this->arr["map"][$i][$col] == $this->arr["map"][$row][$col])
			{
				$ubound = $i;
			}
		}

		$lbound = -1;
		for ($i=$ubound; $i < $this->arr["rows"] && $lbound==-1; $i++)
		{
			if ($this->arr["map"][$i][$col] != $this->arr["map"][$row][$col])
			{
				$lbound = $i-1;
			}
		}

		if ($lbound == -1)
		{
			$lbound = $this->arr["rows"]-1;
		}

		$nm = array();
		$center = ($ubound+$lbound)/2;

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				if ($this->arr["map"][$i][$a] == $this->arr["map"][$row][$col])
				{
					if ($this->arr["map"][$i][$a]["row"] < $center)
					{
						// the hotspot of the cell is above the splitter
						if ($i <= $center)
						{
							// and we currently are also above then leave it be
							$nm[$i][$a] = $this->arr["map"][$i][$a];
						}
						else
						{
							// and we are below choose a new one
							$nm[$i][$a] = array("row" => floor($center)+1, "col" => $this->arr["map"][$i][$a]["col"]);
						}
					}
					else
					{
						// the hotspot of the cell is below the splitter
						if ($i <= $center)
						{
							// but we are above, so make new
							$nm[$i][$a] = array("row" => $ubound, "col" => $this->arr["map"][$i][$a]["col"]);
						}
						else
						{
							// if we are on the same side, use the current value
							$nm[$i][$a] = $this->arr["map"][$i][$a];
						}
					}
				}
				else
				{
					$nm[$i][$a] = $this->arr["map"][$i][$a];
				}
			}
		}

		$this->arr["map"] = $nm;
	}

	/** displays the grid content editing table
		@attrib api=1 params=pos

		@param data required type=array
			The grid data, returned by the grid editor

		@param oid required type=oid
			The object id of the object the grid is attached to

		@returns The html for the grid content editing view

		@errors	none

		@comment
			The main grid content editing view is the view where you can edit cell content

		@examples
			A part of the get_property method from the CL_LAYOUT class:
			function get_property(&$arr)
			{
				$prop = &$arr['prop'];
				switch($prop["name"])
				{
					case "grid_aliases":
						$ge = new grid_editor();
						$prop['value'] = $ge->on_aliases_edit($arr['obj_inst']->meta('grid'), $arr['obj_inst']->id());
						break;
				}
				return PROP_OK;
			}

	**/
	function on_aliases_edit($data, $oid)
	{
		$this->_init_table($data);

		$this->read_template("grid_aliases.tpl");
		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$col="";
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				if (!($spans = $this->get_spans($i, $a)))
				{
					continue;
				}

				$colw = !empty($this->arr["col_widths"][$a]) ? $this->arr["col_widths"][$a] : $spans["colspan"]*150+($spans["colspan"]-1)*7;
				$colh = !empty($this->arr["col_heights"][$a]) ? $this->arr["col_heights"][$a] : $spans["rowspan"]*17+($spans["rowspan"]-1)*9;


				$map = $this->arr["map"][$i][$a];

				$this->vars(array(
					"col" => $map["col"],
					"row" => $map["row"],
					"ta_rows" => $spans["rowspan"],
					"ta_cols" => $spans["colspan"]*20+(($spans["colspan"]-1)*2),
					"content" => htmlspecialchars($this->arr['aliases'][$map['row']][$map['col']]),
					"width" => $colw,
					"height" => $colh,
				));

				$col.=$this->parse("COL_TA");
			}
			$this->vars(array(
				"COL_TA"	=> $col,
			));

			$this->parse("LINE");
		}

		return $this->parse();
	}

	/** this should be called when the content editing view is submitted, this will handle the submit and return the modified grid data
		@attrib api=1 params=pos

		@param data required type=array
			The grid data

		@param post required type=array
			The contents of the POST request

		@returns The updated grid data, that should be stored somewhere by the caller

		@errors none

		@examples
			function set_property(&$arr)
			{
				$prop = &$arr['prop'];
				switch($prop["name"])
				{
					case "grid_aliases":
						$ge = new grid_editor();
						$prop['value'] = $ge->on_aliases_edit_submit($arr['obj_inst']->meta('grid'), $arr['request']);
						break;
				}
			}
	**/
	function on_aliases_edit_submit($data, $post)
	{
		$this->_init_table($data);

		$this->arr["aliases"] = $post["aliases"];

		return $this->arr;
	}

	/** The method to display the grid
		@attrib api=1 params=pos

		@param data required type=array
			The grid data

		@param oid required type=oid
			The object the grid is attached to (used for alias parsing)

		@param tpls optional type=array
			Array of templates that get passed to alias_parser::parse_oo_aliases

		@errors none

		@returns
			The html containing the grid and any aliases it might contain

		@examples
			$gr = new grid_editor();
			$gr->_init_table(array());
			$gr->set_cell_content(1,1, t("allah"));
			echo $gr->show_editor($gr->_get_table(), $obj->id());		// displays a table with a single cell, that contains the word "allah"
	**/
	function show_editor($data, $oid, &$tpls = array())
	{
		$this->_init_table($data);

		$stc = get_instance(CL_STYLE);
		$this->_init_show_styles();
		if ($this->arr["table_style"])
		{
			$table.= "<table border=\"0\" ".$stc->get_table_string($this->arr["table_style"]).">";
		}
		else
		{
			$table.= "<table>";
		}

		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			$cs = "";
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				if (!($spans = $this->get_spans($row, $col)))
				{
					continue;
				}

				$map = $this->arr["map"][$row][$col];

				$cell = $this->arr["aliases"][$map["row"]][$map["col"]];
				$scell = $this->arr["styles"][$map["row"]][$map["col"]];

				$st = $this->_get_cell_style_id($row, $col, $scell);
				if ($st)
				{
					// ok, this is going to really slow :(
					// but i don't see any better solution right now, cause
					// every cell can have its own style (which is object).
					$adds = "";
					if (is_oid($st) && $this->can("view", $st))
					{
						$style_object = new object($st);
						$style_from_site_css = $style_object->prop("site_css");
						if ($style_object->prop("nowrap"))
						{
							$adds = " nowrap=1 ";
						}
					}
					if (empty($style_from_site_css))
					{
						$cs .= "<td $adds colspan=\"".$spans["colspan"]."\" rowspan=\"".$spans["rowspan"]."\" class=\"st".$st."\">";
						active_page_data::add_site_css_style($st);
					}
					else
					{
						$cs .= "<td colspan=\"".$spans["colspan"]."\" rowspan=\"".$spans["rowspan"]."\" class=\"".$style_from_site_css."\">";

					}
				}
				else
				{
					$cs .= "<td colspan=\"".$spans["colspan"]."\" rowspan=\"".$spans["rowspan"]."\">";
				}

				$tmp = trim(str_replace("\n", "<br/>", $this->arr["aliases"][$map["row"]][$map["col"]]));
				if (strpos($tmp, "<a") === false && strpos($tmp, "< a") === false && strpos($tmp, "<A") === false)
				{
					$tmp = create_links($tmp);
				}

				if ($tmp === "")
				{
					$tmp = '<img src="'.aw_ini_get("baseurl").'automatweb/images/transparent.gif" width="1" height="1" alt="" />';
				}
				$cs .= $tmp;

				if ($st)
				{
					$cs.= $stc->get_cell_end_str($st);
				}

				$cs.= "</td>";
			}
			$rs.="<tr>".$cs."</tr>";
		}
		$table.=$rs."</table>";

		$d = get_instance(CL_DOCUMENT);
		$d->create_relative_links($table);

		$al = new alias_parser();
		unset($tpls["image_inplace"]);
		unset($tpls["image_inplace_linked"]);
		$al->parse_oo_aliases($oid, $table, array("templates" => $tpls));

		return $table;
	}

	/** shows layout with template
		@attrib api=1 params=pos

		@param data required type=array
			The grid data

		@param oid required type=oid
			The object the grid is attached to (used for alias parsing)

		@param params optional type=array
			An array of other paramters, can contain:
				cell_content_callback => array(&$object, "method_name", $parameter_to_method)
					if this key is set, then for each cell in the grid, this method will be called with
					the parameter given, the row and the column of the cell and it must return the html
					that will be displayed in the cell
				tpl - the name of the template file to use for displaying the grid
				ignore_empty - true/false - whether to ignore empty cells or not

		@errors
			error is thrown if the template given is not found

		@returns
			The html for the rendered grid

		@examples
			$gr = get_instance("vcl/grid_editor");
			$gr->_init_table(array());
			$gr->set_cell_content(1,1, t("allah"));
			echo $gr->show_editor($gr->_get_table(), $obj->id(), array("tpl" => "contentmgmt/layout/pretty_grid.tpl"));		// displays a table with a single cell, that contains the word "allah"
	**/
	function show_tpl($data, $oid, $params)
	{
		extract($params);

		$this->tpl_init();
		// hmmm?
		$this->sub_merge = 1;
		$this->_init_table($data);

		$this->read_any_template($tpl);

		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			$cs = "";
			$has_content = false;
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				if (!($spans = $this->get_spans($row, $col)))
				{
					continue;
				}

				$ct = "";
				if (is_array($params) && isset($params['cell_content_callback']))
				{
					$map = $this->arr["map"][$row][$col];
					$that =& $params['cell_content_callback'][0];
					$fun =& $params['cell_content_callback'][1];
					$parms =& $params['cell_content_callback'][2];
					$ct = $that->$fun($parms, $map['row'], $map['col']);
				}

				$this->vars(array(
					"colspan" => $spans["colspan"],
					"rowspan" => $spans["rowspan"],
					"content" => $ct
				));

				if ($ct != "")
				{
					$has_content = true;
				}
				$cs .= $this->parse("CELL");
			}
			$this->vars(array(
				"CELL" => $cs
			));

			if (!$ignore_empty || ($ignore_empty && $has_content))
			{
				$l .= $this->parse("LINE");
			}
		}

		return $this->parse();
	}

	/** sets the style for the given row
		@attrib api=1 params=pos

		@param row required type=int
			The row to set the style for

		@param style required type=oid
			The style object to set to the row

		@comment
			Iterates over the cells in the row and sets the style to each cell

		@errors none
		@returns none

		@examples
			$ge = new grid_editor();
			$ge->_init_table($obj->meta("grid"));
			$ge->set_cell_content(1,1, t("allah"));
			$ge->set_row_style(1,$style_obj->id());
			$obj->set_meta("$ge->_get_table());
	**/
	function set_row_style($row, $style)
	{
		for($i = 0; $i < $this->arr["cols"]; $i++)
		{
			$this->arr["styles"][$row][$i]["style"] = $style;
		}
	}

	/** sets the style for the given column
		@attrib api=1 params=pos

		@param col required type=int
			The col to set the style for

		@param style required type=oid
			The style object to set to the column

		@comment
			Iterates over the cells in the column and sets the style to each cell

		@errors none
		@returns none

		@examples
			$ge = new grid_editor();
			$ge->_init_table($obj->meta("grid"));
			$ge->set_cell_content(1,1, t("allah"));
			$ge->set_col_style(1,$style_obj->id());
			$obj->set_meta("$ge->_get_table());
	**/
	function set_col_style($col, $style)
	{
		for($i = 0; $i < $this->arr["rows"]; $i++)
		{
			$this->arr["styles"][$i][$col]["style"] = $style;
		}
	}

	/** sets the style for the given cell
		@attrib api=1 params=pos

		@param row required type=int
			The row the cell is in

		@param col required type=int
			The column the cell is in

		@param style required type=oid
			The style object to set to the cell

		@errors none
		@returns none

		@examples
			$ge = new grid_editor();
			$ge->_init_table($obj->meta("grid"));
			$ge->set_cell_content(1,1, t("allah"));
			$ge->set_cell_style(1,1, $style_obj->id());
			$obj->set_meta("$ge->_get_table());
	**/
	function set_cell_style($row, $col, $style)
	{
		$this->arr["styles"][$row][$col]["style"] = $style;
	}

	/** Returns the grid data for the current grid
		@attrib api=1

		@errors none
		@returns
			array, containing the data for the grid

		@examples
			example in the _init_table documentation

	**/
	function _get_table()
	{
		return $this->arr;
	}

	/** displays the grid styles editing table
		@attrib api=1 params=pos

		@param data required type=array
			The grid data, returned by the grid editor

		@param oid required type=oid
			The object id of the object the grid is attached to

		@returns The html for the grid styles editing view

		@errors	none

		@comment
			The main grid styles editing view is the view where you can assign styles for grid cells

		@examples
			A part of the get_property method from the CL_LAYOUT class:
			function get_property(&$arr)
			{
				$prop = &$arr['prop'];
				switch($prop["name"])
				{
					case "grid_styles":
						$ge = new grid_editor();
						$prop['value'] = $ge->on_styles_edit($arr['obj_inst']->meta('grid'), $arr['obj_inst']->id());
						break;
				}
				return PROP_OK;
			}

	**/
	function on_styles_edit($data, $oid)
	{
		$this->_init_table($data);
		$this->_init_show_styles();

		$this->read_template("grid_styles.tpl");
		for ($col = 0; $col < $this->arr["cols"]; $col++)
		{
			$this->vars(array(
				"col" => $col,
			));
			$this->parse("DC");
		}

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$col="";
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				if (!($spans = $this->get_spans($i, $a)))
				{
					continue;
				}

				$map = $this->arr["map"][$i][$a];
				$cell = $this->arr["styles"][$map["row"]][$map["col"]];
				$scell = $this->arr["styles"][$map["row"]][$map["col"]];

				$style_icon = "";
				$st = $this->_get_cell_style_id($i, $a, $scell);
				if ($this->can("view", $st))
				{
					$td_style = "colspan=\"".$spans["colspan"]."\" rowspan=\"".$spans["rowspan"]."\" class=\"st".$st."\"";
					active_page_data::add_site_css_style($st);
					$st_o = obj($st);
					$st_name = $st_o->name()." (".$st_o->id().")";
					$style_icon = "<img src='".aw_ini_get("baseurl")."automatweb/images/icons/class_71.gif' alt='".$st_name."' title='".$st_name."'>";
				}
				else
				{
					$td_style = "colspan=\"".$spans["colspan"]."\" rowspan=\"".$spans["rowspan"]."\"";
				}

				$this->vars(array(
					"col" => $a,
					"row" => $i,
					"td_style" => $td_style,
					"content" => $this->arr["aliases"][$map["row"]][$map["col"]],
					"style_icon" => $style_icon
				));
				$col.=$this->parse("COL");
			}
			$this->vars(array(
				"COL"	=> $col,
				"row" => $i,
			));
			$this->parse("LINE");
		}

		$st = get_instance(CL_STYLE);
		if ($this->arr["table_style"])
		{
			$tbst = "class=\"".$st->get_style_name($this->arr["table_style"])."\"";
			active_page_data::add_site_css_style($this->arr["table_style"]);
		}
		$this->vars(array(
			"selstyle" => $this->mk_my_orb("sel_style", array(), "layout"),
			"oid" => $oid,
			"tb_style" => $tbst,
		));

		$table = $this->parse();


		return $table;
	}

/*	function on_styles_edit_submit($data, $oid)
	{
		$this->_init_table($data);

		return $this->_get_table();
	}*/

	/** Sets the number of columns in the grid
		@attrib api=1 params=pos

		@param num required type=int
			the number of columns that exist in the grid

		@comment
			Expands/contracts the grid as needed, new cells are empty

		@returns none

		@examples
			$ge = new grid_editor();
			$ge->_init_table($obj->meta("grid"));
			$gr->set_num_cols(3);
			$ge->set_cell_content(1,1, t("allah"));
			$ge->set_cell_content(1,2, t("allah2"));
			$obj->set_meta("$ge->_get_table());
	**/
	function set_num_cols($num)
	{
		if ($num > $this->arr["cols"])
		{
			for ($i = $this->add["cols"]; $i < $num; $i++)
			{
				for ($row = 0; $row < $this->arr["rows"]; $row++)
				{
					$this->arr["map"][$row][$i] = array("row" => $row, "col" => $i);
				}
			}
			$this->arr["cols"] = $num;
		}
		else
		if ($num < $this->arr["cols"])
		{
			$this->arr["cols"] = $num;
		}
	}

	/** Sets the number of rows in the grid
		@attrib api=1 params=pos

		@param num required type=int
			the number of rows that exist in the grid

		@comment
			Expands/contracts the grid as needed, new cells are empty

		@returns none

		@examples
			$ge = new grid_editor();
			$ge->_init_table($obj->meta("grid"));
			$gr->set_num_rows(3);
			$ge->set_cell_content(1,1, t("allah"));
			$ge->set_cell_content(2,1, t("allah2"));
			$obj->set_meta("$ge->_get_table());
	**/
	function set_num_rows($num)
	{
		if ($num > $this->arr["rows"])
		{
			for ($i = $this->add["rows"]; $i < $num; $i++)
			{
				for ($col = 0; $col < $this->arr["cols"]; $col++)
				{
					$this->arr["map"][$i][$col] = array("row" => $i, "col" => $col);
				}
			}
			$this->arr["rows"] = $num;
		}
		else
		if ($num < $this->arr["rows"])
		{
			$this->arr["rows"] = $num;
		}
	}

	/** returns the current number of rows in the grid
		@attrib api=1

		@returns
			The number of rows in the current grid. this will always be greater than 0

	**/
	function get_num_rows()
	{
		return $this->arr["rows"];
	}

	/** returns the current number of columns in the grid
		@attrib api=1

		@returns
			The number of columns in the current grid. this will always be greater than 0

	**/
	function get_num_cols()
	{
		return $this->arr["cols"];
	}

	function _get_cell_style_id($row, $col, $scell)
	{
		$st = 0;
		$est = 0;
		if ($this->arr["table_style"])
		{
			if (($row & 1) > 0)
			{
				$est = $this->style_inst->get_even_style($this->arr["table_style"]);
			}
			else
			{
				$est = $this->style_inst->get_odd_style($this->arr["table_style"]);
			}
		}

		if ($scell["style"] && $st == 0)
		{
			$st = $scell["style"];
		}
		else
		{
			// tshekime et kui on esimene rida/tulp ja stiili pole m22ratud, siis
			// v6tame tabeli stiilist, kui see on m22ratud default stiili esimese rea/tulba jaox
			if ($this->arr["table_style"] && $row < $this->num_frows)
			{
				$st = $this->frow_style;
			}
			else
			if ($this->arr["table_style"] && $col < $this->num_fcols)
			{
				$st = $this->fcol_style;
			}

			if (!$st && $this->arr["table_style"] && $this->num_lrows && $row >= ($this->arr["rows"] - $this->num_lrows))
			{
				$st = $this->lrow_style;
			}

			if (!$st && $this->arr["table_style"] && $this->num_lcols && $col >= ($this->arr["cols"] - $this->num_lcols))
			{
				$st = $this->lcol_style;
			}

			// kui tabeli stiilis pold m22ratud stiili v6i ei old esimene rida/tulp,
			// siis v6tame default celli stiili, kui see on
			if ($st == 0 && $this->arr["default_style"])
			{
				$st = $this->arr["default_style"];
			}
			// damn this was horrible

			if (!$st)
			{
				$st = $est;
			}
  		}
		return $st;
	}

	function _init_show_styles()
	{
		$this->style_inst = get_instance(CL_STYLE);
		$this->frow_style = 0;
		$this->fcol_style = 0;
		$this->num_fcols = 0;
		$this->num_frows = 0;

		$this->lrow_style = 0;
		$this->lcol_style = 0;
		$this->num_lcols = 0;
		$this->num_lrows = 0;

		if ($this->arr["table_style"])
		{
			$this->frow_style = $this->style_inst->get_frow_style($this->arr["table_style"]);
			$this->fcol_style = $this->style_inst->get_fcol_style($this->arr["table_style"]);
			$this->num_frows = $this->style_inst->get_num_frows($this->arr["table_style"]);
			$this->num_fcols = $this->style_inst->get_num_fcols($this->arr["table_style"]);

			$this->lrow_style = $this->style_inst->get_lrow_style($this->arr["table_style"]);
			$this->lcol_style = $this->style_inst->get_lcol_style($this->arr["table_style"]);
			$this->num_lrows = $this->style_inst->get_num_lrows($this->arr["table_style"]);
			$this->num_lcols = $this->style_inst->get_num_lcols($this->arr["table_style"]);
		}
	}

	function _get_edit_toolbar()
	{
		$tb = get_instance("vcl/toolbar");
		$tb->add_button(array(
			'name' => 'merge_down',
			'tooltip' => t("Merge alla"),
			'url' => 'javascript:exec_cmd(\'merge_down\')',
			'img' => 'down_r_arr.png'
		));
		$tb->add_button(array(
			'name' => 'merge_up',
			'tooltip' => t("Merge &uuml;les"),
			'url' => 'javascript:exec_cmd(\'merge_up\')',
			'img' => 'up_r_arr.png'
		));
		$tb->add_button(array(
			'name' => 'merge_left',
			'tooltip' => t("Merge vasakule"),
			'url' => 'javascript:exec_cmd(\'merge_left\')',
			'img' => 'left_r_arr.png'
		));
		$tb->add_button(array(
			'name' => 'merge_right',
			'tooltip' => t("Merge paremale"),
			'url' => 'javascript:exec_cmd(\'merge_right\')',
			'img' => 'right_r_arr.png'
		));

		$tb->add_button(array(
			'name' => 'split_down',
			'tooltip' => t("Split alla"),
			'url' => 'javascript:exec_cmd(\'split_down\')',
			'img' => 'merge_down.png'
		));
		$tb->add_button(array(
			'name' => 'split_up',
			'tooltip' => t("Split &uuml;les"),
			'url' => 'javascript:exec_cmd(\'split_up\')',
			'img' => 'merge_up.png'
		));
		$tb->add_button(array(
			'name' => 'split_left',
			'tooltip' => t("Split vasakule"),
			'url' => 'javascript:exec_cmd(\'split_left\')',
			'img' => 'merge_left.png'
		));
		$tb->add_button(array(
			'name' => 'split_right',
			'tooltip' => t("Split paremale"),
			'url' => 'javascript:exec_cmd(\'split_right\')',
			'img' => 'merge_right.png'
		));
		$tb->add_button(array(
			'name' => 'delete_rc',
			'tooltip' => t("Kustuta read/tulbad"),
			'url' => 'javascript:exec_cmd(\'delete_rc\')',
			'img' => 'delete.gif'
		));

		$this->vars(array(
			"toolbar" => $tb->get_toolbar()
		));
	}

	function _merge_down($arr)
	{
		// decode the action ourselves
		list($rows,$cols,$cells, $cnt) = $this->_do_dec_cmd($arr["fulldata"]);

		foreach($rows as $row)
		{
			// merge all cells on this row down one.
			for($_col = 0; $_col < $this->arr["cols"]; $_col++)
			{
				$map = $this->arr["map"][$row][$_col];
				$this->_exp_down(array(
					"cnt" => $cnt,
					"row" => $map["row"],
					"col" => $map["col"]
				));
			}
		}

		foreach($cols as $col)
		{
			// merge all cells in this column to one.
			// find the real cell identifier for the topmost cell
			// on this column and merge that num_rows cells down
			$map = $this->arr["map"][0][$col];
			$this->_exp_down(array(
				"cnt" => $this->arr["rows"],
				"row" => $map["row"],
				"col" => $map["col"]
			));
		}

		foreach($cells as $cell)
		{
			// for each cell, merge it down as much as requested.
			$map = $this->arr["map"][$cell["row"]][$cell["col"]];
			$this->_exp_down(array(
				"cnt" => $cnt,
				"row" => $map["row"],
				"col" => $map["col"]
			));
		}
	}

	function _merge_up($arr)
	{
		// decode the action ourselves
		list($rows,$cols,$cells, $cnt) = $this->_do_dec_cmd($arr["fulldata"]);

		foreach($rows as $row)
		{
			// merge all cells on this row up one.
			for($_col = 0; $_col < $this->arr["cols"]; $_col++)
			{
				$map = $this->arr["map"][$row][$_col];
				$this->_exp_up(array(
					"cnt" => $cnt,
					"row" => $map["row"],
					"col" => $map["col"]
				));
			}
		}

		foreach($cols as $col)
		{
			// merge all cells in this column to one.
			// find the real cell identifier for the bottom-most cell
			// on this column and merge that num_rows cells down
			$map = $this->arr["map"][$this->arr["rows"]-1][$col];
			$this->_exp_up(array(
				"cnt" => $this->arr["rows"],
				"row" => $map["row"],
				"col" => $map["col"]
			));
		}

		foreach($cells as $cell)
		{
			// for each cell, merge it up as much as requested.
			$map = $this->arr["map"][$cell["row"]][$cell["col"]];
			$this->_exp_up(array(
				"cnt" => $cnt,
				"row" => $map["row"],
				"col" => $map["col"]
			));
		}
	}

	function _merge_left($arr)
	{
		// decode the action ourselves
		list($rows,$cols,$cells, $cnt) = $this->_do_dec_cmd($arr["fulldata"]);

		foreach($rows as $row)
		{
			// merge all cells on this row to one
			// find the rightmost cell
			$map = $this->arr["map"][$row][$this->arr["cols"]-1];
			$this->_exp_left(array(
				"cnt" => $this->arr["cols"],
				"row" => $map["row"],
				"col" => $map["col"]
			));
		}

		foreach($cols as $col)
		{
			// merge all cells in this column left $cnt times
			for ($_row = 0; $_row < $this->arr["rows"]; $_row++)
			{
				$map = $this->arr["map"][$_row][$col];
				$this->_exp_left(array(
					"cnt" => $cnt,
					"row" => $map["row"],
					"col" => $map["col"]
				));
			};
		}

		foreach($cells as $cell)
		{
			// for each cell, merge it left as much as requested.
			$map = $this->arr["map"][$cell["row"]][$cell["col"]];
			$this->_exp_left(array(
				"cnt" => $cnt,
				"row" => $map["row"],
				"col" => $map["col"]
			));
		}
	}

	function _merge_right($arr)
	{
		// decode the action ourselves
		list($rows,$cols,$cells, $cnt) = $this->_do_dec_cmd($arr["fulldata"]);

		foreach($rows as $row)
		{
			// merge all cells on this row to one
			// find the left cell
			$map = $this->arr["map"][$row][0];
			$this->_exp_right(array(
				"cnt" => $this->arr["cols"],
				"row" => $map["row"],
				"col" => $map["col"]
			));
		}

		foreach($cols as $col)
		{
			// merge all cells in this column right $cnt times
			for ($_row = 0; $_row < $this->arr["rows"]; $_row++)
			{
				$map = $this->arr["map"][$_row][$col];
				$this->_exp_right(array(
					"cnt" => $cnt,
					"row" => $map["row"],
					"col" => $map["col"]
				));
			};
		}

		foreach($cells as $cell)
		{
			// for each cell, merge it right as much as requested.
			$map = $this->arr["map"][$cell["row"]][$cell["col"]];
			$this->_exp_right(array(
				"cnt" => $cnt,
				"row" => $map["row"],
				"col" => $map["col"]
			));
		}
	}

	function _split_down($arr)
	{
		// decode the action ourselves
		list($rows,$cols,$cells, $cnt) = $this->_do_dec_cmd($arr["fulldata"]);

		foreach($cells as $cell)
		{
			// for each cell, check if it's area is > 1 in the vertical directtion.
			// if it is, then we just split it, no problem there
			$map = $this->arr["map"][$cell["row"]][$cell["col"]];
			$spans = $this->get_spans($map["row"], $map["col"]);
			if (!$spans)
			{
				continue;
			}

			if ($spans["rowspan"] > 1)
			{
				$this->_split_hor(array(
					"row" => $map["row"],
					"col" => $map["col"]
				));
			}
			else
			{
				// if it is jist uan cell, then we must add a row above the cell
				$this->_add_row(array(
					"after" => $map["row"]-1,
					"num" => 1
				));
				// then merge all other cells from the old row up one
				for ($i = 0; $i < $this->arr["cols"]; $i++)
				{
					if ($i != $cell["col"])
					{
						$this->arr["map"][$cell["row"]][$i] = $this->arr["map"][$cell["row"]+1][$i];
					}
				}
			}
		}
	}

	function _split_up($arr)
	{
		// decode the action ourselves
		list($rows,$cols,$cells, $cnt) = $this->_do_dec_cmd($arr["fulldata"]);

		foreach($cells as $cell)
		{
			// for each cell, check if it's area is > 1 in the vertical directtion.
			// if it is, then we just split it, no problem there
			$map = $this->arr["map"][$cell["row"]][$cell["col"]];
			$spans = $this->get_spans($map["row"], $map["col"]);
			if (!$spans)
			{
				continue;
			}

			if ($spans["rowspan"] > 1)
			{
				$this->_split_hor(array(
					"row" => $map["row"],
					"col" => $map["col"]
				));
			}
			else
			{
				// if it is jist uan cell, then we must add a row below the cell
				$this->_add_row(array(
					"after" => $map["row"],
					"num" => 1
				));
				// then merge all other cells from the old row down one
				for ($i = 0; $i < $this->arr["cols"]; $i++)
				{
					if ($i != $cell["col"])
					{
						$this->arr["map"][$cell["row"]+1][$i] = $this->arr["map"][$cell["row"]][$i];
					}
				}
			}
		}
	}

	function _split_left($arr)
	{
		// decode the action ourselves
		list($rows,$cols,$cells, $cnt) = $this->_do_dec_cmd($arr["fulldata"]);

		foreach($cells as $cell)
		{
			// for each cell, check if it's area is > 1 in the horiz directtion.
			// if it is, then we just split it, no problem there
			$map = $this->arr["map"][$cell["row"]][$cell["col"]];
			$spans = $this->get_spans($map["row"], $map["col"]);
			if (!$spans)
			{
				continue;
			}

			if ($spans["colspan"] > 1)
			{
				$this->_split_ver(array(
					"row" => $map["row"],
					"col" => $map["col"]
				));
			}
			else
			{
				// if it is jist uan cell, then we must add a column to the right of the cell
				$this->_add_col(array(
					"after" => $map["col"],
					"num" => 1
				));
				// then merge all other cells from the old col right one
				for ($i = 0; $i < $this->arr["rows"]; $i++)
				{
					if ($i != $cell["row"])
					{
						$this->arr["map"][$i][$cell["col"]+1] = $this->arr["map"][$i][$cell["col"]];
					}
				}
			}
		}
	}

	function _split_right($arr)
	{
		// decode the action ourselves
		list($rows,$cols,$cells, $cnt) = $this->_do_dec_cmd($arr["fulldata"]);

		foreach($cells as $cell)
		{
			// for each cell, check if it's area is > 1 in the horiz directtion.
			// if it is, then we just split it, no problem there
			$map = $this->arr["map"][$cell["row"]][$cell["col"]];
			$spans = $this->get_spans($map["row"], $map["col"]);
			if (!$spans)
			{
				continue;
			}

			if ($spans["colspan"] > 1)
			{
				$this->_split_ver(array(
					"row" => $map["row"],
					"col" => $map["col"]
				));
			}
			else
			{
				// if it is jist uan cell, then we must add a column to the left of the cell
				$this->_add_col(array(
					"after" => $map["col"],
					"num" => 1
				));
				// then merge all other cells from the old col left one
				for ($i = 0; $i < $this->arr["rows"]; $i++)
				{
					if ($i != $cell["row"])
					{
						$this->arr["map"][$i][$cell["col"]] = $this->arr["map"][$i][$cell["col"]+1];
					}
				}
			}
		}
	}

	function _do_dec_cmd($cmd)
	{
		// decodes cmds like these: action=merge_down;cells=sel_row=0;col=3;cols=;rows=
		// returns array(array(rows), array(cols), array(cells), $cnt)
		// remove action bit
		$cmd = preg_replace("/action=[^;]*/","",$cmd);
		list($cmd, $cnt) = explode(";cnt=", $cmd);
		list($cmd, $rowstr) = explode(";rows=", $cmd);
		list($cmd, $colstr) = explode(";cols=", $cmd);
		list(, $cellstr) = explode(";cells=", $cmd);
		$t = str_replace("dr_", ";", substr($rowstr,3));
		if ($t == "")
		{
			$rows = array();
		}
		else
		{
			$rows = explode(";", $t);
		}

		$t = str_replace("dc_", ";", substr($colstr,3));
		if ($t == "")
		{
			$cols = array();
		}
		else
		{
			$cols = explode(";", $t);
		}
		$_cells = explode("|", str_replace("sel_", "|", substr($cellstr,4)));
		$cells = array();
		foreach($_cells as $_cstr)
		{
			preg_match("/row=(\d*);col=(\d*)/", $_cstr, $mt);
			$cells[] = array("row" => $mt[1], "col" => $mt[2]);
		}

		return array($rows, $cols, $cells, $cnt);
	}

	function debug_map_print()
	{
		echo "<table border=1>";
		for ($r=0; $r < $this->arr["rows"]; $r++)
		{
			echo "<tr>";
			for ($c=0; $c < $this->arr["cols"]; $c++)
				echo "<td>(", $this->arr["map"][$r][$c]["row"], ",",$this->arr["map"][$r][$c]["col"],")</td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	/** imports the grid from a csv file
		@attrib api=1 params=name

		@param file required type=string
			file that contains csv data

		@param sep required type=string
			 column separator in file

		@param remove_empty optional type=bool
			 if true, remove empty lines from end

		@errors none

		@returns the grid data imported from the csv file

		@examples
			$ge = new grid_editor();
			$grid = $ge->do_import(array(
				"file" => "/tmp/file.csv",
				"sep" => ";",
				"remove_empty" => true
			));
	**/
	function do_import($arr)
	{
		extract($arr);

		// siin ei saa file() funktsiooni kasutada, kuna kui reavahetus on jutum2rkide vahel celli sees, siis ei t2henda see rea l6ppu.
		// damnit.
		$file = $this->mk_file($file,$sep);

		$num_rows = count($file);

		reset($file);
		list(,$line) = each($file);
		$num_cols = count(explode($sep,$line));


		$this->arr["cols"] = 1;
		$this->arr["map"] = array();
		$this->arr["map"][0][0] = array("row" => 0, "col" => 0);
		$this->arr["aliases"] = array();
		$this->arr["rows"] = 1;

		// nini. kuna importimisel clearitaxe kogu tabla 2ra niikuinii, siis v6ime lihtsalt uue mapi teha ju
		$this->arr["cols"] = $num_cols;
		$this->arr["rows"] = $num_rows;
		for ($row = 0; $row < $num_rows; $row++)
		{
			for ($col = 0; $col < $num_cols; $col++)
			{
				$this->arr["map"][$row][$col] = array("row" => $row, "col" => $col);
			}
		}

		reset($file);
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			list(,$line) = each($file);
			$line = chop($line).$sep;

			// ok, so far so good.
			// a siin hakkab keemia pihta
			// nimelt, kui celli sees on ; m2rk, siis pannaxe sisule " ymber. ja celli sees olevad " m2rgid dubleeritaxe
			$rowarr = array();

			$pos = 0;
			$len = strlen($line);
			while ($pos < $len)
			{
				$quoted = false;
				if ($line[$pos] == "\"")
				{
					$quoted = true;
					$pos++;
				}

				$cell_content = "";
				$in_cell = true;
				while ($in_cell && $pos < $len)
				{
					if ($quoted && ($line[$pos] == "\"" && $line[$pos+1] != "\""))
					{
						// leidsime celli l6pu
						$in_cell = false;
						$pos+=2;
					}
					else
					if ($line[$pos] == "\"" && $line[$pos+1] == "\"")
					{
						// leidsime dubleeritud jutum2rgi
						$cell_content.="\"";
						$pos+=2;
					}
					else
					if (!$quoted && $line[$pos] == $sep)
					{
						// leidsime celli mis pole jutum2rkide vahel, l6pu
						$in_cell= false;
						$pos++;
					}
					else
					{
						$cell_content.=$line[$pos];
						$pos++;
					}
				}
				$rowarr[] = $cell_content;
			}
			reset($rowarr);
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				list(,$ct) = each($rowarr);
				$ct = str_replace("'", "\"",$ct);
				$this->arr["aliases"][$row][$col] = $ct;
			}
		}

		if ($arr["trim"])
		{
			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				$filled = false;
				for ($col = 0; $col < $this->arr["cols"]; $col++)
				{
					if ($this->arr["aliases"][$row][$col] != "")
					{
						$filled = true;
					}
				}
				if ($filled)
				{
					$last = $row;
				}
			}
			$this->arr["rows"] = $last+1;

			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$filled = false;
				for ($row = 0; $row < $this->arr["rows"]; $row++)
				{
					if ($this->arr["aliases"][$row][$col] != "")
					{
						$filled = true;
					}
				}
				if ($filled)
				{
					$last = $col;
				}
			}
			$this->arr["cols"] = $last+1;
		}
		return $this->arr;
	}

	function mk_file($file,$separator)
	{
		$f = fopen($file,"r");
		$filestr = fread($f,filesize($file));
		fclose($f);
		$len = strlen($filestr);
		$linearr = array();
		$in_cell = false;
		for ($pos=0; $pos < $len; $pos++)
		{
			if ($filestr[$pos] == "\"")
			{
				if ($in_cell == false)
				{
					// pole celli sees ja jutum2rk. j2relikult algab quoted cell
					$in_cell = true;
					$line.=$filestr[$pos];
				}
				else
				if ($in_cell == true && ($filestr[$pos+1] == $separator || $filestr[$pos+1] == "\n" || $filestr[$pos+1] == "\r"))
				{
					// celli sees ja jutum2rk ja j2rgmine on kas semikas v6i reavahetus, j2relikult cell l6peb
					$in_cell = false;
					$line.=$filestr[$pos];
				}
				else
				{
					// dubleeritud jutum2rk
					$line.=$filestr[$pos];
				}
			}
			else
			if ($filestr[$pos] == $separator && $in_cell == false)
			{
				// semikas t2histab celli l6ppu aint siis, kui ta pole jutum2rkide vahel
				$in_cell = false;
				$line.=$filestr[$pos];
			}
			else
			if (($filestr[$pos] == "\n" || $filestr[$pos] == "\r") && $in_cell == false)
			{
				// kui on reavahetus ja me pole quotetud celli sees, siis algab j2rgmine rida

				// clearime j2rgneva l2bu ka 2ra
				if ($filestr[$pos+1] == "\n" || $filestr[$pos+1] == "\r")
				{
					$pos++;
				}
				$linearr[] = $line;
				$line = "";
			}
			else
			{
				$line.=$filestr[$pos];
			}
		}
		return $linearr;
	}

	/** sets the column width for the given column
		@attrib api=1 params=pos

		@param col required type=int
			The column to set the width for

		@param width required type=int
			The width of the column in characters

		@examples
			$ge = new grid_editor();
			$ge->_init_table($arr["obj_inst"]->meta("grid"));
			for($i = 0; $i < $ge->get_num_cols(); $i++)
			{
				$ge->set_col_width($i, $arr["request"]["colw"][$i]);
			}
			$arr['obj_inst']->set_meta('grid',$ge->_get_table());
	**/
	function set_col_width($col, $width)
	{
		$this->arr["col_widths"][$col] = $width;
	}

	/** sets the column height for the given column
		@attrib api=1 params=pos

		@param col required type=int
			The column to set the width for

		@param height required type=int
			The height of the column in characters

		@examples
			$ge = new grid_editor();
			$ge->_init_table($arr["obj_inst"]->meta("grid"));
			for($i = 0; $i < $ge->get_num_cols(); $i++)
			{
				$ge->set_col_height($i, $arr["request"]["colh"][$i]);
			}
			$arr['obj_inst']->set_meta('grid',$ge->_get_table());
	**/
	function set_col_height($col, $height)
	{
		$this->arr["col_heights"][$col] = $height;
	}

	/** returns the current column width for the given golumn
		@attrib api=1 params=pos

		@param col type=int
			The column to return the width for

		@returns
			The width of the given column

	**/
	function get_col_width($col)
	{
		return isset($this->arr["col_widths"][$col]) ? $this->arr["col_widths"][$col] : 0;
	}

	/** returns the current column height for the given golumn
		@attrib api=1 params=pos

		@param col required type=int
			The column to return the height for

		@returns
			The height of the given column

	**/
	function get_col_height($col)
	{
		return isset($this->arr["col_heights"][$col]) ? $this->arr["col_heights"][$col] : 0;
	}
}
