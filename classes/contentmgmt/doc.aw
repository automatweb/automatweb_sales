<?php
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_DOCUMENT, on_save_document)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_DOCUMENT, on_add_doc_rel)

@classinfo trans=1 no_comment=1 relationmgr=yes syslog_type=ST_DOCUMENT r2=1

@default table=documents
@default group=general

	@property navtoolbar type=toolbar no_caption=1 store=no trans=1
	@caption Toolbar

	@property brother_warning type=text store=no no_caption=1
	@property simultaneous_warning type=text store=no no_caption=1

	@property title type=textbox size=60 trans=1
	@caption Pealkiri

	@property subtitle type=textbox size=60 trans=1
	@caption Alapealkiri

	@property alias_ch type=checkbox ch_value=1 default=1 field=meta method=serialize table=objects
	@caption Genereeri alias automaatselt

	@property alias type=textbox size=60 table=objects field=alias
	@caption Alias

	@property author type=textbox size=60 trans=1
	@caption Autor

	@property photos type=textbox size=60 trans=1
	@caption Fotode autor

	@property keywords type=textbox size=60 trans=1
	@caption V&otilde;tmes&otilde;nad

	@property names type=textbox size=60 trans=1
	@caption Nimed

	@property lead type=textarea richtext=1 cols=60 rows=10 trans=1
	@caption Lead

	@property content type=textarea richtext=1 cols=60 rows=30 trans=1
	@caption Sisu

	@property moreinfo type=textarea richtext=1 cols=60 rows=5 trans=1
	@caption Lisainfo

	@property link_text type=textbox size=60 trans=1
	@caption URL

	@property is_forum type=checkbox ch_value=1 trans=1
	@caption Foorum

	@property showlead type=checkbox ch_value=1 default=1 trans=1
	@caption N&auml;ita leadi

	@property show_modified type=checkbox ch_value=1 trans=1 default=1
	@caption N&auml;ita muutmise kuup&auml;eva

	@property doc_modified type=hidden table=documents field=modified trans=1
	@caption Dok. modified

	@property clear_styles type=checkbox ch_value=1 store=no trans=1
	@caption T&uuml;hista stiilid

	@property link_keywords type=checkbox ch_value=1 store=no trans=1
	@caption Lingi v&otilde;tmes&otilde;nad

	@property link_keywords2 type=checkbox ch_value=1 field=meta method=serialize table=objects default=1
	@caption V&otilde;tmes&otilde;nad lingina

	@property frontpage_left type=checkbox ch_value=1 trans=1
	@caption Esilehel tulbas

	@property frontpage_right type=checkbox ch_value=1 trans=1
	@caption Esilehel tulbas paremal

	@property frontpage_center type=checkbox ch_value=1 trans=1
	@caption Esilehel keskel

	@property esilehel type=checkbox ch_value=1 trans=1
	@caption Esilehel

	@property jrk1 type=textbox size=5 ch_value=1 trans=1
	@caption Jrk1

	@property esilehel_uudis type=checkbox ch_value=1 trans=1
	@caption Esilehel uudis

	@property jrk2 type=textbox size=5 ch_value=1 trans=1
	@caption Jrk2

	@property dcache type=checkbox store=no trans=1
	@caption Cache otsingu jaoks

	@property dcache_content type=hidden field=dcache
	@property rating type=hidden
	@property num_ratings type=hidden

	@property show_title type=checkbox ch_value=1 default=1 trans=1
	@caption N&auml;ita pealkirja

	@property no_search type=checkbox ch_value=1 trans=1
	@caption J&auml;ta otsingust v&auml;lja

	@property cite type=textarea cols=60 rows=10 trans=1
	@caption Tsitaat

	@property tm type=textbox size=20 trans=1
	@caption Kuup&auml;ev

	@property show_print type=checkbox ch_value=1 table=objects field=meta method=serialize default=1 trans=1
	@caption 'Prindi' nupp

	@property createdby table=objects field=createdby type=text trans=1
	@caption Kes tegi

	@property user1 table=documents type=textbox size=60 trans=1
	@caption Kasutaja defineeritud 1

	@property user2 table=documents type=textarea rows=2 cols=60 trans=1
	@caption Kasutaja defineeritud 2

	@property user3 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 3

	@property user4 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 4

	@property user5 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 5

	@property user6 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 6

	@property user7 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 7

	@property user8 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 8

	@property user9 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 9

	@property user10 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 10

	@property user11 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 11

	@property user12 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 12

	@property user13 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 13

	@property user14 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 14

	@property user15 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 15

	@property user16 table=documents type=textbox trans=1
	@caption Kasutaja defineeritud 16


	@property userta2 table=documents field=aw_userta2 type=textarea rows=10 cols=60 trans=1
	@caption Kasutaja defineeritud textarea 2

	@property userta3 table=documents field=aw_userta3 type=textarea rows=10 cols=60 trans=1
	@caption Kasutaja defineeritud textarea 3

	@property userta4 table=documents field=aw_userta4 type=textarea rows=10 cols=60 trans=1
	@caption Kasutaja defineeritud textarea 4

	@property userta5 table=documents field=aw_userta5 type=textarea rows=10 cols=60 trans=1
	@caption Kasutaja defineeritud textarea 5

	@property userta6 table=documents field=aw_userta6 type=textarea rows=10 cols=60 trans=1
	@caption Kasutaja defineeritud textarea 6

	@property ucheck1 type=checkbox ch_value=1 table=documents field=aw_ucheck1
	@caption Kasutaja defineeritud checkbox 1

	@property ucheck2 type=checkbox ch_value=1 table=documents field=ucheck2
	@caption Kasutaja defineeritud checkbox 2

	@property ucheck3 type=checkbox ch_value=1 table=documents field=ucheck3
	@caption Kasutaja defineeritud checkbox 3

	@property ucheck4 type=checkbox ch_value=1 table=documents field=ucheck4
	@caption Kasutaja defineeritud checkbox 4

	@property ucheck5 type=checkbox ch_value=1 table=documents field=ucheck5
	@caption Kasutaja defineeritud checkbox 5

	@property ucheck6 type=checkbox ch_value=1 table=documents field=ucheck6
	@caption Kasutaja defineeritud checkbox 6

	@property uservar1 type=classificator field=aw_varuser1 reltype=RELTYPE_VARUSER1 store=connect
	@caption User-defined var 1

	@property uservar2 type=classificator field=aw_varuser2 reltype=RELTYPE_VARUSER2 store=connect
	@caption User-defined var 2

	@property uservar3 type=classificator field=aw_varuser3 reltype=RELTYPE_VARUSER3 store=connect
	@caption User-defined var 3

	@property language type=text type=text store=no trans=1
	@caption Keel

	@property gen_static type=checkbox store=no trans=1
	@caption Genereeri staatiline

	@property sbt type=submit value=Salvesta store=no trans=1

	@property cb_part type=hidden value=1 group=general,settings store=no
	@caption cb_part

	@property nobreaks type=hidden table=documents

	@property no_topic_links type=checkbox table=objects field=meta method=serialize ch_value=1
	@caption &Auml;ra tee Samal teemal linke

	@property create_new_version type=checkbox ch_value=1 store=no
	@caption Loo uus versioon

	@property edit_version type=select store=no
	@caption Vali versioon, mida muuta

	@property associated_file type=fileupload store=no
	@caption Lisa fail

