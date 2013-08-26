<?php
/*

@groupinfo general caption="&Uuml;ldandmed" icon="/automatweb/images/icons/32/1808.png"

	@groupinfo details caption="&Uuml;ldandmed" icon="home" parent=general
	@default group=details

		@property name type=textbox
		@caption Nimi
	
		@property folders_table type=table
		@caption Piltide kataloogid

	@groupinfo settings caption="Seaded" icon="wrench" parent=general
	@default group=settings

@groupinfo management caption="Haldus" icon="/automatweb/images/icons/32/289.png"
@default group=management

	@property images_table type=table
	@caption Pildid

*/

class gallery_modal extends aw_modal {
	
	protected function get_title() {
		$name = html::span(array("data" => array("bind" => "text: name() ? name : 'UUS'")));
		return $name . "&nbsp;|&nbsp;GALERII";
	}
	
	protected function get_save_method() {
		return "AW.UI.admin_if.save";
	}
}
