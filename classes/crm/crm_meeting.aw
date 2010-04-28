<?php

namespace automatweb;
// $Header: /home/cvs/automatweb_dev/classes/crm/crm_meeting.aw,v 1.120 2009/03/10 16:20:34 markop Exp $
// kohtumine.aw - Kohtumine
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_MEETING_DELETE_PARTICIPANTS,CL_CRM_MEETING, submit_delete_participants_from_calendar);

@classinfo syslog_type=ST_CRM_MEETING confirm_save_data=1 maintainer=markop

@default table=objects

@default group=general
@layout top_bit type=vbox closeable=1 area_caption=P&otilde;hiandmed

	@layout top_2way type=hbox parent=top_bit

		@layout top_2way_left type=vbox parent=top_2way

			@property name type=textbox table=objects field=name parent=top_2way_left
			@caption Nimi

			@property comment type=textbox table=objects field=comment parent=top_2way_left
			@caption Kommentaar

			@property add_clauses type=chooser store=no parent=top_2way_left multiple=1
			@caption Lisatingimused

		@layout top_2way_right type=vbox parent=top_2way

			@property start1 type=datetime_select parent=top_2way_right field=start table=planner
			@caption Algus

			@property end type=datetime_select parent=top_2way_right table=planner
			@caption L&otilde;peb

			@property deadline type=datetime_select table=planner field=deadline parent=top_2way_right
			@caption T&auml;htaeg

	@property hrs_table type=table no_caption=1 store=no parent=top_bit

@layout center_bit type=hbox
	@property center_bit_vis type=hidden store=no no_caption=1 parent=center_bit

	@layout center_bit_left type=vbox parent=center_bit

		@layout center_bit_left_ct  type=hbox closeable=1 area_caption=Sisu parent=center_bit_left

	@layout center_bit_right type=vbox parent=center_bit

		@layout center_bit_right_top type=vbox parent=center_bit_right closeable=1 area_caption=Osapooled no_padding=1

		@layout center_bit_right_bottom type=vbox parent=center_bit_right closeable=1 area_caption=Manused no_padding=1


@layout content_bit type=vbox closeable=1 area_caption=Sisu
	@property content type=textarea cols=180 rows=30 table=documents parent=content_bit no_caption=1
	@caption Sisu


@layout customer_bit type=vbox closeable=1 area_caption=Tellijad
#	@property parts_tb type=toolbar no_caption=1 store=no parent=customer_bit

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


@layout center_bit_bottom type=vbox closeable=1 area_caption=Osapooled

#	@property parts_tb type=toolbar no_caption=1 store=no parent=center_bit_bottom
#	@property proj_table type=table no_caption=1 store=no parent=center_bit_bottom
#	@property parts_table type=table no_caption=1 store=no parent=center_bit_bottom

	@property customer type=relpicker table=planner field=customer reltype=RELTYPE_CUSTOMER parent=center_bit_bottom
	@caption Klient

	@property project type=relpicker table=planner field=project reltype=RELTYPE_PROJECT parent=center_bit_bottom
	@caption Projekt

@property is_done type=checkbox table=objects field=flags method=bitmask ch_value=8 // OBJ_IS_DONE
@caption Tehtud

@property promoter type=checkbox ch_value=1 table=planner field=promoter
@caption Korraldaja

@property is_personal type=checkbox ch_value=1 field=meta method=serialize
@caption Isiklik

@property is_work type=checkbox ch_value=1 field=aw_is_work table=planner
@caption T&ouml;&ouml;aeg

@property whole_day type=checkbox ch_value=1 field=meta method=serialize
@caption Kestab terve p&auml;eva

@property udefch1 type=checkbox ch_value=1 user=1 table=kliendibaas_kohtumine
@caption User-defined checkbox 1

@property udeftb1 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 1

@property udeftb2 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 2

@property udeftb3 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 3

@property udeftb4 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 4

@property udeftb5 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 5

@property udeftb6 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 6

@property udeftb7 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 7

@property udeftb8 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 8

@property udeftb9 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 9

@property udeftb10 type=textbox user=1 table=kliendibaas_kohtumine
@caption User-defined textbox 10

@property udefta1 type=textarea user=1 table=kliendibaas_kohtumine
@caption User-defined textarea 1

@property udefta2 type=textarea user=1 table=kliendibaas_kohtumine
@caption User-defined textarea 2

@property udefta3 type=textarea user=1 table=kliendibaas_kohtumine
@caption User-defined textarea 3

@property bill_no type=text table=planner
@caption Arve number

@property participants type=select multiple=1 table=objects field=meta method=serialize
@caption Osalejad

@property time_guess type=textbox size=5 field=meta method=serialize
@caption Prognoositav tundide arv

@property time_real type=textbox size=5 field=meta method=serialize
@caption Tegelik tundide arv

