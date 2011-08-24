<?php

// promo.aw - promokastid.

/* content documents for promo boxes are handled thusly:

- when a document is saved, promo boxes are scanned to see if any of them should display the just-saved document
  if so, then the document is added to the list in meta[content_documents], else it is removed

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE,CL_DOCUMENT, on_save_document)


- when a promo box is saved, the list of documents for it's display is regenerated

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE,CL_PROMO, on_save_promo)

- when a document is deleted, the list of documents needs to be regenerated

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE,CL_DOCUMENT, on_delete_document)

*/

/*
	@classinfo trans=1

	@groupinfo general_sub caption="&Uuml;ldine" parent=general

		@property name type=textbox rel=1 trans=1 table=objects group=general_sub
		@caption Nimi
		@comment Objekti nimi

		@property comment type=textbox table=objects group=general_sub
		@caption Kommentaar
		@comment Vabas vormis tekst objekti kohta

		@property status type=status trans=1 default=1 table=objects group=general_sub
		@caption Aktiivne
		@comment Kas objekt on aktiivne

		@property caption type=textbox table=objects field=meta method=serialize trans=1 group=general_sub
		@caption Pealkiri

		@property type type=select table=objects field=meta method=serialize trans=1 group=general_sub
		@caption Kasti t&uuml;&uuml;p

		@property link type=textbox table=menu group=general_sub
		@caption Link

		@property link_caption type=textbox table=objects field=meta method=serialize group=general_sub
		@caption Lingi kirjeldus

	@groupinfo users caption="Kasutajad" parent=general

		@property groups type=select multiple=1 size=15 group=users method=serialize table=objects field=meta
		@caption Grupid, kellele kasti n&auml;idata

@groupinfo show caption=N&auml;itamine

	@groupinfo show_sub caption="N&auml;itamine" parent=show

		@property no_title type=checkbox ch_value=1  group=show_sub method=serialize table=objects field=meta
		@caption Ilma pealkirjata

		@property show_inact type=checkbox ch_value=1 table=objects field=meta method=serialize group=show_sub
		@caption N&auml;ita mitteaktiivseid dokumente tekstina

		@property auto_period type=checkbox ch_value=1  group=show_sub method=serialize table=objects field=meta
		@caption Perioodilise, automaatselt vahetuva sisuga

	@groupinfo container_locations caption="Konteineri n&auml;itamise asukohad" parent=show

		@property sss_tb type=toolbar store=no no_caption=1 group=container_locations

		@property all_menus type=checkbox ch_value=1 group=container_locations method=serialize table=objects field=meta
		@caption N&auml;ita igal pool

		@property not_in_search type=checkbox ch_value=1 table=objects field=meta method=serialize group=container_locations
		@caption &Auml;ra n&auml;ita otsingu tulemuste lehel

		@property not_in_doc_view type=checkbox ch_value=1 table=objects field=meta method=serialize group=container_locations
		@caption &Auml;ra n&auml;ita dokumendi pikas vaates

		@property trans_all_langs type=checkbox ch_value=1 table=objects field=meta method=serialize group=container_locations
		@caption Sisu n&auml;idatakse k&otilde;ikides keeltes

		@property content_all_langs type=checkbox ch_value=1 table=objects field=meta method=serialize group=container_locations
		@caption Sisu n&auml;idatakse k&otilde;ikides keeltes

		@property section type=table group=container_locations method=serialize store=no
		@caption Vali men&uuml;&uuml;d, mille all kasti n&auml;idata

		@property section_noshow type=table group=container_locations method=serialize store=no
		@caption Vali men&uuml;&uuml;d, mille all kasti EI n&auml;idata

	@groupinfo doc_ord caption="Dokumentide j&auml;rjestamine" parent=show

		@property sort_by type=select table=objects field=meta method=serialize group=doc_ord
		@caption Dokumente j&auml;rjestatakse

		@property sort_ord type=select table=objects field=meta method=serialize group=doc_ord

		@property sort_by2 type=select table=objects field=meta method=serialize group=doc_ord
		@caption Dokumente j&auml;rjestatakse (2)

		@property sort_ord2 type=select table=objects field=meta method=serialize group=doc_ord

		@property sort_by3 type=select table=objects field=meta method=serialize group=doc_ord
		@caption Dokumente j&auml;rjestatakse (3)

		@property sort_ord3 type=select table=objects field=meta method=serialize group=doc_ord

@groupinfo look caption="V&auml;limus"

	@groupinfo look_sub caption="V&auml;limus" parent=look

		@property image type=relpicker reltype=RELTYPE_IMAGE table=objects field=meta method=serialize group=look_sub
		@caption Pilt

	@groupinfo templates caption="Kujundusp&otilde;hjad" parent=look

		@property tpl_lead type=select table=menu group=templates
		@caption Template n&auml;itamiseks

		@property tpl_lead_last type=select table=objects field=meta method=serialize group=templates
		@caption Template viimaste dokumentide n&auml;itamiseks

		@property tpl_lead_last_count type=textbox size=5 table=objects field=meta method=serialize group=templates
		@caption Viimane template alates dokumendist

		@property use_fld_tpl type=checkbox ch_value=1 group=templates method=serialize table=objects field=meta
		@caption Kasuta dokumendi asukoha templatet

		@property promo_tpl type=select table=objects field=meta method=serialize group=templates
		@caption Template (dokumendi sees)

@groupinfo menus caption="Sisu seaded"

	@groupinfo menus_sub parent=menus caption="Dokumendid"

		@property lm_tb type=toolbar store=no no_caption=1 group=menus_sub

		@property docs_from_current_menu type=checkbox ch_value=1 group=menus_sub
		@caption Dokumendid aktiivse men&uuml;&uuml; alt

		@property last_menus type=table group=menus_sub method=serialize store=no
		@caption Vali men&uuml;&uuml;d, mille alt viimaseid dokumente v&otilde;etakse

		@property ndocs type=textbox size=4 group=menus_sub table=menu field=ndocs
		@caption Mitu viimast dokumenti

		@property start_ndocs type=textbox size=4 group=menus_sub table=objects field=meta method=serialize
		@caption Mitu algusest &auml;ra j&auml;tta

		@property is_dyn type=checkbox ch_value=1 table=objects field=meta method=serialize group=menus_sub
		@caption Sisu ei cacheta

		@property separate_pages type=checkbox ch_value=1 table=objects field=meta method=serialize group=menus_sub
		@caption Jaota lehtedeks

		@property docs_per_page type=textbox size=5 table=objects field=meta method=serialize group=menus_sub
		@caption Dokumente lehel

	@groupinfo kws parent=menus caption="M&auml;rks&otilde;nad"

		@property kw_tb type=toolbar store=no group=kws no_caption=1

		@property kws type=keyword_selector store=no group=kws reltype=RELTYPE_KEYWORD
		@caption M&auml;rks&otilde;nad

		@property use_menu_keywords type=checkbox ch_value=1 field=meta method=serialize group=kws table=objects
		@caption Kasuta aktiivse kausta m&auml;rks&otilde;nu

		@property use_doc_content_type type=checkbox ch_value=1 field=meta method=serialize group=kws table=objects
		@caption Kasuta dokumendi sisu t&uuml;&uuml;bi m&auml;&auml;rangut



@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


	@classinfo relationmgr=yes
	@classinfo syslog_type=ST_PROMO

	@tableinfo menu index=id master_table=objects master_index=oid


	@reltype ASSIGNED_MENU value=1 clid=CL_MENU
	@caption n&auml;ita men&uuml;&uuml; juures

	@reltype DOC_SOURCE value=2 clid=CL_MENU
	@caption v&otilde;ta dokumente selle men&uuml;&uuml; alt

	@reltype IMAGE value=3 clid=CL_IMAGE
	@caption pilt

	@reltype NO_SHOW_MENU value=4 clid=CL_MENU
	@caption &Auml;ra n&auml;ita men&uuml;&uuml; juures

	@reltype KEYWORD value=5 clid=CL_KEYWORD
	@caption V&otilde;tmes&otilde;na

	@reltype DOC_IGNORE value=6 clid=CL_MENU
	@caption ignoreeri dokumente selle men&uuml;&uuml; alt
*/
class promo extends class_base implements main_subtemplate_handler
{
	function promo()
	{
		$this->init(array(
			"clid" => CL_PROMO,
			"tpldir" => "promo",
		));
		lc_load("definition");
		$this->lc_load("promo","lc_promo");
		$this->trans_props = array(
			"name","comment","caption","link","link_caption"
		);
	}


