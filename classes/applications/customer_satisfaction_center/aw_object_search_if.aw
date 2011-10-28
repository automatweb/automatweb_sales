<?php

// aw_object_search_if.aw - AW Objektide otsing
/*

@classinfo no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=srch
@default store=no

	@property s_tb type=toolbar no_caption=1

	@layout ver_split type=hbox

		@layout left_side type=vbox parent=ver_split closeable=1 area_caption=&Uuml;ldandmed

			@property s_name type=textbox  size=50 parent=left_side
			@caption Nimi

			@property s_comment type=textbox  size=50 parent=left_side
			@caption Kommentaar

			@property s_clid type=select multiple=1 size=10 parent=left_side
			@caption T&uuml;&uuml;p

			@property s_oid type=textbox  size=50 parent=left_side
			@caption OID

			@property s_parent_search type=text parent=left_side
			@caption Asukoht

			@property s_status type=chooser parent=left_side
			@caption Aktiivsus

			@property s_alias type=textbox  size=50 parent=left_side
			@caption Alias

			@property s_language type=chooser parent=left_side
			@caption Keel

			@property s_period type=select parent=left_side
			@caption Periood

			@property s_site_id type=select parent=left_side
			@caption Saidi ID

			@property s_find_bros type=checkbox ch_value=1 parent=left_side
			@caption Leia vendi

		@layout right_side type=vbox parent=ver_split

			@layout creamod type=vbox closeable=1 area_caption=Muutmine&nbsp;ja&nbsp;lisamine parent=right_side

				@property s_creator type=textbox  size=20 parent=creamod
				@caption Looja

				@property s_creator_from type=chooser parent=creamod default=0 orient=vertical
				@caption Otsida loojat

				@property s_crea_from type=datetime_select parent=creamod default=-1
				@caption Lisatud alates

				@property s_crea_to type=datetime_select parent=creamod default=-1
				@caption Lisatud kuni

				@property s_modifier type=textbox  size=20 parent=creamod
				@caption Muutja

				@property s_modifier_from type=chooser parent=creamod default=0 orient=vertical
				@caption Otsida muutjat

				@property s_mod_from type=datetime_select parent=creamod default=-1
				@caption Muudetud alates

				@property s_mod_to type=datetime_select parent=creamod default=-1
				@caption Muudetud kuni

			@layout keywords type=vbox closeable=1 area_caption=M&auml;rks&otilde;nad parent=right_side

				@property s_kws type=textbox parent=keywords
				@caption M&auml;rks&otilde;nad

			@layout login type=vbox closeable=1 area_caption=teise&nbsp;saiti&nbsp;sisse&nbsp;logimise&nbsp;objekt parent=right_side

				@property login type=select parent=login
				@caption Logimise objekt


	@property s_sbt type=submit
	@caption Otsi

	@property s_res type=table no_caption=1


@default group=srch_complex
@default store=no

	@property s_tb1 type=toolbar no_caption=1

	@layout ver_split1 type=hbox

		@layout left_side1 type=vbox parent=ver_split1

			@layout left_side_top type=vbox parent=left_side1 closeable=1 area_caption=&Uuml;ldine

				@property s_name1 type=textbox  size=50 parent=left_side_top
				@caption Nimi

				@property s_comment1 type=textbox  size=50 parent=left_side_top
				@caption Kommentaar

				@property s_clid1 type=select multiple=1 size=10 parent=left_side_top
				@caption T&uuml;&uuml;p

				@property s_oid1 type=textbox  size=50 parent=left_side_top
				@caption OID

				@property s_parent_search1 type=text parent=left_side_top
				@caption Asukoht

				@property s_status1 type=chooser parent=left_side_top
				@caption Aktiivsus

				@property s_alias1 type=textbox  size=50 parent=left_side_top
				@caption Alias

				@property s_language1 type=chooser parent=left_side_top
				@caption Keel

				@property s_period1 type=select parent=left_side_top
				@caption Periood

				@property s_site_id1 type=select parent=left_side_top
				@caption Saidi ID

				@property s_find_bros1 type=checkbox ch_value=1 parent=left_side_top
				@caption Leia vendi

			@layout left_side_bottom type=vbox parent=left_side1 closeable=1 area_caption=Seosed

				@property s_rel_type1 type=select parent=left_side_bottom
				@caption Seose t&uuml;&uuml;p

				@property s_rel_obj_oid1 type=textbox size=6 parent=left_side_bottom
				@caption Seotud objekti id

				@property s_rel_obj_name1 type=textbox size=20 parent=left_side_bottom
				@caption Seotud objekti nimi

		@layout right_side1 type=vbox parent=ver_split1

			@layout creamod1 type=vbox closeable=1 area_caption=Muutmine&nbsp;ja&nbsp;lisamine parent=right_side1

				@property s_creator1 type=textbox  size=20 parent=creamod1
				@caption Looja

				@property s_creator_from1 type=chooser parent=creamod1 default=0 orient=vertical
				@caption Otsida loojat

				@property s_crea_from1 type=datetime_select parent=creamod1 default=-1
				@caption Lisatud alates

				@property s_crea_to1 type=datetime_select parent=creamod1 default=-1
				@caption Lisatud kuni

				@property s_modifier1 type=textbox  size=20 parent=creamod1
				@caption Muutja

				@property s_modifier_from1 type=chooser parent=creamod1 default=0 orient=vertical
				@caption Otsida muutjat

				@property s_mod_from1 type=datetime_select parent=creamod1 default=-1
				@caption Muudetud alates

				@property s_mod_to1 type=datetime_select parent=creamod1 default=-1
				@caption Muudetud kuni

			@layout keywords1 type=vbox closeable=1 area_caption=M&auml;rks&otilde;nad parent=right_side1

				@property s_kws1 type=textbox parent=keywords1
				@caption M&auml;rks&otilde;nad

			@layout l_timing1 type=vbox closeable=1 area_caption=Ajaline&nbsp;aktiivsus parent=right_side1

				@property s_tmg_activate_from1 type=datetime_select parent=l_timing1 default=-1
				@caption Aktiveeri alates

				@property s_tmg_activate_to1 type=datetime_select parent=l_timing1 default=-1
				@caption Aktiveeri kuni

				@property s_tmg_deactivate_from1 type=datetime_select parent=l_timing1 default=-1
				@caption Deaktiveeri alates

				@property s_tmg_deactivate_to1 type=datetime_select parent=l_timing1 default=-1
				@caption Deaktiveeri kuni



	@property s_sbt1 type=submit
	@caption Otsi

	@property s_res1 type=table no_caption=1

@groupinfo srch caption="Otsing" submit_method=get save=no
@groupinfo srch_complex caption="Detailne otsing" submit_method=get save=no

*/

