<?php
/*
@tableinfo banners index=id master_table=objects master_index=brother_of
@classinfo syslog_type=ST_BANNER relationmgr=yes

@default table=objects
@default group=general

	@property general_toolbar type=toolbar no_caption=1 store=no

	@property name type=textbox
	@caption Nimi

	@property comment type=textbox
	@caption Kommentaar

	@property status type=status
	@caption Aktiivne

	@property url type=textbox table=banners
	@caption URL, kuhu klikkimisel suunatakse

	@property banner_file type=relpicker reltype=RELTYPE_BANNER_FILE table=banners
	@caption Banneri sisu

	@property banner_file_2 type=relpicker reltype=RELTYPE_BANNER_FILE table=banners
	@caption Banneri sisu lisaks

	@property banner_new_win type=checkbox ch_value=16 field=flags method=bitmask
	@caption Link avaneb uues aknas

	@property html type=textarea rows=5 cols=30 table=banners
	@caption Banneri html

@groupinfo display caption="N&auml;itamine"

	@property probability_tbl type=table group=display
	@caption N&auml;itamise t&otilde;en&auml;osus

	@property probability type=textbox display=none table=banners

	@property max_views type=textbox size=3 table=banners  group=display
	@caption Mitu korda bannerit maksimaalselt n&auml;idata

	@property max_clicks type=textbox size=3 table=banners  group=display
	@caption Mitu klikki maksimaalselt

@groupinfo stats caption="Statistika" submit=no

	@property stats_table type=table no_caption=1 store=no group=stats

@groupinfo timing caption="Ajaline aktiivsus"
@default group=timing

	@property timing type=timing store=no
	@caption Ajaline aktiivsus

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

@reltype LOCATION value=1 clid=CL_BANNER_CLIENT
@caption banneri asukoht

@reltype BANNER_FILE value=2 clid=CL_IMAGE,CL_FILE,CL_FLASH,CL_EXTLINK,CL_DOCUMENT
@caption banneri sisu

@reltype TIMING value=20 clid=CL_TIMING
@caption Aeg

*/

class banner extends class_base
{
	function banner()
	{
		$this->init(array(
			"tpldir" => "banner",
			"clid" => CL_BANNER
		));

		$this->trans_props = array(
			"url", "banner_file", "banner_file_2"
		);
	}

	function get_property($arr)
	{
		$prop =& $arr["prop"];
		switch($prop["name"])
		{
			case "probability_tbl":
				$this->do_prob_tbl($arr);
				break;
			case 'general_toolbar':
				$this->do_general_toolbar($prop['toolbar'], $arr);
				break;
			case 'stats_table':
				$this->stats_table($arr);
				break;
		}

		return PROP_OK;
	}

	function set_property($arr)
	{
		$prop =& $arr["prop"];

		switch($prop["name"])
		{
			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "probability_tbl":
				$this->do_save_prob_tbl($arr);
				break;
		}
		return PROP_OK;
	}

	private function stats_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$langs = $this->db_fetch_array("SELECT DISTINCT langid FROM banner_views");
		$lng = get_instance("languages");
		$langnames = $lng->get_list();
		$t->define_field(array(
			"name" => "name",
			"caption" => " "
		));
		$t->define_field(array(
			"name" => "total",
			"caption" => t("Kokku")
		));
		$t->set_caption(sprintf(t("Banneri %s klikkimise ja vaatamise statistika"), $arr["obj_inst"]->name));

		$clickdata = $this->db_fetch_array("SELECT COUNT(*) as cnt, langid FROM banner_clicks WHERE bid = '".$arr["obj_inst"]->id()."' GROUP BY langid");
		$viewdata = $this->db_fetch_array("SELECT COUNT(*) as cnt, langid FROM banner_views WHERE bid = '".$arr["obj_inst"]->id()."' GROUP BY langid");

		$this->click_through = $this->views = $this->clicks = 0;

		foreach($clickdata as $click)
		{
			if($click["langid"])
			{
				$clicks["lang".$click["langid"]] = (int)$click["cnt"];
				$this->clicks += (int)$click["cnt"];
			}
		}

