<?php
/**
A class to help manage template files.
**/
class templatemgr extends core
{
	function templatemgr()
	{
		$this->init("templatemgr");
	}

	/** Retrieves a list of document templates, defined in the templates table
		@attrib api=1 params=name

		@param type required type=int
			the type of templates to list (1 - lead template, 2 - long template)

		@param caption optional type=string
			the string for the default entry, defaults to "default"

		@param menu optional type=bool
			if set, default template is read from that menu, defaults to false

		@param def optional type=string
			default template filename

		@returns
			Array {0 => default caption, template_id => template name,... }

	**/
	function get_template_list($args = array())
	{
		if (!isset($args["caption"]))
		{
			$args["caption"] = "default";
		}

		$def = isset($args["def"]) ? $args["def"] : "";

		$type = (int) $args["type"];
		if (!empty($args["menu"]))
		{
			// find the template for that type for the menu
			if ($type == 1)
			{
				$def = $this->get_lead_template($args["menu"]);
			}
			else
			if ($type == 2)
			{
				$def = $this->get_long_template($args["menu"]);
			}
		}

		$q = "SELECT * FROM template WHERE type = $type ORDER BY id";
		$this->db_query($q);
		$result = array("0" => $args["caption"]);
		$dat = array();
		while($tpl = $this->db_next())
		{
			$dat[] = $tpl;
		}

		foreach($dat as $tpl)
		{
			if ( !file_exists(aw_ini_get("site_basedir").'templates/automatweb/documents/'.$tpl["filename"]) )
			{
				continue;
			}

			$result[$tpl["id"]] = $tpl["name"] . " (${tpl['filename']})";
			if ($tpl["filename"] === $def)
			{
				$def_n = $tpl["name"];
			}
		}

		if (!empty($def_n))
		{
			$result["0"] = t("Vaikimisi: ").$def_n;
		}

		return $result;
	}

	/** Returns document template file name for document template id
		@attrib api=1 params=name

		@param id required type=int
			The document template id to fetch the file name for

		@returns
			Template file name without path or null if not found

	**/
	function get_template_file_by_id($args = array())
	{
		$id = (int)$args["id"];

		static $cache = null;
		if ($cache === null)
		{
			$cache = array();
			$this->db_query("SELECT id,filename FROM template");
			while ($row = $this->db_next())
			{
				$cache[$row["id"]] = $row["filename"];
			}
		}
		return ifset($cache, $id);
	}

	/** returns a list of all template folders that are for this site
		@attrib name=get_template_folder_list params=name nologin="1"

		@comment
			return value is array, key is complete template folder path and value is the path, starting from the site basefolder
	**/
	function get_template_folder_list($arr)
	{
		extract($arr);
		$this->tplfolder_list = array(
			$this->cfg["tpldir"] => aw_ini_get("tpldir")
		);
		$this->_req_tplfolders(aw_ini_get("tpldir"));
		return $this->tplfolder_list;
	}

	private function _req_tplfolders($fld)
	{
		$cnt = 0;
		if ($dir = opendir($fld))
		{
			while (($file = readdir($dir)) !== false)
			{
				if (!($file === "." || $file === ".."))
				{
					$cf = $fld."/".$file;
					if (is_dir($cf))
					{
						$cnt++;
						$this->_req_tplfolders($cf);
						$this->tplfolder_list[$cf] = $cf;
					}
				}
			}
			closedir($dir);
		}
		return $cnt;
	}

