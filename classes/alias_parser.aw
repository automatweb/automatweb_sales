<?php

class alias_parser
{
	private $tmp_vars;

	private static $aw_formatting_tags = array(
		"expander-trigger", "expander-content"
	);

	/** Parses aw formatting tags to html
		@attrib api=1 params=pos

		@param oid type=oid
			the object to which the text belongs to

		@param source type=string

		@comment
		@returns void
		@errors
	**/
	public static function parse_tags($oid, &$source)
	{
		// should eliminate 99% of the texts that don't contain aliases
		if(strpos($source, "#") === false)
		{
			return;
		}

		$tags = implode("|", self::$aw_formatting_tags);
		$tags_pattern = "/(##)(({$tags})\-?[0-9]*)(##)/iS";

		$_res = preg_match_all($tags_pattern, $source, $matches, PREG_SET_ORDER);
		if ($_res)
		{
			foreach ($matches as $key => $data)
			{
			}
		}
	}


	/**  Finds aw aliases in the given string and replaces them with their output
		@attrib api=1

		@param oid required type=int acl=view
			the object to which the text belongs to. the aliases in the text must be connected to this object, it is used to resolve the aliases to real objects

		@param source required type=string
			the text containing the aliases to be parsed. the text is modified so, that aliases are replaced with the output of their objects' parse_alias methods

		@param args required type=array
			array of other parameters:
				$templates - gets passed to the parse_alias method of the handler class as the parameter $tpls as a reference

		@errors
			none

		@returns
			none

		@examples

			$document = object($document_id);
			$content = $document->prop("lead")."&lt;br&gt;".$document->prop("content");

			$alp = get_instance("alias_parser");
			$alp->parse_oo_aliases($document->id(), $content);

			echo $content; // displays document with aliases parsed
	**/
	public function parse_oo_aliases($oid, &$source, $args = array())
	{
		// should eliminate 99% of the texts that don't contain aliases -- ahz
		if(strpos($source, "#") === false)
		{
			return;
		}

		$tags_pattern = "/(#)(\w+?)(\d+?)(v|k|p|\.([a-z_.]+)|)(#)/iS";

		$_res = preg_match_all($tags_pattern, $source, $matches, PREG_SET_ORDER);
		if (!$_res)
		{
			// if no aliases are in text, do nothin
			return;
		}

		extract($args);

		$this->tmp_vars = array();

		$o = obj($oid);
		if ($o->is_brother())
		{
			$o = $o->get_original();
			$oid = $o->id();
		}

		$aliases = $this->get_oo_aliases(array("oid" => $oid));

		$by_idx = $by_alias = array();

		$classlist = aw_ini_get("classes");
		foreach($classlist as $clid => $cldat)
		{
			if (isset($cldat["alias"]))
			{
				$li = explode(",", $cldat["alias"]);
				foreach($li as $lv)
				{
					if (isset($cldat["alias_class"]))
					{
						$by_alias[$lv]["file"] = $cldat["alias_class"];
					}
					else
					{
						$by_alias[$lv]["file"] = $cldat["file"];
					}
					$by_alias[$lv]["class_id"] = $clid;
				}
			}

			if (isset($cldat["old_alias"]))
			{
				$li = explode(",", $cldat["old_alias"]);
				foreach($li as $lv)
				{
					if (isset($cldat["alias_class"]))
					{
						$by_alias[$lv]["file"] = $cldat["alias_class"];
					}
					else
					{
						$by_alias[$lv]["file"] = !empty($cldat["file"]) ? $cldat["file"] : null;
					}
					$by_alias[$lv]["class_id"] = $clid;
				}
			}
		}

		// try to find aliases until we no longer find any.
		// why is this? well, to enable the user to add aliases bloody anywhere. like in files that are to be shown right away
		$i = 0;
		do
		{
			$_res = preg_match_all($tags_pattern, $source, $matches, PREG_SET_ORDER);
			if (is_array($matches))
			{
				// we gather all aliases in here, grouped by class so we gan give them to parse_alias_list()
				$toreplace = array();
				foreach ($matches as $key => $val)
				{
					$clid = $by_alias[$val[2]]["class_id"];
					// dammit, this sucks. I need some way to figure out
					// whether there is a correct idx set in the aliases, and if so
					// use that, instead of the one in the list.
					//$idx = $val[3] - 1;
					$idx = $val[3];
					$target = isset($aliases[$clid][$idx]["to"]) ? $aliases[$clid][$idx]["to"] : null;

					$toreplace[$clid][$val[0]] = isset($aliases[$clid][$idx]) ? $aliases[$clid][$idx] : null;
					$toreplace[$clid][$val[0]]["val"] = $val;
				}

				// here we do the actual parse/replace bit
				foreach($toreplace as $clid => $claliases)
				{
					$emb_obj_name = "emb" . $clid;
					$cldat = $classlist[$clid];
					$class_name = !empty($cldat["alias_class"]) ? $cldat["alias_class"] : $cldat["file"];
					$class_name_base = basename($class_name);
					if ($class_name)
					{
						// load and create the class needed for that alias type
						$$emb_obj_name = new $class_name_base();
						$$emb_obj_name->embedded = true;
					}

					// if not, then parse all the aliases one by one
					foreach($claliases as $avalue => $adata)
					{
						$replacement = "";
						if(isset($adata["target"]) && acl_base::can("view", $adata["target"]))
						{
							if (!empty($adata["val"][5]))
							{ // embedded object property accessed in document content
								$target_o = new object($adata["target"]);
								$property_name = $adata["val"][5];

								try
								{
									$replacement = $target_o->prop_str($property_name);
								}
								catch (awex_obj_prop $e)
								{
									$uid = aw_global_get("uid");
									if ($uid and ($uid === $o->createdby() or $uid === $o->modifiedby()))
									{
										$replacement = sprintf(t("[Viga: objektil %s pole omadust nimega %s]"), $target_o->name(), $property_name);
									}
								}
							}
							elseif (method_exists($$emb_obj_name, "parse_alias"))
							{ // object view embedded in document content
								$parm = array(
									"oid" => $oid,
									"matches" => $adata["val"],
									"alias" => $adata,
									"tpls" => &$args["templates"],
									"data" => isset($args["data"]) ? $args["data"] : null
								);
								$repl = $$emb_obj_name->parse_alias($parm);

								$inplace = false;
								if (is_array($repl))
								{
									$replacement = $repl["replacement"];
									$inplace = $repl["inplace"];
								}
								else
								{
									$replacement = $repl;
								}

								if ($inplace)
								{
									if (isset($this->tmp_vars[$inplace]))
									{
										$this->tmp_vars[$inplace] .= $replacement;
									}
									else
									{
										$this->tmp_vars[$inplace] = $replacement;
									}
									$replacement = "";
								}
							}
						}

						$source = str_replace($avalue, $replacement, $source);
					}
				}
			}
		}
		while ($_res and ++$i < 21);
	}

