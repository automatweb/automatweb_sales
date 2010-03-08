<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_cell.aw,v 1.21 2008/01/31 13:54:33 kristo Exp $
/*
@classinfo  maintainer=kristo
*/

// ysnaga. asi peab olema nii lahendatud, et formi juures on elemendi properitd kirjas
// st forms.contents sees on ka selle elemendi propertid selle formi sees kirjas
// et saax igale formile eraldi elemendi properteid panna
// JA elemendi juures on kirjas, et mis formide sees selle element on. 
// see on sellex, et siis kui on vaja teha nimekirja t2idetud formidest, kus see element sees on, siis
// saab sealt selle kohe teada. 

// no public interface here. everything goes through form.aw
// why? iizi. cause the element properies are saved in the form and therefore the form must always be loaded
// so we have to go through form->load to get the cells' elements.
classload("formgen/form_base");
class form_cell extends form_base
{
	function form_cell()
	{
		$this->form_base();
		$this->sub_merge = 1;
	}

	////
	// !ElementFactory for all you fancy people ;)
	// creates the correct element based on form type
	//
	// sometime in the future this should be rewritten to compose elements together from pieces, using
	// the decorator pattern - for instance, like this: $tmp = new search_element(new listbox_element)
	// then we could split the bloody huge form_element class into smaller pieces
	// 
	function mk_element($type, &$r)
	{
		switch($type)
		{
			default:
			case FTYPE_ENTRY:
				$t = "formgen/form_entry_element";
				break;
			case FTYPE_SEARCH:
				$t = "formgen/form_search_element";
				break;
			case FTYPE_FILTER_SEARCH:
				$t = "formgen/form_filter_search_element";
				break;
		}
		$this->arr[$this->cnt] =& get_instance($t);
		$this->arr[$this->cnt]->load(&$r, &$this->form, $this->col, $this->row, $this->cnt);
		$this->cnt++;
	}

	////
	// !loads the form cell from the given form instance $form and takes it's place as the cell form col $col and row $row
	function load(&$form, $row, $col)
	{
		$this->col = $col;
		$this->row = $row;
		$this->form = &$form;

		$this->id = $this->form->get_id();
		$this->parent = $this->form->get_parent();

		// the number of elements in this cell
		$this->cnt = 0;

		$tp = $form->get_type();

		if (is_array($this->form->arr["elements"][$row][$col]))
		{
			foreach($this->form->arr["elements"][$row][$col] as $k => $v)
			{
				// awkwardly enough, the styles are saved here too under the name "style" and "style_class" - so 
				// we check if it is a number so we won't try to create elements from styles
				if (is_numeric($k))	
				{
					$this->mk_element($tp, &$v);
				}
			}
		}

		$this->style = isset($this->form->arr["elements"][$row][$col]["style"]) ? $this->form->arr["elements"][$row][$col]["style"] : false;
		$this->style_class = isset($this->form->arr["elements"][$row][$col]["style_class"]) ? $this->form->arr["elements"][$row][$col]["style_class"] : false;
	}

	////
	// !Displays cell administration form for previously loaded cell
	function admin_cell()
	{
		$this->read_template("admin_cell.tpl");
		$chlink = $this->mk_my_orb("change", array("id" => $this->id),"form");
		$this->mk_path($this->parent, "<a href='".$chlink.LC_FORM_CELL_CHANGE_FORM_CHANGE_CELL);

		for ($i=0; $i < $this->cnt; $i++)
		{
			$this->vars(array(
				"after" => $this->arr[$i]->get_id(),
				"element" => $this->arr[$i]->gen_admin_html()
			));
			$this->parse("ELEMENT_LINE");
		}

		$this->vars(array(
			"add_el" => $this->mk_my_orb("add_element", array("id" => $this->id, "row" => $this->row, "col" => $this->col),"form"),
		));

		$ca = $this->parse("CAN_ADD");

		$this->vars(array(
			"CAN_ADD" 	=> $ca,
			"cell_style" => $this->picker($this->get_style(), $this->form->style_instance->get_select(0,ST_CELL, true)),
			"reforb"	=> $this->mk_reforb("submit_cell", array("id" => $this->id, "row" => $this->row, "col" => $this->col),"form"),
		));
		return $this->form->do_menu_return($this->parse());
	}

