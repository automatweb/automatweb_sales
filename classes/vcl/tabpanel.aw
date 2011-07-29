<?php

// tabpanel.aw - class for creating tabbed dialogs

class tabpanel extends aw_template
{
	////
	// !Initializes a tabpanel object
	/**
		@attrib params=name api=1
		@param tpl optional type=string
		Uses given template file
		@errors
		- in case of a missing template file, a raise_error will be called
		@comment
		Initializes tabpanel
		@examples
	**/
	function tabpanel($args = array())
	{
		$this->init("tabpanel");
		$tpl = isset($args["tpl"]) ? $args["tpl"] . ".tpl" : "tabs.tpl";
		$this->read_template($tpl);
		$this->tabs = array();
		$this->tabcount = array();
		$this->hide_one_tab = 0;
	}

	/**
		@attrib params=name api=1

		@param active optional type=bool
		Whether to use the "selected" subtemplate for this tab.

		@param caption optional type=string
		Text to display as caption

		@param link optional type=string
		Url to where it links to

		@param tabgroup optional type=string
		If set, uses that template for showing tab

		@param disabled optional type=bool
		True sets the tab disabled(gray, non-clickable etc)

		@param level optional type=int
		Sets the tabs depth level, level 2 items are lower than level 1 tabs etc. Default is 1

		@comment
		Adds a new tab to the panel.
		@example
		$tp = get_instance("vcl/tabpanel");
		// creates tab
		$tp->add_tab(array(
			"active" => true,					// uses selected template
			"caption" => t("first"),			// set's caption
			"link" => "http://www.automatweb.com",	// sets url
		));
		// adds a second tab beneath first, which is disabled
		$tp->add_tab(array(
			"caption" => t("second"),
			"level" => 2,
			"disabled" => true,
		));
	**/
	function add_tab($args = array())
	{
		$tab_prefix = isset($args["tabgroup"]) ? $args["tabgroup"] . "_" : "";
		if (aw_global_get("output_charset") != "" && aw_global_get("output_charset") != aw_global_get("charset"))
		{
			$args["caption"] = iconv(aw_global_get("charset"), aw_global_get("output_charset"), $args["caption"]);
		}
		if (isset($args["active"]) && $args["active"])
		{
			$subtpl = "sel_tab";
			if(!empty($args["encoding"]))
			{
				aw_global_set("output_charset", $args["encoding"]);
			}
		}
		else
		{
			$subtpl = "tab";
		}

		if (isset($args["disabled"]) && $args["disabled"])
		{
			$subtpl = "disabled_tab";
		}

		if (isset($args["level"]) && $args["level"])
		{
			$level = $args["level"];
		}
		else
		{
			$level = 1;
		}

		// no link? so let's show the tab as disabled
		if (isset($args["link"]) && strlen($args["link"]) == 0)
		{
			$subtpl = "disabled_tab";
		}
		$this->vars(array(
			"cfgform_edit_mode" => $this->_do_cfg_edit_mode_check($args),
			"caption" => $args["caption"],
			"link" => $args["link"],
			"target" => $args["target"]
		));

		$use_subtpl = $tab_prefix . $subtpl . "_L" . $level;
		//$secondary = $tab_prefix . $use_subtpl;

		if (!$this->is_template($use_subtpl))
		{
			$use_subtpl = $subtpl . "_L" . $level;
			$tab_prefix = "";
		}

		if (isset($this->tabcount[$tab_prefix . $level]))
		{
			$this->tabcount[$tab_prefix . $level]++;
		}
		else
		{
			$this->tabcount[$tab_prefix . $level] = 1;
		}

		// initialize properly
		if (empty($this->tabs[$tab_prefix . $level]))
		{
			$this->tabs[$tab_prefix . $level] = "";
		}


		//$this->tabs[$level] .= $this->parse($subtpl . "_L" . $level);
		$this->tabs[$tab_prefix . $level] .= $this->parse($use_subtpl);

		// so, I need a way to specify other tab groups.
	}

	////
	// !Initializes a tabpanel component
	function init_vcl_property($arr)
	{
		$prop = $arr["property"];
		$prop["vcl_inst"] = $this;


		return array($prop["name"] => $prop);
		//print "initializing tab panel<br>";



	}

	function get_html()
	{
		// this thing has to return generated html from the component
		return $this->get_tabpanel();
	}

	/**
		@attrib params=name api=1
		@param logo_image optional type=string
		To set logo image.
		@param background_image optional type=string
		To set background image.
		@comment
		Allows to set background & logo image. Tabpanel style must be set to 'with_logo' with set_style() method
		@examples

	**/
	function configure($arr)
	{
		if (isset($arr["logo_image"]))
		{
			$this->vars(array(
				"logo_image" => $arr["logo_image"],
			));
		}

		if (isset($arr["background_image"]))
		{
			$this->vars(array(
				"background_image" => $arr["background_image"],
			));
		}
	}

	/**
		@attrib params=pos api=1
		@comment
		Allows to set template that uses logo
		@param style_name optional type=string
		If param is with_logo, the template is changed.
		@examples
		// set new template that uses logo and background
		$tp->set_style("with_logo");
		// set logo and background image file url's
		$tp->configure(array("logo_image" => "image_file.gif", "background_image" => "background.gif"));
	**/
	function set_style($style_name)
	{
		if ($style_name === "with_logo")
		{
			$this->read_template("tabs_with_logo.tpl");
		}
	}


