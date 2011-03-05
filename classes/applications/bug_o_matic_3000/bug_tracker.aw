<?php
// bug_tracker.aw - BugTrack

define("MENU_ITEM_LENGTH", 20);

/*

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@default table=objects
@default group=general_sub

	@property name type=textbox table=objects field=name
	@caption Nimi

	@property object_type type=relpicker reltype=RELTYPE_OBJECT_TYPE table=objects field=meta method=serialize
	@caption Bugi objekti t&uuml;&uuml;p

	@property bug_folder type=relpicker reltype=RELTYPE_FOLDER table=objects field=meta method=serialize
	@caption Bugide kataloog

	@property do_folder type=relpicker reltype=RELTYPE_FOLDER table=objects field=meta method=serialize
	@caption Arendustellimuste kaust

	@property bug_by_class_parent type=relpicker reltype=RELTYPE_BUG table=objects field=meta method=serialize
	@caption Klasside puusse lisatud bugide asukoht

	@property order_tree_conf type=select field=meta method=serialize table=objects
	@caption Tellimuste puus kuvatakse

	@property tables_show_items type=textbox datatype=int default=50 table=objects field=meta method=serialize
	@caption Ridu tabelites

@default group=projects

	@property proj_toolbar type=toolbar no_caption=1

	@layout proj_split type=hbox width=25%:75%

		@layout proj_left type=vbox parent=proj_split

			@layout proj_tree_lay type=vbox closeable=1 area_caption=Projektide&nbsp;puu parent=proj_left

				@property proj_tree type=treeview no_caption=1 parent=proj_tree_lay

			@layout proj_search_lay type=vbox closeable=1 area_caption=Projektide&nbsp;otsing parent=proj_left

				@property proj_search_name type=textbox store=no parent=proj_search_lay size=33 captionside=top
				@caption Projekti nimi

				@property proj_search_cust type=textbox store=no parent=proj_search_lay size=33 captionside=top
				@caption Klient

				@property proj_search_part type=text size=28 parent=proj_search_lay store=no captionside=top
				@caption Osaleja

				@property proj_search_state type=chooser store=no parent=proj_search_lay  captionside=top
				@caption Staatus

				@property proj_search_code type=textbox store=no parent=proj_search_lay size=33 captionside=top
				@caption Projekti kood

				@property proj_search_arh_code type=textbox store=no parent=proj_search_lay size=33 captionside=top
				@caption Arhiveerimistunnus

				@property proj_search_proj_mgr type=textbox store=no parent=proj_search_lay size=33 captionside=top
				@caption Projektijuht

				@property proj_search_dl_from type=date_select store=no parent=proj_search_lay  captionside=top format=day_textbox,month_textbox,year_textbox
				@caption T&auml;htaeg alates

				@property proj_search_dl_to type=date_select store=no parent=proj_search_lay  captionside=top format=day_textbox,month_textbox,year_textbox
				@caption T&auml;htaeg kuni

				@property proj_search_end_from type=date_select store=no parent=proj_search_lay  captionside=top format=day_textbox,month_textbox,year_textbox
				@caption L&otilde;pp alates

				@property proj_search_end_to type=date_select store=no parent=proj_search_lay  captionside=top format=day_textbox,month_textbox,year_textbox
				@caption L&otilde;pp kuni

				@property proj_search_sbt type=submit  parent=proj_search_lay no_caption=1
				@caption Otsi

		@layout proj_right type=vbox parent=proj_split

			@property proj_tbl1 type=table no_caption=1 parent=proj_right

			@property proj_tbl2 type=table no_caption=1 parent=proj_right

@default group=by_default,by_prop

	@property bug_tb type=toolbar no_caption=1 group=bugs,by_default,by_prop

	@layout bug type=hbox width=20%:80%

		@layout bug_tree type=vbox parent=bug closeable=1 area_caption=Arendus&uuml;lesanded

			@property bug_tree type=treeview parent=bug_tree no_caption=1

		layout bug_table type=vbox parent=bug

			@property bug_list type=text parent=bug no_caption=1 group=bugs,archive,by_default,by_prop


@default group=unestimated_bugs

	@property unset_p type=text store=no
	@caption Kelle buge n&auml;idata

	@property unset_table type=table store=no no_caption=1

@default group=mail_settings

	@property mail_identity type=relpicker reltype=RELTYPE_IMAP field=meta method=serialize
	@caption Meili identiteet

	@property mail_default_folder type=relpicker reltype=RELTYPE_MAIL_DEF_FOLDER field=meta method=serialize
	@caption Vaikimis kataloog meili bugidele

	@property send_monitor_mails type=checkbox ch_value=1 default=1 field=meta method=serialize
	@caption Saada j&auml;lgijatele meile

	@property send_newwho_mails type=checkbox ch_value=1 default=0 field=meta method=serialize
	@caption Saada uuele tegijale alati meil

	@property dorder_mail_contents type=textarea rows=10 cols=50 field=meta method=serialize
	@caption Arendustellimuse loomise meil

@default group=bug_apps

		@property apps_tb type=toolbar no_caption=1 store=no
		@property apps_table type=table no_caption=1 store=no

@default group=commits

	@property commits_table type=table store=no no_caption=1

@default group=search

	@property search_tb type=toolbar store=no no_caption=1
	@caption Otsingu toolbar

@layout s_top_v type=hbox width=50%:50%

	@layout s_name_lay type=vbox closeable=1 area_caption=Sisu parent=s_top_v

		@property s_name type=textbox store=no parent=s_name_lay size=15 captionside=top
		@caption Nimi

		@property s_oid type=textbox store=no parent=s_name_lay size=10 captionside=top
		@caption ID

		@property s_bug_content type=textbox store=no parent=s_name_lay captionside=top
		@caption Sisu

		@property s_find_parens type=checkbox ch_value=1 store=no parent=s_name_lay no_caption=1 captionside=top
		@caption Leia ka buge, millel on alambuge

	@layout s_date_lay type=vbox closeable=1 area_caption=Aeg parent=s_top_v

		@layout s_date_lay_top type=vbox parent=s_date_lay

			@property s_date_type type=chooser store=no parent=s_date_lay_top captionside=top
			@caption Mille j&auml;rgi

			@property s_date_from type=datetime_select store=no parent=s_date_lay_top captionside=top default=-1
			@caption Alates

			@property s_date_to type=datetime_select store=no parent=s_date_lay_top captionside=top default=-1
			@caption Kuni

		@layout s_date_lay_bot width=30%:70% type=hbox parent=s_date_lay

			@property s_date_time_from type=textbox size=4 store=no parent=s_date_lay_bot captionside=top
			@caption Kulunud aeg alates (h)

			@property s_date_time_to type=textbox size=4 store=no parent=s_date_lay_bot captionside=top
			@caption Kulunud aeg kuni (h)

@layout s_top type=hbox width=50%:50%

	@layout s_type_lay type=vbox closeable=1 area_caption=Klass parent=s_top

		@property s_bug_type type=textbox store=no parent=s_type_lay size=15 captionside=top
		@caption T&uuml;&uuml;p

		@property s_bug_class type=select store=no parent=s_type_lay captionside=top
		@caption Klass

		@property s_bug_component type=textbox store=no parent=s_type_lay captionside=top
		@caption Komponent

                @property s_bug_app type=textbox store=no parent=s_type_lay captionside=top
                @caption Rakendus

	@layout s_cut_lay type=vbox closeable=1 area_caption=Klient parent=s_top

		@property s_customer type=textbox store=no parent=s_cut_lay size=15 captionside=top
		@caption Klient

		@property s_project type=textbox store=no parent=s_cut_lay captionside=top
		@caption Projekt

		@property s_deadline type=date_select default=-1 store=no parent=s_cut_lay captionside=top
		@caption T&auml;htaeg

@layout s_bott type=hbox width=50%:50%

	@layout s_status_lay type=vbox closeable=1 area_caption=Staatus parent=s_bott

		@layout s_status_lay_top type=hbox width=30%:70% parent=s_status_lay

			@property s_bug_status type=select store=no multiple=1 parent=s_status_lay_top size=3 captionside=top
			@caption Staatus

			@property s_cust_status type=select store=no multiple=1 parent=s_status_lay_top size=3 captionside=top
			@caption Kliendi staatus

		@layout s_status_lay_bot type=vbox parent=s_status_lay

		@property s_feedback_p type=textbox size=15 store=no parent=s_status_lay_bot captionside=top
		@caption Tagasiside kellelt

		@property s_bug_priority type=select store=no parent=s_status_lay_bot captionside=top
		@caption Prioriteet

		@property s_bug_severity type=select store=no parent=s_status_lay_bot captionside=top
		@caption T&otilde;sidus

		@property s_finance_type type=select store=no parent=s_status_lay_bot captionside=top
		@caption Kulud kaetakse

	@layout s_who_l type=vbox closeable=1 area_caption=Osalejad parent=s_bott

		@layout s_who_empty_l type=hbox parent=s_who_l

			@property s_who type=textbox store=no parent=s_who_empty_l size=15 captionside=top
			@caption Kellele

			@property s_who_empty type=checkbox ch_value=1 store=no parent=s_who_empty_l captionside=top no_caption=1
			@caption T&uuml;hi

		@property s_monitors type=textbox store=no parent=s_who_l captionside=top
		@caption J&auml;lgijad

		@property s_bug_mail type=textbox store=no parent=s_who_l captionside=top
		@caption Bugmail CC

		@property s_createdby type=textbox store=no size=15 parent=s_who_l captionside=top
		@caption Looja


	@property s_sbt type=submit store=no no_caption=1
	@caption Otsi

	@property search_res type=table store=no no_caption=1
	@caption Otsingu tulemused

@default group=search_list

	@property saved_searches type=table store=no no_caption=1

	@property delete_saved type=submit
	@caption Kustuta

@default group=charts
@default group=gantt_chart

	@property gantt_p type=text store=no group=gantt_chart,proj_gantt
	@caption Kelle buge n&auml;idata

	@property gantt_end type=date_select store=no
	@caption Ajavahemiku l&otilde;pp

	@property gantt type=text store=no no_caption=1

	@property gantt_summary type=text store=no
	@caption Kokkuv&otilde;te

	@property gantt_legend type=text store=no
	@caption Legend

@default group=complete

	@property complete_table type=table store=no no_caption=1

@default group=my_bugs_stat

	@property my_bugs_stat_p type=text store=no
	@caption Kelle buge n&auml;idata

	@property my_bugs_stat_table type=table no_caption=1
	@caption Minuga seotud bugid

	@property my_bugs_stat_start type=date_select store=no
	@caption Ajavahemiku algus

	@property my_bugs_stat_end type=date_select store=no
	@caption Ajavahemiku l&otilde;pp

@default group=settings_people

	@property sp_tb type=toolbar store=no no_caption=1

	@property sp_table type=table store=no
	@caption Valitud isikud

	@property sp_p_name type=textbox store=no
	@caption Isik

	@property sp_p_co type=textbox store=no
	@caption Organisatsioon

	@property sp_sbt type=submit
	@caption Otsi

	@property sp_s_res type=table store=no
	@caption Otsingu tulemused

@default group=settings_g

	property agroup type=relpicker reltype=RELTYPE_AGROUP field=meta method=serialize multiple=1 store=connect
	caption Grupid, mis saavad buge sulgeda

	@property fb_folder type=relpicker reltype=RELTYPE_FB_FOLDER field=meta method=serialize
	@caption Tagasiside bugide kaust

	@property default_bug_parent type=relpicker reltype=RELTYPE_FB_FOLDER field=meta method=serialize
	@caption Vaikimisi bugide kaust

	@property bug_type_folder type=relpicker reltype=RELTYPE_BUG_TYPE_FOLDER field=meta method=serialize
	@caption Bugi t&uuml;&uuml;pide kaust

	@property def_notify_list type=textbox table=objects field=meta method=serialize
	@caption Bugi kommentaaride CC

	@property bug_def_deadline type=textbox size=3 table=objects field=meta method=serialize
	@caption Bugi default t&auml;htaeg (p&auml;eva)

	@property cvs2uidmap type=textarea rows=7 cols=20 table=objects field=meta method=serialize
	@caption Kasutajanimede kaart
	@comment Formaat: cvs_kasutaja=aw_kasutaja\ncvs_kasutaja=aw_kasutaja

	@property default_cfgmanager type=relpicker reltype=RELTYPE_CFGMGR table=objects field=meta method=serialize
	@caption Vaikimisi seadete haldur

	@property bug_only_bt_ppl type=checkbox ch_value=1 table=objects field=meta method=serialize
	@caption J&auml;lgijateks ainult valitud inimesed

	@property finance_required type=checkbox ch_value=1 field=meta method=serialize
	@caption Kulude katmine tuleb m&auml;&auml;rata

	@property combined_priority_formula type=textarea rows=30 cols=60 table=objects field=meta method=serialize
	@caption Prioriteedivalem
	@comment Valem mida kasutatakse kombineeritud prioriteedi arvutamiseks. Formaat: php, tagastatav prioriteet muutujas $p. Muutujad: $sp_lut - staatustele vastavad prioriteedid (default array(BUG_OPEN => 100,BUG_INPROGRESS => 110,BUG_DONE => 70,BUG_TESTED => 60,BUG_CLOSED => 50,BUG_INCORRECT => 40,BUG_NOTREPEATABLE => 40,BUG_NOTFIXABLE => 40,BUG_FATALERROR => 200,BUG_FEEDBACK => 130)), $bs - ylesande staatus, $cp - kliendi prioriteet, $pp - projekti prioriteet, $bp - ylesande prioriteet, $bl - ylesande prognoositud tunde, $bi - ylesande t6sidus, $dd - t2htaeg. N2ide: $p = $cp + $pp + $bp;

@default group=settings_statuses

	@property statuses_bug_tbl type=table store=no no_caption=1
	@property statuses_devo_tbl type=table store=no no_caption=1

@default group=reqs

	@property reqs_tb type=toolbar store=no no_caption=1

	@layout reqs_tt type=hbox width=30%:70%

		@layout reqs_tree type=vbox parent=reqs_tt closeable=1 area_caption=N&otilde;uete&nbsp;kategooriad

			@property reqs_tree type=treeview store=no no_caption=1 parent=reqs_tree

		@property reqs_table type=table store=no no_caption=1 parent=reqs_tt

@default group=reqs_proj

	@layout reqs_p_tt type=hbox width=30%:70%

		@layout reqs_p_tree type=vbox parent=reqs_p_tt closeable=1 area_caption=Projektid

			@property reqs_p_tree type=treeview store=no no_caption=1 parent=reqs_p_tree

		@property reqs_p_table type=table store=no no_caption=1 parent=reqs_p_tt

@default group=devo

	@property dev_orders_tb type=toolbar store=no no_caption=1

	@layout dev_orders_h type=hbox width=30%:70%

		@layout dev_orders_tree_v type=vbox parent=dev_orders_h closeable=1 area_caption=Arendustellimuste&nbsp;kategooriad

			@property dev_orders_tree type=treeview store=no no_caption=1 parent=dev_orders_tree_v

		@property dev_orders_table type=table store=no no_caption=1 parent=dev_orders_h

@default group=problems_list

	@property problems_tb type=toolbar store=no no_caption=1
	@property problems_table type=table store=no no_caption=1

@default group=reqs_cust

	@layout reqs_c_tt type=hbox width=30%:70%

		@layout reqs_c_tree type=vbox parent=reqs_c_tt closeable=1 area_caption=Tellijad

			@property reqs_c_tree type=treeview store=no no_caption=1 parent=reqs_c_tree

		@property reqs_c_table type=table store=no no_caption=1 parent=reqs_c_tt

@default group=devo_proj

	@layout devo_p_tt type=hbox width=30%:70%

		@layout devo_p_tree type=vbox parent=devo_p_tt closeable=1 area_caption=Projektid

			@property devo_p_tree type=treeview store=no no_caption=1 parent=devo_p_tree

		@property devo_p_table type=table store=no no_caption=1 parent=devo_p_tt


@default group=devo_cust

	@layout devo_c_tt type=hbox width=30%:70%

		@layout devo_c_tree type=vbox parent=devo_c_tt closeable=1 area_caption=Tellijad

			@property devo_c_tree type=treeview store=no no_caption=1 parent=devo_c_tree

		@property devo_c_table type=table store=no no_caption=1 parent=devo_c_tt

@default group=problems_units

	@property pu_tb type=toolbar no_caption=1 store=no

	@layout pu_h type=hbox width=30%:70%

		@layout pu_tree_b type=vbox parent=pu_h closeable=1 area_caption=Osakonnad

			@property pu_tree type=treeview store=no no_caption=1 parent=pu_tree_b

		@property pu_table type=table store=no no_caption=1 parent=pu_h

@default group=problems_proj

	@property pp_tb type=toolbar no_caption=1 store=no

	@layout pp_h type=hbox width=30%:70%

		@layout pp_tree_b type=vbox parent=pp_h closeable=1 area_caption=Projektid

			@property pp_tree type=treeview store=no no_caption=1 parent=pp_tree_b

		@property pp_table type=table store=no no_caption=1 parent=pp_h

@default group=problems_req

	@property pr_tb type=toolbar no_caption=1 store=no

	@layout pr_h type=hbox width=30%:70%

		@layout pr_tree_b type=vbox parent=pr_h closeable=1 area_caption=N&otilde;uded

			@property pr_tree type=treeview store=no no_caption=1 parent=pr_tree_b

		@property pr_table type=table store=no no_caption=1 parent=pr_h

@default group=stat_hrs_overview

	@property stat_hrs_errs type=table store=no no_caption=1
	@property stat_hrs_overview type=table store=no no_caption=1
	@property stat_hrs_detail type=table store=no no_caption=1

	@layout stat_hrs_o type=vbox
	@layout stat_hrs_range type=hbox parent=stat_hrs_o
		@property stat_hrs_start type=date_select store=no parent=stat_hrs_range
		@caption Alates

		@property stat_hrs_end type=date_select store=no parent=stat_hrs_range
		@caption Kuni

		@layout stat_hrs_s type=vbox parent=stat_hrs_o

		@property stat_hr_bugs type=checkbox store=no ch_value=1 default=1 parent=stat_hrs_s
		@caption &Uuml;lesanded

		@property stat_hr_meetings type=checkbox store=no ch_value=1 default=1 parent=stat_hrs_s
		@caption Kohtumised

		@property stat_hr_tasks type=checkbox store=no ch_value=1 default=1 parent=stat_hrs_s
		@caption Toimetused

		@property stat_hr_calls type=checkbox store=no ch_value=1 default=1 parent=stat_hrs_s
		@caption K&otilde;ned

	@property stat_hrs_submit type=submit store=no
	@caption Otsi


@default group=stat_proj_overview

		@property stat_proj_hrs_start type=date_select store=no
		@caption Alates

		@property stat_proj_hrs_end type=date_select store=no
		@caption Kuni

		@property stat_proj_ppl type=chooser store=no multiple=1
		@caption Inimesed

	@property stat_proj_detail type=table store=no no_caption=1
	@property stat_proj_detail_b type=table store=no no_caption=1

	@layout stat_hrs_s type=vbox parent=stat_hrs_o

		@property stat_proj_bugs type=checkbox store=no ch_value=1 default=1 no_caption=1 parent=stat_hrs_s
		@caption &Uuml;lesanded

		@property stat_proj_meetings type=checkbox store=no ch_value=1 default=1 no_caption=1 parent=stat_hrs_s
		@caption Kohtumised

		@property stat_proj_tasks type=checkbox store=no ch_value=1 default=1 no_caption=1 parent=stat_hrs_s
		@caption Toimetused

		@property stat_proj_calls type=checkbox store=no ch_value=1 default=1 no_caption=1 parent=stat_hrs_s
		@caption K&otilde;ned

@default group=proj_gantt

	@property proj_gantt_end type=date_select store=no
	@caption Ajavahemiku l&otilde;pp

	@property proj_gantt type=text store=no no_caption=1
	@property proj_bug_gantt type=text store=no no_caption=1

@groupinfo general_sub caption="&Uuml;ldine" parent=general
@groupinfo settings_people caption="Isikud" submit=no parent=general
@groupinfo settings_g caption="Muud seaded" parent=general
@groupinfo unestimated_bugs caption="Ennustamata bugid" parent=general
@groupinfo reminders caption="Teavitused" parent=general
@groupinfo mail_settings caption="Meiliseaded" parent=general
@groupinfo bug_apps caption="Rakendused" parent=general
@groupinfo settings_statuses caption="Staatused" parent=general

@groupinfo projects caption="Projektid"

@groupinfo reqs_main caption="N&otilde;uded"

	@groupinfo reqs parent=reqs_main caption="N&otilde;uete puu" submit=no
	@groupinfo reqs_proj caption="Projektid" parent=reqs_main submit=no
	@groupinfo reqs_cust caption="Tellijad isikud" parent=reqs_main submit=no

@groupinfo dev_orders caption="Tellimused"

	@groupinfo devo parent=dev_orders caption="Sisestamine" submit=no
	@groupinfo devo_proj caption="Projektid" parent=dev_orders submit=no
	@groupinfo devo_cust caption="Tellijad isikud" parent=dev_orders submit=no

@groupinfo bugs caption="&Uuml;lesanded" submit=no

	@groupinfo by_default caption="&Uuml;lesanded" parent=bugs submit=no
	@groupinfo by_prop caption="Kategooriad" parent=bugs submit=no
	@groupinfo commits caption="Commitid" parent=bugs submit=no
	@groupinfo search caption="Otsing" submit_method=get save=no parent=bugs
	@groupinfo search_list caption="Salvestatud otsingud" parent=bugs
	@groupinfo archive caption="Arhiiv" submit=no parent=bugs

@groupinfo problems caption="Probleemid"

	@groupinfo problems_list caption="Nimekiri" parent=problems submit=no
	@groupinfo problems_units caption="Osakondade kaupa" parent=problems submit=no
	@groupinfo problems_proj caption="Projektide kaupa" parent=problems submit=no
	@groupinfo problems_req caption="N&otilde;uete kaupa" parent=problems submit=no

@groupinfo charts caption="Kaardid" submit=no
	@groupinfo gantt_chart caption="Gantti diagramm" parent=charts
	@groupinfo complete caption="Valmis (minu lisatud)" parent=charts submit=no
	@groupinfo my_bugs_stat caption="Minu Bugide stat" parent=charts
	@groupinfo stat_hrs_overview caption="T&ouml;&ouml;aja &uuml;levaade" parent=charts
	@groupinfo stat_proj_overview caption="Projektide &uuml;levaade" parent=charts
	@groupinfo proj_gantt caption="Projektide gantt" parent=charts



@reltype MONITOR value=1 clid=CL_CRM_PERSON
@caption J&auml;lgija

@reltype OBJECT_TYPE value=2 clid=CL_OBJECT_TYPE
@caption Objekti t&uuml;&uuml;p

@reltype FOLDER value=3 clid=CL_MENU
@caption Kataloog

@reltype IMP_P value=4 clid=CL_CRM_PERSON
@caption Oluline isik

@reltype BUG value=5 clid=CL_BUG
@caption Bugi

@reltype IMAP value=6 clid=CL_PROTO_IMAP
@caption Imap

@reltype MAIL_DEF_FOLDER value=7 clid=CL_BUG
@caption Meilitud bugide kataloog

@reltype AGROUP value=8 clid=CL_GROUP
@caption Admin grupp

@reltype CFGMGR value=9 clid=CL_CFGMANAGER
@caption Seadete haldur

@reltype DEVO_FOLDER value=10 clid=CL_MENU,CL_DEVELOPMENT_ORDER_CAT
@caption Tellimuste kataloog

@reltype FB_FOLDER value=11 clid=CL_MENU,CL_BUG,CL_BUG_TRACKER
@caption Tagasiside bugide kaust

@reltype OWNER value=12 clid=CL_CRM_COMPANY
@caption Omanik

@reltype BUG_TYPE_FOLDER value=13 clid=CL_META,CL_MENU
@caption Bugi t&uuml;&uuml;pide kaust
*/

