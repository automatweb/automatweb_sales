<?php
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/table.aw,v 1.2 2008/06/03 09:25:34 kristo Exp $
// table.aw - tabelite haldus
/*
@classinfo  maintainer=kristo
*/
class table extends aw_template
{
	var $table_id;
	var $table_name;
	var $arr;
			
	function table()
	{
		$this->init("table_gen");
		$this->sub_merge = 1;
		$this->table_loaded = false;
		lc_load("definition");
		$this->lc_load("table","lc_table");
	}

	////
	// !Parsib ntx dokumendi sees olevaid tabelite aliasi lahti
	function parse_alias($args = array())
	{
		extract($args);
		if ($matches[4] == "v")
		{
			$align = "left";
		}
		if ($matches[4] == "k")
		{
			$align = "center";
		}
		if ($matches[4] == "p")
		{
			$align = "right";
		}
		// if oid is in the arguments check whether that object is attached to 
		// this document and display it instead of document
		$oid = aw_global_get("oid");
		if ($oid)
		{
			$q = "SELECT * FROM aliases WHERE source = '$id' AND target = '$oid' AND type =" . CL_FILE;
			$this->db_query($q);
			$row = $this->db_next();
			if ($row)
			{
				$fi = get_instance(CL_FILE);
				$fl = $fi->get_file_by_id($oid);
				return $fl["content"];
			};
		}
		$replacement = $this->show(array("id" => $alias["target"],"align" => $align));
		return $replacement;
	}

	////
	// !Generates the navigational menu for the table generator
	function gen_navbar($args = array())
	{
		extract($args);
		$xm = new xmlmenu();
		$basedir = $this->cfg["basedir"];
		$links = array(
			"change_url" => $this->mk_my_orb("change", array("id" => $this->id)),
			"edit_url" => $this->mk_my_orb("styles", array("id" => $this->id)),
			"config_url" => $this->mk_my_orb("configure",array("id" => $this->id)),
			"admin_url" => $this->mk_my_orb("admin", array("id" => $this->id)),
			"preview_url" => $this->mk_my_orb("view", array("id" => $this->id)),
			"import_url" => $this->mk_my_orb("gen_import", array("id" => $this->id)),
		);
		if (file_exists($this->template_dir . "/navigation.tpl"))
		{
			$tpl = $this->template_dir . "/navigation.tpl";
		}
		else
		{
			$tpl = $this->adm_template_dir . "/navigation.tpl";
		}
		$retval = $xm->build_menu(array(
			"vars"  => array_merge($vars,$links),
			"xml"   => $basedir . "/xml/tablegen_menu.xml",
			"tpl"   => $tpl,
			"activelist" => $activelist,
		));
		return $retval;
	}

	////
	// !Generates a proper YAH link based on context
	function gen_yah()
	{
		$this->mk_path($this->table_parent,LC_TABLE_CHANGE_TABLE);
	}
		
	function load_table($id)
	{
		if ($this->table_loaded)
		{
			return;
		}

		if (not($id))
		{
			return;
		}

		$this->id = $id;

		$q = "select aw_tables.*, objects.*	from aw_tables 
										LEFT JOIN objects on objects.oid = aw_tables.id 
											where aw_tables.id = $id";
		$this->db_query($q);
		if (!($row = $this->db_next()))
		{
			$this->raise_error(ERR_TBL_NO_TBL,sprintf(t("no such table %s (tables.class->load_table)!"), $id), true);
		}
		
		$this->meta = aw_unserialize($row["metadata"]);
		$this->obj_data = $row;

		$this->arr = aw_unserialize($row["contents"]);

		if ($this->arr["cols"]  < 1 || $this->arr["rows"]  < 1)
		{
			$this->arr["cols"] =1;
			$this->arr["map"][0][0] = array("row" => 0, "col" => 0);
			$this->arr["rows"] = 1;
		}
		$this->table_name = $row["name"];
		$this->table_comment = $row["comment"];
		$this->table_id = $id;
		$this->table_parent = $row["parent"];

		// $this->table_loaded = true;
	}

	/**  
		
		@attrib name=aliases params=name default="0"
		
		@param id required type=int
		
		@returns
		
		
		@comment

	**/
	function aliases($args = array())
	{
		$this->read_template("table_aliases.tpl");
		extract($args);
		$this->vars(array(
			"change"	=> $this->mk_orb("change", array("id" => $id)),
			"styles"	=> $this->mk_orb("styles", array("id" => $id)),
			"aliases" => $this->mk_my_orb("aliases",array("id" => $id)),
			"admin"	=> $this->mk_orb("admin", array("id" => $id)),
			"import"	=> $this->mk_orb("gen_import", array("id" => $id)),
			"aliasmgr_link" => $this->mk_my_orb("list_aliases",array("id" => $id),"aliasmgr"),
			"view"		=> $this->mk_orb("view", array("id" => $id)),
		));
		return $this->parse();
	}

	/** Table configuration form 
		
		@attrib name=configure params=name default="0"
		
		@param id required type=int acl="view;edit"
		
		@returns
		
		
		@comment

	**/
	function configure($args = array())
	{
		extract($args);
		$this->load_table($id);
		$this->read_template("configure.tpl");
		$menu = $this->gen_navbar(array(
			"activelist" => array("configure"),
		));
		$this->gen_yah();

		$st = get_instance(CL_STYLE);
		$stylesel = $st->get_select(0,ST_CELL,true);
		$css = get_instance(CL_CSS);
		$tmp = $css->get_select();
		$stylesel = $stylesel + $tmp;

		$this->vars(array(
			"menu" => $menu,
			"aliases" => checked($this->meta["aliases"]),
			"last_changed" => checked($this->meta["last_changed"]),
			"reforb" => $this->mk_reforb("submit_config",array("id" => $id)),
			"table_name" => $this->table_name,
			"table_header" => $this->arr["table_header"],
			"table_footer" => $this->arr["table_footer"],
			"tablestyle" => $this->option_list($this->arr["table_style"], $st->get_select($this->table_parent,ST_TABLE)),
			"defaultstyle" => $this->option_list($this->arr["default_style"], $stylesel),
			"show_title"  => checked($this->arr["show_title"]),
		));
		return $this->parse();
	}

	/** submits table configuration 
		
		@attrib name=submit_config params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_config($args = array())
	{
		extract($args);
		$this->load_table($id);
		$o = obj($id);
		$o->set_meta("aliases", $aliases);
		$o->set_meta("last_changed", $last_changed);
		$o->set_name($table_name);
		$o->save();

		$this->arr["table_footer"] = $table_footer;
		$this->arr["table_header"] = $table_header;
		$this->arr["show_title"] = $show_title;
		$this->arr["table_style"] = $table_style;
		$this->arr["default_style"] = $default_style;
		if ($default_style)
		{
			$default_style = sprintf("%d",$default_style);
			$this->arr["style2class"][$default_style] = $this->db_fetch_field("SELECT class_id FROM objects WHERE oid = '$default_style'","class_id");
		}

		$stl = get_instance(CL_STYLE);
		if ($this->arr["table_style"])
		{
			$tbst = $stl->mk_cache($this->arr["table_style"]);
			$tbst["even_style"] = sprintf("%d",$tbst["even_style"]);
			$tbst["odd_style"] = sprintf("%d",$tbst["odd_style"]);
			$this->arr["style2class"][$tbst["even_style"]] = $this->db_fetch_field("SELECT class_id FROM objects WHERE oid = ".$tbst["even_style"],"class_id");
			$this->arr["style2class"][$tbst["odd_style"]] = $this->db_fetch_field("SELECT class_id FROM objects WHERE oid = ".$tbst["odd_style"],"class_id");
		}
		$this->save_table($args,false);
		$this->_log(ST_AWTABLE, SA_CHANGE, $table_name);
		return $this->mk_my_orb("configure",array("id" => $id));
	}

	/**  
		
		@attrib name=change params=name default="0"
		
		@param id required acl="view;edit"
		@param is_filter optional
		@param filter optional
		
		@returns
		
		
		@comment
		Change

	**/
	function gen_admin_html($arr)
	{
		extract($arr);

		$this->load_table($id);
		$menu = $this->gen_navbar(array(
			"activelist" => array("change"),
		));

		$this->gen_yah();

		$this->read_template("table_modify.tpl");
		session_register("is_filter$id");
		session_register("filter$id");
		
		if ($arr["is_filter"])
		{
			$GLOBALS["is_filter$id"]=1;
			$GLOBALS["filter$id"]=$filter;
		};
			
		if ($GLOBALS["is_filter$id"])
		{
			//echo("isfilter");//dbg
			$col="";
			for($a=-1;$a<$this->arr["cols"];$a++)
			{
				$this->vars(array("text" => ($a > -1)?chr($a+65):""));
				
				$h_header=$this->parse("H_HEADER");
				$this->vars(array("H_HEADER" => $h_header,"BOX"=>"" ,"AREA"=>""));
				$col.=$this->parse("COL");
			};
			$this->vars(array("COL" => $col));
			$this->parse("LINE");
			$flt=get_instance("formgen/search_filter");
			$flt->id=$GLOBALS["filter$id"];
			$flt->__load_data();

			$blah="";
			if (is_array($flt->data["statdata"]))
				foreach($flt->data["statdata"] as $alias => $dta)
				{
					$blah.="#$alias&nbsp;&nbsp;".$dta["display"]."<br />";
				};
			$this->vars(array("extdata" => $blah));
			$extdata=$this->parse("extdata");
		} else
		{
			$this->gen_yah;
			$extdata="";
		};

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$col="";
			if ($GLOBALS["is_filter$id"])
			{
				$this->vars(array("text" => $i+1,"BOX"=>"" ,"AREA"=>""));
				$h_header=$this->parse("H_HEADER");
				$this->vars(array("H_HEADER" => $h_header));
				$col=$this->parse("COL");
			};
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				if (!($spans = $this->get_spans($i, $a)))
				{
					continue;
				}

				$map = $this->arr["map"][$i][$a];

				$cell = $this->arr["contents"][$map["row"]][$map["col"]];
				$scell = $this->arr["styles"][$map["row"]][$map["col"]];
				
				$this->vars(array(
					"text"	=> str_replace("\"","&quot;",$cell["text"]),
					"text2" => $cell["text"],
					"col"		=> $map["col"],
					"row"		=> $map["row"],
					"num_cols"	=> $scell["cols"],
					"num_rows"	=> $scell["rows"]
				));
				if ($scell["rows"] > 1)
				{
					$ba = $this->parse("AREA");
				}
				else
				{
					$ba = $this->parse("BOX");
				}
				
				$this->vars(array("AREA" => $ba, "BOX" => ""));
				$col.=$this->parse("COL");
			}

