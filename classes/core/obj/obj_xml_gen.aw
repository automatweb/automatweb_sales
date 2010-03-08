<?php
/*
@classinfo  maintainer=kristo
*/

class obj_xml_gen
{
	/**
		@comment
			options are:

			copy_subobjects
			copy_subfolders
			copy_subdocs
			copy_rels
			new_rels
			no_header
	**/
	function gen($oid, $options)
	{
		$o = obj($oid);

		$obj_list = $this->_gather_objects($o, $options);

		$xml = $options["no_header"] ? "<obj id=\"{$oid}\">\n" : "<?xml version='1.0' encoding='".aw_global_get("charset")."'?>\n<obj id=\"{$oid}\">\n";
		$xml .= "<start_object>$oid</start_object>\n";
		$xml .= "<objects>\n";
		list($obj_xml, $id_map) = $this->_ser_objects($o, $obj_list);
		$xml .= $obj_xml;

		$xml .= "</objects>\n";
		$xml .= "<rels>\n";
		$xml .= $this->_ser_rels($o, $obj_list, $id_map, $options);
		$xml .= "</rels>\n</obj>\n";

		return $xml;
	}

	function _gather_objects($o, $options)
	{
		$copy_subobjects = isset($options["copy_subobjects"]) ? $options["copy_subobjects"] : true;
		$copy_subfolders = isset($options["copy_subfolders"]) ? $options["copy_subfolders"] : false;
		$copy_subdocs = isset($options["copy_subdocs"]) ? $options["copy_subdocs"] : false;
		$copy_rels = isset($options["copy_rels"]) ? $options["copy_rels"] : true;
		$new_rels = isset($options["new_rels"]) ? $options["new_rels"] : false;

		$other_objs = array();
		if ($copy_subobjects)
		{
			$other_objs = $this->_fetch_subobjs($o);
		}
		else
		if ($copy_subfolders)
		{
			$other_objs = $this->_fetch_subobjs($o, CL_MENU);
		}
		else
		if ($copy_subdocs)
		{
			$other_objs = $this->_fetch_subobjs($o, array(CL_MENU,CL_DOCUMENT));
		}

		$obj_list =  array($o) + $other_objs;

		$clss = aw_ini_get("classes");

		if ($new_rels)
		{
			foreach($obj_list as $idx => $o)
			{
				if ($clss[$o->class_id()]["no_copy"] == 1)
				{
					unset($obj_list[$idx]);
					continue;
				}
				foreach($o->connections_from() as $c)
				{
					$to = $c->to();
					if ($clss[$to->class_id()]["no_copy"] == 1)
					{
						continue;
					}
					$obj_list[$c->prop("to")] = $c->to();
					$this->_req_read_rel_objs($c->to(), $obj_list);
				}
			}
		}

		return $obj_list;
	}

	function _req_read_rel_objs($o, &$obj_list)
	{
		$clss = aw_ini_get("classes");
		foreach($o->connections_from() as $c)
		{
			if (isset($obj_list[$c->prop("to")]))
			{
				continue; // break cyclic rels
			}
			$to = $c->to();
			if ($clss[$to->class_id()]["no_copy"] == 1)
			{
				continue;
			}
			$obj_list[$c->prop("to")] = $c->to();
			$this->_req_read_rel_objs($c->to(), $obj_list);
		}
	}

	function _ser_objects($start, $other_objects)
	{
		$id_map = array();
		foreach($other_objects as $o)
		{
			list($o_xml, $new_id) = $this->_ser_one_obj($o, $id_map);
			$xml .= $o_xml;
			$id_map[$o->id()] = $new_id;
		}

		return array($xml, $id_map);
	}

	function _ser_one_obj($o, $id_map)
	{
		$id = gen_uniq_id();

		$xml = "\t<object>\n";
		$xml .= "\t\t<xml_id>$id</xml_id>\n";
		$xml .= "\t\t<id>".$o->id()."</id>\n";

		// all object table fields
		$flds = $GLOBALS["object_loader"]->all_ot_flds;
		$xml .= "\t\t<ot_flds>\n";
		foreach($flds as $fld => $t)
		{
			if ($fld == "cachedirty")
			{
				continue;
			}
			$fld = $fld == "jrk" ? "ord" : ($fld == "periodic" ? "is_periodic" : ($fld == "metadata" ? "meta" : $fld));
			$val = $o->$fld();
			if (false && $fld == "parent")
			{
				$val = $id_map[$val];
			}
			if($fld == "meta")
			{
				$val = serialize($val);
			}
			$xml .= "\t\t\t<$fld>".$this->_xml_ser_val($val)."</$fld>\n";
		}
		$xml .= "\t\t</ot_flds>\n";

		// all props
		$xml .= "\t\t<props>\n";
		foreach($o->properties() as $pn => $pv)
		{
			$xml .= "\t\t\t<$pn>".$this->_xml_ser_val($pv)."</$pn>\n";
		}
		$xml .= "\t\t</props>\n";
		$xml .= "\t</object>\n";
		return array($xml, $id);
	}

