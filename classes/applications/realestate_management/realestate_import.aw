<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_REALESTATE_IMPORT relationmgr=yes no_comment=1 no_status=1 maintainer=voldemar

@groupinfo grp_city24 caption="City24"
@groupinfo grp_city24_general caption="Seaded" parent=grp_city24
@groupinfo grp_city24_log caption="Logid" parent=grp_city24

@default table=objects
@default group=general
@default field=meta
@default method=serialize
	@property realestate_mgr type=relpicker reltype=RELTYPE_OWNER clid=CL_REALESTATE_MANAGER automatic=1
	@comment Kinnisvarahalduskeskkond mille objektide hulka soovitakse importida
	@caption Kinnisvarahalduskeskkond

	@property administrative_structure type=relpicker reltype=RELTYPE_ADMINISTRATIVE_STRUCTURE clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE automatic=1
	@caption Riik/haldusjaotus

	@property company type=relpicker reltype=RELTYPE_COMPANY clid=CL_CRM_COMPANY editonly=1
	@comment Organisatsioon mille alla objektid imporditakse
	@caption Organisatsioon

	@property no_default_picture_copy type=checkbox ch_value=1
	@caption Ei impordi default pilti

@default group=grp_city24_general
	@property city24_county type=relpicker reltype=RELTYPE_ADMIN_DIVISION clid=CL_COUNTRY_ADMINISTRATIVE_DIVISION
	@comment Haldusjaotis aadressis&uuml;steemis, mis vastab maakonnale
	@caption Maakond haldusjaotuses

	@property city24_city type=relpicker reltype=RELTYPE_ADMIN_DIVISION clid=CL_COUNTRY_ADMINISTRATIVE_DIVISION
	@caption Linn haldusjaotuses

	@property city24_citypart type=relpicker reltype=RELTYPE_ADMIN_DIVISION clid=CL_COUNTRY_ADMINISTRATIVE_DIVISION
	@caption Linnaosa haldusjaotuses

	@property city24_parish type=relpicker reltype=RELTYPE_ADMIN_DIVISION clid=CL_COUNTRY_ADMINISTRATIVE_DIVISION
	@caption Vald haldusjaotuses

	@property city24_settlement type=relpicker reltype=RELTYPE_ADMIN_DIVISION clid=CL_COUNTRY_ADMINISTRATIVE_DIVISION
	@caption Asula haldusjaotuses

	@property city24_import_url type=textbox
	@comment URL millelt objektid imporditakse
	@caption URL

	@property city24_import_memlimit type=textbox default=1024
	@caption PHP M&auml;lukasutuse limiit impordil (Mb)

	@property city24_import type=text editonly=1
	@comment URL millele p&auml;ringut tehes imporditakse objektid City24 s&uuml;steemist AW'i
	@caption City24 Importimine

	@property city24_deactivate type=text editonly=1
	@comment URL millele p&amp;auml;ringut tehes deaktiveeritakse mitteaktiivsed objektid
	@caption Mitte aktiivseks

@default group=grp_city24_log
	@property last_city24import type=hidden
	@property city24_log_table type=callback callback=callback_city24_log no_caption=1 store=no


// --------------- RELATION TYPES ---------------------

@reltype OWNER clid=CL_REALESTATE_MANAGER value=1
@caption Kinnisvaraobjektide halduskeskkond

@reltype ADMINISTRATIVE_STRUCTURE clid=CL_COUNTRY_ADMINISTRATIVE_STRUCTURE value=2
@caption Haldusjaotus

@reltype COMPANY clid=CL_CRM_COMPANY value=3
@caption Organisatsioon

@reltype ADMIN_DIVISION clid=CL_COUNTRY_ADMINISTRATIVE_DIVISION value=4
@caption Haldusjaotis

*/

define ("REALESTATE_MIN_REQUEST_INTERVAL", 60);
define ("REALESTATE_IMPORT_OK", 0);

define ("REALESTATE_IMPORT_ERR1", 1);
define ("REALESTATE_IMPORT_ERR2", 2);
define ("REALESTATE_IMPORT_ERR3", 3);
define ("REALESTATE_IMPORT_ERR4", 4);
define ("REALESTATE_IMPORT_ERR5", 5);
define ("REALESTATE_IMPORT_ERR6", 6);
define ("REALESTATE_IMPORT_ERR7", 7);
define ("REALESTATE_IMPORT_ERR8", 8);
define ("REALESTATE_IMPORT_ERR9", 9);
define ("REALESTATE_IMPORT_ERR10", 10);
define ("REALESTATE_IMPORT_ERR11", 11);
define ("REALESTATE_IMPORT_ERR61", 12);
define ("REALESTATE_IMPORT_ERR62", 13);
define ("REALESTATE_IMPORT_ERR63", 18);
define ("REALESTATE_IMPORT_ERR12", 14);
define ("REALESTATE_IMPORT_ERR13", 15);
define ("REALESTATE_IMPORT_ERR14", 16);
define ("REALESTATE_IMPORT_ERR15", 17);
define ("REALESTATE_IMPORT_ERR16", 18);
define ("REALESTATE_IMPORT_ERR17", 19);
define ("REALESTATE_IMPORT_ERR18", 20);

define ("REALESTATE_NEWLINE", "<br />");

class realestate_import extends class_base
{
	const AW_CLID = 982;

