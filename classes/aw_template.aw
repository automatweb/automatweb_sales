<?php
/*
@comment
	The aw template engine
*/
class aw_template extends core
{
	/** The main template folder, current instance template directory. Must end with '/' **/
	public $template_dir = "";

	/** Application server template directory **/
	public $adm_template_dir = "";

	/** Current site template directory **/
	public $site_template_dir = "";

	/** Template variable values **/
	public $vars;
	public $sub_merge;
	public $template_filename;
	public $v2_name_map;

	private $debug_mode;

	/** whether to use eval() or preg_replace to render templates **/
	private $use_eval;
	public $v2_templates;
	private $v2_arr;
	private $v2_parent_map;
	private $c_templates;

	/** The derived class should always call this with the template folder as an argument
		@attrib api=1 params=name
		@param tpldir required type=string
			The template folder, relative to basedir/templates/
			No directory delimiter at end

		@example
			class a extends aw_template
			{
				function a()
				{
					$this->init(array("tpldir" => "applications/a"));
				}
			}

	**/
	function init($args = array())
	{
		parent::init($args);
		if (is_array($args))
		{
			if (method_exists($this, "tpl_init"))
			{
				$this->tpl_init(isset($args["tpldir"]) ? $args["tpldir"]."/" :  "");
			}
		}
		else
		{
			$this->tpl_init($args);
		}
		$this->debug_mode = aw_ini_get("debug_mode");
	}

	function tpl_init($basedir = "", $has_top_level_folder = false)
	{
		if (!isset($this->cfg) || !is_array($this->cfg))
		{
			aw_config_init_class($this);
		}

		if ($basedir and substr($basedir, 0, -1) !== "/")
		{
			$basedir .= "/";
		}

		$site_basedir = aw_ini_get("site_basedir");

		if (substr($basedir, 0, 1) !== "/" && !preg_match("/^[a-z]:/i", substr($basedir, 0, 2)))
		{
			if ($has_top_level_folder)
			{
				$this->template_dir = $site_basedir.$basedir;
				$this->adm_template_dir = AW_DIR . $basedir;
				$this->site_template_dir = $site_basedir.$basedir;
			}
			else
			{
				if (is_admin())
				{
					$this->template_dir = AW_DIR . "templates/{$basedir}";
				}
				else
				{
					$this->template_dir = $this->_find_site_template_dir() . "/{$basedir}";
				}
				$this->adm_template_dir = AW_DIR . "templates/{$basedir}";
				$this->site_template_dir = $this->_find_site_template_dir()."/{$basedir}";
			}
		}
		else
		{
			$this->template_dir = $basedir;
			$this->adm_template_dir = $basedir;
			$this->site_template_dir = $basedir;
		}

		$this->vars = array();
		$this->sub_merge = 0;

		$this->_init_vars();

		$this->use_eval = false;
	}

	private function _find_site_template_dir()
	{
		static $dir;
		if ($dir !== null)
		{
			return $dir;
		}

		if (is_admin())
		{
			return $dir = aw_ini_get("site_tpldir");
		}

		if (!aw_global_get("aw_init_done"))
		{
			return aw_ini_get("tpldir");
		}

		$sect = aw_global_get("section");
		if (!$this->can("view", $sect))
		{
			return aw_ini_get("tpldir");
		}

		$rv = aw_ini_get("tpldir");
		foreach(obj($sect)->path() as $path_item)
		{
			if ($path_item->prop("tpl_dir_applies_to_all") && $path_item->prop("tpl_dir"))
			{
				$rv = aw_ini_get("site_basedir").$path_item->prop("tpl_dir");
			}
		}
		return $dir = $rv;
	}

	function _init_vars()
	{
		// this comes from session.
		$this->vars = array(
			"self" => aw_global_get("PHP_SELF"),
			"ext"  => aw_ini_get("ext"),
			// not very random really
			"rand" => time(),
			"current_time" => time(),
			"status_msg" => aw_global_get("status_msg"),
			"baseurl" => aw_ini_get("baseurl"),
			"baseurl_ssl" => str_replace("http", "https", aw_ini_get("baseurl")),
			"cur_lang_id" => aw_global_get("lang_id"),
			"current_url" => urlencode(get_ru()),
			"charset" => aw_global_get("charset")
		);
	}

