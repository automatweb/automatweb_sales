<?php

namespace automatweb;

/*
	@classinfo syslog_type=ST_PROCESS maintainer=kristo
	@classinfo relationmgr=yes

	@default table=objects
	@default group=general

	@property ptype type=select field=meta method=serialize
	@caption Protsessi tüüp

	@property description type=textarea field=meta method=serialize
	@caption Kirjeldus

	@property goal type=textarea field=meta method=serialize
	@caption Eesmärk


	@property root_action type=relpicker reltype=RELTYPE_ACTION field=meta method=serialize group=general
	@caption Juurtegevus

	@property end_action type=relpicker reltype=RELTYPE_ACTION field=meta method=serialize group=general
	@caption L&otilde;pptegevus

	@property transition_folder type=relpicker reltype=RELTYPE_FOLDER field=meta method=serialize group=general
	@caption J&auml;rgnevuste kataloog

	@property id type=hidden table=objects field=oid group=actions

	@property action_list type=text group=actions store=no
	@caption Tegevused

	@groupinfo actions caption=Tegevused

	@reltype INSTRUCTION clid=CL_IMAGE,CL_FILE value=1
	@caption instruktsioon

	@reltype TRANSITION clid=CL_WORKFLOW_TRANSITION value=2
	@caption j&auml;rgnevus

	@reltype FOLDER clid=CL_MENU value=3
	@caption j&auml;rgnevuste kataloog

	@reltype ACTION clid=CL_ACTION,CL_PROCESS value=10
	@caption tegevus
*/

class process extends class_base
{
	const AW_CLID = 175;

	function process()
	{
		$this->init(array(
			'tpldir' => 'workflow',
			'clid' => CL_PROCESS
		));
	}

	function get_property($args)
	{
		$data = &$args["prop"];
		$name = $data["name"];
		$retval = PROP_OK;
		if ($name == "comment")
		{
			return PROP_IGNORE;
		};
		
		switch($name)
		{
			case "action_list":
				$retval = $this->action_list(&$data,&$args);
				break;

			case "ptype":
				$data["options"] = array(
					"0" => t("--vali--"),
					"1" => t("põhiprotsess"),
					"2" => t("tugiprotsess"),
				);
				break;
		}
		return $retval;
	}

	function set_property($args)
	{
		$retval = PROP_OK;
		$data = &$args["prop"];
		switch($data["name"])
		{
			case "action_list":
				$retval = $this->set_actions(&$args);
				break;
		};
		return $retval;
	}

	function action_list(&$data,&$args)
	{
		if ($this->_get_actiondata($data, $args) == PROP_ERROR)
		{
			return PROP_ERROR;
		}

		$data["value"] = $this->_do_action_grid($args["obj_inst"]).$this->_do_transition_table($args["obj_inst"]);
		$data["no_caption"] = 1;
		return PROP_OK;
	}

	function _do_action_grid($process)
	{
		$this->read_template("action_grid.tpl");

		$numa = count($this->actiondata);

		$per_row = (int)floor(sqrt($numa-2)+0.99);

		reset($this->actiondata);
		
		$r = "";
		$rows = count($this->grid);
		for($row = 0; $row < $rows; $row++)
		{
			$c = "";
			$cols = $per_row;
			for($col = 0; $col < $cols; $col++)
			{
				$_t_id = $this->grid[$row][$col]["id"];
				$_t_name = $this->grid[$row][$col]["n"];
				if ($_t_id == $this->root_action_id || $_t_id == $this->end_action_id)
				{
					$_t_name = "<b>".$_t_name."</b>";
				}


				if (!$_t_id)
				{
					$c .= $this->parse("COL_EMPTY");
				}
				else
				{
					$this->vars(array(
						"name" => $_t_name,
						"colspan" => 1,
						"align" => "left"
					));
			
					$this->_parse_transitions($process, $_t_id, $per_row, $row, $col);

					$c .= $this->parse("COL");
				}
			}

			$this->vars(array(
				"COL" => $c,
				"COL_EMPTY" => ""
			));

			$r .= $this->parse("ROW");
		}

		$this->vars(array(
			"ROW" => $r
		));

		return $this->parse();
	}