@default group=settings

	@property show_to_country type=textbox
	@caption N&auml;htav vaid nendest riikidest tulijatele

	@property no_right_pane type=checkbox ch_value=1 trans=1
	@caption Ilma parema paanita

	@property no_left_pane type=checkbox ch_value=1 trans=1
	@caption Ilma vasaku paanita

	@property title_clickable type=checkbox ch_value=1 trans=1 default=1
	@caption Pealkiri klikitav

	@property esilehel type=checkbox ch_value=1 trans=1
	@caption Esilehel

	@property dcache_save type=checkbox ch_value=1 table=objects field=meta method=serialize trans=1
	@caption Cache otsingu jaoks (salvestub)

	@property no_last type=checkbox ch_value=1 trans=1
	@caption &Auml;ra arvesta muutmist

	@property show_last_changed type=checkbox ch_value=1 trans=1 table=objects field=meta method=serialize
	@caption Muutmise kuupaev dokumendi sees

	@property no_show_in_promo type=checkbox ch_value=1 trans=1 table=documents field=no_show_in_promo method=
	@caption &Auml;ra n&auml;ita konteineris

	@property show_in_iframe type=checkbox ch_value=1 table=objects field=meta method=serialize
	@caption Kasuta siseraami

	@property target_audience type=chooser  store=connect multiple=1 reltype=RELTYPE_TARGET_AUDIENCE table=documents field=aw_target_audience
	@caption Sihtr&uuml;hm

	@property doc_content_type type=chooser  store=connect multiple=1 reltype=RELTYPE_DOC_CONTENT_TYPE table=documents field=aw_doc_content_type
	@caption Dokumendi sisu t&uuml;&uuml;p

@default group=relationmgr

	@property aliasmgr type=aliasmgr store=no editonly=1 trans=1
	@caption Aliastehaldur

@default group=calendar

	@property start type=date_select table=planner trans=1
	@caption Algab (kp)

	@property start1 type=datetime_select field=start table=planner trans=1
	@caption Algab

	@property duration type=time_select field=end table=planner trans=1
	@caption Kestab

@default group=kws

	@property kw_tb type=toolbar no_caption=1 store=no group=keywords

	@property kws type=keyword_selector store=no reltype=RELTYPE_KEYWORD
	@caption V&otilde;tmes&otilde;nad

@default group=versions

	@property versions_tb type=toolbar store=no no_caption=1
	@property versions type=table store=no no_caption=1

@groupinfo timing caption="Ajaline aktiivsus"
@default group=timing

	@property timing type=timing store=no
	@caption Ajaline aktiivsus

@default group=transl

	@property trans_tb type=toolbar no_caption=1 store=no

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

@default group=comments

	@property comments_tb type=toolbar store=no no_caption=1 no_rte_button=1
	@property comments_tbl type=table store=no no_caption=1

@groupinfo calendar caption=Kalender
@groupinfo settings caption=Seadistused icon=archive.gif
@groupinfo comments caption=Kommentaarid submit=no
@groupinfo kws caption="V&otilde;tmes&otilde;nad"
@groupinfo versions caption="Versioonid"
@groupinfo transl caption=T&otilde;lgi
@groupinfo relationmgr caption=Seostehaldur submit=no
@groupinfo acl caption=&Otilde;igused
@default group=acl

	@property acl type=acl_manager store=no
	@caption &Otilde;igused

@tableinfo documents index=docid master_table=objects master_index=brother_of
@tableinfo planner index=id master_table=objects master_index=brother_of

@reltype TIMING value=20 clid=CL_TIMING
@caption Aeg

@reltype REMINDER value=21 clid=CL_REMINDER
@caption Meeldetuletus

@reltype LANG_REL value=22 clid=CL_DOCUMENT
@caption Keeleseos


@reltype VARUSER1 value=23 clid=CL_META hidden=1
@caption kasutajadefineeritud muutuja 1

@reltype VARUSER2 value=24 clid=CL_META hidden=1
@caption kasutajadefineeritud muutuja 2

@reltype VARUSER3 value=25 clid=CL_META hidden=1
@caption kasutajadefineeritud muutuja 3

@reltype TARGET_AUDIENCE value=26 clid=CL_TARGET_AUDIENCE hidden=1
@caption Sihtr&uuml;hm

@reltype DOC_CONTENT_TYPE value=27 clid=CL_DOCUMENT_CONTENT_TYPE hidden=1
@caption Dokumendi sisu t&uuml;&uuml;p

@reltype KEYWORD value=28 clid=CL_KEYWORD
@caption V&otilde;tmes&otilde;na

*/

class doc extends class_base
{
	protected $_save_versions;
	protected $_preview;
	protected $_modified;
	protected $force_new_version;
	protected $clear_styles;

	function doc($args = array())
	{
		$this->init(array(
			"clid" => CL_DOCUMENT,
			"tpldir" => "automatweb/documents",
		));
		$this->trans_props = array(
			"alias", "title","lead","content","user7"
		);
	}

