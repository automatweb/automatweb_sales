<?php

// controller types in element - each controller can be used for every one of these,
// they are just here to specify for which controller in the element the controller is selected
define("CTRL_USE_TYPE_ENTRY", 1);			// entry controller - checks on form submit
define("CTRL_USE_TYPE_SHOW", 2);			// show controller - checks on form showing
define("CTRL_USE_TYPE_LB", 3);				// listbox controller - checks every listbox element on showing
define("CTRL_USE_TYPE_DEFVALUE", 4);	// default value controller - if element value is not set the return value of this will be used
define("CTRL_USE_TYPE_VALUE", 5);			// value controller - evals on show and submit - the element value will be the return of this

class form_controller extends form_base
{
	function form_controller()
	{
		$this->form_base();
	}

	/**

		@attrib name=new params=name default="0"

		@param parent required type=int acl="add"
		@param alias_to optional type=int

		@returns


		@comment

	**/
	function add($arr)
	{
		extract($arr);
		$this->read_template("add_controller.tpl");
		$this->mk_path($parent, "Lisa kontroller");

		$l = get_instance("languages");
		$lar = $l->listall();
		foreach($lar as $ld)
		{
			$this->vars(array(
				"lang_name" => $ld["name"],
				"lang_id" => $ld["id"]
			));
			$la.=$this->parse("LANG");
		}
		$this->vars(array(
			"LANG" => $la,
			"reforb" => $this->mk_reforb("submit", array("parent" => $parent, "alias_to" => $alias_to))
		));
		return $this->parse();
	}

	/**

		@attrib name=submit params=name default="0"


		@returns


		@comment

	**/
	function submit($arr = array())
	{
		extract($arr);
		//$this->dequote(&$eq);
		if ($id)
		{
			// update
			$co = $this->load_controller($id);
			$co["name"] = $name;
			$co["meta"]["eq"] = $eq;
			$co["meta"]["errmsg"] = $errmsg;
			$co["meta"]["show_errors_showctl"] = $show_errors_showctl;
			$co["meta"]["warn_only_entry_controller"] = $warn_only_entry_controller;
			$co["meta"]["no_var_replace"] = $no_var_replace;
			$co["meta"]["error_js_pop"] = $error_js_pop;
			$co["meta"]["error_icon"] = $error_icon;
			$this->save_controller($co);
		}
		else
		{
			// add
			$o = obj();
			$o->set_name($name);
			$o->set_class_id(CL_FORM_CONTROLLER);
			$o->set_parent($parent);
			$o->set_status(STAT_ACTIVE);
			$o->set_meta("eq", $eq);
			$o->set_meta("errmsg",$errmsg);
			$o->set_meta("vars", array());
			$o->set_meta("show_errors_showctl" , $show_errors_showctl);
			$o->set_meta("warn_only_entry_controller" , $warn_only_entry_controller);
			$o->set_meta("no_var_replace" , $no_var_replace);
			$o->set_meta("error_js_pop" , $error_js_pop);
			$o->set_meta("error_icon" , $error_icon);
			$id = $o->save();
			if($alias_to)
			{
				$to_o = obj($alias_to);
				$to_o->connect(array(
					"to" => $id,
				));
			}
		}
		return $this->mk_my_orb("change", array("id" => $id));
	}

