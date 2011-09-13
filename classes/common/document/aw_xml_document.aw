<?php

/**
	Default charset is UTF-8. AW global charset is used if set and not otherwise instructed.
**/
class aw_xml_document extends aw_content_document
{
	protected $encoding = "UTF-8";

	public function __construct()
	{
		$this->encoding = aw_global_get("charset");
		return parent::__construct();
	}

	/** Sets page character encoding
		@attrib api=1 params=pos
		@param name type=string
		@comment
		@returns void
		@errors
	**/
	public function set_encoding($name)
	{
		$this->encoding = $name;
	}

	/** Returns character encoding name
		@attrib api=1 params=pos
		@comment
		@returns string
		@errors
	**/
	public function encoding()
	{
		return $this->encoding;
	}
}
