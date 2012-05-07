<?php

class document extends aw_template implements orb_public_interface
{
	var $blocks;//TODO: scope?
	var $title;//TODO: scope?
	var $no_left_pane;//TODO: scope?
	var $no_right_pane;//TODO: scope?

	private $req;

	function document($period = 0)
	{
		$this->init("automatweb/documents");
		// see on selleks, kui on vaja perioodilisi dokumente naidata
		$this->period = $period;

		// this takes less than 0.1 seconds btw
		$xml_def = $this->get_file(array("file" => aw_ini_get("basedir")."xml/documents/defaults.xml"));
		if ($xml_def)
		{
			$this->define_styles($xml_def);
		}

		// siia tuleks kirja panna koik dokumentide tabeli v2ljade nimed,
		// mida voidakse muuta

		$this->knownfields = array("title","subtitle","author","photos","keywords","names",
			"lead","showlead","content","esilehel","jrk1","jrk2", "jrk3",
			"esileht_yleval","esilehel_uudis","title_clickable","cite","channel","tm",
			"is_forum","link_text","lead_comments","newwindow","yleval_paremal",
			"show_title","copyright","long_title","nobreaks","no_left_pane","no_right_pane",
			"no_search","show_modified","frontpage_left","frontpage_center","frontpage_center_bottom",
			"frontpage_right","frontpage_left_jrk","frontpage_center_jrk","frontpage_center_bottom_jrk",
			"frontpage_right_jrk","no_last","dcache","moreinfo","show_to_country"
		);

		// nini. siia paneme nyt kirja v2ljad, mis dokumendi metadata juures kirjas on
		$this->metafields = array("show_print","show_last_changed","show_real_pos","dcache");

		if (isset($GLOBALS["lc_document"]) && is_array($GLOBALS["lc_document"]))
		{
			$this->vars($GLOBALS["lc_document"]);
		}

		$this->subtpl_handlers["FILE"] = "_subtpl_file";
	}

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	/** Fetches a document from the database
		@attrib name=fetch params=name default="0"

		@param docid required type=int
	**/
	function fetch($docid, $no_acl_checks = false)
	{
		if (!acl_base::can("view",$docid))
		{
			//	and why is this commented out?
			$this->data = false;
			return false;
		}

		$docobj = new object($docid);
		if ($this->period > 0 && !$this->ignore_periods)
		{
			if ($docobj->prop("period") != $this->period)
			{
				// maintain status quote .. e.g. do not return anything if
				// the document is not in the correct period or we are ignoring
				// periods (which is the case if a menu is set to show documents
				// from multiple periods
				$docobj = false;
			}
		}

		$this->docobj = $docobj;
		if (is_object($docobj))
		{
			$retval = $docobj->fetch();
			$retval["docid"] = $retval["oid"];
		}

		$retval["title"] = $docobj->trans_get_val("title"); // fix condition when brother has a different name
		return $retval;
	}

