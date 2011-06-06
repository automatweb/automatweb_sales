<?php

// crm_db.aw - CRM database
/*
@classinfo relationmgr=yes prop_cb=1
@default table=objects
@default group=general

	@groupinfo config_general caption=&Uuml;ldised&nbsp;seaded parent=general
	@default group=config_general

		@property name type=textbox
		@caption Nimi

@default field=meta
@default method=serialize

		@property owner_org type=relpicker reltype=RELTYPE_OWNER_ORG store=connect
		@caption Omanikorganisatsioon

		@property selections type=relpicker reltype=RELTYPE_SELECTIONS group=general store=connect
		@caption Vaikimisi valim

		@property dir_firma type=relpicker reltype=RELTYPE_FIRMA_CAT store=connect
		@caption Ettev&otilde;tete kaust

		@property folder_person type=relpicker reltype=RELTYPE_ISIK_CAT store=connect
		@caption T&ouml;&ouml;tajate kaust

		@property dir_address type=relpicker reltype=RELTYPE_ADDRESS_CAT store=connect
		@caption Aadresside kaust

		@property dir_ettevotlusvorm type=relpicker reltype=RELTYPE_ETTEVOTLUSVORM_CAT store=connect
		@caption &Otilde;iguslike vormide kaust

		@property dir_riik type=relpicker reltype=RELTYPE_RIIK_CAT store=connect
		@caption Riikide kaust

		@property dir_piirkond type=relpicker reltype=RELTYPE_PIIRKOND_CAT store=connect
		@caption Piirkondade kaust

		@property dir_maakond type=relpicker reltype=RELTYPE_MAAKOND_CAT store=connect
		@caption Maakondade kaust

		@property dir_linn type=relpicker reltype=RELTYPE_LINN_CAT store=connect
		@caption Linnade kaust

		@property dir_tegevusala type=relpicker multiple=1 reltype=RELTYPE_TEGEVUSALA_CAT store=connect
		@caption Tegevusalade kaust

		@property dir_toode type=relpicker reltype=RELTYPE_TOODE_CAT store=connect
		@caption Toodete kaust

		@property dir_default type=relpicker reltype=RELTYPE_GENERAL_CAT store=connect
		@caption Kaust, kui m&otilde;ni eelnevatest pole m&auml;&auml;ratud, siis kasutatakse seda

	@groupinfo config_org caption=Kataloogi&nbsp;seaded parent=general
	@default group=config_org

		@property flimit type=select
		@caption Kirjeid &uuml;hel lehel

		@property all_ct_data type=checkbox ch_value=1
		@caption Kuva k&otilde;iki kontaktandmeid

		@property display_mode type=chooser orient=vertical
		@caption Kuvatavad organisatsioonid

		@property org_tbl_fields type=select multiple=1
		@caption Tabeli v&auml;ljad

-----------------------------------------------------------------------------
@groupinfo org caption=Kataloog submit=no
@default group=org

	@property org_tlb type=toolbar no_caption=1 store=no

	@layout o_main type=hbox width=20%:80%

		@layout o_left type=vbox parent=o_main

			@layout o_left_top type=vbox parent=o_left closeable=1 area_caption=Kataloogi&nbsp;puu

				@property org_tree type=text store=no no_caption=1 parent=o_left_top

			@layout o_left_bottom type=vbox parent=o_left closeable=1 area_caption=Otsi&nbsp;kataloogist

				@property os_name type=textbox store=no captionside=top parent=o_left_bottom
				@caption Nimi

				@property os_regnr type=textbox store=no captionside=top parent=o_left_bottom
				@caption &Auml;riregistri number

				@property os_address type=textbox store=no captionside=top parent=o_left_bottom
				@caption Aadress

				@property os_director type=textbox store=no captionside=top parent=o_left_bottom
				@caption Firmajuht

				@property os_owner type=textbox store=no captionside=top parent=o_left_bottom
				@caption Omanik

				@property os_turnover_year type=textbox size=10 store=no captionside=top parent=o_left_bottom
				@caption K&auml;ibe aasta

				@property os_turnover type=text store=no captionside=top parent=o_left_bottom
				@caption K&auml;ibe summa

				@property os_county type=textbox store=no captionside=top parent=o_left_bottom
				@caption Maakond

				@property os_city type=textbox store=no captionside=top parent=o_left_bottom
				@caption Linn

				@property os_legal_form type=chooser multiple=1 store=no captionside=top parent=o_left_bottom
				@caption Ettev&otilde;lusvorm

				@property os_submit type=submit store=no parent=o_left_bottom
				@caption Otsi

		@layout o_right type=vbox parent=o_main

			@property org_a2z type=text store=no no_caption=1 parent=o_right

			@property org_tbl type=table store=no no_caption=1 parent=o_right

@reltype SELECTIONS value=1 clid=CL_CRM_SELECTION
@caption Valimid

@reltype FIRMA_CAT value=2 clid=CL_MENU
@caption Organisatsioonide kaust

@reltype ISIK_CAT value=3 clid=CL_MENU
@caption T&ouml;&ouml;tajate kaust

@reltype ADDRESS_CAT value=4 clid=CL_MENU
@caption Aadresside kaust

@reltype LINN_CAT value=5 clid=CL_MENU
@caption Linnade kaust

@reltype MAAKOND_CAT value=6 clid=CL_MENU
@caption Maakondade kaust

@reltype RIIK_CAT value=7 clid=CL_MENU
@caption Riikide kaust

@reltype TEGEVUSALA_CAT value=8 clid=CL_MENU
@caption Tegevusalade kaust

@reltype TOODE_CAT value=9 clid=CL_MENU
@caption Toodete kataloogide kaust

@reltype GENERAL_CAT value=10 clid=CL_MENU
@caption &Uuml;ldkaust

@reltype CALENDAR value=11 clid=CL_PLANNER
@caption Kalender

@reltype ETTEVOTLUSVORM_CAT value=12 clid=CL_MENU
@caption &Otilde;iguslike vormide kaust

@reltype FORMS  value=13 clid=CL_CFGFORM
@caption Sisestusvormid

@reltype METAMGR value=14 clid=CL_METAMGR
@caption Muutujad

@reltype PIIRKOND_CAT value=15 clid=CL_MENU
@caption Piirkondade kaust

@reltype OWNER_ORG value=16 clid=CL_CRM_COMPANY
@caption Omanikorganisatsioon

@reltype OS_CITY value=17 clid=CL_CRM_CITY
@caption Linn otsingus

@reltype OS_COUNTY value=18 clid=CL_CRM_COUNTY
@caption Maakond otsingus

*/

