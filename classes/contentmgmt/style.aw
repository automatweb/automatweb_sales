<?php

define("ST_TABLE",0);
define("ST_CELL",1);
define("ST_ELEMENT",2);

$style_cache = array();

class style extends aw_template
{
	var $type_names = array(0 => LC_STYLE_TABLE_STYLE, 1 => LC_STYLE_CELL_STYLE, 2 => LC_STYLE_ELEMENT_STYLE);

	function style()
	{
		$this->init("style");
		$this->sub_merge = 1;
		lc_load("definition");
		$this->lc_load("style","lc_style");
	}

	function db_listall($parent,$type = -1)
	{
		if ($type != -1)
		{
			$ss = " AND styles.type = $type ";
		}
		if ($parent > 0)
		{
			$pt = "AND parent = $parent";
		}
		$this->db_query("SELECT objects.*,styles.* FROM objects LEFT JOIN styles ON styles.id = objects.oid WHERE status != 0 AND class_id = ".CL_STYLE." $pt ".$ss." ORDER BY objects.jrk,objects.name");
	}

	function get($id)
	{
		if (!$id)
		{
			return false;
		}
		$this->db_query("SELECT objects.*,styles.* FROM objects LEFT JOIN styles ON styles.id = objects.oid WHERE oid = $id ");
		return $this->db_next();
	}

	function get_select($parent, $type, $addempty = false, $css = false)
	{
		$this->db_listall(0,$type);
		if ($addempty)
		{
			$arr = array(0 => "");
		}
		else
		{
			$arr = array();
		}
		while ($row = $this->db_next())
		{
			$arr[$row["id"]] = $row["name"];
		}

		if ($css)
		{
			$ol = new object_list(array(
				"class_id" => CL_CSS,
				"site_id" => array(),
				"lang_id" => array(),
				"sort_by" => "objects.jrk,objects.name"
			));
			for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
			{
				$arr[$o->id()] = "CSS: ".$o->name();
			}
		}
		return $arr;
	}

	// parent
	/**
		@attrib name=list params=name default="0"
		@param parent required
	**/
	function glist($arr)
	{
		extract($arr);

		$this->mk_path($parent,LC_STYLE_STYLES);

		$this->read_template("list.tpl");
		$this->db_listall($parent);
		$this->vars(array("parent" => $parent,"add" => $this->mk_orb("add",array("parent" => $parent))));
		while ($row = $this->db_next())
		{
			$this->vars(array(
				"name" => $row["name"],
				"type" => $this->type_names[$row["type"]],
				"style_id" => $row["id"],
				"change"	=> $this->mk_orb("change",array("parent" => $parent, "id" => $row["id"])),
				"delete"	=> $this->mk_orb("delete",array("parent" => $parent, "id" => $row["id"]))
			));
			$this->parse("LINE");
		}
		return $this->parse();
	}

	// parent
	/**
		@attrib name=add params=name default="0"
		@param parent required acl="add"
	**/
	/**
		@attrib name=new params=name default="0"
		@param parent required acl="add"
	**/
	function add($arr)
	{
		extract($arr);

		$this->mk_path($parent,LC_STYLE_ADD_STYLE);

		$this->read_template("add_sel.tpl");
		$this->vars(array("reforb" => $this->mk_reforb("submit_sel",array("parent" => $parent))));
		return $this->parse();
	}

	// parent, id
	/**
		@attrib name=change params=name default="0"
		@param id required acl="view;edit"
	**/
	function change($arr)
	{
		extract($arr);
		$obj = new object($id);

		$this->mk_path($obj->parent(),LC_STYLE_CHANGE_STYLE);

		$this->style = $this->get($id);
		switch($this->style["type"])
		{
			case ST_TABLE:
				return $this->change_table($arr);
			case ST_CELL:
				return $this->change_cell($arr);
			case ST_ELEMENT:
				return $this->change_element($arr);
		}
	}

