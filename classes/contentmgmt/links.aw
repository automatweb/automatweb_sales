<?php

/*
@classinfo no_status=1 syslog_type=ST_LINKS prop_cb=1
@tableinfo extlinks index=id master_table=objects master_index=oid

@default group=general

	@property comment type=textarea cols=30 rows=5 table=objects
	@caption Kommentaar

	@property url type=textbox table=extlinks default=http://
	@caption URL

	@property alias type=textbox size=60 table=objects field=alias
	@caption Alias

	@property docid type=hidden table=extlinks

	@property hits type=text table=extlinks
	@caption Klikke

	@property alt type=textbox table=objects field=meta method=serialize search=1
	@caption Alt tekst

	@property newwindow type=checkbox ch_value=1 search=1 table=extlinks
	@caption Uues aknas

	@property ord type=textbox size=3 table=objects field=jrk
	@caption J&auml;rjekord

@groupinfo Javascript caption=N&auml;itamine table=extlinks

	@property use_javascript type=checkbox ch_value=1 search=1 group=Javascript table=objects field=meta method=serialize
	@caption Kasuta javascripti

	@property newwinwidth type=textbox group=Javascript table=objects field=meta method=serialize
	@caption Uue akna laius

	@property newwinheight type=textbox group=Javascript table=objects field=meta method=serialize
	@caption Uue akna k&otilde;rgus

	@property js_attributes type=chooser multiple=1 store=no group=Javascript
	@caption Atribuudid

	@property newwintoolbar type=checkbox ch_value=1 group=Javascript table=objects field=meta method=serialize
	@caption Toolbar

	@property newwinlocation type=checkbox ch_value=1 group=Javascript table=objects field=meta method=serialize
	@caption Address bar

	@property newwinmenu type=checkbox ch_value=1 group=Javascript table=objects field=meta method=serialize
	@caption Men&uuml;&uuml;d

	@property newwinscroll type=checkbox ch_value=1 group=Javascript table=objects field=meta method=serialize
	@caption Skrollbarid

	@property style_class type=textbox group=Javascript table=objects field=meta method=serialize
	@caption Stiiliklass

	@property before_link type=textbox group=Javascript table=objects field=meta method=serialize
	@caption Enne lingi teksti

	@property after_link type=textbox group=Javascript table=objects field=meta method=serialize
	@caption P&auml;rast lingi teksti

@groupinfo Pilt caption=Pilt

	@property link_image type=fileupload store=no editonly=1 group=Pilt
	@caption Pilt

	@property link_image_show type=text store=no editonly=1 group=Pilt
	@caption

	@property link_image_check_active type=checkbox ch_value=1 group=Pilt table=objects field=meta method=serialize
	@caption Pilt aktiivne

	@property link_image_active_until type=date_select group=Pilt table=objects field=meta method=serialize
	@caption Pilt aktiivne kuni


@default group=transl

	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

@default group=kws

	@property kws type=keyword_selector store=no
	@caption V&otilde;tmes&otilde;nad

@groupinfo transl caption=T&otilde;lgi
@groupinfo kws caption=V&otilde;tmes&otilde;nad


*/

class links extends class_base
{
	function links()
	{
		$this->init(array(
			"tpldir" => "automatweb/extlinks",
			"clid" => CL_EXTLINK,
		));

		$this->lc_load("extlinks","lc_extlinks");

		$this->trans_props = array(
			"name", "url", "alt"
		);
	}

