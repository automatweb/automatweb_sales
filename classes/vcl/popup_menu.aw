<?php

class popup_menu extends aw_template
{
	private $items = array();
	private $menus = array();
	private $menu_id;

	function popup_menu($menu_id = "")
	{
		$this->init("vcl/popup_menu");
		$this->read_template("js_popup_menu.tpl");

		if ($menu_id)
		{
			$this->begin_menu($menu_id);
		}
	}

	/**

		@attrib name=begin_menu params=pos api=1

		@param menu_id required type=string
			String which identifies popup menu
		@comment
			Sets the popup menu's id and resets items array

		@examples
			$popup_menu = get_instance("vcl/popup_menu");
			$popup_menu->begin_menu("my_popup_menu");
	**/
	function begin_menu($menu_id)
	{
		$this->items = array();
		$this->menus = array();
		$this->menu_id = $menu_id;
	}

	/** Adds new item to popup menu

		@attrib name=add_item params=name api=1

		@param text required type=string
			Item's caption
		@param link required type=string
			Item's link
		@param parent optional type=string
			Submenu id. Created with popup_menu::add_sub_menu()
		@param action optional type=string default=""
			Action to submit on current 'changeform', takes precedence over $link if specified
		@param onclick optional type=string default=""
			What to do on click event. May contain double quotes. Must end with semicolon.
		@param href_id optional type=string default=""
			Element DOM identifier
		@param title optional type=string default=""
			A tooltip style title
		@param target optional type=bool default=false
			If true, link opens in new window
		@param disabled optional type=bool default=false
			Show an unusable menu item
		@param emphasized optional type=bool default=false
			Menu item visual emphasis
		@param confirm optional type=string default=""
			Yes/no question to show user for confirmation

		@comment
			adds new item to popup menu
		@examples
			$popup_menu = new popup_menu();
			$popup_menu->begin_menu("my_popup_menu");
			$popup_menu->add_item(array(
					"text" => t("Valik"),
					"link" => 'http://www.neti.ee/'
			));
	**/
	function add_item($arr)
	{
		if (empty($arr["parent"]))
		{
			$arr["parent"] = $this->menu_id;
		}

		$caption = empty($arr["text"]) ? "[MENU ITEM]" : $arr["text"];
		$attribs = array("class" => "menuItem", "caption" => $caption);
		if (!empty($arr["href_id"])) $attribs["id"] = $arr["href_id"]; // DOM id
		if (!empty($arr["title"])) $attribs["title"] = $arr["title"]; // link title

		if(empty($arr["disabled"]))
		{
			// href
			if (!empty($arr["action"]))
			{
				$attribs["url"] = "javascript:submit_changeform('{$arr["action"]}');";
			}
			elseif (!empty($arr["link"]))
			{
				$attribs["url"] = $arr["link"];
			}
			else
			{
				$attribs["url"] = "javascript:void(0);";
			}

			// onclick
			$attribs["onclick"] = "";
			if (!empty($arr["onClick"])) $attribs["onclick"] = $arr["onClick"];
			if (!empty($arr["onclick"])) $attribs["onclick"] = $arr["onclick"];

			// target window/frame
			if (!empty($arr["target"])) $attribs["target"] = "_blank";

			// confirmation dialog
			if (!empty($arr["confirm"])) $attribs["onclick"] .= "return confirm(\"{$arr["confirm"]}\");";

			$style = "";
		}
		else
		{
			$attribs["url"] = "javascript:void(0);";
			$style = "color: gray; text-decoration: none; cursor: default;";
		}

		if (!empty($arr["emphasized"])) $style .= "background-color: silver;"; // emphasis

		$attribs["style"] = $style;

		$rv = html::href($attribs);

		if (!isset($this->menus[$arr["parent"]]))
		{
			$this->menus[$arr["parent"]] = "";
		}

		$this->menus[$arr["parent"]] .= $rv;
	}

	/** adds a separator line between menu isems that can not be clicked
		@attrib api=1
	**/
	function add_separator($arr = array())
	{
		//$this->items[] = array("__is_sep" => 1);
		if (empty($arr["parent"]))
		{
			$arr["parent"] = $this->menu_id;
		}

		if (!isset($this->menus[$arr["parent"]]))
		{
			$this->menus[$arr["parent"]] = "";
		}

		$this->menus[$arr["parent"]] .= '<div class="menuItemSep"></div>'."\n";
	}