	////
	// !genereerib objekti n8 valmiskujul
	// params: docid, text, tpl, tpls, leadonly, strip_img, secID, boldlead, tplsf, notitleimg, showlead, no_stip_lead, doc
	// tpls - selle votmega antakse ette template source, mille sisse kood paigutada
	// doc - kui tehakse p2ring dokude tabelisse, siis v6ib ju sealt saadud inffi kohe siia kaasa panna ka
	//       s22stap yhe p2ringu.
	// tpl_auto - if tpl is not set and this is true, then the template is autodetected from the document location
	//       lead/full is based on leadonly parameter
	function gen_preview($params)
	{
		if (aw_ini_get("document.use_new_parser"))
		{
			$d = new doc_display();
			return $d->gen_preview($params);
		}

		extract($params);
		global $print;
		$this->print = $print;
		$tpl = isset($params["tpl"]) ? $params["tpl"] : "plain.tpl";
		!isset($leadonly) ? $leadonly = -1 : "";
		!isset($strip_img) ? $strip_img = 0 : "";
		!isset($notitleimg) ? $notitleimg = 0 : "";

		$baseurl = aw_ini_get("baseurl");
		$ext = AW_FILE_EXT;
		$lang_id = aw_global_get("lang_id");

		// kysime dokumendi kohta infot
		// muide docid on kindlasti numbriline, aliaseid kasutatakse ainult
		// menueditis.
		if (!isset($doc) || !is_array($doc))
		{
			// we need to put the translation merging into this fetch function
			$doc = $this->fetch($docid, $no_acl_checks);
			if ($doc == false)
			{
				return "";
			}
			$doc_o = obj($docid);
		}
		else
		{
			$doc_o = obj($doc["docid"]);
		}

		if(!$doc_o->is_visible_to())
		{
			return "";
		}

		if (!empty($params["no_link_if_not_act"]) && $doc_o->status() == object::STAT_NOTACTIVE)
		{
			$doc["title_clickable"] = 0;
		}

		if (aw_ini_get("menuedit.show_real_location"))
		{
			$doc["docid"] = $doc_o->brother_of();
			$docid = $doc_o->brother_of();
		}

		// if oid is in the arguments check whether that object is attached to
		// this document and display it instead of document
		$_t_oid = aw_global_get("oid");
		if ($_t_oid)
		{
			$q = "SELECT * FROM aliases WHERE source = '".$doc_o->brother_of()."' AND target = '$_t_oid' AND type =" . CL_FILE;
			$this->db_query($q);
			$row = $this->db_next();
			if ($row)
			{
				$fi = new file();
				$fl = $fi->get_file_by_id($_t_oid);
				return $fl["content"];
			}
		}

		if (!$params["tpl"])
		{
			// do template autodetect from parent
			$tplmgr = new templatemgr();
			if ($leadonly > -1)
			{
				$tpl = $tplmgr->get_lead_template($doc["parent"]);
			}
			else
			{
				$tpl = $tplmgr->get_long_template($doc["parent"]);
			}
			/*if (!$tpl)
			{
				$tpl = "plain.tpl";
			}*/
		}

		$this->dequote($doc["lead"]);
		// if there is no document with that id, then bail out
		if (!isset($doc))
		{
			return false;
		}

		$oid = aw_global_get("oid");
		if ($oid)
		{
			$q = "SELECT * FROM aliases WHERE source = '$docid' AND target = '$oid' AND type =" . CL_FILE;
			$this->db_query($q);
			$row = $this->db_next();
			if ($row)
			{
				$fi = get_instance(CL_FILE);
				$fl = $fi->get_file_by_id($oid);
				$doc["content"] = $fl["content"];
				$doc["lead"] = "";
				$doc["title"] = "";
				$doc["meta"]["show_print"] = 1;
				$this->vars(array("page_title" => strip_tags($fl["comment"])));
				$pagetitle = strip_tags($fl["comment"]);
			}
		}


		$doc["title"] = $this->sanitize($doc["title"]);
		$doc["lead"] = $this->sanitize($doc["lead"]);

		if ($doc["meta"])
		{
			$meta = $doc["meta"];
		}
		else
		{
			if ($this->can("view", $doc["brother_of"]))
			{
				$__tmp = obj($doc["brother_of"]);
				$meta = $__tmp->meta();
			}
		};

		$si = __get_site_instance();
		//hook for site specific document parsing
		$doc["tpl"] = $tpl;
		$doc["leadonly"] = $leadonly;
		$doc["tpldir"] = &$this->template_dir;
		$doc["vars"] = isset($params["vars"]) ? $params["vars"] : array();
		if ($si)
		{
			// augh .. backwards compatiblity is a fucking bitch
			// that parse_document thingie expects $doc _array_ .. and wants
			// to modify it .. and allah only knows where this is used ...
			$si->parse_document($doc);
			if (!$si->can_show_document($doc))
			{
				return "";
			}
		}
		//$doc["content"] = "<style>.styl1 {color: green; font-family: Verdana; font-weight: bold;} .styl2 {color: blue; font-size: 20px;} .styl3 {color: red; border: 1px solid blue;}</style>" . $doc["content"];
		$params["vars"] = $doc["vars"];
		$tpl = $doc["tpl"];

		if (!trim(strip_tags($doc["lead"])))
		{
			$doc["lead"] = "";
		}


		//$meta = $doc["meta"];
		if (!empty($meta["show_last_changed"]))
		{
			$doc["content"] .= "<p><font size=1><i>".t("Viimati muudetud:")."&nbsp;&nbsp;</i>" . $this->time2date($doc["modified"],4) . "</font>";
		}

		$this->tpl_reset();
		$this->tpl_init("automatweb/documents");

		// see on sellex et kui on laiem doku, siis menyyeditor tshekib
		// neid muutujaid ja j2tab paani 2ra kui tshekitud on.
		$this->no_right_pane = $doc["no_right_pane"];
		$this->no_left_pane = $doc["no_left_pane"];



		// use special template for printing if one is set in the cfg file
		if (aw_global_get("print") && ($this->cfg["print_tpl"]) )
		{
			$tpl = $this->cfg["print_tpl"];
		}
		// kui tpls anti ette, siis loeme template sealt,
		// muidu failist.
		if (isset($tpls) && strlen($tpls) > 0)
		{
			$this->templates["MAIN"] = $tpls;
		}
		elseif (isset($tplsf) && strlen($tplsf) > 0)
		{
			$this->read_any_template($tplsf);
		}
		else
		{
			$this->read_any_template($tpl);
		}

		// if show in iframe is set, just return the iframe
		if ($doc_o->prop("show_in_iframe") && !$_REQUEST["only_document_content"])
		{
			$this->vars(array(
				"iframe_url" => obj_link($doc_o->id())."?only_document_content=1"
			));
			return $this->parse("IFRAME");
		}


		$loid = aw_global_get("lang_oid");
		if ($loid)
		{
// 				$tbl = $this->db_get_table("languages");
// 				if (!isset($tbl["fields"]["show_others"]))
// 				{
// 					$this->db_add_col("languages", array(
// 						'name' => "show_others",
// 						'type' => 'int'
// 					));
// 				}

			$o = obj($loid);
			$txts = new aw_array($o->meta("texts"));//DEPRECATED
			$this->vars($txts->get());
		}

		lc_site_load("document", $this);

		if (( ($meta["show_print"]) && (not($print)) && $leadonly != 1) && empty($is_printing))
		{
			// another wonderful way of showing a link
			$link2 = aw_ini_get("baseurl").aw_url_change_var(array(
				"class" => "document",
				"action" => "print",
				"print" => 1,
				"tv_sel" => isset($_GET["tv_sel"]) ? $_GET["tv_sel"] : "",
				"section" => $docid
			));

			if (!empty($this->cfg["print_cap"]))
			{
				$pc = localparse($this->cfg["print_cap"],array(
					"link2" => $link2,
					"link" => $this->mk_my_orb("print", array("section" => $docid)),
					"docid" => $docid
				));
				if ($this->cfg["pc_bottom"])
				{
					$doc["content"] .= $pc;
				}
				else
				{
					if (!($doc["showlead"] == 1 || $showlead == 1))
					{
						$doc["content"] = $pc . $doc["content"];
					}
					else
					{
						$doc["lead"] = $pc . $doc["lead"];
					}
				}
			}
			else
			{
				$request_uri = aw_ini_get("baseurl").aw_global_get("REQUEST_URI");
				$pos = strpos($request_uri, "&");
				$pos2 = strpos($request_uri, "set_lang_id");
				if ($pos === false && $pos2 === false)
				{
					$link = $request_uri . "?print=1";
				}
				else
				{
					$link = $request_uri . "&print=1";
				}
				$this->vars(array(
					"docid" => $docid,
					"printlink" => $link,
					"tv_sel" => isset($GLOBALS["tv_sel"]) ? $GLOBALS["tv_sel"] : "",
					"printlink2" => $link2,
				));
				#aw_global_set("no_menus",1);
				$_tmp = $this->parse("PRINTANDSEND");
				$this->vars(array("PRINTANDSEND" => $_tmp));
			}
		}


		$this->vars(array("imurl" => "/images/trans.gif"));
		// import charset for print
		if ($this->template_has_var("charset"))
		{
//			$_ld = languages::fetch($lang_id);
			$this->vars(array(
				"charset" => languages::USER_CHARSET,
			)); 
		}

		// load localization settings and put them in the template
		if (isset($GLOBALS["lc_doc"]) && is_array($GLOBALS["lc_doc"]))
		{
			$this->vars($GLOBALS["lc_doc"]);
		}


		// I don't think we should do that here
		// $this->add_hit($docid);


		$doc["content"] = str_replace("#nool#", '<IMG SRC="{VAR:baseurl}img/icon_nool.gif" WIDTH="21" HEIGHT="9" BORDER=0 ALT="">', $doc["content"]);

		# translate stuff between #code# and #/code#
		if (false !== strpos($doc["content"],"#code#"))
		{
		       $doc["content"] = preg_replace("/(#code#)(.+?)(#\/code#)/esm","\"<pre>\".str_replace('#','&#35;',htmlspecialchars(stripslashes('\$2'))).\"</pre>\"",$doc["content"]);
		}

		if (false !== strpos($doc["content"],"#noparse#"))
		{
		       $doc["content"] = preg_replace("/(#noparse#)(.+?)(#\/noparse#)/esm","str_replace('#','&#35;','\$2')",$doc["content"]);
		}

		if (false !== strpos($doc["content"],"#php#"))
		{
		       $doc["content"] = preg_replace("/(#php#)(.+?)(#\/php#)/esm","highlight_string(stripslashes('<'.'?php'.'\$2'.'?'.'>'),true)",$doc["content"]);
		}


		if(aw_ini_get("document.parse_keywords") && $doc_o->prop("link_keywords2"))
		{
			$this->parse_keywords($doc["content"]);
			$this->parse_keywords($doc["lead"]);
		}



		// in_archive disappears if we move around in archives
		// so we need another way to determine whether this document belongs to the active
		// period

		// would be nice if I could check whether the template _has_ one of those period variables
		// and only _then_ load the period class --duke
		$db_periods = get_instance(CL_PERIOD, $this->cfg["per_oid"]);
		$act_per = aw_global_get("act_per_id");
		$pdat = $db_periods->get($act_per);

		// period vars
		$this->vars(array(
			"act_per_id" => $pdat['id'],
			"act_per_name" => $pdat['name'],
			"act_per_comment" => $pdat['comment'],
			"act_per_image_url" => ($pdat['data']['image']['url']) ? $pdat['data']['image']['url'] : "/automatweb/images.trans.gif",
		));

		$this->dequote($doc["title"]);
		$this->title = $doc["title"];

		// hide the content of the document after the marker if the document is from the current period
		// this functionality is used by AM and should perhaps be moved to the site class -- duke
		if (!(($pp = strpos($doc["content"],"#poolita#")) === false))
		{
			if (aw_global_get("current_period") == $doc["period"])
			{
				if ($this->cfg["poolita_text"] != "")
				{
					$def = $this->cfg["poolita_text"];
				}
				else
				{
					$def = t("<br /><B>Edasi loe ajakirjast!</b></font>");//XXX: midagi vana, kaotada, muuta...
				}
				$doc["content"] = substr($doc["content"],0,$pp).$def;
			}
			else
			{
				$doc["content"]  = str_replace("#poolita#","",$doc["content"]);
			}
		}

		// vaatame kas vaja poolitada - kui urlis on show_all siis n2itame tervet, muidu n2itame kuni #edasi# linkini
		if (!empty($GLOBALS["show_all"]))
		{
			$doc["content"] = str_replace("#edasi#", "",$doc["content"]);
			if (!(($pp = strpos($doc["content"],"#edasi1#")) === false))
			{
				$doc["content"] = substr($doc["content"],$pp+8);
			}

			$doc["lead"] = str_replace("#edasi#", "",$doc["lead"]);
			if (!(($pp = strpos($doc["lead"],"#edasi1#")) === false))
			{
				$doc["lead"] = substr($doc["lead"],$pp+8);
			}
		}
		else
		{
			$re = $GLOBALS["lc_document"]["LC_LOE_EDASI"];
			if ($re == "")
			{
				$re = t("Loe edasi");
			}
			if (!(($pp = strpos($doc["content"],"#edasi#")) === false))
			{
				$doc["content"] = substr($doc["content"],0,$pp)."<a href='".aw_url_change_var("show_all", 1)."'>$re</a>";
			}

			if (!(($pp = strpos($doc["content"],"#edasi1#")) === false))
			{
				$doc["content"] = substr($doc["content"],0,$pp)."<a href='".aw_url_change_var("show_all", 1)."'>$re</a>";
			}

			if (!(($pp = strpos($doc["lead"],"#edasi#")) === false))
			{
				$doc["lead"] = substr($doc["lead"],0,$pp)."<a href='".aw_url_change_var("show_all", 1)."'>$re</a>";
			}

			if (!(($pp = strpos($doc["lead"],"#edasi1#")) === false))
			{
				$doc["lead"] = substr($doc["lead"],0,$pp)."<a href='".aw_url_change_var("show_all", 1)."'>$re</a>";
			}
		}


		// laeme vajalikud klassid
		// kui vaja on n?idata ainult dokumendi leadi, siis see tehakse siin
 		if ($leadonly > -1)
		{
			// stripime pildid v?lja.
			if ($strip_img)
			{
				// ja stripime leadist *koik* objektitagid v?lja.
				$this->vars(array("imurl" => "/images/trans.gif"));
				$doc["lead"] = preg_replace("/#(\w+?)(\d+?)(v|k|p|)#/i","",$doc["lead"]);
			};
			// damn, that did NOT make any sense at all - terryf
			$doc["content"] = $doc["lead"];
		}
		else
		{
			if (($doc["lead"]) && ($doc["showlead"] == 1 || $showlead == 1) )
			{
				if ($this->is_template("image_pos"))
				{
					if (preg_match("/#p(\d+?)(v|k|p|)#/i",$doc["lead"],$match))
					{
						// asendame
						$img = get_instance(CL_IMAGE);
						$idata = $img->get_img_by_oid($docid,$match[1]);
						$this->vars(array(
							"imgref" => $idata["url"]
						));
						$this->vars(array("image_pos" => $this->parse("image_pos")));
						$doc["lead"] = preg_replace("/#(\w+?)(\d+?)(v|k|p|)#/i","",$doc["lead"]);
					}
				}
				// I don't know whether this is a good idea, but fuck it, my head hurts
				// and emsl.struktuur.ee wants this, so I'm doing this through site->parse_document
				if (isset($doc["no_strip_lead"]))
				{
					$no_strip_lead = $doc["no_strip_lead"];
				};
				if ($no_strip_lead != 1)
				{
					// here we only strip images
					// because kirjastus.ee dites require it - there are images in lead that get shown in
					// document list, but when viewing the full article the images are somewhere in the article
					// and should not be shown in the lead again
					// but other sites will want to put for instance links in the lead
					// so we gots to keep those.
					$doc["lead"] = preg_replace("/#pict(\d+?)(v|k|p|)#/i","",$doc["lead"]);
					$doc["lead"] = preg_replace("/#p(\d+?)(v|k|p|)#/i","",$doc["lead"]);
				}
				$txt = "";

				if ($boldlead)
				{
					$txt = "<b>";
				};

				if ($doc["lead"] != "" && $doc["lead"] != "&nbsp;")
				{
					if ($this->cfg["lead_splitter"] != "")
					{
						$txt .= $doc["lead"] . $this->cfg["lead_splitter"];
					}
					else
					{
						if (aw_ini_get("content.doctype") == "xhtml")
						{
							$txt .= $doc["lead"] . "<br />";
						}
						else if (aw_ini_get("content.doctype") == "html" )
						{
							$txt .= $doc["lead"] . "<br>";
						}
					}
				}

				// whaat?
				if ($this->cfg["no_lead_splitter"])
				{
					$txt.=$this->cfg["no_lead_splitter"];
				}

				if ($boldlead)
				{
					$txt .= "</b>";
				}

				if (aw_ini_get("content.doctype") == "xhtml")
				{
					$txt .= ($this->cfg["doc_lead_break"] && $no_doc_lead_break != 1 ? "<br />" : "")."$doc[content]";

				}
				else if (aw_ini_get("content.doctype") == "html" )
				{
					$txt .= ($this->cfg["doc_lead_break"] && $no_doc_lead_break != 1 ? "<br>" : "")."$doc[content]";
				}

				$doc["content"] = $txt;
			}
		}


		// all the style magic is performed inside the style engine
		$doc["content"] = $this->parse_text($doc["content"]);
		$doc["content"] = preg_replace("/<loe_edasi>(.*)<\/loe_edasi>/isU","<a href='{$baseurl}index{$ext}/section=$docid'>\\1</a>",$doc["content"]);
		// sellel real on midagi pistmist WYSIWYG edimisvormiga
		// and this also means that we can't have xml inside the document. sniff.
		$doc["content"] = preg_replace("/<\?xml(.*)\/>/imsU","",$doc["content"]);


		$this->docid = $docid;
		$this->source = $doc["content"];

		$this->register_parsers();
		$this->create_relative_links($doc["content"]);
		// viimati muudetud dokude listi rida
		if (preg_match("/#viimati_muudetud num=\"(.*)\"#/",$doc["content"], $matches))
		{
			$doc["content"] = str_replace("#viimati_muudetud num=\"".$matches[1]."\"#",$this->get_last_doc_list($matches[1]),$doc["content"]);
		}

		// if ucheck1 is set and the current template contains "ucheck1" subtemplate, then
		// show it.
		if (!empty($doc["ucheck1"]) && $doc["ucheck1"] && $this->is_template("ucheck1"))
		{
			$ucheck = $this->parse("ucheck1");
			$this->vars(array(
				"ucheck1" => $ucheck,
			));
		};

		if (strpos($doc['content'], "#login#") !== false)
		{
			if (aw_global_get("uid") == "")
			{
				if (($port = aw_ini_get("auth.display_over_ssl_port")) > 0)
				{
					if (!$_SERVER["HTTPS"])
					{
						$bits = parse_url(aw_ini_get("baseurl"));
						header("Location: https://".$bits["host"].":".$port.aw_global_get("REQUEST_URI"));
						die();
					}
				}
				$li = get_instance("aw_template");
				$li->init();
				$li->read_template("login.tpl");
				$doc['content'] = str_replace("#login#", $li->parse(), $doc['content']);
			}
			else
			{
				$doc['content'] = str_replace("#login#", "", $doc['content']);
			}
		}

		if (isset($params["vars"]) && is_array($params["vars"]))
		{
			$this->vars($params["vars"]);
		};

		// create keyword links unless we are in print mode, since you cant click
		// on links on the paper they dont make sense there :P
		if ($this->cfg["keyword_relations"] && not($print) && $params["keywords"])
		{
			$this->create_keyword_relations($doc["content"]);
			$this->create_keyword_relations($doc["lead"]);
		}



		// v6tame pealkirjast <p> maha
		$doc["title"] = preg_replace("/<p>(.*)<\/p>/is","\\1",$doc["title"]);
		// only parse aliases if there might be something to parse
		if (strpos($doc["title"],"#") !== false)
		{
			if ($notitleimg != 1)
			{
					$doc["title"] = $this->parse_aliases(array(
						"text"	=> $doc["title"],
						"oid"	=> $docid,
					));
			}
			else
			{
				$doc["title"] = preg_replace("/#(\w+?)(\d+?)(v|k|p|)#/i","",$doc["title"]);
			}
		};

		// used in am - shows all documents whose author field == current documents title field
		if (!(strpos($doc["content"], "#autori_dokumendid#") === false))
		{
			$doc["content"] = str_replace("#autori_dokumendid#",$this->author_docs($doc["title"]),$doc["content"]);
		}

		// #top# link - viib doku yles
		$top_link = $this->parse("top_link");
		$doc["content"] = str_replace("#top#", $top_link,$doc["content"]);

		// noja, mis fucking "undef" see siin on?
		// damned if I know , v6tax ta 2kki 2ra siis? - terryf
		$al = get_instance("alias_parser");

		if (!isset($text) || $text !== "undef")
		{
			if (strpos($doc["content"],"#") !== false)
			{
				$doc["content"] = $this->parse_aliases(array(
					"oid" => $docid,
					"text" => $doc["content"],
				));
			};
		};

		if (trim($doc["user3"]) != "" && strpos($doc["user3"],"#") !== false)
		{
			$al->parse_oo_aliases($doc["docid"], $doc["user3"], array("templates" => &$this->templates, "meta" => &$meta));
		}

		if (trim($doc["moreinfo"]) != "" && strpos($doc["moreinfo"],"#") !== false)
		{
			$al->parse_oo_aliases($doc["docid"], $doc["moreinfo"], array("templates" => &$this->templates, "meta" => &$meta));
		}

		if (trim($doc["user2"]) != "" && strpos($doc["user2"],"#") !== false)
		{
			$al->parse_oo_aliases($doc["docid"], $doc["user2"], array("templates" => &$this->templates, "meta" => &$meta));
		}

		// where do I put that shit? that break conversion thingie?
		if ($doc["nobreaks"] || $doc["meta"]["cb_nobreaks"]["content"])	// kui wysiwyg editori on kasutatud, siis see on 1 ja pole vaja breike lisada
		{
			if (aw_ini_get("content.doctype") === "xhtml")
			{
				$doc["lead"] = str_replace("<br>", "<br />", $doc["lead"]);
				$doc["content"] = str_replace("<br>" ,"<br />",$doc["content"]);
			}
			else if (aw_ini_get("content.doctype") === "html")
			{
				$doc["lead"] = str_replace("<br />", "<br>", $doc["lead"]);
				$doc["content"] = str_replace("<br />", "<br>",$doc["content"]);
			}



			// fuckwits ... meeza thinks we should do the replacements when we are saving the
			// document .. no? ... cause this nobreak thingie will cause me all kinds of
			// problems later on.
		}
		else
		{
			if (aw_ini_get("content.doctype") == "xhtml")
			{
				$doc["content"] = str_replace("\r\n", "<br />",$doc["content"]);
			}
			else if (aw_ini_get("content.doctype") == "html")
			{
				$doc["content"] = str_replace("\r\n", "<br>",$doc["content"]);
			}
		}

		$al = get_instance("alias_parser");
		$al->parse_oo_aliases($doc["docid"], $doc["content"], array("templates" => &$this->templates, "meta" => &$meta));

		$this->vars($al->get_vars());

		$al->parse_oo_aliases($doc["docid"], $doc["title"], array("templates" => &$this->templates, "meta" => &$meta));
		if ($leadonly > -1)
		{
				/*$doc["title"] = str_replace("</a>", "</a><a href='/".$doc["docid"]."'>", $doc["title"]);
				$doc["title"] = str_replace("<a", "</a><a", $doc["title"]);*/
		}
		$this->vars($al->get_vars());

		// this damn ugly-ass hack is here because we need to be able to put the last search value
		// from form_table to document title
		if (aw_global_get("set_doc_title") != "")
		{
			$doc["title"] = aw_global_get("set_doc_title");
			aw_global_set("set_doc_title","");
		}

		$pb = "";

		$this->vars(array(
			"link_text" => $doc["link_text"],
		));

		if ($doc["photos"])
		{
			if ($this->cfg["link_authors"] && ($this->templates["pblock"]))
			{

				$authors = array();
				$olist = new object_list(array(
					"parent" => $this->cfg["link_authors_section"],
					"class_id" => array(CL_MENU,CL_DOCUMENT),
					"status" => STAT_ACTIVE,
					"name" => $doc["photos"],
				));
				$author_names = $olist->names();
				$x = array_flip($author_names);

				// Nothing was found, craft a special array required by the following block of code
				// (remains from ancient AW)
				if (sizeof($x) == 0)
				{
					$x[$doc["photos"]] = "";
				};

				if (empty($this->cfg["link_default_link"]))
				{
					$authors = array_keys($x);
				}
				else
				{
					while(list($k,$v) = each($x))
					{
						if ($this->cfg["link_default_link"] != "")
						{
							if ($v)
							{
								$authors[] = sprintf("<a href='%s'>%s</a>",document::get_link($v),$k);
							}
							else
							{
								$authors[] = sprintf("<a href='%s'>%s</a>",$this->cfg["link_default_link"],$k);
							};
						}
					};
				};
				$author = join(", ",$authors);
				$this->vars(array("photos" => $author));
			 	$pb = $this->parse("pblock");
			}
			else
			{
				$this->vars(array("photos" => $doc["photos"]));
			 	$pb = $this->parse("pblock");
			};
		};

		// <mail to="bla@ee">lahe tyyp</mail>
 		$doc["content"] = preg_replace("/<mail to=\"(.*)\">(.*)<\/mail>/","<a class='mailto_link' href='mailto:\\1'>\\2</a>",$doc["content"]);
		$doc["content"] = str_replace(LC_DOCUMENT_CURRENT_TIME,$this->time2date(time(),2),$doc["content"]);

		$ab = "";

		if ($doc["author"])
		{
			if ($this->cfg["link_authors"] && isset($this->templates["ablock"]))
			{
				$authors = array();
				$olist = new object_list(array(
					"parent" => $this->cfg["link_authors_section"],
					"class_id" => array(CL_MENU,CL_DOCUMENT),
					"status" => STAT_ACTIVE,
					"name" => $doc["author"],
				));
				$author_names = $olist->names();
				$x = array_flip($author_names);

				if (sizeof($x) == 0)
				{
					$x[$doc["author"]] = "";
				};

				if (empty($this->cfg["link_default_link"]))
				{
					$authors = array_keys($x);
				}
				else
				{
					while(list($k,$v) = each($x))
					{
						if ($this->cfg["link_default_link"] != "")
						{
							if ($v)
							{
								$authors[] = sprintf("<a href='%s'>%s</a>",document::get_link($v),$k);
							}
							else
							{
								$authors[] = sprintf("<a href='%s'>%s</a>",$this->cfg["link_default_link"],$k);
							};
						}
					};
				};

				$author = join(", ",$authors);
				$this->vars(array("author" => $author));
				$ab = $this->parse("ablock");
			}
			else
			{
				$this->vars(array("author" => $doc["author"]));
				$ab = $this->parse("ablock");
			};
		};

		$pts = "";
		if ($this->is_template("RATE"))
		{
			$points = $doc["num_ratings"] == 0 ? 3 : $doc["rating"] / $doc["num_ratings"];
			for ($i=0; $i < $points; $i++)
			{
				$pts.=$this->parse("RATE");
			}
		};


		if (!$this->can("view", $doc["docid"]))
		{
			return "";
		}
		$dc_obj = new object($doc["docid"]);
		if ($this->is_template("LINKLIST") && $dc_obj->prop("no_topic_links") != 1)
		{

			$pos = strpos($doc["content"],t("Vaata lisaks:"));
			if ($pos !== false)
			{
				$doc["content"] = substr($doc["content"],0,$pos);
			};
			$pos = strpos($doc["content"],t("Samal teemal:"));
			if ($pos !== false)
			{
				$doc["content"] = substr($doc["content"],0,$pos);
			};
			$conns = $dc_obj->connections_from(array(
				"class" => array(CL_EXTLINK),
			));
			$ll = "";
			foreach($conns as $item)
			{
				$this->vars(array(
					"url" => aw_ini_get("baseurl") . $item->prop("to"),
					"caption" => $item->prop("to.name"),
				));
				$ll .= $this->parse("LINKITEM");
			};
			$conns2 = $dc_obj->connections_to(array(
				"class" => array(CL_EXTLINK),
			));
			foreach($conns2 as $item)
			{
				$this->vars(array(
					"url" => aw_ini_get("baseurl") . $item->prop("to"),
					"caption" => $item->prop("to.name"),
				));
				$ll .= $this->parse("LINKITEM");
			};
			if (sizeof($conns) > 0 || sizeof($conns2) > 0)
			{
				$this->vars(array(
					"LINKITEM" => $ll,
				));
				$this->vars(array(
					"LINKLIST" => $this->parse("LINKLIST"),
				));
			};

			// generate a list of objects
		}

		if ($this->is_template("DOCLIST") && $dc_obj->prop("no_topic_links") != 1)
		{

			$pos = strpos($doc["content"],t("Vaata lisaks:"));
			if ($pos !== false)
			{
				$doc["content"] = substr($doc["content"],0,$pos);
			};
			$pos = strpos($doc["content"],t("Samal teemal:"));
			if ($pos !== false)
			{
				$doc["content"] = substr($doc["content"],0,$pos);
			};
			$dc_obj = new object($doc["docid"]);
			$conns = $dc_obj->connections_from(array(
				"class" => array(CL_DOCUMENT),
			));
			$ll = "";
			foreach($conns as $item)
			{
				$this->vars(array(
					"url" => aw_ini_get("baseurl") . $item->prop("to"),
					"caption" => $item->prop("to.name"),
				));
				$ll .= $this->parse("DOCITEM");
			};
			if (sizeof($conns) > 0)
			{
				$this->vars(array(
					"DOCITEM" => $ll,
				));
				$this->vars(array(
					"DOCLIST" => $this->parse("DOCLIST"),
				));
			};
		}

		$fr = "";


		if (	$doc["is_forum"] &&
			empty($print) &&
			($this->template_has_var("num_comments") || $this->is_template("FORUM_ADD_SUB") || $this->is_template("FORUM_ADD_SUB_ALWAYS") || $this->is_template("FORUM_ADD"))

	     	)
		{
			$_sect = aw_global_get("section");
			// calculate the amount of comments this document has
			// XXX: I could use a way to figure out which variables are present in the template
			$num_comments = $this->db_fetch_field("SELECT count(*) AS cnt FROM comments WHERE board_id = '$docid'","cnt");
			$this->vars(array(
				"num_comments" => sprintf("%d",$num_comments),
				"comm_link" => $this->mk_my_orb("show_threaded",array("board" => $docid,"section" => $_sect),"forum"),
			));
			$forum = get_instance(CL_FORUM);
			$fr = $forum->add_comment(array("board" => $docid,"section" => $_sect));

			if ($this->is_template('FORUM_RECENT_COMMENTS'))
			{
				$forum->_query_comments(array(
					'board' => $docid,
					'limit' => '5',
					'order' => 'desc'
				));
				while ($row = $this->db_next())
				{
					$this->vars(array(
						'id' => $row['id'],
						'board_id' => $row['board_id'],
						'name' => $row['name'],
						'email' => $row['email'],
						'subject' => $row['subj'],
						'comment' => nl2br(htmlspecialchars(stripslashes($row['comment']))),
						'time' => date('d.m.Y / H:i', $row['time']),
						'site_id' => $row['site_id'],
						'ip' => $row['ip']
					));
					$comms .= $this->parse('FORUM_RECENT_COMMENT');
				}
				$this->vars(array(
					'FORUM_RECENT_COMMENT' => $comms
				));
				$this->vars(array(
					'FORUM_RECENT_COMMENTS' => $this->parse('FORUM_RECENT_COMMENTS')
				));
			}

			if ($num_comments > 0)
			{
				$this->vars(array("FORUM_ADD_SUB" => $this->parse("FORUM_ADD_SUB")));
			}
			$this->vars(array("FORUM_ADD_SUB_ALWAYS" => $this->parse("FORUM_ADD_SUB_ALWAYS")));
		}
		else
		{
			$this->vars(array("FORUM_ADD_SUB_ALWAYS" => ""));
			$this->vars(array("FORUM_ADD_SUB" => ""));
			$this->vars(array("FORUM_RECENT_COMMENTS" => ""));
		}

		$langs = "";
		if ($this->is_template("SEL_LANG") || $this->is_template("LANG"))
		{
			$l = get_instance("languages");
			$larr = $l->listall();
			reset($larr);
			while (list(,$v) = each($larr))
			{
				$this->vars(array("lang_id" => $v["id"], "lang_name" => $v["name"]));
				if ($lang_id == $v["id"])
				{
					$langs.=$this->parse("SEL_LANG");
				}
				else
				{
					$langs.=$this->parse("LANG");
				}
			}
		};

		$lc = "";
		if (!empty($doc["lead_comments"]))
		{
			$lc = $this->parse("lead_comments");
		}

		classload("image");


		if (($this->template_has_var("parent_id") || $this->template_has_var("parent_name") || $this->template_has_var("menu_image") || $this->template_has_var("menu_addr")) && $doc["parent"])
		{
			$p_o = obj($doc["parent"]);
			$this->vars(array(
				"parent_name" => $p_o->name(),
				"parent_id" => $doc["parent"],
				"menu_image" => image::check_url($p_o->meta("img_url")),
				"menu_addr"	=> $p_o->prop("link"),
			));
		}

		if (!isset($this->doc_count))
		{
			$this->doc_count = 0;
		}

		$title = $doc["title"];
		if ($this->cfg["capitalize_title"])
		{
			// switch to estonian locale
			$old_loc = setlocale(LC_CTYPE,0);
			setlocale(LC_CTYPE, 'et_EE');

			$title = str_replace("&NBSP;", "&nbsp;", strtoupper($title));

			// switch back to estonian
			setlocale(LC_CTYPE, $old_loc);
		}

		$orig_doc_tm = $doc["tm"];
		if (!$doc["tm"])
		{
			$doc["tm"] = $doc["modified"];
		}

		$_date = $doc["doc_modified"] > 1 ? $doc["doc_modified"] : $doc["modified"];
		$date_est = date("j", $_date).". ".aw_locale::get_lc_month(date("m", $_date))." ".date("Y", $_date);
		$date_est_n = "";
		if (trim($orig_doc_tm) != "")
		{
			$date_est_n = $date_est;
		}
		$date_est_print = aw_locale::get_lc_date(time(), LC_DATE_FORMAT_SHORT_FULLYEAR);

		$r_docid = $docid;

		if (!headers_sent())
		{
			header("X-AW-Last-Modified: ".$_date);
			header("X-AW-Document-Title: ".(!empty($pagetitle) ? $pagetitle : strip_tags($title)));
		}

		$sel_lang_img_url = "";
		if ($this->template_has_var("sel_lang_img_url"))
		{
			$l = get_instance("languages");
			$ldata = $l->fetch($lang_id);
			$sel_lang_img = $ldata["meta"]["lang_img"];

			$i = get_instance(CL_IMAGE);
			$sel_lang_img_url = html::img(array(
				"url" => $i->get_url_by_id($sel_lang_img)
			));
		}

		if (!empty($GLOBALS["DD"]))
		{
			echo "for doc $doc[docid] tm = ".$doc["tm"]."  den = $date_est_n odtm = $orig_doc_tm <br>";

		}

		$document_link = obj_link($doc["docid"]);
		if ($doc_o->alias() != "")
		{
			if (aw_ini_get("menuedit.recursive_aliases"))
			{
				$als = array();
				$pt = $doc_o->path();
				foreach($pt as $pt_o)
				{
					if ($pt_o->alias() != "")
					{
						$als[] = $pt_o->alias();
					}
				}
				if (aw_ini_get("menuedit.language_in_url"))
				{
					$document_link = aw_ini_get("baseurl").aw_global_get("ct_lang_lc")."/".join("/", $als);
				}
				else
				{
					$document_link = aw_ini_get("baseurl").join("/", $als);
				}
			}
			else
			{
				if (aw_ini_get("menuedit.language_in_url"))
				{
					$document_link = aw_ini_get("baseurl").aw_global_get("ct_lang_lc")."/".$doc_o->alias();
				}
				else
				{
					$document_link = aw_ini_get("baseurl").$doc_o->alias();
				}
			}
		}

		if (!empty($_GET["path"]))
		{
	                $new_path = array();
			$path_ids = explode(",", $_GET["path"]);
                        foreach($path_ids as $_path_id)
                        {
        	                $new_path[] = $_path_id;
                	        $pio = obj($_path_id);
                        	if ($pio->brother_of() == $doc_o->parent())
                                {
                                	break;
                                }
                        }
                        $document_link = aw_ini_get("baseurl")."?section=".$doc_o->id()."&path=".join(",",$new_path).",".$doc_o->id();
		}

		$o_section = new object($doc_o->parent());
		$s_section_name = $o_section->name();

		if (aw_ini_get("content.doctype") == "html")
		{
			$s_content = str_replace("\r\n","<br>", $doc_o->prop("content"));
			$s_lead_br = $doc["lead"] != "" ? "<br>" : "";
		}
		else if (aw_ini_get("content.doctype") == "xhtml")
		{
			$s_content = str_replace("\r\n","<br />",$doc_o->prop("content"));
			$s_lead_br = $doc["lead"] != "" ? "<br />" : "";
		}

		if ($doc_o->prop("show_facebook"))
		{
			$this->vars(array("FACEBOOK" => $this->parse("FACEBOOK")));
		}

		if ($doc_o->prop("show_twitter"))
		{
	/*		arr($this->vars["docid"]);
			arr(aw_ini_get("baseurl"));
			arr(urlencode(aw_ini_get("baseurl").$this->vars["docid"]));*/
			$this->vars(array("twitter_url" => "http://platform.twitter.com/widgets/tweet_button.1333526973.html#_=1334108136206&amp;count=horizontal&amp;id=twitter-widget-26&amp;lang=en&amp;original_referer=http%3A%2F%2Ftwitter.com%2Fabout%2Fresources%2Fbuttons%23tweet&amp;size=m&amp;text=Twitter%20%2F%20Twitter%20buttons&amp;url=".urlencode(aw_ini_get("baseurl").$this->vars["docid"])));
			$this->vars(array("TWITTER" => $this->parse("TWITTER")));
		}


		$this->vars_safe(array(
			"sel_lang_img_url" => $sel_lang_img_url,
			"doc_modified" => $_date,
			"doc_mod" => $doc["doc_modified"],
			"doc_created" => $doc["created"],
			"date_est" => $date_est,
			"date_est_n" => $date_est_n,
			"print_date_est" => $date_est_print,
			"page_title" => !empty($pagetitle) ? $pagetitle : strip_tags($title),
			"title"	=> $title,
			"text"  => $doc["content"],
			"secid" => isset($secID) ? $secID : 0,
			"docid" => $r_docid,
			"ablock"   => isset($ab) ? $ab : 0,
			"pblock"   => isset($pb) ? $pb : 0,
			"moreinfo" => $doc["moreinfo"],
			"cite" => $doc["cite"],
			"date"     => $this->time2date(time(),2),
			"section"  => $GLOBALS["section"],
			"section_name" => $s_section_name,
			"lead_comments" => $lc,
			"locale_date" => aw_locale::get_lc_date($doc["doc_modified"],6),
			"copyright" => isset($doc["copyright"]) ? $doc["copyright"] : null,
			"long_title" => isset($doc["long_title"]) ? $doc["long_title"] : null,
			"link_text" => isset($doc["link_text"]) ? $doc["link_text"] : null,
			"modified"	=> date("d.m.Y", $doc["modified"]),
			"createdby" => $doc["createdby"],
			"date2"	=> $this->time2date($doc["modified"],8),
			"timestamp" => ($doc["modified"] > 1 ? $doc["modified"] : $doc["created"]),
			"created_hr" => "<?php classload(\"document\"); echo document::get_date_human_readable(".$doc["created"]."); ?>",
			"created_human_readable" => "<?php classload(\"document\"); echo document::get_date_human_readable(".$doc["created"]."); ?>",
			"channel"		=> isset($doc["channel"]) ? $doc["channel"] : null,
			"tm"				=> $doc["tm"],
			"tm_only" => $orig_doc_tm,
			"link_text"	=> $doc["link_text"],
			// please don't change the format
			"start1" => date("d.m.Y", $doc["start1"]),
			"start2" => date("d.m.Y H:i", $doc["start1"]),
			"subtitle"	=> $doc["subtitle"],
			"RATE"			=> $pts,
			"FORUM_ADD" => $fr,
			"LANG" => $langs,
			"SEL_LANG" => "",
			"lead_br"	=> $s_lead_br,
			"doc_count" => $this->doc_count++,
			"title_target" => !empty($doc["newwindow"]) ? "target=\"_blank\"" : "",
			"title_link"  => (!empty($doc["link_text"]) ? $doc["link_text"] : (isset($GLOBALS["doc_file"]) ? $GLOBALS["doc_file"] :  "index{$ext}/")."section=".$docid),
			"site_title" => strip_tags($doc["title"]),
			"link" => "",
			"user1" => $doc["user1"],
			"user2" => $doc["user2"],
			"user3" => $doc["user3"],
			"user4" => $doc["user4"],
			"user5" => $doc["user5"],
			"user6" => $doc["user6"],
			"user7" => $doc["user7"],
			"user8" => $doc["user8"],
			"user9" => $doc["user9"],
			"user10" => $doc["user10"],
			"user11" => $doc["user11"],
			"user12" => $doc["user12"],
			"user13" => $doc["user13"],
			"user14" => $doc["user14"],
			"user15" => $doc["user15"],
			"user16" => $doc["user16"],
			"alias" => $doc["alias"],
			"obj_modified" => (is_object($doc_o) ? $doc_o->modified() : ""),
			"sel_menu_id" => $doc_o->parent(),
			"document_link" => $document_link,
			"lead" => $doc["lead"],
			"content" => $s_content,
		));

		for($i = 1;  $i < 7; $i++)
		{
			if ($doc["user".$i] != "")
			{
				$this->vars(array(
					"HAS_user".$i => $this->parse("HAS_user".$i)
				));
			}
			else
			{
				$this->vars(array(
					"NO_user".$i => $this->parse("NO_user".$i)
				));
			}
		}

		if ($doc["content"] != "")
		{
			$this->vars(array(
				"HAS_text" => $this->parse("HAS_text")
			));
		}
		else
		{
			$this->vars(array(
				"NO_text" => $this->parse("NO_text")
			));
		}

		if (is_object($si) && method_exists($si,"get_document_vars"))
		{
			$this->vars($si->get_document_vars($doc));
		};

		if ($title != "")
		{
			$this->vars(array(
				"TITLE_NOT_EMPTY" => $this->parse("TITLE_NOT_EMPTY")
			));
		}
		else
		{
			$this->vars(array(
				"TITLE_NOT_EMPTY" => ""
			));
		}

		$nll = "";
		if (!empty($not_last_in_list))
		{
			$nll = $this->parse("NOT_LAST_IN_LIST");
		}
		$this->vars(array(
			"NOT_LAST_IN_LIST" => $nll
		));

		if ($doc["title_clickable"])
		{
			$this->vars(array("TITLE_LINK_BEGIN" => $this->parse("TITLE_LINK_BEGIN"), "TITLE_LINK_END" => $this->parse("TITLE_LINK_END")));
		}

		if (!empty($doc["channel"]))
		{
			$this->vars(array("HAS_CHANNEL" => $this->parse("HAS_CHANNEL")));
		}

		$this->vars(array(
			"HAS_MODIFIED" => ($orig_doc_tm != "" ? $this->parse("HAS_MODIFIED") : "")
		));

		$this->vars_safe(array(
			"SHOW_TITLE" 	=> ($doc["show_title"] == 1 && $doc["title"] != "") ? $this->parse("SHOW_TITLE") : "",
			"SHOW_TITLE2" 	=> ($doc["show_title"] == 1 && $doc["title"] != "") ? $this->parse("SHOW_TITLE2") : "",
			"EDIT" 		=> (acl_base::prog_acl("view",PRG_MENUEDIT)) ? $this->parse("EDIT") : "",
			"SHOW_MODIFIED" => ($doc["show_modified"]) ? $this->parse("SHOW_MODIFIED") : "",
			"COPYRIGHT"	=> !empty($doc["copyright"]) ? $this->parse("COPYRIGHT") : "",
			"logged" => (aw_global_get("uid") != "" ? $this->parse("logged") : ""),
		));


		// keeleseosed
		if ($this->is_template("LANG_BRO"))
		{
			$lab = array();
			foreach($doc_o->connections_from(array("type" => "RELTYPE_LANG_REL")) as $c)
			{
				$lab[$c->prop("to.lang_id")] = $c->prop("to");
			}
			if (!count($lab))
			{
				$lab = utf_unserialize($doc["lang_brothers"]);
			}
			$langs = "";
			$larr = languages::listall();
			reset($larr);
			while (list(,$v) = each($larr))
			{
				if ($lab[$v["id"]])
				{
					$this->vars(array("lang_id" => $v["id"], "lang_name" => $v["name"],"section" => $lab[$v["id"]]));
					if ($lang_id == $v["id"])
					{
						$langs.=$this->parse("SLANG_BRO");
					}
					else
					{
						$langs.=$this->parse("LANG_BRO");
						// tshekime et kui sellel dokul pole m22ratud muutmise kuup2eva, siis vaatame kas m6nel seotud dokul on
						// ja kui on, siis kasutame seda
						if ($tm == "")
						{
							$tm = $this->db_fetch_field("SELECT tm FROM documents WHERE docid = ".$lab[$v["id"]],"tm");
						}
					}
				}
			}

			$this->vars(array("LANG_BRO" => $langs));
		} // keeleseosed

		$this->do_subtpl_handlers($doc_o);

		$this->do_plugins($doc_o);

		$retval = $this->parse();

		$print = aw_global_get("print");

		if ($print && $this->cfg["remove_links_from_print"])
		{
			$retval = preg_replace("/<a(.*)>/iU", "", $retval);
			$retval = str_replace("</a>", "", $retval);
			$retval = str_replace("</A>", "", $retval);
		}

		if ($print || (isset($GLOBALS["action"]) && $GLOBALS["action"] == "print"))
		{
			$apd = get_instance("layout/active_page_data");
			$apd->on_shutdown_get_styles($retval);
		}

		if ($print)
		{
			$apd = get_instance("layout/active_page_data");
			die($apd->on_shutdown_get_styles($retval));
		}

		return $retval;
	}

