<?php
// $Header: /home/cvs/automatweb_dev/classes/expp/expp_site_content.aw,v 1.10 2008/02/17 21:13:01 kristo Exp $
// expp_site_content.aw - expp_site_content (nimi) 
/*

@classinfo syslog_type=ST_EXPP_SITE_CONTENT relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=dragut

@default table=objects
@default group=general

*/

class expp_site_content extends class_base implements main_subtemplate_handler
{

	var $expp_journal_management_id = 0;

	var $image_tag = "";
	var $main_color = "";
	var $text_color = "";
	var $frame_color = "";
	var $order_composition_information = "";
	var $ordering_terms_document_id = 0;

	var $connections_to_documents = array();
	var $connections_to_links = array();
	var $connections_to_images = array();
	var $connections_to_files = array();
	var $connections_to_groups = array();
	
	var $poll_object = "";
	var $webform_object = "";
	var $forum_object = "";
	var $custom_design_document_object = "";
	var $publication_homepage_link_object = "";


	function expp_site_content()
	{
		// change this to the folder under the templates folder, where this classes templates will be, 
		// if they exist at all. Or delete it, if this class does not use templates
		$this->init(array(
			"tpldir" => "automatweb/menuedit",
			"clid" => CL_EXPP_SITE_CONTENT
		));
	}

	function show($arr) {
		$retHTML = '';
		if( isset( $GLOBALS['expp_site'] ) && !empty($GLOBALS['expp_site'])) {
			$ol = new object_list(array(
				"class_id" => CL_EXPP_JOURNAL_MANAGEMENT,
				"code" => $GLOBALS['expp_site'],
				'lang_id' => array(),
			));
			if ($ol->count() > 0)
			{
				$o = $ol->begin();

				$this->expp_journal_management_id = $o->id();
				
				$image_obj = $o->get_first_obj_by_reltype("RELTYPE_COVER_IMAGE");

				if (!empty($image_obj))
				{
					$image_inst = get_instance(CL_IMAGE);
					$this->image_tag = $image_inst->make_img_tag_wl($image_obj->id());
				}

				// kas kasutaja tahab ise lehte kujundada:
				$choose_design = $o->prop("choose_design");
				
				if ($choose_design == "custom_design")
				{
					// okei, tahab, vaatab kas ta doku ka pand on:
					
					$custom_design_document_id = $o->prop("custom_design_document");

					if ($this->can("view", $custom_design_document_id))
					{
						$this->custom_design_document_object = new object($custom_design_document_id);
					}
				}
				// kui dokut ülalt kätte ei saand, siis n2itame defaulti
				if (empty($this->custom_design_document_object))
				{
					// organisation info
					$organisation_obj = $o->get_first_obj_by_reltype('RELTYPE_ORGANISATION');
					if ( !empty($organisation_obj) )
					{
						// organisatsiooni juurde ei saa praegu logo panna, sest vastav v2li on maha keeratud
					}
			
					$this->connections_to_links = $o->connections_from(array(
						"type" => "RELTYPE_GENERAL_LINK",
						'to.status' => STAT_ACTIVE
					));
					
					$this->connections_to_documents = $o->connections_from(array(
						"type" => "RELTYPE_GENERAL_DOCUMENT",
						'to.status' => STAT_ACTIVE,
						'to.lang_id' => aw_global_get('lang_id')
					));

					$this->connections_to_images = $o->connections_from(array(
						"type" => "RELTYPE_GENERAL_IMAGE",
						'to.status' => STAT_ACTIVE,
						"sort_by_num" => "to.jrk",
						"sort_dir" => "asc",
					));

					$this->connections_to_files = $o->connections_from(array(
						"type" => "RELTYPE_GENERAL_FILE",
						'to.status' => STAT_ACTIVE,
						"sort_by_num" => "to.jrk",
						"sort_dir" => "asc",
					));
	
					// get the connections to active polls
					$connections_to_polls = $o->connections_from(array(
						"type" => "RELTYPE_GENERAL_POLL",
						"to.status" => STAT_ACTIVE,
					));
					// i assume, that there is only one active poll 
					$connection_to_active_poll = reset($connections_to_polls);
					if ($connection_to_active_poll)
					{
						$this->poll_object = $connection_to_active_poll->to();
					}
	
					// get the connection to active webform
					$connections_to_webforms = $o->connections_from(array(
						"type" => "RELTYPE_GENERAL_WEBFORM",
						"to.status" => STAT_ACTIVE,
					));
					$connection_to_active_webform = reset($connections_to_webforms);
					if ($connection_to_active_webform)
					{
						$this->webform_object = $connection_to_active_webform->to();
					}
				
					// get the forum obj
					$this->forum_object = $o->get_first_obj_by_reltype("RELTYPE_GENERAL_FORUM");

					$publications_homepage_oid = $o->prop("publications_homepage");
					if ($this->can("view", $publications_homepage_oid))
					{
						$this->publication_homepage_link_object = new object($publications_homepage_oid);
					}
					
					// i need the publications group:
					// i think i can take all permissions type connections from the management
					// object ...
					$this->connections_to_groups = $o->connections_from(array(
						"type" => RELTYPE_ACL,
						"to.class_id" => CL_GROUP,
					));
	
					$this->main_color = $o->prop("main_color");
					$this->text_color = $o->prop("text_color");
					$this->frame_color = $o->prop("frame_color");
					$this->order_composition_information = nl2br($this->trans_get_val($o, 'order_composition_information'));
					// here i need to get the correct ordering form document according to the active language
					$ordering_terms = $o->prop('ordering_terms');
					$this->ordering_terms_document_id = $ordering_terms[aw_global_get('lang_id')];
				}
			}
			
		}
//		return $retHTML;
	}