			$this->vars(array("COL"	=> $col));
			$this->parse("LINE");
		}
		$st = get_instance(CL_STYLE);
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit", array("id" => $id)),
			"table_id" => $id,
			"aliasmgr_link" => $this->mk_my_orb("list_aliases",array("id" => $id),"aliasmgr"),
			"menu" => $menu,
			"extdata" => $extdata,
			"addstyle" => $this->mk_orb("new",array("parent" => $this->table_parent),"style")
		));

		if ($this->meta["aliases"])
		{
			$this->vars(array("aliases" => $this->parse("aliases")));
		};

		$o = obj($this->table_id);
		foreach($o->connections_to() as $c)
		{
			$this->vars(array(
				"url" => $this->mk_orb("list_aliases", array(
					"id" => $c->prop("to")
				),"aliasmgr"),
				"title" => $c->prop("to.name")
			));
			$this->parse("ALIAS_LINK");
		}
		return $this->parse();
	}

	/**  
		
		@attrib name=admin params=name default="0"
		
		@param id required acl="view;edit"
		@param is_filter optional
		@param filter optional
		
		@returns
		
		
		@comment

	**/
	function gen_admin2_html($arr)
	{
		extract($arr);
		$this->load_table($id);
		$menu = $this->gen_navbar(array(
			"activelist" => array("admin"),
		));
		if (!$GLOBALS["is_filter$id"])
		{
			$this->gen_yah();
		};
		
		$this->read_template("admin.tpl");

		for ($col = 0; $col < $this->arr["cols"]; $col++)
		{
			$fc = "";
			if ($col == 0)
			{
				$this->vars(array("add_col" => $this->mk_orb("nadd_col", array("id" => $id, "after" => -1, "num" => 0))));
				$fc = $this->parse("FIRST_C");
			}
			$this->vars(array(
				"FIRST_C" => $fc, 
				"col" => $col,
				"add_col" => $this->mk_orb("nadd_col", array("id" => $id, "after" => $col,"num" => 0)),
				"del_col"	=> $this->mk_orb("ndel_col", array("id" => $id, "col" => $col))
			));
			$this->parse("DC");
		}
		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$col="";
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				if (!($spans = $this->get_spans($i, $a)))
				{
					continue;
				}

				$map = $this->arr["map"][$i][$a];

				$cell = $this->arr["contents"][$map["row"]][$map["col"]];
				$scell = $this->arr["styles"][$map["row"]][$map["col"]];
					
				$this->vars(array(
					"text"	=> str_replace("\"","&quot;",substr($cell["text"],0,10)),
					"col"		=> $map["col"],
					"row"		=> $map["row"],
					"rows"	=> ($scell["rows"] < 1 ? 1 : $scell["rows"]),
					"cols"	=> ($scell["cols"] < 1 ? 15 : $scell["cols"])
				));
				if ($scell["rows"] > 1)
				{
					$ba = $this->parse("AREA");
				}
				else
				{
					$ba = $this->parse("BOX");
				}
				$this->vars(array("AREA" => $ba, "BOX" => ""));
				$col.=$this->parse("COL");
			}

			$fr = "";
			if ($i == 0)
			{
				$this->vars(array("add_row" => $this->mk_orb("nadd_row", array("id" => $id, "after" => -1,"num" => 0))));
				$fr = $this->parse("FIRST_R");
			}
			$this->vars(array(
				"COL"	=> $col,
				"FIRST_R" => $fr,
				"del_row"	=> $this->mk_orb("ndel_row", array("id" => $id, "row" => $i)),
				"add_row"	=> $this->mk_orb("nadd_row", array("id" => $id, "after" => $i, "num" => 0))
			));

			$this->parse("LINE");
		}
		$st = get_instance(CL_STYLE);
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_admin", array("id" => $id)),
			"table_id" => $id,
			"menu" => $menu,
			"addstyle"		=> $this->mk_orb("new",array("parent" => $this->table_parent),"style")
		));

		$o = obj($this->table_id);
		foreach($o->connections_to() as $c)
		{
			$this->vars(array(
				"url" => $this->mk_orb("change", array(
					"id" => $c->prop("to")
				),"doc"),
				"title" => $c->prop("to.name")
			));
			$this->parse("ALIAS_LINK");
		}
		return $this->parse();
	}

	function update_table()
	{
		global $text,$show_title,$footer_bold,$header_bold;
		$this->dequote(&$text);
		for ($i=0; $i < $this->arr["cols"]; $i++)
		{
			for ($a=0; $a < $this->arr["rows"]; $a++)
			{
				$this->arr["contents"][$a][$i]["text"] = $text[$a][$i];
			}
		}
	}
		
	/**  
		
		@attrib name=submit params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit($arr)
	{
		$this->load_table($arr["id"]);

		$this->save_table($arr);
		$this->_log(ST_AWTABLE, SA_CHANGE, $arr['table_name']);
		return $this->mk_orb("change", array("id" => $arr["id"]));
	}

	function save_table($ar, $update=true)
	{
		if ($update)
		{
			$this->update_table();
		}

		$cdelete = array();
		$rdelete = array();
		reset($ar);
		while (list($k,$v) = each($ar))
		{
			if (substr($k,0,3) == 'dc_' && $v==1)
			{
				$cdelete[substr($k,3)] = substr($k,3);
			}
			else
			if (substr($k,0,3) == 'dr_' && $v==1)
			{
				$rdelete[substr($k,3)] = substr($k,3);
			}
		}

		// kustutame tagant-ettepoole, niiet numbrid ei muutuks
		krsort($cdelete,SORT_NUMERIC);
		krsort($rdelete,SORT_NUMERIC);

		reset($cdelete);
		while (list($k,$v) = each($cdelete))
		{
			$this->del_col(array("col" => $v));
		}

		reset($rdelete);
		while (list($k,$v) = each($rdelete))
		{
			$this->del_row(array("row" => $v));
		}

		$ar["str"]=aw_serialize($this->arr, SERIALIZE_XML);
		$this->quote($ar);
		extract($ar);

		$q = "UPDATE aw_tables SET contents = '$str' WHERE id = ".$this->table_id;
		$this->db_query($q);
					
		$tmp = obj($this->table_id);
		$tmp->save();
	}
		
	function save_table_settings($ar)
	{
		extract($ar);

		$this->arr["t_bgcolor"] = $bgcolor;
		$this->arr["t_border"] = $border;
		$this->arr["t_cellpadding"] = $cellpadding;
		$this->arr["t_cellspacing"] = $cellspacing;
		$this->arr["t_height"] = $height;
		$this->arr["t_hspace"] = $hspace;
		$this->arr["t_vspace"] = $vspace;
		$this->arr["def_style"] = $def_style;

		$str=serialize($this->arr);
		$tmp = obj($this->table_id);
		$tmp->set_name($name);
		$tmp->set_comment($comment);
		$tmp->save();
		
		$this->db_query("UPDATE aw_tables SET contents = '$str' WHERE id = ".$this->table_id);
	}

	function do_add_col($arr)
	{
		extract($arr);

		$this->load_table($id);

		if ($num != 0)
		{
			for ($nnn=0; $nnn < $num; $nnn++)
			{
				$this->arr["cols"]++;

				$nm = array();
				for ($row =0; $row < $this->arr["rows"]; $row++)
				{
					for ($col=0; $col <= $after; $col++)
					{
						$nm[$row][$col] = $this->arr["map"][$row][$col];		// copy the left part of the map
					}
				}

				$change = array();
				for ($row = 0; $row < $this->arr["rows"]; $row++)
					for ($col=$after+1; $col < ($this->arr["cols"]-1); $col++)
					{
						if ($this->arr["map"][$row][$col]["col"] > $after)	
						{
							$nm[$row][$col+1]["col"] = $this->arr["map"][$row][$col]["col"]+1;
							$nm[$row][$col+1]["row"] = $this->arr["map"][$row][$col]["row"];
							$change[] = array("from" => $this->arr["map"][$row][$col], "to" => $nm[$row][$col+1]);
						}
						else
						{
							$nm[$row][$col+1] = $this->arr["map"][$row][$col];
						}
					}

				reset($change);
				while (list(,$v) = each($change))
				{
					for ($row=0; $row < $this->arr["rows"]; $row++)
					{
						for ($col=0; $col <= $after; $col++)
						{
							if ($this->arr["map"][$row][$col] == $v["from"])
							{
								$nm[$row][$col] = $v["to"];
							}
						}
					}
				}

				for ($row = 0; $row < $this->arr["rows"]; $row++)
				{
					if ($this->arr["map"][$row][$after] == $this->arr["map"][$row][$after+1])
					{
						$nm[$row][$after+1] = $nm[$row][$after];
					}
					else
					{
						$nm[$row][$after+1] = array("row" => $row, "col" => $after+1);
					}
				}

				$this->arr["map"] = $nm;
			}
		}
		else
		{
			$this->arr["cols"] ++;

			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				for ($col = $this->arr["cols"]-1; $col > $after; $col--)
				{
					$this->arr["contents"][$row][$col] = $this->arr["contents"][$row][$col-1];
				}
			}
			
			if ($after != -1)
			{
				for ($row = 0; $row < $this->arr["rows"]; $row++)
				{
					$this->arr["contents"][$row][$after+1] = "";
				}
			}

			$nm = array();
			for ($row =0; $row < $this->arr["rows"]; $row++)
			{
				for ($col=0; $col <= $after; $col++)
				{
					$nm[$row][$col] = $this->arr["map"][$row][$col];		// copy the left part of the map
				}
			}

			$change = array();
			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				for ($col=$after+1; $col < ($this->arr["cols"]-1); $col++)
				{
					if ($this->arr["map"][$row][$col]["col"] > $after)	
					{
						$nm[$row][$col+1]["col"] = $this->arr["map"][$row][$col]["col"]+1;
						$nm[$row][$col+1]["row"] = $this->arr["map"][$row][$col]["row"];
						$change[] = array("from" => $this->arr["map"][$row][$col], "to" => $nm[$row][$col+1]);
					}
					else
					{
						$nm[$row][$col+1] = $this->arr["map"][$row][$col];
					}
				}
			}

			reset($change);
			while (list(,$v) = each($change))
			{
				for ($row=0; $row < $this->arr["rows"]; $row++)
				{
					for ($col=0; $col <= $after; $col++)
					{
						if ($this->arr["map"][$row][$col] == $v["from"])
						{
							$nm[$row][$col] = $v["to"];
						}
					}
				}
			}

			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				if ($this->arr["map"][$row][$after] == $this->arr["map"][$row][$after+1])
				{
					$nm[$row][$after+1] = $nm[$row][$after];
				}
				else
				{
					$nm[$row][$after+1] = array("row" => $row, "col" => $after+1);
				}
			}

			$this->arr["map"] = $nm;
		}

		$this->save_table(array(), false);
	}

	/**  
		
		@attrib name=add_col params=name default="0"
		
		@param id required acl="view;edit"
		@param after required
		@param num required
		
		@returns
		
		
		@comment

	**/
	function add_col($arr)
	{
		$this->do_add_col($arr);
		header("Location: ".$this->mk_orb("styles", array("id" => $arr["id"])));
	}

	/**  
		
		@attrib name=nadd_col params=name default="0"
		
		@param id required acl="view;edit"
		@param after required
		@param num required
		
		@returns
		
		
		@comment

	**/
	function nadd_col($arr)
	{
		$this->do_add_col($arr);
		header("Location: ".$this->mk_orb("change", array("id" => $arr["id"])));
	}

	function do_add_row($arr)
	{
		extract($arr);
		$this->load_table($id);

		if ($num != 0)
		{
			for ($nnn=0; $nnn < $num; $nnn++)
			{
				$this->arr["rows"]++;

				$nm = array();
				for ($row =0; $row <= $after; $row++)
				{
					for ($col=0; $col < $this->arr["cols"]; $col++)
					{
						$nm[$row][$col] = $this->arr["map"][$row][$col];		// copy the upper part of the map
					}
				}

				$change = array();
				for ($row = $after+1; $row < ($this->arr["rows"]-1); $row++)
				{
					for ($col=0; $col < $this->arr["cols"]; $col++)
					{
						if ($this->arr["map"][$row][$col]["row"] > $after)	
						{
							$nm[$row+1][$col]["col"] = $this->arr["map"][$row][$col]["col"];
							$nm[$row+1][$col]["row"] = $this->arr["map"][$row][$col]["row"]+1;
							$change[] = array("from" => $this->arr["map"][$row][$col], "to" => $nm[$row+1][$col]);
						}
						else
						{
							$nm[$row+1][$col] = $this->arr["map"][$row][$col];
						}
					}
				}

				reset($change);
				while (list(,$v) = each($change))
				{
					for ($row=0; $row <= $after; $row++)
					{
						for ($col=0; $col < $this->arr["cols"]; $col++)
						{
							if ($this->arr["map"][$row][$col] == $v["from"])
							{
								$nm[$row][$col] = $v["to"];
							}
						}
					}
				}

				for ($col = 0; $col < $this->arr["cols"]; $col++)
				{
					if ( $this->arr["map"][$after][$col] == $this->arr["map"][$after+1][$col])
					{
						$nm[$after+1][$col] = $nm[$after][$col];
					}
					else
					{
						$nm[$after+1][$col] = array("row" => $after+1, "col" => $col);
					}
				}

				$this->arr["map"] = $nm;
			}
		}
		else
		{
			$this->arr["rows"] ++;

			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				for ($row = $this->arr["rows"]-1; $row > $after; $row--)
				{
					$this->arr["contents"][$row][$col] = $this->arr["contents"][$row-1][$col];
					$this->arr["styles"][$row][$col] = $this->arr["styles"][$row-1][$col];
				}
			}
			
			if ($after != -1)
			{
				for ($col = 0; $col < $this->arr["cols"]; $col++)
				{
					$this->arr["contents"][$after+1][$col] = "";
					$this->arr["styles"][$after+1][$col] = "";
				}
			}

			$nm = array();
			for ($row =0; $row <= $after; $row++)
			{
				for ($col=0; $col < $this->arr["cols"]; $col++)
				{
					$nm[$row][$col] = $this->arr["map"][$row][$col];		// copy the upper part of the map
				}
			}

			$change = array();
			for ($row = $after+1; $row < ($this->arr["rows"]-1); $row++)
			{
				for ($col=0; $col < $this->arr["cols"]; $col++)
				{
					if ($this->arr["map"][$row][$col]["row"] > $after)	
					{
						$nm[$row+1][$col]["col"] = $this->arr["map"][$row][$col]["col"];
						$nm[$row+1][$col]["row"] = $this->arr["map"][$row][$col]["row"]+1;
						$change[] = array("from" => $this->arr["map"][$row][$col], "to" => $nm[$row+1][$col]);
					}
					else
					{
						$nm[$row+1][$col] = $this->arr["map"][$row][$col];
					}
				}
			}

			reset($change);
			while (list(,$v) = each($change))
			{
				for ($row=0; $row <= $after; $row++)
				{
					for ($col=0; $col < $this->arr["cols"]; $col++)
					{
						if ($this->arr["map"][$row][$col] == $v["from"])
						{
							$nm[$row][$col] = $v["to"];
						}
					}
				}
			}

			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				if ( $this->arr["map"][$after][$col] == $this->arr["map"][$after+1][$col])
				{
					$nm[$after+1][$col] = $nm[$after][$col];
				}
				else
				{
					$nm[$after+1][$col] = array("row" => $after+1, "col" => $col);
				}
			}

			$this->arr["map"] = $nm;
		}

		$this->save_table(array(), false);
	}

	/**  
		
		@attrib name=add_row params=name default="0"
		
		@param id required acl="view;edit"
		@param after required
		@param num required
		
		@returns
		
		
		@comment

	**/
	function add_row($arr)
	{
		$this->do_add_row($arr);
		header("Location: ".$this->mk_orb("styles", array("id" => $arr["id"])));
	}

	/**  
		
		@attrib name=nadd_row params=name default="0"
		
		@param id required acl="view;edit"
		@param after required
		@param num required
		
		@returns
		
		
		@comment

	**/
	function nadd_row($arr)
	{
		$this->do_add_row($arr);
		header("Location: ".$this->mk_orb("change", array("id" => $arr["id"])));
	}

	function do_del_col($arr)
	{
		extract($arr);
		$this->load_table($id);

		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			for ($c = $col+1; $c < $this->arr["cols"]; $c++)
			{
				$this->arr["contents"][$row][$c-1] = $this->arr["contents"][$row][$c];
				$this->arr["styles"][$row][$c-1] = $this->arr["styles"][$row][$c];
			}
		}

		$nm = array();
		for ($row =0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $d_col; $col++)
			{
				$nm[$row][$col] = $this->arr["map"][$row][$col];	// copy the left part of the map
			}
		}

		$changes = array();
		for ($row =0 ; $row < $this->arr["rows"]; $row++)
		{
			for ($col = $d_col+1; $col < $this->arr["cols"]; $col++)
			{
				if ($this->arr["map"][$row][$col]["col"] > $d_col)
				{
					$nm[$row][$col-1] = array("row" => $this->arr["map"][$row][$col]["row"], "col" => $this->arr["map"][$row][$col]["col"]-1);
					$changes[] = array("from" => $this->arr["map"][$row][$col], 
														 "to" => array("row" => $this->arr["map"][$row][$col]["row"], "col" => $this->arr["map"][$row][$col]["col"]-1));
				}
				else
				{
					$nm[$row][$col-1] = $this->arr["map"][$row][$col];
				}
			}
		}
		$this->arr["map"] = $nm;
		
		reset($changes);
		while (list(,$v) = each($changes))
		{
			for ($row=0; $row < $this->arr["rows"]; $row++)
			{
				for ($col=0; $col < $d_col; $col++)
				{
					if ($this->arr["map"][$row][$col] == $v["from"])
					{
						$this->arr["map"][$row][$col] = $v["to"];
					}
				}
			}
		}
		
		$this->arr["cols"]--;
		$this->save_table(array(), false);
	}

	/**  
		
		@attrib name=del_col params=name default="0"
		
		@param id required acl="view;edit"
		@param col required
		
		@returns
		
		
		@comment

	**/
	function del_col($arr)
	{
		$this->do_del_col($arr);
		header("Location: ".$this->mk_orb("styles", array("id" => $arr["id"])));
	}

	/**  
		
		@attrib name=ndel_col params=name default="0"
		
		@param id required acl="view;edit"
		@param col required
		
		@returns
		
		
		@comment

	**/
	function ndel_col($arr)
	{
		$this->do_del_col($arr);
		header("Location: ".$this->mk_orb("change", array("id" => $arr["id"])));
	}

	function do_del_row($arr)
	{
		extract($arr);
		$this->load_table($id);

		for ($col = 0; $col < $this->arr["cols"]; $col++)
		{
			for ($r = $row+1; $r < $this->arr["rows"]; $r++)
			{
				$this->arr["contents"][$r-1][$col] = $this->arr["contents"][$r][$col];
				$this->arr["styles"][$r-1][$col] = $this->arr["styles"][$r][$col];
			}
		}
		
		$nm = array();
		for ($row =0; $row < $d_row; $row++)
		{
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				$nm[$row][$col] = $this->arr["map"][$row][$col];	// copy the upper part of the map
			}
		}

		$changes = array();
		for ($row =$d_row+1 ; $row < $this->arr["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				if ($this->arr["map"][$row][$col]["row"] > $d_row)
				{
					$nm[$row-1][$col] = array("row" => $this->arr["map"][$row][$col]["row"]-1, "col" => $this->arr["map"][$row][$col]["col"]);
					$changes[] = array("from" => $this->arr["map"][$row][$col], 
														 "to" => array("row" => $this->arr["map"][$row][$col]["row"]-1, "col" => $this->arr["map"][$row][$col]["col"]));
				}
				else
				{
					$nm[$row-1][$col] = $this->arr["map"][$row][$col];
				}
			}
		}
		$this->arr["map"] = $nm;
		
		reset($changes);
		while (list(,$v) = each($changes))
		{
			for ($row=0; $row < $d_row; $row++)
			{
				for ($col=0; $col < $this->arr["cols"]; $col++)
				{
					if ($this->arr["map"][$row][$col] == $v["from"])
					{
						$this->arr["map"][$row][$col] = $v["to"];
					}
				}
			}
		}

		$this->arr["rows"]--;
		$this->save_table(array(), false);
	}

	/**  
		
		@attrib name=del_row params=name default="0"
		
		@param id required acl="view;edit"
		@param row required
		
		@returns
		
		
		@comment

	**/
	function del_row($arr)
	{
		$this->do_del_row($arr);
		header("Location: ".$this->mk_orb("styles", array("id" => $arr["id"])));
	}

	/**  
		
		@attrib name=ndel_row params=name default="0"
		
		@param id required acl="view;edit"
		@param row required
		
		@returns
		
		
		@comment

	**/
	function ndel_row($arr)
	{
		$this->do_del_row($arr);
		header("Location: ".$this->mk_orb("change", array("id" => $arr["id"])));
	}

	/**  
		
		@attrib name=styles params=name default="0"
		
		@param id required acl="view;edit"
		
		@returns
		
		
		@comment

	**/
	function gen_styles($arr)
	{
		extract($arr);
		$this->load_table($id);
		$menu = $this->gen_navbar(array(
			"activelist" => array("edit"),
		));

		if (!$GLOBALS["is_filter$id"])
		{
			$this->gen_yah();
		};

		$this->read_template("styles.tpl");
/*			echo "<table border=1>";
		for ($r=0; $r < $this->arr[rows]; $r++)
		{
			echo "<tr>";
			for ($c=0; $c < $this->arr[cols]; $c++)
				echo "<td>(", $this->arr[map][$r][$c][row], ",",$this->arr[map][$r][$c][col],")</td>";
			echo "</tr>";
		}
		echo "</table>";*/

		for ($col = 0; $col < $this->arr["cols"]; $col++)
		{
			$fc = "";
			if ($col == 0)
			{
				$this->vars(array("add_col" => $this->mk_orb("add_col", array("id" => $id, "after" => -1, "num" => 0))));
				$fc = $this->parse("FIRST_C");
			}
			$this->vars(array(
				"FIRST_C" => $fc, 
				"col" => $col,
				"add_col" => $this->mk_orb("add_col", array("id" => $id, "after" => $col,"num" => 0)),
				"del_col"	=> $this->mk_orb("del_col", array("id" => $id, "col" => $col))
			));
			$this->parse("DC");
		}

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			$col="";
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				$cell = $this->arr["styles"][$i][$a];
				
				$this->vars(array(
					"col"		=> $a,
					"row"		=> $i,
					"ps"		=> $this->mk_orb("pick_style",	array("id" => $id, "col" => $a, "row" => $i))
				));

				if (!($spans = $this->get_spans($i, $a)))
				{
					continue;
				}

				$sh = ""; $sv = "";
				if ($spans["rowspan"] > 1)
				{
					$sh = "&nbsp;| <a href='javascript:split_hor($i,$a)'><img alt='" . LC_TABLE_DEVIDE_CELL_HOR . "' src='/automatweb/images/split_cell_down.gif' border=0></a>";
				}
				if ($spans["colspan"] > 1)
				{
					$sv = "<a href='javascript:split_ver($i,$a)'><img alt='" . LC_TABLE_DEVIDE_CELL_VER . "' src='/automatweb/images/split_cell_left.gif' border=0></a>&nbsp;";
				}

				$eu = "";
				if ($i != 0)
				{
					$eu = "<a href='javascript:exp_up($i,$a)'><img border=0 alt='" . LC_TABLE_DELETE_UPPER_CELL . "' src='/automatweb/images/up_r_arr.gif'></a>&nbsp;";
				}
				$el = "";
				if ($a != 0)
				{
					$el = "<a href='javascript:exp_left($i,$a)'><img border=0 alt='" . LC_TABLE_DELETE_LEFT_CELL . "' src='/automatweb/images/left_r_arr.gif'></a>";
				}
				$er = "";
				if (($a+$spans["colspan"]) != $this->arr["cols"])
				{
					$er="<a href='javascript:exp_right($i,$a)'><img border=0 alt='" . LC_TABLE_DELETE_RIGHT_CELL . "' src='/automatweb/images/right_r_arr.gif'></a>";
				}
				$ed = "";
				if (($i+$spans["rowspan"]) != $this->arr["rows"])
				{
					$ed = "<a href='javascript:exp_down($i,$a)'><img border=0 alt='" . LC_TABLE_DELETE_LOWER_CELL . "' src='/automatweb/images/down_r_arr.gif'></a>";
				}

				$map = $this->arr["map"][$i][$a];

				$this->vars(array(
					"SPLIT_HORIZONTAL"	=> $sh, 
					"SPLIT_VERTICAL"		=> $sv, 
					"EXP_UP"						=> $eu, 
					"EXP_LEFT"					=> $el, 
					"EXP_RIGHT"					=> $er,
					"EXP_DOWN"					=> $ed,
					"text"							=> substr($this->arr["contents"][$map["row"]][$map["col"]]["text"],0,10)
				));
			
				$col.=$this->parse("COL");
			}
			$fr = "";
			if ($i == 0)
			{
				$this->vars(array("add_row" => $this->mk_orb("add_row", array("id" => $id, "after" => -1,"num" => 0))));
				$fr = $this->parse("FIRST_R");
			}
			$this->vars(array(
				"COL"	=> $col,
				"FIRST_R" => $fr,
				"del_row"	=> $this->mk_orb("del_row", array("id" => $id, "row" => $i)),
				"add_row"	=> $this->mk_orb("add_row", array("id" => $id, "after" => $i, "num" => 0))
			));
				
			$this->parse("LINE");
		}
		$st = get_instance(CL_STYLE);
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_styles", array("id" => $id)),
			"table_id" => $id,
			"rows"	=> $this->arr["rows"],
			"menu" => $menu,
			"cols"	=> $this->arr["cols"],
			"addstyle"		=> $this->mk_orb("new",array("parent" => $this->table_parent),"style")
		));
		$o = obj($this->table_id);
		foreach($o->connections_to() as $c)
		{
			$this->vars(array(
				"url" => $this->mk_orb("change", array(
					"id" => $c->prop("to")
				),"doc"),
				"title" => $c->prop("to.name")
			));
			$this->parse("ALIAS_LINK");
		}
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_styles params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_styles($arr)
	{
		extract($arr);

		$this->load_table($id);

		$this->save_table($arr, false);

		return $this->mk_orb("styles", array("id" => $id));
	}

	/**  
		
		@attrib name=submit_admin params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_admin($arr)
	{
		extract($arr);

		$this->load_table($id);

		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			for ($col=0; $col < $this->arr["cols"];  $col++)
			{
				$this->arr["styles"][$row][$col]["rows"] = $rows[$row][$col];
				$this->arr["styles"][$row][$col]["cols"] = $cols[$row][$col];
			}
		}
		$this->save_table($arr, false);

		$this->_log(ST_AWTABLE, SA_CHANGE, $this->table_name);
		return $this->mk_orb("admin", array("id" => $id));
	}

	/**  
		
		@attrib name=pick_style params=name default="0"
		
		@param id required acl="view;edit"
		
		@returns
		
		
		@comment

	**/
	function pick_style($arr)
	{
		extract($arr);
		global $row, $col, $frow, $fcol;

		$this->load_table($id);

		$oarr = array("id" => $id);
		if (isset($row))
		{
			$oarr["row"] = $row;
		}
		if (isset($col))
		{
			$oarr["col"] = $col;
		}
		$ro = $this->mk_reforb("submit_pickstyle", $oarr);
		if (is_array($frow))
		{
			reset($frow);
			while(list(,$v) = each($frow))
			{
				$ro.="\n<input type='hidden' name='frow[]' value='$v'>";
			}
		}
		if (is_array($fcol))
		{
			reset($fcol);
			while(list(,$v) = each($fcol))
			{
				$ro.="<input type='hidden' name='fcol[]' value='$v'>";
			}
		}
		$this->read_template("pickstyle.tpl");

		$st = get_instance(CL_STYLE);
		$stylesel = $st->get_select(0,ST_CELL,true);
		$css = get_instance(CL_CSS);
		$tmp = $css->get_select();
		$stylesel = $stylesel + $tmp;

		$this->vars(array(
			"stylessel" => $this->option_list($this->arr["styles"][$row][$col]["style"], $stylesel),
			"reforb"	=> $ro
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_pickstyle params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_pickstyle($arr)
	{
		extract($arr);
		$this->load_table($id);

		$this->arr["style2class"][$style] = $this->db_fetch_field("SELECT class_id FROM objects WHERE oid = $style","class_id");

		if (isset($col) && isset($row))
		{
			$this->arr["styles"][$row][$col]["style"] = $style;
		}

		if (is_array($frow))
		{
			reset($frow);
			while (list(,$v) = each($frow))
			{
				for ($col=0; $col < $this->arr["cols"]; $col++)
				{
					$this->arr["styles"][$v][$col]["style"] = $style;
				}
			}
		}

		if (is_array($fcol))
		{
			reset($fcol);
			while (list(,$v) = each($fcol))
			{
				for ($row=0; $row < $this->arr["rows"]; $row++)
				{
					$this->arr["styles"][$row][$v]["style"] = $style;
				}
			}
		}

		$this->save_table(array(), false);

		return $this->mk_orb("pick_style", array("id" => $id, "row" => $row, "col" => $col));
	}

	
	function _get_rc($str,&$row,&$col)
	{
		$row=$col="";
		$str=strtoupper($str);
		$lstr=strlen($str);
		$i=0;
		while ($i<$lstr && $str[$i]!="," && $str[$i]!=")")
		{
			$oc=ord($str[$i]);
			if ($oc>=48 && $oc<=57)
			{
				$row.=$str[$i];
			}//number
			else
			{
				$col.=$str[$i];
			};//char
			$i++;
		};
		$col=ord($col)-65;
		$row-=1;
		$r2=$this->arr["map"][$row][$col]["row"];
		$c2=$this->arr["map"][$row][$col]["col"];
		$row=$r2;
		$col=$c2;
		
		return $i;//return the last char idx not matched
	}

	// evalib avaldisi kujul =sum(0.00009265,3.1415,avg(a1,a2,a3,mul(div(b1,sub(c1,8)),10),d2),0,0)
	// a1 jne on nagu excelis col:row 1 based indeksid cellide arraysse
	// lauri kirjutatud jura
	function filter_eval($str)
	{
		//echo("<b>eval=$str</b><br />");//dbg
		if ($str[0]!="=")
		{
			return $str;
		};
		$fnc=strtoupper($str[1].$str[2].$str[3].$str[4]);//v6ta funktsiooni nimi

		$evl="error";
		if ($str[4]=="(")// kui on funktsioon
		{
			// plj22, this is complicated stuff.
			//get parameters for func
			if (!($rightbound=strrpos($str,")")))
			{
				$rightbound=strlen($str);
			};
			$params=substr($str,5,$rightbound-5);
			$parms[0]="";
			$pindex=0;
			$funclevel=0;
			for ($i=5;$i<$rightbound;$i++)
			{
				$dd=1;
				if ($str[$i]=="(")
				{
					$funclevel++;
				} 
				else
				if ($str[$i]==")")
				{
					$funclevel--;
				} 
				else
				if ($str[$i]==",")
				{
					if (!$funclevel)
					{
						$pindex++;
						$dd=0;
					};
				};
				if ($dd)
				{
					if (!isset($parms[$pindex]))
					{
						$parms[$pindex]="";
					}
					$parms[$pindex].=$str[$i];
				};
			};
			

			//echo("parms=<pre>");print_r($parms);Echo("</pre>");//dbg
			foreach ($parms as $k => $val)
			{
				$parms[$k]=$this->filter_eval("=".$val);
				
				//$parms[$k]=(int)$parms[$k];//t2isarvud ainult praegu??
			};
			//echo("parms=<pre>");print_r($parms);Echo("</pre>");//dbg
			$evl=$params;//dbg
			//exec func
			switch ($fnc)
			{
				case "SUM(":
					$evl=0;
					foreach($parms as $val)
						$evl+=$val;
					break;
				case "SUB(":
					$evl="a";
					foreach($parms as $val)
					{
						$evl=($evl=="a")?$val:$evl-$val;
					};
					break;
				case "MUL(":
					$evl=1;
					foreach($parms as $val)
					{
						$evl*=$val;
					};
					break;
				case "DIV(":
					$evl="a";
					foreach($parms as $val)
					{
						$evl=($evl=="a")?$val:$evl/$val;
					};
					break;
				case "AVG(":
					$evl=0;
					$cnt=0;
					foreach($parms as $val)
					{
						$evl+=$val;
						$cnt++;
					};
					$evl/=$cnt;
					break;
				case "RND(":
					$evl=rand($parms[0],$parms[1]);
					break;
				// add your own stuph here:

				
				default:
			};
		} 
		else
		if (ord($str[1])>=48 && ord($str[1])<=57) //kui on konstant
		{
			$evl=substr($str,1,strlen($str)-1);
		} 
		else
		if ($str[1]=="#")
		{
			//echo("al ".substr($str,2,strlen($str)-2)."<br />");//dbg
			
			$evl=$this->fl_external[substr($str,2,strlen($str)-2)];
		}
		else //kui on reference nagu g45
		{
			$row="";$col="";
			$this->_get_rc(substr($str,1,strlen($str)-1),&$row,&$col);
			if (isset($this->flcache["$row$col"]))
			{
				$evl=$this->flcache["$row$col"];
			} 
			else
			{
				if (!isset($this->fleval["$row$col"]))//check for infinite recursion
				{
					$this->fleval["$row$col"]=1;
					$evl=$this->flcache["$row$col"]=$this->filter_eval($this->arr["contents"][$row][$col]["text"]);
					unset($this->fleval["$row$col"]);
				} 
				else
				{
					$evl="a field depends on itself!";
				};
			};
		};
		//echo("ans=$evl<br />");//dbg
		return $evl;
	}

	/** Generates the preview for the tablegen (with navigation bar) 
		
		@attrib name=view params=name default="0"
		
		@param id required type=id
		
		@returns
		
		
		@comment

	**/
	function gen_preview($args = array())
	{
		extract($args);
		$content = $this->show($args);
		$menu = $this->gen_navbar(array(
			"activelist" => array("preview"),
		));
		$this->gen_yah();
		$this->read_template("preview.tpl");
		$this->vars(array(
			"content" => $content,
			"menu" => $menu,
		));
		return $this->parse();
	}

	function show($arr)
	{
		extract($arr);
		// if oid is in the arguments check whether that object is attached to 
		// this document and display it instead of document
		$oid = aw_global_get("oid");
		if ($oid)
		{
			$q = "SELECT * FROM aliases WHERE source = '$id' AND target = '$oid' AND type =" . CL_FILE;
			$this->db_query($q);
			$row = $this->db_next();
			if ($row)
			{
				$fi = get_instance(CL_FILE);
				$fl = $fi->get_file_by_id($oid);
				return $fl["content"];
			};
		}
		$this->load_table($id);
		if ($GLOBALS["is_filter$id"])
		{
			$is_filter="1";
		};

		if ($this->arr["show_title"])
		{
			$table = "<b><font size=\"+1\">".$this->table_name."</font></b><br />";
		}
		
		$stc = get_instance(CL_STYLE); 
		$frow_style = 0; 
		$fcol_style = 0; 
		$num_fcols = 0; 
		$num_frows = 0;

		if ($this->arr["table_header"] != "")
		{
			$header = $this->proc_text($this->arr["table_header"]);
		}
		if ($header != "")
		{
			if ($this->arr["table_style"])
			{
				$header_style = $stc->get_header_style($this->arr["table_style"]);
			}

			$table.= $header_style ? $stc->get_text_begin_str($header_style).$header.$stc->get_text_end_str($header_style) : $header;
		}

		if ($arr["align"] != "")
		{
			$al = "align=\"".$arr["align"]."\"";
		}
		if ($this->arr["table_style"])
		{
			$frow_style = $stc->get_frow_style($this->arr["table_style"]);
			$fcol_style = $stc->get_fcol_style($this->arr["table_style"]);
			$num_frows = $stc->get_num_frows($this->arr["table_style"]);
			$num_fcols = $stc->get_num_fcols($this->arr["table_style"]);
			$table.= "<table $al ".$stc->get_table_string($this->arr[table_style]).">";
		}
		else
		{
			$table.= "<table $al>";
		}

		$used_css_styles = array();
		for ($row=0; $row < $this->arr["rows"]; $row++)
		{
			$cs = "";
			for ($col=0; $col < $this->arr["cols"]; $col++)
			{
				if (!($spans = $this->get_spans($row, $col)))
				{
					continue;
				}

				$map = $this->arr["map"][$row][$col];

				$cell = $this->arr["contents"][$map["row"]][$map["col"]];
				$scell = $this->arr["styles"][$map["row"]][$map["col"]];

				$st = 0;
				$est = 0;
				if ($this->arr["table_style"])
				{
					if (($row & 1) > 0)
					{
						$est = $stc->get_odd_style($this->arr["table_style"]);
					}
					else
					{
						$est = $stc->get_even_style($this->arr["table_style"]);
					}
				}

				if ($scell["style"] && $st == 0)
				{
					$st = $scell["style"];
				}
				else
				{
					if ($est)
					{
						$st = $est;
					}
					else
					{
						// tshekime et kui on esimene rida/tulp ja stiili pole m22ratud, siis 
						// v6tame tabeli stiilist, kui see on m22ratud default stiili esimese rea/tulba jaox
						if ($this->arr["table_style"] && $row < $num_frows)
						{
							$st = $frow_style;
						}
						else
						if ($this->arr["table_style"] && $col < $num_fcols)
						{
							$st = $fcol_style;
						}

						// kui tabeli stiilis pold m22ratud stiili v6i ei old esimene rida/tulp, siis v6tame default celli stiili, kui see on
						if ($st == 0 && $this->arr["default_style"])
						{
							$st = $this->arr["default_style"];
						}
						// damn this was horrible
					}
				}

				if ($st)
				{
					if ($this->arr["style2class"][$st] == CL_CSS)
					{
						$cs .= "<td colspan=\"".$spans["colspan"]."\" rowspan=\"".$spans["rowspan"]."\" class=\"st".$st."\">";
						$used_css_styles[$st] = $st;
					}
					else
					{
						$cs.= $stc->get_cell_begin_str($st,$spans["colspan"],$spans["rowspan"]);
					}
				}
				else
				{
					$cs .= "<td colspan=\"".$spans["colspan"]."\" rowspan=\"".$spans["rowspan"]."\">";
				}

				$celltxt = $is_filter ? $this->filter_eval($cell["text"]) : $cell["text"];
				$cs.= $this->proc_text($celltxt);//siin evalida text lauri

				if ($st)
				{
					$cs.= $stc->get_cell_end_str($st);
				}

				$cs.= "</td>";
			}
			$rs.="<tr>".$cs."</tr>";
		}
		$table.=$rs."</table>";

		if ($this->arr["table_footer"] != "")
		{
			$footer = $this->proc_text($this->arr["table_footer"]);		
		}
		if ($this->meta["last_changed"])
		{
			$footer .= "<br /><small><i>Muudetud: <span align='right'>" . $this->time2date($this->obj_data["modified"],4) . "</span></i></small>";
		};
		if ($footer != "")
		{
			if ($this->arr["table_style"])
			{
				$footer_style = $stc->get_footer_style($this->arr["table_style"]);
			}

			$table.= $footer_style ? $stc->get_text_begin_str($footer_style).$footer.$stc->get_text_end_str($footer_style) : $footer;
		}

		$al = get_instance("alias_parser");
		$al->parse_oo_aliases($id,&$table,array());

		$retval = $table;

		$css_file = "";
		$css = get_instance(CL_CSS);
		$used = array();
		foreach($used_css_styles as $stylid => $stylid)
		{
			if ($used[$stylid] != 1)
			{
				$used[$stylid] = 1;
				$css_file .= $css->get_style_data_by_id($stylid);
			}
		}
		if ($css_file != "")
		{
			$retval = "<style type=\"text/css\">".$css_file."</style>\n".$retval;
		}
		return $retval;
	}

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
			return false;
		}
	}

	/**  
		
		@attrib name=exp_right params=name default="0"
		
		@param id required acl="view;edit"
		@param col required
		@param row required
		@param cnt required
		
		@returns
		
		
		@comment

	**/
	function exp_right($arr)
	{
		extract($arr);
		$this->load_table($id);

		// here we must first find the right bound for the area being expanded and use that instead the $row, because
		// that is an arbitrary position in the area really.
		for ($c=0; $c < $cnt; $c++)
		{
			for ($i=$col; $i < $this->arr["cols"]; $i++)
			{
				if ($this->arr["map"][$row][$i] == $this->arr["map"][$row][$col])
				{
					$r=$i;
				}
				else
				{
					break;
				}
			}

			if (($r+1) < $this->arr["cols"])
			{
				for ($a =0; $a < $this->arr["rows"]; $a++)
				{
					if ($this->arr["map"][$a][$r] == $this->arr["map"][$row][$r])
					{
						$this->arr["map"][$a][$r+1] = $this->arr["map"][$row][$r];		// expand the area
					}
				}
			}
		}

		$this->save_table(array(),false);
		header("Location: ".$this->mk_orb("styles",array("id" => $id)));
	}

	/**  
		
		@attrib name=split_ver params=name default="0"
		
		@param id required acl="view;edit"
		@param col required
		@param row required
		
		@returns
		
		
		@comment

	**/
	function split_ver($arr)
	{
		extract($arr);
		$this->load_table($id);

		$lbound = -1;
		for ($i=0; $i < $this->arr["cols"] && $lbound==-1; $i++)
		{
			if ($this->arr["map"][$row][$i] == $this->arr["map"][$row][$col])
			{
				$lbound = $i;
			}
		}

		$rbound = -1;
		for ($i=$lbound; $i < $this->arr["cols"] && $rbound==-1; $i++)
		{
			if ($this->arr["map"][$row][$i] != $this->arr["map"][$row][$col])
			{
				$rbound = $i-1;
			}
		}

		if ($rbound == -1)
		{
			$rbound = $this->arr["cols"]-1;
		}

		$nm = array();
		$center = ($rbound+$lbound)/2;

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				if ($this->arr["map"][$i][$a] == $this->arr["map"][$row][$col])
				{
					if ($this->arr["map"][$i][$a]["col"] < $center)	
					{
						// the hotspot of the cell is on the left of the splitter
						if ($a <= $center)	
						{
							// and we currently are also on the left side then leave it be
							$nm[$i][$a] = $this->arr["map"][$i][$a];
						}
						else
						{
							// and we are on the right side choose a new one
							$nm[$i][$a] = array("row" => $this->arr["map"][$i][$a]["row"], "col" => floor($center)+1);
						}
					}
					else
					{
						// the hotspot of the cell is on the right of the splitter
						if ($a <= $center)
						{
							// and we are on the left side choose a new one
							$nm[$i][$a] = array("row" => $this->arr["map"][$i][$a]["row"], "col" => $lbound);
						}
						else
						{
							// if we are on the same side, use the current value
							$nm[$i][$a] = $this->arr["map"][$i][$a];
						}
					}	
				}
				else
				{
					$nm[$i][$a] = $this->arr["map"][$i][$a];
				}
			}
		}

		$this->arr["map"] = $nm;
		$this->save_table(array(), false);

		header("Location: ".$this->mk_orb("styles",array("id" => $id)));
	}

	/**  
		
		@attrib name=exp_left params=name default="0"
		
		@param id required acl="view;edit"
		@param col required
		@param row required
		@param cnt required
		
		@returns
		
		
		@comment

	**/
	function exp_left($arr)
	{
		extract($arr);
		$this->load_table($id);

		// again, this is the left bound, so we don't need to find it
		for ($c=0; $c < $cnt; $c++)
		{
			if ($col > 0)
			{
				for ($a =0; $a < $this->arr["rows"]; $a++)
				{
					if ($this->arr["map"][$a][$col] == $this->arr["map"][$row][$col])
					{
						$this->arr["map"][$a][$col-1] = $this->arr["map"][$row][$col];		// expand the area
					}
				}
			}
			$col--;
		}

		$this->save_table(array(),false);
		header("Location: ".$this->mk_orb("styles",array("id" => $id)));
	}

	/**  
		
		@attrib name=exp_up params=name default="0"
		
		@param id required acl="view;edit"
		@param col required
		@param row required
		@param cnt required
		
		@returns
		
		
		@comment

	**/
	function exp_up($arr)
	{
		extract($arr);
		$this->load_table($id);

		// here we don't need to find the upper bound, because this always is the upper bound
		// first we must find out the colspan of the current cell and set all the cell above that one to the correct values in the map
		for ($c=0; $c < $cnt; $c++)
		{
			if ($row > 0)
			{
				for ($a=0; $a < $this->arr["cols"]; $a++)
				{
					if ($this->arr["map"][$row][$a] == $this->arr["map"][$row][$col])
					{
						$this->arr["map"][$row-1][$a] = $this->arr["map"][$row][$col];		// expand the area
					}
				}
			}
			$row--;
		}

		$this->save_table(array(),false);
		header("Location: ".$this->mk_orb("styles",array("id" => $id)));
	}

	/**  
		
		@attrib name=split_hor params=name default="0"
		
		@param id required acl="view;edit"
		@param col required
		@param row required
		
		@returns
		
		
		@comment

	**/
	function split_hor($arr)
	{
		extract($arr);
		$this->load_table($id);

		$ubound = -1;
		for ($i=0; $i < $this->arr["rows"] && $ubound==-1; $i++)
		{
			if ($this->arr["map"][$i][$col] == $this->arr["map"][$row][$col])
			{
				$ubound = $i;
			}
		}

		$lbound = -1;
		for ($i=$ubound; $i < $this->arr["rows"] && $lbound==-1; $i++)
		{
			if ($this->arr["map"][$i][$col] != $this->arr["map"][$row][$col])
			{
				$lbound = $i-1;
			}
		}

		if ($lbound == -1)
		{
			$lbound = $this->arr["rows"]-1;
		}

		$nm = array();
		$center = ($ubound+$lbound)/2;

		for ($i=0; $i < $this->arr["rows"]; $i++)
		{
			for ($a=0; $a < $this->arr["cols"]; $a++)
			{
				if ($this->arr["map"][$i][$a] == $this->arr["map"][$row][$col])
				{
					if ($this->arr["map"][$i][$a]["row"] < $center)	
					{
						// the hotspot of the cell is above the splitter
						if ($i <= $center)	
						{
							// and we currently are also above then leave it be
							$nm[$i][$a] = $this->arr["map"][$i][$a];
						}
						else
						{
							// and we are below choose a new one
							$nm[$i][$a] = array("row" => floor($center)+1, "col" => $this->arr["map"][$i][$a]["col"]);
						}
					}
					else
					{
						// the hotspot of the cell is below the splitter
						if ($i <= $center)
						{
							// but we are above, so make new
							$nm[$i][$a] = array("row" => $ubound, "col" => $this->arr["map"][$i][$a]["col"]);
						}
						else
						{
							// if we are on the same side, use the current value
							$nm[$i][$a] = $this->arr["map"][$i][$a];
						}
					}	
				}
				else
				{
					$nm[$i][$a] = $this->arr["map"][$i][$a];
				}
			}
		}

		$this->arr["map"] = $nm;

		$this->save_table(array(),false);
		header("Location: ".$this->mk_orb("styles",array("id" => $id)));
	}

	/**  
		
		@attrib name=exp_down params=name default="0"
		
		@param id required acl="view;edit"
		@param col required
		@param row required
		@param cnt required
		
		@returns
		
		
		@comment

	**/
	function exp_down($arr)
	{
		extract($arr);
		$this->load_table($id);

		for ($c=0; $c < $cnt; $c++)
		{
			// here we must first find the lower bound for the area being expanded and use that instead the $row, because
			// that is an arbitrary position in the area really.
			for ($i=$row; $i < $this->arr["rows"]; $i++)
			{
				if ($this->arr["map"][$i][$col] == $this->arr["map"][$row][$col])
				{
					$r=$i;
				}
				else
				{
					break;
				}
			}

			if (($r+1) < $this->arr["rows"])
			{
				for ($a=0; $a < $this->arr["cols"]; $a++)
				{
					if ($this->arr["map"][$row][$a] == $this->arr["map"][$row][$col])
					{
						$this->arr["map"][$r+1][$a] = $this->arr["map"][$row][$col];		// expand the area
					}
				}
			}
		}

		$this->save_table(array(),false);
		header("Location: ".$this->mk_orb("styles",array("id" => $id)));
	}

	/**  
		
		@attrib name=gen_import params=name default="0"
		
		@param id required
		
		@returns
		
		
		@comment

	**/
	function gen_import($arr)
	{
		extract($arr);
		$this->load_table($id);
		$menu = $this->gen_navbar(array(
			"activelist" => array("import"),
		));
		$this->gen_yah();
		$this->read_template("import.tpl");

		$this->vars(array(
			"reforb"	=> $this->mk_reforb("import", array("id" => $id)),
			"change"	=> $this->mk_orb("change", array("id" => $id)),
			"styles"	=> $this->mk_orb("styles", array("id" => $id)),
			"view"		=> $this->mk_orb("view", array("id" => $id)),
			"menu" => $menu,
			"name"		=> $this->table_name
		));

		return $this->parse();
	}

	function mk_file($file,$separator)
	{
		$f = fopen($file,"r");
		$filestr = fread($f,filesize($file));
		fclose($f);

		$len = strlen($filestr);
		$linearr = array();
		$in_cell = false;
		for ($pos=0; $pos < $len; $pos++)
		{
			if ($filestr[$pos] == "\"")	
			{
				if ($in_cell == false)
				{
					// pole celli sees ja jutum2rk. j2relikult algab quoted cell
					$in_cell = true;
					$line.=$filestr[$pos];
				}
				else
				if ($in_cell == true && ($filestr[$pos+1] == $separator || $filestr[$pos+1] == "\n" || $filestr[$pos+1] == "\r"))
				{
					// celli sees ja jutum2rk ja j2rgmine on kas semikas v6i reavahetus, j2relikult cell l6peb
					$in_cell = false;
					$line.=$filestr[$pos];
				}
				else
				{
					// dubleeritud jutum2rk
					$line.=$filestr[$pos];
				}
			}
			else
			if ($filestr[$pos] == $separator && $in_cell == false)
			{
				// semikas t2histab celli l6ppu aint siis, kui ta pole jutum2rkide vahel
				$in_cell = false;
				$line.=$filestr[$pos];
			}
			else
			if (($filestr[$pos] == "\n" || $filestr[$pos] == "\r") && $in_cell == false)
			{
				// kui on reavahetus ja me pole quotetud celli sees, siis algab j2rgmine rida

				// clearime j2rgneva l2bu ka 2ra
				if ($filestr[$pos+1] == "\n" || $filestr[$pos+1] == "\r")
					$pos++;
				$linearr[] = $line;
				$line = "";
			}
			else
				$line.=$filestr[$pos];
		}
		return $linearr;
	}

	/**  
		
		@attrib name=import params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function import($arr)
	{
		global $fail,$separator;

		if ($fail == "none")
		{
			$this->raise_error(ERR_TBL_IMPORT_NOFILE,t("table->import: No file uploaded!"),true);
		}

		$this->load_table($arr["id"]);

		// siin ei saa file() funktsiooni kasutada, kuna kui reavahetus on jutum2rkide vahel celli sees, siis ei t2henda see rea l6ppu.
		// damnit.
		$file = $this->mk_file($fail,$separator);
		
		$num_rows = count($file);

		reset($file);
		list(,$line) = each($file);
		$num_cols = count(explode($separator,$line));

		$this->table_loaded = false;

		$this->arr["cols"] = 1;
		$this->arr["map"] = array();
		$this->arr["map"][0][0] = array("row" => 0, "col" => 0);
		$this->arr["contents"] = array();
		$this->arr["rows"] = 1;
		$this->save_table(array(), false);
		$this->table_loaded = false;
		$this->load_table($arr["id"]);

		// nini. kuna importimisel clearitaxe kogu tabla 2ra niikuinii, siis v6ime lihtsalt uue mapi teha ju
		$this->arr["cols"] = $num_cols;
		$this->arr["rows"] = $num_rows;
		for ($row = 0; $row < $num_rows; $row++)
		{
			for ($col = 0; $col < $num_cols; $col++)
			{
				$this->arr["map"][$row][$col] = array("row" => $row, "col" => $col);
			}
		}

		reset($file);
		for ($row = 0; $row < $this->arr["rows"]; $row++)
		{
			list(,$line) = each($file);
			$line = chop($line).$separator;

			// ok, so far so good. 
			// a siin hakkab keemia pihta
			// nimelt, kui celli sees on ; m2rk, siis pannaxe sisule " ymber. ja celli sees olevad " m2rgid dubleeritaxe
			$rowarr = array();
			
			$pos = 0;
			$len = strlen($line);
			while ($pos < $len)
			{
				$quoted = false;
				if ($line[$pos] == "\"")
				{
					$quoted = true;
					$pos++;
				}

				$cell_content = "";
				$in_cell = true;
				while ($in_cell && $pos < $len)
				{
					if ($quoted && ($line[$pos] == "\"" && $line[$pos+1] != "\""))	
					{
						// leidsime celli l6pu
						$in_cell = false;
						$pos+=2;
					}
					else
					if ($line[$pos] == "\"" && $line[$pos+1] == "\"")
					{
						// leidsime dubleeritud jutum2rgi
						$cell_content.="\"";
						$pos+=2;
					}
					else
					if (!$quoted && $line[$pos] == $separator)
					{
						// leidsime celli mis pole jutum2rkide vahel, l6pu
						$in_cell= false;
						$pos++;
					}
					else
					{
						$cell_content.=$line[$pos];
						$pos++;
					}
				}
				$rowarr[] = $cell_content;
			}
			reset($rowarr);
			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				list(,$ct) = each($rowarr);
				$ct = str_replace("'", "\"",$ct);
				$this->arr["contents"][$row][$col]["text"] = $ct;
			}
		}
	
		if ($arr["trim"])
		{
			for ($row = 0; $row < $this->arr["rows"]; $row++)
			{
				$filled = false;
				for ($col = 0; $col < $this->arr["cols"]; $col++)
				{
					if ($this->arr["contents"][$row][$col]["text"] != "")
					{
						$filled = true;
					}
				}
				if ($filled)
				{
					$last = $row;
				}
			}
			$this->arr["rows"] = $last+1;

			for ($col = 0; $col < $this->arr["cols"]; $col++)
			{
				$filled = false;
				for ($row = 0; $row < $this->arr["rows"]; $row++)
				{
					if ($this->arr["contents"][$row][$col]["text"] != "")
					{
						$filled = true;
					}
				}
				if ($filled)
				{
					$last = $col;
				}
			}
			$this->arr["cols"] = $last+1;
		}

		$this->save_table(array(),false);

		return $this->mk_orb("change",array("id" => $arr[id]));
	}

	/**  
		
		@attrib name=new params=name default="0"
		
		@param parent required acl="add"
		@param alias_to optional
		@param return_url optional
		
		@returns
		
		
		@comment

	**/
	function add($arr)
	{
		extract($arr);
		$this->mk_path($parent,LC_TABLE_ADD_TABLE);
		$this->read_template("table_add.tpl");
		$this->vars(array(
			"reforb" => $this->mk_reforb("submit_add", array("parent" => $parent, "alias_to" => $alias_to)),
			"name" => $name,
		 ));
		return $this->parse();
	}
	
	/**  
		
		@attrib name=submit_add params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_add($arr)
	{
		extract($arr);
		
		$o = obj();
		$o->set_parent($parent);
		$o->set_name($name);
		$o->set_class_id(CL_TABLE);
		$o->set_comment($comment);
		$this->id = $id = $o->save(); 
		$this->db_query("INSERT INTO aw_tables(id) VALUES($id)");

		if ($alias_to)
		{
			$o = obj($alias_to);
			$o->connect(array(
				"to" => $this->id
			));
		}

		return $this->mk_orb("change", array("id" => $id));
	}

	function proc_text($txt)
	{
		$txt = str_replace("\n", "<br />", $txt);
		if ($txt == " ")
		{
			return "&nbsp;";
		}
		$txt = str_replace("  ", "&nbsp;&nbsp;", $txt);

		if (strpos($txt, "href") === false)
		{
			$txt = preg_replace("/([-.a-zA-Z0-9_]*)@([-.a-zA-Z0-9_]*)/","<a href='mailto:\\1@\\2'>\\1@\\2</a>", $txt);
			$txt = preg_replace("/((\W|^))((http(s?):\/\/)|(www\.))(\S+)/im", "$2<a href=\"http$5://$6$7\" target=\"_blank\">$4$6$7</a>", $txt);
		}
		if ($txt == "")
		{
			$txt = "<img src='".$this->cfg["baseurl"]."/img/trans.gif'>";
		}
		return $txt;
	}

	/**  
		
		@attrib name=add_doc params=name default="0"
		
		@param id required
		@param parent required acl="add"
		
		@returns
		
		
		@comment
		lisab tabla ja teeb doku juurde aliase ka

	**/
	function add_doc($arr)
	{
		extract($arr);
		$doc = new object($id);
		$this->mk_path($parent,"<a href='".$this->mk_orb("change", array("id" => $id),"document")."'>".$doc->name().LC_TABLE_ADD_TABLE);
		$this->read_template("table_add.tpl");
		$this->vars(array("reforb" => $this->mk_reforb("submit_doc", array("parent" => $parent,"id" => $id))));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_doc params=name default="0"
		
		@param id required
		@param parent required
		
		@returns
		
		
		@comment
		saveb tabeli ja lisab aliase

	**/
	function submit_doc($arr)
	{
		extract($arr);
		
		$o = obj();
		$o->set_parent($parent);
		$o->set_name($name);
		$o->set_class_id(CL_TABLE);
		$o->set_comment($comment);
		$tid = $o->save();

		$this->db_query("INSERT INTO aw_tables(id) VALUES($tid)");
		$o = obj($id);
		$o->connect(array(
			"to" => $tid
		));

		return $this->mk_orb("change", array("id" => $tid), "table");
	}

	/**  
		
		@attrib name=delete params=name default="0"
		
		@param id required acl="delete"
		@param parent required
		
		@returns
		
		
		@comment

	**/
	function delete($arr)
	{
		extract($arr);

		$tmp = obj($id);
		$name = $tmp->name();
		$tmp->delete();
		$this->_log(ST_AWTABLE, SA_DELETE, $name);
		header("Location: ".$this->mk_orb("obj_list", array("parent" => $parent),"menuedit"));
	}

	function _serialize($arr)
	{
		extract($arr);
		$this->db_query("SELECT aw_tables.contents as contents,objects.*,aw_tables.idx as tbl_idx, aw_tables.oid as tbl_oid FROM aw_tables LEFT JOIN objects ON objects.oid = aw_tables.id WHERE id = $oid");
		$row = $this->db_next();
		return serialize($row);
	}

	function _unserialize($arr)
	{
		extract($arr);

		$row = aw_unserialize($str);
		$row["parent"] = $parent;
		unset($row["brother_of"]);
		// $row may contain metadata and the query will fail, if that metadata contains apostrophses
		$this->quote($row);
		$row["lang_id"] = aw_global_get("lang_id");

		$o = obj();
		$o->set_parent($row["parent"]);
		$o->set_name($row["name"]);
		$o->set_class_id(CL_TABLE);
		$o->set_comment($row["comment"]);
		$id = $o->save();

		$this->db_query("UPDATE objects SET brother_of = oid WHERE oid = '$id'");
		$this->db_query("INSERT INTO aw_tables(id,contents,idx,oid) VALUES($id,'".$row["contents"]."','".$row["tbl_idx"]."','".$row["tbl_oid"]."')");
		return true;
	}

	function request_execute($obj)
	{
		return $this->show(array(
			"id" => $obj->id()
		));
	}
}


