<?php

// project.aw - Projekt
/*


@classinfo relationmgr=yes

@tableinfo aw_projects index=aw_oid master_table=objects master_index=brother_of
@tableinfo aw_account_balances master_index=oid master_table=objects index=aw_oid
@default table=objects

@default group=general2
	@layout up_bit type=hbox width=50%:50%
		@layout left_bit type=vbox parent=up_bit closeable=1 area_caption=&Uuml;ldandmed
			@property name type=textbox parent=left_bit
			@caption Nimi

@property balance type=hidden table=aw_account_balances field=aw_balance

			@property code type=textbox table=aw_projects field=aw_code parent=left_bit
			@caption Kood

			@property priority type=textbox table=aw_projects field=aw_priority size=5 parent=left_bit
			@caption Prioriteet

			@property archive_code type=textbox table=aw_projects field=aw_archive_code size=10 parent=left_bit
			@caption Arhiveerimistunnus

			@property state type=select table=aw_projects field=aw_state default=1 parent=left_bit
			@caption Staatus

			@property doc type=relpicker reltype=RELTYPE_PRJ_DOCUMENT table=aw_projects field=aw_doc parent=left_bit
			@caption Loe l&auml;hemalt

			@property proj_price type=textbox table=objects field=meta method=serialize size=5 parent=left_bit
			@caption Projekti hind

			@property prepayment type=textbox table=aw_projects field=aw_prepayment size=5 parent=left_bit
			@caption Ettemaks

			@property outsourcing_expences type=textbox table=objects field=meta method=serialize size=5 parent=left_bit
			@caption Kulud allhankijatele

			@property account_received type=textbox table=objects field=meta method=serialize size=5 parent=left_bit
			@caption Tasu saadud

			@property contact_person_orderer type=select table=aw_projects field=aw_contact_person parent=left_bit
			@caption Tellija kontaktisik

			@property contact_person_implementor type=select table=aw_projects field=aw_contact_person_impl parent=left_bit
			@caption Teostaja kontaktisik

			@property orderer type=popup_search clid=CL_CRM_COMPANY,CL_CRM_PERSON reltype=RELTYPE_ORDERER table=objects field=meta method=serialize multiple=1 store=connect style=relpicker parent=left_bit

			@property implementor type=popup_search clid=CL_CRM_COMPANY,CL_CRM_PERSON reltype=RELTYPE_IMPLEMENTOR table=objects field=meta method=serialize multiple=1 store=connect style=relpicker parent=left_bit

			@property proj_mgr type=relpicker reltype=RELTYPE_PARTICIPANT table=aw_projects field=aw_proj_mgr parent=left_bit clid=CL_CRM_PERSON
			@caption Projekti juht

			@property category type=relpicker reltype=RELTYPE_CATEGORY store=connect multiple=1 size=2 parent=left_bit
			@caption Kategooria

		@layout center_bit type=vbox parent=up_bit no_padding=1
			@layout project_time type=vbox parent=center_bit closeable=1 no_padding=1 area_caption=Ajad

			@property start type=datepicker time=0 table=aw_projects field=aw_start parent=project_time
			@caption Algus

			@property end type=datepicker time=0 table=aw_projects field=aw_end parent=project_time
			@caption L&otilde;pp

			@property deadline type=datepicker time=0 table=aw_projects field=aw_deadline parent=project_time
			@caption T&auml;htaeg

			@property hrs_guess type=textbox table=aw_projects field=aw_hrs_guess size=5 parent=project_time
			@caption  Prognoositav tundide arv

			@layout project_people type=vbox parent=center_bit closeable=1 no_padding=1  area_caption=Osalejad
				@property parts_tb type=toolbar no_caption=1 store=no parent=project_people
				@property orderer_table type=table no_caption=1 store=no parent=project_people
				@property part_table type=table no_caption=1 store=no parent=project_people
				@property impl_table type=table no_caption=1 store=no parent=project_people

		@property implementor type=relpicker table=objects field=meta method=serialize reltype=RELTYPE_IMPLEMENTOR
		@caption Teostajad

		@property orderer type=relpicker table=objects field=meta method=serialize reltype=RELTYPE_ORDERER
		@caption Klient

		@property participants type=relpicker table=objects field=meta method=serialize reltype=RELTYPE_PARTICIPANT
		@caption Osalejad


@default group=info_t
	@property description type=textarea rows=10 cols=50 table=aw_projects field=aw_description
	@caption Kirjeldus

	@property budget type=textbox table=aw_projects field=aw_budget
	@caption Eelarve

	@property proj_type type=classificator table=aw_projects field=aw_type store=connect reltype=RELTYPE_TYPE multiple=1
	@caption Liik

	@property create_task type=checkbox ch_value=1 store=no
	@caption Moodusta &uuml;lesanne


@default group=web_settings
	@property project_navigator type=checkbox ch_value=1 table=aw_projects field=aw_project_navigator
	@caption N&auml;ita projektide navigaatorit

	@property use_template type=select table=aw_projects field=aw_use_template
	@caption V&auml;limus

	@property doc_id type=textbox size=6 table=aw_projects field=aw_doc_id
	@caption Dokumendi ID, milles asub kalendri vaade, milles s&uuml;ndmusi kuvatakse

	@property skip_subproject_events type=checkbox ch_value=1 table=aw_projects field=aw_skip_subproject_events
	@caption &Auml;ra n&auml;ita alamprojektide s&uuml;ndmusi

	@property prj_image type=releditor reltype=RELTYPE_PRJ_IMAGE use_form=emb rel_id=first field=meta method=serialize
	@caption Pilt


@default group=event_list_cal
	@property event_toolbar type=toolbar no_caption=1
	@caption S&uuml;ndmuste toolbar

	@property event_list type=calendar no_caption=1
	@caption Tegevused


@default group=add_event
	@property add_event type=callback callback=callback_get_add_event store=no
	@caption Lisa s&uuml;ndmus


@default group=files
	@property files_tb type=toolbar no_caption=1 store=no

	@layout files_lay type=hbox width=20%:80%
		@layout files_left_lay type=vbox parent=files_lay

		@layout files_tree_lay closeable=1 type=vbox area_caption=Projekti&nbsp;dokumendid parent=files_left_lay
			@property files_tree type=treeview store=no no_caption=1 parent=files_tree_lay

		@layout files_find_lay closeable=1 type=vbox area_caption=Dokumentide&nbsp;otsing parent=files_left_lay
			@property files_find_name type=textbox parent=files_find_lay size=27 captionside=top
			@caption Nimi

			@property files_find_comment type=textbox parent=files_find_lay size=27 captionside=top
			@caption Kirjeldus

			@property files_find_type type=select parent=files_find_lay captionside=top
			@caption T&uuml;&uuml;p

			@property files_search_sbt type=submit captionside=top parent=files_find_lay no_caption=1
			@caption Otsi

		@property files_table type=table store=no no_caption=1 parent=files_lay


@default group=income
	@layout income_lay type=hbox width=20%:80%
		@layout income_left_lay type=vbox parent=income_lay area_caption=Projekti&nbsp;parameetrid

			@property project_costs type=text parent=income_left_lay captionside=top store=no
			@caption Projekti maksumus

			@property planned_work_time type=textbox parent=income_left_lay captionside=top table=aw_projects field=planned_work_time
			@caption Planeeritud t&ouml;&ouml;aeg

			@property planned_work_time_text type=text store=no parent=income_left_lay no_caption=1
			@caption Planeeritud t&ouml;&ouml;aeg tegevustest

			@property average_hr_price type=textbox parent=income_left_lay captionside=top table=aw_projects field=average_hr_price
			@caption Planeeritud keskmine tunni omahind

			@property planned_other_expenses type=textbox parent=income_left_lay captionside=top table=aw_projects field=planned_other_expenses
			@caption Planeeritud muud kulud

		@layout income_right_lay closeable=1 type=vbox parent=income_lay area_caption=Projekti&nbsp;tulud

			@property income_table type=table store=no no_caption=1 parent=income_right_lay

@default group=estimate
	@layout estimate_general type=hbox area_caption=Projekti&nbsp;eelarve

		@property project_estimated_table type=text group=estimate parent=estimate_general store=no no_caption=1
		@caption Projekti eelarvestamise &uuml;ldinfo

	@layout income_spot_layout closeable=1 type=hbox area_caption=Tulukohad
		@property income_spot_table type=text group=estimate store=no no_caption=1 parent=income_spot_layout


@default group=trans
	@property trans type=translator store=no props=name
	@caption T&otilde;lkimine


@default group=sides
	@property sides_tb type=toolbar no_caption=1

	@property sides type=table store=no no_caption=1

	@property sides_st type=text subtitle=1 store=no
	@caption Konfliktsed projektid

	@property sides_conflict type=table store=no no_caption=1


@default group=userdefined
	@property user1 type=textbox table=aw_projects field=aw_user1 user=1
	@caption User-defined textbox 1

	@property user2 type=textbox table=aw_projects field=aw_user2 user=1
	@caption User-defined textbox 2

	@property user3 type=textbox table=aw_projects field=aw_user3 user=1
	@caption User-defined textbox 3

	@property user4 type=textbox table=aw_projects field=aw_user4 user=1
	@caption User-defined textbox 4

	@property user5 type=textbox table=aw_projects field=aw_user5 user=1
	@caption User-defined textbox 5

	@property userch1 type=checkbox ch_value=1 table=aw_projects field=aw_userch1 user=1
	@caption User-defined checkbox 1

	@property userch2 type=checkbox ch_value=1 table=aw_projects field=aw_userch2 user=1
	@caption User-defined checkbox 2

	@property userch3 type=checkbox ch_value=1 table=aw_projects field=aw_userch3 user=1
	@caption User-defined checkbox 3

	@property userch4 type=checkbox ch_value=1 table=aw_projects field=aw_userch4 user=1
	@caption User-defined checkbox 4

	@property userch5 type=checkbox ch_value=1 table=aw_projects field=aw_userch5 user=1
	@caption User-defined checkbox 5

	@property userclassif1 type=classificator reltype=RELTYPE_CLF1 table=aw_projects field=aw_userclf1 user=1
	@caption User-defined classificator 1

	@property controller_disp type=text store=no
	@caption Kontrolleri v&auml;ljund

@default group=bills_list
	@property bills_tb type=toolbar no_caption=1 store=no
	@layout bills type=hbox width=20%:80%
		@layout bills_left parent=bills type=vbox area_caption=Arved&nbsp;staatuste&nbsp;kaupa
			@property bills_tree type=treeview store=no no_caption=1 parent=bills_left

		@layout bills_right parent=bills type=vbox
			@layout data_r_charts type=hbox parent=bills_right width=50%:50% closeable=1 area_caption=Graafikud
				@layout chart1 parent=data_r_charts type=vbox
					@property status_chart type=google_chart no_caption=1 parent=chart1 store=no
				@layout chart2 parent=data_r_charts type=vbox
					@property money_chart type=google_chart no_caption=1 parent=chart2 store=no
			@layout bills_r parent=bills_right type=vbox area_caption=Arvete&nbsp;nimekiri
				@property bills_list type=table no_caption=1 store=no parent=bills_r

@default group=create_bill
	@property create_bill_tb type=toolbar no_caption=1 store=no

	@layout create_bill_table type=vbox area_caption=Arvele&nbsp;lisamata&nbsp;tehtud&nbsp;t&ouml;&ouml;e&nbsp;nimekiri
		@property work_list type=table no_caption=1 store=no parent=create_bill_table

	@layout work_charts type=hbox width=50%:50% closeable=1 area_caption=Graafikud
		@layout works_by_person parent=work_charts type=vbox
			@property works_by_person_chart type=google_chart no_caption=1 parent=works_by_person store=no
		@layout works_by_payment parent=work_charts type=vbox
			@property works_by_payment_chart type=google_chart no_caption=1 parent=works_by_payment store=no

@default group=team
	@property team_tb type=toolbar no_caption=1 store=no

	@layout team type=hbox width=20%:80%
		@layout team_left parent=team type=vbox
			@layout team_tree parent=team_left closeable=1 type=vbox area_caption=Meeskond

			@property team_team_tree type=treeview store=no no_caption=1 parent=team_tree

			@layout team_search parent=team_left closeable=1 type=vbox area_caption=Isikute&nbsp;otsing

			@property team_search_co type=textbox captionside=top parent=team_search size=22
			@caption Firma

			@property team_search_person type=textbox captionside=top parent=team_search size=22
			@caption Isik

			@property hidden_team type=hidden parent=team_search no_caption=1

			@property team_search_sbt type=submit captionside=top parent=team_search no_caption=1
			@caption Otsi

		@layout team_r parent=team type=vbox
			@property team type=table no_caption=1 store=no parent=team_r


@default group=goals_edit
	@property goal_tb type=toolbar no_caption=1

	@layout goal_vb type=hbox width="20%:80%"
		layout goal_tree_lay type=vbox closeable=1 area_caption=Eesm&auml;rgid parent=goal_vb
			property goal_tree type=treeview parent=goal_vb no_caption=1 parent=goal_tree_lay

		@layout task_types_tree_left type=vbox parent=goal_vb

			@layout task_types_tree_lay type=vbox closeable=1 area_caption=Puu parent=task_types_tree_left

				@property task_types_tree type=treeview no_caption=1 parent=task_types_tree_lay

			@layout task_types_search_lay type=vbox closeable=1 area_caption=Otsinguparameetrid parent=task_types_tree_left

 				@property search_part type=textbox captionside=top store=no parent=task_types_search_lay
				@caption Osaleja

				@property search_start type=datepicker time=0 captionside=top store=no parent=task_types_search_lay
				@caption Algus

				@property search_end type=datepicker time=0 captionside=top store=no parent=task_types_search_lay
				@caption L&otilde;pp

				@property search_type type=text captionside=top store=no parent=task_types_search_lay
				@caption T&uuml;&uuml;pide kaupa

				@property tasks_search_sbt type=submit captionside=top parent=task_types_search_lay no_caption=1
				@caption Otsi

		@layout task_table type=vbox closeable=1 area_caption=Tegevused parent=goal_vb
			@property goal_table type=table parent=task_table no_caption=1

@default group=goals_gantt
	@property goals_gantt type=text store=no no_caption=1

@default group=transl
	@property transl type=callback callback=callback_get_transl
	@caption T&otilde;lgi

@default group=strat
	@property strat_tb type=toolbar store=no no_caption=1
	@property strat type=table store=no no_caption=1

@default group=risks
	@property risks_tb type=toolbar store=no no_caption=1
	@property risks type=table store=no no_caption=1


@default group=req,req_process

	@property req_tb type=toolbar store=no no_caption=1

	@layout req_l type=hbox width=20%:80%

		@layout req_tree_l type=vbox parent=req_l closeable=1 no_padding=1 area_caption=N&otilde;uded&nbsp;puu&nbsp;kujul
		@property req_tree type=treeview store=no no_caption=1 parent=req_tree_l

		@layout req_tbl_l type=vbox parent=req_l no_padding=1
		@property req_tbl type=table store=no no_caption=1 parent=req_tbl_l


@default group=hours_stats

	@layout hours_stats_charts type=hbox width=50%:50% closeable=1 area_caption=Graafikud
		@layout hours_stats_by_person parent=hours_stats_charts type=vbox
			@property hours_stats_by_person_chart type=google_chart no_caption=1 parent=hours_stats_by_person store=no
		@layout hours_stats_by_type parent=hours_stats_charts type=vbox
			@property hours_stats_by_type_chart type=google_chart no_caption=1 parent=hours_stats_by_type store=no


	@layout hours_stats_top type=vbox area_caption=Projektiga&nbsp;seotud&nbsp;t&ouml;&ouml;tunnid
		@property hours_stats type=text store=no parent=hours_stats_top
		@caption T&ouml;&ouml;tunnid

		@property hours_stats_table type=table store=no no_caption=1 parent=hours_stats_top
		@caption T&ouml;&ouml;tunnid inimeste kaupa

@default group=stats_money
	@layout stats_money_charts type=vbox closeable=1 area_caption=Graafikud
		@layout stats_money_by_person parent=stats_money_charts type=vbox
			@property stats_money_by_person_chart type=google_chart no_caption=1 parent=stats_money_by_person store=no

	@layout stats_money_top type=vbox area_caption=Projektiga&nbsp;seotud&nbsp;rahavood
		@property money_stats_string type=text store=no parent=stats_money_top no_caption=1
		@caption Rahaline aruanne

		@property stats_money_table no_caption=1 parent=stats_money_top type=table store=no
		@caption Raha inimeste kaupa


@default group=stats
	@layout stats_head type=hbox width=10%:90%

		@layout stats_time type=vbox area_caption=Kuude&nbsp;valik&nbsp;tabelis parent=stats_head
			@property stats_time_chooser type=text store=no parent=stats_time no_caption=1
			@caption Tabeli ajavahemike valik

			@property stats_time_sbt type=submit captionside=top parent=stats_time no_caption=1
			@caption Vali

		@layout stats_right type=vbox parent=stats_head

			@layout stats_charts type=vbox closeable=1 area_caption=Graafik parent=stats_right
				@property stats_time_by_person_chart type=google_chart no_caption=1 parent=stats_charts store=no

			@layout stats_table_l type=vbox closeable=1 parent=stats_right area_caption=Projektiga&nbsp;seotud&nbsp;t&ouml;&ouml;tunnid

				@property stats type=text store=no parent=stats_table_l
				@caption T&ouml;&ouml;tunnid

				@property stats_table type=table store=no no_caption=1 parent=stats_table_l
				@caption T&ouml;&ouml;tunnid inimeste kaupa


@default group=stats_entry

	@property stats_entry_table type=table store=no no_caption=1
	@caption Sisesta t&ouml;&ouml;tunnid

@default group=prods

	@property prods_toolbar type=toolbar store=no no_caption=1
	@property prods_table type=table store=no no_caption=1

@groupinfo general2 parent=general caption="&Uuml;ldandmed"
	@groupinfo estimate caption="Eelarvestamine" parent=general
	@groupinfo income caption="Tulud" parent=general
	@groupinfo strat caption="Eesm&auml;rgid" parent=general submit=no
	@groupinfo risks caption="Riskid" parent=general submit=no
	@groupinfo sides parent=general caption="Konfliktianal&uuml;&uuml;s" submit=no
	@groupinfo info_t caption="Lisainfo" parent=general
	@groupinfo web_settings parent=general caption="Veebiseadistused"
	@groupinfo userdefined caption="Kasutaja defineeritud andmed" parent=general
@groupinfo participants parent=general caption="Osalejad"
@groupinfo event_list caption="Tegevused" submit=no
	@groupinfo goals_edit caption="Tegevused tabelis" parent=event_list submit=no
	@groupinfo goals_gantt caption="Tegevused voop&otilde;hiselt" parent=event_list submit=no
	@groupinfo event_list_cal caption="Tegevused kalendaarselt" submit=no parent=event_list
	@groupinfo req caption="N&otilde;uded" submit=no parent=event_list
	@groupinfo req_process caption="N&otilde;uded protsessidega" submit=no parent=event_list
	@groupinfo stats_entry caption="Vali t&uuml;&uuml;bid" parent=event_list

@groupinfo event_list_premise caption="Tegevused eeldustegevuste p&otilde;hiselt" submit=no
@groupinfo info caption="Projekti info"
@groupinfo valuation caption="Hindamine" submit=no
	@groupinfo strat_res caption="Eesm&auml;rkide hindamise tulemused" parent=valuation store=no submit=no
@groupinfo add_event caption="Muuda s&uuml;ndmust"
@groupinfo files_main caption="Dokumendid" submit=no
	@groupinfo files caption="Dokumendid" submit=no parent=files_main
	@groupinfo prods caption="Tooted" submit=no parent=files_main
@groupinfo trans caption="T&otilde;lkimine"
@groupinfo bills caption="Arved" submit=no
	@groupinfo bills_list caption="Arvete nimekiri" submit=no parent=bills
	@groupinfo create_bill caption="Maksmata t&ouml;&ouml;d" submit=no parent=bills
@groupinfo team caption="Meeskond" submit=no


@groupinfo reports caption="Aruanded" submit=no
	@groupinfo hours_stats caption="T&ouml;&ouml;aja aruanne" submit=no parent=reports
	@groupinfo stats_money caption="Rahavoo aruanne" submit=no parent=reports
	@groupinfo stats caption="Ajap&otilde;hine aruanne" submit=no parent=reports

@groupinfo transl caption=T&otilde;lgi


@reltype SUBPROJECT clid=CL_PROJECT value=1
@caption alamprojekt

@reltype PARTICIPANT clid=CL_CRM_PERSON,CL_USER,CL_CRM_COMPANY value=2
@caption osaleja

@reltype PRJ_EVENT value=3 clid=CL_TASK,CL_CRM_CALL,CL_CRM_OFFER,CL_CRM_DEAL,CL_CRM_MEETING,CL_PARTY,CL_COMICS
@caption S&uuml;ndmus

@reltype PRJ_FILE value=4 clid=CL_FILE
@caption Fail

@reltype TAX_CHAIN value=5 clid=CL_TAX_CHAIN
@caption Maksu p&auml;rg

@reltype PRJ_CFGFORM value=6 clid=CL_CFGFORM
@caption Seadete vorm

@reltype PRJ_DOCUMENT value=7 clid=CL_DOCUMENT
@caption Kirjeldus

@reltype PRJ_IMAGE value=8 clid=CL_IMAGE
@caption Pilt

@reltype ORDERER value=9 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption tellija

@reltype IMPLEMENTOR value=10 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption teostaja

@reltype PRJ_VIDEO value=11 clid=CL_VIDEO
@caption Video

@reltype TYPE value=12 clid=CL_META
@caption t&uuml;&uuml;p

@reltype CONTACT_PERSON value=13 clid=CL_CRM_PERSON
@caption kontaktisik

@reltype CLF1 value=14 clid=CL_META
@caption klassifikaator 1

@reltype SIDE value=15 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption konkurent

@reltype PREPAYMENT_BILL value=16 clid=CL_CRM_BILL
@caption Arve

@reltype STRAT value=17 clid=CL_PROJECT_STRAT_GOAL
@caption Eesm&auml;rk

@reltype RISK value=18 clid=CL_PROJECT_RISK
@caption Risk

@reltype STRAT_EVAL value=19 clid=CL_PROJECT_STRAT_GOAL_EVAL_WS
@caption Eesm&auml;rkide hindamislaud

@reltype RISK_EVAL value=20 clid=CL_PROJECT_RISK_EVAL_WS
@caption Riskide hindamislaud

@reltype TEAM value=21 clid=CL_PROJECT_TEAM
@caption Tiim

@reltype FILES_FLD value=22 clid=CL_MENU
@caption Failide kataloog

@reltype ANALYSIS_WS value=23 clid=CL_PROJECT_ANALYSIS_WS
@caption Anal&uuml;&uuml;si t&ouml;&ouml;laud

@reltype PRODUCT value=24 clid=CL_SHOP_PRODUCT
@caption Toode

@reltype CATEGORY value=25 clid=CL_PROJECT_CATEGORY
@caption Kategooria

@reltype CASH_COW value=26 clid=CL_CRM_CASH_COW
@caption Tulukoht


*/

class project extends class_base
{
	const DAY_LENGTH_SECONDS = 86400;

	private $do_create_task = 0;
	private $event_id = 0;

	function project()
	{
		$this->init(array(
			"clid" => CL_PROJECT,
			"tpldir" => "applications/groupware/project",
		));

		lc_site_load("project", $this);

		$this->event_entry_classes = array(CL_CALENDAR_EVENT, CL_STAGING, CL_CRM_MEETING, CL_TASK, CL_CRM_CALL, CL_PARTY, CL_COMICS);

		$this->states = array(
			PROJ_IN_PROGRESS => t("T&ouml;&ouml;s"),
			PROJ_DONE => t("Valmis")
		);

		$this->trans_props = array(
			"name"
		);

		$this->event_types = array(
			CL_BUG => "&Uuml;lesanded",
			CL_TASK => t("Toimetused"),
			CL_CRM_MEETING => t("Kohtumised"),
			CL_CRM_CALL => t("K&otilde;ned"),
		);
	}

	private function get_all_works_sum()
	{
		if(isset($this->all_work_sum))
		{
			return $this->all_work_sum;
		}
		else
		{
			return 0;
		}
	}

	private function month_selector($start , $end, $month_chooser = array())
	{
		$ret = "";
		$mY = "";
		if(!$end)
		{
			$end = time();
		}

		if(!$start)
		{
			$start = $end - 31*self::DAY_LENGTH_SECONDS;
		}

		if(!$month_chooser || !sizeof($month_chooser))
		{
			$month_chooser = array();
			$month_chooser[date("my" , $end)] = 1;
			$month_chooser[date("my" , mktime(0,0,0, date("m" , $end) - 1, date("d" , $end), date("Y" , $end)))] = 1;
		}

		while($start < $end)
		{
			if($mY != date("my" , $start))
			{
				$mY = date("my" , $start);
				$ret.= html::checkbox(array(
					"name" => "month_chooser[".$mY."]",
					"value" => 1,
					"checked" => ($month_chooser[$mY]) ? 1 : 0
				))." ".date("M Y" , $start)."<br>";
			}
			$start+= self::DAY_LENGTH_SECONDS*28;
		}
		if($mY != date("my" , $start) && date("my" , $start) == date("my" , $end))
		{
			$mY = date("my" , $start);
			$ret.= html::checkbox(array(
				"name" => "month_chooser[".$mY."]",
				"value" => 1,
				"checked" => ($month_chooser[$mY]) ? 1 : 0
			))." ".date("M Y" , $start)."<br>";
		}

		return $ret;
	}

	private function _get_project_estimated_table($arr)
	{
		$table_dat = array();
		$table_dat[] = array(
			"name" => t("Projekti eelarve"),
			"value" => html::textbox(array(
				"name" => "budget",
				"value" => $arr["obj_inst"]->prop("budget"),
				"size" => 5,
				)),
		);
		$table_dat[] = array(
			"name" => t("Projekti v&auml;ljam&uuml;&uuml;gi hind"),
			"value" => $arr["obj_inst"]->prop("proj_price"),
		);
		$table_dat[] = array(
			"name" => t("Eelarvestamata kulude olemasolu"),
			"value" => $arr["obj_inst"]->has_not_guessed_expenses() ? t("on") : t("ei ole"),
		);
		$table_dat[] = array(
			"name" => t("Jooksev maksumus"),
			"value" => 0,
		);
		$table_dat[] = array(
			"name" => t("M&auml;&auml;ramata eelarve osa"),
			"value" => 0,
		);
		$table_dat[] = array(
			"name" => t("Eelarvest v&auml;lja l&auml;inud summa"),
			"value" => 0,
		);
		$arr["prop"]["value"] = $this->do_fckng_table($table_dat);

	}