class bug_tracker extends class_base
{
	private $tables_show_items = 50;
	private $_rq_lev;
	var $combined_priority_formula;

	function bug_tracker()
	{
		$this->init(array(
			"tpldir" => "applications/bug_o_matic_3000/bug_tracker",
			"clid" => CL_BUG_TRACKER
		));
		$this->bug_i = get_instance(CL_BUG);
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;

		if("bugs" === $this->use_group)
		{
			$arr["request"]["group"] = "by_default";
		}
		elseif ("by_default" === $this->use_group)
		{
			$this->sort_type = "parent";
			aw_session_set("bug_tree_sort",array("name" => "parent"));
		}
		elseif ("by_prop" === $this->use_group)
		{
			$this->sort_type = "cat";
			aw_session_set("bug_tree_sort",array("name" => "cat"));
		}

		if ($prop["name"][0] === "s" && $prop["name"][1] === "_" && isset($arr["request"][$prop["name"]]))
		{
			$prop["value"] = $arr["request"][$prop["name"]];
		}

		if (substr($prop["name"], 0, 4) === "reqs")
		{
			static $r_i;
			if (!$r_i)
			{
				$r_i = new bt_req_impl();
			}
			$fn = "_get_".$prop["name"];
			return $r_i->$fn($arr);
		}
		if (substr($prop["name"], 0, 4) === "devo" || substr($prop["name"], 0, 5) === "dev_o")
		{
			static $d_i;
			if (!$d_i)
			{
				$d_i = new bt_devo_impl();
			}
			$fn = "_get_".$prop["name"];
			return $d_i->$fn($arr);
		}
		if (isset($prop["group"]) && substr((string)$prop["group"], 0, 8) === "problems")
		{
			static $p_i;
			if (!$p_i)
			{
				$p_i = new bt_problems_impl();
			}
			$fn = "_get_".$prop["name"];
			return $p_i->$fn($arr);
		}

		if(isset($prop["group"]) && (string)$prop["group"] === "projects")
		{
			static $proj_i;
			if (!$proj_i)
			{
				$proj_i = new bt_projects_impl();
			}
			$fn = "_get_".$prop["name"];
			if(method_exists($proj_i, $fn))
			{
				return $proj_i->$fn($arr);
			}
			elseif(strpos($prop["name"], "proj_search") !== false && $prop["type"] !== "submit")
			{
				$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : "";
				if(!$prop["value"] && strpos($prop["type"], "date") !== false)
				{
					$prop["value"] = -1;
				}
			}
		}

		switch($prop["name"])
		{
			case "order_tree_conf":
				$prop["options"] = array(
					0 => 'Kategooriad',
					1 => 'Alambugidega bugid'
				);
				break;
			case "unset_table":
				$this->_unestimated_table($arr);
				break;

			case "bug_tb":
				$this->_bug_toolbar($arr);
				break;

			case "bug_tree":
				$this->_bug_tree($arr);
				break;

			case "bug_list":
				$this->_bug_list($arr);
				break;

			case "cat":
				if(isset($arr["request"]["cat"]) and $this->can("view", $arr["request"]["cat"]))
				{
					$prop["value"] = $arr["request"]["cat"];
				}
				break;

			case "search_res":
				$this->_search_res($arr);
				break;

			case "s_date_type":
				$prop["options"] = array(
					1 => t("Loomise j&auml;rgi"),
					2 => t("Muutmise j&auml;rgi"),
					3 => t("Viimane kommentaar"),
				);
				break;

			case "s_date_from":
			case "s_date_to":
				if(empty($prop["value"]))
				{
					$prop["value"] = -1;
				}
				break;

			case "s_bug_priority":
			case "s_bug_severity":
				$i = get_instance(CL_BUG);
				$prop["options"] = array("" => "") + $i->get_priority_list();
				break;

			case "s_bug_status":
			case "s_cust_status":
				$i = get_instance(CL_BUG);
				$prop["options"] = array("" => "") + $i->get_status_list();
				break;

			case "s_bug_class":
				$i = get_instance(CL_BUG);
				$prop["options"] = array("" => "") + $i->get_class_list();
				break;

			case "search_tb":
				$this->_search_tb($arr);
				break;

			case "saved_searches":
				$this->_saved_searches($arr);
				break;

			case "gantt":
				$this->_gantt($arr);
				break;

			case "gantt_end":
				$udata = $arr["obj_inst"]->meta("gantt_user_ends");
				$cur = aw_global_get("uid_oid");
				$days = 7;
				if(!empty($udata[$cur]))
				{
					$days = $udata[$cur];
				}
				$prop["value"] = time() + ($days-1) * 24 * 60 * 60;
				break;

			case "gantt_p":
			case "unset_p":
			case "my_bugs_stat_p":
				if (isset($arr["request"]["filt_p"]) and $this->can("view", $arr["request"]["filt_p"]))
				{
					$p = obj($arr["request"]["filt_p"]);
				}
				else
				{
					$u = get_instance(CL_USER);
					$p = obj($u->get_current_person());
				}
				$co = new crm_company();
				$c = new popup_menu();
				$c->begin_menu("bt_g");
				$ppl = $this->get_people_list($arr["obj_inst"]);
				foreach($ppl as $p_id => $p_n)
				{
					$c->add_item(array(
						"text" => $p_n,
						"link" => aw_url_change_var("filt_p", $p_id)
					));
				}
				$prop["value"] = html::obj_change_url($p)." ".$c->get_menu();
				break;

			case "gantt_summary":
				$prop["value"] = sprintf(t("T&ouml;id kokku: %s, tunde %s.<Br>Viimase t&ouml;&ouml; l&otilde;ppt&auml;htaeg %s."),
					$this->job_count,
					$this->job_hrs / 3600,
					date("d.m.Y H:i", $this->job_end)
				);
				break;

			case "gantt_legend":
				$prop["value"] = '<div style="color:black">'.t("Fatal error").'</div>';
				$prop["value"] .= '<div style="color:green">'.t("Vajab tagasisidet").'</div>';
				$prop["value"] .= '<div style="color:red">'.t("T&auml;htaeg &uuml;le").'</div>';
				$prop["value"] .= '<div style="color:orange">'.t("T&auml;htaeg l&auml;hedal").'</div>';
				break;

			case "sp_tb":
			case "sp_table":
			case "sp_s_res":
				static $sp_i;
				if (!$sp_i)
				{
					$sp_i = new bt_settings_people_impl();
				}
				$fn = "_get_".$arr["prop"]["name"];
				return $sp_i->$fn($arr);

			case "sp_p_name":
			case "sp_p_co":
				$prop["value"] = $arr["request"][$prop["name"]];
				$prop["autocomplete_source"] = $this->mk_my_orb($prop["name"] == "sp_p_co" ? "co_autocomplete_source" : "p_autocomplete_source");
				$prop["autocomplete_params"] = array($prop["name"]);
				break;

			case "stat_hrs_overview":
			case "stat_hrs_detail":
			case "stat_hrs_errs":
			case "stat_proj_detail":
			case "stat_proj_detail_b":
			case "stat_proj_ppl":
			case "proj_gantt":
			case "proj_bug_gantt":
				static $st_i;
				if (!$st_i)
				{
					$st_i = new bt_stat_impl();
				}
				$fn = "_get_".$prop["name"];
				return $st_i->$fn($arr);

			case "stat_hrs_start":
				if (empty($arr["request"][$prop["name"]]))
				{
					$prop["value"] = mktime(0, 0, 0, date("n"), 1, date("Y"));
				}
				else
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;

			case "stat_proj_hrs_start":
				if (empty($arr["request"][$prop["name"]]))
				{
					$prop["value"] = mktime(0, 0, 1, date("m"), 1, date("Y"));
				}
				else
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;

			case "stat_hrs_end":
			case "stat_proj_hrs_end":
				if (empty($arr["request"][$prop["name"]]))
				{
					$prop["value"] = time() + 86400;
				}
				else
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;
			case "stat_hr_tasks":
			case "stat_hr_calls":
			case "stat_hr_bugs":
			case "stat_hr_meetings":
				if(empty($arr["request"][$prop["name"]]) && empty($arr["request"]["stat_hrs_end"]))
				{
					$prop["value"] = 1;
				}
				elseif (!empty($arr["request"][$prop["name"]]))
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;
			case "stat_proj_tasks":
			case "stat_proj_calls":
			case "stat_proj_bugs":
			case "stat_proj_meetings":
				if(empty($arr["request"][$prop["name"]]) && empty($arr["request"]["stat_proj_hrs_end"]))
				{
					$prop["value"] = 1;
				}
				else
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;
			case "my_bugs_stat_start":
				classload("core/date/date_calc");
				if(empty($arr["request"][$prop["name"]]))
				{
					$prop["value"] = get_week_start();
				}
				else
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;
			case "my_bugs_stat_end":
				if(empty($arr["request"][$prop["name"]]))
				{
					$prop["value"] = time();
				}
				else
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;

			case "send_newwho_mails":
				if($arr["obj_inst"]->prop("send_monitor_mails"))
				{
					return PROP_IGNORE;
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
			case "gantt_end":
				$t = date_edit::get_timestamp($prop["value"]);
				$day_start = mktime(0,0,1, date('m', $t), date('d', $t), date('Y', $t));
				$day = 24 * 60 * 60;
				if(($day_start + $day) < time())
				{
					$prop["error"] = t("Ajavahemiku l&otilde;pp ei saa olla minevikus");
					$retval = PROP_ERROR;
				}
				else
				{
					$udata = $arr["obj_inst"]->meta("gantt_user_ends");
					$cur = aw_global_get("uid_oid");
					$udata[$cur] = ceil(($day_start - time() + $day)/$day);
					$arr["obj_inst"]->set_meta("gantt_user_ends", $udata);
					$arr["obj_inst"]->save();
				}
				break;
			case "unset_table":
				$this->_save_estimates($arr);
				break;

			case "combined_priority_formula":
				$errors = $this->validate_cp_formula($prop["value"]);

				if (count($errors))
				{
					$prop["error"] = implode(". ", $errors);
					$retval = PROP_FATAL_ERROR;
				}
				break;

			case "bug_list":
				foreach($arr["request"]["bug_priority"] as $bug_id => $bug_val)
				{
					if($this->can("edit",$bug_id))
					{
						$bug = obj($bug_id);
						$bug->set_prop("bug_priority",$bug_val);
						$bug->save();
					}
				}
				foreach($arr["request"]["bug_severity"] as $bug_id => $bug_val)
				{
					if($this->can("edit",$bug_id))
					{
						$bug = obj($bug_id);
						$bug->set_prop("bug_severity",$bug_val);
						$bug->save();
					}
				}
				break;

			case "saved_searches":
				$ss = safe_array($arr["obj_inst"]->meta("saved_searches"));
				foreach($ss as $idx => $search)
				{
					if (isset($arr["request"]["sel"][$idx]))
					{
						unset($ss[$idx]);
					}
				}
				$arr["obj_inst"]->set_meta("saved_searches", $ss);
				break;
		}
		return $retval;
	}

	function _get_commits_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_complete_table($t);
		$prevm = time()-30*24*60*60;
		$c_ol = new object_list(array(
			"class_id" => array(CL_TASK_ROW,CL_BUG_COMMENT),
			"comment" => "%viewcvs.cgi%",
			"lang_id" => array(),
			"site_id" => array(),
			"created" => new obj_predicate_compare(OBJ_COMP_GREATER, $prevm),
		));
		$bug_oids = array();
		foreach($c_ol->arr() as $c_o)
		{
			$bug_oids[$c_o->parent()] = $c_o->parent();
		}
		if(count($bug_oids))
		{
			$ol = new object_list(array(
				"class_id" => CL_BUG,
				"oid" => $bug_oids,
			));
			$this->get_table_from_ol($ol, $t, $arr);
		}
	}

