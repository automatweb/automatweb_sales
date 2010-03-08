<?php
// $Header: /home/cvs/automatweb_dev/classes/applications/clients/expp/expp_journal_management.aw,v 1.29 2007/11/23 07:03:30 dragut Exp $
// expp_journal_management.aw - V&auml;ljaannete haldus 
/*

@classinfo syslog_type=ST_EXPP_JOURNAL_MANAGEMENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

	@property code type=textbox field=comment
	@caption Kood

@groupinfo organisation_general_information caption="Ettev&otilde;tte &uuml;ldandmed"
@default group=organisation_general_information

	@property organisation_logo type=text
	@caption Organisatsiooni logo
	@comment Organisatsiooni logo, saab muuta organisatsiooni juures

	@property organisation_link type=text
	@caption Organisatsioon

	@property show_organisations_publications type=checkbox ch_value=1 field=meta method=serialize
	@caption Kuva selle v&auml;ljaandja teisi v&auml;ljaandeid

@groupinfo publications caption="V&auml;ljaanded"
@default group=publications

	@groupinfo publications_general_info caption="V&auml;ljaannete &uuml;ldinfo" parent=publications
	@default group=publications_general_info

		@property publications_name type=textbox field=meta method=serialize 
		@caption V&auml;ljaande nimi

		@property publications_description type=textbox field=meta method=serialize
		@caption V&auml;ljaande kirjeldus

		@property publications_homepage type=relpicker reltype=RELTYPE_PUBLICATION_HOMEPAGE field=meta method=serialize
		@caption V&auml;ljaande koduleht

		property ordering_terms type=relpicker reltype=RELTYPE_ORDERING_TERMS field=meta method=serialize
		caption Tellimistingimused

		property ordering_terms_rus type=relpicker reltype=RELTYPE_ORDERING_TERMS field=meta method=serialize
		caption Tellimistingimused (vene keeles)

		@property ordering_terms type=callback callback=callback_get_ordering_terms field=meta method=serialize
		@caption Tellimistingimused

		@property order_composition_information type=textarea field=meta method=serialize
		@caption Tellimuse vormistamise informatsioon

		@property design_image type=releditor use_form=emb reltype=RELTYPE_DESIGN_IMAGE rel_id=first field=meta method=serialize
		@caption Logo

		@property cover_image type=releditor use_form=emb reltype=RELTYPE_COVER_IMAGE rel_id=first field=meta method=serialize
		@caption Esikaane pilt

		@property frame_color type=textbox field=meta method=serialize
		@caption Raami toon

		@property text_color type=textbox field=meta method=serialize
		@caption Teksti v&auml;rv

		@property main_color type=textbox field=meta method=serialize
		@caption P&otilde;hitoon
		
		@property choose_design type=chooser field=meta method=serialize
		@caption Kujundusp&otilde;hi

		@property custom_design_document type=relpicker reltype=RELTYPE_GENERAL_DOCUMENT field=meta method=serialize
		@caption Dokument

	@groupinfo publications_list caption="Alamv&auml;ljaanded" parent=publications
	@default group=publications_list

		@property publications_toolbar type=toolbar no_caption=1
		@caption Alamv&auml;ljaannete t&ouml;&ouml;riistariba

		@property publications_table type=table no_caption=1
		@caption Almav&auml;ljaanded

	@groupinfo general_images caption="Pildid" parent=publications
	@default group=general_images

		@property general_images_toolbar type=toolbar no_caption=1
		@caption Piltide t&ouml;&ouml;riistariba

		@property general_images_table type=table no_caption=1
		@caption Pildid

	@groupinfo general_files caption="Failid" parent=publications
	@default group=general_files
	
		@property general_files_toolbar type=toolbar no_caption=1
		@caption Failide t&ouml;&ouml;riistariba

		@property general_files_table type=table no_caption=1
		@caption Failid

	@groupinfo general_links caption="Lingid" parent=publications
	@default group=general_links

		@property general_links_toolbar type=toolbar no_caption=1
		@caption Linkide t&ouml;&ouml;riistariba

		@property general_links_table type=table no_caption=1
		@caption Lingid

        @groupinfo general_documents caption="Dokumendid" parent=publications
        @default group=general_documents

		@property general_documents_toolbar type=toolbar no_caption=1
		@caption Dokumentide t&ouml;&ouml;riistariba

		@property general_documents_table type=table no_caption=1
		@caption Dokumendid

	@groupinfo general_polls caption="Kiirk&uuml;sitlused" parent=publications
	@default group=general_polls

		@property general_polls_toolbar type=toolbar no_caption=1
		@caption Kiirk&uuml;sitluste t&ouml;&ouml;riistariba

		@property general_polls_table type=table no_caption=1
		@caption Kiirk&uumlsitlused

	@groupinfo general_webforms caption="Veebivormid" parent=publications
	@default group=general_webforms

		@property general_webforms_toolbar type=toolbar no_caption=1
		@caption Veebivormide t&ouml;&ouml;riistariba

		@property general_webforms_table type=table no_caption=1
		@caption Veebivormid

	@groupinfo general_forum caption="Foorum" parent=publications
	@default group=general_forum

		@property general_forum type=text  
		@caption Foorum

@groupinfo stats caption="Statistika"
@default group=stats

	@property stats type=text
	@caption Statistika

@groupinfo transl caption="T&otilde;lkimine"
@default group=transl
	
	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi	
	
@reltype ORGANISATION value=1 clid=CL_CRM_COMPANY
@caption Organisatsioon

@reltype DESIGN_IMAGE value=2 clid=CL_IMAGE
@caption Kujunduse pilt

@reltype COVER_IMAGE value=3 clid=CL_IMAGE
@caption Esikaane pilt

@reltype CRM_SECTION value=4 clid=CL_CRM_SECTION
@caption &Uuml;ksus/Toode

@reltype PUBLICATION value=5 clid=CL_EXPP_PUBLICATION,CL_CRM_SECTION
@caption V&auml;ljaanne

@reltype PUBLICATION_IMAGE value=6 clid=CL_IMAGE
@caption V&auml;ljaande pilt

@reltype GENERAL_MINI_GALLERY value=7 clid=CL_MINI_GALLERY
@caption &Uuml;ldine minigalerii

@reltype GENERAL_FILE value=8 clid=CL_FILE
@caption &Uuml;ldine fail

@reltype GENERAL_LINK value=9 clid=CL_EXTLINK
@caption &Uuml;ldine link

@reltype GENERAL_IMAGE value=10 clid=CL_IMAGE
@caption &Uuml;ldine pilt 

@reltype GENERAL_FORUM value=11 clid=CL_FORUM_V2
@caption &Uuml;ldine foorum

@reltype GENERAL_WEBFORM value=12 clid=CL_WEBFORM
@caption &Uuml;ldine veebivorm

@reltype GENERAL_POLL value=13 clid=CL_POLL
@caption &Uuml;ldine kiirk&uuml;sitlus

@reltype GENERAL_DOCUMENT value=14 clid=CL_DOCUMENT
@caption Dokument

@reltype PUBLICATION_HOMEPAGE value=15 clid=CL_EXTLINK
@caption V&auml;ljaandja koduleht

@reltype ORDERING_TERMS value=16 clid=CL_DOCUMENT
@caption Tellimistingimused
*/

