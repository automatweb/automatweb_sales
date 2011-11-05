<?php
/*

@classinfo confirm_save_data=1

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

			@property start1 type=datepicker field=start table=planner parent=top_2way_right
			@caption Algus

			@property end type=datepicker table=planner parent=top_2way_right
			@caption L&otilde;peb

			@property deadline type=datepicker table=planner field=deadline parent=top_2way_right
			@caption T&auml;htaeg


@layout center_bit type=vbox parent=top_bit

	@property hrs_table type=table no_caption=1 store=no parent=center_bit

	@property center_bit_vis type=hidden store=no no_caption=1 parent=center_bit


@layout content_bit type=vbox closeable=1 area_caption=Sisu
	@property content type=textarea cols=100 rows=10 resize_height=-1 field=description table=planner parent=content_bit no_caption=1 width=100%
	@caption Sisu


@layout customer_bit type=vbox closeable=1 area_caption=Tellijad
	@property co_tb type=toolbar no_caption=1 store=no parent=customer_bit
	@property co_table type=table no_caption=1 store=no parent=customer_bit

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
@caption Kokkuleppehind

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

@property code type=hidden table=planner field=code
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

@groupinfo rows caption="Read"
@groupinfo recurrence caption="Kordumine submit=no
@groupinfo calendars caption="Kalendrid"
@groupinfo others caption="Teised" submit_method=get
@groupinfo projects caption="Projektid"
@groupinfo comments caption="Kommentaarid" parent=other_exp
@groupinfo reminders caption="Meeldetuletused" parent=other_exp
@groupinfo participants caption="Osalejad" submit=no
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

@reltype PARTICIPANT value=12 clid=CL_CRM_PERSON
@caption Osaleja

*/


define("STOPPER_PAUSED", 1);
define("STOPPER_RUNNING", 2);

class task extends class_base
{
	protected $add_to_proj;
	protected $post_save_add_parts = array();
	protected $mail_data = array();