	function get_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "sss_tb":
				$this->_get_sss_tb($arr);
				break;

			case "lm_tb":
				$this->_get_lm_tb($arr);
				break;

			case "kw_tb":
				$this->kw_tb($arr);
				break;

			case "promo_tpl":
				$tm = get_instance("templatemgr");
				$prop["options"] = $tm->template_picker(array(
					"folder" => "promo/doctemplates"
				));
				break;

			case "tpl_lead":
			case "tpl_lead_last":
				// kysime infot lyhikeste templatede kohta
				$tplmgr = get_instance("templatemgr");
				$prop["options"] = $tplmgr->get_template_list(array(
					"type" => 1,
					"def" => aw_ini_get("promo.default_tpl"),
					"caption" => t("Vali template")
				));
				break;

			case "type":
				$pa = aw_ini_get("promo.areas");
				if (is_array($pa) && count($pa) > 0)
				{
					$opts = array();
					foreach($pa as $pid => $pd)
					{
						$opts[$pid] = $pd["name"];
					}
				}
				else
				{
					$opts = array(
						"0" => t("Vasakul"),
						"1" => t("Paremal"),
						"2" => t("&Uuml;leval"),
						"3" => t("All"),
						"scroll" => t("Skrolliv"),
					);
				}
				$prop["options"] = $opts;
				break;