class expp_journal_management extends class_base
{
	function expp_journal_management()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "applications/clients/expp/expp_journal_management",
			"clid" => CL_EXPP_JOURNAL_MANAGEMENT
		));

		// translation:
		$this->trans_props = array(
			'name',
			'order_composition_information'	
		);
	}

	//////
	// class_base classes usually need those, uncomment them if you want to use them
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "text_color":
				if (empty($prop['value']))
				{
					$prop['value'] = "#000000";
				}
				break;
			case "choose_design":
				$prop['options'] = array(
					"default_design" => t("Kasutan etteantud p&otilde;hja"),
					"custom_design" => t("Soovin ise kujundada"),
				);
				if (empty($prop['value']))
				{
					$prop['value'] = 'default_design';
				}
				break;
			case "custom_design_document":
				$choose_design = $arr['obj_inst']->prop("choose_design");
				if ($choose_design == "default_design" || empty($choose_design))
				{
					$retval = PROP_IGNORE;
				}
				else
				{
					// have to check if there is any documents connected:
					$connections_to_general_documents = $arr['obj_inst']->connections_from(array(
						"type" => "RELTYPE_GENERAL_DOCUMENT",	
					));
					if (count($connections_to_general_documents) <= 0)
					{
						$new_document = new object();
						$new_document->set_class_id(CL_DOCUMENT);
						$new_document->set_parent($arr['obj_inst']->id());
						$new_document->set_name($arr['obj_inst']->name()." kujundus");
						$new_document->set_prop("content", "#site#");
						$new_document->save();
						$arr['obj_inst']->connect(array(
							"to" => $new_document,
							"type" => "RELTYPE_GENERAL_DOCUMENT",
						));
						$prop['options'][$new_document->id()] = $new_document->name();
					}
				}
				break;
			case "publications_name":
				$prop['value'] = $arr['obj_inst']->meta("publications_name_value");
				$prop['type'] = "text";
			case "publications_description":
				$prop['type'] = "text";
			//	$prop['value'] = t("V&auml;&auml;rtus tuleb Reggy-st, ei ole v&otilde;imalik muuta");
				break;
			case "stats":
				$prop['value'] = t("Siia tuleb statistika");
				break;

		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "organisation":
			case "design_image":
			case "cover_image":
			case "publications_table":
			case "general_images":
			case "general_files":
			case "general_links":
			case "general_documents":
			case "general_polls":
			case "general_webform":
			case "publications_homepage":
				$prop['obj_parent'] = $arr['obj_inst']->id();
				break;
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

        function callback_post_save($arr)
        {
                $cache_inst = get_instance("cache");
                $cache_inst->file_invalidate($arr['obj_inst']->prop("code").".cache");
        }

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function callback_get_ordering_terms($arr)
	{
		$ret = array();
		$lang_inst = get_instance("languages");
		$lang_list = $lang_inst->get_list();
		$meta = $arr["obj_inst"]->meta();

		foreach($lang_list as $lang_id => $lang_name)
		{

			$ret["ordering_terms[$lang_id]"] = array(
				"name" => "ordering_terms[$lang_id]",
				"type" => "relpicker",
				"group" => "publications_general_info",
				"table" => "objects",
				"field" => "meta",
				"method" => "serialize",
				"caption" => sprintf(t("Tellimistingimused (%s)"), $lang_name),
				"value" => $meta["ordering_terms"][$lang_id],
				"reltype" => "RELTYPE_ORDERING_TERMS",
				'store' => 'no'
			);
		}		
		return $ret;
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

// i use it to make any kind of conversions or some other custom stuff, can be deleted once the expp system
// is up and running
	function _get_code($arr)
	{
		if ($_GET['dragut'])
		{
	//		$arr['prop']['value'] = str_replace("%", "#", urlencode($arr['obj_inst']->name()));
	//		$arr['obj_inst']->set_meta("publications_name_value", "test nimi");
	//		$arr['obj_inst']->save();
	//		arr($arr['obj_inst']->prop("publications_name"));
			
			
		}

	}


	function _get_organisation_logo($arr)
	{
		
		$organisation_logo_id = $arr['obj_inst']->meta("organisation_logo_id");
		if (!empty($organisation_logo_id))
		{
			$image_inst = get_instance(CL_IMAGE);
			$arr['prop']['value'] = $image_inst->make_img_tag_wl($organisation_logo_id);

		}
		else
		{
			$arr['prop']['value'] = t("Organisatsiooni juurde ei ole logo m&auml;&auml;ratud");
		}
	}

	/**
		If there is organisation object connected to this object, then check, if the
		organisation object has logo set or not. If it does, then i save the organisations
		logo image object id to meta field, so i can display it.
	**/
	function _get_organisation_link($arr)
	{
		$organisation_object = $arr['obj_inst']->get_first_obj_by_reltype("RELTYPE_ORGANISATION");
		if (!empty($organisation_object))
		{
			$organisation_object_id = $organisation_object->id();
		}
		if ($this->can("view", $organisation_object_id))
		{
			$arr['prop']['value'] = html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $organisation_object_id,
					"return_url" => get_ru(),
				), CL_CRM_COMPANY),
				"caption" => t("Muuda organisatsiooni andmeid"),
			));

