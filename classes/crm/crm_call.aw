<?php
// crm_call.aw - phone call
/*

@classinfo no_status=1 confirm_save_data=1 prop_cb=1

@tableinfo planner index=id master_table=objects master_index=brother_of

@groupinfo comments caption="Kommentaarid"
@groupinfo participants caption="Osalejad" submit=no
@groupinfo other_calls caption="Eelmised k&otilde;ned"
@groupinfo predicates caption="Eeldused"
@groupinfo customer caption="Klient" submit=no
@groupinfo other_settings caption="Muud seaded"

@default table=planner

	@property hr_schedule_job type=hidden datatype=int
	@property customer_relation type=hidden datatype=int
	@property result_task type=hidden datatype=int
	@property offer type=hidden datatype=int field=aw_offer

@default group=predicates
	@property predicates type=relpicker multiple=1 reltype=RELTYPE_PREDICATE store=connect table=objects field=meta method=serialize
	@caption Eeldustegevused

	@property is_goal type=checkbox ch_value=1 table=planner field=aw_is_goal
	@caption Verstapost

@default group=general
	@property call_tools type=toolbar no_caption=1
	@caption K&otilde;ne toimingud

@layout top_bit type=vbox closeable=1 area_caption=P&otilde;hiandmed
	@layout top_2way type=hbox parent=top_bit
		@layout top_2way_left type=vbox parent=top_2way
			@property name type=textbox table=objects field=name parent=top_2way_left
			@caption Nimi

			@property customer_name_edit type=textbox store=no parent=top_2way_left
			@caption Kliendi nimi

			@property comment type=textbox table=objects field=comment parent=top_2way_left
			@caption Kommentaar

			@property phone type=objpicker clid=CL_CRM_PHONE parent=top_2way_left
			@comment Number millele helistati v&otilde;i helistada
			@caption Number

			@property result type=select parent=top_2way_left
			@caption K&otilde;ne tulemus

			@property result_task_view type=text parent=top_2way_left store=no
			@caption Tulemustegevus

			@property new_call_date type=datepicker table=objects field=meta method=serialize parent=top_2way_left
			@caption Uue k&otilde;ne aeg

			@property preferred_language type=objpicker clid=CL_CRM_LANGUAGE table=objects field=meta method=serialize parent=top_2way_left
			@caption Soovitav suhtluskeel

			@property add_clauses type=chooser store=no parent=top_2way_left multiple=1
			@caption Lisatingimused

		@layout top_2way_right type=vbox parent=top_2way
			@property start1 type=datepicker field=start table=planner parent=top_2way_right
			@caption Plaanitud aeg

			@property end type=datepicker table=planner parent=top_2way_right
			@caption L&otilde;peb

			@property deadline type=datepicker table=planner field=deadline parent=top_2way_right
			@caption T&auml;htaeg

			@property real_start type=text table=planner parent=top_2way_right editonly=1
			@caption Tegelik algus

			@property real_duration type=text datatype=int table=planner parent=top_2way_right editonly=1
			@caption Tegelik kestus (h)

			@property real_maker type=objpicker datatype=int table=planner parent=top_2way_right disabled=1 clid=CL_CRM_PERSON
			@caption K&otilde;ne tegija

			@property participant_select type=relpicker delete_rels_popup_button=1 no_edit=1 multiple=1 size=5 store=connect reltype=RELTYPE_PARTICIPANT parent=top_2way_right
			@caption Osalejad

@layout center_bit type=hbox
	@property center_bit_vis type=hidden store=no no_caption=1 parent=center_bit

	@layout center_bit_left type=vbox parent=center_bit
		@layout center_bit_left_ct  type=hbox closeable=1 area_caption="Sisu" parent=center_bit_left

	@layout center_bit_right type=vbox parent=center_bit
		@layout center_bit_right_top type=vbox parent=center_bit_right closeable=1 area_caption=Osapooled no_padding=1
		@layout center_bit_right_bottom type=vbox parent=center_bit_right closeable=1 area_caption=Manused no_padding=1

@layout content_bit type=vbox closeable=1 area_caption="Sisu"
	@property content type=textarea cols=100 rows=10 resize_height=-1 field=description parent=content_bit no_caption=1 width=100%

@layout impl_bit type=vbox closeable=1 area_caption="Osalejad"
	@property impl_tb type=toolbar no_caption=1 store=no parent=impl_bit
	@property parts_table type=table no_caption=1 store=no parent=impl_bit


@layout files_bit type=vbox closeable=1 area_caption=Manused
	@property files_tb type=toolbar no_caption=1 store=no parent=files_bit
	@property files_table type=table no_caption=1 store=no parent=files_bit

@layout reults_bit type=vbox closeable=1 area_caption=Tulemused
	@property task_results_toolbar type=toolbar no_caption=1 store=no parent=reults_bit
	@property task_results_table type=table no_caption=1 store=no parent=reults_bit

	@property project type=relpicker table=planner field=project reltype=RELTYPE_PROJECT parent=center_bit_right_top
	@caption Projekt

@property is_done type=checkbox table=objects field=flags method=bitmask ch_value=8 // OBJ_IS_DONE
@caption Tehtud

@property is_personal type=checkbox ch_value=1 field=meta method=serialize table=objects
@caption Isiklik

@property promoter type=checkbox ch_value=1 table=planner field=promoter
@caption Korraldaja

@property is_work type=checkbox ch_value=1 table=planner field=aw_is_work
@caption T&ouml;&ouml;aeg

@property bill_no type=text table=planner field=bill_no
@caption Arve number

@property hr_price type=textbox size=5 table=objects field=meta method=serialize
@caption Tunni hind

@property in_budget type=checkbox ch_value=1 table=planner field=aw_in_budget
@caption Eelarvesse

@property time_guess type=textbox size=5 field=meta method=serialize table=objects
@caption Prognoositav tundide arv

@property time_real type=textbox size=5 field=meta method=serialize table=objects
@caption Tegelik tundide arv

@property time_to_cust type=textbox size=5 field=meta method=serialize table=objects
@caption Tundide arv kliendile

@property priority type=textbox size=5 table=planner field=priority
@caption Prioriteet

@property hr_price_currency type=select field=meta method=serialize table=objects
@caption Valuuta

@property deal_unit type=textbox size=5 table=planner
@caption &Uuml;hik

@property deal_amount type=textbox size=5 table=planner
@caption Kogus

@property deal_price type=textbox size=5 table=planner
@caption Kokkuleppehind

@property deal_has_tax type=checkbox size=5 table=planner
@caption Sisestati koos k&auml;ibemaksuga

@property send_bill type=checkbox ch_value=1 table=planner field=send_bill group=other_settings
@caption Saata arve


@default table=objects
@default field=meta
@default method=serialize

@property task_toolbar type=toolbar no_caption=1 store=no group=participants
@caption Toolbar

@property comment_list type=comments group=comments no_caption=1
@caption Kommentaarid

@default group=other_calls
	@property other_calls type=table store=no no_caption=1

@default group=customer
	@property customer_info type=text store=no no_caption=1


// ------------------- RELATION TYPES -------------------
@reltype PROJECT value=4 clid=CL_PROJECT
@caption Projekt

@reltype PREDICATE value=9 clid=CL_TASK,CL_CRM_CALL,CL_CRM_MEETING
@caption Eeldustegevus

@reltype FILE value=2 clid=CL_FILE
@caption Fail

@reltype ROW value=7 clid=CL_TASK_ROW
@caption Rida

@reltype TMP1 value=8 clid=CL_CRM_COMPANY_CUSTOMER_DATA
@caption tmp1

@reltype PARTICIPANT value=12 clid=CL_CRM_PERSON
@caption Osaleja

*/