	function _get_income_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "group",
			"caption" => "",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => "",
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => "",
		));
		$t->define_field(array(
			"name" => "pr",
			"caption" => "",
		));
		$t->define_field(array(
			"name" => "tt",
			"caption" => "",
			"align" => "right",
		));

		$bill_sum = $arr["obj_inst"]->get_bill_sum();
		$bill_income = $arr["obj_inst"]->get_project_income_cc();
		$billed_hours = $arr["obj_inst"]->get_billed_hours();
		$billable_hours = $arr["obj_inst"]->get_billable_hours();

		$t->define_data(array(
			"group" => t("Tulud"),
			"name" => t("Makstud"),
			"sum" => $arr["obj_inst"]->get_project_income_text(),
			"pr" => number_format($bill_income  / $arr["obj_inst"]->prop("proj_price") * 100 ,  2)." %" ,
			"tt" => number_format($billed_hours * ($bill_income/$bill_sum),2)." TT",
		));

		$t->define_data(array(
			"group" => "",
			"name" => t("Maksmata"),
			"sum" => $bill_sum - $bill_income,
			"pr" => number_format(($bill_sum - $bill_income) / $arr["obj_inst"]->prop("proj_price") * 100 , 2)." %",
			"tt" => number_format($billed_hours * (1 - ($bill_income/$bill_sum)),2)." TT",
		));

		$t->define_data(array(
			"group" => t("Viittulu"),
			"name" => t("Tehtud ja maksmata"),
			"sum" => round(min((($billable_hours / $arr["obj_inst"]->prop("planned_work_time"))*$arr["obj_inst"]->prop("proj_price")) , ($arr["obj_inst"]->prop("proj_price") - $bill_sum)), 2),
			"pr" => round (($billable_hours / $arr["obj_inst"]->prop("planned_work_time"))*100 , 2)." %",
			"tt" => $billable_hours." TT",
		));

		$t->define_data(array(
			"group" => "",
			"name" => t("Tegemata t&ouml;&ouml;d"),
			"sum" => round((($arr["obj_inst"]->prop("planned_work_time") - $billable_hours  - $billed_hours) / $arr["obj_inst"]->prop("planned_work_time"))*$arr["obj_inst"]->prop("proj_price") , 2),
			"pr" => round ((($arr["obj_inst"]->prop("planned_work_time") - $billable_hours  - $billed_hours)  / $arr["obj_inst"]->prop("planned_work_time"))*100 , 2)." %",
			"tt" => $arr["obj_inst"]->prop("planned_work_time") - $billable_hours - $billed_hours." TT",
		));

		$t->set_sortable(false);
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		switch($data["name"])
		{
			case "project_estimated_table":
				$this->_get_project_estimated_table($arr);
				break;
			case "income_spot_table":
				$this->_get_income_spot_table($arr);
				break;
			case "project_costs":
				$data["value"] = $arr["obj_inst"]->prop("proj_price");
				break;
			case "planned_work_time_text":
				$data["value"] = t("Planeeritud tegevused")." : ";
				$data["value"].= round($arr["obj_inst"]->get_planned_hours() , 2);
				$data["value"].= " ".t("tundi")."<br>";
				break;
			case "money_stats_string":
				$data["value"] = t("Laekunud")." : ";
				$data["value"].= $arr["obj_inst"]->get_project_income_text();
				$data["value"].= "<br>".t("Tuletatud tunnihind")." : ";
				$data["value"].= round(($arr["obj_inst"]->get_project_income_cc() / $arr["obj_inst"]->get_real_hours()) , 2);// . " ".$arr["obj_inst"]->get_default_currency_name();
				break;
			case "stats_time_chooser":
				$data["value"] = $this->month_selector(
					$arr["obj_inst"]->prop("start"),
					time(),
					$arr["request"]["month_chooser"]
				);
				break;
			case "search_part":
				$data["size"] = 34;
			case "search_part":
			case "search_start":
			case "search_end":
				$data["value"] = isset($arr["request"][$data["name"]]) ? $arr["request"][$data["name"]] : "";
				break;
			case "search_type":
				$data["value"]= html::checkbox(array(
					"name" => "search_type[".CL_TASK."]",
					"value" => 1,
					"checked" => !empty($arr["request"][$data["name"]][CL_TASK]) ? 1 : 0
				))." ".t("Toimetus")."<br>".
				$data["value"] = html::checkbox(array(
					"name" => "search_type[".CL_CRM_MEETING."]",
					"value" => 1,
					"checked" => !empty($arr["request"][$data["name"]][CL_CRM_MEETING]) ? 1 : 0
				))." ".t("Kohtumine")."<br>".
				$data["value"] = html::checkbox(array(
					"name" => "search_type[".CL_CRM_CALL."]",
					"value" => 1,
					"checked" => !empty($arr["request"][$data["name"]][CL_CRM_CALL]) ? 1 : 0
				))." ".t("K&otilde;ne")."<br>".
				$data["value"] = html::checkbox(array(
					"name" => "search_type[".CL_BUG."]",
					"value" => 1,
					"checked" => !empty($arr["request"][$data["name"]][CL_BUG]) ? 1 : 0
				))." ".t("Arendus&uuml;lesanne");
				break;

			case "hours_stats_by_person_chart":
				$c = $arr["prop"]["vcl_inst"];
				$c->set_colors(array(
					"990000","000033","000066","000099","0000cc",
					"000000","003300","006600","009900","00cc00",
					"330000","660000","000000","aa0000","aa00cc",
					"330099","660099","990099","aa0099","9900cc",
				));

			case "works_by_person_chart":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}

				$c = $arr["prop"]["vcl_inst"];

				$c->set_type(GCHART_PIE_3D);
				$c->set_size(array(
					"width" => 600,
					"height" => 200,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));

				$times = array();
				$labels = array();

				$works = $arr["obj_inst"]-> get_rows_data();
				$tasks = array();
				$works_per_person = array();
				foreach($works as $work)
				{
					$implementor = reset($work["impl"]);
					$works_per_person[$implementor]+= $work["time_real"];
				}
				foreach($works_per_person as $person => $hours)
				{
					if($person)
					{
						$times[$person] = $hours;
						$labels[] = get_name($person) . " (" .$hours. ")";
					}
				}
				$this->all_work_sum = array_sum($works_per_person);
				$c->add_data($times);
				$c->set_labels($labels);

				$c->set_title(array(
					"text" => t("T&ouml;&ouml;de jaotus inimeste kaupa tundides").". (".t("Kokku").":".array_sum($works_per_person).")",
					"color" => "666666",
					"size" => 11,
				));

				break;

			case "hours_stats_by_type_chart":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}


				$c = $arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_BAR_GV);
				$c->set_size(array(
					"width" => 640,
					"height" => 200,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));

				$c->set_title(array(
					"text" => t("T&ouml;&ouml;tunnid t&uuml;&uuml;pide kaupa"),
					"color" => "444444",
					"size" => 11,
				));

				$times = array();
				$labels = array();

				$all_data = $arr["obj_inst"]->get_rows_data();
				$work_data = array();
				$params = array(
					"prog" => t("Prognoositud"),
					"real" => t("Tegelik"),
					"cust" => t("Kliendile"),

				);

				foreach($all_data as $dat)
				{
					$work_data[$dat["task.class_id"]]["prog"] +=$dat["time_guess"];
					$work_data[$dat["task.class_id"]]["real"] +=$dat["time_real"];
					$work_data[$dat["task.class_id"]]["cust"] +=$dat["time_to_cust"];//? $dat["time_to_cust"] : $dat["time_real"];

				}

				$max = 0;

				foreach($work_data as $clid  => $d)
				{
					foreach($d as $param => $count)
					{
						if($param == "real")
						{
							$labels[] = $this->event_types[$clid];
						}else $labels[] = " ";


						if($max < $count)
						{
							$max = $count;
						}
						$label2[] = $params[$param];
						$times[] = number_format($count , 2);
					}
				}
				$c->set_axis(array(
					GCHART_AXIS_LEFT,
					GCHART_AXIS_BOTTOM,
					GCHART_AXIS_BOTTOM,
					GCHART_AXIS_TOP,
				));

		//set some labels
		$c->add_axis_label(0, array("0", round($max/2), round($max)));

		$c->add_axis_style(2, array(
			"color" => "FFFFFF",
			"font" => 8,
			"align" => GCHART_AXIS_TOP,
		));

		//set the range and style for one of them
		$c->add_axis_style(1, array(
			"color" => "ff0000",
			"font" => 11,
			"align" => GCHART_AXIS_BOTTOM,
		));
				$c->set_colors(array(
					"aa2222", "FFFF00","bbbbbb", "aa2222", "FFFF00","bbbbbb", "aa2222", "FFFF00","bbbbbb", "aa2222", "FFFF00",
				));

				$c->set_bar_sizes(array(
					"width" => 60,
					"bar_spacing" => 3,
					"bar_group_spacing" => 8,
				));

				$c->add_axis_label(2, $labels);
				$c->add_axis_label(3, $times);
				$c->add_data($times);
				$c->set_labels($label2);

				break;
			case "works_by_payment_chart":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}
				$c = $arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_PIE_3D);
				$c->set_size(array(
					"width" => 650,
					"height" => 150,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));

				$times = array();
				$labels = array();

				$works = $arr["obj_inst"]-> get_rows_data();
				$tasks = array();

				foreach($works as $work)
				{
					$tasks[$work["task"]]+= $work["time_real"];
				}
				$work_price = 0;
				$task_list = new object_list();
				$task_list->add(array_keys($tasks));
				foreach($task_list->arr() as $task)
				{
					$work_price+= $task->prop("hr_price") * $tasks[$task->id()];
				}

				$bill_sum = 0;
				$payment_sum = 0;
				$bills = $arr["obj_inst"]->get_bills();
				foreach($bills->arr() as $bill)
				{
					$payment_sum += $bill->get_payments_sum();
					$bill_sum +=$bill->get_sum();
				}
				$c->set_title(array(
					"text" => t("Laekumine t&ouml;&ouml;de eest"),
					"color" => "666666",
					"size" => 11,
				));
				$unpaid_work = max(0 , $work_price - $bill_sum);
				$bill_sum = max(0 , $bill_sum - $payment_sum);
				$times[] = $unpaid_work;
				$times[] = $bill_sum;
				$times[] = $payment_sum;
				$c->set_colors(array(
					"bbbbbb", "aa2222", "FFFF00",
				));
				$labels[] = t("Arvele minemata t&ouml;id summas")." ".$unpaid_work;
				$labels[] = t("Laekumata arveid summas")." ".$bill_sum;
				$labels[] = t("Laekunud")." ".$payment_sum;

				$c->add_data($times);
				$c->set_labels($labels);

				break;
			case "status_chart":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}
				$c = $arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_PIE_3D);
				$c->set_size(array(
					"width" => 350,
					"height" => 120,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));

				$bills = $arr["obj_inst"]->get_bills();

				$times = array();
				$labels = array();

				foreach($bills->arr() as $bill)
				{
					$times[$bill->prop("state")] ++;
				}

				$bill_inst = get_instance(CL_CRM_BILL);

				foreach($times as $status => $count)
				{
					$labels[] = $bill_inst->states[$status]." (".$count.")";
				}
				$c->add_data($times);
				$c->set_labels($labels);
				$c->set_title(array(
					"text" => t("Arveid staatuste kaupa"),
					"color" => "666666",
					"size" => 11,
				));
				break;

			case "stats_money_by_person_chart":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}
				$this->_get_stats_money_by_person_chart($arr);
				break;

		case "stats_time_by_person_chart":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}
				$c2 = $arr["prop"]["vcl_inst"];
				$c2->set_type(GCHART_LINE_CHART);
				$c2->set_size(array(
					"width" => 1000,
					"height" => 220,
				));
				$c2->set_colors(array("5511aa"));
				$c2->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));
				$times = array();
				$data1 = array();
				$bot_axis = array();

				$all_data = $arr["obj_inst"]->get_rows_data();
				$work_data = array();
				$end = $arr["obj_inst"]->prop("end");
				$start = time();
				$max_hours = 0;

				$result = array();

				foreach($all_data as $work)
				{
					if(!$work["time_real"])
					{
						continue;
					}
					$date_day_start = date("YW" , $work["date"]);
					if($end < $work["date"])
					{
						$end = $work["date"];
					}
					if($start > $work["date"])
					{
						$start = $work["date"];
					}
					$result[$work["task.class_id"]][$date_day_start] +=$work["time_real"];
					$result[1][$date_day_start] +=$work["time_real"];
				}


				if($start < $end - self::DAY_LENGTH_SECONDS * 600)
				{
					$start = $end - self::DAY_LENGTH_SECONDS * 600;
				}
				$start = date_calc::get_week_start($start);
				if(!($end > 1 && $start > 1))
				{
					return;
				}
				$month = 1;
				while($end > $start)
				{
					if(date("mY", $month) != date("mY", $start))
					{
						$month = $start;
						$bot_axis[] = date("M Y" , $start);
					}
					else
					{
	//					$bot_axis[] = "";
					}
					$data1[] = $result[1][date("YW" , $start)];
					if($max_hours < $result[1][date("YW" , $start)])
					{
						$max_hours = $result[1][date("YW" , $start)];
					}
					$start += self::DAY_LENGTH_SECONDS*7;
				}

				$c2->add_data($data1);
				$c2->set_axis(array(GCHART_AXIS_LEFT, GCHART_AXIS_BOTTOM));
				$left_axis = array();
				$round_i = 0;
				if($max_hours < 10)
				{
					$round_i = 2;
				}
				if ($max_hours > 0)
				{
					for($i = 0; $i <= $max_hours; $i+= $max_hours/4)
					{
						$left_axis[] = round($i, $round_i);
					}
				}
				$c2->add_axis_label(0, $left_axis);

				$c2->add_axis_label(1, $bot_axis);
				$c2->add_axis_style(1, array(
					"color" => "888888",
					"font" => 10,
					"align" => GCHART_ALIGN_CENTER,
				));
				$c2->set_grid(array(
					"xstep" => 30,
					"ystep" => 20,
				));
				$c2->set_title(array(
					"text" => t("Projekti T&ouml;&ouml; aktiivsus tundides n&auml;da kohta"),
					"color" => "555555",
					"size" => 10,
				));
				break;

			case "money_chart":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}
				$c2 = $arr["prop"]["vcl_inst"];
				$c2->set_type(GCHART_LINE_CHART);
				$c2->set_size(array(
					"width" => 450,
					"height" => 120,
				));
				$c2->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));

				$works = $arr["obj_inst"]-> get_rows_data();

				$tasks = array();

				foreach($works as $work)
				{
					$tasks[$work["task"]]+= $work["time_real"];
				}
				$work_price = 0;
				$task_list = new object_list();
				$task_list->add(array_keys($tasks));
				foreach($task_list->arr() as $task)
				{
					$work_price+= $task->prop("hr_price") * $tasks[$task->id()];
				}

				$bill_sum = 0;
				$payment_sum = 0;
				$bills = $arr["obj_inst"]->get_bills();
				foreach($bills->arr() as $bill)
				{
					$payment_sum += $bill->get_payments_sum();
					$bill_sum +=$bill->get_sum();
				}

				$times = array();
				$data1 = array();

				$data1[] = $work_price;
				$data1[] = $bill_sum;
				$data1[] = $payment_sum;

				$c2->add_data($data1);
				$c2->set_axis(array(GCHART_AXIS_LEFT, GCHART_AXIS_BOTTOM));
				$left_axis = array();
				if ($work_price > 0)
				{
					for($i = 0; $i <= $work_price; $i+= $work_price/4)
					{
						$left_axis[] = round($i, 2);
					}
				}
				$c2->add_axis_label(0, $left_axis);
				$bot_axis = array();

				$bot_axis[] = t("Tehtud t&ouml;id summas")." ".$work_price;
				$bot_axis[] = t("Arveid esitatud summas")." ".$bill_sum;
				$bot_axis[] = t("Laekunud")." ".$payment_sum;

				$c2->add_axis_label(1, $bot_axis);
				$c2->add_axis_style(1, array(
					"color" => "888888",
					"font" => 10,
					"align" => GCHART_ALIGN_CENTER,
				));
				$c2->set_grid(array(
					"xstep" => 30,
					"ystep" => 20,
				));
				$c2->set_title(array(
					"text" => t("Projekti statistika rahaliselt"),
					"color" => "555555",
					"size" => 10,
				));
				$arr["prop"]["type"] = "text";
				$arr["prop"]["value"] = $c2->get_html();
				break;

			case "prods_toolbar":
				$this->_get_prods_toolbar($arr);
				break;

			case "prods_table":
				$this->_get_prods_table($arr);
				break;
			case "income_table":
				$this->_get_income_table($arr);
				break;

			case "hidden_team":
				if($arr["request"]["team"]) $data["value"] = $arr["request"]["team"];
				if($arr["request"]["hidden_team"]) $data["value"] = $arr["request"]["hidden_team"];
				break;
			case "parts_tb":
				$this->_parts_tb($arr);
				break;
			case "orderer_table":
				$this->_orderer_table($arr);
				break;
			case "part_table":
				$this->_part_table($arr);
				break;
			case "impl_table":
				$this->_impl_table($arr);
				break;
/*
			case "analysis_tb":
			case "analysis_table":
				static $ib;
				if (!$ib)
				{
					$ib = get_instance("applications/groupware/project_analysis_impl");
				}
				$fn = "_get_".$data["name"];
				return $ib->$fn($arr);
				break;
*/
			case "files_tb":
			case "files_tree":
			case "files_table":
				static $ia;
				if (!$ia)
				{
					$ia = get_instance("applications/groupware/project_files_impl");
				}
				$fn = "_get_".$data["name"];
				return $ia->$fn($arr);
				break;

			case "team_tb":
			case "team_team_tb":
			case "team_team_tree":
			case "team_team_tbl":
			case "team":
				static $i;
				if (!$i)
				{
					$i = get_instance("applications/groupware/project_teams_impl");
				}
				$fn = "_get_".$data["name"];
				return $i->$fn($arr);
				break;

			case "req_tb":
			case "req_tree":
			case "req_tbl":
				static $i;
				if (!$i)
				{
					$i = get_instance("applications/groupware/project_req_impl");
				}
				$fn = "_get_".$data["name"];
				return $i->$fn($arr);
				break;

			case "risks":
				$this->_risks($arr);
				break;

			case "risks_tb":
				$this->_risks_tb($arr);
				break;

			case "strat_tb":
				$this->_strat_tb($arr);
				break;
/*
			case "strat_a_tb":
				$this->_strat_a_tb($arr);
				break;
*/
			case "strat":
				$this->_strat($arr);
				break;

			case "risks":
				$data["direct_links"] = 1;
				break;
/*
			case "risks_eval":
				$this->_risks_eval($arr);
				break;

			case "risks_eval_tb":
				$this->_risks_eval_tb($arr);
				break;

			case "strat_a":
				$this->_strat_a($arr);
				break;
*/
			case "strat_res":
				$this->_strat_res($arr);
				break;

			case "controller_disp":
				$cs = get_instance(CL_CRM_SETTINGS);
				$pc = $cs->get_project_controller($cs->get_current_settings());
				if ($this->can("view", $pc))
				{
					$pco = obj($pc);
					$pci = $pco->instance();
					$data["value"] = $pci->eval_controller($pc, $arr["obj_inst"]);
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "proj_type":
				if ($arr["new"])
				{
					$data["value"] = array();
				}
				break;

			case "files":
				$this->_get_files($arr);
				break;

			case "sides_conflict":
				$this->_get_sides_conflict($arr);
				break;

			case "files_find_type":
				$data["options"] = array(
					"" => "",
					CL_FILE => t("Fail"),
					CL_CRM_MEMO => t("Memo"),
					CL_CRM_DOCUMENT => t("CRM Dokument"),
					CL_CRM_DEAL => t("Leping"),
					CL_CRM_OFFER => t("Pakkumine"),
					CL_PROJECT_STRAT_GOAL_EVAL_WS => t("Eesm&auml;rkide hindamise t&ouml;&ouml;laud"),
					CL_PROJECT_RISK_EVAL_WS => t("Riskide hindamise t&ouml;&ouml;laud"),
					CL_PROJECT_ANALYSIS_WS => t("Anal&uuml;&uuml;si t&ouml;&ouml;laud"),
				);
			case "team_search_co":
			case "team_search_person":
			case "files_find_name":
			case "files_find_comment":
				$data["value"] = $arr["request"][$data["name"]];
				break;

/*			case "team_search_res":
				$this->_get_team_search_res($arr);
				break;
*/
			case "implementor_person":
				$i = get_instance(CL_CRM_COMPANY);
				if ($this->can("view", $arr["obj_inst"]->prop("implementor")))
				{
					$inf = array();
					$i->get_all_workers_for_company(obj($arr["obj_inst"]->prop("implementor")), $inf);
					$ol = new object_list(array("oid" => $inf));
					$data["options"] = array("" => "") + $ol->names();
				}
				break;

			case "sides_tb":
				$this->_sides_tb($arr);
				break;

			case "sides":
				$this->_sides($arr);
				break;

			case "contact_person_orderer":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					return PROP_IGNORE;
				}
				$data["options"] = array("" => t("--Vali--"));
				foreach($arr["obj_inst"]->connections_from(array("type" => 9)) as $c)
				{
					$c = $c->to();
					if($c->class_id() == CL_CRM_PERSON){
						$data["options"][$c->id()] = $c->name();
					}
					if($c->class_id() == CL_CRM_COMPANY){
						$wl = array();
						$i = get_instance(CL_CRM_COMPANY);
						$i->get_all_workers_for_company($c, $wl);
						if (count($wl))
						{
							$ol = new object_list(array("oid" => $wl));
							foreach ($ol->arr() as $person)
							{
								$data["options"][$person->id()] = $person->name();
							}
						}
					}

				}
				/*
				$ord = $arr["obj_inst"]->prop("orderer");
				if (is_array($ord))
				{
					$ord = reset($ord);
				}
				if ($this->can("view", $ord))
				{
					$this->_proc_cp(obj($ord), $data);
				}
				asort($data["options"]);*/
				break;

			case "contact_person_implementor":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					return PROP_IGNORE;
				}
				$data["options"] = array("" => t("--Vali--"));
				foreach($arr["obj_inst"]->connections_from(array("type" => 10)) as $c)
				{
					$c = $c->to();
					if($c->class_id() == CL_CRM_PERSON){
						$data["options"][$c->id()] = $c->name();
					}
					if($c->class_id() == CL_CRM_COMPANY){
						$wl = array();
						$i = get_instance(CL_CRM_COMPANY);
						$i->get_all_workers_for_company($c, $wl);
						if (count($wl))
						{
							$ol = new object_list(array("oid" => $wl));
							foreach ($ol->arr() as $person)
							{
								$data["options"][$person->id()] = $person->name();
							}
						}
					}

				}
				/*
				$ord = $arr["obj_inst"]->prop("implementor");
				if (is_array($ord))
				{
					$ord = reset($ord);
				}
				if ($this->can("view", $ord))
				{
					$this->_proc_cp(obj($ord), $data);
				}
				asort($data["options"]);*/
				break;

			case "orderer":
				return PROP_IGNORE;
				if ($this->can("view", $arr["request"]["connect_orderer"]))
				{
					$data["value"] = array(
						$arr["request"]["connect_orderer"] =>
							$arr["request"]["connect_orderer"]
					);
				}
				/*if (is_array($data["value"]))
				{
					$data["value"] = reset($data["value"]);
				}*/

				// get values
				$u = get_instance(CL_USER);
				$me = $u->get_current_person();
				$ol = new object_list(array(
					"class_id" => array(CL_CRM_PERSON,CL_CRM_COMPANY),
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_CRM_PERSON.client_manager" => $me,
							"CL_CRM_COMPANY.client_manager" => $me
						)
					)),
					"brother_of" => new obj_predicate_prop("id")
				));


				$data["options"] = array("" => "--vali--") + $ol->names();
				foreach((array)$data["value"] as $_id)
				{
					if (!isset($data["options"][$_id]) && $this->can("view", $_id))
					{
						$tmp = obj($_id);
						$data["options"][$tmp->id()] = $tmp->name();
					}
				}
				asort($data["options"]);
				break;

			case "implementor":
				return PROP_IGNORE;
				if ($arr["new"])
				{
					$data["value"] = $arr["request"]["connect_impl"];
				}
				if (is_array($data["value"]))
				{
					$data["value"] = reset($data["value"]);
				}
				if (!isset($data["options"][$data["value"]]) && $this->can("view", $data["value"]))
				{
					$tmp = obj($data["value"]);
					$data["options"][$tmp->id()] = $tmp->name();
				}

				$u = get_instance(CL_USER);
				$co = obj($u->get_current_company());
				$data["options"][$co->id()] = $co->name();

				asort($data["options"]);
				break;

			case "participants":
				return PROP_IGNORE;
				if (!$arr["new"])
				{
					$cur_pts = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PARTICIPANT"));
				}
				$people = array();
				$u = get_instance(CL_USER);
				$co = $u->get_current_company();
				$i = get_instance(CL_CRM_COMPANY);
				$people = array_keys($i->get_employee_picker(obj($co),false,true));
				if (!count($people))
				{
					$ol = new object_list();
				}
				else
				{
					$ol = new object_list(array("oid" => array_values($people), "lang_id" => array(), "site_id" => array()));
				}
				$sel = array();
				foreach($cur_pts as $pt)
				{
					if ($pt->prop("to.class_id") == CL_USER)
					{
						continue;
					}
					$ol->add($pt->prop("to"));
					$sel[$pt->prop("to")] = $pt->prop("to");
				}

				if (!is_object($arr["obj_inst"]) || !is_oid($arr["obj_inst"]->id()))
				{
					$sel = $u->get_current_person();
				}
				$data["options"] = array("" => t("--Vali--")) + $ol->names();
				asort($data["options"]);
				$data["value"] = $sel;
				break;

			case "state":
				$data["options"] = $this->states;
				break;

			case "event_list":
				$this->gen_event_list($arr);
				break;

			case "event_toolbar":
				$this->gen_event_toolbar($arr);
				break;

			case "use_template":
				$data["options"] = array(
					"weekview" => t("N&auml;dala vaade"),
				);
				break;

			case "goal_tb":
				$this->_goal_tb($arr);
				break;

			case "bills_tree":
				$this->_get_bills_tree($arr);
				break;

			case "bills_list":
				$this->_get_bills_table($arr);
				break;

			case "goal_tree":
				$this->_goal_tree($arr);
				break;

			case "task_types_tree":
				$this->_task_types_tree($arr);
				break;

			case "work_list":
				$this->_get_work_list($arr);
				break;

			case "create_bill_tb":
				$this->_get_create_bill_tb($arr);
				break;
			case "bills_tb":
				$this->_get_bills_tb($arr);
				break;

			case "goal_table":
				$this->_goal_table($arr);
				break;

			case "goals_gantt":
				$data["value"] = $this->_goals_gantt($arr);
				break;

			case "prepayment":
				if (!is_oid($arr["obj_inst"]->id()))
				{
					return PROP_IGNORE;
				}
				$bill = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_PREPAYMENT_BILL");
				if ($bill)
				{
					$data["post_append_text"] = " ".html::obj_change_url($bill);
				}
				else
				{
					$data["post_append_text"] = " ".html::href(array(
						"url" => $this->mk_my_orb("do_create_prepayment_bill", array(
							"id" => $arr["obj_inst"]->id(),
							"ru" => get_ru()
						)),
						"caption" => t("Loo arve")
					));
				}
				break;

			case "stats":return PROP_IGNORE;
				$data["value"] = $this->_get_stats($arr["obj_inst"]);
				break;

			case "stats_table":
				$data["value"] = $this->_get_stats_table($arr);
				break;
			case "hours_stats_table":
				$data["value"] = $this->_get_hours_stats_table($arr);
				break;

			case "stats_money_table":
				$data["value"] = $this->_get_stats_money_table($arr);
				break;

			case "stats_entry_table":
				$this->_get_stats_entry_table($arr);
				break;
			case "hours_stats":
				return PROP_IGNORE;
		}
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "project_estimated_table":
				$arr["obj_inst"]->set_prop("budget" , $arr["request"]["budget"]);
				break;
			case "prods_toolbar":
				$ps = get_instance("vcl/popup_search");
				$ps->do_create_rels($arr["obj_inst"], $arr["request"]["prod_search_res"], 24);
				break;

