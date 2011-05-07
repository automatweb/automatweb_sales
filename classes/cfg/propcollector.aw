<?php
// creates xml files out of property definitions

class propcollector extends aw_template
{
	// there are 3 ways to define a tag
	// with a context
	// 	@tag name key1=value1 key2=value2 .. keyN=valueN
	// without a context, key=value pairs
	// 	@tag key1=value1 key2=value2 .. keyN=valueN
	// simple value
	//	@tag value, always (except for "extends") belongs to some previous tag
	const TAG_CTX = 1;
	const TAG_PAIRS = 2;
	const TAG_VALUE = 3;

	private $tagdata = array();
	private $tags = array(
		"extends" => self::TAG_VALUE,
		"classinfo" => self::TAG_PAIRS,
		"default" => self::TAG_PAIRS,
		"groupinfo" => self::TAG_CTX,
		"tableinfo" => self::TAG_CTX,
		"property" => self::TAG_CTX,
		"layout" => self::TAG_CTX,
		"forminfo" => self::TAG_CTX,
		"reltype" => self::TAG_CTX,
		"column" => self::TAG_CTX,
		"caption" => self::TAG_VALUE,
		"comment" => self::TAG_VALUE
	);


	function propcollector($args = array())
	{
		$this->init(array("no_db" => 1));
		$this->set_tagdata();
	}

	private function req_dir($args = array())
	{
		$path = $args["path"];
		$paths = is_array($path) ? $path : array($path);
		foreach($paths as $path)
		{
			if ($dir = opendir($path))
			{
				while (($file = readdir($dir)) !== false)
				{
					# skip the stuff that starts with .
					if (substr($file,0,1) == ".")
					{
						continue;
					}

					$fqfn = $path . "/" . $file;
					if (is_dir($fqfn) && !is_link($fqfn) && ($file != "CVS"))
					{
						$this->req_dir(array("path" => $fqfn));
					}
					elseif (is_file($fqfn) && is_readable($fqfn) && (preg_match("/\.aw$/",$fqfn)))
					{
						$this->files[] = $fqfn;
					}
				}

				closedir($dir);
			}
		}
	}

	/** Runs the property collector
		@attrib api=1 params=name

		@comment writes property files from changed classes. echoes text to the console as it goes about.
	**/
	public function run($args = array())
	{
		// Add include_path for Zend, so that ZendLoader wouldn't fail!
		set_include_path(get_include_path() . PATH_SEPARATOR . aw_ini_get("basedir")."/addons/");

		$cdir = AW_DIR . "classes";
		$sdir = AW_DIR . "scripts";
		$this->files = array();
		$this->req_dir (array("path" => array($cdir,$sdir)));
		$files = $this->files;
		asort($files);
		$this->count_modified = 0;
		$this->count_total = 0;

		foreach($files as $key => $name)
		{
			$cname = substr(basename($name),0,-3);
				$this->cl_start($cname);
			$this->_parse_file($name);
			$success = $this->cl_end();

			if ($success)
			{
				$this->count_modified++;
			}
		}

		printf("Updated %d files out of %d\nAll done.\n", $this->count_modified, $this->count_total);
	}

	/** Parses and writes one classes properties
		@attrib api=1 params=pos

		@param name required type=string
			The name of the class to parse

		@errors
			throws awex_propcollector if the given class is not derived from class_base

		@returns
			true/false based on successfully writing  the file.
	**/
	public function parse_class($name)
	{
		if (!class_index::is_extension_of($name, "class_base"))
		{
			throw new awex_propcollector("Given class '" . $name . "' has no property generation option");
		}

		$file = class_index::get_file_by_name($name);
		$this->cl_start($name);
		$this->_parse_file($file);
		$success = $this->cl_end();
		return $success;
	}