	function task()
	{
		$this->init(array(
			"tpldir" => "groupware/task",
			"clid" => CL_TASK
		));

		$this->default_stoppers = array(
			CL_TASK,
			CL_CRM_CALL,
			CL_CRM_MEETING,
			CL_BUG,
			CL_TASK_ROW
		);

		$this->stopper_states = array(
			STOPPER_PAUSED => t("Seisab"),
			STOPPER_RUNNING => t("Stopper k&auml;ib")
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

		$ui = get_instance(CL_USER);
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
					"class_id" => CL_CRM_PERSON
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
						"class_id" => CL_PROJECT
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
		$u = get_instance(CL_USER);
		$this->co = $u->get_current_company();
		$this->person = $u->get_current_person();
	}

	function callback_mod_layout(&$arr)
	{
		$type = array(
			CL_TASK => t("Toimetusele"),
			CL_CRM_PRESENTATION => t("Esitlusele"),
			CL_CRM_MEETING => t("Kohtumisele"),
			CL_CRM_CALL => t("K&otilde;nele")
		);
		switch($arr["name"])
		{
			case "customer_bit":
				$arr["area_caption"] = sprintf(t("Tellijad: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->name());
				break;
			case "project_bit":
				$arr["area_caption"] = sprintf(t("Projektid: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->name());
				break;
			case "impl_bit":
				$arr["area_caption"] = sprintf(t("Osalejad: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->name());
				break;
 			case "files_bit":
				$arr["area_caption"] = sprintf(t("Manused: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->name());
				break;
			case "bills_bit":
				$arr["area_caption"] = sprintf(t("Arved: %s \"%s\""), $type[$arr["obj_inst"]->class_id()], $arr["obj_inst"]->name());
				break;
		}
		return true;
	}

	function get_property(&$arr)
	{
		$data = &$arr["prop"];

		///XXX: mis see teeb?
		if (is_object($arr["obj_inst"]) && $arr["obj_inst"]->prop("is_personal") && aw_global_get("uid") != $arr["obj_inst"]->createdby())
		{
			if (!($arr["prop"]["name"] === "start1" || $arr["prop"]["name"] === "end" || $arr["prop"]["name"] === "deadline"))
			{
				return PROP_IGNORE;
			}
		}

		if (!is_object($arr["obj_inst"]))
		{//XXX: miks vaja?
			$arr["obj_inst"] = obj();
		}

		$retval = PROP_OK;
		switch($data["name"])
		{
			case "comment":
				$data["type"] = "textarea";
				$data["rows"] = 2;
				$data["cols"] = 30;
				break;
			case "co_tb":
				$this->_get_co_tb($arr);
				break;

			case "project_tb":
				$this->_get_project_tb($arr);
				break;
			case "impl_tb":
				$this->_get_impl_tb($arr);
				break;

			case "rows_oe":
				$on_bill_str = isset($_SESSION["task"]["__bill_filt_comp"][1]) ? $_SESSION["task"]["__bill_filt_comp"][1] : "";
				unset($_SESSION["task"]["__bill_filt_comp"]);
				if($arr["new"])
				{
					break;
				}

				if($on_bill_str == t("Arveta"))
				{
					$data["value"]= t("Muud kulud, mille kohta ei ole arvet esitatud");
				}
				elseif($on_bill_str == t("Arvel"))
				{
					$data["value"]= t("Muud kulud, mille kohta on arve esitatud");
				}
				else
				{
					$data["value"]= t("Muud kulud");
				}

				$data["value"].= ":\n<br>";
				$cs = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_EXPENSE",
				));
				$stats_inst = new crm_company_stats_impl();
				$cu_name = "";
				$sum = 0;
				$there_are_oe = false;

				if(is_oid($arr["obj_inst"]->prop("hr_price_currency")))
				{
					$co_cu = obj($arr["obj_inst"]->prop("hr_price_currency"));
				}

				foreach ($cs as $key => $ro)
				{
					$ob = $ro->to();
					if($ob->class_id() == CL_CRM_EXPENSE)
					{
						$bno = "";
						if ($this->can("view", $ob->prop("bill_id")))
						{
							if($on_bill_str == t("Arveta"))
							{
								continue;
							}
							$bo = obj($ob->prop("bill_id"));
							$bno = $bo->prop("bill_no");
						}
						elseif($on_bill_str == t("Arvel"))
						{
							continue;
						}
						$onbill = "";
						if ($ob->prop("bill_id"))
						{
							$onbill = sprintf(t("arve nr %s"), $bno);
						}
						$c = $d = $w = $a = "";
						if(is_oid($ob->prop("currency")))
						{
							$curr = obj($ob->prop("currency"));
							$c = $curr->name();
							$sum = $sum + $stats_inst->convert_to_company_currency(array(
								"sum" => $ob->prop("cost"),
								"o" => $ob,
								"company_curr" =>  $co_cu->id(),
							));
						}
						else
						{
							$sum = $sum + $ob->prop("cost");
						}
						if(date_edit::get_timestamp($ob->prop("date")) > 0)
						{
							$d = date("j.m.Y", date_edit::get_timestamp($ob->prop("date")));
						}
						elseif($ob->prop("date") > 0)
						{
							$d = date("j.m.Y", $ob->prop("date"));
						}
						if(is_oid ($ob->prop("who")))
						{
							$who = obj($ob->prop("who"));
							$w = $who->name();
						}
						if(false)
						{
							$a = t("arve nr"). " ";
						}
						$there_are_oe = true;
						$data["value"].= $ob->name().", ". $ob->prop("cost") . " " .$c. ", " . $d. ", " .$w . ", " . $onbill."\n<br>";
					}
	//			$nr++;
				}
				if(is_object($co_cu)) $cu_name = $co_cu->name();
				$data["value"].=t("Kokku:")." ".$sum." ".$cu_name;
				if(!$there_are_oe) $data["value"] = "";
				break;

			case "predicates":
				return PROP_IGNORE;
				break;

			case "predicates_tb":
				$this->_predicates_tb($arr);
				break;

			case "predicates_table":
				$this->_predicates_table($arr);
				break;

		        case "parts_tb":
                               $this->_parts_tb($arr);
                              break;

			case "co_table":
				$this->_co_table($arr);
				break;

			case "proj_table":
				$this->_proj_table($arr);
				break;

			case "parts_table":
				$this->_parts_table($arr);
				break;

			case "hrs_table":
				$this->_hrs_table($arr);
				break;

			case "files_tb":
				$this->_files_tb($arr);
				break;

			case "files_table":
				$this->_files_table($arr);
				break;

			case "bills_tb":
				$this->_bills_tb($arr);
				break;

			case "bills_table":
				$this->_bills_table($arr);
				break;

			case "add_clauses":
				return PROP_IGNORE;
				$has_work_time = $arr["obj_inst"]->has_work_time();
				$data["options"] = array(
//					"status" => t("Aktiivne"),
					"is_done" => t("Tehtud"),
					"whole_day" => t("Terve p&auml;ev"),
					"is_goal" => t("Verstapost"),
					"is_personal" => t("Isiklik"),
//					"send_bill" => t("Arvele"),
					"in_budget" => t("Eelarvesse"),
//					"is_work" => t("T&ouml;&ouml;aeg")
				);
				$data["value"] = array(
//					"status" => $arr["obj_inst"]->prop("status") == STAT_ACTIVE ? 1 : 0,
					"is_done" => $arr["obj_inst"]->prop("is_done") ? 1 : 0,
					"whole_day" => $arr["obj_inst"]->prop("whole_day") ? 1 : 0,
					"is_goal" => $arr["obj_inst"]->prop("is_goal") ? 1 : 0,
					"is_personal" => $arr["obj_inst"]->prop("is_personal") ? 1 : 0,
//					"send_bill" => $arr["obj_inst"]->prop("send_bill") ? 1 : 0,
					"in_budget" => $arr["obj_inst"]->prop("in_budget") ? 1 : 0,
//					"is_work" => $arr["obj_inst"]->prop("is_work") ? 1 : 0,
				);

				if(!$has_work_time)
				{
					$data["options"]["is_work"] = t("T&ouml;&ouml;aeg");
				}

				// read cfgform and check if the props are set in the form
				$cff = get_instance(CL_CFGFORM);
 $cfgform_id = $this->get_cfgform_for_object(array(
                                "obj_inst" => $this->obj_inst,
                                "args" => $arr["request"],
                        ));
				if ($cfgform_id)
				{
				$grps = $cff->get_cfg_groups($cfgform_id);
				$ps = $cff->get_cfg_proplist($cfgform_id);
				foreach($data["options"] as $k => $v)
				{
					if (!isset($ps[$k]) /*|| !isset($grps[$ps[$k]["group"]])*/)
					{
						unset($data["options"][$k]);
					}
					else
					if ( $ps[$k]["caption"] != "")
					{
						$data["options"][$k] = $ps[$k]["caption"];
					}
				}
				}
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
				if(!empty($this->mail_data))
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

			case "end":
				$daystart = 0;
	//			$dayend = mktime($prop["value"]["hour"],$prop["value"]["minute"],0,$prop["value"]["month"],$prop["value"]["day"],$prop["value"]["year"]);
				if(isset($arr["request"]["start1"]))
				{
					$daystart = mktime($arr["request"]["start1"]["hour"],$arr["request"]["start1"]["minute"],0,$arr["request"]["start1"]["month"],$arr["request"]["start1"]["day"],$arr["request"]["start1"]["year"]);
					if($daystart > $data["value"])
					{
						$data["value"] = $daystart;
					}
				}

				$p = get_instance(CL_PLANNER);
				$cal = $p->get_calendar_for_user();
				if ($cal && !($daystart > 0))
				{
					$calo = obj($cal);
					if ($data["name"] === "end" && (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id())))
					{
						$data["value"] = time() + $calo->prop("event_def_len")*60;
					}
				}
				elseif ($arr["new"])
				{
					$data["value"] = time() + 900;
					if(!empty($arr["request"]["date"]))
					{
						$day = $arr["request"]["date"];
						$da = explode("-", $day);
						$data["value"] = mktime(date('h',$data["value"]), date('i', $data["value"]), 0, $da[1], $da[0], $da[2]);
					}
				}
				if ($cal)
				{
					$calo = obj($cal);
					$data["minute_step"] = $calo->prop("minute_step");
				}
				break;

			case "start1":
			case "deadline":
				$p = get_instance(CL_PLANNER);
				$cal = $p->get_calendar_for_user();
				if ($cal)
				{
					$calo = obj($cal);
					$data["minute_step"] = $calo->prop("minute_step");
				}

				if (!empty($arr["new"]))
				{
					$data["value"] = time();
					if(!empty($arr["request"]["date"]))
					{
						$day = $arr["request"]["date"];
						$da = explode("-", $day);
						$data["value"] = mktime(date('h',$data["value"]), date('i', $data["value"]), 0, $da[1], $da[0], $da[2]);
					}
				}
				break;

			case "sel_resources":
				$this->_get_sel_resources($arr);
				break;

			case "name":
				if($this->mail_data)
				{
					$data["value"] = $this->mail_data["subject"];
				}

				if (is_object($arr["obj_inst"]) && empty($data["value"]))
				{
					$data["value"] = $this->_get_default_name($arr["obj_inst"]);
				}

				if(!empty($arr["new"]))
				{
					if(isset($arr["request"]["title"]) && $arr["request"]["title"])
					{
						$data["value"] = $arr["request"]["title"];
					}
					if(isset($arr["request"]["participants"]) && $arr["request"]["participants"])
					{
						$_SESSION["event"]["participants"] = explode("," , $arr["request"]["participants"]);
					}
					$data["post_append_text"] = " <a href='#' onClick='document.changeform.ppa.value=1;document.changeform.submit();'>".t("Stopper")."</a>";
				}
				else
				if (is_object($arr["obj_inst"]))
				{
					$url = $this->mk_my_orb("stopper_pop", array(
						"id" => $arr["obj_inst"]->id(),
						"s_action" => "start",
						"type" => CL_TASK,

						"source_id" => $arr["obj_inst"]->id(),
					));
					$data["post_append_text"] = " <a href='#' onClick='aw_popup_scroll(\"$url\",\"aw_timers\", 800,600)'>".t("Stopper")."</a>";
					if (isset($arr["request"]["stop_pop"]) &&  $arr["request"]["stop_pop"] == 1)
					{
						$data["post_append_text"] .= "<script language='javascript'>aw_popup_scroll(\"$url\",\"aw_timers\", 800, 600)</script>";
					}
				}
				break;

			case "deadline":
				if (!is_object($arr["obj_inst"]) || $arr["new"])
				{
					$data["value"] = time();
				}
				break;

			case "rows_tb":
				$this->_rows_tb($arr);
				break;

			case "rows":
				$this->_rows($arr);
				$arr["prop"]["value"] = null;
				break;

			case "participants":
				return PROP_IGNORE;
				$data["options"] = $this->_get_possible_participants($arr["obj_inst"]);
				$p = array();
				if ($this->can("view", $arr["request"]["alias_to_org"]))
				{
					$ao = obj($arr["request"]["alias_to_org"]);
					if ($ao->class_id() == CL_CRM_PERSON)
					{
						$p[$ao->id()] = $ao->id();
						if (!isset($data["options"][$ao->id()]))
						{
							$data["options"][$ao->id()] = $ao->name();
						}
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
						if (!isset($data["options"][$obj->id()]))
						{
							$data["options"][$obj->id()] = $obj->name();
						}
					}
				}
				$data["value"] = $p;
				break;

			case "code":
				if (is_object($arr["obj_inst"]))
				{
					$pj = $arr["obj_inst"]->prop("project");
					if ($this->can("view", $pj))
					{
						$proj = obj($pj);
						$data["value"] = $proj->prop("code");
					}
				}
				break;

			case "hr_price":
				// get first person connected as participant and read their hr price
				if (empty($data["value"]) && is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
				{
					$conns = $arr['obj_inst']->connections_to(array());
					foreach($conns as $conn)
					{
						if($conn->prop('from.class_id')==CL_CRM_PERSON)
						{
							$pers = $conn->from();
							// get profession
							$rank = $pers->prop("rank");
							if (is_oid($rank) && $this->can("view", $rank))
							{
								$rank = obj($rank);
								$data["value"] = $rank->prop("hr_price");
								// immediately store this thingie as well so that the user will not have to save the object
								if ($arr["obj_inst"]->prop("hr_price") != $data["value"])
								{
									$arr["obj_inst"]->set_prop("hr_price", $data["value"]);
									$arr["obj_inst"]->save();
								}
								return PROP_IGNORE;
							}
						}
					}

				}
				return PROP_IGNORE;

			case 'task_toolbar' :
			{
				$tb = $data['toolbar'];
				$tb->add_button(array(
					'name' => 'del',
					'img' => 'delete.gif',
					'tooltip' => t('Kustuta valitud'),
					'action' => 'submit_delete_participants_from_calendar',
				));

				$tb->add_separator();

				$tb->add_button(array(
					'name' => 'Search',
					'img' => 'search.gif',
					'tooltip' => t('Otsi'),
					'url' => aw_url_change_var(array(
						'show_search' => 1,
					)),
				));

				$tb->add_button(array(
					'name' => 'save',
					'img' => 'save.gif',
					'tooltip' => t('Salvesta'),
					"action" => "save_participant_search_results"
				));

				$tb->add_button(array(
					'name' => 'csv',
					'img' => 'ftype_xls.gif',
					'tooltip' => 'CSV',
					"url" => aw_url_change_var("get_csv_file", 1)
				));

				$this->return_url=aw_global_get('REQUEST_URI');
				break;
			}

			case "project":
				return PROP_IGNORE;
				if ($this->can("view",$arr["request"]["alias_to_org"]))
				{
					$ol = new object_list(array(
						"class_id" => CL_PROJECT,
						"CL_PROJECT.RELTYPE_ORDERER" => $arr["request"]["alias_to_org"]
					));
				}
				else
				if (is_object($arr["obj_inst"]) && $this->can("view", $arr["obj_inst"]->prop("customer")))
				{
					$filt = array(
						"class_id" => CL_PROJECT,
						"CL_PROJECT.RELTYPE_ORDERER" => $arr["obj_inst"]->prop("customer")
					);
					$ol = new object_list($filt);
				}
				else
				{
					$i = new crm_company();
					$prj = $i->get_my_projects();
					if (!count($prj))
					{
						$ol = new object_list();
					}
					else
					{
						$ol = new object_list(array("oid" => $prj));
					}
				}

				$data["options"] = array("" => "") + $ol->names();

				if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
				{
					foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PROJECT")) as $c)
					{
						$data["options"][$c->prop("to")] = $c->prop("to.name");
					}
				}

				if ($arr["request"]["set_proj"])
				{
					$data["value"] = $arr["request"]["set_proj"];
				}

				if (!isset($data["options"][$data["value"]]) && $this->can("view", $data["value"]))
				{
					$tmp = obj($data["value"]);
					$data["options"][$tmp->id()] = $tmp->name();
				}

				asort($data["options"]);
				break;

			case "customer":
				return PROP_IGNORE;
				$i = new crm_company();
				$cst = $i->get_my_customers();
// 				if($this->$co)
// 				{
// 					$data["value"] = $this->$co;
// 				}
				if (!count($cst))
				{
					$data["options"] = array("" => "");
				}
				else
				{
					$ol = new object_list(array("oid" => $cst));
					$data["options"] = array("" => "") + $ol->names();
				}
				if ($this->can("view", $arr["request"]["alias_to_org"]))
				{
					$ao = obj($arr["request"]["alias_to_org"]);
					if ($ao->class_id() == CL_CRM_PERSON)
					{
						$u = get_instance(CL_USER);
						$data["value"] = $u->get_company_for_person($ao->id());
					}
					else
					{
						$data["value"] = $arr["request"]["alias_to_org"];
					}
				}

				if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
				{
					foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
					{
						$data["options"][$c->prop("to")] = $c->prop("to.name");
					}
				}

				if (!isset($data["options"][$data["value"]]) && $this->can("view", $data["value"]))
				{
					$tmp = obj($data["value"]);
					$data["options"][$tmp->id()] = $tmp->name();
				}

				asort($data["options"]);
				if (is_object($arr["obj_inst"]) && $arr["obj_inst"]->class_id() == CL_TASK)
				{
					$arr["obj_inst"]->set_prop("customer", (isset($data["value"]) ? $data["value"] : 0));
				}
				$data["onchange"] = "upd_proj_list()";
				break;

			case "other_expenses":
				$this->_other_expenses($arr);
				break;

			case "search_contact_company":
			case "search_contact_firstname":
			case "search_contact_lastname":
			case "search_contact_code":
				if ($arr["request"]["class"] != "planner")
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				break;

			case "search_contact_results":
				$p = get_instance(CL_PLANNER);
				$data["value"] = $p->do_search_contact_results_tbl($arr["request"]);
				break;
		};
		return $retval;
	}

	function set_property($arr)
	{
		$seti = get_instance(CL_CRM_SETTINGS);
		$sts = $seti->get_current_settings();
		if ($sts && $sts->prop("task_save_controller"))
		{
			$i = get_instance(CL_FORM_CONTROLLER);
			$arr = $i->eval_controller($sts->prop("task_save_controller"), $arr);
		}

		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "hrs_table":
				return $this->save_add_clauses($arr);
				break;
			case "parts_table":
				$this->save_parts_table($arr);
				break;
			case "co_table":
				$this->_save_co_table($arr);
				break;
			case "predicates":
				return PROP_IGNORE;
			case "end":
				if(date_edit::get_timestamp($arr["request"]["start1"]) > date_edit::get_timestamp($prop["value"]))
				{
					$prop["value"] = $arr["request"]["start1"];
					$arr["request"]["end"] = $arr["request"]["start1"];
				}
				break;
			case "parts_table":
				$this->save_parts_table($arr);
				break;
			case "add_clauses":
				return PROP_IGNORE;
				$this->save_add_clauses($arr);
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

			case "sel_resources":
				$this->_set_resources($arr);
				break;

			case "rows":
				$this->_save_rows($arr);
				break;

			case "participants":
				return PROP_IGNORE;
				if (!is_oid($arr["obj_inst"]->id()))
				{
					$this->post_save_add_parts = safe_array($prop["value"]);
					return PROP_IGNORE;
				}
				$prop["value"] = $_POST["participants"];
				$p = array();
				$conns = $arr['obj_inst']->connections_to(array(
					'type' => array(10, 8),//CRM_PERSON.RELTYPE_PERSON_TASK==10
				));
				foreach($conns as $conn)
				{
					$obj = $conn->from();
					$p[$obj->id()] = $obj->id();
				}

				foreach(safe_array($prop["value"]) as $person)
				{
					$this->add_participant($arr["obj_inst"], obj($person));
				}

				foreach($p as $k)
				{
					if ($k != "")
					{
						if (!in_array($k, $prop["value"]))
						{
							$po = obj($k);
							if ($po->is_connected_to(array("to" => $arr["obj_inst"]->id())))
							{
								$po->disconnect(array("from" => $arr["obj_inst"]->id()));
							}
						}
					}
				}
				if ($prop["value"] == "")
				{
					$u = get_instance(CL_USER);
					$po = obj($u->get_current_person());
					$po->connect(array(
						"to" => $arr["obj_inst"]->id(),
						"reltype" => 10
					));
				}

				break;

			case "code":
				$pj = $arr["obj_inst"]->prop("project");
				if ($this->can("view", $pj))
				{
					$proj = obj($pj);
					$prop["value"] = $proj->prop("code");
					$arr["obj_inst"]->set_prop("code", $proj->prop("code"));
				}
				break;

			case "whole_day":
				if ($prop["value"])
				{
					// ahaa! v&otilde;tab terve p&auml;eva!
					$start = $arr["obj_inst"]->prop("start1");
					list($m,$d,$y) = explode("-",date("m-d-Y",$start));
					$daystart = mktime(9,0,0,$m,$d,$y);
					$dayend = mktime(17,0,0,$m,$d,$y);
					$arr["obj_inst"]->set_prop("start1",$daystart);
					$arr["obj_inst"]->set_prop("end",$dayend);
				};
				break;

			case "customer":
				return PROP_IGNORE;
				if (isset($_POST["customer"]))
				{
					$prop["value"] = $_POST["customer"];
				}
				break;

			case "project":
				return PROP_IGNORE;
				if (isset($_POST["project"]))
				{
					$prop["value"] = $_POST["project"];
				}
				// add to proj
				if (is_oid($prop["value"]) && $this->can("view", $prop["value"]))
				{
					$this->add_to_proj = $prop["value"];
				}
				break;

			case "other_expenses":
				foreach(safe_array($_POST["exp"]) as $key => $entry)
				{
					if(is_oid($key) && $this->can("view" ,$key)){
						$obj = obj($key);
						if($obj->class_id() == CL_CRM_EXPENSE)
						{
							if($entry["name"] == "" && $entry["cost"] == "")
							{
								$cs = $arr["obj_inst"]->connections_from(array("to" => $key));
								$c = reset($cs);
								$o = $c->to();
								$o->delete();
							}
							else
							{
								$obj->set_name($entry["name"]);
								$obj->set_prop("date" , $entry["date"]);
								$obj->set_prop("cost" , $entry["cost"]);
								$obj->set_prop("who" , $entry["who"]);
								$obj->set_prop("currency" , $entry["currency"]);
								$obj->set_prop("has_tax" , $entry["has_tax"]);
								$obj->save();
							}
							continue;
						}
					}
					//edasi juhul kui sellist kulude objekti veel pole
					if ($entry["name"] != "" && $entry["cost"] != "")
					{
						$row = obj();
						$row->set_parent($arr["obj_inst"]->id());
						$row->set_class_id(CL_CRM_EXPENSE);
						$row->set_name($entry["name"]);
						$row->set_prop("date", $entry["date"]);
						$row->set_prop("cost", $entry["cost"]);
						$row->set_prop("who" , $entry["who"]);
						$row->set_prop("currency" , $entry["currency"]);
						$row->set_prop("has_tax" , $entry["has_tax"]);
						$row->save();
						$arr["obj_inst"]->connect(array(
							"to" => $row->id(),
							"type" => "RELTYPE_EXPENSE"
						));
					}
				}
				$arr["obj_inst"]->set_meta("other_expenses", null);
				break;

			case "hrs_table":
				$different_customers = 0;
				if(is_oid($arr["obj_inst"]->prop("project")) && $arr["obj_inst"]->prop("customer"))
				{
					$project = obj($arr["obj_inst"]->prop("project"));
					$different_customers = 1;
					foreach($project->connections_from(array("type" => 9)) as $c)
					{
						$orderer = $c->to();
						if($orderer->id() == $arr["obj_inst"]->prop("customer")) $different_customers = 0;
					}
				}

				$url = $this->mk_my_orb("error_popup", array(
					"text" => t("<br /><br /><br />Valitud Projekti ja Toimetuse kliendid erinevad"),
				));
				if($different_customers)
				{
					$prop["error"] = "<script name= javascript>window.open('".$url."','', 'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=150, width=500')</script>";
					return PROP_ERROR;
				}

				if (!(strlen($arr["request"]["hr_price"])> 0))
				{
					$prop["error"] = t("Tunnihind sisestamata!");
					return PROP_ERROR;
				}
				break;

			case "predicates":
				return PROP_IGNORE;
		}
		return $retval;
	}

	function callback_mod_retval(&$arr)
	{
		if (isset($arr["request"]["ppa"])) $arr["args"]["stop_pop"] = $arr["request"]["ppa"];
	}

	function callback_pre_save($arr)
	{
		if ($arr["obj_inst"]->name() == "")
		{
			$arr["obj_inst"]->set_name($this->_get_default_name($arr["obj_inst"]));
		}
		$len = $arr["obj_inst"]->prop("end") - $arr["obj_inst"]->prop("start1");
		$hrs = floor($len / 900) / 4;

		// write length to time fields if empty
		if ($arr["obj_inst"]->is_property("time_to_cust") and !$arr["obj_inst"]->prop("time_to_cust"))
		{
			$arr["obj_inst"]->set_prop("time_to_cust", $hrs);
		}

		if (!empty($arr["request"]["set_resource"]))
		{
			$arr["obj_inst"]->connect(array(
				"to" => $arr["request"]["set_resource"],
				"type" => "RELTYPE_RESOURCE"
			));
		}

		if (!empty($arr["request"]["set_pred"]))
		{
			$pv = $arr["obj_inst"]->prop("predicates");
			if (!is_array($pv) && is_oid($pv))
			{
				$pv = array($pv => $pv);
			}
			else
			if (!is_array($pv) && !is_oid($pv))
			{
				$pv = array();
			}
			$pv[$arr["request"]["set_pred"]] = $arr["request"]["set_pred"];
			$arr["obj_inst"]->set_prop("predicates", $arr["request"]["set_pred"]);
		}

		if ($arr["request"]["group"] === "general" && empty($arr["request"]["add_clauses"]["status"]))
		{
			$arr["obj_inst"]->set_status(STAT_NOTACTIVE);
		}

		if ($arr["request"]["group"] === "general" && !empty($arr["request"]["add_clauses"]["status"]))
		{
			$arr["obj_inst"]->set_status(STAT_ACTIVE);
		}
	}

	function callback_post_save($arr)
	{
		$inst = get_instance("crm_person_obj");
		if($arr["obj_inst"]->class_id() == CL_TASK)
		{
			foreach($arr["obj_inst"]->connections_to(array("from.class_id" => CL_CRM_PERSON, "type" => "RELTYPE_PERSON_TASK")) as $conn)
			{
				$inst->event_notifications(array("connection" => $conn), "task", true);
			}
		}
		elseif($arr["obj_inst"]->class_id() == CL_CRM_MEETING)
		{
			foreach($arr["obj_inst"]->connections_to(array("from.class_id" => CL_CRM_PERSON, "type" => "RELTYPE_PERSON_MEETING")) as $conn)
			{
				$inst->event_notifications(array("connection" => $conn), "meeting", true);
			}
		}

		if (!empty($arr["request"]["predicates"]))
		{
			$predicates = explode(",", $arr["request"]["predicates"]);
			foreach($predicates as $pred)
			{
				if ($this->can("view", $pred))
				{
					$arr["obj_inst"]->connect(array(
						"to" => $pred,
						"type" => "RELTYPE_PREDICATE"
					));
				}
			}
		}

		if (!empty($arr["request"]["participants_h"]))
		{
			$this->post_save_add_parts = explode(",", $arr["request"]["participants_h"]);
		}

		if (!empty($arr["request"]["orderer_h"]) and $this->can("view", $arr["request"]["orderer_h"]))
		{
			$arr["obj_inst"]->connect(array(
				"to" => $arr["request"]["orderer_h"],
				"type" => "RELTYPE_CUSTOMER"
			));
			$arr["obj_inst"]->set_prop("customer" , $arr["request"]["orderer_h"]);
			$arr["obj_inst"]->save();
		}

		if (!empty($arr["request"]["project_h"]))
		{
			$projects = explode(",", $arr["request"]["project_h"]);
			foreach($projects as $proj)
			{
				if ($this->can("view", $proj))
				{
					$arr["obj_inst"]->connect(array(
						"to" => $proj,
						"type" => "RELTYPE_PROJECT"
					));
					$arr["obj_inst"]->create_brother($proj);
				}
			}
		}

		if (!empty($arr["request"]["files_h"]))
		{
			$files = explode(",", $arr["request"]["files_h"]);
			foreach($files as $file)
			{
				if ($this->can("view", $file))
				{
					$arr["obj_inst"]->connect(array(
						"to" => $file,
						"type" => "RELTYPE_FILE"
					));
				}
			}
		}

		if ($this->add_to_proj)
		{
			$arr["obj_inst"]->create_brother($this->add_to_proj);
		}

		if (is_array($this->post_save_add_parts))
		{
			foreach(safe_array($this->post_save_add_parts) as $person)
			{
				$this->add_participant($arr["obj_inst"], obj($person));
			}
		}

		//the person who added the task will be a participant, whether he likes it
		//or not
		if(!empty($arr['new']))
		{
			$this->add_participant($arr["obj_inst"], get_current_person());
		}

		$save = false;
		// check if customer and project are set and if not, set first conns
		if (!is_oid($arr["obj_inst"]->prop("customer")))
		{
			$ro = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CUSTOMER");
			if ($ro)
			{
				$arr["obj_inst"]->set_prop("customer", $ro->id());
				$save = true;
			}
		}

		if (!is_oid($arr["obj_inst"]->prop("project")))
		{
			$ro = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_PROJECT");
			if ($ro)
			{
				$arr["obj_inst"]->set_prop("project", $ro->id());
				$save = true;
			}
		}

		if ($save)
		{
			$arr["obj_inst"]->save();
		}

		$pl = new planner();
		$pl->post_submit_event($arr["obj_inst"]);

		if(!empty($_SESSION["add_to_task"]))
		{
			if(is_oid($_SESSION["add_to_task"]["project"]))
			{
				 $arr["obj_inst"]->connect(array(
					"to" => $_SESSION["add_to_task"]["project"],
					"type" => "RELTYPE_PROJECT"
				));
			}

			if(is_oid($_SESSION["add_to_task"]["customer"]))
			{
				$arr["obj_inst"]->connect(array(
					"to" => $_SESSION["add_to_task"]["customer"],
					"type" => "RELTYPE_CUSTOMER"
				));
			}

			if(is_oid($_SESSION["add_to_task"]["impl"]))
			{
				$this->add_participant($arr["obj_inst"], obj($_SESSION["add_to_task"]["impl"]));
			}
			unset($_SESSION["add_to_task"]);
		}

		if($arr["obj_inst"]->class_id() == CL_CRM_CALL and !empty($arr["request"]["add_clauses"]["create_bug"]))
		{
			$arr["obj_inst"]->create_bug();
		}
	}

	function request_execute($obj)
	{
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $obj->name(),
			"time" => date("d-M-y H:i",$obj->prop("start1")),
			"content" => nl2br($obj->prop("content")),
		));
		return $this->parse();
	}

	function _init_other_exp_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "cost",
			"caption" => t("Hind")
		));
		$t->define_field(array(
			"name" => "currency",
			"caption" => t("Valuuta")
		));
		$t->define_field(array(
			"name" => "km",
			"caption" => t("KM")
		));
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev")
		));
//		$t->define_field(array(
//			"name" => "on_bill",
//			"caption" => t("Arvele")
//		));

