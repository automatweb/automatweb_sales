<?php

// a static constructor
aw_php_template::construct();

class aw_php_template
{
	const TEMPLATE_FILE_EXT = ".php";

	private $tpl_file = "";
	private $site_tpl_file = "";
	private $default_tpl_file = "";
	private $lang_id = AW_REQUEST_UI_LANG_ID;

	private $vars = array();
	private static $default_vars = array();

	private $bound_templates = array();

	public function __construct($class, $name, $lang_id = AW_REQUEST_UI_LANG_ID)
	{
		$path = class_index::get_class_path($class);
		$file = "{$path}{$name}" . self::TEMPLATE_FILE_EXT;
		$site_tpl_file = aw_ini_get("site_tpldir") . $file;
		$default_tpl_file = AW_DIR . "templates/" . $file;

		if (is_readable($site_tpl_file))
		{
			$this->tpl_file = $this->site_tpl_file = $site_tpl_file;
		}
		elseif (is_readable($default_tpl_file))
		{
			$this->tpl_file = $this->default_tpl_file = $default_tpl_file;
		}
		else
		{
			throw new awex_tpl_not_found("Class '{$class}' template '{$name}' file '{$default_tpl_file}' not readable");
		}

		// load template translations
		aw_translations::load("{$class}.tpl.{$name}", $lang_id);
		$this->lang_id = $lang_id;
	}

	public static function construct()
	{
		self::$default_vars = array(
			"self" => aw_global_get("PHP_SELF"),
			"ext"  => AW_FILE_EXT,
			"rand" => time(),
			"current_time" => time(),
			"status_msg" => aw_global_get("status_msg"),
			"baseurl" => aw_ini_get("baseurl"),
			"baseurl_ssl" => str_replace("http", "https", aw_ini_get("baseurl")),
			"cur_lang_id" => aw_global_get("lang_id"),
			"cur_lang_code" => aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_lc") : aw_global_get("LC"),
			"current_url" => urlencode(get_ru()),
			"charset" => languages::USER_CHARSET
		) + $GLOBALS["cfg__default__short"];
	}

	/** Sets variable
		@attrib api=1 params=pos
		@param name type=string
		@param value type=string
		@comment
		@returns void
		@errors
	**/
	public function set_var($name, $value)
	{
		$this->vars[$name] = $value;
	}

	/** Replaces current variables with given array
		@attrib api=1 params=pos
		@param vars type=array
			Associative array of template variables
		@comment
		@returns void
		@errors
			throws awex_tpl if parameter $vars is invalid
	**/
	public function set_vars($vars)
	{
		$this->_check_vars_param($vars);
		$this->vars = $vars;
	}

	/** Adds given variable array to current
		@attrib api=1 params=pos
		@param vars type=array
			Associative array of template variables
		@comment
			Existing variables are not overwritten
		@returns void
		@errors
			throws awex_tpl if parameter $vars is invalid
	**/
	public function add_vars($vars)
	{
		$this->_check_vars_param($vars);
		$this->vars += $vars;
	}

	/** Adds given variable array to current, replaces existing
		@attrib api=1 params=pos
		@param vars type=array
			Associative array of template variables
		@comment
			Existing variables with same name are overwritten
		@returns void
		@errors
			throws awex_tpl if parameter $vars is invalid
	**/
	public function replace_vars($vars)
	{
		$this->_check_vars_param($vars);
		$this->vars = $vars + $this->vars;
	}

	/** Clears all current variables
		@attrib api=1 params=pos
		@comment
		@returns void
		@errors
	**/
	public function clear_vars()
	{
		$this->vars = array();
	}

	/**
		@attrib api=1 params=pos
		@param vars type=array default=array()
			If not specified, variables set by add_vars() etc. are used
		@comment
		@returns string
		@errors
			throws awex_tpl if parse fails
	**/
	public function render($vars = array())
	{
		// get variables
		$vars = empty($vars) ? $this->vars : $vars;

		if (!is_array($vars))
		{
			throw new awex_tpl("Invalid parameter");
		}

		$vars += self::$default_vars ;
		$vars += array("lang_id" => $this->lang_id);

		// include bound templates
		foreach ($this->bound_templates as $name => $template)
		{
			$vars[$name] = $template->render();
		}

		// parse
		set_error_handler(array($this, "parse_error_handler"), error_reporting());
		extract($vars);
		ob_start();
		require $this->tpl_file;
		$r = ob_get_contents();
		ob_end_clean();
		restore_error_handler();
		return $r;
	}

	/** Binds another template to this template's variable with $name
		@attrib api=1 params=pos
		@param template type=aw_php_template
			Template object to bind
		@param name type=string
			Name to bind to
		@comment
			Bind overwrites all other type variables of same name with given template output.
		@returns
		@errors
	**/
	public function bind(aw_php_template $template, $name)
	{
		$this->bound_templates[$name] = $template;
	}

	/** Find variables used in this template
		@attrib api=1 params=pos
		@comment
			A debugging oriented method
		@returns array
			array(
				"$var_name" => array(line_the_var_is_used_on_1, line_used_on_2, ...)
			)
			Each usage info array count corresponds to total number the variable is used in template
		@errors
	**/
	public function list_variables()
	{
		$tpl = file_get_contents($this->tpl_file);
		$tokens = token_get_all($tpl);
		$variables = array();
		foreach ($tokens as $key => $data)
		{
			if (T_VARIABLE === $data[0])
			{
				$variables[$data[1]][] = $data[2];
			}
		}
		return $variables;
	}

	/** Find variables used in this template and return their names
		@attrib api=1 params=pos
		@comment
			A debugging oriented method
		@returns string
		@errors
	**/
	public function list_variable_names()
	{
		$variables = array_keys($this->list_variables());
		$variables = implode("\n", $variables);
		return $variables;
	}

	private function _check_vars_param($vars)
	{
		if (empty($vars) or !is_array($vars))
		{
			throw new awex_tpl("Invalid parameter");
		}
	}

	/**
		@attrib api=1 params=pos
		@returns string
	**/
	public function __toString()
	{
		return $this->render();
	}

	public function parse_error_handler()
	{
		return false;//TODO
	}
}
