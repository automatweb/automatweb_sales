<?php

// tegeleb ORB requestide handlimisega

class orb extends aw_template //TODO: v6iks mitte ekstendida awtpl-i
{
	private $data = "";
	private $info = array();
	private $orb_defs = array();
	private $fatal = true;
	private $silent = false;
	private $_tmp;
	protected $orb_class;

	function orb($args = array())
	{
		$this->init();
		if (!empty($args["class"]))
		{
			$this->process_request($args);
		}
	}

	////
	//! Konstruktor. Koik vajalikud argumendid antakse url-is ette
	//  why the hell did I put all the functionality into the constructor?
	// now I can't put other useful functions into this class and used them
	// without calling the instructor
	public function process_request($args = array())
	{
		// peavad olema v2hemalt
		// a) class
		// b) action
		// c) vars (sisaldab vastavalt vajadusele kas $HTTP_GET_VARS-i voi $HTTP_POST_VARS-i

		// optional
		// d) silent. veateateid ei v2ljastata. caller peaks kontrollima return valuet,
		// kui see on false, siis oli viga.
		$class = $args["class"];
		if ("periods" === $class)
		{
			$class = "period";
		}

		$class = preg_replace("/[^A-z_.\-]/", "", $class);// precaution against filesystem access attempts

		// laeme selle klassi siis
		$orb_defs = $this->try_load_class($class);
		$this->orb_class = new $class();
		$class_reflection = new ReflectionClass($class);

		if (!$class_reflection->implementsInterface("orb_public_interface"))
		{
			throw new awex_orb_class("Requested class '{$class}' doesn't implement ORB interface");
		}

		$this->orb_class->set_request(automatweb::$request);
		$this->orb_defs = $orb_defs;

		// action defineeritud?
		if (!empty($args["action"]))
		{
			$action = $args["action"];
		}
		elseif (!empty($orb_defs[$class]["default"]))
		{
			$action = $orb_defs[$class]["default"];
		}
		else
		{
			throw new awex_orb_action("Action not defined");
		}

		$vars = $args["vars"];

		// main authentication call
		$this->check_login($class, $action);

		// check access for this class. access is checked by the "add tree conf" object assigned to groups
		if (!($class === "users"))//XXX:miks?
		{
			$this->check_class_access($class);
		}

		// create an array of class names that should be loaded.
		$cl2load = array();
		if (is_array($orb_defs[$class]["_extends"]))
		{
			$cl2load = array_merge($cl2load, $orb_defs[$class]["_extends"]);
		}

		$fun = isset($orb_defs[$class][$action]) ? $orb_defs[$class][$action] : NULL;
		// oh the irony
		if (!$fun && $action === "view")
		{
			$action = "change";
		}

		if (is_array($fun))
		{
			$found = true;
		}
		else
		{
			$found = false;
		}

		foreach($cl2load as $clname)
		{
			// not yet found
			if (!$found)
			{
				// only load if definitions for this class are
				// not yet loaded (master class)
				if (empty($_orb_defs[$clname]) && $clname !== "aw_template")
				{
					$_orb_defs = $this->try_load_class($clname);
				}

				$fun = isset($_orb_defs[$clname][$action]) ? $_orb_defs[$clname][$action] : false;

				// XXX: fallback to change for objects which do not have view action
				///miks?
				if ( ($action == "view") && (!is_array($fun)) )
				{
					$action = "change";
					$fun = isset($_orb_defs[$clname][$action]) ? $_orb_defs[$clname][$action] : NULL;
				}

				if (is_array($fun))
				{
					$found = true;
					// copy the function definition from the extended class to the called class
					// this way it works like real inheritance - all the other properties,
					// including which class is instantiated, come from the class that was called
					// and not the class in which the function was found
					$orb_defs[$class][$action] = $_orb_defs[$clname][$action];
				}
			}
		}

		// still not found?
		if (!$found)
		{
			throw new awex_orb_action("Class '{$class}' action '{$action}' not defined");
		}

		// check acl
		$this->do_orb_acl_checks($orb_defs[$class][$action], $vars);

		// handle reforb
		if (isset($vars["reforb"]) && $vars["reforb"] == 1)
		{
			$t = $this->orb_class;
			$fname = $fun["function"];
			if (!method_exists($t,$fname))
			{
				throw new awex_orb_method("RefORB method '{$fname}' for action '{$action}' in class '{$class}' not found.");
			}

			if (isset($orb_defs[$class][$action]["xmlrpc"]) and $orb_defs[$class][$action]["xmlrpc"] == 1)
			{
				$url = $this->do_orb_xmlrpc_call($orb_defs[$class][$action]["server"],$class,$action,$vars);
			}
			else
			{
				$t->set_opt("orb_class", $this->orb_class);

				// reforbi funktsioon peab tagastama aadressi, kuhu edasi minna
				$url = $t->$fname($vars);
			}

			// ja tagasi main programmi
			$this->data = $url;
			return;
		}

		// loome parameetrite array
		$params = array();
		// orb on defineeritud XML-i kaudu
		if (isset($orb_defs[$class][$action]["all_args"]) && $orb_defs[$class][$action]["all_args"] == true)
		{
			$required = $orb_defs[$class][$action]["required"];
			// first check, whether all required arguments are set
			$_params = $_GET;
			foreach(safe_array($vars) as $k => $v)
			{
				$_params[$k] = $v;
			}

			foreach($required as $key => $val)
			{
				if (!isset($_params[$key]))
				{
					throw new awex_orb_param("Required parameter '{$key}' for action '{$action}' in class '{$class}' not specified.");
				}
			}

			foreach($_params as $key => $val)
			{
				$this->validate_value(array(
					"type" => isset($orb_defs[$class][$action]["types"][$key]) ? $orb_defs[$class][$action]["types"][$key] : NULL,
					"name" => $key,
					"value" => $val,
				));
				$params[$key] = $val;
			}
		}
		else
		{
			if ($_SERVER["REQUEST_METHOD"] === "POST")
			{
				$params = $_POST;
			}
			// required arguments
			$required = $orb_defs[$class][$action]["required"];
			$optional = $orb_defs[$class][$action]["optional"];
			$defined = $orb_defs[$class][$action]["define"];
			$_r = new aw_array($required);
			foreach($_r->get() as $key => $val)
			{
				if (!isset($vars[$key]))
				{
					throw new awex_orb_param("Required parameter '{$key}' for action '{$action}' in class '{$class}' not given.");
				}

				$this->validate_value(array(
					"type" => isset($orb_defs[$class][$action]["types"][$key]) ? $orb_defs[$class][$action]["types"][$key] : null,
					"name" => $key,
					"value" => $vars[$key],
				));

				$params[$key] = $vars[$key];
			}

			//optional arguments
			$_o = new aw_array($optional);
			foreach($_o->get() as $key => $val)
			{
				if (!empty($vars[$key]))
				{
					$this->validate_value(array(
						"type" => isset($orb_defs[$class][$action]["types"][$key]) ? $orb_defs[$class][$action]["types"][$key] : null,
						"name" => $key,
						"value" => $vars[$key],
					));
					$params[$key] = $vars[$key];
				}
				elseif (isset($orb_defs[$class][$action]["defaults"][$key]))
				{
					if ($orb_defs[$class][$action]["defaults"][$key] === "true")
					{
						$orb_defs[$class][$action]["defaults"][$key] = true;
					}
					else
					if ($orb_defs[$class][$action]["defaults"][$key] === "false")
					{
						$orb_defs[$class][$action]["defaults"][$key] = false;
					}
					$params[$key] = $orb_defs[$class][$action]["defaults"][$key];
				}
			}
			$params = array_merge($params,$defined);
		}

		if (isset($user))
		{
			$params["user"] = 1;
		}

		// there are some variables in the url that if present should be placed in all the url-s generated by AW
		$tracked_var_names = array("cal","date","trid");
		$tracked_vars = array();
		foreach($tracked_var_names as $tvar)
		{
			$tvar_val = aw_global_get($tvar);
			if ($tvar_val)
			{
				$tracked_vars[$tvar] = $tvar_val;
			}
		}

		$GLOBALS["tracked_vars"] = $tracked_vars;

		if (!empty($orb_defs[$class][$action]["xmlrpc"]))
		{
			$content = $this->do_orb_xmlrpc_call($orb_defs[$class][$action]["server"],$class,$action,$params);
		}
		else
		{
			// ja kutsume funktsiooni v2lja
			$t = $this->orb_class;
			if (method_exists($t, "set_opt"))
			{
				$t->set_opt("orb_class", $this->orb_class);
			}

			$fname = $fun["function"];

			if (!method_exists($t,$fname))
			{
				throw new awex_orb_method("Method '{$fname}' for action '{$action}' in class '{$class}' not found.");
			}
			// this is perhaps the single most important place in the code ;)
			$content = $t->$fname($params);
		}
		$this->data = $content;

		// kui klass teeb enda sisse $info nimelise array, ja kirjutab sinna mingit teksti, siis
		// see votab nad sealt v2lja ja caller saab get_info funktsiooni kaudu k2tte kogu vajaliku info.
		// no ntx aw sees on vaja kuidagi saada string aw index.tpl-i sisse pealkirjaks
		// ilmselt see pole koige lihtsam lahendus, but hey, it works
		if (isset($t->info) && is_array($t->info))
		{
			$this->info = $t->info;
		}
	}

