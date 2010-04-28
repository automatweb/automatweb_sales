<?php

namespace automatweb;

class object_script_interpreter extends class_base
{
	// tokens for the script interpreter
	const TOK_VAR_ASSIGN = 1;	// params { $name }
	const TOK_CREATE_INI = 2;	// params { }
	const TOK_CREATE_OBJ = 3;	// params { }
	const TOK_SETTING = 4;	// params { $name,$value }
	const TOK_CMD_END = 5;		// params { }  - ends line exec
	const TOK_CREATE_REL = 6;

	protected $lineno = 0;
	protected $ext_id_prop = "";
	protected $created_objs = array();
	protected $ext_ids = array();
	protected $ini_settings = array();
	protected $sym_table = array();

	/** executes the object script given in the file $file

		@attrib name=exec_file api=1

		@param file required type=string
			The file containing the script to execute

		@param vars optional type=array
			Array of name=>value pairs that will be set as variables in the script

		@param silent optional type=bool default=false
			If set to true, the script execution is silent

		@param ext_id_prop optional type=string default=''
			If set, returned array will contain an additional element "ext_ids" with values of that property, if exists

		@errors
			error is thrown if the file to execute does not exist
			error is thrown if a syntax error is in the file or it contains undefined symbols

		@returns
			an array with keys:
				"created_objs" => array of oids of the objects that the script created
				"ext_ids" => array of values of ext_id_prop of the objects that the script created (same index as created_objs)
				"ini_settings" => array of name => value pairs of ini settings that the script created
				"vars" => array of name => value pairs of the variables that were in the script

		@comment
			Description of the format of object scripts is in
			$AW_ROOT/docs/tutorials/object_script_interpreter/usage.txt

		@examples:
			-- script.ojs --
			$users = obj { class_id=CL_MENU, parent=${parent}, name="Kasutajad", type=MN_CLIENT, status=STAT_ACTIVE }
			ini { menu.num_menu_images = 3 }
			--- end file --

			-- code --
			$osi = get_instance("install/object_script_interpreter");
			$rv = $osi->exec_file(array(
				"file" => "script.ojs",
				"vars" => array(
					"parent" => 666
				)
			));

			// echos array(menu.num_menu_images => 3)
			echo "ini settings = ".dbg::dump($rv["ini_settings"])." <br>";

			// echos array(parent => 666, users => oid_of_the_new_object
			echo "vars = ".dbg::dump($rv["vars"])." <br>";

			// echos array(oid_of_the_new_object)
			echo "created_objects = ".dbg::dump($rv["created_objs"])." <br>";

	**/
	function exec_file($arr)
	{
		$file = $arr["file"];
		$sc = $this->get_file(array("file" => $file));
		error::raise_if($sc === false, array(
			"id" => "ERR_NO_FILE",
			"msg" => sprintf(t("object_script_interpreter::exec_file(%s): file does not exist!"), $file)
		));

		return $this->exec(array(
			"silent" => isset($arr["silent"]) ? $arr["silent"] : false,
			"script" => $sc,
			"ext_id_prop" => $arr["ext_id_prop"],
			"vars" => $arr["vars"]
		));
	}

