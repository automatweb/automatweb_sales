<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/applications/recycle_bin/recycle_bin.aw,v 1.31 2009/01/16 11:37:33 kristo Exp $
// recycle_bin.aw - Pr&uuml;gikast 
/*

@default table=objects

@classinfo syslog_type=ST_RECYCLE_BIN maintainer=kristo

@default group=recycle_list
	@property toolbar type=toolbar store=no no_caption=1 group=recycle_list,recycle_search
	@property recycle_table type=text store=no no_caption=1

@default group=recycle_search 
	@property s_name type=textbox 
	@caption Nimi

	@property s_comment type=textbox
	@caption Kommentaar

	@property s_class_id type=select multiple=1
	@caption Objektit&uuml;&uuml;p

	@property s_oid type=textbox
	@caption OID

	@property s_modifiedby type=textbox 
	@caption Kustutaja

	@property s_modified_from type=datetime_select
	@caption Kustutatud alates

	@property s_modified_to type=datetime_select
	@caption Kustutatud kuni

	@property submit_btn type=submit 
	@caption Otsi

	@property s_inf type=text subtitle=1
	@caption Otsingu tulemused

	@property s_res type=table no_caption=1

@default group=recycle_groups

	@property delete_grps type=relpicker reltype=RELTYPE_DEL_GRP automatic=1 multiple=1 store=connect
	@caption Grupid, kes saavad kustutada

	@property admin_grps type=relpicker reltype=RELTYPE_ADM_GRP automatic=1 multiple=1 store=connect
	@caption Grupid, kes saavad m&auml;&auml;ranguid muuta

@default group=recycle_autoclean

	@property do_autoclean type=checkbox ch_value=1 field=meta method=serialize
	@caption T&uuml;hjenda automaatselt

	@property autoclean_age type=select field=meta method=serialize
	@caption Kui vanad automaatselt kustutatakse

@groupinfo recycle submit=no caption="Pr&uuml;gikast"
@groupinfo recycle_list submit=no caption="Nimekiri" parent=recycle
@groupinfo recycle_search caption="Otsing" parent=recycle submit_method=get
@groupinfo recycle_settings caption="M&auml;&auml;rangud" 
	@groupinfo recycle_groups parent=recycle_settings caption="Grupid"
	@groupinfo recycle_autoclean parent=recycle_settings caption="Automaatne t&uuml;hjendamine"


@reltype DEL_GRP value=1 clid=CL_GROUP
@caption Kustutaja grupp

@reltype ADM_GRP value=2 clid=CL_GROUP
@caption Admin grupp

@reltype RESTORE_FOLDER value=3 clid=CL_MENU
@caption Taastamise kaust

*/
class recycle_bin extends class_base
{
	const AW_CLID = 856;

	function recycle_bin()
	{
		$this->init(array(
			"clid" => CL_RECYCLE_BIN,
		));
	}
	
	function callback_mod_tab($arr)
	{
		if($arr["id"] == "general")
		{
			return false;
		}

		if ($arr["new"] && $arr["id"] == "recycle_settings")
		{
			return false;
		}

		if (!$arr["new"] && $arr["id"] == "recycle_settings")
		{
			$o = $arr["obj_inst"];
			$admg = array();
			foreach($o->connections_from(array("type" => "RELTYPE_ADM_GRP")) as $c)
			{
				$admg[$c->prop("to")] = $c->prop("to");
			}
			$curgrps = aw_global_get("gidlist_oid");
			$rv = is_array($admg) && count($admg) ? false : true;
			foreach($curgrps as $grp)
			{
				if (isset($admg[$grp]))
				{
					$rv = true;
				}
			}
			return $rv;
		}
	}
	
	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "s_name":
			case "s_comment":
			case "s_class_id":
			case "s_modifiedby":
			case "s_oid":
				$prop["value"] = $arr["request"][$prop["name"]];
				break;

			case "s_modified_from":
			case "s_modified_to":
				load_vcl("date_edit");
				$ts = date_edit::get_timestamp($arr["request"][$prop["name"]]);
				$prop["value"] = $ts;
				break;

