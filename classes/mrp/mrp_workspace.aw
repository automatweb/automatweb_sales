<?php
/*

@classinfo syslog_type=ST_MRP_WORKSPACE relationmgr=yes no_status=1 prop_cb=1 maintainer=voldemar

@groupinfo general caption="Seaded"
	@groupinfo grp_settings_def caption="Seaded" parent=general confirm_save_data=1
	@groupinfo grp_settings_salesman caption="M&uuml;&uuml;gimehe seaded" parent=general
	@groupinfo grp_users_tree caption="Kasutajate puu" parent=general submit=no
	@groupinfo grp_users_mgr caption="Kasutajate rollid" parent=general submit=no
	@groupinfo grp_worksheet caption="T&ouml;&ouml;lehed" parent=general submit_method=get
	@groupinfo grp_res_settings caption="Ressursside parameetrid" parent=general  submit=no
	@groupinfo grp_res_formats caption="Ressursside formaadid" parent=general  submit=no

@groupinfo grp_customers caption="Kliendid" submit=no submit_method=get
@groupinfo grp_projects caption="Projektid" confirm_save_data=1
@groupinfo grp_schedule caption="T&ouml;&ouml;voog" submit=no
	@groupinfo grp_schedule_gantt caption="T&ouml;&ouml;voo diagramm" submit=no parent=grp_schedule
	@groupinfo grp_schedule_google caption="Graafikud" submit=no parent=grp_schedule
@groupinfo grp_resources caption="Ressursid"
	@groupinfo grp_resources_load caption="Koormus" parent=grp_resources
	@groupinfo grp_resources_manage caption="Haldus" parent=grp_resources
	@groupinfo grp_resources_hours_report caption="T&ouml;&ouml;ajaaruanne" parent=grp_resources submit=no
@groupinfo grp_persons caption="Aruanded"
	@groupinfo grp_persons_hours_report caption="T&ouml;&ouml;ajaaruanne" parent=grp_persons
	@groupinfo grp_persons_jobs_report caption="Tehtud t&ouml;&ouml;d inimeste kaupa" parent=grp_persons
	@groupinfo grp_persons_quantity_report caption="T&uuml;kiarvestuse aruanne" parent=grp_persons
	@groupinfo grp_material_report caption="Materjali aruanne" parent=grp_persons submit=no


@groupinfo grp_printer_general caption="Operaatori vaade" submit=no
@groupinfo grp_printer caption="T&ouml;&ouml;d" submit=no parent=grp_printer_general

@groupinfo my_stats caption="Minu aruanded" submit=no parent=grp_printer_general
@default group=my_stats
	layout my_stats_full type=hbox width=20%:80% group=my_stats
		layout my_stats_left type=vbox parent=my_stats_full
		layout my_stats_tree type=vbox parent=my_stats_left area_caption=Vali&nbsp;ajavahemik
			property my_stats_time_tree type=treeview store=no no_caption=1 parent=my_stats_tree
		layout my_stats_right type=vbox parent=grp_persons_full
			layout my_stats_right_top type=hbox parent=my_stats_right width=50%:50%
				layout my_stats_hours_by_resource_chart type=vbox closeable=1 parent=my_stats_right_top area_caption=A
					property my_stats_hours_by_resource_chart type=google_chart store=no parent=my_stats_hours_by_resource_chart no_caption=1
				layout my_stats_pauses_by_resource_chart type=vbox closeable=1 parent=my_stats_right_top area_caption=A
					property my_stats_pauses_by_resource_chart type=google_chart store=no parent=my_stats_pauses_by_resource_chart no_caption=1
			layout my_stats_hours_tbl type=vbox closeable=1 parent=my_stats_right area_caption=T&ouml;&ouml;tundide&nbsp;tabel
				property my_stats_tbl type=table no_caption=1 parent=my_stats_hours_tbl store=no


@groupinfo my_resources caption="Minu ressursid" parent=grp_printer_general
#@default group=my_resources
#	@layout my_resources_full type=hbox width=20%:80%
#	@layout my_resources_tree_box type=vbox closeable=1 area_caption=Ressursid&nbsp;&amp;&nbsp;kategooriad parent=my_resources_full
#		@property my_resources_tree type=text store=no no_caption=1 parent=my_resources_tree_box
#	@layout my_right_pane type=vbox parent=my_resources_full
#		@layout my_resource_deviation_chart type=vbox closeable=1 area_caption=Ressursi&nbsp;h&auml;lbe&nbsp;muutus&nbsp;ajas parent=my_right_pane
#			@property my_resource_deviation_chart type=google_chart no_caption=1 parent=my_resource_deviation_chart store=no
#		@layout my_resource_time_chart type=vbox closeable=1 area_caption=Ressursside&nbsp;ajakasutus parent=my_right_pane
#			@layout my_resource_time_limits type=hbox parent=my_resource_time_chart
#				@property my_resource_time_start type=date_select parent=my_resource_time_limits
#				@caption Alates
#				@property my_resource_time_end type=date_select parent=my_resource_time_limits
#				@caption Kuni
#			@property my_resource_time_chart type=google_chart no_caption=1 parent=my_resource_time_chart store=no
#		@property my_resources_list type=table store=no parent=my_right_pane no_caption=1

#	@groupinfo grp_printer_current caption="Jooksvad t&ouml;&ouml;d" parent=grp_printer submit=no
#	groupinfo grp_printer_old caption="Tegemata t&ouml;&ouml;d" parent=grp_printer submit=no
#	@groupinfo grp_printer_done caption="Tehtud t&ouml;&ouml;d" parent=grp_printer submit=no
#	@groupinfo grp_printer_aborted caption="Katkestatud t&ouml;&ouml;d" parent=grp_printer submit=no

#	@groupinfo grp_printer_in_progress caption="K&otilde;ik t&ouml;&ouml;s olevad" parent=grp_printer submit=no
#	@groupinfo grp_printer_startable caption="K&otilde;ik t&ouml;&ouml;d mida oleks v&otilde;imalik alustada" parent=grp_printer submit=no
#	@groupinfo grp_printer_notstartable caption="T&ouml;&ouml;d, mida ei ole veel v&otilde;imalik alustada" parent=grp_printer submit=no

@groupinfo grp_login_select_res caption="Vali kasutatav ressurss"


@default table=objects
@default field=meta
@default method=serialize


	@property rescheduling_needed type=hidden

	// elements main grouper
	@layout vsplitbox type=hbox group=grp_customers,grp_projects,grp_resources_manage,grp_resources_load,grp_users_tree,grp_users_mgr,grp_settings_def,my_resources width=25%:75%

@default group=grp_customers
	@property customers_toolbar type=toolbar store=no no_caption=1
	@layout customers_box type=vbox parent=vsplitbox
	@layout customers_tree_box type=vbox closeable=1 area_caption=Kliendid parent=customers_box
	@property customers_tree type=treeview store=no no_caption=1 parent=customers_tree_box
	@property customers_list type=table store=no no_caption=1 parent=customers_search_table
	@property customers_list_proj type=table store=no no_caption=1 parent=vsplitbox

	@layout customers_time_box type=vbox closeable=1 area_caption=Projekti&nbsp;t&auml;htaja&nbsp;j&auml;rgi parent=customers_box
		@property customers_time_tree type=treeview store=no no_caption=1 parent=customers_time_box

	@layout customers_search_box type=vbox closeable=1 area_caption=Klientide&nbsp;otsing parent=customers_box
		@property cs_name type=textbox view_element=1 parent=customers_search_box store=no size=33
		@caption Nimi

		@property cs_firmajuht type=textbox view_element=1 parent=customers_search_box store=no size=33
		@caption Kontaktisik

		@property cs_contact type=textbox view_element=1 parent=customers_search_box store=no size=33
		@caption Aadress

		@property cs_phone type=textbox view_element=1 parent=customers_search_box store=no size=33
		@caption Telefon

		@property cs_reg_nr type=textbox view_element=1 parent=customers_search_box store=no size=33
		@caption Kood

		@property cs_status type=textbox view_element=1 parent=customers_search_box store=no size=33
		@caption Staatus

		@property cs_submit type=submit value=Otsi view_element=1 parent=customers_search_box store=no
		@caption Otsi

	@layout customers_search_table type=vbox closeable=1 area_caption=Projektide&nbsp;nimekiri no_padding=1 parent=vsplitbox
		@property cs_result type=table no_caption=1 parent=customers_search_table


@default group=grp_projects
	@property projects_toolbar type=toolbar store=no no_caption=1
	@layout projects_box type=vbox parent=vsplitbox
	@layout projtree_box type=vbox closeable=1 area_caption=Projektid&nbsp;staatuste&nbsp;kaupa parent=projects_box
	@property projects_tree type=text store=no no_caption=1 parent=projtree_box

	@layout projtreetime_box type=vbox closeable=1 area_caption=Lisamise&nbsp;kuup&auml;eva&nbsp;j&auml;rgi parent=projects_box
	@property projects_time_tree type=treeview store=no no_caption=1 parent=projtreetime_box

	@layout project_customers_tree_box type=vbox closeable=1 area_caption=Kliendi parent=projects_box
	@property project_customers_tree type=treeview store=no no_caption=1 parent=project_customers_tree_box


	@property projects_list type=table store=no no_caption=1 parent=vsplitbox

	@property sp_result type=table no_caption=1 parent=vsplitbox

	@layout projects_search_box type=vbox closeable=1 area_caption=Projektide&nbsp;otsing parent=projects_box
		@property sp_name type=textbox view_element=1 parent=projects_search_box store=no captionside=top size=33
		@caption Number

		@property sp_comment type=textbox view_element=1 parent=projects_search_box store=no captionside=top size=33
		@caption Nimetus

		@property sp_customer type=textbox view_element=1 parent=projects_search_box store=no captionside=top size=33
		@caption Klient

		@property sp_starttime type=date_select view_element=1 parent=projects_search_box store=no captionside=top default=-1
		@caption Alustamisaeg (materjalide saabumine) alates

		@property sp_due_date type=date_select view_element=1 parent=projects_search_box store=no captionside=top default=-1
		@caption T&auml;htaeg alates

		@property sp_start_date_start type=date_select view_element=1 parent=projects_search_box store=no captionside=top default=-1
		@caption Lisatud alates

		@property sp_start_date_end type=date_select view_element=1 parent=projects_search_box store=no captionside=top default=-1
		@caption Lisatud kuni

		@property sp_status type=select multiple=1 size=5 view_element=1 parent=projects_search_box captionside=top store=no
		@caption Staatus

		@property sp_submit type=button view_element=1 parent=projects_search_box store=no
		@caption Otsi


@default group=grp_resources_manage,grp_resources_load,my_resources
	@property resources_toolbar type=toolbar store=no no_caption=1 group=grp_resources_manage
	@layout resources_tree_box type=vbox closeable=1 area_caption=Ressursid&nbsp;&amp;&nbsp;kategooriad parent=vsplitbox


@layout ppl_resources type=vbox_sub parent=resources_tree_box area_caption=Minu&nbsp;ressursid closeable=1 no_padding=1 group=my_resources parent=resources_tree_box
		@property resources_tree type=text store=no no_caption=1 parent=resources_tree_box
#		@property pp_resources type=table parent=resources_tree_box no_caption=1 store=no group=my_resources


	@layout right_pane type=vbox parent=vsplitbox
		@layout resource_deviation_chart type=vbox closeable=1 area_caption=Ressursi&nbsp;h&auml;lbe&nbsp;muutus&nbsp;ajas parent=right_pane group=grp_resources_load,my_resources
			@property resource_deviation_chart type=google_chart no_caption=1 parent=resource_deviation_chart store=no group=grp_resources_load,my_resources
		@layout resource_time_chart type=vbox closeable=1 area_caption=Ressursside&nbsp;ajakasutus parent=right_pane group=grp_resources_load,my_resources
			@layout resource_time_limits type=hbox parent=resource_time_chart group=grp_resources_load,my_resources
				@property resource_time_start type=date_select parent=resource_time_limits group=grp_resources_load,my_resources
				@caption Alates
				@property resource_time_end type=date_select parent=resource_time_limits group=grp_resources_load,my_resources
				@caption Kuni
			@property resource_time_chart type=google_chart no_caption=1 parent=resource_time_chart store=no group=grp_resources_load,my_resources
		@property resources_list type=table store=no parent=right_pane no_caption=1

@default group=grp_resources_hours_report
	@layout resources_hours_full type=hbox width=20%:80%
		@layout resources_hours_left type=vbox parent=resources_hours_full
			@layout resources_time_tree type=vbox parent=resources_hours_left area_caption=Vali&nbsp;ajavahemik
				@property resources_time_tree type=treeview store=no no_caption=1 parent=resources_time_tree
			@layout resources_resource_span_tree type=vbox parent=resources_hours_left area_caption=Vali&nbsp;ressursid
				@property resources_resource_span_tree type=treeview store=no no_caption=1 parent=resources_resource_span_tree
		@layout resources_hours_right type=vbox parent=resources_hours_full
			@layout resources_hours_chart type=vbox closeable=1 parent=resources_hours_right area_caption=T&ouml;&ouml;tundide&nbsp;graafik&nbsp;ressursside&nbsp;kaupa
				@property resources_hours_chart type=text no_caption=1 parent=resources_hours_chart store=no
			@layout resources_hours_tbl type=vbox closeable=1 parent=resources_hours_right area_caption=T&ouml;&ouml;tundide&nbsp;tabel&nbsp;ressursside&nbsp;kaupa
				@property resources_hours_tbl type=table no_caption=1 parent=resources_hours_tbl store=no

@default group=grp_persons_hours_report,grp_persons_jobs_report,my_stats,grp_persons_quantity_report
	@layout grp_persons_full type=hbox width=20%:80%
		@layout grp_persons_left type=vbox parent=grp_persons_full
			@layout persons_time_tree type=vbox parent=grp_persons_left area_caption=Vali&nbsp;ajavahemik
				@property persons_time_tree type=treeview store=no no_caption=1 parent=persons_time_tree
			@layout persons_personnel_tree type=vbox parent=grp_persons_left area_caption=Vali&nbsp;inimesed&#44;&nbsp;kelle&nbsp;t&ouml;&ouml;tunde&nbsp;soovid&nbsp;n&auml;ha
				@property persons_personnel_tree type=treeview store=no no_caption=1 parent=persons_personnel_tree
			@layout persons_resource_span_tree type=vbox parent=grp_persons_left area_caption=Vali&nbsp;ressursid group=grp_persons_quantity_report
				@property persons_resource_span_tree type=treeview store=no no_caption=1 parent=persons_resource_span_tree group=grp_persons_quantity_report
			@layout persons_other_options type=vbox parent=grp_persons_left area_caption=T&ouml;&ouml;tundide&nbsp;kuvamise&nbsp;tingimused

		@layout grp_persons_right type=vbox parent=grp_persons_full


@default group=grp_material_report
	@layout grp_material_report_full type=hbox width=20%:80%
		@layout grp_material_report_left type=vbox parent=grp_material_report_full
			@layout grp_material_tree type=vbox parent=grp_material_report_left area_caption=Materjal closeable=1
				@property grp_material_tree type=treeview store=no no_caption=1 parent=grp_material_tree

			@layout grp_material_time_tree type=vbox parent=grp_material_report_left area_caption=Ajavahemik closeable=1
				@property grp_material_time_tree type=treeview store=no no_caption=1 parent=grp_material_time_tree
			@layout grp_material_personnel_tree type=vbox parent=grp_material_report_left area_caption=Inimesed closeable=1
				@property grp_material_personnel_tree type=treeview store=no no_caption=1 parent=grp_material_personnel_tree
			@layout grp_material_resource_span_tree type=vbox parent=grp_material_report_left area_caption=Ressursid closeable=1
				@property grp_material_resource_span_tree type=treeview store=no no_caption=1 parent=grp_material_resource_span_tree
			@layout grp_material_other_options type=vbox parent=grp_material_report_left area_caption=Grupeerimine closeable=1
				@property resource_stats_grouping type=select store=no no_caption=1 parent=grp_material_other_options
				@property grp_material_btn type=button store=no parent=grp_material_other_options no_caption=1
				@caption Grupeeri


		@property material_stats_table type=table store=no no_caption=1 parent=grp_material_report_full

@default group=grp_persons_jobs_report
	#layout grp_persons_full type=hbox width=20%:80%
		#layout grp_persons_left type=vbox parent=grp_persons_full
			#layout persons_other_options type=vbox parent=grp_persons_left area_caption=T&ouml;&ouml;tundide&nbsp;kuvamise&nbsp;tingimused
				@property poo_job_done_only_by type=checkbox ch_value=1 parent=persons_other_options no_caption=1
				@caption Kuva t&ouml;id, mida on teinud ainult valitud isik
				@comment Omab m&otilde;ju ainult &uuml;he isiku t&ouml;&ouml;de kuvamisel
		#layout grp_persons_right type=vbox parent=grp_persons_full
			@property persons_jobs_tbl type=table store=no parent=grp_persons_right no_caption=1

@default group=grp_persons_quantity_report
	#layout grp_persons_full type=hbox width=20%:80%
		#layout grp_persons_left type=vbox parent=grp_persons_full
			#layout persons_other_options type=vbox parent=grp_persons_left area_caption=T&ouml;&ouml;tundide&nbsp;kuvamise&nbsp;tingimused
				@property poo_quantity_broup_by type=chooser multiple=1 orient=vertical parent=persons_other_options captionside=top
				@caption Grupeerimine
				@layout poo_quantity_broup_by type=vbox_sub parent=persons_other_options area_caption=Grupeerimine closeable=1
					@property poo_quantity_broup_by_resource type=button parent=poo_quantity_broup_by captionside=top
					@caption Ressursside kaupa
					@property poo_quantity_broup_by_case type=button parent=poo_quantity_broup_by captionside=top
					@caption Projektide kaupa
					@property poo_quantity_broup_by_job type=button parent=poo_quantity_broup_by captionside=top
					@caption T&ouml;&ouml;de kaupa
		#layout grp_persons_right type=vbox parent=grp_persons_full
			@layout grp_persons_right_split_1 type=hbox width=50%:50% parent=grp_persons_right
				@layout grp_persons_right_split_1_left type=vbox parent=grp_persons_right_split_1 closeable=1 area_caption=T&uuml;kiarvestus&nbsp;inimeste&nbsp;l&otilde;ikes
					@property persons_quantity_chart_quantity_by_person type=google_chart store=no no_caption=1 parent=grp_persons_right_split_1_left
				@layout grp_persons_right_split_1_right type=vbox parent=grp_persons_right_split_1 closeable=1 area_caption=T&uuml;kiarvestus&nbsp;ressursside&nbsp;l&otilde;ikes
					@property persons_quantity_chart_quantity_by_resource type=google_chart store=no no_caption=1 parent=grp_persons_right_split_1_right
			@layout grp_persons_right_split_2 type=hbox width=50%:50% parent=grp_persons_right
				@layout grp_persons_right_split_2_left type=vbox parent=grp_persons_right_split_2 closeable=1 area_caption=T&uuml;kiarvestus&nbsp;projektide&nbsp;l&otilde;ikes
					@property persons_quantity_chart_quantity_by_case type=google_chart store=no no_caption=1 parent=grp_persons_right_split_2_left
				@layout grp_persons_right_split_2_right type=vbox parent=grp_persons_right_split_2 closeable=1 area_caption=T&uuml;kiarvestus&nbsp;t&ouml;&ouml;de&nbsp;l&otilde;ikes
					@property persons_quantity_chart_quantity_by_job type=google_chart store=no no_caption=1 parent=grp_persons_right_split_2_right
			@layout grp_persons_right_split_3 type=vbox parent=grp_persons_right closeable=1 area_caption=T&uuml;kiarvestus&nbsp;inimeste&nbsp;kaupa&nbsp;kuude&nbsp;l&otilde;ikes
				@property persons_quantity_chart_quantity_by_person_in_month type=google_chart store=no no_caption=1 parent=grp_persons_right_split_3
			@layout grp_persons_right_split_4 type=vbox parent=grp_persons_right closeable=1 area_caption=T&uuml;kiarvestus&nbsp;inimeste&nbsp;kaupa&nbsp;n&auml;dalate&nbsp;l&otilde;ikes
				@property persons_quantity_chart_quantity_by_person_in_week type=google_chart store=no no_caption=1 parent=grp_persons_right_split_4
			@property persons_quantity_tbl type=table store=no parent=grp_persons_right no_caption=1

@default group=grp_persons_hours_report,my_stats
	#layout grp_persons_full type=hbox width=20%:80%
		#layout grp_persons_left type=vbox parent=grp_persons_full
			#layout persons_other_options type=vbox parent=grp_persons_left area_caption=T&ouml;&ouml;tundide&nbsp;kuvamise&nbsp;tingimused
				@property poo_started_finished_by type=table parent=persons_other_options captionside=top
				@caption Kuva iga isiku kohta tema poolt
		#layout grp_persons_right type=vbox parent=grp_persons_full
			@layout grp_persons_right_top type=hbox parent=grp_persons_right width=50%:50%
				@layout hours_by_resource_chart type=vbox closeable=1 parent=grp_persons_right_top area_caption=A group=grp_persons_hours_report,my_stats
					@property hours_by_resource_chart type=google_chart store=no parent=hours_by_resource_chart no_caption=1 group=grp_persons_hours_report,my_stats
				@layout pauses_by_resource_chart type=vbox closeable=1 parent=grp_persons_right_top area_caption=A group=grp_persons_hours_report,my_stats
					@property pauses_by_resource_chart type=google_chart store=no parent=pauses_by_resource_chart no_caption=1 group=grp_persons_hours_report,my_stats
			@layout persons_hours_chart type=vbox closeable=1 parent=grp_persons_right area_caption=T&ouml;&ouml;tundide&nbsp;graafik&nbsp;inimeste&nbsp;kaupa group=grp_persons_hours_report,my_stats
				@property persons_hours_chart type=text no_caption=1 parent=persons_hours_chart store=no group=grp_persons_hours_report,my_stats
			@layout persons_hours_tbl type=vbox closeable=1 parent=grp_persons_right no_padding=1 area_caption=T&ouml;&ouml;tundide&nbsp;tabel&nbsp;inimeste&nbsp;kaupa group=grp_persons_hours_report,my_stats
				@property persons_hours_tbl type=table no_caption=1 parent=persons_hours_tbl store=no group=grp_persons_hours_report,my_stats
			@layout persons_detailed_hours_tbl type=vbox closeable=1 parent=grp_persons_right no_padding=1 area_caption=T&ouml;&ouml;tundide&nbsp;tabel&nbsp;t&ouml;&ouml;&nbsp;kaupa group=grp_persons_hours_report,my_stats
				@property persons_detailed_hours_tbl type=table no_caption=1 parent=persons_detailed_hours_tbl store=no group=grp_persons_hours_report,my_stats

@default group=grp_schedule_gantt
	@property master_schedule_chart type=text store=no no_caption=1

	@layout schedule_search_box type=vbox closeable=1 area_caption=Otsing
		@property chart_project_hilight_gotostart type=checkbox store=no parent=schedule_search_box
		@caption Mine valitud projekti algusesse

		@property chart_search type=text store=no parent=schedule_search_box
		@caption Otsi

		@property chart_start_date type=date_select store=no parent=schedule_search_box
		@caption N&auml;idatava perioodi algus

		@property chart_submit type=submit store=no parent=schedule_search_box
		@caption N&auml;ita

@default group=grp_schedule_google

	@layout charts_split type=hbox width=25%:75%

		@layout charts_left type=vbox parent=charts_split

			@layout charts_clients_tree type=vbox parent=charts_left area_caption=Vali&nbsp;klient/kliendikategooria

				@property charts_clients_tree type=treeview store=no no_caption=1 parent=charts_clients_tree

			@layout charts_time_tree type=vbox parent=charts_left area_caption=Vali&nbsp;ajavahemik

				@property charts_time_tree type=treeview store=no no_caption=1 parent=charts_time_tree

		@layout charts_right type=vbox parent=charts_split

			@layout charts_1 type=hbox width=50%:50% parent=charts_right

				@layout states_chart type=vbox area_caption=K&auml;imasolevad&nbsp;projektid&nbsp;staatuste&nbsp;kaupa parent=charts_1 closeable=1

					@property states_chart type=google_chart no_caption=1 parent=states_chart store=no

				@layout deadline_chart type=vbox area_caption=K&auml;imasolevad&nbsp;projektid&nbsp;t&auml;htaja&nbsp;j&auml;rgi parent=charts_1 closeable=1

					@property deadline_chart type=google_chart no_caption=1 parent=deadline_chart store=no

			@layout clients_chart type=vbox area_caption=K&auml;imasolevad&nbsp;projektid&nbsp;klientide&nbsp;kaupa&nbsp;(TOP&nbsp;20) closeable=1 parent=charts_right

				@property clients_chart type=google_chart no_caption=1 parent=clients_chart store=no

@default group=grp_users_tree
	@property user_list_toolbar type=toolbar store=no no_caption=1
	@layout userlist_tree_box type=vbox closeable=1 area_caption=Kasutajad parent=vsplitbox
	@property user_list_tree type=treeview store=no no_caption=1 parent=userlist_tree_box
	@property user_list type=table store=no no_caption=1 parent=vsplitbox

@default group=grp_users_mgr
	@property user_mgr_toolbar type=toolbar store=no no_caption=1
	@layout usermgr_tree_box type=vbox closeable=1 area_caption=Osakonnad parent=vsplitbox
	@property user_mgr_tree type=treeview store=no no_caption=1 parent=usermgr_tree_box
	@property user_mgr type=table store=no no_caption=1 parent=vsplitbox


@default group=grp_settings_def
	//  elements 2-column divider
	@layout vsplitbox2 type=hbox width=50%:50%
	@layout left_column type=vbox closeable=0 area_caption=P&otilde;hiseaded parent=vsplitbox2
	@layout right_column type=vbox closeable=0 area_caption=Planeerija&nbsp;parameetrid parent=vsplitbox2

	@property name type=textbox field=name parent=left_column method=none
	@caption Nimi

	@property owner type=relpicker reltype=RELTYPE_MRP_OWNER clid=CL_CRM_COMPANY parent=left_column
	@caption Organisatsioon

	@property resources_folder type=relpicker reltype=RELTYPE_MRP_FOLDER clid=CL_MENU parent=left_column
	@caption Ressursside kaust

	@property customers_folder type=relpicker reltype=RELTYPE_MRP_FOLDER clid=CL_MENU parent=left_column
	@caption Klientide kaust

	@property projects_folder type=relpicker reltype=RELTYPE_MRP_FOLDER clid=CL_MENU parent=left_column
	@caption Projektide kaust

	@property jobs_folder type=relpicker reltype=RELTYPE_MRP_FOLDER clid=CL_MENU parent=left_column
	@caption T&ouml;&ouml;de kaust

	@property workspace_configmanager type=relpicker reltype=RELTYPE_MRP_WORKSPACE_CFGMGR clid=CL_CFGMANAGER parent=left_column
	@caption Keskkonna seadetehaldur

	@property case_header_controller type=relpicker reltype=RELTYPE_MRP_HEADER_CONTROLLER parent=left_column
	@caption Projekti headeri kontroller

	@property purchasing_manager type=relpicker reltype=RELTYPE_PURCHASING_MANAGER parent=left_column multiple=1
	@caption Laohalduse keskkond

	@property hr_time_format type=chooser parent=left_column
	@caption Ajaformaat t&ouml;&ouml;ajaaruannetes

	@property pv_per_page type=textbox default=30 datatype=int parent=left_column
	@caption Operaatori vaates t&ouml;id lehel

	@property projects_list_objects_perpage type=textbox default=30 datatype=int parent=left_column
	@comment Projektide vaates objekte lehel
	@caption Projekte lehel

	@property max_subcontractor_timediff type=textbox default=1 parent=left_column parent=left_column
	@comment Erinevus allhankijaga kokkulepitud aja ning planeeritud algusaja vahel, mis on lubatud hilinemise/ettej&otilde;udmise piires.
	@caption Allhanke suurim ajanihe (h)

	@property automatic_archiving_period type=textbox parent=left_column datatype=int
	@comment Kui on m&auml;&auml;ratud (nullist suurem) ajavahemik, arhiveeritakse automaatselt projektid, mille valmissaamisest on m&ouml;&ouml;dunud see ajavahemik. Positiivne t&auml;isarv.
	@caption Automaatne arhiveerimine

	@property automatic_archiving_period_unit type=text no_caption=1 parent=left_column store=no

	// @property default_global_buffer type=textbox default=4 parent=left_column
	// @comment Uutele loodavatele ressurssidele vaikimisi pandav p&auml;eva &uuml;ldpuhver.
	// @caption Vaikimisi &uuml;ldpuhver (h)


	// scheduler parameters
	@property parameter_due_date_overdue_slope type=textbox default=0.5 parent=right_column
	@caption &Uuml;le t&auml;htaja olevate projektide t&auml;htsuse t&otilde;us t&auml;htaja &uuml;letamise suurenemise suunas

	@property parameter_due_date_overdue_intercept type=textbox default=10 parent=right_column
	@caption Just t&auml;htaja &uuml;letanud projekti t&auml;htsus

	@property parameter_due_date_decay type=textbox default=0.05 parent=right_column
	@caption Projekti t&auml;htsuse langus t&auml;htaja kaugenemise suunas

	@property parameter_due_date_intercept type=textbox default=0.1 parent=right_column
	@caption Planeerimise hetkega v&otilde;rdse t&auml;htajaga projekti t&auml;htsus

	@property parameter_priority_slope type=textbox default=0.8 parent=right_column
	@caption Kliendi ja projektiprioriteedi suhtelise v&auml;&auml;rtuse t&otilde;us vrd. t&auml;htajaga

	@property parameter_schedule_length type=textbox default=2 parent=right_column
	@caption Ajaplaani ulatus (a)

	@property parameter_min_planning_jobstart type=textbox default=300 parent=right_column
	@caption Ajavahemik planeerimise alguse hetkest milles algavaid t&ouml;id ei planeerita (s)

	@property parameter_schedule_start type=textbox default=300 parent=right_column
	@caption Ajaplaani alguse vahe planeerimise alguse hetkega (s)

	@property parameter_start_priority type=textbox default=1 parent=right_column
	@comment Positiivne reaalarv v&otilde;i 0 kui algusaega ei taheta parima valimisel arvestada. Kasutatakse mitut paralleelset t&ouml;&ouml;d v&otilde;imaldavate ressursside juures t&ouml;&ouml;le kalendrist parima koha valikul. Koha kaal arvutatakse valemiga: (AlgusajaKaal X ParalleelharuVabaAjaAlgus + PikkuseKaal X ParalleelharuVabaAjaPikkus)/2
	@caption T&ouml;&ouml; algusaja kaal

	@property parameter_length_priority type=textbox default=1 parent=right_column
	@comment Vt. t&ouml;&ouml; algusaja kaalu selgitust.
	@caption T&ouml;&ouml; pikkuse kaal

	@property parameter_timescale type=textarea rows=7 cols=30 parent=right_column
	@caption Otsingutabeli ajaskaala definitsioon (Jaotuste algused, komaga eraldatud. Esimene peaks alati 0 olema.)

	@property parameter_timescale_unit type=select parent=right_column
	@caption Skaala aja&uuml;hik

	@property parameter_plan_materials type=checkbox ch_value=1 parent=right_column
	@caption Planeeri materjalide kasutust

	@property logging_disabled type=checkbox ch_value=1 parent=right_column
	@caption &Auml;ra logi operatsioone


#@default group=grp_printer_current,grp_printer_done,grp_printer_aborted,grp_printer_in_progress,grp_printer_startable,grp_printer_notstartable
@default group=grp_printer

	@layout printer_master type=hbox width=25%:75%

		@layout printer_left type=vbox parent=printer_master

			@layout printer_tree type=vbox parent=printer_left closeable=1 area_caption=T&ouml;&ouml;de&nbsp;staatused

				@property printer_tree type=treeview parent=printer_tree store=no no_caption=1

			@layout printer_time_tree_l type=vbox parent=printer_left closeable=1 area_caption=T&ouml;&ouml;d&nbsp;kuup&auml;evade&nbsp;kaupa
				@property printer_time_tree type=treeview parent=printer_time_tree_l store=no no_caption=1

			@layout printer_resource_tree_l type=vbox parent=printer_left closeable=1 area_caption=T&ouml;&ouml;d&nbsp;ressursside&nbsp;kaupa
				@property printer_resource_tree type=treeview parent=printer_resource_tree_l store=no no_caption=1

			@layout printer_search type=vbox parent=printer_left closeable=1 area_caption=T&ouml;&ouml;de&nbsp;otsing

				@property ps_resource type=select parent=printer_search captionside=top
				@caption Resurss

				@property ps_project type=textbox parent=printer_search captionside=top size=33
				@caption Projekt

				@property ps_submit type=submit parent=printer_search
				@caption Otsi

			@layout printer_personal type=vbox parent=printer_left closeable=1 area_caption=T&ouml;&ouml;taja

				@layout ppl_data type=vbox parent=printer_personal

					@property pp_picture type=text parent=ppl_data store=no

					@property pp_name type=text parent=ppl_data store=no
					@caption Nimi:

					@property pp_profession type=text parent=ppl_data store=no
					@caption Amet:

					@property pp_section type=text parent=ppl_data store=no
					@caption &Uuml;ksus:

					@property pp_company type=text parent=ppl_data store=no
					@caption Organisatsioon:

				@layout ppl_birthdays type=vbox_sub parent=printer_personal area_caption=Meie&nbsp;t&ouml;&ouml;tajate&nbsp;s&uuml;nnip&auml;evad closeable=1 no_padding=1

					@property pp_birthdays type=table parent=ppl_birthdays no_caption=1 store=no

		@layout printer_right_m type=vbox parent=printer_master

		@layout printer_right type=vbox parent=printer_master parent=printer_right_m

			@property printer_jobs_prev_link type=text store=no no_caption=1 parent=printer_right

			@property printer_jobs type=table no_caption=1 parent=printer_right

			@property printer_jobs_next_link type=text store=no no_caption=1 parent=printer_right


			@property pj_toolbar type=toolbar store=no no_caption=1 parent=printer_right
			@caption Muuda staatust

			// these are shown when a job is selected
			@property pj_case_header type=text no_caption=1 store=no parent=printer_right

			@property pj_errors type=text store=no parent=printer_right
			@caption Vead

			@layout comment_hbox type=hbox width=40%:60% parent=printer_right
			@caption Kommentaar

			@property pj_change_comment type=textarea rows=5 cols=50 store=no parent=comment_hbox captionside=top
			@caption T&ouml;&ouml; kommentaar

			@property pj_change_comment_history type=text store=no parent=comment_hbox no_caption=1

			@property pj_title_job_data type=text store=no subtitle=1 parent=printer_right
			@caption T&ouml;&ouml; andmed

				@property pj_starttime type=text store=no parent=printer_right
				@caption Algus

				@property pj_length type=text store=no parent=printer_right
				@caption Plaanitud kestus (h)

				@property pj_minstart type=datetime_select store=no parent=printer_right
				@caption Arvatav j&auml;tkamisaeg

				@property pj_remaining_length type=textbox store=no parent=printer_right
				@caption Arvatav l&otilde;petamiseks kuluv aeg (h)

				@property pj_submit type=submit store=no parent=printer_right
				@caption Salvesta

				property pj_pre_buffer type=text store=no parent=printer_right
				caption Eelpuhveraeg (h)

				property pj_post_buffer type=text store=no parent=printer_right
				caption J&auml;relpuhveraeg (h)

				@layout resource_hbox type=hbox width="50%:50%" parent=printer_right
				@caption Ressurss

				@property pj_resource type=text store=no parent=printer_right
				@caption Ressurss

				@property pj_job_comment type=text store=no parent=printer_right
				@caption Kommentaar

				@property pj_state type=text store=no parent=printer_right
				@caption Staatus

			@property pjp_title_proj_data type=text store=no subtitle=1 parent=printer_right
			@caption Projekti andmed

				@property pjp_name type=text store=no parent=printer_right
				@caption Projekti number

				@property pjp_comment type=text store=no parent=printer_right
				@caption Projekti nimetus

				@property pjp_customer type=text store=no parent=printer_right
				@caption Klient

				@property pjp_format type=text store=no parent=printer_right
				@caption Formaat

				@property pjp_sisu_lk_arv type=text store=no parent=printer_right
				@caption Sisu lk arv

				@property pjp_kaane_lk_arv type=text store=no parent=printer_right
				@caption Kaane lk arv

				@property pjp_sisu_varvid type=text store=no parent=printer_right
				@caption Sisu v&auml;rvid

				@property pjp_sisu_varvid_notes type=text store=no parent=printer_right
				@caption Sisu v&auml;rvid Notes

				@property pjp_sisu_lakk_muu type=text store=no parent=printer_right
				@caption Sisu lakk/muu

				@property pjp_kaane_varvid type=text store=no parent=printer_right
				@caption Kaane v&auml;rvid

				@property pjp_kaane_varvid_notes type=text store=no parent=printer_right
				@caption Kaane v&auml;rvid Notes

				@property pjp_kaane_lakk_muu type=text store=no parent=printer_right
				@caption Kaane lakk/muu

				@property pjp_sisu_paber type=text store=no parent=printer_right
				@caption Sisu paber

				@property pjp_kaane_paber type=text store=no parent=printer_right
				@caption Kaane paber

				@property pjp_trykiarv type=text store=no parent=printer_right
				@caption Tr&uuml;kiarv

				@property pjp_trykise_ehitus type=text store=no parent=printer_right
				@caption Tr&uuml;kise ehitus

				@property pjp_kromaliin type=text store=no parent=printer_right
				@caption Kromalin

				@property pjp_makett type=text store=no parent=printer_right
				@caption Makett

				@property pjp_naidis type=text store=no parent=printer_right
				@caption N&auml;idis

			@property pjp_title_case_wf type=text store=no subtitle=1 parent=printer_right
			@caption Projekti t&ouml;&ouml;voog

			@property pjp_material type=table store=no no_caption=1 parent=printer_right

			@property pjp_case_wf type=table store=no no_caption=1 parent=printer_right


		@layout printer_legend_box type=vbox closeable=1 area_caption=Legend parent=printer_right_m

			@property printer_legend type=text no_caption=1 parent=printer_legend_box


@default group=grp_login_select_res
	@property select_session_resource type=select store=no
	@caption Vali kasutatav ressurss


@default group=grp_worksheet
	@property ws_resource type=select multiple=1 store=no size=5
	@caption Ressursid

	@property ws_from type=date_select store=no
	@caption Alates

	@property ws_to type=date_select store=no
	@caption Kuni

	@property ws_sbt type=submit store=no
	@caption N&auml;ita

	@property ws_tbl type=table store=no no_caption=1

@default group=grp_res_settings

	@property res_settings_tb type=toolbar store=no no_caption=1


	@layout grp_res_settings_splitter type=hbox

		@layout grp_res_settings_tree_vb type=vbox closeable=1 area_caption=Seadete&nbsp;kategooriad parent=grp_res_settings_splitter

			@property grp_res_settings_tree type=treeview store=no no_caption=1 parent=grp_res_settings_tree_vb

		@property grp_res_settings_table type=table store=no no_caption=1 parent=grp_res_settings_splitter

@default group=grp_res_formats

	@property res_formats_tb type=toolbar store=no no_caption=1


	@layout grp_res_formats_splitter type=hbox

		@layout grp_res_formats_tree_vb type=vbox closeable=1 area_caption=Formaatide&nbsp;kategooriad parent=grp_res_formats_splitter

			@property grp_res_formats_tree type=treeview store=no no_caption=1 parent=grp_res_formats_tree_vb

		@property grp_res_formats_table type=table store=no no_caption=1 parent=grp_res_formats_splitter


// --------------- RELATION TYPES ---------------------

@reltype MRP_FOLDER value=1 clid=CL_MENU
@caption Kaust

@reltype MRP_WORKSPACE_CFGMGR clid=CL_CFGMANAGER value=2
@caption Keskkonna seaded

@reltype MRP_OWNER clid=CL_CRM_COMPANY value=4
@caption Keskkonna omanik (Organisatsioon)

@reltype MRP_HEADER_CONTROLLER clid=CL_FORM_CONTROLLER value=5
@caption Projekti headeri kontroller

@reltype PURCHASING_MANAGER clid=CL_SHOP_PURCHASE_MANAGER_WORKSPACE value=6
@caption Materjalide hankimise keskkond

@reltype RESOURCE_SETTING clid=CL_MRP_RESOURCE_SETTING_CATEGORY,CL_MRP_RESOURCE_SETTING value=7
@caption Ressursside seaded

@reltype RESOURCE_FORMAT clid=CL_MRP_RESOURCE_FORMAT_CATEGORY,CL_MRP_ORDER_PRINT_FORMAT value=8
@caption Ressursside formaadid
*/

