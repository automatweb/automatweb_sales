<?php
/*
@classinfo  maintainer=kristo
*/
class popup_menu extends aw_template
{
	var $items = array();
	var $menus = array();
	var $menu_id;


	function popup_menu()
	{
		$this->init("vcl/popup_menu");
		$this->read_template("js_popup_menu.tpl");
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
		@param target optional type=string
			If true, link iopens in new window

		@comment
			adds new item to popup menu
		@examples
                        $popup_menu = get_instance("vcl/popup_menu");
                        $popup_menu->begin_menu("my_popup_menu");
                        $popup_menu->add_item(array(
                                "text" => t("Valik"),
                                "link" => 'http://www.neti.ee'
                        ));
	**/
	function add_item($arr)
	{
		if (empty($arr["parent"]))
		{
			$arr["parent"] = $this->menu_id;
		}

		global $mc_counter;
		$mc_counter++;
		if (!empty($arr["onClick"]))
		{
			$arr["onClick"] = " onClick=\"". $arr["onClick"] . "\"";
		}
		else
		{
			$arr["onClick"] = "";
		}

		if (!empty($arr["oncl"]))
		{
			$arr["onClick"] = $arr["oncl"];
		}

		if (isset($arr["link"]))
		{
			$arr["url"] = $arr["link"];
		}

		if (isset($arr["action"]))
		{
			$arr["url"] = "javascript:submit_changeform('$arr[action]');";
		}

		$id = "";
		if (!empty($arr["href_id"]))
		{
			$id = "id='$arr[href_id]'";
		}

		$target = "";
		if (!empty($arr["target"]))
		{
			$target = " target=\"_blank\" ";
		}
		$title = "";
		if(!empty($arr["title"]))
		{
			$title = $arr["title"];
		}

		if (empty($arr["disabled"]))
		{
			$rv ='<a '.$id.' class="menuItem" '.$target.' href="'.$arr["url"].'" '.$arr["onClick"].'>'.$arr["text"]."</a>\n";
		}
		else
		{
			$rv = '<a '.$id.' class="menuItem" '.$target.' href="" title="'.$title.'" onclick="return false;" style="color:gray">'.$arr["text"]."</a>\n";
		}

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

		$this->menus[$arr["parent"]] .= $rv;
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
		if (!isset($param["icon"]))
		{
			$icon = $this->cfg["baseurl"]."/automatweb/images/blue/obj_settings.gif";
		}
		else
		if (substr($param["icon"], 0, 4) == "http")
		{
			$icon = $param["icon"];
		}
		else
		{
			$icon = $this->cfg["baseurl"]."/automatweb/images/icons/".$param["icon"];
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
?>
