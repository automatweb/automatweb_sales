<?php

namespace automatweb;
/*
@classinfo  maintainer=kristo
*/

class promo_display implements main_subtemplate_handler
{
	const AW_CLID = 23;

	////
	// !this must set the content for subtemplates in main.tpl
	// params
	//	inst - instance to set variables to
	//	content_for - array of templates to get content for
	function on_get_subtemplate_content($arr)
	{
		$inst =& $arr["inst"];

		if (aw_ini_get("document.use_new_parser"))
		{
			$doc = get_instance("doc_display");
		}
		else
		{
			$doc = get_instance(CL_DOCUMENT);
		}

		if (aw_ini_get("menuedit.promo_lead_only"))
		{
			$leadonly = 1;
		}
		else
		{
			$leadonly = -1;
		}

		$filter = array();
		$filter["status"] = STAT_ACTIVE;
		$filter["class_id"] = CL_PROMO;

		if (aw_ini_get("menuedit.lang_menus"))
		{
			$filter["lang_id"] = aw_global_get("lang_id");
		}


		enter_function("promo_get_list");
		$list = new object_list($filter);

		$parr = $list->arr();
		$list->sort_by(array("prop" => "ord"));
		$parr = $list->arr();

		exit_function("promo_get_list");

		// pre-fetch all RELTYPE_ASSIGNED_MENU's for all containers
		$con = new connection();
		$assigned_menu_conns = $con->find(array(
			"from" => $list->ids(),
			"from.class_id" => CL_PROMO,
			"type" => "RELTYPE_ASSIGNED_MENU"
		));
		$assigned_menu_conns_by_promo = array();
		foreach($assigned_menu_conns as $assigned_menu_con)
		{
			$assigned_menu_conns_by_promo[$assigned_menu_con["from"]][$assigned_menu_con["to"]] = $assigned_menu_con["to"];
		}

		// prefetch RELTYPE_NO_SHOW_MENU conns
		$noshow_menu_conns = $con->find(array(
			"from" => $list->ids(),
			"from.class_id" => CL_PROMO,
			"type" => "RELTYPE_NO_SHOW_MENU"
		));
		$noshow_menu_conns_by_promo = array();
		foreach($noshow_menu_conns as $noshow_menu_con)
		{
			$noshow_menu_conns_by_promo[$noshow_menu_con["from"]][$noshow_menu_con["to"]] = $noshow_menu_con["to"];
		}



		$tplmgr = get_instance("templatemgr");
		$promos = array();
		$gidlist = aw_global_get("gidlist");
		$lang_id = aw_global_get("lang_id");
		$rootmenu = aw_ini_get("rootmenu");
		$default_tpl_filename = aw_ini_get("promo.default_tpl");
		$tpldir = aw_ini_get("tpldir");
		$no_acl_checks = aw_ini_get("menuedit.no_view_acl_checks");
		$promo_areas = aw_ini_get("promo.areas");

		$displayed_promos = array();

		foreach($parr as $o)
		{
if (!empty($_GET["PROMO_DBG"]))
{
	echo __FILE__."::".__LINE__." with promo ".$o->id()." ".$o->name()." <br>";
}
			if ($o->lang_id() != $lang_id && !$o->prop("content_all_langs"))
			{
				continue;
			}
			

			$found = false;

			$groups = $o->meta("groups");
			if (!is_array($groups) || count($groups) < 1)
			{
				$found = true;
			}
			else
			{
				foreach($groups as $gid)
				{
					if (isset($gidlist[$gid]) && $gidlist[$gid] == $gid)
					{
						$found = true;
					}
				}
			}

if (!empty($_GET["PROMO_DBG"]))
{
	echo __FILE__."::".__LINE__." with promo ".$o->id()." ".$o->name()." <br>";
}
			$doc->doc_count = 0;

			$show_promo = false;
			
			$msec = ifset($assigned_menu_conns_by_promo, $o->id());

			$section_include_submenus = $o->meta("section_include_submenus");

//echo "allm = ".$o->meta("all_menus")." o = ".$o->name()." site_id = ".$o->site_id()." <br>";
			if ($o->meta("all_menus") && ($o->site_id() == aw_ini_get("site_id") || aw_ini_get("promo.show_all_works")))
			{
				$show_promo = true;
			}
			else
			if (isset($msec[$inst->sel_section_real]) && $msec[$inst->sel_section_real])
			{
				$show_promo = true;
			}
			else
			if (is_array($section_include_submenus))
			{
				$pa = array($rootmenu);
				foreach($inst->path as $p_o)
				{
					$pa[] = $p_o->id();
				}

				// here we need to check, whether any of the parent menus for
				// this menu has been assigned a promo box and has been told
				// that it should be shown in all submenus as well
				$sis = new aw_array($section_include_submenus);
				$intersect = array_intersect($pa,$sis->get());
				if (sizeof($intersect) > 0)
				{
					$show_promo = true;
				}
			}

if (!empty($_GET["PROMO_DBG"]))
{
	echo __FILE__."::".__LINE__." with promo ".$o->id()." ".$o->name()." <br>";
}
			// do ignore menus
			$ign_subs = $o->meta("section_no_include_submenus");

			foreach(safe_array(ifset($noshow_menu_conns_by_promo, $o->id())) as $ignore_menu_to)
			{
				if ($inst->sel_section_real == $ignore_menu_to)
				{
					$show_promo = false;
				}
				else
				if (isset($ign_subs[$ignore_menu_to]))
				{
					// get path for current menu and check if ignored menu is above it
					foreach($inst->path_ids as $_path_id)
					{
						if ($_path_id == $ignore_menu_to)
						{
							$show_promo = false;
						}
					}
				}
			}

if (!empty($_GET["PROMO_DBG"]))
{
	echo __FILE__."::".__LINE__." with promo ".$o->id()." ".$o->name()." show = ".dbg::dump($show_promo)." <br>";
}
			if ($found == false)
			{
				$show_promo = false;
			};
if (!empty($_GET["PROMO_DBG"]))
{
	echo "promo = ".$o->id()." show = ".dbg::dump($show_promo)." <br>";
}
			if ($o->meta("not_in_search") == 1 && $_GET["class"] == "site_search_content")
			{
				$show_promo = false;
			}

if (!empty($_GET["PROMO_DBG"]))
{
	echo __FILE__."::".__LINE__." with promo ".$o->id()." ".$o->name()." show = ".dbg::dump($show_promo)." <br>";
}
			if (aw_ini_get("user_interface.hide_untranslated") && !$o->prop_is_translated("name"))
			{
				$show_promo = false;
			}

if (!empty($_GET["PROMO_DBG"]))
{
	echo __FILE__."::".__LINE__." with promo ".$o->id()." ".$o->name()." show = ".dbg::dump($show_promo)." <br>";
}
			$so = obj(aw_global_get("section"));
			if ($o->meta("not_in_doc_view") == 1 && (($so->class_id() == CL_DOCUMENT || $_GET["docid"]) || (is_oid($inst->get_default_document_list()))))
			{
				$show_promo = false;
			}

			// this line decides, whether we should show this promo box here or not.
			// now, how do I figure out whether the promo box is actually in my path?

if (!empty($_GET["PROMO_DBG"]))
{
	echo __FILE__."::".__LINE__." with promo ".$o->id()." ".$o->name()." show = ".dbg::dump($show_promo)." <br>";
}
			if ($show_promo)
			{
				$displayed_promos[$o->id()] = $o;
			}
		}

		// prefetch doc sources and doc ignores for all displayed promos
		$dsdi_cache = array();
		$dsdi_list_by_promo = array();
		if (count($displayed_promos) > 0)
		{
			$dsdi_cache = $con->find(array(
				"from" => array_keys($displayed_promos),
				"type" => array(6,2,5)
			));

			foreach($dsdi_cache as $dsdi_con)
			{
				$dsdi_list_by_promo[$dsdi_con["from"]][$dsdi_con["reltype"]][$dsdi_con["to"]] = $dsdi_con["to"];
			}
		}

	

			foreach($displayed_promos as $o)
			{
				enter_function("show_promo::".$o->name());
				// visible. so show it
				// get list of documents in this promo box
				$pr_c = "";
				global $awt;
				$awt->start("def-doc");

				if ($o->prop("is_dyn"))
				{
					aw_global_set("no_cache", 1);
				}

				// right, here we need to check if the container does not order docs by random, cause if it does, we need to not rely on the saved docs list
				$has_rand = false;
				if ($o->prop("sort_by") == "RAND()" || $o->prop("sort_by2") == "RAND()" || $o->prop("sort_by3") == "RAND()")
				{
					$has_rand = true;
				}
			
				if (!$has_rand && $o->meta("version") == 2 && (aw_ini_get("promo.version") == 2) && !$o->prop("auto_period") && !$o->prop("docs_from_current_menu") && false)
				{
					enter_function("mainc-contentmgmt/promo-read_docs");
					$docid = array_values(safe_array($o->meta("content_documents")));
					foreach($docid as $_idx => $_did)
					{
						if (!is_oid($_did))
						{
							unset($docid[$_idx]);
						}
					}
					if (count($docid))
					{
						// prefetch docs in list so we get them in one query
						$ol = new object_list(array("oid" => $docid));
						$tt = $ol->arr();
						$nids = $ol->ids();
						$tmp = array();	
						foreach($docid as $_id)
						{
							if (in_array($_id, $nids))
							{
								$tmp[] = $_id;
							}
						}
						$docid = $tmp;
					}
					exit_function("mainc-contentmgmt/promo-read_docs");
				}
				else
				{
					enter_function("mainc-contentmgmt/promo-read_docs-old");
					// get_default_document prefetches docs by itself so no need to do list here
					if (!empty($_GET["PROMO_DBG"]))
					{
						$_GET["INTENSE_DUKE"] = 1;
						obj_set_opt("no_cache", 1);
					}
					$docid = $inst->get_default_document(array(
						"obj" => $o,
						"all_langs" => true,
						"dsdi_cache" => !isset($dsdi_list_by_promo[$o->id()]) ? array() : $dsdi_list_by_promo[$o->id()]
					));
					exit_function("mainc-contentmgmt/promo-read_docs-old");
					if (!empty($_GET["PROMO_DBG"]))
					{
						$_GET["INTENSE_DUKE"] = 0;
						echo "version1 <br>";
					}
				}
				if (!empty($_GET["PROMO_DBG"]))
				{
					echo "3promo = ".$o->id()." show = ".dbg::dump($docid)." <br>";
				}
				$awt->stop("def-doc");

				if (!empty($_GET["PROMO_DBG"]))
				{
					echo __FILE__."::".__LINE__." with promo ".$o->id()." ".$o->name()." show = ".dbg::dump($docid)." <br>";
				}
				if (!$docid)
				{
					continue;
				}
				if (!is_array($docid))
				{
					if ($inst->can("view", $docid))
					{
						$do = obj($docid);
						$inst->vars(array(
							"page_name" => $do->trans_get_val("title")
						));
					}
					$docid = array($docid);
				}

if (!empty($_GET["PROMO_DBG"]))
				{
					echo "showing promo ".$o->name()." (".$o->id().")  type = ".$o->meta("type")." docs = ".join(", ", $docid)."<br>";
				}

				$d_cnt = 0;
				$d_total = count($docid);
				aw_global_set("in_promo_display", $o->id());

				if (!$o->prop("tpl_lead"))
				{
					$tpl_filename = $default_tpl_filename;
					if (!$default_tpl_filename)
					{
						continue;
					}
				}
				else
				{
					// find the file for the template by id. sucks. we should join the template table
					// on the menu template I guess
					$tpl_filename = $tplmgr->get_template_file_by_id(array(
						"id" => $o->prop("tpl_lead"),
					));
				}

				$set_tpl_filename = $tpl_filename;
				enter_function("mainc-contentmgmt/promo-show-docs");

				foreach($docid as $d)
				{
if (!empty($_GET["PROMO_DBG"]))
				{
					echo "doc $d <br>";
				}
					$do = obj($d);
					if (aw_ini_get("user_interface.hide_untranslated") && !$do->prop_is_translated("content"))
					{
						continue;
					}
if (!empty($_GET["PROMO_DBG"]))
				{
					echo "doc2 $d <br>";
				}
					$add_2 = false;
					if (($d_cnt % 2)  == 1)
					{
						if (file_exists($tpldir."/automatweb/documents/".$tpl_filename."2"))
						{
							$tpl_filename .= "2";
							$add_2 = true;
						}
					}
					
					if(!$add_2)
					{
						$tpl_filename = $set_tpl_filename;
					}

					if ($d_cnt >= $o->prop("tpl_lead_last_count") && $o->prop("tpl_lead_last"))
                                        {
						$tpl_filename = $tplmgr->get_template_file_by_id(array(
		                                        "id" => $o->prop("tpl_lead_last"),
			                        ));
                                        }

					enter_function("promo-prev");
					$cont = $doc->gen_preview(array(
						"docid" => $d,
						"tpl" => $tpl_filename,
						"leadonly" => $leadonly,
						"section" => $inst->sel_section,
						"strip_img" => false,
						"showlead" => 1,
						"boldlead" => aw_ini_get("promo.boldlead"),
						"no_strip_lead" => 1,
						"no_acl_checks" => $no_acl_checks,
						"vars" => array("doc_ord_num" => $d_cnt+1),
						"not_last_in_list" => (($d_cnt+1) < $d_total)
					));

					if (!empty($_GET["PROMO_DBG"]))
					{
						echo "doc $d cont = ".htmlentities($cont)." <br>";
					}
					exit_function("promo-prev");
					$pr_c .= $cont;
					// X marks the spot
					//$pr_c .= str_replace("\r","",str_replace("\n","",$cont));
					$d_cnt++;
				}
				exit_function("mainc-contentmgmt/promo-show-docs");
				aw_global_set("in_promo_display", 0);

				if (true || $inst->is_template("PREV_LINK"))
				{
					$this->do_prev_next_links($docid, $inst);
				}

				if ($o->prop("separate_pages"))
				{
					$o->set_prop("separate_pages", false);
					$all_docs = $inst->get_default_document(array(
						"obj" => $o,
					));
					$total_docs = count($all_docs);
					$per_page = $o->prop("docs_per_page");
					$pages = $total_docs / $per_page;
					$var_name = "promo_".$o->id()."_page";
					$cur_page = (int)$_GET["promo_".$o->id()."_page"];

					$ps = array();
					for($i = 0; $i < $pages; $i++)
					{
						$inst->vars(array(
							"page_url" => aw_url_change_var($var_name, $i),
							"page_number" => $i+1
						));
						if ($cur_page == $i)
						{
							$ps[] = $inst->parse("PROMO_CUR_PAGE");
						}
						else
						{
							$ps[] = $inst->parse("PROMO_PAGE");
						}
						if ($i == ($cur_page-1))
						{
							$prev_page = $inst->parse("PROMO_PREV_PAGE");
						}
						if ($i == ($cur_page+1))
						{
							$next_page = $inst->parse("PROMO_NEXT_PAGE");
						}
					}
					$inst->vars(array(
						"PROMO_PAGE" => join($inst->parse("PROMO_PAGE_SEP"), $ps),
						"PROMO_CUR_PAGE" => "",
						"PROMO_PAGE_SEP" => "",
						"PROMO_PREV_PAGE" => $prev_page,
						"PROMO_NEXT_PAGE" => $next_page
					));
				}

				$image = "";
				$image_url = "";
				if ($o->prop("image"))
				{
					$i = get_instance(CL_IMAGE);
					$image_url = $i->get_url_by_id($o->prop("image"));
					$image = $i->make_img_tag($image_url);
				}

				$promo_link = $this->get_promo_link($o);
				$inst->vars_safe(array(
					"comment" => $o->trans_get_val("comment"),
					"title" => $o->trans_get_val("name"), 
					"caption" => $o->trans_get_val("caption"),
					"content" => $pr_c,
					"url" => $promo_link,
					"link" => $promo_link,
					"link_caption" => $o->trans_get_val("link_caption"),
					"promo_doc_count" => (int)$d_cnt,
					"image" => $image, 
					"image_url" => $image_url,
					"image_or_title" => ($image == "" ? $o->trans_get_val("caption") : $image),
					"promo_oid" => $o->id()	
				));

				// which promo to use? we need to know this to use
				// the correct SHOW_TITLE subtemplate
				if (is_array($promo_areas) && count($promo_areas) > 0)
				{
					$templates = array();
					foreach($promo_areas as $pid => $pd)
					{
						$templates[$pid] = $pd["def"]."_PROMO";
					}
				}
				else
				{
					$templates = array(
						"scroll" => "SCROLL_PROMO",
						"0" => "LEFT_PROMO",
						"1" => "RIGHT_PROMO",
						"2" => "UP_PROMO",
						"3" => "DOWN_PROMO",
					);
				}
	
				$use_tpl = $templates[$o->meta("type")];
				if (!$use_tpl)
				{
					$use_tpl = "LEFT_PROMO";
				};

				$inst->vars_safe(array(
					$use_tpl."_image" => $image,
					$use_tpl."_image_url" => $image_url,
					$use_tpl."_image_or_title" => ($image == "" ? $o->trans_get_val("caption") : $image),
				));

				$hlc = "";
				if ($o->trans_get_val("link_caption") != "")
				{
					$hlc = $inst->parse($use_tpl.".HAS_LINK_CAPTION");
				}
				$inst->vars_safe(array(
					"HAS_LINK_CAPTION" => $hlc
				));

				if ($o->meta("no_title") != 1)
				{
					$inst->vars_safe(array(
						"SHOW_TITLE" => $inst->parse($use_tpl . ".SHOW_TITLE")
					));
				}
				else
				{
					$inst->vars_safe(array(
						"SHOW_TITLE" => ""
					));
				}
				$ap = "";
				if ($promo_link != "")
				{
					$ap = "_LINKED";
				}
				if (!isset($this->used_promo_tpls[$use_tpl]) || $this->used_promo_tpls[$use_tpl] != 1)
				{
					$ap.="_BEGIN";
					$this->used_promo_tpls[$use_tpl] = 1;
				}

				if(!isset($promos[$use_tpl]))
				{
					$promos[$use_tpl] = "";
				}

				if ($inst->is_template($use_tpl . $ap))
				{
					$promos[$use_tpl] .= $inst->parse($use_tpl . $ap);
					$inst->vars_safe(array($use_tpl . $ap => ""));
				}
				else
				{
					$promos[$use_tpl] .= $inst->parse($use_tpl);
					$inst->vars_safe(array($use_tpl => ""));
				};
				// nil the variables that were imported for promo boxes
				// if we dont do that we can get unwanted copys of promo boxes
				// in places we dont want them
				$inst->vars_safe(array("title" => 
					"", "content" => "","url" => ""));
				exit_function("show_promo::".$o->name());
			}

		$inst->vars_safe($promos);
	}

