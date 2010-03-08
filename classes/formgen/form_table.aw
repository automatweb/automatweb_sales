<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_table.aw,v 1.90 2008/08/27 08:54:12 kristo Exp $
/*
@classinfo  maintainer=kristo
*/
classload("formgen/form_base");
class form_table extends form_base
{
	function form_table()
	{
		$this->form_base();
		$this->sub_merge = 1;
		lc_load("definition");
		$this->lc_load("form","lc_form");
		$this->fakels = array(
			"change" => t("Change"), 
			"view" => t("View"), 
			"special" => t("Special"), 
			"delete" => t("Delete"), 
			"created" => t("Created"), 
			"modified" => t("Modified"), 
			"uid" => t("UID"), 
			"active" => t("Active"), 
			"chpos" => t("Move"),
			"order" => t("Order"), 
			"select" => t("Select"),
			"hidden_select" => t("Peidetud Select"),
			"jrk" => t("Jrk"),
			"cnt" => t("Count"),
			"formel" => t("Koguse element"),
			"formel_price" => t("Hinna element"),
			"entry_id" => t("Sisestuse ID"),
			"text" => t("Tekst")
		);

		$this->lang_id = aw_global_get("lang_id");
		$this->buttons = array("save" => t("Salvesta"), "add" => t("Lisa"), "delete" => t("Kustuta"), "move" => t("Liiguta"));
		$this->ru = aw_global_get("REQUEST_URI");
		$this->image = get_instance(CL_IMAGE);
		$this->uid = aw_global_get("uid");
		$this->controller_instance = get_instance(CL_FORM_CONTROLLER);
	}

	////
	// !returns an array of forms that this table gets elements from
	function get_forms_for_table($id)
	{
		$ret = array();
		$this->db_query("SELECT * FROM form_table2form WHERE table_id = $id");
		while ($row = $this->db_next())
		{
			$ret[$row["form_id"]] = $row["form_id"];
		}
		return $ret;
	}

	////
	//  !returns an array of tables that belong to this form
	function get_form_tables_for_form($id)
	{
		$ret = array();
		$this->db_query("SELECT * FROM form_table2form LEFT JOIN objects ON (form_table2form.table_id = objects.oid) WHERE form_id = '$id'");
		while ($row = $this->db_next())
		{
			$ret[$row["table_id"]] = $row["name"];
		}
		return $ret;
	}

	////
	// !starts the table data definition for table $id
	// form_for_entry_id - optional - the id of the form that has the entries with the id that will be in the entry_id column
	function start_table($id, $form_for_entry_id = 0)
	{
		$this->load_table($id);

		load_vcl("table");
		$this->t = new aw_table(array("prefix" => "fg_".$id));

		// reset css style counter
		$this->used_styles = array();

		// this figures out which fields in the table are numeric and tells the vcl table component about their names
		$this->set_numeric_fields_in_table();

		// this is used when showing order elements - we need to know to which form the entry currently being shown belongs to
		// so this should be passed to this function if all rows are from the same form, or
		// set before calling row_data
		$this->form_for_entry_id = $form_for_entry_id;

		// the columns that have data in them in any row get marked here, so we can honor 
		// "don't show if empty" flag for columns
		$this->table_not_empty_cols = array();

		// figure out a unique name for the form that contains this table
		$cnt = 1;
		$fns = aw_global_get("form_table_html_form_names");
		while (isset($fns["tb_".$this->table_id."_".$cnt]))
		{
			$cnt++;
		}
		$fns["tb_".$this->table_id."_".$cnt] = 1;
		if ($cnt == 1)
		{
			$this->table_html_form_name = "tb_".$this->table_id;
		}
		else
		{
			$this->table_html_form_name = "tb_".$this->table_id."_".$cnt;
		}
		aw_global_set("form_table_html_form_names", $fns);

		if ($GLOBALS["tbl_dbg"] || $GLOBALS["fg_tbl_dbg"])
		{
			echo "table id = $id <br />";
		}

		enter_function("form_table::groupsettings");
		$this->in_show_all_entries_groups = false;
		if (is_array($this->table["user_entries_except_grps"]) && count($this->table["user_entries_except_grps"]) > 0)
		{
			if (count(array_intersect($this->table["user_entries_except_grps"],aw_global_get("gidlist"))) > 0)
			{
				$this->in_show_all_entries_groups = true;
			}
		}

		$this->show_modifiedby = array();

		// find in which group settings group the current user is
		if (is_array($this->table["grpsettings"]))
		{
			$pri = -100000000;
			$show_grps = array();
			$found = false;
			foreach($this->table["grpsettings"] as $gst)
			{
				$diff = array_intersect(aw_global_get("gidlist"), $gst["from_grps"]);
				if ($gst["pri"] >= $pri && count($diff) > 0)
				{
					$show_grps = $gst["to_grps"];
					$found = true;
				}
			}
		}

		exit_function("form_table::groupsettings");

		// this is for no_show_oneliners 
		$this->num_lines = 0;

		// mark down the path
		$old_sk = $GLOBALS["old_sk"];
		$tbl_sk = $GLOBALS["tbl_sk"];
		$fg_table_sessions = aw_global_get("fg_table_sessions");

		// copy the first part of the path from the previous search
		$fg_table_sessions[$tbl_sk] = $fg_table_sessions[$old_sk];

		// check that everyhting is normal
		if (!is_array($fg_table_sessions[$tbl_sk]))
		{
			$fg_table_sessions[$tbl_sk] = Array();
		}

		if ($tbl_sk != "")
		{
			// and finally, add the current search to the path
			$num = count($fg_table_sessions[$tbl_sk]);
			$req = aw_global_get("REQUEST_URI");
			$req = str_replace("&print=1", "", $req);
			$req = str_replace("?print=1", "", $req);
			if ($fg_table_sessions[$tbl_sk][$num-1] != $req)
			{
				$fg_table_sessions[$tbl_sk][] = $req;
			}
		}
		aw_session_set("fg_table_sessions", $fg_table_sessions);
	}

	////
	// !adds another row of data to the table
	function row_data(&$dat,$form_id = 0,$section = 0 ,$op_id = 0,$chain_id = 0, $chain_entry_id = 0)
	{
		enter_function("form_table::row_data", array());

		// check if we should perhaps not show the damn entry
		if (isset($this->table["has_grpsettings"]) && $this->table["has_grpsettings"])
		{
			if ($this->show_modifiedby[$dat["createdby"]] != $dat["createdby"])
			{
				return;
			}
		}
		else
		if ($this->table["user_entries"])
		{
			if ($dat["createdby"] != $this->uid && !$this->in_show_all_entries_groups)
			{
				return;
			}
		}


		$this->last_row_data = $dat;

		$this->num_lines++;

		$reset_aliases = array();

		if ($form_id != 0)
		{
			// here also make the view and other links
			$change_link = $this->get_link("change", $form_id,$section,$op_id,$chain_id,$chain_entry_id, $dat["entry_id"] ? $dat["entry_id"] : $dat['id']);

			$has_change_col = false;
			for ($i = 0; $i < $this->table["cols"]; $i++)
			{
				if (is_array($this->table['defs'][$i]['els']))
				{
					if (in_array("change",$this->table["defs"][$i]["els"]))
					{
						$has_change_col = true;
						if ($this->table["skip_one_liners_col"] == $i && $this->table["skip_one_liners"])
						{
							$this->last_table_alias_url = $change_link;
						}
					}
				}
			}
			if ($has_change_col)
			{
				$this->table["change_cols"]["change"] = "change";
				$dat["ev_change"] = $this->table["texts"]["change"][$this->lang_id];
			}
			$chcls = new aw_array($this->table["change_cols"]);
			foreach($chcls->get() as $chel)
			{
				$cl = $this->get_col_for_el($chel);
				$popdat = $this->table["defs"][$cl];
				if (isset($popdat["link_popup"]) && $popdat["link_popup"])
				{
					$change_link = sprintf("javascript:ft_popup('%s&type=popup','popup',%d,%d,%d,%d,%d,%d)",
						$change_link,
						$popdat["link_popup_scrollbars"],
						!$popdat["link_popup_fixed"],
						$popdat["link_popup_toolbar"],
						$popdat["link_popup_addressbar"],
						$popdat["link_popup_width"],
						$popdat["link_popup_height"]
					);
				};

				// check if any image aliases are set for this column and if there are, stick them in the link
				// and remove them from the array so that they will not be shown l8r
				if (is_array($popdat["alias"]))
				{
					// first, the image aliases, because they affect the row contents
					foreach($popdat["alias"] as $_aidx => $aid)	
					{
						$alias_data = $popdat["alias_data"][$aid];
						if ($alias_data["class_id"] == CL_IMAGE)
						{
							$imgdat = $this->image->get_image_by_id($alias_data["target"]);
							$dat["ev_".$chel] = "<img border='0' src='".$imgdat["url"]."' alt='".$dat["ev_".$chel]."'>";
							$reset_aliases[$cl][$_aidx] = $this->table["defs"][$cl]["alias"][$_aidx];
							unset($this->table["defs"][$cl]["alias"][$_aidx]);
						}
					}
				}

				$dat["ev_".$chel] = "<a href=\"".$change_link."\">".$dat["ev_".$chel]."</a>";
			}

			$v_s = $section;
			if ($this->get_col_for_el("view"))
			{
				// only do this, when necessary and perhaps avoid the next loop
				$this->table["view_cols"]["view"] = "view";
				$dat["ev_view"] = $this->table["texts"]["view"][$this->lang_id];
			}


			$show_link = $this->get_link("show", $form_id,$v_s,$op_id,$chain_id,$chain_entry_id, $dat["entry_id"] ? $dat["entry_id"] : $dat['id']);
			$show_link_popup = $this->get_link("show_popup", $form_id,$v_s,$op_id,$chain_id,$chain_entry_id, $dat["entry_id"] ? $dat["entry_id"] : $dat['id']);
			
			if (is_array($this->table["view_cols"]))
			{
				foreach($this->table["view_cols"] as $v_el)
				{
					$cl = $this->get_col_for_el($v_el);
					$_caption = $dat["ev_".$v_el];
					if ($this->table["skip_one_liners_col"] == $cl && $this->table["skip_one_liners"])
					{
						$this->last_table_alias_url = $show_link;
					}

					$popdat = $this->table["defs"][$cl];
					if (isset($popdat["link_popup"]) && $popdat["link_popup"])
					{
						$show_link = sprintf("javascript:ft_popup('%s&type=popup','popup',%d,%d,%d,%d,%d,%d)",
							$show_link_popup,
							$popdat["link_popup_scrollbars"],
							!$popdat["link_popup_fixed"],
							$popdat["link_popup_toolbar"],
							$popdat["link_popup_addressbar"],
							$popdat["link_popup_width"],
							$popdat["link_popup_height"]
						);
					};

					if (is_array($popdat["alias"]))
					{
						// first, the image aliases, because they affect the row contents
						foreach($popdat["alias"] as $_aidx => $aid)	
						{
							$alias_data = $popdat["alias_data"][$aid];
							if ($alias_data["class_id"] == CL_IMAGE)
							{
								$imgdat = $this->image->get_image_by_id($alias_data["target"]);
								$_caption = "<img border='0' src='".$imgdat["url"]."' alt='".$dat["ev_".$v_el]."'>";
								$reset_aliases[$cl][$_aidx] = $this->table["defs"][$cl]["alias"][$_aidx];
								unset($this->table["defs"][$cl]["alias"][$_aidx]);
							}
						}
					}

					if ($popdat["link_section"] != "")
					{
						$show_link = aw_url_change_var("section", $popdat["link_section"], $show_link);
					}
					// I don't see _targetwin being defined anywhere and this causes a warning
					$dat["ev_".$v_el] = sprintf("<a href=\"%s\">%s</a>",$show_link,$_caption);
				}
			}

			$del_link = $this->get_link("delete", $form_id,$section,$op_id,$chain_id,$chain_entry_id, $dat["entry_id"] ? $dat["entry_id"] : $dat['id']);
			$del_cl = $this->get_col_for_el("delete");
			$deltxt = $this->table["texts"]["delete"][$this->lang_id];
			if ($del_cl)
			{
				$popdat = $this->table["defs"][$del_cl];
				if (is_array($popdat["alias"]))
				{
					// first, the image aliases, because they affect the row contents
					foreach($popdat["alias"] as $_aidx => $aid)	
					{
						$alias_data = $popdat["alias_data"][$aid];
						if ($alias_data["class_id"] == CL_IMAGE)
						{
							$imgdat = $this->image->get_image_by_id($alias_data["target"]);
							$deltxt = "<img border='0' src='".$imgdat["url"]."' alt='".$deltxt."'>";
							$reset_aliases[$del_cl][$_aidx] = $this->table["defs"][$del_cl]["alias"][$_aidx];
							unset($this->table["defs"][$del_cl]["alias"][$_aidx]);
						}
					}
				}
			}

			$dat["ev_delete"] = "<a href=\"".$del_link."\">".$deltxt."</a>";

			if (isset($dat["created"]))
			{
				$dat["ev_created"] = $this->time2date($dat["created"], 2);
			};

			if (isset($dat["modifiedby"]))
			{
				$dat["ev_uid"] = $dat["modifiedby"];
			};

			if (isset($dat["modified"]))
			{
				$dat["ev_modified"] = $this->time2date($dat["modified"], 2);
			};
			$dat["ev_select"] = "<input type='checkbox' name='sel[".$dat["entry_id"]."]' ".checked(isset($this->table["select_default"]) && ($this->table["select_default"]))." VALUE='1'>";
			$dat["ev_hidden_select"] = "<input type='hidden' name='sel[".$dat["entry_id"]."]' ".checked(isset($this->table["select_default"]) && ($this->table["select_default"]))." VALUE='1'>";
			$dat["ev_jrk"] = "[__jrk_replace__]";
		}

		$cursums = aw_global_get("fg_element_sums");

		// here we must preprocess the data, cause then the column names will be el_col_[col_number] not element names
		for ($col = 0; $col < $this->table["cols"]; $col++)
		{
			$cc = $this->table["defs"][$col];

			$awa = new aw_array($cc["el_no_row_if_empty"]);
			foreach($awa->get() as $el_no_row_if_empty_k => $el_no_row_if_empty_v)
			{
				if ($dat["ev_".$el_no_row_if_empty_k] == "")
				{
					return;
				}
			}
			
			$dat["ev_text"] = $cc["is_type_text"];
			if (is_array($cc["els"]) && !$cc["not_active"])
			{
				// do this better. 
				// first we compile the elements in the column together into one string and add their 
				// separators
				$str = array();
				$noshowstr = array();
				foreach($cc["els"] as $elid)
				{
					if ($dat["ev_".$elid] != "" || true)
					{
						if ($cc["el_show"][$elid] == 1)
						{
							$cursums[$elid] += $dat["ev_".$elid];
							if ($cc["is_edit"] && $cc["el_edit"][$elid])
							{
								$str[$elid] = $this->_get_edit_element($elid, $dat, $cc);
							}
							else
							{
								if ($cc['el_main_types'][$elid] == 'date' && count($cc['els']) == 1)
								{
									$str[$elid] = $dat["el_".$elid];
								}
								else
								{
									$str[$elid] = $dat["ev_".$elid];
								}
							}
						}
						else
						{
							$noshowstr[$elid] = $cc['el_main_types'][$elid] == 'date' ? $dat["el_".$elid] : $dat["ev_".$elid];
						}
					}
					else
					if ($cc["el_show"][$elid] == 1)
					{
						// order element will never have a value in the data
						if ($elid == "order" )
						{
							$str[$elid] = $this->get_order_url($col,$dat, $form_id);
						}
					}
				}

				// now go over the $str and $noshowstr arrays and put separators between them
				// if the values are not empty
				$_tstr = array();
				$_preve = 0;
				foreach($str as $_elid => $_elv)
				{
					if ($_elv != "")
					{
						$_tstr[$_elid] .= $this->table["defs"][$col]["el_sep_pre"][$_elid];
						$_tstr[$_elid] .= $_elv;
						$_tstr[$_elid] .= $this->table["defs"][$col]["el_sep"][$_elid];
					}
				}
				$str = join($this->table["defs"][$col]["col_el_sep"],$_tstr);

				$_tstr = array();
				foreach($noshowstr as $_elid => $_elv)
				{
					if ($_elv != "")
					{
						$_tstr[$_elid] .= $this->table["defs"][$col]["el_sep_pre"][$_elid];
						$_tstr[$_elid] .= $_elv;
						$_tstr[$_elid] .= $this->table["defs"][$col]["el_sep"][$_elid];
					}
				}
				$noshowstr = join($this->table["defs"][$col]["col_el_sep"],$_tstr);

				
				$textvalue = $str;

				// then we add the aliases to the column
				$str = $this->process_row_aliases($str, $cc, $dat, $col, $section, $form_id, $textvalue, $noshowstr);

				// and then, finally some misc settings
				if (isset($cc["link_el"]))
				{
					$linktext = $str;
					$ar = new aw_array($cc["alias"]);
					foreach($ar->get() as $aid)	
					{
						$alias_data = $cc["alias_data"][$aid];
						if ($alias_data["class_id"] == CL_IMAGE)
						{
							$linktext = $this->get_image_alias_url($str, $alias_data["target"], $col, $noshowstr);
						}
					}
					$ar = new aw_array($reset_aliases[$col]);
					foreach($ar->get() as $aid)	
					{
						$alias_data = $cc["alias_data"][$aid];
						if ($alias_data["class_id"] == CL_IMAGE)
						{
							$linktext = $this->get_image_alias_url($str, $alias_data["target"], $col, $noshowstr);
						}
					}
					if (isset($cc["link_popup"]) && $cc["link_popup"])
					{
						if ($dat["ev_".$cc["link_el"]] != "")
						{
							$str = "<a href=\"".sprintf("javascript:ft_popup('%s&type=popup','popup',%d,%d,%d,%d,%d,%d)",
								$dat["ev_".$cc["link_el"]],
								$cc["link_popup_scrollbars"],
								!$cc["link_popup_fixed"],
								$cc["link_popup_toolbar"],
								$cc["link_popup_addressbar"],
								$cc["link_popup_width"],
								$cc["link_popup_height"]
							)."\">".$linktext."</a>";
						}
						else
						{
							$str = "";
						}
					}
					else
					{
						if ($dat["ev_".$cc["link_el"]] != "")
						{
							$target = "";
							if ($cc["link_new_window"])
							{
								$target = "target=\"_blank\"";
							}

							// try to detect if the damn thing is a file url
							// if it is, check if the file can be viewed
							// if not, don't show the link
							$__link_url = $dat["ev_".$cc["link_el"]];
							$__show_link = true;
							if (strpos($__link_url, "class=file/action=preview/id=") !== false)
							{
								$__link_url = file::check_url($__link_url);
								if (preg_match("/class=file\/action=preview\/id=(\d+)/ims", $__link_url, $_file_ids))
								{
									if (!$this->can("view", trim($_file_ids[1])))
									{
										$__show_link = false;
									}
								}
							}

							if ($__show_link)
							{
								$str = "<a $target href='".$__link_url."'>".$linktext."</a>";
							}
							else
							{
								$str = "";
							}
						}
						else
						{
							$str = "";
						}
					}
				}
				else
				if (isset($this->table["defs"][$col]["is_email"]))
				{
					if ($this->table["defs"][$col]["link"])
					{
						$str = "<a href='mailto:".$noshowstr."'>".$str."</a>";
					}
					else
					{
						$str = "<a href='mailto:".$str."'>".$str."</a>";
					}
				}
				
				$_a = preg_replace("/<a (.*)>(.*)<\/a>/U","\\2",$str);
				if (trim($_a) != "")
				{
					$this->table_not_empty_cols[$col] = true;
				}
				
				if ($this->table["defs"][$col]["val_controller"])
				{
					$str = $this->controller_instance->eval_controller($this->table["defs"][$col]["val_controller"], $str, false, $dat);
				}
				if (!$this->table["defs"][$col]["link"])
				{
					$dat["ev_col_".$col] = $this->create_email_links($str);
				}
				else
				{
					$dat["ev_col_".$col] = $str;
				}
			}
		}
		
		foreach($reset_aliases as $col => $adat)
		{
			foreach($adat as $aidx => $aval)
			{
				$this->table["defs"][$col]["alias"][$aidx] = $aval;
			}
		}

		aw_global_set("fg_element_sums", $cursums);
		$this->t->define_data($dat);
		exit_function("form_table::row_data", array());
	}

