<?php

//TODO: kommenteerida ja dokumenteerida, milleks see klass on
class active_page_data implements orb_public_interface
{
	const LAYER_OPEN = 1;
	const LAYER_CLOSED = 2;

	private $req;
	private static $load_javascript_files = array(
		"head" => array(),
		"bottom" => array()
	);
	private static $additional_javascript_code = array(
		"head" => "",
		"bottom" => ""
	);

	/** Sets orb request to be processed by this object
		@attrib api=1 params=pos
		@param request type=aw_request
		@returns void
	**/
	public function set_request(aw_request $request)
	{
		$this->req = $request;
	}

	public static function get_active_path()
	{
		static $cur_path = false;
		if (!is_array($cur_path))
		{
			if (!aw_global_get("section"))
			{
				$cur_path = array();
			}
			else
			{
				$so = obj(aw_global_get("section"));
				$o_path = $so->path();
				$cur_path = array();
				foreach($o_path as $o)
				{
					$cur_path[] = $o->id();
				}
			}
		}
		return is_array($cur_path) ? $cur_path : array();
	}

	public static function get_text_content($txt = "")
	{
		static $txt_content;
		if ($txt != "")
		{
			$txt_content = $txt;
		}
		return $txt_content;
	}

	public static function get_active_section()
	{
		static $active_section = -1;
		if ($active_section == -1)
		{
			$active_section = aw_global_get("section");
		}
		return $active_section;
	}

	public static function add_site_css_style($stylid)
	{
		$styles= aw_global_get("__aw_site_styles");
		$styles[$stylid] = $stylid;
		aw_global_set("__aw_site_styles", $styles);
	}

	public static function add_serialized_css_style($text)
	{
		$serialized_styles = aw_global_get("__aw_serialized_styles");
		$serialized_styles[] = $text;
		aw_global_set("__aw_serialized_styles", $serialized_styles);
	}

	public static function on_shutdown_get_styles(&$text)
	{
		$ret = "";
		$styles = new aw_array(aw_global_get("__aw_site_styles"));
		if ($styles->count() > 0)
		{
			$css = get_instance(CL_CSS);

			$ret = "";
			foreach($styles->get() as $stylid)
			{
				if (object_loader::instance()->can("view", $stylid))
				{
					$ret .= $css->get_style_data_by_id($stylid);
				}
			}
		}

		$serialized_styles = new aw_array(aw_global_get("__aw_serialized_styles"));
		foreach($serialized_styles->get() as $styletext)
		{
			$ret .= $styletext;
		}

		if ($ret != "")
		{
			$ret = "<style type=\"text/css\">".$ret."</style>";
		}
		if (stristr($text, "</head>") !== false)
		{
			$text = str_ireplace("</head>", $ret."</head>", $text);
		}
		else
		{
			$text .= $ret;
		}
		return $text;
	}

	/** Returns page javascript part as defined by executed applications
		@attrib api=1 params=pos
		@param pos type=string default="head" set="bottom"|"head"
		@returns string
		@errors none
	**/
	public static function get_javascript($pos = "head")
	{
		$text = "";

		if ("head" === $pos or "bottom" === $pos)
		{
			// add loaded files
			$baseurl = aw_ini_get("baseurl");
			foreach (self::$load_javascript_files[$pos] as $file => $tmp)
			{
				$text .= "<script language=\"Javascript\" type=\"text/javascript\" src=\"{$baseurl}automatweb/js/{$file}\"></script>\n";
			}

			// add separate code
			$text .= "<script language=\"Javascript\" type=\"text/javascript\">\n" . self::$additional_javascript_code[$pos] . "</script>\n";
		}

		return $text;
	}

	/** Returns page javascript part as defined by executed applications
		@attrib api=1 params=pos
		@param js type=string
			Javascript code to be added after separate loaded javascript files
		@param pos type=string default="head" set="bottom"|"head"
		@returns string
		@errors none
	**/
	public static function add_javascript($code, $pos = "head")
	{
		self::$additional_javascript_code[$pos] .= ($code . "\n\n");
	}