	function realestate_import()
	{
		$this->init(array(
			"tpldir" => "applications/realestate_management/realestate_import",
			"clid" => CL_REALESTATE_IMPORT
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];

		switch($prop["name"])
		{
			case "city24_county":
			case "city24_city":
			case "city24_citypart":
			case "city24_parish":
			case "city24_settlement":
				$administrative_structure = $this_object->get_first_obj_by_reltype ("RELTYPE_ADMINISTRATIVE_STRUCTURE");

				if (is_object ($administrative_structure))
				{
					$manager = obj ($this_object->prop("realestate_mgr"));
					$list = new object_list ($administrative_structure->connections_from(array(
						"type" => "RELTYPE_ADMINISTRATIVE_DIVISION",
						"class_id" => CL_COUNTRY_ADMINISTRATIVE_DIVISION,
					)));
					$prop["options"] = $list->names ();
				}
				else
				{
					$prop["error"] = t("Haldusjaotus valimata");
				}
				break;

			case "city24_import":
				$url = $this->mk_my_orb ("city24import", array (
					"id" => $this_object->id(),
					"company" => $this_object->prop ("company"),
				));
				$prop["value"] = html::href(array(
					"url" => $url,
					"target" => "_blank",
					"caption" => t("Impordi")
				));
				break;
			case "city24_deactivate":
				$url = $this->mk_my_orb ("city24deactivate", array (
					"id" => $this_object->id(),
					"company" => $this_object->prop ("company"),
				));
				$prop["value"] = html::href(array(
					"url" => $url,
					"target" => "_blank",
					"caption" => t("Tee mitteaktiivseteks")
				));
				break;
			case "company":
				if (is_oid ($this_object->prop("realestate_mgr")))
				{
					$manager = obj ($this_object->prop("realestate_mgr"));
					$list = new object_list ($manager->connections_from(array(
						"type" => "RELTYPE_REALESTATEMGR_USER",
						"class_id" => CL_CRM_COMPANY,
					)));
					$prop["options"] = $prop["options"] + $list->names ();
				}
				else
				{
					$prop["error"] = t("Kinnisvarahalduskeskkond defineerimata");
				}
				break;
		}

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

	function callback_city24_log ($arr)
	{
		$this_object = $arr["obj_inst"];
		$prop = array ();
		$log = (array) $this_object->meta ("city24_log");

		if (is_oid ($this_object->prop("realestate_mgr")))
		{
			$manager = obj ($this_object->prop("realestate_mgr"));
		}
		else
		{
			$prop["default"]["error"] = t("Kinnisvarahalduskeskkond defineerimata");
			return $prop;
		}

		$date_format = $manager->prop ("default_date_format");

		foreach ($log as $date => $entry)
		{
			foreach ($entry as $key => $line)
			{
				$entry[$key] = is_array ($line) ? implode ('<br>', $line) : $line;
			}

			$entry = implode ('<hr size=1>', $entry);
			$name = "log_{$date}";
			$prop[$name] = array(
				"type" => "text",
				"name" => $name,
				"caption" => t("Logi - ") . date ($date_format, $date),
				"value" => $entry,
			);
		}

		return $prop;
	}


/**
	@attrib name=city24deactivate nologin=1
	@param id required type=int
	@param company required type=int
	@param import_all optional type=int
	@param import_city24id optional type=int
	@param charset_from optional
	@param charset_to optional
	@param quiet optional type=int
**/
	function city24deactivate ($arr)
	{
		$this_object = obj ($arr["id"]);

		$ignore_user_abort_prev_val = ini_get("ignore_user_abort");
		$max_execution_time_prev_val = ini_get("max_execution_time");
		$max_mem_prev_val = ini_get("memory_limit");
		$memory_limit = $this_object->prop("city24_import_memlimit");
		$memory_limit = (empty($memory_limit) ? "1024" : $memory_limit) . "M";
		ini_set("memory_limit", $memory_limit);
		ini_set ("max_execution_time", "3600");
		ini_set ("ignore_user_abort", "1");
		aw_global_set ("no_cache_flush", 1);
		obj_set_opt ("no_cache", 1);


		if (1 != $quiet) { echo t("Import CITY24 xml allikast:") . REALESTATE_NEWLINE; }

		if (!empty ($arr["charset_from"]))
		{
			define ("REALESTATE_IMPORT_CHARSET_FROM", $arr["charset_from"]);
		}
		else
		{
			define ("REALESTATE_IMPORT_CHARSET_FROM", "UTF-8");
		}

		if (!empty ($arr["charset_to"]))
		{
			define ("REALESTATE_IMPORT_CHARSET_TO", $arr["charset_to"]);
		}
		else
		{
			define ("REALESTATE_IMPORT_CHARSET_TO", "ISO-8859-4");
		}


		$import_time = time();
		$last_import = $this_object->prop ("last_city24import");

		if (1 < $last_import and REALESTATE_MIN_REQUEST_INTERVAL > ($import_time - $last_import))
		{
			if (1 != $quiet) { echo t("Viimasest impordist v2hem kui " . REALESTATE_MIN_REQUEST_INTERVAL . "s. Katkestatud.") . REALESTATE_NEWLINE; }
			return;
		}

		$this_object->set_prop ("last_city24import", $import_time);

		if (!is_oid ($this_object->prop ("realestate_mgr")))
		{
			if (1 != $quiet) { echo t("Viga: halduskeskond defineerimata.") . REALESTATE_NEWLINE; }
			return REALESTATE_IMPORT_ERR1;
		}
		else
		{
			$manager = obj ($this_object->prop ("realestate_mgr"));
		}

		$import_url = $this_object->prop ("city24_import_url")."0";
arr($import_url);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $import_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 600);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
		$xml = curl_exec($ch);
		curl_close($ch);

		$parser = xml_parser_create(REALESTATE_IMPORT_CHARSET_FROM);
		xml_parse_into_struct($parser, $xml, $xml_data, $xml_index);

		$imported_properties = array();


		#### index of already imported properties' city24 id-s
		$realestate_classes = array (
			CL_REALESTATE_HOUSE,
			CL_REALESTATE_ROWHOUSE,
			CL_REALESTATE_COTTAGE,
			CL_REALESTATE_HOUSEPART,
			CL_REALESTATE_APARTMENT,
			CL_REALESTATE_COMMERCIAL,
			CL_REALESTATE_GARAGE,
			CL_REALESTATE_LAND,
		);
		$realestate_folders = array (
			$manager->prop ("houses_folder"),
			$manager->prop ("rowhouses_folder"),
			$manager->prop ("cottages_folder"),
			$manager->prop ("houseparts_folder"),
			$manager->prop ("apartments_folder"),
			$manager->prop ("commercial_properties_folder"),
			$manager->prop ("garages_folder"),
			$manager->prop ("land_estates_folder"),
		);
		$realestate_folders = array_unique($realestate_folders);

		$this->end_property_import = false;

		$imported_object_ids = array ();
		$duplicates = array ();
		$list = new object_list (array (
			"class_id" => $realestate_classes,
			"parent" => $realestate_folders,
			"city24_object_id" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
			"is_archived" => 0,
			"is_visible" => 1,
			"lang_id" => array(),
			"site_id" => array()
		));arr("objekte :".sizeof($list->ids()));

	 	$property = $list->begin();
		$city_id = (int) $property->prop ("city24_object_id");
		$imported_object_ids[$city_id] = $property->id();
		while ($property = $list->next())
		{
			$city_id = (int) $property->prop ("city24_object_id");
			$imported_object_ids[$city_id] = $property->id();
		}

		### process data
//arr($xml_data);
		foreach ($xml_data as $key => $data)
		{
			if ("ID" == $data["tag"])
			{
				$city24_id = (int) $data["value"];
				if($imported_object_ids[$city24_id])
				{
					$imported_properties[] = $imported_object_ids[$city24_id];
				}
			}
		}
arr("imporditimiseks objekte :".sizeof($imported_properties));flush();

		### set is_visible to false for objects not found in city24 xml
		if (count($imported_properties))
		{
			$company_id = $this_object->prop("company");
			$all_persons = array();

			if(is_oid($company_id))
			{
				$company = obj($company_id);
				$i = get_instance(CL_CRM_COMPANY);
				$i->get_all_workers_for_company($company, $all_persons);
			}

			// $all_persons = array_keys($all_persons);

			$realestate_objects = new object_list (array (
				"oid" => new obj_predicate_not ($imported_properties),
				"class_id" => $realestate_classes,
				"parent" => $realestate_folders,
				"city24_object_id" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),

				// "modified" => new obj_predicate_compare (OBJ_COMP_GREATER_OR_EQ, $last_import),
				"is_archived" => 0,
				"is_visible" => 1,
				"site_id" => array (),
				"lang_id" => array (),
			));
arr("deaktiviveerimiseks objekte :".sizeof($realestate_objects->ids()));

//			arr($realestate_objects);

			foreach($realestate_objects->arr() as $realestate_object)// et siis muudaks n2htamatuks vaid need objektid, mille maaklerid t88tavad selles ettev6ttes, mis on impordiobjekti juurde seostatud
			{
				print $realestate_object->prop("city24_object_id")." \n<br>";flush();
				if(!is_oid($realestate_object->prop("realestate_agent1")) and !is_oid($realestate_object->prop("realestate_agent2"))) $realestate_object->set_prop ("is_visible", 0);
				if(array_key_exists($realestate_object->prop("realestate_agent1") , $all_persons)) $realestate_object->set_prop ("is_visible", 0);
				if(array_key_exists($realestate_object->prop("realestate_agent2") , $all_persons)) $realestate_object->set_prop ("is_visible", 0);
			}

//			$realestate_objects->set_prop ("is_visible", 0);
			aw_disable_acl();
			$realestate_objects->save ();
			aw_restore_acl();
		}
		die("K6ik on kadunud...");
	}



/**
	@attrib name=city24import nologin=1
	@param id required type=int
	@param company required type=int
	@param import_all optional type=int
	@param import_city24id optional type=int
	@param charset_from optional
	@param charset_to optional
	@param quiet optional type=int
**/
	function city24_import ($arr)
	{
		// error_reporting(E_ALL);
		$this_object = obj ($arr["id"]);

		$ignore_user_abort_prev_val = ini_get("ignore_user_abort");
		$max_mem_prev_val = ini_get("memory_limit");
		$memory_limit = $this_object->prop("city24_import_memlimit");
		$memory_limit = (empty($memory_limit) ? "1024" : $memory_limit) . "M";
		ini_set("memory_limit", $memory_limit);
		ini_set ("max_execution_time", "3600");
		ini_set ("ignore_user_abort", "1");
		$status = REALESTATE_IMPORT_OK;
		$quiet = isset($arr["quiet"]) ? (int) $arr["quiet"] : 0;

		if (1 != $quiet) { echo t("Import CITY24 xml allikast:") . REALESTATE_NEWLINE; }

		if (!empty ($arr["charset_from"]))
		{
			define ("REALESTATE_IMPORT_CHARSET_FROM", $arr["charset_from"]);
		}
		else
		{
			define ("REALESTATE_IMPORT_CHARSET_FROM", "UTF-8");
		}

		if (!empty ($arr["charset_to"]))
		{
			define ("REALESTATE_IMPORT_CHARSET_TO", $arr["charset_to"]);
		}
		else
		{
			define ("REALESTATE_IMPORT_CHARSET_TO", "ISO-8859-4");
		}

		$import_time = time();
		$last_import = $this_object->prop ("last_city24import");

		if (1 < $last_import and REALESTATE_MIN_REQUEST_INTERVAL > ($import_time - $last_import))
		{ // just in case. to avoid closely sequent requests
			if (1 != $quiet) { echo t("Viimasest impordist v2hem kui " . REALESTATE_MIN_REQUEST_INTERVAL . "s. Katkestatud.") . REALESTATE_NEWLINE; }
			return;
		}

		$this_object->set_prop ("last_city24import", $import_time);

		if (!is_oid ($this_object->prop ("realestate_mgr")))
		{
			if (1 != $quiet) { echo t("Viga: halduskeskond defineerimata.") . REALESTATE_NEWLINE; }
			return REALESTATE_IMPORT_ERR1;
		}
		else
		{
			$manager = obj ($this_object->prop ("realestate_mgr"));
		}

		$import_url = $this_object->prop ("city24_import_url");

		$ch = curl_init(); // php fopen doesn't work with https, use curl
		curl_setopt($ch, CURLOPT_URL, $import_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 600);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
		$xml = curl_exec($ch);
		curl_close($ch);

		$parser = xml_parser_create(REALESTATE_IMPORT_CHARSET_FROM);
		xml_parse_into_struct($parser, $xml, $xml_data, $xml_index);

		$cl_realestate_mgr = get_instance (CL_REALESTATE_MANAGER);
		$cl_classificator = get_instance(CL_CLASSIFICATOR);
		$cl_file = new file();
		$cl_image = get_instance(CL_IMAGE);

		### variables
		$this->property_data = NULL;
		$this->end_property_import = false;
		$this->changed_maakonnad = true;
		$this->changed_transaction_types = true;
		$this->changed_conditions = true;
		$this->changed_stove_types = true;
		$this->changed_usage_purposes = true;
		$this->changed_transaction_constraints = true;
		$this->changed_priorities = true;
		$this->changed_legal_statuses = true;
		$this->changed_roof_types = true;
		$this->changed_land_uses = true;
		$admin_structure_changed = false;

		#### admin division objects
		if (
			!$this->can("view", $this_object->prop ("city24_county")) or
			!$this->can("view", $this_object->prop ("city24_parish")) or
			!$this->can("view", $this_object->prop ("city24_city")) or
			!$this->can("view", $this_object->prop ("city24_citypart")) or
			!$this->can("view", $this_object->prop ("city24_settlement"))
		)
		{
			if (1 != $quiet) { echo t("Viga: administratiivjaotuse vasted m&auml;&auml;ramata.") . REALESTATE_NEWLINE; }
			return REALESTATE_IMPORT_ERR16;
		}

		$maakond_division = obj ($this_object->prop ("city24_county"));
		$vald_division = obj ($this_object->prop ("city24_parish"));
		$linn_division = obj ($this_object->prop ("city24_city"));
		$linnaosa_division = obj ($this_object->prop ("city24_citypart"));
		$asula_division = obj ($this_object->prop ("city24_settlement"));

		#### organisatsiooni t88tajad
		$company = obj ($arr["company"]);
		$cl_user = get_instance (CL_USER);
		$cl_crm_company = get_instance (CL_CRM_COMPANY);

		// $employees = new object_list ($company->connections_from (array (
			// "type" => "RELTYPE_WORKERS",
			// "class_id" => CL_CRM_PERSON,
		// )));
		// $employee_data = $employees->names ();

		$employee_data = $cl_crm_company->get_employee_picker($company);
		$employees = array ();
		$realestate_agent_data = array ();

		foreach ($employee_data as $oid => $name)
		{
			if (trim($name))
			{
				$name = split (" ", $name);
				$name_parsed = array ();

				foreach ($name as $string)
				{
					$string = trim ($string);

					if ($string)
					{
						$name_parsed[] = $string;
					}
				}

				$employees[$oid] =join (" ", $name_parsed);
			}
		}
// /* dbg */ if (1 == $_GET["re_import_dbg"]){ arr($employees); }

		$employee_data = null;

		### initialize log
		$import_log = array ();
		$status_messages = array ();

		### indices
		#### property types
		$this->index_property_types = array (
			"Maja" => "house",
			"Ridaelamu" => "rowhouse",
			"Suvila" => "cottage",
			"Majaosa" => "housepart",
			"Korter" => "apartment",
			chr(195) . chr(132) . "ripind" => "commercial",
			"Garaazh" => "garage",
			"Maa" => "land",
		);

		#### index of already imported properties' city24 id-s
		$realestate_classes = array (
			CL_REALESTATE_HOUSE,
			CL_REALESTATE_ROWHOUSE,
			CL_REALESTATE_COTTAGE,
			CL_REALESTATE_HOUSEPART,
			CL_REALESTATE_APARTMENT,
			CL_REALESTATE_COMMERCIAL,
			CL_REALESTATE_GARAGE,
			CL_REALESTATE_LAND,
		);
		$realestate_folders = array (
			$manager->prop ("houses_folder"),
			$manager->prop ("rowhouses_folder"),
			$manager->prop ("cottages_folder"),
			$manager->prop ("houseparts_folder"),
			$manager->prop ("apartments_folder"),
			$manager->prop ("commercial_properties_folder"),
			$manager->prop ("garages_folder"),
			$manager->prop ("land_estates_folder"),
		);
		$realestate_folders = array_unique($realestate_folders);

		$imported_object_ids = array ();
		$duplicates = array ();
		$this->db_query (
		"SELECT p.city24_object_id as city24_object_id, count(p.city24_object_id) as `count` FROM realestate_property p " .
		"LEFT JOIN objects o ON o.oid = p.oid " .
		"WHERE " .
			"p.city24_object_id > 0 AND " .
			"o.status > 0 AND " .
			"o.class_id IN (" . implode (",", $realestate_classes) . ") AND " .
			"o.parent IN (" . implode (",", $realestate_folders) . ")" .
		"GROUP BY p.city24_object_id HAVING (count(p.city24_object_id) > 1)" .
		"");

		while ($duplicate = $this->db_next ())
		{
			$duplicates[] = $duplicate["city24_object_id"];
		}


		$list = new object_list (array (
			"class_id" => $realestate_classes,
			"parent" => $realestate_folders,
			"city24_object_id" => new obj_predicate_compare (OBJ_COMP_GREATER, 0),
			"lang_id" => array(),
			"site_id" => array()
		));
	 	$list->begin();

		while ($property = $list->next())
		{
			$city_id = (int) $property->prop ("city24_object_id");
			if (isset($imported_object_ids[$city_id]))
			{
				$duplicates[] = $city_id;
			}
			else
			{
				$imported_object_ids[$city_id] = $property->id();
			}
		}

		$list = null;

		if (count ($duplicates))
		{
			$duplicates = implode (",", $duplicates);
			$error_msg = t("Loetletud City24 id-ga objekte on AW objektis&uuml;steemis rohkem kui &uuml;ks:") . $duplicates . REALESTATE_NEWLINE;
			$status = REALESTATE_IMPORT_ERR4;
			$import_log[] = $error_msg;

			if (1 != $quiet) echo $error_msg;
		}

		$duplicates = null;

		$imported_properties = array ();

		// ### set locale
		// $locale1 = "et_ET";
		// $locale2 = "et";
		// $locale3 = "est";
		// $locale4 = "est_est";
		// $locale = setlocale ( LC_CTYPE, $locale1, $locale2, $locale3, $locale4);

		// if (false === $locale)
		// {
			// error::raise(array(
				// "msg" => sprintf (t("Locales (%s, %s, %s, %s) not supported in this system."), $locale1, $locale2, $locale3, $locale4),
				// "fatal" => false,
				// "show" => false,
			// ));
		// }


		### process data
		foreach ($xml_data as $key => $data)
		{
			if ("OBJECTTYPE" === $data["tag"])
			{
				$this->property_type = $this->index_property_types[$data["value"]];
			}
			elseif ($this->end_property_import)
			{ ### finish last processed property import
				if (is_object ($property))
				{
					$tmp_agent = aw_global_get("uid");
					if (empty($tmp_agent))
					{
						$tmp_agent = $property->prop ("realestate_agent1");
						if (is_oid($tmp_agent))
						{
							### get agent uid
							$connection = new connection();
							$connections = $connection->find(array(
								"to" => $tmp_agent,
								"from.class_id" => CL_USER,
								"type" => "RELTYPE_PERSON",
							));

							if (count ($connections))
							{
								$connection = reset ($connections);

								if (is_oid ($connection["from"]))
								{
									$from_obj = obj($connection["from"]);
									$tmp_agent = $from_obj->prop("uid");
									aw_switch_user (array ("uid" => $tmp_agent));
								}
							}
						}
					}

					$property->save ();
					if (1 != $quiet) { echo sprintf (t("Objekt city24 id-ga %s imporditud. AW id: %s."), $this->property_data["ID"], $property->id ()) . REALESTATE_NEWLINE; flush(); }

					$property_id = $property->id ();
					$imported_properties[] = $property_id;
					$property = NULL; // v2idetavalt on m2lukasutus unsetiga v6rreldes nii efektiivsem
				}
				else
				{
					$property_id = t("puudub");
					$property_status = REALESTATE_IMPORT_ERR18;
					$msg = sprintf (t("Viga objekti city24 id-ga %s impordil. AW objekti ei loodud."), $this->property_data["ID"]) . REALESTATE_NEWLINE;
					if (1 != $quiet) { echo $msg; }
					$status_messages[] = $msg;
				}

				if ($property_status)
				{
					if (1 != $quiet) { echo sprintf (t("Objekti city24 id-ga %s import osaliselt v6i t2ielikult eba6nnestunud. AW id %. Veastaatus: %s."), $this->property_data["ID"], $property_id, $property_status) . REALESTATE_NEWLINE; flush(); }
					$status = REALESTATE_IMPORT_ERR9;
					$import_log[] = $status_messages;
				}

				$property_status = REALESTATE_IMPORT_OK;
				$status_messages = array ();
				$this->end_property_import = false;
				flush ();
			}
			elseif (("ROW" === $data["tag"]) and ("open" === $data["type"]))
			{
				### start property import
				$new_property = true;
				$this->property_data = array ();
				$this->property_data["PILT"] = array ();
			}
			elseif (is_array ($this->property_data))
			{ ### get&process property data
				if ("ID" === $data["tag"])
				{
					$city24_id = (int) $data["value"];
					$this->property_data["ID"] = $city24_id;

					### load existing object corresponding to city24 id
					$prop_list = new object_list (array (
						"class_id" => $realestate_classes,
						"parent" => $realestate_folders,
						"city24_object_id" => $city24_id,
						"lang_id" => array(),
						"site_id" => array()
					));
					$property = $prop_list->begin();
					if ($city24_id and is_object($property))
					{
						$new_property = false;

						if (!$property->prop("is_visible"))
						{
							$property->set_prop("is_visible", 1);
						}
					}
					else
					{
						$property = null;
					}
				}
				elseif ("EDIT_DATE" === $data["tag"] and is_object($property))
				{
					list ($year, $month, $day, $hour, $min, $sec) = sscanf(trim ($data["value"]),"%u-%u-%u %u:%u:%u");

					if (1992 < $year and 1 <= $month and 12 >= $month and 1 <= $day and 31 >= $day and 0 <= $hour and 24 >= $hour)
					{
						$city24_modified = mktime ($hour, $min, $sec, $month, $day, $year);

						if (
							empty($arr["import_all"])  and
							1 < $last_import and
							$city24_modified < $last_import and
							$city24_modified < $import_time and
							$city24_modified < $property->meta("city24_last_import")
						)
						{
							$this->property_data = null;
							$this->end_property_import = true;
						}
					}
				}
				elseif
				(
					"TEHING" === $data["tag"] or
					"MAAKOND" === $data["tag"] or
					"LINN" === $data["tag"] or
					"LINNAOSA" === $data["tag"] or
					"VALD" === $data["tag"] or
					"ASULA" === $data["tag"] or
					"TANAV" === $data["tag"] or
					"MAJANR" === $data["tag"] or
					"MAAKLER_NIMI" === $data["tag"] or
					"MAAKLER_EMAIL" === $data["tag"] or
					"MAAKLER_TELEFON" === $data["tag"] or
					"PRIO" === $data["tag"] or
					"VALMIDUS" === $data["tag"] or
					"NAITAMAJANR" === $data["tag"] or
					"OMANDIVORM" === $data["tag"] or
					"HIND" === $data["tag"] or
					"TEHING_MYYGIHIND" === $data["tag"] or
					"TEHING_ETTEMAKS" === $data["tag"] or
					"TEHING_KUUYYR" === $data["tag"] or
					"ASUKOHT_KORRUSEID" === $data["tag"] or
					"ASUKOHT_KORRUS" === $data["tag"] or
					"LISAINFO_INFO" === $data["tag"] or
					"SEISUKORD_SIGNA" === $data["tag"] or
					"SEISUKORD_TURVAUKS" === $data["tag"] or
					"SEISUKORD_TREPIKODA" === $data["tag"] or
					"SEISUKORD_LIFT" === $data["tag"] or
					"KIRJELDUS_YLDPIND" === $data["tag"] or
					"KIRJELDUS_TOAD" === $data["tag"] or
					"KIRJELDUS_AHJUKYTE" === $data["tag"] or
					"KIRJELDUS_ELKYTE" === $data["tag"] or
					"KIRJELDUS_DUSH" === $data["tag"] or
					"KIRJELDUS_KYLMKAPP" === $data["tag"] or
					"KIRJELDUS_KELDER" === $data["tag"] or
					"KIRJELDUS_PARKETT" === $data["tag"] or
					"KIRJELDUS_KAABELTV" === $data["tag"] or
					"KIRJELDUS_BOILER" === $data["tag"] or
					"KIRJELDUS_MOOBELVOIM" === $data["tag"] or
					"KIRJELDUS_MAGAMISTOAD" === $data["tag"] or
					"KIRJELDUS_VANNITOAD" === $data["tag"] or
					"KIRJELDUS_KESKKYTE" === $data["tag"] or
					"KIRJELDUS_MOOBEL" === $data["tag"] or
					"KIRJELDUS_GARAAZH" === $data["tag"] or
					"KIRJELDUS_RODU" === $data["tag"] or
					"KIRJELDUS_PESUMASIN" === $data["tag"] or
					"KIRJELDUS_TELEFON" === $data["tag"] or
					"KIRJELDUS_KOOGISUURUS" === $data["tag"] or
					"KIRJELDUS_TV" === $data["tag"] or
					"KIRJELDUS_VANN" === $data["tag"] or
					"KIRJELDUS_SAUN" === $data["tag"] or
					"KIRJELDUS_KAMIN" === $data["tag"] or
					"KIRJELDUS_GAASIKYTE" === $data["tag"] or
					"KIRJELDUS_KOOK" === $data["tag"] or
					"KIRJELDUS_TELEFONE" === $data["tag"] or
					"KIRJELDUS_TOOSTUSVOOL" === $data["tag"] or
					"KIRJELDUS_LOKKANAL" === $data["tag"] or
					"MYYJA_NIMI" === $data["tag"] or
					"MYYJA_TELEFON" === $data["tag"] or
					"MYYJA_EMAIL" === $data["tag"] or
					"PINNA_TYYP" === $data["tag"] or
					"KOMMU_ISDN" === $data["tag"] or
					"KOMMU_ELEKTER" === $data["tag"] or
					"KOMMU_VESI" === $data["tag"] or
					"PLIIT" === $data["tag"] or
					"KRUNT" === $data["tag"] or
					"KATUS" === $data["tag"] or
					"KOHANIMI" === $data["tag"] or
					"MUU_DETAILPLAN" === $data["tag"] or
					"OTSTARVE_VEEL" === $data["tag"] or
					"ASUKOHT_KORTERINR" === $data["tag"] or
					"IKOONI_URL" === $data["tag"] or
					"KIRJELDUS_GARDEROOB" === $data["tag"] or
					"KIRJELDUS_TSENTKANAL" === $data["tag"] or
					"KIRJELDUS_WC" === $data["tag"] or
					"KOMMU_KANALISATSIOON" === $data["tag"] or
					"KOMMU_INTERNET" === $data["tag"] or
					"LINN_LINNAOSA" === $data["tag"] or
					"MUU_KAUGUSTLN" === $data["tag"] or
					"MUU_OTSTARBEMUUT" === $data["tag"] or
					"NAITAKORTERINR" === $data["tag"] or
					"SEISUKORD_EHITUSAASTA" === $data["tag"] or
					"TEHING_KUURENT" === $data["tag"] or
					"TEHING_PIIRANGUD" === $data["tag"] or
					"IS_BOOKED" === $data["tag"] or
					"BOOKED_UNTIL_DATE" === $data["tag"]
				)
				{
					$this->property_data[$data["tag"]] = $data["value"];//!!! mis puhul value on siin undefined index?
				}
				elseif ("PILT" === substr ($data["tag"], 0, 4))
				{
					$pic_nr = (int) substr ($data["tag"], 4);
					$this->property_data["PILT"][$pic_nr] = trim($data["value"]);
				}
				elseif (("ROW" === $data["tag"]) and ("close" === $data["type"]))
				{ ### import property to aw
					$property_status = REALESTATE_IMPORT_OK;
					$this->end_property_import = true;

					### get agent
					#### city24 agent priority
					if (isset($this->property_data["MAAKLER_NIMI"]))
					{
						$agent_data = split (" ", iconv (REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, $this->property_data["MAAKLER_NIMI"]));
						$agent_data_parsed = array ();

						foreach ($agent_data as $string)
						{
							$string = trim ($string);

							if (strlen($string))
							{
								$agent_data_parsed[] = $string;
							}
						}

						$agent_data = join (" ", $agent_data_parsed);
						$agent_oid = (int) reset (array_keys ($employees, $agent_data));

						if (!is_oid($agent_oid))
						{
							$agent_data_parsed1 = $agent_data_parsed;
							$agent_data = array_shift($agent_data_parsed1);
							$agent_data = $agent_data . " " . join("-", $agent_data_parsed1);
							$agent_oid = (int) reset(array_keys($employees, $agent_data));

							if (!is_oid($agent_oid))
							{
								$agent_data_parsed1 = $agent_data_parsed;
								$agent_data = array_pop($agent_data_parsed1);
								$agent_data = join("-", $agent_data_parsed1) . " " . $agent_data;
								$agent_oid = (int) reset(array_keys($employees, $agent_data));
							}
						}

// /* dbg */ if (1 == $_GET["re_import_dbg"]){ echo "maakler: [{$agent_data}]"; }

						#### aw agent priority
						// if (!$new_property)
						// {
							// $agent_oid = $property->prop ("realestate_agent1");
						// }

						// if ($new_property or !is_oid ($agent_oid))
						// {
							// $agent_data = iconv (REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["MAAKLER_NIMI"]));
							// $agent_oid = (int) reset (array_keys ($employees, $agent_data));

	// /* dbg */ if (1 == $_GET["re_import_dbg"]){ echo "maakler: [{$agent_data}]"; }

							// foreach ($employees as $employee_oid => $employee_name)
							// {
								// if ($agent_data and $employee_name and ($agent_data == ((string) $employee_name)))
								// {
									// $agent_oid = (int) $employee_oid;
									// break;
								// }
							// }
						// }

						if (!is_oid ($agent_oid))
						{
							$status_messages[] = sprintf (t("Viga importides objekti city24 id-ga %s. Maakleri nimele [%s] ei vastanud s&uuml;steemis &uuml;hkti kasutajat."), $city24_id, $agent_data) . REALESTATE_NEWLINE;

							if (1 != $quiet)
							{
								echo end ($status_messages);
							}

							$property_status = REALESTATE_IMPORT_ERR5;
							continue;
						}
					}
					else
					{
						$status_messages[] = sprintf (t("Viga importides objekti city24 id-ga %s. Maakler puudub."), $city24_id) . REALESTATE_NEWLINE;

						if (1 != $quiet)
						{
							echo end ($status_messages);
						}

						$property_status = REALESTATE_IMPORT_ERR5;
						continue;
					}


					### load agent data
					if (!isset ($realestate_agent_data[$agent_oid]))
					{
						$agent = obj ($agent_oid);
						$realestate_agent_data[$agent_oid]["object"] = $agent;

						### get section
						$section = $agent->get_first_obj_by_reltype ("RELTYPE_SECTION");

						if (is_object ($section))
						{
							$section = $section->id ();
						}
						else
						{
							$status_messages[] = sprintf (t("Importides objekti city24 id-ga %s ilmnes: maakleril puudub &uuml;ksus."), $city24_id) . REALESTATE_NEWLINE;

							if (1 != $quiet)
							{
								echo end ($status_messages);
							}

							$property_status = REALESTATE_IMPORT_ERR6;
							$section = NULL;
						}

						$realestate_agent_data[$agent_oid]["section_oid"] = $section;

						### get agent uid
						$connection = new connection();
						$connections = $connection->find(array(
							"to" => $agent->id (),
							"from.class_id" => CL_USER,
							"type" => "RELTYPE_PERSON",
						));

						if (count ($connections))
						{
							$connection = reset ($connections);

							if (is_oid ($connection["from"]))
							{
								$from_obj = obj($connection["from"]);
								$agent_uid = $from_obj->prop("uid");
							}
							else
							{
								$status_messages[] = sprintf (t("Viga importides objekti city24 id-ga %s: maakleri kasutajaandmetes on viga. Osa infot j&auml;&auml;b salvestamata."), $city24_id) . REALESTATE_NEWLINE;

								if (1 != $quiet)
								{
									echo end ($status_messages);
								}

								$property_status = REALESTATE_IMPORT_ERR61;
								$agent_uid = false;
							}
						}
						else
						{
							$status_messages[] = sprintf (t("Viga importides objekti city24 id-ga %s: maakleri kasutajaandmeid ei leitud. Osa infot j&auml;&auml;b salvestamata."), $city24_id) . REALESTATE_NEWLINE;

							if (1 != $quiet)
							{
								echo end ($status_messages);
							}

							$property_status = REALESTATE_IMPORT_ERR62;
							$agent_uid = false;
						}

						$realestate_agent_data[$agent_oid]["agent_uid"] = $agent_uid;
					}

					### switch to property owner user
					if ($realestate_agent_data[$agent_oid]["agent_uid"])
					{
						aw_switch_user (array ("uid" => $realestate_agent_data[$agent_oid]["agent_uid"]));
// /* dbg */ if (1 == $_GET["re_import_dbg"]){ echo "kasutaja vahetatud maakleri kasutajaks: [{$realestate_agent_data[$agent_oid]["agent_uid"]}]"; }
					}
					else
					{
						$status_messages[] = sprintf (t("Viga importides objekti city24 id-ga %s: maakler puudub."), $city24_id) . REALESTATE_NEWLINE;

						if (1 != $quiet)
						{
							echo end ($status_messages);
						}

						$property_status = REALESTATE_IMPORT_ERR63;
						continue;
					}

					if ($new_property)
					{
						### create new property object in aw
						$oid = $cl_realestate_mgr->add_property (array ("manager" => $manager->id (), "type" => $this->property_type, "section" => $realestate_agent_data[$agent_oid]["section_oid"]));

						if (is_oid ($oid))
						{
							$property = obj ($oid);

							if (1 != $quiet)
							{
								echo sprintf (t("Loodud objekt aw oid: %s. (City24 id: %s)"), $property->id (), $city24_id) . REALESTATE_NEWLINE;
							}
						}
						else
						{
							$status_messages[] = sprintf (t("Viga importides objekti city24 id-ga %s. Objekti loomine ei tagastanud aw objekti id-d."), $city24_id) . REALESTATE_NEWLINE;

							if (1 != $quiet)
							{
								echo end ($status_messages);
							}

							$property_status = REALESTATE_IMPORT_ERR7;
							continue;
						}
					}

					if ($property->prop ("realestate_agent1") != $agent_oid)
					{
						$property->set_prop ("realestate_agent1", $agent_oid);
						$property->connect (array (
							"to" => $realestate_agent_data[$agent_oid]["object"],
							"reltype" => "RELTYPE_REALESTATE_AGENT",
						));
					}


					### set general property values
					#### city24_object_id
					$property->set_prop ("city24_object_id", $city24_id);

					#### address
					$address = $property->get_first_obj_by_reltype("RELTYPE_REALESTATE_ADDRESS");

					if (!is_object ($address))
					{
						### create address object
						$address = new object();
						$address->set_class_id(CL_ADDRESS);

						### get country
						if (is_oid ($manager->prop ("administrative_structure")))
						{
							### set address' country to default country from manager
							$address->set_parent($manager->prop("administrative_structure"));
							$address->set_prop("administrative_structure", $manager->prop ("administrative_structure"));
							aw_disable_acl();
							$address->save ();
							aw_restore_acl();

							### connect property to address
							$property->connect(array(
								"to" => $address,
								"reltype" => "RELTYPE_REALESTATE_ADDRESS",
							));

							$property->create_brother ($address->id());
						}

						if (!is_object ($address))
						{
							$status_messages[] = sprintf (t("Viga importides objekti city24 id-ga %s. Objekt (oid: %s) loodi ilma aadressita."), $city24_id, $property->id ()) . REALESTATE_NEWLINE;

							if (1 != $quiet)
							{
								echo end ($status_messages);
							}

							$property_status = REALESTATE_IMPORT_ERR8;
							continue;
						}
					}

					$maja_nr = isset($this->property_data["MAJANR"]) ? iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["MAJANR"])) : "";
					$korteri_nr = isset($this->property_data["ASUKOHT_KORTERINR"]) ? iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["ASUKOHT_KORTERINR"])) : "";
					// $kohanimi = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["KOHANIMI"])); // ei kasutata praegu 2007/06/16