class crm_call extends task
{
	function crm_call()
	{
		$this->init(array(
			"tpldir" => "crm/call",
			"clid" => CL_CRM_CALL
		));
	}

	function request_execute($obj)
	{
		$this->read_template("show.tpl");
		$this->vars(array(
			"name" => $obj->name(),
			"icon" => icons::get_icon_url($obj),
			"time" => date("d-M-y H:i", $obj->prop("start1")),
			"content" => nl2br(create_links($obj->prop("content")))
		));
		return $this->parse();
	}

	function parse_alias($arr = array())
	{
		// shows a phone call
		$obj = new object($arr["id"]);
		$done = $obj->prop("is_done");
		$done .= $obj->prop("name");
		return $done;
	}

	function callback_on_load($arr)
	{
		$application = automatweb::$request->get_application();
		if (!empty($arr["request"]["preparing_to_call"]) and "change" === $arr["request"]["action"] and $application->is_a(CL_CRM_SALES))
		{
			$this_o = new object($arr["request"]["id"], array(), CL_CRM_CALL);
			$this_o->lock(aw_locker::LOCK_FULL, aw_locker::SCOPE_SESSION, aw_locker::WAIT_EXCEPTION, time() + 300);//!!! 300 normaalseks
		}
	}

	function _get_customer_name_edit(&$arr)
	{
		$r = PROP_OK;
		$this_o = $arr["obj_inst"];
		$cro = obj($this_o->prop("customer_relation"), array(), CL_CRM_COMPANY_CUSTOMER_DATA);
		$customer = is_object($cro) ? obj($cro->prop("buyer")) : null;

		if (!is_object($customer) or !$customer->is_saved())
		{
			$r = PROP_IGNORE;
		}
		else
		{
			$arr["prop"]["value"] = $customer->name();
		}

		return $r;
	}

