<?php

// reservation.aw - Broneering
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_RESERVATION, on_delete_reservation)

@tableinfo planner index=id master_table=objects master_index=brother_of
@tableinfo aw_room_reservations index=aw_oid master_table=objects master_index=brother_of


@classinfo relationmgr=yes no_status=1 prop_cb=1

@default table=objects
@default group=general
#TAB GENERAL

@groupinfo general caption=&Uuml;ldine default=1 icon=edit focus=cp_fn submit=no

@layout general_split type=hbox

@layout general_up type=vbox closeable=1 area_caption=&Uuml;ldinfo parent=general_split

	@layout general_up_up parent=general_up type=vbox

	@property b_tb type=toolbar store=no no_caption=1 parent=general_up_up

	@property name type=textbox field=name method=none size=20 parent=general_up_up
	@caption Nimi

	@property deadline type=datetime_select table=planner field=deadline parent=general_up_up
	@caption Maksmist&auml;htaeg

	@property verified type=checkbox ch_value=1 table=aw_room_reservations field=aw_verified no_caption=1 default=1 parent=general_up_up
	@caption Kinnitatud

	@property paid type=checkbox ch_value=1 table=aw_room_reservations field=aw_paid no_caption=1 parent=general_up_up
	@caption Makstud

	@property unverify_reason type=text store=no no_caption=1 parent=general_up_up

	@property resource type=relpicker reltype=RELTYPE_RESOURCE table=aw_room_reservations field=aw_resource parent=general_up_up
	@caption Ruum

	@property other_rooms type=select multiple=1 store=no parent=general_up_up
	@caption Lisaks ruumid

	@property customer type=relpicker table=planner field=customer reltype=RELTYPE_CUSTOMER parent=general_up_up
	@caption Klient

	@property cp_fn type=textbox store=no size=20 parent=general_up_up
	@caption Eesnimi

	@property cp_ln type=textbox store=no size=20 parent=general_up_up
	@caption Perenimi

	@property cp_phone type=textbox store=no size=12 parent=general_up_up
	@caption Telefon

	@property cp_email type=textbox store=no size=20 parent=general_up_up
	@caption E-mail

	@property project type=relpicker table=planner field=project reltype=RELTYPE_PROJECT type=popup_search style=autocomplete parent=general_up_up
	@caption Projekt

	@property send_bill type=checkbox ch_value=1 table=planner field=send_bill no_caption=1 parent=general_up_up
	@caption Saata arve

	@property bill_no type=hidden table=planner  parent=general_up_up
	@caption Arve number

	@property comment type=textarea cols=40 rows=1 parent=general_up_up
	@caption Kommentaar

	@property content type=textarea cols=40 rows=5 field=description table=planner parent=general_up_up
	@caption Sisu

	@property time_closed type=checkbox ch_value=1 table=aw_room_reservations field=aw_time_closed parent=general_up_up
	@caption Suletud

	@property closed_info type=textbox table=aw_room_reservations field=aw_closed_info size=30 parent=general_up_up
	@caption Sulgemise p&otilde;hjus

	@layout sbt_layout type=hbox parent=general_up

	@property sbt type=submit no_caption=1 parent=sbt_layout
	@caption Salvesta

	@property sbt_close type=submit no_caption=1 parent=sbt_layout
	@caption Salvesta ja sulge

@layout general_down type=vbox closeable=1 area_caption=Aeg&#44;&nbsp;ja&nbsp;hind parent=general_split

	@property people_count type=textbox size=3 table=aw_room_reservations field=aw_people_count parent=general_down
	@caption Inimesi

	@property start1 type=datetime_select field=start table=planner parent=general_down
	@caption Algus

	@property length type=select store=no parent=general_down
	@caption Pikkus

	@property end type=datetime_select table=planner parent=general_down
	@caption L&otilde;peb

	@property special_discount type=textbox size=5 table=aw_room_reservations field=aw_special_discount parent=general_down
	@caption Spetsiaal allahindlus

	@property special_sum type=textbox size=5 table=aw_room_reservations field=aw_special_sum parent=general_down
	@caption Spetsiaal hind

	@property products_discount type=hidden size=5 table=aw_room_reservations field=aw_products_discount no_caption=1 parent=general_down
	@caption Toodete allahindlus

	property code type=hidden size=5 table=planner field=code parent=general_down
	caption Kood

	@property client_arrived type=chooser table=aw_room_reservations field=aw_client_arrived parent=general_down
	@caption Klient saabus

	@property inbetweener type=select table=aw_room_reservations field=aw_inbetweener parent=general_down
	@caption Vahendaja

	@property people type=select table=aw_room_reservations field=aw_people parent=general_down
	@caption Org. esindajad

	@property type type=select table=aw_room_reservations field=aw_type parent=general_down
	@caption Broneeringu t&uuml;&uuml;p

	@property products_text type=text submit=no parent=general_down
	@caption Toode

	@property sum type=text table=aw_room_reservations field=aw_sum  no_caption=1 parent=general_down
	@caption Summa

	@property modder type=text store=no no_caption=1 parent=general_down

property summary type=textarea cols=80 rows=30 table=planner field=description no_caption=1
caption Kokkuv&otilde;te

@groupinfo reserved_resources caption="Ressursid"
@default group=reserved_resources

	@property resources_tbl type=table no_caption=1

	@property resources_price type=hidden table=aw_room_reservations field=resources_price no_caption=1
	@caption Ressursside eraldi m&auml;&auml;ratud hind

	@property resources_discount type=hidden table=aw_room_reservations field=resources_discount no_caption=1
	@caption Ressursside eraldi m&auml;&auml;ratud soodus

@tableinfo planner index=id master_table=objects master_index=brother_of

@groupinfo products caption="Tooted"
@default group=products

	@property products_tbl type=table no_caption=1

@groupinfo prices caption="Hinnad"
@default group=prices

	@property prices_tbl type=table no_caption=1

@groupinfo ppl caption="Kliendid"
@default group=ppl

	@property ppl_tb type=toolbar no_caption=1 store=no
	@property ppl type=table no_caption=1 store=no

@groupinfo recur caption="Kordumine"

@groupinfo recur_entry caption="Sisesta kordused" parent=recur
@default group=recur_entry

	@property recur_tb type=toolbar no_caption=1 store=no
	@property recur_t type=table no_caption=1 store=no

@groupinfo recur_manage caption="Halda korratud reserveeringuid" parent=recur submit=no
@default group=recur_manage

	@property recur_manage_tb type=toolbar no_caption=1 store=no
	@property recur_manage_t type=table no_caption=1 store=no

@tableinfo planner index=id master_table=objects master_index=brother_of

#RELTYPES

@reltype CUSTOMER value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype PROJECT value=2 clid=CL_PROJECT
@caption Projekt

@reltype RESOURCE value=3 clid=CL_ROOM
@caption Ressurss

@reltype RECURRENCE value=4 clid=CL_RECURRENCE
@caption Kordus

@reltype REPEATED_BRON value=5 clid=CL_RESERVATION
@caption Korratud reserveering

@reltype ORIGINAL_BRON value=6 clid=CL_RESERVATION
@caption Korduse originaal

*/

class reservation extends class_base
{
	function reservation()
	{
		$this->init(array(
			"tpldir" => "applications/groupware/reservation",
			"clid" => CL_RESERVATION
		));
		$this->get_from_parent_props = array("deadline", "verified", "paid", "unverify_reason", "customer", "project", "send_bill", "comment", "content", "people_count", "start1", "length", "end", "client_arrived", "inbetweener", "people");
		$this->bron_types = array(
			"" => "",
			"food" => t("Toitlustuse broneering"),
		);
	}

