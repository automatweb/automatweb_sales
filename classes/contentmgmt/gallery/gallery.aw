<?php
// gallery.aw - gallery management
// $Header: /home/cvs/automatweb_dev/classes/contentmgmt/gallery/gallery.aw,v 1.10 2008/01/31 13:52:27 kristo Exp $
/*
@classinfo  maintainer=kristo
*/

class gallery extends aw_template
{
	function gallery($id = 0)
	{
		$this->init("gallery");
		$this->sub_merge = 1;
		if ($id)
		{
			$this->load($id,$GLOBALS["page"]);
		}
		$this->lc_load("gallery","lc_gallery");
		lc_load("definition");
	}

	function parse_alias($args = array())
	{
		extract($args);
		global $page,$section;
		$this->matches = $matches;
		$this->load($alias["target"],$page);
		return $this->show(array("page" => $page,"section" => $section));
	}
		
	/** generates the form for adding a gallery 
		
		@attrib name=new params=name default="0"
		
		@param parent required acl="add"
		@param alias_to optional
		
		@returns
		
		
		@comment

	**/
	function add($arr)
	{
		extract($arr);
		$this->read_template("add.tpl");
		$this->mk_path($parent,LC_GALLERY_ADD_GAL);

		$this->vars(array(
			"reforb" => $this->mk_reforb("submit", array("parent" => $parent, "alias_to" => $alias_to))
		));
		return $this->parse();
	}

	/** generates the form for changing a gallery 
		
		@attrib name=change params=name default="0"
		
		@param id required acl="edit;view"
		
		@returns
		
		
		@comment

	**/
	function change($arr)
	{
		extract($arr);
		$o = obj($id);
		$this->read_template("add.tpl");
		$this->mk_path($o->parent(),LC_GALLERY_CHANGE_GAL);

		$this->vars(array(
			"name" => $o->name(), 
			"comment" => $o->comment(),
			"reforb" => $this->mk_reforb("submit", array("id" => $id)),
			"content" => $this->mk_orb("admin", array("id" => $id, "page" => "0"))
		));
		$this->parse("CHANGE");
		return $this->parse();
	}

