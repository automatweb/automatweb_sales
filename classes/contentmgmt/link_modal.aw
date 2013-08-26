<?php

/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"
@default group=general

	@property name type=textbox placeholder=Nimi update=instant
	@caption Nimi
	
	@property comment type=textarea
	@caption Kommentaar
	
	@property alt type=textbox
	@caption Alt-tekst
	
	@property url type=textbox
	@caption URL
	
	@property newwindow type=checkbox
	@caption Uues aknas
	
*/

class link_modal extends aw_modal {
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: name() ? name : 'UUS'")));
		return $name . "&nbsp;|&nbsp;LINK";
	}
	
	protected function get_save_method() {
		return "AW.UI.admin_if.save";
	}
}