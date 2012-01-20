<?php

class menu_obj extends _int_object
{
	const CLID = 1;

	const TYPE_CLIENT = 69;
	// sisurubriik
	const TYPE_CONTENT = 70;
	// adminni ylemine menyy
	const TYPE_ADMIN1 = 71;
	const TYPE_PROMO_BOX = 73;
	const TYPE_HOME_FOLDER = 74;
	// kodukataloogi alla tehtud kataloog, et sharetud katalooge olex lihtsam n2idata
	const TYPE_HOME_FOLDER_SUB = 75;
	// formi element, mis on samas ka menyy
	const TYPE_FORM_ELEMENT = 76;
	// public method
	const TYPE_PMETHOD = 77;

	/** Recusively sets status for this and all subfolders. Saves object if status parameter given
		@attrib api=1 params=pos
		@param value type=int default=NULL
			One of object::STAT_* constants. If value is null, current status value will be set for all subfolders and object will not be saved.
		@returns bool
		@errors
			throws awex_obj_state if object not saved
	**/
	public function set_status_recursive($value = null)
	{
		$this->require_state("saved");
		$r = true;

		// determine value to be set and whether to modify and save this object
		if (null === $value)
		{
			$dont_save_this = true;
			$value = $this->status();
		}
		else
		{
			$dont_save_this = false;
		}

		try
		{
			$subfolders = new object_tree(array(
				"class_id" => CL_MENU,
				"parent" => $this->id()
			));

			if ($dont_save_this)
			{ // convert to list and remove this object
				$subfolders = $subfolders->to_list();
				$subfolders->remove($this->id());
			}

			// objtree and objlist have the same interface
			$subfolders->foreach_o(array(
				"func" => "set_status",
				"params" => array($value),
				"save" => true
			));
		}
		catch (Exception $e)
		{
			$r = false;
		}

		return $r;
	}

	/**
		@attrib api=1 params=pos
		@returns bool
		@errors none
	**/
	public function is_frontpage()
	{
		return aw_ini_get("frontpage") == $this->id();
	}

	/**
		@attrib api=1 params=pos
		@returns CL_MENU
	**/
	public static function get_active_section_id()
	{
		static $section = false;

		if (false === $section)
		{
			$section = null;
			$path_info = empty($_SERVER["PATH_INFO"]) ? "" : trim(preg_replace("|\?automatweb=[^&]*|","", $_SERVER["PATH_INFO"]));
			$query_string = empty($_SERVER["QUERY_STRING"]) ? "" : trim(preg_replace("|\?automatweb=[^&]*|","", $_SERVER["QUERY_STRING"]));
			$request_uri = empty($_SERVER["REQUEST_URI"]) ? "" : trim(preg_replace("|\?automatweb=[^&]*|","", $_SERVER["REQUEST_URI"]));

			if (($query_string === "" && $path_info === "") && $request_uri !== "")
			{
				$query_string = $request_uri;
				$query_string = str_replace(array("xmlrpc.aw", "index.aw", "orb.aw", "login.aw", "reforb.aw"), "", $query_string);
			}

			if (strlen($query_string) > 1)
			{
				$path_info .= ("/" === $query_string{0}) ? $query_string : "?{$query_string}";
				// for nginx aw configuration, to make queries like orb.aw/class=file/... work, the
				// question mark is not prepended. apache finds a file orb.aw and passes rest of
				// the url to it. nginx looks for a local file with that whole name, therefore a rewrite
				// is used and everything after .aw is passed as query string: ?/class=file...
			}

			if (substr($path_info, 0, 12) === "/class=image" or substr($path_info, 0, 11) === "/class=file" or substr($path_info, 0, 15) === "/class=flv_file")
			{
				$path_info = substr(str_replace(array("/", "?"), "&", $path_info), 1);
				parse_str($path_info, $_GET);
			}
			else
			{
				$_SERVER["REQUEST_URI"] = $request_uri;

				if (strlen($path_info) > 1)
				{
					if (($_pos = strpos($path_info, "section=")) === false)
					{
						// ok, we need to check if section is followed by = then it is not really the section but
						// for instance index.aw/set_lang_id=1
						// we check for that like this:
						// if there are no / or ? chars before = then we don't prepend

						$qpos = strpos($path_info, "?");
						$slpos = strpos($path_info, "/");
						$eqpos = strpos($path_info, "=");
						$qpos = $qpos ? $qpos : 20000000;
						$slpos = $slpos ? $slpos : 20000000;

						if (!$eqpos || ($eqpos > $qpos || $slpos > $qpos))
						{
							// if no section is in url, we assume that it is the first part of the url and so prepend section = to it
							$path_info = str_replace("?", "&", "section=".substr($path_info, 1));
						}
					}

					// support for links like http://bla/index.aw?291?lcb=117 ?424242?view=3&date=20
					// this is a quick fix for a specific problem on june 22th 2010 with opera.ee site
					// might have been a configuration error, for increase of tolerance in that case then
					if (preg_match("/^\\?([0-9]+)\\?/", $path_info, $section_info))
					{
						$section = $section_info[1];
					}

					if (($_pos = strpos($path_info, "section=")) !== false)
					{
						// this here adds support for links like http://bla/index.aw/section=291/lcb=117
						$t_pi = substr($path_info, $_pos+strlen("section="));
						if (($_eqp = strpos($t_pi, "="))!== false)
						{
							$t_pi = substr($t_pi, 0, $_eqp);
							$_tpos1 = strpos($t_pi, "?");
							$_tpos2 = strpos($t_pi, "&");
							if ($_tpos1 !== false || $_tpos2 !== false)
							{
								// if the thing contains ? or & , then section is the part before it
								if ($_tpos1 === false)
								{
									$_tpos = $_tpos2;
								}
								elseif ($_tpos2 === false)
								{
									$_tpos = $_tpos1;
								}
								else
								{
									$_tpos = min($_tpos1, $_tpos2);
								}
								$section = substr($t_pi, 0, $_tpos);
							}
							else
							{
								// if not, then te section is the part upto the last /
								$_lslp = strrpos($t_pi, "/");
								if ($_lslp !== false)
								{
									$section = substr($t_pi, 0, $_lslp);
								}
								else
								{
									$section = $t_pi;
								}
							}
						}
						else
						{
							$section = $t_pi;
						}
					}

					//
					if (aw_ini_get("menuedit.language_in_url"))
					{
						$section = preg_replace("|^" . AW_REQUEST_CT_LANG_CODE . "/|", "", $section);
					}
				}
				elseif ("/" === $path_info or empty($path_info))
				{
					$section = aw_ini_get("frontpage");
				}
			}
		}

		return $section;
	}
}