	function get_property($arr)
	{
		// let site mod props
		$si = __get_site_instance();
		if ($si && !empty($arr["prop"]["name"]))
		{
			$meth = "get_property_doc_".$arr["prop"]["name"];
			if (method_exists($si, $meth))
			{
				$si->$meth($arr);
			}
		}
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "brother_warning":
				if ($arr["obj_inst"]->is_brother())
				{
					$data["value"] = sprintf(t("NB! Seda dokumenti n&auml;idatakse mitmes kohas, olge palun ettevaatlik! (%s)"), html::get_change_url($arr["obj_inst"]->brother_of(), array("return_url" => get_ru()),
					t("Originaal")));
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "doc_content_type":
				$ol = new object_list(array("class_id" => CL_DOCUMENT_CONTENT_TYPE, "lang_id" => array(), "site_id" => array()));
				$data["options"] = $ol->names();
				break;

			case "target_audience":
				$ol = new object_list(array("class_id" => CL_TARGET_AUDIENCE, "lang_id" => array(), "site_id" => array()));
				$data["options"] = $ol->names();
				if (!is_oid($arr["obj_inst"]->id()))
				{
					$data["value"] = $this->make_keys(array_keys($data["options"]));
				}
				break;

			case "kw_tb":
				$this->kw_tb($arr);
				break;

			case "name":
				$retval = PROP_IGNORE;
				break;

			case "tm":
				if ($arr["new"])
				{
					$format = aw_ini_get("document.date_format");
					if ($format == "n/a")
					{
						$format = "";
					}
					else
					if (empty($format))
					{
						$format = "d.m.Y";
					};
					$data["value"] = date($format);
				};
				break;

			case "duration":
				$_tmp = $arr["data"]["planner"]["end"] - $arr["data"]["planner"]["start"];
				$data["value"] = array(
					"hour" => (int)($_tmp/3600),
					"minute" => ($_tmp % 3600) / 60,
				);
				break;

			case "navtoolbar":
				// I need a better way to do this!
				if (!empty($arr["request"]["cb_part"]))
				{
					$retval = PROP_IGNORE;
				}
				else
				{
					$this->gen_navtoolbar($arr);
				};
				break;

			case "edit_version":
				$data["options"] = $this->get_version_list($arr["obj_inst"]->id());
				$data["value"] = aw_url_change_var("edit_version", $arr["request"]["edit_version"]);
				$data["onchange"] = "window.location = this.options[this.selectedIndex].value;";
				break;

			case "versions":
				$this->_versions($arr);
				break;

			case "versions_tb":
				$this->_versions_tb($arr);
				break;

			case "trans_tb":
				$this->_trans_tb($arr);
				break;

			case "simultaneous_warning":
				return $this->_get_simultaneous_warning($arr);

			case "comments_tb":
				$this->_comments_tb($arr);
				break;

			case "comments_tbl":
				$this->_comments_tbl($arr);
				break;
		}

		if(in_array($data["name"], array("content", "lead")) && !empty($data["richtext"]))
		{
			$cb_nobreaks = $arr["obj_inst"]->meta("cb_nobreaks");
			if(empty($cb_nobreaks[$data["name"]]))
			{
				$data["value"] = nl2br($data["value"]);
			}
		}

		return $retval;
	}

	function set_property($args = array())
	{
		$data = &$args["prop"];
		$retval = class_base::PROP_OK;
		$data["value"] = html_entity_decode($data["value"], ENT_COMPAT, aw_global_get("charset"));
		switch($data["name"])
		{
			case "transl":
				$i = new menu();
				$i->write_trans_aliases($args);
				$this->trans_save($args, $this->trans_props, array("user1", "user3", "user5", "userta2", "userta4","userta3", "userta5", "userta6"));
				$this->funnel_ct_content($args);
				break;

			case "create_new_version":

				break;

			case "link_calendars":
				$this->update_link_calendars($arg);
				break;

			case "link_keywords":
				if (is_oid($args["obj_inst"]->id()))
				{
					$kw = new keyword();
					if (isset($args["request"]["keywords"]))
					{
						$kw->update_keywords(array(
							"keywords" => $args["request"]["keywords"],
							"oid" => $args["obj_inst"]->id(),
						));
					}
					else
					{
						$kw->update_relations(array(
							"id" => $args["obj_inst"]->id(),
							"data" => $args["request"]["content"],
						));
						// also update keyword brother docs
						$kw->update_menu_keyword_bros(array("doc_ids" => array($args["obj_inst"]->id())));
					};
				};
				break;

			case "tm":
				$modified = time();
				list($_date, $_time) = explode(" ", $data["value"]);
				list($hour, $min) = explode(":", $_time);
				$hour = (int)$hour;
				$min = (int)$min;

				$try = explode("/",$_date);
				if (count($try) < 3)
				{
					$ts = 0;
				}
				else
				{
					list($day,$mon,$year) = explode("/",$_date);

					$ts = mktime($hour,$min,0,$mon,$day,$year);
				}

				if ($ts > (3600*24*400))
				{
					$modified = $ts;
				}
				else
				{
					// 2kki on punktidega eraldatud
					if ($_date == "")
					{
						$_date = $data["value"];
					}
					list($day,$mon,$year) = explode(".",$_date);
					$ts = mktime($hour,$min,0,$mon,$day,$year);
					if ($ts)
					{
						$modified = $ts;
					}
					else
					{
						// 2kki on hoopis - 'ga eraldatud?
						list($day,$mon,$year) = explode("-",$_date);
						$ts = mktime($hour,$min,0,$mon,$day,$year);
						if ($ts)
						{
							$modified = $ts;
						}
					}
				}

				// we need this later too
				$this->_modified = $modified;
				break;

			case "dcache":
				if (aw_ini_get("document.use_dcache"))
				{
					//print "generating preview<br>";
					$dcx = get_instance(CL_DOCUMENT);
					$preview = $dcx->gen_preview(array("docid" => $args["obj_inst"]->id()));
					$this->quote($preview);
					$this->_preview = $preview;
				};
				break;

			case "gen_static":
				if (!empty($data["value"]) && is_oid($args["obj_inst"]->id()))
				{
					$dcx = get_instance(CL_DOCUMENT);
					// but this dies anyway
					$dcx->gen_static_doc($args["obj_inst"]->id());
				};
				break;

			case "clear_styles":
				if (isset($args["request"]["clear_styles"]))
				{
					$this->clear_styles = true;
				};
				break;

			case "duration":
				$_start = date_edit::get_timestamp($args["request"]["start1"]);
				$_end = $_start + (3600 * $data["value"]["hour"]) + (60 * $data["value"]["minute"]);
				$data["value"] = $_end;
				break;

			case "content":
				if ($args["request"]["content"]["cb_breaks"] == 0)
				{
					$args["obj_inst"]->set_prop("nobreaks",0);
				};
				// also, if the cfgform says that you are using fck editor, then
				// set the nobreaker
				if ($this->can("view", $args["request"]["cfgform"]))
				{
					$cff = obj($args["request"]["cfgform"]);
					if ($cff->prop("classinfo_allow_rte") == 2)
					{
						$args["obj_inst"]->set_prop("nobreaks",0);
					}
				}
				break;

			case "versions":
				$this->_save_versions = true;
				$args["obj_inst"]->set_no_modify(true);
				break;

			case "associated_file":
				$file = $_FILES["associated_file"]["tmp_name"];
				$file_name = $_FILES["associated_file"]["name"];
				$file_type = $_FILES["associated_file"]["type"];

				if (is_uploaded_file($file))
				{
					if ($this->cfg["upload_virus_scan"])
					{
						if (($vir = $this->_do_virus_scan($file)))
						{
							$data["error"] = "Uploaditud failis on viirus $vir!";
							return class_base::PROP_FATAL_ERROR;
						}
					}

					$pathinfo = pathinfo($file_name);

					if (empty($file_type))
					{
						$mimeregistry = get_instance("core/aw_mime_types");
						$realtype = $mimeregistry->type_for_ext($pathinfo["extension"]);
						$file_type = $realtype;
					}

					$content = file_get_contents($file);

					$cl_file = new file();
					$file_oid = $cl_file->save_file(array(
						"type" => $file_type,
						"content" => $content,
						"parent" => $args["obj_inst"]->parent(),
						"name" => $file_name,
					));

					$args["obj_inst"]->connect(array(
						"to" => $file_oid
					));
				}
				else
				{
					$retval = class_base::PROP_IGNORE;
				}
				break;
		}
		return $retval;
	}

