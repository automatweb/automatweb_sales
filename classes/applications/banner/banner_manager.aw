<?php
/*
@classinfo syslog_type=ST_BANNER_MANAGER relationmgr=yes no_comment=1 no_status=1 prop_cb=1 maintainer=kristo
@default table=objects

@default group=general

	@property container_folder type=relpicker reltype=RELTYPE_CONTAINER_FOLDER field=meta method=serialize
	@caption Konteinerite kaust

	@property document_templates type=relpicker multiple=1 reltype=RELTYPE_DOC_TPL field=meta method=serialize
	@caption Dokumendi mallid

	@property container_places type=select multiple=1 field=meta method=serialize
	@caption Konteinerite paigad


@default group=mgr

	@property mgr_tb type=toolbar no_caption=1 store=no

	@layout mgr_v_split type=hbox width=20%:80%

		@layout mgr_tree_l type=vbox closeable=1 area_caption=Asukohad parent=mgr_v_split

			@property mgr_tree parent=mgr_tree_l store=no no_caption=1 type=treeview

		@layout mgr_tbls type=vbox parent=mgr_v_split

			@property mgr_tbl_bans type=table store=no no_caption=1 parent=mgr_tbls

			@property mgr_tbl_locs type=table store=no no_caption=1 parent=mgr_tbls

@default group=month_overview
	@property mth_ovr_tb type=toolbar no_caption=1 store=no

	@layout mth_ovr_split type=hbox width=20%:80%
		@layout mth_ovr_left type=vbox closeable=1 area_caption=Periood parent=mth_ovr_split
			@property mth_ovr_tree type=treeview store=no no_caption=1 parent=mth_ovr_left

		@layout mth_ovr_right type=vbox closeable=1 area_caption=Bannerid parent=mth_ovr_split
			@property mth_ovr_tbl type=table store=no no_caption=1 parent=mth_ovr_right

@groupinfo mgr caption="Haldus" submit=no 
@groupinfo month_overview caption="Kuu&uuml;levaade" submit=no

@reltype CONTAINER_FOLDER value=1 clid=CL_MENU
@caption Konteinerite kaust

@reltype DOC_TPL value=2 clid=CL_CONFIG_AW_DOCUMENT_TEMPLATE
@caption Dokumendi mall

*/