	////
	// !reads the loaded entries from array of forms $forms and adds another row of data to the table
	function row_data_from_form($forms,$special = "")
	{
		enter_function("form_table::row_data_from_form", array());
		$dat = array();
		foreach($forms as $form)
		{
			if (!isset($dat["entry_id"]))
			{
				$dat["entry_id"] = $form->entry_id;
			}

			for ($row = 0; $row < $form->arr["rows"]; $row++)
			{
				for ($col = 0; $col < $form->arr["cols"]; $col++)
				{
					$elar = array();
					$form->arr["contents"][$row][$col]->get_els(&$elar);
					reset($elar);
					while (list(,$el) = each($elar))
					{
						$dat["ev_".$el->get_id()] = $el->get_value();
						$dat["el_".$el->get_id()] = $el->get_val();
					}
				}
			}
		}
		return $this->row_data($dat);
	}

	////
	// !returns an array of forms used in the table. each entry in the array is an array of elements in that form that are used
	// assumes the table is loaded already.
	function get_used_elements()
	{
		$ret = array();
		for ($i=0; $i < $this->table["cols"]; $i++)
		{
			if (is_array($this->table["defs"][$i]["el_forms"]))
			{
				foreach($this->table["defs"][$i]["el_forms"] as $elid => $fid)
				{
					if ($fid)
					{
						$ret[$fid][$elid] = $elid;
					}
				}
			}
		}
		
		if (is_array($this->table["defsort"]))
		{
			foreach($this->table["defsort"] as $el)
			{
				$fid = $this->table["defsort_forms"][$el["el"]];
				if ($fid)
				{
					$ret[$fid][$el["el"]] = $el["el"];
				}
			}
		}

		if (is_array($this->table["rgrps"]))
		{
			foreach($this->table["rgrps"] as $nr => $dat)
			{
				$ret[$this->table["rgrps_forms"][$dat["el"]]][$dat["el"]] = $dat["el"];
				if ($dat["sort_el"])
				{
					$fid = $this->table["rgrps_forms"][$dat["sort_el"]];
					if ($fid)
					{
						$ret[$fid][$dat["sort_el"]] = $dat["sort_el"];
					}
				}
				if ($dat["search_val_el"])
				{
					$fid = $this->table["rgrps_forms"][$dat["search_val_el"]];
					if ($fid)
					{
						$ret[$fid][$dat["search_val_el"]] = $dat["search_val_el"];
					}
				}
				if (is_array($dat["data_els"]))
				{
					foreach($dat["data_els"] as $_del)
					{
						$fid = $this->table["rgrps_forms"][$_del];
						if ($fid)
						{
							$ret[$fid][$_del] = $_del;
						}
					}
				}
			}
		}

		return $ret;
	}

	////
	// !finalizes table and generates html
	// $no_form_tags - if true, no <form> </form> tags are put on table
	function finalize_table($arr = array())
	{
		extract($arr);

		// check for that damn skip_one_liners thingie
		if ($this->table["skip_one_liners"] && $this->num_lines < 2 && $this->last_table_alias_url != "")
		{
			// now we need to figure out the damn url. well. ok, since it is always the only one in the table
			// we just remember the last one we made
			header("Location: ".$this->last_table_alias_url);
			die();
		}

		// we do this here, so that we can avoid defining the cols that are marked as "don't show empty" and are empty
		$this->t->parse_xml_def_string($this->get_xml());

		if (is_array($this->table["defsort"]))
		{
			$_sby = array();
			$_sord = array();
			foreach($this->table["defsort"] as $nr => $dat)
			{
				if ($dat["el"] == "created")
				{
					$this->t->set_numeric_field("created");
					$_sby["created"] = "created";
					$_sord["created"] = $dat["type"];
				}
				else
				{
					$cl = $this->get_col_for_el($dat["el"]);
					if ($cl === false)
					{
						$cl = gen_uniq_id();
					}
					if ($this->table["defs"][$cl]['el_main_types'][$dat['el']] == 'date')
					{
						$_sby["ev_col_".$cl] = "el_".$dat["el"];
						$this->t->set_numeric_field("ev_col_".$cl);
						$this->t->set_numeric_field("el_".$dat["el"]);
						$_sord["el_".$dat["el"]] = $dat["type"];
					}
					else
					{
						$_sby["ev_col_".$cl] = "ev_".$dat["el"];
						$_sord["ev_".$dat["el"]] = $dat["type"];
					}
					$_sord["ev_col_".$cl] = $dat["type"];
				}
			}
			$this->t->set_default_sortby($_sby);
			$this->t->set_default_sorder($_sord);
		}

		$r_g = false;
		$v_g = false;
		$rgroupdat = false;
		$vgroupdat = false;
		if (is_array($this->table["rgrps"]))
		{
			foreach($this->table["rgrps"] as $nr => $dat)
			{
				$cl = $this->get_col_for_el($dat["el"]);
				if ($dat["vertical"])
				{
					$v_g["ev_".$dat["el"]] = "ev_".$dat["el"];
					$vgroupdat["ev_".$dat["el"]]["sort_el"] = "ev_".$dat["sort_el"];
					$vgroupdat["ev_".$dat["el"]]["sort_order"] = $dat["sort_order"];
				}
				else
				{
					$r_g["ev_col_".$cl] = "ev_".$dat["el"];
					if ($dat["sort_el"])
					{
						$rgroupsortdat["ev_col_".$cl]["sort_el"] = "ev_".$dat["sort_el"];
					}
					else
					{
						$rgroupsortdat["ev_col_".$cl]["sort_el"] = "ev_".$dat["el"];
					}

					$rgroupsortdat["ev_col_".$cl]["sort_order"] = $dat["sort_order"];
					if (is_array($dat["data_els"]))
					{
						foreach($dat["data_els"] as $datel)
						{
							$rgroupdat["ev_col_".$cl][$datel]["el"] = "ev_".$datel;
							$rgroupdat["ev_col_".$cl][$datel]["sep"] = $dat["data_els_seps"][$datel];
							$rgroupdat["ev_col_".$cl][$datel]["sep_after"] = $dat["data_els_seps_after"][$datel];
							$rgroupdat["ev_".$dat["el"]][$datel]["el"] = "ev_".$datel;
							$rgroupdat["ev_".$dat["el"]][$datel]["sep"] = $dat["data_els_seps"][$datel];
							$rgroupdat["ev_".$dat["el"]][$datel]["sep_after"] = $dat["data_els_seps_after"][$datel];
						}
					}
					$rgroupby_sep["ev_col_".$cl]["pre"] = $dat["pre_sep"];
					$rgroupby_sep["ev_col_".$cl]["after"] = $dat["after_sep"];
					$rgroupby_sep["ev_col_".$cl]["mid_sep"] = $dat["mid_sep"];
					$rgroupby_sep["ev_col_".$cl]["real_sep_before"] = $dat["real_sep_before"];
					$rgroupby_sep["ev_".$dat["el"]]["pre"] = $dat["pre_sep"];
					$rgroupby_sep["ev_".$dat["el"]]["after"] = $dat["after_sep"];
					$rgroupby_sep["ev_".$dat["el"]]["mid_sep"] = $dat["mid_sep"];
					$rgroupby_sep["ev_".$dat["el"]]["real_sep_before"] = $dat["real_sep_before"];
				}
			}
		}
		if ($GLOBALS["fg_sort_dbg"] == 1)
		{
		echo "rgroupby = ".dbg::dump($r_g)."<br>";
		echo "rgroupsortdat = ".dbg::dump($rgroupsortdat)."<br>";
		echo "vgroupby = ".dbg::dump($v_g)."<br>";
		echo "vgroupdat = ".dbg::dump($vgroupdat)."<br>";
		}
		$this->t->sort_by(array(
			"rgroupby" => $r_g,
			"rgroupsortdat" => $rgroupsortdat,
			"vgroupby" => $v_g,
			"vgroupdat" => $vgroupdat
		));

		$tbl = $this->get_css();
		$tbl.= $this->get_js();

		// here. add the table header aliases to the table string
		$tbl .= $this->render_aliases($this->table["table_header_aliases"]);

		if ($this->table['text_style'])
		{
			$tbl .= "<span class=\"style_".$this->table['text_style']."\">";
		}
		$tbl .= $this->do_render_text_aliases($this->do_pageselector(nl2br($this->table["header"])));
		if ($this->table['text_style'])
		{
			$tbl .= "</span>";
		}

		if (!$no_form_tags)
		{
			$tbl.="<form action='reforb.".$this->cfg["ext"]."' method='POST' name='".$this->get_html_name_for_tbl_form()."'>\n";
		}

		if ($this->table["buttons"]["save"]["pos"]["up"])
		{
			// draw top button
			$tbl .= $this->draw_button("save");
		}

		if ($this->table["buttons"]["delete"]["pos"]["up"])
		{
			// draw top delete button
			$tbl .= $this->draw_button("delete");
		}
		$tbl.=$this->t->draw(array(
			"rgroupby" => $r_g,
			"rgroupdat" => $rgroupdat,
			"rgroupby_sep" => $rgroupby_sep,
			"titlebar_under_groups" => $this->table["has_grpnames"],
			"has_pages" => $this->table["has_pages"],
			"records_per_page" => $this->table["records_per_page"],
			"act_page" => $GLOBALS["ft_page"],
			"no_titlebar" => $this->table["no_titlebar"]
		));
		if ($this->table["buttons"]["save"]["pos"]["down"])
		{
			// draw top button
			$tbl .= $this->draw_button("save");
		}

		if ($this->table["buttons"]["delete"]["pos"]["down"])
		{
			// draw top delete button
			$tbl .= $this->draw_button("delete");
		}

		if (!$no_form_tags)
		{
			$tbl.= $this->mk_reforb("submit_table", 
				array(
					"return" => $this->binhex($this->ru),
					"table_id" => $this->table_id,
					"form_id_for_entries" => $this->form_for_entry_id
				)
			);
			$tbl.="</form>";
		}

		if ($this->table['text_style'])
		{
			$tbl .= "<span class=\"style_".$this->table['text_style']."\">";
		}
		$tbl .= $this->do_render_text_aliases($this->do_pageselector(nl2br($this->table["footer"])));
		if ($this->table['text_style'])
		{
			$tbl .= "</span>";
		}

		if ($this->table["no_show_empty"] && $this->num_lines < 1)
		{
			if ($this->table["empty_table_alias"])
			{
				if (is_array($this->table["empty_table_alias"]))
				{
					$tbl = $this->render_aliases($this->table["empty_table_alias"]);
				}
				else
				{
					$tbl = $this->render_aliases(array($this->table["empty_table_alias"]));
				}
			}
			else
			{
				$tbl = $this->table["empty_table_text"];
			}
		}

		// now, if we need to show another table as well, do the new search for it
		if ($this->table["show_second_table"] && !aw_global_get("form_table_already_drew_2nd_table"))
		{
			aw_global_set("form_table_already_drew_2nd_table",1);

			// draw the table separator thingiesh
			$tbl .= $this->render_aliases($this->table["show_second_table_tables_sep"]);

			global $entry_id, $section, $match_form, $match_entry;
			// now figure out the use table and search_form arguments from the selected aliases
			$use_table = $this->table_id;
			$search_form = $this->get_opt("current_search_form");
			if (is_array($this->table["show_second_table_aliases"]))
			{
				$tmp = obj($this->table_id);
				foreach($this->table["show_second_table_aliases"] as $aid)
				{
					$c = reset($tmp->connections_from(array(
						"to" => $aid
					)));
					if ($c->prop("to.class_id") == CL_FORM_TABLE)
					{
						$use_table = $c->prop("to");
					}
					else
					if ($c->prop("to.class_id") == CL_FORM)
					{
						$search_form = $c->prop("to");
					}
				}
			}
			$form = get_instance(CL_FORM);
			// here we must find the value for the element($this->table["show_second_table_search_val_el"]) that was on the row
			// we clicked on
			// we do that like this - we add the entry_id and form_id of the row to the search url
			// so here we can load the entry and get the element value
			$form->load($match_form);
			$form->load_entry($match_entry);
			$sve = $form->entry[$this->table["show_second_table_search_val_el"]];
//			$GLOBALS["fg_dbg"] = 1;
			$second_tbl_str = $form->new_do_search(array(
				"entry_id" => $entry_id,
				"restrict_search_el" => array($this->table["show_second_table_search_el"]), 
				"restrict_search_val" => array($sve),
				"use_table" => $use_table,
				"section" => $section,
				"no_form_tags" => false,
				"search_form" => $search_form
			));
//			$GLOBALS["fg_dbg"] = 0;
//			echo "second table (id $use_table) search restrictions el = ",$this->table["show_second_table_search_el"]," val = $sve <br />";

			if ($this->table["show_second_table_where"] != "above")
			{
				$tbl.=$second_tbl_str;
			}
			else
			{
				$tbl = $second_tbl_str.$tbl;
			}
		}

		// change the current document title if necessary
		// this must be here, after we are done showing any aliases, cause they might be documents
		// and then their titles would get changed as well. bad karma.
		if ($this->table["doc_title_is_search"])
		{
			global $restrict_search_yah;
			if (is_array($restrict_search_yah))
			{
				$str = $restrict_search_yah[count($restrict_search_yah)-1];
				if ($this->table["doc_title_is_search_upper"])
				{
					// switch to estonian locale
					$old_loc = setlocale(LC_CTYPE,0);	
					setlocale(LC_CTYPE, 'et_EE');

					$str = strtoupper($str);

					// switch back to estonian
					setlocale(LC_CTYPE, $old_loc);
				}
				aw_global_set("set_doc_title", $str);
			}
		}

		if ($this->table["doc_title_is_yah"])
		{
			global $restrict_search_yah;
			if (is_array($restrict_search_yah))
			{
				$tmp = $restrict_search_yah;
				if ($this->table["doc_title_is_yah_nolast"])
				{
					unset($tmp[count($tmp)-1]);
				}
				$str = join($this->table["doc_title_is_yah_sep"], $tmp);
				if ($this->table["doc_title_is_yah_upper"])
				{
					$old_loc = setlocale(LC_CTYPE,0);	
					setlocale(LC_CTYPE, 'et_EE');

					$str = strtoupper($str);

					// switch back to estonian
					setlocale(LC_CTYPE, $old_loc);
				}
				aw_global_set("set_doc_title", $str);
			}
		}

		// reset css style counter
		$this->used_styles = array();

		return $tbl;
	}