	/**

		@attrib name=change params=name default="0"

		@param id required type=int acl="edit;view"

		@returns


		@comment

	**/
	function change($arr = array())
	{
		extract($arr);
		$co = $this->load_controller($id);
		$this->read_template("add_controller.tpl");
		$this->mk_path($co["parent"], "Muuda kontrollerit");

		$l = get_instance("languages");
		$lar = $l->listall();
		foreach($lar as $ld)
		{
			$this->vars(array(
				"lang_name" => $ld["name"],
				"lang_id" => $ld["id"],
				"errmsg" => $co["meta"]["errmsg"][$ld["id"]]
			));
			$la.=$this->parse("LANG");
		}

		if (is_array($co["meta"]["vars"]))
		{
			foreach($co["meta"]["vars"] as $var => $vd)
			{
				if ($vd["form_id"])
				{
					$fd = obj($vd["form_id"]);
					$ref = $fd->name();
					if ($vd["el_id"])
					{
						$ed = obj($vd["el_id"]);
						$ref.=".".$ed->name();
					}
				}
				$__false = false;
				$this->vars(array(
					"var_name" => $var,
					"var_value" => $this->get_var_value($co,$var, $__false),
					"ref" => $ref,
					"del_var" => $this->mk_my_orb("del_var", array("id" => $id, "var_name" => $var)),
					"ch_var" => $this->mk_my_orb("change_var", array("id" => $id, "var_name" => $var))
				));
				$vl.=$this->parse("VAR_LINE");
			}
		}
		load_javascript("codepress/codepress.js");
		if(strpos($co["meta"]["eq"], "\"") !== false || strpos($co["meta"]["eq"], "<") !== false)
		{
			$co["meta"]["eq"] = htmlspecialchars($co["meta"]["eq"]);
		}
		$this->vars(array(
			"VAR_LINE" => $vl,
			"add_var" => $this->mk_my_orb("add_var", array("id" => $id)),
			"form_list" => $this->mk_my_orb("form_list", array("id" => $id)),
			"LANG" => $la,
			"name" => $co["name"],
			"eq" => $co["meta"]["eq"],
			"reforb" => $this->mk_reforb("submit", array("id" => $id)),
			"show_errors" => checked($co["meta"]["show_errors_showctl"]),
			"warn_only_entry_controller" => checked($co["meta"]["warn_only_entry_controller"]),
			"no_var_replace" => checked($co['meta']['no_var_replace']),
			"error_js_pop" => checked($co['meta']['error_js_pop']),
			"error_icon" => checked($co['meta']['error_icon'])
		));

		$this->vars(array(
			"CHANGE" => $this->parse("CHANGE"),
			"CHANGE2" => $this->parse("CHANGE2"),
		));
		return $this->parse();
	}

	function load_controller($id)
	{
		$ret = obj($id);
		$ret = $ret->fetch();
		$this->loaded_controller = $ret;
		return $ret;
	}

	////
	// !get a list of form controllers
	// if $parents is set, only controllers in those folders are returned
	// returns array ($controller_id => $controller_path_and_name)
	function listall($arr)
	{
		extract($arr);
		if ($add_empty)
		{
			$ret = array("0" => "");
		}
		else
		{
			$ret = array();
		}
		if (is_array($parents) && count($parents) > 0)
		{
			$wh = " AND objects.parent IN(".join(",",$parents).") ";
		}

		$ol = $this->get_menu_list();

		$this->db_query("SELECT oid,name,parent FROM objects WHERE class_id = ".CL_FORM_CONTROLLER." AND status != 0 $wh");
		while($row = $this->db_next())
		{
			if ($add_id)
			{
				$ret[$row["oid"]] = $ol[$row["parent"]]."/".$row["name"]." (".$row["oid"].")";
			}
			else
			{
				$ret[$row["oid"]] = $ol[$row["parent"]]."/".$row["name"];
			}
		}
		return $ret;
	}

	////
	// !this validates entered data $entry via controller $id
	// $form_ref is a reference to the form that the controller is connected to
	// it is used to access the current entry element values
	// $el_ref is a reference to the current element - it is used to import metadata values
	// returns true, if the data matches the controller and an error message if not
	function do_check($id,$entry,&$form_ref,&$el_ref)
	{
		if (!$id)
		{
			return true;
		}
		$res = $this->eval_controller_ref($id, $entry, $form_ref, $el_ref);
		if (!$res)
		{
			$co = $this->load_controller($id);
			if ($co["meta"]["errmsg"][aw_global_get("lang_id")] != "")
			{
				return $this->replace_vars($co,$co["meta"]["errmsg"][aw_global_get("lang_id")],false,&$form_ref, $el_ref, $entry);
			}
			return false;
		}
		return true;
	}

	////
	// !loads controller $id , replaces variables and evals the equasion
	// $entry is the current element's value
	// form_ref - reference to the form that the current element is a part of
	// $el_ref is a reference to the current element - it is used to import metadata values - optional
	function eval_controller_ref($id, $entry, &$form_ref ,&$el_ref)
	{
		if (!$id)
		{
			return true;	// don't remove this, otherwise all controller checks will fail withut a controller
		}

		$this->form_ref =& $form_ref;
		$this->el_ref =& $el_ref;
		$this->entry = $entry;

		$co = $this->load_controller($id);
		$eq = $this->replace_vars($co,$co["meta"]["eq"],true,$form_ref, $el_ref, $entry);

		$eq = "\$res = ".$eq.";\$contr_finish = true;";

		if (aw_ini_get("site_id") == 139)
		{
			@eval($eq);
		}
		else
		{
			if ($_GET["ffo"] == 1)
			{
				echo "eq = $eq <br>";
			}
			@eval($eq);
		}

		if (!$contr_finish)
		{
			$this->dequote(&$eq);
			@eval($eq);
		}

		return $res;
	}

