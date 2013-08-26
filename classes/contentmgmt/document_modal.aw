<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"
@default group=general

	@property title type=textbox placeholder="Pealkiri" update=instant
	@caption Pealkiri

	@property document_status type=chooser
	@caption Staatus

	@property lead type=textarea placeholder="Sissejuhatus" rows=10 class=input-xxlarge
	@caption Sissejuhatus

	@property content type=textarea placeholder="Sisu" rows=30 class=input-xxlarge
	@caption Sisu

	@property moreinfo type=textarea placeholder="Toimetamata" rows=10 class=input-xxlarge
	@caption Toimetamata

	@property reg_date type=datepicker
	@caption Registreerimise kuup&auml;ev

	@property make_date type=datepicker
	@caption Koostamise kuup&auml;ev
	
@groupinfo parties caption="Osapooled" icon="/automatweb/images/icons/32/1809.png"
@default group=parties

	@groupinfo editors caption="Koostajad" icon="pencil" parent=parties
	@default group=editors
	
		@property editors_toolbar type=toolbar

		@property editors_table type=table
		@caption Koostajad

	@groupinfo authors caption="Autorid" icon="star" parent=parties
	@default group=authors
	
		@property authors_toolbar type=toolbar
	
		@property authors_table type=table
		@caption Autorid

	@groupinfo participants caption="Osalejad" icon="user" parent=parties
	@default group=participants
	
		@property participants_toolbar type=toolbar
	
		@property participants_table type=table
		@caption Osalejad
		
@groupinfo attachments caption="Manused" icon="/automatweb/images/icons/32/289.png"
@default group=attachments

	@property attachments_toolbar type=toolbar
	
	@property attachments_table type=table
	@caption Manused

@groupinfo settings caption="Seadistused" icon="/automatweb/images/icons/32/289.png"
@default group=settings

	@property show_title type=checkbox
	@caption N&auml;ita pealkirja

	@property showlead type=checkbox
	@caption N&auml;ita sissejuhatust

	@property show_modified type=checkbox
	@caption N&auml;ita muutmise kuup&auml;eva

	@property esilehel type=checkbox
	@caption Esilehel

	@property title_clickable type=checkbox
	@caption Pealkiri klikitav
	
@groupinfo parents caption="Asukohad" icon="/automatweb/images/icons/32/289.png"
@default group=parents

	@property parents_tree type=treeview

*/

