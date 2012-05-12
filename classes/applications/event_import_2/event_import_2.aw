<?php
// event_import_2.aw - S&uuml;ndmuste import 2
/*

@classinfo syslog_type=ST_EVENT_IMPORT_2 relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general

@default field=meta
@default method=serialize

	@groupinfo general2 caption="&Uuml;ldised seaded" parent=general
	@default group=general2

		@property slave_obj type=relpicker reltype=RELTYPE_SLAVE automatic=1
		@caption Allikas

		@property events_manager type=relpicker reltype=RELTYPE_EVENTS_MANAGER
		@caption S&uuml;ndmuste halduse keskkond

		@property language type=relpicker reltype=RELTYPE_EVENT_LANGUAGE
		@caption Keel
		@comment Objektide keel

		@property translations type=checkbox ch_value=1
		@caption T&otilde;lked
		@comment Andmed salvestatakse objekti t&otilde;lgetena, mitte omaduste v&auml;&auml;rtustena

		@property last_import type=text
		@caption Viimane import
		@comment Viimase impordi l&otilde;pu kellaaeg

		@property event_times_as_events type=checkbox ch_value=1
		@caption Toimumisajad s&uuml;ndmustena

		@property from_start type=checkbox ch_value=1
		@caption Impordi k&otilde;ik
		@comment Kui see chekkimata j&auml;tta, siis imporditakse objektid, mida on allikas muudetud p&auml;rast viimast importi

		@property invoke type=text store=no
		@caption Import

	@groupinfo extsys caption="Siduss&uuml;steemid" parent=general
	@default group=extsys

		@property extsys_event type=relpicker reltype=RELTYPE_EXT_SYS_EVENT store=connect
		@caption S&uuml;ndmused

		@property extsys_event_time type=relpicker reltype=RELTYPE_EXT_SYS_EVENT_TIME store=connect
		@caption Toimumisajad

		@property extsys_location type=relpicker reltype=RELTYPE_EXT_SYS_LOCATION store=connect
		@caption Toimumiskohad

		@property extsys_organizer type=relpicker reltype=RELTYPE_EXT_SYS_ORGANIZER store=connect
		@caption Korraldajad

		@property extsys_sector type=relpicker reltype=RELTYPE_EXT_SYS_SECTOR store=connect
		@caption Valdkonnad

	@groupinfo cfgforms caption="Seadete vormid" parent=general
	@default group=cfgforms

		@property cfgform_event type=relpicker reltype=RELTYPE_CFGFORM_EVENT store=connect
		@caption S&uuml;ndmus

		@property cfgform_event_time type=relpicker reltype=RELTYPE_CFGFORM_EVENT_TIME store=connect
		@caption Toimumisaeg

		@property cfgform_location type=relpicker reltype=RELTYPE_CFGFORM_LOCATION store=connect
		@caption Toimumiskoht

		@property cfgform_organizer type=relpicker reltype=RELTYPE_CFGFORM_ORGANIZER store=connect
		@caption Korraldaja

		@property cfgform_sector type=relpicker reltype=RELTYPE_CFGFORM_SECTOR store=connect
		@caption Valdkond

@groupinfo update_rules caption="Muudatuste reeglid"
@default group=update_rules

	@property urt_event type=table no_caption=1 store=no

	@property urt_event_time type=table no_caption=1 store=no

	@property urt_location type=table no_caption=1 store=no

	@property urt_organizer type=table no_caption=1 store=no

	@property urt_sector type=table no_caption=1 store=no

@reltype SLAVE value=1 clid=CL_JSON_DELFI
@caption Allikas

@reltype EVENTS_MANAGER value=2 clid=CL_EVENTS_MANAGER
@caption S&uuml;ndmuste halduse keskkond

@reltype EVENT_LANGUAGE value=4 clid=CL_LANGUAGE
@caption Keel

@reltype EXT_SYS_EVENT value=5 clid=CL_EXTERNAL_SYSTEM
@caption S&uuml;ndmuste siduss&uuml;steem

@reltype EXT_SYS_EVENT_TIME value=6 clid=CL_EXTERNAL_SYSTEM
@caption Toimumisaegade siduss&uuml;steem

@reltype EXT_SYS_LOCATION value=7 clid=CL_EXTERNAL_SYSTEM
@caption Toimumiskohtade siduss&uuml;steem

@reltype EXT_SYS_ORGANIZER value=8 clid=CL_EXTERNAL_SYSTEM
@caption Korraldajate siduss&uuml;steem

@reltype EXT_SYS_SECTOR value=9 clid=CL_EXTERNAL_SYSTEM
@caption Valdkondade siduss&uuml;steem

@reltype CFGFORM_EVENT value=3 clid=CL_CFGFORM
@caption S&uuml;ndmuse seadete vorm

@reltype CFGFORM_EVENT_TIME value=10 clid=CL_CFGFORM
@caption Toimumisaja seadete vorm

@reltype CFGFORM_LOCATION value=11 clid=CL_CFGFORM
@caption Toimumiskoha seadete vorm

@reltype CFGFORM_ORGANIZER value=12 clid=CL_CFGFORM
@caption Korraldaja seadete vorm

@reltype CFGFORM_SECTOR value=13 clid=CL_CFGFORM
@caption Valdkonna seadete vorm

*/