	function validate_value($args = array())
	{
		if ($args["type"] === "int")
		{
			// check for http: in it and don't error if it is there
			if (strpos($args["value"], "http:") !== false)
			{
				die("silly robot!");
			}

			if (strpos($args["value"], "Result") !== false)
			{
				die("Silly robot!");
			}

			if (!is_numeric($args["value"]))
			{
				throw new awex_orb_param("Parameter '{$args["name"]}' value of wrong type.");
			}
		}
	}

	function load_xml_orb_def($class)
	{
		$fname = "/xml/orb/{$class}.xml";
		cache::get_cached_file(array(
			"fname" => $fname,
			"unserializer" => array($this, "load_xml_orb_def_file"),
			"loader" => array($this, "load_serialized_orb_def"),
		));
		return $this->_tmp;
	}

	function load_serialized_orb_def($args = array())
	{
		$this->_tmp = $args["data"];
	}

	/**
		@attrib api=1 params=name
		@param content type=xml
			Class's ORB xml definition
		@comment
		@returns array
			xml parsed to array
		@errors
	**/
	function load_xml_orb_def_file($args = array())
	{
		// loome parseri
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		// xml data arraysse
		xml_parse_into_struct($parser, $args["content"], $values, $tags);
		// R.I.P. parser
		xml_parser_free($parser);

		// konteinerite tyybid
		$containers = array("class","action","function","arguments");

		// argumentide tyybid
		$argtypes = array("optional","required","define");

		// argumentide andmetyybid (int, string, whatever)
		$types = array();

		// ja siia moodustub loplik struktuur
		$orb_defs = array();

		foreach($values as $key => $val)
		{
			// parajasti t88deldava tag-i nimi
			$tag = $val["tag"];

			// on kas tyhi, "open", "close" voi "complete".
			$tagtype = $val["type"];

			// tagi parameetrid, array
			$attribs = isset($val["attributes"]) ? $val["attributes"] : array();

			// kui tegemist on n8 "konteiner" tag-iga, siis...
			if (in_array($tag,$containers))
			{
				if ((sizeof($attribs) > 0) && in_array($tagtype,array("open","complete")))
				{
					$$tag = $attribs["name"];

					if ("action" === $tag)
					{
						//XXX: DEPRECATED. use "login" attribute
						if (!empty($attribs["nologin"]))
						{
							$orb_defs[$class][$attribs["name"]]["nologin"] = 1;
						}

						if (!empty($attribs["login"]))
						{
							$orb_defs[$class][$attribs["name"]]["login"] = $attribs["login"];
						}
						else
						{
							$orb_defs[$class][$attribs["name"]]["login"] = "all";
						}

						if (!empty($attribs["is_public"]))
						{
							$orb_defs[$class][$attribs["name"]]["is_public"] = 1;
						}

						if (!empty($attribs["is_content"]))
						{
							$orb_defs[$class][$attribs["name"]]["is_content"] = 1;
						}

						if (!empty($attribs["all_args"]))
						{
							$orb_defs[$class][$attribs["name"]]["all_args"] = true;
						}

						if (!empty($attribs["caption"]))
						{
							$orb_defs[$class][$attribs["name"]]["caption"] = $attribs["caption"];
						}

						//XXX: DEPRECATED
						//TODO: klassi atribuutidesse viia
						if (!empty($attribs["default"]))
						{
							$orb_defs[$class]["default"] = $attribs["name"];
						}
					}

					if ($tag === "function")
					{
						$orb_defs[$class][$action][$tag] = $$tag;
						// initsialiseerime need arrayd
						$orb_defs[$class][$action]["required"] = array();
						$orb_defs[$class][$action]["optional"] = array();
						$orb_defs[$class][$action]["define"] = array();
						$orb_defs[$class][$action]["types"] = array();

						// default values for optional arguments
						$orb_defs[$class][$action]["defaults"] = array();

						if (!isset($attribs["xmlrpc"]) && isset($xmlrpc_defs["xmlrpc"]))
						{
							$orb_defs[$class][$action]["xmlrpc"] = $xmlrpc_defs["xmlrpc"];
						}

						if (!isset($attribs["xmlrpc"]) && isset($xmlrpc_defs["server"]))
						{
							$orb_defs[$class][$action]["server"] = $xmlrpc_defs["server"];
						}

						// default action
						if (isset($attribs["default"]) && $attribs["default"])
						{
							$orb_defs[$class]["default"] = $action;
						}
					}
					elseif ($tag === "class")
					{
						// klassi defauldid. kui funktsiooni juures pole, pannakse need
						if (isset($attribs["xmlrpc"]))
						{
							$xmlrpc_defs["xmlrpc"] = $attribs["xmlrpc"];
						}

						if (isset($attribs["server"]))
						{
							$xmlrpc_defs["server"] = $attribs["server"];
						}

						if (isset($attribs["extends"]))
						{
							$extends = explode(",",$attribs["extends"]);
							$orb_defs[$class]["_extends"] = $extends;
						}

						if (isset($attribs["folder"]))
						{
							$orb_defs[$class]["___folder"] = $attribs["folder"];
						}
					}
				}
				elseif ($tagtype === "close")
				{
					$$tag = "";
				}
			}

			// kui leidsime argumenti m22rava tag-i, siis ...
			if (in_array($tag, $argtypes))
			{
				// kontroll, just in case
				if ($tagtype === "complete")
				{
					if ($tag === "define")
					{
						$val = $attribs["value"];
					}
					else
					{
						$val = 1;
					}

					$orb_defs[$class][$action][$tag][$attribs["name"]] = $val;
					if (isset($attribs["type"]))
					{
						$orb_defs[$class][$action]["types"][$attribs["name"]] = $attribs["type"];
					}

					if(isset($attribs["class_id"]))
					{
						$orb_defs[$class][$action]["class_ids"][$attribs["name"]] = explode(",", $attribs["class_id"]);
					}

					if (isset($attribs["default"]))
					{
						$orb_defs[$class][$action]["defaults"][$attribs["name"]] = $attribs["default"];
					}

					if (isset($attribs["acl"]))
					{
						$orb_defs[$class][$action]["acl"][$attribs["name"]] = $attribs["acl"];
					}
				}
			}
		}

		return $orb_defs;
	}

