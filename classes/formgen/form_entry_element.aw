<?php
// $Header: /home/cvs/automatweb_dev/classes/formgen/form_entry_element.aw,v 1.18 2008/01/31 13:54:34 kristo Exp $
// form_entry_element.aw - 
/*
@classinfo  maintainer=kristo
*/
load_vcl("date_edit");
lc_load("definition");
classload("formgen/form_element");
class form_entry_element extends form_element
{
	function form_entry_element()
	{
		$this->form_element();
		$this->init("forms");

		$this->parent = 0;
		$this->entry_id = 0;
		$this->id = 0;
	}

	function gen_admin_html()
	{
		// XXX: is there a way to load localizations only once and not for each element?
		// probably. who knows? - terryf
		$this->lc_load("form","lc_form");

		$this->read_template("admin_element.tpl");

		$this->vars(array(
			"cell_id" => "element_".$this->id, 
		));

		// here we create the listboxes for selecting tables
		if (is_array($this->form->arr["save_tables"]))
		{
			$tbl_num = 0;
			foreach($this->form->arr["save_tables"] as $tbl => $tbcol)
			{
				$ta = $this->db_get_table($tbl);
				foreach($ta["fields"] as $fn => $fdata)
				{
					$this->vars(array(
						"tbl_num" => $tbl_num++,
						"table_name" => $tbl,
						"col_name" => $this->form->get_fg_colname($fn)
					));
					$t_tb.=$this->parse("TBL");
				}
			}
			$this->vars(array("TBL" => $t_tb));
		}

		$GLOBALS["tbl_num"] = $tbl_num;
		if ($this->form->arr["save_table"] == 1)
		{
			$this->vars(array("TABLE_DEFS" => $this->parse("TABLE_DEFS")));

			if (!aw_global_get("search_script"))
			{
				aw_global_set("search_script",true);
				$this->vars(array("SEARCH_SCRIPT" => $this->parse("SEARCH_SCRIPT")));
			}
		}

		if ($this->form->arr["sql_writer_writer"])
		{
			$this->do_search_script(false, array($this->form->arr["sql_writer_writer_form"] => $this->form->arr["sql_writer_writer_form"]));
			$formcache = aw_global_get("formcache");
			$this->vars(array(
				"forms" => $this->picker($this->form->arr["sql_writer_writer_form"], $formcache),
				"linked_el" => $this->arr["sql_writer_el"]
			));
			$this->vars(array("SEARCH_LB" => $this->parse("SEARCH_LB")));
		}

		$this->do_core_admin();

		return $this->parse();
	}

	////
	// !this function takes the changed properties of this element from the form and joins them together in the array of element properties 
	function save(&$arr)
	{
		$ret =  $this->do_core_save(&$arr);

		$base = "element_".$this->id;
		
		if ($this->form->arr["sql_writer_writer"])
		{
			extract($arr);
			$var=$base."_element";
			$this->arr["sql_writer_el"] = $$var;
		}

		return $ret;
	}


	// function that doesn't use templates
	function gen_user_html_not($prefix = "",$elvalues = array(),$no_submit = false, $element_name = false, $udcnt_values = false)		
	{
		return $this->do_core_userhtml($prefix,$elvalues,$no_submit, $element_name, $udcnt_values);
	}

	function process_entry(&$entry, $id,$prefix = "")
	{
		return $this->core_process_entry(&$entry,$id,$prefix);
	}

