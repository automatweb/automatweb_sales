<?php

class cfgutils extends aw_template
{
	private $fbasedir = "xml/properties/";
	private $f_site_basedir = "xml/properties/";
	private $clist_init_done = false;
	private $clist = array();

	public $groupinfo = array();

	function cfgutils($args = array())
	{
		$this->init("");
		$this->fbasedir = $this->fbasedir;
		$this->f_site_basedir = $this->f_site_basedir;
	}

	private function _init_clist()
	{
		if ($this->clist_init_done)
		{
			return false;
		}

		$tmp = aw_ini_get("classes");
		foreach($tmp as $key => $val)
		{
			if (empty($val["file"]))
			{
				continue;
			}
			$fname = $val["file"];
			// cause property catalog is flat - alltho maybe it shouldn't be
			$fl = strpos($fname,"/") ? substr(strrchr($fname,"/"),1) : $fname;
			// for each thingie check, whether the property file for the class
			// exists.
			$this->clist[$key] = $fl;
		}

		// XXX: remove this after document class has been converted
		$this->clist[7] = "doc";

		$this->clist_init_done = true;
	}

	/** Checks whether a class has any properties
		@attrib api=1 params=name

		@param file optional type=string
			name of the class

		@param clid optional type=int
			id of the class

		@comment
			One of the parameters must be given

		@errors
			none

		@returns
			true if the given class has properties, false if not

		@examples
			$cu = get_instance("cfg/cfgutils");
			if ($cu->has_properties(array("clid" => CL_IMAGE)))
			{
				echo "image class has properties!";
			}
	**/
	function has_properties($args = array())
	{
		if ($args['file'])
		{
			$fname = basename($args['file']);
		}
		elseif ($args['clid'])
		{
			$cldat = aw_ini_get("classes");
			$fname = basename($cldat[$args['clid']]["file"]);
		}

		if ($fname === "document")
		{
			return true;
		}
		$retval = false;
		if ($fname)
		{
			// that check is a bit of stupid, OTOH it needs to be fast
			$retval = file_exists(AW_DIR .$this->fbasedir . $fname . '.xml');
			$retval |= file_exists(AW_DIR .$this->f_site_basedir . $fname . '.xml');
		}
		return $retval;
	}

	/** Returns a list of classes that have properties
		@attrib api=1

		@errors
			none

		@returns
			array with class id as the key and the class name as the value

		@examples
			$cu = get_instance("cfg/cfgutils");
			echo html::select(array(
				"name" => "class_select",
				"options" => $cu->get_classes_with_properties()
			));
	**/
	function get_classes_with_properties($args = array())
	{
		$result = array();
		$value = "name";
		$clist = aw_ini_get("classes");

		foreach($clist as $clid => $desc)
		{
			if ($this->has_properties(array("clid" => $clid)))
			{
				$result[$clid] = $desc[$value];
			}
		}

		asort($result);
		return $result;
	}