require_once "mrp_header.aw";

class mrp_workspace extends class_base
{
	public static $state_colours = array (
		mrp_case_obj::STATE_NEW => MRP_COLOUR_NEW,
		mrp_case_obj::STATE_PLANNED => MRP_COLOUR_PLANNED,
		mrp_case_obj::STATE_INPROGRESS => MRP_COLOUR_INPROGRESS,
		mrp_case_obj::STATE_ABORTED => MRP_COLOUR_ABORTED,
		mrp_case_obj::STATE_DONE => MRP_COLOUR_DONE,
		mrp_case_obj::STATE_ONHOLD => MRP_COLOUR_ONHOLD,
		mrp_case_obj::STATE_ARCHIVED => MRP_COLOUR_ARCHIVED,

		mrp_job_obj::STATE_NEW => MRP_COLOUR_NEW,
		mrp_job_obj::STATE_PLANNED => MRP_COLOUR_PLANNED,
		mrp_job_obj::STATE_INPROGRESS => MRP_COLOUR_INPROGRESS,
		mrp_job_obj::STATE_ABORTED => MRP_COLOUR_ABORTED,
		mrp_job_obj::STATE_DONE => MRP_COLOUR_DONE,
		mrp_job_obj::STATE_PAUSED => MRP_COLOUR_PAUSED,
		mrp_job_obj::STATE_SHIFT_CHANGE => MRP_COLOUR_SHIFT_CHANGE
	);


	private $project_list_categories = array(
		"inwork" => "",
		"planned_overdue" => "",
		"overdue" => "",
		"new" => "",
		"planned" => "",
		"subcontracts" => "",
		"all" => "",
		"aborted_jobs" => "",
		"archived" => "",
		"aborted" => "",
		"onhold" => "",
		"search" => "",
		"done" => ""
	);

	var $pj_colors = array(
		"done" => "#BADBAD",
		"can_start" => "#eff6d5",
		"can_not_start" => "#ffe1e1",
		"resource_in_use" => "#ecd995",
		"search_result" => "#a255ff"
	);

	var $active_resource_states = array(
		MRP_STATUS_RESOURCE_AVAILABLE,
		MRP_STATUS_RESOURCE_OUTOFSERVICE,
		MRP_STATUS_RESOURCE_INUSE
	);

	function mrp_workspace()
	{
		$this->project_list_categories = array(
			"inwork" => t("Hetkel t&ouml;&ouml;s"),
			"planned_overdue" => t("Planeeritud &uuml;le t&auml;htaja"),
			"overdue" => t("&Uuml;le t&auml;htaja"),
			"new" => t("Uued"),
			"planned" => t("Plaanisolevad"),
			"all" => t("K&otilde;ik projektid"),
			"archived" => t("Arhiveeritud"),
			"subcontracts" => t("Allhanket&ouml;&ouml;d"),
			"aborted" => t("Katkestatud"),
			"aborted_jobs" => t("Katkestatud t&ouml;&ouml;d"),
			"onhold" => t("Plaanist v&auml;ljas"),
			"search" => t("Otsingutulemused"),
			"done" => t("Valmis")
		);

		$this->resource_states = array(
			0 => "M&auml;&auml;ramata",
			MRP_STATUS_RESOURCE_AVAILABLE => t("Vaba"),
			MRP_STATUS_RESOURCE_INUSE => t("Kasutusel"),
			MRP_STATUS_RESOURCE_OUTOFSERVICE => t("Suletud"),
			MRP_STATUS_RESOURCE_INACTIVE => t("Arhiveeritud")
		);

		$this->states = array (
			MRP_STATUS_NEW => t("Uus"),
			MRP_STATUS_PLANNED => t("Planeeritud"),
			MRP_STATUS_INPROGRESS => t("T&ouml;&ouml;s"),
			MRP_STATUS_ABORTED => t("Katkestatud"),
			MRP_STATUS_DONE => t("Valmis"),
			MRP_STATUS_LOCKED => t("Lukustatud"),
			MRP_STATUS_PAUSED => t("Paus"),
			MRP_STATUS_SHIFT_CHANGE => t("Operaatori vahetus"),
			MRP_STATUS_DELETED => t("Kustutatud"),
			MRP_STATUS_ONHOLD => t("Plaanist v&auml;ljas"),
			MRP_STATUS_ARCHIVED => t("Arhiveeritud")
		);

		$this->init(array(
			"tpldir" => "mrp/mrp_workspace",
			"clid" => CL_MRP_WORKSPACE
		));

		$this->import = get_instance(CL_MRP_PRISMA_IMPORT);
	}

	/**

		@attrib name=save_pj_comment

	**/
	function save_pj_comment($arr)
	{
		$job = obj($arr["pj_job"]);
		$job->add_comment($arr["pj_change_comment"]);
		$job->save();
		return $arr["post_ru"];
	}

	/**
		@attrib name=save_pj_material
	**/
	function save_pj_material($arr)
	{
		$job = obj($arr["pj_job"]);
		$units = array();
		foreach($job->get_material_expense_list() as $id => $material)
		{
			$units[$id] = $material->prop("unit");
		}
		foreach($arr["material_amount"] as $prod => $amount)
		{
			$job->set_used_material_assessment(obj($prod) , $amount, $units[$prod]);
/*			$job->set_used_material(array(
				"product" => $prod,
				"amount" => $amount,
				"unit" => $units[$prod],
			));*/
		}

		return $arr["post_ru"];
	}

	private function _get_res_set_pt($arr)
	{
		return isset($arr["request"]["res_fld"]) ? $arr["request"]["res_fld"] : $arr["obj_inst"]->id();
	}

	public function _get_res_settings_tb($arr)
	{
		$pt = $this->_get_res_set_pt($arr);
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(isset($arr["request"]["res_fld"]) ? array(CL_MRP_RESOURCE_SETTING_CATEGORY, CL_MRP_RESOURCE_SETTING) : array(CL_MRP_RESOURCE_SETTING_CATEGORY), $pt, 7);
		$tb->add_delete_button();
	}

	public function _get_grp_res_settings_table($arr)
	{
		$pt = $this->_get_res_set_pt($arr);
		$arr["prop"]["vcl_inst"]->table_from_ol(
			new object_list(obj($pt)->connections_from(array("type" => "RELTYPE_RESOURCE_SETTING"))),
			array("name", "class_id", "createdby_person", "created", "modifiedby_person", "modified"),
			CL_MRP_RESOURCE_SETTING
		);
	}

	public function _get_grp_res_settings_tree($arr)
	{
		$this->_req_res_settings_tree($arr["prop"]["vcl_inst"], $arr["obj_inst"], 0);

		if (isset($arr["request"]["res_fld"]))
		{
			$arr["prop"]["vcl_inst"]->set_selected_item($arr["request"]["res_fld"]);
		}

		$arr["prop"]["vcl_inst"]->set_root_url(aw_url_change_var("res_fld", null));
		$arr["prop"]["vcl_inst"]->set_root_name($arr["obj_inst"]->name());
		$arr["prop"]["vcl_inst"]->set_root_icon(icons::get_icon_url(CL_MENU));
	}

	private function _req_res_settings_tree($t, $pto, $pt)
	{
		foreach($pto->connections_from(array("type" => "RELTYPE_RESOURCE_SETTING")) as $c)
		{
			$o = $c->to();
			if ($o->class_id() == CL_MRP_RESOURCE_SETTING_CATEGORY)
			{
				$t->add_item($pt, array(
					"id" => $o->id(),
					"name" => $o->name(),
					"url" => aw_url_change_var("res_fld", $o->id()),
					"icon" => icons::get_icon_url($o->class_id() == CL_MRP_RESOURCE_SETTING_CATEGORY ? CL_MENU : $o->class_id())
				));
				$this->_req_res_settings_tree($t, $o, $o->id());
			}
		}
	}


	private function _get_res_fmt_pt($arr)
	{
		return isset($arr["request"]["res_fld"]) ? $arr["request"]["res_fld"] : $arr["obj_inst"]->id();
	}

	public function _get_res_formats_tb($arr)
	{
		$pt = $this->_get_res_fmt_pt($arr);
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_new_button(isset($arr["request"]["res_fld"]) ? array(CL_MRP_RESOURCE_FORMAT_CATEGORY, CL_MRP_ORDER_PRINT_FORMAT) : array(CL_MRP_RESOURCE_FORMAT_CATEGORY), $pt, 8);
		$tb->add_delete_button();
	}

	public function _get_grp_res_formats_table($arr)
	{
		$pt = $this->_get_res_fmt_pt($arr);
		$arr["prop"]["vcl_inst"]->table_from_ol(
			new object_list(obj($pt)->connections_from(array("type" => "RELTYPE_RESOURCE_FORMAT"))),
			array("name", "class_id", "createdby_person", "created", "modifiedby_person", "modified"),
			CL_MRP_ORDER_PRINT_FORMAT
		);
	}

	public function _get_grp_res_formats_tree($arr)
	{
		$this->_req_res_format_tree($arr["prop"]["vcl_inst"], $arr["obj_inst"], 0);

		if (isset($arr["request"]["res_fld"]))
		{
			$arr["prop"]["vcl_inst"]->set_selected_item($arr["request"]["res_fld"]);
		}

		$arr["prop"]["vcl_inst"]->set_root_url(aw_url_change_var("res_fld", null));
		$arr["prop"]["vcl_inst"]->set_root_name($arr["obj_inst"]->name());
		$arr["prop"]["vcl_inst"]->set_root_icon(icons::get_icon_url(CL_MENU));
	}

	private function _req_res_format_tree($t, $pto, $pt)
	{
		foreach($pto->connections_from(array("type" => "RELTYPE_RESOURCE_FORMAT")) as $c)
		{
			$o = $c->to();
			if ($o->class_id() == CL_MRP_RESOURCE_FORMAT_CATEGORY)
			{
				$t->add_item($pt, array(
					"id" => $o->id(),
					"name" => $o->name(),
					"url" => aw_url_change_var("res_fld", $o->id()),
					"icon" => icons::get_icon_url($o->class_id() == CL_MRP_RESOURCE_FORMAT_CATEGORY ? CL_MENU : $o->class_id())
				));
				$this->_req_res_format_tree($t, $o, $o->id());
			}
		}
	}


	public function callback_pre_edit ($arr)
	{
		$this_object = $arr["obj_inst"];

		$this->hours_report_time_format = $this_object->prop("hr_time_format");

		if (!empty($arr["group"]) and $arr["group"] === "grp_projects")
		{
			if (isset($arr["list_request"]))
			{
				$this->list_request = $arr["list_request"];
			}
			elseif (!empty($arr["request"]["sp_search"]))
			{
				$this->list_request = "search";
			}
			elseif (isset($arr["request"]["mrp_tree_active_item"]))
			{
				$this->list_request = $arr["request"]["mrp_tree_active_item"];
			}
			else
			{
				$this->list_request = "planned";
			}

			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"state" => MRP_STATUS_PLANNED,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));

			$this->projects_planned_count = $list->count();

			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"state" => MRP_STATUS_INPROGRESS,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->projects_in_work_count = $list->count();

