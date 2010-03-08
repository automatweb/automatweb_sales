<?php
/*
@classinfo  maintainer=kristo
*/

classload("config");

class export_lite extends aw_template
{
	function export_lite()
	{
		$this->init("export");
		$this->mned = get_instance("menuedit");

		$this->type2ext = array(
			"text/html" => "html",
			"text/html; charset=iso-8859-1" => "html",
			"text/html; charset=iso-8859-15" => "html",
			"text/css" => "css",
			"text/plain" => "txt",
			"text/richtext" => "rtf",
			"text/rtf" => "rtf",
			"image/gif" => "gif",
			"image/jpeg" => "jpg",
			"image/jpg" => "jpg",
			"image/pjpeg" => "jpg",
			"image/png" => "png",
			"application/pdf" => "pdf",
			"application/x-javascript" => "js",
			"application/zip" => "zip",
			"application/msword" => "doc",
			"application/pdf" => "pdf",
			"application/vnd.ms-excel" => "xls",
			"application/octet-stream" => "bin",
			"application/vnd.ms-powerpoint" => "ppt",
			"application/x-zip-compressed" => "zip"
		);
	}

	function init_settings()
	{
		$this->fn_type = FN_TYPE_HASH;
		$this->hashes = array();
		$this->lock_file = aw_ini_get("server.tmpdir")."/export_lite_".aw_ini_get("site_id");
	}

	function do_crawl()
	{
		$this->lock_file = aw_ini_get("server.tmpdir")."/export_lite_".aw_ini_get("site_id");
		if (file_exists($this->lock_file))
		{
			$mt = filemtime($this->lock_file);
			if ((time() - $mt) < 600)
			{
				die(t("eksport juba k&auml;ib!"));
			}
			unlink($this->lock_file);
		}
		$this->init_settings();

		// ok, this is the complicated bit.
		// so, how do we do this? first. forget the time limit, this is gonna take a while.
		aw_set_exec_time(AW_LONG_PROCESS);
		ignore_user_abort(true);

		echo "<font face='Arial'> Toimub staatiliste lehtede genereerimine, palun oodake!<br />\n";
		flush();

		// ok, start from the front page
		$this->fetch_and_save_page($this->cfg["baseurl"]."/?set_lang_id=".aw_global_get("lang_id"),aw_global_get("lang_id"));

		// check for files that need to be removed
		// here's how:
		// during export we keep a running list of id's (url md5 hashes) that we have written
		// to the db and since we do a complete export every time
		// we can just delete all other entries that were created by export
		// but are not in the current list

		$sql = "DELETE FROM static_content WHERE id NOT IN(".join(",", map("'%s'", array_values($this->hashes))).") AND created_by = 'export_lite' AND site_id = '".aw_ini_get("site_id")."'";
		$this->db_query($sql);

		unlink($this->lock_file);
		// ut fix
		if (aw_ini_get("site_id") == 900)
		{
			$this->db_query("UPDATE static_content SET lang_id = 1 WHERE site_id = 900 AND section IN(250,235,234,236)");
		}
		$this->db_query("SELECT distinct(section) FROM static_content WHERE lang_id = 2");
		while ($row = $this->db_next())
		{
			if (!is_oid($row["section"]) || !$this->can("view", $row["section"]))
			{
				continue;
			}
			$o = obj($row["section"]);
			if ($o->lang_id() != 2 && ($o->prop("type") != ML_CLIENT))
			{
				$this->save_handle();
				$this->db_query("UPDATE static_content SET lang_id = 1 WHERE section = $row[section]");
				$this->restore_handle();
			}
		}
		echo "<br />all done. <br /><br />\n\n";
		die();
	}