class aw_object_search_if extends class_base
{
	private $u_oids = array();

	function aw_object_search_if()
	{
		$this->init(array(
			"tpldir" => "applications/customer_satisfaction_center/aw_object_search",
			"clid" => CL_AW_OBJECT_SEARCH_IF
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : "";
		$nm = $prop["name"];
		if (substr($nm, -1) == "1")
		{
			$nm = substr($nm, 0, -1);
		}
		switch($nm)
		{
			case "login":
				$ol = new object_list(array(
					"class_id" => CL_AW_LOGIN
				));
				$prop["options"] = array("" => "") + $ol->names();
				break;
			case "s_parent_search":
				$v = html::textbox(array(
					"name" => "s_parent",
					"size" => 20,
					"value" => isset($arr["request"]["s_parent"]) ? $arr["request"]["s_parent"] : "",
				));
				$url = $this->mk_my_orb("do_search", array(
					"pn" => "s_parent",
					"multiple" => 1,
					"no_submit" => 1,
				),"popup_search");
				$url = "javascript:aw_popup_scroll(\"".$url."\",\"".t("Otsi")."\",550,500)";
				$v .= " ".html::href(array(
					"caption" => html::img(array(
						"url" => "images/icons/search.gif",
						"border" => 0
					)),
					"url" => $url
				));
				$prop["value"] = $v;
				break;
			case "s_creator_from":
			case "s_modifier_from":
				$prop["options"] = array(
					0 => t("Kasutajatest"),
					1 => t("Gruppidest"),
				);
				if(!strlen($prop["value"]))
				{
					$prop["value"] = 0;
				}
				break;
			case "s_crea_from":
			case "s_crea_to":
			case "s_mod_from":
			case "s_mod_to":
			case "s_tmg_activate_from1":
			case "s_tmg_activate_to1":
			case "s_tmg_deactivate_from1":
			case "s_tmg_deactivate_to1":
				if ($prop["value"] < 10)
				{
					$prop["value"] = -1;
				}
				break;

			case "s_clid":
				$odl = new object_data_list(
					array(
					),
					array(
						"" => array(new obj_sql_func(OBJ_SQL_UNIQUE, "clid", "class_id"))
					)
				);
				$cls = array();
				$cldata = aw_ini_get("classes");
				foreach($odl->arr() as $od)
				{
					if(isset($cldata[$od["clid"]]["name"]))
					{
						$cls[$od["clid"]] = aw_html_entity_decode($cldata[$od["clid"]]["name"]);
					}
				}
				//TODO: lisada atc visible filter
				natsort($cls);
				$prop["options"] = $cls;
				if ($prop["name"] == "s_clid1")
				{//XXX: ?
					$prop["onchange"] = "select_reltypes(this);";
				}
				break;

			case "s_status":
				$prop["options"] = array(
					"0" => t("K&otilde;ik"),
					"2" => t("Aktiivsed"),
					"1" => t("Deaktiivsed")
				);
				break;

			case "s_language":
				$prop["options"] = languages::get_list(array("addempty" => true));
				break;

			case "s_period":
				$pr = new period();
				$prop["options"] = $pr->period_list(aw_global_get("act_per_id"),false);
				if (count($prop["options"]) == 0)
				{
					return PROP_IGNORE;
				}
				$prop["options"] = array("" => t("K&otilde;ik")) + $prop["options"];
				break;

			case "s_site_id":
				$dat = $this->db_fetch_array("SELECT distinct(site_id) as site_id FROM objects");
				$sid = aw_ini_get("site_id");
				$sites = array("" => 0, $sid => $sid);
				$sl = new site_list();
				foreach($dat as $row)
				{
					$sites[$row["site_id"]] = $row["site_id"];
				}

				foreach($sites as $nsid)
				{
					if ($nsid != $sid)
					{
						if ($nsid == 0)
						{
							$sites[""] = t("Igalt poolt");
						}
						else
						{

							$sites[$nsid] = $sl->get_url_for_site($nsid);
						}
					}
					else
					{
						$sites[$nsid] = aw_ini_get("baseurl");
					}
				}
				$sites[""] = t("Igalt poolt");
				$prop["options"] = $sites;
				break;

			case "s_res":
				if (!empty($arr["request"]["s_sbt"]))
				{
					$this->_s_res($arr);
				}
				break;

			case "s_tb":
				$this->_s_tb($arr);
				break;

			case "s_rel_type":
				if (is_array($arr["request"]["s_clid1"]))
				{
					$prop["options"] = $this->_get_relation_type_options(reset($arr["request"]["s_clid1"]));
				}
				break;
		}
		return $retval;
	}

	function _s_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "delb",
			"url" => "#",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"onClick" => "check_delete()",
			"confirm" => t("Oled kindel et soovid valitud objektid kustutada?")
		));
		$tb->add_button(array(
			"name" => "cut",
			"action" => "cut",
			"img" => "cut.gif",
			"tooltip" => t("L&otilde;ika")
		));
		$tb->add_button(array(
			"name" => "copy",
			"action" => "copy",
			"img" => "copy.gif",
			"tooltip" => t("Kopeeri")
		));
	}

