<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_search_element.aw,v 1.7 2008/01/31 13:54:34 kristo Exp $
/*
@classinfo  maintainer=kristo
*/

classload("formgen/form_element");
class form_search_element extends form_element
{
	function form_search_element()
	{
		$this->form_element();
		$this->init("forms");

		$this->entry_id = 0;
		$this->sub_merge = 1;
	}

	function gen_admin_html()
	{
		$this->lc_load("form","lc_form");
		$this->read_template("admin_element.tpl");

		$this->do_search_script();

		$this->do_core_admin();

		// teeme formide listboxi ka
		$formcache = aw_global_get("formcache");
		$this->vars(array(
			"forms" => $this->picker($this->arr["linked_form"], $formcache),
			"linked_el" => $this->arr["linked_element"]
		));

		$this->vars(array("SEARCH_LB" => $this->parse("SEARCH_LB")));
		return $this->parse();
	}

	function save(&$arr)
	{
		extract($arr);

		$ret = $this->do_core_save(&$arr);

		$base = "element_".$this->id;
		
		$var=$base."_form";
		$this->arr["linked_form"] = $$var;

		$var=$base."_element";
		$this->arr["linked_element"] = $$var;

		$this->arr["ver2"] = true;

		return $ret;
	}

	function gen_user_html_not($prefix = "",$elvalues = array(),$no_submit = false)		// function that doesn't use templates
	{
		if ($this->arr["ver2"])	// backward compatibility sucks ass, but whut can I do...
		{
			if ($this->get_type() == 'listbox')
			{
				// add an empty element to the listbox so we can tell the difference, 
				// if nothing was selected and we can then ignore the lb in the search
				$this->arr["listbox_items"][$this->arr["listbox_count"]] = "";
				if (!$this->arr["listbox_default"])
				{
					$this->arr["listbox_default"] = $this->arr["listbox_count"];
				}
				$this->arr["listbox_count"]++;
			}
			$r = $this->do_core_userhtml($prefix,$elvalues,$no_submit);
			return $r;
		}
		else
		{
			if ($this->arr["linked_element"] > 0)
			{
				$form = &$this->get_cached_form();

				$t = $form->get_element_by_id($this->arr["linked_element"]);

				if ($t)
				{
					$t->entry = $this->entry;
					$t->entry_id = $this->entry_id;
					if ($t->get_type() == 'listbox')
					{
						// add an empty element to the listbox so we can tell the difference, 
						// if nothing was selected and we can then ignore the lb in the search
						$t->arr["listbox_items"][$t->arr["listbox_count"]] = "";
						$t->arr["listbox_default"] = $t->arr["listbox_count"];
						$t->arr["listbox_count"]++;
					}
					if ($this->arr["text"] != "")
					{
						$t->arr["text"] = $this->arr["text"];
					}

					if (!($t->get_type() == 'file' || $t->get_type() == 'link'))
					{
						$r =  $t->gen_user_html_not(&$images);
						return $r;
					}
					else
					{
						return "";
					}
				}
				else
				{
					return "";
				}
			}
			else
			{
				return "";
			}
		}
	}

	// lauri lisas siia 20.aug.2001 prefix muutuja, mis puudu oli
	function process_entry(&$entry, $id, $prefix)
	{
		if ($this->arr["ver2"])	// backward compatibility is a bitch
		{
			$r = $this->core_process_entry(&$entry,$id,$prefix);
			return $r;
		}
		else
		{
			if (!$this->arr["linked_element"])
			{
				return;
			}

			$form = &$this->get_cached_form();
			$t = $form->get_element_by_id($this->arr["linked_element"]);
			if ($t->get_type() != "listbox")
			{
				$te = array();
				$t->process_entry(&$te, $id);
				$entry[$this->id] = $te[$this->arr["linked_element"]];
			}
			else
			{
				// check if the empty element that we added was selected and if it was, don't write anything to the db, 
				// so we can easily ignore the element in the search
				$var = $t->get_id();
				global $$var;

				if ($$var == "element_".$this->arr["linked_element"]."_lbopt_".$t->arr["listbox_count"])
				{
					$entry[$this->id] = "";
				}
				else
				{
					$entry[$this->id] = $$var;
				}
			}
			$this->entry = $entry[$this->id];
			$this->entry_id = $id;
		}
	}

	function gen_show_html()
	{
		if (!$this->entry_id)
		{
			return "";
		}

		$form = &$this->get_cached_form();
		$t = $form->get_element_by_id($this->arr["linked_element"]);
		$t->entry = $this->entry;
		$t->entry_id = $this->entry_id;
		$r = $t->gen_show_html();
		return $r;
	}

	function &get_cached_form()
	{
		$formcache = aw_global_get("formcache");
		if (!isset($formcache[$this->arr["linked_form"]]))
		{
			$formcache[$this->arr["linked_form"]] = get_instance(CL_FORM);
			$formcache[$this->arr["linked_form"]]->load($this->arr["linked_form"]);
			aw_global_set("formcache", $formcache);
		}
		return $formcache[$this->arr["linked_form"]];
	}
	
	function get_type()		
	{	
		return $this->arr["type"]; 
	}
}
?>