/*			case "risks_eval":
				$this->_save_risks_eval($arr);
				break;

*/			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "files":
				$this->_set_files($arr);
				break;

			case "create_task":
				if ($prop["value"] == 1)
				{
					$this->do_create_task = 1;
				}
				break;

			case "orderer":
			case "implementor":
			case "implementor_person":
				if ($arr["request"]["connect_orderer"] && $prop["name"] === "orderer")
				{
					$prop["value"] = $arr["request"]["connect_orderer"];
				}
				if ($arr["request"]["connect_impl"] && $prop["name"] === "implementor")
				{
					$prop["value"] = $arr["request"]["connect_impl"];
				}
				if (count(explode(",", $prop["value"])) > 1)
				{
					$prop["value"] = $this->make_keys(explode(",", $prop["value"]));
				}
				if (is_oid($prop["value"]))
				{
					$prop["value"] = array($prop["value"]);
				}
				else
				if (!is_array($prop["value"]))
				{
					$prop["value"] = array();
				}
				if (count($prop["value"]))
				{
					foreach($prop["value"] as $v)
					{
						if ($arr["new"] || !$arr["obj_inst"]->is_connected_to(array("type" => "RELTYPE_PARTICIPANT", "to" => $v)))
						{
							$arr["obj_inst"]->connect(array(
								"reltype" => "RELTYPE_PARTICIPANT",
								"to" => $v
							));
						}
					}
				}
				break;

			case "add_event":
				$this->register_event_with_planner($arr);
				break;

			case "sel_resources":
				$this->save_sel_resources($arr);
				break;

			case "resources";
				$this->do_save_resources($arr);
				break;

			case "confirm":
				if ($prop["value"] == 1)
				{
					$this->do_write_times_to_cal($arr);
				}
				break;

			/*case "priority":
				if ($prop["value"] != $arr["obj_inst"]->prop("priority") && is_oid($arr["obj_inst"]->id()) && $arr["obj_inst"]->prop("confirm"))
				{
					// write priority to all events from this
					$evids = new aw_array($arr["obj_inst"]->meta("event_ids"));
					foreach($evids->get() as $evid)
					{
						$evo = obj($evid);
						$evo->set_meta("task_priority", $prop["value"]);
						$evo->save();
					}

					// also, recalc times
					$this->do_write_times_to_cal($arr);
				}
				break;*/

			case "participants":
				if ($arr["new"])
				{
					$p = get_current_person();
					$prop["value"] = array(
						$p->id() => $p->id(),
					);
				}
				break;

			case "state":
				if (!$prop["value"])
				{
					$prop["value"] = PROJ_IN_PROGRESS ;
				}
				break;

			case "stats_entry_table":
				$this->_set_stats_entry_table($arr);
				break;
		}
		return $retval;
	}

	////
	// !Optionally this also needs to support date range ..
	function gen_event_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$arr["prop"]["vcl_inst"]->configure(array(
			"overview_func" => array($this,"get_overview"),
			"full_weeks" => 1,
		));

		$range = $arr["prop"]["vcl_inst"]->get_range(array(
			"date" => $arr["request"]["date"],
			"viewtype" => $arr["request"]["viewtype"],
		));

		$start = $range["start"];
		if ($range["overview_start"])
		{
			$start = $range["overview_start"];
		};

		$end = $range["end"];

		// event translations have the id of the object in original language
		$o = $arr["obj_inst"];
		$fx = $o->get_first_obj_by_reltype(RELTYPE_ORIGINAL);
		if ($fx)
		{
			$o = $fx;
		};

		$this->overview = array();

		$this->used = array();

		$parents = array($o->id());

		if (1 != $o->prop("skip_subproject_events"))
		{
			$this->_recurse_projects(0,$o->id());

			// create a list of all subprojects, so that we can show events from all projects
			if (is_array($this->prj_map))
			{
				foreach($this->prj_map as $key => $val)
				{
					foreach($val as $k1 => $v1)
					{
						$parents[$k1] = $k1;
					};
				};
			};
		};

		// aga vaat siin on mingi jama ..
		$ol = new object_list(array(
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"parent" => $parents,
					"project" => $arr["obj_inst"]->id(),
				)
			)),
			"sort_by" => "planner.start",
			"class_id" => $this->event_entry_classes,
			"CL_STAGING.start1" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $start),
		));
		//new object_list_filter(array("non_filter_classes" => CL_CRM_MEETING)),

		$ol2 = new object_list(array(
			"class_id" => CL_BUG,
			"deadline" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $start),
			"project" => $arr["obj_inst"]->id()
		));
		$ol->add($ol2);

		$req = get_ru();
		$disp = array();
		foreach($ol->arr() as $o)
		{
			$id = $o->id();
			if (isset($disp[$o->brother_of()]))
			{
				continue;
			}
			$disp[$o->brother_of()] = 1;

			if ($o->class_id() == CL_BUG)
			{
				$start = $o->prop("deadline") - ($o->prop("num_hrs_guess")*3600);
				$end = $o->prop("deadline");
			}
			else
			{
				$start = $o->prop("start1");
				$end = $o->prop("end");
			}

			$clid = $o->class_id();
			$link = $this->mk_my_orb("change",array("id" => $id,"return_url" => $req),$clid);

			$rows = safe_array($o->meta("rows"));
			if (!count($rows))
			{
				$t->add_item(array(
					"item_start" => $start,
					"item_end" => $end,
					"data" => array(
						"name" => $o->prop("name"),
						"modifiedby" => $o->modifiedby(),
						"modified" => $o->modified(),
						"created" => $o->created(),
						"createdby" => $o->createdby(),
						"icon" => icons::get_icon_url($o),
						"link" => $link,
					),
				));
			}
			else
			{
				foreach($rows as $row)
				{
					$link = $this->mk_my_orb("change",array("group" => "rows", "id" => $id,"return_url" => $req),$clid)."#row_".$row["date"];
					$t->add_item(array(
						"item_start" => $row["date"],
						"item_end" => $row["date"]+ 100,
						"data" => array(
							"name" => substr($row["task"], 0, 30),
							"modifiedby" => $o->modifiedby(),
							"modified" => $o->modified(),
							"created" => $o->created(),
							"createdby" => $o->createdby(),
							"icon" => icons::get_icon_url($o),
							"link" => $link,
						),
					));
				}
			}

			if ($start > $range["overview_start"])
			{
				// show event on all days it occurs and not only the first
				if ($start < $end)
				{
					for ($i = $start; $i <= $end; $i = $i + 86400)
					{
						$this->overview[$i] = 1;
					};
				};
			};
		};
	}

	function get_overview($arr = array())
	{
		return $this->overview;
	}


	////
	// !returns a list of events from the projects the user participates in
	// project_id (optional) - id of the project, if specified we get events
	// from that project only

	// XXX: split this into separate methods
	function get_event_folders($arr = array())
	{
		$ev_ids = array();
		if (!empty($arr["project_id"]))
		{
			#$ev_ids = $this->get_events_for_project(array("project_id" => $arr["project_id"]));
			$ev_ids = $arr["project_id"];
		}
		else
		if ($arr["type"] == "my_projects")
		{
			// this returns a list of events from "My projects"
			$users = get_instance("users");
			if (aw_global_get("uid"))
			{
				// see asi peab n&uuml;&uuml;d hakkama tagastame foldereid!
				$user_obj = new object($arr["user_ids"][0]);
				$conns = $user_obj->connections_to(array(
					"from.class_id" => CL_PROJECT,
				));
				// ei mingit bloody cyclet, see hakkab lihtsalt tagastame projektide id-sid, onj2!
				$ev_ids = array();
				foreach($conns as $conn)
				{
					$ev_ids[] = $conn->prop("from");
					//$ev_ids = array_merge($ev_ids,$this->get_events_for_project(array("project_id" => $conn->prop("from"))));
				};
			};
		};
		return $ev_ids;
	}

	////
	// !id - participant id
	function get_events_for_participant($arr = array())
	{
		$ev_ids = array();
		$projects = array();
		$obj = new object($arr["id"]);
		if ($obj->class_id() == CL_CRM_COMPANY)
		{
			$conns = $obj->connections_to(array(
				"reltype" => 2 //RELTYPE_PARTICIPANT,
			));
			foreach($conns as $conn)
			{
				$ev_ids = $ev_ids + $this->get_events_for_project(array(
					"project_id" => $conn->prop("from"),
					"class_id" => $arr["clid"],
				));
			};
		};

		return $ev_ids;
	}

	////
	// !Returns a list of event id-s for a given project
	function get_events_for_project($arr)
	{
		$pr_obj = new object($arr["project_id"]);
		$args = array(
			"type" => "RELTYPE_PRJ_EVENT",
		);

		if (!empty($arr["class_id"]))
		{
			$args["to.class_id"] = $arr["class_id"];
		};

		$event_connections = $pr_obj->connections_from($args);

		$ev_id_list = array();
		foreach($event_connections as $conn)
		{
			$ev_id_list[$conn->prop("to")] = $conn->prop("to");
		};

		// add all tasks that have project set
		$ol = new object_list(array(
			"class_id" => CL_TASK,
			"project" => $arr["project_id"],
			"lang_id" => array(),
			"site_id" => array(),
			"brother_of" => new obj_predicate_prop("id")
		));
		foreach($ol->ids() as $id)
		{
			$ev_id_list[$id] = $id;
		}
		return $ev_id_list;
	}

	/**
		@attrib name=wtf
	**/
	function wtf($arr)
	{
		aw_disable_acl();
		$ol = new object_list(array(
			"brother_of" => 10412,
			"lang_id" => array(),
		));
		echo dbg::dump($ol);

		arr($ol);
		foreach($ol->arr() as $o)
		{
			print "id = " . $o->id();
			print "prnt = " . $o->parent();
			print "lang = " . $o->lang();
			print "<br>";
		};

		aw_disable_acl();
		$ol = new object_list(array(
			"brother_of" => 5602,
			"lang_id" => array(),
		));
		echo dbg::dump($ol);

		foreach($ol->arr() as $o)
		{
			print "id = " . $o->id();
			print "prnt = " . $o->parent();
			print "<br>";
		};

		die();




		// and another, english should be activated for this
		// this is original and it has start1
		$o = new object(10083);
		arr($o->properties());

		// this one is translation and it does not have start1
		$o = new object(10085);
		arr($o->properties());

		die();

	}

	function get_event_sources($id)
	{
		$o = new object($id);
		$orig_conns = $o->connections_from(array(
			"type" => 103,
		));
		if (sizeof($orig_conns) > 0)
		{
			$first = reset($orig_conns);
			$id = $first->prop("to");
		};
		$sources = array($id => $id);
		if ($o->prop("skip_subproject_events") != 1)
		{
			$this->used = array();
			$this->_recurse_projects(0, $id);
		};
		if (is_array($this->prj_map))
		{
			foreach($this->prj_map as $key => $val)
			{
				foreach($val as $k1 => $v1)
				{
					$sources[$k1] = $k1;
				}
			}
		}
		return $sources;
	}

	function get_events($arr)
	{
		extract($arr);
		$o = new object($arr["id"]);
		$orig_conns = $o->connections_from(array(
			"type" => 103,
		));

		if (sizeof($orig_conns) > 0)
		{
			$first = reset($orig_conns);
			$arr["id"] = $first->prop("to");
		};

		$parents = array($arr["id"]);

		if (1 != $o->prop("skip_subproject_events"))
		{
			$this->used = array();
			$this->_recurse_projects(0,$arr["id"]);
		};

		if (is_array($this->prj_map))
		{
			// ah vitt .. see project map algab ju parajasti aktiivsest projektist.

			// aga valik "n2ita alamprojektide syndmusi" ei oma ju yleyldse mitte mingit m6tet
			// kui mul on vennad k6igis ylemprojektides ka
			foreach($this->prj_map as $key => $val)
			{
				// nii . aga nyyd ta n2itab mulle ju ka master projektide syndmusi .. which is NOT what I want

				// teisis6nu - mul ei ole syndmuste lugemisel vaja k6iki peaprojekte

				// kyll aga on vaja neid n2itamisel - et ma oskaksin kuvada asukohti. so there
				foreach($val as $k1 => $v1)
				{
					$parents[$k1] = $k1;
				};
			};
		};
		$limit_num = 300;

		$parent = join(",",$parents);

		$limit = "";
		if ($arr["range"]["limit_events"])
		{
			$limit = " LIMIT ".$arr["range"]["limit_events"];
			$limit_num = $arr["range"]["limit_events"];
		}

		// ma pean lugema syndmusi sellest projektist ja selle alamprojektidest.
		$_start = $arr["range"]["start"];
		/* this code is ev0l, we should outcomment it -- ahz
		if ($arr["range"]["overview_start"])
		{
			$_start = $arr["range"]["overview_start"];
		};
		*/
		$_end = $arr["range"]["end"];
		$lang_id = aw_global_get("lang_id");
		$stat_str = "objects.status != 0";

		if(is_array($arr["status"]))
		{
			$stat_str = "objects.status IN (".implode(",", $arr["status"]).")";
		}
		elseif($arr["status"] && aw_global_get("uid") == "")
		{
			$stat_str = "objects.status = " . $arr["status"];
		};

		$active_lang_only = aw_ini_get("project.act_lang_only");

		$q = "
			SELECT
				objects.oid AS id,
				objects.parent,
				objects.class_id,
				objects.brother_of,
				objects.name,
				planner.start,
				planner.end
			FROM planner
			LEFT JOIN objects ON (planner.id = objects.brother_of)
			WHERE ((planner.start >= '${_start}' AND planner.start <= '${_end}')
			OR
			(planner.end >= '${_start}' AND planner.start <= '${_end}')) AND
			$stat_str AND objects.parent IN (${parent}) order by planner.start"; // $limit

		if($arr["range"]["viewtype"] == "relative")
		{
			if($_GET["date"])
			{
				list($d, $m, $y) = split("-", $_GET["date"]);
				$_start = mktime(23, 59, 59, $m, $d, $y);
			}
			else
			{
				$_start = mktime(23, 59, 59, 12, 12, 2020);
			}
			$_start =
			$q = "
			SELECT
				objects.oid AS id,
				objects.parent,
				objects.class_id,
				objects.brother_of,
				objects.name,
				planner.start,
				planner.end
			FROM planner
			LEFT JOIN objects ON (planner.id = objects.brother_of)
			WHERE (planner.start - $_start) <= 0 AND
			$stat_str AND objects.parent IN (${parent}) order by ($_start - planner.start) LIMIT $limit_num";

		}





		// SELECT objects.oid AS id, objects.parent, objects.class_id, objects.brother_of, objects.name, planner.start, planner.end FROM planner LEFT JOIN objects ON (planner.id = objects.brother_of) WHERE ((planner.start >= '1099260000' AND planner.start <= '1104530399') OR (planner.end >= '1099260000' AND planner.end <= '1104530399')) AND objects.status != 0 AND objects.parent IN (2186)

		$this->db_query($q);
		$events = array();
		$pl = get_instance(CL_PLANNER);
		$ids = array();
		$projects = $by_parent = array();
		$lang_id = aw_global_get("lang_id");
		// weblingi jaoks on vaja kysida connectioneid selle projekti juurde!
		while($row = $this->db_next())
		{

			// now figure out which project this thing belongs to?
			//$web_page_id = $row["parent"];

			if (!$this->can("view",$row["brother_of"]))
			{
				//dbg::p1($row["name"]);
				//dbg::p1("skip1");
				continue;
			};

			$e_obj = new object($row["brother_of"]);
			// see leiab siis objekti originaali parenti
			$pr_obj = new object($e_obj->parent());


			if ($active_lang_only == 1 && $pr_obj->lang_id() != $lang_id)
			{
				dbg::p1($row["name"]);
				dbg::p1("skip2");

				continue;
			};

			$projects[$row["parent"]] = $row["parent"];

			$project_name = $pr_obj->name();

			// koostan nimekirja asjadest, mida mul vaja on? ja edasi on vaja
			// nimekirja piltidest


			$prid = $pr_obj->id();

			// mida fakki .. miks see asi NII on?
			$projects[$prid] = $prid;

			// 2kki ma saan siis siin ka kasutada seda tsyklite yhendamist?

			$eid = $e_obj->id();

			$event_parent = $e_obj->parent();
			$event_brother = $e_obj->brother_of();

			if (!($limit_counter >= ($limit_num) && $limit_num))
			{
				if (!isset($events[$event_brother]))
				{
					$limit_counter++;
				}
				$events[$event_brother] = array(
					"start" => $row["start"],
					"end" => $row["end"],
					"pr" => $prid,
					"name" => $e_obj->name(),
					"parent" => $event_parent,
					"comment" => $e_obj->comment(),
					"lang_id" => $e_obj->lang_id(),
					"id" => $eid,
					//"project_image" => $row["project_image"],
					"original_id" => $row["brother_of"],
					//"project_weblink" => aw_ini_get("baseurl") . "/" . $web_page_id,
					//"project_day_url" => aw_ini_get("baseurl") . "/" . $web_page_id . "?view=3&date=" . date("d-m-Y",$row["start"]),
					"project_name" => $project_name,
					"project_name_ucase" => strtoupper($project_name),
					"link" => $this->mk_my_orb("change",array(
						"id" => $eid,
					),$row["class_id"],true,true),
				);
			}
			$ids[$row["brother_of"]] = $row["brother_of"];
			$ids[$e_obj->brother_of()] = $e_obj->brother_of();

			$by_parent[$event_parent][] = $event_brother;


			/*if (++$limit_counter >= $limit_num && $limit_num)
			{
				break;
			}*/
		}


		$pr_list = new object_list(array(
			"class_id" => CL_PROJECT,
		));

		$pr_data = $pr_list->names();


		// now i have a list of all projects .. I need to figure out which menus connect to those projects
		$web_pages = $project_images = array();
		$c = new connection();

		$conns = $c->find(array(
			"from" => $projects,
			"type" => RELTYPE_ORIGINAL,
		));

		foreach($conns as $conn)
		{
			$from = $conn["from"];
			$to = $conn["to"];
			if (!is_oid($to) || !$this->can("view", $to))
			{
				continue;
			}
			$xto = new object($to);
			//$xtod = $xto->id();
			//if ($projects[$from])
			//{
				//unset($projects[$from]);
				$projects[$from] = $to;
				//$projects[$to] = $from;
			//};
		};


		// nii .. yhes6naga me diilime kogu aeg originaalprojektidega siin. eks?
		$conns = $c->find(array(
			"to" => $projects,
			"from.lang_id" => aw_global_get("lang_id"),
			"type" => 17,
		));

		$conns = $c->find(array(
			//"to" => $projects,
			//"to" => $projects,
			"from.lang_id" => aw_global_get("lang_id"),
			"from.class_id" => CL_MENU,
			"type" => 17,
		));

		foreach($conns as $conn)
		{
			$web_pages[$conn["to"]] = $conn["from"];
		}

		$lc = aw_global_get("LC");
		$current_charset = aw_global_get("charset");

		if (1 == $arr["project_media"])
		{
			$conns = $c->find(array(
				"from" => $projects,
				"type" => 11 //RELTYPE_PRJ_VIDEO,
			));

			foreach($conns as $conn)
			{

				$v_o = new object($conn["to"]);
				//$v_o = $conn->to();
				// aga miks siis see asi ei anna mulle t6lget 6iges keeles?
				$tmp = $v_o->properties();
				$tmp["media_id"] = $conn["to"];
				$tmp["name"] = $prop_val = iconv("UTF-8",$current_charset . "//TRANSLIT",$tmp["trans"][$lc]["name"]);
				//$tmp = array_merge($tmp,$tmp["trans"][$lc]);
				// video is always connected to the original project, but when showing
				// the event, I need to show the translated caption and not the original
				$project_videos[$conn["from"]][] = $tmp;

			};

			if (is_array($projects))
			{
				foreach($projects as $project_id)
				{
					$fx = $project_id;
					$fxo = new object($fx);
					// vat see koht siisn tegeleb remappimisega
					if ($project_videos[$fx])
					{
						$project_videos[$fxo->id()] = $project_videos[$fx];
					};
				};
			};
		}

		if (1 == $arr["first_image"])
		{
			$conns = $c->find(array(
				"from" => $projects,
				"type" => 8 //RELTYPE_PRJ_IMAGE,
			));

			$t_img = get_instance(CL_IMAGE);


			foreach($conns as $conn)
			{
				$project_images[$conn["from"]] = $t_img->get_url_by_id($conn["to"]);
			};

			$conns = $c->find(array(
				"from" => $ids,
				"type" => 1, // RELTYPE_PICTURE from CL_STAGING
			));


			foreach($conns as $conn)
			{
				$project_images[$conn["from"]] = $t_img->get_url_by_id($conn["to"]);
			};

			if (is_array($ids))
			{
				foreach($ids as $id)
				{
					$fx = $id;
					$fxo = new object($fx);
					// vat see koht siisn tegeleb remappimisega
					if ($project_images[$fx])
					{
						$project_images[$fxo->id()] = $project_images[$fx];
					};
				};
			};

		};

		$baseurl = aw_ini_get("baseurl");

		foreach($events as $key => $event)
		{
			$prid = $event["pr"];
			if ($projects[$prid])
			{
				$prid = $projects[$prid];
			};

			if ($web_pages[$prid])
			{
				$web_page_id = $web_pages[$prid];
				$events[$key]["project_weblink"] =  $baseurl . "/" . $web_page_id;
				$events[$key]["project_day_url"] = $baseurl . "/" . $web_page_id . "?view=3&date=" . date("d-m-Y",$event["start"]);
			};

			if ($web_pages[$event["pr"]])
			{
				$web_page_id = $web_pages[$event["pr"]];
				$events[$key]["project_weblink"] =  $baseurl . "/" . $web_page_id;
				$events[$key]["project_day_url"] = $baseurl . "/" . $web_page_id . "?view=3&date=" . date("d-m-Y",$event["start"]);
				//$events[$key]["project_name_ucase"] = $pr_data[$event["pr"]];
			};

			if ($project_images[$event["id"]])
			{
				$events[$key]["first_image"] = $project_images[$event["id"]];
			}
			else if ($project_images[$event["pr"]])
			{
				$events[$key]["first_image"] = $project_images[$event["pr"]];
			}
			else
			{
				$events[$key]["first_image"] = $baseurl . "/img/trans.gif";
			};

			if ($project_videos[$event["pr"]])
			{
				$events[$key]["media"] = $project_videos[$event["pr"]];

			};
		};

		if (sizeof($events) > 0)
		{
			$mpr = $this->get_master_project($o,$level);
			$this->prj_level = 1;

			$this->prj_levels[$mpr->id()] = $this->prj_level;
			$this->prj_level++;


			$this->used = array();
			$prj_levels = $this->prj_levels;


			$this->_recurse_projects2($mpr->id());


			// aaah, see on see bloody brother_list ju

			// iga eventi kohta on vaja teada k6iki vendi
			$ol = new object_list(array(
				"brother_of" => $ids,
				"lang_id" => array(),
			));



			// how does it work? Events will be assigned to multiple projects
			// by creating brothers in the event folders of the other projects

			// a tree is built from the projects. While I'm showing projects
			// I don't know on which level a particular project is nor what
			// the path of from the root project is

			// so I create a tree of all projects and assign a level number to
			// each.

			// then a list of all brothers of an event is created, which will
			// yield a list of project id's which is then matched against the
			// project level numbers - and this gives us the desired result

			$ox = $ol->arr();
			foreach($ox as $brot)
			{
				if (!$this->can("view", $brot->parent()))
				{
					continue;
				}
				// et siis teeme uue nimekirja k6igist objektidest, jees?
				$prnt = new object($brot->parent());
				$pid = $prnt->id();
				$prj_level = $this->_ptree[$pid];
				$orig = $brot->get_original();

				if ($prj_level)
				{
					$events[$orig->id()]["parent_" . $prj_level . "_name"] = $this->_pnames[$pid];
					$events[$orig->id()]["parent_" . $prj_level . "_oid"] = $this->_oids[$pid];
				};
			}
		}
		return $events;
	}

	////
	// !connects an event to a project
	// id - id of the project
	// event_id - id of the event
	function connect_event($arr)
	{
		$evt_obj = new object($arr["event_id"]);
		// create a brother under the project object
		$evt_obj->create_brother($arr["id"]);
	}

	////
	// !Disconnects and event from a project
	// id - id of the project
	// event_id - id of the event
	function disconnect_event($arr)
	{
		//print "disconnecting " . $arr["event_id"];
		#$evt_obj = new object($arr["event_id"]);
		#$evt_obj->delete();
		// deleting is broken now until I can figure out something
		//$evt_obj
		/*
		$prj_obj = new object($arr["id"]);
		$prj_obj->disconnect(array(
			"from" => $arr["event_id"],
		));
		*/
	}

	/**
		@attrib name=test_it_out all_args="1"

	**/
	function test_it_out($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
		));
		aw_set_exec_time(AW_LONG_PROCESS);
		for ($o = $ol->begin(); !$ol->end(); $o = $ol->next())
		{
			/*
			if ($o->id() != 87738)
			{
				continue;
			};
			*/
			$o->set_prop("skip_subproject_events",1);
			$o->save();
		};
		print "all done";
		exit;
		while (1 == 0)
		{
			$subs = new object_list(array(
				"parent" => $o->id(),
				"site_id" => array(),
			));
			for ($sub_o = $subs->begin(); !$subs->end(); $sub_o = $subs->next())
			{
				$orig = $sub_o->get_original();
				#print_r($sub_o);
				$sub2parent[$orig->id()] = $sub_o->id();
			};
			#arr($sub2parent);
			#arr($subs);
			$brother_parent = $o->id();
			// now I have to create brothers for each object
			print "projekt " . $o->name(). "<br>";
			print "id = " . $o->id() . "<br>";
			print "connections = ";
			$conns = $o->connections_from(array(
				"type" => "RELTYPE_PRJ_EVENT",
			));
			// create_brother
			print sizeof($conns);
			print "<br><br>";
			foreach($conns as $conn)
			{
				$to_obj = $conn->to();
				$tmp = $to_obj->get_original();
				$to_oid = $tmp->id();
				print "# ";
				print $conn->prop("reltype") . " ";
				print $to_obj->name() . " ";
				$p_obj = new object($to_obj->prop("parent"));
				print $p_obj->name() . "<bR>";
				// but first check, whether I already have an object with that parent!

				print_r($to_obj);
				if ($sub2parent[$to_oid])
				{
					print "brother already exists under $brother_parent<br>";
				}
				else
				{
					print "creating a brother under $brother_parent<br>";
				};
				print "<hr>";
				$to_obj->create_brother($brother_parent);
			};
			// I have to clone those, you know
			print "<br><br>";
		};
		print "oh, man, this is SO cool!";


	}

	function gen_event_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$this_o = $arr["obj_inst"];


		/* XXX: some specific event class adding buttons, not used, determine if neeeded at all
		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Uus"),
		));

		$int = $GLOBALS["relinfo"][$this->clid][3]; //RELTYPE_PRJ_EVENT

		$clinf = aw_ini_get("classes");

		foreach($clinf as $key => $val)
		{
			if (in_array($key,$int["clid"]))
			{
				$tb->add_menu_item(array(
					"parent" => "new",
					"text" => $val["name"],
					"link" => "link",
				));
			}
		}
		*/

		//$tb->add_separator();
		$tb->add_menu_button(array(
			"name" => "subprj",
			"img" => "new.gif",
			"tooltip" => t("Alamprojekt"),
		));

		// see nupp peaks kuvama ka alamprojektid

		$this->used = array();
		$this->prj_level = 0;
		$this->_recurse_projects(0, $this_o->id());

		$form_connections = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_PRJ_CFGFORM",
		));

		$forms = array();
		foreach($form_connections as $form_connection)
		{
			$forms[$form_connection->prop("to")] = $form_connection->prop("to.name");
		}

		$adds = array(CL_STAGING, CL_TASK, CL_CRM_CALL, CL_CRM_MEETING);
		$cl_name = aw_ini_get("classes.".CL_STAGING.".name");
		$create_args = array();

		if (false && is_array($this->prj_map))
		{ //XXX: determine if needed at all, and why the 'false'
			// how do I know that I'm dealing with first level items?
			foreach($this->prj_map as $parent => $items)
			{
				$level = 0;
				foreach($items as $prj_id)
				{
					$level++;
					// if first level projects are configured with skip_subproject_events off
					// then a brother of the added event is created under that first level
					// project
					$use_parent = $parent == 0 ? "subprj" : $parent;
					$pro = new object($prj_id);
					$tb->add_sub_menu(array(
						"name" => $prj_id,
						"parent" => $use_parent,
						"text" => $pro->name(),
					));

					if (1 == $pro->prop("skip_subproject_events"))
					{
						// do nothing
					};

					// but for this to work I also need to figure out the path
					// I'm in. How do I do that?

					// right then, I need a way to create links with correct parent
					// now - how do I do that?

					if (!$this->prj_map[$prj_id])
					{
						foreach($forms as $form_id => $form_name)
						{
							foreach($adds as $add_clid)
							{
								if ($add_clid == CL_TASK)
								{
									$pl = get_instance(CL_PLANNER);
									$this->cal_id = $pl->get_calendar_for_user(array(
										"uid" => aw_global_get("uid"),
									));

									$url = $this->mk_my_orb('new',array(
										'alias_to_org' => $arr["obj_inst"]->prop("orderer"),
										'reltype_org' => 13,
										'add_to_cal' => $this->cal_id,
										'title' => t("Toimetus"),
										'parent' => $arr["id"],
										'return_url' => get_ru(),
										"set_proj" => $arr["obj_inst"]->id()
									),CL_TASK);
									$tb->add_menu_item(array(
										"name" => "x_" . $prj_id . "_" . $form_id."_".$add_clid,
										"parent" => $prj_id,
										"text" => aw_ini_get("classes.{$add_clid}.name"),
										"link" => $url,
									));
								}
								else
								{
									$tb->add_menu_item(array(
										"name" => "x_" . $prj_id . "_" . $form_id."_".$add_clid,
										"parent" => $prj_id,
										"text" => aw_ini_get("classes.{$add_clid}.name"),
										"link" => $this->mk_my_orb("new",array(
											"parent" => $prj_id,
											"group" => "change",
										),$add_clid),
									));
								}
							}
						}
					}
				}
			}
		}
		else
		{
			$conns = $this_o->connections_from(array(
				"type" => "RELTYPE_PRJ_CFGFORM",
			));

			if (sizeof($conns) > 0)
			{
				foreach($conns as $conn)
				{
					$cobj = $conn->to();
					$tb->add_menu_item(array(
						"name" => "x_" . $cobj->id(),
						"parent" => "subprj",
						"text" => $cobj->name(),
						"link" => $this->mk_my_orb("new",array(
							"parent" => $this_o->id(),
							"group" => "change",
							"cfgform" => $cobj->id(),
							"clid" => $cobj->subclass(),
							"return_url" => get_ru(),
						),$cobj->subclass()),
					));
				}
			}
			else
			{
				foreach($adds as $add_clid)
				{
					if ($add_clid == CL_TASK)
					{
						$pl = get_instance(CL_PLANNER);
						$this->cal_id = $pl->get_calendar_for_user(array(
							"uid" => aw_global_get("uid"),
						));

						$ao = $arr["obj_inst"]->prop("orderer");
						if (is_array($ao))
						{
							$ao = reset($ao);
						}
						$url = $this->mk_my_orb('new',array(
							'alias_to_org' => $ao,
							'reltype_org' => 13,
							'add_to_cal' => $this->cal_id,
							'title' => t("Toimetus"),
							'parent' => $arr["obj_inst"]->id(),
							'return_url' => get_ru(),
							"set_proj" => $arr["obj_inst"]->id()
						), CL_TASK);
						$tb->add_menu_item(array(
							"name" => "x_" . $this_o->id()."_".$add_clid,
							"parent" => "subprj",
							"text" => aw_ini_get("classes.{$add_clid}.name"),
							"link" => $url,
						));
					}
					else
					{
						$tb->add_menu_item(array(
							"name" => "x_" . $this_o->id()."_".$add_clid,
							"parent" => "subprj",
							"text" => aw_ini_get("classes.{$add_clid}.name"),
							"link" => $this->mk_my_orb("new",array(
								"parent" => $this_o->id(),
								"group" => "change",
								"return_url" => get_ru(),
							),$add_clid),
						));
					}
				}
			}
		}

		// and now .. to the lowest level ... I need to add configuration forms .. or that other stuff

		//arr($this->prj_map);

		// obviuously peab lingis olema mingi lisaargument. Mille puudumisel omadust ei n2idata ..
		// ja mille eksisteerimisel kuvatakse korrektne vorm.

		// ja siin on nyyd see asi, et property pannakse eraldi tabi peale .. mis teeb asju veel
		// palju-palju raskemaks.

		// embedded form looks somewhat like a releditor .. but it can actually have multiple groups..
		// but now, when I think of that, a releditor might also want to use multiple groups

		// so how do I display those forms inside my form?
		//@reltype PRJ_EVENT value=3 clid=CL_TASK,CL_CRM_CALL,CL_CRM_OFFER,CL_CRM_DEAL,CL_CRM_MEETING

		/*
			1. how do I access that information
			2.



		*/


	}

	function _recurse_projects2($parent)
	{
		$prx = new object($parent);
		$parent = $prx->id();

		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_PROJECT,
			"to.class_id" => CL_PROJECT,
			"type" => 1,
		));

		$subs = array();
		$this->_pnames = array();
		$this->_fullnames = array();
		$saast = array();
		$this->_oids = array();
		foreach($conns as $conn)
		{
			$o1 = new object($conn["from"]);
			$o2 = new object($conn["to"]);
			$subs[$conn["from"]][$conn["to"]] = $conn["to"];
			$subs[$o1->id()][$o2->id()] = $o2->id();
			$this->_pnames[$conn["from"]] = $conn["from.name"];
			$this->_pnames[$conn["to"]] = $conn["to.name"];
			$this->_pnames[$o1->id()] = $o1->name();
			$this->_pnames[$o2->id()] = $o2->name();

			$this->_oids[$conn["from"]] = $conn["from"];
			$this->_oids[$conn["to"]] = $conn["to"];
			$this->_oids[$o1->id()] = $o1->id();
			$this->_oids[$o2->id()] = $o2->id();
		};

		$this->subs = $subs;

		$this->_ptree = array();
		$this->level = 0;

		$this->name_stack = array();
		$this->done = array();
		$this->_finalize_tree($parent);


	}

	function _finalize_tree($parent)
	{
		if (!$this->subs[$parent])
		{
			return false;
		}
		if ($this->done[$parent])
		{
			return false;
		};
		$this->done[$parent] = 1;

		$this->_ptree[$parent] = $this->level;
		$this->level++;

		foreach($this->subs[$parent] as $item)
		{
			$this->_finalize_tree($item);
			$this->_ptree[$item] = $this->level;
		};

		$this->level--;
	}

	// I need to build a tree of names ... HOW?
	// a 2 level array where the key is the name of the project with no children
	// and the value is an array of names of the parents

	// seega .. alustades yhest projektist leiame k6ik selle projekti alamprojektid
	// ma pean siis iga projekti kohta leidma et millisel tasemel ta on.

	////
	// !Gets a list of project id-s as an argument and creates a list of those in some $this variable
	// it should create a list of connections starting from those projects
	function _recurse_projects($parent,$prj_id)
	{
		if ($this->used[$parent])
		{
			return false;
		};
		//dbg::p1("111 recursing from " . $prj_id);
		//flush();
		$prj_obj = new object($prj_id);
		//dbg::p1("111 recursing from " . $prj_obj->name() . " / " . $prj_obj->id());
		//flush();

		/*
		$trans_conns = $prj_obj->connections_from(array(
			"type" => RELTYPE_ORIGINAL,
		));

		if (sizeof($trans_conns) > 0)
		{
			$first = reset($trans_conns);
			$prj_obj = new object($first->prop("to"));

		};
		*/

		//dbg::p1("recursing from " . $prj_obj->name());


		$prj_conns = $prj_obj->connections_from(array(
			"type" => "RELTYPE_SUBPROJECT",
		));
		foreach($prj_conns as $prj_conn)
		{
			$subprj_id = $prj_conn->prop("to");
			$to = $prj_conn->to();
			$this->prj_map[$parent][$subprj_id] = $subprj_id;
			$this->r_prj_map[$subprj_id] = $prj_id;
			$this->prj_levels[$subprj_id] = $this->prj_level;
			$this->prj_level++;
			//if (!$this->used[$subprj_id])
			//{
				$this->used[$subprj_id] = $subprj_id;
				$this->_recurse_projects($subprj_id,$subprj_id);
			//};
			$this->prj_level--;
		}
	}

	function callback_get_add_event($args = array())
	{
		// yuck, what a mess
		$obj = $args["obj_inst"];
		$meta = $obj->meta();

		$event_folder = $obj->id();

		// use the config form specified in the request url OR the default one from the
		// planner configuration
		$event_cfgform = $args["request"]["cfgform_id"];
		// are we editing an existing event?
		if (!empty($args["request"]["event_id"]))
		{
			$event_id = $args["request"]["event_id"];
			$event_obj = new object($event_id);
			if ($event_obj->is_brother())
			{
				$event_obj = $event_obj->get_original();
			};
			$event_cfgform = $event_obj->meta("cfgform_id");
			$this->event_id = $event_id;
			$clid = $event_obj->class_id();
			if ($clid == CL_DOCUMENT || $clid == CL_BROTHER_DOCUMENT)
			{
				unset($clid);
			};
		}
		else
		{
			if (!empty($args["request"]["clid"]))
			{
				$clid = $args["request"]["clid"];
			}
			elseif (is_oid($event_cfgform))
			{
				$cfgf_obj = new object($event_cfgform);
				$clid = $cfgf_obj->prop("subclass");
			};
		};

		$res_props = array();

		// nii - aga kuidas ma lahenda probleemi syndmuste panemisest teise kalendrisse?
		// see peaks samamoodi planneri funktsionaalsus olema. wuhuhuuu

		// no there are 3 possible scenarios.
		// 1 - if a clid is in the url, check whether it's one of those that can be used for enterint events
		//  	then load the properties for that
		// 2 - if cfgform_id is the url, let's presume it belongs to a document and load properties for that
		// 3 - load the default entry form ...
		// 4 - if that does not exist either, then return an error message

		if (isset($clid))
		{
			if (!in_array($clid,$this->event_entry_classes))
			{
				return array(array(
					"type" => "text",
					"value" => t("Seda klassi ei saa kasutada s&uuml;ndmuste sisestamiseks"),
				));
			}
			else
			{
				// 1 - get an instance of that class, for this I need to
				aw_session_set('org_action', aw_global_get('REQUEST_URI'));
				$clfile = basename(aw_ini_get("classes.{$clid}.file"));
				$t =  new $clfile();
				$t->init_class_base();
				$emb_group = "general";
				if ($this->event_id && $args["request"]["cb_group"])
				{
					$emb_group = $args["request"]["cb_group"];
				};
				$this->emb_group = $emb_group;

				$t->id = $this->event_id;

				$all_props = $t->get_property_group(array(
					"group" => $emb_group,
					"cfgform_id" => $event_cfgform,
				));

				$xprops = $t->parse_properties(array(
					"obj_inst" => $event_obj,
					"properties" => $all_props,
					"name_prefix" => "emb",
				));

				//$resprops = array();
				$resprops["capt"] = $this->do_group_headers(array(
					"t" => $t,
				));

				foreach($xprops as $key => $val)
				{
					$val["emb"] = 1;
					$resprops[$key] = $val;
				}

				$resprops[] = array("emb" => 1,"type" => "hidden","name" => "emb[class]","value" => basename($clfile));
				$resprops[] = array("emb" => 1,"type" => "hidden","name" => "emb[action]","value" => "submit");
				$resprops[] = array("emb" => 1,"type" => "hidden","name" => "emb[group]","value" => $emb_group);
				$resprops[] = array("emb" => 1,"type" => "hidden","name" => "emb[clid]","value" => $clid);
				$resprops[] = array("emb" => 1,"type" => "hidden","name" => "emb[cfgform]","value" => $event_cfgform);
				if ($this->event_id)
				{
					$resprops[] = array("emb" => 1,"type" => "hidden","name" => "emb[id]","value" => $this->event_id);
				};
			};
		}
		return $resprops;
	}

	function do_group_headers($arr)
	{
		$xtmp = $arr["t"]->groupinfo;
		$tmp = array(
			"type" => "text",
			"caption" => t("header"),
			"subtitle" => 1,
		);
		$captions = array();
		// still, would be nice to make 'em _real_ second level groups
		// right now I'm simply faking 'em
		// now, just add another
		foreach($xtmp as $key => $val)
		{
			if ($this->event_id && ($key != $this->emb_group))
			{
				$new_group = ($key == "general") ? "" : $key;
				$captions[] = html::href(array(
					"url" => aw_url_change_var("cb_group",$new_group),
					"caption" => $val["caption"],
				));
			}
			else
			{
				$captions[] = $val["caption"];
			};
		};
		$this->emb_group = $emb_group;
		$tmp["value"] = join(" | ",$captions);
		return $tmp;
	}

	function register_event_with_planner($args = array())
	{
		$event_folder = $args["obj_inst"]->id();
		$emb = $args["request"]["emb"];
		$is_doc = false;
		if (!empty($emb["clid"]))
		{
			$tmp = aw_ini_get("classes");
			$clfile = basename(aw_ini_get("classes.{$emb["clid"]}.file"));
			$t =  new $clfile();
			$t->init_class_base();
		}

		if (is_array($emb))
		{
			if (empty($emb["id"]))
			{
				$emb["parent"] = $event_folder;
			};
		};
		if (isset($emb["group"]))
		{
			$this->emb_group = $emb["group"];
		};

		if (!empty($emb["id"]))
		{
			$event_obj = new object($emb["id"]);
			$emb["id"] = $event_obj->brother_of();
		};

		$emb["return"] = "id";

		$this->event_id = $t->submit($emb);
		if (!empty($emb["id"]))
		{
			$this->event_id = $event_obj->id();
		};

		//I really don't like this hack //axel
		$gl = aw_global_get('org_action');

		// so this has something to do with .. connectiong some obscure object to another .. eh?

		// this deals with creating of one additional connection .. hm. I wonder whether
		// there is a better way to do that.

		// tolle uue objekti juurest luuakse seos 2sja loodud eventi juurde jah?

		// aga kui ma lisaks lihtsalt syndmuse isiku juurde?
		// ja see tekiks automaatselt parajasti sisse logitud kasutaja kalendrisse,
		// kui tal selline olemas on? See oleks ju palju parem lahendus.
		// aga kuhu kurat ma sellisel juhul selle syndmuse salvestan?
		// 2kki ma saan seda nii teha, et isiku juures yldse syndmust ei salvestata,
		// vaid broadcastitakse vastav message .. ja siis kalender tekitab selle syndmuse?

		preg_match('/alias_to_org=(\w*|\d*)&/', $gl, $o);
		preg_match('/reltype_org=(\w*|\d*)&/', $gl, $r);
		preg_match('/alias_to_org_arr=(.*)$/', $gl, $s);

		if (is_numeric($o[1]) && is_numeric($r[1]))
		{
			$org_obj = new object($o[1]);
			$org_obj->connect(array(
				"to" => $this->event_id,
				"reltype" => $r[1],
			));
			aw_session_del('org_action');
			if(strlen($s[1]))
			{
				$aliases = unserialize(urldecode($s[1]));
				foreach($aliases as $key=>$value)
				{
					$tmp_o = new object($value);
					$tmp_o->connect(array(
						'to' => $this->event_id,
						'reltype' => $r[1],
					));
				}
			}
			$params = array(
				"source_id" => $org_obj->id(),
				"event_id" => $this->event_id,
			);
			post_message_with_param(
				MSG_EVENT_ADD,
				$org_obj->class_id(),
				$params
			);
		}
		return PROP_OK;
	}

	function callback_mod_tab($args)
	{
		if ($args["activegroup"] !== "add_event" && $args["id"] === "add_event")
		{
			return false;
		}

		if ($args["id"] === "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}
		if ($args["id"] === "trans")
		{
			return false;
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	function callback_mod_retval(&$arr)
	{
		$args = &$arr["args"];
		if ($this->event_id)
		{
			$args["event_id"] = $this->event_id;
			if ($this->emb_group && $this->emb_group !== "general")
			{
				$args["cb_group"] = $this->emb_group;
			}
		}

		if(!empty($arr["request"]["hidden_team"]) && empty($args["team"]))
		{
			$args["team"] = $arr["request"]["hidden_team"];
		}

		if($this->use_group === "goals_edit")
		{
			$args["search_part"] = $arr["request"]["search_part"];
			$args["search_start"] = $arr["request"]["search_start"];
			$args["search_end"] = $arr["request"]["search_end"];
			$args["search_type"] = $arr["request"]["search_type"];
		}
		elseif($this->use_group === "files")
		{
			$args["files_find_name"] = $arr["request"]["files_find_name"];
			$args["files_find_type"] = $arr["request"]["files_find_type"];
			$args["files_find_comment"] = $arr["request"]["files_find_comment"];
		}
		elseif($this->use_group === "stats")
		{
			$args["files_find_name"] = $arr["request"]["files_find_name"];
			$args["files_find_type"] = $arr["request"]["files_find_type"];
			$args["files_find_comment"] = $arr["request"]["files_find_comment"];
		}

		if (isset($arr["request"]["team_search_person"])) $args["team_search_person"] = $arr["request"]["team_search_person"];
		if (isset($arr["request"]["team_search_co"])) $args["team_search_co"] = $arr["request"]["team_search_co"];
	}

	function callback_mod_reforb(&$arr, $request)
	{
		switch($arr["group"])
		{
			case "create_bill":
				$arr["bill_id"] = "";
				break;
		}

		$arr["post_ru"] = post_ru();
		$arr["implementor"] = "0";
		$arr["participants"] = "0";
		$arr["orderer"] = "0";
		$arr["tf"] = isset($request["tf"]) ? $request["tf"] : "";
		$arr["team"] = isset($request["team"]) ? $request["team"] : "";
		$arr["connect_orderer"] = isset($request["connect_orderer"]) ? $request["connect_orderer"] : "";
		$arr["connect_impl"] = isset($request["connect_impl"]) ? $request["connect_impl"] : "";
		$arr["prod_search_res"] = "0";
	}

	function request_execute($o)
	{
		$rv = "";
		$prj_id = $o->id();

		$prj_obj = $o;

		$obj = $o;


		$orig_conns = $o->connections_from(array(
			"type" => 103,
		));

		if (sizeof($orig_conns) > 0)
		{
			$first = reset($orig_conns);
			$prj_id = $first->prop("to");
			$prj_obj = $first->to();
		};


		$this->read_template("show.tpl");


		$cal_view = get_instance(CL_CALENDAR_VIEW);

		// XXX: make the view type configurable
		$views = array(
			3 => $this->vars["lc_day"],
			2 => $this->vars["lc_week"],
			1 => $this->vars["lc_month"],
			0 => $this->vars["lc_year"],
		);

		$view_from_url = aw_global_get("view");
		if (empty($view_from_url))
		{
			$view_from_url = 0;
		};

		if (!$views[$view_from_url])
		{
			$view_from_url = 0;
		};

		$use_template = "";
		if ($view_from_url == 0)
		{
			$use_template = "year";
			$viewtype = "year";
			$start_from = mktime(0,0,0,date("m"), 1, date("Y"));
		};

		if ($view_from_url == 1)
		{
			$use_template = "month";
			$viewtype = "month";
		};

		if ($view_from_url == 2)
		{
			$use_template = "weekview";
			$viewtype = "week";
		};

		if ($view_from_url == 3)
		{
			$use_template = "day";
			$viewtype = "day";
		};

		$project_obj = $obj;

		// no need for that .. I just get the type from url

		// argh .. projekti otse vaatamin on ikka paras sitt kyll

		$caldata = $cal_view->parse_alias(array(
			"obj_inst" => $project_obj,
			"use_template" => $use_template,
			"event_template" => "project_event.tpl",
			"viewtype" => $viewtype,
			"status" => STAT_ACTIVE,
			"skip_empty" => true,
			"full_weeks" => true,
			"start_from" => $start_from
		));

		$dt = aw_global_get("date");
		if (empty($dt))
		{
			$dt = date("d-m-Y");
		}

		$rg = date_calc::get_date_range(array(
			"type" => $viewtype,
			"date" => $dt,
		));

		// it is possible to attach a document containing detailed description of
		// the project to the project. If the connection is present show the document
		// in the web

		$lang_id = aw_global_get("lang_id");

		/*
			[15:17] <terryf_home> ongi sihuke kood
			[15:17] <terryf_home>   if ($arr["from"] && $arr["from.class_id"] && $arr["type"])
			[15:17] <terryf_home>   {
			[15:17] <terryf_home> siis t6lgib from 2ra
			[15:17] <terryf_home> ja muidu ei t6lgi
			[15:18] <terryf_home> and I haven't goot the faintest idea, miks see nii on
			[15:18] <terryf_home> ja mida see katki teeks kui ma selle 2ra muudan
			[15:18] <duke> oki, loen siis k6ik seosed ja v6tan ise need mis mul vaja on
		*/


		$c = new connection();
		$conns = $c->find(array(
			"from" => $prj_obj->id(),
			"from.class_id" => CL_PROJECT,
			//"type" => 7,
			"to.lang_id" => $lang_id,
		));

		/*
		$conns = $prj_obj->connections_from(array(
			"type" => "RELTYPE_PRJ_DOCUMENT",
			"to.lang_id" => aw_global_get("lang_id"),
		));
		*/

		$description = "";
		$first = true;
		if (is_array($conns))
		{
			foreach($conns as $conn)
			{
				if (!$first)
				{
					continue;
				}

				if ($conn["type"] != 7)
				{
					continue;
				}

				$t = new document();
				$description = $t->gen_preview(array(
					"docid" => $conn["to"],
					"leadonly" => -1,
				));

				$first = false;
			};
		};

		$view_navigator = "";


		foreach($views as $key => $val)
		{
			$this->vars(array(
				"text" => $val,
				"url" => aw_url_change_var("view",$key),
			));
			$tpl = ($view_from_url == $key) ? "ACTIVE_VIEW" : "VIEW";
			$view_navigator .= $this->parse($tpl);
		};

		$this->vars(array(
			"VIEW" => $view_navigator,
			"calendar" => $caldata,
			"prev" => aw_url_change_var("date",$rg["prev"]),
			"next" => aw_url_change_var("date",$rg["next"]),
			"description" => $description,
		));

		$rv =  $this->parse();
		return $rv;
	}

	/** Returns an array of subproject id-s, suitable for feeding to object_list

	**/

	function _get_subprojects($arr)
	{
		if (sizeof($arr["from"]) == 0)
		{
			return array();
		};
		$conn = new connection();
		$conns = $conn->find(array(
			"from" => $arr["from"],
			"from.class_id" => CL_PROJECT,
			//"from.lang_id" => aw_global_get("lang_id"),
			"type" => "RELTYPE_SUBPROJECT",
		));

		$res = array();
		if (is_array($conns))
		{
			foreach($conns as $conn)
			{
				// this way I should get the translated object
				//$to = new object($conn["to"]);
				$to = $conn["to"];
				//dbg::p1("created object instance is " . $to->name());
				//dbg::p1("created object instance is " . $to->lang_id());
				$from = $conn["from"];
				//$res[$to->id()] = $to->id();
				$res[$to] = $to;
			};
		};

		return $res;
	}

	function get_master_project($o, &$level)
	{
		$o2 = $o;
		$level = 0;
		$parent_selections = array();

		while ($o2 != false)
		{
			$level++;
			$sp = $o->connections_to(array(
				"type" => 1, // SUBPROJECT
				"from.class_id" => CL_PROJECT,
			));
			$first = reset($sp);
			if (is_object($first))
			{
				$o2 = $first->from();
				array_unshift($parent_selections,$o2->id());
			}
			else
			{
				$o2 = false;
			};
			$tmp = $o;
			$o = $o2;
		};

		return $tmp;
	}

	function get_event_overview($arr)
	{
		// saan ette project id, alguse ja l6pu
		$rv = array();
		$ol = new object_list(array(
			"parent" => $arr["id"],
			"sort_by" => "planner.start",
			new object_list_filter(array("non_filter_classes" => CL_CRM_MEETING)),
			new obj_predicate_compare(OBJ_COMP_IN_TIMESPAN,array("start1", "end"), array($arr["start"], $arr["end"]))
		));

		foreach($ol->arr() as $o)
		{
			$id = $o->id();
			$dstart = (int)($o->prop("start1") / 86400);
			$dend = (int)($o->prop("end") / 86400);
			$rv[] = array(
				"url" => "/" . $o->id(),
				"start" => $o->prop("start1"),
			);
			if ($dend > $dstart)
			{
				for ($i = $dstart + 1; $i <= $dend; $i = $i + 1)
				{
					$rv[] = array(
						"url" => "/" . $o->id(),
						"start" => $i * 86400,
					);
				}
			}
		}
		return $rv;
	}

	function _goal_tb($arr)
	{
		$t = $arr["prop"]["toolbar"];
		$tf = isset($arr["request"]["tf"]) ? $arr["request"]["tf"] :  0;

		$t->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa")
		));
		/*$t->add_menu_item(array(
			"name" => "new_goal",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_PROJECT_GOAL,
				is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : $arr["obj_inst"]->id(),
				array("return_url" => get_ru())
			),
			"text" => t("Verstapost"),
		))*/;
		$ord = $arr["obj_inst"]->prop("orderer");
		if (is_array($ord))
		{
			$ord = reset($ord);
		}
		$t->add_menu_item(array(
			"name" => "new_event",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_TASK,
				$arr["obj_inst"]->id(),
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $ord,
					"set_proj" => $arr["obj_inst"]->id(),
					"set_pred" => $tf
				)
			),
			"text" => t("Toimetus"),
		));
		$t->add_menu_item(array(
			"name" => "new_call",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_CRM_CALL,
				$arr["obj_inst"]->id(),
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $ord,
					"set_proj" => $arr["obj_inst"]->id(),
					"set_pred" => $tf
				)
			),
			"text" => t("K&otilde;ne"),
		));
		$t->add_menu_item(array(
			"name" => "new_meeting",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_CRM_MEETING,
				$arr["obj_inst"]->id(),
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $ord,
					"set_proj" => $arr["obj_inst"]->id(),
					"set_pred" => $tf
				)
			),
			"text" => t("Kohtumine"),
		));

		$t->add_menu_item(array(
			"name" => "new_bug",
			"parent" => "new",
			"link" => html::get_new_url(
				CL_BUG,
				$arr["obj_inst"]->id(),
				array(
					"return_url" => get_ru(),
					"alias_to_org" => $ord,
					"set_proj" => $arr["obj_inst"]->id(),
					"set_pred" => $tf
				)
			),
			"text" => t("Arendus&uuml;lesanne"),
		));

		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));

		$t->add_separator();
		$t->add_button(array(
			"name" => "cut",
			"img" => "cut.gif",
			"action" => "cut_goals",
			"tooltip" => t("L&otilde;ika"),
		));

		if (!empty($_SESSION["proj_cut_goals"]) and is_array($_SESSION["proj_cut_goals"]))
		{
			$t->add_button(array(
				"name" => "paste",
				"img" => "paste.gif",
				"action" => "paste_goals",
				"tooltip" => t("Kleebi"),
			));
		}
	}

	function _get_bills_tree($arr)
	{
		$tv = $arr["prop"]["vcl_inst"];
		$bill_state_count = $arr["obj_inst"]->get_bill_state_count();
		$var = "st";
		if(!isset($arr["request"][$var]))
		{
			$arr["request"][$var] = 10;
		}

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_tree",
		));

		$bills_inst = get_instance(CL_CRM_BILL);
		$states = $bills_inst->states + array("90" => t("K&otilde;ik"));
		foreach($states as $stat_id => $state)
		{
			if($bill_state_count[$stat_id])
			{
				$state.= " (".$bill_state_count[$stat_id].")";
			}
			if (isset($arr["request"][$var]) && $arr["request"][$var] == $stat_id+10)
			{
				$state = "<b>".$state."</b>";
			}
			$tv->add_item(0,array(
				"name" => $state,
				"id" => $stat_id+10,
				"url" => aw_url_change_var($var, $stat_id+10),
			));
		}
	}

	function _get_bills_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$show_payment_info = (!isset($arr["request"]["st"]) || $arr["request"]["st"] > 10)? 1 : 0;

		$t->define_field(array(
			"name" => "bill_no",
			"caption" => t("Number"),
			"sortable" => 1,
			"numeric" => 1
		));

		$t->define_field(array(
			"name" => "bill_date",
			"caption" => t("Kuup&auml;ev"),
		));

		$t->define_field(array(
			"name" => "bill_due_date",
			"caption" => t("Makset&auml;htaeg"),
		));

		if($show_payment_info)
		{
			$t->define_field(array(
				"name" => "payment_date",
				"caption" => t("Laekumiskuup&auml;ev"),
			));
		}

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right"
		));

		if($show_payment_info)
		{
			$t->define_field(array(
				"name" => "balance",
				"caption" => t("Arve saldo"),
				"sortable" => 1,
				"numeric" => 1,
				"align" => "right"
			));
			$t->define_field(array(
				"name" => "paid",
				"caption" => t("Laekunud"),
				"sortable" => 1,
				"numeric" => 1,
				"align" => "right"
			));

			$t->define_field(array(
				"name" => "late",
				"caption" => t("Hilinenud p&auml;evi"),
				"sortable" => 1,
				"numeric" => 1,
				"align" => "right"
			));
		}

		if(isset($arr["request"]["st"]) && $arr["request"]["st"] == 100)
		{
			$t->define_field(array(
				"name" => "state",
				"caption" => t("Staatus"),
				"sortable" => 1
			));
		}

		$t->define_field(array(
			"name" => "print",
			"caption" => t("Prindi"),
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));

		if(!isset($arr["request"]["st"]))
		{
			return;
		}

		$bill_params = (isset($arr["request"]["st"]) && $arr["request"]["st"] != 100) ? array("status" =>  $arr["request"]["st"] - 10) : null;
		$bills = $arr["obj_inst"]->get_bills($bill_params);
		$bill_i = get_instance(CL_CRM_BILL);
		$curr_inst = get_instance(CL_CURRENCY);
		$co_stat_inst = get_instance("applications/crm/crm_company_stats_impl");
		$company_curr = $co_stat_inst->get_company_currency();
		$sum_in_curr = $bal_in_curr = array();
		$balance = $cg = 0;//$cg - currency grouping... if there are different currencies
		$bills_inst = get_instance(CL_CRM_BILL);
		$bills_inst->states;


		foreach($bills->arr() as $bill)
		{
			$cm = $partial = "";
			$payments_total = 0;

			$cursum = $own_currency_sum = $bill_i->get_bill_sum($bill,$tax_add);
			$curid = $bill->get_bill_currency_id();
			$cur_name = $bill->get_bill_currency_name();

			if($company_curr && $curid && ($company_curr != $curid))
			{
				$cg = 1;
			}

			if($cg)//mitmes valuutas
			{
				$own_currency_sum  = $co_stat_inst->convert_to_company_currency(array(
					"sum" =>  $cursum,
					"o" => $bill,
				));
				$sum_str = number_format($cursum, 2)." ".$cur_name;
				$sum_in_curr[$cur_name] += $cursum;
			}
			else//ainult oma organisatsiooni valuutas
			{
				$sum_str = number_format($own_currency_sum, 2);
			}

			if($bill->prop("state") == 3 && $bill->prop("partial_recieved") && $bill->prop("partial_recieved") < $cursum)
			{
				$partial = '<br>'.t("osaliselt");
			}

			if(isset($arr["request"]["st"]) && $arr["request"]["st"] == 100)
			{
				$state = $bills_inst->states[$bill->prop("state")];
			}

			$bill_data = array(
				"bill_no" => html::get_change_url($bill->id(), array("return_url" => get_ru()), parse_obj_name($bill->prop("bill_no"))),
				"bill_date" => date("d.m.Y" , $bill->prop("bill_date")),
				"bill_due_date" => date("d.m.Y" , $bill->prop("bill_due_date")),
				"state" => $state.$partial,
				"sum" => $sum_str,
				"oid" => $bill->id(),
				"print" => $bill->get_bill_print_popup_menu(),
			);

			//laekunud summa
			if($payments_sum = $bill->get_payments_sum())
			{
				$bill_data["paid"] = number_format($payments_sum,2);
			}

			//hilinenud
			if(($bill->prop("state") == 1 || $bill->prop("state") == 6 || $bill->prop("state") == -6) && $bill->prop("bill_due_date") < time())
			{
				$bill_data["late"] = (int)((time() - $bill->prop("bill_due_date")) / (3600*24));
			}

			//laekumiskuup2ev
			if($payment_date = $bill->get_last_payment_date())
			{
				$bill_data["payment_date"] = date("d.m.Y" , $payment_date);
			}

			$curr_balance = $bill->get_bill_needs_payment();
			if($cg)
			{
				$total_balance = $own_currency_sum;
				foreach($bill->connections_from(array("type" => "RELTYPE_PAYMENT")) as $conn)
				{
					$p = $conn->to();
					if($p->prop("currency_rate") && $p->prop("currency_rate") != 1)
					{
						$total_balance -= $p->get_free_sum($bill->id()) / $p->prop("currency_rate");
					}
					else
					{
						$total_balance -= $curr_inst->convert(array(
							"from" => $curid,
							"to" => $company_curr,
							"sum" => $p->get_free_sum($bill->id()),
							"date" =>  $p->prop("date"),
						));
					}
				}
			}
			else
			{
				$total_balance = $curr_balance;
			}

			if($cg)
			{
				$bill_data["balance"] = number_format($curr_balance, 2)." ". $bill->get_bill_currency_name();
				$bal_in_curr[$cur_name] += $curr_balance;
			}
			else
			{
				$bill_data["balance"] = number_format($total_balance, 2);
			}
			$balance += $total_balance;

			$t->define_data($bill_data);
			$sum+= number_format($own_currency_sum,2,".", "");
		}

		$t->set_default_sorder("desc");
		$t->set_default_sortby("bill_no");
		$t->sort_by();
		$t->set_sortable(false);

		$final_dat = array(
			"bill_no" => t("<b>Summa</b>")
		);

		if($cg)
		{
			foreach($sum_in_curr as $cur_name => $amount)
			{
				$final_dat["sum"] .= "<b>".number_format($amount, 2)." ".$cur_name."</b><br>";
				if($arr["request"]["show_bill_balance"])
				{
					$final_dat["balance"] .= "<b>".number_format($bal_in_curr[$cur_name], 2)." ".$cur_name."</b><br>";
				}
			}
			$co_currency_name = "";
			if($this->can("view" , $company_curr))
			{
				$company_curr_obj = obj($company_curr);
				$co_currency_name = $company_curr_obj->name();
			}
			$final_dat["sum"] .= "<b>Kokku: ".number_format($sum, 2).$co_currency_name."</b><br>";

			if($show_payment_info)
			{
				$final_dat["balance"] .= "<b>Kokku: ".number_format($balance, 2).$co_currency_name."</b><br>";
			}
		}
		else
		{
			$final_dat["sum"] = "<b>".number_format($sum, 2)."</b>";
			$final_dat["balance"] .= "<b>".number_format($balance, 2)."</b><br>";
		}
		$t->define_data($final_dat);
	}

	function _get_bills_tb($arr)
	{
		$_SESSION["create_bill_ru"] = get_ru();
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Lisa'),
			'url' => html::get_new_url(CL_CRM_BILL, $arr["obj_inst"]->id(), array(
				"return_url" => get_ru(),
				"project" => $arr["obj_inst"]->id(),
			))
		));
		$tb->add_delete_button();
	}



	function _get_create_bill_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