			case "toolbar":
				$this->do_toolbar(&$arr);
				break;

			case "recycle_table":
				$prop["value"] = $this->do_recycle_table($arr);
				break;
		
			case "s_res":
				$this->_do_s_res($arr);
				break;

			case "s_class_id":
				$prop["options"] = get_class_picker();
				break;

			case "autoclean_age":
				$prop["options"] = $this->get_autoclean_age_options();
				break;
		};
		return $retval;
	}

	function get_autoclean_age_options()
	{
		return array(
			7 => t("1 N&auml;dal"),
			14 => t("2 N&auml;dalat"),
			21 => t("3 N&auml;dalat"),
			31 => t("1 Kuu"),
			61 => t("2 Kuud"),
			92  => t("3 Kuud"),
			123 => t("4 Kuud"),
			160 => t("5 Kuud"),
			185 => t("6 Kuud"),
			365 => t("1 aasta"),
			720 => t("2 aastat")
		);
	}

	function _init_table(&$table)
	{
		$table->define_field(array(
			"name" => "icon",
			"caption" => t(""),
			"width" => 15,
		));
		
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));
		
		$table->define_field(array(
			"name" => "oid",
			"caption" => t("ID"),
			"sortable" => 1,
			"width" => 50,
			"numeric" => 1
		));
		
		$table->define_field(array(
			"name" => "restore",
			"caption" => t("Tegevus"),
		));
		
		$table->define_field(array(
			"name" => "class_id",
			"caption" => t("Objektit&uuml;&uuml;p"),
			"sortable" => 1,
			"width" => 1,
		));
		
		$table->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Kustutaja"),
			"sortable" => "1",
			"width" => 80,
			"align" => "center",
		));
		
		$table->define_field(array(
			"name" => "modified",
			"caption" => t("Kustutatud"),
			"sortable" => "1",
			"width" => 100,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.y - H:m:s",
			"align" => "center",
		));
		
		$table->define_chooser(array(
    		"name" => "mark",
    		"field" => "id",
    		"caption" => t("Vali"),
		));
	}

	function do_recycle_table($arr)
	{
		//$table = &$arr["prop"]["vcl_inst"];
		classload("vcl/table");
		$table = new aw_table();
	
		$this->_init_table($table);	
	
		$cnt = $this->db_fetch_field("SELECT count(*) as cnt FROM objects WHERE status=0 ", "cnt");
		
		if ($arr["request"]["sortby"] == "")
		{
			$arr["request"]["sortby"] = "modified";
		}

		if ($arr["request"]["sort_order"] == "")
		{
			$arr["request"]["sort_order"] = "desc";
		}

		$ob = " ORDER BY ".$arr["request"]["sortby"]." ".$arr["request"]["sort_order"];

		$lim = "LIMIT ".($arr["request"]["ft_page"] * 100).",".(100);

		$query = "SELECT * FROM objects WHERE status=0 AND site_id = ".aw_ini_get("site_id")." $ob ".$lim;
		$this->db_query($query);
		$rows = array();
		while ($row = $this->db_next())
		{
			$rows[$row["oid"]] = $row;
		}

		$this->_insert_tbl($rows, $table, $arr["obj_inst"]->id());

		return $table->draw_text_pageselector(array(
			"d_row_cnt" => $cnt,
			"records_per_page" => 100
		)).$table->draw();
	}

	function _insert_tbl($rows, &$table, $obj_id)
	{	
		$classes = aw_ini_get("classes");
		
		get_instance("core/icons");

		$paths = $this->_get_paths($rows);
		$popup_menu = new popup_menu();
		foreach($rows as $row)
		{
			$popup_menu->begin_menu("restore".$row["oid"]);
			$popup_menu->add_item(array(
				"text" => t("Taasta"),
				"link" => $this->mk_my_orb("restore_object", array(
					"oid" => $row["oid"],
					"id" => $obj_id,
					"return_url" => get_ru(),
				)),
			));
			$popup_menu->add_item(array(
				"text" => t("Taasta ka alamobjektid"),
				"link" => $this->mk_my_orb("restore_object", array(
					"oid" => $row["oid"],
					"id" => $obj_id,
					"return_url" => get_ru(),
					"subs" => 1,
				)),
			));
			$table->define_data(array(
				"name" => $row["name"],
				"modified" => $row["modified"],
				"modifiedby" => $row["modifiedby"],
				"oid" => $row["oid"],
				"id" => $row["oid"],
				"restore" => $popup_menu->get_menu(array(
					"text" => t("Taasta"),
				)),
				"class_id" => $classes[$row["class_id"]]["name"],
				"icon" => html::img(array(
					"url" => icons::get_icon_url($row["class_id"]),
					"alt" => $paths[$row["oid"]],
					"title" => $paths[$row["oid"]]
				))
			));	
		}
	 	$table->set_default_sorder("desc");
		$table->set_default_sortby("modified");

		$table->sort_by();		
	}
	
	function do_toolbar($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "restore",
			"tooltip" => t("Taasta"),
			"img" => "restore.gif",
		));
		$tb->add_menu_item(array(
			"parent" => "restore",
			"text" => t("Valitud objektid"),
			"link" => "#",
			"onClick" => "submit_changeform('restore_objects')",
		));		
		$tb->add_menu_item(array(
			"parent" => "restore",
			"text" => t("Objektid ja alamobjektid"),
			"link" => "#",
			"onClick" => "document.forms.changeform.subs.value=1; submit_changeform('restore_objects')",
		));
		$tb->add_button(array(
			"name" => "refresh",
			"img" => "refresh.gif",
			"tooltip" => t("Uuenda"),
			"url" => aw_url_change_var(array()),
		));

		$o = $arr["obj_inst"];
		$admg = array();
		foreach($o->connections_from(array("type" => "RELTYPE_DEL_GRP")) as $c)
		{
			$admg[$c->prop("to")] = $c->prop("to");
		}
		$curgrps = aw_global_get("gidlist_oid");
		$rv = is_array($admg) && count($admg) ? false : true;
		foreach($curgrps as $grp)
		{
			if (isset($admg[$grp]))
			{
				$rv = true;
			}
		}

		if ($rv)
		{
			$tb->add_button(array(
				"name" => "delete",
				"img" => "delete.gif",
				"tooltip" => t("Kustuta"),
				"action" => "final_delete",
				"confirm" => t("Kas olete 100% kindel et soovite valitud objekte l&otilde;plikult kustutada?")
			));

			$tb->add_button(array(
				"name" => "clear_all",
				"img" => "del_all.gif",
				"tooltip" => t("T&uuml;hjenda"),
				"action" => "clear_all",
				"confirm" => t("Ettevaatust! Objektid kustutatakse j&auml;&auml;davalt!")
			));
		}
	}

	function get_restore_folder($ob)
	{
		$conn = $ob->connections_from(array(
			"type" => "RELTYPE_RESTORE_FOLDER"
		));
		foreach($conn as $c)
		{
			$fo = obj($c->prop("to"));
		}
		return $fo;
	}

	function check_parent($oid, $ob)
	{
		$query = "SELECT parent FROM objects WHERE oid='".$oid."'";
		$p = $this->db_fetch_field($query, "parent");
		$query = "SELECT status FROM objects WHERE oid='".$p."'";
		$status = $this->db_fetch_field($query, "status");

		if($status < 1)
		{
			$fo = $this->get_restore_folder($ob);
			if(!$fo)
			{
				return 0;
			}
			$this->db_query("UPDATE objects SET parent='".$fo->id()."' WHERE oid='".$oid."'");
		}
		return 1;
	}

	function check_subs($oid)
	{
		$query = "SELECT oid FROM objects WHERE parent='".$oid."'";
		$subs = $this->db_fetch_array($query);
		foreach($subs as $sub)
		{
			$oid = $sub["oid"];
			$query = "UPDATE objects SET status=1 WHERE oid =".$oid;
			$this->db_query($query);
			$this->check_subs($oid);
		}
	}

	/**
		@attrib name=restore_object all_args=1
	**/
	function restore_object($arr)
	{
		$query = "UPDATE objects SET status=1 WHERE oid =".$arr['oid'];
		$this->db_query($query);
		// clear cache
		$c = get_instance("cache");
		$c->file_clear_pt("acl");
		
		$ob = obj($arr["id"]);
		$oid = $arr["oid"];
		$this->check_parent($oid, $ob);
		if($arr["subs"])
		{
			$this->check_subs($oid);
		}

		return $arr["return_url"];
	}
	
	/**
		@attrib name=restore_objects
	**/
	function restore_objects($arr)
	{
		if (count($arr) == 0)
		{
			$arr = $_GET;
		}
		$ob = obj($arr["id"]);

		if($arr["mark"])
		{
			foreach($arr["mark"] as $oid)
			{
				if(!$this->check_parent($oid, $ob))
				{
					continue;
				}

				$query = "UPDATE objects SET status=1 WHERE oid=$oid";
				$this->db_query($query);
				if($arr["subs"])
				{
					$this->check_subs($oid);
				}
			}
		}

		// clear cache
		$c = get_instance("cache");
		$c->file_clear_pt("acl");

		return aw_ini_get("baseurl").$arr["return_url"];
	}

	function _get_paths($rows)
	{
		$o2n = array();
		foreach($rows as $row)
		{
			$o2n[$row["oid"]] = array($row["name"], $row["parent"]);
			$o2n[$row["parent"]] = array(NULL, NULL);
		}

		$max_cnt = 50 + count($o2n);
		while ($this->_fetch($o2n))
		{
			if (--$max_cnt < 1)
			{
				break;
			}
		}

		$adm = cfg_get_admin_rootmenu2();

		$ret = array();
		foreach($rows as $row)
		{
			$pt = array();
			$id = $o2n[$row["oid"]][1];
			while ($o2n[$id][1] && $id != $adm)
			{
				$pt[] = $o2n[$id][0];
				$id = $o2n[$id][1];
			}
			$ret[$row["oid"]] = join(" / ", array_reverse($pt));
		}
		return $ret;
	}

	function _fetch(&$o2n)
	{
		$ids = array();
		foreach($o2n as $id => $n)
		{
			if ($n[0] === NULL && $id)
			{
				$ids[] = $id;
			}
		}

		if (!count($ids))
		{
			return false;
		}

		$sql = "SELECT oid,name,parent FROM objects WHERE oid in (".join(",", $ids).")";
		$this->db_query($sql);
		while ($row = $this->db_next())
		{
			$o2n[$row["oid"]] = array($row["name"], $row["parent"]);
			if ($row["parent"] && !isset($o2n[$row["parent"]]))
			{
				$o2n[$row["parent"]] = array(NULL,NULL);
			}
		}
		return true;
	}

	/**

		@attrib name=final_delete

	**/
	function final_delete($arr)
	{
		if (count($arr) == 0)
		{
			$arr = $_GET;
		}
		$cl = aw_ini_get("classes");

		foreach(safe_array($arr["mark"]) as $id)
		{
			// get class
			$clid = $this->db_fetch_field("SELECT class_id FROM objects WHERE oid = '$id' AND status = 0", "class_id");
			if (!$clid)
			{
				continue;
			}
			
			// load props by clid
			$file = $cl[$clid]["file"];
			if ($clid == 29)
			{
				$file = "doc";
			}

			list($properties, $tableinfo, $relinfo) = $GLOBALS["object_loader"]->load_properties(array(
				"file" => basename($file),
				"clid" => $clid
			));

			$tableinfo = safe_array($tableinfo);
			$tableinfo["objects"] = array(
				"index" => "oid"
			);
			foreach($tableinfo as $tbl => $inf)
			{
				$sql = "DELETE FROM $tbl WHERE $inf[index] = '$id' LIMIT 1";
				$this->db_query($sql);
			}

			// also, aliases
			$this->db_query("DELETE FROM aliases WHERE source = '$id' OR target = '$id'");
			$this->db_query("DELETE FROM hits WHERE oid = '$id'");
			$this->db_query("DELETE FROM acl WHERE oid = '$id'");
		}

		return aw_ini_get("baseurl").$arr["return_url"];
	}

	/** clears all deleted objects from the bin. sorta dangerous or something.

		@attrib name=clear_all

	**/
	function clear_all($arr)
	{
		if (count($arr) == 0)
		{
			$arr = $_GET;
		}

		// get list of all deleted objects
		$query = "SELECT oid FROM objects WHERE status=0 AND site_id = ".aw_ini_get("site_id");
		$this->db_query($query);
		while ($row = $this->db_next())
		{
			$arr["mark"][$row["oid"]] = $row["oid"];
		}
		// feed it to final_delete
		return $this->final_delete($arr);
	}

	function _do_s_res($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_table($t);

		$where = array();
		if (!empty($arr["request"]["s_name"]))
		{
			$where[] = " name LIKE '%".$arr["request"]["s_name"]."%' ";
		}

		if (!empty($arr["request"]["s_comment"]))
		{
			$where[] = " comment LIKE '%".$arr["request"]["s_comment"]."%' ";
		}

		if (!empty($arr["request"]["s_oid"]))
		{
			$where[] = " oid = '".$arr["request"]["s_oid"]."' ";
		}

		if (!empty($arr["request"]["s_class_id"]) && is_array($arr["request"]["s_class_id"]) && count($arr["request"]["s_class_id"]))
		{
			$awa = new aw_array($arr["request"]["s_class_id"]);
			$where[] = " class_id IN (".$awa->to_sql().") ";
		}

		if (!empty($arr["request"]["s_modifiedby"]))
		{
			$where[] = " modifiedby LIKE '%".$arr["request"]["s_modifiedby"]."%' ";
		}

		load_vcl("date_edit");
		$ts = date_edit::get_timestamp($arr["request"]["s_modified_from"]);
		if ($ts > 1)
		{
			$where[] = " modified >=  $ts ";
		}

		$ts = date_edit::get_timestamp($arr["request"]["s_modified_to"]);
		if ($ts > 1)
		{
			$where[] = " modified <=  $ts ";
		}

		if (count($where) == 0)
		{
			return;
		}

		// get results
		$sql = "SELECT * FROM objects WHERE status = 0 AND site_id = ".aw_ini_get("site_id")." AND ".join(" AND ", $where);
		$this->db_query($sql);
		$rows = array();
		while ($row = $this->db_next())
		{
			$rows[$row["oid"]] = $row;
		}
		$this->_insert_tbl($rows, $t, $arr["obj_inst"]->id());
	}

	function callback_mod_reforb($arr)
	{
		$arr["return_url"] = aw_global_get("REQUEST_URI");
		$arr["new"] = ($_GET["action"]=="new")?1:0;
		$arr["subs"] = 0;
	}

	/**
		@attrib name=do_autoclean nologin="1"
	**/
	function do_autoclean()
	{
		// list all trash cans
		$ol = new object_list(array(
			"class_id" => CL_RECYCLE_BIN,
			"lang_id" => array()
		));
		if (!$ol->count())
		{
			continue;
		}
		$do_clean = 0;
		foreach($ol->arr() as $o)
		{
			if ($o->prop("do_autoclean") && $o->prop("autoclean_age") > 0)
			{
				$do_clean = $o->prop("autoclean_age");
			}
		}

		if ($do_clean > 1)
		{
			$this->_do_clean($do_clean);
		}
	}

	function _do_clean($max_age)
	{
		$where = array();
		$ts = time() - ($max_age * 24 * 3600);
		$where[] = " modified <=  $ts ";

		$sql = "SELECT * FROM objects WHERE status = 0 AND site_id = ".aw_ini_get("site_id")." AND ".join(" AND ", $where);
		$this->db_query($sql);
		$rows = array();
		while ($row = $this->db_next())
		{
			// aaand delete
			echo "delete $row[oid] - $row[name] mod = ".date("d.m.Y H:i", $row["modified"])."<br>";
			$rows[] = $row["oid"];
		}
		if (count($rows))
		{
			$this->final_delete(array("mark" => $rows));
		}
	}
}
?>