	////
	// !returns the xml definition for table $id to be passed to the table generator. if no id specified, 
	// presumes table is loaded already
	function get_xml($id = 0)
	{
		if ($id)
		{
			$this->load_table($id);
		}
		
		$css = $this->rewrite_table_style_classes();
		
		$xml = "<?xml version='1.0'?>
			<tabledef>
			<definitions>
				<header_normal value=\"".$css["header_normal"]."\"/>
				<header_sortable value=\"".$css["header_sortable"]."\"/>
				<header_sorted value=\"".$css["header_sorted"]."\"/>
				<content_style1 value=\"".$css["content_style1"]."\"/>
				<content_style2 value=\"".$css["content_style2"]."\"/>";
		if ($css["content_sorted_style1"])
		{
		  $xml .= "<content_style1_selected value=\"".$css["content_sorted_style1"]."\"/>";
		}
		if ($css["content_sorted_style2"])
		{
		  $xml .= "<content_style2_selected value=\"".$css["content_sorted_style2"]."\"/>";
		}
		if ($css["group_style"])
		{
			$xml.= "<group_style value=\"".$css["group_style"]."\"/>\n";
		}
		if ($css["grp_add_els_style"])
		{
			$xml.= "<group_add_els_style value=\"".$css["grp_add_els_style"]."\"/>\n";
		}
		$xml.="<tableattribs ";

		if ($this->table["table_style"])
		{
			$s = get_instance(CL_STYLE);
			$xml.=$s->get_table_string($this->table["table_style"]);
		}
		
		$xml.=" />\n</definitions>\n<data>\n";
		
		// add the vertical group by columns
		if (is_array($this->table["rgrps"]))
		{
			foreach($this->table["rgrps"] as $nr => $dat)
			{
				$cl = $this->get_col_for_el($dat["el"]);
				if ($dat["vertical"])
				{
					$xml.="<field name=\"ev_".$dat["el"]."\" caption=\"".$dat["row_title"]."\" talign=\"center\" align=\"center\" />\n";
				}
			}
		}

		$gidlist = aw_global_get("gidlist");
		for ($col = 0; $col < $this->table["cols"]; $col++)
		{
			$cc = $this->table["defs"][$col];

			// don't show column to the users that are not part of the correct groups
			if (is_array($cc["grps"]) && count($cc["grps"]) > 0)
			{
				if (count(array_intersect($gidlist,$cc["grps"])) < 1)
				{
					continue;
				}
			}

			// check column controllers
			$ctrl_ok = true;
			if (is_array($cc["controllers"]))
			{
				foreach($cc["controllers"] as $ctr_id)
				{
					$ctrl_ok &= $this->controller_instance->eval_controller($ctr_id);
				}
			}

			if (!$ctrl_ok)
			{
				// don't show this column if controller not ok
				continue;
			}

			$eln = "col_".$col;
			
			$numericattr = "";
			// we need to check if the first element in the column is numeric - if it is, then we must sort that col numerically
			if (is_array($cc["els"]))
			{
				reset($cc['els']);
				list(,$elid) = each($cc['els']);
				if ($cc['el_main_types'][$elid] == 'date' && count($cc['els']) == 1)
				{
					$fmt = "d.m.y / H:i";
					if ($cc["dateformat"] != "")
					{
						$fmt = $cc["dateformat"];
					}
					$numericattr = ' type="time" format="'.$fmt.'" numeric="yes"';
				}
				else
				if ($cc['el_types'][$elid] == 'int')
				{
					$numericattr = " numeric=\"1\" thousands_sep=\"".$cc['thousands_sep']."\"";
				}
			}
			
			$title = $cc["lang_title"][aw_global_get("lang_id")];
			if (is_array($cc["els"]) && in_array("select", $cc["els"]))
			{
				$title = "&lt;a href='javascript:void(0)' onClick='tb_selall(&quot;".$this->get_html_name_for_tbl_form()."&quot;)'&gt;".$title."&lt;/a&gt;";
			}

			$_sc_d = "";
			$awa = new aw_array($cc["el_set_colname"]);
			foreach($awa->get() as $_sc_elid => $_sc_one)
			{
				if ($_sc_one == 1)
				{
					$_sc_d .= $this->last_row_data["ev_".$_sc_elid];
				}
			}
			if ($_sc_d != "")
			{
				$title = $_sc_d;
			}
			$title = str_replace("&", "&amp;", htmlentities($title, ENT_COMPAT, aw_global_get("charset")));
			if ((!$cc["no_show_empty"] || $this->table_not_empty_cols[$col]) && !$cc["not_active"])
			{
				$xml.="\n<field name=\"ev_".$eln."\" caption=\"".$title."\" talign=\"center\" align=\"center\" ";
				if ($cc["sortable"])
				{
					$xml.=" sortable=\"1\" ";
				}
				if (is_array($cc["styles"]))
				{
					$st = get_instance(CL_STYLE);
					foreach($cc["styles"] as $k => $v)
					{
						if ($v)
						{
							if (($_csscl = $st->get_css_class($v)))
							{
								$_stylename = $_csscl;
							}
							else
							{
								$_stylename = "style_".$v;
							}
							
							$xml.=" $k=\"$_stylename\"";
						}
					}
				}
				$xml.=" $numericattr />\n";
			}
		}
		if ($GLOBALS["dbg_num"]) {echo("<textarea cols=80 rows=40>$xml</textarea>");};
		return $xml.="\n</data></tabledef>";
	}

	////
	// !this makes sure that only one of each style gets put on the page
	function chk_get_css($st, $lst)
	{
		if (!$this->used_styles[$st])
		{
			$this->used_styles[$st] = true;
			return $this->s->get_css($st,$lst);
		}
	}

	function get_css($id = 0)
	{
		if ($GLOBALS["format"] == "pdf")
		{
			return "";
		}
		if ($id)
		{
			$this->load_table($id);
		}
		$this->s = get_instance(CL_STYLE);
		$op = "<style type=\"text/css\">\n";

		if ($this->table["header_normal"])
		{
			$op.= $this->chk_get_css($this->table["header_normal"],$this->table["header_link"]);
		}
		if ($this->table["group_style"])
		{
			$op.= $this->chk_get_css($this->table["group_style"],$this->table["link_style"]);
		}
		if ($this->table["group_add_els_style"])
		{
			$op.= $this->chk_get_css($this->table["group_add_els_style"],$this->table["link_style"]);
		}
		if ($this->table["header_sortable"])
		{
			$op.= $this->chk_get_css($this->table["header_sortable"],$this->table["header_sortable_link"]);
		}
		if ($this->table["header_sorted"])
		{
			$op.= $this->chk_get_css($this->table["header_sorted"],$this->table["header_sortable_link"]);
		}
		if ($this->table["content_style1"])
		{
			$op.= $this->chk_get_css($this->table["content_style1"],$this->table["link_style1"]);
		}
		if ($this->table["content_style2"])
		{
			$op.= $this->chk_get_css($this->table["content_style2"],$this->table["link_style2"]);
		}
		if ($this->table["content_sorted_style1"])
		{
			$op.= $this->chk_get_css($this->table["content_sorted_style1"],$this->table["link_style1"]);
		}
		if ($this->table["content_sorted_style2"])
		{
			$op.= $this->chk_get_css($this->table["content_sorted_style2"],$this->table["link_style2"]);
		}
		if ($this->table["pg_lb_style"])
		{
			$op.= $this->chk_get_css($this->table["pg_lb_style"],0);
		}
		if ($this->table["pg_text_style"])
		{
			$op.= $this->chk_get_css($this->table["pg_text_style"],$this->table["pg_text_style_link"]);
		}
		if ($this->table["text_style"])
		{
			$op.= $this->chk_get_css($this->table["text_style"],$this->table["text_style_link"]);
		}

		for ($col = 0; $col < $this->table["cols"]; $col++)
		{
			$cc = $this->table["defs"][$col];
			if (is_array($cc["styles"]))
			{
				$st = $cc["styles"];
				if ($st["header_normal"])
				{
					$op.= $this->chk_get_css($st["header_normal"],$st["header_link"]);
				}
				if ($st["header_sortable"])
				{
					$op.= $this->chk_get_css($st["header_sortable"],$st["header_sortable_link"]);
				}
				if ($st["header_sorted"])
				{
					$op.= $this->chk_get_css($st["header_sorted"],$st["header_sortable_link"]);
				}
				if ($st["content_style1"])
				{
					$op.= $this->chk_get_css($st["content_style1"],$st["link_style1"]);
				}
				if ($st["content_style2"])
				{
					$op.= $this->chk_get_css($st["content_style2"],$st["link_style2"]);
				}
				if ($st["content_sorted_style1"])
				{
					$op.= $this->chk_get_css($st["content_sorted_style1"],$st["link_style1"]);
				}
				if ($st["content_sorted_style2"])
				{
					$op.= $this->chk_get_css($st["content_sorted_style2"],$st["link_style2"]);
				}
				if ($st["group_style"])
				{
					$op.= $this->chk_get_css($st["group_style"],$st["link_style"]);
				}
			}
		}

		$op.="</style>\n";
		return $op;
	}

	function get_order_url($col,$dat, $form_id)
	{
		if (strpos($this->table["defs"][$col]["order_form"],"?") === false)
		{
			$sep = "?";
		}
		else
		{
			$sep = "&";
		}
		if ($dat["chain_entry_id"])
		{
			$link = $this->table["defs"][$col]["order_form"].$sep."load_chain_data=".$dat["chain_entry_id"];
		}
		else
		{
			$link = $this->table["defs"][$col]["order_form"].$sep."load_entry_data=".$dat["entry_id"]."&load_entry_data_form=".$form_id;
		}
		return "<a href='".$link."'>".$this->table["texts"]["order"][$this->lang_id]."</a>";
	}

