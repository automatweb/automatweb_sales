<?php
// htmlclient - generates HTML for configuration forms

// The idea is that if we want to implement other interfaces
// for editing objects, then we can just add other clients
// (xmlrpc, rdf, tty, etc) which take care of converting the data
// from the cfgmanager to the required form.

class htmlclient extends aw_template
{
	var $embedded;
	var $tabpanel;
	var $error = "";
	var $view_mode;
	var $style;

	public $object_id = 0;
	public $use_group = "";
	public $class_name = "";
	public $view_layout = "";

	private $has_property_help = false;
	private $ui_messages = array();

	function htmlclient($arr = array())
	{
		if(!empty($arr["no_form"]))
		{
			$this->no_form = true;
		}

		$tpldir = empty($arr["tpldir"]) ? "htmlclient" : $arr["tpldir"];
		$this->init(array("tpldir" => $tpldir));
		$this->res = "";
		$this->layout_mode = "default";
		$this->use_group = automatweb::$request->arg("group");
		$this->object_id = (int) automatweb::$request->arg("id");
		$this->class_name = automatweb::$request->class_name();
		$this->form_target = "";
		$this->tpl_vars = !empty($arr["tpl_vars"]) ? $arr["tpl_vars"] : array();
		$this->styles = !empty($arr["styles"]) ? $arr["styles"] : array();
		$this->tabs = (isset($arr["tabs"]) && $arr["tabs"] === false) ? false : true;

		if (!empty($arr["embedded"]))
		{
			$this->embedded = true;
		}

		if (!empty($arr["layout_mode"]))
		{
			$this->set_layout_mode($arr["layout_mode"]);
		}

		$this->form_layout = "";

		if (!empty($arr["template"]))
		{
			// apparently some places try to specify a template without an extension,
			// deal with it
			if (strpos($arr["template"],".tpl"))
			{
				$this->use_template = $arr["template"];
			}
			else
			{
				$this->use_template = $arr["template"] . ".tpl";
			}
		}

		$this->group_style = "";
		$this->layoutinfo = array();
		$this->start_output();

		if ($this->tabs)
		{
			$this->tp = new tabpanel();
		}
	}

	function set_layout($arr)
	{
		$this->layoutinfo = $arr;
	}

	/** this can be used to set the different aspects of the behaviour of the client.
	**/
	function configure($arr)
	{
		// help_url - set to the thing that should give you more information about the place you are in
		// .. i need a few strings too, like "help", "close", "more" .. and also inline help about the group
		// but that should probably be somewhere in the finish_output
		$this->config = $arr;
	}

	function set_form_layout($l)
	{
		$this->form_layout = $l;
	}

	function set_form_target($target = "_top")
	{
		$this->form_target = $target;
		if (!empty($this->form_target))
		{
			if($this->tplmode === "group")
			{
				$this->sub_tpl->vars(array(
					"form_target" => "target='" . $this->form_target . "' ",
				));
			}
			else
			{
				$this->vars(array(
					"form_target" => "target='" . $this->form_target . "' ",
				));
			}
		};
	}

	function add_content_element($location,$content)
	{
		$this->additional_content[$location] = $content;
	}

	function set_layout_mode($mode)
	{
		$this->layout_mode = $mode;
	}

	function set_group_style($styl)
	{
		$this->group_style = $styl;
		$this->tmp = new htmlclient(array("tpldir" => "htmlclient"));
		$this->tmp->read_template($styl . ".tpl");
	}

	// I need to remap all properties, no?

	// so, how do I add a header and a footer?

	// do I make the tabpanel a separate element? and then make it possible to add elements outside
	// the current panel?

	////
	// !Starts the output
	public function start_output($args = array())
	{
		$this->set_parse_method("eval");
		$tpl = "default.tpl";

		if (!empty($this->use_template))
		{
			$tpl = $this->use_template;
		}

		$this->read_template($tpl);
		lc_site_load("htmlclient", $this);

		if(!empty($this->tpl_vars))
		{
			$this->vars($this->tpl_vars);
		}

		$script = aw_global_get("SCRIPT_NAME");

		// siia vaja kirjutada see embedded case
		if (empty($args["handler"]))
		{
			$handler = empty($script) ? "index" : "orb";
		}
		else
		{
			$handler = $args["handler"];
		}

		if ($this->embedded)
		{
			$handler = "index";
		}

		$this->vars(array(
			"handler" => $handler,
		));
		$this->orb_vars = array();
		$this->submit_done = false;
		$this->proplist = array();

		// I need some handler code in the output form, if we have any RTE-s
		$this->rte = false;
	}

	function set_handler($handler)
	{
		$this->vars(array(
			"handler" => $handler,
		));
	}

	public function add_property($args = array())
	{
		$args["name"] = isset($args["name"]) ? $args["name"] : "";
		$args["type"] = isset($args["type"]) ? $args["type"] : "";
		// if value is array, then try to interpret
		// it as a list of elements.

		// the (possibly bad) side effect is that the first added panel will be used
		// as the main tab panel. OTOH, the first should always be the one that
		// is added by class_base, so we should be covered
		if ($args["type"] === "tabpanel" && !is_object($this->tabpanel))
		{
			$this->tabpanel = $args["vcl_inst"];
			return false;
		};

		// but actually, settings parets should take place in class_base itself
		if (isset($args["items"]) && is_array($args["items"]))
		{
			$res = "";
			foreach($args["items"] as $el)
			{
	 			$this->mod_property($el);
				$res .= $this->put_subitem($el);
			};
			$args["value"] = $res;
			$args["type"] = "text";
		}
		else
		{
			$this->mod_property($args);
		};

		$type = isset($args["type"]) ? $args["type"] : "";
		if ($type === "hidden")
		{
			$this->orb_vars[$args["name"]] = isset($args["value"]) ? $args["value"] : null;
		}

		if (!empty($args["layout"]))
		{
			$lf = $this->layoutinfo[$args["layout"]];
		}
		else
		{
			$lf = array();
		};

		if (!empty($args["parent"]) && !empty($this->layoutinfo[$args["parent"]]))
		{
			//$this->layoutinfo[$args["parent"]]["items"][] = $args["html"];
			$this->proplist[$args["name"]] = $args;

		}
		// now I have to check whether this property is placed in a grid
		// if so, place this thing int he grid
		elseif (!empty($args["layout"]) &&
			!empty($this->layoutinfo[$args["layout"]]) &&
			$this->layoutinfo[$args["layout"]]["type"] === "grid")
		{
			// now for starters lets assume that this grid thingie uses autoflow, I'll implement
			// other things later on. properties come in and will be placed in the correct places
			// in that grid
			$lf = $this->layoutinfo[$args["layout"]];
			$size = $lf["cols"] * $lf["rows"];
			if (sizeof($lf["items"]) < $size)
			{
				// temporary solution to deal with colspans
				$this->layoutinfo[$args["layout"]]["items"][] = $args;
				// but I also need to know how to add the fukken spans!

				// so what happens if a element is put in a cell .. now, I do not want to think
				// about that right now

				// colspan 2, rowspan 2 .. yees?
			}
			else
			{
				$this->proplist[$args["name"]] = $args;
			};
			//$this->proplist[$args["name"]] = $args;

		}
		else
		{
			$this->proplist[$args["name"]] = $args;
		};
	}