class xmlmenu
{
	// konstruktor
	// argumendid
	// xmldef(text) - menyy definitsioon, XML-is
	// template(text) - template, mida kasutada menyy joonistamiseks
	// vars(array) - array muuutujatest, mis xml-i sees asendada oleks vaja
	function xmlmenu($args = array())
	{
		extract($args);
		$this->tpl = new aw_template();
		$this->xmldef = localparse($xmldef,$vars);
	}

	//// Ehitab menyy
	// argumendid:
	// vars(array) - muutujad
	// xml(string) - path to xml definiton
	// tpl(string) - path to template
	// activelist(array) - list of active items
	function build_menu($args = array())
	{
		extract($args);
		$this->vars($vars);
		$this->load_from_files(array(
			"xml" => $xml,
			"tpl" => $tpl,
		));
		return $this->create(array(
			"activelist" => $activelist,
		));
	}

	
	//// Impordib muutujad, mida kas template voi xml defi sees kasutakse
	function vars($args = array())
	{
		if (is_array($args))
		{
			foreach($args as $key => $val)
			{
				$this->vars[$key] = str_replace("&","&amp;",$val);
			};
		};
		$this->tpl->vars($this->vars);
	}

	////
	// !Laeb vajalikud definitsioonid failist
	// argumendid:
	// xml - xml faili full path
	// tpl - template faili full path
	function load_from_files($args = array())
	{
		extract($args);
		$menudef = $this->tpl->get_file(array(
			"file" => $xml,
		));

		$template = $this->tpl->get_file(array(
			"file" => $tpl,
		));

		$this->load_from_memory(array(
			"template" => $template,
			"xmldef" => $menudef,
		));
	}