	function change_table($arr)
	{
		extract($arr);
		$this->read_template("change_table.tpl");

		$style = unserialize($this->style["style"]);

		$sel = $this->get_select(0,ST_CELL);
		$sel_css = $this->get_select(0,ST_CELL, false, true);

		$this->vars(array(
			"name" => $this->style["name"],
			"comment" => $this->style["comment"],
			"bgcolor"	=> $style["bgcolor"],
			"cellpadding"	=> $style["cellpadding"],
			"cellspacing"	=> $style["cellspacing"],
			"border"			=> $style["border"],
			"height"			=> $style["height"],
			"width"				=> $style["width"],
			"hspace"			=> $style["hspace"],
			"vspace"			=> $style["vspace"],
			"header_style"	=> $this->picker($style["header_style"],$sel_css),
			"footer_style"	=> $this->picker($style["footer_style"],$sel_css),
			"even_style"	=> $this->picker($style["even_style"],$sel_css),
			"odd_style"	=> $this->picker($style["odd_style"],$sel_css),
			"num_frows"			=> $style["num_frows"],
			"num_fcols"			=> $style["num_fcols"],
			"frow_style"	=> $this->picker($style["frow_style"],$sel_css),
			"fcol_style"	=> $this->picker($style["fcol_style"],$sel_css),
			"reforb"			=> $this->mk_reforb("submit",array("parent" => $parent, "id" => $id))
		));

		return $this->parse();
	}

	function change_cell($arr)
	{
		extract($arr);

		$fonts = array("" => "", "arial" => "Arial","times" => "Times", "verdana" => "Verdana","tahoma" => "Tahoma", "geneva"  => "Geneva", "helvetica" => "Helvetica", "Trebuchet MS" => "Trebuchet MS");

		$fontsizez = array("" => "","-1" => -1, "0" => 0, "1" => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5);

		$fontstyles = array('normal' => "Tavaline", 'bold' => "Bold",'italic' => "Italic", 'underline' => "Underline");

		$style = unserialize($this->style["style"]);

		$this->read_template("change_cell.tpl");
		$this->vars(array(
			"name" => $this->style["name"],
			"comment" => $this->style["comment"],
			"font1"		=> $this->option_list($style["font1"], $fonts),
			"font2"		=> $this->option_list($style["font2"], $fonts),
			"font3"		=> $this->option_list($style["font3"], $fonts),
			"fontsize"	=> $this->option_list($style["fontsize"], $fontsizez),
			"color"		=> $style["color"],
			"bgcolor"	=> $style["bgcolor"],
			"fontstyles"	=> $this->option_list($style["fontstyle"], $fontstyles),
			"align_left"	=> $style["align"] == "left" ? "CHECKED" : "",
			"align_right"	=> $style["align"] == "right" ? "CHECKED" : "",
			"align_center"	=> $style["align"] == "center" ? "CHECKED" : "",
			"valign_top"	=> $style["valign"] == "top" ? "CHECKED" : "",
			"valign_center"	=> $style["valign"] == "center" ? "CHECKED" : "",
			"valign_bottom"	=> $style["valign"] == "bottom" ? "CHECKED" : "",
			"height"	=> $style["height"],
			"width"		=> $style["width"],
			"css_class"		=> $style["css_class"],
			"nowrap"	=> $style["nowrap"]  ? "CHECKED" : "",
			"visited" => $this->picker($style["visited"], $this->get_select(0, ST_CELL, true)),
			"css_text" => $style["css_text"],
			"reforb"	=> $this->mk_reforb("submit",array("parent" => $parent, "id" => $id))
		));
		return $this->parse();
	}

	/**
		@attrib name=submit_sel params=name default="0"
	**/
	function submit_sel($arr)
	{
		// lisame 6ige tyybiga
		$o = obj();
		$o->set_parent($arr["parent"]);
		$o->set_name($arr["name"]);
		$o->set_class_id(CL_STYLE);
		$o->set_comment($arr["comment"]);
		$id = $o->save();

		$this->db_query("INSERT INTO styles (id,type) values($id,".$arr["type"].")");
		return $this->mk_orb("change", array("parent" => $arr["parent"], "id" => $id));
	}

	/**
		@attrib name=submit params=name default="0"
	**/
	function submit($arr)
	{
		extract($arr);

		$sts = serialize($st);

		$this->db_query("UPDATE styles SET style = '$sts' WHERE id = $id");

		$this->_log(ST_STYLE, SA_CHANGE, "$name");

		$tmp = obj($id);
		$tmp->set_name($name);
		$tmp->set_comment($comment);
		$tmp->save();

		return $this->mk_orb("change",array("parent" => $parent, "id" => $id));
	}