	function get_data()
	{
		return $this->data;
	}

	function get_info()
	{
		return $this->info;
	}

	function do_orb_xmlrpc_call($server,$class,$action,$params)
	{
		rpc_create_struct($params);
	}

	////
	// !Checks whether a resource requires login and if so, asks for the password
	// also, remembers the requesterd url so we can redirect back there after the
	// login
	private function check_login($class, $action)
	{
		if (empty($this->orb_defs[$class][$action]["nologin"]) and !empty($this->orb_defs[$class][$action]["login"]))//nologin is DEPRECATED
		{
			switch ($this->orb_defs[$class][$action]["login"])
			{
				case "all":
					if (!aw_global_get("uid"))
					{
						$auth = new auth_config();
						print $auth->show_login();
						exit;
					}
					return;

				case "none":
					return;

				case "root":
					if ("root" !== aw_global_get("uid"))
					{
						$auth = new auth_config();
						print $auth->show_login(array("login_msg" => t("Teie kasutajal pole selle toimingu tegemiseks &otilde;igusi.")));
						exit;
					}
					return;
			}
		}
	}

	// loads orb definitions for class
	private function try_load_class($class)
	{
		$class = preg_replace("/[^A-z_.\-]/", "", $class);// extra precaution against filesystem access attempts

		if (!is_readable(AW_DIR."xml/orb/{$class}.xml") && !is_readable(aw_ini_get("site_basedir")."xml/orb/{$class}.xml"))
		{
			throw new awex_orb_class("Class '{$class}' ORB definition not found");
		}

		$ret = $this->load_xml_orb_def($class);

		if (isset($ret[$class]["_extends"]))
		{
			$extname = $ret[$class]["_extends"][0];
			$tmp = $this->load_xml_orb_def($extname);
			$ret[$class] = array_merge(safe_array(isset($tmp[$extname]) ? $tmp[$extname] : null ),safe_array($ret[$class]));
		}

		return $ret;
	}

