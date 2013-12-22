<?php
class site_loginmenu extends class_base
{
	var $current_login_menu_id;

	function site_loginmenu()
	{
		$this->init(array(
			"tpldir" => "contentmgmt/site_loginmenu",
			"clid" => CL_SITE_LOGINMENU
		));
	}

	function get_site_loginmenu($that)
	{
		if (aw_global_get("uid") == "" || !get_instance("config_login_menus")->show_login_menu())
		{
			return "";
		}
		$out = "";
		$this->read_template("site_loginmenu.tpl");
		$ss = get_instance("contentmgmt/site_show");
		$login_menu_id = $ss->_helper_get_login_menu_id();
		$ol = new object_list(array(
			"class_id" => CL_MENU,
			"parent" => $login_menu_id,
			"lang_id" => AW_REQUEST_CT_LANG_ID,
		));
		$tmp_L1 = "";
		for($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			$ol2 = new object_list(array(
				"class_id" => CL_MENU,
				"parent" => $o->id(),
				"status" => 2,
				"lang_id" => AW_REQUEST_CT_LANG_ID,
			));

			$tmp_L2 = "";
			for($o2 = $ol2->begin(); !$ol2->end(); $o2 = $ol2->next())
			{
				$link = str_replace  ("&","&amp;", $that->make_menu_link($o2));
				if (strlen($link)>0)
				{
					$this->vars(array(
						"text" => $o2->name(),
						"link" => $link
					));
					if (strlen($tmp_L2)==0 && $this->is_template("MENU_LOGGED_L2_ITEM_BEGIN"))
					{
						$tmp_L2 .= $this->parse("MENU_LOGGED_L2_ITEM_BEGIN");
					}
					else
					{
						$tmp_L2 .= $this->parse("MENU_LOGGED_L2_ITEM");
					}
				}
			}
			$this->vars(array(
				"MENU_LOGGED_L2_ITEM" => $tmp_L2,
			));
			if (strlen($tmp_L1)==0 && $this->is_template("MENU_LOGGED_L1_ITEM_BEGIN"))
			{
				$tmp_L1 .= $this->parse("MENU_LOGGED_L1_ITEM_BEGIN");
			}
			else
			{
				$tmp_L1 .= $this->parse("MENU_LOGGED_L1_ITEM");
			}
		}
		$this->vars(array(
			"MENU_LOGGED_L1_ITEM" => $tmp_L1,
		));

		return $this->parse();
	}

}

?>