/*
		$tb->add_button(array(
			'name' => 'new',
			'img' => 'new.gif',
			'tooltip' => t('Lisa'),
			'url' => html::get_new_url(CL_CRM_BILL, $arr["obj_inst"]->id(), array("return_url" => get_ru() , "project" => $arr["obj_inst"]->id()))
		));
*/
		$tb->add_button(array(
			'name' => 'create_bill',
			'img' => 'save.gif',
			'tooltip' => t('Loo arve'),
			'action' => "create_bill",
		));
		$bills = $arr["obj_inst"]->get_bills(array("status" =>  0));
		if($bills->count())
		{
			$tb->add_menu_button(array(
				"name" => "add_to_bill",
				"img" => "search.gif",
				"tooltip" => t("Lisa olemasolevale arvele"),
			));

			foreach($bills->arr() as $bill)
			{
				$tb->add_menu_item(array(
					"parent" => "add_to_bill",
					"text" => $bill->name(),
					"link" =>"javascript:var asd = document.getElementsByName('bill_id');
						asd[0].value=".$bill->id().";
						submit_changeform('create_bill');",
				));
			}
		}
	}

	/**
		@attrib name=create_bill all_args=1
	**/
	function create_bill($arr)
	{
		foreach($arr as $k => $v)
		{
			if (substr($k, 0, 3) === "sel")
			{
				foreach($v as $v_id)
				{
					$arr["sel"][$v_id] = $v_id;
				}
			}
		}

		//klientide kontroll ka vaja
		$project = obj($arr["id"]);
		if(isset($arr["bill_id"]) && $this->can("view", $arr["bill_id"]))
		{
			$bill = obj($arr["bill_id"]);
		}
		elseif(isset($_SESSION["bill_id"]) && $this->can("view", $_SESSION["bill_id"]))
		{
			$bill = obj($_SESSION["bill_id"]);
			unset($_SESSION["bill_id"]);
		}
		else
		{
			$bill = $project->add_bill();
		}
		$bill->add_rows(array(
			"objects" => $arr["sel"],
		));
		$create_bill_ru = html::get_change_url($arr["id"], array("group" => "create_bill"));
		return html::get_change_url($bill->id(),array("return_url" => $create_bill_ru,));
	}

	function callback_mod_layout(&$arr)
	{
		switch($arr["name"])
		{
//			case "bills_left":
//				$arr["area_caption"] = sprintf(t("%s arved staatuste kaupa"), $arr["obj_inst"]->name());
//				break;
			case "task_types_search_lay":
				$arr["area_caption"] = sprintf(t("Otsingu parameetrid"));
				break;
			case "task_types_tree_lay":
				$arr["area_caption"] = sprintf(t("Tegevused t&uuml;&uuml;pide kaupa"));
				break;
			case "task_table":
				$arr["area_caption"] = sprintf(t("Projekti %s tegevused"), $arr["obj_inst"]->name());
				break;
			case "create_bill_table":
				$arr["area_caption"] = sprintf(t("Projekti %s tehtud arveta t&ouml;&ouml;de nimekiri"), $arr["obj_inst"]->name());
				break;
			case "bills_r":
				$var = 10;
				if(isset($arr["request"]["st"]))
				{
					$var = $arr["request"]["st"];
				}
				if($var == 14)
				{
					$state = t("Krediit");
				}
				elseif($var == 15)
				{
					$state = t("Tehtud krediit");
				}
				else
				{
					$bills_inst = get_instance(CL_CRM_BILL);
					$states = $bills_inst->states + array("90" => t("K&otilde;ik"));
					$state = $states[$var-10]." ";
				}
				$arr["area_caption"] = sprintf(t("Projekti %s %sarved"), $arr["obj_inst"]->name(), strtolower($state));
				break;
		}
		return true;
	}

	function callback_generate_scripts($arr)
	{
		$sc = "";
		$sc.= "
			function openall()
			{
				var allElements = document.getElementsByName(\"bug_comments_table\");
				len = allElements.length;
				for (i=0; i < len; i++)
				{
					el=document.getElementsByName(\"bug_comments_table\")[i];
					if (navigator.userAgent.toLowerCase().indexOf(\"msie\")>=0){
						if(el.style.display == \"block\")
							{ d = \"none\";}
						else { d = \"block\";} }
					else {
						if (el.style.display == \"table-row\") {
							d = \"none\";
						}
						else {d = \"table-row\";}
					}
					el.style.display=d;
				}
			}";
		return $sc;

	}

	function _get_work_list($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$t->define_field(array(
			"caption" => t("<a href='javascript:void(0)' onclick='openall();'>Ava</a>"),
			"name" => "open",
			"align" => "center",
//			"sortable" => 1
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"caption" => t("Juhtumi nimi"),
			"name" => "name",
			"align" => "center",
//			"sortable" => 1
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"caption" => t("Tunde"),
			"name" => "hrs",
			"align" => "right",
//			"sortable" => 1
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"caption" => t("Tunde kliendile"),
			"name" => "hrs_cust",
			"align" => "right",
//			"sortable" => 1
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"caption" => t("Tunni hind"),
			"name" => "hr_price",
			"align" => "right",
//			"sortable" => 1
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"caption" => t("Summa"),
			"name" => "sum",
			"align" => "right",
//			"sortable" => 1
			"chgbgcolor" => "color"
		));
/*
		$t->define_field(array(
			"caption" => t("Arvele m&auml;&auml;ramise kuup&auml;ev"),
			"name" => "set_date",
			"align" => "right",
//			"sortable" => 1,
//			"type" => "time",
//			"format" => "d.m.Y"
		));
*/

		$t->define_field(array(
			"caption" => t("tegevuse kuup&auml;ev"),
			"name" => "date",
			"align" => "right",
//			"sortable" => 1,
//			"type" => "time",
//			"format" => "d.m.Y"
			"chgbgcolor" => "color"
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel",
			"chgbgcolor" => "color"
		));

		$rows = new object_list();
		$sum = 0;//summa
		$hrs = 0;//tunde
		$hrs_cust = 0;//tunde kliendile
		$this->stats = get_instance("applications/crm/crm_company_stats_impl");

		$deal_tasks = $arr["obj_inst"]->get_billable_deal_tasks();
		$deal_tasks_ids = $deal_tasks->ids();

		foreach($deal_tasks->arr() as $deal_task)
		{
			$row = obj($deal_task);
			$t->define_data(array(
				"oid" => $row->id(),
				"name" => $row->name(),
				"sum" => $row->prop("deal_price").t("(Kokkuleppehind)").($row->prop("deal_has_tax") ? t("KMga") : ""),
				"set_date" => $row->prop("to_bill_date") ? date("d.m.Y" , $row->prop("to_bill_date")) : "",
				"date" => $row->prop("start1") ? date("d.m.Y" , $row->prop("start1")) : "",
				"hrs" => $this->stats->hours_format($row->prop("deal_amount")),
				"hrs_cust" => $this->stats->hours_format($row->prop("deal_amount")),
			));
			$sum += $row->prop("deal_price");
			$hrs += $row->prop("deal_amount");
			$hrs_cust += $row->prop("deal_amount");
		}

		foreach($arr["obj_inst"]->get_billable_expenses()->arr() as $row)
		{
			$date = $row->prop("date");
			$t->define_data(array(
				"oid" => $row->id(),
				"name" => $ro->name(),
				"sum" => number_format(str_replace(",", ".", $row->prop("cost")),2),
				"date" => date("d.m.Y" , mktime(0,0,0, $date["month"], $date["day"], $date["year"])),
			));
			$sum += $row->prop("cost");
		}

		foreach($arr["obj_inst"]->get_billable_task_rows()->arr() as $row)
		{
			if(!in_array($row->prop("task"), $deal_tasks_ids))
			{
				$hr_price = $row->prop("task.hr_price");
				$t->define_data(array(
					"oid" => $row->id(),
					"name" =>  html::obj_change_url($row , $row->prop("content") ? htmlspecialchars($row->prop("content")) : t("...") , array("group" => "rows")),
					"hrs_cust" => $this->stats->hours_format($row->prop("time_to_cust")),
					"hrs" => $this->stats->hours_format($row->prop("hours_real")),
					"hr_price" => number_format($hr_price,2),
					"sum" => number_format($row->prop("time_to_cust") * $hr_price,2),
					"set_date" => date("d.m.Y" , $row->prop("to_bill_date")),
					"date" => date("d.m.Y" , $row->prop("date")),
				));
			}
			$sum += $row->prop("time_to_cust") * $hr_price;
			$hrs_cust += $row->prop("time_to_cust");
			$hrs += $row->prop("hours_real");
		}

		$bugs = $arr["obj_inst"]->get_billable_bugs();
		$this->hour_prices = array();
		$this->bug_hours = array();
		$this->bug_real_hours = array();

		$colors = array(
			3 => "#99FF66",
			4 => "#99FF66",
			5 => "#99FF66",
			6 => "#99FF66",
			7 => "#99FF66",
			8 => "#99FF66",
			9 => "#99FF66",
			11 => "red",
			12 => "#99FF66",
		);

		$ready = array(3,4,5,6,7,8,9,12);

		foreach($bugs->arr() as $bug)
		{
			$lister = "<span id='bug".$bug->id()."' name=bug_comments_table style='display: none;'>";
			$table = new vcl_table;
			$table->name = "bug".$bug->id();
			$params = array(
				"request" => array(
					"bug" => $bug->id(),
				),
				"prop" => array(
					"vcl_inst" => $table
				)
			);

			$this->_get_bug_row_list($params);
			$lister .= $table->draw();
			$lister .= "</span>";
			$this->hour_prices[$bug->id()] = $bug->prop("hr_price");
			$bug_data = array(
				"open" => html::href(array(
					"url" => "javascript:void(0)", //aw_url_change_var("proj", $p),
					"onClick" => "el=document.getElementById(\"bug".$bug->id()."\"); if (navigator.userAgent.toLowerCase().indexOf(\"msie\")>=0){if (el.style.display == \"block\") { d = \"none\";} else { d = \"block\";} } else { if (el.style.display == \"table-row\") {  d = \"none\"; } else {d = \"table-row\";} }  el.style.display=d;",
					"caption" => t("Ava")
				)),
				"hr_price" => number_format($this->hour_prices[$bug->id()],2),
				"set_date" => date("d.m.Y" , ($bug->prop("to_bill_date"))),
				"date" => date("d.m.Y" , $this->bug_start[$bug->id()]) . " - ".date("d.m.Y" , $this->bug_end[$bug->id()]),
				"oid" => $bug->id(),
			);
			if($colors[$bug->prop("bug_status")])
			{
				$bug_data["color"] = $colors[$bug->prop("bug_status")];
			}
			$bug_data["name"] = html::href(array(
				"caption" => $bug->name() ? htmlspecialchars($bug->name()) : t("..."),
				"url" => html::obj_change_url($bug , array()))).$lister;
			$bug_data["hrs"] = $this->stats->hours_format($this->bug_real_hours[$bug->id()]);
			$bug_data["hrs_cust"] = $this->stats->hours_format($this->bug_hours[$bug->id()]);
			$bug_data["sum"] = number_format(($this->bug_hours[$bug->id()] * $this->hour_prices[$bug->id()]),2);
			$sum += $this->bug_hours[$bug->id()] * $this->hour_prices[$bug->id()];
			$hrs += $this->bug_real_hours[$bug->id()];
			$hrs_cust += $this->bug_hours[$bug->id()];//arr($bug->id()); arr($bug_data);
			$t->define_data($bug_data);
		}

		$t->define_data(array(
			"open" => t("Kokku:"),
			"hrs" => $this->stats->hours_format($hrs),
			"hrs_cust" => $this->stats->hours_format($hrs_cust),
			"sum" => number_format($sum,2),
		));

	}

	private function _init_bug_row_list($t, $bug)
	{
		$t->define_field(array(
			"caption" => t("Sisu"),
			"name" => "comment",
			"align" => "center",
		));

		$t->define_field(array(
			"caption" => t("T&ouml;&ouml;tunde"),
			"name" => "time",
			"align" => "right",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("Tunde kliendile"),
			"name" => "time_cust",
			"align" => "right",
			"sortable" => 1
		));

		$t->define_field(array(
			"caption" => t("tegevuse kuup&auml;ev"),
			"name" => "date",
			"align" => "right",
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel".$bug
		));
	}

	private function _get_bug_row_list($arr)
	{
		if(!($bug = obj($arr["request"]["bug"])))
		{
			return "";
		}

		$t = $arr["prop"]["vcl_inst"];
		$project =& $arr["request"]["project"];
		$t->unset_filter();
		$this->_init_bug_row_list($t,$arr["request"]["bug"]);
		$this->bug_start[$arr["request"]["bug"]] = time();
		$this->bug_end[$arr["request"]["bug"]] = $bug->prop("end");
		$comments = $bug->get_billable_comments();
		foreach($comments->arr() as $comment)
		{
			$capt = substr($comment->prop("content"), 0 , 300);
			$hours = $comment->bill_hours();
			$this->bug_hours[$arr["request"]["bug"]] += $hours;
			$this->bug_real_hours[$arr["request"]["bug"]] += $comment->prop("time_real");

			if($comment->prop("date") > $this->bug_start[$arr["request"]["bug"]])
			{
				$this->bug_end[$arr["request"]["bug"]] = $comment->prop("date");
			}
			if($comment->prop("date") < $this->bug_start[$arr["request"]["bug"]])
			{
				$this->bug_start[$arr["request"]["bug"]] = $comment->prop("date");
			}
			$t->define_data(array(
				"comment" => html::href(array(
					"caption" => $capt ? htmlspecialchars($capt) : t("..."),
					"url" => html::obj_change_url($comment , array()))),
				"time_cust" => $this->stats->hours_format($comment->bill_hours()),
				"time" => $this->stats->hours_format($comment->prop("time_real")),
				"oid" => $comment->id(),
				"date" => date("d.m.Y" , $comment->prop("date")),
			));
		}
	}

	function _task_types_tree($arr)
	{
		$act = isset($arr["request"]["tf"]) ? $arr["request"]["tf"] : 0;
		$tv = $arr["prop"]["vcl_inst"];
		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_task_types",
		));

		$types = array(
			CL_BUG => "Arendus&uuml;lesanded",
			CL_TASK => t("Toimetused"),
			CL_CRM_MEETING => t("Kohtumised"),
			CL_CRM_CALL => t("K&otilde;ned"),
			1 => t("K&ouml;ik"),
		);

		$bug_inst = get_instance(CL_BUG);
		$bugs_data = $arr["obj_inst"]->get_bugs_data();
		$bugs_count = array();
		foreach($bugs_data as $bd)
		{
			if (isset($bugs_count[$bd["bug_status"]]))
			{
				$bugs_count[$bd["bug_status"]]++;
			}
			else
			{
				$bugs_count[$bd["bug_status"]] = 1;
			}
		}

		$tasks_data = $arr["obj_inst"]->get_all_tasks_data();
		$tasks_count = array();
		foreach($tasks_data as $bd)
		{
			if (isset($tasks_count[$bd["class_id"]][$bd["is_done"]]))
			{
				$tasks_count[$bd["class_id"]][$bd["is_done"]] ++;
			}
			else
			{
				$tasks_count[$bd["class_id"]][$bd["is_done"]] = 1;
			}
		}

		$count = 0;
		$count += array_sum($bugs_count);
		$count += array_sum($tasks_count);

		foreach($types as $clid => $name)
		{
			if ($clid == CL_BUG)
			{
				$name = $name." (".array_sum($bugs_count).")";
			}

			if (!empty($tasks_count[$clid]))
			{
				$name = $name." (".array_sum($tasks_count[$clid]).")";
			}

			if($clid == 1)
			{
				$name = $name." (".$count.")";
			}

			$tv->add_item(0,array(
				"name" => $clid == $act ? "<b>".$name."</b>" : $name,
				"id" => $clid,
				"url" => aw_url_change_var("tf", $clid),
			));
		}

		//bugi staatuste kaupa
		foreach($bug_inst->bug_statuses as $stat_id => $caption)
		{
			if(!empty($bugs_count[$stat_id]))
			{
				$caption = $caption." (".$bugs_count[$stat_id].")";
			}
			$tf = CL_BUG."_".$stat_id;
			$tv->add_item(CL_BUG,array(
				"name" => $act == $tf ? "<b>".$caption."</b>" : $caption,
				"id" => $tf,
				"url" => aw_url_change_var("tf", $tf),
			));
		}

		//taskid valmis ja mitte
		$clid = CL_TASK;
		$tf = $clid."_0";
		$nm = t("Tegemata");
		if(!empty($tasks_count[$clid][0]))
		{
			$nm = $nm." (".$tasks_count[$clid][0].")";
		}

		$tv->add_item($clid,array(
			"name" => $act == $tf ? "<b>".$nm."</b>" :$nm ,
			"id" => $tf,
			"url" => aw_url_change_var("tf", $tf),
		));
		$tf = $clid."_1";
		$nm = t("Valmis");
		if(!empty($tasks_count[$clid][8]))
		{
			$nm = $nm." (".$tasks_count[$clid][8].")";
		}
		$tv->add_item($clid,array(
			"name" => $act == $tf ? "<b>".$nm."</b>" : $nm,
			"id" => $tf,
				"url" => aw_url_change_var("tf", $tf),
		));

		//kohtumised valmis ja mitte
		$clid = CL_CRM_MEETING;
		$tf = $clid."_0";
		$nm = t("Tulekul");
		if(!empty($tasks_count[$clid][0]))
		{
			$nm = $nm." (".$tasks_count[$clid][0].")";
		}
		$tv->add_item($clid,array(
			"name" => $act == $tf ? "<b>".$nm."</b>" : $nm,
			"id" => $tf,
				"url" => aw_url_change_var("tf", $tf),
		));
		$tf = $clid."_1";
		$nm = t("L&otilde;pppenud");
		if(!empty($tasks_count[$clid][8]))
		{
			$nm = $nm." (".$tasks_count[$clid][8].")";
		}
		$tv->add_item($clid,array(
			"name" => $act == $tf ? "<b>".$nm."</b>" : $nm,
			"id" => $tf,
				"url" => aw_url_change_var("tf", $tf),
		));

		//kohtumised valmis ja mitte
		$clid = CL_CRM_CALL;
		$tf = $clid."_0";
		$nm = t("Plaanis olevad");
		if(!empty($tasks_count[$clid][0]))
		{
			$nm = $nm." (".$tasks_count[$clid][0].")";
		}
		$tv->add_item($clid,array(
			"name" => $act == $tf ? "<b>".$nm."</b>" : $nm,
			"id" => $tf,
				"url" => aw_url_change_var("tf", $tf),
		));
		$tf = $clid."_1";
		$nm = t("Tehtud");
		if(!empty($tasks_count[$clid][8]))
		{
			$nm = $nm." (".$tasks_count[$clid][8].")";
		}
		$tv->add_item($clid,array(
			"name" => $act == $tf ? "<b>".$nm."</b>" : $nm,
			"id" => $tf,
				"url" => aw_url_change_var("tf", $tf),
		));



/*

		$ol = new object_list(array(
			"class_id" => array(CL_TASK,CL_CRM_CALL,CL_CRM_MEETING),
//			"project" => $arr["obj_inst"]->id(),
			"CL_TASK.RELTYPE_PROJECT.id" => $arr["obj_inst"]->id(),
			"is_goal" => 1,
//			"lang_id" => 1,
			"brother_of" => new obj_predicate_prop("id")
		));
		$ids = $this->make_keys($ol->ids());
		// now make tree, based on predicate tasks



		foreach($ol->arr() as $o)
		{
			$nm = parse_obj_name($o->name());
			if ($arr["request"]["tf"] == $o->id())
			{
				$nm = "<b>".$nm."</b>";
			}

			$pt = $o->prop("predicates");
			if (is_array($pt))
			{
				$pt = $this->make_keys($pt);
				unset($pt["0"]);
				$pt = reset($pt);
			}
			if (!$this->can("view", $pt))
			{
				$pt = $arr["obj_inst"]->id();
			}
			if (!isset($ids[$pt]))
			{
				$pt = $arr["obj_inst"]->id();
			}
			$tv->add_item($pt, array(
				"name" => $nm,
				"id" => $o->id(),
				"url" => aw_url_change_var("tf", $o->id()),
				"iconurl" => icons::get_icon_url(CL_MENU)
			));
		}*/

	}

	function _goal_tree($arr)
	{
		$ol = new object_list(array(
			"class_id" => array(CL_TASK,CL_CRM_CALL,CL_CRM_MEETING),
//			"project" => $arr["obj_inst"]->id(),
			"CL_TASK.RELTYPE_PROJECT.id" => $arr["obj_inst"]->id(),
			"is_goal" => 1,
//			"lang_id" => 1,
			"brother_of" => new obj_predicate_prop("id")
		));
		$ids = $this->make_keys($ol->ids());
		// now make tree, based on predicate tasks
		$tv = $arr["prop"]["vcl_inst"];
		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_goal_t",
		));
		$tv->add_item(0,array(
			"name" => parse_obj_name($arr["obj_inst"]->name()),
			"id" => $arr["obj_inst"]->id(),
			"url" => aw_url_change_var("tf", null),
		));
		foreach($ol->arr() as $o)
		{
			$nm = parse_obj_name($o->name());
			if ($arr["request"]["tf"] == $o->id())
			{
				$nm = "<b>".$nm."</b>";
			}

			$pt = $o->prop("predicates");
			if (is_array($pt))
			{
				$pt = $this->make_keys($pt);
				unset($pt["0"]);
				$pt = reset($pt);
			}
			if (!$this->can("view", $pt))
			{
				$pt = $arr["obj_inst"]->id();
			}
			if (!isset($ids[$pt]))
			{
				$pt = $arr["obj_inst"]->id();
			}
			$tv->add_item($pt, array(
				"name" => $nm,
				"id" => $o->id(),
				"url" => aw_url_change_var("tf", $o->id()),
				"iconurl" => icons::get_icon_url(CL_MENU)
			));
		}
	}

	function _init_goal_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "impl",
			"caption" => t("Osalejad"),
			"align" => "center",
			"sortable" => 1
		));


		$t->define_field(array(
			"name" => "start1",
			"caption" => t("Algus"),
			"align" => "center",
/*			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
*/		));

		$t->define_field(array(
			"name" => "end",
			"caption" => t("L&otilde;pp"),
			"align" => "center",
/*			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
*/		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _goal_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_goal_table($t);

//		$parent = is_oid($arr["request"]["tf"]) ? $arr["request"]["tf"] : new obj_predicate_compare(OBJ_COMP_NULL);


		if(isset($arr["request"]["tf"]))
		{
			$tf = explode("_" , $arr["request"]["tf"]);
			switch($tf[0])
			{
				case CL_BUG:
					$tasks = $arr["obj_inst"]->get_bugs(array("status" => $tf[1]));
					break;

				case CL_TASK:
					$tasks = $arr["obj_inst"]->get_tasks(array("done" => $tf[1]));
					break;
				case CL_CRM_MEETING:
					$tasks = $arr["obj_inst"]->get_meetings(array("done" => $tf[1]));
					break;
				case CL_CRM_CALL:
					$tasks = $arr["obj_inst"]->get_calls(array("done" => $tf[1]));
					break;
				case 1:
					$tasks = $arr["obj_inst"]->get_goals();
					break;
				default:
					$tasks = new object_list();
			}
		}
		else
		{
			$tasks = new object_list();

			if(!empty($arr["request"]["search_part"]) || !empty($arr["request"]["search_start"]) || !empty($arr["request"]["search_end"]) || !empty($arr["request"]["search_type"]))
			{
				$search = array();
				if(!empty($arr["request"]["search_part"]))
				{
					$search["participant"] = $arr["request"]["search_part"];
				}

				if(!empty($arr["request"]["search_start"]))
				{
					$search["from"] = date_edit::get_timestamp($arr["request"]["search_start"]);
				}

				if(!empty($arr["request"]["search_end"]))
				{
					$search["to"] = date_edit::get_timestamp($arr["request"]["search_end"]);
				}

				if(empty($arr["request"]["search_type"]) || !empty($arr["request"]["search_type"][CL_BUG]))
				{
					$tasks->add($arr["obj_inst"]->get_bugs($search));
				}

				if(empty($arr["request"]["search_type"]) || !empty($arr["request"]["search_type"][CL_TASK]))
				{
					$tasks->add($arr["obj_inst"]->get_tasks($search));
				}

				if(empty($arr["request"]["search_type"]) || !empty($arr["request"]["search_type"][CL_CRM_MEETING]))
				{
					$tasks->add($arr["obj_inst"]->get_meetings($search));
				}

				if(empty($arr["request"]["search_type"]) || !empty($arr["request"]["search_type"][CL_CRM_CALL]))
				{
					$tasks->add($arr["obj_inst"]->get_calls($search));
				}
			}
		}


//		$goals = new object_list();

		//$goals = $arr["obj_inst"]->get_goals($parent);

		foreach($tasks->arr() as $goal)
		{
			$goal_data = array(
				"name" => html::href(array(
					"url" => html::get_change_url($goal->id()),
					"caption" => $goal->name(),
					"target" => "_blank"
				)),
				"oid" => $goal->id(),
				"class" => $goal->class_id()
			);
			switch($goal->class_id())
			{
				case CL_CRM_MEETING:
				case CL_TASK:
				case CL_CRM_CALL:
					$goal_data["impl"] = join(", " ,$goal->get_participants()->names());
					$goal_data["start1"] = $goal->prop("start1") ? date("d.m.Y H:i",  $goal->prop("start1")) : "";
					$goal_data["end"] = $goal->prop("end") ? date("d.m.Y H:i",  $goal->prop("end")) : "";
					break;
				case CL_BUG:
					$goal_data["start1"] = date("d.m.Y H:i",  $goal->created());
					$goal_data["impl"] = join(", " ,$goal->get_participants()->names());
					break;
			}


			$t->define_data($goal_data);
		}
//		$t->data_from_ol($goals, array("change_col" => "name"));*/
	}

	/**

		@attrib name=del_goals

	**/
	function del_goals($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array("oid" => $arr["sel"]));
			$ol->delete();
		}

		return $arr["post_ru"];
	}

	function _goals_gantt($arr)
	{
		$units = isset($arr["request"]["units"]) ? $arr["request"]["units"] : "";
		$columns = isset($arr["request"]["column_n"]) ? $arr["request"]["column_n"] : "";
		if(!$columns) $columns = 10;
		$time =  time();
		$this_object = $arr["obj_inst"];
		$chart = new gantt_chart();

		//k6igepealt default v22rtused ... mis siis muutuvad kui tegu on kuude v6i n2dalatega
		$subdivisions = 1;
		$subdivisions = ((int)6/$columns)*4;
		$days = array ("P", "E", "T", "K", "N", "R", "L");
		$column_length = 86400;

		if($units === "months")
		{
			$days = array (t("Jaanuar"), t("Veebruar"), t("M&auml;rts"), t("Aprill"), t("Mai"), t("Juuni"), t("Juuli"), t("August"), t("September"), t("Oktoober"), t("November"), t("Detsember"));
			$subdivisions = 1;
			$subdivisions = (int)(10/$columns)*3;
			$column_length = 86400*30.5;
		}
		elseif($units === "weeks")
		{
			$days = array();
			$x = 0;
			while($x<54)
			{
				$days[$x] = $x.'. '.t("N&auml;dal");
				$x++;
			}
			$subdivisions = (int)(4/$columns)*7;
			$column_length = 86400*7;
		}

		// get all goals/tasks
	/*	$ot = new object_tree(array(
			"parent" => $arr["obj_inst"]->id(),
			"class_id" => array(CL_PROJECT_GOAL,CL_TASK),
		));*/
//		$gt_list = $ot->to_list();
		$gt_list = $arr["obj_inst"]->get_goals();

		$range_start = 2000000000;
		$range_end = 0;
		foreach($gt_list->arr() as $gt)
		{
			switch($gt->class_id())
			{
				case CL_CRM_MEETING:
				case CL_TASK:
				case CL_CRM_CALL:
					$range_start = min($gt->prop("start1"), $range_start);
					$range_end = max($gt->prop("end"), $range_end);

					break;
				case CL_BUG:
					$range_start = min($gt->created(), $range_start);
					$range_end = max($gt->created(), $range_end);
					break;
			}
		}

		if (!empty($arr["request"]["start"])) $range_start = $arr["request"]["start"];

		foreach($gt_list->arr() as $gt)
		{
			$chart->add_row (array (
				"name" => $gt->id(),
				"title" => $gt->name(),
				"uri" => html::get_change_url(
					$gt->id(),
					array("return_url" => get_ru())
				)
			));
		}

		foreach ($gt_list->arr() as $gt)
		{
			switch($gt->class_id())
			{
				case CL_CRM_MEETING:
				case CL_TASK:
				case CL_CRM_CALL:
					$start = $gt->prop ("start1");
					$length = $gt->prop("end") - $start;
					$title = $gt->name()."<br>( ".date("d.m.Y H:i", $start)." - ".date("d.m.Y H:i", $gt->prop("end"))." ) ";
					break;

				case CL_BUG:
					$start = $gt->created();
					$length = $gt->num_hrs_real * 3600;
					$title = $gt->name()."<br>( ".date("d.m.Y H:i", $start)." - ".date("d.m.Y H:i", $start + $length)." ) ";
					break;
			}


			$bar = array (
				"id" => $gt->id (),
				"row" => $gt->id (),
				"start" => $start,
				"length" => $length,
				"title" => $title
			);

			$chart->add_bar ($bar);
		}

		$chart->configure_chart (array (
			"chart_id" => "proj_gantt",
			"style" => "aw",
			"start" => $range_start,
			"end" => $range_end,
			"columns" => $columns,
			"subdivisions" => $subdivisions,
		//	"timespans" => $subdivisions,
			"width" => 950,
			"row_height" => 10,
			"column_length" => $column_length,
		));

		### define columns
		$i = 0;
		while ($i < $columns)
		{
			$day_start = ($range_start + ($i * 86400));
			$day = date ("w", $day_start);
			$date = date ("j/m/Y", $day_start);
			$title = $days[$day] . " - " . $date;
			if($units === "weeks")
			{
				$day_start = ($range_start + ($i * 86400*7));
				$day = (int)date ("W", $day_start);
				$date = date ("j/m/Y", $day_start);
				$date.= " - " .date ("j/m/Y", $day_start+ 86400*6);
				$title = $days[$day] . " " . $date;
			}
			elseif($units === "months")
			{
				$day_start = ($range_start + ($i * 86400*30.5));
				$day = (int)date ("m", $day_start) - 1;
				$date = date ("m/Y", $day_start);
				$title = $days[$day] . " " . $date;
			}
			$uri = aw_url_change_var ("mrp_chart_length", 1);
			$uri = aw_url_change_var ("mrp_chart_start", $day_start, $uri);
			$chart->define_column (array (
				"title" => $title,
				"col" => ($i + 1),
				"uri" => $uri,
			));
			$i++;
		}
		$links = $this->gen_gantt_header_links(array(
			"column_n" => $columns,
			"id" => $arr["request"]["id"],
			"units" => $units,
			"start" => $range_start,
		));

		return $links.'<br>'.$chart->draw_chart ();
	}

	function gen_gantt_header_links($args)
	{
		extract($args);
		$next = $start;
		$last = $start;
		$columns = 10;
		if(!$column_n) $column_n = $columns;
		if($units == "days")
		{
			$columns = 7;
			if(!$column_n) $column_n = $columns;
			$last = $last - 86400*$column_n;
			$next = $next + 86400*$column_n;
		}
		if($units == "weeks")
		{
			$columns = 8;
			if(!$column_n) $column_n = $columns;
			$last = $last - 86400*7*$column_n;
			$next = $next + 86400*7*$column_n;
		}
		if($units == "months")
		{
			$columns = 6;
			if(!$column_n) $column_n = $columns;
			$last = $last - 86400*30.5*$column_n;
			$next = $next + 86400*30.5*$column_n;
		}
		$links = "";
		$x = 0;
		$links.= html::href(array(
			"url" => html::get_change_url(
				$id,
				array(
					"id" => $id,
					"start" => $last,
					"group" => "goals_gantt",
					"units" => $units,
					"column_n" => $columns,
					"return_url" => get_ru(),
					"previous" => 1,
					"column_n" => $column_n,
				)
			),
			"caption" => t("<< Eelmine"),
		));

		while ($x < $columns)
		{
			if($column_n == $x+1)
			{
				$links.= " <b>". ($x+1)."</b>";
			}
			else
			{
				$url =  html::get_change_url(
						$id,
						array(
						"id" => $id,
							"start" => $start,
							"group" => "goals_gantt",
							"column_n" => $x+1,
							"units" => $units,
							"return_url" => get_ru(),
						)
				);
				$links.= " ".html::href(array(
					"url" => $url,
					"caption" => $x+1,
				));
			}
			$x++;
		}

		if($units == "days")
		{
			$links.= " <b>".t("P&auml;evad")."</b>";
		}
		else
		{
			$links.= " ".html::href(array(
				"url" => html::get_change_url(
					$id,
					array(
						"id" => $id,
						"start" => $start,
						"group" => "goals_gantt",
						"units" => "days",
						"return_url" => get_ru(),
					)
				),
				"caption" => t("P&auml;evad"),
			));
		}

		if($units == "weeks")
		{
			$links.= " <b>". t("N&auml;dalad")."</b>";
		}
		else
		{
			$links.= " ".html::href(array(
				"url" => html::get_change_url(
					$id,
					array(
						"start" => $start,
						"id" => $id,
						"group" => "goals_gantt",
						"units" => "weeks",
						"return_url" => get_ru(),
					)
				),
				"caption" => t("N&auml;dalad"),
			));
		}

		if($units == "months")
		{
			$links.= " <b>". t("Kuud")."</b>";
		}
		else
		{
			$links.= " ".html::href(array(
				"url" => html::get_change_url(
					$id,
					array(
						"id" => $id,
						"start" => $start,
						"group" => "goals_gantt",
						"units" => "months",
						"return_url" => get_ru(),
					)
				),
				"caption" => t("Kuud"),
			));
		}

		$links.= " ".html::href(array(
			"url" => html::get_change_url(
				$id,
				array(
					"id" => $id,
					"start" => $next,
					"group" => "goals_gantt",
					"units" => $units,
					"column_n" => $columns,
					"return_url" => get_ru(),
					"after" => 1,
					"column_n" => $column_n,
				)
			),
			"caption" => t("J&auml;rgmine >>"),
		));
		return $links;
	}

	function generate_html($o, $item)
	{
		return "";
	}

	function callback_post_save($arr)
	{
		// write implementor and orderer
		if (!$this->can("view", $arr["obj_inst"]->prop("implementor")))
		{
			$imp = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_IMPLEMENTOR");
			if ($imp)
			{
				$arr["obj_inst"]->set_prop("implementor", $imp->id());
				$save = true;
			}
		}
		if (!$this->can("view", $arr["obj_inst"]->prop("orderer")))
		{
			$imp = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_ORDERER");
			if ($imp)
			{
				$arr["obj_inst"]->set_prop("orderer", $imp->id());
				$save = true;
			}
		}

		if ($save)
		{
			$arr["obj_inst"]->save();
		}

		if ($this->do_create_task == 1)
		{
			$this->_create_task($arr);
		}

		if($arr["request"]["participants"])
		{
			$ps = new popup_search();
			$ps->do_create_rels($arr['obj_inst'], $arr["request"]["participants"], RELTYPE_PARTICIPANT);
		}
		if(substr_count($arr["request"]["return_url"] , "action=new") && (substr_count($arr["request"]["return_url"] , "class=crm_task") || substr_count($arr["request"]["return_url"] , "class=crm_call") || substr_count($arr["request"]["return_url"] , "class=crm_meeting")))
		{
			$_SESSION["add_to_task"]["project"] = $arr["obj_inst"]->id();
		}
	}

	function _mk_tbl()
	{
		$this->db_query("
			create table aw_projects(
				aw_oid int primary key,
				aw_state int,
				aw_start int,
				aw_end int,
				aw_deadline int,
				aw_doc int,
				aw_skip_subproject_events int,
				aw_project_navigator int,
				aw_use_template int,
				aw_doc_id int,
				aw_user1 varchar(255),
				aw_user2 varchar(255),
				aw_user3 varchar(255),
				aw_user4 varchar(255),
				aw_user5 varchar(255),
				aw_userch1 int,
				aw_userch2 int,
				aw_userch3 int,
				aw_userch4 int,
				aw_userch5 int
		)");
		$q = "SELECT * FROM objects WHERE class_id = ".CL_PROJECT." AND status > 0";
		$this->db_query($q);
		aw_disable_acl();
		while($row = $this->db_next())
		{
			$this->save_handle();
			$this->db_query("INSERT INTO aw_projects(aw_oid) values(".$row["oid"].")");
			$o = obj($row["oid"]);
			$pl = $o->get_property_list();
			foreach($pl as $pn => $pd)
			{
				if ($pd["table"] == "aw_projects")
				{
					flush();
					$o->set_prop($pn, $o->meta($pn));
				}
			}
			$o->save();

			$this->restore_handle();
		}
		aw_restore_acl();
	}

	function _proc_cp($ord, &$data)
	{
		$res = array();
		if ($ord)
		{
			$wl = array();
			$i = get_instance(CL_CRM_COMPANY);
			$i->get_all_workers_for_company($ord, $wl);
			if (count($wl))
			{
				$ol = new object_list(array("oid" => $wl));
				$res = array("" => t("--Vali--")) + $ol->names();
			}
		}
		$data["options"] = $res;
	}

	function _sides_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		// add, search, del
		$tb->add_menu_button(array(
			"name" => "add",
			"img" => "new.gif",
			"tooltip" => t("Lisa"),
		));
		$tb->add_menu_item(array(
			"parent" => "add",
			"text" => t("Isik"),
			"link" => html::get_new_url(
				CL_CRM_PERSON,
				$arr["obj_inst"]->id(),
				array(
					"return_url" => get_ru(),
					"alias_to" => $arr["obj_inst"]->id(),
					"reltype" => 15 // RELTYPE_SIDE
				)
			)
		));
		$tb->add_menu_item(array(
			"parent" => "add",
			"text" => t("Organisatsioon"),
			"link" => html::get_new_url(
				CL_CRM_COMPANY,
				$arr["obj_inst"]->id(),
				array(
					"return_url" => get_ru(),
					"alias_to" => $arr["obj_inst"]->id(),
					"reltype" => 15 // RELTYPE_SIDE
				)
			)
		));

		$tb->add_button(array(
			"name" => "search",
			"img" => "search.gif",
			"url" => "javascript:void(0)",
			"onClick" => html::popup(array(
				"url" => $this->mk_my_orb("pop_side_search", array(
					"id" => $arr["obj_inst"]->id()
				)),
				"target" => "pss",
				"height" => 400,
				"width" => 400,
				"quote" => "'",
				"scrollbars" => 1,
				"no_link" => 1,
				//"no_return" => true
			)),
			"tooltip" => t("Otsi"),
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_sides",
			"tooltip" => t("Kustuta"),
		));
	}

	function _init_sides_t($t)
	{
		$t->define_field(array(
			"name" => "side",
			"caption" => t("Vastaspool"),
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _sides($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_sides_t($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SIDE")) as $c)
		{
			$t->define_data(array(
				"side" => html::get_change_url($c->prop("to"), array("return_url" => get_ru()), $c->prop("to.name")),
				"oid" => $c->prop("to")
			));
		}
		$t->set_sortable(false);
	}

	/**
		@attrib name=del_sides
	**/
	function del_sides($arr)
	{
		$o = obj($arr["id"]);
		foreach(safe_array($arr["sel"]) as $id)
		{
			$o->disconnect(array(
				"from" => $id
			));
			/*					"onClick" => "aw_popup_scroll('".$search_url."','_spop',600,500)",*/

		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=pop_side_search
	**/
	function pop_side_search($arr)
	{
		$h = get_instance("cfg/htmlclient");
		$h->start_output();

		$els = array(
			"s_name" => array("caption" => t("Nimi"), "type" => "textbox", "size" => 30),
			"s_class_id" => array("caption" => t("T&uuml;&uuml;p"), "type" => "select", "options" => array("" => "", CL_CRM_PERSON => t("Isik"), CL_CRM_COMPANY => t("Organisatsioon"))),
		);

		foreach($els as $k => $v)
		{
			$v["name"] = $k;
			$v["value"] = $_GET[$k];

			$h->add_property($v);
		}

		$h->put_submit(array());
		$h->finish_output(array(
			"method" => "GET",
			"action" => "pop_side_search",
			"data" => array(
				"orb_class" => "project",
				"id" => $_GET["id"]
			),
			"sbt_caption" => t("Otsi")
		));
		$html = $h->get_result();

		$content = $this->_get_pop_s_res_t($arr);
		$content .= html::submit(array(
			"value" => t("Vali")
		));
		$content .= $this->mk_reforb("save_pop_s_res", array("id" => $_GET["id"]));


		$html .= html::form(array(
			"method" => "POST",
			"action" => "orb.aw",
			"content" => $content
		));

		return $html;
	}

	function _get_pop_s_res_t($arr)
	{
		get_instance("vcl/table");
		$t = new vcl_table();

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));

		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));

		if ($_GET["MAX_FILE_SIZE"])
		{
			$ol = new object_list(array(
				"class_id" => $_GET["s_class_id"] ? $_GET["s_class_id"] : array(CL_CRM_COMPANY, CL_CRM_PERSON),
				"name" => "%".$_GET["s_name"]."%",
				"lang_id" => array(),
				"site_id" => array()
			));
			$t->data_from_ol($ol);
		}

		return $t->draw();
	}

	/**
		@attrib name=save_pop_s_res
	**/
	function save_pop_s_res($arr)
	{
		$o = obj($arr["id"]);
		foreach(safe_array($arr["sel"]) as $id)
		{
			$o->connect(array(
				"to" => $id,
				"reltype" => "RELTYPE_SIDE"
			));
		}
		return aw_ini_get("baseurl")."automatweb/closewin.html";
	}
/*
	function _get_team($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_team_t($t);

		$p = get_instance(CL_CRM_PERSON);

		$from = $arr["obj_inst"]->prop("implementor");
		if (is_array($from))
		{
			$from = reset($from);
		}
		$to = $arr["obj_inst"]->prop("orderer");
		if (is_array($to))
		{
			$to = reset($to);
		}

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PARTICIPANT")) as $c)
		{
			$o = $c->to();

			if ($o->class_id() == CL_USER)
			{
				$i = $o->instance();
				$o = obj($i->get_person_for_user($o));
			}

			$co = $p->get_all_employers_for_person($o);
			$co_s = array();
			if (count($co))
			{
				foreach($co as $co_oid)
				{
					$co_s[] = html::obj_change_url(obj($co_oid));
				}
			}
			else
			{
				$empl = $o->get_first_obj_by_reltype("RELTYPE_WORK");
				if ($empl)
				{
					$co_s[] = html::obj_change_url($empl);
				}
			}

			if ($o->class_id() == CL_CRM_COMPANY)
			{
				continue;
			}

			$role_url = $this->mk_my_orb("change", array(
				"from_org" => $from,
				"to_org" => $to,
				"to_project" => $arr["obj_inst"]->id()
			), "crm_role_manager");

			$ol = new object_list(array(
				"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
				"lang_id" => array(),
				"site_id" => array(),
				"company" => $from,
				"client" => $to,
				"project" => $arr["obj_inst"]->id(),
				"person" => $o->id()
			));


			$rs = array();
			foreach($ol->arr() as $role_entry)
			{
				$tmp = html::obj_change_url($role_entry->prop("role"));
				$tmp = html::obj_change_url($role_entry->prop("unit")).($tmp != "" ? " / " : "").$tmp;
				$rs[] = $tmp;
			}
			$t->define_data(array(
				"person" => html::obj_change_url($o),
				"co" => join(", ", $co_s),
				"rank" => html::obj_change_url($o->prop("rank")),
				"phone" => html::obj_change_url($o->prop("phone")),
				"mail" => html::obj_change_url($o->prop("email")),
				"roles" => join("<br>", $rs)."<br>".html::popup(array(
					"url" => $role_url,
					'caption' => t('Rollid'),
					"width" => 800,
					"height" => 600,
					"scrollbars" => "auto"
				)),
				"oid" => $o->id()
			));
		}
		$t->set_default_sortby("person");
	}
*/
/*
	function _init_team_search_res_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"width" => "16%"
		));

		$t->define_field(array(
			"name" => "co",
			"caption" => t("Organisatsioon"),
			"align" => "center",
			"width" => "16%"
		));

		$t->define_field(array(
			"name" => "rank",
			"caption" => t("Ametinimetus"),
			"align" => "center",
			"sortable" => 1,
			"width" => "16%"
		));

		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"align" => "center",
			"sortable" => 1,
			"width" => "16%"
		));

		$t->define_field(array(
			"name" => "mail",
			"caption" => t("E-post"),
			"align" => "center",
			"sortable" => 1,
			"width" => "16%"
		));

		$t->define_chooser(array(
			"name" => "res_sel",
			"field" => "oid"
		));
	}
*/

	/**
		@attrib name=del_participants
	**/
	function del_participants($arr)
	{
		$o = obj($arr["id"]);
		foreach(safe_array($arr["sel"]) as $oid)
		{
			$o->disconnect(array(
				"from" => $oid,
				"type" => "RELTYPE_PARTICIPANT"
			));
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=add_participants
	**/
	function add_participants($arr)
	{
		if(!is_oid($arr["id"])) return $arr["post_ru"];
		$o = obj($arr["id"]);
		if(is_oid($arr["team"])) $team = obj($arr["team"]);
		foreach(safe_array($arr["sel"]) as $oid)
		{
			$o->connect(array(
				"to" => $oid,
				"reltype" => "RELTYPE_PARTICIPANT"
			));
			if(is_oid($arr["team"])) $team->connect(array(
					"to" => $oid,
					"reltype" => "RELTYPE_TEAM_MEMBER"
				));
		}

		return aw_url_change_var("no_search",1,$arr["post_ru"]);
		return $arr["post_ru"];
	}

	function _get_sides_conflict($arr)
	{
		$sides = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SIDE")));
		if (!$sides->count())
		{
			return;
		}

		// get all projects that have some of the current projects sides as participants
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_PARTICIPANT.id" => $sides->ids()
		));

		$c = new connection();
		$t = $c->find(array(
			"from.class_id" => CL_PROJECT,
			"type" => "RELTYPE_PARTICIPANT",
			"to" => $sides->ids()
		));

		$tmp = array();
		foreach($t as $c)
		{
			$tmp[] = $c["from"];
		}
		$arr["prj"] = $tmp;
		$i = get_instance("applications/crm/crm_company_cust_impl");
		return $i->_get_my_projects($arr);
	}

	function _create_task($arr)
	{
		$o = obj();
		$o->set_parent($arr["obj_inst"]->id());
		$o->set_class_id(CL_TASK);
		$o->set_prop("start1", time());
		$o->set_prop("content", $arr["obj_inst"]->prop("description"));

		$ord = $arr["obj_inst"]->prop("orderer");
		if (is_array($ord))
		{
			$ord = reset($ord);
		}

		$o->set_name($arr["obj_inst"]->name());
		$o->set_prop("customer", $ord);
		$o->set_prop("project", $arr["obj_inst"]->id());
		$o->save();

		$arr["obj_inst"]->connect(array(
			"to" => $o->id(),
			"reltype" => "RELTYPE_PRJ_EVENT"
		));

		$u = get_instance(CL_USER);
		$p = obj($u->get_current_person());
		$p->connect(array(
			"to" => $o->id(),
			"reltype" => "RELTYPE_PERSON_TASK"
		));

		$cal = get_instance(CL_PLANNER);
		$cal_id = $cal->get_calendar_for_person($p);
		if ($this->can("view", $cal_id))
		{
			$cal->add_event_to_calendar(obj($cal_id), $o);
		}

		if ($this->can("view", $arr["obj_inst"]->prop("implementor")))
		{
			$ord = obj($arr["obj_inst"]->prop("implementor"));
			$ord->connect(array(
				"to" => $o->id(),
				"reltype" => "RELTYPE_TASK"
			));
		}

		header("Location: ".html::get_change_url($o->id(), array("return_url" => $arr["request"]["post_ru"])));
		die();
	}

	function _get_files($arr)
	{
		$objs = array();

		if (is_object($arr["obj_inst"]) && is_oid($arr["obj_inst"]->id()))
		{
			$ol = new object_list($arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_PRJ_FILE"
			)));
			$objs = $ol->arr();
		}

		$objs[] = obj();
		$objs[] = obj();
		$objs[] = obj();

		$types = array(
			CL_FILE => t(""),
			CL_CRM_MEMO => t("Memo"),
			CL_CRM_DOCUMENT => t("CRM Dokument"),
			CL_CRM_DEAL => t("Leping"),
			CL_CRM_OFFER => t("Pakkumine")
		);
		$impl = $arr["obj_inst"]->prop("implementor");
		if (is_array($impl))
		{
			$impl = reset($impl);
		}

		if ($this->can("view", $impl))
		{
			$impl_o = obj($impl);
			if (!$impl_o->get_first_obj_by_reltype("RELTYPE_DOCS_FOLDER"))
			{
				$u = get_instance(CL_USER);
				$impl = $u->get_current_company();
			}
		}

		if ($this->can("view", $impl))
		{
			$implo = obj($impl);
			$f = get_instance("applications/crm/crm_company_docs_impl");
			$fldo = $f->_init_docs_fld(obj($impl));
			$ot = new object_tree(array(
				"parent" => $fldo->id(),
				"class_id" => CL_MENU
			));
			$folders = array($fldo->id() => $fldo->name());
			$this->_req_level = 0;
			$this->_req_get_folders($ot, $folders, $fldo->id());

			// add server folders if set
			$sf = $implo->get_first_obj_by_reltype("RELTYPE_SERVER_FILES");
			if ($sf)
			{
				$s = $sf->instance();
				$fld = $s->get_folders($sf);
				$t = $arr["prop"]["vcl_inst"];

				usort($fld, create_function('$a,$b', 'return strcmp($a["name"], $b["name"]);'));

				$folders[$sf->id().":/"] = $sf->name();
				$this->_req_get_s_folders($fld, $sf, $folders, 0);
			}
		}
		else
		{
			$fldo = obj();
			$folders = array();
		}

		foreach($objs as $idx => $o)
		{
			$this->vars(array(
				"name" => $o->name(),
				"idx" => $idx,
				"types" => $this->picker($types)
			));

			if (is_oid($o->id()))
			{
				$ff = $o->get_first_obj_by_reltype("RELTYPE_FILE");
				if (!$ff)
				{
					$ff = $o;
				}
				$fi = $ff->instance();
				$fu = html::href(array(
					"url" => $fi->get_url($ff->id(), $ff->name()),
					"caption" => $ff->name()
				));
				$data[] = array(
					"name" => html::get_change_url($o->id(), array("return_url" => get_ru()), $o->name()),
					"file" => $fu,
					"type" => aw_ini_get("classes." . $o->class_id() . ".name"),
					"del" => html::href(array(
						"url" => $this->mk_my_orb("del_file_rel", array(
								"return_url" => get_ru(),
								"fid" => $o->id(),
								"from" => $arr["obj_inst"]->id()
						)),
						"caption" => t("Kustuta")
					)),
					"folder" => $o->path_str(array(
						"start_at" => $fldo->id(),
						"path_only" => true
					))
				);
			}
			else
			{
				$data[] = array(
					"name" => html::textbox(array(
						"name" => "fups_d[$idx][tx_name]"
					)),
					"file" => html::fileupload(array(
						"name" => "fups_".$idx
					)),
					"type" => html::select(array(
						"options" => $types,
						"name" => "fups_d[$idx][type]"
					)),
					"del" => "",
					"folder" => html::select(array(
						"name" => "fups_d[$idx][folder]",
						"options" => $folders
					))
				);
			}
		}

		$t = new vcl_table(array(
			"layout" => "generic",
		));

		$t->define_field(array(
			"caption" => t("Nimi"),
			"name" => "name",
		));

		$t->define_field(array(
			"caption" => t("Fail"),
			"name" => "file",
		));

		$t->define_field(array(
			"caption" => t("T&uuml;&uuml;p"),
			"name" => "type",
		));

		$t->define_field(array(
			"caption" => t("Kataloog"),
			"name" => "folder",
		));

		$t->define_field(array(
			"caption" => t(""),
			"name" => "del",
		));

		foreach($data as $e)
		{
			$t->define_data($e);
		}

		$arr["prop"]["value"] = $t->draw();
	}

	function _set_files($arr)
	{
		$t = obj($arr["request"]["id"]);
		$u = get_instance(CL_USER);
		$co = obj($u->get_current_company());
		foreach(safe_array($_POST["fups_d"]) as $num => $entry)
		{
			if (is_uploaded_file($_FILES["fups_".$num]["tmp_name"]))
			{
				$f = get_instance("applications/crm/crm_company_docs_impl");
				$fldo = $f->_init_docs_fld($co);
				if ($this->can("add", $entry["folder"]))
				{
					$fldo = obj($entry["folder"]);
				}
				if (!$fldo)
				{
					return;
				}

				if ($entry["type"] == CL_FILE)
				{
					// add file
					$f = get_instance(CL_FILE);

					$fs_fld = null;
					if (strpos($entry["folder"], ":") !== false)
					{
						list($sf_id, $sf_path) = explode(":", $entry["folder"]);
						$sf_o = obj($sf_id);
						$fs_fld = $sf_o->prop("folder").$sf_path;
					}
					$fil = $f->add_upload_image("fups_$num", $fldo->id(), 0, $fs_fld);

					if (is_array($fil))
					{
						$t->connect(array(
							"to" => $fil["id"],
							"reltype" => "RELTYPE_PRJ_FILE"
						));
					}
				}
				else
				{
					$o = obj();
					$o->set_class_id($entry["type"]);
					$o->set_name($entry["tx_name"] != "" ? $entry["tx_name"] : $_FILES["fups_$num"]["name"]);


					$o->set_parent($fldo->id());
					if ($entry["type"] != CL_FILE)
					{
						$o->set_prop("project", $t->id());
						$o->set_prop("customer", reset($t->prop("orderer")));
					}
					$o->save();

					// add file
					$f = get_instance(CL_FILE);

					$fs_fld = null;
					if (strpos($entry["folder"], ":") !== false)
					{
						list($sf_id, $sf_path) = explode(":", $entry["folder"]);
						$sf_o = obj($sf_id);
						$fs_fld = $sf_o->prop("folder").$sf_path;
					}
					$fil = $f->add_upload_image("fups_$num", $o->id(), 0, $fs_fld);

					if (is_array($fil))
					{
						$o->connect(array(
							"to" => $fil["id"],
							"reltype" => "RELTYPE_FILE"
						));
						$t->connect(array(
							"to" => $o->id(),
							"reltype" => "RELTYPE_PRJ_FILE"
						));
					}
				}
			}
		}
		return $arr["post_ru"];
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

	function callback_pre_save($arr)
	{
		if ($arr["obj_inst"]->prop("code") == "")
		{
			// call site based code gen
			$si = __get_site_instance();
			if (method_exists($si, "project_gen_code"))
			{
				$arr["obj_inst"]->set_prop("code", $si->project_gen_code($arr));
			}
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
		@attrib name=do_create_prepayment_bill
		@param id required type=int acl=view
		@param ru required
	**/
	function do_create_prepayment_bill($arr)
	{
		$arr["obj_inst"] = obj($arr["id"]);

		$bill = obj();
		$bill->set_class_id(CL_CRM_BILL);
		$bill->set_parent($arr["id"]);
		$bill->save();

		$ser = get_instance(CL_CRM_NUMBER_SERIES);
		$bno = $ser->find_series_and_get_next(CL_CRM_BILL,0,time());
		if (!$bno)
		{
			$bno = $bill->id();
		}
		$bill->set_prop("bill_no", $bno);
		$bill->set_name(sprintf(t("Arve nr %s"), $bill->prop("bill_no")));

		$cust = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_ORDERER");
		$impl = $arr["obj_inst"]->prop("orderer");
		if (is_array($impl))
		{
			$impl = reset($impl);
		}
		$bill->set_prop("customer", $impl);
		$bill->set_prop("impl", $arr["obj_inst"]->prop("implementor"));
		$bill->save();


		$arr["obj_inst"]->connect(array(
			"to" => $bill->id(),
			"type" => "RELTYPE_PREPAYMENT_BILL"
		));

		$br = obj();
		$br->set_class_id(CL_CRM_BILL_ROW);
		$br->set_parent($bill->id());
		$br->set_prop("name", sprintf(t("%s ettemaks"), $arr["obj_inst"]->name()));
		$br->set_prop("amt", 1);
		$br->set_prop("price", $arr["obj_inst"]->prop("prepayment"));
		$br->set_prop("date", date("d.m.Y", time()));
		$br->save();

		$bill->connect(array(
			"to" => $br->id(),
			"type" => "RELTYPE_ROW"
		));

		return $this->mk_my_orb("change", array("id" => $bill->id(), "return_url" => $arr["ru"]), CL_CRM_BILL);
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ("aw_account_balances" == $tbl)
		{
			$i = get_instance(CL_CRM_CATEGORY);
			return $i->do_db_upgrade($tbl, $field);
		}
		switch($field)
		{
			case "aw_archive_code":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "varchar(50)"
				));
				return true;
			case "planned_work_time":
			case "average_hr_price":
			case "planned_other_expenses":
			case "aw_budget":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "double"
				));
				return true;
			case "aw_proj_mgr":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int",
				));
				$ol = new object_list(array(
					"class_id" => CL_PROJECT,
					"site_id" => array(),
					"lang_id" => array(),
				));
				foreach($ol->arr() as $o)
				{
					$o->set_prop("proj_mgr", $o->meta("proj_mgr"));
					$o->save();
				}
				return true;
		}
	}

	function _get_risk_eval($p)
	{
		$pp = get_current_person();
		$ol = new object_list(array(
			"class_id" => CL_PROJECT_RISK_EVALUATION,
			"lang_id" => array(),
			"site_id" => array(),
			"proj" => $p->id(),
			"evaluator" => $pp->id()
		));
		if ($ol->count())
		{
			return $ol->begin();
		}
		else
		{
			$o = obj();
			$o->set_parent($p->id());
			$o->set_class_id(CL_PROJECT_RISK_EVALUATION);
			$o->set_name(sprintf(t("Hinnang projektile %s"), $p->name()));
			$o->set_prop("proj", $p->id());
			$o->set_prop("evaluator" , $pp->id());
			$o->save();
			return $o;
		}
	}