@property time_to_cust type=textbox size=5 field=meta method=serialize
@caption Tundide arv kliendile

@property hr_price type=textbox size=5 field=meta method=serialize
@caption Tunni hind

@property in_budget type=checkbox ch_value=1 table=planner field=aw_in_budget
@caption Eelarvesse


@property priority type=textbox size=5 table=planner field=priority
@caption Prioriteet

@property hr_price_currency type=select field=meta method=serialize
@caption Valuuta

	@property deal_unit type=textbox size=5 table=planner
	@caption &Uuml;hik

	@property deal_amount type=textbox size=5 table=planner
	@caption Kogus

	@property deal_price type=textbox size=5 table=planner
	@caption Kokkuleppehind

	@property deal_has_tax type=checkbox size=5 table=planner
	@caption Sisestati koos k&auml;ibemaksuga

@property summary type=textarea cols=80 rows=30 table=planner field=description
@caption Kokkuv&otilde;te

@property controller_disp type=text store=no
@caption Kontrolleri v&auml;ljund


@property send_bill type=checkbox ch_value=1 table=planner field=send_bill group=other_settings
@caption Saata arve


@default field=meta
@default method=serialize

@property task_toolbar type=toolbar no_caption=1 store=no group=participants
@caption Toolbar

@property recurrence type=releditor reltype=RELTYPE_RECURRENCE group=recurrence rel_id=first props=start,recur_type,end,weekdays,interval_daily,interval_weekly,interval_montly,interval_yearly,interval_hourly,interval_minutely
@caption Kordused

@property calendar_selector type=calendar_selector store=no group=calendars
@caption Kalendrid

@property other_selector type=other_calendar_selector store=no group=others
@caption Teised

@property project_selector type=project_selector store=no group=projects all_projects=1
@caption Projektid

@property comment_list type=comments group=comments no_caption=1
@caption Kommentaarid

@property participant type=participant_selector store=no group=participants no_caption=1
@caption Osalejad

@property search_contact_company type=textbox store=no group=participants
@caption Organisatsioon

@property search_contact_firstname type=textbox store=no group=participants
@caption Eesnimi

@property search_contact_lastname type=textbox store=no group=participants
@caption Perenimi

@property search_contact_code type=textbox store=no group=participants
@caption Isikukood

@property search_contact_button type=submit store=no group=participants action=search_contacts
@caption Otsi

@property search_contact_results type=table store=no group=participants no_caption=1
@caption Tulemuste tabel

@default group=resources

	@property sel_resources type=table no_caption=1

@default group=other_settings


	@property summary type=textarea cols=80 rows=30 table=planner field=description no_caption=1
	@caption Kokkuv&otilde;te

	@property udeflb1 type=classificator reltype=RELTYPE_UDEFLB1 store=connect
	@caption Kasutajdefineeritud muutuja 1

	@property userfile1 type=releditor reltype=RELTYPE_FILE1 rel_id=first use_form=emb field=meta method=serialize
	@caption Failiupload 1

	@property userfile2 type=releditor reltype=RELTYPE_FILE2 rel_id=first use_form=emb field=meta method=serialize
	@caption Failiupload 2

	@property userfile3 type=releditor reltype=RELTYPE_FILE3 rel_id=first use_form=emb field=meta method=serialize
	@caption Failiupload 3



@default group=predicates

	@property predicates type=relpicker multiple=1 reltype=RELTYPE_PREDICATE store=connect table=objects field=meta method=serialize
	@caption Eeldustegevused

	@property is_goal type=checkbox ch_value=1 table=planner field=aw_is_goal
	@caption Verstapost

@default group=transl

	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

	@property aliasmgr type=aliasmgr store=no editonly=1 trans=1
	@caption Aliastehaldur

@groupinfo recurrence caption=Kordumine
@groupinfo calendars caption=Kalendrid
@groupinfo others caption=Teised
@groupinfo projects caption=Projektid
@groupinfo comments caption=Kommentaarid
@groupinfo participants caption=Osalejad submit=no
@groupinfo resources caption="Ressursid"
@groupinfo other_settings caption="Muud seaded"
@groupinfo transl caption=T&otilde;lgi
@groupinfo predicates caption="Eeldused"

@tableinfo documents index=docid master_table=objects master_index=brother_of
@tableinfo planner index=id master_table=objects master_index=brother_of
@tableinfo kliendibaas_kohtumine index=id master_table=objects master_index=brother_of

@reltype RECURRENCE value=1 clid=CL_RECURRENCE
@caption Kordus

@reltype CUSTOMER value=3 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype PROJECT value=4 clid=CL_PROJECT
@caption Projekt

@reltype RESOURCE value=5 clid=CL_MRP_RESOURCE
@caption ressurss

@reltype BILL value=6 clid=CL_CRM_BILL
@caption Arve