	/** sets the parse method for templates - "" or "eval"

		@attrib api=1

		@param method optional type=string
			defaults to ""

		@comment
			sets the template parsing method used by the current instance of the template parser.
			the available methods are:
			"" - the default - uses regular expressions to parse the template
			"eval" - uses php's eval() function to parse the template - this is faster, but slightly incompatible with the default

		@errors
			none

		@returns
			none

		@examples
			$tpl = new aw_template;
			$tpl->set_parse_method("eval");
			$tpl->read_template("aa.tpl");
			echo $tpl->parse(); // this is now slightly faster
	**/
	function set_parse_method($method = "")
	{
		if ($method === "eval")
		{
			$this->use_eval = true;
		}
	}

	/** resets the template parser to the default state - clears all variables and loaded templates
		@attrib api=1

		@errors
			none

		@returns
			none

		@examples
			$tpl = new aw_template;
			$tpl->set_parse_method("eval");
			$tpl->read_template("aa.tpl");
			echo $tpl->parse();
			$tpl->reset(); // now no variables are defined nor any template loaded
	**/
	function reset()
	{
		return $this->tpl_reset();
	}

	/** Resets all template content, but not the variable values
		@attrib api=1
	**/
	function tpl_reset()
	{
		unset($this->templates);
		$this->v2_templates = array();
		$this->v2_name_map = array();
		$this->v2_parent_map = array();
	}

	////
	// !Deprecated - use html::select instead
	function option_list($active,$array)
	{
		$res = "";
		if (is_array($array))
		{
			while(list($k,$v) = each($array))
			{
				$selected = ($active == $k) ? " selected " : "";
				$res .= sprintf("<option %s value='%s'>%s</option>\n",$selected,$k,$v);
			}
		}
		return $res;
//		return html::select(array("selected" => $active,"options" => $array));
	}

	////
	// !Deprecated - use html::select instead
	function multiple_option_list($active,$array)
	{
		$res = "";
		if (not(is_array($array)))
		{
			return false;
		};

		if (is_array($active))
		{
			$active = array_flip($active);
		}

		while(list($k,$v) = each($array))
		{
			$selected = isset($active[$k]) ? " selected " : "";
			$res .= sprintf("<option %s value='%s'>%s</option>\n",$selected,$k,$v);
		}
		return $res;
//		return html::select(array("selected" => $active,"options" => $array,"multiple" => 1));
	}

	////
	// !Deprecated - use html::select instead
	function mpicker($active, $array)
	{
		return $this->multiple_option_list($active, $array);
	}

	////
	// !Deprecated - use html::select instead
	function picker($active,$array)
	{
		return $this->option_list($active,$array);
	}

	/** reads the template whose name is given.
		@attrib api=1

		@param name type=string
			the name of the template file to load

		@param silent type=bool default=false
			if set to 1, no error is thrown if template is not found, false is returned instead

		@comment
			The full path to the template is assembled like this:
			if on the site side
			the tpldir setting from aw_ini_get(), then the folder given in the constructor to init() and finally, the file name gevn to this method. if the file is not found here, then the path
			basedir from ini file, templates folder, path given in init() and the file name is tested

			if in the admin interface
			basedir from ini file, templates folder, path given in init() and the file name is tested

			so basically, if on the site side, the site templates folder is checked and then the admin templates folder, so that you can override templates for each site

		@errors
			if no template file is found and the silent flag is not set, error is thrown

		@returns
			true if no error or silent flag not set, false if no template is found and silent flag is set

		@examples
			$tpl = new aw_template;
			$tpl->set_parse_method("eval");
			if ($tpl->read_template("aa.tpl", 1) === false)
			{
				echo "no template found!";
			}
	**/
	function read_template($name, $silent = false)
	{
		$this->template_filename = $this->template_dir . $name;

		if (!file_exists($this->template_filename))
		{
			$this->template_filename = $this->adm_template_dir . $name;
			if(function_exists("get_file_version"))
			{
				$this->template_filename = get_file_version($this->template_filename);
			}
		}

		// try to load a template from aw directory then
		if (file_exists($this->template_filename))
		{
			// validate filename, it has to be in either site_basedir or basedir
			if (!$this->_validate_pathname($this->template_filename))
			{
				if ($silent)
				{
					return false;
				}
				else
				{
					throw new awex_bad_file_path($this->template_filename);
				}
			}
			else
			{
				$this->_record_template_load($this->template_filename);
				$retval = $this->read_tpl(file($this->template_filename));
			}
		}
		else
		{
			if ($silent)
			{
				$retval = false;
			}
			else
			{
				$e = new awex_tpl_not_found(sprintf("Template '%s' resolved to '%s' not found", $name, $this->template_filename));
				$e->tpl = $this->template_filename;
			}
		}
		return $retval;
	}