/*
	function _save_risks_eval($arr)
	{
		// see if there is an eval for this person already, if not, create it , if it is, update it
		$se = $this->_get_risk_eval($arr["obj_inst"]);
		$se->set_meta("grid", $arr["request"]["a"]);
		$se->save();
	}

	function _init_risks_eval_tbl($t, $o)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Hindamislaua nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Hindamislaua looja nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _risks_eval($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_risks_eval_tbl($t, $arr["obj_inst"]);

		$u = get_instance(CL_USER);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_RISK_EVAL")) as $c)
		{
			$st = $c->to();
			$p = $u->get_person_for_uid($st->createdby());
			$t->define_data(array(
				"name" => html::obj_change_url($c->to()),
				"createdby" => $p->name(),
				"created" => $st->created(),
				"ord" => $st->ord(),
				"oid" => $c->prop("to")
			));
		}
	}
*/
	function _init_strat_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Eesm&auml;rgi nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Eesm&auml;rgi esitaja nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "ord",
			"caption" => t("J&auml;rjekord"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _strat($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_strat_t($t);

		$u = get_instance(CL_USER);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_STRAT")) as $c)
		{
			$st = $c->to();
			$p = $u->get_person_for_uid($st->createdby());
			$t->define_data(array(
				"name" => html::obj_change_url($c->to()),
				"createdby" => $p->name(),
				"created" => $st->created(),
				"ord" => $st->ord(),
				"oid" => $c->prop("to")
			));
		}
		$t->set_default_sorder("asc");
		$t->set_default_sortby("ord");
		$t->sort_by();
	}

	function _strat_tb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Editegur"),
			"url" => html::get_new_url(CL_PROJECT_STRAT_GOAL, $arr["obj_inst"]->id(), array("return_url" => get_ru(), "alias_to" => $arr["obj_inst"]->id(), "reltype" => 17))
		));
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));
	}