	/** Loads, unserializes and returns properties for a single class, also caches them
		@attrib api=1 params=name

		@param file optional type=string
			name of the class to load properties from. either this or class_id must be given

		@param clid optional type=clid
			class id of the class to load properties for

		@param filter optional type=string
			filter the properties based on a attribute

		@param load_trans optional type=bool
			wheter to load translated props or not

		@returns
			array { prop_name => prop_data } for all props in the given class. subclass props are not loaded. use load_properties for that!
	**/
	function load_class_properties($args = array())
	{
		$args["load_trans"] = isset($args["load_trans"])?$args["load_trans"]:1;
		$clid = null;
		extract($args);

		if (!$this->clist_init_done)
		{
			$this->_init_clist();
		}

		if (empty($args["source"]) && empty($args["file"]) && !empty($args["clid"]))
		{
			$file = isset($this->clist[$clid]) ? $this->clist[$clid] : "";
		}

		$system = isset($args["system"]) ? 1 : 0;
		$layout = null;

		// if system is set, then no captions/translations/etc will be loaded,
		// since storage really doesn't care. so why should property loader?

		// you can also directly parse XML, in which case we do not cache anything.
		// the only sad user of this feature is document class and it's def_cfgform.xml functionality,
		// which really should die.
		if (empty($args['source']))
		{
			$fqfn = $this->fbasedir . $file . ".xml";
			if(function_exists("get_file_version") && is_file(AW_DIR .($fqfn_version = get_file_version($fqfn))))
			{
				$fqfn = $fqfn_version;
			}
			$fqfn = AW_DIR .$fqfn;
			if (!is_file($fqfn))
			{
				$fqfn = AW_DIR .$this->f_site_basedir . $file . ".xml";
			}

			$cachename = aw_ini_get("cache.page_cache") . "propdef_{$file}.cache";

			if (!$system)
			{
				load_class_translations($file);
			}
		}

		$from_cache = false;
		if (empty($args['source']) && file_exists($cachename) && file_exists($fqfn) && (filemtime($cachename) > filemtime($fqfn)))
		{
			include($cachename);
			$from_cache = true;
		}
		else
		{
			if (!empty($args['source']))
			{
				$source = $args['source'];
			}
			else
			{
				$source = $this->get_file(array("file" => $fqfn));
			}

			$p = xml_parser_create();
			$x = xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
			$x = xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
			xml_parse_into_struct($p, $source, $vals, $index);
			xml_parser_free($p);

			$props = array();
			$containers = array("property","classinfo","groupinfo","tableinfo","reltypes","forminfo","layout");
			$propdef = array();
			$propkey = false;
			$tagname = false;
			$defvals = array(
				"property" => array("store" => "", "method" => "", "table" => "", "field" => "")
			);
			// if only the XML file would have a bit saner structure, the following could be a lot easier
			foreach($vals as $val)
			{
				$value = isset($val["value"]) ? $val["value"] : null;

				if (2 == $val["level"] && "open" === $val["type"] && in_array($val["tag"],$containers))
				{
					$propkey = $val["tag"];
				}
				elseif (2 == $val["level"] && "close" == $val["type"] && in_array($val["tag"],$containers))
				{
					$propkey = false;
				}
				elseif ("property" === $propkey && "complete" === $val["type"])
				{
					if ("name" === $val["tag"])
					{
						$propname = $value;
						// new property, init def
						if (!isset($propdef[$propkey][$propname]) && isset($defvals[$propkey]))
						{
							$propdef[$propkey][$propname] = $defvals[$propkey];
						}
					}
					// if this tags parent is a 'container' (containing multiple values),
					// then add to that, otherwise just use the name of the tag
					if ($tagname)
					{
						$propdef[$propkey][$propname][$tagname][] = $value;
					}
					else
					{
						$tag = $val["tag"];
						$propdef[$propkey][$propname][$tag] = $value;
					}
				}
				else
				/*** some attributes (props, table_fields) contain multiple values, the following 2
					ifs deal with that **/
				if ("property" === $propkey && "open" === $val["type"])
				{
					$tagname = $val["tag"];
				}
				else
				if ("property" === $propkey && "close" === $val["type"])
				{
					$tagname = false;
				}
				else
				/*** multiple value handling ends **/
				if ("groupinfo" == $propkey || "reltypes" == $propkey || "tableinfo" == $propkey ||
						"layout" == $propkey || "forminfo" == $propkey)
				{
					// level 3 is the direct child of propkey
					if (3 == $val["level"] && "open" === $val["type"])
					{
						$propname = $val["tag"];
					}
					elseif (3 == $val["level"] && "close" === $val["type"])
					{
						$propname = false;
					}
					// level 4 is the direct child of level 3 tag, this deals with multiple values
					elseif (4 == $val["level"] && "open" === $val["type"])
					{
						if ("open" === $val["type"]) $tagname = $val["tag"];
					}
					elseif (4 == $val["level"] && "close" === $val["type"])
					{
						if ("close" === $val["type"])
						{
							$tagname = false;
						}
					}
					elseif ($val["level"] > 3 && $val["type"] === "complete" && isset($val["value"]))
					{
						if ($tagname)
						{
							$propdef[$propkey][$propname][$tagname][] = $value;
						}
						else
						{
							$propdef[$propkey][$propname][$val["tag"]] = $value;
						}
					}
				}
				else if (!empty($val["value"]))
				{
					$propdef[$propkey][$val["tag"]] = $value;
				}
			}
		}

		$properties = empty($propdef["property"]) ? array() : $propdef["property"];
		$classinfo = $this->tableinfo = $relinfo = $groupinfo = array();
		if (isset($propdef["layout"]) && is_array($propdef['layout']))
		{
			foreach ($propdef['layout'] as $k => $d)
			{
				$propdef['layout'][$k]['caption'] = aw_html_entity_decode(isset($d['caption']) ? $d['caption'] : "");
			}

		}

		$this->propdef = $propdef;

		if (isset($propdef["classinfo"]))
		{
			$classinfo = $propdef["classinfo"];
		}

		if (isset($propdef["tableinfo"]))
		{
			$this->tableinfo = $propdef["tableinfo"];
		}

		if (isset($propdef["reltypes"]))
		{
			$relinfo = $propdef["reltypes"];
		}

		if (isset($propdef["groupinfo"]))
		{
			$groupinfo = $propdef["groupinfo"];
		}

		// new
		if(isset($propdef["layout"]))
		{
			$layout = $propdef["layout"];
		}

		foreach($properties as $k => $d)
		{
			if (!isset($d["caption"]))
			{
				continue;
			}
			$properties[$k]["caption"] = aw_html_entity_decode($d["caption"]);
			if(isset($d["help"]))
			{
				$properties[$k]["comment"] = aw_html_entity_decode($d["comment"]);
			}
			if(isset($d["help"]))
			{
				$properties[$k]["help"] = aw_html_entity_decode($d["help"]);
			}
		}
		foreach($groupinfo as $k => $d)
		{
			$groupinfo[$k]["caption"] = aw_html_entity_decode($d["caption"]);
		}



		if (is_array($relinfo))
		{
			foreach($relinfo as $k => $dat)
			{
				if (isset($dat["caption"]))
				{
					$relinfo[$k]["caption"] = aw_html_entity_decode($dat["caption"]);
				}
			}
		}

		// new
		foreach(safe_array($layout) as $k => $dat)
		{
			$layout[$k]["area_caption"] = aw_html_entity_decode(str_replace("&nbsp;"," ", isset($dat["area_caption"]) ? $dat["area_caption"] : null));
		}

		// translate
		if(!empty($args["load_trans"]))
		{
			foreach($properties as $k => $d)
			{
				$caption = isset($d["caption"]) ?  $d["caption"] : "";

				if (empty($d["name"]))
				{
					throw new aw_exception("Property name can't be empty (file $fqfn, prop data: " . var_export($d, true) . ")");
				}

				$name = $d["name"];

				$properties[$k]["orig_caption"] = $caption;
				$t_str = "Omaduse {$caption} ({$name}) caption";
				$tmp = t2($t_str);
				if ($tmp !== NULL)
				{
					$properties[$k]["caption"] = $tmp;
				}

				$tmp = t2("Omaduse {$caption} ({$name}) kommentaar");
				if ($tmp !== NULL)
				{
					$properties[$k]["comment"] = $tmp;
				}

				$tmp = t2("Omaduse {$caption} ({$name}) help");
				if ($tmp !== NULL)
				{
					$properties[$k]["help"] = $tmp;
				}
			}

			foreach($groupinfo as $k => $d)
			{
				$groupinfo[$k]["orig_caption"] = $groupinfo[$k]["caption"];
				$tmp = t2("Grupi ".$d["caption"]." (".$k.") pealkiri");
				if ($tmp !== NULL)
				{
					$groupinfo[$k]["caption"] = $tmp;
				}
			}

			if (is_array($relinfo))
			{
				foreach($relinfo as $k => $dat)
				{
					$tmp = "Seose ". (isset($dat["caption"]) ? htmlentities($dat["caption"]) : "") . " (RELTYPE_".$k.") tekst";
					$tmp = t2($tmp);
					if ($tmp !== NULL)
					{
						$relinfo[$k]["caption"] = $tmp;
					}

				}
			}

			// new
			if(is_array($layout))
			{
				foreach($layout as $k => $dat)
				{
					$tmp = "Kujundusosa ".$dat["area_caption"]." (".$k.") pealkiri";
					$tmp = t2($tmp);
					if($tmp !== NULL)
					{
						$layout[$k]["area_caption"] = $tmp;
					}
				}
			}
		}

		$this->classinfo = $classinfo;
		$tmp = array();

		if (count($this->groupinfo))
		{
			if (is_array($groupinfo))
			{
				$this->groupinfo = array_merge($this->groupinfo,$groupinfo);
			}
		}
		else
		{
			$this->groupinfo = $groupinfo;
		}
		// new
		$this->layout = $layout;
		$this->propdef["layout"] = $layout;

		$tmp = array();
		if (is_array($relinfo))
		{
			foreach($relinfo as $key => $val)
			{
				$_name = "RELTYPE_" . $key;
				$relx = $val;

				if (array_key_exists("clid", $relx) && !is_array($relx["clid"]) && strlen(trim($relx["clid"])))
				{
					$relx["clid"] = array($relx["clid"]);
				}

				$_clidlist = array();

				if (is_array($relinfo) && array_key_exists("clid", $relx))
				{
					foreach(safe_array($relx["clid"]) as $clid)
					{
						if (defined($clid))
						{
							$_clidlist[] = constant($clid);
						}
					}
				}

				$relx["clid"] = $_clidlist;
				$tmp[$key] = $relx;
				$tmp[$_name] = $relx;
				// define the constant
				if (!defined($_name))
				{
					define($_name, $tmp[$key]["value"]);
				}
				$tmp[$tmp[$key]["value"]] = $relx;
			}
		}
		$this->relinfo = $tmp;

		$res = array();

		$do_filter = false;

		// naw, it cannot really be empty, can it?
		if (empty($filter["form"]) && !$system)
		{
			$filter["form"] = array("","add","edit");
		}

		if (isset($filter) && is_array($filter) && sizeof($filter) > 0)
		{
			$do_filter = true;
			if (in_array("group",array_keys($filter)) && strlen($filter["group"]) == 0 )
			{
				$filter["group"] = "general";
			}
			$pass_count = sizeof($filter);
		}

		if (is_array($properties))
		{
			foreach($properties as $key => $val)
			{
				$_tmp = $val;
				$name = isset($_tmp["name"]) ? $_tmp["name"] : null;
				if (empty($_tmp["form"]))
				{
					$_tmp["form"] = "";
				}
				if ($do_filter)
				{
					$pass = 0;
					foreach($filter as $key => $val)
					{
						// all is a special value, this makes it appear regardless of the filter value
						if (isset($_tmp[$key]) && $_tmp[$key] == "all")
						{
							$pass++;
						}
						else if (is_array($val))
						{
							if (isset($_tmp[$key]) && is_array($_tmp[$key]))
							{
								$intersect = array_intersect($_tmp[$key],$val);
								if (sizeof($intersect) > 0)
								{
									$pass++;
								};
							}
							else
							{
								if (in_array($_tmp[$key],$val))
								{
									$pass++;
								}
							}
						}
						else if (isset($_tmp[$key]) && is_array($_tmp[$key]) && in_array($val,$_tmp[$key]))
						{
							$pass++;
						}
						else if (ifset($_tmp, $key) == $val)
						{
							$pass++;
						}
					}

					if ($pass == $pass_count)
					{
						$res[$name] = $_tmp;
					}
				}
				else
				{
					$res[$name] = $_tmp;
				}
			}
		}

		if (!$from_cache && !empty($cachename))
		{
			//print "writing out";
			$fp = fopen($cachename, "w");
			$str = "<?php\n";
			$str .= aw_serialize($propdef,SERIALIZE_PHP_FILE,array("arr_name" => "propdef"));
			$str .= "?>";
			fwrite($fp, $str);
			fclose($fp);
		}

		return $res;
	}