			$applicable_states = array ( // also used below for getting limited lists
				MRP_STATUS_INPROGRESS,
				MRP_STATUS_PLANNED,
			);
			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"due_date" => new obj_predicate_compare (OBJ_COMP_LESS, time()),
				"state" => $applicable_states,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->projects_overdue_count = $list->count();

			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"state" => $applicable_states,
				"planned_date" => new obj_predicate_prop (OBJ_COMP_GREATER, "due_date"),
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->projects_planned_overdue_count = $list->count();

			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"state" => MRP_STATUS_NEW,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->projects_new_count = $list->count();

			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"state" => MRP_STATUS_DONE,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->projects_done_count = $list->count();

			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"state" => MRP_STATUS_ABORTED,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->projects_aborted_count = $list->count();

			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"state" => MRP_STATUS_ONHOLD,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->projects_onhold_count = $list->count();

/* very slow and gives little useful info. disabled for now.
			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"state" => MRP_STATUS_ARCHIVED,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->projects_archived_count = $list->count();
 */
			$list = new object_list (array (
				"class_id" => CL_MRP_CASE,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->projects_all_count = $list->count();

			$list = new object_list (array (
				"class_id" => CL_MRP_JOB,
				"state" => MRP_STATUS_ABORTED,
				"parent" => $this_object->prop ("jobs_folder"),
				// "createdby" => aw_global_get('uid'),
			));
			$this->jobs_aborted_count = $list->count();

			$list = $this->_get_subcontract_job_list($this_object);
			$this->jobs_subcontracted_count = $list->count();

			### project list args
			#### limit
			$perpage = $this_object->prop ("projects_list_objects_perpage") ? $this_object->prop ("projects_list_objects_perpage") : 30;
			$limit = ((isset($arr["request"]["ft_page"]) ? (int) $arr["request"]["ft_page"] : 0) * $perpage) . "," . $perpage;

			$sort_order = (isset($arr["request"]["sort_order"]) and "desc" === $arr["request"]["sort_order"]) ? "desc" : "asc";
			$tmp = NULL;

			#### sort
			switch ($this->list_request)
			{
				case "inwork":
					$sort_by = new obj_predicate_sort(array("due_date" => $sort_order));
					break;

				case "planned_overdue":
				case "overdue":
				case "new":
				case "planned":
				case "all":
				case "done":
				default:
					$sort_by = new obj_predicate_sort(array("starttime" => $sort_order));
					break;
			}

			if (isset($arr["request"]["sortby"]))
			{
				switch ($arr["request"]["sortby"])
				{
					case "starttime":
						$sort_by = new obj_predicate_sort(array("starttime" => $sort_order));
						break;

					case "planned_date":
						$sort_by = new obj_predicate_sort(array("planned_date" => $sort_order));
						$tmp = new obj_predicate_compare (OBJ_COMP_GREATER, 0);//!!! temporary. acceptable solution needed. projects with planned_date NULL not retrieved.
						break;

					case "due_date":
						$sort_by = new obj_predicate_sort(array("due_date" => $sort_order));
						break;

					case "priority":
						$sort_by = new obj_predicate_sort(array("project_priority" => $sort_order));
						break;
				}
			}

			#### common args
			$args = array(
				"class_id" => CL_MRP_CASE,
				"limit" => $limit,
				"parent" => $this_object->prop ("projects_folder"),
				// "createdby" => aw_global_get('uid'),
				$sort_by,
				"planned_date" => $tmp,//!!! to enable sorting by planned_date which is in mrp_case_schedule table
			);


			if(!empty($arr["request"]["cat"]) || !empty($arr["request"]["alph"]))
			{
				$co_id = $arr["obj_inst"]->prop("owner");
				if (!$this->can("view", $co_id))
				{
					break;
				}
				else
				{
					$co = obj($co_id);
				}
				$customers = new object_list();
				if (isset($arr["request"]["cat"]) and is_oid($arr["request"]["cat"]))
				{
					// get customers from cat
					$cat = obj($arr["request"]["cat"]);
					foreach($cat->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
					{
						$customers->add($c->prop("to"));
					}
				}
				elseif($arr["request"]["alph"])
				{
					$customers_data = $co->get_all_customer_ids(array("name" => $arr["request"]["alph"]));
					foreach($customers_data as $customer)
					{
						if($this->can("view" , $customer))
						{
							$customers->add($customer);
						}
					}
				}
				$args["customer"] = $customers->ids();
			}

			if(!empty($arr["request"]["timespan"]))
			{
				switch($arr["request"]["timespan"])
				{
					case "current_week":
						list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
						$from = mktime(0, 0, 0, $M, $D-$N+1, $Y);
						$to = mktime(23, 59, 59, $M, $D+7-$N, $Y);
						break;

					case "last_week":
						list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
						$from = mktime(0, 0, 0, $M, $D-$N-6, $Y);
						$to = mktime(23, 59, 59, $M, $D-$N, $Y);
						break;

					case "current_month":
						list($Y, $M) = explode("-", date("Y-n"));
						$from = mktime(0, 0, 0, $M, 1, $Y);
						$to = mktime(23, 59, 59, $M+1, 0, $Y);
						break;

					case "last_month":
						list($Y, $M) = explode("-", date("Y-n"));
						$from = mktime(0, 0, 0, $M-1, 1, $Y);
						$to = mktime(23, 59, 59, $M, 0, $Y);
						break;
				}
				$args["created"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $from, $to, "int");
			}

			### get list
			if (strstr($this->list_request, "archived_"))
			{
				$tmp = explode("_", $this->list_request);

				if (3 == count($tmp))
				{
					$year = $tmp[1];
					$month = $tmp[2];
					unset($args["limit"]);
					$args["state"] = MRP_STATUS_ARCHIVED;
					$args["starttime"] = new obj_predicate_compare (
						OBJ_COMP_BETWEEN,
						mktime(0,0,0,$month,1,$year),
						mktime(0,0,0,((12 == $month) ? 1 : ($month + 1)),1,((12 == $month) ? ($year + 1) : $year))
					);

					$this->projects_list_objects = new object_list ($args);
					$this->projects_list_objects_count = $this->projects_list_objects->count();
					$args["limit"] = $limit;
					$this->projects_list_objects = new object_list ($args);
				}
			}
			else
			{
				switch ($this->list_request)
				{
					case "all":
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->projects_all_count;
						break;

					case "planned":
						$args["state"] = MRP_STATUS_PLANNED;
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->projects_planned_count;
						break;

					case "inwork":
						$args["state"] = MRP_STATUS_INPROGRESS;
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->projects_in_work_count;
						break;

					case "planned_overdue":
						$args["state"] = $applicable_states;
						$args["planned_date"] = new obj_predicate_prop (OBJ_COMP_GREATER, "due_date");
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->projects_planned_overdue_count;
						break;

					case "overdue":
						$args["due_date"] = new obj_predicate_compare (OBJ_COMP_LESS, time());
						$args["state"] = $applicable_states;
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->projects_overdue_count;
						break;

					case "new":
						$args["state"] = MRP_STATUS_NEW;
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->projects_new_count;
						break;

					case "done":
						$args["state"] = MRP_STATUS_DONE;
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->projects_done_count;
						break;

					case "aborted":
						$args["state"] = MRP_STATUS_ABORTED;
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->projects_aborted_count;
						break;

					case "onhold":
						$args["state"] = MRP_STATUS_ONHOLD;
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->projects_onhold_count;
						break;

					case "aborted_jobs":
						if(is_array($args["customer"]))
						{
							$projects = new object_list ($args);
							$args["project"] = $projects->ids();
						}

						$applicable_sortorders = array (
							"due_date",
						);

						if (in_array($arr["request"]["sortby"], $applicable_sortorders))
						{
							$args["CL_MRP_JOB.project(CL_MRP_CASE).due_date"] = new obj_predicate_compare (OBJ_COMP_GREATER, 0);//!!! temporary. acceptable solution needed. projects with planned_date NULL not retrieved.
							$args["sort_by"] = "mrp_case_826_project.due_date {$sort_order}";
						}
						else
						{
							unset($args["sort_by"]);
						}

					//	$args["project.customer"] = $args["customer"];
						unset($args["planned_date"]);
						unset($args["customer"]);

						$args["class_id"] = CL_MRP_JOB;
						$args["state"] = MRP_STATUS_ABORTED;
						$args["parent"] = $this_object->prop ("jobs_folder");
						$this->projects_list_objects = new object_list ($args);
						$this->projects_list_objects_count = $this->jobs_aborted_count;
						break;

					case "subcontracts":
						if(is_array($args["customer"]))
						{
							$projects = new object_list ($args);
							$args["project"] = $projects->ids();
						}
						$this->projects_list_objects = $this->_get_subcontract_job_list($this_object, NULL,$args["project"]);
						$this->projects_list_objects_count = $this->jobs_subcontracted_count;
						break;
				}
			}
		}

		if(!empty($arr["request"]["poo_started_finished_by"]))
		{
			$arr["obj_inst"]->set_prop("poo_started_finished_by", $arr["request"]["poo_started_finished_by"]);
			$arr["obj_inst"]->save();
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$this_object = $arr["obj_inst"];

		### require remaining_length and minstart when job was aborted_jobs
		if (is_oid (aw_global_get ("mrp_printer_aborted")))
		{
			if ($prop["name"] === "pj_remaining_length")
			{
				$job = obj (aw_global_get ("mrp_printer_aborted"));
				$len  = floor ($job->prop("remaining_length") / 3600);
				$prop["value"] = $len;
				return PROP_OK;
			}
			elseif ($prop["name"] === "pj_minstart")
			{
				$prop["value"] = time ();
				return PROP_OK;
			}
			elseif ($prop["name"] === "pj_submit")
			{
				return PROP_OK;
			}
			else
			{
				return PROP_IGNORE;
			}
		}
		else
		{
			if (($prop["name"] === "pj_remaining_length") or ($prop["name"] === "pj_minstart") or ($prop["name"] === "pj_submit"))
			{
				return PROP_IGNORE;
			}
		}

		if (substr($prop["name"], 0, 3) === "pjp")
		{
			if (empty($arr["request"]["pj_job"]))
			{
				return PROP_IGNORE;
			}

			// get prop from project
			if (empty($prop["subtitle"]))
			{
				$job = obj($arr["request"]["pj_job"]);
				$proj = obj($job->prop("project"));
				$rpn = substr($prop["name"], 4);
				$prop["value"] = $proj->prop($rpn);

				if ($prop["name"] === "pjp_case_wf")
				{
					$this->_pjp_case_wf($arr);
					return PROP_OK;
				}
				if ($prop["name"] === "pjp_material")
				{
					$this->_pjp_material($arr);
					return PROP_OK;
				}
				if ($prop["name"] === "pjp_customer")
				{
					$this->_pjp_customer($arr);
					return PROP_OK;
				}

				$retv = $this->import->get_prop_value($prop, $rpn);
				if ($retv != PROP_OK)
				{
					return $retv;
				}
			}
		}

		if (substr($prop["name"], 0, 3) === "pj_")
		{
			if (empty($arr["request"]["pj_job"]))
			{
				return PROP_IGNORE;
			}

			// get prop from job
			if (empty($prop["subtitle"]))
			{
				$job = obj($arr["request"]["pj_job"]);
				$rpn = substr($prop["name"], 3);
				switch($rpn)
				{
					case "errors":
						$errs = aw_global_get("mrpws_err");
						if (is_array($errs) && count($errs))
						{
							$prop["value"] = "<span style='color: #FF0000; font-size: 20px'>".join("<br>", $errs)."</span>";
							aw_session_del("mrpws_err");
							return PROP_OK;
						}
						return PROP_IGNORE;
						break;

					case "toolbar":
						$this->_do_pj_toolbar($arr, $job);
						return PROP_OK;

					case "starttime":
						$prop["value"] = date("d.m.Y H:i", $job->prop("starttime"));
						break;

					case "length":
						$len  = sprintf("%02d", floor($job->prop("length") / 3600)).":";
						$len .= sprintf("%02d", floor(($job->prop("length") % 3600) / 60));
						$prop["value"] = $len;
						break;

					case "pre_buffer":
						$len  = sprintf("%02d", floor($job->prop("pre_buffer") / 3600)).":";
						$len .= sprintf("%02d", floor(($job->prop("post_buffer") % 3600) / 60));
						$prop["value"] = $len;
						break;

					case "post_buffer":
						$len  = sprintf("%02d", floor($job->prop("post_buffer") / 3600)).":";
						$len .= sprintf("%02d", floor(($job->prop("post_buffer") % 3600) / 60));
						$prop["value"] = $len;
						break;

					case "project":
						$tmp = obj($job->prop($rpn));
						if ($this->can("edit", $tmp->id()))
						{
						$prop["value"] = html::href(array(
							"url" => $this->mk_my_orb("change", array(
								"id" => $tmp->id(),
								"return_url" => get_ru()
							)),
							"caption" => "<span style='font-size:20px'>" . $tmp->name() . "</span>"
						));
						}
						else
						{
							$prop["value"] = $tmp->name();
						}
						break;

					case "resource":
						$tmp = obj($job->prop($rpn));
						if ($this->can("edit", $tmp->id()))
						{
							$prop["value"] = html::obj_change_url($tmp);
						}
						else
						{
							$prop["value"] = $tmp->name();
						}
						break;

					case "state":
						$prop["value"] = "<span style='padding: 5px; background: ".self::$state_colours[$job->prop($rpn)]."'>".mrp_job_obj::get_state_names($job->prop($rpn))."<span>";
						break;

					case "case_header":
						$c_o = obj($job->prop("project"));
						$c_i = $c_o->instance();
						$prop["value"] = $c_i->get_header(array("obj_inst" => $c_o));
						break;

					case "change_comment_history":
						$txt = array();
						$cnt = 0;
						$user_inst = get_instance("user");

						foreach(safe_array($job->meta("change_comment_history")) as $comment_hist_item)
						{
							$user = $user_inst->get_obj_for_uid($comment_hist_item["uid"]);
							$txt[] = date("d.m.Y H:i", $comment_hist_item["tm"]).": ".$comment_hist_item["text"]." (".($user ? $user->get_user_name(): $comment_hist_item["uid"]).")";
							if ($cnt++ > 4)
							{
								break;
							}
						}
						$prop["value"] = "<div style='padding: 5px;'>".trim(join("<br>", $txt))."</div>";
						break;

					case "job_comment":
						$prop["value"] = $job->comment();
						break;

					default:
						$prop["value"] = $job->prop($rpn);
						if ($prop["value"] == "" && $prop["name"] !== "pj_change_comment")
						{
							return PROP_IGNORE;
						}
						break;
				}

				// Why is this necessary?	-kaarel 3.06.2009
				/*
				if ($prop["value"] == "")
				{
					$prop["value"] = "&nbsp;";
				}
				*/
			}
		}

		switch($prop["name"])
		{
			case "grp_material_btn":
				return PROP_IGNORE;
				$prop["onclick"] = "javascript:$('[name=grouping]').val('".$id."');update_material_table();";
				break;
			case "resource_stats_grouping":
				$prop["options"] = array(
					"0" => t("Ei grupeeri"),
					"people" => t("Inimeste kaupa"),
					"section" => t("Osakondade kaupa"),
					"resource" => t("Ressursside kaupa"),
					"work" => t("T&ouml;&ouml;de kaupa"),
				);
				$prop["onchange"] = "javascript:update_material_table();";
				break;
			### printer tab
			case "printer_tree":
				$this->_get_printer_tree($arr);
				break;

			case "pp_picture":
				$prop["value"] = get_instance("image")->make_img_tag_wl(obj(get_instance("user")->get_current_person())->picture);
				break;

			case "pp_name":
			case "pp_profession":
			case "pp_section":
			case "pp_company":
				$map = array(
					"pp_name" => "name",
					"pp_profession" => "rank.name",
					"pp_section" => "org_section.name",
					"pp_company" => "work_contact.name",
				);
				$prop["value"] = obj(get_instance("user")->get_current_person())->prop($map[$prop["name"]]);
				if(strlen($prop["value"]) === 0)
				{
					$retval = PROP_IGNORE;
				}
				break;

			case "pp_resources":
				$retval = PROP_IGNORE;
				//$this->_get_pp_resources($arr);
				break;

			case "pp_birthdays":
				$this->_get_pp_birthdays($arr);
				break;

			case "ps_resource":
				$resids = $this->get_cur_printer_resources(array(
					"ws" => $arr["obj_inst"],
					"ign_glob" => true
				));
				$res_ol = new object_list();
				if (count($resids))
				{
					$res_ol = new object_list(array("oid" => $resids,"sort_by" => "objects.name"));
				}
				$empty_selection = array();
				if (count($resids) > 1)
				{
					$empty_selection = array("0" => t("K&otilde;ik ressursid"));
				}
				$prop["options"] = $empty_selection + $res_ol->names();
				$prop["value"] = aw_global_get("mrp_operator_use_resource");
				$prop["onchange"] = "xchanged=1; submit_changeform('');";
				break;

			### projects tab
			case "sp_submit":
				$uri = automatweb::$request->get_uri();
				$uri->set_arg(array(
					"sp_search",
					"sp_name",
					"sp_starttime[day]",
					"sp_starttime[month]",
					"sp_starttime[year]",
					"sp_due_date[day]",
					"sp_due_date[month]",
					"sp_due_date[year]",
					"sp_customer",
					"sp_status"
				), null);
				$prop["onclick"] = 'window.location=\'' . $uri->get() .
					'&sp_search=1&sp_name=\'+document.forms[\'changeform\'].sp_name.value+\''.
					'&sp_starttime[day]=\'+document.forms[\'changeform\'][\'sp_starttime[day]\'][\'value\']+\''.
					'&sp_starttime[month]=\'+document.forms[\'changeform\'][\'sp_starttime[month]\'][\'value\']+\''.
					'&sp_starttime[year]=\'+document.forms[\'changeform\'][\'sp_starttime[year]\'][\'value\']+\''.
					'&sp_due_date[day]=\'+document.forms[\'changeform\'][\'sp_due_date[day]\'][\'value\']+\''.
					'&sp_due_date[month]=\'+document.forms[\'changeform\'][\'sp_due_date[month]\'][\'value\']+\''.
					'&sp_due_date[year]=\'+document.forms[\'changeform\'][\'sp_due_date[year]\'][\'value\']+\''.
					'&sp_status=\'+document.forms[\'changeform\'].sp_status.value+\''.
					'&sp_customer=\'+document.forms[\'changeform\'].sp_customer.value'
				;
				$prop["onclick"] = "update_projects_div();";
				break;

			// project search parameters
			case "sp_status":
				//$prop["options"] = array("" => t("K&otilde;ik")) + $this->states;
				//$prop["value"] = isset($arr["request"]["sp_status"]) ? $arr["request"]["sp_status"] : array();
				$prop["type"] = "text";
				$prop["value"] = "";
				foreach($this->states as $state => $name)
				{
					$prop["value"].=html::checkbox(array(
						"value" => 1,
						"name" => "sp_status[".$state."]",
					))." ".$name."<br>";
				}
				break;

			case "sp_name":
				$prop["value"] = isset($arr["request"]["sp_name"]) ? $arr["request"]["sp_name"] : "";
				break;

			case "sp_comment":
				$prop["value"] = isset($arr["request"]["sp_comment"]) ? $arr["request"]["sp_comment"] : "";
				break;

			case "sp_customer":
				$prop["value"] = isset($arr["request"]["sp_customer"]) ? $arr["request"]["sp_customer"] : "";
				break;

			case "sp_starttime":
				$prop["value"] = isset($arr["request"]["sp_starttime"]) ? $arr["request"]["sp_starttime"] : -1;
				break;

			case "sp_due_date":
				$prop["value"] = isset($arr["request"]["sp_due_date"]) ? $arr["request"]["sp_due_date"] : -1;
				break;

			case "projects_toolbar":
				$this->create_projects_toolbar ($arr);
				break;

			case "projects_tree":
				$this->create_projects_tree ($arr);
				break;
			case "projects_time_tree":
			case "customers_time_tree":
			case "printer_time_tree":
				$this->_get_projects_time_tree($arr);
				break;
			case "projects_list":
				if (empty($arr["request"]["sp_search"]))
				{
					$tree_active_item = isset($arr["request"]["mrp_tree_active_item"]) ? $arr["request"]["mrp_tree_active_item"] : null;
					switch ($tree_active_item)
					{
						case "subcontracts":
							### update schedule
							$schedule = get_instance (CL_MRP_SCHEDULE);
							$schedule->create (array("mrp_workspace" => $this_object->id()));

							$this->create_subcontract_jobs_list ($arr);
							break;

						case "aborted_jobs":
							$this->create_aborted_jobs_list ($arr);
							break;

						case "all":
						case "planned":
						case "inwork":
						case "planned_overdue":
						case "overdue":
						case "subcontracts":
							### update schedule
							$schedule = get_instance (CL_MRP_SCHEDULE);
							$schedule->create (array("mrp_workspace" => $this_object->id()));

						default:
							$this->create_projects_list ($arr);
							break;
					}
				}
				else
				{
					$retval = PROP_IGNORE;
				}
				break;

			### users tab
			case "users_toolbar":
				$this->create_users_toolbar ($arr);
				break;
			case "users_tree":
				$this->create_users_tree ($arr);
				break;
			case "users_list":
				$this->create_users_list ($arr);
				break;

			### resources tab
			case "resources_toolbar":
				$this->create_resources_toolbar ($arr);

				if (aw_global_get("mrp_errors"))
				{
					$retval = PROP_ERROR;
					$prop["error"] = aw_global_get("mrp_errors");
					aw_session_del("mrp_errors");
				}
				break;
			case "printer_resource_tree":
			case "my_resources_tree":
			case "resources_tree":
				$this->create_resources_tree ($arr);
				break;
			case "resources_list":
				$this->create_resources_list ($arr);
				break;
			case "my_resource_time_start":
			case "resource_time_start":
				if(isset($prop["value"]) and !is_numeric($prop["value"]))
				{
					$prop["value"] = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
				}
				break;

			### customers tab
			case "cs_submit":
				$prop["onclick"] = "document.forms.changeform.cat.value='';";
				break;
			case "customers_toolbar":
				$this->create_customers_toolbar ($arr);
				break;
			case "customers_tree":
			case "project_customers_tree":
				$this->create_customers_tree ($arr);
				break;
			case "customers_list":
				return $this->create_customers_list ($arr);
				break;
			case "customers_list_proj":
				return $this->create_customers_list_proj ($arr);
				break;

			### schedule tab
			case "states_chart":
				$applicable_states = array_diff(array_keys($this->states), array(MRP_STATUS_DONE, MRP_STATUS_ARCHIVED));
				$data = array();
				$labels = array();
				$colors = array();
				$odl = new object_data_list(
					array(
						"class_id" => CL_MRP_CASE,
						"parent" => $this_object->prop("projects_folder"),
						"state" => $applicable_states,
					),
					array(
						CL_MRP_CASE => array("state"),
					)
				);
				foreach($odl->arr() as $o)
				{
					if(!isset($data[$o["state"]]))
					{
						$data[$o["state"]] = 1;
					}
					else
					{
						$data[$o["state"]]++;
					}
					$colors[$o["state"]] = strtolower(preg_replace("/[^0-9A-Za-z]/", "", self::$state_colours[$o["state"]]));
					$labels[$o["state"]] = $this->states[$o["state"]]." (".$data[$o["state"]].")";
				}

				$c = &$arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_PIE_3D);
				$c->set_size(array(
					"width" => 500,
					"height" => 100,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));
				$c->set_colors($colors);
				$c->add_data($data);
				$c->set_labels($labels);
				break;

			case "clients_chart":
				$data = array();
				$labels = array();
				$prms = array(
					"class_id" => CL_MRP_CASE,
					"parent" => $this_object->prop("projects_folder"),
				);
				$clientspan = automatweb::$request->arg("clientspan");
				if(is_oid($clientspan) && $this->can("view", $clientspan))
				{
					$clientspan_obj = obj($clientspan);
					switch($clientspan_obj->class_id())
					{
						case CL_CRM_CATEGORY:
							$prms["customer.RELTYPE_CUSTOMER(CL_CRM_CATEGORY)"] = $clientspan;
							break;

						case CL_CRM_COMPANY:
							$prms["customer"] = $clientspan;
							break;
					}
				}
				elseif(!empty($clientspan))
				{
					$prms["customer.name"] = $clientspan."%";
				}
				$odl = new object_data_list(
					$prms,
					array(
						CL_MRP_CASE => array("customer", "customer.name"),
					)
				);
				foreach($odl->arr() as $o)
				{
					$key = $o["customer"];
					$name = $o["customer.name"];
					if(!isset($data[$key]))
					{
						$data[$key] = 1;
					}
					else
					{
						$data[$key]++;
					}
					$labels[$key] = $name." (".$data[$key].")";
				}
				$top_cust = $data;
				rsort($top_cust);
				// If there are over 20 customers, show only the top 20.
				$requirement = $top_cust[min(20, count($top_cust)) - 1];
				foreach($data as $k => $v)
				{
					if($v < $requirement)
					{
						unset($data[$k]);
						unset($labels[$k]);
					}
				}
				$labels[0] = sprintf(t("(M&Auml;&Auml;RAMATA) (%u)"), $data[0]);
				$c = &$arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_PIE_3D);
				$c->set_size(array(
					"width" => 800,
					"height" => 200,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));
				$colors = array();
				for($i = 15; $i > 0; $i -= 3)
				{
					$colors[] = "aaaa".str_repeat(dechex($i), 2);
					$colors[] = "bb".str_repeat(dechex($i), 2)."bb";
					$colors[] = str_repeat(dechex($i), 2)."cccc";
					$colors[] = "fb".str_repeat(dechex($i), 2)."bf";
					$colors[] = "affa".str_repeat(dechex($i), 2);
					$colors[] = str_repeat(dechex($i), 2)."ceec";
				}
				$colors = array_slice($colors, 0, count($data));
				$c->set_colors($colors);
				$data = array_slice($data, 0, 60);
				$labels = array_slice($labels, 0, 60);
				$c->add_data($data);
				$c->set_labels($labels);
				break;

			case "deadline_chart":
				$data = array();
				$labels = array();
				$odl = new object_data_list(
					array(
						"class_id" => CL_MRP_CASE,
						"state" => array(
							MRP_STATUS_INPROGRESS,
							MRP_STATUS_PLANNED,
						),
						"parent" => $this_object->prop("projects_folder"),
					),
					array(
						CL_MRP_CASE => array("due_date", "planned_date"),
					)
				);
				$names = array(
					0 => t("Graafikus"),
					1 => t("&Uuml;le t&auml;htaja"),
				);
				$colors_ = array(
					0 => "00ff00",
					1 => "ff0000",
				);
				foreach($odl->arr() as $o)
				{
					$key = $o["planned_date"] > $o["due_date"] ? 1 : 0;
					if(!isset($data[$key]))
					{
						$data[$key] = 1;
					}
					else
					{
						$data[$key]++;
					}
					$colors[$key] = $colors_[$key];
					$labels[$key] = $names[$key]." (".$data[$key].")";
				}

				$c = &$arr["prop"]["vcl_inst"];
				$c->set_type(GCHART_PIE_3D);
				$c->set_size(array(
					"width" => 500,
					"height" => 100,
				));
				$c->add_fill(array(
					"area" => GCHART_FILL_BACKGROUND,
					"type" => GCHART_FILL_SOLID,
					"colors" => array(
						"color" => "e9e9e9",
					),
				));
				$c->set_colors($colors);
				$c->add_data($data);
				$c->set_labels($labels);
				break;

			case "master_schedule_chart":
				### update schedule
				$schedule = get_instance (CL_MRP_SCHEDULE);
				$schedule->create (array("mrp_workspace" => $this_object->id()));

				$prop["value"] = $this->create_schedule_chart($arr);
				break;

			case "chart_start_date":
				$prop["value"] = empty ($arr["request"]["mrp_chart_start"]) ? $this->get_week_start () : $arr["request"]["mrp_chart_start"];
				break;

			// case "chart_project_hilight":
				// if (is_oid ($arr["request"]["mrp_hilight"]))
				// {
					// $options = array ();
					// $prop["value"] = $arr["request"]["mrp_hilight"];
				// }
				// else
				// {
					// $options = array ("0" => " ");
				// }

				// $applicable_states = array (
					// MRP_STATUS_PLANNED,
					// MRP_STATUS_DONE,
					// MRP_STATUS_ARCHIVED,
					// MRP_STATUS_INPROGRESS,
				// );

				// $list = new object_list (array (
					// "class_id" => CL_MRP_CASE,
					// "state" => $applicable_states,
					// "parent" => $this_object->prop ("projects_folder"),
				// ));

				// for ($project =& $list->begin (); !$list->end (); $project =& $list->next ())
				// {
					// $options[$project->id ()] = $project->name ();
				// }

				// $prop["options"] = $options;
				// break;

			case "replan":
				if ($arr["request"]["action"] == "view")
				{
					return PROP_IGNORE;
				}
				$plan_url = $this->mk_my_orb("create", array(
					"return_url" => get_ru(),
					"mrp_workspace" => $this_object->id (),
					"mrp_force_replan" => 1,
				), "mrp_schedule");
				$plan_href = html::href(array(
					"caption" => t("[Planeeri]"),
					"url" => $plan_url,
					)
				);
				$prop["value"] = $plan_href;
				break;

			### settings tab
			case "automatic_archiving_period_unit":
				$prop["value"] = t("P&auml;ev(a) peale projekti valmimist");
				break;

			case "parameter_timescale_unit":
				$prop["options"] = array (
					"86400" => t("P&auml;ev"),
					"60" => t("Minut"),
				);
				break;

			case "max_subcontractor_timediff":
				$prop["value"] = isset($prop["value"]) ? round (($prop["value"] / 3600), 2) : 0;
				break;

			case "hr_time_format":
				$prop["options"] = array(
					0 => t("Tunnid k&auml;ndendmurruna"),
					1 => t("Tunnid ja minutid"),
				);
				$prop["value"] = isset($prop["value"]) ? (int)$prop["value"] : 0;
				break;

			### users tab
			case "user_list_toolbar":
				$this->_user_list_toolbar($arr);
				break;

			case "user_list_tree":
				$this->_user_list_tree($arr);
				break;

			case "user_list":
				$this->_user_list($arr);
				break;

			case "user_mgr_toolbar":
				$this->_user_mgr_toolbar($arr);
				break;

			case "user_mgr_tree":
				$this->_user_mgr_tree($arr);
				break;

			case "user_mgr":
				$this->_user_mgr($arr);
				break;

			case "printer_jobs":
				if (!empty($arr["request"]["pj_job"]))
				{
					return PROP_IGNORE;
				}

				### update schedule
				$schedule = get_instance (CL_MRP_SCHEDULE);
				$schedule->create (array("mrp_workspace" => $this_object->id()));

				$this->_printer_jobs($arr);
				break;

			case "printer_legend":
				if (!empty($arr["request"]["pj_job"]))
				{
					$retval = PROP_IGNORE;
				}
				else
				{
					$prop["value"] = "<span style='font-size: 11px; padding: 5px; background: ".$this->pj_colors["done"]."'>".t("Valmis")."</span>&nbsp;&nbsp;";
					$prop["value"] .= "<span style='font-size: 11px; padding: 5px; background: ".$this->pj_colors["can_start"]."'>".t("V&otilde;ib alustada")."</span>&nbsp;&nbsp;";
					$prop["value"] .= "<span style='font-size: 11px; padding: 5px; background: ".$this->pj_colors["can_not_start"]."'>".t("Ei saa alustada/t&ouml;&ouml;s")."</span>&nbsp;&nbsp;";
					$prop["value"] .= "<span style='font-size: 11px; padding: 5px; background: ".$this->pj_colors["resource_in_use"]."'>".t("Eeldust&ouml;&ouml; tehtud")."</span>&nbsp;&nbsp;";
					$prop["value"] .= "<span style='font-size: 11px; padding: 5px; background: ".$this->pj_colors["search_result"]."'>".t("Otsingu tulemus")."</span>&nbsp;&nbsp;";
				}
				break;

			case "sp_name":
			case "sp_comment":
			case "sp_customer":
			case "cs_name":
			case "cs_firmajuht":
			case "cs_contact":
			case "cs_phone":
			case "cs_reg_nr":
				if (isset($arr["request"][$prop["name"]]))
				{
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;

			case "sp_starttime":
			case "sp_due_date":
				if (isset($arr["request"][$prop["name"]]))
				{
					$prop["value"] = date_edit::get_timestamp($arr["request"][$prop["name"]]);
				}
				break;

			case "sp_result":
				$this->_sp_result($arr);
				break;

			case "cs_result":
				$this->_cs_result($arr);
				break;

			case "sp_status":
				if (isset($arr["request"][$prop["name"]]))
				{
					$prop["options"] = array(
						MRP_STATUS_DONE => $this->states[MRP_STATUS_DONE],
						MRP_STATUS_ABORTED => $this->states[MRP_STATUS_ABORTED],
						MRP_STATUS_PLANNED => $this->states[MRP_STATUS_PLANNED],
						MRP_STATUS_ARCHIVED => $this->states[MRP_STATUS_ARCHIVED]
					);
					$prop["value"] = $arr["request"][$prop["name"]];
				}
				break;

			case "select_session_resource":
				$resids = $this->get_cur_printer_resources(array(
					"ws" => $arr["obj_inst"],
					"ign_glob" => true
				));
				if (count($resids) === 1)
				{
					$resid = reset($resids);
					if ($resid)
					{
						aw_session_set("mrp_operator_use_resource", $resid);
					}
					else
					{
						aw_session_del("mrp_operator_use_resource");
					}
					// aaaand redirect
					header("Location: ".$this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "branch_id" => "grp_printer_current")));
					die();
				}
				elseif (count($resids) > 0)
				{
					$ol = new object_list(array("oid" => $resids));
				}
				else
				{
					$ol = new object_list();
				}

				$prop["options"] = /*array("" => "") +*/ $ol->names();
				$prop["value"] = aw_global_get("mrp_operator_use_resource");
				break;

			case "chart_search":
				$this->_chart_search($arr);
				break;

			case "printer_jobs_next_link":
				if (!empty($arr["request"]["pj_job"]) || isset($arr["request"]["branch_id"]) && in_array($arr["request"]["branch_id"], array("grp_printer_notstartable", "grp_printer_startable")))
				{
					return PROP_IGNORE;
				}
				$page = isset($arr["request"]["printer_job_page"]) ? (int) $arr["request"]["printer_job_page"] : 0;
				$prop["value"] = html::href(array(
					"url" => aw_url_change_var("printer_job_page", $page+1),
					"caption" => t("J&auml;rgmine lehek&uuml;lg")
				));
				break;

			case "printer_jobs_prev_link":
				if (!empty($arr["request"]["pj_job"]) || isset($arr["request"]["branch_id"]) && in_array($arr["request"]["branch_id"], array("grp_printer_notstartable","grp_printer_startable")))
				{
					return PROP_IGNORE;
				}

				if (empty($arr["request"]["printer_job_page"]))
				{
					return PROP_IGNORE;
				}
				$prop["value"] = html::href(array(
					"url" => aw_url_change_var("printer_job_page", $arr["request"]["printer_job_page"]-1),
					"caption" => t("Eelmine lehek&uuml;lg")
				));
				break;

			### worksheets tab
			case "ws_resource":
				$res_list = $this->get_cur_printer_resources(array("ws" => $arr["obj_inst"]));
				if (count($res_list))
				{
					$ol = new object_list(array(
						"oid" => $res_list,
						"site_id" => array(),
						"lang_id" => array(),
					));
					$prop["options"] = $ol->names();
				}
				$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : null;
				break;

			case "ws_from":
			case "ws_to":
				$prop["value"] = isset($arr["request"][$prop["name"]]) ? date_edit::get_timestamp($arr["request"][$prop["name"]]) : 0;
				break;

			case "ws_tbl":
				$this->_ws_tbl($arr);
				break;

			### persons tab
			case "poo_job_done_only_by":
				$prop["label"] = $prop["caption"];
				if(!is_oid($this->get_hours_person($arr)))
				{
					$retval = PROP_IGNORE;
				}
				break;
		}
		return $retval;
	}

	public function _get_persons_quantity_chart_quantity_by_person_in_month($arr)
	{
		list($from, $to) = $this->get_hours_from_to();
		if($to - $from <= 31*24*3600)
		{
			return PROP_IGNORE;
		}
		return $this->quantity_by_smth_in_time_chart($arr, "m", array("person", "month"));
	}

	public function _get_persons_quantity_chart_quantity_by_person_in_week($arr)
	{
		list($from, $to) = $this->get_hours_from_to();
		if($to - $from <= 7*24*3600)
		{
			return PROP_IGNORE;
		}
		return $this->quantity_by_smth_in_time_chart($arr, "W", array("person", "week"));
	}

	protected function quantity_by_smth_in_time_chart($arr, $type, $groupby = array("person", "month"))
	{
		list($from, $to) = $this->get_hours_from_to();
		$_data = mrp_job_obj::get_progress_for_params(array(
			"from" => $from,
			"to" => $to,
			"person" => $this->get_hours_person($arr),
			"resource" => $this->get_hours_resource($arr),
			"groupby" => $groupby,
		));

		$data = array();
		$weeks = array();
		$months = array();
		$years = array();

		foreach($_data as $pid => $pdata)
		{
			for($y = date("Y", $from); $y <= date("Y", $to); $y++)
			{
				$i_from = $y === date("Y", $from) ? date($type, $from) : 1;
				$i_to = $y === date("Y", $to) ? date($type, $to) : date($type, mktime(0, 0, 0, 12, 31, $y));
				for($i = (int)$i_from; $i <= $i_to; $i++)
				{
					if($i < 10)
					{
						$i = "0".$i;
					}
					$data[$pid][$y."_".$i] = isset($_data[$pid][$y][(int)$i]) ? $_data[$pid][$y][(int)$i] : 0;

					switch($type)
					{
						case "W":
							$weeks[$y."_".$i] = (int)$i;
							break;

						case "m":
							$months[$y."_".$i] = aw_locale::get_lc_month((int)$i);
							break;
					}
				}

				$years[$y] = $y;
			}
			ksort($data[$pid]);
		}

		$c = &$arr["prop"]["vcl_inst"];
		$c->use_cache(false);
		$c->set_type(GCHART_LINE_CHART);
		$c->set_size(array(
			"width" => 800,
			"height" => 200,
		));
		$c->set_grid(array(
			"xstep" => 0,
			"ystep" => 10,
		));
		$c->add_fill(array(
			"area" => GCHART_FILL_BACKGROUND,
			"type" => GCHART_FILL_SOLID,
			"colors" => array(
				"color" => "e9e9e9",
			),
		));

		$ol = new object_list(array(
			"oid" => array_merge(array(-1), array_keys($data)),
			"lang_id" => array(),
			"site_id" => array(),
		));
		$names = $ol->names();

		$legend_labels = array();
		$labels = array();
		$cnt = 0;
		$max = 0;
		foreach($data as $pid => $pdata)
		{
			if(isset($names[$pid]))
			{
				$legend_labels[$pid] = parse_obj_name($names[$pid]);
				$c->add_data($pdata);
				$labels = array_keys($pdata);
			}
			$cnt++;
			$pmax = max($pdata);
			$max = max($max, $pmax);
		}
		$c->set_legend(array(
			"labels" => $legend_labels,
			"position" => GCHART_POSITION_RIGHT,
		));
		$c->set_colors($c->generate_colors($cnt));

		switch($type)
		{
			case "W":
				$c->set_axis(array(
					GCHART_AXIS_LEFT,
					GCHART_AXIS_BOTTOM,
					GCHART_AXIS_BOTTOM,
				));
				$c->add_axis_label(1, $weeks);
				$c->add_axis_label(2, $years);
				break;

			case "m":
				$c->set_axis(array(
					GCHART_AXIS_LEFT,
					GCHART_AXIS_BOTTOM,
					GCHART_AXIS_BOTTOM,
				));
				$c->add_axis_label(1, $months);
				$c->add_axis_label(2, $years);
				break;

			default:
				$c->set_axis(array(
					GCHART_AXIS_LEFT,
					GCHART_AXIS_BOTTOM,
				));
				$c->add_axis_label(1, $years);
				break;
		}
		$c->add_axis_range(0, array(0, $max));
	}

	public function _get_persons_quantity_chart_quantity_by_person($arr)
	{
		return $this->quantity_by_smth_chart($arr, "person");
	}

	public function _get_persons_quantity_chart_quantity_by_resource($arr)
	{
		return $this->quantity_by_smth_chart($arr, "resource");
	}

	public function _get_persons_quantity_chart_quantity_by_case($arr)
	{
		return $this->quantity_by_smth_chart($arr, "case");
	}

	public function _get_persons_quantity_chart_quantity_by_job($arr)
	{
		return $this->quantity_by_smth_chart($arr, "job");
	}

	protected function quantity_by_smth_chart($arr, $groupby = "person")
	{
		list($from, $to) = $this->get_hours_from_to();
		$_data = mrp_job_obj::get_progress_for_params(array(
			"from" => $from,
			"to" => $to,
			"person" => $this->get_hours_person($arr),
			"resource" => $this->get_hours_resource($arr),
			"groupby" => $groupby,
		));
		$ol = new object_list(array(
			"oid" => array_merge(array(-1), array_keys($_data)),
			"lang_id" => array(),
			"site_id" => array(),
		));
		$labels = $ol->names();

		$data = array();
		foreach($labels as $k => $v)
		{
			$data[$k] = $_data[$k];
		}

		$c = &$arr["prop"]["vcl_inst"];
		$c->use_cache(false);
		$c->set_type(GCHART_PIE_3D);
		$c->set_size(array(
			"width" => 400,
			"height" => 120,
		));
		$c->add_fill(array(
			"area" => GCHART_FILL_BACKGROUND,
			"type" => GCHART_FILL_SOLID,
			"colors" => array(
				"color" => "e9e9e9",
			),
		));
		$c->add_data($data);
		$c->set_labels($labels);
	}

	public function _get_charts_clients_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_item(0, array(
			"id" => "all",
			"name" => t("K&otilde;ik"),
			//"url" => aw_url_change_var("clientspan", NULL),
			"reload" => array(
				"layouts" => array("charts_right"),
				"params" => array("clientspan" => NULL),
			),
		));

		$this->create_customers_tree($arr);

		// Hack the reload stuff
		foreach($t->get_item_ids() as $id)
		{
			$item = $t->get_item($id);
			$param = strlen($item["reload"]["params"]["cat"]) ? $item["reload"]["params"]["cat"] : (strlen($item["reload"]["params"]["cust"]) ? $item["reload"]["params"]["cust"] : $item["reload"]["params"]["alph"]);
			$item["reload"] = array(
				"layouts" => array("charts_right"),
				"params" => array("clientspan" => $param),
			);
			$t->set_item($item);
		}

		$clientspan = automatweb::$request->arg("clientspan");

		if($this->can("view", $clientspan))
		{
			$t->set_selected_item($clientspan);
		}
		elseif(!empty($clientspan))
		{
			$t->set_selected_item("alph_".$clientspan);
		}
		else
		{
			$t->set_selected_item("all");
		}
	}

	public function _get_charts_time_tree($arr)
	{
		return $this->_get_time_tree($arr);
	}

	public function _get_poo_quantity_broup_by($arr)
	{
		$arr["prop"]["options"] = array(
			"resource" => t("Ressurss"),
			"case" => t("Projekt"),
			"job" => t("T&ouml;&ouml;"),
		);
	}

	public function _get_poo_quantity_broup_by_resource($arr)
	{
		$prop = &$arr["prop"];
		$prop["style"] = "font-size: 11px; font-weight:bold; color:#FFFFFF; background-color:#05A6E9;";
		$prop["onclick"] = "reload_property('persons_quantity_chart_quantity_by_person', {poo_quantity_broup_by_resource: 1})";
	}

	public function _init_persons_quantity_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_caption(t("T&uuml;kiarvestus"));

		$t->define_field(array(
			"name" => "person",
			"caption" => t("T&ouml;&ouml;taja"),
			"align" => "left"
		));

		$groupby_captions = array(
			"resource" => t("Ressurss"),
			"case" => t("Projekt"),
			"job" => t("T&ouml;&ouml;"),
		);
		foreach(safe_array($arr["obj_inst"]->prop("poo_quantity_broup_by")) as $groupby)
		{
			$t->define_field(array(
				"name" => $groupby,
				"caption" => $groupby_captions[$groupby],
				"align" => "left",
				"sortable" => true,
			));
		}

		$t->define_field(array(
			"name" => "quantity",
			"caption" => t("Eksemplaride arv"),
			"align" => "right"
		));
	}

	protected function persons_quantity_tbl_insert_data($t, $row, $groupby, $lvl, $data)
	{
		if(is_array($data))
		{
			foreach($data as $k => $v)
			{
				$row[$groupby[$lvl]] = obj($k)->name();
				$this->persons_quantity_tbl_insert_data($t, $row, $groupby, $lvl +1, $v);
			}
		}
		else
		{
			$row["quantity"] = $data;
			$t->define_data($row);
		}
	}

	public function _get_persons_quantity_tbl($arr)
	{
		$this->_init_persons_quantity_tbl($arr);
		$t = &$arr["prop"]["vcl_inst"];

		list($from, $to) = $this->get_hours_from_to();
		$groupby = array_values(array_merge(array("person"), safe_array($arr["obj_inst"]->prop("poo_quantity_broup_by"))));
		$data = mrp_job_obj::get_progress_for_params(array(
			"from" => $from,
			"to" => $to,
			"person" => $this->get_hours_person($arr),
			"resource" => $this->get_hours_resource($arr),
			"groupby" => $groupby,
		));

		foreach($data as $person => $data_)
		{
			$this->persons_quantity_tbl_insert_data($t, array("person" => obj($person)->name()), $groupby, 1, $data_);
		}

		$t->set_rgroupby(array(
			"person" => "person",
		));
		$t->set_default_sortby(array_merge(array(
			"person"
		), $groupby));
	}

	public function _get_poo_started_finished_by($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_titlebar_display(false);
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "prsn_hndl_s",
			"caption" => "",
		));
		$t->define_field(array(
			"name" => "prsn_hndl_f",
			"caption" => "",
		));
		$t->define_field(array(
			"name" => "prsn_hndl_s_or_f",
			"caption" => "",
		));
		$t->define_field(array(
			"name" => "prsn_hndl_s_and_f",
			"caption" => "",
		));

		$charts = array(
			"prsn_hndl_s" => array(
				"value" => mrp_job_obj::PRSN_HNDL_S,
				"title" => t("Kasuta iga isiku t&ouml;&ouml;aja arvestuseks ainult neid operatsiooni t&ouml;&ouml;ajafragmente, mille on t&ouml;&ouml;sse/pausile pannud tema ise."),
				"colors" => array(
					"bfd6e8",
					"0099ff",
				),
			),
			"prsn_hndl_f" => array(
				"value" => mrp_job_obj::PRSN_HNDL_F,
				"title" => t("Kasuta iga isiku t&ouml;&ouml;aja arvestuseks ainult neid operatsiooni t&ouml;&ouml;ajafragmente, mille on t&ouml;&ouml;st/pausilt &auml;ra v&otilde;tnud tema ise."),
				"colors" => array(
					"0099ff",
					"bfd6e8",
				),
			),
			"prsn_hndl_s_or_f" => array(
				"value" => mrp_job_obj::PRSN_HNDL_S_OR_F,
				"title" => t("Kasuta iga isiku t&ouml;&ouml;aja arvestuseks k&otilde;iki operatsiooni t&ouml;&ouml;ajafragmente, mille ta on pannud t&ouml;&ouml;sse/pausile v&otilde;i t&ouml;&ouml;st/pausilt &auml;ra v&otilde;tnud."),
				"colors" => array(
					"0099ff",
				),
			),
			"prsn_hndl_s_and_f" => array(
				"value" => mrp_job_obj::PRSN_HNDL_S_AND_F,
				"title" => t("Kasuta iga isiku t&ouml;&ouml;aja arvestuseks ainult neid operatsiooni t&ouml;&ouml;ajafragmente, mille on t&ouml;&ouml;sse/pausile pannud tema ise ja ka t&ouml;&ouml;st/pausilt &auml;ra v&otilde;tnud tema ise."),
				"colors" => array(
					"bfd6e8",
				),
			),
		);
		$data = array();
		foreach($charts as $chart => $chart_prms)
		{
			$c = new google_chart();
			$c->set_type(GCHART_VENN);
			$c->add_data(array(100, 100, 0, 30));
			$c->set_size(array(
				"width" => 50,
				"height" => 50,
			));
			$c->add_fill(array(
				"area" => GCHART_FILL_BACKGROUND,
				"type" => GCHART_FILL_SOLID,
				"colors" => array(
					"color" => $arr["obj_inst"]->prop("poo_started_finished_by") == $chart_prms["value"] ? "9bff79" : "e1e1e1",
				),
			));
			$c->set_colors($chart_prms["colors"]);
			$data[$chart] = html::href(array(
				"url" => aw_url_change_var("poo_started_finished_by", $chart_prms["value"]),
				"caption" => $c->get_html(),
				"title" => $chart_prms["title"],
			));
		}

		$t->define_data($data);
	}

	public function _get_resource_deviation_chart($arr)
	{
		if(isset($arr["request"]["mrp_tree_active_item"]) && $this->can("view", $arr["request"]["mrp_tree_active_item"]) && obj($arr["request"]["mrp_tree_active_item"])->class_id() == CL_MRP_RESOURCE)
		{
			$id = $arr["request"]["mrp_tree_active_item"];

			$odl = new object_data_list(
				array(
					"class_id" => CL_MRP_JOB,
					"resource" => $id,
					"state" => MRP_STATUS_DONE,
					"finished" => new obj_predicate_compare(OBJ_COMP_GREATER, 0),
					new obj_predicate_sort(array("finished" => "ASC")),//!!! miks sort selline on? milleks seda vaja kui ikkag sql lihtsalt kuskil?
				),
				array(
					CL_MRP_JOB => array("length", "finished"),
				)
			);
			if($odl->count() == 0)
			{
				$arr["prop"]["type"] = "text";
				$arr["prop"]["value"] = sprintf(t("Ressursil '%s' ei ole veel &uuml;htegi l&otilde;petatud t&ouml;&ouml;d."), obj($id)->name());
				return PROP_OK;
			}
			$jobs = $odl->arr();

			$real = array();
			$stats = $this->db_fetch_array("SELECT * FROM mrp_stats WHERE job_oid IN (".implode(",", array_keys($odl->arr())).") ORDER BY end DESC");
			foreach($stats as $stat)
			{
				if((int)$stat["end"] === 0)
				{
					continue;
				}
				$real[$stat["job_oid"]] = isset($real[$stat["job_oid"]]) ? $real[$stat["job_oid"]] + $stat["length"] : $stat["length"];
				$end[$stat["job_oid"]] = max($stat["end"], isset($end[$stat["job_oid"]]) ? $end[$stat["job_oid"]] : 0);
			}
			$end = array_slice($end, 0, 200, true);
			$min_end = min($end);
			$max_end = max($end);

			foreach($jobs as $oid => $job)
			{
				if(!isset($real[$oid]) || !isset($end[$oid]))
				{
					continue;
				}
				$real[$oid] = isset($real[$oid]) ? $real[$oid] : 0;
				$end[$oid] = $job["finished"];

				$d = $job["length"] != 0 ? ($real[$oid] - $job["length"]) / $job["length"] : 0;
				$data_x[$oid] = ($end[$oid] - $min_end) / ($max_end - $min_end) * 100;
				$data_y[$oid] = $d;
			}
			$min_d = min($data_y);
			$max_d = max($data_y);

			for($i = $min_end; $i < $max_end + 30*24*3600; $i += 30*24*3600)
			{
				$months[date("F", $i)] = date("F", $i);
				$years[date("Y", $i)] = date("Y", $i);
			}

			$c = &$arr["prop"]["vcl_inst"];
			$c->set_type(GCHART_LINE_CHARTXY);
			$c->set_size(array(
				"width" => 800,
				"height" => 200,
			));
			$c->add_fill(array(
				"area" => GCHART_FILL_BACKGROUND,
				"type" => GCHART_FILL_SOLID,
				"colors" => array(
					"color" => "e9e9e9",
				),
			));
			$c->add_data(array(0,100));
			$c->add_data(array(0,0));
			$c->add_data($data_x);
			$c->add_data($data_y);
			$c->set_colors(array(
				strtolower(preg_replace("/[^0-9A-Za-z]/", "", MRP_COLOUR_PLANNED)),
				"0099ff",
			));
			$c->set_grid(array(
				"xstep" => 0,
				"ystep" => 20,
			));
			$abs_deviation_max = max(abs($min_d), $max_d);
			$range = array(round($abs_deviation_max * -1.25, 1), round($abs_deviation_max * 1.25, 1));
			$c->set_data_scales(array(
				array(0,100),
				$range,
				array(0,100),
				$range,
			));
			$c->set_axis(array(
				GCHART_AXIS_LEFT,
				GCHART_AXIS_BOTTOM,
				GCHART_AXIS_BOTTOM,
			));
			$c->add_axis_range(0, $range);
			$c->add_axis_label(1, $months);
			$c->add_axis_label(2, $years);
			$c->set_legend(array(
				"labels" => array(
					t("Planeeritud kestus"),
					t("Tegeliku t&ouml;&ouml;aja suhteline h&auml;lve v&otilde;rreldes planeerituga. Negatiivne h&auml;lve v&auml;ljendab alakulu, positiivne h&auml;lve &uuml;lekulu."),
				),
				"position" => GCHART_POSITION_BOTTOM,
			));
		}
		else
		{
			return PROP_IGNORE;
		}
	}

	public function _get_persons_jobs_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->set_caption("Tehtud t&ouml;&ouml;d");

		// Init
		$t->define_field(array(
			"name" => "name",
			"caption" => t("T&ouml;&ouml;"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "project",
			"caption" => t("Projekt"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "resource",
			"caption" => t("Ressurss"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "persons",
			"caption" => t("Inimesed"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "hours",
			"caption" => t("T&ouml;&ouml;aeg"),
		));
			$t->define_field(array(
				"name" => "inprogress",
				"caption" => t("Tegelik"),
				"align" => "right",
				"sortable" => true,
				"sorting_field" => "inprogress_int",
				"parent" => "hours",
			));
			$t->define_field(array(
				"name" => "length",
				"caption" => t("Planeeritud"),
				"align" => "right",
				"sortable" => true,
				"sorting_field" => "length_int",
				"parent" => "hours",
			));
		$t->define_field(array(
			"name" => "deviation",
			"caption" => t("H&auml;lve"),
		));
			$t->define_field(array(
				"name" => "abs_deviation",
				"caption" => t("Absoluutne"),
				"align" => "right",
				"parent" => "deviation",
				"sortable" => true,
				"sorting_field" => "abs_deviation_int",
			));
			$t->define_field(array(
				"name" => "rel_deviation",
				"caption" => t("Suhteline"),
				"align" => "right",
				"parent" => "deviation",
				"sortable" => true,
				"sorting_field" => "rel_deviation_int",
			));

		$t->set_numeric_field(array("length_int", "paused_int", "inprogress_int", "rel_deviation_int", "abs_deviation_int"));

		// Data
		list($from, $to) = $this->get_hours_from_to();
		$odl = new object_data_list(
			array(
				"class_id" => CL_MRP_JOB,
				"person.oid" => $this->get_hours_person($arr),
				"finished" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $from, $to, "int"),
			),
			array(
				CL_MRP_JOB => array(
					"person",
					"length",
					"length_deviation",
					"real_length",
					"resource" => "resource_id",
					"resource(CL_MRP_RESOURCE).name" => "resource",
					"project" => "project_id",
					"project(CL_MRP_CASE).name" => "project",
					"project(CL_MRP_CASE).customer" => "customer_id",
					"project(CL_MRP_CASE).customer.name" => "customer",
				),
			)
		);

		// Get all the person names
		$pids = array(-1);
		foreach($odl->get_element_from_all("person") as $e)
		{
			$pids = array_merge($pids, (array)$e);
		}
		$names_ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"oid" => $pids,
		));
		$names = $names_ol->names();

		foreach($odl->arr() as $o)
		{
			$o["person"] = (array)$o["person"];
			if(is_oid($this->get_hours_person($arr)) && $arr["obj_inst"]->prop("poo_job_done_only_by") && (count($o["person"]) > 1 || reset($o["person"]) != $this->get_hours_person($arr)))
			{
				continue;
			}

			$persons = array();
			foreach($o["person"] as $pid)
			{
				if(isset($names[$pid]))
				{
					$persons[$pid] = parse_obj_name($names[$pid]);
				}
			}
			sort($persons);
			$t->define_data(array(
				"name" => $o["name"],
				"project" => $o["project"],
				"customer" => $o["customer"],
				"resource" => $o["resource"],
				"persons" => implode(", ", $persons),
				"inprogress" => $this->format_hours($o["real_length"]/3600),
				"inprogress_int" => $o["real_length"],
				"length" => $this->format_hours($o["length"]/3600),
				"length_int" => $o["length"],
				"abs_deviation" => $this->format_hours($o["length_deviation"]/3600),
				"abs_deviation_int" => $o["length_deviation"],
				"rel_deviation" => sprintf("%s%%", $o["length"] > 0 ? number_format($o["length_deviation"]/$o["length"]*100, 2) : ($o["real_length"] > 0 ? "&#8734;" : "0")),
				"rel_deviation_int" => $o["length"] > 0 ? $o["length_deviation"]/$o["length"] : ($o["real_length"] > 0 ? (int)sprintf("%u", -1) : 0),
			));
		}
	}

	public function _get_persons_hours_chart($arr)
	{
		return $this->_get_hours_chart($arr, "person");
	}

	public function _get_resources_hours_chart($arr)
	{
		return $this->_get_hours_chart($arr, "resource");
	}

	private function _get_hours_chart($arr, $kf = "person")
	{
		$data = $this->get_hours($arr, $kf);

		if(count($data[MRP_STATUS_INPROGRESS]) === 0 && count($data[MRP_STATUS_PAUSED]) === 0)
		{
			return PROP_IGNORE;
		}

		$arr["prop"]["value"] = "";
		switch($kf)
		{
			case "resource":
				$perrow = 4;
				$colors = array(
					"ffffff",
					strtolower(preg_replace("/[^0-9A-Za-z]/", "", self::$state_colours[MRP_STATUS_PLANNED])),
					strtolower(preg_replace("/[^0-9A-Za-z]/", "", self::$state_colours[MRP_STATUS_INPROGRESS])),
					strtolower(preg_replace("/[^0-9A-Za-z]/", "", self::$state_colours[MRP_STATUS_PAUSED])),
				);
				$labels = array(
					t("Ressursi kogu vaba aeg (h)"),
					t("Planeeritud t&ouml;&ouml;aeg (h)"),
					t("Efektiivne t&ouml;&ouml;aeg (h)"),
					t("Paus (h)"),
				);
				$bar_sizes = array(
					"width" => 35,
					"bar_spacing" => 2,
					"bar_group_spacing" => 34,
				);
				$max_all = max(array_merge($data[MRP_STATUS_INPROGRESS], $data[MRP_STATUS_PAUSED], $data[MRP_STATUS_PLANNED], $data["available"]));
				break;

			default:
				$perrow = 8;
				$colors = array(
					strtolower(preg_replace("/[^0-9A-Za-z]/", "", self::$state_colours[MRP_STATUS_INPROGRESS])),
					strtolower(preg_replace("/[^0-9A-Za-z]/", "", self::$state_colours[MRP_STATUS_PAUSED])),
				);
				$labels = array(
					t("Efektiivne t&ouml;&ouml;aeg (h)"),
					t("Paus (h)"),
				);
				$bar_sizes = array(
					"width" => 35,
					"bar_spacing" => 5,
					"bar_group_spacing" => 15,
				);
				$max_all = max(array_merge($data[MRP_STATUS_INPROGRESS], $data[MRP_STATUS_PAUSED]));
				break;
		}
		for($i = 0; $i < count($data["name"]); $i += $perrow)
		{
			$c = new google_chart();
			$c->set_type(GCHART_BAR_GV);
			$c->set_size(array(
				"width" => 950,
				"height" => 200,
			));
			$c->set_bar_sizes($bar_sizes);
			$c->add_fill(array(
				"area" => GCHART_FILL_BACKGROUND,
				"type" => GCHART_FILL_SOLID,
				"colors" => array(
					"color" => "e9e9e9",
				),
			));
			$data_1 = array_slice($data[MRP_STATUS_INPROGRESS], $i, $perrow);
			$data_2 = array_slice($data[MRP_STATUS_PAUSED], $i, $perrow);
			$max = max(array_merge($data_1, $data_2));
			if($kf == "resource")
			{
				$data_3 = array_slice($data["available"], $i, $perrow);
				$data_4 = array_slice($data[MRP_STATUS_PLANNED], $i, $perrow);
				$c->add_data($data_3);
				$c->add_data($data_4);
				$max = max(array_merge($data_1, $data_2, $data_3, $data_4));
			}
			$c->add_data($data_1);
			$c->add_data($data_2);
			$c->set_colors($colors);
			$c->set_legend(array(
				"labels" => $labels,
				"position" => GCHART_POSITION_RIGHT,
			));
			$c->set_axis(array(
				GCHART_AXIS_BOTTOM,
				GCHART_AXIS_LEFT,
			));
			$c->add_axis_label(0, array_slice($data["name"], $i, $perrow));
			$c->add_axis_range(1, array(0, $max_all));
			$c->set_data_scales(array(array(0, $max_all*100/$max)));
			$arr["prop"]["value"] .= $c->get_html()."<br />";
		}
	}

	public function _get_resource_time_chart($arr)
	{
		if(isset($arr["request"]["mrp_tree_active_item"]) && $this->can("view", $arr["request"]["mrp_tree_active_item"]))
		{
			$resources_folder = $arr["request"]["mrp_tree_active_item"];
		}
		else
		{
			$resources_folder = $arr["obj_inst"]->prop("resources_folder");
		}

		$resource_tree_filter = array(
			"parent" => $resources_folder,
			"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
			"sort_by" => "objects.jrk",
		);
		if($arr["request"]["group"] == "my_resources")
		{
			$resource_tree_filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_MRP_RESOURCE.oid" => $this->get_my_resources(),
					"class_id" => CL_MENU,
				)
			));
		}

		$resource_tree = new object_tree($resource_tree_filter);
		$ids = $resource_tree->ids();
		$ids[] = $resources_folder;

		$start = $arr["obj_inst"]->resource_time_start;
		$end = $arr["obj_inst"]->resource_time_end;
		$start = is_numeric($start) ? $start : mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
		$end = is_numeric($end) ? ($end + 24*3600 -1) : time();
		$span = $end - $start;

		$res = array();
		$inst = get_instance("mrp_resource");
		foreach(array_merge($resource_tree->to_list()->arr(), array(obj($resources_folder))) as $o)
		{
			if($o->class_id() == CL_MRP_RESOURCE)
			{
				$res["free"][$o->id()] = 100;
				$res["name"][$o->id()] = $o->name();
				$res["unavail"][$o->id()] = 0;
				$res["fake"][$o->id()] = 0;
				$res["real"][$o->id()] = 0;
				$res["plan"][$o->id()] = 0;
				$res["paus"][$o->id()] = 0;
				foreach($inst->get_unavailable_periods($o, $start, $end) as $from => $to)
				{
					$res["unavail"][$o->id()] += max(0, $to - $from);
				}
				foreach($inst->get_recurrent_unavailable_periods($o, $start, $end) as $period)
				{
					for($g = $period["start"] + $period["time"]; $g < $period["end"]; $g += $period["interval"])
					{
						$u = $g + $period["length"] > $period["end"] ? $period["end"] - $g : $period["length"];
						$res["unavail"][$o->id()] += max(0, $u);
					}
				}
			}
		}

		$odl = new object_data_list(
			array(
				"class_id" => CL_MRP_JOB,
				"resource" => $ids,
				"state" => mrp_job_obj::STATE_DONE,
				"finished" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end + 24*3600 - 1, "int"),
			),
			array(
				CL_MRP_JOB => array("real_length", "length", "started", "finished", "resource"),
			)
		);

		$o_datas = $odl->arr();

		$data = mrp_job_obj::get_resource_hours(array(
			"from" => $start,
			"to" => $end,
			"state" => array(MRP_STATUS_INPROGRESS, MRP_STATUS_PAUSED),
			"resource" => $ids,
			"job" => array_keys($o_datas),
			"convert_to_hours" => false,
		));

		$res["real"] = $data[MRP_STATUS_INPROGRESS] + safe_array($res["real"]);
		$res["paus"] = $data[MRP_STATUS_PAUSED] + safe_array($res["paus"]);

		foreach($o_datas as $oid => $o)
		{
			$k = $o["resource"];
			$plan = $o["real_length"] ? $o["length"] * $res["real"][$k] / $o["real_length"] : 0;

			$res["plan"][$k] = (int) (isset($res["plan"][$k]) ? $res["plan"][$k] + $plan : $plan);
		}
		foreach($res["free"] as $k => $v)
		{
			$res["real"][$k] = round($res["real"][$k] / $span * 100, 2);
			$res["plan"][$k] = round($res["plan"][$k] / $span * 100, 2);
			$res["paus"][$k] = round($res["paus"][$k] / $span * 100, 2);
			$res["unavail"][$k] = round($res["unavail"][$k] / $span * 100, 2);

			$res["over_plan"][$k] = max(0, $res["plan"][$k] - $res["real"][$k]);
			$res["under_plan"][$k] = max(0, $res["real"][$k] - $res["plan"][$k]);

			$res["real"][$k] -= $res["under_plan"][$k];
			$res["plan"][$k] -= $res["over_plan"][$k];

			$res["free"][$k] -= $res["real"][$k] + $res["paus"][$k] + $res["over_plan"][$k] + $res["under_plan"][$k] + $res["unavail"][$k];
		}

		//	Stupid
		ksort($res["unavail"]);
		ksort($res["free"]);
		ksort($res["real"]);
		ksort($res["over_plan"]);
		ksort($res["under_plan"]);
		ksort($res["paus"]);
		ksort($res["name"]);
		ksort($res["plan"]);

		$cnt = count($res["fake"]);
		if($cnt <= 13)
		{
			$c = $arr["prop"]["vcl_inst"];
			$c->set_type(GCHART_BAR_H);
			$c->set_size(array(
				"width" => 800,
				"height" => 27 * $cnt + 18,
			));
			$c->add_fill(array(
				"area" => GCHART_FILL_BACKGROUND,
				"type" => GCHART_FILL_SOLID,
				"colors" => array(
					"color" => "e9e9e9",
				),
			));
			$c->add_data(array_merge(array(100), $res["fake"]));
			$c->add_data(array_merge(array(0), $res["unavail"]));
			$c->add_data(array_merge(array(0), $res["free"]));
			$c->add_data(array_merge(array(0), $res["real"]));
			$c->add_data(array_merge(array(0), $res["over_plan"]));
			$c->add_data(array_merge(array(0), $res["under_plan"]));
			$c->add_data(array_merge(array(0), $res["paus"]));
			$c->set_colors(array(
				"e9e9e9",
				strtolower(preg_replace("/[^0-9A-Za-z]/", "", MRP_COLOUR_UNAVAILABLE)),
				"ffffff",
				strtolower(preg_replace("/[^0-9A-Za-z]/", "", MRP_COLOUR_INPROGRESS)),
				"00ff00",
				"ff0000",
				strtolower(preg_replace("/[^0-9A-Za-z]/", "", MRP_COLOUR_PAUSED)),
			));
			$c->set_axis(array(
				GCHART_AXIS_LEFT,
			));
			$c->add_axis_label(0, array_reverse($res["name"]));
			$c->set_legend(array(
				"labels" => array(
					t(""),
					t("Kinnine aeg"),
					t("Vaba aeg"),
					t("Tegelik t&ouml;&ouml;aeg"),
					t("Alakulu"),
					t("&Uuml;lekulu"),
					t("Paus"),
				),
				"position" => GCHART_POSITION_BOTTOM,
			));
		}
		else
		{
			$arr["prop"]["type"] = "text";
			$arr["prop"]["value"] = t("Liiga palju ressursse!");
		}
	}

	function _get_pauses_by_resource_chart($arr)
	{
		return $this->_get_something_by_resource_chart($arr, MRP_STATUS_PAUSED);
	}

	function _get_something_by_resource_chart($arr, $state)
	{
		list($from, $to) = $this->get_hours_from_to();
		if($arr["request"]["group"] == "my_stats")
		{
			$me = get_current_person();
			$persons = (array)$me->id();
		}
		else
		{
			$persons = (array)$this->get_hours_person($arr);
		}

		switch($arr["obj_inst"]->prop("poo_started_finished_by"))
		{
			default:
			case mrp_job_obj::PRSN_HNDL_S:
				$pid_query = count($persons) > 0 ? "aw_previous_pid IN (".implode(",", $persons).") AND" : "";
				break;

			case mrp_job_obj::PRSN_HNDL_F:
				$pid_query = count($persons) > 0 ? "aw_pid IN (".implode(",", $persons).") AND" : "";
				break;

			case mrp_job_obj::PRSN_HNDL_S_AND_F:
				$pid_query = count($persons) > 0 ? "aw_pid IN (".implode(",", $persons).") AND aw_pid = aw_previous_pid AND" : "";
				break;

			case mrp_job_obj::PRSN_HNDL_S_OR_F:
				$pid_query = count($persons) > 0 ? "(aw_pid IN (".implode(",", $persons).") OR aw_previous_pid IN (".implode(",", $persons).")) AND" : "";
				break;
		}

		$res = $this->db_fetch_array("
			SELECT
				aw_resource_id as res,
				SUM(LEAST(aw_tm - $from, aw_job_last_duration, $to - aw_tm + aw_job_last_duration)) as sum
			FROM
				mrp_job_rows
			WHERE
				$pid_query
				aw_job_previous_state = $state AND
				(aw_tm BETWEEN $from AND $to OR aw_tm - aw_job_last_duration < $to AND aw_tm > $from)
			GROUP BY
				aw_resource_id
		");
		$ids = array();
		foreach($res as $row)
		{
			$ids[] = $row["res"];
		}
		if(count($ids) > 0)
		{
			$ol = new object_list(array(
				"class_id" => CL_MRP_RESOURCE,
				"oid" => $ids,
				"lang_id" => array(),
				"site_id" => array(),
			));
			$names = $ol->names();
			foreach($res as $row)
			{
				if(!isset($names[$row["res"]]))
				{
					continue;
				}
				$data[$row["res"]] = $row["sum"];
				$labels[$row["res"]] = sprintf(t("%s (%s)"), $names[$row["res"]], $this->format_hours($row["sum"]/3600));
			}
			$colors = array();
			for($i = 15; $i > 0; $i -= 3)
			{
				$colors[] = "aaaa".str_repeat(dechex($i), 2);
				$colors[] = "bb".str_repeat(dechex($i), 2)."bb";
				$colors[] = str_repeat(dechex($i), 2)."cccc";
				$colors[] = "fb".str_repeat(dechex($i), 2)."bf";
				$colors[] = "affa".str_repeat(dechex($i), 2);
				$colors[] = str_repeat(dechex($i), 2)."ceec";
			}

			$c = &$arr["prop"]["vcl_inst"];
			$c->set_type(GCHART_PIE_3D);
			$c->set_size(array(
				"width" => 400,
				"height" => 120,
			));
			$c->add_fill(array(
				"area" => GCHART_FILL_BACKGROUND,
				"type" => GCHART_FILL_SOLID,
				"colors" => array(
					"color" => "e9e9e9",
				),
			));
			$c->set_colors($colors);
			$c->add_data($data);
			$c->set_labels($labels);
		}
		else
		{
			$arr["prop"]["type"] = "text";
			$type = $state === MRP_STATUS_INPROGRESS ? t("efektiivset t&ouml;&ouml;tundi") : t("pausi");
			$arr["prop"]["value"] = sprintf(t("Valitud ajavahemikus ei ole &uuml;htegi %s."), $type);
		}
	}

	function _get_hours_by_resource_chart($arr)
	{
		return $this->_get_something_by_resource_chart($arr, MRP_STATUS_INPROGRESS);
	}

	private function get_hours_from_to()
	{
		$from = 0;
		$to = time();
		if(isset($_GET["timespan"]))
		{
			if(substr(automatweb::$request->arg("timespan"), 0, 5) === "date_")
			{
				list($Y, $M, $D) = array_merge(explode("_", substr(automatweb::$request->arg("timespan"), 5)), array(NULL, NULL));
				if($M === NULL)
				{
					$from = mktime(0, 0, 0, 1, 1, $Y);
					$to = mktime(23, 59, 59, 12, 31, $Y);
				}
				elseif($D === NULL)
				{
					$from = mktime(0, 0, 0, $M, 1, $Y);
					$to = mktime(23, 59, 59, $M+1, 0, $Y);
				}
				else
				{
					$from = mktime(0, 0, 0, $M, $D, $Y);
					$to = mktime(23, 59, 59, $M, $D, $Y);
				}
			}
			else
			{
				switch($_GET["timespan"])
				{
					case "current_week":
						list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
						$from = mktime(0, 0, 0, $M, $D-$N+1, $Y);
						$to = mktime(23, 59, 59, $M, $D+7-$N, $Y);
						break;

					case "last_week":
						list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
						$from = mktime(0, 0, 0, $M, $D-$N-6, $Y);
						$to = mktime(23, 59, 59, $M, $D-$N, $Y);
						break;

					case "current_month":
						list($Y, $M) = explode("-", date("Y-n"));
						$from = mktime(0, 0, 0, $M, 1, $Y);
						$to = mktime(23, 59, 59, $M+1, 0, $Y);
						break;

					case "last_month":
						list($Y, $M) = explode("-", date("Y-n"));
						$from = mktime(0, 0, 0, $M-1, 1, $Y);
						$to = mktime(23, 59, 59, $M, 0, $Y);
						break;
				}
			}
		}
		return array($from, $to);
	}

	private function get_hours_person($arr)
	{
		if(!isset($this->hours_person))
		{
			if(isset($_GET["cat"]) && $this->can("view", $_GET["cat"]))
			{
				$p = obj($_GET["cat"]);
				if($p->class_id() == CL_CRM_PERSON)
				{
					return $_GET["cat"];
				}
			}
			if(isset($_GET["unit"]))
			{
				$o = obj($arr["obj_inst"]->owner);
				$o->set_prop("use_only_wr_workers", 1);
				return array_merge($o->get_workers(array(
					"profession" => isset($_GET["cat"]) ? $_GET["cat"] : array(),
					"section" => $_GET["unit"],
				))->ids(), array(-1));
			}
			$o = obj($arr["obj_inst"]->prop("owner"));
			$this->hours_person = array_merge(($o->class_id() == CL_CRM_COMPANY ? $o->get_workers()->ids() : array()), array(-1));
		}
		return $this->hours_person;
	}

	private function get_hours_resource($arr)
	{
		if(!isset($this->hours_resource))
		{
			if(isset($arr["request"]["resource_span"]) && $this->can("view", $arr["request"]["resource_span"]))
			{
				$p = obj($arr["request"]["resource_span"]);
				if($p->class_id() == CL_MRP_RESOURCE)
				{
					return $arr["request"]["resource_span"];
				}
			}
			if(isset($arr["request"]["resource_span"]) and $this->can("view", $arr["request"]["resource_span"]))
			{
				$parent = $arr["request"]["resource_span"];
			}
			else
			{
				$parent = $arr["obj_inst"]->prop("resources_folder");
			}
			$object_list = new object_tree(array(
				"class_id" => array(CL_MRP_RESOURCE, CL_MENU),
				"parent" => $parent,
			));

			$this->hours_resource = array_merge($object_list->ids(), array(-1));
		}
		return $this->hours_resource;
	}

	private function get_hours_by_job($arr, $kf = "person")
	{
		list($from, $to) = $this->get_hours_from_to();
		$data_prms = array(
			"from" => $from,
			"to" => $to,
			"by_job" => true,
			"state" => array(MRP_STATUS_INPROGRESS, MRP_STATUS_PAUSED),
		);
		switch($kf)
		{
			case "person":
				$data_prms["person"] = isset($arr["request"]["person_show_jobs"]) ? $arr["request"]["person_show_jobs"] : $this->get_hours_person($arr);
				$data_prms["person_handling"] = $arr["obj_inst"]->prop("poo_started_finished_by");
				$data = mrp_job_obj::get_person_hours($data_prms);
				$clid = CL_CRM_PERSON;
				break;

			case "resource":
				$data_prms["resource"] = $this->get_hours_resource($arr);
				$data = mrp_job_obj::get_resource_hours($data_prms);
				$clid = CL_MRP_RESOURCE;
				break;
		}

		// Gather job OIDs
		$ids = array(-1);
		foreach($data[MRP_STATUS_INPROGRESS] as $pid => $jobs)
		{
			$ids = array_merge($ids, array_keys($jobs));
		}
		$odl = new object_data_list(
			array(
				"class_id" => CL_MRP_JOB,
				"oid" => $ids
			),
			array(
				CL_MRP_JOB => array(
					"resource" => "resource_id",
					"resource(CL_MRP_RESOURCE).name" => "resource",
					"project" => "project_id",
					"project(CL_MRP_CASE).name" => "project",
					"project(CL_MRP_CASE).customer" => "customer_id",
					"project(CL_MRP_CASE).customer.name" => "customer",
				),
			)
		);

		$job_data = $odl->arr();

		$ret = array();
		// Hetkel eeldab, et t88d on ainult yhe inimese jaoks.
		foreach($data[MRP_STATUS_INPROGRESS] as $pid => $jobs)
		{
			foreach(array_keys($jobs) as $job_id)
			{
				$ret[$job_id] = array(
					"project" => $job_data[$job_id]["project"],
					"customer" => $job_data[$job_id]["customer"],
					"name" => $job_data[$job_id]["name"],
					"resource" => $job_data[$job_id]["resource"],
					"paused" => $this->format_hours($data[MRP_STATUS_PAUSED][$pid][$job_id]),
					"inprogress" => $this->format_hours($data[MRP_STATUS_INPROGRESS][$pid][$job_id]),
				);
			}
		}

		return $ret;
	}

	private function get_hours($arr, $kf = "person")
	{
		if(!isset($this->hours[$kf]))
		{
			list($from, $to) = $this->get_hours_from_to();
			$data_prms = array(
				"from" => $from,
				"to" => $to,
				"state" => array(MRP_STATUS_INPROGRESS, MRP_STATUS_PAUSED),
				"count" => true,
				"average" => true,
			);
			switch($kf)
			{
				case "person":
					if($arr["request"]["group"] == "my_stats")
					{
						$me = get_current_person();
						$data_prms["person"] = (array)$me->id();
					}
					else
					{
						$data_prms["person"] = $this->get_hours_person($arr);
					}
					$data_prms["person_handling"] = $arr["obj_inst"]->prop("poo_started_finished_by");
					$data = mrp_job_obj::get_person_hours($data_prms);
					$clid = CL_CRM_PERSON;
					break;

				case "resource":
					$data_prms["resource"] = $this->get_hours_resource($arr);
					$data = mrp_job_obj::get_resource_hours($data_prms);
					$clid = CL_MRP_RESOURCE;

					// Additionnal data
					$planned = mrp_resource_obj::get_planned_hours(array(
						"from" => $from,
						"to" => $to,
						"id" => $data_prms["resource"],
					));
					break;
			}
			$data["name"] = array();

			$ids = array_keys($data[MRP_STATUS_PAUSED]);
			if(count($ids) > 0)
			{
				$ol = new object_list(array(
					"class_id" => $clid,
					"oid" => $ids,
					"lang_id" => array(),
					"site_id" => array(),
				));
				$pn = $ol->names();
			}
			foreach(array_keys($data[MRP_STATUS_PAUSED]) as $id)
			{
				if(!isset($pn[$id]))
				{
					unset($data[MRP_STATUS_PAUSED][$id]);
					unset($data[MRP_STATUS_INPROGRESS][$id]);
				}
				else
				{
					$data["name"][$id] = $pn[$id];
					if($kf == "resource")
					{
						$data["available"][$id] = obj($id)->get_available_hours(array("from" => $from, "to" => $to))/3600;
						$data[MRP_STATUS_PLANNED][$id] = ((int)ifset($planned, $id))/3600;
					}
				}
			}
			$this->hours[$kf] = $data;
		}
		return $this->hours[$kf];
	}

	function _get_grp_material_tree($arr)
	{
//		$c = $arr["obj_inst"]->prop("owner");
//		$o = obj($c);

		$t = &$arr["prop"]["vcl_inst"];

		if(!isset($arr["request"]["material"]))
		{
			$arr["request"]["material"] = "all_materials";
		}

		$t->set_selected_item($arr["request"]["material"]);
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "grp_material_tree",
		));

		$t->add_item(0, array(
			"id" => "all_materials",
			"name" => t("K&otilde;ik materjalid"),
		));

		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT_CATEGORY,
			"lang_id" => array(),
			"site_id" => array(),
	//		"parent.parent" =>  $arr["obj_inst"] -> get_warehouses() -> ids(),
	//		"parent.class_id" =>  new obj_predicate_not(CL_SHOP_PRODUCT_CATEGORY),
		));
		foreach($ol->arr() as $o)
		{
			if($o->prop("parent.class_id") == CL_SHOP_PRODUCT_CATEGORY)
			{
				continue;
			}
			$id = $o->id();
			$name = $o->name();
			$t->add_item("all_materials", array(
				"id" => $id,
				"name" => $name,
				"url" => "javascript:$('[name=material]').val('".$id."');update_material_table();",
				"iconurl" => icons::get_icon_url(CL_MENU),
			));
			$this->add_cat_leaf($t , $id);
		}
	}

	function add_cat_leaf($tv , $parent)
	{
		if(!is_oid($parent))
		{
			return;
		}
		$ol = new object_list(array(
			"class_id" => array(CL_SHOP_PRODUCT_CATEGORY),
			"site_id" => array(),
			"lang_id" => array(),
			"parent" => $parent,
		));

		foreach($ol->names() as $id => $name)
		{
			$tv->add_item($parent,array(
				"name" => $name,
				"id" => $id."",
				"url" => "javascript:$('[name=material]').val('".$id."'); update_material_table();",
				"iconurl" => icons::get_icon_url(CL_MENU),
			));
			$this->add_cat_leaf($tv , $id);
		}

		$products = new object_list(array(
			"class_id" => array(CL_SHOP_PRODUCT),
			"site_id" => array(),
			"lang_id" => array(),
			"CL_SHOP_PRODUCT.RELTYPE_CATEGORY" => $parent,
		));
		foreach($products->names() as $id => $name)
		{
			$tv->add_item($parent,array(
				"name" => $name,
				"id" => $id."",
				"url" => "javascript:$('[name=material]').val('".$id."'); update_material_table();",
				"iconurl" => icons::get_icon_url(CL_SHOP_PRODUCT),
			));
		}
	}

	function _get_grp_material_personnel_tree($arr)
	{
		$c = $arr["obj_inst"]->prop("owner");
		$o = obj($c);

		$i = get_instance(CL_CRM_COMPANY);
		$i->active_node = empty($arr["request"]["cat"]) ? (empty($arr["request"]["unit"]) ? $c : $arr["request"]["unit"]) : $arr["request"]["cat"];

		$t = &$arr["prop"]["vcl_inst"];
		if(empty($_GET["unit"]))
		{
			$t->set_selected_item($c);
		}

		$t->add_item(0, array(
			"id" => $c,
			"name" => $o->name(),
			"url" => aw_url_change_var(array(
				"unit" => NULL,
				"cat" => NULL,
			)),
		));
		$i->generate_tree(array(
			"tree_inst" => &$t,
			"obj_inst" => $o,
			"node_id" => $c,
			"conn_type" => "RELTYPE_SECTION",
			"attrib" => "unit",
			"leafs" => true,
			"show_people" => true,
			"url" => aw_ini_get("baseurl").aw_url_change_var(array(
				"person_show_jobs" => NULL,
			))
		));

		// Hack the tree with reload stuff
		foreach($t->get_item_ids() as $id)
		{
			$item = $t->get_item($id);
			$item["url"] = "javascript:$('[name=people]').val('".$id."'); update_material_table();";
			$t->set_item($item);
		}
	}

	function _get_persons_personnel_tree($arr)
	{
		$c = $arr["obj_inst"]->prop("owner");
		$o = obj($c);

		$i = get_instance(CL_CRM_COMPANY);
		$i->active_node = empty($arr["request"]["cat"]) ? (empty($arr["request"]["unit"]) ? $c : $arr["request"]["unit"]) : $arr["request"]["cat"];

		$t = &$arr["prop"]["vcl_inst"];
		if(empty($_GET["unit"]))
		{
			$t->set_selected_item($c);
		}

		$t->add_item(0, array(
			"id" => $c,
			"name" => $o->name(),
			"url" => aw_url_change_var(array(
				"unit" => NULL,
				"cat" => NULL,
			)),
		));
		$i->generate_tree(array(
			"tree_inst" => &$t,
			"obj_inst" => $o,
			"node_id" => $c,
			"conn_type" => "RELTYPE_SECTION",
			"attrib" => "unit",
			"leafs" => true,
			"show_people" => true,
			"url" => aw_ini_get("baseurl").aw_url_change_var(array(
				"person_show_jobs" => NULL,
			))
		));

		// Hack the tree with reload stuff
		foreach($t->get_item_ids() as $id)
		{
			$item = $t->get_item($id);
			$url = new aw_uri($item["url"]);
			$item["reload"] = array(
				"layouts" => array("grp_persons_right", "resources_hours_right"),
				"props" => array("material_stats_table"),
				"params" => array(
					"unit" => $url->arg("unit"),
					"cat" => $url->arg("cat")
				),
			);
			unset($item["url"]);
			$t->set_item($item);
		}
	}

	public function _get_grp_material_resource_span_tree($arr)
	{
		$this_object = $arr["obj_inst"];
		$applicable_states = array(
			MRP_STATUS_RESOURCE_INACTIVE,
		);

		### resource tree
		$resources_folder = $this_object->prop ("resources_folder");

		if($arr["request"]["group"] == "my_resources" || $arr["request"]["group"] == "grp_printer_general")
		{
			$resids = $this->get_cur_printer_resources(array(
				"ws" => $arr["obj_inst"],
				"ign_glob" => true
			));
			$res_ol = new object_list();
			if (count($resids))
			{
				$res_ol = new object_list(array("oid" => $resids,"sort_by" => "objects.name"));
			}

			$this->resources_folder = $resources_folder;
			$this->my_resources_menus = array();
			foreach($res_ol->arr() as $res_o)
			{
				$this->my_resources_menus[$res_o->parent()] = $res_o->parent();
				$this->get_resources_parents($res_o->parent());
			}

			$filter = array(
				"parent" => $resources_folder,
				"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
				"sort_by" => "objects.jrk",
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_MRP_RESOURCE.oid" => $res_ol->ids(),
						"CL_MENU.oid" => $this->my_resources_menus,
					)
				)),
			);
		}
		else
		{
			$filter = array(
				"parent" => $resources_folder,
				"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
				"sort_by" => "objects.jrk",
				// "CL_MRP_RESOURCE.state" => new obj_predicate_not(array($applicable_states)), // archived res removal std version
			);
		}

		$resource_tree = new object_tree($filter);

		// archived res removal backwards compatible version
		$this->mrp_remove_resources_from_tree = array();
		$resource_tree->foreach_cb(array(
			"func" => array(&$this, "cb_remove_inactive_res"),
			"save" => false,
		));

		if (count($this->mrp_remove_resources_from_tree))
		{
			$resource_tree->remove($this->mrp_remove_resources_from_tree);
		}
		// END archived res removal backwards compatible version

		classload("vcl/treeview");
		$tree_prms = array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "resourcetree",
				"persist_state" => true,
			),
			"root_item" => obj ($resources_folder),
			"ot" => $resource_tree,
			"var" => $attrb,
			"node_actions" => array (
				CL_MRP_RESOURCE => "change",
			),
		);

		if(in_array($arr["request"]["group"], array("grp_resources_load", "grp_resources", "grp_resources_hours_report", "grp_persons_quantity_report")))
		{
			unset($tree_prms["node_actions"]);
		}

		$treeview_inst = new treeview;
		$tree = $treeview_inst->tree_from_objects($tree_prms);
		// Hack the tree with reload stuff
		foreach($tree->get_item_ids() as $id)
		{
			$item = $tree->get_item($id);
			$item["url"] = "javascript:$('[name=resource]').val('".$id."'); update_material_table();";
			$tree->set_item($item);
		}
		if (isset($arr["prop"]["value"]))
		{
			$arr["prop"]["value"] .= $tree->finalize_tree ();
		}
		else
		{
			$arr["prop"]["value"] = $tree->finalize_tree ();
		}


	}

	public function _get_persons_resource_span_tree($arr)
	{
		return $this->create_resources_tree($arr, "resource_span");
	}

	public function _get_resources_resource_span_tree($arr)
	{
		return $this->create_resources_tree($arr, "resource_span");
	}

	public function _get_resources_time_tree($arr)
	{
		return $this->_get_time_tree($arr);
	}

	public function _get_persons_time_tree($arr)
	{
		return $this->_get_time_tree($arr);
	}
	public function _get_grp_material_time_tree($arr)
	{
		$tv =& $arr["prop"]["vcl_inst"];
		$var = "timespan";
		$tv->set_selected_item(isset($arr["request"][$var]) ? $arr["request"][$var] : "period_week");

		$tv->start_tree(array(
			"type" => TREE_DHTML,
			"persist_state" => true,
			"tree_id" => "proj_bills_time_tree",
		));

		$tv->add_item(0,array(
			"name" => t("K&otilde;ik ajavahemikud"),
			"id" => "all_time",
			"url" => aw_url_change_var($var, "all_time"),
		));

		$branches = array(
			"last_week" => t("Eelmine n&auml;dal"),
			"period_week" => t("K&auml;esolev n&auml;dal"),
			"period_last_last" => t("&Uuml;leelmine kuu"),
			"period_last" => t("Eelmine kuu"),
			"period_current" => t("K&auml;esolev kuu"),
			"period_next" => t("J&auml;rgmine kuu"),
			"period_lastyear" => t("Eelmine aasta"),
			"period_year" => t("K&auml;esolev aasta"),
		);

		foreach($branches as $id => $caption)
		{
			$tv->add_item("all_time", array(
				"id" => $id,
				"name" => $caption,
				"url" => "javascript:$('[name=timespan]').val('".$id."'); update_material_table();",
			));
		}
	}

	public function _get_my_stats_time_tree($arr)
	{
		return $this->_get_time_tree($arr);
	}

	function _get_time_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_selected_item(isset($arr["request"]["timespan"]) ? $arr["request"]["timespan"] : "all");
		$branches = array(
			0 => array(
//				"all" => t("K&otilde;ik"),
				"current_week" => t("K&auml;esolev n&auml;dal"),
				"last_week" => t("M&ouml;&ouml;dunud n&auml;dal"),
				"current_month" => t("K&auml;esolev kuu"),
				"last_month" => t("M&ouml;&ouml;dunud kuu"),
			),
		);

		$from_to = $this->db_fetch_row("SELECT MIN(aw_tm) as 'from', MAX(aw_tm) as 'to' FROM mrp_job_rows WHERE aw_tm > 0;");
		$tm = $from_to["from"];
		while($tm < $from_to["to"])
		{
			list($M, $D, $Y) = explode("-", date("n-j-Y", $tm));
			$branches[0]["date_".$Y] = $Y;
			$branches["date_".$Y]["date_".$Y."_".$M] = sprintf(t("%s %u"), aw_locale::get_lc_month($M), $Y);
			$branches["date_".$Y."_".$M]["date_".$Y."_".$M."_".$D] = sprintf(t("%u. %s %u"), $D, aw_locale::get_lc_month($M), $Y);
			$tm = mktime(0, 0, 0, $M, $D+1, $Y);
		}

		foreach($branches as $parent => $branch)
		{
			foreach($branch as $id => $caption)
			{
				$t->add_item($parent, array(
					"id" => $id,
					"name" => $caption,
					"reload" => array(
						"layouts" => array("grp_persons_right", "resources_hours_right", "charts_right"),
						"props" => array("material_stats_table"),
						"params" => array("timespan" => $id === "all" ? NULL : $id),
					),
				));
			}
		}
		}

	function _get_projects_time_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_selected_item(isset($arr["request"]["timespan"]) ? $arr["request"]["timespan"] : "all");
		$branches = array(
			0 => array(
				"all" => t("K&otilde;ik"),
				"current_week" => t("K&auml;esolev n&auml;dal"),
				"last_week" => t("M&ouml;&ouml;dunud n&auml;dal"),
				"current_month" => t("K&auml;esolev kuu"),
				"last_month" => t("M&ouml;&ouml;dunud kuu"),
			),
		);

