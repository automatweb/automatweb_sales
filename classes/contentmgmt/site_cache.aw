<?php
/*
@classinfo  maintainer=kristo
*/

class site_cache extends aw_template
{
	function site_cache()
	{
		$this->init("automatweb/menuedit");
	}

	function show($arr = array())
	{
		if (!isset($arr["template"]) || $arr["template"] == "")
		{
			$arr["template"] = "main.tpl";
		}
		$log = get_instance("contentmgmt/site_logger");
		$log->add($arr);
		$si = __get_site_instance();
		if (is_object($si) && method_exists($si,"pre_start_display"))
		{
			$si->pre_start_display($arr);
		}
		//if (aw_ini_get("menuedit.content_from_class_base") == 1 && aw_global_get("section") != aw_ini_get("frontpage"))
		if (aw_ini_get("menuedit.content_from_class_base") == 1)
		{
			$arr["content_only"] = 1;
		}
		else
		if (($content = $this->get_cached_content($arr)))
		{
			$this->ip_access($arr);
			return $this->do_final_content_checks($content);
		}

		// okey, now

		$inst = get_instance("contentmgmt/site_show");
		$content = $inst->show($arr);
		if (aw_ini_get("menuedit.content_from_class_base") == 1)
		{
			// now I'm assuming that frontpage is set to some kind of AW object
			$obj = new object(aw_ini_get("site_container"));
			$t = get_instance($obj->class_id());

			if (aw_global_get("section") != aw_ini_get("frontpage") || empty($_REQUEST["group"]))
			{
				// see kuvab vajaliku sisu
				$content = $t->change(array(
					"id" => aw_ini_get("site_container"),
					"group" => $_REQUEST["group"],
					"content" => $content,
				));
			}
			else
			{
				// see kuvab muutmisvormi
				$content = $t->change(array(
					"id" => aw_ini_get("site_container"),
					"group" => $_REQUEST["group"],
				));


			};

		};

		if (!aw_global_get("no_cache"))
		{
			$this->set_cached_content($arr, $content);
		}
		return $this->do_final_content_checks($content);
	}

	////
	// !returns the cached content for the requested page
	// if no user is logged in and the page exist in the cache
	function get_cached_content($arr)
	{
		if (aw_global_get("uid") != "")
		{
			return false;
		}

		if (aw_global_get("no_cache"))
		{
			return false;
		}

		// don't cache pages with generated content, they usually change for each request
		if ($arr["text"] != "")
		{
			if (!isset($arr["force_cache"]) || $arr["force_cache"] != true)
			{
				return false;
			}
		}

		// check cache
		$cp = $this->get_cache_params($arr);

		$cache = get_instance("cache");
		$tmp = $cache->get(aw_global_get("raw_section"), $cp, aw_global_get("section"));
		return $tmp;
	}

	function set_cached_content($arr, $content)
	{
		if (aw_global_get("uid") != "")
		{
			return false;
		}

		// don't cache pages with generated content, they usually change for each request
		if (!empty($arr["text"]))
		{
			if (empty($arr["force_cache"]))
			{
				return false;
			}
		}

		// check cache
		$cp = $this->get_cache_params($arr);

		$cache = get_instance("cache");
		$cache->set(aw_global_get("raw_section"), $cp, $content, true, aw_global_get("section"));
	}