	/** reads a template from the given string
		@attrib api=1

		@param source required
			the template content

		@errors
			none

		@returns
			true

		@examples
			$tpl = new aw_template;
			$str = "{VAR:foo} is here";
			$tpl->us_template($str);
			$tpl->vars(array(
				"foo" => "Mr. T"
			));
			echo $tpl->parse();
	**/
	public function use_template($source)
	{
		$slines = explode("\n",$source);
		return $this->read_tpl($slines);
	}

	/** reads a template from a file from the admin template folder
		@attrib api=1 params=pos

		@param name required type=string
			name of the file to load

		@param silent optional
			optional - is set to 1, no errors are thrown, instead false is returned on error

		@errors
			if file is not found, error is thrown

		@returns
			false if silent == 1 and file is not found, else true

		@examples
			$tpl = new aw_template;
			$tpl->read_adm_template("foo.tpl");
			$tpl->vars(array(
				"foo" => "Mr. T"
			));
			echo $tpl->parse();
	**/
	public function read_adm_template($name,$silent = 0)
	{
		$retval = true;
		$this->template_filename = $this->adm_template_dir.$name;
		if(function_exists("get_file_version"))
		{
			$this->template_filename = get_file_version($this->template_filename);
		}
		if (file_exists($this->template_filename))
		{
			$this->_record_template_load($this->template_filename);
			$retval = $this->read_tpl(file($this->template_filename));
		}
		else
		{
			if ($silent)
			{
				$retval = false;
			}
			else
			{
				// raise_error drops out, therefore $retval has no meaning here
				$this->raise_error("ERR_TPL_NOTPL", sprintf(t("Template '%s' not found"), $this->template_filename),true);
			}
		}
		return $retval;
	}

	/**  reads the given template from the site's template folder even if the user is in the admin interface
		@attrib api=1 params=pos

		@param name type=string
			name of the file to load

		@param silent type=bool default=false
			if set to 1, no errors are thrown, instead false is returned on error

		@errors
			if file is not found, error is thrown, unless silent flag is given

		@returns
			false if silent == 1 and file is not found, else true

		@examples
			$tpl = new aw_template;
			$tpl->read_site_template("foo.tpl");
			$tpl->vars(array(
				"foo" => "Mr. T"
			));
			echo $tpl->parse();
	**/
	public function read_site_template($name,$silent = false)
	{
		$retval = true;
		$this->template_filename = $this->site_template_dir.$name;
		if (file_exists($this->template_filename))
		{
			$this->_record_template_load($this->template_filename);
			$retval = $this->read_tpl(file($this->template_filename));
		}
		else
		{
			if ($silent)
			{
				$retval = false;
			}
			else
			{
				// raise_error drops out, therefore $retval has no meaning here
				$this->raise_error("ERR_TPL_NOTPL", sprintf(t("Template '%s' not found"), $this->template_filename),true);
			}
		}
		return $retval;
	}

	/** tries to read the given template from the site's template folder even if the user is in the admin interface and if not found in the site folder, tries to read it from the admin templates folder
		@attrib api=1 params=pos

		@param name type=string
			name of the file to load

		@param silent type=bool default=false
			if set to 1, no errors are thrown, instead false is returned on error

		@errors
			if file is not found, error is thrown, unless silent flag is given

		@returns
			false if silent == 1 and file is not found, else true

		@examples
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			$tpl->vars(array(
				"foo" => "Mr. T"
			));
			echo $tpl->parse();
	**/
	public function read_any_template($name, $silent = false)
	{
		$this->template_filename = $this->site_template_dir.$name;
		$this->template_filename = trim($this->template_filename);
		if (file_exists($this->template_filename))
		{
			$this->_record_template_load($this->template_filename);
			$retval = $this->read_tpl(file($this->template_filename));
		}
		else
		{
			$this->template_filename = $this->adm_template_dir.$name;
			if(function_exists("get_file_version"))
			{
				$this->template_filename = get_file_version($this->template_filename);
			}
			if (file_exists($this->template_filename))
			{
				$this->_record_template_load($this->template_filename);
				$retval = $this->read_tpl(file($this->template_filename));
			}
			else
			{
				if ($silent)
				{
					$retval = false;
				}
				else
				{
					// raise_error drops out, therefore $retval has no meaning here
					$this->raise_error("ERR_TPL_NOTPL",sprintf(t("Template '%s' not found in admin or site folder"), $this->template_filename),true);
				};
			}
		}
		return $retval;
	}

