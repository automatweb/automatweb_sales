<?php

class menuedit extends aw_template implements request_startup
{
	function menuedit()
	{
		$this->init("automatweb/menuedit");
	}

	function request_startup()
	{
		$section = aw_global_get("section");
		$rs = $section == "" ? aw_ini_get("frontpage") : $section;
		$rs = str_replace("/", "-", $rs);
		$rs = str_replace("\\", "-", $rs);
		aw_global_set("raw_section", $rs);

		if (strpos($section, ":") !== false)
		{
			$section = (int)$section;
		}

		$set_lang_id = false;
		$set_ct_lang_id = isset($_GET["set_ct_lang_id"]) ? $_GET["set_ct_lang_id"] : null;
		if (aw_ini_get("menuedit.language_in_url"))
		{
			if (strlen($section) > 2 && $section[2] == "%")
			{
				$section = urldecode($section);
			}
			if (strlen($section) == 2)
			{
				$tmp = languages::get_id_for_code($section);
				if ($tmp)
				{
					$fp = aw_ini_get("ini_frontpage");
					if (is_array($fp))
					{
						$fp = $fp[$tmp];
					}
					if (!$fp)
					{
						$fp = aw_ini_get("frontpage");
					}
					$section = $section."/".$fp;
				}
			}
			$tmp = explode("/", $section, 2);
			if (isset($tmp[1]))
			{
				$lc = $tmp[0];
				$section_a = $tmp[1];
			}
			else
			{
				$lc = $section;
				$section_a = "";
			}
			if (strlen($lc) > 2)
			{
				$section_a = $section;
				$lc = aw_global_get("ct_lang_lc");
			}
			if ($section_a == "" && substr($section, -1) != "/")
			{
				$lc = aw_global_get("ct_lang_lc");
				$section_a = $section;
			}
			else
			if ($section_a == "")
			{
				$section_a = aw_ini_get("frontpage");
			}

			if ($lc != "" && $section_a != "")
			{
				// switch to lang
				if (aw_ini_get("user_interface.full_content_trans"))
				{
					$set_ct_lang_id = languages::get_id_for_code($lc);
				}
				else
				{
					$set_lang_id = languages::get_id_for_code($lc);
				}
			}
			$section = $section_a;
		}

		$realsect = $this->check_section($section);
		if ($this->can("view",$realsect))
		{
			$_obj = obj($realsect);
			// if the section is a menu and has a link, then redirect the user to that link
			if ($_obj->class_id() == CL_MENU && $_obj->prop("link") != "" && $_obj->id() != aw_ini_get("frontpage"))
			{
				if (strpos($_obj->trans_get_val("link"), $_obj->id()) === false &&
					!($realsect == aw_ini_get("frontpage") 	&& $_obj->trans_get_val("link") === "/")
					&& empty($_GET["class"])
					&& $_obj->trans_get_val("link") != $_obj->alias()
				)
				{
					$ls = $_obj->trans_get_val("link");
					if ($ls{0} === "/")
					{
						$ls = aw_ini_get("baseurl").substr($ls, 1);
					}
					elseif (strpos($ls, "://") === false)
					{
						$ls = "/".$ls;
					}

					if (get_ru() != $ls)
					{
						header("Location: ".$ls);
						die();
					}
				}
			}

			$class_id = $_obj->class_id();

			if ($class_id == CL_MENU)
			{
				if (!($_obj->prop("type") == MN_CLIENT) && !$set_lang_id && !$set_ct_lang_id )
				{
					$set_lang_id = $_obj->lang_id();
				};
			}
			else
			if ($class_id != CL_EXTLINK)
			{
				if ($class_id == CL_DOCUMENT && $this->can("view", $_obj->parent()))
				{
					$pt = obj($_obj->parent());
					if (!($pt->prop("content_all_langs") && $pt->prop("type") == MN_CLIENT) && !$set_lang_id && !$set_ct_lang_id)
					{
						$set_lang_id = $_obj->lang_id();
					}
				}
				else
				if (!$set_ct_lang_id)
				{
					$set_lang_id = $_obj->lang_id();
				}

				// we do document hit count logging here, because
				// we know if it's a document or not here
				if (1 == aw_ini_get("document_statistics.use") && $realsect != aw_ini_get("frontpage") && ($class_id == CL_DOCUMENT || $class_id == CL_BROTHER_DOCUMENT || $class_id == CL_PERIODIC_SECTION))
				{
					$dt = get_instance(CL_DOCUMENT_STATISTICS);
					$dt->add_hit($realsect);
				}
			}
		}

		if ($set_ct_lang_id)
		{
			$_SESSION["ct_lang_id"] = $set_ct_lang_id;
			$_SESSION["ct_lang_lc"] = languages::get_langid($set_ct_lang_id);
			aw_global_set("ct_lang_lc", $_SESSION["ct_lang_lc"]);
			aw_global_set("ct_lang_id", $_SESSION["ct_lang_id"]);
			//$_COOKIE["ct_lang_id"] = $set_ct_lang_id;
			//$_COOKIE["ct_lang_lc"] = $_SESSION["ct_lang_lc"];
			setcookie("ct_lang_id", $set_ct_lang_id, time() + 3600, "/");
			setcookie("ct_lang_lc", $_SESSION["ct_lang_lc"], time() + 3600, "/");
		}

		if ($set_lang_id && aw_global_get("lang_id") != $set_lang_id)
		{
			if (!languages::set_active($set_lang_id))
			{
				$realsect = $this->cfg["frontpage"];
			}
			else
			{
				$GLOBALS["objects"] = array();
				// we must reset the objcache here, because
				// it already contains the section obj
				// and after the language switch it contains the old language
				// anyway, tyhis does not add much overhead,
				// because here we should only have the section object loaded
			}
			if (is_array(aw_ini_get("ini_frontpage")))
        		{
				$tmp = aw_ini_get("ini_frontpage");
				$GLOBALS["cfg"]["frontpage"] = $tmp[aw_global_get("lang_id")];
			}
		}
		aw_global_set("section",$realsect);
	}