	// kysib "sarnaseid" dokusid mingi v?lja kaudu
	// XXX
	function get_relations_by_field($params)
	{
		$field = $params["field"]; // millisest v?ljast otsida
		$keywords = split(",",$params["keywords"]); // mida sellest v?ljast otsida,
																		// comma separated listi
		$section = $params["section"]; // millisest sektsioonist otsida
		// kui me midagi ei otsi, siis pole siin midagi teha ka enam. GET OUT!
		if (!is_array($keywords))
		{
			return false;
		}
		else
		{
			// moodustame p?ringu dokude (v6i menyyde) saamiseks, mis vastavad meile
			// vajalikule tingimusele
			$retval = array();
			while(list($k,$v) = each($keywords))
			{
				$v = trim(strip_tags($v));

/*				if (!is_array($section))
				{
					$ot = new object_tree(array(
						"parent" => $section,
						"class_id" => array(CL_MENU,CL_DOCUMENT),
						"status" => 2
					));
					$ol = $ot->to_list();
					for($o =& $ol->begin(); !$ol->end(); $o =& $ol->next())
					{
						if (strpos($o->name(), $v) !== false)
						{
							$retval[$v] = $o->id();
							break;
						}
					}
				}
				else
				{*/
					// fields may contain HTML and we don't want that
					if (is_array($section) && (sizeof($section) > 0))
					{
						$prnt = "parent IN (".join(",",$section).")";
					}
					else
					{
						$prnt = "parent = " . (int)$section;
					}
					$q = "SELECT oid FROM objects
								WHERE $prnt AND $field LIKE '%$v%' AND objects.status = 2 AND objects.class_id = ".CL_MENU;
					$retval[$v] = $this->db_fetch_field($q,"oid");
					if (!$retval[$v])
					{
						$q = "SELECT oid FROM objects
									WHERE $prnt AND $field LIKE '%$v%' AND objects.status = 2 AND objects.class_id = ".CL_DOCUMENT;
						$retval[$v] = $this->db_fetch_field($q,"oid");
					}
//				}
			}; // eow
			return $retval;
		}; // eoi
	}