class event_import_2 extends class_base
{
	function event_import_2()
	{
		$this->init(array(
			"tpldir" => "applications/events_import/event_import_2",
			"clid" => CL_EVENT_IMPORT_2
		));
		$this->happy_input = array(
			"location" => array(
				array(
					"name",
					"url" => array(
						array(
							"name",
							"url",
							"jrk",
							"ext_id",
						),
					),
					"address" => array(
						array(
							"name",
							"comment",
							"address",
							"ext_id",
							"jrk",
						),
					),
					"email" => array(
						array(
							"mail",
							"jrk",
							"ext_id",
						),
					),
					"phone",
					"photo" => array(
						array(
							"ext_id",
							"small",
							"big",
							"name",
							"jrk",
						),
					),
					"comment",
					"organizer" => array(),
					"ext_id",
				),
			),
			"event" => array(
				array(
					"sector" => array(),
					"url" => array(
						array(
							"name",
							"url",
							"jrk",
							"ext_id",
						),
					),
					"name",
					"description",
					"photo" => array(
						array(
							"ext_id",
							"small",
							"big",
							"name",
							"jrk",
						),
					),
					"event_time" => array(
						array(
							"location",
							"start",
							"end",
							"jrk",
							"ext_id",
						),
					),
					"ext_id",
					"organizer" => array(),
					"email" => array(
						array(
							"mail",
							"jrk",
							"ext_id",
						),
					),
					"phone" => array(
						array(
							"name",
							"type",
							"ext_id",
							"jrk",
						),
					),
					"address" => array(
						array(
							"name",
							"comment",
							"address",
							"ext_id",
							"jrk",
						),
					),
				),
			),
			"sector" => array(
				array(
					"tegevusala",
					"ext_id",
					"jrk",
				),
			),
			"organizer" => array(
				array(
					"name",
					"phone" => array(
						array(
							"name",
							"type",
							"ext_id",
							"jrk",
						),
					),
					"address" => array(
						array(
							"name",
							"comment",
							"address",
							"ext_id",
							"jrk",
						),
					),
					"email" => array(
						array(
							"mail",
							"jrk",
							"ext_id",
						),
					),
					"url" => array(
						array(
							"name",
							"url",
							"jrk",
							"ext_id",
						),
					),
					"comment",
					"ext_id",
				),
			),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "last_import":
				$prop["value"] = date("Y-m-d H:i:s", $prop["value"]);
				break;

			case "invoke":
				$prop["value"] = html::href(array(
					"caption" => t("K&auml;ivita import"),
					"url" => $this->mk_my_orb("invoke", array("id" => $arr["obj_inst"]->id(), "verbose" => 1, "return_url" => get_ru())),
				));
				break;
		}

		return $retval;
	}

