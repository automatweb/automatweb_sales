<?php
/*
@classinfo  maintainer=kristo
*/

class search_conf extends aw_template 
{
	var $per_page = 10;
	function search_conf()
	{
		$this->init("search_conf");
		lc_load("definition");
		$this->lc_load("search_conf","lc_search_conf");
		lc_site_load("search",&$this);
	}

	function gen_admin($level)
	{
		$lang_id = aw_global_get("lang_id");
		$SITE_ID = $this->cfg["site_id"];

		$c = get_instance("config");
		$conf = unserialize($c->get_simple_config("search_conf"));

		if (!$level)
		{
			$this->read_template("conf1.tpl");
			$this->vars(array(
				"section" => $this->multiple_option_list($conf[$SITE_ID][$lang_id]["sections"],$this->get_menu_list())
			));
			return $this->parse();
		}
		else
		{
			$sarr = $this->get_menu_list();

			$this->read_template("conf2.tpl");
			reset($conf[$SITE_ID][$lang_id]["sections"]);
			while (list(,$v) = each($conf[$SITE_ID][$lang_id]["sections"]))
			{
				$this->vars(array(
					"section" => $sarr[$v],
					"section_id" => $v,
					"section_name" => $conf[$SITE_ID][$lang_id]["names"][$v],
					"order" => $conf[$SITE_ID][$lang_id]["order"][$v]
				));
				$s.= $this->parse("RUBR");
			}
			$this->vars(array("RUBR" => $s));
			return $this->parse();
		}
	}

	function submit($arr)
	{
		$lang_id = aw_global_get("lang_id");
		$SITE_ID = $this->cfg["site_id"];

		extract($arr);

		if (is_array($section))
		{
			reset($section);
			$a = array();
			while (list(,$v) = each($section))
			{
				$a[$v]=$v;
			}
		}

		$c = get_instance("config");
		$conf = unserialize($c->get_simple_config("search_conf"));

		if (!$level)
		{
			$conf[$SITE_ID][$lang_id]["sections"] = $a;
			$c->set_simple_config("search_conf",serialize($conf));
			return 1;
		}
		else
		{
			$conf[$SITE_ID][$lang_id]["names"] = array();
			reset($arr);
			while (list($k,$v) = each($arr))
			{
				if (substr($k,0,3) == "se_")
				{
					$id = substr($k,3);
					$conf[$SITE_ID][$lang_id]["names"][$id] = $v;
				}
			}

			$conf[$SITE_ID][$lang_id]["order"] = array();
			reset($arr);
			while (list($k,$v) = each($arr))
			{
				if (substr($k,0,3) == "so_")
				{
					$id = substr($k,3);
					$conf[$SITE_ID][$lang_id]["order"][$id] = $v;
				}
			}
			$c->set_simple_config("search_conf",serialize($conf));
			return 1;
		}
	}

	function get_search_list(&$default)
	{
		static $m;
		if (!$m)
		{
			$m = get_instance("contentmgmt/site_search/old_search_model");
		}
		return $m->get_search_list(&$default);
	}