	////
	// !Send a link to someone
	//XXX: imelikke saidi id-sid sisaldanud meetod.
	function send_link()
	{
		global $from_name, $from, $section, $copy,$to_name, $to,$comment;

		$baseurl = aw_ini_get("baseurl");
		$SITE_ID = aw_ini_get("site_id");
		$ext = AW_FILE_EXT;

		$text = "$from_name ($from) soovitab teil vaadata saiti ".$baseurl.",\nlinki ".$baseurl."index$ext?section=$section \n\n$from_name kommentaar lingile: $comment\n";
		if ($copy) $bcc = "\nCc: $copy ";

		send_mail("\"$to_name\" <".$to.">","Artikkel saidilt ".$baseurl,$text,"From: \"$from_name\" <".$from.">\nSender: \"$from_name\" <".$from.">\nReturn-path: \"$from_name\" <".$from.">".$bcc."\n\n");
	}

	function telekava_doc($content)
	{
		$paevad = array("0" => "#telekava_neljapaev#", "1" => "#telekava_reede#", "2" => "#telekava_laupaev#", "3" => "#telekava_pyhapaev#", "4" => "#telekava_esmaspaev#", "5" => "#telekava_teisipaev#", "6" => "#telekava_kolmapaev#");
		reset($paevad);
		while (list($num, $v) = each($paevad))
		{
			if (strpos($content,$v) === false)
			{
				continue;
			}
			else
			{
				break;
			}
		}

		// arvutame v2lja, et millal oli eelmine neljap2ev
		$sub_arr = array("0" => "3", "1" => "4", "2" => "5", "3" => "6", "4" => "0", "5" => "1", "6" => "2");
		$date = mktime(0,0,0,date("m"),date("d"),date("Y"));

		$d_begin = $date - $sub_arr[date("w")]*24*3600;
		$rdate = $d_begin+$num*24*3600;

	}

