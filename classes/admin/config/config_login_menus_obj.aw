<?php

class config_login_menus_obj extends _int_object
{
	const CLID = 251;

	function delete($full_delete = false)
	{
		// if this is the active object then delete from config table as well
		$is_act = $this->flag(OBJ_FLAG_IS_SELECTED);
		parent::delete($full_delete);
		if ($is_act)
		{
			config::set_simple_config("login_menus_".aw_ini_get("site_id"), "");
		}
	}
}