	/** shows the search form 
		
		@attrib name=search params=name nologin="1" default="0"
		
		@param s_parent optional
		@param s_parent_arr optional
		@param s_keywords optional
		@param sstring_title optional
		@param sstring_author optional
		@param date_from optional
		@param date_to optional
		@param sstring optional
		@param a2c_log optional
		@param t2c_log optional
		@param c2k_log optional
		@param d2k_log optional
		@param search optional
		@param page optional
		@param t_type optional
		@param c_type optional
		@param sortby optional
		@param section optional
		@param search_all optional
		@param max_results optional
		
		@returns
		
		
		@comment

	**/
	function search($arr)
	{
		extract($arr);
		$this->read_template("search.tpl");

		$k = get_instance(CL_KEYWORD);

		$keys = array();
		$sel_keys = false;
		if (is_array($s_keywords))
		{
			foreach($s_keywords as $kw)
			{
				$keys[$kw] = $kw;
				$sel_keys = true;
			}
		}
		$search_list = $this->get_search_list(&$def);
		// if s_parent isn't numeric, set it to zero. otherwise various
		// interesting effects will happen. I spent fscking 2 hours debugging
		// this in www.eas.ee
		if ($s_parent != sprintf("%d",$s_parent))
		{
			$s_parent = 0;
		};

		$sp = "";
		$first = true;
		foreach($search_list as $sl_idx => $sl_val)
		{
			$this->vars(array(
				"sp_val" => $sl_idx,
				"sp_text" => $sl_val,
				"sp_sel" => (is_array($s_parent_arr) ? checked(in_array($sl_idx, $s_parent_arr)) : checked(($s_parent ? $s_parent == $sl_idx : $first) ))
			));
			$sp.=$this->parse("SEARCH_PARENT");
			$first = false;
		}

		load_vcl("date_edit");
		$de = new date_edit;
		$de->configure(array(
			"year" => "",
			"month" => "",
			"day" => ""
		));
		
		$date_from = date_edit::get_timestamp($date_from);
		$date_to = date_edit::get_timestamp($date_to);

		if (!$search)
		{
			$date_from = mktime(0,0,0,date("m"),date("d"),date("Y")-1);
			$date_to = time()+24*3600;
			$max_results = 50;
		}

				
		$sstring_title = trim($sstring_title);
		$sstring_author = trim($sstring_author);
		$sstring = trim($sstring);

		$this->vars(array(
			"SEARCH_PARENT" => $sp,
			"search_sel" => $this->option_list($s_parent,$search_list),
			"sstring_title" => $sstring_title,
			"sstring_author" => $sstring_author,
			"sstring" => $sstring,
			"t2c_or" => selected($t2c_log == "OR"),
			"t2c_and" => selected($t2c_log == "AND"),
			"a2c_or" => selected($a2c_log == "OR"),
			"a2c_and" => selected($a2c_log == "AND"),
			"c2k_or" => selected($c2k_log == "OR"),
			"c2k_and" => selected($c2k_log == "AND"),
			"d2k_or" => selected($d2k_log == "OR"),
			"d2k_and" => selected($d2k_log == "AND"),
			"t_type1" => selected($t_type == 1),
			"t_type2" => selected($t_type == 2),
			"t_type3" => selected($t_type == 3),
			"c_type1" => selected($c_type == 1),
			"c_type2" => selected($c_type == 2),
			"c_type3" => selected($c_type == 3),
			"max_results" => $this->picker($max_results, array("10" => "10", "20" => "20", "30" => "30", "40" => "40", "50" => "50", "60" => "60", "70" => "70", "80" => "80", "90" => "90", "100" => "100")),
			"max_results_tb" => $max_results,
			"date_from" => $de->gen_edit_form("date_from", $date_from, 2002, date("Y")+5, true),
			"date_to" => $de->gen_edit_form("date_to", $date_to, 2002, date("Y")+5, true),
			"keywords" => $this->multiple_option_list($keys,$k->get_all_keywords(array("type" => ARR_KEYWORD))),
			"reforb"	=> $this->mk_reforb("search", array("reforb" => 0,"search" => 1,"section" => aw_global_get("section"), "set_lang_id" => aw_global_get("lang_id")))
		));

		$this->quote(&$sstring);
		$this->quote(&$sstring_title);
		// this means that we only have one textbox, that sould search from title || body
		if ($search_all)
		{
			$sstring = $sstring_title;
			$t2c_log = "OR";
			$c_type = $t_type;
		}

		if ($search && ($sstring_title != "" || $sstring != "" || $sstring_author != "" || $date_from > (24*3600*200) || $date_to > (24*3600*200)))
		{
			// if we should be actually searching from a form, let formgen work it's magick
			$grps = $this->get_groups();
			if ($grps[$s_parent]["search_form"])
			{
				// do form search
				$finst = get_instance(CL_FORM);
				// we must load the form before we can set element values
				$finst->load($grps[$s_parent]["search_form"]);

				$s_q = $sstring != "" ? $sstring : $sstring_title;

				// set the search elements values
				foreach($grps[$s_parent]["search_elements"] as $el)
				{
					$finst->set_element_value($el, $s_q, true);
				}

				global $restrict_search_el,$restrict_search_val,$use_table,$search_form;
				$this->vars(array(
					"SEARCH" => $finst->new_do_search(array(
						"restrict_search_el" => $restrict_search_el,
						"restrict_search_val" => $restrict_search_val,
						"use_table" => $use_table,
						"section" => $section,
						"search_form" => $search_form
					))
				));

				return $this->parse();
			}
			else
			if ($grps[$s_parent]["static_search"])
			{
				return $this->do_static_search($sstring != "" ? $sstring : $sstring_title, $page, $arr, $s_parent, $t_type, $s_parent_arr);
			}


			// and here we do the actual searching bit!

			// assemble the search criteria sql
			if ($sstring_title != "")
			{
				if ($t_type == 1)	//	m6ni s6na
				{
					$q_cons2.="(".join(" OR ",map("(title LIKE '%%%s%%')",explode(" ",$sstring_title))).")";
				}
				else
				if ($t_type == 2)	//	k6ik s6nad
				{
					$q_cons2.="(".join(" AND ",map("(title LIKE '%%%s%%')",explode(" ",$sstring_title))).")";
				}
				else
				if ($t_type == 3)	//	fraas
				{
					$q_cons2.="(title LIKE '%".$sstring_title."%')";
				}

				if ($sstring != "" || $sel_keys)	// these can't be moved to the next if, cause then we'd have to check if $sstring != ""
				{
					if ($t2c_log == "OR")
					{
						$q_cons2.=" OR ";
					}
					else
					{
						$q_cons2.=" AND ";
					}
				}
			}

			if ($sstring != "")
			{
				if ($c_type > 3 || $c_type < 1)
				{
					$c_type = 1;
				}
				if ($c_type == 1)	//	m6ni s6na
				{
					// dokude tabelist otsing
					$q_cons2.="(".join(" OR ",map("(content LIKE '%%%s%%' OR lead LIKE '%%%s%%')",explode(" ",$sstring))).")";
					// failide tabelist otsing
					$q_fcons2="(".join(" OR ",map("(content LIKE '%%%s%%')",explode(" ",$sstring))).")";
					// tabelite tabelist otsing
					$q_tcons2="(".join(" OR ",map("(contents LIKE '%%%s%%')",explode(" ",$sstring))).")";
				}
				else
				if ($c_type == 2)	//	k6ik s6nad
				{
					$q_cons2.="(".join(" AND ",map("(content LIKE '%%%s%%' OR lead LIKE '%%%s%%')",explode(" ",$sstring))).")";
					$q_fcons2="(".join(" AND ",map("(content LIKE '%%%s%%')",explode(" ",$sstring))).")";
					$q_tcons2="(".join(" AND ",map("(contents LIKE '%%%s%%')",explode(" ",$sstring))).")";
				}
				else
				if ($c_type == 3)	//	fraas
				{
					$q_cons2.="(content LIKE '%".$sstring."%' OR lead LIKE '%".$sstring."%')";
					$q_fcons2="(content LIKE '%".$sstring."%')";
					$q_tcons2="(contents LIKE '%".$sstring."%')";
				}

				if ($sel_keys != "")
				{
					if ($c2k_log == "OR")
					{
						$q_cons2.=" OR ";
					}
					else
					{
						$q_cons2.=" AND ";
					}
				}
			}


			if ($sel_keys)
			{
				$q_cons2.="(".join(" OR ",map("documents.keywords LIKE '%%%s%%'",$keys)).")";
			}

			if ($sstring_author != "")
			{
				$this->quote(&$sstring_author);
				if ($q_cons2 != "")
				{
					$q_cons2 .= " AND ";
				}
				$q_cons2 .= " (documents.author LIKE '%$sstring_author%') ";
			}

			if ($date_from > 24*3600*12)
			{
				// also include that day's articles
				$date_from += 24*3600-1;
				if ($q_cons2 != "")
				{
					$q_cons2 .= " AND ";
				}
				$q_cons2 .= " (documents.modified >= $date_from) ";
			}

			if ($date_to > 24*3600*12)
			{
				// also include that day's articles
				$date_to += 24*3600-1;
				if ($q_cons2 != "")
				{
					$q_cons2 .= " AND ";
				}
				$q_cons2 .= " (documents.modified <= $date_to) ";
			}

			// search from files and tables here. ugh. ugly. yeah. I know.

			// oh crap. siin peab siis failide seest ka otsima. 
			if ($q_fcons2 != "")
			{
				$mtfiles = array();
				$this->db_query("SELECT id FROM files WHERE files.showal = 1 AND $q_fcons2 ");
				while ($row = $this->db_next())
				{
					$mtfiles[] = $row["id"];
				}
				$fstr = join(",",$mtfiles);
				if ($fstr != "")
				{
					// nyyd leiame k6ik aliased, mis vastavatele failidele tehtud on
					$this->db_query("SELECT source FROM aliases WHERE target IN ($fstr)");
					while ($row = $this->db_next())
					{
						$faliases[] = $row["source"];
					}
					// nyyd on $faliases array dokumentidest, milles on tehtud aliased matchivatele failidele.
					if (is_array($faliases))
					{
						$fasstr = "OR documents.docid IN (".join(",",$faliases).")";
					}
				}
			}

			if ($q_tcons != "")
			{
				// nini. otsime tabelite seest ka.
				$mts = array();
				$this->db_query("SELECT id FROM aw_tables WHERE $q_tcons");
				while ($row = $this->db_next())
				{
					$mts[] = $row["id"];
				}

				$mtsstr = join(",",$mts);
				if ($mtsstr != "")
				{
					// nyyd on teada k6ik tabelid, ksu string sisaldub
					// leiame k6ik aliased, mis on nendele tabelitele tehtud
					$this->db_query("SELECT source FROM aliases WHERE target IN ($mtsstr)");
					while ($row = $this->db_next())
					{
						$mtals[$row["source"]] = $row["source"];
					}

					// see on siis nimekiri dokudest, kuhu on tehtud aliased tabelitest, mis matchisid
					$mtalsstr = "OR documents.docid IN (".join(",",$mtals).")";
					//echo "ms = $mtalsstr<br />";
				}
			}

			// now fit in the results from searching files and tables
			if ($q_cons2 != "")
			{
				$q_cons2="((".$q_cons2.") $fasstr $mtalsstr)";
			}
			else
			{
				if ($fasstr != "" || $mtalsstr != "")
				{
					$q_cons2="($fasstr $mtalsstr)";
				}
			}

			// get all the parents under what the document can be
			$p_arr = $this->get_parent_arr($s_parent, $s_parent_arr);
			
			$_tpstr = join(",",$p_arr);
			if ($_tpstr != "")
			{
				$q_cons = "status = 2 AND parent IN (".$_tpstr.") ";
			}
			else
			{
				$q_cons = "status = 2  ";
			}

			if ($q_cons2 != "")
			{
				$q_cons.=" AND (".$q_cons2.")";
			}

			$perstr = "";
			if (aw_ini_get("search_conf.only_active_periods"))
			{
				$pei = get_instance(CL_PERIOD);
				$plist = $pei->period_list(0,false,1);
				$perstr = ($q_cons != "" ? " AND " : "")." objects.period IN (".join(",", array_keys($plist)).")";
			}
						
			$sid = " AND site_id = ".aw_ini_get("site_id")." AND objects.lang_id = ".aw_global_get("lang_id");

			// make pageselector
			$cnt = $this->db_fetch_field("SELECT count(*) as cnt FROM documents LEFT JOIN objects ON objects.oid = documents.docid WHERE $q_cons $perstr $sid","cnt");
			if ($max_results > 0)
			{
				$cnt = min($max_results, $cnt);
			}

			$this->vars(array(
				"PAGESELECTOR" => $this->do_pageselector($cnt,$arr)
			));

			$ap = $this->do_sorting($arr);
			$page = max(0, $page);

			$sql = "SELECT objects.*,documents.* FROM documents LEFT JOIN objects ON objects.oid = documents.docid WHERE $q_cons $perstr $sid $ap LIMIT ".($page*$this->per_page).",".$this->per_page;
			$this->db_query($sql);
			while ($row = $this->db_next())
			{
				if (!$this->can("view", $row["docid"]))
				{
					continue;
				}
		
				$fld = "content";
				if (aw_ini_get("search_conf.show_in_results") != "")
				{
					$fld = aw_ini_get("search_conf.show_in_results");
				}
				$co = strip_tags($row[$fld]);
				$co = preg_replace("/#(.*)#/","",substr($co,0,strpos($co,"\n")));
				$co = ($row["author"] != "" ? "Autor: ".$row["author"]."<br>" : "").$co;

				$sec = $row["docid"];
				$this->vars(array(
					"section" => $sec,
					"title" => $row["title"],
					"modified" => $row["tm"] != "" ? $row["tm"] : $this->time2date($row["modified"],2),
					"content" => $co
				));
				$mat.=$this->parse("MATCH");
			}
			$this->vars(array(
				"MATCH" => $mat
			));
			if ($mat != "")
			{
				$this->vars(array(
					"SEARCH" => $this->parse("SEARCH")
				));
			}
			else
			{
				$this->vars(array(
					"NO_RESULTS" => $this->parse("NO_RESULTS")
				));
			}

			// logime ka et tyyp otsis ja palju leidis.
			$this->do_log($search_list,$s_parent,$t_type,$sstring_title,$sstring,$t2c_log,$sel_keys,$keys,$c2k_log,$cnt);
		}
		return $this->parse();
	}