	////
	// !loads controller $id , replaces variables and evals the equasion
	// $entry is the current element's value
	// form_ref - reference to the form that the current element is a part of
	// $el_ref is a reference to the current element - it is used to import metadata values - optional
	function eval_controller($id, $entry = false, $form_ref = false,$el_ref = false)
	{
		if (!$id)
		{
			return true;	// don't remove this, otherwise all controller checks will fail withut a controller
		}
		$this->form_ref =& $form_ref;
		$this->el_ref =& $el_ref;
		$this->entry = $entry;

		$co = $this->load_controller($id);
		$eq = $this->replace_vars($co,$co["meta"]["eq"],true,$form_ref, $el_ref, $entry);

		$eq = "\$res = ".$eq.";\$contr_finish = true;";

		if (aw_ini_get("site_id") == 139)
		{
			eval($eq);
		}
		else
		{
			eval($eq);
		}

		if (!$contr_finish)
		{
			$this->dequote(&$eq);
			@eval($eq);
		}

		return $res;
	}

	////
	// !this imports all the variable values to equasion $eq
	function replace_vars($co,$eq,$add_quotes,$form_ref = false, $el_ref = false, $el_value = "")
	{
		// load controllers
		$eq = preg_replace("/{load:(\d*)}/e","\$this->_load_ctrl_eq(\\1)",$eq);

		// include files
		$eq = preg_replace("/{include:(.*)}/eU","\$this->_incl_file(\"\\1\")",$eq);

		if ($co['meta']['no_var_replace'] == 1)
		{
			//return $eq;
		}
		// now do element metadata as well
		if (is_object($el_ref) && method_exists($el_ref, "get_metadata"))
		{
			foreach($el_ref->get_metadata() as $mtk => $mtv)
			{
				$eq = str_replace("[el.".$mtk."]",$mtv,$eq);
			}
		}
		$eq = preg_replace("/(\[el\.[-a-zA-Z0-9 _:\(\)\.]*\])/","0",$eq);

		if ($co['meta']['no_var_replace'] == 1)
		{
			return $eq;
		}

		enter_function("form_controller::replace_vars::".$co["oid"]);
		if (is_array($co["meta"]["vars"]))
		{
			foreach($co["meta"]["vars"] as $var => $vd)
			{
//				echo "var = '$var' <br />";
				if (strpos($eq,"[".$var."]") !== false)
				{
//					echo "included <br />";
					$val = str_replace("\"", "\\\"", $this->get_var_value($co, $var, &$form_ref));
//					echo "val = $val <br />";
					if ($add_quotes)
					{
						$val = "\"".str_replace("\"","\\\"",$val)."\"";
					}
					$eq = str_replace("[".$var."]",$val,$eq);
				}
			}
		}

		// now import all current form element values as well
		if (is_object($form_ref) && method_exists($form_ref, "get_all_els"))
		{
			$els = $form_ref->get_all_els();
			foreach($els as $el)
			{
				$var = $el->get_el_name();
	//			echo "var = '$var' eq = $eq <br />";
				if (strpos($eq,"[".$var."]") !== false)
				{
					$val = str_replace("\"", "\\\"", $el->get_controller_value());
					if ($add_quotes)
					{
						$val = "\"".str_replace("\"","\\\"",$val)."\"";
					}
	//				echo "replace '$var' with '$val' <br />";
					$eq = str_replace("[".$var."]",$val,$eq);
				}
			}
		}

		$eq = str_replace("[el]","\"".$el_value."\"",$eq);

		// and finally init all non-initialized vars to zero to avoid parse errors
		$eq = preg_replace("/(\[[-a-zA-Z0-9 _:\(\)\.]*\])/","0",$eq);

		exit_function("form_controller::replace_vars::".$co["oid"]);
		return $eq;
	}

