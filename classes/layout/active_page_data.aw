<?php

class active_page_data
{
	const LAYER_OPEN = 1;
	const LAYER_CLOSED = 2;

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
				if ($GLOBALS["object_loader"]->can("view", $stylid))
				{
					$ret .= $css->get_style_data_by_id($stylid);
				}
			}
		}

		$serialized_styles = new aw_array(aw_global_get("__aw_serialized_styles"));
		foreach($serialized_styles->get() as $styletext)
		{
			$ret .= $styletext;
		};

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

	public static function get_javascript($pos = "")
	{
		$js = (array) aw_global_get("__aw_javascript");

		$text = "";
		$files = safe_array(($pos === 'bottom') ? (isset($js['bottom']) ? $js['bottom'] : array()) : (isset($js['head']) ? $js['head'] : array()));
		foreach ($files as $file)
		{
			$text .= "<script type=\"text/javascript\" src=\"".$file."\"></script>\n";
		}

		return $text;
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
			$query = "SELECT aw_state FROM layer_states WHERE aw_class = '{$class}' AND aw_group = '{$group}' AND aw_layer = '{$layout}' AND aw_uid = '{$uid}'";
			$result = $GLOBALS["object_loader"]->db_query($query, false);

			if (true !== $result)
			{
				// create table and repeat query
				self::check_layer_state_storage();
				$GLOBALS["object_loader"]->db_query($query);
			}

			$row = $GLOBALS["object_loader"]->db_next();
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

		$result = $GLOBALS["object_loader"]->db_query("CREATE TABLE layer_states (aw_class varchar(250), aw_group varchar(250), aw_layer varchar(250), aw_state int(1) default 1, aw_uid varchar(250), PRIMARY KEY (aw_class, aw_group, aw_layer, aw_uid))");
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
			$result = $GLOBALS["object_loader"]->db_query($query, false);
			if (true !== $result)
			{
				// create table and repeat query
				self::check_layer_state_storage();
				$GLOBALS["object_loader"]->db_query($query);
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
			$query = "INSERT INTO `layer_states` (`aw_state`, `aw_class`, `aw_group`, `aw_layer`, `aw_uid`) VALUES ({$state}, '{$u_class}', '{$u_group}', '{$u_layout}', '{$uid}') ON DUPLICATE KEY UPDATE `aw_state` = {$state}";
			$result = $GLOBALS["object_loader"]->db_query($query, false);

			if (true !== $result)
			{
				// create table and repeat query
				self::check_layer_state_storage();
				$GLOBALS["object_loader"]->db_query($query);
			}
		}

		exit;
	}
}