	function on_get_subtemplate_content($arr) {
		$this->show();

	/*
		if (empty($this->image_tag) && empty($this->document_content) && empty($this->connections_to_links))
		{
			return;
		}
	*/
		$this->read_template("main.tpl");

		/* if this variable gets its value as true, then show the expp gray table */
		/* at this moment (13.11.2005), it should get true, when it is needed to show:
			- link to webform
			- link to forum
			- links
			- poll
		*/
		$show_links_table = false;
		$document_inst = get_instance(CL_DOCUMENT);

		if (!empty($this->custom_design_document_object))
		{
			$document_inst = get_instance(CL_DOCUMENT);
			$document_content = $document_inst->gen_preview(array(
				"docid" => $this->custom_design_document_object->id(),
			));

			$this->vars(array(
				"content" => $document_content
			));
			
			$this->vars(array(
				"CUSTOM_DESIGN" => $this->parse("CUSTOM_DESIGN"),
			));

		}
		else
		{
			/* DOKUMENTIDE PARSIMINE */
	
			$dokumendid = "";
			$dokumendid_lingina = "";
			foreach ($this->connections_to_documents as $connection_to_document)
			{
				$document_object = $connection_to_document->to();
				$document_id = $document_object->id();
			//	$document_title = $document_object->prop("title");
				$document_title = $this->trans_get_val($document_object, 'title');
			//	$document_content = $document_object->prop("content");
				$document_content = $this->trans_get_val($document_object, 'content');

				
				$this->vars(array(
					"DOCUMENT_ID" => $document_id,
					"DOCUMENT_TITLE" => $document_title,
					"DOCUMENT_CONTENT" => "",
				));
	
				// kui on määratud, et dokumenti peaks kuvama lingina:
				if ($document_object->prop("ucheck1") == 1)
				{
					$this->vars(array(
						"DOCUMENT_LINK" => $this->parse("DOCUMENT_LINK"),
					));
					$dokumendid_lingina .= $this->parse("GENERAL_DOCUMENTS_AS_LINKS");
					
				}
				else
				{
					// ???
					// vbla ma peaks alias_parseri abil lihtsalt doc_contenti stringist aliased v2lja parsima
					// ja mitte kasutama seda gen_preview()-d ? --dragut
					$document_content = $document_inst->gen_preview(array(
						'docid' => $document_id
					));

					$this->vars(array(
						"DOCUMENT_CONTENT" => $document_content,
					));
				
					$this->vars(array(
						"DOCUMENT_CONTENT" => $this->parse("DOCUMENT_CONTENT"),
					));
					$dokumendid .= $this->parse("GENERAL_DOCUMENTS");
				}
				
			}
	
			/* PILTIDE PARSIMINE */
			$pildid = "";
			foreach ($this->connections_to_images as $connection_to_image)
			{
				$image_inst = get_instance(CL_IMAGE);
				$image_object = $connection_to_image->to();
				$this->vars(array(
					"GENERAL_IMAGE_TAG" => $image_inst->make_img_tag_wl($image_object->id()),
					'GENERAL_IMAGE_COMMENT' => $this->trans_get_val($image_object, 'comment'),
				));
	
				$pildid .= $this->parse("GENERAL_IMAGES");
	
			}

			/* ORGANISATSIOONI PILT (LOGO) */

			
	
			/* LINKIDE PARSIMINE */
			$lingid = "";
			foreach ($this->connections_to_links as $connection_to_link)
			{
				$link_object = $connection_to_link->to();
				$target = "";
				if ($link_object->prop("newwindow") > 0)
				{
					$target = "target=\"_blank\"";
				}

			//	$link_url = $link_object->prop('url');
				$link_url = $this->trans_get_val($link_object, 'url');
			//	$link_name = $link_object->name();
				$link_name = $this->trans_get_val($link_object, 'name');
				$link_alt_txt = $this->trans_get_val($link_object, 'alt');

				if ( empty($link_name) )
				{
					$link_name = $link_url;
				}

				$this->vars(array(
					"GENERAL_LINK_URL" => $link_url,
					"GENERAL_LINK_NAME" => $link_name,
					"GENERAL_LINK_TARGET" => $target,
					"GENERAL_LINK_ALT_TEXT" => $link_alt_txt
				));
	
				$lingid .= $this->parse("GENERAL_LINK");
			}
			if (!empty($lingid))
			{
				$this->vars(array(
					"GENERAL_LINK" => $lingid
				));
				$lingid = $this->parse("GENERAL_LINKS");
				$show_links_table = true;
			}
	
			/* FAILIDE PARSIMINE */
			$failid = "";
			foreach ($this->connections_to_files as $connection_to_file)
			{
				$file_inst = get_instance(CL_FILE);
				$file_object = $connection_to_file->to();
				$filename = $file_object->name();
				$file_id = $file_object->id();
			//	$file_comment = $file_object->prop('comment');
				$file_comment = $this->trans_get_val($file_object, 'comment');
				$target = "";
				if ( $file_object->prop('newwindow') > 0 )
				{
					$target = "target=\"_blank\"";
				}
				
				$this->vars(array(
					"GENERAL_FILE_URL" => $file_inst->get_url($file_id, $file_name),
					"GENERAL_FILE_NAME" => $filename,
					'GENERAL_FILE_COMMENT' => $file_comment,
					'GENERAL_FILE_TARGET' => $target
				));
	
				$failid .= $this->parse("GENERAL_FILE");
	
			}
			if (!empty($failid))
			{
				$this->vars(array(
					"GENERAL_FILE" => $failid
				));
				$failid = $this->parse("GENERAL_FILES");
			}
	
			/* KIIRKÜSITLUS */
			
			if (!empty($this->poll_object))
			{
				$poll_inst = get_instance(CL_POLL);
				$poll_id = $this->poll_object->id();
				if ($this->can("view", $poll_id))
				{
					$general_poll = $poll_inst->gen_user_html($poll_id);
					$show_links_table = true;
				}
			}
			/* LINK HALDUSOBJEKTILE */
			/* peab olema nähtav ainult adminnidele ja vastava haldusobjekti toimetajate grupile */
			$link_to_management = "";
			$show_link_to_management = false;
			
			// logged in users group ids:
			$group_ids = aw_global_get("gidlist_oid");
			if (in_array(aw_ini_get('admin_group_id'), $group_ids))
			{
				$show_link_to_management = true;
			}
			else
			{

				foreach($this->connections_to_groups as $connection_to_group)
				{
					if (in_array($connection_to_group->prop("to"), $group_ids))
					{
						$show_link_to_management = true;
					}
				}
			}
			if ($show_link_to_management === true)
			{
				$this->vars(array(
					"LINK_TO_MANAGEMENT_URL" => $this->mk_my_orb("change", array(
						"id" => $this->expp_journal_management_id,
						"group" => "publications",
					), CL_EXPP_JOURNAL_MANAGEMENT, true, true),
					"LINK_TO_MANAGEMENT_NAME" => t("Andmete muutmine"),
				));
				$link_to_management = $this->parse("LINK_TO_MANAGEMENT");
			}
	
			/* LINK FOORUMILE */
			if (!empty($this->forum_object))
			{
				$this->vars(array(
					"LINK_TO_FORUM_NAME" => $this->forum_object->name(),
					"LINK_TO_FORUM_URL" => aw_ini_get("baseurl")."/section=".$this->forum_object->id()."&tel_tpl=1",

				));
				$link_to_forum = $this->parse("LINK_TO_FORUM");
				$show_links_table = true;
			}
			
			/* LINK VEEBIVORMILE */
			if (!empty($this->webform_object))
			{
				$this->vars(array(
					"LINK_TO_WEBFORM_NAME" => $this->webform_object->name(),
					"LINK_TO_WEBFORM_URL" => aw_ini_get("baseurl")."/section=".$this->webform_object->id()."&tel_tpl=1",
				));
				$link_to_webform = $this->parse("LINK_TO_WEBFORM");
				$show_links_table = true;

			}

			/* LINK VÄLJAANDE KODULEHELE */
			if (!empty($this->publication_homepage_link_object))
			{
				$target = "";
				if ($this->publication_homepage_link_object->prop("newwindow") > 0)
				{
					$target = "_blank";
				}
				
				$this->vars(array(
					"LINK_TO_PUBLICATION_HOMEPAGE_NAME" => $this->publication_homepage_link_object->name(),
					"LINK_TO_PUBLICATION_HOMEPAGE_URL" => $this->publication_homepage_link_object->prop("url"),
					"LINK_TO_PUBLICATION_HOMEPAGE_TARGET" => $target,
				));
				$link_to_publication_homepage = $this->parse("LINK_TO_PUBLICATION_HOMEPAGE");
			}
			
			/* show links table (look the comment near $show_links_table first declaration */
			if ($show_links_table === true)
			{
				$this->vars(array(
					"GENERAL_POLL" => $general_poll,
					"GENERAL_LINKS" => $lingid,
					"LINK_TO_FORUM" => $link_to_forum,
					"LINK_TO_WEBFORM" => $link_to_webform,
				));
				$links_table = $this->parse("LINKS_TABLE");
			}
			
			$this->vars(array(
				"DOC_IMAGE" => $this->image_tag,
				"GENERAL_DOCUMENTS" => $dokumendid,
				"GENERAL_DOCUMENTS_AS_LINKS" => $dokumendid_lingina,
				"GENERAL_IMAGES" => $pildid,
				"GENERAL_FILES" => $failid,
				"LINKS_TABLE" => $links_table,
				"LINK_TO_MANAGEMENT" => $link_to_management,
				"LINK_TO_PUBLICATION_HOMEPAGE" => $link_to_publication_homepage,
				"ORDER_COMPOSITION_INFORMATION" => $this->order_composition_information,
				"ORDERING_TERMS_DOC_ID" => $this->ordering_terms_document_id,
			));
			$this->vars(array(
				"DEFAULT_DESIGN" => $this->parse("DEFAULT_DESIGN"),
			));
		}
		// lets load the LANG vars too:
		lc_site_load("menuedit", $this);

		// XXX - i think that it would be a good idea to check at the beginning, if $GLOBALS['expp_site']
		// variable is set or not, because if it isn't then i don't have to parse those subs --dragut
		if (!empty($GLOBALS['expp_site']))
		{
			if ($this->ordering_terms_document_id)
			{
				$doc_ordering_terms_link = $this->parse( "DOC_ORDERING_TERMS_LINK" );
			}
			else
			{
				$doc_ordering_terms_link = $this->parse( "DOC_NO_ORDERING_TERMS_LINK" );
			}
		}
		$arr["inst"]->vars(array(
			"VAIKE_DOC" => $this->parse("VAIKE_DOC"),
			"DOC_FRAME_COLOR" => $this->frame_color,
			"DOC_MAIN_COLOR" => $this->main_color,
			"DOC_TEXT_COLOR" => $this->text_color,
			"DOC_COMPOSITION_INFORMATION" => ( $this->order_composition_information ? $this->parse( "DOC_COMPOSITION_INFORMATION" ) : '' ),
//			"DOC_ORDERING_TERMS_LINK" => ( $this->ordering_terms_document_id ? $this->parse( "DOC_ORDERING_TERMS_LINK" ) : $this->parse( "DOC_NO_ORDERING_TERMS_LINK" ) ),
			"DOC_ORDERING_TERMS_LINK" => $doc_ordering_terms_link,
		));
	}
	
	function register( $in ) {
		$GLOBALS['expp_site'] = $in;
	}
}
?>