	////
	// !returns array of cache parameters with what you can check if the current page is in the cache
	// params:
	//	format
	//
	function get_cache_params($arr)
	{
		$cp = array();
		if (isset($arr["format"]) && $arr["format"] != "")
		{
			$cp[] = $arr["format"];
		}

		$cp[] = aw_global_get("act_per_id");
		$cp[] = aw_global_get("lang_id");
		$cp[] = aw_global_get("ct_lang_id");
		$cp[] = isset($_SESSION["doc_content_type"]) ? $_SESSION["doc_content_type"] : null;
		$cp[] = isset($_SESSION["nliug"]) ? $_SESSION["nliug"] : null;
		if (isset($GLOBALS["oid"]))
		{
			$cp[] = $GLOBALS["oid"];
		}

		if (isset($_SESSION["menu_context"]) && is_array($_SESSION["menu_context"]))
		{
			$cp[] = join(",", $_SESSION["menu_context"]);
		}

		foreach($_SESSION as $k => $v)
		{
			if (substr($k, 0, 6) == "style_")
			{
				$cp[] = $k."_".$v;
			}
		}

		// here we sould add all the variables that are in the url to the cache parameter list
		foreach(automatweb::$request->get_args() as $var => $val)
		{
			// just to make sure that each user does not get it's own copy
			if ($var != "automatweb" && $var != "set_lang_id")
			{
				if (is_array($val))
				{
					$ov = $val;
					$val = "";
					foreach($ov as $vv)
					{
						$val.=$vv;
					}
				}
				$cp[] = $var."-".$val;
			}
		}

		return $cp;
	}

	function do_final_content_checks($res)
	{
		if (strpos($res,"[ss") !== false)
		{
			$res = preg_replace("/\[ss(\d+)\]/e","md5(time().\$_SERVER[\"REMOTE_ADDR\"].\"\\1\")",$res);
		};
		if (strpos($res,"[bloc") !== false)
		{
			preg_match_all("/\[bloc(\d+)\]/",$res, $mt);
			if (count($mt))
			{
				$bn = get_instance(CL_BANNER);
				$res = $bn->put_banners_in_html($res, $mt);
			}
		};
		if (strpos($res,"[document_statistics") !== false)
		{
			$ds = get_instance(CL_DOCUMENT_STATISTICS);
			$res = preg_replace("/\[document_statistics(\d+)\]/e", "\$ds->show(array('id' => \\1))", $res);
		};

		// if the template contains php tags, eval it.
		if (strpos($res, "<?php") !== false)
		{
			ob_start();

			$tres = $res;
			$res = str_replace("<?xml", "&lt;?xml", $res);
			$res = str_replace("<?XML", "&lt;?xml", $res);

			eval("?>".$res);
			$res = ob_get_contents();
			if (strpos($res, "syntax err") !== false || strpos($res, "parse err") !== false || strpos($res, "Fatal err") !== false)
			{
				preg_match("/on line \<b\>(\d+)\<\/b\>/ims", $res, $mt);
				$lines = explode("\n", str_replace("<?xml", "&lt;?xml", $tres));
				echo "<pre>";
				$mt[1]--;
				echo htmlentities($lines[$mt[1]-2])."<br>";
				echo htmlentities($lines[$mt[1]-1])."<br>";
				echo "<b>".htmlentities($lines[$mt[1]-0])."</b><br>";
				echo htmlentities($lines[$mt[1]+1])."<br>";
				echo htmlentities($lines[$mt[1]+2])."<br>";
				echo htmlentities($lines[$mt[1]+3])."<br>";
				echo htmlentities($lines[$mt[1]+4])."<br>";

				die("syntax error in template");
			}
			ob_end_clean();
		}

		// also clear the no_cache flag from session if present
		// why? well to let the session continue with cached pages.
		// basically, to have error messages on your submit handler you set
		// aw_session_set("no_cache", 1);
		// then redirect to some page. now, the no_cache stays as an aw_global
		// so on the next pageview it is checked and honored.
		// now, it could be the task of the application to clear this
		// but then the content would still end up in the cache
		// because this thing sets the cache content if the page is uncached
		// and the no_cache flag is off after having displayed the page
		// therefore this is the only place where we can safely clear the flag.
		aw_session_set("no_cache", 0);

		$res .= $this->build_popups();

		return $res;
	}