			case "groups":
				$prop["options"] = get_instance(CL_GROUP)->get_group_picker(array(
					"type" => array(group_obj::TYPE_REGULAR,group_obj::TYPE_DYNAMIC),
				));
				break;

			case "sort_by":
			case "sort_by2":
			case "sort_by3":
				$prop['options'] = array(
					'' => "",
					'objects.jrk' => t("J&auml;rjekorra j&auml;rgi"),
					'objects.created' => t("Loomise kuup&auml;eva j&auml;rgi"),
					'objects.modified' => t("Muutmise kuup&auml;eva j&auml;rgi"),
					'documents.modified' => t("Dokumenti kirjutatud kuup&auml;eva j&auml;rgi"),
					'objects.name' => t("Objekti nime j&auml;rgi"),
					'planner.start' => t("Kalendris valitud aja j&auml;rgi"),
					'RAND()' => t("Random"),
				);
				break;

			case "sort_ord":
			case "sort_ord2":
			case "sort_ord3":
				$prop['options'] = array(
					'DESC' => t("Suurem (uuem) enne"),
					'ASC' => t("V&auml;iksem (vanem) enne"),
				);
				break;

			case "last_menus":
				$this->get_doc_sources($arr);
				break;

			case "section":
				$this->get_menus($arr);
				break;

			case "section_noshow":
				$this->get_menus_noshow($arr);
				break;

			case "content_all_langs":
				return PROP_IGNORE;
				break;

			case "link":
				$this->_get_linker($prop, $arr["obj_inst"]);
				break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "section":
				$arr["obj_inst"]->set_meta("section_include_submenus",$arr["request"]["include_submenus"]);
				break;

			case "section_noshow":
				$arr["obj_inst"]->set_meta("section_no_include_submenus",$arr["request"]["include_no_submenus"]);
				break;