		foreach($viewdata as $view)
		{
			if($view["langid"])
			{
				$t->define_field(array(
					"name" => "lang".$view["langid"],
					"caption" => t($langnames[$view["langid"]])
				));
				$views["lang".$view["langid"]] = (int)$view["cnt"];
				$this->views += (int)$view["cnt"];
				if(!$clicks["lang".$view["langid"]])
				{
					$clicks["lang".$view["langid"]] = 0;
				}
			}
		}

		foreach($views as  $langid => $v)
		{
			if ($v > 0)
			{
				$ct_lang = round(($clicks[$langid] / $v) * 100.0, 4);
				$cthrough[$langid] = $ct_lang."%";
				$this->click_through += $ct_lang;
			}
			else
			{
				$cthrough[$langid] = "0%";
			}
		}

		$cthrough["name"] = t("Click-through ratio");
		$cthrough["total"] = round($this->click_through,4)."%";
		$t->define_data($cthrough);
		$views["name"] = t("Vaatamisi");
		$views["total"] = $this->views;
		$t->define_data($views);
		$clicks["name"] = t("Klikke");
		$clicks["total"] = $this->clicks;
		$t->define_data($clicks);
	}

	private function do_save_prob_tbl(&$arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_LOCATION")) as $c)
		{
			$loc = $c->to();
			// banners for that location
			foreach($loc->connections_to(array("from.class_id" => CL_BANNER)) as $c)
			{
				$bann = $c->from();
				$bann->set_prop("probability", $arr["request"]["prob"][$bann->id()]);
				$bann->save();
			}
		}
	}

	// Generates toolbar
	private function do_general_toolbar (&$tb, $arr)
	{
		$tb->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=> t('Uus')
		));

		$alias_to = $arr['obj_inst']->id();

		$clss = aw_ini_get("classes");
		foreach(array(CL_IMAGE, CL_FILE, CL_FLASH, CL_EXTLINK, CL_DOCUMENT) as $clid)
		{
			$tb->add_menu_item(array(
				'parent'=>'add_item',
				'text'=> $clss[$clid]['name'],
				'link'=>aw_url_change_var(array(
					'action' => 'new',
					'parent' => $arr['obj_inst']->id(),
					'alias_to' => $alias_to,
					'reltype' => 2, // CL_BANNER.RELTYPE_BANNER_FILE
					"class" => strlen(strrchr($clss[$clid]['file'], '/')) ?  substr(strrchr($clss[$clid]['file'],'/'), 1) : $clss[$clid]['file'],
					'return_url' => get_ru(),
				))
			));
		}

		$tb->add_menu_separator(array(
			'parent' => 'add_item',
		));

		$tb->add_menu_item(array(
			'parent'=>'add_item',
			'text'=> $clss[CL_BANNER_CLIENT]['name'],
			'link'=>aw_url_change_var(array(
				'action' => 'new',
				'parent' => $arr['obj_inst']->id(),
				'alias_to' => $alias_to,
				'reltype' => 1, // CL_BANNER.RELTYPE_LOCATION
				'return_url' => get_ru(),
				"class" => "banner_client",
			))
		));
	}

	private function init_prob_tbl(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Banner"),
			"width" => "90%"
		));

		$t->define_field(array(
			"name" => "prob",
			"caption" => t("N&auml;itamise t&otilde;en&auml;osus"),
			"align" => "center"
		));
	}

	private function do_prob_tbl(&$arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->init_prob_tbl($t);

		// get the location(s) for this banner and then get all banners for that location. stick them into a table
		// and let the user enter probabilities
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_LOCATION")) as $c)
		{
			$loc = $c->to();
			// banners for that location
			$t->define_data(array(
				"name" => "<b>".$loc->name()."</b>"
			));

			foreach($loc->connections_to(array("from.class_id" => CL_BANNER)) as $c)
			{
				$bann = $c->from();
				$t->define_data(array(
					"name" => "&nbsp;&nbsp;&nbsp;&nbsp;".$bann->name(),
					"prob" => html::textbox(array(
						"name" => "prob[".$bann->id()."]",
						"value" => $bann->prop("probability"),
						"size" => 6
					))
				));
			}
		}
		$t->set_sortable(false);
	}

	/** randomly selects a banner from the specified group

		@comment
			$gid - banner area id
	**/
	private function get_grp($gid, $die = true)
	{
		$baids = array();
		$cnt = 0;
		// selektime k6ik bannerid selle kliendi kohta.
		$g_obj = obj($gid);
		$bbs = array();
		foreach($g_obj->connections_to(array("from.class_id" => CL_BANNER)) as $c)
		{
			$bbs[] = $c->prop("from");
		}
		if (!count($bbs))
		{
			return $this->error_banner(false);
		}

		$bbs = join(",",$bbs);
		if ($bbs == "")
		{
			$this->error_banner($die);
		}

		if (!aw_ini_get("user_interface.full_content_trans"))
		{
			$stat = "AND objects.status = 2";
		}

		$q = "
			SELECT
				banners.id as id,
				banners.probability as pb
			FROM banners
			LEFT JOIN objects ON objects.oid = banners.id
			WHERE
				banners.id IN ($bbs)
				$stat
				AND (clicks <= max_clicks OR (max_clicks is null OR max_clicks = 0) OR clicks is null)
				AND (views <= max_views OR (max_views is null OR max_views = 0) or views is null)
			ORDER BY RAND()
		";
		$this->db_query($q);

		$sum = 0;
		while ($row = $this->db_next())
		{
			if (!$this->can("view", $row["id"]))
			{
				continue;
			}
			if (aw_ini_get("user_interface.full_content_trans"))
			{
				$bo = obj($row["id"]);
				if ($bo->trans_get_val("status") != STAT_ACTIVE)
				{
					continue;
				}
			}

			$baids[$cnt++] = $row;
			$sum += $row["pb"];
		}
		$bid = null;
		if ($cnt > 0)
		{
			srand ((double) microtime() * 10000000);
			if ($sum == 0)
			{
				// If all %-s are zero, all have equal chances
				// we can just pick first, as list was randomized in SQL
				$bid = $baids[0]["id"];
				if (!$bid)
				{
					$this->error_banner($die);
				}
			}
			else
			{
				// select the banner by it's probability.
				// probability of every banner = it's % / sum of all %
				// banners with 0% have no chance now! (unless all are 0, then previous block runs)
				$r = rand(1, $sum);
				$pb = 1;
				for ($i=1; $i<=$cnt; $i++)
				{
					if ($r >= $pb && $r < ($pb+$baids[$i-1]["pb"]))
					{
						$bid = $baids[$i-1]["id"];
						break;
					}
					$pb += $baids[$i-1]["pb"];
				}
			}
			return obj($bid);
		}
		else
		{
			if (!$bid)
			{
				$this->error_banner($die);
			}
		}
	}

	private function add_click($bid)
	{
		$langid = $this->get_lang_id();
		$this->quote($ss);
		$ip = aw_global_get("HTTP_X_FORWARDED_FOR");
		if ($ip == "" || !(strpos($ip,"unknown") === false))
		{
			$ip = aw_global_get("REMOTE_ADDR");
		}

		$bid = (int)$bid;
		$this->db_query("INSERT INTO banner_clicks (tm,bid,ip,langid) values('".time()."','$bid','$ip','$langid')");
	}

	/** adds a record to the banner_ids table to identify shown banners l8r **/
	private function add_view($bid,$ss,$noview,$clid)
	{
		$langid = $this->get_lang_id();
		$this->quote($ss);
		$this->db_query("INSERT INTO banner_ids(ss,bid,tm,clid) values('$ss','$bid',".time().",'$clid')");
		if (!$noview)
		{
			$ip = aw_global_get("HTTP_X_FORWARDED_FOR");
			if ($ip == "" || !(strpos($ip,"unknown") === false))
			{
				$ip = aw_global_get("REMOTE_ADDR");
			}
			// ok. a prize for anybody (except terryf and duke) who figures out why the next line is like it is :)
			$day = mktime(8,42,17,date("n"),date("d"),date("Y"));
			$this->db_query("INSERT INTO banner_views (tm,bid,ip,clid,langid) VALUES(".time().",$bid,'$ip','$clid','$langid')");
			$this->db_query("UPDATE banners SET views=views+1 WHERE id = $bid");
		}
	}

	private function add_simple_view($bid, $clid)
	{
		$langid = $this->get_lang_id();
		$ip = aw_global_get("HTTP_X_FORWARDED_FOR");
		if ($ip == "" || !(strpos($ip,"unknown") === false))
		{
			$ip = aw_global_get("REMOTE_ADDR");
		}
		// ok. a prize for anybody (except terryf and duke) who figures out why the next line is like it is :)
		$day = mktime(8,42,17,date("n"),date("d"),date("Y"));
		$this->db_query("INSERT INTO banner_views (tm,bid,ip,clid,langid) VALUES(".time().",$bid,'$ip','$clid','$langid')");
		$this->db_query("UPDATE banners SET views=views+1 WHERE id = $bid");
	}

	private function get_lang_id()
	{
		$type = aw_ini_get("user_interface.content_trans");
		if($type == 1)
		{
			$langid = aw_global_get("ct_lang_id");
		}
		else
		{
			$langid = aw_global_get("lang_id");
		}
		return $langid;
	}

	/** called when user clicks on banner, finds the correct banner according to the session id

		@comment
			also cleans the banner_ids table of all views older than 48 hours
	**/
	private function find_url($ss,$clid)
	{
		$langid = $this->get_lang_id();
		$bid = $this->db_fetch_field("SELECT bid FROM banner_ids WHERE ss = '$ss' AND clid = '$clid'","bid");
		if (!$bid)
		{
			$this->error_click();
		}
		// no need to do this every time. let's say it has a 1% probability of happening.
		srand ((double) microtime() * 10000000);
		$pb = rand(1,101);
		if ($pb == 69)
		{
			$this->db_query("DELETE FROM banner_ids WHERE tm < ".(time()-(48*3600)));
		}

		$ip = aw_global_get("HTTP_X_FORWARDED_FOR");
		if ($ip == "" || !(strpos($ip,"unknown") === false))
		{
			$ip = aw_global_get("REMOTE_ADDR");
		}

		$this->db_query("INSERT INTO banner_clicks (tm,bid,ip,clid,langid,refferer) VALUES(".time().",$bid,'$ip','$clid','$langid','".aw_global_get("HTTP_REFERER")."')");
		$this->db_query("UPDATE banners SET clicks=clicks+1 WHERE id = $bid");

		return obj($bid);
	}

	/** shows banner

		@attrib name=proc_banner nologin="1"

		@param id optional
		@param bid optional
		@param gid optional
		@param html optional
		@param ss optional
		@param click optional

	**/
	function proc_banner($arr)
	{
		extract($arr);
		if ($html)
		{
			// tagastame baasi kirjutatud htmli banneri naitamisex.
			if (!$gid)
			{
				die(t("No banner location id given"));
			}
			die($this->db_fetch_field("SELECT html FROM banner_clients WHERE id = $gid","html"));
		}
		else
		if ($click)
		{
			// redirect
			if ($this->can("view", $bid))
			{
				$this->add_click($bid);
				$ba = obj($bid);
				header("Location: ".$ba->trans_get_val("url"));
				die();
			}

			if ($gid)
			{
				$ret = $this->find_url($ss,$gid);
				header("Location: ".$ret->trans_get_val("url"));
				die();
			}
		}
		else
		{
			// show
			$ba = array();
			if ($bid)
			{
				$ba = $this->get($bid);
				if (!$ba)
				{
					$this->error_banner();
				}
			}

			if ($gid)
			{
				$ba = $this->get_grp($gid);
				if (!is_object($ba))
				{
					$this->error_banner();
				}
				$this->add_view($ba->id(),$ss,$noview,$gid);
			}

			if (!is_object($ba))
			{
				$this->error_banner();
			}

			$this->display_banner($ba);
		}
	}

	/** shows a transparent gif, used if any errors occur while showing banner **/
	private function error_banner($die = true)
	{
		if ($die)
		{
			header("Content-type: image/gif");
			readfile(aw_ini_get("baseurl")."/automatweb/images/trans.gif");
			die();
		}
	}

	/** redirects to the site, used when any errors occur when the user clicks on a banner **/
	private function error_click()
	{
		header("Location: ".aw_ini_get("baseurl"));
		die();
	}

	private function display_banner($o)
	{
		if (!$o->trans_get_val("banner_file"))
		{
			$this->error_banner();
		}

		$f_o = obj($o->trans_get_val("banner_file"));
		if ($f_o->class_id() == CL_IMAGE)
		{
			$i = get_instance(CL_IMAGE);
			$r = $i->get_image_by_id($f_o->id());
			$i->show(array(
				"file" => basename($r["file"])
			));
		}
		else
		if ($f_o->class_id() == CL_FLASH)
		{
			if ($o->prop("url") != "")
			{
				$url = $this->mk_my_orb("proc_banner", array("click" => 1, "id" => $o->id()));
				$f_o->set_prop("click_tag", $url);
			}
			$i = get_instance(CL_FLASH);
			$i->show(array(
				"file" => basename($f_o->prop("file"))
			));
		}
	}

	/** Returns the html for the given banner or banner location
		@attrib api=1 params=pos

		@param loc optional type=oid
			The banner location object oid. If this is given and not the banner, then it fids a banner from the location settings to display

		@param banner optionsl type=oid
			The oid of the banner to display. If not set, the location must be. If set, the location is ignored and the given banner is used.

		@returns
			The html for the banner
	**/
	function get_banner_html($loc, $banner = null, $interval = true)
	{
		if ($banner === null)
		{
			$banner = $this->get_grp($loc, false);
		}
		if ($banner)
		{
			$content = $banner->trans_get_val("banner_file");
			if (is_oid($content) && $this->can("view", $content))
			{
				if ($loc)
				{
					$this->add_simple_view($banner->id(), $loc);
				}
				$content_o = obj($content);
				$url = $this->mk_my_orb("proc_banner", array("click" => 1, "bid" => $banner->id()), "banner", false, false, "&");
				$target = $banner->prop("banner_new_win") ? "target=\"_blank\"" : "";
				switch($content_o->class_id())
				{
					case CL_IMAGE:
						$i = get_instance("image");
						$img = $i->get_url_by_id($content_o->id());
						if ($banner->prop("url") == "")
						{
							$html = "<img alt='' border='0' src='$img'/>";
						}
						else
						{
							$html = "<a $target href='$url'><img alt='' border='0' src='$img'/></a>";
						}
						break;

					case CL_FILE:
						$i = get_instance("file");
						$f_url = $i->get_url($content_o->id(), $content_o->name());
						if ($i->can_be_embedded($content_o))
						{
							$c_html = "<img border='0' src='$f_url' alt=''/>";
						}
						else
						{
							$c_html = $content_o->name();
						}
						if ($banner->prop("url") == "")
						{
							$html = $c_html;
						}
						else
						{
							$html = "<a $target href='$url'>$c_html</a>";
						}
						break;

					case CL_FLASH:
						$f = get_instance(CL_FLASH);
						if ($banner->prop("url") == "")
						{
							$html = $f->view(array(
								"id" => $content_o->id()
							));
						}
						else
						{
							$html = $f->view(array(
								"id" => $content_o->id(),
								"clickTAG" => $url
							));
						}
						break;

					case CL_EXTLINK:
						$l = get_instance(CL_EXTLINK);
						$html = $l->parse_alias(array("alias" => array("target" => $content_o->id())));
						break;

					case CL_DOCUMENT:
						$l = get_instance("document");
						$html = $l->gen_preview(array("docid" => $content_o->id(), "leadonly" => 1));
						break;

					default:
						$html = "";
				}
				if ($interval)
				{
					$location = obj($loc);
					if (($si = $location->prop("dynamic_switch_interval")) > 0)
					{
						return $this->intervalize_html($html, $si, $loc);
					}
				}
				return $html;
			}
		}
	}

	private function intervalize_html($html, $interval, $oid)
	{
		$html = "<div id=\"bloc$oid\">".$html."</div>";

		$html .= "<script type=\"text/javascript\">function bannerIntervalize".$oid."()
		{
			$.get(\"".$this->mk_my_orb("fetch_banner_content", array("loc" => $oid), "banner_client")."\", {}, function (data) { $(\"#bloc".$oid."\").html(data); setTimeout(\"bannerIntervalize".$oid."()\", ".($interval * 1000).");});
		} setTimeout(\"bannerIntervalize".$oid."()\", ".($interval * 1000).");</script>";
		return $html;
	}

	/** Inserts banner content into html
		@attrib api=1 params=pos

		@param html required type=string
			the html to put banners in, with the banner places marked

		@param list required type=array
			an array containing intems to replace with banners array { 0 => array { index => string to replace }, 1 => array { index => banner place oid } }
	**/
	function put_banners_in_html($html, $list)
	{
		foreach($list[0] as $idx => $repl)
		{
			$html = str_replace($repl, $this->get_banner_html($list[1][$idx]), $html);
		}
		return $html;
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}
}