	function do_orb_acl_checks($act, $vars)
	{
		if (isset($act["acl"]) && is_array($act["acl"]))
		{
			foreach($act["acl"] as $varname => $varacl)
			{
				if (isset($vars[$varname]))
				{
					$varvalue = (int) $vars[$varname];
					if ($varvalue)
					{
						$aclarr = explode(";", $varacl);
						foreach($aclarr as $aclid)
						{
							if (!object_loader::can($aclid, $varvalue))
							{
								$auth = new auth_config();
								echo $auth->show_login(array("login_msg" => t("Teil puudub $aclid-&otilde;igus objektile id-ga {$varvalue}!")));
								exit;
							}

							if(isset($act["class_ids"][$varname]) && is_array($act["class_ids"][$varname]))
							{
								$true = false;
								$obj = obj($varvalue);
								foreach($act["class_ids"][$varname] as $val)
								{
									if($obj->class_id() == constant($val))
									{
										$true = true;
										break;
									}
								}

								if(!$true)
								{
									error::raise(array(
										"id" => "ERR_ORB_WRONG_CLASS",
										"msg" => $vars["class"]."::".$vars["action"].": class id of argument ".$varname." is not ".implode(" or ", $act["class_ids"][$varname]),
									));
								}
							}
						}
					}
				}
			}
		}
	}