	/** Parses the contents of the given file or string and generates properties from that
		@attrib api=1 params=name

		@param file optional type=string
			The name and path of the file to parse. either this or data has to be given

		@param data required type=string
			a string to parse for properties

		@returns
			The property definition xml file content or false if file was not found
	**/
	function parse_file($arr)
	{
		$name = $arr["file"];
		if (file_exists($name))
		{
			$lines = @file($name);
		}
		else
		if (!empty($arr["data"]))
		{
			$lines = explode("\n",$arr["data"]);
		};


		if (!is_array($lines))
		{
			return false;
		};

		$tags = $this->tags;

		$this->cl_start("");
		foreach($lines as $line)
		{
			$tagname = "";
			$tagdata = "";
			if (($taginfo = preg_match("/^\s*@(\w*) (.*)/",$line,$m)))
			{
				$tagname = $m[1];
				$tagdata = $m[2];
			}
			if (isset($tags[$tagname]) && $tags[$tagname] == self::TAG_PAIRS)
			{
				$attribs = $this->_parse_attribs($m[2]);
				if ($tagname === "classinfo")
				{
					$this->classinfo = array_merge($this->classinfo,$attribs);
				}

				if ($tagname === "default")
				{
					$this->defaults = array_merge($this->defaults,$attribs);
				}
			}

			if (isset($tags[$tagname]) && $tags[$tagname] == self::TAG_CTX)
			{
				preg_match("/(\w+?) (.*)/",$tagdata,$m);
				$aname = $m[1];
				$attribs = $m[2];
				if ($tagname === "groupinfo")
				{
					$this->set_groupinfo($aname,$attribs);
				}
				else
				if ($tagname === "tableinfo")
				{
					$this->set_tableinfo($aname,$attribs);
				}
				else
				if ($tagname === "property")
				{
					$this->add_property($aname,$attribs);
				}
				else
				if ($tagname === "layout")
				{
					$this->add_layout($aname,$attribs);
				}
				else
				if ($tagname === "reltype")
				{
					$this->add_reltype($aname,$attribs);
				}
				else
				if ($tagname === "forminfo")
				{
					$this->add_forminfo($aname,$attribs);
				}
				else
				{
					$this->classdef[$tagname][$aname] = $this->_parse_attribs($attribs);
					$this->name = $aname;
					$this->last_element = $tagname;
				};
			};

			if (isset($tags[$tagname]) && $tags[$tagname] == self::TAG_VALUE)
			{
				if ($tagname === "caption")
				{
					$this->add_caption($tagdata);
				};
				if ($tagname === "comment")
				{
					$this->add_comment($tagdata);
				}
			}
		}

		$success = $this->cl_end(0);
		return $this->cdata;
	}

	////
	// !Starts a new class
	private function cl_start($cname)
	{
		$this->cl_name = $cname;
		$this->properties = array();
		$this->defaults = array();
		$this->classinfo = array();
		$this->groupinfo = array();
		$this->tableinfo = array();
		$this->views = array();
		$this->reltypes = array();
		$this->forminfo = array();
		$this->layout = array();
		$this->classdef = array();
	}

	private function add_property($name,$data)
	{
		$_x = new aw_array(explode(" ",$data));
		$fields = array("name" => $name);
		// add defaults first, propery definition can override those.
		foreach($this->defaults as $key => $val)
		{
			if (empty($fields[$key]))
			{
				$fields[$key] = $val;
			};
		};

		foreach($_x->get() as $field)
		{
			$attr = explode("=", $field, 2);
			$fname = $attr[0];
			$fvalue = isset($attr[1]) ? $attr[1] : "";
			if ($fname && strlen($fvalue))
			{
				// try to split fvalue
				$_split = explode(",",$fvalue);
				if (sizeof($_split) > 1)
				{
					$fields[$fname] = $_split;
				}
				else
				{
					if ($fname === "form" && substr($fvalue,0,1) == "+")
					{
						$fields[$fname] = array("add","edit",substr($fvalue,1));
						// add to defaults, otherwise overwrite
					}
					else
					{
						$fields[$fname] = $fvalue;
					}
				}
			}
		}

		$this->validate_fields(array(
			"type" => "property",
			"name" => $name,
			"fields" => $fields
		));

		// things listed here have automatically set their store attribute to "no"
		// unless explicitly requested otherwise
		$no_store = array("table","calendar","toolbar","treeview","submit");
		if (in_array($fields["type"],$no_store) && empty($fields["store"]))
		{
			$fields["store"] = "no";
		}

		// field defaults to the name of the property
		if (empty($fields["field"]))
		{
			$fields["field"] = $fields["name"];
		}
/*
		if ($fields["store"] === "no")
		{
			//unset($fields["table"]);
			//unset($fields["method"]);
			//unset($fields["field"]);
		}
 */
		if (!empty($fields["view"]) && !$this->views[$fields["view"]])
		{
			$this->views[$fields["view"]] = 1;
		}

		$this->properties[$name] = $fields;
		$this->name = $name;
		$this->last_element = "property";

	}

	private function add_reltype($name,$data)
	{
		$fields = $this->_parse_attribs($data);
		$this->validate_fields(array(
			"type" => "reltype",
			"name" => $name,
			"fields" => $fields,
		));
		$this->reltypes[$name] = $fields;
		$this->name = $name;
		$this->last_element = "relation";

	}