//		$from_to = $this->db_fetch_row("SELECT MIN(aw_tm) as 'from', MAX(aw_tm) as 'to' FROM mrp_job_rows WHERE aw_tm > 0;");
/*		$tm = $from_to["from"];
		while($tm < $from_to["to"])
		{
			list($M, $D, $Y) = explode("-", date("n-j-Y", $tm));
			$branches[0]["date_".$Y] = $Y;
			$branches["date_".$Y]["date_".$Y."_".$M] = sprintf(t("%s %u"), aw_locale::get_lc_month($M), $Y);
			$branches["date_".$Y."_".$M]["date_".$Y."_".$M."_".$D] = sprintf(t("%u. %s %u"), $D, aw_locale::get_lc_month($M), $Y);
			$tm = mktime(0, 0, 0, $M, $D+1, $Y);
		}
*/
		foreach($branches as $parent => $branch)
		{
			foreach($branch as $id => $caption)
			{
				$t->add_item($parent, array(
					"id" => $id,
					"name" => $caption,
					/*
					"url" => aw_url_change_var(array(
						"timespan" => $id === "all" ? NULL : $id,
					)),
					*/
					"reload" => array(
						"props" => array("projects_list", "customers_list_proj"),
						"layouts" => array("resources_hours_right", "printer_right", "customers_search_table"),
						"params" => array("timespan" => $id === "all" ? NULL : $id, "pj_job" => NULL),
					),
				));
			}
		}
	}

	function format_hours($n)
	{
		if(empty($this->hours_report_time_format))
		{
			return number_format($n, 2);
		}
		else
		{
			switch($this->hours_report_time_format)
			{
				case 1:
					$n = round($n, 3);
					return sprintf("%s%d:%02u", $n < 0 ? "-" : "", floor(abs($n)), round(abs(fmod($n, 1)*60)));
					break;
			}
		}
	}

	public function _get_persons_detailed_hours_tbl($arr)
	{
		if((empty($arr["request"]["person_show_jobs"]) || !is_oid($arr["request"]["person_show_jobs"])) && !is_oid($this->get_hours_person($arr)))
		{
			return PROP_IGNORE;
		}

		$t = &$arr["prop"]["vcl_inst"];

		// Init
		$t->define_field(array(
			"name" => "name",
			"caption" => t("T&ouml;&ouml;"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "project",
			"caption" => t("Projekt"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "resource",
			"caption" => t("Ressurss"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "inprogress",
			"caption" => t("Efektiivne t&ouml;&ouml;aeg"),
			"align" => "right",
		));
		$t->define_field(array(
			"name" => "paused",
			"caption" => t("Paus"),
			"align" => "right",
		));

		$rows = $this->get_hours_by_job($arr, "person");

		foreach($rows as $row)
		{
			$t->define_data($row);
		}
	}

	public function _get_persons_hours_tbl($arr)
	{
		return $this->_get_hours_tbl($arr, "person");
	}

	public function _get_resources_hours_tbl($arr)
	{
		return $this->_get_hours_tbl($arr, "resource");
	}

	private function _get_hours_tbl($arr, $kf = "person")
	{
		$kd = array(
			"resource" => array(
				"caption" => t("Ressurss"),
			),
			"person" => array(
				"caption" => t("T&ouml;&ouml;taja"),
			),
		);

		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => $kf,
			"caption" => $kd[$kf]["caption"],
			"sortable" => 1,
		));
		if($kf == "resource")
		{
			$t->define_field(array(
				"name" => "available",
				"caption" => sprintf(t("Ressursi kogu vaba aeg %s"), empty($this->hours_report_time_format) ? t("(h)") : t("(h:mm)")),
				"sortable" => 1,
				"sorting_field" => "available_num",
				"align" => "right",
			));
			$t->define_field(array(
				"name" => "planned",
				"caption" => sprintf(t("Planeeritud t&ouml;&ouml;aeg %s"), empty($this->hours_report_time_format) ? t("(h)") : t("(h:mm)")),
				"sortable" => 1,
				"sorting_field" => "planned_num",
				"align" => "right",
			));
		}
		$t->define_field(array(
			"name" => "inprogress",
			"caption" => sprintf(t("Efektiivne t&ouml;&ouml;aeg %s"), empty($this->hours_report_time_format) ? t("(h)") : t("(h:mm)")),
			"sortable" => 1,
			"sorting_field" => "inprogress_num",
			"align" => "right",
		));
		$t->define_field(array(
			"name" => "paused",
			"caption" => sprintf(t("Paus %s"), empty($this->hours_report_time_format) ? t("(h)") : t("(h:mm)")),
			"sortable" => 1,
			"sorting_field" => "paused_num",
			"align" => "right",
		));
		$t->define_field(array(
			"name" => "paused_cnt",
			"caption" => t("Pause kokku"),
			"sortable" => 1,
			"numerical" => 1,
			"align" => "right",
		));
		$t->define_field(array(
			"name" => "paused_avg",
			"caption" => sprintf(t("Keskmine pausi pikkus %s"), empty($this->hours_report_time_format) ? t("(h)") : t("(h:mm)")),
			"sortable" => 1,
			"sorting_field" => "paused_avg_num",
			"align" => "right",
		));
		$data = $this->get_hours($arr, $kf);
		$rows = array();
		foreach(array_keys($data["name"]) as $key)
		{
			$rows[$key][$kf] = html::href(array(
				"caption" => $data["name"][$key],
				"url" => aw_url_change_var(array(
					"person_show_jobs" => $key,
				)),
				"title" => sprintf(t("Vaata %s t&ouml;id valitud ajavahemikus"), aw_locale::get_genitive_for_name($data["name"][$key])),
			));
//			$rows[$key][$kf] = $data["name"][$key];
			$rows[$key]["inprogress"] = $this->format_hours($data[MRP_STATUS_INPROGRESS][$key]);
			$rows[$key]["inprogress_num"] = $data[MRP_STATUS_INPROGRESS][$key];
			$rows[$key]["paused"] = $this->format_hours($data[MRP_STATUS_PAUSED][$key]);
			$rows[$key]["paused_num"] = $data[MRP_STATUS_PAUSED][$key];
			$rows[$key]["paused_cnt"] = (int)$data["count"][MRP_STATUS_PAUSED][$key];
			$rows[$key]["paused_avg"] = $this->format_hours($data["average"][MRP_STATUS_PAUSED][$key]);
			$rows[$key]["paused_avg_num"] = $data["average"][MRP_STATUS_PAUSED][$key];
			if($kf == "resource")
			{
				$rows[$key]["available"] = $this->format_hours($data["available"][$key]);
				$rows[$key]["available_num"] = $data["available"][$key];
				$rows[$key]["planned"] = $this->format_hours($data[MRP_STATUS_PLANNED][$key]);
				$rows[$key]["planned_num"] = $data[MRP_STATUS_PLANNED][$key];
			}
		}
		foreach($rows as $row)
		{
			$t->define_data($row);
		}
		$t->set_numeric_field(array("paused_num", "paused_avg_num", "inprogress_num"));
		$t->sort_by();
	}

	function _get_pp_birthdays($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "left",
		));
		$t->define_field(array(
			"name" => "birthday",
			"caption" => t("S&uuml;nnip&auml;ev"),
			"align" => "center",
			"sorting_field" => "birthday_tm"
		));

		$ps = obj($arr["obj_inst"]->owner)->get_workers()->arr();
		uasort($ps, array($this, "sort_by_birthday"));

		$cnt = 1;
		foreach($ps as $p)
		{
			if((int)$p->birthday <= 0)
			{
				continue;
			}

			if($cnt++ > 10)
			{
				break;
			}

			$bd_tm = explode("-", $p->birthday);
			$bd_tm = mktime(0, 0, 0, $bd_tm[1], $bd_tm[2], $bd_tm[0]);
			$bd = date("d-m-Y", $bd_tm);
			if(date("d-m", $bd_tm) == date("d-m"))
			{
				$bd = t("T&auml;na");
			}
			elseif(date("d-m", $bd_tm) == date("d-m", time() + 24*3600))
			{
				$bd = t("Homme");
			}
			$t->define_data(array(
				"name" => parse_obj_name($p->name),
				"birthday" => $bd,
				"birthday_tm" => $p->birthday,
			));
		}
	}

	function sort_by_birthday($a, $b)
	{
		$a_tm = explode("-", $a->birthday."-1-1");
		$b_tm = explode("-", $b->birthday."-1-1");
		$retval = ((int)$a_tm[1] - (int)date("n") == (int)$b_tm[1] - (int)date("n") ? (int)$a_tm[2] < (int)$b_tm[2] : (int)sprintf("%u", (int)$a_tm[1] - (int)date("n")) < (int)sprintf("%u", (int)$b_tm[1] - (int)date("n"))) ? -1 : 1;

		return $retval;
	}

	function _get_pp_resources($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "resource",
			"caption" => t("Ressurss"),
			"align" => "left"
		));
		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"align" => "center"
		));

		$rs = $this->get_cur_printer_resources(array(
			"ws" => $arr["obj_inst"],
			"ign_glob" => true
		));

		if(count($rs) > 0)
		{
			$inst = get_instance(CL_MRP_RESOURCE);

			foreach($rs as $r_id)
			{
				$r = obj($r_id);
				list($state, $num_jobs) = $inst->get_resource_state($r);

				$t->define_data(array(
					"resource" => $r->name(),
					"state" => sprintf(t("%s (%u)"), $inst->resource_states[$state], $num_jobs),
				));
			}
		}
		else
		{
			return PROP_IGNORE;
		}
	}

	function _get_printer_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$branches = array(
			"grp_printer_current" => "Jooksvad t&ouml;&ouml;d",
			"grp_printer_old" => "Tegemata t&ouml;&ouml;d",
			"grp_printer_done" => "Tehtud t&ouml;&ouml;d",
			"grp_printer_aborted" => "Katkestatud t&ouml;&ouml;d",
			"grp_printer_in_progress" => "K&otilde;ik t&ouml;&ouml;s olevad",
			"grp_printer_startable" => "K&otilde;ik t&ouml;&ouml;d mida oleks v&otilde;imalik alustada",
			"grp_printer_notstartable" => "T&ouml;&ouml;d, mida ei ole veel v&otilde;imalik alustada",
		);
		if(!isset($_GET["pj_job"]) || !$this->can("view", $_GET["pj_job"]))
		{
			$selected = isset($_GET["branch_id"]) && array_key_exists($_GET["branch_id"], $branches) ? $_GET["branch_id"] : "grp_printer_current";
			$t->set_selected_item($selected);
		}


		foreach($branches as $id => $caption)
		{
			$t->add_item(0, array(
				"id" => $id,
				"name" => $caption,
				/*
				"url" => aw_url_change_var(array(
					"pj_job" => NULL,
					"printer_job_page" => NULL,
					"branch_id" => $id,
				)),
				*/
				"reload" => array(
					"layouts" => array("printer_right"),
					"params" => array(
						"printer_job_page" => NULL,
						"branch_id" => $id,
						"pj_job" => NULL
					),
				),
			));
		}
	}

	function set_property ($arr = array ())
	{
		$this_object = $arr["obj_inst"];
		$prop =& $arr["prop"];
		$retval = PROP_OK;

		if ( (substr($prop["name"], 0, 9) === "parameter") and ($this_object->prop ($prop["name"]) != $prop["value"]) )
		{
			### post rescheduling msg
			$this_object->set_prop("rescheduling_needed", 1);
		}

		switch ($prop["name"])
		{
			case "printer_legend":
				if (isset($arr["request"]["ps_resource"]))
				{
					if ($arr["request"]["ps_resource"])
					{
						aw_session_set("mrp_operator_use_resource", $arr["request"]["ps_resource"]);
					}
					else
					{
						aw_session_del("mrp_operator_use_resource");
					}
				}
				break;

			case "projects_list":
				$retval = $this->save_custom_form_data ($arr);
				$applicable_lists = array (
					"planned_overdue",
					"overdue",
					"subcontracts",
					"aborted_jobs"
				);

				if (empty ($arr["request"]["mrp_tree_active_item"]) or in_array ($arr["request"]["mrp_tree_active_item"], $applicable_lists))
				{
					$this_object->set_prop("rescheduling_needed", 1);
				}
				break;

			case "resources_list":
				$retval = $this->save_custom_form_data ($arr);
				break;

			case "max_subcontractor_timediff":
				$prop["value"] = round ($prop["value"] * 3600);
				break;

			case "select_session_resource":
				if ($prop["value"])
				{
					aw_session_set("mrp_operator_use_resource", $prop["value"]);
				}
				else
				{
					aw_session_del("mrp_operator_use_resource");
				}
				// aaaand redirect
				header("Location: ".$this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "branch_id" => "grp_printer_current")));
				die();
				break;

			### settings tab
			case "automatic_archiving_period":
				$requested_period = (int) abs($prop["value"]);
				$saved_period = (int) $this_object->prop("automatic_archiving_period");
				$time = mktime(28,0,0, date("m"), date("d"), date("Y")); // four a.m. next day

				if ($requested_period and !$saved_period)
				{
					### add archiving scheduler
					$scheduler = get_instance("scheduler");
					$event = $this->mk_my_orb("archive_projects", array("id" => $this_object->id()));

					$scheduler->add(array(
						"event" => $event,
						"time" => $time,
						"uid" => aw_global_get("uid"),
						"auth_as_local_user" => true,
					));
				}
				elseif (!$requested_period and $saved_period)
				{
					### delete archiving scheduler
					$scheduler = get_instance("scheduler");
					$event = $this->mk_my_orb("archive_projects", array("id" => $this_object->id()));
					$scheduler->remove(array(
						"event" => $event,
					));
				}
				elseif ($requested_period and $saved_period and $requested_period != $saved_period)
				{
					### delete old event
					$scheduler = get_instance("scheduler");
					$event = $this->mk_my_orb("archive_projects", array("id" => $this_object->id()));
					$scheduler->remove(array(
						"event" => $event,
					));

					### add new
					$scheduler->add(array(
						"event" => $event,
						"time" => $time,
						"uid" => aw_global_get("uid"),
						"auth_as_local_user" => true,
					));
				}
				break;

			case "max_subcontractor_timediff":
				$prop["value"] = round ($prop["value"] * 3600);
				break;
		}

		return $retval;
	}

	function callback_mod_retval ($arr)
	{
		// delete justsaved msg where needed
		$applicable_groups =  array(
			"grp_printer",
			"grp_printer_current",
			"grp_printer_old",
			"grp_printer_done",
			"grp_printer_aborted",
			"grp_printer_in_progress",
			"grp_printer_startable",
			"grp_schedule_gantt",
			"grp_projects",
			"grp_printer_notstartable"
		);
		if (isset($arr["args"]["branch_id"]) and in_array($arr["args"]["branch_id"], $applicable_groups))
		{
			unset($arr["args"]["just_saved"]);
		}

		$params_to_keep = array(
			"mrp_tree_active_item",
			"timespan",
			"unit",
			"cat",
		);
		foreach($params_to_keep as $param_to_keep)
		{
			if(isset($arr["request"][$param_to_keep]))
			{
				$arr["args"][$param_to_keep] = $arr["request"][$param_to_keep];
			}
		}

		### gantt chart start selection
		if ($arr["request"]["chart_start_date"])
		{
			$month = (int) $arr["request"]["chart_start_date"]["month"];
			$day = (int) $arr["request"]["chart_start_date"]["day"];
			$year = (int) $arr["request"]["chart_start_date"]["year"];
			$mrp_chart_start = mktime (0, 0, 0, $month, $day, $year);
			$arr["args"]["mrp_chart_start"] = $mrp_chart_start;
		}

		### gantt chart project hilight
		if ($arr["request"]["chart_project_hilight"])
		{
			$ol = new object_list(array(
				"class_id" => CL_MRP_CASE,
				"name" => $arr["request"]["chart_project_hilight"]
			));
			if ($ol->count())
			{
				$tmp = $ol->begin();
				$arr["args"]["mrp_hilight"] = $tmp->id();
			}
			$arr["args"]["chart_project_hilight"] = $arr["request"]["chart_project_hilight"];
		}

		if ($arr["request"]["chart_customer"])
		{
			$arr["args"]["chart_customer"] = $arr["request"]["chart_customer"];
		}

		### gantt chart start move to project start
		if ($arr["request"]["chart_project_hilight_gotostart"])
		{
			$project_id = false;

			if (is_oid ($arr["args"]["mrp_hilight"]))
			{
				$project_id = $arr["args"]["mrp_hilight"];
			}

			if ($project_id)
			{
				$project = obj ($project_id);

				switch ($project->prop ("state"))
				{
					case MRP_STATUS_PLANNED:
						$starttime_prop = "starttime";
						break;
					case MRP_STATUS_INPROGRESS:
					case MRP_STATUS_DONE:
					case MRP_STATUS_ARCHIVED:
						$starttime_prop = "started";
						break;
				}

				$this_object = obj($arr["args"]["id"]);
				$list = new object_list (array (
					"class_id" => CL_MRP_JOB,
					"project" => $project_id,
					"parent" => $this_object->prop ("jobs_folder"),
					"length" => new obj_predicate_compare (OBJ_COMP_GREATER, 0),
					"resource" => new obj_predicate_compare (OBJ_COMP_GREATER, 0),
					$starttime_prop => new obj_predicate_compare (OBJ_COMP_GREATER, 0),
				));
				$list->sort_by (array(
					"prop" => $starttime_prop,
					"order" => "asc" ,
				));
				$first_job = $list->begin();

				if (is_object ($first_job))
				{
					$project_start = $first_job->prop ($starttime_prop);
					$project_start = mktime (0, 0, 0, date ("m", $project_start), date ("d", $project_start), date("Y", $project_start));
					$arr["args"]["mrp_chart_start"] = $project_start;
				}
			}
		}

		$_SESSION["mrp"]["ps_project"] = $arr["request"]["ps_project"];
	}

	function callback_pre_save ($arr)
	{
		if (is_oid (aw_global_get ("mrp_printer_aborted")))
		{
			$minstart= mktime (0, 0, 0, $arr["request"]["pj_minstart"]["month"], $arr["request"]["pj_minstart"]["day"], $arr["request"]["pj_minstart"]["year"]);
			$job = obj (aw_global_get ("mrp_printer_aborted"));
			$job->set_prop ("remaining_length", (int) ($arr["request"]["pj_remaining_length"]*3600));
			$job->set_prop ("minstart", (int) ($minstart));
			$job->save ();
			aw_session_del ("mrp_printer_aborted");
		}
	}

	// method only needed in self::create_resources_tree() method archived res removal backwards compatible version