	////
	// !executes an orb function call and returns the data that the function returns
	// params:
	// required:
	//	action - orb action to exec
	// optional
	//  class - class for the action - default the current class
	//  params - params to the action
	//  method - the method to use when doing the function call - possible values: local / xmlrpc / (soap - not implemented yet)
	//  server - if doing a rpc call, the server where to connect
	//  login_obj - if we must log in to a serverm the id of the CL_AW_LOGIN that will be used to login to the server
	//              if this is set, then server will be ignored
	function do_method_call($arr)
	{
		extract($arr);
		$params = isset($arr["params"]) ? $arr["params"] : array();

		$this->fatal = true;
		$this->silent = false;

		if (!isset($class))
		{
			throw new awex_orb_class("No class specified");
		}

		if (!isset($action))
		{
			$this->raise_error("ERR_ORB_AUNDEF",E_ORB_ACTION_UNDEF,true,$this->silent);
		}

		// get orb defs for the class

		// check params
		if (!isset($method) || (isset($method) && ($method === "local")))
		{
			$orb_defs = $this->try_load_class($class);
			$params = $this->check_method_params($orb_defs, $params, $class, $action);
			$arr["params"] = $params;
			$this->do_orb_acl_checks($orb_defs[$class][$action], $params);
		}


		// do the call
		if (!isset($method) || (isset($method) && ($method === "local")))
		{
			// local call
			$___folder = isset($orb_defs[$class]["___folder"]) ? $orb_defs[$class]["___folder"] : NULL;
			$data = $this->do_local_call($orb_defs[$class][$action]["function"], $class, $params, $___folder);
		}
		else
		{
			// log in if necessary or get the existing session for rpc call
			list($arr["remote_host"], $arr["remote_session"]) = $this->get_remote_session($arr);

			// load rpc handler
			$inst = get_instance("core/orb/".$method);
			if (!is_object($inst))
			{
				$this->raise_error("ERR_ORB_RPC_NO_HANDLER",sprintf(t("Could not load request handler for request method '%s'"), $method), true,$this->silent);
			}
			// send the remote request and read the result
			$data = $inst->do_request($arr);
		}

		return $data;
	}

	////
	// !checks the parameters $params for action $action, defined in $defs and returns the matching parameters
	function check_method_params($orb_defs, $params, $class, $action)
	{
		$ret = array();
		if (isset($orb_defs[$class][$action]["all_args"]) && $orb_defs[$class][$action]["all_args"] == true)
		{
			return $params;
		}
		else
		{
			// required arguments
			$required = $orb_defs[$class][$action]["required"];
			$optional = $orb_defs[$class][$action]["optional"];
			$defined = $orb_defs[$class][$action]["define"];
			if (is_array($required))
			{
				foreach($required as $key => $val)
				{
					if (!isset($params[$key]))
					{
						$this->raise_error("ERR_ORB_CPARM",sprintf(E_ORB_CLASS_PARM,$key,$action,$class),true,$this->silent);
					}

					$vartype = $orb_defs[$class][$action]["types"][$key];
					if ($vartype === "int")
					{
						if (!is_numeric($params[$key]))
						{
							$this->raise_error("ERR_ORB_NINT",sprintf(E_ORB_NOT_INTEGER,$key),true,$this->silent);
						}
					}
					$ret[$key] = $params[$key];
				}
			}

			//optional arguments
			if (is_array($optional))
			{
				foreach($optional as $key => $val)
				{
					$vartype = $orb_defs[$class][$action]["types"][$key];
					if (!empty($params[$key]))
					{
						if ( ($vartype === "int") && ($params[$key] != sprintf("%d",$vars[$key])) )
						{
							$this->raise_error("ERR_ORB_NINT",sprintf(E_ORB_NOT_INTEGER,$key),true,$this->silent);
						}
						$ret[$key] = $params[$key];
					}
					else
					// note, there seems to be some bitrot here, isset breaks things

					if (!empty($orb_defs[$class][$action]["defaults"][$key]))
					{
						$ret[$key] = $orb_defs[$class][$action]["defaults"][$key];
					}
				};
			}

			if (is_array($defined))
			{
				$ret += $defined;
			}
		}
		return $ret;
	}

