<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_output.aw,v 1.21 2008/08/14 13:04:44 tarvo Exp $
classload("formgen/form_base");
/*
@classinfo  maintainer=kristo
*/
class form_output extends form_base 
{
	function form_output()
	{
		$this->form_base();
		$this->sub_merge = 1;
		$this->is_form_output = true;	// so that when something needs to tell the deifference it can

		if (!$this->controller_instance)
		{
			$this->controller_instance = get_instance(CL_FORM_CONTROLLER);
		}
		$this->style_instance = get_instance(CL_STYLE);
	}

	/** Kuvab vormi, kust saab valida v2ljundi tyypide vahel 
		
		@attrib name=new params=name default="0"
		
		@param parent required acl="add"
		
		@returns
		
		
		@comment
		regrettably I had to call this add, or ORB will break

	**/
	function add($args = array())
	{
		extract($args);
		$this->read_template("output_category.tpl");
		$this->mk_path($parent,"Vali v&auml;ljundi t&uuml;&uuml;p");
	
		$this->vars(array(
			"reforb" => $this->mk_reforb("choose_output_type",array("parent" => $parent)),
		));
		return $this->parse();
	}

	/** Soltuvalt eelnevast vormist valitud tyybile teeb redirecti oigesse kohta 
		
		@attrib name=choose_output_type params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function choose_output_type($args = array())
	{
		extract($args);
		if ($type == "html")
		{
			$url = $this->mk_my_orb("add_html",array("parent" => $parent));
		}
		elseif ($type == "xml")
		{
			$url = $this->mk_my_orb("add_xml",array("parent" => $parent));
		};
		return $url;
	}

	/**  
		
		@attrib name=add_xml params=name default="0"
		
		@param parent required
		
		@returns
		
		
		@comment
		

	**/
	/**  
		
		@attrib name=edit_xml params=name default="0"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function edit_xml($args = array())
	{
		extract($args);
		$this->read_template("add_xml_output.tpl");
		$this->mk_path($id,"Koosta XML v&auml;ljund");
		if ($id)
		{
			$odata = obj($id);
			$this->vars(array(
				"adminurl" => $this->mk_my_orb("xml_op",array("id" => $id)),
			));
		}
		$sel = ($odata->meta("forms")) ? array_flip($odata->meta("forms")) : array();
		$this->vars(array(
			"name" => $odata->name(),
			"comment" => $odata->comment(),
			"admin" => (isset($id)) ? $this->parse("admin") : "",
			"forms" => $this->multiple_option_list($sel, $this->get_list(FTYPE_ENTRY,true,true)),
			"reforb" => $this->mk_reforb("submit_xml",array("parent" => $parent,"id" => $id)),
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_xml params=name default="0"
		
		
		@returns
		
		
		@comment
		

	**/
	function submit_xml($args = array())
	{
		extract($args);
		
		if ($id)
		{
			$o = obj($id);
			$o->set_name($name);
			$o->set_comment($comment);
		}
		else
		{
			$o = obj();
			$o->set_parent($parent);
			$o->set_name($name);
			$o->set_comment($comment);
			$o->set_class_id(CL_FORM_XML_OUTPUT);
		};

		$o->set_meta("forms", $forms);
		$id = $o->save();
	
		return $this->mk_my_orb("edit_xml",array("id" => $id));
	}