	/**

		@attrib name=new params=name is_public="1" caption="New document" default="0"

		@param parent required acl="add"
		@param period optional
		@param alias_to optional
		@param alias_to_prop optional
		@param return_url optional
		@param reltype optional

		@returns


		@comment

	**/
	function add($arr)
	{
		$arr["return_url"] = $arr["return_url"];
		$str = $this->mk_my_orb("new", $arr, "doc");
		return $str;
	}


	/** Displays the document edit form

		@attrib name=change params=name is_public="1" caption="Edit document" default="0"

		@param id required type=int acl="edit;view"
		@param section optional
		@param period optional
		@param return_url optional

		@returns string
	**/
	function change($arr)
	{
		$oob = obj($arr["id"]);
		$oob = $oob->fetch();

		if ($oob["class_id"] == CL_BROTHER_DOCUMENT && $oob["brother_of"])
		{
			$id = $oob["brother_of"];
		}

		$url = $this->mk_my_orb("change", array(
			"id" => $oob["oid"],
			"section" => $this->req->arg("section"),
			"period" => isset($arr["period"]) ? $arr["period"] : "",
			"is_sa" => $this->req->arg("is_sa"),
			"edit_version" => $this->req->arg("edit_version"),
			"return_url" => $this->req->arg("return_url")
		), "doc");
		return $url;
	}