	function get_aliases_for_table()
	{
		if (!($ret = aw_cache_get("form_table::aliases",$this->table_id)))
		{
			$am = get_instance("alias_parser");
			$ret = array();
			$as = $am->get_oo_aliases(array("oid" => $this->table_id));
			$tmp = aw_ini_get("classes");
			foreach($as as $clid => $dat)
			{
				foreach($dat as $cp)
				{
					list($astr) = explode(",",$tmp[$cp["to.class_id"]]["alias"]);
					$ret[$cp["to"]] = "#".$astr.($cp["idx"])."#";
				}
			}
			aw_cache_set("form_table::aliases", $this->table_id, $ret);
		}

		return $ret;
	}

	function get_data_for_alias($aid)
	{
		$to = obj($this->table_id);
		$c = reset($to->connections_from(array(
			"to" => $aid
		)));

		$ret = array(
			"id" => $c->prop("to"),
			"source" => $c->prop("from"),
			"target" => $c->prop("to"),
			"class_id" => $c->prop("to.class_id"),
		);
		return $ret;
	}

	function get_js()
	{
		return "<script language='javascript'>
			var chk_status = ".($this->table["select_default"] == 1 ? "false" : "true").";

				function tb_selall(frmna)
				{
					els = eval('document.'+frmna);
					len = els.elements.length;
					for (i=0; i < len; i++)
					{
						if (els.elements[i].name.indexOf('sel') != -1)
						{
							els.elements[i].checked=chk_status;
						}
					}
					chk_status = !chk_status;
					return false;
				}

				function fg_increment(formname, elname, value)
				{
					form = eval('document.'+formname+'.elements');
					for (i=0; i < form.length; i++)
					{
						if (form[i].name == elname)
						{
							vl = parseInt(form[i].value);
							if (!vl)
							{
								vl = 0;
							}
							form[i].value = Math.max(0,vl + value);
						}
					}
				}

				function ft_popup(url, name, scrollbars, fixed, toolbar, locationbar, width, height)
				{
					var wprops = \"toolbar=\"+toolbar+\",location=\"+locationbar+\",directories=0,status=0,\"+
					\"menubar=0,scrollbars=\"+scrollbars+\",resizable=\"+fixed+\",width=\" + width + \",height=\" + height;
					openwindow = window.open(url,name,wprops);
					openwindow.focus();
				}

				function ft_confirm(caption,url)
				{
					var answer=confirm(caption);
					if (answer)
					{
						window.location=url;
					}
				}
		</script>";
	}

	/**  
		
		@attrib name=new params=name default="0"
		
		@param parent required acl="add"
		
		@returns
		
		
		@comment

	**/
	function new_add($arr)
	{
		extract($arr);
		$this->read_template("add_table_settings.tpl");
		$this->mk_path($parent, "Lisa formi tabel");

		$lang = get_instance("languages");

		$this->vars(array(
			"languages" => $this->mpicker(array(), $lang->get_list()),
			"forms" => $this->mpicker(array(), $this->get_flist(array("type" => FTYPE_ENTRY, "addfolders" => true, "sort" => true))),
			"folders" => $this->mpicker(array(), $this->get_menu_list()),
			"reforb" => $this->mk_reforb("new_submit_settings", array("parent" => $parent))
		));

		return $this->parse();
	}

