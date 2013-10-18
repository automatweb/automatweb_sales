<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"

	@groupinfo general_details caption="Rekvisiidid" parent=general icon="home"
	@default group=general_details

		@property customer_name type=textbox placeholder="Nimi"
		@caption Nimi

		@property customer_short_name type=textbox placeholder="Nime l&uuml;hend"
		@caption Nime l&uuml;hend

		@property customer_ettevotlusvorm type=select
		@caption &Otilde;iguslik vorm

		@property customer_tax_nr type=textbox placeholder="KM kood"
		@caption KM kood

		@property customer_reg_nr type=textbox placeholder="Registrikood"
		@caption Registrikood

		@property customer_year_founded_show type=datepicker
		@caption Asutamise kuup&auml;ev
		
		@property customer_year_founded type=hidden
		
		@property customer_comment type=textarea placeholder="Kommentaar"
		@caption Kommentaar
	
	@groupinfo general_parties caption="Osapooled" parent=general icon="th-large"
	@default group=general_parties
	
	@groupinfo general_people caption="V&otilde;tmeisikud" parent=general icon="user"
	@default group=general_people
	
	@groupinfo general_owners caption="Omanikud" parent=general icon="lock"
	@default group=general_owners
	
	@groupinfo general_bank_details caption="Pangarekvisiidid" parent=general icon="briefcase"
	@default group=general_bank_details

@groupinfo contact caption="Kontaktid" icon="/automatweb/images/icons/32/223.png"

	@groupinfo contact_email caption="E-postiaadressid" parent=contact icon="envelope"
	@default group=contact_email
	
		@property contact_email_table type=table
		@caption E-postiaadressid
	
	@groupinfo contact_phone caption="Telefoninumbrid" parent=contact icon="book"
	@default group=contact_phone
	
		@property contact_phone_table type=table
		@caption Telefoninumbrid
	
	@groupinfo contact_address caption="Aadressid" parent=contact icon="map-marker"
	@default group=contact_address
	
		@property contact_address_toolbar type=toolbar
	
		@property contact_address_table type=table
		@caption Aadressid
	
@groupinfo employees caption="T&ouml;&ouml;tajad" icon="/automatweb/images/icons/32/1809.png"

	@property employees_toolbar type=toolbar

	@property employees_table type=table
	@caption T&ouml;&ouml;tajad

@groupinfo orders caption="Tellimused" icon="/automatweb/images/icons/32/1009.png"
@default group=orders

@groupinfo bills caption="Arved" icon="/automatweb/images/icons/32/1009.png"
@default group=bills

*/

