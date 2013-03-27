<?php

class aw_modal implements orb_public_interface {
	
	protected $request;
	
	public function set_request(aw_request $request) {
		$this->request = $request;
	}
	
	public function parse() {
		$template = new aw_php_template("aw_modal", "default");
		
		if (is_callable(array($this, "get_header_template"))) {
			$template->bind($this->get_header_template(), "header");
		}

		if (is_callable(array($this, "get_content_template"))) {
			$template->bind($this->get_content_template(), "content");
		}

		if (is_callable(array($this, "get_footer_template"))) {
			$template->bind($this->get_footer_template(), "footer");
		}
		
		automatweb::$result->set_data($template->render());
		automatweb::$instance->http_exit();
	}
	
}