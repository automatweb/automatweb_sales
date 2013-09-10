<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"
@default group=general

	@property name type=textbox placeholder=Nimi update=instant
	@caption Nimi
	
	@property comment type=textarea placeholder=Kommentaar
	@caption Kommentaar
	
	@property status type=chooser
	@caption Aktiivne
	
	@property status_recursive type=checkbox
	@caption Aktiveeri/deaktiveeri ka alamkaustad
	
	@property alias type=textbox placeholder=Alias
	@caption Alias
	
*/

class menu_modal extends aw_modal {
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: name() ? name : 'UUS'")));
		return $name . "&nbsp;|&nbsp;KAUST";
	}
	
	protected function get_save_method() {
		return "AW.UI.admin_if.save";
	}
	
	protected function _get_status(&$property) {
		$property["data"] = array("bind" => "chooser: status, chooserOptions: { '1': 'Deaktiivne', '2': 'Aktiivne' }");
	}
}