@reltype ROW value=7 clid=CL_TASK_ROW
@caption Rida

@reltype UDEFLB1 value=65 clid=CL_META
@caption Kasutajadefineritud muutuja 1

@reltype PREDICATE value=9 clid=CL_TASK,CL_CRM_CALL,CL_CRM_MEETING
@caption Eeldustegevus

@reltype FILE value=2 clid=CL_FILE
@caption Fail

@reltype FILE1 value=86 clid=CL_FILE
@caption fail1

@reltype FILE2 value=87 clid=CL_FILE
@caption fail2

@reltype FILE3 value=88 clid=CL_FILE
@caption fail3


*/

class crm_meeting extends task
{
	const AW_CLID = 224;

	var $return_url;

	function crm_meeting()
	{
		$this->init(array(
			"clid" => CL_CRM_MEETING,
		));

		$this->trans_props = array(
			"name", "comment", "content"
		);
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
	}

	function get_property($arr)
	{
		if (is_object($arr["obj_inst"]) && $arr["obj_inst"]->prop("is_personal") && aw_global_get("uid") != $arr["obj_inst"]->createdby())
		{
			if (!($arr["prop"]["name"] == "start1" || $arr["prop"]["name"] == "end" || $arr["prop"]["name"] == "deadline"))
			{
				return PROP_IGNORE;
			}
		}

		$data = &$arr['prop'];
		$i = get_instance(CL_TASK);
		switch($data['name'])
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

			case "parts_tb":
				$i->_parts_tb($arr);
				break;

			case "co_table":
				$i->_co_table($arr);
				break;

			case "proj_table":
				$i->_proj_table($arr);
				break;

			case "parts_table":
				$i->_parts_table($arr);
				break;

			case "files_tb":
				$i->_files_tb($arr);
				break;

			case "files_table":
				$i->_files_table($arr);
				break;

			case "bills_tb":
				$this->_bills_tb($arr);
				break;

			case "bills_table":
				$this->_bills_table($arr);
				break;

			case "hrs_table":
				$this->_hrs_table($arr);
				break;

			case "add_clauses":
//				if (!is_object($arr["obj_inst"]))
//				{
					return PROP_IGNORE;
//				}

				$has_work_time = $arr["obj_inst"]->has_work_time();
				$data["options"] = array(
					"status" => t("Aktiivne"),
					"is_done" => t("Tehtud"),
					"whole_day" => t("Terve p&auml;ev"),
					"is_personal" => t("Isiklik"),
					"send_bill" => t("Arvele"),
//					"is_work" => t("T&ouml;&ouml;aeg"),
				);
				$data["value"] = array(
					"status" => $arr["obj_inst"]->prop("status") == STAT_ACTIVE ? 1 : 0,
					"is_done" => $arr["obj_inst"]->prop("is_done") ? 8 : 0,
					"whole_day" => $arr["obj_inst"]->prop("whole_day") ? 1 : 0,
					"is_personal" => $arr["obj_inst"]->prop("is_personal") ? 1 : 0,
					"send_bill" => $arr["obj_inst"]->prop("send_bill") ? 1 : 0,
//					"is_work" => $arr["obj_inst"]->prop("is_work") ? 1 : 0,
				);
				if(!$has_work_time)
				{
					$data["options"]["is_work"] = t("T&ouml;&ouml;aeg");
				}

				break;
			case "is_done":
			case "status":
			case "whole_day":
			case "is_personal":
//			case "send_bill":
			case "time_guess":
			case "time_real":
			case "time_to_cust":
			case "hr_price":
			case "bill_no":
			case "is_work":
			case "priority":
			case "hr_price_currency":
			case "in_budget":
			case "deal_unit":
			case "deal_amount":
			case "deal_price":
			case "deal_has_tax":
			case "promoter":
				return PROP_IGNORE;

			case "controller_disp":
				$cs = get_instance(CL_CRM_SETTINGS);
				$pc = $cs->get_meeting_controller($cs->get_current_settings());
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

			case "name":
				if(isset($this->mail_data))
				{
					$data["value"] = $this->mail_data["subject"];
					break;
				}
				if($arr["new"] && $arr["request"]["title"])
				{
					$data["value"] = $arr["request"]["title"];
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
				}
				break;
			case "start1":
			case "end":
				$p = get_instance(CL_PLANNER);
				$cal = $p->get_calendar_for_user();
				if ($cal)
				{
					$calo = obj($cal);
					$data["minute_step"] = $calo->prop("minute_step");
					if ($data["name"] == "end" && (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id())))
					{
						$data["value"] = time() + $calo->prop("event_def_len")*60;
					}
				}
				else
				if ($data["name"] == "end" && $arr["new"])
				{
					$data["value"] = time() + 900;
				}
				if ($arr["new"])
				{
					if($day = $arr["request"]["date"])
					{
						$da = explode("-", $day);
						$data["value"] = mktime(date('h',$data["value"]), date('i', $data["value"]), 0, $da[1], $da[0], $da[2]);
					}
				}
				break;