	function reason_list()
	{
		return array(
			3 => t("Loobus"),
			4 => t("Uus aeg"),
			5 => t("P&otilde;hjus teadmata"),
			6 => t("Paneb ise uue aja"),
		);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		if($is_lower_bron = $arr["obj_inst"]->is_lower_bron())
		{
			$master_bron = obj($is_lower_bron);
		}
		switch($prop["name"])
		{
			case "type":

	/*			$ol = new object_list(array(
					"class_id" => CL_RFP,
					"lang_id" => array(),
					"site_id" => array(),
				//	"CL_RFP.RELTYPE_CATERING_RESERVATION.type" =>  obj_predicate_not("food"),
				));
				foreach($ol->arr() as $o)
				{
					foreach($o->connections_from(array(
						"type" => "RELTYPE_CATERING_RESERVATION",
					)) as $c)
					{
						$bron = $c->to();
						if($bron->prop("type") != "food")
						{
							arr($bron);
							$bron->set_prop("type" , "food");
							$bron->save();
						}
					}
				}
*/

				return PROP_IGNORE;
			case "other_rooms":
				if($is_lower_bron) return PROP_IGNORE;
				if($this->can("view" , $arr["obj_inst"]->prop("resource")))
				{
					$room = obj($arr["obj_inst"]->prop("resource"));
					$prop["options"] = $room->get_other_rooms_selection();
					$prop["value"] = $arr["obj_inst"]->get_other_bron_rooms();
					if(!sizeof($prop["options"]))
					{
						return PROP_IGNORE;
					}
				}
				elseif(!is_oid($arr["obj_inst"]->id()) && $arr["request"]["resource"])
				{
					$room = obj($arr["request"]["resource"]);
					$prop["options"] = $room->get_other_rooms_selection();
					$prop["value"] = array_keys($prop["options"]);
					if(!sizeof($prop["options"]))
					{
						return PROP_IGNORE;
					}
				}
				else
				{
					return PROP_IGNORE;
				}
				break;
			case "bill_no":
				if($is_lower_bron)
				{
					$prop["value"] = $master_bron->prop[$prop["name"]];
				}
				//if(aw_global_get("uid")== "struktuur"){arr($arr["obj_inst"]->meta());arr($this->mk_my_orb("parse_alias", array("level" => 1, "preview" => 1, "id" => $arr["obj_inst"]->id() , "tpl" => $tpl,)));}
				if(!is_oid($prop["value"]))
				{
					return PROP_IGNORE;
				}
				break;
			case "people_count":
				if($is_lower_bron)
				{
					$prop["type"] = "text";
					$prop["value"] = $master_bron->prop[$prop["name"]];
				}
				if(!($prop["value"] > 0))
				{
					$prop["value"] = 1;
				}
				break;

/*			case "sbt":
				$prop["type"] = "submit";
				$prop["value"] = t("Salvesta!");
				$prop["onclick"] = "javascript:submit_changeform();";
				$prop["class"] = "sbtbutton";
				break;*/

			case "sbt_close":
				$prop["type"] = "text";
				$prop["value"] = "<input id='cbsubmit' type='submit' name='sbt_close' value='Salvesta ja sulge' class='sbtbutton' onclick=''  />";
				/*$prop["value"] = "<input id='cbsubmit' type='submit' name='sbt_close' value='Salvesta ja sulge' class='sbtbutton'
					onclick='
						if (typeof(aw_submit_handler) != \"undefined\")
						{
							if (aw_submit_handler() == false)
							{
								return false;
							}
						}'
						/>";
				*/
				break;
			case "resource":
				if (!isset($prop["options"][$prop["value"]]) && $this->can("view", $prop["value"]))
				{
					$tmp = obj($prop["value"]);
					$prop["options"][$prop["value"]] = $tmp->name();
				}
				$prop["options"] = $this->get_resource_options($arr["obj_inst"]);
				if($arr["new"] && $arr["request"][$prop["name"]])
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;
			case "start1":
			case "end":
				if($is_lower_bron)
				{
					$prop["type"] = "text";
					$prop["value"] = date("d.m.Y H:i" , $prop["value"]);
					return PROP_OK;
				}
				$prop["options"] = $this->get_resource_options($arr["obj_inst"]);
				if($arr["new"] && $arr["request"][$prop["name"]])
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;
			case "products_tbl":
				if($is_lower_bron) return PROP_IGNORE;
				$this->get_products_tbl;
				break;

			case "verified":
				if($is_lower_bron) return $prop["disabled"] = 1;
				$conn = array();
				if($this->can("view", $arr["obj_inst"]->id()))
				{
					$conn = $arr["obj_inst"]->connections_to(array(
						"from.class_id" => CL_RFP,
					));
				}
				if(count($conn) || $arr["request"]["rfp"])
				{
					$prop["disabled"] = 1;
				}
				if($arr["request"]["ver"])$prop["value"] = 1;
				if ($prop["value"] == 1)
				{
					$prop["onclick"] = "document.changeform.reason.value=prompt(\"Sisestage t&uuml;histuse p&otilde;hjus\");if (document.changeform.reason.value == \"\") {document.changeform.verified.checked=true; } else {submit_changeform(\"unverify\");}";
				}
				break;
			/*case "special_sum":
				$curr = $arr["obj_inst"]->prop("resource.currency");
				if(!(is_array($curr) && sizeof($curr) > 1))
				{
					$prop["value"] = $this->get_total_price(array(
						"reservation" => $arr["obj_inst"]->id(),
					));
				}
				else
				{
					$prop["value"] = null;
				}
				break;*/

			case "sum":
				if($is_lower_bron) return PROP_IGNORE;
				$prop["value"] = $this->_format_sum($arr["obj_inst"]);
				break;

			case "deadline":
				if($arr["obj_inst"]->prop("verified"))
				{
					return PROP_IGNORE;
				}
				if(!$prop["value"])
				{
					$prop["value"] = time() + 15*60;
				}
				if($is_lower_bron)
				{
					$prop["type"] = "text";
					$prop["value"] = $prop["value"] = date("d.m.Y h:i" , $prop["value"]);
				}
				break;
			case "client_arrived":
				$prop["options"] = array(0 => t("M&auml;rkimata"), 2 => t("Ei") , 1 => t("Jah"));
//				if(!$prop["value"])
//				{
//					$prop["value"] = 0;
//				}
				if($is_lower_bron)
				{
					$prop["type"] = "text";
					if(!$prop["value"])
					{
						return PROP_IGNORE;
					}
					$prop["value"] = $prop["value"] == 1 ? t("Jah") : t("Ei");
				}
				break;

			case "products_text":
				if($is_lower_bron) return PROP_IGNORE;
				$prop["value"] = $this->get_products_text($arr["obj_inst"]);
				break;

			case "people":
				if(is_oid($arr["obj_inst"]->prop("resource")))
				{
					$room = obj($arr["obj_inst"]->prop("resource"));
				}
				else
				{
					if(is_oid($arr["request"]["resource"]))
					{
						$room = obj($arr["request"]["resource"]);
					}
				}
				if(is_object($room))
				{
					$prop["options"] = array("") + $room->get_all_workers();
	/*				$professions = $room->prop("professions");
					if(is_array($professions) && sizeof($professions))
					{
						$ol = new object_list(array(
							"class_id" => CL_CRM_PERSON,
							"lang_id" => array(),
							"CL_CRM_PERSON.RELTYPE_RANK" => $professions,
						));
						$prop["options"] = array("") + $ol->names();
					}*/
				}
				break;
			case "inbetweener":
				if($is_lower_bron) return PROP_IGNORE;
				if(is_oid($arr["obj_inst"]->prop("resource")))
				{
					$room = obj($arr["obj_inst"]->prop("resource"));
				}
				else
				{
					if(is_oid($arr["request"]["resource"]))
					{
						$room = obj($arr["request"]["resource"]);
					}
				}
				if(is_object($room))
				{
					$prop["options"] = array("") + $room->get_all_sellers();
/*					$professions = $room->prop("seller_professions");
					if(is_array($professions) && sizeof($professions))
					{
						$ol = new object_list(array(
							"class_id" => CL_CRM_PERSON,
							"lang_id" => array(),
							"CL_CRM_PERSON.RELTYPE_RANK" => $professions,
						));
						$prop["options"] = array("") + $ol->names();
					}*/
				}
				break;

//			case "sum":
//				break;


			case "name":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					return PROP_IGNORE;
				}
				$prop["value"] = $this->get_correct_name($arr["obj_inst"]);
				$prop["type"] = "text";
				if($is_lower_bron)
				{
					$prop["value"].= "\n<br>".t("P&otilde;hibronn").": ".html::get_change_url($master_bron->id(),array() , $arr["obj_inst"]->prop("parent.name"));
				}
				break;

// 			case "products_discount":
// 				if(aw_global_get("uid") == "struktuur")arr($prop);
// 				break;


			case "customer":
				if($is_lower_bron)
				{
					if($this->can("view" , $prop["value"]))
					{
						$prop["type"] = "text";
						$prop["value"] = html::get_change_url($prop["value"],array() , $arr["obj_inst"]->prop("customer.name"));
						return PROP_OK;
					}
					return PROP_IGNORE;
				}
				if ($arr["request"]["set_cust"])
				{
					$prop["value"] = $arr["request"]["set_cust"];
					$arr["obj_inst"]->set_prop("customer", $arr["request"]["set_cust"]);
				}
				$prop["onchange"] = "window.location.href='".aw_url_change_var("set_cust", null)."&set_cust='+this.options[this.selectedIndex].value";
				break;
			case "project":
				if($is_lower_bron)
				{
					if($this->can("view" , $prop["value"]))
					{
						$prop["type"] = "text";
						$prop["value"] = html::get_change_url($prop["value"], array() , $arr["obj_inst"]->prop("project.name"));
						return PROP_OK;
					}
					return PROP_IGNORE;
				}
				$prop["autocomplete_source"] = $this->mk_my_orb("proj_autocomplete_source");
				$prop["autocomplete_params"] = array("project");
				//see selleks, et js lisamise juures saaks aru kas projekti prop on yldse kasutuses, et sealt asju otsida
				$this->has_project_prop = 1;
				break;
			case "paid":
			case "send_bill":
			case "time_closed":
				if($is_lower_bron) $prop["disabled"] = 1;
				break;
			case "closed_info":
				if($is_lower_bron)
				{
					if(!$prop["value"])
					{
						return PROP_IGNORE;
					}
					$prop["type"] = "text";
				}
				break;
			case "special_sum":
				$prices = $arr["obj_inst"]->get_special_sum();
				$prop["type"] = "text";
				$prop["value"] = "";
				$cs = $arr["obj_inst"]->get_room_currencies();
				foreach($cs as $id => $name)
				{
					$prop["value"].= $name." ".html::textbox(array(
						"name" => "special_sum[".$id."]",
						"value" => $prices[$id],
						"size" => "4",
					))."<br>";
				}

			case "special_discount":
			case "length":
				if($is_lower_bron)return PROP_IGNORE;
				break;
		};
		return $retval;
	}

	/**
		@attrib name=proj_autocomplete_source
		@param project optional
	**/
	function proj_autocomplete_source($arr)
	{
		$cl_json = get_instance("protocols/data/json");

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);
		$ol = new object_list(array(
			"class_id" => array(CL_PROJECT),
			"name" => iconv("UTF-8", aw_global_get("charset"), $arr["project"])."%",
			"lang_id" => array(),
			"site_id" => array(),
			"state" => new obj_predicate_not(PROJ_DONE)
		));
		$autocomplete_options = $ol->names();
             $autocomplete_options = $ol->names();
                foreach($autocomplete_options as $k => $v)
                {
                        $autocomplete_options[$k] = iconv(aw_global_get("charset"), "UTF-8", parse_obj_name($v));
                }
		header("Content-type: text/html; charset=utf-8");
		exit ($cl_json->encode($option_data));
	}

	function callback_generate_scripts($arr)
	{
		if($this->has_project_prop)
		{
			$check_url = $this->mk_my_orb("is_there_project", array("project" => " "));
				$script = "function aw_submit_handler() {
				url = '".$check_url."'+document.changeform.project_awAutoCompleteTextbox.value;
				el=aw_get_url_contents('".$check_url."'+document.changeform.project_awAutoCompleteTextbox.value);
				if(!(el>0))
				{
					return confirm('".t("Sellise nimega projekti ei ole veel andmebaasis, kas soovite uut lisada?")."')
				}
				return false;
			}";
		}
		if($arr["request"]["saved"])
		{
			$script .= "if (window.opener) {window.opener.location.reload();}";
		}
		return $script;
	}

	function callback_get_cfgmanager($arr)
	{
		if ($arr["request"]["action"] == "change")
		{
			$o = obj($arr["request"]["id"]);
			$conn = $o->connections_to(array(
				"from.class_id" => CL_RFP,
			));
			if(count($conn))
			{
				$rfp = 1;
			}
		}
		elseif($this->can("view", $arr["request"]["rfp"]))
		{
			$rfp = 1;
		}
		if($rfp)
		{
			$rfpm = get_instance(CL_RFP_MANAGER);
			$rfpmid = $rfpm->get_sysdefault();
			if($this->can("view", $rfpmid))
			{
				$rfpmo = obj($rfpmid);
				if($rfpmo->prop("rv_cfgmanager"))
				{
					return $rfpmo->prop("rv_cfgmanager");
				}
			}
		}
	}

	/**
		@attrib name=is_there_project
		@param project optional
	**/
	function is_there_project($arr)
	{
		$arr["project"] = substr($arr["project"],1);
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"lang_id" => array(),
			"site_id" => array(),
			"name" => $arr["project"],
		));
		$res = sizeof($ol->ids());
		header("Content-type: text/html; charset=utf-8");
		exit ($res."");
	}

	function set_property($arr = array())
	{
		$room_inst = get_instance(CL_ROOM);
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		// get resource, then get settings from that and verify req fields
		if ($this->can("view", $arr["request"]["resource"]) && !$arr["request"]["time_closed"])
		{
			$reso = obj($arr["request"]["resource"]);
			$resi = $reso->instance();
			$sett = $resi->get_settings_for_room($reso);
			$reqf = $sett->meta("bron_req_fields");
			if (is_array($reqf) && count($reqf))
			{
				if ($reqf[$prop["name"]]["req"] == 1 && $prop["value"] == "")
				{
					$prop["error"] = sprintf(t("V&auml;li %s peab olema t&auml;idetud!"), $prop["caption"]);
					return PROP_FATAL_ERROR;
				}
			}
		}
		switch($prop["name"])
		{
			case "verified":
				if(!$arr["new"])
				{
					$conn = $arr["obj_inst"]->connections_to(array(
						"from.class_id" => CL_RFP,
					));
					if(count($conn))
					{
						return PROP_IGNORE;
					}
				}
				break;

			case "other_rooms":
				$rooms = $arr["obj_inst"]->get_other_bron_rooms();//ruumid mis on hetkel lisaks broneeritud
				$r = array();
				$error_rooms = array();
				foreach($rooms as $bron => $room)
				{
					$r[$room] = $bron;
				}
				$start = mktime($arr["request"]["start1"]["hour"], $arr["request"]["start1"]["minute"], 0, $arr["request"]["start1"]["month"], $arr["request"]["start1"]["day"], $arr["request"]["start1"]["year"]);
				$end = mktime($arr["request"]["end"]["hour"], $arr["request"]["end"]["minute"], 0, $arr["request"]["end"]["month"], $arr["request"]["end"]["day"], $arr["request"]["end"]["year"]);

				foreach($prop["value"] as $key => $room)
				{
					if($this->can("view" , $room))
					{
						$oro = obj($room);
						if(!$oro->is_available(array(
							"start" => $start,
							"end" => $end,
							"ignore_booking" => $r[$room] ? $r[$room] : null,
						)))
						{
							$error_rooms[] = $oro->name();
							unset($prop["value"][$key]);
						}
					}
				}
				if(sizeof($error_rooms))
				{
					$prop["error"] = t("Sellisele ajale ei saa broneerida ruume:")." ".join("," , $error_rooms);
					return PROP_FATAL_ERROR;
				}
				if($this->can("view" , $arr["obj_inst"]->prop("resource")))
				{
					$room = obj($arr["obj_inst"]->prop("resource"));
					$options = $room->get_other_rooms_selection();
					if(!(is_array($options) && sizeof($options)))
					{
						return PROP_IGNORE;
					}
					$arr["obj_inst"]->make_slave_brons($prop["value"]);
				}
				else
				{
					return PROP_IGNORE;
				}
				break;
			case "resources_price":
			case "resources_discount":
				$retval =  PROP_IGNORE;
				break;
			case "cp_ln":
			case "cp_fn":
			case "cp_phone":
			case "cp_email":
				$fn = "_get_".$data["name"];
				return $this->$fn($arr);
				break;
			case "products_tbl":
				$this->set_products_info($arr["obj_inst"]->id(), $arr["request"]);
				break;

			case "time_closed":
				if ($prop["value"]  && $arr["request"]["closed_info"] == "" && !$arr["obj_inst"]->get_room_setting("dont_ask_close_reason"))
				{
					$prop["error"] = t("Sulgemise p&otilde;hjus peab olema t&auml;idetud!");
					return PROP_FATAL_ERROR;
				}
				break;
			case "end":
				$s = $arr["request"]["start1"];
				$e = $arr["request"]["end"];
				$room_obj = obj($arr["request"]["resource"]);

				$filter = array(
//					"room" => $arr["request"]["resource"],
					"start" => mktime($s["hour"],$s["minute"],0,$s["month"],$s["day"],$s["year"]),
					"end" => (mktime($e["hour"],$e["minute"],0,$e["month"],$e["day"],$e["year"]) + $room_obj->prop("buffer_after")*$room_obj->prop("buffer_after_unit")),
					"ignore_booking" => $arr["obj_inst"]->id(),
				);
				if($arr["obj_inst"]->prop("type") || $arr["request"]["type"])
				{
					$filter["type"] = $arr["obj_inst"]->prop("type") ? $arr["obj_inst"]->prop("type") :$arr["request"]["type"];
				}
				if(!$room_obj->is_available($filter))
//				if(!$room_obj->prop("allow_multiple") &&  !$room_inst->check_if_available($filter))
				{
					$last_bron = $GLOBALS["last_bron_id"];
					unset($GLOBALS["last_bron_id"]);
					$prop["error"] = t("Sellisele ajale ei saa antud ruumi broneerida");
					if($this->can("view" , $last_bron))
					{
						$last = obj($last_bron);
						$prop["error"].= ":<br>".$last->name();//." - ".$last->prop("customer.name")." :".date("H:i" , $last->prop("start1"));
					}
					return PROP_FATAL_ERROR;
				}
				if(mktime($s["hour"],$s["minute"],0,$s["month"],$s["day"],$s["year"]) >= mktime($e["hour"],$e["minute"],0,$e["month"],$e["day"],$e["year"]))
				{
					$prop["error"] = t("Broneeringu l&ouml;pp peab olema hiljem kui algus");
					return PROP_FATAL_ERROR;
				}
				if(!$arr["new"])
				{
					$rdata = $this->get_resources_data($arr["obj_inst"]->id());
					if(is_array($rdata) && count($rdata))
					{
						$s_o = $arr["obj_inst"]->prop("start1");
						$e_o = $arr["obj_inst"]->prop("end");
						foreach($rdata as $res => $data)
						{
							if(date("H.i",$s_o) == date("H.i", $data["start1"]) && date("H.i",$e_o) == date("H.i", $data["end"]))
							{
								$rdata[$res]["start1"] = date_edit::get_timestamp($s);
								$rdata[$res]["end"] = date_edit::get_timestamp($e);
							}
						}
						$this->set_resources_data(array(
							"reservation" => $arr["obj_inst"]->id(),
							"resources_info" => $rdata,
						));
					}
				}
				break;

			case "special_sum":
				$arr["obj_inst"]->set_special_sum($prop["value"]);
//				$this->set_total_price(array(
//					"reservation" => $arr["obj_inst"]->id(),
//					"sum" => $prop["value"],
//				));
				break;
			case "project":
				if(!is_oid($prop["value"]))
				{
					if(is_oid($arr["request"]["project_awAutoCompleteTextbox"]) && $this->can("view" , $arr["request"]["project_awAutoCompleteTextbox"]))
					{
						$prop["value"] = $arr["request"]["project_awAutoCompleteTextbox"];
					}
					else
					{
						$ol = new object_list(array(
							"name" => $arr["request"]["project_awAutoCompleteTextbox"],
							"class_id" => array(CL_PROJECT),
							"lang_id" => array(),
						));
						$cust_obj = $ol->begin();
						if(is_object($cust_obj))
						{
							$prop["value"] = $cust_obj->id();
						}
						else
						{
							$prop["value"] = $arr["obj_inst"]->set_new_project($arr["request"]["project_awAutoCompleteTextbox"]);
						}
					}
				}
		}
		return $retval;
	}

	function calc_obj_name($o)
	{
		$o->set_name(sprintf(t("%s: %s / %s-%s %s"),
	                $o->prop("customer.name"),
                        date("d.m.Y", $o->prop("start1")),
                        date("H:i", $o->prop("start1")),
                        date("H:i", $o->prop("end")),
                        $o->prop("resource.name")
		));
	}

	function callback_pre_save($arr)
	{
		$this->calc_obj_name($arr["obj_inst"]);
		if ($arr["request"]["length"] > 0)
		{
			$mul = 3600;
			if ($this->can("view", $arr["obj_inst"]->prop("resource")))
			{
				$room = obj($arr["obj_inst"]->prop("resource"));
				if ($room->prop("time_unit") == 1)
				{
					$mul = 60;
				}
			}

			$arr["obj_inst"]->set_prop("end", $arr["obj_inst"]->prop("start1")+$arr["request"]["length"]*$mul);
		}
	}