	// builds HTML popups
	function build_popups()
	{
		if (!empty($_GET["print"]))
		{
			return;
		}
		if (!$this->can("view", aw_global_get("section")))
		{
			return;
		}
		$so = obj(aw_global_get("section"));
		$this->path = $so->path();
		$last_menu = 0;
		$cnt = count($this->path);
		$has_ctx = false;
		for ($i = 0; $i < $cnt; $i++)
		{
			if ($this->path[$i]->class_id() == CL_MENU)
			{
				$last_menu = $this->path[$i]->id();
				if ($this->path[$i]->prop("has_ctx") == 1)
				{
					$has_ctx = 1;
				}
			}
		}

		// insert context to session
		if ($has_ctx == 1)
		{
			$lmo = obj($last_menu);

			// write to session all contexts the current menu has
			$_SESSION["menu_context"] = array();
			foreach($lmo->connections_from(array("type" => "RELTYPE_CTX")) as $c)
			{
				$_SESSION["menu_context"][] = $c->prop("to.name");
			}
		}

		$this->sel_section = $last_menu;

		// that sucks. We really need to rewrite that
		// I mean we always read information about _all_ the popups
		$pl = new object_list(array(
			"status" => STAT_ACTIVE,
			"class_id" => CL_HTML_POPUP,
			"site_id" => array(),
		));

		if (count($pl->ids()) > 0)
		{
			$t = get_instance(CL_HTML_POPUP);
		};
		$popups = "";
		foreach($pl->arr() as $o)
		{
			$o_id = $o->id();
			if ($o->prop("only_once") && $_SESSION["popups_shown"][$o_id] == 1)
			{
				continue;
			}

			$sh = false;
			foreach($o->connections_from(array("type" => "RELTYPE_FOLDER")) as $c)
			{
				if ($c->prop("to") == $this->sel_section)
				{
					//$popups .= sprintf("window.open('%s','htpopup','top=0,left=0,toolbar=0,location=0,menubar=0,scrollbars=0,width=%s,height=%s');", $url, $o->meta("width"), $o->meta("height"));
					if (!$t)
					{
						$t = get_instance(CL_HTML_POPUP);
					}
					$popups .= $t->get_popup_data($o);
					$sh = true;
					$_SESSION["popups_shown"][$o_id] = 1;
				}
			}

			$inc_submenus = $o->meta("section_include_submenus");

			if (!$sh && is_array($inc_submenus) && count($inc_submenus) > 0)
			{
				$path = obj($this->sel_section);
				$path = $path->path();

				foreach($path as $p_o)
				{
					if ($inc_submenus[$p_o->parent()])
					{
						//$popups .= sprintf("window.open('%s','htpopup','top=0,left=0,toolbar=0,location=0,menubar=0,scrollbars=0,width=%s,height=%s');", $url, $o->meta("width"), $o->meta("height"));
						if (!$t)
						{
							$t = get_instance(CL_HTML_POPUP);
						}
						$popups .= $t->get_popup_data($o);
						$_SESSION["popups_shown"][$o_id] = 1;
					}
				}
			}
		}
		return $popups;
	}

	function ip_access($arr)
	{
		if (isset($arr["force_sect"]) and $this->can("view", $arr["force_sect"]))
		{
			$so = obj($arr["force_sect"]);
		}
		if (!$this->can("view", aw_global_get("section")))
		{
			return;
		}
		else
		{
			$so = obj(aw_global_get("section"));
		}
		$p = $so->path();
		$p[] = $so;
		$p = array_reverse($p);
		foreach($p as $o)
		{
			$ipa = $o->meta("ip_allow");
			$ipd = $o->meta("ip_deny");
			if ((is_array($ipd) && count($ipd)) || (is_array($ipa) && count($ipa)))
			{
				$si = get_instance("contentmgmt/site_show");
				$si->section_obj = $so;
				$si->do_check_ip_access(array(
					"allowed" => $ipa,
					"denied" => $ipd
				));
			}
		}
	}
}
?>