	function fetch_and_save_page($url, $lang_id, $single_page_only = false, $file_name = false)
	{
		// check if we must stop
		$_stfn = aw_ini_get("server.tmpdir")."/aw.export_lite.stop";
		if (file_exists($_stfn))
		{
			@unlink($_stfn);
			echo "found stop flag! <br>";
			die();
		}
		touch($this->lock_file);
		$url = $this->rewrite_link($url);
///		echo "rewrote link to $url <br>";

		$_url = $url;
		if ($url == "")
		{
			echo "<p><br />VIGA, tyhi url! </b><br />";
		}

		$url = $this->add_session_stuff($url, $lang_id);
		//echo "addes session stuff: $url <br>";

		// if we have done this page already, let's not do it again!
		if (isset($this->hashes[$url]))
		{
			//echo "fetch_and_save_page($_url, $lang_id) returning, cause we did this already <br />";
			return;
		}

//		echo "fetch_and_save_page($url, $lang_id) <br>";

		$this->fsp_level++;

		// here we track the active language in the url
		$t_lang_id = $lang_id;
		if (preg_match("/set_lang_id=(\d*)/", $url,$mt))
		{
			$t_lang_id=$mt[1];
		}

		$is_print = false;
		if (strpos($url, "print=1") !== false)
		{
			$is_print = true;
		}

		if (strpos($url, "class=document") !== false && strpos($url, "action=print") !== false)
		{
			$is_print = true;
		}

		if (strpos($url, "class=document") !== false && strpos($url, "action=send") !== false)
		{
			$is_print = true;
		}

		if (strpos($url, "class=document") !== false && strpos($url, "action=feedback") !== false)
		{
			$is_print = true;
		}

		if (strpos($url, "class=document") !== false && strpos($url, "action=change") !== false)
		{
			$is_print = true;
		}

		if (strpos($url, "class=image") !== false && strpos($url, "action=show") !== false)
		{
			$is_print = true;
		}

		if (strpos($url, "poll.aw") !== false)
		{
			$is_print = true;
		}

		if (strpos($url, "class=poll") !== false)
		{
			$is_print = true;
		}

		if (preg_match("/\/orb.aw\?set_lang_id\=\d$/imsU", $url))
		{
			$is_print = true;
		}

		if ($is_print)
		{
			$this->fsp_level--;
			return;
		}


		// set the hash table
		$this->hashes[$url] = $this->get_hash_for_url($url,$t_lang_id);

		// read content
		$this->real_url = "";
		$this->cur_lang_id = $t_lang_id;
		$fc = $this->get_page_content($url);
		$current_section = $this->current_section;
		$current_title = $this->current_title;
		$current_last_modified = $this->current_last_modified;
		$this->current_title = "";
		$this->current_last_modified = "";
	
		
		if ($this->is_extlink)
		{
			$this->fsp_level--;
			return;
		}

		if ($this->real_url != "")
		{
			$url = $this->real_url;
			$this->hashes[$url] = $this->get_hash_for_url($url,$t_lang_id);
		}
		
		$content_type = $this->get_content_type_from_headers($this->last_request_headers);
		if ($content_type != "html")
		{
			$this->fsp_level--;
			return;
		}

		if (function_exists("memory_get_usage"))
		{
			echo "current memory usage: ".memory_get_usage()." <br>";
		}
		echo "saving $url (req level: $this->fsp_level)<br />\n";
		flush();

		// now. convert all the links in the page
		$this->convert_links($fc,$t_lang_id, $single_page_only, $url);
	
		$this->save_file($fc, $current_section, $t_lang_id, $url, $current_title, $current_last_modified);

		$this->fsp_level--;
		return $f_name;
	}

