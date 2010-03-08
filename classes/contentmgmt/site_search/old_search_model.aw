<?php
/*
@classinfo  maintainer=kristo
*/

class old_search_model extends aw_template implements main_subtemplate_handler
{
	function old_search_model()
	{
		$this->init();
	}

	function on_get_subtemplate_content($arr)
	{
		$id = $arr["inst"]->section_obj->id();
		$def = isset($_GET["parent"]) ? $_GET["parent"] : $id;
		$sl = $this->get_search_list(&$def);
		$arr["inst"]->vars(array(
			"search_sel" => $this->option_list($def,$sl),
			"section" => $id,
			"str" => htmlentities(isset($_GET["str"]) ? $_GET["str"] : "")
		));
		$arr["inst"]->vars(array(
			"SEARCH_SEL" => $arr["inst"]->parse("SEARCH_SEL")
		));
	}

	function get_search_list(&$default)
	{
		$grps = $this->get_groups();
		$ret = array();
		foreach($grps as $grpid => $gdata)
		{
			if (aw_global_get("uid") != "" || $gdata["users_only"] != 1)
			{
				if (is_array($gdata["menus"]))
				{
					foreach($gdata["menus"] as $mn1 => $mn2)
					{
						if ($mn1 == $default)
						{
							$def = $grpid;
						}
					}
				};
				$ret[$grpid] = $gdata["name"];
			}
		}
		return $ret;
	}

	function get_groups($no_strip = false)
	{
		$cache = get_instance("cache");
		$cs = $cache->file_get("search_groups-".$this->cfg["site_id"]);
		if ($cs)
		{
			$ret = aw_unserialize($cs);
		}
		else
		{
			$dat = $this->get_cval("search_grps");
			$ret = aw_unserialize($dat);
			$cache->file_set("search_groups-".$this->cfg["site_id"],aw_serialize($ret));
		};

		if ($no_strip)
		{
			$r = $ret;
		}
		else
		{
			$r = $ret[aw_ini_get("site_id")][aw_global_get("lang_id")];
		}

		if (!is_array($r))
		{
			return array();
		}
		else
		{
			return $r;
		}
	}
}
?>