/*
	function _strat_a_tb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Eesm&auml;rkide hindamise t&ouml;&ouml;laud"),
			"url" => html::get_new_url(CL_PROJECT_STRAT_GOAL_EVAL_WS, $arr["obj_inst"]->id(), array("return_url" => get_ru(), "alias_to" => $arr["obj_inst"]->id(), "reltype" => 19))
		));
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));

	}

	function _risks_eval_tb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Riskide hindamise t&ouml;&ouml;laud"),
			"url" => html::get_new_url(CL_PROJECT_RISK_EVAL_WS, $arr["obj_inst"]->id(), array("return_url" => get_ru(), "alias_to" => $arr["obj_inst"]->id(), "reltype" => 20))
		));
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));

	}

	function _init_strat_a_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Hindamislaua nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Hindamislaua looja nimi"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Kuup&auml;ev"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"format" => "d.m.Y H:i",
			"numeric" => 1
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _strat_a($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_strat_a_t($t);

		$u = get_instance(CL_USER);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_STRAT_EVAL")) as $c)
		{
			$st = $c->to();
			$p = $u->get_person_for_uid($st->createdby());
			$t->define_data(array(
				"name" => html::obj_change_url($c->to()),
				"createdby" => $p->name(),
				"created" => $st->created(),
				"ord" => $st->ord(),
				"oid" => $c->prop("to")
			));
		}
	}
*/
	function _init_risks_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Riski nimi"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "owner",
			"caption" => t("Omanik"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "desc",
			"caption" => t("Kirjeldus"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _risks($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_risks_t($t);

		$pr = get_instance(CL_PROJECT_RISK);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_RISK")) as $c)
		{
			$r = $c->to();
			$t->define_data(array(
				"name" => html::obj_change_url($r),
				"owner" => html::obj_change_url($r->prop("owner")),
				"desc" => nl2br($r->comment()),
				"type" => $pr->types[$r->prop("type")],
				"oid" => $r->id()
			));
		}
	}

	function _risks_tb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Risk"),
			"url" => html::get_new_url(CL_PROJECT_RISK, $arr["obj_inst"]->id(), array("return_url" => get_ru(), "alias_to" => $arr["obj_inst"]->id(), "reltype" => 18))
		));
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "del_goals",
			"tooltip" => t("Kustuta"),
		));
	}

	/**
		@attrib name=cut_goals
	**/
	function cut_goals($arr)
	{
		$_SESSION["proj_cut_goals"] = $arr["sel"];
		$_SESSION["proj_cut_from"] = $arr["tf"];
		return $arr["post_ru"];
	}

	/**
		@attrib name=paste_goals
	**/
	function paste_goals($arr)
	{
		if ($arr["tf"])
		{
			// rewire predicates for cut goals
			foreach(safe_array($_SESSION["proj_cut_goals"]) as $goal_id)
			{
				$go = obj($goal_id);
				$p = $go->prop("predicates");
				if (!is_array($p))
				{
					if ($this->can("view", $p))
					{
						$p = array($p=>$p);
					}
					else
					{
						$p = array();
					}
				}
				$p = $this->make_keys($p);
				unset($p[$_SESSION["proj_cut_from"]]);
				$p[$arr["tf"]] = $arr["tf"];
				$go->set_prop("predicates", $p);
				$go->save();
			}
		}

		unset($_SESSION["proj_cut_goals"]);
		unset($_SESSION["proj_cut_from"]);
		return $arr["post_ru"];
	}

	function get_team($p)
	{
		$ret = array();
		foreach($p->connections_from(array("type" => "RELTYPE_PARTICIPANT")) as $c)
		{
			$o = $c->to();

			if ($o->class_id() == CL_USER)
			{
				$i = $o->instance();
				$o = obj($i->get_person_for_user($o));
			}

			if ($o->class_id() != CL_CRM_PERSON)
			{
				continue;
			}
			$ret[$o->id()] = $o->id();
		}
		return $ret;
	}

	/**
		@attrib name=copy_team_mem
	**/
	function copy_team_mem($arr)
	{
		$_SESSION["proj_team_member_copy"] = $arr["sel"];
		return $arr["post_ru"];
	}

	/**
		@attrib name=paste_team_mem
	**/
	function paste_team_mem($arr)
	{
		if($arr["team"] == "all_parts")
		{
			$project = obj($arr["id"]);
			foreach(safe_array($_SESSION["proj_team_member_copy"]) as $mem_id)
			{
				$project->connect(array(
					"to" => $mem_id,
					"type" => "RELTYPE_PARTICIPANT",
				));
			}
			$_SESSION["proj_team_member_copy"] = null;
			return $arr["post_ru"];
		}
		if (!$this->can("view", $arr["team"]))
		{
			return $arr["post_ru"];
		}
		$team = obj($arr["team"]);
		foreach(safe_array($_SESSION["proj_team_member_copy"]) as $mem_id)
		{
			$team->connect(array(
				"to" => $mem_id,
				"type" => "RELTYPE_TEAM_MEMBER"
			));
		}
		$_SESSION["proj_team_member_copy"] = null;
		return $arr["post_ru"];
	}

	/**
		@attrib name=del_team_mem
	**/
	function del_team_mem($arr)
	{
		//kustutab meeskonnad
		if($arr["team"] == "teams" && is_oid($arr["id"]))
		{
			$project = obj($arr["id"]);
			foreach(safe_array($arr["sel"]) as $mem_id)
			{
				$project->disconnect(array("from" => $mem_id));
			}
		}
		//kustutab meeskonnaliikmed meeskonnast
		if(is_oid($arr["team"]))
		{
			$team = obj($arr["team"]);
			foreach(safe_array($arr["sel"]) as $mem_id)
			{
				$team->disconnect(array("from" => $mem_id));
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=cut_files
	**/
	function cut_files($arr)
	{
		$_SESSION["proj_cut_files"] = $arr["sel"];
		return $arr["post_ru"];
	}

	/**
		@attrib name=paste_files
	**/
	function paste_files($arr)
	{
		$pt = $arr["tf"];
		if (!$arr["tf"])
		{
			$pt = $this->_get_files_pt(array(
				"request" => array("tf" => $arr["tf"]),
				"obj_inst" => obj($arr["id"])
			));
		}
		foreach(safe_array($_SESSION["proj_cut_files"]) as $file)
		{
			$fo = obj($file);
			$fo->set_parent($pt);
			$fo->save();
		}
		unset($_SESSION["proj_cut_files"]);
		return $arr["post_ru"];
	}

	function _init_orderer_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel_ord",
			"field" => "oid"
		));
		$t->define_field(array(
			"name" => "orderer",
			"caption" => t("Tellija"),
			"sortable" => 1,
			"width" => "40%"
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"sortable" => 1,
			"width" => "35%"
		));
		$t->define_field(array(
			"name" => "contact",
			"caption" => t("Kontaktisik"),
			"sortable" => 1,
			"width" => "25%"
		));
	}

	function _orderer_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_orderer_table($t);

		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}

		foreach($arr["obj_inst"]->connections_from(array("type" => 9)) as $c)
		{
			$c = $c->to();
			if($c->class_id() == CL_CRM_PERSON)
			$t->define_data(array(
				"oid" => $c->id(),
				"orderer" => html::obj_change_url($c),
				"phone" => html::obj_change_url($c->prop("phone")),
				"contact" => html::obj_change_url($c)
			));

			else
			$t->define_data(array(
				"oid" => $c->id(),
				"orderer" => html::obj_change_url($c),
				"phone" => html::obj_change_url($c->prop("phone_id")),
				// "contact" => html::obj_change_url($c->prop("contact_person")) //FIXME: contact_person pole crmcompany property
			));
		}
	}

	function _init_part_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel_part",
			"field" => "oid"
		));
		$t->define_field(array(
			"name" => "participants",
			"caption" => t("Osaleja"),
			"sortable" => 1,
			"width" => "40%"
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"sortable" => 1,
			"width" => "35%"
		));
		$t->define_field(array(
			"name" => "contact",
			"caption" => t("Kontaktisik"),
			"sortable" => 1,
			"width" => "25%"
		));
	}

	function _part_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_part_table($t);

		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		foreach($arr["obj_inst"]->connections_from(array("type" => 2)) as $c)
		{
			$c = $c->to();
			if($c->class_id() == CL_CRM_PERSON)
			$t->define_data(array(
				"oid" => $c->id(),
				"participants" => html::obj_change_url($c),
				"phone" => $c->get_phone(),
				"contact" => html::obj_change_url($c)
			));

			else
			$t->define_data(array(
				"oid" => $c->id(),
				"participants" => html::obj_change_url($c),
				"phone" => html::obj_change_url($c->prop("phone_id")),
				"contact" => html::obj_change_url($c->prop("contact_person"))
			));
		}
	}

	function _init_impl_table($t)
	{
		$t->define_chooser(array(
			"name" => "sel_ord",
			"field" => "oid"
		));
		$t->define_field(array(
			"name" => "implementor",
			"caption" => t("Teostaja"),
			"sortable" => 1,
			"width" => "40%"
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"sortable" => 1,
			"width" => "35%"
		));
		$t->define_field(array(
			"name" => "contact",
			"caption" => t("Kontaktisik"),
			"sortable" => 1,
			"width" => "25%"
		));
	}

	function _impl_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_impl_table($t);

		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		foreach($arr["obj_inst"]->connections_from(array("type" => 10)) as $c)
		{
			$c = $c->to();
			if($c->class_id() == CL_CRM_PERSON){
				$t->define_data(array(
					"oid" => $c->id(),
					"implementor" => html::obj_change_url($c),
					"phone" => html::obj_change_url($c->prop("phone")),
					"contact" => html::obj_change_url($c)
				));
			}
			else
			{
				$t->define_data(array(
					"oid" => $c->id(),
					"implementor" => html::obj_change_url($c),
					"phone" => html::obj_change_url($c->prop("phone_id")),
					"contact" => "", //html::obj_change_url($c->prop("contact_person")) //TODO: v6tta contact person kliendisuhtest
				));
			}
		}
	}

	function _parts_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Uus"),
		));

		$tb->add_sub_menu(array(
			"parent" => "new",
			"name" => "cust",
			"text" => t("Tellija"),
		));

		$tb->add_menu_item(array(
			"parent" => "cust",
			"text" => t("Organisatsioon"),
			"link" => html::get_new_url(CL_CRM_COMPANY, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 9 // RELTYPE_CUSTOMER
			)),
		));
		$tb->add_menu_item(array(
			"parent" => "cust",
			"text" => t("Isik"),
			"link" => html::get_new_url(CL_CRM_PERSON, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 9 // RELTYPE_CUSTOMER
			)),
		));

