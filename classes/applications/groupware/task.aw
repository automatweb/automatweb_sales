<?php

namespace automatweb;
/*

@classinfo syslog_type=ST_TASK confirm_save_data=1 maintainer=markop

@default table=planner
	@property hr_schedule_job type=hidden datatype=int
	@property customer_relation type=hidden datatype=int


@default table=objects

@default group=general
@layout top_bit type=vbox closeable=1 area_caption=P&otilde;hiandmed

	@layout top_2way type=hbox parent=top_bit

		@layout top_2way_left type=vbox parent=top_2way

			@property name type=textbox table=objects field=name parent=top_2way_left
			@caption Nimi

			@property balance type=hidden table=aw_account_balances field=aw_balance parent=top_2way_left

			@property comment type=textbox table=objects field=comment parent=top_2way_left
			@caption Kommentaar

			@property add_clauses type=chooser store=no parent=top_2way_left multiple=1
			@caption Lisatingimused

		@layout top_2way_right type=vbox parent=top_2way

			@property start1 type=datetime_select field=start table=planner parent=top_2way_right
			@caption Algus

			@property end type=datetime_select table=planner parent=top_2way_right
			@caption L&otilde;peb

			@property deadline type=datetime_select table=planner field=deadline parent=top_2way_right
			@caption T&auml;htaeg


@layout center_bit type=vbox parent=top_bit

	@property hrs_table type=table no_caption=1 store=no parent=center_bit

	@property center_bit_vis type=hidden store=no no_caption=1 parent=center_bit


@layout content_bit type=vbox closeable=1 area_caption=Sisu
	@property content type=textarea cols=180 rows=30 field=description table=planner parent=content_bit no_caption=1
	@caption Sisu


@layout customer_bit type=vbox closeable=1 area_caption=Tellijad
#	@property parts_tb type=toolbar no_caption=1 store=no parent=customer_bit

	@property co_tb type=toolbar no_caption=1 store=no parent=customer_bit
	@property co_table type=table no_caption=1 store=no parent=customer_bit


#@layout bottom_pit type=vbox
#	@property parts_tb type=toolbar no_caption=1 store=no parent=bottom_pit

#	@property co_table type=table no_caption=1 store=no parent=bottom_pit
#	@property proj_table type=table no_caption=1 store=no parent=bottom_pit
#	@property parts_table type=table no_caption=1 store=no parent=bottom_pit

@layout project_bit type=vbox closeable=1 area_caption=Projektid
	@property project_tb type=toolbar no_caption=1 store=no parent=project_bit
	@property proj_table type=table no_caption=1 store=no parent=project_bit

@layout impl_bit type=vbox closeable=1 area_caption=Osalejad
	@property impl_tb type=toolbar no_caption=1 store=no parent=impl_bit
	@property parts_table type=table no_caption=1 store=no parent=impl_bit

@layout files_bit type=vbox closeable=1 area_caption=Manused
	@property files_tb type=toolbar no_caption=1 store=no parent=files_bit
	@property files_table type=table no_caption=1 store=no parent=files_bit

@layout bills_bit type=vbox closeable=1 area_caption=Arved
	@property bills_tb type=toolbar no_caption=1 store=no parent=bills_bit
	@property bills_table type=table no_caption=1 store=no parent=bills_bit

#@layout files_pit type=vbox
#	@property files_tb type=toolbar no_caption=1 store=no parent=files_pit
#	@property files_table type=table no_caption=1 store=no parent=files_pit

@property ppa type=hidden store=no no_caption=1
@property customer type=relpicker table=planner field=customer reltype=RELTYPE_CUSTOMER
@caption Klient

@property project type=relpicker table=planner field=project reltype=RELTYPE_PROJECT
@caption Projekt

@property is_done type=checkbox field=flags method=bitmask ch_value=8 // OBJ_IS_DONE
@caption Tehtud


@layout personal type=hbox
@caption Kestab terve p&auml;eva

	@property whole_day type=checkbox ch_value=1 field=meta method=serialize parent=personal no_caption=1

	@property is_personal type=checkbox ch_value=1 field=meta method=serialize parent=personal no_caption=1
	@caption Isiklik

@property promoter type=checkbox ch_value=1 table=planner field=promoter
@caption Korraldaja

@property in_budget type=checkbox ch_value=1 table=planner field=aw_in_budget
@caption Eelarvesse

@property is_work type=checkbox ch_value=1 table=planner field=aw_is_work

@property service_type type=relpicker store=connect reltype=RELTYPE_SERVICE_TYPE
@caption Teenuse liik

@property priority type=textbox size=5 table=planner field=priority
@caption Prioriteet

@property num_hrs_guess type=textbox size=5 field=meta method=serialize
@caption Prognoositav tundide arv

@property num_hrs_real type=textbox size=5 field=meta method=serialize
@caption Tegelik tundide arv

@property num_hrs_to_cust type=textbox size=5 field=meta method=serialize
@caption Tundide arv kliendile

@layout hr_price_layout type=hbox no_caption=1
caption Tunnihind

	@property hr_price type=textbox size=5 field=meta method=serialize parent=hr_price_layout
	@caption Tunnihind

	@property hr_price_currency type=select field=meta method=serialize parent=hr_price_layout
	@caption Valuuta

@layout deal_price_layout type=hbox no_caption=1
caption Kokkuleppehind

	@property deal_unit type=textbox size=5 table=planner parent=hr_price_layout
	@caption &Uuml;hik

	@property deal_amount type=textbox size=5 table=planner parent=hr_price_layout
	@caption Kogus

	@property deal_price type=textbox size=5 table=planner parent=hr_price_layout
	@caption Kokkuleppehind

	@property deal_has_tax type=checkbox size=5 table=planner method=serialize parent=hr_price_layout
	@caption Sisestati koos k&auml;ibemaksuga

@property bill_no type=text table=planner
@caption Arve number

@property code type=hidden size=5 table=planner field=code
@caption Kood

@property participants type=popup_search multiple=1 table=objects field=meta method=serialize clid=CL_CRM_PERSON
@caption Osalejad

@property controller_disp type=text store=no
@caption Kontrolleri v&auml;ljund

property aliasmgr type=aliasmgr store=no
caption Seostehaldur

@default field=meta

@property task_toolbar type=toolbar no_caption=1 store=no group=participants method=serialize
@caption "Toolbar"

@property recurrence type=releditor reltype=RELTYPE_RECURRENCE group=recurrence rel_id=first props=start,recur_type,end,weekdays,interval_daily,interval_weekly,interval_montly,interval_yearly, method=serialize
@caption Kordused
@property calendar_selector type=calendar_selector store=no group=calendars method=serialize
@caption Kalendrid

@property other_selector type=other_calendar_selector store=no group=others no_caption=1 method=serialize
@caption Teised

@property project_selector type=project_selector store=no group=projects method=serialize
@caption Projektid

@property comment_list type=comments group=comments no_caption=1 method=serialize
@caption Kommentaarid

@property rmd type=reminder group=reminders store=no method=serialize
@caption Meeldetuletus

@property participant type=participant_selector store=no group=participants no_caption=1 method=serialize
@caption Osalejad

@property search_contact_company type=textbox store=no group=participants method=serialize
@caption Organisatsioon

@property search_contact_firstname type=textbox store=no group=participants method=serialize
@caption Eesnimi

@property search_contact_lastname type=textbox store=no group=participants method=serialize
@caption Perenimi

@property search_contact_code type=textbox store=no group=participants method=serialize
@caption Isikukood

@property search_contact_button type=submit store=no group=participants action=search_contacts method=serialize
@caption Otsi

@property search_contact_results type=table store=no group=participants no_caption=1 method=serialize
@caption Tulemuste tabel

@default group=other_exp
@default group=comments
//ajutiselt teine parent
	@property other_expenses type=table store=no no_caption=1 method=serialize

@property send_bill type=checkbox ch_value=1 table=planner field=send_bill
@caption Saata arve

@default group=rows

	@property rows_tb type=toolbar store=no no_caption=1 method=serialize
	@property rows type=table store=no no_caption=1 method=serialize
	@property rows_bottom type=text no_caption=1
	@property rows_oe type=text no_caption=1

@default group=resources

	@property sel_resources type=table no_caption=1 method=serialize

@default group=predicates
	@layout predicates_l type=hbox width=40%:60%
	@layout predicates_left type=vbox parent=predicates_l closeable=1 area_caption=Eeldustegevused no_padding=1

	@property predicates_tb type=toolbar parent=predicates_left no_caption=1
	@property predicates_table type=table store=no  no_caption=1 parent=predicates_left

	@layout predicates_right type=vbox parent=predicates_l

	@property predicates multiple=1 type=relpicker multiple=1 reltype=RELTYPE_PREDICATE store=connect field=meta parent=predicates_right method=serialize
	@caption Eeldustegevused

	@property is_goal type=checkbox ch_value=1 table=planner field=aw_is_goal method=
	@caption Verstapost

@groupinfo rows caption=Read
@groupinfo recurrence caption=Kordumine submit=no
@groupinfo calendars caption=Kalendrid
@groupinfo others caption=Teised submit_method=get
@groupinfo projects caption=Projektid
@groupinfo comments caption=Kommentaarid parent=other_exp
@groupinfo reminders caption=Meeldetuletused parent=other_exp
@groupinfo participants caption=Osalejad submit=no
@groupinfo other_exp caption="Muud kulud"
@groupinfo resources caption="Ressursid" parent=other_exp
@groupinfo predicates caption="Eeldused" parent=other_exp

@tableinfo planner index=id master_table=objects master_index=brother_of
@tableinfo aw_account_balances master_index=oid master_table=objects index=aw_oid

@reltype RECURRENCE value=1 clid=CL_RECURRENCE
@caption Kordus

@reltype FILE value=2 clid=CL_FILE
@caption Fail

@reltype CUSTOMER value=3 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype PROJECT value=4 clid=CL_PROJECT
@caption Projekt

@reltype RESOURCE value=5 clid=CL_MRP_RESOURCE
@caption Ressurss

@reltype BILL value=6 clid=CL_CRM_BILL
@caption Arve

@reltype ROW value=7 clid=CL_TASK_ROW
@caption Rida

@reltype ATTACH value=8 clid=CL_CRM_MEMO,CL_CRM_DEAL,CL_CRM_DOCUMENT,CL_CRM_OFFER
@caption Manus

@reltype PREDICATE value=9 clid=CL_TASK,CL_CRM_CALL
@caption Eeldustegevus

@reltype EXPENSE value=10 clid=CL_CRM_EXPENSE
@caption Muu kulu

@reltype SERVICE_TYPE value=11 clid=CL_CRM_SERVICE_TYPE
@caption Teenuse liik
*/


