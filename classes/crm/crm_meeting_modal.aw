<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"
@default group=general

	@property name type=textbox update=instant class=input-xxlarge
	@caption Kohtumise teema
	
	@layout timespan type=horizontal captionside=left width=6:6

		@property start1 type=datetimepicker parent=timespan
		@caption Algus

		@property end type=datetimepicker parent=timespan
		@caption L&otilde;pp

	@property comment type=textarea class=input-xxlarge
	@caption Kokkuv&otilde;te

	@property content type=textarea rows=15 class=input-xxlarge
	@caption Sisu

@groupinfo participants caption="Osalejad" icon="/automatweb/images/icons/32/1809.png"
@default group=participants
	
	@property participants_toolbar type=toolbar

	@property participants_table type=table
	@caption Osalejad

@groupinfo attachments caption="Manused" icon="/automatweb/images/icons/32/289.png"
@default group=attachments
	
	@property attachments_toolbar type=toolbar

	@property attachments_table type=table
	@caption Manused

*/

class crm_meeting_modal extends aw_modal {
	
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: name() ? name : 'UUS'")));
		return $name . "&nbsp;|&nbsp;KOHTUMINE";
	}
	
	protected function get_save_method() {
		return "AW.UI.calendar.saveEventDetails";
	}
	
	protected function _get_name(&$property) {
		$property["data"] = array("bind" => "value: name, valueUpdate: 'afterkeydown'");
	}
	
	protected function _get_start1(&$property) {
		$property["data"] = array("bind" => "datetimepicker: start1");
	}
	
	protected function _get_end(&$property) {
		$property["data"] = array("bind" => "datetimepicker: end");
	}
	
	protected function _get_comment(&$property) {
		$property["data"] = array(
			"bind" => "value: comment",
		);
	}
	
	protected function _get_content(&$property) {
		$property["data"] = array(
			"bind" => "value: content",
		);
	}
	
	protected function _get_participants_toolbar(&$property) {
		// FIXME: Make a separate class for new toolbar!
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
							"data" => array("bind" => "click: selectParticipantsFromColleagues"),
							"url" => "javascript:void(0)",
							"caption" => t("Kolleeg"),
						)),
						html::href(array(
							"data" => array("bind" => "click: selectParticipantsFromClients"),
							"url" => "javascript:void(0)",
							"caption" => t("Klient"),
						)),
					),
				))
			)),
/*
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
//				"onclick" => '$("#contact-address-edit").slideDown(200);',
				"data" => array("bind" => "click: selectParticipantsFromColleagues"),
				"caption" => html::italic("", "icon-plus")." ".t("Lisa kolleeg"),
			)),
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
//				"onclick" => '$("#contact-address-edit").slideDown(200);',
				"data" => array("bind" => "click: selectParticipantsFromClients"),
				"caption" => html::italic("", "icon-plus")." ".t("Lisa klient"),
			)),
		*/
		);
	}
	
	protected function _get_participants_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "participants_table",
			"caption" => t("Osalejad"),
			"fields" => array("name", "time_guess", "time_real", "time_to_cust", "billable", "actions"),
			"header" => array(
				"fields" => array(
					"name" => t("Nimi"),
					"time_guess" => t("Prognoositud tunde"),
					"time_real" => t("Kulunud tunde"),
					"time_to_cust" => t("Tunde kliendile"),
					"billable" => t("Arvele"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: participants"),
				"fields" => array(
					"name" => array("data" => array("bind" => "text: impl().name")),
					"time_guess" => html::textbox(array(
						"class" => "input-mini",
						"data" => array("bind" => "value: time_guess"),
					)),
					"time_real" => html::textbox(array(
						"class" => "input-mini",
						"data" => array("bind" => "value: time_real"),
					)),
					"time_to_cust" => html::textbox(array(
						"class" => "input-mini",
						"data" => array("bind" => "value: time_to_cust"),
					)),
					"billable" => html::checkbox(array(
						"data" => array("bind" => "checked: billable"),
					)),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeParticipant"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _get_attachments_toolbar(&$property) {
		// FIXME: Make a separate class for new toolbar!
		$property["buttons"] = array(
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
//				"onclick" => '$("#contact-address-edit").slideDown(200);',
				"data" => array("bind" => "click: selectAttachments"),
				"caption" => html::italic("", "icon-plus")." ".t("Lisa manuseid"),
			)),
		);
	}
	
	protected function _get_attachments_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "attachments_table",
			"caption" => t("Manused"),
			"fields" => array("name", "actions"),
			"header" => array(
				"fields" => array(
					"name" => t("Nimi"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: attachments"),
				"fields" => array(
					"name" => array("data" => array("bind" => "text: name")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeAttachment"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
}