class crm_customer_modal_company extends crm_customer_modal {
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: name() ? name : 'UUS'")));
		$type = html::span(array("data" => array("bind" => "text: isBuyer() ? 'OSTJA' : 'M&Uuml;&Uuml;JA'")));
		return $name . "&nbsp;|&nbsp;" . $type . "&nbsp;ORGANISATSIOON";
	}
	
	protected function get_save_method() {
		return "AW.UI.crm_customer_view.save_customer";
	}
	
	protected function get_popups_template() {
		return new aw_php_template("crm_customer_modal_company", "popups");
	}
	
	protected function _get_customer_name(&$property) {
		$property["data"] = array("bind" => "value: name, valueUpdate: 'afterkeydown'");
	}
	
	protected function _get_customer_short_name(&$property) {
		$property["data"] = array("bind" => "value: short_name");
	}
	
	protected function _get_customer_ettevotlusvorm(&$property) {
		$property["data"] = array("bind" => "value: ettevotlusvorm");
		$property["options"] = crm_company_obj::get_company_forms()->names();
	}
	
	protected function _get_customer_tax_nr(&$property) {
		$property["data"] = array("bind" => "value: tax_nr");
	}
	
	protected function _get_customer_reg_nr(&$property) {
		$property["data"] = array("bind" => "value: reg_nr");
	}
	
	protected function _get_customer_year_founded_show(&$property) {
		$property["data"] = array(
			"bind" => "value: year_founded_show",
			"provide" => "datepicker",
			"data-date-format" => "dd.mm.yyyy",
		);
	}
	
	protected function _get_customer_year_founded(&$property) {
		$property["data"] = array("bind" => "value: year_founded");
	}
	
	protected function _get_customer_comment(&$property) {
		$property["data"] = array("bind" => "value: comment");
	}
	
	protected function _get_contact_email_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "contact_email_table",
			"caption" => t("E-postiaadressid"),
			"fields" => array("e-mail", "type", "actions"),
			"header" => array(
				"fields" => array(
					"e-mail" => t("E-post"),
					"type" => t("T&uuml;&uuml;p"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: emails"),
				"fields" => array(
					"e-mail" => array("data" => array("bind" => "text: mail")),
					"type" => array("data" => array("bind" => "text: contact_type_caption")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.editEmail"),
						"caption" => html::italic("", "icon-pencil"),
					))." &nbsp; ".html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeEmail"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
			"footer" => array(
				"fields" => array(
					"e-mail" => html::textbox(array("data" => array("bind" => "value: email_selected().mail"))),
					"type" => html::select(array(
						"id" => "contact-email-contact_type",
						"data" => array("bind" => "value: email_selected().contact_type"),
						"options" => ml_member_obj::get_contact_type_names(),
					)),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: saveEmail"),
						"caption" => html::italic("", "icon-ok"),
					))." &nbsp; ".html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: resetEmail"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _get_contact_phone_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "contact_phone_table",
			"caption" => t("Telefoninumbrid"),
			"fields" => array("phone", "type", "actions"),
			"header" => array(
				"fields" => array(
					"phone" => t("Telefoninumber"),
					"type" => t("T&uuml;&uuml;p"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: phones"),
				"fields" => array(
					"phone" => array("data" => array("bind" => "text: name")),
					"type" => array("data" => array("bind" => "text: type_caption")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.editPhone"),
						"caption" => html::italic("", "icon-pencil"),
					))." &nbsp; ".html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removePhone"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
			"footer" => array(
				"fields" => array(
					"phone" => html::textbox(array("data" => array("bind" => "value: phone_selected().name"))),
					"type" => html::select(array(
						"id" => "contact-phone-type",
						"data" => array("bind" => "value: phone_selected().type"),
						"options" => crm_phone_obj::get_old_type_options(),
					)),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: savePhone"),
						"caption" => html::italic("", "icon-ok"),
					))." &nbsp; ".html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: resetPhone"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _get_contact_address_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "contact_address_table",
			"caption" => t("Aadressid"),
			"fields" => array("address", "types", "coordinates", "section", "actions"),
			"header" => array(
				"fields" => array(
					"address" => t("Aadress"),
					"types" => t("T&uuml;&uuml;bid"),
					"coordinates" => t("Koordinaadid"),
					"section" => t("&Uuml;ksus"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: addresses"),
				"fields" => array(
					"address" => array("data" => array("bind" => "text: name")),
					"types" => array("data" => array("bind" => "text: type_caption")),
					"coordinates" => array("data" => array("bind" => "text: (coord_x() ? coord_x() : '') + (coord_y() ? (', ' + coord_y()) : '')")),
					"section" => array("data" => array("bind" => "text: (section() ? section()[0].name : '')")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.editAddress"),
						"caption" => html::italic("", "icon-pencil"),
					))." &nbsp; ".html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeAddress"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _get_employees_table(&$property) {
		// FIXME: Make a separate class for knockout-tb table!
		$property["table"] = array(
			"id" => "contact_employees_table",
			"caption" => t("T&ouml;&ouml;tajad"),
			"fields" => array("name", "gender", "e-mail", "phone", "skills", "actions"),
			"header" => array(
				"fields" => array(
					"name" => t("Nimi"),
					"gender" => t("Sugu"),
					"e-mail" => t("E-post"),
					"phone" => t("Telefon"),
					"skills" => t("Oskused"),
					"actions" => t("Valikud"),
				)
			),
			"content" => array(
				"data" => array("bind" => "foreach: employees"),
				"fields" => array(
					"name" => array("data" => array("bind" => "text: name")),
					"gender" => array("data" => array("bind" => "text: gender_caption")),
					"e-mail" => array("data" => array("bind" => "text: email")),
					"phone" => array("data" => array("bind" => "text: phone")),
					"skills" => array("data" => array("bind" => "text: skills_caption")),
					"actions" => html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.editEmployee"),
						"caption" => html::italic("", "icon-pencil"),
					))." &nbsp; ".html::href(array(
						"url" => "javascript:void(0)",
						"data" => array("bind" => "click: \$root.removeEmployee"),
						"caption" => html::italic("", "icon-remove"),
					)),
				)
			),
		);
	}
	
	protected function _get_contact_address_toolbar(&$property) {
		// FIXME: Make a separate class for new toolbar!
		$property["buttons"] = array(
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
				"onclick" => '$("#contact-address-edit").slideDown(200);',
				"caption" => html::italic("", "icon-plus")." ".t("Lisa uus aadress"),
			)),
		);
	}
	
	protected function _get_employees_toolbar(&$property) {
		// FIXME: Make a separate class for new toolbar!
		$property["buttons"] = array(
			html::href(array(
				"url" => "javascript:void(0)",
				"class" => "btn",
				"onclick" => '$("#employees-edit").slideDown(200);',
				"caption" => html::italic("", "icon-plus")." ".t("Lisa uus t&ouml;&ouml;taja"),
			)),
		);
	}
}
