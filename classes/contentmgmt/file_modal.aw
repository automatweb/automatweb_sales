<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"
@default group=general

	@property name type=textbox placeholder=Nimi update=instant
	@caption Nimi

	@property status type=chooser
	@caption Aktiivne

	@property file type=fileupload
	@caption Vali fail
	
	@property ord type=textbox
	@caption J&auml;rjekord
	
	@property comment type=textarea
	@caption Kommentaar
	
	@property alias type=textbox
	@caption Alias
	
	@property file_url type=textbox
	@caption URL
	
@groupinfo settings caption="Seadistused" icon="/automatweb/images/icons/32/289.png"
@default group=settings

	@property newwindow type=checkbox
	@caption Uues aknas
	
	@property show_framed type=checkbox
	@caption N&auml;ita raamis
	
	@property show_icon type=checkbox
	@caption N&auml;ita ikooni

*/

class file_modal extends aw_modal {
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: name() ? name : 'UUS'")));
		return $name . "&nbsp;|&nbsp;FAIL";
	}
	
	protected function get_save_method() {
		return "AW.UI.admin_if.save";
	}
	
	protected function _get_status(&$property) {
		$property["data"] = array("bind" => "chooser: status, chooserOptions: { '1': 'Deaktiivne', '2': 'Aktiivne' }");
	}
}