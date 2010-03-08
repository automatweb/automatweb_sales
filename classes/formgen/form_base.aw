<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_base.aw,v 1.30 2008/01/31 13:54:33 kristo Exp $
// form_base.aw - this class loads and saves forms, all form classes should derive from this.
lc_load("automatweb");
/*
@classinfo  maintainer=kristo
*/

classload("formgen/form_db_base");
class form_base extends form_db_base
{
	function form_base()
	{
		$this->init("forms");
		lc_load("definition");
		$this->lc_load("form","lc_form");
	}

	////
	// !Loads the specified form
	// Forms are saved as serialized arrays in forms.content
	// the array is structured like this:
	// $arr[rows] - number of rows in form
	// $arr[cols] - number of columns in form
	// $arr[map]	- array that contains the map used in merging and splitting form cells
	//							the array is 2 dimensional, $arr[rows] wide and $arr[cols] deep
	//							each element represents the corresponding cell and shows which cell
	//							should really be displayed instead of the cell in taht position
	//							example:
	//							if a form has 2 rows and 3 columns and the rightmost 4 cells are merged into one, then the map looks like this:
	//							$arr[map][0][0] = array("row" => 0, "col" => 0);
	//							$arr[map][0][1] = array("row" => 0, "col" => 1);
	//							$arr[map][0][2] = array("row" => 0, "col" => 1);
	//							$arr[map][1][0] = array("row" => 1, "col" => 0);
	//							$arr[map][1][1] = array("row" => 0, "col" => 1);
	//							$arr[map][1][2] = array("row" => 0, "col" => 1);
	//							so the form looks like this:
	//							---------------
	//							| 0,0 |				|
	//							-------  0,1  -
	//							| 1,0 |       |
	//							---------------
	//
	// $arr[contents]	- array of form_cell's, one for each cell, this is not saved to the database,
	//									instead it contains the actual objects that are created from $arr[elements] upon loading
	// $arr[style]		- form's table style id
	// $arr[elements] - array of elements in the form, indexed by row and column
	function load($id = 0)
	{
		if ($id == 0)
		{
			// see tuleb form klassi konstruktorist
			$id = $this->fid;
		};

		$q = "SELECT forms.*,objects.* FROM forms LEFT JOIN objects ON objects.oid = forms.id WHERE forms.id = '$id'";
		$this->db_query($q);
		if (!($row = $this->db_next()))
		{
			$this->raise_error(ERR_FG_NOFORM,sprintf(t("form->load(%s): no such form!"), $id),true);
		}

		$this->name = $row["name"];
		$this->id = $row["oid"];
		$this->parent = $row["parent"];
		$this->type = $row["type"];
		$this->subtype = $row["subtype"];
		$this->comment = $row["comment"];
		$this->lang_id = $row["lang_id"];
		$this->meta = aw_unserialize($row["metadata"]);
		$this->entry_id = 0;
		$this->flags = $row["flags"];

		$this->arr = aw_unserialize($row["content"]);

		$this->normalize();
		$this->load_elements();
	}