//	function cb_remove_inactive_res(&$o, $parent)
	function cb_remove_inactive_res(&$o)
	{
		if (CL_MRP_RESOURCE == $o->class_id() and MRP_STATUS_RESOURCE_INACTIVE == $o->prop("state"))
		{
			$this->mrp_remove_resources_from_tree[] = $o->id();
		}
	}

	private function get_my_resources()
	{
		$filter = array();
		$filter["class_id"] = CL_MRP_RESOURCE_OPERATOR;
		$filter["site_id"] = array();
		$filter["lang_id"] = array();
		$person = get_current_person();
		if(is_object($person))
		{
			$filter["profession"] = array_keys($person->get_profession_selection());
		}
		else
		{
			$filter["oid"] = 1;
		}
		$ol = new object_list($filter);
		$resources = array();
		foreach($ol->arr() as $o)
		{
			$resources[] = $o->prop("resource");
		}
		return $resources;
	}

	private function get_resources_parents($menu)
	{
		if($this->can("view" , $menu))
		{
			$menu = obj($menu);
			if($menu->parent() != $this->resources_folder)
			{
				$this->my_resources_menus[$menu->parent()] = $menu->parent();
				$this->get_resources_parents($menu->parent());
			}
		}
	}

	function create_resources_tree ($arr = array(), $attrb = "mrp_tree_active_item")
	{
		$this_object = $arr["obj_inst"];
		$applicable_states = array(
			MRP_STATUS_RESOURCE_INACTIVE,
		);

		### resource tree
		$resources_folder = $this_object->prop ("resources_folder");

		if($arr["request"]["group"] == "my_resources" || $arr["request"]["group"] == "grp_printer_general")
		{
			$resids = $this->get_cur_printer_resources(array(
				"ws" => $arr["obj_inst"],
				"ign_glob" => true
			));
			$res_ol = new object_list();
			if (count($resids))
			{
				$res_ol = new object_list(array("oid" => $resids,"sort_by" => "objects.name"));
			}

			$this->resources_folder = $resources_folder;
			$this->my_resources_menus = array();
			foreach($res_ol->arr() as $res_o)
			{
				$this->my_resources_menus[$res_o->parent()] = $res_o->parent();
				$this->get_resources_parents($res_o->parent());
			}

			$filter = array(
				"parent" => $resources_folder,
				"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
				"sort_by" => "objects.jrk",
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_MRP_RESOURCE.oid" => $res_ol->ids(),
						"CL_MENU.oid" => $this->my_resources_menus,
					)
				)),
			);
		}
		else
		{
			$filter = array(
				"parent" => $resources_folder,
				"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
				"sort_by" => "objects.jrk",
				// "CL_MRP_RESOURCE.state" => new obj_predicate_not(array($applicable_states)), // archived res removal std version
			);
		}

		$resource_tree = new object_tree($filter);

		// archived res removal backwards compatible version
		$this->mrp_remove_resources_from_tree = array();
		$resource_tree->foreach_cb(array(
			"func" => array(&$this, "cb_remove_inactive_res"),
			"save" => false,
		));

		if (count($this->mrp_remove_resources_from_tree))
		{
			$resource_tree->remove($this->mrp_remove_resources_from_tree);
		}
		// END archived res removal backwards compatible version

		classload("vcl/treeview");
		$tree_prms = array(
			"tree_opts" => array(
				"type" => TREE_DHTML,
				"tree_id" => "resourcetree",
				"persist_state" => true,
			),
			"root_item" => obj ($resources_folder),
			"ot" => $resource_tree,
			"var" => $attrb,
			"node_actions" => array (
				CL_MRP_RESOURCE => "change",
			),
			"reload" => array(
				"layouts" => array(
					"printer_right",
					"right_pane",
					"grp_persons_right",
					"resources_hours_right",
				),
			)
		);

		if(in_array($arr["request"]["group"], array("grp_resources_load", "grp_resources", "grp_resources_hours_report", "grp_persons_quantity_report")))
		{
			unset($tree_prms["node_actions"]);
		}

		$treeview_inst = new treeview;
		$tree = $treeview_inst->tree_from_objects($tree_prms);

		if (isset($arr["prop"]["value"]))
		{
			$arr["prop"]["value"] .= $tree->finalize_tree ();
		}
		else
		{
			$arr["prop"]["value"] = $tree->finalize_tree ();
		}
	}

	function create_resources_list ($arr = array())
	{
		$table = $arr["prop"]["vcl_inst"];
		$this_object = $arr["obj_inst"];
		$person = get_current_person();
		$my = ($arr["request"]["group"] == "my_resources");

		if (isset($arr["request"]["mrp_tree_active_item"]) and $this->can("view", $arr["request"]["mrp_tree_active_item"]))
		{
			$active_item = obj ($arr["request"]["mrp_tree_active_item"]);

			if ($active_item->class_id () != CL_MENU)
			{
				if($arr["request"]["group"] == "grp_resources_load" || $arr["request"]["group"] == "grp_resources")
				{
					return $this->create_resources_load_list($arr);
				}
				$parent = $active_item->parent ();
			}
			else
			{
				$parent = $active_item->id ();
			}
		}
		else
		{
			$parent = $this_object->prop ("resources_folder");
		}

		$parent_o = new object($parent);
		$owner = $arr["obj_inst"]->prop("RELTYPE_MRP_OWNER.name");
		$table->set_caption(sprintf(t("%s ressursid kategoorias '%s'"), $owner, $parent_o->name()));
		if($my)
		{
			$table->set_caption(sprintf(t("%s ressursid kategoorias '%s'"), $person->name(), $parent_o->name()));
		}

		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "operator",
			"caption" => t("Operaator"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "status",
			"caption" => t("Staatus"),
			"sortable" => 1
		));
		$table->define_field(array(
			"name" => "order",
			"caption" => t("Jrk."),
			"callback" => array (&$this, "order_field_callback"),
			"callb_pass_row" => false,
			"sortable" => 1,
			"numeric" => 1
		));

		$table->define_chooser(array(
			"name" => "selection",
			"field" => "resource_id",
		));

		$table->set_default_sortby("order");
		$table->set_default_sorder("asc");

		$res_filter = array(
			"class_id" => CL_MRP_RESOURCE,
			"parent" => $parent,
		);

		if($my)
		{
			$res_filter["oid"] = $this->get_my_resources();
		}

		$object_list = new object_list($res_filter);

		$resources = $object_list->arr();

		$res2p = $this->get_workers_for_resources($object_list->ids());

		foreach ($resources as $resource)
		{
			$operators = array();

			if (isset($res2p[$resource->id()]))
			{
				foreach($res2p[$resource->id()] as $person)
				{
					$operators[] = html::obj_change_url($person);
				}
			}

			$table->define_data (array (
				"name" => $arr["request"]["group"] == "grp_resources_load" || $arr["request"]["group"] == "grp_resources" ? html::href(array(
					"url" => aw_url_change_var("mrp_tree_active_item", $resource->id()),
					"caption" => $resource->name(),
				)) : html::obj_change_url($resource),
				"order" => $resource->ord (),
				"operator" => join(",",$operators),
				"status" => $this->resource_states[$resource->prop("state")],
				"resource_id" => $resource->id(),
			));
		}
	}

	function create_resources_load_list($arr)
	{
		$args = array_merge($arr, array("obj_inst" => obj($arr["request"]["mrp_tree_active_item"])));
		return $args["obj_inst"]->instance()->create_job_list_table($args, true);
	}

	function create_resources_toolbar ($arr = array())
	{
		$toolbar = $arr["prop"]["toolbar"];
		$this_object = $arr["obj_inst"];

		if (isset($arr["request"]["mrp_tree_active_item"]) and $this->can("view", $arr["request"]["mrp_tree_active_item"]))
		{
			$active_item = obj ($arr["request"]["mrp_tree_active_item"]);

			if ($active_item->class_id () != CL_MENU)
			{
				$parent = $active_item->parent ();
			}
			else
			{
				$parent = $active_item->id ();
			}
		}
		else
		{
			$parent = $this_object->prop ("resources_folder");
		}

		$add_resource_url = $this->mk_my_orb("new", array(
			"return_url" => get_ru(),
			"mrp_workspace" => $this_object->id (),
			"mrp_parent" => $parent,
			"parent" => $parent,
		), "mrp_resource");
		$add_category_url = $this->mk_my_orb("new", array(
			"return_url" => get_ru(),
			"parent" => $parent,
		), "menu");

		$toolbar->add_menu_button(array(
			"name" => "add",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus"),
		));
		$toolbar->add_menu_item(array(
			"parent" => "add",
			"text" => t("Ressurss"),
			"link" => $add_resource_url,
		));
		$toolbar->add_menu_item(array(
			"parent" => "add",
			"text" => t("Ressurssikategooria"),
			"link" => $add_category_url,
		));

		$toolbar->add_separator();

		$toolbar->add_button(array(
			"name" => "cut",
			"tooltip" => t("L&otilde;ika"),
			"action" => "cut_resources",
			"img" => "cut.gif",
		));

		$toolbar->add_button(array(
			"name" => "copy",
			"tooltip" => t("Kopeeri"),
			"action" => "copy_resources",
			"img" => "copy.gif",
		));

		if (
			isset($_SESSION["mrp_workspace"]["cut_resources"]) and count(safe_array($_SESSION["mrp_workspace"]["cut_resources"])) > 0 or
			isset($_SESSION["mrp_workspace"]["copied_resources"]) and count(safe_array($_SESSION["mrp_workspace"]["copied_resources"])) > 0
		)
		{
			$toolbar->add_button(array(
				"name" => "paste",
				"tooltip" => t("Kleebi"),
				"action" => "paste_resources",
				"img" => "paste.gif",
			));
		};

		$toolbar->add_separator();

		$toolbar->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Arhiveeri"),
			"action" => "delete",
			"confirm" => t("Arhiveerida k&otilde;ik valitud ressursid?"),
		));
	}

	function create_projects_toolbar ($arr = array())
	{
		$toolbar = $arr["prop"]["toolbar"];
		$this_object = $arr["obj_inst"];
		$add_project_url = $this->mk_my_orb("new", array(
			"return_url" => get_ru(),
			"mrp_workspace" => $this_object->id (),
			"parent" => $this_object->prop ("projects_folder")
		), "mrp_case");
		$toolbar->add_button(array(
			"name" => "add",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus projekt"),
			"url" => $add_project_url
		));
		$toolbar->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta valitud projekt(id)"),
			"confirm" => t("Kustutada k&otilde;ik valitud projektid?"),
			"action" => "delete"
		));
		$toolbar->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta"),
			"action" => ""
		));
	}

	function create_projects_tree ($arr = array())
	{
		$this_object = $arr["obj_inst"];
		$projects_folder = $this_object->prop ("projects_folder");
		$open_path = NULL;
		$url = automatweb::$request->get_uri();
		$url->unset_arg(array(
			"ft_page",
			"sp_search",
			"sp_name",
			"sp_starttime",
			"sp_customer",
			"sp_due_date",
			"sp_status"
		));
		$reload = array(
			"props" => array("projects_list"),
			"params" => array(
				"ft_page" => NULL,
				"sp_search" => NULL,
				"sp_name" => NULL,
				"sp_starttime" => NULL,
				"sp_customer" => NULL,
				"sp_due_date" => NULL,
				"sp_status" => NULL,
			),
		);

		if (isset($arr["request"]["mrp_tree_active_item"]) and strstr($arr["request"]["mrp_tree_active_item"], "archived_"))
		{
			$tmp = explode("_", $arr["request"]["mrp_tree_active_item"]);

			if (3 == count($tmp))
			{
				$open_path = array("archived", "archived_" . $tmp[1], "archived_" . $tmp[1] . "_" . $tmp[2]);
			}
		}

		$tree = get_instance("vcl/treeview");
		$tree->start_tree (array (
			"type" => TREE_DHTML,
			"tree_id" => "projecttree",
			"persist_state" => 0,
			"has_root" => 1,
			"open_path" => $open_path,
			"root_url" => aw_url_change_var (array(
				"mrp_tree_active_item" => "all",
				"ft_page" => 0
			)),
			"root_name" => t("K&otilde;ik projektid") . " (" . $this->projects_all_count . ")",
			"get_branch_func" => $this->mk_my_orb("get_projects_subtree", array(
				"id" => $this_object->id(),
				"url" => aw_global_get("REQUEST_URI"),
				// "url" => aw_global_get("REQUEST_URI"),
				// "parent" => "",
			)) . "&parent=",//!!! ilmselt ajutine muudatus prisma serveri jaoks -- mkmyorb on seal arvatavasti vana vms.
		));
		$tree->set_only_one_level_opened (true);

		$url->set_arg("mrp_tree_active_item", "planned");
		$reload["params"]["mrp_tree_active_item"] = "planned";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["planned"] . " (" . $this->projects_planned_count . ")",
			"id" => "planned",
			"parent" => 0,
//			"url" => $url->get(),
			"reload" => $reload,
		));

		$url->set_arg("mrp_tree_active_item", "inwork");
		$reload["params"]["mrp_tree_active_item"] = "inwork";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["inwork"] . " (" . $this->projects_in_work_count . ")",
			"id" => "inwork",
			"parent" => 0,
//			"url" => $url->get(),
			"reload" => $reload,
		));

		$url->set_arg("mrp_tree_active_item", "planned_overdue");
		$reload["params"]["mrp_tree_active_item"] = "planned_overdue";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["planned_overdue"] . " (" . $this->projects_planned_overdue_count . ")",
			"id" => "planned_overdue",
			"parent" => 0,
//			"url" => $url->get(),
			"reload" => $reload,
		));

		$url->set_arg("mrp_tree_active_item", "overdue");
		$reload["params"]["mrp_tree_active_item"] = "overdue";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["overdue"] . " (" . $this->projects_overdue_count . ")",
			"id" => "overdue",
			"parent" => 0,
//			"url" => $url->get(),
			"reload" => $reload,
		));

		$url->set_arg("mrp_tree_active_item", "new");
		$reload["params"]["mrp_tree_active_item"] = "new";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["new"] . " (" . $this->projects_new_count . ")",
			"id" => "new",
			"parent" => 0,
//			"url" => $url->get(),
			"reload" => $reload,
		));

		$url->set_arg("mrp_tree_active_item", "aborted");
		$reload["params"]["mrp_tree_active_item"] = "aborted";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["aborted"] . " (" . $this->projects_aborted_count . ")",
			"id" => "aborted",
			"parent" => 0,
//			"url" => $url->get(),
			"reload" => $reload,
		));

		$url->set_arg("mrp_tree_active_item", "onhold");
		$reload["params"]["mrp_tree_active_item"] = "onhold";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["onhold"] . " (" . $this->projects_onhold_count . ")",
			"id" => "onhold",
			"parent" => 0,
//			"url" => $url->get(),
			"reload" => $reload,
		));

		$url->set_arg("mrp_tree_active_item", "done");
		$reload["params"]["mrp_tree_active_item"] = "done";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["done"] . " (" . $this->projects_done_count . ")",
			"id" => "done",
			"parent" => 0,
//			"url" => $url->get(),
			"reload" => $reload,
		));

		$tree->add_item (0, array (
			"name" => $this->project_list_categories["archived"],// . " (" . $this->projects_archived_count . ")",
			"id" => "archived",
			"parent" => 0,
			"url" => "javascript: void(0);",
			// "url" => aw_url_change_var (array(
				// "mrp_tree_active_item" => "archived",
				// "ft_page" => 0
			// )),
		));

		if ($this_object->prop("automatic_archiving_period"))
		{
			$tree->add_item ("archived", array (
				"id" => "dummy",
				"parent" => "archived",
			));
		}

		$url->set_arg("mrp_tree_active_item", "subcontracts");
		$reload["params"]["mrp_tree_active_item"] = "subcontracts";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["subcontracts"] . " (" . $this->jobs_subcontracted_count . ")",
			"parent" => 0,
			"id" => "subcontracts",