//// this one doesn't work in some reason :/
//			$organisation_logo = $organisation_object->get_first_obj_by_reltype("RELTYPE_ORGANISATION_LOGO");
//			var_dump($organisation_logo);

			$organisation_logo_connections = $organisation_object->connections_from(array(
				"type" => 45 // crm_company.logo (organisation's logo)
			));
			
			if (!empty($organisation_logo_connections))
			{
				// get the first connection:
				$organisation_logo_connection  = reset($organisation_logo_connections);
				$organisation_logo_id = $organisation_logo_connection->prop("to");

				$arr['obj_inst']->set_meta("organisation_logo_id", $organisation_logo_id);
				$arr['obj_inst']->save();
			}
		}
		else
		{
			$arr['prop']['value'] = html::href(array(
				"url" => $this->mk_my_orb("new", array(
					"alias_to" => $arr['obj_inst']->id(),
					"parent" => $arr['obj_inst']->id(),
					"reltype" => 1, // expp_journal_management.organisation
					"return_url" => get_ru(),
				), CL_CRM_COMPANY),
				"caption" => t("Lisa organisatsioon"),
			));
		}

		return PROP_OK;
	}

	function _get_publications_toolbar($arr)
	{
		$t = &$arr['prop']['toolbar'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus v&auml;ljaanne"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr['obj_inst']->id(),
				"parent" => $arr['obj_inst']->id(),
				"reltype" => 5, // expp_journal_management.publication
				"return_url" => get_ru(),
			), CL_EXPP_PUBLICATION),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "_delete_objects",
			"confirm" => t("Oled kindel, et soovid valitud v&auml;ljaanded kustutada?"),
		));

		return PROP_OK;
	}

	function _get_publications_table($arr)
	{

		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			"name" => "publication_id",
			"caption" => t("ID"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Aktiivsus"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center",
			"width" => "5%",
		));
		$connections_to_publications = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_PUBLICATION",
		));
		foreach ($connections_to_publications as $connection_to_publication)
		{
			$publication_id = $connection_to_publication->prop("to");
			$publication_object = $connection_to_publication->to();
			$t->define_data(array(
				"publication_id" => $publication_id,
				"name" => $connection_to_publication->prop("to.name"),
				"status" => html::checkbox(array(
					"name" => "status[".$publication_id."]",
					"value" => $publication_id,
					"checked" => ($publication_object->status() == STAT_ACTIVE) ? true : false,
				)),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $publication_id,
						"return_url" => get_ru(),
						), CL_EXPP_PUBLICATION),
					"caption" => t("Muuda"),
				)),
				"select" => html::checkbox(array(
					"name" => "selected_ids[".$publication_id."]",
					"value" => $publication_id,
				)),
			));
		}

		return PROP_OK;
	}

	function _set_publications_table($arr)
	{
		$connections_to_publications = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_PUBLICATION",
		));
		foreach ($connections_to_publications as $connection_to_publication)
		{
			$publication_id = $connection_to_publication->prop("to");
			if (is_oid($publication_id) && $this->can("edit", $publication_id))
			{
				$publication_object = new object($publication_id);
				if (in_array($publication_id, $arr['request']['status']))
				{
					$publication_object->set_status(STAT_ACTIVE);
				}
				else
				{
					$publication_object->set_status(STAT_NOTACTIVE);
				}
				$publication_object->save();
			}
		}
		return PROP_OK;
	}

	function _get_general_images_toolbar($arr)
	{
		$t = &$arr['prop']['toolbar'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus pilt"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr['obj_inst']->id(),
				"parent" => $arr['obj_inst']->id(),
				"reltype" => 10, // expp_journal_management.general_image
				"return_url" => get_ru(),
			), CL_IMAGE),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "_delete_objects",
			"confirm" => t("Oled kindel, et soovid valitud pildid kustutada?"),
		));

		return PROP_OK;
	}

	function _get_general_images_table($arr)
	{

		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			"name" => "image_id",
			"caption" => t("ID"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Aktiivsus"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "order",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center",
			"width" => "5%",
		));
		$connections_to_images = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_IMAGE",
		));
		foreach ($connections_to_images as $connection_to_image)
		{
			$image_id = $connection_to_image->prop("to");
			$image_object = $connection_to_image->to();
			$t->define_data(array(
				"image_id" => $image_id,
				"name" => $connection_to_image->prop("to.name"),
				"status" => html::checkbox(array(
					"name" => "status[".$image_id."]",
					"value" => $image_id,
					"checked" => ($image_object->status() == STAT_ACTIVE) ? true : false,
				)),
				"order" => html::textbox(array(
					"name" => "order[".$image_id."]",
					"value" => $image_object->ord(),
					"size" => 3
				)),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $image_id,
						"return_url" => get_ru(),
						), CL_IMAGE),
					"caption" => t("Muuda"),
				)),
				"select" => html::checkbox(array(
					"name" => "selected_ids[".$image_id."]",
					"value" => $image_id,
				)),
			));
		}

		return PROP_OK;
	}

	function _set_general_images_table($arr)
	{
		$connections_to_images = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_IMAGE",
		));
		foreach ($connections_to_images as $connection_to_image)
		{
			$image_id = $connection_to_image->prop("to");
			if (is_oid($image_id) && $this->can("edit", $image_id))
			{
				$image_object = new object($image_id);
				if (in_array($image_id, $arr['request']['status']))
				{
					$image_object->set_status(STAT_ACTIVE);
				}
				else
				{
					$image_object->set_status(STAT_NOTACTIVE);
				}
				$image_object->set_ord((int)$arr['request']['order'][$image_id]);
				$image_object->save();
			}
		}
		return PROP_OK;
	}

	function _get_general_files_toolbar($arr)
	{
		$t = &$arr['prop']['toolbar'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus fail"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr['obj_inst']->id(),
				"parent" => $arr['obj_inst']->id(),
				"reltype" => 8, // expp_journal_management.general_file
				"return_url" => get_ru(),
			), CL_FILE),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "_delete_objects",
			"confirm" => t("Oled kindel, et soovid valitud failid kustutada?"),
		));

		return PROP_OK;
	}

	function _get_general_files_table($arr)
	{

		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			"name" => "file_id",
			"caption" => t("ID"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Aktiivsus"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "order",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center",
			"width" => "5%",
		));
		$connections_to_files = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_FILE",
		));
		foreach ($connections_to_files as $connection_to_file)
		{
			$file_id = $connection_to_file->prop("to");
			$file_object = $connection_to_file->to();
			$t->define_data(array(
				"file_id" => $file_id,
				"name" => $connection_to_file->prop("to.name"),
				"status" => html::checkbox(array(
					"name" => "status[".$file_id."]",
					"value" => $file_id,
					"checked" => ($file_object->status() == STAT_ACTIVE) ? true : false,
				)),
				"order" => html::textbox(array(
					"name" => "order[".$file_id."]",
					"value" => $file_object->ord(),
					"size" => 3
				)),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $file_id,
						"return_url" => get_ru(),
						), CL_FILE),
					"caption" => t("Muuda"),
				)),
				"select" => html::checkbox(array(
					"name" => "selected_ids[".$file_id."]",
					"value" => $file_id,
				)),
			));
		}

		return PROP_OK;
	}

	function _set_general_files_table($arr)
	{
		$connections_to_files = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_FILE",
		));
		foreach ($connections_to_files as $connection_to_file)
		{
			$file_id = $connection_to_file->prop("to");
			if (is_oid($file_id) && $this->can("edit", $file_id))
			{
				$file_object = new object($file_id);
				if (in_array($file_id, $arr['request']['status']))
				{
					$file_object->set_status(STAT_ACTIVE);
				}
				else
				{
					$file_object->set_status(STAT_NOTACTIVE);
				}
				$file_object->set_ord((int)$arr['request']['order'][$file_id]);
				$file_object->save();
			}
		}
		return PROP_OK;
	}

	function _get_general_links_toolbar($arr)
	{
		$t = &$arr['prop']['toolbar'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus link"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr['obj_inst']->id(),
				"parent" => $arr['obj_inst']->id(),
				"reltype" => 9, // expp_journal_management.general_link
				"return_url" => get_ru(),
			), CL_EXTLINK),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "_delete_objects",
			"confirm" => t("Oled kindel, et soovid valitud lingid kustutada?"),
		));

		return PROP_OK;
	}

	function _get_general_links_table($arr)
	{

		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			"name" => "link_id",
			"caption" => t("ID"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Aktiivsus"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "order",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center",
			"width" => "5%",
		));
		$connections_to_links = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_LINK",
		));
		foreach ($connections_to_links as $connection_to_link)
		{
			$link_id = $connection_to_link->prop("to");
			$link_object = $connection_to_link->to();
			$t->define_data(array(
				"link_id" => $link_id,
				"name" => $connection_to_link->prop("to.name"),
				"status" => html::checkbox(array(
					"name" => "status[".$link_id."]",
					"value" => $link_id,
					"checked" => ($link_object->status() == STAT_ACTIVE) ? true : false,
				)),
				"order" => html::textbox(array(
					"name" => "order[".$link_id."]",
					"value" => $link_object->ord(),
					"size" => 3
				)),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $link_id,
						"return_url" => get_ru(),
						), CL_EXTLINK),
					"caption" => t("Muuda"),
				)),
				"select" => html::checkbox(array(
					"name" => "selected_ids[".$link_id."]",
					"value" => $link_id,
				)),
			));
		}

		return PROP_OK;
	}

	function _set_general_links_table($arr)
	{
		$connections_to_links = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_LINK",
		));
		foreach ($connections_to_links as $connection_to_link)
		{
			$link_id = $connection_to_link->prop("to");
			if (is_oid($link_id) && $this->can("edit", $link_id))
			{
				$link_object = new object($link_id);
				if (in_array($link_id, $arr['request']['status']))
				{
					$link_object->set_status(STAT_ACTIVE);
				}
				else
				{
					$link_object->set_status(STAT_NOTACTIVE);
				}
				$link_object->set_ord((int)$arr['request']['order'][$link_id]);
				$link_object->save();
			}
		}
		return PROP_OK;
	}




	function _get_general_documents_toolbar($arr)
	{
		$t = &$arr['prop']['toolbar'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus dokument"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr['obj_inst']->id(),
				"parent" => $arr['obj_inst']->id(),
				"reltype" => 14, // expp_journam_management.general_document
				"return_url" => get_ru(),	
			), CL_DOCUMENT),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "_delete_objects",
			"confirm" => t("Oled kindel, et soovid valitud dokumendid kustutada?"),
		));

		return PROP_OK;
	}

	function _get_general_documents_table($arr)
	{

		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			"name" => "document_id",
			"caption" => t("ID"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Aktiivsus"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "as_link",
			"caption" => t("N&auml;ita lingina"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			'name' => 'lang',
			'caption' => t('Keel'),
			'align' => 'center',
			'width' => '10%'
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center",
			"width" => "5%",
		));
		$connections_to_documents = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_DOCUMENT",
		));
		$lang_inst = get_instance('languages');
		$lang_list = $lang_inst->get_list();
		foreach ($connections_to_documents as $connection_to_document)
		{
			$document_id = $connection_to_document->prop("to");
			$document_object = $connection_to_document->to();
			$t->define_data(array(
				"document_id" => $document_id,
				"name" => $connection_to_document->prop("to.name"),
				"status" => html::checkbox(array(
					"name" => "status[".$document_id."]",
					"value" => $document_id,
					"checked" => ($document_object->status() == STAT_ACTIVE) ? true : false,
				)),
				"as_link" => html::checkbox(array(
					"name" => "as_link[".$document_id."]",
					"value" => $document_id,
					"checked" => ($document_object->prop("ucheck1") == 1) ? true : false,
				)),
				'lang' => html::select(array(
					'name' => 'lang['.$document_id.']',
					'options' => $lang_list,
					'selected' => $document_object->lang_id()
				)),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $document_id,
						"return_url" => get_ru(),
						), "doc"),
					"caption" => t("Muuda"),
				)),
				"select" => html::checkbox(array(
					"name" => "selected_ids[".$document_id."]",
					"value" => $document_id,
				)),
			));
		}

		return PROP_OK;
	}

	function _set_general_documents_table($arr)
	{
		$connections_to_documents = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_DOCUMENT",
		));
		foreach ($connections_to_documents as $connection_to_document)
		{
			$document_id = $connection_to_document->prop("to");
			if (is_oid($document_id) && $this->can("edit", $document_id))
			{
				$document_object = new object($document_id);
				$document_object->set_lang_id($arr['request']['lang'][$document_id]);
				// to show document as link or not
				if (in_array($document_id, $arr['request']['as_link']))
				{
					$document_object->set_prop("ucheck1", true);
				}
				else
				{
					$document_object->set_prop("ucheck1", false);
				}

				// set documents status
				if (in_array($document_id, $arr['request']['status']))
				{
					$document_object->set_status(STAT_ACTIVE);
				}
				else
				{
					$document_object->set_status(STAT_NOTACTIVE);
				}

				$document_object->save();
			}
		}
		return PROP_OK;
	}


	function _get_general_polls_toolbar($arr)
	{
		$t = &$arr['prop']['toolbar'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus kiirk&uuml;sitlus"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr['obj_inst']->id(),
				"parent" => $arr['obj_inst']->id(),
				"reltype" => 13, // expp_journam_management.general_poll
				"return_url" => get_ru(),	
			), CL_POLL),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "_delete_objects",
			"confirm" => t("Oled kindel, et soovid valitud kiirk&uuml;sitlused kustutada?"),
		));

		

		return PROP_OK;
	}

	function _get_general_polls_table($arr)
	{

		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'poll_id',
			'caption' => t('ID'),
			'align' => 'center',
			'width' => '10%',
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "activity",
			"caption" => t("Aktiivsus"),
			"align" => "center",
			"width" => "10%",
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center",
			"width" => "5%",
		));
		$connections_to_polls = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_POLL",
			"sort_by_num" => "to.status",
			"sort_dir" => "asc",
		));
		foreach ($connections_to_polls as $connection_to_poll)
		{
			$poll_id = $connection_to_poll->prop("to");
			$t->define_data(array(
				'poll_id' => $poll_id,
				"name" => $connection_to_poll->prop("to.name"),
				"activity" => html::radiobutton(array(
					"name" => "activity",
					"value" => $poll_id,
					"checked" => ($connection_to_poll->prop("to.status") == STAT_ACTIVE) ? true : false,
				)),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $poll_id,
						"return_url" => get_ru(),
						), CL_POLL),
					"caption" => t("Muuda"),
				)),
				"select" => html::checkbox(array(
					"name" =>"selected_ids[".$poll_id."]",
					"value" => $poll_id,
				)),
			));
		}

		return PROP_OK;
	}

	function _set_general_polls_table($arr)
	{
		$connections_to_polls = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_POLL",
		));

		foreach ($connections_to_polls as $connection_to_poll)
		{
			$poll_id = $connection_to_poll->prop("to");
			if (is_oid($poll_id) && $this->can("edit", $poll_id))
			{
				$poll_object = new object($poll_id);
				if ($arr['request']['activity'] == $poll_id)
				{
					$poll_object->set_status(STAT_ACTIVE);
				}
				else
				{
					$poll_object->set_status(STAT_NOTACTIVE);
				}
				$poll_object->save();
			}
		}
		return PROP_OK;
	}

	function _get_general_webforms_toolbar($arr)
	{
		$t = &$arr['prop']['toolbar'];
		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus veebivorm"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr['obj_inst']->id(),
				"parent" => $arr['obj_inst']->id(),
				"reltype" => 12, // expp_journal_management.general_webform
				"return_url" => get_ru(),	
			), CL_WEBFORM),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "_delete_objects",
			"confirm" => t("Oled kindel, et soovid valitud veebivormid kustutada?"),
		));

		

		return PROP_OK;
	}

	function _get_general_webforms_table($arr)
	{

		$t = &$arr['prop']['vcl_inst'];
		$t->define_field(array(
			'name' => 'webform_id',
			'caption' => t('ID'),
			'align' => 'center',
			'width' => '10%',
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "activity",
			"caption" => t("Aktiivsus"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
			"width" => "10%",
		));
		$t->define_field(array(
			"name" => "select",
			"caption" => t("Vali"),
			"align" => "center",
			"width" => "5%",
		));
		$connections_to_webforms = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_WEBFORM",
			"sort_by_num" => "to.status",
			"sort_dir" => "asc",
		));
		foreach ($connections_to_webforms as $connection_to_webform)
		{
			$webform_id = $connection_to_webform->prop("to");
			$t->define_data(array(
				'webform_id' => $webform_id,
				"name" => $connection_to_webform->prop("to.name"),
				"activity" => html::radiobutton(array(
					"name" => "activity",
					"value" => $webform_id,
					"checked" => ($connection_to_webform->prop("to.status") == STAT_ACTIVE) ? true : false,
				)),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $webform_id,
						"return_url" => get_ru(),
						), CL_WEBFORM),
					"caption" => t("Muuda"),
				)),
				"select" => html::checkbox(array(
					"name" =>"selected_ids[".$webform_id."]",
					"value" => $webform_id,
				)),
			));
		}

		return PROP_OK;
	}

	function _set_general_webforms_table($arr)
	{
		$connections_to_webforms = $arr['obj_inst']->connections_from(array(
			"type" => "RELTYPE_GENERAL_WEBFORM",
		));

		foreach ($connections_to_webforms as $connection_to_webform)
		{
			$webform_id = $connection_to_webform->prop("to");
			if (is_oid($webform_id) && $this->can("edit", $webform_id))
			{
				$webform_object = new object($webform_id);
				if ($arr['request']['activity'] == $webform_id)
				{
					$webform_object->set_status(STAT_ACTIVE);
				}
				else
				{
					$webform_object->set_status(STAT_NOTACTIVE);
				}
				$webform_object->save();
			}
		}
		return PROP_OK;
	}

	function _get_general_forum($arr)
	{
		$forum_object = $arr['obj_inst']->get_first_obj_by_reltype("RELTYPE_GENERAL_FORUM");
		if (!empty($forum_object))
		{
			$forum_object_id = $forum_object->id();
		}
		if (is_oid($forum_object_id) && $this->can("view", $forum_object_id))
		{
			$arr['prop']['value'] = html::href(array(
				"url" => $this->mk_my_orb("change", array(
					"id" => $forum_object_id,
					"return_url" => get_ru(),
				), "forum_v2"),
				"caption" => t("Link foorumile")." &quot;".$forum_object->name()."&quot;",
			));
		}
		else
		{
			$arr['prop']['value'] = html::href(array(
				"url" => $this->mk_my_orb("new", array(
					"alias_to" => $arr['obj_inst']->id(),
					"parent" => $arr['obj_inst']->id(),
					"reltype" => 11, // expp_journal_management.general_forum
					"return_url" => get_ru(),
				), CL_FORUM_V2),
				"caption" => t("Lisa foorum"),
			));
		}
		
		return PROP_OK;
	}

	/**
		@attrib name=_delete_objects
	**/
	function _delete_objects($arr)
	{

		foreach ($arr['selected_ids'] as $id)
		{
			if (is_oid($id) && $this->can("delete", $id))
			{
				$object = new object($id);
				$object->delete();
			}
		}

		return $arr['post_ru'];
	}
}
?>