	/**  
		
		@attrib name=new_submit_settings params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function new_submit_settings($arr)
	{
		extract($arr);

		if ($id)
		{
			$o = obj($id);
			$o->set_name($name);
			$o->save();
		}
		else
		{
			$o = obj();
			$o->set_parent($parent);
			$o->set_class_id(CL_FORM_TABLE);
			$o->set_name($name);
			$o->set_comment($comment);
			$id = $o->save();

			$this->db_query("INSERT INTO form_tables(id, content, num_cols, cols) values('$id','',0,0)");
		}

		$this->load_table($id);

		$t_forms = $this->get_forms_for_table($id);
		
		if (is_array($t_forms))
		foreach($t_forms as $formid)
		{
			$formels=$this->get_form_elements(array("id"=> $formid,"key"=> "id"));
			if (is_array($formels))
			{
				foreach($formels as $k_elid => $v_eldata)
				{
					$els[$k_elid]=$formid;
					$elsubtypes[$formid][$k_elid]["type"]=$v_eldata["type"];
					$elsubtypes[$formid][$k_elid]["subtype"]=$v_eldata["subtype"];
					$elsubtypes[$formid][$k_elid]["thousands_sep"]=$v_eldata["thousands_sep"];
				};
			}
		};

		$this->table["has_aliasmgr"] = $settings["has_aliasmgr"];
		$this->table["has_yah"] = $settings["has_yah"];
		$this->table["has_ftbl_extlinks"] = $settings["has_ftbl_extlinks"];
		$this->table["select_default"] = $settings["select_default"];
		$this->table["has_textels"] = $settings["has_textels"];
		$this->table["has_groupacl"] = $settings["has_groupacl"];
		$this->table["has_grpnames"] = $settings["has_grpnames"];
		$this->table["print_button"] = $settings["print_button"];
		$this->table["has_pages"] = $settings["has_pages"];
		$this->table["has_pages_type"] = $settings["has_pages_type"];
		$this->table["records_per_page"] = $settings["records_per_page"];
		$this->table["skip_one_liners"] = $settings["skip_one_liners"];
		$this->table["skip_one_liners_col"] = $settings["skip_one_liners_col"];
		$this->table["user_entries"] = $settings["user_entries"];
		$this->table["doc_title_is_search"] = $settings["doc_title_is_search"];
		$this->table["doc_title_is_search_upper"] = $settings["doc_title_is_search_upper"];
		$this->table["doc_title_is_yah"] = $settings["doc_title_is_yah"];
		$this->table["doc_title_is_yah_sep"] = $settings["doc_title_is_yah_sep"];
		$this->table["doc_title_is_yah_nolast"] = $settings["doc_title_is_yah_nolast"];
		$this->table["doc_title_is_yah_upper"] = $settings["doc_title_is_yah_upper"];
		$this->table["show_second_table"] = $settings["show_second_table"];
		$this->table["show_second_table_where"] = $settings["show_second_table_where"];
		$this->table["show_second_table_search_el"] = $settings["show_second_table_search_el"];
		$this->table["show_second_table_search_val_el"] = $settings["show_second_table_search_val_el"];
		$this->table["show_second_table_tables_sep"] = $this->make_keys($settings["show_second_table_tables_sep"]);
		$this->table["table_header_aliases"] = $this->make_keys($settings["table_header_aliases"]);
		$this->table["no_show_empty"] = $settings["no_show_empty"];
		$this->table["empty_table_text"] = $settings["empty_table_text"];
		$this->table["empty_table_alias"] = $this->make_keys($settings["empty_table_alias"]);
		$this->table["no_grpels_in_restrict"] = $settings["no_grpels_in_restrict"];
		$this->table["has_grpsettings"] = $settings["has_grpsettings"];
		$this->table["forms"] = $this->make_keys($settings["forms"]);
		$this->table["languages"] = $this->make_keys($settings["languages"]);
		$this->table["moveto"] = $this->make_keys($settings["folders"]);
		$this->table["view_cols"] = $this->make_keys($settings["view_cols"]);
		$this->table["change_cols"] = $this->make_keys($settings["change_cols"]);
		$this->table["user_entries_except_grps"] = $this->make_keys($user_entries_except_grps);
		$this->table["show_second_table_aliases"] = $this->make_keys($settings["show_second_table_aliases"]);
		$this->dequote(&$settings["header"]);
		$this->dequote(&$settings["footer"]);
		$this->table["header"] = $settings["header"];
		$this->table["footer"] = $settings["footer"];
		$this->table["no_titlebar"] = $settings["no_titlebar"];

		if (!is_array($defsort))
		{
			$defsort = array();
		}
		$this->table["defsort"] = array();
		foreach($defsort as $nr => $dat)
		{
			if ($dat["el"])
			{
				$this->table["defsort"][] = $dat;
				$this->table["defsort_forms"][$dat["el"]] = $els[$dat["el"]];
				$this->table["defsort_el_types"][$dat["el"]] = $elsubtypes[$els[$dat["el"]]][$dat["el"]]["subtype"];
			}
		}
		usort($this->table["defsort"], create_function('$a,$b','if ($a["ord"] > $b["ord"]) return 1; if ($a["ord"] < $b["ord"]) return -1; return 0;'));

		if (!is_array($grps))
		{
			$grps = array();
		}
		$this->table["grps"] = array();
		foreach($grps as $nr => $dat)
		{
			if ($dat["gp_el"])
			{
				$this->table["grps"][] = $dat;
				$this->table["grps_forms"][$dat["gp_el"]] = $els[$dat["gp_el"]];
			}
		}

		if (!is_array($rgrps))
		{
			$rgrps = array();
		}
		$this->table["rgrps"] = array();
		foreach($rgrps as $nr => $dat)
		{
			if ($dat["el"])
			{
				$dat["data_els"] = $this->make_keys($dat["data_els"]);

				$this->table["rgrps"][] = $dat;
				$this->table["rgrps_forms"][$dat["el"]] = $els[$dat["el"]];
				$this->table["rgrps_el_types"][$dat["el"]] = $elsubtypes[$els[$dat["el"]]][$dat["el"]]["subtype"];
				if ($dat["sort_el"])
				{
					$this->table["rgrps_forms"][$dat["sort_el"]] = $els[$dat["sort_el"]];
					$this->table["rgrps_el_types"][$dat["sort_el"]] = $elsubtypes[$els[$dat["sort_el"]]][$dat["sort_el"]]["subtype"];
				}

				if ($dat["search_val_el"])
				{
					$this->table["rgrps_forms"][$dat["search_val_el"]] = $els[$dat["search_val_el"]];
					$this->table["rgrps_el_types"][$dat["search_val_el"]] = $elsubtypes[$els[$dat["search_val_el"]]][$dat["search_val_el"]]["subtype"];
				}

				if (is_array($dat["data_els"]))
				{
					foreach($dat["data_els"] as $_del)
					{
						$this->table["rgrps_forms"][$_del] = $els[$_del];
					}
				}
			}
		}
		usort($this->table["rgrps"], create_function('$a,$b','if ($a["ord"] > $b["ord"]) return 1; if ($a["ord"] < $b["ord"]) return -1; return 0;'));

		$im = get_instance(CL_IMAGE);
		if (is_array($this->table["buttons"]))
		{
			foreach($this->table["buttons"] as $bt_id => $bt_dat)
			{
				$imn = "buttons_".$bt_id."_image";
				global $$imn;
				if (($$imn == "none" || $$imn == "") && !$buttons[$bt_id]["delimage"])
				{
					$buttons[$bt_id]["image"] = $this->table["buttons"][$bt_id]["image"];
				}
				else
				if ($buttons[$bt_id]["delimage"])
				{
					$buttons[$bt_id]["image"] = array();
				}
				else
				{
					$buttons[$bt_id]["image"] = $im->add_upload_image($imn, $this->table_parent, $this->table["buttons"][$bt_id]["image"]["id"]);
				}
			}
		}

		$this->table["buttons"] = $buttons;

		$this->save_table_settings();
		return $this->mk_my_orb("new_change_settings", array("id" => $id));
	}

	/**  
		
		@attrib name=new_change_settings params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function new_change_settings($arr)
	{
		extract($arr);
		$this->load_table($id);
		$this->read_template("add_table_settings.tpl");
		$this->mk_path($this->table_parent, "Muuda formi tabelit");

		$this->do_menu();

		$lang = get_instance("languages");
		$us = get_instance("users");

		$els = $this->get_tbl_elements();

		$als = $this->get_aliases_for_table();
		$this->vars(array(
			"name" => $this->table_name,
			"comment" => $this->table_comment,
			"languages" => $this->mpicker($this->table["languages"], $lang->get_list()),
			"forms" => $this->mpicker($this->table["forms"], $this->get_flist(array("type" => FTYPE_ENTRY, "addfolders" => true, "sort" => true))),
			"folders" => $this->mpicker($this->table["moveto"], $this->get_menu_list()),
			"has_print_button" => checked($this->table["print_button"]),
			"has_grpnames" => checked($this->table["has_grpnames"]),
			"has_groupacl" => checked($this->table["has_groupacl"]),
			"has_textels" => checked($this->table["has_textels"]),
			"has_aliasmgr" => checked($this->table["has_aliasmgr"]),
			"select_default" => checked($this->table["select_default"]),
			"has_yah" => checked($this->table["has_yah"]),
			"has_ftbl_extlinks" => checked($this->table["has_ftbl_extlinks"]),
			"has_pages" => checked($this->table["has_pages"]),
			"has_pages_text" => checked($this->table["has_pages_type"] == "text"),
			"has_pages_lb" => checked($this->table["has_pages_type"] == "lb"),
			"has_user_entries" => checked($this->table["user_entries"]),
			"records_per_page" => $this->table["records_per_page"],
			"uee_grps" => $this->mpicker($this->table["user_entries_except_grps"], get_instance(CL_GROUP)->get_group_picker(array("type" => array(group_obj::TYPE_REGULAR,group_obj::TYPE_DYNAMIC)))),
			"skip_one_liners" => checked($this->table["skip_one_liners"]),
			"skip_one_liners_col" => $this->picker($this->table["skip_one_liners_col"],$this->get_col_picker()),
			"show_second_table" => checked($this->table["show_second_table"]),
			"second_table_below" => checked($this->table["show_second_table_where"] != "above"),
			"second_table_above" => checked($this->table["show_second_table_where"] == "above"),
			"no_grpels_in_restrict" => checked($this->table["no_grpels_in_restrict"]),
			"no_show_empty" => checked($this->table["no_show_empty"]),
			"empty_table_text" => $this->table["empty_table_text"],
			"empty_table_alias" => $this->mpicker($this->table["empty_table_alias"], $als),
			"view_cols" => $this->mpicker($this->table["view_cols"], $els),
			"show_second_table_aliases" => $this->mpicker($this->table["show_second_table_aliases"], $als),
			"second_table_search_el" => $this->picker($this->table["show_second_table_search_el"], $els),
			"second_table_search_val_el" => $this->picker($this->table["show_second_table_search_val_el"], $els),
			"show_second_table_tables_sep" => $this->mpicker($this->table["show_second_table_tables_sep"], $als),
			"table_header_aliases" => $this->mpicker($this->table["table_header_aliases"], $als),
			"change_cols" => $this->mpicker($this->table["change_cols"], $els),
			"doc_title_is_search" => checked($this->table["doc_title_is_search"]),
			"doc_title_is_search_upper" => checked($this->table["doc_title_is_search_upper"]),
			"doc_title_is_yah" => checked($this->table["doc_title_is_yah"]),
			"doc_title_is_yah_upper" => checked($this->table["doc_title_is_yah_upper"]),
			"doc_title_is_yah_nolast" => checked($this->table["doc_title_is_yah_nolast"]),
			"doc_title_is_yah_sep" => $this->table["doc_title_is_yah_sep"],
			"has_grpsettings" => checked($this->table["has_grpsettings"]),
			"no_titlebar" => checked($this->table["no_titlebar"]),
			"header" => htmlentities($this->table["header"]),
			"footer" => htmlentities($this->table["footer"]),
			"reforb" => $this->mk_reforb("new_submit_settings", array("id" => $id))
		));

		$mnr = 0;
		if (is_array($this->table["defsort"]))
		{
			foreach($this->table["defsort"] as $nr => $dat)
			{
				$this->vars(array(
					"ds_nr" => $nr,
					"ds_ord" => $dat["ord"],
					"ds_els" => $this->picker($dat["el"], $els),
					"ds_asc" => checked($dat["type"] == "asc"),
					"ds_desc" => checked($dat["type"] == "desc")
				));
				$l.= $this->parse("DEFSORT_LINE");
				$mnr = $nr;
				$mord = max($dat["ord"], $mord);
			}
		}
		$this->vars(array(
			"ds_nr" => $mnr+1,
			"ds_ord" => $mord+1,
			"ds_els" => $this->picker('', $els),
			"ds_asc" => checked(true),
			"ds_desc" => checked(false)
		));
		$l.= $this->parse("DEFSORT_LINE");
		$this->vars(array("DEFSORT_LINE" => $l));

		$l = "";
		if (is_array($this->table["grps"]))
		{
			foreach($this->table["grps"] as $nr => $dat)
			{
				$this->vars(array(
					"grp_nr" => $nr,
					"gp_sep" => $dat["sep"],
					"gp_els" => $this->picker($dat["gp_el"], $els),
					"collect_els" => $this->picker($dat["collect_el"], $els),
					"ord_els" => $this->picker($dat["ord_el"], $els),
				));
				$l.= $this->parse("GRPLINE");
				$mnr = $nr;
			}
		}
		$this->vars(array(
			"grp_nr" => $mnr+1,
			"gp_sep" => ",",
			"gp_els" => $this->picker('', $els),
			"collect_els" => $this->picker('', $els),
			"ord_els" => $this->picker('', $els),
		));
		$l.= $this->parse("GRPLINE");
		$this->vars(array("GRPLINE" => $l));


		$l = "";
		$mnr = 0;
		$mord = 0;
		if (is_array($this->table["rgrps"]))
		{
			foreach($this->table["rgrps"] as $nr => $dat)
			{
				$this->vars(array(
					"grp_nr" => $nr,
					"gp_ord" => $dat["ord"],
					"gp_real_sep_before" => htmlspecialchars($dat["real_sep_before"]),
					"gp_row_title" => $dat["row_title"],
					"els" => $this->picker($dat["el"], $els),
					"sort_els" => $this->picker($dat["sort_el"], $els),
					"sort_order" => $this->picker($dat["sort_order"], array("asc" => "Asc", "desc" => "Desc")),
					"gp_vertical" => checked($dat["vertical"] == 1),
					"data_els" => $this->mpicker($dat["data_els"], $els),
					"search_val_els" => $this->picker($dat["search_val_el"], $els),
					"search_to_els" => $this->picker($dat["search_to_el"], $els),
					"data_els" => $this->mpicker($dat["data_els"], $els),
					"pre_sep" => $dat["pre_sep"],
					"after_sep" => $dat["after_sep"],
					"mid_sep" => $dat["mid_sep"]
				));
				$del = "";
				if (is_array($dat["data_els"]))
				{
					foreach($dat["data_els"] as $_del)
					{
						$this->vars(array(
							"del" => $_del,
							"del_name" => $els[$_del],
							"del_sep" => $dat["data_els_seps"][$_del],
							"del_sep_after" => $dat["data_els_seps_after"][$_del]
						));
						$del.=$this->parse("DATEL");
					}
				}
				$this->vars(array(
					"DATEL" => $del
				));
				$l.= $this->parse("GRP2LINE");
				$mnr = $nr;
				$mord = max($dat["ord"], $mord);
			}
		}
		$this->vars(array(
			"grp_nr" => $mnr+1,
			"gp_ord" => $mord+1,
			"gp_row_title" => "",
			"els" => $this->picker('', $els),
			"sort_els" => $this->picker('', $els),
			"gp_vertical" => checked(false),
			"data_els" => $this->mpicker(array(), $els),
			"pre_sep" => "",
			"gp_real_sep_before" => "",
			"after_sep" => "",
			"DATEL" => ""
		));
		$l.= $this->parse("GRP2LINE");
		$this->vars(array("GRP2LINE" => $l));

		$l = "";
		foreach($this->buttons as $btn_id => $btn_name)
		{
			$img = "";
			if ($this->table["buttons"][$btn_id]["image"]["url"] != "")
			{
				$img = image::make_img_tag(image::check_url($this->table["buttons"][$btn_id]["image"]["url"]));
			}
			$this->vars(array(
				"button_check" => checked($this->table["buttons"][$btn_id]["check"] == 1),
				"button_text" => $this->table["buttons"][$btn_id]["text"],
				"button_ord" => $this->table["buttons"][$btn_id]["ord"],
				"button_up" => checked($this->table["buttons"][$btn_id]["pos"]["up"] == 1),
				"button_down" => checked($this->table["buttons"][$btn_id]["pos"]["down"] == 1),
				"bt_name" => $btn_name,
				"bt_id" => $btn_id,
				"image" => $img
			));
			$l.=$this->parse("BUTTON");
		}
		$this->vars(array(
			"BUTTON" => $l,
			"CHANGE" => $this->parse("CHANGE")
		));
		return $this->parse();
	}

	function save_table_settings()
	{
		// make things just a bit more sane
		for($i=0; $i < $this->table["cols"]; $i++)
		{
			if (!is_array($this->table["defs"][$i]["els"]))
			{
				$this->table["defs"][$i]["els"] = array();
			}
		}
		if (!is_array($this->table["forms"]))
		{
			$this->table["forms"] = array();
		}

		$this->db_query("DELETE FROM form_table2form WHERE table_id = ".$this->table_id);
		if (is_array($this->table["forms"]))
		{
			foreach($this->table["forms"] as $fid)
			{
				$this->db_query("INSERT INTO form_table2form(form_id,table_id) VALUES($fid,".$this->table_id.")");
			}
		}
		$co = aw_serialize($this->table,SERIALIZE_PHP);
		$this->quote(&$co);
		$q = "UPDATE form_tables SET num_cols = '".$this->table["cols"]."' , content = '$co' WHERE id = ".$this->table_id;
		$this->db_query($q);
	}

	function do_menu()
	{
		$tpl = get_instance("aw_template");
		$tpl->tpl_init("forms");
		$tpl->read_template("fg_table_menu.tpl");

		$items["change"] = array("name" => "Tulbad", "url" => $this->mk_my_orb("change", array("id" => $this->table_id), "",false,true));

		$items["new_change_settings"] = array("name" => "M&auml;&auml;rangud", "url" => $this->mk_my_orb("new_change_settings", array("id" => $this->table_id), "",false,true));

		$items["new_change_styles"] = array("name" => "Stiilid", "url" => $this->mk_my_orb("new_change_styles", array("id" => $this->table_id), "",false,true));

		$items["new_change_styles"] = array("name" => "Stiilid", "url" => $this->mk_my_orb("new_change_styles", array("id" => $this->table_id), "",false,true));

		if ($this->table["has_aliasmgr"])
		{
			$items["new_change_aliasmgr"] = array("name" => "Aliastehaldur", "url" => $this->mk_my_orb("new_change_aliasmgr", array("id" => $this->table_id), "",false,true));
		}

		$items["new_change_translate"] = array("name" => "T&otilde;lgi", "url" => $this->mk_my_orb("new_change_translate", array("id" => $this->table_id), "",false,true));

		if ($this->table["has_grpsettings"])
		{
			$items["has_grpsettings"] = array("name" => "Gruppide m&auml;&auml;rangud", "url" => $this->mk_my_orb("change_grpsettings", array("id" => $this->table_id), "",false,true));
		}

		$this->vars(array(
			"menu" => $this->do_tpl_menu($items, $tpl)
		));
	}

	/**  
		
		@attrib name=change params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function new_change_cols($arr)
	{
		extract($arr);
		$this->read_template("add_table_cols.tpl");
		$this->load_table($id);
		$this->mk_path($this->table_parent, "Muuda formi tabelit");
		$this->do_menu();

		$els = $this->get_tbl_elements();
		$els_nof = $this->get_tbl_elements(true);

		$this->vars(array(
			"num_cols" => $this->table["cols"],
			"reforb" => $this->mk_reforb("new_submit_cols", array("id" => $id))
		));

		$style_inst = get_instance(CL_STYLE);
		for ($col = 0; $col < $this->table["cols"]; $col++)
		{
			$this->vars(array(
				"lang_title" => $this->table["defs"][$col]["lang_title"][aw_global_get("lang_id")],
				"ord" => $this->table["defs"][$col]["ord"],
				"col_id" => $col,
				"lang_id" => aw_global_get("lang_id")
			));
			$coldata[$col][1] = $this->parse("COL_HEADER");

			
			$this->vars(array(
				"els" => $this->mpicker($this->table["defs"][$col]["els"], $els),
			));
			$coldata[$col][2] = $this->parse("SEL_ELS");

			if ($this->table["defs"][$col]["els"]["order"] == "order")
			{
				$this->vars(array(
					"order_form" => $this->table["defs"][$col]["order_form"]
				));
				$coldata[$col][3] = $this->parse("SEL_ORDER_FORM");
			}

			if ($this->table["defs"][$col]["els"]["text"] == "text")
			{
				$this->vars(array(
					"is_type_text" => $this->table["defs"][$col]["is_type_text"]
				));
				$coldata[$col][4] = $this->parse("IS_TYPE_TEXT");
			}

			if ($this->table["has_aliasmgr"])
			{
				$fta = $this->get_aliases_for_table();
				$this->vars(array(
					"aliases" => $this->mpicker($this->table["defs"][$col]["alias"], $fta)
				));
				$coldata[$col][5] = $this->parse("SEL_ALIAS");
			}

			if ($this->table["has_groupacl"])
			{
				$us = get_instance("users");
				$this->vars(array(
					"grps" => $this->mpicker($this->table["defs"][$col]["grps"], get_instance(CL_GROUP)->get_group_picker(array("type" => array(group_obj::TYPE_REGULAR,group_obj::TYPE_DYNAMIC))))
				));
				$coldata[$col][6] = $this->parse("SEL_GRPS");
			}

			if ($this->table["defs"][$col]["link"] == 1)
			{
				$this->vars(array(
					"link" => $this->picker($this->table["defs"][$col]["link_el"], $els)
				));
				$coldata[$col][7] = $this->parse("SEL_LINK");
			}

			$l = "";
			$elns = array();
			if (is_array($this->table["defs"][$col]["els"]))
			{
				foreach($this->table["defs"][$col]["els"] as $el)
				{
					$this->vars(array(
						"el_name" => $els[$el],
						"el_ord" => str_replace("\"", "&quot;", $this->table["defs"][$col]["el_ord"][$el]),
						"el_id" => $el,
						"el_sep" => str_replace("\"", "&quot;", $this->table["defs"][$col]["el_sep"][$el]),
						"el_sep_pre" => str_replace("\"", "&quot;", $this->table["defs"][$col]["el_sep_pre"][$el]),
						"el_show" => checked($this->table["defs"][$col]["el_show"][$el]),
						"el_edit" => checked($this->table["defs"][$col]["el_edit"][$el]),
						"el_set_colname" => checked($this->table["defs"][$col]["el_set_colname"][$el]),
						"el_no_row_if_empty" => checked($this->table["defs"][$col]["el_no_row_if_empty"][$el]),
						"el_search" => checked($this->table["defs"][$col]["el_search"][$el])
					));
					$ise = "";
					if ($this->table["defs"][$col]["is_edit"])
					{
						$ise = $this->parse("IS_EDITABLE2");
					}
					$this->vars(array(
						"IS_EDITABLE2" => $ise
					));
					$l.= $this->parse("SEL_EL");
					$elns[$el] = $els[$el];
				}
			}

			$ise = "";
			if ($this->table["defs"][$col]["is_edit"])
			{
				$ise = $this->parse("IS_EDITABLE");
			}
			$this->vars(array(
				"IS_EDITABLE" => $ise,
				"SEL_EL" => $l,
				"search_map" => $this->picker($this->table["defs"][$col]["search_map"], $elns),
				"search_el" => $this->picker($this->table["defs"][$col]["search_el"], $elns),
				"split_col_search" => checked($this->table["defs"][$col]["split_col_search"]),
				"split_col_search_splitter" => $this->table["defs"][$col]["split_col_search_splitter"],
				"link_section" => $this->table["defs"][$col]["link_section"]
			));
			$has_ftable_aliases = false;
			if (is_array($this->table["defs"][$col]["alias"]))
			{
				foreach($this->table["defs"][$col]["alias"] as $aid)	
				{
					$alias_data = $this->table["defs"][$col]["alias_data"][$aid];
					//$c = new connection($aid);
					//$__o = $c->to();
					if ($alias_data["class_id"] == CL_FORM_TABLE)
					{
						$has_ftable_aliases = true;
					}
				}
			}

			$this->vars(array(
				"HAS_FTABLE_ALIASES" => ($has_ftable_aliases ? $this->parse("HAS_FTABLE_ALIASES") : ""),
				"col_not_active" => checked($this->table["defs"][$col]["not_active"]),
				"col_el_sep" => str_replace("\"", "&quot;", $this->table["defs"][$col]["col_el_sep"])
			));
			$coldata[$col][10] = $this->parse("SEL_SETTINGS");

			$sts = $style_inst->get_select(0,ST_CELL, true);

			$ol = new object_list(array(
				"class_id" => CL_FORM_CONTROLLER,
				"site_id" => array(),
				"lang_id" => array()
			));
			$bk_names = array("" => "") + $ol->names();


			$this->vars(array(
				"col_sortable" => checked($this->table["defs"][$col]["sortable"]),
				"col_email" => checked($this->table["defs"][$col]["is_email"]),
				"is_edit" => checked($this->table["defs"][$col]["is_edit"]),
				"col_clicksearch" => checked($this->table["defs"][$col]["clicksearch"]),
				"col_link" => checked($this->table["defs"][$col]["link"]),
				"col_link_popup" => checked($this->table["defs"][$col]["link_popup"]),
				"col_link_new_window" => checked($this->table["defs"][$col]["link_new_window"]),
				"no_show_empty" => checked($this->table["defs"][$col]["no_show_empty"]),
				"has_col_style" => checked($this->table["defs"][$col]["has_col_style"]),
				"header_normal_styles" => $this->picker($this->table["defs"][$col]["styles"]["header_normal"], $sts),
				"header_link_styles" => $this->picker($this->table["defs"][$col]["styles"]["header_link"], $sts),
				"header_sortable_styles" => $this->picker($this->table["defs"][$col]["styles"]["header_sortable"], $sts),
				"header_sorted_styles" => $this->picker($this->table["defs"][$col]["styles"]["header_sorted"], $sts),
				"header_sortable_link_styles" => $this->picker($this->table["defs"][$col]["styles"]["header_sortable_link"], $sts),
				"content_style1_styles" => $this->picker($this->table["defs"][$col]["styles"]["content_style1"], $sts),
				"content_style2_styles" => $this->picker($this->table["defs"][$col]["styles"]["content_style2"], $sts),
				"content_sorted_style1_styles" => $this->picker($this->table["defs"][$col]["styles"]["content_sorted_style1"], $sts),
				"content_sorted_style2_styles" => $this->picker($this->table["defs"][$col]["styles"]["content_sorted_style2"], $sts),
				"link_style1_styles" => $this->picker($this->table["defs"][$col]["styles"]["link_style1"], $sts),
				"link_style2_styles" => $this->picker($this->table["defs"][$col]["styles"]["link_style2"], $sts),
				"group_style_styles" => $this->picker($this->table["defs"][$col]["styles"]["group_style"], $sts),
				"controllers" => $this->mpicker($this->table["defs"][$col]["controllers"], $bk_names),
				"val_controller" => $this->picker($this->table["defs"][$col]["val_controller"], $bk_names)
			));
			$hst = "";
			if ($this->table["defs"][$col]["has_col_style"])
			{
				$hst = $this->parse("HAS_STYLE");
			}
			$this->vars(array(
				"HAS_STYLE" => $hst
			));
			$coldata[$col][11] = $this->parse("SEL_SETINGS2");

			$this->vars(array(
				"popup_width" => $this->table["defs"][$col]["link_popup_width"],
				"popup_height" => $this->table["defs"][$col]["link_popup_height"],
				"scrollbars" => checked($this->table["defs"][$col]["link_popup_scrollbars"]),
				"fixed" => checked($this->table["defs"][$col]["link_popup_fixed"]),
				"toolbar" => checked($this->table["defs"][$col]["link_popup_toolbar"]),
				"addressbar" => checked($this->table["defs"][$col]["link_popup_addressbar"]),
			));
			$coldata[$col][12] = $this->parse("SEL_POPUP");

			$this->vars(array(
				"img_type_img" => checked($this->table["defs"][$col]["image_type"] == "img"),
				"img_type_tximg" => checked($this->table["defs"][$col]["image_type"] == "tximg"),
				"img_type_imgtx" => checked($this->table["defs"][$col]["image_type"] == "imgtx"),
			));
			$coldata[$col][13] = $this->parse("SEL_IMAGE");

			$has_dates = false;
			$awa = new aw_array($this->table["defs"][$col]["els"]);
			
			foreach($awa->get() as $_elid)
			{
				if ($this->table["defs"][$col]["el_main_types"][$_elid] == "date")
				{
					$has_dates = true;
				}
			}
			if ($has_dates)
			{
				$this->vars(array(
					"dateformat" => $this->table["defs"][$col]["dateformat"]
				));
				$coldata[$col][14] = $this->parse("SEL_DATEFORMAT");
			}
		}
		$l = "";
		for ($idx = 1; $idx < 15; $idx++)
		{
			$td = "";
			for ($col = 0; $col < $this->table["cols"]; $col++)
			{
				$this->vars(array(
					"content" => $coldata[$col][$idx]
				));
				$td.=$this->parse("TD");
			}
			$this->vars(array("TD" => $td));
			$l.=$this->parse("ROW");
		}
		$this->vars(array(
			"ROW" => $l,
			"COL_HEADER" => "",
			"SEL_ELS" => "",
			"SEL_ALIAS" => "",
			"SEL_GRPS" => "",
			"SEL_LINK" => "",
			"SEL_SETTINGS" => "",
			"SEL_SETINGS2" => "",
			"SEL_POPUP" => "",
			"SEL_IMAGE" => "",
			"SEL_ORDER_FORM" => "",
			"SEL_FORMEL" => "",
			"SEL_DATEFORMAT" => ""
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=new_submit_cols params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function new_submit_cols($arr)
	{
		extract($arr);
		$this->load_table($id);

		$old_defs = $this->table["defs"];
		$this->table["cols"] = $num_cols;
		$this->table["defs"] = $cols;

		// now create lookup tables for elements so we know in which forms they are
		$t_forms = $this->get_forms_for_table($id);
		
		if (is_array($t_forms))
		foreach($t_forms as $formid)
		{
			$formels=$this->get_form_elements(array("id"=> $formid,"key"=> "id"));
			if (is_array($formels))
			{
				foreach($formels as $k_elid => $v_eldata)
				{
					$els[$k_elid]=$formid;
					$elsubtypes[$formid][$k_elid]["type"]=$v_eldata["type"];
					$elsubtypes[$formid][$k_elid]["subtype"]=$v_eldata["subtype"];
					$elsubtypes[$formid][$k_elid]["thousands_sep"]=$v_eldata["thousands_sep"];
				};
			}
		};

		for ($i=0; $i < $this->table["cols"]; $i++)
		{
			$this->table["defs"][$i]["els"] = $this->make_keys($cols[$i]["els"]);
			$this->table["defs"][$i]["controllers"] = $this->make_keys($cols[$i]["controllers"]);
			if ($this->table["defs"][$i]["link_el"])
			{
				$lel = $this->table["defs"][$i]["link_el"];
				$ret[$els[$lel]][$lel] = $lel;
				$this->table["defs"][$i]["el_forms"][$lel] = $els[$lel];
			}

			foreach($this->table["defs"][$i]["els"] as $elid)
			{
				$this->table["defs"][$i]["el_forms"][$elid] = $els[$elid];
				$this->table["defs"][$i]["el_types"][$elid] = $elsubtypes[$els[$elid]][$elid]["subtype"];
				$this->table["defs"][$i]["el_main_types"][$elid] = $elsubtypes[$els[$elid]][$elid]["type"];

				// if the element was just added, default the damn thing to show and search
				if (!isset($old_defs[$i]["els"][$elid]))
				{
					$this->table["defs"][$i]["el_show"][$elid] = 1;
					$this->table["defs"][$i]["el_search"][$elid] = 1;
				}
			}
			$this->table["defs"][$i]["grps"] = $this->make_keys($cols[$i]["grps"]);
			$this->table["defs"][$i]["alias"] = $this->make_keys($cols[$i]["alias"]);
			$this->table["defs"][$i]["formel"] = $this->make_keys($cols[$i]["formel"]);

			foreach($this->table["defs"][$i]["alias"] as $aid)
			{
				$this->table["defs"][$i]["alias_data"][$aid] = $this->get_data_for_alias($aid);
			}

			// sort elements in col
			$this->elsort_dat = $this->table["defs"][$i]["el_ord"];
			uasort($this->table["defs"][$i]["els"], array($this, "elsort"));

			// this must only be done for all languages except the current
			$tla = new aw_array($old_defs[$i]["lang_title"]);
			foreach($tla->get() as $olt_id => $olt_val)
			{
				if ($olt_id != aw_global_get("lang_id"))
				{
					$this->table["defs"][$i]["lang_title"][$olt_id] = $old_defs[$i]["lang_title"][$olt_id];
				}
			}
		}

		usort($this->table["defs"], create_function('$a,$b','if ($a["ord"] > $b["ord"]) return 1; if ($a["ord"] < $b["ord"]) return -1; return 0;'));

		$this->save_table_settings();
		return $this->mk_my_orb("change", array("id" => $id));
	}

	function elsort($a,$b)
	{
		if ($this->elsort_dat[$a] > $this->elsort_dat[$b])
		{
			return 1;
		}
		if ($this->elsort_dat[$a] < $this->elsort_dat[$b])
		{
			return -1;
		}
		return 0;
	}

	/**  
		
		@attrib name=new_change_styles params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function new_change_styles($arr)
	{
		extract($arr);
		$this->read_template("add_table_styles.tpl");
		$this->load_table($id);
		$this->mk_path($this->table_parent, "Muuda formi tabelit");
		$this->do_menu();

		$s = get_instance(CL_STYLE);
		$css = $s->get_select(0,ST_CELL, true);

		$this->vars(array(
			"tablestyles" => $this->picker($this->table["table_style"],$s->get_select(0,ST_TABLE,true)),
			"header_normal" => $this->picker($this->table["header_normal"],$css),
			"header_link" => $this->picker($this->table["header_link"], $css),
			"header_sortable" => $this->picker($this->table["header_sortable"],$css),
			"header_sortable_link" => $this->picker($this->table["header_sortable_link"], $css),
			"header_sorted" => $this->picker($this->table["header_sorted"],$css),
			"content_style1" => $this->picker($this->table["content_style1"],$css),
			"content_style2" => $this->picker($this->table["content_style2"],$css),
			"content_sorted_style1" => $this->picker($this->table["content_sorted_style1"],$css),
			"content_sorted_style2" => $this->picker($this->table["content_sorted_style2"],$css),
			"group_style" => $this->picker($this->table["group_style"],$css),
			"group_link_style" => $this->picker($this->table["group_link_style"],$css),
			"link_style1" => $this->picker($this->table["link_style1"],$css),
			"link_style2" => $this->picker($this->table["link_style2"],$css),
			"pg_text_style" => $this->picker($this->table["pg_text_style"], $css),
			"pg_text_style_link" => $this->picker($this->table["pg_text_style_link"], $css),
			"pg_lb_style" => $this->picker($this->table["pg_lb_style"], $css),
			"sum_style" => $this->picker($this->table["sum_style"],$css),
			"text_style" => $this->picker($this->table["text_style"],$css),
			"text_style_link" => $this->picker($this->table["text_style_link"],$css),
			"grp_add_els_style" => $this->picker($this->table["grp_add_els_style"],$css),
			"reforb" => $this->mk_reforb("new_submit_styles", array("id" => $id))
		));

		return $this->parse();
	}

	/**  
		
		@attrib name=new_submit_styles params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function new_submit_styles($arr)
	{
		extract($arr);
		$this->load_table($id);

		$this->table = array_merge($this->table, $styles);

		$this->save_table_settings();
		return $this->mk_my_orb("new_change_styles", array("id" => $id));
	}

	/**  
		
		@attrib name=new_change_translate params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function new_change_translate($arr)
	{
		extract($arr);
		$this->read_template("add_table_translate.tpl");
		$this->load_table($id);
		$this->mk_path($this->table_parent, "Muuda formi tabelit");
		$this->do_menu();

		$la = get_instance("languages");
		$ls = $la->get_list();
		foreach($ls as $lid => $lname)
		{
			if ($this->table["languages"][$lid] != $lid)
			{
				unset($ls[$lid]);
			}
		}

		foreach($ls as $lid => $lname)
		{
			$this->vars(array(
				"lang_name" => $lname
			));
			$lh.= $this->parse("LANG_H");
		}

		for ($col = 0; $col < $this->table["cols"]; $col++)
		{
			$this->vars(array(
				"col_id" => $col
			));
			$lg = "";
			foreach($ls as $lid => $lname)
			{
				$this->vars(array(
					"lang_id" => $lid,
					"title" => $this->table["defs"][$col]["lang_title"][$lid]
				));
				$lg.=$this->parse("LANG");
			}
			$this->vars(array(
				"LANG" => $lg
			));
			$l.=$this->parse("COL");
		}

		$clh = "";
		foreach($ls as $lid => $lname)
		{
			$this->vars(array(
				"lang_name" => $lname
			));
			$clh.=$this->parse("CLANG_H");
		}

		$ct = "";
		foreach($this->fakels as $fakelname => $__fk)
		{
			$cl="";
			$this->vars(array(
				"eltype" => $fakelname
			));
			foreach($ls as $lid => $lname)
			{
				$tx = $this->table["texts"][$fakelname][$lid];
				$this->vars(array(
					"lang_id" => $lid,
					"t_name" => ($tx != "" ? $tx : $__fk )
				));
				$cl.=$this->parse("CLANG");
			}
			$this->vars(array(
				"CLANG" => $cl
			));
			$ct.=$this->parse("COL_TEXT");
		}

		// delete popup text
		$cl = "";
		$this->vars(array(
			'eltype' => 'delete_popup_text'
		));
		foreach($ls as $lid => $lname)
		{
			$this->vars(array(
				"lang_id" => $lid,
				"t_name" => (isset($this->table["texts"]['delete_popup_text'][$lid]) ? $this->table["texts"]['delete_popup_text'][$lid] : 'Kas oled kindel et soovid kustutada?' )
			));
			$cl .= $this->parse("CLANG");
		}
		$this->vars(array(
			"CLANG" => $cl
		));
		$ct .= $this->parse("COL_TEXT");

		$this->vars(array(
			"COL_TEXT" => $ct,
			"CLANG_H" => $clh,
			"COL" => $l,
			"LANG_H" => $lh,
			"reforb" => $this->mk_reforb("new_submit_translate", array("id" => $id))
		));

		return $this->parse();
	}

	/**  
		
		@attrib name=new_submit_translate params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function new_submit_translate($arr)
	{
		extract($arr);
		$this->load_table($id);

		$la = get_instance("languages");
		$ls = $la->get_list();

		for ($col = 0; $col < $this->table["cols"]; $col++)
		{
			foreach($ls as $lid => $lname)
			{
				$this->table["defs"][$col]["lang_title"][$lid] = $langs[$col][$lid];
			}
		}
		
		$this->table["texts"] = $texts;

		$this->save_table_settings();
		return $this->mk_my_orb("new_change_translate", array("id" => $id));
	}

	/**  
		
		@attrib name=new_change_aliasmgr params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function new_change_aliasmgr($arr)
	{
		extract($arr);
		$this->read_template("add_table_aliasmgr.tpl");
		$this->load_table($id);
		$this->mk_path($this->table_parent, "Muuda formi tabelit");
		$this->do_menu();

		$this->vars(array(
			"aliasmgr" => $this->mk_my_orb("list_aliases", array("id" => $id), "aliasmgr")
		));
		return $this->parse();
	}

	////
	// !returns an array of all elements that are in the forms selected for this table
	// honors the no_text_tlements setting
	function get_tbl_elements($no_fakels = false)
	{
		$ret = array();
		if (is_array($this->table["forms"]))
		{
			foreach($this->table["forms"] as $fid)
			{
				$ret += $this->get_form_elements(array("id" => $fid, "key" => "id", "all_data" => false));
			}
		}
		if (!$no_fakels)
		{
			$ret+=$this->fakels;
		}
		return $ret;
	}

	function get_col_for_el($el)
	{
		$cl = false;
		for ($_i = 0; $_i < $this->table["cols"]; $_i++)
		{
			if (isset($this->table["defs"][$_i]["els"][$el]) && $this->table["defs"][$_i]["els"][$el])
			{
				$cl = $_i;
			}
		}
		return $cl;
	}

	function get_group_by_elements()
	{
		$ret = array();
		if (is_array($this->table["grps"]))
		{
			foreach($this->table["grps"] as $nr => $dat)
			{
				$ret[$this->table["grps_forms"][$dat["gp_el"]]][] = $dat["gp_el"];
			}
		}
		return $ret;
	}

	function get_group_by_collect_elements()
	{
		$ret = array();
		if (is_array($this->table["grps"]))
		{
			foreach($this->table["grps"] as $nr => $dat)
			{
				if (isset($dat["collect_el"]))
				{
					$ret[$dat["collect_el"]]["sep"] = $dat["sep"];
					$ret[$dat["collect_el"]]["ord_el"] = $dat["ord_el"];
				}
			}
		}
		return $ret;
	}

	////
	// !this sets the numeric status for each element in the form_table to the vcl table
	function set_numeric_fields_in_table()
	{
		for ($i=0; $i < $this->table["cols"]; $i++)
		{
			if (is_array($this->table["defs"][$i]["els"]))
			{
				foreach($this->table["defs"][$i]["els"] as $elid)
				{
					if ($this->table["defs"][$i]["el_types"][$elid] == "int")
					{
						$this->t->set_numeric_field("ev_".$elid);
					}
				}
			}
		}
		if (is_array($this->table["defsort_el_types"]))
		{
			foreach($this->table["defsort_el_types"] as $el => $type)
			{
				if ($type == "int")
				{
					$this->t->set_numeric_field("ev_".$el);
				}
			}
		}
		if (is_array($this->table["rgrps_el_types"]))
		{
			foreach($this->table["rgrps_el_types"] as $el => $type)
			{
				if ($type == "int")
				{
					$this->t->set_numeric_field("ev_".$el);
				}
			}
		}
	}

	////
	// !returns the element contents for a column that has image alias
	// params:
	// $elval - element value befure adding image
	// $alias_target - the linked image id
	// $col - the column that we are talking about
	// $alt - the image's alt text
	function get_image_alias_url($elval, $alias_target, $col, $alt = "")
	{
		$imgdat = $this->image->get_image_by_id($alias_target);

		switch($this->table["defs"][$col]["image_type"])
		{
			case "img":
				$elval = "<img border='0' src='".$imgdat["url"]."' alt=\"$alt\">";
				break;

			case "tximg":
				$elval .= "<img border='0' src='".$imgdat["url"]."' alt=\"$alt\">";
				break;

			case "imgtx":
				$elval = "<img border='0' src='".$imgdat["url"]."' alt=\"$alt\">".$elval;
				break;
		}
		return $elval;
	}

	////
	// !returns the element contents for a column that has form table alias - adds a link around the column
	// params:
	// $elval - element value for the column
	// $alias_target - the id if the aliased form table
	// $row_data - the data containing all tha values for the current row
	// $col - the column we are processing
	// $cc - row settings
	// $textvalue - text value of column before images and links are applied to it
	function get_ftable_alias_url($elval, $alias_target, $row_data, $col, $cc, $form_id, $textvalue)
	{
if ($_GET["HJ"] == 1)
{
	echo dbg::short_backtrace()." <br>";
}
		// request uri, set in constructor
		$ru = $this->ru;
		if (strpos($ru, "?") !== false)
		{
			$ru = obj_link($GLOBALS["cur_process_alias"])."?".substr($ru, strpos($ru, "?"));
		}
		else
		{
			$ru = obj_link($GLOBALS["cur_process_alias"]);
		}
		// new approach - restrict_* are now arrays - so that they remember previous levels as well
		$ru = preg_replace("/use_table=[^&$]*/","",$ru);
		$ru = preg_replace("/tbl_sk=[^&$]*/","",$ru);
		$ru = preg_replace("/old_sk=[^&$]*/","",$ru);
		// matching form and entry
		$ru = preg_replace("/match_form=[^&$]*/","",$ru);
		$ru = preg_replace("/match_entry=[^&$]*/","",$ru);