class crm_db extends class_base
{
	const ORGS_BY_CUSTOMER_RELATIONS = 0;
	const ORGS_BY_SECTORS = 1;

	function crm_db()
	{
		$this->init(array(
			"clid" => crm_db_obj::CLID,
		));
		$this->org_tbl_fields = array(
			"jrk" => t("Jrk"),
			"org" => t("Organisatsioon"),
			"field" => t("P&otilde;hitegevus"),
			"ettevotlusvorm" => t("Ettev&otilde;lusvorm"),
			"address" => t("Aadress"),
			"e_mail" => t("E-post"),
			"url" => t("WWW"),
			"phone" => t("Telefon"),
			"org_leader" => t("Juht"),
			"cr_manager" => t("Kliendihaldur"),
			"changed" => t("Muudetud"),
			"created" => t("Loodud"),
		);
		$this->org_tbl_fields_add_args = array(
			"jrk" => array("sorting_field" => "jrk_int"),
		);
	}

	function _set_org_tbl($arr)
	{
		foreach(safe_array($arr["prop"]["value"]) as $oid => $v)
		{
			if(isset($v["sector"]) && $this->can("view", $v["sector"]))
			{
				$ol = new object_list(array(
					"class_id" => CL_CRM_COMPANY_SECTOR_MEMBERSHIP,
					"company" => $oid,
					"sector" => $v["sector"],
					"lang_id" => array(),
					"site_id" => array(),
					new obj_predicate_limit(1),
				));
				if($ol->count() > 0)
				{
					$o = $ol->begin();
					$o->set_ord((int)$v["jrk"]);
					$o->save();
				}
				elseif((int)$v["jrk"] !== 0)
				{
					$o = obj();
					$o->set_class_id(CL_CRM_COMPANY_SECTOR_MEMBERSHIP);
					$o->set_parent($oid);
					$o->set_name(sprintf(t("Organisatsiooni OIDga %u seos tegevusalaga OIDga %u"), $oid, $v["sector"]));
					$o->set_prop("company", $oid);
					$o->set_prop("sector", $v["sector"]);
					$o->set_ord((int)$v["jrk"]);
					$o->save();
				}
			}
			else
			{
				$o = obj($oid);
				$o->set_ord((int)$v["jrk"]);
				$o->save();
			}
		}
	}