	/** executes the script given in $script

		@attrib name=exec api=1 params=name

		@param script required type=string
			The script to execute in a string

		@param vars optional type=array
			Array of name=>value pairs that will be set as variables in the script

		@param silent optional type=bool default=false
			If set to true, the script execution is silent

		@param ext_id_prop optional type=string default=''
			If set, returned array will contain an additional element "ext_ids" with values of that property, if exists

		@errors
			error is thrown if a syntax error is in the file or it contains undefined symbols

		@returns
			an array with keys:
				"created_objs" => array of oids of the objects that the script created
				"ext_ids" => array of values of ext_id_prop of the objects that the script created (same index as created_objs)
				"ini_settings" => array of name => value pairs of ini settings that the script created
				"vars" => array of name => value pairs of the variables that were in the script

		@comment
			Description of the format of object scripts is in
			$AW_ROOT/docs/tutorials/object_script_interpreter/usage.txt

		@examples:
			$script = "\$users = obj { class_id=CL_MENU, parent=\${parent}, name=\"Kasutajad\", type=MN_CLIENT, status=STAT_ACTIVE }\nini { menu.num_menu_images = 3 }\n";

			-- code --
			$osi = get_instance("install/object_script_interpreter");
			$rv = $osi->exec(array(
				"script" => $script,
				"vars" => array(
					"parent" => 666
				)
			));

			// echos array(menu.num_menu_images => 3)
			echo "ini settings = ".dbg::dump($rv["ini_settings"])." <br>";

			// echos array(parent => 666, users => oid_of_the_new_object
			echo "vars = ".dbg::dump($rv["vars"])." <br>";

			// echos array(oid_of_the_new_object)
			echo "created_objects = ".dbg::dump($rv["created_objs"])." <br>";

	**/
	function exec($arr)
	{
		$this->_init_sym_table($arr["vars"]);
		$this->lineno = 0;

		if (!empty($arr["ext_id_prop"]))
		{
			$this->ext_id_prop = $arr["ext_id_prop"];
		}

		$lines = explode("\n", $arr["script"]);
		foreach($lines as $line)
		{
			$nl = trim($line);
			if (strlen($nl) > 0 and "#" !== $nl{0})
			{
				$this->_exec_line($nl, !$arr["silent"]);
			}
		}

		return array(
			"created_objs" => $this->_get_created_objs(),
			"ext_ids" => $this->ext_ids,
			"ini_settings" => $this->_get_ini_settings(),
			"vars" => $this->_get_sym_table()
		);
	}

	function _init_sym_table($vars)
	{
		$this->created_objs = array();
		$this->ext_ids = array();
		$this->ini_settings = array();
		$this->sym_table =array();

		$awa = new aw_array($vars);
		foreach($awa->get() as $k => $v)
		{
			$this->sym_table[$k] = $v;
		}
	}

	function _get_sym_table()
	{
		return $this->sym_table;
	}

	function _get_created_objs()
	{
		return $this->created_objs;
	}

	function _get_ini_settings()
	{
		if (!is_array($this->ini_settings))
		{
			return array();
		}
		return $this->ini_settings;
	}

	function _get_sym($n)
	{
		error::raise_if(!isset($this->sym_table[$n]), array(
			"id" => "ERR_OSI_NO_VAR",
			"msg" => sprintf(t("object_script_interpreter::_get_sym(%s): no variable by the name %s defined on line %s!"), $n, $n, $this->lineno)
		));
		return $this->sym_table[$n];
	}

	function _replace_syms($line)
	{
		$this->lineno++;
		// unquoted syms are !{name}
		$line = preg_replace('/\!\{(.*)\}/USe',"\"\".\$this->_get_sym(\"\\1\").\"\"",$line);
		return preg_replace('/\$\{(.*)\}/USe',"\"\\\"\".\$this->_get_sym(\"\\1\").\"\\\"\"",$line);
	}