	/** finds the full document template for the given menu
		@attrib api=1 params=pos

		@param section required type=int
			The menu to find the template for

		@comment
			if the template is not set for this menu, traverses the object tree upwards
			until it finds a menu for which it is set

		@returns
			Template file name without path
	**/
	function get_long_template($section)
	{
		if (empty($section))
		{
			return "plain.tpl";
		}
		$obj = new object($section);
		$clid = $obj->class_id();
		if ($clid == CL_PERIODIC_SECTION || $clid == CL_DOCUMENT)
		{
			$section = $obj->parent();
		}

		$template = "";

		$path = $obj->path();
		if (is_array($path))
		{
			$path = array_reverse($path);
			foreach($path as $path_item)
			{
				$tpl_view = $path_item->is_property("tpl_view") ? $path_item->prop("tpl_view") : null;
				if (empty($template) && is_oid($tpl_view) && ($section == $path_item->id() || !$path_item->prop("tpl_view_no_inherit")))
				{
					$template = $this->get_template_file_by_id(array("id" => $tpl_view));
				}
			}
		}

		if (empty($template))
		{
			$template = "plain.tpl";
		}

		return $template;
	}

	/** finds the lead document template for the given menu
		@attrib api=1 params=pos

		@param section required type=int
			The menu to find the template for

		@comment
			if the template is not set for this menu, traverses the object tree upwards
			until it finds a menu for which it is set

		@returns
			Template file name without path
	**/
	function get_lead_template($section)
	{
		$obj = new object($section);
		$path = $obj->path();
		$template = "";
		if (is_array($path))
		{
			$path = array_reverse($path);
			foreach($path as $path_item)
			{
				$tpl_lead = $path_item->prop("tpl_lead");
				if (empty($template) && is_oid($tpl_lead) && ($section == $path_item->id() || !$path_item->prop("tpl_lead_no_inherit")))
				{
					$template = $this->get_template_file_by_id(array("id" => $tpl_lead));
				}
			}
		}

		if (empty($template))
		{
			$template = "lead.tpl";
		}
		return $template;
	}

	/** returns an array of templates that are in template folder $folder, checks site side first, then admin
		@attrib api=1 params=name

		@param folder required type=string
			The template folder to list templates for

		@param empty optional type=boolean default=true
			If true, an empty choice will be added to the list.

		@returns
			array { template_file => template_file }
	**/
	function template_picker($arr)
	{
		$fp_site = aw_ini_get("site_tpldir").$arr["folder"];
		$fp_adm = aw_ini_get("basedir")."templates/".$arr["folder"];

		$ret = (!isset($arr["empty"]) or $arr["empty"]) ? array("" => "") : array();

		if (is_dir(aw_ini_get("tpldir").$arr["folder"]))
		{
			$dc = $this->get_directory(array(
				"dir" => aw_ini_get("tpldir").$arr["folder"]
			));
			foreach($dc as $file)
			{
				if (substr($file, -3) === "tpl")
				{
					$ret[$file] = $file;
				}
			}
		}

		if (is_dir($fp_site))
		{
			$dc = $this->get_directory(array(
				"dir" => $fp_site
			));
			foreach($dc as $file)
			{
				if (substr($file, -3) === "tpl")
				{
					$ret[$file] = $file;
				}
			}
		}

		if (count($ret) == 1)
		{
			$dc = $this->get_directory(array(
				"dir" => $fp_adm
			));
			foreach($dc as $file)
			{
				if (substr($file, -3) === "tpl")
				{
					$ret[$file] = $file;
				}
			}
		}

		return $ret;
	}

	/** List available template names for a template category/folder
		@attrib api=1 params=pos
		@param tpldir type=string
			Folder/category path in template bank. e.g. "automatweb/menuedit"
		@param folder type=array default=array("tpl", "php")
			Template types to list. Default is all types.
		@comment
			If multiple types requested, type/extension is appended, if only one type then template names list without extensions/type suffixes is returned.
		@returns array
		@errors
	**/
	public static function get_list($tpldir, $type = array("tpl", "php"))
	{
		$dir = realpath(AW_DIR . "templates/{$tpldir}");
		$templates = scandir($dir);
		$name_only = 1 === count($type);
		foreach ($templates as $key => $value)
		{
			if (!in_array(substr(strrchr($value, "."), 1), $type))
			{
				unset($templates[$key]);
			}
			elseif ($name_only)
			{
				$templates[$key] = substr($value, 0, strrpos($value, "."));
			}
		}
		return $templates;
	}
}