	/** Loads class properties
		@attrib api=1

		@param load_trans optional type=bool
			whether to load translated properties or not (by default it loads)

		@param filter optional type=array
			array of filters to filter the properties by. for instance group => general
			returns only the properties whose group is general

		@param file optional type=string
			The file whose properties to load, no path

		@param clid optional type=int
			The class id whose properties to load

		@errors
			none

		@comment
			One of file or clid must be given

		@returns
			array of properties, with the property name as the key and property data
			as the value
	**/
	function load_properties($args = array())
	{
		$clid = isset($args["clid"]) ? $args["clid"] : 0;

		if (empty($args["file"]))
		{
			try
			{
				$file = aw_ini_get("classes.{$clid}.file");
				$file = basename($file);
			}
			catch (Exception $e)
			{
				//!!! mis on  default? mis saab kui siia satutakse
			}

			if ($clid == 7)
			{
				$file = "doc";
			}

			if (empty($file))
			{
				throw new aw_exception("No valid file or class id given.");
			}
		}
		else
		{
			$file = basename($args["file"]);
		}

		$tft = 0;
		$adm_ui_lc = aw_ini_get("user_interface.default_language");
		$trans_fn = AW_DIR . "lang/trans/{$adm_ui_lc}/aw/{$file}" . AW_FILE_EXT;
		if (file_exists($trans_fn))
		{
			require_once($trans_fn);
			$tft = filemtime($trans_fn);
		}

		$sc = ifset($GLOBALS, "cfg", "classes", $clid, "site_class");
		if ($sc)
		{
			$ts = is_readable(aw_ini_get("site_basedir")."xml/properties/{$file}.xml") ? filemtime(aw_ini_get("site_basedir")."/xml/properties/{$file}.xml") : null;
		}
		else
		{
			$ts = is_readable(AW_DIR."xml/properties/{$file}.xml") ? filemtime(AW_DIR."/xml/properties/{$file}.xml") : null;
		}

		$args["adm_ui_lc"] = $adm_ui_lc;
		$key = md5(serialize($args));
		$res = cache::file_get_ts($key, max($ts, $tft));
		$cache_d = null;
		if ($res)
		{
			$cache_d = aw_unserialize($res);
		}

		if (is_array($cache_d) && !$sc)
		{
			foreach($cache_d as $k => $v)
			{
				$this->$k = $v;
			}
			$ret = $cache_d["ret"];
		}
		else
		{
			$ret = $this->load_properties_unc($args);
			$cache_d = array(
				"groupinfo" => $this->groupinfo,
				"tableinfo" => $this->tableinfo,
				"classinfo" => $this->classinfo,
				"relinfo" => $this->relinfo,
				"propdef" => $this->propdef,
				"ret" => $ret
			);
			cache::file_set($key, aw_serialize($cache_d, SERIALIZE_PHP_FILE));
		}
		return $ret;
	}

