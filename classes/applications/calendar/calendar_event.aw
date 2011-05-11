<?php
// calendar_event.aw - Kalendri syndmus
/*
@classinfo syslog_type=ST_CALENDAR_EVENT relationmgr=yes maintainer=kristo

@default group=general
@default table=planner

@property jrk type=textbox size=4 table=objects
@caption Jrk

@property start1 type=datetime_select field=start
@caption Algab

@property end type=datetime_select field=end
@caption L&otilde;peb

@property project_selector type=project_selector store=no group=projects all_projects=1
@caption Projektid

@property project_selector2 type=project_selector store=no group=projects22 all_projects=1
@caption Projektid 2

@property utextbox1 type=textbox
@caption

@property utextbox2 type=textbox
@caption

@property utextbox3 type=textbox
@caption

@property utextbox4 type=textbox
@caption

@property utextbox5 type=textbox
@caption

@property utextbox6 type=textbox
@caption

@property utextbox7 type=textbox
@caption

@property utextbox8 type=textbox
@caption

@property utextbox9 type=textbox
@caption

@property utextbox10 type=textbox
@caption

@property utextarea1 type=textarea
@caption

@property utextarea2 type=textarea
@caption

@property utextarea3 type=textarea
@caption

@property utextarea4 type=textarea
@caption

@property utextarea5 type=textarea
@caption

@property utextvar1 type=classificator
@caption

@property utextvar2 type=classificator
@caption

@property utextvar3 type=classificator
@caption

@property utextvar4 type=classificator
@caption

@property utextvar5 type=classificator
@caption

@property utextvar6 type=classificator
@caption

@property utextvar7 type=classificator
@caption

@property utextvar8 type=classificator
@caption

@property utextvar9 type=classificator
@caption

@property utextvar10 type=classificator store=connect reltype=RELTYPE_UTEXTVAR10
@caption

@property ufupload1 type=fileupload
@caption Faili upload 1

@property multifile_upload type=multifile_upload table=objects field=meta method=serialize reltype=RELTYPE_PICTURE image=1 store=no
@caption Pildi upload

@property ucheckbox1 type=checkbox
@caption

@property ucheckbox2 type=checkbox
@caption

@property ucheckbox3 type=checkbox
@caption

@property ucheckbox4 type=checkbox
@caption

@property ucheckbox5 type=checkbox
@caption

@property title type=textarea field=title
@caption Sissejuhatus

@property short_description type=textarea allow_rte=2 field=user1
@caption L&uuml;hikirjeldus

@property description type=textarea allow_rte=2 field=user2
@caption Kirjeldus

property url type=releditor table=objects field=meta method=serialize reltype=RELTYPE_URL use_form=emb rel_id=first
caption S&uuml;ndmuse kodulehek&uuml;lg

@property sector type=relpicker multiple=1 reltype=RELTYPE_SECTOR store=connect automatic=1 size=10
@caption Valdkonnad

property location type=popup_search reltype=RELTYPE_LOCATION clid=CL_SCM_LOCATION style=autocomplete field=ucheck5 no_edit=1
caption Toimumiskoht

@property location_subt type=text subtitle=1 store=no
@caption Toimumiskoht

@property location type=releditor reltype=RELTYPE_LOCATION rel_id=first props=name,address store=connect
@caption Toimumiskoht

property location_tb type=toolbar store=no no_caption=1
caption Asukoha toolbar

property location_table type=table store=no no_caption=1
caption Asukoha tabel

@property organizer_subt type=text subtitle=1 store=no
@caption Korraldaja

@property organizer type=releditor reltype=RELTYPE_ORGANIZER clid=CL_CRM_COMPANY method=serialize field=meta table=objects rel_id=first props=name,contact table_fields=name,contact
@caption Korraldaja

property organizer_tb type=toolbar store=no no_caption=1
caption Korraldaja toolbar

property organizer_table type=table store=no no_caption=1
caption Korraldaja tabel


- korraldaja releditoriks (kontakt tel, aadress[riik, maakond, linn, t2nav], email, www, nimi)

property event_time_table type=table no_caption=1 store=no
caption Toimumisaegade tabel

@property event_time_edit type=releditor store=no mode=manager2 reltype=RELTYPE_EVENT_TIMES props=start,end,location table_fields=start,end,location
@caption Toimumisajad


@property ufupload1 type=fileupload table=objects field=meta method=serialize
@caption Faili upload 1

@property aliasmgr type=aliasmgr no_caption=1 store=no
@caption Aliastehaldur

@property level type=select field=level field=ucheck4
@caption Tase

@property published type=checkbox field=ucheck2
@caption Avaldatud

@property front_event type=checkbox field=ucheck3
@caption Esilehe s&uuml;ndmus

@property event_time type=relpicker reltype=RELTYPE_EVENT_TIME store=connect
@caption Toimumisaeg

@default field=meta
@default method=serialize
@default table=objects

@property uimage1 type=releditor reltype=RELTYPE_PICTURE rel_id=first use_form=emb
@caption Pilt

@property seealso type=relpicker reltype=RELTYPE_SEEALSO
@caption Vaata lisaks

@property recurrence type=releditor reltype=RELTYPE_RECURRENCE group=recurrence rel_id=first props=start,recur_type,end,weekdays,interval_daily,interval_weekly,interval_montly,interval_yearly,
@caption Kordused

@property iver type=image_verification width=150 height=30 text_color=000000 background_color=FFFFFF font_size=10 side=right
@caption Kontrolltekst

@groupinfo projects caption="Projektid"
@groupinfo recurrence caption=Kordumine


@tableinfo planner index=id master_table=objects master_index=brother_of

@groupinfo transl caption=T&otilde;lgi
@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi


@reltype PICTURE value=1 clid=CL_IMAGE
@caption Pilt

@reltype SEEALSO value=2 clid=CL_DOCUMENT
@caption Vaata lisaks

@reltype RECURRENCE value=3 clid=CL_RECURRENCE
@caption Kordus

@reltype UTEXTVAR10 value=4 clid=CL_META
@caption RELTYPE_UTEXTVAR10

@reltype SECTOR value=5 clid=CL_CRM_SECTOR
@caption Tegevusala

@reltype ORGANIZER value=6 clid=CL_CRM_COMPANY
@caption Korraldaja

@reltype URL value=7 clid=CL_URL
@caption Korraldaja

@reltype LOCATION value=8 clid=CL_SCM_LOCATION
@caption Toimumiskoht

@reltype EVENT_TIME value=9 clid=CL_EVENT_TIME
@caption Toimumisaeg

@reltype FILE value=10 clid=CL_FILE
@caption Fail

@reltype EVENT_TIMES value=11 clid=CL_EVENT_TIME
@caption Toimumisaeg (releditor)


*/


