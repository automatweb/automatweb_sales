<?php
// period.aw - periods
/*

	@default group=general
	@default table=objects

	@property name type=textbox
	@caption Nimetus (aasta,kuu)

	@property comment type=textbox
	@caption Kommentaar (teema)

	@property perimage type=relpicker reltype=RELTYPE_IMAGE rel_id=first use_form=emb field=meta method=serialize
	@caption Pilt2

	@property status type=status
	@caption Arhiivis

	@property syear type=hidden table=periods field=syear datatype=int

	@property per_id type=text table=periods field=id datatype=int
	@caption Perioodi id.

	@property per_oid type=hidden table=periods field=oid datatype=int
	@caption Oid

	@default field=meta
	@default method=serialize

	@property image_link type=textbox
	@caption Pildi link

	@property pyear type=select
	@caption Aasta

	@property contents_doc type=relpicker reltype=RELTYPE_CONTENTS_DOC table=objects field=meta method=serialize
	@caption Sisukorra dokument

	@property preview type=text store=no editonly=1
	@caption Eelvaade

	property activity type=callback callback=callback_get_activity_list group=activity no_caption=1
	caption Aktiivsus

	@property activity type=table group=activity no_caption=1
	@caption Aktiivsus

	@tableinfo periods index=obj_id master_table=objects master_index=oid
	@classinfo relationmgr=yes
	@groupinfo activity caption=Aktiivsus

	@reltype IMAGE value=1 clid=CL_IMAGE
	@caption Perioodi pilt

	@reltype CONTENTS_DOC value=2 clid=CL_DOCUMENT
	@caption Sisukorra dokument

*/

// perioodi pilt

class period extends class_base implements request_startup
{
	private $period_cache = array();