	private function load_properties_unc($args)
	{
		$args["load_trans"] = isset($args["load_trans"])?$args["load_trans"]:1;
		extract($args);
		$filter = isset($args["filter"]) ? $args["filter"] : array();
		$clinf = aw_ini_get("classes");

		if (empty($file))
		{
			if (isset($clinf[$clid]["file"]))
			{
				$file = basename($clinf[$clid]["file"]);
			}

			if ($clid == 7) $file = "doc";

			if (empty($file))
			{
				throw new aw_exception("No file or class id given.");
			}
		}

		$adm_ui_lc = aw_ini_get("user_interface.default_language");
		$trans_fn = AW_DIR . "/lang/trans/{$adm_ui_lc}/aw/".basename($file) . AW_FILE_EXT;
		if (file_exists($trans_fn))
		{
			require_once($trans_fn);
		}

		$this->groupinfo = array();
		$coreprops = $this->load_class_properties(array(
			"load_trans" => isset($args["load_trans"]) ? $args["load_trans"] : null, //!!! mis on  default?
			"file" => "class_base",
			"filter" => $filter,
			"system" => isset($args["system"]) ? $args["system"] : null, //!!! mis on  default?
		));

		$cldat = array();
		if (isset($clinf[$clid]))
		{
			$cldat = $clinf[$clid];
		}

		if (isset($cldat["generated"]))
		{
			$fld = aw_ini_get("site_basedir")."files/classes";
			$loc = $fld . "/" . $cldat["file"] . AW_FILE_EXT;

			$anakin = new propcollector();
			$result = $anakin->parse_file(array(
				"file" => $loc,
			));

			$objprops = array();

			foreach(safe_array($result["properties"]) as $key => $val)
			{
				if (!isset($val["name"]))
				{
					$val["name"] = "";
				}
				$objprops[$val["name"]] = $val;
			};

			// XXX: wtf?
			$this->tableinfo = isset($result["properties"]["tableinfo"]) ? $result["properties"]["tableinfo"] : null;
		}
		else
		{
			$objprops = $this->load_class_properties(array(
				"load_trans" => $args["load_trans"],
				"file" => $file,
				"filter" => $filter,
				"system" => isset($args["system"]) ? $args["system"] : null,
			));
		}

		if (empty($this->classinfo["trans"]))
		{
			unset($coreprops["needs_translation"]);
			unset($coreprops["is_translated"]);
		}

		if (is_array($objprops))
		{
			foreach($objprops as $name => $objprop)
			{
				// if a property belongs to multiple groups and one of them is not
				// defined then add the group, value of the group attribute becomes
				// the caption
				if (!empty($objprop["group"]))
				{
					if ((is_int($objprop["group"]) or is_string($objprop["group"])) and empty($this->groupinfo[$objprop["group"]]))
					{
						$this->groupinfo[$objprop["group"]] = array("caption" => $objprop["group"]);
					}
					elseif(is_array($objprop["group"]))
					{
						foreach($objprop["group"] as $_group)
						{
							if (empty($this->groupinfo[$_group]))
							{
								$this->groupinfo[$_group] = array("caption" => $_group);
							}
						}
					}
				}

				// Allow overriding of properties defined in class_base
				if (isset($coreprops[$name]))
				{
					unset($coreprops[$name]);
				}
			}
		}

		$rv = array_merge($coreprops,$objprops);
		return $rv;
	}