	/** Performs a search from all documents

		@attrib name=search params=name nologin="1" default="0"

		@param parent optional
		@param str optional
		@param section optional
		@param sortby optional
		@param from optional

		@returns


		@comment
		parent - alates millisest sektsioonist otsida
		str - mida otsida
		section - section id, mille sisu asemel otsingutulemusi naidatakse
		sortby - mille jargi otsingu tulemusi sortida
		from - alates millisest vastusest naitama hakatakse?

	**/
	function do_search($arr = array())
	{
		extract($arr);
		if ($sortby == "")
		{
			$sortby = "percent";
		}

		$parent = (int)$parent;
		if (!$parent  || $parent === "default")
		{
			$parent = $this->get_cval("search::default_group");
		}

		$str = trim($str);
		$this->tpl_init("automatweb/documents");
		// kas ei peaks checkkima ka teiste argumentide oigsust?
		$ostr = $str;
		$this->quote($str);

		// otsingustringi polnud, redirect veateatele. Mdx, kas selle
		// asemel ei voiks ka mingit custom dokut kasutada?
		if ($str == "")
		{
			$this->read_template("search_none.tpl");
			lc_site_load("document",$this);
			return $this->parse();
		}

		$this->read_template("search.tpl");

		lc_site_load("document",$this);

		// genereerime listi koikidest menyydest, samasugune kood on ka
		// mujal olemas, kas ei voiks seda kuidagi yhtlustada?

		// besides, mulle tundub, et otsingutulemusi kuvades taidetakse seda koodi
		// 2 korda .. teine kord menueditis. Tyhi too?
		if ($this->cfg["lang_menus"] == 1)
		{
			$ss = " AND (objects.lang_id = ".aw_global_get("lang_id")." OR menu.type = ".MN_CLIENT.")";
		}
		else
		{
			$ss = "";
		};

		$sc = get_instance("contentmgmt/site_search/search_conf");
		$search_groups = $sc->get_groups();

		if ($search_groups[$parent]["search_form"])
		{
			// do form search
			$finst = get_instance(CL_FORM);
			// we must load the form before we can set element values
			$finst->load($search_groups[$parent]["search_form"]);

			// set the search elements values
			foreach($search_groups[$parent]["search_elements"] as $el)
			{
				$finst->set_element_value($el, $str, true);
			}

			global $restrict_search_el,$restrict_search_val,$use_table,$search_form;
			$this->vars(array(
				"MATCH" => $finst->new_do_search(array(
					"restrict_search_el" => $restrict_search_el,
					"restrict_search_val" => $restrict_search_val,
					"use_table" => $use_table,
					"section" => $section,
					"search_form" => $search_form
				)),
				"HEADER" => ""
			));

			return $this->parse();
		}

		$search_group = $search_groups[$parent];

		$this->menucache = array();
		$this->db_query("SELECT objects.oid as oid, objects.parent as parent,objects.last as last,objects.status as status,objects.metadata as metadata,objects.name as name
										 FROM objects LEFT JOIN menu ON menu.id = objects.oid
										 WHERE objects.class_id = 1 AND objects.status = 2 $ss");
		$parent_list = array();
		while ($row = $this->db_next())
		{
			// peame tshekkima et kui tyyp pole sisse loginud et siis ei otsitaks users only menyyde alt
			$can = true;
			if (aw_global_get("uid") == "" && $search_group["no_usersonly"] == 1)
			{
				$meta = aw_unserialize($row["metadata"]);
				if ($meta["users_only"] == 1)
				{
					$can = false;
				}
			}
			if ($can)
			{
				$this->menucache[$row["parent"]][] = $row;
				$this->mmc[$row["oid"]] = $row;
			}
		}
		// find the parent menus based on the search menu group id
		$parens = $sc->get_menus_for_grp($parent);
		$this->darr = array();
		$this->marr = array();
		if (is_array($parens))
		{
			foreach($parens as $_parent)
			{
				if ($this->can("view",$_parent))
				{
					$this->marr[] = $_parent;
					// list of default documents
					if (is_array($this->mmc[$_parent]))
					{
						$this->rec_list($_parent,$this->mmc[$_parent]["name"]);
					}
				};
			}
		};

		$ml = join(",",$this->marr);
		$ml2 = join(",",$this->darr);
		if ($ml != "")
		{
			$ml = " AND objects.parent IN ($ml) ";
		}

		/*if ($ml2 != "")
		{
			$ml.= " AND objects.oid IN ($ml2) ";
		}*/

		if ($sortby === "time")
		{
			$ml.=" ORDER BY objects.modified DESC";
		}

		// oh crap. siin peab siis failide seest ka otsima.
		$mtfiles = array();
		// this query is _very_ expensive, if there are lots of records in
		// the files table OTOH, it's quite hard to "fix" this as it is -- duke
		$this->db_query("SELECT id FROM files WHERE files.showal = 1 AND files.content LIKE '%$str%' ");
		while ($row = $this->db_next())
		{
			$mtfiles[] = $row["id"];
		}

		$docids = array();

		$fstr = join(",",$mtfiles);
		if ($fstr != "")
		{
			// nyyd leiame k6ik aliased, mis vastavatele failidele tehtud on
			// and we need that because .. ?
			$this->db_query("SELECT * FROM aliases WHERE target IN ($fstr)");
			while ($row = $this->db_next())
			{
				$docids[$row["source"]] = $row["source"];
				//$faliases[] = $row["source"];
			}
			// nyyd on $faliases array dokumentidest, milles on tehtud aliased matchivatele failidele.
			/*
			if (is_array($faliases))
			{
				$fasstr = "OR documents.docid IN (".join(",",$faliases).")";
			}
			*/
		}

		// nini. otsime tabelite seest ka.
		$mts = array();
		// expensive as well. We need a better way to do searches - shurely there
		// are some good algoritms for that? -- duke
		$this->db_query("SELECT id FROM aw_tables WHERE contents LIKE '%$str%'");
		while ($row = $this->db_next())
		{
			$mts[] = $row["id"];
		}

		$mtsstr = join(",",$mts);
		if ($mtsstr != "")
		{
			// nyyd on teada k6ik tabelid, ksu string sisaldub
			// leiame k6ik aliased, mis on nendele tabelitele tehtud
			$mtals = array();
			$this->db_query("SELECT * FROM aliases WHERE target IN ($mtsstr)");
			while ($row = $this->db_next())
			{
				$docids[$row["source"]] = $row["source"];
				//$mtals[$row["source"]] = $row["source"];
			}

			/*

			$mts = join(",",$mtals);
			if ($mts != "")
			{
				// see on siis nimekiri dokudest, kuhu on tehtud aliased tabelitest, mis matchisid
				$mtalsstr = "OR documents.docid IN (".$mts.")";
			}
			*/
			//echo "ms = $mtalsstr<br />";
		}

		$cnt = 0;
		//max number of occurrences of search string in document
		$max_count = 0;
		$docarr = array();

		if ( sizeof($docids) > 0 )
		{
			$docidstr = " OR documents.docid IN (" . join(",",$docids) . ")";
		};

		$plist = join(",",$parent_list);
		if ($ostr[0] === "\"")
		{
			$str = substr($str, 2,strlen($str)-4);
			// search concrete quoted string
			$docmatch = "documents.title LIKE '%".$str."%' OR documents.content LIKE '%".$str."%' OR documents.author LIKE '%".$str."%'";
			if ($this->cfg["use_dcache"])
			{
				$docmatch .= " OR documents.dcache LIKE '%" . $str . "%'";
			};
		}
		else
		{
			// search all words
			$wds = explode(" ",$str);
			$docmatcha = array();
			$docmatcha[] = join(" AND ",map("documents.title LIKE '%%%s%%'",$wds));
			$docmatcha[] = join(" AND ",map("documents.content LIKE '%%%s%%'",$wds));
			$docmatcha[] = join(" AND ",map("documents.author LIKE '%%%s%%'",$wds));
			if ($this->cfg["use_dcache"])
			{
				$docmatcha[] = join(" AND ",map("documents.dcache LIKE '%%%s%%'",$wds));
			};
			$docmatch = join(" OR ", map("(%s)",$docmatcha));
		}
		$perstr = "";
		if (aw_ini_get("search_conf.only_active_periods"))
		{
			$pei = get_instance(CL_PERIOD);
			$plist = $pei->period_list(0,false,1);
			$perstr = " AND  objects.period IN (".join(",", array_keys($plist)).")";
		}
		$q = "SELECT documents.*,objects.parent as parent, objects.modified as modified, objects.parent as parent
										 FROM documents
										 LEFT JOIN objects ON objects.oid = documents.docid
										 WHERE ($docmatch) AND objects.status = 2 $perstr AND objects.lang_id = ".aw_global_get("lang_id")." AND objects.site_id = " . $this->cfg["site_id"] . " AND (documents.no_search is null OR documents.no_search = 0) $ml";
		$si = __get_site_instance();
		$this->db_query($q);
		while($row = $this->db_next())
		{
			if (not($this->can("view",$row["docid"])))
			{
				continue;
			};

			if (aw_global_get("uid") == "" && $search_group["no_usersonly"] == 1)
			{
				// check the object
				$o = obj($row["docid"]);
				$uson = false;
				foreach($o->path() as $p_o)
				{
					if ($p_o->class_id() == CL_MENU && $p_o->prop("users_only"))
					{
						$uson = true;
					}
				}

				if ($uson)
				{
					continue;
				}
			}

			// find number of matches in document for search string, for calculating percentage
			// if match is found in title, then multiply number by 5, to emphasize importance

			// hook for site specific document parsing
			if (is_object($si))
			{
				if ($si->parse_search_result_document($row) == -1)
				{
					continue;
				}
			}

			$c = substr_count(strtoupper($row["content"]),strtoupper($str)) + substr_count(strtoupper($row["title"]),strtoupper($str))*5;
			$max_count = max($c,$max_count);

			// find the first paragraph of text or lead if it contains something
			if ($row["lead"] != "")
			{
				$co = strip_tags($row["lead"]);
				$co = preg_replace("/#(\w+?)(\d+?)(v|k|p|)#/i","",$co);
			}
			else
			{
				if (aw_ini_get("content.doctype") == "xhtml")
				{
					$p1 = strpos($row["content"],"<br />");
				}
				else if (aw_ini_get("content.doctype") == "html")
				{
					$p1 = strpos($row["content"],"<br>");
				}
				$p2 = strpos($row["content"],"</p>");
				$pos = min($p1,$p2);
				$co = substr($row["content"],0,$pos);
				$co = strip_tags($co);
				$co = preg_replace("/#(\w+?)(\d+?)(v|k|p|)#/i","",$co);
			}
			// to hell with html in titles
			$row["title"] = strip_tags($row["title"]);
			$title = ($row["title"]) ? $row["title"] : "(nimetu)";
			$docarr[] = array(
				"matches" => $c,
				"title" => $title,
				"section" => $row["docid"],
				"content" => $co,
				"modified" => $this->time2date($row["modified"],5),
				"tm" => $row["tm"],
				"parent" => $row["parent"]
			);
			$cnt++;

		}

		if ($sortby == "percent")
		{
			$d2arr = array();
			reset($docarr);
			while (list(,$v) = each($docarr))
			{
				if ($max_count == 0)
				{
					$d2arr[100][] = $v;
				}
				else
				{
					$d2arr[($v[matches]*100) / $max_count][] = $v;
				}
			}

			krsort($d2arr,SORT_NUMERIC);

			$docarr = array();
			reset($d2arr);
			while (list($p,$v) = each($d2arr))
			{
				reset($v);
				while (list(,$v2) = each($v))
				{
					$docarr[] = $v2;
				}
			}

		}

		$per_page = 10;

		$mned = get_instance("menuedit");

		$num = 0;
		reset($docarr);
		while (list(,$v) = each($docarr))
		{
			if ($num >= $from && $num < ($from + $per_page))	// show $per_page matches per screen
			{
				if ($max_count == 0)
				{
					$sstr = 100;
				}
				else
				{
					$sstr = substr(($v["matches"]*100) / $max_count,0,4);
				}

				$sec = $v["section"];
				if ($mc->subs[$v["parent"]] == 1)
				{
					// if it is the only document under the menu, make link to the menu instead
					$sec = $v["parent"];
				}

				$this->vars(array("title"			=> strip_tags($v["title"]),
													"percent"		=> $sstr,
													"content"		=> preg_replace("/#(.*)#/","",$v["content"]),
													"modified"	=> $v["tm"] == "" ? $v["modified"] : $v["tm"],
													"section"		=> $sec));
				$r.= $this->parse("MATCH");
			}
			$num++;
		}

		if ($num == 0 && $this->is_template("NO_RESULTS"))
		{
			if ($cnt == 0)
			{
				return $this->parse("NO_RESULTS");
			}
		}
		$this->vars(array(
			"MATCH" => $r,
			"s_parent" => $parent,
			"sstring" => htmlspecialchars(urldecode($str)),
			"sstringn" => htmlspecialchars($str),
			"section" => $section,
			"matches" => $cnt,
			"sortby" => $sortby
		));

		// make prev page / next page
		if ($cnt > $per_page)
		{
			if ($from > 0)
			{
				$this->vars(array(
					"from" => $from-$per_page,
					"prev_link" => $this->mk_my_orb("search", array(
						"parent" => $parent,
						"str" => $str,
						"section" => $section,
						"sortby" => $sortby,
						"from" => $from-$per_page
					)),
				));
				$prev = $this->parse("PREVIOUS");
			}
			if ($from+$per_page <= $cnt)
			{
				$this->vars(array(
					"from" => $from+$per_page,
					"next_link" => $this->mk_my_orb("search", array(
						"parent" => $parent,
						"str" => $str,
						"section" => $section,
						"sortby" => $sortby,
						"from" => $from+$per_page
					)),
				));
				$next = $this->parse("NEXT");
			}

			for ($i=0; $i < $cnt / $per_page; $i++)
			{
				$this->vars(array(
					"from" => $i*$per_page,
					"page_from" => $i*$per_page,
					"page_to" => min(($i+1)*$per_page,$cnt),
					"page_link" => $this->mk_my_orb("search", array(
						"parent" => $parent,
						"str" => $str,
						"section" => $section,
						"sortby" => $sortby,
						"from" => $i*$per_page
					)),
				));
				if ($i*$per_page == $from)
				{
					$pg.=$this->parse("SEL_PAGE");
				}
				else
				{
					$pg.=$this->parse("PAGE");
				}
			}
		}
		$this->vars(array(
			"PREVIOUS" => $prev,
			"NEXT" => $next,
			"PAGE" => $pg,
			"SEL_PAGE" => "",
			"from" => $from,
			"section" => $section,
			"sortchanged" => $this->mk_my_orb("search", array(
				"parent" => $parent,
				"str" => $str,
				"section" => $section,
				"sortby" => "time",
				"from" => $from
			)),
			"sortpercent" => $this->mk_my_orb("search", array(
				"parent" => $parent,
				"str" => $str,
				"section" => $section,
				"sortby" => "percent",
				"from" => $from
			)),
		));
		$ps = $this->parse("PAGESELECTOR");
		$this->vars(array(
			"PAGESELECTOR" => $ps,
			"HEADER" => $this->parse("HEADER")
		));

		$this->_log(ST_SEARCH, SA_DO_SEARCH, "otsis stringi $str , alamjaotusest nr $parent, leiti $cnt dokumenti");
		$this->quote($str);
		$this->quote($parent);
		$this->db_query("INSERT INTO searches(str,s_parent,numresults,ip,tm) VALUES('$str','$parent','$cnt','".aw_global_get("REMOTE_ADDR")."','".time()."')");

		$retval = $this->parse();
		return $this->parse();
	}

	function rec_list($parent,$pref = "")
	{
		if (!is_array($this->menucache[$parent]))
		{
			return;
		}

		reset($this->menucache[$parent]);
		while(list(,$v) = each($this->menucache[$parent]))
		{
			if ($v["status"] == 2)
			{
				$this->marr[] = $v["oid"];
				if ($v["last"] > 0)
				{
					$this->darr[] = $v["last"];
				}
				if (aw_ini_get("content.doctype") == "xhtml")
				{
					dbg::p("name: ".$pref."/".$v["name"]." id = ".$v["oid"]." <br />");
				}
				else if (aw_ini_get("content.doctype") == "xhtml")
				{
					dbg::p("name: ".$pref."/".$v["name"]." id = ".$v["oid"]." <br>");
				}
				$this->rec_list($v["oid"],$pref."/".$v["name"]);
			}
		}
	}

	/**

		@attrib name=lookup params=name nologin="1" default="0"

		@param id required
		@param sortby optional
		@param origin optional

		@returns


		@comment

	**/
	function lookup($args = array())
	{
		$SITE_ID = aw_ini_get("site_id");
		extract($args);
		$id = (int)$id;
		$q = "SELECT documents.author as author, objects.oid as oid, objects.name as name, documents.modified as modified FROM objects LEFT JOIN keywordrelations ON (keywordrelations.id = objects.oid) LEFT JOIN documents ON (documents.docid = objects.oid) WHERE status = 2 AND class_id IN (" . CL_DOCUMENT . "," . CL_PERIODIC_SECTION . ") AND site_id = '$SITE_ID' AND keywordrelations.keyword_id = '$id' ORDER BY documents.modified DESC";
		$retval = "";
		load_vcl("table");

		$tt = new aw_table(array(
			"prefix" => "keywords",
			"tbgcolor" => "#C3D0DC",
		));

		$tt->parse_xml_def($this->cfg["site_basedir"]."/xml/generic_table.xml");
		$tt->define_field(array(
			"name" => "name",
			"caption" => t("Pealkiri"),
			"talign" => "center",
			"sortable" => 1,
		));
		$tt->define_field(array(
			"name" => "modified",
			"type" => "time",
			"caption" => t("Kuup&auml;ev"),
			"talign" => "center",
			"format" => "j.m.y",
			"align" => "center",
			"sortable" => 1,
		));
		$tt->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Autor"),
			"talign" => "center",
			"align" => "center",
			"sortable" => 1,
		));
		$this->db_query($q);
		while($row = $this->db_next())
		{
			$this->save_handle();
			$x = $this->get_relations_by_field(array(
				"field"    => "name",
				"keywords" => $row["author"],
				"section"  => $this->cfg["link_authors_section"]
			));
			$authors = array();
			while(list($k,$v) = each($x))
			{
				if ($this->cfg["link_default_link"] != "")
				{
					if ($v)
					{
						$authors[] = sprintf("<a href='%s'>%s</a>",document::get_link($v),$k);
					}
					else
					{
						$authors[] = sprintf("<a href='%s'>%s</a>",$this->cfg["link_default_link"],$k);
					};
				}
				else
				{
					$authors[] = $k;
				}
			}

			$author = join(", ",$authors);
			$this->restore_handle();

			$tt->define_data(array(
				"name" => sprintf("<a href='%s'>%s</a>",document::get_link($row["oid"]),$row["name"]),
				//"modified" => $this->time2date($row["modified"],8),
				"modified" => $row["modified"],
				"modifiedby" => $author,
			));
		};
		$tt->set_default_sortby("modified");
		$tt->set_default_sorder("desc");
		$tt->sort_by();
		return $tt->draw();
	}

	function get_last_doc_list($num)
	{
		$tp = "";
		$this->db_query("SELECT objects.oid as oid ,name,objects.modified as modified FROM objects LEFT JOIN documents ON documents.docid = objects.brother_of WHERE (class_id = ".CL_DOCUMENT." OR class_id = ".CL_PERIODIC_SECTION.") AND status = 2 ORDER BY objects.modified DESC LIMIT $num");
		while ($row = $this->db_next())
		{
			$this->vars(array(
				"title" => $row["name"],
				"docid" => $row["oid"],
				"modified" => $this->time2date($row["modified"],2)
			));
			$tp.=$this->parse("lchanged");
		}
		$this->vars(array("lchanged" => ""));
		return $tp;
	}

	function register_parsers()
	{
		// keywordide list. bijaatch!
		$mp = $this->register_parser(array(
					"reg" => "/(#)huvid(.+?)(#)/i",
					));

		$this->register_sub_parser(array(
					"class" => "keywords",
					"reg_id" => $mp,
					"function" => "parse_aliases",
					));

		// detailed search
		$mp = $this->register_parser(array(
					"reg" => "/(#)search_conf(#)/i",
					));

		$this->register_sub_parser(array(
					"class" => "search_conf",
					"reg_id" => $mp,
					"function" => "search",
					));
	}

	function parse_alias($args = array())
	{
		extract($args);
		$d = $alias;
		if ($meta[$d["target"]])
		{
			$replacement = "<a href='/?class=objects&action=show&id=$d[target]'>$d[name]</a>";
		}
		elseif ($alias["aliaslink"] == 1)
		{
			if (aw_ini_get("menuedit.long_section_url"))
			{
				$replacement = sprintf("<a class=\"documentlink\" href='%s?section=%d'>%s</a>", aw_ini_get("baseurl"), $d["target"], $d["name"]);
			}
			else
			{
				$replacement = sprintf("<a class=\"documentlink\" href='%s%d'>%s</a>", aw_ini_get("baseurl"), $d["target"], $d["name"]);
			}
		}
		else
		{
			$replacement = $this->gen_preview(array("docid" => $d["target"], "leadonly" => 1 ));
		};
		return $replacement;


	}

	////
	// !Creates relative links inside the text
	function create_relative_links(&$text)
	{
		// linkide parsimine
		while (preg_match("/(#)(\d+?)(#)(.*)(#)(\d+?)(#)/imsU",$text,$matches))
		{
			$text = str_replace($matches[0],"<a href='#" . $matches[2] . "'>$matches[4]</a>",$text);
		}

		while(preg_match("/(#)(s)(\d+?)(#)/",$text,$matches))
		{
			$text = str_replace($matches[0],"<a name='" . $matches[3] . "'> </a>",$text);
		}
	}

	////
	// !Creates keyword relations
	// this should be toggled with a preference in site config
	function create_keyword_relations(&$text)
	{
		// FIXME: check whether that query is optimal
		$q = "SELECT keywords.keyword AS keyword,keyword_id FROM keywordrelations
			LEFT JOIN keywords ON (keywordrelations.keyword_id = keywords.oid)
			WHERE keywordrelations.id = '$this->docid'";
		$this->db_query($q);
		$keywords = array();
		while($row = $this->db_next())
		{
			$keywords[$row["keyword"]] = sprintf(" <a href='%s' title='%s'>%s</a> ",$this->mk_my_orb("lookup",array("id" => $row["keyword_id"],"section" => $docid),"document"),"LINK",$row["keyword"]);
		}

		if (is_array($keywords))
		{
			// performs the actual search and replace
			foreach ($keywords as $k_key => $k_val)
			{
				$k_key = str_replace("/","\/",$k_key);
				if (trim($k_key) != "")
				{
					$text = preg_replace("/\b$k_key\b/i",$k_val," " . $text . " ");
				}
			};
		}
	}

	/** lets the user send a document to someone else

		@attrib name=send params=name nologin="1" default="0"

		@param section required

		@returns


		@comment

	**/
	function send($arr)
	{
		extract($arr);
		$o = obj($section);
		$this->read_template("email.tpl");
		lc_site_load("document", $this);
		$this->vars(array(
			"docid" => $section,
			"section" => $section,
			"doc_name" => $o->title,
			"reforb" => $this->mk_reforb("submit_send", array("section" => $section))
		));
		return $this->parse();
	}

	/** actually sends the document as a link via e-mail

		@attrib name=submit_send params=name nologin="1" default="0"


		@returns


		@comment

	**/
	function submit_send($arr)
	{
		extract($arr);
		$this->read_template("doc_mail.tpl");
		lc_site_load("document", $this);

		$from_name = isset($arr["from_name"]) ? $arr["from_name"] : "";
		$to_name = isset($arr["to_name"]) ? $arr["to_name"] : "";
		$from = isset($arr["from"]) ? $arr["from"] : "";
		$to = isset($arr["to"]) ? $arr["to"] : "";
		$bcc = isset($arr["bcc"]) ? $arr["bcc"] : "";
		$section = isset($arr["section"]) ? $arr["section"] : "";
		$comment = isset($arr["comment"]) ? $arr["comment"] : "";

		$this->vars(array(
			"from_name" => $from_name,
			"from" => $from,
			"section" => $section,
			"comment" => $comment
		));

		if (!empty($copy))
		{
			$bcc = "\nCc: $copy ";
		}

		$tos = explode(",", $to);
		foreach($tos as $to)
		{
			if ($to_name != "")
			{
				$_to = "\"$to_name\" <".$to.">";
			}
			else
			{
				$_to = $to;
			}
			send_mail($_to,str_replace("\n","",str_replace("\r","",$this->parse("title"))),$this->parse("mail"),"Content-Type: text/plain; charset=\"".languages::USER_CHARSET."\"\nFrom: \"$from_name\" <".$from.">\nSender: \"$from_name\" <".$from.">\nReturn-path: \"$from_name\" <".$from.">".$bcc."\n\n");
		}

		$this->quote($section);
		$name = $this->db_fetch_field("SELECT name FROM objects WHERE oid = '{$section}'", "name");
		$this->_log("ST_DOCUMENT", "SA_SEND", "$from_name  $from saatis dokumendi <a href='".aw_ini_get("baseurl")."?section=".$section."'>$name</a> $to_name $to  'le",$section);

		$si = __get_site_instance();
		if (method_exists($si, "handle_send_to_friend_redirect"))
		{
			return $si->handle_send_to_friend_redirect();
		}
		return aw_ini_get("baseurl")."?section=".$section;
	}

	/**

		@attrib name=feedback params=name nologin="1" default="0"

		@param section required
		@param e optional type=int
		@param print optional type=int

		@returns

	**/
	function feedback($arr)
	{
		extract($arr);
		$e = isset($arr["e"]) ? $arr["e"] : null;

		$feedback = new feedback();
		$inf = obj($section);
		$this->read_template("feedback.tpl");
		if ($e == 1)
		{
			$this->vars(array(
				"ERROR" => $this->parse("ERROR")
			));
		}
		$this->vars(array(
			"uid" => aw_global_get("uid")
		));
		$tekst = "";
		$a = new aw_array($feedback->tekst);
		foreach($a->get() as $k => $v)
		{
			$tekst .= "<tr><td align='right'><input type='radio' name='tekst' value='$k'></td><td align=\"left\" class=\"text2\">$v</td></tr>";
		}

		$kujundus = "";
		$a = new aw_array($feedback->kujundus);
		foreach($a->get() as $k => $v)
		{
			$kujundus .= "<tr><td align='right'><input type='radio' name='kujundus' value='$k'></td><td align=\"left\" class=\"text2\">$v</td></tr>";
		};

		$struktuur = ""; $tehnika = ""; $ala = "";
		$a = new aw_array($feedback->struktuur);
		foreach($a->get() as $k => $v)
		{
			$struktuur .= "<tr><td align='right'><input type='radio' name='struktuur' value='$k'></td><td align=\"left\" class=\"text2\">$v</td></tr>";
		};

		$a = new aw_array($feedback->ala);
		foreach($a->get() as $k => $v)
		{
			$ala .= "<tr><td align='right'><input type='radio' name='ala' value='$k'></td><td align=\"left\" class=\"text2\">$v</td></tr>";
		}

		$a = new aw_array($feedback->tehnika);
		foreach($a->get() as $k => $v)
		{
			$tehnika .= "<tr><td align='right'><input type='checkbox' name='tehnika[]'  value='$k'></td><td align=\"left\" class=\"text2\">$v</td></tr>";
		}

	   	$this->vars(array(
			"docid" => $section,
			"tekst" => $tekst,
			"kujundus" => $kujundus,
			"struktuur" => $struktuur,
			"ala" => $ala,
			"tehnika" => $tehnika,
			"title" => $inf->title,
			"reforb" => $this->mk_reforb("submit_feedback", array("docid" => $section, "print" => empty($print) ? null : $print))
		));
		return $this->parse();
	}

	/**
		@attrib name=submit_feedback params=name nologin="1" default="0"
	**/
	function submit_feedback($arr)
	{
		extract($arr);
		$inf = obj($docid);
		$feedback = new feedback();
		$arr["title"] = $inf->title;
		$feedback->add_feedback($arr);
		$this->_log("ST_DOCUMENT", "SA_SEND", "$eesnimi $perenimi , email:$mail saatis feedbacki", $docid);
		$params = array("section" => $docid,"eesnimi" => $eesnimi);
		if($print)
		{
			$params["print"] = $print;
		}
		return $this->mk_my_orb("thanks", $params);
	}

	/**
		@attrib name=thanks params=name nologin="1" default="0"
		@param eesnimi optional
	**/
	function thanks($arr)
	{
		extract($arr);
		$this->read_template("feedback_thanks.tpl");
		$this->vars(array(
			"eesnimi" => strip_tags($eesnimi),
		));
		return $this->parse();
	}

	/**
		@attrib name=print params=name nologin="1" default="0"
		@param section required
	**/
	function do_print($arr)
	{
		extract($arr);
		ob_start();
		$dat = $this->get_record("objects","oid",$section);
		$this->_log("ST_DOCUMENT", "SA_PRINT", "$dat[name] ",$section);
		$str = $this->gen_preview(array(
			"docid" => $section,
			"tpl" => "print.tpl",
			"is_printing" => true,
			"no_strip_lead" => 1
		));

		if (aw_ini_get("document.printview_expand_links") == 1 )
		{
			// find href links in html
			preg_match_all("/\<a.*href=[\"'](.*)[\"'].*>(.*)\<\/a>/imsU", $str, $a_link_matches);
			foreach ($a_link_matches[0] as $key => $var)
			{
				if (	strpos($a_link_matches[1][$key], "mailto") === false &&
						strpos($a_link_matches[1][$key], "http") === false &&
						 strpos($a_link_matches[1][$key], "https") === false
						 )
				{ // relative urls
					$a_print_link_find = array(
						"/href\s*=\s*[\"'](\/.*)[\"'](.*)>(.*)</U",
						"/href\s*=\s*[\"']([^h][^t][^t][^p][^:].*)[\"'](.*)>(.*)<|href\s*=\s*[\"']([^h][^t][^t][^p][^s][^:].*)[\"'](.*)>(.*)</U",
					);

					$a_print_link_replace = array(
						"href=\"".aw_ini_get("baseurl")."\\1\"\\2>\\3 <span class=\"url\">(".aw_ini_get("baseurl")."\\1)</span><",
						"href=\"".aw_ini_get("baseurl")."\\1\"\\2>\\3 <span class=\"url\">(".aw_ini_get("baseurl")."\\1)</span><",
					);
					$tmp = preg_replace ($a_print_link_find, $a_print_link_replace, $a_link_matches[0][$key]);
					$str = str_replace($a_link_matches[0][$key], $tmp, $str);
				}
				else if (strpos($a_link_matches[1][$key], "mailto") === false)
				{ //
					$a_print_link_find = array(
						"/href\s*=\s*[\"'](http.*)[\"'](.*)>(.*)</U",
					);

					// add baseurl only to relative urls. rest should have http scheme, dependency to prior processing of this string to be printed
					if (strpos($a_link_matches[1][$key], "http") === false)
					{
						$a_print_link_replace = array(
							"href=\"".aw_ini_get("baseurl")."\\1\"\\2>\\3 <span class=\"url\">(\\1)</span><",
						);
					}
					else
					{
						$a_print_link_replace = array(
							"href=\"\\1\"\\2>\\3 <span class=\"url\">(\\1)</span><",
						);
					}

					$tmp = preg_replace ($a_print_link_find, $a_print_link_replace, $a_link_matches[0][$key]);
					$str = str_replace($a_link_matches[0][$key], $tmp, $str);
				}
				else if (strpos($a_link_matches[1][$key], "mailto") !== false)
				{
					$a_print_link_find = array(
						"/<a.*href\s*=\s*[\"']{1}\mailto:(.*)[\"']{1}.*a>/U",
					);

					$a_print_link_replace = array(
						"\\1",
					);
					$tmp = preg_replace ($a_print_link_find, $a_print_link_replace, $a_link_matches[0][$key]);
					$str = str_replace($a_link_matches[0][$key], $tmp, $str);

				}
			}
		}
		echo $str;
		aw_shutdown();
		if (isset($GLOBALS["format"]) and $GLOBALS["format"] === "pdf")
		{
			$content = ob_get_contents();
			ob_end_clean();
			$conv = get_instance("core/converters/html2pdf");
			$pdf = $conv->convert(array("source" => $content));
			header("Content-type: application/pdf");
			die($pdf);
		}
		die();
	}

	function author_docs($author)
	{
		$lsu = aw_ini_get("menuedit.long_section_url");

		$_lim = aw_ini_get("document.max_author_docs");
		if ($_lim)
		{
			$lim = "LIMIT ".$_lim;
		}
		$perstr = "";
		if (aw_ini_get("search_conf.only_active_periods"))
		{
			$pei = get_instance(CL_PERIOD);
			$plist = $pei->period_list(0,false,1);
			$perstr = " and objects.period IN (".join(",", array_keys($plist)).")";
		}
		$sql = "
			SELECT docid,title
			FROM documents
				LEFT JOIN objects ON objects.oid = documents.docid
			WHERE author = '$author' AND objects.status = 2 $perstr
			ORDER BY objects.created DESC $lim
		";
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$this->save_handle();
			$num_comments = $this->db_fetch_field("SELECT count(*) AS cnt FROM comments WHERE board_id = '$row[docid]'","cnt");
			$this->restore_handle();

			if ($lsu)
			{
				$link = aw_ini_get("baseurl")."index.".$this->cfg["ext"]."/section=".$row["docid"];
			}
			else
			{
				$link = aw_ini_get("baseurl").$row["docid"];
			}

			$this->vars(array(
				"link" => $link,
				"comments" => $num_comments,
				"title" => strip_tags($row["title"]),
				"comm_link" => $this->mk_my_orb("show_threaded",array("board" => $row["docid"]),"forum"),
			));
			$hc = "";
			if ($num_comments > 0)
			{
				$hc = $this->parse("HAS_COMM");
			}

			$this->vars(array("HAS_COMM" => $hc));

			$c.=$this->parse("AUTHOR_DOC");
		}
		return $c;
	}

	function get_link($docid)
	{
		$lsu = aw_ini_get("menuedit.long_section_url");
		$bu = aw_ini_get("baseurl");
		if ($lsu)
		{
			return $bu."?section=$docid";
		}
		else
		{
			return $bu.$docid;
		}
	}

	////
	// !generates static pages fot the document ($id) , the document's parent menu and the documents brothers and menus
	// uses the settings set in the general static site settings for generation
	function gen_static_doc($id)
	{
		echo t("<font face='arial'>Toimub staatiliste lehtede	genereerimine, palun oodake!<br /><br />\n\n");
		echo "\n\r<br />";
		echo "\n\r<br />"; flush();
		ob_start();

		$exp = get_instance(CL_EXPORT_RULE);
		$exp->init_settings();

		$obj = obj($id);

		// doc parent
		$exp->fetch_and_save_page(aw_ini_get("baseurl")."index.".$this->cfg["ext"]."?section=".$obj->parent(), $obj->lang_id(), true);

		$exp->exp_reset();

		// doc
		$exp->fetch_and_save_page(aw_ini_get("baseurl")."index.".$this->cfg["ext"]."?section=".$id, $obj->lang_id(), true);
		// print doc
		$exp->fetch_and_save_page(aw_ini_get("baseurl")."index.".$this->cfg["ext"]."?section=".$id."&print=1", $obj->lang_id(), true);


		// if the document is on the front page, then do the damn
		// frontpage as well
		$fp = $this->db_fetch_field("SELECT frontpage_left FROM documents WHERE docid = $id", "frontpage_left");
		if ($fp == 1)
		{
			$exp->fetch_and_save_page(aw_ini_get("baseurl")."index.".$this->cfg["ext"]."?section=".aw_ini_get("frontpage"), $obj->lang_id(), true);
		}

		ob_end_clean();
		echo  t("Staatilised lehek&uuml;ljed loodud!<br />\n");
		die("<br /><br /><a href='".$this->mk_my_orb("change", array("id" => $id))."'> <<< ".
			t("tagasi dokumendi muutmise juurde")."</a>");
	}

	function do_subtpl_handlers($doc_o)
	{
		foreach($this->subtpl_handlers as $tpl => $handler)
		{
			if ($this->is_template($tpl))
			{
				$this->$handler($doc_o);
			}
		}
	}

	// failide ikoonid kui on template olemas, namely www.stat.ee jaox
	function _subtpl_file($doc_o)
	{
		$mime_registry = new aw_mime_types();
		$aliases = $doc_o->connections_from(array(
			"type" => CL_FILE
		));
		foreach($aliases as $alias)
		{
			$file = $alias->to();
			$ext = $mime_registry->ext_for_type($file->prop("type"));
			if ($ext == "")
			{
				$pos = strrpos($file->name(),".");
				if ($pos)
				{
					$ext = substr($file->name(), $pos+1);
				}
			}

			if ($ext && $ext !== "html")
			{
				$this->vars(array(
					"url" => file::get_url($file->id(),$file->name()),
					"im" => $ext
				));

				$fff .= $this->parse("FILE");
			}
		}
		$this->vars(array(
			"FILE" => $fff
		));
	}

	function do_plugins($doc_o)
	{
		// now I need to gather information about the different templates
		$plugins = $this->get_subtemplates_regex("plugin\.(\w*)");
		if (!count($plugins))
		{
			return;
		}
		$m_pl = $doc_o->meta("plugins");

		$plg_arg = array();
		foreach($plugins as $plg_name)
		{
			$plg_arg[$plg_name] = array(
				"value" => $m_pl[$plg_name],
				"tpl" => $this->templates["plugin.$plg_name"],
			);
		}

		$plg_ldr = get_instance("plugins/plugin_loader");
		$plugindata = $plg_ldr->load_by_category(array(
			"category" => get_class($this),
			"plugins" => $plugins,
			"method" => "show",
			"args" => $plg_arg,
		));

		$pvars = array();
		foreach($plugindata as $key => $val)
		{
			$name = "plugin.${key}";
			if (!empty($val))
			{
				$pvars[$name] = $this->parse($name);
			};
		};

		$this->vars($pvars);
	}

	function sanitize($str)
	{
		// remove p tags from start and end
		$string = preg_replace("/(^<p>|<\/p>$)/i","",trim($str));
		return $string;
	}

	////
	// !Registreerib uue aliasteparseri
	// argumendid:
	// class(string) - klassi nimi, passed to classload. May be empty
	// function(string) - function to be called for this alias. May be empty.
	// reg(string) - regulaaravaldis, samal kujul nagu preg_replace esimene argument

	// aw shit, siin ei saa ju rekursiivseid aliaseid kasutada. Hm .. aga kas neid
	// siis yldse kusagil kasutatakse? No ikka .. tabelis voib olla pilt.

	function register_parser($args = array())
	{
		// esimesel kasutamisel loome uue n8 dummy objekti, mille sisse
		// edaspidi registreerime koikide parserite callback meetodid
		if (!isset($this->parsers) || !is_object($this->parsers))
		{
			$this->parsers = new stdClass;
			// siia paneme erinevad regulaaravaldised
			$this->parsers->reglist = array();
		}

		extract($args);

		if (isset($class) && isset($function) && $class && $function)
		{
			if (!is_object($this->parsers->$class))
			{
				$this->parsers->$class = get_instance($class);
			};

			$block = array(
				"reg" => $reg,
				"class" => $class,
				"parserchain" => array(),
				"function" => $function,
			);
		}
		else
		{
			// kui klassi ja funktsiooni polnud defineeritud, siis j2relikult
			// soovitakse siia alla registreerida n8. sub_parsereid.
			$block = array(
				"reg" => $reg,
				"parserchain" => array(),
			);
		};
		$this->parsers->reglist[] = $block;


		// tagastab 2sja registreeritud parseriobjekti ID nimekirjas
		return sizeof($this->parsers->reglist) - 1;
	}

	////
	// !Registreerib alamparseri
	// argumendid:
	// idx(int) - millise $match array elemendi peale erutuda
	// match(string) - mis peaks elemendi v22rtuses olema, et see v2lja kutsuks
	// reg_id(int) - millise master parseri juurde see registreerida
	// class(string) - klass
	// function(string) - funktsiooni nimi
	function register_sub_parser($args = array())
	{
		extract($args);
		/*if (!isset($this->parsers->$class) || !is_object($this->parsers->$class))
		{
			$this->parsers->$class = get_instance($class);
		};*/

		$block = array(
			"idx" => isset($idx) ? $idx : 0,
			"match" => isset($match) ? $match : 0,
			"class" => $class,
			"function" => $function,
			"templates" => isset($templates) ? $templates : array(),
		);

		$this->parsers->reglist[$reg_id]["parserchain"][] = $block;
	}

	////
	// !Parsib mingi tekstibloki kasutates selleks register_parser ja register_sub_parser funktsioonide abil
	// registreeritud parsereid
	// argumendid:
	// text(string) - tekstiblokk
	// oid(int) - objekti id, mille juurde see kuulub
	function parse_aliases($args = array())
	{
		$this->blocks = array();
		extract($args);
		$o = obj($oid);
		$meta = $o->meta();

		// tuleb siis teha tsykkel yle koigi registreeritud regulaaravaldiste
		if (!is_array($this->parsers->reglist))
		{
			return;
		}

		foreach($this->parsers->reglist as $pkey => $parser)
		{
			// itereerime seni, kuni see 2sjaleitud regulaaravaldis enam ei matchi.
			$cnt = 0;
			while(preg_match($parser["reg"],$text,$matches))
			{
				$cnt++;
				if ($cnt > 50)
				{
					return;
				};
				// siia tuleb tekitada mingi if lause, mis
				// vastavalt sellele kas parserchain on defineeritud voi mitte, kutsub oige asja v2lja
				if (sizeof($parser["parserchain"] > 0))
				{
					foreach($parser["parserchain"] as $skey => $sval)
					{
						$inplace = false;
						if (($matches[$sval["idx"]] == $sval["match"]) || (!$sval["idx"]))
						{
							$cls = $sval["class"];
							$fun = $sval["function"];
							$tpls = array();

							foreach($sval["templates"] as $tpl)
							{
								$tpls[$tpl] = $this->templates[$tpl];
							};

							$params = array(
								"oid" => $oid,
								"idx" => $sval["idx"],
								"matches" => $matches,
								"tpls" => $tpls,
								"meta" => $meta,
							);

							if (!$this->parsers->$cls)
							{
								$this->parsers->$cls = get_instance($cls);
							}
							$repl = $this->parsers->$cls->$fun($params);

							if (is_array($repl))
							{
								$replacement = $repl["replacement"];
								$inplace = $repl["inplace"];
							}
							else
							{
								$replacement = $repl;
							};

							if (is_array($this->parsers->$cls->blocks))
							{
								$this->blocks = $this->blocks + $this->parsers->$cls->blocks;
							};

							if ($inplace)
							{
								$this->vars(array($inplace => $replacement));
								$inplace = false;
								$text = preg_replace($parser["reg"],"",$text,1);
							}
							else
							{
								$text = preg_replace($parser["reg"],$replacement,$text,1);
							};
							$replacement = "";
						};
					};
				};
			};
		};
		return $text;
	}

	// loeb sisse XML formaadis stiilifaili. See kust data tuleb pole enam selle klassi
	// vaid calleri probleem
	function define_styles($data)
	{
		// that's the whole magic
		$parser = xml_parser_create();
		xml_parse_into_struct($parser, $data, $values, $tags);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_free($parser);

		foreach ($values as $element)
		{
			if ($element["tag"] === "TAG")
			{
				$id = $element["attributes"]["ID"];
				$this->tags[$id] = $element["value"];
			};
		};

	}

	//This could be bloody slow
	function parse_keywords(&$content)
	{
		$cleaned = str_replace("&nbsp;", "", strip_tags($content));
		// ya can't put a dot in here, cause that breaks stupid-ass keywords that have dots in them. so if you need to find keywords that are at the end of the sentence, then
		// ya gotta figure out a way to regex so that a.b does not get split but a. does.
		$content_arr = preg_split('/[\s|,]+/', $cleaned);
		$content_arr2 = $content_arr;

		for($i=0; $i<count($content_arr); $i++)
		{
			$str = trim($content_arr[$i]);
			if ($str != "")
			{
				$content_arr[$i] = "%".$str."%";
			}
		}

		//Lets find keywords in document
		$keywords = new object_list(array(
			"class_id" => CL_KEYWORD,
			"keyword" => $content_arr,
			"site_id" => array()
		));

		foreach ($keywords->arr() as $keyword)
		{
			$keys_arr[strtolower($keyword->prop("keyword"))] = $keyword->id();
		}

		if(!$keys_arr)
		{
			return;
		}


		for($i=0; $i<count($content_arr2); $i++)
		{
			$kw = strtolower($content_arr2[$i]);
			if (isset($keys_arr[$kw]))
			{
				$keyid = $keys_arr[$kw];

				$href = html::href(array(
					"caption" => $content_arr2[$i],
					"url" => $this->mk_my_orb("show_documents", array(
						"id" => $keyid,
						"section" => aw_global_get("section"),
					), CL_KEYWORD),
				));
				$content = preg_replace("/([>|\s|\.|,|\&]+)(".preg_quote($content_arr2[$i]).")([\s|\.|,|\&|<]+)/imsU", "\\1".$href."\\3", $content);
			}
		}
		return $content;
	}

	function parse_text($text)
	{
		foreach ($this->tags as $tag => $val)
		{
			$find = "#${tag}#(.*)#\\/${tag}#";
			$val = trim($val);
			$text = preg_replace("/" . $find . "/ismU",$val,$text);
		}
		return $text;
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		if ($table === "planner" && $field === "deadline")
		{
			// create column and copy values from tasks
			$this->db_add_col($table, array(
				"name" => $field,
				"type" => "int"
			));
			$ol = new object_list(array(
				"class_id" => CL_TASK,
				"lang_id" => array(),
				"site_id" => array()
			));
			foreach($ol->arr() as $o)
			{
				$o->set_prop("deadline", $o->meta("deadline"));
				$o->save();
			}
		}

		if($table === "planner" && $field === "aw_is_work")
		{
			$this->db_add_col($table, array(
				"name" => $field,
				"type" => "int"
			));
		}

		switch($field)
		{
			case "ucheck2":
			case "ucheck3":
			case "ucheck4":
			case "ucheck5":
			case "ucheck6":
			case "image":
			case "aw_varuser1":
			case "aw_varuser2":
			case "aw_varuser3":
			case "aw_target_audience":
			case "aw_is_goal":
			case "aw_in_budget":
			case "no_show_in_promo":
			case "ufupload1":
			case "show_to_country":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "int"
				));
				return true;

			case "aw_ucheck1":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "int"
				));
				$this->resque_from_meta($table, $field);
				return true;

			case "user1":
			case "user2":
			case "user3":
			case "user4":
			case "user5":
			case "user6":
			case "user7":
			case "user8":
			case "user9":
			case "user10":
			case "user11":
			case "user12":
			case "user13":
			case "user14":
			case "user15":
			case "user16":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "text"
				));
				return true;

			case "aw_userta2":
			case "aw_userta3":
			case "aw_userta4":
			case "aw_userta5":
			case "aw_userta6":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "text"
				));
				$this->resque_from_meta($table, $field);
				return true;

			case "show_to_country":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "varchar(255)"
				));
				return true;
		}
		return false;
	}

	private function resque_from_meta($t, $f)
	{
		$map = array(
			"aw_ucheck1" => "ucheck1",
			"aw_userta2" => "userta2",
			"aw_userta3" => "userta3",
			"aw_userta4" => "userta4",
			"aw_userta5" => "userta5",
			"aw_userta6" => "userta6",
		);
		if(isset($map[$f]))
		{
			$ol = new object_list(array(
				"class_id" => CL_DOCUMENT,
				"lang_id" => array(),
				"site_id" => array(),
			));
			foreach($ol->arr() as $oid => $o)
			{
				$v = $o->meta($map[$f]);
				$this->db_query("UPDATE $t SET $f = '$v' WHERE docid = '$oid' LIMIT 1;");
			}
		}
	}

	// todo 2 viimast if'i
	function get_date_human_readable($i_timestamp_created)
	{
		$a_months = array(
			1=>t("jaanuar"),
			2=>t("veebruar"),
			3=>t("m&auml;rts"),
			4=>t("aprill"),
			5=>t("mai"),
			6=>t("juuni"),
			7=>t("juuli"),
			8=>t("august"),
			9=>t("september"),
			10=>t("oktoober"),
			11=>t("november"),
			12=>t("detsember")
		);

		$i_time_from_created_to_current_time = time() - $i_timestamp_created;

		if ($i_time_from_created_to_current_time < 60)
		{
			return t("Just postitatud");
		}
		else if ($i_time_from_created_to_current_time < 60*60)
		{
			$i_minutes = floor($i_time_from_created_to_current_time / 60);
			if ($i_minutes == 1)
			{
				return t(sprintf("%s minut tagasi",$i_minutes));
			}
			else
			{
				return t(sprintf("%s minutit tagasi",$i_minutes));
			}
		}
		else if ($i_time_from_created_to_current_time < 60*60*24)
		{
			$i_hours = floor($i_time_from_created_to_current_time / 60 / 60);
			if ($i_hours == 1)
			{
				return t(sprintf("%s tund tagasi",$i_hours));
			}
			else
			{
				return t(sprintf("%s tundi tagasi",$i_hours));
			}
		}
		else if ($i_time_from_created_to_current_time < 60*60*24*31)
		{
			$i_days = floor($i_time_from_created_to_current_time / 60 / 60 / 24);
			if ($i_days == 1)
			{
				return t(sprintf("%s p&auml;ev tagasi",$i_days));
			}
			else
			{
				return t(sprintf("%s p&auml;eva tagasi",$i_days));
			}
		}
		else if (date("Y", $i_timestamp_created) == date("Y", time() ))
		{
			return date("j", $i_timestamp_created).". ".$a_months[date("n", $i_timestamp_created)];
		}
		else
		{
			return date("j", $i_timestamp_created).". ".$a_months[date("n", $i_timestamp_created)]." ".date("Y", $i_timestamp_created);
		}
	}

	/**
		@attrib name=get_comment_stats params=pos nologin="1"
		@param start required type=int
			stats start timestamp
		@returns array()
			array(year => array(month => comments))
		@comment
	**/
	function get_comment_stats($start)
	{
		$docs = array();
		$q = "SELECT oid FROM objects WHERE class_id='".CL_DOCUMENT."' AND created > '".$start."' AND status>0";
		$this->db_query($q);
		while($crow = $this->db_next())
		{
			$docs[$crow["oid"]] = $crow["id"];
		}

		$result = array();
		$months = array();
		$q = "SELECT time,board_id FROM comments  WHERE time > '".$start."'";
		$this->db_query($q);
		while($crow = $this->db_next())
		{
			if(array_key_exists($crow["board_id"] , $docs))
			{
				$cnt++;
				$result[date("Y" , $crow["time"])][date("m" , $crow["time"])]++;
			}
		}
		return $result;
	}

}