		$t->define_field(array(
			"name" => "who",
			"caption" => t("")
		));

		$t->define_field(array(
			"name" => "bill",
			"caption" => t("Arve nr.")
		));
	}

	function _other_expenses($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_other_exp_t($t);

		$dat = safe_array($arr["obj_inst"]->meta("other_expenses"));
// 		$dat = array();
		$dat[] = array();
		$dat[] = array();
		$dat[] = array();
		$nr = 1;

		$participians = $this->get_partipicants($arr);
		$pa_list = new object_list();
		if (is_oid($participians) || (is_array($participians) && count($participians)))
		{
			$pa_list->add($participians);
		}

		$cs = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_EXPENSE",
		));
		foreach ($cs as $key => $ro)
		{
			$ob = $ro->to();
			if($ob->class_id() == CL_CRM_EXPENSE)
			{
				$bno = "";
				if ($this->can("view", $ob->prop("bill_id")))
				{
					$bo = obj($ob->prop("bill_id"));
					$bno = $bo->prop("bill_no");
				}
				$onbill = "";
				if ($ob->prop("bill_id"))
				{
					$onbill = sprintf(t("Arve nr %s"), $bno);
				}
				if($bno)//kui kulu on arvel, siis ei tasu muuta lasta
				{
					$t->define_data(array(
						"name" => $ob->name(),
						"cost" => $ob->prop("cost"),
						"currency"=> $ob->prop("currency"),
						"date" => date("d.m.Y" , date_edit::get_timestamp($ob->prop("date"))),
						"who" => $ob->prop("who.name"),
						"bill" => $onbill,
						"currency" => $ob->prop("currency.name"),
						"km" => $ob->prop("has_tax")? "*" : "",
					));
				}
				else
				{
					$t->define_data(array(
						"name" => html::textbox(array(
							"name" => "exp[".$ob->id()."][name]",
							"value" => $ob->name(),
						)),
						"cost" => html::textbox(array(
							"name" => "exp[".$ob->id()."][cost]",
							"size" => 5,
							"value" => $ob->prop("cost"),
						)),
						"currency"=> html::select(array(
							"options" => $this->_get_currencys(),
							"name" => "exp[".$ob->id()."][currency]",
							"value" => $ob->prop("currency"),
						)),
						"date" => html::date_select(array(
							"name" => "exp[".$ob->id()."][date]",
							"value" => $ob->prop("date"),
						)),
						"who" => html::select(array(
							"name" => "exp[".$ob->id()."][who]",
							"value" => $ob->prop("who"),
							"options" => $pa_list->names(),
						)),
						"on_bill" => html::checkbox(array(
							"name" => "exp[".$ob->id()."][on_bill]",
							"value" => 1,
							"checked" => $checked,
						)),
						"bill" => $onbill,
						"km" =>  html::checkbox(array(
							"name" => "exp[".$ob->id()."][has_tax]",
							"value" => 1,
							"checked" => $ob->prop("has_tax")?1:0,
						)),

					));
		//			$nr++;
				}
			}
		}
		foreach($dat as $exp)
		{
			$t->define_data(array(
				"name" => html::textbox(array(
					"name" => "exp[$nr][name]",
					"value" => $exp["exp"]
				)),
				"cost" => html::textbox(array(
					"name" => "exp[$nr][cost]",
					"size" => 5,
					"value" => $exp["cost"]
				)),
				"currency"=> html::select(array(
					"options" => $this->_get_currencys(),
					"name" => "exp[$nr][currency]",
					"value" => $exp["currency"],
				)),
				"date" => html::date_select(array(
					"name" => "exp[$nr][date]",
				)),
				"who" => html::select(array(
					"name" => "exp[".$nr."][who]",
					"options" => $pa_list->names(),
				)),
			));
			$nr++;
		}
		$t->set_sortable(false);
	}

	function _get_currencys()
	{
		$data = array();
		$curr_object_list = new object_list(array(
			"class_id" => CL_CURRENCY
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



	function _init_rows_t($t, $impl_filt = NULL)
	{
		$selected = "";

		$settings_inst = new crm_settings();
		$sts = $settings_inst->get_current_settings();
		if ($sts && $sts->prop("default_task_rows_bills_filter"))
		{
			$settings_inst = new crm_settings();
			$selected = $settings_inst->bills_filter_options[$sts->prop("default_task_rows_bills_filter")];
		}

/*		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev")."<br>",
			"align" => "center",
			"sortable" => 1,
			"chgbgcolor" => "col",
			"callback" =>  array(&$this, "__date_format"),
			"callb_pass_row" => true,
		));
*/
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("Jrk")."<br>".t("Kuup&auml;ev"),
			"align" => "center",
			"callback" =>  array(&$this, "__ord_format"),
			"callb_pass_row" => true,
			"numeric" => 1,
		));

	/*	$t->define_field(array(
			"name" => "id",
//			"caption" => t("Jrk"),
//			"align" => "center",
		"callb_pass_row" => true,
		"numeric" => 1,
		));
*/
		$t->define_field(array(
			"name" => "task",
			"caption" => t("Tegevus"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "impl",
			"caption" => t("Teostaja"),
			"align" => "center",
			"filter" => $impl_filt,
			"filter_compare" => array($this, "__impl_filt_comp")
		));

		$t->define_field(array(
			"name" => "time",
			"caption" => t("Tunde"),
			"align" => "left",
			"nowrap" => 1,
			"callback" =>  array($this, "__time_format"),
			"callb_pass_row" => true,
		));

		/*$t->define_field(array(
			"name" => "time_real",
			"caption" => t("Kulunud tunde"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "time_to_cust",
			"caption" => t("Tunde kliendile"),
			"align" => "center"
		));*/

		$t->define_field(array(
			"name" => "done",
			"caption" => "<a href='javascript:void(0)' onClick='aw_sel_chb(document.changeform,\"done\")'>".t("Tehtud")."</a>",
			"align" => "center",
			"filter" => array(
				t("Jah"),
				t("Ei")
			),
			"filter_compare" => array($this, "__done_filt_comp"),
		));

		$t->define_field(array(
			"name" => "on_bill",
			"caption" => "<a href='javascript:void(0)' onClick='aw_sel_chb(document.changeform,\"on_bill\")'>".t("Arvele")."</a>",
			"align" => "center",
			"filter_options" => array("selected" => $selected),
			"filter" => array(
				t("Jah"),
				t("Ei"),
				t("Arvel"),
				t("Arveta")
			),
			"filter_compare" => array($this, "__bill_filt_comp")
		));

		$t->define_field(array(
			"name" => "comments",
			"caption" => html::img(array("url" => aw_ini_get("baseurl")."/automatweb/images/forum_add_new.gif", "border" => 0)),
			"align" => "center",
			"filter" => array(
				t("Jah"),
				t("Ei")
			),
			"filter_compare" => array($this, "__com_filt_comp")
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
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
		if ($str == t("Arveta") && $row["bill_val"] !== "billed")
		{
			return true;
		}
		else
		if ($str == t("Arveta"))
		{
			return false;
		}

		if ($str == t("Arvel") && $row["bill_val"] !== "billed")
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

	function _rows($arr)
	{
	//	$tasks = new object_list(array("class_id" => CL_TASK, "lang_id" => array()));
	//	foreach($tasks->arr() as $task)
	//	{
	//		if($task->prop("hr_price_currency") != $task->prop("deal_price_currency")) arr($task->id());
	//	}

		$seti = get_instance(CL_CRM_SETTINGS);
		$sts = $seti->get_current_settings();
		if ($sts && $sts->prop("task_rows_controller"))
		{
			$i = get_instance(CL_FORM_CONTROLLER);
			$res = $i->eval_controller($sts->prop("task_rows_controller"), $arr);
			foreach($res as $key => $val)
			{
				$this->$key = $val;
			}
		}

		$t = $arr["prop"]["vcl_inst"];

		$impls = $this->_get_possible_participants($arr["obj_inst"], true, $arr["obj_inst"]->prop("participants"));
		$this->_init_rows_t($t, array_values($impls));

		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$u = get_instance(CL_USER);
		$def_impl = $u->get_current_person();
		$o_def_impl = array($def_impl => $def_impl);
		$cs = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_ROW",
		));
		$data = $data_done = array();
		foreach ($cs as $key => $ro)
		{
			$ob = $ro->to();
			if($ob->class_id() == CL_TASK_ROW)
			{
				if($ob->prop("done") || !strlen($ob->prop("date")))
				{
					$data_done[] = $ro;
				}
				else
				{
					$data[] = $ro;
				}
			}
			else
			{
				if($ob->prop("is_done") || !strlen($ob->prop("start1")))
				{
					$data_done[] = $ro;
				}
				else
				{
					$data[] = $ro;
				}
			}
		}
		ksort($data);
		ksort($data_done);
		//$cs = array_merge($data, $data_done);

		$null_idx = 0;
		$comm = get_instance(CL_COMMENT);
		$ank_idx = 1;

		$rows_object_list = new object_list();
		foreach($cs as $ro)
		{
			$row = $ro->to();
			$rows_object_list->add($row);
		}
		//$rows_object_list->sort_by(array("prop" => "ord","order" => "desc"));
		$row_ids = $rows_object_list->ids();
		$row_ids[] = NULL;
		$row_ids[] = NULL;
		$row_ids[] = NULL;
		$not_sorted=true;

		//statistikasse nende ridade tunnid, mida reaalselt tabelis n2ha (kliendile)
		$this->visible_rows_sum = 0;
		//k6ikide ridade tundide summa (kliendile)
		$this->sum = 0;

		foreach($row_ids as $ro)
		{
			if ($ro === NULL)
			{
				$idx = $null_idx--;
				$row = obj();
				$def_impl = $o_def_impl;
				if($not_sorted)	$t->sort_by(array(
					"field" => array("date", "ord" , "id"),
					"order" => array("asc", "asc" , "asc"),
				));
				$t->set_sortable(false);
				$not_sorted = false;
			}
			else
			{
				$idx = $ro;
				$row = obj($ro);
				$def_impl = array();
			}
			$ank_idx++;
			$date_sel = "<A HREF='#'  onClick=\"var cal=new CalendarPopup();cal.select(aw_get_el('rows[$idx][date]'),'anchor".$ank_idx."','dd/MM/yy'); return false;\"
						   NAME='anchor".$idx."' ID='anchor".$ank_idx."'>".t("vali")."</A>";

			$comments = "";
			$comments_cnt = 0;
			if (is_oid($idx))
			{
				$comments_cnt = $comm->get_comment_count(array("parent" => $idx));
				$comments = html::popup(array(
					"width" => 800,
					"height" => 500,
					"scrollbars" => 1,
					"url" => html::get_change_url($idx, array("group" => "comments")),
					"caption" => sprintf(t("%s (%s)"), html::img(array("url" => aw_ini_get("baseurl")."/automatweb/images/forum_add_new.gif", "border" => 0)), $comments_cnt)
				));
			}

			$is = (is_array($row->prop("impl")) && count($row->prop("impl"))) ? $row->prop("impl") : $def_impl;
			$is_str = array();
			foreach(safe_array($is) as $is_id)
			{
				$iso = obj($is_id);
				if (!isset($impls[$is_id]))
				{
					$impls[$is_id] = $iso->name();
				}
				$is_str[] = $iso->name();
			}
			$bno = "";
			if ($this->can("view", $row->prop("bill_id")))
			{
				$bo = obj($row->prop("bill_id"));
				$bno = $bo->prop("bill_no");
			}
			$pref = "";
			if ($row->class_id() == CL_CRM_MEETING)
			{
				$date = date("d/m/y",($row->prop("start1") > 100 ? $row->prop("start1") : ($row->created() ? $row->created() :time())));
				$d_comp = date("Ymd",$row->prop("start1") > 100 ? $row->prop("start1") : ($row->created() ? $row->created() :time()));
				$i = $row->instance();
				$i->get_property($argb);
				//$impls = $pr["options"];
				$is = $pr["value"];
				$pref = html::obj_change_url($row->id())." <br>".date("d.m.Y H:i", $row->prop("start1"))." - ".date("d.m.Y H:i", $row->prop("end"))."<br>";
			}
			else
			{
				$date = date("d/m/y",($row->prop("date") > 100 ? $row->prop("date") : ($row->created() ? $row->created() :time())));
				$d_comp =  date("Ymd",($row->prop("date") > 100 ? $row->prop("date") : ($row->created() ? $row->created() :time())));
				$app = "";
			}

			if($d_comp < date("Ymd",time()))
			{
				$col = "red";
			}
			elseif($d_comp == date("Ymd", time()))
			{
				$col = "yellow";
			}
			else
			{
				$col = "white";
			}
			if($ro === null || ( $row->class_id() == CL_TASK_ROW && $row->prop("done") || ($row->class_id() == CL_CRM_MEETING && $row->prop("is_done"))))
			{
				$col = "white";
			}

			$pr = array("name" => "participants");
			$argb = array(
				"obj_inst" => $row,
				"request" => $arr["request"],
				"prop" => &$pr
			);

			$stopper = "";
			if ($idx > 0)
			{
				$url = $this->mk_my_orb("stopper_pop", array(
					"id" => $idx,
					"s_action" => "start",
					"type" => t("Toimetus"),
					"name" => isset($data["value"]) ? $data["value"] : ""
				));
				$stopper = " <a href='#' onClick='aw_popup_scroll(\"$url\",\"aw_timers\",320,400)'>".t("Stopper")."</a>";
			}
			else
			{
				$url = $this->mk_my_orb("stopper_pop", array(
					"id" => $arr["obj_inst"]->id(),
					"s_action" => "start",
					"type" => t("Toimetus"),
					"name" => $arr["obj_inst"]->name()
				));
				$stopper = " <a href='#' onClick='aw_popup_scroll(\"$url\",\"aw_timers\",320,400)'>".t("Stopper")."</a>";
			}

			$onbill = "";
			$bv = "";
			if ($row->prop("bill_id"))
			{
				$onbill = sprintf(t("Arve nr %s"), $bno);
				$bv = "billed";
			}
			else
			if ($row->prop("bill_no"))
			{
				$onbill = sprintf(t("Arve nr %s"), $row->prop("bill_no"));
				$bv = "billed";
			}
			else
			{
				$onbill = html::checkbox(array(
					"name" => "rows[$idx][on_bill]",
					"value" => 1,
					"checked" => ($row->class_id() == CL_CRM_MEETING ? $row->prop("send_bill") : $row->prop("on_bill"))
				));
				$bv = ($row->class_id() == CL_CRM_MEETING ? $row->prop("send_bill") : $row->prop("on_bill"));
			}

			$time_to_cust = $row->is_saved() && $row->is_property("time_to_cust") ? $row->prop("time_to_cust") : 0;
			$this->sum+= $time_to_cust;

			//kui arve on olemas, siis ei tahaks lasta muuta enam asju
			if($this->can("view" , $row->prop("bill_id")))
			{
				$date = date("d/m/y",($row->prop("date") > 100 ? $row->prop("date") : $row->created()));
				$imps = "";
				foreach($is as $impo)
				{
					$imps.= $impls[$impo]."<br>\n";
				}
				$t->define_data(array(
					"idx" => $idx,
					"ord_val" => $row->prop("ord"),
					"date_val" => $date,
					"date_sel" => $date_sel,
					"sum_val" => $time_to_cust,
					"ord" => $row->prop("ord"),
					"id" => $row->id(),
					"task" => $row->prop("content"),
					"date" => $row->prop("date") - ($row->prop("date")%3600),
					"impl" => $imps,

//					"impl" => html::select(array(
//					"name" => "rows[$idx][impl]",
//					"options" => $impls,
//					"value" => $is,
//					"multiple" => 1
//				)),
					"impl_val" => $is_str,
					"time" => $row->prop("time_guess")." - ".t("Prognoos")."<br>".$row->prop("time_real")." - ".t("Kulunud")."<br>".$time_to_cust." - ".t("Kliendile")."<br>",
					"done" => "",
					"done_val" => $row->class_id() == CL_CRM_MEETING ? $row->prop("is_done") : $row->prop("done"),
					"on_bill" => $onbill,
					"bill_val" => $bv,
					"comments" => $comments,
					"comments_cnt" => $comments_cnt,
					"oid" => $row->id(),
					"col" => $col,
					"bill_id" => $row->prop("bill_id"),
				));
			}
			else
			{
				$t->define_data(array(
					"idx" => $idx,
					"ord_val" => $row->prop("ord"),
					"date_val" => $date,
					"date_sel" => $date_sel,
					"sum_val" => $time_to_cust,
					"ord" => $row->prop("ord"),
					"id" => $row->id(),
					"task" => $pref."<a name='row_".$idx."'></a>".html::textarea(array(
						"name" => "rows[$idx][task]",
						"value" => $row->prop("content"),
						"rows" => 5,
						"cols" => 45
					)).$app,
					"date" => $row->prop("date") - ($row->prop("date")%3600),
					"impl" => html::select(array(
						"name" => "rows[$idx][impl]",
						"options" => $impls,
						"value" => $is,
						"multiple" => 1
					)),
					"impl_val" => $is_str,
					"time" => html::textbox(array(
						"name" => "rows[$idx][time_guess]",
						"value" => $row->prop("time_guess"),
						"size" => 3
					))." - ".t("Prognoos")."<br>".
					html::textbox(array(
						"name" => "rows[$idx][time_real]",
						"value" => $row->prop("time_real"),
						"size" => 3
					))." - ".t("Kulunud")."<br>".
					html::textbox(array(
						"name" => "rows[$idx][time_to_cust]",
						"value" => $time_to_cust,
						"size" => 3
					))." - ".t("Kliendile")."<br>".$stopper,
					"done" => html::checkbox(array(
						"name" => "rows[$idx][done]",
						"value" => 1,
						"checked" => $row->class_id() == CL_CRM_MEETING ? $row->prop("is_done") : $row->prop("done")
					)),
					"done_val" => $row->class_id() == CL_CRM_MEETING ? $row->prop("is_done") : $row->prop("done"),
					"on_bill" => $onbill,
					"bill_val" => $bv,
					"comments" => $comments,
					"comments_cnt" => $comments_cnt,
					"oid" => $row->id(),
					"col" => $col
				));
			}
		}
/*		if(is_oid($arr["obj_inst"]->prop("hr_price_currency")))
		{
			$sad = obj($arr["obj_inst"]->prop("hr_price_currency"));
			$curr = $sad->name();
		}
		if($arr["obj_inst"]->prop("deal_price"))
		{
			$sum = $arr["obj_inst"]->prop("deal_amount");
			$cash = $arr["obj_inst"]->prop("deal_price");
		}
		else
		{
			$cash = $sum * $arr["obj_inst"]->prop("hr_price");
		}*/
		$t->define_data(array(
			"result_sum" => 1,
			"task_object" => $arr["obj_inst"],
		));
	}

	function __ord_format($val)
	{
		if (!empty($val["sum_val"]))
		{
			$this->visible_rows_sum += $val["sum_val"];
		}

		if(!empty($val["date_val"]))
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
		if(!empty($val["result_sum"]))
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
		if(!empty($val["date_val"]))
		{
			if(isset($val["bill_id"]) and $this->can("view", $val["bill_id"]))
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

	function get_task_expenses($o)
	{
		$cs = $o->connections_from(array(
			"type" => "RELTYPE_EXPENSE",
		));
		$expenses = new object_list();
		foreach ($cs as $key => $ro)
		{
			$ob = $ro->to();
			if($ob->class_id() == CL_CRM_EXPENSE)
			{
				$expenses->add($ob);
			}
		}
		return $expenses;
	}

	function get_task_bill_rows($task, $only_on_bill = true, $bill_id = null)
	{
		// check if task has rows defined that go on bill
		// if, then ret those
		// if not, return data for bill
		$stats_inst = new crm_company_stats_impl();

		$rows = array();
		//$dat = safe_array($task->meta("rows"));
		if ($task->brother_of() != $task->id())
		{
			$task = obj($task->brother_of());
		}

		$curr = $task->prop("hr_price_currency");
		foreach($task->connections_from(array("type" => "RELTYPE_ROW")) as $c)
		{
			$row = $c->to();
			$idx = $row->id();
			if (($row->prop("send_bill") || $row->prop("on_bill") == 1 || !$only_on_bill) && ($bill_id === null || $row->prop("bill_id") == $bill_id || $row->prop("bill_no") == $bill_id))
			{
				$id = $task->id()."_".$idx;
				$time_to_cust = $row->is_property("time_to_cust") ? $row->prop("time_to_cust") : 0;
				$rows[$id] = array(
					"name" => $row->prop("content"),
					"unit" => t("tund"),
					"date" => $row->class_id() == CL_CRM_MEETING ? $row->prop("start1") : $row->prop("date"),
					"price" => $task->prop("hr_price"),
					"amt" => $time_to_cust,
					"amt_real" => $row->prop("time_real"),
					"amt_guess" => $row->prop("time_guess"),
					"sum" => str_replace(",", ".", $time_to_cust) * $task->prop("hr_price"),
					"has_tax" => 1,
					"on_bill" => 1,
					"bill_id" => $row->prop("bill_id") ? $row->prop("bill_id") : $row->prop("bill_no"),
					"impl" => $row->prop("impl"),
					"row_oid" => $row->id(),
					"is_done" => $row->class_id() == CL_CRM_MEETING ? $row->prop("is_done") : $row->prop("done")
				);
			}
		}
		if (!count($rows) )
		{
			// add the main task to the first bill only
			$add = true;
			if ($bill_id !== null)
			{
				$conns = $task->connections_from(array("type" => "RELTYPE_BILL", "order_by" => "to.id"));
				$bc = reset($conns);
				if ($bc && $bill_id != $bc->prop("to"))
				{
					if ($bill_id != $bc->prop("to"))
					{
						$add = false;
					}
				}
			}

			if ($add)
			{
				$rows[$task->id()] = array(
					"name" => $task->name(),
					"unit" => t("tund"),
					"price" => $task->prop("hr_price"),
					"date" => $task->prop("start1"),
					"amt" => $task->prop("num_hrs_to_cust"),
					"amt_real" => $task->prop("num_hrs_real"),
					"amt_guess" => $task->prop("num_hrs_guess"),
					"sum" => str_replace(",", ".", $task->prop("num_hrs_to_cust")) * $task->prop("hr_price"),
					"has_tax" => 1,
					"on_bill" => 1,
					"impl" => $task->prop("participants")
				);
			}
		}

		// add other expenses rows
		foreach(safe_array($task->meta("other_expenses")) as $idx => $oe)
		{
			$id = $task->id()."_oe_".$idx;
			$rows[$id] = array(
				"name" => $oe["exp"],
				"unit" => "",
				"price" => $oe["cost"],
				"amt" => 1,
				"amt_real" => 1,
				"amt_guess" => 1,
				"sum" => $oe["cost"],
				"has_tax" => 1,
				"is_oe" => true,
				"on_bill" => 1
			);
		}

		foreach ($task->connections_from(array("type" => "RELTYPE_EXPENSE")) as $key => $ro)
		{
			$ob = $ro->to();
			if($ob->class_id() == CL_CRM_EXPENSE)
			{
				if (($bill_id === null || $ob->prop("bill_id") == $bill_id || !(is_oid($ob->prop("bill_id")) && $this->can("view" , $ob->prop("bill_id")))))

				{
					$id = $task->id()."_oe_".$ob->id();
					$rows[$id] = array(
						"name" => $ob->name(),
						"unit" => "",
						"row_oid" => $ob->id(),
						"price" => $stats_inst->convert_to_company_currency(array(
							"sum" => $ob->prop("cost"),
							"o" => $ob,
							"company_curr" =>  $curr,
						)),
						"amt" => 1,
						"amt_real" => 1,
						"amt_guess" => 1,
						"sum" => $stats_inst->convert_to_company_currency(array(
							"sum" => $ob->prop("cost"),
							"o" => $ob,
							"company_curr" =>  $curr,
						)),
						"has_tax" => 1,
						"is_oe" => true,
						"on_bill" => 1,
						"bill_id" => $ob->prop("bill_id"),
						"date" => $ob->prop("date") ? $ob->prop("date") : time(),
						"impl" => $ob->prop("who"),
					);
				}
			}
		}
		return $rows;
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

	function _rows_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			"name" => "new_meeting",
			"img" => "new.gif",
			"tooltip" => t("Kohtumine"),
			"url" => $this->mk_my_orb(
				"new",
				array(
					"parent" => $arr["obj_inst"]->parent(),
					"return_url" => get_ru(),
					"alias_to" => $arr["obj_inst"]->id(),
					"reltype" => 7,
					"alias_to_org" => $arr["obj_inst"]->prop("customer"),
					"set_proj" => $arr["obj_inst"]->prop("project")
				),
				CL_CRM_MEETING
			)
		));

		$b = array(
			'name' => 'create_bill',
			'img' => 'create_bill.jpg',
			'tooltip' => t('Loo arve'),
		);

		if ($arr["obj_inst"]->prop("bill_no") != "")
		{
			$b["url"] = html::get_change_url($arr["obj_inst"]->prop("bill_no"), array("return_url" => get_ru()));
		}
		else
		{
			$b['action'] = 'create_bill_from_task';
		}
		$tb->add_button($b);

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta read"),
			"action" => "delete_task_rows"
		));

		$tb->add_button(array(
			"name" => "copy",
			"img" => "copy.gif",
			"tooltip" => t("Kopeeri read"),
			"action" => "copy_task_rows"
		));

		$tb->add_button(array(
			"name" => "cut",
			"img" => "cut.gif",
			"tooltip" => t("L&otilde;ika read"),
			"action" => "cut_task_rows"
		));

		if(aw_session::get("task_rows"))
		{
			$tb->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"tooltip" => t("Aseta read"),
				"action" => "paste_task_rows"
			));
		}
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

	/**
		@attrib name=create_bill_from_task
		@param id required type=int acl=view
		@param post_ru required
	**/
	function create_bill_from_task($arr)
	{
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();

		$task = obj($arr["id"]);

		if (!$co)
		{ // a user with no work relation can't create bills
			class_base::show_error_text(t("Arvet ei saa luua kui te pole &uuml;heski organisatsioonis liige/t&ouml;&ouml;taja"));
			return;
		}

		$i = new crm_company();
		return $i->create_bill(array(
			"id" => $co,
			"proj" => $task->prop("project"),
			"cust" => $task->prop("customer"),
			"sel" => array($task->id() => $task->id()),
			"post_ru" => $arr["post_ru"]
		));
	}

	function _get_default_name($o)
	{
		$n = $o->prop_str("project");
		if ($n == "")
		{
			$n = $o->prop_str("customer");
			if ($n == "")
			{
				$uid = $o->createdby();
				if ($uid != "")
				{
					$u = get_instance("users");
					$u_o = obj($u->get_oid_for_uid($uid));

					$u = get_instance(CL_USER);
					$p = obj($u->get_person_for_user($u_o));
					$n = sprintf(t("%s toimetus"), $p->name());
				}
			}
		}
		return $n;
	}

	function _get_possible_participants($o, $proj_only = false, $sel = array())
	{
		$opts = array();
		// also add all workers for my company
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		$w = array();
		$i = get_instance(CL_CRM_COMPANY);
		$w = $co ? array_keys($i->get_employee_picker(obj($co), false, true)) : array();
		foreach($w as $oid)
		{
			$t = obj($oid);
			$opts[$oid] = $t->name();
		}
		asort($opts);

		if ($proj_only)
		{
			// filter by project participants
			if ($this->can("view", $o->prop("project")))
			{
				$p = obj($o->prop("project"));
				$p_p = array();
				foreach($p->connections_from(array("type" => "RELTYPE_PARTICIPANT")) as $c)
				{
					$p_p[$c->prop("to")] = $c->prop("to");
				}

				foreach($opts as $k => $v)
				{
					if (!isset($p_p[$k]) && !isset($sel[$k]))
					{
						unset($opts[$k]);
					}
				}
			}
		}

		if(is_object($o) && is_oid($o->id()))
		{
			$conns = $o->connections_to(array(
				'type' => array(10, 8),//CRM_PERSON.RELTYPE_PERSON_TASK==10
			));
			foreach($conns as $conn)
			{
				$obj = $conn->from();
				$opts[$obj->id()] = $obj->name();
			}
		}

		return array("" => t("--vali--")) + $opts;
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

	function _init_sel_res_t($t)
	{
		$t->define_field(array(
			"name" => "cal",
			"caption" => t("Kalender"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "events",
			"caption" => t("Staatus"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "sel",
			"caption" => t("Vali"),
			"align" => "center"
		));
	}

	function _get_sel_resources($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_sel_res_t($t);

		// get resources from my company
		$co = get_instance(CL_CRM_COMPANY);
		$res = $co->get_my_resources();

		$sel_res = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_RESOURCE")));
		$sel_ids = array_flip($sel_res->ids());
		foreach($res->arr() as $r)
		{
			// get events for the resource
			$avail = true;
			$evstr = "";
			$ri = $r->instance();
			$events = $ri->get_events_for_range(
				$r,
				$arr["obj_inst"]->prop("start1"),
				$arr["obj_inst"]->prop("end")
			);
			if (count($events))
			{
				$avail = false;
				$evstr = t("Ressurss on valitud aegadel kasutuses:<br>");
				foreach($events as $event)
				{
					$evstr .= date("d.m.Y H:i", $event["start"])." - ".
							  date("d.m.Y H:i", $event["end"])."  ".$event["name"]."<br>";
				}
			}

			if ($avail)
			{
				$una = $ri->get_unavailable_periods(
					$r,
					$arr["obj_inst"]->prop("start1"),
					$arr["obj_inst"]->prop("end")
				);

				if (count($una))
				{
					$avail = false;
					$evstr = t("Ressurss ei ole valitud aegadel kasutatav!<br>Kinnised ajad:<br>");
					foreach($una as $event)
					{
						$evstr .= date("d.m.Y H:i", $event["start"])." - ".
								  date("d.m.Y H:i", $event["end"]).": ".$event["name"];
					}
				}
			}

			if ($avail)
			{
				$una = $ri->get_recurrent_unavailable_periods(
					$r,
					$arr["obj_inst"]->prop("start1"),
					$arr["obj_inst"]->prop("end")
				);
				if (count($una))
				{
					$avail = false;
					$evstr = t("Ressurss ei ole valitud aegadel kasutatav!<br>Kinnised ajad:<br>");
					foreach($una as $event)
					{
						$evstr .= date("d.m.Y H:i", $event["start"])." - ".
								  date("d.m.Y H:i", $event["end"])."<br>";
					}
				}
			}

			$t->define_data(array(
				"name" => html::obj_change_url($r),
				"cal" => html::get_change_url($r->id(), array("return_url" => get_ru(), "group" => "grp_resource_schedule"), t("Vaata")),
				"sel" => html::checkbox(array(
					"name" => "sel[".$r->id()."]",
					"value" => 1,
					"checked" => isset($sel_ids[$r->id()]) ? true : false
				)),
				"events" => ($avail ? t("Ressurss on vaba") : $evstr)
			));
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

	function _proc_stop_act($arr, &$stos)
	{
		// lets store stoppers data
		$ac_postfix = "_awAutoCompleteTextbox";

		foreach($arr as $k => $v)
		{
			// god-mother-fukin-damn .. uhh. well, i have to ignore those double elements from ac
			if(substr($k, (0-strlen($ac_postfix))) == $ac_postfix)
			{
				continue;
			}

			$spl = explode("_", $k);
			// well, here we filter out the stoppers data
			if($spl[0] === "stopdata")
			{
				// here we find such elements which use autocomplete.. hopefully
				if(array_key_exists($k.$ac_postfix, $arr))
				{
					// we have to store the real value and the caption in case autocomplete is used
					if($v != $arr[$k.$ac_postfix])
					{
						$stos[$spl[1]]["data"][$spl[2]]["value"] = $v;
						$stos[$spl[1]]["data"][$spl[2]]["caption"] = $arr[$k.$ac_postfix];
					}
					else
					{
						$stos[$spl[1]]["data"][$spl[2]]["value"] = $v;
						$stos[$spl[1]]["data"][$spl[2]]["caption"] = "";
					}
				}
				else
				{
					$stos[$spl[1]]["data"][$spl[2]]["value"] = $v;
					$stos[$spl[1]]["data"][$spl[2]]["caption"] = "";
				}
			}
		}

		// searched_oids
		if (!empty($arr["searched_oid"]))
		{
			foreach($arr["searched_oid"] as $sto_ident => $oid)
			{
				if($this->can("view", $oid) && !$this->can("view", $stos[$sto_ident]))
				{
					$o = obj($oid);
					$stos[$sto_ident]["oid"] = $oid;
					$stos[$sto_ident]["name"] = $o->name();
					$url = $this->mk_my_orb("stopper_pop",array(), CL_TASK);
				}
			}
		}

		if ($arr["s_action"] === "del")
		{
			unset($stos[$arr["ident"]]);
		}
		elseif ($arr["s_action"] === "pause")
		{
			$elapsed = time() - $stos[$arr["ident"]]["start"];
			$stos[$arr["ident"]]["base"] += $elapsed;
			$stos[$arr["ident"]]["state"] = STOPPER_PAUSED;
		}
		elseif ($arr["s_action"] === "stop")
		{
			// stop timer and write row to task
			$stopper = $stos[$arr["ident"]];

			$elapsed = (time() - $stopper["start"]) + $stopper["base"];
			$el_hr = floor($elapsed / 3600) + ceil((($elapsed % 3600) / 60) / 15) * 0.25;
			$stopper["hours"] = $el_hr;

			$i = get_instance($stopper["type"]);
			$stopper["first_start"] = $arr["ident"];
			$rv = false;
			if(method_exists($i, "handle_stopper_stop"))
			{
				$rv = $i->handle_stopper_stop($stopper);

			}
			if(!$rv)
			{
				unset($stos[$arr["ident"]]);
			}
			else
			{
				$this->stop_error[$arr["ident"]] = $rv;
				// do something !!
			}
		}
		elseif ($arr["s_action"] === "start")
		{
			// pause all running timers
			foreach((array)$stos as $k => $stopper)
			{
				if ($stopper["state"] == STOPPER_RUNNING && $k != $arr["ident"])
				{
					$elapsed = time() - $stopper["start"];
					$stos[$k]["base"] += $elapsed;
					$stos[$k]["state"] = STOPPER_PAUSED;
				}
			}
			if($arr["id"])
			{
				foreach($stos as $k => $stopper)
				{
					if($stopper["id"] == $arr["id"])
					{
						$arr["ident"] = $k;
						break;
					}
				}
			}
			$new_stop = $stos[$arr["ident"]];
			if ($stos[$arr["ident"]]["state"] != STOPPER_RUNNING)
			{
				$new_stop["start"] = time();
			}

			if(!$arr["source_id"] && $oid = $stos[$arr["ident"]]["oid"])
			{
				$o = obj($oid);
				$arr["name"] = $o->name();
			}

			if($this->can("view", $arr["source_id"]))
			{
				$o = obj($arr["source_id"]);
				if(in_array($o->class_id(), $this->default_stoppers))
				{
					$new_stop["type"] = $arr["type"] = $o->class_id();
					$new_stop["oid"] = $arr["oid"] = $o->id();
					$arr["name"] = $o->name();
					//$this->change_stop_type(array($arr["ident"]));
					$inst = $o->instance();
					if (method_exists($inst, "handle_stopper_start"))
					{
						$inst->handle_stopper_start($o);
					}
				}

			}

			if (isset($arr["type"]))
			{
				$new_stop["type"] = $arr["type"];
			}

			if (isset($arr["name"]))
			{
				$new_stop["name"] = $arr["name"];
			}
			else
			{
				$arr["name"] = $new_stop["name"] = "Nimetu stopper";
			}
			$ident = $arr["ident"]?$arr["ident"]:$new_stop["start"];
			$new_stop["state"] = STOPPER_RUNNING;
			$new_stop["data"]["name"]["value"] = $arr["name"];
			$stos[$ident] = $new_stop;
			$url = $this->mk_my_orb("stopper_pop",array(), CL_TASK);
		}
		return $url;
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
		$p = new planner();
		return $p->save_participant_search_results($arr);
	}

	function callback_mod_tab($arr)
	{
		if ($arr["obj_inst"]->prop("is_personal") && aw_global_get("uid") != $arr["obj_inst"]->createdby())
		{
			if ($arr["id"] != "general")
			{
				return false;
			}
		}
		return true;
	}

	function _save_rows($arr)
	{
		$res = array();
		// go over existing rows and save info for those
		// add new rows that are without oid
		// I think rows should not be deleted. or we can add that later
		$task = obj($arr["request"]["id"]);
		$max_row = 0;
		$max_ord = 0;
		//paneb ikka ette maksimumi leidma juba, siis on lollikindlam
		//k2ib kyll topelt tsykli l2bi, kuid savestamisel vaevalt see oluline ajakadu on
		if(sizeof($_POST["rows"]) > 3)
		{
			foreach($_POST["rows"] as $row)
			{
				if($max_ord < $row["ord"])
				{
					$max_ord = $row["ord"];
				}
			}
		}
		foreach(safe_array($_POST["rows"]) as $_oid => $e)
		{
			if (!is_oid($_oid) || !$this->can("view", $_oid))
			{
				if ($e["task"] == "")
				{
					continue;
				}
				$o = $task->add_row();
				/*
				$o = obj();
				$o->set_class_id(CL_TASK_ROW);
				$o->set_parent($arr["request"]["id"]);
				$o->save();*/
				$is_mod = true;
			}
			else
			{
				$cs = $task->connections_from(array("to" => $_oid));
				$c = reset($cs);
				$o = $c->to();
				/*if ($e["task"] == "")
				{
					$o->delete();
					continue;
				}*/
				$is_mod = false;
			}

			list($d,$m,$y) = explode("/", $e["date"]);
			$_tm = mktime(0,0,0, $m, $d, $y);
			if ($o->class_id() == CL_CRM_MEETING)
			{
				if (date("d.m.Y", $o->prop("start1")) != date("d.m.Y", $_tm))
				{
					if ($o->prop("end") < $_tm)
					{
						$len = $o->prop("end") - $o->prop("start1");
						$o->set_prop("end", $_tm + $len);
					}

					$o->set_prop("start1", $_tm);
					$is_mod = true;
				}
			}
			else
			{
				if ($o->prop("date") != $_tm)
				{
					$o->set_prop("date", $_tm);
					$is_mod = true;
				}
			}

			if(isset($e["impl"]))
			{
				foreach(safe_array($e["impl"]) as $i)
				{
					if ($this->can("view", $i))
					{
						$this->add_participant($task, obj($i));
					}
				}
			}

			if(isset($e["task"]))
			{
				if ($o->prop("content") != $e["task"])
				{
					$o->set_prop("content", $e["task"]);
					$is_mod = true;
				}
			}

			if(isset($e["impl"]))
			{
				if ($o->class_id() == CL_CRM_MEETING)
				{
					$mti = $o->instance();
					$pr = array(
						"name" => "participants",
						"value" => $this->make_keys($e["impl"]),
					);
					$_POST["participants"] = $this->make_keys($e["impl"]);
					$mti->set_property(array(
						"obj_inst" => $o,
						"request" => $arr["request"],
						"prop" => $pr
					));
					$is_mod = true;
				}
				else
				{
					if ($o->prop("impl") != $this->make_keys($e["impl"]))
					{
						$o->set_prop("impl", $e["impl"]);
						$is_mod = true;
					}
				}
			}

			if(isset($e["time_guess"]))
			{
				$e["time_guess"] = str_replace(",", ".", $e["time_guess"]);
				if ($o->prop("time_guess") != $e["time_guess"])
				{
					$o->set_prop("time_guess", $e["time_guess"]);
					$is_mod = true;
				}
			}

			//j2rjekorra seadmine
			if($e["ord"] > $max_ord) $max_ord = $e["ord"];
			if($e["ord"] == null)
			{
				$e["ord"] = 10+$max_ord;
				$max_ord = $e["ord"];
			}
			if ($o->prop("ord") != $e["ord"])
			{
				$o->set_prop("ord", $e["ord"]);
				$is_mod = true;
			}

			if(isset($e["time_real"]))
			{
				$e["time_real"] = str_replace(",", ".", $e["time_real"]);
				if ($o->prop("time_real") != $e["time_real"])
				{
					$o->set_prop("time_real", $e["time_real"]);
					$is_mod = true;
				}
			}

			if(isset($e["time_to_cust"]))
			{
				if ($e["time_to_cust"] == "")
				{
					$e["time_to_cust"] = $e["time_real"];
				}

				$e["time_to_cust"] = str_replace(",", ".", $e["time_to_cust"]);
				if ($o->is_property("time_to_cust") and $o->prop("time_to_cust") != $e["time_to_cust"])
				{
					$o->set_prop("time_to_cust", $e["time_to_cust"]);
					$is_mod = true;
				}
			}

			if(isset($e["done"]))
			{
				if ($o->class_id() == CL_CRM_MEETING)
				{
					$o->set_prop("is_done", $e["done"] ? 8 : 0);
					$is_mod = true;
				}
				else
				{
					if ((int)$o->prop("done") != (int)$e["done"])
					{
						$o->set_prop("done", (int)$e["done"]);
						$is_mod = true;
					}
				}
			}

			if(!$o->prop("bill_no"))//isset($e["on_bill"]))
			{
				if ($o->class_id() != CL_CRM_MEETING)
				{
					if ((int)$o->prop("on_bill") != (int)$e["on_bill"])
					{
						$o->set_prop("on_bill", (int)$e["on_bill"]);
						if ($o->is_property("to_bill_date"))
						{
							$o->set_prop("to_bill_date", time());
						}
						$is_mod = true;
					}
				}
				else
				{
					$o->set_meta("on_bill", (int)$e["on_bill"]);
					$o->set_prop("send_bill", (int)$e["on_bill"]);
				}
			}

			if ($is_mod)
			{
				$o->save();
			}

			$task->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_ROW"
			));
		}
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
			$arr
		);
		return $arr['post_ru'];
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["predicates"] = 0;
		$arr["participants_h"] = 0;
		$arr["orderer_h"] = isset($request["alias_to_org"]) ? $request["alias_to_org"] : 0;
		$arr["project_h"] = isset($request["set_proj"]) ? $request["set_proj"] : 0;
		$arr["files_h"] = 0;
		if ($request["action"] === "new")
		{
			if (isset($request["add_to_cal"])) $arr["add_to_cal"] = $request["add_to_cal"];
			if (isset($request["alias_to_org"])) $arr["alias_to_org"] = $request["alias_to_org"];
			if (isset($request["reltype_org"])) $arr["reltype_org"] = $request["reltype_org"];
			if (isset($request["set_pred"])) $arr["set_pred"] = $request["set_pred"];
			if (isset($request["set_resource"])) $arr["set_resource"] = $request["set_resource"];
		}
	}

	function _req_get_s_folders($fld, $fldo, &$folders, $parent)
	{
		$this->_lv++;
		foreach($fld as $dat)
		{
			if ($dat["parent"] === $parent)
			{
				$folders[$fldo->id().":".$dat["id"]] = str_repeat("&nbsp;&nbsp;&nbsp;", $this->_lv).$dat["name"];
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
			$i = new crm_company();
			$prj = $i->get_my_projects();
			if (!count($prj))
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list(array("oid" => $prj));
			}
		}
		else
		{
			$filt = array(
				"class_id" => CL_PROJECT,
				"CL_PROJECT.RELTYPE_PARTICIPANT" => $arr["cust"]
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
		if ($row->is_property("time_to_cust"))
		{
			$row->set_prop("time_to_cust", strlen($arr["data"]["timetocust"]["value"])?$arr["data"]["timetocust"]["value"]:$arr["hours"]);
		}
		$row->set_prop("done", $arr["data"]["isdone"]["value"]?1:0);
		$row->set_prop("on_bill", $arr["data"]["tobill"]["value"]?1:0);
		$row->save();
//		$o->connect(array(
//			"to" => $row->id(),
//			"type" => "RELTYPE_ROW"
//		));
	}

	function _hrs_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$cff = new cfgform();
		$cfgform_id = $this->get_cfgform_for_object(array(
			"obj_inst" => $this->obj_inst,
			"args" => $arr["request"],
		));
		$has = false;
		if ($this->can("view", $cfgform_id))
		{
			$has = true;
			$ps = $cff->get_cfg_proplist($cfgform_id);
		}

			if (!$has || isset($ps["priority"]))
			{
				$t->define_field(array(
					"name" => "priority",
					"caption" => isset($ps) && $ps["priority"]["caption"] != "" ?  $ps["priority"]["caption"]  : t("Prioriteet"),
					"align" => "center",
				));
			}
			$t->define_field(array(
				"name" => "hours",
				"caption" => t("Tundide kulu"),
				"align" => "center",
			));
			if (!$has || isset($ps["num_hrs_guess"]))
			{
				$t->define_field(array(
					"name" => "time_guess",
					"caption" => isset($ps) && $ps["num_hrs_guess"]["caption"] != "" ?  $ps["num_hrs_guess"]["caption"] : t("Prognoos"),
					"align" => "center",
					"parent" => "hours",
				));
			}
			if (!$has || isset($ps["num_hrs_real"]))
			{
				$t->define_field(array(
					"name" => "time_real",
					"caption" => isset($ps) && $ps["num_hrs_real"]["caption"] != "" ? $ps["num_hrs_real"]["caption"] : t("Tegelik"),
					"align" => "center",
					"parent" => "hours",
				));
			}

			if (!$has || isset($ps["num_hrs_to_cust"]))
			{
				$t->define_field(array(
					"name" => "time_to_cust",
					"caption" => isset($ps) && $ps["num_hrs_to_cust"]["caption"] != "" ? $ps["num_hrs_to_cust"]["caption"] : t("Kliendile"),
					"align" => "center",
					"parent" => "hours",
				));
			}

			if (!$has || isset($ps["hr_price"]))
			{
				$t->define_field(array(
					"name" => "hr_price",
					"caption" => isset($ps) && $ps["hr_price"]["caption"] != "" ?  $ps["hr_price"]["caption"] : t("Tunnihind"),
					"align" => "center",
					"parent" => "hours",
				));
			}

			if (!$has || isset($ps["deal_price"]))
			{
				$t->define_field(array(
					"name" => "deal_price",
					"caption" => isset($ps) && $ps["deal_price"]["caption"] != "" ?  $ps["deal_price"]["caption"] : t("Kokkuleppehind"),
					"align" => "center"
				));

				$t->define_field(array(
					"name" => "deal_price_price",
					"caption" => isset($ps) && $ps["deal_price"]["caption"] != "" ?  $ps["deal_price"]["caption"] : t("Hind"),
					"align" => "center",
					"parent" => "deal_price",
				));
				$t->define_field(array(
					"name" => "deal_price_amount",
					"caption" => isset($ps) && $ps["deal_price"]["caption"] != "" ?  $ps["deal_price"]["caption"] : t("Kogus"),
					"align" => "center",
					"parent" => "deal_price",
				));
				$t->define_field(array(
					"name" => "deal_price_unit",
					"caption" => isset($ps) && $ps["deal_price"]["caption"] != "" ?  $ps["deal_price"]["caption"] : t("&Uuml;hik"),
					"align" => "center",
					"parent" => "deal_price",
				));
				$t->define_field(array(
					"name" => "deal_price_km",
					"caption" => isset($ps) && $ps["deal_price"]["caption"] != "" ?  $ps["deal_price"]["caption"] : t("KM"),
					"align" => "center",
					"parent" => "deal_price",
				));
			}

			if (!$has || isset($ps["hr_price_currency"]))
			{
				$t->define_field(array(
					"name" => "hr_price_currency",
					"caption" => isset($ps) && $ps["hr_price_currency"]["caption"] != "" ? $ps["hr_price_currency"]["caption"] : t("Valuuta"),
					"align" => "center"
				));
			}

			$t->define_field(array(
				"name" => "add_clauses",
				"caption" => t("Lisatingimused"),
				"align" => "center"
			));

			$t->define_field(array(
				"name" => "status",
				"caption" => t("Aktiivne"),
				"align" => "center",
				"parent" => "add_clauses",
			));
			$t->define_field(array(
				"name" => "is_done",
				"caption" => t("Tehtud"),
				"align" => "center",
				"parent" => "add_clauses",
			));

			if($arr["obj_inst"]->class_id() != CL_CRM_CALL)
			{
					$t->define_field(array(
					"name" => "whole_day",
					"caption" => t("Terve p&auml;ev"),
					"align" => "center",
					"parent" => "add_clauses",
				));
			}

			if($arr["obj_inst"]->class_id() == CL_TASK)$t->define_field(array(
				"name" => "is_goal",
				"caption" =>  t("Verstapost"),
				"align" => "center",
				"parent" => "add_clauses",
			));

			$t->define_field(array(
				"name" => "is_personal",
				"caption" => t("Isiklik"),
				"align" => "center",
				"parent" => "add_clauses",
			));

			if($arr["obj_inst"]->class_id() == CL_CRM_CALL)//teiste jaoks pole veel vajadust n2inud
			{
				$t->define_field(array(
					"name" => "promoter",
					"caption" =>  t("K&otilde;ne suund"),
					"align" => "center",
					"parent" => "add_clauses",
				));

				if(!$arr["obj_inst"]->bug_created())
				{
					$t->define_field(array(
						"name" => "create_bug",
						"caption" =>  t("Tee bugi"),
						"align" => "center",
						"parent" => "add_clauses",
					));
				}
			}
/*
			$t->define_field(array(
				"name" => "send_bill",
				"caption" => t("Arvele"),
				"align" => "center",
				"parent" => "add_clauses",
			));
*/
			if($arr["obj_inst"]->class_id() == CL_TASK)$t->define_field(array(
				"name" => "in_budget",
				"caption" =>  t("Eelarvesse"),
				"align" => "center",
				"parent" => "add_clauses",
			));

			$has_work_time = $arr["obj_inst"]->has_work_time();

			if(!$has_work_time)
			{
				$t->define_field(array(
					"name" => "is_work",
					"caption" => t("T&ouml;&ouml;aeg"),
					"align" => "center",
					"parent" => "add_clauses",
				));
			}


/*			if (!$has || isset($ps["bill_no"]))
			{
				$t->define_field(array(
					"name" => "bill_no",
					"caption" => isset($ps) && $ps["bill_no"]["caption"] != "" ? $ps["bill_no"]["caption"] : t("Arve number"),
					"align" => "center"
				));
			}
			if (!$has || isset($ps["code"]))
			{
				$t->define_field(array(
					"name" => "code",
					"caption" => isset($ps) && $ps["code"]["caption"] != "" ? $ps["code"]["caption"]  : t("Kood"),
					"align" => "center"
				));
			}
			if (!$has || isset($ps["service_type"]))
			{
				$t->define_field(array(
					"name" => "service_type",
					"caption" => isset($ps) && $ps["service_type"]["caption"] != "" ?  $ps["service_type"]["caption"] : t("Teenuse liik"),
					"align" => "center"
				));
			}*/

		$curr_object_list = new object_list(array(
			"class_id" => CL_CURRENCY
		));
		$curs = array();
		foreach($curr_object_list->arr() as $curr)
		{
			$curs[$curr->id()] = $curr->name();
		}
		$u = get_instance(CL_USER);
		$company = obj($u->get_current_company());
		if(!$arr["obj_inst"]->prop("hr_price_currency") && $arr["obj_inst"]->class_id())
		{
			$arr["obj_inst"]->set_prop("hr_price_currency", $company->prop("currency"));
		}


		// small conversion - if set, create a relation instead and clear, so that we can have multiple
/*		if ($this->can("view", $arr["obj_inst"]->prop("bill_no") ))
		{
			$arr["obj_inst"]->connect(array(
				"to" => $arr["obj_inst"]->prop("bill_no"),
				"type" => "RELTYPE_BILL"
			));
			$arr["obj_inst"]->set_prop("bill_no", "");
			$arr["obj_inst"]->save();
		}

		$bno = "";
		if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
		{
			$cs = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_BILL"));
			if (!count($cs))
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list($cs);
			}
			$bno = html::obj_change_url($ol->arr());
		}

		if ($bno == "" && is_object($arr["obj_inst"]) && !$arr["new"])
		{
			$bno = html::href(array(
				"url" => $this->mk_my_orb("create_bill_from_task", array("id" => $arr["obj_inst"]->id(),"post_ru" => get_ru())),
				"caption" => t("Loo uus arve")
			));
		}

		$stypes = new object_list(array(
			"class_id" => CL_CRM_SERVICE_TYPE,
			"lang_id" => array(),
			"site_id" => array()
		));*/
		$t->non_filtered = 1;

		$t->define_data(array(
			"priority" => html::textbox(array(
				"name" => "priority",
				"value" => $arr["obj_inst"]->prop("priority"),
				"size" => 2
			)),
			"time_guess" => html::textbox(array(
				"name" => ($arr["obj_inst"]->class_id() != CL_TASK) ? "time_guess" : "num_hrs_guess",
				"value" => is_object($arr["obj_inst"]) ? ($arr["obj_inst"]->class_id() != CL_TASK ? $arr["obj_inst"]->prop("time_guess") : $arr["obj_inst"]->prop("num_hrs_guess")) : 0,
				"size" => 5
			)),
			"time_real" => html::textbox(array(
				"name" => ($arr["obj_inst"]->class_id() != CL_TASK) ? "time_real" : "num_hrs_real",
				"value" => is_object($arr["obj_inst"]) ? ($arr["obj_inst"]->class_id() != CL_TASK ? $arr["obj_inst"]->prop("time_real") : $arr["obj_inst"]->prop("num_hrs_real")): 0,
				"size" => 5
			)),
			"time_to_cust" => html::textbox(array(
				"name" => ($arr["obj_inst"]->class_id() != CL_TASK) ? "time_to_cust" :"num_hrs_to_cust",
				"value" => is_object($arr["obj_inst"]) ? ($arr["obj_inst"]->class_id() != CL_TASK ? $arr["obj_inst"]->prop("time_to_cust") : $arr["obj_inst"]->prop("num_hrs_to_cust")): 0,
				"size" => 5
			)),
			"hr_price" => html::textbox(array(
				"name" => "hr_price",
				"value" => $arr["obj_inst"]->prop("hr_price"),
				"size" => 3
			)),
			"deal_price_price" => html::textbox(array(
				"name" => "deal_price",
				"value" => $arr["obj_inst"]->prop("deal_price"),
				"size" => 4
			)),
			"deal_price_amount" => html::textbox(array(
				"name" => "deal_amount",
				"value" => $arr["obj_inst"]->prop("deal_amount"),
				"size" => 4
			)),
			"deal_price_unit" => html::textbox(array(
				"name" => "deal_unit",
				"value" => $arr["obj_inst"]->prop("deal_unit"),
				"size" => 4
			)),
			"deal_price_km" => html::checkbox(array(
				"name" => "deal_has_tax",
				"value" => 1,
				"checked" => $arr["obj_inst"]->prop("deal_has_tax"),
			)),
			"hr_price_currency" => html::select(array(
				"name" => "hr_price_currency",
				"options" => $curs,
				"value" => $arr["obj_inst"]->prop("hr_price_currency"),
			)),
			"create_bug" => html::checkbox(array(
				"name" => "add_clauses[create_bug]",
				"value" => 1,
			)),
//			"bill_no" => $bno,
		//	"code" => $arr["obj_inst"]->prop("code"),
/*			"service_type" => html::select(array(
				"name" => "service_type",
				"options" => array("" => "") + $stypes->names(),
				"value" => $arr["obj_inst"]->prop("service_type")
			))*/

			"status" => html::checkbox(array(
				"name" => "add_clauses[status]",
				"value" => 1,
				"checked" => is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("status") == STAT_ACTIVE ? 1 : 0,
			)),
			"is_done" => html::checkbox(array(
				"name" => "add_clauses[is_done]",
				"value" => 1,
				"checked" => is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("is_done") ? 8 : 0,
			)),
			"whole_day" => html::checkbox(array(
				"name" => "add_clauses[whole_day]",
				"value" => 1,
				"checked" => is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("whole_day") ? 1 : 0,
			)),
			"is_personal" => html::checkbox(array(
				"name" => "add_clauses[is_personal]",
				"value" => 1,
				"checked" => is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("is_personal") ? 1 : 0,
			)),

			"promoter" => html::select(array(
				"name" => "add_clauses[promoter]",
				"value" => $arr["obj_inst"]->prop("promoter"),
				"options" => array("1" => t("Tuli sisse") , "0" => t("L&auml;ks v&auml;lja")),
			)),

/*			"send_bill" => html::checkbox(array(
				"name" => "add_clauses[send_bill]",
				"value" => 1,
				"checked" => (is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("send_bill")) || isset($arr["new"]) ? 1 : 0,
			)),*/
			"is_work" => html::checkbox(array(
				"name" => "add_clauses[is_work]",
				"value" => 1,
			)),
			"in_budget" => html::checkbox(array(
				"name" => "add_clauses[in_budget]",
				"value" => 1,
				"checked" => is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("in_budget") ? 1 : 0,
			)),
			"is_goal" => html::checkbox(array(
				"name" => "add_clauses[is_goal]",
				"value" => 1,
				"checked" => is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("is_goal") ? 1 : 0,
			)),
		));
	}

	function save_add_clauses($arr)
	{
		$arr["obj_inst"]->set_status(empty($arr["request"]["add_clauses"]["status"]) ? STAT_NOTACTIVE : STAT_ACTIVE);
		$arr["obj_inst"]->set_prop("is_done", empty($arr["request"]["add_clauses"]["is_done"]) ? 0 : 8);

		if($arr["obj_inst"]->class_id() != CL_CRM_CALL)
		{
			$arr["obj_inst"]->set_prop("whole_day", empty($arr["request"]["add_clauses"]["whole_day"]) ? 0 : 1);
			$arr["obj_inst"]->set_prop("promoter", empty($arr["request"]["add_clauses"]["promoter"]) ? 0 : 1);
		}
		elseif($arr["obj_inst"]->class_id() == CL_TASK)
		{
			$arr["obj_inst"]->set_prop("is_goal", empty($arr["request"]["add_clauses"]["is_goal"]) ? 0 : 1);
		}

		$arr["obj_inst"]->set_prop("is_personal", empty($arr["request"]["add_clauses"]["is_personal"]) ? 0 : 1);
		$arr["obj_inst"]->set_prop("in_budget", empty($arr["request"]["add_clauses"]["in_budget"]) ? 0 : 1);
		// $arr["obj_inst"]->set_prop("send_bill", empty($arr["request"]["add_clauses"]["send_bill"]) ? 0 : 1);

		if(!empty($arr["request"]["add_clauses"]["is_work"]))
		{
			$rowdata = array("time_real" => $arr["obj_inst"]->prop("time_real"), "time_to_cust" => $arr["obj_inst"]->prop("time_to_cust"));
			$arr["obj_inst"]->set_primary_row($rowdata);
		}

		if(empty($arr["request"]["add_clauses"]["send_bill"]) || $arr["obj_inst"]->if_can_set_billable())
		{
//			$arr["obj_inst"]->set_prop("send_bill", $arr["request"]["add_clauses"]["send_bill"] ? 1 : 0);
		}
		else
		{
			$this->show_error_text(sprintf(t("Puuduvad &otilde;igused m&auml;&auml;rata tehtud t&ouml;id arvele! P&ouml;&ouml;rduge kliendihaldur %s poole!"), $arr["obj_inst"]->get_client_mgr_name()));
			return PROP_ERROR;
		}

		return PROP_OK;
	}

	function __parts_sort($a, $b)
	{
		return strcmp($a->name(), $b->name());
	}

	function _get_co_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

//TMP DISABLED
//TODO: these must call a proper method for adding a customer
// reltype_customer is deprecated!
		// $tb->add_button(array(
			// "name" => "new_cust",
			// "parent" => "cust",
			// "tooltip" => t("Uus Organisatsioon"),
			// "url" => html::get_new_url(CL_CRM_COMPANY, $arr["obj_inst"]->parent(), array(
				// "return_url" => get_ru(),
				// "alias_to" => $arr["obj_inst"]->id(),
				// "reltype" => 3 // RELTYPE_CUSTOMER
			// )),
		// ));
		// $tb->add_button(array(
			// "name" => "new_cust2",
			// "parent" => "cust2",
			// "tooltip" => t("Uus isik"),
			// "url" => html::get_new_url(CL_CRM_PERSON, $arr["obj_inst"]->parent(), array(
				// "return_url" => get_ru(),
				// "alias_to" => $arr["obj_inst"]->id(),
				// "reltype" => 3 // RELTYPE_CUSTOMER
			// )),
		// ));
//END TMP DISABLED

		$url = $this->mk_my_orb("do_search", array("pn" => "orderer_h", "clid" => array(
			CL_CRM_PERSON,
			CL_CRM_COMPANY
		)), "popup_search");
		$tb->add_button(array(
			"name" => "search",
			"tooltip" => t("Otsi tellijat"),
			"url" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_rels"
		));

	}

	function _get_project_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			"name" => "new_cust",
			"parent" => "cust",
			"tooltip" => t("Uus projekt"),
			"url" => html::get_new_url(CL_PROJECT, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 4 // RELTYPE_PROJECT
			)),
		));

		$s = isset($arr["s"]) ? $arr["s"] : null;

		$url = $this->mk_my_orb("do_search", array("pn" => "project_h", "clid" => CL_PROJECT, "multiple" => 1, "s" => $s), "crm_project_search");
		$tb->add_button(array(
			"name" => "search",
			"tooltip" => t("Otsi projekte"),
			"url" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_rels"
		));

	}

	function _get_impl_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"parent" => "new",
			"name" => "part",
			"text" => t("Uus osaleja"),
		));

		if (is_oid($arr["obj_inst"]->id()))
		{
			foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
			{
				$cust = $c->to();
				$tb->add_menu_item(array(
					"parent" => "part",
					"text" => sprintf(t("Lisa isik organisatsiooni %s"), $cust->name()),
					"link" => html::get_new_url(CL_CRM_PERSON, $cust->id(), array(
						"return_url" => get_ru(),
						"add_to_task" => $arr["obj_inst"]->id(),
						"add_to_co" => $cust->id(),
					))
				));
			}
		}

		$cur_co = $cur  = get_current_company();
		if ($cur_co)
		{
			$tb->add_menu_item(array(
				"text" => sprintf(t("Lisa isik organisatsiooni %s"), $cur_co->name()),
				"parent" => "part",
				"link" => html::get_new_url(CL_CRM_PERSON, $cur_co->id(), array(
					"return_url" => get_ru(),
					"add_to_task" => $arr["obj_inst"]->id(),
					"add_to_co" => $cur_co->id()
				))
			));
		}

		$s = isset($arr["s"]) ? $arr["s"] : null;

		$url = $this->mk_my_orb("do_search", array("pn" => "participants_h", "clid" => CL_CRM_PERSON,"multiple" => 1, "s" => $s), "crm_participant_search");
		$tb->add_button(array(
			"name" => "part_search",
			"tooltip" => t("Otsi"),
			"url" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
//			"name" => "",
		));

		$tb->add_menu_button(array(
//			"parent" => "part_search",
			"text" => t("Lisa osaleja t&ouml;&ouml;tajate hulgast:"),
			"name" => "search_part",
		));


		//otsib enda ja kliendi t88tajate hulgast osalejaid
		if ($cur_co)
		{
			$workers = crm_company::get_employee_picker($cur_co, false, true);
			if(!count($workers))
			{
				$workers = crm_company::get_employee_picker($cur_co, false, false);
			}
			if(sizeof($workers))
			{
				$tb->add_sub_menu(array(
					"parent" => "search_part",
					"text" => $cur_co->name(),
					"name" => "part_our",
				));
				foreach($workers as $oid=>$name)
				{
					$worker = obj($oid);
					$url = $this->mk_my_orb("add_part_popup", array("part" => $worker->id(), "task" => $arr["obj_inst"]->id()), "task");
					$tb->add_menu_item(array(
						"parent" => "part_our",
						"text" => $worker->name(),
						"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
					));
				}
			}
		}

		if (is_oid($arr["obj_inst"]->id()))
		{
			foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
			{
				$customer = $c->to();
				if($customer->class_id() != CL_CRM_COMPANY)
				{
					continue;
				}
				$cust_workers = crm_company::get_employee_picker($customer, false, true);
				if(!count($cust_workers))
				{
					$cust_workers = crm_company::get_employee_picker($customer, false, false);
				}
				if(sizeof($cust_workers))
				{
					$tb->add_sub_menu(array(
						"parent" => "search_part",
						"text" => $customer->name(),
						"name" => "part_cust_".$customer->id(),
					));
					foreach($cust_workers as $c=>$name)
					{
						$worker = obj($c);
						$url = $this->mk_my_orb("add_part_popup", array("part" => $worker->id(), "task" => $arr["obj_inst"]->id()), "task");
						$tb->add_menu_item(array(
							"parent" => "part_cust_".$customer->id(),
							"text" => $worker->name(),
							"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
						));
					}
				}
			}
		}

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_parts"
		));
	}

	function _parts_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"text" => t("Uus"),
		));

		$tb->add_sub_menu(array(
			"parent" => "new",
			"name" => "cust",
			"text" => t("Tellija"),
		));

		if (!is_object($arr["obj_inst"]))
		{
			return;
		}

		$tb->add_menu_item(array(
			"parent" => "cust",
			"text" => t("Organisatsioon"),
			"link" => html::get_new_url(CL_CRM_COMPANY, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 3 // RELTYPE_CUSTOMER
			)),
		));
		$tb->add_menu_item(array(
			"parent" => "cust",
			"text" => t("Isik"),
			"link" => html::get_new_url(CL_CRM_PERSON, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 3 // RELTYPE_CUSTOMER
			)),
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Projekt"),
			"link" => html::get_new_url(CL_PROJECT, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 4 // RELTYPE_PROJECT
			)),
		));

		$tb->add_sub_menu(array(
			"parent" => "new",
			"name" => "part",
			"text" => t("Osaleja"),
		));

		if (is_oid($arr["obj_inst"]->id()))
		{
			foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
			{
				$cust = $c->to();
				$tb->add_menu_item(array(
					"parent" => "part",
					"text" => sprintf(t("Lisa isik organisatsiooni %s"), $cust->name()),
					"link" => html::get_new_url(CL_CRM_PERSON, $cust->id(), array(
						"return_url" => get_ru(),
						"add_to_task" => $arr["obj_inst"]->id(),
						"add_to_co" => $cust->id(),
					))
				));
			}
		}

		$cur_co = get_current_company();
		$tb->add_menu_item(array(
			"text" => sprintf(t("Lisa isik organisatsiooni %s"), $cur_co->name()),
			"parent" => "part",
			"link" => html::get_new_url(CL_CRM_PERSON, $cur_co->id(), array(
				"return_url" => get_ru(),
				"add_to_task" => $arr["obj_inst"]->id(),
				"add_to_co" => $cur_co->id()
			))
		));

		$tb->add_menu_button(array(
			"name" => "search",
			"text" => t("Otsi"),
			"img" => "search.gif"
		));

		$url = $this->mk_my_orb("do_search", array("pn" => "orderer_h", "clid" => array(
			CL_CRM_PERSON,
			CL_CRM_COMPANY
		)), "popup_search");
		$tb->add_menu_item(array(
			"name" => "search",
			"text" => t("Tellija"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
		));


		$url = $this->mk_my_orb("do_search", array("pn" => "project_h", "clid" => CL_PROJECT, "multiple" => 1), "crm_project_search");
		$tb->add_sub_menu(array(
			"parent" => "search",
			"text" => t("Projekt"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
			"name" => "project_search",
		));

		$cur = get_current_company();
		$s = array("co" => array($cur->id() => $cur->id()));
		if (is_oid($arr["obj_inst"]->id()))
		{
			foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
			{
				$s["co"][$c->prop("to")] = $c->prop("to");
			}
		}

		$url = $this->mk_my_orb("do_search", array("pn" => "participants_h", "clid" => CL_CRM_PERSON,"multiple" => 1, "s" => $s), "crm_participant_search");
		$tb->add_sub_menu(array(
			"parent" => "search",
			"text" => t("Osaleja"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
			"name" => "part_search",
		));

		$tb->add_menu_item(array(
			"parent" => "part_search",
			"text" => t("Otsi"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
//			"name" => "",
		));

		//otsib enda ja kliendi t88tajate hulgast osalejaid
		if (is_oid($cur->id()))
		{
			$workers = crm_company::get_employee_picker($cur, false, true);
			if(!count($workers))
			{
				$workers = crm_company::get_employee_picker($cur, false, false);
			}
			if(sizeof($workers))
			{
				$tb->add_sub_menu(array(
					"parent" => "part_search",
					"text" => $cur->name(),
					"name" => "part_our",
				));
				foreach($workers as $oid=>$name)
				{
					$worker = obj($oid);
					$url = $this->mk_my_orb("add_part_popup", array("part" => $worker->id(), "task" => $arr["obj_inst"]->id()), "task");
					$tb->add_menu_item(array(
						"parent" => "part_our",
						"text" => $worker->name(),
						"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
					));
				}
			}
		}

		if (is_oid($arr["obj_inst"]->id()))
		{
			foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
			{
				$customer = $c->to();
				if($customer->class_id() != CL_CRM_COMPANY)
				{
					continue;
				}
				$cust_workers = crm_company::get_employee_picker($customer, false, true);
				if(!count($cust_workers))
				{
					$cust_workers = crm_company::get_employee_picker($customer, false, false);
				}
				if(sizeof($cust_workers))
				{
					$tb->add_sub_menu(array(
						"parent" => "part_search",
						"text" => $customer->name(),
						"name" => "part_cust",
					));
					foreach($cust_workers as $c=>$name)
					{
						$worker = obj($c);
						$url = $this->mk_my_orb("add_part_popup", array("part" => $worker->id(), "task" => $arr["obj_inst"]->id()), "task");
						$tb->add_menu_item(array(
							"parent" => "part_cust",
							"text" => $worker->name(),
							"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
						));
					}
				}
			}
		}


		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_rels"
		));

		$s = array("co" => array($cur->id() => $cur->id()));
		if (is_oid($cur->id()))
		{
			$cust_data = new object_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				"seller" => $cur->id()
			));
			if($cust_data->count() < 30)
			{
				foreach($cust_data->list as $cdid)
				{
					$cd = obj($cdid);
					$s["co"][$cd->prop("buyer")] = $cd->prop("buyer");
				}
			}
			$cust_data2 = new object_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				"buyer" => $cur->id()
			));
			if($cust_data2->count() < 30)
			{
				foreach($cust_data2->list as $cdid)
				{
					$cd = obj($cdid);
					$s["co"][$cd->prop("seller")] = $cd->prop("seller");
				}
			}
		}

		$url = $this->mk_my_orb("do_search", array("pn" => "project_h", "clid" => CL_PROJECT, "multiple" => 1, "s" => $s), "crm_project_search");
		$tb->add_menu_item(array(
			"parent" => "project_search",
			"text" => t("Otsi"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
		));

		$customers = array("1");
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
		{
			$customers[] = $c->prop("to");
		}

		$filter = array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_ORDERER.id" => $customers
		);

		if($arr["request"]["class"] === "crm_meeting")
		{
			$user = get_instance(CL_USER);
			$person = $user->get_current_person();
			$filter["CL_PROJECT.RELTYPE_PARTICIPANT"] = $person;
		}

		$ol = new object_list($filter);

		foreach($ol->arr() as $project)
		{
			$url = $this->mk_my_orb("add_project_popup", array("project" => $project->id(), "task" => $arr["obj_inst"]->id()), "task");
			$tb->add_menu_item(array(
				"parent" => "project_search",
				"text" => $project->name(),//t("Projekt"),
				"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
			));
		}
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
	}

	function _init_co_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel_ord",
			"field" => "oid"
		));
		$t->define_field(array(
			"name" => "orderer",
			"caption" => t("Tellija"),
			"sortable" => 1,
			"width" => "60%"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"parent" => "orderer",
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"sortable" => 1,
			"parent" => "orderer",
		));
		$t->define_field(array(
			"name" => "contact",
			"caption" => t("Kontaktisik"),
			"sortable" => 1,
			"parent" => "orderer",
		));
		$t->define_field(array(
			"name" => "party",
			"caption" => t("Osalus"),
			"sortable" => 1,
			"width" => "39%"
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Tundides"),
			"sortable" => 1,
			"parent" => "party",
		));
		$t->define_field(array(
			"name" => "percentage",
			"caption" => t("Protsentides"),
			"sortable" => 1,
			"parent" => "party",
		));
	}

	function _co_table($arr)
	{

		if (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id()))
		{
			return PROP_IGNORE;
		}
		$orderers = $arr["obj_inst"]->get_orderers();
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_co_table($t);

		foreach($orderers->arr() as  $c)
		{
			$row = $arr["obj_inst"]->get_party_obj($c->id());
			$t->define_data(array(
				"oid" => $c->id(),
				"name" => html::obj_change_url($c),
				"phone" => html::obj_change_url($c->prop("phone_id")),
				"contact" => html::obj_change_url($c->prop("contact_person")),
				"hours" => html::textbox(array(
					"name" => "orderers[".$c->id()."][hours]",
					"value" => $row ? $row->prop("hours") : "",
					"size" => 3
				)),
				"percentage" => html::textbox(array(
					"name" => "orderers[".$c->id()."][percentage]",
					"value" => $row ? $row->prop("percentage") : "",
					"size" => 3
				)),
			));
		}
	}

	function _save_co_table($arr)
	{
		$rows = isset($arr["request"]["orderers"]) ? safe_array($arr["request"]["orderers"]) : array();
		foreach($rows as $key => $row)
		{
			$set = 0;
			foreach($row as $prop => $val)
			{
				if($val)
				{
					$set = 1;
					break;
				}
			}
			if($set)
			{
				$row["participant"] = $key;
				$arr["obj_inst"]->set_party($row);
			}
		}
	}

	function _init_proj_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel_proj",
			"field" => "oid"
		));
		$t->define_field(array(
			"name" => "project",
			"caption" => t("Projekt"),
			"sortable" => 1,
			"width" => "60%"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"parent" => "project"
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Staatus"),
			"sortable" => 1,
			"parent" => "project"
		));
		$t->define_field(array(
			"name" => "deadline",
			"caption" => t("L&otilde;ppt&auml;htaeg"),
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y",
			"parent" => "project"
		));
		$t->define_field(array(
			"name" => "party",
			"caption" => t("Osalus"),
			"sortable" => 1,
			"width" => "39%"
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("Osalus tundides"),
			"sortable" => 1,
			"parent" => "party"
		));
		$t->define_field(array(
			"name" => "percentage",
			"caption" => t("Osalus protsentides"),
			"sortable" => 1,
			"parent" => "party"
		));
	}

	function _proj_table($arr)
	{

		if (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id()) || !sizeof($p_conn = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PROJECT"))))
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];

		$this->_init_proj_table($t);

		$p = get_instance(CL_PROJECT);
		foreach($p_conn as $c)
		{
			$c = $c->to();
			$row = $arr["obj_inst"]->get_party_obj($c->id());
			$t->define_data(array(
				"oid" => $c->id(),
				"name" => html::obj_change_url($c),
				"status" => $p->states[$c->prop("state")],
				"deadline" => $c->prop("deadline"),
				"hours" => html::textbox(array(
					"name" => "orderers[".$c->id()."][hours]",
					"value" => $row ? $row->prop("hours") : "",
					"size" => 3
				)),
				"percentage" => html::textbox(array(
					"name" => "orderers[".$c->id()."][percentage]",
					"value" => $row ? $row->prop("percentage") : "",
					"size" => 3
				)),
			));
		}
	}

	function _init_parts_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel_part",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "participant",
			"caption" => t("Osaleja"),
			"sortable" => 1,
			"width" => "50%"
		));
		$t->define_field(array(
			"name" => "part",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"parent" => "participant"
		));
		$t->define_field(array(
			"name" => "prof",
			"caption" => t("Ametinimetus"),
			"sortable" => 1,
			"parent" => "participant"
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"sortable" => 1,
			"parent" => "participant"
		));

		$t->define_field(array(
			"name" => "work",
			"caption" => t("T&ouml;&ouml;aeg"),
			"sortable" => 1,
			"width" => "49%"
		));
		$t->define_field(array(
			"name" => "time_guess",
			"caption" => t("Prognoositud tunde"),
			"sortable" => 1,
			"parent" => "work"
		));
		$t->define_field(array(
			"name" => "time_real",
			"caption" => t("Kulunud tunde"),
			"sortable" => 1,
			"parent" => "work"
		));
		$t->define_field(array(
			"name" => "time_to_cust",
			"caption" => t("Tunde kliendile"),
			"sortable" => 1,
			"parent" => "work"
		));