	/** presents the interface for adding variables to controller

		@attrib name=add_var params=name default="0"

		@param id required

		@returns


		@comment

	**/
	function add_var($arr)
	{
		extract($arr);
		$co = $this->load_controller($id);
		$this->mk_path($co["parent"], "<a href='".$this->mk_my_orb("change", array("id" => $id))."'>Muuda kontrollerit</a> / Lisa muutuja");
		$this->read_template("ctr_add_var.tpl");

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_var", array("id" => $id))
		));
		return $this->parse();
	}

	/** saves the variable prefs

		@attrib name=submit_var params=name default="0"


		@returns


		@comment

	**/
	function submit_var($arr)
	{
		extract($arr);
		$co = $this->load_controller($id);

		$var_name = str_replace("'","",$var_name);
		$var_name = str_replace("\"","",$var_name);
		$var_name = str_replace("\\","",$var_name);
		$var_name = strip_tags($var_name);

		if ($change)
		{
			$co["meta"]["vars"][$var_name]["form_id"] = $sel_form;
			$co["meta"]["vars"][$var_name]["el_id"] = $sel_el;
			$co["meta"]["vars"][$var_name]["other_form_id"] = $other_sel_form;
			$co["meta"]["vars"][$var_name]["other_el_id"] = $other_sel_el;
			$co["meta"]["vars"][$var_name]["et_type"] = $entry_type;
			$co["meta"]["vars"][$var_name]["et_entry_id"] = $sel_entry_id;
		}
		else
		{
			if ($var_name != "")
			{
				$co["meta"]["vars"][$var_name] = array("name" => $var_name);
			}
		}

		$this->save_controller($co);

		if ($var_name == "")
		{
			return $this->mk_my_orb("add_var", array("id" => $id));
		}
		else
		{
			return $this->mk_my_orb("change_var", array("id" => $id, "var_name" => $var_name));
		}
	}

	/**

		@attrib name=change_var params=name default="0"

		@param id required
		@param var_name required

		@returns


		@comment

	**/
	function change_var($arr)
	{
		extract($arr);
		$co = $this->load_controller($id);

		$this->mk_path($co["parent"], "<a href='".$this->mk_my_orb("change", array("id" => $id))."'>Muuda kontrollerit</a> / Lisa muutuja");
		$this->read_template("ctr_add_var.tpl");

		$v_form_id = $co["meta"]["vars"][$var_name]["form_id"];
		$v_el_id = $co["meta"]["vars"][$var_name]["el_id"];

		$o_form_id = $co["meta"]["vars"][$var_name]["other_form_id"];
		$o_el_id = $co["meta"]["vars"][$var_name]["other_el_id"];

		$forms = $this->get_flist(array("addfolders" => true, "lang_id" => aw_global_get("lang_id")));
		asort($forms);

		if ($v_form_id)
		{
			$this->vars(array(
				"elements" => $this->picker($v_el_id,array("" => "") + $this->get_elements_for_forms(array($v_form_id)))
			));

			if ($v_el_id)
			{
				$et_type = $co["meta"]["vars"][$var_name]["et_type"];

				$this->vars(array(
					"et_entry_id" => checked($et_type == "entry_id"),
					"et_user_data" => checked($et_type == "user_data"),
					"et_user_entry" => checked($et_type == "user_entry"),
					"et_same_chain" => checked($et_type == "same_chain"),
					"et_other_chain" => checked($et_type == "other_chain"),
					"et_writer_entry" => checked($et_type == "writer_entry"),
					"et_session" => checked($et_type == "session"),
					"et_element_sum" => checked($et_type == "element_sum")
				));

				if ($et_type == "entry_id")
				{
					$et_entry_id = $co["meta"]["vars"][$var_name]["et_entry_id"];

					$this->vars(array(
						"entries" => $this->picker($et_entry_id, $this->get_entries(array("id" => $v_form_id,"addempty" => true))),
						"change_entry" => $this->mk_my_orb("show", array("id" => $v_form_id, "entry_id" => $et_entry_id),"form")
					));
					if ($et_entry_id)
					{
						$this->vars(array(
							"CHANGE_ENTRY" => $this->parse("CHANGE_ENTRY")
						));
					}
					$this->vars(array(
						"ET_ENTRY_ID" => $this->parse("ET_ENTRY_ID"),
					));
				}

				$oe = "";
				if ($et_type == "other_chain")
				{
					$this->vars(array(
						"other_forms" => $this->picker($o_form_id,$forms),
						"other_elements" => $this->picker($o_el_id,array("" => "") + $this->get_elements_for_forms(array($o_form_id)))
					));
					$oe = $this->parse("OTHER_ELEMENT");
				}
				$this->vars(array(
					"OTHER_ELEMENT" => $oe
				));
				$this->vars(array(
					"EL_SEL" => $this->parse("EL_SEL"),
				));
			}
			$this->vars(array(
				"FORM_SEL" => $this->parse("FORM_SEL")
			));
		}

		$this->vars(array(
			"forms" => $this->picker($v_form_id,$forms),
			"reforb" => $this->mk_reforb("submit_var", array("id" => $id, "var_name" => $var_name, "change" => 1)),
			"var_name" => $var_name,
		));

		$this->vars(array(
			"CHANGE" => $this->parse("CHANGE"),
		));
		return $this->parse();
	}

	function save_controller($co)
	{
		$o = obj($co["oid"]);
		$o->set_name($co["name"]);
		$awa = new aw_array($co["meta"]);
		foreach($awa->get() as $k => $v)
		{
			$o->set_meta($k, $v);
		}
		$o->save();
	}

	function get_var_value($co,$var_name, &$form_ref)
	{
		enter_function("form_controller::get_var_value");
		$fid = $co["meta"]["vars"][$var_name]["form_id"];
		$elid = $co["meta"]["vars"][$var_name]["el_id"];

		$o_fid = $co["meta"]["vars"][$var_name]["other_form_id"];
		$o_elid = $co["meta"]["vars"][$var_name]["other_el_id"];

		$et_type = $co["meta"]["vars"][$var_name]["et_type"];
		$et_entry_id = $co["meta"]["vars"][$var_name]["et_entry_id"];
		$cache_key = $fid."::".$elid."::".$et_type."::".$et_entry_id."::".$o_fid."::".$o_elid;
		if ($fid && $elid && $et_type)
		{
/*			if (($val = aw_cache_get("controller::var_value_cache", $cache_key)))
			{
				return $val;
			}*/

			$form =& $this->cache_get_form_instance($fid);

			if ($et_type == "entry_id")
			{
				$entry_id = $et_entry_id;
			}
			else
			if ($et_type == "user_entry")
			{
				// the first entry made for this form by the current user
				$dat = $form->get_entries(array("user" => aw_global_get("uid"),"max_lines" => 1));
				reset($dat);
				list($entry_id,$entry_name) = each($dat);
			}
			else
			if ($et_type == "same_chain")
			{
				// figure out the current chain entry and load it
				// i hope that does the right thing
				$chent = aw_global_get("current_chain_entry");
				if ($chent)
				{
					$chd = $this->get_chain_entry($chent, true);
					$entry_id = $chd[$fid];
					if (!$entry_id)
					{
						// if the entry for this form has not been made in the chain or is in a related form,
						// try and load any entry from the chain, since it will contain all the available elements anyway! yay!
						if (is_array($chd))
						{
							foreach($chd as $_fid => $entry_id)
							{
								if ($entry_id)
								{
									$form =& $this->cache_get_form_instance($_fid);
									break;
								}
							}
						}
					}
				}
			}
			else
			if ($et_type == "other_chain")
			{
				enter_function("form_controller::get_var_value::other_chain");
				// figure out the current chain entry and load it
				// i hope that does the right thing
				$chent = aw_global_get("current_chain_entry");
				if ($chent || (is_object($form_ref) && $o_fid == $form_ref->id))
				{
					// check if the form that the relation element is in, is the current form
					// if so, then read it directly from the shown entry
					if (is_object($form_ref) && $o_fid == $form_ref->id)
					{
						$rel_eid = $form_ref->get_element_value($o_elid, true);
					}
					else
					{
						$chd = $this->get_chain_entry($chent, true);
						$entry_id = $chd[$o_fid];
//						echo "got entry id in THIS chain as $entry_id <br />";
						// now read the related form's entry id, then get the chain entry id from that
						$rel_eid = $this->db_fetch_field("SELECT el_".$o_elid." as val FROM form_".$o_fid."_entries WHERE id = '$entry_id'", "val");
					}
					// get the chain entry and find the corect form entry from that
					list($_tmp, $_tmp2, $_tmp_lbopt, $rel_eid) = explode("_", $rel_eid);
//					echo "got rel_eid as $rel_eid , fid = $fid<br />";
					$t_fid = $this->get_form_for_entry($rel_eid);
					if ($t_fid)
					{
						$rel_ch_eid = $this->db_fetch_field("SELECT chain_id FROM form_".$t_fid."_entries WHERE id = '$rel_eid'", "chain_id");
					}
//					echo "got rel_ch_eid as $rel_ch_eid <br />";

					$chd = $this->get_chain_entry($rel_ch_eid, true);
//					echo "got chd as ".dbg::dump($chd)." <br />";
					$entry_id = $chd[$fid];
//					echo "got entry_id as $entry_id <br />";
					if (!$entry_id)
					{
						// if the entry for this form has not been made in the chain or is in a related form,
						// try and load any entry from the chain, since it will contain all the available elements anyway! yay!
						if (is_array($chd))
						{
							foreach($chd as $_fid => $entry_id)
							{
								if ($entry_id)
								{
									$form =& $this->cache_get_form_instance($_fid);
									break;
								}
							}
						}
					}
				}
				exit_function("form_controller::get_var_value::other_chain");
			}
			else
			if ($et_type == "session")
			{
				$sff = aw_global_get("session_filled_forms");
//				echo "sff = <pre>", var_dump($sff),"</pre> fid = $fid <br />";
				$entry_id = $sff[$fid];
//				echo "entry id for form $fid = $entry_id <br />";
			}
			else
			if ($et_type == "writer_entry")
			{
				$entry_id = aw_global_get("current_writer_entry");
//				echo "got eid $entry_id <br />";
			}
			else
			if ($et_type == "element_sum")
			{
				$cursums = aw_global_get("fg_element_sums");
				exit_function("form_controller::get_var_value");
				return $cursums[$elid];
			}

			if ($entry_id)
			{
				if ($form->entry_id != $entry_id)
				{
//					echo "loading entry for form $form->id , entry = $entry_id <br />";
					enter_function("form_controller::get_var_value::le::form::".$form->id."::eid::".$entry_id);
					$form->load_entry($entry_id, true);
					exit_function("form_controller::get_var_value::le::form::".$form->id."::eid::".$entry_id);
				}
				// and now read the damn value
				$el =& $form->get_element_by_id($elid);
				if (is_object($el))
				{
					enter_function("form_controller::get_var_value::gcv");
					$val = $el->get_controller_value();
					exit_function("form_controller::get_var_value::gcv");
//					echo "val = $val entry = $el->entry , elid = $elid <br />";
					exit_function("form_controller::get_var_value");
					return $val;
				}
				else
				{
					$val = $form->entry[$elid];
//					echo "returning pure val for element $elid <br />";
					exit_function("form_controller::get_var_value");
					return $val;
				}
			}
		}
		exit_function("form_controller::get_var_value");
	}

	/**

		@attrib name=del_var params=name default="0"

		@param id required
		@param var_name optional

		@returns


		@comment

	**/
	function del_var($arr)
	{
		extract($arr);
		$co = $this->load_controller($id);
		unset($co["meta"]["vars"][$var_name]);
		$this->save_controller($co);
		return $this->mk_my_orb("change", array("id" => $id));
	}

	////
	// !returns, for the current loaded controller whether
	// error messages should be shown instead of elements in show controllers
	function get_show_errors()
	{
		return $this->loaded_controller["meta"]["show_errors_showctl"];
	}

	/** shows the user in what forms what elements use this controller and lets the user remove it

		@attrib name=form_list params=name default="0"

		@param id required

		@returns


		@comment

	**/
	function form_list($arr)
	{
		extract($arr);
		$co = $this->load_controller($id);
		$this->read_template("ctrl_form_list.tpl");
		$this->mk_path($co["parent"],"<a href='".$this->mk_my_orb("change", array("id" => $id))."'>Muuda kontrollerit</a> / Formide nimekiri");

		// hoo-kay, now we gots to figure out how to get the forms this is used in
		// since we bloody well can't load every form in the database we must have the relations
		// somewhere written down
		// so we will get table called form_controller2element(ctrl_id, form_id, el_id, type)

		$this->vars(array(
			"ENTRY_ELEMENT" => $this->_do_type($id,CTRL_USE_TYPE_ENTRY,"ENTRY_ELEMENT"),
			"SHOW_ELEMENT" => $this->_do_type($id,CTRL_USE_TYPE_SHOW,"SHOW_ELEMENT"),
			"LB_ELEMENT" => $this->_do_type($id,CTRL_USE_TYPE_LB,"LB_ELEMENT"),
			"DEFVL_ELEMENT" => $this->_do_type($id,CTRL_USE_TYPE_DEFVALUE,"DEFVL_ELEMENT"),
			"VL_ELEMENT" => $this->_do_type($id,CTRL_USE_TYPE_VALUE,"VL_ELEMENT"),
			"reforb" => $this->mk_reforb("submit_form_list", array("id" => $id))
		));
		return $this->parse();
	}

	function _do_type($id, $type, $tpl)
	{
		$ret = "";
		$this->db_query("SELECT form_id, el_id, form_o.name as form_name, el_o.name as el_name
										 FROM form_controller2element AS fc2e
										 LEFT JOIN objects AS form_o ON form_o.oid = fc2e.form_id
										 LEFT JOIN objects AS el_o ON el_o.oid = fc2e.el_id
										 WHERE ctrl_id = '$id' AND type = '$type' AND form_o.status != 0");
		while ($row = $this->db_next())
		{
			$this->vars(array(
				"form_name" => $row["form_name"],
				"element_name" => $row["el_name"],
				"form_id" => $row["form_id"],
				"el_id" => $row["el_id"]
			));
			$ret.=$this->parse($tpl);
		}
		return $ret;
	}

	/** saves the changes the user has made to the element list

		@attrib name=submit_form_list params=name default="0"


		@returns


		@comment

	**/
	function submit_form_list($arr)
	{
		extract($arr);
		$this->_proc_arr($id, $entryels, $entryels_n, CTRL_USE_TYPE_ENTRY);
		$this->_proc_arr($id, $showels, $showels_n, CTRL_USE_TYPE_SHOW);
		$this->_proc_arr($id, $lbels, $lbels_n, CTRL_USE_TYPE_LB);
		$this->_proc_arr($id, $defvlels, $defvlels_n, CTRL_USE_TYPE_DEFVALUE);
		$this->_proc_arr($id, $vlels, $vlels_n, CTRL_USE_TYPE_VALUE);

		return $this->mk_my_orb("form_list", array("id" => $id));
	}

	function _proc_arr($id, $ar, $ar_n, $typ)
	{
		if (is_array($ar))
		{
			foreach($ar as $fid => $forms)
			{
				if (is_array($forms))
				{
					foreach($forms as $elid => $one)
					{
						if ($ar_n[$fid][$elid] != 1)
						{
							// this was removed, load the form and remove controller
							$f = get_instance(CL_FORM);
							$f->load($fid);
							$f->remove_controller_from_element(array(
								"controller" => $id,
								"element" => $elid,
								"type" => $typ
							));
//							echo "tried to remove from form $fid controller $id , element $elid , type = $typ <br />";
							$f->save();
						}
					}
				}
			}
		}
	}

	function is_warning_controller($id)
	{
		$co = $this->load_controller($id);
		return $co["meta"]["warn_only_entry_controller"];
	}

	////
	// !loads controller, replaces vars - thevars are taken from the current controller, not the linked controller scope
	function _load_ctrl_eq($id)
	{
		$co = $this->load_controller($id);
		return $this->replace_vars($co,$co["meta"]["eq"],true,$this->form_ref, $this->el_ref, $this->entry);
	}

	function _incl_file($file)
	{
		$fn = aw_ini_get("site_basedir")."/".$file.".".aw_ini_get("ext");
		$fc = $this->get_file(array("file" => $fn));
		$fc = preg_replace("/{include:(.*)}/eU","\$this->_incl_file(\\1)",$fc);
		return $fc;
	}

	function parse_alias($args = array())
	{
		extract($args);
		return $this->eval_controller($alias["target"]);
	}

	function do_check_and_html($id, $val, &$that, &$el)
	{
		$res = $this->do_check($id, $val, $that, $el);
		$str = "";
		if ($res !== true)
		{
			if ($this->loaded_controller["meta"]["error_js_pop"])
			{
				$str .= "<script language=\"javascript\">alert(\"$res\");</script>";
			}

			if ($this->loaded_controller["meta"]["error_icon"])
			{
				$str .= "<img src='".aw_ini_get("baseurl")."/img/error.gif"."' alt='$res'>";
			}
			else
			{
				$str .= "<font color='red' size='2'>";
				$str .= $res;
				$str .= "</font>";
			}
		}
		return $str;
	}
}
