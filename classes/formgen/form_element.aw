<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_element.aw,v 1.88 2008/03/12 21:24:10 kristo Exp $
// form_element.aw - vormi element.
/*
@classinfo  maintainer=kristo
*/
class form_element extends aw_template
{
		// FIXME: need stringid lokaliseerida
		var $all_subtypes=array(
			"textbox" => array(
				"" => "",
				"count" => "Mitu",
				"period" => "Perioodiühik",
				"int" => "Arv",
				"activity" => "Aktiivsuse pikendamine",
				"email" => "E-mail", 
				"surname" => "Eesnimi", 
				"lastname" => "Perekonnanimi",
				"password" => "Parool"
			),
			"textarea" => array(
			),
			"checkbox" => array(
				"" => "",
				"toggle_active" => "Aktiivsus",
				"is_forum" => "Foorum",
				"no_right_pane" => "Ilma parema paanita",
				"clear_styles" => "T&uuml;hista stiilid",
				"link_keywords" => "Lingi v&otilde;tmes&otilde;nad",
				"archive" => "Arhiveeri",
				"esilehel " => "Esilehel",
				"frontpage_left" => "Esilehel tulbas"
			),
			"radiobutton" => array(
			),
			"listbox" => array(
				"" => "",
				"relation" => "Seoseelement",
				"activity" => "Aktiivsuse pikendamine"
			),
			"multiple" => array(
			),
			"file" => array(
			),
			"link" => array(
				"" => "",
				"show_op" => "N&auml;ita pikemalt",
				"show_calendar" => "Näita kalendrit",
			),
			"button" => array(
				"" => "",
				"submit" => "Submit",
				"reset" => "Reset",
				"delete" => "Kustuta",
				"url" => "URL",
				"preview" => "Eelvaade",
				"confirm" => 
					"Kinnita",
				"order" => "Tellimine",
				"close" => "Sulge aken"
			),
			"price" => array(
			),
			"date" => array(
				"" => "",
				"from" => "Algus",
				"to" => "L&otilde;pp",
				"expires" => "Aegumine",
				"created" => "Loomine",
				"activity" => "Aktiivsuse pikendamine"
			),
			"alias" => array(
				"single" => "Ühekordne",
				"multiple" => "Mitmekordne",
			),
			"timeslice" => array(
				"" => "",
				"period" => "Periood",
				"release" => "Release period",
			),
		);

		var $all_types = array(
			"textbox" => "Tekstiboks",
			'textarea' => "Mitmerealine tekst",
			'checkbox' => "Checkbox",
			'radiobutton' => "Radiobutton",
			'listbox' => "Listbox",
			'multiple' => "Multiple listbox",
			'file' => "Faili lisamine",
			'link' => "H&uuml;perlink",
			'button' => "Nupp",
			'price' => "Hind",
			'date' => "Kuup&auml;ev",
			'alias' => "Alias",
			'timeslice' => "Ajaühik",
		);

	function form_element()
	{
		$this->init("");
		$this->lc_load("form_element","lc_form_element");

		// we need that for wysiwyg "textareas"
		$this->is_ie = !(strpos(aw_global_get("HTTP_USER_AGENT"),"MSIE") === false);

		$this->timeslice_types = array(
			'hour' => $this->vars["SUBTYPE_TS_HOUR"],
			'day' => $this->vars["SUBTYPE_TS_DAY"],
		);
		if (!isset($this->arr["type"]))
		{
			$this->arr["type"] = "";
		}
	}

	////
	// !Loads the element's settings from the data array passed as $arr
	// $form - the form this element is contained in
	// $col - col of the element
	// $row - row of the element
	// $idx - the index this element has in the parent cell's element array
	function load(&$arr,&$form,$col,$row, $idx = 0)
	{
		$this->form =& $form;
		$this->arr = $arr; 
		$this->id = $arr["id"];
		$this->fid = $form->get_id();
		$this->col = $col;
		$this->row = $row;
		$this->index = $idx;
		if (!isset($this->arr["type"]))
		{
			$this->arr["type"] = "";
		}
	}


