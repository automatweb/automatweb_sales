<?php
/*
@classinfo  maintainer=kristo
*/

class form_ctr_alias extends core
{
	function form_ctr_alias()
	{
		$this->init("forms");
	}

	////
	// !loads controller, replaces vars - thevars are taken from the current controller, not the linked controller scope
	function _load_ctrl_eq($id)
	{
		$co = $this->load_controller($id);
		return $co["meta"]["eq"];
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
		$id = $args["alias"]["target"];
		$ret = obj($id);
		$co = $ret->fetch();
		$this->loaded_controller = $co;

		$eq = $this->replace_vars($co,$co["meta"]["eq"],true,$form_ref, $el_ref, $entry);

		$eq = "\$res = ".$eq.";\$contr_finish = true;";
		eval($eq);
		if (!$contr_finish)
		{
			$this->dequote(&$eq);
			eval($eq);
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
			return $eq;
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

			$fi = get_instance("formgen/form");
			$form =& $fi->cache_get_form_instance($fid);

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
}