	function gen_show_html()
	{
		if ($this->arr["hidden"])
		{
			return "";
		}

		$lang_id = aw_global_get("lang_id");
		$html = "";

		if ($this->arr["value_controller"]) 
		{
			$this->entry = $this->form->controller_instance->eval_controller($this->arr["value_controller"], "", &$this->form, $this);
		}

		if ($this->arr["type"] == "textarea")
		{
			$allow_html = 1;
			if ($this->form->arr["allow_html_set"] == 1)
			{
				$allow_html = $this->form->arr["allow_html"];
			}
			$src = ($allow_html) ? $this->entry : htmlspecialchars($this->entry);
			if (strpos($src, "<a h") === false)
			{
				$src = create_links($src);
			}
			$html = str_replace("\n","<br />",$src);
		}
				
		if ($this->arr["type"] == "radiobutton")
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
				
		if ($this->arr["type"] == "listbox")
		{
			$this->_init_listbox_content();
			$sp = split("_", $this->entry, 10);
			$sp[3] = (int)$sp[3];
			if ($this->form->lang_id != $lang_id)
			{
				if (isset($this->arr["listbox_lang_items"][$lang_id][$sp[3]]))
				{
					$html=$this->arr["listbox_lang_items"][$lang_id][$sp[3]];
				}
				else
				{
					$html=$this->arr["listbox_items"][$sp[3]];
				}
			}
			else
			{
				$html=$this->arr["listbox_items"][$sp[3]];
			}
		}
				
		if ($this->arr["type"] == "multiple")
		{
			$ec=explode(",",$this->entry);
			reset($ec);
			foreach($ec as $v)
			{
				$vx = (int)$v;
				if ($this->form->lang_id != $lang_id)
				{
					$html.=($this->arr["multiple_lang_items"][$lang_id][$vx]." ");
				}
				else
				{
					$html.=($this->arr["multiple_items"][$vx]." ");
				}
			}
		}

		if ($this->arr["type"] == "checkbox")
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
				
		if ($this->arr["type"] == "textbox")
		{
			$src = ($this->form->arr["allow_html"]) ? $this->entry : htmlspecialchars($this->entry);
			if (strpos($src, "<a h") === false && !$this->arr["value_controller"])
			{
				$src = trim(create_links(" ".$src." " ));
			}
			if ($this->arr["subtype"] == "int" && $this->arr["thousands_sep"] != "" && $src != " ")
			{
				// insert separator every after every 3 chars, starting from the end. 
				$src = strrev(chunk_split(strrev(trim($src)),3,$this->arr["thousands_sep"]));
				// chunk split adds one too many separators, so remove that
				$src = substr($src,strlen($this->arr["thousands_sep"]));
			}
			if ($this->arr["subtype"] == "email")
			{
				if (aw_ini_get("formgen.obfuscate_email") == 1)
				{
					$src = preg_replace("/([-.a-zA-Z0-9_]*)@([-.a-zA-Z0-9_]*)/","<script language=\"javascript\">fEpost(\"\\1\",\"\\2\");</script><noscript>\\1<img src='".aw_ini_get("baseurl")."/img/at.png' alt='@' style='vertical-align: middle;'/>\\2</noscript>", $src);
				}
				else
				{
					$src = "<a href='mailto:".$src."'>$src</a>";
				}
			}
			$html = $src;
		}

		if ($this->arr["type"] == "price")
		{
			$html.=$this->entry;
			// currencies are cached the first time we ask for one
			if ($this->arr["price_cur"])
			{
				if (!is_object($this->currency))
				{
					$this->currency = get_instance(CL_CURRENCY);
				}
				if ($this->form->active_currency)
				{
					// if the currency in which to show price is set, then show that currency
					$cur = $this->currency->get($this->form->active_currency);
					$in_dem = (double)$cur["rate"]*(double)$this->entry;
					$html.=$cur["name"];
				}
				else
				{
					$cur = $this->currency->get($this->arr["price_cur"]);
					$in_dem = (double)$cur["rate"]*(double)$this->entry;
					$html.=$cur["name"];
				}

				if (is_array($this->arr["price_show"]))
				{
					foreach($this->arr["price_show"] as $prid)
					{
						$cur = $this->currency->get($prid);
						$val = round((double)$cur["rate"]*$in_dem,2);
						$html.=$this->arr["price_sep"].$val.$cur["name"];
					}
				}
			}
		}

		if ($this->arr["type"] == "date")
		{
			if ($this->arr["subtype"] == "created")
			{
				$html.=$this->get_date_value();
			}
			else
			{
				if ($this->arr["date_format"] == "")
				{
					$html.=$this->time2date($this->entry,5);
				}
				else
				{
					if ($this->entry < 100)
					{
						$html = "";
					}
					else
					{
						$html.=date($this->arr["date_format"],$this->entry);
					}
				}
			}
		}
		if ($this->arr["type"] == "file")
		{
			classload("file");
			if ($this->entry["url"] != "")	
			{
				$furl = file::check_url($this->entry["url"]);
				if (substr($furl, 0, 4) != "http")
				{
					$furl = aw_ini_get("baseurl").$furl;
				}
				if ($this->arr["ftype"] == 1)
				{
					$html.="<img src='".$furl."'>";
				}
				else
				{
					$linktext = ($this->entry["name"]) ? $this->entry["name"] : $this->arr["flink_text"];
					$html.="<a target='_new' href='".$furl."'>".$linktext."</a>";
				}
			}
		}

		if ($this->arr["type"] == "link")
		{
			$target = "";
			if ($this->arr["link_newwindow"])
			{
				$target = 'target="_blank"';
			}

			if ($this->arr["subtype"] == "show_op")
			{
				$html.="<a $target href='".$this->mk_my_orb("show_entry", array("id" => $this->form->id, "entry_id" => $this->entry_id, "op_id" => $this->arr["link_op"], "section" => $GLOBALS["section"]),"form")."'>".$this->arr["link_text"]."</a>";
			}
			else
			{
				// ok, bit of trickery here. if $this->entry is not an array, use it as the link address 
				// and the text of the element as the link caption
				if (is_array($this->entry))
				{
					$html.="<a $target href='".$this->entry["address"]."'>".$this->entry["text"]."</a>";
				}
				else
				{
					$html.="<a $target href='".$this->entry."'>".$this->arr["link_text"]."</a>";
				}
			}
		}

		if ($this->arr["type"] == "button")
		{
			if ($lang_id == $this->form->lang_id)
			{
				$butt = $this->arr["button_text"];
			}
			else
			{
				$butt = $this->arr["lang_button_text"][$lang_id];
			}
			if ($this->arr["subtype"] == "order")
			{
				$loc = $this->mk_my_orb("show", array("id" => $this->arr["order_form"], "load_entry_data" => $this->form->entry_id,"section" => aw_global_get("section")),"form");
				$html = "<input type='submit' VALUE='".$butt."' onClick=\"window.location='".$loc."';return false;\">";
			}
			else
			if ($this->arr["subtype"] == "close")
			{
				$html = "<input type='submit' VALUE='".$butt."' onClick='window.close();return false;'>";
			}
		}

		if ($this->arr["type"] == "alias")
		{
			$obj = get_instance("core/objects");
			if ( ($this->arr["alias_type"] == 1) && (strlen($this->arr["alias_subtype"]) > 2) )
			{
				// we need to show the actual aliased object, not the one added to the output
				$o = obj($this->arr["id"]);
				$conn = $o->connections_from();
				if (sizeof($conn) > 0)
				{
					reset($conn);
					list(,$f_c) = each($conn);
					$html = $obj->show(array("id" => $f_c->prop("to")));
				}
				else
				{
					$html = "";
				};

			}
			elseif ($this->arr["alias"] > 0)
			{
				// yeah!
				$html = $obj->show(array("id" => $this->arr["alias"]));
			};
		}

		if ($this->arr["type"] == "timeslice")
		{

			$html = "timeslice!";


		}

		if ($this->arr["el_css_style"])
		{
			$html = "<span class=\"st".$this->arr["el_css_style"]."\">".$html."</span>";
			classload("layout/active_page_data");
			active_page_data::add_site_css_style($this->arr["el_css_style"]);
		}

		if ($this->form->lang_id == $lang_id)
		{
			$text = $this->arr["text"];
			$info = $this->arr["info"]; 
		}
		else
		{
			$text = $this->arr["lang_text"][$lang_id];
			$info = $this->arr["lang_info"][$lang_id]; 
		}
		$baseurl = $this->cfg["baseurl"];

		if (!$this->arr["ignore_text"])
		{
			if ($this->arr["type"] != "")
			{
				$sep_ver = ($this->arr["text_distance"] > 0 ? "<br /><img src='$baseurl/images/transa.gif' width='1' height='".$this->arr["text_distance"]."' border='0'><br />" : "<br />");
				$sep_hor = ($this->arr["text_distance"] > 0 ? "<img src='$baseurl/images/transa.gif' height='1' width='".$this->arr["text_distance"]."' border='0'>" : "");
			}
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

		if ($info != "")
		{
			$html .= "<br /><font face='arial, geneva, helvetica' size='1'>&nbsp;&nbsp;$info</font>";
		}

		if (!$this->arr["ignore_text"])
		{
			if ($this->arr["sep_type"] == 1)	// reavahetus
			{
				$html.="<br />";
			}
			else
			if ($this->arr["sep_pixels"] > 0)
			{
				$html.="<img src='$baseurl/images/transa.gif' width=".$this->arr["sep_pixels"]." height=1 border=0>";
			}

			if ($this->arr["sep_type"] == 1)	// reavahetus
			{
				$html.="<br />";
			}
			else
			// this is bad too. We need an image called transa.gif for each site.
			// so? of course we need an image like that? what the fuck is wrong with that? - terryf
			// 1) trans.gif, transa.gif, t.gif .. why the hell do we need so many of them?
			// THAT is the reason I don't like this image.
			// we should use only one and perhaps it in the automatweb/images dir
			// besides using transparent pixels for layout is SO 20st century
			// but .. what the hell.
			//
			// ok, so now we use trans.gif from automatweb folder, not site folder. better now? - terryf
			// yeah. with this. But there are lots of other places which use different transparent gifs, those should be fixed too.  --duke
			if ($this->arr["sep_pixels"] > 0)
			{
				$html.="<img src='$baseurl/automatweb/images/trans.gif' width=".$this->arr["sep_pixels"]." height=1 border=0 />";
			}
		}

		return $html;
	}

	function gen_show_text()
	{
		if (!$this->entry_id)
		{
			return "";
		}

		$lang_id = aw_global_get("lang_id");
		if (!$this->arr["ignore_text"])
		{
			if ($this->form->lang_id == $lang_id)
			{
				$text = $this->arr["text"];
			}
			else
			{
				$text = $this->arr["lang_text"][$lang_id];
			}
		}

		$html = $text;
		if ($html != "")
		{
			$html = $text." ";
		}

		$xval = $this->get_value();
		if ($xval != "")
		{
			$html.=$xval." ";
		}

		return strip_tags($html);
	}
}
?>