	/** Loads properties from a given xml sring
		@attrib api=1 params=pos

		@param args optional type=array
			array { xml_definition => the string to load props from }

		@param get_layout optional type=bool
			Whether to inclus list of layouts in the output as well. defaults to false.

		@returns
			array{ array{property_name => property_data}, array{group_name => group_data}, array{layout_name => layout_data} }
	**/
	function parse_cfgform($args = array(), $get_layout = false)
	{
		$proplist = $grplist = $layout = array();
		if (isset($args["xml_definition"]))
		{
			$proplist = $this->load_class_properties(array(
				'source' => $args['xml_definition'],
			));
			$grplist = $this->groupinfo;
			$layout = (array) $this->layout;
		}

		if ($get_layout)
		{
			return array($proplist,$grplist,$layout);
		}
		else
		{
			return array($proplist,$grplist);
		}
	}

	/** Returns the content of the classinfo tag for the last loaded class
		@attrib api=1

		@returns
			array of key=>value pairs for the @classinfo tag of the last class loaded via load_properties
	**/
	function get_classinfo()
	{
		return $this->classinfo;
	}

	/** Returns the layouts for the last loaded class
		@attrib api=1

		@returns
			array of layouts for the class last loaded via load_properties
			array key is layout name, array value is array of layout key=>value pairs
	**/
	function get_layoutinfo()
	{
		return isset($this->propdef["layout"]) ? $this->propdef["layout"] : array();
	}