	private function init_urt($arr, $fs)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "field",
			"caption" => t("Property"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "update",
			"caption" => t("Uuenda property sisu, ka siis kui nii AWs kui allikas on sisu muudetud"),
			"align" => "center",
		));
		$ch = $arr["obj_inst"]->meta($arr["prop"]["name"]);
		foreach($fs as $f => $fdata)
		{
			$t->define_data(array(
				"field" => strlen($fdata["caption"]) > 0 ? $fdata["caption"] : $f,
				"update" => html::checkbox(array(
					"name" => $arr["prop"]["name"]."[".$f."]",
					"value" => 1,
					"checked" => checked((int)$ch[$f] === 1 || !is_array($ch)),
				)),
				"ord" => isset($fdata["ord"]) ? $fdata["ord"] : 0,
			));
		}
		$t->set_default_sortby("ord");
		return $t;
	}

	function _get_urt_event($arr)
	{
		$props = $this->can("view", $arr["obj_inst"]->cfgform_event) ? get_instance(CL_CFGFORM)->get_cfg_proplist($arr["obj_inst"]->cfgform_event) : get_instance(CL_CALENDAR_EVENT)->get_all_properties();

		$t = $this->init_urt(&$arr, $props);
		$t->set_caption("S&uuml;ndmus");
	}

	function _get_urt_event_time($arr)
	{
		$props = $this->can("view", $arr["obj_inst"]->cfgform_event_time) ? get_instance(CL_CFGFORM)->get_cfg_proplist($arr["obj_inst"]->cfgform_event_time) : get_instance(CL_EVENT_TIME)->get_all_properties();

		$t = $this->init_urt(&$arr, $props);
		$t->set_caption("Toimumisaeg");
	}

	function _get_urt_location($arr)
	{
		$props = $this->can("view", $arr["obj_inst"]->cfgform_location) ? get_instance(CL_CFGFORM)->get_cfg_proplist($arr["obj_inst"]->cfgform_location) : get_instance(CL_SCM_LOCATION)->get_all_properties();

		$t = $this->init_urt(&$arr, $props);
		$t->set_caption("Toimumiskoht");
	}

	function _get_urt_organizer($arr)
	{
		$props = $this->can("view", $arr["obj_inst"]->cfgform_organizer) ? get_instance(CL_CFGFORM)->get_cfg_proplist($arr["obj_inst"]->cfgform_organizer) : get_instance(CL_CRM_COMPANY)->get_all_properties();

		$t = $this->init_urt(&$arr, $props);
		$t->set_caption("Korraldaja");
	}

	function _get_urt_sector($arr)
	{
		$props = $this->can("view", $arr["obj_inst"]->cfgform_sector) ? get_instance(CL_CFGFORM)->get_cfg_proplist($arr["obj_inst"]->cfgform_sector) : get_instance(CL_CRM_SECTOR)->get_all_properties();

		$t = $this->init_urt(&$arr, $props);
		$t->set_caption("Valdkond");
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		switch($prop["name"])
		{
			case "urt_event":
			case "urt_event_time":
			case "urt_location":
			case "urt_organizer":
			case "urt_sector":
				$arr["obj_inst"]->set_meta($prop["name"], $arr["request"][$prop["name"]]);
				break;

			case "last_import":
				return PROP_IGNORE;
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $ob->prop("name"),
		));
		return $this->parse();
	}

	/**
		@attrib name=invoke api=1

		@param id required type=oid

		@param return_url optional type=string

		@param verbose optional type=bool
	**/
	function invoke($arr)
	{
		// Turn cache off
		aw_global_set("no_cache_flush", 1);
		obj_set_opt("no_cache", 1);

		$o = obj($arr["id"]);

		$inst = array(CL_IMAGE);
		foreach($inst as $i)
		{
			$this->inst[$i] = get_instance($i);
		}

		// Get configuration
		$this->conf = $this->get_conf(&$o);
		$this->conf["verbose"] = $arr["verbose"] ? 1 : 0;

		// Get imported objects
//		$this->impd_objs = $this->get_impd_objs($conf);

		// Get data
		$slave_inst = obj($o->slave_obj)->instance();
		$mmih_args = array(
			"id" => $o->slave_obj,
			"charset" => $o->prop("language.lang_charset"),
		);
		if($o->last_import && !$o->from_start)
		{
			$mmih_args["from"] = $o->last_import;
		}
		$this->data = $this->get_data($slave_inst, $mmih_args);

		if($this->check_progress())
		{
			$this->init_reload(60);
		}

		$this->import("location");
		$this->import("sector");
		$this->import("organizer");
		$this->import("event");

		$o->last_import = time();
		$this->save_obj($o);

		$this->print_log("unlinking input file");
		unlink($this->conf["input_file"]);
		$this->print_log("unlinking progress file");
		unlink($this->conf["progress_file"]);

		return $arr["return_url"];
	}

	private function import($itype)
	{
		$this->print_log("<h2>IMPORTING ".strtoupper($itype)."S</h2>", 0);
		$i = 0;
		foreach($this->data[$itype] as $data)
		{
			$id = $data["ext_id"];

			if($this->check_ext_id_done($itype, $id))
			{
				$this->print_log("Skipped ".$itype.", ext_id ".$id);
				continue;
			}

//			$new = !array_key_exists($id, $this->impd_objs[$itype]) || !$this->can("view", $this->impd_objs[$itype][$id]);
			$oid = $this->check_impd_obj($id, $itype);
			$new = !is_oid($oid);
			if(!$new)
			{
				$this->print_log("Existing ".$itype);

				$o = obj($oid);
			}
			else
			{
				$this->print_log("<b>New ".$itype."</b>");

				$o = obj();
				$o->set_class_id($this->conf["clid"][$itype]);
				$o->set_parent($this->conf["dir"][$itype]);
			}

			$fn_to_call = "process_".$itype."s";
			$this->$fn_to_call($o, $data, $new);

			$o->set_meta("orig_content_".$this->conf["lang_id"], $oc);
			$this->save_obj($o);

			$this->print_log("ext_id - ".$id, 2);
			$this->print_log("OID - ".$o->id(), 2);

			// We add the new object to the list of imported objects.
			if($new)
			{
				$this->add_to_impd_objs($o, $id, $itype);
			}
			$this->add_to_progress_and_check_time_limit($itype, $id);
		}
		$this->print_log("<h3>DONE IMPORTING ".strtoupper($itype)."S</h3>", 0);
	}

	private function process_sectors(&$o, $data, $new)
	{
		$oc = $new ? array() : $o->meta("orig_content_".$this->conf["lang_id"]);
		if($this->conf["translations"])
		{
			$translations = $o->meta("translations");
			$trans = &$translations[$this->conf["lang_id"]];
		}
		foreach($data as $k => $v)
		{
			// We'll update the content if it differs from original and it's not prohibited to do that.
			$update = $this->permission_to_update(&$o, &$k, &$oc, "sector");
			if($new || $update)
			{
				switch($k)
				{
					case "jrk":
						$this->print_log($k." => ".$v, 4);
						$o->set_ord($v);
						break;

					case "tegevusala":
						$this->print_log($k." => ".$v, 4);
						if($this->conf["translations"])
						{
							$trans[$k] = $v;
						}
						else
						{
							$o->set_prop($k, $v);
						}
						break;
				}
			}
			// We need to save the original content. Otherwise we can't tell if the content has been changed in AW or the source.
			$oc[$k] = $v;
		}
		if($this->conf["translations"])
		{
			$o->set_meta("translations", $translations);
		}
	}

	private function process_locations(&$o, $data, $new)
	{
		$oc = $new ? array() : $o->meta("orig_content_".$this->conf["lang_id"]);
		if($this->conf["translations"])
		{
			$translations = $o->meta("translations");
			$trans = &$translations[$this->conf["lang_id"]];
		}
		foreach($data as $k => $v)
		{
			// We'll update the content if it differs from original and it's not prohibited to do that.
			$update = $this->permission_to_update(&$o, &$k, &$oc, "location");
			if($new || $update)
			{
				switch($k)
				{
					case "jrk":
						$this->print_log($k." => ".$v, 4);
						$o->set_ord($v);
						break;

					case "url":
					case "email":
					case "phone":
					case "organizer":
					case "tegevusala":
						// Values passed, but nothing to do with 'em.
						break;

					case "address":
						$this->process_address($o, $v, $new);
						break;

					case "photo":
						$this->process_photo($o, $v, $new);
						break;

					case "name":
					case "comment":
						if($this->conf["translations"])
						{
							$trans[$k] = $v;
						}
						else
						{
							$o->set_prop($k, $v);
						}
						break;
				}
			}
			// We need to save the original content. Otherwise we can't tell if the content has been changed in AW or the source.
			$oc[$k] = $v;
		}
		if($this->conf["translations"])
		{
			$o->set_meta("translations", $translations);
		}
	}

	private function process_organizers(&$o, $data, $new)
	{
		$oc = $new ? array() : $o->meta("orig_content_".$this->conf["lang_id"]);
		if($this->conf["translations"])
		{
			$translations = $o->meta("translations");
			$trans = &$translations[$this->conf["lang_id"]];
		}
		foreach($data as $k => $v)
		{
			// We'll update the content if it differs from original and it's not prohibited to do that.
			$update = $this->permission_to_update(&$o, &$k, &$oc, "organizer");
			if($new || $update)
			{
				switch($k)
				{
					case "jrk":
						$this->print_log($k." => ".$v, 4);
						$o->set_ord($v);
						break;

					case "url":
					case "email":
					case "phone":
					case "organizer":
						// Values passed, but nothing to do with 'em.
						break;

					case "address":
						$this->process_address($o, $v, $new);
						break;

					case "photo":
						$this->process_photo($o, $v, $new);
						break;

					case "name":
					case "comment":
						$this->print_log($k." => ".$v, 4);
						if($this->conf["translations"])
						{
							$trans[$k] = $v;
						}
						else
						{
							$o->set_prop($k, $v);
						}
						break;
				}
			}
			// We need to save the original content. Otherwise we can't tell if the content has been changed in AW or the source.
			$oc[$k] = $v;
		}
		if($this->conf["translations"])
		{
			$o->set_meta("translations", $translations);
		}
	}

	private function process_events(&$o, $data, $new)
	{
		$oc = $new ? array() : $o->meta("orig_content_".$this->conf["lang_id"]);
		if($this->conf["translations"])
		{
			$translations = $o->meta("translations");
			$trans = &$translations[$this->conf["lang_id"]];
		}
		foreach($data as $k => $v)
		{
			// We'll update the content if it differs from original and it's not prohibited to do that.
			$update = $this->permission_to_update(&$o, &$k, &$oc, "event");
			if($new || $update)
			{
				switch($k)
				{
					case "name":
					case "description":
						$this->print_log($k." => ".$v, 4);
						if($this->conf["translations"])
						{
							$trans[$k] = $v;
						}
						else
						{
							$o->set_prop($k, $v);
						}
						break;

					case "sector":
						foreach($v as $sect)
						{
							$oid = $this->check_impd_obj($sect, "sector");
//							if($this->can("view", $this->impd_objs["sector"][$sect]))
							if($this->can("view", $oid))
							{
								$o->connect(array(
//									"to" => $this->impd_objs["sector"][$sect],
									"to" => $oid,
									"type" => "RELTYPE_SECTOR",
									"data" => $sect,
								));
							}
						}
						break;

					case "url":
						$this->process_url($o, $v, $new);
						break;

					case "photo":
						$this->process_photo($o, $v, $new);
						break;

					case "event_time":
						if(!$this->conf["event_times_as_events"])
						{
							$this->process_event_times($o, $v, $new);
						}
						break;

					case "organizer":
						foreach($v as $org)
						{
							$oid = $this->check_impd_obj($sect, "sector");
//							if($this->can("view", $this->impd_objs["organizer"][$org]))
							if($this->can("view", $oid))
							{
								$o->connect(array(
//									"to" => $this->impd_objs["organizer"][$org],
									"to" => $oid,
									"type" => "RELTYPE_ORGANIZER",
									"data" => $org,
								));
							}
						}
						break;

					case "address":
					case "phone":
						// Values passed, but nothing to do with 'em.
						break;
				}
			}
			// We need to save the original content. Otherwise we can't tell if the content has been changed in AW or the source.
			$oc[$k] = $v;
		}
		if($this->conf["translations"])
		{
			$o->set_meta("translations", $translations);
		}
		if($this->conf["event_times_as_events"])
		{
			// Save event times as events.
			$first = true;
			foreach($data["event_time"] as $time)
			{
				// I HAVE TO CHANGE THE EXTERNAL ID!!. The external ID of the event time might be the same as the external ID of some event. Fuggit! -kaarel
				$ext_id = $data["ext_id"]."_EVNTTM_".$time["ext_id"];
				$oid = $this->check_impd_obj($ext_id, "event");
//				$new_et = $this->can("view", $this->impd_objs["event"][$ext_id]);
				$new_et = !$this->can("view", $oid);
				if(!$new_et)
				{
					$t = obj($oid);
				}
				else
				{
					$t = $first ? $o : obj($o->save_new());
					$first = false;
				}
				foreach($time as $k => $v)
				{
					$update = $this->permission_to_update(&$t, &$k, &$oc, "event");
					if($new_et || $update)
					{
						switch($k)
						{
							case "location":
								$oid = $this->check_impd_obj($time["location"], "location");
//								if($this->can("view", $this->impd_objs["location"][$time["location"]]))
								if($this->can("view", $oid))
								{
									$t->connect(array(
//										"to" => $this->impd_objs["location"][$time["location"]],
										"to" => $oid,
										"type" => "RELTYPE_LOCATION",
										"data" => $time["location"],
									));
//									$t->set_prop("location", $this->impd_objs["location"][$time["location"]]);
									$t->set_prop("location", $oid);
								}
								break;

							case "start":
								$t->set_prop("start1", $time[$k]);
								break;

							case "end":
								$t->set_prop($k, $time[$k]);
								break;

							case "jrk":
								$t->set_ord($time["jrk"]);
								break;
						}
					}
				}
				$this->save_obj($t);
			}
		}
	}

	private function add_to_impd_objs($o, $ext_id, $type)
	{
		$ext_sys_id = $this->conf["extsys"][$type];

		$e = obj();
		$e->set_class_id(CL_EXTERNAL_SYSTEM_ENTRY);
		$e->set_parent($ext_sys_id);
		$e->ext_sys_id = $ext_sys_id;
		$e->obj = $o->id();
		$e->value = $ext_id;
		$this->save_obj($e);

//		$this->impd_objs[$type][$ext_id] = $o->id();
	}

	private function get_impd_objs()
	{
		$r = array();

		$ots = array("event", "location", "organizer", "sector");

		foreach($ots as $ot)
		{
			$odl = new object_data_list(
				array(
					"class_id" => CL_EXTERNAL_SYSTEM_ENTRY,
					"ext_sys_id" => $this->conf["extsys"][$ot],
					"lang_id" => array(),
					"site_id" => array(),
					"parent" => array(),
					"status" => array(),
				),
				array(
					CL_EXTERNAL_SYSTEM_ENTRY => array("obj", "value"),
				)
			);
			foreach($odl->arr() as $v)
			{
				$r[$ot][$v["value"]] = $v["obj"];
			}
		}

		return $r;
	}

	private function get_conf($o)
	{
		$c = array(
			"id" => $o->id(),
			"input_file" => aw_ini_get("site_basedir")."/files/event_import_".$o->id()."_input.txt",
			"progress_file" => aw_ini_get("site_basedir")."/files/event_import_".$o->id()."_progress.txt",
		);
		$ts =  array("event", "event_time", "location", "organizer", "sector");

		$em = obj($o->events_manager);
		$c["dir"]["event"] = $em->event_menu;
		$c["dir"]["location"] = $em->places_menu;
		$c["dir"]["organizer"] = $em->organiser_menu;
		$c["dir"]["sector"] = $em->sector_menu;
		foreach($c["dir"] as $k => $v)
		{
			if(!$this->can("view", $v))
			{
				die(sprintf(t("You must set dir for %s"), $k));
			}
		}

		foreach($ts as $t)
		{
			$c["extsys"][$t] = $o->prop("extsys_".$t);
			if(!$this->can("view", $c["extsys"][$t]))
			{
				die(sprintf(t("You must set external system for %s"), $t));
			}
		}

		$c["clid"]["event"] = CL_CALENDAR_EVENT;
		$c["clid"]["event_time"] = CL_EVENT_TIME;
		$c["clid"]["location"] = CL_SCM_LOCATION;
		$c["clid"]["organizer"] = CL_CRM_COMPANY;
		$c["clid"]["sector"] = CL_CRM_SECTOR;

		foreach($ts as $t)
		{
			$meta = $o->meta("urt_".$t);
			if(is_array($meta))
			{
				foreach(array_keys(get_instance($c["clid"][$t])->get_all_properties()) as $p)
				{
					if(!array_key_exists($p, $meta))
					{
						$c["do_not_update"][$t][] = $p;
					}
				}
			}
		}

		$c["event_times_as_events"] = $o->prop("event_times_as_events");

		$c["lang_id"] = $o->prop("language.lang_id");
		$c["translations"] = $o->prop("translations");

		return $c;
	}

	private function print_log($s, $lvl = 1)
	{
		if($this->conf["verbose"] == 1)
		{
			for($i = 0; $i < $lvl; $i++)
			{
				print "&nbsp;&nbsp;";
			}
			print $s."<br />";
			flush();
		}
	}

	private function save_obj(&$o)
	{
		aw_disable_acl();
		$o->save();
		aw_restore_acl();
	}

	private function permission_to_update($o, $k, $oc, $type)
	{
		// These are checked otherwise.
		$keys_always_allowed = array(
			"event" => array("photo", "url"),
			"event_time" => array(),
			"location" => array("photo", "address"),
			"organizer" => array("photo", "address"),
			"sector" => array(),
		);
		if(in_array($k, $keys_always_allowed[$type]))
		{
			return true;
		}

		$p = ($k == "jrk") ? $o->ord() : ($this->conf["translations"] ? $o->trans_get_val($k, $this->conf["lang_id"], true) : $o->prop($k));
		if($oc[$k] !== $p)
		{
			return !in_array($k, $this->conf["do_not_update"][$type]);
		}
		else
		{
			return true;
		}
	}

	private function process_photo(&$o, $photos, $new)
	{
		foreach($photos as $photo)
		{
			if(!is_oid($o->id()))
			{
				$this->save_obj($o);
			}
			$no_photo = count(connection::find(array(
				"data" => $photo["ext_id"],
				"from" => $o->id(),
				"type" => "RELTYPE_PHOTO",
			))) == 0;
			if($new || $no_photo)
			{
				$this->print_log("photo => ".$photo["name"], 4);
				$photo_data = $this->inst[CL_IMAGE]->add_image(array(
					"from" => "url",
					"url" => $photo["big"],
					"parent" => $o->id(),
					"orig_name" => $photo["name"],
				));
				$photo_obj = obj($photo_data["id"]);
				$photo_obj->set_ord($photo["jrk"]);
				$this->save_obj($photo_obj);
				$o->connect(array(
					"to" => $photo_obj,
					"type" => "RELTYPE_PHOTO",
					"data" => $photo["ext_id"],
				));
				$this->print_log("photo => OID - ".$photo_obj->id(), 4);
				switch($o->class_id())
				{
					case CL_CALENDAR_EVENT:
						$o->uimage1 = $photo_obj->id();
						break;

					case CL_CRM_COMPANY:
						$o->logo = $photo_obj->id();
						$o->connect(array(
							"to" => $photo_obj->id(),
							"type" => "RELTYPE_IMAGE",
						));
						break;

					default:
						$o->photo = $photo_obj->id();
						break;
				}
			}
		}
	}

	private function process_address(&$o, $addrs, $new)
	{
		foreach($addrs as $addr)
		{
			if(!is_oid($o->id()))
			{
				$this->save_obj($o);
			}
			$no_addr = count(connection::find(array(
				"data" => $addr["ext_id"],
				"from" => $o->id(),
				"type" => "RELTYPE_ADDRESS",
			))) == 0;
			if($new || $no_addr)
			{
				$this->print_log("address => ".$addr["name"], 4);
				$addr_obj = obj();
				$addr_obj->set_class_id(CL_CRM_ADDRESS);
				$addr_obj->set_parent($o->id());
				$addr_obj->name = $addr["name"];
				$addr_obj->comment = $addr["comment"];
				$addr_obj->aadress = $addr["address"];
				$addr_obj->set_ord($addr["jrk"]);
				$this->save_obj($addr_obj);
				$o->connect(array(
					"to" => $addr_obj,
					"type" => "RELTYPE_ADDRESS",
					"data" => $addr["ext_id"],
				));
				$this->print_log("address => OID - ".$addr_obj->id(), 4);
				switch($o->class_id())
				{
					case CL_CRM_COMPANY:
						$o->contact = $addr_obj->id();
						break;

					default:
						$o->address = $addr_obj->id();
						break;
				}
			}
		}
	}

	private function process_url(&$o, $urls, $new)
	{
		foreach($urls as $url)
		{
			if(!is_oid($o->id()))
			{
				$this->save_obj($o);
			}
			$no_url = count(connection::find(array(
				"data" => $url["ext_id"],
				"from" => $o->id(),
				"type" => "RELTYPE_URL",
			))) == 0;
			if($new || $no_url)
			{
				$this->print_log("url => ".$url["name"], 4);
				$url_obj = obj();
				$url_obj->set_class_id(CL_EXTLINK);
				$url_obj->set_parent($o->id());
				$url_obj->name = $url["name"];
				$url_obj->url = $url["url"];
				$url_obj->set_ord($url["jrk"]);
				$this->save_obj($url_obj);
				$o->connect(array(
					"to" => $url_obj,
					"type" => "RELTYPE_URL",
					"data" => $url["ext_id"],
				));
				$this->print_log("connected to url => OID - ".$url_obj->id(), 4);
			}
		}
	}

	private function process_event_times($o, $event_times, $new)
	{
		if(!is_oid($o->id()))
		{
			$this->save_obj($o);
		}
		foreach($event_times as $time)
		{
			$ol = new object_list(array(
				"class_id" => CL_EXTERNAL_SYSTEM_ENTRY,
				"ext_sys_id" => $this->conf["extsys"]["event_time"],
				"lang_id" => array(),
				"site_id" => array(),
				"parent" => array(),
				"status" => array(),
				"value" => $time["ext_id"],
				"limit" => 1,
			));
			$new_et = $ol->count() == 0;
			if(!$new_et)
			{
				$t = obj($ol->begin());
			}
			else
			{
				$t = obj();
				$t->set_class_id(CL_EVENT_TIME);
				$t->set_parent($o->id());
			}
			$oc = $new_et ? array() : $o->meta("orig_content_".$this->conf["lang_id"]);
			foreach($time as $k => $v)
			{
				$update = $this->permission_to_update(&$t, &$k, &$oc, "event_time");
				if($new_et || $update)
				{
					switch($k)
					{
						case "location":
							$oid = $this->check_impd_obj($time["location"], "location");
//							if($this->can("view", $this->impd_objs["location"][$time["location"]]))
							if($this->can("view", $oid))
							{
								$t->connect(array(
//									"to" => $this->impd_objs["location"][$time["location"]],
									"to" => $oid,
									"type" => "RELTYPE_LOCATION",
									"data" => $time["location"],
								));
//								$t->set_prop("location", $this->impd_objs["location"][$time["location"]]);
								$t->set_prop("location", $oid);
							}
							break;

						case "start":
						case "end":
							$t->set_prop($k, $time[$k]);
							break;

						case "jrk":
							$t->set_ord($time["jrk"]);
							break;
					}
				}
				// We need to save the original content. Otherwise we can't tell if the content has been changed in AW or the source.
				$oc[$k] = $v;
			}
			$this->save_obj($t);
			$o->connect(array(
				"to" => $t->id(),
				"type" => "RELTYPE_EVENT_TIMES",
			));
			if($new_et)
			{
				$this->add_to_impd_objs($t, $time["ext_id"], "event_time");
			}
		}
	}

	function get_data($slave_inst, $mmih_args)
	{
		if(file_exists($this->conf["input_file"]))
		{
			return unserialize(file_get_contents($this->conf["input_file"]));
		}
		else
		{
			$content = $slave_inst->make_master_import_happy($mmih_args);
			$f = fopen($this->conf["input_file"], "w");
			fwrite($f, serialize($content));
			return $content;
		}
	}

	function check_progress()
	{
		if(file_exists($this->conf["progress_file"]))
		{
			$progress_data = explode("\n", file_get_contents($this->conf["progress_file"]));
			foreach($progress_data as $core_row)
			{
				$row = unserialize($core_row);
				$this->progress[$row["type"]][] = $row["ext_id"];
			}
			foreach($this->data as $type => $data)
			{
				foreach($data as $item)
				{
					if(strlen($item["ext_id"]) !== 0 && !in_array($item["ext_id"], $this->progress[$type]))
					{
						return true;
					}
				}
			}
			return false;
		}
		return true;
	}

	function init_reload($i)
	{
		$this->countdown = time() + $i - 10;
		$this->print_log("Timeout after ".$i." seconds.");
	}

	function add_to_progress_and_check_time_limit($type, $ext_id)
	{
		$this->progress[$type][] = $ext_id;

		$f = fopen($this->conf["progress_file"], "a");
		fwrite($f, serialize(array("type" => $type, "ext_id" => $ext_id))."\n");
		fclose($f);

		if(time() >= $this->countdown)
		{
			$this->print_log("<b>TOO CLOSE TO TIME LIMIT, REFRESHING!</b>");
//			$this->print_log("<b>TOO CLOSE TO TIME LIMIT, EMTYING CACHE AND REFRESHING!</b>");
//			get_instance("cache")->full_flush();
			print "<script language=\"javascript\">window.location.reload(true);</script>";
			exit;
		}
	}

	function check_ext_id_done($type, $ext_id)
	{
		return in_array($ext_id, $this->progress[$type]);
	}

	function check_impd_obj($id, $type)
	{
		$ol = new object_list(array(
			"class_id" => CL_EXTERNAL_SYSTEM_ENTRY,
			"ext_sys_id" => $this->conf["extsys"][$type],
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => array(),
			"status" => array(),
			"value" => $id,
			"limit" => 1,
		));
		if($ol->count() > 0)
		{
			$oid = obj(reset($ol->ids()))->obj;
			return is_oid($oid) && $this->can("view", $oid) ? $oid: -1;
		}
		return -1;
	}
}

?>