	/**  
		
		@attrib name=xml_op params=name default="0"
		
		@param id required
		
		@returns
		
		
		@comment
		

	**/
	function xml_op($args = array())
	{
		$this->mk_path($id,"Koosta XML v&auml;ljund");
		$this->read_template("xml_output.tpl");
		extract($args);
		$odata = obj($id);
		$xdata = $odata->meta();

		if (is_array($xdata["forms"]))
		{
			$forms = "";
			foreach($xdata["forms"] as $key => $val)
			{
				$el = "";
				$this->load($val);
				$name = $this->name;
				$this->vars(array("fname" => "$name ($val)"));
				for ($i=0; $i < $this->arr["rows"]; $i++)
				{
					$cols="";
					for ($a=0; $a < $this->arr["cols"]; $a++)
					{
						if (!($arr = $this->get_spans($i, $a)))
						{
							continue;
						}
 
						$cell = &$this->arr["contents"][$arr["r_row"]][$arr["r_col"]];
						$els = $cell->get_elements();
						if (is_array($els))
						{
							foreach($els as $key => $val)
							{
								if ( ($val["type"] == "textbox") || ($val["type"] == "textarea") )
								{
									$jrk = ($xdata["data"]["jrk"][$val["id"]]) ? $xdata["data"]["jrk"][$val["id"]] : 0;
									if ($xdata["data"]["tag"][$val["id"]])
									{
										$tag = $xdata["data"]["tag"][$val["id"]];
									}
									else
									{
										// tagi nime leidmiseks stripime koigepealt 
										// nimest thikud
										$tag = strtolower(str_replace(" ","",$val["name"]));
										if (preg_match("/(^\w*)/",$tag,$matches))
										{
											$tag = $matches[1];
										};
									};

									if ( isset($xdata["data"]["active"][$val["id"]]) )
									{
										$checked = ($xdata["data"]["active"][$val["id"]]) ? "checked" : "";
									}
									else
									{
										$checked = "checked";
									};										
									$this->vars(array(
										"id" => $val["id"],
										"jrk" => $jrk,
										"checked" => $checked,
										"tag" => $tag,
										"name" => $val["name"],
										"type" => $val["type"],
									));
									$el .= $this->parse("element");
								};
							};
						};
					};
				}
				$this->vars(array(
					"element" => $el,
				));
				$forms .= $this->parse("form");
			};
		};
		$this->vars(array(
			"form" => $forms,
			"edurl" => $this->mk_my_orb("edit_xml",array("id" => $id)),
			"reforb" => $this->mk_reforb("submit_xml_output",array("id" => $id)),
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_xml_output params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_xml_output($args = array())
	{
		extract($args);
		$real_act = array();
		foreach($exists as $key => $val)
		{
			$real_act[$key] = $active[$key];
		};
		$data = array(
			"jrk" => $jrk,
			"tag" => $tag,
			"active" => $real_act,
		);
		$o = obj($id);
		$o->set_meta("data", $data);
		$o->save();
		return $this->mk_my_orb("xml_op",array("id" => $id));
	}

	/** Kuvab vormi, kust saab valida HTML v2ljundi jaoks vajalikud atribuudid. 
		
		@attrib name=add_html params=name default="0"
		
		@param parent required
		
		@returns
		
		
		@comment

	**/
	function add_html($arr)
	{
		extract($arr);
		$this->read_template("add_output.tpl");
		$this->mk_path($parent,LC_FORM_OUTPUT_ADD_OUT_STYLE);

		$st = get_instance(CL_STYLE);

		$this->vars(array(
			"reforb" => $this->mk_reforb("add_html_step2", array("parent" => $parent,"reforb" => 0)),
			"forms" => $this->multiple_option_list(array(), $this->get_list(FTYPE_ENTRY,true,true)),
			"forms2" => $this->multiple_option_list(array(), $this->get_list(FTYPE_ENTRY,true,true)),
			"styles" => $this->picker(0,$st->get_select(0,ST_TABLE)),
			"meth" => "GET"
		));
		$this->parse("ADD");
		return $this->parse();
	}

	/** Kuvab vormi, kust saab valida HTML v2ljundi jaoks vajalikud atribuudid. ja nyyd ka juba valitud alusformide j2rjekorda 
		
		@attrib name=add_html_step2 params=name all_args="1" default="0"
		
		
		@returns
		
		
		@comment

	**/
	function add_html_step2($arr)
	{
		extract($arr);
		$this->read_template("add_output.tpl");
		$this->mk_path($parent,LC_FORM_OUTPUT_ADD_OUT_STYLE);

		$st = get_instance(CL_STYLE);

		$els = array();

		$bof = array();

		// avoid the error message if a baseform was not chosen.
		// this is better than putting the whole cycle into one if block
		$baseform = (is_array($baseform)) ? $baseform : array();

		foreach($baseform as $bf)
		{
			// don't ask me why but for some weirdass reason the forms get duplicated in the array twice. so we avoid that
			if (!$bof[$bf])
			{
				$this->vars(array(
					"form_id" => $bf,
					"form_name" => $this->db_fetch_field("SELECT name FROM objects WHERE oid = $bf","name")
				));
				$a2.=$this->parse("ADD_2_LINE");
				$bof[$bf] = $bf;

				$f = get_instance(CL_FORM);
				$f->load($bf);
				$els+=$f->get_all_elements();
			}
		}

		foreach($forms as $fo)
		{
			$fos[$fo] = $fo;
		}

		$this->vars(array(
			"ADD_2_LINE" => $a2,
			"reforb" => $this->mk_reforb("submit", array("parent" => $parent)),
			"forms" => $this->multiple_option_list($fos, $this->get_list(FTYPE_ENTRY,true,false)),
			"forms2" => $this->multiple_option_list($bof, $this->get_list(FTYPE_ENTRY,true,false)),
			"styles" => $this->picker($table_style,$st->get_select(0,ST_TABLE)),
			"meth" => "POST",
			"name" => $name,
			"comment" => $comment,
			"els" => $this->multiple_option_list($els, $els)
		));
		$this->parse("ADD");
		$this->parse("ADD2");
		return $this->parse();
	}

	/**  
		
		@attrib name=submit params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit($arr)
	{
		extract($arr);

		if ($id)
		{
			$o = obj($id);
			$o->set_name($name);
			$o->set_comment($comment);
			$o->save();
		}
		else
		{
			$o = obj();
			$o->set_name($name);
			$o->set_parent($parent);
			$o->set_comment($comment);
			$o->set_class_id(CL_FORM_OUTPUT);
			$id = $o->save();

			$this->db_query("INSERT INTO form_output(id) VALUES($id)");
			$this->load_output($id);
			$elements = $this->make_keys($elements);

			if (is_array($baseform))
			{
				// if the user selected a form to base this op on, make it look like the form.
				$ord = (is_array($ord)) ? $ord : array();
				asort($ord);
				$f = get_instance(CL_FORM);
				$this->output= array();
				foreach($ord as $bfid => $or)
				{
					$f->load($bfid);

					$base_row = $this->output["rows"];
	
					$this->output["rows"] += $f->arr["rows"];
					$this->output["cols"] = max($f->arr["cols"],$this->output["cols"]);

					for ($row =0; $row < $f->arr["rows"]; $row++)
					{
						for ($col =0; $col < $f->arr["cols"]; $col++)
						{
							$elarr=array();
							$f->arr["contents"][$row][$col]->get_els(&$elarr);
							$this->output[$base_row+$row][$base_col+$col]["style"] = $f->arr["contents"][$row][$col]->get_style();

							$num=0;
							foreach($elarr as $el)
							{
								if ($elements[$el->get_id()] == $el->get_id())
								{
									$this->output[$base_row+$row][$base_col+$col]["elements"][$num] = $el->get_props();
									$this->output[$base_row+$row][$base_col+$col]["elements"][$num]["linked_form"] = $bfid;
									$this->output[$base_row+$row][$base_col+$col]["elements"][$num]["linked_element"] = $el->get_id();
									$num++;
								}
							}
							$this->output[$base_row+$row][$base_col+$col]["el_count"] = $num;
							$this->output["map"][$base_row+$row][$base_col+$col]["col"] = $f->arr["map"][$row][$col]["col"]+$base_col;
							$this->output["map"][$base_row+$row][$base_col+$col]["row"] = $f->arr["map"][$row][$col]["row"]+$base_row;
						}
					}
				}
				$this->save_output($id);
			}
		}

		$this->db_query("DELETE FROM output2form WHERE op_id = $id");
		if (is_array($forms))
		{
			foreach($forms as $fid)
			{
				$this->db_query("INSERT INTO output2form (op_id, form_id) VALUES($id,'$fid')");
			}
		}
	
		// FIXME: we load and save the output twice if that's a new form
		$this->load_output($id);
		$this->output["table_style"] = $table_style;
		$this->output["has_aliasmgr"] = $has_aliasmgr;
		$this->output["has_controllers"] = $has_controllers;
		$this->output["session_value"] = $session_value;
		$this->output["session_form"] = $session_form;

		$this->save_output($id);
		return $this->mk_orb("change", array("id" => $id));
	}

	/**  
		
		@attrib name=change params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function change($arr)
	{
		extract($arr);
		$object = obj($id);
		if ($object->class_id() == CL_FORM_XML_OUTPUT )
		{
			return $this->mk_my_orb("edit_xml",array("id" => $id));
		};

		$this->load_output($id);
		$this->read_template("add_output.tpl");
		$this->mk_path($this->parent, LC_FORM_OUTPUT_CHANGE_OUT_STYLE);

		$st = get_instance(CL_STYLE);
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit", array("id" => $id)),
			"name" => $this->name,
			"comment" => $this->comment,
			"admin" => $this->mk_orb("admin_op", array("id" => $id)),
			"has_aliasmgr" => checked($this->output["has_aliasmgr"]),
			"has_controllers" => checked($this->output["has_controllers"]),
			"session_value" => checked($this->output["session_value"]),
			"session_form" => $this->picker($this->output["session_form"],$this->get_flist(array("addempty" => true))),
			"forms" => $this->multiple_option_list($this->get_op_forms($id), $this->get_list(FTYPE_ENTRY,false,true)),
			"styles" => $this->picker($this->output["table_style"],$st->get_select(0,ST_TABLE)),
			"meth" => "POST"
		));
		$this->parse("CHANGE");
		return $this->parse();
	}

	function debug_map_print()
	{
		echo "<table border=1>";
		for ($r=0; $r < $this->output["rows"]; $r++)
		{
			echo "<tr>";
			for ($c=0; $c < $this->output["cols"]; $c++)
				echo "<td>(", $this->output["map"][$r][$c]["row"], ",",$this->output["map"][$r][$c]["col"],")</td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	/** generates the grid used in changing the output $id 
		
		@attrib name=admin_op params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function admin($arr)
	{
		extract($arr);
		$this->read_template("output_grid.tpl");
		$this->load_output($id);
		$this->mk_path($this->parent,sprintf(LC_FORM_OUTPUT_OUTPUT_ADMIN,$this->mk_orb("change", array("id" => $id))));
		$op_id = $id;

		// vaja on arrayd el_id => el_name k6ikide elementide kohta, mis on selle v2ljundi juurde valitud formides
		$elarr = $this->mk_elarr($id);

		// $this->debug_map_print();

		// put all styles in this form in an array so they will be faster to use
		$style = get_instance(CL_STYLE);
		$style_select = $style->get_select(0,ST_CELL, true);
		$this->vars(array(
			"styles" => $this->picker(0,$style_select)
		));
		// tabeli ylemine rida delete column nuppudega
		for ($a=0; $a < $this->output["cols"]; $a++)
		{
			$fc = "";
			if ($a == 0)
			{
				$this->vars(array("add_col" => $this->mk_orb("add_col", array("id" => $op_id, "after" => -1))));
				$fc = $this->parse("FIRST_C");
			}

			$this->vars(array(
				"add_col" => $this->mk_orb("add_col", array("id" => $op_id, "after" => $a)),
				"del_col" => $this->mk_orb("del_col", array("id" => $op_id, "col" => $a)),
				"FIRST_C" => $fc
			));
			$this->parse("DC");
		}

		for ($row = 0; $row < $this->output["rows"]; $row++)
		{
			$this->vars(array("COL" => ""));
			for ($col = 0; $col < $this->output["cols"]; $col++)
			{
				if (!($arr = $this->get_spans($row, $col, $this->output["map"], $this->output["rows"], $this->output["cols"])))
				{
					// kui see cell on peidus m6ne teise all, siis 2rme seda joonista
					continue;
				}
				
				$rcol = (int)$arr["r_col"];
				$rrow = (int)$arr["r_row"];
				$cell = $this->output[$rrow][$rcol];

				$element="";
				for ($i=0; $i < $cell["el_count"]; $i++)
				{
					$this->vars(array(
						"el_name" => $cell["elements"][$i]["name"],
						"el_text" => $cell["elements"][$i]["text"],
						"col" => $rcol, 
						"row" => $rrow,
						"el_cnt" => $i
					));
					$element.=$this->parse("ELEMENT");
				}

				$this->vars(array(
					"colspan" => $arr["colspan"], 
					"rowspan" => $arr["rowspan"],
					"num_els_plus3" => $cell["el_count"]+5,
					"cell_id" => ($rrow."_".$rcol), 
					"ELEMENT" => $element, 
					"exp_left"	=> $this->mk_orb("exp_left", array("id" => $op_id, "col" => $col, "row" => $row)),
					"exp_right"	=> $this->mk_orb("exp_right", array("id" => $op_id, "col" => $col, "row" => $row)),
					"exp_up"	=> $this->mk_orb("exp_up", array("id" => $op_id, "col" => $col, "row" => $row)),
					"exp_down"	=> $this->mk_orb("exp_down", array("id" => $op_id, "col" => $col, "row" => $row)),
					"split_ver"	=> $this->mk_orb("split_cell_ver", array("id" => $id, "col" => $col, "row" => $row)),
					"split_hor"	=> $this->mk_orb("split_cell_hor", array("id" => $id, "col" => $col, "row" => $row)),
					"ch_cell" => $this->mk_my_orb("ch_cell", array("id" => $id, "col" => (int)$rcol, "row" => (int)$rrow)),
					"addel" => $this->mk_my_orb("add_element", array("id" => $id, "col" => (int)$rcol, "row" => (int)$rrow)),
					"style_name" => $style_select[$cell["style"]]
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
				if ($row != 0)
				{
					$eu = $this->parse("EXP_UP");
				}
				$el = "";
				if ($col != 0)
				{
					$el = $this->parse("EXP_LEFT");
				}
				$er = "";
				if (($col+$arr["colspan"]) != $this->output["cols"])
				{
					$er = $this->parse("EXP_RIGHT");
				}
				$ed = "";
				if (($row+$arr["rowspan"]) != $this->output["rows"])
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
				$spls = "";
				if ($sh != "" || $sv != "")
				{
					$spls = $this->parse("SPLITS");
				}
				$this->vars(array("SPLITS" => $spls));
				$this->parse("COL");
			}
			$fi = "";
			if ($row==0)
			{
				$this->vars(array("add_row" => $this->mk_orb("add_row", array("id" => $op_id, "after" => -1))));
				$fi = $this->parse("FIRST_R");
			}
			$this->vars(array(
				"add_row" => $this->mk_orb("add_row", array("id" => $op_id, "after" => $row)),
				"del_row" => $this->mk_orb("del_row", array("id" => $op_id, "row" => $row)),
				"FIRST_R" => $fi
			));
			$this->parse("LINE");
		}

		$this->vars(array(
			"aliasmgr" => $this->mk_my_orb("aliasmgr",array("id" => $id)),
		));

		$css = get_instance(CL_CSS);
		$this->vars(array(
			"reforb"	=> $this->mk_reforb("submit_admin", array("id" => $id, "op_id" => $op_id)),
			"addr_reforb" => $this->mk_reforb("add_n_rows", array("id" => $id,"after" => $this->output["rows"]-1)),
			"addc_reforb" => $this->mk_reforb("add_n_cols", array("id" => $id,"after" => $this->output["cols"]-1)),
			"folders" => $this->picker(0,$this->get_menu_list(false,true)),
			"css_styles" => $this->picker(0,$css->get_select(true)),
			"ALIASMGR" => ($this->output["has_aliasmgr"]) ? $this->parse("ALIASMGR") : "",
			"change" => $this->mk_my_orb("change",array("id" => $id)),
			"translate" => $this->mk_my_orb("translate", array("id" => $id))
		));
		return $this->parse();
	}

	/** Wrapper for including alias manager in form output editing 
		
		@attrib name=aliasmgr params=name default="0"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function aliasmgr($args = array())
	{
		extract($args);
		$this->read_template("output_aliasmgr.tpl");
		$this->vars(array(
			"aliasmgr_link" => $this->mk_my_orb("list_aliases",array("id" => $id),"aliasmgr"),
			"change" => $this->mk_my_orb("change",array("id" => $id)),
		));
		return $this->parse();

	}

	/**  
		
		@attrib name=add_n_rows params=name default="0"
		
		@param id required acl="edit;view"
		@param after optional
		@param count optional
		
		@returns
		
		
		@comment

	**/
	function add_n_rows($arr)
	{
		extract($arr);
		for ($i=0; $i < $nrows; $i++)
		{
			$this->add_row(array("id" => $id, "after" => $after));
		}
		return $this->mk_my_orb("admin_op", array("id" => $id));
	}

	/**  
		
		@attrib name=add_n_cols params=name default="0"
		
		@param id required acl="edit;view"
		@param after optional
		@param count optional
		
		@returns
		
		
		@comment

	**/
	function add_n_cols($arr)
	{
		extract($arr);
		for ($i=0; $i < $ncols; $i++)
		{
			$this->add_col(array("id" => $id, "after" => $after));
		}
		return $this->mk_my_orb("admin_op", array("id" => $id));
	}

	/** saves the output grid ($id) 
		
		@attrib name=submit_admin params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function submit_admin($arr)
	{
		extract($arr);
		$this->load_output($id);

		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				$cell = &$this->output[$row][$col];
				if ($sel[$row][$col] == 1)
				{
					$cell["style"] = $selstyle;
				}
				for ($i=0; $i < $cell["el_count"]; $i++)
				{
					$cell["elements"][$i]["text"] = $texts[$row][$col][$i];
					if ($cell["elements"][$i]["name"] != $names[$row][$col][$i])
					{
						$fe = get_instance("formgen/form_entry_element");
						$fe->do_change_name($names[$row][$col][$i], $cell["elements"][$i]["id"]);
						$cell["elements"][$i]["name"] = $names[$row][$col][$i];
					}
					if ($elsel[$row][$col][$i] == 1 && $setfolder)
					{
						$o = obj($cell["elements"][$i]["id"]);
						$o->set_parent($setfolder);
						$o->save(); 
					}
					else
					if ($elsel[$row][$col][$i] == 1 && $setcss !== "")
					{
						$cell["elements"][$i]["el_css_style"] = $setcss;
					}
				}
			}
		}
		
		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				$cell = &$this->output[$row][$col];
				for ($i=0; $i < $cell["el_count"]; $i++)
				{
					if ($elsel[$row][$col][$i] == 1 && isset($diliit))
					{
						// we must delete the element from this op.
						// we must also shift all other elements up one 
						for ($a=$i; $a < $cell["el_count"]; $a++)
						{
							if ($a > 0)
							{
								$cell["elements"][$a-1] = $cell["elements"][$a];
							}
						}
						$cell["el_count"] --;
					}
				}
			}
		}

		// nyyt elementide liigutamine 
		$this->save_output($id);
		return $this->mk_orb("admin_op", array("id" => $id));
	}

	/** saves cell and contained element's properties 
		
		@attrib name=submit_admin_cell params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_admin_cell($arr)
	{
		extract($arr);
		$this->load_output($id);

		$cell = &$this->output[$row][$col];

		for ($i=0; $i < $cell["el_count"]; $i++)
		{
			$el = get_instance("formgen/form_search_element");
			$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);

			if ($el->save(&$arr) == false)
			{
				// we must delete the element from this op.
				// we must also shift all other elements up one 
				for ($a=$i; $a < $cell["el_count"]-1; $a++)
				{
					$cell["elements"][$a] = $cell["elements"][$a+1];
				}
				$cell["el_count"] --;
			}
			else
			{
				$cell["elements"][$i] = $el->get_props();
			}
		}
		$cell["style"] = $cell_style;
		$this->save_output($id);
		return $this->mk_orb("ch_cell", array("id" => $id,"row" => $row, "col" => $col));
	}

	////
	// !saves the current state of the loaded form output
	function save_output($id)
	{
		$tp = aw_serialize($this->output,SERIALIZE_PHP);
		$this->quote(&$tp);
		$this->db_query("UPDATE form_output SET op = '$tp' WHERE id = $id");
		$o = obj($id);
		$o->save(); 
		$this->_log(ST_FORM_OP, SA_CHANGE,sprintf(LC_FORM_OUTPUT_CHANGED_STYLE,$name), $id);
	}

	/** adds a column to output $id after $after 
		
		@attrib name=add_col params=name default="0"
		
		@param id required acl="edit;view"
		@param after optional
		
		@returns
		
		
		@comment

	**/
	function add_col($arr)
	{
		extract($arr);
		$this->load_output($id);

		for ($row = 0; $row < $this->output["rows"]; $row++)
		{
			for ($i=$this->output["cols"]; $i > $after; $i--)
			{
				$this->output[$row][$i+1] = $this->output[$row][$i];
			}
		}

		for ($row = 0; $row < $this->output["rows"]; $row++)
		{
			$this->output[$row][$after+1] = "";
		}

		$this->output["cols"]++;
		$this->map_add_col($this->output["rows"], $this->output["cols"], &$this->output["map"],$after);

		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** deletes the column $col of output $id 
		
		@attrib name=del_col params=name default="0"
		
		@param id required acl="edit;view"
		@param col optional
		
		@returns
		
		
		@comment

	**/
	function del_col($arr)
	{
		extract($arr);
		$this->load_output($id);

		for ($row = 0; $row < $this->output["rows"]; $row++)
		{
			for ($i=$col; $i < ($this->output["cols"]-1); $i++)
			{
				$this->output[$row][$i] = $this->output[$row][$i+1];
			}
		}

		for ($row = 0; $row < $this->output["rows"]; $row++)
		{
			$this->output[$row][$this->output["cols"]-1] = "";
		}

		$this->map_del_col($this->output["rows"], $this->output["cols"], &$this->output["map"],$col);
		$this->output["cols"]--;

		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** adds a row to output $id after row $after 
		
		@attrib name=add_row params=name default="0"
		
		@param id required acl="edit;view"
		@param after optional
		
		@returns
		
		
		@comment

	**/
	function add_row($arr)
	{
		extract($arr);
		$this->load_output($id);

		for ($row=$this->output["rows"]; $row > $after; $row--)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				$this->output[$row+1][$col] = $this->output[$row][$col];
			}
		}

		for ($col = 0; $col < $this->output["cols"]; $col++)
		{
			$this->output[$after+1][$col] = "";
		}

		$this->output["rows"]++;
		$this->map_add_row($this->output["rows"], $this->output["cols"], &$this->output["map"], $after);

		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** deletes row $row of output $id 
		
		@attrib name=del_row params=name default="0"
		
		@param id required acl="edit;view"
		@param row optional
		
		@returns
		
		
		@comment

	**/
	function del_row($arr)
	{
		extract($arr);
		$this->load_output($id);

		$row_d = $row;
		for ($row=$row_d; $row < ($this->output["rows"]-1); $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				$this->output[$row][$col] = $this->output[$row+1][$col];
			}
		}

		for ($col = 0; $col < $this->output["cols"]; $col++)
		{
			$this->output[$this->output["rows"]-1][$col] = "";
		}

		$this->map_del_row($this->output["rows"], $this->output["cols"], &$this->output["map"], $row_d);
		$this->output["rows"]--;
		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** merges the cell ($row, $col) in output $id with the cell immediately above it 
		
		@attrib name=exp_up params=name default="0"
		
		@param id required acl="edit;view"
		@param row optional
		@param col optional
		
		@returns
		
		
		@comment

	**/
	function exp_up($arr)
	{
		extract($arr);
		$this->load_output($id);
		$this->map_exp_up($this->output["rows"], $this->output["cols"], &$this->output["map"],$row,$col);
		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** merges the cell ($row,$col) in output $id with the cell below it 
		
		@attrib name=exp_down params=name default="0"
		
		@param id required acl="edit;view"
		@param row optional
		@param col optional
		
		@returns
		
		
		@comment

	**/
	function exp_down($arr)
	{
		extract($arr);
		$this->load_output($id);
		$this->map_exp_down($this->output["rows"], $this->output["cols"], &$this->output["map"],$row,$col);
		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** merges the cell ($row,$col) in output $id with the cell to the left of it 
		
		@attrib name=exp_left params=name default="0"
		
		@param id required acl="edit;view"
		@param row optional
		@param col optional
		
		@returns
		
		
		@comment

	**/
	function exp_left($arr)
	{
		extract($arr);
		$this->load_output($id);
		$this->map_exp_left($this->output["rows"], $this->output["cols"], &$this->output["map"],$row,$col);
		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** merges the cell ($row,$col) in output $id with the cell to the right of it 
		
		@attrib name=exp_right params=name default="0"
		
		@param id required acl="edit;view"
		@param row optional
		@param col optional
		
		@returns
		
		
		@comment

	**/
	function exp_right($arr)
	{
		extract($arr);
		$this->load_output($id);
		$this->map_exp_right($this->output["rows"], $this->output["cols"], &$this->output["map"],$row,$col);
		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** splits the cell ($row,$col) in output $id vertically 
		
		@attrib name=split_cell_ver params=name default="0"
		
		@param id required acl="edit;view"
		@param row optional
		@param col optional
		
		@returns
		
		
		@comment

	**/
	function split_cell_ver($arr)
	{
		extract($arr);
		$this->load_output($id);
		$this->map_split_ver($this->output["rows"], $this->output["cols"], &$this->output["map"],$row,$col);
		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/** splits the cell ($row,$col) in output $id horizontally 
		
		@attrib name=split_cell_hor params=name default="0"
		
		@param id required acl="edit;view"
		@param row optional
		@param col optional
		
		@returns
		
		
		@comment

	**/
	function split_cell_hor($arr)
	{
		extract($arr);
		$this->load_output($id);
		$this->map_split_hor($this->output["rows"], $this->output["cols"], &$this->output["map"],$row,$col);
		$this->save_output($id);
		$orb = $this->mk_orb("admin_op", array("id" => $id));
		header("Location: $orb");
		return $orb;
	}

	/**  
		
		@attrib name=ch_cell params=name default="0"
		
		@param id required acl="edit;view"
		@param row required
		@param col required
		
		@returns
		
		
		@comment

	**/
	function ch_cell($arr)
	{
		extract($arr);
		$this->read_template("admin_cell.tpl");
		$u1 = $this->mk_my_orb("change", array("id" => $id));
		$this->mk_path($this->parent,sprintf(LC_FORM_OUTPUT_CHANGE_OUTPUT_ADMIN,$u1,$this->mk_my_orb("admin_op",array("id" => $id))));

		$this->load_output($id);

		$ell = "";
		$cell = &$this->output[$row][$col];
		for ($i=0; $i < $cell["el_count"]; $i++)
		{
			$el = get_instance("formgen/form_search_element");
			$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);
			$this->vars(array(
				"element" => $el->gen_admin_html(),
				"after" => $el->get_id(),
			));
			$ell.=$this->parse("ELEMENT_LINE");
		}
		$this->vars(array(
			"ELEMENT_LINE" => $ell, 
			"add_el" => $this->mk_my_orb("add_element", array("id" => $id, "row" => $row, "col" => $col))
		));

		$this->vars(array(
			"CAN_ADD" => $this->parse("CAN_ADD"),
			"cell_style" => $this->picker($cell["style"], $this->style_instance->get_select(0,ST_CELL, true)),
			"reforb"	=> $this->mk_reforb("submit_admin_cell", array("id" => $this->output_id, "row" => $row, "col" => $col),"form_output")
		));
		return $this->parse();
	}

	////
	// !fake function to support form_element embedding
	function get_search_targets()
	{
		return $this->get_op_forms($this->output_id);
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

	function mk_elarr($id)
	{
		// vaja on arrayd el_id => el_name k6ikide elementide kohta, mis on selle v2ljundi sees
		$elarr = array("0" => "");
		$op_forms = $this->get_op_forms($id);
		$fidstring = join(",",map2("%s",$op_forms));
		if ($fidstring != "")
		{
			// make preview
			$this->db_query("SELECT oid,form_id FROM objects LEFT JOIN form_entries ON form_entries.id = objects.oid WHERE class_id = ".CL_FORM_ENTRY." AND status != 0 AND form_entries.form_id IN ($fidstring)");
			$row = $this->db_next();
			if ($row)
			{
				$this->vars(array(
					"preview" => $this->mk_orb("show_entry", array("id" => $row["form_id"], "op_id" => $id, "entry_id" => $row["oid"]),"form")
				));
				$this->vars(array("PREVIEW" => $this->parse("PREVIEW")));
			}

			$this->db_query(
				"SELECT el_id, objects.name as name
				 FROM element2form 
					 LEFT JOIN objects ON objects.oid = element2form.el_id
					WHERE form_id IN ($fidstring)"
			);
			while ($row = $this->db_next())
			{
				$elarr[$row["el_id"]] = $row["name"];
			}
		}
		return $elarr;
	}

	/** Adds an element to the end of celll $row $col 
		
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
		$this->load_output($id);
		$this->mk_path($this->parent, "<a href='".$this->mk_orb("change", array("id" => $this->id),"form").LC_FORM_CELL_CHANGE_FROM_ADD_ELEMENT);

		if (!$wizard_step)
		{
			$this->read_template("add_el_wiz1.tpl");
			$mlist = $this->get_menu_list();
			
			$this->vars(array(
				"reforb" => $this->mk_reforb("add_element", array("id" => $id, "row" => $row, "col" => $col,"wizard_step" => 1),"form_output"),
				"folders"		=> $this->picker($this->parent, $mlist),
				"elements"	=> $this->picker(0,$this->listall_elements(true))
			));
			return $this->parse();
		}
		else
		{
			global $HTTP_POST_VARS;
			extract($_POST);

			if ($type == "add")
			{
				$o = obj();
				$o->set_name($name);
				$o->set_parent($parent);
				$o->set_class_id(CL_FORM_ELEMENT);
				$el = $o->save();

				$this->db_query("INSERT INTO form_elements (id) values($el)");
			}
			else
			{
				if ($el)
				{
					$oo = obj($el);
					$name = $oo->name();
					$ord = $oo->jrk();
				}
			}
		
			if ($el)
			{
				// add the element into the form.
				// but! use the props saved in the form_elements table to create them with the right config right away!
				$props = $this->db_fetch_field("SELECT props FROM form_elements WHERE id = ".$el,"props");
				$arr = aw_unserialize($props);
				$arr["id"] = $el;
				$arr["name"] = $name;
				$arr["ord"] = $ord;
				$arr["linked_element"] = 0;
				$arr["linked_form"] = 0;
				$this->output[$row][$col]["el_count"]++;	// don't change the order of this and the next one :)
				$this->output[$row][$col]["elements"][$this->output[$row][$col]["el_count"]-1] = $arr;
				$this->save_output($id);
			}
			return $this->mk_orb("ch_cell", array("id" => $id, "row" => $row, "col" => $col));
		}
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
		$this->load_output($id);
		$this->read_template("op_translate.tpl");
		$this->mk_path($this->parent,sprintf(LC_FORM_OUTPUT_OUTPUT_ADMIN,$this->mk_orb("change", array("id" => $id))));

		$la = get_instance("languages");
		$langs = $la->listall();

		foreach($langs as $lar)
		{
			$this->vars(array(
				"lang_name" => $lar["name"]
			));
			$lah.=$this->parse("LANGH");
		}
		$this->vars(array("LANGH" => $lah));

		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				for($i=0; $i < $this->output[$row][$col]["el_count"]; $i++)
				{
					$el=get_instance("formgen/form_entry_element");
					$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);

					$lcol = "";
					foreach($langs as $lar)
					{
						$this->vars(array(
							"text" => $el->get_lang_text($lar["id"]),
							"col" => $col,
							"row" => $row,
							"elid" => $i,
							"lang_id" => $lar["id"]
						));
						$lcol.=$this->parse("LCOL");
					}
					$this->vars(array("LCOL" => $lcol));
					$lrow.=$this->parse("LROW");
				}
			}
		}
		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				for($i=0; $i < $this->output[$row][$col]["el_count"]; $i++)
				{
					$el=get_instance("formgen/form_entry_element");
					$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);
					if ($el->get_type() == "listbox")
					{
						for ($a=0; $a < $el->arr["listbox_count"]; $a++)
						{
							$lcol1 = "";
							foreach($langs as $lar)
							{
								if ($lar["id"] != $this->lang_id)
								{
									$txt = $el->arr["listbox_lang_items"][$lar["id"]][$a];
								}
								else
								{
									$txt = $el->arr["listbox_items"][$a];
								}
								$this->vars(array(
									"text" => $txt,
									"col" => $col,
									"row" => $row,
									"elid" => $i,
									"lang_id" => $lar["id"],
									"item" => $a
								));
								$lcol1.=$this->parse("LCOL1");
							}
							$this->vars(array("LCOL1" => $lcol1));
							$lrow1.=$this->parse("LROW1");
						}
					}
				}
			}
		}
		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				for($i=0; $i < $this->output[$row][$col]["el_count"]; $i++)
				{
					$el=get_instance("formgen/form_entry_element");
					$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);
					if ($el->get_type() == "multiple")
					{
						for ($a=0; $a < $el->arr["multiple_count"]; $a++)
						{
							$lcol2 = "";
							foreach($langs as $lar)
							{
								if ($lar["id"] != $this->lang_id)
								{
									$txt = $el->arr["multiple_lang_items"][$lar["id"]][$a];
								}
								else
								{
									$txt = $el->arr["multiple_items"][$a];
								}
								$this->vars(array(
									"text" => $txt,
									"col" => $col,
									"row" => $row,
									"elid" => $i,
									"lang_id" => $lar["id"],
									"item" => $a
								));
								$lcol2.=$this->parse("LCOL2");
							}
							$this->vars(array("LCOL2" => $lcol2));
							$lrow2.=$this->parse("LROW2");
						}
					}
				}
			}
		}
		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				for($i=0; $i < $this->output[$row][$col]["el_count"]; $i++)
				{
					$el=get_instance("formgen/form_entry_element");
					$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);
					$lcol3 = "";
					foreach($langs as $lar)
					{
						if ($lar["id"] != $this->lang_id)
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
							"elid" => $i,
							"lang_id" => $lar["id"],
						));
						$lcol3.=$this->parse("LCOL3");
					}
					$this->vars(array("LCOL3" => $lcol3));
					$lrow3.=$this->parse("LROW3");
				}
			}
		}
		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				for($i=0; $i < $this->output[$row][$col]["el_count"]; $i++)
				{
					$el=get_instance("formgen/form_entry_element");
					$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);
					if ($el->get_type() == "textbox" || $el->get_type() == "textarea")
					{
						$lcol4 = "";
						foreach($langs as $lar)
						{
							if ($lar["id"] != $this->lang_id)
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
								"elid" => $i,
								"lang_id" => $lar["id"],
							));
							$lcol4.=$this->parse("LCOL4");
						}
						$this->vars(array("LCOL4" => $lcol4));
						$lrow4.=$this->parse("LROW4");
					}
				}
			}
		}
		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				for($i=0; $i < $this->output[$row][$col]["el_count"]; $i++)
				{
					$el=get_instance("formgen/form_entry_element");
					$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);
					$lcol5 = "";
					foreach($langs as $lar)
					{
						if ($lar["id"] != $this->lang_id)
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
							"elid" => $i,
							"lang_id" => $lar["id"],
						));
						$lcol5.=$this->parse("LCOL5");
					}
					$this->vars(array("LCOL5" => $lcol5));
					$lrow5.=$this->parse("LROW5");
				}
			}
		}
		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				for($i=0; $i < $this->output[$row][$col]["el_count"]; $i++)
				{
					$el=get_instance("formgen/form_entry_element");
					$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);
					if ($el->get_type() == "button")
					{
						$lcol6 = "";
						foreach($langs as $lar)
						{
							if ($lar["id"] != $this->lang_id)
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
								"elid" => $i,
								"lang_id" => $lar["id"],
							));
							$lcol6.=$this->parse("LCOL6");
						}
						$this->vars(array("LCOL6" => $lcol6));
						$lrow6.=$this->parse("LROW6");
					}
				}
			}
		}


		$lcol7 = "";
		foreach($langs as $lar)
		{
			$txt = $this->output["lang_close_button_text"][$lar["id"]];
			$this->vars(array(
				"text" => $txt,
				"lang_id" => $lar["id"],
			));
			$lcol7.=$this->parse("LCOL7");
		}
		$this->vars(array("LCOL7" => $lcol7));


		$this->vars(array(
			"LROW" => $lrow,
			"LROW1" => $lrow1,
			"LROW2" => $lrow2,
			"LROW3" => $lrow3,
			"LROW4" => $lrow4,
			"LROW5" => $lrow5,
			"LROW6" => $lrow6,
			"reforb" => $this->mk_reforb("submit_translate", array("id" => $id))
		));

		return $this->parse();
	}

	/**  
		
		@attrib name=submit_translate params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_translate($arr)
	{
		extract($arr);
		$this->load_output($id);

		$la = get_instance("languages");
		$langs = $la->listall();

		for ($row=0; $row < $this->output["rows"]; $row++)
		{
			for ($col=0; $col < $this->output["cols"]; $col++)
			{
				for($i=0; $i < $this->output[$row][$col]["el_count"]; $i++)
				{

					$el=get_instance("formgen/form_entry_element");
					$el->load($this->output[$row][$col]["elements"][$i],&$this,$col,$row);
					foreach($langs as $lar)
					{
						$this->output["lang_close_button_text"][$lar["id"]] = $close_button[$lar["id"]];
						$this->output[$row][$col]["elements"][$i]["lang_text"][$lar["id"]] = $r[$row][$col][$lar["id"]][$i];
						$this->output[$row][$col]["elements"][$i]["lang_info"][$lar["id"]] = $s[$row][$col][$lar["id"]][$i];
						$this->output[$row][$col]["elements"][$i]["lang_default"][$lar["id"]] = $d[$row][$col][$lar["id"]][$i];
						$this->output[$row][$col]["elements"][$i]["lang_must_error"][$lar["id"]] = $e[$row][$col][$lar["id"]][$i];
						if ($el->get_type() == "button")
						{
							$this->output[$row][$col]["elements"][$i]["lang_button_text"][$lar["id"]] = $b[$row][$col][$lar["id"]][$i];
						}
					}
					if ($el->get_type() == "listbox")
					{
						foreach($langs as $lar)
						{
							for ($a=0; $a < $el->arr["listbox_count"]; $a++)
							{
								$this->output[$row][$col]["elements"][$i]["listbox_lang_items"][$lar["id"]][$a] = $l[$row][$col][$lar["id"]][$i][$a];
							}
						}
					}
					else
					if ($el->get_type() == "multiple")
					{
						foreach($langs as $lar)
						{
							for ($a=0; $a < $el->arr["multiple_count"]; $a++)
							{
								$this->output[$row][$col]["elements"][$i]["multiple_lang_items"][$lar["id"]][$a] = $m[$row][$col][$lar["id"]][$i][$a];
							}
						}
					}
				}
			}
		}
		$this->save_output($id);

		return $this->mk_my_orb("translate", array("id" => $id));
	}

	function get_element_by_type($type,$subtype = "",$all_els = false)
	{
		return array();
	}

	function parse_alias($args = array())
	{
		extract($args);

		$fo = get_instance(CL_FORM);
		$replacement = $fo->show(array(
			"op_id" => $alias["target"],
		));

		return $replacement;
	}
}
?>