define("STOPPER_PAUSED", 1);
define("STOPPER_RUNNING", 2);

class task extends class_base
{
	const AW_CLID = 244;

	protected $add_to_proj;
	protected $post_save_add_parts = array();

	function task()
	{
		$this->init(array(
			"tpldir" => "groupware/task",
			"clid" => CL_TASK,
		));

		$this->default_stoppers = array(
			CL_TASK,
			CL_CRM_CALL,
			CL_CRM_MEETING,
			CL_BUG,
			CL_TASK_ROW,
		);

		$this->stopper_states = array(
			STOPPER_PAUSED => t("Seisab"),
			STOPPER_RUNNING => t("Stopper k&auml;ib"),
		);
	}

        function get_partipicants($arr)
        {

               $p = array();
                if ($this->can("view", $arr["request"]["alias_to_org"]))
                {
                        $ao = obj($arr["request"]["alias_to_org"]);
                        if ($ao->class_id() == CL_CRM_PERSON)
                        {
                                $p[$ao->id()] = $ao->id();
                        }
                }
                if(is_object($arr['obj_inst']) && is_oid($arr['obj_inst']->id()))
                {
                        $conns = $arr['obj_inst']->connections_to(array(
                                'type' => array(10, 8),//CRM_PERSON.RELTYPE_PERSON_TASK==10
                        ));
                        foreach($conns as $conn)
                        {
                                $obj = $conn->from();
                                $p[$obj->id()] = $obj->id();
                        }
                }
                return $p;
        }

	/**
		@attrib name=stopper_pop all_args=1
		@param id optional
		@param s_action optional
		@param type optional
		@param name optional
		@param desc optional
		@param source optional
		@param ident optional
		@param null_stos optional
	**/
	function stopper_pop($arr)
	{
		$this->read_template("stopper_pop.tpl");

		$ui = new user();
		$u = obj($ui->get_current_user());
		$stos = $this->get_stos($u);
		$ret = $this->_proc_stop_act($arr, $stos);
		$stos = ($arr["null_stos"] == 1)?array():$stos; /// for developing time only!!
		$u->set_prop("stoppers", aw_serialize($stos, SERIALIZE_NATIVE));
		$u->save();
		if ($ret)
		{
			header("Location:". $ret);
			$post = "<script language='javascript'>window.opener.reload();</script>";
		}

		$s = "";
		$num = 0;
		if (count($stos) < 1)
		{
			if ($post != "")
			{
				header("Location: ".aw_ini_get("baseurl")."/automatweb/closewin.html");
			}
			else
			{
				header("Location: ".aw_ini_get("baseurl")."/automatweb/closewin_no_r.html");
			}
			die();
		}

		$html = $this->draw_alternative_stoppers($stos, $arr);
		return $html;
	}


	function stopper_autocomplete($requester, $params)
	{
		switch($requester)
		{
			case "send_bill":
				if($arr["new"])
				{
					$prop["value"] = 1;
				}
				break;
			case "part":
				$l = new object_list(array(
					"class_id" => CL_CRM_PERSON,
				));
				foreach($l->arr() as $obj)
				{
					$ret[$obj->id()] = $obj->name();
				}
			break;
			case "project":

				if(strlen($params["part"]))
				{
					$parts = split(",", $params["part"]);

					$c = new connection();
					$conns = $c->find(array(
						"from.class_id" => CL_PROJECT,
						"to" => $parts,
					));
					foreach($conns as $conn)
					{
						$p = obj($conn["from"]);
						$ret[$p->id()] = $p->name();
					}
				}
				else
				{
					$l = new object_list(array(
						"class_id" => CL_PROJECT,
					));
					foreach($l->arr() as $obj)
					{
						$ret[$obj->id()] = $obj->name();
					}

				}
			break;
			default:
				$ret = array();
				break;
		}
		return $ret;
	}

	function gen_stopper_addon($fafa)
	{
		$props = array(
			array(
				"name" => "name",
				"type" => "textbox",
				"caption" => t("Nimi"),
			),
			array(
				"name" => "part",
				"type" => "textbox",
				"caption" => t("Osaleja"),
				"autocomplete" => true,
			),
			array(
				"name" => "project",
				"type" => "textbox",
				"caption" => t("Projekt"),
				"autocomplete" => true,
			),
			array(
				"name" => "isdone",
				"type" => "checkbox",
				"caption" => t("Tehtud"),
				"ch_value" => 1,
				"value" => 1,
			),
			array(
				"name" => "tobill",
				"type" => "checkbox",
				"caption" => t("Arvele"),
			),
			array(
				"name" => "desc",
				"type" => "textarea",
				"caption" => t("Kirjeldus"),
			),
		);
		return $props;
	}


	function gen_existing_stopper_addon($fafa)
	{
		$inst = get_instance(CL_TASK_ROW);
		$fu = $inst->gen_stopper_addon($fafa);
		foreach($fu as $k => $v)
		{
			if($v["name"] == "task")
			{
				unset($fu[$k]);
			}
		}
		return $fu;
	}

