<?php

// form.aw - Class for creating forms
// This class should be split in 2, one that handles editing of forms, and another that allows
// filling them and processing the results. It's needed to complete our plan to take over the world.

// constants for get_elements_for_row - specify wheter the return array is
// element_name => element_value
define("ARR_ELNAME", 1);
// or element_id => element_value
define("ARR_ELID",2);

// constants for get_element_by_name
// it returns just the first element with the name
define("RET_FIRST", 1);
// it returns all elements with the name, return type is array
define("RET_ALL", 2);

class form extends form_base
{
	function form()
	{
		$this->form_base();

		$this->sub_merge = 1;

		// these types are used in the "add new form" page
		// feel free to move them to the ini file
		$this->ftypes = array(
			FTYPE_ENTRY => $this->vars["LC_FORMS_TYPE_ENTRY"],
			FTYPE_SEARCH => $this->vars["LC_FORMS_TYPE_SEARCH"],
			FTYPE_FILTER_SEARCH => $this->vars["LC_FORMS_TYPE_FILTER_SEARCH"],
			FTYPE_CONFIG => $this->vars["LC_FORMS_TYPE_CONFIG"],
		);

		$this->formaliases = "";
		$this->entry_id = 0;

		$this->active_currency = 0;

		if (!$this->controller_instance)
		{
			$this->controller_instance = get_instance(CL_FORM_CONTROLLER);
		}
		$this->style_instance = get_instance(CL_STYLE);
	}

	////
	// !Alias parser
	function parse_alias($args = array())
	{
		extract($args);

		$replacement = $this->gen_preview(array(
			"id" => $alias["target"],
			"form_action" => "/reforb.".$this->cfg["ext"],
			"load_entry_data" => isset($GLOBALS["load_entry_data"]) ? $GLOBALS["load_entry_data"] : false,
			"load_entry_data_form" => isset($GLOBALS["load_entry_data_form"]) ? $GLOBALS["load_entry_data_form"] : false,
			"load_chain_data" => isset($GLOBALS["load_chain_data"]) ? $GLOBALS["load_chain_data"] : false
		));

		return $replacement;
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

	/** Generates form admin interface

		@attrib name=change params=name default="0"

		@param id required acl="edit;view"
		@param return_url optional
		@param alias_to optional

		@returns


		@comment
		$arr[id] - form id, required
		FIXME: Move form editing functions into a separate class
		so that we can market the form editor as a separate component

	**/
	function gen_grid($arr)
	{
		extract($arr);
		$this->if_init($id,"grid.tpl",LC_FORM_CHANGE_FORM);

//		$this->debug_map_print();

		for ($a=0; $a < $this->arr["cols"]; $a++)
		{
			$fi = "";
			if ($a == 0)
			{
				$this->vars(array(
					"add_col" => $this->mk_orb("add_col", array(
								"id" => $this->id,
								"after" => -1,
								"count" => 1,
								)),
				));
				$fi = $this->parse("FIRST_C");
			}

			$fl = true;
			/*for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				$els = $this->arr["contents"][$row][$a]->get_elements();
				reset($els);
				while(list(,$v) = each($els))
				{
					if (!$this->can("delete",$v["id"]))
					{
						$fl = false;
					}
				}
			}*/
			$this->vars(array(
				"form_col" => $a,
				"del_col"		=> $this->mk_orb("del_col",array("id" => $this->id, "col" => $a))
			));
			$cd = "";
			if ($fl == true)
			{
				$cd = $this->parse("DELETE_COL");
			}

			$this->vars(array(
				"FIRST_C" => $fi,
				"DELETE_COL" => $cd,
				"add_col"	=> $this->mk_orb("add_col", array("id" => $this->id, "count" => 1, "after" => $a))
			));
			$this->parse("DC");
		}

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$cols="";
			$fl = true;
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				if (!($arr = $this->get_spans($i, $a)))
				{
					continue;
				}

				if (is_object($this->arr["contents"][$arr["r_row"]][$arr["r_col"]]))
				{
					$els = $this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->get_elements();
				}

				reset($els);
				$el = "";
				$el_cnt=0;
				while (list(, $v) = each($els))
				{
					// the element's can_view property is ignored here
					$this->vars(array(
						"form_cell_text"	=> $v["text"],
						"form_cell_order"	=> $v["order"],
						"element_id"			=> $v["id"],
						"el_name"					=> ($v["name"] == "" ? "&nbsp;" : $v["name"]),
						"el_type"					=> ($v["type"] == "" ? "&nbsp;" : $v["type"]),
						"form_cell_grp"   => $v["group"],
						"chpos" => $this->mk_my_orb("change_el_pos", array("id" => $this->id, "col" => (int)$arr["r_col"], "row" => (int)$arr["r_row"],"el_id" => $v["id"]))
					));
					$el.=$this->parse("ELEMENT");
					$el_cnt++;
				}

				$this->vars(array(
					"ELEMENT" => $el, "cell_col" => $a, "cell_row" => $i, "ELEMENT_NOEDIT" => "","num_els_plus3"=>($el_cnt+5),
					"exp_left"	=> $this->mk_orb("exp_cell_left", array("id" => $this->id, "col" => $a, "row" => $i)),
					"exp_up"		=> $this->mk_orb("exp_cell_up", array("id" => $this->id, "col" => $a, "row" => $i)),
					"exp_down"	=> $this->mk_orb("exp_cell_down", array("id" => $this->id, "col" => $a, "row" => $i)),
					"exp_right"	=> $this->mk_orb("exp_cell_right", array("id" => $this->id, "col" => $a, "row" => $i)),
					"split_ver"	=> $this->mk_orb("split_cell_ver", array("id" => $this->id, "col" => $a, "row" => $i)),
					"split_hor"	=> $this->mk_orb("split_cell_hor", array("id" => $this->id, "col" => $a, "row" => $i)),
					"admin_cell"	=> $this->mk_orb("admin_cell", array("id" => $this->id, "col" => (int)$arr["r_col"], "row" => (int)$arr["r_row"])),
					"add_element" => $this->mk_orb("add_element", array("id" => $this->id, "col" => (int)$arr["r_col"], "row" => (int)$arr["r_row"])),
				));
				$sh = ""; $sv = "";
				if ($arr["rowspan"] > 1)
				{
					$sh = $this->parse("SPLIT_HORIZONTAL");
				}
				if ($arr["colspan"] > 1)
				{
					$sv = $this->parse("SPLIT_VERTICAL");
				}

				$eu = "";
				if ($i != 0)
				{
					$eu = $this->parse("EXP_UP");
				}
				$el = "";
				if ($a != 0)
				{
					$el = $this->parse("EXP_LEFT");
				}
				$er = "";
				if (($a+$arr["colspan"]) != $this->arr["cols"])
				{
					$er = $this->parse("EXP_RIGHT");
				}
				$ed = "";
				if (($i+$arr["rowspan"]) != $this->arr["rows"])
				{
					$ed = $this->parse("EXP_DOWN");
				}

				$this->vars(array(
					"SPLIT_HORIZONTAL" => $sh,
					"SPLIT_VERTICAL" => $sv,
					"EXP_UP" => $eu,
					"EXP_LEFT" => $el,
					"EXP_RIGHT" => $er,
					"EXP_DOWN" => $ed
				));
				$cols.=$this->parse("COL");
			}
			$fi = "";
			if ($i==0)
			{
				$this->vars(array("add_row" => $this->mk_orb("add_row", array("id" => $this->id, "after" => -1, "count" => 1))));
				$fi = $this->parse("FIRST_R");
			}
			$this->vars(array("del_row" => $this->mk_orb("del_row", array("id" => $this->id, "row" => $i))));
			$cd = $this->parse("DELETE_ROW");

			$this->vars(array(
				"COL" => $cols,
				"FIRST_R" => $fi,
				"DELETE_ROW" => $cd,
				"add_row" => $this->mk_orb("add_row", array("id" => $this->id, "after" => $i, "count" => 1))
			));
			$l.=$this->parse("LINE");
		}