	function check_section($section, $show_errors = true)
	{
		// check frontpage - if it is array, pick the correct one from the language
		$frontpage = aw_ini_get("frontpage");
		if (is_array($frontpage))
		{
			$frontpage = $frontpage[aw_global_get("lang_id")];
			aw_ini_set("frontpage",$frontpage);
			$this->cfg["frontpage"] = $frontpage;
		}

		// cut the / from the end
		// so that http://site/alias and http://site/alias/ both work
		if (substr($section,-1) === "/")
		{
			$section = substr($section,0,-1);
		};

		// if the baseurl is site.ee/foo/bla, then cut that out from section
		$bits = parse_url(aw_ini_get("baseurl"));
		if (!empty($bits["path"]))
		{
			$section = str_replace(substr($bits["path"], 1), "", $section);
		}

		if (empty($section))
		{
			$ret = $frontpage < 1 ? 1 : $frontpage;

			if (!headers_sent())
			{
				header("X-AW-Section: ".$frontpage);
			}
			return $ret;
		}

		if ("reforb".AW_FILE_EXT === $section)
		{
			if (automatweb::$request->arg_isset("section"))
			{
				$section = automatweb::$request->arg("section");
			}
			else
			{
				$section = $frontpage;
			}
		}

		if ($section === 'favicon.ico')
		{
			// if user requested favicon, then just show the thing here and be done with it
			$c = get_instance("config");
			$c->show_favicon(array());
		}

		if ($section === 'sitemap.gz')
		{
			// if user requested favicon, then just show the thing here and be done with it
			$c = get_instance(CL_MENU);
			$c->get_sitemap();
		}

		if($section === 'robots.txt')
		{
			echo "Sitemap: ".aw_ini_get("baseurl")."sitemap.gz";
			die();
		}

		// sektsioon ei olnud numbriline
		if (!is_oid($section))
		{
			if ($this->cfg['recursive_aliases'])
			{
				// first I have to check whether the alias contains /-s and if so, split
				// the url into pieces
				$sections = explode("/",$section);

				// if it contains a single $section, it is now located in $sections[0]

				$candidates = array();
				$last = array_pop($sections);

				// well, I think I have a better idea .. I'll start from the last item
				// calculate all possible aliases and then select one
				$flt = array(
					"alias" => $last,
					//"status" => STAT_ACTIVE,
					"site_id" => aw_ini_get("site_id"),
					"lang_id" => array(),
				);
				if (aw_ini_get("ini_rootmenu"))
				{
					$tmp = aw_ini_get("rootmenu");
					aw_ini_set("rootmenu", aw_ini_get("ini_rootmenu"));
				}

				$clist = new object_list($flt);
				for($check_obj = $clist->begin(); !$clist->end(); $check_obj = $clist->next())
				{
					if($check_obj->prop("short_alias"))//shortcut
					{
						$obj = $check_obj;
						continue;
					}
					// put it in correct order and remove the first element (object itself)
					$path = array_reverse($check_obj->path());
					$curr_id = $check_obj->id();
					$candidates[$curr_id] = "";

					$stop = false;

					foreach($path as $path_obj)
					{
						if (!$stop)
						{
							$alias = $path_obj->alias();
							if (strlen($alias) > 0)
							{
								$candidates[$curr_id] = $alias . "/" . $candidates[$curr_id];
							}
							else
							{
								$stop = true;
							}
						}
					}
				}

				if (aw_ini_get("ini_rootmenu"))
				{
					aw_ini_set("rootmenu", $tmp);
				}

				if(empty($obj))//edasi oleks nagu ainult selle sektsioooni objekti otsimine, seda pole vaja kui see olemas
				{

					// Viskame v2lja need, mis niikuinii ei sobi. N2iteks juht, kus yks alias on t6lgitud, teine mitte - http://ekt.einst.ee/en/cultural-heritage/muuseumid
					//			-kaarel 6.02.2009
					foreach($candidates as $cand_id => $cand_path)
					{
						$path_for_obj = substr($cand_path,0,-1);
						if ($path_for_obj != $section)
						{
							unset($candidates[$cand_id]);
						}
					}


					if (aw_ini_get("user_interface.full_content_trans") && !count($candidates))
					{
						// do the same check for the translated aliases
						$this->quote($last);
						$lang_id = aw_global_get("ct_lang_id");
						$this->quote($lang_id);
						$rows = $this->db_fetch_array("SELECT menu_id FROM aw_alias_trans WHERE alias = '$last' AND lang_id = '$lang_id'");
						$ol = new object_list(array(
							"alias" => $last,
							//"status" => STAT_ACTIVE,
							"site_id" => aw_ini_get("site_id")
						));
						foreach($ol->ids() as $id)
						{
							$rows[] = array("menu_id" => $id);
						}
						foreach ($rows as $row)
						{
							if (!$this->can("view", $row["menu_id"]))
							{
								continue;
							}
							$check_obj = obj($row["menu_id"]);
							// put it in correct order and remove the first element (object itself)
							$path = array_reverse($check_obj->path());
							$curr_id = $check_obj->id();
							$candidates[$curr_id] = "";

							$stop = false;

							foreach($path as $path_obj)
							{
								if (!$stop)
								{
									$alias = $this->db_fetch_field("SELECT alias FROM aw_alias_trans WHERE lang_id = $lang_id AND menu_id =".$path_obj->id(), "alias");
									if ($alias == null || !$path_obj->meta("trans_".$lang_id."_status"))
									{
										$alias = $path_obj->alias();
									}
									if (strlen($alias) > 0)
									{
										$candidates[$curr_id] = $alias . "/" . $candidates[$curr_id];
									}
									else
									{
										$stop = true;
									}
								}
							}
						}
					}

					foreach($candidates as $cand_id => $cand_path)
					{
						$path_for_obj = substr($cand_path,0,-1);
						if ($path_for_obj == $section)
						{
							if (!empty($obj))
							{
								$tmp = obj($cand_id);
								if ($tmp->ord() < $obj->ord())
								{
									continue;
								}
							}

							$obj = new object($cand_id);
							if ($obj->id() != $obj->brother_of() && $this->can("view", $obj->brother_of()))
							{
								$obj = obj($obj->brother_of());
							}
						}
					}
				}
			}
			else
			{
				// vaatame, kas selle nimega aliast on?
				$this->quote($section);
				$ol = new object_list(array(
					"alias" => $section,
					//"status" => STAT_ACTIVE,
					"site_id" => aw_ini_get("site_id"),
					"lang_id" => array()
				));
				if ($ol->count() < 1)
				{
					$lang_id = aw_ini_get("user_interface.full_content_trans") ? aw_global_get("ct_lang_id") : aw_global_get("lang_id");
					// check translations
					$this->quote($section);
					$menu_id = null;
					if (aw_ini_get("user_interface.full_content_trans"))
					{
						$menu_id = $this->db_fetch_field("SELECT menu_id FROM aw_alias_trans WHERE lang_id = $lang_id AND alias = '$section'", "menu_id");
					}
					elseif (aw_ini_get("menuedit.login_on_no_access") == 1)
					{
						$row = $this->db_fetch_row("SELECT oid,status FROM objects WHERE lang_id = $lang_id AND alias = '$section'");
						if ($row && $row["status"] > 1)
						{
							// try login, just in case this is protected
							auth_config::redir_to_login();
						}
					}

					if ($this->can("view", $menu_id))
					{
						$obj = obj($menu_id);
					}
					else
					{
						$obj = false;
					}
				}
				else
				{
					$obj = $ol->begin();
				}
			}

			// nope. mingi skriptitatikas? voi cal6
			// inside joked ruulivad exole duke ;)
			// nendele kes aru ei saanud - cal6 ehk siis kalle volkov - ehk siis okia tyyp
			// oli esimene kes aw seest kala leidis - kui urli panna miski oid, mida polnud, siis asi hangus - see oli siis kui
			// www.struktuur.ee esimest korda v2lja tuli.
			// niiet nyyd te siis teate ;)
			// - terryf
			if (empty($obj))
			{
				if ($show_errors)
				{
					$this->do_error_redir($section);
				}
				else
				{
					return false;
				}
			}
			else
			{
				$section = $obj->id();
			}
		}
		else
		{
			// mingi kontroll, et kui sektsioon ei eksisteeri, siis n?tame esilehte
			if (!$this->can("view", $section))
			{
				$ns = $_SERVER["REQUEST_URI"];
				if ($show_errors)
				{
					if (aw_ini_get("menuedit.login_on_no_access") == 1)
					{
						classload("core/users/auth/auth_config");
						auth_config::redir_to_login();
					}
					else
					{
						$this->do_error_redir($section);
					}
				}
				else
				{
					$section = $frontpage;
				}
			}
			else
			{
				$o = obj($section);
				if ($o->site_id() != aw_ini_get("site_id") && aw_global_get("uid") == "" && aw_ini_get("menuedit.objects_from_other_sites") != 1)
				{
					if ($show_errors)
					{
						$this->do_error_redir($section);
					}
					else
					{
						$section = $frontpage;
					}
				}
			}
		}

		if (!$section)
		{
			$section = aw_ini_get("frontpage");
		}

		if (!headers_sent())
		{
			header("X-AW-Section: ".$section);
		}

		return $section;
	}

