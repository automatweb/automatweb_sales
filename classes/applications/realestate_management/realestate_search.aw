<?php
// realestate_search.aw - Kinnisvaraobjektide otsing
/*

@classinfo syslog_type=ST_REALESTATE_SEARCH relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar

@groupinfo grp_search caption="Otsing"
@groupinfo grp_formdesign caption="Parameetrite valik"

@default table=objects
@default group=general
@default field=meta
@default method=serialize
	@property template type=select
	@caption N&auml;itamise p&otilde;hi (template)

	@property result_format type=select
	@caption Otsingutulemuste n&auml;itamise formaat

	@property result_no_form type=checkbox ch_value=1
	@comment N&auml;ita otsingutulemusi ilma otsinguvormita. Ei m&otilde;juta admin liidese otsingut.
	@caption Tulemused otsinguvormita

	@property search_result_no_redirect type=checkbox ch_value=1
	@comment Otsingutulemustelt objekti detailvaatele ei suunata. Ei m&otilde;juta admin liidese otsingut.
	@caption Suunamiseta

	@property result_pageselector_pos type=select
	@comment Otsingutulemuste tabeli navigatsiooni asukoht. Ei m&otilde;juta admin liidese otsingut.
	@caption Tabeli navigatsiooni asukoht

	@property searchform_select_size type=textbox datatype=int
	@comment [0] - v&otilde;imalus valida parameetrile vaid &uuml;ks v&auml;&auml;rtus, [1 - ...] - v&otilde;imalus valida mitu.
	@caption Otsinguvormi valikuelementide suurus

	@property searchform_columns type=textbox datatype=int default=2
	@comment Mtimes tulbas otsingu vormielemente kuvada.
	@caption Otsinguvormi tulpade arv

	@property searchform_pagesize type=textbox datatype=int default=25
	@caption Otsingutulemusi lehel

	@property realestate_mgr type=relpicker reltype=RELTYPE_OWNER clid=CL_REALESTATE_MANAGER automatic=1
	@comment Kinnisvarahalduskeskkond, mille objektide hulgast otsida soovitakse
	@caption Kinnisvarahalduskeskkond

	@property administrative_structure type=relpicker reltype=RELTYPE_ADMINISTRATIVE_STRUCTURE clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE automatic=1
	@caption Haldusjaotus

	@property default_searchparam_sort_by type=select
	@caption Default j&auml;rjestus

	@property default_searchparam_sort_order type=select
	@caption Default j&auml;rjestuse suund

	@property default_per_page type=select
	@caption Vaikimisi kuulutusi lehe kohta

	@property save_search type=checkbox ch_value=1
	@caption Salvesta otsingutulemus

	@property sort_by_options type=hidden

	@property max_results type=select
	@caption Maksimaalselt otsingutulemusi

@default group=grp_search
	@property search_class_id type=select multiple=1 size=5
	@caption Objekt

	@property search_city24_id type=textbox datatype=int size=10
	@caption City24 ID

	@property search_transaction_type type=select multiple=1 size=3
	@caption Tehingu t&uuml;&uuml;p

	@layout box1 type=hbox
	@caption Hinnavahemik
	@property search_transaction_price_min type=textbox parent=box1 size=10
	@caption Hind min
	@property search_transaction_price_max type=textbox parent=box1 size=10
	@caption Hind max

	@layout box2 type=hbox
	@comment Ruutmeetrit
	@caption &uuml;ldpinna vahemik
	@property search_total_floor_area_min type=textbox parent=box2 size=10
	@caption &uuml;ldpind min
	@property search_total_floor_area_max type=textbox parent=box2 size=10
	@caption &uuml;ldpind max

	@property search_number_of_rooms type=textbox datatype=int size=10
	@caption Tubade arv

	@property searchparam_address1 type=select multiple=1
	@caption Maakond

	@property searchparam_address2 type=select multiple=1
	@caption Linn

	@property searchparam_address3 type=select multiple=1
	@caption Linnaosa

	@property searchparam_address4 type=select multiple=1
	@caption Vald

	@property searchparam_address5 type=select multiple=1
	@caption Asula

	@property searchparam_address_street type=textbox
	@caption T&auml;nav

	// @property search_address_text type=textbox
	// @caption Aadress vabatekstina

	@property search_condition type=select multiple=1 size=5
	@caption Valmidus

	@property searchparam_fromdate type=date_select
	@caption Alates kuup&auml;evast

	@property search_usage_purpose type=select multiple=1 size=5
	@caption &auml;ripinna t&uuml;&uuml;p

	@property search_agent type=select multiple=1 size=5
	@caption Maakler

	@property search_special_status type=select
	@caption Eristaatus

	@property search_is_middle_floor type=checkbox
	@caption Pole esimene ega viimane korrus

	@property searchparam_onlywithpictures type=checkbox
	@caption N&auml;ita ainult pildiga kuulutusi

	@property searchparam_sort_by type=select
	@caption J&auml;rjestus

	@property searchparam_sort_order type=select
	@caption J&auml;rjestuse suund

	@property per_page type=select
	@caption Kuulutusi lehe kohta

	@property search_button type=submit store=no
	@caption Otsi

	@property searchresult type=table store=no
	@caption Otsingutulemused


@default group=grp_formdesign
	@property formelements type=chooser multiple=1 orient=vertical
	@caption Otsinguparameetrid

	@property formdesign_sort_options type=select multiple=1 size=5
	@comment Objektiatribuudid, mida n&auml;idatakse sortimise valikus
	@caption J&auml;rjestamise valik

	@property agent_sections type=select multiple=1 size=5
	@comment Osakonnad, mille maaklereid otsinguparameetri v&auml;&auml;rtuse valikus n&auml;idatakse
	@caption Maaklerite osakonnad


// --------------- RELATION TYPES ---------------------

@reltype OWNER clid=CL_REALESTATE_MANAGER value=1
@caption Kinnisvaraobjektide halduskeskkond

@reltype ADMINISTRATIVE_STRUCTURE clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE value=2
@caption Haldusjaotus

*/


define ("REALESTATE_SEARCH_ALL", "ALL");
define ("NEWLINE", "<br />\n");

class realestate_search extends class_base
{
	var $realestate_classes = array (
		CL_REALESTATE_HOUSE,
		CL_REALESTATE_ROWHOUSE,
		CL_REALESTATE_COTTAGE,
		CL_REALESTATE_HOUSEPART,
		CL_REALESTATE_APARTMENT,
		CL_REALESTATE_COMMERCIAL,
		CL_REALESTATE_GARAGE,
		CL_REALESTATE_LAND,
	);
	var $result_table_recordsperpage = 25;
	var $search_sort_options = array ();
	var $search_sort_orders = array ();

	function realestate_search ()
	{
		$this->init (array (
			"tpldir" => "applications/realestate_management/realestate_search",
			"clid" => CL_REALESTATE_SEARCH,
		));
		$this->search_sort_options = array (
			"name" => array ("caption" => t("Nimi"), "table" => "objects"),
			"class_id" => array ("caption" => t("Objekti t&uuml;&uuml;p"), "table" => "objects"),
			"created" => array ("caption" => t("Loodud"), "table" => "objects"),
			"modified" => array ("caption" => t("Muudetud"), "table" => "objects"),
			"transaction_price" => array("caption" => t("Hind"), "table" => "realestate_property"),
		);
		$this->search_sort_orders = array (
			"ASC" => t("Kasvav"),
			"DESC" => t("Kahanev"),
		);
		$this->per_page_options = array (
			10 => 10,
			25 => 25,
			50 => 50,
			100 => 100,
		);
	}

	function callback_on_load ($arr)
	{
		if (is_oid ($arr["request"]["id"]))
		{
			$this_object = obj ($arr["request"]["id"]);

			if ($this->can ("view", $this_object->prop ("realestate_mgr")))
			{
				$this->realestate_manager = obj ($this_object->prop ("realestate_mgr"));
			}

			if (is_oid ($this_object->prop ("administrative_structure")))
			{
				$this->administrative_structure = obj ($this_object->prop ("administrative_structure"));
			}
		}

		$this->classificator = get_instance(CL_CLASSIFICATOR);
	}