	function do_log($search_list,$s_parent,$t_type,$sstring_title,$sstring,$t2c_log,$sel_keys,$keys,$c2k_log,$cnt)
	{
		$this->db_query("INSERT INTO searches(str,s_parent,numresults,ip,tm) VALUES('$sstring','$s_parent','$cnt','".aw_global_get("REMOTE_ADDR")."','".time()."')");

		$sel_parent = $search_list[$s_parent];
		if ($t_type == 1)
		{
			$s = LC_SEARCH_CONF_SOME_WORD;
		}
		else
		if ($t_type == 2)
		{
			$s = LC_SEARCH_CONF_ALL_WORDS;
		}
		else
		if ($t_type == 3)
		{
			$s = LC_SEARCH_CONF_PHRASE;
		}
		if ($sstring_title != "")
		{
			$l=LC_SEARCH_CONF_IN_TITLE.$s.sprintf(LC_SEARCH_CONF_FROM_STRING,$sstring_title);
			if ($sstring != "")
			{
				if ($t2c_log == "OR")
				{
					$l.=LC_SEARCH_CONF_OR;
				}
				else	// AND
				{
					$l.=LC_SEARCH_CONF_AND;
				}
			}
		}

		if ($c_type == 1)
		{
			$s = LC_SEARCH_CONF_SOME_WORD;
		}
		else
		if ($c_type == 2)
		{
			$s = LC_SEARCH_CONF_ALL_WORDS;
		}
		else
		if ($c_type == 3)
		{
			$s = LC_SEARCH_CONF_PHRASE;
		}
		if ($sstring != "")
		{
			$l.=LC_SEARCH_CONF_IN_SUBJECT.$s.sprintf(LC_SEARCH_CONF_FROM_STRING,$sstring);
			if ($sel_keys != "")
			{
				if ($c2k_log == "OR")
				{
					$l.=LC_SEARCH_CONF_OR;
				}
				else	// AND
				{
					$l.=LC_SEARCH_CONF_AND;
				}
			}
		}

		if ($sel_keys)
		{
			$l.=LC_SEARCH_CONF_WITH_KEYWORD;
			$l.=join(",",map("%s",$keys));
		}
		$this->_log(ST_SEARCH, SA_DO_SEARCH, sprintf(LC_SEARCH_CONF_LOOK_ANSWER,$sel_parent,$l,$cnt));
	}

