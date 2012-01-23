<?php

class doc_display extends aw_template
{
	var $no_left_pane;
	var $no_right_pane;
	var $templates; //TODO: m22rata skoop

	function doc_display()
	{
		$this->init();
	}

	/** displays document
		@attrib api=1 params=name

		@param docid required type=oid
			document id

		@param tpl optional type=string
			template file to use

		@param leadonly optional type=bool
			show only lead, defaults to full

		@param vars optional type=array
			extra vars for doc template

		@param not_last_in_list optional type=bool

		@param no_link_if_not_act optional type=bool

	**/
	function gen_preview($arr)
	{
		$arr["leadonly"] = isset($arr["leadonly"]) ? $arr["leadonly"] : null;
		$doc = obj($arr["docid"]);
		if (aw_ini_get("config.object_versioning") == 1 && $_GET["docversion"] != "")
		{
			$doc->load_version($_GET["docversion"]);
		}
		if(!$doc->is_visible_to())
		{
			return t("Ei ole &otilde;igust n&auml;ha");
		}
		$doc_parent = obj($doc->parent());
		$this->tpl_reset();
		$this->tpl_init("automatweb/documents");
		$tpl_file = $this->_get_template($arr);
		$this->read_any_template($tpl_file);

		$si = __get_site_instance();

		if ($si)
		{
			$si->parse_document_new($doc);
		}

		if ($this->template_has_var("text"))
		{
			$text = $this->get_document_text($arr, $doc);
		}
		$lead = "";
		if ($this->template_has_var("lead"))
		{
			$lead = $this->_get_lead($arr, $doc);
		}
		$content = "";
		if ($this->template_has_var("content"))
		{
			$content = $this->_get_content($arr, $doc);
		}

		// parse keyword subs
		$this->parse_keywords($doc);

		$this->create_relative_links($text);
		$text_no_aliases = preg_replace("/#(\w+?)(\d+?)(v|k|p|)#/i","",$text);

		$al = new alias_parser();
		$mt = $doc->meta();

		if ($this->template_has_var("text"))
		{
			$al->parse_oo_aliases(
				$doc->id(),
				$text,
				array(
					"templates" => &$this->templates,
					"meta" => &$mt
				)
			);
		}

		if ($this->template_has_var("content"))
		{
			$al->parse_oo_aliases(
				$doc->id(),
				$content,
				array(
					"templates" => &$this->templates,
					"meta" => &$mt
				)
			);
		}

		if ($this->template_has_var("lead"))
		{
			$al->parse_oo_aliases(
				$doc->id(),
				$lead,
				array(
					"templates" => &$this->templates,
					"meta" => &$mt
				)
			);
		}

		$this->vars(array("image_inplace" => ""));
		$this->vars($al->get_vars());
		$docmod = $doc->prop("doc_modified");
		$_date = $doc->prop("doc_modified") > 1 ? $doc->prop("doc_modified") : $doc->modified();
		$modf = $doc->modifiedby();
		$modf_eml = "";
		if ($modf != "" && $this->template_has_var_full("modifiedby_email"))
		{
			$u = get_instance(CL_USER);
			$p = $u->get_person_for_uid($modf);
			$modf = $p->name();
			$modf_eml = $p->prop("email.mail");
		}

		$doc_link = $this->get_doc_link($doc);

		$em = $this->template_has_var_full("edit_doc") ? $this->_get_edit_menu($doc) : NULL;

		$orig = $doc->get_original();

		$user1 = $doc->trans_get_val("user1");
		$al->parse_oo_aliases($doc->id(), $user1, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user1")));
		$user2 = $doc->trans_get_val("user2");
		$al->parse_oo_aliases($doc->id(), $user2, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user2")));
		$user3 = $doc->trans_get_val("user3");
		$al->parse_oo_aliases($doc->id(), $user3, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user3")));
		$user4 = $doc->trans_get_val("user4");
		$al->parse_oo_aliases($doc->id(), $user4, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user4")));
		$user5 = $doc->trans_get_val("user5");
		$al->parse_oo_aliases($doc->id(), $user5, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user5")));
		$user6 = $doc->trans_get_val("user6");
		$al->parse_oo_aliases($doc->id(), $user6, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user6")));
		$user7 = $doc->trans_get_val("user7");
		$al->parse_oo_aliases($doc->id(), $user7, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user7")));
		$user8 = $doc->trans_get_val("user8");
		$al->parse_oo_aliases($doc->id(), $user8, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user8")));
		$user9 = $doc->trans_get_val("user9");
		$al->parse_oo_aliases($doc->id(), $user9, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user9")));
		$user10 = $doc->trans_get_val("user10");
		$al->parse_oo_aliases($doc->id(), $user10, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user10")));
		$user11 = $doc->trans_get_val("user11");
		$al->parse_oo_aliases($doc->id(), $user11, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user11")));
		$user12 = $doc->trans_get_val("user12");
		$al->parse_oo_aliases($doc->id(), $user12, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user12")));
		$user13 = $doc->trans_get_val("user13");
		$al->parse_oo_aliases($doc->id(), $user13, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user13")));
		$user14 = $doc->trans_get_val("user14");
		$al->parse_oo_aliases($doc->id(), $user14, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user14")));
		$user15 = $doc->trans_get_val("user15");
		$al->parse_oo_aliases($doc->id(), $user15, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user15")));
		$user16 = $doc->trans_get_val("user16");
		$al->parse_oo_aliases($doc->id(), $user16, array("templates" => $this->templates, "meta" => $mt, "data" => array("prop" => "user16")));

		$userta1 = $orig->trans_get_val("userta1");
		$al->parse_oo_aliases($doc->id(), $userta1, array("templates" => $this->templates, "meta" => $mt));
		$userta2 = $orig->trans_get_val("userta2");
		$al->parse_oo_aliases($doc->id(), $userta2, array("templates" => $this->templates, "meta" => $mt));
		$userta3 = $orig->trans_get_val("userta3");
		$al->parse_oo_aliases($doc->id(), $userta3, array("templates" => $this->templates, "meta" => $mt));
		$userta4 = $orig->trans_get_val("userta4");
		$al->parse_oo_aliases($doc->id(), $userta4, array("templates" => $this->templates, "meta" => $mt));
		$userta5 = $orig->trans_get_val("userta5");
		$al->parse_oo_aliases($doc->id(), $userta5, array("templates" => $this->templates, "meta" => $mt));
		$userta6 = $orig->trans_get_val("userta6");
		$al->parse_oo_aliases($doc->id(), $userta6, array("templates" => $this->templates, "meta" => $mt));

		$title = $doc->trans_get_val("title");
		if (aw_global_get("set_doc_title") != "")
		{
			$title = aw_global_get("set_doc_title");
			aw_global_set("set_doc_title","");
		}

		$uinst = get_instance(CL_USER);
		$mb_person = obj();
		if ($this->template_has_var_full("modified_by"))
		{
			$mb_person = $uinst->get_person_for_uid($doc->prop("modifiedby"));
		}
		$this->vars($al->get_vars());

		$lang_id = aw_global_get("lang_id");
		$sel_lang = languages::fetch($lang_id);

		$title = $doc->trans_get_val("title");
		$tmp = $arr;
		$tmp["leadonly"] = -1;
		$this->vars_safe(array(
			"date_est_docmod" => $docmod > 1 ? aw_locale::get_lc_date($_date, LC_DATE_FORMAT_LONG) : "",
			"text" => $text,
			"fullcontent" => $this->get_document_text($tmp, $doc),
			"text_no_aliases" => $text_no_aliases,
			"title" => $title,
			"author" => $doc->prop("author"),
			"docid" => $doc->id(),
			"modified_by" => $mb_person->name(),
			"date_est" => aw_locale::get_lc_date($_date, LC_DATE_FORMAT_LONG),
			"date_est_fullyear" => aw_locale::get_lc_date($_date, LC_DATE_FORMAT_LONG_FULLYEAR),
			"date_est_fullyear_short" => aw_locale::get_lc_date($_date, LC_DATE_FORMAT_SHORT_FULLYEAR),
			"print_date_est" => aw_locale::get_lc_date(time(), LC_DATE_FORMAT_LONG),
			"modified" => date("d.m.Y", $doc->modified()),
			"created_tm" => $doc->created(),
			"created_hr" => "<?php classload(\"doc_display\"); echo doc_display::get_date_human_readable(".$doc->created()."); ?>",
			"created_human_readable" => "<?php classload(\"doc_display\"); echo doc_display::get_date_human_readable(".$doc->created()."); ?>",
			"created" => date("d.m.Y", $doc->created()),
			"modifiedby" => $modf,
			"modifiedby_email" => $modf_eml,
			"parent_id" => $doc->parent(),
			"parent_name" => $doc_parent->name(),
			"user1" => $user1,
			"user2" => $user2,
			"user3" => $user3,
			"user4" => $user4,
			"user5" => $user5,
			"user6" => $user6,
			"user7" => $user7,
			"user8" => $user8,
			"user9" => $user9,
			"user10" => $user10,
			"user11" => $user11,
			"user12" => $user12,
			"user13" => $user13,
			"user14" => $user14,
			"user15" => $user15,
			"user16" => $user16,
			"userta1" => $userta1,
			"userta2" => $userta2,
			"userta3" => $userta3,
			"userta4" => $userta4,
			"userta5" => $userta5,
			"userta6" => $userta6,
			"link_text" => $doc->prop("link_text"),
			"page_title" => strip_tags($title),
			"date" => $_date,
			"doc_modified" => $_date, // backward compability
			"edit_doc" => $em,
			"doc_link" => $doc_link,
			"document_link" => $doc_link,
			"print_link" => aw_url_change_var("print", 1),
			"printlink" => aw_url_change_var("print", 1), // backward compability
			"trans_lc" => aw_global_get("ct_lang_lc"),
			"lead" => $lead,
			"content" => $content,
			"alias" => $doc->trans_get_val("alias"),
			"last_error_message" => isset($_SESSION["aw_session_track"]["aw"]["last_error_message"]) ? $_SESSION["aw_session_track"]["aw"]["last_error_message"] : "",
			"lang_code" => $sel_lang["acceptlang"]
		));

		$ablock = "";
		if ($doc->prop("author") != "")
		{
			$this->vars(array(
				"ablock" => $this->parse("ablock")
			));
		}

		$nll = "";
		if (!empty($arr["not_last_in_list"]))
		{
			$nll = $this->parse("NOT_LAST_IN_LIST");
		}
		$this->vars(array(
			"NOT_LAST_IN_LIST" => $nll
		));

		$this->vars(array(
			"logged" => (aw_global_get("uid") != "" ? $this->parse("logged") : ""),
		));

		if (aw_global_get("print") == 1)
		{
			aw_session_set("no_cache", 1);
			$cs = aw_global_get("charset");
			if (aw_ini_get("user_interface.full_content_trans"))
			{
				$ld = languages::fetch(aw_global_get("ct_lang_id"));
				$cs = $ld["charset"];
			}
			header("Content-type: text/html; charset=".$cs);
		}

		$this->vars(array(
			"SHOW_MODIFIED" => ($doc->prop("show_modified") ? $this->parse("SHOW_MODIFIED") : ""),
		));

		$ps = "";
		if (( ($doc->prop("show_print")) && empty($_GET["print"]) && $arr["leadonly"] != 1))
		{
			$ps = $this->parse("PRINTANDSEND");
		}

		if ($doc->prop("title_clickable"))
		{
			$this->vars(array("TITLE_LINK_BEGIN" => $this->parse("TITLE_LINK_BEGIN"), "TITLE_LINK_END" => $this->parse("TITLE_LINK_END")));
		}
		$this->vars_safe(array(
			"SHOW_TITLE" => ($doc->prop("show_title") == 1 && $doc->prop("title") != "") ? $this->parse("SHOW_TITLE") : "",
			"PRINTANDSEND" => $ps,
			"SHOW_MODIFIED" => ($doc->prop("show_modified") ? $this->parse("SHOW_MODIFIED") : ""),
		));

		$this->_do_forum($doc);
		$this->_do_charset($doc);
		$this->_do_checkboxes($doc);
		$this->_do_user_subs($doc);

		$this->vars(array(
			"logged" => (aw_global_get("uid") != "" ? $this->parse("logged") : ""),
		));

		if (aw_global_get("print") == 1)
		{
			aw_session_set("no_cache", 1);
			$cs = aw_global_get("charset");
			if (aw_ini_get("user_interface.full_content_trans"))
			{
				$ld = languages::fetch(aw_global_get("ct_lang_id"));
				$cs = $ld["charset"];
			}
			header("Content-type: text/html; charset=".$cs);
		}
		$str = $this->parse();
		$this->vars(array("image_inplace" => ""));
		return $str;
	}

	private function _get_template($arr)
	{
		// use special template for printing if one is set in the cfg file
		if (aw_global_get("print") && aw_ini_get("document.print_tpl"))
		{
			return aw_ini_get("document.print_tpl");
		}
		if (isset($arr["tpl"]))
		{
			return $arr["tpl"];
		}

		// do template autodetect from parent
		$tplmgr = get_instance("templatemgr");
		if ($leadonly > -1)
		{
			$tpl = $tplmgr->get_lead_template($doc["parent"]);
		}
		else
		{
			$tpl = $tplmgr->get_long_template($doc["parent"]);
		}
		if ($tpl == "")
		{
			return $arr["leadonly"] ? "lead.tpl" : "plain.tpl";
		}
		return $tpl;
	}

	/** Returns the text for the document, based on the settings.
		@attrib api=1 params=pos

		@param arr required type=array
			array { no_strip_lead => [bool], leadonly => [-1 / > -1 ], showlead => [bool] }

		@param doc required type=cl_document
			The document to get text for

		@returns
			Text content for the document, as per the settings

	**/
	function get_document_text($arr, $doc)
	{
		$lead = $doc->trans_get_val("lead");
		if (empty($arr["no_strip_lead"]))
		{
			$lead = preg_replace("/#pict(\d+?)(v|k|p|)#/i","",$lead);
			$lead = preg_replace("/#p(\d+?)(v|k|p|)#/i","",$lead);
		}
		$content = $doc->trans_get_val("content");
		$sps = $doc->meta("setps");
		if (!empty($sps["lead"]))
		{
			$lead = $sps["lead"];
		}
		if (!empty($sps["content"]))
		{
			$content = $sps["content"];
		}
		$content = str_replace("<!--[", "<!-- [", $content);
		$content = str_replace("]-->","] -->", $content);
		if (isset($arr["leadonly"]) and $arr["leadonly"] > -1)
		{
			$text = $lead;
		}
		else
		{
			if ($doc->prop("showlead") || $arr["showlead"])
			{
				if (trim(strtolower($lead)) === "<br>")
				{
					$lead = "";
				}

				if ($lead != "")
				{
					if (aw_ini_get("document.boldlead"))
					{
						$lead = html::bold($lead);
					}
					$text = $lead.aw_ini_get("document.lead_splitter").$content;
				}
				else
				{
					$text = $content;
				}
			}
			else
			{
				$text = $content;
			}
		}

		if (aw_ini_get("document.use_wiki_parser") == 1)
		{
			$this->parse_wiki($text, $doc);
		}
		$this->_parse_youtube_links($text);

		// line break conversion between wysiwyg and not
		$cb_nb = $doc->meta("cb_nobreaks");
		if (!($doc->prop("nobreaks") || $cb_nb["content"]))
		{
			$text = str_replace("\r\n","<br />",$text);
			$text = str_replace("</li><br />", "</li>", $text);
			$text = str_replace("<br /><ul><br />", "<ul>", $text);
			$text = str_replace("</ul><br />", "</ul>", $text);
		}

		if (strpos($text, "#login#") !== false)
		{
			if (!aw_global_get("uid"))
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
				$li = new aw_template();
				$li->init();
				lc_site_load("login", $li);
				$li->read_template("login.tpl");
				$text = str_replace("#login#", $li->parse(), $text);
			}
			else
			{
				$text = str_replace("#login#", "", $text);
			}
		}

		// if show in iframe is set, just return the iframe
		if ($doc->prop("show_in_iframe") && !$_REQUEST["only_document_content"])
		{
			$this->vars(array(
				"iframe_url" => obj_link($doc->id())."?only_document_content=1"
			));
			return $this->parse("IFRAME");
		}

		return $text;
	}

	private function _get_lead($arr, $doc)
	{
		$lead = $doc->trans_get_val("lead");
		if (!$arr["no_strip_lead"])
		{
			$lead = preg_replace("/#pict(\d+?)(v|k|p|)#/i","",$lead);
			$lead = preg_replace("/#p(\d+?)(v|k|p|)#/i","",$lead);
		}

		if (!empty($sps["lead"]))
		{
			$lead = $sps["lead"];
		}

		if (trim(strtolower($lead)) === "<br>")
		{
			$lead = "";
		}

		$text = $lead; //$doc->trans_get_val("lead");

		if (aw_ini_get("document.use_wiki_parser") == 1)
		{
			$this->parse_wiki($text, $doc);
		}
		$this->_parse_youtube_links($text);

		// line break conversion between wysiwyg and not
		$cb_nb = $doc->meta("cb_nobreaks");
		if (!($doc->prop("nobreaks") || $cb_nb["content"]))
		{
			$text = str_replace("\r\n","<br />",$text);
			$text = str_replace("</li><br />", "</li>", $text);
			$text = str_replace("<br /><ul><br />", "<ul>", $text);
			$text = str_replace("</ul><br />", "</ul>", $text);
		}

		return $text;
	}

	private function _get_content($arr, $doc)
	{
		$content = $doc->trans_get_val("content");
		$content = str_replace("<!--[", "<!-- [", $content);
		$content = str_replace("]-->","] -->", $content);

		$text = $content; //$doc->trans_get_val("content");

		if (aw_ini_get("document.use_wiki_parser") == 1)
		{
			$this->parse_wiki($text, $doc);
		}
		$this->_parse_youtube_links($text);

		// line break conversion between wysiwyg and not
		$cb_nb = $doc->meta("cb_nobreaks");
		if (!($doc->prop("nobreaks") || $cb_nb["content"]))
		{
			if (aw_ini_get("content.doctype") == "xhtml")
			{
				$text = str_replace("\r\n","<br />",$text);
			}
			else
			{
				$text = str_replace("\r\n","<br>",$text);
			}
				$text = str_replace("</li><br />", "</li>", $text);
				$text = str_replace("<br /><ul><br />", "<ul>", $text);
				$text = str_replace("</ul><br />", "</ul>", $text);
		}

		return $text;
	}

	/** Parses the given text as a wiki entry and formats it for display
		@attrib api=1 params=pos

		@param str required type=string
			The string to parse and format. Passed by reference

		@param doc required type=cl_document
			The object that the text belongs to

		@returns
			none, the string is parsed and formatted in-place
	**/
	function parse_wiki(&$str, $doc)
	{
		$str = trim($str);
		$this->_parse_wiki_links($str);
		$this->_parse_wiki_lists($str);
		$this->_parse_wiki_titles($str);
		$this->_parse_wiki_create_paragraphs($str);
		// ''italic''
		// '''bold'''
		// '''''italic and bold''''''
		$this->_parse_wiki_fontstyle($str);
		// ~~~ = username
		// ~~~~ = username date/time (UTC)
		// ~~~~~ = date/time alone (UTC)
		$this->_parse_wiki_sign($str, $doc);
		$this->_parse_wiki_table($str);
	}

	private function _parse_wiki_table(&$str)
	{
		if ( preg_match_all("/{\|.*\|}/imsU", $str, $mt_wiki_tables ) !== 0)
		{
			$tmp = $str;
			$a_text = array();

			// read lines to array
			while ( strlen(trim( $tmp)) > 0 )
			{
				if (preg_match( "/^(.*)\r\n/U" , $tmp, $mt))
				{
					$tmp = preg_replace( "/^(.*)\r\n/U"  , "" , $tmp );
				}
				else if (preg_match( "/^(.*)$/U" , $tmp, $mt))
				{
					$tmp = preg_replace( "/^(.*)$/U"  , "" , $tmp );
				}
				$a_text[] = array ("text" => $mt[1]);
			}

			$a_tables = array();
			foreach($mt_wiki_tables[0] as $key => $var)
			{
				$a_tables[] = $this->_parse_wiki_table_get_to_array( $var );
			}

			$ti = get_instance("vcl/table");
			$ti = new aw_table();
			$ti->set_layout("generic");
			$i_tableid=0;
			foreach($a_tables as $key => $var)
			{
				$s_tables[] = $this->_parse_wiki_table_parse_array_to_html( $a_tables[$key], $i_tableid++ );
			}

			for ($i=0;$i<count($s_tables);$i++)
			{
				$str = preg_replace  ("/{\|.*\|}/imsU",  $s_tables[$i], $str, 1);
			}

			//arr($a_tables, true);
			//arr($mt_wiki_tables, true);
		}
	}

	private function _parse_wiki_table_parse_array_to_html($a_table, $i_tableid)
	{
		$ti = get_instance("vcl/table");
		$ti = new aw_table();
		$ti->set_layout("generic");

		// header
		$i_colnr =0;
		foreach ($a_table["data"]["header"] as $key => $val)
		{
			if (strpos  ($val, "|") > 0)
			{
				$s_name = end(explode("|", $val));
			}
			else
			{
				$s_name = $val;
			}
			$ti->define_field(array(
				"name" => $i_tableid."_col_".$i_colnr++,
				"caption" => $s_name,
				"sortable" => 1,
				"align" => "left"
			));
		}

		// rows
		foreach ($a_table["data"]["rows"] as $key => $val)
		{
			$i_colnr =0;
			$a_define_data = array();
			foreach ($a_table["data"]["rows"][$key] as $key2 => $val2)
			{
				if (strpos  ($val2, "|") > 0)
				{
					$s_data = end(explode("|", $val2));
				}
				else
				{
					$s_data = $val2;
				}

				$a_define_data[$i_tableid."_col_".$i_colnr] = $s_data;
				$i_colnr++;
			}
			$ti->define_data($a_define_data);
		}

		return $ti->draw();
	}

	private function _parse_wiki_table_get_to_array($s_table)
	{
		$a_out = array();

		// header
		{
			// table atribs
			preg_match("/{\|\s(.*)\r\n/U", $s_table, $mt );
			$a_out["table_atributes"] = $mt[1];

			// table header
			preg_match("/.*\|\-(.*)\|\-/imsU", $s_table, $mt);
			// if cell contains style elements then deal with it in
			// _parse_wiki_table_parse_array_to_html($a_table)
			preg_match_all("/!\s(.*)\r\n/imsU", $mt[1], $a_table_header);
			$a_out["data"]["header"] = $a_table_header[1];
		}
		// rows
		{

			preg_match("/.*\|\-.*\|-(.*)\|}/imsU", $s_table, $mt);
			$rows = explode ("|-", $mt[1] );
			foreach($rows as $key => $var)
			{
				$cols = explode("| ", $var);
				unset ($cols[0]);
				$a_out["data"]["rows"][] = $cols;
			}
		}

		return $a_out;
	}

	private function _parse_wiki_links(&$str)
	{
		$a_pattern = array();
		$a_replacement = array();

		$a_pattern[] = "/\[([http|https].*)\s{1}(.*)\]/U";
		$a_replacement[] = html::href(array(
			"url" => "\\1",
			"rel" => "nofollow",
			"caption" => "\\2",
		));
		$a_pattern[] = "/\[([http|https].*)\]/U";
		$a_replacement[] = html::href(array(
			"url" => "\\1",
			"rel" => "nofollow",
			"caption" => "[<?php if (!isset(\$i_tmp_link_counter)){\$i_tmp_link_counter = 1;} echo \$i_tmp_link_counter++; ?>]",
		));
		$str = preg_replace  ( $a_pattern  , $a_replacement  , $str );
	}

	private function _parse_wiki_sign(&$str, $doc)
	{
		if (strpos($str, "~~~")!== false )
		{
			$ts = $doc->created();
			$i_month = (int)gmdate( "n",  $ts);
			$s_month = ucfirst(aw_locale::get_lc_month($i_month));
			$s_created  = gmdate( "H:i, j ",  $ts).$s_month." ". gmdate( "Y (\U\TC)",  $ts);

			$a_pattern = array();
			$a_replacement = array();
			$a_pattern[] = "/~~~~~/";
			$a_replacement[] = $s_created;
			$a_pattern[] = "/~~~~/";
			$a_replacement[] = aw_global_get("uid")." ".$s_created;
			$a_pattern[] = "/~~~/";
			$a_replacement[] = aw_global_get("uid");
			$str = preg_replace  ( $a_pattern  , $a_replacement  , $str );
		}
	}

	private function _parse_wiki_fontstyle(&$str)
	{
		if (strpos($str, "''")!== false)
		{
			$a_pattern = array();
			$a_replacement = array();
			$a_pattern[] = "/'''''(.*)'''''/U";
			$a_replacement[] = "<b><i>\\1</i></b>";
			$a_pattern[] = "/''''(.*)''''/U";
			$a_replacement[] = "<b>'\\1'</b>"; // don't do anything special... just 1 pair ' is over
			$a_pattern[] = "/'''(.*)'''/U";
			$a_replacement[] = "<b>\\1</b>";
			$a_pattern[] = "/''(.*)''/U";
			$a_replacement[] = "<i>\\1</i>";
			$str = preg_replace  ( $a_pattern  , $a_replacement  , $str );
		}
	}

	// because 2 br's dont't make paragraph
	private function _parse_wiki_create_paragraphs(&$str)
	{
		if (strlen(trim($str))>0)
		{
			if (strpos  ($str, "\r\n\r\n" )!== false)
			{
				$str = "<p>$str</p>";
				$str = str_replace  ( "\r\n\r\n", "</p><p>", $str );
			}
		}
	}

	private function _parse_wiki_titles(&$str)
	{
		$a_str = explode("\r\n", $str);
		$i_a_str_count = count($a_str);
		if (strpos($str, "=") !== false)
		{
			for ($i=0;$i<$i_a_str_count;$i++)
			{
				$a_pattern[1] = "/^======(.+)======$/";
				$a_replacement[1] = "<h6>\\1</h6>";
				$a_pattern[2] = "/^=====(.+)=====$/";
				$a_replacement[2] = "<h5>\\1</h5>";
				$a_pattern[3] = "/^====(.+)====$/";
				$a_replacement[3] = "<h4>\\1</h4>";
				$a_pattern[4] = "/^===(.+)===$/";
				$a_replacement[4] = "<h3>\\1</h3>";
				$a_pattern[5] = "/^==(.+)==$/";
				$a_replacement[5] = "<h2>\\1</h2>";
				$a_pattern[6] = "/^=(.+)=$/";
				$a_replacement[6] = "<h1>\\1</h1>";

				$a_str[$i] = preg_replace  ( $a_pattern  , $a_replacement  , $a_str[$i] );
			}
		}
		$str = implode  ( "\r\n", $a_str );
	}

	private function _parse_wiki_lists(&$str)
	{
		$tmp = $str;
		$a_text = array();

		// read lines to array
		$i=0;
		while ( strlen(trim( $tmp)) > 0 )
		{
			if (preg_match  ( "/^(.*)\r\n/U" , $tmp, $mt))
			{
				$tmp = preg_replace  ( "/^(.*)\r\n/U"  , "" , $tmp );
			}
			else if (preg_match  ( "/^(.*)$/U" , $tmp, $mt))
			{
				$tmp = preg_replace  ( "/^(.*)$/U"  , "" , $tmp );
			}
			else if (preg_match  ( "/^(.*)\n/U" , $tmp, $mt))
			{
				$tmp = preg_replace  ( "/^(.*)\n/U"  , "" , $tmp );
			}
			$a_text[] = array ("text" => $mt[1]);
			$i++;
		}

		// get more info to $a_text:
		// recursion level
		// list type (ordered, unordered)
		$tmp = "";
		$b_doc_containts_list = false;
		$i_text_count = count($a_text);
		$i_current_unordered_list_max_recursion=0;
		$i_current_ordered_list_max_recursion=0;
		for ($i=0;$i<$i_text_count;$i++)
		{
			$a_tmp = $this->_parse_wiki_lists_get_info_about_listitem_at_line($a_text[$i]["text"]);
			$a_text[$i]["listlevel"] = $a_tmp["listlevel"] == "same_as_last" ? $a_text[$i-1]["listlevel"] : $a_tmp["listlevel"];
			$a_text[$i]["listype"] = $a_tmp["listype"];
		}

		// and finally output the list
		for ($i=0;$i<$i_text_count;$i++)
		{
			if ($a_text[$i]["listype"] == "unordered")
			{
				$s_list_prefix = "<ul>";
				$s_list_sufix = "</ul>";
				$s_list_item = "<li>";
			}
			else if ($a_text[$i]["listype"] == "ordered")
			{
				$s_list_prefix = "<ol>";
				$s_list_sufix = "</ol>";
				$s_list_item = "<li>";
			}
			else if ($a_text[$i]["listype"] == "definitionlist")
			{
				$s_list_prefix = "<dl>";
				$s_list_sufix = "</dl>";
				$s_list_item = "<dd>";
			}

			if ($a_text[$i-1]["listlevel"]==1 && $a_text[$i]["listlevel"]==0)
			{
				$tmp .= $s_list_sufix;
				$tmp .= $a_text[$i]["text"]."\r\n";
			}
			else if ($a_text[$i]["listlevel"]==0 && $a_text[$i+1]["listlevel"]==0)
			{
				$tmp .= $a_text[$i]["text"]."\r\n";
			}
			else if ($a_text[$i]["listlevel"]==0 && $a_text[$i+1]["listlevel"]==1)
			{
				$tmp .= $a_text[$i]["text"]."\r\n";
				$tmp .= $s_list_prefix;
			}
			else
			{
				// start list
				if ($a_text[$i-1]["listlevel"]<$a_text[$i]["listlevel"])
				{
					$tmp .= $s_list_prefix;
				}

				// listitems
				if (strpos  ( $a_text[$i]["text"], "*:" ) === false || strpos  ( $a_text[$i]["text"], "#:" ) === false )
				{
					preg_match("/^.*\s(.*)$/imU", $a_text[$i]["text"], $mt);
					$a_text[$i]["text"] = $mt[1];
					$tmp .= $s_list_item.$a_text[$i]["text"];
				}
				else
				{
					$tmp .= "<br>".$a_text[$i]["text"];
				}

				// end list
				if ($a_text[$i]["listlevel"]>$a_text[$i+1]["listlevel"])
				{
					$i_level_offset = $a_text[$i]["listlevel"]-$a_text[$i+1]["listlevel"];
					for ($j=0;$j<$i_level_offset;$j++)
					{
						$tmp .= $s_list_sufix;
					}
				}
			}
		}

		// for debug
		if (count($a_text)>4)
		{
		//arr($a_text,1);
		//arr($tmp,1);
		}
		$str = $tmp;
	}

	private function _parse_wiki_lists_get_info_about_listitem_at_line($s_line)
	{
		if ( preg_match  ( "/^(\*.*)\s/U" , $s_line, $mt) )
		{
			if (strpos  ( $s_line, "*:" ) !== false )
			{
				return array (
						"listlevel" => "same_as_last",
						"listype" => "unordered",
				);
			}

			return array (
				"listlevel" => strlen($mt[1]),
				"listype" => "unordered",
			);
		}

		if ( preg_match  ( "/^(\#.*)\s/U" , $s_line, $mt) )
		{
			if (strpos  ( $s_line, "#:" ) !== false )
			{
				return array (
						"listlevel" => "same_as_last",
						"listype" => "ordered",
				);
			}

			return array (
				"listlevel" => strlen($mt[1]),
				"listype" => "ordered",
			);
		}

		if ( preg_match  ( "/^(:*)\s/U" , $s_line, $mt) )
		{
			return array (
				"listlevel" => strlen($mt[1]),
				"listype" => "definitionlist",
			);

		}

		return array (
			"listlevel" => "0",
			"listype" => "",
		);
	}

	private function _parse_youtube_links(&$str)
	{
		$tmp_template_explode = explode("/", $this->template_filename);
		$tmp_template = end($tmp_template_explode);
		if ( $this->is_template("youtube_link") && (strpos($str, "http://www.youtube.com/")!==false || strpos($str, "http://youtube.com/")!==false))
		{
			$str = str_replace  ( array("http://www.youtube.com/watch?v=", "http://youtube.com/watch?v=")  , array("http://www.youtube.com/v/", "http://www.youtube.com/v/"), $str );
			$this->vars(array(
				"video_id" => "\${1}\${2}\${3}\${4}",
			));
			$s_embed = $this->parse("youtube_link");
			$str = preg_replace  ("/http:\/\/www.youtube.com\/v\/([A-Za-z0-9_-]*)|http:\/\/www.youtube.com\/v\/([A-Za-z0-9_-]*)/ims", $s_embed, $str);
		}
	}

	private function _do_forum($doc)
	{
		$fr = "";
		if ($doc->prop("is_forum") &&
			($this->is_template("FORUM_ADD_SUB") || $this->is_template("FORUM_ADD_SUB_ALWAYS") || $this->is_template("FORUM_ADD"))

	     	)
		{
			$_sect = aw_global_get("section");
			// calculate the amount of comments this document has
			// XXX: I could use a way to figure out which variables are present in the template
			$num_comments = $this->db_fetch_field("
				SELECT
					count(*) AS cnt
				FROM
					comments
				WHERE
					board_id = '".$doc->id()."'
				AND
					lang_id = ".((int)aw_global_get("ct_lang_id")),
				"cnt");
			$this->vars(array(
				"num_comments" => sprintf("%d",$num_comments),
				"comm_link" => str_replace("&", "&amp;", $this->mk_my_orb("show_threaded",array("board" => $doc->id(),"section" => $_sect),"forum")),
			));
			$forum = get_instance(CL_FORUM);
			$fr = $forum->add_comment(array("board" => $doc->id(),"section" => $_sect));

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
		}
		$this->vars(array(
			'FORUM_ADD' => $fr
		));

		if ($doc->prop("is_forum") && $this->is_template("FORUM_POST"))
		{
			if ($num_comments>0)
			{
				$this->db_query("
					SELECT
						id,
						name,
						url,
						time,
						comment
					FROM
						comments
					WHERE
						board_id = ".$doc->id() ."
					AND
						lang_id = ".aw_global_get("ct_lang_id")."
					ORDER BY time ASC");

				$i=1;
				while($row = $this->db_next())
				{
					$s_comment = $row["comment"];
					//$this->_parse_youtube_links(& $s_comment);
					$this->parse_wiki($s_comment, $doc);
					$s_name = $row["name"];
					$s_url = $row["url"];
					if (strlen($s_url)>0)
					{
						$s_name = html::href(array(
							"caption" => $s_name,
							"url" => $s_url,
							"rel" => "nofollow",
						));
					}
					$this->dequote($s_comment);
					$s_comment = nl2br ($s_comment);
					$this->vars(array(
						"id" => $row["id"],
						"name" => $s_name,
						"post_created_hr" => $this->get_date_human_readable( $row["time"]),
						"comment" => $s_comment,
						"count" => $i++,
					));
					$tmp .= $this->parse("FORUM_POST");

					$this->vars(array(
						"FORUM_POST" => $tmp,
					));
					$this->parse();
				}
			}
		}
	}

	private function _do_charset($doc)
	{
		if ($this->template_has_var("charset"))
		{
			$_langs = get_instance("languages");
			$_ld = $_langs->fetch(aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id"));
			$this->vars(array(
				"charset" => $_ld["charset"]
			));
		};
	}

	private function _do_checkboxes($doc)
	{
		if ($doc->prop("ucheck1") == 1)
		{
			$this->vars(array(
				"UCHECK1_CHECKED" => $this->parse("UCHECK1_CHECKED"),
				"UCHECK1_UNCHECKED" => ""
			));
		}
		else
		{
			$this->vars(array(
				"UCHECK1_CHECKED" => "",
				"UCHECK1_UNCHECKED" => $this->parse("UCHECK1_UNCHECKED")
			));
		}
	}

	private function _get_edit_menu($menu)
	{
		if (!acl_base::prog_acl() || !empty($_SESSION["no_display_site_editing"]))
		{
			return;
		}
		$pm = get_instance("vcl/popup_menu");
		$pm->begin_menu("site_edit_".$menu->id());
		$url = $this->mk_my_orb("new", array("parent" => $menu->parent(), "ord_after" => $menu->id(), "return_url" => get_ru(), "is_sa" => 1), CL_DOCUMENT, true);
		$pm->add_item(array(
			"text" => t("Lisa uus"),
			"onclick" => "aw_popup_scroll(\"{$url}\", \"aw_doc_edit\",800, 600);",
			"link" => "javascript:void(0)"
		));
		$url = $this->mk_my_orb("change", array("id" => $menu->id(), "return_url" => get_ru(), "is_sa" => 1), CL_DOCUMENT, true);
		$pm->add_item(array(
			"text" => t("Muuda"),
			"onclick" => "aw_popup_scroll(\"{$url}\", \"aw_doc_edit\",800, 600);",
			"link" => "javascript:void(0)"
		));
		$pm->add_item(array(
			"text" => t("Peida"),
			"link" => $this->mk_my_orb("hide_doc", array("id" => $menu->id(), "ru" => get_ru()), "menu_site_admin")
		));
		$pm->add_item(array(
			"text" => t("L&otilde;ika"),
			"link" => $this->mk_my_orb("cut_doc", array("id" => $menu->id(), "ru" => get_ru()), "menu_site_admin")
		));
		if (isset($_SESSION["site_admin"]["cut_doc"]) && $this->can("view", $_SESSION["site_admin"]["cut_doc"]))
		{
			$pm->add_item(array(
				"text" => t("Kleebi"),
				"link" => $this->mk_my_orb("paste_doc", array("after" => $menu->id(), "ru" => get_ru()), "menu_site_admin")
			));
		}
		return $pm->get_menu();
	}

	/** Converts aw-style relative links (#1# ... #s1#, ... } to real relative hrefs
		@attrib api=1 params=pos

		@param text required type=string
			Passed by reference, The text to replace

		@returns
			none, text is replaced in-place.
	**/
	function create_relative_links(&$text)
	{
		while (preg_match("/(#)(\d+?)(#)(.*)(#)(\d+?)(#)/imsU",$text,$matches))
		{
			$text = str_replace($matches[0],"<a href='#" . $matches[2] . "'>$matches[4]</a>",$text);
		}
		while(preg_match("/(#)(s)(\d+?)(#)/",$text,$matches))
		{
			$text = str_replace($matches[0],"<a name='" . $matches[3] . "'> </a>",$text);
		}
	}

	private function _do_user_subs($doc)
	{
		$u1s = "";
		if ($doc->prop("user1") != "")
		{
			$u1s = $this->parse("user1_sub");
		}
		$u2s = "";
		if ($doc->prop("user2") != "")
		{
			$u2s = $this->parse("user2_sub");
		}
		$u3s = "";
		if ($doc->prop("user3") != "")
		{
			$u3s = $this->parse("user3_sub");
		}
		$u4s = "";
		if ($doc->prop("user4") != "")
		{
			$u4s = $this->parse("user4_sub");
		}
		$u5s = "";
		if ($doc->prop("user5") != "")
		{
			$u5s = $this->parse("user5_sub");
		}
		$u6s = "";
		if ($doc->prop("user6") != "")
		{
			$u6s = $this->parse("user6_sub");
		}
		$u7s = "";
		if ($doc->prop("user7") != "")
		{
			$u7s = $this->parse("user7_sub");
		}
		$u8s = "";
		if ($doc->prop("user8") != "")
		{
			$u8s = $this->parse("user8_sub");
		}
		$u9s = "";
		if ($doc->prop("user9") != "")
		{
			$u9s = $this->parse("user9_sub");
		}
		$u10s = "";
		if ($doc->prop("user10") != "")
		{
			$u6s = $this->parse("user10_sub");
		}
		$u11s = "";
		if ($doc->prop("user11") != "")
		{
			$u11s = $this->parse("user11_sub");
		}
		$u12s = "";
		if ($doc->prop("user12") != "")
		{
			$u6s = $this->parse("user12_sub");
		}
		$u13s = "";
		if ($doc->prop("user13") != "")
		{
			$u6s = $this->parse("user13_sub");
		}
		$u14s = "";
		if ($doc->prop("user14") != "")
		{
			$u6s = $this->parse("user14_sub");
		}
		$u15s = "";
		if ($doc->prop("user15") != "")
		{
			$u6s = $this->parse("user15_sub");
		}
		$u16s = "";
		if ($doc->prop("user16") != "")
		{
			$u6s = $this->parse("user16_sub");
		}
		$ut2 = "";
		if ($doc->prop("userta2") != "")
		{
			$ut2 = $this->parse("userta2_sub");
		}
		$ut3 = "";
		if ($doc->prop("userta3") != "")
		{
			$ut3 = $this->parse("userta3_sub");
		}
		$ut4 = "";
		if ($doc->prop("userta4") != "")
		{
			$ut4 = $this->parse("userta4_sub");
		}
		$ut5 = "";
		if ($doc->prop("userta5") != "")
		{
			$ut5 = $this->parse("userta5_sub");
		}
		$ut6 = "";
		if ($doc->prop("userta6") != "")
		{
			$ut6 = $this->parse("userta6_sub");
		}
		$this->vars(array(
			"user1_sub" => $u1s,
			"user2_sub" => $u2s,
			"user3_sub" => $u3s,
			"user4_sub" => $u4s,
			"user5_sub" => $u5s,
			"user6_sub" => $u6s,
			"user7_sub" => $u7s,
			"user8_sub" => $u8s,
			"user9_sub" => $u9s,
			"user10_sub" => $u10s,
			"user11_sub" => $u11s,
			"user12_sub" => $u12s,
			"user13_sub" => $u13s,
			"user14_sub" => $u14s,
			"user15_sub" => $u15s,
			"user16_sub" => $u16s,
			"userta2_sub" => $ut2,
			"userta3_sub" => $ut3,
			"userta4_sub" => $ut4,
			"userta5_sub" => $ut5,
			"userta6_sub" => $ut6
		));
	}

	/** Returns a correct link for the given document
		@attrib api=1 params=pos

		@param doc required type=cl_document
			The document to create link for

		@param lc optional type=string
			The language to create the link for. Defaults to the current language

		@returns
			A link that displays the current document in the website
	**/
	public static function get_doc_link($doc, $lc = null)
	{
		$doc_link = obj_link($doc->id());
		if (aw_ini_get("document.links_to_same_section"))
		{
			$doc_link = aw_url_change_var("docid", $doc->id(), obj_link(aw_global_get("section")));
		}
		if ($doc->prop("alias") != "" || aw_ini_get("menuedit.language_in_url"))
		{
			static $ss_i;
			if (!$ss_i)
			{
				$ss_i = get_instance("contentmgmt/site_show");
			}
			$doc_link = $ss_i->make_menu_link($doc, $lc);
		}

		if (!empty($_GET["path"]))
		{
			$new_path = array();
			$path_ids = explode(",", $_GET["path"]);
			foreach($path_ids as $_path_id)
			{
				$new_path[] = $_path_id;
				$pio = obj($_path_id);
				if ($pio->brother_of() == $doc->parent())
				{
					break;
				}
			}
			$doc_link = aw_ini_get("baseurl")."/?section=".$doc->id()."&path=".join(",",$new_path).",".$doc->id();
		}

		return $doc_link;
	}

	// todo 2 viimast if'i
	public function get_date_human_readable($i_timestamp_created)
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
				return sprintf(t("%s minut tagasi"), $i_minutes);
			}
			else
			{
				return sprintf(t("%s minutit tagasi"), $i_minutes);
			}
		}
		else if ($i_time_from_created_to_current_time < 60*60*24)
		{
			$i_hours = floor($i_time_from_created_to_current_time / 60 / 60);
			if ($i_hours == 1)
			{
				return sprintf(t("%s tund tagasi"), $i_hours);
			}
			else
			{
				return sprintf(t("%s tundi tagasi"), $i_hours);
			}
		}
		else if ($i_time_from_created_to_current_time < 60*60*24*31)
		{
			$i_days = floor($i_time_from_created_to_current_time / 60 / 60 / 24);
			if ($i_days == 1)
			{
				return sprintf(t("%s p&auml;ev tagasi"), $i_days);
			}
			else
			{
				return sprintf(t("%s p&auml;eva tagasi"), $i_days);
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

	private function parse_keywords($doc)
	{
		// parse subs KEYWORD_BEGIN, KEYWORD and KEYWORD_END
		if ($doc->prop("keywords") != "")
		{
			// if commas are not used then ake spaces as separators
			if (strpos($doc->prop("keywords"), ",")===false)
			{
				$a_keywords = explode(" ", $doc->prop("keywords"));
			}
			else
			{
				$a_keywords = explode(",", $doc->prop("keywords"));
			}

			$tmp = "";
			for ($i=0;$i<count($a_keywords);$i++)
			{
				if ($i==0 && $this->is_template("KEYWORD_BEGIN"))
				{
					$this->vars(array(
						"text" => $a_keywords[$i],
						"link" => "link",
					));

					$tmp .= trim($this->parse("KEYWORD_BEGIN"));
					continue;
				}

				if ($i<count($a_keywords)-1 && $this->is_template("KEYWORD"))
				{
					$this->vars(array(
						"text" =>  $a_keywords[$i],
						"link" => "link",
					));

					$tmp .= trim($this->parse("KEYWORD"));
					continue;
				}

				if ($i==count($a_keywords)-1 && $this->is_template("KEYWORD_END"))
				{
					$this->vars(array(
						"text" =>  $a_keywords[$i],
						"link" => "link",
					));

					$tmp .= trim($this->parse("KEYWORD_END"));
					continue;
				}

				$this->vars(array(
						"text" =>  $a_keywords[$i],
						"link" => "link",
					));

				$tmp .= trim($this->parse("KEYWORD"));
			}
			// now try to put the keywords to template
			if ($this->is_template("KEYWORD_BEGIN"))
			{
				$this->vars(array(
						"KEYWORD_BEGIN" => $tmp,
				));
			}
			else if ($this->is_template("KEYWORD"))
			{
				$this->vars(array(
						"KEYWORD" => $tmp,
				));
			}


		}

	}
}