	function convert_links(&$fc,$lang_id, $single_page_only, $url = false)
	{
//		echo "convert_links(fc,$lang_id) <br />";
		// uukay. so the links we gotta convert are identified by having $baseurl in them. so look for that
		$baseurl = $this->cfg["baseurl"];
		$ext = $this->cfg["ext"];

		$ends = array("'","\"",">"," ","\n");
		$len = strlen($fc);

		// do a replace for malformed links for img.aw
		$fc = str_replace("\"/img","\"".$baseurl."/img",$fc);
		$fc = str_replace("'/img","'".$baseurl."/img",$fc);
		// fix some other common mistakes 
		$fc = str_replace("\"/index.".$ext,"\"".$baseurl."/index.".$ext,$fc);
		$fc = str_replace("'/index.".$ext,"'".$baseurl."/index.".$ext,$fc);
		// sitemap
		$fc = str_replace("\"/sitemap","\"".$baseurl."/sitemap",$fc);
		$fc = str_replace("'/sitemap","'".$baseurl."/sitemap",$fc);
		$fc = str_replace("'/tellimine","'".$baseurl."/tellimine",$fc);
		$fc = str_replace("\"/tellimine","\"".$baseurl."/tellimine",$fc);

		// href='/666' type of links
		$fc = preg_replace("/href='\/(\d*)'/iU","href='".$baseurl."/\\1'",$fc);
		$fc = preg_replace("/href=\"\/(\d*)\"/iU","href=\"".$baseurl."/\\1\"",$fc);

		// also, remove all things between html comments so we won't find any erroneous urls from those
		$fc = preg_replace("/<!--(.*)-->/imsU","", $fc);

		while (($pos = strpos($fc,$baseurl)) !== false)
		{
			// now find all of the link - we do that by looking for ' " > or space
			$begin = $pos;
			$end = $pos+strlen($baseurl);
			$link = $baseurl;
			while (!in_array($fc[$end],$ends) && $end < $len)
			{
				$end++;
			}

			// correct the link
			$link = $this->rewrite_link(substr($fc,$begin,($end-$begin)));
			if (!$this->is_external($link))
			{
				// fetch the page
				$this->fetch_and_save_page($link,$lang_id);
			}
			// we still gotta replace the link, even if it is an extlink outta here, 
			// cause otherwise we would end up in an infinite loop

//			echo "replace $link with $fname begin = $begin end = $end , url = $url <br />";
			// replace the link in the html
			$fname = md5($link);
			$fc = substr($fc,0,$begin).$fname.substr($fc,$end);
		}

//		echo "convert_links($url,$lang_id) returning <br />";
	}

	function save_file($o_fc, $cur_sec, $lang_id, $url, $title, $modified)
	{
		echo "saving $url content! <br />\n";

		//$h_id = md5($url).",".$lang_id;
		$h_id = $this->hashes[$url];

		if (preg_match("/<!-- MODIFIED:(.*) -->/U", $o_fc, $mt))
		{
			$nmodified = trim($mt[1]);
			//echo "preg match for time  = $modified <br>";
			if (!is_numeric($nmodified) && $nmodified != "")
			{
				list($d,$m,$y) = explode("/", $nmodified);
	
				$nmodified = mktime(0,0,0,$m, $d, $y);
			}
			if ($nmodified > 1)
			{
				$modified = $nmodified;
			}
		}
		
		if (!$modified)
		{
			$modified = time();
		}
		
		if (strpos($url, "use_table") !== false)
		{
			$modified = time();
		}
		// now, we also gots to ask the content without menus, cause we don't need to 
		// search no stinkin menus.
		$sep = "?";
		if (strpos($url, "?") !== false)
		{
			$sep = "&";
		}
		$fc = $this->get_page_content($url.$sep."real_no_menus=1");
		//echo "fetched real content as ".$url.$sep."real_no_menus=1 <br>";

		$o_title = $title;
		
		$nm = preg_match_all("/\<!-- PAGE_TITLE (.*) \/PAGE_TITLE -->/U", $fc, $mt_t, PREG_SET_ORDER);
		$title = $mt_t[$nm-1][1];
		
		$title = trim(strip_tags($title));

		//echo "title b4 preg = $title <br>";
		if ($title == "")
		{
			// if all else fails, read the <TITLE>foo</TITLE> tag...
			preg_match("/<TITLE>(.*)<\/TITLE>/iUs", $o_fc, $nt_t);
			$title = trim(strip_tags($nt_t[1]));
		//	echo "title from preg = $title <br>";
		}
		$title = trim(strip_tags($title));

		if ($title == "")
		{
			$title = $o_title;
		}
		$title = trim(strip_tags($title));
					
		$this->quote(&$title);


		// also remove javascript content
		$fc = preg_replace("/<script(.*)<\/script>/imsU","", $fc);
		// and css styles
		$fc = preg_replace("/<style(.*)<\/style>/imsU","", $fc);
		// and html comments
		$fc = preg_replace("/<!--(.*)-->/imsU","", $fc);

		$fc = trim(strip_tags($fc));
		$this->quote($fc);
		$this->quote(&$url);
		
		// check cur_sec, if it's a document, go up to menu
		$sql = "SELECT parent,class_id from objects where oid = '$cur_sec'";
		$data = $this->db_fetch_row($sql);
		if ($data["class_id"] != CL_MENU)
		{
			$cur_sec = $data["parent"];
		}
	
		if (!$cur_sec)
		{
			// parse the section from the url. since the url is always nicely formatted, it is relatively easy.
			$urld = parse_url($url);
			parse_str($urld["query"], $paramd);
			$cur_sec = $paramd["section"];
		}

		if (aw_ini_get("site_id") == 900)
		{
			if ($cur_sec == 250 || $cur_sec == 235 || $cur_sec == 234 || $cur_sec == 236)
			{
				$lang_id = 1;
			}
		}
		
		$t_id = $this->db_fetch_field("SELECT id FROM static_content WHERE id = '$h_id'","id");

		if ($t_id == $h_id)
		{
			$q = "UPDATE static_content SET lang_id = '$lang_id', content = '$fc',modified = '$modified', section = '$cur_sec',title = '$title',url='$url',created_by = 'export_lite', last_modified = ".time()."  WHERE id = '$h_id'";
			$this->db_query($q);
		}
		else
		{
			$q = "
				INSERT INTO static_content(
					id, 					content, 					modified, 					section, 
					lang_id,				title,						url,						created_by,
					site_id, last_modified
				) 
				VALUES(
					'$h_id',				'$fc',						'$modified',					'$cur_sec',
					'$lang_id',				'$title',					'$url',						'export_lite',
					'".aw_ini_get("site_id")."', ".time()."
				)
			";
			$this->db_query($q);
		}
	}