	function do_sorting($pa)
	{
		$sortby = $pa["sortby"];
		if ($sortby == "")
		{
			$sortby = "modified";
		}

		$pa["sortby"] = "modified";
		$this->vars(array(
			"sort_modified" => $this->mk_my_orb("search", $pa)
		));
		$pa["sortby"] = "title";
		$this->vars(array(
			"sort_title" => $this->mk_my_orb("search", $pa)
		));
		$pa["sortby"] = "content";
		$this->vars(array(
			"sort_content" => $this->mk_my_orb("search", $pa)
		));
		$sort_m = $this->parse("SORT_MODIFIED");
		$sort_t = $this->parse("SORT_TITLE");
		$sort_c = $this->parse("SORT_CONTENT");
		if ($sortby == "modified")
		{
			$ap = "ORDER BY documents.modified DESC";
			$sort_m = $this->parse("SORT_MODIFIED_SEL");
		}
		else
		if ($sortby == "title")
		{
			$ap = "ORDER BY documents.title";
			$sort_t = $this->parse("SORT_TITLE_SEL");
		}
		else
		if ($sortby == "content")
		{
			$ap = "ORDER BY documents.content";
			$sort_c = $this->parse("SORT_CONTENT_SEL");
		}
		$this->vars(array(
			"SORT_MODIFIED" => $sort_m,
			"SORT_MODIFIED_SEL" => "",
			"SORT_TITLE" => $sort_t,
			"SORT_TITLE_SEL" => "",
			"SORT_CONTENT" => $sort_c,
			"SORT_CONTENT_SEL" => "",
		));
		return $ap;
	}