//		if ($arr["obj_inst"]->prop("customer"))
//		{
		$tb->add_sub_menu(array(
			"parent" => "new",
			"name" => "part",
			"text" => t("Osaleja"),
		));
//		}
//		$cur_co = get_current_company();
		$tb->add_menu_item(array(
			"parent" => "part",
			"text" => t("Organisatsioon"),
			"link" => html::get_new_url(CL_CRM_COMPANY, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 2
			)),
		));
		$tb->add_menu_item(array(
			"parent" => "part",
			"text" => t("Isik"),
			"link" => html::get_new_url(CL_CRM_PERSON, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 2
			)),
		));

		$tb->add_sub_menu(array(
			"parent" => "new",
			"name" => "expl",
			"text" => t("Teostaja"),
		));
		$tb->add_menu_item(array(
			"parent" => "expl",
			"text" => t("Organisatsioon"),
			"link" => html::get_new_url(CL_CRM_COMPANY, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 10
			)),
		));
		$tb->add_menu_item(array(
			"parent" => "expl",
			"text" => t("Isik"),
			"link" => html::get_new_url(CL_CRM_PERSON, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 10
			)),
		));


/*		$tb->add_menu_item(array(
			"text" => sprintf(t("Lisa isik organisatsiooni %s"), $cur_co->name()),
			"parent" => "part",
			"link" => html::get_new_url(CL_CRM_PERSON, $cur_co->id(), array(
				"return_url" => get_ru(),
				"add_to_task" => $arr["obj_inst"]->id(),
				"add_to_co" => $cur_co->id()
			))
		));
*/
/*		$tb->add_menu_item(array(
			"parent" => "new",
			"text" => t("Teostaja"),
			"link" => html::get_new_url(CL_CRM_COMPANY, $arr["obj_inst"]->parent(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 10
			)),
		));
*/
		$tb->add_menu_button(array(
			"name" => "search",
			"tooltip" => t("Otsi"),
			"img" => "search.gif"
		));

		$cur = get_current_company();
		$s = $cur ? array("co" => array($cur->id() => $cur->id())) : array();
		if (is_oid($arr["obj_inst"]->id()))
		{
			foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
			{
				$s["co"][$c->prop("to")] = $c->prop("to");
			}
			foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_ORDERER")) as $c)
			{
				$s["co"][$c->prop("to")] = $c->prop("to");
			}
		}

		$url = $this->mk_my_orb("do_search", array(
			"pn" => "orderer",
			"clid" => array(
				CL_CRM_PERSON,
				CL_CRM_COMPANY
			),
			"s" => $s
		), "popup_search");
		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("Tellija"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi")."',550,500)",
		));

		$url = $this->mk_my_orb("do_search", array(
			"pn" => "participants",
			"clid" => array(
				CL_CRM_PERSON,
				CL_CRM_COMPANY,
				CL_USER,
			),
			"s" => $s
		), "crm_participant_search");
		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("Osaleja"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi1")."',550,500)",
		));

		$url = $this->mk_my_orb("do_search", array(
			"pn" => "implementor",
			"clid" => array(
				CL_CRM_PERSON,
				CL_CRM_COMPANY
			),
			"s" => $s
		), "crm_participant_search");

		$tb->add_menu_item(array(
			"parent" => "search",
			"text" => t("Teostaja"),
			"link" => "javascript:aw_popup_scroll('$url','".t("Otsi2")."',550,500)",
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_rels"
		));
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
		}

		if (is_array($arr["sel_part"]) && count($arr["sel_part"]))
		{
			foreach(safe_array($arr["sel_part"]) as $item)
			{
				$o->disconnect(array(
					"from" => $item,
				));
			}
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

	function _get_stats($o)
	{
		// bugs
		$ol = new object_list(array(
			"class_id" => CL_BUG,
			"project" => $o->id()
		));
		$hrs = 0;
		foreach($ol->arr() as $b)
		{
			$hrs += $b->prop("num_hrs_real");
		}
		$res = "Bugid: ".$hrs." tundi <br>";
		// tasks
		$ol = new object_list(array(
			"class_id" => array(CL_TASK,CL_CRM_MEETING,CL_CRM_CALL),
			"project" => $o->id(),
			"brother_of" => new obj_predicate_prop("id")
		));
		$tot_h = $hrs;
		$hrs = 0;
		foreach($ol->arr() as $b)
		{
			$hrs += $b->prop("num_hrs_real") + $b->prop("time_real");
			foreach($b->connections_from(array("type" => "RELTYPE_ROW")) as $c)
			{
				$r = $c->to();
				$hrs += $r->prop("time_real");
			}
		}


		$res .= "Taskid & k6ned & kohtumised: $hrs tundi <br>";
		$tot_h += $hrs;

		$hrs_price = $tot_h * 500;

		$res .= "Projekti kasum: ".($o->prop("proj_price") - $hrs_price)." <br>";

		return $res;
	}

	/**
		@attrib name=export_req
	**/
	function export_req($arr)
	{
		$o = obj($arr["id"]);
		if (!$arr["tf"] && !$arr["proc"])
		{
			return;
		}

		if ($arr["proc"])
		{
			$ol = new object_list(array(
				"process" => $arr["proc"],
				"class_id" => array(CL_PROCUREMENT_REQUIREMENT),
			));
		}
		else
		{
			$ol = new object_list(array(
				"parent" => $arr["tf"],
				"class_id" => array(CL_PROCUREMENT_REQUIREMENT)
			));
		}

		$t = new vcl_table();
		$t->table_from_ol($ol, array("name", "created", "pri", "req_co", "req_p", "project", "process", "planned_time", "desc", "state", "budget"), CL_PROCUREMENT_REQUIREMENT);
		header('Content-type: application/octet-stream');
		header('Content-disposition: root_access; filename="req.csv"');
		print $t->get_csv_file();
		die();
	}

	function _get_prods_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_search_button(array(
			"pn" => "prod_search_res",
			"multiple" => 1,
			"clid" => CL_SHOP_PRODUCT,
		));
		$tb->add_delete_rels_button();
	}

	function _get_prods_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->table_from_ol(
			new object_list($arr["obj_inst"]->connections_from(array(
				"type" => "RELTYPE_PRODUCT"
			))),
			array("name", "comment", "price"),
			CL_SHOP_PRODUCT
		);
	}

	private function _init_stats_table($t, $types)
	{
		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"align" => "right"
		));

		$all_types = array(
			"paid" => t("Tasuline"),
			"unpaid" => t("Tasuta")
		);
		foreach($types as $type_id => $subs)
		{
			$t->define_field(array(
				"name" => "type_".$type_id,
				"caption" => aw_ini_get("classes.{$type_id}.name"),
				"align" => "center"
			));

			$t->define_field(array(
				"name" => "type_".$type_id."_sub_paid",
				"caption" => t("Tasuline"),
				"align" => "center" ,
				"parent" => "type_".$type_id
			));
			$t->define_field(array(
				"name" => "type_".$type_id."_sub_unpaid",
				"caption" => t("Tasuta"),
				"align" => "center" ,
				"parent" => "type_".$type_id
			));

			foreach(get_current_company()->get_activity_stats_types() as $a_type_id => $a_type_name)
			{
				$t->define_field(array(
					"name" => "type_".$type_id."_sub_".$a_type_id,
					"caption" => $a_type_name,
					"align" => "center",
					"parent" => "type_".$type_id
				));
				$all_types[$a_type_id] = $a_type_name;
			}
		}

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center"
		));
		foreach($all_types as $sub_id => $sub_capt)
		{
			$t->define_field(array(
				"name" => "sum_".$sub_id,
				"caption" => $sub_capt,
				"align" => "center",
				"parent" => "sum"
			));
		}
	}

	private function _get_stats_money_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$work_data = $arr["obj_inst"]->get_workers_stats();

		//iga isiku info
		foreach($work_data as  $person  => $d)
		{
			if($this->can("view" , $person))
			{
				$data_defined = $d;
				foreach($data_defined as $key => $dt)
				{
					if($dt)
					{
						$data_defined[$key] = number_format($dt , 2);
					}
					else
					{
						$data_defined[$key] = "";
					}
				}
				$data_defined["person"] = html::obj_change_url($person);
				$t->define_data($data_defined);
			}
		}

		//kokkuv6tte rea info
		$data_defined = $work_data["result"];
		$data_defined["person"] = t("Kokku");
		$t->define_data($data_defined);

		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"align" => "left",
		));


		$t->define_field(array(
			"name" => "guess",
			"caption" => t("Prognoositud t&ouml;&ouml;tunde"),
			"align" => "right",
		));

		$t->define_field(array(
			"name" => "real",
			"caption" => t("Tegelikke t&ouml;&ouml;tunde"),
			"align" => "right",
		));

		$t->define_field(array(
			"name" => "without",
			"caption" => t("Tunnihinnata t&ouml;&ouml;tunde"),
			"align" => "right",
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("T&ouml;id summas"),
			"align" => "right",
		));

		$t->define_field(array(
			"name" => "cust",
			"caption" => t("T&ouml;&ouml;tunde kliendile"),
			"align" => "right",
		));

		$t->define_field(array(
			"name" => "sum_cust",
			"caption" => t("T&ouml;id kliendile summas"),
			"align" => "right",
		));

		$t->define_field(array(
			"name" => "on_bill",
			"caption" => t("T&ouml;id arvel summas"),
			"align" => "right",
		));

		$t->define_field(array(
			"name" => "payments",
			"caption" => t("Tasu laekunud summas"),
			"align" => "right",
		));

		$t->set_sortable(false);
	}

	private function _get_hours_stats_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$all_data = $arr["obj_inst"]->get_rows_data();
		$work_data = array();
		$this->event_types = $this->event_types + array(1 => t("Kokku"));
		$params = array(
			"prog" => t("Prognoositud"),
			"real" => t("Tegelik"),
			"cust" => t("Kliendile"),

		);

		$result = array();

		foreach($all_data as $data)
		{
			$person = reset($data["impl"]);
			if(!isset($work_data[$person]))
			{
				$work_data[$person] = array();
			}
		$custh = $data["time_to_cust"];// ? $data["time_to_cust"] : $data["time_real"];
			$work_data[$person][$data["task.class_id"]]["real"] +=$data["time_real"];
			$work_data[$person][1]["real"] +=$data["time_real"];
			$work_data[$person][$data["task.class_id"]]["cust"] +=$custh;
			$work_data[$person][1]["cust"] +=$custh;
			$work_data[$person][$data["task.class_id"]]["prog"] +=$data["time_guess"];
			$work_data[$person][1]["prog"] +=$data["time_guess"];

			$result[$data["task.class_id"]]["real"] +=$data["time_real"];
			$result[1]["real"] +=$data["time_real"];
			$result[$data["task.class_id"]]["cust"] +=$custh;
			$result[1]["cust"] +=$custh;
			$result[$data["task.class_id"]]["prog"] +=$data["time_guess"];
			$result[1]["prog"] +=$data["time_guess"];
		}

		//iga isiku info
		foreach($work_data as  $person  => $d)
		{
			$data_defined = array();
			$data_defined["person"] = html::obj_change_url($person);
			foreach($this->event_types as $clid => $capt)
			{
				foreach($params as $param_id => $param_name)
				{
					if(!$d[$clid][$param_id])
					{
						$data_defined[$clid."_".$param_id] = "-";
					}
					else
					{
						$data_defined[$clid."_".$param_id] = number_format($d[$clid][$param_id],2);
					}
				}
			}
			$t->define_data($data_defined);
		}

		//kokkuv6tte rea info
		$data_defined = array();
		$data_defined["person"] = "<b>" . t("Kokku") . "</b>";
		foreach($this->event_types as $clid => $capt)
		{
			foreach($params as $param_id => $param_name)
			{
				if(!$result[$clid][$param_id])
				{
					$data_defined[$clid."_".$param_id] = "-";
				}
				else
				{
					$data_defined[$clid."_".$param_id] = number_format($result[$clid][$param_id] , 2);
				}
			}
		}
		$t->define_data($data_defined);




		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"align" => "left",
		));

		foreach($this->event_types as $clid => $capt)
		{
			$t->define_field(array(
				"name" => "field".$clid,
				"caption" => $capt,
				"align" => "center",
			));
			foreach($params as $param_id => $param_name)
			{
				$t->define_field(array(
					"name" => $clid."_".$param_id,
					"caption" => $param_name,
					"align" => "right",
					"parent" => "field".$clid,
				));
			}
		}

		$t->set_sortable(false);
	}

	private function _get_stats_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$all_data = $arr["obj_inst"]->get_rows_data();
		$work_data = array();
		$this->event_types = $this->event_types;
		$end = $arr["obj_inst"]->prop("end");

		if($arr["obj_inst"]->prop("start") > 0)
		{
			$start = $arr["obj_inst"]->prop("start");
		}
		else
		{
			$start = time();
		}
		$result = array();

		foreach($all_data as $data)
		{
			if(!$data["time_real"])
			{
				continue;
			}
			$person = reset($data["impl"]);
			if(!isset($work_data[$person]))
			{
				$work_data[$person] = array();
			}

			if($end < $data["date"])
			{
				$end = $data["date"];
			}
			if($start > $data["date"])
			{
				$start = $data["date"];
			}
			$date_day_start = date("dmy" , $data["date"]);
			$work_data[$person][$data["task.class_id"]][$date_day_start] +=$data["time_real"];
			$work_data[$person][1][$date_day_start] +=$data["time_real"];

			$result[$data["task.class_id"]][$date_day_start] +=$data["time_real"];
			$result[1][$date_day_start] +=$data["time_real"];
		}


		$month_chooser = $arr["request"]["month_chooser"];
		if(!$month_chooser || !$month_chooser)
		{
			$month_chooser = array();
//			$month_chooser[date("my" , $end)] = 1;
//			$month_chooser[date("my" , mktime(0,0,0, date("m" , $end) - 1, date("d" , $end), date("Y" , $end)))] = 1;
			$month_chooser[date("my" , time())] = 1;
			$month_chooser[date("my" , mktime(0,0,0, date("m" , time()) - 1, date("d" , time()), date("Y" , time())))] = 1;
			if($end < time())
			{
				$end = time();
			}
		}



		//iga isiku info
		foreach($work_data as $person  => $d)
		{
			if(!$person) continue;
			$data_defined = array();//$work_data[$person][1];
			$data_defined["person"] = "<b>".html::obj_change_url($person, str_replace(" " , "&nbsp;" , get_name($person)))."</b>";
			$t->define_data($data_defined);

			foreach($this->event_types as $clid => $capt)
			{
				$data_defined = $work_data[$person][$clid];
				$data_defined["person"] = $capt;
				$t->define_data($data_defined);
			}
		}

		//kokkuv6tte rea info
		$data_defined = array();
		$data_defined["person"] =  "<b>".t("Kokku")."</b>";
		$t->define_data($data_defined);
		foreach($this->event_types as $clid => $capt)
		{
			$data_defined = $result[$clid];
			$data_defined["person"] = $capt;
			$t->define_data($data_defined);
		}

		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"align" => "left",
		));


		//mingi piirang ka peale...
/*		if($start < $end - self::DAY_LENGTH_SECONDS * 30)
		{
			$start = $end - self::DAY_LENGTH_SECONDS * 30;
		}
*/

/*		foreach($month_chooser as $month_str => $val)
		{
			$time = mktime(0,0,0, substr($month_str , 0 , 2), 11, substr($month_str , 2 , 2));

			$t->define_field(array(
				"name" => date("my" , $time),
				"caption" => date("M Y" ,$time),
				"align" => "center",
			));
		}
*/
		$start = date_calc::get_day_start($start);
		if(!($end > 1 && $start > 1))
		{
			return;
		}
		while($end > $start)
		{
			if(!$month_chooser[date("my" , $start)])
			{
				$start += self::DAY_LENGTH_SECONDS;
				continue;
			}
			if($parent_field !=  date("my" , $start))
			{
				$t->define_field(array(
					"name" => date("my" , $start),
					"caption" => date("M Y" , $start),
					"align" => "center",
				));
				$parent_field =  date("my" , $start);
			}
			$t->define_field(array(
				"name" => date("dmy" , $start),
				"caption" => date("d" , $start),
				"align" => "right",
				"parent" => date("my" , $start),
				"callback" =>  array($this, "__tm_field_format"),
				"callb_pass_row" => true,
			));
			$start += self::DAY_LENGTH_SECONDS;
		}