	function get_ext_for_link($link, $headers)
	{
		echo "get_ext_for_link($link, headers) <br />";
		if (isset($this->link2type[$link]))
		{
			echo "get_ext_for_link($link, headers) returning ",$this->link2type[$link],"<br />";
			return $this->link2type[$link];
		}

		if (count($headers) < 1)
		{
			// we gotta get the page, cause we haven't yet
			$this->get_page_content($link);
			$headers = explode("\n", $this->last_request_headers);
		}

		// deduct the type from the headers - muchos beteros that way
		$ct = "text/html";
		foreach($headers as $hd)
		{
			if (preg_match("/Content\-Type\: (.*)/", $hd, $mt))
			{
				$ct = $mt[1];
			}
		}

		if (!isset($this->type2ext[$ct]))
		{
			echo "<B><font color=red><br />hmVIGA! EI LEIDNUD ext for type $ct <br /></font></b>";
		}

		$this->link2type[$link] = $this->type2ext[$ct];
		echo "get_ext_for_link($link, headers) returning ",$this->link2type[$ct],"<br />";
		return $this->type2ext[$ct];
	}

	////
	// !checks the link and rewrites it, so all section links are the same and some other weirdness to make
	// things work correctly
	function rewrite_link($link)
	{
		if (strpos($link, "/tellimine") !== false)
		{
			return $link;
		}
		if (isset($this->rewrite_link_cache[$link]))
		{
			return $this->rewrite_link_cache[$link];
		}
//		echo "rewrite_link($link) <br />";
		$baseurl = $this->cfg["baseurl"];
		$ext = $this->cfg["ext"];
		$frontpage = $this->cfg["frontpage"];
		$basedir = $this->cfg["site_basedir"]."/public";
		$_link = $link;

		$link = str_replace($baseurl."/?",$baseurl."/index.$ext?",$link);
		if (substr($link,0,2) == "/?")
		{
			$link = $baseurl."/index.$ext?".substr($link,2);
		}

		// do link processing as aw would upon request startup
		$ud = parse_url($link);
		if (!preg_match("/(banner.aw|graphs.aw|css|poll|files|ipexplorer|icon.aw|gallery.aw|login|stats|vcl|misc|index|images|feedback|forms|indexx|showimg|sorry|monitor|vv|automatweb|img|reforb|orb)/",$link))
		{
			// treat the damn thing as an alias
			// aliases will not contain ? and & so do this:
			$end = "";
			if (substr($ud["path"],1) != "" || $ud["query"] != "" || $ud["fragment"] != "")
			{
				$sec_str = substr($ud["path"],1,7) == "section" ? "" : "section=";
				$end = "?".$sec_str.str_replace("?", "&",substr($ud["path"],1)."&".$ud["query"].$ud["fragment"]);
			}
			$link = $baseurl."/index.".$ext.$end;
			// now just extract it again
			$ud = parse_url($link);
			// damn, this does not handle urls like http://bla/index.aw?section=2345/oid=333
			parse_str($ud["query"],$HG);
			// so we do some subtle trickery here. basically, section cannot contain =
			// so we check for that
			if (($eqpos = strpos($HG["section"], "=")) !== false)
			{
//				echo "doing weird magick for link $_link <br />";
				$tp = substr($HG["section"], 0, $eqpos);
				$lslpos = strrpos($tp, "/");
				$ttp = substr($tp, 0, $lslpos);
				// now $ttp contains the real section value, so we can replace the / after the section with &
				// and now we should also replace all other /'s with &'s after the end of the section variable
				$seclen = strlen("section=".$ttp."&");
				$aftersec = str_replace("/", "&", substr($ud["query"], $seclen));
				// and now put the full string together again
				$tq = "section=".$ttp."&".$aftersec;
				parse_str($tq,$HG);
//				echo "hg = <pre>", var_dump($HG),"</pre> <br />";
//				echo "returning tq = $tq <br />";
			}

			$js = "";
			foreach($HG as $k => $v)
			{
				if ($k == "section" && !is_numeric($v))
				{
					// we must turn the section into a number always. 
					$v = $this->mned->check_section($v,false);
				}
				if (is_array($v))
				{
					$vs = $this->make_array_url_string($k,$v);
				}
				else
				{
					$vs = $k."=".$v;
				}
				if ($js != "")
				{
					$js.="&";
				}
				$js.=$vs;
			}
//			$js = join("&", map2("%s=%s", $HG));
			if ($js != "")
			{
				$js = "?".$js;
			}
			$link = $baseurl."/index.aw".$js;
//			echo "returned1 $link for $_link <br />";
//			echo "rewrite_link($_link) returning $link <br />";
			$this->rewrite_link_cache[$_link] = $link;
			return $link;
		}
		else
		{
			// first check if the file exists. if it does, then no rewrite the link.
			if ($this->link_is_file($link))
			{
				$this->rewrite_link_cache[$_link] = $link;
				return $link;
			}
	
			$link = str_replace($this->cfg["baseurl"]."/index.".$this->cfg["ext"], "", $link);
			$link = str_replace($this->cfg["baseurl"]."/reforb.".$this->cfg["ext"], "", $link);
			$link = str_replace($this->cfg["baseurl"]."/login.".$this->cfg["ext"], "", $link);
			$link = $this->do_aw_parse_url($link);
			// now that we got a nice link, check if it is a class=links&action=show, cause those do redirects and
			// php's fopen can't handle that
			if (strpos($link, "class=links") !== false && strpos($link, "action=show") !== false)
			{
				preg_match("/id=(\d*)/", $link,$mt);

				$el = get_instance(CL_EXTLINK);
				$ld = $el->get_link($mt[1]);
				$link = $ld["url"];
				if (substr($link,0,4) == "http" && strpos($link,$baseurl) === false)
				{
					// external link, should not be touched I guess
//					echo "rewrite_link($_link) returning $link <br />";
//					$this->rewrite_link_cache[$_link] = $link;
					return $link;
				}

				if (strpos($link,$baseurl) === false && $link[0] == "/")
				{
					$link = $baseurl.$link;
				}
				$link = $this->rewrite_link($link);
//				echo "rewrote extlink $_link to $link  <br />";
			}
			$this->rewrite_link_cache[$_link] = $link;
//			echo "rewrite_link($_link) returning $link <br />";
			return $link;
		}
	}