		// and the leftover &&'s
		$ru = preg_replace("/&{2,}/","&",$ru);
		$ru = str_replace("?&", "?",$ru);
		if (strpos($ru,"?") === false)
		{
			$sep = "?";
		}
		else
		{
			$sep = "&";
		}
		
		$new_sk = gen_uniq_id();
		if ($this->table["has_ftbl_extlinks"])
		{
			$url = $ru.$sep."use_table=".$alias_target;
			$url = substr($url, strpos($url, "?")+1);
			$cur_alias_data = $this->get_cur_processing_alias_data();
			$url = $this->cfg["baseurl"]."/index.".$this->cfg["ext"]."?section=".aw_global_get("section")."&class=form&action=show_entry&id=".$cur_alias_data["id"]."&entry_id=".$cur_alias_data["entry_id"]."&op_id=1&".$url;
		}
		else
		{
			$url = $ru.$sep."use_table=".$alias_target;
		}
		// if we are doing grouping in the table then we must include the group elements value(s) as a restrict search element
		// as well, because it would make a whole lotta sense that way
		if (is_array($this->table["rgrps"]) && !$this->table["no_grpels_in_restrict"])
		{
			foreach($this->table["rgrps"] as $nr => $_dat)
			{
				$search_el = $_dat["search_to_el"] ? $_dat["search_to_el"] : $_dat["el"];
				$search_val = $_dat["search_val_el"] ? $_dat["search_val_el"] : $_dat["el"];
				$url .= "&restrict_search_el[]=".$search_el;
				$url .= "&restrict_search_val[]=".urlencode($row_data["ev_".$search_val]);
				if ($this->table["has_yah"])
				{
					$url .= "&restrict_search_yah[]=".urlencode($row_data["ev_".$_dat["el"]]);
				}
			}
		}