	function do_core_admin()
	{
		load_vcl("date_edit");
		$de = new date_edit(0);
		$de->configure(array(
			"year" => "",
			"month" => "",
			"day" => "",
			"hour" => "",
			"minute" => "",
			"classid" => "small_button"
		));

		// now load the subtypes from the db or if they are not specified in the db, use all of them.
		// this function puts types and subtypes in $this->subtypes
		$this->get_types_cached();
		$css = get_instance(CL_CSS);

			$this->vars(array(
				"cell_id"									=> "element_".$this->id,
				"cell_text"								=> htmlspecialchars($this->arr["text"]),
				"cell_name"								=> htmlspecialchars($this->arr["name"]),
				"cell_type_name"					=> htmlspecialchars($this->arr["type_name"]),
				"cell_dist"								=> htmlspecialchars($this->arr["text_distance"]),
				"types"										=> $this->picker($this->arr["type"],$this->types),
				"default_name"						=> "element_".$this->id."_def",
				"default"									=> htmlspecialchars($this->arr["default"]),
				"cell_info"								=> htmlspecialchars($this->arr["info"]),
				"front_checked"						=> checked($this->arr["front"] == 1),
				"cell_order"							=> $this->arr["ord"],
				"type"										=> $this->arr["type"],
				"sep_enter_checked"				=> checked($this->arr["sep_type"] == 1),
				"sep_space_checked"				=> checked($this->arr["sep_type"] != 1),
				"cell_sep_pixels"					=> $this->arr["sep_pixels"],
				"element_id"							=> $this->id,
				"text_pos_up"							=> checked($this->arr["text_pos"] == "up"),
				"text_pos_down"						=> checked($this->arr["text_pos"] == "down"),
				"text_pos_left"						=> checked($this->arr["text_pos"] == "left"),
				"text_pos_right"					=> checked($this->arr["text_pos"] == "right"),
				"length"									=> $this->arr["length"],
				"srow_grp"								=> $this->arr["srow_grp"],
				"changepos"								=> $this->mk_orb("change_el_pos",array("id" => $this->fid, "col" => $this->col, "row" => $this->row, "el_id" => $this->id), "form"),
				"ignore_text" 						=> checked($this->arr["ignore_text"]),
				"act_from" 								=> $de->gen_edit_form("element_".$this->id."_act_from",$this->arr["act_from"],2001,2005,true),
				"act_to" 									=> $de->gen_edit_form("element_".$this->id."_act_to",$this->arr["act_to"],2001,2005,true),
				"has_act"								 	=> checked($this->arr["has_act"] == 1),
				"entry_controllers" 			=> $this->multiple_option_list($this->arr["entry_controllers"], $this->form->get_list_controllers()),
				"show_controllers" 				=> $this->multiple_option_list($this->arr["show_controllers"], $this->form->get_list_controllers()),
				"default_controller" 			=> $this->picker($this->arr["default_controller"], $this->form->get_list_controllers(true)),
				"value_controller" 				=> $this->picker($this->arr["value_controller"], $this->form->get_list_controllers(true)),
				"disabled" 								=> checked($this->arr["disabled"]),
				'hidden' 									=> checked($this->arr['hidden']),
				"search_all_text"					=> checked($this->arr["search_all_text"]),
				"search_separate_words"		=> checked($this->arr["search_separate_words"]),
				"search_logical"					=> checked($this->arr["search_logical"]),
				"search_separate_words_sep" => $this->arr["search_separate_words_sep"],
				"search_field_in_set"			=> checked($this->arr["search_field_in_set"]),
				"link_newwindow"					=> checked($this->arr["link_newwindow"]),
				"search_logical_append" => $this->arr["search_logical_append"],
				"search_logical_prepend" => $this->arr["search_logical_prepend"],
				"is_translatable" => checked($this->arr["is_translatable"]),
				"show_as_text" => checked($this->arr["show_as_text"]),
				"no_hidden_el" => checked($this->arr["no_hidden_el"]),
				"el_css_style" => $this->picker($this->arr["el_css_style"], $css->get_select(true)),
				"el_tabindex" => $this->arr["el_tabindex"],
			));

			$this->vars(array(
				"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : "")
			));
			if ($this->form->type == FTYPE_SEARCH && in_array($this->arr["type"],array("textbox","textarea","price")))
			{
				$this->vars(array("SEARCH_PROPS" => $this->parse("SEARCH_PROPS")));
			}

			// now do element metadata
			$mtd = "";
			$md = $this->arr["metadata"][aw_global_get("lang_id")];
			if (!is_array($md) || count($md) < 1)
			{
				$md = $this->arr["metadata"][$this->form->lang_id];
			}
			if (is_array($md))
			{
				foreach($md as $mn => $mv)
				{
					$this->vars(array(
						"metadata_name" => $mn,
						"metadata_value" => $mv
					));
					$mtd.=$this->parse("METADATA");
				}
			}
			$this->vars(array(
				"metadata_name" => "",
				"metadata_value" => ""
			));
			$mtd.=$this->parse("METADATA");
			$this->vars(array(
				"METADATA" => $mtd
			));

			$cd = "";
			$cd = $this->parse("CAN_DELETE");

			$li = ""; $hl = ""; $hl2 = "";
			if ($this->arr["type"] == "link")
			{
				$this->vars(array(
					"link_text"			=> $this->arr["link_text"],
					"link_address"	=> $this->arr["link_address"],
					"subtypes" => $this->picker($this->arr["subtype"], $this->subtypes["link"])
				));
				$li = $this->parse("HLINK_ITEMS");
				if ($this->arr["subtype"] == "show_calendar")
				{
					$this->vars(array(
						"clink_targets" => $this->picker($this->arr["clink_target"],array("form" => "Vorm","form_chain" => "Vormipärg")),
						"clink_no_orb" => checked($this->arr["clink_no_orb"]),
					));
					$this->vars(array(
						"CALENDAR_LINK" => $this->parse("CALENDAR_LINK"),
					));
				};
				$this->vars(array(
					"HAS_SUBTYPE" => $this->parse("HAS_SUBTYPE"),
					"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : "")
				));
			}
			else
			{
				$hl2 = $this->parse("EL_NOHLINK");
			}

			if ($this->arr["type"] == "link" && $this->arr["subtype"] == "show_op")
			{
				$ops = $this->form->get_op_list($this->form->id);
				$this->vars(array(
					"ops" => $this->picker($this->arr["link_op"], $ops[$this->form->id])
				));
				$hl = $this->parse("EL_HLINK");
			}

			$this->vars(array("EL_HLINK" => $hl, "EL_NOHLINK" => $hl2));
			$fi = "";
			if ($this->arr["type"] == "file")
			{
				$this->vars(array(
					"ftype_image_selected"	=> checked($this->arr["ftype"] == 1),
					"ftype_file_selected"		=> checked($this->arr["ftype"] == 2),
					"file_link_text"				=> $this->arr["flink_text"],
					"file_show"							=> checked($this->arr["fshow"] == 1),
					"file_alias"						=> checked($this->arr["fshow"] != 1),
					"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : ""),
					"file_new_win" => checked($this->arr["file_new_win"] == 1),
					"button_css_class" => $this->arr["button_css_class"],
					"file_delete_link_text" => $this->arr["file_delete_link_text"][$this->form->lang_id]				));
				$fi = $this->parse("FILE_ITEMS");
			}

			$lb = "";
			if ($this->arr["type"] == "listbox")
			{
				$this->vars(array(
					"must_fill_checked" => checked($this->arr["must_fill"] == 1),
					"must_error" => $this->arr["must_error"],
					"lb_size" => $this->arr["lb_size"],
					"subtypes" => $this->picker($this->arr["subtype"], $this->subtypes["listbox"]),
					"submit_on_select" => checked($this->arr["submit_on_select"]),
					"lb_search_like" => checked($this->arr["lb_search_like"]),
					"onChange" => $this->arr["onChange"]
				));
				$this->vars(array(
					"HAS_SIMPLE_CONTROLLER" => $this->parse("HAS_SIMPLE_CONTROLLER"),
					"SHOW_AS_TEXT" => $this->parse("SHOW_AS_TEXT")
				));
				$users = get_instance("users");
				for ($b=0; $b < ($this->arr["listbox_count"]+1); $b++)
				{
					$this->vars(array(
						"listbox_item_id" 			=> "element_".$this->id."_lb_".$b,
						"listbox_item_value"		=> $this->arr["listbox_items"][$b],
						"listbox_radio_name"		=> "element_".$this->id."_lradio",
						"listbox_radio_value"		=> $b,
						"listbox_radio_checked"	=> checked($this->arr["listbox_default"] == $b),
						"listbox_order_name" => "element_".$this->id."_lb_order_".$b,
						"listbox_order_value" => $this->arr["listbox_order"][$b],
						"listbox_activity_name" => "element_".$this->id."_lbact_".$b,
						"listbox_activity_value" => $this->arr["listbox_activity"][$b],
						"num" => $b,
						"user_entries_only" => checked($this->arr["user_entries_only"] == 1),
						"user_entries_only_exclude" => $this->mpicker($this->arr["user_entries_only_exclude"], get_instance(CL_GROUP)->get_group_picker(array("type" => array(group_obj::TYPE_REGULAR,group_obj::TYPE_DYNAMIC)))),
						"chain_entries_only" => checked($this->arr["chain_entries_only"] == 1),
					));
					$at = "";
					if ($this->arr["subtype"] == "activity")
					{
						$at = $this->parse("LISTBOX_ITEMS_ACTIVITY");
					}
					$this->vars(array("LISTBOX_ITEMS_ACTIVITY" => $at));
					$lb.=$this->parse("LISTBOX_ITEMS");
				}
				$this->vars(array(
					"HAS_SUBTYPE" => $this->parse("HAS_SUBTYPE"),
					"ACTIVITY" => $this->parse("ACTIVITY")
				));
				$relation_lb = $relation_lb_show = $rel_line = "";
				$relation_uniq = "";
				if ($this->arr["subtype"] == "relation" && !$this->form->is_form_output)
				{
					$this->do_search_script(true);
					$r_els = $this->form->get_elements_for_forms(array($this->arr["rel_form"]));
					$this->vars(array(
						"rel_forms" => $this->picker($this->arr["rel_form"], $this->form->get_relation_targets()),
						"rel_el" => $this->arr["rel_element"],
						"unique"	=> checked($this->arr["rel_unique"] == 1),
						"rel_show_elements" => $this->mpicker($this->arr["rel_elements_show"], $r_els)
					));
					$l = "";
					if (is_array($this->arr["rel_elements_show"]))
					{
						foreach($this->arr["rel_elements_show"] as $r_elid)
						{
							$this->vars(array(
								"rel_el_n" => $r_els[$r_elid],
								"r_id" => $r_elid,
								"r_el_ord" => $this->arr["rel_el_ord"][$r_elid],
								"r_el_sep" => $this->arr["rel_el_sep"][$r_elid],
							));
							$l.=$this->parse("REL_LINE");
						}
					}
					$this->vars(array(
						"REL_LINE" => $l
					));
					$relation_lb = $this->parse("RELATION_LB");
					$relation_uniq = $this->parse("SEARCH_RELATION");
					$relation_lb_show = $this->parse("RELATION_LB_SHOW");
				}
				$this->vars(array(
					"RELATION_LB" => $relation_lb,
					"RELATION_LB_SHOW" => $relation_lb_show,
					"SEARCH_RELATION" => $relation_uniq,
					"HAS_DEFAULT_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_DEFAULT_CONTROLLER") : ""),
				));
			}

			$mu = "";
			$mul_opts = "";
			if ($this->arr["type"] == "multiple")
			{
				for ($b=0; $b < ($this->arr["multiple_count"]+1); $b++)
				{
					$this->vars(array(
						"multiple_item_id" 				=> "element_".$this->id."_mul_".$b,
						"multiple_item_value"			=> $this->arr["multiple_items"][$b],
						"multiple_check_name"			=> "element_".$this->id."_m_".$b,
						"multiple_check_value"		=> "1",
						"multiple_check_checked"	=> checked($this->arr["multiple_defaults"][$b] == 1),
						"multiple_order_name" => "element_".$this->id."_m_order_".$b,
						"multiple_order_value" => $this->arr["multiple_order"][$b],
						"num" => $b
					));
					$mu.=$this->parse("MULTIPLE_ITEMS");
				}
				$this->vars(array(
					"lb_size" => $this->arr["mb_size"],
					"mul_items_sep" => $this->arr['mul_items_sep']
				));
				$mul_opts = $this->parse("MULTIPLE_OPTS");
			}
			$this->vars(array("MULTIPLE_OPTS" => $mul_opts));

			if ($this->arr["type"] == "listbox" || $this->arr["type"] == "multiple")
			{
				$this->vars(array(
					"sort_by_order" => checked($this->arr["sort_by_order"]),
					"sort_by_alpha" => checked($this->arr["sort_by_alpha"]),
					"lb_item_controllers" => $this->multiple_option_list($this->arr["lb_item_controllers"], $this->form->get_list_controllers()),
				));

				if ($this->form->arr["has_controllers"])
				{
					$this->vars(array(
						"HAS_CONTROLLER" => $this->parse("HAS_CONTROLLER"),
						"LB_ITEM_CONTROLLER" => $this->parse("LB_ITEM_CONTROLLER"),
						"NO_ITEM_CONTROLLER" => ""
					));
				}
				else
				{
					$this->vars(array(
						"HAS_CONTROLLER" => "",
						"LB_ITEM_CONTROLLER" => "",
						"NO_ITEM_CONTROLLER" => $this->parse("NO_ITEM_CONTROLLER")
					));
				}

				if ($this->arr["subtype"] == "activity")
				{
					$this->vars(array("LISTBOX_SORT_ACTIVITY" => $this->parse("LISTBOX_SORT_ACTIVITY")));
				}

				$ol = new object_list(array(
					"class_id" => CL_FORM,
					"site_id" => array(),
					"lang_id" => array()
				));
				$lbdforms = array("" => "") + $ol->names();

				asort($lbdforms);
				$lbdeels = array("0" => "");
				if ($this->arr["lb_data_from_form"])
				{
					$lbdeels = $this->form->get_elements_for_forms(array($this->arr["lb_data_from_form"]),false, true);
					$lbdeels_s = $lbdeels;
				}
				else
				if ($this->arr["rel_form"])
				{
					$lbdeels_s = $this->form->get_elements_for_forms(array($this->arr["rel_form"]),false, true);
				}
				$this->vars(array(
					"lb_data_from_form" => $this->picker($this->arr["lb_data_from_form"], $lbdforms),
					"lb_data_from_el" => $this->picker($this->arr["lb_data_from_el"], $lbdeels),
					"lb_data_from_el_sby" => $this->picker($this->arr["lb_data_from_el_sby"], $lbdeels_s)
				));

				$this->vars(array(
					"LISTBOX_SORT" => $this->parse("LISTBOX_SORT"),
					"LB_MUL_DS" => $this->parse("LB_MUL_DS")
				));
			}

			$ta = "";
			if ($this->arr["type"] == "textarea")
			{
				$this->vars(array(
					"textarea_cols_name"	=> "element_".$this->id."_ta_cols",
					"textarea_rows_name"	=> "element_".$this->id."_ta_rows",
					"must_fill_checked" => checked($this->arr["must_fill"] == 1),
					"must_error" => $this->arr["must_error"],
					"check_length" => checked($this->arr["check_length"]),
					"max_length" => $this->arr["max_length"],
					"check_length_error" => $this->arr["check_length_error"],
					"textarea_cols"	=> $this->arr["ta_cols"],
					"textarea_rows"	=> $this->arr["ta_rows"],
					"is_wysiwyg" => checked($this->arr["wysiwyg"] == 1)
				));

				$ta = $this->parse("TEXTAREA_ITEMS");

				$this->vars(array(
					"HAS_SIMPLE_CONTROLLER" => $this->parse("HAS_SIMPLE_CONTROLLER"),
					"HAS_DEFAULT_CONTROLLER" => $this->parse("HAS_DEFAULT_CONTROLLER"),
					"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : ""),
					"CHECK_LENGTH" => $this->parse("CHECK_LENGTH"),
				));
			}

			$gp="";
			if ($this->arr["type"] == "radiobutton")
			{
				$this->vars(array(
					"default_checked"		=> checked($this->arr["default"] == 1),
					"cell_group"				=> $this->arr["group"],
					"ch_value" => $this->arr["ch_value"],
					"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : ""),
				));
				$gp = $this->parse("RADIO_ITEMS");
			}

			$dt="";
			if ($this->arr["type"] == "textbox")
			{
				if ($this->arr["up_down_count_el_form"])
				{
					$udcel_els = $this->form->get_elements_for_forms(array($this->arr["up_down_count_el_form"]), false,true);
				}
				else
				{
					$udcel_els = array(0 => "");
				}
				classload("image");
				$this->vars(array(
					"must_fill_checked" => checked($this->arr["must_fill"] == 1),
					"must_error" => $this->arr["must_error"],
					"subtypes" => $this->picker($this->arr["subtype"], $this->subtypes["textbox"]),
					"activity_hours" => checked($this->arr["activity_type"] == "hours"),
					"activity_days" => checked($this->arr["activity_type"] == "days"),
					"activity_weeks" => checked($this->arr["activity_type"] == "weeks"),
					"activity_months" => checked($this->arr["activity_type"] == "months"),
					"activity_date" => checked($this->arr["activity_type"] == "date"),
					"thousands_sep" => $this->arr["thousands_sep"],
					"check_length" => checked($this->arr["check_length"]),
					"max_length" => $this->arr["max_length"],
					"check_length_error" => $this->arr["check_length_error"],
					"up_down_button" => checked($this->arr["up_down_button"]),
					"up_down_count" => $this->arr["up_down_count"],
					"udcel_forms" => $this->picker($this->arr["up_down_count_el_form"], $this->form->get_flist(array("type" => FTYPE_ENTRY, "addempty" => true))),
					"udcel_els" => $this->picker($this->arr["up_down_count_el_el"], $udcel_els),
					"up_button_img" => image::make_img_tag(image::check_url($this->arr["up_button_img"]["url"])),
					"down_button_img" => image::make_img_tag(image::check_url($this->arr["down_button_img"]["url"])),
					"up_button_use_img" => checked($this->arr["up_button_use_img"]),
					"down_button_use_img" => checked($this->arr["down_button_use_img"]),
					"js_flopper" => checked($this->arr["js_flopper"]),
					"js_flopper_value" => $this->arr["js_flopper_value"]
				));
				$this->vars(array(
					"HAS_SIMPLE_CONTROLLER" => $this->parse("HAS_SIMPLE_CONTROLLER"),
					"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : ""),
					"HAS_DEFAULT_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_DEFAULT_CONTROLLER") : ""),
					"CHECK_LENGTH" => $this->parse("CHECK_LENGTH"),
					"HAS_ADD_SUB_BUTTONS" => ($this->arr["up_down_button"] ? $this->parse("HAS_ADD_SUB_BUTTONS") : ""),
					"SHOW_AS_TEXT" => $this->parse("SHOW_AS_TEXT")
				));
				$dt = $this->parse("DEFAULT_TEXT");
				$this->vars(array("HAS_SUBTYPE" => $this->parse("HAS_SUBTYPE")));
				$this->vars(array("IS_TEXTBOX_ITEMS" => $this->parse("IS_TEXTBOX_ITEMS")));

				if ($this->arr["subtype"] == "activity")
				{
					$this->vars(array("ACTIVITY" => $this->parse("ACTIVITY")));
				}

				if ($this->arr["subtype"] == "count")
				{
					$this->vars(array("COUNT" => $this->parse("COUNT")));
				}

				if ($this->arr["subtype"] == "int")
				{
					$this->vars(array("IS_NUMBER" => $this->parse("IS_NUMBER")));
				}

				if ($this->arr["subtype"] == "period")
				{
					$period_types = array("hour" => "tund","day" => "päev","week" => "week","month" => "month");
					$this->vars(array(
						"period_types" => $this->picker($this->arr["period_type"],$period_types),
						"period_items" => $this->arr["period_items"],
						"max_period_items" => $this->arr["max_period_items"],
					));

					$this->vars(array("HAS_PERIOD" => $this->parse("HAS_PERIOD")));
				}
			}

			$dc="";
			if ($this->arr["type"] == "checkbox")
			{
				$this->vars(array(
					"default_checked"	=> checked($this->arr["default"] == 1),
					"ch_value" => $this->arr["ch_value"],
					"ch_grp" => $this->arr["ch_grp"],
					"subtypes" => $this->picker($this->arr["subtype"], $this->subtypes["checkbox"])
				));
				$dc = $this->parse("CHECKBOX_ITEMS");
				$this->vars(array(
					"HAS_SUBTYPE" => $this->parse("HAS_SUBTYPE"),
					"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : ""),
				));
			}

			$pc="";
			if ($this->arr["type"] == "price")
			{
				$cur = get_instance(CL_CURRENCY);
				$gl = $cur->get_list();
				$this->vars(array(
					"price"	=> $this->arr["price"],
					"price_cur" => $this->picker($this->arr["price_cur"], $gl),
					"price_sep" => $this->arr["price_sep"],
					"price_show" => $this->multiple_option_list($this->arr["price_show"], $gl),
					"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : ""),
				));
				$pc = $this->parse("PRICE_ITEMS");
			}

			$al = "";
			if ($this->arr["type"] == "alias")
			{
				// fid, if we are editing a form
				// output_id, if we are editing an output
				$id = ($this->fid) ? $this->fid : $this->form->output_id;

				$o = obj($id);
				$conn = $o->connections_from();

				$aliaslist = array();
				foreach($conn as $c)
				{
					$aliaslist[$c->prop("to")] = $c->prop("to.name");
				};

				$atypelist = array(
					"0" => "Ühekordne",
					"1" => "Igal sisestusel oma",
				);

				$this->vars(array(
					"aliaslist" => $this->picker($this->arr["alias"],$aliaslist),
					"aliastype" => $this->picker($this->arr["alias_type"],$atypelist),
				));

				$al = $this->parse("ALIASES");
			}


			$bt = "";
			if ($this->arr["type"] == "submit" || $this->arr["type"] == "reset")
			{
				$this->vars(array(
					"button_text" => $this->arr["button_text"],
					"button_css_class" => $this->arr["button_css_class"],
					"chain_forward" => checked($this->arr["chain_forward"]==1)
				));
				$bt = $this->parse("BUTTON_ITEMS");
			}

			$bt = "";
			if ($this->arr["type"] == "button")
			{
				if ($this->arr["subtype"] == "preview")
				{
					$formb = get_instance("formgen/form_base");
					$opl = $formb->get_op_list();

					$this->vars(array(
						"bops" => $this->picker($this->arr["button_op"],$opl[$this->fid])
					));
				}

				$img = "";
				if ($this->arr["button_img"]["url"] != "")
				{
					classload("image");
					$img = "<img src='".image::check_url($this->arr["button_img"]["url"])."'>";
				}
				$this->vars(array(
					"button_css_class" => $this->arr["button_css_class"],
					"button_text" => $this->arr["button_text"],
					"subtypes" => $this->picker($this->arr["subtype"], $this->subtypes["button"]),
					"button_url" => $this->arr["button_url"],
					"chain_forward" => checked($this->arr["chain_forward"]==1),
					"chain_backward" => checked($this->arr["chain_backward"]==1),
					"chain_finish" => checked($this->arr["chain_finish"]==1),
					"folders" => $this->picker($this->arr["confirm_moveto"],$this->get_menu_list()),
					"redirect" => $this->arr["confirm_redirect"],
					"order_form" => $this->picker($this->arr["order_form"],$this->form->get_list(FTYPE_ENTRY)),
					"button_img" => $img,
					"use_button_img" => checked($this->arr["button_img"]["use"] == 1),
					"bt_redir_after_submit" => checked($this->arr["bt_redir_after_submit"]),
					"button_js_confirm" => checked($this->arr["button_js_confirm"]),
					"button_js_confirm_text" => $this->arr["button_js_confirm_text"],
					"button_js_next_form_in_chain" => checked($this->arr["button_js_next_form_in_chain"])
				));
				$bt = $this->parse("BUTTON_ITEMS");
				$this->vars(array(
					"HAS_SUBTYPE" => $this->parse("HAS_SUBTYPE"),
					"BUTTON_CONFIRM_TYPE" => ($this->arr["subtype"] == "confirm" ? $this->parse("BUTTON_CONFIRM_TYPE") : ""),
					"BUTTON_SUB_URL" => ($this->arr["subtype"] == "url" ? $this->parse("BUTTON_SUB_URL") : ""),
					"BUTTON_SUB_OP" => ($this->arr["subtype"] == "preview" ? $this->parse("BUTTON_SUB_OP") : ""),
					"BUTTON_SUB_ORDER" => ($this->arr["subtype"] == "order" ? $this->parse("BUTTON_SUB_ORDER") : ""),
					"HAS_ONLY_SHOW_CONTROLLER" => "" ,
					"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : ""),
				));
			}

			$tslice = "";
			if ($this->arr["type"] == "timeslice")
			{
				$this->vars(array(
					"slicelengthlist" => $this->picker($this->arr["slicelength"],$this->timeslice_types),
					"subtypes" => $this->picker($this->arr["subtype"], $this->subtypes["timeslice"]),
				));
				$this->vars(array(
					"TIMESLICE" => $this->parse("TIMESLICE"),
					"HAS_SUBTYPE" => $this->parse("HAS_SUBTYPE"),
				));
			};

			$di = "";
			if ($this->arr["type"] == "date")
			{
				$add_types = array("60" => "Minutit", "3600" => "Tundi", "86400" => "P&auml;eva", "604800" => "N&auml;dalat", "2592000" => "Kuud");
				$d_el_os = $this->form->get_element_by_type("date","",true);
				$d_els = array();
				foreach($d_el_os as $d_el)
				{
					if ($d_el->get_id() != $this->get_id())	// do not let the user select the current element
					{
						$d_els[$d_el->get_id()] = $d_el->get_el_name();
					}
				}
				$has_all = false;
				if ($this->arr["has_year"] != 1 && $this->arr["has_month"] != 1 && $this->arr["has_day"] != 1 &&
						$this->arr["has_hr"] != 1 && $this->arr["has_minute"] != 1 && $this->arr["has_second"] != 1)
				{
					$has_all = true;
				}
				$this->vars(array(
					"from_year" => $this->arr["from_year"],
					"to_year" => $this->arr["to_year"],
					"subtypes" => $this->picker($this->arr["subtype"], $this->subtypes["date"]),
					"def_date_num" => $this->arr["def_date_num"],
					"add_types" => $this->picker($this->arr["def_date_add"],$add_types),
					"date_now_checked" => checked($this->arr["def_date_type"] == "now"),
					"date_rel_checked" => checked($this->arr["def_date_type"] == "rel"),
					"date_none_checked" => checked($this->arr["def_date_type"] == "none"),
					"date_rel_els" => $this->picker($this->arr["def_date_rel_el"], $d_els),
					"has_year" => checked($this->arr["has_year"] == 1 || $has_all),
					"has_month" => checked($this->arr["has_month"] == 1 || $has_all),
					"has_day" => checked($this->arr["has_day"] == 1 || $has_all),
					"has_hr" => checked($this->arr["has_hr"] == 1),
					"has_minute" => checked($this->arr["has_minute"] == 1),
					"has_second" => checked($this->arr["has_second"] == 1),
					"date_format" => $this->arr["date_format"],
					"year_ord" => $this->arr["year_ord"],
					"month_ord" => $this->arr["month_ord"],
					"day_ord" => $this->arr["day_ord"],
					"hr_ord" => $this->arr["hour_ord"],
					"minute_ord" => $this->arr["minute_ord"],
					"second_ord" => $this->arr["second_ord"],
					"year_textbox" => checked($this->arr["year_textbox"]),
					"month_textbox" => checked($this->arr["month_textbox"]),
					"day_textbox" => checked($this->arr["day_textbox"]),
					"hr_textbox" => checked($this->arr["hr_textbox"]),
					"minute_textbox" => checked($this->arr["minute_textbox"]),
					"second_textbox" => checked($this->arr["second_textbox"]),
					// use a textbox for entering dates
					"visual_use_textbox" => checked($this->arr["visual_use_textbox"]),
				));
				$di = $this->parse("DATE_ITEMS");
				$this->vars(array(
					"HAS_SUBTYPE" => $this->parse("HAS_SUBTYPE"),
					"HAS_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_CONTROLLER") : ""),
					"HAS_DEFAULT_CONTROLLER" => ($this->form->arr["has_controllers"] ? $this->parse("HAS_DEFAULT_CONTROLLER") : ""),
				));
			}

			if ($this->form->arr["save_table"] == 1)
			{
				$tar = array("" => "");
				if (is_array($this->form->arr["save_tables"]))
				{
					foreach($this->form->arr["save_tables"] as $tbl => $tblcolel)
					{
						$tar[$tbl] = $this->form->get_fg_tblname($tbl);
					}
				}

				$num = -1;
				if (is_array($this->arr["table"]))
				{
					foreach($this->arr["table"] as $num => $dat)
					{
						$this->vars(array(
							"tables" => $this->picker($dat["table"], $tar),
							"table_col" => $dat["col"],
							"num" => $num
						));
						$_tbp.=$this->parse("TABLE_LB");
					}
				}
				$this->vars(array(
					"tables" => $this->picker("", $tar),
					"table_col" => "",
					"num" => ++$num
				));
				$_tbp.=$this->parse("TABLE_LB");
				$this->vars(array("TABLE_LB" => $_tbp));
			}

			$this->vars(array(
				"IS_TRANSLATABLE" => ($this->form->arr["is_translatable"] ? $this->parse("IS_TRANSLATABLE") : ""),
				"LISTBOX_ITEMS"		=> $lb,
				"MULTIPLE_ITEMS"	=> $mu,
				"TEXTAREA_ITEMS"	=> $ta,
				"RADIO_ITEMS"			=> $gp,
				"DEFAULT_TEXT"		=> $dt,
				"CHECKBOX_ITEMS"	=> $dc,
				"CAN_DELETE"			=> $cd,
				"FILE_ITEMS"			=> $fi,
				"HLINK_ITEMS"			=> $li,
				"BUTTON_ITEMS"		=> $bt,
				"PRICE_ITEMS"			=> $pc,
				"DATE_ITEMS"			=> $di,
				"ALIASES"			=> $al,
			));
	}

	function do_core_save(&$arr)
	{
		extract($arr);

		$base = "element_".$this->id;

		$var = $base."_sort_order";
		$this->arr["sort_by_order"] = $$var;

		$var = $base."_sort_alpha";
		$this->arr["sort_by_alpha"] = $$var;

		$var = $base."_ignore_text";
		$this->arr["ignore_text"] = $$var;

		$var = $base."_is_translatable";
		$this->arr["is_translatable"] = $$var;

		$var = $base."_show_as_text";
		$this->arr["show_as_text"] = $$var;

		$var = $base."_no_hidden_el";
		$this->arr["no_hidden_el"] = $$var;

		$var = $base."_el_css_style";
		$this->arr["el_css_style"] = $$var;

		$var = $base."_el_tabindex";
		$this->arr["el_tabindex"] = $$var;

		$cnt =0;
		if (is_array($this->arr["table"]))
		{
			$cnt = count($this->arr["table"]);
		}
		$this->arr["table"] = array();
		$num = 0;
		for ($i = 0; $i < $cnt+1; $i++)
		{
			$var = $base."_table_".$i;
			$var2 = $base."_tbl_col_".$i;
			if ($$var != "")
			{
				$this->arr["table"][$num] = array("table" => $$var,"col" => $$var2);
				$num++;
			}
		}

		$var = $base."_act_from";
		global $$var;
		$v = $$var;
		$this->arr["act_from"] = mktime($v["hour"],$v["minute"],0,$v["month"],$v["day"],$v["year"]);

		$var = $base."_act_to";
		global $$var;
		$v = $$var;
		$this->arr["act_to"] = mktime($v["hour"],$v["minute"],0,$v["month"],$v["day"],$v["year"]);

		$var = $base."_has_act";
		global $$var;
		$this->arr["has_act"] = $$var;

		$var = $base."_search_all_text";
		global $$var;
		$this->arr["search_all_text"] = $$var;

		$var = $base."_search_separate_words";
		global $$var;
		$this->arr["search_separate_words"] = $$var;

		$var = $base."_search_separate_words_sep";
		global $$var;
		$this->arr["search_separate_words_sep"] = $$var;

		$var = $base."_search_logical";
		global $$var;
		$this->arr["search_logical"] = $$var;

		$var = $base."_search_logical_prepend";
		global $$var;
		$this->arr["search_logical_prepend"] = $$var;

		$var = $base."_search_logical_append";
		global $$var;
		$this->arr["search_logical_append"] = $$var;

		$var = $base."_search_field_in_set";
		global $$var;
		$this->arr["search_field_in_set"] = $$var;

		$var=$base."_text";
		$this->arr["text"] = $$var;
		$var=$base."_name";
		// check if the name has changed and if it has, then update the real object also
		if ($$var != $this->arr["name"])
		{
			$this->arr["name"] = $$var;
			$this->do_change_name($$var);
		}

		$var=$base."_type_name";
		if ($$var != $this->arr["type_name"])
		{
			$this->arr["type_name"] = $$var;
			$this->do_change_type_name($$var);
		}

		$var=$base."_list";
		$this->arr["join_list"] = $$var;
		$var=$base."_email_el";
		$this->arr["join_email"] = $$var;

		$var=$base."_type";
		if ($$var == "delete")
		{
			return false;
		}

		$this->arr["type"] = $$var;
		$var = $base."_info";
		$this->arr["info"]=$$var;
		$var=$base."_front";
		$this->arr["front"] = $$var;
		$var=$base."_dist";
		$this->arr["text_distance"] = $$var;
		$var=$base."_text_pos";
		$this->arr["text_pos"] = $$var;

		// save selected controllers for element
		$var=$base."_entry_controllers";
		$this->arr["entry_controllers"] = $this->make_keys($$var);
		$var=$base."_show_controllers";
		$this->arr["show_controllers"] = $this->make_keys($$var);
		$var=$base."_default_controller";
		$this->arr["default_controller"] = $$var;
		$var=$base."_value_controller";
		$this->arr["value_controller"] = $$var;

		$var=$base."_disabled";
		$this->arr["disabled"] = $$var;

		$var=$base.'_hidden';
		$this->arr['hidden'] = $$var;

		// metadata
		$this->arr["metadata"][aw_global_get("lang_id")] = array();
		$varn = $base."_metadata_name";
		$varv = $base."_metadata_value";
		$varv = $$varv;
		foreach($$varn as $nr => $vl)
		{
			if ($vl != "")
			{
				$this->arr["metadata"][aw_global_get("lang_id")][$vl] = $varv[$nr];
			}
		}

		if ($this->arr["type"] == "listbox" || $this->arr["type"] == "multiple")
		{
			$var = $base."_lb_data_from_form";
			$this->arr["lb_data_from_form"] = $$var;

			$var = $base."_lb_data_from_el";
			$this->arr["lb_data_from_el"] = $$var;
			
			$var = $base."_lb_data_from_el_sby";
			$this->arr["lb_data_from_el_sby"] = $$var;
		}

		if ($this->arr["type"] == "listbox")
		{
			$arvar = $base."_sel";
			$ar = $$arvar;

			$dwtvar = $base."_lbitems_dowhat";
			$dwat = $$dwtvar;

			$var = $base."_lb_size";
			$this->arr["lb_size"] = $$var;

			$var = $base."_submit_on_select";
			$this->arr["submit_on_select"] = $$var;

			$var = $base."_lb_search_like";
			$this->arr["lb_search_like"] = $$var;

			$var = $base."_onChange";
			$this->arr["onChange"] = $$var;

			$var = $base."_lb_item_controllers";
			$this->arr["lb_item_controllers"] = $this->make_keys($$var);

			$this->arr["listbox_items"] = array();
			$cnt=$this->arr["listbox_count"]+1;
			for ($b=0,$num=0; $b < $cnt; $b++)
			{
				if (!($dwat == "del" && $ar[$b] == 1))
				{
					$var=$base."_lb_".$b;
					$this->arr["listbox_items"][$num] = $$var;

					$var=$base."_lb_order_".$b;
					$this->arr["listbox_order"][$num] = $$var;

					$var=$base."_lbact_".$b;
					$this->arr["listbox_activity"][$num] = $$var;

					$num++;
					if ($dwat=="add" && $ar[$b] == 1)
					{
						$this->arr["listbox_items"][$num] = " ";
						$num++;
					}
				}
			}
			while (isset($this->arr["listbox_items"][$num-1]) && ($this->arr["listbox_items"][$num-1] == ""))
			{
				$num--;
			}

			$this->arr["listbox_count"]=$num;
			$var = $base."_lradio";
			$this->arr["listbox_default"] = $$var;

			$this->import_lb_data();

			// sort listbox
			$this->sort_listbox();

			// save relation elements
			$this->db_query("DELETE FROM form_relations WHERE form_from = '".$this->arr["rel_form"]."' AND form_to = '".$this->form->id."' AND el_from = '".$this->arr["rel_element"]."' AND el_to = '".$this->id."'");
			if ($this->arr["subtype"] == "relation")
			{
				$var = $base."_unique";
				$this->arr["rel_unique"] = $$var;

				$var = $base."_user_entries_only";
				$this->arr["user_entries_only"] = $$var;

				$var = $base."_user_entries_only_exclude";
				$this->arr["user_entries_only_exclude"] = $this->make_keys($$var);

				$var = $base."_chain_entries_only";
				$this->arr["chain_entries_only"] = $$var;

				$var = $base."_rel_element_show";
				$this->arr["rel_elements_show"] = $this->make_keys($$var);

				$var = $base."_rel_element_show_order";
				$this->arr["rel_el_ord"] = $$var;
				if (is_array($this->arr["rel_el_ord"]))
				{
					asort($this->arr["rel_el_ord"]);
				}

				$var = $base."_rel_element_show_sep";
				$this->arr["rel_el_sep"] = $$var;

				$rel_changed = false;
				$var = $base."_rel_form";
				$this->arr["rel_form"] = $$var;

				$var = $base."_rel_element";
				$this->arr["rel_element"] = $$var;

				// always update the relation in the table, just to be sure
				if ($this->arr["rel_table_id"])
				{
					// SET form_from = '".$this->arr["rel_form"]."' , form_to = '".$this->form->id."' , el_from = '".$this->arr["rel_element"]."' , el_to = ".$this->id."
					$this->db_query("DELETE FROM form_relations WHERE id = '".$this->arr["rel_table_id"]."'");
				}

				// make sure we got it right.
				$this->db_query("DELETE FROM form_relations WHERE form_from = '".$this->arr["rel_form"]."' AND form_to = '".$this->form->id."' AND el_from = '".$this->arr["rel_element"]."' AND el_to = '".$this->id."'");

				$this->db_query("INSERT INTO form_relations (form_from,form_to,el_from,el_to) VALUES('".$this->arr["rel_form"]."','".$this->form->id."','".$this->arr["rel_element"]."','".$this->id."')");
				$this->arr["rel_table_id"] = $this->db_last_insert_id();
			}
		}

		if ($this->arr["type"] == "multiple")
		{
			$arvar = $base."_sel";
			$ar = $$arvar;

			$dwtvar = $base."_lbitems_dowhat";
			$dwat = $$dwtvar;

			$var = $base."_lb_size";
			$this->arr["mb_size"] = $$var;

			$var = $base."_lb_item_controllers";
			$this->arr["lb_item_controllers"] = $this->make_keys($$var);

			$this->arr["multiple_items"] = array();
			$cnt=$this->arr["multiple_count"]+1;	
			for ($b=0,$num=0; $b < $cnt; $b++)
			{
				if (!($dwat == "del" && $ar[$b] == 1))
				{
					$var=$base."_mul_".$b;
					$this->arr["multiple_items"][$num] = $$var;

					$var = $base."_m_".$b;
					$this->arr["multiple_defaults"][$num] = $$var;

					$var=$base."_m_order_".$b;
					$this->arr["multiple_order"][$num] = $$var;
					$num++;

					if ($dwat=="add" && $ar[$b] == 1)
					{
						$this->arr["multiple_items"][$num] = " ";
						$num++;
					}
				}
			}
			if ($this->arr["multiple_items"][$num-1] == "")
			{
				$num--;
			}
			$this->arr["multiple_count"]=$num;
			$this->sort_multiple();

			$this->import_m_data();

			$var = $base."_mul_items_sep";
			$this->arr["mul_items_sep"]= $$var;
		}

		if ($this->arr["type"] == "textarea")
		{
			$var = $base."_ta_rows";
			$this->arr["ta_rows"]= $$var;
			$var = $base."_ta_cols";
			$this->arr["ta_cols"]=$$var;
			$var=$base."_must_fill";
			$this->arr["must_fill"] = $$var;
			$var=$base."_must_error";
			$this->arr["must_error"] = $$var;
			$var=$base."_wysiwyg";
			$this->arr["wysiwyg"] = $$var;
			$var=$base."_check_length";
			$this->arr["check_length"] = $$var;
			$var=$base."_max_length";
			$this->arr["max_length"] = $$var;
			$var=$base."_check_length_error";
			$this->arr["check_length_error"] = $$var;
		}

		if ($this->arr["type"] == "radiobutton")
		{
			$var=$base."_group";
			$this->arr["group"] = $$var;
		}

		if ($this->arr["type"] == "price")
		{
			$var=$base."_price";
			$this->arr["price"] = $$var;
			$var=$base."_length";
			$this->arr["length"] = $$var;
			$var=$base."_price_cur";
			$this->arr["price_cur"] = $$var;
			$var=$base."_price_sep";
			$this->arr["price_sep"] = $$var;
			$var=$base."_price_show";
			$this->arr["price_show"] = array();
			if (is_array($$var))
			{
				foreach($$var as $curid)
				{
					$this->arr["price_show"][$curid] = $curid;
				}
			}
		}

		if ($this->arr["type"] == "alias")
		{
			$var=$base."_alias";
			$this->arr["alias"] = $$var;

			// I should somehow determine what type the alias is,
			// and if it is a calendar, then update it to point to an entry

			$var=$base."_alias_type";
			$this->arr["alias_type"] = $$var;
			
		}

		if ($this->arr["type"] == "textbox" || $this->arr["type"] == "textarea" || $this->arr["type"] == "checkbox" || $this->arr["type"] == "radiobutton")
		{
			$var=$base."_def";
			$this->arr["default"] = $$var;
			$var=$base."_length";
			$this->arr["length"] = $$var;
			$var = $base."_ch_value";
			$this->arr["ch_value"] = $$var;
		}

		if ($this->arr["type"] == "checkbox")
		{
			$var = $base."_ch_grp";
			$this->arr["ch_grp"] = $$var;
		}

		if ($this->arr["type"] == "textbox" || $this->arr["type"] == "listbox")
		{
			$var=$base."_must_fill";
			$this->arr["must_fill"] = $$var;
			$var=$base."_must_error";
			$this->arr["must_error"] = $$var;
		}

		if ($this->arr["type"] == "textbox")
		{
			$var=$base."_activity_type";
			$this->arr["activity_type"] = $$var;
			$var=$base."_thousands_sep";
			$this->arr["thousands_sep"] = $$var;
			$var=$base."_period_type";
			$this->arr["period_type"] = $$var;
			$var=$base."_period_items";
			$this->arr["period_items"] = $$var;
			$var=$base."_max_period_items";
			$this->arr["max_period_items"] = $$var;
			$var=$base."_check_length";
			$this->arr["check_length"] = $$var;
			$var=$base."_max_length";
			$this->arr["max_length"] = $$var;
			$var=$base."_check_length_error";
			$this->arr["check_length_error"] = $$var;
			$var=$base."_up_down_button";
			$this->arr["up_down_button"] = $$var;
			$var=$base."_up_down_count";
			$this->arr["up_down_count"] = $$var;
			$var=$base."_up_down_count_el_form";
			$this->arr["up_down_count_el_form"] = $$var;
			$var=$base."_up_down_count_el_el";
			$this->arr["up_down_count_el_el"] = $$var;

			$var=$base."_up_button_use_img";
			$this->arr["up_button_use_img"] = $$var;
			$var=$base."_down_button_use_img";
			$this->arr["down_button_use_img"] = $$var;

			$var=$base."_js_flopper";
			$this->arr["js_flopper"] = $$var;
			$var=$base."_js_flopper_value";
			$this->arr["js_flopper_value"] = $$var;

			$img = get_instance(CL_IMAGE);
			$var=$base."_up_button_img";
			$this->arr["up_button_img"] = $img->add_upload_image($var, $this->id, $this->arr["up_button_img"]["id"]);
			$var=$base."_down_button_img";
			$this->arr["down_button_img"] = $img->add_upload_image($var, $this->id, $this->arr["down_button_img"]["id"]);
		}

		if ($this->arr["type"] == 'file')
		{
			$var=$base."_filetype";
			$this->arr["ftype"] = $$var;
			$var=$base."_file_link_text";
			$this->arr["flink_text"] = $$var;
			$var=$base."_file_show";
			$this->arr["fshow"] = $$var;
			$var=$base."_file_new_win";
			$this->arr["file_new_win"] = $$var;
			
			$var = $base."_button_css_class";
			$this->arr["button_css_class"] = $$var;

			$var=$base."_file_delete_link_text";
			if (!is_array($this->arr["file_delete_link_text"]))
			{
				$this->arr["file_delete_link_text"] = array();
			}
			$this->arr["file_delete_link_text"][$this->form->lang_id] = $$var;
		}

		if ($this->arr["type"] == 'link')
		{
			$var=$base."_link_text";
			$this->arr["link_text"] = $$var;
			$var=$base."_link_address";
			$this->arr["link_address"] = $$var;
			$var=$base."_link_op";
			$this->arr["link_op"] = $$var;
			$var=$base."_clink_target";
			$this->arr["clink_target"] = $$var;
			$var=$base."_clink_no_orb";
			$this->arr["clink_no_orb"] = $$var;
			$var=$base.'_link_newwindow';
			$this->arr['link_newwindow'] = $$var;
		}

		if ($this->arr["type"] == 'timeslice')
		{
			$var=$base."_slicelength";
			$this->arr["slicelength"] = $$var;
		}

		if ($this->arr["type"] == 'date')
		{
			$var=$base."_from_year";
			$this->arr["from_year"] = $$var;
			$var=$base."_to_year";
			$this->arr["to_year"] = $$var;
			$var=$base."_def_date_type";
			$this->arr["def_date_type"] = $$var;
			$var=$base."_def_date_num";
			$this->arr["def_date_num"] = $$var;
			$var=$base."_def_date_add_type";
			$this->arr["def_date_add"] = $$var;
			$var=$base."_def_date_rel";
			$this->arr["def_date_rel_el"] = $$var;
			$var=$base."_has_year";
			$this->arr["has_year"] = $$var;
			$var=$base."_has_month";
			$this->arr["has_month"] = $$var;
			$var=$base."_has_day";
			$this->arr["has_day"] = $$var;
			$var=$base."_has_hr";
			$this->arr["has_hr"] = $$var;
			$var=$base."_has_minute";
			$this->arr["has_minute"] = $$var;
			$var=$base."_has_second";
			$this->arr["has_second"] = $$var;
			$var=$base."_date_format";
			$this->arr["date_format"] = $$var;
			$var=$base."_year_ord";
			$this->arr["year_ord"] = $$var;
			$var=$base."_month_ord";
			$this->arr["month_ord"] = $$var;
			$var=$base."_day_ord";
			$this->arr["day_ord"] = $$var;
			$var=$base."_hr_ord";
			$this->arr["hour_ord"] = $$var;
			$var=$base."_minute_ord";
			$this->arr["minute_ord"] = $$var;
			$var=$base."_second_ord";
			$this->arr["second_ord"] = $$var;
			$var=$base."_year_textbox";
			$this->arr["year_textbox"] = $$var;
			$var=$base."_month_textbox";
			$this->arr["month_textbox"] = $$var;
			$var=$base."_day_textbox";
			$this->arr["day_textbox"] = $$var;
			$var=$base."_hr_textbox";
			$this->arr["hr_textbox"] = $$var;
			$var=$base."_minute_textbox";
			$this->arr["minute_textbox"] = $$var;
			$var=$base."_second_textbox";
			$this->arr["second_textbox"] = $$var;
			$var=$base."_visual_use_textbox";
			$this->arr["visual_use_textbox"] = $$var;
		}

		if ($this->arr["type"] == "submit" || $this->arr["type"] == "reset" || $this->arr["type"] == "button")
		{
			$var = $base."_btext";
			$this->arr["button_text"] = $$var;

			$var = $base."_burl";
			$this->arr["button_url"] = $$var;

			$var = $base."_bop";
			$this->arr["button_op"] = $$var;

			$var = $base."_chain_forward";
			$this->arr["chain_forward"] = $$var;

			$var = $base."_chain_backward";
			$this->arr["chain_backward"] = $$var;

			$var = $base."_chain_finish";
			$this->arr["chain_finish"] = $$var;

			$var = $base."_confirm_moveto";
			$this->arr["confirm_moveto"] = $$var;

			$var = $base."_confirm_redirect";
			$this->arr["confirm_redirect"] = $$var;

			$var = $base."_order_form";
			$this->arr["order_form"] = $$var;

			$var = $base."_use_button_img";
			$this->arr["button_img"]["use"] = $$var;

			$var = $base."_button_css_class";
			$this->arr["button_css_class"] = $$var;

			$var = $base."_bt_redir_after_submit";
			$this->arr["bt_redir_after_submit"] = $$var;

			$var = $base."_button_js_confirm";
			$this->arr["button_js_confirm"] = $$var;

			$var = $base."_button_js_confirm_text";
			$this->arr["button_js_confirm_text"] = $$var;

			$var = $base."_button_js_next_form_in_chain";
			$this->arr["button_js_next_form_in_chain"] = $$var;

			$var = $base."_button_img";
			$im = get_instance(CL_IMAGE);
			$_tmp = $im->add_upload_image($var,$this->id);
			if ($_tmp)
			{
				$this->arr["button_img"] = $_tmp;
			}
		}

		$var = $base."_separator_type";
		$this->arr["sep_type"] = $$var;

		$var = $base."_sep_pixels";
		$this->arr["sep_pixels"] = $$var;

		$var = $base."_order";
		$$var+=0;
		if ($this->arr["ord"] != $$var)
		{
			$this->arr["ord"] = $$var;
			$o = obj($this->id);
			$o->set_ord($$var);
			$o->save(); 
		}

		$var = $base."_subtype";
		$this->arr["subtype"] = $$var;
			
		$var = $base."_srow_grp";
		$this->arr["srow_grp"] = $$var;
		
		return true;
	}

	////
	// !generates the javascript code that checks whether elements are filled or not and if some that must be are not
	// it will not let you submit the form unless you turn off the javascript
	function gen_check_html()
	{
		$lang_id = aw_global_get("lang_id");
		if ($this->form->lang_id == $lang_id)
		{
			$mue = isset($this->arr["must_error"]) ? $this->arr["must_error"] : false;
		}
		else
		{
			$mue = isset($this->arr["lang_must_error"][$lang_id]) ? $this->arr["lang_must_error"][$lang_id] : false;
		}

		$mue = str_replace("\"","\\\"",$mue);
		
		if ($this->form->lang_id == $lang_id)
		{
			$cle = isset($this->arr["check_length_error"]) ? $this->arr["check_length_error"] : false;
		}
		else
		{
			$cle = isset($this->arr["check_length_error"][$lang_id]) ? $this->arr["check_length_error"][$lang_id] : false;
		}

		$cle = str_replace("\"","\\\"",$cle);

		$str = "";
		if ($this->arr["type"] == "textarea" && $this->arr["wysiwyg"] == 1)
		{
			$str .= "document.fm_".$this->form->id."._el_".$this->id.".value=_ifr_".$this->id.".document.body.innerHTML;\n";
		}

		if (($this->arr["type"] == "textbox" || $this->arr["type"] == "textarea") && isset($this->arr["must_fill"]) && $this->arr["must_fill"] == 1)
		{
			$str .= "for (i=0; i < document.fm_".$this->fid.".elements.length; i++) ";
			$str .= "{ if (document.fm_".$this->fid.".elements[i].name == \"";
			$str .=$this->id;
			$str .= "\" && document.fm_".$this->fid.".elements[i].value == \"\")";
			//return  $str."{ alert(\"".$mue."\");return false; }}\n";
			$str .= "{ alert(\"".$mue."\");return false; }}\n";
		}
		//else
		if (($this->arr["type"] == "textbox" || $this->arr["type"] == "textarea") && isset($this->arr["check_length"]) && $this->arr["check_length"] == 1)
		{
			$max_len = $this->arr["max_length"];
			$str .= "for (i=0; i < document.fm_".$this->fid.".elements.length; i++) ";
			$str .= "{ if (document.fm_".$this->fid.".elements[i].name == \"";
			$str .=$this->id;
			$str .= "\" && document.fm_".$this->fid.".elements[i].value.length > $max_len)";
			//return  $str."{ alert(\"".$mue."\");return false; }}\n";
			$str .= "{ alert(\"".$cle."\");return false; }}\n";
		}
		//else
		if ($this->arr["type"] == "listbox" && isset($this->arr["must_fill"]) && $this->arr["must_fill"] == 1)
		{
			$str .= "for (i=0; i < document.fm_".$this->fid.".elements.length; i++) ";
			$str .= "{ if (document.fm_".$this->fid.".elements[i].name == \"";
			$str .=$this->id;
			$str .= "\" && document.fm_".$this->fid.".elements[i].selectedIndex == 0)";
			//return  $str."{ alert(\"".$mue."\");return false; }}\n";
			$str .= "{ alert(\"".$mue."\");return false; }}\n";
		}

		return $str;
	}

	////
	// !seab vormielemendi sisu
	function set_content($args = array())
	{
		switch($this->arr["type"])
		{
			case "textbox":
				$this->arr["text"] = $args["content"];
				break;

			case "listbox":
				$this->arr["listbox_items"] = $args["content"];
				break;
		};
	}

	function get_lang_text($lid = -1)		
	{	
		if ($lid == -1)
		{
			$lid = aw_global_get("lang_id");
		}
		if ($this->form->lang_id == $lid)
		{
			return $this->arr["text"];
		}
		else
		{
			return $this->arr["lang_text"][$lid];
		}
	}

	function get_text()		
	{	
		return $this->arr["text"]; 
	}

	function get_ch_grp() 
	{ 
		return $this->arr["ch_grp"]; 
	}

	function get_ch_value() 
	{
		return $this->arr["ch_value"]; 
	}

	function get_el_name()		
	{	
		return $this->arr["name"]; 
	}

	function get_style()	
	{	
		return $this->arr["style"]; 
	}

	function get_type()		
	{
		return $this->arr["type"]; 
	}

	function get_subtype()		
	{	
		return isset($this->arr["subtype"]) ? $this->arr["subtype"] : ""; 
	}

	function get_srow_grp()		
	{	
		return isset($this->arr["srow_grp"]) ? $this->arr["srow_grp"] : ""; 
	}

	function get_id()			
	{ 
		return $this->id;	
	}

	function get_order()	
	{ 
		return $this->arr["ord"]; 
	}

	function get_props()  
	{ 
		return $this->arr; 
	}

	function get_row()		
	{ 
		return $this->row; 
	}

	function get_col()		
	{ 
		return $this->col; 
	}

	function get_el_group()		
	{
		return $this->arr["group"]; 
	}

	function get_related_form() 
	{ 
		return $this->arr["rel_form"]; 
	}

	function get_related_element() 
	{ 
		return $this->arr["rel_element"]; 
	}

	function is_translatable()
	{
		return $this->arr["is_translatable"];
	}

	function get_el_lb_items()	
	{
		// XYZ
		if ($this->arr["subtype"] == "relation" && $this->arr["rel_element"] && $this->arr["rel_form"])
		{
			$this->make_relation_listbox_content();
		}

		// I want the contents of the listbox for that kind of listboxes tooo -- duke
		if ($this->arr["lb_data_from_form"] && $this->arr["lb_data_from_el"])
		{
			$opts = array(
				"rel_form" => $this->arr["lb_data_from_form"],
				"rel_element" => $this->arr["lb_data_from_el"],
				"sort_by_alpha" => $this->arr["sort_by_alpha"],
				"rel_unique" => $this->arr["rel_unique"],
				"ret_ids" => true,
				"el_sort_by" => $this->arr["lb_data_from_el_sby"]
			);
			list($cnt,$vals) = $this->form->get_entries_for_element($opts);
			foreach($vals as $e_id => $e_val)
			{
				$this->arr["listbox_items"][$e_id] = $e_val;
			}
			$this->arr["listbox_count"] = $cnt;
		}

		$retval = is_array($this->arr["listbox_items"]) ? $this->arr["listbox_items"] : array();
		return $retval;
	} 

	function get_thousands_sep() 
	{ 
		return $this->arr["thousands_sep"]; 
	}

	// generic wrapper
	function get_prop($key)
	{
		return $this->arr[$key];
	}

	function get_show_controllers() 
	{ 
		if (isset($this->arr["show_controllers"]) && is_array($this->arr["show_controllers"]))
		{
			return $this->arr["show_controllers"];
		}
		return array();
	}

	function get_entry_controllers() 
	{ 
		if (is_array($this->arr["entry_controllers"]))
		{
			return $this->arr["entry_controllers"];
		}
		return array();
	}

	function get_lb_controllers() 
	{ 
		if (is_array($this->arr["lb_item_controllers"]))
		{
			return $this->arr["lb_item_controllers"];
		}
		return array();
	}

	function get_default_value_controller()
	{
		return $this->arr["default_controller"];
	}

	function get_value_controller()
	{
		return $this->arr["value_controller"];
	}

	////
	// !returns the name of table that the data from this element should be written to
	function get_save_table()
	{
		if ($this->form->arr["save_table"] == 1)
		{
			return $this->arr["table"][0]["table"];
		}
		else
		{
			return "form_".$this->form->id."_entries";
		}
	}

	////
	// !returns the name of column that the data from this element should be written to
	function get_save_col()
	{
		if ($this->form->arr["save_table"] == 1)
		{
			return $this->arr["table"][0]["col"];
		}
		else
		{
			return "ev_".$this->id;
		}
	}

	////
	// !returns the name of column that the data from this element should be written to - if it is formgen table
	// it returns el_ instead of ev_
	function get_save_col2()
	{
		if ($this->form->arr["save_table"] == 1)
		{
			return $this->arr["table"][0]["col"];
		}
		else
		{
			return "el_".$this->id;
		}
	}

	function get_metadata($lid = 0)
	{
		if (!$lid)
		{
			$lid = aw_global_get("lang_id");
		}
		$d_lid = $this->form->lang_id;
		return is_array($this->arr["metadata"][$lid]) ? $this->arr["metadata"][$lid] : (is_array($this->arr["metadata"][$d_lid]) ? $this->arr["metadata"][$d_lid] : array());
	}

	function get_up_down_count_el_form()
	{
		return $this->arr["up_down_count_el_form"];
	}

	function get_up_down_count_el_el()
	{
		return $this->arr["up_down_count_el_el"];
	}

	////
	// !saves the element properties that are on the grid editing page (name, text, grp)
	// $dat - POST vars
	function save_short($dat)
	{
		$var = "element_".$this->id."_text";
		if (isset($dat[$var]))
		{
			$this->arr["text"] = $dat[$var];
			$this->dequote($this->arr["text"]);
		};

		$var = "element_".$this->id."_grp";
		if ($this->arr["type"] == "checkbox")
		{
			$this->arr["ch_grp"] = $dat[$var];
		}
		else
		{
			$this->arr["group"] = $dat[$var];
		}

		$var = "element_".$this->id."_name";
		if ($dat[$var] != $this->arr["name"])
		{
			$this->arr["name"] = $dat[$var];
			$this->dequote($this->arr["name"]);
			$this->do_change_name($dat[$var]);
		}
	}

	function do_change_name($name,$id = -1)
	{
		if ($id == -1)
		{
			$id = $this->id;
		}
		$o = obj($id);
		$o->set_name($name);
		$o->save(); 
		// ok now here we must fuckin load all the forms that contain this element and fuckin change all elements names in those. 
		// shit I hate this but I suppose it's gotta be done
		$this->save_handle();
		$this->db_query("SELECT * FROM element2form WHERE el_id = ".$id);
		while ($drow = $this->db_next())
		{
			$fup = get_instance(CL_FORM);
			$fup->load($drow["form_id"]);
			for ($row = 0;$row < $fup->arr["rows"]; $row++)
			{
				for ($col = 0; $col < $fup->arr["cols"]; $col++)
				{
					if (is_array($fup->arr["elements"][$row][$col]))
					{
						foreach($fup->arr["elements"][$row][$col] as $k => $v)
						{
							if ($k == $id)
							{
								$fup->arr["elements"][$row][$col][$k]["name"] = $name;
							}
						}
					}
				}
			}
			$fup->save();
		}
		$this->restore_handle();
	}

	function do_change_type_name($name)
	{
		$this->db_query("UPDATE form_elements SET type_name = '$name' WHERE id = ".$this->id);

		// ok now here we must fuckin load all the forms that contain this element and fuckin change all elements typenames in those. 
		// shit I hate this but I suppose it's gotta be done
		$this->save_handle();
		$this->db_query("SELECT * FROM element2form WHERE el_id = ".$this->id);
		while ($drow = $this->db_next())
		{
			$fup = get_instance(CL_FORM);
			$fup->load($drow["form_id"]);
			for ($row = 0;$row < $fup->arr["rows"]; $row++)
			{
				for ($col = 0; $col < $fup->arr["cols"]; $col++)
				{
					if (is_array($fup->arr["elements"][$row][$col]))
					{
						foreach($fup->arr["elements"][$row][$col] as $k => $v)
						{
							if ($k == $this->id)
							{
								$fup->arr["elements"][$row][$col][$k]["type_name"] = $name;
							}
						}
					}
				}
			}
			$fup->save();
		}
		$this->restore_handle();
	}

	////
	// this function deletes the element from this form only
	function del()
	{
		// if this is a relation element, remove it from the list of relations
		if ($this->arr["rel_table_id"])
		{
			$this->db_query("DELETE FROM form_relations WHERE id = '".$this->arr["rel_table_id"]."'");
		}
		$this->form->del_element_cols($this->fid, $this->id);
	}

	function gen_action_html()
	{
		$this->read_template("admin_element_actions.tpl");
		$this->vars(array("element_id" => "element_".$this->id, "email" => $this->arr["email"], "element_text" => $this->arr["text"]));
		return $this->parse();
	}

	function set_style($id)
	{
		$this->arr["style"] = $id;
	}

	function set_entry(&$arr, $e_id)
	{
		$this->entry = $arr[$this->id];
		$this->entry_id = $e_id;
		if ($this->arr["type"] == "file" || $this->arr["type"] == "link")
		{
			$tmp = aw_unserialize($this->entry);
			if ($tmp !== false)
			{
				$this->entry = $tmp;
			}
		}
	}

	function change_pos($arr,&$f)
	{
		$this->read_template("change_pos.tpl");
		$obj = obj($this->id);
		if (!(is_array($f->arr["el_menus"]) && count($f->arr["el_menus"]) > 0))
		{
			$mlist = $this->get_menu_list();
		}
		else
		{
			$tlist = $this->get_menu_list();
			foreach($f->arr["el_menus"] as $menuid)
			{
				$mlist[$menuid] = $tlist[$menuid];
			}
		}

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_chpos", array("id" => $this->fid, "col" => $this->col, "row" => $this->row, "el_id" => $this->id), "form"),
			"folders"	=> $this->picker($obj->parent(), $mlist),
			"name"		=> $this->arr["name"]
		));

		for ($col=0; $col < $f->arr["cols"]; $col++)
		{
			$this->vars(array(
				"col" => $col+1
			));
			$cc.=$this->parse("COLNUMC");
			$cc2.=$this->parse("COLNUM");
		}
		$this->vars(array(
			"COLNUMC" => $cc,
			"COLNUM" => $cc2
		));
		for ($row = 0; $row < $f->arr["rows"]; $row++)
		{
			$c = "";
			$cc="";
			for ($col = 0; $col < $f->arr["cols"]; $col++)
			{
				$this->vars(array(
					"row" => $row, 
					"col" => $col, 
					"checked" => checked($this->col == $col && $this->row == $row),
					"cnt" => $cnt++,
					"drow" => $row+1
				));
				$c.=$this->parse("COL");
				$cc.=$this->parse("COLC");
			}
			$this->vars(array("COL" => $c,"COLC" => $cc));
			$l.=$this->parse("ROW");
			$cl.=$this->parse("ROWC");
		}
		$this->vars(array("ROW" => $l,"ROWC" => $cl));
		return $this->parse();
	}

	function _date_ord_cmp($a,$b)
	{
		if ($a == $b)
		{
			return 0;
		}
		return ($a < $b) ? -1 : 1;
	}

	function do_core_userhtml($prefix,$elvalues,$no_submit, $element_name = false, $udcnt_values = false)
	{
		// check if this element is supposed to be shown right now
		$show = true;
		if (!isset($this->arr["act_from"]))
		{
			$show = true;
		}
		else
		{
			if ($this->arr["act_from"] > (24*3600*400) && time() < $this->arr["act_from"] && $this->arr["has_act"] == 1)
			{
				$show = false;
			}
			if ($this->arr["act_to"] > (24*3600*400) && time() > $this->arr["act_to"] && $this->arr["has_act"] == 1)
			{
				$show = false;
			}
		}
		if (!$show)
		{
			return "";
		}

		$elid = $this->id;

		if ($element_name === false)
		{
			$element_name = $prefix.$elid;
		}

		$html="";
		if (isset($this->form->controller_errors[$this->id]) && 
			is_array($this->form->controller_errors[$this->id]) && 
			count($this->form->controller_errors[$this->id]) > 0)
		{
			$html.=join("", $this->form->controller_errors[$this->id]);
		}

		$lang_id = aw_global_get("lang_id");
		if ($this->form->lang_id == $lang_id)
		{
			$text = isset($this->arr["text"]) ? $this->arr["text"] : false;
			$info = isset($this->arr["info"]) ? $this->arr["info"] : false; 
		}
		else
		{
			$text = isset($this->arr["lang_text"][$lang_id]) ? $this->arr["lang_text"][$lang_id] : false;
			$info = isset($this->arr["lang_info"][$lang_id]) ? $this->arr["lang_info"][$lang_id] : false; 
		}

		$ext = false;

		$stat_check = "";


		if (aw_global_get("fg_check_status"))
		{
			$stat_check = " onChange='set_changed()' ";
		};

		if (!isset($this->arr["disabled"]))
		{
			$disabled = "";
		}
		else
		{
			$disabled = ($this->arr["disabled"] == 1 ? " disabled " : "");
		}

		if ($disabled)
		{
			$html.="<input type='hidden' name='".$element_name."' value='".$this->get_val($elvalues)."' />";
		}

		$css = "";
		if ($this->arr["el_css_style"])
		{
			$css = "class=\"st".$this->arr["el_css_style"]."\"";
			$el_css_class = "st".$this->arr["el_css_style"];
			classload("layout/active_page_data");
			active_page_data::add_site_css_style($this->arr["el_css_style"]);
		}

		if ($this->arr["el_tabindex"])
		{
			if ($css != "")
			{
				$css .= " ";
			}
			$css .= "tabindex=\"".($this->arr["el_tabindex"]+$this->form->base_tabindex)."\"";
		}
		switch($this->arr["type"])
		{
			case "textarea":
				// only IE supports wysiwyg editor
				if ($this->arr['hidden'])
				{
					$html .= html::hidden(array(
						'name' => $element_name,
						'value' => $this->get_val($elvalues)
					));
				}
				else
				if (($this->arr["wysiwyg"] == 1) && ($this->is_ie))
				{
					$html.="<input type=\"hidden\" name=\"_el_".$element_name."\" value=\"".htmlspecialchars($this->get_val($elvalues))."\" />";
					$html.="<iframe name=\"_ifr_".$element_name."\" onFocus=\"sel_el='_el_".$element_name."'\" frameborder=\"1\" width=\"".($this->arr["ta_cols"]*10)."\" height=\"".($this->arr["ta_rows"]*10)."\"></iframe>\n";
					$html.="<script for=window event=onload>\n";
					$html.="_ifr_".$element_name.".document.designMode='On';\n";
					$html.="_ifr_".$element_name.".document.write(\"<body style='font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;background-color: #FFFFFF; border: #CCCCCC solid; border-width: 1px 1px 1px 1px; margin-left: 0px;padding-left: 3px;	padding-top: 0px;	padding-right: 3px; padding-bottom: 0px;'>\");\n";
					$html.="_ifr_".$element_name.".document.write(document.fm_".$this->form->id."._el_".$element_name.".value);\n";
					$html.="</script>\n";
				}
				else
				{
					$html.="<textarea $disabled $stat_check NAME='".$element_name."' COLS='".$this->arr["ta_cols"]."' ROWS='".$this->arr["ta_rows"]. "' $css>";
					$html .= htmlspecialchars($this->get_val($elvalues));
					$html .= "</textarea>";
				}
				break;

			case "radiobutton":
				$ch = ($this->entry_id ? checked($this->entry == $this->id) : checked($this->arr["default"] == 1));
				$html .="<input type='radio' $disabled $stat_check NAME='".$prefix."radio_group_".$this->arr["group"]."' VALUE='".$this->id."' $ch $css/>";
				break;

			case "listbox":
				// kui seoseelement siis feigime sisu
				$rel = $this->_init_listbox_content();

				if ($this->arr["value_controller"] && $this->form->arr["has_controllers"])
				{
					$this->form->controller_instance->eval_controller($this->arr["value_controller"], $this->entry, &$this->form, &$this);
				}

				if (!$this->arr['hidden'] && !$this->arr["show_as_text"])
				{
					$sos = "";
					if ($this->arr["onChange"] != "")
					{
							$sos = "onChange=\"".$this->arr["onChange"]."\"";
					}
					else
					if ($this->arr["submit_on_select"])
					{
						// for search forms we must not submit the form, but instead set element value in url
						if ($this->form->type == FTYPE_SEARCH)
						{
							if (strpos(aw_global_get("REQUEST_URI"), "?") === false)
							{
								$_sep = "?";
							}
							else
							{
								$_sep = "&";
							}
							$sos = "onChange=\"window.location = window.location + '".$_sep."elvalues[".$this->get_el_name()."]=' + this.options[this.selectedIndex].value\"";
						}
						else
						{
							$sos = "onChange=\"fm_".$this->form->id.".submit()\"";
						}
					}
			
					$html .="<select $css $disabled $sos $stat_check name='".$element_name."'";
					if ($this->arr["lb_size"] > 1)
					{
						$html.=" size=\"".$this->arr["lb_size"]."\"";
					}
					$html.=">";
				}
				$cnt = $this->arr["listbox_count"];

				if ($lang_id != $this->form->lang_id && !$rel)
				{
					$larr = $this->arr["listbox_lang_items"][$lang_id];
				}
				else
				{
					$larr = $this->arr["listbox_items"];
				}

				$lb_opts = "";

				if ($this->entry != "")
				{
					// in case the element id is incorrect in the entry, this can happen with relation elements
					list($__1,$__2,$__3,$__def) = explode("_",$this->entry);
					$_lbsel = "element_".$this->id."_lbopt_".$__def;
				}
				else
				{
					if (isset($elvalues[$this->get_el_name()]))
					{
						list($__1,$__2,$__3,$__def) = explode("_",$elvalues[$this->get_el_name()]);
						$_lbsel = "element_".$this->id."_lbopt_".$__def;
					}
					else
					{
						if ($this->form->arr["has_controllers"] && $this->arr["default_controller"])
						{
							$_lbsel = $this->form->controller_instance->eval_controller($this->arr["default_controller"], "", &$this->form, $this);
						}
						else
						{
							$_lbsel = "element_".$this->id."_lbopt_".$this->arr["listbox_default"];
						}
					}
				}

				if ($this->arr["show_as_text"])
				{
					if (is_array($larr))
					{
						foreach($larr as $b => $value)
						{
							$_v = "element_".$this->id."_lbopt_".$b;
							if ($_v == $_lbsel)
							{
								$html .= $value;
								if (!$this->arr["no_hidden_el"])
								{
									$html .= html::hidden(array(
										"name" => $element_name,
										"value" => $_v
									));
								}
							}
						}
					}
				}
				else
				{
				if (is_array($larr))
				{
					foreach($larr as $b => $value)
					{
						$_v = "element_".$this->id."_lbopt_".$b;

						$lbsel = ($_lbsel == $_v ? " SELECTED " : "");

						if (is_array($larr))
						{
	//						list($key,$value) = each($larr);
							$key = $b;


							// now check all listbox item controllers for this lb item and if any of them fail, don't show item
							$controllers_ok = true;
							if (isset($this->arr["lb_item_controllers"]) && is_array($this->arr["lb_item_controllers"]))
							{
								foreach($this->arr["lb_item_controllers"] as $ctrlid)
								{
									if (($res = $this->form->controller_instance->do_check($ctrlid, $value, &$this->form, $this)) !== true)
									{
										$controllers_ok = false;
									}
								}
							}

							if ($controllers_ok)
							{
								if ($ext)
								{
									$lb_opts .= "<option $lbsel value='$key'>$value</option>\n";
									if ($this->arr['hidden'] && $_lbsel == $_v)
									{
										$html .= html::hidden(array(
											'name' => $element_name,
											'value' => $key
										));
									}
								}
								else
								{
									// teeb pisikest trikka - kui on otsinguform ja me n2itame parajasti viimast elementi - see on automaagiliselt
									// lisatud tyhi element, siis topime selle hoopis k6ige esimeseks a numbri j2tame samax. voh.
									if (($this->form->type == FTYPE_SEARCH || $this->form->type == FTYPE_FILTER_SEARCH )&& $b == ($cnt-1))
									{
										$lb_opts ="<option $lbsel VALUE='element_".$this->id."_lbopt_".$b."'>".$value.$lb_opts."</option>\n";
									}
									else
									{
										$lb_opts.="<option $lbsel VALUE='element_".$this->id."_lbopt_".$b."'>".$value."</option>\n";
									}
									if ((isset($this->arr['hidden']) && $this->arr['hidden']) && $_lbsel == $_v)
									{
										$html .= html::hidden(array(
											'name' => $element_name,
											'value' => "element_".$this->id."_lbopt_".$b
										));
									}
								}
							}
						}
					}
				}
				if (!$this->arr['hidden'])
				{
					$html.=$lb_opts."</select>\n";
				}
				}
				break;

			case "multiple":
				$this->_do_init_multiple_items();
				$html.="<select $css $disabled $stat_check NAME='".$element_name."[]' MULTIPLE";
				if ($this->arr["mb_size"] > 1)
				{
					$html.=" size=\"".$this->arr["mb_size"]."\"";
				}
				$html.=">\n";

				if ($this->entry_id)
				{
					$ear = explode(",",$this->entry);
				}

				if ($lang_id != $this->form->lang_id)
				{
					$larr = $this->arr["multiple_lang_items"][$lang_id];
				}
				else
				{
					$larr = $this->arr["multiple_items"];
				}

				$_larr = new aw_array($larr);
				foreach($_larr->get() as $b => $itval)
				{
					$sel = false;
					if ($this->entry_id)
					{
						reset($ear);
						while (list(,$v) = each($ear))
						{
							if ((string)$v === (string)$b)
							{
								$sel = true;
							}
						}
					}
					else
					{
						$sel = ($this->arr["multiple_defaults"][$b] == 1 ? true : false);
					}

					// now check all multiple item controllers for this item and if any of them fail, don't show item
					$controllers_ok = true;
					if (is_array($this->arr["lb_item_controllers"]))
					{
						foreach($this->arr["lb_item_controllers"] as $ctrlid)
						{
							if (($res = $this->form->controller_instance->do_check($ctrlid, $itval, &$this->form, $this)) !== true)
							{
								$controllers_ok = false;
							}
						}
					}

					if ($controllers_ok)
					{
						$html.="<option ".selected($sel == true)." VALUE='$b'>".$itval ."</option>";
					}
				}
				$html.="</select>\n";
				break;

			case "checkbox":
				if ($this->arr["hidden"])
				{
					$sel = ($this->entry_id ? $this->entry  : $this->arr["default"]);
					$html .= "<input type=\"hidden\" name=\"$element_name\" value=\"$sel\" />";
				}
				else
				{
					$sel = ($this->entry_id ? checked($this->entry == 1) : checked($this->arr["default"] == 1));
					$html .= "<input $css $disabled $stat_check type='checkbox' NAME='".$element_name."' VALUE='1' $sel />\n";
				}
				break;

			case "textbox":
				$l = $this->arr["length"] ? "SIZE='".$this->arr["length"]."'" : "";
				$tb_type = "text";
				if ($this->arr["subtype"] == "password")
				{
					$tb_type = "password";
				}

				$tb_val = $this->get_val($elvalues);

				if ($this->arr["subtype"] == "int")
				{
					$cursums = aw_global_get("fg_element_sums");
					$cursums[$this->id] += $tb_val;
					aw_global_set("fg_element_sums", $cursums);
				}

				$js_flopper = "";
				if ($this->arr["js_flopper"])
				{
					$js_flopper = "onFocus=\"if(this.value=='".$this->arr["js_flopper_value"]."') this.value = ''\" onblur=\"if(this.value=='') this.value='".$this->arr["js_flopper_value"]."';\"";
				}

				$aft = "";
				if ($this->arr["subtype"] == "int" && $this->arr["up_down_button"])
				{
					$udcnt = $this->arr["up_down_count"];
					if ($this->arr["up_down_count_el_form"] && $this->arr["up_down_count_el_el"])
					{
						// now figure out the damn value. but how the hell do we do that??!!
						// damn, this is not good, but I see no other way.
						// the data where the value for the element should be, gets passed as $udcnt_values
						$udcnt = (int)$udcnt_values["ev_".$this->arr["up_down_count_el_el"]];
					}
					$onc = "fg_increment(\"".$this->form->get_form_html_name()."\",\"".$element_name."\",".$udcnt.");return false";
					if ($this->arr["up_button_use_img"] && $this->arr["up_button_img"]["id"])
					{
						$aft = "<input type='image' src='".image::check_url($this->arr["up_button_img"]["url"])."' onClick='$onc'>";
					}
					else
					{
						$aft = "<input type='button' onClick='$onc' value='+'>";
					}

					$onc = "fg_increment(\"".$this->form->get_form_html_name()."\",\"".$element_name."\",-".$udcnt.");return false";
					if ($this->arr["down_button_use_img"] && $this->arr["down_button_img"]["id"])
					{
						$aft .= "<input type='image' src='".image::check_url($this->arr["down_button_img"]["url"])."' onClick='$onc'>";
					}
					else
					{
						$aft .= "<input type='button' onClick='$onc' value='-'>";
					}
				}
				if ($this->arr['hidden'])
				{
					$html .= html::hidden(array(
						'name' => $element_name,
						'value' => htmlspecialchars($tb_val)
					));
				}
				else
				if ($this->arr["show_as_text"])
				{
					$html .= $tb_val;
					if (!$this->arr["no_hidden_el"])
					{
						$html .= html::hidden(array(
							'name' => $element_name,
							'value' => htmlspecialchars($tb_val)
						));
					}
				}
				else
				{
					if (trim($tb_val) == "" && $this->arr["js_flopper"])
					{
						$tb_val = $this->arr["js_flopper_value"];
					}
					$html .= "<input $js_flopper $css $disabled $stat_check type='$tb_type' NAME='".$element_name."' $l VALUE=\"".(htmlspecialchars($tb_val))."\" />$aft\n";
				}
				break;


			case "price":
				$l = $this->arr["length"] ? "SIZE='".$this->arr["length"]."'" : "";
				$html .= "<input $css $disabled $stat_check type='text' NAME='".$element_name."' $l VALUE=\"".(htmlspecialchars(round($this->get_val($elvalues),2)))."\" />\n";
				break;

			case "alias":
				// igal entryle on võimalik sisestada oma alias
				if ( $this->arr["alias_type"] == 1)
				{
					$o = obj($this->arr["id"]);
					$conn = $o->connections_from();
					reset($conn);
					list(,$c) = each($conn);

					$def = $defs[$this->arr["alias_subtype"]];

					if ($c->prop("to.class_id") != $def["class_id"])
					{
						$link = $def["addlink"];
						$caption = "Lisa objekt ($def[title])";
					}
					else
					{
						$link = $def["chlink"];
						$link .= "&id=" . $c->prop("to");
						$caption = "Muuda objekti ($def[title])";
					};

					$window = "window.open('$link','edit','toolbar=no,location=no,directories=no,menubar=no,width=800,height=500')";
					$html .= "<a href=\"#\" onClick=\"$window\">$caption</a>";
				}
				elseif ($this->arr["alias"] > 0)
				{
					$obj = get_instance("core/objects");
					// just show the aliased object
					// yeah!
					// I really hope that the thing we pass there is a reference to the form data
					$html .= $obj->show(array("id" => $this->arr["alias"],"form" => $this->form, "caption" => $this->arr["text"]));
				};
				break;

			case "timeslice":
				$values = aw_unserialize($this->get_val($elvalues));
				$html = sprintf("<input type='text' name='%s_count' size='3' maxlength='3' value='%d' />\n",$element_name,$values["count"]);
				$html .= sprintf("<select name='%s_type'>%s</select>\n",$element_name,$this->picker($values["type"],$this->timeslice_types));
				break;

			case "button":
			case "submit":
			case "reset":
				$csscl = ($this->arr["button_css_class"] != "" ? "class=\"".$this->arr["button_css_class"]."\"" : "");
				if ($csscl != "")
				{
					$css = $csscl;
				}
				// useful if we we are generating a preview of a form and don't want to let the user
				// to submit the form
				if (!$no_submit)
				{
					$onclick = "";
					if ($this->onclick != "")
					{
						$onclick = $this->onclick;
					}

					if ($lang_id == $this->form->lang_id)
					{
						$butt = $this->arr["button_text"];
					}
					else
					{
						$butt = $this->arr["lang_button_text"][$lang_id];
					}

					get_instance(CL_IMAGE);

					if ($this->arr["button_img"]["use"] == 1)
					{
						$btype = "image";
						$bsrc  = "src=\"".image::check_url($this->arr["button_img"]["url"])."\" alt=\"$butt\" border=\"0\"";
					}
					else
					{
						$btype = "submit";
						$bsrc = "";
					}
					if ($this->arr["subtype"] == "submit" || $this->arr["type"] == "submit" || $this->arr["subtype"] == "confirm")
					{
						$bname = sprintf("name='submit[%d]'",$this->id);
						if ($onclick == "")
						{
							$onclick =  "return check_submit();";
						}
						$html .= "<input $css $disabled $bname type='$btype' $bsrc VALUE='".$butt."' onClick=\"$onclick\" />\n";
					}
					else
					if ($this->arr["subtype"] == "reset" || $this->arr["type"] == "reset")
					{
						if ($onclick == "")
						{
							$onclick =  "form_reset(); return false;";
						}
						if ($btype == "image")
						{
							$html .= "<input $css $disabled type='image' $bsrc onClick=\"$onclick\" />\n";
						}
						else
						{
							$html .= "<input $css $disabled type='reset' VALUE='".$butt."' />\n";
						};
					}
					else
					if ($this->arr["subtype"] == "url")
					{
						if ($onclick == "")
						{
							if ($this->arr["button_js_next_form_in_chain"] == 1 && aw_global_get("chain_next_form_url") != "")
							{
								$onclick =  "window.location='".aw_global_get("chain_next_form_url")."';return false;";
							}
							else
							{
								if (isset($this->arr["lang_button_url"][aw_global_get("lang_id")]))
								{
									$buu = $this->arr["lang_button_url"][aw_global_get("lang_id")];
								}
								else
								{
									$buu = $this->arr["button_url"];
								}
								$onclick =  "window.location='".$buu."';return false;";
							}
						}

						if ($this->arr["bt_redir_after_submit"])
						{
							$html .= "<input $css $disabled type='$btype' $bsrc NAME='bt_url_".$this->id."' VALUE='".$butt."' />\n";
						}
						else
						{
							$html .= "<input $css $disabled type='$btype' $bsrc VALUE='".$butt."' onClick=\"$onclick\" />\n";
						}
					}
					else
					if ($this->arr["subtype"] == "order")
					{
						$loc = $this->mk_my_orb("show", array("id" => $this->arr["order_form"], "load_entry_data" => $this->form->entry_id,"section" => $GLOBALS["section"]),"form");
						if ($onclick == "")
						{
							$onclick = "window.location='".$loc."';return false;";
						}
						$html .= "<input $css $disabled type='$btype' $bsrc VALUE='".$butt."' onClick=\"$onclick\" />\n";
					}
					else
					if ($this->arr["subtype"] == "close")
					{
						if ($onclick == "")
						{
							$onclick = "window.close();return false;";
						}
						$html .= "<input $css $disabled type='$btype' $bsrc VALUE='".$butt."' onClick=\"$onclick\" />\n";
					}
				}
				break;

			case "file":
				if ($this->entry_id)
				{
					$html.=$this->get_value();
				}
				if (isset($this->arr["file_delete_link_text"][aw_global_get("lang_id")]))
				{
					$dlk = html::href(array(
						"url" => aw_url_change_var("del_image", $this->id),
						"caption" => $this->arr["file_delete_link_text"][aw_global_get("lang_id")]
					));
					$html .= $dlk."<br>";
				}
				$csscl = ($this->arr["button_css_class"] != "" ? "class=\"".$this->arr["button_css_class"]."\"" : "");
				$html .= "<input type='file' $disabled $stat_check NAME='".$element_name."' value='' $csscl/>\n";
				break;

			case "link":
				if ($this->arr["subtype"] == "show_calendar")
				{
					$_text = $this->arr["link_text"];
					if ($this->arr["clink_target"] == "form_chain")
					{
						$cal_id = aw_global_get("current_chain");
						$ctrl = aw_global_get("current_chain_entry");
					}
					else
					{
						$cal_id = $this->form->id;
						$ctrl = $this->entry_id;
					};
					$orb = !($this->arr["clink_no_orb"]);
					if ($cal_id)
					{
						$_link = $this->mk_my_orb("view",array("id" => $cal_id,"ctrl" => $ctrl),"planner",0,$orb);
					}
					else
					{
						$_link = "#";
					};
					if ($ctrl)
					{
						$html .= "<a target='_blank' href='$_link'>$_text</a>";
					};

				}
				else
				if ($this->arr["subtype"] != "show_op")
				{
					$html.="<table border=0><tr><td align=right>".$this->arr["link_text"]."</td><td><input type='text' NAME='".$element_name."_text' VALUE='".($this->entry_id ? $this->entry["text"] : "")."' /></td></tr>";
					$html.="<tr><td align=right>".$this->arr["link_address"]."</td><td><input type='text' NAME='".$element_name."_address' VALUE='".($this->entry_id ? $this->entry["address"] : "")."' /></td></tr></table>";
					$html.="<a onClick=\"e_".$this->fid."_elname='".$element_name."_text';e_".$this->fid."_elname2='".$element_name."_address';\" href=\"javascript:remote('no',500,400,'".$this->mk_orb("search_doc", array(),"links")."')\">Vali dokument</a>";
				}
				break;

			case "date":
				load_vcl("date_edit");
				$de = new date_edit(time());
				if ($el_css_class)
				{
					$de->set("classid", $el_css_class);
				}
				$bits = array();
				$has_some = false;
				if ($this->arr["has_year"])
				{
					$bits["year"] = $this->arr["year_ord"];
					$has_some = true;
				}
				if ($this->arr["has_month"])
				{
					$bits["month"] = $this->arr["month_ord"];
					$has_some = true;
				}
				if ($this->arr["has_day"])
				{
					$bits["day"] = $this->arr["day_ord"];
					$has_some = true;
				}
				if ($this->arr["has_hr"])
				{
					$bits["hour"] = $this->arr["hour_ord"];
					$has_some = true;
				}
				if ($this->arr["has_minute"])
				{
					$bits["minute"] = $this->arr["minute_ord"];
					$has_some = true;
				}
				if ($this->arr["has_second"])
				{
					$bits["second"] = $this->arr["second_ord"];
					$has_some = true;
				}
				else
				{
					load_vcl("date_edit");
					$de = new date_edit(time());
					if ($el_css_class)
					{
						$de->set("classid", $el_css_class);
					}

					$bits = array();
					$has_some = false;
					if ($this->arr["has_year"])
					{
						$key = ($this->arr["year_textbox"]) ? "year_textbox" : "year";
						$bits[$key] = $this->arr["year_ord"];
						$has_some = true;
					}
					if ($this->arr["has_month"])
					{
						$key = ($this->arr["month_textbox"]) ? "month_textbox" : "month";
						$bits[$key] = $this->arr["month_ord"];
						$has_some = true;
					}
					if ($this->arr["has_day"])
					{
						$key = ($this->arr["day_textbox"]) ? "day_textbox" : "day";
						$bits[$key] = $this->arr["day_ord"];
						$has_some = true;
					}
					if ($this->arr["has_hr"])
					{
						$key = ($this->arr["hr_textbox"]) ? "hour_textbox" : "hour";
						$bits[$key] = $this->arr["hour_ord"];
						$has_some = true;
					}
					if ($this->arr["has_minute"])
					{
						$key = ($this->arr["minute_textbox"]) ? "minute_textbox" : "minute";
						$bits[$key] = $this->arr["minute_ord"];
						$has_some = true;
					}
					if ($this->arr["has_second"])
					{
						$bits["second"] = $this->arr["second_ord"];
						$has_some = true;
					}
				};

				uasort($bits,array($this,"_date_ord_cmp"));


				if ($has_some)
				{
					$de->configure($bits);
				}
				else
				{
					$de->configure(array(
						"year" => "",
						"month" => "",
						"day" => ""
					));
				}
				$fy = $this->arr["from_year"];
				$ty = $this->arr["to_year"];
				if ($this->arr["def_date_type"] == "now")
				{
					$def = time() + ($this->arr["def_date_num"] * $this->arr["def_date_add"]);
				}
				else
				if ($this->arr["def_date_type"] == "none")
				{
					$def = -1;
				}
				else
				{
					$def = time();
				}
//				echo "aentry_id = $this->entry_id , $this->entry <br />";
				$vl = $this->get_val($elvalues);
				if ($this->arr["hidden"])
				{
					$html .= "<input type=\"hidden\" name=\"$element_name\" value=\"".($vl ? $vl : $def)."\" />";
				}
				else
				{
					$html .= $de->gen_edit_form($element_name, ($vl ? $vl : $def),($fy ? $fy : 2000),($ty ? $ty : 2005),true);
				}
				break;
		};

		if ($this->arr['hidden'])
		{
			$text = "";
			$info = "";
		}

		$sep_ver = "";
		$sep_hor = "";
		if ($this->arr["type"] != "")
		{
			$sep_ver = ($this->arr["text_distance"] > 0 ? "<br /><img src='/images/transa.gif' width='1' height='".$this->arr["text_distance"]."' border='0' /><br />" : "<br />");
			$sep_hor = ($this->arr["text_distance"] > 0 ? "<img src='/images/transa.gif' height='1' width='".$this->arr["text_distance"]."' border='0' />" : "");
		}

		if (!isset($this->arr["text_pos"]))
		{
			$html = $text.$sep_hor.$html;		// default is on left of element
		}
		else
		{
			if ($this->arr["text_pos"] == "up")
			{
				$html = $text.$sep_ver.$html;
			}
			else
			if ($this->arr["text_pos"] == "down")
			{
				$html = $html.$sep_ver.$text;
			}
			else
			if ($this->arr["text_pos"] == "right")
			{
				$html = $html.$sep_hor.$text;
			}
			else
			{
				$html = $text.$sep_hor.$html;		// default is on left of element
			}
		}
		if (isset($this->arr["info"]) && $this->arr["info"] != "")
		{
			$html .= "<br /><font face='arial, geneva, helvetica' size='1'>&nbsp;&nbsp;$info</font>";
		}

		if ($this->arr["sep_type"] == 1)	// reavahetus
		{
			$html.="<br />";
		}
		else
		if (isset($this->arr["sep_pixels"]) && $this->arr["sep_pixels"] > 0)
		{
			$html.="<img src='/images/transa.gif' width=".$this->arr["sep_pixels"]." height=1 border=0 />";
		}
		return $html;
	}

	////
	// tagastab mingi elemendi väärtuse - formgen internal representation (el_xxx)
	function get_val($elvalues = array(), $do_val_ctrl = false)
	{
		$lang_id = aw_global_get("lang_id");

		// if value controiller is set, always use that
		if ($this->arr["value_controller"] && (!$this->form->arr["sql_writer_writer"] || $do_val_ctrl)) 
		{
//			echo "entry = $this->entry <br />";
			$val = $this->form->controller_instance->eval_controller($this->arr["value_controller"], $this->entry, &$this->form, &$this);
		}
		else
		// kui entry on laetud, siis voetakse see sealt.
		if ($this->entry_id)
		{
			$val = $this->entry;
		}
		else
		// vastasel korral, kui $elvalues midagi sisaldab, siis saame info sealt
		if (isset($elvalues[$this->arr["name"]]) && $elvalues[$this->arr["name"]] != "")
		{
			$val = $elvalues[$this->arr["name"]];
		}
		else
		// if a default value controller is specified, then get the value from that
		if ($this->arr["default_controller"])
		{
			$val = $this->form->controller_instance->eval_controller($this->arr["default_controller"], "", &$this->form, $this);
		}
		// finally, if nothing else succeeded, we will just use the default.
		else
		{
			if ($lang_id != $this->form->lang_id)
			{
				$val = $this->arr["lang_default"][$lang_id];
			}
			else
			{
				$val = $this->arr["default"];
			}
		}
		return $val;
	}

	function core_process_entry(&$entry, $id,$prefix = "")
	{
		//// This is called for every single element in the form.
		// $this->form->post_vars contains $HTTP_POST_VARS.
		if ($this->arr["type"] == 'link')
		{
			$var = $this->form->post_vars[$prefix.$this->id."_text"];
			$var2= $this->form->post_vars[$prefix.$this->id."_address"];
			$var = array("text" => $var, "address" => $var2);
		}
		else
		if ($this->arr["type"] == 'file')
		{
			// gotcha, siis handletakse piltide uploadi
			$var = $prefix.$this->id;
			$img = get_instance(CL_FILE);
			$var = $img->add_upload_image($var,$this->form->entry_parent,$this->entry["id"]);
		}
		else
		if ($this->arr["type"] == "radiobutton")
		{
			$var = $this->form->post_vars[$prefix."radio_group_".$this->arr["group"]];
		}
		else
		//if ($this->arr["type"] == "button" && $this->arr["subtype"] == "confirm")
		//{
			// confirm button moves the entry to another folder

			// but we don't use a prefix when creating a confirm button,
			// which means we can just ignore it (prefix) here.
		//	if (isset($GLOBALS[$prefix."confirm"]))
		//	{
				// just set the entry parent to the correct value, the object will actually be updated a bit later
		//		$this->form->entry_parent = $this->arr["confirm_moveto"];
		//	}
		//}
		//else

//// oh no you don't! this broke saving form entries that had relation listboxes in them!
//		if ( ($this->arr["type"] == "listbox") && ($this->arr["subtype"] == "relation") )
//		{
			//print "Updating relation!<br />";
//		}
//		else
		if ($this->arr["type"] == "button")
		{
			if ($this->arr["subtype"] == "url" && $this->arr["bt_redir_after_submit"])
			{
				if ($this->form->post_vars["bt_url_".$this->id] != "" || $this->form->post_vars["bt_url_".$this->id."_x"] != "")
				{
					$buu = $this->arr["button_url"];
					if (isset($this->arr["lang_button_url"][aw_global_get("lang_id")]))
					{
						$buu = $this->arr["lang_button_url"][aw_global_get("lang_id")];
					}
					aw_session_set("form_redir_after_submit_".$this->form->id, $buu);
					if (!aw_ini_get("fg.no_set_use_eid_once"))
					{
						$this->form->set_use_eid_once = true;
					}
					$this->form->go_to_after_submit = $buu;
				}
			}
			else
			{
				$s_arr = $this->form->post_vars["submit"];
				$clicked = false;
				if (is_array($s_arr))
				{
					reset($s_arr);
					list($skey,) = each($s_arr);
					if ($skey == $this->id)
					{
						$clicked = true;
					};
				};

				if ($clicked)
				{
					// pass a reference to this elements "arr" back to the form
					$this->form->set_opt("el_submit",&$this->arr);

					if ($this->arr["subtype"] == "confirm")
					{
						$this->form->set_opt("entry_parent",$this->arr["confirm_moveto"]);
					}
				}
			}
		}
		else
		if ($this->arr["type"] == "multiple")
		{
			$var = $this->form->post_vars[$prefix.$this->id];
			if (is_array($var))
			{
				$var = join(",",$var);
			}
			else
			{
				$var = "";
			}
		}
		else
		if ($this->arr["type"] == "date")
		{
			// subtle trickery here. since if this is a related date element it must get its value from the other element
			// we just use that elements variable for that
			$d_id = $this->id;
			if ($this->arr["def_date_type"] == "rel")
			{
				$d_id = $this->arr["def_date_rel_el"];
			}
			$v = $this->form->post_vars[$prefix.$d_id];
			if ($this->arr["hidden"] || aw_global_get("is_ft_change"))
			{
				if (is_array($v))
				{
					$_tmp = mktime($v["hour"],$v["minute"],0,$v["month"],$v["day"],$v["year"]);
					$tm = $_tmp;
					$var = $_tmp;
				}
				else
				{
					$tm = $v;
					$var = $v;
				}
			}
			else
			{
				list($d,$m,$y) = explode("-",date("d-m-y"));

				$var = mktime($v["hour"],$v["minute"],0,$v["month"],$v["day"],$v["year"]);

				$var = $prefix.$d_id;
				global $$var;
				$v = $$var;
				if (!$this->arr["has_month"])
				{
					$v["month"] = 1;
				}
				if (!$this->arr["has_day"])
				{
					$v["day"] = 1;
				}
				if (!$this->arr["has_year"])
				{
					$v["year"] = date("Y");
				}
			
				if ($v["year"] > 0 || (!$this->arr["has_year"]))
				{
					$tm = mktime($v["hour"],$v["minute"],0,$v["month"],$v["day"],$v["year"]);
				}
				else
				{
					$tm = 0;
				}
			}

			if ($this->arr["def_date_type"] == "rel")
			{
				$var+=($this->arr["def_date_num"] * $this->arr["def_date_add"]);
			}
			else
			{
				// I don't get it
				$var = $tm;
			};
		}
		else
		if ($this->arr["type"] == "timeslice")
		{
			$count = $this->form->post_vars[$prefix.$this->id."_count"];
			$type= $this->form->post_vars[$prefix.$this->id."_type"];
			$var = array("count" => $count, "type" => $type);
		}
		else
		if ($this->arr["type"] == "textarea" && $this->arr["wysiwyg"] == 1 && ($this->is_ie))
		{
			$var = $this->form->post_vars["_el_".$prefix.$this->id];
		}
		else
		if ($this->arr["type"] == "listbox")
		{
			$this->_init_listbox_content();
			$var = $this->form->post_vars[$prefix.$this->id];
		}
		else
		{
			$var = $this->form->post_vars[$prefix.$this->id];
		}

		
		// if value controiller is set, always use that
		if ($this->arr["value_controller"] && !$this->form->arr["sql_writer_writer"]) 	
		{
			//$var = $this->form->controller_instance->eval_controller($this->arr["value_controller"], $var, &$this->form, $this);
			$this->form->value_controller_queue[] = array(
				"col" => $this->col,
				"row" => $this->row,
				"idx" => $this->index,
				"id" => $this->id,
				"ctrl_id" => $this->arr["value_controller"],
				"val" => $var,
			);
		}

		$entry[$this->id] = $var;
		$this->entry = $var;
		$this->entry_id = $id;


		if ($this->form->arr["has_controllers"])
		{
			// now let all the element's controllers do their checks
			return $this->do_entry_controller_checks();
		}
		return true;
	}

	function get_selection_id()
	{
		$html = "";
		switch($this->arr["type"])
		{
			case "listbox":
				if ($this->arr["subtype"] == "relation" && $this->arr["rel_element"] && $this->arr["rel_form"])
				{
					if ($this->entry != "")
					{
						// relation listbox can only have a value if the user has selected something!
						$this->make_relation_listbox_content();
					}
				}
                                                                                                                            
				$sp = split("_", $this->entry, 10);
				$html = $sp[3];
				break;
		};
		return $html;
        }



	////
	// !returns the elements value in the currently loaded entry in a form that can be presented to the user (ev_xxx)
	function get_value($numeric = false)
	{
		switch($this->arr["type"])
		{
			case "textarea":
				$html = trim($this->entry);
				break;

			case "radiobutton":
				if (!$numeric)
				{
					if ($this->arr["ch_value"] != "")
					{
						$html=$this->entry == $this->id ? $this->arr["ch_value"] : "";
					}
					else
					{
						$html=($this->entry == $this->id ? " (X) " : " (-) ");
					}
				}
				else
				{
					$html = ($this->entry == $this->id ? 1 : 0);
				}
				break;

			case "listbox":
				if ($this->arr["subtype"] == "relation" && $this->arr["rel_element"] && $this->arr["rel_form"])
				{
					if ($this->entry != "")
					{
						// relation listbox can only have a value if the user has selected something!
						$this->make_relation_listbox_content();
					}
				}

				if ($numeric)
				{
					$html = $this->entry;
				}
				else
				{
					$sp = split("_", $this->entry, 10);
					if (isset($this->arr["listbox_lang_items"][aw_global_get("lang_id")][$sp[3]]))
					{
						$html = $this->arr["listbox_lang_items"][aw_global_get("lang_id")][$sp[3]];
					}
					else
					{
						$html = $this->arr["listbox_items"][$sp[3]];
					}
				}
				break;

			case "multiple":
				$this->_do_init_multiple_items();
				$ec=explode(",",$this->entry);
				$_t = array();
				foreach($ec as $v)
				{
					$_t[] = $this->arr["multiple_items"][$v];
				}
				$html .= join($this->arr['mul_items_sep'], $_t);
				break;

			case "checkbox":
				if (!$numeric)
				{
					if ($this->arr["ch_value"] != "")
					{
						$html=$this->entry == 1 ? $this->arr["ch_value"] : "";
					}
					else
					{
						$html=$this->entry == 1 ? "(X) " : " (-) ";
					}
				}
				else
				{
					$html = $this->entry;
				}
				break;

			case "textbox":
				$html = trim($this->entry);
				break;

			case "date":
				$html = $this->get_date_value($numeric);
				break;

			case "price":
				$html = trim($this->entry);
				break;

			case "link":
				$html = $this->entry["address"];
				break;

			case "timeslice":
				$html = $this->entry["count"] . $this->timeslice_types[$this->entry["type"]];
				break;

			case "file":
				if ($GLOBALS["del_image"] == $this->id && $this->form->entry_id)
				{
					unset($GLOBALS["del_image"]);
					$fi = get_instance(CL_FORM);
					$fi->load($this->form->id);
					$fi->load_entry($this->form->entry_id);
					$fi->set_element_value($this->id, false, false);
					$fi->entry[$this->id] = NULL;
					$fi->process_entry(array(
						"id" => $this->form->id,
						"entry_id" => $this->form->entry_id,
						"no_load_form" => true,
						"no_load_entry" => true,
						"no_process_entry" => true
					));	
					$this->entry = false;
				}

				if ($this->entry["url"] != "")
				{
					classload("file");
					if ($this->arr["ftype"] == 1)
					{
						if ($this->arr["fshow"])
						{
							$dlk = "";
							if (isset($this->arr["file_delete_link_text"][aw_global_get("lang_id")]))
							{
								$dlk = html::href(array(
									"url" => aw_url_change_var("del_image", $this->id),
									"caption" => $this->arr["file_delete_link_text"][aw_global_get("lang_id")]
								));
							}
							$html="<img src=\"".file::check_url($this->entry["url"])."\" />$dlk<br />";
						}
					}
					else
					if ($this->arr["ftype"] == 2)
					{
						if ($this->arr["fshow"])
						{
							$target = "";
							if ($this->arr["file_new_win"] == 1)
							{
								$target = "target=\"_blank\"";
							}

							$html.="<a $target href=\"".file::check_url($this->entry["url"])."\" />".$this->arr["flink_text"]."</a><br />";
						}
					}
				}
				break;
		};
		return $html;
	}

	function sort_listbox()
	{
		if (is_array($this->arr["listbox_items"]))
		{
			$cnt=0;
			foreach($this->arr["listbox_items"] as $k => $v)
			{
				if ($v != "")
				{
					$ar[$k] = $v;
				}
			}

			if (is_array($ar))
			{
				if ($this->arr["sort_by_order"] || $this->arr["sort_by_alpha"])
				{
					uksort($ar,array($this,"__lb_sort"));
				}
				$ordar = $this->arr["listbox_order"];
				$this->arr["listbox_items"] = array();
				$this->arr["listbox_order"] = array();
				foreach( $ar as $k => $v)
				{
					$this->arr["listbox_items"][] = $v;
					$this->arr["listbox_order"][] = $ordar[$k];
				}
			}
		}
	}

	function __lb_sort($a,$b)
	{
		if ($this->arr["sort_by_order"])
		{
			$oa = (int)$this->arr["listbox_order"][$a];
			$ob = (int)$this->arr["listbox_order"][$b];

			if ($oa == $ob)
			{
				if ($this->arr["sort_by_alpha"])
				{
					$res =  strcmp($this->arr["listbox_items"][$a],$this->arr["listbox_items"][$b]);
					return $res;
				}
				else
				{
					return 0;
				}
			}
			else
			if ($oa < $ob)
			{
				return -1;
			}
			else
			{
				return 1;
			}
		}
		else
		if ($this->arr["sort_by_alpha"])
		{
			return strcmp($this->arr["listbox_items"][$a],$this->arr["listbox_items"][$b]);
		}
		else
		{
			return 0;
		}
	}

	function sort_multiple()
	{
		if (is_array($this->arr["multiple_items"]))
		{
			$cnt=0;
			foreach($this->arr["multiple_items"] as $k => $v)
			{
				if ($v != "")
				{
					$ar[$cnt++] = $v;
				}
			}

			if (is_array($ar))
			{
				uksort($ar,array($this,"__mb_sort"));
				$cnt=0;
				$ordar = $this->arr["multiple_order"];
				foreach( $ar as $k => $v)
				{
					$this->arr["multiple_items"][$cnt] = $v;
					$this->arr["multiple_order"][$cnt] = $ordar[$k];
					$cnt++;
				}
			}
		}
	}

	function __mb_sort($a,$b)
	{
		if ($this->arr["sort_by_order"])
		{
			if ($this->arr["multiple_order"][$a] == $this->arr["multiple_order"][$b])
			{
				if ($this->arr["sort_by_alpha"])
				{
					$res =  strcmp($this->arr["multiple_items"][$a],$this->arr["multiple_items"][$b]);
					return $res;
				}
				else
				{
					return 0;
				}
			}
			else
			if ($this->arr["multiple_order"][$a] < $this->arr["multiple_order"][$b])
			{
				return -1;
			}
			else
			{
				return 1;
			}
		}
		else
		if ($this->arr["sort_by_alpha"])
		{
			return strcmp($this->arr["multiple_items"][$a],$this->arr["multiple_items"][$b]);
		}
		else
		{
			return 0;
		}
	}

	////
	// !imports the data from a text file if the user uploaded it
	function import_lb_data()
	{
		$base = "element_".$this->id;
		$var = $base."_import";
		global $$var;
		if (is_uploaded_file($$var))
		{
			// imprtime siis phaili sisu
			$this->arr["listbox_items"] = array();
			$fc = file($$var);
			$cnt=0;
			foreach($fc as $line)
			{
				$line = str_replace("\r","",$line);
				$line = str_replace("\n","",$line);
				if ($line == "")
				{
					$line = " ";
				}
				$this->arr["listbox_items"][$cnt++] = $line;
			}
			$this->arr["listbox_count"] = $cnt;
		}
	}

	////
	// !imports the data from a text file if the user uploaded it
	function import_m_data()
	{
		$base = "element_".$this->id;
		$var = $base."_import";
		global $$var;
		if (is_uploaded_file($$var))
		{
			// imprtime siis phaili sisu
			$this->arr["multiple_items"] = array();
			$fc = file($$var);
			$cnt=0;
			foreach($fc as $line)
			{
				$line = str_replace("\r","",$line);
				$line = str_replace("\n","",$line);
				if ($line == "")
				{
					$line = " ";
				}
				$this->arr["multiple_items"][$cnt++] = $line;
			}
			$this->arr["multiple_count"] = $cnt;
		}
	}

	function do_search_script($rel = false, $tarr = false)
	{
		if (!aw_global_get("search_script"))
		{
			aw_global_set("search_script",true);
			$this->vars(array("SEARCH_SCRIPT" => $this->parse("SEARCH_SCRIPT")));
		}

		if (!aw_global_get("elements_created"))
		{
			// make javascript arrays for form elements
			$formcache = array(0 => "");
			if ($tarr === false)
			{
				$tarr = array();
				$tarr = $this->form->get_search_targets();
				$tarr += $this->form->get_relation_targets();
			}

			$tarstr = join(",",map2("%s",$tarr));
			if ($tarstr != "")
			{
				$this->db_query("SELECT name, oid FROM objects WHERE status != 0 AND oid IN ($tarstr)");
				while ($row = $this->db_next())
				{
					$formcache[$row["oid"]] = $row["name"];
				}
			
				global $tbl_num;
				$el_num = (int)$tbl_num;
				$this->db_query("SELECT objects.name as el_name, element2form.el_id as el_id,element2form.form_id as form_id FROM element2form LEFT JOIN objects ON objects.oid = element2form.el_id WHERE element2form.form_id IN ($tarstr)");
				while ($row = $this->db_next())
				{
					$this->vars(array(
						"el_num" => $el_num++,
						"el_id" => $row["el_id"],
						"el_text" => htmlspecialchars(str_replace("\n","",$row["el_name"])),
						"form_id" => $row["form_id"]
					));
					$eds.=$this->parse("ELDEFS");
				}
			}
			$this->vars(array("ELDEFS" => $eds));
			$this->vars(array("SEARCH_DEFS" => $this->parse("SEARCH_DEFS")));
			aw_global_set("elements_created",true);
			aw_global_set("formcache",$formcache);
		}
	}

	////
	// !reads the elements for the relation listbox (this element) from the database
	function make_relation_listbox_content()
	{
		$this->save_handle();

		// I made it a separete function because I need those valuse in exact same order in form->process_entry
		// too - to check whether the entry falls into allowed range in a calendar
		$opts = $this->arr;
		$opts["ret_ids"] = true;
		if ($this->arr["chain_entries_only"])
		{
			$cce = aw_global_get("current_chain_entry");
			if ($cce)
			{
				$opts["limit_chain_id"] = $cce;
			}
		}

		if (!is_array($this->arr["rel_el_ord"]))
		{
			if ($this->arr["lb_data_from_el_sby"] != "")
			{
				$opts["el_sort_by"] = $this->arr["lb_data_from_el_sby"];
			}
			list($cnt,$this->arr["listbox_items"]) = $this->form->get_entries_for_element($opts);
		}
		else
		{
			$this->arr["listbox_items"] = array();
			foreach($this->arr["rel_el_ord"] as $r_elid => $r_ord)
			{
				$opts["rel_element"] = $r_elid;
				list($cnt,$vals) = $this->form->get_entries_for_element($opts);
				foreach($vals as $e_id => $e_val)
				{
					$this->arr["listbox_items"][$e_id] .= $this->arr["rel_el_sep"][$r_elid].$e_val;
				}
			}
		}
		$this->arr["listbox_count"] = $cnt;
		if ($this->form->type == FTYPE_SEARCH || $this->form->type == FTYPE_FILTER_SEARCH)
		{
			$this->arr["listbox_count"] = $cnt+1;
			$this->arr["listbox_items"][$cnt] = "";
			$this->arr["listbox_default"] = $cnt;
		}
		$this->restore_handle();
	}

	function get_types_cached()
	{
		if (!is_array($this->subtypes))
		{
			$co = get_instance("config");
			if (!($dat = $co->get_simple_config("form::element_subtypes")))
			{
				$this->subtypes = $this->all_subtypes;
			}
			else
			{
				$this->subtypes = aw_unserialize($dat);
			}
		}

		if (!is_array($this->types))
		{
			$co = get_instance("config");
			if (!($dat = $co->get_simple_config("form::element_types")))
			{
				$this->types = $this->all_types;
			}
			else
			{
				$this->types = aw_unserialize($dat);
			}
		}
	}

	function get_all_types()
	{
		return $this->all_types;
	}

	function get_all_subtypes()
	{
		return $this->all_subtypes;
	}

	////
	// !this returns the value for ane element that will be used in controllers 
	// it picks the best version based on the element type
	function get_controller_value()
	{
		if ($this->arr["type"] == "date")
		{
			$val = $this->get_val();
		}
		else
		if ($this->arr["type"] == "radiobutton")
		{
			// for radio elements in the same radio group,
			// we should return the value of the selected radiobutton
	
			// the entry for radio button is the id of the button in the group that is selected. 
			// so we can easily find the correct element
			$sel_id = $this->entry;
			if ($sel_id)
			{
				// if something is selected, go fetch it's value
				$el =& $this->form->get_element_by_id($sel_id);
				$val = $el->get_ch_value();
			}
		}
		else
		{
			$val = $this->get_value();
		}
		return $val;
	}

	////
	// !this gives a chance to validate input to all the element's controllers
	function do_entry_controller_checks()
	{
		if ($this->arr["type"] == "button")
		{
			if ($GLOBALS["submit"][$this->id] == "")
			{
				return;
			}
		}

		$ret = true;
		if (is_array($this->arr["entry_controllers"]))
		{
			foreach($this->arr["entry_controllers"] as $ctrlid)
			{
				$this->form->controller_queue[] = array(
					"ctrlid" => $ctrlid, 
					"val" => $this->get_controller_value(), 
					"el_id" => $this->id,
				);
			}
		}
		return $ret;
	}

	function remove_entry_controller($controller)
	{
		unset($this->arr["entry_controllers"][$controller]);
	}

	function remove_show_controller($controller)
	{
		unset($this->arr["show_controllers"][$controller]);
	}

	function remove_lb_controller($controller)
	{
		unset($this->arr["lb_item_controllers"][$controller]);
	}

	function remove_defvalue_controller($controller)
	{
		unset($this->arr["default_controller"]);
	}

	function remove_value_controller($controller)
	{
		unset($this->arr["value_controller"]);
	}

	function get_date_value($numeric = false)
	{
		if ($this->arr["subtype"] == "created")
		{
			if ($numeric)
			{
				return $this->form->entry_created;
			}
			else
			if ($this->arr["date_format"] == "")
			{
				$html.=$this->time2date($this->form->entry_created,2);
			}
			else
			{
				$html.=date($this->arr["date_format"],$this->form->entry_created);
			}
		}
		else
		{
			if ($numeric)
			{
				if (!$this->entry)
				{
					$vl = $this->get_val();
					if ($this->arr["def_date_type"] == "now")
					{
						$def = time() + ($this->arr["def_date_num"] * $this->arr["def_date_add"]);
					}
					else
					{
						$def = time();
					}
					return $vl ? $vl : $def;
				}
				else
				{
					return $this->entry;
				}
			}
			else
			if ($this->arr["date_format"] == "")
			{
				$html.=$this->time2date($this->entry,5);
			}
			else
			{
				if ($this->entry < 2)
				{
					$html = "";
				}
				else
				{
					$html.=date($this->arr["date_format"],$this->entry);
				}
			}
		}
		return $html;
	}

	function get_writer_element()
	{
		return $this->arr["sql_writer_el"];
	}

	////
	// !sets the element's value to $val 
	// if $user_val == true, then $val is human-readable - it tries to find the best match with the real values
	// else $val is assumed to be fg internal value
	function set_value($val, $user_val = true)
	{
		if (!$user_val)
		{
			$this->entry = $val;
			return;
		}

		switch($this->arr["type"])
		{
			case "checkbox":
				if ($val === 1 || $val === 0)
				{
					$this->entry = $val;
				}
				else
				{
					if ($val == $this->arr["ch_value"])
					{
						$this->entry = 1;
					}
					else
					{
						$this->entry = 0;
					}
				}
				break;

			case "radiobutton":
				if ($val === 1)
				{
					$this->entry = $this->id;
				}
				else
				{
					if ($val == $this->arr["ch_value"])
					{
						$this->entry = $this->id;
					}
					else
					{
						$this->entry = 0;
					}
				}
				break;

			case "listbox":
				$lbitems = $this->get_el_lb_items();
				foreach($lbitems as $idx => $str)
				{
					if (trim($str) == trim($val))
					{
						$this->entry = "element_".$this->arr["id"]."_lbopt_".$idx;
						return;
					}
				}
				break;

			case "multiple":
				// we'll see how we need to use this, but now do just arrays
				if (is_array($val))
				{
					$mulims = is_array($this->arr["multiple_items"]) ? $this->arr["multiple_items"] : array();

					$this->entry = array();
					foreach($val as $_val)
					{
						// and for each do the search and find the index and join them together with ,'s
						foreach($mulims as $idx => $str)
						{
							if ($str == $_val)
							{
								$this->entry[] = $idx;
							}
						}
					}
					$this->entry = join(",", $this->entry);
				}
				break;

			default:
				$this->entry = $val;
				break;
		}
	}

	function _do_init_multiple_items()
	{
		$lang_id = aw_global_get("lang_id");
		if ($this->arr["lb_data_from_form"] && $this->arr["lb_data_from_el"])
		{
			$opts = array(
				"rel_form" => $this->arr["lb_data_from_form"],
				"rel_element" => $this->arr["lb_data_from_el"],
				"sort_by_alpha" => $this->arr["sort_by_alpha"],
				"rel_unique" => $this->arr["rel_unique"],
				"ret_ids" => true,
			);
			$this->arr["multiple_items"] = array();
			$this->arr["multiple_lang_items"] = array();
			list($cnt,$vals) = $this->form->get_entries_for_element($opts);
			foreach($vals as $e_id => $e_val)
			{
				$this->arr["multiple_items"][$e_id] = $e_val;
				$this->arr["multiple_lang_items"][$lang_id][$e_id] = $e_val;
			}
			$this->arr["multiple_count"] = count($vals);;
		}
	}

	function _init_listbox_content()
	{
		if ($this->arr["subtype"] == "relation" && $this->arr["rel_element"] && $this->arr["rel_form"])
		{
			$rel = true;
			if ($this->form->type == FTYPE_ENTRY)
			{
				$this->arr["gefe_add_empty"] = true;
			}
			$this->make_relation_listbox_content();
		}
		else
		if ($this->arr["lb_data_from_form"] && $this->arr["lb_data_from_el"])
		{
			$opts = array(
				"rel_form" => $this->arr["lb_data_from_form"],
				"rel_element" => $this->arr["lb_data_from_el"],
				"sort_by_alpha" => $this->arr["sort_by_alpha"],
				"rel_unique" => $this->arr["rel_unique"],
				"ret_ids" => true,
			);
			list($cnt,$vals) = $this->form->get_entries_for_element($opts);
			foreach($vals as $e_id => $e_val)
			{
				$this->arr["listbox_items"][$e_id] = $e_val;
			}
			$this->arr["listbox_count"] = $cnt;
			$this->arr["listbox_lang_items"][aw_global_get("lang_id")] = $this->arr["listbox_items"];
		}
		return $rel;
	}

	function upd_value()
	{
		if ($this->arr["value_controller"] && (!$this->form->arr["sql_writer_writer"])) 
		{
			$this->entry = $this->form->controller_instance->eval_controller($this->arr["value_controller"], $this->entry, &$this->form, $this);
			$this->form_ref->entry[$this->id] = $this->entry;
		}
	}
}
?>