	/**
		@attrib name=delete params=name default="0"
		@param parent required
		@param id required acl="delete"
	**/
	function delete($arr)
	{
		extract($arr);

		$tmp = obj($id);
		$tmp->delete();
		header("Location: ".$this->mk_orb("obj_list", array("parent" => $parent),"menuedit"));
	}

	function get_table_string($id)
	{
		if (!is_oid($id) || !$this->can("view", $id))
		{
			return false;
		}
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
			classload("layout/active_page_data");
			active_page_data::add_site_css_style($st["oid"]);
			$o = obj($id);
			if ($o->prop("margin") != "")
			{
				$rv = " cellpadding=\"".$o->prop("margin")."\" ";
			}
			return $rv."class=\"".$this->get_style_name($st["oid"])."\"";
		}

		if ($st["bgcolor"] != "")
		{
			$str.="bgcolor=\"".$st["bgcolor"]."\" ";
		}
		if ($st["border"] != "")
		{
			$str.="border=\"".$st["border"]."\" ";
		}
		if ($st["cellpadding"] != "")
		{
			$str.="cellpadding=\"".$st["cellpadding"]."\" ";
		}
		if ($st["cellspacing"] != "")
		{
			$str.="cellspacing=\"".$st["cellspacing"]."\" ";
		}
		if ($st["height"] != "")
		{
			$str.="height=\"".$st["height"]."\" ";
		}
		if ($st["width"] != "")
		{
			$str.="width=\"".$st["width"]."\" ";
		}
		if ($st["hspace"] != "")
		{
			$str.="hspace=\"".$st["hspace"]."\" ";
		}
		if ($st["vspace"] != "")
		{
			$str.="vspace=\"".$st["vspace"]."\" ";
		}