	////
	// !Shows an error indicator in the form
	///DEPRECATED
	function show_error() { $this->push_msg(t("Viga sisendandmetes"), "ERROR"); }


	/** Shows a general message text for whole view
		@attrib api=1 params=pos
		@param text type=string
		@param class type=string default=""
			Message class. Possible values:
			"OK" -- a positive/success message.
			"ERROR" -- an error/failure message.
			"" -- a neutral/informative message
		@comment
			Messages get shown if they are pushed before calling htmlclient::get_result()
	**/
	public function push_msg($text, $class = "", $safe = false)
	{
		if (is_string($text) and strlen($text) > 1 and ("" === $class or "OK" === $class or "ERROR" === $class))
		{
			$this->vars(array(
				"text" => $safe ? htmlspecialchars(aw_html_entity_decode($text)) : aw_html_entity_decode($text),
				"class" => $class
			));
			$this->ui_messages[] = $this->parse("MESSAGE");
		}
	}

	function mod_property(&$args)
	{
		// that too should not be here. It only forms 2 radiobuttons ...
		// which could as well be done some place else

		// of course this should be here, where the hell else do you
		// want it to be?
		if (empty($args["type"]))
		{
			return false;
		}

		$val = "";
		if ($args["type"] === "status")
		{
			$args["type"] = "chooser";
			$args["options"] = array(
				STAT_ACTIVE => t("Jah"),
				STAT_NOTACTIVE => t("Ei"),
			);
		}

		if (empty($args["value"]) && isset($args["vcl_inst"]) && is_callable(array($args["vcl_inst"], "get_html")))
		{
			$args["value"] = $args["vcl_inst"]->get_html(isset($args["parent"]) and !empty($this->layoutinfo[$args["parent"]]["closeable"]));
		}

		if($args["type"] === "reset" || $args["type"] === "button")
		{
			//$args["no_caption"] = 1;
			$args["value"] = $args["caption"];
			//$args["caption"] = "&nbsp;";
			unset($args["caption"]);
		}

		if ($args["type"] === "s_status")
		{
			if (empty($args["value"]))
			{
				// default to deactive
				$args["value"] = STAT_NOTACTIVE;
			};
			$args["type"] = "chooser";
			// hm, do we need STAT_ANY? or should I just fix the search
			// do not use dumb value like 3 -- duke
			$args["options"] = array(
				3 => t("K&otilde;ik"),
				STAT_ACTIVE => t("Aktiivne"),
				STAT_NOTACTIVE => t("Mitteaktiivne"),
			);
		}

		if ($args["type"] === "colorpicker")
		{
			$val .= html::textbox(array(
					"name" => $args["name"],
					"size" => 7,
					"maxlength" => 7,
					"value" => isset($args["value"]) ? $args["value"] : "",
			));

			$cplink = $this->mk_my_orb("colorpicker",array(),"css");

			static $colorpicker_script_done = 0;

			$script = "";
			if (!$colorpicker_script_done)
			{
				$script .= "<script type='text/javascript'>\n".
						"var element = 0;\n".
						"function set_color(clr) {\n".
						"document.getElementById(element).value=clr;\n".
						"}\n".
						"function colorpicker(el) {\n".
						"element = el;\n".
						"aken=window.open('$cplink','colorpickerw','height=220,width=310');\n".
						"aken.focus();\n".
						"};\n".
						"</script>";
				$colorpicker_script_done = 1;
			}

			$tx = "<a href=\"javascript:colorpicker('$args[name]')\">".t("Vali")."</a>";

			$val .= html::text(array("value" => $script . $tx));
			$args["value"] = $val;
		}

		if ($args["type"] === "submit")
		{
			if (empty($args["value"]))
			{
				$args["value"] = ifset($args, "caption");
			}
			unset($args["caption"]);
		}

		if ($args["type"] === "container")
		{
			$args["type"] = "text";
		}
	}

	////
	// !Creates a normal line
	function put_line($args)
	{
		$rv = "";
		$caption = isset($args["caption"]) ? $args["caption"] : "";
		unset($args["caption"]);

		if ($caption == "")
		{
			$caption = " ";
		}

		$tpl_vars = array(
			"caption" => $caption,
			"comment" => $this->get_property_help($args),
			"element" => $this->draw_element($args),
			"webform_caption" => !empty($args["style"]["caption"]) ? "st".$args["style"]["caption"] : "",
			"webform_comment" => !empty($args["style"]["comment"]) ? "st".$args["style"]["comment"] : "",
			"webform_element" => !empty($args["style"]["prop"]) ? "st".$args["style"]["prop"] : "",
		);
		$add = "";
		$add2 = "";
		if(!empty($args["capt_ord"]))
		{
			$add = strtoupper("_".$args["capt_ord"]);
		}

		$datesub = 0;
		if($args["type"] === "date_select")
		{
			if(isset($this->tmp) && is_object($this->tmp))
			{
				if($this->tmp->is_template("DATE_LINE".$add))
				{
					$datesub = 1;
				}
			}
			else
			{
				if($this->is_template("DATE_LINE".$add))
				{
					$datesub = 1;
				}
			}
		}
		$tpl_vars["element_name"] = $args["name"];

		if($datesub)
		{
			$add2 = "DATE_";
		}
		// I wanda mis kammi ma selle tmp-iga tegin
		// different layout mode eh? well, it sucks!

		if (isset($this->tmp) && is_object($this->tmp))
		{
			$this->tmp->vars($tpl_vars);
			$rv = $this->tmp->parse($add2."LINE".$add);
		}
		else
		{
			$this->vars_safe($tpl_vars);
			$rv = $this->parse($add2."LINE".$add);
		}
		return $rv;
	}

	private function _do_cfg_edit_mode_check($arr)
	{
		if (!isset($_SESSION["cfg_admin_mode"]) || !$_SESSION["cfg_admin_mode"] == 1 || !is_oid($this->object_id))
		{
			return "";
		}

		static $cur_cfgform;
		static $cur_cfgform_found = false;
		$o = obj($this->object_id);

		if (!$cur_cfgform_found)
		{
			$cur_cfgfor_found = true;
			$i = new class_base();
			$i->clid = $o->class_id();
			$cur_cfgform = $i->get_cfgform_for_object(array(
				"args" => automatweb::$request->get_args(),
				"obj_inst" => $o,
				"ignore_cfg_admin_mode" => 1
			));
		}

		$green = " <a href='javascript:void(0)' onclick='cfEditClick(\"".$arr["name"]."\", ".$this->object_id.");'><img src='".aw_ini_get("icons.server")."cfg_edit_green.png' id='cfgEditProp".$arr["name"]."'/></a>";
		$red = " <a href='javascript:void(0)' onclick='cfEditClick(\"".$arr["name"]."\", ".$this->object_id.");'><img src='".aw_ini_get("icons.server")."cfg_edit_red.png' id='cfgEditProp".$arr["name"]."'/></a>";

		$time_active_icon = " <a href='javascript:void(0)' onclick='cfEditClickTime(\"".$arr["name"]."\", ".$this->object_id.");'><img src='".aw_ini_get("icons.server")."cfg_timer_active.png' id='cfgEditPropTime".$arr["name"]."'/></a>";
		$time_inactive_icon = " <a href='javascript:void(0)' onclick='cfEditClickTime(\"".$arr["name"]."\", ".$this->object_id.");'><img src='".aw_ini_get("icons.server")."cfg_timer_inactive.png' id='cfgEditPropTime".$arr["name"]."'/></a>";

		// get default cfgform for this object and get property status from that
		$cf = $cur_cfgform;
		$retval = $green;
		if ($this->can("view", $cf))
		{
			$cfo = obj($cf);
			if (!$cfo->instance()->is_active_property($cfo, $arr["name"]))
			{
				return $red;
			}
		}

		return $retval;
	}