	/**

		@attrib name=search_doc params=name

		@param s_name optional
		@param s_content optional

		@returns


		@comment

	**/
	function search_doc($arr)
	{
		extract($arr);
		$this->read_template("search_doc.tpl");

		if ($s_name != "" || $s_content != "")
		{

			load_vcl("table");
			$t = new aw_table(array(
				"layout" => "generic"
			));
			$t->define_field(array(
				"name" => "pick",
				"caption" => t("Vali see"),
			));
			$t->define_field(array(
				"name" => "name",
				"caption" => t("Nimetus"),
				"sortable" => 1
			));
			$t->define_field(array(
				"name" => "parent",
				"caption" => t("Asukoht"),
				"sortable" => 1
			));
			$t->define_field(array(
				"name" => "createdby",
				"caption" => t("Looja"),
				"sortable" => 1
			));
			$t->define_field(array(
				"name" => "modified",
				"caption" => t("Viimati muudetud"),
				"type" => "time",
				"format" => "d.m.Y / H:i",
				"sortable" => 1
			));
			$sres = new object_list(array(
				"class_id" => CL_DOCUMENT,
				"name" => "%".$s_name."%",
				"content" => "%".$s_content."%"
			));
			$sres->add(new object_list(array(
				"class_id" => CL_MENU,
				"name" => "%".$s_name."%"
			)));
			for($o = $sres->begin(); !$sres->end(); $o = $sres->next())
			{
				if (aw_ini_get("menuedit.long_section_url"))
				{
					$url = "/".$this->cfg["index_file"].".".AW_FILE_EXT."/section=".$o->id();
				}
				else
				{
					$url = "/".$o->id();
				}
				$name = strip_tags($o->name());
				$name = str_replace("'","",$name);

				$row["pick"] = html::href(array(
					"url" => 'javascript:ss("'.$url.'","'.str_replace("\"", "\\\"", str_replace("'", "&#39;", $o->name())).'")',
					"caption" => t("Vali see")
				));
				$row["name"] = html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $o->id())),
					"caption" => $o->name()
				));
				$row["parent"] = $o->path_str(array(
					"max_len" => 4
				));
				$row["createdby"] = $o->createdby();
				$row["modified"] = $o->modified();
				$t->define_data($row);

			}

			$t->set_default_sortby("name");
			$t->sort_by();
			$this->vars(array("LINE" => $t->draw()));
		}
		else
		{
			$s_name = "%";
			$s_content = "%";
		}
		$this->vars(array(
			"reforb" => $this->mk_reforb("search_doc", array("reforb" => 0)),
			"s_name"	=> $s_name,
			"s_content"	=> $s_content,
			"doc_sel" => checked($s_class_id != "item"),
		));
		return $this->parse();
	}

	/**
		@attrib name=show params=name nologin="1"
		@param id required type=int
		@returns
		@comment
	**/
	function show($arr)
	{
		extract($arr);
		$link = obj($id);
		$url = $this->trans_get_val($link, "url");

		if (substr(trim($url), 0, 4) === "www.")
		{
			$url = "http://{$url}";
		}

		if (!empty($url) && $link->prop("docid"))
		{
			$url = "/".$link->prop("docid");
		}

		if ($url{0} === "/")
		{
			$url = aw_ini_get("baseurl").substr($url, 1);
		}

		if (aw_ini_get("links.use_hit_counter"))
		{
			$this->add_hit($id, aw_global_get("HTTP_HOST"), aw_global_get("uid"));
		}

		header("Location: ".$url);
		die();
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "name":
				$js = "<script>
					tb = document.getElementById(\"".$prop["name"]."\");
					if (window.parent.name == \"InsertAWFupCommand\" && tb.value == \"\")
					{

						FCK=window.parent.opener.FCK;
						if(FCK.Selection.GetType() == \"Text\")
						{
							tb.value = (FCK.EditorDocument.selection)?FCK.EditorDocument.selection.createRange().text:FCK.EditorDocument.getSelection();
						}
					}
				</script>";
				$prop["post_append_text"] = $js;
			break;
			case "newwintoolbar":
			case "newwinlocation":
			case "newwinmenu":
			case "newwinscroll":
				$retval = PROP_IGNORE;
				break;

			case "js_attributes":
				$prop["options"] = array(
					"newwintoolbar" => t("T&ouml;&ouml;riistariba"),
					"newwinlocation" => t("Aadressi riba"),
					"newwinmenu" => t("Men&uuml;&uuml;d"),
					"newwinscroll" => t("Kerimisriba"),
				);
				$prop["value"]["newwintoolbar"] = $arr['obj_inst']->prop("newwintoolbar");
				$prop["value"]["newwinlocation"] = $arr['obj_inst']->prop("newwinlocation");
				$prop["value"]["newwinmenu"] = $arr['obj_inst']->prop("newwinmenu");
				$prop["value"]["newwinscroll"] = $arr['obj_inst']->prop("newwinscroll");
				break;


			case "link_image_show":
				$img = new object_list(array(
					"parent" => $arr["obj_inst"]->id(),
					"class_id" => CL_FILE,
					"lang_id" => array()
				));
				if ($img->count() > 0)
				{
					$o = $img->begin();
					$f = get_instance(CL_FILE);
					if ($f->can_be_embedded($o))
					{
						$prop['value'] = html::img(array(
							'url' => file::get_url($o->id(),$o->name())
						));
					}
				}
				break;

			case "url":
				$o = $arr["obj_inst"];
				$p =& $arr["prop"];
				$ps = new ct_linked_obj_search();
				if ($this->can("view", $o->meta("linked_obj")))
				{
					$p["post_append_text"] = sprintf(t("Valitud objekt: %s /"), html::obj_change_url($o->meta("linked_obj")));
					$p["post_append_text"] .= " ".html::href(array(
						"url" => $this->mk_my_orb("remove_linked", array("id" => $o->id(), "ru" => get_ru()), "menu"),
						"caption" => html::img(array("url" => aw_ini_get("baseurl")."automatweb/images/icons/delete.gif", "border" => 0))
					))." / ";
				}
				else
				{
					$p["post_append_text"] = "";
				}

				$p["post_append_text"] .= t(" Otsi uus objekt: ").$ps->get_popup_search_link(array(
					"pn" => "link_pops",
					"clid" => array(CL_DOCUMENT, CL_EXTLINK)
				));
				break;

			case "link_image_active_until":
				$prop["year_from"] = 1930;
				break;
		}
		return $retval;
	}

	function set_property(&$arr)
	{
		$prop = $arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "newwintoolbar":
			case "newwinlocation":
			case "newwinmenu":
			case "newwinscroll":
				$retval = PROP_IGNORE;
				break;

			case "js_attributes":
				$arr['obj_inst']->set_prop("newwintoolbar",isset($prop["value"]["newwintoolbar"]) ? 1 : 0);
				$arr['obj_inst']->set_prop("newwinlocation",isset($prop["value"]["newwinlocation"]) ? 1 : 0);
				$arr['obj_inst']->set_prop("newwinmenu",isset($prop["value"]["newwinmenu"]) ? 1 : 0);
				$arr['obj_inst']->set_prop("newwinscroll",isset($prop["value"]["newwinscroll"]) ? 1 : 0);
				break;


			case "link_image":
				$old_file = 0;

				$img = new object_list(array(
					"parent" => $arr["obj_inst"]->id(),
					"class_id" => CL_FILE,
					"lang_id" => array()
				));
				if ($img->count() > 0)
				{
					$o = $img->begin();
					$old_file = $o->id();
				}

				$f = new file();
				$f->add_upload_image("link_image", $arr['obj_inst']->id(), $old_file);
				$retval = PROP_IGNORE;
				break;
		}
		return $retval;
	}

	function _set_url(&$arr)
	{
		$r = class_base::PROP_OK;
		try
		{
			$url = new aw_uri($arr["prop"]["value"]);

			if ("javascript" === $url->get_scheme())
			{
				$r = class_base::PROP_ERROR;
				//XXX: v6ibolla vaja paremat lahendust, sest v6ib vaja olla js linke teha
				// for security reasons, disallow javascript
				$arr["prop"]["error"] = t("URI skeem 'javascript' pole lubatud.");
			}
			else
			{
				$arr["prop"]["value"] = $url->get();
			}
		}
		catch (Exception $e)
		{
			$r = class_base::PROP_ERROR;
			$arr["prop"]["error"] = t("Sisestatud URL on ebakorrektne.");
		}
		return $r;
	}

	////
	// !Hoolitseb ntx doku sees olevate extlinkide aliaste parsimise eest (#l2#)
	function parse_alias($args = array())
	{
		$ld = new links_display();
		return $ld->parse_alias($args);
	}

	// registreerib kliki lingile
	// peab ehitama ka mehhanisimi sp&auml;mmimise v&auml;ltimiseks
	function add_hit($id)
	{
		$o = obj($id);
		$prev = obj_set_opt("no_cache", 1);
		$o->set_prop("hits", $o->prop("hits")+1);
		$o->save();
		obj_set_opt("no_cache", $prev);
		$this->_log(ST_EXTLINK, SA_CLICK, $o->name(), $id);
	}

	function request_execute($obj)
	{
		$this->show(array("id" => $obj->id()));
	}

	function callback_post_save($arr)
	{
		if (!empty($arr["request"]["save_and_doc"]))
		{
			if (aw_ini_get("extlinks.directlink") == 1)
			{
				$link_url = $arr["obj_inst"]->prop("url");
				if (substr($link_url, 0, 3) === "www")
				{
					$link_url = "http://".$link_url;
				}
			}
			else
			{
				$link_url = obj_link($arr["obj_inst"]->id());
			}

			$url = $this->mk_my_orb("fetch_file_tag_for_doc", array("id" => $arr["obj_inst"]->id()), CL_FILE);

			$i = new image();
			$i->gen_image_alias_for_doc(array(
				"img_id" => $arr["obj_inst"]->id(),
				"doc_id" => $arr["request"]["ldocid"] ? $arr["request"]["ldocid"] : $arr["request"]["id"],
				"no_die" => 1
			));

			die("
				<script type=\"text/javascript\" src=\"".aw_ini_get("baseurl")."automatweb/js/aw.js\"></script>
				<script language='javascript'>

				function SetAttribute( element, attName, attValue )
				{
					if ( attValue == null || attValue.length == 0 )
					{
						element.removeAttribute( attName, 0 ) ;
					}
					else
					{
						element.setAttribute( attName, attValue, 0 ) ;
					}
				}

				FCK=window.parent.opener.FCK;
				var eSelected = FCK.Selection.MoveToAncestorNode(\"A\");
				if (eSelected)
				{
					eSelected.href=\"".$link_url."\";
					eSelected.innerHTML=\"".addslashes($arr["obj_inst"]->prop("name"))."\";
					SetAttribute( eSelected, \"_fcksavedurl\", \"$link_url\" ) ;
				}
				else
				{
					FCK.InsertHtml(aw_get_url_contents(\"$url\"));
				}

				window.parent.close();
			</script>
			");
		}


	}

	function callback_generate_scripts($arr)
	{
		return "
		if (window.parent.name == \"InsertAWFupCommand\")
		{
		nsbt = document.createElement('input');nsbt.name='save_and_doc';nsbt.type='submit';nsbt.id='button';nsbt.value='".t("Salvesta ja paiguta dokumenti")."'; el = document.getElementById('buttons');el.appendChild(nsbt);}";
	}

	function callback_mod_retval(&$arr)
	{
		$arr["args"]["docid"] = $arr["request"]["docid"];
	}

	function callback_mod_reforb(&$arr, $request)
	{
		if (isset($request["ldocid"])) $arr["ldocid"] = $request["ldocid"];
		$arr["link_pops"] = "0";
	}

	function callback_mod_tab(&$arr)
	{
		if (!empty($_REQUEST["docid"]))
		{
			$arr["link"] = aw_url_change_var("docid", $_REQUEST["docid"], $arr["link"]);
		}
		if ($arr["id"] === "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	/**
		@attrib name=gen_link_alias_for_doc params=name
		@param doc_id required type=int
		@param link_id required type=int
		@param close optional type=bool
	**/
	function gen_link_alias_for_doc($arr)
	{
		$c = new connection();
		if (is_oid($doc_id))
		{
			$c->load(array(
				"from" => $arr["doc_id"],
				"to" => $arr["link_id"],
			));
			$c->save();
		}
		$close = "<script language=\"javascript\">
		javascript:window.parent.close();
		</script>";
		$out = $arr["close"]?$close:$c->id();
		die($out);
	}

	function callback_pre_save($arr)
	{
		if ($this->can("view", $arr["request"]["link_pops"]))
		{
			$arr["obj_inst"]->set_meta("linked_obj", $arr["request"]["link_pops"]);
		}
	}
}