	function draw_alternative_stoppers($stoppers, $arr)
	{
		$htmlclient = get_instance("cfg/htmlclient");
		$htmlclient->start_output(array(
			"template" => "default"
		));
		$layoutinfo = array(
			"main_layout" => array(
				"type" => "vbox",
				"no_caption" => 1,
			)
		);
		// this upper_layout is here for a reason, so dont you even try to remove this..
		$layoutinfo["upper_layout"] = array(
			"type" => "vbox",
			"no_caption" => 1,
			"parent" => "main_layout",
		);

		$cls = aw_ini_get("classes");
		foreach($stoppers as $ident => $stopper)
		{
			if ($stopper["state"] == STOPPER_RUNNING)
			{
				$el = (time() - $stopper["start"]) + $stopper["base"];
			}
			else
			{
				$el = $stopper["base"];
			}
			$elapsed_hr = (int)($el / 3600);
			$elapsed_min = (int)(($el - $elapsed_hr * 3600) / 60);
			$elapsed_sec = (int)($el - ($elapsed_hr * 3600 + $elapsed_min * 60));

			$layoutinfo["sto_".$ident."_area"] = array(
				"type" => "vbox",
				"closeable" => 1,
				"area_caption" => sprintf(t("Stopper '%s'"), $stopper["name"]),
				"parent" => "main_layout",
			);
			if($this->stop_error[$ident])
			{
				$htmlclient->add_property(array(
					"name" => "error",
					"parent" => "sto_".$ident."_area",
					"type" => "text",
					"no_caption" => 1,
					"value" => "<div style=\"border:1px solid red;padding:3px;\">".$this->stop_error[$ident]."</div>",
				));
				unset($this->stop_error[$ident]);
			}
			$layoutinfo["sto_".$ident."_hbox"] = array(
				"type" => "hbox",
				"parent" => "sto_".$ident."_area",
			);
			$layoutinfo["sto_".$ident."_left"] = array(
				"type" => "vbox",
				"parent" => "sto_".$ident."_hbox",
			);
			$layoutinfo["sto_".$ident."_right"] = array(
				"type" => "vbox",
				"parent" => "sto_".$ident."_hbox",
			);
			$htmlclient->add_property(array(
				"name" => "stop_".$ident."_name",
				"caption" => t("Nimi"),
				"type" => "text",
				"value" => $stopper["name"],
				"parent" => "sto_".$ident."_left",
			));
			if($stopper["type"])
			{
				$htmlclient->add_property(array(
					"name" => "stop_".$ident."_type",
					"caption" => t("T&uuml;&uuml;p"),
					"type" => "text",
					"value" => $cls[$stopper["type"]]["name"],
					"parent" => "sto_".$ident."_left",
				));
			}
			else
			{
				unset($opts);
				$opts[0] = t("-- Vali t&uuml;&uuml;p --");
				foreach($this->default_stoppers as $clid)
				{
					$opts[$clid] = $cls[$clid]["name"];
				}
				$url = $this->mk_my_orb("change_stop_type", array(
					"ident" => $ident,
				));
				$htmlclient->add_property(array(
					"name" => "stop_".$ident."_type",
					"caption" => t("T&uuml;&uuml;p"),
					"type" => "select",
					"options" => $opts,
					"parent" => "sto_".$ident."_left",
					"onchange" => "javascript:aw_get_url_contents('".$url."&type=' + document.getElementById('stop_".$ident."_type').value); document.location.reload();"
				));
			}

			if($stopper["type"] && !$this->can("view", $stopper["oid"]))
			{
				$search_butt = new popup_search();
				$sb = $search_butt->get_popup_search_link(array(
					"pn" => "searched_oid[".$ident."]",
					"multiple" => false,
					"clid" => $stopper["type"],
				));
				$htmlclient->add_property(array(
					"parent" => "sto_".$ident."_left",
					"name" => "search_butt",
					"type" => "text",
					"caption" => t("Otsi olemasolev objekt"),
					"value" => $sb,
				));
				$htmlclient->add_property(array(
					"parent" => "sto_".$ident."_left",
					"name" => "searched_oid[".$ident."]",
					"type" => "hidden",
					"no_caption" => 1,
				));
			}

			$htmlclient->add_property(array(
				"name" => "stop_".$ident."_time",
				"caption" => t("Aeg"),
				"type" => "text",
				"value" => "<div id=\"stopdiv_".$ident."_time\">".sprintf("%02d:%02d:%02d",$elapsed_hr,$elapsed_min, $elapsed_sec)."</div>",
				"parent" => "sto_".$ident."_left",
			));
			// top secret property for js stops array
			if($stopper["state"] == STOPPER_RUNNING)
			{
				$js = '<script language="javascript">
			stops['.$ident.'] = new Array('.$elapsed_hr.','.$elapsed_min.','.$elapsed_sec.');
			</script>';
				$htmlclient->add_property(array(
					"name" => "stop_".$ident."_js_time",
					"no_caption" => 1,
					"type" => "text",
					"value" => $js,
					"parent" => "sto_".$ident."_left",
				));
			}
			$htmlclient->add_property(array(
				"name" => "stop_".$ident."_state",
				"caption" => t("Staatus"),
				"type" => "text",
				"value" => $this->stopper_states[$stopper["state"]],
				"parent" => "sto_".$ident."_left",
			));
			unset($actions);

			if($stopper["state"] == STOPPER_RUNNING)
			{
				$actions[] = html::href(array(
					"caption" => t("Paus"),
					"url" => "javascript:document.changeform.ident.value = \"".$ident."\";document.changeform.s_action.value = \"pause\"; submit_changeform(\"stopper_pop\");",
				));
			}
			else
			{
				$actions[] = html::href(array(
					"caption" => t("Start"),
					"url" => "javascript:document.changeform.ident.value = \"".$ident."\";document.changeform.s_action.value = \"start\"; submit_changeform(\"stopper_pop\");",
				));
			}
			$actions[] = html::href(array(
				"caption" => t("L&otilde;peta"),
				"url" => "javascript:document.changeform.ident.value = \"".$ident."\";document.changeform.s_action.value = \"stop\"; submit_changeform(\"stopper_pop\");",
			));
			$actions[] = html::href(array(
				"caption" => t("Kustuta"),
				"url" => $this->mk_my_orb("stopper_pop", array(
					"ident" => $ident,
					"s_action" => "del",
				)),
			));

			$htmlclient->add_property(array(
				"type" => "hidden",
				"no_caption" => 1,
				"name" => "ident",
			));
			$htmlclient->add_property(array(
				"type" => "hidden",
				"no_caption" => 1,
				"name" => "s_action",
			));

			$htmlclient->add_property(array(
				"name" => "stop_".$ident."_actions",
				"caption" => t("Tegevused"),
				"type" => "text",
				"parent" => "sto_".$ident."_left",
				"value" => join(" | ", $actions),
			));
			if($stopper["type"])
			{

				$i = get_instance($stopper["type"]);
				$method = $this->can("view", $stopper["oid"])?"gen_existing_stopper_addon":"gen_stopper_addon";
				$props = method_exists($i, $method)?$i->$method($stopper):array();
				unset($params);
				foreach($props as $prop)
				{
					$params[] = "stopdata_".$ident."_".$prop["name"];
				}
				foreach($props as $prop)
				{
					$prop_orig_name = $prop["name"];
					$prop["name"] = "stopdata_".$ident."_".$prop["name"];

					if($prop["autocomplete"] || isset($prop["autocomplete_delimiters"]))
					{
						$prop["autocomplete_params"] = $params;
						$prop["option_is_tuple"] = true;
						$prop["autocomplete_source"] = $this->mk_my_orb("provide_addon_data", array(
							"class" => "task",
							"requester_class" => $stopper["type"],
						));

						//$prop["content"] = $stopper["data"][$prop_orig_name]["caption"]?$stopper["data"][$prop_orig_name]["caption"]:$stopper["data"][$prop_orig_name]["value"];
						if(strlen($stopper["data"][$prop_orig_name]["caption"]))
						{
							$val = split(",", $stopper["data"][$prop_orig_name]["value"]);
							$cap = split(",", $stopper["data"][$prop_orig_name]["caption"]);
							foreach($val as $k => $val)
							{
								$prop["selected"][$val] = $cap[$k];
							}
						}
					}
					if($prop["type"] == "checkbox")
					{
						// this little thingie here helps dha checkbox phenomen
						$prop["pre_append_text"] = html::hidden(array(
							"name" => $prop["name"],
							"value" => 0,
						));
					}
					$prop["value"] = strlen($prop["value"])?$prop["value"]:$stopper["data"][$prop_orig_name]["value"];
					$prop["selected"] = strlen($prop["selected"])?$prop["selected"]:$stopper["data"][$prop_orig_name]["value"];
					$prop["parent"] = "sto_".$ident."_right";
					/*
					if($prop["value"])
					{
						$correct_prop_hidden = $prop;
						$correct_prop_hidden["type"] = "hidden";
						$correct_prop_hidden["no_caption"] = 1;
						$htmlclient->add_property($correct_prop_hidden);

						$prop["name"] = $prop["name"]."_fake";
						$prop["disabled"] = 1;

						//$prop["type"] = "hidden";
						//$prop["no_caption"] = 1;
					}
					*/
					$htmlclient->add_property($prop);
				}
			}

		}
		$htmlclient->add_property(array(
			"name" => "general_javascript_beginning",
			"type" => "text",
			"no_caption" => 1,
			"value" => "<script>var stops = new Array(); </script>",
			"parent" => "upper_layout",
		));
		$htmlclient->add_property(array(
			"name" => "general_javascript",
			"type" => "text",
			"no_caption" => 1,
			"value" => $this->gimmi_stopper_js(),
		));
		$htmlclient->set_layout($layoutinfo);
		$htmlclient->finish_output(array(
			"action" => "stopper_pop",
			"method" => "GET",
			"data" => array(
				"class" => "task",
			),
		));
		return $htmlclient->get_result(array(
			"form_only" => 1,
		));
	}