		$url .= "&restrict_search_el[]=".$cc["search_el"];
		$url .= "&restrict_search_val[]=".urlencode($row_data["ev_".$cc["search_map"]]);
//		echo "sm = ",$row_data["ev_".$cc["search_map"]]," <br />";
		if ($this->table["has_yah"])
		{
			$url .= "&restrict_search_yah[]=".urlencode($textvalue);
		}
		
		$url.="&tbl_sk=".$new_sk."&old_sk=";
		if (isset($GLOBALS["tbl_sk"]))
		{
			$url .= $GLOBALS["tbl_sk"];
		};
		$url.="&match_form=".$form_id."&match_entry=".$row_data["entry_id"];

		// AND if there is a form selected as aliase for this column, then we must specify it as the new search form
		if (is_array($this->table["defs"][$col]["alias_data"]))
		{
			foreach($this->table["defs"][$col]["alias_data"] as $_aid)
			{
				if ($_aid["class_id"] == CL_FORM)
				{
					$url = preg_replace("/search_form=[^&$]*/","",$url);
					$url.="&search_form=".$_aid["target"];
				}
			}
		}
		if (!isset($this->table["skip_one_liners_col"]))
		{
			$this->last_table_alias_url = $url;
		}
		else
		{
			if ($col == $this->table["skip_one_liners_col"])
			{
				$this->last_table_alias_url = $url;
			}
		}
		