	function get_hash_for_url($url, $lang_id)
	{
		if (isset($this->hash2url[$lang_id][$url]))
		{
			return $this->hash2url[$lang_id][$url];
		}

		//echo "get_hash_for_url($url, $lang_id)<br />";
		$fpurls = array(
			$this->cfg["baseurl"]."/?set_lang_id=1&automatweb=aw_export",
			$this->cfg["baseurl"]."/index.".$this->cfg["ext"]."?set_lang_id=1&automatweb=aw_export",
			$this->cfg["baseurl"]."/?automatweb=aw_export&set_lang_id=1",
			$this->cfg["baseurl"]."/index.".$this->cfg["ext"]."?automatweb=aw_export&set_lang_id=1",
			$this->cfg["baseurl"]."/?set_lang_id=1",
			$this->cfg["baseurl"]."/index.".$this->cfg["ext"]."?set_lang_id=1",
			$this->cfg["baseurl"]."/?set_lang_id=1",
			$this->cfg["baseurl"]."/index.".$this->cfg["ext"]."?set_lang_id=1",
			$this->cfg["baseurl"]."/index.".$this->cfg["ext"]."?section=".aw_ini_get("frontpage")."&set_lang_id=1",
		);
		if (in_array($url,$fpurls))
		{
//			echo "get_hash_for_url($url, $lang_id) returning index<br />";
			$this->hash2url[$lang_id][$url] = "index";
			return "index";
		}

		$tmp = gen_uniq_id(str_replace($this->cfg["baseurl"],"",$url)).",".$lang_id;
//		echo "get_hash_for_url($url, $lang_id) returning ",$tmp,"<br />";
		$this->hash2url[$lang_id][$url] = $tmp;
		return $tmp;
	}