	////
	// !Creates a submit button
	function put_submit($arr)
	{
		$name = "SUBMIT";
		$tpl_vars = array(
			"sbt_caption" => $arr["value"] ? $arr["value"] : t("Salvesta"),
			"name" => $arr["name"] ? $arr["name"] : "",
			"comment" => ( !empty($arr["comment"]) ) ? $arr["comment"] : "",
			"action" => isset($arr["action"]) ? $arr["action"] : "",
			"webform_element" => !empty($arr["style"]["prop"]) ? "st".$arr["style"]["prop"] : "",
			"webform_caption" => !empty($arr["style"]["prop"]) ? "st".$arr["style"]["prop"] : "",
			"webform_comment" => !empty($arr["style"]["prop"]) ? "st".$arr["style"]["prop"] : "",
		);

		if(isset($arr["capt_ord"]) && $arr["capt_ord"] === "right")
		{
			 $name .= strtoupper("_".$arr["capt_ord"]);
		}
		$this->vars_safe($tpl_vars);
		$rv = $this->parse($name);
		return $rv;
	}

	function put_subitem($args)
	{
		$tpl_vars = array(
			"err_msg" => isset($args["error"]) ? $args["error"] : "",
			"caption" => isset($args["caption"]) ? $args["caption"] : "",
			"element" => $this->draw_element($args),
			"space" => isset($args["space"]) ? $args["space"] : 0,
		);
		// SUBITEM - element first, caption right next to it
		// SUBITEM2 - caption first, element right next to it
		$tpl = $args["type"] == "checkbox" ? "SUBITEM" : "SUBITEM2";
		$this->vars_safe($tpl_vars);
		$rv = $this->parse($tpl);
		return $rv;
	}

	function put_header($args)
	{
		$name = "HEADER";
		$tpl_vars = array(
			"caption" => $args["caption"],
			"webform_header" => !empty($args["style"]["prop"]) ? "st".$args["style"]["prop"] : "",
		);
		$this->vars_safe($tpl_vars);
		$rv = $this->parse($name);
		return $rv;
	}

	function put_header_subtitle($args)
	{
		$name = "SUB_TITLE";
		$tpl_vars = array(
			"value" => (!empty($args["value"]) ? $args["value"] : $args["caption"]).$this->_do_cfg_edit_mode_check($args),
			"webform_subtitle" => !empty($args["style"]["prop"]) ? "st".$args["style"]["prop"] : "",
			"st_id" => $args["name"]
		);
		$this->vars_safe($tpl_vars);
		$rv = $this->parse($name);
		return $rv;
	}

	function put_content($args)
	{
		$tpl_vars = array(
			//"value" => $args["value"],
			"cell_id" => isset($args["name"]) ? $args['name']."_cell" : "_cell",
			"value" => $this->draw_element($args),
			"webform_content" => !empty($args["style"]["prop"]) ? "st".$args["style"]["prop"] : "",
		);
		$this->vars_safe($tpl_vars);
		$rv = $this->parse("CONTENT");
		return $rv;
	}