/*		$t->define_field(array(
			"name" => "done",
			"caption" => t("Tehtud"),
			"sortable" => 1,
			"width" => "5%"
		));*/
		$t->define_field(array(
			"name" => "on_bill",
			"caption" => t("Arvele"),
			"sortable" => 1,
			"parent" => "work"
		));
	}

	function fast_add_participants($arr, $types)
	{
		if(isset($_SESSION["event"]) && is_array($_SESSION["event"]["participants"]) && !$arr["new"])
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

	function save_parts_table($arr)
	{
		$rows = isset($arr["request"]["rows"]) ? safe_array($arr["request"]["rows"]) : array();
		foreach($rows as $key => $row)
		{
			if(!isset($row["on_bill"]))
			{
				$row["on_bill"] = 0;
			}
			$set = 1;//0;
/*			foreach($row as $prop => $val)
			{
				if($val)
				{
					$set = 1;
					break;
				}
			}*/
			if($set && $this->can("view"  , $key))
			{
				$row["person"] = $key;
				$arr["obj_inst"]->set_primary_row($row);
			}
		}
	}

	function _parts_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_parts_table($t);

		if (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$p = new project();

		//syndmuste kiirlisamiseks teeb sellise h2ki
		$types = array();
		$this->fast_add_participants($arr, $types);
		$parts = $arr["obj_inst"]->get_participants();
		if(!$parts->count())
		{
			return PROP_IGNORE;
		}

		foreach($parts->arr() as $c)
		{
			$name = obj($c);
			$name = $name->name();
			$oid = method_exists($c, "company_id") ? $c->company_id() : null;
			if($oid)
			{
				$obj = obj($oid);
				$name = strlen($tmp = $obj->prop("short_name"))?$name." (".$tmp.")":$name;
			}
			$data = array(
				"oid" => $c->id(),
				"part" => html::obj_change_url($c, $name),
				"prof" => html::obj_change_url($c->prop("rank")),
				"phone" => html::obj_change_url($c->prop("phone"))
			);

			$row = $arr["obj_inst"]->get_primary_row_for_person($c->id());
			$data["time_guess"] = html::textbox(array(
				"name" => "rows[".$c->id()."][time_guess]",
				"value" => $row ? $row->prop("time_guess") : "",
				"size" => 3
			));
			$data["time_real"] = html::textbox(array(
				"name" => "rows[".$c->id()."][time_real]",
				"value" => $row ? $row->prop("time_real") : "",
				"size" => 3
			));
			$data["time_to_cust"] = html::textbox(array(
				"name" => "rows[".$c->id()."][time_to_cust]",
				"value" => $row ? $row->prop("time_to_cust") : "",
				"size" => 3
			));
/*			$data["done"] = html::checkbox(array(
				"name" => "rows[".$c->id()."][done]",
				"value" => 1,
				"checked" => $row ? $row->prop("done") : "",
			));*/
			$data["on_bill"] = $row && $row->prop("bill_id") ? $row->prop("bill_id.bill_no") :
				html::checkbox(array(
					"name" => "rows[".$c->id()."][on_bill]",
					"value" => 1,
					"checked" => $row ? $row->prop("on_bill") : "",
			));

			$t->define_data($data);
		}
	}

	/**
		@attrib name=new_files_on_demand all_args=1
	**/
	function new_files_on_demand($arr)
	{

		$tb = new popup_menu();
		$tb->begin_menu("new_pop");

		$u = get_instance(CL_USER);
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

		$u = get_instance(CL_USER);
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
 			$f = new crm_company_docs_impl();
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

	function _files_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "nemw",
//			"tooltip" => t("Uus"),
			"load_on_demand_url" => $this->mk_my_orb("new_files_on_demand", array("obj_inst" => $arr["obj_inst"] -> id())),
		));

		// insert folders where to add
