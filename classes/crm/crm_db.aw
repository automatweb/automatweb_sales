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

	@property create_customer_data_client_manager type=hidden store=no
	@property create_customer_data_categories type=hidden store=no

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

				@property os_submit type=button store=no parent=o_left_bottom
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
	protected $fields_in_use = array();

	function crm_db()
	{
		$this->init(array(
			"clid" => crm_db_obj::CLID,
		));
		$this->org_tbl_fields = array(
			"jrk" => t("Jrk"),
			"org" => t("Organisatsioon"),
			"ettevotlusvorm" => t("Ettev&otilde;lusvorm"),
			"address" => t("Aadress"),
			"e_mail" => t("E-post"),
			"url" => t("WWW"),
			"phone" => t("Telefon"),
			"org_leader" => t("Juht"),
			"modified" => t("Muudetud"),
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
					self::ORGS_BY_CUSTOMER_RELATIONS => t("Kuva ainult organisatsioone, millel on omanikorganisatsiooniga kliendisuhe"),
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
		$arr["prop"]["value"] = html::div(array(
			"id" => "org_tree",
		));
		return PROP_OK;
	}

	function _init_company_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_default_sortby("fname");

		$fields = is_array($arr["obj_inst"]->org_tbl_fields) ? $arr["obj_inst"]->org_tbl_fields : array_keys($this->org_tbl_fields);
		foreach($fields as $field)
		{
			if (!isset($this->org_tbl_fields[$field]))
			{
				continue;
			}

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
		if($arr["obj_inst"]->prop("display_mode") == self::ORGS_BY_CUSTOMER_RELATIONS and automatweb::$request->arg("os_sector"))
		{
			foreach($companies->ids() as $id)
			{
				$jrks[$id] = 0;
			}
			$jrk_odl = new object_data_list(
				array(
					"class_id" => CL_CRM_COMPANY_SECTOR_MEMBERSHIP,
					"CL_CRM_COMPANY_SECTOR_MEMBERSHIP.RELTYPE_COMPANY" => $companies->ids(),
					"CL_CRM_COMPANY_SECTOR_MEMBERSHIP.RELTYPE_SECTOR" => automatweb::$request->arg("os_sector"),
				),
				array(
					CL_CRM_COMPANY_SECTOR_MEMBERSHIP => array("company", "jrk"),
				)
			);
			foreach($jrk_odl->arr() as $jrk_od)
			{
				$jrks[$jrk_od["company"]] = $jrk_od["jrk"];
			}
			asort($jrks, SORT_NUMERIC);
		}

		if($companies->count() > $perpage)
		{
			$t->define_pageselector(array(
				"type" => "lbtxt",
				"records_per_page" => $perpage,
				"d_row_cnt" => $companies->count(),
				"no_recount" => true,
			));
			$p = isset($_GET["ft_page"]) ? (int)$_GET["ft_page"] : 0;
			$companies->slice($p * $perpage, $perpage);
		}

		$company_data = $this->get_companies_tbl_data($arr["obj_inst"], $companies);

		foreach($company_data as $company)
		{
			$row = array("id" => $company["oid"]);

			if (in_array("org", $this->fields_in_use))
			{
				$pm = $this->get_org_popupmenu(array("oid" => $company["oid"], "cd_oid" => null)); //$cd !== null ? $cd->id : null));
				$row["org"] = $pm->get_menu(array(
					"text" => parse_obj_name(!empty($company["ettevotlusvorm.shortname"]) ? "{$company["name"]} {$company["ettevotlusvorm.shortname"]}" : $company["name"]),
				));
				$row["name"] = $company["name"];

				$t->set_default_sortby("name");
			}
			if (in_array("jrk", $this->fields_in_use))
			{
				$sector = automatweb::$request->arg("os_sector") ? html::hidden(array(
					"name" => "org_tbl[{$company["oid"]}][sector]",
					"value" => automatweb::$request->arg("os_sector"),
				)) : "";

				$row["jrk"] = html::textbox(array(
					"name" => "org_tbl[{$company["oid"]}][jrk]",
					"size" => 2,
					"value" => $company["jrk"],
				)).$sector;
				$row["jrk_int"] = $company["jrk"];

				$t->set_numeric_field("jrk_int");
				$t->set_default_sortby("jrk_int");
			}
			if (in_array("ettevotlusvorm", $this->fields_in_use) and !empty($company["ettevotlusvorm"]))
			{
				$row["ettevotlusvorm"] = $company["ettevotlusvorm.shortname"];
			}
			if (in_array("org_leader", $this->fields_in_use) and !empty($company["firmajuht"]))
			{
				$row["org_leader"] = $this->change_link($company["firmajuht"], $company["firmajuht.name"]);
			}
			if (in_array("e_mail", $this->fields_in_use))
			{
				$row["e_mail"] = implode(", ", $company["e_mail"]);
			}
			if (in_array("phone", $this->fields_in_use))
			{
				$row["phone"] = implode(", ", $company["phone"]);
			}
			if (in_array("url", $this->fields_in_use))
			{
				$urls = array();
				foreach($company["url"] as $url)
				{
					$url = substr($url, strpos($url, "http://"));
					$urls[] = strlen($url) > 0 ? $url = html::href(array("url" => "http://".$url, "caption" => $url, "target" => "_blank")) : "";
				}
				$row["url"] = implode(",", $urls);
			}
			if (in_array("address", $this->fields_in_use))
			{
				$row["address"] = implode(", ", $company["address"]);
			}
			if (in_array("modified", $this->fields_in_use))
			{
				$row["modified"] = date("Y.m.d H:i" , $company["modified"]);
			}
			if (in_array("created", $this->fields_in_use))
			{
				$row["created"] = date("Y.m.d H:i" , $company["created"]);
			}

			$t->define_data($row);
		}
	}

	protected function get_companies_tbl_data($db, $companies)
	{
		$data = $companies->arr();
		$load_emails_for = $load_adresses_for = $load_urls_for = $load_phones_for = array();

		foreach($data as $oid => $v)
		{
			if (in_array("phone", $this->fields_in_use) and (in_array("url", $this->fields_in_use) or empty($v["phone_id"])))
			{
				$load_phones_for[] = $oid;
				$data[$oid]["phone"] = array();
			}
			else
			{
				$data[$oid]["phone"] = (array)$v["phone_id(CL_CRM_PHONE).name"];
			}

			if (in_array("url", $this->fields_in_use) and ($db->prop("all_ct_data") or empty($v["url_id"])))
			{
				$load_urls_for[] = $oid;
				$data[$oid]["url"] = array();
			}
			else
			{
				$data[$oid]["url"] = (array)$v["url_id(CL_EXTLINK).url"];
			}

			if (in_array("e_mail", $this->fields_in_use) and ($db->prop("all_ct_data") or empty($v["email_id"])))
			{
				$load_emails_for[] = $oid;
				$data[$oid]["e_mail"] = array();
			}
			else
			{
				$data[$oid]["e_mail"] = (array)$v["email_id(CL_ML_MEMBER).mail"];
			}

			if (in_array("address", $this->fields_in_use) and ($db->prop("all_ct_data") or empty($v["contact"])))
			{
				$load_adresses_for[] = $oid;
				$data[$oid]["address"] = array();
			}
			else
			{
				$data[$oid]["address"] = (array)$v["contact(CL_CRM_ADDRESS).name"];
			}
		}

		if (count($load_phones_for) > 0)
		{
			$odl = new object_data_list(
				array(
					"class_id" => crm_phone_obj::CLID,
					"RELTYPE_PHONE(CL_CRM_COMPANY).id" => $load_phones_for,
				),
				array(
					crm_phone_obj::CLID => array("name", "RELTYPE_PHONE(CL_CRM_COMPANY).oid"),
				)
			);

			foreach($odl->arr() as $od)
			{
				$data[$od["RELTYPE_PHONE(CL_CRM_COMPANY).oid"]]["phone"][] = $od["name"];
			}
		}
		
		if (count($load_emails_for) > 0)
		{
			$odl = new object_data_list(
				array(
					"class_id" => ml_member_obj::CLID,
					"RELTYPE_EMAIL(CL_CRM_COMPANY).id" => $load_emails_for,
				),
				array(
					ml_member_obj::CLID => array("mail", "RELTYPE_EMAIL(CL_CRM_COMPANY).oid"),
				)
			);

			foreach($odl->arr() as $od)
			{
				$data[$od["RELTYPE_EMAIL(CL_CRM_COMPANY).oid"]]["e_mail"][] = $od["mail"];
			}
		}
		
		if (count($load_adresses_for) > 0)
		{
			$odl = new object_data_list(
				array(
					"class_id" => crm_address_obj::CLID,
					"RELTYPE_ADDRESS(CL_CRM_COMPANY).id" => $load_adresses_for,
				),
				array(
					crm_address_obj::CLID => array("name", "RELTYPE_ADDRESS(CL_CRM_COMPANY).oid"),
				)
			);

			foreach($odl->arr() as $od)
			{
				$data[$od["RELTYPE_ADDRESS(CL_CRM_COMPANY).oid"]]["address"][] = $od["name"];
			}
		}
		
		if (count($load_urls_for) > 0)
		{
			$odl = new object_data_list(
				array(
					"class_id" => link_fix::CLID,
					"RELTYPE_URL(CL_CRM_COMPANY).id" => $load_urls_for,
				),
				array(
					link_fix::CLID => array("url", "RELTYPE_URL(CL_CRM_COMPANY).oid"),
				)
			);

			foreach($odl->arr() as $od)
			{
				$data[$od["RELTYPE_URL(CL_CRM_COMPANY).oid"]]["url"][] = $od["url"];
			}
		}

		return $data;
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

		$customer_data_html_url = $this->mk_my_orb("get_customer_data_prompt", array());
		$onclick = <<<SCRIPT
$.please_wait_window.show();
$.ajax({
	url: '{$customer_data_html_url}',
	success: function(html){
		$.please_wait_window.hide();
		$.prompt(html, {
			callback: function(v,m){
				if(v == true){
					$('input[type=hidden][name=create_customer_data_client_manager]').val(m.find('#client_manager').val());
					$('input[type=hidden][name=create_customer_data_categories]').val(m.find('#categories').val());
					submit_changeform('create_customer_data');
				}
			},
			buttons: { 'Salvesta': true, 'Katkesta': false }
		});
	}
});
SCRIPT;
		$tb->add_button(array(
			"name" => "create_customer_data",
			"tooltip" => t(sprintf("Loo kliendisuhe organisatsiooniga '%s'", $current_org->name())),
			"url" => "javascript:void(0)",
			"onclick" => $onclick,
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
		@param create_customer_data_client_manager type=int
		@param create_customer_data_categories type=string
			Comma separated category OIDs
	**/
	public function create_customer_data($arr)
	{
		if(!empty($arr["sel"]))
		{
			foreach($arr["sel"] as $company_id)
			{
				$company = obj($company_id, array(), crm_company_obj::CLID);
				$customer_relation = $company->find_customer_relation(null, true);

				if(!empty($arr["create_customer_data_client_manager"]) and is_oid($arr["create_customer_data_client_manager"]))
				{
					$customer_relation->set_prop("client_manager", $arr["create_customer_data_client_manager"]);
				}

				if(!empty($arr["create_customer_data_categories"]))
				{
					$customer_relation->set_prop("categories", explode(",", $arr["create_customer_data_categories"]));
				}

				$customer_relation->save();
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
				if(strlen(automatweb::$request->arg("os_sector")) > 0)
				{
					$parents = $parents_tmp = automatweb::$request->arg("os_sector");
					while(count($parents_tmp) > 0)
					{
						$ol = new object_list(array(
							"class_id" => crm_sector_obj::CLID,
							"parent" => $parents_tmp,
						));
						$parents_tmp = array_diff($ol->ids(), $parents);
						$parents = array_merge($parents, $ol->ids());
					}
					$vars["parent"] = $parents;
				}
				break;

			case self::ORGS_BY_CUSTOMER_RELATIONS:
				$oo = $arr["obj_inst"]->owner_org;
				if(!$this->can("view", $oo))
				{
					return array(new object_data_list(), array());
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
					return array(new object_data_list(), array());
				}

				if(strlen(automatweb::$request->arg("os_sector")) > 0)
				{
					$parents = $parents_tmp = automatweb::$request->arg("os_sector");
					while(count($parents_tmp) > 0)
					{
						$ol = new object_list(array(
							"class_id" => crm_sector_obj::CLID,
							"parent" => $parents_tmp,
						));
						$parents_tmp = array_diff($ol->ids(), $parents);
						$parents += $ol->ids();
					}
					$vars["pohitegevus"] = $parents;
				}
				break;
		}

		if (!$all_orgs)
		{
			if (strlen(automatweb::$request->arg("os_name")) > 0)
			{
				$vars["name"] = "%".$_GET["os_name"]."%";
			}
			if (strlen(automatweb::$request->arg("os_regnr")) > 0)
			{
				$vars["reg_nr"] = "%".$_GET["os_regnr"]."%";
			}
			if (strlen(automatweb::$request->arg("os_legal_form")) > 0)
			{
				$vars["ettevotlusvorm"] = $_GET["os_legal_form"];
			}
			if (strlen(automatweb::$request->arg("os_director")) > 0)
			{
				$vars["firmajuht.name"] = "%".$_GET["os_director"]."%";
			}
			$adr_vars = array();
			if (strlen(automatweb::$request->arg("os_address")) > 0)
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
		if(strlen(automatweb::$request->arg("os_turnover_year")) > 0)
		{
			$annual_report_filter["year"] = automatweb::$request->arg("os_turnover_year");
		}
		if(strlen(automatweb::$request->arg("os_turnover_from")) > 0 and strlen(automatweb::$request->arg("os_turnover_to")) > 0)
		{
			$annual_report_filter["turnover"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, automatweb::$request->arg("os_turnover_from"), automatweb::$request->arg("os_turnover_to"), "int");
		}
		elseif(strlen(automatweb::$request->arg("os_turnover_from")) > 0)
		{
			$annual_report_filter["turnover"] = new obj_predicate_compare(obj_predicate_compare::GREATER_OR_EQ, automatweb::$request->arg("os_turnover_from"), null, "int");
		}
		elseif(strlen(automatweb::$request->arg("os_turnover_to")) > 0)
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
				return array(new object_data_list(), array());
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
				return array(new object_data_list(), array());
			}
		}



		$companies = new object_data_list($vars, array(
			crm_company_obj::CLID => $this->get_org_tbl_odl_props($arr["obj_inst"]),
		));
		return array($companies, $customer_data);
	}

	protected function get_org_tbl_odl_props($db)
	{
		$props = array("oid");
		if (in_array("jrk", $this->fields_in_use))
		{
			$props[] = "jrk";
		}
		if (in_array("org", $this->fields_in_use))
		{
			$props[] = "name";
			$props[] = "ettevotlusvorm.shortname";
		}
		if (in_array("ettevotlusvorm", $this->fields_in_use))
		{
			$props[] = "ettevotlusvorm";
			$props[] = "ettevotlusvorm.shortname";
		}
		if (in_array("org_leader", $this->fields_in_use))
		{
			$props[] = "firmajuht";
			$props[] = "firmajuht.name";
		}
		if (in_array("e_mail", $this->fields_in_use) and !$db->prop("all_ct_data"))
		{
			$props[] = "email_id";
			$props[] = "email_id(CL_ML_MEMBER).mail";
		}
		if (in_array("phone", $this->fields_in_use) and !$db->prop("all_ct_data"))
		{
			$props[] = "phone_id";
			$props[] = "phone_id(CL_CRM_PHONE).name";
		}
		if (in_array("url", $this->fields_in_use) and !$db->prop("all_ct_data"))
		{
			$props[] = "url_id";
			$props[] = "url_id(CL_EXTLINK).url";
		}
		if (in_array("address", $this->fields_in_use) and !$db->prop("all_ct_data"))
		{
			$props[] = "contact";
			$props[] = "contact(CL_CRM_ADDRESS).name";
		}
		if (in_array("modified", $this->fields_in_use))
		{
			$props[] = "modified";
		}
		if (in_array("created", $this->fields_in_use))
		{
			$props[] = "created";
		}

		return $props;
	}

	private function set_org_tbl_caption($arr)
	{
		if(isset($_GET["os_submit"]))
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
			"link" => $this->mk_my_orb("gt_change", array("id" => $arr["oid"], "return_url" => get_ru()))
		));

		if (!empty($arr["cd_oid"]))
		{
			$pm->add_item(array(
				"text" => t("Muuda kliendisuhet"),
			"link" => $this->mk_my_orb("gt_change", array("id" => $arr["cd_oid"], "return_url" => get_ru()))
			));
		}

		return $pm;
	}

	/**
		@attrib name=get_org_tree_nodes params=pos
		@param id required
			The OID of the crm_db object
		@param node optional default=-1
			The id of the parent node for which the children will be returned.
	**/
	public function get_org_tree_nodes($arr)
	{
		$o = obj($arr["id"], array(), crm_db_obj::CLID);
		$ol = new object_list(array(
			"class_id" => crm_sector_obj::CLID,
			"parent" => (isset($arr["node"]) and $arr["node"] > 0) ? $arr["node"] : $o->prop("dir_tegevusala"),
		));
		$data = array();
		foreach($ol->names() as $oid => $name)
		{
			$data[] = array(
				"data" => iconv(aw_global_get("charset"), "utf-8", strlen($name) > 30 ? substr($name, 0, 30)."..." : $name),
				"attr" => array("id" => $oid),
				"state" => "closed"
			);
		}
		die(json_encode($data));
	}

	/**
		@attrib name=get_customer_data_prompt all_args=1
	**/
	public function get_customer_data_prompt($arr)
	{		
		$company = obj(user::get_current_company(), array(), crm_company_obj::CLID);

		$client_manager_caption = t("Kliendihaldur");
		$categories_caption = t("Kliendikategooria(d)");
		$client_manager_input = objpicker::create(array(
			"name" => "client_manager",
			"object" => obj(null, array(), crm_company_customer_data_obj::CLID),
			"clid" => crm_person_obj::CLID,
			"value" => user::get_current_person(),
		));

		$categories = $company->get_customer_categories(null, array(crm_category_obj::TYPE_GENERIC, crm_category_obj::TYPE_BUYER));
		$categories_input = html::select(array(
			"name" => "categories",
			"multiple" => true,
			"options" => $categories->names(),
		));

		$html = html::div(array(
			"id" => "customer_data_prompt",
			"content" => "{$client_manager_caption}:<br />
{$client_manager_input}<br />
{$categories_caption}:<br />
{$categories_input}"
		));

		die($html);
	}

	public function callback_pre_edit($arr)
	{
		$this->fields_in_use = is_array($arr["obj_inst"]->org_tbl_fields) ? $arr["obj_inst"]->org_tbl_fields : array_keys($this->org_tbl_fields);
	}

	public function callback_generate_scripts($arr)
	{
		if("org" === $this->use_group)
		{
			//	For the customer relation creation layer.
			load_javascript("bsnAutosuggest.js");

			load_javascript("jquery/plugins/jsTree/jquery.jstree.js");
			load_javascript("reload_properties_layouts.js");

			$ajax_url = $this->mk_my_orb("get_org_tree_nodes", array("id" => $arr["obj_inst"]->id()));

			//	TODO: I shouldn't define the URL of the CSS file!
			return "
			$('#org_tree').jstree({
				'json_data' : {
					'ajax': {
						'type': 'GET',
						'url': '{$ajax_url}',
						'async': true,
						'data': function(n) {
							return { 'node': n.attr ? n.attr('id') : -1 }; 
						}
					}
				},
				'themes': { 'theme': 'default', 'url': '/automatweb/js/jquery/plugins/jsTree/themes/default/style.css' },
				'checkbox': { 'override_ui': true },
				'plugins' : ['json_data','themes','checkbox','ui']
			});

			$('input[name=os_submit]').click(function(){
				var sectors = [];
				$('#org_tree').jstree('get_checked').each(function(){
					sectors.push($(this).attr('id'));
				});
				var legal_forms = []
				$('input[name^=os_legal_form]:checked').each(function(){
					legal_forms.push($(this).val());
				});
				reload_property(['org_tbl'], {
					os_submit: 1,
					os_sector: sectors,
					os_name: $('#os_name').val(),
					os_regnr: $('#os_regnr').val(),
					os_address: $('#os_address').val(),
					os_director: $('#os_director').val(),
					os_owner: $('#os_owner').val(),
					os_turnover_year: $('#os_turnover_year').val(),
					os_turnover: $('#os_turnover').val(),
					os_county: $('#os_county').val(),
					os_city: $('#os_city').val(),
					os_legal_form: legal_forms
				});
			});
			";
		}

		return "";
	}
}