	function add_session_stuff($url, $lang_id)
	{
		if (strpos($url,"?") === false)
		{
			$sep = "?";
		}
		else
		{
			$sep = "&";
		}

		if (strpos($url, "set_lang_id=") === false)
		{
			$url = $url.$sep."set_lang_id=".$lang_id;
		}
		return $url;
	}

	function is_external($link)
	{
		if (substr($link, 0, 4) == "ftp:" || (substr($link,0,4) == "http" && strpos($link, $this->cfg["baseurl"]) === false))
		{
//			echo "is_external($link) returning true <br />";
			return true;
		}
//		echo "is_external($link) returning false<br />";
		return false;
	}

	function is_dynamic($link)
	{
		if (strpos($link, "/poll.".$this->cfg["ext"]) !== false)
		{
			return true;
		}
		else
		if (strpos($link, "class=document&action=change") !== false)
		{
			return true;
		}
		return false;
	}

	function exp_reset()
	{
		$this->hashes = array();
		$this->link2type = array();
		$this->hash2url = array();
		$this->ftn_used = array();
		$this->fta_used = array();
		$this->hash2url[2][$this->cfg["baseurl"]."/index.aw?section=20&set_lang_id=2"] = "english";
		$this->hash2url[2][$this->cfg["baseurl"]."/index.aw?set_lang_id=2"] = "english";
		$this->hash2url[2]["http://editwww.ut.ee/index.aw?section=20&set_lang_id=2"] = "english";
		$this->hash2url[2]["http://editwww.ut.ee/index.aw?set_lang_id=2"] = "english";
		$this->hash2url[2]["http://editwww3.ut.ee/index.aw?section=20&set_lang_id=2"] = "english";
		$this->hash2url[2]["http://editwww3.ut.ee/index.aw?set_lang_id=2"] = "english";
	}
	