/*	function set_sum($arr)
	{
		extract($arr);
		$this_obj = obj($id);
		if(!is_oid($resource))
		{
			return 0;
		}
		$room = obj($resource);

		$prices = $room->connections_from(array(
			"class_id" => CL_ROOM_PRICE,
			"type" => "RELTYPE_ROOM_PRICE",
		));
		foreach($prices as $conn)
		{
			$price = $conn->to();
			if(($price->prop("date_from") < $this_obj->prop("start1")) && $price->prop("date_to") > $this_obj->prop("end"))
			{
//				if()
//				{
					arr($price->prop("weekdays"));
//				}
			}

		}

//		if($people_count <= $room->prop("normal_capacity"))
//		{
//			$sum = $people_count *
//		}
		$sum = 0;
		$this_obj->set_prop("sum" , $sum);
		$this_obj->save();
		return $sum;
	}*/

	function callback_mod_retval(&$arr)
	{
		$arr["args"]["saved"] = 1;
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["post_ru"] = post_ru();
		$arr["reason"] = " ";
		$arr["add_p"] = "0";
		if(!empty($request["calendar"]))
		{
			$arr["calendar"] = $request["calendar"];
		}

		if(empty($arr["id"]))
		{
			$arr["resource"] = $request["resource"];
		}

		// rfp crap
		if(!empty($request["rfp"]))
		{
			$arr["rfp"] = $request["rfp"];
		}

		if(!empty($request["rfp_reltype"]))
		{
			$arr["rfp_reltype"] = $request["rfp_reltype"];
		}

		if(!empty($request["rfp_organisation"]))
		{
			$arr["rfp_organisation"] = $request["rfp_organisation"];
		}

		if(!empty($request["type"]))
		{
			$arr["type"] = $request["type"];
		}
	}

	function callback_post_save($arr)
	{
		if($arr["new"] && is_oid($arr["request"]["type"]))
		{
			$arr["obj_inst"]->set_prop("type" ,$arr["request"]["type"]);
			$arr["obj_inst"]->save();
		}
		if($arr["new"]==1 && is_oid($arr["request"]["calendar"]) && $this->can("view" , $arr["request"]["calendar"]))
		{
			$cal = obj($arr["request"]["calendar"]);
			$cal->connect(array(
				"to" => $arr["obj_inst"]->id(),
				"reltype" => "RELTYPE_EVENT"
			));
		}
		if($arr["new"] && is_oid($arr["request"]["resource"]) && $this->can("view" , $arr["request"]["resource"]))
		{
			$arr["obj_inst"]->set_prop("resource" ,$arr["request"]["resource"]);
			$arr["obj_inst"]->save();
		}

		$ps = get_instance("vcl/popup_search");
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["add_p"], 1 /* RELTYPE_CUSTOMER */);


		if($arr["new"] && is_oid($arr["request"]["rfp"]))
		{
			$rfp = obj($arr["request"]["rfp"]);
			$rfp->connect(array(
				"type" => $arr["request"]["rfp_reltype"]?$arr["request"]["rfp_reltype"]:"RELTYPE_RESERVATION",
				"to" => $arr["obj_inst"]->id(),
			));

			if($arr["request"]["type"] == "food")
			{
				$arr["obj_inst"]->set_prop("type" , "food");
			}

			$vf = 0;
			if($rfp->prop("confirmed") == 2)
			{
				$vf = 1;
			}
			$arr["obj_inst"]->set_prop("verified", $vf);

			if($org = $arr["request"]["rfp_organisation"])
			{
				$org = obj($org);
				$arr["obj_inst"]->connect(array(
					"type" => "RELTYPE_CUSTOMER",
					"to" => $org->id(),
				));
				$arr["obj_inst"]->set_prop("customer", $org->id());
				$arr["obj_inst"]->set_correct_name();
			}
			$arr["obj_inst"]->save();
		}
		// well, this here makes a new person when rfp class makes a new reservation and personal data is provided also
		/*
		if($arr["new"])
		{
			$arr["request"]["new"] = array(
				"firstname" => $arr["request"]["person_fname"],
				"name" => $arr["request"]["person_lname"],
				"email" => $arr["request"]["person_email"],
				"phone" => $arr["request"]["person_phone"],
			);
			$this->_set_ppl($arr);
		}
		 */


		if ($arr["request"]["sbt_close"] != "")
		{
			$d = "<script language='javascript'>if (window.opener) window.opener.location.href='".$arr["request"]["return_url"]."'; window.close();</script>";
			die($d);
		}
	}

	////////////////////////////////////
	// the next functions are optional - delete them if not needed
	////////////////////////////////////

	/** this will get called whenever this object needs to get shown in the website, via alias in document **/
	function show($arr)
	{
		$ob = new object($arr["id"]);
		$this->read_template("show.tpl");



		$this->vars(array(
			"name" => $ob->prop("name"),
			"verified" => ($ob->prop("verified") ? t("Kinnitatud") : t("Kinnitamata")),
			"time_str" => $this->get_time_str(array(
				"start" => $ob->prop("start1"),
				"end" => $ob->prop("end"),
			)),
		));
		return $this->parse();
	}

	function get_time_str($arr)
	{
		$room_inst = get_instance(CL_ROOM);
		extract($arr);
		$res = "";
		$res.= $room_inst->weekdays[(int)date("w" , $arr["start"])];
		$res.= ", ";
		$res.= date("d.m.Y" , $arr["start"]);
		$res.= ", ";
		$res.= date("H:i" , $arr["start"]);
		$res.= " - ";
		$res.= date("H:i" , $arr["end"]);
		return $res;
	}

	function request_execute ($this_object)
	{
		return $this->show (array (
			"this" => $this_object,
		));
	}