	////
	// !Laeb vajalikud definitsioonid m2lust
	// argumendid:
	// xmldef(text) - menyy definitsioon, XML-is
	// template(text) - template, mida kasutada menyy joonistamiseks
	function load_from_memory($args = array())
	{
		$this->tpl->use_template($args["template"]);
		$this->xmldef = localparse($args["xmldef"],$this->vars);
	}
		
	////
	// !Loob menyy
	// argumendid:
	// activelist(array) - array aktiivsetest elementidest
	function create($args = array())
	{
		$nil = array();
		$nil[] = "";
		$this->activelist = array_merge($nil,$args["activelist"]);
		$this->level = 1;

		// genereerime menyy
		$menudefs = aw_unserialize($this->xmldef);
		$this->_gen_menu($menudefs);

		return $this->tpl->parse();
	}

	////
	// !Kutsutakse v2lja create seest, ning kutsub iseennast rekursiivselt v2lja
	function _gen_menu($menudefs = array())
	{
		// foreach-i ei saa rekursiivsete funktsioonide sees kasutada, sest see loob iga
		// kord uue koopia arrayst
		while(list($key,$val) = each($menudefs))
		{
			if ($this->activelist[$this->level] == $key)
			{
				$tpl = "level" . $this->level . "_act";
			}
			else
			{
				$tpl = "level" . $this->level;
			};
			$var = "level" . $this->level;
			$this->tpl->vars(array(
					"link" => $val["link"],
					"caption" => $val["caption"],
				));
			$this->tpl->vars_merge(array($var => $this->tpl->parse($tpl)));
			if (is_array($val["sublinks"]) && ($key == $this->activelist[$this->level]))
			{
				$this->level++;
				$this->_gen_menu($val["sublinks"]);
				$this->level--;
			};
		};
	}
};

?>