	function get_property($arr)
	{enter_function("jigaboo");
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object =& $arr["obj_inst"];

		if ($prop["group"] == "grp_search" and !is_object ($this->realestate_manager))
		{
			if ($this->can ("view", $this_object->prop ("realestate_mgr")))
			{
				$this->realestate_manager = obj ($this_object->prop ("realestate_mgr"));
			}

			if ($prop["group"] == "grp_search" and !is_object ($this->realestate_manager))
			{
				$prop["error"] = t("Kinnisvarahalduskeskkond m&auml;&auml;ramata");
				return PROP_FATAL_ERROR;
			}
		}

		if ($prop["group"] == "grp_search" and !is_object ($this->administrative_structure))
		{
			if (is_oid ($this_object->prop ("administrative_structure")))
			{
				$this->administrative_structure = obj ($this_object->prop ("administrative_structure"));
			}

			if ($prop["group"] == "grp_search" and !is_object ($this->administrative_structure))
			{
				$prop["error"] = t("Haldusjaotus m&auml;&auml;ramata");
				return PROP_FATAL_ERROR;
			}
		}

		if (!is_object($this->classificator))
		{
			$this->classificator = get_instance(CL_CLASSIFICATOR);
		}

		switch($prop["name"])
		{
			case "template":
				$prop["caption"] .=
				'<br />'.t("valik kataloogist:").' '.wordwrap($this->site_template_dir, 47, "<br />\n",1);

				$prop["options"] = array("" => "");
				$files = array();
				if ($handle = opendir($this->site_template_dir)) {
					while (false !== ($file = readdir($handle))) {
						if(substr_count($file, '.tpl') == 1) $prop["options"][substr($file, 0, -4)] = substr($file, 0, -4);
					}
				}
				break;

			case "result_format":
				$prop["caption"] .= '<br />'.t("valik kataloogist:").' '.wordwrap($this->site_template_dir, 47, "<br />\n",1);
				$prop["options"] = array("format1" => "format1");
				$files = array();
				if ($handle = opendir($this->site_template_dir)) {
					while (false !== ($file = readdir($handle))) {
						if(substr_count($file, '.tpl') == 1) $prop["options"][substr($file, 0, -4)] = substr($file, 0, -4);
					}
				}
				break;

			case "result_pageselector_pos":
				$prop["options"] = array (
					"top" => t("&Uuml;leval"),
					"bottom" => t("All"),
					"both" => t("&Uuml;leval ja all"),
				);
				break;

			case "search_class_id":
				$prop["options"] = array (
					CL_REALESTATE_HOUSE => t("Maja"),
					CL_REALESTATE_ROWHOUSE => t("Ridaelamu"),
					CL_REALESTATE_COTTAGE => t("Suvila"),
					CL_REALESTATE_HOUSEPART => t("Majaosa"),
					CL_REALESTATE_APARTMENT => t("Korter"),
					CL_REALESTATE_COMMERCIAL => t("&Auml;ripind"),
					CL_REALESTATE_GARAGE => t("Garaaz"),
					CL_REALESTATE_LAND => t("Maat&uuml;kk"),
				);
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["ci"];
				break;

			case "search_transaction_type":
				$prop_args = array (
					"clid" => CL_REALESTATE_PROPERTY,
					"name" => "transaction_type",
				);
				list ($options, $name, $use_type) = $this->classificator->get_choices($prop_args);
				// $prop["options"] = array("" => "") + $options->names();
				$prop["options"] = is_object($options) ? $options->names() : array();
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["tt"];
				break;

			case "search_transaction_price_min":
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["tpmin"];
				break;

			case "search_transaction_price_max":
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["tpmax"];
				break;

			case "search_total_floor_area_min":
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["tfamin"];
				break;

			case "search_total_floor_area_max":
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["tfamax"];
				break;

			case "search_total_floor_area":
				break;

			case "search_number_of_rooms":
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["nor"];
				break;

			case "search_city24_id":
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["c24id"];
				break;

			case "searchparam_address1":
				$list =& $this->administrative_structure->prop (array (
					"prop" => "units_by_division",
					"division" => $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_1"),
				));
				$options = is_object ($list) ? $list->names () : array (); ### maakond
				$prop["options"] = $options;
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["a1"];
				break;

			case "searchparam_address2":
				$list =& $this->administrative_structure->prop (array (
					"prop" => "units_by_division",
					"division" => $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_2"),
				));
				$options = is_object ($list) ? $list->names () : array (); ### linn
				$prop["options"] = $options;
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["a2"];
				break;

			case "searchparam_address3":
				$list =& $this->administrative_structure->prop (array (
					"prop" => "units_by_division",
					"division" => $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_3"),
				));
				$options = is_object ($list) ? $list->names () : array (); ### linnaosa
				$prop["options"] = $options;
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["a3"];
				break;

			case "searchparam_address4":
				$list =& $this->administrative_structure->prop (array (
					"prop" => "units_by_division",
					"division" => $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_4"),
				));
				$options = is_object ($list) ? $list->names () : array (); ### vald
				$prop["options"] = $options;
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["a4"];
				break;

			case "searchparam_address5":
				$list =& $this->administrative_structure->prop (array (
					"prop" => "units_by_division",
					"division" => $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_5"),
				));
				$options = is_object ($list) ? $list->names () : array (); ### asula
				$prop["options"] = $options;
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["a5"];
				break;

			case "searchparam_address_street":
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["as"];
				break;

			case "searchparam_fromdate":
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : isset ($_GET["realestate_search"]["fd"]) ? mktime (0, 0, 0, (int) $_GET["realestate_search"]["fd"]["month"], (int) $_GET["realestate_search"]["fd"]["day"], (int) $_GET["realestate_search"]["fd"]["year"]) : (time () - 60*86400);
				break;

			case "search_condition":
				$prop_args = array (
					"clid" => CL_REALESTATE_HOUSE,
					"name" => "condition",
				);
				list ($options, $name, $use_type) = $this->classificator->get_choices($prop_args);
				// $prop["options"] = array("" => "") + $options->names();
				$prop["options"] = is_object($options) ? $options->names() : array();
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["c"];
				break;

			case "search_usage_purpose":
				$prop_args = array (
					"clid" => CL_REALESTATE_COMMERCIAL,
					"name" => "usage_purpose",
				);
				list ($options, $name, $use_type) = $this->classificator->get_choices($prop_args);
				// $prop["options"] = array("" => "") + $options->names();
				$prop["options"] = is_object($options) ? $options->names() : array();
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["up"];
				break;

			case "search_special_status":
				$prop_args = array (
					"clid" => CL_REALESTATE_HOUSE,
					"name" => "special_status",
				);
				list ($options, $name, $use_type) = $this->classificator->get_choices($prop_args);
				$prop["options"] = is_object($options) ? array("" => "") + $options->names() : array();
				// $prop["options"] = $options->names();
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["ss"];
				break;

			case "search_agent":
				$sections = $this_object->prop ("agent_sections");

				if (is_array ($sections))
				{
					$options = array ();
					aw_switch_user (array ("uid" => $this->realestate_manager->prop ("almightyuser")));

					foreach ($sections as $section_oid)
					{
						if (is_oid ($section_oid))
						{
							$section = obj ($section_oid);
							$employees = $section->get_workers();
							$options += $employees->names ();
						}
					}

					natcasesort ($options);
					$prop["options"] = $options;
					$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["agent"];
					aw_restore_user ();
				}
				break;

			case "default_searchparam_sort_by":
			case "searchparam_sort_by":
			case "formdesign_sort_options":
				if ($prop["name"] == "formdesign_sort_options")
				{
					$prop["value"] = array_keys ($prop["value"]);
				}
				elseif ($prop["name"] == "searchparam_sort_by")
				{
					$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["sort_by"];
				}

				if (!is_object ($this->cl_cfgu))
				{
					$this->cl_cfgu = get_instance("cfg/cfgutils");
				}

				if (!$this->realestate_object_properties)
				{
					$this->realestate_object_properties = $this->cl_cfgu->load_properties(array ("clid" => CL_REALESTATE_SEARCH));
				}

				foreach ($this->search_sort_options as $prop_name => $prop_data)
				{
					$options[$prop_name] = $prop_data["caption"];
				}

				foreach ($this->realestate_object_properties as $prop_name => $prop_data)
				{
					if ($prop_data["table"] == "realestate_property")
					{
						$options[$prop_name] = $prop_data["caption"];
					}
				}
				if ($prop["name"] == "searchparam_sort_by")
				{
					$prop["value"] = $this_object->meta("searc_sort_by");
					if(!$prop["value"])
					{
						$prop["value"] = $this_object->prop("default_searchparam_sort_by");
					}
				}
				$prop["options"] = $options;
				break;

			case "searchparam_sort_order":
				$prop["options"] = $this->search_sort_orders;
				$prop["value"] = (!$_GET["realestate_srch"] and $this_object->prop ("save_search")) ? $prop["value"] : $_GET["realestate_search"]["sort_ord"];
				if(!$prop["value"])
				{
					$prop["value"] = $this_object->prop("default_searchparam_sort_order");
				}
				break;

			case "default_searchparam_sort_order":
				$prop["options"] = $this->search_sort_orders;
				break;

			case "per_page":
				$prop["options"] = $this->per_page_options;
				break;

			case "default_per_page":
				$prop["options"] = $this->per_page_options;
				break;

			case "max_results":
				$prop["options"] = array("" => "" , 10=>10 , 25=>25 , 50=>50 , 100=>100);
				break;

			case "searchresult":
				$table =& $prop["vcl_inst"];
				$this->_init_properties_list ($table);
				break;

			case "formelements":
				$options = array ();

				if (!is_object ($this->cl_cfgu))
				{
					$this->cl_cfgu = get_instance("cfg/cfgutils");
				}

				if (!$this->realestate_object_properties)
				{
					$this->realestate_object_properties = $this->cl_cfgu->load_properties(array ("clid" => CL_REALESTATE_SEARCH));
				}

				$applicable_properties = array (
					"search_class_id",
					"search_city24_id",
					"search_transaction_type",
					"search_transaction_price_min",
					"search_transaction_price_max",
					"search_total_floor_area_min",
					"search_total_floor_area_max",
					"search_number_of_rooms",
					"searchparam_address1",
					"searchparam_address2",
					"searchparam_address3",
					"searchparam_address4",
					"searchparam_address5",
					"searchparam_address_street",
					"search_condition",
					"searchparam_fromdate",
					"search_usage_purpose",
					"search_is_middle_floor",
					"search_special_status",
					"searchparam_onlywithpictures",
					"searchparam_sort_by",
					"searchparam_sort_order",
					"per_page",
					"search_agent",
				);

				foreach ($this->realestate_object_properties as $name => $property_data)
				{
					if (in_array ($name, $applicable_properties))
					{
						$options[$name] = $property_data["caption"];
					}
				}

				$prop["options"] = $options;
				break;

			case "agent_sections":
				$options = array ();
				$list = new object_list ($this->realestate_manager->connections_from(array(
					"type" => "RELTYPE_REALESTATEMGR_USER",
					"class_id" => CL_CRM_COMPANY,
				)));
				$companies = $list->arr ();

				foreach ($companies as $company)
				{
					$list = new object_list (array (
						"parent" => $company->id (),
						"class_id" => CL_CRM_SECTION,
						"site_id" => array (),
					));
					$options += $list->names ();
				}

				$prop["options"] = $options;
				break;

			case "sort_by_options":
				return PROP_IGNORE;
		}
exit_function("jigaboo");
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object =& $arr["obj_inst"];

		if (($prop["group"] == "grp_search") and (!$this_object->prop ("save_search")))
		{
			return PROP_IGNORE;
		}

		switch($prop["name"])
		{
			case "formdesign_sort_options":
			case "searchparam_sort_by":
				### save available options for web search.
				if (!$this->search_sort_options_loaded)
				{
					$this->cl_cfgu = get_instance("cfg/cfgutils");
					$properties = $this->cl_cfgu->load_properties(array ("clid" => CL_REALESTATE_PROPERTY));

					foreach ($properties as $prop_name => $prop_data)
					{
						if ($prop_data["table"] == "realestate_property")
						{
							$this->search_sort_options[$prop_name] = array ("caption" => $prop_data["caption"], "table" => "realestate_property");
						}
					}

					$this->search_sort_options_loaded = true;
				}

				if ($prop["name"] == "formdesign_sort_options")
				{
					$selection = array ();

					foreach ($this->search_sort_options as $prop_name => $prop_data)
					{
						if (in_array ($prop_name, $prop["value"]))
						{
							$selection[$prop_name] = $prop_data["caption"];
						}
					}

					$prop["value"] = $selection;
				}
				elseif ($prop["name"] == "searchparam_sort_by")
				{
					$prop["value"] = $this->search_sort_options;
					$this_object->set_meta("searc_sort_by" , $arr["request"]["searchparam_sort_by"]);
					$this_object->save();
				}
				break;
		}
		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function callback_pre_save ($arr)
	{
		// arr ($arr);
	}

	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function show($arr)
	{
		enter_function("re_search::show");
		$this_object = obj ($arr["id"]);

		if (is_oid ($_GET["realestate_show_property"]) and !$this_object->prop("search_result_no_redirect"))
		{
			return $this->show_property ($arr);
		}
		enter_function("re_search::show - init & generate form & search");
		$visible_formelements = (array) $this_object->prop ("formelements");
		$this->result_table_recordsperpage = (int) $this_object->prop ("searchform_pagesize");
		if (is_oid ($this_object->prop ("realestate_mgr")) and !is_object ($this->realestate_manager))
		{
			$this->realestate_manager = obj ($this_object->prop ("realestate_mgr"));
		}
		else
		{
			echo t("Kinnisvarahalduskeskkond m&auml;&auml;ramata.") . NEWLINE;
			return false;
		}

		### options
		$this->get_options ($arr);

		### values
		if ($this_object->prop ("save_search") and !$_GET["realestate_srch"])
		{
			$saved_search = true;
			$args = array (
				"realestate_srch" => 1,
				"ci" => $this_object->prop ("search_class_id"),
				"c24id" => $this_object->prop ("search_city24_id"),
				"tt" => $this_object->prop ("search_transaction_type"),
				"tpmin" => $this_object->prop ("search_transaction_price_min"),
				"tpmax" => $this_object->prop ("search_transaction_price_max"),
				"tfamin" => $this_object->prop ("search_total_floor_area_min"),
				"tfamax" => $this_object->prop ("search_total_floor_area_max"),
				"nor" => $this_object->prop ("search_number_of_rooms"),
				"a1" => $this_object->prop ("searchparam_address1"),
				"a2" => $this_object->prop ("searchparam_address2"),
				"a3" => $this_object->prop ("searchparam_address3"),
				"a4" => $this_object->prop ("searchparam_address4"),
				"a5" => $this_object->prop ("searchparam_address5"),
				"as" => $this_object->prop ("searchparam_address_street"),
				"at" => $this_object->prop ("searchparam_addresstext"),
				"fd" => $this_object->prop ("searchparam_fromdate"),
				"up" => $this_object->prop ("search_usage_purpose"),
				"agent" => $this_object->prop ("search_agent"),
				"c" => $this_object->prop ("search_condition"),
				"imf" => $this_object->prop ("search_is_middle_floor"),
				"ss" => $this_object->prop ("search_special_status"),
				"owp" => $this_object->prop ("searchparam_onlywithpictures"),
				"sort_by" => $this_object->prop ("searchparam_sort_by"),
				"sort_ord" => $this_object->prop ("searchparam_sort_order"),
				"per_page" => $this_object->prop ("per_page"),
			);
		}
		else
		{
			$saved_search = false;
			$args = array (
				"realestate_srch" => $_GET["realestate_srch"],
				"ci" => $_GET["realestate_ci"],
				"c24id" => $_GET["realestate_c24id"],
				"tt" => $_GET["realestate_tt"],
				"tpmin" => $_GET["realestate_tpmin"],
				"tpmax" => $_GET["realestate_tpmax"],
				"tfamin" => $_GET["realestate_tfamin"],
				"tfamax" => $_GET["realestate_tfamax"],
				"nor" => $_GET["realestate_nor"],
				"a1" => $_GET["realestate_a1"],
				"a2" => $_GET["realestate_a2"],
				"a3" => $_GET["realestate_a3"],
				"a4" => $_GET["realestate_a4"],
				"a5" => $_GET["realestate_a5"],
				"as" => $_GET["realestate_as"],
				"at" => $_GET["realestate_at"],
				"fd" => $_GET["realestate_fd"],
				"up" => $_GET["realestate_up"],
				"agent" => $_GET["realestate_agent"],
				"c" => $_GET["realestate_c"],
				"imf" => $_GET["realestate_imf"],
				"ss" => $_GET["realestate_ss"],
				"owp" => $_GET["realestate_owp"],
				"sort_by" => $_GET["realestate_sort_by"],
				"sort_ord" => $_GET["realestate_sort_ord"],
				"per_page" => $_GET["per_page"],
			);
		}
		$search = $this->get_search_args ($args, $this_object);
		//2kki teeks nii?
		if(!$search["sort_by"]) $search["sort_by"]=$this_object->meta("searc_sort_by");
		if(!$search["sort_by"]) $search["sort_by"]=$this_object->prop("default_searchparam_sort_by");
		if(!$search["sort_ord"]) $search["sort_ord"]=$this_object->prop("default_searchparam_sort_order");

		if (!$this_object->prop ("result_no_form"))
		{
			### captions
			$properties = $this_object->get_property_list ();

			### formelements
			$select_size = (int) $this_object->prop ("searchform_select_size");
			$form_elements = array ();
			if (in_array ("search_class_id", $visible_formelements))
			{
				$form_elements["ci"]["caption"] = $properties["search_class_id"]["caption"];
				$form_elements["ci"]["element"] = html::select(array(
					"name" => "realestate_ci",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $this->options_ci,
					"value" => ($saved_search and is_array ($search["ci"])) ? NULL : $search["ci"],
				));
			}

			if (in_array ("search_city24_id", $visible_formelements))
			{
				$form_elements["c24id"]["caption"] = $properties["search_city24_id"]["caption"];
				$form_elements["c24id"]["element"] = html::textbox(array(
					"name" => "realestate_c24id",
					"value" => $search["c24id"],
					"size" => "6",
					// "textsize" => "11px",
				));
			}

			if (in_array ("search_transaction_type", $visible_formelements))
			{
				$form_elements["tt"]["caption"] = $properties["search_transaction_type"]["caption"];
				$form_elements["tt"]["element"] = html::select(array(
					"name" => "realestate_tt",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $this->options_tt,
					"value" => ($saved_search and is_array ($search["tt"])) ? NULL : $search["tt"],
				));
			}

			if (in_array ("search_transaction_price_min", $visible_formelements))
			{
				$form_elements["tpmin"]["caption"] = $properties["search_transaction_price_min"]["caption"];
				$form_elements["tpmin"]["element"] = html::textbox(array(
					"name" => "realestate_tpmin",
					"value" => empty ($search["tpmin"]) ? "" : $search["tpmin"],
					"size" => "6",
					// "textsize" => "11px",
				));
			}

			if (in_array ("search_transaction_price_max", $visible_formelements))
			{
				$form_elements["tpmax"]["caption"] = $properties["search_transaction_price_max"]["caption"];
				$form_elements["tpmax"]["element"] = html::textbox(array(
					"name" => "realestate_tpmax",
					"value" => empty ($search["tpmax"]) ? "" : $search["tpmax"],
					"size" => "6",
					// "textsize" => "11px",
				));
			}

			if (in_array ("search_transaction_price_min", $visible_formelements) and in_array ("search_transaction_price_max", $visible_formelements))
			{
				$form_elements["tp"]["caption"] = t("Hind");
				$form_elements["tp"]["element"] = $form_elements["tpmin"]["element"]  . t(" kuni ") . $form_elements["tpmax"]["element"];
				unset ($form_elements["tpmin"]);
				unset ($form_elements["tpmax"]);
			}

			if (in_array ("search_total_floor_area_min", $visible_formelements))
			{
				$form_elements["tfamin"]["caption"] = $properties["search_total_floor_area_min"]["caption"];
				$form_elements["tfamin"]["element"] = html::textbox(array(
					"name" => "realestate_tfamin",
					"value" => empty ($search["tfamin"]) ? "" : $search["tfamin"],
					"size" => "6",
					// "textsize" => "11px",
				));
			}

			if (in_array ("search_total_floor_area_max", $visible_formelements))
			{
				$form_elements["tfamax"]["caption"] = $properties["search_total_floor_area_max"]["caption"];
				$form_elements["tfamax"]["element"] = html::textbox(array(
					"name" => "realestate_tfamax",
					"value" => empty ($search["tfamax"]) ? "" : $search["tfamax"],
					"size" => "6",
					// "textsize" => "11px",
				));
			}

			if (in_array ("search_total_floor_area_min", $visible_formelements) and in_array ("search_total_floor_area_max", $visible_formelements))
			{
				$form_elements["tfa"]["caption"] = t("&Uuml;ldpind");
				$form_elements["tfa"]["element"] = $form_elements["tfamin"]["element"]  . t(" kuni ") . $form_elements["tfamax"]["element"];
				unset ($form_elements["tfamin"]);
				unset ($form_elements["tfamax"]);
			}

			if (in_array ("search_number_of_rooms", $visible_formelements))
			{
				$form_elements["nor"]["caption"] = $properties["search_number_of_rooms"]["caption"];
				$form_elements["nor"]["element"] = html::textbox(array(
					"name" => "realestate_nor",
					"value" => $search["nor"],
					"size" => "6",
					// "textsize" => "11px",
				));
			}

			if (in_array ("searchparam_address1", $visible_formelements))
			{
				if (in_array ("searchparam_address2", $visible_formelements))
				{
					if (!is_object ($this->division2))
					{
						$this->division2 = $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_2");
					}

					$a2_division_id = $this->division2->id ();
				}

				if (in_array ("searchparam_address4", $visible_formelements))
				{
					if (!is_object ($this->division4))
					{
						$this->division4 = $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_4");
					}

					$a4_division_id = $this->division4->id ();
				}

				$form_elements["a1"]["caption"] = $properties["searchparam_address1"]["caption"];
				$onchange = (in_array ("searchparam_address2", $visible_formelements) ? "reChangeSelection(this, false, 'realestate_a2', '{$a2_division_id}');wait(1500);" : NULL);
				$form_elements["a1"]["element"] = html::select(array(
					"name" => "realestate_a1",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $this->options_a1,
					"value" => ($saved_search and is_array ($search["a1"])) ? NULL : $search["a1"],
					"onchange" => $onchange,
				));
			}
			if (in_array ("searchparam_address2", $visible_formelements))
			{
				if (in_array ("searchparam_address1", $visible_formelements))
				{
					$options = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik linnad"));
					$options = !empty ($search["a2"]) ? array (reset ($search["a2"]) => $this->options_a2[reset ($search["a2"])]) + $options : $options;
				}
				else
				{
					$options = $this->options_a2;
				}

				if (in_array ("searchparam_address3", $visible_formelements))
				{
					if (!is_object ($this->division3))
					{
						$this->division3 = $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_3");
					}

					$a3_division_id = $this->division3->id ();
				}

				$form_elements["a2"]["caption"] = $properties["searchparam_address2"]["caption"];
				$form_elements["a2"]["element"] = html::select(array(
					"name" => "realestate_a2",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $options,
					"value" => ($saved_search and is_array ($search["a2"])) ? NULL : $search["a2"],
					"onchange" => (in_array ("searchparam_address3", $visible_formelements) ? "reChangeSelection(this, false, 'realestate_a3', '{$a3_division_id}');" : NULL),
				));
			}

			if (in_array ("searchparam_address3", $visible_formelements))
			{
				if (in_array ("searchparam_address2", $visible_formelements))
				{
					$options = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik linnaosad"));
					$options = !empty ($search["a3"]) ? array (reset ($search["a3"]) => $this->options_a3[reset ($search["a3"])]) + $options : $options;
				}
				else
				{
					$options = $this->options_a3;
				}

				$form_elements["a3"]["caption"] = $properties["searchparam_address3"]["caption"];
				$form_elements["a3"]["element"] = html::select(array(
					"name" => "realestate_a3",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $options,
					"value" => ($saved_search and is_array ($search["a3"])) ? NULL : $search["a3"],
				));
			}

			if (in_array ("searchparam_address4", $visible_formelements))
			{
				if (in_array ("searchparam_address1", $visible_formelements))
				{
					$options = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik vallad"));
					$options = !empty ($search["a4"]) ? array (reset ($search["a4"]) => $this->options_a4[reset ($search["a4"])]) + $options : $options;
				}
				else
				{
					$options = $this->options_a4;
				}

				if (in_array ("searchparam_address5", $visible_formelements))
				{
					if (!is_object ($this->division5))
					{
						$this->division5 = $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_5");
					}

					$a5_division_id = $this->division5->id ();
				}

				$form_elements["a4"]["caption"] = $properties["searchparam_address4"]["caption"];
				$form_elements["a4"]["element"] = html::select(array(
					"name" => "realestate_a4",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $options,
					"value" => ($saved_search and is_array ($search["a4"])) ? NULL : $search["a4"],
					"onchange" => (in_array ("searchparam_address5", $visible_formelements) ? "reChangeSelection(this, false, 'realestate_a5', '{$a5_division_id}');" : NULL),
				));
			}

			if (in_array ("searchparam_address5", $visible_formelements))
			{
				if (in_array ("searchparam_address4", $visible_formelements))
				{
					$options = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik asulad"));
					$options = !empty ($search["a5"]) ? array (reset ($search["a5"]) => $this->options_a5[reset ($search["a5"])]) + $options : $options;
				}
				else
				{
					$options = $this->options_a5;
				}

				$form_elements["a5"]["caption"] = $properties["searchparam_address5"]["caption"];
				$form_elements["a5"]["element"] = html::select(array(
					"name" => "realestate_a5",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $options,
					"value" => ($saved_search and is_array ($search["a5"])) ? NULL : $search["a5"],
				));
			}

			if (in_array ("searchparam_address_street", $visible_formelements))
			{
				$form_elements["as"]["caption"] = $properties["searchparam_address_street"]["caption"];
				$form_elements["as"]["element"] = html::textbox(array(
					"name" => "realestate_as",
					"value" => $search["as"],
					"size" => "16",
					// "textsize" => "11px",
				));
			}

			if (in_array ("searchparam_addresstext", $visible_formelements))
			{
				$form_elements["at"]["caption"] = $properties["searchparam_addresstext"]["caption"];
				$form_elements["at"]["element"] = html::textbox(array(
					"name" => "realestate_at",
					"value" => $search["at"],
					"size" => "16",
					// "textsize" => "11px",
				));
			}

			if (in_array ("searchparam_fromdate", $visible_formelements))
			{
				$form_elements["fd"]["caption"] = $properties["searchparam_fromdate"]["caption"];
				$form_elements["fd"]["element"] = html::date_select(array(
					"name" => "realestate_fd",
					"mon_for" => 1,
					"value" => $search["fd"],
					// "textsize" => "11px",
				));
			}

			if (in_array ("search_condition", $visible_formelements))
			{
				$form_elements["c"]["caption"] = $properties["search_condition"]["caption"];
				$form_elements["c"]["element"] = html::select(array(
					"name" => "realestate_c",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $this->options_c,
					"value" => ($saved_search and is_array ($search["c"])) ? NULL : $search["c"],
				));
			}

			if (in_array ("search_usage_purpose", $visible_formelements))
			{
				$form_elements["up"]["caption"] = $properties["search_usage_purpose"]["caption"];
				$form_elements["up"]["element"] = html::select(array(
					"name" => "realestate_up",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $this->options_up,
					"value" => ($saved_search and is_array ($search["up"])) ? NULL : $search["up"],
				));
			}

			if (in_array ("search_agent", $visible_formelements))
			{
				$form_elements["agent"]["caption"] = $properties["search_agent"]["caption"];
				$form_elements["agent"]["element"] = html::select(array(
					"name" => "realestate_agent",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $this->options_agent,
					"value" => ($saved_search and is_array ($search["agent"])) ? NULL : $search["agent"],
				));
				// $form_elements["agent"]["element"] = html::textbox(array(
					// "name" => "realestate_agent",
					// "value" => $search["agent"],
					// "size" => "16",
					// "textsize" => "11px",
				// ));
			}

			if (in_array ("search_is_middle_floor", $visible_formelements))
			{
				$form_elements["imf"]["caption"] = $properties["search_is_middle_floor"]["caption"];
				$form_elements["imf"]["element"] = html::checkbox(array(
					"name" => "realestate_imf",
					"value" => 1,
					"checked" => $search["imf"],
				));
			}

			if (in_array ("search_special_status", $visible_formelements))
			{
				$form_elements["ss"]["caption"] = $properties["search_special_status"]["caption"];
				$form_elements["ss"]["element"] = html::select(array(
					"name" => "realestate_ss",
					"multiple" => $select_size,
					"size" => $select_size,
					"options" => $this->options_ss,
					"value" => ($saved_search and is_array ($search["ss"])) ? NULL : $search["ss"],
				));
			}

			if (in_array ("searchparam_onlywithpictures", $visible_formelements))
			{
				$form_elements["owp"]["caption"] = $properties["searchparam_onlywithpictures"]["caption"];
				$form_elements["owp"]["element"] = html::checkbox(array(
					"name" => "realestate_owp",
					"value" => 1,
					"checked" => $search["owp"],
				));
			}

			if (in_array ("searchparam_sort_by", $visible_formelements))
			{
				$form_elements["sort_by"]["caption"] = $properties["searchparam_sort_by"]["caption"];
				$sort_by_options = array();
				foreach($this_object->prop ("formdesign_sort_options") as $key => $val)
				{
					$sort_by_options[$key] = $this->search_sort_options[$key]["caption"];
				}
				$form_elements["sort_by"]["element"] = html::select(array(
					"name" => "realestate_sort_by",
					"options" => $sort_by_options,
					"value" => $search["sort_by"],
				));
			}
			if (in_array ("searchparam_sort_order", $visible_formelements))
			{
				$form_elements["sort_ord"]["caption"] = $properties["searchparam_sort_order"]["caption"];
				$form_elements["sort_ord"]["element"] = html::select(array(
					"name" => "realestate_sort_ord",
					"options" => $this->search_sort_orders,
					"value" => $search["sort_ord"],
				));
			}
			if (in_array ("per_page", $visible_formelements))
			{
				$form_elements["per_page"]["caption"] = $properties["per_page"]["caption"];
				$form_elements["per_page"]["element"] = html::select(array(
					"name" => "per_page",
					"options" => $this->per_page_options,
					"value" => $search["per_page"],
				));
			}
		}
		if ($_GET["realestate_srch"] == 1 or $this_object->prop ("save_search"))
		{ ### search
			$args = array (
				"this" => $this_object,
				"search" => $search,
			);
			$list =& $this->search ($args);
			$search_requested = true;
		}
		else
		{
			$list = new object_list ();
			$search_requested = false;
		}
		exit_function("re_search::show - init & generate form & search");
		enter_function("re_search::show - process searchresults");
		$result_count = $list->count ();
		if($_GET && $this_object->prop("default_per_page")) $this->result_table_recordsperpage = $this_object->prop("default_per_page");
		if($_GET["per_page"]) $this->result_table_recordsperpage = $_GET["per_page"];
		if ($result_count)
		{ ### result
			classload ("vcl/table");
			$table = new vcl_table ();
			$classes = aw_ini_get ("classes");

			switch ($this_object->prop ("result_format"))
			{
				case "format1": //see miski default variant asjade kuvamiseks
					### leave only objects for requested page in list
					$table->set_layout("realestate_searchresult");
					$table->define_field(array(
						"name" => "object",
						"caption" => NULL,
					));

					if ($this->result_count > $this->result_table_recordsperpage)
					{
						$table->define_pageselector (array (
							"type" => "text",
							"d_row_cnt" => $this->result_count,
							"no_recount" => true,
							"records_per_page" => $this->result_table_recordsperpage,
							"position" => $this_object->prop("result_pageselector_pos"),
						));
					}

					foreach ($this->realestate_classes as $cls_id)
					{
						$cl_instance_var = "cl_property_" . $cls_id;

						if (!is_object ($this->$cl_instance_var))
						{
							$this->$cl_instance_var = get_instance ($cls_id);
							$this->$cl_instance_var->classes = $classes;
						}
					}

					$property = $list->begin ();

					while (is_object ($property))
					{
						$cl_instance_var = "cl_property_" . $property->class_id ();
						$object_html = $this->$cl_instance_var->view (array (
							"this" => $property,
							"view_type" => "short",
						));
						$data = array (
							"object" => $object_html,
 						);
						$table->define_data ($data);
						$property = $list->next ();
					}
					// foreach ($list as $property)
					// {
						// $cl_instance_var = "cl_property_" . $property->class_id ();
						// $object_html = $this->$cl_instance_var->view (array (
							// "this" => $property,
							// "view_type" => "short",
						// ));

						// $data = array (
							// "object" => $object_html,
 						// );
						// $table->define_data ($data);
					// }
					$table->sortable=false;
					$result = $table->get_html ();
					break;
				default : //juhul kui on valitud mingisugune template pakkumiste kuvamiseks... et siis saab igast erinevaid variante teha jne
					if (file_exists($this->site_template_dir.'/'.$this_object->prop ("result_format").".tpl"))
					{
						$template = $this_object->prop ("result_format").".tpl";
						$this->read_template($template);
						lc_site_load("realestate_search",&$this);

					}
					$table->set_layout("realestate_searchresult");
					$table->define_field(array(
						"name" => "object",
						"caption" => NULL,
					));

					if ($this->result_count > $this->result_table_recordsperpage)
					{
						$table->define_pageselector (array (
							"type" => "text",
							"d_row_cnt" => $this->result_count,
							"no_recount" => true,
							"records_per_page" => $this->result_table_recordsperpage,
							"position" => $this_object->prop("result_pageselector_pos"),
						));
					}
					foreach ($this->realestate_classes as $cls_id)
					{
						$cl_instance_var = "cl_property_" . $cls_id;

						if (!is_object ($this->$cl_instance_var))
						{
							$this->$cl_instance_var = get_instance ($cls_id);
							$this->$cl_instance_var->classes = $classes;
						}
					}

					if($this->is_template("OBJECT"))
					{
						if ($this->can ("view", $this_object->prop ("realestate_manager")))
						{
							$realestate_manager = obj ($this_object->prop ("realestate_manager"));
							$default_icon = $realestate_manager->prop ("default_".$types[$this_object->class_id()]."_image");
						}
						$property_inst = get_instance(CL_REALESTATE_PROPERTY);

						$c = "";
						$property = $list->begin ();
						while (is_object ($property))
						{
							//see tegelt jama moment... et v6iks olla realestate_property sees miski funktsioon , mis vastavad propertyd tagastab... ja mida ta ise ka kasutab... et moment suht m6ttetu kopeerimine v2ikeste nyanssidega
							$properties = $property_inst->get_property_data (array (
								"this" => $property,
								"no_picture_data" => true,
								"no_client_data" => true,
								"no_extended_agent_data" => true,
								"no_address_data" => true,
								"required_properties" => $required_properties,
							));
							if(!$properties["picture_icon"]["value"])
							{
								$properties["picture_icon"]["value"] = $default_icon;
								$properties["picture_icon"]["strvalue"] = aw_ini_get("baseurl").$default_icon;
							}
							$i = 1;
							$no_picture_data = true;


							foreach ($properties as $name => $prop_data)
							{
								if (array_key_exists ($name, $property_inst->extras_property_names) and (int) ($prop_data["value"]))
								{
									### properties that go under tplvar "extras", from index
									$extras[] = $property_inst->extras_property_names[$name];
								}
								elseif (("checkbox" == $prop_data["type"] and !empty ($prop_data["caption"]) and (int) ($prop_data["value"]) and "has_" == substr ($name, 0, 4)))
								{
									### properties that go under tplvar "extras"
									$prop_caption = $prop_data["caption"];
									$first_char = in_array ($name, $property_inst->re_propnames_starting_with_acronym) ? $prop_caption{0} : strtolower ($prop_caption{0});
									$value = $first_char . substr ($prop_caption, 1);
									$extras[] = $value;
									$property_inst->extras_property_names[$name] = $value;// collect extras names into index array for faster mass processing.
								}
								else
								{
									if (trim ($prop_data["strvalue"]))
									{
										$prop_vars = array ();
										$prop_vars["value"] = $prop_data["strvalue"];
										$prop_vars["caption"] = $prop_data["caption"];
										$this->vars ($prop_vars);
										$data[$name] = $this->parse ($name);// main time consumer in this loop
									}

									$data[$name . "_value"] = $prop_data["strvalue"];
									$data[$name . "_caption"] = $prop_data["caption"];
								}
							}

							// "/" oli kuskile vahelt kadunud....
							$data["picture_icon_value"] = str_replace(aw_ini_get("baseurl"), aw_ini_get("baseurl").'/', $data["picture_icon_value"]);
							$data["picture_icon"] = str_replace(aw_ini_get("baseurl"), aw_ini_get("baseurl").'/', $data["picture_icon"]);
							$data["additional_info"] = $this_object->prop ("additional_info_" . aw_global_get("LC"));

							//et ei n2itataks hinda, kui see on 0
							if(!$data["transaction_price_value"] > 0)
							{
								$data["transaction_price_value"] = null;
								$data["transaction_price"] = null;
							}
							if(!$data["agent_email"])
							{
								$data["agent_email"] = "";
							}

							$cl_instance_var = "cl_property_" . $property->class_id ();
							$object_html = $this->$cl_instance_var->view (array (
								"this" => $property,
								"view_type" => "short",
							));
							$data["object"] = $object_html;
							$this->vars = $data;
							$c .= $this->parse("OBJECT");
							$property = $list->next ();
						}
						$this->vars = array (
							"OBJECT" => $c,
							"pageselector" => $table->get_html(),
						);
					}
					$result = $this->parse();
					break;
			}
		}
		else {
			// ma olen kahes kohas siin l2hema paarisaja rea jooksul poole aasta sees taolist asja v2lja kommenteerind, sest veebis n2eb topelt. miks neid juurde tehakse pidevalt?
			$result = ""; //t("Otsinguparameetritele vastavaid kuulutusi ei leitud!");
		}
		exit_function("re_search::show - process searchresults");
		enter_function("re_search::show - parse");
		### style
		if (file_exists($this->site_template_dir.'/'.$this_object->prop("template").".css"))
		{
			$template = $this_object->prop ("template") . ".css";
			$this->read_template($template);
			$this->vars (array());
			$table_style = $this->parse ();
		}
		### output
		$template = $this_object->prop ("template") . ".tpl";
		if (!$this->is_template($template))
		{
			return "";
		}
		$this->read_template($template);
		lc_site_load("realestate_search",&$this);

		if ($this_object->prop ("result_no_form") and $search_requested)
		{ #### don't show search form
			$this->vars(array(
				"table_style" => $table_style,
				"result" => $result,
				// "number_of_results" => $result_count ? t("Otsinguparameetritele vastavaid objekte ei leitud") : sprintf (t("Leitud %s objekti"), $result_count),
			));
		}
		else
		{
			$el_count = count ($form_elements);
			$column_count = $this_object->prop ("searchform_columns");
			$elements_in_column = ceil ($el_count/$column_count);
			$columns = "";
			$j = $column_count;

			while ($j--)
			{
				$i = $elements_in_column;
				$rows = "";

				while ($i--)
				{
					$caption = $element = "&nbsp;";
					$el = array_shift ($form_elements);

					if ($el)
					{
						$caption = $el["caption"];
						$element = $el["element"];
					}
					else continue;
					$this->vars (array (
						"caption" => $caption,
						"element" => $element,
					));
					$rows .= $this->parse ("RE_SEARCHFORM_ROW");
				}

				$this->vars (array (
					"RE_SEARCHFORM_ROW" => $rows,
				));
				$columns .= $this->parse ("RE_SEARCHFORM_COL");
			}

			$this->vars (array (
				"RE_SEARCHFORM_COL" => $columns,
				"buttondisplay" => $el_count ? "block" : "none",
				"columns" => $column_count,
				"a1_element_id" => "realestate_a1",
				"a2_element_id" => "realestate_a2",
				"a3_element_id" => "realestate_a3",
				"a4_element_id" => "realestate_a4",
				"a5_element_id" => "realestate_a5",
				"a2_division" => $a2_division_id,
				"a3_division" => $a3_division_id,
				"a4_division" => $a4_division_id,
				"a5_division" => $a5_division_id,
				"search_transaction_type" => array_shift($this_object->prop("search_transaction_type")),
				"session_url" => $this->mk_my_orb (
					"get_session_data",
					array(
						"id" => $this_object->id (),
					)
					,CL_REALESTATE_SEARCH, false, true),
			));
			$form = $this->parse ("RE_SEARCHFORM");

			$options_url = $this->mk_my_orb ("get_select_options", array (
				"id" => $this_object->id (),
			), CL_REALESTATE_SEARCH, false, true);

			### search result count
			$number_of_results = "";

			if ($search_requested)
			{
				$number_of_results = (0 < $this->result_count) ? sprintf (t("Leitud %s objekti"), $this->result_count) : t("Otsinguparameetritele vastavaid objekte ei leitud");
			}

			$this->vars (array (
				"RE_SEARCHFORM" => $form,
				"table_style" => $table_style,
				"result" => $result,
				"options_url" => $options_url,
				"number_of_results" => $number_of_results,
			));
		}
		$result = $this->parse();
		exit_function("re_search::show - parse");
		exit_function("re_search::show");
		return $result;
	}

	function get_options ($arr)
	{
		enter_function ("re_search::get_options");
		$this_object = obj ($arr["id"]);

		if (!is_object ($this->classificator))
		{
			$this->classificator = get_instance(CL_CLASSIFICATOR);
		}

		if (!is_object ($this->realestate_manager))
		{
			if (is_oid ($this_object->prop ("realestate_mgr")))
			{
				$this->realestate_manager = obj ($this_object->prop ("realestate_mgr"));
			}

			if (!is_object ($this->realestate_manager))
			{
				echo t("Kinnisvarahalduskeskkond otsinguobjektil defineerimata.") . NEWLINE;
				return false;
			}
		}


		if (!is_object ($this->administrative_structure))
		{
			if (is_oid ($this_object->prop ("administrative_structure")))
			{
				$this->administrative_structure = obj ($this_object->prop ("administrative_structure"));
			}

			if (!is_object ($this->administrative_structure))
			{
				if (is_oid ($this->realestate_manager->prop ("administrative_structure")))
				{
					$this->administrative_structure = obj ($this->realestate_manager->prop ("administrative_structure"));
				}

				if (!is_object ($this->administrative_structure))
				{
					echo t("Haldusjaotus otsinguobjektil ja kinnisvarahalduskeskkonnas defineerimata.") . NEWLINE;
					return false;
				}
			}
		}

		### class_id
		$this->options_ci = array (
			CL_REALESTATE_HOUSE => t("Maja"),
			CL_REALESTATE_ROWHOUSE => t("Ridaelamu"),
			CL_REALESTATE_COTTAGE => t("Suvila"),
			CL_REALESTATE_HOUSEPART => t("Majaosa"),
			CL_REALESTATE_APARTMENT => t("Korter"),
			CL_REALESTATE_COMMERCIAL => t("&Auml;ripind"),
			CL_REALESTATE_GARAGE => t("Garaaz"),
			CL_REALESTATE_LAND => t("Maat&uuml;kk"),
		);
		natcasesort ($this->options_ci);
		$this->options_ci = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik objektid")) + $this->options_ci;

		### transaction_type
		$prop_args = array (
			"clid" => CL_REALESTATE_PROPERTY,
			"name" => "transaction_type",
		);
		list ($options_tt, $name, $use_type) = $this->classificator->get_choices($prop_args);
		$lang_id = aw_global_get("lang_id");
		foreach ($options_tt->arr() as $key=> $val)
		{
			/*$trans = $val->meta("tolge");
			if($trans[$lang_id]) $this->options_tt[$key] = iconv("UTF-8", aw_global_get("charset"),  $trans[$lang_id]);
//			if($trans[$lang_id]) $this->options_tt[$key] = $trans[$lang_id];*/
			$this->options_tt[$key] = $val->trans_get_val("name");
		}
		natcasesort ($this->options_tt);
		$this->options_tt = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik tehingud")) + $this->options_tt;

		### address1
		$list =& $this->administrative_structure->prop (array (
			"prop" => "units_by_division",
			"division" => $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_1"),
		));
		$this->options_a1 = array();
		if (is_object($list))
		{
			foreach($list->arr() as $_list_item)
			{
				$this->options_a1[$_list_item->id()] = $this->trans_get_val($_list_item, "name");
			}

		}
		natcasesort ($this->options_a1);
		$this->options_a1 = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik maakonnad")) + $this->options_a1;
		### to save time, get only a minimal set of options for elementary web search
		if ($arr["get_minimal_set"])
		{
			return;
		}
/*
		### address2
		$list =& $this->administrative_structure->prop (array (
			"prop" => "units_by_division",
			"division" => $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_2"),
		));
		//$this->options_a2 = is_object ($list) ? $list->names () : array (); // linn;
		$this->options_a2 = array();
		if (is_object($list))
		{
			foreach($list->arr() as $_list_item)
			{
				$this->options_a2[$_list_item->id()] = $this->trans_get_val($_list_item, "name");
			}

		}

		natcasesort ($this->options_a2);
		$this->options_a2 = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik linnad")) + $this->options_a2;

		### address3
		if (!is_object ($this->division3))
		{
			$this->division3 = $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_3");
		}

		$list =& $this->administrative_structure->prop (array (
			"prop" => "units_by_division",
			"division" => $this->division3,
		));
		//$this->options_a3 = is_object ($list) ? $list->names () : array (); // linnaosa;
		$this->options_a3 = array();
		if (is_object($list))
		{
			foreach($list->arr() as $_list_item)
			{
				$this->options_a3[$_list_item->id()] = $this->trans_get_val($_list_item, "name");
			}

		}

		natcasesort ($this->options_a3);
		$this->options_a3 = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik linnaosad")) + $this->options_a3;

		### address4
		$list =& $this->administrative_structure->prop (array (
			"prop" => "units_by_division",
			"division" => $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_4"),
		));
		//$this->options_a4 = is_object ($list) ? $list->names () : array (); // vald;
		$this->options_a4 = array();
		if (is_object($list))
		{
			foreach($list->arr() as $_list_item)
			{
				$this->options_a4[$_list_item->id()] = $this->trans_get_val($_list_item, "name");
			}

		}

		natcasesort ($this->options_a4);
		$this->options_a4 = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik vallad")) + $this->options_a4;

		### address5
		if (!is_object ($this->division5))
		{
			$this->division5 = $this->realestate_manager->get_first_obj_by_reltype ("RELTYPE_ADDRESS_EQUIVALENT_5");
		}

		$list =& $this->administrative_structure->prop (array (
			"prop" => "units_by_division",
			"division" => $this->division5,
		));
		//$this->options_a5 = is_object ($list) ? $list->names () : array (); // asula;
		$this->options_a5 = array();
		if (is_object($list))
		{
			foreach($list->arr() as $_list_item)
			{
				$this->options_a5[$_list_item->id()] = $this->trans_get_val($_list_item, "name");
			}

		}
		natcasesort ($this->options_a5);
		$this->options_a5 = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik asulad")) + $this->options_a5;
*/
		### condition
		$prop_args = array (
			"clid" => CL_REALESTATE_HOUSE,
			"name" => "condition",
		);
		list ($options_c, $name, $use_type) = $this->classificator->get_choices($prop_args);
		foreach ($options_c->arr() as $key=> $val)
		{
			/*$trans = $val->meta("tolge");
			if($trans[$lang_id]) $this->options_c[$key] = iconv("UTF-8", aw_global_get("charset"),  $trans[$lang_id]);
	//		if($trans[$lang_id]) $this->options_c[$key] = $trans[$lang_id];
			else */
			$this->options_c[$key] = $val->trans_get_val("name");
		}
		natcasesort ($this->options_c);
		$this->options_c = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik valmidused")) + $this->options_c;

		### usage_purpose
		$prop_args = array (
			"clid" => CL_REALESTATE_COMMERCIAL,
			"name" => "usage_purpose",
		);
		list ($options_up, $name, $use_type) = $this->classificator->get_choices($prop_args);
		$this->options_up = $options_up->names();
		foreach ($options_up->arr() as $key=> $val)
		{
			/*$trans = $val->meta("tolge");
			if($trans[$lang_id]) $this->options_up[$key] = iconv("UTF-8", aw_global_get("charset"),  $trans[$lang_id]);
			//if($trans[$lang_id]) $this->options_up[$key] = $trans[$lang_id];
			else */
			$this->options_up[$key] = $val->trans_get_val("name");
		}
		$this->options_up = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik t&uuml;&uuml;bid")) + $this->options_up;

		### special_status
		$prop_args = array (
			"clid" => CL_REALESTATE_HOUSE,
			"name" => "special_status",
		);
		list ($options_ss, $name, $use_type) = $this->classificator->get_choices($prop_args);
		foreach ($options_ss->arr() as $key=> $val)
		{
			/*$trans = $val->meta("tolge");
			if($trans[$lang_id]) $this->options_ss[$key] = iconv("UTF-8", aw_global_get("charset"),  $trans[$lang_id]);
			if($trans[$lang_id]) $this->options_ss[$key] = $trans[$lang_id];
			else */
			$this->options_ss[$key] = $val->trans_get_val("name");
		}
		natcasesort ($this->options_ss);
		$this->options_ss = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik")) + $this->options_ss;
		### agent
		$sections = $this_object->prop ("agent_sections");
		$options = array ();

		if (is_array ($sections))
		{
			aw_switch_user (array ("uid" => $this->realestate_manager->prop ("almightyuser")));

			foreach ($sections as $section_oid)
			{
				if (is_oid ($section_oid))
				{
					// 1
					// $section = obj ($section_oid);
					// $employees = new object_list ($section->connections_from(array(// teeb iga objekti jaoks loadi
						// "type" => "RELTYPE_WORKERS",
						// "class_id" => CL_CRM_PERSON,
					// )));
					// END 1

					// 2
					$connection = new connection ();
					$section_connections = $connection->find (array (
						"from" => $section_oid,
						"type" => 2,
					));

					foreach ($section_connections as $connection)
					{
						$employee_ids[] = $connection["to"];
					}
					$employees = new object_list (array (
						"oid" => $employee_ids,
						"class_id" => CL_CRM_PERSON,
						"site_id" => array (),
						"lang_id" => array (),
					));
					// END 2
					$options = $options + $employees->names ();
				}
			}

			aw_restore_user ();
		}

		natcasesort ($options);
		$this->options_agent = array(REALESTATE_SEARCH_ALL => t("K&otilde;ik maaklerid")) + $options;
		exit_function ("re_search::get_options");
	}

	function agent_has_realestate_properties ($agent)
	{
		$connections = $agent->connections_to ();

		foreach ($connections as $connection)
		{
			if (in_array ($connection->prop ("from.class_id"), $this->realestate_classes))
			{
				return true;
			}
		}

		return false;
	}

	function get_search_args ($arr, $this_object = NULL)
	{
		if ($arr["realestate_srch"] == 1)
		{
			$arr["ci"] = ($arr["ci"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["ci"];
			$search_ci = (array) $arr["ci"];
			unset ($search_ci[REALESTATE_SEARCH_ALL]);

			foreach ($search_ci as $value)
			{
				if (!isset ($this->options_ci[$value]))
				{
					$search_ci = NULL;
					break;
				}
			}

			$arr["tt"] = ($arr["tt"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["tt"];
			$search_tt = (array) $arr["tt"];
			unset ($search_tt[REALESTATE_SEARCH_ALL]);

			foreach ($search_tt as $value)
			{
				if (!isset ($this->options_tt[$value]))
				{
					$search_tt = NULL;
					break;
				}
			}

			$search_tpmin = (float) $arr["tpmin"];
			$search_tpmax = (float) $arr["tpmax"];
			$search_tfamin = (float) $arr["tfamin"];
			$search_tfamax = (float) $arr["tfamax"];
			$search_nor = trim($arr["nor"]) ? (int) $arr["nor"] : NULL;
			$search_c24id = trim($arr["c24id"]) ? (int) $arr["c24id"] : NULL;

			$arr["a1"] = ($arr["a1"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["a1"];
			$search_a1 = (array) $arr["a1"];
			unset ($search_a1[REALESTATE_SEARCH_ALL]);

			foreach ($search_a1 as $value)
			{
				if (!isset ($this->options_a1[$value]))
				{
					$search_a1 = NULL;
					break;
				}
			}

			$arr["a2"] = ($arr["a2"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["a2"];
			$search_a2 = (array) $arr["a2"];
			unset ($search_a2[REALESTATE_SEARCH_ALL]);
/*
			foreach ($search_a2 as $value)
			{
				if (!isset ($this->options_a2[$value]))
				{
					$search_a2 = NULL;
					break;
				}
			}
*/
			$arr["a3"] = ($arr["a3"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["a3"];
			$search_a3 = (array) $arr["a3"];
			unset ($search_a3[REALESTATE_SEARCH_ALL]);
/*
			foreach ($search_a3 as $value)
			{
				if (!isset ($this->options_a3[$value]))
				{
					$search_a3 = NULL;
					break;
				}
			}
*/
			$arr["a4"] = ($arr["a4"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["a4"];
			$search_a4 = (array) $arr["a4"];
			unset ($search_a4[REALESTATE_SEARCH_ALL]);
/*
			foreach ($search_a4 as $value)
			{
				if (!isset ($this->options_a4[$value]))
				{
					$search_a4 = NULL;
					break;
				}
			}
*/
			$arr["a5"] = ($arr["a5"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["a5"];
			$search_a5 = (array) $arr["a5"];
			unset ($search_a5[REALESTATE_SEARCH_ALL]);
/*
			foreach ($search_a5 as $value)
			{
				if (!isset ($this->options_a5[$value]))
				{
					$search_a5 = NULL;
					break;
				}
			}
*/
			$search_as = addcslashes(str_pad ($arr["as"], 200));
			$search_at = str_pad ($arr["at"], 200);
			$search_fd = mktime (0, 0, 0, (int) $arr["fd"]["month"], (int) $arr["fd"]["day"], (int) $arr["fd"]["year"]);

			$arr["c"] = ($arr["c"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["c"];
			$search_c = (array) $arr["c"];
			unset ($search_c[REALESTATE_SEARCH_ALL]);

			foreach ($search_c as $value)
			{
				if (!isset ($this->options_c[$value]))
				{
					$search_c = NULL;
					break;
				}
			}

			$arr["up"] = ($arr["up"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["up"];
			$search_up = (array) $arr["up"];
			unset ($search_up[REALESTATE_SEARCH_ALL]);

			foreach ($search_up as $value)
			{
				if (!isset ($this->options_up[$value]))
				{
					$search_up = NULL;
					break;
				}
			}

			$arr["ss"] = ($arr["ss"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["ss"];
			$search_ss = (array) $arr["ss"];
			unset ($search_ss[REALESTATE_SEARCH_ALL]);

			foreach ($search_ss as $value)
			{
				if (!isset ($this->options_ss[$value]))
				{
					$search_ss = NULL;
					break;
				}
			}

			// $arr["agent"] = ($arr["agent"] === REALESTATE_SEARCH_ALL) ? NULL : $arr["agent"];
			// $search_agent = (array) $arr["agent"];
			// unset ($search_agent[REALESTATE_SEARCH_ALL]);

			// foreach ($search_agent as $value)
			// {
				// if (!isset ($this->options_agent[$value]))
				// {
					// $search_agent = NULL;
					// break;
				// }
			// }
			$search_agent = trim ($arr["agent"]);

			$search_imf = (int) $arr["imf"];
			$search_owp = (int) $arr["owp"];

			$options = $this_object->prop ("sort_by_options");

			if (is_array ($options))
			{
				$this->search_sort_options = $options;
			}
			$search_sort_by = array_key_exists ($arr["sort_by"], $this->search_sort_options) ? $this->search_sort_options[$arr["sort_by"]]["table"] . "" . $arr["sort_by"] : NULL;
			//tegelt see $arr["sort_by"]]["table"] j22b mulle ikka arusaamatuks, et miks teda vaja l2heb.... moment kirjutasin lihtsalt yle selle v22rtuse
			if($_GET["realestate_sort_by"])$search_sort_by = $_GET["realestate_sort_by"];

			$search_sort_ord = array_key_exists ($arr["sort_ord"], $this->search_sort_orders) ? $arr["sort_ord"] : NULL;
		}
		else
		{
			$search_fd = (time () - 182*86400);
		}

		$args = array (
			"ci" => $search_ci,
			"c24id" => $search_c24id,
			"tt" => $search_tt,
			"tpmin" => $search_tpmin,
			"tpmax" => $search_tpmax,
			"tfamin" => $search_tfamin,
			"tfamax" => $search_tfamax,
			"nor" => $search_nor,
			"a1" => $search_a1,
			"a2" => $search_a2,
			"a3" => $search_a3,
			"a4" => $search_a4,
			"a5" => $search_a5,
			"as" => $search_as,
			"at" => $search_at,
			"fd" => $search_fd,
			"up" => $search_up,
			"ss" => $search_ss,
			"agent" => $search_agent,
			"c" => $search_c,
			"imf" => $search_imf,
			"owp" => $search_owp,
			"sort_by" => $search_sort_by,
			"sort_ord" => $search_sort_ord,
			"per_page" => $arr["per_page"],
		);
		return $args;
	}

	function &search ($arr)
	{
		enter_function ("re_search::search");
		$this_object = $arr["this"];
		$search_ci = $arr["search"]["ci"];
		$search_c24id = $arr["search"]["c24id"];
		$search_tpmin = $arr["search"]["tpmin"];
		$search_tpmax = $arr["search"]["tpmax"];
		$search_tfamin = $arr["search"]["tfamin"];
		$search_tfamax = $arr["search"]["tfamax"];
		$search_fd = $arr["search"]["fd"];
		$search_nor = $arr["search"]["nor"];
		$search_tt = $arr["search"]["tt"];
		$search_c = $arr["search"]["c"];
		$search_up = $arr["search"]["up"];
		$search_ss = $arr["search"]["ss"];
		$search_agent = $arr["search"]["agent"];
		$search_a1 = $arr["search"]["a1"];
		$search_a2 = $arr["search"]["a2"];
		$search_a3 = $arr["search"]["a3"];
		$search_a4 = $arr["search"]["a4"];
		$search_a5 = $arr["search"]["a5"];
		$search_as = $arr["search"]["as"];
		$search_at = $arr["search"]["at"];
		$search_owp = $arr["search"]["owp"];
		$search_imf = $arr["search"]["imf"];
		if($search_tpmin>0)$search_tpmin--;
		if($search_tpmax>0)$search_tpmax++;
		switch($arr["search"]["sort_by"])  // NEVER, EVER, EVER!!! can you let the user enter sql via the url
		{
			case "name":
			case "class_id":
			case "jrk":
			case "created":
			case "modified":
			case "transaction_price":
				$search_sort_by = $arr["search"]["sort_by"];
				break;

			default:
				$search_sort_by = "name";
		}
		switch($arr["search"]["sort_ord"])
		{
			case "DESC":
			case "ASC":
				$search_sort_ord = $arr["search"]["sort_ord"];
				break;

			default:
				$search_sort_ord = "ASC";
				break;
		}
		$list = array ();
		$parents = array ();

		enter_function ("re_search::search - process arguments & constraints");

		if (!count ($search_ci))
		{
			$search_ci = $this->realestate_classes;
		}

		foreach ($search_ci as $clid)
		{
			switch ($clid)
			{
				case CL_REALESTATE_HOUSE:
					if (is_oid ($this->realestate_manager->prop ("houses_folder")))
					{
						$parents[] = $this->realestate_manager->prop ("houses_folder");
						// $search_ci_clstr = "CL_REALESTATE_HOUSE";
					}
					break;
				case CL_REALESTATE_ROWHOUSE:
					if (is_oid ($this->realestate_manager->prop ("rowhouses_folder")))
					{
						$parents[] = $this->realestate_manager->prop ("rowhouses_folder");
						// $search_ci_clstr = "CL_REALESTATE_ROWHOUSE";
					}
					break;
				case CL_REALESTATE_COTTAGE:
					if (is_oid ($this->realestate_manager->prop ("cottages_folder")))
					{
						$parents[] = $this->realestate_manager->prop ("cottages_folder");
						// $search_ci_clstr = "CL_REALESTATE_COTTAGE";
					}
					break;
				case CL_REALESTATE_HOUSEPART:
					if (is_oid ($this->realestate_manager->prop ("houseparts_folder")))
					{
						$parents[] = $this->realestate_manager->prop ("houseparts_folder");
						// $search_ci_clstr = "CL_REALESTATE_HOUSEPART";
					}
					break;
				case CL_REALESTATE_COMMERCIAL:
					if (is_oid ($this->realestate_manager->prop ("commercial_properties_folder")))
					{
						$parents[] = $this->realestate_manager->prop ("commercial_properties_folder");
						// $search_ci_clstr = "CL_REALESTATE_COMMERCIAL";
					}
					break;
				case CL_REALESTATE_GARAGE:
					if (is_oid ($this->realestate_manager->prop ("garages_folder")))
					{
						$parents[] = $this->realestate_manager->prop ("garages_folder");
						// $search_ci_clstr = "CL_REALESTATE_GARAGE";
					}
					break;
				case CL_REALESTATE_LAND:
					if (is_oid ($this->realestate_manager->prop ("land_estates_folder")))
					{
						$parents[] = $this->realestate_manager->prop ("land_estates_folder");
						// $search_ci_clstr = "CL_REALESTATE_LAND";
					}
					break;
				case CL_REALESTATE_APARTMENT:
					if (is_oid ($this->realestate_manager->prop ("apartments_folder")))
					{
						$parents[] = $this->realestate_manager->prop ("apartments_folder");
						// $search_ci_clstr = "CL_REALESTATE_APARTMENT";
					}
					break;
			}
		}

		if (!empty ($search_agent) && $search_agent > 0)
		{
			// ### freetext agent search
			// $agents_list = new object_list (array (
				// "class_id" => CL_CRM_PERSON,
				// "name" => "%" . $search_agent . "%",
				// "site_id" => array (),
				// "lang_id" => array (),
			// ));
			// $search_agent = $agents_list->ids ();

			### agent by selection
			$agent_constraint = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"realestate_agent1" => (int) $search_agent,
					"realestate_agent2" => (int) $search_agent,
				)
			));
		}
		else
		{
			$agent_constraint = NULL;
		}

		### compose transaction_price constraint
		if ($search_tpmin and $search_tpmax)
		{
			$tp_constraint = new obj_predicate_compare (OBJ_COMP_BETWEEN, $search_tpmin, $search_tpmax);
		}
		elseif ($search_tpmin)
		{
			$tp_constraint = new obj_predicate_compare (OBJ_COMP_GREATER_OR_EQ, $search_tpmin);
		}
		elseif ($search_tpmax)
		{
			$tp_constraint = new obj_predicate_compare (OBJ_COMP_LESS_OR_EQ, $search_tpmax);
		}
		else
		{
			$tp_constraint = NULL;
		}

		### compose total_floor_area constraint
		if ($search_tfamin and $search_tfamax)
		{
			$tfa_constraint = new obj_predicate_compare (OBJ_COMP_BETWEEN, $search_tfamin, $search_tfamax);
		}
		elseif ($search_tfamin)
		{
			$tfa_constraint = new obj_predicate_compare (OBJ_COMP_GREATER_OR_EQ, $search_tfamin);
		}
		elseif ($search_tfamax)
		{
			$tfa_constraint = new obj_predicate_compare (OBJ_COMP_LESS_OR_EQ, $search_tfamax);
		}
		else
		{
			$tfa_constraint = NULL;
		}

		### get address constraint
		if (count ($search_a5))
		{
			$search_admin_units = $search_a5;
		}
		elseif (count ($search_a4))
		{
			$search_admin_units = $search_a4;
		}
		elseif (count ($search_a3))
		{
			$search_admin_units = $search_a3;
		}
		elseif (count ($search_a2))
		{
			$search_admin_units = $search_a2;
		}
		elseif (count ($search_a1))
		{
			$search_admin_units = $search_a1;
		}
		else
		{
			$search_admin_units = false;
		}

		### sorting
		$sort = $search_sort_by ? $search_sort_by . " " . ($search_sort_ord ? $search_sort_ord : "ASC") : NULL;

		### class specific arguments
		$class_specific_args = array();
		if (
			in_array(CL_REALESTATE_HOUSE, $search_ci) or
			in_array(CL_REALESTATE_HOUSEPART, $search_ci) or
			in_array(CL_REALESTATE_ROWHOUSE, $search_ci) or
			in_array(CL_REALESTATE_COTTAGE, $search_ci) or
			in_array(CL_REALESTATE_APARTMENT, $search_ci) or
			in_array(CL_REALESTATE_COMMERCIAL, $search_ci) or
			in_array(CL_REALESTATE_GARAGE, $search_ci)
		)
		{
			$class_specific_args["condition"] = $search_c;
			$class_specific_args["total_floor_area"] = $tfa_constraint;

			if (!in_array(CL_REALESTATE_GARAGE, $search_ci))
			{
				$class_specific_args["number_of_rooms"] = (empty ($search_nor) ? NULL : $search_nor);
			}
		}

		#### apartment
		if (in_array(CL_REALESTATE_APARTMENT, $search_ci))
		{
			$class_specific_args["is_middle_floor"] = (empty ($search_imf) ? NULL : $search_imf);
		}

		#### commercial
		if (in_array(CL_REALESTATE_COMMERCIAL, $search_ci))
		{
			$class_specific_args["usage_purpose"] = $search_up;
		}

		exit_function ("re_search::search - process arguments & constraints");
		enter_function ("re_search::search - get objlist");

		### search
		$args = array (
			"class_id" => $search_ci,
			"parent" => $parents,
			"created" => new obj_predicate_compare (OBJ_COMP_GREATER, $search_fd),
			"site_id" => array (),
			"lang_id" => array (),
			"transaction_type" => $search_tt,
			"transaction_price" => $tp_constraint,
			"special_status" => $search_ss,
			"is_visible" => 1,
			// "address_connection" => $address_ids,
			// $address_constraint,
			$agent_constraint,
			"sort_by" => $sort
		);
		if ($search_c24id)
		{
			$args["city24_object_id"] = $search_c24id;
		}

		$args = $args + $class_specific_args;
		$result_list = new object_list ($args);

		// $result_list = $result_list->arr ();
		exit_function ("re_search::search - get objlist");
		enter_function ("re_search::search - address");

		$tmp_list = new object_list();
		if($search_owp)
		{
/* dbg */ enter_function ("re_search::search - owp");
			foreach ($result_list->arr() as $obj)
			{
				$conns = $obj->connections_from(array(
						"type" => "RELTYPE_REALESTATE_PICTURE",
				));
				if(sizeof($conns) >0)
				{
					$tmp_list->add($obj);
				}
			}
			$result_list = $tmp_list;
/* dbg */ exit_function ("re_search::search - owp");
		}
		//kui saidilt tuleb otsing
		if($_GET["per_page"]) $this->result_table_recordsperpage = (int) ($_GET["per_page"]);

		### search by address
		if (strlen($search_as) > 1 and $result_list->count())
		{ // by street
			$search_as = addcslashes($search_as);
			$streets = new object_list(array(
				"class_id" => CL_ADDRESS_STREET,
				"name" => "%{$search_as}%",
				"site_id" => array(),
				"lang_id" => array(),
			));

			if ($streets->count())
			{
				if (false === $search_admin_units)
				{ // search from all streets found
					$search_admin_units = $streets->ids();
				}
				else
				{ // only from streets under other specified admin units
					if (1 == $streets->count())
					{ // only one street, discard other units
						$street = $streets->begin();
						$search_admin_units = array($street->id());
					}
					else
					{ // filter streets not under specified admin units
						$streets = $streets->arr();
						foreach ($streets as $street)
						{
							$found_parent_unit = false;
							do
							{
								$sp = $street->parent();
								if (in_array($sp, $search_admin_units))
								{
									$found_parent_unit = $sp;
								}
							}
							while ($sp and !$found_parent_unit);

							if ($found_parent_unit)
							{
								$search_admin_units[reset(array_keys($search_admin_units, $found_parent_unit))] = $street->id();
							}
						}
					}
				}
			}
		}

		if ($search_admin_units !== false and $result_list->count())
		{
			$prop_ids = $result_list->ids ();
			$addr_ids = array();
			$administrative_structure = $this->realestate_manager->prop("administrative_structure");

			if (!is_oid($administrative_structure))
			{
				error::raise(array(
					"msg" => t("Haldusjaotuse struktuur defineerimata"),
					"fatal" => false,
					"show" => false,
				));
			}

			$administrative_structure = new object($administrative_structure);

/* dbg */ enter_function ("re_search::search - address 1");
			foreach ($search_admin_units as $unit_id)
			{
				if ($this->can("view", $unit_id))
				{
/* dbg */ enter_function ("re_search::search - address 1.1");
					$unit = new object($unit_id);
					$addrs = $administrative_structure->prop(array("prop" => "addresses_by_unit", "unit" => $unit));
/* dbg */ exit_function ("re_search::search - address 1.1");
/* dbg */ enter_function ("re_search::search - address 1.2");
					$addr_ids = array_merge(
						$addr_ids,
						$addrs->ids()
					);
/* dbg */ exit_function ("re_search::search - address 1.2");
				}
			}
/* dbg */ exit_function ("re_search::search - address 1");

/* dbg */ enter_function ("re_search::search - address 2");
			$address_connections = connection::find(array(
				"from" => $prop_ids,
				"to" => $addr_ids,
				"type" => 1,
			));
/* dbg */ exit_function ("re_search::search - address 2");

			### search by adminunit
			if (count ($address_connections))
			{
/* dbg */ enter_function ("re_search::search - address 3");
				$applicable_prop_ids = array();

				foreach ($address_connections as $connection)
				{
					$applicable_prop_ids[] = $connection["from"];
				}
/* dbg */ exit_function ("re_search::search - address 3");

/* dbg */ enter_function ("re_search::search - address 4");
				### filter out properties not under specified admin units
				$start_offset = (int) $_GET["ft_page"] * $this->result_table_recordsperpage;
				$end_offset = $start_offset + $this->result_table_recordsperpage;
				$result_count = 0;

				foreach ($prop_ids as $oid)
				{
					if (in_array ($oid, $applicable_prop_ids))
					{
						++$result_count;

						if (($result_count <= $start_offset) or ($result_count > $end_offset))
						{
							$result_list->remove ($oid);
						}
					}
					else
					{
						$result_list->remove ($oid);
					}
				}
/* dbg */ exit_function ("re_search::search - address 4");

				$this->result_count = $result_count;
			}
			else
			{
				$result_list = new object_list();
				$this->result_count = 0;
			}
		}
		else
		{
			enter_function ("re_search::search - tbl page filter");
			### count all
			$this->result_count = $result_list->count ();
			if($this_object->prop("max_results") >0  && $this->result_count > $this_object->prop("max_results"))
			{
				$max_limit = 1;
			}
			### limit
			$limit = ((int) $_GET["ft_page"] * $this->result_table_recordsperpage) . "," . $this->result_table_recordsperpage;
			if(
				$max_limit
				&& ((int) $_GET["ft_page"]+1) * $this->result_table_recordsperpage > $this_object->prop("max_results"))
			{
				$limit = ((int) $_GET["ft_page"] * $this->result_table_recordsperpage) . "," .( $this_object->prop("max_results") - ($_GET["ft_page"] * $this->result_table_recordsperpage));
			}
			$args["limit"] = $limit;
			//	ei m]eld v'lja miskit paremat moodust, kuidas nende ainult piltidega listiga edasi majandada
			// loodab, et ikka muu ka n[[d m]jub veel
			// tegelt kahtlane, et miks siin [ldse uus list on vaja teha
			$tmp_list = new object_list();
			$cnt = 0;
			if($search_owp)
			{
				foreach ($result_list->arr() as $obj)
				{
					if($cnt >= (((int) $_GET["ft_page"] + 1) * $this->result_table_recordsperpage))
					{
						break;
					}

					if($max_limit && $cnt > $this_object->prop("max_results"))
					{
						break;
					}

					if($cnt >= ((int) $_GET["ft_page"] * $this->result_table_recordsperpage))
					{
						$tmp_list->add($obj);
					}
					$cnt++;
				}
				$result_list = $tmp_list;
			}
			else
			{
				$result_list->filter ($args);
			}
			exit_function ("re_search::search - tbl page filter");
		}
		if($this_object->prop("max_results") >0  && $this->result_count > $this_object->prop("max_results"))
		{
			$this->result_count = $this_object->prop("max_results");
		}
		exit_function ("re_search::search - address");
		exit_function ("re_search::search");
		return $result_list;
	}

	function _init_properties_list (&$table)
	{
		### table definition
		$table->define_field(array(
			"name" => "class",
			"caption" => t("T&uuml;&uuml;p"),
		));

		$table->define_field(array(
			"name" => "address_1",
			"caption" => t("Maa&shy;kond"),
			"sortable" => 1,
		));

		$table->define_field(array(
			"name" => "address_2",
			"caption" => t("Linn"),
			"sortable" => 1,
		));

		$table->define_field(array(
			"name" => "address_3",
			"caption" => t("Vald"),
			"sortable" => 1,
		));

		$table->define_field (array (
			"name" => "transaction_type",
			"caption" => t("Tehing"),
			"sortable" => 1,
		));

		$table->define_field(array(
			"name" => "created",
			"caption" => t("Lisatud"),
			"type" => "time",
			"format" => $this->default_date_format,
			"sortable" => 1
		));

		$table->define_field(array(
			"name" => "modified",
			"caption" => t("Muudetud"),
			"type" => "time",
			"format" => $this->default_date_format,
			"sortable" => 1
		));

		$table->define_field(array(
			"name" => "owner_company",
			"caption" => t("Omanik"),
			"sortable" => 1
		));

		$table->define_field(array(
			"name" => "agent",
			"caption" => t("Maakler"),
			"filter" => $agents_filter,
			"sortable" => 1,
		));

		// $table->define_field(array(
			// "name" => "visible",
			// "caption" => <a href='javascript:selall(\"realestatemgr-is_visible\")'>t("N&auml;h&shy;tav")</a>,
			// "tooltip" => t("K&otilde;ik read: vali/kaota valik"),
		// ));

		$table->define_field(array(
			"name" => "archived",
			"caption" => t("Arhi&shy;veeri&shy;tud"),
		));

		$table->set_default_sortby ("created");
		$table->set_default_sorder ("desc");
		$table->define_pageselector (array (
			"type" => "text",
			"d_row_cnt" => $this->result_count,
			"records_per_page" => $this->result_table_recordsperpage,
		));
	}

	function show_property ($arr)
	{
		if (!$this->can ("view", $_GET["realestate_show_property"]))
		{
			return false;
		}

		$property = obj ($_GET["realestate_show_property"]);
		$cl_instance_var = "cl_property_" . $property->class_id ();

		if (!is_object ($this->$cl_instance_var))
		{
			$this->$cl_instance_var = get_instance ($property->class_id ());
		}

		return $this->$cl_instance_var->request_execute ($property);
	}

/**
    @attrib name=get_select_options nologin=1
	@param id required type=int
	@param reAddressSelected optional
	@param reAddressDivision optional
	@returns List of options separated by newline (\n). Void on error.
**/
	function get_select_options ($arr)
	{
		$this_object = obj ($arr["id"]);
		$parent_value = $arr["reAddressSelected"];
		$child_division = obj ((int) $arr["reAddressDivision"]);
		$administrative_structure = $this_object->get_first_obj_by_reltype ("RELTYPE_ADMINISTRATIVE_STRUCTURE");
		if($child_division->prop ("type") == CL_COUNTRY_CITY) $all_selection = t("K&otilde;ik linnad");
		elseif($child_division->prop ("type") == CL_COUNTRY_CITYDISTRICT) $all_selection = t("K&otilde;ik linnaosad");
		elseif($child_division->name() == "Vald") $all_selection = t("K&otilde;ik vallad");
		else $all_selection = t("K&otilde;ik asulad");

		$options = array(REALESTATE_SEARCH_ALL . "=>" . $all_selection);

		if (is_oid ($parent_value) and is_object ($child_division) and is_object ($administrative_structure))
		{
			### get units
			$list =& $administrative_structure->prop (array (
				"prop" => "units_by_division",
				"division" => $child_division,
				"parent" => $parent_value,
			));
			$administrative_units = is_object ($list) ? $list->names () : array ();
			natcasesort ($administrative_units);

			### parse units to a3_options
			foreach ($administrative_units as $unit_id => $unit_name)
			{
				$options[] = $unit_id . "=>" . $unit_name;
			}
		}

		$options = implode ("\n", $options);
		$options = html_entity_decode($options, ENT_NOQUOTES, aw_global_get("charset"));
		$charset = aw_global_get("charset");
		header ("Content-Type: text/html; charset=" . $charset);
		echo $options;
		exit;
	}

/**
    @attrib name=get_session_data nologin=1
	@returns List of options separated by newline (\n). Void on error.
**/
	function get_session_data($id)
	{
		global $data;
		if($data == "trans")
		{
			$trans = aw_global_get("transaction");
			aw_session_del("transaction") ;
			echo $trans;
		}
		if($data == "type")
		{
			$trans = aw_global_get("rs_type");
			aw_session_del("rs_type") ;
			echo $trans;
		}
		exit;
	}
}
?>