//-- methods --//

	/**
		@param resource
		@param start
		@param end
		@comment
			basically what this does, is checks if this reservation can use given resource object in given time perion, and if can how many isntances of it
		@returns
			returns number instances that this resource can be used in this time period
	**/
	function resource_availability($arr)
	{
		$res = $arr["resource"];
		if(!is_oid($res))
		{
			arr("ehh");
			return 0;
		}
		$list = new object_list(array(
			"class_id" => CL_RESERVATION,
			"start1" => new obj_predicate_compare(OBJ_COMP_LESS, $arr["end"]),
			"end" => new obj_predicate_compare(OBJ_COMP_GREATER, $arr["start"]),
		));
		$total_usage = 0;
		foreach($list->arr() as $oid => $obj)
		{
			$inf = $this->resource_info($oid);
			foreach($inf as $resource => $count)
			{
				$total_usage = ($resource == $res)?($total_usage+$count):$total_usage;
			}
		}
		$res = obj($res);
		$total_count = count($res->prop("thread_data"));
		return ($total_count-$total_usage);
	}

	function resource_info($reservation)
	{
		if(!is_oid($reservation))
		{
			return false;
		}
		$reservation = obj($reservation);
		return $reservation->meta("resource_info");
	}

	/**
		@param reservation
			reservation object oid
		@param info
			array(
				resource object oid => number of resource instances used
			)
	**/
	function set_resource_info($reservation, $info)
	{
		if(!is_oid($reservation))
		{
			false;
		}
		$reservation = obj($reservation);
		$reservation->set_meta("resource_info", $info);
		$reservation->save();
		return true;
	}

	function _get_resources_tbl($arr)
	{
		$room = $arr["obj_inst"]->prop("resource");
		$room = $this->can("view", $room)?obj($room):false;
		$currency = $room?$room->prop("currency"):array();

		$t = $arr["prop"]["vcl_inst"];

		$t->define_header($arr["obj_inst"]->name());
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "price",
			"align" => "center",
			"caption" => t("Ressursi &uuml;hiku hind"),
		));

		foreach($currency as $cur)
		{
			if($this->can("view", $cur))
			{
				$cur = obj($cur);
				$t->define_field(array(
					"name" => "price_".$cur->id(),
					"parent" => "price",
					"caption" => $cur->name(),
					"align" => "center",
					"chgbgcolor" => "split",
				));
			}
		}
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Soodus"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
			"align" => "center",
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Aeg"),
			"chgbgcolor" => "split",
			"align" => "center",
		));
		$room = obj($arr["obj_inst"]->prop("resource"));
		$room_inst = $room->instance();
		$rdata = $this->get_resources_data($arr["obj_inst"]->id());
		$rss = $room_inst->get_room_resources($room->id());
		$rfp = get_instance(CL_RFP);
		uasort($rss, array($rfp, "__sort_resources"));
		foreach($rss as $res_obj)
		{
			$res = $res_obj->id();
			$data = array();
			$data = array(
				"date" => $rfp->gen_time_form(array(
					"varname" => "resources_info[".$res."]",
					"start1" => ($rdata[$res]["start1"])?$rdata[$res]["start1"]:$arr["obj_inst"]->prop("start1"),
					"end" => ($rdata[$res]["end"])?$rdata[$res]["end"]:$arr["obj_inst"]->prop("end"),
				)),
				"name" => $res_obj->name(),
				"amount" => html::textbox(array(
					//"name" => "cnt[".$res."]",
					"name" => "resources_info[".$res."][count]",
					"value" => $rdata[$res]["count"],
					"size" => 5,
				)),
				"discount" => html::textbox(array(
					//"name" => "discount[".$res."]",
					"name" => "resources_info[".$res."][discount]",
					"value" => $rdata[$res]["discount"],
					"size" => 5,
				)),
				"comment" => html::textarea(array(
					"name" => "resources_info[".$res."][comment]",
					"value" => $rdata[$res]["comment"],
					"cols" => 20,
					"rows" => 3,
				)),
				"res_ord" => $res_obj->ord(),
			);
			foreach($currency as $cur)
			{
				if($this->can("view", $cur))
				{
					$price  = $rdata[$res]["prices"][$cur];
					if((!$price or $price <= 0) and is_array($arr["request"]["resource_default_prices"]))
					{
						$price = $arr["request"]["resource_default_prices"][$arr["obj_inst"]->prop("resource")][$res][$cur];
					}
					$data["price_".$cur] = html::textbox(array(
						"name" => "resources_info[".$res."][prices][".$cur."]",
						"value" => $price, //$rdata[$res]["prices"][$cur],
						"size" => 5,
					));
				}
			}
			$t->define_data($data);
		}
		// so, so lets set the total price/discount thingies

		$t->define_data(array(
			"split" => "#CCCCCC",
		));

		$sum_data = array(
			"name" => t("Kogusumma"),
		);

		$res_price_data = $this->get_resources_price($arr["obj_inst"]->id());
		foreach($currency as $cur)
		{
			if(!$this->can("view", $cur))
			{
				continue;
			}
			$sum_data["price_".$cur] = html::textbox(array(
				"name" => "resources_total_price[".$cur."]",
				"value" => $res_price_data[$cur],
				"size" => 5,
			));
		}
		$t->define_data($sum_data);

		$res_discount = $this->get_resources_discount($arr["obj_inst"]->id());
		$t->define_data(array(
			"name" => t("Kogusoodus (%)"),
			"discount" => html::textbox(array(
				"name" => "resources_total_discount",
				"value" => $res_discount,
				"size" => 5,
			)),
		));

		$t->set_sortable(false);
	}


	function _set_resources_tbl($arr)
	{
		// new
		$this->set_resources_data(array(
			"reservation" => $arr["request"]["id"],
			"resources_info" => $arr["request"]["resources_info"],
		));
		$this->set_resources_price(array(
			"reservation" => $arr["request"]["id"],
			"prices" => $arr["request"]["resources_total_price"]
		));
		$this->set_resources_discount(array(
			"reservation" => $arr["request"]["id"],
			"discount" => $arr["request"]["resources_total_discount"],
		));

		// old
		// well, this old function should point to new as well.. or smth
		//$this->set_resource_info($arr["obj_inst"]->id(), $arr["request"]["cnt"]);
	}

	function get_room_products($room)
	{
		$ol = new object_list();
		if(is_oid($room))
		{
			$room = obj($room);
		}
		if(is_object($room))
		{
			$room_instance = get_instance(CL_ROOM);
			$ol = $room_instance->get_prod_list($room);
		}
		return $ol;
	}

	function _get_products_order_view($arr)
	{//arr($arr);
		extract($arr);
		$shop_order_center = get_instance(CL_SHOP_ORDER_CENTER);
		$wh = get_instance(CL_SHOP_WAREHOUSE);
		$room_instance = get_instance(CL_ROOM);
		if(is_oid($room) && $this->can("view" , $room))
		{
			$room_obj = obj($room);
			$warehouse = $room_obj->prop("warehouse");
			if(is_oid($warehouse) && $this->can("view" , $warehouse))
			{
				$w_obj = obj($warehouse);
				$w_cnf = obj($w_obj->prop("conf"));
				if(is_oid($w_obj->prop("order_center")) && $this->can("view" , $w_obj->prop("order_center")))
				{
					$soc = obj($w_obj->prop("order_center"));
					$pl_ol =  $room_instance->get_active_items($room);
					$pl = $pl_ol->arr();

					//peksab need v2lja mis ruumi juures aktiivseks pole l2inud
					$shop_order_center->do_sort_packet_list($pl, $soc->meta("itemsorts"), $soc->prop("grouping"));

					// get the template for products for this folder
					$layout = $shop_order_center->get_prod_layout_for_folder($soc, $room_obj->prop("resources_fld"));

					// get the table layout for this folder
					$t_layout = $shop_order_center->get_prod_table_layout_for_folder($soc, $room_obj->prop("resources_fld"));
					$shop_order_center->web_discount = $room_instance->get_prod_discount(array("room" =>  $room_obj->id()));
					$html .= $shop_order_center->do_draw_prods_with_layout(array(
						"t_layout" => $t_layout,
						"layout" => $layout,
						"pl" =>  $pl,
						"soc" => $soc,
					));
					return $html;
				}
			}
		}
		$this->_get_products_tbl(array(
			"prop" => array("vcl_inst" => $arr["prop"]["vcl_inst"]),
			"web" => $arr["web"],
			"room" => $arr["room"],
		));
		return 0;
	}

	function set_products_info($oid, $arr)
	{
		if($this->can("view", $oid))
 		{
			$o = obj($oid);
			$conn = $o->connections_to(array(
				"from.class_id" => CL_RFP,
				"type" => "RELTYPE_RESERVATION",
			));
			if(!count($conn))
			{
				$conn = $o->connections_to(array(
					"from.class_id" => CL_RFP,
					"type" => "RELTYPE_CATERING_RESERVATION",
				));
			}
			if(count($conn))
			{
				foreach($conn as $c)
				{
					$rfpo = $c->from();
					$prods = $rfpo->meta("prods");
					foreach($arr["amount"] as $id => $amt)
					{
						if(!$this->can("view", $id))
						{
							continue;
						}
						$prods[$id.".".$arr["id"]]["amount"] = $amt;
						$prods[$id.".".$arr["id"]]["discount"] = $arr["change_discount"][$id];
						if(!$prods[$id.".".$arr["id"]]["price"])
						{
							$prod_price = $this->get_product_price(array("product" => $id, "reservation" => $oid));
							$prods[$id.".".$arr["id"]]["price"] = $this->_get_admin_price_view(obj($id), $prod_price);
						}
						$prods[$id.".".$arr["id"]]["sum"] = number_format($prods[$id.".".$arr["id"]]["price"] * $amt * ((100 - $arr["change_discount"][$id]) / 100), 2);
						$prods[$id.".".$arr["id"]]["start1"] = $o->prop("start1");
						$prods[$id.".".$arr["id"]]["end"] = $o->prop("end");
					}
					$rfpo->set_meta("prods", $prods);
					$rfpo->save();
				}
			}
			foreach($arr["change_price"] as $key => $val)
			{
				$this->set_product_price(array(
					"reservation" => $o,
					"product" => $key,
					"sum" => $val,
				));
			}
			$this->set_products_price(array(
				"reservation" => $o,
				"sum" => $arr["final_sum"],
			));
			$this->set_product_discount(array(
				"reservation" => $o,
				"products" => $arr["change_discount"],
			));
			$o->set_meta("amount", $arr["amount"]);
			$this->set_products_discount(array(
				"reservation" => $o->id(),
				"discount" => $arr["discount"],
			));

//			$o->set_meta("prod_discount", $arr["discount"]);
			$o->save();
			return true;
		}
		return false;
	}

	function _get_products_tbl($arr)
	{
		if(!$arr["web"])
		{
			$is_admin = 1;
		}
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "picture",
			"caption" => t("&nbsp"),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind")
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
		));
		if($is_admin)$t->define_field(array(
			"name" => "discount",
			"caption" => t("Soodus"),
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa")
		));
		//kui veebipoolne
		if($arr["web"])
		{
			$prod_list = $this->get_room_products($arr["room"]);
			$amount = $arr["obj_inst"]->$_SESSION["room_reservation"]["products"];
		}
		else
		{
			$prod_list = $this->get_room_products($arr["obj_inst"]->prop("resource"));
			$amount = $arr["obj_inst"]->meta("amount");
			$discount = $this->get_product_discount($arr["obj_inst"]->id());
		}
		if($this->can("view" , $arr["room"]))
		{
			$room = obj($arr["room"]);
		}
		else
		{
			$room = obj($arr["obj_inst"]->prop("resource"));
		}

		$warehouse = obj($room->prop("warehouse"));
		if(is_oid($warehouse->prop("conf")))
		{
			$conf = obj($warehouse->prop("conf"));
			if($conf->prop("sell_prods"))
			{
				$sell_products = 1;
			}
		}

		// get rfp currency if exists
		$mgri = new rfp_manager();
		$mgrid = $mgri->get_sysdefault();
		$currency = 0;
		if ($mgrid)
		{
			try
			{
				$mgro = obj($mgrid);
				$currency = $mgro->prop("default_currency");
			}
			catch (Exception $e)
			{
			}
		}

		if(is_object($room))
		{
			$ri = $room->instance();
			$prod_data = $ri->get_prod_data_for_room($room);
		}
		$image_inst = get_instance(CL_IMAGE);
		$parent = 0;
		foreach($prod_list->arr() as $prod)
		{
			$image = "";
			if(is_object($prod->get_first_obj_by_reltype(array("type" => "RELTYPE_IMAGE"))))
			{
				$pic = $prod->get_first_obj_by_reltype(array("type" => "RELTYPE_IMAGE"));
				if(is_object($pic))
				{
					$image = $image_inst->make_img_tag_wl($pic->id());
				}
			}
			$po = obj($prod->parent());
			if($po->id() != $parent)
			{
				$parent = $po->id();
				$t->define_data(array(
					"picture" => "<h4><b>".$po->name()."<b></h4>",
					"name" => "",
					"amount" => "",
				));
			}

			if($sell_products)
			{
				$prod_price = $this->get_product_price(array("product" => $prod->id(), "reservation" => $arr["obj_inst"], "curr" => $currency));
				$prod_sum = $prod_price * $amount[$prod->id()];
				$prod_sum = $prod_sum - ($prod_sum * $discount[$prod->id()])/100;
				$t->define_data(array(
					"picture" => $image,
					"name" => "<b>".$prod->name()."<b> <i>".$prod->comment()."</i>",
					"amount" =>  html::textbox(array(
						"name"=>'amount['.$prod->id().']',
						"value" => $amount[$prod->id()],
						"size" => 5,
						"onChange" => "el=document.getElementById('pr".$prod->id()."');el.innerHTML=this.value*".$prod_price.";els=document.getElementsByTagName('span');tots = 0;for(i=0; i < els.length; i++) { el=els[i]; if (el.id.indexOf('pr') == 0) { tots += parseInt(el.innerHTML);}} te=document.getElementById('total');te.innerHTML=tots;disc=parseInt(document.changeform.discount.value);disc_el=document.getElementById('disc_val');if(disc>0){disc_el.innerHTML=(tots*(disc/100));} sum_val = document.getElementById('sum_val');if (disc > 0) {sum_val.innerHTML=(tots-(tots*(disc/100)));} else { sum_val.innerHTML=tots; } "
					)),
					"discount" =>  html::textbox(array(
						"name"=>'change_discount['.$prod->id().']',
						"value" => $discount[$prod->id()],
						"size" => 2,
					))." %",

					"price" => $is_admin ? $this->_get_admin_price_view($prod,$prod_price):number_format($prod_price, 2),
					"sum" => "<span id='pr".$prod->id()."'>".number_format($prod_sum, 2)."</span>",
					//ei julge praegu kylge panna
/*					"sum" =>  html::textbox(array(
						"name"=>'price['.$prod->id().']',
						"value" => number_format((!$arr["web"])? $this->get_product_price(array("product" => $prod->id(), "reservation" => $arr["obj_inst"])): $prod->prop("price"), 2),
						"size" => 5,
	//					"onChange" => "el=document.getElementById('pr".$prod->id()."');el.innerHTML=this.value*".$prod->prop("price").";els=document.getElementsByTagName('span');tots = 0;for(i=0; i < els.length; i++) { el=els[i]; if (el.id.indexOf('pr') == 0) { tots += parseInt(el.innerHTML);}} te=document.getElementById('total');te.innerHTML=tots;disc=parseInt(document.changeform.discount.value);disc_el=document.getElementById('disc_val');if(disc>0){disc_el.innerHTML=(tots*(disc/100));} sum_val = document.getElementById('sum_val');if (disc > 0) {sum_val.innerHTML=(tots-(tots*(disc/100)));} else { sum_val.innerHTML=tots; } "
					)),
*/
					"parent" => $po->name()
				));
				$sum += $prod_sum;
			}
			else
			{
				$t->define_data(array(
					"picture" => $image,
					"name" => "<b>".$prod->name()."<b>",
	//				"amount" =>  html::textbox(array(
	//					"name"=>'amount['.$prod->id().']',
	//					"value" => $amount[$prod->id()],
	//				)),
					"parent" => $po->name()
				));
				$packages = $prod->connections_from(array(
					"type" => "RELTYPE_PACKAGING",
				));
				foreach($packages as $conn)
				{
					$package = $conn->to();
					if(!$prod_data[$package->id()]["active"])
					{
						continue;
					}
					$image = "";
					if(is_object($package->get_first_obj_by_reltype(array("type" => "RELTYPE_IMAGE"))))
					{
						$pic = $package->get_first_obj_by_reltype(array("type" => "RELTYPE_IMAGE"));
						if(is_object($pic))
						{
							$image = $image_inst->make_img_tag_wl($pic->id());
						}
					}
					$t->define_data(array(
						"picture" => $image,
						"name" => "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$package->name(),
						"amount" =>  html::textbox(array(
							"name"=>'amount['.$package->id().']',
							"value" => $amount[$package->id()],
						)),
					));
				}
			}
		}
	//	$t->set_default_sortby("name");
	//	$t->sort_by(array("rgroupby" => array("parent" => "parent")));
		$t->set_sortable(false);

		//if(aw_global_get("uid") == "struktuur"){arr($total_price_set); arr($discount[$prod->id()]);}

		$total_price_set = $this->get_products_price(array("reservation" => $arr["obj_inst"]));
		$t->define_data(array(
			"name" => t("Kogusumma"),
			"sum" => "<span id=total>".number_format($sum, 2)."</span>",
			"amount" => html::textbox(array(
				"name"=>"final_sum",
				"value" => $total_price_set ? number_format($total_price_set, 2) : "",
				"size" => 5,
			)),
		));

		$set_doscount = $arr["obj_inst"] ? $this->get_products_discount($arr["obj_inst"]->id()) : 0;

		if($total_price_set) $sum = $total_price_set;
		$disc = $sum * ($set_doscount / 100.0);
		$t->define_data(array(
			"name" => t("Allahindlus (%)"),
			"amount" => html::textbox(array(
				"name" => "discount",
				"value" => $set_doscount,
				"size" => 4,
				"onChange" => "els=document.getElementsByTagName('span');tots = 0;for(i=0; i < els.length; i++) { el=els[i]; if (el.id.indexOf('pr') == 0) { tots += parseInt(el.innerHTML);}} te=document.getElementById('total');te.innerHTML=tots;disc=parseInt(document.changeform.discount.value);disc_el=document.getElementById('disc_val');if(disc>0){disc_el.innerHTML=(tots*(disc/100));} sum_val = document.getElementById('sum_val');if (disc > 0) {sum_val.innerHTML=(tots-(tots*(disc/100)));} else { sum_val.innerHTML=tots; }"
			)),
			"sum" => "<span id='disc_val'>".number_format($disc, 2)."</span>"
		));

		$t->define_data(array(
			"name" => t("<b>Summa</b>"),
			"sum" => "<span id='sum_val'>".number_format($sum-$disc, 2)."</span>"
		));

		return $t;
	}

	private function _sort_by_time($a, $b)
	{
		return $a->prop("start1") - $b->prop("start1");
	}

	function _get_prices_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		if($arr["obj_inst"])
		{
			$rvs[] = $arr["obj_inst"];
		}
		elseif(count($arr["ids"]))
		{
			$t->define_field(array(
				"name" => "name",
				"caption" => t("Reserveering"),
				"chgbgcolor" => "split",
			));
			$rvs = array();
			foreach($arr["ids"] as $id)
			{
				$rvs[] = obj($id);
			}
			$t->define_field(array(
				"name" => "tables",
				"caption" => t("Laudade asetus"),
				"chgbgcolor" => "split",
			));
			$rfpm = get_instance(CL_RFP_MANAGER);
			$rmobj = obj($rfpm->get_sysdefault());
			if($this->can("view", $rmobj->prop("table_form_folder")))
			{
				$ol = new object_list(array(
					"class_id" => CL_META,
					"parent" => $rmobj->prop("table_form_folder")
				));
				$tableoptions = array(0=>t("--vali--"));
				foreach($ol->arr() as $obj)
				{
					$tableoptions[$obj->id()] = $obj->name();
				}
			}
		}
		if($arr["request"]["do_room_separators"])
		{
			$t->set_rgroupby(array(
				"room" => "room",
			));
		}
		usort($rvs, array($this, "_sort_by_time"));
		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Soodustus %"),
			"chgbgcolor" => "split",
		));
		$t->define_field(array(
			"name" => "custom",
			"caption" => t("Kokkuleppehind"),
			"chgbgcolor" => "split",
		));
		if($arr["request"]["define_chooser"])
		{
			$t->define_chooser(array(
				"name" => "sel",
				"field" => "reservation",
			));
		}
		$t->set_sortable(false);
		$setcur = array();
		$totals = 0;
		if($arr["request"]["use_rfp_minmax_hours_pricecalc"])
		{
			$rfpi = get_instance(CL_RFP);
			$rfpmo = get_instance(CL_RFP_MANAGER);
			$rfpmo = obj($rfpmo->get_sysdefault());
		}
		foreach($rvs as $o)
		{
			$d = array();
			$room_instance = get_instance(CL_ROOM);
			$times = array(
				"start1" => $o->prop("start1"),
				"end" => $o->prop("end"),
			);
			if($arr["request"]["use_rfp_minmax_hours_pricecalc"])
			{
				$times = $rfpi->alter_reservation_time_include_extra_min_hours($o, $rfpmo);
			}
			$sum = $room_instance->cal_room_price(array(
				"room" => $o->prop("resource"),
				"start" => $times["start1"],
				"end" => $times["end"],
				"people" => $o->prop("people_count"),
				"products" => $o->meta("amount"),
				"bron" => $o,
				"detailed_info" => true
			));
			if($arr["request"]["use_rfp_minmax_hours_pricecalc"])
			{
				$sum["room_price"] = $rfpi->alter_reservation_price_include_extra_max_hours($o, $rfpmo, $sum["room_price"]);
			}


			$total = 0;
			foreach($sum["room_price"] as $cur => $price)
			{
				if(!$setcur[$cur] && (!count($arr["ids"]) || ($arr["rfpo_inst"] && $arr["rfpo_inst"]->prop("default_currency") == $cur)))
				{
					$co = obj($cur);
					$t->define_field(array(
						"name" => "price".$cur,
						"caption" => $co->name(),
						"align" => "right",
						"chgbgcolor" => "split",
					));
					$setcur[$cur] = $cur;
				}
			}
			if(!$setcur["total"])
			{
				$t->define_field(array(
					"name" => "total",
					"caption" => t("Hind"),
					"chgbgcolor" => "split",
				));
				$setcur["total"] = 1;
			}
			foreach($sum["room_price"] as $cur => $price)
			{
				$price = number_format($price, 2);
				$d["price".$cur] = $price;

				$total += ($arr["request"]["default_currency"] == $cur)?str_replace(",","",$price):0;
			}
			if($arr["request"]["do_room_separators"])
			{
				$d["room"] = html::checkbox(array(
					"name" => "room_sel[".$o->prop("resource")."]",
					"value" => $o->prop("resource"),
				)).html::obj_change_url(obj($o->prop("resource")));
				$rooms_added[] = $o->prop("resource");
			}
			$d["name"] = html::obj_change_url($o); //->name();
			$d["discount"] = html::textbox(array(
				"name" => "discount_".$o->id(),
				"value" => $o->prop("special_discount"),
				"size" => 5
			));
			$ssum = $o->get_special_sum();
			$cur = $arr["request"]["default_currency"] ? $arr["request"]["default_currency"] : $cur;
			$d["custom"] = html::textbox(array(
				"name" => "custom_".$o->id(),
				"value" => $ssum[$cur],
				"size" => 5
			));
			$d["tables"] = html::select(array(
				"name" => "tables_".$o->id(),
				"options" => $tableoptions,
				"value" => $o->meta("tables"),
			));
			if($arr["request"]["define_chooser"])
			{
				$d["reservation"] = ($arr["request"]["chooser"] == "room")?$o->prop("resource"):$o->id();
			}
			if($ssum[$cur])
			{
				$total = $ssum[$cur];
			}
			$totals += $total;
			$d["total"] = number_format($total,2);
			$t->define_data($d);
		}
		if($arr["request"]["do_room_separators"] and is_array($arr["request"]["extra_rooms_for_separators"]))
		{
			foreach($arr["request"]["extra_rooms_for_separators"] as $room)
			{
				if(in_array($room, $rooms_added))
				{
					continue;
				}

				$t->define_data(array(
					"room" => html::checkbox(array(
						"name" => "room_sel[".$room."]",
						"value" => $room,
					)).html::obj_change_url(obj($room)),
				));
			}
		}
		if(count($rvs) > 0)
		{
			$t->define_data(array(
				"split" => "#CCCCCC",
			));
			$t->define_data(array(
				"custom" => "<strong>".t("Kokku:")."</strong>",
				"total" => number_format($totals,2),
			));
			$h = $t->get_data();
			end($h);
			$this->prices_tbl_sum_row = key($h);
		}
	}

	function _get_admin_price_view($prod,$sum)
	{
		//TODO: mis segadus siin on?
		//if(aw_global_get("uid") != "struktuur")
		return number_format($sum, 2); //$prod->prop("price");
		return number_format($sum, 2).
			html::href(array(
				"onclick" => "document.getElementById(\"change_pr".$prod->id()."\").style.display=\"\"",
				"caption" =>
					"*",
				"url" => "javascript:;",
			)).
		"<div id='change_pr".$prod->id()."' style='display:none' >".
			html::textbox(array(
				"name"=>'change_price['.$prod->id().']',
				"size" => 5,
			)).
		"</div>";
	}


	function add_order($reservation, $order, $time = false)
	{
		if(!is_oid($reservation) || !is_oid($order))
		{
			return false;
		}
		$reservation = obj($reservation);

		$orders = $this->get_orders($reservation->id());
		if(!$time || ($time < $reservation->prop("start1") && $time > $reservation->prop("end")))
		{
			$time = $reservation->prop("start1");
		}
		$orders[$order] = $time;
		$reservation->set_meta("order_times", $orders);
		$reservation->save();
	}

	function get_orders($reservation)
	{
		if(!is_oid($reservation))
		{
			return false;
		}
		$reservation = obj($reservation);
		return $reservation->meta("order_times");
	}

	/**
		@attrib name=mark_arrived_popup params=name all_args=1
		@param bron required type=oid
			products and their amounts
	**/
	function mark_arrived_popup($arr)
	{
		if ($this->can("view", $arr["bron"]))
		{
			$arr["bron"] = array($arr["bron"]);
		}
		extract($arr);
		$ret = "<form method=POST action=".get_ru().">";
		foreach(safe_array($arr["bron"]) as $bron)
		{
			$bron_obj = obj($bron);
			if(isset($_POST[$bron]))
			{
				$bron_obj->set_prop("client_arrived" , $_POST[$bron]);
				$bron_obj->save();
			}
			$ret.= t("Broneering : ");
			$ret.= date("G:i" , $bron_obj->prop("start1"));
			$ret.= "-";
			$ret.= date("G:i" , $bron_obj->prop("end"));
			if(is_oid($bron_obj->prop("resource")))
			{
				$res = obj($bron_obj->prop("resource"));
				$ret.= "\n<br>".$res->name();
			}

			if(is_oid($bron_obj->prop("customer")))
			{
				$customer = obj($bron_obj->prop("customer"));
				$ret.= "\n<br>".$customer->name();
			}
			$ret.= "\n<br>".html::radiobutton(array("name" => $bron , "value" => 2 , "caption" => t("Klient ei ilmunud kohale")));
			$ret.= "\n<br>".html::radiobutton(array("name" => $bron , "value" => 1 , "caption" => t("Klient ilmus kohale")));
			$ret .= "<hr>";
		}

		$ret.= "\n<br>".html::submit(array("name" => "submit", "value" => t("M&auml;rgi")));
		$ret.="</form>";
		if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$d = "<script type='text/javascript'>window.close();</script>";
			die($d);
		}
		$ret.="<!-- $arr[bron] -->";
		die($ret);
	}

	function _get_b_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		if ($this->can("delete", $arr["obj_inst"]->id()))
		{
			$tb->add_button(array(
				"name" => "delete_bron",
				"tooltip" => t("Kustuta broneering"),
				"confirm" => t("Kas oled kindel et soovid broneeringut kustutada?"),
				"img" => "delete.gif",
				"action" => "del_bron"
			));
			$has = true;
		}

		if ($arr["obj_inst"]->prop("verified") && !$arr["obj_inst"]->get_rfp())
		{
			$tb->add_button(array(
				"name" => "unverify",
				"tooltip" => t("T&uuml;hista kinnitus"),
				"onClick" => "document.changeform.reason.value=prompt('Sisestage t&uuml;histuse p&otilde;hjus');submit_changeform('unverify')",
				"action" => ""
			));
			$has = true;
		}

		$has = true;
		$tb->add_button(array(
			"name" => "rclose",
			"img" => "refresh.gif",
			"onClick" => "window.opener.refresh();window.close();"
		));
		if (!$has)
		{
			return PROP_IGNORE;
		}
	}

	/**
		@attrib name=unverify
	**/
	function unverify($arr)
	{
		$o = obj($arr["id"]);
		$o->set_prop("verified", 0);
		$o->set_meta("unverify_reason", $arr["reason"]);
		$o->save();
		if ($this->can("view", $o->prop("resource")))
		{
			$res = obj($o->prop("resource"));
			$sets = $res->prop("settings");
			if (is_array($sets))
			{
				$sets = reset($sets);
			}
			if ($this->can("view", $sets))
			{
				$set = obj($sets);
				if ($set->prop("send_uv_mail"))
				{
					send_mail(
						$set->prop("uv_mail_to"),
						$set->prop("uv_mail_subj"),
						str_replace("#ord#", $this->_get_mail_ord_ct($o), str_replace("#reason#", $arr["reason"] , $set->prop("uv_mail_ct"))),
						"From: ".$this->_get_del_mail_from($set)
					);
				}
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=del_bron
	**/
	function del_bron($arr)
	{
		$o = obj($arr["id"]);
		$room = obj($o->prop("resource"));
		$o->delete();
		$room_i = get_instance(CL_ROOM);
		$settings = $room_i->get_settings_for_room($room);
		if ($settings->prop("bron_no_popups"))
		{
			return $arr["return_url"];
		}
		// close if in popup
		die("<script language='javascript'>
			if (window.opener)
			{
				window.opener.location.reload();
				window.close();
			}
			else
			{
				window.location = \"".$arr["return_url"]."\";
			}
		</script>");
	}

	function _get_length($arr)
	{
		$len = $arr["obj_inst"]->prop("end") - $arr["obj_inst"]->prop("start1");
		if ($len > 3600)
		{
			$arr["prop"]["post_append_text"] = sprintf(t("/ Hetkel pikkus: %d tundi"),
				floor($len / 3600),
				floor(($len - floor($len / 3600)*3600) / 60)
			);
		}
		else
		{
			$arr["prop"]["post_append_text"] = sprintf(t("/ Hetkel pikkus: %02d minutit"),
				floor($len / 60)
			);
		}
		$room_i = get_instance(CL_ROOM);
		$rts = $room_i->get_time_units();
		if ($this->can("view", $arr["obj_inst"]->prop("resource")))
		{
			$arr["prop"]["options"][0] = t("--vali--");
			$room = obj($arr["obj_inst"]->prop("resource"));
			$tf = $room->prop("time_from");
			$tt = $room->prop("time_to");
			$ts = $room->prop("time_step");
			$stf = $room->prop("selectbox_time_from");
			$stt = $room->prop("selectbox_time_to");
			$sts = $room->prop("selectbox_time_step");
			if($stf && $stt && $sts)
			{
				$tf = $stf;
				$tt = $stt;
				$ts = $sts;
			}
			for($i = $tf; $i <= $tt; $i += $ts)
			{
				$arr["prop"]["options"][(string)$i] = $i;
			}
			$i -= $ts;
			switch($room->prop("time_unit"))
			{
				case 2:
					$max = 24;
					break;
				case 3:
					$max = 30;
					break;
				default:
					$max = 60;
					break;
			}
			if($i < $max)
			{
				for(;$i<=$max;$i++)
				{
					$arr["prop"]["options"][(string)$i] = $i;
				}
			}
			$arr["prop"]["post_append_text"] = $rts[$room->prop("time_unit")].$arr["prop"]["post_append_text"];
			return;
		}
		$arr["prop"]["options"][0] = t("--vali--");
		$arr["prop"]["options"] = array_merge($arr["prop"]["options"], $this->make_keys(range(1, 20)));
		$arr["prop"]["post_append_text"] = t("Tundi").$arr["prop"]["post_append_text"];
	}

	function _get_cp_fn($arr)
	{
		if (!$this->can("view", $arr["obj_inst"]->prop("customer")))
		{
			if($arr["request"]["person_rfp_fname"])
			{
				$arr["prop"]["value"] = $arr["request"]["person_rfp_fname"];
			}
			return PROP_OK;
		}
		$cust = obj($arr["obj_inst"]->prop("customer"));
		if ($cust->class_id() == CL_CRM_PERSON)
		{
			$arr["prop"]["value"] = $cust->prop("firstname");
		}
		else
		{
			return PROP_IGNORE;
		}
	}

	function _get_cp_ln($arr)
        {
                if (!$this->can("view", $arr["obj_inst"]->prop("customer")))
                {
			if($arr["request"]["person_rfp_lname"])
			{
				$arr["prop"]["value"] = $arr["request"]["person_rfp_lname"];
			}
                        return PROP_OK;
                }

                $cust = obj($arr["obj_inst"]->prop("customer"));
                if ($cust->class_id() == CL_CRM_PERSON)
                {
                        $arr["prop"]["value"] = $arr["obj_inst"]->prop("customer.lastname");
                }
                else
                {
                        return PROP_IGNORE;
                }
        }

	function _get_cp_phone($arr)
        {
                if (!$this->can("view", $arr["obj_inst"]->prop("customer")))
                {
			if($arr["request"]["person_rfp_phone"])
			{
				$arr["prop"]["value"] = $arr["request"]["person_rfp_phone"];
			}
                        return PROP_OK;
                }

                $cust = obj($arr["obj_inst"]->prop("customer"));
                if ($cust->class_id() == CL_CRM_PERSON)
                {
                        $arr["prop"]["value"] = $arr["obj_inst"]->prop("customer.phone.name");
                }
                else
                {
                        return PROP_IGNORE;
                }
        }

        function _get_cp_email($arr)
        {
                if (!$this->can("view", $arr["obj_inst"]->prop("customer")))
                {
			if($arr["request"]["person_rfp_email"])
			{
				$arr["prop"]["value"] = $arr["request"]["person_rfp_email"];
			}
                        return PROP_OK;
                }

                $cust = obj($arr["obj_inst"]->prop("customer"));
                if ($cust->class_id() == CL_CRM_PERSON)
                {
                        $arr["prop"]["value"] = $arr["obj_inst"]->prop("customer.email.mail");
                }
                else
                {
                        return PROP_IGNORE;
                }
        }


	function _get_people_count($arr)
        {
		if($arr["obj_inst"]->is_lower_bron())
		{
			$arr["prop"]["type"] = "text";
		}
		if($arr["request"]["people_count_rfp"])
		{
			$arr["prop"]["value"] = $arr["request"]["people_count_rfp"];
		}
		return PROP_OK;
        }



	function get_room_prop($room, $prop)
	{
		if(is_oid($room) && $this->can("view" , $room))
		{
			$room = obj($room);
		}
		if(is_object($room))
		{
			$room_inst = get_instance(CL_ROOM);
			$settings = $room_inst->get_settings_for_room($room);
			return $settings->prop($prop);
		}
		return null;
	}

	function _set_cp_fn($arr)
	{
                if (!$this->can("view", $arr["obj_inst"]->prop("customer")) && $arr["prop"]["value"] != "")
                {
			list($fn, $ln) = explode(" ", $arr["request"]["new"]["name"]);

			if($this->get_room_prop($arr["request"]["resource"], "use_existing_person"))
			{
				$ol = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"lang_id" => array(),
					"site_id" => array(),
					"firstname" => trim($arr["request"]["cp_fn"]),
					"lastname" => trim($arr["request"]["cp_ln"])
				));
			}
			else
			{
				$ol = new object_list();
			}
			if ($ol->count())
			{
				$cust = $ol->begin();
			}
			else
			{
				$cust = obj();
				$cust->set_parent($arr["obj_inst"]->id() ? $arr["obj_inst"]->id() : $_POST["parent"]);
				$cust->set_class_id(CL_CRM_PERSON);
				$cust->save();
			}
			$arr["obj_inst"]->set_prop("customer", $cust->id());
                }

		if (!$this->can("view", $arr["obj_inst"]->prop("customer")) || $arr["prop"]["value"] == "")
		{
			return PROP_IGNORE;
		}
                $cust = obj($arr["obj_inst"]->prop("customer"));
                if ($cust->class_id() == CL_CRM_PERSON)
                {
			$cust->set_prop("firstname", $arr["prop"]["value"]);
			$cust->set_name($cust->prop("firstname")." ".$cust->prop("lastname"));
			aw_disable_acl();
			$cust->save();
			aw_restore_acl();
		}
		return PROP_IGNORE;
	}

        function _set_cp_ln($arr)
         {

                if (!$this->can("view", $arr["obj_inst"]->prop("customer")) || $arr["prop"]["value"] == "")
                {
                        return PROP_IGNORE;
                }

                $cust = obj($arr["obj_inst"]->prop("customer"));
                if ($cust->class_id() == CL_CRM_PERSON)
                {
                        $cust->set_prop("lastname", $arr["prop"]["value"]);
                        $cust->set_name($cust->prop("firstname")." ".$cust->prop("lastname"));
			aw_disable_acl();
                        $cust->save();
			aw_restore_acl();
                }
                return PROP_IGNORE;
        }

        function _set_cp_phone($arr)
        {
                if (!$this->can("view", $arr["obj_inst"]->prop("customer")) || $arr["prop"]["value"] == "")
                {
                        return PROP_IGNORE;
                }

                $cust = obj($arr["obj_inst"]->prop("customer"));
                if ($cust->class_id() == CL_CRM_PERSON)
                {
			if ($this->can("view", $cust->prop("phone")))
			{
				$ph = obj($cust->prop("phone"));
			}
			else
			{
				$ph = obj();
				$ph->set_parent($cust->id());
				$ph->set_class_id(CL_CRM_PHONE);
			}
			$ph->set_name($arr["prop"]["value"]);
			aw_disable_acl();
			$ph->save();
			aw_restore_acl();
			if (!$this->can("view", $cust->prop("phone")))
			{
				$cust->connect(array(
					"to" => $ph->id(),
					"type" => "RELTYPE_PHONE"
				));
				$cust->set_prop("phone", $ph->id());
				aw_disable_acl();
				$cust->save();
				aw_restore_acl();
			}
                }
                return PROP_IGNORE;
        }

        function _set_cp_email($arr)
        {
                if (!$this->can("view", $arr["obj_inst"]->prop("customer")) || $arr["prop"]["value"] == "")
                {
                        return PROP_IGNORE;
                }

                $cust = obj($arr["obj_inst"]->prop("customer"));
                if ($cust->class_id() == CL_CRM_PERSON)
                {
                        if ($this->can("view", $cust->prop("email")))
                        {
                                $ph = obj($cust->prop("email"));
                        }
                        else
                        {
                                $ph = obj();
                                $ph->set_parent($cust->id());
                                $ph->set_class_id(CL_ML_MEMBER);
                        }
                        $ph->set_name($arr["prop"]["value"]);
			$ph->set_prop("mail", $arr["prop"]["value"]);
			aw_disable_acl();
                        $ph->save();
			aw_restore_acl();
                        if (!$this->can("view", $cust->prop("email")))
                        {
                                $cust->connect(array(
                                        "to" => $ph->id(),
                                        "type" => "RELTYPE_EMAIL"
                                ));
                                $cust->set_prop("email", $ph->id());
				aw_disable_acl();
                                $cust->save();
				aw_restore_acl();
                        }
                }
                return PROP_IGNORE;
        }

	function _get_modder($arr)
	{
		$u = get_instance(CL_USER);
		$p = $u->get_person_for_uid($arr["obj_inst"]->createdby());
		$mp = $u->get_person_for_uid($arr["obj_inst"]->modifiedby());
		if(is_oid($p->id()) && $this->can("view" , USER::get_company_for_person($p)))
		{
			$co = obj(USER::get_company_for_person($p));
			if($co->name()) $c = "(".html::obj_change_url($co).")";
		}
		if(is_oid($mp->id()) && $this->can("view" , USER::get_company_for_person($mp)))
		{
			$co = obj(USER::get_company_for_person($mp));
			if($co->name()) $mc = "(".html::obj_change_url($co).")";
		}

		$arr["prop"]["value"] = sprintf(
			t("Loomine: %s %s / %s.<br>Muutmine: %s %s / %s"),
			html::obj_change_url($p),$c,
			date("d.m.Y H:i", $arr["obj_inst"]->created()),
			html::obj_change_url($mp),$mc,
			date("d.m.Y H:i", $arr["obj_inst"]->modified())
		);

		//l6ppu maksmise infi, juhul kui on makstud
		//ei n2inud m6tet eraldi property tegemiseks
		//default valuuta tuleb systeemis olev default, juhul kui makse infost seda k2tte ei saa
		if(is_array($arr["obj_inst"]->meta("payment_info")))
		{
			$inf = $arr["obj_inst"]->meta("payment_info");
			$sum = $inf["sum"];
			if(!$inf["curr"])
			{
				$currency = get_instance(CL_CURRENCY);
				$inf["curr"] = $currency->get_default_currency_name();
			}
			$bron_cost = $arr["obj_inst"]->get_sum_in_curr($inf["curr"]);
			if($bron_cost != "")
			{
				if($sum > $bron_cost)
				{
					$inf["sum"] = $bron_cost." (".$sum.")";
				}
				elseif($sum == $bron_cost)
				{
					$inf["sum"] = $sum;
				}
				else
				{
					$inf["sum"] = $bron_cost;
				}
			}

			$arr["prop"]["value"].= sprintf(
				t("<br>Tasutud %s %s (%s) %s , maksja %s"),
				$inf["sum"],
				$inf["curr"],
				$inf["bank"],
				$inf["time"] > 1 ? date("d.m.Y H:i", $inf["time"]) : "",
				$inf["payer"]
			);
		}
	}

	function _get_unverify_reason($arr)
	{
		if ($arr["obj_inst"]->meta("unverify_reason") == "")
		{
			return PROP_IGNORE;
		}
		$arr["prop"]["value"] = sprintf(t("Kinnituse eemaldamise p&otilde;hjus: %s"), $arr["obj_inst"]->meta("unverify_reason"));
	}

	function on_delete_reservation($arr)
	{
		$o = obj($arr["oid"]);
		if ($this->can("view", $o->prop("resource")))
		{
			$res = obj($o->prop("resource"));
			$sets = $res->prop("settings");
			if (is_array($sets))
			{
				$sets = reset($sets);
			}
			if ($this->can("view", $sets))
			{
				$set = obj($sets);
				if ($set->prop("send_del_mail"))
				{
					$this->do_send_on_delete_mail($o, $set);
				}
			}
		}
	}

	function do_send_on_delete_mail($bron, $settings)
	{
		send_mail(
			$settings->prop("del_mail_to"),
			$settings->prop("del_mail_subj"),
			str_replace("#ord#", $this->_get_mail_ord_ct($bron), $settings->prop("del_mail_ct")),
			"From: ".$this->_get_del_mail_from($settings)
		);
	}

	function _get_mail_ord_ct($bron)
	{
		$res = "";
		//kliendi nimi, kontaktid, aeg, broneeritud ruum, toidud
		$res .= sprintf(t("Klient: %s / %s / %s\n"),
			$bron->prop("customer.name"),
			$bron->prop("customer.email.mail"),
			$bron->prop("customer.phone.name")
		);
		$res .= sprintf(t("Aeg: %s: %s-%s\n"),
			date("d.m.Y", $bron->prop("start1")),
			date("H:i", $bron->prop("start1")),
			date("H:i", $bron->prop("end"))
		);
		$res .= sprintf(t("Koht: %s\n"), $bron->prop("resource.name"));

                $amount = $bron->meta("amount");
                $val = array();
                foreach($amount as $product => $amt)
 	        {
        	        if($amt && $this->can("view", $product))
                	{
		                $prod=obj($product);
                		$val[] = $prod->name();
	                }
                }
                $res .= sprintf(t("Toidud: %s\n"), join($val , ","));

		return $res;
	}

	function _get_del_mail_from($s)
	{
		if ($s->prop("del_mail_from_name") != "")
		{
			return $s->prop("del_mail_from_name")." <".$s->prop("del_mail_from").">";
		}
		return $s->prop("del_mail_from");
	}

	function get_products_text($o, $sep = "<BR>")
	{
		$amount = $o->meta("amount");
		$val = array();
		foreach($amount as $product => $amt)
		{
			if($amt && $this->can("view", $product))
			{
				$prod=obj($product);
				if($prod->meta("cur_prices") && array_sum($prod->meta("cur_prices")))
				{
					$str = "";
					foreach($prod->meta("cur_prices") as $curr => $sum)
					{
						if($this->can("view" , $curr))
						{
							$co = obj($curr);
							$str.= number_format($sum*$amt,2)." " .$co->name()." ";
						}
					}
				}
				else
				{
					$str = number_format($prod->prop("price")*$amt,2);
				}

				$val[] = sprintf(t("%s: %s tk / %s"),
					$prod->name(),
					$amt,
					$str
				);
			}
		}
		return join($val , $sep);
	}

	function get_products_wo_amount_text($o, $sep = "<BR>")
	{
		$amount = $o->meta("amount");
		$val = array();
		foreach($amount as $product => $amt)
		{
			if($amt && $this->can("view", $product))
			{
				$prod=obj($product);
				$val[] = $prod->name();
			}
		}
		return join($val , $sep);
	}

	function _format_sum($o)
	{
		classload("vcl/table");
		$t = new vcl_table;

		$t->define_field(array(
			"name" => "desc",
			"caption" => t("&nbsp;"),
			"align" => "right",
		));

		$room_instance = get_instance(CL_ROOM);
		$sum = $room_instance->cal_room_price(array(
			"room" => $o->prop("resource"),
			"start" => $o->prop("start1"),
			"end" => $o->prop("end"),
			"people" => $o->prop("people_count"),
			"products" => $o->meta("amount"),
			"bron" => $o,
			"detailed_info" => true
		));

		foreach($sum["room_price"] as $cur => $price)
		{
			$co = obj($cur);
			$t->define_field(array(
				"name" => "price".$cur,
				"caption" => $co->name(),
				"align" => "right",
			));
		}

		$d = array(
			"desc" => t("Tooted")
		);
		foreach($sum["prod_price"] as $cur => $price)
		{
			$d["price".$cur] = number_format($price, 2);
		}
		$t->define_data($d);

		$d = array(
			"desc" => t("Ruum")
		);
		foreach($sum["room_price"] as $cur => $price)
		{
			$d["price".$cur] = number_format($price, 2);
		}
		$t->define_data($d);

		$d = array(
			"desc" => sprintf(t("Soodustus ruumilt (-%d %%)"), $sum["room_bargain"]*100)
		);
		foreach($sum["room_bargain_value"] as $cur => $price)
		{
			$d["price".$cur] = number_format($price, 2);
		}
		$t->define_data($d);

		$d = array(
			"desc" => sprintf(t("Soodustus toodetelt (-%d %%)"), $sum["prod_discount"])
		);
		foreach($sum["prod_discount_value"] as $cur => $price)
		{
			$d["price".$cur] = number_format($price, 2);
		}
		$t->define_data($d);


		$d = array(
			"desc" => t("Summa")
		);
if(is_oid($o->id())){	$sum = $o->get_sum();

                foreach($sum as $cur => $price)
		{                 $d["price".$cur] = number_format($price, 2);
              }
}
else{
		foreach($sum["room_price"] as $cur => $price)
		{
			$d["price".$cur] = number_format($price + $sum["prod_price"][$cur], 2);
		}}


		$t->define_data($d);


		return $t->draw();


		$pv = "";
		foreach($sum as $cur=>$price)
		{
			$cur = obj($cur);
			$pv .= $price." ".$cur->name()."<br>";
			$t->define_data(array(
				"desc" => $cur->name(),
				"price" => $price
			));
		}
		return $t->draw();
		return $pv;
	}

	function do_db_upgrade($t, $f)
	{
		if ($f == "" && $t == "aw_room_reservations")
		{
			$this->db_query("CREATE TABLE aw_room_reservations(aw_oid int primary key,
				aw_verified int,
				aw_paid int,
				aw_resource int,
				aw_time_closed int,
				aw_closed_info varchar(255),
				aw_people_count int,
				aw_special_discount double,
				aw_client_arrived int,
				aw_people int,
				aw_inbetweener int,
				aw_sum text,
				aw_special_sum double,
				aw_products_discount double,
				resource_price varchar(13),
				resource_discount varchar(13)
			)");
			echo "table <br>\n";
		flush();
			$ol = new object_list(array(
				"class_id" => CL_RESERVATION,
				"lang_id" => array(),
				"site_id" => array()
			));
echo "list <br>\n";
flush();
			$ids = $ol->ids();
			foreach($ids as $id)
			{
				$this->db_query("INSERT INTO aw_room_reservations(aw_oid) values($id)");
			}
echo "inserts <br>\n";
flush();
			foreach($ol->arr() as $o)
			{
				$o->set_prop("verified", $o->meta("verified"));
				$o->set_prop("resource", $o->meta("resource"));
				$o->set_prop("time_closed", $o->meta("time_closed"));
				$o->set_prop("closed_info", $o->meta("closed_info"));
				$o->set_prop("people_count", $o->meta("people_count"));
				$o->set_prop("special_discount", $o->meta("special_discount"));
				$o->set_prop("client_arrived", $o->meta("client_arrived"));
				$o->set_prop("people", $o->meta("people"));
				aw_disable_acl();
				$o->save();
				aw_restore_acl();
				echo $o->id()." <br>\n";
				flush();
			}
echo "all done <br>\n";
flush();
			return true;
		}
		else
		{
			switch($f)
			{
				case "resources_price":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "text"
					));
					break;
				case "resources_discount":
				case "aw_type":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "varchar(13)"
					));
					break;
				case "aw_special_sum":
				case "aw_special_discount":
				case "aw_products_discount":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "double"
					));
					break;
				case "aw_paid":
				case "aw_inbetweener":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "int"
					));
					break;
			}
			return true;
		}
		return false;
	}

	function bank_return($arr)
	{
		$rr = get_instance(CL_ROOM_RESERVATION);
		$rr->bank_return($arr);
		return $url;
	}

	function _get_ppl_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];

		$disp = true;
		if ($this->can("view", $arr["obj_inst"]->prop("resource")))
		{
			$ro = obj($arr["obj_inst"]->prop("resource"));
			if ($ro->prop("max_capacity") > 0 && count($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER"))) >= $ro->prop("max_capacity"))
			{
				$disp = false;
			}
		}

		$this->disp = $disp;
		if ($disp)
		{
			$tb->add_new_button(array(CL_CRM_PERSON),$arr["obj_inst"]->id(), 1 /* RELTYPE_CUSTOMER */);
			$tb->add_search_button(array(
				"pn" => "add_p",
				"clid" => CL_CRM_PERSON,
			));
		}
		$tb->add_delete_rels_button();
	}

	function _init_ppl_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi")."\n<br>(".t("Eesnimi")."/".t("Perenimi").")",
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("Meiliaadress"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_ppl($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ppl_t($t);
		$t->set_sortable(false);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"phone" => html::obj_change_url($o->prop("phone")),
				"email" => html::obj_change_url($o->prop("email")),
				"oid" => $o->id()
			));
		}

		if ($this->disp)
		{
			$t->define_data(array(
				"name" =>  html::textbox(array(
					"name" => "new[firstname]",
					"size" => 15
				)).html::textbox(array(
					"name" => "new[name]",
					"size" => 15
				)),
				"phone" => html::textbox(array(
					"name" => "new[phone]",
					"size" => 15
				)),
				"email" => html::textbox(array(
					"name" => "new[email]",
					"size" => 15
				)),
			));
		}
	}

	function _set_ppl($arr)
	{
		if ($arr["request"]["new"]["name"] != "")
		{
			//list($fn, $ln) = explode(" ", $arr["request"]["new"]["name"]);
			$fn = $arr["request"]["new"]["firstname"];
			$ln = $arr["request"]["new"]["name"];
			$ol = new object_list(array(
				"class_id" => CL_CRM_PERSON,
				"lang_id" => array(),
				"site_id" => array(),
				"firstname" => trim($fn),
				"lastname" => trim($ln)
			));
			if ($ol->count())
			{
				$cust = $ol->begin();
			}
			else
			{
				$cust = obj();
				$cust->set_parent($arr["obj_inst"]->id() ? $arr["obj_inst"]->id() : $_POST["parent"]);
				$cust->set_class_id(CL_CRM_PERSON);
				$cust->set_prop("firstname", trim($fn));
				$cust->set_prop("lastname", trim($ln));
				$cust->set_name(trim($fn)." ".trim($ln));
				$cust->save();

				if ($arr["request"]["new"]["phone"] != "")
				{
					$ph = obj();
					$ph->set_parent($cust->id());
					$ph->set_class_id(CL_CRM_PHONE);
					$ph->set_name($arr["request"]["new"]["phone"]);
					$ph->save();
					$cust->connect(array(
						"to" => $ph->id(),
						"type" => "RELTYPE_PHONE"
					));
					$cust->set_prop("phone", $ph->id());
				}

				if ($arr["request"]["new"]["email"] != "")
				{
                	                $ph = obj();
                        	        $ph->set_parent($cust->id());
                                	$ph->set_class_id(CL_ML_MEMBER);
		                        $ph->set_name($arr["request"]["new"]["email"]);
					$ph->set_prop("mail", $arr["request"]["new"]["email"]);
                		        $ph->save();
                        	        $cust->connect(array(
                                	        "to" => $ph->id(),
                                        	"type" => "RELTYPE_EMAIL"
	                                ));
        	                        $cust->set_prop("email", $ph->id());
				}
				$cust->save();
			}

			$arr["obj_inst"]->connect(array(
				"to" => $cust->id(),
				"type" => "RELTYPE_CUSTOMER"
			));
		}
	}

	function _get_recur_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_RECURRENCE), $arr["obj_inst"]->id(), 4 /* RELTYPE_RECURRENCE */);
		$tb->add_delete_button();
	}

	function _init_recur_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "confirmed",
			"caption" => t("Kinnita"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _get_recur_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_recur_t($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_RECURRENCE")) as $c)
		{
			$r = $c->to();
			if ($r->meta("confirmed"))
			{
				$t->define_data(array(
					"name" => html::obj_view_url($r),
					"confirmed" => t("Kinnituatud, kordused loodud"),
				));
			}
			else
			{
				$t->define_data(array(
					"name" => html::obj_change_url($r),
					"confirmed" => html::checkbox(array(
						"name" => "confirm[".$r->id()."]",
						"value" => 1
					)),
					"oid" => $r->id()
				));
			}
		}
	}

	function _set_recur_t($arr)
	{
		foreach(safe_array($arr["request"]["confirm"]) as $id => $val)
		{
			if ($val == 1)
			{
				$this->do_create_recurring_events($arr["obj_inst"], obj($id));
			}
		}
	}

	function do_create_recurring_events($reservation, $recur)
	{
		$ri = $recur->instance();
		$day_secs = $reservation->prop("start1") - get_day_start($reservation->prop("start1"));
		$day_end_secs = $reservation->prop("end") - get_day_start($reservation->prop("start1"));
		foreach($ri->get_event_range(array("id" => $recur->id(), "start" => $recur->prop("start"), "end" => $recur->prop("end"))) as $event)
		{
			$day = get_day_start($event["recur_start"]);

			$from = $day + $day_secs;
			$to = $day + $day_end_secs;

			$new = $this->_copy_object($reservation, $reservation->parent());
			$new->set_prop("start1", $from);
			$new->set_prop("end", $to);
			$this->calc_obj_name($new);
			$new->save();

			$reservation->connect(array(
				"to" => $new->id(),
				"type" => "RELTYPE_REPEATED_BRON"
			));

			$new->connect(array(
				"to" => $reservation->id(),
				"type" => "RELTYPE_ORIGINAL_BRON"
			));
		}
		$recur->set_meta("confirmed", 1);
		$recur->save();
	}

	function _copy_object($old, $parent)
	{
		$o = obj();
		$o->set_class_id($old->class_id());
		$o->set_parent($parent);
		$o->set_comment($old->comment());

		// meta
		foreach($old->meta() as $k => $v)
		{
			$o->set_meta($k, $v);
		}

		// props
		foreach($old->properties() as $k => $v)
		{
			if ($o->is_property($k))
			{
				$o->set_prop($k, $v);
			}
		}
		$o->save();

		// conns
		foreach($old->connections_from() as $c)
		{
			if (!$o->is_connected_to(array("to" => $c->prop("to"), "type" => $c->prop("reltype"))))
			{
				if ($c->prop("reltype") != 5)
				{
					$o->connect(array(
						"to" => $c->prop("to"),
						"reltype" => $c->prop("reltype")
					));
				}
			}
		}

		return $o;
	}

	function _init_recur_manage_t($t)
	{
		$t->define_field(array(
			"name" => "from",
			"caption" => t("Alates"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "to",
			"caption" => t("Kuni"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "room",
			"caption" => t("Ruum"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_recur_manage_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_recur_manage_t($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_REPEATED_BRON")) as $c)
		{
			$bron = $c->to();
			$t->define_data(array(
				"from" => html::datetime_select(array(
					"value" => $bron->prop("start1"),
					"name" => "bron[".$bron->id()."][from]"
				)),
				"to" => html::datetime_select(array(
					"value" => $bron->prop("end"),
					"name" => "bron[".$bron->id()."][to]"
				)),
				"room" => html::select(array(
					"name" => "bron[".$bron->id()."][room]",
					"options" => $this->get_resource_options($bron),
					"value" => $bron->prop("resource")
				)),
				"oid" => $bron->id(),
				"start" => $bron->prop("start1")
			));
		}
		$t->set_default_sortby("start");
		$t->set_caption(t("Korratud reserveeringud"));
		$t->sort_by();
	}

	function get_resource_options($o)
	{
		$rv = array();
		if ($this->can("view", $o->prop("resource")))
		{
			$ri = get_instance(CL_ROOM);
			$sets = $ri->get_settings_for_room(obj($o->prop("resource")));
			if ($sets->prop("related_room_folder"))
			{
				$rrs = new object_list(array(
					"class_id" => CL_ROOM,
					"parent" => $sets->prop("related_room_folder"),
					"lang_id" => array(),
					"site_id" => array()
				));//arr($prop["options"]);
				$rv += $rrs->names();
			}
		}
		if (!isset($rv[$o->prop("resource")]) && $this->can("view", $o->prop("resource")))
		{
			$tmp = obj($o->prop("resource"));
			$rv[$o->prop("resource")] = $tmp->name();
		}
		return $rv;
	}

	function _get_recur_manage_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_save_button();
		$tb->add_delete_button();
	}

	function _set_recur_manage_t($arr)
	{
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_REPEATED_BRON")) as $c)
		{
			$mod = false;
			$bron = $c->to();
			$ts = date_edit::get_timestamp($arr["request"]["bron"][$bron->id()]["from"]);
			if ($bron->prop("start1") != $ts)
			{
				$mod = true;
				$bron->set_prop("start1", $ts);
			}
			$ts = date_edit::get_timestamp($arr["request"]["bron"][$bron->id()]["to"]);
			if ($bron->prop("end") != $ts)
			{
				$mod = true;
				$bron->set_prop("end", $ts);
			}
			if ($bron->prop("resource") != ($ts = $arr["request"]["bron"][$bron->id()]["room"]))
			{
				$mod = true;
				$bron->set_prop("resource", $ts);
			}

			if ($mod)
			{
				$bron->save();
			}
		}
	}

	/** Returns the total price of the reservation in all the currencies
		@attrib api=1
		@param reservation required type=object
	**/
	function get_reservation_price($reservation)
	{
		$room_instance = get_instance(CL_ROOM);
		return $room_instance->cal_room_price(array(
			"room" => $reservation->prop("resource"),
			"start" => $reservation->prop("start1"),
			"end" => $reservation->prop("end"),
			"people" => $reservation->prop("people_count"),
			"products" => $reservation->meta("amount"),
			"bron" => $reservation,
		));
	}

        function get_products_discount($reservation)
        {
                extract($arr);
                if(is_oid($reservation) && $this->can("view" , $reservation))
                {
                        $reservation = obj($reservation);
                }
                if(!is_object($reservation))
                {
                        return false;
                }
                return $reservation->prop("products_discount");
        }
        /** gets reservation products discount
                @attrib api=1 params=name
                @param reservation required type=object/oid
                @returns array
                        array(prod1 => discount, prod2 => discount , ...)
        **/
        function get_product_discount($reservation)
        {
                if(is_oid($reservation) && $this->can("view" , $reservation))
                {
                        $reservation = obj($reservation);
                }
                if(!is_object($reservation))
                {
                        return false;
                }
                return $reservation->meta("product_discount");
        }

	/**
		@attrib params=pos api=1
		@param id required type=oid
			Reservation object id

		@comment
			Fetches information about this reservation resources
		@returns
			array of information about resources
			array(
				RESOURCE_OID => array(
					count => 'amount of resources',
					discount => 'discount percent for resource',
					price => array(
						CURRENCY_OID => 'price in given currency'
					),
				)
			)
	**/
	function get_resources_data($oid)
	{
		if(!$this->can("view", $oid))
		{
			return false;
		}
		$o = obj($oid);
		return $o->meta("resources_info");
	}

	/**
		@attrib params=name api=1
		@param reservation required type=oid
			Reservation object id
		@param resources_info required type=array
			information about resources in array:
			array(
				RESOURCE_OID => array(
					cnt => 'amount of resources',
					discount => 'discount percent for resource',
					price => array(
						CURRENCY_OID => 'price in given currency'
					),
				)
			)
		@comment
			Set's information about this reservation resources
	**/
	function set_resources_data($arr)
	{
		if(!$this->can("view", $arr["reservation"]))
		{
			return false;
		}
		$obj = obj($arr["reservation"]);
		$obj->set_meta("resources_info", $arr["resources_info"]);
		$obj->save();
		return true;
	}

	//annab kogusumma(kui on)
	/**
		@attrib api=1 params=pos
		@param reservation required type=oid
			Reservation object oid
		@returns
			resources special prices in array
			array(
				CURRENCY_OBJECT_OID => price,
			)
	**/
	function get_resources_price($oid)
	{
		if(!$this->can("view", $oid))
		{
			return false;
		}
		$o = obj($oid);
		return aw_unserialize($o->prop("resources_price"));
	}

	/** sets resources price for bron
		@attrib api=1 params=name
		@param reservation required type=oid
			reservation object oid
		@param prices optional type=array
			prices array(
				CURRENCY_OBJECT_OID => price,
			)
		@returns
			true on success, false otherwise
	**/
	function set_resources_price( $arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return false;
		}
		$reservation->set_prop("resources_price" , aw_serialize($prices, SERIALIZE_NATIVE));
		$reservation->save();
		return true;
	}

	/** gets reservation resources discount
		@attrib api=1 params=pos
		@param reservation required type=oid
			reservation objekt oid
		@comment
			Gets reservation resources discount.
		@returns
			resources discount.
	**/
	function get_resources_discount($reservation)
	{
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return false;
		}
		return $reservation->prop("resources_discount");
	}


	/** sets reservation resources discount
		@attrib api=1 params=name
		@param reservation required type=oid
			Reservation object oid
		@param discount required type=double
			Resources special discount
		@returns
			true on success, false otherwise
	**/
	function set_resources_discount($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return false;
		}
		$reservation->set_prop("resources_discount" , $discount);
		$reservation->save();
		return true;
	}

// edasi toodete kr2pp
	/** Returns products data
		@attrib api=1 params=name
		@param reservation required type=object/oid
		@returns array
			array(prod1 => array("sum" => .. , "amount" => .. ) , ...)
	**/
	function get_products_data($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return false;
		}

		$prd = $reservation->meta("amount");
		foreach($prd as $product => $amount)
		{
			$products[$product]["amount"] = $amount;
			$products[$product]["sum"] = $this->get_product_price(array("reservation" => $reservation, "curr" => $curr));
		}
		return $products;
	}

	/**
		@attrib api=1 params=name
		@param reservation required type=object/oid
		@param data required type=array
			products data (id => array(amount => .. , sum => .., ))
		@returns true, if success... false if not
	**/
	function set_products_data($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return false;
		}
		$amount = $reservation->meta("amount");
		$price = $reservation->meta("products_price");
		foreach($data as $prod => $val)
		{
			$amount[$prod] = $val["amount"];
			$price[$prod] = $val["sum"];
		}
		$reservation->set_meta("amount", $amount);
		$reservation->set_meta("products_price", $price);
		return true;
	}

	/**
		@attrib api=1 params=name
		@param reservation required type=object/oid
		@param curr optional type=oid
			currency object id
	**/
	function get_products_price($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return 0;
		}
		$sum = $reservation->meta("products_total_price");
		if(is_array($sum))
		{
			if($curr)
			{
				return $sum[$curr];
			}
			else
			{
				return $sum[$this->get_default_currency];
			}
		}
		else
		{
			if(!$curr)
			{
				return $sum;
			}
			else
			{
				$c_inst = get_instance(CL_CURRENCY);
				return $c_inst->convert(array(
					"from" => $this->get_default_currency,
					"to" => $curr,
					"sum" => $sum,
				));
			}
		}
		return 0;
	}

	/** sets products price for bron
		@attrib api=1 params=name
		@param reservation required type=object/oid
			reservation object
		@param sum optiopal type=int
			product price sum (if there is only one product)
		@param curr optional type=oid
			currency object id
		@returns 1 - success , 0 - unsuccess
	**/
	function set_products_price($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return false;
		}
		$prod_info = $reservation->meta("products_total_price");

		//kui m22ratakse kindla valuutaga summa, kui mitte, siis v6ib arvestada default valuutana
		if(!$curr)
		{
			$prod_info = $sum;
		}
		else
		{
			if(!is_array($prod_info))
			{
				$prod_info = array($this->get_default_currency($reservation) => $prod_info);
			}
			$prod_info[$curr] = $sum;
		}
		$reservation->set_meta("products_total_price" , $prod_info);
		$reservation->save();
		return 1;
	}

	/** sets products price for bron
		@attrib api=1 params=name
		@param reservation required type=object/oid
			reservation object
		@param products optional type=array
			products array([oid] => sum)
		@param product optional type=int
			product oid (if there is only one product)
		@param sum optiopal type=int
			product price sum (if there is only one product)
		@param curr optional type=oid
			currency object id
		@returns 1 - success , 0 - unsuccess
	**/
	function set_product_price($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return false;
		}
		$prod_info = $reservation->meta("products_price");
		if(!is_array($prod_info))
		{
			$prod_info = array();
		}
		if($sum && $product)
		{
			$products = array($product => $sum);
		}

		foreach($products as $product => $sum)
		{
			//kui m22ratakse kindla valuutaga summa, kui mitte, siis v6ib arvestada default valuutana
			if(!$curr)
			{
				$prod_info[$product] = $sum;
			}
			else
			{
				$prod_info[$product][$curr] = $sum;
			}
		}
		$reservation->set_meta("products_price" , $prod_info);
		$reservation->save();
		return 1;
	}

	/** sets reservation products discount
		@attrib api=1 params=name
		@param reservation required type=object/oid
		@param discount required type=double
		@returns 1 - success , 0 - unsuccess
	**/
	function set_products_discount($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return 0;
		}
		$reservation->set_prop("products_discount" , $discount);
		return 1;
	}

	/** sets reservation product discount
		@attrib api=1 params=name
		@param reservation required type=object/oid
		@param products required type=array
			array(prod1 => discount1 , prod2 => discount2 , ...)
		@returns 1 - success , 0 - unsuccess
	**/
	function set_product_discount($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return 0;
		}
		$discount = $reservation->meta("product_discount");
		if(!is_array($discount)) $discount = array();

		foreach($products as $p => $d)
		{
			if(isset($d))
			{
				$discount[$p] = $d;
			}
		}
		$reservation->set_meta("product_discount" , $discount);
		return 1;
	}