class document_modal extends aw_modal {
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: title() ? title : 'UUS'")));
		return $name . "&nbsp;|&nbsp;DOKUMENT";
	}
	
	protected function get_save_method() {
		return "AW.UI.admin_if.save";
	}
	
	protected function _get_document_status(&$property) {
		$property["data"] = array(
			"bind" => "chooser: document_status, chooserOptions: " . str_replace("\"", "'", json_encode(doc_obj::get_document_status_names())),
		);
	}
	
	protected function _get_editors_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "editors_table",
			"caption" => t("Koostajad"),
			"fields" => array("name", "phone", "e-mail", "organisation", "actions"),
			"header" => array(
				"fields" => array(
					"name" => t("Nimi"),
					"phone" => t("Telefon"),
					"e-mail" => t("E-post"),
					"organisation" => t("Organisatsioon"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: editors"),
				"fields" => array(
					"name" => array("data" => array("bind" => "text: name")),
					"phone" => array("data" => array("bind" => "text: phone")),
					"e-mail" => array("data" => array("bind" => "text: email")),
					"organisation" => array("data" => array("bind" => "text: organisation.name")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeEditor"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _get_authors_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "authors_table",
			"caption" => t("Autorid"),
			"fields" => array("name", "phone", "e-mail", "organisation", "actions"),
			"header" => array(
				"fields" => array(
					"name" => t("Nimi"),
					"phone" => t("Telefon"),
					"e-mail" => t("E-post"),
					"organisation" => t("Organisatsioon"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: authors"),
				"fields" => array(
					"name" => array("data" => array("bind" => "text: name")),
					"phone" => array("data" => array("bind" => "text: phone")),
					"e-mail" => array("data" => array("bind" => "text: email")),
					"organisation" => array("data" => array("bind" => "text: organisation.name")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeAuthor"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _get_participants_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "participants_table",
			"caption" => t("Osalejad"),
			"fields" => array("chooser", "name", "phone", "e-mail", "organisation", "participantion_type", "permissions", "actions"),
			"header" => array(
				"fields" => array(
/*					"chooser" => html::href(array(
						"url" => "javascript:void(0)",
						"onclick" => "AW.UI.table.chooser.toggle(this)",
						"caption" => t("Vali"),
					)), */
					"chooser" => t("Vali"),
					"name" => t("Nimi"),
					"phone" => t("Telefon"),
					"e-mail" => t("E-post"),
					"organisation" => t("Organisatsioon"),
					"participantion_type" => t("Osaluse t&uuml;&uuml;p"),
					"permissions" => t("&Otilde;igused"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: participants"),
				"fields" => array(
					"chooser" => html::checkbox(array(
						"data" => array("bind" => "checked: chosen"),
					)),
					"name" => array("data" => array("bind" => "text: name")),
					"phone" => array("data" => array("bind" => "text: phone")),
					"e-mail" => array("data" => array("bind" => "text: email")),
					"participantion_type" => array("data" => array("bind" => "chooser: participantion_type, chooserOptions: " . str_replace("\"", "'", json_encode(doc_obj::get_participation_type_names())))),
					"permissions" => array("data" => array("bind" => "chooser: permissions, chooserOptions: " . str_replace("\"", "'", json_encode(doc_obj::get_participation_permission_names())))),
					"organisation" => array("data" => array("bind" => "text: organisation().name")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeParticipant"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _get_editors_toolbar(&$property) {
		$property["buttons"] = array(
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
				"data" => array("bind" => "click: selectEditors"),
				"caption" => html::italic("", "icon-plus")." ".t("Lisa koostaja"),
			)),
		);
	}
	
	protected function _get_authors_toolbar(&$property) {
		$property["buttons"] = array(
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
				"data" => array("bind" => "click: selectAuthors"),
				"caption" => html::italic("", "icon-plus")." ".t("Lisa autor"),
			)),
		);
	}
	
	protected function _get_participants_toolbar(&$property) {
		$property["buttons"] = array(
			html::div(array(
				"class" => "btn-group dropup",
				"content" => html::href(array(
					"url" => "javascript:void(0)",
					"class" => "btn dropdown-toggle",
					"data" => array("toggle" => "dropdown"),
					"caption" => html::italic("", "icon-plus")." ".t("Lisa osaleja")." ".html::span(array("class" => "caret")),
				)).html::ul(array(
					"class" => "dropdown-menu",
					"style" => "text-align: left",
					"items" => array(
						html::href(array(
							"data" => array("bind" => "click: selectParticipatingColleague"),
							"url" => "javascript:void(0)",
							"caption" => t("Kolleeg"),
						)),
						html::href(array(
							"data" => array("bind" => "click: selectParticipatingSection"),
							"url" => "javascript:void(0)",
							"caption" => t("&Uuml;ksus"),
						)),
						html::href(array(
							"data" => array("bind" => "click: selectParticipatingClientGroup"),
							"url" => "javascript:void(0)",
							"caption" => t("Kliendigrupp"),
						)),
					),
				))
			)),
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
				"data" => array("bind" => "click: notifyParticipants"),
				"caption" => html::italic("", "icon-envelope")." ".t("Teata muudatustest"),
			)),
		);
	}
	
	protected function _get_attachments_toolbar(&$property) {
		$property["buttons"] = array(
			html::div(array(
				"class" => "btn-group dropup",
				"content" => html::href(array(
					"url" => "javascript:void(0)",
					"class" => "btn dropdown-toggle",
					"data" => array("toggle" => "dropdown"),
					"caption" => html::italic("", "icon-plus")." ".t("Lisa uus")." ".html::span(array("class" => "caret")),
				)).html::ul(array(
					"class" => "dropdown-menu",
					"style" => "text-align: left",
					"items" => array(
						html::href(array(
							"data" => array("bind" => "click: createAttachmentFile"),
							"url" => "javascript:void(0)",
							"caption" => t("Fail"),
						)),
						html::href(array(
							"data" => array("bind" => "click: createAttachmentImage"),
							"url" => "javascript:void(0)",
							"caption" => t("Pilt"),
						)),
						html::href(array(
							"data" => array("bind" => "click: createAttachmentLink"),
							"url" => "javascript:void(0)",
							"caption" => t("Link"),
						)),
						html::href(array(
							"data" => array("bind" => "click: createAttachmentDocument"),
							"url" => "javascript:void(0)",
							"caption" => t("Dokument"),
						)),
					),
				))
			)),html::div(array(
				"class" => "btn-group dropup",
				"content" => html::href(array(
					"url" => "javascript:void(0)",
					"class" => "btn dropdown-toggle",
					"data" => array("toggle" => "dropdown"),
					"caption" => html::italic("", "icon-search")." ".t("Otsi olemasolev")." ".html::span(array("class" => "caret")),
				)).html::ul(array(
					"class" => "dropdown-menu",
					"style" => "text-align: left",
					"items" => array(
						html::href(array(
							"data" => array("bind" => "click: searchAttachmentFile"),
							"url" => "javascript:void(0)",
							"caption" => t("Fail"),
						)),
						html::href(array(
							"data" => array("bind" => "click: searchAttachmentImage"),
							"url" => "javascript:void(0)",
							"caption" => t("Pilt"),
						)),
						html::href(array(
							"data" => array("bind" => "click: searchAttachmentLink"),
							"url" => "javascript:void(0)",
							"caption" => t("Link"),
						)),
						html::href(array(
							"data" => array("bind" => "click: searchAttachmentDocument"),
							"url" => "javascript:void(0)",
							"caption" => t("Dokument"),
						)),
					),
				))
			)),
		);
	}
	
	protected function _get_attachments_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "attachments_table",
			"caption" => t("Manused"),
			"fields" => array("name", "type", "actions"),
			"header" => array(
				"fields" => array(
					"name" => t("Nimi"),
					"type" => t("T&uuml;&uuml;p"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: attachments"),
				"fields" => array(
					"name" => array("data" => array("bind" => "text: name")),
					"type" => array("data" => array("bind" => "text: type")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeAttachment"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _set_editors($document, $editors) {
		$document->set_meta("editors", $editors);
	}
	
	protected function _set_authors($document, $authors) {
		$document->set_meta("authors", $authors);
	}
	
	protected function _set_participants($document, $participants) {
		$document->set_meta("participants", $participants);
	}
	
	protected function _get_parents_tree(&$property) {
		$io_url = core::mk_my_orb("get_parents_tree_items", array(), "document_modal");
		$property["data"] = array("bind" => "treeview: parents, treeviewOptions: { io: '{$io_url}' }");
	}
	
	/**
		@attrib name=get_parents_tree_items
	**/
	public function get_parents_tree_items() {
		$parent = automatweb::$request->arg_isset("parent") ? automatweb::$request->arg("parent") : aw_ini_get("rootmenu");
		
		$ol = new object_list(array(
			"class_id" => CL_MENU,
			"parent" => $parent
		));
		
		$items = $this->__prepare_object_list($ol);
		
		$json_encoder = new json();
		$json = $json_encoder->encode($items, aw_global_get("charset"));
	
		automatweb::$result->set_data($json);
		automatweb::$instance->http_exit();
	}
	
	private function __prepare_object_list($ol, $children = true) {
		$items = array();
		
		foreach ($ol->names() as $id => $name) {
			$items[] = array(
				"id" => $id,
				"label" => html::span(array("content" => $name, "data" => array("node-id" => $id))),
//				"expanded" => true,
				"leaf" => false,
				"type" => "check",
//				"checked" => true,
				"io" => core::mk_my_orb("get_parents_tree_items", array("parent" => $id), "document_modal"),
				"children" => $children ? $this->__prepare_object_list(new object_list(array("class_id" => CL_MENU, "parent" => $id))) : null,
			);
		}
		
		return $items;
	}
}