	function do_prev_next_links($docs, &$tpl)
	{
		$s_prev = $s_next = "";

		$cur_doc = obj(aw_global_get("section"));
		if ($cur_doc->class_id() == CL_DOCUMENT)
		{
			$fp_prev = false;
			$fp_next = false;
			$prev = false;
			$get_next = false;
			foreach($docs as $d)
			{
				if ($get_next)
				{
					$fp_next = $d;
					$get_next = false;
				}
				if ($d == $cur_doc->id())
				{
					$fp_prev = $prev;
					$get_next = true;
				}
				$prev = $d;
			}

			if ($fp_prev)
			{
				$tpl->vars(array(
					"prev_link" => obj_link($fp_prev)
				));
				$s_prev = $tpl->parse("PREV_LINK");
			}

			if ($fp_next)
			{
				$tpl->vars(array(
					"next_link" => obj_link($fp_next)
				));
				$s_next = $tpl->parse("NEXT_LINK");
			}
		}

		$tpl->vars(array(
			"PREV_LINK" => $s_prev,
			"NEXT_LINK" => $s_next
		));
	}

	function get_promo_link($o)
	{
		$link_str = $o->trans_get_val("link");
		$i = new file();
		if ($i->can("view", $o->meta("linked_obj")))
		{
			$linked_obj = obj($o->meta("linked_obj"));
			if ($linked_obj->class_id() == CL_MENU)
			{
				$ss = get_instance("contentmgmt/site_show");
				$link_str = $ss->make_menu_link($linked_obj);
			}
			else
			{
				$dd = get_instance("doc_display");
				$link_str = $dd->get_doc_link($linked_obj);
			}
		}
		return $link_str;
	}
}
?>