	function do_local_call($func, $class, $params, $folder)
	{
		if ($folder != "")
		{
			$folder.="/";
		}
		$inst = get_instance($folder.$class);
		if (is_object($inst))
		{
			if (method_exists($inst, $func))
			{
				return $inst->$func($params);
			}
			else
			{
				$this->raise_error("ERR_ORB_MNOTFOUND",sprintf(E_ORB_METHOD_NOT_FOUND,$func,$class),true,$this->silent);
			}
		}
		else
		{
			$this->raise_error("ERR_ORB_NOCLASS",E_ORB_CLASS_UNDEF,true,$this->silent);
		}
	}

	////
	// !returns the session id for the rpc call
	// params:
	// either login_obj or server must be specified
	// login_obj - the oid of the CL_AW_LOGIN object - the server is read from that
	// server - the server to use (no login)
	function get_remote_session($arr)
	{
		extract($arr);
		if (!empty($login_obj))
		{
			$login = new http();
			list($server, $cookie) = $login->login_from_obj($login_obj);
			$this->rpc_session_cookies[$server] = $cookie;
		}
		else
		{
			if (empty($server))
			{
				$this->raise_error("ERR_ORB_RPC_NO_SERVER", "No server defined for ORB RPC call!", true, false);
			}

			$login = new http();
			$this->rpc_session_cookies[$server] = $login->handshake(array(
				"silent" => true,
				"host" => $server
			));
		}
		return array($server,$this->rpc_session_cookies[$server]);
	}

	////
	// !handles a rpc call - ie decodes the request and calls the right function, encodes returned data and returns the encoded data
	// params:
	//	method - request method, currently only xmlrpc is supported
	public function handle_rpc_call($arr)
	{
		extract($arr);

		// now, catch all output
		ob_start();

		// load rpc handler
		$inst = new $method();
		if (!is_object($inst))
		{
			$this->raise_error("ERR_ORB_RPC_NO_HANDLER",sprintf(t("orb::handle_rpc_call - Could not load request handler for request method '%s'"), $method), true,false);
		}

		// decode request
		$request = $inst->decode_request();

		if (empty($request["class"]))
		{
			$inst->handle_error(1, "No class given");
		}

		// do the method calling thing
		$orb_defs = $this->try_load_class($request["class"]);

		$params = $this->check_method_params($orb_defs, $request["params"], $request["class"], $request["action"]);

		if (!isset($orb_defs[$request["class"]][$request["action"]]))
		{
			$this->raise_error("ERR_ORB_MNOTFOUND",sprintf("No action with name %s defined in class %s! Malformed XML?",$request["action"],$request["class"]),true,$this->silent);
		}

		$ret = $this->do_local_call($orb_defs[$request["class"]][$request["action"]]["function"], $request["class"], $params,$orb_defs[$request["class"]]["___folder"]);


		$output = (string) ob_get_contents();
		ob_end_clean();

		if (strlen($output))
		{
			$output = htmlentities($output);
			$inst->handle_error(2, "Output generated during RPC call! content: '{$output}'");
		}

		return $inst->encode_return_data($ret);
	}

	////
	// !Returns a list of all defined ORB classes
	// interface(string) - name of the interface file
	function get_classes_by_interface($args = array())
	{
		if (empty($args["interface"]))
		{
			// wuh los, man?
			return false;
		}

		switch($args["interface"])
		{
			case "content":
				$ifile = "content.xml";
				$flag = "is_content";
				break;

			case "interface":
			default:
				$ifile = "public.xml";
				$flag = "is_public";
		};

		// klassi definitsioon sisse
		$xmldef = $this->get_file(array(
			"file" => aw_ini_get("basedir") . "xml/interfaces/$ifile"
		));

		// loome parseri
		$parser = xml_parser_create();
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
		// xml data arraysse
		// XXX: I need some kind of error checking here!! -- duke
		xml_parse_into_struct($parser, $xmldef, $values, $tags);
		// R.I.P. parser
		xml_parser_free($parser);

		$pclasses = array();

		foreach($values as $key => $val)
		{
			$attr = isset($val["attributes"]) ? $val["attributes"] : array();
			if ( ($val["tag"] == "class") && ($val["type"] == "complete") && $attr['id'] != '')
			{
				$pm = $this->get_methods_by_flag(array(
					"flag" => $flag,
					"id" => $attr["id"],
					"name" => $attr["name"],
				));

				if (sizeof($pm)  > 0)
				{
					$pclasses = $pclasses + $pm;
				}
			}
		}

		return $pclasses;

	}