	////
	// !Finished the output
	function finish_output($arr = array())
	{
		extract($arr);
		$sbt = $res = "";
		$data = (isset($arr["data"]) and is_array($arr["data"])) ? $arr["data"] : array();
		$action = isset($arr["action"]) ? $arr["action"] : "";

		$orb_class = empty($data["orb_class"]) ? "cfgmanager" : $data["orb_class"];
		unset($data["orb_class"]);
		$data = $data + $this->orb_vars;

		$vars = array();
		$this->layout_by_parent = array();
		$this->lp_chain = array();

		foreach($this->layoutinfo as $key => $val)
		{
			$lparent = isset($val["parent"]) ? $val["parent"] : "_main";
			$val["name"] = $key;
			$this->layout_by_parent[$lparent][$key] = $val;
			$this->lp_chain[$key] = $lparent;
			// mul on iga layoudi kohta vaja teada tema k&otilde;ige esimest layouti
		}

		$this->properties_by_parent = array();

		if (sizeof($this->proplist) > 0)
		{
			foreach($this->proplist as $ki => $item)
			{
				$pp = isset($item["parent"]) ? $item["parent"] : "_main";
				$this->properties_by_parent[$pp][$ki] = $ki;
				// track usage of submit button, if one does not exist in class properties
				// then we add one ourself. This is not a good way to do this, but hey ..
				// and it gets worse...
				if (isset($item["type"]) && $item["type"] === "submit")
				{
					$this->submit_done = true;
				}
			}
		}


		if ($this->submit_done || $this->view_mode == 1)
		{

		}
		elseif (empty($submit) || $submit !== "no")
		{
			$var_name = "SUBMIT";
			$tpl_vars = array(
				"sbt_caption" => !empty($sbt_caption) ? $sbt_caption : t("Salvesta"),
			);
			// I need to figure out whether I have a relation manager
			$this->vars_safe($tpl_vars);

			if (!empty($back_button))
			{
				$this->vars_safe(array(
					"back_button_caption" => t("&lt;&lt;&lt; Tagasi"),
					"back_button_name" => t("submit_and_back"),
				));
				$this->vars_safe(array(
					"BACK_BUTTON" => $this->parse("BACK_BUTTON")
				));
			}

			if (!empty($forward_button))
			{
				$this->vars_safe(array(
					"forward_button_caption" => t("Edasi &gt;&gt;&gt;"),
					"forward_button_name" => t("submit_and_forward"),
				));
				$this->vars_safe(array(
					"FORWARD_BUTTON" => $this->parse("FORWARD_BUTTON")
				));
			}
			$sbt = $this->parse($var_name);
		}

		$this->layoutinfo["_main"] = array(
			"type" => "vbox",
		);

		$xxx = $this->parse_layouts("_main");
		if (sizeof($this->proplist) > 0)
		{
			foreach($this->proplist as $ki => $item)
			{
				// this was set in parse_layout
				if (isset($item["__ignore"]))
				{
					continue;
				}

				$item["html"] = $this->create_element($item);
				if (!empty($item["error"]))
				{
					$this->vars_safe(array(
						"err_msg" => $item["error"],
					));
					$res .= $this->parse("PROP_ERR_MSG");
				}

				// this is what I was talking about before ...
				// move submit button _before_ the aliasmgr
				if (!empty($sbt) && isset($item["type"]) && $item["type"] === "aliasmgr")
				{
					$res .= $sbt;
					unset($sbt);
				}
				$res .= $item["html"];
			}
		}

		$submit_handler = $txt = "";
		if ($this->rte)
		{
			if($this->rte_type == 2)
			{
				$rte = new fck_editor();
				$res .= $rte->draw_editor(array(
					"lang" => aw_ini_get("user_interface.default_language"),
					"props" => $this->rtes,
				));
				foreach($this->rtes as $rte)
				{
					$data["cb_nobreaks[${rte}]"] = 1;
				}
			}
			elseif($this->rte_type == 3)
			{
				$rte = new codepress();
				$res .= $rte->draw_editor(array(
					"props" => $this->rtes,
				));
				foreach($this->rtes as $rte)
				{
					$data["cb_nobreaks[${rte}]"] = 1;
				}
			}
			else
			{
				// make a list of of all RTE-s
				// would be nice if I could update the textareas right when the iframe loses focus ..
				// I'm almost sure I can do that.
				foreach($this->rtes as $rte)
				{
					$txt .= "if (document.getElementById('${rte}_edit'))\n";
					$txt .= "{\n";
					$txt .= "tmpdat = document.getElementById('${rte}_edit').contentWindow.document.body.innerHTML;\n";
					$txt .= "document.changeform.elements['${rte}'].value=document.getElementById('${rte}_edit').contentWindow.document.body.innerHTML;\n";
					$txt .= "}\n";
					$data["cb_nobreaks[${rte}]"] = 1;
				}
				$submit_handler = $txt;
			}
		}

		$scripts = isset($scripts) ? $scripts : "";

		if (!empty($arr["focus"]) && automatweb::$request->arg("action") === "new")
		{
			$scripts .= "if (typeof(document.changeform['" . $arr["focus"] ."'].focus) != \"undefined\") { document.changeform['" . $arr["focus"] ."'].focus();\n}";
		}

		if (isset($is_sa_changed) && $is_sa_changed == 1)
		{
			$scripts .= "if (window.opener) { window.opener.location.reload(); }\n";
		}

		$fn = basename($_SERVER["SCRIPT_FILENAME"],".aw");
		$data["ret_to_orb"] = $fn === "orb" ? 1 : 0;
		$data["charset"] = aw_global_get("charset");

		// let's hope that nobody uses that vbox and hbox spagetti with grouptemplates -- ahz
		// groupboxes where implemented for rateme .. the code is not exactly elegant .. can I kill it?
		// please-please-please?
		if (empty($method))
		{
			$method = "post";
		}

		if ("post" !== strtolower($method))
		{
			$data["no_reforb"] = 1;
			foreach ($data as $key => $value)
			{
				if (is_array($value) and empty($value) or !is_array($value) and strlen($value) < 1)
				{
					unset($data[$key]);
				}
			}
		}

		$this->vars_safe(array(
			"submit_handler" => $submit_handler,
			"scripts" => $scripts,
			"method" => $method,
			"content" => $res,
			"reforb" => $this->mk_reforb($action, $data, $orb_class),
			"form_handler" => !empty($form_handler) ? $form_handler : "orb.aw",
			"SUBMIT" => isset($sbt) ? $sbt : "",
			"help" => ifset($arr, "help"),
		));

		if (!empty($no_insert_reforb))
		{
			$ds = array();
			foreach($data as $k => $v)
			{
				$ds[] = "<input type='hidden' name='$k' value='$v'>";
			}
			$this->vars_safe(array(
				"reforb" => implode("\n", $ds)
			));
		}
	}