/* 		$u = get_instance(CL_USER);
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
 				"parent" => "nemw",
 				"name" => "mainf",
 				"text" => $fldo->name(),
 			));
 			$this->_add_fa($tb, "mainf", $fldo->id());
 			$this->_req_level = 0;
 		$this->_req_get_folders_tb($ot, $folders, $fldo->id(), $tb, "mainf");
 		}
*/
		$url = $this->mk_my_orb("do_search", array("pn" => "files_h", "clid" => array(
			CL_FILE,CL_CRM_MEMO,CL_CRM_DOCUMENT,CL_CRM_DEAL,CL_CRM_OFFER,CL_MENU
		), "multiple" => 1), "task_file_search");
		$tb->add_button(array(
			"name" => "search",
//			"img" => "search.gif",
			"tooltip" => t("Otsi"),
			"url" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)"
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_rels"
		));
	}

	function _req_get_folders_tb($ot, &$folders, $parent, $tb, $parent_nm,$task)
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

	function _add_fa($tb, $pt_n, $pt,$task)
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

	function _init_files_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Manuse nimi"),
			"sortable" => 1,
			"width" => "40%"
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"sortable" => 1,
			"width" => "35%"
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Viimane muutja"),
			"sortable" => 1,
			"width" => "25%"
		));
	}

	function _files_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_files_table($t);

		if (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$clss = aw_ini_get("classes");
		$u = get_instance(CL_USER);
		$objs = array();
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_FILE")) as $c)
		{
			if ($c->prop("to.class_id") == CL_MENU)
			{
				$ol = new object_list(array(
					"class_id" => array(CL_FILE,CL_CRM_MEMO,CL_CRM_DOCUMENT,CL_CRM_DEAL,CL_CRM_OFFER),
					"parent" => $c->prop("to")
				));
				foreach($ol->arr() as $o)
				{
					$objs[] = $o;
				}
			}
			else
			{
				$objs[] = $c->to();
			}
		}

		foreach($objs as $c)
		{
			$m = $c->modifiedby();
			$m = $u->get_person_for_uid($m);
			$po = obj($c->parent());
			$t->define_data(array(
				"oid" => "o_".$c->id(),
				"name" => html::obj_change_url($c),
				"type" => $clss[$c->class_id()]["name"],
				"modifiedby" => html::obj_change_url($m),
				"parent_name" => $po->name()
			));
		}
		$t->set_rgroupby(array("parent_name" => "parent_name"));
	}

	function _bills_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		if (!$arr["new"])
		{

			$tb->add_button(array(
				"name" => "new_bill",
				"parent" => "cust",
				"tooltip" =>t("Loo uus arve"),
				"url" => $this->mk_my_orb("create_bill_from_task", array("id" => 							$arr["obj_inst"]->id(),"post_ru" => get_ru())),
			));
		}
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_objects"
		));
	}

	function _init_bills_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$t->define_field(array(
			"name" => "bill",
			"caption" => t("Arve"),
			"sortable" => 1,
			"width" => "90%"
		));
	}

	function _bills_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		if (is_oid($arr["obj_inst"]->id()) && sizeof($cs = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_BILL"))))
		{
			if (!count($cs))
			{
				$ol = new object_list();
			}
			else
			{
				$ol = new object_list($cs);
			}
		}
		else
		{
			return PROP_IGNORE;
		}

		$this->_init_bills_table($t);

		foreach($ol->arr() as $c)
		{
			$t->define_data(array(
				"oid" => $c->id(),
				"bill" => html::obj_change_url($c),
			));
		}
		$t->set_rgroupby(array("parent_name" => "parent_name"));
	}

	function _get_task_results_toolbar(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		if (!$arr["new"])
		{

			$tb->add_button(array(
				"name" => "new_call",
				"tooltip" => t("Uus k&otilde;ne"),
				"url" => $this->mk_my_orb("new", array("parent" => $arr["obj_inst"]->parent(), "return_url" => get_ru()))
			));
		}
	}

	function _get_task_results_table(&$arr)
	{
		if (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id()))
		{
			return;
		}

		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Tegevuse nimi"),
			"width" => "40%"
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"width" => "35%"
		));
		$t->define_field(array(
			"name" => "modifiedby",
			"caption" => t("Viimane muutja"),
			"width" => "25%"
		));

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_RESULT_TASK")) as $c)
		{
			$result_task = $c->to();
			$m = $c->modifiedby();
			$m = $u->get_person_for_uid($m);
			$po = obj($c->parent());
			$t->define_data(array(
				"oid" => "o_".$c->id(),
				"name" => html::obj_change_url($c),
				"type" => $clss[$c->class_id()]["name"],
				"modifiedby" => html::obj_change_url($m),
				"parent_name" => $po->name()
			));
		}

		$t->set_rgroupby(array("parent_name" => "parent_name"));
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

		if (isset($arr["sel_proj"]) && is_array($arr["sel_proj"]) && count($arr["sel_proj"]))
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

		if (isset($arr["sel_part"]) && is_array($arr["sel_part"]) && count($arr["sel_part"]))
		{
			$arr["check"] = $arr["sel_part"];
			$arr["event_id"] = $arr["id"];
			post_message_with_param(
				MSG_MEETING_DELETE_PARTICIPANTS,
				CL_CRM_MEETING,
				$arr
			);
		}

		if (isset($arr["sel"]) && is_array($arr["sel"]) && count($arr["sel"]))
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
				$arr
			);
		}
		return $arr["post_ru"];
	}

	function _init_predicates_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$t->define_field(array(
			"name" => "predicates",
			"caption" => t("Eeldustegevus"),
			"sortable" => 1,
			"width" => "80%"
		));
	}

	function _predicates_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_predicates_table($t);

		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		foreach($arr["obj_inst"]->connections_from(array("type" => 9)) as $c)
		{
			$c = $c->to();
			$t->define_data(array(
				"oid" => $c->id(),
				"predicates" => html::obj_change_url($c),
			));
		}
		return $t->draw();
	}


	function _predicates_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Uus"),
		));

		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Toimetus"),
			"link" => html::get_new_url(CL_TASK, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 9
			)),
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("K&otilde;ne"),
			"link" => html::get_new_url(CL_CRM_CALL, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 9
			)),
		));

		$url = $this->mk_my_orb("do_search", array(
				"pn" => "predicates",
				"clid" => array(
					CL_TASK,
					CL_CRM_CALL
				),"multiple"=>1,
				),"popup_search");

		$tb->add_button(array(
			"name" => "search",
			"tooltip" => t("Otsi"),
			"img" => "search.gif",
			"url" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_rels"
		));
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
				case "aw_offer":
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
