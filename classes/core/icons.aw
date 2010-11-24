<?php

class icons
{
	public static function get_std_icon_url($name)
	{
		return aw_ini_get("baseurl") . "/automatweb/images/icons/{$name}.gif";
	}

	/** returns the url for the icons for the given class / object name (for file objects)

		@attrib api=1

		@comment
			arg1 - class id / object class instance
			name - object name
	**/
	public static function get_icon_url($arg1, $name = "")
	{
		$done = false;

		if (is_object($arg1))
		{
			$clid = $arg1->class_id();
			$done = $arg1->flags() & OBJ_IS_DONE;
		}
		else
		{
			$clid = $arg1;
		}

		if ($clid == CL_FILE)
		{
			if (empty($name) and is_object($arg1))
			{
				$name = $arg1->name();
			}

			$pi = pathinfo($name);
			if (!isset($pi["extension"]))
			{
				$pi["extension"] = null;
			}
			$icon_url = aw_ini_get("icons.server")."/ftype_".strtolower($pi["extension"]).".gif";
			return $icon_url;
			// return aw_ini_get("icons.server")."/ftype_".strtolower($pi["extension"]).".gif";
		}
		else
		if (in_array($clid,array("promo_box","brother","conf_icon_other","conf_icon_programs","conf_icon_classes","conf_icon_ftypes","conf_icons","conf_jf","conf_users","conf_icon_import","conf_icon_db","homefolder","hf_groups")))
		{
			return aw_ini_get("icons.server")."/iother_".$clid.".gif";
		}
		else
		{
			$sufix = !empty($done) ? "_done" : "";
			return aw_ini_get("icons.server")."/class_".$clid.$sufix.".gif";
		}
	}

	/**
		@attrib params=pos api=1
		@param fid required type=int
		Feature id which icon to search

		@comment
		Returns url to required features icon
		@returns
		url to required icon
	**/
	public static function get_feature_icon_url($fid)
	{
		return aw_ini_get("icons.server")."/prog_".$fid.".gif";
	}

	/**
		@attrib params=pos api=1
		@param clid required type=int
		The class id which icon you wanna get.

		@comment
		Locates the correct icon for given class and returns html image tag
		@returns
		<img src="corrent iconurl">
		@examples
		classload("core/icons");
		$ic = icons::get_icon(CL_MENU);

		// $ic conains
		// <img src='http://_blabla_/automatweb/images/icons/class_1.gif'>
	**/
	public static function get_icon($o)
	{
		if (!is_object($o) && object_loader::can("view", $o))
		{
			$o = obj($o);
		}
		return html::img(array(
			"url" => icons::get_icon_url($o),
			"alt" => sprintf(t("Objekti id on %s"), $o->id()),
			"title" => sprintf(t("Objekti id on %s"), $o->id()),
			"border" => 0
		));
	}

	/**
		@attrib params=pos api=1
		@param clid required type=int
		The class id which icon you wanna get.

		@comment
		Locates the correct icon for given class and returns html image tag
		@returns
		<img src="corrent iconurl">
		@examples
		classload("core/icons");
		$ic = icons::get_icon_tag(CL_MENU);

		// $ic conains
		// <img src='http://_blabla_/automatweb/images/icons/class_1.gif'>
	**/
	public static function get_class_icon($clid)
	{
		return html::img(array(
			"url" => icons::get_icon_url($clid),
			"border" => 0
		));
	}
}