	////
	// !this tries to keep session between pages
	function get_page_content($url)
	{
		if (!$this->cookie)
		{
			$this->get_session();
		}

		$host = str_replace("http://","",aw_ini_get("baseurl"));
		preg_match("/.*:(.+?)/U",$host, $mt);
		if ($mt[1])
		{
			$host = str_replace(":".$mt[1], "", $host);
		}
		$port = ($mt[1] ? $mt[1] : 80);

		$y_url = str_replace(aw_ini_get("baseurl"),"", $url);
		$req  = "GET $y_url HTTP/1.0\r\n";
		$req .= "Host: ".$host.($port != 80 ? ":".$port : "")."\r\n";
		$req .= "Cookie: automatweb=".$this->cookie."\r\n";
		$req .= "User-agent: AW-export_lite-spider\r\n";
		$req .= "\r\n";
		classload("protocols/socket");
		$socket = new socket(array(
			"host" => $host,
			"port" => $port,
		));
//		echo "req = ".dbg::dump($req)." <br>";
		$socket->write($req);
		$ipd = "";
		while($data = $socket->read(10000000))
		{
			$ipd .= $data;
		};
//		echo "ipd = ".dbg::dump($ipd)." <br>";
		list($headers,$data) = explode("\r\n\r\n",$ipd,2);
		$this->last_request_headers = $headers;

		$this->current_section = "";
		$this->current_last_modified = "";
		$this->current_title = "";
		$this->is_extlink = false;

		$spl = explode("\r\n", $headers);
		foreach($spl as $line)
		{
			if (strpos($line, "Location:") !== false)
			{
				$to = trim(str_replace("Location:", "", $line));
				if ($this->is_external($to))
				{
					$this->is_extlink = true;
				}
				else
				{
					// if it's an intra-site link, then fetch the content 
					$to = $this->rewrite_link($to);
					$to = $this->add_session_stuff($to, $this->cur_lang_id);
					$this->real_url = $to;
					return $this->get_page_content($to);
				}
			}

			if (strpos($line, "X-AW-Section: ") !== false)
			{
				$this->current_section = trim(str_replace("X-AW-Section: ", "", $line));
			}

			if (strpos($line, "X-AW-Last-Modified: ") !== false)
			{
				$this->current_last_modified = trim(str_replace("X-AW-Last-Modified: ", "", $line));
			}

			if (strpos($line, "X-AW-Document-Title: ") !== false)
			{
				$this->current_title = trim(str_replace("X-AW-Document-Title: ", "", $line));
			}
		}
		return $data;
	}

	function get_session()
	{
		$host = str_replace("http://","",$this->cfg["baseurl"]);
		preg_match("/.*:(.+?)/U",$host, $mt);
		if ($mt[1])
		{
			$host = str_replace(":".$mt[1], "", $host);
		}
		$port = ($mt[1] ? $mt[1] : 80);
		classload("protocols/socket");
		$socket = new socket(array(
			"host" => $host,
			"port" => $port,
		));
		
		$op = "HEAD / HTTP/1.0\r\n";
		$op .= "User-Agent: AW-EXPORT-LITE\r\n";
		$op .= "Host: $host".($port != 80 ? ":".$port : "" )."\r\n\r\n";

		print "<pre>";
		print "Acquiring session\n";
		flush();

		//echo "op = $op <br />";
		$socket->write($op);

		$ipd="";
		
		while($data = $socket->read())
		{
			$ipd .= $data;
		};

		//echo "ipd = $ipd <br />";
		if (preg_match("/automatweb=(.*);/",$ipd,$matches))
		{
			$cookie = $matches[1];
		};

		$this->cookie = $cookie;

		print "Got session, ID is $cookie\n</pre>";
	}

	function make_array_url_string($k,$v)
	{
		$ret = array();
		foreach($v as $_k => $_v)
		{
			if (is_array($_v))
			{
				$ret[] =$this->make_array_url_string($k."[$_k]", $_v);
			}
			else
			{
				$ret[] =$k."[]=".urlencode($_v);
			}
		}
		return join("&", $ret);
	}