	private function add_layout($name,$data)
	{
		$fields = $this->_parse_attribs($data);
		$this->validate_fields(array(
			"type" => "layout",
			"name" => $name,
			"fields" => $fields,
		));
		if (empty($fields["group"]) && !empty($this->defaults["group"]))
		{
			$fields["group"] = $this->defaults["group"];
		};
		$fields["area_caption"] = empty($fields["area_caption"]) ? "" : htmlentities($fields["area_caption"]);
		$this->layout[$name] = $fields;
		$this->name = $name;
		$this->last_element = "layout";
	}

	private function add_forminfo($name,$data)
	{
		$this->forminfo[$name] = $this->_parse_attribs($data);
		$this->validate_fields(array(
			"type" => "forminfo",
			"name" => $name,
			"fields" => $this->forminfo[$name],
		));
	}

	private function set_groupinfo($id,$data)
	{
		$open_token = false;
		# so that we get the last token as well
		$data .= " ";
		# this could be rewritten to be shorter, of course. Feel free to do it
		$tmp = "";
		for ($i = 0; $i < strlen($data); $i++)
		{
			$chr = $data[$i];
			if ($open_token)
			{
				if ($chr === "\"")
				{
					if (strlen($tmp) > 0)
					{
						$tmp = explode("=",$tmp);
						$_name = isset($tmp[0]) ? $tmp[0] : "";
						$_value = isset($tmp[1]) ? $tmp[1] : "";

						if($_name === "caption")
						{
							$_value = htmlentities($_value);
						}

						$this->validate_fields(array(
							"type" => "groupinfo",
							"name" => $id,
							"field" => $_name,
							"value" => $_value
						));
						$this->groupinfo[$id][$_name] = $_value;
						$tmp = "";
					}
					$open_token = false;
				}
				else
				{
					$tmp .= $chr;
				}
			}
			else
			{
				if ($chr === "\"")
				{
					$open_token = true;
				}
				elseif ($chr === " ")
				{
					if (strlen($tmp) > 0)
					{
						$tmp = explode("=",$tmp);
						$_name = isset($tmp[0]) ? $tmp[0] : "";
						$_value = isset($tmp[1]) ? $tmp[1] : "";

						if($_name === "caption")
						{
							$_value = htmlentities($_value);
						}

						$this->validate_fields(array(
							"type" => "groupinfo",
							"name" => $id,
							"field" => $_name,
							"value" => $_value
						));
						$this->groupinfo[$id][$_name] = $_value;
						$tmp = "";
					}
					$open_token = false;
				}
				else
				{
					$tmp .= $chr;
				}
			}
		}
	}

	private function set_tableinfo($id,$data)
	{
		$attr = $this->_parse_attribs($data);
		$this->validate_fields(array(
			"type" => "tableinfo",
			"name" => $id,
			"fields" => null,
		));
		if (empty($attr["master_index"]) && $attr["master_table"] === "objects")
		{
			$attr["master_index"] = "brother_of";
		};
		$this->tableinfo[$id] = $attr;
	}

	private function add_caption($caption)
	{
		switch($this->last_element)
		{
			case "property":
				$this->properties[$this->name]["caption"] = htmlentities($caption);
				break;

			case "relation":
				$this->reltypes[$this->name]["caption"] = htmlentities($caption);
				break;

			case "column":
				$this->classdef["column"][$this->name]["caption"] = htmlentities($caption);
				break;

			case "layout":
				$this->layout[$this->name]["caption"] = htmlentities($caption);
				break;
		}
	}

	private function add_comment($comment)
	{
		if (isset($this->last_element) && $this->last_element === "property")
		{
			$this->properties[$this->name]["comment"] = htmlentities($comment);
		}
	}