	/** saves or creates the gallery 
		
		@attrib name=submit params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function csubmit($arr)
	{
		extract($arr);

		if ($id)
		{
			$o = obj($id);
			$o->set_name($name);
			$o->set_comment($comment);
			$o->save();
		}
		else
		{
			$parent = $parent ? $parent : $this->cfg["rootmenu"];
			$o = obj();
			$o->set_parent($parent);
			$o->set_name($name);
			$o->set_comment($comment);
			$o->set_class_id(CL_GALLERY);
			$id = $o->save();

			$this->db_query("INSERT INTO galleries VALUES($id,'')");
			if ($alias_to)
			{
				$o = obj($alias_to);
				$o->connect(array(
					"to" => $id
				));
			}
		}
		return $this->mk_orb("change", array("id" => $id));
	}

	function load($id,$pg)
	{
		if (not($id))
		{
			return;
		}
		$this->db_query("SELECT objects.*, galleries.content as content FROM objects LEFT JOIN galleries ON galleries.id = objects.oid WHERE oid = $id");
		if (!($row = $this->db_next()))
		{
			$this->raise_error(ERR_GAL_NOGAL,sprintf(t("load_gallery(%s): no such gallery!"), $id), true);
		}

		$this->arr = unserialize($row["content"]);
		$this->name = $row["name"]; 
		$this->parent = $row["parent"];
		$this->id = $id;
		$this->comment = $row["comment"];
		$this->vars(array("id" => $id, "name" => $this->name, "comment" => $this->comment));

		if ($this->arr["pages"] < 1)
		{
			$this->arr["pages"] = 1;
		}
		for ($pg = 0; $pg < $this->arr["pages"]; $pg++)
		{
			if ($this->arr[$pg]["rows"] < 1)
			{
				$this->arr[$pg]["rows"] = 1;
			}
			if ($this->arr[$pg]["cols"] < 1)
			{
				$this->arr[$pg]["cols"] = 1;
			}
		}
	}

	/** generates the form for uploading pictures for gallery $id, page $page 
		
		@attrib name=admin params=name default="0"
		
		@param id required acl="edit;view"
		@param page optional
		
		@returns
		
		
		@comment

	**/
	function admin($arr)
	{
		extract($arr);
		if ($page < 1)
		{
			$page = "0";
		}
		$this->read_template("grid.tpl");
		$this->load($id,$page);
		$this->mk_path($this->parent, "<a href='".$this->mk_orb("change", array("id" => $id))."'>Muuda</a> / Sisu");
		
		for ($pg = 0; $pg < $this->arr["pages"]; $pg++)
		{
			$this->vars(array(
				"page" => $pg,
				"to_page" => $this->mk_orb("admin", array("id" => $id, "page" => $pg))
			));
			if ($pg == $page)
			{
				$p.=$this->parse("SEL_PAGE");
			}
			else
			{
				$p.=$this->parse("PAGE");
			}
		}
		$this->vars(array(
			"PAGE" => $p, 
			"SEL_PAGE" => "",
			"add_page" => $this->mk_orb("add_page", array("id" => $id)),
			"del_page" => $this->mk_orb("del_page", array("id" => $id))
		));

		classload("image");
		for ($row = 0; $row < $this->arr[$page]["rows"]; $row++)
		{
			$this->vars(array("row" => $row));
			$c = "";
			for ($col = 0; $col < $this->arr[$page]["cols"]; $col++)
			{
				$cell = $this->arr[$page]["content"][$row][$col];
				$this->vars(array(
					"imgurl" => image::check_url($cell["tnurl"]), 
					"caption" => $cell["caption"], 
					"bigurl" => image::check_url($cell["bigurl"]),
					"col" => $col,
					"date" => $cell["date"],
					"link" => $cell["link"],
					"ord" => $cell["ord"],
					"has_textlink" => checked($cell["has_textlink"]),
					"textlink" => $cell["textlink"],
					'glink' => $cell['glink']
				));
				$b = $cell["bigurl"] != "" ? $this->parse("BIG") : "";
				$h = $cell["tnurl"] != "" ? $this->parse("HAS_IMG") : "";
				$gl = $this->arr['is_automatic_slideshow'] ? $this->parse("IS_AUTOMATIC_GAL") : "";
				$this->vars(array(
					"BIG" => $b,
					"HAS_IMG" => $h,
					"IS_AUTOMATIC_GAL" => $gl
				));
				$c.=$this->parse("CELL");
			}
			$this->vars(array("CELL" => $c));
			$l.=$this->parse("LINE");
		}
		$this->vars(array(
			"LINE" => $l,
			"page" => $page,
			"reforb" => $this->mk_reforb("c_submit", array("id" => $id,"page" => $page)),
			"del_row" => $this->mk_orb("del_row", array("id" => $id, "page" => $page)),
			"del_col" => $this->mk_orb("del_col", array("id" => $id, "page" => $page)),
			"is_slideshow" => checked($this->arr["is_slideshow"]==1),
			"is_automatic_slideshow" => checked($this->arr["is_automatic_slideshow"]==1),
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=add_row params=name default="0"
		
		@param id required acl="edit;view"
		@param page optional
		@param rows optional
		
		@returns
		
		
		@comment

	**/
	function add_rows($arr)
	{
		extract($arr);
		$this->load($id,$page);
		$this->arr[$page]["rows"] += $rows;
		$this->save();
		header("Location: ".$this->mk_orb("admin", array("id" => $id, "page" => $page)));
		die();
	}

	/**  
		
		@attrib name=add_col params=name default="0"
		
		@param id required acl="edit;view"
		@param page optional
		@param cols optional
		
		@returns
		
		
		@comment

	**/
	function add_cols($arr)
	{
		extract($arr);
		$this->load($id,$page);
		$this->arr[$page]["cols"] += $cols;
		$this->save();
		header("Location: ".$this->mk_orb("admin", array("id" => $id, "page" => $page)));
		die();
	}

	/**  
		
		@attrib name=del_col params=name default="0"
		
		@param id required acl="edit;view"
		@param page optional
		
		@returns
		
		
		@comment

	**/
	function del_col($arr)
	{
		extract($arr);
		$this->load($id,$page);
		$this->arr[$page]["cols"]--;
		for ($i=0; $i < $this->arr[$page]["rows"]; $i++)
		{
			$this->arr[$page]["content"][$i][$this->arr[$page]["cols"]] = "";
		}
		$this->save();
		header("Location: ".$this->mk_orb("admin", array("id" => $id, "page" => $page)));
		die();
	}

	/**  
		
		@attrib name=del_row params=name default="0"
		
		@param id required acl="edit;view"
		@param page optional
		
		@returns
		
		
		@comment

	**/
	function del_row($arr)
	{
		extract($arr);
		$this->load($id,$page);
		$this->arr[$page]["rows"]--;
		for ($i=0; $i < $this->arr[$page]["cols"]; $i++)
		{
			$this->arr[$page]["content"][$this->arr[$page]["rows"]][$i] = "";
		}
		$this->save();
		header("Location: ".$this->mk_orb("admin", array("id" => $id, "page" => $page)));
		die();
	}

	function save()
	{
		$content = serialize($this->arr);
		$q = "UPDATE galleries SET content = '$content' WHERE id = $this->id";
		$this->db_query($q);
	}

	/** saves the uploaded pictures for gallery $id, on page $page 
		
		@attrib name=c_submit params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit($arr)
	{
		extract($arr);
		$this->load($id,$page);
		if ($page < 1)
		{
			$page = 0;
		}

		for ($row = 0; $row < $this->arr[$page]["rows"]; $row++)
		{
			for ($col = 0; $col < $this->arr[$page]["cols"]; $col++)
			{
				$t = get_instance(CL_IMAGE);
				$ar = $t->add_upload_image("tn_".$row."_".$col, $this->id, $this->arr[$page]["content"][$row][$col]["tn_id"]);
				if (isset($ar["sz"]))
				{
					$this->arr[$page]["content"][$row][$col]["tn_id"] = $ar["id"];
					$this->arr[$page]["content"][$row][$col]["tnurl"] = image::check_url($ar["url"]);
					$this->arr[$page]["content"][$row][$col]["tnxsize"] = $ar["sz"][0];
					$this->arr[$page]["content"][$row][$col]["tnysize"] = $ar["sz"][1];
				}

				$ar = $t->add_upload_image("im_".$row."_".$col, $this->id, $this->arr[$page]["content"][$row][$col]["im_id"]);
				if (isset($ar["sz"]))
				{
					$this->arr[$page]["content"][$row][$col]["im_id"] = $ar["id"];
					$this->arr[$page]["content"][$row][$col]["bigurl"] = image::check_url($ar["url"]);
					$this->arr[$page]["content"][$row][$col]["xsize"] = $ar["sz"][0];
					$this->arr[$page]["content"][$row][$col]["ysize"] = $ar["sz"][1];
				}

				$var = "caption_".$row."_".$col;
				global $$var;
				$v = str_replace("\\","",$$var);
				$v = str_replace("'","\"",$v);
				$this->arr[$page]["content"][$row][$col]["caption"] = $v;

				$var = "link_".$row."_".$col;
				global $$var;
				$v = str_replace("\\","",$$var);
				$v = str_replace("'","\"",$v);
				$this->arr[$page]["content"][$row][$col]["link"] = $v;

				$var = "date_".$row."_".$col;
				global $$var;
				$v = str_replace("\\","",$$var);
				$v = str_replace("'","\"",$v);
				$this->arr[$page]["content"][$row][$col]["date"] = $v;

				$var = "ord_".$row."_".$col;
				global $$var;
				$v = str_replace("\\","",$$var);
				$v = str_replace("'","\"",$v);
				$this->arr[$page]["content"][$row][$col]["ord"] = $v;

				$var = "has_textlink_".$row."_".$col;
				global $$var;
				$this->arr[$page]["content"][$row][$col]["has_textlink"] = $$var;

				$var = "textlink_".$row."_".$col;
				global $$var;
				$v = str_replace("\\","",$$var);
				$v = str_replace("'","\"",$v);
				$this->arr[$page]["content"][$row][$col]["textlink"] = $v;

				$var = "glink_".$row."_".$col;
				global $$var;
				$this->arr[$page]["content"][$row][$col]["glink"] = $$var;

				$var = "erase_".$row."_".$col;
				global $$var;
				if ($$var == 1)
				{
					$this->arr[$page]["content"][$row][$col] = array();
				}
			}
		}
		$this->arr["is_slideshow"] = $is_slideshow;
		$this->arr["is_automatic_slideshow"] = $is_automatic_slideshow;
		$this->save();
		return $this->mk_orb("admin", array("id" => $id, "page" => $page));
	}

	/** adds a page to the gallery and returns to grid 
		
		@attrib name=add_page params=name default="0"
		
		@param id required acl="edit;view"
		@param page optional
		
		@returns
		
		
		@comment

	**/
	function add_page($arr)
	{
		extract($arr);
		$this->load($id,$page);
		$this->arr["pages"] ++;
		$this->save();
		header("Location: ".$this->mk_orb("admin", array("id" => $id, "page" => $page)));
		die();
	}

	function _sort_cmp($a,$b)
	{
		if ($a["ord"] == $b["ord"])
		{
			return 0;
		}
		return ($a["ord"] < $b["ord"]) ? -1 : 1;
	}

	/**  
		
		@attrib name=show params=name nologin="1" default="0"
		
		@param id optional
		@param page optional
		@param col optional
		@param row optional
		@param section optional
		@param nr optional
		
		@returns
		
		
		@comment

	**/
	function show($page)
	{
		if (is_array($page))
		{
			extract($page);	// via orb call
			//global $page, $id;
			$this->load($id,$page);
		}

		classload("image");
		if ($this->arr["is_automatic_slideshow"] == 1)
		{
			$this->read_template("show_slideshow_automatic.tpl");
			$imgurls = array();
			for ($page = 0; $page < $this->arr["pages"]; $page++)
			{
				for ($row = 0; $row < $this->arr[$page]["rows"]; $row++)
				{
					for ($col = 0; $col < $this->arr[$page]["cols"]; $col++)
					{
						$cell = $this->arr[$page]["content"][$row][$col];
						if ($cell["tnurl"] != "")
						{
							$imgurls[] = $cell;
						}
					}
				}
			}

			usort($imgurls,array($this,"_sort_cmp"));
			$ius = array();
			$rus = array();
			foreach($imgurls as $dat)
			{
				$bigurl = image::check_url($dat["tnurl"]);
				$ius[] = "\"".$bigurl."\"";
				$rus[] = $dat['glink'];
			}

			$this->vars(array(
				"img_urls" => join(",",$ius),
				"ref_urls" => join(",",map("\"%s\"",$rus)),
				"img_url" => image::check_url($this->arr[0]["content"][0][0]["tnurl"])
			));
			return $this->parse();
		}
    	else
		if ((isset($col) && isset($row)) && (!$this->arr["is_slideshow"] || $GLOBALS["show_big"]))
		{
			$this->read_template("show_pic.tpl");
			if (is_array($page))
			{
				$page = (int)$page["page"];
			}
			$cell = $this->arr[$page]["content"][$row][$col];
			$bigurl = image::check_url($cell["bigurl"]);
			$this->vars(array(
				"bigurl" => $bigurl, 
				"caption" => $cell["caption"], 
				"date" => $cell["date"]
			));
		}
		else
		if ($this->arr["is_slideshow"] == 1)
		{
			if ($nr < 1)
			{
				$nr = 0;
			}

			$imgurls = array();
			for ($page = 0; $page < $this->arr["pages"]; $page++)
			{
				for ($row = 0; $row < $this->arr[$page]["rows"]; $row++)
				{
					for ($col = 0; $col < $this->arr[$page]["cols"]; $col++)
					{
						$cell = $this->arr[$page]["content"][$row][$col];
						if ($cell["tnurl"] != "")
						{
							$cell["col"] = $col;
							$cell["row"] = $row;
							$cell["page"] = $page;
							$imgurls[] = $cell;
						}
					}
				}
			}

			usort($imgurls,array($this,"_sort_cmp"));
			$ius = array();
			$cnt=0;
			foreach($imgurls as $dat)
			{
				$bigurl = image::check_url($dat["bigurl"]);
				$tnurl = image::check_url($dat["tnurl"]);
				$caps[$cnt] = $dat["caption"];
				$dates[$cnt] = $dat["date"];
				$xsizes[$cnt] = $dat["xsize"];
				$ysizes[$cnt] = $dat["ysize"];
				$rows[$cnt] = $dat["row"];
				$cols[$cnt] = $dat["col"];
				$pages[$cnt] = $dat["page"];
				$tnurls[$cnt] = $tnurl;
				$ius[$cnt++] = $bigurl;
			}
		
			$this->read_template("show_slideshow.tpl");
			$bigurl = $ius[$nr];
			$cap = $caps[$nr];
			$deit = $dates[$nr];
			$tnurl = $tnurls[$nr];
			$xsize = $xsizes[$nr];
			$ysize = $ysizes[$nr];

			$prev_nr = (($nr - 1) > -1) ? $nr - 1 : $cnt - 1;
			$next_nr = $nr+1 >= $cnt ? 0 : $nr+1;

			$gurl = $this->mk_my_orb("show",array(
				"id" => $id,
				"col" => (string)($cols[$nr]),
				"row" => (string)($rows[$nr]), 
				"page" => $pages[$nr],
				"show_big" => 1
			),"gallery",false,true,"/");

			$this->vars(array(
				"bigurl" => $tnurl,
				"caption" => $cap,
				"date" => $deit,
				"next" => $this->mk_my_orb("show", array("id" => $this->id, "nr" => $next_nr),"",false,true),
				"prev" => $this->mk_my_orb("show", array("id" => $this->id, "nr" => $prev_nr),"",false,true),
				"lurl" => "javascript:rremote('".$gurl."',$xsize,$ysize)",
				"xsize" => $xsize,
				"ysize" => $ysize
			));

			if ($bigurl != "")
			{
				$this->vars(array("HAS_LARGE" => $this->parse("HAS_LARGE")));
			}
			$ret = $this->parse();
			return $ret;
		}
    else
		{
			if ($page < 1)
			{
				$page = 1;
			}
			$this->read_template("show.tpl");

			$align= array("k" => "align=\"center\"", "p" => "align=\"right\"" , "v" => "align=\"left\"" ,"" => "");
			$this->vars(array(
				"align" => $align[$this->matches[4]],
			));
			for ($row = 0; $row < $this->arr[$page-1]["rows"]; $row++)
			{
				$c = "";
				for ($col = 0; $col < $this->arr[$page-1]["cols"]; $col++)
				{
					$cell = $this->arr[$page-1]["content"][$row][$col];
					$xsize = $cell["xsize"] ? $cell["xsize"] : 500;
					$add = $cell["caption"] != "" ? 50 : 0;
					$ysize = $cell["ysize"] ? $cell["ysize"] + $add: 400;
					if ($cell["link"] != "")
					{
						$url = $cell["link"];
						$target="target=\"_blank\"";
					}
					else
					{	
						$gurl = $this->mk_my_orb("show", array(
							"id" => $this->id,
							"col" => $col,
							"row" => $row,
							"page" => $page-1
						),"gallery", false,true,"/");
						$url = "javascript:rremote(\"".$gurl."\",$xsize,$ysize)";
						$target = "";
					}

					// strip the beginning of a posible absolute url
					$tnurl = image::check_url($cell["tnurl"]);
					$this->vars(array(
						"tnurl" => $tnurl, 
						"caption" => $cell["caption"], 
						"date" => $cell["date"],
						"url" => $url,
						"target" => $target,
						"textlink" => $cell["textlink"],
					));
					if ($cell["tnurl"] != "")
					{
						if ($cell["bigurl"] != "" || !$this->is_template("NOLINK_IMAGE")) 
						{
							$_tpl = "IMAGE";
						}
						else
						{
							$_tpl = "NOLINK_IMAGE";
						}
					}
					else
					{
						$_tpl = "EMPTY";
					}

					if ($cell["has_textlink"] && $this->is_template("LINK"))
					{
						$_tpl = "LINK";
					};

					$c .= $this->parse($_tpl);
				}
				$this->vars(array("IMAGE" => $c,"NOLINK_IMAGE" => "", "LINK" => ""));
				$l.=$this->parse("LINE");
			}
		}

		$baseurl = $this->cfg["baseurl"]."/index.".$this->cfg["ext"]."/section=$section";

		for ($pg = 1; $pg <= $this->arr["pages"]; $pg++)
		{
			$this->vars(array("num" => $pg,"url" => $baseurl."/page=$pg"));
			if ($this->is_template("PAGE_SEL") && $pg == $page)
			{
				$p.=$this->parse("PAGE_SEL");
			}
			else
			{
				$p.=$this->parse("PAGE");
			}
		}

		$pr = "";
		if ($page > 1)
		{
			$this->vars(array("url" => $baseurl."/page=".($page-1)));
			$pr = $this->parse("PREVIOUS");
		}
		$nx = "";
		if (($page-1) < ($this->arr["pages"]-1))
		{
			$this->vars(array("url" => $baseurl."/page=".($page+1)));
			$nx = $this->parse("NEXT");
		}

		$this->vars(array(
			"PAGE_SEL" => "", 
			"LINE" => $l,
			"PAGE" => $p,
			"sel_page" => $page,
			"PREVIOUS" => $pr, 
			"NEXT" => $nx,
		));

		if ($this->arr["pages"] > 1)
		{
			$this->vars(array("PAGES" => $this->parse("PAGES")));
		}
		else
		{
			$this->vars(array("PAGES" => ""));
		}
		return $this->parse();
	}

	/**  
		
		@attrib name=del_page params=name default="0"
		
		@param id required acl="edit;view"
		@param page optional
		
		@returns
		
		
		@comment

	**/
	function del_page($arr)
	{
		extract($arr);
		$this->load($id,$page);
		$this->arr["pages"] --;
		$this->save();
		header("Location: ".$this->mk_orb("admin", array("id" => $id, "page" => 0)));
		die();
	}
}
?>