			case "sel_resources":
				$t = get_instance(CL_TASK);
				$t->_get_sel_resources($arr);
				break;

			case "project":
				return PROP_IGNORE;
				$nms = array();
				if ($this->can("view",$arr["request"]["alias_to_org"]))
				{
					$ol = new object_list(array(
						"class_id" => CL_PROJECT,
						"CL_PROJECT.RELTYPE_PARTICIPANT" => $arr["request"]["alias_to_org"],
						"lang_id" => array(),
						"site_id" => array()
					));
				}
				else
				if (is_object($arr["obj_inst"]) && !$arr["new"] && $this->can("view", $arr["obj_inst"]->prop("customer")))
				{
					$ol = new object_list(array(
						"class_id" => CL_PROJECT,
						"CL_PROJECT.RELTYPE_PARTICIPANT" => $arr["obj_inst"]->prop("customer"),
						"lang_id" => array(),
						"site_id" => array()
					));
					$nms = $ol->names();
				}
				else
				{
					$i = get_instance(CL_CRM_COMPANY);
					$prj = $i->get_my_projects();
					if (!count($prj))
					{
						$ol = new object_list();
					}
					else
					{
						$ol = new object_list(array("oid" => $prj));
					}
					$nms = $ol->names();
				}

				$data["options"] = array("" => "") + $nms;

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
				$i = get_instance(CL_CRM_COMPANY);
				$cst = $i->get_my_customers();
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
						$u = new user();
						$data["value"] = $u->get_company_for_person($ao->id());
					}
					else
					{
						$data["value"] = $arr["request"]["alias_to_org"];
					}
				}

				if (!isset($data["options"][$data["value"]]) && $this->can("view", $data["value"]))
				{
					$tmp = obj($data["value"]);
					$data["options"][$tmp->id()] = $tmp->name();
				}

				asort($data["options"]);
				if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
				{
					$arr["obj_inst"]->set_prop("customer", $data["value"]);
				}
				$data["onchange"] = "upd_proj_list()";
				break;

			case "participants":

				if($arr["new"] && $arr["request"]["participants"])
				{
					$_SESSION["event"]["participants"] = explode("," , $arr["request"]["participants"]);
				}
				//if(aw_global_get("uid") == "marko")arr($arr);
				return PROP_IGNORE;
				$opts = array();
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
						'type' => array( 8),//CRM_PERSON.RELTYPE_PERSON_MEETING==10
					));
					foreach($conns as $conn)
					{
						$obj = $conn->from();
						$opts[$obj->id()] = $obj->name();
						$p[$obj->id()] = $obj->id();
					}
				}
				// also add all workers for my company
				$u = new user();
				$co = $u->get_current_company();
				$w = array();
				$i = get_instance(CL_CRM_COMPANY);
				$i->get_all_workers_for_company(obj($co), &$w);
				foreach($w as $oid)
				{
					$o = obj($oid);
					$opts[$oid] = $o->name();
				}

				$i = get_instance(CL_CRM_COMPANY);
				uasort($opts, array(&$i, "__person_name_sorter"));

				$data["options"] = array("" => t("--Vali--")) + $opts;
				$data["value"] = $p;
				break;


			case 'task_toolbar' :
			{
				$tb = &$data['toolbar'];
				$tb->add_button(array(
					'name' => 'del',
					'img' => 'delete.gif',
					'tooltip' => t('Kustuta valitud'),
					'action' => 'submit_delete_participants_from_calendar',
					"confirm" => t("Oled kindel, et tahad valitud osalejad eemaldada?"),
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

			case "hr_price":
				// get first person connected as participant and read their hr price
				if ($data["value"] == "" && is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
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
								break;
							}
						}
					}

				}
				break;
		}
	}


	function parse_alias($arr)
	{
		$target = new object($arr["alias"]["target"]);
		return html::href(array(
			//"url" => aw_ini_get("baseurl") . "/" . $target->id(),
			"url" => $this->mk_my_orb("change",array("id" => $target->id()),$target->class_id(),true,true),
			"caption" => $target->name(),
		));
	}

	function set_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "hrs_table":
				$this->save_add_clauses($arr);
				break;
			case "parts_table":
				$this->save_parts_table($arr);
				break;
			case "co_table":
				$this->_save_co_table($arr);
				break;
			case "end":
				if(date_edit::get_timestamp($arr["request"]["start1"]) > date_edit::get_timestamp($data["value"]))
				{

					$data["value"] = $arr["request"]["start1"];
					$arr["request"]["end"] = $arr["request"]["start1"];
				}
			case "start1":
				if($data["value"]["hour"] == "---")
				{
					$arr["request"][$data["name"]]["hour"] = "0";
				}
				if($data["value"]["minute"] == "---")
				{
					$arr["request"][$data["name"]]["minute"] = "0";
				}
				break;

			case "add_clauses":
				return PROP_IGNORE;
				$this->save_add_clauses($arr);
				break;

			case "is_done":
			case "status":
			case "whole_day":
			case "is_personal":