		$this->vars(array(
			"LINE"				=> $l,
			"addr_reforb"	=> $this->mk_reforb("add_row", array("id" => $this->id, "after" => $this->arr["rows"]-1)),
			"addc_reforb"	=> $this->mk_reforb("add_col", array("id" => $this->id, "after" => $this->arr["cols"]-1)),
			"reforb"			=> $this->mk_reforb("submit_grid", array("id" => $this->id))
		));
		return $this->do_menu_return();
	}

	/** Shows all form elements and lets user pick their style

		@attrib name=all_elements2 params=name default="0"

		@param id required acl="edit;view"

		@returns


		@comment
		TODO: Move to another class

	**/
	function gen_all_elements2($arr)
	{
		extract($arr);
		$this->if_init($id, "all_elements2.tpl", LC_FORM_ALL_ELEMENTS);

		for ($c =0; $c < $this->arr["cols"]; $c++)
		{
			$this->vars(array("col1" => $c+1));
			$hh.=$this->parse("HE");
		}
		$this->vars(array("HE" => $hh));

		$css = get_instance(CL_CSS);

		$this->vars(array("form_id" => $this->id));
		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$cols="";
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				$this->vars(array("ELEMENT"	=> "", "STYLEITEMS" => "", "SOME_ELEMENTS" => ""));
				if (!($arr = $this->get_spans($i, $a)))
				{
					continue;
				}

				$cell = &$this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->arr;
				$el_t = "";

				$_cell = new aw_array($cell);
				foreach($_cell->get() as $idx => $el)
				{
					// the element's can_view property is ignored here
					$this->vars(array(
						"row" => $arr["r_row"],
						"col" => $arr["r_col"],
						"index" => $idx,
						"el_text"	=> ($el->get_text() == "" ? "&nbsp;" : $el->get_text()),
						"el_name"	=> ($el->get_el_name() == "" ? "&nbsp;" : $el->get_el_name()),
						"el_type"	=> ($el->get_type() == "" ? "&nbsp;" : $el->get_type()),
						"taborder"	=> $el->arr["el_tabindex"],
						"css_style" => $this->picker($el->arr["el_css_style"], $css->get_select(true))
					));
					$meta = $el->get_metadata();
					$md = "";
					foreach($meta as $m_k => $m_v)
					{
						$this->vars(array(
							"meta" => $m_v,
							"meta_idx" => $m_k
						));
						$md .= $this->parse("METADATA");
					}

					$this->vars(array(
						"meta" => "",
						"meta_idx" => ""
					));
					$md .= $this->parse("METADATA");

					$is_tb = "";
					$is_rd = "";
					$is_ch = "";

					if ($el->get_type() == "textbox")
					{
						$this->vars(array(
							"length" => $el->arr["length"]
						));
						$is_tb = $this->parse("IS_TEXTBOX");
					}

					if ($el->get_type() == "radiobutton")
					{
						$this->vars(array(
							"value" => $el->arr["ch_value"]
						));
						$is_rd = $this->parse("IS_RADIO");
					}

					if ($el->get_type() == "checkbox")
					{
						$this->vars(array(
							"value" => $el->arr["ch_value"]
						));
						$is_ch = $this->parse("IS_CHECK");
					}

					$this->vars(array(
						"METADATA" => $md,
						"IS_TEXTBOX" => $is_tb,
						"IS_RADIO" => $is_rd,
						"IS_CHECK" => $is_ch,
					));
					$el_t.=$this->parse("ELEMENT");
				}

				$this->vars(array(
					"ELEMENT" => $el_t,
					"col" => $arr["r_col"],
					"row" => $arr["r_row"],
					"row1" => $arr["r_row"]+1
				));

				$this->vars(array("SOME_ELEMENTS" => $this->parse("SOME_ELEMENTS")));

				$cols.=$this->parse("COL");
			}
			$this->vars(array("COL" => $cols));
			$this->parse("LINE");
		}
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_all_els2", array("id" => $id)),
			"styles" => $this->picker(0,$stylesel),
			"folders" => $this->picker(0,(is_array($this->arr["el_move_menus"]) &&  count($this->arr["el_move_menus"]) > 0 ? array("" => "" ) + $this->arr["el_move_menus"] : $this->get_menu_list(false,true))),
			"types" => $this->picker(0,$this->listall_el_types(true)),
			"controllers" => $this->multiple_option_list(array(), $this->get_list_controllers(true))
		));

		return $this->do_menu_return();
	}

	/**

		@attrib name=submit_all_els2

	**/
	function submit_all_els2($arr)
	{
		extract($arr);
		$this->load($id);
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				if (!($arr = $this->get_spans($row, $col)))
				{
					continue;
				}

				$cell = &$this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->arr;
				$_cell = new aw_array($cell);
				foreach($_cell->get() as $idx => $el)
				{
					$t_meta = array();

					$awa = new aw_array($meta[$arr["r_row"]][$arr["r_col"]][$idx]);
					foreach($awa->get() as $m_idx => $m_data)
					{
						if ($m_data["key"] != "")
						{
							$t_meta[$m_data["key"]] = $m_data["value"];
						}
					}

					$this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->arr[$idx]->arr["metadata"][aw_global_get("lang_id")] = $t_meta;
					$this->arr["elements"][$arr["r_row"]][$arr["r_col"]][$el->get_id()]["metadata"][aw_global_get("lang_id")] = $t_meta;

					$this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->arr[$idx]->arr["el_tabindex"] = $taborder[$arr["r_row"]][$arr["r_col"]][$idx];
					$this->arr["elements"][$arr["r_row"]][$arr["r_col"]][$el->get_id()]["el_tabindex"] = $taborder[$arr["r_row"]][$arr["r_col"]][$idx];

					$this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->arr[$idx]->arr["el_css_style"] = $css_style[$arr["r_row"]][$arr["r_col"]][$idx];
					$this->arr["elements"][$arr["r_row"]][$arr["r_col"]][$el->get_id()]["el_css_style"] = $css_style[$arr["r_row"]][$arr["r_col"]][$idx];

					if ($el->get_type() == "checkbox")
					{
						$this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->arr[$idx]->arr["ch_value"] = $checkb[$arr["r_row"]][$arr["r_col"]][$idx];
						$this->arr["elements"][$arr["r_row"]][$arr["r_col"]][$el->get_id()]["ch_value"] = $checkb[$arr["r_row"]][$arr["r_col"]][$idx];
					}
					if ($el->get_type() == "radiobutton")
					{
						$this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->arr[$idx]->arr["ch_value"] = $radio[$arr["r_row"]][$arr["r_col"]][$idx];
						$this->arr["elements"][$arr["r_row"]][$arr["r_col"]][$el->get_id()]["ch_value"] = $radio[$arr["r_row"]][$arr["r_col"]][$idx];
					}
					if ($el->get_type() == "textbox")
					{
						$this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->arr[$idx]->arr["length"] = $textbox[$arr["r_row"]][$arr["r_col"]][$idx];
						$this->arr["elements"][$arr["r_row"]][$arr["r_col"]][$el->get_id()]["length"] = $textbox[$arr["r_row"]][$arr["r_col"]][$idx];
					}
				}
			}
		}
		$this->save();

		return $this->mk_my_orb("all_elements2", array("id" => $id));
	}

	/** Shows all form elements and lets user pick their style

		@attrib name=all_elements params=name default="0"

		@param id required acl="edit;view"

		@returns


		@comment
		TODO: Move to another class

	**/
	function gen_all_elements($arr)
	{
		extract($arr);
		$this->if_init($id, "all_elements.tpl", LC_FORM_ALL_ELEMENTS);

		$style = get_instance(CL_STYLE);
		$stylesel = $style->get_select(0,ST_CELL,true);
		$css = get_instance(CL_CSS);
		$tmp = $css->get_select();
		$stylesel = $stylesel + $tmp;

		for ($c =0; $c < $this->arr["cols"]; $c++)
		{
			$this->vars(array("col1" => $c+1));
			$hh.=$this->parse("HE");
		}
		$this->vars(array("HE" => $hh));

		$this->vars(array("form_id" => $this->id));
		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$cols="";
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				$this->vars(array("ELEMENT"	=> "", "STYLEITEMS" => "", "SOME_ELEMENTS" => ""));
				if (!($arr = $this->get_spans($i, $a)))
				{
					continue;
				}

				$cell = &$this->arr["contents"][$arr["r_row"]][$arr["r_col"]];
				$els = array();
				if (is_object($cell))
				{
					$els = $cell->get_elements();
				}
				reset($els);
				$el = "";
				while (list(, $v) = each($els))
				{
					// the element's can_view property is ignored here
					$this->vars(array(
						"el_text"	=> ($v["text"] == "" ? "&nbsp;" : $v["text"]),
						"el_name"	=> ($v["name"] == "" ? "&nbsp;" : $v["name"]),
						"el_type"	=> ($v["type"] == "" ? "&nbsp;" : $v["type"]),
						"form_cell_order"	=> $v["order"],
						"element_id"			=> $v["id"]
					));
					$el.=$this->parse("ELEMENT");
				}

				$__stsel = 0;
				if (is_object($this->arr["contents"][$arr["r_row"]][$arr["r_col"]]))
				{
					$__stsel = $this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->get_style();
				}
				$this->vars(array(
					"ELEMENT"				=> $el,
					"style_name" => $stylesel[$__stsel],
					"col"						=> $arr["r_col"],
					"row"						=> $arr["r_row"],
					"row1" => $arr["r_row"]+1
				));

				$this->vars(array("SOME_ELEMENTS" => $this->parse("SOME_ELEMENTS")));

				$cols.=$this->parse("COL");
			}
			$this->vars(array("COL" => $cols));
			$this->parse("LINE");
		}
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_all_els", array("id" => $id)),
			"styles" => $this->picker(0,$stylesel),
			"folders" => $this->picker(0,(is_array($this->arr["el_move_menus"]) &&  count($this->arr["el_move_menus"]) > 0 ? array("" => "" ) + $this->arr["el_move_menus"] : $this->get_menu_list(false,true))),
			"types" => $this->picker(0,$this->listall_el_types(true)),
			"controllers" => $this->multiple_option_list(array(), $this->get_list_controllers(true))
		));

		return $this->do_menu_return();
	}

	/** saves the selected styles from viewing all element layout

		@attrib name=submit_all_els params=name default="0"


		@returns


		@comment
		TODO: Move to another class

	**/
	function submit_all_els($arr)
	{
		extract($arr);

		$style_obj = obj();
		if ($setstyle)
		{
			$style_obj = obj($setstyle);
		}

		$this->load($id);
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				if ($chk[$row][$col] == 1)
				{
					$this->arr["contents"][$row][$col]->set_style($setstyle,$style_obj->class_id());
					if ($addel)
					{
						// we must add an element of the specified type to this cell
						$this->arr["contents"][$row][$col]->do_add_element(array(
							"parent" => $this->arr["newel_parent"],
							"name" => "uus_element_".(++$newelcnt),
							"based_on" => $addel
						));
					}
				}
			}
		}

		if (is_array($selel))
		{
			foreach($selel as $selid)
			{
				if ($setfolder)
				{
					$tmp = obj($selid);
					$tmp->set_parent($parent);
					$tmp->save();
				}
			}
		}
		$this->save();

		if (is_array($selel) && isset($diliit))
		{
			$this->load($id);
			foreach($selel as $selid)
			{
				$el = $this->get_element_by_id($selid);
				if ($el)
				{
					unset($this->arr["elements"][$el->get_row()][$el->get_col()][$selid]);
					$el->del();
				}
			}
			$this->save();
		}

		if (is_array($selel))
		{
			$this->load($id);
			$tad = $this->make_keys($add_entry_controllers);
			$tsd = $this->make_keys($add_show_controllers);
			$tld = $this->make_keys($add_lb_controllers);

			foreach($selel as $selid)
			{
				$el =& $this->get_element_by_id($selid);
				if (is_array($add_entry_controllers))
				{
					if ($add_controllers == 1)
					{
						foreach($tad as $k => $v)
						{
							$this->arr["elements"][$el->get_row()][$el->get_col()][$selid]["entry_controllers"][$k] = $v;
						}
					}
					else
					{
						$this->arr["elements"][$el->get_row()][$el->get_col()][$selid]["entry_controllers"] = $tad;
					}
				}
				if (is_array($add_show_controllers))
				{
					if ($add_controllers == 1)
					{
						foreach($tsd as $k => $v)
						{
							$this->arr["elements"][$el->get_row()][$el->get_col()][$selid]["show_controllers"][$k] = $v;
						}
					}
					else
					{
						$this->arr["elements"][$el->get_row()][$el->get_col()][$selid]["show_controllers"] = $tsd;
					}
				}
				if (is_array($add_lb_controllers))
				{
					if ($add_controllers == 1)
					{
						foreach($tld as $k => $v)
						{
							$this->arr["elements"][$el->get_row()][$el->get_col()][$selid]["lb_item_controllers"][$k] = $v;
						}
					}
					else
					{
						$this->arr["elements"][$el->get_row()][$el->get_col()][$selid]["lb_item_controllers"] = $tld;
					}
				}
				if ($add_value_controller)
				{
					$this->arr["elements"][$el->get_row()][$el->get_col()][$selid]["value_controller"] = $add_value_controller;
				}
				if ($add_def_value_controller)
				{
					$this->arr["elements"][$el->get_row()][$el->get_col()][$selid]["default_controller"] = $add_def_value_controller;
				}
			}
			$this->save();
		}

		return $this->mk_my_orb("all_elements", array("id" => $id));
	}

	/** saves the table properties of the form

		@attrib name=save_settings params=name default="0"


		@returns


		@comment
		TODO: Move to another class

	**/
	function save_settings($arr)
	{
		extract($arr);
		$this->load($id);

		$this->arr["allow_html"] = $allow_html;
		$this->arr['allow_html_set'] = 1;
		$this->arr["def_style"] = $def_style;
		$this->arr["after_submit"] = $after_submit;
		$this->arr["after_submit_text"] = $after_submit_text;
		$this->arr["after_submit_link"] = $after_submit_link;
		$this->arr["has_aliasmgr"] = $has_aliasmgr;
		$this->arr["has_controllers"] = $has_controllers;
		$this->arr["sql_writer"] = $sql_writer;
		$this->arr["sql_writer_form"] = $sql_writer_form;
		$this->arr["show_form_with_results"] = $show_form_with_results;
		$this->arr["sql_writer_writer"] = $sql_writer_writer;
		$this->arr["sql_writer_writer_form"] = $sql_writer_writer_form;
		$this->arr["search_act_lang_only"] = $search_act_lang_only;
		$this->arr["hide_empty_rows"] = $hide_empty_rows;
		$this->arr["calendar_controller"] = $calendar_controller;
		$this->arr["sql_writer_redirect_after"] = $sql_writer_redirect_after;
		$this->arr["no_use_eid_once"] = $no_use_eid_once;
		$this->arr["trans_sect"] = $trans_sect;
		$this->arr["dont_show_trans"] = $dont_show_trans;
		$this->arr["js_default_element"] = $js_default_element;
		$this->arr["join_optimizer_pessimist"] = $join_optimizer_pessimist;

		if ($is_translatable && !$this->arr["is_translatable"])
		{
			// create translations for ell entries
			$this->_create_translations();
		}

		$this->arr["is_translatable"] = $is_translatable;

		if ($this->subtype & FSUBTYPE_JOIN)
		{
			$this->subtype = FSUBTYPE_JOIN;
		}
		else
		{
			$this->subtype = 0;
		}
		if ($email_form_action)
		{
			$this->subtype += FSUBTYPE_EMAIL_ACTION;
		};

		$this->subtype += $calendar_role;

		$this->flags = 0;
		if ($has_calendar)
		{
			$this->flags += OBJ_HAS_CALENDAR;
		};


		$this->arr["name_els"] = array();
		if (is_array($entry_name_el))
		{
			foreach($entry_name_el as $elid)
			{
				$this->arr["name_els"][$elid] = $elid;
			}
		}

		if ($this->type != FTYPE_SEARCH)
		{
			if (aw_ini_get("site_id") != 22)
			{
			if (join(",",map("%s",$old_namels)) != join(",",map("%s",$this->arr["name_els"])))
			{
				// now go through all entries and rename them
				$this->db_query("SELECT oid FROM objects LEFT JOIN form_entries ON form_entries.id = objects.oid WHERE class_id = ".CL_FORM_ENTRY." AND form_entries.form_id = ".$this->id." AND status != 0");
				while ($row = $this->db_next())
				{
					$this->save_handle();
					$this->entry_name = "";
					$this->load_entry($row["oid"]);
					$this->update_entry_name($row["oid"]);
					$this->restore_handle();
				}
			}
			}
		}

		$this->arr["try_fill"] = $try_fill;
		$this->arr["check_status"] = $check_status;
		$this->arr["check_status_text"] = $check_status_text;
		$this->arr["show_table"] = $show_table;
		$this->arr["table"] = $table;
		$this->arr["tablestyle"] = $tablestyle;
		$this->arr["after_submit_op"] = $after_submit_op;
		$this->arr["sort_op_by"] = $sort_op_by;
		$this->arr["sort_op_order"] = $sort_op_order;
		$this->save();
		return $this->mk_orb("table_settings", array("id" => $id));
	}

	/** saves the changes the user has made in the form generated by gen_grid

		@attrib name=submit_grid params=name default="0"


		@returns


		@comment
		TODO: Move to another class

	**/
	function save_grid($arr)
	{
		extract($arr);
		$this->load($id);

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				$this->arr["contents"][$i][$a]->save_short($arr);
			}
		}

		$this->save();

		// ok here we must do the element separation for all the checked elements
		if (is_array($sel))
		{
			$this->load($id);
			foreach($sel as $elid)
			{
				$inothers = true;
				$this->db_query("SELECT * FROM element2form WHERE el_id = $elid AND form_id != $id");
				while ($row = $this->db_next())
				{
					$inothers = true;
				}
				if ($inothers)
				{
					// since this element is in some other forms as well, we must create a replica and remove the old one from this form
					$el = $this->get_element_by_id($elid);

					if ($el)
					{
						$el_parent = $this->db_fetch_field("SELECT parent FROM objects WHERE oid = ".$el->get_id(),"parent");

						$newelid = $this->arr["contents"][$el->get_row()][$el->get_col()]->do_add_element(array(
							"name" => $el->get_el_name(),
							"parent" => $this->arr["tear_folder"],
							"based_on" => $elid,
							"props" => $el->get_props()
						));

						// save element props also
						$prp = aw_serialize($this->arr["elements"][$el->get_row()][$el->get_col()][$newelid],SERIALIZE_XML);
						$this->quote(&$prp);
						$this->db_query("UPDATE form_elements SET props = '$prp' WHERE id = $newelid");

						unset($this->arr["elements"][$el->get_row()][$el->get_col()][$elid]);
						$el->del();	// delete the element from this form
					}
				}
			}
			$this->save();
		}

		global $HTTP_POST_VARS;
		$cdelete = array();
		$rdelete = array();
		reset($_POST);
		while (list($k,$v) = each($_POST))
		{
			if (substr($k,0,3) == 'dc_' && $v==1)
				$cdelete[substr($k,3)] = substr($k,3);
			else
			if (substr($k,0,3) == 'dr_' && $v==1)
				$rdelete[substr($k,3)] = substr($k,3);
		}

		// kustutame tagant-ettepoole, niiet numbrid ei muutuks
		krsort($cdelete,SORT_NUMERIC);
		krsort($rdelete,SORT_NUMERIC);

		reset($cdelete);
		while (list($k,$v) = each($cdelete))
		{
			$this->cells_loaded = false;
			$this->delete_column(array("id" => $id, "col" => $v));
		}

		reset($rdelete);
		while (list($k,$v) = each($rdelete))
		{
			$this->cells_loaded = false;
			$this->delete_row(array("id" => $id, "row" => $v));
		}

		return $this->mk_orb("change",array("id" => $this->id));
	}

	/** Adds $count columns after column $after in form $id

		@attrib name=add_col params=name default="0"

		@param id required acl="edit;view"
		@param after required
		@param count required

		@returns


		@comment

	**/
	function add_col($arr)
	{
		extract($arr);
		$this->load($id);

		$this->do_add_col($arr);

		$this->save();
		$orb = $this->mk_orb("change", array("id" => $this->id));
		// since this function can be called both from reforb and orb
		// we make sure we return to the right place afterwards.
		header("Location: $orb");
		return $orb;
	}

	function do_add_col($arr)
	{
		extract($arr);
		while ($count-- > 0)
		{
			$this->arr["cols"]++;
			$this->map_add_col($this->arr["rows"], $this->arr["cols"], &$this->arr["map"],$after);

			// move necessary elements to the right
			for ($i = $this->arr["cols"]; $i > ($after+1); $i--)
			{
				for ($a = 0; $a < $this->arr["rows"]; $a++)
				{
					$this->arr["elements"][$a][$i] = $this->arr["elements"][$a][$i-1];
				}
			}
			// zero out all elemnts on the newly added column
			for ($a = 0; $a < $this->arr["rows"]; $a++)
			{
				$this->arr["elements"][$a][$after+1] = array();
			}
		}
	}

	/** Adds rows to the form

		@attrib name=add_row params=name default="0"

		@param id required acl="edit;view"
		@param after required
		@param count required

		@returns


		@comment
		parameters:
		id - form id
		after - row number after which rows are added
		count - number of rows to add

	**/
	function add_row($arr)
	{
		extract($arr);
		$this->load($id);

		$this->do_add_row($arr);
		$this->save();
		$orb = $this->mk_orb("change", array("id" => $this->id));
		header("Location: $orb");
		return $orb;
	}

	function do_add_row($arr)
	{
		extract($arr);
		while ($count-- > 0)
		{
			$this->arr["rows"]++;
			$this->map_add_row($this->arr["rows"], $this->arr["cols"], &$this->arr["map"], $after);

			// now we must also move all elements in $this->arr[elements]
			// so that when the form is loaded they get put in the correct
			// places.
			for ($i=$this->arr["rows"]; $i > $after; $i--)
			{
				for ($a = 0; $a < $this->arr["cols"]; $a++)
				{
					$this->arr["elements"][$i][$a] = $this->arr["elements"][$i-1][$a];
				}
			}
			// zero out all elements on the newly inserted row
			for ($a = 0; $a < $this->arr["cols"]; $a++)
			{
				$this->arr["elements"][$after+1][$a] = array();
			}
		}
	}

	/** Deletes column $col of form $id

		@attrib name=del_col params=name default="0"

		@param id required acl="edit;view"
		@param col required

		@returns


		@comment

	**/
	function delete_column($arr)
	{
		extract($arr);
		$this->load($id);

		$this->do_delete_column($arr);

		$this->save();
		$orb = $this->mk_orb("change" , array("id" => $this->id));
		header("Location: $orb");
		return $orb;
	}

	function do_delete_column($arr)
	{
		extract($arr);
		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			// we don't delete the element from the database, we jsut delete it
			// from this form.
			$this->arr["elements"][$i][$col] = array();
			$this->arr["contents"][$i][$col]->del();
			$this->arr["contents"][$i][$col] = "";
			$this->arr["contents"][$i][$this->arr["cols"]-1] = array();
		}

		$this->map_del_col($this->arr["rows"], $this->arr["cols"], &$this->arr["map"],$col);

		// we must also shift all elements that are to the right of the deleted
		// column 1 position to the left
		for ($i=$col; $i < $this->arr["cols"]; $i++)
		{
			for ($a=0; $a < $this->arr["rows"]; $a++)
			{
				$this->arr["elements"][$a][$i] = $this->arr["elements"][$a][$i+1];
			}
		}

		$this->arr["cols"]--;
	}

	/** Deletes row $row from form $id

		@attrib name=del_row params=name default="0"

		@param id required acl="edit;view"
		@param row required

		@returns


		@comment

	**/
	function delete_row($arr)
	{
		extract($arr);
		$this->load($id);

		$this->do_delete_row($arr);

		$this->save();
		$orb = $this->mk_orb("change", array("id" => $this->id));
		header("Location: $orb");
		return $orb;
	}

	function do_delete_row($arr)
	{
		extract($arr);
		for ($i=0; $i < $this->arr["cols"]; $i++)
		{
			$this->arr["elements"][$row][$i] = array();
			$this->arr["contents"][$row][$i]->del();
			$this->arr["contents"][$row][$i] = "";
			$this->arr["contents"][$this->arr["rows"]-1][$i] = "";
		}

		$this->map_del_row($this->arr["rows"], $this->arr["cols"], &$this->arr["map"], $row);

		// we must move all elements below the deleted row up by one
		for ($i = $row; $i < $this->arr["rows"]; $i++)
		{
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				$this->arr["elements"][$i][$a] = $this->arr["elements"][$i+1][$a];
			}
		}

		$this->arr["rows"]--;
	}

	/** Generates the form used in modifying the table settings

		@attrib name=table_settings params=name default="0"

		@param id required acl="edit;view"

		@returns


		@comment

	**/
	function gen_settings($arr)
	{
		extract($arr);
		$this->if_init($id,"settings.tpl", LC_FORM_CHANGE_SETTINGS);

		$t = get_instance(CL_STYLE);
		$menulist = $this->get_menu_list();
		$ops = $this->get_op_list($id);

		// Why such an obsession with bit masks instead of just using
		// $this->arr["something"]?
		// Because I need a fast way (and that means directly in the SQL
		// clause) to figure out the type of the form -- duke

		$keys = array(0,FSUBTYPE_EV_ENTRY,FSUBTYPE_CAL_CONF,FSUBTYPE_CAL_SEARCH,FSUBTYPE_CAL_CONF2);
		foreach($keys as $_role)
		{
			if ((int)$this->subtype & $_role)
			{
				$sel_role = $_role;
			};
		};

		// let form_base->do_menu know that it needs to draw the calendar tab
		if ($sel_role)
		{
			$this->uses_calendar = true;
		};

		$roles = array(
			"0" => "Puudub",
			FSUBTYPE_EV_ENTRY => "Eventite sisestamine",
			FSUBTYPE_CAL_CONF => "Ajavahemike sisestamine",
			FSUBTYPE_CAL_CONF2 => "Kalendri defineerimine",
			FSUBTYPE_CAL_SEARCH => "Otsing",
		);

		$allow_html = true;
		if ($this->arr['allow_html_set'])
		{
			$allow_html = $this->arr['allow_html'];
		}

		$ol = new object_list(array(
			"class_id" => CL_FORM_TABLE,
			"site_id" => array(),
			"lang_id" => array()
		));
		$ft_list = $ol->names();

		$ol = new object_list(array(
			"class_id" => CL_FORM_CONTROLLER,
			"site_id" => array(),
			"lang_id" => array()
		));
		$fc_list = $ol->names();

		$this->vars(array(
			"roles"		=> $this->picker($sel_role,$roles),
			"allow_html"	=> checked($allow_html),
			"def_style"	=> $this->picker($this->arr["def_style"],$t->get_select(0,ST_CELL)),
			"as_1"	=> ($this->arr["after_submit"] == 1 ? "CHECKED" : ""),
			"as_2"	=> ($this->arr["after_submit"] == 2 ? "CHECKED" : ""),
			"as_3"	=> ($this->arr["after_submit"] == 3 ? "CHECKED" : ""),
			"as_4"	=> ($this->arr["after_submit"] == 4 ? "CHECKED" : ""),
			"ops" => $this->picker($this->arr["after_submit_op"], $ops[$id]),
			"els"	=> $this->multiple_option_list(is_array($this->arr["name_els"]) ? $this->arr["name_els"] : $this->arr["name_el"] ,$this->get_all_elements()),
			"try_fill"	=> checked($this->arr["try_fill"]),
			"check_status"	=> checked($this->arr["check_status"]),
			"has_aliasmgr"	=> checked($this->arr["has_aliasmgr"]),
			"has_controllers"	=> checked($this->arr["has_controllers"]),
			"email_form_action" => checked($this->subtype & FSUBTYPE_EMAIL_ACTION),
			"check_status_text" => $this->arr["check_status_text"],
			"show_table_checked" => checked($this->arr["show_table"]),
			"tables" => $this->picker($this->arr["table"],$ft_list),
			"calendar_controllers" => $this->picker($this->arr["calendar_controller"],$fc_list),
			"tablestyles" => $this->picker($this->arr["tablestyle"], $t->get_select(0,ST_TABLE)),
			"search_doc" => $this->mk_orb("search_doc", array(),"links"),
			"has_calendar" => checked($this->flags & OBJ_HAS_CALENDAR),
			"sql_writer" => checked($this->arr["sql_writer"]),
			"sql_writer_writer" => checked($this->arr["sql_writer_writer"]),
			"sql_writer_writer_forms" => $this->picker($this->arr["sql_writer_writer_form"], $this->get_flist(array("type" => FTYPE_ENTRY, "addfolders" => true, "search" => true))),
			"forms" => $this->picker($this->arr["sql_writer_form"], $this->get_flist(array("type" => FTYPE_ENTRY, "addfolders" => true, "search" => true))),
			"show_form_with_results" => checked($this->arr["show_form_with_results"]),
			"search_act_lang_only" => checked($this->arr["search_act_lang_only"]),
			"hide_empty_rows" => checked($this->arr["hide_empty_rows"]),
			"is_translatable" => checked($this->arr["is_translatable"]),
			"sql_writer_redirect_after" => htmlspecialchars($this->arr["sql_writer_redirect_after"]),
			"no_use_eid_once" => checked($this->arr["no_use_eid_once"]),
			"dont_show_trans" => checked($this->arr["dont_show_trans"]),
			"js_default_element" => $this->picker($this->arr["js_default_element"], array("" => "") + $this->get_all_elements()),
			"join_optimizer_pessimist" => checked($this->arr["join_optimizer_pessimist"])
		));

		$sopels = "";
		$awa = new aw_array($this->arr["sort_op_by"]);
		$maxnr = 1;
		foreach($awa->get() as $nr => $sop_el)
		{
			if (!$sop_el)
			{
				continue;
			}
			$this->vars(array(
				"sop_nr" => $nr,
				"s_op_elements" => $this->picker($sop_el,array("" => "") + $this->get_all_elements()),
				"s_op_orders" => $this->picker($this->arr["sort_op_order"][$nr], array("" => "","asc" => "kasvavas", "desc" => "kahanevas"))
			));
			$sopels .= $this->parse("SEARCH_OP");
			$maxnr = $nr+1;
		}

		$this->vars(array(
			"sop_nr" => $maxnr,
			"s_op_elements" => $this->picker(0,array("" => "") + $this->get_all_elements()),
			"s_op_orders" => $this->picker("", array("" => "", "asc" => "kasvavas", "desc" => "kahanevas"))
		));
		$sopels .= $this->parse("SEARCH_OP");
		$this->vars(array(
			"SEARCH_OP" => $sopels
		));

		$l = get_instance("languages");
		$lang_list = $l->get_list();

		$asl_lang ="";
		foreach($lang_list as $lid => $lname)
		{
			if (is_array($this->arr["after_submit_link"]))
			{
				$asl = $this->arr["after_submit_link"][$lid];
			}
			else
			{
				$asl = $this->arr["after_submit_link"];
			}

			$this->vars(array(
				"lang_name" => $lname,
				"lang_id" => $lid,
				"after_submit_link" => $asl
			));
			$asl_lang .= $this->parse("ASL_LANG");
		}
		$this->vars(array(
			"ASL_LANG" => $asl_lang
		));

		$lt = "";
		if ($this->arr["is_translatable"])
		{
			$show = true;
			$admg = aw_ini_get("form.translation_groups");
			if ($admg)
			{
				if (!in_array($admg, aw_global_get("gidlist")))
				{
					$show = false;
				}
			}

			if ($show)
			{
				foreach($lang_list as $lid => $ln)
				{
					$this->vars(array(
						"lang_name" => $ln,
						"lang_id" => $lid,
						"trans_sect" => $this->arr["trans_sect"][$lid]
					));

					$lt .= $this->parse("IS_TRANSLATABLE");
				}
			}
		}
		$this->vars(array(
			"IS_TRANSLATABLE" => $lt
		));

		$ns = "";
		if ($this->type != 2)
		{
			$ns = $this->parse("NOSEARCH");
		}
		else
		{
			$ns = $this->parse("SEARCH");
		}

		$this->vars(array(
			"NOSEARCH" => $ns,
			"SEARCH" => "",
			"reforb"	=> $this->mk_reforb("save_settings", array("id" => $this->id))
		));
		return $this->do_menu_return();
	}

	/** Wrapper for showing alias manager inside the formgen

		@attrib name=form_aliasmgr params=name default="0"

		@param id required

		@returns


		@comment

	**/
	function form_aliasmgr($args = array())
	{
		extract($args);
		$this->if_init($id,"aliasmgr.tpl", $this->vars["LC_FORMS_ALIASMGR"]);
		$this->vars(array(
			"aliasmgr_link" => $this->mk_my_orb("list_aliases",array("id" => $id),"aliasmgr"),
		));
		return $this->do_menu_return();

	}

	/** shows form $id

		@attrib name=show params=name nologin="1" default="0"

		@param id required
		@param entry_id optional
		@param extraids optional
		@param reforb optional
		@param load_entry_data optional
		@param load_entry_data_form optional
		@param load_chain_data optional
		@param method optional

		@returns


		@comment
		optional parameters:
		$entry_id - the entry to show
		$reforb - replaces {VAR:reforb}
		$form_action = <form action='$form_action'
		$extraids - array of parameters to pass along with the form
		$elvalues - array of name => value pairs for elements that specify default values
		$prefix - value to prefix the element names with
		$silent_errors - if true, error messages are only written to syslog, not shown to user
		$load_entry_data - loads the specified entry's data (can be an other form) and matches the elements in this form by element names
		$method - form submit mthod, defaults to POST
		$load_chain_data - loads the specified chain entry's data and matches the elements in this form by element names

	**/
	function gen_preview($arr)
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		$arr["prefix"] = isset($arr["prefix"]) ? $arr["prefix"] : false;
		$arr["elvalues"] = isset($arr["elvalues"]) ?  $arr["elvalues"] : (isset($GLOBALS["elvalues"]) ? $GLOBALS["elvalues"] : false) ;
		$arr["no_submit"] = isset($arr["no_submit"]) ? $arr["no_submit"]  : false;
		extract($arr);

		// kui id-d pole antud, siis kasutame seda vormi, mis juba eelnevalt
		// laetud on. Somewhere.
		if (isset($id))
		{
			$this->load($id);
		};

		if ($form_action == "")
		{
			if (stristr(aw_global_get("REQUEST_URI"),"/automatweb")!=false)
			{
				$form_action = "/automatweb/reforb.".$this->cfg["ext"];
			}
			else
			{
				$form_action = "/reforb.".$this->cfg["ext"];
			}
		}

		if (!$entry_id && ($_eid = aw_global_get("form_use_entry_id_once_".$this->id)) != "")
		{
			$entry_id = $_eid;
			$arr["entry_id"] = $_eid;
			aw_session_del("form_use_entry_id_once_".$this->id);
		}

		$section = aw_global_get("section");

		// obj_id is for config forms and it allows us to specify the object into which we would have to save
		// the data from the form

		if (!isset($reforb) || $reforb == "")
		{
			$reforb = $this->mk_reforb("process_entry", array("id" => $this->id,"section" => $section,"obj_id" => $obj_id));
		}

		if (!$entry_id)
		{
			$entry_id = 0;
		}

		// if the entry is an error-entry, than the data will be in the session, check for that
		if (aw_global_get("form_".$this->id."_entry_".$entry_id."_is_error"))
		{
			// we are in error, load the data from the session
			$this->entry_id = $entry_id;
			$this->entry = aw_global_get("form_".$this->id."_entry_".$entry_id."_data");


			$this->controller_errors = aw_global_get("form_".$this->id."_entry_".$entry_id."_errors");
			// use a fake entry id just so that the values get shown
			$this->read_entry_from_array(-1);

			// and clear the session
			aw_session_del("form_".$this->id."_entry_".$entry_id."_data");
			aw_session_del("form_".$this->id."_entry_".$entry_id."_errors");
			aw_session_del("form_".$this->id."_entry_".$entry_id."_is_error");
		}
		else
		if (isset($entry_id) && $entry_id)
		{
			error::view_check($entry_id);
			$this->load_entry($entry_id,$silent_errors);
		}
		else
		{
			$this->elvalues = array();
			enter_function("form::load_chain_data");
			$tb_els = array();
			if ($load_entry_data)
			{
				if ($load_entry_data_form)
				{
					$lf_fid = $load_entry_data_form;
				}
				else
				{
					$lf_fid = $this->get_form_for_entry($load_entry_data);
				}
				$lf_fm = get_instance(CL_FORM);
				$lf_fm->load($lf_fid);
				$lf_fm->load_entry($load_entry_data);
				$lf_els = $lf_fm->get_all_els();
				foreach($lf_els as $lf_el)
				{
					$elvalues[$lf_el->get_el_name()] = $lf_fm->entry[$lf_el->get_id()];
					$this->elvalues[$lf_el->get_id()] = $lf_fm->entry[$lf_el->get_id()];
					if ($lf_el->get_type() == "textbox")
					{
						$tb_els[$lf_el->get_el_name()] = true;
					}
				}
			}
			if ($load_chain_data)
			{
				enter_function("form::load_chain_data::gce");
				$ed = $this->get_chain_entry($load_chain_data, true);
				exit_function("form::load_chain_data::gce");
				foreach($ed as $c_fid => $c_eid)
				{
					enter_function("form::load_chain_data::cgfi");
					$lf_fm =& $this->cache_get_form_instance($c_fid);
					exit_function("form::load_chain_data::cgfi");
					enter_function("form::load_chain_data::gae");
					$lf_els = $lf_fm->get_all_els();
					exit_function("form::load_chain_data::gae");

					enter_function("form::load_chain_data::le");
					$lf_fm->load_entry($c_eid);
					exit_function("form::load_chain_data::le");
					enter_function("form::load_chain_data::iter");
					foreach($lf_els as $lf_el)
					{
						$elvalues[$lf_el->get_el_name()] = $lf_fm->entry[$lf_el->get_id()];
						$this->elvalues[$lf_el->get_id()] = $lf_fm->entry[$lf_el->get_id()];
						if ($lf_el->get_type() == "textbox")
						{
							$tb_els[$lf_el->get_el_name()] = true;
						}
					}
					exit_function("form::load_chain_data::iter");
				}
			}

			if (is_array($elvalues))
			{
				for ($row = 0; $row < $this->arr["rows"]; $row++)
				{
					for($col = 0; $col < $this->arr["cols"]; $col++)
					{
						for($idx = 0; $idx < $this->arr["contents"][$row][$col]->cnt; $idx++)
						{
							$el =& $this->arr["contents"][$row][$col]->arr[$idx];
							$eln = $el->get_el_name();
							if (isset($elvalues[$eln]))
							{
								$user_data = false;
								if ($tb_els[$eln] && $el->get_type() == "listbox")
								{
									$user_data = true;
								}
								$this->arr["contents"][$row][$col]->arr[$idx]->set_value($elvalues[$eln], $user_data);
							}
						}
					}
				}
			}
			exit_function("form::load_chain_data");
		}

		$this->read_template((isset($tpl) ? $tpl : "show.tpl"),1);

		$this->vars(array(
			"form_id" => $id,
			"method" => ($method != "" ? $method : "POST")
		));

		aw_global_set("fg_check_status",false);
		if ($this->arr["check_status"])
		{
			aw_global_set("fg_check_status",true);
			$this->vars(array("check_status_text" => $this->arr["check_status_text"]));
		};

		// if this is calendar, load the form_calendar object and make it available
		// to controllers
		if ($this->subtype & FSUBTYPE_EV_ENTRY)
		{
			// check vacations and if found, update calendar->form relations
			// set error messages otherwise
			$this->fcal_instance = get_instance("formgen/form_calendar");
			$this->fcal_instance->init_cal_controller(array("id" => $this->id));
			$rel_value = $this->get_element_selection_id($this->fcal_instance->el_relation);
			$this->fcal_instance->relation = $rel_value;
		}

		$c="";
		$chk_js = "";
		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$html=$this->mk_row_html($i,$prefix,$elvalues,$no_submit);
			if (!($html === "" && $this->arr["hide_empty_rows"] == 1))
			{
				$this->vars(array("COL" => $html));
				$c.=$this->parse("LINE");

				if ($this->type == FTYPE_ENTRY)
				{
					// generate all entry checking html
					for ($a = 0; $a < $this->arr["cols"]; $a++)
					{
						$chk_js .= $this->arr["contents"][$i][$a]->gen_check_html();
					}
				}
			}
		}

		$this->vars(array("var_name" => "entry_id", "var_value" => $this->entry_id));
		$ei = $this->parse("EXTRAIDS");
		$this->vars(array("var_name" => "return_url", "var_value" => aw_global_get("REQUEST_URI")));
		$ei .= $this->parse("EXTRAIDS");

		$ei .= $this->gen_preview_append;

		if (isset($extraids) && is_array($extraids))
		{
			reset($extraids);
			while(list($k,$v) = each($extraids))
			{
				$this->vars(array("var_name" => $k, "var_value" => $v));
				$ei.=$this->parse("EXTRAIDS");
			}
		}

		$tblstring = "";
		if ($this->arr["tablestyle"])
		{
			$st = get_instance(CL_STYLE);
			$tblstring = $st->get_table_string($this->arr["tablestyle"]);
		}

		$this->vars(array(
			"tblstring" => $tblstring,
			"LINE" => $c,
			"EXTRAIDS" => $ei,
			"form_action" => $form_action,
			"formtag_name" => $formtag_name, // lauri muudetud-> formtag_name on formi tagi nimi kuhu see form parsitakse
			"reforb" => $reforb,
			"checks" => $chk_js,
			"stat_check_sub" => (aw_global_get("fg_check_status")) ? $this->parse("stat_check_sub") : "",
			"ch_link" => $this->mk_my_orb("change",array("id" => $id),"form",1,1),
		));

		if ($this->can("edit",$id))
		{
			$this->vars(array(
				"EDIT" => $this->parse("EDIT"),
			));
		}

		$in_grps = true;
		if (aw_ini_get("form.translator_groups") != "")
		{
			$in_grps = false;
			$gps = explode(",", aw_ini_get("form.translator_groups"));
			$ugs = aw_global_get("gidlist");
			foreach($gps as $gp)
			{
				if (in_array($gp, $ugs))
				{
					$in_grps = true;
				}
			}
		}

		if ($this->arr["is_translatable"] && !$this->arr["dont_show_trans"] && $in_grps)
		{
			$tra = "";
			$la = get_instance("languages");
			$ll = $la->get_list();
			foreach($ll as $lid => $ld)
			{
				if ($lid == aw_global_get("lang_id"))
				{
					continue;
				}
				if (aw_global_get("is_showing_chain"))
				{
					if ($this->arr["trans_sect"][$lid])
					{
						$tu = aw_url_change_var("section", $this->arr["trans_sect"][$lid]);
						$tu = aw_url_change_var("set_lang_id", $lid, $tu);
					}
					else
					{
						$tu = aw_url_change_var("set_lang_id", $lid, $tu);
					}
				}
				else
				{
					$params = array("id" => $this->id, "entry_id" => $this->entry_id, "set_lang_id" => $lid);
					if ($this->arr["trans_sect"][$lid])
					{
						$params["section"] = $this->arr["trans_sect"][$lid];
					}
					$tu = $this->mk_my_orb("show" , $params);
				}
				$this->vars(array(
					"tr_link" => $tu,
					"lang" => $ld
				));
				$tra.=$this->parse("USER_TRANS");
			}
			$this->vars(array(
				"USER_TRANS" => $tra
			));
		}

		$st = $this->parse();

		// siia array sisse pannaxe css stiilide nimed ja id'd form_cell::gen_user_html sees, mis vaja genereerida
		if (is_array($this->styles))
		{
			$css_file = "";
			$css = get_instance(CL_CSS);
			$used = array();
			foreach($this->styles as $stylid => $stylname)
			{
				if ($used[$stylid] != 1)
				{
					$used[$stylid] = 1;
					$css_file .= $css->get_style_data_by_id($stylid);
				}
			}
			$st = "<style type=\"text/css\">".$css_file."</style>\n".$st;
		}

		if (($go_to = aw_global_get("form_redir_after_submit_".$this->id)) != "")
		{
			header("Location: $go_to");
			aw_session_del("form_redir_after_submit_".$this->id);
		}

		if ($this->arr["js_default_element"])
		{
			$st .= "<script language=\"javascript\">__el = aw_get_el('fm_".$this->id."', '".$this->arr["js_default_element"]."');if (__el)  { __el.focus(); }</script>";
		}

		return $st;
	}

	////
	// !generates one row of form elements
	function mk_row_html($row,$prefix = "",$elvalues = array(),$no_submit = false)
	{
		$html = "";
		for ($a=0; $a < $this->arr["cols"]; $a++)
		{
			if (($arr = $this->get_spans($row, $a)))
			{
				$ds = isset($this->arr["def_style"]) ? $this->arr["def_style"] : 0;
				if (is_object($this->arr["contents"][$arr["r_row"]][$arr["r_col"]]))
				{
					$_html = $this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->gen_user_html_not($ds,$arr["colspan"], $arr["rowspan"],$prefix,$elvalues,$no_submit);
					if ($_html !== -1)
					{
						$html .= $_html;
					}
				}
			}
		}
		return $html;
	}


	/** saves the entry for the form $id, if $entry_id specified, updates it instead of creating a new one

		@attrib name=process_entry params=name nologin="1" default="0"


		@returns


		@comment
		elements are assumed to be prefixed by $prefix
		optional argument $chain_entry_id - if creating a new entry and it is specified, the entry is created with that chain entry id
		parent (id) - mille alla entry salvestada
		no_load_form - if true, the form is not loaded
		no_process_entry - no entry is read from user entered data, the loaded entry is just saved
		no_load_entry - if set, the entry that is already loaded is used to save data - use this to change data before saving

	**/
	function process_entry($arr)
	{
		extract($arr);

		aw_set_exec_time(AW_LONG_PROCESS);
		// values can be passed from the caller inside the $values argument, or..
		if (is_array($values))
		{
			$this->post_vars = $values;
		}
		else
		if (!$no_post_vars)
		{
			// .. if that is not the case, then we just import all the POST variables.
			$this->post_vars = safe_array($_POST) + safe_array($_GET);
		};

		// if this is set to true, then a variable in the session will be set to the created/loaded entry id, so that
		// the next time the form is viewed in the current session, this entry id will be used if not specified in the url
		$this->set_use_eid_once = false;

		if (!$no_load_form)
		{
			$this->load($id);
		}

		// tshekime et kas see entry on ikka loaditud formi jaox
		if ($entry_id && $this->arr["save_table"] != 1)
		{
			$fid = $this->get_form_for_entry($entry_id);
			// ja kui pole siis ignoorime seda
			if ($fid != $id && $fid != "")
			{
				$entry_id = false;
			}
		}

		// if entry_id is set, load the entry so we can use the previous data as well
		if ($entry_id && !$no_load_entry)
		{
			$this->load_entry($entry_id);
		}
		$this->entry_id = $entry_id;
		aw_global_set("form_last_proc_entry_id", $entry_id);

		// ff_folder on vormi konfist mratud folderi id, mille alla entry peaks
		// minema. parent argument overraidib selle
		$this->entry_parent = isset($parent) ? $parent : $this->arr["ff_folder"];

		$this->controller_errors = array();

		$new = ($entry_id) ? false : true;

		// check_calendar might want to alter those
		$controllers_ok = true;
		$controller_warnings_ok = true;

		$this->in_process_entry = true;

		if (!$no_process_entry)
		{
//			echo "ctrlchk <br />";
			$this->controller_queue = array();
			$this->value_controller_queue = array();
			for ($i=0; $i < $this->arr["rows"]; $i++)
			{
				for ($a=0; $a < $this->arr["cols"]; $a++)
				{
					// gather the data from the bunch of POST variables into $this->entry
					// - an array of element_id => element_data
					$this->arr["contents"][$i][$a] -> process_entry(&$this->entry, $this->entry_id,$prefix);
				}
			}

			foreach($this->value_controller_queue as $dat)
			{
				$cval = $this->controller_instance->eval_controller(
					$dat["ctrl_id"],
					$dat["val"],
					&$this,
					&$this->arr["contents"][$dat["row"]][$dat["col"]]->arr[$dat["idx"]]
				);
				$this->arr["contents"][$dat["row"]][$dat["col"]]->arr[$dat["idx"]]->entry = $cval;
				$this->entry[$dat["id"]] = $cval;
			}

			foreach($this->controller_queue as $ctrl)
			{
				if (!$ctrl["val"])
				{
					$ctrl["val"] = $this->get_element_value($ctrl["el_id"],true);
				}
				$res = $this->controller_instance->do_check_and_html($ctrl["ctrlid"], $ctrl["val"], &$this, $this->get_element_by_id($ctrl["el_id"]));
//				echo "ctrlid = $ctrl[ctrlid] val - $ctrl[val] <br />";
				if ($res !== "")
				{
					if (!$res)
					{
						$res = "error!";
					};
					$this->controller_errors[$ctrl["el_id"]][] = $res;
					if (!$this->controller_instance->is_warning_controller($ctrl["ctrlid"]))
					{
						$controllers_ok = false;
					}
					$controller_warnings_ok = false;
					if (aw_global_get("uid") == "erkihotel")
					{
//						echo "ctrlid $ctrl[ctrlid] failed! <br />";
					}
				}
			}

			// moved calendar checks after value controller checks, so that calendar can use value
			// controller generated values.

			$this->has_controller_warnings = !$controller_warnings_ok;
			$this->has_controller_errors = !$controllers_ok;

			// check calendar only if other controllers had no errors
			if (!$this->has_controller_errors && ($this->subtype & FSUBTYPE_EV_ENTRY))
			{
				// if this form uses a calendar and is an event entry form, figure out
				// whether the calendar it is trying to write to, have enough vacancies
				// check vacations and if found, update calendar->form relations
				// set error messages otherwise
				$fcal = get_instance("formgen/form_calendar");
				$els = $this->get_form_elements(array(
					"use_loaded" => true,
					"key" => "id",
				));

				$errors = $fcal->check_calendar(array(
					"id" => $id,
					"post_vars" => $this->post_vars,//$this->entry,
					"entry_id" => $entry_id,
					"chain_entry_id" => $chain_entry_id,
					"els" => $els,
					"formref" => &$this,
				));

				$this->controller_errors = $this->controller_errors + $fcal->get_controller_errors();

				if (!empty($this->arr["calendar_controller"]) && $fcal->vac < 0)
				{
					$this->controller_instance->eval_controller(
						$this->arr["calendar_controller"],
						$fcal->msg,
						&$this
					);
				}

				$has_errors = $errors;
				$controller_warnings_ok = $fcal->fatal;
				$has_cal_errors = $errors;
			};
//			echo "ctrlok = $controllers_ok warnok = $controller_warnings_ok <br />";

			if ( (!$controllers_ok) || ($has_errors) || (!$controller_warnings_ok))
			{
				// ok, now the error messages are in $this->controller_errors

				if (!$entry_id)
				{
					$entry_id = 0;
				}

				// we must stick the data that was entered in the session,
				aw_session_set("form_".$this->id."_entry_".$entry_id."_data", $this->entry);
				aw_session_set("form_".$this->id."_entry_".$entry_id."_errors", $this->controller_errors);
				aw_session_set("form_".$this->id."_entry_".$entry_id."_is_error", true);
				aw_session_del("form_redir_after_submit_".$this->id);
				aw_session_set("no_cache", 1);

				if ((!$controllers_ok) || ($has_errors))
				{
					// return to the form display url and show the error messages to the user so he/she can
					// correct the data
					if ($return_url == "")
					{
						$return_url = $GLOBALS["return_url"];
						if ($return_url == "")
						{
							// if no return url was specified, try to come up with a reasonable one by ourselves
							$return_url = $this->mk_my_orb("show", array("id" => $this->id, "entry_id" => $this->entry_id));
						}
					}
					aw_session_set("form_".$this->id."_entry_".$entry_id."_is_error_url", $return_url);
					if (!$this->no_headers)
					{
						header("Location: ".$return_url);
						die();
					}
				}
			}
		}

		if ($new)
		{
			// we override the lang_id here, because entries that have been entered over
			// XML-RPC do not know what their language_id might be, so specify one.

			// well .. since form_rpc is gone, the above comment and any code related to
			// it is now useless
			$this->entry_id = $this->create_entry_object(array(
				"parent" => $this->entry_parent,
				"class_id" => CL_FORM_ENTRY,
				"lang_id" => $lang_id,
			));
		}

		// paneme kirja, et kasutaja t2itis selle formi et siis kasutajax regimisel saame seda kontrollida.
		$sff = aw_global_get("session_filled_forms");
		$sff[$this->id] = $this->entry_id;
		aw_session_set("session_filled_forms", $sff);

		if (!$controller_warnings_ok)
		{
			aw_session_set("form_".$this->id."_entry_".$this->entry_id."_data", $this->entry);
			aw_session_set("form_".$this->id."_entry_".$this->entry_id."_errors", $this->controller_errors);
			aw_session_set("form_".$this->id."_entry_".$this->entry_id."_is_error", true);
		}

		$this->update_entry_name($this->entry_id,$this->entry_parent);

		if ($new)
		{
			// now write the data from the previously gathered array to the storage medium
			// specified in the forms settings
			$this->create_entry_data(array(
				"entry_id" => $this->entry_id,
				"entry" => $this->entry,
				"chain_entry_id" => $chain_entry_id,
				"cal_id"  => $cal_id,
			));

			$this->is_new_entry = true;
			// see logimine on omal kohal ainult siis, kui titmine toimub
			// lbi veebi.
			$this->_log(ST_FORM_ENTRY, SA_ADD,"Lisas formi $this->name ($this->id) kaudu uue sisestuse $this->entry_name ($this->entry_id) ", $this->entry_id);
		}
		else
		{
			// update the stored data from the gathered data
			$this->update_entry_data($this->entry_id,$this->entry);
			$this->_log(ST_FORM_ENTRY, SA_CHANGE,"Muutis formi $this->name ($this->id) sisestust $this->entry_name ($this->entry_id)", $this->entry_id);
			$this->is_new_entry = false;
		}

		$eid = $this->entry_id;

		// if this form has anything to do with calendars, perform the necessary
		// actions. If we got here, all checks have been passed.
		if (!$no_process_entry && ($this->subtype & FSUBTYPE_EV_ENTRY))
		{
			$els = $this->get_form_elements(array(
                                "use_loaded" => true,
                                "key" => "id",
                        ));
			$fcal->make_event_relations(array(
				"id" => $id,
				"post_vars" => $this->post_vars,
				"eid" => $this->entry_id,
				"chain_entry_id" => $chain_entry_id,
				"els" => $els,
			));
		}
		elseif (!$no_process_entry && ($this->subtype & FSUBTYPE_CAL_CONF))
		{
			$fc = get_instance("formgen/form_calendar");
			$els = $this->get_form_elements(array(
                                "use_loaded" => true,
                                "key" => "id",
                        ));
			$fc->fg_update_cal_conf(array(
				"post_vars" => &$this->post_vars,
				"arr" => &$this->arr,
				"id" => &$this->id,
				"entry_id" => &$this->entry_id,
				"cal_id" => &$cal_id,
				"cal_relation" => &$cal_relation,
				"els" => $els,
			));
		}
		elseif (!$no_process_entry && ($this->subtype & FSUBTYPE_CAL_CONF2))
		{
			$id = $this->id;
			$_start = (int)$this->arr["cal_start"];
			$_end = (int)$this->arr["cal_end"];
			$_max = (int)$this->arr["cal_count"];
			$_period_cnt = (int)$this->arr["cal_period"];
			$q = "DELETE FROM calendar2timedef WHERE cal_id = '$id' AND entry_id = '$eid'";
			$this->db_query($q);
			$relation = ($chain_entry_id) ? $chain_entry_id : $this->entry_id;
			$q = "INSERT INTO calendar2timedef (oid,cal_id,entry_id,start,end,max_items,period,period_cnt,relation)
				VALUES ('$id','$id','$eid','$_start','$_end','$_max','2','$_period_cnt','$relation')";
			$this->db_query($q);
		};

		if ($this->set_use_eid_once == true && !$this->arr["no_use_eid_once"])
		{
			aw_session_set("form_use_entry_id_once_".$this->id, $this->entry_id);
		}

		$fact = get_instance("formgen/form_actions");
		$fact->do_actions(&$this, $this->entry_id);

		if (!empty($this->go_to_after_submit))
		{
			aw_session_del("form_redir_after_submit_".$this->id);
			return $this->go_to_after_submit;
		}

		if (isset($redirect_after) && $redirect_after)
		{
			// if this variable has been set in extraids when showing the form, redirect to it
			return $redirect_after;
		}

		switch ($this->get_location())
		{
			case "redirect":
				if (is_array($this->arr["after_submit_link"]))
				{
					$l = $this->arr["after_submit_link"][aw_global_get("lang_id")];
				}
				else
				{
					$l = $this->arr["after_submit_link"];
				}
				break;
			case "search_results":
				$l = $this->mk_my_orb("show_entry", array("id" => $id, "entry_id" => $this->entry_id, "op_id" => 1,"section" => $section));
				break;
			case "show_op":
				$l = $this->mk_my_orb("show_entry", array("id" => $id, "entry_id" => $this->entry_id, "op_id" => $this->arr["after_submit_op"],"section" => $section));
				break;
			default:
				if ($this->type == FTYPE_SEARCH || $this->type == FTYPE_FILTER_SEARCH)
				{
					// n2itame ocingu tulemusi
					$l = $this->mk_my_orb("show_entry", array("id" => $id, "entry_id" => $this->entry_id,"op_id" => 1,"section" => $section));
				}
				else
				{
					$l = $this->mk_my_orb("show", array("id" => $id, "entry_id" => $this->entry_id));
				}
				break;
		}

		aw_session_del("form_redir_after_submit_".$this->id);
		return $l;
	}

	////
	// !once upon a time a wanderer(duuk) posed the question:
	//
	// what exactly does this code do?
	//
	// well, you can select a bunch of elements and then the data entered in those elements will be used to name the form_entry object.
	// and this is where it's done - terryf
	//
	// and such was the reply.
	function update_entry_name($entry_id,$entry_parent = false)
	{
		$uar = array(
			"oid" => $entry_id,
			"comment" => ""
		);
		if ($entry_parent !== false)
		{
			$uar["parent"] = $entry_parent;
		}

		if (is_array($this->arr["name_els"]))
		{
			foreach($this->arr["name_els"] as $elid)
			{
				$el = $this->get_element_by_id($elid);
				if ($el)
				{
					if ($el->get_type() == "")
					{
						$this->entry_name.= " ".$el->get_text();
					}
					else
					{
						$this->entry_name.= " ".$el->get_value();
					}
				}
			}
			$uar["name"] = $this->entry_name;
 			$this->update_entry_object($uar);
		}
		else
		if ($this->arr["name_el"])
		{
			$el = $this->get_element_by_id($this->arr["name_el"]);
			if ($el)
			{
				if ($el->get_type() == "")
				{
					$this->entry_name = $el->get_text();
				}
				else
				{
					$this->entry_name = $el->get_value();
				}
			}
			$uar["name"] = $this->entry_name;
			$this->update_entry_object($uar);
		}
	}

	/** shows entry $entry_id of form $id using output $op_id

		@attrib name=show_entry params=name nologin="1" default="0"

		@param op_id required type=int
		@param id optional
		@param entry_id optional
		@param search_el optional
		@param search_val optional

		@returns


		@comment
		if $no_load_entry == true, the loaded entry is used
		if $no_load_op == true, the loaded output is used
		optional - search_el and search_val - if then does a search using only that element and value
		optional - load_chain_data / load_entry_data - fills the form with values from those entries (matches element names)

	**/
	function show($arr)
	{
		extract($arr);
		$lcd = $load_chain_data;

		// if reset argument is set, zero out all data that has been gathered inside templates
		if (isset($reset))
		{
			$this->tpl_reset();
		};

		if (!$no_load_entry && $id)
		{
			error::view_check($id);
			$this->load($id);
		}

		// if this is a search form, then search, instead of showing the entered data
		if ($this->type == FTYPE_SEARCH || $this->type == FTYPE_FILTER_SEARCH)
		{
			$tmp = $this->show_s_res($arr);
			return $tmp;
		}

		if (!$no_load_op)
		{
			error::view_check($op_id);
			if ($GLOBALS["fg_op_dbg"] == 1)
			{
				echo "op_id = $op_id <br />";
			}
			$this->load_output($op_id);
		}

		// if it is set for this op that it is to be loaded from the session then get the entry id for the form
		if ($this->output["session_value"] && !$entry_id)
		{
			$session_filled_forms = aw_global_get("session_filled_forms");
			$entry_id = $session_filled_forms[$this->output["session_form"]];
			if (!$entry_id)
			{
				$no_load_entry = true;
			}
			$id = $this->output["session_form"];
			if (!$id)
			{
				$this->raise_error(ERR_F_OP_NO_SESSION_FORM,sprintf(t("Sessioonist lugemise formi pole valitud v&auml;ljundile %s "), $op_id),true);
			}

			error::view_check($id);
			$this->load($id);
		}

		if (!$no_load_entry)
		{
			error::view_check($entry_id);
			$this->load_entry($entry_id);
			//$this->_do_value_controllers();
		}
		else
		{
			$entry_id = $this->entry_id;
		}
		if (isset($no_html) && $no_html)
		{
			$this->read_template("show_user_nohtml.tpl");
		}
		else
		{
			$this->read_template("show_user.tpl");
		}

		$t_style = get_instance(CL_STYLE);
		// kui on tabeli stiil m22ratud v2ljundile, siis kasutame seda, kui pole, siis vaatame kas sellele formile on
		// m22ratud default stiil ja kui on, siis kasutame seda
		$fcol_style = 0;
		$fcol_cnt = 0;
		$frow_style = 0;
		$frow_cnt = 0;
		if ($this->output["table_style"])
		{
			$fcol_style = $t_style->get_fcol_style($this->output["table_style"]);
			$fcol_cnt = $t_style->get_num_fcols($this->output["table_style"]);
			$frow_style = $t_style->get_frow_style($this->output["table_style"]);
			$frow_cnt = $t_style->get_num_frows($this->output["table_style"]);
		}

		// kui tabeli stiilis ei m22ratud default stiili, siis v6etakse see formist. I guess.
		if ($this->arr["def_style"] && $fcol_cnt < 1 && $frow_cnt < 1)
		{
			$fcol_style = $this->arr["def_style"];
			$fcol_cnt = $this->output["cols"];
			$frow_style = $this->arr["def_style"];
			$frow_cnt = $this->output["rows"];
		}

		$op_far = $this->get_op_forms($op_id);

		// tsykkel yle koigi outputi ridade ja cellide
		for ($row = 0; $row < $this->output["rows"]; $row++)
		{
			$html="";
			for ($col = 0; $col < $this->output["cols"]; $col++)
			{
				if (!($arr = $this->get_spans($row, $col, $this->output["map"], $this->output["rows"], $this->output["cols"])))
				{
					continue;
				}

				$rrow = (int)$arr["r_row"];
				$rcol = (int)$arr["r_col"];
				$op_cell = $this->output[$rrow][$rcol];
				$style_id = $op_cell["style"];
				if ($style_id == 0)
				{
					// now. find the defult style based on the row / col default styles.
					// start with cols
					if ($col < $fcol_cnt && $fcol_style)
					{
						$style_id = $fcol_style;
					}
					else
					if ($row < $frow_cnt && $frow_style)
					{
						$style_id = $frow_style;
					}
				}

				$chtml= "";
				for ($i=0; $i < $op_cell["el_count"]; $i++)
				{
					// load the element from output
					$el=get_instance("formgen/form_entry_element");
					$el->load($op_cell["elements"][$i],&$this,$rcol,$rrow);

					// if the element is linked, then fake the elements entry
					if ($op_cell["elements"][$i]["linked_element"] && $op_far[$op_cell["elements"][$i]["linked_form"]] == $op_cell["elements"][$i]["linked_form"])
					{
						// now fake the correct id
						// ok, we have to make a backup of $this->entry - because we just might overwrite important entries in it
						// if the element id's in the output are the same as the element id's in the linked form

						// damn, we have to set relation form and element from the original form in the element
						// - the output does not contain them :(
						if ($el->arr["subtype"] == "relation")
						{
							$opelform =& $this->cache_get_form_instance($op_cell["elements"][$i]["linked_form"]);
							$opelformel = $opelform->get_element_by_id($op_cell["elements"][$i]["linked_element"]);
							$el->arr["rel_form"] = $opelformel->arr["rel_form"];
							$el->arr["rel_element"] = $opelformel->arr["rel_element"];
						}

						$_entry = array();
						$_entry[$el->get_id()] = $this->entry[$op_cell["elements"][$i]["linked_element"]];
						$el->set_entry($_entry,$this->entry_id);
					}

					// now do the element show controller check
					$show = true;
					$shcs = $el->get_show_controllers();
					foreach($shcs as $ctlid)
					{
						if (($res = $this->controller_instance->do_check($ctlid, $el->get_controller_value(), &$this, $el)) !== true)
						{
							$show = false;
						}
					}

					if ($show)
					{
						if ($el)
						{
							// this must be here, because radiobuttons use the element id to see if they are checked
							$el->id = $op_cell["elements"][$i]["linked_element"];
							$chtml.= $el->gen_show_html();
						}
					}
				}

				if (isset($no_html) && $no_html)
				{
					$html.=$chtml." ";
				}
				else
				{
					if ($style_id != 0)
					{
						$html.= $t_style->get_cell_begin_str($style_id,$arr["colspan"],$arr["rowspan"]).$chtml.$t_style->get_cell_end_str($style_id)."</td>";
					}
					else
					{
						$html.= "<td colspan=\"".$arr["colspan"]."\" rowspan=\"".$arr["rowspan"]."\">".$chtml."</td>";
					}
				}
			}
			$this->vars(array("COL" => $html));
			$this->parse("LINE");
		}

		// uurime v2lja outputi tabeli stiili ja kasutame seda
		if ($this->output["table_style"])
		{
			$this->vars(array(
				"tablestring" => $t_style->get_table_string($this->output["table_style"])
			));
		}
		$retval = $this->parse();
		global $type;
		if ($type == "popup")
		{
			if ($this->output["lang_close_button_text"][aw_global_get("lang_id")] != "")
			{
				$retval .= "<form><input type='button' onClick='javascript:window.close()' value='".$this->output["lang_close_button_text"][aw_global_get("lang_id")]."'></form>";
			}
		};

		if ($GLOBALS["format"] == "pdf")
		{
			$co = get_instance("core/converters/html2pdf");
			$pdf = $co->convert(array("source" => $retval));
			header("Content-type: application/pdf");
			die($pdf);
		}
		return $retval;
	}

	/** Merge the cell above cell($row,$col) in form $id

		@attrib name=exp_cell_up params=name default="0"

		@param id required acl="edit;view"
		@param row required
		@param col required

		@returns


		@comment

	**/
	function exp_cell_up($arr)
	{
		extract($arr);
		$this->load($id);
		$this->map_exp_up($this->arr["rows"], $this->arr["cols"], &$this->arr["map"],$row,$col);
		$this->save();
		$orb = $this->mk_orb("change", array("id" => $this->id));
		header("Location: $orb");
		return $orb;
	}

	/** Merges the cell ($row,$col) in form $id with the cell immediately below it

		@attrib name=exp_cell_down params=name default="0"

		@param id required acl="edit;view"
		@param row required
		@param col required

		@returns


		@comment

	**/
	function exp_cell_down($arr)
	{
		extract($arr);
		$this->if_init($id);
		$this->map_exp_down($this->arr["rows"], $this->arr["cols"], &$this->arr["map"],$row,$col);
		$this->save();
		$orb = $this->mk_orb("change", array("id" => $this->id));
		header("Location: $orb");
		return $orb;
	}

	/** Expand cell at $row,$col with the cell to it's left, in form $id

		@attrib name=exp_cell_left params=name default="0"

		@param id required acl="edit;view"
		@param row required
		@param col required

		@returns


		@comment

	**/
	function exp_cell_left($arr)
	{
		extract($arr);
		$this->load($id);
		$this->map_exp_left($this->arr["rows"], $this->arr["cols"], &$this->arr["map"],$row,$col);
		$this->save();
		$orb = $this->mk_orb("change", array("id" => $this->id));
		header("Location: $orb");
		return $orb;
	}

	/** Merges the cell ($row, $col) in form $id with the cell right to it

		@attrib name=exp_cell_right params=name default="0"

		@param id required acl="edit;view"
		@param row required
		@param col required

		@returns


		@comment

	**/
	function exp_cell_right($arr)
	{
		extract($arr);
		$this->load($id);
		$this->map_exp_right($this->arr["rows"], $this->arr["cols"], &$this->arr["map"],$row,$col);
		$this->save();
		$orb = $this->mk_orb("change", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	function get_location()
	{
		if ($this->type == FTYPE_SEARCH)
		{
			return "search_results";
		}

		switch($this->arr["after_submit"])
		{
			case 1:
				return "edit";
			case 3:
				return "redirect";
			case 4:
				return "show_op";
		}
	}

	/** Splits the cell ($row, $col) in form $id vertically

		@attrib name=split_cell_ver params=name default="0"

		@param id required acl="edit;view"
		@param row required
		@param col required

		@returns


		@comment

	**/
	function split_cell_ver($arr)
	{
		extract($arr);
		$this->load($id);
		$this->map_split_ver($this->arr["rows"], $this->arr["cols"], &$this->arr["map"],$row,$col);
		$this->save();
		$orb = $this->mk_orb("change", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** splits the cell at ($row, $col) in form $id vertically

		@attrib name=split_cell_hor params=name default="0"

		@param id required acl="edit;view"
		@param row required
		@param col required

		@returns


		@comment

	**/
	function split_cell_hor($arr)
	{
		extract($arr);
		$this->load($id);
		$this->map_split_hor($this->arr["rows"], $this->arr["cols"], &$this->arr["map"],$row,$col);
		$this->save();
		$orb = $this->mk_orb("change", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** generates the form for selecting among which forms to search for search form $id

		@attrib name=sel_search params=name default="0"

		@param id required acl="edit;view"
		@param page optional

		@returns


		@comment

	**/
	function gen_search_sel($arr)
	{
		extract($arr);
		$this->if_init($id, "search_sel.tpl", "Vali otsitavad formid");

		$this->vars(array("LINE" => "")); $cnt=0;

		$ops = $this->get_op_list();

		$per_page = 10;

		$total = $this->db_fetch_field("SELECT count(oid) as cnt FROM objects LEFT JOIN forms ON forms.id = objects.oid WHERE status != 0 AND class_id = ".CL_FORM." AND forms.type = ".FTYPE_ENTRY,"cnt");
		$pages = $total / $per_page;
		for ($i=0; $i < $pages; $i++)
		{
			$this->vars(array(
				"from" => ($i*$per_page),
				"to" => min(($i+1)*$per_page, $total),
				"pageurl" => $this->mk_my_orb("sel_search", array("id" => $id, "page" => $i))
			));
			if ($i == $page)
			{
				$pp.=$this->parse("SEL_PAGE");
			}
			else
			{
				$pp.=$this->parse("PAGE");
			}
		}
		$this->vars(array(
			"PAGE" => $pp,
			"SEL_PAGE" => ""
		));

		$this->db_query("SELECT oid,parent,name,comment FROM objects LEFT JOIN forms ON forms.id = objects.oid WHERE status != 0 AND class_id = ".CL_FORM." AND forms.type = ".FTYPE_ENTRY." LIMIT ".($page*$per_page).",$per_page");
		while($row = $this->db_next())
		{
			$tar = array(0 => "");
			if (is_array($ops[$row["oid"]]))
			{
				foreach($ops[$row["oid"]] as $opid => $opname)
				{
					$tar[$opid] = $opname;
				}
			}
			$sel = $this->arr["search_from"][$row["oid"]] == 1 ? $this->arr["search_outputs"][$row["oid"]] : 0;
			$this->vars(array(
				"form_name"	=> $row["name"],
				"form_comment" => $row["comment"],
				"form_location" => $row["parent"],
				"form_change" => $this->mk_my_orb("change", array("id" => $row["oid"])),
				"form_id" => $row["oid"],
				"row"	=> $cnt,
				"checked" => checked($this->arr["search_from"][$row["oid"]] == 1),
				"prev" => $this->arr["search_from"][$row["oid"]],
				"ops" => $this->picker($sel,$tar)
			));
			$this->parse("LINE");
			$cnt+=2;
		}

		$this->vars(array(
			"reforb"	=> $this->mk_reforb("save_search_sel", array("id" => $this->id,"page" => $page)),
			"formsonly" => checked($this->arr["formsonly"] == 1),
			"chains" => $this->picker($this->arr["se_chain"], $this->get_chains(true))
		));

		//////////////////////////////////////////////////
		// new version starts here

		// we let the user pick one - either the form searches from a form chain
		// or it searches from several forms
		// if the user picks several forms, they will be checked, whether it is possible to bind them all together via relation elements
		// if not, the user is notified of the error and the selection will not be saved
		// after successfully selecting some forms the user will be able to select the output to use when showing the search results
		//
		// if the user picks that the form searches from a chain
		// he can select the chain and after saving also select the output with what the data will be shown

		$status_msg = aw_global_get("status_msg");
		$form_selsearch_error = aw_global_get("form_selsearch_error");
		$form_selsearch_data  = aw_global_get("form_selsearch_data");

		if ($form_selsearch_error == 1)
		{
			$this->arr["search_type"] = $form_selsearch_data["search_type"];
			$this->arr["search_forms"] = $form_selsearch_data["search_forms"];
			$this->arr["search_form_op"] = $form_selsearch_data["search_form_op"];
			$this->arr["search_chain"] = $form_selsearch_data["search_chain"];
			$this->arr["search_chain_op"] = $form_selsearch_data["search_chain_op"];
			$this->vars(array(
				"status_msg" => $status_msg
			));
			aw_session_del("form_selsearch_error");
			aw_session_del("form_selsearch_data");
			aw_session_del("status_msg");
		}

		$this->vars(array(
			"use_new_search" => checked($this->arr["new_search_engine"]),
			"forms_search" => checked($this->arr["search_type"] == "forms"),
			"chain_search" => checked($this->arr["search_type"] == "chain"),
			"forms" => $this->multiple_option_list($this->arr["search_forms"],$this->get_flist(array("type" => FTYPE_ENTRY))),
			"nchains" => $this->picker($this->arr["search_chain"],$this->get_chains(true)),
			"show_csv_link" => checked($this->arr["search_show_csv_link"]),
			"show_s_res_as_forms" => checked($this->arr["show_s_res_as_forms"])
		));

		$cs = $cs2= "";

		if ($this->arr["search_chain"])
		{
			$forms = $this->get_forms_for_chain($this->arr["search_chain"]);
			$_ops = array(0 => "");
			foreach($ops as $o_fid => $ar)
			{
				if (in_array($o_fid,$forms) && is_array($ar))
				{
					foreach($ar as $_opid => $_opname)
					{
						$_ops[$_opid] = $_opname;
					}
				}
			}

			$this->vars(array(
				"chain_op" => $this->picker($this->arr["search_chain_op"],$_ops),
				"chain_repeater" => $this->picker($this->arr["search_chain_repeater"], array("0" => "")+$this->get_forms_for_chain($this->arr["search_chain"], true))
			));

			$cs = $this->parse("CHAIN_SEL");
			$cs2 = $this->parse("CHAIN_SEL2");
		}

		$fs = "";
		if (count($this->arr["search_forms"]) > 0)
		{
			$_ops = array(0 => "");
			foreach($ops as $o_fid => $ar)
			{
				if (in_array($o_fid,$this->arr["search_forms"]) && is_array($ar))
				{
					foreach($ar as $_opid => $_opname)
					{
						$_ops[$_opid] = $_opname;
					}
				}
			}

			$this->vars(array(
				"form_op" => $this->picker($this->arr["search_form_op"],$_ops)
			));
			$fs = $this->parse("FORM_SEL");
		}
		$this->vars(array(
			"FORM_SEL" => $fs,
			"CHAIN_SEL" => $cs,
			"CHAIN_SEL2" => $cs2,
			"reforb"	=> $this->mk_reforb("save_search_sel", array("id" => $this->id,"page" => $page)),
		));

		return $this->do_menu_return();
	}

	/** saves the forms from which to search for search form $id

		@attrib name=save_search_sel params=name default="0"


		@returns


		@comment

	**/
	function save_search_sel(&$arr)
	{
		extract($arr);
		$this->load($id);

		if (is_array($inpage))
		{
			foreach($inpage as $ifid => $v)
			{
				$var = "ch_".$ifid;
				$this->arr["search_from"][$ifid] = $$var;
				$var = "sel_".$ifid;
				$this->arr["search_outputs"][$ifid] = $$var;
			}

			// kas ocime aint formist v6i yritame p2rga leida
			$this->arr["formsonly"] = $formsonly;

			$this->arr["se_chain"] = $se_chain;
		}

		$this->save();

		////////////////////////////////////////////
		// new version here

		$this->load($id);

		$this->arr["new_search_engine"] = $use_new_search;
		$this->arr["search_type"] = $search_from;
		$this->arr["search_forms"] = array();
		if (is_array($forms))
		{
			foreach($forms as $fid)
			{
				$this->arr["search_forms"][$fid] = $fid;
			}
		}

		$this->arr["search_form_op"] = $form_op;

		$this->arr["search_chain"] = $search_chain;
		$this->arr["search_chain_op"] = $chain_op;
		$this->arr["search_chain_repeater"] = $chain_repeater;
		$this->arr["search_show_csv_link"] = $show_csv_link;
		$this->arr["show_s_res_as_forms"] = $show_s_res_as_forms;

		if ($this->arr["search_type"] == "chain")
		{
			$this->arr["search_forms"] = $this->get_forms_for_chain($this->arr["search_chain"]);
		}

		// here we must check if the users selection is valid - whether all the selected forms are connected via
		// relation elements and also remember the form where one should start in order to be able to traverse
		// all the selected forms in the most efficient manner
		// if the users selection is not valid, save the data in the session and give an error message

		if (($msg = $this->check_search_target_form_relations()) != "ok")
		{
			$form_selsearch_data = array();
			$form_selsearch_data["search_type"] = $this->arr["search_type"];
			$form_selsearch_data["search_forms"] = $this->arr["search_forms"];
			$form_selsearch_data["search_form_op"] = $this->arr["search_form_op"];
			$form_selsearch_data["search_chain"] = $this->arr["search_chain"];
			$form_selsearch_data["search_chain_op"] = $this->arr["search_chain_op"];
			$form_selsearch_data["search_chain_repeater"] = $this->arr["search_chain_repeater"];
			$status_msg = $msg;
			aw_session_set("form_selsearch_data", $form_selsearch_data);
			aw_session_set("status_msg", $status_msg);
			aw_session_set("form_selsearch_error",1);
		}
		else
		{
			$this->save();
		}
		return $this->mk_orb("sel_search", array("id" => $id,"page" => $page));
	}

	////
	// !this checks if the search from forms settings are all nice and clean - if all the forms can be reached from
	// any other and sets the optimal form to start from
	function check_search_target_form_relations()
	{
		if ($this->arr["search_type"] == "forms")
		{
			if (!$this->arr["search_form_op"])
			{
				return "No output selected for forms!";
			}

			$found = false;
			foreach($this->arr["search_forms"] as $fid)
			{
				$this->req_check_stf_relations_map = array();
				$this->req_check_stf_relations($fid);
				// now we must check the map if all the selected forms are covered in it and there also must be no
				// "holes" in it - meaning that you must be able to access all the selected forms startig from one of them
				// and touching all of them, but not any others - they might have relations to some other forms
				// but you must not have to use those relations to access all of the search forms
				$all_filled = true;
				foreach($this->arr["search_forms"] as $_fid)
				{
					if ($this->req_check_stf_relations_map[$_fid] != $_fid)
					{
						$all_filled = false;
					}
				}
				if ($all_filled)
				{
					$found = true;
					$this->arr["start_search_relations_from"] = $fid;
				}
			}

			if (!$found)
			{
				$this->arr["start_search_relations_from"] = 0;
				return "Not all forms are connected via relations!";
			}
		}
		else
		{
			if (!$this->arr["search_chain_op"])
			{
				$this->arr["start_search_relations_from"] = 0;
				return "No output selected for chain!";
			}
			else
			{
				// we must find the first form of the chain and use that as the form to start relations from
				$dat = $this->get_forms_for_chain($this->arr["search_chain"]);
				reset($dat);
				list(,$_fid) = each($dat);
				$this->arr["start_search_relations_from"] = $_fid;
				if ($this->arr["search_chain_repeater"])
				{
					$this->arr["start_search_relations_from"] = $this->arr["search_chain_repeater"];
				}
			}
		}
		return "ok";
	}

	////
	// !this recurses through all the relations and writes the info in a map - so we can check if it managed to touch
	// all the selected forms or not.
	function req_check_stf_relations($fid)
	{
		// now here we must try to figure out if the form is connected to any others - via relation elements

		$this->req_check_stf_relations_map[$fid] = $fid;

		$f = get_instance(CL_FORM);
		$f->load($fid);

		$rels = $f->get_element_by_type("listbox","relation",true);
		foreach($rels as $el)
		{
			// if the related form is selected as a search form, then follow the relation, otherwise don't
			$rel_f = $el->get_related_form();
			// also if we have already visited that form, make sure we don't end up in a loop
			if ($this->arr["search_forms"][$rel_f] == $rel_f && $this->req_check_stf_relations_map[$rel_f] != $rel_f)
			{
				$this->req_check_stf_relations($rel_f);
			}
		}
	}

	////
	// !searches and displays the results
	// paramters:
	//	$id - search form id - if not specified, assumes the form is loaded
	//	$entry_id - search form entry id - if not specified, assumes loaded
	//	$section - the active section
	//  $no_form_tags - the <form> </form> tags will be omitted
	//	restrict_search_el - array of element id's that should be added to the search
	//	restrict_search_val - array of element values that should be added to the search
	//	use_table - show results with table use_table instead of the default
	//	search_form - use the specified form instead of the default ($id)
	function new_do_search($arr)
	{
		extract($arr);

		// $this->arr["search_chain"] on selle chaini id, mille kalendreid ma arvestama pean
		// samas kuulub seal iga kalender konkreetse prja elementvormi klge, so I have
		// to figure out which one it is, to find the calendars I required

		if ($search_form)
		{
			$id = $search_form;
			$entry_id = 0;
		}

		if ($id)
		{
			$this->load($id);
		}
		if ($entry_id)
		{
			$this->load_entry($entry_id);
			$this->_do_value_controllers();
		}

		if (is_array($restrict_search_el))
		{
			// alter the loaded search entry with the data
			$l2r = $this->get_linked2real_element_array();

			foreach($restrict_search_el as $idx => $rel)
			{
				$el =& $this->get_element_by_id($l2r[$rel]);

				if (is_object($el) && is_object($this->arr["contents"][$el->get_row()][$el->get_col()]))
				{
					$this->arr["contents"][$el->get_row()][$el->get_col()]->set_element_entry($l2r[$rel],$restrict_search_val[$idx],true);
				}
			}
		}

		$show_form = get_instance(CL_FORM);	// if showing results as outputs, this will be used
		$form_table = get_instance(CL_FORM_TABLE); // if showing results as a form_table, this will be used

		$used_els = array();
		$group_els = array();

		// now, if the results are to be shown in a table, load the table and ask it for the necessary elements
		// if it is not to be shown in a table - but as a list of outputs - or whatever - assume all elements are necessary
		if ($this->arr["show_table"])
		{
			if (!$this->arr["table"])
			{
				$this->raise_error(ERR_FG_NOTABLE,sprintf(t("No table selected for showing search results for form %s!"), $this->id),true);
			}
			if (!$use_table)
			{
				$use_table = $this->arr["table"];
			}

			$form_table->start_table($use_table, $this->arr["start_search_relations_from"]);
			$form_table->set_opt("current_search_form", $this->id);
			$form_table->current_search_form_inst =& $this;

			$used_els = $form_table->get_used_elements();
//			echo "used_els = <pre>", print_r($used_els),"</pre> <br />";
			$group_els = $form_table->get_group_by_elements();
//			echo "group_els = <pre>", var_dump($group_els),"</pre> <br />";

			$group_collect_els = $form_table->get_group_by_collect_elements();
//			echo "group_cllect_els = <pre>", var_dump($group_collect_els),"</pre> <br />";
		}
		else
		{
			// here we need to get the elements used in the form_output that will show the entries
			$show_form->load($this->arr["start_search_relations_from"]);
			$show_form->load_output($this->get_search_output());
			$used_els = $this->get_op_linked_elements();
		}

		if (!$output_id)
		{
			// execute it and show the results in the desired form
			if ($this->arr["search_type"] == "forms")
			{
				$op = $this->arr["search_form_op"];
			}
			else
			{
				$op = $this->arr["search_chain_op"];
			}
		}
		else
		{
			$op = $output_id;
		}

		if ($this->arr["search_chain"])
		{
			$fc = get_instance(CL_FORM_CHAIN);
			$chain = $fc->load_chain($this->arr["search_chain"]);
			// check whether the chain and the form both use a calendar
			$has_calendar = $chain["flags"] & OBJ_HAS_CALENDAR;
			if (!($this->subtype & FSUBTYPE_CAL_SEARCH))
			{
				$has_calendar = false;
			};
			if ($has_calendar)
			{
				$has_vacancies = true;
				$fcal = get_instance("formgen/form_calendar");
				$cal_id = $this->arr["search_chain"];
				$contr = $fc->chain["cal_controller"];
				/*
				print "loading calendar for " . $this->arr["search_chain"] . "<br />";
				print "controller is $contr<br />";
				*/
				$q = "SELECT * FROM calendar2forms WHERE cal_id = '$cal_id'";
				$this->db_query($q);
				$cf = $this->db_next();
				// $row now contains the record which shows us the characteristics
				// of the event entry form used
				// if the chain has no
				if (!$cf)
				{
					$has_vacancies = false;
				};
				$els = $fcal->get_form_elements(array("id" => $this->id));
				// figure out the start and end elements
				$start_el = $end_el = 0;
				$count_el = 0;
				foreach($els as $key => $val)
				{
					if ($val["type"] == "date")
					{
						if ($start_el == 0)
						{
							$start_el = $val["id"];
						}
						else
						{
							$end_el = $val["id"];
						}
					}

					if ( ($val["type"] == "textbox") && ($val["subtype"] == "count") )
					{
						$count_el = $val["id"];
					};
				};

				$start = $this->entry[$start_el];
				$end = $this->entry[$end_el];
				$count = (int)$this->entry[$count_el];


				// if no amount was specified default to 1
				// and .. why the hell are you searching anyway if you dont want any rooms?
				if ($count == 0)
				{
					$count = 1;
				};

			};
		}
		// if we use a calendar, retrieve the data first without grouping
		// so that we can perform our magic on it.
		if ($has_calendar)
		{
			$sql = $this->get_search_query(array(
				"used_els" => $used_els,
			));
//			print $sql;
//			print "<br />";
			$this->db_query($sql,false);
		//	print "SQL = $sql<br />";
		//	flush();
			list(,$__gr) = each($used_els);
			if (is_array($__gr))
			{
				list(,$_gr) = each($__gr);
			};
			$groups = array();
			$_key = "el_" . $_gr;

			while($row = $this->db_next())
			{
				$vacs = $fcal->check_vacancies(array(
					"cal_id" => $this->arr["search_chain"],
					"contr" => $contr,
					"entry_id" => $row["entry_id"],
					"id" => $cf["form_id"],
					"eid" => $row["chain_entry_id"],
					"start" => $start,
					"end" => $end - 1,
					"req_items" => $count,
				));
				//$vacs = 0;
				if (!$groups[$row[$_key]] && ($vacs > -1))
				{
					$groups[$row[$_key]] = $vacs;
				}
				else
				if (($groups[$row[$_key]] < $vacs) && ($vacs > -1))
				{
					$groups[$row[$_key]] = $vacs;
				}

			};
		};

		// now get the search query
//		echo "getting search query , used_els = <pre>",var_dump($used_els) ,"</pre><br />";

		$sql = $this->get_search_query(array(
			"used_els" => $used_els,
			"group_els" => $group_els,
			"group_collect_els" => $group_collect_els,
			"sort_by" => $this->arr["sort_op_by"],
			"sort_order" => $this->arr["sort_op_order"]
		));
//		echo "sql = $sql <br />";
		$result = "";
		if (strpos($sql, "WHERE") === false)
		{
			$sql .= " WHERE 1=0";
		}
		$this->db_query($sql,false);
		$cur_row = 0;
		$total_rows = $this->num_rows();
		if ($this->arr["show_table"] && !$this->arr["show_s_res_as_forms"])
		{
			$form_table->set_num_rows($this->num_rows());
		}
		while ($row = $this->db_next())
		{
			$this->save_handle();
			/*
			print "<pre>";
			print_r($row);
			print "</pre>";
			*/
			if ($this->arr["show_s_res_as_forms"])
			{
				$show_form->reset();
				$show_form->_init_vars();
				$show_form->load_entry($row["entry_id"]);
				$result.=$show_form->gen_preview(array(
					"id" => $show_form->id,
					"entry_id" => $row["entry_id"],
					"no_load_entry" => true,
					"tpl" => "show_noform.tpl",
					"prefix" => "sr_".$row["entry_id"]."_",
					"no_submit" => !($cur_row == ($total_rows-1))
				));
				$this->s_res_as_forms_eids[] = $row["entry_id"];
				$this->s_res_as_forms_fid = $show_form->id;
			}
			else
			if ($this->arr["show_table"])
			{
				if (isset($row["chain_entry_id"]) && $row["chain_entry_id"])
				{
					$this->save_handle();
					$cid = $this->get_chain_for_chain_entry($row["chain_entry_id"]);
					$this->restore_handle();
				};

				//print "processing entry $row[entry_id]<br />";
				if ($has_calendar)
				{
					$vacs = $groups[$row[$_key]];
					//print "key = $_key<br />";
					//print "vacs = $vacs<br />";
					if ($vacs > -1.1)
					{
						//print "added line<br />";
						$form_table->row_data($row,$this->arr["start_search_relations_from"],$section,$op,$cid,$row["chain_entry_id"]);
					}
					else
					{
						dbg::p("no show because no vacancies <br />");
					}
				}
				else
				{
					if (not(isset($cid)))
					{
						$cid = 0;
					};

					if (isset($row["chain_entry_id"]))
					{
						$rcid = $row["chain_entry_id"];
					}
					else
					{
						$rcid = 0;
					};

					$form_table->row_data($row,$this->arr["start_search_relations_from"],$section,$op,$cid,$rcid);
					$row_count++;
				}
			}
			else
			{
				// this should load the entry without doing any db queries - from the result row data
				// hm - but - maybe we shouldn't do this - maybe the form also has some other relations that are
				// not selected as search forms - so their data will not get loaded. damn. ok, so we just do load_entry for the form
				$show_form->reset();
				$show_form->_init_vars();
				$show_form->load_entry($row["entry_id"]);
				$result.=$show_form->show(array("id" => $show_form->id,"entry_id" => $row["entry_id"], "op_id" => $show_form->output_id,"no_load_entry" => true, "no_load_op" => true));
			}
			$cur_row++;
			$this->restore_handle();
		}

		// now if we are showing table, finish the table
		if ($this->arr["show_table"] && !$this->arr["show_s_res_as_forms"])
		{
			$result = $form_table->finalize_table(array("no_form_tags" => $no_form_tags));
			if ($this->arr["search_show_csv_link"] == 1)
			{
				$result = "Leiti $row_count rida. <a href='".aw_url_change_var("get_csv_file", 1)."'>CSV</a><br />".$result;
			}

			if ($GLOBALS["get_csv_file"] == 1)
			{
				header("Content-type: text/csv");
				header("Content-disposition: inline; filename=tulemused.csv;");
				die($form_table->t->get_csv_file(";"));
			}
		}
		return $result;
	}

	function get_search_output()
	{
		if ($this->arr["search_type"] == "forms")
		{
			return $this->arr["search_form_op"];
		}
		return $this->arr["search_chain_op"];
	}

	////
	// !does the actual searching part and returns
	// an array, that has one entry for each form selected as a search target
	// and that entry is an array of matching entries for that form
	// parent(int) - millise parenti alt entrysid otsida
	// $restrict_el - if set, the element in the search form that is linked to this element
	// will be set to value $restrict_val
	function search($entry_id = 0,$parent = 0,$search_el = "",$search_val = "",$restrict_search_el = 0, $restrict_search_val = "")
	{
		if ($this->arr["new_search_engine"] == 1)
		{
			return $this->new_search(array("entry_id" => $entry_id));
		}

		// laeb tidetud vormi andmed sisse
		if ($search_el != "" && $search_val != "")
		{
			$this->entry = array();
			$this->entry[$search_el] = $search_val;
			$this->read_entry_from_array($entry_id);
			$word_search = false;
		}
		else
		{
			if ($entry_id)
			{
				$this->load_entry($entry_id);
			}
			$word_search = true;
			// TODO: restrict search here
		}

		if (is_array($restrict_search_el))
		{
			// alter the loaded search entry with the data
			$l2r = $this->get_linked2real_element_array();

			foreach($restrict_search_el as $idx => $rel)
			{
				$el =& $this->get_element_by_id($l2r[$rel]);
				$this->arr["contents"][$el->get_row()][$el->get_col()]->set_element_entry($l2r[$rel],$restrict_search_val[$idx]);
			}
		}

		// gather all the elements of this form in an array
		$els = array();
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$this->arr["contents"]["$row"]["$col"]->get_els(&$els);
			};
		};

		$ret = array();

		if (!is_array($this->arr["search_from"]))
		{
			$this->raise_error(ERR_FG_NOTARGETS,sprintf(t("form->search(%s): no forms selected as search targets!"), $entry_id),true);
		}

		reset($this->arr["search_from"]);
		$this->cached_results = array();

		if ($this->arr["formsonly"] != 1)
		{
			if ($this->arr["se_chain"])
			{
				$is_chain = true;
				$chain_id = $this->arr["se_chain"];
			}
			else
			{
				// leiame kas see otsing on p2rja kohta.
				$fidstr = join(",", map2("%s",$this->arr["search_from"]));
				if ($fidstr != "")
				{
					$this->db_query("SELECT distinct(chain_id) as chain_id FROM form2chain WHERE form_id IN ($fidstr)");
					while ($row = $this->db_next())
					{
						$is_chain = true;
						$chain_id = $row["chain_id"];
					}
				}
			}
		}

		if ($is_chain)
		{
			$this->chain_id = $chain_id;
			// loop through all the forms that are selected as search targets
			$ar = $this->get_forms_for_chain($chain_id);
			reset($ar);
			list($mid,$v) = each($ar);
			$this->search_form = $mid;
			// let's create the query a bit differently - join only the tables that are actually being searched from

			$query = "";
			$forms_in_q = array(); // here we store all the ids of the forms that are actually used in the query and thus must be linked
			$ch_q = array();
			reset($els);
			// loop through all the elements of this form
			while( list(,$el) = each($els))
			{
				if ($el->arr["linked_form"] && $el->arr["linked_element"])
				{
					if (trim($el->get_value()) != "")
					{
						if ($el->get_type() == "multiple")
						{
							$query.=" AND (";
							$ec=explode(",",$el->entry);
							reset($ec);
							$qpts = array();
							while (list(, $v) = each($ec))
							{
								$qpts[] =" form_".$el->arr["linked_form"]."_entries.ev_".$el->arr["linked_element"]." like '%".$el->arr["multiple_items"][$v]."%' ";
							}

							$query.= join("OR",$qpts).")";
						}
						else
						if ($el->get_type() == "checkbox")
						{
							//checkboxidest ocime aint siis kui nad on tshekitud
							if ($el->get_value(true) == 1)
							{
								// grupeerime p2ringus nii et checkboxi gruppide vahel on AND ja grupi sees OR
								$ch_q[$el->get_ch_grp()][] = " form_".$el->arr["linked_form"]."_entries.ev_".$el->arr["linked_element"]." like '%".$el->get_value()."%' ";
							}
						}
						else
						if ($el->get_type() == "radiobutton")
						{
							if ($el->get_value(true) == 1)
							{
								$query.="AND (form_".$el->arr["linked_form"]."_entries.ev_".$el->arr["linked_element"]." LIKE '%".$el->get_value()."%')";
							}
						}
						else
						if ($el->get_type() == "date")
						{
							if ($el->get_subtype() == "from")
							{
								$query.= "AND (form_".$el->arr["linked_form"]."_entries.el_".$el->arr["linked_element"]." >= ".$this->entry[$el->get_id()].")";
							}
							else
							if ($el->get_subtype() == "to")
							{
								$query.= "AND (form_".$el->arr["linked_form"]."_entries.el_".$el->arr["linked_element"]." <= ".$this->entry[$el->get_id()].")";
							}
							else
							{
								$query.= "AND (form_".$el->arr["linked_form"]."_entries.el_".$el->arr["linked_element"]." = ".$this->entry[$el->get_id()].")";
							}
						}
						else
						{
							$value = $el->get_value();
							$elname = sprintf("form_%s_entries.ev_%s",
											$el->arr["linked_form"],
											$el->arr["linked_element"]);
							// now split it at the spaces
/*							if ($word_search)
							{
								if (preg_match("/\"(.*)\"/",$value,$matches))
								{
									$qstr = " $elname LIKE '%$matches[1]%' ";
								}
								else
								{
									$pieces = explode(" ",$value);
									if (is_array($pieces))
									{
										$qstr = join (" OR ",map("$elname LIKE '%%%s%%'",$pieces));
									}
									else
									{
										$qstr = " $elname LIKE '%$value%' ";
									};
								};
								$query.= "AND ($qstr)";
							}
							else
							{*/
								$query.= "AND (form_".$el->arr["linked_form"]."_entries.ev_".$el->arr["linked_element"]." like '%".$el->get_value()."%')";
//							}
						}
						if ($el->arr["linked_form"] != $mid)
						{
							$forms_in_q[$el->arr["linked_form"]] = $el->arr["linked_form"];
						}
					}
				}
			}

			// k2ime l2bi erinevad checkboxide grupid ja paneme gruppide vahele AND ja checkboxide vahele OR
			foreach($ch_q as $chgrp => $ch_ar)
			{
				$chqs = join(" OR ", $ch_ar);
				if ($chqs !="")
				{
					$query.=" AND ($chqs)";
				}
			}
			// now compose the complete query
			if ($query == "")
			{
				// return all the chain entries for the first form in the chain
				$query = "SELECT distinct(chain_id) as oid FROM form_".$mid."_entries LEFT JOIN objects ON objects.oid = form_".$mid."_entries.id LEFT JOIN objects AS ch_objs ON ch_objs.oid = form_".$mid."_entries.chain_id WHERE objects.status != 0 AND form_".$mid."_entries.chain_id IS NOT NULL AND ch_objs.status != 0";
			}
			else
			{
				// join all the necessary forms together
				$query = "SELECT distinct(form_".$mid."_entries.chain_id) as oid FROM form_".$mid."_entries LEFT JOIN objects ON objects.oid = form_".$mid."_entries.id LEFT JOIN objects AS ch_objs ON ch_objs.oid = form_".$mid."_entries.chain_id ".join(" ",map2("LEFT JOIN form_%s_entries ON form_%s_entries.chain_id = form_".$mid."_entries.chain_id",$forms_in_q))." WHERE objects.status != 0 AND form_".$mid."_entries.chain_id IS NOT NULL AND ch_objs.status != 0 ".$query;
			}

			$this->main_search_form = $mid;
			$matches = array();
			dbg::p("form_search_q1 = $query  <br />\n");
//		flush();
			$this->db_query($query);
//		echo "q finished \n <br />";
//		flush();

			while ($row = $this->db_next())
			{
				$matches[] = $row["oid"];
			}

			$this->form_search_only = false;
			$ret = $matches;
		}
		else
		{
			// loop through all the forms that are selected as search targets
			while (list($id,$v) = each($this->arr["search_from"]))
			{
				if ($v == 1)		// search only selected forms
				{
					break;
				}
			}

			if (!$id)
			{
				$this->raise_error(ERR_FG_NOTARGETS,t("No forms selected as search targets!"));
			}

			$this->search_form = $id;
			// create the sql that searches from this form's entries
			$query="SELECT * FROM form_".$id."_entries LEFT JOIN objects ON objects.oid = form_".$id."_entries.id WHERE objects.status !=0 AND objects.lang_id = ".aw_global_get("lang_id")." " ;
			if (is_array($parent))
			{
				$query .= sprintf(" AND objects.parent IN (%s)",join(",",$parent));
			}

			// loop through all the elements of this form
			$ch_q = array();
			reset($els);
			while( list(,$el) = each($els))
			{
				if ($el->arr["linked_form"] == $id)	// and use only the elements that are members of the current form in the query
				{
					// oh la la
					if ($el->get_type() == "multiple")
					{
						if ($el->entry != "")
						{
							$query.=" AND (";
							$ec=explode(",",$el->entry);
							reset($ec);
							$qpts = array();
							while (list(, $v) = each($ec))
							{
								$qpts[] =" form_".$el->arr["linked_form"]."_entries.ev_".$el->arr["linked_element"]." like '%".$el->arr["multiple_items"][$v]."%' ";
							}

							$query.= join("OR",$qpts).")";
						}
					}
					else
					if ($el->get_type() == "checkbox")
					{
						//checkboxidest ocime aint siis kui nad on tshekitud
						if ($el->get_value(true) == 1)
						{
							$ch_q[$el->get_ch_grp()][] = " form_".$el->arr["linked_form"]."_entries.ev_".$el->arr["linked_element"]." like '%".$el->get_value()."%' ";
						}
					}
					else if ($el->get_type() == "radiobutton")
					{
						if ($el->get_value(true) == 1)
						{
							$query.="AND (form_".$el->arr["linked_form"]."_entries.ev_".$el->arr["linked_element"]." LIKE '%".$el->get_value()."%')";
						}
					}
					else if ($el->get_type() == "date")
					{
						if ($el->get_subtype() == "from" && $this->entry[$el->get_id()] > 1)
						{
							$query.= "AND (form_".$el->arr["linked_form"]."_entries.el_".$el->arr["linked_element"]." >= ".$this->entry[$el->get_id()].")";
						}
						else if ($el->get_subtype() == "to" && $this->entry[$el->get_id()] > 1)
						{
							$query.= "AND (form_".$el->arr["linked_form"]."_entries.el_".$el->arr["linked_element"]." <= ".$this->entry[$el->get_id()].")";
						}
						else
						{
							if ($this->entry[$el->get_id()] > 1)
							{
								$query.= "AND (form_".$el->arr["linked_form"]."_entries.el_".$el->arr["linked_element"]." = ".$this->entry[$el->get_id()].")";
							}
						}
					}
					else if ($el->get_type() == "radiobutton")
					{
						// blah
					}
					else
					if ($el->get_type() == "listbox")
					{
						$value = $el->get_value();
						$elname = sprintf("ev_%s",$el->arr["linked_element"]);
						$qstr = " $elname LIKE '%$value%' ";
						$query.= "AND ($qstr)";
					}
					else if ($el->get_value() != "")
					{
						$value = $el->get_value();
						$elname = sprintf("ev_%s",$el->arr["linked_element"]);
						// now split it at the spaces
	/*					if ($word_search)
						{
							if (preg_match("/\"(.*)\"/",$value,$matches))
							{
								$qstr = " $elname LIKE '%$matches[1]%' ";
							}
							else
							{
								$pieces = explode(" ",$value);
								if (is_array($pieces))
								{
									$qstr = join (" OR ",map("$elname LIKE '%%%s%%'",$pieces));
								}
								else
								{
									$qstr = " $elname LIKE '%$value%' ";
								};
							};

							$query.= "AND ($qstr)";
						}
						else
						{	*/
							$query.= "AND (form_".$el->arr["linked_form"]."_entries.ev_".$el->arr["linked_element"]." like '%".$el->get_value()."%')";
//						}
					}
				}
			}


			// k2ime l2bi erinevad checkboxide grupid ja paneme gruppide vahele AND ja checkboxide vahele OR
			foreach($ch_q as $chgrp => $ch_ar)
			{
				$chqs = join(" OR ", $ch_ar);
				if ($chqs !="")
				{
					$query.=" AND ($chqs)";
				}
			}

			if ($query == "")
			{
				$query = "SELECT * FROM form_".$id."_entries";
			}

			dbg::p("form_search_q2 = $query <br />");
			$matches = array();
			$this->db_query($query);
			while ($row = $this->db_next())
			{
				$matches[] = $row["id"];
			}

			$ret = $matches;
			$this->form_search_only = true;
		}

		return $ret;
	}

	function do_search($entry_id, $output_id,$search_el,$search_val, $no_tags)
	{
		global $section,$use_table, $restrict_search_el, $restrict_search_val,$search_form;

		if ($this->arr["new_search_engine"] == 1)
		{
			// convert old settings to new ones
/*			$this->arr["new_search_engine"] = 1;
			$this->arr["search_type"] = (!$this->arr["se_chain"] ? "forms" : "chain");
			$this->arr["search_forms"] = array();
			if (is_array($this->arr["search_from"]))
			{
				foreach($this->arr["search_from"] as $_fid => $one)
				{
					if ($one == 1)
					{
						$this->arr["search_forms"][$_fid] = $_fid;
						$op = $this->arr["search_outputs"][$_fid];
					}
				}
			}
			$this->arr["search_form_op"] = $op;
			$this->arr["search_chain"] = $this->arr["se_chain"];
			$this->arr["search_chain_op"] = $op;*/
			// well, this MIGHT do the trick - we can't know for sure, because
			// the previous version allowed for ambiguous settings

			return $this->new_do_search(array(
				"entry_id" => $entry_id,
				"restrict_search_el" => $restrict_search_el,
				"restrict_search_val" => $restrict_search_val,
				"use_table" => $use_table,
				"section" => $section,
				"no_form_tags" => $no_tags,
				"search_form" => $search_form
			));
		}


		$matches = $this->search($entry_id,0,$search_el,$search_val,$restrict_search_el, $restrict_search_val);

		if (is_array($restrict_search_el))
		{
			// alter the loaded search entry with the data
			$l2r = $this->get_linked2real_element_array();

			foreach($restrict_search_el as $idx => $rel)
			{
				$el =& $this->get_element_by_id($l2r[$rel]);
				if (is_object($el) && is_object($this->arr["contents"][$el->get_row()][$el->get_col()]))
				{
					$this->arr["contents"][$el->get_row()][$el->get_col()]->set_element_entry($l2r[$rel],$restrict_search_val[$idx]);
				}
			}
		}

		if ($this->arr["show_table"])
		{
			if (!$this->arr["table"])
			{
				$this->raise_error(ERR_FG_NOTABLE,t("No table selected for showing data!"),true);
			}

			$ft = get_instance(CL_FORM_TABLE);
			// This stuff is here for the numeric element type. -->
			/*
			$els = array();
			for ($row=0; $row < $this->arr["rows"]; $row++)
			{
				for ($col=0; $col < $this->arr["cols"]; $col++)
				{
					$this->arr["contents"]["$row"]["$col"]->get_els(&$els);
				};
			};
			$stt=time();
			reset($els);
			while( list(,$el) = each($els))
			{
				if ($el->arr["subtype"]=="num")
					$ft->runtime_col_types[$el->arr["id"]]="numeric";
				if ($GLOBALS["dbg_num"]) echo("setting num for ".$el->arr["id"]."<br />");
			};
			if ($GLOBALS["dbg_num"]) {echo("<pre>");print_r($ft->runtime_col_types);echo("</pre>");};
			*/
			// <--
			if (!$use_table)
			{
				$use_table = $this->arr["table"];
			}
			$ft->start_table($use_table);

			// make an array of linked_element => this form element
			$linked_els = $this->get_linked2real_element_array();

			// this returns an array of roms each of which is an array of elements that are actually used in the table
			$used_els = $ft->get_used_elements();
			if ($this->form_search_only)
			{
				foreach($used_els as $form_id => $el_arr)
				{
					foreach($el_arr as $elid)
					{
						$q_els[] = "form_".$form_id."_entries.ev_".$elid." as ev_".$elid;
					}
				}

				if (!is_array($q_els))
				{
					$this->raise_error(ERR_FG_NOTBLELS,t("Tulemuste kuvamise tabelis pole yhtegi elementi!"), true);
				}

				$eids = join(",", $matches);
				if ($eids != "")
				{
					$jss = join(",",$q_els);
					if ($jss != "")
					{
						$jss=",".$jss;
					}
					$chenrties = array();
					$q = "SELECT objects.modifiedby as modifiedby,objects.modified as modified,objects.created as created,objects.parent as parent,form_".$form_id."_entries.id as entry_id $jss FROM form_".$form_id."_entries LEFT JOIN objects ON objects.oid = form_".$form_id."_entries.id WHERE form_".$form_id."_entries.id in ($eids) AND objects.status != 0";
					$this->db_query($q);
					$cnt = 0;
					while ($row = $this->db_next())
					{
						if ($this->can("view",$row["entry_id"])  || $this->cfg["site_id"] == 11 || aw_ini_get("menuedit.no_view_acl_checks"))
						{
							$cnt++;

							// dis shit here makes the link that does a new search on the element you clicked
							if (is_array($ft->table["doelsearchcols"]))
							{
								foreach($ft->table["doelsearchcols"] as $_de_col => $_de_elid_ar)
								{
									if ($row["ev_".$_de_elid_ar["elid"]] != "")
									{
										$_de_url = $this->mk_my_orb("show_entry",
											array(
												"id" => $this->id,
												"entry_id" => $this->entry_id,
												"op_id" => 1,
												"search_el" => $linked_els[$_de_elid_ar["elid"]],
												"search_val" => $row["ev_".$_de_elid_ar["elid"]]
											)
										);
										$row["ev_".$_de_elid_ar["elid"]] = "<a href='".$_de_url."'>".$row["ev_".$_de_elid_ar["elid"]]."</a>";
									}
								}
							}
							$ft->row_data($row,$form_id,$section,$this->arr["search_outputs"][$form_id]);
						}
					}
				}
			}
			else
			{
				// figure out what elements from what forms are used in the table and bring in the data from those forms and
				// those forms elements only.
				$joins = array();
				$q_els = array();
				$has_eid = false;
				if (!is_array($used_els[$this->search_form]))
				{
					$used_els = array($this->search_form => array()) + $used_els;
				}
				foreach($used_els as $form_id => $el_arr)
				{
					$joins[] = "LEFT JOIN form_".$form_id."_entries ON form_".$form_id."_entries.chain_id = form_chain_entries.id";
					if (!$has_eid)
					{
						$q_els[] = "form_".$form_id."_entries.id as entry_id";
						$has_eid = true;
						$real_form_id = $form_id;
					}
					foreach($el_arr as $elid)
					{
						$q_els[] = "form_".$form_id."_entries.ev_".$elid." as ev_".$elid;
					}
				}

				$eids = join(",", $matches);
				if ($eids != "")
				{
					$jss = join(",",$q_els);
					if ($jss != "")
					{
						$jss=",".$jss;
					}
					$joss = join(" ",$joins);
					$chenrties = array();
					$q = "SELECT form_chain_entries.id as chain_entry_id,fco.modifiedby as modifiedby, fco.created as created, fco.modified as modified  $jss FROM form_chain_entries LEFT JOIN objects AS fco ON fco.oid = form_chain_entries.id $joss WHERE form_chain_entries.id in ($eids)";
					$this->db_query($q);
					$cnt = 0;
					$used_ids = array();
					while ($row = $this->db_next())
					{
						if ($used_ids[$row["chain_entry_id"]])
						{
							continue;
						}
						$used_ids[$row["chain_entry_id"]]=1;
						if ($this->can("view",$row["entry_id"])  || $this->cfg["site_id"] == 11 || aw_ini_get("menuedit.no_view_acl_checks"))
						{
							$cnt++;
							// kui on p2rg, siis muudame p2rga
							if ($this->can("edit",$row["entry_id"])  || $this->cfg["site_id"] == 11)
							{
								$row["ev_change"] = "<a href='".$this->mk_my_orb("show", array("id" => $this->chain_id,"section" => 1,"entry_id" => $row["chain_entry_id"],"section" => $section), "form_chain")."'>".$ft->table["texts"]["change"][$ft->lang_id]."</a>";

								$row["ev_chpos"] = "<input type='hidden' name='old_pos[$real_form_id][".$row["entry_id"]."]' value='".$row["parent"]."'><select name='chpos[$real_form_id][".$row["entry_id"]."]'>".$this->picker($row["parent"],/*$ft->get_menu_picker()*/array())."</select>";
							}
							$row["ev_created"] = $this->time2date($row["created"], 2);
							$row["ev_uid"] = $row["modifiedby"];
							$row["ev_modified"] = $this->time2date($row["modified"], 2);
							$row["ev_view"] = "<a href='".$this->mk_my_orb("show_entry", array("id" => $real_form_id,"entry_id" => $row["entry_id"], "op_id" => $this->arr["search_outputs"][$real_form_id],"section" => $section))."'>".$ft->table["texts"]["view"][$ft->lang_id]."</a>";
							if ($this->can("delete", $row["entry_id"])  || $this->cfg["site_id"]== 11)
							{
								$row["ev_delete"] = "<a href='javascript:box2(\"Kas oled kindel et tahad kustutada?\",\"".$this->mk_my_orb(
									"delete_entry",
										array(
											"id" => $real_form_id ,
											"entry_id" => $row["chain_entry_id"],
											"after" => $this->binhex($this->mk_my_orb("show_entry", array("id" => $this->id, "entry_id" => $entry_id, "op_id" => $output_id,"section" => $section)))
										),
									"form")."\")'>".$ft->table["texts"]["delete"][$ft->lang_id]."</a>";
							}
							if ($ft->table["view_col"] && $ft->table["view_col"] != "view")
							{
								$row["ev_".$ft->table["view_col"]] = "<a href='".$this->mk_my_orb("show_entry", array("id" => $real_form_id,"entry_id" => $row["entry_id"], "op_id" => $this->arr["search_outputs"][$real_form_id],"section" => $section))."'>".$row["ev_".$ft->table["view_col"]]."</a>";
							}
							if ($ft->table["change_col"] && $ft->table["change_col"] != "change")
							{
								$row["ev_".$ft->table["change_col"]] = "<a href='".$this->mk_my_orb("show", array("id" => $this->chain_id,"section" => 1,"entry_id" => $row["chain_entry_id"],"section" => $section), "form_chain")."'>".$row["ev_".$ft->table["change_col"]]."</a>";
							}

							// dis shit here makes the link that does a new search on the element you clicked
							if (is_array($ft->table["doelsearchcols"]))
							{
								foreach($ft->table["doelsearchcols"] as $_de_col => $_de_elid_ar)
								{
									if ($row["ev_".$_de_elid_ar["elid"]] != "")
									{
										$_de_url = $this->mk_my_orb("show_entry", array("id" => $this->id, "entry_id" => $this->entry_id, "op_id" => 1, "search_el" => $linked_els[$_de_elid_ar["elid"]], "search_val" => $row["ev_".$_de_elid_ar["elid"]]));
										$row["ev_".$_de_elid_ar["elid"]] = "<a href='".$_de_url."'>".$row["ev_".$_de_elid_ar["elid"]]."</a>";
									}
								}
							}
							$ft->row_data($row);
						}
					}
				}
			}
			$_sby = $GLOBALS["sortby"];
			$_so = $GLOBALS["sort_order"];
			if ($_sby == "")
			{
				$_sby = "ev_".$ft->table["defaultsort"];
				$_so = "asc";
				$_sn = ($ft->table["defaultsort_type"] == "int");
			}
			if ($ft->table["group"])
			{
				$_grpby = "ev_".$ft->table["group"];
			}
			$ft->t->sort_by(array("field" => $_sby,"sorder" => $_so,"group_by" => $_grpby));
			$tbl = $ft->get_css();
			$is_button_table = $ft->table["submit_top"] || $ft->table["user_button_top"] || $ft->table["submit_bottom"] || $ft->table["user_button_bottom"];
			if ($is_button_table)
			{
				$tbl.="<form action='reforb.aw' method='POST'>\n";
			}
			if ($ft->table["submit_top"])
			{
				$tbl.="<input type='submit' value='".$ft->table["submit_text"]."'>";
			}

			if ($ft->table["closewin"])
			{
				global $ft_closewin;
				$ft_closewin[$output_id] = $ft->table["closewin_value"];
				session_register("ft_closewin");
			};

			if ($ft->table["user_button_top"])
			{
				$tbl.="&nbsp;<input type='submit' value='".$ft->table["user_button_text"]."' onClick=\"window.location='".$ft->table["user_button_url"]."';return false;\">";
			}
			$_rgrpby = "";
			if ($ft->table["rgroup"])
			{
				$_rgrpby = "ev_".$ft->table["rgroup"];
			}
			$tbl.=$ft->t->draw(array("rgroupby" => $_rgrpby));

			if ($ft->table["submit_bottom"])
			{
				$tbl.="<input type='submit' value='".$ft->table["submit_text"]."'>";
			}
			if ($ft->table["user_button_bottom"])
			{
				$tbl.="&nbsp;<input type='submit' value='".$ft->table["user_button_text"]."' onClick=\"window.location='".$ft->table["user_button_url"]."';return false;\">";
			}

			if ($GLOBALS["get_csv_file"])
			{
				header('Content-type: application/octet-stream');
				header('Content-disposition: root_access; filename="csv_output_'.$id.'.csv"');
				print $ft->t->get_csv_file();
				die();
			};

			$tbl.= $this->mk_reforb("submit_table", array("return" => $this->binhex($this->mk_my_orb("show_entry", array("id" => $this->id, "entry_id" => $entry_id, "op_id" => $output_id)))));

			if ($is_button_table)
			{
				$tbl.="</form>";
			}

//			$tbl = create_links($tbl);

			global $print;

			if ( ($ft->table["print_button"]) && (not($print)) )
			{
				$link = aw_global_get("REQUEST_URI") . "&print=1";
				$tbl = $tbl . "<div align=right><a href='$link' target='_new'><img src='/img/print.gif' border='0' title='Print'></a></div>";
			}

			if ($this->cfg["site_id"] == 14 || $this->cfg["site_id"] == 50)
			{
				$url = "&nbsp;&nbsp;<a href='".aw_global_get("REQUEST_URI")."&get_csv_file=1' target=_blank>CSV</a><br />";
				$tbl="Otsingu tulemusena leiti ".$cnt." kirjet. <br />".$url.$tbl;
			}
			return $tbl;
		}
		else
		{
			// n2itame sisestusi lihtsalt yxteise j2rel
			if ($this->form_search_only)
			{
				$fid = $this->search_form;
				$t = get_instance(CL_FORM);
				reset($matches);
				while (list(,$eid) = each($matches))
				{
					$t->reset();
					$html.=$t->show(array("id" => $fid, "entry_id" => $eid, "op_id" => $this->arr["search_outputs"][$fid]));
				}
			}
			else
			{
				$fid = $this->search_form;
				$t = get_instance(CL_FORM);
				// need on chain entry id'd
				$mtstr = join(",",$matches);
				if ($mtstr != "")
				{
					$this->db_query("SELECT form_".$fid."_entries.id as id FROM form_chain_entries LEFT JOIN form_".$fid."_entries ON form_chain_entries.id = form_".$fid."_entries.chain_id WHERE form_chain_entries.id IN ($mtstr)");
					while ($row = $this->db_next())
					{
						$t->reset();
						$html.=$t->show(array("id" => $fid, "entry_id" => $row["id"], "op_id" => $this->arr["search_outputs"][$fid]));
					}
				}
			}
		}

		return " ".$html;
	}

	/** this gets called when the user views search results as a table that has a submit button.

		@attrib name=submit_table params=name default="0"

		@param return required

		@returns


		@comment
		here we must change the activity / loactio of the form entries

	**/
	function submit_table($arr)
	{
		extract($arr);
		if (is_array($old_active))
		{
			foreach($old_active as $fid => $ear)
			{
				foreach($ear as $eid => $status)
				{
					if ($active[$fid][$eid] == 1 && $status == 1)	// new status active, old not active
					{
						// make obj active
						$tmp = obj($eid);
						$tmp->set_status(2);
						$tmp->save();
					}
					else
					if ($active[$fid][$eid] != 1 && $status == 2)	// new status not active, old active
					{
						// make not active
						$tmp = obj($eid);
						$tmp->set_status(1);
						$tmp->save();
					}
				}
			}
		}

		if (is_array($old_pos))
		{
			foreach($old_pos as $fid => $par)
			{
				foreach($par as $eid => $loc)
				{
					if ($chpos[$fid][$eid] != 0 && $loc != $chpos[$fid][$eid])	// location selected and changed
					{
						// change location
						$tmp = obj($eid);
						$tmp->set_parent($chpos[$fid][$eid]);
						$tmp->save();
					}
				}
			}
		}
		return $this->hexbin($return);
	}

	function html()
	{
		$frm= $this->gen_user_html();
		$frm = htmlentities($frm);
		$this->reset();
		$this->read_template("html.tpl");
		$this->vars(array("form" => $frm));
		return $this->parse();
	}

	/** Adds a new form

		@attrib name=new params=name default="0"

		@param parent required acl="add"
		@param alias_doc optional
		@param return_url optional
		@param alias_to optional

		@returns


		@comment

	**/
	function add($arr)
	{
		extract($arr);
		$this->mk_path($parent,LC_FORM_ADD_FORM);
		$this->read_template("form_add.tpl");
		$mlist = $this->get_menu_list();

		// generate a list of files in the config directory. actually I only need this for
		// config forms, so this will be gone from here as soon as I figure out another
		// way to show the file picker only if it is needed
		$files = $this->get_directory(array("dir" => aw_ini_get("basedir") . "/xml/config"));

		$this->vars(array(
			"forms" => $this->picker(0,$this->get_list(FTYPE_ENTRY,true)),
			"types" => $this->picker(-1,$this->ftypes),
			"config_files" => is_array($files) ? $this->picker("planner.xml",$files) : "",
			"el_default_folders" => $this->picker($parent,$mlist),
			"reforb"	=> $this->mk_reforb("submit_add",array("parent" => $parent, "alias_doc" => $alias_doc))
		));
		return $this->parse();
	}

	/** Submits the new form

		@attrib name=submit_add params=name default="0"


		@returns


		@comment

	**/
	function submit_add($arr)
	{
		extract($arr);

		$o = obj();
		$o->set_parent($parent);
		$o->set_name($name);
		$o->set_class_id(CL_FORM);
		$o->set_comment($comment);
		$o->set_status(STAT_ACTIVE);
		$id = $o->save();

		// $type is integer now
		$this->db_query("INSERT INTO forms(id, type,content,cols,rows) VALUES($id, $type,'',1,1)");
		$this->db_query("CREATE TABLE form_".$id."_entries (id INT PRIMARY KEY,chain_id INT,deleted int default 0,lang_id int, INDEX(chain_id))");
		$this->db_query("ALTER TABLE form_".$id."_entries ADD index deleted(deleted)");
		$this->db_query("ALTER TABLE form_".$id."_entries ADD index lang_id(lang_id)");

		$this->load($id);

		// default folder for new elements. I _need_ to know  a folder for config forms
		// put perhaps this is useful somewhere else as well
		$this->arr["el_default_folder"] = $el_default_folder;

		// add the type of the form to the log message as well
		$this->_log(ST_FORM, SA_ADD, $this->ftypes[$type] ." ". $name, $id);

		// XXX: sucky-sucky
		if ($alias_doc)
		{
			$o = obj($alias_doc);
			$o->connect(array(
				"to" => $id
			));
		}

		// uhm yeah. if the user selected a base form, then we must clone it and all the elements in it

		// unless it's a config form in which case I don't really want to clone it. At least not right
		// now -- duke
		if ($base)
		{
			// don't you like this a lot better? :) -- duke
			// why yes. yes I do :) -- terryf
			$this->_clone_from($base,$id);
		}

		$this->save();

		// change is gen_grid
		return $this->mk_orb("change", array("id" => $id));
	}

	////
	// !Clones "this" form from another
	function _clone_from($base,$id)
	{
		$bf = get_instance(CL_FORM);
		$bf->load($base);

		$this->arr = $bf->arr;
		$this->arr["elements"] = array();
		$this->arr["contents"] = array();
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				// this is a trick to make form cells in $bf save new elements to $this
				$bf->arr["contents"][$row][$col]->form = &$this;

				$bf->arr["contents"][$row][$col]->id = $id;

				if (is_array($bf->arr["elements"][$row][$col]))
				{
					foreach($bf->arr["elements"][$row][$col] as $elid => $elval)
					{
						if (is_numeric($elid))
						{
							// replicate this element into this form!!
							$el_parent = $this->db_fetch_field("SELECT parent FROM objects WHERE oid = $elid", "parent");

							$newel = $bf->arr["contents"][$row][$col]->do_add_element(array(
								"parent" => $el_parent,
								"name" => $elval["name"],
								"based_on" => $elid
							));

							$elval["id"] = $newel;
							$elval["ver2"] = true;
							$elval["linked_form"] = $base;
							$elval["linked_element"] = $elid;

							// if it's a relation element, then the element holds the id of the row in form_relations table
							// we must create a new one here, because otherwise when we delete elements from the new form
							// then the relations for the old element will also get deleted.
							if ($elval["rel_table_id"])
							{
								$this->db_query("SELECT * FROM form_relations WHERE id = ".$elval["rel_table_id"]);
								$dat = $this->db_next();
								$this->db_query("INSERT INTO form_relations(form_from, form_to, el_from, el_to)
								VALUES('".$dat["form_from"]."','".$dat["form_to"]."','".$dat["el_from"]."','".$dat["el_to"]."')");
								$elval["rel_table_id"] = $this->db_last_insert_id();
							}

							$this->arr["elements"][$row][$col][$newel] = $elval;
//								echo "elval linked form = $base , real = ", $this->arr["elements"][$row][$col][$newel]["linked_form"]," <br />";
							// save element props also
							$prp = aw_serialize($this->arr["elements"][$row][$col][$newel],SERIALIZE_XML);
							$this->quote(&$prp);
							$this->db_query("UPDATE form_elements SET props = '$prp' WHERE id = $newel");
						}
						else
						{
							// class style
							$this->arr["elements"][$row][$col][$elid] = $elval;
						}
					}
				}
			}
		}
		$this->arr["search_from"][$base]=1;
	}

	/** Generates admin form for editing cell at ($row,$col) in form $id

		@attrib name=admin_cell params=name default="0"

		@param id required acl="edit;view"
		@param row required
		@param col required

		@returns


		@comment

	**/
	function admin_cell($arr)
	{
		extract($arr);
		$this->load($id);
		return $this->arr["contents"][$row][$col]->admin_cell();
	}

	/** Adds an element to the end of

		@attrib name=add_element params=name default="0"

		@param id required acl="edit;view"
		@param row required
		@param col required

		@returns


		@comment

	**/
	function add_element($arr)
	{
		extract($arr);
		$this->load($id);
		return $this->arr["contents"][$row][$col]->add_element();
	}

	/** submits a new element to form (from add_element)

		@attrib name=submit_element params=name default="0"


		@returns


		@comment

	**/
	function submit_element($args = array())
	{
		extract($args);
		$this->load($id);
		$this->arr["contents"][$row][$col]->submit_element($args);
		return $this->mk_my_orb("admin_cell", array("id" => $id, "row" => $row, "col" => $col));
	}

	/** saves the elements in the cell ($row, $col) in form $id

		@attrib name=submit_cell params=name default="0"


		@returns


		@comment

	**/
	function submit_cell($arr)
	{
		extract($arr);
		$this->load($id);
		$this->arr["contents"][$row][$col]->submit_cell($arr);
		$this->save();
		return $this->mk_my_orb("admin_cell", array("id" => $this->id, "row" => $row, "col" => $col));
	}

	/** deletes form $id

		@attrib name=delete params=name default="0"

		@param id required
		@param parent required

		@returns


		@comment

	**/
	function delete($arr)
	{
		extract($arr);
		$tmp = obj($id);
		$name = $tmp->name();
		$tmp->delete();
		$this->_log(ST_FORM, SA_DELETE, $name, $id);
		header("Location: ".$this->mk_orb("obj_list", array("parent" => $parent), "menuedit"));
	}

	////
	// !finds the element with id $id in the loaded form and returns a reference to it
	function &get_element_by_id($id)
	{
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);
				while (list(,$el) = each($elar))
				{
					if ($el->get_id() == $id)
					{
						return $el;
					}
				}
			}
		}
		return false;
	}

	////
	// !finds the element with name $name in the loaded form and returns a reference to it
	// Kui ma nyyd oieti aru saan, siis see eeldab muu hulgas ka seda, et on laetud mingi entry.
	//
	// nope, see ei eelda, get_element_value_by_name eeldab et miski entry on loaditud - terryf
	// $type can be either RET_FIRST - returns the first element or RET_ALL - returns all elements with the name
	function get_element_by_name($name,$type = RET_FIRST)
	{
		$ret = array();
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);
				reset($elar);
				while (list(,$el) = each($elar))
				{
					if ($el->get_el_name() == $name)
					{
						if ($type == RET_FIRST)
						{
							return $el;
						}
						else
						{
							$ret[] = $el;
						}
					}
				}
			}
		}

		if ($type == RET_FIRST || !is_array($ret))
		{
			return false;
		}
		else
		{
			return $ret;
		}
	}

	////
	// !Finds the id-s for for form elements passed by name to this function
	// argumendid:
	// names(array) - array nimedest, mille id-sid me teada tahame
	function get_ids_by_name($args = array())
	{
		extract($args);
		$retval = array();
		$namelist = array_flip($names);

		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);
				reset($elar);
				while(list(,$el) = each($elar))
				{
					$name = $el->arr["name"];
					if (isset($namelist[$name]))
					{
						$retval[$name] = sprintf("el_%d",$el->arr["id"]);
					};
				};
			};
		};
		return $retval;
	}

	////
	// !finds the first element with type $type (and subtype $subtype) in the loaded form and returns a reference to it
	// if no such element is found, returns false
	// if all_els is true, returns all matching elements in an array. if none match, returns empty array
	function get_element_by_type($type,$subtype = "",$all_els = false)
	{
		$ret = array();
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);
				reset($elar);
				while (list(,$el) = each($elar))
				{
					if ($el->get_type() == $type && ($subtype == "" || $el->get_subtype() == $subtype))
					{
						if ($all_els)
						{
							$ret[] = $el;
						}
						else
						{
							return $el;
						}
					}
				}
			}
		}
		if ($all_els)
		{
			return $ret;
		}
		return false;
	}

	/** generates the form for changing the form element's position in the hierarchy and in the cells

		@attrib name=change_el_pos params=name default="0"

		@param id required acl="edit;view"
		@param col required
		@param row required
		@param el_id required

		@returns


		@comment

	**/
	function change_el_pos($arr)
	{
		extract($arr);
		$this->if_init($id, "", "<a href='".$this->mk_orb("change", array("id" => $id)).LC_FORM_CHANGE_FORM_CHOOSE_EL_LOC);
		$el =&$this->get_element_by_id($el_id);
		return $el->change_pos($arr,&$this);
	}

	/** saves the element position changes

		@attrib name=submit_chpos params=name default="0"


		@returns


		@comment

	**/
	function submit_chpos($arr)
	{
		extract($arr);
		$this->load($id);

		$tmp = obj($el_id);
		$tmp->set_parent($parent);
		$tmp->save();

		if (is_array($c_cell))
		{
			$oldel = $this->arr["elements"][$row][$col][$el_id];
			$oldel_ob = obj($el_id);

			$cnt = 1;
			foreach($c_cell as $rowc)
			{
				list($r,$c) = explode("_", $rowc);
				// $r,$c = kuhu kopeerida element
				// kordame niimitu korda kui mitu elementi tyyp tahtis

				for ($i=0; $i < $el_count; $i++)
				{
					$name = $oldel["name"]."_".$cnt;
					$this->arr["contents"][$row][$col]->do_add_element(array(
						"name" => $name,
						"parent" => $oldel_ob->parent(),
						"based_on" => $el_id
					));
					$cnt++;
				}
			}
			$this->save();	// sync
			$this->load($id);
		}

		list($r,$c) = explode("_", $s_cell);

		if (!($r == $row && $c == $col))
		{
			$this->arr["elements"][$r][$c][$el_id] = $this->arr["elements"][$row][$col][$el_id];
			unset($this->arr["elements"][$row][$col][$el_id]);
			if (!is_array($this->arr["elements"][$row][$col]))
			{
				$this->arr["elements"][$row][$col] = array();
			}
			$this->save();
		}

		return $this->mk_orb("change_el_pos", array("id" => $this->id, "col" => $c, "row" => $r, "el_id" => $el_id));
	}

	function _serialize($arr)
	{
		extract($arr);
		$this->db_query("SELECT objects.*, forms.* FROM objects LEFT JOIN forms ON forms.id = objects.oid WHERE oid = $oid");
		$row = $this->db_next();
		if (!$row)
		{
			return false;
		}
		$str = serialize($row);
		$this->quote(&$str);
		return $str;
	}

	function _unserialize($arr)
	{
		extract($arr);

		$this->dequote($str);

		$row = utf_unserialize($str);
		// basically, we create a new object and insert the stuff in the array right back in it.
		$o = obj();
		$o->set_parent($parent);
		$o->set_name($row["name"]);
		$o->set_class_id(CL_FORM);
		$o->set_comment($row["comment"]);
		$o->set_status(STAT_ACTIVE);
		$o->set_alias($row["alias"]);
		$oid = $o->save();

		// same with the form.
		$this->quote(&$row);
		$this->quote(&$row);
		$this->db_query("INSERT INTO forms(id,content,type,cols,rows) values($oid,'".$row["content"]."','".$row["type"]."','".$row["cols"]."','".$row["rows"]."')");

		// create form entries table
		$this->db_query("CREATE TABLE form_".$oid."_entries (id int primary key,chain_id int)");

		// then we go through alla the elements in the form
		$this->load($oid);

		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				if (is_array($this->arr["elements"][$row][$col]))
				{
					reset($this->arr["elements"][$row][$col]);
					while (list($k,$v) = each($this->arr["elements"][$row][$col]))
					{
						// and for each alter the correct db tables
						$this->add_element_cols($this->id,$k);
					}
				}
			}
		}
		// and we should be done? ok, except for form actions and outputs, but we do those l8r.
		return $oid;
	}

	/** generates the form for changing output metainfo

		@attrib name=metainfo params=name default="0"

		@param id required acl="edit;view"

		@returns


		@comment

	**/
	function metainfo($arr)
	{
		extract($arr);
		$this->if_init($id,"metainfo.tpl","Muutis formi $this->name metainfot");
		$row = obj($this->id);

		$this->db_query("SELECT count(id) as cnt from form_entries where form_id = $this->id");
		if (!($cnt = $this->db_next()))
		{
			$this->raise_error(ERR_FG_EMETAINFO,t("form->metainfo(): weird error!"), true);
		}

		$this->vars(array(
			"created" => $this->time2date($row->created(),2),
			"created_by" => $row->createdby(),
			"modified" => $this->time2date($row->modified(),2),
			"modified_by" => $row->modifiedby(),
			"views" => 1,
			"num_entries" => $cnt["cnt"],
			"position" => $ret,
			"reforb" => $this->mk_reforb("submit_metainfo", array("id" => $this->id)),
			"form_name" => $row->name(),
			"form_comment" => $row->comment()
		));
		return $this->do_menu_return();
	}


	/**

		@attrib name=submit_metainfo params=name default="0"


		@returns


		@comment

	**/
	function submit_metainfo(&$arr)
	{
		extract($arr);
		$tmp = obj($id);
		$tmp->set_name($name);
		$tmp->set_comment($comment);
		$tmp->save();
		$this->_log(ST_FORM, SA_CHANGE, $this->name, $id);
		return $this->mk_orb("metainfo",  array("id" => $id));
	}

	////
	// !returns the value of the entered element. form entry must be loaded before calling this.
	function get_element_value_by_name($name, $numeric = false)
	{
		$el = $this->get_element_by_name($name);
		if (!$el)
		{
			return false;
		}

		$va = $el->get_value($numeric);
		return $va;
	}

	////
	// !returns the value of the entered element. finds the first element of $type (and $subtype)  and
	// ignores the rest. form entry must be loaded before calling this.
	function get_element_value_by_type($type,$subtype = "")
	{
		$el = $this->get_element_by_type($type,$subtype);
		if (!$el)
		{
			return false;
		}

		$va = $el->get_value();
		return $va;
	}

	////
	// !returns the value of element with id $id
	// $numeric - if true, the element will try to return a number instead of a string (checkbox value vs 1/0)
	function get_element_value($id, $numeric = false)
	{
		$el = $this->get_element_by_id($id);
		if ($el)
		{
			$ev =  $el->get_value($numeric);
			return $ev;
		}
		return "";
	}

	////
	// !returns the value of element with id $id
	// $numeric - if true, the element will try to return a number instead of a string (checkbox value vs 1/0)
	function get_element_value_ctr($id, $numeric = false)
	{
		$el = $this->get_element_by_id($id);
		if ($el)
		{
			$ev =  $el->get_val($numeric);
			return $ev;
		}
		return "";
	}

	////
	// !sets the element $id's value in the loaded entry to $val
	// $user_val - if set $val is assumed to be user value and the element is set with a different function
	function set_element_value($id,$val,$user_val = false)
	{
		$elref = $this->get_element_by_id($id);
		if (is_object($elref))
		{
			if (is_object($this->arr["contents"][$elref->get_row()][$elref->get_col()]))
			{
				$this->arr["contents"][$elref->get_row()][$elref->get_col()] -> set_element_entry($id,$val,$user_val);
			}
		}
	}

	////
	// !returns the selected element of a form element
	// right now works for listboxes only
	function get_element_selection_id($id)
	{
		$el = $this->get_element_by_id($id);
		if ($el)
		{
			$ev =  $el->get_selection_id($numeric);
			return $ev;
		}
		return "";
        }

	////
	// !sets the element value in the loaded entry to $val fort elements of type $type
	function set_element_value_by_type($type,$val)
	{
		$el = $this->get_element_by_type($type);
		if ($el)
		{
			$id = $el->get_id();
			$this->set_element_value($id,$val);
		}
	}

	////
	// !returns the number of rows in the currently loaded form
	function get_num_rows()
	{
		return $this->arr["rows"];
	}

	////
	// !returns all the element_name => value pairs for the specified row
	// type values are defined in the beginning of this file
	function get_elements_for_row($row,$type = ARR_ELNAME)
	{
		$ret = array();
		for ($col = 0; $col < $this->arr["cols"]; $col++)
		{
			$this->arr["contents"][$row][$col]->get_els(&$elar);
			reset($elar);
			while (list(,$el) = each($elar))
			{
				if ($type == ARR_ELNAME)
				{
					$k = $el->get_el_name();
				}
				else
				if ($type == ARR_ELID)
				{
					$k = $el->get_id();
				}
				$ret[$k] = $el->get_value();
			}
		}
		return $ret;
	}

	////
	// !returns true if the value is the value that a checkbox recieves if it is checked
	function is_checked_value($val)
	{
		if ($val == '1')
		{
			return true;
		}
		return false;
	}

	////
	// !returns array of name => value pairs for the loaded form entry if $type == ARR_ELNAME
	// if $type == ARR_ELID, then array index is element id
	function get_element_values($type = ARR_ELNAME)
	{
		$ret = array();
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$this->arr["contents"][$row][$col]->get_els(&$elar);
				reset($elar);
				while (list(,$el) = each($elar))
				{
					if ($type == ARR_ELNAME)
					{
						$k = $el->get_el_name();
					}
					else
					if ($type == ARR_ELID)
					{
						$k = $el->get_id();
					}
					$ret[$k] = $el->get_value();
				}
			}
		}
		return $ret;
	}

	////
	// returns the entry in an array that you can feed to restore_entry to revert the saved entry to the old data
	function get_entry($form_id,$entry_id,$id_only = false)
	{
		$ret = array();
		$this->db_query("SELECT * FROM form_".$form_id."_entries WHERE id = $entry_id");
		$row =  $this->db_next();
		if ($row)
		{
			foreach($row as $k => $v)
			{
				$key = substr($k,3);
				if (substr($k,0,3) == "el_")
				{
					if ( $id_only )
					{
						$ret[$key] = $v;
					}
					else
					{
						$ret[$k] = $v;
					};
				}
			}
		}
		return $ret;
	}

	function restore_entry($form_id,$entry_id,$arr)
	{
		if (!is_array($arr))
		{
			return;
		}
		$str = join(",",map2(" %s = '%s' ",$arr));
		if ($str != "" && $entry_id)
		{
			$this->db_query("UPDATE form_".$form_id."_entries SET $str WHERE id = $entry_id");
		}
	}

	/**

		@attrib name=convels params=name default="0"


		@returns


		@comment

	**/
	function convels()
	{
		// convert from old representation of element -> form relations to the new and better one
		$this->db_query("SELECT form_elements.*,objects.* FROM form_elements LEFT JOIN objects ON objects.oid = form_elements.id WHERE objects.status != 0");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			echo "element ".$row["name"]." id ".$row["oid"]."<br />";
			$fa = utf_unserialize($row["forms"]);
			if (is_array($fa))
			{
				foreach($fa as $fid)
				{
					$this->db_query("INSERT INTO element2form(el_id,form_id) VALUES(".$row["oid"].",$fid)");
					echo "in form $fid <br />";
				}
			}
			flush();
			$this->restore_handle();
		}
	}

	/**

		@attrib name=convtype params=name default="0"


		@returns


		@comment

	**/
	function convtype()
	{
		$this->db_query("SELECT objects.*,menu.* FROM objects,menu WHERE objects.oid = menu.id AND objects.class_id = ".CL_MENU." AND menu.type = ".MN_FORM_ELEMENT);
		while ($row = $this->db_next())
		{
			$this->save_handle();
			$tmp = obj($row["oid"]);
			$tmp->set_class_id(CL_FORM_ELEMENT);
			$tmp->save();
			echo "oid = ", $row["oid"], "name = ",$row["name"], "<br />";
			$this->restore_handle();
		}
	}

	/**

		@attrib name=convindexes params=name default="0"


		@returns


		@comment

	**/
	function convindexes()
	{
		$this->db_query("SELECT * FROM objects WHERE class_id = ".CL_FORM);
		while ($row = $this->db_next())
		{
			$this->save_handle();

			$this->db_query("SELECT * FROM element2form WHERE form_id = ".$row["oid"]);
			while ($erow ->db_next())
			{
				$this->save_handle();

				echo "q = ALTER TABLE form_".$row["oid"]."_entries ADD index ev_".$erow["el_id"]."(ev_".$erow["el_id"]."(10)) ";
				flush();
				$this->db_query("ALTER TABLE form_".$row["oid"]."_entries ADD index ev_".$erow["el_id"]."(ev_".$erow["el_id"]."(10))");
				echo "q = ALTER TABLE form_".$row["oid"]."_entries ADD index el_".$erow["el_id"]."(ev_".$erow["el_id"]."(10)) ";
				flush();
				$this->db_query("ALTER TABLE form_".$row["oid"]."_entries ADD index el_".$erow["el_id"]."(el_".$erow["el_id"]."(10))");

				$this->restore_handle();
			}

			$this->restore_handle();
		}
	}

	/** converts form_xx_entries table and adds ev_xxx columns

		@attrib name=convtables params=name default="0"


		@returns


		@comment

	**/
	function convtables()
	{
		$this->db_query("SELECT * FROM objects WHERE class_id = ".CL_FORM);
		while ($row = $this->db_next())
		{
			$this->save_handle();

			$this->db_query("SELECT * FROM element2form WHERE form_id = ".$row["oid"]);
			while ($erow = $this->db_next())
			{
				$this->save_handle();

				echo "q = ALTER TABLE form_".$row["oid"]."_entries ADD ev_".$erow["el_id"]." text <br />";
				flush();
				$this->db_query("ALTER TABLE form_".$row["oid"]."_entries ADD ev_".$erow["el_id"]." text");

				$this->restore_handle();
			}

			$this->restore_handle();
		}
	}

	/**

		@attrib name=conventries params=name default="0"


		@returns


		@comment

	**/
	function conventries()
	{
		$run = true;
		$this->db_query("SELECT * FROM objects WHERE class_id = ".CL_FORM);

		while ($frow = $this->db_next())
		{

			if ($run)
			{
			$this->save_handle();

			$form = get_instance(CL_FORM);
			$form->load($frow["oid"]);

			echo "form ",$frow["oid"],"<br />\n";
			flush();
			$cnt = 0;
			$this->db_query("SELECT * FROM form_".$frow["oid"]."_entries");
			while ($erow = $this->db_next())
			{
				$cnt++;
				if (($cnt % 100) == 0)
				{
					echo "cnt = $cnt <br />\n";
					flush();
				}
				$this->save_handle();

				$form->load_entry($erow["id"]);
				for ($row = 0; $row < $form->arr["rows"]; $row++)
				{
					for ($col = 0; $col < $form->arr["cols"]; $col++)
					{
						$elar = array();
						$form->arr["contents"][$row][$col]->get_els(&$elar);
						foreach($elar as $el)
						{
//							if ($erow["ev_".$el->get_id()] != $el->get_value())
//							{
								$ev = $el->get_value();
//								$ev = preg_replace("/<(.*)>(.*)<\/(.*)>/imsU","",$ev);
								$ev = str_replace("'", "\\'", strip_tags($ev));
	echo "value for element ", $el->get_id(), " set to $ev <br />\n";
	flush();
								//$this->db_query("UPDATE form_".$frow["oid"]."_entries SET ev_".$el->get_id()." = '".$ev."' WHERE id = ".$erow["id"]);
//							}
						}
					}
				}

				$this->restore_handle();
			}

			$this->restore_handle();
			}
		}
	}

	/**

		@attrib name=convchains params=name default="0"


		@returns


		@comment

	**/
	function convchains()
	{
		$this->db_query("DELETE FROM form2chain");

		$this->db_query("SELECT * FROM form_chains");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			$cc = aw_unserialize($row["content"]);
			foreach($cc["forms"] as $fid)
			{
				$this->db_query("INSERT INTO form2chain(form_id,chain_id,ord) values($fid,".$row["id"].",'".$cc["form_order"][$fid]."')");
			}
			$this->restore_handle();
		}
	}

	/** lets the user select all folders for the form

		@attrib name=set_folders params=name default="0"

		@param id required acl="edit;view"

		@returns


		@comment

	**/
	function set_folders($arr)
	{
		extract($arr);
		$this->if_init($id,"settings_folders.tpl", LC_FORM_CHANGE_FOLDERS);

		$_menulist = $this->get_menu_list(false,false,-1,false);

		$_tp = $this->get_list(FTYPE_ENTRY,false,true);
		asort($_tp);

		// we must remove the current form from the list of relation forms so that the user can't relate a form to itself
		$_tmp = array();
		foreach($_tp as $k => $v)
		{
			if ($k != $id)
			{
				$_tmp[$k] = "(".$k.") ".$v;
			}
		}

		// now. if some parent menus are selected, go through all of them and add all their sub-menus to the list
		// if not, add all menus to the list
		if (is_array($this->arr["main_folders"]) && count($this->arr["main_folders"]) > 0)
		{
			$menulist = array();
			foreach($this->arr["main_folders"] as $mfid)
			{
				$menulist = $menulist + $this->get_list(false,false,$mfid);
			}
		}
		else
		{
			$menulist = $_menulist;
		}
		$this->vars(array(
			"main_folders" => $this->multiple_option_list($this->arr["main_folders"], $_menulist),
			"relation_forms" => $this->multiple_option_list($this->arr["relation_forms"], $_tmp),
			"ff_folder"	=> $this->picker($this->arr["ff_folder"], $menulist),
			"ne_folder"	=> $this->picker($this->arr["newel_parent"], $menulist),
			"tear_folder"	=> $this->picker($this->arr["tear_folder"], $menulist),
			"el_menus" => $this->multiple_option_list($this->arr["el_menus"], $menulist),
			"el_menus2" => $this->multiple_option_list($this->arr["el_menus2"], $menulist),
			"el_move_menus" => $this->multiple_option_list($this->arr["el_move_menus"], $menulist),
			"el_default_folders" => $this->picker($this->arr["el_default_folder"],$menulist),
			"form_controller_folders" => $this->multiple_option_list($this->arr["controller_folders"], $menulist),
			"reforb"	=> $this->mk_reforb("save_folders", array("id" => $id))
		));
		return $this->do_menu_return();
	}

	/** Salvestab vormi settingutes mratud folderite asukohad.

		@attrib name=save_folders params=name default="0"


		@returns


		@comment

	**/
	function save_folders($arr)
	{
		extract($arr);
		$this->load($id);

		// ff_folder - kuhu pannakse vormi sisestused?
		$this->arr["ff_folder"] = $ff_folder;

		// kataloog, kuhu lisatakse uued elemendid
		$this->arr["newel_parent"] = $newel_parent;

		// kataloog, kuhu pannakse rebitud elemendid
		$this->arr["tear_folder"] = $tear_folder;

		// kataloogid, kuhu saab uusi elemente salvestada
		$this->arr["el_menus"] = $this->make_keys($el_menus);

		// formid kust saab seoseelemente valida
		$this->arr["relation_forms"] = $this->make_keys($relation_forms);

		// kataloogid kuhu saab elemente liigutada
		$ms = $this->get_menu_list();
		$this->arr["el_move_menus"] = array();
		if (is_array($el_move_menus))
		{
			foreach($el_move_menus as $menuid)
			{
				$this->arr["el_move_menus"][$menuid] = $ms[$menuid];
			}
		}

		// kataloogid, kust alt kontrollereid valida lastakse
		$this->arr["controller_folders"] = $this->make_keys($form_controller_folders);

		// default folder uute elementide jaoks
		$this->arr["el_default_folder"] = $el_default_folder;

		$this->arr["el_menus2"] = $this->make_keys($el_menus2);
		$this->arr["main_folders"] = $this->make_keys($main_folders);

		$this->save();
		return $this->mk_orb("set_folders", array("id" => $id));
	}

	function get_search_targets()
	{
		$ret = array();
		if ($this->arr["new_search_engine"] == 1)
		{
			if (is_array($this->arr["search_forms"]))
			{
				$ret = $this->arr["search_forms"];
			}
		}
		else
		{
			if (is_array($this->arr["search_from"]))
			{
				foreach ($this->arr["search_from"] as $fid => $one)
				{
					if ($one == 1)
					{
						$ret[$fid] = $this->arr["search_from"][$fid];
					}
				}
			}
		}
		return $ret;
	}

	function get_relation_targets()
	{
		$ret = array();
		if (is_array($this->arr["relation_forms"]))
		{
			foreach ($this->arr["relation_forms"] as $fid)
			{
				$o = obj($fid);
				$ret[$fid] = $o->name();
			}
		}
		return $ret;
	}

	////
	// !if this function is called, all price elements are converted to this currency
	function set_active_currency($cuid = 0)
	{
		$this->active_currency = $cuid;
	}

	/** shows the form texts translation form

		@attrib name=translate params=name default="0"

		@param id required acl="edit;view"

		@returns


		@comment

	**/
	function translate($arr)
	{
		extract($arr);
		$this->if_init($id,"translate.tpl","T&otilde;gi");

		$la = get_instance("languages");
		$langs = $la->listall(true);

		foreach($langs as $lar)
		{
			$this->vars(array(
				"lang_name" => $lar["name"]
			));
			$lah.=$this->parse("LANGH");
		}
		$this->vars(array("LANGH" => $lah));

		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					$lcol = "";
					foreach($langs as $lar)
					{
						$lt = $el->get_lang_text($lar["id"]);
						$this->vars(array(
							"text" => ($lt != "" ? $lt : $el->get_text()),
							"col" => $col,
							"row" => $row,
							"elid" => $el->get_id(),
							"lang_id" => $lar["id"]
						));
						$lcol.=$this->parse("LCOL");
					}
					$this->vars(array("LCOL" => $lcol,"name" => $el->arr["name"]));
					$lrow.=$this->parse("LROW");
				}
			}
		}
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					if ($el->get_type() == "listbox")
					{
						for ($i=0; $i < $el->arr["listbox_count"]; $i++)
						{
							$lcol1 = "";
							foreach($langs as $lar)
							{
								if ($lar["id"] != $this->lang_id && $el->arr["listbox_lang_items"][$lar["id"]][$i] != "")
								{
									$txt = $el->arr["listbox_lang_items"][$lar["id"]][$i];
								}
								else
								{
									$txt = $el->arr["listbox_items"][$i];
								}
								$this->vars(array(
									"text" => $txt,
									"col" => $col,
									"row" => $row,
									"elid" => $el->get_id(),
									"lang_id" => $lar["id"],
									"item" => $i
								));
								$lcol1.=$this->parse("LCOL1");
							}
							$this->vars(array("LCOL1" => $lcol1,"name" => $el->arr["name"]));
							$lrow1.=$this->parse("LROW1");
						}
					}
				}
			}
		}
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					if ($el->get_type() == "multiple")
					{
						for ($i=0; $i < $el->arr["multiple_count"]; $i++)
						{
							$lcol2 = "";
							foreach($langs as $lar)
							{
								if ($lar["id"] != $this->lang_id && $el->arr["multiple_lang_items"][$lar["id"]][$i] != "")
								{
									$txt = $el->arr["multiple_lang_items"][$lar["id"]][$i];
								}
								else
								{
									$txt = $el->arr["multiple_items"][$i];
								}
								$this->vars(array(
									"text" => $txt,
									"col" => $col,
									"row" => $row,
									"elid" => $el->get_id(),
									"lang_id" => $lar["id"],
									"item" => $i
								));
								$lcol2.=$this->parse("LCOL2");
							}
							$this->vars(array("LCOL2" => $lcol2,"name" => $el->arr["name"]));
							$lrow2.=$this->parse("LROW2");
						}
					}
				}
			}
		}
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					$lcol3 = "";
					foreach($langs as $lar)
					{
						if ($lar["id"] != $this->lang_id && $el->arr["lang_info"][$lar["id"]] != "")
						{
							$txt = $el->arr["lang_info"][$lar["id"]];
						}
						else
						{
							$txt = $el->arr["info"];
						}
						$this->vars(array(
							"text" => $txt,
							"col" => $col,
							"row" => $row,
							"elid" => $el->get_id(),
							"lang_id" => $lar["id"],
						));
						$lcol3.=$this->parse("LCOL3");
					}
					$this->vars(array("LCOL3" => $lcol3,"name" => $el->arr["name"]));
					$lrow3.=$this->parse("LROW3");
				}
			}
		}
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					if ($el->get_type() == "textbox" || $el->get_type() == "textarea")
					{
						$lcol4 = "";
						foreach($langs as $lar)
						{
							if ($lar["id"] != $this->lang_id && $el->arr["lang_default"][$lar["id"]] != "")
							{
								$txt = $el->arr["lang_default"][$lar["id"]];
							}
							else
							{
								$txt = $el->arr["default"];
							}
							$this->vars(array(
								"text" => $txt,
								"col" => $col,
								"row" => $row,
								"elid" => $el->get_id(),
								"lang_id" => $lar["id"],
							));
							$lcol4.=$this->parse("LCOL4");
						}
						$this->vars(array("LCOL4" => $lcol4,"name" => $el->arr["name"]));
						$lrow4.=$this->parse("LROW4");
					}
				}
			}
		}
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					if ( ($el->arr["type"] == "") || ($el->arr["type"] == "button") )
					{
						continue;
					};
					$lcol5 = "";
					foreach($langs as $lar)
					{
						if ($lar["id"] != $this->lang_id && $el->arr["lang_must_error"][$lar["id"]] != "")
						{
							$txt = $el->arr["lang_must_error"][$lar["id"]];
						}
						else
						{
							$txt = $el->arr["must_error"];
						}
						$this->vars(array(
							"text" => $txt,
							"col" => $col,
							"row" => $row,
							"elid" => $el->get_id(),
							"lang_id" => $lar["id"],
						));
						$lcol5.=$this->parse("LCOL5");
					}
					$this->vars(array("LCOL5" => $lcol5,"name" => $el->arr["name"]));
					$lrow5.=$this->parse("LROW5");
				}
			}
		}
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					if ( ($el->arr["type"] == "") || ($el->arr["type"] == "button") )
					{
						continue;
					};
					$lcol8 = "";
					foreach($langs as $lar)
					{
						if ($lar["id"] != $this->lang_id && $el->arr["lang_check_length_error"][$lar["id"]] != "")
						{
							$txt = $el->arr["lang_check_length_error"][$lar["id"]];
						}
						else
						{
							$txt = $el->arr["check_length_error"];
						}
						$this->vars(array(
							"text" => $txt,
							"col" => $col,
							"row" => $row,
							"elid" => $el->get_id(),
							"lang_id" => $lar["id"],
						));
						$lcol8.=$this->parse("LCOL8");
					}
					$this->vars(array("LCOL8" => $lcol8,"name" => $el->arr["name"]));
					$lrow8.=$this->parse("LROW8");
				}
			}
		}
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					if ($el->get_type() == "button")
					{
						$lcol6 = "";
						foreach($langs as $lar)
						{
							if ($lar["id"] != $this->lang_id && $el->arr["lang_button_text"][$lar["id"]] != "")
							{
								$txt = $el->arr["lang_button_text"][$lar["id"]];
							}
							else
							{
								$txt = $el->arr["button_text"];
							}
							$this->vars(array(
								"text" => $txt,
								"col" => $col,
								"row" => $row,
								"elid" => $el->get_id(),
								"lang_id" => $lar["id"],
							));
							$lcol6.=$this->parse("LCOL6");
						}
						$this->vars(array("LCOL6" => $lcol6,"name" => $el->arr["name"]));
						$lrow6.=$this->parse("LROW6");
					}
				}
			}
		}

		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					$mtd = $el->get_metadata();
					foreach($mtd as $mtk => $mtv)
					{
						$lcol7 = "";
						foreach($langs as $lar)
						{
							$tx = $el->arr["metadata"][$lar["id"]][$mtk];
							if (!is_array($el->arr["metadata"][$lar["id"]]) || count($el->arr["metadata"][$lar["id"]]) < 1)
							{
								$tx = $el->arr["metadata"][$this->lang_id][$mtk];
							}
							$this->vars(array(
								"text" => $tx,
								"col" => $col,
								"row" => $row,
								"mtk" => $mtk,
								"elid" => $el->get_id(),
								"lang_id" => $lar["id"],
							));
							$lcol7.=$this->parse("LCOL7");
						}
						$this->vars(array("LCOL7" => $lcol7,"name" => $el->arr["name"]));
						$lrow7.=$this->parse("LROW7");
					}
				}
			}
		}

		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					if (!($el->get_type() == "button" && $el->get_subtype() == "url"))
					{
						continue;
					}
					$lcol9 = "";
					foreach($langs as $lar)
					{
						$tx = $el->arr["button_url"];
						if (isset($el->arr["lang_button_url"][$lar["id"]]))
						{
							$tx = $el->arr["lang_button_url"][$lar["id"]];
						}
						$this->vars(array(
							"text" => str_replace("\"","&quot;",$tx),
							"col" => $col,
							"row" => $row,
							"elid" => $el->get_id(),
							"lang_id" => $lar["id"],
						));
						$lcol9.=$this->parse("LCOL9");
					}
					$this->vars(array("LCOL9" => $lcol9,"name" => $el->arr["name"]));
					$lrow9.=$this->parse("LROW9");
				}
			}
		}

		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					if ($el->get_type() != "file")
					{
						continue;
					}
					$lcol10 = "";
					foreach($langs as $lar)
					{
						$tx = $el->arr["file_delete_link_text"][$lar["id"]];
						$this->vars(array(
							"text" => str_replace("\"","&quot;",$tx),
							"col" => $col,
							"row" => $row,
							"elid" => $el->get_id(),
							"lang_id" => $lar["id"],
						));
						$lcol10.=$this->parse("LCOL10");
					}
					$this->vars(array("LCOL10" => $lcol10,"name" => $el->arr["name"]));
					$lrow10.=$this->parse("LROW10");
				}
			}
		}

		$this->vars(array(
			"LROW" => $lrow,
			"LROW1" => $lrow1,
			"LROW2" => $lrow2,
			"LROW3" => $lrow3,
			"LROW4" => $lrow4,
			"LROW5" => $lrow5,
			"LROW6" => $lrow6,
			"LROW7" => $lrow7,
			"LROW8" => $lrow8,
			"LROW9" => $lrow9,
			"LROW10" => $lrow10,
			"reforb" => $this->mk_reforb("submit_translate", array("id" => $id))
		));

		return $this->do_menu_return();
	}

	/**

		@attrib name=submit_translate params=name default="0"


		@returns


		@comment

	**/
	function submit_translate($arr)
	{
		extract($arr);
		$this->load($id);

		$la = get_instance("languages");
		$langs = $la->listall(true);

		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$elar = array();
				$this->arr["contents"][$row][$col]->get_els(&$elar);

				foreach($elar as $el)
				{
					foreach($langs as $lar)
					{
						$this->arr["elements"][$row][$col][$el->get_id()]["lang_text"][$lar["id"]] = $r[$row][$col][$lar["id"]][$el->get_id()];
						$this->arr["elements"][$row][$col][$el->get_id()]["lang_info"][$lar["id"]] = $s[$row][$col][$lar["id"]][$el->get_id()];
						$this->arr["elements"][$row][$col][$el->get_id()]["lang_default"][$lar["id"]] = $d[$row][$col][$lar["id"]][$el->get_id()];
						$this->arr["elements"][$row][$col][$el->get_id()]["lang_must_error"][$lar["id"]] = $e[$row][$col][$lar["id"]][$el->get_id()];
						$this->arr["elements"][$row][$col][$el->get_id()]["lang_check_length_error"][$lar["id"]] = $cl[$row][$col][$lar["id"]][$el->get_id()];
						if ($el->get_type() == "button")
						{
							$this->arr["elements"][$row][$col][$el->get_id()]["lang_button_text"][$lar["id"]] = $b[$row][$col][$lar["id"]][$el->get_id()];
						}
					}
					if ($el->get_type() == "listbox")
					{
						foreach($langs as $lar)
						{
							for ($i=0; $i < $el->arr["listbox_count"]; $i++)
							{
								$this->arr["elements"][$row][$col][$el->get_id()]["listbox_lang_items"][$lar["id"]][$i] = $l[$row][$col][$lar["id"]][$el->get_id()][$i];
							}
						}
					}
					else
					if ($el->get_type() == "multiple")
					{
						foreach($langs as $lar)
						{
							for ($i=0; $i < $el->arr["multiple_count"]; $i++)
							{
								$this->arr["elements"][$row][$col][$el->get_id()]["multiple_lang_items"][$lar["id"]][$i] = $m[$row][$col][$lar["id"]][$el->get_id()][$i];
							}
						}
					}
					else
					if ($el->get_type() == "button" && $el->get_subtype() == "url")
					{
						foreach($langs as $lar)
						{
							$this->arr["elements"][$row][$col][$el->get_id()]["lang_button_url"][$lar["id"]] = $bu[$row][$col][$el->get_id()][$lar["id"]];
						}
					}
					else
					if ($el->get_type() == "file")
					{
						foreach($langs as $lar)
						{
							$this->arr["elements"][$row][$col][$el->get_id()]["file_delete_link_text"][$lar["id"]] = $dt[$row][$col][$el->get_id()][$lar["id"]];
						}
					}

					// now set metadata
					$this->arr["elements"][$row][$col][$el->get_id()]["metadata"] = $w[$row][$col][$el->get_id()];
				}
			}
		}
		$this->save();

		return $this->mk_my_orb("translate", array("id" => $id));
	}

	/**

		@attrib name=convformat params=name default="0"


		@returns


		@comment

	**/
	function convformat()
	{
		aw_set_exec_time(AW_LONG_PROCESS);
		$this->db_query("SELECT oid FROM objects WHERE class_id = ".CL_FORM." AND status != 0");
		while ($row = $this->db_next())
		{
			echo "form $row[oid] \n<br />";
			flush();
			$f = get_instance(CL_FORM);
			$f->load($row["oid"]);
			$f->save();
		}

		$this->db_query("SELECT oid FROM objects WHERE class_id = ".CL_FORM_OUTPUT." AND status != 0");
		while ($row = $this->db_next())
		{
			echo "form_op $row[oid] \n<br />";
			flush();
			$f = get_instance(CL_FORM_OUTPUT);
			$f->load_output($row["oid"]);
			$f->save_output($row["oid"]);
		}
	}

	/**

		@attrib name=sel_tables params=name default="0"

		@param id required acl="edit;view"

		@returns


		@comment

	**/
	function tables($arr)
	{
		extract($arr);
		$this->if_init($id,"admin_tables.tpl","Muuda tabeleid");

		$this->vars(array(
			"status_msg" => aw_global_get("status_msg")
		));
		aw_session_del("status_msg");

		// check if we are in the middle of an error and load the data from the session
		if (aw_global_get("form_table_save_error") == true)
		{
			$f_t_o = aw_global_get("f_t_o");
			if (is_array($f_t_o))
			{
				foreach($f_t_o as $key => $value)
				{
					$this->arr[$key] = $value;
				}
			}

			aw_session_del("form_table_save_error");
			aw_session_del("f_t_o");
		}

		// make a list of forms so we can name formgen tables as the name of the form
		$forms = $this->get_flist();

		$tables = array();
		$tbels = array();
		$this->db_list_tables();
		while ($tb = $this->db_next_table())
		{
			$_tb = $this->get_fg_tblname($tb);
			if ($_tb)
			{
				$tables[$tb] = $_tb;
			}
		}

		if (is_array($this->arr["save_tables"]))
		{
			$num_tbls = count($this->arr["save_tables"]);

			if ($num_tbls > 0)
			{
				// teeme array valitud tabelitest, mille saab picker funxioonile ette s88ta
				$_tables = array("" => "");
				foreach($tables as $_tb => $_temp)
				{
					if (isset($this->arr["save_tables"][$_tb]))
					{
						$__tb = $this->get_fg_tblname($_tb);
						if ($__tb)
						{
							$_tables[$_tb] = $__tb;
						}
					}
				}
				$this->vars(array(
					"objs_sel" => $this->picker($this->arr["save_tables_obj_tbl"],$_tables),
				));

				if ($this->arr["save_tables_obj_tbl"] != "")
				{
					$ta = $this->db_get_table($this->arr["save_tables_obj_tbl"]);
					foreach($ta["fields"] as $fn => $fdata)
					{
						$fields[$fn] = $this->get_fg_colname($fn);
					}
					$this->vars(array(
						"obj_column" => $this->picker($this->arr["save_tables_obj_col"],$fields)
					));
					$this->vars(array(
						"OBJ_SEL" => $this->parse("OBJ_SEL")
					));
				}

				$this->vars(array(
					"SOME_TABLES" => $this->parse("SOME_TABLES")
				));
			}

			foreach($this->arr["save_tables"] as $tbl => $tbcol)
			{
				// teeme array k6igist tabelitest peale selle
				$_tables = array();
				foreach($tables as $_tb)
				{
					if ($_tb != $tbl && isset($this->arr["save_tables"][$_tb]))
					{
						$_tables[$_tb] = $this->get_fg_tblname($_tb);
					}
				}

				// loeme tabeli tulpade inffi sisse
				$ta = $this->db_get_table($tbl);
				$fields = array("" => "");
				foreach($ta["fields"] as $fn => $fdata)
				{
					$fields[$fn] = $this->get_fg_colname($fn);
				}

				$this->vars(array(
					"table_name" => $tbl,
					"usr_table_name" => $this->get_fg_tblname($tbl),
					"cols" => $this->picker(trim($tbcol),$fields),
					"rel_tbls" => $this->multiple_option_list($this->arr["save_tables_rels"][$tbl],$_tables)
				));

				$ot = "";
				if ($num_tbls > 1)
				{
					$ret = "";
					if (is_array($this->arr["save_tables_rels"][$tbl]))
					{
						foreach($this->arr["save_tables_rels"][$tbl] as $_tb)
						{
							$ta = $this->db_get_table($_tb);
							$fields2 = array("" => "");
							foreach($ta["fields"] as $fn => $fdata)
							{
								$fields2[$fn] = $this->get_fg_colname($fn);
							}
							$this->vars(array(
								"rel_f_cols" => $this->picker($this->arr["save_tables_rel_els"][$tbl][$_tb]["from"],$fields),
								"rel_t_cols" => $this->picker($this->arr["save_tables_rel_els"][$tbl][$_tb]["to"],$fields2),
								"foreign_table" => $this->get_fg_tblname($_tb)
							));
							$ret.=$this->parse("REL_TABLE");
						}
					}

					$this->vars(array(
						"REL_TABLE" => $ret
					));
					$ot = $this->parse("HAS_OTHER_TABLES");
				}

				$this->vars(array(
					"HAS_OTHER_TABLES" => $ot
				));
				$tabel.=$this->parse("TABLE");
			}
		}
		$tbl = new aw_array($tables);
		$st = new aw_array($this->arr["save_tables"]);
		$this->vars(array(
			"save_table" => checked($this->arr["save_table"] == 1),
			"tables" => $this->multiple_option_list($this->make_keys(array_keys($st->get())), $tbl->get()),
			"TABLE" => $tabel,
			"reforb" => $this->mk_reforb("submit_tables", array("id" => $id))
		));
		return $this->do_menu_return();
	}

	/**

		@attrib name=submit_tables params=name default="0"


		@returns


		@comment

	**/
	function submit_tables($arr)
	{
		extract($arr);
		$this->load($id);

		$this->arr["save_table"] = $save_table;
		$this->arr["save_tables"] = array();
		if (is_array($tables))
		{
			foreach($tables as $tbl)
			{
				$this->arr["save_tables"][$tbl] = (string)$indexes[$tbl]." ";
			}
		}

		$this->arr["save_tables_obj_tbl"] = $objs_where;
		$this->arr["save_tables_obj_col"] = $obj_column;

		$this->arr["save_tables_rels"] = array();
		if (is_array($relations))
		{
			foreach($relations as $tbl => $connections)
			{
				if (is_array($connections))
				{
					foreach($connections as $_tb)
					{
						$this->arr["save_tables_rels"][$tbl][$_tb] = $_tb;
					}
				}
			}
		}

		$this->arr["save_tables_rel_els"] = array();
		if (is_array($rel_cols))
		{
			foreach($rel_cols as $tbl => $_dt)
			{
				foreach($_dt as $_tb => $ar)
				{
					$this->arr["save_tables_rel_els"][$tbl][$_tb]["to"] = $ar["to"];
					$this->arr["save_tables_rel_els"][$tbl][$_tb]["from"] = $ar["from"];
				}
			}
		}

		if (($msg = $this->check_table_relation_integrity()) == "ok")
		{
			$this->save();
			aw_session_set("status_msg","</font><font color=\"#000000\">Changes saved!");
		}
		else
		{
			aw_session_set("status_msg",$msg);
			// and here we must stuff all that shite in the session and set a flag indicating it.
			$f_t_o = array();
			$f_t_o["save_tables_rel_els"] = $this->arr["save_tables_rel_els"];
			$f_t_o["save_tables_rels"] = $this->arr["save_tables_rels"];
			$f_t_o["save_tables_obj_tbl"] = $this->arr["save_tables_obj_tbl"];
			$f_t_o["save_tables_obj_col"] = $this->arr["save_tables_obj_col"];
			$f_t_o["save_table"] = $this->arr["save_table"];
			$f_t_o["save_tables"] = $this->arr["save_tables"];

			aw_session_set("f_t_o",$f_t_o);
			aw_session_set("form_table_save_error",1);
		}
		return $this->mk_my_orb("sel_tables", array("id" => $id));
	}

	////
	// !this is the tricky bit. here we must check if the database tables' relations are complete and non-cyclic
	// that means that no table may be related to itself either directly or indirectly and you must be able to
	// reach any table from any other table by crawling through the relations
	function check_table_relation_integrity()
	{
		// we assume everything is fine if
		// a) we don't save things to existing tables
		// b) no tables are selected as save targets
		// c) just one table is selected
		if ($this->arr["save_table"] != 1 || !is_array($this->arr["save_tables"]))
		{
			return "ok";
		}

		if (count($this->arr["save_tables"]) < 2)
		{
			reset($this->arr["save_tables"]);
			list($k,$v) = each($this->arr["save_tables"]);
			$this->arr["save_table_start_from"] = $k;
			return "ok";
		}

		// easier said than done, that though.

		// ok, so let's try to detect cycles first

		// so we do that by starting from one table and following the relations and if we get back to any table we already
		// visited it means we have a cyclic dependency. and we don't like their kind around here. yessireee <ptui>
		foreach($this->arr["save_tables"] as $tbl => $col)
		{
			$this->cyclic_used_map = array();
			if (!$this->req_cyclic_dep($tbl))
			{
				return "Table $tbl has a cyclic dependency - changes not saved!";
			}
		}

		// now try and find if the chain is broken anywhere
		// once again, we go crawling through the relations and mark down the tables that we reach by crawling
		// and after we finish we check if we managed to cover all the tables. right?
		// and as a side-effect - a very useful side-effect as I might add :) - if we succeed in touching all the tables
		// we mark down the table where we started so we can use it later when writing stuff to the database
		$break_table = "";
		foreach($this->arr["save_tables"] as $tbl => $col)
		{
			if ($this->do_chain_dep($tbl,&$break_table))
			{
				// if we get here then that means that if we start from $tbl we can reach all the other tables as well
				// - so we mark that down, to be used l8r
				$this->arr["save_table_start_from"] = $tbl;
				return "ok";
			}
		}

		// if we end up here - that means that req_chain_dep failed every time and we have a broken
		// relation on our hands - so we report it to the user
		return "Table $break_table was not reachable through any relation - changes not saved!";
	}

	////
	// !this finds out if all the tables are reachable through relations if we start from $tbl
	// if all are reachable returns true
	// if a table is not reachable, returns false and puts the table's name on $break_table
	function do_chain_dep($tbl,&$break_table)
	{
		// reset the map
		$this->chain_dep_map = array();

		// make a local copy so we don't screw up internal pointers
		$_tmp = $this->arr["save_tables"];

		foreach($_tmp as $_tbl => $col)
		{
			$this->chain_dep_map[$_tbl] = false;
		}

		// populate the map
		$this->chain_dep_depth = 0;
		$this->req_chain_dep($tbl);

		// check if we missed any tables - but we must also check that maybe the table we started from has no relations
		// then it would be nicer to report this table to the user, not the first - so how do we do that? maybe we should record
		// tha maximim depth of the recursion and if it's zero, return the starting table? sounds like it might work.
		if ($this->chain_dep_depth == 0)
		{
			$break_table = $tbl;
			return false;
		}

		// if we got somewhere, find the first random one
		foreach($this->chain_dep_map as $tbl => $status)
		{
			if ($status == false)
			{
				// found one we missed!
				$break_table = $tbl;
				return false;
			}
		}
		return true;
	}

	////
	// !does the crawling through relations bit for do_chain_dep
	function req_chain_dep($tbl)
	{
		// mark this table in the map
		$this->chain_dep_map[$tbl] = true;

		// make a local copy so we won't screw up internal pointers
		$_tmp = $this->arr["save_tables_rels"][$tbl];
		if (!is_array($_tmp))
		{
			// if we reached an end of relation return
			return;
		}

		$this->chain_dep_depth++;

		// now go through all the other relations recursively
		foreach($_tmp as $r_tbl)
		{
			$this->req_chain_dep($r_tbl);
		}
	}

	////
	// !recursively crawls through database table relations and returns false if it ends up in the table it started from
	// otherwise returns true
	function req_cyclic_dep($tbl)
	{
		if ($this->cyclic_used_map[$tbl] == $tbl)
		{
			return false;
		}

		$this->cyclic_used_map[$tbl] = $tbl;

		// make a local copy so we won't screw up internal pointers
		$_tmp = $this->arr["save_tables_rels"][$tbl];
		if (!is_array($_tmp))
		{
			// if we reached an end of relation return true, cause that means no cycle
			return true;
		}

		foreach($_tmp as $r_tbl)
		{
			if (!$this->req_cyclic_dep($r_tbl))
			{
				return false;
			}
		}

		// what, that's it? wow. no fuckin way this will work.
		return true;
	}

	/** generates the form for selecting the used filter

		@attrib name=sel_filter_search params=name default="0"

		@param id required acl="edit;view"
		@param page optional

		@returns


		@comment

	**/
	function gen_filter_search_sel($arr)
	{
		$page=(int)$page;
		extract($arr);
		$this->if_init($id, "filter_search_sel.tpl", "Vali kasutatav filter");

		$this->vars(array("LINE" => "")); $cnt=0;

		$per_page = 10;

		$total = $this->db_fetch_field("SELECT count(oid) as cnt FROM objects  WHERE status != 0 AND class_id = ".CL_SEARCH_FILTER,"cnt");
		$pages = $total / $per_page;
		for ($i=0; $i < $pages; $i++)
		{
			$this->vars(array(
				"from" => ($i*$per_page),
				"to" => min(($i+1)*$per_page, $total),
				"pageurl" => $this->mk_my_orb("sel_filter_search", array("id" => $id, "page" => $i))
			));
			if ($i == $page)
			{
				$pp.=$this->parse("SEL_PAGE");
			}
			else
			{
				$pp.=$this->parse("PAGE");
			}
		}
		$this->vars(array(
			"PAGE" => $pp,
			"SEL_PAGE" => ""
		));

		$this->db_query("SELECT oid,parent,name,comment FROM objects  WHERE status != 0 AND class_id = ".CL_SEARCH_FILTER." LIMIT ".($page*$per_page).",$per_page");
		while($row = $this->db_next())
		{
			$tar = array(0 => "");
			if (is_array($ops[$row["oid"]]))
			{
				foreach($ops[$row["oid"]] as $opid => $opname)
				{
					$tar[$opid] = $opname;
				}
			}

			$this->vars(array(
				"flt_name"	=> $row["name"],
				"flt_comment" => $row["comment"],
				"flt_location" => $row["parent"],
				"flt_id" => $row["oid"],
				"row"	=> $cnt,
				"checked" => checked($this->arr["search_filter"] == $row["oid"]),
			));
			$this->parse("LINE");
			$cnt+=2;
		}

		$this->vars(array(
			"reforb"	=> $this->mk_reforb("save_filter_search_sel", array("id" => $this->id,"page" => $page)),
		));

		return $this->do_menu_return();
	}

	/** saves the used filter for search form $id

		@attrib name=save_filter_search_sel params=name default="0"


		@returns


		@comment

	**/
	function save_filter_search_sel(&$arr)
	{
		extract($arr);
		$this->load($id);

		$this->arr["search_filter"] = $sel;

		$this->save();
		return $this->mk_orb("sel_filter_search", array("id" => $id,"page" => $page));
	}

	//Output mratakse niikuinii filtri poolt.
	function do_filter_search($entry_id, $output_id,$arr)
	{
		if (!$this->arr["search_filter"])
		{
			return "Kasutatav filter formile $this->id on m&auml;&auml;ramata";
		};
		$sf = get_instance("formgen/search_filter");
		$sf->id=$this->arr["search_filter"];

		$sf->__load_filter();
		$this->load_entry($entry_id);
		//Nd tuleb filtri osad kigu pealt mlus ra muuta ja
		// panna asemele see kamm, mille kasutaja sisestas

		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$this->arr["contents"][$row][$col]->get_els(&$elar);
				while (list(,$el) = each($elar))
				{
					if ($el->arr["part"]!="" && $el->arr["part"]!=-1)
					{
						//Nii. siin tuleb nd vaadata et kui filtri osa tp on 2 (date)
						if ($sf->filter["p".(int)$el->arr["part"]]["type"]==2)
						{
							$valx=$el->get_val();
						} else
						{
							$valx=$el->get_value();
						};

						if ($el->get_type() == "date")
						{
							$valx = $el->get_value(true);
						}
						$sf->filter["p".(int)$el->arr["part"]]["val"]=$valx;
						if ($GLOBALS["dbg_ft"]) {echo("blah part=".$el->arr["part"]." type= ".$sf->filter["p".(int)$el->arr["part"]]["type"]." val=".$valx);};
					};
				}
			}
		}

		if ($GLOBALS["dbg_ft"]) {echo("&uuml;le kantud filter=<pre>");print_r($sf->filter);echo("</pre>");};

		$arr["no_menu"]=1;
		$arr["dont_load_filter"]=1;

		$arr["this_page"]=$this->mk_my_orb("show_entry",array("id"=>$arr["id"],"entry_id"=>$entry_id,"op_id" => $arr["op_id"]));
		$arr["this_page_array"]=array("class" => "form", "action" => "show_entry","id" => $arr["id"],"entry_id"=>$entry_id,"op_id" => $arr["op_id"]);
		$arr["id"]=$sf->id;


		return $sf->orb_search($arr);
	}

	function get_linked2real_element_array()
	{
		$ret = array();
		$els = $this->get_all_els();
		foreach($els as $el)
		{
			$ret[$el->arr["linked_element"]] = $el->get_id();
		}
		return $ret;
	}

	////
	// !removes controller $controller for type $type from element $element in the loaded form
	function remove_controller_from_element($arr)
	{
		extract($arr);
		$el = $this->get_element_by_id($element);
		if ($el)
		{
			$this->arr["contents"][$el->get_row()][$el->get_col()]->remove_controller_from_element($arr);
			$this->arr["contents"][$el->get_row()][$el->get_col()]->prep_save();
		}
	}

	function get_current_chain_entry()
	{
		return $this->current_chain_entry;
	}

	function set_current_chain_entry($id)
	{
		$this->current_chain_entry = $id;
	}

	/**

		@attrib name=calendar params=name default="0"

		@param id required acl="edit;view"

		@returns


		@comment

	**/
	function gen_calendar($args = array())
	{
		extract($args);
		$this->if_init($id,"calendar.tpl", "Kalendris&auml;tungid");

		$_els = $this->get_all_elements(array("type" => 1));

		$els_start = $els_end = $els_count = $els_period = $els_release = array("0" => " -- Vali --");



		foreach($_els as $key => $val)
		{
			if ( ($val["type"] == "date") && ($val["subtype"] == "from") )
			{
				$els_start[$key] = $val["name"];
			};

			if ( ($val["type"] == "date") && ($val["subtype"] == "to") )
			{
				$els_end[$key] = $val["name"];
			};

			if ( ($val["type"] == "textbox") && ($val["subtype"] == "count") )
			{
				$els_count[$key] = $val["name"];
			};

			if ( ($val["type"] == "timeslice") && ($val["subtype"] == "period") )
			{
				$els_period[$key] = $val["name"];
			};

			if ( ($val["type"] == "timeslice") && ($val["subtype"] == "release") )
			{
				$els_release[$key] = $val["name"];
			};

		};

		if ($this->subtype == FSUBTYPE_CAL_CONF)
		{
			$fc = get_instance("formgen/form_calendar");
			$_cont = $fc->fg_define_calendar(array(
				"els_start" => &$els_start,
				"els_end" => &$els_end,
				"els_count" => &$els_count,
				"els_period" => &$els_period,
				"els_release" => &$els_release,
				"arr" => &$this->arr,
				"id" => $this->id,
				"all_els" => &$_els,
			));
			return $this->do_menu_return($_cont);
		};

		$ol = new object_list(array(
			"class_id" => CL_FORM,
			"site_id" => array(),
			"lang_id" => array()
		));
		$forms = $ol->names();

		$ol = new object_list(array(
			"class_id" => CL_FORM_CHAIN,
			"flags" => array(
				"mask" => OBJ_HAS_CALENDAR,
				"flags" => OBJ_HAS_CALENDAR
			),
			"site_id" => array(),
			"lang_id" => array()
		));
		$chains = $ol->names();

		$of_target_type = ($this->arr["of_target_type"]) ? $this->arr["of_target_type"] : "form";

		$lines = "";

		if ($this->subtype & FSUBTYPE_EV_ENTRY)
		{
			$ft = get_instance(CL_FORM_TABLE);
			$tables = $ft->get_form_tables_for_form($id);

			$q = "SELECT *,objects.name AS name FROM calendar2forms
				LEFT JOIN objects ON (calendar2forms.cal_id = objects.oid)
				WHERE form_id = '$id'";

			$this->db_query($q);
			while($row = $this->db_next())
			{
				$this->vars(array(
					"name" => $row["name"],
					"start" => $_els[$row["el_start"]]["name"],
					"end" => $_els[$row["el_end"]]["name"],
					"cnt" => $_els[$row["el_cnt"]]["name"],
					"table" => $tables[$row["ev_table"]],
					"rel" => $_els[$row["el_relation"]]["name"],
					"ch_link" => $this->mk_my_orb("edit_cal_rel",array("form_id" => $id,"id" => $row["id"])),
					"del_link" => $this->mk_my_orb("del_cal_rel",array("form_id" => $id,"id" => $row["id"])),
				));

				$lines .= $this->parse("LINE");
			};
		}
		elseif ($this->subtype & FSUBTYPE_CAL_CONF2)
		{
			$de = new date_edit(0);
			$de->configure(array(
				"year" => "",
				"month" => "",
				"day" => "",
			));

			if ($this->arr["cal_start"])
			{
				$start = $this->arr["cal_start"];
				$end = $this->arr["cal_end"] + 1;
			}
			else
			{
				list($_d,$_m,$_y) = explode("-",date("d-m-Y"));
				$start = mktime(0,0,0,$_m,$_d,$_y);
				$end = $start + 86400;
			};

			$this->vars(array(
				"start" => $de->gen_edit_form("start",$start,2000,2037),
				"end" => $de->gen_edit_form("end",$end,2000,2037),
				"count" => $this->arr["cal_count"],
				"period" => $this->arr["cal_period"],
			));
			$this->parse("DEFINE2");
		};


		$this->vars(array(
			//"roles" => $this->picker($this->subtype,$roles),
			"event_display_tables" => $this->picker($this->arr["event_display_table"],$tables),
			"event_start_els" => $this->picker($this->arr["event_start_el"],$date_els),
			"start_disabled" => disabled(sizeof($els_start) == 1),
			"end_disabled" => disabled(sizeof($els_end) == 1),
			"count_disabled" => disabled(sizeof($els_count) == 1),
			"period_disabled" => disabled(sizeof($els_period) == 1),
			"release_disabled" => disabled(sizeof($els_release) == 1),
			"els_start" => $this->picker($this->arr["el_event_start"],$els_start),
			"els_end" => $this->picker($this->arr["el_event_end"],$els_end),
			"els_count" => $this->picker($this->arr["el_event_count"],$els_count),
			"els_period" => $this->picker($this->arr["el_event_period"],$els_period),
			"els_release" => $this->picker($this->arr["el_event_release"],$els_release),
			"newlink" => $this->mk_my_orb("new_cal_rel",array("form_id" => $this->id)),
			"LINE" => $lines,
			"reforb"	=> $this->mk_reforb("submit_calendar", array("id" => $this->id))
		));
		$res = "";
		switch($this->subtype)
		{
			case FSUBTYPE_EV_ENTRY:
				$this->parse("ENTRY");
				break;

			case FSUBTYPE_CAL_CONF:
				$this->parse("DEFINE");
				break;

			default:
		};

		return $this->do_menu_return();
	}

	/**

		@attrib name=submit_calendar params=name default="0"


		@returns


		@comment

	**/
	function submit_calendar($args = array())
	{
		extract($args);
		$this->load($id);
		$this->arr["event_display_table"] = $event_display_table;
		$this->arr["event_start_el"] = $event_start_el;

		// those are defined for FSUBTYPE_CAL_CONF
		$this->arr["el_event_start"] = $el_event_start;
		$this->arr["el_event_end"] = $el_event_end;
		$this->arr["el_event_count"] = $el_event_count;
		$this->arr["el_event_period"] = $el_event_period;
		$this->arr["el_event_release"] = $el_event_release;

		$this->arr["amount_el"] = $amount_el;
		$this->arr["period_type"] = $period_type;
		$this->arr["release_type"] = $release_type;

		$this->arr["per_amount"] = $per_amount;
		$this->arr["per_unit_type"] = $per_unit_type;
		$this->arr["release_textbox"] = $release_textbox;
		$this->arr["release_unit_type"] = $release_unit_type;

		if ($this->subtype  & FSUBTYPE_CAL_CONF2)
		{
			$_start = (int)date_edit::get_timestamp($args["start"]);
			$_end = (int)date_edit::get_timestamp($args["end"]) - 1;
			$_count = (int)$args["count"];
			$_period = (int)$args["period"];

			$this->arr["cal_start"] = $_start;
			$this->arr["cal_end"] = $_end;
			$this->arr["cal_count"] = $_count;
			$this->arr["cal_period"] = $_period;
		};
		//print "start = $_start, end = $_end, count = $_count, period = $_period<br />";

		$this->save();
		return $this->mk_my_orb("calendar",array("id" => $id));

	}

	/** Adds or Edits a new event<->calendar relation

		@attrib name=new_cal_rel params=name default="0"

		@param form_id required type=int

		@returns


		@comment
		if id is set, we are editing, otherwise we are adding a new one

	**/
	/**

		@attrib name=edit_cal_rel params=name default="0"

		@param id required type=int
		@param form_id required type=int

		@returns


		@comment

	**/
	function edit_cal_rel($args = array())
	{
		extract($args);
		$this->if_init($form_id,"calendar_relation.tpl", "Kalendris&auml;tungid");

		$els = $this->get_all_elements(array("type" => 1));

		$fcal = get_instance("formgen/form_calendar");
		$c = $fcal->edit_calendar_relation(array(
			"els" => &$els,
			"id" => $id,
			"form_id" => $form_id,
		));

		return $this->do_menu_return($c);
	}

	/**

		@attrib name=submit_cal_rel params=name default="0"


		@returns


		@comment

	**/
	function submit_cal_rel($args = array())
	{
		$fcal = get_instance("formgen/form_calendar");
		$fcal->submit_calendar_relation($args);
		return $this->mk_my_orb("calendar",array("id" => $args["form_id"]));
	}

	/**

		@attrib name=del_cal_rel params=name default="0"

		@param id required type=int
		@param form_id required type=int

		@returns


		@comment

	**/
	function del_cal_rel($args = array())
	{
		extract($args);
		$q = "DELETE FROM calendar2forms WHERE form_id = '$form_id' AND id = '$id'";
		$this->db_query($q);
		return $this->mk_my_orb("calendar",array("id" => $form_id));
	}

	function show_s_res($arr)
	{
		extract($arr);

		$no_tags = false;

		if ($this->arr["sql_writer"] && $this->arr["sql_writer_form"])
		{
			$no_tags = true;
		}

		if ($this->type == FTYPE_SEARCH)
		{
			if ($this->arr["show_s_res_as_forms"])
			{
				$no_tags = true;
				$this->s_res_as_forms_eids = array();
				$this->s_res_as_forms_fid = 0;
			}
			$search_res = $this->do_search($entry_id, $op_id, $search_el, $search_val, $no_tags).$search_res;
			if ($this->arr["show_s_res_as_forms"])
			{
				$search_res = "<form action='reforb.".$this->cfg["ext"]."' method='POST' name='sr_".$this->id."'>".$search_res;
			}
			else
			if ($no_tags)
			{
				$search_res = "<form action='reforb.".$this->cfg["ext"]."' method='POST' name='tb_".$this->arr["table"]."'>".$search_res;
			}
		}
		else
		if ($this->type == FTYPE_FILTER_SEARCH)
		{
			$arr["no_form_tags"] = $no_tags;
			$search_res = $this->do_filter_search($entry_id, $op_id, $arr);
			if ($no_tags)
			{
				$search_res = "<form action='reforb.".$this->cfg["ext"]."' method='POST' name='tb_".$this->arr["table"]."'>".$search_res;
			}
		}
		if ($this->arr["sql_writer"] && $this->arr["sql_writer_form"])
		{
			$tf = get_instance(CL_FORM);
			$search_res .= "<br />".$tf->gen_preview(array(
				"id" => $this->arr["sql_writer_form"],
				"tpl" => "show_noform.tpl",
			));
		}
		if ($this->arr["show_s_res_as_forms"])
		{
			$search_res.= $this->mk_reforb("submit_sr", array(
				"id" => $arr["id"],
				"entry_id" => $arr["entry_id"],
				"return_url" => aw_global_get("REQUEST_URI"),
				"sr_fid" => $this->s_res_as_forms_fid,
				"sr_eids" => $this->s_res_as_forms_eids
			));
			$search_res.="</form>";
		}
		else
		if ($no_tags)
		{
			$search_res.= $this->mk_reforb("submit_writer", array(
				"id" => $arr["id"],
				"entry_id" => $arr["entry_id"],
				"section" => aw_global_get("section")
			));
			$search_res.="</form>";
		}

		if ($this->arr["show_form_with_results"])
		{
			// also, since here we can change content without the url changing
			// let squid know this
			if ( aw_ini_get("config.use_squid"))
			{
				$ma = aw_ini_get("config.http_cache_max_age");
				session_cache_limiter("must-revalidate, max-age=".$ma);
				header("Cache-Control: must-revalidate, max-age=".$ma);
				header("Expires: ".gmdate("D, d M Y H:i:s",time()+$ma)." GMT");
			}
			$search_res = $this->gen_preview(array(
				"id" => $id,
				"entry_id" => $entry_id,
			)).$search_res;
		}
		return $search_res;
	}

	/** handles sql writer form submits

		@attrib name=submit_writer params=name default="0"

		@param id required
		@param entry_id optional

		@returns


		@comment

	**/
	function submit_writer($arr)
	{
		extract($arr);
		$seids = array();
		if (is_array($sel))
		{
			foreach($sel as $seid => $one)
			{
				if ($one == 1)
				{
					$seids[$seid] = $seid;
				}
			}
		}

		$this->load($id);
//		echo "load $id <br />";

		global $HTTP_POST_VARS;
//		echo "post vars = <pre>", var_dump($HTTP_POST_VARS),"</pre> <br />";
		// process the writer form entry
		$wrf = get_instance(CL_FORM);
		$wrf->process_entry(array(
			"id" => $this->arr["sql_writer_form"],
		));
//		echo "proc entry for ", $this->arr["sql_writer_form"]," <br />";
		$writer_entry_id = $wrf->entry_id;

		// load the form whose entries we will change
//		echo "load to write form ",$wrf->arr["sql_writer_writer_form"]," <br />";

		$wrf = get_instance(CL_FORM);
		$wrf->load($this->arr["sql_writer_form"]);
		$wrf->load_entry($writer_entry_id);

		$wrf->ef = get_instance(CL_FORM);
		$wrf->ef->load($wrf->arr["sql_writer_writer_form"]);

		// now we must load all selected entries
		// and for each
		// calculate the value in the writer form based on the entered elements value and let controllers process it
		// and then save the entry
		foreach($seids as $seid)
		{
			aw_global_set("current_writer_entry", $seid);
			$changeset = array();

			$wrf_els = $wrf->get_all_els();

			$wrf->ef->load_entry($seid);

			foreach($wrf_els as $el)
			{
				$wrt_to_el = $el->get_writer_element();
				$wrt_to_el_ref = $wrf->ef->get_element_by_id($wrt_to_el);
				if (is_object($wrt_to_el_ref))
				{
					aw_global_set("writer_current_element_prev_value", $wrt_to_el_ref->get_value());
				}
				else
				{
					aw_global_set("writer_current_element_prev_value", false);
				}

				if ($wrt_to_el)
				{
					$wrt_el_val = $el->get_val(array(),true);

					$changeset[$wrt_to_el] = $wrt_el_val;
				}
			}
			foreach($changeset as $ch_el => $ch_el_val)
			{
				$wrf->ef->set_element_value($ch_el, $ch_el_val, true);
				$wrf->ef->entry[$ch_el] = $ch_el_val;
			}
			$wrf->ef->process_entry(array(
				"id" => $wrf->arr["sql_writer_writer_form"],
				"entry_id" => $seid,
				"no_load_form" => true,
				"no_load_entry" => true,
				"no_process_entry" => true
			));
		}

		if (($butt_delete != "" || $butt_delete_x > 0) && is_array($sel))
		{
			foreach($sel as $_id => $one)
			{
				if ($one == 1)
				{
					if (!$form_id_for_entries)
					{
						$form_id_for_entries = $this->get_form_for_entry($_id);
					}
					$this->do_delete_entry($form_id_for_entries, $_id);
				}
			}
		}

		// and finally, call the search func so the search results will be shown again
		if ($this->arr["sql_writer_redirect_after"] != "")
		{
			return $this->arr["sql_writer_redirect_after"];
		}
		else
		{
			return $this->mk_my_orb("show_entry", array("id" => $id, "entry_id" => $entry_id, "op_id" => 1, "section" => $section));
		}
	}

	////
	// !returns all the entry elements that are connected to the loaded output
	// returns array[form_id][el_id] = el_id
	function get_op_linked_elements()
	{
		$ret = array();
		for ($row = 0; $row < $this->output["rows"]; $row++)
		{
			for ($col = 0; $col < $this->output["cols"]; $col++)
			{
				$op_cell = $this->output[$row][$col];
				for ($i=0; $i < $op_cell["el_count"]; $i++)
				{
					$lf = $op_cell["elements"][$i]["linked_form"];
					$le = $op_cell["elements"][$i]["linked_element"];
					$ret[$lf][$le] = $le;
				}
			}
		}
		return $ret;
	}

	////
	// !this checks if the table $tb is a formgen created table and if so, returns the form's name
	// this is used when showing the user the tables where the form should write from/to
	function get_fg_tblname($tb)
	{
		if (!(substr($tb,0,5) == "form_" && substr($tb,-8) == "_entries"))
		{
			return $tb;
		}
		else
		{
			// get form id
			preg_match("/form_(\d*)_entries/", $tb, $mt);
			if (!isset($this->form_name_cache))
			{
				$this->form_name_cache = $this->get_flist();
			}

			if (isset($this->form_name_cache[$mt[1]]))
			{
				return "form::".$this->form_name_cache[$mt[1]];
			}
		}
		return false;
	}

	////
	// !this checks if the column $col is a formgen created table column and if so, returns the element's name
	// this is used when showing the user the tables where the form should write from/to
	function get_fg_colname($col)
	{
		$el = false;
		if (substr($col, 0, 3) == "el_")
		{
			$el = true;
			$prefix = "el_";
		}
		else
		if (substr($col, 0, 3) == "ev_")
		{
			$el = true;
			$prefix = "ev_";
		}

		if ($el)
		{
			if (!isset($this->form_element_name_cache))
			{
				$ol = new object_list(array(
					"class_id" => CL_FORM_ELEMENT,
					"site_id" => array(),
					"lang_id" => array()
				));
				$this->form_element_name_cache = $ol->names();
			}

			if (isset($this->form_element_name_cache[substr($col, 3)]))
			{
				$col = $prefix.$this->form_element_name_cache[substr($col, 3)];
			}
		}
		return $col;
	}

	////
	// !returns the name of the form, as written in html
	function get_form_html_name()
	{
		return $this->form_html_name;
	}

	////
	// !sets the name of the form, as written in html
	function set_form_html_name($name)
	{
		$this->form_html_name = $name;
	}

	////
	// !uses the new search engine to do the search for the form that is loaded
	// and the search terms are specified in entry $entry_id
	// the difference between new_do_search and this is that new_do_search returns html for the search results
	// but this returns an array of matching entry id's
	// parameters:
	//    entry_id - the entry for the loaded form that contains the search terms. if not specified, loaded entry is used
	function new_search($arr)
	{
		extract($arr);
		if ($entry_id)
		{
			$this->load_entry($entry_id);
		}

		$ret = array();
		$sql = $this->get_search_query(array(
			"ret_id_only" => true
		));

		$this->matched_chain_entries = array();
//		echo "sql = $sql <br />";
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$ret[] = $row["entry_id"];
			if ($row["chain_entry_id"])
			{
				$this->matched_chain_entries[] = $row["chain_entry_id"];
			}
		}

		$this->search_form = $this->arr["start_search_relations_from"];
		return $ret;
	}

	function get_last_search_chain_entry_ids()
	{
		return is_array($this->matched_chain_entries) ? array_unique($this->matched_chain_entries) : array();
	}

	/** Generates a preview of the form and adds the formgen menubars to it

		@attrib name=preview_form params=name default="0"

		@param id required

		@returns


		@comment

	**/
	function preview_form($args = array())
	{
		extract($args);
		$this->if_init($id,"show.tpl", "Eelvaade");
		$this->vars(array(
			"LINE" => $this->gen_preview(array("id" => $id)),
		));
		return $this->do_menu_return();
	}

	/**

		@attrib name=joins params=name default="0"

		@param id required acl="edit;view"

		@returns


		@comment

	**/
	function joins($arr)
	{
		extract($arr);
		$this->if_init($id, "joins.tpl", "Seosed");

		$ol = new object_list(array(
			"class_id" => CL_FORM,
			"site_id" => array(),
			"lang_id" => array()
		));
		$nms = $ol->names();

		$ol = new object_list(array(
			"class_id" => CL_FORM_ELEMENT,
			"site_id" => array(),
			"lang_id" => array()
		));
		$elnms = $ol->names();

		$sf = $this->arr["start_search_relations_from"];
		if ($this->type == FTYPE_ENTRY)
		{
			$sf = $id;
		}
		$this->build_form_relation_tree($sf);

		foreach($this->form_rel_tree as $_ff_id => $_td)
		{
			foreach($_td as $_tf_id => $_jdat)
			{
				$froe = $_jdat["el_from"];
				if ($froe != "chain_id")
				{
					$froe = $elnms[$froe];
					if ($froe == "")
					{
						$froe = $this->db_fetch_field("SELECT name FROM objects WHERE oid = ".$_jdat["el_from"], "name");
					}
				}
				$toe = $_jdat["el_to"];
				if ($toe != "chain_id")
				{
					$toe = $elnms[$toe];
					if ($toe == "")
					{
						$toe = $this->db_fetch_field("SELECT name FROM objects WHERE oid = ".$_jdat["el_to"], "name");
					}
				}

				$relid = "rel_".$_jdat["form_from"]."_".$_jdat["form_to"]."_".$_jdat["el_from"]."_".$_jdat["el_to"];
				$this->vars(array(
					"from_change" => $this->mk_my_orb("change", array("id" => $_jdat["form_from"])),
					"from_form" => $nms[$_jdat["form_from"]],
					"to_change" => $this->mk_my_orb("change", array("id" => $_jdat["form_to"])),
					"to_form" => $nms[$_jdat["form_to"]],
					"from_el" => $froe,
					"to_el" => $toe,
					"relid" => $relid,
					"checked" => checked($this->arr["leave_out_joins"][$relid] == $relid)
				));
				$l .= $this->parse("LINE");
			}
		}

		$this->vars(array(
			"LINE" => $l,
			"reforb" => $this->mk_reforb("submit_joins", array("id" => $id))
		));

		return $this->do_menu_return();
	}

	/**

		@attrib name=submit_joins params=name default="0"


		@returns


		@comment

	**/
	function submit_joins($arr)
	{
		extract($arr);

		$this->load($id);
		$this->arr["leave_out_joins"] = $this->make_keys($no_join);
		$this->save();

		return $this->mk_my_orb("joins", array("id" => $id));
	}

	/**

		@attrib name=export params=name default="0"

		@param id required

		@returns


		@comment

	**/
	function export($arr)
	{
		extract($arr);
		$this->read_template("export.tpl");

		$this->id = $id;

		$this->vars(array(
			"to_db" => checked(true),
			"reforb" => $this->mk_reforb("submit_export", array("id" => $id))
		));

		return $this->do_menu_return();
	}

	/**

		@attrib name=submit_export params=name default="0"


		@returns


		@comment

	**/
	function submit_export($arr)
	{
		extract($arr);

		$inst = false;
		if ($to == "db")
		{
			$inst = get_instance("formgen/form_export_db");
		}

		if (is_object($inst))
		{
			return $inst->do_export($arr);
		}
		return $this->mk_my_orb("export", array("id" => $id));
	}

	function _create_translations()
	{
		// this will only work if the form is not saved to other tables...
		if (!$this->arr["save_table"])
		{
			$tb = "form_".$this->id."_entries";

			// check if lang_id column exists, if not, add it!
			$tbl = $this->db_get_table($tb);
			if (!isset($tbl["fields"]["lang_id"]))
			{
				$this->db_add_col($tb, array(
					"name" => "lang_id",
					"type" => "int",
					"default" => $this->lang_id
				));
				$this->db_add_index($tb, array(
					"name" => "lang_id",
					"col" => "lang_id"
				));
				// now we gots to drop the primary key on id column as well..
				$sql = "ALTER TABLE $tb DROP PRIMARY KEY";
				$this->db_query($sql);
			}

			$e2l = array();

			$sql = "SELECT id, lang_id FROM $tb";
			$this->db_query($sql);
			while ($row = $this->db_next())
			{
				$e2l[$row['id']][$row['lang_id']] = $row["id"];
			}

			$l = get_instance("languages");
			$la = $l->get_list();

			// now go over all entries and check if they have data for all languages.
			// if not, then add it.
			foreach($e2l as $eid => $ldat)
			{
				foreach($la as $lang_id => $lang_dat)
				{
					if (!isset($ldat[$lang_id]))
					{
						// we must copy data from the default language (the form's language)
						$sql = "SELECT * FROM $tb WHERE lang_id = ".$this->lang_id." AND id = $eid";
						$row = $this->db_fetch_row($sql);

						$cols = array();
						$vals = array();
						foreach($row as $col => $val)
						{
							if ($col != "id" && $col != "lang_id")
							{
								$cols[] = $col;
								$vals[] = $val;
							}
						}
						$this->quote($vals);
						$_cols = join(",", $cols);
						$_vals = join(",", map("'%s'",$vals));
						$sql = "INSERT INTO $tb (id,lang_id,$_cols) VALUES('$eid','$lang_id',$_vals)";
						$this->db_query($sql);
					}
				}
			}
		}
	}

	function on_language_add($args)
	{
		// when the user adds a language, we need to go over all forms defined in the system and if they
		// are translatable forms, then add defs for the new language to all entries
		$this->db_query("SELECT oid FROM objects WHERE class_id = ".CL_FORM." AND status != 0");
		while ($row = $this->db_next())
		{
			$f = get_instance(CL_FORM);
			$f->load($row["oid"]);
			if ($f->arr["is_translatable"])
			{
				$f->_create_translations();
			}
		}
	}

	function _do_value_controllers()
	{
		for($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$this->arr["contents"][$row][$col]->upd_value();
			}
		}
	}

	/**

		@attrib name=submit_sr params=name default="0"

		@param id required
		@param entry_id optional

		@returns


		@comment

	**/
	function submit_sr($arr)
	{
		extract($arr);

		$ar = new aw_array($sr_eids);
		foreach($ar->get() as $eid)
		{
			$prefix = "sr_".$eid."_";
			$f = get_instance(CL_FORM);
			$f->process_entry(array(
				"id" => $sr_fid,
				"entry_id" => $eid,
				"prefix" => $prefix,
			));
		}
		return $arr["return_url"];
	}

	/** deletes the form entry

		@attrib name=delete_entry

		@param id required type=int acl=view
		@param entry_id required type=int acl=delete
		@param after required

	**/
	function delete_entry($arr)
	{
		return parent::delete_entry($arr);
	}
};	// class ends
?>