	/** checks if a SUB with the name given exits in the currently loaded template
		@attrib api=1

		@param name type=string
			name of the SUB to check for

		@errors
			none

		@returns
			true if the SUB is in the current template, false if no template is loaded or no such SUB exists

		@examples
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			if ($this->is_template("FOO"))
			{
				echo $this->parse("FOO");
			}
	**/
	function is_template($name)
	{
		$retval = isset($this->v2_name_map[$name]);
		return $retval;
	}

	/** checks if the currently loaded template's given SUB contains a VAR with the name given
		@attrib api=1

		@param varname required type=string
			name of the VAR to check for

		@param tplname optional type=string
			defaults to MAIN - the SUB to check for the VAR

		@errors
			none

		@returns
			true if the VAR is in the given SUB, false if no template is loaded or no such VAR exists

		@examples
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			if ($this->template_has_var("allah"))
			{
				$this->vars(array(
					"allah" => "akhbar"
				));
			}
	**/
	function template_has_var($varname,$tplname = "MAIN")
	{
		return strpos($this->v2_templates[$tplname],"{VAR:" . $varname . "}") !== false;
	}

	/** checks if the template contains the given variable. checks the complete template. slow
		@attrib api=1

		@param varname type=string
			name of the VAR to check for

		@comment
			<b>PERFORMANCE WARNING: this is quite slow, do not use often </b>

		@errors
			none

		@returns
			true if the VAR is in the current template, false if no template is loaded or no such VAR exists

		@examples
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			if ($this->template_has_var_full("allah"))
			{
				$this->vars(array(
					"allah" => "akhbar"
				));
			}
	**/
	function template_has_var_full($varname, $partial = false)
	{
		$tmp = join("\n", $this->v2_arr);
		if ($partial)
		{
			return strpos($tmp,"{VAR:" . $varname) !== false;
		}
		else
		{
			return strpos($tmp,"{VAR:" . $varname . "}") !== false;
		}
	}

	/** checks if the SUB $parent is the immediate parent of the SUB $tpl
		@attrib api=1

		@param tpl required type=string
			the SUB whose parent SUB you want to check for

		@param parent required type=string
			the name of the SUB that should be the parent of the SUB $tpl

		@errors
			none

		@returns
			true if $parent is parent SUB pf $tpl SUB, false if not

		@examples
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			if ($this->is_parent_tpl("FOO", "BUJAKA"))
			{
				$this->vars(array(
					"FOO" => $this->parse("BUJAKA.FOO")
				));
				$this->vars(array(
					"BUJAKA" => $this->parse("BUJAKA")
				));
			}
			else
			{
				$this->vars(array(
					"BUJAKA" => $this->parse("FOO.BUJAKA")
				));
				$this->vars(array(
					"FOO" => $this->parse("FOO")
				));
			}
	**/
	function is_parent_tpl($tpl,$parent)
	{
		if (!isset($this->v2_parent_map[$tpl]))
		{
			return "" == $parent;
		}
		else
		{
			return $this->v2_parent_map[$tpl] == $parent;
		}
	}

	/** returns the name if the immediate parent SUB of the given SUB
		@attrib api=1

		@errors
			none

		@param tpl required type=string
			the SUB whose parent SUB you want

		@returns
			name if the parent SUB of the given SUB, null if the given SUB has no parent or no such SUB exists

		@examples
			-- template foo.tpl
			<!-- SUB: BAR -->

			<!-- SUB: FOO -->
			<!-- END SUB: FOO -->
			<!-- END SUB: BAR -->

		-- code
			$tpl = new aw_template;

			$tpl->read_any_template("foo.tpl");

			echo $tpl->get_parent_template("FOO"); // returns BAR
	**/
	function get_parent_template($tpl)
	{
		return $this->v2_parent_map[$tpl];
	}

