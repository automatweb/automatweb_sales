<?php

class aw_xhtml_document extends aw_xml_document
{
	protected $javascript_files_in_header = array();
	protected $javascript_files_in_footer = array();
	protected $javascript_in_header = array();
	protected $javascript_in_footer = array();
	protected $stylesheets = array();
	protected $style = array();
	protected $main_template;
	protected $vars = array(
		"title" => "AutomatWeb document",
		"charset" => "UTF-8",
		"lang" => "en",
		"lang_id" => "2",
		"meta_description" => "",
		"meta_keywords" => "",
		"meta_abstract" => "",
		"meta_author" => "",
		"meta_copyright" => "",
		"meta_revisit_after" => "",
		"meta_distribution" => "",
		"meta_robots" => "",
		"meta_rating" => "",
		"meta_generator" => "",
		"meta_content_language" => "",
		"meta_pragma" => "",
		"meta_refresh" => "",
		"meta_expires" => "",
		"meta_window_target" => "",
		"style_files" => "",
		"style" => "",
		"javascript_files_header" => "",
		"javascript_files_footer" => "",
		"javascript_header" => "",
		"javascript_footer" => ""
	);

	public function __construct()
	{
		parent::__construct();
		$this->main_template = new aw_php_template("aw_xhtml_document", "aw_xhtml_document");
		$this->vars["charset"] = $this->encoding;
	}

	/**
		@attrib api=1 params=pos
		@param file type=string
			CSS stylesheet file url.
		@comment
		@returns void
		@errors
	**/
	public function add_stylesheet($file)
	{
		$this->stylesheets[] = $file;
	}

	/**
		@attrib api=1 params=pos
		@param css type=string
			CSS code
		@comment
		@returns void
		@errors
	**/
	public function add_style($css)
	{
		$loader = get_caller_str();
		$this->style[] = "/* CSS loaded by {$loader} */\n" . trim($css);
	}

	/**
		@attrib api=1 params=pos
		@param file type=string
			Javascript file url.
		@param position type=string default="header" set="header"|"footer"
			Whether to add to head section or at the end of document
		@comment
		@returns void
		@errors
	**/
	public function add_javascript_file($file, $position = "header")
	{
		if ("header" === $position)
		{
			$this->javascript_files_in_header[] = $file;
		}
		elseif ("footer" === $position)
		{
			$this->javascript_files_in_footer[] = $file;
		}
		else
		{
		}
	}

	/**
		@attrib api=1 params=pos
		@param code type=string
			Javascript code
		@param position type=string default="header" set="header"|"footer"
			Whether to add to head section or at the end of document
		@comment
		@returns void
		@errors
	**/
	public function add_javascript($code, $position = "header")
	{
		$loader = get_caller_str();
		if ("header" === $position)
		{
			$this->javascript_in_header[] = "/* Javascript code loaded by {$loader} */\n" . trim($code);
		}
		elseif ("footer" === $position)
		{
			$this->javascript_in_footer[] = "/* Javascript code loaded by {$loader} */\n" . trim($code);
		}
		else
		{
		}
	}

	/** Sets a template to be the document content
		@attrib api=1 params=pos
		@param template type=aw_php_template
		@returns void
		@errors
	**/
	public function set_content_template(aw_php_template $template)
	{
		$this->main_template->bind($template, "content");
	}

	/** Sets xhtml title element content
		@attrib api=1 params=pos
		@param value type=string
			Plain one line text
		@comment
		@returns void
		@errors
	**/
	public function set_title($value)
	{
		$this->vars["title"] = $value;
	}

	public function render()
	{
		$this->main_template->set_vars($this->vars);
		$this->main_template->replace_vars(array(
			"style_files" => $this->stylesheets,
			"style" => implode("\n\n", $this->style),
			"javascript_files_header" => $this->javascript_files_in_header,
			"javascript_files_footer" => $this->javascript_files_in_footer,
			"javascript_header" => implode("\n\n", $this->javascript_in_header),
			"javascript_footer" => implode("\n\n", $this->javascript_in_footer)
		));
		return $this->main_template->render();
	}
}