	/**
		@attrib params=name api=1
		@comment
		makes html code from given tabpanel object
		@param panels_only optinal type=bool
		Returns somesort of array including tabpanel in arr[][0]
		@param toolbar optinal type=string
		Puts the given text on top of the tabpanel???
		@returns
		Returns html code for that tabpanel
		@examples
		$tp = get_instance("vcl/tabpanel");
		$tp->add_tab(array(
			"caption" => t("first"),
		));

		//echos the tabpanel.
		print $tp->get_tabpanel();
	**/
	function get_tabpanel($args = array())
	{
		$tabs = "";
		$panels = array();
		$this->vars(array(
			"uid" => aw_global_get("uid"),
			"time" => $this->time2date(time()),
		));
		foreach($this->tabcount as $level => $val)
		{
			if (($val > 1) || !$this->hide_one_tab)
			{
				$prefix = "";
				$lnr = $level;
				if (strpos($level,"_") !== false)
				{
					$px = strpos($level,"_") + 1;
					$prefix = substr($level,0,strpos($level,"_") + 1);
					$lnr = substr($level,$px);
				}
				$this->vars_safe(array(
					$prefix . "tab_L" . $lnr  => $this->tabs[$level],
				));
				$this->vars_safe(array(
					$prefix . "tabs_L" . $lnr => $this->parse($prefix . "tabs_L" . $lnr),
				));

				if (!empty($args["panels_only"]))
				{
					$r_prefix = str_replace("_","",$prefix);
					$panels[$r_prefix][] = $this->parse($prefix . "tabs_L" . $lnr);
				}
			}
		}

		if (!empty($args["panels_only"]))
		{
			return $panels;
		}


		$toolbar = isset($args["toolbar"]) ? $args["toolbar"] : "";
		$toolbar2 = isset($args["toolbar2"]) ? $args["toolbar2"] : "";

		// how do I return different subtemplates?

		$this->vars_safe(array(
			//"tabs" => $tabs,
			"toolbar" => $toolbar,
//          "toolbar2" => $toolbar2,
			"content" => $args["content"],
		));

		if (count($this->tabcount))
		{
			$this->vars(array(
				"HAS_TABS" => $this->parse("HAS_TABS")
			));
		}
		else
		{
			$this->vars(array(
				"NO_TABS" => $this->parse("NO_TABS")
			));
		}

		return $this->parse();
	}

	/**
		@attrib params=name api=1
		@comment
		Makes a tabpanel from given tabs. Links are made using aw_url_change_var($var, key_for_tab).
		@param panel_props optional type=array
		Tabpanel initializing parameters('tlp')
		@param var required type=string
		Variable name with what to pass the selection to url
		@param default optional type=string
		Sets the default tab
		@param opts optional type=array
		Array of tabs:
		array ("tab_key" => "caption")
		@returns
		Returns an instance of tabpanel, with the given tabs added.
		@examples
		$tp = tabpanel::simple_tabpanel(array(
			"panel_props" => array("tpl" => "headeronly"),
			"var" => "cool_tab",
			"default" => "entities",
			"opts" => array("entities" => "Olemid", "processes" => "Protsessid")
		));
		print $tp->get_tabpanel();
		//this will create a tabpanel with two tabs, active is derived from the "cool_tab" variable in the url
	**/
	function simple_tabpanel($arr)
	{
		if (!isset($_GET[$arr["var"]]) || empty($_GET[$arr["var"]]))
		{
			$_GET[$arr["var"]] = $arr["default"];
		}

		$tb = new tabpanel($arr["panel_props"]);
		foreach($arr["opts"] as $k => $v)
		{
			$tb->add_tab(array(
				"link" => aw_url_change_var($arr["var"], $k),
				"caption" => $v,
				"active" => ($_GET[$arr["var"]] == $k)
			));
		}
		return $tb;
	}

	protected function _do_cfg_edit_mode_check($arr)
	{
		if (!isset($_SESSION["cfg_admin_mode"]) || !$_SESSION["cfg_admin_mode"] == 1 || !is_oid($_GET["id"]))
		{
			return "";
		}

		static $cur_cfgform;
		static $cur_cfgform_found = false;

		if (!$cur_cfgform_found)
		{
			$cur_cfgfor_found = true;
			$i = get_instance(CL_FILE);
			$o = obj($_GET["id"]);
			$i->clid = $o->class_id();
			$cur_cfgform = $i->get_cfgform_for_object(array(
				"args" => $_GET,
				"obj_inst" => $o,
				"ignore_cfg_admin_mode" => 1
			));
		}

		$green = " <a href='javascript:void(0)' onClick='cfEditClickGroup(\"".$arr["id"]."\", ".$_GET["id"].");'><img src='".aw_ini_get("icons.server")."cfg_edit_green.png' id='cfgEditGroup".$arr["id"]."'/></a>";
		$red = " <a href='javascript:void(0)' onClick='cfEditClickGroup(\"".$arr["id"]."\", ".$_GET["id"].");'><img src='".aw_ini_get("icons.server")."cfg_edit_red.png' id='cfgEditGroup".$arr["id"]."'/></a>";

		// get default cfgform for this object and get property status from that
		if ($this->can("view", $cur_cfgform))
		{
			$cfo = obj($cur_cfgform);
			if ($cfo->group_is_hidden($arr["id"]))
			{
				return $red;
			}
			else
			{
				return $green;
			}
		}
		else
		{
			// green buton
			return $green;
		}
	}
}