	function callback_pre_save($args = array())
	{
		// map title to name
		$obj_inst = $args["obj_inst"];
		$obj_inst->set_name($obj_inst->prop("title"));

		if (isset($this->_preview))
		{
			$obj_inst->set_meta("dcache",$this->_preview);
			$res = trim(preg_replace("/<.*>/imsU", " ",$this->_preview));
			$len = strlen($res);
			for($i = 0; $i < $len; $i++)
			{
				if (ord($res{$i}) < 32)
				{
					$res{$i} = " ";
				}
			}
			$obj_inst->set_prop("dcache_content", $res);
		}

		if (isset($this->_modified))
		{
			$obj_inst->set_prop("doc_modified",$this->_modified);
		}

		// RTE also has a button to clear styles
		if ($this->clear_styles)
		{
			$obj_inst->set_prop("content",$this->_doc_strip_tags($obj_inst->prop("content")));
			$obj_inst->set_prop("lead",$this->_doc_strip_tags($obj_inst->prop("lead")));
			$obj_inst->set_prop("moreinfo",$this->_doc_strip_tags($obj_inst->prop("moreinfo")));
		}

		if ($this->can("view", $args["request"]["cfgform"]))
		{
			$cff = obj($args["request"]["cfgform"]);
			if ($cff->prop("classinfo_allow_rte") == 2)
			{
				$obj_inst->set_prop("nobreaks",1);
			}
			//if ($cff->prop("on_save_settings_remove_word_html") == 1)
			//{
				$obj_inst->set_prop("content",$this->_doc_clean_html($obj_inst->prop("content")));
				$obj_inst->set_prop("lead",$this->_doc_clean_html($obj_inst->prop("lead")));
				$obj_inst->set_prop("moreinfo",$this->_doc_clean_html($obj_inst->prop("moreinfo")));
			//}
		}

		$old_tm = $obj_inst->prop("tm");
		if (empty($old_tm) && !empty($args["request"]["tm"]))
		{
			$obj_inst->set_prop("tm",date("d.m.y",$obj_inst->prop("modified")));
		}

		$modby = $obj_inst->modifiedby();
		if ($args["request"]["edit_version"])
		{
			$out = array();
			parse_str($args["request"]["edit_version"], $out);
			if ($out["edit_version"] != "")
			{
				$this->quote($out["edit_version"]);
				$modby = $this->db_fetch_field("SELECT vers_crea_by FROM documents_versions WHERE docid = ".$obj_inst->id()." AND version_id = '".$out["edit_version"]."'", "vers_crea_by");
			}
		}

		if ($args["request"]["create_new_version"] == 1)
		{
			$obj_inst->set_create_new_version();
		}
		else
		if ($args["request"]["edit_version"])
		{
			$out = array();
			parse_str($args["request"]["edit_version"], $out);
			if ($out["edit_version"] != "")
			{
				$obj_inst->set_save_version($out["edit_version"]);
			}
		}
	}

	function callback_post_save($args = array())
	{
		if ($args["obj_inst"]->prop("dcache_save") == 1)
		{
			$dcx = get_instance(CL_DOCUMENT);
			$preview = $dcx->gen_preview(array(
				"docid" => $args["obj_inst"]->id()
			));
			$this->quote($preview);

			$res = trim(preg_replace("/<.*>/imsU", " ",$preview));
			$len = strlen($res);
			for($i = 0; $i < $len; $i++)
			{
				if (ord($res{$i}) < 32)
				{
					$res{$i} = " ";
				}
			}
			$args["obj_inst"]->set_prop("dcache_content", $res);
			$args["obj_inst"]->save();
		}

		if ($this->_save_versions)
		{
			$this->_save_versions($args);
		}

		if(aw_ini_get("menu.automatic_aliases")  && $args["obj_inst"]->prop("alias_ch") == 1)
		{
			if(!strlen($args["obj_inst"]->alias()))
			{
				$m = get_instance(CL_MENU);
				$new_alias = $m->gen_nice_alias($args["obj_inst"]->name());
				if ($new_alias != $args["obj_inst"]->alias())
				{
					$args["obj_inst"]->set_alias($new_alias);
					$args["obj_inst"]->save();
				}
			}
		}

		if(!$args["request"]["no_rte"])
		{
			$props = $this->get_property_group($args);
			foreach($props as $prop)
			{
				if($prop["type"] === "textarea" && $prop["richtext"])
				{
					$val = $args["obj_inst"]->prop($prop["name"]);
					$setval = false;
					if(substr($val, -6, 6) == "<br>".chr(13).chr(10))
					{
						$val = substr($val, 0, -6);
						$setval = true;
					}
					elseif(substr($val, -2, 2) == chr(13).chr(10))
					{
						$val = substr($val, 0, -2);
						$setval = true;
					}
					if($setval)
					{
						$args["obj_inst"]->set_prop($prop["name"], $val);
						$args["obj_inst"]->save();
					}
				}
			}
		}
	}

	private function _doc_strip_tags($arg)
	{
		$arg = strip_tags($arg,"<b><i><u><br /><p><ul><li><ol>");
		$arg = str_replace("<p>","",$arg);
		$arg = str_replace("<p>","",$arg);
		$arg = str_replace("</p>","",$arg);
		$arg = str_replace("</p>","",$arg);
		return $arg;
	}

	// clean word
	private function _doc_clean_html($html)
	{
		$html = ereg_replace("<(/)?(meta|title|style|font|span|del|ins)[^>]*>","",$html);

		// another pass over the html 2x times, removing unwanted attributes
		//$html = ereg_replace("<([^>]*)(class|lang|size|face)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>","<\\1>",$html);
		//$html = ereg_replace("<([^>]*)(class|lang|size|face)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>","<\\1>",$html);

		// kill table cell style tag
		preg_match_all("/<td.*>/imsU", $html, $mt);
		foreach($mt[0] as $key=>$td)
		{
			$html = str_replace($td, preg_replace("/^(<td.*)(style=\".*\")(.*>)$/isU","\\1 \\3",$td), $html);
		}

		// finishing up - maby should make optional
		$html = preg_replace(
			array("/<!--.*-->/imsU", "/\<h([0-9])\s*\>/imsU"),
			array("", "<h\\1>"),
			$html
		);
		return $html;
	}