	function _tokenize_line($line)
	{
		$toks = array();

		// now, check for var assign
		if ($line{0} === "$")
		{
			$sppos = min(strpos($line, "="), strpos($line, " "));

			$toks[] = array(
				"tok" => self::TOK_VAR_ASSIGN,
				"params" => array(
					"name" => trim(substr($line, 1, $sppos-1))
				)
			);

			$line = trim(substr($line, $sppos));
			if ($line{0} === "=")
			{
				$line = trim(substr($line, 1));
			}
		}

		// now, check for obj/ini
		$str = substr($line, 0, 3);
		if ($str === "obj")
		{
			$toks[] = array(
				"tok" => self::TOK_CREATE_OBJ,
				"params" => array()
			);
		}
		else
		if ($str === "ini")
		{
			$toks[] = array(
				"tok" => self::TOK_CREATE_INI,
				"params" => array()
			);
		}
		else
		if ($str === "rel")
		{
			$toks[] = array(
				"tok" => self::TOK_CREATE_REL,
				"params" => array()
			);
		}
		else
		{
			error::raise(array(
				"id" => "ERR_OSI_PARSE",
				"msg" => sprintf(t("object_script_interpreter::_tokenize_line(%s): parse error - unrecognized command on line %s!"), $line, $this->lineno)
			));
		}

		$line = trim(substr($line, 3));

		error::raise_if($line{0} !== "{", array(
			"id" => "ERR_OSI_PARSE",
			"msg" => sprintf(t("object_script_interpreter::_tokenize_line(%s): parse error no opening brace after command on line %s!"), $line, $this->lineno)
		));

		error::raise_if($line{strlen($line)-1} !== "}", array(
			"id" => "ERR_OSI_PARSE",
			"msg" => sprintf(t("object_script_interpreter::_tokenize_line(%s): parse error no closing brace on line %s!"), $line, $this->lineno)
		));

		$line = trim(substr(substr($line,0,-1), 1));

		// now we gots to parse the opts
		$len = strlen($line);

		// read name=value
		$cnt = 0;

		while ($cnt < $len)
		{
			while ($line{$cnt} === " " || $line{$cnt} === "=" || $line{$cnt} === "\t")
			{
				$cnt++;
			}

			$o_n = "";
			while($line{$cnt} !== "=" && $cnt < $len)
			{
				$o_n .= $line{$cnt};
				$cnt++;
			}

			while ($line{$cnt} === " " || $line{$cnt} === "=" || $line{$cnt} === "\t")
			{
				$cnt++;
			}

			$o_v = "";
			if ($line{$cnt} === "\"")
			{
				$cnt++;
				// read quoted value
				while ($cnt < $len && $line{$cnt} !== "\"")
				{
					$o_v .= $line{$cnt};
					$cnt++;
				}

				// and skip the final "
				$cnt++;
			}
			else
			{
				// read un quoted value
				while ($cnt < $len && $line{$cnt} !== " " && $line{$cnt} !== ",")
				{
					$o_v .= $line{$cnt};
					$cnt++;
				}
			}

			// skip final spaces && ,
			while ($line{$cnt} === " " || $line{$cnt} === "," || $line{$cnt} === "\t")
			{
				$cnt++;
			}

			if (strlen($o_n) > 0 and $o_n{0} === "\"")
			{
				$o_n = substr($o_n, 1);
			}

			$toks[] = array(
				"tok" => self::TOK_SETTING,
				"params" => array(
					"name" => trim($o_n),
					"value" => trim($o_v)
				)
			);
		}

		$toks[] = array(
			"tok" => self::TOK_CMD_END,
			"params" => array()
		);

		return $toks;
	}


	function _exec_line($line, $echo = true)
	{
		$line = $this->_replace_syms($line);
		$toks = $this->_tokenize_line($line);

		$start = 0;
		if ($toks[$start]["tok"] == self::TOK_VAR_ASSIGN)
		{
			$start = 1;
		}

		if ($toks[$start]["tok"] == self::TOK_CREATE_OBJ)
		{
			$cnt = $start+1;

			if ($start === 1)
			{
				$this->sym_table[$toks[0]["params"]["name"]] = "test_value";
			}
		}

		if ($echo)
		{
			echo "exec line $line <br>\n";
			flush();
		}
		$this->_exec_toks($toks);
	}

	function _syntax_check_line($line)
	{
		$line = $this->_replace_syms($line);
		$toks = $this->_tokenize_line($line);

		$start = 0;
		if ($toks[$start]["tok"] == self::TOK_VAR_ASSIGN)
		{
			$start = 1;
		}

		if ($toks[$start]["tok"] == self::TOK_CREATE_OBJ)
		{
			$cnt = $start+1;

			if ($start === 1)
			{
				$this->sym_table[$toks[0]["params"]["name"]] = "test_value";
			}
		}
	}