	////
	// !Loads form elements from database
	// loads elements as specified in $this->arr[elements]
	// form must be loaded previously
	// puts elements in $this->arr[contents]
	function load_elements()
	{
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$this->arr["contents"][$row][$col] = get_instance("formgen/form_cell");		
				$this->arr["contents"][$row][$col] -> load(&$this,$row,$col);
			}
		}
	}

	////
	// !Makes sure the form conforms to a specified standard
	// the form mus at least have one element and row count and column count must be at least 1
	// if things are not ok, the form is recreated and saved
	function normalize()
	{
		// this makes sure that the map gets initialized properly
		if (!$this->arr["map"][0][0]["row"])
		{
			$this->arr["map"][0][0]["row"] = 0;
		}
		if (!$this->arr["map"][0][0]["col"])
		{
			$this->arr["map"][0][0]["col"] = 0;
		}

		if ($this->arr["cols"] < 1)
		{
			$this->arr["cols"] = 1;
		}
		if ($this->arr["rows"] < 1)
		{
			$this->arr["rows"] = 1;
		}

		if (!$this->arr["ff_folder"])
		{
			$this->arr["ff_folder"] = $this->parent;
		}
	}

	////
	// !writes the current form's settings from memory to the database
	function save()
	{
		// here we also update the controller usage table
		$this->db_query("DELETE FROM form_controller2element WHERE form_id = '".$this->id."'");
		
		// go over all the elements in the form and create an array that contains the table/col that 
		// eash element writes to
		// this will be used to be able to create search queries without having to load all the forms
		$el_tbls = array();

		// we must do this, otherwise we also serialize all the cells and stuff, which isn't necessary
		for ($col = 0; $col < $this->arr["cols"]; $col++)		
		{
			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				$this->arr["map"][$row][$col]["row"] = (int)$this->arr["map"][$row][$col]["row"];
				$this->arr["map"][$row][$col]["col"] = (int)$this->arr["map"][$row][$col]["col"];
				// if we are adding rows/columns, then those objects might not be initialized yet
				if (is_object($this) && is_object($this->arr["contents"][$row][$col]))
				{
					$ret = array();
					$this->arr["contents"][$row][$col]->get_els(&$ret);
					foreach($ret as $el)
					{
						if ($this->arr["has_controllers"])
						{
							// save all the controllers used in this form to the database so we can easily look them up later
							$entry_c = $el->get_entry_controllers();
							foreach($entry_c as $ctrlid)
							{
								$this->db_query("INSERT INTO form_controller2element(ctrl_id, form_id ,el_id, type)
									VALUES('$ctrlid','".$this->id."','".$el->get_id()."','".CTRL_USE_TYPE_ENTRY."')");
							}

							$show_c = $el->get_show_controllers();
							foreach($show_c as $ctrlid)
							{
								$this->db_query("INSERT INTO form_controller2element(ctrl_id, form_id ,el_id, type)
									VALUES('$ctrlid','".$this->id."','".$el->get_id()."','".CTRL_USE_TYPE_SHOW."')");
							}

							$lb_c = $el->get_lb_controllers();
							foreach($lb_c as $ctrlid)
							{
								$this->db_query("INSERT INTO form_controller2element(ctrl_id, form_id ,el_id, type)
									VALUES('$ctrlid','".$this->id."','".$el->get_id()."','".CTRL_USE_TYPE_LB."')");
							}

							$defvl = $el->get_default_value_controller();
							if ($defvl)
							{
								$this->db_query("INSERT INTO form_controller2element(ctrl_id, form_id ,el_id, type)
									VALUES('$defvl','".$this->id."','".$el->get_id()."','".CTRL_USE_TYPE_DEFVALUE."')");
							}

							$vlc = $el->get_value_controller();
							if ($vlc)
							{
								$this->db_query("INSERT INTO form_controller2element(ctrl_id, form_id ,el_id, type)
									VALUES('$vlc','".$this->id."','".$el->get_id()."','".CTRL_USE_TYPE_VALUE."')");
							}
						}
					}
				}
			}
		}

		for ($col = 0; $col < $this->arr["cols"]; $col++)		
		{
			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				// if we are adding rows/columns, then those objects might not be initialized yet
				if (is_object($this) && is_object($this->arr["contents"][$row][$col]))
				{
					// check if the cell can be seen..
					if (($arr = $this->get_spans($row, $col)))
					{
						if (is_object($this) && is_object($this->arr["contents"][$arr["r_row"]][$arr["r_col"]]))
						{
							$ret = array();
							$this->arr["contents"][$arr["r_row"]][$arr["r_col"]]->get_els(&$ret);
							foreach($ret as $el)
							{
								$el_tbls["els"][$el->get_id()]["table"] = $el->get_save_table();
								$el_tbls["els"][$el->get_id()]["col"] = $el->get_save_col();
								$el_tbls["els"][$el->get_id()]["col2"] = $el->get_save_col2();
							}
						}
					}
				}
			}
		}

		for ($col = 0; $col < $this->arr["cols"]; $col++)		
		{
			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				$this->arr["contents"][$row][$col] = "";
			}
		}

		// some sanity checks
		if (!$this->arr["cols"])
		{
			$this->arr["cols"] = 0;
		}
		if (!$this->arr["rows"])
		{
			$this->arr["rows"] = 0;
		}
		// set to 0 if not set already. 
		$this->subtype = (int)$this->subtype;

		$el_tbls["save_tables"] = $this->get_tables_for_form();
		$el_tbls["save_table"] = $this->arr["save_table"];
		$el_tbls["save_table_data"] = $this->arr["save_tables"];
		$el_tbls["save_table_start_from"] = $this->arr["save_table_start_from"];
		$el_tbls["save_tables_obj_tbl"] = $this->arr["save_tables_obj_tbl"];

		$el_tbls["is_translatable"] = $this->arr["is_translatable"];

		$tmp = obj($this->id);
		$tmp->set_name($this->name);
		$tmp->set_comment($this->comment);
		$tmp->set_flags((int)$this->flags);
		$awa = new aw_array($this->meta);
		foreach($awa->get() as $k => $v)
		{
			$tmp->set_meta($k, $v);
		}
		$tmp->save();

		$contents = aw_serialize($this->arr,SERIALIZE_PHP);
		$this->quote(&$contents);

		$el_tblstr = aw_serialize($el_tbls,SERIALIZE_PHP);
		$this->quote(&$el_tblstr);

		aw_session_del_patt("/_fr_forms_used.*/");
		aw_session_del_patt("/form_rel_tree.*/");

		$this->db_query("UPDATE forms SET content = '$contents', subtype = " . $this->subtype . ", rows = ".$this->arr["rows"]." , cols = ".$this->arr["cols"].",el_tables = '$el_tblstr' WHERE id = ".$this->id);
		$this->_log(ST_FORM, SA_CHANGE, $this->name, $this->id);
	}

	////
	// !Loads form, template and generates description header
	// usually called in the beginning of a UI generating function
	// initialize interface
	function if_init($id, $tpl = "", $desc = "")
	{
		$this->load($id);
		if ($tpl != "")
		{
			$this->read_template($tpl);
		}
		$chlink = sprintf("<a href='%s'>%s</a> / ",$this->mk_my_orb("change",array("id" => $id),"form"),$this->name);
		if ($desc != "")
		{
			$this->mk_path($this->parent,$chlink . $desc);
		}
	}

	function finit($id, $tpl = "", $desc = "")
	{
		return $this->init($id,$tpl,$desc);
	}

	////
	// !helper function. generates the formgen menu and returns the string. 
	// use instead of return $this->parse() in the end of UI generating functions
	function do_menu_return($st = "")
	{
		if ($st == "")
		{
			$st = $this->parse();
		}
		$this->reset();
		global $lc_form;
		if (is_array($lc_form))
		{
			$this->vars($lc_form);
		}
		$this->read_template("menu.tpl");
		$this->do_menu();
		return $this->parse().$st;
	}

	////
	// !draws the formgen menu and makes the correct tab active. 
	function do_menu()
	{
		$action = aw_global_get("action");

		$this->vars(array(
			"form_id"					=> $this->id, 
			"change"					=> $this->mk_my_orb("change", array("id" => $this->id),"form"),
			"show"						=> $this->mk_my_orb("preview_form", array("id" => $this->id),"form"),
			"table_settings"	=> $this->mk_my_orb("table_settings", array("id" => $this->id),"form"),
			"all_elements"		=> $this->mk_my_orb("all_elements", array("id" => $this->id),"form"),
			"all_elements2"		=> $this->mk_my_orb("all_elements2", array("id" => $this->id),"form"),
			"actions"					=> $this->mk_my_orb("list_actions", array("id" => $this->id),"form_actions"),
			"sel_search"			=> $this->mk_my_orb("sel_search", array("id" => $this->id), "form"),
			"metainfo"				=> $this->mk_my_orb("metainfo", array("id" => $this->id), "form"),
			"sel_filter_search" => $this->mk_my_orb("sel_filter_search", array("id" => $this->id), "form"),
			"import_entries" => $this->mk_my_orb("import_form_entries", array("id" => $this->id),"form_import"),
			"set_folders" => $this->mk_my_orb("set_folders", array("id" => $this->id),"form"),
			"translate" => $this->mk_my_orb("translate", array("id" => $this->id),"form"),
			"tables" => $this->mk_my_orb("sel_tables", array("id" => $this->id),"form"),
			"aliasmgr" => $this->mk_my_orb("form_aliasmgr", array("id" => $this->id),"form"),
			"calendar" => $this->mk_my_orb("calendar",array("id" => $this->id),"form"),
			"joins" => $this->mk_my_orb("joins",array("id" => $this->id),"form"),
			"export" => $this->mk_my_orb("export", array("id" => $this->id),"form")
		));

		if (in_array($action, array("change","preview_form","all_elements","sel_search","sel_filter_search","form_aliasmgr")))
		{
			$this->parse("GRID_SEL");
		}

		if (in_array($action, array("table_settings","list_actions","metainfo","set_folders","translate","sel_tables","calendar","new_cal_rel","edit_cal_rel","joins","export")))
		{
			$this->parse("SETTINGS_SEL");
		}

		if ($this->type == FTYPE_SEARCH)
		{
			$this->parse("SEARCH_SEL");
		} 
		else
		if ($this->type == FTYPE_FILTER_SEARCH)
		{
			$this->parse("FILTER_SEARCH_SEL");
		}

		$this->parse("RELS");
		$this->parse("CAN_GRID");
		$this->parse("CAN_ALL");
		$this->parse("CAN_TABLE");
		$this->parse("CAN_META");
		$this->parse("CAN_PREVIEW");
		$this->parse("CAN_ACTION");
		
		if ($this->arr["has_aliasmgr"])
		{
			$this->parse("HAS_ALIASMGR");
		};
	
		if ((int)$this->subtype & FORM_USES_CALENDAR)	
		{
			$this->parse("USES_CALENDAR");
		};

		$this->vars(array("FG_MENU" => $this->parse("FG_MENU")));
	}

	////
	// !generates a plain-text representation of the loaded entry for the loaded form, suitable for e-mailing
	function show_text()
	{
		$msg = "";
		for ($r = 0; $r < $this->arr["rows"]; $r++)
		{
			$msg.=$this->mk_show_text_row($r)."\n";
		}
		return $msg;
	}

	////
	// !generates row $r of the plain-text representation of the loaded entry for the loaded form 
	function mk_show_text_row($r)
	{
		$msg = "";
		for ($c = 0; $c < $this->arr["cols"]; $c++)
		{
			$elr = array();
			$this->arr["contents"][$r][$c]->get_els(&$elr);
			foreach($elr as $v)
			{
				$msg.=$v->gen_show_text();
			}
		}
		return $msg;
	}

	////
	// !loads the specified output for the currently loaded form
	function load_output($id)
	{
		$q = "SELECT form_output.*,objects.* FROM objects
			LEFT JOIN form_output ON form_output.id = objects.oid
			WHERE objects.oid = '$id'";
		$this->db_query($q);
		if (!($row = $this->db_next()))
		{
			$this->raise_error(ERR_FG_NOOP,sprintf("No such output %s",$id),true);
		}

		$this->output = aw_unserialize($row["op"]);
		$this->output_id = $id;

		// hey! these will conflict with the ones set by loading the form!
		$this->name = $row["name"];
		$this->comment = $row["comment"];
		$this->parent = $row["parent"];
		$this->lang_id = $row["lang_id"];

		// fake some stuff for form elements to work:
		$this->arr["has_controllers"] = $this->output["has_controllers"];

		// make sure things are in an at least relatively coherent state
		if ($this->output["cols"] < 1 || $this->output["rows"] < 1)
		{
			$this->output["cols"] = 1;
			$this->output["rows"] = 1;
			$this->output["map"][0][0] = array("row" => 0, "col" => 0);
		}
	}

	////
	// !returns a list of forms, filtered by type
	// arguments:
	// type(int) - listitavate vormide tüüp
	// addempty(bool) - kas lisada tagastatava array algusse tühi element?
	// onlyactive(bool) - whether to list only active forms?
	// addfolders(bool) - if true, folders are added to list of forms
	// lang_id - if set, filters by lang id
	// all_data - if set, all dafa of form is included
	// sort - if set, the list will be sorted
	function get_flist($args = array())
	{
		extract($args);

		$ret = ($addempty) ? array("0" => "") : array();
		$st = ($onlyactive) ? " = 2" : "!= 0";
		
		if ($lang_id)
		{
			$wh = " AND objects.lang_id = ".$lang_id;
		}

		if ($addfolders)
		{
			$ol = $this->get_menu_list();
		}

		if ($type)
		{
			$typ = " AND forms.type = $type ";
		}

		if ($subtype)
		{
			$typ .= " AND (forms.subtype && $subtype) > 0 ";
		}

		$q = sprintf("	SELECT
					objects.name AS name,
					objects.oid AS oid,
					objects.parent AS parent
				FROM forms
				LEFT JOIN objects ON objects.oid = forms.id
				WHERE objects.status %s $typ $wh",
				$st);
		$this->db_query($q);
		while ($row = $this->db_next())
		{
			if ($addfolders)
			{
				$row["name"] = $ol[$row["parent"]]."/".$row["name"]." (".$row["oid"].")";
			}
			if ($all_data)
			{
				$ret[$row["oid"]] = $row;
			}
			else
			{
				$ret[$row["oid"]] = $row["name"];
			}
		}
		if ($sort)
		{
			asort($ret);
		}
		return $ret;
	}

	////
	// !returns a list of forms, filtered by type, wrapper for get_flist
	function get_list($type,$addempty = false,$onlyactive = false)
	{
		return $this->get_flist(array(
			"type" => $type,
			"addempty" => $addempty,
			"onlyactive" => $onlyactive,
		));
	}

	////
	// !returns a list of all form_outputs
	// if $fid is specified, only outputs for form $fid are returned
	function get_op_list($fid = 0)
	{
		$ret = array();
		if ($fid)
		{
			$ss = " AND form_id = $fid ";
		}
		$this->db_query("SELECT op_id,objects.name as name,form_id FROM output2form LEFT JOIN objects ON objects.oid = output2form.op_id WHERE class_id = ".CL_FORM_OUTPUT." AND status !=0  $ss");
		while ($row = $this->db_next())
		{
			$ret[$row["form_id"]][$row["op_id"]] = $row["name"];
		}
		return $ret;
	}

	////
	// !returns an array of all form id's for output $op_id
	function get_op_forms($op_id)
	{
		$ret = array();
		$this->db_query("SELECT form_id FROM output2form WHERE op_id = $op_id");
		while ($row = $this->db_next())
		{
			$ret[$row["form_id"]] = $row["form_id"];
		}
		return $ret;
	}

	////
	// !loads form table $id
	// form table data is loaded into $this->table array
	function load_table($id)
	{
		$this->db_query("SELECT objects.*,form_tables.* FROM objects LEFT JOIN form_tables ON form_tables.id = objects.oid WHERE oid = '$id'");
		$row = $this->db_next();
		$this->table_name = $row["name"];
		$this->table_comment = $row["comment"];
		$this->table_id = $id;
		$this->table_parent = $row["parent"];

		$this->table = aw_unserialize($row["content"]);
		$this->table["cols"] = $row["num_cols"];

		if ($this->table["cols"] < 1)
		{
			$this->table["cols"] = 1;
		}
	}

	////
	// !returns an array of id => name of all elements in the forms whose id's are in $arr
	// params:
	// $arr - array of form id's to include
	// $ret_forms - return value is el_id => form_id instead of el_id => el_name
	// addempty - if true, an empty element is added first
	function get_elements_for_forms($arr,$ret_forms = false,$addempty = false)
	{
		$ret = $addempty ? array(0 => "") : array();
		if (!is_array($arr))
		{
			return $ret;
		}

		$sss = join(",",$arr);
		if ($sss != "")
		{
			$this->db_query("SELECT form_id,el_id,objects.name as name 
											FROM element2form 
												LEFT JOIN objects ON objects.oid = element2form.el_id 
												LEFT JOIN objects AS f_obj ON f_obj.oid = element2form.form_id 
											WHERE element2form.form_id IN (".$sss.") AND f_obj.status != 0");
			while ($row = $this->db_next())
			{
				if ($ret_forms)
				{
					$ret[$row["el_id"]] = $row["form_id"];
				}
				else
				{
					$ret[$row["el_id"]] = $row["name"]." (".$row["el_id"].")";
				}
			}
		}
		return $ret;
	}

	////
	// !returns an array of elements for a form, (including id-s, types, 'n stuff)
	// arguments:
	// id(int) - id of the form, which we are to load
	// key(int) - what value to use as the key of the resulting array. default is the name
	// use_loaded (bool) - if set, use the already loaded form
	// all_data - if true, returns all data, else just the name, default is true
	function get_form_elements($args = array())
	{
		extract($args);
		$arrkey = ($args["key"]) ? $args["key"] : "name";
		$all_data = (isset($args["all_data"])) ? $args["all_data"] : true;

		if (not($use_loaded))
		{
			$this->load($id);
		};

		$retval = array();
		for ($i = 0; $i < $this->arr["rows"]; $i++)
		{
			$cols = "";
			for ($j = 0; $j < $this->arr["cols"]; $j++) 
			{
				// kui see cell on mone teise "all", siis jätame
				// ta lihtsalt vahele
				if (!($arr = $this->get_spans($i, $j)))
				{
					continue;
				}

				$cell = &$this->arr["contents"][$arr["r_row"]][$arr["r_col"]];
				if (is_object($cell))
				{
					$els = $cell->get_elements();
					foreach($els as $key => $val)
					{
						// we only want elements with type set, the rest
						// is probably just captions 'n stuff
						if ($val["type"])
						{
							if ($all_data)
							{
								$retval[$val[$arrkey]] = $val;
							}
							else
							{
								$retval[$val[$arrkey]] = $val["name"];
							}
						};
					};
				}
			}
		}
		return $retval;
	}

	////
	// !returns an array of references to the instances of all elements in this form
	// well, theoretically references anyway, but php craps out here and actually, if you modify them, they get cloned 
	// and changes end up in the cloned versions, so no changing stuff through these pointers
	function get_all_els()
	{	
		// damn, this needs to be different for outputs
		$ret = array();
		if ($this->output_id)
		{
			$op_far = $this->get_op_forms($this->output_id);
			for ($row = 0; $row < $this->output["rows"]; $row++)
			{
				for ($col = 0; $col < $this->output["cols"]; $col++)
				{
					if (!($arr = $this->get_spans($row, $col, $this->output["map"], $this->output["rows"], $this->output["cols"])))
					{
						continue;
					}
					$rrow = (int)$arr["r_row"];
					$rcol = (int)$arr["r_col"];
					$op_cell = $this->output[$rrow][$rcol];
					for ($i=0; $i < $op_cell["el_count"]; $i++)
					{
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
						$ret[] = $el;
					}
				}
			}
		}

		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$this->arr["contents"][$row][$col]->get_els(&$ret);
			}
		}
		return $ret;
	}

	////
	// !returns array id => name of all elements in the loaded form
	// what if I want to know the types of the elements as well?
	// if type argument is set, then the values of the returned array are 
	// also arrays, consiting of two elements,
	// 1) type of the element
	// 2) the actual name
	function get_all_elements($args = array())
	{
		$ret = array();
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$elar = $this->arr["contents"][$row][$col]->get_elements();
				foreach($elar as $el)
				{
					if ($args["type"])
					{
						$block = array(
							"name" => $el["name"],
							"type" => $el["type"],
							"subtype" => $el["subtype"],
						);
						$ret[$el["id"]] = $block;
					}
					elseif ($args["typematch"])
					{
						if ($el["type"] == $args["typematch"])
						{
							$ret[$el["id"]] = $el["name"];
						};
					}
					else
					{
						$ret[$el["id"]] = $el["name"];
					};
				}
			}
		}
		return $ret;
	}

	////
	// !returns array id => name of all elements in the loaded output
	// if type argument is set, then the values of the returned array are 
	// also arrays, consiting of two elements,
	// 1) type of the element
	// 2) the actual name
	function get_all_elements_in_op($args = array())
	{
		$ret = array();
		for ($row = 0; $row < $this->output["rows"]; $row++)
		{
			for ($col = 0; $col < $this->output["cols"]; $col++)
			{
				$cell = $this->output[$row][$col];
				for ($i=0; $i < $cell["el_count"]; $i++)
				{
					if ($args["type"])
					{
						$block = array(
							"name" => $cell["elements"][$i]["name"],
							"type" => $cell["elements"][$i]["type"],
						);
						$ret[$cell["elements"][$i]["id"]] = $block;
					}
					else
					{
						$ret[$cell["elements"][$i]["id"]] = $cell["elements"][$i]["name"];
					}
				}
			}
		}
		return $ret;
	}

	////
	// !returns an array of form_id => entry_id for the given chain entry id
	function get_chain_entry($entry_id, $no_show = false)
	{
		$row = $this->get_record("form_chain_entries","id",$entry_id);
		$ids = aw_unserialize($row["ids"]);

		if ($no_show)
		{
			// get the show only forms from the chain and kick those entries out
			$ct = $this->db_fetch_field("SELECT content FROM form_chains WHERE id = '".$this->get_chain_for_chain_entry($entry_id)."'","content");
			$c = aw_unserialize($ct);
			$_ar = new aw_array($c["no_load"]);
			foreach($_ar->get() as $n_fid => $one)
			{
				if ($one == 1)
				{
					unset($ids[$n_fid]);
				}
			}
		}

		return $ids;
	}

	////
	// !Retrieves all form entry id's for a single form in the chain entry
	// used for the forms which "repeat" inside the chain
	function get_form_entries_for_chain_entry($chain_entry_id,$form_id)
	{
		// protect the query from string arguments
		$q = sprintf("SELECT id FROM form_%d_entries LEFT JOIN objects ON (form_%d_entries.id = objects.oid) WHERE chain_id = %d AND objects.status = 2",$form_id,$form_id,$chain_entry_id);
		$this->db_query($q);

		// always return an array
		$eids = array();
		while($row = $this->db_next())
		{
			$eids[] = $row["id"];
		};
		return $eids;
	}

	////
	// !loads form chain $id into $this->chain
	// this should probably check for site_id as well, to avoid using the object from the wrong site
	function load_chain($id)
	{
		$this->db_query("SELECT objects.*, form_chains.* FROM objects LEFT JOIN form_chains ON objects.oid = form_chains.id WHERE objects.oid = $id");
		$row = $this->db_next();
		$this->chain = aw_unserialize($row["content"]);
		return $row;
	}

	////
	// !returns the chain id for the chain entry id $cid
	function get_chain_for_chain_entry($cid)
	{
		$cid = (int)$cid;
		if (!($res = aw_cache_get("fc_cache",$cid)))
		{
			$q = "SELECT chain_id FROM form_chain_entries WHERE id = '$cid'";
			$res = $this->db_fetch_field($q,"chain_id");
			aw_cache_set("fc_cache",$cid,$res);
		}
		return $res;
	}

	////
	// !returns a list of form_chains that form $fid is part of
	function get_chains_for_form($fid)
	{
		$ret = array();
		$this->db_query("SELECT chain_id FROM form2chain LEFT JOIN objects ON objects.oid = form2chain.chain_id WHERE form_id = $fid AND objects.status != 0");
		while ($row = $this->db_next())
		{
			$ret[$row["chain_id"]] = $row["chain_id"];
		}
		return $ret;
	}

	////
	// !returns a list of forms that make up form_chain $chid
	function get_forms_for_chain($chid, $ret_name = false)
	{
		if (is_array($res = aw_cache_get("form_base::get_forms_for_chain::$ret_name", $chid)))
		{
			return $res;
		}
		$this->save_handle();
		$ret = array();
		$this->db_query("SELECT form_id,objects.name as name FROM form2chain LEFT JOIN objects ON objects.oid = form2chain.form_id WHERE chain_id = $chid AND objects.status != 0 ORDER BY ord");
		while ($row = $this->db_next())
		{
			if ($ret_name)
			{
				$ret[$row["form_id"]] = $row["name"];
			}
			else
			{
				$ret[$row["form_id"]] = $row["form_id"];
			}
		}
		$this->restore_handle();
		aw_cache_set("form_base::get_forms_for_chain::$ret_name", $chid, $ret);
		return $ret;
	}

	////
	// !returns an array of chain_id => chain_name pairs, one for each chain in the system
	function get_chains($addempty = false)
	{
		$ret = $addempty ? array(0 => "") : array();

		$this->db_query("SELECT chain_id,objects.name as name FROM form2chain LEFT JOIN objects ON objects.oid = form2chain.chain_id WHERE objects.status != 0");
		while ($row = $this->db_next())
		{
			$ret[$row["chain_id"]] = $row["name"];
		}
		return $ret;
	}

	////
	// !I think those should be replaced by generic get and put method in the core class
	// so that I could use $this->get["type"] which then in turn accesses $this->type
	// 
	// well, yeah, of course they do look dumb like this, but that's the point of accessor/mutator functions -
	// to be there in case the representation changes - and if they are like this, then quite possibly
	// we wouldn't have to modify any external code at all. but the function to access the array members - 
	// what's the difference between it and a regular array access (except that the latter is faster) ?
	function get_type()
	{
		return $this->type;
	}

	////
	// !returns loaded form's id
	function get_id()
	{
		return $this->id;
	}

	////
	// !returns loaded form's parent 
	function get_parent()
	{
		return $this->parent;
	}

	////
	// !returns an array of id => type_name pairs for all template elements 
	// (identified by the fact that the type name is set)
	function listall_el_types($addempty = false)
	{
		$ret = $addempty ? array(0 => "") : array();

		$this->db_query("SELECT * FROM form_elements WHERE type_name != ''");
		while ($row = $this->db_next())
		{
			$ret[$row["id"]] = $row["type_name"];
		}
		return $ret;
	}

	////
	// !creates a list of all elements that are not in the current form/output and if folders where
	// to read elements from are set in form settings, then only elements under those folders are returned
	// the last bit does not apply to for outputs just yet
	// if $is_op == true , the existing elements are read from the form output instead of the form
	function listall_elements($is_op = false)
	{
		$ar = array(0 => "");

		$check_parent = false;
		if (is_array($this->arr["el_menus2"]) && count($this->arr["el_menus2"]) > 0)
		{
			$check_parent = true;
		}

		// get list of menus
		$ol = $this->get_menu_list();
		
		// get list of elements in the current form or output
		$elarr = $is_op ? $this->get_all_elements_in_op() : $this->get_all_elements();

		$ol = new object_list(array(
			"class_id" => CL_FORM_ELEMENT,
			"site_id" => array(),
			"lang_id" => array()
		));
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			if (!isset($elarr[$o->id()]))
			{
				// if this element does not exist in this form yet and is in the right folder,
				// add it to the select list.
				if (!$check_parent || $is_op || ($check_parent && in_array($o->parent(),$this->arr["el_menus2"])))
				{
					$ar[$o->id()] = $ol[$o->parent()]."/".$o->name();
				}
			}
		}

		asort($ar);
		return $ar;
	}

	////
	// !loads entry $entry_id for the loaded form and maps the data to the form elements
	function load_entry($entry_id)
	{
		$this->entry_id = $entry_id;

		// reads the data from the configured data source for the form and returns it as an array of el_id => el_value pairs
		// this reads in all elements through all the relations it can find, except reverse relation element relations
		// , true means that it will only read formgen values from the db, not the user values
		$this->entry = $this->read_entry_data($entry_id, true);
		// now $this->entry contains el_id => el_value pairs - not user values though, they are formgen values

		// so we feed the data to the elements and that should be it
		$this->read_entry_from_array($entry_id);
	}

	////
	// !goes over all the elements and distributes the entry loaded to $this->entry to each element
	function read_entry_from_array($entry_id)
	{
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$this->arr["contents"][$row][$col] -> set_entry(&$this->entry, $entry_id);
			};
		};
	}

	////
	// !this function "unloads" the current form entry
	function unload_entry()
	{
		$this->entry_id = 0;
		$this->entry = array();
		$this->read_entry_from_array(0);
	}

	////
	// !returns a cached list of controllers for this form
	// return value is array(controller_id => controller_path_and_name)
	// this can't be put in form.aw, cause form_element needs to access this and then it can't in an output
	function get_list_controllers($add_empty = false)
	{
		if (!$this->controller_instance)
		{
			$this->controller_instance = get_instance(CL_FORM_CONTROLLER);
		}

		if (!($ret = aw_global_get("form_controllers_cache".$add_empty)))
		{
			$ret = $this->controller_instance->listall(array("parents" => $this->arr["controller_folders"],"add_empty" => $add_empty, "add_id" => true));
			aw_global_set("form_controllers_cache".$add_empty,$ret);
		}
		return $ret;
	}

	////
	// !Returns the colspan and rowspan of the specified cell from the specified map
	// used in showing/adminning the form
	// parameters:
	// $i - row
	// $i - column
	// $map - the map from which the spans are calculated
	// $rows - rows in the map
	// $cols - columns in the map
	// if $map or $rows or $cols are omitted, they are taken from $this
	function get_spans($i, $a, $map = -1,$rows = -1, $cols = -1)	// row, col
	{
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
			// we return false if the cell is not the top-left cell of the area, because then we need to skip drawing it
			return false;
		}
	}

	////
	// !adds a column to map $map with dimensions $rows / $cols , after col $after
	function map_add_col($rows,$cols,&$map,$after)
	{
		$nm = array();
		for ($row =0; $row < $rows; $row++)
		{
			for ($col=0; $col <= $after; $col++)
			{
				$nm[$row][$col] = $map[$row][$col];		// copy the left part of the map
			}
		}

		$change = array();
		for ($row = 0; $row < $rows; $row++)
		{
			for ($col=$after+1; $col < ($cols-1); $col++)
			{
				if ($map[$row][$col]["col"] > $after)	
				{
					$nm[$row][$col+1]["col"] = $map[$row][$col]["col"]+1;
					$nm[$row][$col+1]["row"] = $map[$row][$col]["row"];
					$change[] = array("from" => $map[$row][$col], "to" => $nm[$row][$col+1]);
				}
				else
				{
					$nm[$row][$col+1] = $map[$row][$col];
				}
			}
		}

		reset($change);
		while (list(,$v) = each($change))
		{
			for ($row=0; $row < $rows; $row++)
			{
				for ($col=0; $col <= $after; $col++)
				{
					if ($map[$row][$col] == $v["from"])
					{
						$nm[$row][$col] = $v["to"];
					}
				}
			}
		}

		for ($row = 0; $row < $rows; $row++)
		{
			if ($map[$row][$after] == $map[$row][$after+1])
			{
				$nm[$row][$after+1] = $nm[$row][$after];
			}
			else
			{
				$nm[$row][$after+1] = array("row" => $row, "col" => $after+1);
			}
		}

		$map = $nm;
	}

	////
	// !deletes col $d_col from map $map with dimensions [$rows x $cols]
	function map_del_col($rows,$cols,&$map,$d_col)
	{
		$nm = array();
		for ($row =0; $row < $rows; $row++)
		{
			for ($col=0; $col < $d_col; $col++)
			{
				$nm[$row][$col] = $map[$row][$col];	// copy the left part of the map
			}
		}

		// shit. I remember doing this gave me a really bad headache. 
		// .. and now, 6 months later I can understand why :p

		$changes = array();
		for ($row =0 ; $row < $rows; $row++)
		{
			for ($col = $d_col+1; $col < $cols; $col++)
			{
				if ($map[$row][$col]["col"] > $d_col)
				{
					$nm[$row][$col-1] = array("row" => $map[$row][$col]["row"], "col" => $map[$row][$col]["col"]-1);
					$changes[] = array("from" => $map[$row][$col], 
														 "to" => array("row" => $map[$row][$col]["row"], "col" => $map[$row][$col]["col"]-1));
				}
				else
				{
					$nm[$row][$col-1] = $map[$row][$col];
				}
			}
		}
		$map = $nm;
		
		reset($changes);
		while (list(,$v) = each($changes))
		{
			for ($row=0; $row < $rows; $row++)
			{
				for ($col=0; $col < $d_col; $col++)
				{
					if ($map[$row][$col] == $v["from"])
					{
						$map[$row][$col] = $v["to"];
					}
				}
			}
		}
	}

	////
	// !adds a row to the map $map [$rows x $cols] , after row $after
	function map_add_row($rows,$cols,&$map,$after)
	{
		$nm = array();
		for ($row =0; $row <= $after; $row++)
		{
			for ($col=0; $col < $cols; $col++)
			{
				$nm[$row][$col] = $map[$row][$col];		// copy the upper part of the map
			}
		}

		$change = array();
		for ($row = $after+1; $row < ($rows-1); $row++)
		{
			for ($col=0; $col < $cols; $col++)
			{
				if ($map[$row][$col]["row"] > $after)	
				{
					$nm[$row+1][$col]["col"] = $map[$row][$col]["col"];
					$nm[$row+1][$col]["row"] = $map[$row][$col]["row"]+1;
					$change[] = array("from" => $map[$row][$col], "to" => $nm[$row+1][$col]);
				}
				else
				{
					$nm[$row+1][$col] = $map[$row][$col];
				}
			}
		}

		reset($change);
		while (list(,$v) = each($change))
		{
			for ($row=0; $row <= $after; $row++)
			{
				for ($col=0; $col < $cols; $col++)
				{
					if ($map[$row][$col] == $v["from"])
					{
						$nm[$row][$col] = $v["to"];
					}
				}
			}
		}

		for ($col = 0; $col < $cols; $col++)
		{
			if ($map[$after][$col] == $map[$after+1][$col])
			{
				$nm[$after+1][$col] = $nm[$after][$col];
			}
			else
			{
				$nm[$after+1][$col] = array("row" => $after+1, "col" => $col);
			}
		}

		$map = $nm;
	}

	////
	// !deletes row $d_row of map $map [$rows x $cols]
	function map_del_row($rows,$cols,&$map,$d_row)
	{
		$nm = array();
		for ($row =0; $row < $d_row; $row++)
		{
			for ($col=0; $col < $cols; $col++)
			{
				$nm[$row][$col] = $map[$row][$col];	// copy the upper part of the map
			}
		}

		$changes = array();
		for ($row =$d_row+1 ; $row < $rows; $row++)
		{
			for ($col = 0; $col < $cols; $col++)
			{
				if ($map[$row][$col]["row"] > $d_row)
				{
					$nm[$row-1][$col] = array("row" => $map[$row][$col]["row"]-1, "col" => $map[$row][$col]["col"]);
					$changes[] = array("from" => $map[$row][$col], 
														 "to" => array("row" => $map[$row][$col]["row"]-1, "col" => $map[$row][$col]["col"]));
				}
				else
				{
					$nm[$row-1][$col] = $map[$row][$col];
				}
			}
		}
		$map = $nm;
		
		reset($changes);
		while (list(,$v) = each($changes))
		{
			for ($row=0; $row < $d_row; $row++)
			{
				for ($col=0; $col < $cols; $col++)
				{
					if ($map[$row][$col] == $v["from"])
					{
						$map[$row][$col] = $v["to"];
					}
				}
			}
		}
	}

	////
	// !merges the cell ($row,$col) of map $map with the cell above it
	function map_exp_up($rows,$cols,&$map,$row,$col)
	{
		// here we don't need to find the upper bound, because this always is the upper bound
		if ($row > 0)
		{
			// first we must find out the colspan of the current cell and set all the cell above that one to the correct values in the map
			for ($a=0; $a < $cols; $a++)
			{
				if ($map[$row][$a] == $map[$row][$col])
				{
					$map[$row-1][$a] = $map[$row][$col];		// expand the area
				}
			}
		}
	}

	////
	// !merges the cell ($row,$col) in map $map, with the cell below it
	function map_exp_down($rows,$cols,&$map,$row,$col)
	{
		// here we must first find the lower bound for the area being expanded and use that instead the $row, because
		// that is an arbitrary position in the area really.
		for ($i=$row; $i < $rows; $i++)
		{
			if ($map[$i][$col] == $map[$row][$col])
			{
				$r=$i;
			}
			else
			{
				break;
			}
		}

		if (($r+1) < $rows)
		{
			for ($a=0; $a < $cols; $a++)
			{
				if ($map[$row][$a] == $map[$row][$col])
				{
					$map[$r+1][$a] = $map[$row][$col];		// expand the area
				}
			}
		}
	}

	////
	// !merges the cell ($row,$col) in map $map with the cell to the left of it
	function map_exp_left($rows,$cols,&$map,$row,$col)
	{
		// again, this is the left bound, so we don't need to find it
		if ($col > 0)
		{
			for ($a =0; $a < $rows; $a++)
			{
				if ($map[$a][$col] == $map[$row][$col])
				{
					$map[$a][$col-1] = $map[$row][$col];		// expand the area
				}
			}
		}
	}

	////
	// !merges the cell ($row,$col) of map $map with the cell to the right of it
	function map_exp_right($rows,$cols,&$map,$row,$col)
	{
		// here we must first find the right bound for the area being expanded and use that instead the $row, because
		// that is an arbitrary position in the area really.
		for ($i=$col; $i < $cols; $i++)
		{
			if ($map[$row][$i] == $map[$row][$col])
			{
				$r=$i;
			}
			else
			{
				break;
			}
		}

		if (($r+1) < $cols)
		{
			for ($a =0; $a < $rows; $a++)
			{
				if ($map[$a][$r] == $map[$row][$r])
				{
					$map[$a][$r+1] = $map[$row][$r];		// expand the area
				}
			}
		}
	}

	////
	// !splits the cell at $row,$col on map $map vertically
	function map_split_ver($rows,$cols,&$map,$row,$col)
	{
		$lbound = -1;
		for ($i=0; $i < $cols && $lbound==-1; $i++)
		{
			if ($map[$row][$i] == $map[$row][$col])
			{
				$lbound = $i;
			}
		}

		$rbound = -1;
		for ($i=$lbound; $i < $cols && $rbound==-1; $i++)
		{
			if ($map[$row][$i] != $map[$row][$col])
			{
				$rbound = $i-1;
			}
		}

		if ($rbound == -1)
		{
			$rbound = $cols-1;
		}

		$nm = array();
		$center = ($rbound+$lbound)/2;

		for ($i=0; $i < $rows; $i++)
		{
			for ($a=0; $a < $cols; $a++)
			{
				if ($map[$i][$a] == $map[$row][$col])
				{
					if ($map[$i][$a]["col"] < $center)	
					{
						// the hotspot of the cell is on the left of the splitter
						if ($a <= $center)	
						{
							// and we currently are also on the left side then leave it be
							$nm[$i][$a] = $map[$i][$a];
						}
						else
						{
							// and we are on the right side choose a new one
							$nm[$i][$a] = array("row" => $map[$i][$a]["row"], "col" => floor($center)+1);
						}
					}
					else
					{
						// the hotspot of the cell is on the right of the splitter
						if ($a <= $center)
						{
							// and we are on the left side choose a new one
							$nm[$i][$a] = array("row" => $map[$i][$a]["row"], "col" => $lbound);
						}
						else
						{
							// if we are on the same side, use the current value
							$nm[$i][$a] = $map[$i][$a];
						}
					}	
				}
				else
				{
					$nm[$i][$a] = $map[$i][$a];
				}
			}
		}

		$map = $nm;
	}

	function map_split_hor($rows,$cols,&$map,$row,$col)
	{
		$ubound = -1;
		for ($i=0; $i < $rows && $ubound==-1; $i++)
		{
			if ($map[$i][$col] == $map[$row][$col])
			{
				$ubound = $i;
			}
		}

		$lbound = -1;
		for ($i=$ubound; $i < $rows && $lbound==-1; $i++)
		{
			if ($map[$i][$col] != $map[$row][$col])
			{
				$lbound = $i-1;
			}
		}

		if ($lbound == -1)
		{
			$lbound = $rows-1;
		}

		$nm = array();
		$center = ($ubound+$lbound)/2;

		for ($i=0; $i < $rows; $i++)
		{
			for ($a=0; $a < $cols; $a++)
			{
				if ($map[$i][$a] == $map[$row][$col])
				{
					if ($map[$i][$a]["row"] < $center)	
					{
						// the hotspot of the cell is above the splitter
						if ($i <= $center)	
						{
							// and we currently are also above then leave it be
							$nm[$i][$a] = $map[$i][$a];
						}
						else
						{
							// and we are below choose a new one
							$nm[$i][$a] = array("row" => floor($center)+1, "col" => $map[$i][$a]["col"]);
						}
					}
					else
					{
						// the hotspot of the cell is below the splitter
						if ($i <= $center)
						{
							// but we are above, so make new
							$nm[$i][$a] = array("row" => $ubound, "col" => $map[$i][$a]["col"]);
						}
						else
						{
							// if we are on the same side, use the current value
							$nm[$i][$a] = $map[$i][$a];
						}
					}	
				}
				else
				{
					$nm[$i][$a] = $map[$i][$a];
				}
			}
		}

		$map = $nm;
	}
}
?>