	/** Returns relation type info for the last loaded class
		@attrib api=1

		@returns
			array of relation data for the last loaded class
			array contains three entries for each relation type.
			for each type, there are keys:
				RELTYPE_FOO
				FOO
				51

			in other words, the complete reltype name, short reltype name and reltype value.
			for each of these, the array value is an array of (value, clid, caption)
	**/
	function get_relinfo()
	{
		return is_array($this->relinfo) ? $this->relinfo : array();
	}

	/** Returns the content of the forminfo tag for the last loaded class
		@attrib api=1

		@returns
			array of key=>value pairs for the @forminfo tag of the last class loaded via load_properties
	**/
	function get_forminfo()
	{
		return isset($this->propdef["forminfo"]) ? $this->propdef["forminfo"] : array();
	}

	/** Returns the content of the tableinfo tags for the last loaded class
		@attrib api=1

		@returns
			array with entries for each @tableinfo tag in the last loaded class
			key is table name, value is array(index,master_table,master_index)
	**/
	function get_tableinfo()
	{
		return isset($this->propdef["tableinfo"]) ? $this->propdef["tableinfo"] : array();
	}

	/** Returns the content of the groupinfo tags for the last loaded class
		@attrib api=1

		@returns
			array with entries for each @groupinfo tag in the last loaded class
			key is group name, value is array of key=>value pairs for the groupinfo
	**/
	function get_groupinfo()
	{
		return $this->groupinfo;
	}
}