	function get_result($arr = array())
	{
		if(empty($this->no_form))
		{
			$this->vars_safe(array(
				"SHOW_CHANGEFORM" => $this->parse("SHOW_CHANGEFORM"),
				"SHOW_CHANGEFORM2" => $this->parse("SHOW_CHANGEFORM2"),
			));
		}

		// show ui messages
		if (count($this->ui_messages) and empty($this->no_form))
		{
			$this->vars_safe(array(
				"MESSAGE" => implode("", $this->ui_messages)
			));
			$this->ui_messages = array();
		}

		if ($this->has_property_help)
		{
			$this->vars_safe(array(
				"HAS_PROPERTY_HELP" => $this->parse("HAS_PROPERTY_HELP")
			));
		}

		// options from cfgform
		if (isset($arr["awcb_cfgform"]) and is_object($arr["awcb_cfgform"]))
		{ // load misc options from given cfgform
			$cfgform_o = $arr["awcb_cfgform"];

			// elem. id for all aw interface container tables
			if ($cfgform_o->prop("awcb_add_id"))
			{// add cfgform object id to main container element id
				$this->vars["contenttbl_id"] = "awcbContentTbl" . $cfgform_o->id();
			}
			else
			{
				$this->vars["contenttbl_id"] = "awcbContentTblDefault";
			}

			// show only form. no tabs, header
			if ($cfgform_o->prop("awcb_form_only"))
			{
				$arr["form_only"] = 1;
			}

			// confirm save
			if ($cfgform_o->prop("awcb_confirm_save_data"))
			{
				$arr["confirm_save_data"] = 1;
			}
		}
		else
		{
			$this->vars["contenttbl_id"] = "awcbContentTblDefault";
		}

		// confirm save/discard on leave page
		if (!empty($arr["confirm_save_data"]) && !(automatweb::$request->arg("action") === "check_leave_page" || automatweb::$request->arg("group") === "relationmgr"))
		{
			$this->vars_safe(array(
				"msg_unload_leave_notice" => t("Andmed on salvestamata, kas soovite andmed enne lahkumist salvestada?"),
				"msg_unload_save_error" => aw_html_entity_decode(t("Andmete salvestamine kahjuks ei &otilde;nnestunud"))
			));
			$this->vars_safe(array(
				"CHECK_LEAVE_PAGE" => $this->parse("CHECK_LEAVE_PAGE")
			));
		}

		if (!empty($arr["element_only"]) && count($this->proplist) <= 1)
		{
			$rv = !empty($this->vars["element"]) ? $this->vars["element"] : $this->vars["value"];
		}
		elseif (!empty($arr["raw_output"]))
		{
			$rv = $this->vars["content"];
		}
		elseif (isset($arr["form_only"]))
		{
			return $this->parse();
		}
		else
		{
			if (empty($arr["content"]))
			{
				$rv = $this->parse();
			}
			else
			{
				$rv = $arr["content"];
			}

			$tp = $this->tp;

			if (is_object($this->tabpanel))
			{
				$tp = $this->tabpanel;
			}

			$bm = new popup_menu();
			$bm->begin_menu("user_bookmarks");
			$bmq = new popup_menu();
			$bmq->begin_menu("user_qa");
			$bmb = new popup_menu();
			$bmb->begin_menu("settings_pop");
			$bm_h = new popup_menu();
			$bm_h->begin_menu("history_pop");

			$tp->vars_safe(array(
				"warn" => isset($this->config["warn"]) ? $this->config["warn"] : null,
			));
			$tp->vars_safe(array(
				"help" => $this->vars["help"],
				"help_url" => isset($this->config["help_url"]) ? $this->config["help_url"] : null,
				"translate_url" => isset($this->config["translate_url"]) ? $this->config["translate_url"] : null,
				"translate_text" => isset($this->config["translate_text"]) ? $this->config["translate_text"] : null,
				"more_help_text" => isset($this->config["more_help_text"]) ? $this->config["more_help_text"] : null,
				"close_help_text" => isset($this->config["close_help_text"]) ? $this->config["close_help_text"] : null,
				"open_help_text" => isset($this->config["open_help_text"]) ? $this->config["open_help_text"] : null,
				"WARNING_LAYER" => !empty($this->config["warn"]) ? $tp->parse("WARNING_LAYER") : "",
				"HAS_WARNING" => !empty($this->config["warn"]) ? $tp->parse("HAS_WARNING") : "",
				"feedback_link" => $this->mk_my_orb("redir_new_feedback", array(
					"d_class" => $this->class_name,
					"d_obj" => $this->object_id,
					"object_grp" => $this->use_group,
					"url" => get_ru(),
				), "customer_feedback_entry"),
				"feedback_text" => t("Tagasiside"),
				"feedback_m_link" => $this->mk_my_orb("redir_m", array("url" => $this->my_get_ru("customer_feedback_manager")), "customer_feedback_manager"),
				"feedback_m_text" => t("Kasutajatugi"),
				"help_text" => t("Abi"),
				"search_text" => t("Otsi"),
				"bm_pop" => $this->prog_acl("view", "can_bm") ? $bm->get_menu(array(
					"load_on_demand_url" => $this->mk_my_orb("pm_lod", array("url" => get_ru()), "user_bookmarks"),
					"text" => '<img src="/automatweb/images/aw06/ikoon_jarjehoidja.gif" alt="" width="16" height="14" border="0" class="ikoon" />'.t("J&auml;rjehoidja")//.' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" style="margin: 0 -3px 1px 0px" />'
				)) : "",
				"history_pop" => $this->prog_acl("view", "can_history") ? $bm_h->get_menu(array(
					"load_on_demand_url" => $this->mk_my_orb("hist_lod", array("url" => get_ru()), "user"),
					"text" => '<img src="/automatweb/images/aw06/ikoon_ajalugu.gif" alt="" width="13" height="13" border="0" class="ikoon" />'.t("Ajalugu")//.' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" style="margin: 0 -3px 1px 0px" />'
				)) : "",
				"qa_pop" => $this->prog_acl("view", "can_quick_add") ? $bmq->get_menu(array(
					"load_on_demand_url" => $this->mk_my_orb("qa_lod", array("url" => get_ru()), "obj_quick_add"),
					"text" => '<img alt="" title="" border="0" src="'.aw_ini_get("baseurl").'automatweb/images/aw06/ikoon_lisa.gif" id="mb_user_qa" border="0" class="ikoon" />'.t("Lisa kiiresti")//.' <img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" style="margin: 0 -3px 1px 0px" /></a>'
				)) : "",
				"settings_pop" => $bmb->get_menu(array("load_on_demand_url" => $this->mk_my_orb("settings_lod", array("url" => get_ru()), "user"))),
				"srch_link" => $this->mk_my_orb("redir_search", array("url" => $this->my_get_ru("aw_object_search_if")), "aw_object_search_if")
			));

			if ($this->prog_acl("view", "can_search"))
			{
				$tp->vars_safe(array(
					"HAS_SEARCH" => $tp->parse("HAS_SEARCH")
				));
			}

			if (!automatweb::$request->arg("in_popup"))
			{
				$tp->vars_safe(array(
					"NOT_POPUP" => $tp->parse("NOT_POPUP")
				));
			}

			if (aw_ini_get("site_id") == 155)
			{
				$tp->vars_safe(array(
					"NEWIF" => $tp->parse("NEWIF"),
				));
			}

			if (!empty($this->config["show_help"]))
			{
				$tp->vars(array(
					"SHOW_HELP" => $tp->parse("SHOW_HELP"),
				));
			}

			if (isset($this->config["add_txt"]) && $this->config["add_txt"] != "")
			{
				$tp->vars(array(
					"addt_content" => $this->config["add_txt"]
				));
				$tp->vars(array(
					"ADDITIONAL_TEXT" => $tp->parse("ADDITIONAL_TEXT")
				));
			}

			if ($this->form_layout !== "boxed")
			{
				// perhaps, just perhaps I should create a separate property type
				// out of the tabpanel
				//$rv = $this->tp->get_tabpanel(array());
				if ($this->tabs)
				{
					$rv = $tp->get_tabpanel(array(
						"content" => $rv,
					));
				}
			}
			else
			{
				$tabs = $tp->get_tabpanel(array(
					"content" => $rv,
					"panels_only" => true,
				));

				if (is_array($tabs))
				{
					foreach($tabs as $key => $item)
					{
						if (empty($key))
						{
							$loc = "top";
						}
						elseif ($key === "navi")
						{
							$loc = "left";
						}
						else
						{
							$loc = $key;
						}
						$this->additional_content[$loc] .= join("",$item);
					}
				}
				else
				{
					$rv = $tabs;
				}

				//$this->additional_content["top"] .= $tabs;
				//$tabs = $tp->get_tabpanel(array());
				//$this->additional_content["top"] .= $tabs;
			}
		}

		if ($this->form_layout === "boxed")
		{
			$this->read_template("boxed.tpl");
			$this->vars_safe(array(
				"top_content" => $this->additional_content["top"],
				"left_content" => $this->additional_content["left"],
				"right_content" => $this->additional_content["right"],
				"bottom_content" => $this->additional_content["bottom"],
				"content" => $rv,
			));
			return $this->parse();
		}

		return $rv;
	}