	/** returns an array of names of the parent SUB's of the given SUB. this can return several names if there are several SUB's with the same name under different parents
		@attrib api=1

		@errors
			none

		@param tpl required type=string
			the SUB whose parent SUB's you want

		@returns
			name if the parent SUB's of the given SUB, null if the given SUB has no parent or no such SUB exists

		@examples
			-- template foo.tpl
			<!-- SUB: BAR -->
				<!-- SUB: FOO -->
				<!-- END SUB: FOO -->
			<!-- END SUB: BAR -->

			<!-- SUB: BAZ -->
				<!-- SUB: FOO -->
				<!-- END SUB: FOO -->
			<!-- END SUB: BAZ -->

			-- code
			$tpl = new aw_template;

			$tpl->read_any_template("foo.tpl");

			$res = $tpl->get_parent_templates("FOO"); // returns array("BAR", "BAZ")
	**/
	function get_parent_templates($tpl)
	{
		$ret = array();
		foreach($this->v2_templates as $fqname => $tt)
		{
			// if fqname contains the needed template,
			// get the parent
			$parts = explode(".", $fqname);
			foreach($parts as $idx => $part)
			{
				if ($part == $tpl)
				{
					$ret[] = $parts[$idx-1];
					break;
				}
			}
		}
		return $ret;
	}

	/** checks if the SUB $tpl is a child SUB of the $parent SUB. checks the full chain of SUB's
		@attrib api=1

		@param tpl required type=string
			the SUB whose parent SUB's you want to check

		@param parent required type=string
			the name of the parent SUB to check

		@errors
			none

		@returns
			true if the $tpl SUB has a parent SUB by the name $parent

		@examples
			-- template foo.tpl
			<!-- SUB: AHH -->
				<!-- SUB: BAR -->
					<!-- SUB: FOO -->
					<!-- END SUB: FOO -->
				<!-- END SUB: BAR -->
			<!-- END SUB: AHH -->

			-- code
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			$res = $tpl->is_in_parents_tpl("FOO", "AHH"); // returns true
	**/
	function is_in_parents_tpl($tpl, $parent)
	{
		$fp = $this->v2_name_map[$tpl];
		if (strpos($fp, $parent) === false)
		{
			return false;
		}
		return true;
	}

	/** imports variables into the current template, overwriting the previous variables of the same name. escapes php code
		@attrib api=1

		@param params required type=array
			array of name => value pairs that are used as variable name => variable value

		@errors
			none

		@returns
			none

		@examples
			-- template foo.tpl
			{VAR:allah}

			-- code
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			$tpl->vars(array(
				"allah" => "akhbar"
			));
			echo $tpl->parse(); // prints akhbar
	**/
	function vars($params)
	{
		foreach($params as $k => $v)
		{
			$this->vars[$k] = str_replace("<?php", "&lt;?php", $v);
		}
	}

	/** imports variables into the current template, overwriting the previous variables of the same name. DOES NOT escape php code
		@attrib api=1

		@param params required type=array
			array of name => value pairs that are used as variable name => variable value

		@errors
			none

		@returns
			none

		@examples
			-- template foo.tpl
			{VAR:allah}

			-- code
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			$tpl->vars(array(
				"allah" => "akhbar"
			));
			echo $tpl->parse(); // prints akhbar
	**/
	function vars_safe($params)
	{
		$this->vars = array_merge($this->vars,$params);
	}

	/** imports variables into the current template, appending to the previous variables of the same name
		@attrib api=1

		@param params required type=array
			array of name => value pairs that are used as variable name => variable value

		@errors
			none

		@returns
			none

		@examples
			-- template foo.tpl
			{VAR:allah}

			-- code
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			$tpl->vars(array(
				"allah" => "allah"
			));
			$tpl->vars_merge(array(
				"allah" => " akhbar"
			));
			echo $tpl->parse(); // prints allah akhbar
	**/
	function vars_merge($params)
	{
		while(list($k,$v) = each($params))
		{
			$this->vars[$k] .= $v;
		}
	}