	function _init_complete_table($t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
		));
		$t->define_field(array(
			"name" => "id",
			"caption" => t("Id"),
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "who",
			"caption" => t("Kellele"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "bug_priority",
			"caption" => t("Prioriteet"),
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "bug_severity",
			"caption" => t("T&otilde;sidus"),
			"sortable" => 1,
			"numeric" => 1,
		));
		$t->define_field(array(
			"name" => "deadline",
			"caption" => t("T&auml;htaeg"),
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y / H:i"
		));
		$t->define_field(array(
			"name" => "last_comment",
			"caption" => t("Viimane kommentaar"),
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y / H:i"
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("K"),
			"sortable" => 1,
			"numeric" => 1,
			"callback" => array(&$this,"comment_callback"),
			"callb_pass_row" => 1,
		));
		$t->sort_by();
		$t->set_default_sortby("last_comment");
	}

	function _get_complete_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_complete_table($t);
		$cur_u = aw_global_get("uid");
		$ol = new object_list(array(
			"class_id" => CL_BUG,
			"bug_status" => 3,
   			"createdby" => $cur_u,
			"site_id" => array(),
			"lang_id" => array(),
		));
		$this->get_table_from_ol($ol, $t, $arr);

	}

	function get_table_from_ol($ol, $t, $arr)
	{
		$u = get_instance(CL_USER);
		$us = get_instance("users");
		$bug_i = get_instance(CL_BUG);
		$bug_list = $ol->arr();
		$user_list = array();
		foreach($bug_list as $oid=>$bug)
		{
			$bt = $bug_i->_get_bt($bug);
			if($bt && $bt->id() != $arr["obj_inst"]->id())
			{
				unset($bug_list[$oid]);
			}
			else
			{
				$oids[$oid] = $oid;
				$user_list[] = $bug->createdby();
			}
		}
		$u2p = $this->get_user2person_arr_from_list($user_list);

		if (!$ol->count())
		{
			$comment_ol = new object_list();
		}
		else
		{
			$comment_ol = new object_list(array(
				"parent" => $oids,
				"class_id" => array(CL_TASK_ROW,CL_BUG_COMMENT),
     				"lang_id" => array(),
				"site_id" => array()
			));
		}
		$comments_by_bug = array();
		$lastdates = array();
		foreach($comment_ol->arr() as $comm)
		{
			$comments_by_bug[$comm->parent()]++;
			$created = $comm->created();
			if($lastdates[$comm->parent()] > $created || !$lastdates[$comm->parent()])
			{
				$lastdates[$comm->parent()] = $created;
			}
		}

		$formula = $arr["obj_inst"]->prop("combined_priority_formula");

		foreach($bug_list as $bug)
		{
			$crea = $bug->createdby();
			$p = obj($u2p[$crea]);
			$nl = html::obj_change_url($bug);
			$opurl = aw_url_change_var("b_id", $bug->id());
			if ($params["path"])
			{
				$nl = $bug->path_str(array(
					"to" => $params["bt"]->id(),
					"path_only" => true
				 ))." / ".$nl;
			}

			$col = "";
			$dl = $bug->prop("deadline");
			if ($dl > 100 && time() > $dl)
			{
				$col = "#ff0000";
			}
			else
				if ($dl > 100 && date("d.m.Y") == date("d.m.Y", $dl)) // today
			{
				$col = "#f3f27e";
			}
			$t->define_data(array(
				"id" => $bug->id(),
				"name" => $nl,
				"who" => $bug->prop_str("who"),
				"bug_priority" => $bug->class_id() == CL_MENU ? "" : $bug->prop("bug_priority"),
				"bug_severity" => $bug->class_id() == CL_MENU ? "" : $bug->prop("bug_severity"),
				"created" => $bug->created(),
				"deadline" => $bug->prop("deadline"),
				"num_hrs_guess" => $bug->prop("num_hrs_guess"),
				"id" => $bug->id(),
				"oid" => $bug->id(),
				"sort_priority" => $bug_i->get_sort_priority($bug, $formula),
				"icon" => icons::get_icon($bug),
				"obj" => $bug,
    				"last_comment" => (int)$lastdates[$bug->id()],
	 			"comment_count" => (int)$comments_by_bug[$bug->id()],
				"comment" => (int)$comments_by_bug[$bug->id()],
				"col" => $col
			));
		}
	}

	function _get_my_bugs_stat_table($arr)
	{
		classload("core/date/date_calc");

		$t = $arr['prop']['vcl_inst'];
		$t->set_sortable(false);

		$t->define_field(array(
			'name' => 'name',
			'caption' => t('Nimi')
		));
		$t->define_field(array(
			'name' => 'bug_lifespan',
			'caption' => t('Bugi eluiga'),
			'width' => '10%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'comment_count',
			'caption' => t('Kommentaaride arv'),
			'width' => '10%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'working_hours',
			'caption' => t('T&ouml;&ouml;tunnid'),
			'width' => '10%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'working_hours_1',
			'caption' => t('T&ouml;&ouml;'),
			'width' => '10%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'working_hours_2',
			'caption' => t('Projekt'),
			'width' => '10%',
			'align' => 'center'
		));
		$t->define_field(array(
			'name' => 'working_hours_3',
			'caption' => t('Arendus'),
			'width' => '10%',
			'align' => 'center'
		));
		if($st = $arr["request"]["my_bugs_stat_start"])
		{
			$start = mktime(0, 0, 1, $st["month"], $st["day"], $st["year"]);
		}
		else
		{
			$start = get_week_start();
		}
		if($e = $arr["request"]["my_bugs_stat_end"])
		{
			$end = mktime(23, 59, 59, $e["month"], $e["day"], $e["year"]);
		}
		else
		{
			$end = time();
		}
		$bug_comments = new object_list(array(
			"class_id" => array(CL_TASK_ROW),
			"lang_id" => array(),
			"site_id" => array(),
			"created" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $start, $end),
			"sort_by" => "objects.createdby, objects.created"
		));

		$u = get_instance(CL_USER);
		$person = $u->get_current_person();

		$uid = aw_global_get('uid');
		if ($arr["request"]["filt_p"])
		{
			$p = get_instance(CL_CRM_PERSON);
			$person = $arr["request"]["filt_p"];
			$u = $p->has_user(obj($arr["request"]["filt_p"]));
			if ($u)
			{
				$uid = $u->prop("uid");
			}
		}
		$bugs = array();
		foreach ($bug_comments->arr() as $id => $bug_comment)
		{
			$bug_id = $bug_comment->task_id();

			if ($bug_comment->createdby() == '')
			{
				$text = $bug_comment->comment();
				if (preg_match("/cvs commit by ([^ ]+) in/imsU", $text, $mt))
				{
					if ($uid == $mt[1])
					{
						$bugs[$bug_id][$id] = $bug_comment;
					}
				}

			}
			else
			{
				if ($bug_comment->prop("impl") && in_array($person, $bug_comment->prop("impl")))
				{
					$bugs[$bug_id][$id] = $bug_comment;
				}
//				if ($uid == $bug_comment->createdby())
//				{
//					$bugs[$bug_id][$id] = $bug_comment;
//				}
			}
		}

		$our_company = get_current_company();

		foreach ($bugs as $bug_id => $comments)
		{
			$bug = new object($bug_id);
			$working_hours = 0;
			foreach ($comments as $comment_id => $comment)
			{
				$working_hours += $comment->prop('add_wh');
			}

			$development = $bug->prop("customer") == $our_company->id() ? 1 : 0;
			if($development && $bug->class_id() == CL_BUG)
			{
				$bug->finance_type = 3;
			}
			$t->define_data(array(
				'name' => html::href(array(
					'url' => $this->mk_my_orb('change', array(
						'id' => $bug_id,
						'return_url' => get_ru()
					), $bug->class_id()),
					'caption' => $bug->name()
				)),
				'bug_lifespan' => ($bug->class_id() == CL_BUG) ? $bug->get_lifespan() : "",
				'comment_count' => count($comments),
				'working_hours' => $working_hours,
				'working_hours_1' => $bug->finance_type == 1 ? $working_hours : 0,
				'working_hours_2' => $bug->finance_type == 2 ? $working_hours : 0,
				'working_hours_3' => $bug->finance_type == 3 ? $working_hours : 0
			));
			$sum += $working_hours;
			$cnt += count($comments);
			$sums[$bug->finance_type] += $working_hours;
			if($bug->class_id() == CL_BUG) $i_total_lifespan += $bug->get_lifespan(array(
				"without_string_prefix"=>true,
				"only_days"=>true,
			));
		}

		$t->sort_by();
		$t->set_sortable(false);
		$i_bug_count = count($bugs);
		$t->define_data(array(
			"name" => html::strong(t("Keskmine")),
			"bug_lifespan" => html::strong(number_format($i_total_lifespan/$i_bug_count, 2)),
			"working_hours" => html::strong(number_format($sum/$i_bug_count, 2)),
			"comment_count" => html::strong(number_format($cnt/$i_bug_count, 2))
		));
		$t->define_data(array(
			"name" => html::strong(t("Summa")),
			"bug_lifespan" => html::strong($i_total_lifespan),
			"working_hours" => html::strong($sum),
			"comment_count" => html::strong($cnt),
			"working_hours_1" => html::strong(number_format($sums[1], 2)),
			"working_hours_2" => html::strong(number_format($sums[2], 2)),
			"working_hours_3" => html::strong(number_format($sums[3], 2)),
		));
		return PROP_OK;
	}

	function _bug_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		if ($arr["request"]["group"] == "by_class")
		{
			$pt = $arr["obj_inst"]->prop("bug_by_class_parent");
		}
		else
		{
			$pt = !empty($arr["request"]["b_id"]) ? $arr["request"]["b_id"] : $this->get_bugs_parent($arr["obj_inst"]);
		}

		$tb->add_button(array(
			"name" => "add_bug",
			"tooltip" => t("Lisa"),
			"url" => html::get_new_url(CL_BUG, $pt, array(
				"return_url" => get_ru(),
			)),
			"href_id" => "add_bug_href",
			"img" => "new.gif",
		));

		$tb->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta"),
			"action" => "",
			"img" => "save.gif",
		));
		$tb->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => "delete",
			"confirm" => t("Oled kindel, et soovid bugi kustutada?"),
		));

		$base = $this->mk_my_orb("cut_b");

		$cut_js = "
			url = '$base';
			len = document.changeform.elements.length;
			cnt = 0;
			for(i = 0; i < len; i++)
			{
				if (document.changeform.elements[i].name.indexOf('sel') != -1 && document.changeform.elements[i].checked)
				{
					url += '&sel[]='+document.changeform.elements[i].value;
					document.changeform.elements[i].checked=false;
					cnt++;
				}
			}

			if (cnt > 0)
			{
				aw_get_url_contents(url);
				//window.location=url;
				paste_button = document.getElementById('paste_button');
				paste_button.style.visibility='visible';
			}
			else
			{
				paste_button = document.getElementById('paste_button');
				paste_button.style.visibility='hidden';
			}
			return false;
		";

		$tb->add_button(array(
			"name" => "cut",
			"tooltip" => t("L&otilde;ika"),
			"img" => "cut.gif",
//			"onClick" => $cut_js,
			"action" => "cut_b",
		));

		$vis = "hidden;";
		if (isset($_SESSION["bt"]["cut_bugs"]) && is_array($_SESSION["bt"]["cut_bugs"]) && count($_SESSION["bt"]["cut_bugs"]))
		{
			$vis = "visible;";
			$tb->add_button(array(
				"name" => "paste",
				"tooltip" => t("Kleebi"),
				"img" => "paste.gif",
				"action" => "paste_b",
//			"surround_start" => "<span id='paste_button' style='visibility: $vis;'>",
//			"surround_end" => "</span>"
			));
		}
		if ($vis == "visible")
		{
		}

		$tb->add_separator();

		$tb->add_menu_button(array(
			"name" => "assign",
			"tooltip" => t("M&auml;&auml;ra"),
			"img" => "class_38.gif"
		));

		// list all people to assign to
		// list all my co-workers who are important to me, from crm
		$ppl = $this->get_people_list($arr["obj_inst"]);
		foreach($ppl as $p_oid => $p_name)
		{
			$tb->add_menu_item(array(
				"parent" => "assign",
				"text" => $p_name,
				"link" => "#",
				"onClick" => "document.changeform.assign_to.value=$p_oid;submit_changeform('assign_bugs')"
			));
		}

		$tb->add_menu_button(array(
			"name" => "set_status",
			"tooltip" => t("Staatus"),
			"img" => "class_".CL_BUG.".gif"
		));

		// list all people to assign to
		// list all my co-workers who are important to me, from crm
		$dat = get_instance(CL_BUG);
		$ppl = $dat->get_status_list();
		foreach($ppl as $p_oid => $p_name)
		{
			$tb->add_menu_item(array(
				"parent" => "set_status",
				"text" => $p_name,
				"link" => "#",
				"onClick" => "document.changeform.assign_to.value=$p_oid;submit_changeform('set_bug_status')"
			));
		}

		$tb->add_cdata(html::href(array(
			"url" => get_ru(),
			"caption" => t("Bookmarkimise url"),
			"id" => "sync_url"
		)));
	}

	/**
	@attrib name=proj_tree_level all_args=1
	**/
	function proj_tree_level($arr)
	{
		get_instance("applications/bug_o_matic_3000/bt_projects_impl")->proj_tree_level($arr);
	}

	/**
	@attrib name=cut_project
	**/
	function cut_project($arr)
	{
		unset($_SESSION["bt_proj_copy_clip"]);
		$_SESSION["bt_proj_cut_clip"] = $arr["sel"];
		return $arr["post_ru"];
	}

	/**
	@attrib name=copy_project
	**/
	function copy_project($arr)
	{
		unset($_SESSION["bt_proj_cut_clip"]);
		$_SESSION["bt_proj_copy_clip"] = $arr["sel"];
		return $arr["post_ru"];
	}

	/**
	@attrib name=paste_project
	**/
	function paste_project($arr)
	{
		if($this->can("view", $arr["filt_value"]))
		{
			$cat = obj($arr["filt_value"]);
		}
		if(!$cat || $cat->class_id() != CL_PROJECT_CATEGORY)
		{
			return $arr["post_ru"];
		}

		if($ids = $_SESSION["bt_proj_cut_clip"])
		{
			$rem_cat = true;
			unset($_SESSION["bt_proj_cut_clip"]);
		}
		elseif($ids = $_SESSION["bt_proj_copy_clip"])
		{
			$rem_cat = false;
			unset($_SESSION["bt_proj_copy_clip"]);
		}
		else
		{
			return $arr["post_ru"];
		}

		foreach($ids as $id)
		{
			if($this->can("view", $id))
			{
				$o = obj($id);
				if($rem_cat)
				{
					$o->set_prop("category", $arr["filt_value"]);
				}
				else
				{
					$vals = $o->prop("category");
					$vals[$arr["filt_value"]] = $arr["filt_value"];
					$o->set_prop("category", $vals);
				}
				$o->save();
			}
		}
		return $arr["post_ru"];
	}

	/**  to get subtree for default view
		@attrib name=get_node all_args=1

	**/
	function get_node($arr)
	{
		$node_tree = get_instance("vcl/treeview");
		$node_tree->start_tree (array (
			"type" => TREE_DHTML,
			"tree_id" => "bug_tree",
			"branch" => 1,
		));

		$ol = new object_list(array(
			"parent" => $arr["parent"],
			"class_id" => array(CL_BUG, CL_MENU, CL_DEVELOPMENT_ORDER),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_BUG.bug_status" => new obj_predicate_not(array(bug::BUG_CLOSED, 14)),
					"class_id" => new obj_predicate_not(CL_BUG),
				),
			)),
			"sort_by" => "objects.name"
		));

		$arr["set_retu"] = aw_url_change_var("b_id", $arr["parent"], $arr["set_retu"]);

		$objects = $ol->arr();
		foreach($objects as $obj_id => $object)
		{
			$ol = new object_list(array(
				"parent" => $obj_id,
				"class_id" => array(CL_BUG, CL_MENU, CL_DEVELOPMENT_ORDER),
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_BUG.bug_status" => new obj_predicate_not(array(bug::BUG_CLOSED, 14)),
						"class_id" => new obj_predicate_not(CL_BUG),
					),
				)),
			));
			$ol_list = $ol->arr();
			$subtree_count = (count($ol_list) > 0)?" (".count($ol_list).")":"";

			$nm = $this->name_cut($object->name()).$subtree_count;
			if (false && $_GET["b_id"] == $obj_id)
			{
				$nm = "<b>".$nm."</b>";
			}
			$icon_url = icons::get_icon_url($object->class_id());
			$node_tree->add_item(0 ,array(
				"id" => $obj_id,
				"name" => $nm."  (".html::get_change_url($obj_id, array("return_url" => $arr["set_retu"]), t("<span style='font-size: 8px;'>Muuda</span>")).")",
				"iconurl" => $icon_url,
				"url" => html::get_change_url($arr["inst_id"], array(
					"group" => $arr["active_group"],
					"b_id" => $obj_id,
				)),
				"onClick" => "do_bt_table_switch($obj_id, this);return false;",
				"alt" => $object->name()
			));

			foreach($ol_list as $sub_id => $sub_obj)
			{
				$node_tree->add_item( $obj_id, array(
					"id" => $sub_id,
					"name" => $sub_obj->name()." (".html::get_change_url($sub_id, array("return_url" => $arr["set_retu"]), t("<span style='font-size: 8px;'>Muuda</span>")).")",
					"onClick" => "do_bt_table_switch($sub_id, this);return false;"
				));
			}
		}

		die($node_tree->finalize_tree());
	}

	/**
	@attrib name=get_node_cat all_args=1
	**/
	function get_node_cat($arr)
	{
		$arr["parent"] = substr($arr["parent"], 1);
		$pr_i = get_instance("applications/bug_o_matic_3000/bt_projects_impl");

		$tree = get_instance("vcl/treeview");
		$tree->start_tree (array (
			"type" => TREE_DHTML,
			"tree_id" => "bug_tree",
			"branch" => 1,
		));
		switch($arr["parent"])
		{
			case 1:
				$pr_i->__insert_cust_categories($tree, 0, $arr);
				break;

			case 2:
				$pr_i->__insert_sections($tree, 0, $arr);
				break;

			case 3:
				$this->__insert_ppl_tree1($tree, 0, $arr, "monitors");
				break;

			case 4:
				$this->__insert_ppl_tree1($tree, 0, $arr, "who");
				break;

			case 5:
				$this->__insert_classes_tree($tree, 0, 0, $arr);
				break;

			default:
				if($this->can("view", $arr["parent"]))
				{
					$o = obj($arr["parent"]);
					switch($o->class_id())
					{
						case CL_CRM_CATEGORY:
							$pr_i->__insert_category_subs($tree, $o, 0, $arr);
							break;

						case CL_CRM_SECTION:
							$pr_i->__insert_section_subs($tree, $o, 0, $arr);
							break;

						case CL_CRM_PERSON:
							$pr_i->__insert_person_projects($tree, $o, 0, $arr);
							break;
					}
				}
				elseif(strpos($arr["parent"], "_"))
				{
					$tmp = explode("_", $arr["parent"]);
					switch($tmp[0])
					{
						case "monitors":
						case "who":
							if($this->can("view", $tmp[1]))
							{
								$this->__insert_ppl_tree2($tree, obj($tmp[1]), 0, $arr, $tmp[0]);
							}
							break;
						case "fld":
							$this->__insert_classes_tree($tree, 0, $tmp[1], $arr);
							break;
					}
				}
		}
		die($tree->finalize_tree());
	}

	private function __insert_ppl_tree1($tree, $parent, $arr, $prop)
	{
		$owner = self::_get_owner($arr);
		if(!$owner)
		{
			return;
		}
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"CL_CRM_PERSON.RELTYPE_MONITOR(CL_BUG).class_id" => CL_BUG,
			"CL_CRM_PERSON.RELTYPE_CURRENT_JOB.employer.oid" => $owner->id()
		));
		$ppl = $ol->ids();
		$ol = new object_list();
		if(count($ppl))
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_SECTION,
				"CL_CRM_SECTION.RELTYPE_SECTION(CL_CRM_PERSON_WORK_RELATION).RELTYPE_CURRENT_JOB(CL_CRM_PERSON)" => $ppl
			));
		}
		foreach($ol->arr() as $o)
		{
			$tree->add_item($parent, array(
				"id" => $prop."_".$o->id(),
				"name" => $o->name(),
				"url" => "#",
			));
			if($parent == 0)
			{
				$tree->add_item($prop."_".$o->id(), array());
			}
		}
	}

	private function __insert_ppl_tree2($tree, $o, $parent, $arr, $prop)
	{
		$bp_i = new bt_projects_impl();
		$owner = self::_get_owner($arr);
		if(!$owner)
		{
			return;
		}
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"CL_CRM_PERSON.RELTYPE_CURRENT_JOB.RELTYPE_SECTION" => $o->id(),
			"CL_CRM_PERSON.RELTYPE_MONITOR(CL_BUG).class_id" => CL_BUG,
			"CL_CRM_PERSON.RELTYPE_CURRENT_JOB.employer.oid" => $owner->id(),
		));
		foreach($ol->arr() as $oid => $o)
		{
			$ch_ol = new object_list(array(
				"class_id" => CL_BUG,
				"site_id" => array(),
				"lang_id" => array(),
				$prop => $oid,
			));
			if($num = $ch_ol->count())
			{
				$tree->add_item($parent, array(
					"id" => $prop."_".$oid,
					"name" => $bp_i->__parse_name($o->name(), $prop."_".$oid, $arr, $num),
					"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
					"url" => aw_url_change_var(array(
						"filt_type" => $prop,
						"filt_value" => $prop."_".$oid,
					), false, $arr["set_retu"]),
				));
			}

		}
	}

	private function __insert_classes_tree($tree, $parent, $cl_parent, $arr)
	{
		if($parent != 0)
		{
			$tree->add_item($parent, array(
				"id" => "",
				"url" => "#",
			));
		}
		$bp_i = new bt_projects_impl();
		$f = aw_ini_get("classfolders");
		foreach($f as $id => $dat)
		{
			if ($dat["parent"] == $cl_parent)
			{
				$tree->add_item($parent, array(
					"id" => "fld_".$id,
					"name" => $dat["name"],
					"url" => "#",
				));
				if(strpos($parent, "fld") === false)
				{
					$this->__insert_classes_tree($tree, "fld_".$id, $id, $arr);
				}
			}
		}
		$cl = aw_ini_get("classes");
		foreach($cl as $id => $dat)
		{
			if(isset($dat["parents"]) && $dat["parents"] == $cl_parent)
			{
				$num = 0;
				if($parent == 0)
				{
					$num_ol = new object_list(array(
						"class_id" => CL_BUG,
						"site_id" => array(),
						"lang_id" => array(),
						"bug_class" => $id,
					));
					$num = $num_ol->count();
				}
				$tree->add_item($parent, array(
					"id" => "cl_".$id,
					"iconurl" => icons::get_icon_url($id),
					"name" => $bp_i->__parse_name($dat["name"], "fld_".$id, $arr, $num),
					"url" => aw_url_change_var(array(
						"filt_type" => "class",
						"filt_value" => "cl_".$id,
					), false, $arr["set_retu"]),
				));
			}
		}
	}

	function _bug_tree($arr)
	{
		$this->tree = new treeview();
		$this->active_group = $arr["request"]["group"];
		$this->sort_type = aw_global_get("bug_tree_sort");
		$this->self_id = $arr["obj_inst"]->id();
		$this->tree_root_name = "Bug-Tracker";
		switch($this->sort_type["name"])
		{
			case "cat":
				$orb_function = "get_node_cat";
				$tid = "_cat";
				break;

			default:
				$tid = "_def";
				$orb_function = "get_node";
				break;
		}

		$this->tree->start_tree(array(
			"type" => TREE_DHTML,
			"has_root" => 1,
			"tree_id" => "bug_tree".$tid,
			"persist_state" => 1,
			"root_name" => t("&Uuml;lesanded"),
			"root_url" => aw_url_change_var("b_id", null),
			"get_branch_func" => $this->mk_my_orb($orb_function, array(
				"type" => $this->sort_type["name"],
				"reltype" => isset($this->sort_type["reltype"]) ? $this->sort_type["reltype"] : NULL,
				"clid"=> isset($this->sort_type["class"]) ? $this->sort_type["class"] : NULL,
				"inst_id" => $this->self_id,
				"active_group" => $this->active_group,
				"b_id" => automatweb::$request->arg("b_id"),
				"p_fld_id" => automatweb::$request->arg("p_fld_id"),
				"p_cls_id" => automatweb::$request->arg("p_cls_id"),
				"p_cust_id" => automatweb::$request->arg("p_cust_id"),
				"b_mon" => automatweb::$request->arg("b_mon"),
				"b_stat" => automatweb::$request->arg("b_stat"),
				"filt_type" => automatweb::$request->arg("filt_type"),
				"filt_value" => automatweb::$request->arg("filt_value"),
				"set_retu" => get_ru(),
				"parent" => " ",
			)),
		));
		if($this->sort_type["name"] == "parent")
		{
			$this->generate_bug_tree(array(
				"parent" => $this->get_bugs_parent($arr["obj_inst"]),
			));
		}

		if($this->sort_type["name"] == "cat")
		{
			$this->generate_cat_bug_tree($arr);
		}

		$arr["prop"]["value"] = $this->tree->finalize_tree();
		$arr["prop"]["type"] = "text";

	}

	function generate_bug_tree($arr)
	{
		$ol = new object_list(array("parent" => $arr["parent"], "class_id" => array(CL_BUG, CL_MENU, CL_DEVELOPMENT_ORDER), "bug_status" => new obj_predicate_not(bug::BUG_CLOSED),"lang_id" => array(), "site_id" => array()));
		$objects = $ol->arr();

		$nm = $this->tree_root_name." (".$ol->count().")";
		if (empty($_GET["b_id"]))
		{
			$nm = "<b>".$nm."</b>";
		}
		$this->tree->add_item(0,array(
				"id" => $this->self_id,
				"name" => $nm,
				"url" => aw_url_change_var("b_id", null)
		));
		foreach($objects as $obj_id => $object)
		{
			$nm = $object->name();
			if (isset($_GET["b_id"]) && $_GET["b_id"] == $obj_id)
			{
				$nm = "<b>".$nm."</b>";
			}
			$this->tree->add_item($arr["parent"] , array(
				"id" => $obj_id,
				"name" => $nm." ".html::get_change_url($obj_id, array("return_url" => get_ru()), t("Muuda")),
				"onClick" => "do_bt_table_switch($obj_id);return false;"
			));
		}
	}

	function generate_cat_bug_tree($arr)
	{
		$pr_i = get_instance("applications/bug_o_matic_3000/bt_projects_impl");

		$this->tree->add_item(0, array(
			"id" => 1,
			"name" => t("Kliendid"),
			"url" => aw_url_change_var(array("filt_type" => null, "filt_value" => null)),
		));
		$pr_i->__insert_cust_categories($this->tree, 1, $arr);

		$this->tree->add_item(0, array(
			"id" => 2,
			"name" => t("Projektijuhid"),
			"url" => aw_url_change_var(array("filt_type" => null, "filt_value" => null)),
		));
		$pr_i->__insert_sections($this->tree, 2, $arr);

		$this->tree->add_item(0, array(
			"id" => 3,
			"name" => t("J&auml;lgijad"),
			"url" => aw_url_change_var(array("filt_type" => null, "filt_value" => null)),
		));
		$this->__insert_ppl_tree1($this->tree, 3, $arr, "monitors");

		$this->tree->add_item(0, array(
			"id" => 4,
			"name" => t("Tegijad"),
			"url" => aw_url_change_var(array("filt_type" => null, "filt_value" => null)),
		));
		$this->__insert_ppl_tree1($this->tree, 4, $arr, "who");

		$this->tree->add_item(0, array(
			"id" => 5,
			"name" => t("Klassid"),
			"url" => aw_url_change_var(array("filt_type" => null, "filt_value" => null)),
		));
		$this->__insert_classes_tree($this->tree, 5, 0, $arr);
	}

	function name_cut($name)
	{
		$pre = substr($name, 0, MENU_ITEM_LENGTH);
		$suf = (strlen($name) > MENU_ITEM_LENGTH)?"...":"";
		return strip_tags($pre.$suf);
	}

	function callb_who($val)
	{
		$name = "";
		if($this->can("view", $val))
		{
			$obj = obj($val);
			$name = $obj->name();
		}
		return $name;
	}

	function show_priority($_param)
	{
		if ($_param["obj"]->class_id() == CL_MENU)
		{
			return "";
		}
		$return = html::textbox(array(
			"name" => "bug_priority[".$_param["oid"]."]",
			"size" => 2,
			"value" => $_param["bug_priority"],
		));
		return $return;
	}

	function show_severity($_param)
	{
		if ($_param["obj"]->class_id() == CL_MENU)
		{
			return "";
		}
		$return = html::textbox(array(
			"name" => "bug_severity[".$_param["oid"]."]",
			"size" => 2,
			"value" => $_param["bug_severity"],
		));
		return $return;
	}

	function show_status($_val)
	{
		if ($_val["obj"]->class_id() == CL_MENU)
		{
			return "";
		}
		$values = $this->bug_i->get_status_list();
		return $values[$_val["bug_status"]];
	}

	function show_status_no_edit($_val)
	{
		if ($_val["obj"]->class_id() == CL_MENU)
		{
			return "";
		}
		$values = $this->bug_i->get_status_list();
		return $values[$_val["bug_status"]];
	}

	function comment_callback($arr)
	{
		if ($arr["obj"]->class_id() == CL_MENU)
		{
			return "";
		}

		return html::img(array("url" => aw_ini_get("baseurl")."/automatweb/images/forum_add_new.gif", "border" => 0))." ".html::get_change_url($arr["oid"] , array("group" => "comments" , "return_url" => get_ru()), $arr["comment_count"] ? $arr["comment_count"] : " 0 ");
	}

	function sp_callback($arr)
	{
		return number_format($arr["sort_priority"], 1, '.', '') . "...";
	}

	function _init_bug_list_tbl($t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
		));
		$t->define_field(array(
			"name" => "id",
			"caption" => t("Id"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1
		));
		$bugi = get_instance(CL_BUG);
		$statuses = $bugi->get_status_list();
		unset($statuses[5]);
		$t->define_field(array(
			"name" => "bug_status",
			"caption" => t("Staatus"),
			"sortable" => 1,
//			"callback" => array(&$this, "show_status"),
//			"callb_pass_row" => 1,
			"filter" => $statuses,
			"sorting_field" => "bug_sort",
		));

		$t->define_field(array(
			"name" => "who",
			"caption" => t("Kellele"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "bug_priority",
			"caption" => t("Prioriteet"),
			"sortable" => 1,
			"numeric" => 1,
			"callback" => array(&$this, "show_priority"),
			"callb_pass_row" => 1,
			"filter" => array(
				t("1"),
				t("2"),
				t("3"),
				t("4"),
				t("5"),
			),
		));
		$t->define_field(array(
			"name" => "bug_severity",
			"caption" => t("T&otilde;sidus"),
			"sortable" => 1,
			"numeric" => 1,
			"callback" => array(&$this, "show_severity"),
			"callb_pass_row" => 1,
			"filter" => array(
				t("1"),
				t("2"),
				t("3"),
				t("4"),
				t("5"),
			),
		));

		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"sortable" => 1,
			"filter" => "automatic",
		));

		$t->define_field(array(
			"name" => "deadline",
			"caption" => t("T&auml;htaeg"),
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y / H:i"
		));

		$t->define_field(array(
			"name" => "comment",
			"caption" => t("K"),
			"sortable" => 1,
			"numeric" => 1,
			"callback" => array(&$this,"comment_callback"),
			"callb_pass_row" => 1,
		));

		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
	}

	function _init_bug_list_tbl_no_edit(&$t)
	{
		$t->define_field(array(
			"name" => "icon",
			"caption" => t(""),
		));

		$t->define_field(array(
			"name" => "id",
			"caption" => t("ID"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1
		));

		$bugi = get_instance(CL_BUG);
		$statuses = $bugi->get_status_list();
		unset($statuses[5]);
		unset($statuses[14]);
		$t->define_field(array(
			"name" => "bug_status",
			"caption" => t("Staatus"),
			"sortable" => 1,
//			"callback" => array(&$this, "show_status"),
//			"callb_pass_row" => 1,
			"sorting_field" => "bug_sort",
			"filter" => $statuses,
		));
		$t->define_field(array(
			"name" => "who",
			"caption" => t("Kellele"),
			"sortable" => 1,
		));

		$t->define_field(array(
			"name" => "sort_priority",
			"caption" => t("SP"),
			"tooltip" => t("Kombineeritud prioriteet"),
			"sortable" => 1,
			"numeric" => 1,
			"callback" => array(&$this,"sp_callback"),
			"callb_pass_row" => 0
		));

		$t->define_field(array(
			"name" => "bug_priority",
			"caption" => t("Pri"),
			"sortable" => 1,
			"numeric" => 1,
			"filter" => array(
				t("1"),
				t("2"),
				t("3"),
				t("4"),
				t("5"),
			),
		));

		$t->define_field(array(
			"name" => "num_hrs_guess",
			"caption" => t("P"),
			"tooltip" => t("Prognoositud tunde"),
			"sortable" => 1,
			"numeric" => 1,
		));

		$t->define_field(array(
			"name" => "createdby",
			"caption" => t("Looja"),
			"sortable" => 1,
			"filter" => "automatic",
		));

		$t->define_field(array(
			"name" => "deadline",
			"caption" => t("T&auml;htaeg"),
			"chgbgcolor" => "col",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y / H:i"
		));

		$t->define_field(array(
			"name" => "comment",
			"caption" => t("K"),
			"sortable" => 1,
			"numeric" => 1,
			"callback" => array(&$this,"comment_callback"),
			"callb_pass_row" => 1,
		));

		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
	}

	function _bug_list($arr)
	{
		$t = new vcl_table();
		$this->_init_bug_list_tbl($t);
		$t->set_caption(t("Nimekiri arendus&uuml;lesannetest"));

		$pt = !empty($arr["request"]["cat"]) ? $arr["request"]["cat"] : $arr["obj_inst"]->id();
		if($this->can("view", $pt) && empty($arr["request"]["filt_type"]))
		{
			// arhiivi tab
			if($this->use_group == "archive")
			{
				$ot = new object_tree(array(
					"parent" => $pt,
					"class_id" => array(CL_BUG, CL_MENU)
				));

				$ol = new object_list(array(
					"oid" => $ot->ids(),
					"class_id" => CL_BUG,
					"bug_status" => bug::BUG_CLOSED
				));
			}
			// bugid tab
			else
			{
				$filt = array(
					"parent" => $pt,
					"class_id" => array(CL_BUG,CL_MENU,CL_DEVELOPMENT_ORDER)
				);
				$closed = 0;
				foreach($arr["request"] as $r)
				{
					if($r == "1,6,Suletud")
						$closed = 1;
				}
				if(!$closed)
				{
					$filt[] = new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_BUG.bug_status" => new obj_predicate_not(array(bug::BUG_CLOSED, 14)),
							"class_id" => new obj_predicate_not(CL_BUG),
						),
					));
				}

				if(strlen(automatweb::$request->arg("p_id")))
				{
					$filt[$this->sort_type["name"]] = $arr["request"]["p_id"];
				}
				elseif(strlen(automatweb::$request->arg("b_id")))
				{
					$filt["parent"] = $arr["request"]["b_id"];
				}
				else
				if (!empty($arr["request"]["p_fld_id"]))	// class folder
				{
					// list classes for that folder
					$clss = aw_ini_get("classes");
					$c = array();
					foreach($clss as $clid => $dat)
					{
						foreach(explode(",", $dat["parents"]) as $parent)
						{
							if ($parent == $arr["request"]["p_fld_id"])
							{
								$c[] = $clid;
							}
						}
					}
					$filt["bug_class"] = $c;
					unset($filt["parent"]);
				}
				else
				if (!empty($arr["request"]["p_cls_id"]))	// class
				{
					$filt["bug_class"] = $arr["request"]["p_cls_id"];
					unset($filt["parent"]);
				}

				if (!empty($arr["request"]["b_stat"]))
				{
					$filt["bug_status"] = $arr["request"]["b_stat"];
					unset($filt["parent"]);
				}

				if (!empty($arr["request"]["b_mon"]))
				{
					//$filt["monitors"] = $arr["request"]["b_mon"];
					$filt["CL_BUG.RELTYPE_MONITOR"] = $arr["request"]["b_mon"];
					unset($filt["parent"]);
				}
				$ol = new object_list($filt);
			}
		}
		elseif(!empty($arr["request"]["filt_type"]) && !empty($arr["request"]["filt_value"]))
		{
			$filt = array(
				"class_id" => CL_BUG
			);
			$val = $arr["request"]["filt_value"];
			switch($arr["request"]["filt_type"])
			{
				case "customer":
					$filt["customer"] = $val;
					if($this->can("view", $val))
					{
						$t->set_caption(sprintf(t("Arendus&uuml;lesanded, mille klient on %s"), obj($val)->name()));
					}
					break;

				case "project":
					$filt["project"] = $val;
					if($this->can("view", $val))
					{
						$t->set_caption(sprintf(t("Arendus&uuml;lesanded, mille projekt on %s"), obj($val)->name()));
					}
					break;

				case "monitors":
				case "who":
					$tmp = explode("_", $val);
					if($this->can("view", $tmp[1]))
					{
						$t->set_caption(sprintf(t("Arendus&uuml;lesanded, mille %s on %s"), (($tmp[0] == "monitors") ? t("j&auml;lgija") : t("tegija")), obj($tmp[1])->name()));
					}
					$filt[$tmp[0]] = $tmp[1];
					break;

				case "class":
					$tmp = explode("_", $arr["request"]["filt_value"]);
					$t->set_caption(sprintf(t("Arendus&uuml;lesanded, mille klass on %s"),  t($cls[$tmp[1]]["name"])));
					$filt["bug_class"] = $tmp[1];
					break;
			}
			$ol = new object_list($filt);
		}
		else
		{
			$ol = new object_list();
		}

		$this->populate_bug_list_table_from_list($t, $ol, array("bt" => $arr["obj_inst"]));
		$t->set_default_sortby("id");
		$t->set_default_sorder("desc");
		$t->sort_by();
		$arr["prop"]["value"] = "<span id=\"bug_table\">".$t->get_html()."</table>";
		if (automatweb::$request->arg("tb_only") == 1)
		{
			die($t->draw());
		}
	}

	function populate_bug_list_table_from_list($t, $ol, $params = array(), $s = array())
	{
		$u = get_instance(CL_USER);
		$us = get_instance("users");
		$bug_i = get_instance(CL_BUG);
		$states = $bug_i->get_status_list();
		$devo_states = get_instance(CL_DEVELOPMENT_ORDER)->get_status_list();
		$bug_list = $ol->arr();
		$user_list = array();
		foreach($bug_list as $bug)
		{
			$user_list[] = $bug->createdby();
		}
		$u2p = $this->get_user2person_arr_from_list($user_list);

		if (!$ol->count())
		{
			$comment_ol = new object_list();
		}
		else
		{
			$comment_ol = new object_list(array(
				"parent" => $ol->ids(),
				"class_id" => array(CL_TASK_ROW,CL_BUG_COMMENT),
				"sort_by" => "created asc",
			));
		}

		$comments_by_bug = array();
		$date_from = date_edit::get_timestamp(isset($s["s_date_from"]) ? $s["s_date_from"] : NULL);
		$date_to = date_edit::get_timestamp(isset($s["s_date_to"]) ? $s["s_date_to"] : NULL);
		foreach($comment_ol->arr() as $comm)
		{
			if(!isset($comments_by_bug[$comm->parent()]))
			{
				$comments_by_bug[$comm->parent()] = 0;
			}

			$comments_by_bug[$comm->parent()]++;
			$latest_com_by_bug[$comm->parent()] = $comm->created();
			if(($date_from > 1 && $comm->created() < $date_from)  ||($date_to > 1 && $comm->created() > $date_to))
			{
				continue;
			}

			if(!isset($times_by_bug[$comm->parent()]))
			{
				$times_by_bug[$comm->parent()] = 0;
			}
			$times_by_bug[$comm->parent()] += $comm->prop("add_wh");
		}

		if ($_GET["action"] === "list_only_fetch")
		{
			$t->set_request_uri($this->mk_my_orb("change", array("id" => $params["bt"]->id(), "group" => "by_default", "b_id" => $_GET["b_id"]), "bug_tracker"));
		}

		$formula = $params["bt"]->prop("combined_priority_formula");
		$bug_count = 0;

		$bug_sort = array(
			bug::BUG_FATALERROR => 0,
			bug::BUG_FEEDBACK => 1,
			bug::BUG_INPROGRESS => 2,
			bug::BUG_OPEN => 3,
			bug::BUG_DEVORDER => 4,
			bug::BUG_WONTFIX => 5,
			bug::BUG_TESTING => 6,
			bug::BUG_VIEWING => 7,
			bug::BUG_TESTED => 8,
			bug::BUG_DONE => 9,
			bug::BUG_CLOSED => 10,
			bug::BUG_INCORRECT => 11,
			bug::BUG_NOTREPEATABLE => 12,
			bug::BUG_NOTFIXABLE => 13
		);

		foreach($bug_list as $bug)
		{
			if((!empty($s["s_date_time_from"]) && $times_by_bug[$bug->id()] < $s["s_date_time_from"]) || (!empty($s["s_date_time_to"]) && $times_by_bug[$bug->id()] > $s["s_date_time_to"]))
			{
				continue;
			}
			if(isset($s["s_date_type"]) && $s["s_date_type"] == 3 && ($latest_com_by_bug[$bug->id()] < $date_from || $latest_com_by_bug[$bug->id()] > $date_to))
			{
				continue;
			}
			$bug_count++;
			$crea = $bug->createdby();
			$p = obj($u2p[$crea]);

			if ($_GET["action"] == "list_only_fetch")
			{
				$nl = html::href(array(
					"url" => html::get_change_url($bug->id(), array(
						"return_url" => $this->mk_my_orb("change", array("id" => $params["bt"]->id(), "group" => "by_default", "b_id" => $bug->parent()), "bug_tracker"),
					)),
					"caption" => parse_obj_name($bug->name())
				));
				$opurl = $this->mk_my_orb("change", array("id" => $params["bt"]->id(), "group" => "by_default", "b_id" => $bug->id()), "bug_tracker");
			}
			else
			{
				$nl = html::obj_change_url($bug);
				$opurl = aw_url_change_var("b_id", $bug->id());
			}

			if (!empty($params["path"]))
			{
				$nl = $bug->path_str(array(
					"to" => $params["bt"]->id(),
					"path_only" => true
				))." / ".$nl;
			}

			$col = "";
			$dl = $bug->prop("deadline");
			if ($dl > 100 && time() > $dl)
			{
				$col = "#ff0000";
			}
			else
			if ($dl > 100 && date("d.m.Y") == date("d.m.Y", $dl)) // today
			{
				$col = "#f3f27e";
			}

			$t->define_data(array(
				"id" => $bug->id(),
				"name" => $nl." (".html::href(array(
					"url" => $opurl,
					"caption" => t("Sisene")
				)).")",
				"bug_status" => $bug->class_id() == CL_DEVELOPMENT_ORDER ? (isset($devo_states[$bug->prop("bug_status")]) ? $devo_states[$bug->prop("bug_status")] : NULL) : (isset($states[$bug->prop("bug_status")]) ? $states[$bug->prop("bug_status")] : 0),
				"bug_sort" => isset($bug_sort[$bug->prop("bug_status")]) ? $bug_sort[$bug->prop("bug_status")] : NULL,
				"who" => $bug->prop_str("who"),
				"bug_priority" => $bug->class_id() == CL_MENU ? "" : $bug->prop("bug_priority"),
				"bug_severity" => $bug->class_id() == CL_MENU ? "" : $bug->prop("bug_severity"),
				"createdby" => $p->name(),
				"created" => $bug->created(),
				"deadline" => $bug->prop("deadline"),
				"num_hrs_guess" => $bug->prop("num_hrs_guess"),
				"id" => $bug->id(),
				"oid" => $bug->id(),
				"sort_priority" => $bug_i->get_sort_priority($bug, $formula),
				"icon" => icons::get_icon($bug),
				"obj" => $bug,
				"comment_count" => isset($comments_by_bug[$bug->id()]) ? (int)$comments_by_bug[$bug->id()] : 0,
				"comment" => isset($comments_by_bug[$bug->id()]) ? (int)$comments_by_bug[$bug->id()] : 0,
				"col" => $col
			));
		}
		if(!empty($params["add_bug_count"]))
		{
			$t->set_caption(sprintf(t("Leiti %s bugi"), $bug_count));
		}
		$t->set_numeric_field("bug_sort");
		$t->set_numeric_field("sort_priority");
		$t->set_default_sortby("sort_priority");
		$t->set_default_sorder("desc");
	}

	/**
		@attrib name=delete
		@param cat optional
	**/
	function delete($arr)
	{
		foreach($arr["sel"] as $id)
		{
			if($this->can("view", $id))
			{
				$obj = obj($id);
				$obj->delete();
			}
		}
		return $arr["post_ru"];
	}

	function callback_mod_retval(&$arr)
	{
		$arr["args"]["sp_p_name"] = isset($arr["request"]["sp_p_name"]) ? $arr["request"]["sp_p_name"] : "";
		$arr["args"]["sp_p_co"] = isset($arr["request"]["sp_p_co"]) ? $arr["request"]["sp_p_co"] : "";
		$arr["args"]["stat_hrs_start"] = isset($arr["request"]["stat_hrs_start"]) ? $arr["request"]["stat_hrs_start"] : "";
		$arr["args"]["stat_hrs_end"] = isset($arr["request"]["stat_hrs_end"]) ? $arr["request"]["stat_hrs_end"] : "";
		$arr["args"]["stat_proj_hrs_start"] = isset($arr["request"]["stat_proj_hrs_start"]) ? $arr["request"]["stat_proj_hrs_start"] : "";
		$arr["args"]["stat_proj_hrs_end"] = isset($arr["request"]["stat_proj_hrs_end"]) ? $arr["request"]["stat_proj_hrs_end"] : "";
		$arr["args"]["stat_hr_bugs"] = isset($arr["request"]["stat_hr_bugs"]) ? $arr["request"]["stat_hr_bugs"] : "";
		$arr["args"]["stat_hr_tasks"] = isset($arr["request"]["stat_hr_tasks"]) ? $arr["request"]["stat_hr_tasks"] : "";
		$arr["args"]["stat_hr_calls"] = isset($arr["request"]["stat_hr_calls"]) ? $arr["request"]["stat_hr_calls"] : "";
		$arr["args"]["stat_hr_meetings"] = isset($arr["request"]["stat_hr_meetings"]) ? $arr["request"]["stat_hr_meetings"] : "";
		$arr["args"]["stat_proj_bugs"] = isset($arr["request"]["stat_proj_bugs"]) ? $arr["request"]["stat_proj_bugs"] : "";
		$arr["args"]["stat_proj_tasks"] = isset($arr["request"]["stat_proj_tasks"]) ? $arr["request"]["stat_proj_tasks"] : "";
		$arr["args"]["stat_proj_calls"] = isset($arr["request"]["stat_proj_calls"]) ? $arr["request"]["stat_proj_calls"] : "";
		$arr["args"]["stat_proj_meetings"] = isset($arr["request"]["stat_proj_meetings"]) ? $arr["request"]["stat_proj_meetings"] : "";
		$arr["args"]["my_bugs_stat_start"] = isset($arr["request"]["my_bugs_stat_start"]) ? $arr["request"]["my_bugs_stat_start"] : "";
		$arr["args"]["my_bugs_stat_end"] = isset($arr["request"]["my_bugs_stat_end"]) ? $arr["request"]["my_bugs_stat_end"] : "";
		$arr["args"]["stat_proj_ppl"] = isset($arr["request"]["stat_proj_ppl"]) ? $arr["request"]["stat_proj_ppl"] : "";

		if ("stat_hrs_overview" === $arr["args"]["group"])
		{
			$arr["args"]["just_saved"] = null;
		}

		if (!empty($arr["request"]["proj_search_sbt"]))
		{
			$arr["args"]["proj_search_cust"] = isset($arr["request"]["proj_search_cust"]) ? $arr["request"]["proj_search_cust"] : "";
			$arr["args"]["proj_search_part"] = isset($arr["request"]["proj_search_part"]) ? $arr["request"]["proj_search_part"] : "";
			$arr["args"]["proj_search_name"] = isset($arr["request"]["proj_search_name"]) ? $arr["request"]["proj_search_name"] : "";
			$arr["args"]["proj_search_code"] = isset($arr["request"]["proj_search_code"]) ? $arr["request"]["proj_search_code"] : "";
			$arr["args"]["proj_search_arh_code"] = isset($arr["request"]["proj_search_arh_code"]) ? $arr["request"]["proj_search_arh_code"] : "";
			$arr["args"]["proj_search_proj_mgr"] = isset($arr["request"]["proj_search_proj_mgr"]) ? $arr["request"]["proj_search_proj_mgr"] : "";
			$arr["args"]["proj_search_task_name"] = isset($arr["request"]["proj_search_task_name"]) ? $arr["request"]["proj_search_task_name"] : "";
			$arr["args"]["proj_search_dl_from"] = isset($arr["request"]["proj_search_dl_from"]) ? $arr["request"]["proj_search_dl_from"] : "";
			$arr["args"]["proj_search_dl_to"] = isset($arr["request"]["proj_search_dl_to"]) ? $arr["request"]["proj_search_dl_to"] : "";
			$arr["args"]["proj_search_end_from"] = isset($arr["request"]["proj_search_end_from"]) ? $arr["request"]["proj_search_end_from"] : "";
			$arr["args"]["proj_search_end_to"] = isset($arr["request"]["proj_search_end_to"]) ? $arr["request"]["proj_search_end_to"] : "";
			$arr["args"]["proj_search_state"] = isset($arr["request"]["proj_search_state"]) ? $arr["request"]["proj_search_state"] : "";
			$arr["args"]["proj_search_sbt"] = 1;
			$arr["args"]["do_proj_search"] = 1;
		}
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["tf"] = automatweb::$request->arg("tf");
		$arr["assign_to"] = 0;
		$arr["b_id"] = automatweb::$request->arg("b_id");
		$arr["save_search_name"] = "";
		$arr["post_ru"] = aw_url_change_var("post_ru", null, post_ru());
		if($this->use_group === "settings_statuses")
		{
			foreach(get_instance(CL_BUG)->bug_statuses as $stid => $stat)
			{
				$arr["add_mail_group_bug"][$stid] = 0;
				$arr["add_mail_group_cust"][$stid] = 0;
			}
			foreach(get_instance(CL_DEVELOPMENT_ORDER)->get_status_list() as $stid => $stat)
			{
				$arr["add_mail_group_devo"][$stid] = 0;
			}
		}

		if($this->use_group === "projects")
		{
			$arr["filt_value"] = $request["filt_value"];
		}
	}

	/**
		@attrib name=assign_bugs
		@param sel optional
		@param post_ru optional
		@param assign_to optional
	**/
	function assign_bugs($arr)
	{
		if ($arr["assign_to"])
		{
			object_list::iterate_list($arr["sel"],"set_prop", "who", $arr["assign_to"]);
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=set_bug_status
		@param sel optional
		@param post_ru optional
		@param assign_to optional
	**/
	function set_bug_status($arr)
	{
		if ($arr["assign_to"])
		{
			object_list::iterate_list($arr["sel"],"set_prop", "bug_status", $arr["assign_to"]);
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=cut_b
		@param sel optional
		@param post_ru optional
	**/
	function cut_b($arr)
	{
		$_SESSION["bt"]["cut_bugs"] = array();
		foreach(safe_array($arr["sel"]) as $bug_id)
		{
			$_SESSION["bt"]["cut_bugs"][$bug_id] = $bug_id;
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=paste_b
	**/
	function paste_b($arr)
	{
		object_list::iterate_list($_SESSION["bt"]["cut_bugs"], "set_parent", $arr["b_id"] ? $arr["b_id"] : ($arr["tf"] ? $arr["tf"] : $arr["id"]));
		$_SESSION["bt"]["cut_bugs"] = null;
		return $arr["post_ru"];
	}

	function get_bugs_parent($tracker)
	{
		if ($this->can("view", $ret = $tracker->prop("bug_folder")))
		{
			return $ret;
		}
		return $tracker->id();
	}

	/**
		@attrib name=fetch_structure_in_xml
		@param id required type=int acl=view
	**/
	function fetch_structure_in_xml($arr)
	{
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"".aw_global_get("charset")."\" standalone=\"yes\"?>\n<response>\n";

		$bt = obj($arr["id"]);
		$pt = $this->get_bugs_parent($bt);

		$ot = new object_tree(array(
			"class_id" => array(CL_MENU,CL_BUG),
			"parent" => $pt
		));

		$this->_req_get_struct_xml($pt, $ot, $xml);

		$xml .= "</response>";
		die($xml);
	}

	function _req_get_struct_xml($parent, $ot, &$xml)
	{
		$this->_req_get_struct_xml_level++;
		foreach($ot->level($parent) as $obj)
		{
			$xml .= "<item><value>".$obj->id()."</value><text>".str_repeat("__", $this->_req_get_struct_xml_level).str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $obj->name())))."</text></item>\n";
			$this->_req_get_struct_xml($obj->id(), $ot, $xml);
		}
		$this->_req_get_struct_xml_level--;
	}

	function _search_res($arr)
	{
		if (!$arr["request"]["MAX_FILE_SIZE"])
		{
			return;
		}
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_bug_list_tbl_no_edit($t);

		$search_filt = $this->_get_bug_search_filt($arr["request"]);
		$ol = new object_list($search_filt);

		if ($arr["request"]["s_find_parens"] != 1 && $ol->count())
		{
			$bugs = $ol->arr();

			// now, filter out all bugs that have sub-bugs
			$sub_bugs = new object_list(array(
				"class_id" => CL_BUG,
				"parent" => $ol->ids()
			));

			foreach($sub_bugs->arr() as $sub_bug)
			{
				unset($bugs[$sub_bug->parent()]);
			}
			$ol = new object_list();
			$ol->add(array_keys($bugs));
		}

		$this->populate_bug_list_table_from_list($t, $ol, array(
			"path" => true,
			"bt" => $arr["obj_inst"],
			"add_bug_count" => true,
		), $arr["request"]);
	}

	function _get_bug_search_filt($r)
	{
		$res = array(
			"class_id" => CL_BUG,
			"lang_id" => array(),
			"site_id" => array()
		);

		$txtf = array("oid", "name", "bug_url", "bug_component", "bug_mail");
		foreach($txtf as $field)
		{
			if (isset($r["s_".$field]) and strlen(trim($r["s_".$field])))
			{
				$res[$field] = $this->_get_string_filt($r["s_".$field]);
			}
		}

		$sf = array("bug_status", "bug_class", "bug_severity", "bug_priority", "cust_status");
		foreach($sf as $field)
		{
			if (isset($r["s_".$field]) and (!is_string($r["s_".$field]) or strlen(trim($r["s_".$field]))))
			{
				$res[$field] = $r["s_".$field];
			}
		}

		if(isset($r["s_date_type"]))
		{
			switch($r["s_date_type"])
			{
				case 1:
					$prop = "created";
					break;

				case 2:
					$prop = "modified";
					break;
			}

			if($prop)
			{
				$from = date_edit::get_timestamp($r["s_date_from"]);
				$to = date_edit::get_timestamp($r["s_date_to"]);
				if($from > 1 && $to > 1)
				{
					$res[$prop] = new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $from, $to);
				}
				elseif($from > 1)
				{
					$res[$prop] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $from);
				}
				elseif($to > 1)
				{
					$res[$prop] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $to);
				}
			}
		}

		if (isset($r["s_monitors"]) and strlen(trim($r["s_monitors"])))
		{
			$res["CL_BUG.RELTYPE_MONITOR.name"] = $this->_get_string_filt($r["s_monitors"]);
		}

		if (isset($r["s_feedback_p"]) and strlen(trim($r["s_feedback_p"])))
		{
			$res["CL_BUG.bug_feedback_p.name"] = $this->_get_string_filt($r["s_feedback_p"]);
		}

		if (isset($r["s_finance_type"]) and $r["s_finance_type"])
		{
			if ($r["s_finance_type"] == -1)
			{
				$res[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array("logic" => "OR", "conditions" => array("CL_BUG.finance_type" => new obj_predicate_compare(OBJ_COMP_LESS, 1)))),
						new object_list_filter(array("logic" => "OR", "conditions" => array("CL_BUG.finance_type" => new obj_predicate_compare(OBJ_COMP_NULL))))
					)
				));
			}
			else
			{
				$res["CL_BUG.finance_type"] = $r["s_finance_type"];
			}
		}

		$cplx = array("who", "customer", "project");
		foreach($cplx as $field)
		{
			if (isset($r["s_".$field]) and strlen(trim($r["s_".$field])))
			{
				$res["CL_BUG.".$field.".name"] = $this->_get_string_filt($r["s_".$field]);
			}
		}

		if (isset($r["s_bug_type"]) and strlen(trim($r["s_bug_type"])))
		{
			$res["CL_BUG.bug_type(CL_META).name"] = $this->_get_string_filt($r["s_bug_type"]);
		}

		if (isset($r["s_bug_app"]) and strlen(trim($r["s_bug_app"])))
		{
			$res["CL_BUG.bug_app(CL_META).name"] = $this->_get_string_filt($r["s_bug_app"]);
		}

		if (isset($r["s_who_empty"]) and $r["s_who_empty"] == 1)
		{
			$res["who"] = new obj_predicate_compare(OBJ_COMP_EQUAL, "");
			unset($res["CL_BUG.who.name"]);
		}

		if (isset($r["s_bug_content"]) and strlen(trim($r["s_bug_content"])))
		{
			$res[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_BUG.RELTYPE_COMMENT.comment" => $this->_get_string_filt($r["s_bug_content"]),
					"bug_content" => $this->_get_string_filt($r["s_bug_content"]),
				)
			));
		}

		if (isset($r["s_createdby"]) and strlen(trim($r["s_createdby"])))
		{
			// map name to possible persons, get users for those and search by that
			$ul = new object_list(array(
				"class_id" => CL_USER,
				"CL_USER.RELTYPE_PERSON.name" => "%".$r["s_createdby"]."%",
				"lang_id" => array(),
				"site_id" => array()
			));
			if ($ul->count())
			{
				$res["createdby"] = $ul->names();
			}
			else
			{
				$res["oid"] = -1;
			}
		}
		return $res;
	}

	function _get_string_filt($s)
	{
		$this->dequote($s);
		// separated by commas delimited by "
		$p = array();
		$len = strlen($s);
		$cur_str = "";
		for ($i = 0; $i < $len; $i++)
		{
			if ($s[$i] === "\"" && $in_q)
			{
				// end of quoted string
				$p[] = $cur_str;
				$in_q = false;
			}
			else
			if ($s[$i] === "\"" && !$in_q)
			{
				$cur_str = "";
				$in_q = true;
			}
			else
			if ($s[$i] === "," && !$in_q)
			{
				$p[] = $cur_str;
				$cur_str = "";
			}
			else
			{
				$cur_str .= $s[$i];
			}
		}
		$p[] = $cur_str;
		$p = array_unique($p);

		return map("%%%s%%", $p);
	}

	function _search_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		// save search
		$tb->add_button(array(
			"name" => "save",
			"tooltip" => t("Salvesta otsing"),
			"action" => "save_search",
			"onClick" => "document.changeform.save_search_name.value=prompt('Sisesta nimi');",
			"img" => "save.gif",
		));

		// pick saved searches
		$s = safe_array($arr["obj_inst"]->meta("saved_searches"));
		$ss = array($this->mk_my_orb("change", array(
			"id" => $arr["obj_inst"]->id(),
			"group" => "search_t",
			"return_url" => $arr["request"]["return_url"])) => ""
		);
		foreach($s as $idx => $search)
		{
			if ($search["creator"] == aw_global_get("uid"))
			{
				$opr = $search["params"];
				$opr["id"] = $arr["obj_inst"]->id();
				$opr["group"] = "search";
				$opr["MAX_FILE_SIZE"] = 100000000;
				$url = $this->mk_my_orb("change", $opr);
				$ss[$url] = $search["name"];
			}
		}
		$html = html::select(array(
			"options" => $ss,
			"onchange" => "el=document.changeform.go_to_saved_search;window.location=el.options[el.selectedIndex].value",
			"name" => "go_to_saved_search",
			"value" => get_ru()
		));
		$tb->add_cdata($html);

		$tb->add_menu_button(array(
			"name" => "assign",
			"tooltip" => t("M&auml;&auml;ra"),
			"img" => "class_38.gif"
		));

		// list all people to assign to
		// list all my co-workers who are important to me, from crm
		$ppl = $this->get_people_list($arr["obj_inst"]);
		foreach($ppl as $p_oid => $p_name)
		{
			$tb->add_menu_item(array(
				"parent" => "assign",
				"text" => $p_name,
				"link" => "#",
				"onClick" => "document.changeform.assign_to.value=$p_oid;submit_changeform('assign_bugs')"
			));
		}
	}

	function _get_apps_tb($arr)
	{
		$parent = $arr["obj_inst"]->id();
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_BUG_APP_TYPE), $parent, '',array());
		$tb->add_delete_button();
		$tb->add_save_button();
	}

	function _get_apps_table($arr)
	{
		$parent = $arr["obj_inst"]->id();
		$t = $arr["prop"]["vcl_inst"];

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$t->set_caption(t("Rakendused"));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi")
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Seotud bugi t&uuml;&uuml;p"),
		));
		$types = array("" => t("--vali--"));
		if($f = $arr["obj_inst"]->prop("bug_type_folder"))
		{
			$ol = new object_list(array(
				"class_id" => CL_META,
				"parent" => $f,
			));
			$types += $ol->names();
		}
		$ol = new object_list(array(
			"parent" => $parent,
			"class_id" => array(CL_BUG_APP_TYPE)
		));
		foreach($ol->arr() as $oid => $o)
		{
			$type = $o->get_first_obj_by_reltype("RELTYPE_TYPE");
			if($type)
			{
				$tp = $type->id();
			}
			$t->define_data(array(
				"oid" => $oid,
				"name" => $o->name(),
				"type" => html::select(array(
					"options" => $types,
					"value" => $tp,
					"name" => "bug_app_types[".$oid."]",
				)),
			));
		}
	}

	function _set_apps_table($arr)
	{
		foreach($arr["request"]["bug_app_types"] as $app => $type)
		{
			$o = obj($app);
			$t = $o->get_first_obj_by_reltype("RELTYPE_TYPE");
			if($t)
			{
				$o->disconnect(array(
					"type" => "RELTYPE_TYPE",
					"from" => $t,
				));
			}
			$o->connect(array(
				"to" => $type,
				"type" => "RELTYPE_TYPE",
			));
		}
	}

	/**
		@attrib name=save_search all_args=1
	**/
	function save_search($arr)
	{
		if ($arr["save_search_name"] == "")
		{
			return $arr["post_ru"];
		}
		$search_params = array();

		foreach($arr as $k => $v)
		{
			if ($k[0] == "s" && $k[1] == "_")
			{
				$search_params[$k] = $v;
			}
		}

		$o = obj($arr["id"]);
		$ss = safe_array($o->meta("saved_searches"));
		$ss[] = array(
			"name" => $arr["save_search_name"],
			"params" => $search_params,
			"creator" => aw_global_get("uid")
		);
		$o->set_meta("saved_searches", $ss);
		$o->save();

		return $arr["post_ru"];
	}

	function _init_saved_searches($t)
	{
		$t->define_field(array(
			"name" => "who",
			"caption" => t("Kelle otsing"),
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
			"name" => "params",
			"caption" => t("Otsingu parameetrid"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "idx"
		));
	}

	function _saved_searches($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_saved_searches($t);

		$bug_props = $arr["obj_inst"]->get_property_list();
		$bugi = get_instance(CL_BUG);

		$u = get_instance(CL_USER);
		$ss = safe_array($arr["obj_inst"]->meta("saved_searches"));
		foreach($ss as $idx => $search)
		{
			$p = $u->get_person_for_uid($search["creator"]);
			$ps = array();
			foreach(safe_array($search["params"]) as $par_nm => $par_val)
			{
				if ($par_val != "")
				{
					if (is_array($par_val))
					{
						if (count($par_val))
						{
							if ($par_nm == "s_deadline")
							{
								$ts = date_edit::get_timestamp($par_val);
								if ($ts > 300)
								{
									$ps[] = $bug_props[$par_nm]["caption"]." = ".date("d.m.Y", $ts);
								}
							}
							else
							if ($par_nm == "s_bug_status")
							{
								$states = $bugi->get_status_list();
								$tmp = array();
								foreach($par_val as $state)
								{
									$tmp[] = $states[$state];
								}
								$ps[] = $bug_props[$par_nm]["caption"]." = ".join(",", $tmp);
							}
							else
							{
								$ps[] = $bug_props[$par_nm]["caption"]." = ".join(",", $par_val);
							}
						}
					}
					else
					{
						if ($par_nm == "s_who")
						{
							$ps[] = "Kellele = ".$par_val;
						}
						else
						{
							$ps[] = $bug_props[$par_nm]["caption"]." = ".$par_val;
						}
					}
				}
			}

			$opr = $search["params"];
			$opr["id"] = $arr["obj_inst"]->id();
			$opr["group"] = "search";
			$opr["MAX_FILE_SIZE"] = 100000000;
			$url = $this->mk_my_orb("change", $opr);

			$t->define_data(array(
				"name" => html::href(array(
					"url" => $url,
					"caption" => $search["name"]
				)),
				"idx" => $idx,
				"who" => $p->name(),
				"params" => join("<br>", $ps)
			));
		}
	}

	function callback_generate_scripts($arr)
	{
		unset($arr["request"]["class"]);
		unset($arr["request"]["action"]);
		$url = $this->mk_my_orb("list_only_fetch", $arr["request"]);

		$new_url = $this->mk_my_orb("new", array(), CL_BUG);
		$cur_url = aw_url_change_var("b_id", NULL);

		return "
		var last_bold_node;
		var last_bold_node_cont;
		function do_bt_table_switch(bugid, that)
		{
			url = '$url&b_id='+bugid;
			el = document.getElementById('bug_table');
			el.innerHTML=aw_get_url_contents(url);
			document.changeform.b_id.value=bugid;
			new_el = document.getElementById('add_bug_href');
			new_el.href = '$new_url&parent='+bugid+'&return_url='+encodeURIComponent(document.location.href);

			bm_url = document.getElementById('sync_url');
			bm_url.href = '$cur_url&b_id='+bugid;

			if (last_bold_node)
			{
				last_bold_node.innerHTML=last_bold_node_cont;
			}

			last_bold_node = that;
			last_bold_node_cont = that.innerHTML;
			that.innerHTML= '<b>'+that.innerHTML+'</b>';
		}";
	}

	/**
		@attrib name=list_only_fetch all_args=1
	**/
	function list_only_fetch($arr)
	{
		$p = array();
		$val = $this->_bug_list(array(
			"prop" => &$p,
			"request" => $arr,
			"obj_inst" => obj($arr["id"]),
		));
		header("Content-type: text/html; charset=".aw_global_get("charset"));

		echo ($p["value"]);
		aw_shutdown();
		die();
	}

	function __gantt_sort($a, $b)
	{
		$a_pri = $this->bug_i->get_sort_priority($a, $this->combined_priority_formula);
		$b_pri = $this->bug_i->get_sort_priority($b, $this->combined_priority_formula);
		return $a_pri == $b_pri ? 0 : ($a_pri > $b_pri ? -1 : 1);
	}

	function get_last_estimation_over_deadline_bugs()
	{
		return $this->over_deadline;
	}

	function get_estimated_end_time_for_bug($bug, $this_o = false)
	{
		$this->over_deadline = array();
		$p = $bug->prop("who");
		if (!$p)
		{
			return null;
		}
		$args = array(
			"request" =>  array(
				"filt_p" => $p,
			),
			"ret_b_time" => $bug->id(),
			"ret_b" => $bug,
			"obj_inst" => $this_o
		);
		return $this->_gantt($args);
	}

	function _gantt(&$arr)
	{
		$chart = new gantt_chart();
		$udata = is_object($arr["obj_inst"]) ? $arr["obj_inst"]->meta("gantt_user_ends") : array();
		$cur = aw_global_get("uid_oid");
		if(isset($udata[$cur]) && $cs = $udata[$cur])
		{
			$columns = $cs;
		}
		else
		{
			$columns = 7;
		}
		$this->gt_days_in_col = 1;
		if($columns > 10)
		{
			$this->gt_days_in_col = ceil($columns/10);
			$columns = 10;
		}
		$col_length = $this->gt_days_in_col*24*60*60;

		if (isset($arr["request"]["filt_p"]) and $this->can("view", $arr["request"]["filt_p"]))
		{
			$p = obj($arr["request"]["filt_p"]);
		}
		else
		{
			$u = get_instance(CL_USER);
			$p = obj($u->get_current_person());
		}

		if (is_object($arr["obj_inst"]))
		{
			$this->combined_priority_formula = $arr["obj_inst"]->prop("combined_priority_formula"); // required by get_undone_bugs_by_p(), __gantt_sort()
		}

		classload("core/date/date_calc");
		$range_start = get_day_start();
		$range_end = time() + $columns * $col_length;

		$subdivisions = 1;

		$has = false;
		$gt_list = $this->get_undone_bugs_by_p($p);
		$bi = get_instance(CL_BUG);
		foreach($gt_list as $gt)
		{
			$cdata = $this->get_gantt_bug_colors($gt);

			$nm = parse_obj_name($gt->name());
			if ($gt->customer)
			{
				if ($gt->prop("customer.short_name") != "")
				{
					$nm .= " / ".$gt->prop("customer.short_name");
				}
				else
				{
					$nm .= " / ".$gt->prop("customer.name");
				}
			}
			if ($gt->finance_type)
			{
				$ft = get_instance(CL_BUG)->get_finance_types();
				$nm .= " / ".$ft[$gt->finance_type];
			}

			$chart->add_row (array (
				"name" => $gt->id(),
				"title" => $nm,
				"uri" => html::get_change_url(
					$gt->id(),
					array("return_url" => get_ru())
				),
				"row_name_class" => $cdata["class"],
			));
			if (!empty($arr["ret_b"]) && $gt->id() == $arr["ret_b"]->id())
			{
				$has = true;
			}
		}

		if (!$has && !empty($arr["ret_b"]))
		{
			$gt_list[] = $arr["ret_b"];
			usort($gt_list, array($this, "__gantt_sort"));
		}
		$this->day2wh = $this->get_person_whs($p);

		$this->gt_start = $this->get_next_avail_time_from(time(), $this->day2wh);

		$sect = $this->get_sect();
		$curday = 0;
		$this->job_count = count($gt_list);
		foreach ($gt_list as $gt)
		{
			$this->gt_start = $this->get_next_avail_time_from($this->gt_start, $this->day2wh);
			$rbp = $gt->meta("real_by_p");
			if(isset($rbp[$p->id()]))
			{
				$real_hrs = $rbp[$p->id()];
				$hrs_by_p = true;
			}
			else
			{
				$real_hrs = $gt->prop("num_hrs_real");
				$hrs_by_p = false;
			}

			$gbp = $gt->meta("guess_by_p");
			if(isset($gbp[$p->id()]) && $gbp[$p->id()] > 0 && $hrs_by_p)
			{
				$guess_hrs = $gbp[$p->id()];
			}
			elseif($gt->prop("num_hrs_guess") > 0)
			{
				$guess_hrs = $gt->prop("num_hrs_guess");
			}
			else
			{
				$guess_hrs = 0;
			}

			if ($guess_hrs > 0)
			{
				$length = $guess_hrs * 3600 - ($real_hrs * 3600);
				if ($length < 0)
				{
					$length = 3600;
				}
			}
			else
			{
				$length = 7200;
			}
			$this->job_hrs += $length;
			$this->check_sect($sect, $curday);
			$cdata = $this->get_gantt_bug_colors($gt);
			$color = $cdata["color"];
			if ($length > $sect[$curday]["len"])
			{
				// split into parts
				$tot_len = $length;
				$length = $sect[$curday]["len"];
				$remaining_len = $tot_len - $length;
				$title = parse_obj_name($gt->name())."<br>( ".date("d.m.Y H:i", $this->gt_start)." - ".date("d.m.Y H:i", $this->gt_start + $length)." ) ";
				$bar = array (
					"id" => $gt->id (),
					"row" => $gt->id (),
					"start" => $this->gt_start,
					"length" => $length,
					"title" => $title,
					"colour" => $color,
				);

				$chart->add_bar ($bar);
				$this->gt_start += $length;
				$curday++;

				while($remaining_len > 0)
				{
					$this->check_sect($sect, $curday);
					$length = min($remaining_len, $sect[$curday]["len"]);
					$remaining_len -= $length;
					$this->gt_start = $this->get_next_avail_time_from($this->gt_start, $this->day2wh);
					$title = $gt->name()."<br>( ".date("d.m.Y H:i", $this->gt_start)." - ".date("d.m.Y H:i", $this->gt_start + $length)." ) ";
					$bar = array (
						"id" => $gt->id (),
						"row" => $gt->id (),
						"start" => $this->gt_start,
						"length" => $length,
						"title" => $title,
						"colour" => $color,
					);
					$chart->add_bar ($bar);
					$this->gt_start += $length;
					$sect[$curday]["len"] -= $length;
				}
			}
			else
			{
				$title = parse_obj_name($gt->name())."<br>( ".date("d.m.Y H:i", $this->gt_start)." - ".date("d.m.Y H:i", $this->gt_start + $length)." ) ";
				$bar = array (
					"id" => $gt->id (),
					"row" => $gt->id (),
					"start" => $this->gt_start,
					"length" => $length,
					"title" => $title,
					"colour" => $color,
				);
				$sect[$curday]["len"] -= $length;
				$chart->add_bar ($bar);
				$this->gt_start += $length;
			}

			if ($gt->prop("deadline") > 300 && $this->gt_start > $gt->prop("deadline"))
			{
				$this->over_deadline[$gt->id()] = $gt;
			}

			if (isset($arr["ret_b_time"]) and $gt->id() == $arr["ret_b_time"])
			{
				return $this->gt_start;
			}
		}
		$this->job_end = $this->gt_start;
		$chart->configure_chart (array (
			"chart_id" => "bt_gantt",
			"style" => "aw",
			"start" => $range_start,
			"end" => $range_end,
			"columns" => $columns,
			"subdivisions" => $subdivisions,
			"timespans" => $subdivisions,
			"width" => 850,
			"row_height" => 10,
			"column_length" => $col_length,
			"timespan_range" => $col_length,
			"row_dfn" => t("&Uuml;lesanne"),
		));

		### define columns
		$i = 0;
		$days = array ("P", "E", "T", "K", "N", "R", "L");

		while ($i < $columns)
		{
			$day_start = (get_day_start() + ($i * $this->gt_days_in_col * 86400));
			$day = date ("w", $day_start);
			$date = date ("j/m/Y", $day_start);
			$uri = aw_url_change_var ("mrp_chart_length", 1);
			$uri = aw_url_change_var ("mrp_chart_start", $day_start, $uri);
			$chart->define_column (array (
				"col" => ($i + 1),
				"title" => $days[$day] . " - " . $date,
				"uri" => $uri,
			));
			$i++;
		}

		$arr["prop"]["value"] = $chart->draw_chart ();
		return $chart;
	}

	/**
	@attrib name=aw_firefoxtools_gantt
	@param id required type=int
	**/
	function aw_firefoxtools_gantt($arr)
	{
		$arr["obj_inst"] = obj($arr["id"]);
		$chart = $this->_gantt($arr);
		$rows = $chart->get_rows();

		$out = "menu = [";
		foreach($rows as $row)
		{
			$name = utf8_encode(html_entity_decode($row["title"]));
			$url = $row["uri"];
			$class = $row["name_class"];

			$out .= '{"name":"'.$name.'",'.
				'"url":"'.$url.'",'.
				'"class":"'.strtolower($class).'"'.
			"},";
		}
		$out .= '];';
		die($out);
	}

	/**
	@attrib name=aw_firefoxtools_bugs_history
	**/
	function aw_firefoxtools_bugs_history($arr)
	{
		$cur_p = get_current_person();
		$prevm = time()-3600*24*10;
		$filter = array(
			"class_id" => array(CL_TASK_ROW),
			"CL_TASK_ROW.task.class_id" => CL_BUG,
			"lang_id" => array(),
			"site_id" => array(),
			//"created" => new obj_predicate_compare(OBJ_COMP_GREATER, $prevm),
			"sort_by" => "objects.created desc",
			"impl" => $cur_p->id(),
			"limit" => 100,
		);

		$t = new object_data_list(
			$filter,
			array(
				CL_TASK_ROW => array(
					"task"
				),
			)
		);
		$tasks = array();
		$count = 0;
		foreach($t->list_data as $data)
		{
			if($count >= 40) break;
			if(!$tasks[$data["task"]] && $this->can("view" , $data["task"]))
			{
				$tasks[$data["task"]] = $data["task"];
			}
			$count++;
		}

		$ol = new object_list();
		if(sizeof($tasks))
		{
			$ol->add($tasks);
		}

		$out = "menu = [";
		foreach($ol->arr() as $o)
		{
			$name = utf8_encode( html_entity_decode( $o->name()  ));
			$url = $this->mk_my_orb("change", array(
				"id" => $o->id(),
			), "bug");
			$class = "";

			$out .= '{"name":"'.$name.'",'.
				'"url":"'.$url.'",'.
				'"modified":"'.$o->modified.'"'.
			"},";
		}
		$out .= '];';
		die($out);
	}

	/**
	@attrib name=aw_firefoxtools_new_bug_url
	@param id required type=int
	@param bug_url required type=string
	**/
	function aw_firefoxtools_new_bug_url($arr)
	{
		$tree = array();

		$bug_url = $arr["bug_url"];
		$arr["obj_inst"] = obj($arr["id"]);

		$ol = new object_list(array(
			"class_id" => CL_BUG,
			"bug_url" => "%$bug_url%",
			"limit" => "0,3",
		));

		// find parent folder of bug even if under subbugs
		if($ol->count())
		{
			$bug = $ol->begin();
			array_push($tree, $bug);
			$parent_id = $bug->parent();
			$i = 0;
			while(true)
			{
				$ol = new object_list(array(
					"oid" => $parent_id,
				));
				$o = $ol->begin();
				if($o->prop("name") === "Kliendid")
				{
					array_push($tree, $o);
					$tree = array_reverse($tree);

					// check if we found correct place for our new bug
					if($tree[0]->name()=="Kliendid" &&
						strlen($tree[1]->name())==1 )
					{
						die("http://intranet.automatweb.com/automatweb/orb.aw?class=bug&action=new&parent=".$tree[2]->id());
					}
					die("error1");
				}
				else
				{
					$parent_id = $o->parent();
					array_push($tree, $o);
				}
				$i++;
				if ($i==15)
				{
					die("error2");
				}
			}
		}
		die("error3");
	}

	function check_sect(&$sect, &$curday)
	{
		if(!isset($sect[$curday]) or !is_array($sect[$curday]))
		{
			$this->gt_start = $this->get_next_avail_time_from($this->gt_start, $this->day2wh);
			$sect = $this->get_sect();
			$curday = 0;
			$this->gt_start = $sect[$curday]["start"];
		}
		elseif($sect[$curday]["len"] <= 0)
		{
			$curday++;
			if(is_array($sect[$curday]))
			{
				$this->gt_start = $sect[$curday]["start"];
			}
		}
	}

	function get_sect()
	{
		$sect_len = 0;
		$i = 0;
		$sect["lens"] = array();
		while($i < $this->gt_days_in_col)
		{
			$cur =  $this->gt_start + $i * 24 * 60 * 60;
			$day_info = $this->day2wh[date("w", $cur)];
			$day_start = $day_info[0];
			$day_end = $day_info[1];
			$chlen = $day_end - $day_start;
			if($chlen <= 0)
			{
				$daylen = 0;
			}
			else
			{

				$st =  mktime($day_start, 0, 0, date('m', $cur), date('d', $cur), date('Y', $cur));
				if($this->gt_start == $cur)
				{
					$st2 = mktime(date('H', $cur), date('i', $cur), 0, date('m', $cur), date('d', $cur), date('Y', $cur));
					if($st2 > $st)
					{
						$st = $st2;
					}
				}
				$end = mktime($day_end, 0, 0, date('m', $cur), date('d', $cur), date('Y', $cur));
				$daylen = $end - $st;
			}
			//$sect_len += $daylen;
			$sect_lens[] = array(
				"len" => $daylen,
				"start" => mktime($day_start, 0, 0, date('m', $cur), date('d', $cur), date('Y', $cur)),
				//"end" => mktime($day_end, 0, 0, date('m', $cur), date('d', $cur), date('Y', $cur)),
			);
			$i++;
		}
		return $sect_lens;
	}
	function get_gantt_bug_colors($gt)
	{
		$color = "silver";
		$class = "VclGanttRowName btGanttRowName";
		$deadline = $gt->prop("deadline");
		if($deadline > 1)
		{
			if($deadline < time())
			{
				$color = "red";
				$class .= " deadline0";
			}
			elseif(($deadline - 5*24*60*60) < time() && $deadline - 4*24*60*60 > time())
			{
				$color = "yellow";
				$class .= " deadline5";
			}
			elseif(($deadline - 4*24*60*60) < time() && $deadline - 3*24*60*60 > time())
			{
				$color = "#FFD400";
				$class .= " deadline4";
			}
			elseif(($deadline - 3*24*60*60) < time() && $deadline - 2*24*60*60 > time())
			{
				$color = "#FFA900";
				$class .= " deadline3";
			}
			elseif(($deadline - 2*24*60*60) < time() && $deadline - 1*24*60*60 > time())
			{
				$color = "#FF7E00";
				$class .= " deadline2";
			}
			elseif(($deadline - 1*24*60*60) < time() && $deadline > time())
			{
				$color = "#FF5300";
				$class .= " deadline1";
			}
		}

		$status = $gt->prop("bug_status");
		if($status == 10)
		{
			$color = "green";
			$class .= " feedback";
		}
		elseif($status == 11)
		{
			$color = "black";
			$class .= " fatalerror";
		}
		return array(
			"color" => $color,
			"class" => $class,
		);
	}

	function validate_cp_formula($formula)
	{
		$errors = array();
		$allowed_words = array(
			// bug status constants
			"BUG_OPEN", "BUG_INPROGRESS",	"BUG_DONE", "BUG_TESTED", "BUG_CLOSED", "BUG_INCORRECT", "BUG_NOTREPEATABLE", "BUG_NOTFIXABLE", "BUG_FATALERROR", "BUG_FEEDBACK",

			// array functions
			"array_ change_ key_ case", "array_ chunk", "array_ combine", "array_ count_ values", "array_ diff_ assoc", "array_ diff_ key", "array_ diff_ uassoc", "array_ diff_ ukey", "array_ diff", "array_ fill_ keys", "array_ fill", "array_ filter", "array_ flip", "array_ intersect_ assoc", "array_ intersect_ key", "array_ intersect_ uassoc", "array_ intersect_ ukey", "array_ intersect", "array_ key_ exists", "array_ keys", "array_ map", "array_ merge_ recursive", "array_ merge", "array_ multisort", "array_ pad", "array_ pop", "array_ product", "array_ push", "array_ rand", "array_ reduce", "array_ reverse", "array_ search", "array_ shift", "array_ slice", "array_ splice", "array_ sum", "array_ udiff_ assoc", "array_ udiff_ uassoc", "array_ udiff", "array_ uintersect_ assoc", "array_ uintersect_ uassoc", "array_ uintersect", "array_ unique", "array_ unshift", "array_ values", "array_ walk_ recursive", "array_ walk", "array", "arsort", "asort", "compact", "count", "current", "each", "end", "extract", "in_ array", "key", "krsort", "ksort", "list", "natcasesort", "natsort", "next", "pos", "prev", "range", "reset", "rsort", "shuffle", "sizeof", "sort", "uasort", "uksort", "usort",

			// math functions
			"abs", "acos", "acosh", "asin", "asinh", "atan2", "atan", "atanh", "base_ convert", "bindec", "ceil", "cos", "cosh", "decbin", "dechex", "decoct", "deg2rad", "exp", "expm1", "floor", "fmod", "getrandmax", "hexdec", "hypot", "is_ finite", "is_ infinite", "is_ nan", "lcg_ value", "log10", "log1p", "log", "max", "min", "mt_ getrandmax", "mt_ rand", "mt_ srand", "octdec", "pi", "pow", "rad2deg", "rand", "round", "sin", "sinh", "sqrt", "srand", "tan", "tanh",

			// string functions
			"addcslashes", "addslashes", "bin2hex", "chop", "chr", "chunk_ split", "convert_ cyr_ string", "convert_ uudecode", "convert_ uuencode", "count_ chars", "crc32", "crypt", "explode", "get_ html_ translation_ table", "hebrev", "hebrevc", "html_ entity_ decode", "htmlentities", "htmlspecialchars_ decode", "htmlspecialchars", "implode", "join", "levenshtein", "localeconv", "ltrim", "md5", "metaphone", "ord", "parse_ str", "quoted_ printable_ decode", "quotemeta", "rtrim", "sha1", "similar_ text", "soundex", "sscanf", "str_ getcsv", "str_ ireplace", "str_ pad", "str_ repeat", "str_ replace", "str_ rot13", "str_ shuffle", "str_ split", "str_ word_ count", "strcasecmp", "strchr", "strcmp", "strcoll", "strcspn", "strip_ tags", "stripcslashes", "stripos", "stripslashes", "stristr", "strlen", "strnatcasecmp", "strnatcmp", "strncasecmp", "strncmp", "strpbrk", "strpos", "strrchr", "strrev", "strripos", "strrpos", "strspn", "strstr", "strtok", "strtolower", "strtoupper", "strtr", "substr_ compare", "substr_ count", "substr_ replace", "substr", "trim", "ucfirst", "ucwords", "wordwrap",

			// date&time functions
			"checkdate", "date_ create", "date_ date_ set", "date_ default_ timezone_ get", "date_ default_ timezone_ set", "date_ format", "date_ isodate_ set", "date_ modify", "date_ offset_ get", "date_ parse", "date_ sun_ info", "date_ sunrise", "date_ sunset", "date_ time_ set", "date_ timezone_ get", "date_ timezone_ set", "date", "getdate", "gettimeofday", "gmdate", "gmmktime", "gmstrftime", "idate", "localtime", "microtime", "mktime", "strftime", "strptime", "strtotime", "time", "timezone_ abbreviations_ list", "timezone_ identifiers_ list", "timezone_ name_ from_ abbr", "timezone_ name_ get", "timezone_ offset_ get", "timezone_ open", "timezone_ transitions_ get"
		);
		$global_vars = array("$_SERVER", "$HTTP_SERVER_VARS", "$_SESSION", "$HTTP_SESSION_VARS", "$_COOKIE", "$HTTP_COOKIE_VARS", "$_ENV", "$HTTP_ENV_VARS", "$GLOBALS", "$_FILES", "$HTTP_POST_FILES");
		$tokenized_formula = token_get_all("<?php " . $formula . "?>");

		foreach ($tokenized_formula as $token_data)
		{
			if (is_array($token_data))
			{
				switch ($token_data[0])
				{
					case T_STRING:
						if (!in_array($token_data[1], $allowed_words))
						{
							$errors[] = sprintf(t("keelatud nimi: %s"), $token_data[1]);
						}
						break;

					case T_EVAL:
						$errors[] = t("eval() keelatud.");
						break;

					case T_GLOBAL:
						$errors[] = t("global keelatud.");
						break;

					case T_VARIABLE:
						if (in_array($token_data[1], $global_vars))
						{
							$errors[] = t("globaalsed muutujad keelatud.");
						}
						break;

					case T_INCLUDE:
					case T_INCLUDE_ONCE:
					case T_REQUIRE:
					case T_REQUIRE_ONCE:
						$errors[] = t("include() keelatud.");
						break;
				}
			}
			elseif ("`" === $token_data)
			{
				$errors[] = t("'backtick' operaator keelatud.");
			}
		}

		return $errors;
	}

	/**
		@attrib name=add_s_res_to_p_list
	**/
	function add_s_res_to_p_list($arr)
	{
		$o = obj($arr["id"]);
		$persons = $o->meta("imp_p");
		foreach(safe_array($arr["sel"]) as $p_id)
		{
			$persons[aw_global_get("uid")][$p_id] = $p_id;
		}
		$o->set_meta("imp_p", $persons);
		$o->save();
		return $arr["post_ru"];
	}

	/**
		@attrib name=remove_p_from_l_list
	**/
	function remove_p_from_l_list($arr)
	{
		$o = obj($arr["id"]);
		$persons = $o->meta("imp_p");
		foreach(safe_array($arr["sel"]) as $p_id)
		{
			unset($persons[aw_global_get("uid")][$p_id]);
		}
		$o->set_meta("imp_p", $persons);
		$o->save();
		return $arr["post_ru"];
	}

	/**
		@attrib name=co_autocomplete_source
		@param sp_p_co optional
	**/
	function co_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"name" => $arr["sp_p_co"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 100
		));
		return $ac->finish_ac($ol->names());
	}

	/**
		@attrib name=p_autocomplete_source
		@param sp_p_p optional
	**/
	function p_autocomplete_source($arr)
	{
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);

		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"name" => $arr["sp_p_p"]."%",
			"lang_id" => array(),
			"site_id" => array(),
			"limit" => 200
		));
		return $ac->finish_ac($ol->names());
	}

	function get_people_list($bt)
	{
		$ret = array();
		$persons = $bt->meta("imp_p");
		$persons = safe_array($persons[aw_global_get("uid")]);

		if (!count($persons))
		{
			return array();
		}

		$ol = new object_list(array(
			"oid" => $persons,
			"lang_id" => array(),
			"site_id" => array()
		));
		return $ol->names();
	}

	function generate_mon_bug_tree($arr)
	{
		$this->tree->add_item(0,array(
			"id" => $this->self_id,
			"name" => $this->tree_root_name,
		));

		// list all monitors
		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_BUG,
			"type" => "RELTYPE_MONITOR"
		));
		foreach($conns as $con)
		{
			$this->tree->add_item($this->self_id, array(
				"id" => $con["to"],
				"name" => $con["to.name"]
			));
		}
	}

	/**
		@attrib name=get_node_monitor all_args=1
	**/
	function get_node_monitor($arr)
	{
		$node_tree = get_instance("vcl/treeview");
		$node_tree->start_tree (array (
			"type" => TREE_DHTML,
			"tree_id" => "bug_tree",
			"branch" => 1,
		));
		$bugi = get_instance(CL_BUG);

		$po = obj();
		if ($this->can("view", $arr["parent"]))
		{
			$po = obj($arr["parent"]);
		}
		if ($po->class_id() == CL_CRM_PERSON)
		{
			// only statuses
			foreach($bugi->get_status_list() as $sid => $sn)
			{
				if ($arr["b_stat"] == $sid)
				{
					$sn = "<b>".$sn."</b>";
				}
				$node_tree->add_item(0, array(
					"id" => $arr["parent"]."_".$sid,
					"name" => $sn,
					"url" => html::get_change_url(
						$arr["inst_id"],
						array(
							"group" => "by_monitor",
							"b_stat" => $sid,
							"b_mon" => $arr["parent"]
						)
					)
				));
			}
			die($node_tree->finalize_tree());
		}

		// list all monitors
		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_BUG,
			"type" => "RELTYPE_MONITOR"
		));
		$mons = array();
		foreach($conns as $con)
		{
			$mons[$con["to"]] = $con["to.name"];
		}
		foreach($mons as $_to => $_to_name)
		{
			if ($_to == $arr["b_mon"])
			{
				$_to_name = "<b>".$_to_name."</b>";
			}
			$node_tree->add_item(0, array(
				"id" => $_to,
				"name" => $_to_name,
				"iconurl" => icons::get_icon_url(CL_CRM_PERSON),
				"url" => html::get_change_url(
					$arr["inst_id"],
					array(
						"group" => "by_monitor",
						"b_mon" => $_to
					)
				)
			));

			// add statuses under ppl

			foreach($bugi->get_status_list() as $sid => $sn)
			{
				if ($arr["b_stat"] == $sid && $_to == $arr["b_mon"])
				{
					$sn = "<b>".$sn."</b>";
				}
				$node_tree->add_item($_to, array(
					"id" => $_to."_".$sid,
					"name" => $sn,
					"url" => html::get_change_url(
						$arr["inst_id"],
						array(
							"group" => "by_monitor",
							"b_stat" => $sid,
							"b_mon" => $_to
						)
					)
				));
			}
		}

		die($node_tree->finalize_tree());
	}

	function get_person_whs($p)
	{
		$whs = $p->prop("work_hrs");
		$ret = array();
		if ($whs == "")
		{
			return array(
				0 => array(0,0),
				1 => array(9, 17),
				2 => array(9, 17),
				3 => array(9, 17),
				4 => array(9, 17),
				5 => array(9, 17),
				6 => array(0, 0),
			);
		}
		else
		{
			$lines = explode("\n", $whs);
			$lut = array(
				"E" => 1,
				"T" => 2,
				"K" => 3,
				"N" => 4,
				"R" => 5,
				"L" => 6,
				"P" => 0
			);
			foreach($lines as $l)
			{
				$l = trim($l);
				if ($l == "")
				{
					continue;
				}
				list($d, $hrs) = explode(":", $l);
				$ret[$lut[$d]] = explode("-", trim($hrs));
			}
		}
		return $ret;
	}

	function get_next_avail_time_from($tm, $day2wh)
	{
		if ($this->_rq_lev > 7)
		{
			error::raise(array(
				"id" => "ERR_TIME_LOOP",
				"msg" => t("bug_tracker::get_next_avail_time_from(): time is in loop!")
			));
			die();
		}
		$this->_rq_lev++;
		$hr = date("H", $tm);
		$day = date("w", $tm);
		$day_start = isset($day2wh[$day][0]) ? $day2wh[$day][0] : 0;
		$day_end = isset($day2wh[$day][1]) ? $day2wh[$day][1] : 0;
		if ($day_start == $day_end || $hr >= $day_end)
		{
			$rv = $this->get_next_avail_time_from(mktime(0,0,0, date("m", $tm), date("d", $tm)+1, date("Y", $tm)), $day2wh);
			$this->_rq_lev--;
			return $rv;
		}

		if ($hr < $day_start)
		{
			$this->_rq_lev--;
			return mktime($day_start, 0, 0, date("m", $tm), date("d", $tm), date("Y", $tm));
		}
		$this->_rq_lev--;
		return $tm;
	}

	function get_undone_bugs_by_p($p)
	{
		// get all goals/tasks
		$ft = array(
			"class_id" => CL_BUG,
			"bug_status" => array(bug::BUG_OPEN, bug::BUG_INPROGRESS, bug::BUG_FATALERROR, bug::BUG_TESTING, bug::BUG_VIEWING),
			"CL_BUG.who.name" => $p->name(),
			"lang_id" => array(),
			"site_id" => array()
		);
		$ot = new object_tree($ft);
		$gt_list = $ot->to_list();
		// add bugs that are marked as need feedback from the person
		$gt_list->add(new object_list(array(
			"class_id" => CL_BUG,
			"bug_status" => array(bug::BUG_FEEDBACK),
			"CL_BUG.bug_feedback_p.name" => $p->name(),
			"lang_id" => array(),
			"site_id" => array()
		)));

		$bugs = $gt_list->arr();

		// now, filter out all bugs that have sub-bugs
		$sub_bugs = new object_list(array(
			"class_id" => CL_BUG,
			"parent" => $gt_list->ids(),
			"lang_id" => array(),
			"site_id" => array(),
			"bug_status" => new obj_predicate_not(bug::BUG_CLOSED)
		));

		foreach($sub_bugs->arr() as $sub_bug)
		{
			unset($bugs[$sub_bug->parent()]);
		}

		$gt_list = new object_list();
		$gt_list->add(array_keys($bugs));
		$gt_list->sort_by_cb(array(
			&$this, "__gantt_sort"
		));

		$rv = $gt_list->arr();
		foreach($rv as $idx => $bug)
		{
			if ($bug->prop("bug_predicates") != "")
			{
				$preds = explode(",", $bug->prop("bug_predicates"));
				$preds_done = true;
				foreach($preds as $pred_id)
				{
					$pred_id = str_replace("#", "", $pred_id);
					if ($this->can("view", $pred_id))
					{
						$predo = obj($pred_id);
						if ($predo->prop("bug_status") < 3 || $predo->prop("bug_status") == bug::BUG_FATALERROR || $predo->prop("bug_status") == bug::BUG_FEEDBACK)
						{
							$preds_done = false;
						}
					}
				}
				if (!$preds_done)
				{
					unset($rv[$idx]);
				}
			}
		}
		return $rv;
	}

	/**
		@attrib name=nag_about_unestimated_bugs nologin=1
	**/
	function nag_about_unestimated_bugs($arr)
	{
		// auth as me
		aw_switch_user(array("uid" => "kix"));

		// get list of all users to whom bugs are assigned
		$c = new connection();
		$users = array();
		foreach($c->find(array("from.class_id" => CL_BUG, "type" => "RELTYPE_MONITOR")) as $c)
		{
			$users[$c["to"]] = $c["to"];
		}

		$us = array();
		foreach($users as $user)
		{
			// list&sort bugs for that user
			// get first 10 and nag him about their estimated lengths
			$p = obj($user);
			$us[$p->name()] = $p;
		}

		foreach($us as $p)
		{
			echo "user ".$p->name()." <br>";

			$nag_about = $this->get_unestimated_bugs_by_p($p);

			if (count($nag_about) > 0)
			{
				$mail = sprintf(t("Tere!\nMina olen AW Bugtrack. Sul (%s) on prognoositavad ajad m22ramata nendele bugidele, palun tee seda kohe!\n\n"), $p->name());
				foreach($nag_about as $nb)
				{
					$mail .= $nb->name()." ".obj_link($nb->id())."\n";
				}
				$mail .= "\n\nLihtsalt saad seda teha siit:\n";
				$mail .= "http://intranet.automatweb.com/automatweb/orb.aw?class=bug_tracker&action=change&id=142821&group=unestimated_bugs";
				echo "send to ".$p->prop("email.mail")."<br>".nl2br($mail)." <br><br><br><br>";
				send_mail($p->prop("email.mail"), t("Bugtracki M22ramata ajad"), $mail);
			}
		}

		echo "<hr>";

		get_instance(CL_BUG);
		// get all bugs that are needs feedback and send mail to their creators
		$ol = new object_list(array(
			"class_id" => CL_BUG,
			"lang_id" => array(),
			"site_id" => array(),
			"bug_status" => bug::BUG_FEEDBACK,
		));

		$bug2uid = array();
		foreach($ol->arr() as $o)
		{
			if ($this->can("view", $o->prop("bug_feedback_p")))
			{
				$bug2uid[$o->prop("bug_feedback_p")][] = $o;
			}
			else
			{
				$bug2uid[$o->createdby()][] = $o;
			}
		}

		$u = get_instance("users");
		foreach($bug2uid as $b_uid => $bugs)
		{
			if ($this->can("view", $b_uid))
			{
				$b_person = obj($b_uid);
				$eml = $b_person->prop_str("email");
			}
			else
			{
				$u_oid = $u->get_oid_for_uid($b_uid);
				if (!$this->can("view", $u_oid))
				{
					continue;
				}
				$uo = obj($u_oid);

				$eml = $uo->prop("email");
			}

			$ct = "Tere!\nMina olen AW Bugtrack. Sul on vastamata vajab tagasisidet buge:\n";
			foreach($bugs as $bug)
			{
				$ct .= obj_link($bug->id())." ".$bug->name()."\n";
			}
			$ct .= "\n\nEdu vastamisel!\n";

			echo "send to ".$eml."<br>".nl2br($ct)." <br><br><br><br>";
			send_mail($eml, t("Bugtracki vastamata bugid"), $ct);
		}
		die(t("all done"));
	}

	function get_unestimated_bugs_by_p($p)
	{
		$bugs = $this->get_undone_bugs_by_p($p);
		$cnt = 0;
		$nag_about = array();
		$user = $p->instance()->has_user($p);
		foreach($bugs as $bug)
		{
			if ($cnt > 9)
			{
				break;
			}
			if ($bug->prop("num_hrs_guess") == 0)
			{
				$gbp = $bug->meta("guess_by_p");
				$p = get_current_person()->id();
				if(!$gbp[$p])
				{
					$cnt++;
					$nag_about[] = $bug;
				}
			}
		}

		return $nag_about;
	}

	function _init_unestimated_table(&$t, $p)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Arendus&uuml;lesanne"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "time_bug",
			"caption" => t("Prognoos"),
			"align" => "center"
		));
		if($p->id() == obj(aw_global_get("uid_oid"))->get_first_obj_by_reltype("RELTYPE_PERSON")->id())
		{
			$t->define_field(array(
				"name" => "time_p",
				"caption" => t("Isiklik prognoos"),
				"align" => "center"
			));
		}
	}

	function _unestimated_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$p = get_current_person();
		if ($arr["request"]["filt_p"])
		{
			$p = obj($arr["request"]["filt_p"]);
		}
		$this->_init_unestimated_table($t, $p);

		$this->combined_priority_formula = $arr["obj_inst"]->prop("combined_priority_formula"); // required by get_unestimated_bugs_by_p()

		foreach($this->get_unestimated_bugs_by_p($p) as $bug)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($bug),
				"time_bug" => html::textbox(array(
					"name" => "bugs[".$bug->id()."][bug]",
					"value" => $bug->prop("num_hrs_guess"),
					"size" => 5
				)),
				"time_p" => html::textbox(array(
					"name" => "bugs[".$bug->id()."][p]",
					"value" => 0,
					"size" => 5
				)),
			));
		}
		$t->set_caption(t("Esimesed 10 ennustamata bugi"));
		$t->set_sortable(false);
	}

	function _save_estimates($arr)
	{
		$cp = get_current_person();
		$p = $cp->id();
		foreach(safe_array($arr["request"]["bugs"]) as $bid => $data)
		{
			$bo = obj($bid);
			if($est = $data["bug"])
			{
				$bo->set_prop("num_hrs_guess", $est);
				$bo->save();
			}
			if($est = $data["p"])
			{
				$o = obj();
				$o->set_class_id(CL_TASK_ROW);
				$o->set_parent($bid);
				$o->set_prop("done" , 1);
				$o->set_prop("task" , $bid);
				$o->set_prop("add_wh_guess", $est);
				$o->set_comment(sprintf(t("Isiku prognoositud tundide arv muudeti %s => %s"), 0, $est));
				$o->save();
				$bo->connect(array(
					"to" => $o->id(),
					"type" => "RELTYPE_COMMENT"
				));
				$gbp = $bo->meta("guess_by_p");
				$gbp[$p] = $est;
				$bo->set_meta("guess_by_p", $gbp);
				$bo->save();
			}
		}
	}

	function _get_statuses_devo_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "status",
			"align" => "center",
			"caption" => t("Staatus"),
		));
		$t->define_field(array(
			"name" => "disp_devo",
			"align" => "center",
			"caption" => t("Kuvamine"),
		));
		$t->define_field(array(
			"name" => "mail_groups",
			"align" => "center",
			"caption" => t("Meilide saatmine"),
		));
		$t->set_caption(t("Arendustellimuse staatused"));
		$doi = get_instance(CL_DEVELOPMENT_ORDER);
		$sdd = $arr["obj_inst"]->meta("status_disp_devo");
		$mg = $arr["obj_inst"]->meta("st_mail_groups_devo");
		if($rmb = $arr["request"]["rm_mail_group_devo"])
		{
			$dat = explode(",", $rmb);
			unset($mg[$dat[0]][$dat[1]]);
			$arr["obj_inst"]->set_meta("st_mail_groups_devo", $mg);
			$arr["obj_inst"]->save();
			$mg = $arr["obj_inst"]->meta("st_mail_groups_devo");
		}
		foreach($doi->get_status_list() as $stid => $status)
		{
			$url = $this->mk_my_orb("do_search", array(
				"pn" => "add_mail_group_devo[".$stid."]",
				"clid" => array(CL_GROUP),
				"multiple" => 0,
			),"popup_search");
			$url = "javascript:aw_popup_scroll(\"".$url."\",\"".t("Otsi")."\",550,500)";
			$mailgroups = html::href(array(
				"caption" => html::img(array(
					"url" => "images/icons/search.gif",
					"border" => 0,
				)),
				"url" => $url
			));
			$pm = get_instance("vcl/popup_menu");
			$pm->begin_menu("add_mail_group_devo_menu_".$stid);
			$count = 0;
			foreach($mg[$stid] as $gid)
			{
				$pm->add_item(array(
					"text" => obj($gid)->name(),
					"link" => aw_url_change_var("rm_mail_group_devo", $stid.",".$gid),
				));
				$count++;
			}
			if($count)
			{
				$mailgroups .= $pm->get_menu(array(
					"icon" => "delete.gif",
				));
			}
			$t->define_data(array(
				"status" => $status,
				"disp_devo" => html::checkbox(array(
					"ch_value" => 1,
					"name" => "sdd[".$stid."]",
					"checked" => ($sdd[$stid] == "no")?0:1,
				)),
				"mail_groups" => $mailgroups,
			));
		}
	}

	function _get_statuses_bug_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "status",
			"align" => "center",
			"caption" => t("Staatus"),
		));
		$t->define_field(array(
			"name" => "disp_bug",
			"align" => "center",
			"caption" => t("Bugil kuvamine"),
		));
		$t->define_field(array(
			"name" => "disp_cust",
			"align" => "center",
			"caption" => t("Bugi kliendil kuvamine"),
		));
		$t->define_field(array(
			"name" => "cust_bugst",
			"align" => "center",
			"caption" => t("Kliendistaatus => P&otilde;histaatus"),
			"tooltip" => t("Milleks muutub p&otilde;histaatus, kui kliendistaatust muuta"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "mail_groups_cust",
			"align" => "center",
			"caption" => t("Meilide saatmine"),
			"tooltip" => t("Millistele kasutajagruppidele saata meil, kui muudetakse kliendistaatust"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "bugst_cust",
			"align" => "center",
			"caption" => t("P&otilde;histaatus => Kliendistaatus"),
			"tooltip" => t("Milleks muutub kliendistaatus, kui p&otilde;histaatust muuta"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "mail_groups_bug",
			"align" => "center",
			"caption" => t("Meilide saatmine"),
			"tooltip" => t("Millistele kasutajagruppidele saata meil, kui muudetakse p&otilde;histaatust"),
			"sortable" => 1,
		));
		$t->set_caption(t("Bugi staatused"));
		$bi = get_instance(CL_BUG);
		$sdb = $arr["obj_inst"]->meta("status_disp_bug");
		$sdc = $arr["obj_inst"]->meta("status_disp_cust");
		$bcs = $arr["obj_inst"]->meta("bug_cust_status_conns");
		$cbs = $arr["obj_inst"]->meta("cust_bug_status_conns");
		$mg_bug = $arr["obj_inst"]->meta("st_mail_groups_bug");
		$mg_cust = $arr["obj_inst"]->meta("st_mail_groups_cust");
		$vars = array("bug", "cust");
		if($rmb = $arr["request"]["rm_mail_group_bug"])
		{
			$dat = explode(",", $rmb);
			unset($mg_bug[$dat[0]][$dat[1]]);
			$arr["obj_inst"]->set_meta("st_mail_groups_bug", $mg_bug);
			$arr["obj_inst"]->save();
			$mg_bug = $arr["obj_inst"]->meta("st_mail_groups_bug");
		}
		if($rmb = $arr["request"]["rm_mail_group_cust"])
		{
			$dat = explode(",", $rmb);
			unset($mg_cust[$dat[0]][$dat[1]]);
			$arr["obj_inst"]->set_meta("st_mail_groups_cust", $mg_cust);
			$arr["obj_inst"]->save();
			$mg_cust = $arr["obj_inst"]->meta("st_mail_groups_cust");
		}
		foreach($bi->bug_statuses as $stid => $status)
		{
			$vars = array("bug", "cust");
			foreach($vars as $var)
			{
				$url = $this->mk_my_orb("do_search", array(
					"pn" => "add_mail_group_".$var."[".$stid."]",
					"clid" => array(CL_GROUP),
					"multiple" => 0,
				),"popup_search");
				$url = "javascript:aw_popup_scroll(\"".$url."\",\"".t("Otsi")."\",550,500)";
				${"mailgroups_".$var} = html::href(array(
					"caption" => html::img(array(
						"url" => "images/icons/search.gif",
						"border" => 0,
					)),
					"url" => $url
				));
				$pm = get_instance("vcl/popup_menu");
				$pm->begin_menu("add_mail_group_".$var."_menu_".$stid);
				$count = 0;
				foreach(${"mg_".$var}[$stid] as $gid)
				{
					$pm->add_item(array(
						"text" => obj($gid)->name(),
						"link" => aw_url_change_var("rm_mail_group_".$var, $stid.",".$gid),
					));
					$count++;
				}
				if($count)
				{
					${"mailgroups_".$var} .= $pm->get_menu(array(
						"icon" => "delete.gif",
					));
				}
			}
			$t->define_data(array(
				"status" => $status,
				"disp_bug" => html::checkbox(array(
					"ch_value" => 1,
					"name" => "sdb[".$stid."]",
					"checked" => ($sdb[$stid] == "no")?0:1,
				)),
				"disp_cust" => html::checkbox(array(
					"ch_value" => 1,
					"name" => "sdc[".$stid."]",
					"checked" => ($sdc[$stid] == "no")?0:1,
				)),
				"cust_bugst" => html::select(array(
					"options" => array(0 => t("--Ei muutu--")) + $bi->bug_statuses,
					"value" => $bcs[$stid],
					"name" => "bcs[".$stid."]",
				)),
				"bugst_cust" => html::select(array(
					"options" => array(0 => t("--Ei muutu--")) + $bi->bug_statuses,
					"value" => $cbs[$stid],
					"name" => "cbs[".$stid."]",
				)),
				"mail_groups_bug" => $mailgroups_bug,
				"mail_groups_cust" => $mailgroups_cust,
			));
		}
	}

	function _set_statuses_bug_tbl($arr)
	{
		$bi = get_instance(CL_BUG);
		foreach($bi->bug_statuses as $stid => $status)
		{
			if(!$arr["request"]["sdb"][$stid])
			{
				$sdb[$stid] = "no";
			}
			if(!$arr["request"]["sdc"][$stid])
			{
				$sdc[$stid] = "no";
			}
		}
		$doi = get_instance(CL_DEVELOPMENT_ORDER);
		foreach($doi->get_status_list() as $stid => $status)
		{
			if(!$arr["request"]["sdd"][$stid])
			{
				$sdd[$stid] = "no";
			}
		}
		$mgb = $arr["obj_inst"]->meta("st_mail_groups_bug");
		foreach($arr["request"]["add_mail_group_bug"] as $stid => $gid)
		{
			if($gid)
			{
				$mgb[$stid][$gid] = $gid;
			}
		}
		$mgc = $arr["obj_inst"]->meta("st_mail_groups_cust");
		foreach($arr["request"]["add_mail_group_cust"] as $stid => $gid)
		{
			if($gid)
			{
				$mgc[$stid][$gid] = $gid;
			}
		}
		$mgd = $arr["obj_inst"]->meta("st_mail_groups_devo");
		foreach($arr["request"]["add_mail_group_devo"] as $stid => $gid)
		{
			if($gid)
			{
				$mgd[$stid][$gid] = $gid;
			}
		}
		$arr["obj_inst"]->set_meta("st_mail_groups_bug", $mgb);
		$arr["obj_inst"]->set_meta("st_mail_groups_cust", $mgc);
		$arr["obj_inst"]->set_meta("st_mail_groups_devo", $mgd);
		$arr["obj_inst"]->set_meta("status_disp_bug", $sdb);
		$arr["obj_inst"]->set_meta("status_disp_cust", $sdc);
		$arr["obj_inst"]->set_meta("status_disp_devo", $sdd);
		$arr["obj_inst"]->set_meta("bug_cust_status_conns", $arr["request"]["bcs"]);
		$arr["obj_inst"]->set_meta("cust_bug_status_conns", $arr["request"]["cbs"]);
		$arr["obj_inst"]->save();
	}

	/**
		@attrib name=f
	**/
	function disp_wh($arr)
	{
		classload("core/date/date_calc");
		$tmp = mktime(0,0,0,1,1,2007);
		$coms = new object_list(array(
			"class_id" => array(CL_TASK_ROW,CL_BUG_COMMENT),
			"lang_id" => array(),
			"site_id" => array(),
			//"created" => new obj_predicate_compare(OBJ_COMP_GREATER, get_week_start()/*-7*3600*24*/),
			"created" => new obj_predicate_compare(OBJ_COMP_GREATER, $tmp/*-7*3600*24*/),
			"sort_by" => "objects.createdby, objects.created"
		));
//		echo "com count = ".$coms->count()." <br>";
echo "<div style='font-size: 10px;'>";
		//$i = array("marko" => "", "dragut" => "", "tarvo" => "", "sander" => "");
		$i = array("helle" => "", "sander" => "", "tarvo" => "");

		foreach($coms->arr() as $com)
		{
			if ($com->createdby() == "")
			{
				// parse from cvs
				$tx = $com->comment();
				if (preg_match("/cvs commit by ([^ ]+) in/imsU", $tx, $mt))
				{
					$uid = $mt[1];
					if ($uid == "kristo")
					{
						$uid = "kix";
					}
					if ($uid == "markop")
					{
						$uid = "marko";
					}
					$com_by_p[$uid][] = $com;
				}
				else
				{
					echo "error, comment $tx no uid <br>";
				}
			}
			else
			{
				$com_by_p[$com->createdby()][] = $com;
				$uid = $com->createdby();
			}
			if (!isset($i[$uid]))
			{
				continue;
			}

			//echo date("d.m.Y H:i", $com->created())." ".$uid."<br>".substr(nl2br($com->comment()), 0, 200)."<hr>";
			$bs[$uid][] = array(
				"t" => $com->created(),
				"c" => $com->comment(),
				"p" => $com->parent()
			);
			$wh[$uid] += $com->prop("add_wh");
		}
		asort($bs);
		foreach($bs as $uid => $bgs)
		{
			echo "$uid has ".count($bgs)." comments wh = ".$wh[$uid]."<br><br>";
			foreach($bgs as $bg)
			{
				$o = obj($bg["p"]);
				echo "bug ".$o->name()."(tunde hetkel:".$o->prop("num_hrs_real").") - ".nl2br($bg["c"])." - time - ".date("d.m.Y", $bg["t"])."<hr>";
			}
		}

		die();

		// calc work hrs per p
		foreach($com_by_p as $uid => $coms)
		{
			$tm = 0;
			foreach($coms as $com)
			{
				$tm += $com->prop("add_wh");
			}
			echo "tot wh for $uid => $tm <br>";
		}
		die();
	}

	function _add_custs_to_tree($t)
	{
		$co = get_current_company();
		$this->_req_cust_tree($t, $co, 0);
	}

	function _req_cust_tree($t, $co, $pt)
	{
		foreach($co->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
		{
			$t->add_item($pt, array(
				"id" => $c->prop("to"),
				"name" => $this->name_cut($c->prop("to.name")),
				"url" => aw_url_change_var("cust", null)
			));
			$this->_req_cust_tree($t, $c->to(), $c->prop("to"));
		}

		foreach($co->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
		{
			$t->add_item($pt, array(
				"id" => $c->prop("to"),
				"name" => $this->name_cut($c->prop("to.name")),
				"url" => aw_url_change_var("cust", $c->prop("to")),
				"iconurl" => icons::get_icon_url(CL_CRM_COMPANY)
			));
		}
	}

	/**
		@attrib name=export_req
	**/
	function export_req($arr)
	{
		$o = obj($arr["id"]);
		$pt = $arr["tf"] ? $arr["tf"] : $o->id();
		$ol = new object_list(array(
			"class_id" => CL_PROCUREMENT_REQUIREMENT,
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $pt
		));
		$t = new vcl_table();
		$t->table_from_ol($ol, array("name", "created", "pri", "req_co", "req_p", "project", "process", "planned_time", "desc", "state", "budget"), CL_PROCUREMENT_REQUIREMENT);
		header('Content-type: application/octet-stream');
		header('Content-disposition: root_access; filename="req.csv"');
		print $t->get_csv_file();
		die();
	}

	/**
		@attrib name=mail_scanner nologin="1"
	**/
	function mail_scanner($arr)
	{
die("a");
		$u = get_instance("users");
		$u->login(array("uid" => "kix", "password" => "jobu13"));
		aw_switch_user(array("uid" => "kix"));
		$ol = new object_list(array(
			"class_id" => CL_BUG_TRACKER,
			"lang_id" => array(),
			"site_id" => array(),
		));

		foreach($ol->arr() as $bt)
		{
			if (!$this->can("view", $bt->prop("mail_identity")))
			{
				continue;
			}

			echo "bt = ".$bt->name()." (".$bt->id().") <br>";
			$imap = obj($bt->prop("mail_identity"));
			$imap_i = $imap->instance();
			$imap_i->connect_server(array(
				"obj_inst" => $imap
			));

			echo $imap_i->test_connection(array(
				"obj_inst" => $imap
			))."<br>";

			echo "tested conns <br>\n";
			flush();
			// now we need to figure out which emails to scan. preferably new ones only.
			$fld_c = $imap_i->get_folder_contents(array("from" => 0, "to" => 100000));
			echo "got cont <br>\n";
			flush();

			// process messages and create bugs from them
			foreach($fld_c as $msg_id => $msg)
			{
				echo "process $msg[subject] <br>\n";
				flush();
				$imap_i->msg_content = "";
				$ms = $imap_i->fetch_message(array("msgid" => $msg_id));

				$b = obj();
				$b->set_class_id(CL_BUG);
				// parent - get default folder from settings OR
				// see if the mail has it set
				$b->set_parent($this->_parse_parent_from_mail_or_default($bt, $ms["content"]));

				$b->set_name($msg["subject"]);
				$b->set_prop("bug_status",1);
				$b->set_prop("bug_priority", $this->_parse_priority_from_mail($ms["content"]));
				$who = $this->_parse_who_from_mail($msg);
				$b->set_prop("who", $who);
				$u = get_instance(CL_USER);
				$b->set_prop("monitors", $this->_parse_monitors_from_message($ms["content"], $u->get_company_for_person($who)));

				$b->set_prop("bug_content", $ms["content"]);
				$b->set_prop("customer", $this->_parse_customer_from_message($ms["content"]));
				$b->set_prop("customer_person", $this->_parse_customer_person_from_message($ms["content"], $b->prop("customer")));
				$b->set_prop("project", $this->_parse_project_from_message($ms["content"]));
				$b->set_prop("bug_mail", $msg["froma"]);
				$b->set_meta("imap_id", $msg_id);
				aw_disable_acl();
				$b->save();
				aw_restore_acl();
			}
			$imap_i->delete_msgs_from_folder(array_keys($fld_c));
		}
		die("all done");
	}

	function _parse_parent_from_mail_or_default($bt, $c)
	{
		if (preg_match("/Kaust: (.*)^/imsU", $c, $mt))
		{
			$path = explode("/", trim($mt[1]));
			$fld = obj($this->can("view", $bt->prop("bug_folder")) ? $bt->prop("bug_folder") : $bt->id());
			foreach($path as $path_item)
			{
				$ol = new object_list(array(
					"parent" => $fld->id(),
					"lang_id" => array(),
					"site_id" => array(),
					"name" => trim($path_item)
				));
				if (!$ol->count())
				{
					return $bt->prop("mail_default_folder");
				}
				$fld = $ol->begin();
			}
			return $fld->id();
		}
		else
		{
			return $bt->prop("mail_default_folder");
		}
	}

	function _parse_priority_from_mail($c)
	{
		if (preg_match("/Pri: (.*)^/imsU", $c, $mt))
		{
			return (int)trim($mt[1]);
		}
		return 3;
	}

	function _parse_who_from_mail($msg)
	{
		// find the person to whom this mail address belongs to
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			"site_id" => array(),
			"CL_CRM_PERSON.RELTYPE_EMAIL.mail" => trim($msg["froma"])
		));
		if ($ol->count())
		{
			$p = $ol->begin();
			return $p->id();
		}
		return null;
	}

	function _parse_monitors_from_message($c, $co)
	{
		// all members of our company whose emails are in the email
		$emls = $this->make_keys($this->_parse_emails_from_message($c));

		if (!count($emls))
		{
			return null;
		}
		if (!is_object($co))
		{
			return null;
		}

		// get all ppl for the company
		$co_inst = get_instance(CL_CRM_COMPANY);
		$ppl = $co_inst->get_employee_picker($co);

		// and find their emails
		$rv = array();
		foreach($ppl as $p_id => $p_name)
		{
			$po = obj($p_id);
			foreach($po->connections_from(array("type" => "RELTYPE_EMAIL")) as $c)
			{
				$m = $c->to();
				if (isset($emls[$m->prop("mail")]))
				{
					$rv[] = $p_id;
				}
			}
		}

		return $rv;
	}

	function _parse_customer_from_message($c)
	{
		$emls = $this->_parse_emails_from_message($c);
		if (!count($emls))
		{
			return null;
		}
		// now I suppose we should find all customers email addresses and see if any of them overlap
		$mail2cust = $this->_get_customer_email_list($emls);
		$possible_custs = array();
		foreach($emls as $email)
		{
			if (isset($mail2cust[trim($email)]))
			{
				$possible_custs[] = $mail2cust[trim($email)];
			}
		}

		$cur_co = get_current_company();
		foreach($possible_custs as $id)
		{
			$o = obj($id);
			if ($o->class_id() == CL_CRM_COMPANY && $cur_co->id() != $id)
			{
				return $o->id();
			}
		}
		return null;
	}

	function _parse_customer_person_from_message($c, $customer)
	{
		if (!$customer)
		{
			return null;
		}
		$m =  $this->_parse_monitors_from_message($c, obj($customer));
		if (is_array($m) && count($m))
		{
			return reset($m);
		}
		return null;
	}

	function _parse_project_from_message($c)
	{
		return null;
	}

	function _parse_emails_from_message($c)
	{
		preg_match_all('/([a-z0-9-]*((\.|_)?[a-z0-9]+)+@([a-z0-9]+(\.|-)?)+[a-z0-9]\.[a-z]{2,})/imsU',$c, $mt, PREG_PATTERN_ORDER);
		return $mt[0];
	}

	function _get_customer_email_list($emails)
	{
		$ret = array();

		$ml_ol = new object_list(array(
			"class_id" => CL_ML_MEMBER,
			"mail" => $emails,
			"site_id" => array(),
			"lang_id" => array()
		));
		if (!$ml_ol->count())
		{
			return $ret;
		}
		$ml_ids = $ml_ol->ids();

		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_CRM_COMPANY,
			"type" => "RELTYPE_EMAIL",
			"to" => $ml_ids
		));
		$ids = array();
		foreach($conns as $con)
		{
			$ids[] = $con["to"];
		}
		if (count($ids))
		{
			$ol = new object_list(array("oid" => $ids, "lang_id" => array(), "site_id" => array()));
			$ol->arr();
		}

		foreach($conns as $con)
		{
			$eml = obj($con["to"]);

			if ($eml->prop("mail") != "")
			{
				$ret[$eml->prop("mail")] = $con["from"];
			}
		}

		$conns = $c->find(array(
			"from.class_id" => CL_CRM_PERSON,
			"type" => "RELTYPE_EMAIL",
			"to" => $ml_ids
		));
		$ids = array();
		foreach($conns as $con)
		{
			$ids[] = $con["to"];
		}
		if (count($ids))
		{
			$ol = new object_list(array("oid" => $ids, "lang_id" => array(), "site_id" => array()));
			$ol->arr();
		}
		$u = get_instance(CL_USER);
		foreach($conns as $con)
		{
			$eml = obj($con["to"]);

			if ($eml->prop("mail") != "")
			{
				// get company for person
				// and return that instead
				$ret[$eml->prop("mail")] = $u->get_company_for_person($con["from"]);
			}
		}

		return $ret;
	}

	/**
	@attrib name=create_feedback_bug all_args=1 no_login=1
	**/
	function create_feedback_bug($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_BUG_TRACKER,
		));
		foreach($ol->arr() as $o)
		{
			if(is_oid($o->prop("fb_folder")))
			{
				$fbf = $o->prop("fb_folder");
			}
		}
		if($fbf)
		{
			$o = obj();
			$o->set_class_id(CL_BUG);
			$o->set_parent($fbf);
			$o->set_name("Tagasiside saidilt ".$arr["site"]);
			$ol = new object_list(array(
				"name" => $arr["company"],
				"class_id" => CL_CRM_COMPANY,
			));
			if($ol->count())
			{
				$co = $ol->begin();
				$o->set_prop("customer", $co->id());
			}
			$cldata = aw_ini_get("classes");
			$content = "Sait: ".$arr["site"];
			$content .= "\nIsik: ".$arr["person"];
			$content .= "\nFirma: ".$arr["company"];
			$content .= "\nKlass: ".$cldata[$arr["fb_class"]]["name"];
			$content .= "\nKaart: ".$arr["group"];
			$content .= "\nObjekt: ".$arr["name"]." (".$arr["oid"].")";
			$link = str_replace(aw_ini_get("baseurl"),$arr["site"]."/automatweb",$this->mk_my_orb("change", array("id" => $arr["fb_oid"]), CL_CUSTOMER_FEEDBACK_ENTRY));
			$content .= "\n\nTagasiside objekt: ".$link;
			$content .= "\n\nKommentaar:\n".$arr["comment"];
			$o->set_prop("bug_content", $content);
			$o->set_prop("bug_priority", 6 - $arr["seriousness"]);
			$o->set_prop("bug_status", 1);
			$obj_link = str_replace(aw_ini_get("baseurl"),$arr["site"]."/automatweb",$this->mk_my_orb("change", array("id" => $arr["oid"], "group" => $arr["group"]), $arr["fb_class"]));
			$o->set_prop("bug_url", $obj_link);
			$o->set_prop("bug_class", $arr["fb_class"]);
			$o->save();
			$url = $this->mk_my_orb("change", array("id" => $o->id()), CL_BUG);
			$url = str_replace(aw_ini_get("baseurl"), aw_ini_get("baseurl")."/automatweb", $url);
			return $url;
		}
		else
		{
			return false;
		}
	}

	function get_user2person_arr_from_list($user_list)
	{
		$u2p = array();
		if (count($user_list))
		{
			$ol = new object_list(array(
				"class_id" => CL_USER,
				"lang_id" => array(),
				"site_id" => array(),
				"uid" => $user_list
			));
			$oid_list = array();
			foreach($ol->arr() as $uo)
			{
				$oid_list[$uo->id()] = $uo->prop("uid");
			}

			$c = new connection();
			$u2p_conns = $c->find(array(
				"from.class_id" => CL_USER,
				"from" => array_keys($oid_list),
				"type" => "RELTYPE_PERSON"
			));
			$person_oids = array();
			foreach($u2p_conns as $con)
			{
				$person_oids[] = $con["to"];
				$u2p[$oid_list[$con["from"]]] = $con["to"];
			}

			$person_ol = new object_list(array("class_id" => CL_CRM_PERSON, "oid" => $person_oids, "lang_id" => array(), "site_id" => array()));
			$person_ol->arr();
		}
		return $u2p;
	}

	public function _get_s_finance_type($arr)
	{
		$arr["prop"]["options"] = array(
			"" => t("--vali--"),
			-1 => t("Valimata"),
			1 => t("T&ouml;&ouml; l&otilde;ppedes"),
			2 => t("Projekti l&otilde;ppedes"),
			3 => t("Arendus")
		);
		$arr["prop"]["value"] = isset($arr["request"]["s_finance_type"]) ? $arr["request"]["s_finance_type"] : "";
	}

	public static function _get_owner($arr)
	{
		$obj_inst = !empty($arr["id"]) ? obj($arr["id"]) : (!empty($arr["inst_id"]) ? obj($arr["inst_id"]) : obj($arr["request"]["id"]));
		$conn = $obj_inst->connections_from(array(
			"type" => "RELTYPE_OWNER",
		));
		if(count($conn))
		{
			$c = reset($conn);
			$owner = $c->to();
		}
		return $owner;
	}
}
