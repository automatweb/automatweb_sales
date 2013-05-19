<?php

class document_modal extends aw_modal {
	
	protected function get_header_template() {
		return new aw_php_template("document_modal", "header");
	}
	
	protected function get_content_template() {
		
		$template = new aw_php_template("document_modal", "content");
		
		return $template;
	}
	
	protected function get_footer_template() {
		return new aw_php_template("document_modal", "footer");
	}
}