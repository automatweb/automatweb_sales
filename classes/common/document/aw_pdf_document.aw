<?php

class aw_pdf_document extends aw_content_document
{
	private $pdf_content = "";
	private $pdf_author = "";

	public function __construct()
	{
		return parent::__construct();
	}

	/** Sets document pdf content
		@attrib api=1 params=pos
		@param str type=string
		@comment
		@returns void
		@errors
	**/
	public function set_content($str)
	{
		$this->pdf_content = $str;
	}

	public function render()
	{
		return $this->pdf_content;
	}

	/**
		@attrib api=1 params=pos
		@param name type=string
		@comment
		@returns void
		@errors
	**/
	public function set_author($name)
	{
		$this->pdf_author = $name;
	}
}