class calendar_event extends class_base
{
	function calendar_event()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/calendar_event",
			"clid" => CL_CALENDAR_EVENT
		));

		$this->level_options = array(
			1 => "&Uuml;leriikliku t&auml;htsusega",
			2 => "Kohaliku t&auml;htsusega",
			3 => "V&auml;lismaal toimuv"
		);
		$this->trans_props = array(
			"name", "title",  "short_description", "description", "utextbox1", "utextbox2", "utextbox3", "utextbox4", "utextbox5", "utextbox6", "utextbox7", "utextbox8", "utextbox9", "utextbox10", "utextarea1", "utextarea2", "utextarea3", "utextarea4", "utextarea5"
		);

		lc_site_load("document", $this);
	}

	/**

		@attrib name=new params=name all_args="1" nologin="1"

		@param class required
		@param parent optional type=int acl="add"
		@param period optional
		@param alias_to optional
		@param alias_to_prop optional
		@param return_url optional
		@param reltype optional type=int

	**/
	public function new_change($arr)
	{
		return parent::new_change($arr);
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "ufupload1":
                                if (is_uploaded_file($_FILES["ufupload1"]["tmp_name"]))
                                {
                                        $f = get_instance(CL_FILE);
					$prop["value"] = $f->create_file_from_string(array(
						"id" => $prop["value"],
						"parent" => $_POST["parent"],
						"content" => file_get_contents($_FILES["ufupload1"]["tmp_name"]),
						"name" => $_FILES["ufupload1"]["name"],
						"type" => $_FILES["ufupload1"]["type"]
					));
				}
				break;

			case "end":
				if(!is_numeric($prop["value"]["hour"]))
				{
					$prop["value"]["hour"] = 0;
				}
				if(!is_numeric($prop["value"]["minute"]))
				{
					$prop["value"]["minute"] = 0;
				}
				if(!is_numeric($prop["value"]["year"]) || !is_numeric($prop["value"]["month"]) || !is_numeric($prop["value"]["day"]))// || !is_numeric($prop["value"]["hour"]) || !is_numeric($prop["value"]["minute"]))
				{
					// There better be some good explanation why I can't use $arr["prop"]["value"] for date_select.
					$request = &$arr["request"];
					$request["end"] = $arr["request"]["start1"];
					// Just in case it should turn to normal one day...
					$prop["value"] = $arr["request"]["start1"];
				}
				else
				{
					$start = mktime($arr["request"]["start1"]["hour"], $arr["request"]["start1"]["minute"], 0, $arr["request"]["start1"]["month"], $arr["request"]["start1"]["day"], $arr["request"]["start1"]["year"]);
					$end = mktime($prop["value"]["hour"], $prop["value"]["minute"], 0, $prop["value"]["month"], $prop["value"]["day"], $prop["value"]["year"]);

					if ($start > $end)
					{
						$prop["value"]["day"] = $arr["request"]["start1"]["day"];
						$prop["value"]["month"] = $arr["request"]["start1"]["month"];
						$prop["value"]["year"] = $arr["request"]["start1"]["year"];
						$prop["value"]["hour"] = $arr["request"]["start1"]["hour"];
						$prop["value"]["minute"] = $arr["request"]["start1"]["minute"];
					}
				}
				break;

			case "organizer":
/*				$os = explode(",",$prop["value"]);
				foreach($os  as $id)
				{
					if(is_oid($id))
					{
						$arr["obj_inst"]->connect(array("to" => $id, "type" => "RELTYPE_ORGANIZER"));
					}
				}
				return PROP_IGNORE;
*/
				break;
			case "event_time":
				return PROP_IGNORE;

			case "event_time_table":
				$this->id = $arr["obj_inst"]->id();
				$this->save_event_times($arr["request"]["event_time"]);
				break;

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

/*			case "organizer":
		 		if(!is_oid($prop["value"]))
 				{
 					if(is_oid($arr["request"]["organizer_awAutoCompleteTextbox"]) && $this->can("view" , $arr["request"]["organizer_awAutoCompleteTextbox"]))
 					{
 						$prop["value"] = $arr["request"]["organizer_awAutoCompleteTextbox"];
 					}
 					elseif($arr["request"]["organizer_awAutoCompleteTextbox"])
 					{
 						$ol = new object_list(array(
 							"name" => $arr["request"]["organizer_awAutoCompleteTextbox"],
 							"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
 							"lang_id" => array(),
 						));
 						$cust_obj = $ol->begin();
 						if(is_object($cust_obj))$prop["value"] = $cust_obj->id();
 					}
 				}
				break;
			case "location":
				//arr($prop["value"]);die();


		 		if(!is_oid($prop["value"]))
 				{
 					if(is_oid($arr["request"]["location_awAutoCompleteTextbox"]) && $this->can("view" , $arr["request"]["location_awAutoCompleteTextbox"]))
 					{
 						$prop["value"] = $arr["request"]["location_awAutoCompleteTextbox"];
 					}
 					elseif($arr["request"]["location_awAutoCompleteTextbox"])
 					{
 						$ol = new object_list(array(
 							"name" => $arr["request"]["location_awAutoCompleteTextbox"],
 							"class_id" => CL_SCM_LOCATION,
 							"lang_id" => array(),
 						));
 						$cust_obj = $ol->begin();
 						if(is_object($cust_obj))$prop["value"] = $cust_obj->id();
 					}
 				}
				break;*/
		}
		$meta = $arr["obj_inst"]->meta();
		if (substr($prop["name"],0,1) == "u")
		{
			if ($meta[$prop["name"]])
			{
				$arr["obj_inst"]->set_meta($prop["name"],"");
			};
		};
		return $retval;
	}

	function callback_mod_tab($arr)
	{
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function get_property($arr)
	{
		$retval = PROP_OK;
		$prop = &$arr["prop"];
                if ($arr["obj_inst"])
                {
                        $meta = $arr["obj_inst"]->meta();
                        if (substr($prop["name"],0,1) == "u")
                        {
                                if (!empty($meta[$prop["name"]]))
                                {
                                        $prop["value"] = $meta[$prop["name"]];
                                };
                        };
                };

		switch($prop["name"])
		{
			case "title":
			case "description":
			//	$prop["value"] = nl2br($prop["value"]);
				break;
			//case "multifile_upload": return PROP_IGNORE;
			case "level":
				$prop["options"] = $this->level_options;
				break;

			case "ufupload1":
				if ($this->can("view", $prop["value"]))
				{
					$fo = obj($prop["value"]);
					$fi = $fo->instance();
					$prop["value"] = $fi->parse_alias(array("alias" => array("target" => $fo->id())));
				}
				break;

			case "event_time":
				return PROP_IGNORE;

			case "event_time_table":
//				if (!$arr['new'])
//				{
					$this->do_event_time_table($arr);
//				}
				break;
			case "location":
				//$prop["table_fields"] = array("name","address.riik","afaf");
//				$prop["props"] = $prop["table_fields"];//explode(",", "name,address,address.riik,address.linn,address.street,address.county");
//					arr($prop);
				break;
			case "organizer_table":
				$this->_get_organizer_table($arr);
				break;
			case "organizer_tb":
				$this->_get_organizer_tb($arr);
				break;
			case "location_table":
				$this->_get_location_table($arr);
				break;
			case "location_tb":
				$this->_get_location_tb($arr);
				break;
		}
		return $retval;
	}

	function check_format($t)
	{
		if(!$t["start"] || !$t["location"])
		{
			return null;
		}
		$start_dt = explode(" ", $t["start"]);
		$end_dt = explode(" ", $t["end"]);

		$start_d = explode(".", $start_dt[0]);
		$end_d = explode(".", $end_dt[0]);

		$start_t = explode(":", $start_dt[1]);
		$end_t = explode(":",$end_dt[1]);

		if(!($start_d[0] > -1 && $start_d[0] < 32 && $start_d[1] > 0 && $start_d[1] < 13 && $start_d[2] > 100 && $start_d[2] < 3000 && $start_t[0] > -1 && $start_t[0]< 61 && $start_t[1] > -1 && $start_t[0] <61)) return t("Algusaeg ei vasta formaadile");
		if(!($end_d[0] > -1 && $end_d[0] < 32 && $end_d[1] > 0 && $end_d[1] < 13 && $end_d[2] > 100 && $end_d[2] < 3000 && $end_t[0] > -1 && $end_t[0]< 61 && $end_t[1] > -1 && $end_t[0] <61)) return t("L&otilde;puaeg ei vasta formaadile");

		return null;
	}

	function save_event_times($times)
	{
		$event = obj($this->id);
		foreach($times as $id => $val)
		{
			if(!$val["end"])
			{
				$val["end"] = $val["start"];
			}
			$error[$id] = $this->check_format($val);
			if(is_oid($id) && $this->can("view" , $id))
			{
				$o = obj($id);
			}
			else
			{
				if($val["start"])
				{
					$o = new object();
					$o->set_name("");
					$o->set_class_id(CL_EVENT_TIME);
					$o->set_parent($this->id);
				}
				else
				{
					continue;
				}
			}
			$loc_list = new object_list(array(
				"lang_id" => array(),
				"site_id" => array(),
				"name" => $val["location"],
				"class_id" => CL_SCM_LOCATION,
			));
			if($loc_list->count())
			{
				$location = reset($loc_list->arr());
			}
			else
			{
				$error[$id] = t("Sellist toimumiskohta pole");
			}
			$start_dt = explode(" ", $val["start"]);
			$end_dt = explode(" ", $val["end"]);
			$start_d = explode(".", $start_dt[0]);
			$end_d = explode(".", $end_dt[0]);
			$start_t = explode(":", $start_dt[1]);
			$end_t = explode(":",$end_dt[1]);

			if(!$error[$id])
			{
				$start = mktime($start_t[0] , $start_t[1] , 0  , $start_d[1],$start_d[0],$start_d[2]);
				$end = mktime($end_t[0] , $end_t[1] , 0 ,$end_d[1],$end_d[0],$end_d[2]);
				$o->set_prop("start" , $start);
				$o->set_prop("end" , ($end > 1) ? $end : $start);
				$o->set_prop("location" , $location->id());
			//	$o->set_prop("event" , $event->id());
				$o->save();
				$event->add_event_time($o->id());arr($o->id());
			//	$event->connect(array("to" => $o->id(), "reltype" => 9));
			}
		}
		$_SESSION["event_time_save_errors"] = $error;
	}

	function do_event_time_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Algus"),
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("L&otilde;pp"),
		));
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Asukoht"),
		));
		$t->define_field(array(
			"name" => "delete",
			"caption" => "X",
		));

		if(is_oid($arr["obj_inst"]->id())){
//			$event_ol = new object_list(array(
//				"class_id" => CL_EVENT_TIME,
//				"lang_id" => array(),
//				"event" => $arr["obj_inst"]->id(),
//			));
//			foreach($event_ol->arr() as $o)
//			{
			foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_EVENT_TIME")) as $c)
			{
				$o = $c->to();
				$t->define_data(array(
					"start" => html::textbox(array(
						"name" => "event_time[".$o->id()."][start]",
						"size" => 15,
						"value" => date("d.m.Y H:i" , $o->prop("start")),
					)).'<a href="javascript:void(0);" onClick="var cal = new CalendarPopup();
	cal.select(changeform.event_time_'.$o->id().'__start_,\'anchornew\',\'dd.MM.yyyy HH:mm\'); return false;" title="Vali kuup&auml;ev" name="anchornew" id="anchornew">vali</a><font color=red> '.$_SESSION["event_time_save_errors"][$o->id()]." </font>",
					"end" => html::textbox(array(
						"name" => "event_time[".$o->id()."][end]",
						"size" => 15,
						"value" => date("d.m.Y H:i" , $o->prop("end")),
					)).'<a href="javascript:void(0);" onClick="var cal = new CalendarPopup();
	cal.select(changeform.event_time_'.$o->id().'__end_,\'anchornew\',\'dd.MM.yyyy HH:mm\'); return false;" title="Vali kuup&auml;ev" name="anchornew" id="anchornew">vali</a>',
					"location" => html::textbox(array(
						"name" => "event_time[".$o->id()."][location]",
						"size" => 30,
						"value" => $o->prop("location.name"),
						"autocomplete_source" => $this->mk_my_orb ("locations_autocomplete_source", array(), CL_CALENDAR_EVENT, false, true),
						"autocomplete_params" => "event_time[".$o->id()."][location]",
					)),
					"delete" => html::href(array(
						"caption" => t("Kustuta"),
						"url" =>  $this->mk_my_orb("remove_event_time", array(
							"id" => $o->id(),
							"return_url" => get_ru(),
						)),
					)),
				));
			}
		}
		$t->define_data(array(
			"start" => html::textbox(array(
					"name" => "event_time[new][start]",
					"size" => 15,
					"value" => "",
				)).'<a href="javascript:void(0);" onClick="var cal = new CalendarPopup();
cal.select(changeform.event_time_new__start_,\'anchornew\',\'dd.MM.yyyy HH:mm\'); return false;" title="Vali kuup&auml;ev" name="anchornew" id="anchornew">vali</a><font color=red> '.$_SESSION["event_time_save_errors"]["new"]." </font>",
			"end" => html::textbox(array(
					"name" => "event_time[new][end]",
					"size" => 15,
					"value" => "",
				)).'<a href="javascript:void(0);" onClick="var cal = new CalendarPopup();
cal.select(changeform.event_time_new__end_,\'anchornew\',\'dd.MM.yyyy HH:mm\'); return false;" title="Vali kuup&auml;ev" name="anchornew" id="anchornew">vali</a>',
			"location" => html::textbox(array(
				"name" => "event_time[new][location]",
				"size" => 30,
				"value" => is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("location") ? $arr["obj_inst"]->prop("location.name"):"",
				"autocomplete_source" => $this->mk_my_orb ("locations_autocomplete_source", array(), CL_CALENDAR_EVENT, false, true),
				"autocomplete_params" => "event_time[new][location]",
			)),
		));
		unset($_SESSION["event_time_save_errors"]);
	}

	/**
		@attrib name=locations_autocomplete_source
		@param location optional
		@param parent optional
	**/
	function locations_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_SCM_LOCATION,
			"name" => "%".$arr["location"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 2000,
		));
		return $ac->finish_ac($ol->names());
	}

	/**
		@attrib name=organizer_autocomplete_source
		@param organizer optional
		@param parent optional
	**/
	function organizer_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"name" => "%".$arr["organizer"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 2000,
		));
		return $ac->finish_ac($ol->names());
	}

	/**
		@attrib name=remove_event_time
		@param id required
		@param return_url required
	**/
	function delete_items($arr)
	{
		extract($arr);
		$o = obj($id);
		$o->delete();
		return $return_url;
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	////
	// !this will be called if the object is put in a document by an alias and the document is being shown
	// parameters
	//    alias - array of alias data, the important bit is $alias[target] which is the id of the object to show
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	function request_execute($o)
	{
		// if ($_GET["exp"] == 1)
		if (aw_ini_get("calender_event.use_show2_instead_of_show"))
		{
			return $this->show2(array("id" => $o->id()));
		}
		else
		{
			return $this->show(array("id" => $o->id()));
		};
	}

	function show2($arr)
	{
		$ob = new object($arr["id"]);
		$cform = $ob->meta("cfgform_id");
		// feega hea .. nyyd on vaja veel nimed saad
		$cform_obj = new object($cform);
		$output_form = $cform_obj->prop("use_output");
		if (is_oid($output_form))
		{
			$cform = $output_form;
		};
		$t = get_instance(CL_CFGFORM);
		$props = $t->get_props_from_cfgform(array("id" => $cform));

		// also get view controllers from cfgform and apply those
		$cform_o = obj($cform);
		$ctrs = safe_array($cform_o->meta("view_controllers"));

		$htmlc = get_instance("cfg/htmlclient",array("template" => "webform.tpl"));
		$htmlc->start_output();
		$aliasmgr = get_instance("alias_parser");
		$prop_list = $ob->get_property_list();
		foreach($props as $propname => $propdata)
		{
			$ok = true;
			if (is_array($ctrs[$propname]))
			{
				foreach($ctrs[$propname] as $v_ctr_oid)
				{
					if ($this->can("view", $v_ctr_oid))
					{
						$vco = obj($v_ctr_oid);
						$vci = $vco->instance();
						$prop_list[$propname]["value"] = $ob->prop($propname);
						$ok &= ($vci->check_property($prop_list[$propname], $v_ctr_oid, array("obj" => $ob)) == PROP_OK);
					}
				}
			}

			if (!$ok)
			{
				continue;
			}
			if($val = $prop_list[$propname]["value"])
			{
				$value = $val;
			}
			else
			{
		  		$value = $ob->prop_str($propname);
			}
			if ($propdata["type"] == "datetime_select")
			{
				if($value == -1)
				{
					continue;
				}
				$_v = $value;
				$value = date("Hi", $_v);
				if($value == "0000")
				{
					$value = date("d-m-Y", $_v);
				}
				else
				{
					$value = date("d-m-Y H:i", $_v);
				}
				//$value = date("d-m-Y H:i",$value);
			};

			if (!empty($value))
			{
				$value = create_links($value);//nl2br(create_links($value));
				if(strpos($value, "#") !== false)
				{
					$aliasmgr->parse_oo_aliases($arr["id"], $value);
				}
				$htmlc->add_property(array(
					"name" => $propname,
					"caption" => $propdata["caption"],
					"value" => $value,
					"type" => "text",
				));
			}
		}
		$htmlc->finish_output(array("submit" => "no"));

		$html = $htmlc->get_result(array(
			"form_only" => 1
		));

		return $html;
	}

	////
	// !this shows the object. not strictly necessary, but you'll probably need it, it is used by parse_alias
	/**
	@attrib name=show all_args=1 params=name
	**/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");

		$oid_props = array("classificator", "relpicker", "releditor");

		$props = get_instance(CL_CFGFORM)->get_default_proplist(array("o" => $ob));
		foreach($props as $k => $p)
		{
			switch($k)
			{
				case "seealso":
					$v = $this->can("view", $ob->$k) ? get_instance(CL_DOCUMENT)->show(array("id" => $ob->$k)) : "";
					break;

				case "start1":
				case "end":
					$v = date("d-m-Y H:i:s", $ob->$k);
					$data[$k.".date"] = get_lc_date($ob->$k, LC_DATE_FORMAT_LONG_FULLYEAR);
					$data[$k.".time"] = date("H:i", $ob->$k);
					break;

				default:
					$v = in_array($p["type"], $oid_props) ? $ob->trans_get_val($k.".name") : $ob->trans_get_val($k);
					get_instance("alias_parser")->parse_oo_aliases($ob, $v);
					break;
			}
			$data[$k] = $v;//nl2br($v);
		}

		if($this->can("view", $_GET["event_time"]))
		{
			$time = obj($_GET["event_time"]);
			$data["start1"] = date("d-m-Y H:i:s", $time->start);
			$data["start1.date"] = get_lc_date($time->start, LC_DATE_FORMAT_LONG_FULLYEAR);
			$data["start1.time"] = date("H:i", $time->start);
			$data["end"] = date("d-m-Y H:i:s", $time->end);
			$data["end.date"] = get_lc_date($time->end, LC_DATE_FORMAT_LONG_FULLYEAR);
			$data["end.time"] = date("H:i", $time->end);
			$data["location"] = $time->trans_get_val("location.name");
		}

		$this->vars($data);
		$this->parse_prop_subs($ob, $props);
		return $this->parse();
	}

	function _get_organizer_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			"name" => "main_menu",
			"tooltip" => t("Uus"),
		));

		$tb->add_menu_item(array(
			"parent" => "main_menu",
			"title" => t("Organisatsioon"),
			"text" => t("Organisatsioon"),
			"tooltip" => t("Lisa uus korraldaja"),
			"link" => $this->mk_my_orb("new", array(
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 6,
				"alias_to_prop" => "organizer",
				"return_url" => get_ru(),
				"parent" => $arr["obj_inst"]->id(),
			), CL_CRM_COMPANY),
		));

		$tb->add_menu_item(array(
			"parent" => "main_menu",
			"tooltip" => t("Lisa uus korraldaja"),
			"title" => t("Isik"),
			"text" => t("Isik"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 6,
				"alias_to_prop" => "organizer",
				"return_url" => get_ru(),
				"parent" => $arr["obj_inst"]->id(),
			), CL_CRM_PERSON),
		));

		$popup_search = get_instance("vcl/popup_search");
		$search_butt = $popup_search->get_popup_search_link(array(
			"pn" => "organizer",
			"clid" => array(CL_CRM_PERSON,CL_CRM_COMPANY),
		));
		$tb->add_cdata($search_butt);


		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "remove_organizers",
			"confirm" => t("Oled kindel, et kustutada?"),
		));

	}

	function _get_organizer_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "phone",
			"caption" =>  t("Telefon"),
		));
		$t->define_field(array(
			"name" => "country",
			"caption" =>  t("Riik"),
		));
		$t->define_field(array(
			"name" => "county",
			"caption" =>  t("Maakond"),
		));
		$t->define_field(array(
			"name" => "city",
			"caption" =>  t("Linn"),
		));
		$t->define_field(array(
			"name" => "street",
			"caption" =>  t("T&auml;nav"),
		));
		$t->define_field(array(
			"name" => "email",
			"caption" =>  t("E-mail"),
		));
		$t->define_field(array(
			"name" => "www",
			"caption" =>  t("WWW"),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" =>  t("Nimi"),
		));
		$t->define_field(array(
			"name" => "change",
			"caption" =>  t("Muuda"),
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->set_caption(t("Korraldaja"));
		if(!$arr["obj_inst"]->prop("organizer")) return;
		$o = obj($arr["obj_inst"]->prop("organizer"));
//		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ORGANIZER")) as $c)
//		{
//			$o = $c->to();
			$change_url = html::obj_change_url($o->id(),t("Muuda"));
			if($o->class_id() == CL_CRM_COMPANY)
			{
				$t->define_data(array(
					"name" => $o->name(),
					"email" => $o->prop("email_id.mail"),
					"country" => $o->prop("contact.riik.name"),
					"county" => $o->prop("contact.maakond.name"),
					"city" => $o->prop("contact.linn.name"),
					"street" => $o->prop("contact.aadress"),
					"www" => $o->prop("url_id.name"),
					"phone" => $o->prop("phone_id.name"),
					"change" => $change_url,
					"oid" => $o->id()
				));
			}
			else
			{
				$t->define_data(array(
					"name" => $o->name(),
					"email" => $o->prop("email.mail"),
					"country" => $o->prop("address.riik.name"),
					"county" => $o->prop("address.maakond.name"),
					"city" => $o->prop("address.linn.name"),
					"street" => $o->prop("address.aadress"),
					"www" => $o->prop("url.name"),
					"phone" => $o->prop("phone.name"),
					"change" => $change_url,
					"oid" => $o->id()
				));

			}
//		}

	}

	function _get_location_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus toimumiskoht"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 8,
				"alias_to_prop" => "location",
				"return_url" => get_ru(),
				"parent" => $arr["obj_inst"]->id(),
			), CL_SCM_LOCATION),
		));


		$popup_search = get_instance("vcl/popup_search");
		$search_butt = $popup_search->get_popup_search_link(array(
			"pn" => "location",
			"clid" => array(CL_SCM_LOCATION),
		));
		$tb->add_cdata($search_butt);


		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "remove_organizers",
			"confirm" => t("Oled kindel, et kustutada?"),
		));

	}

	function _get_location_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" =>  t("Nimi"),
		));
		$t->define_field(array(
			"name" => "country",
			"caption" =>  t("Riik"),
		));
		$t->define_field(array(
			"name" => "county",
			"caption" =>  t("Maakond"),
		));
		$t->define_field(array(
			"name" => "city",
			"caption" =>  t("Linn"),
		));
		$t->define_field(array(
			"name" => "street",
			"caption" =>  t("T&auml;nav"),
		));
		$t->define_field(array(
			"name" => "change",
			"caption" =>  t("Muuda"),
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->set_caption(t("Asukoht"));
		if(!$arr["obj_inst"]->prop("location")) return;
		$o = obj($arr["obj_inst"]->prop("location"));
//		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_LOCATION")) as $c)
//		{
//			$o = $c->to();
			$change_url = html::obj_change_url($o->id(),t("Muuda"));
			$t->define_data(array(
				"name" => $o->name(),
				"country" => $o->prop("address.riik.name"),
				"county" => $o->prop("address.maakond.name"),
				"city" => $o->prop("address.linn.name"),
				"street" => $o->prop("address.aadress"),
				"change" => $change_url,
				"oid" => $o->id()
			));
//		}

	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
	}

	/**
		@attrib name=remove_organizers
	**/
	function remove_organizers($arr)
	{
		$o = obj($arr["id"]);
		foreach($arr["sel"] as $id)
		{
			$o->disconnect(array("from" => $id));
		}
		return $arr["post_ru"];
	}

	public function do_db_upgrade($table, $field, $q, $err)
	{
		if ("planner" === $table)
		{
			switch($field)
			{
				case "ucheckbox1":
				case "ucheckbox2":
				case "ucheckbox3":
				case "ucheckbox4":
				case "ucheckbox5":
					$this->db_add_col($table, array(
						"name" => $field,
						"type" => "INT(1) UNSIGNED DEFAULT '0' NOT NULL"
					));
					return true;
			}
		}

		return false;
	}

	private function parse_prop_subs($o, $props)
	{
		foreach($props as $k => $p)
		{
			switch($k)
			{
				default:
					if(strlen($o->prop($k)) > 0)
					{
						$this->vars(array(
							strtoupper($k) => $this->parse(strtoupper($k)),
						));
					}
					break;
			}
		}
	}


}
?>