	function do_pageselector($cnt,$arr)
	{
		$page = $arr["page"];
		$num_pages = floor(($cnt / $this->per_page) + 0.5);
		$pa = $arr;
		$pg = "";
		$prev = "";
		$nxt = "";
		for ($i=0; $i < $num_pages; $i++)
		{
			$pa["page"] = $i;
			$this->vars(array(
				"page" => $this->mk_my_orb("search", $pa),
				"page_from" => $i*$this->per_page,
				"page_to" => min(($i+1)*$this->per_page,$cnt)
			));
			if ($i == $page)
			{
				$pg.=$this->parse("SEL_PAGE");
			}
			else
			{
				$pg.=$this->parse("PAGE");
			}
		}
		$pa["page"] = max((int)$page-1,0);
		$this->vars(array(
			"prev" => $this->mk_my_orb("search", $pa)
		));
		$pa["page"] = min((int)$page+1,$num_pages-1);
		$this->vars(array(
			"next" => $this->mk_my_orb("search", $pa)
		));
		if ($page > 0)
		{
			$prev = $this->parse("PREVIOUS");
		}
		
		if (((int)$page) < ($num_pages-1))
		{
			$nxt = $this->parse("NEXT");
		}
		$this->vars(array(
			"PREVIOUS" => $prev, 
			"NEXT" => $nxt,
			"PAGE" => $pg, 
			"SEL_PAGE" => ""
		));
		return $this->parse("PAGESELECTOR");
	}