		return $str;
	}

	function get_cell_begin_str($id,$colspan = -1, $rowspan = -1)
	{
		$st = $this->mk_cache($id);

		$fstr = array();
		if ($st["font1"] != "")		$fstr[] = $st["font1"];
		if ($st["font2"] != "")		$fstr[] = $st["font2"];
		if ($st["font3"] != "")		$fstr[] = $st["font3"];
		$fstr = join(",", $fstr);
		$fstyles = array();
		if ($fstr != "")
		{
			$fstyles[] = "face=\"".$fstr."\"";
		}

		if ($st["fontsize"])
		{
			$fstyles[] = "size=\"".$st["fontsize"]."\"";
		}

		if ($st["color"] != "")
		{
			$fstyles[] = "color=\"".$st["color"]."\"";
		}

		$fsstr = join(" ",$fstyles);
		if ($fsstr != "")
		{
			$str = "<font ".$fsstr.">";
		}

		if (isset($st["bgcolor"]) && $st["bgcolor"] != "")
		{
			$cstyles[] = "bgcolor=\"".$st["bgcolor"]."\"";
		}
		if (isset($st["align"]) && $st["align"] != "")
		{
			$cstyles[] = "align=\"".$st["align"]."\"";
		}
		if (isset($st["valign"]) && $st["valign"] != "")
		{
			$cstyles[] = "valign=\"".$st["valign"]."\"";
		}
		if (isset($st["height"]) && $st["height"] != "")
		{
			$cstyles[] = "height=\"".$st["height"]."\"";
		}
		if (isset($st["width"]) && $st["width"] != "")
		{
			$cstyles[] = "width=\"".$st["width"]."\"";
		}
		if (isset($st["nowrap"]) && $st["nowrap"] != "")
		{
			$cstyles[] = "nowrap";
		}
		if ($colspan > 0)
		{
			$cstyles[] = "colspan=\"".$colspan."\"";
		}
		if ($rowspan > 0)
		{
			$cstyles[] = "rowspan=\"".$rowspan."\"";
		}

		if ($st["fontstyle"] == "bold")
		{
			$str.="<b>";
		}
		else
		if ($st["fontstyle"] == "italic")
		{
			$str.="<i>";
		}
		else
		if ($st["fontstyle"] == "underline")
		{
			$str.="<u>";
		}

		$cstr = join(" ",$cstyles);
		$cssc = "";
		if (isset($st["css_class"]) && $st["css_class"] != "")
		{
			$cssc = " class=\"".$st["css_class"]."\" ";
		}
		if ($cstr != "")
		{
			$cell = "<td ".$cssc.$cstr.">".$str;
		}
		else
		{
			$cell = "<td".$cssc.">".$str;
		}

		return $cell;
	}

	function get_cell_end_str($id)
	{
		$st = $this->mk_cache($id);

		$str = "";
		if ($st["fontstyle"] == "bold")
		{
			$str = "</b>";
		}
		else
		if ($st["fontstyle"] == "italic")
		{
			$str = "</i>";
		}
		else
		if ($st["fontstyle"] == "underline")
		{
			$str = "</u>";
		}

		if ($st["font1"] != "" || $st["font2"] != "" || $st["font3"] != "" || $st["fontsize"] || $st["color"] != "")
		{
			$str.= "</font>";
		}

		return $str;
	}

	function get_frow_style($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
			if (!is_oid($id) || !$this->can("view", $id))
			{
				return false;
			}
			$tmp = obj($id);
			return $tmp->prop("frow_style");
		}

		return $st["frow_style"];
	}

	function get_fcol_style($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
			if (!is_oid($id) || !$this->can("view", $id))
			{
				return false;
			}
			$tmp = obj($id);
			return $tmp->prop("fcol_style");
		}

		return $st["fcol_style"];
	}

	function get_num_frows($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
                        if (!is_oid($id) || !$this->can("view", $id))
                        {
                                return false;
                        }
			$tmp = obj($id);
			return $tmp->prop("num_frows");
		}

		return $st["num_frows"];
	}

	function get_num_fcols($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
                        if (!is_oid($id) || !$this->can("view", $id))
                        {
                                return false;
                        }
			$tmp = obj($id);
			return $tmp->prop("num_fcols");
		}

		return $st["num_fcols"];
	}

	function get_lrow_style($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
                        if (!is_oid($id) || !$this->can("view", $id))
                        {
                                return false;
                        }
			$tmp = obj($id);
			return $tmp->prop("lrow_style");
		}

		return $st["lrow_style"];
	}

	function get_lcol_style($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
                        if (!is_oid($id) || !$this->can("view", $id))
                        {
                                return false;
                        }
			$tmp = obj($id);
			return $tmp->prop("lcol_style");
		}

		return $st["lcol_style"];
	}

	function get_num_lrows($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
                        if (!is_oid($id) || !$this->can("view", $id))
                        {
                                return false;
                        }
			$tmp = obj($id);
			return $tmp->prop("num_lrows");
		}

		return $st["num_lrows"];
	}

	function get_num_lcols($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
                        if (!is_oid($id) || !$this->can("view", $id))
                        {
                                return false;
                        }
			$tmp = obj($id);
			return $tmp->prop("num_lcols");
		}

		return $st["num_lcols"];
	}

	function get_text_begin_str($id)
	{
		$st = $this->mk_cache($id);

		$fstr = array();
		if ($st["font1"] != "")		$fstr[] = $st["font1"];
		if ($st["font2"] != "")		$fstr[] = $st["font2"];
		if ($st["font3"] != "")		$fstr[] = $st["font3"];
		$fstr = join(",", $fstr);
		if ($fstr != "")
		{
			$fstyles[] = "face=\"".$fstr."\"";
		}

		if ($st["fontsize"] != "")
		{
			$fstyles[] = "size=\"".$st["fontsize"]."\"";
		}

		if ($st["color"] != "")
		{
			$fstyles[] = "color=\"".$st["color"]."\"";
		}

		$fsstr = join(" ",$fstyles);
		if ($fsstr != "")
		{
			$str = "<font ".$fsstr.">";
		}

		if ($st["fontstyle"] == "bold")
		{
			$str.="<b>";
		}
		else
		if ($st["fontstyle"] == "italic")
		{
			$str.="<i>";
		}
		else
		if ($st["fontstyle"] == "underline")
		{
			$str.="<u>";
		}

		return $str;
	}

	function get_text_end_str($id)
	{
		$st = $this->mk_cache($id);

		if ($st["fontstyle"] == "bold")
		{
			$str = "</b>";
		}
		else
		if ($st["fontstyle"] == "italic")
		{
			$str = "</i>";
		}
		else
		if ($st["fontstyle"] == "underline")
		{
			$str = "</u>";
		}

		if ($st["font1"] != "" || $st["font2"] != "" || $st["font3"] != "" || $st["fontsize"] != "" || $st["color"] != "")
		{
			$str.= "</font>";
		}

		return $str;
	}

	function get_header_style($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
			$tmp = obj($id);
			return $tmp->prop("header_style");
		}
		return $st["header_style"];
	}

	function get_footer_style($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
			$tmp = obj($id);
			return $tmp->prop("footer_style");
		}
		return $st["footer_style"];
	}

	function get_odd_style($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
			if (!is_oid($id) || !$this->can("view", $id))
			{
				return false;
			}
			$tmp = obj($id);
			return $tmp->prop("odd_style");
		}
		return $st["odd_style"];
	}

	function get_even_style($id)
	{
		$st = $this->mk_cache($id);
		if ($st["class_id"] == CL_CSS)
		{
			if (!is_oid($id) || !$this->can("view", $id))
			{
				return false;
			}
			$tmp = obj($id);
			return $tmp->prop("even_style");
		}
		return $st["even_style"];
	}

	function get_css_class($id)
	{
		$st = $this->mk_cache($id);
		return $st["css_class"];
	}

	function mk_cache($id)
	{
		if (aw_cache_get("style_cache",$id))
		{
			$stl = aw_cache_get("style_cache",$id);
		}
		else
		{
			$st = $this->get($id);
			$stl = unserialize($st["style"]);
			$stl["name"] = $st["name"];
			$stl["class_id"] = $st["class_id"];
			$stl["oid"] = $st["oid"];
			aw_cache_set("style_cache",$id,$stl);
		}
		return $stl;
	}

	function _get_css($st)
	{
		$fstr = array();
		if ($st["font1"] != "")
		{
			$fstr[] = $st["font1"];
		}
		if ($st["font2"] != "")
		{
			$fstr[] = $st["font2"];
		}
		if ($st["font3"] != "")
		{
			$fstr[] = $st["font3"];
		}
		$fstyles = array();
		$fstr = join(",", $fstr);
		if ($fstr != "")
		{
			$fstyles[] = "font-family: ".$fstr.";";
		}

		if ($st["fontsize"] != "")
		{
			$fstyles[] = "font-size: ".(4+$st["fontsize"]*3)."pt;";
		}

		if ($st["color"] != "")
		{
			$fstyles[] = "color: ".$st["color"].";";
		}

		if ($st["fontstyle"] == "bold")
		{
			$fstyles[] = "font-weight: bold;";
		}
		else
		if ($st["fontstyle"] == "italic")
		{
			$fstyles[] = "font-style: italic;";
		}
		else
		if ($st["fontstyle"] == "underline")
		{
			$fstyles[] = "text-decoration: underline;";
		}

		if (isset($st["bgcolor"]) && $st["bgcolor"] != "")
		{
			$fstyles[] = "background-color: ".$st["bgcolor"].";";
		}
		if (isset($st["align"]) && $st["align"] != "")
		{
			$fstyles[] = "text-align: ".$st["align"].";";
		}
		if (isset($st["valign"]) && $st["valign"] != "")
		{
			$fstyles[] = "vertical-align: ".$st["valign"].";";
		}
		if (isset($st["height"]) && $st["height"] != "")
		{
			$fstyles[] = "height: ".$st["height"].";";
		}
		if (isset($st["width"]) && $st["width"] != "")
		{
			$fstyles[] = "width: ".$st["width"].";";
		}

		if (isset($st["nowrap"]) && $st["nowrap"] == 1)
		{
			$fstyles[] = "white-space: nowrap;";
		}

		if (isset($st["css_text"]) && $st["css_text"] != "")
		{
			$fstyles[] = $st["css_text"];
		}
		return  join("\n",$fstyles);
	}

	////
	// !returns the css definition that matches style $id
	function get_css($id,$a_id = 0)
	{
		$st = $this->mk_cache($id);

		$fsstr = $this->_get_css($st);
		if ($fsstr != "")
		{
			$str = ".style_".$id." { \n".$fsstr." \n} \n";
		}

		if ($a_id)
		{
			$sta = $this->mk_cache($a_id);

			$fsstr = $this->_get_css($sta);
			if ($fsstr != "")
			{
				$str .= "\n.style_".$id." a { \n".$fsstr." \n} \n";

				if ($sta['visited'])
				{
					$vst = $this->mk_cache($sta['visited']);
					$visstr = $this->_get_css($vst);
					if ($visstr != "")
					{
						$str .= ".style_".$id." a:visited { \n".$visstr." \n} \n";
					}
				}
			}
		}
		return $str;
	}

	function _serialize($arr)
	{
		extract($arr);
		$this->db_query("SELECT objects.*, styles.* FROM objects LEFT JOIN styles ON styles.id = objects.oid WHERE oid = $oid");
		$row = $this->db_next();
		if (!$row)
		{
			return false;
		}
		return serialize($row);
	}

	function _unserialize($arr)
	{
		extract($arr);

		$row = unserialize($str);
		// basically, we create a new object and insert the stuff in the array right back in it.
		$o = obj();
		$o->set_parent($parent);
		$o->set_name($row["name"]);
		$o->set_class_id(CL_STYLE);
		$o->set_comment($row["comment"]);
		$o->set_status($row["status"]);
		$o->set_ord($row["jrk"]);
		$o->set_alias($row["alias"]);
		$oid = $o->save();

		// same with the style.
		$this->quote(&$row);
		$this->db_query("INSERT INTO	styles(id,style,type) values($oid,'".$row["style"]."','".$row["type"]."')");

		return $oid;
	}

	////////////////////////////////////
	// new sty le management methods

	function get_table_style_picker()
	{
		$aret = $this->get_select(0, ST_TABLE, true);
		$ol = new object_list(array(
			"class_id" => CL_CSS,
			"lang_id" => array(),
			"sort_by" => "objects.jrk,objects.name",
			"site_id" => array()
		));
		foreach($ol->names() as $id => $nm)
		{
			$aret[$id] = "CSS: ".$nm;
		}

		$ol = new object_list(array(
			"oid" => array_keys($aret),
			"sort_by" => "objects.site_id,objects.class_id",
			"lang_id" => array(),
			"site_id" => array()
		));

		$ret = array("" => "");
		$sl = get_instance("install/site_list");
		foreach($ol->arr() as $o)
		{
			$pt = obj($o->parent());
			$ret[$o->id()] = $sl->get_url_for_site($o->site_id()).": ".$aret[$o->id()];
		}

		return $ret;
	}

	function apply_style_to_text($st, $text, $param)
	{
		if (!is_oid($st) || !$this->can("view", $st))
		{
			return $text;
		}

		$o = obj($st);
		if ($o->class_id() == CL_CSS)
		{
			active_page_data::add_site_css_style($st);
			return "<span class=\"".$this->get_style_name($st)."\">$text</a>";
		}
		else
		{
			$tmp = false;

			if ($param["is_header"])
			{
				$tmp = $this->get_header_style($st);
			}
			else
			if ($param["is_footer"])
			{
				$tmp = $this->get_footer_style($st);
			}
			else
			if ($param["is_odd"])
			{
				$tmp = $this->get_odd_style($st);
			}
			else
			if ($param["is_even"])
			{
				$tmp = $this->get_even_style($st);
			}

			$st = $tmp;

			if (is_oid($st) && $this->can("view", $st))
			{
				active_page_data::add_serialized_css_style($this->get_css($st));
				return "<span class=\"".$this->get_style_name($st)."\">$text</a>";
			}
		}
		return $text;
	}

	function get_style_name($id)
	{
		if (!is_oid($id) || !$this->can("view", $id))
		{
			return "";
		}
		$o = obj($id);
		if (($nm = trim($o->prop("site_css"))) != "")
		{
			return $nm;
		}
		return "st".$id;
	}
}
