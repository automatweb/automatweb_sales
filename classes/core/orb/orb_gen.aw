<?php
/*
@classinfo  maintainer=kristo
*/

/** aw orb def generator

	@author terryf <kristo@struktuur.ee>
	@cvs $Id: orb_gen.aw,v 1.6 2009/07/14 09:54:04 instrumental Exp $

	@comment
	generates orb defs, based on information from docgen_analyzer
**/

class orb_gen extends class_base
{
	function orb_gen()
	{
		$this->init("core/docgen");
	}

	function _get_orb_defs2($data)
	{
		$folder = substr(dirname($data["file"]), 1);
		$rv = array();
		foreach($data["functions"] as $f_name => $f_data)
		{
			// func is public if name attrib is set
			$attr = isset($f_data["doc_comment"]["attribs"]) ? $f_data["doc_comment"]["attribs"] : null;
			if (!empty($attr["name"]))
			{
				$a_name = $attr["name"];
				$x_a = array();
				if (isset($attr["default"]) && $attr["default"] == 1)
				{
					$x_a["default"] = 1;
				}

				if (isset($attr["nologin"]) && $attr["nologin"] == 1)
				{
					$x_a["nologin"] = 1;
				}

				if (isset($attr["is_public"]) && $attr["is_public"] == 1)
				{
					$x_a["is_public"] = 1;
				}

				if (isset($attr["all_args"]) && $attr["all_args"] == 1)
				{
					$x_a["all_args"] = 1;
				}

				if (isset($attr["is_content"]) && $attr["is_content"] == 1)
				{
					$x_a["is_content"] = 1;
				}

				if (!empty($attr["caption"]))
				{
					// php5 compliance
					$x_a["caption"] = htmlentities($attr["caption"]);
					//$x_a["caption"] = str_replace("&", "&amp;", $attr["caption"]);
				}

				$this->validate_fields(array(
					"data" => $attr,
					"type" => "attrib",
					"func" => $f_name,
				));

				// make parameters
				$par = isset($f_data["doc_comment"]["params"]) ? new aw_array($f_data["doc_comment"]["params"]) : new aw_array();

				//
				$arguments = array();
				foreach($par->get() as $p_name => $p_dat)
				{
					$x_p = array();
					$x_p["req"] = $p_dat["req"];
					if (!empty($p_dat["type"]))
					{
						$x_p["type"] = $p_dat["type"];
					}

					if(!empty($p_dat["class_id"]))
					{
						$x_p["class_id"] = $p_dat["class_id"];
					}

					if (!empty($p_dat["acl"]))
					{
						$x_p["acl"] = $p_dat["acl"];
					}

					if (!empty($p_dat["default"]))
					{
						$x_p["default"] = $p_dat["default"];
					}

					if (!empty($p_dat["value"]))
					{
						$x_p["value"] = $p_dat["value"];
					}
					
					$this->validate_fields(array(
						"data" => $p_dat,
						"type" => "param",
						"func" => $f_name,
					));
					
					$arguments[$p_name] = $x_p;
				}

				$rv[$a_name] = array(
					"function" => $f_name,
					"actionattribs" => $x_a,
					"arguments" => $arguments,
				);

			}
		}
		return $rv;
	}

	function _get_orb_xml($arr,$classdata)
	{
		$xml  = "<?xml version='1.0'?>\n";
		$xml .= "<orb>\n";
		$folder = str_replace($this->cfg["classdir"],"",dirname($classdata["file"]));
		if (substr($folder,0,1) == "/")
		{
			$folder = substr($folder,1);
		};
		$xml .= "\t<class name=\"".$classdata["name"]."\" folder=\"".$folder."\" extends=\"".$classdata["extends"]."\">\n";
		foreach($arr as $aname => $adata)
		{
			// tuleb moodustada action string
			// tuleb moodustada function string
			$xml .= "\t\t<action name=\"$aname\"";
			foreach($adata["actionattribs"] as $act_name => $act_value)
			{
				$xml .= " $act_name=\"$act_value\"";
			};
			$xml .= ">\n";
			$xml .= "\t\t\t<function name=\"" . $adata["function"] . "\">\n";
			$xml .= "\t\t\t\t<arguments>\n";
			foreach($adata["arguments"] as $arg_name => $arg_data)
			{
					if(empty($arg_data["req"]))
					{
						$arg_data["req"] = "optional";
					}
					$xml .= "\t\t\t\t\t<".$arg_data["req"]." name=\"$arg_name\"";
					unset($arg_data["req"]);
					foreach($arg_data as $akey => $aval)
					{
						$xml .= " $akey=\"$aval\"";
					};
					// kuidas ma edastan kas argument on required v6i optional?

					$xml .= " />\n";
			};
			$xml .= "\t\t\t\t</arguments>\n";
			$xml .= "\t\t\t</function>\n";
			$xml .= "\t\t</action>\n\n";
		}

		$xml .= "\t</class>\n";
		$xml .= "</orb>\n";
		return $xml;
	}