	function get_parent_arr($parent, $s_parent_arr)
	{
		if ($this->cfg["lang_menus"] == 1)
		{
			$ss = " AND objects.lang_id = ".aw_global_get("lang_id");
		}

		$this->menucache = array();
		$this->db_query("SELECT objects.oid as oid, objects.parent as parent,objects.last as last,objects.status as status
										 FROM objects 
										 WHERE objects.class_id = 1 AND objects.status = 2 $ss");
		while ($row = $this->db_next())
		{
			$this->menucache[$row["parent"]][] = $row;
		}

		// now, make a list of all menus below $parent
		$this->marr = array();
		// list of default documents
		$this->darr = array();

		// $parent is the id of the menu group, not the parent menu
		// so now we figure out the parent menus and do rec_list for all of them 
                if (!is_array($s_parent_arr))
                {
                        $s_parent_arr =array($parent);
                }		

                $this->marr = array();
                foreach($s_parent_arr as $parent)
                {
			$mens = $this->get_menus_for_grp($parent);
			if (is_array($mens))
			{
				$this->marr += $mens;
				if (is_array($mens))
				{
					foreach($mens as $mn)
					{
						$this->rec_list($mn);
					}
				};
			}
		}
		return (is_array($this->marr)) ? $this->marr : array(0);
	}

	function rec_list($parent)
	{
		if (!is_array($this->menucache[$parent]))
		{
			return;
		}

		reset($this->menucache[$parent]);
		while(list(,$v) = each($this->menucache[$parent]))
		{
			//if ($v["status"] == 2)
			if ($v["status"] > 0)
			{
				$this->marr[] = $v["oid"];
				if ($v["last"] > 0)
					$this->darr[] = $v["last"];
				$this->rec_list($v["oid"]);
			}
		}
	}

	// updates documents timestamp from document::tm and objects::modified to documents::modified
	/**  
		
		@attrib name=upd_dox params=name nologin="1" default="0"
		
		@param s_parent optional
		@param s_keywords optional
		@param sstring_title optional
		@param sstring optional
		@param t2c_log optional
		@param c2k_log optional
		@param search optional
		@param page optional
		@param t_type optional
		@param c_type optional
		@param sortby optional
		
		@returns
		
		
		@comment

	**/
	function upd_dox()
	{
		$this->db_query("SELECT objects.oid as oid,objects.modified as modified,documents.tm as tm,documents.title as title FROM objects LEFT JOIN documents ON documents.docid = objects.oid WHERE objects.class_id = 7");
		while ($row = $this->db_next())
		{
			$this->save_handle();
			$modified = $row["modified"];
			if ($row["tm"] != "")
			{
				list($day,$mon,$year) = explode("/",$row["tm"]);

				$ts = mktime(0,0,0,$mon,$day,$year);
				if ($ts)
				{
					$modified = $ts;
				}
			}

			$this->db_query("UPDATE documents SET modified = $modified WHERE docid = ".$row["oid"]);
			echo "modified doc ",$row["title"], " , tm = ",$row["tm"], " set date to ", $this->time2date($modified,3), "<br />\n";
			flush();
			$this->restore_handle();
		}
	}

	/**  
		
		@attrib name=change params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function change($arr)
	{
		$this->read_template("change.tpl");

		$act_grp = $this->get_cval("search::default_group");

		$grps = $this->get_groups();
		foreach($grps as $grpid => $grpdata)
		{
			$this->vars(array(
				"grpid" => $grpid,
				"name" => $grpdata["name"],
				"ord" => $grpdata["ord"],
				"change" => $this->mk_my_orb("change_grp", array("id" => $grpid)),
				"delete" => $this->mk_my_orb("delete_grp", array("id" => $grpid)),
				"checked" => checked($act_grp == $grpid)
			));
			$l.=$this->parse("LINE");
		}

		$id = @max(array_keys($grps))+1;

		$this->vars(array(
			"add" => $this->mk_my_orb("change_grp", array("id" => $id)),
			"LINE" => $l,
			"s_log" => $this->mk_my_orb("search_log", array()),
			"no_act_search" => checked(!$act_grp),
			"reforb" => $this->mk_reforb("submit_conf")
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_conf params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_conf($arr)
	{
		extract($arr);
		
		$this->set_cval("search::default_group", $act_search);
		return $this->mk_my_orb("change");
	}

	/**  
		
		@attrib name=change_grp params=name default="0"
		
		@param id optional
		
		@returns
		
		
		@comment

	**/
	function change_grp($arr)
	{
		extract($arr);
		$this->read_template("change_grp.tpl");
		$this->mk_path(0,"<a href='".$this->mk_my_orb("change", array())."'>Gruppide nimekiri</a> / Muuda gruppi");
		$grps = $this->get_groups();

		$f = get_instance(CL_FORM);
		$flist = $f->get_flist(array(
			"type" => FTYPE_SEARCH, 
			"addempty" => true, 
			"addfolders" => true,
			"sort" => true
		));

		$els = $f->get_elements_for_forms(array($grps[$id]["search_form"]));

		$this->vars(array(
			"name" => $grps[$id]["name"],
			"ord" => $grps[$id]["ord"],
			"id" => $id,
			"menus" => $this->multiple_option_list($grps[$id]["menus"],$this->get_menu_list()),
			"no_usersonly" => checked($grps[$id]["no_usersonly"] == 1),
			"users_only" => checked($grps[$id]["users_only"] == 1),
			"static_search" => checked($grps[$id]["static_search"] == 1),
			"min_len" => $grps[$id]["min_len"],
			"max_len" => $grps[$id]["max_len"],
			"empty_no_docs" => checked($grps[$id]["empty_search"] < 2),
			"empty_all_docs" => checked($grps[$id]["empty_search"] == 2),
			"search_forms" => $this->picker($grps[$id]["search_form"], $flist),
			"search_elements" => $this->mpicker($grps[$id]["search_elements"], $els),
			"reforb" => $this->mk_reforb("submit_change_grp", array("id" => $id))
		));
		return $this->parse();
	}

	/**  
		
		@attrib name=submit_change_grp params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function submit_change_grp($arr)
	{
		extract($arr);

		$grps = $this->get_groups();

		$grps[$id]["name"] = $name;
		$grps[$id]["ord"] = $ord;
		$grps[$id]["no_usersonly"] = $no_usersonly;
		$grps[$id]["users_only"] = $users_only;
		$grps[$id]["static_search"] = $static_search;
		$grps[$id]["min_len"] = $min_len;
		$grps[$id]["max_len"] = $max_len;
		$grps[$id]["empty_search"] = $empty_search;
		$grps[$id]["menus"] = $this->make_keys($menus);
		$grps[$id]["search_form"] = $search_form;
		$grps[$id]["search_elements"] = $this->make_keys($search_elements);
		
		$this->save_grps($grps);

		return $this->mk_my_orb("change_grp", array("id" => $id));
	}

	function _grp_sort($a,$b)
	{
		if ($a["ord"] == $b["ord"])
		{
			return 0;
		}
		return $a["ord"] < $b["ord"] ? -1 : 1;
	}

	function save_grps($grps)
	{
		// here we must first sort the $grps array based on user entered order
		uasort($grps,array($this,"_grp_sort"));
		$cache = get_instance("cache");

		$lgps = $this->get_groups(true);
		$lgps[$this->cfg["site_id"]][aw_global_get("lang_id")] = $grps;

		$cache->file_set("search_groups-".$this->cfg["site_id"],aw_serialize($lgps));
		$dat = aw_serialize($lgps,SERIALIZE_XML);
		$this->quote(&$dat);
		$c = get_instance("config");
		$c->set_simple_config("search_grps", $dat);
	}

	function get_groups($no_strip = false)
	{
		static $m;
		if (!$m)
		{
			$m = get_instance("contentmgmt/site_search/old_search_model");
		}
		return $m->get_groups($no_strip);
	}

	/**  
		
		@attrib name=delete_grp params=name default="0"
		
		@param id optional
		
		@returns
		
		
		@comment

	**/
	function delete_grp($arr)
	{
		extract($arr);
		$grps = $this->get_groups();
		unset($grps[$id]);
		$this->save_grps($grps);
		header("Location: ".$this->mk_my_orb("change", array()));
	}

	function get_menus_for_grp($gp)
	{
		$grps = $this->get_groups();
		return $grps[$gp]["menus"];
	}

	/**  
		
		@attrib name=search_log params=name default="0"
		
		
		@returns
		
		
		@comment

	**/
	function search_log($arr)
	{
		extract($arr);
		$this->read_template("search_log.tpl");

		$grps = $this->get_groups();

		$this->db_query("SELECT * FROM searches");
		while ($row = $this->db_next())
		{
			$this->vars(array(
				"time" => date("d.m.Y / H:i", $row["tm"]),
				"str" => $row["str"],
				"s_parent" => $grps[$row["s_parent"]]["name"],
				"numresults" => $row["numresults"],
				"ip" => $row["ip"],
				"s_url" => $this->cfg["baseurl"]."/index.".$this->cfg["ext"]."?class=document&action=search&str=".$row["str"]."&parent=".$row["s_parent"]
			));
			$l.=$this->parse("LINE");
		}
		$this->vars(array(
			"add" => $this->mk_my_orb("change_grp", array("id" => $id)),
			"LINE" => $l,
			"s_log" => $this->mk_my_orb("search_log", array())
		));
		return $this->parse();
	}

	function do_static_search($str, $page,$arr, $s_parent, $t_type, $s_parent_arr)
	{
		$p_arr = $this->get_parent_arr($s_parent, $s_parent_arr);
		$p_arr_str = join(",",$p_arr);
		if ($p_arr_str != "")
		{
			$p_arr_str = " section IN ($p_arr_str) ";
		}

		if ($t_type == 2)	//	k6ik s6nad
		{
			$q_cons.=" (".join(" AND ",map("(content LIKE '%%%s%%')",explode(" ",$str))).")";
		}
		else
		if ($t_type == 3)	//	fraas
		{
			$q_cons.=" (content LIKE '%".$str."%')";
		}
		else
		{
			$q_cons.=" (".join(" OR ",map("(content LIKE '%%%s%%')",explode(" ",$str))).")";
		}

		$q_cons .= " AND lang_id = '".aw_global_get("lang_id")."'";

		if ($p_arr_str != "" && $q_cons != "")
		{
			$q_cons = " AND ".$q_cons;
		}


		$q = "SELECT count(*) as cnt FROM export_content WHERE $p_arr_str $nou $q_cons";
		$cnt = $this->db_fetch_field($q, "cnt");

		$this->do_sorting($arr);

		$public_url = $this->get_cval("export::public_symlink_name");

		$q = "SELECT * FROM export_content WHERE $p_arr_str $nou $q_cons GROUP BY title ORDER BY modified LIMIT ".$page*$this->per_page.",".$this->per_page;
		$this->db_query($q);
		while ($row = $this->db_next())
		{
			//preg_match("/\<!-- PAGE_TITLE (.*) \/PAGE_TITLE -->/U", $row["content"], $mt);
			//$title = strip_tags($mt[1]);
			$title = $row["title"];
			//if (file_exists($public_url."/".$row['filename']))
                        $show = false;
                        if ($srud)
                        {
                                $show = ($row["orig_url"] != "");
                        }
                        else
                        {
                                $show = file_exists($public_url."/".$row['filename']);
                        }
                        if ($show)
			{
				$fn = $row["filename"];
				$this->vars(array(
					"section" => $fn,
					"title" => ($title != "" ? $title : $row["filename"]),
					"modified" => $this->time2date($row["modified"],5),
				));
				$mat.=$this->parse("MATCH");
				//$cnt++;
			}
		}
		$this->vars(array(
			"MATCH" => $mat
		));
		$ps = $this->do_pageselector($cnt,$arr);
		$this->vars(array(
			"PAGESELECTOR" => $ps
		));
		if ($mat != "")
		{
			$this->vars(array(
				"SEARCH" => $this->parse("SEARCH")
			));
		}
		else
		{
			$this->vars(array(
				"NO_RESULTS" => $this->parse("NO_RESULTS")
			));
		}

		return $this->parse();
	}
}
?>