	function draw_element($args = array())
	{
		if (!isset($args["type"]))
		{
			$args["type"] = "";
		}
		$tmp = new aw_array($args);
		$arr = $tmp->get();

		if ($args["type"] === "submit")
		{
			$this->submit_done = true;
		}

		// Check the types and call their counterparts
		// from the HTML class. If you want to support
		// a new property type, this is where you will have
		// to register it.
		switch($args["type"])
		{
			case "chooser":
				$options = isset($arr["options"]) ? new aw_array($arr["options"]) : new aw_array();
				$retval = "";

				foreach($options->get() as $key => $val)
				{
					$caption = $val;
					if (!empty($args["edit_links"]))
					{
						$o = new object($key);
						$caption = html::href(array(
							"url" => $this->mk_my_orb("change",array("id" => $key),$o->class_id()),
							"caption" => $caption,
						));
					}

					if (!empty($arr["multiple"]))
					{
						$retval .= html::checkbox(array(
							"label" => $caption,
							"name" => $arr["name"] . "[" . $key . "]",
							"checked" => !empty($arr["value"][$key]),
							"value" => $key,
							"disabled" => ifset($arr, "disabled", $key),
							"onclick" => ifset($arr, "onclick"),
						));
					}
					else
					{
						$retval .= html::radiobutton(array(
							"caption" => $caption,
							"name" => $arr["name"],
							"checked" => isset($arr["value"]) && ($arr["value"] == $key),
							"value" => $key,
							"onclick" => empty($arr["onclick"]) ? "" : $arr["onclick"],
							"disabled" => empty($arr["disabled"][$key]) ? "" : $arr["disabled"][$key],
						));
					}

					if (isset($arr["orient"]) and $arr["orient"] === "vertical")
					{
						$retval .= html::linebreak();
					}
				}
				break;

			case "select":
				$retval = html::select($arr);
				break;

			case "textbox":
				$retval = html::textbox($arr);
				$id = str_replace("[","_",$arr["name"]);
				$id = str_replace("]","_",$id);
				break;

			case "textarea":
				if (isset($arr["richtext"]))
				{
					// It is needed to pass the rich text editor type to textarea drawing function, because for example FCKEditor needs textarea elements to be present in html --dragut@11.11.2009
					$arr['rte_type'] = (int)$this->rte_type;

					$this->rte = true;
					$this->rtes[] = $arr["name"];
					$this->vars_safe(array("rte_type" => $this->rte_type));
				}
				$retval = html::textarea($arr);
				$this->vars(array("value" => NULL));
				break;

			case "password":
				$retval = html::password($arr);
				break;

			case "fileupload":
				$retval = html::fileupload($arr);
				break;

			case "checkbox":
				$arr["checked"] = (!empty($arr["value"]) && ( (isset($arr["ch_value"]) && $arr["value"] == $arr["ch_value"]) || !isset($arr["ch_value"]) ) );
				$arr["value"] = isset($arr["ch_value"]) ? $arr["ch_value"] : "";
				if (!empty($arr["no_caption"]))
				{
					unset($arr["caption"]);
				}
				$tmp = $arr;
				if (!empty($tmp["parent"]))
				{
					unset($tmp["caption"]);
					unset($tmp["orig_caption"]);
				}
				$retval = html::checkbox($tmp);
				/*
				$retval = html::checkbox(array(
					"label" => isset($arr["label"]) ? $arr["label"] : "",
					"name" => $arr["name"],
					"value" => isset($arr["ch_value"]) ? $arr["ch_value"] : "",
					"caption" => isset($arr["caption"]) ? $arr["caption"] : "",
					"checked" => ($arr["value"] && ( (isset($arr["ch_value"]) && $arr["value"] == $arr["ch_value"]) || !isset($arr["ch_value"]) ) ),
					"onclick" => $arr["onclick"],
				));
				*/
				break;

				// will probably be deprecated, after all what good is a
				// single
			case "radiobutton":
				$retval = html::radiobutton(array(
					"name" => $arr["name"],
					"value" => $arr["rb_value"],
					"caption" => $arr["caption"],
					"checked" => ($arr["value"] == $arr["rb_value"])
				));
				break;

			case "submit":
				// but what if there is more than 1 of those?
				// attaching this might just break something somewhere
				$submit_onclick = isset($arr["onclick"]) ? ($arr["onclick"] . " ") : "";
				if (isset($arr['action']))
				{
					$arr["onclick"] = $submit_onclick."submit_changeform('" . $arr['action'] . "'); return false;";
				}
				else
				{
					$arr["onclick"] = $submit_onclick."submit_changeform();";
				}
				$arr["class"] = "sbtbutton";
				$retval = html::submit($arr);
				break;

			case "reset":
				$arr["class"] = "sbtbutton";

			case "button":
				$retval = html::button($arr);
				break;

			case "time_select":
				$retval = html::time_select($arr);
				break;

			case "date_select":
				$retval = html::date_select($arr);
				break;

			case "datetime_select":
				$retval = html::datetime_select($arr);
				break;

			case "img":
				$retval = html::img($arr);
				break;

			case "href":
				$retval = html::href($arr);
				break;
			case "hidden":
				// hidden elements end up in the orb_vars
				$this->orb_vars[$arr["name"]] = ifset($arr, "value");
				$retval = "";
				break;

			default:
				$retval = html::text($arr);
				break;
		}

		// do cfg edit mode check
		/*
		$this->vars(array(
			"cfgform_edit_mode" => $this->_do_cfg_edit_mode_check($arr),
		));
		*/
		$retval .= $this->_do_cfg_edit_mode_check($arr);

		return $retval;
	}

	function add_tab($arr)
	{
		if (is_object($this->tabpanel))
		{
			return $this->tabpanel->add_tab($arr);
		}
		else
		{
			return $this->tp->add_tab($arr);
		}
	}

	function create_element($item)
	{
		$type = isset($item["type"]) ? $item["type"] : "";
		$value = isset($item["value"]) ? $item["value"] : null;
		$item["html"] = "";
		if ($type === "iframe")
		{
			$src = $item["src"];
			$item["html"] = "<iframe id='contentarea' name='contentarea' src='${src}' style='width: 100%; height: 95%; border-top: 1px solid black;' frameborder='no' scrolling='yes'></iframe>";
		}
		else if ($type === "hidden")
		{
			// hidden elements end up in the orb_vars
			$this->orb_vars[$item["name"]] = $value;
		}
		else if ($type === "submit")
		{
			$item["html"] = $this->put_submit($item);
			$this->submit_done = true;
		}
		else if (isset($item["no_caption"]))
		{
			$item["html"] = $this->put_content($item);
		}
		else if (isset($item["subtitle"]))
		{
			$item["html"] = $this->put_header_subtitle($item);
		}
		else if ($type)
		{
			$item["html"] = $this->put_line($item);
		}
		// this I do not like
		elseif (!empty($item["caption"]))
		{
			$item["html"] = $this->put_header($item);
		}
		else
		{
			$item["html"] = $this->put_content($item);
		};
		return $item["html"];
	}