	////
	// !returns an array containing data about all the elements in this cell
	function get_elements()
	{
		$ret = array();
		for ($i=0; $i < $this->cnt; $i++)
		{
			$ret[$i]["text"] = $this->arr[$i]->get_text();
			$ret[$i]["name"] = $this->arr[$i]->get_el_name();
			$ret[$i]["type"] = $this->arr[$i]->get_type();
			// subtype vaja teada int sortimise jaoks form_tables
			$ret[$i]["subtype"] = $this->arr[$i]->get_subtype();
			// and I need value to do some voodoo with form_calendar 
			$ret[$i]["value"] = $this->arr[$i]->get_value();
			$ret[$i]["id"] = $this->arr[$i]->get_id();
			$ret[$i]["order"] = $this->arr[$i]->get_order();
			$ret[$i]["group"] = $this->arr[$i]->get_el_group();
			$ret[$i]["lb_items"] = $this->arr[$i]->get_el_lb_items();
			$ret[$i]["thousands_sep"] = $this->arr[$i]->get_thousands_sep();

			// if that element is a relation element, perhaps we should try
			// and load it's contents too?
			$ret[$i]["rel_form"] = $this->arr[$i]->get_prop("rel_form");
			$ret[$i]["rel_element"] = $this->arr[$i]->get_prop("rel_element");

			// I need those to figure out the oid of the selected item
			// in a relation listbox in form->process_entry
			$ret[$i]["sort_by_alpha"] = $this->arr[$i]->get_prop("sort_by_alpha");
			$ret[$i]["rel_unique"] = $this->arr[$i]->get_prop("rel_unique");

			if ($this->arr[$i]->get_type() == "checkbox")
			{
				$ret[$i]["group"] = $this->arr[$i]->arr["ch_grp"];
			}
		}
		return $ret;
	}

	////
	// !this is called when the form grid is saved, and we must only save the name and order of elements
	// $dat - the POST data
	function save_short($dat)
	{
		for ($i=0; $i < $this->cnt; $i++)
		{
			$this->arr[$i]->save_short($dat);
		}
		$this->prep_save();
	}

	////
	// !Adds a new element in the folder $parent and associates it with the currently loaded form also. 
	// if wizard_step is not set then we are coming from the "add new element" link and have to let the
	// user make a choice what element she wants to add
	// if wizard_step is set, then she already made her choice and we can probably just add the element
	function add_element()
	{
		$churl = $this->mk_orb("change", array("id" => $this->id),"form");
		$this->mk_path($this->parent, "<a href='".$churl.LC_FORM_CELL_CHANGE_FROM_ADD_ELEMENT);
		$this->read_template("add_el_wiz1.tpl");

		$tlist = $this->get_menu_list();

		if (!(is_array($this->form->arr["el_menus"]) && count($this->form->arr["el_menus"]) > 0))
		{
			$mlist = $tlist;
		}
		else
		{
			$mlist = array();
			foreach($this->form->arr["el_menus"] as $menuid)
			{
				$mlist[$menuid] = $tlist[$menuid];
			}
		}
		
		$this->vars(array(
			"reforb"		=> $this->mk_reforb("submit_element", array("id" => $this->id, "row" => $this->row, "col" => $this->col),"form"),
			"folders"		=> $this->picker($this->parent, $mlist),
			"elements"	=> $this->picker(0,$this->form->listall_elements())
		));
		return $this->form->do_menu_return($this->parse());
	}