					$address_city24[$maakond_division->id()] = isset($this->property_data["MAAKOND"]) ? iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["MAAKOND"])) : "";
					$address_city24[$linn_division->id()] = isset($this->property_data["LINN"]) ? iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["LINN"])) : "";
					$address_city24[$linnaosa_division->id()] = isset($this->property_data["LINNAOSA"]) ? iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["LINNAOSA"])) : "";


					$address_city24[$vald_division->id()] = "";
					if (isset($this->property_data["VALD"]))
					{
						$vald = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["VALD"]));
						// ignore VALD if lowercase value is 'city'
						if ("city" != strtolower($vald))
						{
							$address_city24[$vald_division->id()] = $vald;
						}
					}

					$address_city24[$asula_division->id()] = isset($this->property_data["ASULA"]) ? iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["ASULA"])) : "";
					$address_city24["street"] = isset($this->property_data["TANAV"]) ? iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["TANAV"])) : "";

					$address_text = $address->prop ("address_array");
					unset($address_text[ADDRESS_COUNTRY_TYPE]);

					if (!empty($arr["import_all"]) or (($address_text != $address_city24 or $maja_nr !== $address->prop("house") or $korteri_nr !== $address->prop("apartment")) and $current_user === $maakler_user))
					{
						##### set address
						$address->set_prop ("unit_name", array (
							"division" => $maakond_division,
							"name" => $address_city24[$maakond_division->id()],
						));

						$address->set_prop ("unit_name", array (
							"division" => $vald_division,
							"name" => $address_city24[$vald_division->id()],
							));

						$address->set_prop ("unit_name", array (
							"division" => $linn_division,
							"name" => $address_city24[$linn_division->id()],
						));

						$address->set_prop ("unit_name", array (
							"division" => $linnaosa_division,
							"name" => $address_city24[$linnaosa_division->id()],
						));

						$address->set_prop ("unit_name", array (
							"division" => $asula_division,
							"name" => $address_city24[$asula_division->id()],
						));

						$address->set_prop ("unit_name", array (
							"division" => "street",
							"name" => $address_city24["street"],
						));

						$address->set_prop ("house", $maja_nr);
						$address->set_prop ("apartment", $korteri_nr);
						aw_disable_acl();
						$address->save ();
						aw_restore_acl();

						$address_text = $address->prop ("address_array");
						unset($address_text[ADDRESS_COUNTRY_TYPE]);
						$address_text = implode (", ", $address_text);
						$name = $address_text . " " . $address->prop ("house") . ($address->prop ("apartment") ? "-" . $address->prop ("apartment") : "");
						$property->set_name ($name);//!!! nime panemine yhte funktsiooni!
						$admin_structure_changed = true;
					}

					$address = null;

					#### transaction_type
					if ($this->changed_transaction_types)
					{
						#### transaction types
						$prop_args = array (
							"clid" => CL_REALESTATE_PROPERTY,
							"name" => "transaction_type",
						);
						list ($options, $NULL, $NULL) = $cl_classificator->get_choices ($prop_args);
						$transaction_types = $options->names();
						$this->changed_transaction_types = false;
					}

					$value = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["TEHING"]));
					$variable_oid = (int) reset (array_keys ($transaction_types, $value));

					if (!is_oid ($variable_oid) and !empty ($value))
					{
						$variable_oid = $this->add_variable (CL_REALESTATE_PROPERTY, "transaction_type", $value);
						$this->changed_transaction_types = true;
					}

					$property->set_prop ("transaction_type", $variable_oid);

					#### transaction_price
					$value = isset($this->property_data["HIND"]) ? round ($this->property_data["HIND"], 2) : 0;
					$property->set_prop ("transaction_price", $value);

					#### transaction_price2
					$value = round ($this->property_data["TEHING_MYYGIHIND"], 2);
					$property->set_prop ("transaction_price2", $value);

					### price per m2
					if($property->is_property("total_floor_area"))
					{
						$value = isset($this->property_data["PRICE_PER_M2"]) ? round ($this->property_data["PRICE_PER_M2"], 2) : 0;
						$property->set_prop ("price_per_m2", $value);
					}

					### booked
					if(isset($this->property_data["IS_BOOKED"]) and !$property->prop("is_booked"))
					{
						$property->set_prop("is_booked", 1);

						if (isset($this->property_data["BOOKED_UNTIL_DATE"]))
						{
							list ($year, $month, $day) = sscanf(trim ($data["value"]),"%u-%u-%u");

							if (2006 < $year and 1 <= $month and 12 >= $month and 1 <= $day and 31 >= $day)
							{
								$value = mktime(0, 0, 0, $month, $day, $year);
								$property->set_prop("booked_until", $value);
							}
						}
					}
					else
					{
						$property->set_prop("is_booked", 0);
					}

					#### transaction_rent
					$value = isset($this->property_data["TEHING_KUUYYR"]) ? round ($this->property_data["TEHING_KUUYYR"], 2) : 0;
					$property->set_prop ("transaction_rent", $value);

					#### property_area
					if($property->is_property("property_area")) $property->set_prop ("property_area", $this->property_data["KRUNT"]);

					#### transaction_constraints
					if ($this->changed_transaction_constraints)
					{
						#### transaction_constraints
						$prop_args = array (
							"clid" => CL_REALESTATE_PROPERTY,
							"name" => "transaction_constraints",
						);
						list ($options, $NULL, $NULL) = $cl_classificator->get_choices($prop_args);
						$transaction_constraints = $options->names();
						$this->changed_transaction_constraints = false;
					}

					$value = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["TEHING_PIIRANGUD"]));
					$variable_oid = (int) reset (array_keys ($transaction_constraints, $value));

					if (!is_oid ($variable_oid) and !empty ($value))
					{
						$variable_oid = $this->add_variable (CL_REALESTATE_PROPERTY, "transaction_constraints", $value);
						$this->changed_transaction_constraints = true;
					}

					$property->set_prop ("transaction_constraints", $variable_oid);

					#### transaction_down_payment
					$property->set_prop ("transaction_down_payment", $this->property_data["TEHING_ETTEMAKS"]);

					#### seller data
					$client = $property->get_first_obj_by_reltype("RELTYPE_REALESTATE_SELLER");
					$clients = array ();
					$seller_name = trim ($this->property_data["MYYJA_NIMI"]);

					if (!is_object ($client) and !empty ($seller_name))
					{
						$duplicate_client = false;

						$seller_firstname = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, strtok ($seller_name, " "));
						$seller_lastname = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, strtok (" "));

						##### search for existing client by name
						$list = new object_list (array (
							"class_id" => CL_CRM_PERSON,
							"parent" => array ($manager->prop ("clients_folder")),
							"firstname" => array ($seller_firstname),
							"lastname" => array ($seller_lastname),
						));

						if ($list->count ())
						{
							if ($list->count () > 1)
							{
								$property_status = REALESTATE_IMPORT_ERR10;

								foreach ($list->arr() as $o)
								{
									$client_edit_url = html::href(array(
										"url" => $this->mk_my_orb ("change", array (
											"id" => $o->id(),
										), "crm_person"),
										"target" => "_blank",
										"caption" => $o->id (),
									));
									$client_connect_url = html::href(array(
										"url" => $this->mk_my_orb ("set_client", array (
											"property" => $property->id (),
											"client" => $o->id(),
											"client_type" => "seller",
										)),
										"caption" => t("Vali see klient"),
									));
									$clients[] = REALESTATE_NEWLINE . $client_edit_url . " " . $client_connect_url;
								}

								$clients = implode(" ", $clients);
								$status_messages[] = sprintf (t("Importides objekti city24 id-ga %s ilmnes: antud nimega kliente on rohkem kui &uuml;ks. Ei tea millist valida. AW oid: %s. Leitud kliendid: "), $city24_id, $property->id ()) . '<blockquote>' . $clients . '</blockquote>' . REALESTATE_NEWLINE . REALESTATE_NEWLINE;

								if (1 != $quiet)
								{
									echo end($status_messages);
								}
							}
							else
							{
								$client = $list->begin();
								$email = $client->get_first_obj_by_reltype("RELTYPE_EMAIL");
								$phone = $client->get_first_obj_by_reltype("RELTYPE_PHONE");
							}
						}
						else
						{
							##### create seller
							$client = new object ();
							$client->set_class_id (CL_CRM_PERSON);
							$client->set_parent ($manager->prop ("clients_folder"));
							$client->save ();
						}

						if (REALESTATE_IMPORT_ERR10 !== $property_status)
						{
							if (!is_object($email))
							{
								###### create seller email
								$email = new object ();
								$email->set_class_id (CL_ML_MEMBER);
								$email->set_parent ($manager->prop ("clients_folder"));
								$email->save ();
								$client->connect (array (
									"to" => $email,
									"reltype" => "RELTYPE_EMAIL",
								));
							}

							if (!is_object($phone))
							{
								###### create seller phone
								$phone = new object ();
								$phone->set_class_id (CL_CRM_PHONE);
								$phone->set_parent ($manager->prop ("clients_folder"));
								$phone->save ();
								$client->connect (array (
									"to" => $phone,
									"reltype" => "RELTYPE_PHONE",
								));
							}

							##### save seller data
							$client->set_prop ("firstname", $seller_firstname);
							$client->set_prop ("lastname", $seller_lastname);
							$client->set_name ($seller_firstname . " " . $seller_lastname);

							$email->set_prop ("mail", $this->property_data["MYYJA_EMAIL"]);
							$phone->set_name ($this->property_data["MYYJA_TELEFON"]);

							$client->save ();
							$email->save ();
							$phone->save ();

							$property->connect (array (
								"to" => $client,
								"reltype" => "RELTYPE_REALESTATE_SELLER",
							));
						}
					}

					#### priority
					if ($this->changed_priorities)
					{
						#### priorities
						$prop_args = array (
							"clid" => CL_REALESTATE_PROPERTY,
							"name" => "priority",
						);
						list ($options, $NULL, $NULL) = $cl_classificator->get_choices($prop_args);
						$options = $options->arr ();
						$priorities = array ();

						foreach ($options as $variable)
						{
							$priorities[$variable->comment()] = $variable->id();
						}

						$this->changed_priorities = false;
					}

					if (isset($this->property_data["PRIO"]))
					{
						$altvalue = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["PRIO"]));
						$variable_oid = $priorities[$altvalue];

						if (is_oid ($variable_oid))
						{
							$property->set_prop ("priority", $variable_oid);
						}
					}

					#### show_house_number_on_web
					$value = isset($this->property_data["NAITAMAJANR"]) ? (int) ("Y" === $this->property_data["NAITAMAJANR"]) : 0;
					$property->set_prop ("show_house_number_on_web", $value);

					#### additional_info
					$value = isset($this->property_data["LISAINFO_INFO"]) ? iconv(REALESTATE_IMPORT_CHARSET_FROM, (REALESTATE_IMPORT_CHARSET_TO."//TRANSLIT"), $this->property_data["LISAINFO_INFO"]) : "";
					$property->set_prop ("additional_info_et", $value);

					#### picture_icon
					if ($property->prop ("picture_icon_city24") != $this->property_data["IKOONI_URL"])
					{
						if(substr_count($this->property_data["IKOONI_URL"], '-no-picture') > 0 && $this_object->prop("no_default_picture_copy"))
						{
							break;
						}
						# delete old
						$image = $property->get_first_obj_by_reltype("RELTYPE_REALESTATE_PICTUREICON");

						if (is_object($image))
						{
							$file = $image->prop ("file");
							unlink ($file);
							$image->delete ();
						}

						$image_url = $this->property_data["IKOONI_URL"];
						$imagedata = file_get_contents ($image_url);
						$file = $cl_file->_put_fs(array(
							"type" => "image/jpeg",
							"content" => $imagedata,
						));

						$image =& new object ();
						$image->set_class_id (CL_IMAGE);
						$image->set_parent ($property->id ());
						$image->set_status(STAT_ACTIVE);
						$image->set_name ($property->id () . " " . t("v&auml;ike pilt"));
						$image->set_prop ("file", $file);
						$image->save ();
						$property->set_prop ("picture_icon_image", $image->id ());
						$property->set_prop ("picture_icon_city24", $image_url);
						$property->set_prop ("picture_icon", $cl_image->get_url_by_id ($image->id ()));
						$property->connect (array (
							"to" => $image,
							"reltype" => "RELTYPE_REALESTATE_PICTUREICON",
						));
						$imagedata = NULL;
						$image = null;
					}

					#### pictures
					$list = new object_list ($property->connections_from(array(
						"type" => "RELTYPE_REALESTATE_PICTURE",
						"class_id" => CL_IMAGE,
					)));
					$list = $list->arr();

					##### remove removed
					foreach ($list as $image)
					{
						if (!in_array ($image->meta ("picture_city24_id"), $this->property_data["PILT"]))
						{
							$file = $image->prop ("file");
							unlink ($file);
							$image->delete ();
						}
						else
						{
							$existing_pictures[$image->meta ("picture_city24_id")] = $image;
						}
					}

					##### add new pictures & change order
					ksort ($this->property_data["PILT"]);
					foreach ($this->property_data["PILT"] as $key => $picture_url)
					{
						if (!array_key_exists($picture_url, $existing_pictures))
						{ # add new
							$imagedata = file_get_contents($picture_url);

							if (false !== $imagedata)
							{
								if ("\xFF\xD8" === substr($imagedata, 0, 2)) // JPEG signature
								{
									$file = $cl_file->_put_fs(array(
										"type" => "image/jpeg",
										"content" => $imagedata,
									));

									$image =& new object ();
									$image->set_class_id (CL_IMAGE);
									$image->set_parent ($property->id ());
									$image->set_status(STAT_ACTIVE);
									$image->set_ord ($key);
									$image->set_name ($property->id () . "_" . t(" pilt ") . $key);
									$image->set_prop("file", $file);
									$image->set_meta("picture_city24_id", $picture_url);
									$image->save ();
									$property->connect (array (
										"to" => $image,
										"reltype" => "RELTYPE_REALESTATE_PICTURE",
									));
								}
								else
								{
									$status_messages[] = sprintf (t("Viga importides objekti city24 id-ga %s. Pilt (nr. %s, id %s) pole JPEG fail."), $key, $picture_url) . REALESTATE_NEWLINE;
									$property_status = REALESTATE_IMPORT_ERR17;
								}
							}
							else
							{
								$status_messages[] = sprintf (t("Viga importides objekti city24 id-ga %s. Pildi (nr. %s, id %s) lugemine eba6nnestus."), $key, $picture_url) . REALESTATE_NEWLINE;
								$property_status = REALESTATE_IMPORT_ERR17;
							}

							$imagedata = NULL;
							$image = null;
						}
						elseif ($key != $existing_pictures[$picture_url]->ord())
						{ # change order
							$existing_pictures[$picture_url]->set_ord($key);
							$existing_pictures[$picture_url]->save();
						}
					}

					if (REALESTATE_IMPORT_ERR17 === $property_status and 1 != $quiet)
					{
						echo end ($status_messages);
					}

					$existing_pictures = NULL;


					### set type specific property values
					switch ($this->property_type)
					{
						case "house":
						case "rowhouse":
						case "cottage":
						case "housepart":
						case "apartment":
						case "commercial":
						case "garage":
							#### total_floor_area
							$value = isset($this->property_data["KIRJELDUS_YLDPIND"]) ? round ($this->property_data["KIRJELDUS_YLDPIND"], 2) : 0;
							$property->set_prop ("total_floor_area", $value);

							#### has_alarm_installed
							$value = isset($this->property_data["SEISUKORD_SIGNA"]) ? (int) ("Y" === $this->property_data["SEISUKORD_SIGNA"]) : 0;
							$property->set_prop ("has_alarm_installed", $value);

							#### condition
							if ($this->changed_conditions)
							{
								#### conditions
								$prop_args = array (
									"clid" => CL_REALESTATE_HOUSE,
									"name" => "condition",
								);
								list ($options, $NULL, $NULL) = $cl_classificator->get_choices($prop_args);
								$conditions = $options->names();
								$this->changed_conditions = false;
							}

							$value = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["VALMIDUS"]));
							$variable_oid = (int) reset (array_keys ($conditions, $value));

							if (!is_oid ($variable_oid) and !empty ($value))
							{
								$variable_oid = $this->add_variable (CL_REALESTATE_HOUSE, "condition", $value);
								$this->changed_conditions = true;
							}

							$property->set_prop ("condition", $variable_oid);
							break;
					}

					switch ($this->property_type)
					{
						case "house":
						case "rowhouse":
						case "cottage":
						case "housepart":
						case "apartment":
						case "commercial":
							#### number_of_storeys
							$value = (int) $this->property_data["ASUKOHT_KORRUSEID"];
							$property->set_prop ("number_of_storeys", $value);

							#### number_of_rooms
							$value = (int) $this->property_data["KIRJELDUS_TOAD"];
							$property->set_prop ("number_of_rooms", $value);

							#### has_central_heating
							$value = isset($this->property_data["KIRJELDUS_KESKKYTE"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_KESKKYTE"]) : 0;
							$property->set_prop ("has_central_heating", $value);

							#### has_electric_heating
							$value = isset($this->property_data["KIRJELDUS_ELKYTE"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_ELKYTE"]) : 0;
							$property->set_prop ("has_electric_heating", $value);

							#### has_gas_heating
							$value = isset($this->property_data["KIRJELDUS_GAASIKYTE"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_GAASIKYTE"]) : 0;
							$property->set_prop ("has_gas_heating", $value);

							#### has_shower
							$value = isset($this->property_data["KIRJELDUS_DUSH"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_DUSH"]) : 0;
							$property->set_prop ("has_shower", $value);

							#### has_refrigerator
							$value = isset($this->property_data["KIRJELDUS_KYLMKAPP"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_KYLMKAPP"]) : 0;
							$property->set_prop ("has_refrigerator", $value);

							#### has_furniture
							$value = isset($this->property_data["KIRJELDUS_MOOBEL"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_MOOBEL"]) : 0;
							$property->set_prop ("has_furniture", $value);

							#### has_furniture_option
							$value = isset($this->property_data["KIRJELDUS_MOOBELVOIM"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_MOOBELVOIM"]) : 0;
							$property->set_prop ("has_furniture_option", $value);
							break;
					}

					switch ($this->property_type)
					{
						case "house":
						case "rowhouse":
						case "cottage":
						case "housepart":
						case "apartment":
							#### year_built
							$value = (int) $this->property_data["SEISUKORD_EHITUSAASTA"];
							$property->set_prop ("year_built", $value);

							#### legal_status
							if ($this->changed_legal_statuses)
							{
								#### legal_statuses
								$prop_args = array (
									"clid" => CL_REALESTATE_HOUSE,
									"name" => "legal_status",
								);
								list ($options, $NULL, $NULL) = $cl_classificator->get_choices($prop_args);
								$legal_statuses = $options->names();
								$this->changed_legal_statuses = false;
							}

							$value = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["OMANDIVORM"]));
							$variable_oid = (int) reset (array_keys ($legal_statuses, $value));

							if (!is_oid ($variable_oid) and !empty ($value))
							{
								$variable_oid = $this->add_variable (CL_REALESTATE_HOUSE, "legal_status", $value);
								$this->changed_legal_statuses = true;
							}

							$property->set_prop ("legal_status", $variable_oid);

							#### number_of_bedrooms
							$value = (int) $this->property_data["KIRJELDUS_MAGAMISTOAD"];
							$property->set_prop ("number_of_bedrooms", $value);

							#### number_of_bathrooms
							$value = (int) $this->property_data["KIRJELDUS_VANNITOAD"];
							$property->set_prop ("number_of_bathrooms", $value);

							#### has_wardrobe
							$value = isset($this->property_data["KIRJELDUS_GARDEROOB"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_GARDEROOB"]) : 0;
							$property->set_prop ("has_wardrobe", $value);

							#### has_separate_wc
							$value = isset($this->property_data["KIRJELDUS_WC"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_WC"]) : 0;
							$property->set_prop ("has_separate_wc", $value);

							#### has_garage
							$value = isset($this->property_data["KIRJELDUS_GARAAZH"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_GARAAZH"]) : 0;
							$property->set_prop ("has_garage", $value);

							#### has_sauna
							$value = isset($this->property_data["KIRJELDUS_SAUN"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_SAUN"]) : 0;
							$property->set_prop ("has_sauna", $value);

							#### has_balcony
							$value = isset($this->property_data["KIRJELDUS_RODU"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_RODU"]) : 0;
							$property->set_prop ("has_balcony", $value);

							#### has_wood_heating
							$value = isset($this->property_data["KIRJELDUS_AHJUKYTE"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_AHJUKYTE"]) : 0;
							$property->set_prop ("has_wood_heating", $value);

							#### has_cable_tv
							$value = isset($this->property_data["KIRJELDUS_KAABELTV"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_KAABELTV"]) : 0;
							$property->set_prop ("has_cable_tv", $value);

							#### has_phone
							$value = isset($this->property_data["KIRJELDUS_TELEFON"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_TELEFON"]) : 0;
							$property->set_prop ("has_phone", $value);

							#### has_tv
							$value = isset($this->property_data["KIRJELDUS_TV"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_TV"]) : 0;
							$property->set_prop ("has_tv", $value);

							#### has_bath
							$value = isset($this->property_data["KIRJELDUS_VANN"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_VANN"]) : 0;
							$property->set_prop ("has_bath", $value);

							#### has_boiler
							$value = isset($this->property_data["KIRJELDUS_BOILER"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_BOILER"]) : 0;
							$property->set_prop ("has_boiler", $value);

							#### has_washing_machine
							$value = isset($this->property_data["KIRJELDUS_PESUMASIN"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_PESUMASIN"]) : 0;
							$property->set_prop ("has_washing_machine", $value);

							#### has_parquet
							$value = isset($this->property_data["KIRJELDUS_PARKETT"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_PARKETT"]) : 0;
							$property->set_prop ("has_parquet", $value);
							break;
					}

					switch ($this->property_type)
					{
						case "apartment":
						case "commercial":
							#### floor
							$value = (int) $this->property_data["ASUKOHT_KORRUS"];
							$property->set_prop ("floor", $value);

							#### has_lift
							$value = isset($this->property_data["SEISUKORD_LIFT"]) ? (int) ("Y" === $this->property_data["SEISUKORD_LIFT"]) : 0;
							$property->set_prop ("has_lift", $value);

							#### property_area
							$value = round ($this->property_data["KRUNT"]);
							$property->set_prop ("property_area", $value);

							break;
					}

					switch ($this->property_type)
					{
						case "house":
						case "rowhouse":
						case "cottage":
						case "housepart":
							#### property_area
							$value = round ($this->property_data["KRUNT"]);
							$property->set_prop ("property_area", $value);

							#### has_cellar
							$value = isset($this->property_data["KIRJELDUS_KELDER"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_KELDER"]) : 0;
							$property->set_prop ("has_cellar", $value);

							#### has_industrial_voltage
							$value = isset($this->property_data["KIRJELDUS_TOOSTUSVOOL"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_TOOSTUSVOOL"]) : 0;
							$property->set_prop ("has_industrial_voltage", $value);

							#### has_local_sewerage
							$value = isset($this->property_data["KIRJELDUS_LOKKANAL"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_LOKKANAL"]) : 0;
							$property->set_prop ("has_local_sewerage", $value);

							#### has_central_sewerage
							$value = isset($this->property_data["KIRJELDUS_TSENTKANAL"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_TSENTKANAL"]) : 0;
							$property->set_prop ("has_central_sewerage", $value);

							#### roof_type
							if ($this->changed_roof_types)
							{
								#### roof_types
								$prop_args = array (
									"clid" => CL_REALESTATE_HOUSE,
									"name" => "roof_type",
								);
								list ($options, $NULL, $NULL) = $cl_classificator->get_choices($prop_args);
								$roof_types = $options->names();
								$this->changed_roof_types = false;
							}

							$value = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["KATUS"]));
							$variable_oid = (int) reset (array_keys ($roof_types, $value));

							if (!is_oid ($variable_oid) and !empty ($value))
							{
								$variable_oid = $this->add_variable (CL_REALESTATE_HOUSE, "roof_type", $value);
								$this->changed_roof_types = true;
							}

							$property->set_prop ("roof_type", $variable_oid);

							#### has_fireplace
							$value = isset($this->property_data["KIRJELDUS_KAMIN"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_KAMIN"]) : 0;
							$property->set_prop ("has_fireplace_heating", $value);
							break;

						case "apartment":
							#### show_apartment_number
							$value = isset($this->property_data["NAITAKORTERINR"]) ? (int) ("Y" === $this->property_data["NAITAKORTERINR"]) : 0;
							$property->set_prop ("show_apartment_number", $value);

							#### is_middle_floor
							$value = 0;
							$floors = (int) $this->property_data["ASUKOHT_KORRUSEID"];
							$floor = (int) $this->property_data["ASUKOHT_KORRUS"];

							if (
								($floors) and
								($floor) and
								($floors - $floor) and
								($floor != 1) and
								($floors > 2)
							)
							{
								$value = 1;
							}

							$property->set_prop ("is_middle_floor", $value);

							#### has_hallway_locked
							$value = isset($this->property_data["SEISUKORD_TREPIKODA"]) ? (int) ("Y" === $this->property_data["SEISUKORD_TREPIKODA"]) : 0;
							$property->set_prop ("has_hallway_locked", $value);

							#### kitchen_area
							$value = round ($this->property_data["KIRJELDUS_KOOGISUURUS"], 1);
							$property->set_prop ("kitchen_area", $value);

							#### has_cellar
							$value = isset($this->property_data["KIRJELDUS_KELDER"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_KELDER"]) : 0;
							$property->set_prop ("has_cellar", $value);

							#### has_fireplace
							$value = isset($this->property_data["KIRJELDUS_KAMIN"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_KAMIN"]) : 0;
							$property->set_prop ("has_fireplace", $value);

							#### stove_type
							if ($this->changed_stove_types)
							{
								#### stove_types
								$prop_args = array (
									"clid" => CL_REALESTATE_APARTMENT,
									"name" => "stove_type",
								);
								list ($options, $NULL, $NULL) = $cl_classificator->get_choices($prop_args);
								$stove_types = $options->names();
								$this->changed_stove_types = false;
							}

							$value = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["PLIIT"]));
							$variable_oid = (int) reset (array_keys ($stove_types, $value));

							if (!is_oid ($variable_oid) and !empty ($value))
							{
								$variable_oid = $this->add_variable (CL_REALESTATE_APARTMENT, "stove_type", $value);
								$this->changed_stove_types = true;
							}

							$property->set_prop ("stove_type", $value);

							#### has_security_door
							$value = isset($this->property_data["SEISUKORD_TURVAUKS"]) ? (int) ("Y" === $this->property_data["SEISUKORD_TURVAUKS"]) : 0;
							$property->set_prop ("has_security_door", $value);
							break;

						case "commercial":
							#### transaction_monthly_rent
							$value = round ($this->property_data["TEHING_KUURENT"], 2);
							$property->set_prop ("transaction_monthly_rent", $value);

							#### usage_purpose
							if ($this->changed_usage_purposes)
							{
								#### usage purposes
								$prop_args = array (
									"clid" => CL_REALESTATE_COMMERCIAL,
									"name" => "usage_purpose",
								);
								list ($options, $NULL, $NULL) = $cl_classificator->get_choices($prop_args);
								$usage_purposes = $options->names();
								$this->changed_usage_purposes = false;
							}

							$value = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["PINNA_TYYP"]));
							$variable_oid = (int) reset (array_keys ($usage_purposes, $value));

							if (!is_oid ($variable_oid) and !empty ($value))
							{
								$variable_oid = $this->add_variable (CL_REALESTATE_COMMERCIAL, "usage_purpose", $value);
								$this->changed_usage_purposes = true;
							}

							$property->set_prop ("usage_purpose", $variable_oid);

							#### has_kitchen
							$value = isset($this->property_data["KIRJELDUS_KOOK"]) ? (int) ("Y" === $this->property_data["KIRJELDUS_KOOK"]) : 0;
							$property->set_prop ("has_kitchen", $value);

							#### has_internet
							$value = isset($this->property_data["KOMMU_INTERNET"]) ? (int) ("Y" === $this->property_data["KOMMU_INTERNET"]) : 0;
							$property->set_prop ("has_internet", $value);

							#### has_isdn
							$value = isset($this->property_data["KOMMU_ISDN"]) ? (int) ("Y" === $this->property_data["KOMMU_ISDN"]) : 0;
							$property->set_prop ("has_isdn", $value);

							#### number_of_phone_lines
							$value = (int) $this->property_data["KIRJELDUS_TELEFONE"];
							$property->set_prop ("number_of_phone_lines", $value);
							break;

						case "land":
							#### distance_from_tallinn
							$value = (int) $this->property_data["MUU_KAUGUSTLN"];
							$property->set_prop ("distance_from_tallinn", $value);

							#### land_use
							if ($this->changed_land_uses)
							{
								#### land_uses
								$prop_args = array (
									"clid" => CL_REALESTATE_LAND,
									"name" => "land_use",
								);
								list ($options, $NULL, $NULL) = $cl_classificator->get_choices($prop_args);
								$land_uses = $options->names();
								$this->changed_land_uses = false;
							}

							$value = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["OTSTARVE_VEEL"]));
							$variable_oid = (int) reset (array_keys ($land_uses, $value));

							if (!is_oid ($variable_oid) and !empty ($value))
							{
								$variable_oid = $this->add_variable (CL_REALESTATE_LAND, "land_use", $value);
								$this->changed_land_uses = true;
							}

							$property->set_prop ("land_use", $variable_oid);

							#### land_use_2
							// $value = iconv(REALESTATE_IMPORT_CHARSET_FROM, REALESTATE_IMPORT_CHARSET_TO, trim ($this->property_data["OTSTARVE_VEEL"]));//!!! city-s pole kaht maa otstarvet?
							// $variable_oid = (int) reset (array_keys ($land_uses, $value));

							// if (!is_oid ($variable_oid) and !empty ($value))
							// {
								// $variable_oid = $this->add_variable (CL_REALESTATE_LAND, "land_use", $value);
							// }

							// $property->set_prop ("land_use_2", $variable_oid);

							#### is_changeable
							$value = isset($this->property_data["MUU_OTSTARBEMUUT"]) ? (int) ("Y" === $this->property_data["MUU_OTSTARBEMUUT"]) : 0;
							$property->set_prop ("is_changeable", $value);

							#### has_electricity
							$value = isset($this->property_data["KOMMU_ELEKTER"]) ? (int) ("Y" === $this->property_data["KOMMU_ELEKTER"]) : 0;
							$property->set_prop ("has_electricity", $value);

							#### has_sewerage
							$value = isset($this->property_data["KOMMU_KANALISATSIOON"]) ? (int) ("Y" === $this->property_data["KOMMU_KANALISATSIOON"]) : 0;
							$property->set_prop ("has_sewerage", $value);

							#### has_water
							$value = isset($this->property_data["KOMMU_VESI"]) ? (int) ("Y" === $this->property_data["KOMMU_VESI"]) : 0;
							$property->set_prop ("has_water", $value);

							#### has_zoning_ordinance
							$value = isset($this->property_data["MUU_DETAILPLAN"]) ? (int) ("Y" === $this->property_data["MUU_DETAILPLAN"]) : 0;
							$property->set_prop ("has_zoning_ordinance", $value);
							break;
					}

					if (REALESTATE_IMPORT_OK !== $property_status)
					{
						$property->set_meta("city24_last_import", 0);
					}
					else
					{
						$property->set_meta("city24_last_import", $import_time);
					}
				}
			}
		}

		// save admin structure if addresses changed/added. To update admin structure index
		if ($admin_structure_changed and is_oid($manager->prop ("administrative_structure")))
		{
			$as_o = new object($manager->prop ("administrative_structure"));
			$as_o->save();
		}

		$additional_languages = array (
			"ENG" => "en",
			"RUS" => "ru",
			"FIN" => "fi",
		);

		foreach ($additional_languages as $lang_name => $lang_code)
		{
			$tmp_import_url = str_replace ("lang", "tmpvariable39903", $import_url);
			$import_url = str_replace ("tmpvariable39903", "lang", aw_url_change_var ("tmpvariable39903", $lang_name, $tmp_import_url));
			$xml = file_get_contents ($import_url);
			xml_parse_into_struct ($parser, $xml, $xml_data, $xml_index);
			$this->end_property_import = false;
			$status_messages = array ();

			foreach ($xml_data as $key => $data)
			{
				if ($this->end_property_import)
				{ ### finish last processed property import
					if (is_object ($property))
					{
						$property->save ();

						if (1 != $quiet) echo sprintf (t("Lisainfo (%s) objektile city24 id-ga %s imporditud. AW id: %s. Impordi staatus: %s"), $lang_name, $this->property_data["ID"], $property->id (), $property_status) . REALESTATE_NEWLINE;
					}
					else
					{
						if (1 != $quiet)
						{
							echo sprintf (t("Viga objekti city24 id-ga %s lisainfo (%s) impordil. Veastaatus: %s"), $this->property_data["ID"], $lang_name, $property_status) . REALESTATE_NEWLINE;
						}
					}

					if ($property_status)
					{
						$status = REALESTATE_IMPORT_ERR9;
						$import_log[] = $status_messages;
					}

					$status_messages = array ();
					$this->end_property_import = false;
					flush ();
				}
				elseif (("ROW" === $data["tag"]) and ("open" === $data["type"]))
				{
					### start property additional info import
					$this->property_data = array ();
				}
				elseif (is_array ($this->property_data))
				{ ### get&process property data
					if (
						"ID" === $data["tag"] or
						"LISAINFO_INFO" === $data["tag"]
					)
					{
						$this->property_data[$data["tag"]] = $data["value"];
					}
					elseif (("ROW" === $data["tag"]) and ("close" === $data["type"]))
					{ ### import property additional info to aw
						$property_status = REALESTATE_IMPORT_OK;
						$this->end_property_import = true;
						$city24_id = (int) $this->property_data["ID"];

						### load existing object corresponding to city24 id
						if (array_key_exists($city24_id, $imported_object_ids))
						{
							$property = new object($imported_object_ids[$city24_id]);
						}

						if (!is_object ($property))
						{
							$status_messages[] = sprintf (t("Viga importides lisainfot (%s) objekti city24 id-ga %s: vastavat aw objekti ei leitud."), $lang_name, $city24_id) . REALESTATE_NEWLINE;

							if (1 != $quiet)
							{
								echo end ($status_messages);
							}

							$property_status = REALESTATE_IMPORT_ERR15;
							continue;
						}


						### agent ...
						$agent_oid = $property->prop ("realestate_agent1");

						if (!is_oid ($agent_oid))
						{
							$status_messages[] = sprintf (t("Viga importides lisainfot (%s) objekti city24 id-ga %s. Objektil puudub maakler."), $lang_name, $city24_id) . REALESTATE_NEWLINE;

							if (1 != $quiet)
							{
								echo end ($status_messages);
							}

							$property_status = REALESTATE_IMPORT_ERR5;
							continue;
						}

						### load agent data
						if (!isset ($realestate_agent_data[$agent_oid]))
						{
							$agent = obj ($agent_oid);

							### get agent uid
							$connection = new connection();
							$connections = $connection->find(array(
								"to" => $agent->id(),
								"from.class_id" => CL_USER,
								"type" => "RELTYPE_PERSON",
							));

							if (count ($connections))
							{
								$connection = reset ($connections);

								if (is_oid ($connection["from"]))
								{
									$from_obj = obj($connection["from"]);
									$agent_uid = $from_obj->prop("uid");
								}
								else
								{
									$status_messages[] = sprintf (t("Viga importides lisainfot (%s) objekti city24 id-ga %s: maakleri kasutajaandmetes on viga. Osa infot j&auml;&auml;b salvestamata."), $lang_name, $city24_id) . REALESTATE_NEWLINE;

									if (1 != $quiet)
									{
										echo end ($status_messages);
									}

									$property_status = REALESTATE_IMPORT_ERR61;
									$agent_uid = false;
								}
							}
							else
							{
								$status_messages[] = sprintf (t("Viga importides lisainfot (%s) objekti city24 id-ga %s: maakleri kasutajaandmeid ei leitud. Osa infot j&auml;&auml;b salvestamata."), $lang_name, $city24_id) . REALESTATE_NEWLINE;

								if (1 != $quiet)
								{
									echo end ($status_messages);
								}

								$property_status = REALESTATE_IMPORT_ERR62;
								$agent_uid = false;
							}

							$realestate_agent_data[$agent_oid]["agent_uid"] = $agent_uid;
						}

						### switch to property owner user
						if ($realestate_agent_data[$agent_oid]["agent_uid"])
						{
							aw_switch_user (array ("uid" => $realestate_agent_data[$agent_oid]["agent_uid"]));
	// /* dbg */ if (1 == $_GET["re_import_dbg"]){ echo "kasutaja vahetatud maakleri kasutajaks: [{$realestate_agent_data[$agent_oid]["agent_uid"]}]"; }
						}
						else
						{
							$status_messages[] = sprintf (t("Viga importides lisainfot (%s) objekti city24 id-ga %s: maakler puudub."), $lang_name, $city24_id) . REALESTATE_NEWLINE;

							if (1 != $quiet)
							{
								echo end ($status_messages);
							}

							$property_status = REALESTATE_IMPORT_ERR63;
							continue;
						}


						### set property values
						#### additional_info
						$list = new object_list(array(
							"class_id" => CL_LANGUAGE,
							"lang_acceptlang" => $lang_code,
							"site_id" => array(),
							"lang_id" => array(),
						));
						$language = $list->begin ();

						if (is_object ($language))
						{
							$charset = $language->prop("lang_charset");
							$value = iconv(REALESTATE_IMPORT_CHARSET_FROM, $charset, $this->property_data["LISAINFO_INFO"]);
							$property->set_prop ("additional_info_{$lang_code}", $value);
						}
						else
						{
							$status_messages[] = sprintf (t("Viga importides lisainfot (%s) objekti city24 id-ga %s ilmnes: keeleobjekti ei leitud."), $lang_name, $city24_id) . REALESTATE_NEWLINE;

							if (1 != $quiet)
							{
								echo end ($status_messages);
							}

							$property_status = REALESTATE_IMPORT_ERR14;
						}

						if (REALESTATE_IMPORT_OK !== $property_status)
						{
							$property->set_meta("city24_last_import", 0);
						}
					}
				}
			}
		}

		### set is_visible to false for objects not found in city24 xml
		if (count($imported_properties))
		{
			$company_id = $this_object->prop("company");
			$all_persons = array();

			if(is_oid($company_id))
			{
				$company = obj($company_id);
				$i = get_instance(CL_CRM_COMPANY);
				$i->get_all_workers_for_company($company, $all_persons);
			}

			// $all_persons = array_keys($all_persons);

			$realestate_objects = new object_list (array (
				"oid" => new obj_predicate_not ($imported_properties),
				"class_id" => $realestate_classes,
				"parent" => $realestate_folders,
				// "modified" => new obj_predicate_compare (OBJ_COMP_GREATER_OR_EQ, $last_import),
				"is_archived" => 0,
				"is_visible" => 1,
				"site_id" => array (),
				"lang_id" => array (),
			));

			foreach($realestate_objects->arr() as $realestate_object)// et siis muudaks n&auml;htamatuks vaid need objektid, mille maaklerid t&ouml;&ouml;tavad selles ettev&otilde;ttes, mis on impordiobjekti juurde seostatud
			{
				if(!is_oid($realestate_object->prop("realestate_agent1")) and !is_oid($realestate_object->prop("realestate_agent2"))) $realestate_object->set_prop ("is_visible", 0);
				if(array_key_exists($realestate_object->prop("realestate_agent1") , $all_persons)) $realestate_object->set_prop ("is_visible", 0);
				if(array_key_exists($realestate_object->prop("realestate_agent2") , $all_persons)) $realestate_object->set_prop ("is_visible", 0);
			}

		//	$realestate_objects->set_prop ("is_visible", 0);
			aw_disable_acl();
			$realestate_objects->save ();
			aw_restore_acl();
		}

		### save log
		$logs = (array) $this_object->meta ("city24_log");
		$logs[$import_time] = $import_log;
		krsort ($logs);

		if (count ($logs) > 10)
		{
			array_pop ($logs);
		}

		$this_object->set_meta ("city24_log", $logs);

		### fin.
		aw_disable_acl();
		$this_object->save ();
		aw_restore_acl();
		xml_parser_free ($parser);
		$cl_cache = get_instance ("cache");
		$cl_cache->full_flush ();

		ini_set ("ignore_user_abort", $ignore_user_abort_prev_val);
		aw_set_exec_time(AW_LONG_PROCESS);
		ini_set ("memory_limit", $max_mem_prev_val);

		if (1 != $quiet)
		{
			echo sprintf(t("Import tehtud. Staatus: %s"), $status);
			exit;
		}

		return $status;
	}

	function add_variable ($clid, $name, $value)
	{
		if (!is_object ($this->cl_object_type))
		{
			$this->cl_object_type = get_instance(CL_OBJECT_TYPE);
		}

		$ff = $this->cl_object_type->get_obj_for_class(array(
			"clid" => $clid,
		));
		$oft = obj ($ff);
		$clf = $oft->meta("classificator");
		$clf_type = $oft->meta("clf_type");
		$use_type = $clf_type[$name];
		$ofto = obj ($clf[$name]);
		$parent = $ofto->id ();

		if (is_oid ($parent))
		{
			$no = new object;
			$no->set_class_id(CL_META);
			$no->set_status(STAT_ACTIVE);
			$no->set_parent($parent);
			$no->set_name($value);
			$no->save();

			return $no->id ();
		}
		else
		{
			echo sprintf (t("Viga: muutuja %s klassil id-ga %s defineerimata."), $name, $clid) . REALESTATE_NEWLINE;
			return false;
		}
	}

/**
	@attrib name=set_client
	@param property required
	@param client required
	@param client_type required
**/
	function set_client ($arr)
	{
		if (is_oid ($arr["property"]))
		{
			$property = obj ($arr["property"]);
		}
		elseif (is_object ($arr["property"]))
		{
			$property = $arr["property"];
		}
		else
		{
			return false;
		}

		if (is_oid ($arr["client"]))
		{
			$client = obj ($arr["client"]);
		}
		elseif (is_object ($arr["client"]))
		{
			$client = $arr["client"];
		}
		else
		{
			return false;
		}

		switch ($arr["client_type"])
		{
			case "seller":
				$reltype = "RELTYPE_REALESTATE_SELLER";
				break;

			case "buyer":
				$reltype = "RELTYPE_REALESTATE_BUYER";
				break;

			default:
				return false;
		}

		$property->connect (array (
			"to" => $client,
			"reltype" => $reltype,
		));
		$property->set_prop ($arr["client_type"], $client->id ());
		$property->save ();
		return true;
	}

/**
	@attrib name=city24xml nologin=1
	@param id required type=int
**/
	function city24_xml ($arr)
	{
		header ("Content-Type: application/xml");

		$out_charset = "ISO-8859-4";
		$this_object = obj ($arr["id"]);
		$import_url = $this_object->prop ("city24_import_url");
		$xml = file_get_contents ($import_url);
		// $xml = iconv ("UTF-8", $out_charset, $xml);
		// $xml = preg_replace ('/encoding\=\"UTF\-8\"/Ui', 'encoding="' . $out_charset . '"', $xml, 1);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $import_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 600);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
		// $xml = curl_exec($ch);
		curl_exec($ch);
		curl_close($ch);

		// echo $xml;
		exit;
	}
}
?>