	////
	// !Returns a list of methods inside a class matching a flag
	// added a param: if no_id is set to true, then it will not added that ugly class id to key -- ahz
	function get_methods_by_flag($args = array())
	{
		extract($args);
		$orbclass = get_instance("core/orb/orb");//XXX: milleks iseenda instance?
		$orb_defs = $orbclass->load_xml_orb_def($id);
		$methods = array();
		foreach(safe_array($orb_defs[$id]) as $key => $val)
		{
			if (is_array($val) && isset($val[$flag]))
			{
				$caption = isset($val["caption"]) ? $val["caption"] : $val["function"];
				$methods[(empty($no_id) ? "{$id}/" : "") . $key] = $name . " / " . $caption;
			}
		}

		return $methods;
	}

	/** Returns a list of all actions defined for a class
		@attrib api=1 params=name

		@param class required type=string
			The class to return actions for
	**/
	function get_class_actions($arr)
	{
		$methods = array();
		$cur_class = $arr["class"];

		do
		{
			$orb_defs = $this->load_xml_orb_def($cur_class);

			if (isset($orb_defs[$cur_class]))
			{
				foreach(safe_array($orb_defs[$cur_class]) as $key => $val)
				{
					if ($key === "_extends" || $key === "___folder")
					{
						continue;
					}
					$methods[$key] = $val["function"];
				}
			}
		}
		while(isset($orb_defs[$cur_class]["_extends"][0]) and ($cur_class = $orb_defs[$cur_class]["_extends"][0]));

		return $methods;
	}

	function get_public_method($args = array())
	{
		extract($args);
		$orbclass = new orb();
		$orb_defs = $orbclass->load_xml_orb_def($id);

		if ($action === "default")
		{
			$action = $orb_defs[$id]["default"];
		}

		$meth = $orb_defs[$id][$action];
		$meth["values"] = array();

		if ($orb_defs[$id]["___folder"] != "")
		{
			$fld = $orb_defs[$id]["___folder"]."/";
		}
		$cl = get_instance($fld.$id);
		$ar = array();
		if ($id === "document")
		{
			if ($cl->get_opt("cnt_documents") == 1)
			{
				$meth["values"]["id"] = $cl->get_opt("shown_document");
			}
			$meth["values"]["period"] = aw_global_get("act_per_id");
			//$data = $cl->get_opt("data");
			$meth["values"]["parent"] = $cl->get_opt("parent");
			if ($action === "change" && $cl->get_opt("shown_document"))
			{
				$meth["values"]["id"] = $cl->get_opt("shown_document");
			}

			if ($action === "new")
			{
				if ($this->can("view", aw_global_get("section")))
				{
					$tmp = obj(aw_global_get("section"));
					if ($tmp->class_id() == CL_DOCUMENT)
					{
						$tmp = obj($tmp->parent());
					}
					$meth["values"]["parent"] = $tmp->id();
				}
			}
		}

		if ($id === "doc")
		{
			$cl = get_instance("document");
			if ($cl->get_opt("cnt_documents") == 1)
			{
				$meth["values"]["id"] = $cl->get_opt("shown_document");
			}

			$meth["values"]["period"] = aw_global_get("act_per_id");
			if ($action === "change" && $cl->get_opt("shown_document"))
			{
				$meth["values"]["id"] = $cl->get_opt("shown_document");
			}

			if ($action === "new")
			{
				$meth["values"]["parent"] = aw_global_get("section");
			}
		}

		if ($id === "menu")
		{
			if ($this->can("view", aw_global_get("section")))
			{
				$so = obj(aw_global_get("section"));
				if ($so->class_id() != CL_MENU)
				{
					$so = obj($so->parent());
				}
				$meth["values"]["parent"] = $so->id();
				$meth["values"]["id"] = $so->id();
			}
		}
		elseif ($id === "file")
		{
			$meth["values"]["parent"] = aw_global_get("section");
			$meth["values"]["id"] = aw_global_get("section");
		}

		if($id === "method")
		{
			if($obj)
			{
				$meth["values"]["mid"] = $obj;
			}
		}
		return $meth;
	}