	////
	// !add_element submit handler
	function submit_element($args = array())
	{
		extract($args);
		if ($type == "add")
		{
			// add new element

			// form_elements table is used to remember the elements properties so that when the element is
			// inserted into another form the default properties are the same as in the pervious form
			// the actual info about how the element is to be shown is written into the form's data structure 
			// and also element2form table contains all element -> form relationships
			$o = obj();
			$o->set_parent($parent);
			$o->set_name($name);
			$o->set_class_id(CL_FORM_ELEMENT);
			$el = $o->save();

			$this->db_query("INSERT INTO form_elements (id) values($el)");
			$arr = array(); // new elements do not have any props, so set that to 0
		}
		else
		{
			// the other choice is most likely "select" which ment that the user selected an already existing element
			if ($el)
			{
				// so we read it's properties from the element's table
				$oo = obj($el);
				$name = $oo->name();
				$ord = $oo->ord();
				$props = $this->db_fetch_field("SELECT props FROM form_elements WHERE id = ".$el,"props");
				$arr = aw_unserialize($props);
			}
		}
		
		if ($el)
		{
			// create necessary db tables
			$this->form->add_element_cols($this->id,$el);

			// add the element into the form.
			// but! use the props saved in the form_elements table to create them with the right config right away!
			
			// sneak in the new bits
			$arr["id"] = $el;
			$arr["name"] = $name;
			$arr["ord"] = $ord;

			// so we lose the relations if adding an existing element. Is there a good reason for that? -- duke
			//
			// well - sonce the likelyhood of these still pointing to the right place is pretty small, it seemes
			// best to do it. but no, I can't think of any other reason right now. why, you no like it? - terryf
			$arr["linked_element"] = 0;
			$arr["linked_form"] = 0;
			$arr["linked_element"] = 0;
			$arr["rel_table_id"] = 0;

			$this->form->arr["elements"][$this->row][$this->col][$el] = $arr;
			$this->form->save();
		}
	}

	////
	// !adds an element to this cell
	// $parent, $name, $ord - pretty obvious methinks
	// $based_on - the element whose properties the new element will recieve. if props is not set and this is, the properties are 
	//		read from the database from the based_on element, optional
	// $props - the element properties, optional
	function do_add_element($arr)
	{
		extract($arr);
		$this->save_handle();

		$o = obj();
		$o->set_parent($this->form->id);
		if (is_oid($parent))
		{
			$o->set_parent($parent);
		}
		$o->set_name($name);
		$o->set_class_id(CL_FORM_ELEMENT);
		$el = $o->save();
		$this->db_query("INSERT INTO form_elements (id) values($el)");
		$this->form->add_element_cols($this->id,$el);

		if (!is_array($props) && $based_on)
		{
			$props = $this->db_fetch_field("SELECT props FROM form_elements WHERE id = ".$based_on,"props");
			$arr = aw_unserialize($props);
		}
		else
		if (is_array($props))
		{
			$arr = $props;
		}
		else
		{
			$arr = array();
		}
		$arr["id"] = $el;
		$arr["name"] = $name;
		$arr["ord"] = $ord;
		$arr["linked_element"] = 0;
		$arr["linked_form"] = 0;
		$arr["type_name"] = "";
		$arr["rel_table_id"] = 0;
		$this->form->arr["elements"][$this->row][$this->col][$el] = $arr;
		$this->restore_handle();

		return $el;
	}

	//// 
	// !deletes all the elements in this cell
	// this will only get called when the user deletes a row or column - then it will be called for each cell in that row/col
	function del()
	{
		for ($i=0; $i < $this->cnt; $i++)
		{
			$this->arr[$i]->del();
		}
	}