	function _dbg_tok_dump($toks)
	{
		foreach($toks as $n => $t)
		{
			echo "tok $n = { $t[tok], params = ".join(",", map2("%s => %s", $t["params"]))." }<br>";
		}
	}

	function _exec_toks($toks)
	{
		$start = 0;
		if ($toks[$start]["tok"] == self::TOK_VAR_ASSIGN)
		{
			$start = 1;
		}

		if ($toks[$start]["tok"] == self::TOK_CREATE_OBJ)
		{
			$cnt = $start+1;
			$o = new object();

			// go over opts to set class id first
			while ($toks[$cnt]["tok"] != self::TOK_CMD_END)
			{
				if ($toks[$cnt]["params"]["name"] === "class_id")
				{
					$o->set_class_id($this->_get_value($toks[$cnt]["params"]["value"]));
				}
				$cnt++;
			}

			// now set all opts
			$cnt = $start+1;
			while ($toks[$cnt]["tok"] != self::TOK_CMD_END)
			{
				if ($toks[$cnt]["params"]["name"] === "metadata")
				{
					$v = aw_unserialize($this->_get_value($toks[$cnt]["params"]["value"]));
					foreach(safe_array($v) as $k => $v)
					{
						$o->set_meta($k, $v);
					}
				}
				else
				if (substr($toks[$cnt]["params"]["name"], 0, 5) === "meta.")
				{
					$mn = substr($toks[$cnt]["params"]["name"], 5);
					$o->set_meta($mn, $this->_get_value($toks[$cnt]["params"]["value"]));
				}
				else
				{
					switch($toks[$cnt]["params"]["name"])
					{
						case "class_id":
							break;

						case "parent":
							$o->set_parent($this->_get_value($toks[$cnt]["params"]["value"]));
							break;

						case "lang_id":
							$o->set_lang_id($this->_get_value($toks[$cnt]["params"]["value"]));
							break;

						case "flags":
							$o->set_flags($this->_get_value($toks[$cnt]["params"]["value"]));
							break;

						default:
							$o->set_prop($toks[$cnt]["params"]["name"], $this->_get_value($toks[$cnt]["params"]["value"]));
							break;
					}
				}

				$cnt++;
			}

			$o->save();
			$this->created_objs[] = $o->id();

			if ($this->ext_id_prop)
			{
				if ($o->is_property($this->ext_id_prop))
				{
					$this->ext_ids[] = $o->prop($this->ext_id_prop);
				}
				else
				{
					$this->ext_ids[] = null;
				}
			}

			if ($start == 1)
			{
				$this->sym_table[$toks[0]["params"]["name"]] = $o->id();
			}
		}
		else
		if ($toks[$start]["tok"] == self::TOK_CREATE_INI)
		{
			$this->ini_settings[$toks[$start+1]["params"]["name"]] = $toks[$start+1]["params"]["value"];
		}
		else
		if ($toks[$start]["tok"] == self::TOK_CREATE_REL)
		{
			$c = new connection();
			$parm = array();
			// now set all opts
			$cnt = $start+1;
			while ($toks[$cnt]["tok"] != self::TOK_CMD_END)
			{
				$parm[$toks[$cnt]["params"]["name"]] = $this->_get_value($toks[$cnt]["params"]["value"]);
				$cnt++;
			}

			if (!count($parm) || !is_oid($parm["from"]) || !is_oid($parm["to"]))
			{
				error::raise(array(
					"id" => "ERR_OSI_REL",
					"msg" => t("object_script_interpreter::_exec_toks(): relation must have both ends defined!")
				));
			}

			$c->change($parm);

			if ($start == 1)
			{
				$this->sym_table[$toks[0]["params"]["name"]] = $c->id();
			}
		}
	}

	function _get_value($v)
	{
		if (defined($v))
		{
			return constant($v);
		}
		return $v;
	}
}
?>