	/** Loads javascript file
		@attrib api=1 params=pos

		@param file type=string
			Javascript filename/path to include. The root directory for the files is "$automatweb_site/automatweb/js/".

		@param position type=string default="head" set="bottom"|"head"
			Specifies the position, where the javascript file will be linked in page. Possible values are "head" (default) and "bottom". If the position is "head", then the file will be linked in bbetween page head tags. If it is set to "bottom", the it will be linked in at the bottom of the page.

		@comment
			The function allows you to load javascript file from code. It will be linked in between head tags, or at the bottom of the page, as specified
		@examples
			active_page_data::load_javascript("my/dir/my_javascriptfile.js"); // the file will be included between head tags
			active_page_data::load_javascript("my_custom_javascript_file.js", "bottom"); // the file will be included at the bottom of the page
	**/
	public static function load_javascript($file, $pos = "head")
	{
		if (!isset(self::$load_javascript_files[$pos][$file])) // assuming that some scripts may be needed to be included both at head and bottom
		{
			self::$load_javascript_files[$pos][$file] = null;
		}
	}

	/** returns the state of the layer
		@attrib api=1 params=pos
		@param class type=string
		@param group type=string
		@param layout type=string
		@return int
			0 - no layer state info found, active_page_data::LAYER_OPEN, active_page_data::LAYER_CLOSED
	**/
	public static function get_layer_state($class, $group, $layout)
	{
		$state = 0;
		$uid = aw_global_get("uid");

		if ($uid and $class and $layout)
		{
			$object_loader = object_loader::instance();
			$query = "SELECT aw_state FROM layer_states WHERE aw_class = '{$class}' AND aw_group = '{$group}' AND aw_layer = '{$layout}' AND aw_uid = '{$uid}'";
			$result = $object_loader->db_query($query, false);

			if (true !== $result)
			{
				// create table and repeat query
				self::check_layer_state_storage();
				$object_loader->db_query($query);
			}

			$row = $object_loader->db_next();
			$state = (int) $row["aw_state"];
		}

		return $state;
	}

	private static function check_layer_state_storage()
	{
		// try to create state table and alter it if necessary
		// returns true on success and also if all queries fail,
		// which could mean all is ok or indicate unknown problems
		// char cols size is 250 because MyISAM's max key length is 1000 bytes

		$result = object_loader::instance()->db_query("CREATE TABLE layer_states (aw_class varchar(250), aw_group varchar(250), aw_layer varchar(250), aw_state int(1) default 1, aw_uid varchar(250), PRIMARY KEY (aw_class, aw_group, aw_layer, aw_uid))");
		return $result;
	}

	/**
		@attrib name=open_layer
		@param u_class required
		@param u_group required
		@param u_layout required
	**/
	function open_layer($arr)
	{
		$u_class = $arr["u_class"];
		$u_group = $arr["u_group"];
		$u_layout = $arr["u_layout"];
		$uid = aw_global_get("uid");

		if ($uid and $u_class and $u_layout)
		{
			$state = self::LAYER_OPEN;
			$query = "INSERT INTO `layer_states` (`aw_state`, `aw_class`, `aw_group`, `aw_layer`, `aw_uid`) VALUES ({$state}, '{$u_class}', '{$u_group}', '{$u_layout}', '{$uid}') ON DUPLICATE KEY UPDATE `aw_state` = {$state}";
			$result = object_loader::instance()->db_query($query, false);
			if (true !== $result)
			{
				// create table and repeat query
				self::check_layer_state_storage();
				object_loader::instance()->db_query($query);
			}
		}

		exit;
	}

	/**
		@attrib name=close_layer
		@param u_class required
		@param u_group required
		@param u_layout required
	**/
	function close_layer($arr)
	{
		$u_class = $arr["u_class"];
		$u_group = $arr["u_group"];
		$u_layout = $arr["u_layout"];
		$uid = aw_global_get("uid");

		if ($uid and $u_class and $u_layout)
		{
			$state = self::LAYER_CLOSED;
			$object_loader = object_loader::instance();
			$query = "INSERT INTO `layer_states` (`aw_state`, `aw_class`, `aw_group`, `aw_layer`, `aw_uid`) VALUES ({$state}, '{$u_class}', '{$u_group}', '{$u_layout}', '{$uid}') ON DUPLICATE KEY UPDATE `aw_state` = {$state}";
			$result = $object_loader->db_query($query, false);

			if (true !== $result)
			{
				// create table and repeat query
				self::check_layer_state_storage();
				$object_loader->db_query($query);
			}
		}

		exit;
	}
}