	////
	// !generates the html for this cell. or rather - let's the elements generate it and adds styles
	function gen_user_html_not($def_style, $colspan, $rowspan,$prefix = "",$elvalues,$no_submit=false)
	{
		$c = "";
		$cs = "";
		$has_els = false;
		$has_nothidden = false;
		for ($i = 0; $i < $this->cnt; $i++)
		{
			// here we must check the show element controllers
			$errs = array();
			$shcs = $this->arr[$i]->get_show_controllers();
			$controllers_ok = true;
			foreach($shcs as $ctlid)
			{
				$res = $this->form->controller_instance->do_check($ctlid, $this->arr[$i]->get_controller_value(), &$this->form, &$this->arr[$i]);
				if ($res !== true)
				{
					$controllers_ok = false;
					if ($this->form->controller_instance->get_show_errors())
					{
						$errs[] = $res;
					}
				}
			}

			if ($controllers_ok)
			{
				if (!$this->arr[$i]->arr["hidden"])
				{
					$has_nothidden = true;
				}
				$c .= $this->arr[$i]->gen_user_html_not($prefix,$elvalues,$no_submit);
				$has_els = true;
			}
			else
			{
				$erstr = join("<br />", $errs);
				if ($erstr != "")
				{
					$c .= "<font color='red' size='2'>".$erstr."</font>";
					$has_els = true;
				}
			}
		}

		if ($has_els == false && $this->form->arr["hide_empty_rows"] == 1)
		{
			return -1;
		}

		if (!$has_nothidden && $this->form->arr["hide_empty_rows"] == 1)
		{
			$this->form->gen_preview_append .= $c;
			return "";
		}

		if ($c == "")
		{
			$c = "<img src='".$this->cfg["baseurl"]."/automatweb/images/trans.gif' height=1 width=1 border=0>";
		}

		// this gets set to the cell style at loading time
		$style_id = $this->style;
		if (!$style_id)
		{
			$style_id = $def_style;
		}

		if ($this->style_class == CL_CSS)
		{
			$styl = "";
			if ($style_id)
			{
				if (!isset($this->form->styles[$style_id]))
				{
					$form_style_count = aw_global_get("form_style_count");
					$styl = "formstyle".$form_style_count;
					$this->form->styles[$style_id] = $styl;
					aw_global_set("form_style_count", $form_style_count+1);
				}
				else
				{
					$styl = $this->form->styles[$style_id];
				}
			}
			$cs.="<td colspan=\"".$colspan."\" rowspan=\"".$rowspan."\" class=\"$styl\">".$c."</td>";
		}
		else
		{
			if ($style_id)
			{
				$cs.= $this->form->style_instance->get_cell_begin_str($style_id,$colspan,$rowspan);
			}
			else
			{
				$cs .= "<td colspan=\"".$colspan."\" rowspan=\"".$rowspan."\">";
			}

			$cs.= $c;

			if ($style_id)
			{
				$cs.= $this->form->style_instance->get_cell_end_str($style_id);
			}

			$cs.= "</td>";
		}			
		return $cs;
	}
	
	////
	// !gets called after submitting a form and must read the data from POST vars and gather it into $entry array. 
	// also must check view controllers, so that we don't overwrite data for elements that were not in the form.
	// $entry - array to collect the data to  (el_id => value)
	// $id - entry_id
	// prefix - gets prepended to all element's names in html - so we can merge several forms together
	function process_entry(&$entry, $id,$prefix = "")
	{
		$controllers_ok = true;

		// iterate over all the elements in the cell
		for ($i=0; $i < $this->cnt; $i++)
		{
			$shctrlok = true;
			$errs = array();
			$shcs = $this->arr[$i]->get_show_controllers();
			foreach($shcs as $ctlid)
			{
				$res = $this->form->controller_instance->do_check($ctlid, $this->arr[$i]->get_controller_value(), &$this->form, $this->arr[$i]);
				if ($res !== true)
				{
					$shctrlok = false;
					//echo "show controller $ctlid failed for element ".$this->arr[$i]->get_id()." <br />";
				}
			}
			if ($shctrlok)
			{
				// call process_entry for each element and let it manage the data itself
				$controllers_ok &= $this->arr[$i] -> process_entry(&$entry, $id,$prefix);
			}
		};
		return $controllers_ok;
	}

	////
	// !changes the contained element's values to the ones given in $arr, e_id - entry_id
	function set_entry(&$arr, $e_id)
	{
		for ($i=0; $i < $this->cnt; $i++)
		{
			$this->arr[$i] -> set_entry(&$arr, $e_id);
		}
	}

	////
	// !returns an array containing references to all the contained form elements in this cell
	// but it doesn't return the value, it modifies the array passed as an argument
	function get_els(&$arr)
	{
		if (!is_array($arr))
		{
			$arr = array();
		}
		for ($i=0; $i < $this->cnt; $i++)
		{
			$arr[] = &$this->arr[$i];
		}
	}

	////
	// !returns the style id for this cell
	function get_style()
	{
		return $this->style;
	}

