<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"
@default group=general

	@property name type=textbox
	@caption Nimi

	@property start1_show type=datetimepicker
	@caption Algus
	
	@property start1 type=hidden

	@property end_show type=datetimepicker
	@caption L&otilde;pp
	
	@property end type=hidden

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
	
	protected function _get_start1_show(&$property) {
		$property["data"] = array(
			"bind" => "value: start1_show",
		);
	}
	
	protected function _get_start1(&$property) {
		$property["data"] = array("bind" => "value: start1");
	}
	
	protected function _get_end_show(&$property) {
		$property["data"] = array(
			"bind" => "value: end_show",
		);
	}
	
	protected function _get_end(&$property) {
		$property["data"] = array("bind" => "value: end");
	}
	
	protected function _get_participants_toolbar(&$property) {
		// FIXME: Make a separate class for new toolbar!
		$property["buttons"] = array(
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
		);
	}
	
	protected function _get_participants_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "participants_table",
			"caption" => t("Osalejad"),
			"fields" => array("name", "actions"),
			"header" => array(
				"fields" => array(
					"name" => t("Nimi"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: participants"),
				"fields" => array(
					"name" => array("data" => array("bind" => "text: name")),
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