//			case "send_bill":
			case "is_work":
			case "promoter":
				return PROP_IGNORE;

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "sel_resources":
				$t = get_instance(CL_TASK);
				$t->_set_resources($arr);
				break;

			case "project":
				return PROP_IGNORE;
				if (isset($_POST["project"]))
				{
					$data["value"] = $_POST["project"];
				}
				// add to proj
				if (is_oid($data["value"]) && $this->can("view", $data["value"]))
				{
					$this->add_to_proj = $data["value"];
				}
				break;

			case "customer":
				return PROP_IGNORE;
				if (isset($_POST["customer"]))
				{
					$data["value"] = $_POST["customer"];
				}
				break;

			case "participants":

				return PROP_IGNORE;
				if (!is_oid($arr["obj_inst"]->id()))
				{
					$this->post_save_add_parts = safe_array($data["value"]);
					return PROP_IGNORE;
				}
				$prop["value"] = $_POST["participants"];
				$pl = get_instance(CL_PLANNER);
				foreach(safe_array($data["value"]) as $person)
				{
					$p = obj($person);
					$p->connect(array(
						"to" => $arr["obj_inst"]->id(),
						"reltype" => "RELTYPE_PERSON_MEETING"
					));

					// also add to their calendar
					if (($cal = $pl->get_calendar_for_person($p)))
					{
						$pl->add_event_to_calendar(obj($cal), $arr["obj_inst"]);
					}
				}
				break;

			case "whole_day":
				if ($data["value"])
				{
					$start = $arr["obj_inst"]->prop("start1");

					list($m,$d,$y) = explode("-",date("m-d-Y",$start));
					$daystart = mktime(9,0,0,$m,$d,$y);
					$dayend = mktime(17,0,0,$m,$d,$y);

					$arr["obj_inst"]->set_prop("start1",$daystart);
					$arr["obj_inst"]->set_prop("end",$dayend);
				};
				break;
		};
		return $retval;
	}