	// that is just plain wrong .. it should return orb defs, not create xml out of them
	function _get_orb_defs($data)
	{
		$xml  = "<?xml version='1.0'?>\n";
		$xml .= "<orb>\n";

		$folder = substr(dirname($data["file"]), 1);

		$xml .= "\t<class name=\"".$data["name"]."\" folder=\"".$folder."\" extends=\"".$data["extends"]."\">\n";

		foreach($data["functions"] as $f_name => $f_data)
		{
			// func is public if name attrib is set
			$attr = $f_data["doc_comment"]["attribs"];
			if (!empty($attr["name"]))
			{
				$a_name = $attr["name"];
				$xml .= "\t\t<action name=\"$a_name\"";
				$x_a = array();
				if (isset($attr["default"]) && $attr["default"] == 1)
				{
					$x_a[] = "default=\"1\"";
				}

				if (isset($attr["nologin"]) && $attr["nologin"] == 1)
				{
					$x_a[] = "nologin=\"1\"";
				}

				if (isset($attr["is_public"]) && $attr["is_public"] == 1)
				{
					$x_a[] = "is_public=\"1\"";
				}

				if (isset($attr["all_args"]) && $attr["all_args"] == 1)
				{
					$x_a[] = "all_args=\"1\"";
				}

				if (isset($attr["is_content"]) && $attr["is_content"] == 1)
				{
					$x_a[] = "is_content=\"1\"";
				}

				if (!empty($attr["caption"]))
				{
					// php5 compliance
					$x_a[] = "caption=\"".htmlentities($attr["caption"])."\"";
					//$x_a[] = "caption=\"".str_replace("&", "&amp;", $attr["caption"])."\"";
				}

				$xml .= " ".join(" ", $x_a).">\n";

				$xml .= "\t\t\t<function name=\"$f_name\">\n";
				$xml .= "\t\t\t\t<arguments>\n";

				// make parameters
				$par = new aw_array($f_data["doc_comment"]["params"]);

				foreach($par->get() as $p_name => $p_dat)
				{
					$xml .= "\t\t\t\t\t<".$p_dat["req"]." name=\"$p_name\"";

					$x_p = array();
					if (isset($p_dat["type"]) && $p_dat["type"] != "")
					{
						$x_p[] = "type=\"".$p_dat["type"]."\"";
					}

					if(isset($p_dat["class_id"]) && $d_dat["class_id"] != "")
					{
						$x_p[] = "class_id=\"".$p_dat["class_id"]."\"";
					}

					if (isset($p_dat["acl"]) && $p_dat["acl"] != "")
					{
						$x_p[] = "acl=\"".$p_dat["acl"]."\"";
					}

					if (isset($p_dat["default"]) && $p_dat["default"] != "")
					{
						$x_p[] = "default=\"".$p_dat["default"]."\"";
					}

					if (isset($p_dat["value"]) && $p_dat["value"] != "")
					{
						$x_p[] = "value=\"".$p_dat["value"]."\"";
					}

					$xml .= " ".join(" ", $x_p)."/>\n";
				}
				$xml .= "\t\t\t\t</arguments>\n";
				$xml .= "\t\t\t</function>\n";
				$xml .= "\t\t</action>\n\n";
			}
		}
		$xml .= "\t</class>\n";
		$xml .= "</orb>\n";
		return $xml;
	}

	function make_orb_defs_from_doc_comments()
	{
		$p = new parser();
		$files = array();
		$p->_get_class_list(&$files, AW_DIR . "classes");

		foreach($files as $file)
		{
			/*
			$ignp = $this->cfg["basedir"]."/classes/core/locale";
			if (substr($file, 0, strlen($ignp)) == $ignp)
			{
				continue;
			}
			*/
			// check if file is modified
			$clmod = @filemtime($file);
			$xmlmod = @filemtime(AW_DIR . "xml/orb/".basename($file, ".aw").".xml");

			if ($clmod >= $xmlmod)
			{
				$da = new aw_code_analyzer();
				$cld = $da->analyze_file($file, true);
				
				// if there are no classes in the file then it gets ignored
				if (!is_array($cld["classes"]) || count($cld["classes"]) < 1)
				{
					continue;
				}

				foreach($cld["classes"] as $class => $cldat)
				{					
					if (is_array($cldat["functions"]) && !empty($class) && strtolower($class) == strtolower(basename($file, ".aw")))
					{
						// count orb methods
						$orb_method_count = 0;

						$this->currentclass = $class;

						// XXX: figure out what the duke is going on here?
						$od = $this->_get_orb_defs2($cldat);

						if (sizeof($od) == 0)
						{
							// check if parent class has orb actions
							if (!empty($cldat["extends"]))
							{
								$orb_i = new orb();
								$pr_defs = $orb_i->load_xml_orb_def($cldat["extends"]);
								$has = false;
								if (is_array($pr_defs))
								{
									foreach($pr_defs[$cldat["extends"]] as $def)
									{
										if (!empty($def["function"]))
										{
											$has = true;
										}
									}
								}
								if (!$has)
								{
									continue;
								}
							}
							else
							{
								continue;
							}
						};
						echo "make orb defs for $file\n";
						$xml = $this->_get_orb_xml($od,$cldat);
						//print_r($xml);
						//continue;
						//$od = str_replace(substr($this->cfg["basedir"]."/classes/",1), "", $this->_get_orb_defs($cldat));
						//$od = str_replace(substr($this->cfg["basedir"]."/classes",1), "", $od);

						$this->put_file(array(
							"file" => AW_DIR . "xml/orb/{$class}.xml",
							"content" => $xml
						));
					}
				}
				flush();
			}
		}
		echo ("all done\n");
	}

	function validate_fields($data)
	{
		if(!$this->tagdata)
		{
			$this->set_tagdata();
		}
		$vals = $this->tagdata[$data["type"]];
		foreach($data["data"] as $var => $val)
		{
			if(!isset($vals[$var]))
			{
				print "***WARNING: Unknown field {$var} in {$data["type"]} at {$data["func"]} ({$this->currentclass})\n";
			}
		}
	}

	function set_tagdata()
	{
		$xmldir = AW_DIR . "xml/";
		$xml = simplexml_load_file($xmldir."orb_types.xml");
		foreach($xml->children() as $k1 => $v1)
		{
			foreach($v1->children() as $k2 => $v2)
			{
				$data[$k1][$k2] = reset($v2->attributes());
			}
		}
		$this->tagdata = $data;
	}
}
?>
