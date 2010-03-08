<?php
// xml.aw - xml serializer
// at the moment (Apr 25, 2001) it can serialize PHP arrays to XML and vice versa
/*
@classinfo  maintainer=kristo
*/
class xml
{
	////
	// !Konstruktor
	function xml($args = array())
	{
		// konteiner tag
		$this->ctag = isset($args["ctag"]) ? $args["ctag"] : "xml";
		// xml versioon
		$this->xml_version = isset($args["xml_version"]) ? $args["xml_version"] : "1.0";
		// numbriliste elementide prefix arrayde serialiseerimise juures
		$this->num_prefix = isset($args["num_prefix"]) ? $args["num_prefix"] : "num_";

		$this->child_id = array();

		$this->enumerate = true;
		if (isset($args["enumerate"]))
		{
			$this->enumerate = $args["enumerate"];
		};

		if (isset($args["child_id"]) && is_array($args["child_id"]))
		{
			foreach($args["child_id"] as $key => $val)
			{
				$this->set_child_id($key,$val);
			};
		};
	}

	// lopetab xml definitsiooni (s.t. lisab versiooninumbri ning root tagi
	function _complete_definition($args = array())
	{
		$data = $args["data"];
		if ($this->ctag)
		{
			$retval = sprintf("<?xml version='%s'?>\n<%s>\n%s</%s>\n",$this->xml_version,$this->ctag,$data,$this->ctag);
		}
		else
		{
			$retval = sprintf("<?xml version='%s'?>\n%s\n",$this->xml_version,$data);
		};
		return $retval;
	}

	////
	// !Set a name for children nodes. This is used instead of num_XX if applicable
	// NB! Right now the unserializer cannot decode data in that format.
	// damn it .. I really could use an xpath interpreter
	function set_child_id($key,$id)
	{
		$this->child_id[$key] = $id;
	}

	////
	// !Genereerib parameetrige pohjal tag-i
	function xml_gen_tag($args = array())
	{
		// nende m2rkide puhul kasutame tagi v22rtuse esitamisel CDATA notatsiooni,
		// vastasel juhul lihtsalt v2ljastame stringi
		$specials = array("<",">","\"");

		$tag = $args["tag"];
		$value = $args["value"];
		$spacer = $args["spacer"];

		// tulem on soltuvalt spetsiaalm2rkide olemasolust $value-s, kas
		//	<tag>
		//	<![CDATA[
		//	value
		//	]]>
		//	</tag>
		//
		//	v6i
		//
		// 	<tag>value</tag>
		//
		// kusjuures koikidele ridadele liidetakse ette $spacer

		reset($specials);
		$is_special = false;
		foreach($specials as $spec_char)
		{
			$pos = strpos($value,$spec_char);
			if ($pos === false)
			{

			}
			else
			{
				$is_special = true;
			};
		};

		$value = str_replace("&","&amp;",$value);
		if ($is_special)
		{
			$retval = $spacer . "<$tag>\n" . $spacer . "<![CDATA[" . $value . "]]>\n" . $spacer . "</$tag>\n";
		}
		else
		{
			$retval = $spacer . "<$tag>" . $value . "</$tag>" . "\n";
		};
		return $retval;
	}

	////
	// !Serialiseerib array XML-i. Kutsub ennast rekursiivselt v2lja
	// arg - array
	function xml_serialize($arg = array())
	{
		$this->parents = array();
		$tmp = $this->_xml_serialize($arg);
		$r = $this->_complete_definition(array(
			"data" => $tmp,
		));
		return $r;
	}

	////
	// !This one does the all the dirty job
	function _xml_serialize($arg = array(),$level = 0)
	{
		if (!is_array($arg))
		{
			return;
		};

		if (sizeof($arg) == 0)
		{
			return;
		};

		$tmp = "";
		$realval = "";

		reset($arg);
		foreach($arg as $key => $val)
		{
			// kui $val on array, siis t88tleme seda rekursiivselt,
			// muidu salvestame tagi siia
			$spacer = str_repeat("      ",$level);

			// numbrilised tagid pole paraku lubatud, seega liidame neile prefiksina "num_" ette
			if (gettype($key) == "integer")
			{
				$plen = sizeof($this->parents);
				if ($plen >= 1)
				{
					$pkey = $this->parents[$plen-1];
				}

				if (!empty($this->child_id[$pkey]))
				{
					$xkey = $this->child_id[$pkey];
				}
				else
				{
					$xkey = $this->num_prefix;
					if ($this->enumerate)
					{
						$xkey .= $key;
					};
				};
				$key = $xkey;
			};

			if (is_array($val))
			{
				$level++;
				$realval .= sprintf("%s<%s>\n",$spacer,$key);
				array_push($this->parents,$key);
				$realval .= $this->_xml_serialize($val,$level);
				array_pop($this->parents);
				$realval .= sprintf("%s</%s>\n",$spacer,$key);
				$level--;
			}
			else
			{
				$realval .= $this->xml_gen_tag(array(
					"tag" => $key,
					"value" => $val,
					"spacer" => $spacer,
				));
			};
			$tmp = $realval;
		};
		return $tmp;
	}

	////
	// !V6tab XML definitsiooni (mis peab olema korrektne), ning tagastab php array
	// source - xml
	function xml_unserialize($args = array())
	{
		$source = $args["source"];
		$retval = array();
		$ckeys = array();

		// parsimist enam kiiremaks ei saa, see toimub enivei PHP siseselt
		$parser = xml_parser_create();

		// keerame tag-ide suurt2htedeks konvertimise maha
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);

		// kui tahad aru saada mida see funktsioon ja j2rgnevad read teevad, siis debuukimise ajal
		xml_parse_into_struct($parser,$source,&$keys,&$values);
		/*{
			echo "error! <br>";
echo xml_error_string(xml_get_error_code($parser))."<br>";
echo "line = ".xml_get_current_line_number($parser);
		}*/
		// void siin teha midagi sellist: print_r($keys), print_r($values);

		// Good parser. Now go back where you came from
		xml_parser_free($parser);

		$datablock = "";
		$ckeys = array();
		$path = "";

		foreach($keys as $k1 => $v1)
		{
			if ( ($v1["type"] == "cdata") || ($v1["level"] < 2) )
			{
				continue;
			};
			$tag = $v1["tag"];

			$pref_idx = strpos($tag,$this->num_prefix);
			if (not($pref_idx === false))
			{
				$realkey = substr($tag,strlen($this->num_prefix));
				if ($realkey == sprintf("%d",$realkey))
				{
					$tag = $realkey;
				};
			};

			// kui lopetet tag, siis on meil v22rtus k2es, ja rohkem pole vaja midagi teha
			if ($v1["type"]	== "complete")
			{
				$path1 = $path . "[\"" . $tag . "\"]";

				// value algusest ja l6pust liigne r2ga maha
				$value = trim(isset($v1["value"]) ? $v1["value"] : "");

				// moodustame evali jaoks rea
				$value = str_replace("\\","\\\\",$value);
				$value = str_replace("\"","\\\"",$value);
				$datablock .= "\$retval" . $path1 . "=\"$value\";\n";
			}
			elseif ($v1["type"] == "open")
			{
				array_push($ckeys,sprintf("[\"%s\"]",$tag));
				$path = join("",$ckeys);
			}
			elseif ($v1["type"] == "close")
			{
				$void = array_pop($ckeys);
				$path = join("",$ckeys);
			};
			// ylej22nud tage ignoreeritakse
		}
		eval($datablock);
		return $retval;
	}

};

?>