	function get_property(&$arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "display_mode":
				$prop["options"] = array(
					self::ORGS_BY_CUSTOMER_RELATIONS => t("Kuva ainult organisatsioone, millel on omanikorganisatsioonida kliendisuhe"),
					self::ORGS_BY_SECTORS => t("Kuva organisatsioone, mis on tegevusalade all"),
				);
				$prop["value"] = isset($prop["value"]) ? $prop["value"] : 0;
				break;

			case "org_tbl_fields":
				$prop["options"] = $this->org_tbl_fields;
				break;

			case "flimit":
				$prop["options"] = array (30 => 30, 60 => 60, 100 => 100);
				break;

			case "os_show_in_webview":
				$prop["options"] = array(
					1 => t("Ei kuvata veebis"),
					2 => t("Kuvatakse veebis"),
				);
				$prop["value"] = isset($_GET[$prop["name"]]) ? $_GET[$prop["name"]] : NULL;
				break;

			case "os_legal_form":
				$odl = new object_data_list(
					array(
						"class_id" => CL_CRM_CORPFORM,
						"parent" => is_oid($arr["obj_inst"]->dir_ettevotlusvorm) ? $arr["obj_inst"]->dir_ettevotlusvorm : array(),
						"lang_id" => array(),
						"site_id" => array(),
						new obj_predicate_sort(array(
							"jrk" => "asc",
							"shortname" => "asc"
						)),
					),
					array(
						CL_CRM_CORPFORM => array("shortname"),
					)
				);
				$prop["options"] = $odl->get_element_from_all("shortname");
				$prop["value"] = isset($_GET[$prop["name"]]) ? $_GET[$prop["name"]] : NULL;
				break;

			case "os_city":
			case "os_county":
			case "os_name":
			case "os_regnr":
			case "os_address":
			case "os_director":
			case "os_turnover_year":
			case "os_owner":
				$prop["value"] = automatweb::$request->arg($prop["name"]);
				break;

			case "os_turnover":
				$prop["value"] = html::textbox(array(
					"name" => "os_turnover_from",
					"value" => automatweb::$request->arg("os_turnover_from"),
					"size" => 15,
				))." - ".html::textbox(array(
					"name" => "os_turnover_to",
					"value" => automatweb::$request->arg("os_turnover_to"),
					"size" => 15,
				));
				break;
		}
		return  $retval;
	}

	//	TODO: Put it back to work with customer relations! (Maybe even include the display/don't display in the web division?)
	function _get_org_tree($arr)
	{
		$arr["prop"]["value"] = array();
		foreach($arr["obj_inst"]->prop("dir_tegevusala") as $sector_dir)
		{
			$parent = new object($sector_dir, array(), menu_obj::CLID);

			$treeview = treeview::tree_from_objects(array(
				"tree_opts" => array(
					"type" => TREE_DHTML_WITH_CHECKBOXES,
					"tree_id" => "org_tree_".$parent->id(),
					"checkbox_data_var" => "os_sector",
					"persist_state" => true,
					"url_target" => "",
					"item_name_length" => 30,
					"checked_nodes" => explode(",", automatweb::$request->arg("os_sector")),
				),
				"root_item" => $parent,
				"target_url" => "",
				"class_id" => CL_CRM_SECTOR,
				"ot" => new object_tree(array(
					"class_id" => crm_sector_obj::CLID,
					"parent" => $parent->id()
				)),
				"var" => "os_sector",
			));

			$arr["prop"]["value"] .= $treeview->get_html();
		}
	}

	function _init_company_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_default_sortby("fname");

		$fields = is_array($arr["obj_inst"]->org_tbl_fields) ? $arr["obj_inst"]->org_tbl_fields : array_keys($this->org_tbl_fields);
		foreach($fields as $field)
		{
			$args = array(
				"name" => $field,
				"caption" => $this->org_tbl_fields[$field],
				"sortable" => 1,
			);
			$add_args = !empty($this->org_tbl_fields_add_args[$field]) ? $this->org_tbl_fields_add_args[$field] : array();
			$args = array_merge($args, $add_args);
			$t->define_field($args);
		}
		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
	}

	function _get_org_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_company_table($arr);
		$this->set_org_tbl_caption($arr);

		$perpage = 20;
		if ($arr["obj_inst"]->prop("flimit") != "")
		{
			$perpage = $arr["obj_inst"]->prop("flimit");
		};

		list($companies, $customer_data) = $this->get_orgs($arr);

		// Get the order!
		if(isset($_GET["branch_id"]) && $this->can("view", $_GET["branch_id"]))
		{
			foreach($companies->ids() as $id)
			{
				$jrks[$id] = 0;
			}
			$jrk_odl = new object_data_list(
				array(
					"class_id" => CL_CRM_COMPANY_SECTOR_MEMBERSHIP,
					"CL_CRM_COMPANY_SECTOR_MEMBERSHIP.RELTYPE_COMPANY" => $companies->ids(),
					"CL_CRM_COMPANY_SECTOR_MEMBERSHIP.RELTYPE_SECTOR" => isset($_GET["branch_id"]) ? $_GET["branch_id"] : array(),
					"lang_id" => array(),
					"site_id" => array(),
				),
				array(
					CL_CRM_COMPANY_SECTOR_MEMBERSHIP => array("company", "jrk"),
				)
			);
			foreach($jrk_odl->arr() as $jrk_od)
			{
				$jrks[$jrk_od["company"]] = $jrk_od["jrk"];
			}
		}
		else
		{
			$jrks = $companies->ords();
		}
		asort($jrks, SORT_NUMERIC);

		if($companies->count() > $perpage)
		{
			$t->define_pageselector(array(
				"type" => "lbtxt",
				"records_per_page" => $perpage,
				"d_row_cnt" => $companies->count(),
				"no_recount" => true,
			));
			$p = isset($_GET["ft_page"]) ? (int)$_GET["ft_page"] : 0;

			$ids = $companies->ids();
			$ids_to_cut = array_diff($ids, array_keys(array_slice($jrks, $p * $perpage, $perpage, true)));
			$companies->remove($ids_to_cut);
		}
		$coms = $companies->arr();
		foreach($coms as $com)
		{
			$ol = $com->prop("firmajuht");
			$org_leader = "";
			if(is_oid($ol))
			{
				$ol_obj = obj($ol, array(), crm_person_obj::CLID);
				$org_leader = html::get_change_url($ol, array("return_url" => get_ru()), $ol_obj->name());
			}
			$cr_manager = "";
			$crm = $com->prop("client_manager.name");
			if(strlen($crm) > 0)
			{
				$cr_manager = html::get_change_url($com->prop("client_manager"), array("return_url" => get_ru()), $com->prop("client_manager.name"));
			}

			if (!$arr["obj_inst"]->prop("all_ct_data") && $this->can("view", $com->prop("email_id")))
			{
				$eml = $com->prop("email_id.mail");
			}
			else
			{
				$phc = $com->connections_from(array("type" => "RELTYPE_EMAIL"));
				$pha = array();
				foreach($phc as $ph_con)
				{
					$ph_o = $ph_con->to();
					$pha[] = $ph_o->prop("mail");
				}
				$eml = join(", ", $pha);
			}

			if (!$arr["obj_inst"]->prop("all_ct_data") && $this->can("view", $com->prop("phone_id")))
			{
				$phs = $com->prop("phone_id.name");
			}
			else
			{
				$phc = $com->connections_from(array("type" => "RELTYPE_PHONE"));
				$pha = array();
				foreach($phc as $ph_con)
				{
					$pha[] = $ph_con->prop("to.name");
				}
				$phs = join(", ", $pha);
			}

			if (!$arr["obj_inst"]->prop("all_ct_data") && $this->can("view", $com->prop("url_id")))
			{
				$url = $com->prop("url_id.url");
				$url = substr($url, strpos($url, "http://"));
				if(strlen($url) > 0)
				{
					$url = html::href(array("url" => "http://".$url, "caption" => $url, "target" => "_blank"));
				}
			}
			else
			{
				$phc = $com->connections_from(array("type" => "RELTYPE_URL"));
				$pha = array();
				foreach($phc as $ph_con)
				{
					$tu = $ph_con->prop("to.name");
					$tu = substr($tu, strpos($tu, "http://"), strlen($tu)+1);
					if(strlen($tu) > 0)
					{
						$tu = html::href(array("url" => $tu, "caption" => $tu, "target" => "_blank"));
					}
					$pha[] = $tu;
				}
				$url = join(", ", $pha);
			}

			if (!$arr["obj_inst"]->prop("all_ct_data") && $this->can("view", $com->prop("contact")))
			{
				$cts = $com->prop("contact.name");
			}
			else
			{
				$phc = $com->connections_from(array("type" => "RELTYPE_ADDRESS"));
				$pha = array();
				foreach($phc as $ph_con)
				{
					$pha[] = $ph_con->prop("to.name");
				}
				$cts = join(", ", $pha);
			}

			try
			{
				$cd = $com->find_customer_relation(null);
			}
			catch (awex_crm $e)
			{
				$cd = null;
			}
			$pm = $this->get_org_popupmenu(array("oid" => $com->id(), "cd_oid" => $cd !== null ? $cd->id : null));
			$jrk = isset($_GET["branch_id"]) && $this->can("view", $_GET["branch_id"]) ? (isset($jrks[$com->id()]) ? (int)$jrks[$com->id()] : 0) : (int)$com->ord();
			$sector = isset($_GET["branch_id"]) && $this->can("view", $_GET["branch_id"]) ? html::hidden(array(
				"name" => "org_tbl[".$com->id()."][sector]",
				"value" => $_GET["branch_id"],
			)) : "";
			$t->define_data(array(
				"jrk" => html::textbox(array(
					"name" => "org_tbl[".$com->id()."][jrk]",
					"size" => 2,
					"value" => $jrk,
				)).$sector,
				"jrk_int" => $jrk,
				"id" => $com->id(),
//				"org" => html::get_change_url($com->id(), array("return_url" => get_ru()), strlen($com->name()) ? $com->name() : t("(nimetu)")),
				"org" => $pm->get_menu(array(
					"text" => parse_obj_name($com->name()),
				)),
				"field" => $com->prop_str("pohitegevus"),
				"ettevotlusvorm" => $com->prop_str("ettevotlusvorm"),
				"address" => $cts,
				"e_mail" => $eml,
				"url" => $url,
				"phone" => $phs,
				"org_leader" => $org_leader,
				"cr_manager" => $cr_manager,
				"changed" => date("Y.m.d H:i" , $com->modified()),
				"created" => date("Y.m.d H:i" , $com->created()),
			));
		}
		$t->set_numeric_field("jrk_int");
		$t->set_default_sortby("jrk_int");
	}

	function _get_org_tlb(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "create_event",
			"tooltip" => t("Lisa"),
			"img" => "new.gif",
		));

		$df = $arr["obj_inst"]->prop("dir_firma");
		$df = is_array($df) ? reset($df) : $df;
		if ($this->can("view", $df))
		{
			$tb->add_menu_item(array(
				"parent" => "create_event",
				"text" => t("Lisa organisatsioon"),
				"url" => $this->mk_my_orb("new", array("parent" => $df,"return_url" => get_ru(), "sector" => $_GET["branch_id"]), CL_CRM_COMPANY),
			));
		}

		if($arr["request"]["group"] == "tegevusalad" || $arr["request"]["group"] == "org")
		{
			if (isset($_GET["branch_id"]) && $this->can("add", $_GET["branch_id"]))
			{
				$tb->add_menu_item(array(
					"parent" => "create_event",
					"text" => t("Lisa tegevusala"),
					"url" => $this->mk_my_orb("new", array("parent" => $_GET["branch_id"], "return_url" => get_ru()), CL_CRM_SECTOR),
				));
			}
			else
			{
				$ar = new aw_array($arr["obj_inst"]->prop("dir_tegevusala"));
				foreach($ar->get() as $pt)
				{
					$pto = obj($pt);
					$tb->add_menu_item(array(
						"parent" => "create_event",
						"text" => sprintf(t("Lisa tegevusala %s"), $pto->name()),
						"url" => $this->mk_my_orb("new", array("parent" => $pt,"return_url" => get_ru()), CL_CRM_SECTOR),
					));
				}
			}
		}
		$tb->add_save_button();
		$pl = get_instance(CL_PLANNER);
		$cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));
		if(!empty($cal_id))
		{
			$tb->add_button(array(
				"name" => "user_calendar",
				"tooltip" => t("Kasutaja kalender"),
				"url" => html::get_change_url($cal_id, array("group" => "views", "return_url" => get_ru())),
				"img" => "icon_cal_today.gif",
			));
		}
		$tb->add_separator();
		$tb->add_menu_button(array(
			"name" => "go_navigate",
			"tooltip" => t("Ava valim"),
			"img" => "iother_shared_folders.gif",
		));
		$tb->add_separator();
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"action" => "delete_organizations",
			"confirm" => t("Kustutada valitud organisatsioonid?"),
			"img" => "delete.gif",
		));

		$current_org = obj(user::get_current_company(), array(), crm_company_obj::CLID);
		$tb->add_separator();
		$tb->add_button(array(
			"name" => "create_customer_data",
			"tooltip" => t(sprintf("Loo kliendisuhe organisatsiooniga '%s'", $current_org->name())),
			"action" => "create_customer_data",
		));

		$conns = $arr["obj_inst"]->connections_from(array(
			"class" => CL_CRM_SELECTION,
			"sort_by" => "to.name",
		));

		$ops = array();
		$ops[0] = t("-- vali valim --");

		foreach($conns as $conn)
		{
			$to = $conn->prop("to");
			$name = $conn->prop("to.name");
			$ops[$to] = $name;
			$tb->add_menu_item(array(
				"parent" => "go_navigate",
				"text" => $name,
				"url" => html::get_change_url($to),
			));
		};
		$str = html::select(array(
			"name" => "add_to_selection",
			"options" => $ops,
			"selected" => isset($selected) ? $selected : array(),
		));
		$tb->add_cdata($str, "right");
		$tb->add_separator(array(
			"side" => "right",
		));
		$tb->add_button(array(
			"name" => "go_add",
			"tooltip" => t("Lisa valitud valimisse"),
			"action" => "copy_to_selection",
			"confirm" => t("Paiguta valitud organisatsioonid sellesse valimisse?"),
			"img" => "import.gif",
			"side" => "right",
		));
	}

	public function _get_org_a2z($arr)
	{
		$all_letters = range("A", "Z");

		$let = array(
			aw_html_entity_decode("&Otilde;", ENT_NOQUOTES, aw_global_get("charset")),
			aw_html_entity_decode("&Auml;", ENT_NOQUOTES, aw_global_get("charset")),
			aw_html_entity_decode("&Ouml;", ENT_NOQUOTES, aw_global_get("charset")),
			aw_html_entity_decode("&Uuml;", ENT_NOQUOTES, aw_global_get("charset"))
		);
		foreach($let as $l)
		{
			$all_letters[] = $l;
		}

		$arr["prop"]["value"] = "<br>".str_repeat("&nbsp;", 4);
		$arr["prop"]["value"] .= html::href(array(
			"url" => aw_url_change_var("letter", NULL),
			"caption" => isset($_GET["letter"]) ? t("K&otilde;ik") : "<b>".t("K&otilde;ik")."</b>",
		))."&nbsp;";

		foreach($all_letters as $l)
		{
			$arr["prop"]["value"] .= html::href(array(
				"url" => aw_url_change_var("letter", $l),
				"caption" => isset($_GET["letter"]) && $_GET["letter"] === $l ? "<b>".$l."</b>" : $l,
			))."&nbsp;";
		}
	}

	/**

		@attrib name=delete_organizations params=name all_args="1"

	**/
	function delete_organizations($arr)
	{
		foreach(safe_array($arr["sel"]) as $obj_id)
		{
			if($this->can("delete", $obj_id))
			{
				$o = obj($obj_id);
				$o->delete();
			}
		}
		return urldecode($arr["post_ru"]);
	}

	/**

		@attrib name=copy_to_selection params=name all_args="1"

	**/
	function copy_to_selection($arr)
	{
		$selinst = get_instance(CL_CRM_SELECTION);
		$selinst->add_to_selection(array(
			"add_to_selection" => $arr["add_to_selection"],
			"sel" => $arr["sel"],
		));
		unset($arr["MAX_FILE_SIZE"]);
		unset($arr["action"]);
		unset($arr["reforb"]);
		unset($arr["sel"]);
		return $this->mk_my_orb("change", $arr);
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_mod_retval(&$arr)
	{
		$args = &$arr["args"];
		// no I need add all those things in search_form1 do my request vars
		if (isset($arr["request"]["org_search"]) and is_array($arr["request"]["org_search"]))
		{
			$args["org_search"] = $arr["request"]["org_search"];
		}

		foreach($arr["request"] as $k => $v)
		{
			if(substr($k, 0, 3) == "os_" && !empty($v))
			{
				unset($args["letter"]);
				unset($args["branch_id"]);
				unset($args["on_web"]);
				$args[$k] = $v;
			}
		}
	}

	/**
		@attrib name=show_on_web all_args=1
	**/
	public function show_on_web($arr)
	{
		$ids = safe_array($arr["sel"]);
		if(count($ids) == 0 || !is_oid($arr["id"]) || !is_oid(obj($arr["id"])->owner_org))
		{
			return $arr["post_ru"];
		}
		$seller = obj($arr["id"])->owner_org;

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"lang_id" => array(),
			"site_id" => array(),
			"buyer" => $ids,
			"seller" => $seller,
		));

		foreach($ol->arr() as $o)
		{
			if(!$o->show_in_webview)
			{
				$o->show_in_webview = 1;
				$o->save();
			}
			unset($ids[$o->buyer]);
		}

		foreach(array_keys($ids) as $id)
		{
			$o = obj();
			$o->set_class_id(CL_CRM_COMPANY_CUSTOMER_DATA);
			$o->set_parent($seller);
			$o->seller = $seller;
			$o->buyer = $id;
			$o->show_in_webview = 1;
			$o->save();
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=hide_on_web all_args=1
	**/
	public function hide_on_web($arr)
	{
		$ids = safe_array($arr["sel"]);
		if(count($ids) == 0 || !is_oid($arr["id"]) || is_oid(obj($arr["id"])->owner_org))
		{
			return $arr["post_ru"];
		}
		$seller = obj($arr["id"])->owner_org;

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"lang_id" => array(),
			"site_id" => array(),
			"buyer" => $ids,
			"seller" => $seller,
		));

		$ids = array_flip($ids);

		foreach($ol->arr() as $o)
		{
			if($o->show_in_webview)
			{
				$o->show_in_webview = 0;
				$o->save();
			}
			unset($ids[$o->buyer]);
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=create_customer_data params=name
		@param sel optional type=array
	**/
	public function create_customer_data($arr)
	{
		if(!empty($arr["sel"]))
		{
			foreach($arr["sel"] as $company_id)
			{
				$company = obj($company_id, array(), crm_company_obj::CLID);
				$company->find_customer_relation(null, true);
			}
		}
		return $arr["post_ru"];
	}

	private function get_orgs($arr, $all_orgs = false)
	{
		$t = $arr["prop"]["vcl_inst"];

		$vars = array(
			"class_id" => crm_company_obj::CLID,
			new obj_predicate_sort(array(
//				"jrk" => "ASC",
				"name" => "ASC",
			)),
		);

		$customer_data = array();
		switch ($arr["obj_inst"]->prop("display_mode"))
		{
			case self::ORGS_BY_SECTORS:
				$parents = array(-1);
				foreach($arr["obj_inst"]->prop("dir_tegevusala") as $sector_dir)
				{
					$sector_tree = new object_tree(array(
						"class_id" => crm_sector_obj::CLID,
						"parent" => $sector_dir,
					));
					$parents[] = $sector_dir;
					$parents += $sector_tree->ids();
				}
				$vars["parent"] = $parents;
				if(automatweb::$request->arg_isset("os_sector"))
				{
					$vars["parent"] = explode(",", automatweb::$request->arg("os_sector"));
				}
				break;

			case self::ORGS_BY_CUSTOMER_RELATIONS:
				$oo = $arr["obj_inst"]->owner_org;
				if(!$this->can("view", $oo))
				{
					return array(new object_list(), array());
				}
				$cd_odl = new object_data_list(
					array(
						"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
						"lang_id" => array(),
						"site_id" => array(),
						"seller" => $oo,
						"show_in_webview" => $show_in_webview,
					),
					array(
						CL_CRM_COMPANY_CUSTOMER_DATA => array("buyer"),
					)
				);
				$vars["oid"] = $cd_odl->get_element_from_all("buyer");
				$customer_data = array_reverse($vars["oid"]);
				if(count($vars["oid"]) === 0)
				{
					return array(new object_list(), array());
				}

				if(automatweb::$request->arg_isset("os_sector"))
				{
					$vars["pohitegevus"] = explode(",", automatweb::$request->arg("os_sector"));
				}
				break;
		}

		if(!$all_orgs)
		{
			if(isset($_GET["os_name"]))
			{
				$vars["name"] = "%".$_GET["os_name"]."%";
			}
			if(isset($_GET["os_regnr"]))
			{
				$vars["reg_nr"] = "%".$_GET["os_regnr"]."%";
			}
			if(isset($_GET["os_legal_form"]))
			{
				$vars["ettevotlusvorm"] = $_GET["os_legal_form"];
			}
			if(isset($_GET["os_director"]))
			{
				$vars["firmajuht.name"] = "%".$_GET["os_director"]."%";
			}
			$adr_vars = array();
			if(isset($_GET["os_address"]))
			{
				$adr_vars["aadress"] = "%".$_GET["os_address"]."%";
			}
			if(strlen(automatweb::$request->arg("os_city")) > 0)
			{
				$cities = array();
				foreach(explode(",", automatweb::$request->arg("os_city")) as $city)
				{
					$cities[] = "%".trim($city)."%";
				}
				$adr_vars["linn(CL_CRM_CITY).name"] = $cities;
			}
			if(strlen(automatweb::$request->arg("os_county")) > 0)
			{
				$counties = array();
				foreach(explode(",", automatweb::$request->arg("os_county")) as $county)
				{
					$counties[] = "%".trim($county)."%";
				}
				$adr_vars["maakond(CL_CRM_COUNTY).name"] = $counties;
			}
			if(count($adr_vars) > 0)
			{
				foreach($adr_vars as $k => $v)
				{
					unset($adr_vars[$k]);
					$adr_vars["CL_CRM_COMPANY.RELTYPE_ADDRESS.".$k] = $v;
				}
				$vars[] = new object_list_filter(array(
					"logic" => "AND",
					"conditions" => $adr_vars,
				));
			}
		}

		$annual_report_filter = array(
			"class_id" => crm_company_annual_report_obj::CLID,
		);
		if(automatweb::$request->arg_isset("os_turnover_year"))
		{
			$annual_report_filter["year"] = automatweb::$request->arg("os_turnover_year");
		}
		if(automatweb::$request->arg_isset("os_turnover_from") and automatweb::$request->arg_isset("os_turnover_to"))
		{
			$annual_report_filter["turnover"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, automatweb::$request->arg("os_turnover_from"), automatweb::$request->arg("os_turnover_to"), "int");
		}
		elseif(automatweb::$request->arg_isset("os_turnover_from"))
		{
			$annual_report_filter["turnover"] = new obj_predicate_compare(obj_predicate_compare::GREATER_OR_EQ, automatweb::$request->arg("os_turnover_from"), null, "int");
		}
		elseif(automatweb::$request->arg_isset("os_turnover_to"))
		{
			$annual_report_filter["turnover"] = new obj_predicate_compare(obj_predicate_compare::LESS_OR_EQ, automatweb::$request->arg("os_turnover_to"), null, "int");
		}

		if (count($annual_report_filter) > 1)
		{
			$annual_report_odl = new object_data_list(
				$annual_report_filter,
				array(
					crm_company_annual_report_obj::CLID => array("company")
				)
			);
			if ($annual_report_odl->count() > 0)
			{
				$vars["oid"] = isset($vars["oid"]) ? $vars["oid"] + $annual_report_odl->get_element_from_all("company"): $annual_report_odl->get_element_from_all("company");
			}
			else
			{
				return array(new object_list(), array());
			}
		}

		if (strlen(automatweb::$request->arg("os_owner")) > 0)
		{
			$owner_odl = new object_data_list(
				array(
					"class_id" => crm_company_ownership_obj::CLID,
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_CRM_COMPANY_OWNERSHIP.owner(CL_CRM_PERSON).name" => "%". automatweb::$request->arg("os_owner") ."%",
							"CL_CRM_COMPANY_OWNERSHIP.owner(CL_CRM_COMPANY).name" => "%". automatweb::$request->arg("os_owner") ."%",
						),
					)),
				),
				array(
					crm_company_ownership_obj::CLID => array("company")
				)
			);
			if ($owner_odl->count() > 0)
			{
				$vars["oid"] = isset($vars["oid"]) ? $vars["oid"] + $owner_odl->get_element_from_all("company"): $owner_odl->get_element_from_all("company");
			}
			else
			{
				return array(new object_list(), array());
			}
		}

		$companies = new object_list($vars);
		return array($companies, $customer_data);
	}

	private function set_org_tbl_caption($arr)
	{
		if(isset($_GET["os_sector"]) && !empty($_GET["os_sector"]))
		{
			$ol = new object_list(array(
				"oid" => explode(",", $_GET["os_sector"])
			));
			$c = sprintf(t("Organisatsioonid, mille tegevusalade hulgas on \"%s\""), join("\", \"", $ol->names()));
		}
		elseif(isset($_GET["os_submit"]))
		{
			$c = t("Otsingutulemused");
		}
		else
		{
			if(isset($_GET["on_web"]))
			{
				$c = (int)$_GET["on_web"] == 2 ? t("K&otilde;ik organisatsioonid, mida kuvatakse veebis") : t("K&otilde;ik organisatsioonid, mida veebis ei kuvata");
			}
			else
			{
				$c = t("K&otilde;ik organisatsioonid");
			}
		}

		// Letter filter
		if(isset($_GET["letter"]))
		{
			$c .= t(" (nime algust&auml;ht ".$_GET["letter"].")");
		}

		$arr["prop"]["vcl_inst"]->set_caption($c);
	}

	function get_org_popupmenu($arr)
	{
		$pm = get_instance("vcl/popup_menu");
		$pm->begin_menu($arr["oid"]);

		$pm->add_item(array(
			"text" => t("Muuda organisatsiooni"),
			"link" => html::get_change_url($arr["oid"], array("return_url" => get_ru()))
		));

		if (!empty($arr["cd_oid"]))
		{
			$pm->add_item(array(
				"text" => t("Muuda kliendisuhet"),
				"link" => html::get_change_url($arr["cd_oid"], array("return_url" => get_ru()))
			));
		}

		return $pm;
	}
}