	/** adds an item to the given menu that opens up a new sub-menu
		@attrib api=1

		@param parent optional type=string
			The internal name of the submenu, use to make sub-sub-..-menus

		@param text required type=string
			The text to display for the sub menu

		@param name required type=string
			The id of the menu
	**/
	function add_sub_menu($arr)
	{
		if (empty($arr["parent"]))
		{
			$arr["parent"] = $this->menu_id;
		}
		$arr["sub_menu_id"] = $arr["name"];
		$baseurl = $this->cfg["baseurl"];
		$rv = '<a class="menuItem" href="" onclick="return false;"
			        onmouseover="menuItemMouseover(event, \''.$arr["sub_menu_id"].'\');">
				<span class="menuItemText">'.$arr["text"].'</span>
				<span class="menuItemArrow"><img style="border:0px" src="'.$baseurl.
				'/automatweb/images/arr.gif" alt=""></span></a>';

		if (isset($this->menus[$arr["parent"]]))
		{
			$this->menus[$arr["parent"]] .= $rv;
		}
		else
		{
			$this->menus[$arr["parent"]] = $rv;
		}
	}

	/** Returns the HTML of the popup menu

		@attrib name=get_menu params=name api=1

		@param icon optional type=string
			Icon image name

		@param text optional type=string
			If you wish to display text instead of an icon as the menu button, then put it here

		@param load_on_demand_url optional type=string
			Setting this triggers the load on demand functionality. When the user clicks the icon, this url is fetched and it must return the menu content.

		@param is_toolbar optional type=bool
			If this is used from a toolbar button load on demand function, then you should set this to make things look alright

		@comment
			returns the html source of the popup menu
		@examples
                        $popup_menu = get_instance("vcl/popup_menu");
                        $popup_menu->begin_menu("my_popup_menu");
                        $popup_menu->add_item(array(
                                "text" => t("Valik"),
                                "link" => 'http://www.neti.ee'
                        ));
			echo $popup_menu->get_menu();
	**/
	function get_menu($param = NULL)
	{
		if (isset($param["icon"]) and substr($param["icon"], 0, 4) === "http")
		{
			$icon = $param["icon"];
		}
		else
		{
			$icon_name = empty($param["icon"]) ? "cog" : $param["icon"];
			$icon = icons::get_std_icon_url($icon_name);
		}


		$is = "";
		foreach($this->menus as $parent => $menudata)
		{
			$is .= '<div id="'.$parent.'" class="menu" onmouseover="menuMouseover(event)">'."\n${menudata}</div>\n";
		};
		$this->vars(array(
			"ss" => $is,
			"menu_id" => $this->menu_id,
			"menu_icon" => $icon,
			"alt" => isset($param["alt"]) ? $param["alt"] : null
		));

		if (!empty($param["text"]))
		{
			$this->vars(array(
				"text" => $param["text"]
			));
			$this->vars(array(
				"HAS_TEXT" => $href_ct = $this->parse("HAS_TEXT")
			));
		}
		else
		{
			$this->vars(array(
				"HAS_ICON" => $href_ct = $this->parse("HAS_ICON")
			));
		}

		if (!empty($param["is_toolbar"]))
		{
			$this->vars(array(
				"IS_TOOLBAR" => $this->parse("IS_TOOLBAR")
			));
		}

		if (is_array($param) && !empty($param["load_on_demand_url"]))
		{
			static $lod_num;
			$lod_num++;
			return "<div id='lod_".$this->menu_id."'><a href='javascript:void(0);' onClick='tb_lod".$lod_num."()' class='nupp'>$href_ct</a></div>
			<script language=javascript>
			function tb_lod".$lod_num."()
			{
				el = document.getElementById(\"lod_".$this->menu_id."\");
				el.innerHTML=aw_get_url_contents(\"".$param["load_on_demand_url"]."\");
				nhr=document.getElementById(\"href_".$this->menu_id."\");
				if (document.createEvent) {evObj = document.createEvent(\"MouseEvents\");evObj.initEvent( \"click\", true, true );nhr.dispatchEvent(evObj);}
				else {
					nhr.fireEvent(\"onclick\");
				}
			}
			</script>
			";
		}

		return $this->parse();
	}
}