class banner_manager extends class_base
{
	function banner_manager()
	{
		$this->init(array(
			"tpldir" => "applications/banner/banner_manager",
			"clid" => CL_BANNER_MANAGER
		));

		$this->mth_overview_tbl = "banner_monthly_overview";
		$this->clicks = "banner_clicks";
		$this->views = "banner_views";
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}
		return $retval;
	}	

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}


	function _get_mth_ovr_tb(&$arr)
	{
		$arr["prop"]["vcl_inst"]->add_button(array(
			"name" => "gen_overview",
			"tooltip" => t("Genereeri uued andmed"),
			"img" => "archive_small.gif",
			"url" => $this->mk_my_orb("gen_monthly_overview", array(
				"rurl" => get_ru(),
				"class_id" => CL_BANNER_MANAGER,
			)),
		));
	}

	function _get_mth_ovr_tree(&$arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->set_root_name(t("Kuud"));
		$t->set_root_icon(icons::get_icon_url("archive_small.gif"));
		$s = $this->_get_banner_first_action();
		$e = $this->_get_banner_last_action();
		for($y = date("Y", $s); $y <= date("Y", $e); $y++)
		{
			$t->add_item(0, array(
				"id" => $y,
				"name" => $y,
				"action" => "#",
				"url" => "#",
			));
			$m = ($y == date("Y", $s))?date("n",$s):1;
			$me = ($y == date("Y", $e))?date("n",$e):12;
			for($m; $m <= $me; $m++)
			{
				$args = array(
					"class_id" => CL_BANNER_MANAGER,
					"id" => $arr["obj_inst"]->id(),
					"group" => $arr["request"]["group"],
					"month" => $m,
					"year" => $y,
				);
				$t->add_item($y, array(
					"id" => $y."_".$m,
					"name" => date("M", mktime(0,0,0,($m + 1),0,$y)),
					"url" => $this->mk_my_orb("change", $args),
				));
			}
		}
	}

	/** Generates monthly overview to separate table from banner vlicks & banner_views
		@attrib params=name api=1 name=gen_monthly_overview_from_last_months all_args=1
		@param months optional type=int default=3
			number of last months from what the conversion will be done
	 **/
	public function gen_montly_overview_from_last_months($arr)
	{
		$mth = $arr["months"]?$arr["months"]:3;
		$this->_gen_ovrview_conversion(1, $mth);
		$this->_gen_ovrview_conversion(2, $mth);
		return $arr["rurl"];
	}

	/** Generates monthly overview to separate table from banner_clicks & banner_views
		@attrib paramas=name api=1 name=gen_monthly_overview all_args=1
	 **/
	public function gen_monthly_overview($arr)
	{
		$this->_gen_ovrview_conversion(1);
		$this->_gen_ovrview_conversion(2);
		return $arr["rurl"];
	}

	private function _check_mth_ovrview_tbl()
	{
		$this->db_query("SHOW TABLES");
		$found = false;
		while($row = $this->db_next())
		{
			$tbl = reset($row);
			if($tbl == $this->mth_overview_tbl)
			{
				$found = true;
				break;
			}
		}
		if(!$found)
		{
			$ovr_fields = array(
				"banner_oid" => "int",
				"year" => "int",
				"month" => "int",
				"type" => "int",
				"langid" => "int",
				"count" => "int",
			);

			foreach($ovr_fields as $f => $t)
			{
				$cfields[] = "`".$f."` ".$t;
				$ifields[] = "`".$f."`";
			}
			
			$cfieldsql = implode(", ", $cfields);
			$ifieldsql = implode(", ", $ifields);

			$this->db_query("CREATE TABLE ".$this->mth_overview_tbl." (`oid` int primary key, ".$cfieldsql.")");
		}
	}

	private function _gen_ovrview_conversion($t, $mth = false)
	{
		$this->_check_mth_ovrview_tbl();
		$type = array(
			1 => $this->views,
			2 => $this->clicks,
		);
		//$this->db_query("SELECT * FROM ".$type[$t]);
		if($mth)
		{
			$m = date("n") - ($mth - 1);
			$tm = mktime(0,0,0, $m, 0, date("Y"));
			$where = " WHERE tm > ".$tm;
		}
		else
		{
			$where = "";
		}
		$this->db_query("select bid,langid, month(from_unixtime(tm)) as month, year(from_unixtime(tm)) as year, count(concat(year(from_unixtime(tm)), month(from_unixtime(tm)))) as kokku from ".$type[$t]." group by concat(year(from_unixtime(tm)),month(from_unixtime(tm))),bid,langid". $where);
		while($row = $this->db_next())
		{
			$data[$row["bid"].".".$row["year"].".".$row["month"].".".$row["langid"].".".$t] += $row["kokku"];
		}
		$this->db_query("SELECT * FROM ".$this->mth_overview_tbl);
		while($row = $this->db_next())
		{
			$exist[$row["banner_oid"].".".$row["year"].".".$row["month"].".".$row["langid"].".".$row["type"]] = $row["count"];
		}
		foreach($data as $k => $val)
		{
			$spl = split("[.]", $k);
			if(array_key_exists($k, $exist))
			{
				$sql = "UPDATE ".$this->mth_overview_tbl." SET count='".($exist[$k] + $val)."' WHERE banner_oid='".$spl[0]."' AND year='".$spl[1]."' AND month='".$spl[2]."' AND type='".$spl[4]."' AND langid='".$spl[3]."'";
			}
			else
			{
				$sql = "INSERT INTO ".$this->mth_overview_tbl." values(0, '".$spl[0]."', '".$spl[1]."', '".$spl[2]."', '".$spl[4]."', '".$spl[3]."', '".$val."')";
			}
			$this->db_query($sql);
		}
		$remove = "DELETE FROM ".$type[$t];
		$this->db_query($remove);
	}


	private function _get_overview($arr)
	{
		$arr["year"] = $arr["year"]?$arr["year"]:0;
		$arr["month"] = $arr["month"]?++$arr["month"]:0;
		$this->db_query("SELECT * FROM ".$this->mth_overview_tbl." WHERE year='".$arr["year"]."' AND month='".$arr["month"]."'");
		while($row = $this->db_next())
		{
			$return[$row["banner_oid"]][$row["langid"]][$row["type"]] = $row;
		}
		return $return;
	}

	private function _get_banner_last_action()
	{
		$this->db_query("SELECT MAX(CONCAT(year,LPAD(month, 2, 0))) as comp FROM ".$this->mth_overview_tbl);
		$r = $this->db_next();
		return mktime(0,0,0, substr($r["comp"], 4, 2), 0, substr($r["comp"],0, 4));
	}

	private function _get_banner_first_action()
	{
		$this->db_query("SELECT MIN(CONCAT(year,LPAD(month, 2, 0))) as comp FROM ".$this->mth_overview_tbl);
		$r = $this->db_next();
		return mktime(0,0,0, substr($r["comp"], 4, 2), 0, substr($r["comp"],0, 4));
	}

	private function _get_distinct_langs_for_month($arr)
	{
		$this->db_query("SELECT DISTINCT(langid) as langid from ".$this->mth_overview_tbl);
		while($row = $this->db_next())
		{
			$langs[$row["langid"]] = $row["langid"];
		}
		return $langs;
	}

	private function _init_mth_ovr_tbl(&$arr)
	{
		//error_reporting(E_ALL);
		//ini_set("display_errors", "1");
		//$langs = get_instance(CL_LANGUAGES);
		//$langs = $langs->get_list();
		$t =& $arr["prop"]["vcl_inst"];
		if($arr["request"]["month"] && $arr["request"]["year"])
		{
			$t->define_header(date("M Y", mktime(0,0,0, ($arr["request"]["month"] +1), 0, $arr["request"]["year"])));
		}
		$t->define_field(array(
			"name" => "banner",
			"caption" => t("B&auml;nner"),
		));
		$langs = get_instance("languages")->get_list();
		foreach($this->_get_distinct_langs_for_month($time_arr) as $lang)
		{
			$t->define_field(array(
				"name" => "lang_".$lang,
				"caption" => $langs[$lang],
			));
			$t->define_field(array(
				"name" => "views[".$lang."]",
				"caption" => t("Vaatamiste arv"),
				"parent" => "lang_".$lang,
			));
			$t->define_field(array(
				"name" => "clicks[".$lang."]",
				"caption" => t("Klikkide arv"),
				"parent" => "lang_".$lang,
			));
		}
	}

	function _get_mth_ovr_tbl(&$arr)
	{
		$this->_init_mth_ovr_tbl($arr);
		$t =& $arr["prop"]["vcl_inst"];
		$v = $this->_get_overview($arr["request"]);
		foreach($v as $banner => $data)
		{
			foreach($data as $lang => $types)
			{
				$d["clicks[".$lang."]"] = $types[2]["count"];
				$d["views[".$lang."]"] = $types[1]["count"];
			}
			$d["banner"] = html::obj_change_url(obj($banner));
			$t->define_data($d);
		}
	}

	function _get_container_places($arr)
	{
		$v = aw_ini_get("promo.areas");
		$t = array();
		foreach($v as $k => $d)
		{
			$t[$k] = $d["name"];
		}
		$arr["prop"]["options"] = $t;
	}

	function _get_mgr_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa")
		));
		$clss = aw_ini_get("classes");
		if ($arr["request"]["container"])
		{
			$pseh = aw_register_ps_event_handler(CL_BANNER_MANAGER, "pseh_second_banner_loc", array("mgr" => $arr["obj_inst"]->id(), "pt" => $arr["request"]["container"]), CL_BANNER_CLIENT);
		}
		else
		{
			$pseh = aw_register_ps_event_handler(CL_BANNER_MANAGER, "pseh_banner_loc", array("mgr" => $arr["obj_inst"]->id()), CL_BANNER_CLIENT);
		}
		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => $clss[CL_BANNER_CLIENT]["name"],
			"url" => html::get_new_url(CL_BANNER_CLIENT, $arr["obj_inst"]->prop("container_folder"), array(
				"return_url" => get_ru(),
				"pseh" => $pseh,
				"mgr" => $arr["obj_inst"]->id()
			))
		));

		// now, if nothing is clicked, no banners can be added
		// if a container is selected (container==tf) then banners can be added under all places in container
		// if a place is selected, then only in that place banners can be added
		if ($arr["request"]["tf"] && $arr["request"]["tf"] == $arr["request"]["container"])
		{
			$places = $this->_get_places_for_container($arr["request"]["tf"]);
			if (count($places) > 1)
			{
				$tb->add_sub_menu(array(	
					"parent" => "new",
					"name" => "ban",
					"text" => t("Banner")
				));
				foreach($places as $place_id)
				{
					$po = obj($place_id);
					$tb->add_menu_item(array(
						"parent" => "ban",
						"text" => $po->name(),
						"url" => html::get_new_url(CL_BANNER, $place_id, array(
							"return_url" => get_ru(),
							"pseh" => aw_register_ps_event_handler(CL_BANNER_MANAGER, "pseh_add_banner", array("place" => $place_id), CL_BANNER)
						))
					));
				}
			}
			else
			{
				$tb->add_menu_item(array(
					"parent" => "new",
					"text" => $clss[CL_BANNER]["name"],
					"url" => html::get_new_url(CL_BANNER, $arr["request"]["tf"], array(
						"return_url" => get_ru(),
						"pseh" => aw_register_ps_event_handler(CL_BANNER_MANAGER, "pseh_add_banner", array("place" => reset($places)), CL_BANNER)
					))
				));
			}
		}
		else
		if ($arr["request"]["tf"])
		{
			$tb->add_menu_item(array(
				"parent" => "new",
				"text" => $clss[CL_BANNER]["name"],
				"url" => html::get_new_url(CL_BANNER, $arr["request"]["tf"], array(
					"return_url" => get_ru(),
					"pseh" => aw_register_ps_event_handler(CL_BANNER_MANAGER, "pseh_add_banner", array("place" => $arr["request"]["tf"]), CL_BANNER)
				))
			));
		}
		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"action" => "save_stats",
			"tooltip" => t("Salvesta")
		));
		$tb->add_delete_button();
	}

	function pseh_add_banner($ban, $params)
	{
		$ban->connect(array(
			"to" => $params["place"],
			"type" => "RELTYPE_LOCATION"
		));
	}

	function pseh_second_banner_loc($o, $params)
	{
		// add another banner location to an already existing container
		// get document under container
		$ol = new object_list(array("class_id" => CL_DOCUMENT, "parent" => $params["pt"]));
		$doc = $ol->begin();

		if (!$doc)
		{
			$mgr = obj($params["mgr"]);
			$cont = obj($params["pt"]);
			$doc = obj();
			$doc->set_class_id(CL_DOCUMENT);
			$doc->set_parent($mgr->prop("container_folder"));
			$doc->set_name($cont->name());
			$doc->set_status(STAT_ACTIVE);
			$doc->set_prop("title", $cont->name());
			$doc->save();
		}

		$c = new connection();
		$c->change(array(
			"from" => $doc->id(),
			"to" => $o->id()
		));

		$conns = $c->find(array("from" => $doc->id(), "to.class_id" => CL_BANNER_CLIENT));

		$num = count($conns);
		
		$doc->set_prop("lead", $doc->prop("lead")."<br>#bannerplace".$num."#");
		$doc->save();
	}

	function pseh_banner_loc($o, $params)
	{
		$mgr = obj($params["mgr"]);
		
		// user created location, now we create container and document and connect them all together
		$osi = get_instance("install/object_script_interpreter");

$script = <<<EOT
	\$container = obj { class_id=CL_PROMO, parent=\${parent}, name=\${name}, status=STAT_ACTIVE, caption=\${name}, type=\${type}, tpl_lead=\${tpl_lead} }
		\$doc = obj { class_id=CL_DOCUMENT, parent=\${container}, name=\${name} status=STAT_ACTIVE, title=\${name}, lead="#bannerplace1#" }
		rel { from=\${doc} to=\${bannerplace} }
EOT;

		$osi->exec(array(
			"script" => $script,
			"vars" => array(
				"parent" => $mgr->prop("container_folder"),
				"name" => $o->name(),
				"type" => $_POST["cont_place"],
				"bannerplace" => $o->id(),
				"tpl_lead" => $_POST["cont_doc_tpl"]
			),
			"silent" => true
		));
	}

	private function get_cont_locs($cont, $t, $parent, $arr)
	{
		
		// get document under promo and list all connections from that to bannerplaces
		$ol = new object_list(array("class_id" => CL_DOCUMENT, "parent" => $cont->id()));
		$doc = $ol->begin();
		if ($doc)
		{
			$c = new connection();
			$conns = $c->find(array("from" => $doc->id(), "to.class_id" => CL_BANNER_CLIENT));
			if (count($conns) > 0)
			{
				foreach($conns as $con)
				{
					$o = obj($con["to"]);
					$url = html::get_change_url( $arr["obj_id"], array(
						"group" => $arr["active_group"],
						"container" => $cont->id(),
						"tf" => $con["to"]
					));
					$t->add_item($parent,array(
						"id" => $con["to"],
						"iconurl" => icons::get_icon_url($o->class_id()),
						"name" => $arr["tf"] == $con["to"] ? "<b>".$con["to.name"]."</b>" : $con["to.name"],
						"url" => $url
					));
				}
			}
		}
	}

	/**
		@attrib name=mgr_tree_func all_args=1
	**/
	function mgr_tree_func($arr)
	{
		
		$t = get_instance("vcl/treeview");
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"get_branch_func" => $this->mk_my_orb(array(
				"inst_id" => $arr["inst_id"],
				"active_group" => $arr["active_group"],
				"obj_id" => $arr["obj_id"],
				"tf" => $arr["tf"],
				"parent" => "0",
			))
		));
		$o = obj((int)$arr["parent"]);
		if($o->class_id() == CL_PROMO)
		{
			$this->get_cont_locs($o, $t, 0, $arr);
		}
		else
		{
			$cont_list = new object_list(array(
				"class_id" => array(CL_PROMO, CL_MENU),
				"parent" => (int)$arr["parent"]
			));
			foreach($cont_list->arr() as $cont)
			{
				$url = html::get_change_url( $arr["obj_id"], array(
					"group" => $arr["active_group"],
					"container" => $cont->id(),
					"tf" => $cont->id()
				));
				$t->add_item(0, array(
					"id" => $cont->id(),
					"iconurl" => icons::get_icon_url($cont->class_id()),
					"name" => $arr["tf"] == $cont->id() ? "<b>".$cont->name()."</b>" : $cont->name(),
					"url" => $url,
				));
				if($cont->class_id()==CL_PROMO)
				{
					$this->get_cont_locs($cont, $t, $cont->id(), $arr);
				}
			}
		}
		die($t->finalize_tree());
	}

	function _get_mgr_tree($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		
		$t->set_root_name(t("K&otilde;ik asukohad"));
		$t->set_root_url(aw_url_change_var(array("tf" => null, "container" => null)));
		$t->set_root_icon(icons::get_icon_url(CL_MENU));
		$oid = $arr["obj_inst"]->id();
		$t->set_branch_func($this->mk_my_orb("mgr_tree_func", array(
			"active_group" => $arr["request"]["group"],
			"obj_id" => $oid,
			"tf" => $arr["request"]["tf"],
			"parent" => "0",
		)));
		// list all containers in the selected folder 
		$cont_list = new object_list(array(
			"class_id" => array(CL_PROMO, CL_MENU),
			"parent" => $arr["obj_inst"]->prop("container_folder") 
		));
		foreach($cont_list->arr() as $cont)
		{
			$t->add_item(0, array(
				"id" => $cont->id(),
				"iconurl" => icons::get_icon_url($cont->class_id()),
				"name" => $arr["request"]["tf"] == $cont->id() ? "<b>".$cont->name()."</b>" : $cont->name(),
				"url" => aw_url_change_var(array("tf" => $cont->id(), "container" => $cont->id()))
			));
			if($cont->class_id()==CL_PROMO)
			{
				$this->get_cont_locs($cont, $t, $cont->id(), array(
					"active_group"  => $arr["request"]["group"],
					"tf" => $arr["request"]["tf"],
					"obj_id" => $oid
				));
			}
			else
			{
				$ol = new object_list(array(
					"class_id" => array(CL_PROMO, CL_MENU),
					"parent" => $cont->id()
				));
				foreach($ol->arr() as $o)
				{
					$t->add_item($cont->id(), array(
						"id" => $o->id(),
						"name" => $arr["request"]["tf"] == $o->id() ? "<b>".$o->name()."</b>" : $cont->name(),
						"url" => aw_url_change_var(array("tf" => $o->id(), "container" => $o->id()))
					));
				}
			}
		}
	}

	private function _init_locs_tbl($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Aktiivne?"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "show_locs",
			"caption" => t("Kus n&auml;idatakse"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "tools",
			"caption" => "",
			"align" => "center",
			"width" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_mgr_tbl_locs($arr)
	{
		if ($arr["request"]["tf"] && $arr["request"]["tf"] != $arr["request"]["container"])
		{
			return PROP_IGNORE;
		}

		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_locs_tbl($t);
		$t->set_caption(t("Bannerite n&auml;itamise kohad"));

		$pl2cont = array();
		if (!$arr["request"]["tf"])
		{
			$ol = new object_list(array(
				"class_id" => CL_PROMO,
				"parent" => $arr["obj_inst"]->prop("container_folder")
			));
			$places = array();
			foreach($ol->arr() as $o)
			{
				$tmp = $this->_get_places_for_container($o->id());
				foreach($tmp as $plid)
				{
					$places[] = $plid;
					$pl2cont[$plid] = $o->id();
				}
			}
		}
		else
		{
			$places = $this->_get_places_for_container($arr["request"]["tf"]);
			foreach($places as $plid)
			{
				$pl2cont[$plid] = $arr["request"]["tf"];
			}
		}

		foreach($places as $place_id)
		{
			$pl = obj($place_id);
			$cont = obj($pl2cont[$place_id]);
			if ($cont->prop("all_menus"))
			{
				$locs = t("Igal pool");
			}
			else
			{
				$conns = $cont->connections_from(array(
					"type" => "RELTYPE_ASSIGNED_MENU"
				));
				$locs = array();
				foreach($conns as $mnc)
				{
					$locs[] = $mnc->prop("to.name");
				}
				$locs = join(", ", $locs);

				if ($locs == "")
				{
					$locs = t("Mitte kuskil");
				}
			}

			$pm = get_instance("vcl/popup_menu");
			$pm->begin_menu("loc".$pl->id());
		
			$conns = $pl->connections_to(array(
				"from.class_id" => CL_DOCUMENT
			));
			$con = reset($conns);
			$doc = $con->from();

			$pm->add_item(array(
				"text" => t("Muuda dokumenti"),
				"link" => html::get_change_url($doc->id(), array("return_url" => get_ru()))
			));
			$pm->add_item(array(
				"text" => t("Muuda asukohta"),
				"link" => html::get_change_url($pl->id(), array("return_url" => get_ru()))
			));
			$pm->add_item(array(
				"text" => t("Muuda konteinerit"),
				"link" => html::get_change_url($pl2cont[$place_id], array("return_url" => get_ru()))
			));

			$t->define_data(array(
				"oid" => $place_id,
				"name" => html::obj_change_url($pl),
				"show_locs" => $locs." / ".html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $pl2cont[$place_id], 
						"group" => "container_locations",
						"return_url" => get_ru()
					), CL_PROMO),
					"caption" => t("Muuda")
				)),
				"status" => html::checkbox(array(
					"name" => "status[".$cont->id()."]",
					"value" => 1,
					"checked" => $cont->status() == STAT_ACTIVE
				)).html::hidden(array(
					"name" => "prev_status[".$cont->id()."]",
					"value" => $cont->status()
				)),
				"tools" => $pm->get_menu()
			));
		}
	}

	private function _init_bans_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Aktiivne?"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "disp_probability",
			"caption" => t("N&auml;itamise t&otilde;en&auml;osus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "deactivate_at",
			"caption" => t("Aktiivne kuni"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "img",
			"caption" => t("Sisu"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Kuhu suunab"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "tools",
			"caption" => "",
			"align" => "center",
			"width" => 1
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_mgr_tbl_bans($arr)
	{
		if (!$arr["request"]["tf"])
		{
			return PROP_IGNORE;
		}
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_bans_t($t);

		// if a single location selected, get banners from that only, if container, get all banners from that
		if ($arr["request"]["tf"] == $arr["request"]["container"])
		{
			$places = $this->_get_places_for_container($arr["request"]["container"]);
			$plstrs = array();
			foreach($places as $place_id)
			{	
				$po  = obj($place_id);
				$plstrs[] = $po->name();
			}
			$t->set_caption(sprintf(t("Bannerid, mida n&auml;idatakse asukohtades %s"), join(", ", $plstrs)));
		}
		else
		{
			$places = array($arr["request"]["tf"]);
			$tfo = obj($arr["request"]["tf"]);
			$t->set_caption(sprintf(t("Bannerid, mida n&auml;idatakse asukohas %s"), $tfo->name()));
		}

		$c = new connection();
		if(count($places))
		$conns = $c->find(array(
			"from.class_id" => CL_BANNER,
			"type" => "RELTYPE_LOCATION",
			"to" => $places
		));
		$bi = get_instance(CL_BANNER);
		foreach($conns as $con)
		{
			$b = obj($con["from"]);
			$deac = -1;
			$timing = $b->get_first_obj_by_reltype("RELTYPE_TIMING");
			if ($timing)
			{
				$deac = $timing->prop("deactivate");
			}
			$pm = get_instance("vcl/popup_menu");
			$pm->begin_menu("ban".$b->id());
		
			$loc = $b->get_first_obj_by_reltype("RELTYPE_LOCATION");
			$conns = $loc->connections_to(array(
				"from.class_id" => CL_DOCUMENT
			));
			$con = reset($conns);
			$doc = $con->from();

			$pm->add_item(array(
				"text" => t("Muuda bannerit"),
				"link" => html::get_change_url($b->id(), array("return_url" => get_ru()))
			));
			$pm->add_item(array(
				"text" => t("Muuda dokumenti"),
				"link" => html::get_change_url($doc->id(), array("return_url" => get_ru()))
			));
			$pm->add_item(array(
				"text" => t("Muuda asukohta"),
				"link" => html::get_change_url($loc->id(), array("return_url" => get_ru()))
			));
			$pm->add_item(array(
				"text" => t("Muuda konteinerit"),
				"link" => html::get_change_url($doc->parent(), array("return_url" => get_ru()))
			));
			$t->define_data(array(
				"name" => html::obj_change_url($b),
				"img" => $bi->get_banner_html(null, $b),
				"location" => html::href(array(
					"url" => $b->prop("url"),
					"caption" => substr($b->prop("url"), 0, 50)
				)),
				"oid" => $b->id(),
				"status" => html::checkbox(array(
					"name" => "status[".$b->id()."]",
					"value" => 1,
					"checked" => $b->status() == STAT_ACTIVE
				)).html::hidden(array(
					"name" => "prev_status[".$b->id()."]",
					"value" => $b->status()
				)),
				"disp_probability" => html::textbox(array(
					"name" => "dp[".$b->id()."]",
					"value" => $b->prop("probability"),
					"size" => 5
				)),
				"deactivate_at" => html::date_select(array(
					"name" => "da[".$b->id()."]",
					"value" => $deac
				)),
				"tools" => $pm->get_menu()
			));
		}
	}

	private function _get_places_for_container($ct_id)
	{
		$o = obj($ct_id);
		$rv = array();
		if($o->class_id() == CL_PROMO)
		{
			$ol = new object_list(array("class_id" => CL_DOCUMENT, "parent" => $ct_id));
			$doc = $ol->begin();
	
			if ($doc)
			{
				$c = new connection();
				$conns = $c->find(array("from" => $doc->id(), "to.class_id" => CL_BANNER_CLIENT));
				foreach($conns as $con)
				{
					$rv[] = $con["to"];
				}
			}
		}
		return $rv;
	}			

	/** 
		@attrib name=save_stats
	**/
	function _set_mgr_tbl_bans($arr)
	{
		foreach($arr["prev_status"] as $oid => $stat)
		{
			if ($arr["status"][$oid] != ($stat-1))
			{
				$o = obj($oid);
				$o->set_status($arr["status"][$oid]+1);
				$o->save();
			}
		}


		foreach($arr["dp"] as $b_oid => $dp)
		{
			$bo = obj($b_oid);	
			if ($bo->prop("probability") != $dp)
			{
				$bo->set_prop("probability", $dp);
				$bo->save();
			}
		}

		foreach($arr["da"] as $b_oid => $tm)
		{
			$bo = obj($b_oid);
			$tms = date_edit::get_timestamp($tm);
			$timing = $bo->get_first_obj_by_reltype("RELTYPE_TIMING");
			if (!$timing && $tms != -1)
			{
				$timing = obj();
				$timing->set_class_id(CL_TIMING);
				$timing->set_parent($b_oid);
				$timing->save();
				$bo->connect(array(
					"to" => $timing->id(),
					"type" => "RELTYPE_TIMING"
				));
			}

			if ($timing && $tms != $timing->prop("deactivate"))
			{
				$timing->set_prop("deactivate", $tms);
				$timing->save();
			}
		}

		return $arr["post_ru"];
	}
}
?>