	function gimmi_stopper_js()
	{

		return '<script language="javascript">
	var thisdate = new Date();
	var timestamp_start = thisdate.getTime();
	stops_begin = stops;
	$.timer(1000, function (timer) {
		var thisdate = new Date();
		duration = (((thisdate.getTime() - timestamp_start) /1000).toFixed(0))*1.0;
		for(stopKey in stops)
		{
			start_duration = (stops[stopKey][0]*60*60+stops[stopKey][1]*60+stops[stopKey][2])*1.0;
			el = $(\'#stopdiv_\'+stopKey+\'_time\').html(_return_normal_clock(start_duration+duration));
		}
	});

	function _return_normal_clock(seconds)
	{
		var s = seconds;
		var m = h = 0;
		while (s>59)
		{
			s -= 60;
			m++;
		}
		while (m>59)
		{
			m -= 60;
			h++;
		}
		if (h < 10)
		{
			h = "0"+h;
		}
		if (m < 10)
		{
			m = "0"+m;
		}
		if (s < 10)
		{
			s = "0"+s;
		}
		return h+":"+m+":"+s;
	}
	</script>';
	}


	/**
		@attrib name=provide_addon_data all_args=1
	**/
	function provide_addon_data($arr)
	{
		$spl = split("_", $arr["requester"]);
		$requester_stop = $spl[1];
		$requester_prop = $spl[2];
		foreach($arr as $k => $v)
		{
			$spl = split("_", $k);
			if($spl[0] == "stopdata" && $spl[1] == $requester_stop)
			{
				$params[$spl[2]] = $v;
			}

			$fa[++$i] = $k."=>".$v;
		}
		$inst = get_instance($arr["requester_class"]);
		$opt = $inst->stopper_autocomplete($requester_prop, $params);
		foreach($opt as $k => $v)
		{
			$opt[$k] = str_replace(";", ",", $v);
		}

		$ret = array(
			"options" => $opt,
		);
		$json = get_instance("protocols/data/json");
		die($json->encode($ret));
	}

	function callback_get_default_group($arr)
	{
		$seti = get_instance(CL_CRM_SETTINGS);
		$sts = $seti->get_current_settings();
		if ($sts && $sts->prop("view_task_rows_open") && ($_GET["action"] != "new"))
		{
			return "rows";
		}
		return "general";
	}

	function callback_on_load($arr)
	{
		if(isset($arr["request"]["msgid"]) && $arr["request"]["msgid"])
		{
			$mail = get_instance(CL_MESSAGE);
			$this->mail_data = $mail->fetch_message(Array(
				"mailbox" => "INBOX" ,
				"msgrid" => $arr["request"]["msgrid"],
				"msgid" => $arr["request"]["msgid"],
				"fullheaders" => "",
			));
		}
		$u = new user();
		$this->co = $u->get_current_company();
		$this->person = $u->get_current_person();
	}

	function callback_mod_layout(&$arr)
	{
		$type = array(
			CL_TASK => t("Toimetusele"),
			CL_CRM_MEETING => t("Kohtumisele"),
			CL_CRM_CALL => t("K&otilde;nele"),
			CL_CRM_PRESENTATION => t("Esitlusele")
		);
		switch($arr["name"])
		{
			case "customer_bit":
				$arr["area_caption"] = sprintf(t("Tellijad: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->prop_xml("name"));
				break;
			case "project_bit":
				$arr["area_caption"] = sprintf(t("Projektid: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->prop_xml("name"));
				break;
			case "impl_bit":
				$arr["area_caption"] = sprintf(t("Osalejad: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->prop_xml("name"));
				break;
 			case "files_bit":
				$arr["area_caption"] = sprintf(t("Manused: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->prop_xml("name"));
				break;
			case "bills_bit":
				$arr["area_caption"] = sprintf(t("Arved: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->prop_xml("name"));
				break;
		}
		return true;
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];

		if (is_object($arr["obj_inst"]) && $arr["obj_inst"]->prop("is_personal") && aw_global_get("uid") != $arr["obj_inst"]->createdby())
		{
			if (!($arr["prop"]["name"] == "start1" || $arr["prop"]["name"] == "end" || $arr["prop"]["name"] == "deadline"))
			{
				return PROP_IGNORE;
			}
		}
		if (!is_object($arr["obj_inst"]))
		{
			$arr["obj_inst"] = obj();
		}
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "comment":
				if ($data["type"] !== "textarea")
				{
					$data["type"] = "textarea";
					$data["rows"] = 2;
					$data["cols"] = 30;
				}
				break;

			case "predicates":
				return PROP_IGNORE;
				break;

			case "priority":
			case "bill_no":
			case "deal_price":
			case "deal_has_tax":
			case "deal_unit":
			case "deal_amount":
			case "num_hrs_guess":
			case "num_hrs_real":
			case "num_hrs_to_cust":
			case "is_done":
			case "status":
			case "whole_day":
			case "is_goal":
			case "is_personal":
			case "hr_price_currency":
			case "in_budget":
			case "service_type":
			case "is_work":
			case "promoter":
				return PROP_IGNORE;

			case "controller_disp":
				$cs = get_instance(CL_CRM_SETTINGS);
				$pc = $cs->get_task_controller($cs->get_current_settings());
				if ($this->can("view", $pc))
				{
					$pco = obj($pc);
					$pci = $pco->instance();
					$prop["value"] = $pci->eval_controller($pc, $arr["obj_inst"]);
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "content":
				$data["style"] = "width: 100%";
				if(isset($this->mail_data))
				{
					$data["value"] = sprintf(
					"From: %s\nTo: %s\nSubject: %s\nDate: %s\n\n%s",
						$this->mail_data["from"],
						$this->mail_data["to"],
						$this->mail_data["subject"],
						$this->mail_data["date"],
						$this->mail_data["content"]);
					break;
				}
				break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "end":
				if(date_edit::get_timestamp($arr["request"]["start1"]) > date_edit::get_timestamp($prop["value"]))
				{
					$prop["value"] = $arr["request"]["start1"];
					$arr["request"]["end"] = $arr["request"]["start1"];
				}
				break;

			case "is_done":
			case "status":
			case "whole_day":
			case "is_goal":
			case "is_personal":
			case "in_budget":
			case "service_type":
			case "is_work":
			case "promoter":
				return PROP_IGNORE;
		}
		return $retval;
	}

	function _get_currencys()
	{
		$data = array();
		$curr_object_list = new object_list(array(
                      "class_id" => CL_CURRENCY,
                ));
                foreach($curr_object_list->arr() as $curr)
                {
                	 $data[$curr->id()] = $curr->name();
                }
		return $data;
	}

	/**
		@attrib name=error_popup
		@param text optional
	**/
	function error_popup($arr)
	{
		return $arr["text"];
	}

	/**
		@attrib name=search_for_proj
		@param retf optional
	**/
	function search_for_proj($arr)
	{
	}

	function __com_filt_comp($key, $str, $row)
	{
		if (!is_oid($row["oid"]))
		{
			return true;
		}
		if ($str == t("Jah") && !$row["comments_cnt"])
		{
			return false;
		}
		if ($str == t("Ei") && $row["comments_cnt"])
		{
			return false;
		}
		return true;
	}

	function __done_filt_comp($key, $str, $row)
	{
		if (!is_oid($row["oid"]))
		{
			return true;
		}
		if ($str == t("Jah") && !$row["done_val"])
		{
			return false;
		}
		if ($str == t("Ei") && $row["done_val"])
		{
			return false;
		}
		return true;
	}

	function __bill_filt_comp($key, $str, $row)
	{
		$_SESSION["task"]["__bill_filt_comp"] = array($key, $str, $row);
		if (!is_oid($row["oid"]))
		{
			return true;
		}
		if ($str == t("Arveta") && $row["bill_val"] != "billed")
		{
			return true;
		}
		else
		if ($str == t("Arveta"))
		{
			return false;
		}

		if ($str == t("Arvel") && $row["bill_val"] != "billed")
		{
			return false;
		}
		if ($str == t("Jah") && ($row["bill_val"] == 0 || $row["bill_val"] == "billed"))
		{
			return false;
		}
		if ($str == t("Ei") && ($row["bill_val"] == 1 || $row["bill_val"] == "billed"))
		{
			return false;
		}
		return true;
	}

	function __impl_filt_comp($key, $str, $row)
	{
		if (!is_oid($row["oid"]))
		{
			return true;
		}
		return in_array($str, $row["impl_val"]);
	}

	function __ord_format($val)
	{
		$this->visible_rows_sum += $val["sum_val"];
		if($val["date_val"])
		{
			return html::textbox(array(
						"name" => "rows[".$val["idx"]."][ord]",
						"value" => $val["ord_val"],
						"size" => 3,
			)).$this->__date_format($val);
		}
	}


	function __time_format($val)
	{
		if($val["result_sum"])
		{
			if(is_oid($val["task_object"]->prop("hr_price_currency")))
			{
				$sad = obj($val["task_object"]->prop("hr_price_currency"));
				$curr = $sad->name();
			}
			if($val["task_object"]->prop("deal_price"))
			{
				$sum = $val["task_object"]->prop("deal_amount");
				$cash = $val["task_object"]->prop("deal_price");
			}
			else
			{
				$sum = $this->visible_rows_sum;
				$cash = $sum * $val["task_object"]->prop("hr_price");
			}
			$unit = $val["task_object"]->prop("deal_unit");
			//defauldiks oleks tunnid
			if($unit) $unit = t("h");
			return "<b>".t("Kokku:").$sum." ".$unit." (".$cash." ".$curr.")";
		}
		return $val["time"];
	}

	function __date_format($val)
	{
		if($val["date_val"])
		{
			if($this->can("view" , $val["bill_id"]))
			{
				return $val["date_val"];
			}
			return html::textbox(array(
					"name" => "rows[".$val["idx"]."][date]",
					"value" => $val["date_val"],
					"size" => 7
			)).$val["date_sel"];
		}
	}

	function __id_format($val)
	{
		return " ";
	}

	/**
		@attrib name=del_file_rel
		@param fid required
		@param return_url optional
	**/
	function del_file_rel($arr)
	{
		$f = obj($arr["fid"]);
		$ff = $f->get_first_obj_by_reltype("RELTYPE_FILE");
		if ($ff)
		{
			$ff->delete();
		}
		$f->delete();
		return $arr["return_url"];
	}

	function _req_get_folders($ot, &$folders, $parent)
	{
		$this->_req_level++;
		$objs = $ot->level($parent);
		foreach($objs as $o)
		{
			$folders[$o->id()] = str_repeat("&nbsp;&nbsp;&nbsp;", $this->_req_level).$o->name();
			$this->_req_get_folders($ot, $folders, $o->id());
		}
		$this->_req_level--;
	}

	/**
		@attrib name=copy_task_rows all_args=1
	**/
	function copy_task_rows($arr)
	{
		$_SESSION["task_rows"] = null;
		$_SESSION["task_rows"]["sel"] = $arr["sel"];
		return $arr["post_ru"];
	}

	/**
		@attrib name=cut_task_rows all_args=1
	**/
	function cut_task_rows($arr)
	{
		$_SESSION["task_rows"] = null;
		$_SESSION["task_rows"]["sel"] = $arr["sel"];
		$_SESSION["task_rows"]["remove_conn"] = $arr["id"];
		return $arr["post_ru"];
	}

	/**
		@attrib name=paste_task_rows all_args=1
	**/
	function paste_task_rows($arr)
	{
		if(is_oid($_SESSION["task_rows"]["remove_conn"]) && $this->can("view" , $_SESSION["task_rows"]["remove_conn"]))
		{
			$rem_task = obj($_SESSION["task_rows"]["remove_conn"]);
		}
		$task = obj($arr["id"]);
		if(is_object($rem_task))
		{
			foreach($_SESSION["task_rows"]["sel"] as $row)
			{
				$rem_task->disconnect(array(
					"from" => $row,
					"reltype" => "RELTYPE_ROW",
				));
				$task->connect(array("to"=> $row, "type" => "RELTYPE_ROW"));
				$ro = obj($row);
				$ro->set_prop("task" , $task->id());
				$ro->save();
			}
		}
		else
		{
			$nc_props = array("on_bill", "bill_id" , "to_bill_date");
			foreach($_SESSION["task_rows"]["sel"] as $row)
			{
				if(!$this->can("view" , $row)) continue;
				$ro = obj($row);
//				$new_row = new object();
//				$new_row->set_class_id(CL_TASK_ROW);
//				$new_row->set_parent($ro->parent());

				$new_row = $task->add_row();
				$new_row->set_name($ro->name());

				foreach($ro->properties() as $prop => $val)
				{
					if($new_row->is_property($prop) && !in_array($prop,$nc_props))
					{
						$new_row->set_prop($prop , $val);
					}
				}
				$new_row->save();
				//metas ei n2i miskit kasulikku olevat... loodetavasti ka ei tule
//				$new_row->set_meta($ro->meta());
//				$new_row->save();
//				$task->connect(array("to"=> $new_row->id(), "type" => "RELTYPE_ROW"));
			}
		}
		$_SESSION["task_rows"] = null;
		return $arr["post_ru"];
	}

	function add_participant(object $task, object $person)
	{
		$pl = get_instance(CL_PLANNER);
		$person->connect(array(
			"to" => $task->id(),
			"reltype" => "RELTYPE_PERSON_TASK"
		));

		// also add to their calendar
		if (($cal = $pl->get_calendar_for_person($person)))
		{
			$pl->add_event_to_calendar(obj($cal), $task);
		}
	}

	function _set_resources($arr)
	{
		if($arr["obj_inst"]->id() > 0)
		{
			$sel_res = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_RESOURCE")));
		}
		else
		{
			$sel_res = new object_list();
		}
		$sel_ids = array_flip($sel_res->ids());

		$sbt = safe_array($arr["request"]["sel"]);
		foreach($sbt as $_id => $one)
		{
			if (!isset($sel_ids[$_id]))
			{
				$arr["obj_inst"]->connect(array(
					"to" => $_id,
					"type" => "RELTYPE_RESOURCE"
				));
			}
		}

		foreach($sel_ids as $_id => $b)
		{
			if (!isset($sbt[$_id]))
			{
				$arr["obj_inst"]->disconnect(array(
					"from" => $_id
				));
			}
		}
	}

	function new_change($arr)
	{
		aw_session_set('org_action',aw_global_get('REQUEST_URI'));
		return parent::new_change($arr);
	}


	function get_stos($u = false)
	{
		static $cur_u;
		if (!$cur_u)
		{
			$cur_u = obj(aw_global_get("uid_oid"));
		}
		$u = $u ? $u : $cur_u;
		return aw_unserialize($u->prop("stoppers"));
	}

	// seda peaks timmima veidi, crm_company_overview_impl'is ~600 rea juures kysitakse l2bi selle fun'i objekti id'ga, mitte alustusaja j2rgi nagu nyyd o
	function stopper_is_running($task_id)
	{
		$stos = $this->get_stos();
		return $stos[$task_id]["state"] == STOPPER_RUNNING;
	}

	function get_stopper_time($task_id)
	{
		$stos = $this->get_stos();
		$elapsed = time() - $stos[$task_id]["start"];
		return $stos[$task_id]["base"] + $elapsed;
	}

	/**
		@attrib name=change_stop_type
		@param ident optional type=int
		@param type optional type=int
	**/
	function change_stop_type($arr)
	{
		$ui = new user();
		$u = obj($ui->get_current_user());
		$stos = $this->get_stos($u);
		$stos[$arr["ident"]]["type"] = $arr["type"];
		$u->set_prop("stoppers", aw_serialize($stos, SERIALIZE_NATIVE));
		$u->save();
		header("Location:".$this->mk_my_orb("stopper_pop", array(
			"new" => 1,
		), CL_TASK));
	}

	/**
		@attrib name=search_contacts
	**/
	function search_contacts($arr)
	{
		return $this->mk_my_orb('change',array(
				'id' => $arr['id'],
				'group' => $arr['group'],
				'search_contact_company' => ($arr['search_contact_company']),
				'search_contact_firstname' => ($arr['search_contact_firstname']),
				'search_contact_lastname' => ($arr['search_contact_lastname']),
				'search_contact_code' => ($arr['search_contact_code']),
				"return_url" => $arr["return_url"]
			),
			$arr['class']
		);
	}

	/**

		@attrib name=save_participant_search_results

	**/
	function save_participant_search_results($arr)
	{
	}

	/**

      @attrib name=submit_delete_participants_from_calendar
      @param id required type=int acl=view

	**/
	function submit_delete_participants_from_calendar($arr)
	{
		post_message_with_param(
			MSG_MEETING_DELETE_PARTICIPANTS,
			CL_CRM_MEETING,
			&$arr
		);
		return $arr['post_ru'];
	}

	function callback_mod_reforb(&$arr)
	{
		$arr["predicates"] = 0;
		$arr["post_ru"] = post_ru();
		$arr["participants_h"] = 0;
		$arr["orderer_h"] = 0;
		$arr["orderer_h"] = isset($_GET["alias_to_org"]) ? $_GET["alias_to_org"] : 0;
		$arr["project_h"] = isset($_GET["set_proj"]) ? $_GET["set_proj"] : 0;
		$arr["files_h"] = 0;
		if ($_GET["action"] == "new")
		{
			$arr["add_to_cal"] = $_GET["add_to_cal"];
			$arr["alias_to_org"] = $_GET["alias_to_org"];
			$arr["reltype_org"] = $_GET["reltype_org"];
			$arr["set_pred"] = $_GET["set_pred"];
			$arr["set_resource"] = $_GET["set_resource"];
		}
	}

	function _req_get_s_folders($fld, $fldo, &$folders, $parent)
	{
		$this->_lv++;
		foreach($fld as $dat)
		{
			if ($dat["parent"] === $parent)
			{
				$folders[$fldo->id().":".$dat["id"]] = str_repeat("&nbsp;&nbsp;&nbsp;", $this->_lv).iconv("utf-8", aw_global_get("charset")."//IGNORE", $dat["name"]);
				$this->_req_get_s_folders($fld, $fldo, $folders, $dat["id"]);
			}
		}
		$this->_lv--;
	}

	/**
		@attrib name=get_proj_for_cust
		@param cust optional
	**/
	function get_proj_for_cust($arr)
	{
		if (!$arr["cust"])
		{
			$i = get_instance(CL_CRM_COMPANY);
			$prj = $i->get_my_projects();
			if (!count($prj))
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list(array("oid" => $prj, "lang_id" => array(), "site_id" => array()));
			}
		}
		else
		{
			$filt = array(
				"class_id" => CL_PROJECT,
				"CL_PROJECT.RELTYPE_PARTICIPANT" => $arr["cust"],
			);
			$ol = new object_list($filt);
		}
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"".aw_global_get("charset")."\" standalone=\"yes\"?>\n<response>\n";

		foreach($ol->names() as $id => $n)
		{
			$xml .= "<item><value>$id</value><text>$n</text></item>";
		}
		$xml .= "</response>";
		die($xml);
	}

	function callback_generate_scripts($arr)
	{
		$url = $this->mk_my_orb("get_proj_for_cust");
		$sc = !$arr["new"] ? "set_changed();" : "";
		return ($arr["new"] ? "disable_set_changed=1;":"").'
			function upd_proj_list()
			{
				'.$sc.'
				aw_do_xmlhttprequest("'.$url.'&cust="+document.changeform.customer.options[document.changeform.customer.selectedIndex].value, proj_fetch_callb);
			}

			function proj_fetch_callb()
			{
				if (req.readyState == 4)
				{
					// only if "OK"
					if (req.status == 200)
					{
						response = req.responseXML.documentElement;
						items = response.getElementsByTagName("item");
						aw_clear_list(document.changeform.project);
						aw_add_list_el(document.changeform.project, "", "'.t("--vali--").'");

						for(i = 0; i < items.length; i++)
						{
							value = items[i].childNodes[0].firstChild.data;
							text = items[i].childNodes[1].firstChild.data;
							aw_add_list_el(document.changeform.project, value, text);
						}
					}
					else
					{
						alert("There was a problem retrieving the XML data:\n" + req.statusText);
					}
				}
			}
		';
	}

	/**
		@attrib name=delete_task_rows
	**/
	function delete_task_rows($arr)
	{
		foreach(safe_array($arr["sel"]) as $s)
		{
			$o = obj($s);
			$o->delete();
		}
		return $arr["post_ru"];
	}


	function handle_stopper_stop($arr)
	{
		if(!$this->can("view", $arr["oid"]))
		{
			if($arr["data"]["name"]["value"] && $this->can("view", $arr["data"]["part"]["value"]) && $this->can("view", $arr["data"]["project"]["value"]))
			{
				$o = new object();
				$o->set_parent($arr["data"]["project"]["value"]);
				$o->set_name($arr["data"]["name"]["value"]);
				$o->set_class_id(CL_TASK);
				$o->set_prop("start1", $arr["first_start"]);

				$o->set_prop("is_done", $arr["data"]["isdone"]["value"]?1:0);
				$o->set_prop("send_bill", $arr["data"]["tobill"]["value"]?1:0);
				$o->set_prop("content", $arr["data"]["desc"]["value"]);

				$o->save();
				$person = obj($arr["data"]["part"]["value"]);
				$person->connect(array(
					"to" => $o->id(),
					"type" => "RELTYPE_PERSON_CALL",
				));
				$o->connect(array(
					"to" => $arr["data"]["project"]["value"],
					"type" => "RELTYPE_PROJECT",
				));

				$arr["oid"] = $o->id();
			}
			else
			{
				return false;
			}
		}

		$o = obj($arr["oid"]);
		$o->set_prop("num_hrs_real", $o->prop("nuh_hrs_real") + $arr["hours"]);
		$o->set_prop("num_hrs_to_cust", $o->prop("time_to_cust") + $arr["hours"]);
		$o->set_prop("end", time());

		$cp = get_current_person();

//		$row = obj();
//		$row->set_parent($o->id());
//		$row->set_class_id(CL_TASK_ROW);
		$row = $o->add_row();

		$row->set_prop("content", $arr["data"]["desc"]["value"]);
		$row->set_prop("date", $arr["start"]);
		$row->set_prop("impl", array($cp->id() => $cp->id()));

		$row->set_prop("time_guess", strlen($arr["data"]["timeguess"]["value"])?$arr["data"]["timeguess"]["value"]:$arr["hours"]);
		$row->set_prop("time_real", strlen($arr["data"]["timereal"]["value"])?$arr["data"]["timereal"]["value"]:$arr["hours"]);
		$row->set_prop("time_to_cust", strlen($arr["data"]["timetocust"]["value"])?$arr["data"]["timetocust"]["value"]:$arr["hours"]);
		$row->set_prop("done", $arr["data"]["isdone"]["value"]?1:0);
		$row->set_prop("on_bill", $arr["data"]["tobill"]["value"]?1:0);
		$row->save();
//		$o->connect(array(
//			"to" => $row->id(),
//			"type" => "RELTYPE_ROW"
//		));
	}

	function __parts_sort($a, $b)
	{
		return strcmp($a->name(), $b->name());
	}

	/**
		@attrib name=add_part_popup all_args=1
	**/
	function add_part_popup($arr)
	{
		extract($arr);
		if(!(is_oid($task) && $this->can("view" , $task)))
		{
			die('<script src="http://intranet.automatweb.com/orb.aw?class=minify_js_and_css&action=get_js&name=aw_admin.js" type="text/javascript"></script>
			<script type="text/javascript">
				el=aw_get_el("participants_h",window.opener.document.changeform);if (!el) { el=aw_get_el("participants_h", window.opener.document.changeform);} if (!el) { el=aw_get_el("participants_h", window.opener.document.changeform);} if (el.options) {sz= el.options.length;el.options.length=sz+1;el.options[sz].value='.$part.';el.options[sz].selected = 1;} else {el.value = '.$part.';} window.opener.document.changeform.submit();window.close()
			</script>'
			);
		}

		$task = obj($task);

		$p = obj($part);
		$types = 10;
		if ($task->class_id() == CL_CRM_CALL)
		{
			$types = 9;
		}
		if ($task->class_id() == CL_CRM_MEETING)
		{
			$types = 8;
		}
		$p->connect(array(
			"to" => $task->id(),
			"reltype" => $types
		));

		$pl = get_instance(CL_PLANNER);
		// also add to their calendar
		if (($cal = $pl->get_calendar_for_person($p)))
		{
			$pl->add_event_to_calendar(obj($cal), $task);
		}

	//	$task->connect(array("to" => $part, "reltype" => 4));
		die('<script type="text/javascript">
			window.opener.location.reload();
			window.close();
			</script>'
		);
		//die($arr["error"]."\n<br>"."<input type=button value='OK' onClick='javascript:window.close();'>");
	}

	/**
		@attrib name=add_project_popup all_args=1
	**/
	function add_project_popup($arr)
	{
		extract($arr);
		$task = obj($task);
		$task->connect(array("to" => $project, "reltype" => 4));
		die('<script type="text/javascript">
			window.opener.location.reload();
			window.close();
			</script>'
		);
		//die($arr["error"]."\n<br>"."<input type=button value='OK' onClick='javascript:window.close();'>");
	}

	function fast_add_participants($arr, $types)
	{
		if(isset($_SESSION["event"]) && is_array($_SESSION["event"]["participants"]) && empty($arr["new"]))
		{
			foreach($_SESSION["event"]["participants"] as $pa)
			{
				if(!(is_oid($pa) && $this->can("view" , $pa)))
				{
					continue;
				}
				$p = obj($pa);
				if(is_array($types))
				{
					$types = reset($types);
				}
				$p->connect(array(
					"to" => $arr["obj_inst"]->id(),
					"reltype" => $types
				));
			}
			unset($_SESSION["event"]["participants"]);
		}
	}

	/**
		@attrib name=new_files_on_demand all_args=1
	**/
	function new_files_on_demand($arr)
	{

		$tb = new popup_menu();
		$tb->begin_menu("new_pop");

		$u = new user();
		$arr["obj_inst"] = obj($arr["obj_inst"]);
 		if ($arr["obj_inst"] && $this->can("view", $arr["obj_inst"]->prop("customer")))
 		{
 			$impl = $arr["obj_inst"]->prop("customer");
 			$impl_o = obj($impl);
 			if (!$impl_o->get_first_obj_by_reltype("RELTYPE_DOCS_FOLDER"))
 			{
 				$impl = $u->get_current_company();
 			}
 		}
 		else
 		{
 			$impl = $u->get_current_company();
 		}
 		if ($this->can("view", $impl))
 		{
 			$implo = obj($impl);
 			$f = get_instance("applications/crm/crm_company_docs_impl");
 			$fldo = $f->_init_docs_fld($implo);
 			$ot = new object_tree(array(
 				"parent" => $fldo->id(),
 				"class_id" => CL_MENU
 			));
 			$folders = array($fldo->id() => $fldo->name());
 			$tb->add_sub_menu(array(
 	//			"parent" => "nemw",
 				"name" => "mainf",
 				"text" => $fldo->name(),
 			));
 			$this->_add_fa($tb, "mainf", $fldo->id(),$arr["obj_inst"]->id());
 			$this->_req_level = 0;
 			$this->_req_get_folders_tb($ot, $folders, $fldo->id(), $tb, "mainf",$arr["obj_inst"]->id());
 		}

		header("Content-type: text/html; charset=".aw_global_get("charset"));
		//arr($tb);
		die($tb->get_menu(array(
			"text" => '<img src="/automatweb/images/icons/new.gif" alt="seaded" width="17" height="17" border="0" align="left" style="margin: -1px 5px -3px -2px" />
			<img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" class="nool" />'
		)));

	}

	/**
		@attrib name=new_files_on_demand all_args=1
	**/
	function new_files_on_demand_____redeclared ($arr)//to author: fix or remove
	{

		$tb = new popup_menu();
		$tb->begin_menu("new_pop");

		$u = new user();
		$arr["obj_inst"] = obj($arr["obj_inst"]);
 		if ($arr["obj_inst"] && $this->can("view", $arr["obj_inst"]->prop("customer")))
 		{
 			$impl = $arr["obj_inst"]->prop("customer");
 			$impl_o = obj($impl);
 			if (!$impl_o->get_first_obj_by_reltype("RELTYPE_DOCS_FOLDER"))
 			{
 				$impl = $u->get_current_company();
 			}
 		}
 		else
 		{
 			$impl = $u->get_current_company();
 		}
 		if ($this->can("view", $impl))
 		{
 			$implo = obj($impl);
 			$f = get_instance("applications/crm/crm_company_docs_impl");
 			$fldo = $f->_init_docs_fld($implo);
 			$ot = new object_tree(array(
 				"parent" => $fldo->id(),
 				"class_id" => CL_MENU
 			));
 			$folders = array($fldo->id() => $fldo->name());
 			$tb->add_sub_menu(array(
 	//			"parent" => "nemw",
 				"name" => "mainf",
 				"text" => $fldo->name(),
 			));
 			$this->_add_fa($tb, "mainf", $fldo->id(),$arr["obj_inst"]->id());
 			$this->_req_level = 0;
 			$this->_req_get_folders_tb($ot, $folders, $fldo->id(), $tb, "mainf",$arr["obj_inst"]->id());
 		}

		header("Content-type: text/html; charset=".aw_global_get("charset"));
		//arr($tb);
		die($tb->get_menu(array(
			"text" => '<img src="/automatweb/images/icons/new.gif" alt="seaded" width="17" height="17" border="0" align="left" style="margin: -1px 5px -3px -2px" />
			<img src="/automatweb/images/aw06/ikoon_nool_alla.gif" alt="#" width="5" height="3" border="0" class="nool" />'
		)));

	}

	function _req_get_folders_tb($ot, &$folders, $parent, &$tb, $parent_nm,$task)
	{
		$this->_req_level++;
		$objs = $ot->level($parent);
		foreach($objs as $o)
		{
			$tb->add_sub_menu(array(
				"parent" => $parent_nm,
				"name" => "fd".$o->id(),
				"text" => $o->name(),
			));
			$this->_add_fa($tb, "fd".$o->id(), $o->id(),$task);
			$this->_req_get_folders_tb($ot, $folders, $o->id(), $tb, "fd".$o->id(),$task);
		}
		$this->_req_level--;
	}

	function _add_fa(&$tb, $pt_n, $pt,$task)
	{
		$types = array(
			CL_FILE => t("Fail"),
			CL_CRM_MEMO => t("Memo"),
			CL_CRM_DOCUMENT => t("CRM Dokument"),
			CL_CRM_DEAL => t("Leping"),
			CL_CRM_OFFER => t("Pakkumine")
		);
		foreach($types as $clid => $nm)
		{
			$tb->add_item(array(
				"parent" => $pt_n,
				"text" => $nm,
				"link" => html::get_new_url($clid, $pt, array(
					"return_url" => $task?(html::get_change_url($task)):get_ru(),
					"alias_to" => $task,
					"reltype" => 2
				)),
			));
		}
	}

	/**
		@attrib name=delete_rels
	**/
	function delete_rels($arr)
	{
		$o = obj($arr["id"]);
		$o = obj($o->brother_of());
		if (is_array($arr["sel_ord"]) && count($arr["sel_ord"]))
		{
			foreach(safe_array($arr["sel_ord"]) as $item)
			{
				$o->disconnect(array(
					"from" => $item,
				));
			}
			// now we need to get the first orderer and set that as the new default orderer
			$ord = $o->get_first_obj_by_reltype("RELTYPE_CUSTOMER");
			if ($ord && $o->prop("customer") != $ord->id())
			{
				$o->set_prop("customer", $ord->id());
				$o->save();
			}
			else
			if (!$ord)
			{
				$o->set_prop("customer", 0);
				$o->save();
			}
		}

		if (is_array($arr["sel_proj"]) && count($arr["sel_proj"]))
		{
			foreach(safe_array($arr["sel_proj"]) as $item)
			{
				$o->disconnect(array(
					"from" => $item,
				));
			}
			// now we need to get the first orderer and set that as the new default orderer
			$ord = $o->get_first_obj_by_reltype("RELTYPE_PROJECT");
			if ($ord && $o->prop("project") != $ord->id())
			{
				$o->set_prop("project", $ord->id());
				$o->save();
			}
			else
			if (!$ord)
			{
				$o->set_prop("project", 0);
				$o->save();
			}
		}

		if (is_array($arr["sel_part"]) && count($arr["sel_part"]))
		{
			$arr["check"] = $arr["sel_part"];
			$arr["event_id"] = $arr["id"];
			post_message_with_param(
				MSG_MEETING_DELETE_PARTICIPANTS,
				CL_CRM_MEETING,
				&$arr
			);
		}

		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			foreach(safe_array($arr["sel"]) as $item)
			{
				if (substr($item, 0, 2) == "o_")
				{
					list(,$oid) = explode("_", $item);
					$o = obj($oid);
					$o->delete();
				}
				else
				{
					$o->disconnect(array(
						"from" => $item,
					));
				}
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=delete_parts
	**/
	function delete_parts($arr)
	{
		$o = obj($arr["id"]);
		$o = obj($o->brother_of());

		if (is_array($arr["sel_part"]) && count($arr["sel_part"]))
		{
			$arr["check"] = $arr["sel_part"];
			$arr["event_id"] = $arr["id"];
			foreach($arr["sel_part"] as $part)
			{

				$pr = $o->get_primary_row_for_person($part);
				if($pr)
				{
					$pr->delete();
				}
			}
			post_message_with_param(
				MSG_MEETING_DELETE_PARTICIPANTS,
				CL_CRM_MEETING,
				&$arr
			);
		}
		return $arr["post_ru"];
	}

	//Toimima peaks see siis nii, et kui Toimetuses on ainult 1 rida, siis pannakse kokkuleppehind
	// sinna rea taha kirja. Kui on kaks v6i rohkem 0 tundidega rida, siis jagatakse kokkuleppehind
	//v6rdselt nendele ridadele. Kui on osa ridu tundidega ja osa ilma, siis jagatakse kokkuleppehind
	//ainult tundidega ridade vahel 2ra.
	function get_row_ageement_price($row, $task = null)
	{
		if(is_oid($row))
		{
			$row = obj($row);
		}
		if(is_object($row))
		{
			$row_cnt = 0;
			$time_cnt =0;
			if(is_oid($task))
			{
				$task = obj($task);
			}
			if(!is_object($task))
			{
				$task_conn = reset($row->connections_to(array(
					"type" => 7,
				)));
				$task = obj($task_conn->from());
			}
			if(!is_object($task))
			{
				return 0;
			}
			$sum = $task->prop("deal_price");
			$cs = $task->connections_from(array(
				"type" => "RELTYPE_ROW",
			));
			foreach ($cs as $key => $ro)
			{
				$ob = $ro->to();
				$row_cnt ++;
				$time_cnt = $time_cnt + $ob->prop("time_to_cust");

			}
			//kui on ainuke rida
			if($row_cnt == 1)
			{
				return $sum;
			}
			//kui on mitu rida , mille aeg n2itab 0
			if($row_cnt > 1 && $time_cnt == 0)
			{
				return $sum / $row_cnt;
			}
			//kui on mitu rida ja m6nel on aeg, teistel mitte
			if($row_cnt > 1 && $time_cnt > 0)
			{
				return ($row->prop("time_to_cust")/$time_cnt) * $sum;
			}
		}
		return 0;
	}

	function do_db_upgrade($t, $f, $q, $err)
	{
		if ("aw_account_balances" === $t)
		{
			$i = get_instance(CL_CRM_CATEGORY);
			return $i->do_db_upgrade($t, $f);
		}
		elseif ("planner" === $t)
		{
			switch($f)
			{
				case "result":
				case "result_task":
				case "phone":
				case "promoter":
				case "deal_has_tax":
				case "real_start":
				case "real_duration":
				case "real_maker":
				case "deadline":
				case "customer_relation":
				case "hr_schedule_job":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "int"
					));
					return true;
				case "deal_unit":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "varchar(31)"
					));
					return true;
				case "deal_amount":
				case "deal_price":
					$this->db_add_col($t, array(
						"name" => $f,
						"type" => "double"
					));
					return true;
			}
		}
	}
}
?>