	function period($oid = 0)
	{
		$this->init(array(
			"clid" => CL_PERIOD,
			"tpldir" => "automatweb/periods",
		));
		if (!$oid)
		{
			$oid = $this->cfg["per_oid"];
		}

		$this->oid = $this->cfg["per_oid"];
		$this->cf_name = "periods-cache-site_id-".$this->cfg["site_id"]."-period-";
		$this->cf_ap_name = "active_period-cache-site_id-".$this->cfg["site_id"];
		$this->init_active_period_cache();
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "pyear":
				$opt = array();
				for($i = date("Y")-5; $i < date("Y")+6; $i++)
				{
					$opt[$i] = $i;
				}
				$data["options"] = $opt;
				break;

			case "preview":
				$perdat = $this->get_record("periods","obj_id",$arr["obj_inst"]->id());
				// mk_my_orb doesn't let me create URL's to the site from admin,
				// and I don't have time to fix it, so I have to do this.
				$url = $this->cfg["baseurl"] . "?class=contents&action=show&period=$perdat[id]";
				$data["value"] = html::href(array(
					"url" => $url,
					"caption" => $arr["prop"]["caption"],
					"target" => "_blank",
				));
				break;

			case "activity":
				$this->mk_activity_table($arr);
				break;

			case "per_oid":
				$data["value"] = aw_ini_get("per_oid");
				break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "activity":
				$this->activate_period($arr["request"]["activeperiod"],$this->oid);
				break;
		};
		return $retval;
	}

	function mk_activity_table($arr)
	{
		// this is supposed to return a list of all active periods,
		// to let the user choose the active one
		/*
		$table = new aw_table(array(
			"layout" => "generic",
			"xml_def" => "periods/list",
                ));
		*/
		$table = &$arr["prop"]["vcl_inst"];
		$table->parse_xml_def("periods/list");

		$active = $this->rec_get_active_period();
		$this->clist();
		while($row = $this->db_next())
		{
                        $actcheck = checked($row["id"] == $active);
                        $act_html = "<input type='radio' name='activeperiod' $actcheck value='$row[id]'>";
                        $row["active"] = $act_html;
                        $table->define_data($row);
		};
		$table->set_default_sortby("id");
		$table->set_default_sorder("desc");

		/*
		$tmp = $args["prop"];
		$tmp["value"] = $table->draw();
		$tmp["type"] = "text";
		return array($tmp);
		*/
	}

	function callback_post_save($arr)
	{
		if (!empty($args["new"]))
		{
			$q = sprintf("INSERT periods (oid,obj_id) VALUES (%d,%d)",$this->oid,$arr["obj_inst"]->id());
			$this->db_query($q);
		};
		$perdata = $this->db_fetch_row("SELECT id FROM periods WHERE obj_id = " . $arr["obj_inst"]->id());
		$id = $perdata["id"];
		cache::file_invalidate($this->cf_name.$id);
		aw_cache_set("per_by_id", $id, false);
		aw_global_set("aw_period_cache",0);
	}

	function init_active_period_cache()
	{
		if (($cc = cache::file_get($this->cf_ap_name)))
		{
			aw_cache_set_array("active_period",aw_unserialize($cc));
		}
	}

	function mk_percache()
	{
		if (!aw_global_get("aw_period_cache"))
		{
			$this->db_query("SELECT * FROM periods LEFT JOIN objects ON (periods.obj_id = objects.oid) WHERE status != 0");
			while ($row = $this->db_next())
			{
				$this->period_cache[$row["oid"]][] = $row;
			}
			aw_global_set("aw_period_cache",1);
		}
	}

	function clist($arc_only = -1)
	{
		$this->mk_percache();

		// read all periods from db and then compare the oids to the ones in the object chain for $oid
		$oid = $this->oid;
		$sufix = ($arc_only > -1) ? " AND status = 2 " : " AND status != 0 ";
		$valid_period = 0;
		if ($this->can("view", $this->oid))
		{
			$tmp = obj($this->oid);
			$ochain = array_reverse($tmp->path());
			// hm, but we must make sure we go from bottom to top always
			foreach($ochain as $chaino)
			{
				// now, if some periods exist for this object, use that object.
				if (isset($this->period_cache[$chaino->id()]) && is_array($this->period_cache[$chaino->id()]))
				{
					$valid_period = $chaino->id();
					break;
				}
			}
		}

		if (!$valid_period)
		{
			$valid_period = $this->oid;
		}
		// if no periods were found, attach them to the object - this happens if no periods exist for instance
		$q = "SELECT * FROM periods LEFT JOIN objects ON (periods.obj_id = objects.oid) WHERE periods.oid = '$valid_period'  $sufix ORDER BY objects.jrk DESC";
		$this->oid = $valid_period;
		$this->db_query($q);
	}

	function activate_period($id,$oid)
	{
		$q = "UPDATE menu SET active_period = '$id' WHERE id = '$oid'";
		$this->db_query($q);
		cache::file_invalidate($this->cf_ap_name);
		cache::file_clear_pt("html");
	}

	// see funktsioon tagastab k2igi eksisteerivate perioodide nimekirja
	// array kujul
	// $active muutujaga saab ette anda selle, milline periood peaks olema aktiivne
	// kui $active == 0, siis on selected see option, mis parajasti aktiivne on
	// kui $active == 'somethingelse', siis on selectitud vastava id-ga element
	function period_list($active, $addempty = false, $onlyarc = -1)
	{
		if ($active == 0)
		{
			$active = $this->get_cval("activeperiod");
		};
		$this->active = $active;
		$this->clist($onlyarc);
		if ($addempty)
		{
			$elements = array("0" => "");
		}
		else
		{
			$elements = array();
		}
		while($row = $this->db_next())
		{
			$elements[$row["id"]] = $row["name"];
		};
		return $elements;
	}

	function period_olist($active = 0)
	{
		return $this->picker($this->active,$this->period_list($active));
	}

	function period_mlist($active)
	{
		return $this->mpicker($this->active,$this->period_list($active));
	}

	function get_active_period($oid = 0)
	{
		if (!$oid)
		{
			$oid = $this->oid;
		};
		// ok, here we have problem - $ap could very well be empty and then we will think
		// that it is not in the cache.
		// so, to fix that we rewrite 0 to -1 :)
		// ok, basically when we would normally add a 0 to the cache, now we add -1
		// and when retrieving it, we act accordingly
		if (($ap = aw_cache_get("active_period", $oid)))
		{
			return ($ap == -1 ? 0 : $ap);
		}
		else
		{
			// the good bit about this is, that active_period is set only through this class, so we can
			// contain the cache flushing pretty well
			$q = "SELECT active_period FROM menu WHERE id = '".$oid."'";
			$ap = $this->db_fetch_field($q,"active_period");
			if (!$ap)
			{
				$ap = $this->rec_get_active_period(($oid ? $oid : -1));
			}

			// now add this period to the cache
			aw_cache_set("active_period", $oid,($ap == 0 ? -1 : $ap));

			// and also to the file-on-disk cache
			$str = aw_serialize(aw_cache_get_array("active_period"));
			cache::file_set($this->cf_ap_name,$str);
			return $ap;
		}
	}

	// ee, v6ib ju nii olla, et sellel sektsioonil pole aktiivset perioodi m22ratud, aga tema parentil on, niiet tuleb see otsida...
	function rec_get_active_period($oid = -1)
	{
		$oid = $oid == -1 ? $this->oid : $oid;
		do {
			$q = "SELECT menu.active_period as active_period,objects.parent as parent FROM menu left join objects on objects.oid = menu.id WHERE id = '"  . $oid."'";
			$this->db_query($q);
			$row = $this->db_fetch_row();
			$oid = $row["parent"];
		} while (!$row["active_period"] && $row["parent"] > 1);

		return $row["active_period"];
	}

	function get_id_for_oid($oid)
	{
		return $this->db_fetch_field("select id from periods where obj_id = '$oid'", "id");
	}

	function get_oid_for_id($id)
	{
		return $this->db_fetch_field("select obj_id from periods where id = '$id'", "obj_id");
	}

	////
	// !returns period $id
	function get($id)
	{
		$id = (int)$id;
		if (!$id)
		{
			return;
		}
		// 1st, the in-memory cache
		if (($pr = aw_cache_get("per_by_id", $id)))
		{
			return $pr;
		}
		// 2nd, the file-on-disk cache
		if (($cc = cache::file_get($this->cf_name.$id)))
		{
			$pr = aw_unserialize($cc);
			aw_cache_set("per_by_id", $id, $pr);
			return $pr;
		}
		// and finally, the db
		$q = "SELECT *,objects.name,objects.metadata,objects.status as o_status FROM periods LEFT JOIN objects ON (periods.obj_id = objects.oid) WHERE id = '$id'";
		$this->db_query($q);
		$pr = $this->db_fetch_row();
		$pr["data"] = aw_unserialize($pr["metadata"]);

		$pr_tmp = $pr;
		unset($pr_tmp["metadata"]);
		unset($pr_tmp["acldata"]);
		$str = aw_serialize($pr_tmp);
		cache::file_set($this->cf_name.$id, $str);
		aw_cache_set("per_by_id", $id, $pr);
		return $pr;
	}

	function request_startup()
	{
		// check if a period number was specified in the url
		$period = aw_global_get("period");
		if ($period)
		{
			// only let them switch to active periods if not logged in
			if (aw_global_get("uid") == "")
			{
				$pd = $this->get($period);
				if ($pd["o_status"] != STAT_ACTIVE)
				{
					$period = $this->get_active_period();
				}
			}
			// if it was, we should switch
			$act_per_id = $period;
			aw_session_set("act_per_id", $act_per_id);

			// now we check if the newly selected period is the active period -
			// yes, this will take a query, but this will not be done often, only when the user switches periods
			$r_act_per = $this->get_active_period();

			if ($r_act_per == $act_per_id)
			{
				$in_archive = false;
			}
			else
			{
				$in_archive = true;
			}
			$current_period = $this->get_active_period();
			aw_session_set("current_period", $current_period);
			aw_session_set("in_archive", $in_archive);
		}
		else
		{
			$current_period = $this->get_active_period();
			aw_session_set("current_period", $current_period);
			// no period specified in the url
			if (!aw_global_get("act_per_id"))
			{
				// and no period was previously active, pick the default.
				//$act_per_id = $this->get_active_period();
				$act_per_id = $current_period;
				aw_session_set("act_per_id", $act_per_id);
				$in_archive = false;
				aw_session_set("in_archive", $in_archive);
			}
			// if a period was previously active we just leave it like that
		}

		if (aw_global_get("uid") == "")
		{
			$pd = $this->get(aw_global_get("act_per_id"));
			if ($pd["o_status"] != STAT_ACTIVE)
			{
				aw_global_set("act_per_id", $this->get_active_period());
			}
		}

		if (($ap = aw_global_get("act_per_id")))
		{
			// and if after all this we have managed to figure out the active period we go and spoil it all by loading it
			aw_global_set("act_period",$this->get($ap));
		}
	}

	/**
		@attrib name=list params=name nologin="1" is_public="1" caption="List" default="1"
	**/
	function site_list($arr)
	{
		$this->read_template("arhiiv.tpl");
		$this->clist(1);
		$pyear = 0;
		while($row = $this->db_next())
		{
			$dat = aw_unserialize($row["metadata"]);
			if ($pyear != $dat["pyear"])
			{
				$this->vars(array(
					"pyear" => $dat["pyear"],
				));

				$content .= $this->parse("year");
				$pyear = $dat["pyear"];
			};
			$this->vars(array(
				"period" => $row["id"],
				"description" => $row["name"]
			));
			if ($row["id"] == aw_global_get("act_per_id"))
			{
				$content .= $this->parse("active");
			}
			else
			{
				$content .= $this->parse("passive");
			}
		}
		return $content;
	}

	function list_periods($args = array())
	{
		$this->clist();
		$retval = array();
		while($row = $this->db_next())
		{
			$row["data"] = aw_unserialize($row["metadata"]);
			$retval[] = $row;
		};
		return $retval;
	}

	////
	// !import period variables into main.tpl
	function on_site_show_import_vars($arr)
	{
		// PERF: the following code should run only if the site has periods
		$_t = aw_global_get("act_period");

		$imc = get_instance(CL_IMAGE);
		if ((!isset($_t["data"]["image"]) or !$this->can("view", $_t["data"]["image"])) && isset($_t["data"]["perimage"]) && $this->can("view", $_t["data"]["perimage"]))
		{
			$imdata = $imc->get_image_by_id($_t["data"]["perimage"]);
		}
		else
		{
			$imdata = $imc->get_image_by_id($_t["data"]["image"]);
		}
		$arr["inst"]->vars(array(
			"per_string" => $_t["name"],
			"act_per_id" => $_t["id"],
			"per_comment" => $_t["comment"],
			"def_per_id" => $this->get_active_period(),
			"per_img_url" => image::check_url($imdata["url"]),
			"per_img_tag" => image::make_img_tag(image::check_url($imdata["url"])),
			"per_img_link" => ($_t["data"]["image_link"] != "" ? $_t["data"]["image_link"] : aw_ini_get("baseurl"))
		));

		if (isset($_t["data"]["image"]["url"]) && $_t["data"]["image"]["url"] != "")
		{
			// so this is where it's done... eh
			// how do I convert this thing to an usual promo box?

			// so .. I need a special kind of promo box, that displays the
			// the active period as it's contents

			// so .. can I create a separate object out of it? Then what about the cache?
			$arr["inst"]->vars(array(
				"HAS_PERIOD_IMAGE" => $arr["inst"]->parse("HAS_PERIOD_IMAGE")
			));
		}

		if ($arr["inst"]->is_template("PERIOD_SWITCH"))
		{
			$per_inst = get_instance(CL_PERIOD);
			$plist = array_reverse($per_inst->period_list(0,false, 1), true);
			$next = false;
			$prev_per_id = 0;
			$next_per_id = 0;

			foreach($plist as $pid => $pname)
			{
				if ($next)
				{
					$next_per_id = $pid;
					$next_per_name = $pname;
					break;
				}

				if ($pid == $_t["id"])
				{
					$next = true;
					$prev_per_id = $prev;
					$prev_per_name = $prev_name;
				}

				$prev = $pid;
				$prev_name = $pname;
			}

			$arr["inst"]->vars(array(
				"prev_per_id" => $prev_per_id,
				"prev_per_name" => $prev_per_name,
				"prev_per_link" => aw_ini_get("baseurl")."/period=".$prev_per_id,
				"next_per_id" => $next_per_id,
				"next_per_name" => $next_per_name,
				"next_per_link" => aw_ini_get("baseurl")."/period=".$next_per_id,
			));

			$arr["inst"]->vars(array(
				"HAS_PREV_PERIOD" => ($prev_per_id ? $arr["inst"]->parse("HAS_PREV_PERIOD") : ""),
				"HAS_NEXT_PERIOD" => ($next_per_id ? $arr["inst"]->parse("HAS_NEXT_PERIOD") : ""),
			));

			$arr["inst"]->vars(array(
				"PERIOD_SWITCH" => $arr["inst"]->parse("PERIOD_SWITCH")
			));
		}

	}

	function parse_alias($arr = array())
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		$ap = aw_global_get("act_period");
		$oid = $ap["obj_id"];
		$o = obj($oid);
		$this->read_template("show.tpl");

		if ($o->prop("contents_doc"))
		{
			$link = aw_ini_get("baseurl")."/".$o->prop("contents_doc");
		}
		else
		{
			$link = aw_ini_get("baseurl")."/period=".$o->prop("per_id");
		}

		if ($o->prop("perimage"))
		{
			$i = get_instance(CL_IMAGE);
			$image = $i->make_img_tag($i->get_url_by_id($o->prop("perimage")));
		}
		else
		if ($o->meta("image"))
		{
			$i = get_instance(CL_IMAGE);
			$image = $i->make_img_tag($i->get_url_by_id($o->meta("image")));
		}
		else
		{
			$image = $o->name();
		}

		$this->vars(array(
			"name" => $o->name(),
			"link" => $link,
			"image" => $image
		));

		return $this->parse();
	}
}