//siit alumised juba v6iks t88tada
//totaalse hinna ja allahindluse teema
	/**
		@attrib api=1 params=name
		@param reservation required type=object/oid
		@param curr optional type=oid
			currency object id
	**/
	function get_total_price($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return 0;
		}
		$sum = $reservation->meta("special_sum");
		if(is_array($sum))
		{
			if($curr)
			{
				return $sum[$curr];
			}
			else
			{
				return $sum[$this->get_default_currency];
			}
		}
		else
		{
			if(!$curr)
			{
				return $sum;
			}
			else
			{
				$c_inst = get_instance(CL_CURRENCY);
				return $c_inst->convert(array(
					"from" => $this->get_default_currency,
					"to" => $curr,
					"sum" => $sum,
				));
			}
		}
		return 0;
	}

	// - m22rab kogusumma
	/**
		@attrib api=1 params=name
		@param reservation required type=object/oid
			reservation object
		@param sum required type=int
			reservation total sum
		@param curr optional type=oid
			currency object id
		@returns 1 - success , 0 - unsuccess
	**/
	function set_total_price($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return 0;
		}

		//kui m22ratakse kindla valuutaga summa, kui mitte, siis v6ib arvestada default valuutana
		if(!$curr)
		{
			$reservation->set_meta("special_sum" , $sum);
		}
		else
		{
			$sum_array = $reservation->meta("special_sum");
			if(!is_array($sum_array))
			{
				$sum_array = array($this->get_default_currency($reservation) => $sum_array);
			}
			$sum_array[$curr] = $sum;
			$reservation->set_meta("special_sum" , $sum_array);
		}
		$reservation->save();
		return 1;
	}

	// - annab kogusumma sooduse (kui on)
	/**
		@attrib api=1 params=pos
		@param reservation required type=object/oid
	**/
	function get_total_discount($reservation)
	{
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return false;
		}
		return $reservation->meta("special_discount");
	}

	// - m22rab kogusumma sooduse
	/**
		@attrib api=1 params=pos
		@param reservation required type=object/oid
		@param discount required type=double
		@returns 1 - success , 0 - unsuccess
	**/
	function set_total_discount($reservation , $discount)
	{
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return 0;
		}
		$reservation->set_meta("special_discount" , $discount);
		$reservation->save();
		return 1;
	}

	//leiab default valuuta broneeringu jaoks
	//objekti annab kaasa selleks, et miskeid muid tingimusi 2kki broneeringul oleks kust valuuta v6tta
	/**
		@attrib api=1 params=pos
		@param reservation required type=object/oid
	**/
	function get_default_currency($reservation)
	{
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
			$curr = $reservation->prop("resource.location.owner.currency.id");
			if($curr)
			{
				return $curr;
			}
		}
		$curr_inst = get_instance(CL_CURRENCY);
		return $curr_inst->get_default_currency();
	}

	/**
		@attrib api=1 params=name
		@param reservation required type=object/oid
			reservation object
		@param product required type=oid
			product id
		@param curr optional type=oid
			price currency
	**/
	function get_product_price($arr)
	{
		extract($arr);
		if(is_oid($reservation) && $this->can("view" , $reservation))
		{
			$reservation = obj($reservation);
		}
		if(!is_object($reservation))
		{
			return false;
		}
		$this->c_inst = get_instance(CL_CURRENCY);
		//otsib siis k6igepealt broneeringu metast, siis vaatab edasi... a noh, et kui on valuuta m22ratud, siis proovib seda valuutat leida, v6i siis kui miski teine teema on ,siis h2kib ymber
		if($sum = $this->_get_product_price_from_meta(array(
			"product" => $product,
			"reservation" => $reservation,
			"curr" => $curr,
		)))
		{
			return $sum;
		}

		//l6puks siis produkti enda juurest
		if(is_oid($product) && $this->can("view" , $product))
		{
			return $this->_get_product_price_from_product($product , $reservation->id(), $curr);
		}
		else
		{
			return false;
		}
	}

	function _get_product_price_from_product($product, $r, $curr)
	{
		$p = obj($product);
		$prices = $p->meta("cur_prices");
		if(!$curr)
		{
			if($p->prop("price"))
			{
				return $p->prop("price");
			}
			if(array_key_exists($this->get_default_currency($r) , $prices))
			{
				return $prices[$this->get_default_currency($r)];
			}
			else return false;
		}
		else
		{
			if(array_key_exists($curr , $prices))
			{
				return $prices[$curr];
			}
			if(array_key_exists($this->get_default_currency($r) , $prices))
			{
				return $this->c_inst->convert(array(
					"from" => $this->get_default_currency($r),
					"to" => $curr,
					"sum" => $prices[$this->get_default_currency($r)],
				));
			}
			if($p->prop("price"))
			{
				return $this->c_inst->convert(array(
					"from" => $this->get_default_currency($r),
					"to" => $curr,
					"sum" => $p->prop("price"),
				));
			}
		}
		return false;
	}

	function _get_product_price_from_meta($arr)
	{
		extract($arr);
		$prices = $reservation->meta("products_price");
		if($prices[$product])
		{
			if(is_oid($curr))
			{
				if(is_array($prices[$product]))
				{
					if(array_key_exists($curr , $prices[$product]))
					{
						return $prices[$product][$curr];
					}
					if(array_key_exists($this->get_default_currency($reservation->id()) , $prices[$product]))
					{
						return $this->c_inst->convert(array(
							"from" => $this->get_default_currency($reservation->id()),
							"to" => $curr,
							"sum" => $prices[$product][$reservation->id()],
						));
					}
				}
				else
				{
					return $this->c_inst->convert(array(
						"from" => $this->get_default_currency($reservation->id()),
						"to" => $curr,
						"sum" => $prices[$product],
					));
				}
			}
			else
			{
				if(is_array($prices[$product]))
				{
					return $prices[$product][$this->get_default_currency($reservation->id())];
				}
				else
				{
					return $prices[$product];
				}
			}
		}
		else return false;
	}

	/** returns correct reservation name
		@attrib params=pos
		@param obj
			Reservation obj
	 **/
	public function get_correct_name($obj)
	{
		return sprintf(t("%s: %s / %s-%s %s"),
			$obj->prop("customer.name"),
			date("d.m.Y", $obj->prop("start1")),
			date("H:i", $obj->prop("start1")),
			date("H:i", $obj->prop("end")),
			$obj->prop("resource.name")
		);
	}
}