	function _do_transition_table($process)
	{
		load_vcl("table");
		$t = new aw_table(array(
			"xml_def" => "workflow/transition_table",
			"layout" => "generic",
		));
		
		$transition_list = new object_list(array(
			"class_id" => CL_WORKFLOW_TRANSITION,
			"process_id" => $process->id()
		));

		for ($transition = $transition_list->begin(); !$transition_list->end(); $transition = $transition_list->next())
		{
			$t->define_data(array(
				"name" => $transition->name(),
				"from" => $this->actiondata[$transition->prop("from_act")],
				"to" => $this->actiondata[$transition->prop("to_act")],
				"edit" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $transition->id()), "workflow_transition"),
					"caption" => t("Muuda")
				)),
				"del" => html::checkbox(array(
					"name" => "del[]",
					"value" => $transition->id()
				))
			));
		}

		$t->define_data(array(
			"name" => html::textbox(array(
				"name" => "add_action[name]",
				"value" => "",
			)),
			"from" => html::select(array(
				"name" => "add_action[from_act]",
				"options" => $this->actiondata,
			)),
			"to" => html::select(array(
				"name" => "add_action[to_act]",
				"options" => $this->actiondata,
			)),
			"edit" => t("lisa j&auml;rgnevus"),
			"del" => ""
		));

		return $t->draw();
	}

	function set_actions($args = array())
	{
		extract($args["request"]);

		$process = $args["obj_inst"];

		if (is_array($del))
		{
			$ol = new object_list(array("oid" => $del, "class_id" => CL_WORKFLOW_TRANSITION));
			$ol->delete();
		}

		// check if the add action is filled
		if (isset($add_action) && $add_action["name"] != "" && $add_action["from_act"] && $add_action["to_act"])
		{
			if ($add_action["from_act"] == $add_action["to_act"])
			{
				$args["prop"]["error"] = t("J&auml;rgnevuse otsad ei tohi samad olla!");
				return PROP_ERROR;
			}

			// check if it already exists
			$ol = new object_list(array(
				"class_id" => CL_WORKFLOW_TRANSITION,
				"process_id" => $process->id(),
				"from_act" => $add_action["from_act"],
				"to_act" => $add_action["to_act"]
			));
			if ($ol->count() > 0)
			{
				$args["prop"]["error"] = t("Selline j&auml;rgnevus on juba olemas!");
				return PROP_ERROR;
			}
			else
			{
				$o = obj();
				$o->set_class_id(CL_WORKFLOW_TRANSITION);
				$o->set_parent($process->prop("transition_folder"));
				$o->set_name($add_action["name"]);
				$o->set_prop("process_id", $process->id());
				$o->set_prop("from_act", $add_action["from_act"]);
				$o->set_prop("to_act", $add_action["to_act"]);
				$o->set_status(STAT_ACTIVE);
				$o->save();
				$o->connect(array(
					"to" => $process->id(),
					"reltype" => "RELTYPE_TRANSITION",
				));
				$process->connect(array(
					"to" => $o->id(),
					"reltype" => "RELTYPE_PROCESS" // from transition
				));
			}
		}
		return PROP_OK;
	}

	function _get_actiondata(&$data, &$args)
	{
		// phase 1, kuvame objekti juurfunktsiooni.
		// ja sinna juurde lingi "defineeri järgmine tegevus"
		if ($args["obj_inst"]->meta("root_action"))
		{
			$this->root_action_id = $args["obj_inst"]->meta("root_action");
		}
		else
		{
			$data["error"] = t("Juurtegevus on valimata");
			return PROP_ERROR;
		};

		if ($args["obj_inst"]->meta("end_action"))
		{
			$this->end_action_id = $args["obj_inst"]->meta("end_action");
		}
		else
		{
			$data["error"] = t("L&otilde;pptegevus on valimata");
			return PROP_ERROR;
		};

		if (!$args["obj_inst"]->prop("transition_folder"))
		{
			$data["error"] = t("J&auml;rgnevuste kataloog on valimata!");
			return PROP_ERROR;
		}


		$conns = $args["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_ACTION",
			"sort_by" => "to.jrk"
		));

		if (sizeof($conns) == 0)
		{
			$data["error"] = t("Objektil puuduvad 'tegevus' tüüpi seosed");
			return PROP_ERROR;
		};

		$tt = array();
		foreach($conns as $conn)
		{
			$tt[] = $conn->to();
		}

		$this->grid = $this->_optimize_action_layout($tt, $args["obj_inst"]);

		// now, make the actiondata from the grid
		$this->actiondata = array();
		$this->actiondata_ord = array();
		$rows = count($this->grid);
		for($ridx = 0; $ridx < $rows; $ridx++)
		{
			$cols = count($this->grid[$ridx]);
			for($cidx = 0; $cidx < $cols; $cidx++)
			{
				$cd = $this->grid[$ridx][$cidx];
				$this->actiondata[$cd["id"]] = $cd["n"];
				$this->actiondata_ord[$cd["id"]] = ($ridx * $per_row) + $cidx;
			}
		}

		$conns = $args["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_TRANSITION",
		));

		$this->transitions = array();
		foreach($conns as $conn)
		{
			$this->transitions[$conn->prop("to")] = $conn->prop("to.name");
		}
	}

	function _parse_transitions($process, $_t_id, $per_row, $row, $col)
	{
		$ars = array(
			"UP_ARROW_LEFT" => array(),
			"UP_ARROW_RIGHT" => array(),
			"UP_ARROW" => array(),
			"LEFT_ARROW" => array(),
			"RIGHT_ARROW" => array(),
			"DOWN_ARROW_RIGHT" => array(),
			"DOWN_ARROW" => array(),
			"DOWN_ARROW_LEFT" => array(),
		);

		// go over all transitions that are from this action
		// and for each figure out the direction it is on the diagram
		// and make the arrows in the correct direction.
		$ol = new object_list(array(
			"class_id" => CL_WORKFLOW_TRANSITION,
			"process_id" => $process->id(),
			"from_act" => $_t_id
		));
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$to = $o->prop("to_act");
			// now, figure out the direction that the to action has from the current
			list($to_row, $to_col) = $this->_o_get_pos($this->grid, $to);

			if ($to_row == $row && $to_col < $col)
			{
				// left
				$ars["LEFT_ARROW"][] = $o->name();
			}
			else
			if ($to_col == $col && $to_row < $row)
			{
				// up
				$ars["UP_ARROW"][] = $o->name();
			}
			else
			if ($to_col < $col && $to_row < $row)
			{
				// up left
				$ars["UP_ARROW_LEFT"][] = $o->name();
			}
			else
			if ($to_col > $col && $to_row < $row)
			{
				// up right
				$ars["UP_ARROW_RIGHT"][] = $o->name();
			}
			else
			// down or right
			if ($to_row == $row && $to_col > $col)
			{
				// right
				$ars["RIGHT_ARROW"][] = $o->name();
			}
			else
			// down
			if ($to_col == $col && $to_row > $row)
			{
				// up
				$ars["DOWN_ARROW"][] = $o->name();
			}
			else
			if ($to_col < $col && $to_row > $row)
			{
				// up left
				$ars["DOWN_ARROW_LEFT"][] = $o->name();
			}
			else
			if ($to_col > $col && $to_row > $row)
			{
				// up right
				$ars["DOWN_ARROW_RIGHT"][] = $o->name();
			}
		}

		// now, parse all the arrows as the array says
		foreach($ars as $sub => $names)
		{
			$arstr = "";
			if (count($names) > 0)
			{
				$this->vars(array(
					"alt" => join(",", $names)
				));
				$arstr = $this->parse($sub);
			}
			$this->vars(array(
				$sub => $arstr
			));
		}
	}

	function _optimize_action_layout($acts, $process)
	{
		// ok, try to do the layout of actions here
		// idea is:
		// 1 - put the default layout in an array
		// 2 - increase cnt, go over array
		// 3 - for each action that has a transition check the distance
		// 4 - if distance > 1
		// 5 - swap the related action and the that is closest on the same direction
		// 6 - if cnt < 10, goto 2
		

		$per_row = (int)floor(sqrt(count($acts)-2)+0.99);
		$grid = array();

		// 1
		$this->_o_init_grid($grid, $per_row, $acts);
		//echo "default layout = <br>";
		//$this->_o_dump_grid($grid);

		$transitions = $this->_o_get_trans($process);

		// 2
		for($i = 0; $i < 10; $i++)
		{
			$swaps = $this->_o_opt_grid($grid, $transitions);
			//echo "after transform no $i , grid = <Br>";
			//$this->_o_dump_grid($grid);
			if (!$swaps)
			{
				break;
			}
		}

		return $grid;
	}

	function _o_opt_grid(&$grid, $transitions)
	{
		$did_swap = false;
		foreach($transitions as $t)
		{
			// 3
			list($from_row, $from_col) = $this->_o_get_pos($grid, $t->prop("from_act"));
			list($to_row, $to_col) = $this->_o_get_pos($grid, $t->prop("to_act"));
	
			$distance = $this->_o_get_distance($from_row, $from_col, $to_row, $to_col);
			//echo "distance from [$from_row, $from_col] to [$to_row, $to_col] = $distance <br>";

			// 4
			if ($distance > 1)
			{
				// 5

				// find the one to swap with
				if ($to_row == $from_row)
				{
					$swap_row = $to_row;
					if ($to_col > $from_col)
					{
						$swap_col = $from_col+1;
					}
					else
					if ($to_col < $from_col)
					{
						$swap_col = $from_col-1;
					}
				}
				else
				if ($to_col == $from_col)
				{
					$swap_col = $to_col;
					if ($to_row > $from_row)
					{
						$swap_row = $from_row+1;
					}
					else
					if ($to_row < $from_row)
					{
						$swap_row = $from_row-1;
					}
				}
				
				$t = $grid[$swap_row][$swap_col];
				$grid[$swap_row][$swap_col] = $grid[$to_row][$to_col];
				$grid[$to_row][$to_col] = $t;
				$did_swap = true;
			}
		}

		return $did_swap;
	}

	function _o_get_distance($f_r, $f_c, $t_r, $t_c)
	{
		if ($f_r == $t_r && $f_c == $t_c)
		{
			return 0;
		}

		// ignore diagonals for now
		if ($f_r == $t_r)
		{
			// rows are same, cols different
			return abs($f_c - $t_c);
		}
		else
		if ($f_c == $t_c)
		{
			return abs($f_r - $t_r);
		}
		return 1;
	}

	function _o_get_pos($grid, $actid)
	{
		foreach($grid as $r => $rd)
		{
			foreach($rd as $c => $cd)
			{
				if ($cd["id"] == $actid)
				{
					return array($r, $c);
				}
			}
		}
		return false;
	}

	function _o_get_trans($o)
	{
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_TRANSITION",
		));

		$ret = array();
		foreach($conns as $conn)
		{
			$ret[] = $conn->to();
		}
		return $ret;
	}

	function _o_init_grid(&$grid, $per_row, $acts)
	{
		$idx = $per_row;
		$grid = array();
		foreach($acts as $act)
		{
			$actid = $act->id();
			$actn = $act->name();

			if ($actid == $this->root_action_id)
			{
				$grid[0][0] = array(
					"id" => $actid,
					"n" => $actn
				);
			}
			else
			if ($actid == $this->end_action_id)
			{
				$l_actn = $actn;
			}
			else
			{
				$row = (int)($idx / $per_row);
				$col = $idx % $per_row;
				$max_row = max($row, $max_row);

				$grid[$row][$col] = array(
					"id" => $actid,
					"n" => $actn
				);
				$idx++;
			}
		}

		if ($this->end_action_id)
		{
			$grid[$max_row+1][0] = array(
				"id" => $this->end_action_id,
				"n" => $l_actn
			);
		}
	}

	function _o_dump_grid($grid)
	{
		echo "<pre>";
		$rows = count($grid);
		for($ridx = 0; $ridx < $rows; $ridx++)
		{
			$cols = count($grid[$ridx]);
			for($cidx = 0; $cidx < $cols; $cidx++)
			{
				$cd = $grid[$ridx][$cidx];
				echo "($ridx,$cidx) = [".$cd["n"]."]\t\t";
			}
			echo "\n";
		}
		echo "</pre>";
	}
}
?>
