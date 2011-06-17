<?php

class icons
{
	//classes that have special icon
	private static $icon_other_classes = array("promo_box","brother","conf_icon_other","conf_icon_programs","conf_icon_classes","conf_icon_ftypes","conf_icons","conf_jf","conf_users","conf_icon_import","conf_icon_db","homefolder","hf_groups");

	// file type extensions that have special icons. extension => icon
	private static $file_type_extensions = array(
		"ddoc" => "ddoc",
		"doc" => "doc",
		"dwf" => "dwf",
		"dwg" => "dwg",
		"gif" => "gif",
		"htm" => "htm",
		"html" => "html",
		"jpg" => "jpg",
		"jpeg" => "jpg",
		"jpe" => "jpg",
		"mht" => "mht",
		"pdf" => "pdf",
		"ppt" => "ppt",
		"rtf" => "rtf",
		"sxi" => "sxi",
		"sxw" => "sxw",
		"xls" => "xls",
		"flv" => "video",
		"mpg" => "video",
		"mpeg" => "video",
		"avi" => "video",
		"wmv" => "video",
		"mpe" => "video",
		"mp4" => "video",
		"mkv" => "video"
	);

	public static function get_std_icon_url($name)
	{
		return aw_ini_get("icons.server") . "{$name}.png";
	}

	/** returns the url for the icons for the given class / object name (for file objects)
		@attrib api=1 params=pos
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
			$extension = empty($pi["extension"]) ? "" : strtolower($pi["extension"]);
			$icon_file_url = isset(self::$file_type_extensions[$extension]) ? "ftype_" . self::$file_type_extensions[$extension] . ".gif" : "classes/class_{$clid}.gif";
		}
		elseif (in_array($clid, self::$icon_other_classes))
		{
			$icon_file_url = "iother_{$clid}.gif";
		}
		else
		{
			$sufix = $done ? "_done" : "";
			$icon_file_url = "classes/class_{$clid}{$sufix}.gif";
		}

		return aw_ini_get("icons.server") . $icon_file_url;
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
		return aw_ini_get("icons.server")."prog_".$fid.".gif";
	}

	/**
		@attrib params=pos api=1
		@param o required type=object
			The object whose icon you wanna get.
		@comment
			Locates the currect icon for given class and returns html image tag
		@returns string
			<img src="icon_url">
		@examples
			$ic = icons::get_icon(CL_MENU);
			// $ic contains
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
		@param name type=string
			Standard icon name
		@param alt type=string default=""
			Image alt text
		@comment
			Locates the icon for given name and returns html image tag
		@returns string
			<img src="icon_url">
	**/
	public static function get_std_icon($name, $alt = "")
	{
		return html::img(array(
			"url" => icons::get_std_icon_url($name),
			"alt" => $alt,
			"border" => 0
		));
	}

	/**
		@attrib params=pos api=1
		@param clid required type=int
			The class id which icon you wanna get.
		@comment
			Locates the currect icon for given class and returns html image tag
		@returns string
			<img src="icon_url">
		@examples
		$ic = icons::get_icon_tag(CL_MENU);
		// $ic contains
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