	/** replaces variables with their values and returns the content of the given sub as parsed text
		@attrib api=1

		@param object optional type=string
			optional, defaults to the main sub - name of the SUB to parse

		@errors
			none

		@returns
			text of the sub, with variables replaced by their assigned values

		@examples
			-- template foo.tpl
			<!-- SUB: BOO -->
				{VAR:allah}
			<!-- END SUB: BOO -->

			-- code
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			$tpl->vars(array(
				"allah" => "akhbar"
			));
			$tpl->vars(array(
				"BOO" => $tpl->parse("BOO")
			));
			echo $tpl->parse(); // prints akhbar
	**/
	function parse($object = "MAIN")
	{
		$tmp = isset($this->v2_name_map[$object]) ? $this->v2_name_map[$object] : "";
		$val = isset($this->v2_templates[$tmp]) ? $this->v2_templates[$tmp] : "";
		if ($this->use_eval)
		{
			$cval = ifset($this->c_templates, $tmp);
			$vars = $this->vars;
			eval("\$src=\"" . $cval . "\";");
		}
		else
		{
			$src = localparse($val, $this->vars);
		}

		// v6tame selle maha ka .. this is NOT a good place for that
		//aw_session_del("status_msg", true);

		if ($this->sub_merge == 1)
		{
			if (!isset($this->vars[$object]))
			{
				$this->vars[$object] = "";
			}
	   		$this->vars[$object] .= $src;
		}

		if (aw_ini_get("debug_mode") === "1" && isset($_GET["TPL"]) && $_GET["TPL"] === "2" && $object === "MAIN")
		{
			print "Available variables for: " . $this->template_filename;
			print "<pre>";
			print_r($this->vars);
			print "</pre>";
		}

		return $src;
	}

	////
	// !$arr - template content, array of lines of text
	function read_tpl($arr)
	{
		if (isset($_GET["TPL"]) and "1" === $_GET["TPL"])
		{
			// this will add link to documentation
			$pos = strpos($this->template_filename, aw_ini_get("tpldir"));
			$tpl_doc_link = ($pos === false) ? str_replace(aw_ini_get('basedir')."templates/", "http://dev.struktuur.ee/wiki/index.php/Templates", $this->template_filename) :
			str_replace(aw_ini_get("tpldir"), "http://dev.struktuur.ee/wiki/index.php/Templates", $this->template_filename);
			aw_global_set("TPL=1", aw_global_get("TPL=1").'$_aw_tpl_equals_1["'.$this->template_filename.'"]=array("link"=>"'.$tpl_doc_link.'");$_aw_tpl_equals_1_counter[]="'.$this->template_filename.'";');
		}

		$this->tpl_reset();
		if (is_array($arr))
		{
			reset($arr);
			$this->v2_arr = $arr;
			$this->req_read_tpl("MAIN","MAIN","");
		}
		return true;
	}

	private function req_read_tpl($fq_name,$cur_name,$parent_name)
	{
		$cur_src = "";
		$this->v2_parent_map[$cur_name] = $parent_name;
		while (list(,$line) = each($this->v2_arr))
		{
			// this check allows us to avoid a LOT of preg_match calls,
			// those are probably expensive. I don't care what the profiler
			// says, just think about how a regexp engine works. Simple
			// string comparing is ALWAYS faster. --duke
			if (strpos($line,"<!--") === false)
			{
				$cur_src.=$line;
			}
			else
			if (preg_match("/<!-- SUB: (.*) -->/S",$line, $mt))
			{
				// start new subtemplate
				$this->req_read_tpl($fq_name.".".$mt[1],$mt[1],$cur_name);
				// add the var def for this sub to this template
				$cur_src.="{VAR:".$mt[1]."}";
			}
			else
			if (preg_match("/^(.*)<!-- END SUB: (.*) -->/S",$line, $mt))
			{
				/* This avoid obligatory newline after each block, making templates more flexible..
				/use eg
				|<!-- SUB: file-->
				|{VAR:filecontents}
				|EOF-with-no-linebreak<!-- END SUB: file -->
				*/
				$cur_src .= $mt[1];
				// found an end of this subtemplate,
				// finish and exit
				$this->v2_templates[$fq_name] = $cur_src;
				if ($this->use_eval)
				{
					$xsrc = str_replace("\"","\\\"",$cur_src);
					$this->c_templates[$fq_name] = preg_replace("/{VAR:(.+?)}/S","\".(isset(\$vars['\$1']) ? \$vars['\$1'] : null).\"",$xsrc);
				};

				$this->templates[$cur_name] = $cur_src;	// ugh, this line for aliasmanager and image_inplace compatibility :(
				$this->v2_name_map[$cur_name] = $fq_name;
				$this->v2_name_map[$parent_name.".".$cur_name] = $fq_name;
				$this->v2_name_map[$fq_name] = $fq_name;
				return;
			}
			else
			{
				// just add this line
				$cur_src.=$line;
			}
		}
		$this->v2_templates[$fq_name] = $cur_src;
		if ($this->use_eval)
		{
			$xsrc = str_replace("\"","\\\"",$cur_src);
			$this->c_templates[$fq_name] = preg_replace("/{VAR:(.+?)}/S","\".(isset(\$vars['\$1']) ? \$vars['\$1'] : null).\"",$xsrc);
		};

		$this->templates[$cur_name] = $cur_src;	// ugh, this line for aliasmanager and image_inplace compatibility :(
		$this->v2_name_map[$cur_name] = $fq_name;
		$this->v2_name_map[$fq_name] = $fq_name;
		return;
	}