//			"url" => $url->get(),
			"reload" => $reload,
		));

		$url->set_arg("mrp_tree_active_item", "aborted_jobs");
		$reload["params"]["mrp_tree_active_item"] = "aborted_jobs";
		$tree->add_item (0, array (
			"name" => $this->project_list_categories["aborted_jobs"] . " (" . $this->jobs_aborted_count . ")",
			"parent" => 0,
			"id" => "aborted_jobs",
//			"url" => $url->get(),
			"reload" => $reload,
		));

		if ("search" !== $this->list_request)
		{
			$active_node = empty ($arr["request"]["mrp_tree_active_item"]) ? "planned" : $arr["request"]["mrp_tree_active_item"];
			$active_node = (isset($arr["request"]["mrp_tree_active_item"]) and "all" === $arr["request"]["mrp_tree_active_item"]) ? 0 : $active_node;
			$tree->set_selected_item ($active_node);
		}
		$arr["prop"]["value"] = $tree->finalize_tree(0);
	}

	function create_projects_list ($arr = array ())
	{
		$table = $arr["prop"]["vcl_inst"];

		$this_object = $arr["obj_inst"];
		$table->name = "projects_list_" . $this->list_request;

		$table->define_field (array (
			"name" => "customer",
			"caption" => t("Klient"),
			"chgbgcolor" => "bgcolour_overdue",
			// "sortable" => 1,
		));
		$table->define_field (array (
			"name" => "name",
			"caption" => t("Pro&shy;jekt"),
			"chgbgcolor" => "bgcolour_overdue",
			// "sortable" => 1,
			"numeric" => 1
		));
		$table->define_field (array (
			"name" => "title",
			"caption" => t("Pro&shy;jekti nimi"),
			"chgbgcolor" => "bgcolour_overdue",
			"sortable" => 1,
		));

		$no_plan_lists = array (
			"onhold",
			"new",
		);

		switch ($this->list_request)
		{
			case "inwork":
			case "planned_overdue":
			case "overdue":
			case "new":
			case "planned":
			case "aborted":
			case "onhold":
				$table->define_field (array (
					"name" => "starttime",
					"caption" => t("Materjalide<br />saabumine"),
					"chgbgcolor" => "bgcolour_overdue",
					"type" => "time",
					"format" => MRP_DATE_FORMAT,
					"sortable" => 1,
				));
				$table->define_field(array(
					"name" => "planned_date",
					"caption" => t("Planeeritud<br />valmimine"),
					"chgbgcolor" => "bgcolour_overdue",
					"type" => "time",
					"format" => MRP_DATE_FORMAT,
					"sortable" => (in_array($this->list_request, $no_plan_lists)) ? 0 : 1,
				));
				$table->define_field(array(
					"name" => "due_date",
					"caption" => t("T&auml;htaeg"),
					"type" => "time",
					"format" => MRP_DATE_FORMAT,
					"chgbgcolor" => "bgcolour_overdue",
					"sortable" => 1,
				));
				$table->define_field(array(
					"name" => "priority",
					"chgbgcolor" => "bgcolour_overdue",
					"caption" => t("Prio&shy;ri&shy;teet"),
					"callback" => array (&$this, "priority_field_callback"),
					"callb_pass_row" => false,
					"sortable" => 1,
				));
				break;

			case "all":
			case "done":
			case "archived":
				$table->define_field (array (
					"name" => "priority",
					"chgbgcolor" => "bgcolour_overdue",
					"caption" => t("Prio&shy;ri&shy;teet"),
					"sortable" => 1,
					"sorting_field" => "priority_int",
				));
				break;
		}

		$table->define_field(array(
			"name" => "sales_priority",
			"caption" => t("MP"),
			"tooltip" => t("M&uuml;&uuml;gi prioriteet"),
			"chgbgcolor" => "bgcolour_overdue",
			// "sortable" => 1,
		));

		if ($this->list_request !== "search")
		{
			$table->define_chooser(array(
				"name" => "selection",
				"field" => "project_id",
			));
		}

		switch ($this->list_request)
		{
			case "all":
			case "planned":
			case "inwork":
			case "planned_overdue":
			case "overdue":
			case "new":
			case "done":
			case "aborted":
			case "onhold":
				$list = $this->projects_list_objects;
				break;

			case "search":
				$list = $arr["search_res"];
				break;
		}

		$caption = $this->project_list_categories[$this->list_request];

		if (strstr($this->list_request, "archived_"))
		{
			$list = $this->projects_list_objects;

			// archive period caption
			$period_parts = explode("_", $this->list_request);
			$caption .= " ";
			$period_parts = array_reverse($period_parts);
			foreach ($period_parts as $part)
			{
				if ("archived" !== $part)
				{
					$caption .= "." . $part;
				}
			}
		}

		$jobs_folder = $this_object->prop ("jobs_folder");

		$return_url = get_ru();

		if(!$list)
		{
			$list = new object_list();
		}

		$projects = $list->arr ();
		$bg_colour = "";

		foreach ($projects as $project_id => $project)
		{
			$priority = $project->prop ("project_priority");
			$act = "change";

			if (!$this->can("edit", $project_id))
			{
				$act = "view";
			}

			$change_url = $this->mk_my_orb($act, array(
				"id" => $project_id,
				"return_url" => $return_url,
				"group" => "grp_case_workflow",
			), "mrp_case");

			### get planned project finishing date
			$planned_date = $project->prop ("planned_date");

			if (!$planned_date)
			{
				$jobs = $project->get_job_count();

				$list = new object_list (array (
					"class_id" => CL_MRP_JOB,
					"state" => MRP_STATUS_PLANNED,
					"parent" => $jobs_folder,
					"exec_order" => $jobs,
					"project" => $project_id,
				));
				$last_job = $list->begin ();
				$planned_date = is_object ($last_job) ? date (MRP_DATE_FORMAT, ($last_job->prop ("planned_length") + $last_job->prop ("starttime"))) : "-";
			}

			### get project customer
			$customer = $project->get_first_obj_by_reltype("RELTYPE_MRP_CUSTOMER");

			### do request specific operations
			if ("inwork" === $this->list_request or "planned" === $this->list_request)
			{
				### hilight for planned overdue
				$bg_colour = ($project->prop ("due_date") < $planned_date) ? MRP_COLOUR_PLANNED_OVERDUE : "";

				### hilight for overdue
				$bg_colour = ($project->prop ("due_date") <= time ()) ? MRP_COLOUR_OVERDUE : $bg_colour;
			}

			### define data for html table row
			$data = array (
				"name" => html::href (array (
					"caption" => $project->name(),
					"url" => $change_url,
					)
				),
				"customer" => (is_object($customer) ? $customer->name () : ""),
				"priority" => $priority,
				"priority_int" => $priority,
				"sales_priority" => $project->prop ("sales_priority"),
				"title" => substr(wordwrap($project->comment(), 25, "...", true), 0, 26),
				"starttime" => $project->prop ("starttime"),
				"due_date" => $project->prop ("due_date"),
				"planned_date" => $planned_date,
				"project_id" => $project_id,
				"bgcolour_overdue" => $bg_colour,
			);

			if (!$bg_colour)
			{
				unset ($data["bgcolour_overdue"]);
			}

			$table->define_data($data);
		}

		switch ($this->list_request)
		{
			case "inwork":
				$table->set_default_sortby ("due_date");
				break;

			case "planned_overdue":
			case "overdue":
			case "new":
			case "planned":
			case "all":
			case "done":
			default:
				$table->set_default_sortby ("starttime");
				break;
		}

		$table->set_caption(sprintf(t("Projektid: %s"), $caption));

		$coloured_lists = array(
			"inwork",
			"planned",
			"search"
		);
		if (in_array($this->list_request, $coloured_lists))
		{
			$table->set_footer('<div style="display: block; margin: 4px;"><span style="height: 15px; margin-right: 3px; background-color: ' . MRP_COLOUR_PLANNED_OVERDUE . '; border: 1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> '.t("&Uuml;le t&auml;htaja planeeritud").' <span style="height: 15px; margin-right: 3px; margin-left: 25px; background-color: ' . MRP_COLOUR_OVERDUE . '; border: 1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> '.t("&Uuml;le t&auml;htaja l&auml;inud").'</div>');
		}

		$table->set_default_sorder ("asc");
		$table->define_pageselector (array (
			"type" => "lbtxt",
			"d_row_cnt" => $this->projects_list_objects_count,
			"records_per_page" => $this_object->prop("projects_list_objects_perpage") ? $this_object->prop("projects_list_objects_perpage") : 30,
		));
	}

	function create_subcontract_jobs_list ($arr = array ())
	{
		$table = $arr["prop"]["vcl_inst"];
		$this_object = $arr["obj_inst"];

		$table->define_field (array (
			"name" => "customer",
			"caption" => t("Klient"),
			"chgbgcolor" => "bgcolour_overdue",
			// "sortable" => 1,
		));
		$table->define_field (array (
			"name" => "project",
			"caption" => t("Projekt"),
			"chgbgcolor" => "bgcolour_overdue",
			// "sortable" => 1,
		));
		$table->define_field (array (
			"name" => "resource",
			"caption" => t("Ressurss"),
			"chgbgcolor" => "bgcolour_overdue",
			// "sortable" => 1,
		));
		$table->define_field(array(
			"name" => "scheduled_date",
			"type" => "time",
			"format" => MRP_DATE_FORMAT,
			"numeric" => 1,
			"caption" => t("Planeeritud algusaeg"),
			"chgbgcolor" => "bgcolour_overdue",
			"sortable" => 1,
		));
		$table->define_field (array (
			"name" => "advisedstart",
			"caption" => t("Soovitav algusaeg"),
		));
		$table->define_field(array(
			"name" => "modify",
			"chgbgcolor" => "bgcolour_overdue",
			"caption" => t("Ava"),
		));

		$table->set_default_sortby ("scheduled_date");
		$table->set_default_sorder ("asc");
		$table->define_pageselector (array (
			"type" => "lbtxt",
			"d_row_cnt" => $this->projects_list_objects_count,
			"records_per_page" => $this_object->prop("projects_list_objects_perpage") ? $this_object->prop("projects_list_objects_perpage") : 30,
		));
		$table->set_caption($this->project_list_categories[$this->list_request]);

		$jobs = $this->projects_list_objects->arr();

		foreach ($jobs as $job_id => $job)
		{
			$project_id = $job->prop ("project");
			$resource_id = $job->prop ("resource");

			if (!is_oid ($project_id) or !is_oid ($resource_id))
			{
				continue;
			}

			$project = obj ($project_id);
			$resource = obj ($resource_id);

			### get project customer
			$customer = $project->get_first_obj_by_reltype("RELTYPE_MRP_CUSTOMER");

			### hilight for planned overdue
			if ( ($this_object->prop ("advised_starttime") > time() ) and (abs($job->prop ("starttime") - $job->prop ("advised_starttime")) > $this_object->prop ("max_subcontractor_timediff")) )
			{
				$bg_colour = MRP_COLOUR_PLANNED_OVERDUE;
			}
			else
			{
				$bg_colour = false;
			}

			### define data for html table row
			$definition = array (
				"customer" => (is_object ($customer) ? $customer->name () : ""),
				"project" => html::obj_change_url($project),
				"resource" => html::obj_change_url($resource),
				"scheduled_date" => $job->prop ("starttime"),
				"modify" => html::obj_change_url($job, t("Ava")),
				"bgcolour_overdue" => $bg_colour,
				"advisedstart" => '<span style="white-space: nowrap;">' . html::datetime_select(array(
					"name" => "mrp_job_advisedstart-" . $job_id,
					"value" => $job->prop ("advised_starttime"),
					"day" => "text",
					"month" => "text",
					"textsize" => "11px",
					)
				) . '</span>',
			);

			if (!$bg_colour)
			{
				unset ($definition["bgcolour_overdue"]);
			}

			$table->define_data($definition);
		}
	}

	function create_aborted_jobs_list ($arr = array ())
	{
		$table = $arr["prop"]["vcl_inst"];
		$this_object = $arr["obj_inst"];

		$table->define_field (array (
			"name" => "customer",
			"caption" => t("Klient"),
			// "sortable" => 1,
		));
		$table->define_field (array (
			"name" => "project",
			"caption" => t("Projekt"),
			// "sortable" => 1,
		));
		$table->define_field (array (
			"name" => "resource",
			"caption" => t("Ressurss"),
			// "sortable" => 1,
		));
		$table->define_field(array(
			"name" => "due_date",
			"type" => "time",
			"format" => MRP_DATE_FORMAT,
			"numeric" => 1,
			"caption" => t("Projekti t&auml;htaeg"),
			"sortable" => 1,
		));
		$table->define_field (array (
			"name" => "minstart",
			"caption" => t("Vara&shy;seim j&auml;tka&shy;mis&shy;aeg"),
		));
		$table->define_field (array (
			"name" => "reschedule",
			"caption" => t("Tagasta planeerimisse"),
		));

		$table->define_field(array(
			"name" => "abort_comment",
			"caption" => t("Katkestamise kommentaar"),
			// "sortable" => 1
		));

		$table->define_field(array(
			"name" => "modify",
			"caption" => t("Ava"),
		));

		$table->set_caption($this->project_list_categories[$this->list_request]);
		$table->set_default_sortby ("due_date");
		$table->set_default_sorder ("asc");
		$table->define_pageselector (array (
			"type" => "lbtxt",
			"d_row_cnt" => $this->projects_list_objects_count,
			"records_per_page" => $this_object->prop("projects_list_objects_perpage") ? $this_object->prop("projects_list_objects_perpage") : 30,
		));

		$jobs = $this->projects_list_objects->arr ();

		foreach ($jobs as $job_id => $job)
		{
			$project_id = $job->prop ("project");
			$resource_id = $job->prop ("resource");

			if (!is_oid ($project_id) or !is_oid ($resource_id))
			{
				continue;
			}

			$project = obj ($project_id);
			$resource = obj ($resource_id);

			### get project customer
			$customer = $project->get_first_obj_by_reltype("RELTYPE_MRP_CUSTOMER");

			### define data for html table row
			$definition = array (
				"customer" => (is_object ($customer) ? $customer->name () : ""),
				"project" => html::obj_change_url($project),
				"resource" => html::get_change_url(
					$resource->id(),
					array("return_url" => get_ru()),
					$resource->name ()
				),
				"due_date" => $project->prop ("due_date"),
				"modify" => html::obj_change_url($job, t("Ava")),
				"reschedule" => html::checkbox(array(
					"name" => "mrp_job_reschedule-" . $job_id,
					)
				),
				"minstart" => '<span style="white-space: nowrap;">' . html::datetime_select(array(
					"name" => "mrp_job_minstart-" . $job_id,
					"value" => (($job->prop ("minstart")) ? $job->prop ("minstart") : time()),
					"day" => "text",
					"month" => "text",
					"textsize" => "11px",
					)
				) . '</span>',
				"abort_comment" => $this->get_abort_comment_from_job($job)
			);

			$table->define_data($definition);
		}
	}

	function create_schedule_chart ($arr)
	{
		$time =  time();
		$this_object = $arr["obj_inst"];
		$chart = get_instance ("vcl/gantt_chart");
		$columns = (int) (isset($arr["request"]["mrp_chart_length"]) ? $arr["request"]["mrp_chart_length"] : 7);
		$range_start = (int) (isset($arr["request"]["mrp_chart_start"]) ? $arr["request"]["mrp_chart_start"] : $this->get_week_start ());
		$range_end = (int) ($range_start + $columns * 86400);
		$hilighted_project = (int) (isset($arr["request"]["mrp_hilight"]) ? $arr["request"]["mrp_hilight"] : false);
		$hilighted_jobs = array ();

		switch ($columns)
		{
			case 1:
				$subdivisions = 24;
				break;

			default:
				$subdivisions = 3;
		}

		### add row dfn-s, resource names
		$toplevel_categories = new object_list (array (
			"class_id" => CL_MENU,
			"parent" => $this_object->prop ("resources_folder"),
		));
		$toplevel_categories->add(new object($this_object->prop ("resources_folder")));

		$mrp_schedule = get_instance(CL_MRP_SCHEDULE);

		for ($category = $toplevel_categories->begin(); !$toplevel_categories->end(); $category = $toplevel_categories->next())
		{
			$id = $category->id();

			if ($id !== $this_object->prop ("resources_folder"))
			{
				$chart->add_row (array (
					"name" => $id,
					"title" => $category->name(),
					"type" => "separator",
				));
			}

			$resource_tree = new object_tree(array(
				"parent" => $id,
				"class_id" => array (CL_MRP_RESOURCE),
				"sort_by" => "objects.jrk",
			));
			$resources = $resource_tree->to_list();

			for ($resource = $resources->begin (); !$resources->end (); $resource = $resources->next ())
			{
				$chart->add_row (array (
					"name" => $resource->id(),
					"title" => $resource->name(),
					"uri" => html::get_change_url(
						$resource->id(),
						array("return_url" => get_ru())
					)
				));

				if (empty($arr["request"]["chart_customer"]))
				{
					### add reserved times for resources, cut off past
					$reserved_times = $mrp_schedule->get_unavailable_periods_for_range(array(
						"mrp_resource" => $resource->id(),
						"mrp_start" => $range_start,
						"mrp_length" => $range_end - $range_start
					));

					foreach($reserved_times as $rt_start => $rt_end)
					{
						if ($rt_end > $time)
						{
							$rt_start = ($rt_start < $time) ? $time : $rt_start;
							$chart->add_bar(array(
								"row" => $resource->id(),
								"start" => $rt_start,
								"length" => $rt_end - $rt_start,
								"nostartmark" => true,
								"colour" => MRP_COLOUR_UNAVAILABLE,
								"url" => "#",
								"layer" => 2,
								"title" => sprintf(t("Kinnine aeg %s - %s"), date(MRP_DATE_FORMAT, $rt_start), date(MRP_DATE_FORMAT, $rt_end))
							));
						}
					}
				}
			}
		}

		### get job id-s for hilighted project if requested
		if ($hilighted_project)
		{
			$list = new object_list (array (
				"class_id" => CL_MRP_JOB,
				"parent" => $this_object->prop ("jobs_folder"),
				"project" => $hilighted_project,
			));
			$hilighted_jobs = $list->ids ();
		}

		// ### get jobs in requested range & add bars
		// $res = $this->db_fetch_array (
			// "SELECT MAX(schedule.planned_length), MAX(job.finished-job.started) FROM mrp_job as job ".
			// "LEFT JOIN mrp_schedule schedule ON schedule.oid = job.oid " .
			// "WHERE job.state !=" . MRP_STATUS_DELETED . " AND ".
			// "job.length > 0 AND ".
			// "job.resource > 0 ".
		// "");
		// rsort ($res[0]);
		// $max_length = reset ($res[0]);
		$jobs = array ();

		### job states that are shown in chart past
		$applicable_states = array (
			MRP_STATUS_DONE,
			MRP_STATUS_INPROGRESS,
			MRP_STATUS_PAUSED,
			MRP_STATUS_SHIFT_CHANGE,
		);

		if (!empty($arr["request"]["chart_customer"]))
		{
			$this->db_query (
			"SELECT job.oid,job.project,job.state,job.started,job.finished,job.resource,job.exec_order,schedule.*,o.metadata " .
			"FROM " .
				"mrp_job as job " .
				"LEFT JOIN objects o ON o.oid = job.oid " .
				"LEFT JOIN mrp_schedule schedule ON schedule.oid = job.oid " .
				"LEFT JOIN aliases a_job ON (a_job.source = o.oid AND a_job.reltype = 2) " .
				"LEFT JOIN aliases a_case ON (a_case.source = a_job.target AND a_case.reltype = 2) " .
				"LEFT JOIN objects o_cust ON o_cust.oid = a_case.target " .
			"WHERE " .
				"job.state IN (" . implode (",", $applicable_states) . ") AND " .
				"o_cust.name like '%".$arr["request"]["chart_customer"]."%' AND " .
				"o.status > 0 AND " .
				"o.parent = " . $this_object->prop ("jobs_folder") . " AND " .
				"((!(job.started < ".$range_start.")) OR ((job.state = " . MRP_STATUS_DONE . " AND job.finished > ".$range_start.") OR (job.state != " . MRP_STATUS_DONE . " AND ".$time." > ".$range_start."))) AND " .
				"job.started < ".$range_end." AND " .
				"job.project > 0 AND " .
				"job.length > 0 AND " .
				"job.resource > 0 " .
			"");

			while ($job = $this->db_next())
			{
				if ($this->can("view", $job["oid"]))
				{
					$metadata = aw_unserialize ($job["metadata"]);
					$job["paused_times"] = $metadata["paused_times"];
					$jobs[] = $job;
				}
			}

			// $filt = array (
				// "class_id" => CL_MRP_JOB,
				// "state" => $applicable_states,
				// "parent" => $this_object->prop ("jobs_folder"),
				// "started" => new obj_predicate_compare (OBJ_COMP_BETWEEN, ($range_start - $max_length), $range_end),
				// "resource" => new obj_predicate_compare (OBJ_COMP_GREATER, 0),
				// "length" => new obj_predicate_compare (OBJ_COMP_GREATER, 0),
				// "project" => new obj_predicate_compare (OBJ_COMP_GREATER, 0),
			// );

			// ### filter by customer as well
			// $filt["CL_MRP_JOB.RELTYPE_MRP_PROJECT.RELTYPE_MRP_CUSTOMER.name"] = $arr["request"]["chart_customer"];
			// $list = new object_list ($filt);
			// $list_jobs = $list->arr ();
		}
		else
		{
			$this->db_query (
			"SELECT job.oid,job.project,job.state,job.started,job.finished,job.resource,job.exec_order,schedule.*,o.metadata " .
			"FROM " .
				"mrp_job as job " .
				"LEFT JOIN objects o ON o.oid = job.oid " .
				"LEFT JOIN mrp_schedule schedule ON schedule.oid = job.oid " .
			"WHERE " .
				"job.state IN (" . implode (",", $applicable_states) . ") AND " .
				"o.status > 0 AND " .
				"o.parent = '" . $this_object->prop ("jobs_folder") . "' AND " .
				"((!(job.started < ".$range_start.")) OR ((job.state = " . MRP_STATUS_DONE . " AND job.finished > ".$range_start.") OR (job.state != " . MRP_STATUS_DONE . " AND ".$time." > ".$range_start."))) AND " .
				"job.started < ".$range_end." AND " .
				"job.project > 0 AND " .
				"job.length > 0 AND " .
				"job.resource > 0 " .
			"");

			while ($job = $this->db_next())
			{
				if ($this->can("view", $job["oid"]))
				{
					$metadata = aw_unserialize ($job["metadata"]);
					$job["paused_times"] = isset($metadata["paused_times"]) ? $metadata["paused_times"] : array();
					$jobs[] = $job;
				}
			}
		}

		### job states that are shown in chart future
		$applicable_states = array (
			MRP_STATUS_PLANNED,
			MRP_STATUS_ABORTED,
		);

		if (!empty($arr["request"]["chart_customer"]))
		{
			$this->db_query (
			"SELECT job.oid,job.project,job.state,job.started,job.finished,job.resource,job.exec_order,schedule.*,o.metadata " .
			"FROM " .
				"mrp_job as job " .
				"LEFT JOIN objects o ON o.oid = job.oid " .
				"LEFT JOIN mrp_schedule schedule ON schedule.oid = job.oid " .
				"LEFT JOIN aliases a_job ON (a_job.source = o.oid AND a_job.reltype = 2) " .
				"LEFT JOIN aliases a_case ON (a_case.source = a_job.target AND a_case.reltype = 2) " .
				"LEFT JOIN objects o_cust ON o_cust.oid = a_case.target " .
			"WHERE " .
				"job.state IN (" . implode (",", $applicable_states) . ") AND " .
				"o_cust.name like '%".$arr["request"]["chart_customer"]."%' AND " .
				"o.status > 0 AND " .
				"o.parent = " . $this_object->prop ("jobs_folder") . " AND " .
				"schedule.starttime < ".$range_end." AND " .
				"schedule.starttime > ".$time." AND " .
				"((!(schedule.starttime < ".$range_start.")) OR ((schedule.starttime + schedule.planned_length) > ".$range_start.")) AND " .
				"job.project > 0 AND " .
				"job.length > 0 AND " .
				"job.resource > 0 " .
			"");

			while ($job = $this->db_next())
			{
				if ($this->can("view", $job["oid"]))
				{
					$metadata = aw_unserialize ($job["metadata"]);
					$job["paused_times"] = $metadata["paused_times"];
					$jobs[] = $job;
				}
			}

			// $filt = array (
				// "class_id" => CL_MRP_JOB,
				// "parent" => $this_object->prop ("jobs_folder"),
				// "state" => $applicable_states,
				// "starttime" => new obj_predicate_compare (OBJ_COMP_BETWEEN, ($range_start - $max_length), $range_end),
				// "starttime" => new obj_predicate_compare (OBJ_COMP_GREATER, time ()),
				// "resource" => new obj_predicate_compare (OBJ_COMP_GREATER, 0),
				// "length" => new obj_predicate_compare (OBJ_COMP_GREATER, 0),
			// );

			// ### filter by customer as well
			// $filt["CL_MRP_JOB.RELTYPE_MRP_PROJECT.RELTYPE_MRP_CUSTOMER.name"] = $arr["request"]["chart_customer"];
			// $list = new object_list ($filt);
			// $list_jobs = array_merge ($list->arr (), $list_jobs);


			// #//!!! arrayks konvertimine, et yhtiks db_queryga saadud asjaga, kui kliendiga koos p2ring tehtud pole seda enam vaja.
			// foreach ($list_jobs as $list_job)
			// {
				// $jobs[] = array (
					// "oid" => $list_job->id (),
					// "paused_times" => $list_job->meta ("paused_times"),
					// "project" => $list_job->prop ("project"),
					// "state" => $list_job->prop ("state"),
					// "started" => $list_job->prop ("started"),
					// "finished" => $list_job->prop ("finished"),
					// "planned_length" => $list_job->prop ("planned_length"),
					// "starttime" => $list_job->prop ("starttime"),
					// "resource" => $list_job->prop ("resource"),
					// "exec_order" => $list_job->prop ("exec_order"),
				// );
			// }
			// #//!!! END arrayks konvertimine
		}
		else
		{
			$this->db_query (
			"SELECT job.oid,job.project,job.state,job.started,job.finished,job.resource,job.exec_order,schedule.*,o.metadata " .
			"FROM " .
				"mrp_job as job " .
				"LEFT JOIN objects o ON o.oid = job.oid " .
				"LEFT JOIN mrp_schedule schedule ON schedule.oid = job.oid " .
			"WHERE " .
				"job.state IN (" . implode (",", $applicable_states) . ") AND " .
				"o.status > 0 AND " .
				"o.parent = '" . $this_object->prop ("jobs_folder") . "' AND " .
				"schedule.starttime < ".$range_end." AND " .
				"schedule.starttime > ".$time." AND " .
				"((!(schedule.starttime < ".$range_start.")) OR ((schedule.starttime + schedule.planned_length) > ".$range_start.")) AND " .
				"job.project > 0 AND " .
				"job.length > 0 AND " .
				"job.resource > 0 " .
			"");

			while ($job = $this->db_next())
			{
				if ($this->can("view", $job["oid"]))
				{
					$metadata = aw_unserialize ($job["metadata"]);
					$job["paused_times"] = $metadata["paused_times"];
					$jobs[] = $job;
				}
			}
		}

		foreach ($jobs as $job)
		{
			if (!$this->can("view", $job["project"]))
			{
				continue;
			}

			$project = obj ($job["project"]);

			### project states that are shown in chart
			$applicable_states = array (
				MRP_STATUS_PLANNED,
				MRP_STATUS_INPROGRESS,
				MRP_STATUS_DONE,
				MRP_STATUS_ARCHIVED,
			);

			if (!in_array ($project->prop ("state"), $applicable_states))
			{
				continue;
			}

			### get start&length according to job state
			switch ($job["state"])
			{
				case MRP_STATUS_DONE:
					$start = $job["started"];
					$length = $job["finished"] - $job["started"];
// /* dbg */ echo date(MRP_DATE_FORMAT, $start) . "-" . date(MRP_DATE_FORMAT, $start + $length) . "<br>";
					break;

				case MRP_STATUS_PLANNED:
					$start = $job["starttime"];
					$length = $job["planned_length"];
					break;

				case MRP_STATUS_SHIFT_CHANGE:
				case MRP_STATUS_PAUSED:
				case MRP_STATUS_INPROGRESS:
					$start = $job["started"];
					$length = (($start + $job["planned_length"]) < $time) ? ($time - $start) : $job["planned_length"];
					break;
			}

			$resource = obj ($job["resource"]);
			$job_name = $project->name () . "-" . $job["exec_order"] . " - " . $resource->name ();

			### set bar colour
			$colour = self::$state_colours[$job["state"]];
			$colour = in_array ($job["oid"], $hilighted_jobs) ? MRP_COLOUR_HILIGHTED : $colour;

			$bar = array (
				"id" => $job["oid"],
				"row" => $resource->id (),
				"start" => $start,
				"colour" => $colour,
				"length" => $length,
				"layer" => 0,
				"uri" => aw_url_change_var ("mrp_hilight", $project->id ()),
				"title" => $job_name . " (" . date (MRP_DATE_FORMAT, $start) . " - " . date (MRP_DATE_FORMAT, $start + $length) . ")"
/* dbg */ . " [res:" . $resource->id () . " t&ouml;&ouml;:" . $job["oid"] . " proj:" . $project->id () . "]"
			);

			$chart->add_bar ($bar);

			### add paused bars
			foreach(safe_array($job["paused_times"]) as $pd)
			{
				if ($pd["start"] && $pd["end"])
				{
					$bar = array (
						"row" => $resource->id (),
						"start" => $pd["start"],
						"nostartmark" => true,
						"layer" => 1,
						"colour" => self::$state_colours[MRP_STATUS_PAUSED],
						"length" => ($pd["end"] - $pd["start"]),
						"uri" => aw_url_change_var ("mrp_hilight", $project->id ()),
						"title" => $job_name . ", paus (" . date (MRP_DATE_FORMAT, $pd["start"]) . " - " . date (MRP_DATE_FORMAT, $pd["end"]) . ")"
					);

					$chart->add_bar ($bar);
				}
			}
		}

		### config
		$chart->configure_chart (array (
			"chart_id" => "master_schedule_chart",
			"style" => "aw",
			"start" => $range_start,
			"end" => $range_end,
			"columns" => $columns,
			"caption" => t("Voop&otilde;hine tootmisgraafik"),
			"footer" => $this->draw_colour_legend(),
			"subdivisions" => $subdivisions,
			"timespans" => $subdivisions,
			"width" => 850,
			"row_height" => 10,
			"navigation" => $this->create_chart_navigation($arr)
		));

		### define columns
		$i = 0;
		$days = array ("P", "E", "T", "K", "N", "R", "L");

		while ($i < $columns)
		{
			$day_start = ($range_start + ($i * 86400));
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

		return $chart->draw_chart ();
	}

	function create_chart_navigation ($arr)
	{
		$start = (int) (isset($arr["request"]["mrp_chart_start"]) ? $arr["request"]["mrp_chart_start"] : time ());
		$columns = (int) (isset($arr["request"]["mrp_chart_length"]) ? $arr["request"]["mrp_chart_length"] : 7);
		$start = ($columns === 7) ? $this->get_week_start ($start) : $start;
		$period_length = $columns * 86400;
		$length_nav = array ();
		$start_nav = array ();

		for ($days = 1; $days < 8; $days++)
		{
			if ($columns == $days)
			{
				$length_nav[] = $days;
			}
			else
			{
				$length_nav[] = html::href (array (
					"caption" => $days,
					"url" => aw_url_change_var ("mrp_chart_length", $days),
				));
			}
		}

		$start_nav[] = html::href (array (
			"caption" => t("<<"),
			"title" => t("5 tagasi"),
			"url" => aw_url_change_var ("mrp_chart_start", ($this->get_time_days_away (5*$columns, $start, -1))),
		));
		$start_nav[] = html::href (array (
			"caption" => t("Eelmine"),
			"url" => aw_url_change_var ("mrp_chart_start", ($this->get_time_days_away ($columns, $start, -1))),
		));
		$start_nav[] = html::href (array (
			"caption" => t("T&auml;na"),
			"url" => aw_url_change_var ("mrp_chart_start", $this->get_week_start ()),
		));
		$start_nav[] = html::href (array (
			"caption" => t("J&auml;rgmine"),
			"url" => aw_url_change_var ("mrp_chart_start", ($this->get_time_days_away ($columns, $start))),
		));
		$start_nav[] = html::href (array (
			"caption" => t(">>"),
			"title" => t("5 edasi"),
			"url" => aw_url_change_var ("mrp_chart_start", ($this->get_time_days_away (5*$columns, $start))),
		));

		$navigation = sprintf(t('&nbsp;&nbsp;Periood: %s &nbsp;&nbsp;P&auml;evi perioodis: %s'), implode (" ", $start_nav) ,implode (" ", $length_nav));

		if (isset($arr["request"]["mrp_hilight"]) and $this->can("view", $arr["request"]["mrp_hilight"]))
		{
			$project = obj ($arr["request"]["mrp_hilight"]);
			$deselect = html::href (array (
				"caption" => t("Kaota valik"),
				"url" => aw_url_change_var ("mrp_hilight", ""),
			));
			$change_url = html::obj_change_url ($project);
			$navigation .= t(' &nbsp;&nbsp;Valitud projekt: ') . $change_url . ' (' . $deselect . ')';
		}

		return $navigation;
	}

	function save_custom_form_data ($arr = array ())
	{
		$retval = PROP_OK;

		foreach ($arr["request"] as $name => $value)
		{
			$prop = explode ("-", $name);

			if (2 === count($prop))
			{
				$name = $prop[0];
				$oid = $prop[1];

				if ($this->can("edit", $oid))
				{
					switch ($name)
					{
						case "mrp_project_priority":
							$project = obj ($oid);
							$project->set_prop ("project_priority", aw_math_calc::string2float($value));
							$project->save ();
							break;

						case "mrp_job_minstart":
							$job = obj ($oid);
							$minstart = mktime ($value["hour"], $value["minute"], 0, $value["month"], $value["day"], $value["year"]);
							$job->set_prop ("minstart", $minstart);
							$job->save ();
							break;

						case "mrp_job_advisedstart":
							$job = obj ($oid);
							$advised_starttime = mktime ($value["hour"], $value["minute"], 0, $value["month"], $value["day"], $value["year"]);
							$job->set_prop ("advised_starttime", $advised_starttime);
							$job->save ();
							break;

						case "mrp_job_reschedule":
							if ($value)
							{
								$applicable_states = array (
									MRP_STATUS_ABORTED,
								);
								$job = obj ($oid);

								if (in_array ($job->prop ("state"), $applicable_states))
								{
									$job->load_data();
									$job->plan();
								}
							}
							break;

						case "mrp_resource_order":
							$resource = obj ($oid);
							$resource->set_ord ((int) $value);
							$resource->save ();
							break;
					}
				}
			}
		}

		return $retval;
	}

	/**
		@attrib name=delete
	**/
	function delete ($arr)
	{
		$sel = $arr["selection"];

		if (is_array ($sel))
		{
			$ol = new object_list (array (
				"oid" => array_keys ($sel),
			));
			$errors = NULL;
			$res_e = array();
			$jobs_e = array();

			for ($o = $ol->begin (); !$ol->end (); $o = $ol->next ())
			{
				if (CL_MRP_RESOURCE == $o->class_id() and MRP_STATUS_RESOURCE_INUSE != $o->prop("state"))
				{
					$applicable_states = array(
						MRP_STATUS_DONE
					);
					$resource_jobs = new object_list(array(
						"class_id" => CL_MRP_JOB,
						"resource" => $o->id(),
						"state" => new obj_predicate_not($applicable_states),
						"site_id" => array(),
						"lang_id" => array()
					));

					if ($resource_jobs->count())
					{
						$unfinished_jobs = array();
						foreach ($resource_jobs->ids() as $job_id)
						{
							$unfinished_jobs[] = html::get_change_url($job_id, array(), $job_id);
						}

						$res_e[] = $o->name() . " [l&otilde;petamata t&ouml;&ouml;d: " . implode(",", $unfinished_jobs) . "]";
					}
					else
					{
						$o->delete();
					}
				}
				elseif ($this->can ("delete", $o->id()))
				{
					$o->delete ();
				}
			}

			if (count($res_e))
			{
				$errors .= t("Ei saa arhiveerida, sest on l&otilde;petamata t&ouml;id: "). implode(",", $res_e);
				aw_session_set("mrp_errors", $errors);
			}
		}

		$return_url = $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"return_url" =>  ($arr["return_url"]),
			"group" => $arr["group"],
			"subgroup" => $arr["subgroup"],
		), "mrp_workspace");
		return $return_url;
	}

	function get_proj_overdue($this_object)
	{
		$ret = array();
		$this->db_query("
			SELECT
				objects.oid
			FROM
				objects
				LEFT JOIN mrp_case on mrp_case.oid = objects.oid
				LEFT JOIN aliases ON (aliases.source = objects.oid AND aliases.reltype = 3)
				LEFT JOIN mrp_job ON mrp_job.oid = aliases.target
			WHERE
				objects.status > 0 AND
				objects.class_id = ".CL_MRP_CASE." AND
				objects.parent = " . $this_object->prop ("projects_folder") . " AND
				(mrp_job.starttime + mrp_job.length) > mrp_case.due_date
		");
		while ($row = $this->db_next())
		{
			$ret[$row["oid"]] = $row["oid"];
		}
		return $ret;
	}

    // @attrib name=get_time_days_away
	// @param days required type=int
	// @param direction optional type=int
	// @param time optional
	// @returns UNIX timestamp for time of day start $days away from day start of $time
	// @comment DST safe if cumulated error doesn't exceed 12h. If $direction is negative, time is computed for days back otherwise days to.
	function get_time_days_away ($days, $time = false, $direction = 1)
	{
		if (false === $time)
		{
			$time = time ();
		}

		$time_daystart = mktime (0, 0, 0, date ("m", $time), date ("d", $time), date("Y", $time));
		$day_start = ($direction < 0) ? ($time_daystart - $days*86400) : ($time_daystart + $days*86400);
		$nodst_hour = (int) date ("H", $day_start);

		if ($nodst_hour)
		{
			if ($nodst_hour < 13)
			{
				$dst_error = $nodst_hour;
				$day_start = $day_start - $dst_error*3600;
			}
			else
			{
				$dst_error = 24 - $nodst_hour;
				$day_start = $day_start + $dst_error*3600;
			}
		}

		return $day_start;
	}

	function get_week_start ($time = false) //!!! somewhat dst safe (safe if error doesn't exceed 12h)
	{
		if (!$time)
		{
			$time = time ();
		}

		$date = getdate ($time);
		$wday = $date["wday"] ? ($date["wday"] - 1) : 6;
		$week_start = $time - ($wday * 86400 + $date["hours"] * 3600 + $date["minutes"] * 60 + $date["seconds"]);
		$nodst_hour = (int) date ("H", $week_start);

		if ($nodst_hour)
		{
			if ($nodst_hour < 13)
			{
				$dst_error = $nodst_hour;
				$week_start = $week_start - $dst_error*3600;
			}
			else
			{
				$dst_error = 24 - $nodst_hour;
				$week_start = $week_start + $dst_error*3600;
			}
		}

		return $week_start;
	}

	function _user_list_toolbar($arr)
	{
		$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MRP_OWNER");
		if ($o)
		{
			$tmp = $arr["obj_inst"];
			$arr["obj_inst"] = $o;
			$co = $o->instance();
			$co->callback_on_load($arr);

			$i = get_instance("applications/crm/crm_company_people_impl");
			$i->_get_contact_toolbar($arr);

			$tb = $arr["prop"]["vcl_inst"];
			$tb->remove_button("Kone");
			$tb->remove_button("Kohtumine");
			$tb->remove_button("Toimetus");
			$tb->remove_button("Search");
			$tb->remove_button("important");
			$tb->remove_button(0);
			$tb->remove_button(1);
			$tb->remove_button(2);
			$tb->remove_button(3);

			$arr["obj_inst"] = $tmp;
		}
	}

	function _user_list_tree($arr)
	{
		$this->_delegate_co_v($arr, "_get_unit_listing_tree");
	}

	function _user_list($arr)
	{
		$arr["prop"]["fields"] = array("image", "name", "phone", "email", "section", "rank");
		$this->_delegate_co_v($arr, "_get_human_resources");
	}

	function _delegate_co_v($arr, $fun)
	{
		$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MRP_OWNER");
		if ($o)
		{
			$tmp = $arr["obj_inst"];
			$arr["obj_inst"] = $o;
			$co = $o->instance();
			$co->callback_on_load($arr);

			$i = get_instance("applications/crm/crm_company_people_impl");
			$i->$fun($arr);
			$arr["obj_inst"] = $tmp;
		}
	}

	/** handler for person list delete. forwards to crm_company

		@attrib name=submit_delete_relations

	**/
	function submit_delete_relations($arr)
	{
		$this->_delegate_co($arr, "submit_delete_relations");
		return $arr["return_url"];
	}

	function callback_mod_reforb(&$arr)
	{
		$_GET_params_to_keep = array(
			"unit",
			"category",
			"cat",
			"pj_job",
			"mrp_tree_active_item",
			"timespan",
			"material",
			"people",
			"resource"
		);
		foreach($_GET_params_to_keep as $_GET_param_to_keep)
		{
			if(isset($_GET[$_GET_param_to_keep]))
			{
				$arr[$_GET_param_to_keep] = $_GET[$_GET_param_to_keep];
			}
			else
			{
				$arr[$_GET_param_to_keep] = "";
			}
		}

		$group = isset($_GET["group"]) ? $_GET["group"] : "";

		if ($group !== "grp_search" && $group !== "grp_search_proj" && $group !== "grp_search_cust")
		{
			$arr['return_url'] = get_ru();
		}

		aw_register_header_text_cb(array(&$this, "make_aw_header"));

		if ($group !== "grp_worksheet")
		{
			$arr["post_ru"] = post_ru();
		}

		if ($group === "grp_worksheet")
		{
			$arr["return_url"] = NULL;
		}
	}

	/** cuts the selected person objects

		@attrib name=cut_p

	**/
	function cut_p($arr)
	{
		return $this->_delegate_co($arr, "cut_p");
	}

	/** marks persons as important

		@attrib name=mark_p_as_important

	**/
	function mark_p_as_important($arr)
	{
		return $this->_delegate_co($arr, "mark_p_as_important");
	}

	/** copies the selected person objects

		@attrib name=copy_p

	**/
	function copy_p($arr)
	{
		return $this->_delegate_co($arr, "copy_p");
	}

	/** pastes the cut/copied person objects

		@attrib name=paste_p

	**/
	function paste_p($arr)
	{
		return $this->_delegate_co($arr, "paste_p");
	}

	function _delegate_co($arr, $fun)
	{
		$oo = obj($arr["id"]);
		$o = $oo->get_first_obj_by_reltype("RELTYPE_MRP_OWNER");
		if ($o)
		{
			$arr["id"] = $o->id();

			$o = $o->instance();
			$o->callback_on_load($arr);
			return $o->$fun($arr);
		}
	}

	function _user_mgr_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"caption" => t("Salvesta"),
			"action" => "submit_user_mgr_save"
		));
	}

	function _user_mgr_tree($arr)
	{
		$this->_delegate_co_v($arr, "_get_unit_listing_tree");
		// remove all professions from the tree
		$tv = $arr["prop"]["vcl_inst"];
		$tv->remove_item(CRM_ALL_PERSONS_CAT);

		foreach($tv->get_item_ids() as $id)
		{
			$item = $tv->get_item($id);
			if ((int) $item["class_id"] === CL_CRM_PROFESSION)
			{
				$tv->remove_item($id);
			}
		}
	}

	function _init_user_mgr(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "sel_resource",
			"caption" => t("Vali ressurss"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "all_resources",
			"caption" => t("N&auml;ita k&otilde;ikide ressurside t&ouml;id"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "dept_resources",
			"caption" => t("N&auml;ita osakonna ressurside t&ouml;id"),
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center"
		));
	}

	function _user_mgr($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_user_mgr($t);

		if (empty($arr["request"]["unit"]))
		{
			return;
		}

		$resource_tree = new object_tree(array(
			"parent" => $arr["obj_inst"]->prop ("resources_folder"),
			"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
			"sort_by" => "objects.jrk",
		));
		$l = $resource_tree->to_list();
		$resources = array("" => "");
		foreach($l->arr() as $o)
		{
			if ($o->class_id() == CL_MRP_RESOURCE)
			{
				$resources[$o->id()] = $o->name();
			}
		}

		$prof2res = array();
		$ol = new object_list(array(
			"class_id" => CL_MRP_RESOURCE_OPERATOR,
			"parent" => $arr["obj_inst"]->id()
		));
		foreach($ol->arr() as $o)
		{
			$prof2res[$o->prop("profession")] = $o->prop("resource");
			$all_resources[$o->prop("profession")] = $o->prop("all_resources");
			$dept_resources[$o->prop("profession")] = $o->prop("all_section_resources");
		}

		$unit = obj($arr["request"]["unit"]);
		$t->set_caption(sprintf(t("Ametikohad osakonnas '%s'"), $unit->name()));

		foreach($unit->connections_from(array("type" => "RELTYPE_PROFESSIONS")) as $c)
		{
			$all_res = $all_resources[$c->prop("to")];
			$t->define_data(array(
				"name" => $c->prop("to.name"),
				"sel_resource" => html::select(array(
					"name" => "user_mgr[".$c->prop("to")."][resource]",
					"options" => $resources,
					"value" => $prof2res[$c->prop("to")],
					"multiple" => 1,
					"size" => 4,
					"disabled" => $all_res,
				)),
				"all_resources" => html::checkbox(array(
					"name" => "user_mgr[".$c->prop("to")."][all_resources]",
					"value" => 1,
					"checked" => $all_resources[$c->prop("to")],
				)).html::hidden(array(
					"name" => "user_mgr[".$c->prop("to")."][old_all_resources]",
					"value" => $all_resources[$c->prop("to")]
				)),
				"dept_resources" => html::checkbox(array(
					"name" => "user_mgr[".$c->prop("to")."][all_section_resources]",
					"value" => 1,
					"checked" => $dept_resources[$c->prop("to")],
				)).html::hidden(array(
					"name" => "user_mgr[".$c->prop("to")."][old_all_section_resources]",
					"value" => $dept_resources[$c->prop("to")]
				)),
				"change" => html::get_change_url($c->prop("to"), array(), "Muuda")
			));
		}
	}

	/**

		@attrib name=submit_user_mgr_save

	**/
	function submit_user_mgr_save($arr)
	{
		if (!$this->can("view", $arr["unit"]))
		{
			return $arr["return_url"];
		}

		$unit = obj($arr["unit"]);

		// get all professions for selected unit
		$professions = new object_list($unit->connections_from(array(
			"type" => "RELTYPE_PROFESSION"
		)));

		// get existing operators for the selected unit
		$operators = new object_list(array(
			"class_id" => CL_MRP_RESOURCE_OPERATOR,
			"parent" => $arr["id"],
			"profession" => $professions->ids(),
			"unit" => $arr["unit"]
		));
		$existing_rels = array();
		foreach($operators->arr() as $o)
		{
			$existing_rels[$o->prop("profession")] = $o;
		}

		foreach(safe_array(ifset($arr, "user_mgr")) as $prof => $data)
		{
			if(!is_oid($prof))
			{
				continue;
			}

			if (!isset($existing_rels[$prof]))
			{
				// create new
				$rel = obj();
				$rel->set_class_id(CL_MRP_RESOURCE_OPERATOR);
				$rel->set_parent($arr["id"]);
				$prof_o = obj($prof);
				$res_o = obj($res);
				$rel->set_name("Ametinimetus ".$prof_o->name()." => ressurss ".$res_o->name());
				$rel->set_prop("profession", $prof);
				$rel->set_prop("resource", $res);
				$rel->set_prop("unit", $arr["unit"]);
				$rel->save();
			}
			else
			{
				// change current
				$rel = $existing_rels[$prof];
				if(empty($data["all_resources"]))
				{
					$rel->set_prop("resource", $data["resource"]);
				}
				$rel->set_prop("all_resources", empty($data["all_resources"]) ? 0 : 1);
				$rel->set_prop("all_section_resources", empty($data["all_section_resources"]) ? 0 : 1);
				$rel->save();
			}
		}

		// cleverly return
		return $arr["return_url"];
	}

	function create_customers_toolbar($arr)
	{
		$co = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_MRP_OWNER");
		if (!$co)
		{
			return;
		}

		$t = $arr["prop"]["vcl_inst"];

		$t->add_menu_button(array(
			"name" => "add_menu",
			"tooltip" => t("Uus"),
		));

		$t->add_menu_item(array(
			"parent" => "add_menu",
			"text" => t('Kategooria'),
			"link" => html::get_new_url(CL_CRM_CATEGORY, $co->id(), array(
				"alias_to" => (empty($arr["request"]["cat"]) ? $co->id() : $arr["request"]["cat"]),
				"reltype" => (empty($arr["request"]["cat"]) ? 30 : 2),
				"return_url" => get_ru()
			)),
		));

		// if (false && is_oid($arr["request"]["cat"]))
		if (isset($arr["request"]["cat"]) and is_oid($arr["request"]["cat"]))
		{
			$t->add_menu_item(array(
				"parent" => "add_menu",
				"text" => t('Klient'),
				"link" => html::get_new_url(CL_CRM_COMPANY, $co->id(), array(
					"alias_to" => ($arr["request"]["cat"] ? $arr["request"]["cat"] : $co->id()),
					"reltype" => 3,
					"return_url" => get_ru()
				))
			));
		}

		$t->add_button(array(
			"name" => "save_priors",
			"tooltip" => t("Salvesta prioriteedid"),
			"img" => "save.gif",
			"url" => "javascript:update_priors();"
		));

		/*$t->add_button(array(
			"name" => "delete",
			"tooltip" => t("Kustuta"),
			"img" => "delete.gif",
			"action" => "delete_customers"
		));*/
	}

	function create_customers_tree($arr)
	{
		$co_id = $arr["obj_inst"]->prop("owner");
		if (!$this->can("view", $co_id))
		{
			return;
		}
		else
		{
			$co = obj($co_id);
		}

		$t = $arr["prop"]["vcl_inst"];
		$all_customers = $co->get_all_customer_ids();

		$cases = $arr["obj_inst"]->get_all_mrp_cases_data();
		$customers = new object_list();
		$customer_count = array();
		foreach($cases as $data)
		{
			if (isset($customer_count[$data["customer"]]))
			{
				$customer_count[$data["customer"]]++;
			}
			else
			{
				$customer_count[$data["customer"]] = 1;
			}
		}

		foreach($all_customers as $id)//ei k6ike panna, sest miskeid nulle ja asju tuleb alati sisse ja siis annab errorit
		{
			try
			{
				$customers->add($id);
			}
			catch (Exception $e)
			{
			}
		}

		$reload = array(
			"layouts" => array("customers_search_table"),
			"props" => array("projects_list"),
		);

		$reload["params"]["cat"] = $reload["params"]["cust"] = NULL;
		$t->add_item(0, array(
			"id" => "cats",
			"name" => t("Kliendid kategooriate kaupa"),
			"reload" => $reload,
		));

		foreach($co->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
		{
			$count = $this->_req_create_customers_tree($c->to(), $t);
			$nm = $c->prop("to.name")." (".$count.")";
			$reload["params"]["cat"] = $c->prop("to");
			$reload["params"]["cust"] = NULL;
			$t->add_item("cats", array(
				"id" => $c->prop("to"),
				"name" => $nm,
				"reload" => $reload,
			));
		}

		if (isset($arr["request"]["cs_name"]))
		{
			$t->set_selected_item(0); // doesn't work here. cat&cust select clearing for search request solved by adding js in cs_submit get_prop call.
		}
		elseif (!empty($arr["request"]["cust"]))
		{
			$t->set_selected_item($arr["request"]["cust"]);
		}
		elseif (!empty($arr["request"]["cat"]))
		{
			$t->set_selected_item($arr["request"]["cat"]);
		}

		$reload["params"]["cat"] = NULL;
		$reload["params"]["cust"] = NULL;
		$t->add_item(0, array(
			"id" => "alph",
			"name" => t("Kliendid A - Z"),
			"reload" => $reload,
		));
		$A_to_Z = array();
//		$customers = $co->get_customers_by_customer_data_objs();
//		$customers -> add($arr["obj_inst"]->get_all_mrp_customers());

		foreach($customers->names() as $oid => $name)
		{
			$char = strtoupper(substr(trim($name), 0, 1));
			if(!isset($A_to_Z[$char]))
			{
				$A_to_Z[$char] = 1;
			}
			else
			{
				$A_to_Z[$char]++;
			}
			if(!isset($customer_count[$oid]))
			{
				$customer_count[$oid] = 0;
			}
			$nm = parse_obj_name($name)." (".$customer_count[$oid].")";
			if(isset($arr["request"]["cust"]) and $arr["request"]["cust"] == $oid)
			{
				$t->set_selected_item($oid);
			}

			$reload["params"]["cat"] = NULL;
			$reload["params"]["cust"] = $oid;
			$t->add_item("alph_".$char, array(
				"id" => $oid,
				"name" => $nm,
				"iconurl" => icons::get_icon_url(CL_CRM_COMPANY),
				"reload" => $reload,
			));
		}
		ksort($A_to_Z);
		foreach($A_to_Z as $char => $count)
		{
			$nm = $char." (" . $count. ")";
			if(isset($arr["request"]["alph"]) and $arr["request"]["alph"] == $char)
			{
				$t->set_selected_item("alph_".$char);
			}
			$reload["params"]["cat"] = NULL;
			$reload["params"]["cust"] = NULL;
			$reload["params"]["alph"] = $char;
			$t->add_item("alph", array(
				"id" => "alph_".$char,
				"name" => $nm,
				"reload" => $reload,
			));
		}
	}

	function _req_create_customers_tree($co, &$t)
	{
		foreach($co->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
		{
			$nm = $c->prop("to.name");
			$t->add_item($co->id(), array(
				"id" => $c->prop("to"),
				"name" => $nm,
				"reload" => array(
					"layouts" => array("customers_search_table"),
					"props" => array("projects_list"),
					"params" => array(
						"cat" => $c->prop("to"),
						"cust" => null
					),
				),
			));
			if(isset($arr["request"]["cat"]) and $arr["request"]["cat"] == $c->prop("to"))
			{
				$t->set_selected_item($c->prop("to"));
			}
			$this->_req_create_customers_tree($c->to(), $t);
		}

		$count = 0;
		foreach($co->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
		{
			$nm = $c->prop("to.name");
			$t->add_item($co->id(), array(
				"id" => $c->prop("to"),
				"name" => $nm,
				"reload" => array(
					"layouts" => array("customers_search_table"),
					"props" => array("projects_list"),
					"params" => array(
						"cat" => null,
						"cust" => $c->prop("to")
					),
				),
				"iconurl" => icons::get_icon_url($c->prop("to.class_id"))
			));
			if(isset($arr["request"]["cust"]) and $arr["request"]["cust"] == $c->prop("to"))
			{
				$t->set_selected_item($c->prop("to"));
			}
			$count++;
		}
		return $count;
	}

	function _init_cust_list_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "email",
			"caption" => t("E-mail"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "contact_person",
			"caption" => t("Kontaktisik"),
			"sortable" => 1,
			"align" => "center"
		));


		$t->define_field(array(
			"name" => "cust_manager",
			"caption" => t("Kliendihaldur"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_field(array(
			"name" => "priority",
			"caption" => t("Prioriteet"),
			"sortable" => 1,
			"align" => "center"
		));

		$t->define_chooser(array(
			"name" => "select",
			"field" => "oid"
		));
	}

	function create_customers_list($arr)
	{
		$retval = PROP_IGNORE;
		$co_id = $arr["obj_inst"]->prop("owner");
		if (!$this->can("view", $co_id))
		{
			return;
		}
		else
		{
			$co = obj($co_id);
		}

		if (empty($arr["request"]["cust"]) and !isset($arr["request"]["cs_name"]) || $arr["request"]["alph"])
		{
			$t = $arr["prop"]["vcl_inst"];
			$this->_init_cust_list_t($t);

			$customers = new object_list();

			if (isset($arr["request"]["cat"]) and is_oid($arr["request"]["cat"]))
			{
				// get customers from cat
				$cat = obj($arr["request"]["cat"]);
				$t->set_caption(sprintf(t("Kliendid kategoorias '%s'"), $cat->name()));
				foreach($cat->connections_from(array("type" => "RELTYPE_CUSTOMER")) as $c)
				{
					$customers->add($c->prop("to"));
				}
			}
			elseif(!empty($arr["request"]["alph"]))
			{
				$t->set_caption(sprintf(t("'%s' t&auml;hega algavate klientide nimekiri"), $arr["request"]["alph"]));
				$customers_data = $co->get_all_customer_ids(array("name" => $arr["request"]["alph"]));
				foreach($customers_data as $customer)
				{
					if($this->can("view" , $customer))
					{
						$customers->add($customer);
					}
				}
			}


			if(!empty($arr["request"]["timespan"]))
			{
				$time = $this->get_hours_from_to();
				$filter = array(
					"class_id" => CL_MRP_CASE,
					"customer" => $customers->ids(),
				);
				$filter["due_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $time[0], $time[1], "int");
				$ol = new object_list($filter);
				$project_customers = array();
				foreach($ol->arr() as $o)
				{
					$project_customers[$o->prop("customer")] = $o->prop("customer");
				}
			}

			foreach($customers->arr() as $cust)
			{
				if($arr["request"]["timespan"] && !in_array($cust->id() , $project_customers))
				{
					continue;
				}

				$cust_rel = $cust->find_customer_relation($co);
				$t->define_data(array(
					"name" => html::get_change_url($cust->id(), array("return_url" => get_ru()), $cust->name()),
					"address" => $cust->prop_str("contact"),
					"phone" => join (" ," , $cust->get_phones()),
					"email" => $cust->get_mail(),
					"oid" => $cust->id(),
					"priority" => html::textbox(array("size" => 5 , "name" => "priority[".$cust->id()."]" , "value" => is_object($cust_rel) ? $cust_rel->prop("priority") : "0")),
					"contact_person" => is_object($cust_rel) && $cust_rel->prop("buyer_contact_person")?  html::get_change_url($cust_rel->prop("buyer_contact_person"), array("return_url" => get_ru()), $cust_rel->prop("buyer_contact_person.name")) : "",
					"cust_manager" => is_object($cust_rel) && $cust_rel->prop("client_manager") ? html::get_change_url($cust_rel->prop("client_manager"), array("return_url" => get_ru()), $cust_rel->prop("client_manager.name")) : "",
				));
			}

			$retval = PROP_OK;
		}

		return $retval;
	}

	function _init_cust_list_proj_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Number"),
			"align" => "center",
			"sortable" => 1,
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "comment",
			"caption" => t("Kommentaar"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Algus"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("T&auml;htaeg"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
		$t->define_field(array(
			"name" => "planned",
			"caption" => t("Planeeritud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y H:i"
		));
	}

	function create_customers_list_proj($arr)
	{
		if (isset($arr["request"]["cust"]) and is_oid($arr["request"]["cust"]) and !isset($arr["request"]["cs_name"]))
		{
			$t = $arr["prop"]["vcl_inst"];
			$this->_init_cust_list_proj_t($t);

			$cust = obj($arr["request"]["cust"]);

			$filter = array(
				"class_id" => CL_MRP_CASE,
				"customer" => $cust->id()
			);

			if($arr["request"]["timespan"])
			{
				switch($arr["request"]["timespan"])
				{
					case "current_week":
						list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
						$from = mktime(0, 0, 0, $M, $D-$N+1, $Y);
						$to = mktime(23, 59, 59, $M, $D+7-$N, $Y);
						break;
					case "last_week":
						list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
						$from = mktime(0, 0, 0, $M, $D-$N-6, $Y);
						$to = mktime(23, 59, 59, $M, $D-$N, $Y);
					break;

					case "current_month":
						list($Y, $M) = explode("-", date("Y-n"));
						$from = mktime(0, 0, 0, $M, 1, $Y);
						$to = mktime(23, 59, 59, $M+1, 0, $Y);
					break;

					case "last_month":
						list($Y, $M) = explode("-", date("Y-n"));
						$from = mktime(0, 0, 0, $M-1, 1, $Y);
						$to = mktime(23, 59, 59, $M, 0, $Y);
						break;
				}
				$filter["due_date"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $from, $to, "int");
			}

			$ol = new object_list($filter);

			foreach($ol->arr() as $case)
			{
				$t->define_data(array(
					"name" => html::obj_change_url($case),
					"comment" => $case->comment(),
					"start" => $case->prop("starttime"),
					"end" => $case->prop("due_date"),
					"planned" => $case->prop("planned_date")
				));
			}
			$t->set_default_sortby("name");
			$t->set_caption(sprintf(t("Kliendi '%s' projektid"), $cust->name()));
			$t->sort_by();
			return PROP_OK;
		}

		return PROP_IGNORE;
	}

	/** imports given project from prisma db

		@attrib name=import_project

		@param id required type=int

	**/
	function import_project($arr)
	{
		$i = get_instance(CL_MRP_PRISMA_IMPORT);
		$id = $i->import_project($arr["id"]);

		header("Location: ".html::get_change_url($id)."&return_url=".urlencode(html::get_change_url(aw_ini_get("prisma.ws"))."&group=grp_projects"));
		die();
	}

	function _init_printer_jobs_t(&$t, $grp)
	{
		if ("grp_printer_done" == $grp)
		{
			$t->define_field(array(
				"name" => "tm_end",
				"caption" => t("L&otilde;pp"),
				"type" => "time",
				"align" => "center",
				"format" => "d.m.y H:i",
				"numeric" => 1,
				"chgbgcolor" => "bgcol",
			));
		}
		else
		{
			$t->define_field(array(
				"name" => "tm",
				"caption" => t("Algus"),
				"type" => "time",
				"align" => "center",
				"format" => "d.m.y H:i",
				"numeric" => 1,
				"chgbgcolor" => "bgcol",
				"sortable" => 1,
				"nowrap" => 1
			));
		}

		$t->define_field(array(
			"name" => "length",
			"caption" => t("Pikkus"),
			"align" => "center",
			"numeric" => 1,
			"chgbgcolor" => "bgcol",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "status",
			"caption" => t("Staatus"),
			"chgbgcolor" => "bgcol",
			"align" => "center",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "project",
			"caption" => t("Projekt"),
			"align" => "center",
			"chgbgcolor" => "bgcol",
			"sortable" => 1,
			"numeric" => 1,
			"callback" => array(&$this, "pj_project_field_callback"),
			"callb_pass_row" => true
		));

		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"align" => "center",
			"chgbgcolor" => "bgcol",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "proj_comment",
			"caption" => t("Projekti nimi"),
			"align" => "center",
			"chgbgcolor" => "bgcol",
		));

		$t->define_field(array(
			"name" => "job_comment",
			"caption" => t("Kommentaar"),
			"align" => "center",
			"chgbgcolor" => "bgcol",
			"sortable" => 1
		));

		$t->define_field(array(
			"name" => "resource",
			"caption" => t("Ressurss"),
			"align" => "center",
			"chgbgcolor" => "bgcol",
			"sortable" => 1
		));

		/*$t->define_field(array(
			"name" => "worker",
			"caption" => t("Teostaja"),
			"align" => "center",
			"chgbgcolor" => "bgcol",
			"sortable" => 1
		));*/

		$t->define_field(array(
			"name" => "job",
			"caption" => t("Ava"),
			"align" => "center",
			"chgbgcolor" => "bgcol",
		));
	}

	private function get_all_cat_resources($id)
	{
		$resource_tree_filter = array(
			"parent" => $id,
			"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
			"sort_by" => "objects.jrk",
		);
		$resource_tree = new object_tree($resource_tree_filter);
		return $resource_tree->ids();
	}

	function _printer_jobs($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		if(isset($arr["request"]["pj_job"]) && $this->can("view", $arr["request"]["pj_job"]))
		{
			$t->set_header(sprintf(t("T&ouml;&ouml; \"%s\" detailvaade"), obj($arr["request"]["pj_job"])->name()));
			$t->set_footer(sprintf(t("T&ouml;&ouml; \"%s\" detailvaade"), obj($arr["request"]["pj_job"])->name()));
		}
		else
		{
			$caps = array(
				"grp_printer_current" => t("Jooksvad t&ouml;&ouml;d"),
				"grp_printer_old" => t("Tegemata t&ouml;&ouml;d"),
				"grp_printer_done" => t("Tehtud t&ouml;&ouml;d"),
				"grp_printer_aborted" => t("Katkestatud t&ouml;&ouml;d"),
				"grp_printer_in_progress" => t("K&otilde;ik t&ouml;&ouml;s olevad"),
				"grp_printer_startable" => t("K&otilde;ik t&ouml;&ouml;d mida oleks v&otilde;imalik alustada"),
				"grp_printer_notstartable" => t("T&ouml;&ouml;d, mida ei ole veel v&otilde;imalik alustada"),
			);
			$grp = isset($_GET["branch_id"]) ? $_GET["branch_id"] : "grp_printer_current";
			if(array_key_exists($grp, $caps))
			{
				$t->set_header(html::bold($caps[$grp]));
				$t->set_footer(html::bold($caps[$grp]));
			}
		}


		$grp = isset($arr["prop"]["branch_id"]) ? $arr["prop"]["branch_id"] : "grp_printer_current";
		$this->_init_printer_jobs_t($t, $grp);


		if(!empty($arr["request"]["mrp_tree_active_item"]))
		{
			$res = $this->get_all_cat_resources($arr["request"]["mrp_tree_active_item"]);
		}
		else
		{
			$res = $this->get_cur_printer_resources(array(
				"ws" => $arr["obj_inst"]
			));
		}

		$per_page = $arr["obj_inst"]->prop("pv_per_page");
		$proj_states = false;
		$page = isset($arr["request"]["printer_job_page"]) ? (int) $arr["request"]["printer_job_page"] : 0;
		$limit = ($page*$per_page).",".$per_page;

		if(!isset($arr["request"]["branch_id"]))
		{
			$arr["request"]["branch_id"] = "";
		}
		$arr["request"]["printer_job_page"] = isset($arr["request"]["printer_job_page"]) ? $arr["request"]["printer_job_page"] : 0;

		switch ($arr["request"]["branch_id"])
		{
			case "grp_printer_done":
				$states = array(MRP_STATUS_DONE);
				$default_sortby = "mrp_job.started";
				if (empty($arr["request"]["sort_order"]))
				{
					$arr["request"]["sort_order"] = "desc";
				}

				if (isset($arr["request"]["sortby"]) and $arr["request"]["sortby"] === "tm")
				{
					$arr["request"]["sortby"] = "started";
				}
				break;

			case "grp_printer_aborted":
				$states = array(MRP_STATUS_ABORTED);
				$default_sortby = "mrp_job.started";
				break;

			case "":
			case "grp_printer_current":
				$default_sortby = "mrp_schedule.starttime";
				$states = array(MRP_STATUS_PLANNED,MRP_STATUS_INPROGRESS,MRP_STATUS_PAUSED,MRP_STATUS_SHIFT_CHANGE);
				$proj_states = array(MRP_STATUS_NEW,MRP_STATUS_PLANNED,MRP_STATUS_INPROGRESS,MRP_STATUS_PAUSED);
				break;

			case "grp_printer_in_progress":
				$default_sortby = "mrp_schedule.starttime";
				$states = array(MRP_STATUS_INPROGRESS,MRP_STATUS_PAUSED,MRP_STATUS_SHIFT_CHANGE);
				break;

			case "grp_printer_startable":
				$default_sortby = "mrp_schedule.starttime";
				$states = array(MRP_STATUS_PLANNED,MRP_STATUS_INPROGRESS,MRP_STATUS_PAUSED,MRP_STATUS_SHIFT_CHANGE);
				$proj_states = array(MRP_STATUS_NEW,MRP_STATUS_PLANNED,MRP_STATUS_INPROGRESS,MRP_STATUS_PAUSED);
				$limit = (((int)$arr["request"]["printer_job_page"])*$per_page).",200";
				break;

			case "grp_printer_notstartable":
				$default_sortby = "mrp_schedule.starttime";
				$states = array(MRP_STATUS_PLANNED,MRP_STATUS_PAUSED,MRP_STATUS_SHIFT_CHANGE);
				$proj_states = array(MRP_STATUS_NEW,MRP_STATUS_PLANNED,MRP_STATUS_INPROGRESS,MRP_STATUS_PAUSED);
				$limit = (((int)$arr["request"]["printer_job_page"])*$per_page).",200";
				break;
		}

		$sby = isset($arr["request"]["sortby"]) ? $arr["request"]["sortby"] : "";
		if ($sby === "")
		{
			$sby = $default_sortby;
		}
		else
		{
			// map to db table
			switch($sby)
			{
				case "started":
					$sby = "mrp_job.started";
					break;

				case "tm":
					$sby = "mrp_schedule.starttime";
					break;

				case "length":
					$sby = "mrp_job.length";
					break;

				case "status":
					$sby = "mrp_job.state";
					break;

				case "project":
					$sby = "CAST(objects_826_project.name AS UNSIGNED)";
					break;

				case "job_comment":
					$sby = "objects.comment";
					break;

				case "resource":
					$sby = "mrp_job.resource";
					break;

				case "customer":
					$sby = "objects_828_customer.name";
					break;
			}
		}

		if ($sby !== "" && !empty($arr["request"]["sort_order"]))
		{
			$sort_order = strtoupper($arr["request"]["sort_order"]);
			$sort_order = "ASC" === $sort_order ? "ASC" : "DESC";
			$sby .= " " . $sort_order;
		}

		// now, if the session contans [mrp][do_pv_proj_s] then we must get a list of all the jobs in the current view
		// then iterate them until we find a job with the requested project
		// and then figure out the page number and finally, redirect the user to that page.
		// this sort of sucks, but I can't figure out how to do the count in sql..
		if (!empty($_SESSION["mrp"]["ps_project"]))
		{
			// this needs to get done abit differently - we need to find all the jobs
			// that are part of this project for the current resource(s)
			// and if none are under the current tab, then switch to another tab,
			// where they can be found
			// so, list the jobs
			// but first we need the oid of the project
			$proj2oid = new object_list(array("name" => $_SESSION["mrp"]["ps_project"], "class_id" => CL_MRP_CASE));
			if ($proj2oid->count() && count($res))
			{
				$s_proj = $proj2oid->begin();

				$q = "SELECT * FROM mrp_job WHERE
					project = '".$s_proj->id()."' AND
					resource IN (".join(",", $res).")";
				$this->db_query($q);

				$f_j_state = NULL;
				$view = "";
				while ($row = $this->db_next())
				{
					if ($f_j_state === NULL)
					{
						$f_j_state = $row["state"];
					}

					if (in_array($row["state"], $states))
					{
						$view = $_GET["branch_id"];
					}
				}

				if ($view === "")
				{
					switch($f_j_state)
					{
						case MRP_STATUS_DONE:
							$view = "grp_printer_done";
							break;

						case MRP_STATUS_ABORTED:
							$view = "grp_printer_aborted";
							break;

						default:
							$view = "grp_printer";
					}
				}

				if ($view !== $_GET["branch_id"])
				{
					header("Location: ".aw_url_change_var("branch_id", $view));
					die();
				}

				$find_proj = $_SESSION["mrp"]["ps_project"];
				unset($_SESSION["mrp"]["ps_project"]);
				$_SESSION["mrp"]["pv_s_hgl"] = $s_proj->id();
				$jobs = $this->get_next_jobs_for_resources(array(
					"resources" => $res,
					"states" => $states,
					"sort_by" => $sby,
					"proj_states" => $proj_states
				));

				$count = 0;
				foreach($jobs as $job)
				{
					$count++;
					$proj = obj($job["project"]);
					if ($proj->name() == $find_proj)
					{
						$page = floor($count / $per_page);
						header("Location: ".aw_url_change_var("printer_job_page", $page));
						die();
					}
				}
			}
		}

		$jobs = $this->get_next_jobs_for_resources(array(
			"resources" => $res,
			"limit" => $limit,
			"states" => $states,
			"sort_by" => $sby,
			"proj_states" => $proj_states,
			"timespan" => automatweb::$request->arg("timespan"),
		));

		$workers = $this->get_workers_for_resources($res);

		$mrp_case = get_instance(CL_MRP_CASE);

		$cnt = 0;
		foreach($jobs as $job)
		{
			if (!$this->can("view", $job["project"]))
			{
				continue;
			}
			$cnt++;
			$proj = obj($job["project"]);

			$workers_str = array();
			if(isset($workers[$job["resource"]]))
			{
				foreach(safe_array($workers[$job["resource"]]) as $person)
				{
					if ($this->can("edit", $person->id()))
					{
						$workers_str[] = html::obj_change_url($person);
					}
					else
					{
						$workers_str[] = $person->name();
					}
				}
			}

			$custo = "";
			$cust = $proj->get_first_obj_by_reltype("RELTYPE_MRP_CUSTOMER");
			if (is_object($cust))
			{
				if ($this->can("edit", $cust->id()))
				{
					$custo = html::obj_change_url($cust);
				}
				else
				{
					$custo = $cust->name();
				}
			}

			$mrp_job = obj($job["oid"]);

			### set colours
			if ($job["state"] == MRP_STATUS_DONE)
			{
				// dark green
				$bgcol = $this->pj_colors["done"];
			}
			elseif ($job["state"] == MRP_STATUS_INPROGRESS)
			{
				$bgcol = $this->pj_colors["can_not_start"];
			}
			elseif ($mrp_job->can_start())
			{
				// light green
				$bgcol = $this->pj_colors["can_start"];
			}
			else
			{
				if ($mrp_job->job_prerequisites_are_done())
				{
					$bgcol = $this->pj_colors["resource_in_use"];
				}
				else
				{
					// light red
					$bgcol = $this->pj_colors["can_not_start"];
				}
			}

			if ($arr["request"]["branch_id"] === "grp_printer_startable" && $bgcol == $this->pj_colors["can_not_start"])
			{
				continue;
			}

			if ($arr["request"]["branch_id"] === "grp_printer_notstartable" && ($bgcol == $this->pj_colors["can_start"] || $cnt > 5))
			{
				continue;
			}

			if (isset($job["project"]) && isset($_SESSION["mrp"]["pv_s_hgl"]) && $job["project"] == $_SESSION["mrp"]["pv_s_hgl"] || !isset($job["project"]) && !isset($_SESSION["mrp"]["pv_s_hgl"]))
			{
				$bgcol = $this->pj_colors["search_result"];
			}

			$state = '<span style="color: ' . self::$state_colours[$job["state"]] . ';">' . $this->states[$job["state"]] . '</span>';

			$start = $end = $length = 0;
			### get length, end and start according to job state
			switch ($arr["request"]["branch_id"])
			{
				case "grp_printer_done":
					$start = $job["started"];
					$end = $job["finished"];
					$length = $job["finished"] - $job["started"];
					break;

				case "grp_printer_aborted":
					$start = $job["started"];
					$end = "...";//!!! lugeda logist v kuskilt abortimise aeg
					$length = 0;//!!!
					break;

				case "":
				case "grp_printer_current":
				case "grp_printer_startable":
					$start = $job["starttime"];
					$end = $job["starttime"] + $job["length"];
					$length = $job["length"];
					break;
			}


			$len  = sprintf ("%02d", floor($length / 3600)).":";
			$len .= sprintf ("%02d", floor(($length % 3600) / 60));

			$resource_str = $job["resource(CL_MRP_RESOURCE).name"];
			if ($this->can("edit", $job["resource"]))
			{
				$resource_str = html::obj_change_url($job["resource"]);
			}

			$project_str = $proj->name();
			$proj_com = $proj->comment();
			if ($this->can("edit", $proj->id()))
			{
				$proj_com = html::get_change_url(
					$proj->id(),
					array("return_url" => get_ru()),
					parse_obj_name($proj->comment())
				);
			}

			$comment = $job["comment"];
			if (strlen($comment) > 20)
			{
				$comment = html::href(array(
					"url" => "javascript:void(0)",
					"caption" => substr($comment, 0, 20),
					"title" => $comment
				));
			}
			### ...
			$t->define_data(array(
				"tm" => $start,
				"tm_end" => $end,
				"length" => $len,
				"job" => html::href(array(
					"caption" => "<span style=\"font-size: 15px;\">".t("Ava")."</span>",
					/*
					"url" => aw_url_change_var(array(
						"pj_job" =>  $job["oid"],
						"return_url" => get_ru()
					)),
					*/
					"reload" => array(
						"layouts" => array("printer_right"),
						"params" => array(
							"pj_job" =>  $job["oid"]
						),
					),
				)),
				"resource" => $resource_str,
				"worker" => join(", ",$workers_str),
				"project" => $project_str,
				"project_id" => $proj->id(),
				"proj_pri" => $proj->prop("project_priority"),
				"proj_comment" => $proj_com,
				"customer" => $custo,
				"status" => $state,
				"bgcol" => $bgcol,
				"job_comment" => $comment
			));
		}

		if ("grp_printer_done" === $grp)
		{
			$t->set_default_sortby("tm_end");
			if (aw_global_get("sortby") === "tm")
			{
				aw_global_set("sortby", "tm_end");
			}
		}
		else
		{
			$t->set_default_sortby("tm");
		}

		if (isset($arr["request"]["sort_order"]) and $arr["request"]["sort_order"] === "desc")
		{
			$t->set_default_sorder("desc");
		}

		$t->sort_by();
		$t->set_sortable(false);
	}

	function get_cur_printer_resources_desc($arr)
	{
		if (aw_global_get("mrp_operator_use_resource"))
		{
			$o = obj(aw_global_get("mrp_operator_use_resource"));
			return $o->name();
		}
		// get person
		$u = get_instance(CL_USER);
		$person = obj($u->get_current_person());

		// get professions for person
		$profs = new object_list($person->connections_from(array(
			"type" => "RELTYPE_RANK"
		)));

		// if current person has no rank, return all resources
		if (!$profs->count())
		{
			return "";
		}

		// get resource operators for professions
		$ops = new object_list(array(
			"profession" => $profs->ids(),
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_MRP_RESOURCE_OPERATOR
		));

		// get resources
		$ret = array();
		foreach($ops->arr() as $op)
		{
			if ($this->can("view", $op->prop("resource")))
			{
				$reso = obj($op->prop("resource"));

				if (in_array($reso->prop("state"), $this->active_resource_states))
				{
					$ret[] = $reso->name();
				}
			}
		}

		// if no resources are given, check if the current user should have
		// all resources displayed, the department's resources displayed
		// or none
		$ws = $arr["ws"];

		$all_res = $ws->meta("umgr_all_resources");
		foreach($profs->arr() as $prof)
		{
			if (isset($all_res[$prof->id()]) && $all_res[$prof->id()] == 1)
			{
				return t("K&otilde;ik ressursid");
			}
		}

		$dept_res = $ws->meta("umgr_dept_resources");
		foreach($profs->arr() as $prof)
		{
			if ($dept_res[$prof->id()] == 1)
			{
				return t("Osakonna ressursid");
			}
		}

		return join(", ", $ret);
	}

	function get_cur_printer_resources($arr)
	{
		if (aw_global_get("mrp_operator_use_resource") && !$arr["ign_glob"])
		{
			return array(aw_global_get("mrp_operator_use_resource") => aw_global_get("mrp_operator_use_resource"));
		}

		// get person
		$u = get_instance(CL_USER);
		$person = obj($u->get_current_person());

		// get professions for person
		$profs = new object_list($person->connections_from(array(
			"type" => "RELTYPE_RANK"
		)));

		// if current person has no rank, return all resources
		if (!$profs->count())
		{
			return array();
		}

		// get resource operators for professions
		$ops = new object_list(array(
			"profession" => $profs->ids(),
			"lang_id" => array(),
			"site_id" => array(),
			"class_id" => CL_MRP_RESOURCE_OPERATOR
		));

		// get resources
		$ret = array();
		foreach($ops->arr() as $op)
		{
			foreach(safe_array($op->prop("resource")) as $resource_id)
			{
				if ($this->can("view", $resource_id))
				{
					$ret[$resource_id] = $resource_id;
				}
			}
		}

		// if no resources are given, check if the current user should have
		// all resources displayed, the department's resources displayed
		// or none
		if (count($ret) == 0)
		{
			$ws = $arr["ws"];

			$all_res = $ws->meta("umgr_all_resources");
			$dept_res = $ws->meta("umgr_dept_resources");

			foreach($profs->arr() as $prof)
			{
				if ($all_res[$prof->id()] == 1)
				{
					// return all resources
					$ol = new object_list(array(
						"class_id" => CL_MRP_RESOURCE,
						"lang_id" => array(),
						"site_id" => array()
					));

					foreach ($ol->arr() as $res_o)
					{
						if (!in_array($res_o->prop("state"), $this->active_resource_states))
						{
							$ol->remove($res_o);
						}
					}

					return $this->make_keys($ol->ids());
				}
				else
				if ($dept_res[$prof->id()] == 1)
				{
					// get the user's department
					foreach($person->connections_from(array("RELTYPE_SECTION")) as $c)
					{
						$sect = $c->to();

						// get all resources for section
						$ol = new object_list(array(
							"class_id" => CL_MRP_RESOURCE_OPERATOR,
							"unit" => $sect->id()
						));
						foreach($ol->arr() as $o)
						{
							$ret[$o->prop("resource")] = $o->prop("resource");
						}
					}
				}
			}
		}

		foreach ($ret as $res_oid)
		{
			$res_o = new object($res_oid);

			if (!in_array($res_o->prop("state"), $this->active_resource_states))
			{
				unset($ret[$res_oid]);
			}
		}

		return $ret;
	}

	/** returns array of job objects for the given professions in time order

		@comment
			resources - array of recource id's to return jobs for
			limit - limit number of returned data
			ws - workspace object
	**/
	function get_next_jobs_for_resources($arr)
	{
		if (!isset($arr["resources"]) || !is_array($arr["resources"]) || count($arr["resources"]) === 0)
		{
			return array();
		}

		if (empty($arr["minstart"]))
		{
			$arr["minstart"] = 100;
		}

		$filt = array(
			"resource" => $arr["resources"],
			"class_id" => CL_MRP_JOB,
			"site_id" => array(),
			"lang_id" => array(),
			"starttime" => new obj_predicate_compare(OBJ_COMP_GREATER, -1),
//!!!
			"sort_by" => isset($arr["sort_by"]) ? $arr["sort_by"] : "mrp_schedule.starttime",
//!!!
		);

		if($arr["timespan"])
		{
			switch($arr["timespan"])
			{
				case "current_week":
					list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
					$from = mktime(0, 0, 0, $M, $D-$N+1, $Y);
					$to = mktime(23, 59, 59, $M, $D+7-$N, $Y);
					break;
				case "last_week":
					list($Y, $M, $D, $N) = explode("-", date("Y-n-j-N"));
					$from = mktime(0, 0, 0, $M, $D-$N-6, $Y);
					$to = mktime(23, 59, 59, $M, $D-$N, $Y);
				break;

				case "current_month":
					list($Y, $M) = explode("-", date("Y-n"));
					$from = mktime(0, 0, 0, $M, 1, $Y);
					$to = mktime(23, 59, 59, $M+1, 0, $Y);
				break;

				case "last_month":
					list($Y, $M) = explode("-", date("Y-n"));
					$from = mktime(0, 0, 0, $M-1, 1, $Y);
					$to = mktime(23, 59, 59, $M, 0, $Y);
					break;
			}
			$filt["starttime"] = new obj_predicate_compare(OBJ_COMP_BETWEEN, $from, $to, "int");
		}

		if(!empty($arr["limit"]))
		{
			$filt["limit"] = $arr["limit"];
		}

		if (!empty($arr["states"]))
		{
			$filt["state"] = $arr["states"];
		}

		$filt["CL_MRP_JOB.project(CL_MRP_CASE).name"] = "%";
		// this also does or is null, cause the customer can be null
		$filt["CL_MRP_JOB.project(CL_MRP_CASE).customer.name"] = new obj_predicate_not(1);

		if (!empty($arr["proj_states"]))
		{
			$filt["CL_MRP_JOB.project(CL_MRP_CASE).state"] = $arr["proj_states"];
		}

		if(!empty($arr["filter"]) && is_array($arr["filter"]))
		{
			$filt = array_merge($filt, $arr["filter"]);
		}

		$jobs = new object_data_list(
			$filt,
			array(
				CL_MRP_JOB => array(
					"oid",
					"comment",
					"state",
					"project",
					"resource",
					"started",
					"finished",
					"aborted",
					"length",
					"starttime",
					"remaining_length",
					"exec_order",
					"resource(CL_MRP_RESOURCE).name",
				),
			)
		);
		$ret = array();
		foreach($jobs->arr() as $o)
		{
			if ($this->can("view", $o["resource"]))
			{
				$ret[$o["oid"]] = $o;
			}
		}
		return $ret;
	}

	/** reverse lookup, from resources to persons

		@comment
			res - array of resource id's to look up
	**/
	function get_workers_for_resources($res)
	{
		$persons = array();
		$profs = array();

		$ops = new object_list(array(
			"class_id" => CL_MRP_RESOURCE_OPERATOR,
			"resource" => $res,
			"site_id" => array(),
			"lang_id" => array()
		));
		foreach($ops->arr() as $op)
		{
			// get professions for resources
			$prof = $op->prop("profession");
			foreach(safe_array($op->prop("resource")) as $res)
			{
				$persons[$res][$prof] = $prof;
			}
			$profs[$prof] = $prof;
		}

		if (!count($profs))
		{
			return array();
		}

		// get persons for professions
		$prof2person = array();
		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_CRM_PERSON,
			"type" => 7,
			"to.oid" => $profs
		));
		foreach($conns as $con)
		{
			$prof2person[$con["to"]][$con["from"]] = $con["from"];
		}

		$ret = array();
		foreach($persons as $resource => $profs)
		{
			foreach($profs as $prof)
			{
				if(isset($prof2person[$prof]))
				{
					foreach(safe_array($prof2person[$prof]) as $person)
					{
						$ret[$resource][$person] = obj($person);
					}
				}
			}
		}
		return $ret;
	}

	function _do_pj_toolbar($arr, $job)
	{
		$arr["obj_inst"] = $job;
		$arr["request"]["id"] = $job->id();
		$j = get_instance(CL_MRP_JOB);
		$j->callback_on_load($arr);
		$j->create_job_toolbar($arr);

		$arr["prop"]["toolbar"]->add_button(array(
			"name" => "save_comment",
			"tooltip" => t("Salvesta kommentaar"),
			"action" => "save_pj_comment",
			"confirm" => t("Oled kindel et soovid kommentaari salvestada?"),
		));
	}

	/**
		@attrib name=start
		@param id required type=int
	**/
	function start ($arr)
	{
		$tmp = $arr;
		$j = get_instance(CL_MRP_JOB);
		$arr["id"] = $arr["pj_job"];

		$ud = parse_url($j->start($arr));
		$pars = array();
		parse_str($ud["query"], $pars);
		$this->dequote($pars["errors"]);
		$errs = unserialize($pars["errors"]);

		if (is_array($errs) && count($errs))
		{
			aw_session_set("mrpws_err", $errs);
		}

		return $arr["post_ru"];
		return $this->mk_my_orb("change", array(
			"id" => $tmp["id"],
			"branch_id" => "grp_printer_current",
			"pj_job" => $tmp["pj_job"]
		));
	}

	/**
		@attrib name=done
		@param id required type=int
	**/
	function done ($arr)
	{
		$tmp = $arr;
		$j = get_instance(CL_MRP_JOB);
		$arr["id"] = $arr["pj_job"];

		$ud = parse_url($j->done($arr));
		$pars = array();
		parse_str($ud["query"], $pars);
		$this->dequote($pars["errors"]);
		$errs = unserialize($pars["errors"]);

		if (is_array($errs) && count($errs))
		{
			aw_session_set("mrpws_err", $errs);
		}

		return $arr["post_ru"];
		return $this->mk_my_orb("change", array(
			"id" => $tmp["id"],
			"branch_id" => "grp_printer_current",
			"pj_job" => $tmp["pj_job"]
		));
	}

	/**
		@attrib name=abort
		@param id required type=int
	**/
	function abort ($arr)
	{
		$tmp = $arr;
		$j = get_instance(CL_MRP_JOB);
		$arr["id"] = $arr["pj_job"];
		$job_id = $arr["id"];

		$ud = parse_url($j->abort($arr));
		$pars = array();
		parse_str($ud["query"], $pars);
		$this->dequote($pars["errors"]);
		$errs = unserialize($pars["errors"]);

		if (is_array($errs) && count($errs))
		{
			aw_session_set("mrpws_err", $errs);
		}

		aw_session_set ("mrp_printer_aborted", $job_id);

		return $arr["post_ru"];
		return $this->mk_my_orb("change", array(
			"id" => $tmp["id"],
			"branch_id" => "grp_printer_current",
			"pj_job" => $tmp["pj_job"]
		));
	}

	/**
		@attrib name=pause
		@param id required type=int
	**/
	function pause ($arr)
	{
		$tmp = $arr;
		$j = get_instance(CL_MRP_JOB);
		$arr["id"] = $arr["pj_job"];

		$ud = parse_url($j->pause($arr));
		$pars = array();
		parse_str($ud["query"], $pars);
		$this->dequote($pars["errors"]);
		$errs = unserialize($pars["errors"]);

		if (is_array($errs) && count($errs))
		{
			aw_session_set("mrpws_err", $errs);
		}

		return $arr["post_ru"];
		return $this->mk_my_orb("change", array(
			"id" => $tmp["id"],
			"branch_id" => "grp_printer_current",
			"pj_job" => $tmp["pj_job"]
		));
	}

	/**
		@attrib name=scontinue
		@param id required type=int
	**/
	function scontinue ($arr)
	{
		$tmp = $arr;
		$j = get_instance(CL_MRP_JOB);
		$arr["id"] = $arr["pj_job"];

		$ud = parse_url($j->scontinue($arr));
		$pars = array();
		parse_str($ud["query"], $pars);
		$this->dequote($pars["errors"]);
		$errs = unserialize($pars["errors"]);

		if (is_array($errs) && count($errs))
		{
			aw_session_set("mrpws_err", $errs);
		}

		return $arr["post_ru"];
		return $this->mk_my_orb("change", array(
			"id" => $tmp["id"],
			"branch_id" => "grp_printer_current",
			"pj_job" => $tmp["pj_job"]
		));
	}

	/**
		@attrib name=acontinue
		@param id required type=int
	**/
	function acontinue ($arr)
	{
		$tmp = $arr;
		$j = get_instance(CL_MRP_JOB);
		$arr["id"] = $arr["pj_job"];

		$ud = parse_url($j->acontinue($arr));
		$pars = array();
		parse_str($ud["query"], $pars);
		$this->dequote($pars["errors"]);
		$errs = unserialize($pars["errors"]);

		if (is_array($errs) && count($errs))
		{
			aw_session_set("mrpws_err", $errs);
		}

		return $arr["post_ru"];
		return $this->mk_my_orb("change", array(
			"id" => $tmp["id"],
			"branch_id" => "grp_printer_current",
			"pj_job" => $tmp["pj_job"]
		));
	}

	/**
		@attrib name=end_shift
		@param id required type=int
	**/
	function end_shift ($arr)
	{
		$tmp = $arr;
		$j = get_instance(CL_MRP_JOB);
		$arr["id"] = $arr["pj_job"];

		$ud = parse_url($j->end_shift($arr));
		$pars = array();
		parse_str($ud["query"], $pars);
		$this->dequote($pars["errors"]);
		$errs = unserialize($pars["errors"]);

		if (is_array($errs) && count($errs))
		{
			aw_session_set("mrpws_err", $errs);
		}

		return $arr["post_ru"];
		return $this->mk_my_orb("change", array(
			"id" => $tmp["id"],
			"branch_id" => "grp_printer_current",
			"pj_job" => $tmp["pj_job"]
		));
	}

	function mrp_log($proj, $job, $msg, $comment = '')
	{
		///!!!! logging_disabled teha.
		$this->quote(&$comment);
		$this->quote(&$msg);
		$this->db_query("INSERT INTO mrp_log (
					project_id,job_id,uid,tm,message,comment
				)
				values(
					".((int)$proj).",".((int)$job).",'".aw_global_get("uid")."',".time().",'{$msg}','{$comment}'
				)
		");
	}

	function safe_settype_float ($value) // DEPRECATED
	{ return aw_math_calc::string2float($value); }

	/**

		@attrib name=cut_resources

	**/
	function cut_resources($arr)
	{
		$_SESSION["mrp_workspace"]["cut_resources"] = safe_array($arr["selection"]);
		return $arr["return_url"];
	}

	/**

		@attrib name=copy_resources

	**/
	function copy_resources($arr)
	{
		$_SESSION["mrp_workspace"]["copied_resources"] = safe_array($arr["selection"]);
		return $arr["return_url"];
	}

	/**

		@attrib name=paste_resources

	**/
	function paste_resources($arr)
	{
		foreach(safe_array($_SESSION["mrp_workspace"]["cut_resources"]) as $resource)
		{
			if ($this->can("edit", $resource))
			{
				$o = obj($resource);
				$o->set_parent($arr["mrp_tree_active_item"]);
				$o->save();
			}
		}
		unset($_SESSION["mrp_workspace"]["cut_resources"]);

		if (isset($_SESSION["mrp_workspace"]["copied_resources"]) and is_array($_SESSION["mrp_workspace"]["copied_resources"]))
		{
			foreach($_SESSION["mrp_workspace"]["copied_resources"] as $resource)
			{
				if ($this->can("view", $resource) && isset($arr["mrp_tree_active_item"]) && $this->can("add", $arr["mrp_tree_active_item"]))
				{
					$o = obj($resource);
					$o->set_parent($arr["mrp_tree_active_item"]);
					$o->save_new();
				}
			}
		}
		unset($_SESSION["mrp_workspace"]["copied_resources"]);

		return $arr["return_url"];
	}

	function callback_on_load($arr)
	{
		$this->cfgmanager = $this->callback_get_cfgmanager($arr);
	}

	function callback_get_cfgmanager($arr)
	{
		if (isset($arr["request"]["id"]) and $this->can("view", $arr["request"]["id"]))
		{
			$o = obj($arr["request"]["id"]);
			if ($this->can("view", $o->prop("workspace_configmanager")))
			{
				return $o->prop("workspace_configmanager");
			}
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] === "grp_login_select_res")
		{
			return false;
		}

		if(in_array($arr["id"], array("grp_persons", "grp_persons_jobs_report", "grp_persons_hours_report", "grp_resources_hours_report", "grp_persons_quantity_report")))
		{
			$arr["link"] = aw_url_change_var("timespan", "current_week", $arr["link"]);
		}

		if (automatweb::$request->arg("group") === "grp_login_select_res")
		{
			unset($arr["classinfo"]["relationmgr"]);
			return false;
		}
		return true;
	}

	function callback_mod_layout(&$arr)
	{
		switch($arr["name"])
		{
			case "customers_search_table":
				if(!empty($arr["request"]["alph"]) || !empty($arr["request"]["cat"]))
				{
					$arr["area_caption"] = t("Klientide nimekiri");
				}
				break;

			case "persons_personnel_tree":
				if(automatweb::$request->arg("group") === "my_stats")
				{
					return false;
				}
				elseif(automatweb::$request->arg("group") === "grp_persons_jobs_report")
				{
					$arr["area_caption"] = t("Vali inimesed, kelle tehtud t&ouml;id soovid n&auml;ha");
				}
				elseif(automatweb::$request->arg("group") === "grp_persons_quantity_report")
				{
					$arr["area_caption"] = t("Vali inimesed, kelle t&uuml;kiarvestust soovid n&auml;ha");
				}
				break;

			case "persons_detailed_hours_tbl":
				$o = isset($_GET["person_show_jobs"]) ? obj($_GET["person_show_jobs"]) : (obj(isset($_GET["cat"]) ? $_GET["cat"] : NULL));
				$arr["area_caption"] = sprintf(t("%s t&ouml;&ouml;tundide tabel t&ouml;&ouml;de kaupa"), aw_locale::get_genitive_for_name(parse_obj_name($o->name())));
				break;

			case "pauses_by_resource_chart":
			case "hours_by_resource_chart":
				$type = $arr["name"] === "hours_by_resource_chart" ? t("Efektiivsed t&ouml;&ouml;tunnid") : t("Pausid");
				if(empty($_GET["timespan"]))
				{
					$arr["area_caption"] = sprintf(t("%s ressursside kaupa"), $type);
				}
				else
				{
					list($from, $to) = $this->get_hours_from_to();
					$period = sprintf(t("ajavahemikul %s kuni %s"), date("d.m.Y", $from), date("d.m.Y", $to));
					$arr["area_caption"] = sprintf(t("%s ressursside kaupa %s"), $type, $period);
				}
				break;

			case "persons_hours_chart":
			case "persons_hours_tbl":
				$type = $arr["name"] === "persons_hours_tbl" ? t("tabel") : t("graafik");
				if(empty($_GET["timespan"]))
				{
					$arr["area_caption"] = sprintf(t("T&ouml;&ouml;tundide %s inimeste kaupa"), $type);
					if($arr["request"]["group"] = "my_stats")
					{
						$arr["area_caption"] = sprintf(t("T&ouml;&ouml;tundide %s"), $type);
					}
				}
				else
				{
					list($from, $to) = $this->get_hours_from_to();
					$period = sprintf(t("Ajavahemiku %s kuni %s"), date("d.m.Y", $from), date("d.m.Y", $to));
					$arr["area_caption"] = sprintf(t("%s t&ouml;&ouml;tundide %s inimeste kaupa"), $period, $type);
					if($arr["request"]["group"] = "my_stats")
					{
						$arr["area_caption"] = sprintf(t("%s t&ouml;&ouml;tundide %s"), $period, $type);
					}
				}
				break;

			case "resource_deviation_chart":
				if(isset($arr["request"]["mrp_tree_active_item"]) and $this->can("view", $arr["request"]["mrp_tree_active_item"]))
				{
					$arr["area_caption"] = sprintf(t("Ressursi '%s' suhtelise h&auml;lbe muutus ajas"), obj($arr["request"]["mrp_tree_active_item"])->name());
				}
				break;

/*			case "printer_right":
				if(isset($arr["request"]["pj_job"]) && $this->can("view", $arr["request"]["pj_job"]))
				{
					$arr["area_caption"] = sprintf(t("T&ouml;&ouml; \"%s\" detailvaade"), obj($arr["request"]["pj_job"])->name());
				}
				else
				{
					$caps = array(
						"grp_printer_current" => t("Jooksvad t&ouml;&ouml;d"),
						"grp_printer_old" => t("Tegemata t&ouml;&ouml;d"),
						"grp_printer_done" => t("Tehtud t&ouml;&ouml;d"),
						"grp_printer_aborted" => t("Katkestatud t&ouml;&ouml;d"),
						"grp_printer_in_progress" => t("K&otilde;ik t&ouml;&ouml;s olevad"),
						"grp_printer_startable" => t("K&otilde;ik t&ouml;&ouml;d mida oleks v&otilde;imalik alustada"),
						"grp_printer_notstartable" => t("T&ouml;&ouml;d, mida ei ole veel v&otilde;imalik alustada"),
					);
					$grp = isset($_GET["branch_id"]) ? $_GET["branch_id"] : "grp_printer_current";
					if(array_key_exists($grp, $caps))
					{
						$arr["area_caption"] = $caps[$grp];
					}
				}
				break;*/
		}
		return true;
	}

	function priority_field_callback ($row)
	{
		$applicable_lists = array (
			"planned",
			"planned_overdue",
			"overdue",
			"inwork",
		);

		if (in_array($this->list_request, $applicable_lists))
		{
			$cellcontents = html::textbox (array (
				"name" => "mrp_project_priority-" . $row["project_id"],
				"size" => "5",
				"textsize" => "12px",
				"value" => $row["priority"],
				"maxlength" => "10",
			));
		}
		else
		{
			$cellcontents = $row["priority"];
		}

		return $cellcontents;
	}

	function order_field_callback ($row)
	{
		$cellcontents = 	html::textbox (array (
			"name" => "mrp_resource_order-" . $row["resource_id"],
			"size" => "2",
			"value" => $row["order"],
		));
		return $cellcontents;
	}

	function make_aw_header()
	{
		// current user name, logout link
		$us = get_instance(CL_USER);

		$p_id = $us->get_current_person();
		if (!$p_id)
		{
			return "";
		}

		$person = obj($p_id);
		$hdr = "<span style=\"font-size: 18px; color: red;\">".$person->prop("name")." | ".html::href(array(
				"url" => $this->mk_my_orb("logout", array(), "users"),
				"caption" => t("Logi v&auml;lja")
			))."  | ".$this->get_cur_printer_resources_desc(array("ws" => obj(aw_ini_get("prisma.ws"))))." </span>";

		return $hdr;
	}

	function _sp_result($arr)
	{
		$retval = PROP_IGNORE;

		if (!empty($arr["request"]["sp_search"]))
		{
			$filt = array("class_id" => CL_MRP_CASE);

			// parse search args
			/// name
			if (!empty($arr["request"]["sp_name"]))
			{
				$filt["name"] = "%".$arr["request"]["sp_name"]."%";
			}

			/// comment
			if (!empty($arr["request"]["sp_comment"]))
			{
				$filt["comment"] = "%".$arr["request"]["sp_comment"]."%";
			}

			/// customer
			if (!empty($arr["request"]["sp_customer"]))
			{
				$filt["CL_MRP_CASE.customer.name"] = "%".$arr["request"]["sp_customer"]."%";
			}

			/// min due date
			if (!empty($arr["request"]["sp_due_date"]))
			{
				$tmp = date_edit::get_timestamp($arr["request"]["sp_due_date"]);
				if ($tmp > 100)
				{
					$filt["due_date"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $tmp);
				}
			}

			/// min starttime
			if (!empty($arr["request"]["sp_starttime"]))
			{
				$tmp = date_edit::get_timestamp($arr["request"]["sp_starttime"]);
				if ($tmp > 100)
				{
					$filt["starttime"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $tmp);
				}
			}


			if (!empty($arr["request"]["sp_start_date_start"]) || !empty($arr["request"]["sp_start_date_end"]))
			{
				$tmp = date_edit::get_timestamp($arr["request"]["sp_start_date_start"]);
				$arr["request"]["sp_start_date_end"]["day"]++;//kaasaarvatud see p2ev
				$tmp2 = date_edit::get_timestamp($arr["request"]["sp_start_date_end"]);
				if ($tmp > 100 && $tmp2 > 100)
				{
					$filt["created"] =  new obj_predicate_compare (OBJ_COMP_BETWEEN, $tmp, $tmp2);
				}
				elseif ($tmp2 > 100)
				{
					$filt["created"] = new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $tmp2);
				}
				elseif($tmp > 100)
				{
					$filt["created"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $tmp);
				}

			}


			/// prj status
			if (!empty($arr["request"]["sp_status"]))
			{
				$filt["state"] = $arr["request"]["sp_status"];
			}

			// create objlist
			if (1 === count($filt))
			{
				$results = new object_list();
			}
			else
			{
				$results = new object_list($filt);
			}



//if(aw_global_get("uid") == "struktuur.markop") {arr($results);arr($filt);arr($arr["request"]);}
			$this->list_request = "search";
			$this->projects_list_objects_count = $results->count();
			$arr["search_res"] = $results;
			$this->create_projects_list($arr);
			$retval = PROP_OK;
		}

		return $retval;
	}

	/**
		@attrib name=ajax_update_projects_table all_args=1
		@param id required type=int
	**/
	function ajax_update_projects_table($arr)
	{
		classload("vcl/table");
		$t = new vcl_table();

		$arr["prop"] = array("vcl_inst" => $t);
		$arr["obj_inst"] = obj(6103);

		$date_fields = array("sp_starttime" , "sp_due_date" , "sp_start_date_start" , "sp_start_date_end");
		foreach($date_fields as $var)
		{
			$arr["request"][$var]["day"] = $arr[$var."_day"];
			$arr["request"][$var]["month"] = $arr[$var."_month"];
			$arr["request"][$var]["year"] = $arr[$var."_year"];
		}
		$arr["request"]["sp_name"] = $arr["sp_name"];
		$arr["request"]["sp_comment"] = $arr["sp_comment"];
		$arr["request"]["sp_customer"] = $arr["sp_customer"];
//		$arr["request"]["sp_due_date"] = $arr["sp_due_date"];
//		$arr["request"]["sp_starttime"] = $arr["sp_starttime"];

		if($arr["sp_status"])
		{
			$arr["request"]["sp_status"] = array_keys($arr["sp_status"]);
		}
		$arr["request"]["sp_search"] = 1;

		$this->_sp_result($arr);
		print iconv(aw_global_get("charset"), "UTF-8", $t->get_html());
		die("Test");
	}

	function _cs_result($arr)
	{
		$retval = PROP_IGNORE;

		if (isset($arr["request"]["cs_name"]))
		{
			$t = $arr["prop"]["vcl_inst"];
			$this->_init_cust_list_t($t);

			if (empty($arr["request"]["MAX_FILE_SIZE"]))
			{
				$results = new object_list();
			}
			else
			{

				$filt = array(
					"class_id" => CL_CRM_COMPANY,
					"name" => "%".$arr["request"]["cs_name"]."%",
					"reg_nr" => "%".$arr["request"]["cs_reg_nr"]."%",
				);

				if ($arr["request"]["cs_firmajuht"] != "")
				{
					$filt["CL_CRM_COMPANY.firmajuht(CL_CRM_PERSON).name"] = "%".$arr["request"]["cs_firmajuht"]."%";
				}
				if ($arr["request"]["cs_contact"] != "")
				{
					$filt["CL_CRM_COMPANY.firmajuht(CL_CRM_PERSON).name"] = "%".$arr["request"]["cs_contact"]."%";
				}
				if ($arr["request"]["cs_phone"] != "")
				{
					$filt["CL_CRM_COMPANY.phone_id(CL_CRM_PHONE).name"] = "%".$arr["request"]["cs_phone"]."%";
				}
				$results = new object_list($filt);
			}

			$t->set_caption(t("Klientide otsingu tulemused"));

			$csn = array();
			foreach($results->arr() as $cust)
			{
				if (isset($csn[$cust->name()]))
				{
					continue;
				}
				$csn[$cust->name()] = 1;
				$t->define_data(array(
					"name" => html::obj_change_url($cust),
					"address" => $cust->prop_str("contact"),
					"phone" => $cust->prop_str("phone_id"),
					"email" => $cust->prop_str("email_id"),
					"oid" => $cust->id(),
					"priority" => $cust->prop("priority")
				));
			}
			$retval = PROP_OK;
		}

		return $retval;
	}

	function draw_colour_legend ()
	{
		$dfn = "";
		$rows = "";
		$i = 1;
		$state_colours = self::$state_colours;
		$state_colours["hilighted"] = MRP_COLOUR_HILIGHTED;
		$state_colours["unavailable"] = MRP_COLOUR_UNAVAILABLE;
		$states = $this->states;
		$states["hilighted"] = t("Valitud projekt");
		$states["unavailable"] = t("Kinnine aeg");

		foreach ($state_colours as $state => $colour)
		{
			$name = $states[$state];
			$dfn .= '<td class="awmenuedittabletext" style="background-color: ' . $colour . '; width: 30px;">&nbsp;</td><td class="awmenuedittabletext" style="padding: 0px 15px 0px 6px;">' . $name . '</td>';

			if (!(($i++)%5))
			{
				$rows .= '<tr>' . $dfn . '</tr>';
				$dfn = "";
			}
		}

		$rows .= '<tr>' . $dfn . '</tr>';
		return '<table cellspacing="4" cellpadding="0">' . $rows . '</table>';
	}

	function _get_subcontract_job_list($this_object, $limit = NULL,$projects = array())
	{
		if (empty($this->subcontractor_resource_ids))
		{
			$resource_tree = new object_tree (array (
				"parent" => $this_object->prop ("resources_folder"),
				"class_id" => array (CL_MENU, CL_MRP_RESOURCE),
				"sort_by" => "objects.jrk",
			));
			$list = $resource_tree->to_list ();
			$list->filter (array (
				"class_id" => CL_MRP_RESOURCE,
				"type" => MRP_RESOURCE_SUBCONTRACTOR,
			));
			$this->subcontractor_resource_ids = $list->ids ();
		}

		if (!empty ($this->subcontractor_resource_ids))
		{
			$applicable_states = array (
				mrp_job_obj::STATE_NEW,
				mrp_job_obj::STATE_PLANNED
			);
			$sort_by = NULL;

			if ($limit)
			{
				$sort_order = ("desc" == $arr["request"]["sort_order"]) ? "desc" : "asc";
				$sort_by = "mrp_case.starttime"; // default sort order
				$tmp = NULL;

				switch ($arr["request"]["sortby"])
				{
					case "scheduled_date": // for aborted jobs list
					default:
						$sort_by = "mrp_schedule.starttime {$sort_order}";
						$tmp = new obj_predicate_compare (OBJ_COMP_GREATER, 0);//!!! temporary. acceptable solution needed. jobs with starttime NULL not retrieved.
						break;
				}
			}

			$filter = array (
				"class_id" => CL_MRP_JOB,
				"state" => $applicable_states,
				"resource" => $this->subcontractor_resource_ids,
				"parent" => $this_object->prop ("jobs_folder"),
				"limit" => $limit,
				"sort_by" => $sort_by,
			);
			if(is_array($projects))
			{
				$filter["project"] = $projects;
			}

			if(isset($tmp))
			{
				$filter["starttime"] = $tmp;
			}
			$list = new object_list ($filter);
			return $list;
		}
		else
		{
			return new object_list();
		}
	}

	protected function _chart_search($arr)
	{
		if (isset($arr["request"]["mrp_hilight"]) and $this->can("view", $arr["request"]["mrp_hilight"]) and empty($arr["request"]["chart_project_hilight"]))
		{
			$o = obj($arr["request"]["mrp_hilight"]);
			$arr["request"]["chart_project_hilight"] = $o->name();
		}

		$str  = t("<i>Valitud projekt</i>");
		$str .= " ";
		$str .= html::textbox(array(
			"name" => "chart_project_hilight",
			"value" => isset($arr["request"]["chart_project_hilight"]) ? $arr["request"]["chart_project_hilight"] : "",
			"size" => 6
		));
		$str .= " ";
		$str .= t("<i>Klient</i>");
		$str .= " ";
		$str .= html::textbox(array(
			"name" => "chart_customer",
			"value" => isset($arr["request"]["chart_customer"]) ? $arr["request"]["chart_customer"] : ""
		));

		$spl = $this->mk_my_orb("cust_search_pop", array("id" => $arr["obj_inst"]->id()));
		$str .= " <a href='javascript:void(0)' onClick='aw_popup_scroll(\"$spl\",\"_spop\",300,400)'>Otsi kliente</a>";
		$str .= "<script language='javascript'>function setLink(n) { document.changeform.chart_customer.value = n; document.changeform.submit();}</script>";
		$arr["prop"]["value"] = $str;
	}

	/**

		@attrib name=cust_search_pop

		@param s_name optional
		@param s_content optional
	**/
	function cust_search_pop($arr)
	{
		$s_name = $arr["s_name"];
		$s_content = $arr["s_content"];
		$this->read_template("csp.tpl");
		if (!(empty($s_name) and empty($s_content)))
		{
			$t = new aw_table(array(
				"layout" => "generic"
			));
			$t->define_field(array(
				"name" => "name",
				"caption" => t("Nimetus"),
				"sortable" => 1
			));
			$t->define_field(array(
				"name" => "pick",
				"caption" => t("Vali see"),
			));

			$sres = new object_list(array(
				"class_id" => CL_CRM_COMPANY,
				"name" => "%".$s_name."%",
			));
			for ($o = $sres->begin(); !$sres->end(); $o = $sres->next())
			{
				$name = strip_tags($o->name());
				$name = str_replace("'","",$name);

				$row["pick"] = html::href(array(
					"url" => 'javascript:ss("'.str_replace("'", "&#39;", $o->name()).'")',
					"caption" => t("Vali see")
				));
				$row["name"] = html::obj_change_url($o);
				$t->define_data($row);

			}

			$t->set_default_sortby("name");
			$t->sort_by();
			$this->vars(array("LINE" => $t->draw()));
		}
		else
		{
			$s_name = "%";
			$s_content = "%";
		}

		$this->vars(array(
			"reforb" => $this->mk_reforb("cust_search_pop", array("reforb" => 0)),
			"s_name"	=> $s_name,
			"doc_sel" => checked($s_class_id != "item"),
		));
		exit($this->parse());
	}

	function pj_project_field_callback($row)
	{
		if ($this->can("edit", $row["project_id"]))
		{
			return html::get_change_url(
				$row["project_id"],
				array(
					"return_url" => get_ru()
				),
				$row["project"]
			);
		}
		return $row["project"];
	}

	public function _pjp_customer(&$arr)
	{
		$job = obj($arr["request"]["pj_job"]);
		$arr["prop"]["value"] = obj($job->prop("project"))->prop("customer.name");
	}

	function _pjp_case_wf($arr)
	{
		$case = get_instance(CL_MRP_CASE);
		$arr["no_edit"] = 1;
		$job = obj($arr["request"]["pj_job"]);
		$arr["obj_inst"] = obj($job->prop("project"));
		$arr["request"]["group"] = isset($_GET["branch_id"]) ? $_GET["branch_id"] : "grp_printer_current";
		$case->create_workflow_table($arr);
	}

	function _pjp_material($arr)
	{
		$job = obj($arr["request"]["pj_job"]);
		$t = $arr["prop"]["vcl_inst"];
		$res = get_instance(CL_MRP_JOB);
		$res->draw_expense_list_table($t , $job);
	}

	function _ws_tbl($arr)
	{
		if (empty($arr["request"]["MAX_FILE_SIZE"]))//??? milleks?
		{
			return;
		}

		$t = $arr["prop"]["vcl_inst"];
		$res = get_instance(CL_MRP_RESOURCE);
		$res->_init_job_list_table($t);
		$t->define_field(array(
			"name" => "resource",
			"caption" => t("Resurss"),
			"sortable" => 1,
			"align" => "center"
		));
		$t->set_default_sortby ("starttime");
		$t->set_default_sorder ("asc");

		$applicable_states = array (
			MRP_STATUS_PLANNED,
			MRP_STATUS_PAUSED,
			MRP_STATUS_SHIFT_CHANGE,
			MRP_STATUS_INPROGRESS
		);

		$from = date_edit::get_timestamp($arr["request"]["ws_from"]);
		$to = date_edit::get_timestamp($arr["request"]["ws_to"]) + 24 * 3600;

		$list = new object_list(array(
			"class_id" => CL_MRP_JOB,
			"resource" => $arr["request"]["ws_resource"],
			"state" => $applicable_states,
			"starttime" => new obj_predicate_compare (OBJ_COMP_BETWEEN, $from, $to),
		));

		$res->draw_job_list_table_from_list($t, $list);
	}

	function get_abort_comment_from_job($job)
	{
		$hist = safe_array($job->meta("change_comment_history"));
		return $hist[0]["text"]." (".$hist[0]["uid"].")";
	}

	/**
		@attrib name=archive_projects
		@param id required type=int
	**/
	function archive_projects($arr)
	{
		// aw_switch_user("struktuur");//!!! prismas vist pole seda kasutajat. yldiselt peaks see olema ja seda siin kasutama. [voldemar 9/14/06]
		// aw_switch_user("kix");//!!! ajutiselt v2lja -- prismas probleem aw_switch_user-iga [voldemar 9/14/06]

		if (!$this->can("view", $arr["id"]))
		{
			$scheduler->add(array(
				"event" => $event,
				"time" => time() + 86400,
				"uid" => aw_global_get("uid"),
				"auth_as_local_user" => true,
			));
			exit (1);
		}

		$this_object = obj($arr["id"]);
		$aap = $this_object->prop("automatic_archiving_period");

		if (!$aap)
		{
			exit (2);
		}

		### archive projects finished before now minus autoarchive period
		$a_time = time() - $aap * 86400;

		$projects = new object_list (array (
			"class_id" => CL_MRP_CASE,
			"state" => MRP_STATUS_DONE,
			"parent" => $this_object->prop ("projects_folder"),
			"finished" => new obj_predicate_compare(OBJ_COMP_BETWEEN, 10, $a_time),
		));

//!!! ajutine
foreach ($projects->arr() as $project)
{
		$project->set_prop("archived", time());
		$project->set_prop("state", MRP_STATUS_ARCHIVED);
		$project->save();
}

/* ajutiselt v2lja -- prismas ei t88ta objlist->set_prop miskip2rast. [voldemar 9/14/06]
		$projects->set_prop("archived", time());
		$projects->set_prop("state", MRP_STATUS_ARCHIVED);
		$projects->save();

END ajutine
*/

		### add next archiving event to scheduler
		$scheduler = get_instance("scheduler");
		$event = $this->mk_my_orb("archive_projects", array("id" => $this_object->id()));

		$scheduler->add(array(
			"event" => $event,
			"time" => time() + 86400,
			"uid" => aw_global_get("uid"),
			"auth_as_local_user" => true,
		));

		exit (0);
	}

	/**
		@attrib name=ajax_save_priors all_args=1
	**/
	public function ajax_save_priors($arr)
	{
		$o = obj($arr["id"]);
		$o->set_priors($arr["priority"]);
		die("1");
	}

	/**
		@attrib name=get_projects_subtree
		@param id required type=int
		@param parent required
		@param url required
	**/
	function get_projects_subtree($arr)
	{
		if (strstr($arr["parent"], "archived"))
		{
			$url = $arr["url"];
			$period_data = explode("_", $arr["parent"]);
			$bottom_level = isset($period_data[1]);
			$this_object = obj($arr["this"]);

			$tree = get_instance("vcl/treeview");
			$tree->start_tree (array (
				"type" => TREE_DHTML,
				"tree_id" => "projecttree",
				"has_root" => false,
				"persist_state" => 0,
			));
			$tree->set_only_one_level_opened (true);

			# get items
			if ($bottom_level)
			{ ## by month
				$period = 0;
				$end = 12;
			}
			else
			{ ## by year
				$period = 2003;
				$end = date("Y");
			}

			while (++$period <= $end)
			{
				$start_month = $bottom_level ? $period : 1;
				$start_year = $bottom_level ? $period_data[1] : $period;
				$end_month = $bottom_level ? ((12 == $start_month) ? 1 : ($start_month + 1)) : 1;
				$end_year = $bottom_level ? ((12 == $start_month) ? ($start_year + 1) : $start_year) : ($start_year + 1);

				$list = new object_list (array (
					"class_id" => CL_MRP_CASE,
					"state" => MRP_STATUS_ARCHIVED,
					"starttime" => new obj_predicate_compare (
						OBJ_COMP_BETWEEN,
						mktime(0,0,0,$start_month,1,$start_year),
						mktime(0,0,0,$end_month,1,$end_year)
					),
					"parent" => $this_object->prop ("projects_folder"),
				));
				$count = $list->count();

				if ($count)
				{
					$id = implode("_", $period_data) . "_" . $period;

					$tree->add_item ($arr["parent"], array (
						"name" => $period . " ({$count})",
						"id" => $id,
						"parent" => $arr["parent"],
						"url" => $bottom_level ? aw_url_change_var ("mrp_tree_active_item", $id, aw_url_change_var ("ft_page", 0, $url)) : "javascript: void(0);",
					));

					if (!$bottom_level)
					{
						$tree->add_item ($id, array (
							"id" => "dummy" . $id,
							"parent" => $id,
						));
					}
				}
			}

			preg_match ("/mrp_tree_active_item=(archived_[^\&]+)/", $url, $active_node);

			if ($active_node[1])
			{
				$tree->set_selected_item ($active_node[1]);
			}

			exit ($tree->finalize_tree(array("rootnode" => $arr["parent"])));
		}
	}

	public function callback_generate_scripts($arr)
	{
		$js = "";
		if(automatweb::$request->arg("group") === "grp_material_report")
		{
			$js = "
				function update_material_table()
				{
					$.post('/automatweb/orb.aw?class=mrp_workspace&action=ajax_update_prop',{
							id: ".$arr["obj_inst"]->id()."
							, prop: 'material_stats_table'
							, people: $('[name=people]').val()
							, material: $('[name=material]').val()
							, timespan: $('[name=timespan]').val()
						, grouping: document.getElementById('resource_stats_grouping').value
							, resource: $('[name=resource]').val()}
							,function(html){
						x=document.getElementsByName('material_stats_table');
						x[0].innerHTML = html;//alert(html);
					});
				}
				function update_products_tree()
				{
					$.post('/automatweb/orb.aw?class=mrp_workspace&action=ajax_update_prop',{
						id: ".$arr["obj_inst"]->id()."
					, prop: 'grp_material_tree'
					, material: $('[name=material]').val()}
					,function(html){
						x=document.getElementById('grp_material_tree');
						x.innerHTML = html;//alert(html);
					});
				}
				function update_material_time_tree()
				{
					$.post('/automatweb/orb.aw?class=mrp_workspace&action=ajax_update_prop',{
						id: ".$arr["obj_inst"]->id()."
						, prop: 'grp_material_time_tree'
						, timespan: $('[name=timespan]').val()}
						,function(html){
						x=document.getElementById('grp_material_time_tree');
						x.innerHTML = html;//alert(html);
					});
				}
			";
		}
		elseif(automatweb::$request->arg("group") === "grp_customers")
		{
			$js = "
				function update_priors()
				{
					var a=document.getElementsByName('sp_status');
					var result=[];
					//for (var i=0; i<a.length; i++) {
					//	a[i].checked?result.push(a[i].value):'';
					//}
					result = $('input[name^=priority]');
					$.get('/automatweb/orb.aw?class=mrp_workspace&action=ajax_save_priors&'+result.serialize(),{
							id: ".$arr["obj_inst"]->id()."},function(html){
						alert('".t("Prioriteedid salvestatud")."');
					});
				}";
		}
		elseif(automatweb::$request->arg("group") === "grp_projects")
		{
			$vars = array("sp_name" , "sp_comment" , "sp_customer");
			$date_fields = array("sp_starttime" , "sp_due_date" , "sp_start_date_start" , "sp_start_date_end");
			$ajax_vars = array();
			foreach($vars as $var)
			{
				$ajax_vars[] = $var.": document.getElementsByName('".$var."')[0].value\n";
			}

			foreach($date_fields as $var)
			{
				$ajax_vars[] = $var."_"."day: document.getElementsByName('".$var."[day]')[0].value\n";
				$ajax_vars[] = $var."_"."month: document.getElementsByName('".$var."[month]')[0].value\n";
				$ajax_vars[] = $var."_"."year: document.getElementsByName('".$var."[year]')[0].value\n";
			}

			$js = "
				function update_projects_div()
				{
					var a=document.getElementsByName('sp_status');
					var result=[];
					//for (var i=0; i<a.length; i++) {
					//	a[i].checked?result.push(a[i].value):'';
					//}
					result = $('input[name^=sp_status]');


					button=document.getElementsByName('sp_submit')[0];
					button.disabled = true;
					$.post('/automatweb/orb.aw?class=mrp_workspace&action=ajax_update_projects_table&'+result.serialize(),{
							id: ".$arr["obj_inst"]->id()."
							, ".join(", " , $ajax_vars)."},function(html){
						x=document.getElementsByName('projects_list');
						x[0].innerHTML = html;
						button.disabled = false;
					});
				}";
		}
		elseif(automatweb::$request->arg("group") === "grp_users_mgr")
		{
			$js = "
				$(document).ready(function(){
					$(\"input[type='checkbox'][name$='][all_resources]']\").click(function(){
						o = $(this);
						if(o.attr('checked'))
						{
							$(\"select[name^='\"+o.attr('name').replace('all_resources', 'resource')+\"']\").attr('disabled', 'disabled');
						}
						else
						{
							$(\"select[name^='\"+o.attr('name').replace('all_resources', 'resource')+\"']\").removeAttr('disabled');
						}
					});
				});
			";
		}

		return $js;
	}


	private function get_range($val)
	{
		switch($val)
		{
			case "period_last_week":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")-1, 1, date("Y")),
					"to" => mktime(0,0,0, date("m")-1, 8, date("Y")),
				);
			break;
			case "period_week":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m"), 1, date("Y")),
					"to" => mktime(0,0,0, date("m"), 8, date("Y")),
				);
			break;
			case "period_last_last":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")-2, 1, date("Y")),
					"to" => mktime(0,0,0, date("m")-1, 1, date("Y")),
				);
			break;
			case "period_last":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")-1, 1, date("Y")),
					"to" => mktime(0,0,0, date("m"), 1, date("Y")),
				);
			break;
			case "period_current":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m"), 1, date("Y")),
					"to" => mktime(0,0,0, date("m")+1, 1, date("Y")),
				);
			break;
			case "period_next":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, date("m")+1, 1, date("Y")),
					"to" => mktime(0,0,0, date("m")+2, 1, date("Y")),
				);
			break;
			case "period_year":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, 1, 1, date("Y")),
					"to" => mktime(0,0,0, 1, 1, date("Y")+1),
				);
			break;
			case "period_lastyear":
				$filt["bill_date_range"] = array(
					"from" => mktime(0,0,0, 1, 1, date("Y")-1),
					"to" => mktime(0,0,0,1 , 1, date("Y")),
				);
			break;
			default :return null;
		}
		return $filt["bill_date_range"];
	}

	/**
		@attrib name=ajax_update_prop all_args=1
		@param id optional type=int
		@param prop optional type=string
	**/
	function ajax_update_prop($arr)
	{
		$property = $arr["prop"];
		$arr["obj_inst"] = obj($arr["id"]);
		foreach($arr as $key => $val)
		{
			if(!is_array($val))
			{
				$arr["request"][$key] = $val;
			}
		}

		$arr["request"]["die"] = 1;

		switch($property)
		{
			case "material_stats_table":
				classload("vcl/table");
				$t = new vcl_table();
				break;
			case "grp_material_tree":
			case "grp_material_time_tree":
				classload("vcl/treeview");
				$t = new treeview();
				break;
		}

		$arr["prop"] = array("vcl_inst" => $t);
		$fun = "_get_".$property;
		$this->$fun($arr);
		print iconv(aw_global_get("charset"), "UTF-8", $t->get_html());
		die();
	}

	function _get_material_stats_table($arr)
	{
		$group = $arr["request"]["grouping"];
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_caption(t("Materjalide kulu"));
		$t->set_sortable(false);

		$t->define_field(array(
			"name" => "material",
			"caption" => t("Materjal"),
			"align" => "left"
		));
		if($group)
		{
			switch($group)
			{
				case "work":
					$t->define_field(array(
						"name" => "prop",
						"caption" => t("T&ouml;&ouml;"),
						"align" => "left"
					));
					break;
				case "people":
					$t->define_field(array(
						"name" => "prop",
						"caption" => t("T&ouml;&ouml;taja"),
						"align" => "left"
					));
					break;
				case "section":
					$t->define_field(array(
						"name" => "prop",
						"caption" => t("Osakond"),
						"align" => "left"
					));
					break;
				case "resource":
					$t->define_field(array(
						"name" => "prop",
						"caption" => t("Ressurss"),
						"align" => "left"
					));
					break;
			}

		}
		$t->define_field(array(
			"name" => "prog",
			"caption" => t("Prognoositud"),
			"align" => "right"
		));

		$t->define_field(array(
			"name" => "real",
			"caption" => t("Kulunud"),
			"align" => "right"
		));

		$t->define_field(array(
			"name" => "unit",
			"caption" => t("&Uuml;hik"),
			"align" => "left"
		));
		$filter = array();
		if(isset($arr["request"]["timespan"]))
		{
			$filter = $this->get_range($arr["request"]["timespan"]);
		}
		if(isset($arr["request"]["material"]) && $this->can("view" , $arr["request"]["material"]))
		{
			$mat = obj($arr["request"]["material"]);
			if($mat->class_id() == CL_SHOP_PRODUCT)
			{
				$filter["product"] = $arr["request"]["material"];
			}
			else
			{
				$filter["category"] = $arr["request"]["material"];
			}
		}
		if(isset($arr["request"]["resource"]) && $this->can("view" , $arr["request"]["resource"]))
		{
			$mat = obj($arr["request"]["resource"]);
			if($mat->class_id() == CL_MRP_RESOURCE)
			{
				$filter["resource"] = $arr["request"]["resource"];
			}
			else
			{
				$filter["resource"] = $arr["obj_inst"]->get_menu_resources($arr["request"]["resource"]);
			}
		}
		if(isset($arr["request"]["people"]))
		{
			if(sizeof(explode("_" , $arr["request"]["people"])) > 1)
			{
				$ads = explode("_" , $arr["request"]["people"]);
				$arr["request"]["people"] = $ads[1];
			}

			if($this->can("view" , $arr["request"]["people"]))
			{
				$mat = obj($arr["request"]["people"]);
				if($mat->class_id() == CL_CRM_PERSON)
				{
					$filter["people"] = $arr["request"]["people"];
				}
				elseif($mat->class_id() == CL_CRM_PROFESSION)
				{
					$workers = $mat->get_workers();
					if($workers->count())
					{
						$filter["people"] = $workers->ids();
					}
					else
					{
						$filter["people"] = 1;
					}
				}
				elseif($mat->class_id() == CL_CRM_SECTION)
				{
					$workers = $mat->get_worker_selection();
					if(sizeof($workers))
					{
						$filter["people"] = array_keys($workers);
					}
					else
					{
						$filter["people"] = 1;
					}
				}
			}
		}

		$data = array();
		if(!$group)
		{
			$material_expense_data = $arr["obj_inst"]->get_material_expense_data($filter);
			foreach($material_expense_data as $med)
			{
				if(!isset($data[$med["product"]]))
				{
					$data[$med["product"]] = array("amount" => 0, "used_amount" => 0 , "unit" => 0);
				}
				$data[$med["product"]]["amount"]+= $med["amount"];
				$data[$med["product"]]["used_amount"]+= $med["used_amount"];
				$data[$med["product"]]["unit"] = $med["unit"];
			}

			foreach($data as $id => $d)
			{
				$t->define_data(array(
					"unit" => get_name($d["unit"]),
					"real" => $d["used_amount"],
					"prog" => $d["amount"],
					"material" => get_name($id),
				));
			}
		}
		else
		{
			$gp = "";
			switch($group)
			{
				case "work":
					$gp = "job";
					break;
				case "resource":
					$gp = "job.resource";
					break;
				case "people":
					$gp = "job.person";
					break;
			}
			switch($group)
			{
				case "work":
				case "resource":
					$material_expense_data = $arr["obj_inst"]->get_material_expense_data($filter);
					foreach($material_expense_data as $med)
					{
						if(!isset($data[$med["product"]]))
						{
							$data[$med["product"]][$med[$gp]]= array("amount" => 0, "used_amount" => 0 , "unit" => 0);
						}
						$data[$med["product"]][$med[$gp]]["amount"]+= $med["amount"];
						$data[$med["product"]][$med[$gp]]["used_amount"]+= $med["used_amount"];
						$data[$med["product"]][$med[$gp]]["unit"] = $med["unit"];
					}
					break;
				case "people":
					$material_expenses = $arr["obj_inst"]->get_material_expenses($filter);
					foreach($material_expenses->arr() as $me)
					{
						$persons = $me->prop("job.person");
						if(!is_array($persons))
						{
							$persons = array($persons);
						}
						foreach($persons as $person => $name)
						{
							if(!isset($data[$me->prop("product")]))
							{
								$data[$me->prop("product")][$person]= array("amount" => 0, "used_amount" => 0 , "unit" => 0);
							}
							$data[$me->prop("product")][$person]["amount"]+= $me->prop("amount");
							$data[$me->prop("product")][$person]["used_amount"]+= $me->prop("used_amount");
							$data[$me->prop("product")][$person]["unit"] = $me->prop("unit");
						}
					}
					break;
				case "section":
					$material_expenses = $arr["obj_inst"]->get_material_expenses($filter);
					$person_list = array();
					$person_to_section = array();
					foreach($material_expenses->arr() as $me)
					{
						$persons = $me->prop("job.person");
						if(!is_array($persons))
						{
							$persons = array($persons);
						}
						foreach($persons as $person)
						{
							$person_list[$person] = $person;
						}
					}

					$person_ol = new object_list();
					$person_ol->add($person_list);
					foreach($person_ol->arr() as $o)
					{
						$person_to_section[$o->id()] = array_keys($o->get_section_names());
					}

					foreach($material_expenses->arr() as $me)
					{
						$persons = $me->prop("job.person");
						if(!is_array($persons))
						{
							$persons = array($persons);
						}
						foreach($persons as $person)
						{
							foreach($person_to_section[$person] as $section)
							{
								if(!isset($data[$me->prop("product")]))
								{
									$data[$me->prop("product")][$section]= array("amount" => 0, "used_amount" => 0 , "unit" => 0);
								}
								$data[$me->prop("product")][$section]["amount"]+= $me->prop("amount");
								$data[$me->prop("product")][$section]["used_amount"]+= $me->prop("used_amount");
								$data[$me->prop("product")][$section]["unit"] = $me->prop("unit");
							}
						}
					}
					break;
			}

			foreach($data as $id => $d)
			{
				$t->define_data(array(
//					"unit" => get_name($d["unit"]),
//					"real" => $d["used_amount"],
//					"prog" => $d["amount"],
					"material" => "<b>".get_name($id)."</b>",
				));
				foreach($d as $prop => $amounts)
				{
					$t->define_data(array(
						"prop" => get_name($prop),
						"unit" => get_name($amounts["unit"]),
						"real" => $amounts["used_amount"],
						"prog" => $amounts["amount"],
	//					"material" => "<b>".get_name($id)."</b>",
					));
				}
			}

		}
	}
}