	////
	// !Ends a class
	private function cl_end($write = 1)
	{
		$sr = get_instance("core/serializers/xml",array("ctag" => ""));
		$sr->set_child_id("properties","property");
		$outdir = AW_DIR . "xml/properties/";
		$success = false;

		if (sizeof($this->properties) > 0 || sizeof($this->classinfo) > 0)
		{
			$fullname = $outdir . $this->cl_name . ".xml";
			if (1 == $write)
			{
				print "Creating $fullname\n";
			};
			$arr = array();
			$arr["properties"] = array_values($this->properties);

			if (sizeof($this->classinfo) > 0)
			{
				$arr["properties"]["classinfo"] = $this->classinfo;
			};

			if (sizeof($this->groupinfo) > 0)
			{
				$arr["properties"]["groupinfo"] = $this->groupinfo;
			};

			if (sizeof($this->tableinfo) > 0)
			{
				$arr["properties"]["tableinfo"] = $this->tableinfo;
			};

			if (sizeof($this->views) > 0)
			{
				$arr["properties"]["views"] = $this->views;
			};

			if (sizeof($this->reltypes) > 0)
			{
				$arr["properties"]["reltypes"] = $this->reltypes;
			};

			if (sizeof($this->layout) > 0)
			{
				$arr["properties"]["layout"] = $this->layout;
			};

			if (sizeof($this->forminfo) > 0)
			{
				$arr["properties"]["forminfo"] = $this->forminfo;
			};

			if (!empty($this->classdef["column"]) && sizeof($this->classdef["column"]) > 0)
			{
				$arr["properties"]["columns"] = $this->classdef["column"];
			};

			if ($write == 1)
			{
				$res = $sr->xml_serialize($arr);
				$this->put_file(array(
					"file" => $fullname,
					"content" => $res,
				));
			}
			else
			{
				$this->cdata = $arr;
			}

			$success = true;
		}
		return $success;
	}

	private function _parse_attribs($data)
	{
		$_x = new aw_array(explode(" ",$data));
		//$fields = array("name" => $name);
		$fields = array();
		foreach($_x->get() as $field)
		{
			if (!$field)
			{
				continue;
			};
			list($fname,$fvalue) = explode("=",$field);
			$fname = trim($fname);
			$fvalue = trim($fvalue);
			if (strlen($fname) && strlen($fvalue))
			{
				// try to split fvalue
				if(substr($fvalue, 0, 1) == "\"" && substr($fvalue, -1, 1) == "\"")
				{
					$fvalue = substr($fvalue, 1, strlen($fvalue) - 2);
				}
				else
				{
					$_split = explode(",",$fvalue);
				}
				if (sizeof($_split) > 1)
				{
					$fields[$fname] = $_split;
				}
				else
				{
					$fields[$fname] = $fvalue;
				}
			}
			else
			{
				print "Invalid syntax: $field\n";
			};
		}
		return $fields;
	}

	private function _parse_file ($name)
	{
		$cname = substr(basename($name),0,-3);
		$this->currentclass = $cname;

		if (!class_index::is_instantiable($cname))
		{
			return false;
		}

		$tmp = new $this->currentclass();
		// properties are generated for cb extensions only
		if (!$tmp instanceof class_base)
		{
			return false;
		}

		$targetfile = AW_DIR . "xml/properties/{$cname}.xml";
		$outdir = AW_DIR . "xml/properties/";
		$parent_modified = false;

		### check whether xml file is already up to date
		if (file_exists($targetfile))
		{
			$this->count_total++;
			$target_mtime = filemtime($targetfile);
			$source_mtime = filemtime($name);

			if ($source_mtime < $target_mtime)
			{
				$modified = false;
			}
			else
			{
				$modified = true;
			}
		}
		else
		{
			$modified = true;
		}

		### parse file
		$lines = file($name);

		if (is_array($lines))
		{
			$parent = "";

			foreach ($lines as $line)
			{ ### see if current class has a parent
				$taginfo = preg_match("/^\s*@(\w*) (.*)/",$line,$m);
				$tagname = isset($m[1]) ? $m[1] : "";
				$tagdata = isset($m[2]) ? $m[2] : "";

				if ($tagname === "extends")
				{
					$parent = AW_DIR . "classes/" . trim ($tagdata) . AW_FILE_EXT;

					if (file_exists ($parent))
					{ ### parse parent class data into current class' data. The fact that this recursive call is made here makes multiple inheritance possible. If that should become undesirable this whole if section can be moved outside innermost foreach loop.
						$this->count_total--;

						### if current was modified, parent has to be parsed too
						if ($modified)
						{
							touch ($parent);
						}

						$parent_modified = $this->_parse_file ($parent);
					}
				}
			}

			if ($modified or $parent_modified)
			{ ### parse current class
				$this->_parse_properties ($lines);
			}
		}

		return $modified;
	}