	/** Redirects the user to the error page for a non-existing page
		@attrib api=1 params=pos

		@param section required type=string
			The section (address) the user tried to access that does not exist

		@comment
			Checks url-replacement tables and old url relocation tables and if found, redirects to the correct url. if not, then tries to find the error page from the ini file and if not found. just prints 404 error message.
			Also terminates execution.
	**/
	function do_error_redir($section)
	{
		// check site config
		$pl = new object_list(array(
			"class_id" => CL_CONFIG_OLD_REDIRECT,
			"flags" => array(
				"mask" => OBJ_FLAG_IS_SELECTED,
				"flags" => OBJ_FLAG_IS_SELECTED
			)
		));
		$gr = aw_global_get("REQUEST_URI");
		$repl_gr = $gr;
		$repls = false;
		foreach($pl->arr() as $item)
		{
			foreach(safe_array($item->meta("repl")) as $row)
			{
				if (strpos($repl_gr, $row["with"]) !== false)
				{
					$repls = true;
					$repl_gr = str_replace($row["with"], $row["what"], $repl_gr);
				}
			}
			foreach(safe_array($item->meta("d")) as $row)
			{
				if ("/".$row["old"] == $gr)
				{
					header("HTTP/1.0 301 Moved Permanently");
					if (substr($row["new"], 0, 4) === "http")
					{
						header("Location: ".$row["new"]);
					}
					else
					{
						header("Location: ".aw_ini_get("baseurl").$row["new"]);
					}
					die();
				}
			}
		}

		if ($repls)
		{
			header("HTTP/1.0 301 Moved Permanently");
			if (substr($repl_gr, -1) === "/")
			{
				$repl_gr = substr($repl_gr, 0, -1);
			}
			header("Location: ".aw_ini_get("baseurl").$repl_gr);
			die();
		}
		$si = __get_site_instance();
		if (is_object($si) && method_exists($si, "handle_error_redir"))
		{
			$tmp = $si->handle_error_redir($section);
			if ($tmp != "")
			{
				header("Location: $tmp");
				die();
			}
		}

		// neat :), kui objekti ei leita, siis saadame 404 koodi
		$r404 = aw_ini_get("menuedit.404redir");
		if (is_array($r404))
		{
			if (aw_ini_get("user_interface.full_content_trans"))
			{
				$r404 = $r404[aw_global_get("ct_lang_lc")];
				if ($r404 == "")
				{
					$r404 = $r404["en"];
				}
			}
			else
			{
				$r404 = $r404[aw_global_get("lang_id")];
			}
		}

		if ($r404 && "/".$GLOBALS["section"] != $r404)
		{
			header("Location: " . $r404);
		}
		else
		{
			header ("HTTP/1.1 404 Not Found");
			printf(t("<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\"> <html><head><title>404</title><meta name=\"robots\" content=\"noindex, nofollow\"></head><body><h1>404 Sellist sektsiooni pole</h1></body></html>"));
		}
		exit;
	}
}