	function _fetch_subobjs($parent, $clid_filt = array())
	{
		$ot = new object_tree(array(
			"parent" => $parent->id(),
			"class_id" => $clid_filt
		));
		$ol = $ot->to_list();
		return $ol->arr();
	}

	function _xml_ser_val($v)
	{
		if ($v == "" || is_numeric($v))
		{
			return $v;
		}
		if (is_array($v))
		{
			$v = aw_serialize($v,SERIALIZE_PHP);
		}

		if (is_string($v))
		{
			$v = htmlentities($v);
			$len = strlen($v);
			for($i = 0; $i < $len; $i++)
			{
				if (ord($v[$i]) < 32)
				{
					$v[$i] = " ";
				}
			}
			return "<![CDATA[\n".$v."\n]]>";
		}
	}

	function _ser_rels($o, $obj_list, &$id_map, $options)
	{
		$copy_rels = isset($options["copy_rels"]) ? $options["copy_rels"] : true;
		$new_rels = isset($options["new_rels"]) ? $options["new_rels"] : false;
		$clss = aw_ini_get("classes");
		$xml = "";
		if ($copy_rels || $new_rels)
		{
			foreach($obj_list as $o)
			{
				$als = $o->meta("aliaslinks");
				foreach($o->connections_from() as $c)
				{
					$to = $c->to();
					if ($clss[$to->class_id()]["no_copy"] == 1)
					{
						continue;
					}
					$xml .= "\t<rel>\n";
					$xml .= "\t\t<from>".$c->prop("from")."</from>\n";
					$xml .= "\t\t<to>".$c->prop("to")."</to>\n";
					$xml .= "\t\t<from_xml>".$id_map[$c->prop("from")]."</from_xml>\n";
					if ($new_rels)
					{
						// recurse on the to object if not in map
						if (!isset($id_map[$c->prop("to")]))
						{
							die("error".__FILE__."::".__LINE__);
						}
						$xml .= "\t\t<to_xml>".$id_map[$c->prop("to")]."</to_xml>\n";
					}
					$xml .= "\t\t<reltype>".$c->prop("reltype")."</reltype>\n";
					$xml .= "\t\t<aliaslink>".$als[$c->prop("to")]."</aliaslink>\n";
					$xml .= "\t</rel>\n";
				}
			}
		}
		return $xml;
	}

	function _req_ser_rel($oid, $options, &$id_map)
	{
		list($xml, $id) = $this->_ser_one_obj($oid, $id_map);
		$id_map[$oid] = $id;
		return $xml;
	}

	function unser($xml, $parent)
	{
		// parse xml into struct
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		//xml_parse_into_struct($parser,$xml,&$keys,&$values);
		xml_set_element_handler($parser, array(&$this, "_start_el"), array(&$this, "_end_el"));
		xml_set_character_data_handler($parser, array(&$this, "_chard"));
		$res = xml_parse($parser, $xml, true);
		if (!$res)
		{
echo xml_error_string  (xml_get_error_code($parser))."<br>";
		echo 	sprintf('XML error at line %d column %d',
                    xml_get_current_line_number($parser),
                    xml_get_current_column_number($parser));
echo "<pre>".htmlentities($xml)."</pre>";
		}
		xml_parser_free($parser);
		// create objects
//echo dbg::dump($this->objects);

		$oid = $this->_crea_obj($this->objects[$this->start_object], $parent);
		unset($this->objects[$this->start_object]);
		$old2new = array($this->start_object => $oid);
		$this->_req_crea_objs($this->start_object, $oid, $old2new);

		// now create all objects under the same parent, that are out of the hierarchy
		// also, try to find object trees in objects that are not prented
		while (count($this->objects))
		{
			reset($this->objects);
			list($oid, $inf) = each($this->objects);

			$new_oid = $this->_crea_obj($inf, $parent);
			$old2new[$oid] = $new_oid;
			unset($this->objects[$oid]);
			$this->_req_crea_objs($oid, $new_oid, $old2new);
		}

		// create rels
		foreach($this->rels as $rel)
		{
			$new_oid = $old2new[$rel["from"]];
			$new_obj = obj($new_oid);
			if ($rel["to_xml"] != "")
			{
				$new_to = obj($old2new[$rel["to"]]);
			}
			else
			{
				$new_to = obj($rel["to"]);
			}

			$new_obj->connect(array(
				"to" => $new_to->id(),
				"type" => $rel["reltype"]
			));
	
			if ($rel["aliaslink"])
			{
				$lls = $new_obj->meta("aliaslinks");
				$lls[$new_to->id()] = 1;
				$new_obj->set_meta("aliaslinks", $lls);
				$new_obj->save();
			}
		}

		return $oid;
	}