	private function gen_navtoolbar($arr)
	{
		$toolbar = $arr["prop"]["toolbar"];
		$toolbar->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"url" => "javascript:submit_changeform();",
			"img" => "save.gif",
		));


		if (is_object($arr["obj_inst"]) && $arr["obj_inst"]->id())
		{
			$dd = get_instance("doc_display");
			$url = $dd->get_doc_link($arr["obj_inst"]);
			if($arr["obj_inst"]->prop("alias"))
			{
				$ss = get_instance("contentmgmt/site_show");
				$url = $ss->make_menu_link($arr["obj_inst"]);
			}
			if (ifset($arr, "request", "edit_version") != "")
			{
				$url = aw_url_change_var("docversion", $arr["request"]["edit_version"], $url);
			}
			$toolbar->add_button(array(
				"name" => "preview",
				"tooltip" => t("Eelvaade"),
				"target" => "_blank",
				"url" => $url,
				"img" => "preview.gif",
			));

			$toolbar->add_separator();
		};
	}

	/**
		@attrib name=show params=name default="0"
		@param id required
	**/
	function show($args)
	{
		extract($args);
		$d = get_instance(CL_DOCUMENT);
		return $d->gen_preview(array("docid" => $args["id"]));
	}

	/** Returns array of addable document types
		@attrib api=1 params=pos

		@param parent required type=oid
			The object the add links point to

		@param period required type=int
			The period the add links point to

		@returns
			array { doc_type => array { name => type name , link => add link }, ... } for all doc types in the system. or a default type if none found
	**/
	function get_doc_add_menu($parent, $period)
	{
		$cfgforms = $this->get_cfgform_list();
		$retval = array();

		// can't use empty on function
		$def_cfgform = aw_ini_get("document.default_cfgform");
		if (empty($def_cfgform))
		{
			$retval["ng_doc"] = array(
				"name" => t("Dokument 2.0"),
				"link" => $this->mk_my_orb("new",array("parent" => $parent,"period" => $period),"doc"),
			);
		}

		foreach($cfgforms as $key => $val)
		{
			$retval["doc_$key"] = array(
				"name" => $val,
				"link" => $this->mk_my_orb("new",array("parent" => $parent,"period" => $period,"cfgform" => $key),"doc"),
			);
		}
		$retval["doc_brother"] = array(
			"name" => t("Dokument (vend)"),
			"link" => $this->mk_my_orb("new",array("parent" => $parent,"period" => $period),"document_brother"),
		);
		return $retval;
	}

	function callback_mod_retval($args = array()) //TODO:FIXME: siin on midagi valesti.
	{
		$request = &$args["request"];
		$new = $args["new"];
		$args = &$args["args"]; //FIXME: ...
		// if this is a new object, then the form is posted with the _top target
		// this ensures that the top toolbar will be updated as well
		if (!$new && $request["cb_part"])
		{
			$args["cb_part"] = $request["cb_part"];
		}

		if (!empty($request["no_rte"]))
		{
			$args["no_rte"] = 1;
		}

		if (aw_ini_get("config.object_versioning") == 1)
		{
			if (!empty($request["edit_version"]))
			{
				$out = array();
				if (strlen($request["edit_version"]) == 32)
				{
					$args["edit_version"] = $request["edit_version"];
				}
				else
				{
					parse_str($request["edit_version"], $out);
					$args["edit_version"] = $out["edit_version"];
				}
			}

			if ($request["create_new_version"] == 1 || $this->force_new_version)
			{
				// set edit version to new one
				$args["edit_version"] = $this->db_fetch_field("SELECT version_id FROM documents_versions ORDER BY vers_crea DESC LIMIT 1", "version_id");
				$_SESSION["vers_created"][$args["id"]] = $args["edit_version"];
			}
		}

		$args["period"] = $_POST["period"];
	}

	function callback_mod_reforb(&$args, $request)
	{
		if (!empty($_REQUEST["cb_part"]))
		{
			$args["cb_part"] = $_REQUEST["cb_part"];
		}
		$args["post_ru"] = post_ru();
		if (!empty($request["edit_version"]))
		{
			$args["edit_version"] = $request["edit_version"];
		}
		$args["temp_var1"] = " 0 ";
	}

	function on_save_document($params)
	{
		if (!aw_ini_get("document.save_act_docs"))
		{
			return;
		}

		$o = obj($params["oid"]);
		$period = $o->period();
		$oid = $o->id();

		// go over all menus that are parents of this document and mark this doc as active for them if it is active and not active if it is not.
		foreach($o->path() as $p_o)
		{
			if ($p_o->id() != $o->id())
			{
				$save = false;
				$docs = $p_o->meta("active_documents");
				$docs_p = $p_o->meta("active_documents_p");
				if ($o->status() == STAT_ACTIVE)
				{
					if ($period > 1)
					{
						if (!isset($docs_p[$period][$oid]))
						{
							$save = true;
						}
						$docs_p[$period][$oid] = $oid;
					}
					else
					{
						if (!isset($docs[$oid]))
						{
							$save = true;
						}
						$docs[$oid] = $oid;
					}
				}
				else
				{
					if ($period > 1)
					{
						if (isset($docs_p[$period][$oid]))
						{
							unset($docs_p[$period][$oid]);
							$save = true;
						}
					}
					else
					{
						if (isset($docs[$oid]))
						{
							$save = true;
						}
						unset($docs[$oid]);
					}
				}

				$p_o->set_meta("active_documents", $docs);
				$p_o->set_meta("active_documents_p", $docs_p);
				if ($save && $p_o->class_id() && $p_o->parent() && $this->can("edit", $p_o->id()))
				{
					$p_o->save();
				}
			}
		}
	}

	function on_add_doc_rel($arr)
	{
		if ($arr["connection"]->prop("reltype") != 22)
		{
			return;
		}

		// create reverse conn
		$other = $arr["connection"]->to();

		$other->connect(array(
			"to" => $arr["connection"]->prop("from"),
			"type" => "RELTYPE_LANG_REL"
		));
	}

	private function get_version_list($did)
	{
		$ret = array(aw_url_change_var("edit_version", NULL) => t("Aktiivne"));
		$this->db_query("SELECT version_id, vers_crea, vers_crea_by FROM documents_versions WHERE docid = '$did' order by vers_crea desc");
		$u = get_instance(CL_USER);
		while ($row = $this->db_next())
		{
			$pers = $u->get_person_for_uid($row["vers_crea_by"]);
			$ret[aw_url_change_var("edit_version", $row["version_id"])] = $pers->name()." ".date("d.m.Y H:i", $row["vers_crea"]);
		}
		return $ret;
	}

	function callback_on_load($p)
	{
		if (aw_ini_get("document.confirm_unsaved") == 1)
		{
			$GLOBALS["confirm_save_data"] = 1;
		}

		if (!empty($p["request"]["edit_version"]))
		{
			$out = array();
			parse_str($p["request"]["edit_version"], $out);
			if (!$out["id"] && $p["request"]["id"])
			{
				$o = obj($p["request"]["id"]);
				$o->load_version($p["request"]["edit_version"]);
			}
			else
			if ($out["edit_version"] != "")
			{
				$o = obj($p["request"]["id"]);
				$o->load_version($out["edit_version"]);
			}
		}
	}

	private function _init_versions_t(&$t)
	{
		$t->define_field(array(
			"name" => "ver",
			"caption" => t("Versioon"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "mod",
			"caption" => t("Muuda"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "rating",
			"caption" => t("Hinne"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "rate",
			"caption" => t("Hinda"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "active",
			"caption" => t("M&auml;&auml;ra aktiivseks"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "delete",
			"caption" => t("Vali"),
			"align" => "center"
		));
	}

	private function _versions($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_versions_t($t);

		$u = get_instance(CL_USER);
		$rs = get_instance(CL_RATE_SCALE);
		$my = $arr["obj_inst"]->modifiedby();


		if ($my != "")
		{
			$pers = $u->get_person_for_uid($my);
			$capt = $pers->name()." ".date("d.m.Y H:i", $arr["obj_inst"]->modified())." ".t("Aktiivne");
		}
		else
		{
			$capt = $my." ".date("d.m.Y H:i", $arr["obj_inst"]->modified())." ".t("Aktiivne");
		}
		$t->define_data(array(
			"ver" => html::href(array("target" => "_blank", "url" => obj_link($arr["obj_inst"]->id()), "caption" => $capt)),
			"active" => "",
			"delete" => "",
			"rating" => $u->get_rating($my),
			"rate" => html::select(array(
				"name" => "set_rating[".$row["version_id"]."]",
				"options" => array("" => t("--Vali--")) + $rs->_get_scale(aw_ini_get("config.object_rate_scale"))
			)),
			"mod" => html::href(array(
				"target" => "_blank",
				"url" => html::get_change_url($arr["obj_inst"]->id(), array("return_url" => get_ru())),
				"caption" => t("Muuda")
			)),
			"sby" => time() + 24*3600*100
		));
		$u = get_instance(CL_USER);
		$this->db_query("SELECT version_id, vers_crea, vers_crea_by FROM documents_versions WHERE docid = '".$arr["obj_inst"]->id()."'");
		while ($row = $this->db_next())
		{
			$pers = $u->get_person_for_uid($row["vers_crea_by"]);
			$capt = $pers->name()." ".date("d.m.Y H:i", $row["vers_crea"]);
			$t->define_data(array(
				"ver" => html::href(array("target" => "_blank", "url" => aw_url_change_var("docversion", $row["version_id"], obj_link($arr["obj_inst"]->id())), "caption" => $capt)),
				"active" => html::radiobutton(array(
					"name" => "set_act_ver",
					"value" => $row["version_id"]
				)),
				"delete" => html::checkbox(array(
					"name" => "del_version[]",
					"value" => $row["version_id"]
				)),
				"rating" => $u->get_rating($row["vers_crea_by"]),
				"rate" => html::select(array(
					"name" => "set_rating[".$row["version_id"]."]",
					"options" => array("" => t("--Vali--")) + $rs->_get_scale(aw_ini_get("config.object_rate_scale"))
				)),
				"mod" => html::href(array(
					"target" => "_blank",
					"url" => html::get_change_url($arr["obj_inst"]->id(), array("return_url" => get_ru(), "edit_version" => $row["version_id"])),
					"caption" => t("Muuda")
				)),
				"sby" => $row["vers_crea"]
			));
		}
		$t->set_default_sortby("sby");
		$t->set_default_sorder("desc");
	}

	private function _save_versions($arr)
	{
		$arr["obj_inst"]->set_no_modify(true);

		$o = obj($arr["request"]["id"]);
		if (!is_oid($arr["request"]["id"]))
		{
			return;
		}
		$o->load_version("");

		$u = get_instance(CL_USER);
		foreach(safe_array($arr["request"]["set_rating"]) as $version_id => $rating)
		{
			if ($rating !== "")
			{
				// get creator for version
				if ($version_id == "")
				{
					$u->add_rating($o->modifiedby(), $rating);
				}
				else
				{
					$crea = $this->db_fetch_field("SELECT vers_crea_by FROM documents_versions WHERE docid = '".$o->id()."' AND version_id = '$version_id'", "vers_crea_by");
					if ($crea)
					{
						$u->add_rating($crea, $rating);
					}
				}
			}
		}

		// set active
		// copy from _versions table do real table and flush cache
		if ($arr["request"]["set_act_ver"] != "")
		{
			$sav = $arr["request"]["set_act_ver"];
			$this->quote($sav);
			$data = $this->db_fetch_row("SELECT * FROM documents_versions WHERE docid = '".$arr["obj_inst"]->id()."' AND version_id = '$sav'");
			if ($data)
			{
				// switch old and new versions
				$old_o = $this->db_fetch_row("SELECT * FROM objects WHERE oid = '".$arr["obj_inst"]->id()."'");
				$old_d = $this->db_fetch_row("SELECT * FROM documents WHERE docid = '".$arr["obj_inst"]->id()."'");

				// write old version to versions table as new version
				$o->set_no_modify(true);
				$o->set_create_new_version();
				$o->save();
				// update the modified date and modifier to point to the old modifier, because it really is HIS version
				$new_ver = $this->db_fetch_field("SELECT version_id FROM documents_versions ORDER BY vers_crea DESC LIMIT 1", "version_id");
				$this->db_query("UPDATE documents_versions SET vers_crea = $old_o[modified], vers_crea_by = '$old_o[modifiedby]' WHERE version_id = '$new_ver'");


				// write version to objtable
				$this->quote($data);
				$id = $arr["obj_inst"]->id();
				$this->db_query("DESCRIBE documents");
				$sets = array();
				while ($row = $this->db_next())
				{
					$sets[$row["Field"]] = $data[$row["Field"]];
				}
				$q = "UPDATE objects SET name = '$data[title]',modified = '$data[vers_crea]', modifiedby = '$data[vers_crea_by]'  WHERE oid = $id";
				$this->db_query($q);
				$q = "UPDATE documents SET ".join(",", map2("`%s` = '%s'", $sets))."  WHERE docid = $id";
				$this->db_query($q);
				$this->db_query("DELETE FROM documents_versions WHERE docid = '".$arr["obj_inst"]->id()."' AND version_id = '$sav'");


				$c = get_instance("cache");
				$c->file_clear_pt("storage_object_data");
				$c->file_clear_pt("storage_search");
				$c->file_clear_pt("html");
			}
		}
	}

	private function _versions_tb($arr)
	{
		$toolbar = &$arr["prop"]["vcl_inst"];
		$toolbar->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"action" => "delete_versions",
			"img" => "delete.gif",
		));
		$toolbar->closed = 1;
	}

	/**
		@attrib name=set_act_ver
		@param docid required type=int
		@param ver required
		@param retu required
	**/
	function set_act_ver($arr)
	{
		$o = obj($arr["docid"]);
		$this->_save_versions(array(
			"obj_inst" => $o,
			"request" => array(
				"id" => $arr["docid"],
				"set_act_ver" => $arr["ver"]
			)
		));
		return $arr["retu"];
	}

	private function kw_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$ol = new object_list(array(
			"class_id" => CL_KEYWORD
		));
		$parents = array();
		foreach($ol->arr() as $kw)
		{
			$parents[$kw->parent()] = $kw->parent();
		}

		if (count($parents) > 1)
		{
			$tb->add_menu_button(array(
				"name" => "new",
				"img" => "new.gif"
			));
			foreach($parents as $pt_id)
			{
				$po = obj($pt_id);
				$tb->add_menu_item(array(
					"parent" => "new",
					"text" => $po->name(),
					"link" => html::get_new_url(CL_KEYWORD, $pt_id, array("return_url" => get_ru()))
				));
			}
		}
		else
		{
			$pt = $arr["obj_inst"]->id();
			if (aw_ini_get("config.keyword_folder"))
			{
				$pt = aw_ini_get("config.keyword_folder");
			}
			if (count($parents) == 1)
			{
				$pt = reset($parents);
			}
			$tb->add_button(array(
				"name" => "new_kw",
				"tooltip" => t("V&otilde;tmes&otilde;na"),
				"url" => html::get_new_url(CL_KEYWORD, $pt, array("return_url" => get_ru())),
				"img" => "new.gif",
			));
		}
		$tb->closed = 1;
	}

	/**
		@attrib name=delete_versions
	**/
	function delete_versions($arr)
	{
		// delete selected
		$o = obj($arr["id"]);
		foreach(safe_array($arr["del_version"]) as $v)
		{
			$this->quote($v);
			$this->db_query("DELETE FROM documents_versions WHERE docid = '".$o->id()."' AND version_id = '$v'");
		}

		return $arr["post_ru"];
	}

	/**
	@attrib name=delete_comments
	**/
	function delete_comments($arr)
	{
		if($this->can("edit", $arr["id"]))
		{
			foreach($arr["sel"] as $oid)
			{
				$q = sprintf("DELETE FROM comments WHERE board_id = '%s' AND id='%s'", mysql_real_escape_string($arr["id"]), mysql_real_escape_string($oid));
				$this->db_query($q);
			}
		}
		return $arr["post_ru"];
	}

	function callback_get_transl($arr)
	{
		$pl = $arr["obj_inst"]->get_property_list();
		$cfgform_id = $this->get_cfgform_for_object(array(
			"obj_inst" => $arr["obj_inst"],
			"args" => $arr["request"],
		));
		if ($this->can("view", $cfgform_id))
		{
			$cf = get_instance(CL_CFGFORM);
			$pl = $cf->get_props_from_cfgform(array("id" => $cfgform_id));
		}

		$val = '<div id="floatlayerk" style="left: 800px; top: 200px; width: 250px; border:solid black 1px;padding:5px; background: #dddddd;overflow: -moz-scrollbars-vertical;">'.
			("<b>".iconv(aw_global_get("charset"), "UTF-8", $pl["title"]["caption"]).":</b>")." ".iconv(aw_global_get("charset"), "UTF-8", $arr["obj_inst"]->prop("title"))."<br><br>".
			("<b>".iconv(aw_global_get("charset"), "UTF-8", $pl["lead"]["caption"]).":</b>")." ".iconv(aw_global_get("charset"), "UTF-8", $arr["obj_inst"]->prop("lead"))."<br><br>".
			("<b>".iconv(aw_global_get("charset"), "UTF-8", $pl["content"]["caption"]).":</b>")." ".iconv(aw_global_get("charset"), "UTF-8", $arr["obj_inst"]->prop("content")).
			'</div>';
		$val .= '<script language="javascript">el=document.getElementById(\'floatlayerk\');if (el) {el.style.position=\'absolute\';el.style.left=800;el.style.top=200;}</script>';


		$rv = $this->trans_callback($arr, $this->trans_props, array("user1", "user3", "user5", "userta2", "userta3", "userta4", "userta5", "userta6"));
		$rvv = array();
		$nm = "origt";
		$rvv[$nm] = array(
			"name" => $nm,
			"no_caption" => 1,
			"type" => "text",
			"value" => $val,
		);

		return $rvv + $rv;
	}

	private function _trans_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "preview",
			"tooltip" => t("Eelvaade"),
			"img" => "preview.gif"
		));
		$l = get_instance("languages");
		$ll = $l->get_list(array(/*"ignore_status" => true,*/ "all_data" => true));

		$dd = get_instance("doc_display");
		foreach($ll as $lid => $lang)
		{
			if ($lid == $arr["obj_inst"]->lang_id())
			{
				continue;
			}
			$tb->add_menu_item(array(
				"parent" => "preview",
				"text" => t($lang["name"]),
				"url" => $dd->get_doc_link($arr["obj_inst"], $lang["acceptlang"]),
				"target" => "_blank"
			));
		}
	}

	private function _comments_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "delete_comment",
			"action" => "delete_comments",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta valitud kommentaarid"),
			"confirm" => t("Oled kindel, et soovid valitud kustutada?"),
		));
	}

	private function _comments_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "content",
			"caption" => t("Sisu"),
		));

		$q = sprintf("SELECT * FROM comments WHERE board_id = '%s' ORDER BY time", $arr["obj_inst"]->id());;
		$rows = $this->db_fetch_array($q);
		foreach($rows as $row)
		{
			$t->define_data(array(
				"oid" => $row["id"],
				"name" => $row["name"],
				"content" => nl2br(htmlspecialchars(stripslashes($row['comment']))),
			));
		}
	}

	function callback_mod_tab($arr)
	{
		if (!empty($_GET["edit_version"]))
		{
			$arr["link"] = aw_url_change_var("edit_version", $_GET["edit_version"], $arr["link"]);
		}
	}

	private function funnel_ct_content($arr)
	{
		// check table
		$this->db_last_error = false;
		$this->db_query("select oid FROM doc_ct_content LIMIT 1",false);
		if ($this->db_last_error !== false)
		{
			$this->db_query("CREATE TABLE IF NOT EXISTS doc_ct_content(oid int, parent int, lang_id int, site_id int, title varchar(255), lead mediumtext, content mediumtext,tm int, modified int, user1 text, user4 text, author varchar(255), photos varchar(255), dcache text, no_search int)");
			$this->db_query(" alter table doc_ct_content add index oid(oid)");
			$this->db_query("alter table doc_ct_content add index lang_id(lang_id)");
			$this->_conv_ct_docs();
		}
		// basically, create a table
		// that holds doc contents in all languages
		// so that site_search_content
		// can search in other languages as well
		$id = $arr["obj_inst"]->id();
		foreach(safe_array($arr["obj_inst"]->meta("translations")) as $lid => $props)
		{
			$this->quote($props);
			if ($this->db_fetch_field("select oid FROM doc_ct_content WHERE oid = '$id' AND lang_id ='$lid'", "oid"))
			{
				$this->db_query("UPDATE doc_ct_content SET
					parent = '".$arr["obj_inst"]->parent()."',
					title = '".$props["title"]."',
					lead = '".$props["lead"]."',
					content = '".$props["content"]."',
					tm = '".$arr["obj_inst"]->prop("tm")."',
					modified = '".$arr["obj_inst"]->prop("doc_modified")."',
					user1 = '".$arr["obj_inst"]->prop("user1")."',
					user4 = '".$arr["obj_inst"]->prop("user4")."',
					author = '".$arr["obj_inst"]->prop("author")."',
					photos = '".$arr["obj_inst"]->prop("photos")."',
					dcache = '".$arr["obj_inst"]->prop("dcache")."',
					no_search = '".$arr["obj_inst"]->prop("no_search")."'
					WHERE oid = '$id' AND lang_id ='$lid'");
			}
			else
			{
				$this->db_query("INSERT INTO doc_ct_content(oid,parent,lang_id,site_id,title,lead,content,
					tm,modified,user1,user4,author,photos,dcache,no_search)
					VALUES($id,
						'".$arr["obj_inst"]->parent()."',
						$lid,
						'".$arr["obj_inst"]->site_id()."',
						'".$props["title"]."',
						'".$props["lead"]."',
						'".$props["content"]."',
						'".$arr["obj_inst"]->prop("tm")."',
						'".$arr["obj_inst"]->prop("doc_modified")."',
						'".$arr["obj_inst"]->prop("user1")."',
						'".$arr["obj_inst"]->prop("user4")."',
						'".$arr["obj_inst"]->prop("author")."',
						'".$arr["obj_inst"]->prop("photos")."',
						'".$arr["obj_inst"]->prop("dcache")."',
						'".$arr["obj_inst"]->prop("no_search")."'
					)");
			}
		}
	}

	private function _conv_ct_docs()
	{
		$ol = new object_list(array(
			"class_id" => CL_DOCUMENT
		));
		foreach($ol->arr() as $o)
		{
			echo "o = ".$o->name()." (".$o->id().") <br>\n";
			flush();
			$this->funnel_ct_content(array("obj_inst" => $o));
		}
		die("all done");
	}

	/**
		@attrib name=get_fckstyles_path
	**/
	function get_fckstyles_path()
	{
		if (aw_ini_get("document.site_fck_styles"))
		{
			die('/css/fckstyles.xml');
		}
		else
		{
			die('/automatweb/js/fckeditor/fckstyles.xml');
		}
	}

	/**
                @attrib name=get_fckstyles_css
        **/
        function get_fckstyles_css()
        {
                if (aw_ini_get("document.site_fck_styles"))
                {
                        die('/css/stiil.css');
                }
                else
                {
                        die('/automatweb/js/fckeditor/fck_editorarea.css');
                }
        }

	/* This is in document.aw
	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
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
			case "show_to_country":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(255)"
				));
			return true;
		}
	}
	*/

	private function _get_simultaneous_key($o)
	{
		return "document_editor_open_".$o->id();
	}

	private function _get_simultaneous_warning($arr)
	{
		if ($arr["new"])
		{
			return PROP_IGNORE;
		}

		$key = $this->_get_simultaneous_key($arr["obj_inst"]);

		// check if someone is editing this doc
		// if so, then check if the edit open date is longer than the session timeout
		// if it is, then no editing, else warning
		$data = aw_unserialize($this->get_cval($key));
		if (!is_array($data))
		{
			return PROP_IGNORE;
		}

		$mod = false;
		foreach($data as $uid => $d)
		{
			if ($d["tm"] < (time() - ini_get("session.gc_maxlifetime")))
			{
				unset($data[$uid]);
				$mod = true;
			}
		}

		if ($mod)
		{
			$this->set_cval($key, serialize($data));
		}

		unset($data[aw_global_get("uid")]);

		if (!count($data))
		{
			return PROP_IGNORE;
		}

		if (count($data) == 1)
		{
			$data = reset($data);
			$arr["prop"]["value"] = html::strong(sprintf(t("Hoiatus, seda dokumenti muudab hetkel kasutaja %s, muutmisvaate avas %s"),
				obj($data["person"])->name,
				date("d.m.Y H:i:s", $data["tm"])
			));
		}
		else
		{
			$str = t("Hoiatus, seda dokumenti muudavad hetkel kasutajad:");
			foreach($data as $entry)
			{
				$str .= " ".(obj($entry["person"])->name)." ".t("avas muutmisvaate")." ".date("d.m.Y H:i:s", $entry["tm"]);
			}
			$arr["prop"]["value"] = $str;
		}
		return PROP_OK;
	}

	function callback_pre_edit($arr)
	{
		if (is_oid($arr["obj_inst"]->id()))
		{
			$key = $this->_get_simultaneous_key($arr["obj_inst"]);
			$data = aw_unserialize($this->get_cval($key));

			$data[aw_global_get("uid")] = array(
				"tm" => time(),
				"uid" => aw_global_get("uid_oid"),
				"person" => get_current_person()->id()
			);
			$this->set_cval($key, serialize($data));
		}
	}

	/**
		@attrib name=mark_leave_editor nologin=1
		@param oid optional
	**/
	function mark_leave_editor($arr)
	{
		if (!is_oid($arr["oid"]))
		{
			die();
		}

		$key = $this->_get_simultaneous_key(obj($arr["oid"]));

		$data = unserialize($this->get_cval($key));

		if (isset($data[aw_global_get("uid")]))
		{
			unset($data[aw_global_get("uid")]);

			$this->set_cval($key, serialize($data));
		}

		die();
	}

	function callback_generate_scripts($arr)
	{
$rv = <<<EOF
$(window).unload( function () {
	awDocUnloadHandler ();
});
function awDocUnloadHandler()
{
	$.ajax({
		type: "GET",
		url: "orb.aw?class=doc&action=mark_leave_editor",
		data: "&oid="+$.gup("id"),
		async: false,
		success: function(msg){
		},
		error: function(msg){
			//alert( "{VAR:msg_leave_error}");
		}
	});
}
EOF;
		return $rv;
	}
}