///XXX: tundub et seda meetodit pole vaja
	/**
		@attrib name=delete_bms
	**/
	function delete_bms($arr)
	{
		object_list::iterate_list($_GET["sel"], "delete");
		die("<script>window.back();</script>");
	}

	function _init_s_res_t($t)
	{
		$t->define_field(array(
			"name" => "oid",
			"caption" => t("OID"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "lang",
			"caption" => t("Keel"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "class_id",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Asukoht"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Loodud"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Muutja"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "oppnar",
			"caption" => t("Ava"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _search_mk_call($filter, $arr)
	{
		$_parms = array(
			"class" => "aw_object_search_if",
			"action" => "search_object_list",
			"params" => $filter
		);
		if (!empty($arr["request"]["login"]))
		{
			$_parms["method"] = "xmlrpc";
			$_parms["login_obj"] = $arr["request"]["login"];

			if(!is_array($_parms["params"]["lang_id"]) || !sizeof($_parms["params"]["lang_id"]))
			{
				unset($_parms["params"]["lang_id"]);
			}
			if(!is_array($_parms["params"]["site_id"]) || !sizeof($_parms["params"]["site_id"]))
			{
				unset($_parms["params"]["site_id"]);
			}
			unset($_parms["params"]["brother_of"]);
		}
		$ret =  $this->do_orb_method_call($_parms);
		return $ret;
	}

	/**
		@attrib name=search_object_list params=name all_args=1
	**/
	function search_object_list($filter)
	{
		$rowsres = array(
			array(
				"parent" => "parent",
				"lang_id" => "lang",
				"created" => "created",
				"createdby" => "createdby",
				"modified" => "modified",
				"modifiedby" => "modifiedby"
			),
		);
		$rows_arr = new object_data_list($filter , $rowsres);
		return $rows_arr->arr();
	}

	function _s_res($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_s_res_t($t);
		$filt = $this->get_s_filt($arr);

		if (count($filt) == 1)
		{
			return;
		}

		if (!empty($arr["request"]["login"]))
		{
			$lo = new aw_login();
			$serv = $lo->get_server($arr["request"]["login"]);
			$old_bu = $this->cfg["baseurl"];
			$this->cfg["baseurl"] = "http://{$serv}/";
			$is_remote = true;
			aw_ini_set("baseurl" , $this->cfg["baseurl"]);
		}

		//nyyd object data listi peal asi
		$data = $this->_search_mk_call($filt,$arr);

		$t->set_caption(sprintf(t("Leiti %s objekti"), sizeof($data)));

		foreach($data as $id => $d)
		{
			if (aw_ini_isset("classes.{$d["class_id"]}"))
			{
				if($d["class_id"] == CL_USER)
				{
					$this->u_oids[] = $id;
				}

				$lang = languages::get_code_for_id($d["lang"]);

				$t->define_data(array(
					"oid" => $id,
					"icon" => html::img(array(
						"url" => icons::get_icon_url($d["class_id"], $d["name"]),
						"alt" => sprintf(t("Objekti id on %s"), $id),
						"title" => sprintf(t("Objekti id on %s"), $id),
						"border" => 0
					)),
					"name" => html::href(array(
						"caption" => $d["name"],
						"url" => $this->mk_my_orb("change", array("id" => $d["oid"]), $d["class_id"])
					)),
					"lang" => $lang,
					"class_id" => aw_ini_isset("classes.{$d["class_id"]}.name"),
					"location" => isset($d["parent_name"]) ? $d["parent_name"] : "",
					"created" => $d["created"],
					"createdby" => $d["createdby"],
					"modified" => $d["modified"],
					"modifiedby" => $d["modifiedby"],
					"oppnar" => html::href(array(
						"url" => $this->mk_my_orb("redir", array("parent" => $id), "admin_if"),
						"caption" => t("Ava")
					))
				));
			}
		}

		if (!empty($arr["request"]["login"]))
		{
			aw_ini_set("baseurl", $old_bu);
			$this->cfg["baseurl"] = $old_bu;
		}
		$t->set_default_sortby("name");
	}

	function get_s_filt($arr)
	{
		foreach($arr["request"] as $k => $v)
		{
			if (substr($k, -1) == "1")
			{
				$arr["request"][substr($k, 0, -1)] = $v;
			}
		}
		$filt = array("limit" => 2000, "lang_id" => array(), "site_id" => array());
		$arrprops = array("s_name", "s_parent");
		foreach($arrprops as $arrprop)
		{
			if(strpos($arr["request"][$arrprop], ","))
			{
				$arr["request"][$arrprop] = explode(",", $arr["request"][$arrprop]);
				foreach($arr["request"][$arrprop] as $id => $val)
				{
					$arr["request"][$arrprop][$id] = "%".trim($val)."%";
				}
			}
		}
		$groupprops = array("creator", "modifier");
		foreach($groupprops as $gp)
		{
			if($arr["request"]["s_".$gp."_from"] && $arr["request"]["s_".$gp])
			{
				$ol = new object_list(array(
					"class_id" => CL_GROUP,
					"name" => "%".$arr["request"]["s_".$gp]."%"
				));
				$ppl = array();
				foreach($ol->arr() as $grp)
				{
					$conn = $grp->connections_from(array(
						"type" => "RELTYPE_MEMBER",
					));
					foreach($conn as $c)
					{
						$uo = obj($c->prop("to"));
						$uid = $uo->name();
						$ppl[] = $uid;
					}
				}
				$arr["request"]["s_".$gp] = $ppl;
			}
		}
		if($arr["request"]["s_parent"])
		{
			$filt["parent"] = $arr["request"]["s_parent"];
		}
		$props = array(
			"s_name" => "name",
			"s_comment" => "comment",
			"s_clid" => "class_id",
			"s_creator" => "createdby",
			"s_modifier" => "modifiedby",
			"s_alias" => "alias",
		);
		foreach($props as $pn => $ofn)
		{
			if (!empty($arr["request"][$pn]))
			{
				if (is_array($arr["request"][$pn]))
				{
					$filt[$ofn] = $arr["request"][$pn];
				}
				else
				{
					$filt[$ofn] = "%".$arr["request"][$pn]."%";
				}
			}
		}

		$nf = array("s_status" => "status", /*"s_oid" => "oid",*/ "s_site_id" => "site_id", "s_period" => "period", "s_language" => "lang_id");
		foreach($nf as $pn => $ofn)
		{
			if (isset($arr["request"][$pn]) && $arr["request"][$pn] > 0)
			{
				$filt[$ofn] = $arr["request"][$pn];
			}
		}

		if (!isset($arr["request"]["s_find_bros"]) || !$arr["request"]["s_find_bros"])
		{
			$filt["brother_of"] = new obj_predicate_prop("id");
		}

		if (is_numeric($arr["request"]["s_oid"]))
		{
			$filt["oid"] = $arr["request"]["s_oid"];
		}
		else
		if (isset($arr["request"]["s_oid"][0]) && $arr["request"]["s_oid"][0] == "<")
		{
			$filt["oid"] = new obj_predicate_compare(OBJ_COMP_LESS, substr($arr["request"]["s_oid"], 1));
		}
		else
		if (isset($arr["request"]["s_oid"][0]) && $arr["request"]["s_oid"][0] == ">")
		{
			$filt["oid"] = new obj_predicate_compare(OBJ_COMP_GREATER, substr($arr["request"]["s_oid"], 1));
		}
		else
		if (strpos($arr["request"]["s_oid"], "-") !== false)
		{
			list($o_from, $o_to) = explode("-", $arr["request"]["s_oid"]);
			if (is_numeric($o_from) && is_numeric($o_to))
			{
				$filt["oid"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $o_from, $o_to);
			}
		}

		if (isset($arr["request"]["s_rel_type"]) && $arr["request"]["s_rel_type"] != "" && ($arr["request"]["s_rel_obj_oid"] != "" || $arr["request"]["s_rel_obj_name"] != "") && is_array($arr["request"]["s_clid"]))
		{
			$clid = reset($arr["request"]["s_clid"]);
			$cl_const = aw_ini_get("classes.{$clid}.def");

			$filt_name = $cl_const.".".$arr["request"]["s_rel_type"];
			if ($arr["request"]["s_rel_obj_oid"])
			{
				$filt[$filt_name] = explode(",", $arr["request"]["s_rel_obj_oid"]);
			}
			else
			if ($arr["request"]["s_rel_obj_name"] != "")
			{
				$filt[$filt_name.".name"] = map("%%%s%%", explode(",", $arr["request"]["s_rel_obj_name"]));
			}
		}

		$c_from = date_edit::get_timestamp($arr["request"]["s_crea_from"]);
		$c_to = date_edit::get_timestamp($arr["request"]["s_crea_to"]);
		$m_from = date_edit::get_timestamp($arr["request"]["s_mod_from"]);
		$m_to = date_edit::get_timestamp($arr["request"]["s_mod_to"]);

		if ($c_from > 1 && $c_to > 1)
		{
			$filt["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $c_from, $c_to);
		}
		else
		if ($c_from > 1)
		{
			$filt["created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $c_from);
		}
		else
		if ($c_to > 1)
		{
			$filt["created"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $c_to);
		}

		if ($m_from > 1 && $m_to > 1)
		{
			$filt["modified"] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $m_from, $m_to);
		}
		else
		if ($m_from > 1)
		{
			$filt["modified"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $m_from);
		}
		else
		if ($m_to > 1)
		{
			$filt["modified"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $m_to);
		}

		if ($arr["request"]["s_kws"] != "")
		{
			$filt["CL_DOCUMENT.RELTYPE_KEYWORD.name"] = "%".$arr["request"]["s_kws"]."%";
		}

		return $filt;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["return_url"] = automatweb::$request->arg("return_url");
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] !== "srch" && $arr["id"] !== "srch_complex")
		{
			return false;
		}
		return true;
	}

	function callback_generate_scripts($arr)
	{
		$ret = "var oids = Array()".chr(13).chr(10);
		foreach($this->u_oids as $oid)
		{
			$ret .= "oids[".$oid."] = ".$oid.chr(13).chr(10);
		}
		$this->read_template("scripts.tpl");
		$ret .= $this->parse();
		return $ret;
	}

	function init_search()
	{
		$ol = new object_list(array(
			"class_id" => CL_AW_OBJECT_SEARCH_IF
		));
		if ($ol->count())
		{
			return $ol->begin();
		}
		$o = obj();
		$o->set_class_id(CL_AW_OBJECT_SEARCH_IF);
		$o->set_name(t("AW Objektide otsing"));
		$o->set_parent(aw_ini_get("amenustart"));
		$o->save();
		return $o;
	}

	/**
		@attrib name=redir_search
		@param url optional
		@param s_name optional
		@param s_clid optional
	**/
	function redir_search($arr)
	{
		$so = $this->init_search();
		$args = array("group" => "srch");

		if (!empty($arr["url"]))
		{
			$args["return_url"] = $arr["url"];
		}

		if (!empty($arr["s_name"]))
		{
			$args["s_name"] = $arr["s_name"];
		}

		if (!empty($arr["s_clid"]))
		{
			$args["s_clid"] = $arr["s_clid"];
		}

		return html::get_change_url($so->id(), $args);
	}

	/** cuts the selected objects

		@attrib name=cut params=name default="0"
		@returns
		@comment

	**/
	function cut($arr)
	{
		$i = new admin_if();
		$i->if_cut($_GET);
		die("<script>window.back();</script>");
	}

	/** copies the selected objects
		@attrib name=copy params=name default="0"
	**/
	function copy($arr)
	{
		$i = new admin_if();
		return $i->if_copy($_GET);
		die("<script>window.back();</script>");
	}

	/**
		@attrib name=get_relation_types
		@param s_clid required
	**/
	function get_relation_types($arr)
	{
		$rv = $this->_get_relation_type_options($arr["s_clid"]);
		die($this->picker("", $rv));
	}

	private function _get_relation_type_options($clid)
	{
		$tmp = obj();
		$tmp->set_class_id($clid);

		$rv = array("" => t("--vali--"));
		foreach($tmp->get_relinfo() as $d => $inf)
		{
			if (substr($d, 0, strlen("RELTYPE")) === "RELTYPE")
			{
				$rv[$d] = $inf["caption"];
			}
		}

		return $rv;
	}
}