	/**  Returns an array of aw aliases that are attached to gthe given object
		@attrib api=1 params=name

		@param oid required type=int acl=view
			the object for which the aliases are returned

		@errors
			none

		@returns
			array { aliased object class_id => array { alias number => array { all connection properties + aliaslink } } }

		@examples

			$alp = get_instance("alias_parser");
			$alias_list = $alp->get_oo_aliases(array("oid" => $oid));

			foreach($alias_list as $class_id => $data)
			{
				foreach($data as $idx => $alias)
				{
					echo "object ".$alias["to"].", name ".$alias["name"]." is connected <br>";
				}
			}
	**/
	function get_oo_aliases($args = array())
	{
		extract($args);

		if (!$oid)
		{
			return array();
		}

		// lets' remove this for now. If there is a problem with alias enumeration
		// somewhere, then it should be fixed case by case basis instead of doing
		// it blindly over and over and over and over again
		//$this->recover_idx_enumeration($oid);

		$obj = obj($oid);
		$als = $obj->meta("aliaslinks");

		$ids = array();
		$cf = $obj->connections_from();
		foreach($cf as $c)
		{
			$ids[] = $c->prop("to");
		}

		// fetch objs in object_list, it's fastah
		if (count($ids))
		{
			$ol = new object_list(array("oid" => $ids));
			$ol->begin();
		}

		foreach($cf as $c)
		{
			$tp = $c->prop();
			$tp["aliaslink"] = isset($als[$c->prop("to")]) ? $als[$c->prop("to")] : null;
			$tp["source"] = $tp["from"];

			$tp["target"] = $tp["to"];
			$tp["to"] = $tp["to"];
			$tp["class_id"] = $tp["to.class_id"];
			$tp["name"] = $tp["to.name"];
			$retval[$tp["to.class_id"]][$tp["idx"]] = $tp;
		}
		return $retval;
	}

	/** returns an array of alias id => alias name (#blah666#) for the given object
		@attrib api=1 params=pos

		@param oid required type=int acl=view
			the object for which the aliases are returned

		@errors
			none

		@returns
			array { aliased object id => alias string }

		@examples

			$alp = get_instance("alias_parser");
			$alias_list = $alp->get_alias_list_for_obj_as_aliasnames($oid);

			foreach($alias_list as $obj_id => $alias_string)
			{
				echo "alias for object $obj_id is $alias_string <br>";
			}
	**/
	function get_alias_list_for_obj_as_aliasnames($oid)
	{
		$cnts = array();
		$ret = array();

		$o = obj($oid);
		foreach($o->connections_from() as $c)
		{
			list($astr) = explode(",", aw_ini_get("classes.".$c->prop("to.class_id").".alias"));
			$ret[$c->prop("to")] = "#".$astr.($c->prop("idx"))."#";
		}
		return $ret;
	}

	/** returns the variables that should be inserted into the current template and that were created by the aliases parsed. each class can return from it's parse_alias method, an array that contains the variables that get added to this list.

		@attrib api=1

		@errors
			none

		@returns
			array of variables that should be passed to $this->vars()

		@examples
			$this->read_template("plain.tpl");

			$document = object($document_id);
			$content = $document->prop("lead")."&lt;br&gt;".$document->prop("content");

			$alp = get_instance("alias_parser");
			$alp->parse_oo_aliases($document->id(), $content);

			$this->vars(array("content" => $content));
			$this->vars($alp->get_vars());

			echo $this->parse(); // displays document with aliases parsed
	**/
	function get_vars()
	{
		return (isset($this->tmp_vars) && is_array($this->tmp_vars)) ? $this->tmp_vars : array();
	}
}