/***************************** MISC. SCRIPTS *********************************/
#set all  PLANNED projects INPROGRESS that have jobs INPROGRESS, DONE, PAUSED or ABORTED
// UPDATE mrp_case, mrp_job, objects SET mrp_case.state=3 WHERE
// mrp_case.oid=mrp_job.oid AND
// mrp_case.oid=objects.oid AND
// objects.status > 0 AND
// objects.parent = this_object->prop(projects_folder) AND
// mrp_case.state=2 AND
// mrp_job.state in (3,5,7,4)
//    lisada parenti kontrollimine

// http://mailprisma/automatweb/orb.aw?class=mrp_schedule&action=create&copyjobstoschedule=1&mrp_workspace=1259
// /* COPY JOBS FROM mrp_job TO mrp_schedule */
// /* dbg */ if ($_GET["copyjobstoschedule"]==1){
// /* dbg */ $this->db_query ("SELECT mrp_job.oid FROM mrp_job LEFT JOIN objects ON objects.oid = mrp_job.oid WHERE objects.status > 0");
// /* dbg */ while ($job = $this->db_next ()) {
// /* dbg */ $this->save_handle(); $this->db_query ("insert into mrp_schedule (oid) values ({$job["oid"]})"); $this->restore_handle(); $i++;} echo $i." t88d."; exit;
// /* dbg */ }