	/** Retrieves a list of SUB's matching a regexp
		@attrib api=1

		@param regex required type=string
			perl-compatible regular expression to match with SUB names

		@comment
			don't forget to add braces () to the regex or you won't get any results

		@errors
			none

		@returns
			array of the names of the SUB's matching the regex

		@examples
			-- template foo.tpl

			<!-- SUB: BOO_FOO -->
				{VAR:allah}
			<!-- END SUB: BOO_FOO -->

			-- code
			$tpl = new aw_template;

			$tpl->read_any_template("foo.tpl");

			$tpls = $tpl->get_subtemplates_regex("BOO_(\w*)"; // returns array("FOO")
			$tpls = $tpl->get_subtemplates_regex("(BOO_\w*)"; // returns array("BOO_FOO")
	**/
	function get_subtemplates_regex($regex)
	{
		$tpls = array_keys($this->v2_name_map);
		$res = array();
		foreach($tpls as $key)
		{
			if (preg_match("/^$regex/",$key,$matches))
			{
				$res[] = $matches[1];
			};
		};
		return array_unique($res);
	}

	/** returns the un-parsed content of the given SUB
		@attrib api=1

		@param name required type=string
			name of the SUB whose content to return

		@errors
			none

		@returns
			the un-parsed content of the given SUB

		@examples
			-- template foo.tpl
			<!-- SUB: BOO_FOO -->
				{VAR:allah}
			<!-- END SUB: BOO_FOO -->

			-- code
			$tpl = new aw_template;
			$tpl->read_any_template("foo.tpl");
			$str = $tpl->get_template_string("BOO_FOO"); // returns "{VAR:allah}"
	**/
	function get_template_string($name)
	{
		$tmp = isset($this->v2_name_map[$name]) ? $this->v2_name_map[$name] : "";
		return isset($this->v2_templates[$tmp]) ? $this->v2_templates[$tmp] : "";
	}

	private function _validate_pathname($path)
	{
		$pt = str_replace("\\", "/", realpath($path));
		$sd = str_replace("\\", "/", realpath(aw_ini_get("site_basedir")));
		$bd = str_replace("\\", "/", realpath(aw_ini_get("basedir")));

		if (substr($pt, 0, strlen($sd)) == $sd || substr($pt, 0, strlen($bd)) == $bd)
		{
			return true;
		}
		return false;
	}

	private function _record_template_load($fn)
	{

		return;
		/*
		if (strpos($fn, aw_ini_get("site_basedir")) !== false)
		{
			$f = fopen(aw_ini_get("site_basedir")."files/template_log_".date("Y_m").".log", "a");
			fwrite($f, time()."|".get_ru()."|".$fn."\n");
			fclose($f);
		}
		*/
	}
}

/** Throw this when you get a file name that is invalid. or should not be read. **/
class awex_bad_file_path extends aw_exception
{
	public $path;

	function __construct($path)
	{
		$this->path = $path;
	}
}

/** Generic template engine exception **/
class awex_tpl extends aw_exception
{
	public $tpl = "";
}

/** Template not found **/
class awex_tpl_not_found extends awex_tpl {}