	function do_aw_parse_url($link)
	{
		$_link = $link;
//		echo "enter do_aw_parse_url($link) <br />\n\n";
//		flush();
		$pi = str_replace($this->cfg["baseurl"], "", $link);

		$HG = array();
		if ($pi) 
		{
			// if $pi contains & or = 
			if (preg_match("/[&|=]/",$pi)) 
			{
				// expand and import PATH_INFO
				// replace ? and / with & in $pi and output the result to HTTP_GET_VARS
				// why so?
				parse_str(str_replace("?","&",str_replace("/","&",$pi)),$HG);
		//		echo "gv = <pre>", var_dump($HTTP_GET_VARS),"</pre> <br />";
			} 

			if (($_pos = strpos($pi, "section=")) === false)
			{
				// ok, we need to check if section is followed by = then it is not really the section but 
				// for instance index.aw/set_lang_id=1
				// we check for that like this:
				// if there are no / or ? chars before = then we don't prepend

				$qpos = strpos($pi, "?");
				$slpos = strpos($pi, "/");
				$eqpos = strpos($pi, "=");
				$qpos = $qpos ? $qpos : 20000000;
				$slpos = $slpos ? $slpos : 20000000;

				if (!$eqpos || ($eqpos > $qpos || $slpos > $qpos))
				{
					// if no section is in url, we assume that it is the first part of the url and so prepend section = to it
					$pi = str_replace("?", "&", "section=".substr($pi, 1));
				}
			}

			if (($_pos = strpos($pi, "section=")) !== false)
			{
				// this here adds support for links like http://bla/index.aw/section=291/lcb=117
				$t_pi = substr($pi, $_pos+strlen("section="));
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
						else
						if ($_tpos2 === false)
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
		};
		if ($section != "")
		{
			$HG["section"] = $section;
		}
		$js = "";
		foreach($HG as $k => $v)
		{
			if ($k == "section" && !is_numeric($v))
			{
				// we must turn the section into a number always. 
				$v = $this->mned->check_section($v,false);
			}
			if ($k == "section" && trim($v) === "")
			{
				continue;
			}
			if (is_array($v))
			{
				$vs = $this->make_array_url_string($k,$v);
			}
			else
			{
				$vs = $k."=".$v;
			}
			if ($js != "")
			{
				$js.="&";
			}
			$js.=$vs;
		}
//			$js = join("&", map2("%s=%s", $HG));
		if ($js != "")
		{
			$js = "?".$js;
		}

		if (strpos($_link, "orb.".$this->cfg["ext"]) !== false)
		{
			$link = $this->cfg["baseurl"]."/orb.".$this->cfg["ext"].$js;
		}
		else
		if (strpos($_link, "poll.".$this->cfg["ext"]) !== false)
		{
			$link = $this->cfg["baseurl"]."/poll.".$this->cfg["ext"].$js;
		}
		else
		{
			$link = $this->cfg["baseurl"]."/index.".$this->cfg["ext"].$js;
		}
//		echo "exit do_aw_parse_url($link) = $link <br />\n\n";
//		flush();
		return $link;
	}

	function link_is_file($link)
	{
		$link = str_replace($this->cfg["baseurl"], "", $link);
		$basedir = $this->cfg["site_basedir"]."/public";
		// now separate it by /'s and find the first one that matches a file
		$pathbits = array();
		
		$pt = strtok($link, "?&/");
		while ($pt !== false)
		{
			if ($pt != "")
			{
				if (!$found)
				{
					if (strpos($pt, "/") !== false)
					{
						$cname .= $pt;
					}
					else
					{
						$cname .= "/".$pt;
					}
					$trypath = $basedir.$cname;
					if (@is_file($trypath))
					{
						if (strpos($trypath, "orb.".$this->cfg["ext"]) === false &&
								strpos($trypath, "index.".$this->cfg["ext"]) === false && 
								strpos($trypath, "poll.".$this->cfg["ext"]) === false)
						{
							$found = true;
						}
					}
				}
			}
			$pt = strtok("?&/");
		}
		return $found; 
	}

	function stop_rule($arr)
	{
		extract($arr);
		$fp = fopen(aw_ini_get("server.tmpdir")."/aw.export_lite.stop","w");
		fwrite($fp, $id);
		fclose($fp);
		die("Kirjutasin expordi stop flagi faili ".
			aw_ini_get("server.tmpdir")."/aw.export_lite.stop<br /><a href='".$this->mk_my_orb("change", array("id" => $id))."'>Tagasi</a>");
	}

	function get_content_type_from_headers($headers)
	{
		$headers = explode("\n", $headers);

		// deduct the type from the headers - muchos beteros that way
		$ct = "text/html";
		foreach($headers as $hd)
		{
			if (preg_match("/Content\-Type\: (.*)/", $hd, $mt))
			{
				$ct = trim($mt[1]);
				if (strpos($ct,";"))
				{
					$ct = substr($ct,0,strpos($ct,";"));
				};
			}
		}

		if (!isset($this->type2ext[$ct]))
		{
			echo "<B><font color=red><br />VIGA! EI LEIDNUD ext for type $ct <br /></font></b>";
		}
		return $this->type2ext[$ct];
	}
}
?>