		$cc = $this->table["defs"][$col];
		if (isset($cc["link_popup"]) && $cc["link_popup"])
		{
			$ret = "<a href=\"".sprintf("javascript:ft_popup('%s&type=popup','popup',%d,%d,%d,%d,%d,%d)",
				$url,
				$cc["link_popup_scrollbars"],
				!$cc["link_popup_fixed"],
				$cc["link_popup_toolbar"],
				$cc["link_popup_addressbar"],
				$cc["link_popup_width"],
				$cc["link_popup_height"]
			)."\">".$elval."</a>";
		}
		else
		{
			$ret = "<a href='".$url."'>";
			$ret.=$elval."</a>";
		}
		return $ret;
	}

	////
	// !returns the element contents for a column that has form output alias - adds a view link to that op for the current entry
	// params:
	// $elval - column value
	// $alias_target - linked op id
	// $entry_id - the entry_id for the current row
	// $section - the current section
	function get_fop_alias_url($elval, $alias_target, $entry_id, $section, $popdat)
	{
		if (isset($popdat["link_popup"]) && $popdat["link_popup"])
		{
			$url = $this->mk_my_orb("show_entry", array(
				"id" => $this->get_form_for_entry($entry_id),
				"entry_id" => $entry_id,
				"op_id" => $alias_target,
				"section" => $section
			),"form", false, true);
			$url = sprintf("javascript:ft_popup('%s&type=popup','popup',%d,%d,%d,%d,%d,%d)",
				$url,
				$popdat["link_popup_scrollbars"],
				!$popdat["link_popup_fixed"],
				$popdat["link_popup_toolbar"],
				$popdat["link_popup_addressbar"],
				$popdat["link_popup_width"],
				$popdat["link_popup_height"]
			);
		}
		else
		{
			$url = $this->mk_my_orb("show_entry", array(
				"id" => $this->get_form_for_entry($entry_id),
				"entry_id" => $entry_id,
				"op_id" => $alias_target,
				"section" => $section
			),"form");
		}
	
		return "<a href=\"".$url."\">".$elval."</a>";
	}

	////
	// !processes all the aliases for the current row and modifies the row content accordingly
	// params:
	// $str - the current row content
	// $cc - the curent row settings ($this->table["defs"][$col])
	// $dat - the current row data
	// $col - the current column number
	// $section - the current section
	// $form_id - the id of the form of the current entry
	// $textvalue - the value of the column without any images or links or shit
	// $image_alt - if set and soem image alaises are present, it gets set as the alt text for the image
	function process_row_aliases($str, $cc, $dat, $col, $section, $form_id, $textvalue, $image_alt = "")
	{
		if (is_array($cc["alias"]))
		{
			// first, the image aliases, because they affect the row contents
			foreach($cc["alias"] as $aid)
			{
				$alias_data = $cc["alias_data"][$aid];
				if ($alias_data["class_id"] == CL_IMAGE)
				{
					$str = $this->get_image_alias_url($str, $alias_data["target"], $col, $image_alt);
				}
			}

			// then the form_table aliases, cause they can add a link around the whole thing
			// and the form_output aliases, for the same reason
			foreach($cc["alias"] as $aid)
			{
				$alias_data = $cc["alias_data"][$aid];
				if ($alias_data["class_id"] == CL_FORM_TABLE)
				{
					// don't make the link if the cell content is itself a link
					if (strpos($str, "<a href=") === false)
					{
						if ($cc["split_col_search"] == 1)
						{
							$sparts = explode($cc["split_col_search_splitter"], $str);
							$rparts = array();
							foreach($sparts as $spart)
							{
								$_tdat = $dat;
								$_tdat["ev_".$cc["search_map"]] = $spart;
								$rparts[] = $this->get_ftable_alias_url($spart, $alias_data["target"], $_tdat, $col, $cc, $form_id, $spart);
							}
							$str = join($cc["split_col_search_splitter"],$rparts);
						}
						else
						{
							$str = $this->get_ftable_alias_url($str, $alias_data["target"], $dat, $col, $cc, $form_id, $textvalue);
						}
					}
					else
					{
						$this->dequote(&$str);
					}
				}
				else
				if ($alias_data["class_id"] == CL_FORM_OUTPUT)
				{
					$str = $this->get_fop_alias_url($str, $alias_data["target"], $dat["entry_id"], $section, $cc);
				}
			}
		}
		
		return $str;
	}

	function get_link($type, $form_id, $section, $op_id, $chain_id, $chain_entry_id, $entry_id)
	{
		$link = "";
		switch($type)
		{
			case "change":
				if ($chain_id)
				{
					// if we are in a chain, leave the chain also shown
					$link = $this->mk_my_orb("show",
						array(
							"id" => $chain_id,
							"form_id" => $form_id,
							"entry_id" => $chain_entry_id,
							"form_entry_id" => $entry_id,
							"section" => $section
						),
					"form_chain");
				}
				else
				{
					$link = $this->mk_my_orb("show", 
						array(					
							"id" => $form_id,
							"entry_id" => $entry_id,
							"section" => $section
						), 
					"form");
				}
				break;

			case "show":
				$link = $this->mk_my_orb("show_entry",array(
					"id" => $form_id,
					"entry_id" => $entry_id, 
					"op_id" => $op_id,				
					"section" => $section
				),
				"form");
				break;

			case "show_popup":
				$link = $this->mk_my_orb("show_entry",array(
					"id" => $form_id,
					"entry_id" => $entry_id, 
					"op_id" => $op_id,				
					"section" => $section
				),
				"form", false,true);
				break;

			case "delete":
				$after_show = $this->ru;
				$link = $this->mk_my_orb(
					"delete_entry", 
					array(
						"id" => $form_id,
						"entry_id" => $entry_id, 
						"after" => $this->binhex($after_show)
					),
					"form"
				);

				if ($this->table["texts"]["delete_popup_text"][$this->lang_id] != "")
				{
					$link = "javascript:ft_confirm('".$this->table["texts"]["delete_popup_text"][$this->lang_id]."','$link')";
				}
				break;
		}
		return $link;
	}

	function do_render_text_aliases($text)
	{
		$am = get_instance("alias_parser");
		
		$aliases = $am->get_oo_aliases(array("oid" => $this->table_id));
		
		// we must do all form table aliases ourselves unfortunately. 
		// form table alias marker is w
		while (preg_match("/#u(\d+)#/U", $text, $mt))
		{
			$alid = $aliases[CL_FORM_OUTPUT][$mt[1]-1]["target"];
			if (!$alid)
			{
				$alid = $aliases[CL_FORM_OUTPUT][$mt[1]]["target"];
			}
			$text = str_replace("#u".$mt[1]."#", $this->do_parse_ftbl_alias($alid), $text);
		}
		
		$am->parse_oo_aliases($this->table_id, $text);
		return $text;
	}
	
	////
	// !renders the aliases that are passed as an array of alias id's
	function render_aliases($arr)
	{
		$tbl = "";
		// this is here to make sure that any document aliases will not be using the print template
		$print = aw_global_get("print");
		aw_global_set("print", false);

		if (is_array($arr))
		{
			$als = $this->get_aliases_for_table();
			foreach($arr as $aid)
			{
				$tmp = obj($this->table_id);
				$c = reset($tmp->connections_from(array(
					"to" => $aid
				)));
				if ($c && $c->prop("to.class_id") == CL_FORM_OUTPUT)
				{
					// if it is a form output alias then show the output with the data the user clicked on last
					$tbl .= $this->do_parse_ftbl_alias($c->prop("to"));
				}
				else
				{
					// if it is something else, oo parse the thing
					// get the #blah555# notation
					$str = " ".$als[$aid]." ";


					$amgr = get_instance("alias_parser");
					$amgr->parse_oo_aliases($this->table_id, $str);

					$tbl .= $str;
				}
			}
		}
		aw_global_set("print", $print);
		return $tbl;
	}

	function do_parse_ftbl_alias($id)
	{
		if (!$GLOBALS["match_form"] || !$GLOBALS["match_entry"])
		{
			$this->raise_error(ERR_FG_TBL_NOSEARCHTBL, "Can't show output alias in form table, no matching form or entry set - outputs can only be shown if doing a search from a previous table", true, false, 0, ($GLOBALS["tbl_sk"] != "" ? true : false));
		}
		$finst = get_instance(CL_FORM);
		return $finst->show(array(
			"id" => $GLOBALS["match_form"],
			"entry_id" => $GLOBALS["match_entry"],
			"op_id" => $id
		));	 
	}
	
	/**  
		
		@attrib name=change_grpsettings params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function change_grpsettings($arr)
	{
		extract($arr);
		$this->read_template("add_table_grpsettings.tpl");
		$this->load_table($id);
		$this->mk_path($this->table_parent, "Muuda formi tabelit");
		$this->do_menu();

		$grplist = get_instance(CL_GROUP)->get_group_picker(array("type" => array(group_obj::TYPE_REGULAR,group_obj::TYPE_DYNAMIC)));

		$l = "";
		if (is_array($this->table["grpsettings"]))
		{
			foreach($this->table["grpsettings"] as $idx => $gsettings)
			{
				$this->vars(array(
					"from_grps" => $this->mpicker($gsettings["from_grps"],$grplist),
					"to_grps" => $this->mpicker($gsettings["to_grps"],$grplist),
					"pri" => $gsettings["pri"],
					"num" => $idx
				));
				$l.=$this->parse("GRPSETTING");
			}
		}

		$this->vars(array(
			"from_grps" => $this->mpicker(array(), $grplist),
			"to_grps" => $this->mpicker(array(), $grplist),
			"pri" => "",
			"num" => $idx+1
		));
		$l.=$this->parse("GRPSETTING");

		$this->vars(array(
			"GRPSETTING" => $l,
			"reforb" => $this->mk_reforb("submit_grpsettings", array("id" => $id))
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_grpsettings params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_grpsettings($arr)
	{
		extract($arr);
		$this->load_table($id);

		$this->table["grpsettings"] = array();
		if (is_array($grpsettings))
		{
			foreach($grpsettings as $gst)
			{
				$gst["from_grps"] = $this->make_keys($gst["from_grps"]);
				$gst["to_grps"] = $this->make_keys($gst["to_grps"]);

				if (count($gst["from_grps"]) > 0)
				{
					$this->table["grpsettings"][] = $gst;
				}
			}
		}

		$this->save_table_settings();
		return $this->mk_my_orb("change_grpsettings", array("id" => $id));
	}

	function do_pageselector($txt)
	{
		if (strpos($txt, "#lk#") !== false)
		{
			$pgsel = "";
			if ($this->table["has_pages"])
			{
				$num_pages = $this->num_lines / $this->table["records_per_page"];
				$ru = preg_replace("/ft_page=\d*/", "", $this->ru);
				$ru = preg_replace("/\&{2,}/","&",$ru);
				$sep = "&";
				if (strpos($ru, "?") === false)
				{
					$sep = "?";
				}
				if ($this->table["has_pages_type"] == "text")	// text pageselector
				{
					$cl = "";
					if ($this->table["pg_text_style"])
					{
						$cl = "class=\"style_".$this->table["pg_text_style"]."\"";
					}
					for ($i = 0; $i < $num_pages; $i++)
					{
						$from = $i*$this->table["records_per_page"]+1;
						$to = min(($i+1)*$this->table["records_per_page"], $this->num_lines);
						$url = $ru.$sep."ft_page=".$i;
						if ($GLOBALS["ft_page"] == $i)
						{
							$pgsel.="<span $cl>".$from." - ".$to."</span>";
						}
						else
						{
							$pgsel.="<span $cl><a href='$url'>".$from." - ".$to."</a></span>";
						}
						if ($i < ($num_pages - 1))
						{
							$pgsel.= " | ";
						}
					}
				}
				else	// listbox pageselector
				{
					$cl = "";
					if ($this->table["pg_lb_style"])
					{
						$cl = "class=\"style_".$this->table["pg_lb_style"]."\"";
					}
					$pgsel = "<select $cl name=\"ft_page\" onChange=\"window.location='".$ru.$sep."ft_page='+this.options[this.selectedIndex].value\">";
					for ($i = 0; $i < $num_pages; $i++)
					{
						$from = $i*$this->table["records_per_page"]+1;
						$to = min(($i+1)*$this->table["records_per_page"], $this->num_lines);
						$sel = "";
						if ($GLOBALS["ft_page"] == $i)
						{
							$sel = "selected";
						}
						$pgsel.= "<option $sel value='".$i."'>".$from." - ".$to;
					}
					$pgsel.="</select>";
				}
			}
			$txt = str_replace("#lk#", $pgsel, $txt);
		}
		return $txt;
	}

	function set_num_rows($num)
	{
		$this->data_num_rows = $num;
	}

	/** if we put a submit button in the table and the user presses it, this is where we end up 
		
		@attrib name=submit_table params=name nologin="1" default="0"
		
		@param return required
		
		@returns
		
		
		@comment

	**/
	function submit_table($arr)
	{
		extract($arr);

		$this->load_table($table_id);

		if (($butt_delete != "" || $butt_delete_x > 0) && is_array($sel))
		{
			foreach($sel as $id => $one)
			{
				if ($one == 1)
				{
					if (!$form_id_for_entries)
					{
						$form_id_for_entries = $this->get_form_for_entry($id);
					}
					$this->do_delete_entry($form_id_for_entries, $id);
				}
			}
		}

		// check for edit elements and if they are there, process the damn things
		$has_edit = false;
		foreach($this->table["defs"] as $col => $def)
		{
			if ($def["is_edit"])
			{
				$has_edit = true;
			}
		}

		if ($has_edit)
		{
			$f = get_instance(CL_FORM);
			$awa = new aw_array($shown_entries);
			foreach($awa->get() as $form_id => $ents)
			{
				//echo "load form $form_id <Br>";
				$f->load($form_id);

				$awg = $GLOBALS["__aw_globals"];
				$en = new aw_array($ents);
				foreach($en->get() as $entryd => $els)
				{
					$GLOBALS["__aw_globals"] = $awg;
					// also, we gots to save all aw globals, cause controllers use them
					$f->load_entry($entryd);
					$chain_entry_id = $this->db_fetch_field("SELECT chain_id FROM form_".$form_id."_entries WHERE id = '$entryd'", "chain_id");
					aw_global_set("proc_chain_entry_id", $chain_entry_id);
					aw_global_set("current_chain_entry", $chain_entry_id);
					aw_global_set("is_ft_change", 1);

					$f->post_vars = array();
					foreach($f->entry as $_eid => $_eval)
					{
						$f->post_vars["ft_row_".$entryd."_form_".$form_id."_".$_eid] = $_eval;
					}
					foreach($_POST as $vn => $vv)
					{
						if (substr($vn, 0, strlen("ft_row_".$entryd."_form_".$form_id)) == "ft_row_".$entryd."_form_".$form_id)
						{
							$f->post_vars[$vn] = $vv;
						}
					}

					$f->no_headers = true;
					$f->process_entry(array(
						"id" => $form_id,
						"entry_id" => $entryd,
						"chain_entry_id" => $chain_entry_id,
						"prefix" => "ft_row_".$entryd."_form_".$form_id."_",
						"no_post_vars" => 1
					));
				}
			}
		}

		return $this->hexbin($return);
	}

	function get_col_picker()
	{
		$ret = array();
		for ($i=0; $i < $this->table["cols"]; $i++)
		{
			$ret[$i] = $this->table["defs"][$i]["lang_title"][aw_global_get("lang_id")];
		}
		return $ret;
	}

	////
	// !returns the name attribute of the <form tag that surrounds the current table
	function get_html_name_for_tbl_form()
	{
		return $this->table_html_form_name;
	}

	////
	// !returns the sum of all price elements shown in the current table so far
	function get_price_elements_sum()
	{
		return $this->pricel_sum;
	}

	function draw_button($bt_id)
	{
		if ($bt_id == "delete" && $this->table["texts"]["delete_popup_text"][$this->lang_id] != "")
		{
			$onc = "onClick=\"if (!confirm('".$this->table["texts"]["delete_popup_text"][$this->lang_id]."')) return false;\"";
		}

		$ret = "";
		if ($this->table["buttons"][$bt_id]["image"]["url"] != "")
		{
			$ret ="<input value='".$this->table["buttons"][$bt_id]["text"]."' name='butt_".$bt_id."' type='image' src='".image::check_url($this->table["buttons"][$bt_id]["image"]["url"])."' $onc>";
		}
		else
		{
			$ret ="<input class=\"button\" type='submit' name='butt_".$bt_id."' value='".$this->table["buttons"][$bt_id]["text"]."' $onc>";
		}
		return $ret;
	}

	function callback_alias_cache_get_url_hash($arr)
	{
		$url = preg_replace('/tbl_sk=[^&$]*/','',$arr['url']);
		$ru = preg_replace('/old_sk=[^&$]*/','',$ru);
		// also insert current user's groups in the url. yeah yeah I know that we could do with less caches, but what the hell
		$ru .= "&gid=".join(",",aw_global_get("gidlist"));
		return gen_uniq_id($ru);
	}

	////
	// !gets called when showing cached alias
	function callback_alias_cache_show_alias($arr)
	{
		// mark down the path
		$old_sk = $GLOBALS["old_sk"];
		$tbl_sk = $GLOBALS["tbl_sk"];
		$fg_table_sessions = aw_global_get("fg_table_sessions");

		// copy the first part of the path from the previous search
		$fg_table_sessions[$tbl_sk] = $fg_table_sessions[$old_sk];

		// check that everyhting is normal
		if (!is_array($fg_table_sessions[$tbl_sk]))
		{
			$fg_table_sessions[$tbl_sk] = Array();
		}

		if ($tbl_sk != "")
		{
			// and finally, add the current search to the path
			$num = count($fg_table_sessions[$tbl_sk]);
			$req = aw_global_get("REQUEST_URI");
			$req = str_replace("&print=1", "", $req);
			$req = str_replace("?print=1", "", $req);
			if ($fg_table_sessions[$tbl_sk][$num-1] != $req)
			{
				$fg_table_sessions[$tbl_sk][] = $req;
			}
		}
		aw_session_set("fg_table_sessions", $fg_table_sessions);
		echo "called back alias cache <br />";
	}

	function create_email_links($str)
	{
		if (aw_ini_get("formgen.obfuscate_email") == 1)
		{
			return preg_replace("/([-.a-zA-Z0-9_]*)@([-.a-zA-Z0-9_]*)/","<script language=\"javascript\">fEpost(\"\\1\",\"\\2\");</script><noscript>\\1<img src='".aw_ini_get("baseurl")."/img/at.png' alt='@' style='vertical-align: middle;'/>\\2</noscript>", $str);
		}
		return preg_replace("/([-.a-zA-Z0-9_]*)@([-.a-zA-Z0-9_]*)/","<a href='mailto:\\1@\\2'>\\1@\\2</a>", $str);
	}


	function get_cur_processing_alias_data()
	{
		return aw_global_get("fg_cur_processing_alias_data");
	}

	function _get_edit_element($elid, $row, $defs)
	{
		$form_id = $defs["el_forms"][$elid];
		if (!is_object($this->edit_el_forms[$form_id]))
		{
			$this->edit_el_forms[$form_id] = get_instance(CL_FORM);
			$this->edit_el_forms[$form_id]->load($form_id);
		}

		$app = "";
		if ($this->edit_el_forms[$form_id]->entry_id != $row["entry_id"])
		{
			$this->edit_el_forms[$form_id]->load_entry($row["entry_id"]);
		}

		$el = $this->edit_el_forms[$form_id]->get_element_by_id($elid);
		$app = "<input type='hidden' name='shown_entries[$form_id][".$row["entry_id"]."][]' value='".$elid."'>";
		return $el->gen_user_html_not("ft_row_".$row["entry_id"]."_form_".$form_id."_").$app;
	}

	function rewrite_table_style_classes()
	{
		$st = get_instance(CL_STYLE);
		$css = array();

		if ($this->table["header_normal"])
		{
			if (($_csscl = $st->get_css_class($this->table["header_normal"])))
			{
				$css["header_normal"] = $_csscl;
			}
			else
			{
				$css["header_normal"] = "style_".$this->table["header_normal"];
			}
		}

		if ($this->table["header_sortable"])
		{
			if (($_csscl = $st->get_css_class($this->table["header_sortable"])))
			{
				$css["header_sortable"] = $_csscl;
			}
			else
			{
				$css["header_sortable"] = "style_".$this->table["header_sortable"];
			}
		}

		if ($this->table["header_sorted"])
		{
			if (($_csscl = $st->get_css_class($this->table["header_sorted"])))
			{
				$css["header_sorted"] = $_csscl;
			}
			else
			{
				$css["header_sorted"] = "style_".$this->table["header_sorted"];
			}
		}

		if ($this->table["content_style1"])
		{
			if (($_csscl = $st->get_css_class($this->table["content_style1"])))
			{
				$css["content_style1"] = $_csscl;
			}
			else
			{
				$css["content_style1"] = "style_".$this->table["content_style1"];
			}
		}

		if ($this->table["content_style2"])
		{
			if (($_csscl = $st->get_css_class($this->table["content_style2"])))
			{
				$css["content_style2"] = $_csscl;
			}
			else
			{
				$css["content_style2"] = "style_".$this->table["content_style2"];
			}
		}

		if ($this->table["content_sorted_style1"])
		{
			if (($_csscl = $st->get_css_class($this->table["content_sorted_style1"])))
			{
				$css["content_sorted_style1"] = $_csscl;
			}
			else
			{
				$css["content_sorted_style1"] = "style_".$this->table["content_sorted_style1"];
			}
		}

		if ($this->table["content_sorted_style2"])
		{
			if (($_csscl = $st->get_css_class($this->table["content_sorted_style2"])))
			{
				$css["content_sorted_style2"] = $_csscl;
			}
			else
			{
				$css["content_sorted_style2"] = "style_".$this->table["content_sorted_style2"];
			}
		}

		if ($this->table["group_style"])
		{
			if (($_csscl = $st->get_css_class($this->table["group_style"])))
			{
				$css["group_style"] = $_csscl;
			}
			else
			{
				$css["group_style"] = "style_".$this->table["group_style"];
			}
		}

		if ($this->table["grp_add_els_style"])
		{
			if (($_csscl = $st->get_css_class($this->table["grp_add_els_style"])))
			{
				$css["grp_add_els_style"] = $_csscl;
			}
			else
			{
				$css["grp_add_els_style"] = "style_".$this->table["grp_add_els_style"];
			}
		}

		return $css;
	}
	
	////
	// !generates a simple one-level menu from the given data structure - the active item is determined by orb action
	function do_tpl_menu($items, &$tpl)
	{
		global $action;
		$im = "";
		foreach($items as $iid => $idata)
		{
			$tpl->vars(array(
				"url"	=> $idata["url"],
				"text" => $idata["name"]
			));
			if ($action == $iid)
			{
				$im.=$tpl->parse("SEL_ITEM");
			}
			else
			{
				$im.=$tpl->parse("ITEM");
			}
		}
		$tpl->vars(array(
			"ITEM" => $im,
			"SEL_ITEM" => ""
		));
		return $tpl->parse();
	}
}
?>