/*
	function callback_post_save($arr)
	{

		if ($_POST["participants_h"] > 0)
		{
			$this->post_save_add_parts = explode(",", $_POST["participants_h"]);
		}

		if (is_array($this->post_save_add_parts))
		{
			foreach(safe_array($this->post_save_add_parts) as $person)
			{
				$this->add_participant($arr["obj_inst"], obj($person));
			}

		}
		if(!empty($arr['new']))
		{
			$this->add_participant($arr["obj_inst"], get_current_person());
		}
		if ($this->add_to_proj)
		{
			$arr["obj_inst"]->create_brother($this->add_to_proj);
		}

		if ($this->can("view", $_POST["orderer_h"]))
		{
			$arr["obj_inst"]->connect(array(
				"to" => $_POST["orderer_h"],
				"type" => "RELTYPE_CUSTOMER"
			));
			$arr["obj_inst"]->set_prop("customer" , $_POST["orderer_h"]);
			$arr["obj_inst"]->save();
		}
		if ($_POST["project_h"] > 0)
		{
			foreach(explode(",", $_POST["project_h"]) as $proj)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $proj,
					"type" => "RELTYPE_PROJECT"
				));
				$arr["obj_inst"]->create_brother($proj);
			}
		}
		if ($_POST["files_h"] > 0)
		{
			foreach(explode(",", $_POST["files_h"]) as $proj)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $proj,
					"type" => "RELTYPE_FILE"
				));
			}
		}

		$pl = get_instance(CL_PLANNER);
		$pl->post_submit_event($arr["obj_inst"]);

		if($_SESSION["add_to_task"])
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
	}

*/
	/**
	@attrib name=submit_delete_participants_from_calendar
	@param id required type=int acl=view
	  @param group optional
	  @param check required
	**/
	function submit_delete_participants_from_calendar($arr)
	{
		if(is_array($arr["check"]))
		{
			foreach($arr["check"] as $person_id)
			{
				$obj = new object($person_id);
				if($obj->class_id() == CL_CRM_PERSON)
				{
					$ev = obj($arr["event_id"] ? $arr["event_id"] : $arr["id"]);
					if ($obj->is_connected_to(array("to" => $ev->brother_of())))
					{
						$obj->disconnect(array("from" => $ev->brother_of()));
						// also, remove from that person's calendar

						$person_i = $obj->instance();
						if ($_user = $person_i->has_user($obj))
						{
							$cals = $_user->connections_to(array(
								"from.class_id" => CL_PLANNER,
								"type" => "RELTYPE_CALENDAR_OWNERSHIP"
							));
							foreach($cals as $cal_con)
							{
								$cal = $cal_con->from();
								$event_folder = $cal->prop("event_folder");
								if (is_oid($event_folder) && $this->can("add", $event_folder))
								{
									// get brother
									$bl = new object_list(array(
										"brother_of" => $arr["event_id"],
										"site_id" => array(),
										"lang_id" => array(),
										"parent" => $event_folder
									));
									if ($bl->count())
									{
										$bro = $bl->begin();
										if ($bro->id() != $bro->brother_of())
										{
											$bro->delete();
										}
										else
										{
											// now, if we hit the original, then we have a problem
											// we still need to delete it, but we must turn it into a brother of the next one in line
											// if there is one. if not, then there's really nothing we can do.
											// so, list all brothers for this object
											$bl = new object_list(array(
												"brother_of" => $arr["event_id"],
												"site_id" => array(),
												"lang_id" => array(),
												"oid" => new obj_predicate_compare(OBJ_COMP_GREATER, $arr["event_id"])
											));
											if ($bl->count())
											{
												$nreal = $bl->begin();
												$nreal->originalize();
												$bro->delete();
											}
										}
									}
								}
							}
						}
					}
				}
				else
				{
					$obj->delete();
				}
			}
		}
		return html::get_change_url($arr["id"], array("group" => $arr["group"]));
	}

	/**
		@attrib name=search_contacts
	**/
	function search_contacts($arr)
	{
		return $this->mk_my_orb('change',array(
				'id' => $arr['id'],
				'group' => $arr['group'],
				'search_contact_firstname' => ($arr['search_contact_firstname']),
				'search_contact_lastname' => ($arr['search_contact_lastname']),
				'search_contact_code' => ($arr['search_contact_code']),
				'search_contact_company' => ($arr['search_contact_company']),
				"return_url" => $arr["return_url"]
			),
			$arr['class']
		);
	}

	function request_execute($o)
	{
		return $this->show2(array("id" => $o->id()));
	}

	function show2($arr)
	{
		$has_tpl = $this->read_template($_GET["date"] != "" ? "display_event.tpl" : "display_event_in_list.tpl", 1);
		$ob = new object($arr["id"]);
		$cform = $ob->meta("cfgform_id");
		// feega hea .. n&uuml;&uuml;d on vaja veel nimed saad
		$cform_obj = new object($cform);
		$output_form = $cform_obj->prop("use_output");
		if (is_oid($output_form))
		{
			$t = new cfgform();
			$props = $t->get_props_from_cfgform(array("id" => $output_form));
		}
		else
		{
			$props = $this->load_defaults();
		}

		$htmlc = get_instance("cfg/htmlclient",array("template" => "webform.tpl"));
		$htmlc->start_output();

		$this->vars(array(
			"oid" => $ob->id(),
			"date" => date("d.m.Y", $ob->prop("start1"))
		));

		foreach($props as $propname => $propdata)
		{
		  	$value = $ob->prop($propname);
			if ($propdata["type"] == "datetime_select")
			{
				if ($value == -1)
				{
					continue;
				};
				$value = date("d-m-Y H:i", $value);
			};

			if ($has_tpl)
			{
				$this->vars(array(
					$propname => nl2br($value),
					$propname."_caption" => $propdata["caption"]
				));
				if ($_GET["EVT_DBG"])
				{
					echo "property $propname => ".nl2br($value)." <br>";
				}
			}
			if (!empty($value))
			{
			   $htmlc->add_property(array(
			      "name" => $propname,
			      "caption" => $propdata["caption"],
			      "value" => nl2br($value),
			      "type" => "text",
			   ));
			   $this->vars(array(
			   	"HAS_".$propname => $this->parse("HAS_".$propname)
			   ));
			}
			else
			{
				$this->vars(array(
					"HAS_".$propname => ""
				));
			}
		};
		$htmlc->finish_output(array("submit" => "no"));

		$html = $htmlc->get_result(array(
			"form_only" => 1
		));

		if ($has_tpl)
		{
			return $this->parse();
		}
		return $html;
	}

	function new_change($arr)
	{
		aw_session_set('org_action',aw_global_get('REQUEST_URI'));
		return parent::new_change($arr);
	}

	/**

		@attrib name=save_participant_search_results

	**/
	function save_participant_search_results($arr)
	{
		$p = get_instance(CL_PLANNER);
		return $p->save_participant_search_results($arr);
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function add_participant($task, $person)
	{
		$pl = get_instance(CL_PLANNER);
		$person->connect(array(
			"to" => $task->id(),
			"reltype" => "RELTYPE_PERSON_MEETING"
		));
		// also add to their calendar
		if (($cal = $pl->get_calendar_for_person($person)))
		{
			$pl->add_event_to_calendar(obj($cal), $task);
		}
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["participants_h"] = 0;
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
/*
	function callback_pre_save($arr)
	{
		$len = $arr["obj_inst"]->prop("end") - $arr["obj_inst"]->prop("start1");
		$hrs = floor($len / 900) / 4;

		// write length to time fields if empty
		if ($arr["obj_inst"]->prop("time_to_cust") == "")
		{
			$arr["obj_inst"]->set_prop("time_to_cust", $hrs);
		}
		if ($arr["request"]["set_resource"] != "")
		{
			$arr["obj_inst"]->connect(array(
				"to" => $arr["request"]["set_resource"],
				"type" => "RELTYPE_RESOURCE"
			));
		}

		if ($arr["obj_inst"]->prop("time_real") == "")
		{
			$arr["obj_inst"]->set_prop("time_real", $hrs);
		}

		if ($arr["request"]["set_pred"] != "")
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
	}
*/
	function callback_generate_scripts($arr)
	{
		$task = get_instance(CL_TASK);
		return $task->callback_generate_scripts($arr);
	}
/*
	function _hrs_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Tundide arv"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "time_guess",
			"caption" => t("Prognoositav"),
			"align" => "center",
			"parent" => "time"
		));
		$t->define_field(array(
			"name" => "time_real",
			"caption" => t("Tegelik"),
			"align" => "center",
			"parent" => "time"
		));
		$t->define_field(array(
			"name" => "time_to_cust",
			"caption" => t("Kliendile"),
			"align" => "center",
			"parent" => "time"
		));
		$t->define_field(array(
			"name" => "hr_price",
			"caption" => t("Tunnihind"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "bill_no",
			"caption" => t("Arve number"),
			"align" => "center"
		));
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
			$t->define_field(array(
				"name" => "whole_day",
				"caption" => t("Terve p&auml;ev"),
				"align" => "center",
				"parent" => "add_clauses",
			));
			$t->define_field(array(
				"name" => "is_personal",
				"caption" => t("Isiklik"),
				"align" => "center",
				"parent" => "add_clauses",
			));
			$t->define_field(array(
				"name" => "send_bill",
				"caption" => t("Arvele"),
				"align" => "center",
				"parent" => "add_clauses",
			));

			$has_work_time = $arr["obj_inst"]->has_work_time();
			if(!$has_work_time)
			{
				$t->define_field(array(
					"name" => "is_work",
					"caption" => t("Arvele"),
					"align" => "center",
					"parent" => "add_clausels",
				));
			}

		// small conversion - if set, create a relation instead and clear, so that we can have multiple
		if (is_object($arr["obj_inst"]) && $this->can("view", $arr["obj_inst"]->prop("bill_no") ))
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

		$t->define_data(array(
			"time_guess" => html::textbox(array(
				"name" => "time_guess",
				"value" => is_object($arr["obj_inst"]) ? $arr["obj_inst"]->prop("time_guess") : 0,
				"size" => 5
			)),
			"time_real" => html::textbox(array(
				"name" => "time_real",
				"value" => is_object($arr["obj_inst"]) ? $arr["obj_inst"]->prop("time_real") : 0,
				"size" => 5
			)),
			"time_to_cust" => html::textbox(array(
				"name" => "time_to_cust",
				"value" => is_object($arr["obj_inst"]) ? $arr["obj_inst"]->prop("time_to_cust") : 0,
				"size" => 5
			)),
			"hr_price" => html::textbox(array(
				"name" => "hr_price",
				"value" => is_object($arr["obj_inst"]) ? $arr["obj_inst"]->prop("hr_price") : 0,
				"size" => 5
			)),
			"bill_no" => $bno,

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
			"send_bill" => html::checkbox(array(
				"name" => "add_clauses[send_bill]",
				"value" => 1,
				"checked" => is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("send_bill") ? 1 : 0,
			)),
			"is_work" => html::checkbox(array(
				"name" => "add_clauses[is_work]",
				"value" => 1,
			)),
		));
	}
*/
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


// stopper crap

	function handle_stopper_stop($arr)
	{
		if(!$this->can("view", $arr["oid"]))
		{
			if(!strlen($arr["data"]["name"]["value"]) || !strlen($arr["data"]["part"]["value"]) || !strlen($arr["data"]["project"]["value"]))
			{
				return t("Nimi, osaleja ja projekt peavad olema t&auml;idetud!");
			}
		}
		if(!$this->can("view", $arr["data"]["project"]["value"]))
		{
			$cc = get_current_company();
			$np = new object();
			$np->set_class_id(CL_PROJECT);
			$np->set_parent($cc->id());
			$np->set_name($arr["data"]["project"]["value"]);
			$np->save();
			$arr["data"]["project"]["value"] = $np->id();
		}
		if(!$this->can("view", $arr["data"]["part"]["value"]))
		{
			$cc = get_current_company();
			$np = new object();
			$np->set_class_id(CL_CRM_PERSON);
			$np->set_parent($cc->id());
			$np->set_name($arr["data"]["part"]["value"]);
			$np->save();
			$arr["data"]["part"]["value"] = $np->id();
		}

		if(!$this->can("view", $arr["oid"]))
		{
			$o = new object();
			$o->set_parent($arr["data"]["project"]["value"]);
			$o->set_name($arr["data"]["name"]["value"]);
			$o->set_class_id(CL_CRM_MEETING);
			$o->set_prop("start1", $arr["first_start"]);
			$o->save();
			$person = obj($arr["data"]["part"]["value"]);
			$person->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_PERSON_MEETING",
			));
			$o->connect(array(
				"to" => $arr["data"]["project"]["value"],
				"type" => "RELTYPE_PROJECT",
			));

			$arr["oid"] = $o->id();
		}
		$o = obj($arr["oid"]);
		$o->set_prop("time_real", $o->prop("time_real") + $arr["hours"]);
		$o->set_prop("time_to_cust", $o->prop("time_to_cust") + $arr["hours"]);
		$o->set_prop("is_done", $arr["data"]["isdone"]["value"]?8:0);
	//	$o->set_prop("send_bill", $arr["data"]["tobill"]["value"]?1:0);
		$o->set_prop("content", $arr["data"]["desc"]["value"]);
		$o->set_prop("end", time());
		$o->save();
		return false;
	}

	function stopper_autocomplete($requester, $params)
	{
		switch($requester)
		{
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

	function gen_stopper_addon($arr)
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

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl == "kliendibaas_kohtumine" && $field == "")
		{
			$this->db_query("create table kliendibaas_kohtumine (id int primary key)");
			return true;
		}
		if ("planner" == $tbl)
		{
			$i = get_instance(CL_TASK);
			return $i->do_db_upgrade($tbl, $field);
		}
		switch($field)
		{
			case "udefch1":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int",
				));
				$this->do_db_upgrade_copy_data($field);
				return true;

			case "udeftb1":
			case "udeftb2":
			case "udeftb3":
			case "udeftb4":
			case "udeftb5":
			case "udeftb6":
			case "udeftb7":
			case "udeftb8":
			case "udeftb9":
			case "udeftb10":
			case "udefta1":
			case "udefta2":
			case "udefta3":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "text"
				));
				$this->do_db_upgrade_copy_data($field);
				return true;
		}
		return false;
	}

	private function do_db_upgrade_copy_data($field)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_MEETING,
			"parent" => array(),
			"site_id" => array(),
			"lang_id" => array(),
			"status" => array(),
		));
		foreach($ol->arr() as $o)
		{
			$value = $o->meta($field);
			$oid = $o->id();
			$this->db_query("
				INSERT INTO
					personnel_management_job_offer (oid, $field)
				VALUES
					('$oid', '$value')
			");
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
			$o->set_class_id(CL_CRM_MEETING);
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
						$o->set_prop($key , $val);
						break;
				}
			}

			if($arr["customer"])
			{
				$customers = new object_list(array(
					"class_id" => CL_CRM_COMPANY,
					"site_id" => array(),
					"lang_id" => array(),
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
					"site_id" => array(),
					"lang_id" => array(),
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
						"site_id" => array(),
						"lang_id" => array(),
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

			if(is_array($arr["participants"]) && sizeof($arr["participants"]))
			{
				foreach($arr["participants"] as $participant)
				{
					if($this->can("view" , $participant))
					{
						$o->add_participant($participant);
					}
				}
			}

			$u = new user();
			$p = $u->get_current_person();

			$o->add_participant($p);
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
			"value" => mktime(date("H"), 0, 0, date("m"), (date("d")+1), date("y")),
		));

		$htmlc->add_property(array(
			"name" => "end",
			"type" => "datetime_select",
			"caption" => t("L&otilde;pp"),
			"value" => mktime((date("H")+1), 0, 0, date("m"), date("d")+1, date("y")),
		));

		$htmlc->add_property(array(
			"name" => "hr_price",
			"type" => "textbox",
			"caption" => t("Tunnihind"),
		));

		$htmlc->add_property(array(
			"name" => "content",
			"type" => "textarea",
			"caption" => t("Sisu"),
			"rows" => 10,
			"cols" => 60,
		));

		$htmlc->add_property(array(
			"name" => "participants",
			"type" => "select",
			"caption" => t("Osalejad"),
			"multiple" => 1,
			"size" => 10,
			"options" => $company->get_worker_selection(),
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
			"value" => t("Lisa uus kohtumine!"),
			"onclick" => "changeform.submit();",
			"caption" => t("Lisa uus kohtumine!")
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

}
?>