			case "ndocs":
				$arr["obj_inst"]->set_meta("as_name",$arr["request"]["as_name"]);
				$arr["obj_inst"]->set_meta("src_submenus",$this->make_keys($arr["request"]["src_submenus"]));
				break;
		};
		return $retval;

	}

	function get_menus($arr)
	{
		$obj = $arr["obj_inst"];
		$section_include_submenus = $obj->meta("section_include_submenus");

		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
		));
		$t->define_field(array(
			"name" => "check",
			"caption" => t("k.a. alammen&uuml;&uuml;d"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));

		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_ASSIGNED_MENU"
		));

		foreach($conns as $c)
		{
			$c_o = $c->to();

			$t->define_data(array(
				"id" => $c_o->id(),
				"name" => $c_o->path_str(array(
					"max_len" => 3
				)),
				"check" => html::checkbox(array(
					"name" => "include_submenus[".$c_o->id()."]",
					"value" => $c_o->id(),
					"checked" => $section_include_submenus[$c_o->id()],
				)),
			));
		}
	}

	function get_menus_noshow($arr)
	{
		$obj = $arr["obj_inst"];
		$section_no_include_submenus = $obj->meta("section_no_include_submenus");

		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
		));
		$t->define_field(array(
			"name" => "check",
			"caption" => t("k.a. alammen&uuml;&uuml;d"),
			"talign" => "center",
			"width" => 80,
			"align" => "center",
		));

		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_NO_SHOW_MENU"
		));

		foreach($conns as $c)
		{
			$c_o = $c->to();

			$t->define_data(array(
				"id" => $c_o->id(),
				"name" => $c_o->path_str(array(
					"max_len" => 3
				)),
				"check" => html::checkbox(array(
					"name" => "include_no_submenus[".$c_o->id()."]",
					"value" => $c_o->id(),
					"checked" => $section_no_include_submenus[$c_o->id()],
				)),
			));
		}
	}

	function get_doc_sources($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"talign" => "center",
			"align" => "center",
			"nowrap" => "1",
			"width" => "30",
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"talign" => "center",
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "as_name",
			"caption" => t("Pane pealkirjaks"),
			"talign" => "center",
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "src_submenus",
			"caption" => t("k.a. alammen&uuml;&uuml;d"),
			"talign" => "center",
			"align" => "center",
		));

		$obj = $arr["obj_inst"];

		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_DOC_SOURCE"
		));

		$as_name = $obj->meta("as_name");
		$ssm = $obj->meta("src_submenus");

		foreach($conns as $c)
		{
			$c_o = $c->to();
			$c_id = $c_o->id();
			$t->define_data(array(
				"id" => $c_id,
				"name" => $c_o->path_str(array(
					"max_len" => 3
				)),
				"as_name" => html::radiobutton(array(
					"name" => "as_name",
					"value" => $c_id,
						"checked" => ($as_name == $c_id)
				)),
				"src_submenus" => html::checkbox(array(
					"name" => "src_submenus[]",
					"value" => $c_id,
					"checked" => ($ssm[$c_id] == $c_id)
				))
			));
		}
		$t->define_data(array(
			"id" => 0,
			"name" => "",
			"as_name" => html::radiobutton(array(
				"name" => "as_name",
				"value" => 0,
				"checked" => (!$as_name)
			))
		));
	}

	function callback_pre_edit($args = array())
	{
		$id = $args["obj_inst"]->id();
		$obj = $args["obj_inst"];

		// first check, whether the promo box was in the very old format (contained serialized data
		// in the comment field
		$check1 = aw_unserialize($obj->prop("comment"));
		$check2 = aw_unserialize($obj->meta("sss"));
		if (is_array($check1) || is_array($check2))
		{
			$convert_url = $this->mk_my_orb("promo_convert",array(),"converters");
			print "See objekt on vanas formaadis. Enne kui seda muuta saab, tuleb k&otilde;ik s&uuml;steemis olevad promokastid uude formaati konvertida. <a href='$convert_url'>Kliki siia</a> konversiooni alustamiseks";
			exit;
		};

		// now, check, whether we have to convert the current contents of comment and sss to relation objects
		// we use a flag in object metainfo for that
		// converters->convert_promo_relations()

	}

	function callback_on_submit_relation_list($args = array())
	{
		// this is where we put data back into object metainfo, for backwards compatibility
		$obj =& obj($args["id"]);

		$oldaliases = $obj->connections_from(array(
			"class" => CL_MENU
		));

		$section = array();
		$last_menus = array();

		foreach($oldaliases as $alias)
		{
			if ($alias->prop("reltype") == RELTYPE_ASSIGNED_MENU)
			{
				$section[$alias->prop("to")] = $alias->prop("to");
			};

			if ($alias->prop("reltype") == RELTYPE_DOC_SOURCE)
			{
				$last_menus[$alias->prop("to")] = $alias->prop("to");
			};
		};

		// serializes makes empty array into array("0" => "0") and this is bad in this
		// case, so we work around it
		if (sizeof($last_menus) == 0)
		{
			$last_menus = "";
		};

		$obj->set_meta("section",$section);
		$obj->set_meta("last_menus",$last_menus);

		$obj->save();
	}

	function parse_alias($args = array())
	{
		$alias = $args["alias"];
		$ob = obj($alias["target"]);
		if ($ob->prop("ndocs") == -1)
		{
			return "";
		}

		// if there is another promo dok template, then use that
		if ($ob->prop("promo_tpl") != "")
		{
			$this->read_template("doctemplates/".$ob->prop("promo_tpl"));
		}
		else
		{
			$this->read_template("default.tpl");
		}

		$ss = get_instance("contentmgmt/site_show");
		$def = new aw_array($ss->get_default_document(array(
			"obj" => obj($alias["target"])
		)));

		if ($ob->prop("sort_by"))
		{
			$_ob = $ob->prop("sort_by")." ".$ob->prop("sort_ord");
			if ($ob->prop("sort_by") == "documents.modified")
			{
				$_ob .= " ,objects.created DESC";
			};
		}
		if (($_ob != "") && (sizeof($def->get()) > 0))
		{
			$ol = new object_list(array(
				"class_id" => array(CL_DOCUMENT, CL_PERIODIC_SECTION, CL_BROTHER_DOCUMENT),
				"oid" => $def->get(),
				"sort_by" => $_ob,
				"lang_id" => array()
			));


			$def = new aw_array($ol->ids());
		}

		$ndocs = $ob->prop("ndocs");
		if ($ndocs)
		{
			$def = new aw_array(array_slice($def->get(), 0, $ndocs));
		}

		if ($def->count() < 1)
		{
			return "";
		}
		$content = "";
		$doc = get_instance(CL_DOCUMENT);

		$parms = array(
			"leadonly" => 1,
			"showlead" => 1,
			"boldlead" => $this->cfg["boldlead"],
			"no_strip_lead" => 1,
		);

		if (!$ob->meta('use_fld_tpl'))
		{
			$mgr = get_instance("templatemgr");
			$parms["tpl"] = $mgr->get_template_file_by_id(array(
				"id" => $ob->prop("tpl_lead")
			));
		}
		else
		{
			$parms["tpl_auto"] = 1;
		}

		if ($ob->prop("is_dyn"))
		{
			aw_global_set("no_cache", 1);
		}


		$_numdocs = count($def->get());
		$_curdoc = 1;
		foreach($def->get() as $key => $val)
		{
			$_parms = $parms;
			$_parms["docid"] = $val;
			$_parms["not_last_in_list"] = ($_curdoc < $_numdocs);
			$_parms["no_link_if_not_act"] = 1;
			$content .= $doc->gen_preview($_parms);
			$_curdoc ++;
		}

		if ($this->is_template("PREV_LINK"))
		{
			$this->do_prev_next_links($def->get(), $this);
		}

		$as_name = $ob->meta("as_name");

		if ($as_name && $ob->meta("caption") == "")
		{
			if ($this->can("view",$as_name))
			{
				$as_n_o = obj($as_name);
				$ob->set_prop('caption',$as_n_o->name());
			}
		}

		$image = "";
		$image_url = "";
		$image_id = $ob->prop("image");
		if ($image_id)
		{
			$i = get_instance(CL_IMAGE);
			$image_url = $i->get_url_by_id($image_id);
			$image = $i->make_img_tag($image_url);
		}

		$align= array("k" => "align=\"center\"", "p" => "align=\"right\"" , "v" => "align=\"left\"" ,"" => "");
		$this->vars(array(
			"title" => $ob->trans_get_val("caption"),
			"content" => $content,
			"align" => $align[$args["matches"][4]],
			"link" => $this->get_promo_link($ob),
			"link_caption" => $ob->trans_get_val("link_caption"),
			"image" => $image,
			"image_url" => $image_url,
			"image_or_title" => ($image == "" ? $ob->trans_get_val("caption") : $image),
		));

		if (!$ob->meta('no_title'))
		{
			$this->vars(array(
				"SHOW_TITLE" => $this->parse("SHOW_TITLE")
			));
		}
		else
		{
			$this->vars(array(
				"SHOW_TITLE" => ""
			));
		}

		// ADD_ITEM subtemplate should contain the link to add a new document
		// to a container and will be made visible if there is an logged in user
		// this check of course sucks, acl should be used instead --duke
		if (aw_global_get("uid") != "" && $this->is_template("ADD_ITEM"))
		{
			$conns = $ob->connections_from(array(
				"type" => "RELTYPE_DOC_SOURCE",
			));
			if (sizeof($conns) > 0)
			{
				$first = reset($conns);
				$this->vars(array(
					"add_item_url" => $this->mk_my_orb("new",array(
						"parent" => $first->prop("to"),
						"period" => aw_global_get("current_period"),
					),"doc",true),
				));
				$this->vars(array(
					"ADD_ITEM" => $this->parse("ADD_ITEM"),
				));
			};
		}

		return $this->parse();
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

	function on_get_subtemplate_content($args)
	{
		$d = get_instance("contentmgmt/promo_display");
		return $d->on_get_subtemplate_content($args);
	}

	function on_save_document($arr)
	{
		if (aw_ini_get("promo.version") != 2)
		{
			return;
		}
		$o = obj($arr["oid"]);
		// figure out if this document is to be shown in any promo in the system
		// to do that
		// make a list of all promo boxes
		$boxes = new object_list(array(
			"class_id" => CL_PROMO
		));

		$o = $o->get_original();
		$arr["oid"] = $o->brother_of();

		$path = $o->path();
	 	// get brothers as well, causet they might be in boxes as well
		$bros = new object_list(array(
			"brother_of" => $o->brother_of()
		));
		$paths = array();
		foreach($bros->arr() as $bro)
		{
			$paths[$bro->id()] = $bro->path();
		}

		$set_oid = $o->id();
		foreach($boxes->arr() as $box)
		{
			$add_to_list = false;

			// for each box, check the folders where it gets documents and if this document's parent is one of them,
			$fld = $this->_get_folders_for_box($box);
			$is_in_promo = false;
			foreach($fld as $f => $subs)
			{
				if ($f == $o->parent() || ($subs && ($set_oid = $this->_is_in_paths($paths, $f))))
				{
					$is_in_promo = true;
					break;
				}
			}

			// get don't show folders and if the doc is in one of those then padabim-padaboom, you know what I mean, vinnie
			foreach($box->connections_from(array("type" => "RELTYPE_DOC_IGNORE")) as $c)
			{
				if ($c->prop("to") == $o->parent())
				{
					$is_in_promo = false;
				}
			}

			if ($is_in_promo)
			{
				// check if it has ndocs > 0
				if ($box->prop("ndocs") || true)
				{
					// if so, check the sorting order and compare the current document to the current list
					// if it belongs in the list, add it to the list
					// how do we do that?
					// well, make an list of the documents in the current list
					// add the new document to it
					// and give the id's and sort by and length to an object_list and let the database sort it all out
					$ids = safe_array($box->meta("content_documents"));
					$ids[$set_oid] = $set_oid;
					$limit = $box->prop("ndocs");

					$filt = array(
						"oid" => $ids,
						"limit" => $limit > 0 ? $limit : null,
						"status" => ($box->prop("show_inact") ? array(STAT_ACTIVE, STAT_NOTACTIVE) : STAT_ACTIVE),
						new object_list_filter(array("non_filter_classes" => CL_DOCUMENT))
					);
					$ob = $this->_get_ordby($box);
					if (trim($ob) != "")
					{
						$filt["sort_by"] = $ob;
					}

					$ol = new object_list($filt);
					$ids = $ol->ids();
					if ($box->prop("start_ndocs") > 0)
					{
						$fin_cnt = $box->prop("ndocs") - $box->prop("start_ndocs");
						if (count($ids) > $fin_cnt)
						{
							$ids = array_slice($ids, count($ids) - $fin_cnt);
						}
					}
					// now we know the whole list, so just set that
					$box->set_meta("content_documents", $this->make_keys($ids));
					aw_disable_acl();
					$box->save();
					aw_restore_acl();
					continue;
				}
				else
				{
					$add_to_list = true;
				}
			}

			if ($o->status() == STAT_NOTACTIVE && !$box->prop("show_inact"))
			{
				$add_to_list = false;
			}

			if ($add_to_list)
			{
				$mt = safe_array($box->meta("content_documents"));
				$mt[$set_oid] = $set_oid;

				if ($box->prop("sort_by") != "")
				{
					// need to reorder list
					$filt = array(
						"oid" => $mt,
						"status" => ($box->prop("show_inact") ? array(STAT_ACTIVE, STAT_NOTACTIVE) : STAT_ACTIVE),
						new object_list_filter(array("non_filter_classes" => CL_DOCUMENT))
					);
					$ob = $this->_get_ordby($box);
					if (trim($ob) != "")
					{
						$filt["sort_by"] = $ob;
					}
					$ol = new object_list($filt);
					$mt = $this->make_keys($ol->ids());
				}

				$box->set_meta("content_documents", $mt);
				aw_disable_acl();
				$box->save();
				aw_restore_acl();
			}
		}
	}

	function _get_folders_for_box($box)
	{
		$ret = array();
		$subs = safe_array($box->meta("src_submenus"));
		foreach($box->connections_from(array("type" => "RELTYPE_DOC_SOURCE")) as $c)
		{
			$ret[$c->prop("to")] = isset($subs[$c->prop("to")]) and $subs[$c->prop("to")] == $c->prop("to");
		}

		if (!count($ret))
		{
			return array($box->id() => $box->id());
		}

		return $ret;
	}

	function _is_in_path($path, $f)
	{
		foreach($path as $o)
		{
			if ($o->id() == $f)
			{
				return true;
			}
		}
		return false;
	}

	function _is_in_paths($paths, $f)
	{
		foreach($paths as $oid => $path)
		{
			if ($this->_is_in_path($path, $f))
			{
				return $oid;
			}
		}
		return false;
	}

	function _get_ordby($box)
	{
		$ordby = NULL;
		if ($box->meta("sort_by") != "")
		{
			$ordby = $box->meta("sort_by");
			if ($box->meta("sort_ord") != "")
			{
				$ordby .= " ".$box->meta("sort_ord");
			}
			if ($box->meta("sort_by") == "documents.modified")
			{
				$ordby .= ", objects.created DESC";
			};
		}
		return $ordby;
	}

	function on_save_promo($arr)
	{
		$o = obj($arr["oid"]);

		// get list of docs for promo
		$si = get_instance("contentmgmt/site_show");

		$dd = $si->get_default_document(array(
			"obj" => $o
		));
		if ($dd == false)
		{
			$dd = array();
		}

		if (!is_array($dd))
		{
			$dd = array($dd);
		}
		$o->set_meta("content_documents", $this->make_keys($dd));

		$o->set_meta("version", 2);
		$o->save();
	}

	function on_delete_document($arr)
	{
		$o = obj($arr["oid"]);

		// figure out if this document is to be shown in any promo in the system
		// to do that
		// make a list of all promo boxes
		$ol = new object_list(array(
			"class_id" => CL_PROMO,
			"lang_id" => array(),
			"site_id" => array()
		));

		$path = $o->path();

		foreach($ol->arr() as $box)
		{
			$add_to_list = false;

			// for each box, check the folders where it gets documents and if this document's parent is one of them,
			$fld = $this->_get_folders_for_box($box);
			$is_in_promo = false;
			foreach($fld as $f => $subs)
			{
				if ($f == $o->parent() || ($subs && $this->_is_in_path($path, $f)))
				{
					$is_in_promo = true;
					break;
				}
			}

			if ($is_in_promo)
			{
				// get list of docs for promo
				$si = get_instance("contentmgmt/site_show");

				$box->set_meta("content_documents", $this->make_keys($si->get_default_document(array(
					"obj" => $box
				))));

				$box->set_meta("version", 2);
				if ($box->can("edit"))
				{
					$box->save();
				}
			}
		}
	}

	function kw_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$pt = $arr["obj_inst"]->id();
		if (aw_ini_get("config.keyword_folder"))
		{
			$pt = aw_ini_get("config.keyword_folder");
		}
		$tb->add_button(array(
			"name" => "new_kw",
			"tooltip" => t("M&auml;rks&otilde;na"),
			"url" => html::get_new_url(CL_KEYWORD, $pt, array("return_url" => get_ru())),
			"img" => "new.gif",
		));
		$tb->closed = 1;
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function _get_sss_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$ps = get_instance("vcl/popup_search");
		$tb->add_cdata($ps->get_popup_search_link(array("pn" => "_set_sss", "clid" => CL_MENU)));
	}

	function _get_lm_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$ps = get_instance("vcl/popup_search");
		$tb->add_cdata($ps->get_popup_search_link(array("pn" => "_set_lm_sss", "clid" => CL_MENU)));
	}

	function callback_mod_reforb($arr)
	{
		$arr["_set_sss"] = "0";
		$arr["_set_lm_sss"] = "0";
		$arr["link_pops"] = "0";
	}

	function callback_post_save($arr)
	{
		$ps = get_instance("vcl/popup_search");
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["_set_sss"], 1 /* RELTYPE_ASSIGNED_MENU */);
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["_set_lm_sss"], 2 /* RELTYPE_DOC_SOURCE */);
	}

	function _get_linker(&$p, $o)
	{
		$ps = new ct_linked_obj_search();
		$p["post_append_text"] = "";
		if ($this->can("view", $o->meta("linked_obj")))
		{
			$p["post_append_text"] = sprintf(t("Valitud objekt: %s /"), html::obj_change_url($o->meta("linked_obj")));
			$p["post_append_text"] .= " ".html::href(array(
				"url" => $this->mk_my_orb("remove_linked", array("id" => $o->id(), "ru" => get_ru()), "menu"),
				"caption" => html::img(array("url" => aw_ini_get("baseurl")."/automatweb/images/icons/delete.gif", "border" => 0))
			))." / ";
		}
		$p["post_append_text"] .= t(" Otsi uus objekt: ").$ps->get_popup_search_link(array(
			"pn" => "link_pops",
			"clid" => array(doc_obj::CLID, link_fix::CLID)
		));
	}

	function callback_pre_save($arr)
	{
		if ($this->can("view", $arr["request"]["link_pops"]))
		{
			$arr["obj_inst"]->set_meta("linked_obj", $arr["request"]["link_pops"]);
		}
	}

	function get_promo_link($o)
	{
		$link_str = $o->trans_get_val("link");
		if ($this->can("view", $o->meta("linked_obj")))
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
