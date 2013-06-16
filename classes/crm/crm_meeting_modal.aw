<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"
@default group=general

	@property name type=textbox
	@caption Nimi

	@property start1 type=textbox
	@caption Algus

	@property end type=textbox
	@caption L&otilde;pp

@groupinfo participants caption="Osalejad" icon="/automatweb/images/icons/32/289.png"
@default group=participants

*/

class crm_meeting_modal extends aw_modal {
	
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: name() ? name : 'UUS'")));
		return $name . "&nbsp;|&nbsp;KOHTUMINE";
	}
	
	protected function get_save_method() {
		return "(function(){})";//"AW.UI.crm_meeting.save";
	}
}