	private function _parse_properties ($lines)
	{
		foreach($lines as $line)
		{
			$taginfo = preg_match("/^\s*@(\w*) (.*)/",$line,$m);

			if (!isset($m[1]) or !isset($this->tags[$m[1]]))
			{ // skip api doc comments and other irrelevant tags
				continue;
			}

			$tagname = $m[1];
			$tagdata = $m[2];

			switch ($this->tags[$tagname])
			{
				case self::TAG_PAIRS:
					$attribs = $this->_parse_attribs($m[2]);

					if ($tagname === "classinfo")
					{
						$this->classinfo = array_merge($this->classinfo,$attribs);
					}

					if ($tagname === "default")
					{
						$this->defaults = array_merge($this->defaults,$attribs);
					}
					break;

				case self::TAG_CTX:
					preg_match("/(\w+?) (.*)/",$tagdata,$m);
					$aname = $m[1];
					$attribs = $m[2];

					if ($tagname === "groupinfo")
					{
						$this->set_groupinfo($aname,$attribs);
					}
					elseif ($tagname === "tableinfo")
					{
						$this->set_tableinfo($aname,$attribs);
					}
					elseif ($tagname === "property")
					{
						$this->add_property($aname,$attribs);
					}
					elseif ($tagname === "layout")
					{
						$this->add_layout($aname,$attribs);
					}
					elseif ($tagname === "reltype")
					{
						$this->add_reltype($aname,$attribs);
					}
					elseif ($tagname === "forminfo")
					{
						$this->add_forminfo($aname,$attribs);
					}
					else
					{
						$this->classdef[$tagname][$aname] = $this->_parse_attribs($attribs);
						$this->name = $aname;
						$this->last_element = $tagname;
					}
					break;

				case self::TAG_VALUE:
					if ($tagname === "caption")
					{
						$this->add_caption ($tagdata);
					}

					if ($tagname === "comment")
					{
						$this->add_comment ($tagdata);
					}
					break;
			}
		}
	}

	private function validate_fields($arr)
	{
		if(isset($arr["fields"]) && !is_array($arr["fields"]) && $arr["field"] && $arr["value"])
		{
			$fields = array(
				$arr["field"] => $arr["value"]
			);
		}
		else
		if(isset($arr["fields"]) && is_array($arr["fields"]))
		{
			$fields = $arr["fields"];
		}
		else
		{
			$fields = array();
		}

		if($arr["type"] === "property" && empty($fields["type"]))
		{
			print "***WARNING: Property \"{$arr["name"]}\" with undefined type ({$this->currentclass})\n";
			return;
		}

		$tagfields = $this->tagdata[$arr["type"]];
		$err_add_text = "";
		$other = array();
		if($arr["type"] === "property")
		{
			$other = $tagfields["global"];
			if(!isset($tagfields[$fields["type"]]))
			{
				print "***WARNING: Property \"{$arr["name"]}\" with unknown type \"{$fields["type"]}\" ({$this->currentclass})\n";
				return;
			}
			$tagfields = $tagfields[$fields["type"]];
			$err_add_text = " with type \"".$fields["type"]."\"";
		}
		foreach($fields as $f => $val)
		{
			if($arr["type"] === "property" && $f === "name")
			{
				continue;
			}

			if(!isset($tagfields[$f]) && !isset($other[$f]))
			{
				print "***WARNING: Unknown field \"{$f}\" in {$arr["type"]} \"{$arr["name"]}\"{$err_add_text} ({$this->currentclass})\n";
			}
			else
			{
				$type = isset($tagfields[$f]) ? $tagfields[$f]["type"] : "";
				if($type)
				{
					switch($type)
					{
						case "clid":
							if(!is_array($val) and strpos($val, ","))
							{
								$val = explode(",", $val);
							}
							if(!is_array($val))
							{
								$val = array($val);
							}
							foreach($val as $v)
							{
								if(!defined($v))
								{
									print "***WARNING: Unknown clid {$v} in {$arr["type"]} {$arr["name"]} ({$this->currentclass})\n";
								}
							}
							break;
					}
				}
			}
		}
	}

	function set_tagdata()
	{
		$xmldir = AW_DIR . "xml/";
		$xml = simplexml_load_file($xmldir."property_types.xml");
		foreach($xml->children() as $k1 => $v1)
		{
			foreach($v1->children() as $k2 => $v2)
			{
				if($k1 == "property")
				{
					if(count($v2->children()))
					{
						foreach($v2->children() as $k3 => $v3)
						{
							$v3_attrs = $v3->attributes();
							$data[$k1][$k2][$k3] = reset($v3_attrs);
						}
					}
					else
					{
						$data[$k1][$k2] = array();
					}
				}
				else
				{
					$v2_attrs = $v2->attributes();
					$data[$k1][$k2] = reset($v2_attrs);
				}
			}
		}
		$this->tagdata = $data;
	}
}

/** property parser error message **/
class awex_propcollector extends aw_exception {}