/*
		$stats_by_ppl = $arr["obj_inst"]->stats_get_by_person();
		$types = array();
		$tot_sums = array();
arr($stats_by_ppl);
		foreach($stats_by_ppl as $person => $data)
		{
			$d = array(
				"person" => html::obj_change_url($person),
			);
			$sum = array();
			foreach($data as $type_id => $inf)
			{
				$sum["paid"] += $inf["paid"];
				$sum["unpaid"] += $inf["unpaid"];
				foreach(safe_array($inf["act_type"]) as $a_type_id => $a_type_hrs)
				{
					$sum[$a_type_id] += $a_type_hrs;
					$d["type_".$type_id."_sub_".$a_type_id] = $a_type_hrs;
				}

				$d["type_".$type_id."_sub_paid"] += $inf["paid"];
				$d["type_".$type_id."_sub_unpaid"] += $inf["unpaid"];

				$types[$type_id] = $type_id;
			}

			foreach($sum as $sum_id => $sum_val)
			{
				$d["sum_".$sum_id] = $sum_val;
			}

			foreach($d as $k => $v)
			{
				$tot_sums[$k] += $v;
			}
			$t->define_data($d);
		}

		$this->_init_stats_table($t, $types);

		$tot_sums["person"] = t("Summa");
		$t->define_data(array_map(create_function('$a', 'return html::strong($a);'), $tot_sums));
*/
		$t->set_sortable(false);
	}

	function __tm_field_format($val)
	{
		if(!$val[$val["_this_cell"]])
		{
			return "-";
		}
		else
		{
			return($val[$val["_this_cell"]]);
		}
	}


	private function _init_stats_entry_table($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "content",
			"caption" => t("Sisu"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "add_wh",
			"caption" => t("Lisandunud t&ouml;&ouml;tunnid"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "on_bill",
			"caption" => t("Arvele?"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "hrs_cust",
			"caption" => t("Tunde kliendile"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "act_type",
			"caption" => t("Tegevuse t&uuml;&uuml;p"),
			"align" => "center"
		));

		foreach(get_current_company()->get_activity_stats_types() as $type_id => $type_name)
		{
			$t->define_field(array(
				"name" => "sa_type_".$type_id,
				"caption" => $type_name,
				"align" => "center",
				"parent" => "act_type"
			));
		}
	}

	private function _get_stats_entry_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_stats_entry_table($t);
		$this->act_stats_types = get_current_company()->get_activity_stats_types();

		if (!empty($arr["request"]["show_last"]))
		{
			$com_list = $arr["obj_inst"]->get_bug_comments(date_calc::get_day_start()-24*3600)->arr();
		}
		else
		{
			$com_list = $arr["obj_inst"]->get_bug_comments()->arr();
		}

		foreach($com_list as $task)
		{
			if ($task->add_wh > 0)
			{
				$this->_stats_entry_insert_row($t, $task);
			}
		}
	}

	private function _stats_entry_insert_row($t, $o)
	{
		$d = array(
			"name" => html::obj_change_url($o->parent()),
			"person" => html::obj_change_url(get_instance(CL_USER)->get_person_for_uid($o->createdby)),
			"type" => aw_ini_get("classes.{$o->class_id}.name"),
			"content" => nl2br(substr($o->comment, 0, 100)),
			"add_wh" => $o->add_wh,
			"on_bill" => html::checkbox(array(
				"name" => "d[".$o->id."][send_bill]",
				"value" => 1,
				"checked" => $o->send_bill,
			)),
			"hrs_cust" => html::textbox(array(
				"name" => "d[".$o->id."][hrs_cust]",
				"value" => $o->add_wh_cust,
				"size" => 5,
				"onfocus" => "el = getElementById('d[".$o->id."][send_bill]'); el.checked=true;"
			))
		);
		foreach($this->act_stats_types as $type_id => $type_name)
		{
			$d["sa_type_".$type_id] = html::radiobutton(array(
				"name" => "d[".$o->id."][act_type]",
				"value" => $type_id,
				"checked" => $o->activity_stats_type == $type_id
			));
		}
		$t->define_data($d);
	}

	private function _set_stats_entry_table($arr)
	{
		foreach($arr["obj_inst"]->get_bug_comments()->arr() as $task)
		{
			$mod = false;
			if ($task->send_bill != $arr["request"]["d"][$task->id]["send_bill"])
			{
				$task->send_bill = $arr["request"]["d"][$task->id]["send_bill"];
				$mod = true;
			}

			if ($task->send_bill && $mod)
			{
				// write hrs to cust
				$hrs = trim($arr["request"]["d"][$task->id]["hrs_cust"]);
				if (empty($hrs))
				{
					$tmp = $task->add_wh;
					// round to 30min
					$mins = $tmp - floor($tmp);
					if ($mins > 0.5)
					{
						$mins = 1;
					}
					else
					if ($mins > 0)
					{
						$mins = 0.5;
					}
					$hrs = floor($tmp) + $mins;
				}

				$task->add_wh_cust = $hrs;
			}
			else
			if (!$task->send_bill && $mod)
			{
				$task->add_wh_cust = 0;
			}

			if ($task->activity_stats_type != $arr["request"]["d"][$task->id]["act_type"])
			{
				$task->activity_stats_type = $arr["request"]["d"][$task->id]["act_type"];
				$mod = true;
			}

			if ($mod)
			{
				$task->save();
			}
		}
	}

	/**
		@attrib name=daily_stats_type_check nologin="1"
	**/
	function daily_stats_type_check($arr)
	{
		get_instance("users")->login(array("uid" => aw_ini_get("project.default_uid"), "password" => aw_ini_get("project.default_password")));
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"lang_id" => array(),
			"site_id" => array(),
		));

		$send = array();
		foreach($ol->arr() as $o)
		{
			if (!$this->can("view", $o->proj_mgr))
			{
				continue;
			}
			$eml = $o->prop("proj_mgr.email.mail");
			if (!is_email($eml))
			{
				continue;
			}

			$com_list = $o->get_bug_comments(date_calc::get_day_start()-24*3600)->arr();
			if (count($com_list) > 0)
			{
				// send mail to maintainer
				$send[$eml][] = $o->id;
			}
		}

		foreach($send as $email => $projs)
		{
			$ct = "Tere!\n\nTeie projektidesse on lisandunud tegevusi. Palun m2rkige nende tyybid:\n\n";
			foreach($projs as $proj)
			{
				$ct .= $this->mk_my_orb("change", array("id" => $proj, "group" => "stats_entry", "show_last" => 1), "project")."\n";
			}

			echo "send mail to $email <pre>$ct</pre><Br>";
			send_mail($email, t("Uued projekti tegevused"), $ct, "From: ".aw_ini_get("baseurl")." <info@struktuur.ee>");
		}
		die("all done");
	}

	function _get_stats_money_by_person_chart($arr)
	{
		$c2 = $arr["prop"]["vcl_inst"];
		$c2->set_type(GCHART_LINE_CHARTXY);
		$c2->set_size(array(
			"width" => 1000,
			"height" => 220,
		));
		$c2->set_colors(array("5511aa" , "55aa44"));
		$c2->add_fill(array(
			"area" => GCHART_FILL_BACKGROUND,
			"type" => GCHART_FILL_SOLID,
			"colors" => array(
				"color" => "e9e9e9",
			),
		));
		$time = array();
		$time_data = array();
		$all_data = $arr["obj_inst"]->get_rows_data();
		$payments = $arr["obj_inst"]->get_payments_data();
		$end = $arr["obj_inst"]->prop("end");
		$start = time();
		$result = array();

		$chart_data = $chart_payments = array();
		$tasks = $task_prices = array();
		foreach($all_data as $data)
		{
			if(!($data["time_real"] || $data["time_to_cust"]))
			{
				continue;
			}
			$date_day_start = date("wmy" , $data["date"]);
			if($end < $data["date"])
			{
				$end = $data["date"];
			}
			if($start > $data["date"])
			{
				$start = $data["date"];
			}
			$tasks[$data["task"]] = $data["task"];
		}

		foreach($payments as $p_data)
		{
			if($end < $p_data["date"])
			{
				$end = $p_data["date"];
			}
		}
		$end = $end+self::DAY_LENGTH_SECONDS;


		$tasks_ol = new object_list();
		$tasks_ol->add($tasks);
		foreach($tasks_ol->arr() as $to)
		{
			$task_prices[$to->id()] = $to->prop("hr_price");
		}

		if($end < $start + 15000000)
		{
			$start = $end - 15000000;
		}

		$round = round(4*($end-$start)/100 , -5);

		foreach($all_data as $data)
		{
			if(!($data["time_real"] || $data["time_to_cust"]))
			{
				continue;
			}
			$date_day_start = round($data["date"]/$round);
			$custh = $data["time_to_cust"];// ? $data["time_to_cust"] : $data["time_real"];
			$chart_data[$date_day_start] += $task_prices[$data["task"]] * $custh;
		}

		foreach($payments as $p_data)
		{
			$date_day_start = round($p_data["date"]/$round);
			$chart_payments[$date_day_start] += $p_data["sum"];
		}

		$sum = 0;
		$pay_sum = 0;
		$x = 0;

		$data0 = $data1 = $data2 = $labels = array();

		$my = "";
		while(true)
		{
			if($my != date("my" , $start))
			{
				$my = date("my" , $start);
				$labels[]= date("M Y" , $start);
			}
			$st= round($start/$round);
			$sum+= $chart_data[$st];
			$pay_sum += $chart_payments[$st];
			$data1[] = $sum;
			$data3[] = $pay_sum;
			if($start > $end)
			{
				break;
			}
			$start += $round;
			$x++;
		}

		if(sizeof($labels) > 19)
		{
			$rnd = round(sizeof($labels) / 19) + 1;

			foreach($labels as $key => $label)
			{
				if(($key % $rnd) != 0)
				{
					unset($labels[$key]);
				}
			}
		}

		$c2->set_labels($labels);
 		$c2->add_data(-1);
 		$c2->add_data($data1);
 		$c2->add_data(-1);
 		$c2->add_data($data3);

// 		$c2->add_data(array(10,20,40,80,90,95,99));
 //		$c2->add_data(array(20,30,40,50,60,70,80));
 //		$c2->add_data(-1);
 //		$c2->add_data(array(5,25,45,65,85));


		$c2->set_axis(array(
			GCHART_AXIS_RIGHT,
			GCHART_AXIS_BOTTOM
		));
		$left_axis = array();
		$sum = max($sum , $pay_sum);
		if ($sum > 0)
		{
			for($i = 0; $i <= $sum; $i+= $sum/4)
			{
				$left_axis[] = round($i, 0);
			}
		}
		$c2->add_axis_label(0, $left_axis);
		$c2->add_axis_label(1, $bot_axis);
		$c2->add_axis_style(1, array(
			"color" => "888888",
			"font" => 10,
			"align" => GCHART_ALIGN_CENTER,
		));
		$c2->set_grid(array(
			"xstep" => 30,
			"ystep" => 20,
		));
		$c2->set_title(array(
			"text" => t("T&ouml;id tehtud t&ouml;&ouml;d ja laekunud summad kokku ajaliselt"),
			"color" => "555555",
			"size" => 10,
		));
		$c2->set_legend(array(
			"labels" => array(
				t("Tehtud t&ouml;id summas"),
				t("Raha laekunud"),
			),
			"position" => GCHART_POSITION_LEFT,
		));
		//
	}

	/**
		@attrib name=add_cash_cow
		@param id required type=oid
			project id
	**/
	public function add_cash_cow($arr)
	{
		$o = obj($arr["id"]);
		{
			$o->add_cash_cow();
			return;
		}
	}

	/**
		@attrib name=add_expense_spot
		@param id required type=oid
			project id
	**/
	public function add_expense_spot($arr)
	{
		$o = obj($arr["id"]);
		{
			$o->add_expense_spot();
			return;
		}
	}

	/** returns project product selection
		@attrib api=1
		@returns array
	**/
	public function get_prod_selection()
	{
		$prods = array("" => t("--vali--"));
		// get prords from co
		$u = get_instance(CL_USER);
		$co = obj($u->get_current_company());
		$wh = $co->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
		if ($wh)
		{
			$wh_i = $wh->instance();
			$pkts = $wh_i->get_packet_list(array(
				"id" => $wh->id()
			));
			foreach($pkts as $pko)
			{
				$prods[$pko->id()] = $pko->name();
			}
		}
		return $prods;
	}

	/** returns bill unit selection
		@attrib api=1
		@returns array
	**/
	public function get_unit_selection()
	{
		if($this->unit_selection)
		{
			return $this->unit_selection;
		}
		// get prords from co
		$filter = array(
			"class_id" => CL_UNIT,
			"lang_id" => array(),
			"site_id" => array(),
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_UNIT => array(
					new obj_sql_func(OBJ_SQL_UNIQUE, "name", "objects.name"),
				)
			)
		);

		$names = $t->get_element_from_all("name");
		$prods[""] = t("M&auml;&auml;ramata");


		foreach($names as $id => $name)
		{
			if($name)
			{
				$prods[$this->get_unit_id($name)] = $name;
			}
		}
		$this->unit_selection = $prods;
		return $prods;
	}

	/** returns bill unit selection
		@attrib api=1
		@returns array
	**/
	public function get_suply_selection()
	{
		if($this->suply_selection)
		{
			return $this->suply_selection;
		}
		// get prords from co
		$filter = array(
			"class_id" => CL_SHOP_WAREHOUSE,
			new obj_predicate_limit(20)
		);

		$ol = new object_list($filter);
		$this->suply_selection = $ol->names();
		return $this->suply_selection;
	}

	private function get_unit_id($name)
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"name" => $name,
			new obj_predicate_limit(1)
		));
		$ids = $ol->ids();
		return reset($ids);
	}

	/**
		@attrib name=get_income_spot_table
		@param id required type=oid
			project id
		@param die optional type=boolean
	**/
	public function get_income_spot_table($arr)
	{
		$o = obj($arr["id"]);
		$id = $o->id();
		$val= html::button(array(
			"value" => t("Lisa uus tulukoht"),
			"name" => "add_cash_cow",
			"onclick" => "
				$.post('/automatweb/orb.aw?class=project&action=add_cash_cow',{id: ".$id."},function(data){load_new_data".$id."();});
				function load_new_data".$id."()
				{
				$.get('/automatweb/orb.aw',{class: 'project', action: 'get_income_spot_table', die: 1,id: '".$id."'}, function (html) {
				x=document.getElementById('project_income_table_div');
				x.innerHTML=html;});
				}",
		))."<br>";

		$incomes = $o->get_cash_cows();
		if($incomes->count())
		{
			foreach($incomes->ids() as $income)
			{
				$val.= "<hr>".html::div(array(
					"content" => html::div(array(
						"content" => html::bold(t("Projekti tulukoht")),
						"class" => "pais",
					)).$this->draw_expense_spots_table(array("id" => $income)),
					"id" => "project_expense_table_".$income,
					"border" => "3px solid rgb(0, 0, 0)",
					"padding" => "15px",
				))."<br><br><br><br><br>";
			}
		}
		if($arr["die"])
		{
			die(iconv(aw_global_get("charset"), "UTF-8", $val));
		}
		return $val;
	}

	private function do_fckng_table($arr = array())
	{
		$t2 = new vcl_table();
		$t2->define_field(array(
			"name" => "name",
			"align" => "right"
		));
		$t2->define_field(array(
			"name" => "value",
			"align" => "left"
		));
		foreach($arr as $id => $val)
		{
			$t2->define_data(array(
				"name" => $val["name"],
				"value" => $val["value"],
			));
		}
		return $t2->draw(array("no_titlebar" => 1));
	}

	/**
		@attrib name=save_income_table all_args=1
	**/
	public function save_income_table($arr)
	{
		$o = obj($arr["id"]);
		$vars = array("product" , "unit" , "amount" , "unit_price" , "incoming_income" , "ready");
		foreach($vars as $var)
		{
			$o->set_prop($var , $arr[$var]);
		}
		$o->save();
		return $o->id();
	}

	/**
		@attrib name=delete_expense all_args=1
	**/
	public function delete_expense($arr)
	{
		$o = obj($arr["id"]);
		if($o->class_id() == CL_CASH_COW || $o->class_id() == CL_CRM_EXPENSE_SPOT || $o->class_id() == CL_CRM_EXPENSE_SPOT_ROW)
		{
			$o->delete();
			return true;
		}
	}

	/**
		@attrib name=save_expense_spot_table all_args=1
	**/
	public function save_expense_spot_table($arr)
	{
		$o = obj($arr["id"]);
		$vars = array("product" , "unit" , "amount" ,  "incoming_income" , "ready","supplier");
		$row_vars = array("product" , "unit" , "amount" , "unit_price" , "supplier");

		foreach($vars as $var)
		{
			$o->set_prop($var , $arr[$var]);
		}
		$o->save();

		$rows = array();

		foreach($arr as $key => $val)
		{
			if(substr($key , 0 , 4) == "row_");
			$data = explode( "_" , $key);
			if (is_oid($data[1]))
			{
				$rows[] = $data[1];
			}
		}

		if(sizeof($rows))
		{
			$ol = new object_list();
			$ol -> add($rows);

			foreach($ol->arr() as $row)
			{
				foreach($row_vars as $var)
				{
					if($arr["row_".$row->id()."_".$var])
					{
						$row->set_prop($var , $arr["row_".$row->id()."_".$var]);
					}
				}
				$row->save();
			}
		}


		return $o->id();
	}


	/**
		@attrib name=draw_expense_spots_table
		@param id required type=oid
			project id
		@param die optional type=boolean
		@param change optional type=boolean
	**/
	public function draw_expense_spots_table($arr)
	{
		$o = obj($arr["id"]);
		$change = $arr["change"];

		$id = $o->id();
		$val= "";

		get_instance("vcl/table");
		$t = new vcl_table();

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Tulukoha nimetus"),
		));
		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("&Uuml;hiku v&auml;ljam&uuml;&uuml;gi hind"),
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
		));

		$t->define_data(array(
			"name" => $change ? html::select(array(
				"name" => "project_income[".$id."][product]",
				"value" => $o->prop("product"),
				"options" => $this->get_prod_selection(),
			)) :  $o->prop("product.name"),
			"unit" => $change ? html::select(array(
				"name" => "project_income[".$id."][unit]",
				"value" => $o->prop("unit"),
				"options" => $this->get_unit_selection(),
			)) :  $o->prop("unit.name"),
			"amount" => $change ? html::textbox(array(
				"name" => "project_income[".$id."][amount]",
				"value" => $o->prop("amount"),
				"size" => 5,
			)) : $o->prop("amount"),
			"price" => $change ? html::textbox(array(
				"name" => "project_income[".$id."][unit_price]",
				"value" => $o->prop("unit_price"),
				"size" => 5,
			)) :$o->prop("unit_price"),
			"sum" => $o->prop("unit_price")*$o->prop("amount"),
		));

		$val.= $t->draw();

		$real_ready = 53.38;
		$income = 1231231.43;
		$table_dat = array();
		$table_dat[] = array(
			"name" => t("Projektiosa valmidustase"),
			"value" => $real_ready. "%",
		);
		$table_dat[] = array(
			"name" => t("Viittulu "),
			"value" => $income,
		);
		$table_dat[] = array(
			"name" => t("Projektiosa valmidustase"),
			"value" => $change ? html::textbox(array(
				"name" => "project_income[".$id."][ready]",
				"value" => $o->prop("ready")
			)) : $o->prop("ready"),
		);
		$table_dat[] = array(
			"name" => t("Viittulu "),
			"value" => $change ? html::textbox(array(
				"name" => "project_income[".$id."][incoming_income]",
				"value" => $o->prop("incoming_income")
			)) : $o->prop("incoming_income"),
		);
		$val.= $this->do_fckng_table($table_dat);


		if($change)
		{
			$vars = array("product" , "unit" , "amount" , "unit_price" , "incoming_income" , "ready");
			$ajax_vars = array();
			foreach($vars as $var)
			{
				$ajax_vars[] = $var.": document.getElementsByName('project_income[".$id."][".$var."]')[0].value\n";

			}
		}

		$val.= html::button(array(
			"value" => t("Lisa uus kulukoht"),
			"name" => "add_expense_spot",
			"onclick" => "
				$.post('/automatweb/orb.aw?class=project&action=add_expense_spot',{id: ".$id."},function(data){load_new_data".$id."();});
				function load_new_data".$id."()
				{
				$.get('/automatweb/orb.aw',{class: 'project', action: 'draw_expense_spots_table', die: 1,id: '".$id."'}, function (html) {
				x=document.getElementById('project_expense_table_".$id."');
				x.innerHTML=html;});
				}",
		)).
		html::button(array(
			"value" => $change ? t("Salvesta tulukoht") : t("Muuda tulukohta"),
			"name" => "change_income_table",
			"onclick" =>  ($change ? ("$.post('/automatweb/orb.aw?class=project&action=save_income_table',{id: ".$id." , ".join(", " , $ajax_vars)."},function(data){load_new_data".$id."();});")
					: "load_new_data".$id."();").
				"
				function load_new_data".$id."()
				{
				$.get('/automatweb/orb.aw',{class: 'project', action: 'draw_expense_spots_table', die: 1,id: '".$id."',change: '".($change ? "0": "1")."'}, function (html) {
				x=document.getElementById('project_expense_table_".$id."');
				x.innerHTML=html;});
				}",
		)).
		html::button(array(
			"value" => t("Kustuta tulukoht"),
			"name" => "change_income_table",
			"onclick" =>  "$.get('/automatweb/orb.aw',{class: 'project', action: 'delete_expense', die: 1,id: '".$id."'}, function (html) {
					x=document.getElementById('project_expense_table_".$id."');
					x.parentNode.removeChild(x);})",
		))."<br>";

		$expenses = $o->get_expense_spots();
		if($expenses->count())
		{
			foreach($expenses->ids() as $expense)
			{
				$val.= "<hr>".html::div(array(
					"border" => "1px solid rgb(0, 0, 0);",
					"content" => $this->draw_expense_spot_table(array("id" => $expense)),
					"id" => "project_one_expense_table".$expense,
					"padding" => "15px",
				))."<br>";
			}
		}
		if($arr["die"])
		{
			die(iconv(aw_global_get("charset"), "UTF-8", $val));
		}
		return $val;
	}

	/**
		@attrib name=draw_expense_spot_table
		@param id required type=oid
			project id
		@param die optional type=boolean
		@param change optional type=boolean
	**/
	public function draw_expense_spot_table($arr)
	{
		$o = obj($arr["id"]);
		$c = $arr["change"];//makes stuff editable
		$id = $o->id();
		$rows = $o->get_rows();
		$ajax_vars = array();
		if($c)
		{
			$vars = array("product" , "unit" , "amount" , "incoming_income" , "ready","supplier");
			foreach($vars as $var)
			{
				$ajax_vars[] = $var.": document.getElementsByName('expense_spot[".$id."][".$var."]')[0].value\n";

			}
		}

		if($c && $rows->count())
		{
			$row_ajax_vars = array();
			$vars = array("product" , "unit" , "amount" , "unit_price" , "supplier");
			foreach($vars as $var)
			{
				foreach($rows->ids() as $rowid)
				{
					$row_ajax_vars[] = "row_".$rowid."_".$var.": document.getElementsByName('row[".$rowid."][".$var."]')[0].value\n";
				}
			}
		}

		$button = html::button(array(
			"value" => t("Lisa uus rida"),
			"name" => "add_expense_spot",
			"onclick" => "
				$.post('/automatweb/orb.aw?class=crm_expense_spot&action=add_expense_spot_row',{id: ".$id."},function(data){load_new_data".$id."();});
				function load_new_data".$id."()
				{
				$.get('/automatweb/orb.aw',{class: 'project', action: 'draw_expense_spot_table', die: 1,id: '".$id."'}, function (html) {
				x=document.getElementById('project_one_expense_table".$id."');
				x.innerHTML=html;});
				}",
		));
		$button2 = html::button(array(
			"value" => $c ? t("Salvesta kulukoht") : t("Muuda kulukohta"),
			"name" => "change_expense_spot",
			"onclick" =>  ($c ? ("$.post('/automatweb/orb.aw?class=project&action=save_expense_spot_table',{id: ".$id." , ".join(", " , $ajax_vars).($rows->count() ? (" , " . join(", " , $row_ajax_vars)) : "" )."},function(data){load_new_data".$id."();});")
					: "load_new_data".$id."();").
				"
				function load_new_data".$id."()
				{
				$.get('/automatweb/orb.aw',{class: 'project', action: 'draw_expense_spot_table', die: 1,id: '".$id."',change: '".($c ? "0": "1")."'}, function (html) {
				x=document.getElementById('project_one_expense_table".$id."');
				x.innerHTML=html;});
				}",
		));

		$delete_button = html::button(array(
			"value" => t("Kustuta tulukoht"),
			"name" => "change_income_table",
			"onclick" =>  "$.get('/automatweb/orb.aw',{class: 'project', action: 'delete_expense', die: 1,id: '".$id."'}, function (html) {
					x=document.getElementById('project_one_expense_table".$id."');
					x.parentNode.removeChild(x);})",
		));

		$val = "";


		get_instance("vcl/table");
		$t = new vcl_table();
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
		));
		$t->define_field(array(
			"name" => "spot_name",
//			"caption" => t("Nimetus"),
			"parent" => "name",
			"chgbgcolor" => "color"
		));
		$t->define_field(array(
			"name" => "row_name",
//			"caption" => t("Nimetus"),
			"parent" => "name",
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
			"chgbgcolor" => "color"
		));
		$t->define_field(array(
			"name" => "amount",
			"caption" => t("Kogus"),
			"chgbgcolor" => "color"
		));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("&Uuml;hiku omahind"),
			"chgbgcolor" => "color"
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa/Eelarve"),
			"chgbgcolor" => "color"
		));
		$t->define_field(array(
			"name" => "supplier",
			"caption" => t("Tarnija/Hankija"),
			"chgbgcolor" => "color"
		));
		$t->define_field(array(
			"name" => "delete",
			"caption" => t("X"),
			"chgbgcolor" => "color"
		));

		$t->set_header(sprintf(t("Projekti %s kulukoht %s"), "" , $o->prop("product.name")));
		//$t->set_header($o->name());


		$t->define_data(array(
			"spot_name" => $c ? html::select(array(
				"name" => "expense_spot[".$id."][product]",
				"value" => $o->prop("product"),
				"options" => $this->get_prod_selection(),
			)) :  $o->prop("product.name"),
			"unit" => $c ? html::select(array(
				"name" => "expense_spot[".$id."][unit]",
				"value" => $o->prop("unit"),
				"options" => $this->get_unit_selection(),
			)) :  $o->prop("unit.name"),
			"amount" => $c ? html::textbox(array(
				"name" => "expense_spot[".$id."][amount]",
				"value" => $o->get_amount(),
				"size" => 8,
			)) : $o->get_amount(),
			"supplier" => $c ? html::select(array(
				"name" => "expense_spot[".$id."][supplier]",
				"value" => $o->prop("supplier"),
				"options" => $this->get_suply_selection(),
			)) :$o->prop("supplier.name"),
			"sum" => $o->get_sum(),
			"color" => "gray",
		));

		if($rows->count())
		{
			foreach($rows->arr() as $row)
			{
				$t->define_data(array(
					"row_name" => $c ? html::select(array(
						"name" => "row[".$row->id()."][product]",
						"value" => $row->prop("product"),
						"options" => $this->get_prod_selection(),
					)) :  $row->prop("product.name"),
					"unit" => $c ? html::select(array(
						"name" => "row[".$row->id()."][unit]",
						"value" => $row->prop("unit"),
						"options" => $this->get_unit_selection(),
					)) :  $row->prop("unit.name"),
					"amount" => $c ? html::textbox(array(
						"name" => "row[".$row->id()."][amount]",
						"value" => $row->prop("amount"),
						"size" => 8,
					)) : $row->prop("amount"),
					"price" => $c ? html::textbox(array(
						"name" => "row[".$row->id()."][unit_price]",
						"value" => $row->prop("unit_price"),
						"size" => 8,
					)) : $row->prop("unit_price"),
					"sum" => $row->prop("unit_price")*$row->prop("amount"),
					"supplier" => $c ? html::select(array(
						"name" => "row[".$row->id()."][supplier]",
						"value" => $row->prop("supplier"),
						"options" => $this->get_suply_selection(),
					)) : $row->prop("supplier.name"),
					"delete" => html::href(array(
						"url" => "javascript:;",
						"onclick" => '$.get("/automatweb/orb.aw",{class: "project", action: "delete_expense", die: 1,id: "'.$row->id().'"}, function (asd) {
							$.get("/automatweb/orb.aw",{class: "project", action: "draw_expense_spot_table", die: 1,id: "'.$id.'"}, function (html) {
								x=document.getElementById("project_one_expense_table'.$id.'");
								x.innerHTML=html;
							});}
						);',
						"caption" => html::img(array(
							"url" => aw_ini_get("baseurl").'/automatweb/images/icons/delete.gif',
						)),
					)),
				));

				$amount = 0;

				 if(!$c) $t->define_data(array(
					"row_name" => "	 	 -".t("s.h. juba kasutatud"),
					//"unit" => $o->prop("unit.name"),
					"amount" => $amount,
					"price" => $row->prop("unit_price"),
					"sum" => $row->prop("unit_price")*$amount,
	//				"supplier" => $o->prop("supplier"),
				));
			}
		}

		$t->define_data(array(
			"spot_name" => "<br>",
			"color" => "white",
		));

		$t->define_data(array(
			"spot_name" => html::bold(t("Kokku:")),
			"row_name" => html::bold(t("M&auml;&auml;ratud kogus")),
			"unit" => html::bold(t("M&auml;&auml;ramata kogus")),
			"amount" => html::bold(t("Jooksev maksumus")),
			"price" => html::bold(t("M&auml;&auml;ramata eelarve osa")),
			"sum" => html::bold(t("Eelarvest &uuml;le l&auml;inud summa")),
			"color" => "white",
		));

		$assigned = $o->get_assigned_amount();
		$t->define_data(array(
			"row_name" => $assigned,
			"unit" => $o->get_amount() - $assigned,
			"amount" => 0,
			"price" => 13300,
			"sum" => 300,
		));
		$t->define_data(array(
			"spot_name" => "	 	 -".t("s.h. juba kasutatud"),
			"unit" => 70,
			"price" => 7100,
		));
		$val.= $button.$button2.$delete_button."<br>";
		$val.= $t->draw();

		$real_ready = 53.38;
		$income = 1231231.43;
		$table_dat = array();
		$table_dat[] = array(
			"name" => t("Projektiosa valmidustase"),
			"value" => $real_ready. "%",
		);
		$table_dat[] = array(
			"name" => t("Viittulu "),
			"value" => $income,
		);
		$table_dat[] = array(
			"name" => t("Projektiosa valmidustase"),
			"value" =>  $c ? html::textbox(array(
				"name" => "expense_spot[".$id."][ready]",
				"value" => $o->prop("ready")
			)) : $o->prop("ready"),
		);
		$table_dat[] = array(
			"name" => t("Viittulu "),
			"value" =>  $c ? html::textbox(array(
				"name" => "expense_spot[".$id."][incoming_income]",
				"value" => $o->prop("incoming_income")
			)) : $o->prop("incoming_income"),
		);
		$val.= $this->do_fckng_table($table_dat);

		if($arr["die"])
		{
			die(iconv(aw_global_get("charset"), "UTF-8", $val));
		}
		return $val;
	}


	private function _get_income_spot_table($arr)
	{
		$arr["prop"]["value"] = $this->get_income_spot_table(array("id" => $arr["obj_inst"]->id()));

		$arr["prop"]["value"] = html::div(array(
			"content" => $arr["prop"]["value"],
			"id" => "project_income_table_div",
		));
		return $val;
	}

}