	static function check_class_access($class)
	{
		if (
			"root" !== aw_global_get("uid") and
			aw_ini_get("acl.check_prog") and
			(
				($conf = add_tree_conf_obj::get_active_configuration()) and !add_tree_conf::can_access_class($conf, $class) or // main class access check
				"restrictive" === aw_ini_get("acl.policy")  // if atc not found, policy determines access
			)
		)
		{
			$auth = new auth_config();
			print $auth->show_login(array("login_msg" => t("Teie kasutajal pole selle klassi kasutamiseks &otilde;igusi.")));
			exit;
		}
	}

	/** Creates orb links
		@attrib api=1 params=pos
		@param action type=string
		@param class type=int|string
		@param o type=int|object|aw_oid default=0
		@param args type=array default=array()
			Additional arguments
		@param sep type=string default='&'
			Argument separator
		@returns
		@errors
			throws awex_orb_class if no valid class given
		@comment
	**/
	public static function create_request_url($class, $action, $o = 0, $args = array(), $sep = "&")
	{//TODO: pooleli
		// resolve to name
		if (is_numeric($class))
		{
			try
			{
				$class = basename(aw_ini_get("classes.{$class}.file"));
			}
			catch (Exception $e)
			{
				throw new awex_orb_class("Invalid class id '{$class}'");
			}
		}
		elseif (!aw_ini_isset("class_lut.{$class}"))
		{
			throw new awex_orb_class("Invalid class name '{$class}'");
		}

		// tracked_vars comes from orb->process_request
		if (isset($GLOBALS["tracked_vars"]) and is_array($GLOBALS["tracked_vars"]))
		{
			$args = $args + $GLOBALS["tracked_vars"];
		}

		$args["class"] = $class;
		$args["action"] = $action;

		// figure out the request method once.
		static $r_use_orb;
		if (!isset($r_use_orb))
		{
			$r_use_orb = basename($_SERVER["SCRIPT_NAME"], AW_FILE_EXT) === "orb";
		}

		if (!$honor_r_orb)
		{
			$r_use_orb = false;
		}

		$in_admin = isset($GLOBALS["cfg"]["in_admin"]) ? (bool) $GLOBALS["cfg"]["in_admin"] : false;

		$ru = null;
		if (isset($args["return_url"]))
		{
			$ru = $args["return_url"];
			unset($args["return_url"]);
		}

		$args = self::encode_request_args($args, "");
		$res = aw_ini_get("baseurl");
		if ($force_admin || $in_admin)
		{
			$res .= "automatweb/";
			$use_orb = true;
		}

		if ($use_orb || $r_use_orb)
		{
			$res .= "orb" . AW_FILE_EXT;
		}

		$res .= ($sep === "/") ? "/" : "?";
		foreach($this->orb_values as $name => $value)
		{
			// lets skip the parameter only when it is empty string --dragut
			if ($value !== '')
			{
				$add = $name."=".$value.$sep;
				if(strlen($res.$add) > 2047)
				{
					$add = substr($add, 0, 2000);
				}
				$res .= $add;
			}
		}

		if ($ru !== null)
		{
			$rv = $res."return_url=".urlencode($ru).$sep;
		}
		else
		{
			$rv = substr($res,0,-strlen($sep));
		}

		$len = strlen($rv);
		if ($len > 2047)
		{
			$rv = substr($rv, 0, 2047);
		}
		return $rv;
	}

	private static function encode_request_args($args, $prefix, $no_urlencoding = false, $use_empty = false)
	{
		foreach($args as $name => $value)
		{
			if (is_array($value))
			{
				$_tpref = "" == $prefix ? $name : "[".$name."]";
				$args[$name] = self::encode_request_args($args[$name], $prefix.$_tpref, $no_urlencoding, $use_empty);
			}
			else
			{
				// commented this out, because it breaks stuff - namely, urls that are created via
				// $this->mk_orb("admin_cell", array("id" => $this->id, "col" => (int)$args["r_col"], "row" => (int)$args["r_row"]))
				// where the col and row parameters will be "0"
				// it will not include them.. damned if I know why
				// so, before putting this back, check that
				// - terryf

				// 0 will get included now, "" will not. reforb sets use_empty so
				// that gets everything
				if ((isset($value) && ($value !== "")) || $use_empty)
				//{
					if (!$no_urlencoding)
					{
						$value = urlencode($value);
					}
					$args[empty($prefix) ? $name : $prefix."[".$name."]"] = $value;
				//};
			}
		}

		return $args;
	}
}

/** Generic ORB exception **/
class awex_orb extends aw_exception {}

/** ORB class related exception **/
class awex_orb_class extends awex_orb {}

/** ORB method related exception **/
class awex_orb_method extends awex_orb {}

/** ORB action related exception **/
class awex_orb_action extends awex_orb {}

/** ORB parameter related exception **/
class awex_orb_param extends awex_orb {}

