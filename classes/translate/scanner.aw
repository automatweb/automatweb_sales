<?php
// parses a bunch o files and creates translation templates
class scanner extends aw_template
{
	function scanner()
	{
		$this->init(array(
			"no_db" => 1
		));
	}

	function req_dir($args = array())
	{
		$path = $args["path"];
		if ($dir = opendir($path))
		{
			while (($file = readdir($dir)) !== false)
			{
				# skip the stuff that starts with .
				if (substr($file,0,1) == ".")
				{
					continue;
				};

				$fqfn = $path . "/" . $file;
				if (is_dir($fqfn) && !is_link($fqfn) && ($file != "CVS"))
				{
					$this->req_dir(array("path" => $fqfn));
				}
				elseif (is_file($fqfn) && is_readable($fqfn) && (preg_match("/\.aw$/",$fqfn)))
				{
					$this->files[] = $fqfn;
				};
			};
			closedir($dir);
		};
	}

	function make_keys($arr)
	{
		// max() needs at least one element
		$ret = array(0);
		if (is_array($arr))
		{
			foreach($arr as $v)
			{
				$ret[$v] = $v;
			}
		}
		return $ret;
	}

	function run()
	{
		$cdir = $this->cfg["basedir"] . "/classes";
		$this->files = array();
		$this->req_dir(array("path" => $cdir));
		$files = $this->files;
		ksort($files);

		$this->winsize = 4;

		$trans_ini = file_get_contents('config/ini/translate.ini');
		preg_match_all("/translate\.ids\[(\d+)\]/",$trans_ini, $mt);
		$trans_ids = $this->make_keys($mt[1]);
		$new_ini = "";
		$next_free = max($trans_ids)+1;
		$used = array();

		// context translation map
		$this->match_table = array(
			"callback" => "text",
			"relpicker" => "select",
			"generated" => "text",
		);

		aw_global_set("no_db_connection",1);

		$cfgu = get_instance("cfg/cfgutils");

		foreach($files as $fname)
		{
			$this->valid = false;
			//$props = $cfgu->load_class_properties(array(
			$props = $cfgu->load_class_properties(array(
				"file" => basename($fname,".aw"),
			));
			$classinfo = $cfgu->classinfo;
			$trans_id = $classinfo["trans_id"]["text"];
			if (!empty($props) && !empty($trans_id))
			{
				$props = $cfgu->load_properties(array(
					"file" => basename($fname,".aw"),
				));
				$groupinfo = $cfgu->groupinfo;
				$this->scan_file(array(
					"file" => $fname,
					"props" => $props,
					"classinfo" => $classinfo,
					"groupinfo" => $groupinfo,
				));
			};
			if (!empty($this->trans_id) && !defined($this->trans_id) && !$used[$this->trans_id])
			{
				// create a new trans_id then!
				$new_ini .= "translate.ids[$next_free] = " . $this->trans_id . "\n";
				$used[$this->trans_id] = 1;
				$next_free++;
			}
			if ($this->valid)
			{
				// write the translation out .. first figure out a name
				$outname = "xml/trtemplate/" . $this->trans_id . ".xml";

				// but before I can write it out, I need to read it in .. deserialize it
				// add the new strings .. and serialize it again .. and only then
				// can I write it out
				$old = file_exists($outname) ? $this->_unser($outname) : array();

				// but this shit will leave the original strings hanging around there
				$new = array_merge($old,$this->strings);

				$ser = aw_serialize(array_values($new),SERIALIZE_XML,array("ctag" => "trtemplate","num_prefix" => "string","enumerate" => false));

				$this->put_file(array(
					"file" => $outname,
					"content" => $ser,
				));
				print "writing $outname\n";


			}
		}

		$this->put_file(array(
			"file" => "config/ini/translate.ini",
			"content" => $trans_ini . $new_ini,
		));

		if (strlen($new_ini) > 0)
		{
			print "Following lines were added to translate.ini\n";
			print $new_ini;
			print "-----------\n";
		}

		print "ALL DONE!!!\n";
	}

	function scan_file($arr)
	{
		$this->strings = array();
		$this->trans_id = "";

		$basefile = basename($arr["file"],".aw");

		foreach($arr["props"] as $prop)
		{
			$propname = $prop["name"];
			$proptype = $prop["type"];

			// ignore hidden properties for now
			if ($proptype == "hidden")
			{
				continue;
			};

			if ($this->match_table[$proptype])
			{
				$ctx = $this->match_table[$proptype];
			}
			else
			{
				$ctx = $proptype;
			};

			$id = md5("prop" . $basefile . $propname);
			$this->strings[$id] = array(
				"id" => $id,
				"file" => $basefile,
				"ctx" => $ctx,
				"caption" => $prop["caption"],
				"comment" => $prop["comment"],
			);

			$this->trans_id = $arr["classinfo"]["trans_id"]["text"];

		}

		foreach($arr["groupinfo"] as $gname => $grp)
		{
			$id = md5("group" . $basefile . $gname);
			$this->strings[$id] = array(
				"id" => $id,
				"file" => $basefile,
				"ctx" => "tab",
				"caption" => $grp["caption"],
				"comment" => $grp["comment"],
			);
		}

		$this->valid = false;
		if (sizeof($this->strings) == 0)
		{

		}
		elseif (empty($this->trans_id))
		{
			//print "ERR: $fname doesn't have a defined translation context, skipping\n";
		}
		else
		{
			$this->valid = true;
			print "Updating $fname\n";
		};

	}

	function _unser($old)
	{
		$source = file_get_contents($old);
		$res = array();
		$p = xml_parser_create();
		xml_parser_set_option($p,XML_OPTION_CASE_FOLDING,0);
		xml_parse_into_struct($p,$source,$vals,$index);
		xml_parser_free($p);
		foreach($vals as $key => $val)
		{
			if ($val["tag"] == "id" && $val["type"] == "complete")
			{
				$id = $val["value"];
			};
			if ($val["tag"] == "file" && $val["type"] == "complete")
			{
				$file = $val["value"];
			};
			if ($val["tag"] == "ctx" && $val["type"] == "complete")
			{
				$ctx = $val["value"];
			};
			if ($val["tag"] == "caption" && $val["type"] == "complete")
			{
				$caption = $val["value"];
			};
			if ($val["tag"] == "comment" && $val["type"] == "complete")
			{
				$comment = $val["value"];
			};
			if ($val["tag"] == "string" && $val["type"] == "close")
			{
				$res[$id] = array(
					"id" => $id,
					"file" => $file,
					"ctx" => $ctx,
					"caption" => $caption,
					"comment" => $comment,
				);
				$file = $ctx = $caption = $comment = "";
			};
                }
		return $res;
	}


}