// http://mailprisma/automatweb/orb.aw?class=mrp_schedule&action=create&copyprojectstoschedule=1&mrp_workspace=1259
// /* COPY PROJECTS FROM mrp_case TO mrp_case_schedule */
// /* dbg */ if ($_GET["copyprojectstoschedule"]==1){
// /* dbg */ $this->db_query ("SELECT mrp_case.oid FROM mrp_case LEFT JOIN objects ON objects.oid = mrp_case.oid WHERE objects.status > 0");
// /* dbg */ while ($job = $this->db_next ()) {
// /* dbg */ $this->save_handle(); $this->db_query ("insert into mrp_case_schedule (oid) values ({$job["oid"]})"); $this->restore_handle(); $i++;} echo $i." projekti."; exit;
// /* dbg */ }


/* dbg */ //finish all jobs in progress and set_all_resources_available
// if ($_GET["mrp_set_all_resources_available"])
// {
		// $resources_folder = $this_object->prop ("resources_folder");
		// $resource_tree = new object_tree(array(
			// "parent" => $resources_folder,
			// "class_id" => array(CL_MENU, CL_MRP_RESOURCE),
			// "sort_by" => "objects.jrk",
		// ));
	// $list = new object_list (array (
		// "class_id" => CL_MRP_JOB,
		// "state" => MRP_STATUS_INPROGRESS,
	// ));

	// $jj = $list->arr();
	// $j = get_instance(CL_MRP_JOB);

	// foreach ($jj as $job_id => $job)
	// {
		// echo "job id: " . $job->id() ."<br>";
		// $arr = array("id"=>$job_id);
		// $ud = parse_url($j->done($arr));
		// $pars = array();
		// parse_str($ud["query"], $pars);
		// $this->dequote($pars["errors"]);
		// $errs = unserialize($pars["errors"]);
		// echo "done: [" . implode(",", $errs) . "]<br><br>";
	// }

	// $list = $resource_tree->to_list();
	// $list->filter (array (
		// "class_id" => CL_MRP_RESOURCE,
	// ));
	// $list = $list->arr();

	// foreach ($list as $res_id => $r)
	// {
		// echo "res id: " . $res_id ."<br>";
		// $r->set_prop("state", MRP_STATUS_RESOURCE_AVAILABLE);
		// $r->save();
		// echo "state set to: [" . MRP_STATUS_RESOURCE_AVAILABLE . "]<br><br>";
	// }
// }
// /* dbg */

?>