	////
	// !saves the style for the current cell and also in the form so that it will be remembered if the form is saved
	// $id - style id
	// $style_class - the class_id of the style object (this is used to differentiate between css and aw styles)
	function set_style($id,$style_class = 0)
	{
		$this->form->arr["elements"][$this->row][$this->col]["style"] = $id;
		$this->form->arr["elements"][$this->row][$this->col]["style_class"] = $style_class;
		$this->style = $id;
		$this->style_class = $style_class;
	}

	////
	// !saves the elements in this cell and also deletes the ones that must be and saves cell style
	function submit_cell(&$arr)
	{
		$deleted = false;
		// gather all properties of the elements in the cell in their arrays from the submitted form
		// and put them in the form's array of element properties
		for ($i=0; $i < $this->cnt; $i++)
		{
			$elid = $this->arr[$i]->get_id();
			if ($this->arr[$i]->save(&$arr) == false)
			{
				// save function returning false signalt we must delete the element from this form.

				// this handles the database side
				$this->arr[$i]->del();
				unset($this->form->arr["elements"][$this->row][$this->col][$elid]);

				// we must also erase the object from this cell 
				unset($this->arr[$i]);
				$deleted = true;
			}
			else
			{
				// save the elements properties to form_elements' table so that when you add the same element to a new form, 
				// all it's properties are exactly the same! yeah! baby! SWEET!
				$xp = aw_serialize($this->arr[$i]->get_props(),SERIALIZE_XML);
				$this->quote(&$xp);
				$this->db_query("UPDATE form_elements SET props = '".$xp."' WHERE id = ".$elid);
			}
		}
		// if we deleted some element(s), we must compact the element array, leaving out the deleted elements
		if ($deleted)
		{
			$tmp = array();
			$num = 0;
			foreach($this->arr as $cnt => $el)
			{
				if (is_object($el))
				{
					$tmp[$num++] = $el;
				}
			}
			$this->arr = $tmp;
			$this->cnt = $num;
		}
		
		$this->prep_save();
		$this->set_style($arr["cell_style"], CL_STYLE);
	}

	////
	// !lets all member elements generate javascript that checks the element values before submit and returns the aggregate
	function gen_check_html()
	{
		$ret = "";
		for ($i=0; $i < $this->cnt; $i++)
		{
			$ret.=$this->arr[$i]->gen_check_html();
		}
		return $ret;
	}

	////
	// !removes controller $controller for type $type from element $element in this cell
	function remove_controller_from_element($arr)
	{
		extract($arr);
		for ($i=0; $i < $this->cnt; $i++)
		{
			if ($this->arr[$i]->get_id() == $element)
			{
				switch($type)
				{
					case CTRL_USE_TYPE_ENTRY:
						$this->arr[$i]->remove_entry_controller($controller);
						break;

					case CTRL_USE_TYPE_SHOW:
						$this->arr[$i]->remove_show_controller($controller);
						break;

					case CTRL_USE_TYPE_LB:
						$this->arr[$i]->remove_lb_controller($controller);
						break;

					case CTRL_USE_TYPE_DEFVALUE:
						$this->arr[$i]->remove_defvalue_controller($controller);
						break;

					case CTRL_USE_TYPE_VALUE:
						$this->arr[$i]->remove_value_controller($controller);
						break;
				}
			}
		}
		$this->prep_save();
	}

	////
	// !call this after the cell's elements properties have changed - it makes sure that they get
	// saved along with form_base::save
	function prep_save()
	{
		for ($i=0; $i < $this->cnt; $i++)
		{
			$this->form->arr["elements"][$this->row][$this->col][$this->arr[$i]->get_id()] = $this->arr[$i]->get_props();
		}
	}

	////
	// !sets element $el 's entry to $val if the element exists in this cell
	function set_element_entry($el,$val, $usr_val = false)
	{
		for ($i=0; $i < $this->cnt; $i++)
		{
			if ($this->arr[$i]->get_id() == $el)
			{
				$this->arr[$i]->set_value($val, $usr_val);
			}
		}
	}

	function upd_value()
	{
		for ($i=0; $i < $this->cnt; $i++)
		{
			$this->arr[$i]->upd_value();
		}
	}
};
?>