	function parse_layouts($layout_name)
	{
		$layout_items = array();
		$sub_layouts = array();
		if(!empty($this->view_layout) && ($this->view_layout === $layout_name || isset($this->parent_layouts) && !empty($this->show_layouts[$this->parent_layouts[$layout_name]])))
		{
			$this->show_layouts[$layout_name] = 1;
		}

		foreach(safe_array(isset($this->layout_by_parent[$layout_name]) ? $this->layout_by_parent[$layout_name] : null) as $lkey => $lval)
		{
			$this->parent_layouts[$lkey] = $layout_name;
			$html = $this->parse_layouts($lkey);
			if($this->view_layout && $this->view_layout == $lkey)
			{
				die(mb_convert_encoding($html, "UTF-8", "ISO-8859-1"));
			}
			$sub_layouts[$lkey] = $html;

			if (!empty($html))
			{
				$layout_items[] = $html;
			}
		}

		if(!empty($this->view_layout))
		{
			if(empty($this->show_layouts[$this->parent_layouts[$layout_name]]) && empty($this->show_layouts[$layout_name]))
			{
				return null;
			}
		}
		$html = "";
		$ldata = $this->layoutinfo[$layout_name];
		$location = false;
		if ($layout_name === "_main")
		{
			// put already parsed layouts in their correct places
			// first property in the layout sets the location
			foreach($this->proplist as $pkey => $pval)
			{
				if (!empty($pval["parent"]))
				{
					$gx = ifset($this->lp_chain, $pval["parent"]);
					while (isset($this->lp_chain[$gx]) and $this->lp_chain[$gx] != "_main")
					{
						$gx = $this->lp_chain[$gx];
					}

					if (!empty($sub_layouts[$gx]))
					{
						$this->proplist[$pkey]["value"] = $sub_layouts[$gx];
						$this->proplist[$pkey]["type"] = "text";
						$this->proplist[$pkey]["caption"] = empty($this->layoutinfo[$gx]["caption"]) ? "" : $this->layoutinfo[$gx]["caption"];
						// XXX: right now this is rewriting the first property in a box to contain
						// the rest of the parsed properties in that box, probably shouldn't
						// do that though.

						// set no_caption, if layout has no caption, otherwise the output
						// will contain a property with an empty caption, which will look
						// ugly in htmlclient at least
						if (empty($this->proplist[$pkey]["caption"]))
						{
							$this->proplist[$pkey]["no_caption"] = 1;
						}
						unset($this->proplist[$pkey]["__ignore"]);
						unset($sub_layouts[$gx]);
					}
					// this deals with lp_chain thingie .. I need to fix that too
					elseif (!empty($sub_layouts[$pval["parent"]]))
					{
						$gx = $pval["parent"];
						$this->proplist[$pkey]["value"] = $sub_layouts[$gx];
						$this->proplist[$pkey]["type"] = "text";
						// XXX: this will probably cause me problems later on ...
						unset($this->proplist[$pkey]["caption"]);
						$this->proplist[$pkey]["no_caption"] = 1;

						if (!empty($this->layoutinfo[$gx]["caption"]))
						{
							$this->proplist[$pkey]["caption"] = $this->layoutinfo[$gx]["caption"];
							unset($this->proplist[$pkey]["no_caption"]);
						};
						unset($this->proplist[$pkey]["__ignore"]);
						unset($sub_layouts[$gx]);
					}
				}
			}
		}
		else
		{
			// this deals with  deepers levels
			foreach(safe_array(isset($this->properties_by_parent[$layout_name]) ? $this->properties_by_parent[$layout_name] : null) as $pkey => $pval)
			{
				$layout_items[] = $this->put_griditem($this->proplist[$pkey]);
				$this->proplist[$pkey]["__ignore"] = 1;
			}
		}

		if (empty($layout_items) and empty($sub_layouts))
		{
			$html = "";
		}
		elseif ("hbox" === $ldata["type"])
		{
			$cell_widths = array();
			if (!empty($ldata["width"]))
			{
				$cell_widths = explode(":",$ldata["width"]);
			}

			$content = "";
			foreach($layout_items as $cell_nr => $layout_item)
			{
				$cell_width = isset($cell_widths[$cell_nr]) ? " width='" . $cell_widths[$cell_nr] . "'" : "";
				$this->vars_safe(array(
					"item" => $layout_item,
					"item_width" => $cell_width
				));
				$content .= $this->parse("GRID_HBOX_ITEM");
			}

			$closer = $ghc = $gce = "";
			if (!empty($ldata["area_caption"]))
			{
				$state = active_page_data::get_layer_state($this->class_name, $this->use_group, $layout_name);
				if (!$state and isset($ldata["default_state"]) and $ldata["default_state"] === "closed")
				{
					if ($ldata["default_state"] === "closed")
					{
						$state = active_page_data::LAYER_CLOSED;
					}
					elseif ($ldata["default_state"] === "open")
					{
						$state = active_page_data::LAYER_OPEN;
					}
				}

				if (!empty($ldata["closeable"]))
				{
					$this->vars_safe(array(
						"grid_name" => $layout_name,
						"close_text" => t("Kinni"),
						"open_text" => t("Lahti"),
						"start_text" => active_page_data::LAYER_CLOSED === $state ? t("Lahti") : t("Kinni"),
						"open_layer_url" => $this->mk_my_orb("open_layer", array(
							"u_class" => $this->class_name,
							"u_group" => $this->use_group,
							"u_layout" => $layout_name
						), "active_page_data"),
						"close_layer_url" => $this->mk_my_orb("close_layer", array(
							"u_class" => $this->class_name,
							"u_group" => $this->use_group,
							"u_layout" => $layout_name
						), "active_page_data"),
						"closer_state" => active_page_data::LAYER_CLOSED === $state ? "down" : "up"
					));
					$closer = $this->parse("GRID_HAS_CLOSER");
				}

				$this->vars_safe(array(
					"grid_name" => $layout_name,
					"area_caption" => htmlspecialchars(aw_html_entity_decode($ldata["area_caption"])),
					"display" => active_page_data::LAYER_CLOSED === $state ? "none" : "block",
					"GRID_HAS_CLOSER" => $closer,
				));
				$ghc = $this->parse("GRID_HAS_CAPTION");
				$gce = $this->parse("GRID_HAS_CAPTION_END");
			}

			$this->vars_safe(array(
				"GRID_HBOX_ITEM" => $content,
				"GRID_HAS_CAPTION" => $ghc,
				"GRID_HAS_CAPTION_END" => $gce
			));

			$_t = $this->parse("GRID_HBOX");

			if($this->view_layout && $this->view_layout === $layout_name && !$this->view_outer)
			{
				$html .= $_t;
			}
			else
			{
				$this->vars_safe(array(
					"GRID_HBOX" => $_t,
					"grid_outer_name" => $layout_name."_outer",
				));
				$html .= $this->parse("GRID_HBOX_OUTER");
			}
		}
		elseif ("vbox" === $ldata["type"])
		{
			$content = "";
			foreach($layout_items as $cell_nr => $layout_item)
			{
				$this->vars_safe(array(
					"item" => $layout_item,
				));
				$content .= $this->parse("GRID_VBOX_ITEM");
			};

			$this->vars_safe(array(
				"GRID_VBOX_ITEM" => $content,
			));

			$closer = $ghc = $gce = "";
			if (!empty($ldata["area_caption"]))
			{
				$state = active_page_data::get_layer_state($this->class_name, $this->use_group, $layout_name);
				if (!$state and isset($ldata["default_state"]) and $ldata["default_state"] === "closed")
				{
					if ($ldata["default_state"] === "closed")
					{
						$state = active_page_data::LAYER_CLOSED;
					}
					elseif ($ldata["default_state"] === "open")
					{
						$state = active_page_data::LAYER_OPEN;
					}
				}

				if (!empty($ldata["closeable"]))
				{
					$this->vars_safe(array(
						"grid_name" => $layout_name,
						"close_text" => t("Kinni"),
						"open_text" => t("Lahti"),
						"start_text" => active_page_data::LAYER_CLOSED === $state ? t("Lahti") : t("Kinni"),
						"open_layer_url" => $this->mk_my_orb("open_layer", array(
							"u_class" => $this->class_name,
							"u_group" => $this->use_group,
							"u_layout" => $layout_name
						), "active_page_data"),
						"close_layer_url" => $this->mk_my_orb("close_layer", array(
							"u_class" => $this->class_name,
							"u_group" => $this->use_group,
							"u_layout" => $layout_name
						), "active_page_data"),
						"closer_state" => active_page_data::LAYER_CLOSED === $state ? "down" : "up"
					));
					$closer = $this->parse("VGRID_HAS_CLOSER");
				}

				//miski errori n2itamise v6imalus layouti sisse
				$this->vars(array("layout_error" => !empty($_SESSION["layout_error"][$layout_name]) ? $_SESSION["layout_error"][$layout_name] : ""));
				$this->vars(array("LAYOUT_ERROR_SUB" => !empty($_SESSION["layout_error"][$layout_name]) ? $this->parse("LAYOUT_ERROR_SUB") : ""));
				unset($_SESSION["layout_error"][$layout_name]);

				$this->vars_safe(array(
					"grid_name" => $layout_name,
					"area_caption" => htmlspecialchars(aw_html_entity_decode($ldata["area_caption"])),
					"display" => active_page_data::LAYER_CLOSED === $state ? "none" : "block",
					"VGRID_HAS_CLOSER" => $closer,
					"VGRID_HAS_PADDING" => !empty($ldata["no_padding"]) ? "" : $this->parse("VGRID_HAS_PADDING"),
					"VGRID_NO_PADDING" => !empty($ldata["no_padding"]) ? $this->parse("VGRID_NO_PADDING") : "",
				));
				$ghc = $this->parse("VGRID_HAS_CAPTION");
				$gce = $this->parse("VGRID_HAS_CAPTION_END");
			}

			$this->vars_safe(array(
				"VGRID_HAS_CAPTION" => $ghc,
				"VGRID_HAS_CAPTION_END" => $gce
			));

			$_t = $this->parse("GRID_VBOX");
			if($this->view_layout && $this->view_layout === $layout_name && !$this->view_outer)
			{
				$html .= $_t;
			}
			else
			{
				$this->vars_safe(array(
					"GRID_VBOX" => $_t,
					"grid_outer_name" => $layout_name . "_outer",
				));
				$html .= $this->parse("GRID_VBOX_OUTER");
			}
		}
		elseif ("vbox_sub" === $ldata["type"])
		{
			$content = "";
			foreach($layout_items as $cell_nr => $layout_item)
			{
				$this->vars_safe(array(
					"item" => $layout_item,
				));
				$content .= $this->parse("GRID_VBOX_SUB_ITEM");
			}

			$this->vars_safe(array(
				"GRID_VBOX_SUB_ITEM" => $content,
			));

			$closer = $ghc = $gce = "";
			if (!empty($ldata["area_caption"]))
			{
				$state = active_page_data::get_layer_state($this->class_name, $this->use_group, $layout_name);
				if (!$state and isset($ldata["default_state"]) and $ldata["default_state"] === "closed")
				{
					if ($ldata["default_state"] === "closed")
					{
						$state = active_page_data::LAYER_CLOSED;
					}
					elseif ($ldata["default_state"] === "open")
					{
						$state = active_page_data::LAYER_OPEN;
					}
				}

				if (!empty($ldata["closeable"]))
				{
					$this->vars_safe(array(
						"grid_name" => $layout_name,
						"close_text" => t("Kinni"),
						"open_text" => t("Lahti"),
						"start_text" => active_page_data::LAYER_CLOSED === $state ? t("Lahti") : t("Kinni"),
						"open_layer_url" => $this->mk_my_orb("open_layer", array(
							"u_class" => $this->class_name,
							"u_group" => $this->use_group,
							"u_layout" => $layout_name
						), "active_page_data"),
						"close_layer_url" => $this->mk_my_orb("close_layer", array(
							"u_class" => $this->class_name,
							"u_group" => $this->use_group,
							"u_layout" => $layout_name
						), "active_page_data"),
						"closer_state" => active_page_data::LAYER_CLOSED === $state ? "down" : "up"
					));
					$closer = $this->parse("VSGRID_HAS_CLOSER");
				}

				$this->vars_safe(array(
					"grid_name" => $layout_name,
					"area_caption" => htmlspecialchars(aw_html_entity_decode($ldata["area_caption"])),
					"display" => active_page_data::LAYER_CLOSED === $state ? "none" : "block",
					"VSGRID_HAS_CLOSER" => $closer,
					"VSGRID_HAS_PADDING" => !empty($ldata["no_padding"]) ? "" : $this->parse("VSGRID_HAS_PADDING"),
					"VSGRID_NO_PADDING" => !empty($ldata["no_padding"]) ? $this->parse("VSGRID_NO_PADDING") : "",
				));
				$ghc = $this->parse("VSGRID_HAS_CAPTION");
				$gce = $this->parse("VSGRID_HAS_CAPTION_END");
			}

			$this->vars_safe(array(
				"VSGRID_HAS_CAPTION" => $ghc,
				"VSGRID_HAS_CAPTION_END" => $gce
			));

			$_t = $this->parse("GRID_VBOX_SUB");
			if($this->view_layout && $this->view_layout === $layout_name && !$this->view_outer)
			{
				$html .= $_t;
			}
			else
			{
				$this->vars_safe(array(
					"GRID_VBOX_SUB" => $_t,
					"grid_outer_name" => $layout_name."_outer",
				));
				$html .= $this->parse("GRID_VBOX_SUB_OUTER");
			}
		}

		return $html;
	}