	function _req_crea_objs($old_parent, $new_parent, &$oid_map)
	{
		// scan object list for objects with parent $old_parent
		// create objects for them and req on all objects created
		foreach($this->objects as $oid => $inf)
		{
			if ($inf["ot_flds"]["parent"] == $old_parent)
			{
				$new_oid = $this->_crea_obj($inf, $new_parent);
				$oid_map[$oid] = $new_oid;
				unset($this->objects[$oid]);
				$this->_req_crea_objs($oid, $new_oid, $oid_map);
			}
		}
	}

	function _crea_obj($data, $parent)
	{
		if($data["ot_flds"]["class_id"] == CL_USER && get_instance(CL_USER)->username_is_taken(trim($data["props"]["uid"])))
		{
			// Can't have two user objects with same uid!
			return get_instance(CL_USER)->get_obj_for_uid(trim($data["props"]["uid"]))->create_brother($parent);
		}
		$o = obj();
		$o->set_class_id($data["ot_flds"]["class_id"]);
		$o->set_parent($parent);
		$o->set_name(html_entity_decode($data["ot_flds"]["name"]));
		$o->set_status($data["ot_flds"]["status"]);
		$o->set_comment(html_entity_decode($data["ot_flds"]["comment"]));
		$o->set_ord($data["ot_flds"]["ord"]);
		$o->set_alias($data["ot_flds"]["alias"]);

		$o->set_subclass($data["ot_flds"]["subclass"]);
		$o->set_flags($data["ot_flds"]["flags"]);

		$pl = $o->get_property_list();

		// now, props
		foreach($data["props"] as $k => $v)
		{
			if (isset($data["ot_flds"][$k]))
			{
				continue;
			}
			
			// we need to skip metadata props, because they are serialized and we didn't do that here
			if ($o->is_property($k) && $pl[$k]["field"] != "metadata" && $pl[$k]["table"] != "objects")
			{
				$v = html_entity_decode($v);
				$o->set_prop($k, $v);
			}
		}

		$md = aw_unserialize(html_entity_decode(trim($data["ot_flds"]["meta"])));
		if (is_array($md))
		{
			foreach($md as $k => $v)
			{
				$v = $v;
				$o->set_meta($k, $v);
			}
		}

		$o->save();
		return $o->id();
	}

	function _start_el($parser, $name, $attr)
	{
		switch($name)
		{
			case "start_object":
				$this->_is_start_object = 1;
				break;

			case "obj";
				$this->seems_valid = true;
				break;

			case "objects":
				$this->_in_obj_list = 1;
				break;

			case "object":
				$this->cur_obj = array();
				$this->_in_obj = 1;
				break;

			case "xml_id":
			case "id":
				$this->index = $name;
				break;

			case "ot_flds":
			case "props":
				$this->index1 = $name;
				break;

			case "rel":
				$this->_in_rel = 1;
				$this->_in_obj = 0;
				$this->cur_rel = array();
				break;

			default:
				$this->index2 = $name;
				break;
		}
	}

	function _end_el($parser, $name)
	{
		switch($name)
		{
			case "start_object":
				$this->_is_start_object = 0;
				break;

			case "obj":
				break;

			case "objects":
				$this->_in_obj_list = false;
				break;

			case "object":
				$this->_in_obj = 0;
				$this->objects[$this->cur_obj["id"]] = $this->cur_obj;
				$this->cur_obj = false;
				break;

			case "xml_id":
			case "id":
				$this->index = false;
				break;

			case "ot_flds":
			case "props":
				$this->index1 = false;
				break;

			case "rel":
				$this->rels[] = $this->cur_rel;
				$this->cur_rel = false;
				$this->_in_rel = 0;
				break;
		}
	}

	function _chard($parser, $str)
	{
		$str = iconv("utf-8", aw_global_get("charset"), $str);
		if (trim($str) == "")
		{
			return;
		}

		if ($this->_is_start_object)
		{
			$this->start_object = $str;
		}
		if ($this->_in_obj)
		{
			if ($this->index)
			{
				$this->cur_obj[$this->index] .= $str;
			}
			else
			{
				if ($this->cur_obj[$this->index1][$this->index2] != "")
				{
					$this->cur_obj[$this->index1][$this->index2] .= "\n";
				}
				$this->cur_obj[$this->index1][$this->index2] .= $str;
			}
		}
		if ($this->_in_rel)
		{
			$this->cur_rel[$this->index2] .= $str;
		}
	}
}
?>