	function _set_customer_name_edit(&$arr)
	{
		$r = PROP_IGNORE;
		$this_o = $arr["obj_inst"];
		$cro = obj($this_o->prop("customer_relation"), array(), CL_CRM_COMPANY_CUSTOMER_DATA);
		$customer = obj($cro->prop("buyer"));

		if (!is_object($customer) or !$customer->is_saved())
		{
			// $arr["prop"]["error"] = t("Klienti ei leitud");
			// $r = PROP_ERROR;
		}
		else
		{
			if ($customer->is_a(CL_CRM_PERSON))
			{
				$name = explode(" ", $arr["prop"]["value"], 2);
				$firstname = isset($name[0]) ? $name[0] : "";
				$lastname = isset($name[1]) ? $name[1] : "";
				$customer->set_prop("firstname", $firstname);
				$customer->set_prop("lastname", $lastname);
			}
			else
			{
				$name = $arr["prop"]["value"];
				$customer->set_name($name);
			}
			$customer->save();
		}

		return $r;
	}

	function _get_result_task_view(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$result_task = $this_o->prop("result_task");
		$r = PROP_IGNORE;

		if ($result_task)
		{
			try
			{
				$result_task = new object($result_task);
				$caption = $result_task->prop_xml("name");
				$caption = empty($caption) ? t("[Nimetu]") : $caption;
				$arr["prop"]["value"] = html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $result_task->id(),
						"return_url" => get_ru()
					), $result_task->class_id()),
					"caption" => $caption,
					"title" => t("Muuda")
				));

				$r = PROP_OK;
			}
			catch (awex_obj $e)
			{
			}
		}

		return $r;
	}

	function _get_customer_info(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$cro = new crm_company_customer_data();
		$cro->form_only = true;
		$cro->no_form = true;
		$arr["prop"]["value"] = $cro->view(array(
			"id" => $this_o->prop("customer_relation"),
			"class" => "crm_company_customer_data",
			"group" => "sales_data"
		));
		return PROP_OK;
	}

	function _get_call_tools(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$this_o = $arr["obj_inst"];

		$tb->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "submit",
			"img" => "save.gif"
		));

		if ($this_o->prop("real_duration") < 1)
		{
			if ($this_o->can_start())
			{ // call hasn't started yet
				$tb->add_button(array(
					"name" => "start",
					"tooltip" => t("Alusta k&otilde;net"),
					"action" => "start",
					"img" => "start.gif"
				));
			}
			elseif($this_o->prop("real_start") > 2)
			{ // end call button
				$tb->add_button(array(
					"name" => "end",
					"tooltip" => t("L&otilde;peta k&otilde;ne ja salvesta andmed"),
					"action" => "end",
					"img" => "stop.gif"
				));
			}
		}
		return PROP_OK;
	}

	function _get_start1(&$arr)
	{
		// $arr["prop"]["onblur"] = date("d.m.Y H:i", $arr["prop"]["value"]);

		// preset start time either from request 'day' param. or default
		if (!empty($arr["new"]))
		{
			if (!empty($arr["request"]["pt"]))
			{
				$arr["prop"]["value"] = (int) $arr["request"]["pt"];
			}
			else
			{
				$arr["prop"]["value"] = time();
			}
		}
		return PROP_OK;
	}

	function _get_real_start(&$arr)
	{
		if ($arr["obj_inst"]->has_started())
		{
			if (isset($arr["prop"]["value"]) and $arr["prop"]["value"] > 1)
			{
				$arr["prop"]["value"] = date("d.m.Y H:i", $arr["prop"]["value"]);
			}
			$r = PROP_OK;
		}
		else
		{ // don't show when call hasn't been made
			$r = PROP_IGNORE;
		}
		return $r;
	}

	function _get_real_duration(&$arr)
	{
		if ($arr["obj_inst"]->has_ended())
		{
			$value = isset($arr["prop"]["value"]) ? $arr["prop"]["value"] : 0;
			$arr["prop"]["value"] = aw_locale::get_lc_time($value, aw_locale::TIME_SHORT_WORDS);
			$r = PROP_OK;
		}
		else
		{ // don't show when call hasn't been made
			$r = PROP_IGNORE;
		}
		return $r;
	}

	function _get_result(&$arr)
	{
		$r = PROP_IGNORE;
		if ($arr["obj_inst"]->prop("real_start") > 1)
		{
			$arr["prop"]["options"] = array("" => "") + $arr["obj_inst"]->result_names();
			$arr["prop"]["onchange"] = "crmCallProcessResult(this);";
			$r = PROP_OK;
		}
		return $r;
	}

	function _get_phone(&$arr)
	{
		$r = PROP_OK;
		if (empty($arr["value"]) and isset($arr["request"]["phone_id"]) and is_oid($arr["request"]["phone_id"]))
		{
			$phone = obj($arr["request"]["phone_id"], array(), CL_CRM_PHONE);
			$arr["prop"]["value"] = $phone->id();
		}

		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$arr["prop"]["disabled"] = true;
		}
		return $r;
	}

	function _get_new_call_date(&$arr)
	{
		$r = PROP_IGNORE;
		if ($arr["obj_inst"]->prop("real_start") > 1)
		{
			$r = PROP_OK;
		}
		return $r;
	}

	function _get_preferred_language(&$arr)
	{
		$r = PROP_IGNORE;
		if ($arr["obj_inst"]->prop("real_start") > 1)
		{
			$r = PROP_OK;
		}
		return $r;
	}

	function _set_preferred_language(&$arr)
	{
		$r = PROP_IGNORE;
		if ($arr["obj_inst"]->prop("real_start") > 1)
		{
			$r = PROP_OK;
			$application = automatweb::$request->get_application();

			if ($application->is_a(CL_CRM_SALES))
			{
				if (isset($arr["request"]["result"]) and crm_call_obj::RESULT_LANG == $arr["request"]["result"] and !is_oid($arr["prop"]["value"]))
				{
					$arr["prop"]["error"] = t("Soovitav suhtluskeel peab olema m&auml;&auml;ratud");
					$r = PROP_FATAL_ERROR;
				}
			}
		}
		return $r;
	}

	function get_property(&$arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		$i = new task();

		switch($data['name'])
		{
			case "comment":
				if ($data["type"] !== "textarea")
				{
					$data["type"] = "textarea";
					$data["rows"] = 2;
					$data["cols"] = 30;
				}
				break;

			case "real_maker":
				if (!$arr["obj_inst"]->has_started())
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "is_done":
			case "status":
			case "is_personal":
			case "time_guess":
			case "time_real":
			case "time_to_cust":
			case "bill_no":
			case "hr_price":
			case "is_work":
			case "priority":
			case "hr_price_currency":
			case "in_budget":
			case "deal_unit":
			case "deal_amount":
			case "deal_price":
			case "deal_has_tax":
			case "promoter":
			case "project":
			case "customer":
				$retval = PROP_IGNORE;
				break;

			case "name":
				if(!empty($arr["request"]["title"]) && !empty($arr["new"]))
				{
					$data["value"] = $arr["request"]["title"];
				}
				break;

			case "content":
				$data["style"] = "width: 100%";
				if(count($this->mail_data))
				{
					$data["value"] = sprintf(
					"From: %s\nTo: %s\nSubject: %s\nDate: %s\n\n%s",
						$this->mail_data["from"],
						$this->mail_data["to"],
						$this->mail_data["subject"],
						$this->mail_data["date"],
						$this->mail_data["content"]);
				}
				break;

			case "end":
				if (!empty($arr["new"]))
				{
					if (!empty($arr["request"]["pt"]))
					{
						$data["value"] = (int) $arr["request"]["pt"];
					}
					else
					{
						$data["value"] = date_calc::get_day_start();
					}

					$data["value"] += crm_call_obj::DEFAULT_DURATION;
				}
				break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "add_clauses":
			case "is_done":
			case "status":
			case "is_personal":
			case "is_work":
			case "promoter":
			case "customer":
			case "project":
				return PROP_IGNORE;

			case "new_call_date":
				$v = datepicker::get_timestamp($data["value"]);
				$application = automatweb::$request->get_application();

				if ($application->is_a(CL_CRM_SALES))
				{
					if (isset($arr["request"]["result"]) and crm_call_obj::RESULT_CALL == $arr["request"]["result"] and $v <= time())
					{
						if ($v < 2)
						{
							$arr["prop"]["error"] = t("Uue k&otilde;ne aeg peab olema m&auml;&auml;ratud");
						}
						else
						{
							$arr["prop"]["error"] = t("Uue k&otilde;ne aeg ei saa olla minevikus");
						}
						return PROP_FATAL_ERROR;
					}
				}
				else
				{
					if ($v > time())
					{
						// create a new call from the current one
						$o = new object();
						$o->set_class_id(CL_CRM_CALL);
						$o->set_parent($arr["obj_inst"]->parent());
						foreach($arr["obj_inst"]->properties() as $pn => $pv)
						{
							if($o->is_property($pn))
							{
								$o->set_prop($pn , $pv);
							}
						}
						$o->save();
						foreach($arr["obj_inst"]->connections_from(array()) as $c)
						{
							$o->connect(array(
								'type' => $c->prop("reltype"),
								'to' => $c->prop("to"),
							));
						}
						foreach($arr["obj_inst"]->connections_to(array()) as $c)
						{
							$from = obj($c->prop("from"));
							$from->connect(array(
								'type' => $c->prop("reltype"),
								'to' => $o->id(),
							));
						}
					}
					elseif ($v > 300)
					{
						$data["error"] = t("Uue k&otilde;ne aeg ei tohi olla minevikus!");
						return PROP_FATAL_ERROR;
					}
				}
				break;

			case "end":
				if(isset($arr["request"]["start1"]) and date_edit::get_timestamp($arr["request"]["start1"]) > date_edit::get_timestamp($data["value"]))
				{
					$data["value"] = $arr["request"]["start1"];
					$arr["request"]["end"] = $arr["request"]["start1"];
				}
				break;
		}

		return $retval;
	}

	function _set_phone(&$arr)
	{
		$r = PROP_OK;
		if (empty($arr["prop"]["value"]) and isset($arr["request"]["phone_id"]) and is_oid($arr["request"]["phone_id"]))
		{
			$arr["prop"]["value"] = $arr["request"]["phone_id"];
		}
		return $r;
	}

	function _set_result(&$arr)
	{
		$this_o = $arr["obj_inst"];
		if (isset($arr["request"]["action"]) and "end" === $arr["request"]["action"] and empty($arr["prop"]["value"]) or $this_o->prop("result") and empty($arr["prop"]["value"])) // result must be defined when ending call. result can't be unset when call was ended and result defined
		{
			$arr["prop"]["error"] = t("Tulemus peab olema m&auml;&auml;ratud");
			return PROP_FATAL_ERROR;
		}
		return PROP_OK;
	}

	function _get_comment(&$arr)
	{
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$arr["prop"]["value"] = "";
			$arr["prop"]["type"] = "textarea";
			$arr["prop"]["rows"] = "5";
			$arr["prop"]["cols"] = "40";
			$arr["prop"]["caption"] = t("Lisa kommentaar");
			$arr["prop"]["comment"] = t("Kommentaar lisatakse kommentaaridele kliendiinfos.");
		}
		return PROP_OK;
	}

	function _set_comment(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$val = $arr["prop"]["value"];
		if (strlen($val) > 1 and $val !== $this_o->comment() and $this_o->prop("customer_relation"))
		{
			$comm = new forum_comment();
			$commdata = $this_o->name() . ":\n" . $val;
			if (strlen($commdata["comment"]))
			{
				$comm->submit(array(
					"parent" => $this_o->prop("customer_relation"),
					"commtext" => $commdata,
					"return" => "id"
				));
			}
		}
		return PROP_OK;
	}

	function new_change($arr)
	{
		aw_session_set('org_action',aw_global_get('REQUEST_URI'));
		return parent::new_change($arr);
	}

	function callback_mod_reforb(&$arr, $request)
	{
		if ($_GET["action"] === "new")
		{
			if (isset($request["alias_to_org"])) $arr["alias_to_org"] = $request["alias_to_org"];
			if (isset($request["reltype_org"])) $arr["reltype_org"] = $request["reltype_org"];
			if (isset($request["set_pred"])) $arr["set_pred"] = $request["set_pred"];
			if (isset($request["set_resource"])) $arr["set_resource"] = $request["set_resource"];
		}

		if (is_oid(automatweb::$request->arg("phone_id")))
		{
			$arr["phone_id"] = automatweb::$request->arg("phone_id");
		}
	}

	function callback_generate_scripts($arr)
	{
		$this_o = $arr["obj_inst"];
		$task = get_instance(CL_TASK);
		$scripts = $task->callback_generate_scripts($arr);
		$result_call = crm_call_obj::RESULT_CALL;
		$result_presentation = crm_call_obj::RESULT_PRESENTATION;
		$result_refused = crm_call_obj::RESULT_REFUSED;
		$result_noanswer = crm_call_obj::RESULT_NOANSWER;
		$result_busy = crm_call_obj::RESULT_BUSY;
		$result_hungup = crm_call_obj::RESULT_HUNGUP;
		$result_outofservice = crm_call_obj::RESULT_OUTOFSERVICE;
		$result_invalidnr = crm_call_obj::RESULT_INVALIDNR;
		$result_voicemail = crm_call_obj::RESULT_VOICEMAIL;
		$result_newnumber = crm_call_obj::RESULT_NEWNUMBER;
		$result_disconnected = crm_call_obj::RESULT_DISCONNECTED;
		$redirect_to_presentation = ($this_o->is_in_progress() or $this_o->prop("real_duration")) ? "true" : "false";

		$scripts .= <<<EOS
// hide and show elements according to call result
crmCallProcessResult(document.getElementById("result"), true);

function crmCallProcessResult(resultElem, init)
{
	if (resultElem)
	{
		if (resultElem.value == {$result_call})
		{ // show new call date dateselect
			$("input[name='new_call_date[date]']").parent().parent().parent().parent().css("display", "");
		}
		else if (resultElem.value == {$result_presentation})
		{
			$("input[name='new_call_date[date]']").parent().parent().parent().parent().css("display", "none");
			// $("a[href='javascript:submit_changeform('end');']").parent().css("display", "none"); // hide end call btn

			if ($("input[name='result_task']").attr("value") == 0)
			{ // hide 'end call' button
				$("a[href='javascript:submit_changeform('end');']").parent().css("display", "none");
			}

			if (!init && {$redirect_to_presentation})
			{
				//redirect to presentation view
				confirm_save = false;
				submit_changeform("submit");
			}
		}
		else
		{
			$("input[name='new_call_date[date]']").parent().parent().parent().parent().css("display", "none");
		}
	}
}
EOS;
		return $scripts;
	}

	function _init_other_class_t($t)
	{
		$t->define_field(array(
			"name" => "when",
			"caption" => t("Millal"),
			"align" => "center",
			"type" => "time",
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "content",
			"caption" => t("Sisu"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "ed",
			"caption" => t("Vaata"),
			"align" => "center"
		));
	}

	function _other_calls($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_other_class_t($t);

		if (!$arr["obj_inst"]->prop("customer"))
		{
			return;
		}

		// get all previous calls to the same customer
		$ol = new object_list(array(
			"class_id" => CL_CRM_CALL,
			"customer" => $arr["obj_inst"]->prop("customer"),
			"brother_of" => new obj_predicate_prop("id")
		));
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"when" => $o->prop("start1"),
				"content" => nl2br($o->prop("content")),
				"ed" => html::obj_change_url($o)
			));
		}
		$t->set_default_sortby("when");
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
				"MSG_MEETING_DELETE_PARTICIPANTS",
				CL_CRM_MEETING,
				$arr
			);
		}

		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			foreach(safe_array($arr["sel"]) as $item)
			{
				$o->disconnect(array(
					"from" => $item,
				));
			}
		}
		return $arr["post_ru"];
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ("planner" === $tbl)
		{
			$i = get_instance(CL_TASK);
			return $i->do_db_upgrade($tbl, $field);
		}
	}

	private function get_new_parent($parent)
	{
		if($this->can("add" , $parent))
		{
			return $parent;
		}
		$company = get_current_company();
		return $company->id();
	}

	/**
		@attrib name=quick_add all_args=1
	**/
	function quick_add($arr)
	{
		$company = get_current_company();
		if($arr["bug_content"] || $arr["name"])
		{
			$o = new object();
			$o->set_class_id(CL_CRM_CALL);
			$o->set_parent($this->get_new_parent($arr["parent"]));
			$o->set_name($arr["name"]);
			foreach($arr as $key => $val)
			{
				switch($key)
				{
					case "start1":
					case "end":
						$o->set_prop($key , date_edit::get_timestamp($val));
						break;
					case "hr_price":
					case "content":
					case "promoter":
						$o->set_prop($key , $val);
						break;
				}
			}

			if($arr["customer"])
			{
				$customers = new object_list(array(
					"class_id" => CL_CRM_COMPANY,
					"name" => $arr["customer"],
					"limit" => 1,
				));
				$customer = reset($customers->arr());
				if(!$customer)
				{
					$customer = obj($company->add_customer($arr["customer"]));
				}
				if(is_object($customer))
				{
					$o->add_customer($customer->id());
				}
			}

			if($arr["customer_person"] && is_object($customer))
			{
				$customer_persons = new object_list(array(
					"class_id" => CL_CRM_PERSON,
					"name" => $arr["customer_person"],
					"limit" => 1,
				));
				$customer_person = reset($customer_persons->ids());
				if(!$customer_person)
				{
					$customer_person = $customer->add_worker_data(array(
						"worker" => $arr["customer_person"],
					));
				}
				$o->add_participant($customer_person);
			}

			if($arr["project"])
			{
				if(is_object($customer))
				{
					$projects = $customer->get_projects_as_customer();
					foreach($projects->names() as $id => $name)
					{
						if($arr["project"] == $name)
						{
							$project = $id;
							break;
						}
					}
					if(!$project)
					{
						$project = $customer->add_project_as_customer($arr["project"]);
					}
				}
				else
				{
					$projects = new object_list(array(
						"class_id" => CL_PROJECT,
						"name" => $arr["project"],
						"limit" => 1,
					));
					$project = reset($projects->ids());
				}

				if($project)
				{
					$o->add_project($project);
				}
			}

			$u = get_instance(CL_USER);
			$p =$u->get_current_person();

			$data["person"] = $p;
			$data["time_real"] = round(((date_edit::get_timestamp($arr["end"]) - date_edit::get_timestamp($arr["start1"])) / 3600) , 2);
			$data["time_to_cust"] = (((int)(($data["time_real"] - 0.001)*4)) + 1) / 4;
			$o->set_participant_data($data);
			$o->save();
			$res = "<script language='javascript'>window.close();</script>";
			die($res);
		}
		$co_inst = get_instance(CL_CRM_COMPANY);
		$htmlc = get_instance("cfg/htmlclient");
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "name",
			"type" => "textbox",
			"caption" => t("L&uuml;hikirjeldus"),
		));

		$htmlc->add_property(array(
			"name" => "start1",
			"type" => "datetime_select",
			"caption" => t("Algus"),
			"value" => time() - 15*60,
		));

		$htmlc->add_property(array(
			"name" => "end",
			"type" => "datetime_select",
			"caption" => t("L&otilde;pp"),
			"value" => time(),
		));

		$htmlc->add_property(array(
			"name" => "hr_price",
			"type" => "textbox",
			"caption" => t("Tunnihind"),
		));

		$htmlc->add_property(array(
			"name" => "promoter",
			"type" => "select",
			"caption" => t("K&otilde;ne suund"),
			"options" => array("1" => t("Tuli sisse") , "0" => t("L&auml;ks v&auml;lja")),
		));

		$htmlc->add_property(array(
			"name" => "content",
			"type" => "textarea",
			"caption" => t("Sisu"),
			"rows" => 10,
			"cols" => 60,
		));

		$htmlc->add_property(array(
			"name" => "klient",
			"type" => "text",
			"caption" => t("Klient"),
			"subtitle" => 1
		));

		$htmlc->add_property(array(
			"name" => "customer",
			"type" => "textbox",
			"caption" => t("Organisatsioon"),
			"autocomplete_class_id" => array(CL_CRM_COMPANY),
		));

		$htmlc->add_property(array(
			"name" => "customer_person",
			"type" => "textbox",
			"caption" => t("Isik"),
			"autocomplete_source" => "/automatweb/orb.aw?class=crm_company&action=worker_options_autocomplete_source",
			"autocomplete_params" => array("customer"),
		));

		$htmlc->add_property(array(
			"name" => "project",
			"type" => "textbox",
			"caption" => t("Projekt"),
			"autocomplete_source" => "/automatweb/orb.aw?class=crm_company&action=proj_autocomplete_source",
			"autocomplete_params" => array("customer","project"),
		));

		$htmlc->add_property(array(
			"name" => "sub",
			"type" => "button",
			"value" => t("Lisa uus K&otilde;ne!"),
			"onclick" => "changeform.submit();",
			"caption" => t("Lisa uus K&otilde;ne!")
		));
		$data = array(
			"orb_class" => $_GET["class"]?$_GET["class"]:$_POST["class"],
			"reforb" => 0,
			"parent" => $_GET["parent"],
		);
		$htmlc->finish_output(array(
			"action" => "quick_add",
			"method" => "POST",
			"data" => $data,
			"submit" => "no"
		));

		$content = $htmlc->get_result();
		return $content;
	}

	/**
      @attrib name=start all_args=1
      @param id required type=int acl=view
      @param phone_id optional type=int acl=view
	**/
	public function start($arr)
	{
		try
		{
			$this_o = obj($arr["id"], array(), crm_call_obj::CLID);

			if (!empty($arr["phone_id"]))
			{
				$phone = obj($arr["phone_id"], array(), CL_CRM_PHONE);
				$this_o->make($phone);
			}
			else
			{
				$this_o->make();
			}
		}
		catch (awex_mrp_resource_unavailable $e)
		{
			$list = new object_list(array(
				"class_id" => array(crm_call_obj::CLID, CL_CRM_PRESENTATION, CL_TASK, CL_CRM_MEETING),//!!! task classes. teha korralikult
				"hr_schedule_job" => $e->processed_jobs
			));

			$unfinished_task_list = array();
			foreach ($list->arr() as $task)
			{
				$task_link = html::href(array(
					"caption" => $task->id,
					"url" => html::get_change_url($task->id),
				));
				$unfinished_task_list[] = "'{$task->name()}' (id: {$task_link})";
			}
			$unfinished_task_list = implode(", ", $unfinished_task_list);

			$this->show_error_text(sprintf(t("Teie kasutajal on pooleli tegevus(ed) %s. Uut k&otilde;net ei saa alustada."), $unfinished_task_list));
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("Viga."));
		}

		$return_url = !empty($arr["post_ru"]) ? aw_url_change_var("phone_id", null, $arr["post_ru"]) : $this->mk_my_orb("change", array("id" => $arr["id"]), "crm_call");
		return $return_url;
	}

	/**
      @attrib name=end all_args=1
      @param id required type=int acl=view
      // @param id required type=int acl=view
	**/
	public function end($arr)
	{
		$this_o = new object($arr["id"]);
		$r = $this->submit($arr);
		if ($this->data_processed_successfully())
		{
			try
			{
				$this_o->end();
			}
			catch (awex_crm_call_state $e)
			{
				if (crm_call_obj::ERR_STATE_ENDED === $e->getCode())
				{
					$this->show_error_text(t("K&otilde;net &uuml;ritati l&otilde;petada kahekordselt."));
				}
				else
				{
					throw $e;
				}
			}

			$this_o->unlock();

			$application = automatweb::$request->get_application();
			if ($application->is_a(CL_CRM_SALES))
			{
				// return to calls list
				$default_return_url = $this->mk_my_orb("change", array(
					"id" => $application->id(),
					"group" => "calls"
				), "crm_sales");

				try
				{
					$return_url = new aw_uri($r);
					$return_url = new aw_uri($return_url->arg("return_url"));
					if ("crm_sales" === $return_url->arg("class"))
					{
						$r = $return_url->get();
					}
					else
					{
						$r = $default_return_url;
					}
				}
				catch (Exception $e)
				{
					$r = $default_return_url;
				}
			}
		}
		return $r;
	}

	function submit($arr = array())
	{
		$r = parent::submit($arr);
		if ("submit" === $arr["action"] and $this->data_processed_successfully())
		{
			$this_o = new object($arr["id"]);
			$application = automatweb::$request->get_application();

			if ($application->is_a(CL_CRM_SALES))
			{
				$result = (int) $this_o->prop("result");
				if (crm_call_obj::RESULT_PRESENTATION === $result and !$this_o->prop("result_task.user_reviewed"))
				{
					try
					{
						// jump to presentation
						$this->show_msg_text(t("Sisestage k&otilde;ne tulemusena loodud uue esitluse andmed"));
						$result_task = obj($this_o->prop("result_task"), array(), CL_CRM_PRESENTATION);
						$r = html::get_change_url($result_task->id(), array(
							"return_url" => $arr["post_ru"],
							crm_presentation::REQVAR_SHOW_SCHEDULING_IFACE => "1"
						));
					}
					catch (Exception $e)
					{
						$this->show_error_text(t("K&otilde;ne tulemuseks olev esitlus pole avatav"));
					}
				}
			}
		}
		return $r;
	}
}