	function put_griditem($arr)
	{
		// support caption positions TOP and LEFT for now only
		// name refers to a VAR inside the template
		$caption_template = (isset($arr["captionside"]) and "TOP" === strtoupper($arr["captionside"])) ? "CAPTION_TOP" : "CAPTION_LEFT"; // TOP or LEFT
		$caption = empty($arr["caption"]) ? "" : $arr["caption"];
		$errmsg = "";
		$property_help = $this->get_property_help($arr);

		// reset all captions
		$this->vars_safe(array(
			"caption" => $caption,
			"comment" => $property_help,
			"CAPTION_LEFT" => "",
			"CAPTION_TOP" => "",
			"element" => $this->draw_element($arr),
			"element_name" => $arr["name"],
			"err_msg" => isset($arr["error"]) ? $arr["error"] : "",
			"GRID_ERR_MSG" => ""
		));

		// errmsg
		if (!empty($arr["error"]) and empty($this->no_form))
		{
			$errmsg = $this->parse("GRID_ERR_MSG");
		}

		// main vars
		$this->vars_safe(array(
			"caption" => $caption,
			"comment" => $property_help,
			"CAPTION_LEFT" => "",
			"CAPTION_TOP" => "",
			"element" => $this->draw_element($arr),
			"err_msg" => isset($arr["error"]) ? $arr["error"] : "",
			"GRID_ERR_MSG" => $errmsg,
			$caption_template => $this->parse($caption_template)
		));

		$tpl = empty($arr["no_caption"]) ? "GRIDITEM" : "GRIDITEM_NO_CAPTION";
		$src = $this->parse($tpl);
		return $src;
	}

	private function get_property_help($args)
	{
		$this->has_property_help = true;
		if(isset($args["comment"]) && strlen($args["comment"]))
		{
			$this->vars(array(
				"property_comment" => html_entity_decode($args["comment"])
			));
			$property_help = $this->parse("PROPERTY_HELP");
		}
		else
		{
			$property_help = "";
		}
		return $property_help;
	}

	private function my_get_ru($class)
	{
		if($class == $this->class_name)
		{
			return isset($_GET["return_url"]) ? $_GET["return_url"] : (isset($_GET["url"]) ? $_GET["url"] : NULL);
		}
		return get_ru();
	}
}
