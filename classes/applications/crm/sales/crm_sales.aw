<?php

/*

@classinfo syslog_type=ST_CRM_SALES relationmgr=yes no_status=1 maintainer=voldemar prop_cb=1

GROUP DECLARATIONS
@groupinfo settings caption="Seaded"
@groupinfo settings_general caption="&Uuml;ldine" parent=settings
@groupinfo settings_offers caption="Pakkumised" parent=settings

// contacts, presentations and calls tabs/groups are handled by their own separate static view classes
// respectively crm_sales_contacts_view, ...presentations_view and ...calls_view
@groupinfo contacts caption="Kontaktid" submit_method=get
@groupinfo calls caption="K&otilde;ned" submit_method=get
@groupinfo presentations caption="Esitlused" submit_method=get

// calendar views are intended to show overviewing info about all task objects in a scope (all, only user's own, ...)
// handled in crm_sales_calendar_view
@groupinfo calendar caption="Kalender" submit=no
@groupinfo personal_calendar caption="Minu kalender" submit=no parent=calendar
@groupinfo general_calendar caption="&Uuml;ldkalender" submit=no parent=calendar
@groupinfo calls_calendar caption="K&otilde;nede kalender" submit=no parent=calendar
@groupinfo presentations_calendar caption="Esitluste kalender" submit=no parent=calendar

// data entry forms are constructed with help of releditors but data is processed in
// releditor property setter callback.
@groupinfo data_entry caption="Sisestamine"
@groupinfo data_entry_contact_person caption="Kontakt (isik)" parent=data_entry submit=no
@groupinfo data_entry_contact_co caption="Kontakt (organisatsioon)" parent=data_entry submit=no
@groupinfo data_entry_contact_employee caption="Klientorganisatsiooni kontaktisik" parent=data_entry submit=no
@groupinfo data_entry_import caption="Import" parent=data_entry

@groupinfo offers caption="Pakkumised" submit=no submit_method=get

// statistics and analysis views. handled by separate static view classes
@groupinfo statistics caption="&Uuml;levaated"
@groupinfo statistics_telemarketing caption="Telemarketing" parent=statistics submit_method=get
@groupinfo statistics_offers caption="Pakkumised" parent=statistics submit=no submit_method=get



PROPERTY DECLARATIONS
@default table=objects
@default field=meta
@default method=serialize
@default group=general
	@property owner type=relpicker reltype=RELTYPE_OWNER clid=CL_CRM_COMPANY
	@caption Keskkonna omanik

@default group=settings_general
	@layout splitbox1 type=hbox width=50%:50% closeable=1 no_caption=1
	@layout splitbox2 type=vbox closeable=1 area_caption=Kasutajaliidese&nbsp;vaadete&nbsp;konfiguratsioonid&nbsp;rollide&nbsp;kaupa
	@layout splitbox21 type=hbox width=50%:50% parent=splitbox2
	@layout splitbox22 type=hbox width=50%:50% parent=splitbox2
	@layout folders_box type=vbox parent=splitbox1 closeable=1 area_caption=&Uuml;ldseaded
	@layout roles_box type=vbox parent=splitbox1 closeable=1 area_caption=Rollid
	@layout main_app_cfg_box type=vbox parent=splitbox21 area_caption="M&uuml;i&uuml;git&ouml;&ouml;laud"
	@layout call_cfg_box type=vbox parent=splitbox21 area_caption="K&otilde;ne"
	@layout presentation_cfg_box type=vbox parent=splitbox22 area_caption="Esitlus"

	@property calls_folder type=relpicker reltype=RELTYPE_FOLDER clid=CL_MENU parent=folders_box
	@comment Kaust kuhu salvestatakse ning kust loetakse selle m&uuml;&uuml;gikeskkonna k&otilde;neobjektid
	@caption K&otilde;nede kaust

	@property presentations_folder type=relpicker reltype=RELTYPE_FOLDER clid=CL_MENU parent=folders_box
	@comment Kaust kuhu salvestatakse ning kust loetakse selle m&uuml;&uuml;gikeskkonna esitlused
	@caption Esitluste kaust

	@property contracts_folder type=relpicker reltype=RELTYPE_FOLDER clid=CL_MENU parent=folders_box
	@comment Kaust kuhu salvestatakse ning kust loetakse selle m&uuml;&uuml;gikeskkonna lepingud
	@caption Lepingute kaust

	@property offers_folder type=relpicker reltype=RELTYPE_FOLDER clid=CL_MENU parent=folders_box
	@comment Kaust kuhu salvestatakse ning kust loetakse selle m&uuml;&uuml;gikeskkonna pakkumisobjektid
	@caption Pakkumiste kaust

	@property price_components_folder type=relpicker reltype=RELTYPE_FOLDER clid=CL_MENU parent=folders_box
	@comment Kaust kuhu salvestatakse ning kust loetakse selle m&uuml;&uuml;gikeskkonna hinnakomponentide objekte
	@caption Hinnakomponentide kaust

	@property price_component_categories_folder type=relpicker reltype=RELTYPE_FOLDER clid=CL_MENU parent=folders_box
	@comment Kaust kust loetakse selle m&uuml;&uuml;gikeskkonna hinnakomponendi kategooriate objekte
	@caption Hinnakomponendi kategooriate kaust

	@property warehouse type=objpicker clid=CL_SHOP_WAREHOUSE parent=folders_box
	@caption Ladu

	@property avg_call_duration_est type=textbox default=300 datatype=int parent=folders_box
	@comment Hinnanguline keskmine m&uuml;&uuml;gik&otilde;ne kestus (sekundites)
	@caption K&otilde;nekestuse hinnang (s)

	@property call_result_busy_recall_time type=textbox default=300 datatype=int parent=folders_box
	@comment Millise ajavahemiku j&auml;rel uuesti helistada kui number oli kinni (sekundites)
	@caption Uus k&otilde;ne kui nr. kinni (s)

	@property call_result_noanswer_recall_time type=textbox default=7200 datatype=int parent=folders_box
	@comment Millise ajavahemiku j&auml;rel uuesti helistada kui number ei vasta (sekundites)
	@caption Uus k&otilde;ne kui ei vasta (s)

	@property call_result_outofservice_recall_time type=textbox default=86400 datatype=int parent=folders_box
	@comment Millise ajavahemiku j&auml;rel uuesti helistada kui number on teeninduspiirkonnast v&auml;ljas (sekundites)
	@caption Uus k&otilde;ne kui tel. v&auml;ljas (s)
//!!!! vaadata yle mida 0 t2hendab -- et kui 0 siis ei loodagi uut k6net n2iteks
	@property call_result_noanswer_recall_time type=textbox default=43200 datatype=int parent=folders_box
	@comment Millise ajavahemiku j&auml;rel uuesti helistada kui numbril vastab automaatvastaja v&otilde;i k&otilde;nepost (sekundites). 0 t&auml;hendab
	@caption Uus k&otilde;ne kui automaatvastaja/k&otilde;nepost (s)

	@property call_result_notinterested_recall_time type=textbox default=120 datatype=int parent=folders_box
	@comment Millise ajavahemiku j&auml;rel uuesti helistada kui kontakt pole hetkel huvitatud (p&auml;evades). Nullist suurem t&auml;isarv.
	@caption Uus k&otilde;ne kui hetkel pole huvitatud (p)

	@property call_result_recall_retries type=textbox default=5 datatype=int parent=folders_box
	@comment Mitu korda uuesti helistada kui kinni, ei vasta, on v&auml;ljas v&otilde;i automaatvastaja
	@caption Uuesti proovida enim (korda)

	@property avg_presentation_duration_est type=textbox default=7200 datatype=int parent=folders_box
	@comment Hinnanguline keskmine m&uuml;&uuml;giesinduse kestus (sekundites)
	@caption Esitluse kestuse hinnang (s)

	@property autocomplete_options_limit type=textbox default=20 datatype=int parent=folders_box
	@comment Mitu valikut pakkuda autocomplete otsingutes
	@caption Autocomplete valikuid

	@property tables_rows_per_page type=textbox default=25 datatype=int parent=folders_box
	@comment Mitu lehel rida kuvada tabelites
	@caption Ridu tabelites

	// roles
	@property role_profession_data_entry_clerk type=relpicker reltype=RELTYPE_ROLE_PROFESSION clid=CL_CRM_PROFESSION parent=roles_box
	@comment Andmesisestaja rollile vastav ametinimetus organisatsioonis
	@caption Andmesisestaja amet

	@property role_profession_telemarketing_salesman type=relpicker reltype=RELTYPE_ROLE_PROFESSION clid=CL_CRM_PROFESSION parent=roles_box
	@comment Telemarketingit&ouml;&ouml;taja rollile vastav ametinimetus organisatsioonis
	@caption Telemarketingit&ouml;&ouml;taja amet

	@property role_profession_telemarketing_manager type=relpicker reltype=RELTYPE_ROLE_PROFESSION clid=CL_CRM_PROFESSION parent=roles_box
	@comment Telemarketingi juhi rollile vastav ametinimetus organisatsioonis
	@caption Telemarketingi juhi amet

	@property role_profession_salesman type=relpicker reltype=RELTYPE_ROLE_PROFESSION clid=CL_CRM_PROFESSION parent=roles_box
	@comment M&uuml;&uuml;gimehe/m&uuml;&uuml;giesindaja rollile vastav ametinimetus organisatsioonis
	@caption M&uuml;&uuml;giesindaja amet

	@property role_profession_sales_manager type=relpicker reltype=RELTYPE_ROLE_PROFESSION clid=CL_CRM_PROFESSION parent=roles_box
	@comment M&uuml;&uuml;gijuhi rollile vastav ametinimetus organisatsioonis
	@caption M&uuml;&uuml;gijuhi amet

	@property role_profession_manager type=relpicker reltype=RELTYPE_ROLE_PROFESSION clid=CL_CRM_PROFESSION parent=roles_box
	@comment Juhi rollile vastav ametinimetus organisatsioonis
	@caption Juhi amet

	// cfgforms
	//// main application cfgforms
	@property cfgf_main_generic type=relpicker reltype=RELTYPE_CFGFORM parent=main_app_cfg_box
	@caption K&otilde;ik kasutajad

	@property cfgf_main_data_entry_clerk type=relpicker reltype=RELTYPE_CFGFORM parent=main_app_cfg_box
	@caption Andmesisestaja

	@property cfgf_main_telemarketing_salesman type=relpicker reltype=RELTYPE_CFGFORM parent=main_app_cfg_box
	@caption Telemarketingit&ouml;&ouml;taja

	@property cfgf_main_telemarketing_manager type=relpicker reltype=RELTYPE_CFGFORM parent=main_app_cfg_box
	@caption Telemarketingi juht

	@property cfgf_main_salesman type=relpicker reltype=RELTYPE_CFGFORM parent=main_app_cfg_box
	@caption M&uuml;&uuml;giesindaja

	@property cfgf_main_sales_manager type=relpicker reltype=RELTYPE_CFGFORM parent=main_app_cfg_box
	@caption M&uuml;&uuml;gijuht

	@property cfgf_main_manager type=relpicker reltype=RELTYPE_CFGFORM parent=main_app_cfg_box
	@caption Juht


	//// call cfgforms
	@property cfgf_call_generic type=relpicker reltype=RELTYPE_CFGFORM parent=call_cfg_box
	@caption K&otilde;ik kasutajad

	@property cfgf_call_data_entry_clerk type=relpicker reltype=RELTYPE_CFGFORM parent=call_cfg_box
	@caption Andmesisestaja

	@property cfgf_call_telemarketing_salesman type=relpicker reltype=RELTYPE_CFGFORM parent=call_cfg_box
	@caption Telemarketingit&ouml;&ouml;taja

	@property cfgf_call_telemarketing_manager type=relpicker reltype=RELTYPE_CFGFORM parent=call_cfg_box
	@caption Telemarketingi juht

	@property cfgf_call_salesman type=relpicker reltype=RELTYPE_CFGFORM parent=call_cfg_box
	@caption M&uuml;&uuml;giesindaja

	@property cfgf_call_sales_manager type=relpicker reltype=RELTYPE_CFGFORM parent=call_cfg_box
	@caption M&uuml;&uuml;gijuht

	@property cfgf_call_manager type=relpicker reltype=RELTYPE_CFGFORM parent=call_cfg_box
	@caption Juht


	//// presentation cfgforms
	@property cfgf_presentation_generic type=relpicker reltype=RELTYPE_CFGFORM parent=presentation_cfg_box
	@caption K&otilde;ik kasutajad

	@property cfgf_presentation_data_entry_clerk type=relpicker reltype=RELTYPE_CFGFORM parent=presentation_cfg_box
	@caption Andmesisestaja

	@property cfgf_presentation_telemarketing_salesman type=relpicker reltype=RELTYPE_CFGFORM parent=presentation_cfg_box
	@caption Telemarketingit&ouml;&ouml;taja

	@property cfgf_presentation_telemarketing_manager type=relpicker reltype=RELTYPE_CFGFORM parent=presentation_cfg_box
	@caption Telemarketingi juht

	@property cfgf_presentation_salesman type=relpicker reltype=RELTYPE_CFGFORM parent=presentation_cfg_box
	@caption M&uuml;&uuml;giesindaja

	@property cfgf_presentation_sales_manager type=relpicker reltype=RELTYPE_CFGFORM parent=presentation_cfg_box
	@caption M&uuml;&uuml;gijuht

	@property cfgf_presentation_manager type=relpicker reltype=RELTYPE_CFGFORM parent=presentation_cfg_box
	@caption Juht

@default group=settings_offers

	@property cfgf_offers_toolbar type=toolbar store=no no_caption=1

	@layout settings_offers_vsplitbox type=hbox width=50%:50%

		@layout settings_offers_left type=vbox parent=settings_offers_vsplitbox area_caption=Pakkumiste&nbsp;seaded

			@property cfgf_offers_hide_mandatory_price_components type=checkbox parent=settings_offers_left
			@caption Peida kohustuslikud hinnakomponendid

		@layout settings_offers_right type=vbox parent=settings_offers_vsplitbox

			@property cfgf_offers_price_component_categories_table type=table store=no no_caption=1 parent=settings_offers_right

			@property cfgf_offers_price_components_table type=table store=no no_caption=1 parent=settings_offers_right

@default group=contacts
	@layout contacts_vsplitbox type=hbox width=25%:75%
	@property contacts_toolbar type=toolbar store=no no_caption=1
	@layout contacts_box type=vbox parent=contacts_vsplitbox
	@layout contacts_tree_box type=vbox closeable=1 area_caption=Kontaktide&nbsp;valik parent=contacts_box
	@property contacts_tree type=treeview store=no no_caption=1 parent=contacts_tree_box
	@property contacts_list type=table store=no no_caption=1 parent=contacts_vsplitbox

	@layout contacts_search_box type=vbox closeable=1 area_caption=Kontaktide&nbsp;otsing parent=contacts_box
		@property cts_name type=textbox parent=contacts_search_box store=no size=20 captionside=top
		@caption Nimi

		@property cts_address type=textbox parent=contacts_search_box store=no size=20 captionside=top
		@caption Aadress

		@property cts_phone type=textbox parent=contacts_search_box store=no size=20 captionside=top
		@caption Telefon

		@property cts_lead_source type=textbox parent=contacts_search_box store=no size=20 captionside=top
		@caption Soovitaja

		@property cts_salesman type=select parent=contacts_search_box store=no captionside=top
		@caption M&uuml;&uuml;giesindaja

		@property cts_status type=select parent=contacts_search_box store=no captionside=top
		@caption Staatus

		@property cts_cat type=objpicker parent=contacts_search_box store=no options_callback=crm_sales::get_category_options captionside=top clid=CL_CRM_CATEGORY size=20
		@caption Kliendigrupp

		@property cts_calls type=textbox parent=contacts_search_box store=no captionside=top size=20
		@comment Positiivne t&auml;isarv. V&otilde;imalik kasutada v&otilde;rdlusoperaatoreid suurem kui ( &gt; ), v&auml;iksem kui ( &lt; ) ning '='. Kui operaatorit pole numbri ees, arvatakse vaikimisi operaatoriks v&otilde;rdus ( = )
		@caption Tehtud k&otilde;nesid

		@property cts_sort_mode type=select parent=contacts_search_box store=no captionside=top
		@caption Tulemuste j&auml;rjestus

		@property cts_count type=checkbox parent=contacts_search_box store=no captionside=top
		@comment Ilma tulemuste arvu kokkulugemiseta on p&auml;ringud kiiremad.
		@caption N&auml;ita tulemuste arvu

		@property cts_submit type=submit value=Otsi parent=contacts_search_box store=no
		@caption Otsi



@default group=calls
	@layout calls_vsplitbox type=hbox width=25%:75%
	@property calls_toolbar type=toolbar store=no no_caption=1
	@layout calls_box type=vbox parent=calls_vsplitbox
	@layout calls_tree_box type=vbox closeable=1 area_caption=K&otilde;nede&nbsp;valik parent=calls_box
	@property calls_tree type=treeview store=no no_caption=1 parent=calls_tree_box
	@property calls_list type=table store=no no_caption=1 parent=calls_vsplitbox

	@layout calls_search_box type=vbox closeable=1 area_caption=K&otilde;nede&nbsp;otsing parent=calls_box
		@property cs_name type=textbox parent=calls_search_box store=no size=20 captionside=top
		@caption Kontakti nimi

		@property cs_address type=textbox parent=calls_search_box store=no size=20 captionside=top
		@comment Aadressid on kujul "V&auml;iksem kohanimi, Suurem kohanimi". N&auml;iteks otsides kontakte Harjumaal Kolgas tuleb kirjutada "kolga harjumaa"
		@caption Kontakti aadress

		@property cs_phone type=textbox parent=calls_search_box store=no size=20 captionside=top
		@comment M&otilde;ni kontakti telefoninumbritest algab ...
		@caption Kontakti telefon

		@property cs_lead_source type=textbox parent=calls_search_box store=no size=20 captionside=top
		@caption Kontakti soovitaja

		@property cs_caller type=select parent=calls_search_box store=no captionside=top
		@caption Helistaja

		@property cs_call_result type=select parent=calls_search_box store=no captionside=top
		@caption Tulemus

		@property cs_call_real_start_from type=datepicker parent=calls_search_box store=no captionside=top
		@caption Aeg
		@comment Ajavahemik, milles otsitav(ad) k&otilde;ne(d) on alustatud. Kui sisestada ainult vahemiku algus siis otsitakse vaikimisi sellele j&auml;rgnevast 24 tunnist
		@property cs_call_real_start_to type=datepicker parent=calls_search_box store=no no_caption=1

		@property cs_last_caller type=select parent=calls_search_box store=no captionside=top
		@comment Isik kes viimasena k&otilde;nega seotud kontaktile helistas
		@caption Viimane helistaja

		@property cs_last_call_result type=select parent=calls_search_box store=no captionside=top
		@comment K&otilde;nega seotud kontaktile viimati tehtud k&otilde;ne tulemus
		@caption Viimase tulemus

		@property cs_salesman type=select parent=calls_search_box store=no captionside=top
		@comment Kontaktile m&auml;&auml;ratud m&uuml;&uuml;giesindaja
		@caption M&uuml;&uuml;giesindaja

		@property cs_status type=select parent=calls_search_box store=no captionside=top
		@caption Kontakti staatus

		@property cs_count type=checkbox parent=calls_search_box store=no captionside=top
		@comment Ilma tulemuste arvu kokkulugemiseta on p&auml;ringud kiiremad.
		@caption N&auml;ita tulemuste arvu

		@property cs_submit type=submit value=Otsi parent=calls_search_box store=no
		@caption Otsi



@default group=presentations
	@layout presentations_vsplitbox type=hbox width=25%:75%
	@property presentations_toolbar type=toolbar store=no no_caption=1
	@layout presentations_box type=vbox parent=presentations_vsplitbox
	@layout presentations_tree_box type=vbox closeable=1 area_caption=Esitluste&nbsp;valik parent=presentations_box
	@property presentations_tree type=treeview store=no no_caption=1 parent=presentations_tree_box
	@property presentations_list type=table store=no no_caption=1 parent=presentations_vsplitbox

	@layout presentations_search_box type=vbox closeable=1 area_caption=Esitluste&nbsp;otsing parent=presentations_box
		@property ps_name type=textbox parent=presentations_search_box store=no size=20 captionside=top
		@caption Kontakti nimi

		@property ps_address type=textbox parent=presentations_search_box store=no size=20 captionside=top
		@caption Kontakti aadress

		@property ps_phone type=textbox parent=presentations_search_box store=no size=20 captionside=top
		@caption Kontakti telefon

		@property ps_lead_source type=textbox parent=presentations_search_box store=no size=20 captionside=top
		@caption Kontakti soovitaja

		@property ps_salesman type=select parent=presentations_search_box store=no captionside=top
		@caption M&uuml;&uuml;giesindaja

		@property ps_createdby type=select parent=presentations_search_box store=no captionside=top
		@caption Looja

		@property ps_result type=select parent=presentations_search_box store=no captionside=top
		@caption Tulemus

		@property ps_created_from type=datepicker parent=presentations_search_box store=no captionside=top
		@caption Loodud vahemikus
		@comment Kui sisestada ainult vahemiku algus siis otsitakse vaikimisi sellele j&auml;rgnevast 24 tunnist
		@property ps_created_to type=datepicker parent=presentations_search_box store=no no_caption=1

		@property ps_start_from type=datepicker parent=presentations_search_box store=no captionside=top
		@caption Kokkulepitud aeg
		@comment Kui sisestada ainult vahemiku algus siis otsitakse vaikimisi sellele j&auml;rgnevast 24 tunnist
		@property ps_start_to type=datepicker parent=presentations_search_box store=no no_caption=1

		@property ps_real_start_from type=datepicker parent=presentations_search_box store=no captionside=top
		@caption Tegelik toimumisaeg
		@comment Kui sisestada ainult vahemiku algus siis otsitakse vaikimisi sellele j&auml;rgnevast 24 tunnist
		@property ps_real_start_to type=datepicker parent=presentations_search_box store=no no_caption=1

		@property ps_submit type=submit value=Otsi parent=presentations_search_box store=no
		@caption Otsi


@default group=personal_calendar
	@property personal_calendar type=calendar store=no no_caption=1


@default group=general_calendar
	@property general_calendar type=calendar store=no no_caption=1


@default group=calls_calendar
	@property calls_calendar type=calendar store=no no_caption=1


@default group=presentations_calendar
	@property presentations_calendar type=calendar store=no no_caption=1

--------------------------------------------------------------
@layout de_form_box type=vbox group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee area_caption=Uus&nbsp;kontakt
@layout de_form_pane type=hbox group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee parent=de_form_box
@layout de_table_box type=vbox group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee
@layout contact_entry_form type=vbox group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee parent=de_form_pane
@layout contact_entry_form_right type=vbox group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee parent=de_form_pane

@default group=data_entry
	@property contact_entry_toolbar type=toolbar store=no group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee no_caption=1

--------------------------------------------------------------
@default group=data_entry_contact_co
	@property contact_entry_co type=releditor reltype=RELTYPE_TMP2 store=no props=name,ettevotlusvorm,fake_phone,fake_mobile,fake_email parent=contact_entry_form
	@caption Kontakt (organisatsioon)

	@property contact_entry_contacts_title type=text store=no parent=contact_entry_form_right group=data_entry_contact_co
	@caption Kontaktisikud:

	@property contact_entry_contacts_subtitle_1 type=text store=no parent=contact_entry_form_right group=data_entry_contact_co
	@caption Kontaktisik 1
	@property contact_entry_co_contact_1 type=releditor reltype=RELTYPE_TMP3 store=no props=lastname,firstname,fake_phone,fake_email parent=contact_entry_form_right no_caption=1
	@property contact_entry_co_contact_1_profession type=textbox store=no parent=contact_entry_form_right
	@caption Ametinimetus

	@property contact_entry_contacts_subtitle_2 type=text store=no parent=contact_entry_form_right group=data_entry_contact_co
	@caption Kontaktisik 2
	@property contact_entry_co_contact_2 type=releditor reltype=RELTYPE_TMP3 store=no props=lastname,firstname,fake_phone,fake_email parent=contact_entry_form_right no_caption=1
	@property contact_entry_co_contact_2_profession type=textbox store=no parent=contact_entry_form_right
	@caption Ametinimetus

	@property contact_entry_contacts_subtitle_3 type=text store=no parent=contact_entry_form_right group=data_entry_contact_co
	@caption Kontaktisik 3
	@property contact_entry_co_contact_3 type=releditor reltype=RELTYPE_TMP3 store=no props=lastname,firstname,fake_phone,fake_email parent=contact_entry_form_right no_caption=1
	@property contact_entry_co_contact_3_profession type=textbox store=no parent=contact_entry_form_right
	@caption Ametinimetus

--------------------------------------------------------------
@default group=data_entry_contact_employee
	@property contact_entry_empoyee type=releditor reltype=RELTYPE_TMP3 store=no props=lastname,firstname,gender,fake_phone,fake_email parent=contact_entry_form
	@caption Kontaktisik

	@property contact_entry_organization type=objpicker store=no parent=contact_entry_form clid=CL_CRM_COMPANY
	@comment Kui isik on liige v&otilde;i t&ouml;&ouml;tab mingis organisatsioonis, saab siin seda valida
	@caption Organisatsioon

	@property contact_entry_profession type=objpicker store=no parent=contact_entry_form clid=CL_CRM_PROFESSION options_callback=crm_sales::get_profession_options(contact_entry_organization)
	@caption Amet

	@property contact_entry_comment type=textarea store=no rows=5 cols=30 parent=contact_entry_form
	@caption Kommentaar


--------------------------------------------------------------
@default group=data_entry_contact_person
	@property contact_entry_person type=releditor reltype=RELTYPE_TMP3 store=no props=lastname,firstname,gender,fake_phone,fake_email parent=contact_entry_form
	@caption Kontakt (isik)

	@property contact_entry_add_comment type=textarea store=no rows=5 cols=30 parent=contact_entry_form group=data_entry_contact_co,data_entry_contact_person
	@comment Siia sisestatud kommentaar lisatakse igal salvestamisel kliendi andmete juurde. Juba sisestatud kommentaare siin ei kuvata.
	@caption Lisa kommentaar

	@property contact_entry_address_title type=text store=no parent=contact_entry_form group=data_entry_contact_co,data_entry_contact_person
	@caption Aadress:

	@property contact_entry_address type=releditor store=no props=country,location_data,location,street,house,apartment,postal_code,po_box table_fields=name,location,street,house,apartment reltype=RELTYPE_TMP1 parent=contact_entry_form group=data_entry_contact_co,data_entry_contact_person

	@property contact_entry_separator1 type=text store=no parent=contact_entry_form group=data_entry_contact_co,data_entry_contact_person
	@caption &nbsp;

	@property contact_entry_salesman type=select store=no group=data_entry_contact_co,data_entry_contact_person parent=contact_entry_form
	@caption M&uuml;&uuml;giesindaja

	@property contact_entry_lead_source type=textbox store=no group=data_entry_contact_co,data_entry_contact_person parent=contact_entry_form
	@caption Soovitaja

	@property contact_entry_category type=relpicker size=5 multiple=1 store=no group=data_entry_contact_co,data_entry_contact_person parent=contact_entry_form reltype=RELTYPE_TMP4
	@caption Kliendigrupp

--------------------------------------------------------------
@default group=data_entry_import
	@property import_toolbar type=toolbar store=no no_caption=1

	@property import_objects type=table store=no
	@caption Seadistatud impordid

@layout contact_entry_buttons type=hbox parent=de_form_box group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee width=10%:20%:70%
	@property contact_entry_space type=text store=no group=data_entry_contact_co,data_entry_contact_person parent=contact_entry_buttons no_caption=1

	@property contact_entry_submit type=submit store=no group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee parent=contact_entry_buttons
	@caption Salvesta

	@property contact_entry_reset type=text store=no group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee parent=contact_entry_buttons no_caption=1

	@property last_entries_list type=table store=no group=data_entry_contact_co,data_entry_contact_person,data_entry_contact_employee no_caption=1 parent=de_table_box

	@property contact_entry_lead_source_oid type=hidden store=no group=data_entry_contact_co,data_entry_contact_person

--------------------------------------------------------------
@default group=offers

	@property offers_toolbar type=toolbar store=no no_caption=1

	@layout offers_vsplitbox type=hbox width=25%:75%

		@layout offers_box type=vbox parent=offers_vsplitbox
			
			@layout offers_tree_box_state type=vbox closeable=1 area_caption=Pakkumiste&nbsp;valik&nbsp;staatuse&nbsp;j&auml;rgi parent=offers_box

				@property offers_tree_state type=treeview store=no no_caption=1 parent=offers_tree_box_state
		
			@layout offers_tree_box_timespan type=vbox closeable=1 area_caption=Pakkumiste&nbsp;valik&nbsp;perioodi&nbsp;j&auml;rgi parent=offers_box

				@property offers_tree_timespan type=treeview store=no no_caption=1 parent=offers_tree_box_timespan
		
			@layout offers_tree_box_customer_category type=vbox closeable=1 area_caption=Pakkumiste&nbsp;valik&nbsp;kliendikategooria&nbsp;j&auml;rgi parent=offers_box

				@property offers_tree_customer_category type=treeview store=no no_caption=1 parent=offers_tree_box_customer_category

		@layout offers_search_box type=vbox closeable=1 area_caption=Pakkumiste&nbsp;otsing parent=offers_box

			@property os_customer_name type=textbox view_element=1 parent=offers_search_box store=no size=20 captionside=top
			@caption Kliendi nimi

			@property os_submit type=submit value=Otsi view_element=1 parent=offers_search_box store=no
			@caption Otsi

		@property offers_list type=table store=no no_caption=1 parent=offers_vsplitbox


@default group=statistics_telemarketing
	@property tmstat_display_table type=table store=no no_caption=1

	@layout tmstat_control_container type=hbox width=50:50 area_caption=Parameetrid
		@property tmstat_m type=select store=no no_caption=1 parent=tmstat_control_container
		@property tmstat_y type=select store=no no_caption=1 parent=tmstat_control_container
		@property tmstat_s type=submit value=Vaata parent=tmstat_control_container store=no
		@caption Vaata

@default group=statistics_offers

	@layout statistics_offers_vsplitbox type=hbox width=25%:75%

		@layout statistics_offers_box type=vbox parent=statistics_offers_vsplitbox
			
			@layout statistics_offers_tree_box_state type=vbox closeable=1 area_caption=Pakkumiste&nbsp;valik&nbsp;staatuse&nbsp;j&auml;rgi parent=statistics_offers_box

				@property statistics_offers_tree_state type=treeview store=no no_caption=1 parent=statistics_offers_tree_box_state
		
			@layout statistics_offers_tree_box_timespan type=vbox closeable=1 area_caption=Pakkumiste&nbsp;valik&nbsp;perioodi&nbsp;j&auml;rgi parent=statistics_offers_box

				@property statistics_offers_tree_timespan type=treeview store=no no_caption=1 parent=statistics_offers_tree_box_timespan
		
			@layout statistics_offers_tree_box_customer_category type=vbox closeable=1 area_caption=Pakkumiste&nbsp;valik&nbsp;kliendikategooria&nbsp;j&auml;rgi parent=statistics_offers_box

				@property statistics_offers_tree_customer_category type=treeview store=no no_caption=1 parent=statistics_offers_tree_box_customer_category

		@property statistics_offers_list type=table store=no no_caption=1 parent=statistics_offers_vsplitbox


RELATION TYPE DECLARATIONS
@reltype OWNER value=1 clid=CL_CRM_COMPANY
@caption Keskkonna omanikfirma

@reltype FOLDER value=3 clid=CL_MENU
@caption Kaust

@reltype ROLE_PROFESSION value=4 clid=CL_CRM_PROFESSION
@caption Rollile vastav amet

@reltype CFGFORM value=6 clid=CL_CFGFORM
@caption Seadete vorm

@reltype IMPORT value=7 clid=CL_CSV_IMPORT
@caption Kontaktide import

/// helper relations that are never used to connect actual objects
@reltype TMP1 value=8 clid=CL_ADDRESS
@caption Kontakti aadress (s&uuml;steemisiseseks kasutuseks)

@reltype TMP2 value=2 clid=CL_CRM_COMPANY
@caption Kontakt firma (s&uuml;steemisiseseks kasutuseks)

@reltype TMP3 value=5 clid=CL_CRM_PERSON
@caption Kontakt isik (s&uuml;steemisiseseks kasutuseks)

@reltype TMP4 value=6 clid=CL_CRM_CATEGORY
@caption Kliendigrupp (s&uuml;steemisiseseks kasutuseks)


*/

class crm_sales extends class_base
{
	// LIST VIEWS
	// these define predefined filter configurations for lists of
	// sales objects (calls, presentations, contacts)
	// used for example to construct a tree menu of lists available
	// "SEARCH" is for indicating that a custom filter is currently used i.e. user has entered search parameters

	// current calls is a list of calls to be made in shown order. sorted by importance. prioritizing
	// algorithm/sort definition should later be made either configurable or selectable from predefined choices
	// intended to be a type of task list for telemarketing salespersons
	const CALLS_CURRENT = 1;
	//...
	const CALLS_SEARCH = 2;

	const CONTACTS_DEFAULT = 1;
	const CONTACTS_SEARCH = 2;
	const CONTACTS_CATEGORY = 3;

	const PRESENTATIONS_DEFAULT = 1;
	const PRESENTATIONS_SEARCH = 2;
	const PRESENTATIONS_YESTERDAY = 3;
	const PRESENTATIONS_TODAY = 4;
	const PRESENTATIONS_TOMORROW = 5;
	const PRESENTATIONS_ADDED_TODAY = 6;

	const OFFERS_DEFAULT = 1;
	const OFFERS_SEARCH = 2;
	const OFFERS_YESTERDAY = 3;
	const OFFERS_TODAY = 4;
	const OFFERS_THIS_WEEK = 5;
	const OFFERS_LAST_WEEK = 6;

	// colours for different states
	const COLOUR_CAN_START = "#EFF6D5";
	const COLOUR_IN_PROGRESS = "#ECD995";
	const COLOUR_OVERDUE = "#FFE1E1";
	const COLOUR_DONE = "#CCCCCC";

	public static $calls_list_views = array();
	public static $calls_list_view = self::CALLS_CURRENT;

	public static $contacts_list_views = array();
	public static $contacts_list_view = self::CONTACTS_DEFAULT;

	public static $presentations_list_views = array();
	public static $presentations_list_view = self::PRESENTATIONS_DEFAULT;

	public static $offers_list_views = array();
	public static $offers_list_view = self::OFFERS_THIS_WEEK;

	private static $no_edit_contact_entry_props = array(
		"contact_entry_co_contact_1",
		"contact_entry_co_contact_2",
		"contact_entry_co_contact_3",
		"contact_entry_co_contact_1_profession",
		"contact_entry_co_contact_2_profession",
		"contact_entry_co_contact_3_profession",
		"contact_entry_contacts_subtitle_1",
		"contact_entry_contacts_subtitle_2",
		"contact_entry_contacts_subtitle_3",
		"contact_entry_contacts_title"
	);

	// ...
	private $contact_entry_edit_object; // object to be edited in contact entry view (crm_company or crm_person)

	function crm_sales ()
	{
		// LIST VIEW DEFINITIONS
		// caption - for showing to human users
		// in_tree - whether to show item in list view selection menus

		// predefined list views/searches for calls view
		self::$calls_list_views = array(
			self::CALLS_CURRENT => array(
				"caption" => t("Aktiivsed k&otilde;ned"),
				"in_tree" => true
			),
			self::CALLS_SEARCH => array(
				"caption" => t("Otsingu tulemused (kokku %s)"),
				"caption_no_count" => t("Otsingu tulemused (lehek&uuml;lg %s)"),
				"in_tree" => false
			)
		);

		// predefined list views/searches for contacts view
		self::$contacts_list_views = array(
			self::CONTACTS_DEFAULT => array(
				"caption" => t("Algseis"),
				"in_tree" => true
			),
			self::CONTACTS_CATEGORY => array(
				"caption" => t("Kliendid grupis '%s' (kokku %s)"),
				"caption_no_count" => t("Kliendid grupis '%s' (lehek&uuml;lg %s)"),
				"in_tree" => false
			),
			self::CONTACTS_SEARCH => array(
				"caption" => t("Otsingu tulemused (kokku %s)"),
				"caption_no_count" => t("Otsingu tulemused (lehek&uuml;lg %s)"),
				"in_tree" => false
			)
		);

		// predefined list views/searches for presentations view
		self::$presentations_list_views = array(
			self::PRESENTATIONS_DEFAULT => array(
				"caption" => t("K&otilde;ik esitlused"),
				"in_tree" => true
			),
			self::PRESENTATIONS_ADDED_TODAY => array(
				"caption" => t("T&auml;na loodud esitlused"),
				"in_tree" => true
			),
			self::PRESENTATIONS_TODAY => array(
				"caption" => t("T&auml;nased esitlused"),
				"in_tree" => true
			),
			self::PRESENTATIONS_TOMORROW => array(
				"caption" => t("Homsed esitlused"),
				"in_tree" => true
			),
			self::PRESENTATIONS_YESTERDAY => array(
				"caption" => t("Eilsed esitlused"),
				"in_tree" => true
			),
			self::PRESENTATIONS_SEARCH => array(
				"caption" => t("Otsingu tulemused (kokku %s)"),
				"in_tree" => false
			)
		);

		self::$offers_list_views = array(
			self::OFFERS_DEFAULT => array(
				"caption" => t("K&otilde;ik pakkumised"),
				"in_tree" => true
			),
			self::OFFERS_TODAY => array(
				"caption" => t("T&auml;nased pakkumised"),
				"in_tree" => true
			),
			self::OFFERS_YESTERDAY => array(
				"caption" => t("Eilsed pakkumised"),
				"in_tree" => true
			),
			self::OFFERS_THIS_WEEK => array(
				"caption" => t("K&auml;esoleva n&auml;dala pakkumised"),
				"in_tree" => true
			),
			self::OFFERS_LAST_WEEK => array(
				"caption" => t("M&ouml;&ouml;dunud n&auml;dala pakkumised"),
				"in_tree" => true
			),
			self::OFFERS_SEARCH => array(
				"caption" => t("Otsingu tulemused (kokku %s)"),
				"in_tree" => false
			),
		);

		// ...
		$this->init(array(
			"tpldir" => "applications/sales/crm_sales",
			"clid" => CL_CRM_SALES
		));
	}

	function callback_on_load($arr)
	{
		if ("calls" === $this->use_group and (
			!empty($arr["request"]["cs_name"]) or
			!empty($arr["request"]["cs_address"]) or
			!empty($arr["request"]["cs_phone"]) or
			!empty($arr["request"]["cs_lead_source"]) or
			!empty($arr["request"]["cs_salesman"]) or
			!empty($arr["request"]["cs_last_caller"]) or
			!empty($arr["request"]["cs_last_call_result"]) or
			!empty($arr["request"]["cs_caller"]) or
			!empty($arr["request"]["cs_call_result"]) or
			!empty($arr["request"]["cs_call_real_start_from"]) or
			!empty($arr["request"]["cs_call_real_start_to"]) or
			!empty($arr["request"]["cs_status"])
		))
		{ // determine requested calls list type
			self::$calls_list_view = self::CALLS_SEARCH;
		}
		elseif ("contacts" === $this->use_group)
		{ // determine requested contacts list type
			if (
				!empty($arr["request"]["cts_name"]) or
				!empty($arr["request"]["cts_address"]) or
				!empty($arr["request"]["cts_phone"]) or
				!empty($arr["request"]["cts_lead_source"]) or
				!empty($arr["request"]["cts_calls"]) or
				!empty($arr["request"]["cts_salesman"]) or
				!empty($arr["request"]["cts_status"])
			)
			{
				self::$contacts_list_view = self::CONTACTS_SEARCH;
			}
			elseif (!empty($arr["request"]["cts_cat"]))
			{
				self::$contacts_list_view = self::CONTACTS_CATEGORY;
			}
		}
		elseif ("presentations" === $this->use_group)
		{ // determine requested presentations list type
			$list_id = isset($arr["request"]["crmListId"]) ? (int) $arr["request"]["crmListId"] : 0;
			if (isset(self::$presentations_list_views[$list_id]))
			{
				self::$presentations_list_view = $list_id;
			}
			elseif (
				!empty($arr["request"]["ps_name"]) or
				!empty($arr["request"]["ps_address"]) or
				!empty($arr["request"]["ps_phone"]) or
				!empty($arr["request"]["ps_createdby"]) or
				!empty($arr["request"]["ps_result"]) or
				!empty($arr["request"]["ps_lead_source"]) or
				!empty($arr["request"]["ps_created_from"]) or
				!empty($arr["request"]["ps_created_to"]) or
				!empty($arr["request"]["ps_start_from"]) or
				!empty($arr["request"]["ps_start_to"]) or
				!empty($arr["request"]["ps_real_start_from"]) or
				!empty($arr["request"]["ps_real_start_to"]) or
				!empty($arr["request"]["ps_salesman"])
			)
			{
				self::$presentations_list_view = self::PRESENTATIONS_SEARCH;
			}
		}
		elseif ("offers" === $this->use_group || "statistics_offers" === $this->use_group)
		{
			$list_id = isset($arr["request"]["crmListId"]) ? (int) $arr["request"]["crmListId"] : 0;
			if(isset(self::$offers_list_views[$list_id]))
			{
					self::$offers_list_view = $list_id;
			}
			elseif(!empty($arr["request"]["os_customer"]))
			{
					self::$offers_list_view = self::OFFERS_SEARCH;
			}
		}
		elseif ("data_entry" === substr($this->use_group, 0, 10))
		{
			Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
			$this->zend_view = new Zend_View();
			$this->zend_view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
			$this->zend_view->dojo()->enable()
				->setDjConfigOption('parseOnLoad', true)
				->addStylesheetModule('dijit.themes.tundra');
		}
	}

	function callback_pre_edit($arr)
	{
		if (isset($arr["request"]["contact_list_load_contact"]))
		{ // if a contact is selected from last entries list then get its id
			try
			{
				$o = new object($arr["request"]["contact_list_load_contact"]);
				if (("data_entry_contact_co" === $this->use_group and $o->is_a(CL_CRM_COMPANY)) or ("data_entry_contact_person" === $this->use_group and $o->is_a(CL_CRM_PERSON)))
				{
					$this->contact_entry_edit_object = $o;
				}
			}
			catch (Exception $e)
			{
			}
		}
	}

	function callback_mod_layout(&$arr)
	{
		if (is_object($this->contact_entry_edit_object) and "de_form_box" === $arr["name"])
		{ // if a contact from last entries list is being edited then change entry form container layout caption
			$arr["area_caption"] = sprintf(t("Kontakti '%s' muutmine"), $this->contact_entry_edit_object->name());
		}
		return PROP_OK;
	}

	function _get_contact_entry_address(&$arr)
	{
		if (is_object($this->contact_entry_edit_object))
		{
			$contact = new object($this->contact_entry_edit_object->id());
			$address = $contact->get_first_obj_by_reltype("RELTYPE_ADDRESS_ALT");

			if ($address)
			{
				$arr["prop"]["edit_id"] = $address->id();
			}
		}
		return PROP_OK;
	}

	function _get_contact_entry_salesman(&$arr)
	{
		$r = PROP_OK;
		if (is_object($this->contact_entry_edit_object))
		{
			$owner = $arr["obj_inst"]->prop("owner");
			$customer_relation = $this->contact_entry_edit_object->get_customer_relation($owner);
			$arr["prop"]["value"] = $customer_relation->prop("salesman");
		}

		try
		{
			$oid = new aw_oid($arr["obj_inst"]->prop("role_profession_salesman"));
			$profession = obj($oid, array(), CL_CRM_PROFESSION);
			$this->set_employees_options($arr, $profession);
		}
		catch (Exception $e)
		{
		}

		try
		{
			$oid = new aw_oid($arr["obj_inst"]->prop("role_profession_sales_manager"));
			$profession = obj($oid, array(), CL_CRM_PROFESSION);
			$this->set_employees_options($arr, $profession);
		}
		catch (Exception $e)
		{
		}

		return $r;
	}

	protected function set_employees_options(&$arr, object $profession = null, $only_active = true)
	{
		$owner = $arr["obj_inst"]->prop("owner");
		$cache = new cache();
		settype($arr["prop"]["options"], "array");
		$relationship = $only_active ? "active" : "all";

		if (is_object($profession))
		{
			$key = "crm_sales_{$relationship}_employees_options_" . $profession->id();
			$employees_options = $cache->file_get_ts($key, $owner->modified());

			if (false === $employees_options)
			{
				$employees = $owner->get_employees($only_active, $profession);
				$employees_options = array("" => "") + $employees->names();
				$cache->file_set($key, serialize($employees_options));
			}
			else
			{
				$employees_options = unserialize($employees_options);
			}

			$arr["prop"]["options"] += $employees_options;
		}
		else
		{ // all employees
			$key = "crm_sales_{$relationship}_employees_options";
			$employees_options = $cache->file_get_ts($key, $owner->modified());

			if (false === $employees_options)
			{
				$employees_options = array("" => "") + $owner->get_worker_selection();
				$cache->file_set($key, serialize($employees_options));
			}
			else
			{
				$employees_options = unserialize($employees_options);
			}

			$arr["prop"]["options"] += $employees_options;
		}
	}

	function _get_contact_entry_lead_source(&$arr)
	{
		$r = PROP_OK;
		if (is_object($this->contact_entry_edit_object))
		{
			$owner = $arr["obj_inst"]->prop("owner");
			$customer_relation = $this->contact_entry_edit_object->get_customer_relation($owner);
			$arr["prop"]["value"] = $customer_relation->prop("sales_lead_source.name");
		}
		return $r;
	}

	function _get_contact_entry_organization(&$arr)
	{
		$r = PROP_OK;
		if (is_object($this->contact_entry_edit_object))
		{
			$organization_ids = $this->contact_entry_edit_object->get_organization_ids(false);
			$arr["prop"]["value"] = reset($organization_ids);
		}
		return $r;
	}

	function _get_contact_entry_category(&$arr)
	{
		$r = PROP_OK;

		if (is_object($this->contact_entry_edit_object))
		{
			$owner = $arr["obj_inst"]->prop("owner");
			$customer_relation = $this->contact_entry_edit_object->get_customer_relation($owner);
			$categories = new object_list($customer_relation->connections_from(array("type" => "RELTYPE_CATEGORY")));
			$arr["prop"]["value"] = $categories->ids();
		}

		$arr["prop"]["options"] = $arr["obj_inst"]->prop("owner")->get_customer_categories()->names();

		//TODO: better interface element
		// $this->zend_view->dojo()->requireModule('dijit.form.NumberSpinner');
		// return $this->zend_view->numberSpinner(
			// "content_table[{$row["row"]->id()}][price_component][{$row["price_component"]->id()}][value]",
			// $value,
			// array(
				// "min" => $min,
				// "max" => $max,
				// "places" => 0
			// ),
			// array(
				// "id" => "content_table_{$row["row"]->id()}_price_component_{$row["price_component"]->id()}_value",
			// )
		// ).($row["price_component"]->prop("is_ratio") ? t("%") : "");
		return $r;
	}

	function _get_contact_entry_lead_source_oid(&$arr)
	{
		$r = PROP_OK;
		if (is_object($this->contact_entry_edit_object))
		{
			$owner = $arr["obj_inst"]->prop("owner");
			$customer_relation = $this->contact_entry_edit_object->get_customer_relation($owner);
			$arr["prop"]["value"] = $customer_relation->prop("sales_lead_source");
		}
		return $r;
	}

	function _get_cs_salesman(&$arr)
	{
		return $this->get_salesman_search_prop($arr);
	}

	function _get_cts_salesman(&$arr)
	{
		return $this->get_salesman_search_prop($arr);
	}

	function _get_cts_sort_mode(&$arr)
	{
		$sort_mode_names = array(
			"" => t("Sorteerimata"),
			"name-asc" => t("Nimi &lt;"),
			"name-desc" => t("Nimi &gt;"),
			"last_call_time-asc" => t("Viimase k&otilde;ne aeg &lt;"),
			"last_call_time-desc" => t("Viimase k&otilde;ne aeg &gt;"),
			"last_call_maker-asc" => t("Viimase k&otilde;ne tegija &lt;"),
			"last_call_maker-desc" => t("Viimase k&otilde;ne tegija &gt;"),
			"last_call_result-asc" => t("Viimase k&otilde;ne tulemus &lt;"),
			"last_call_result-desc" => t("Viimase k&otilde;ne tulemus &gt;"),
			"calls_made-asc" => t("Tehtud k&otilde;nesid &lt;"),
			"calls_made-desc" => t("Tehtud k&otilde;nesid &gt;"),
			"lead_source-asc" => t("Soovitaja &lt;"),
			"lead_source-desc" => t("Soovitaja &gt;"),
			"salesman-asc" => t("M&uuml;&uuml;giesindaja &lt;"),
			"salesman-desc" => t("M&uuml;&uuml;giesindaja &gt;")
		);
		$arr["prop"]["value"] = isset($arr["request"]["cts_sort_mode"]) ? $arr["request"]["cts_sort_mode"] : "";
		$arr["prop"]["options"] = $sort_mode_names;
		return PROP_OK;
	}

	function _get_ps_createdby(&$arr)
	{
		$r = PROP_OK;
		$this->set_employees_options($arr, null, false);
		$arr["prop"]["value"] = isset($arr["request"]["ps_createdby"]) ? $arr["request"]["ps_createdby"] : "";
		return $r;
	}

	function _get_ps_result(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["options"] = array("" => "") + crm_presentation_obj::result_names();
		$arr["prop"]["value"] = isset($arr["request"]["ps_result"]) ? $arr["request"]["ps_result"] : "";
		return $r;
	}

	function _get_ps_salesman(&$arr)
	{
		$r = PROP_OK;

		if (crm_sales_obj::ROLE_SALESMAN === $arr["obj_inst"]->get_current_user_role())
		{
			$r = PROP_IGNORE;
		}
		else
		{
			$r = $this->get_salesman_search_prop($arr);
		}

		return $r;
	}

	function _get_ps_created_from(&$arr)
	{
		$r = PROP_OK;
		if (isset($arr["request"]["ps_created_from"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["ps_created_from"]);
		}
		return $r;
	}

	function _get_ps_created_to(&$arr)
	{
		$r = PROP_OK;
		if (isset($arr["request"]["ps_created_to"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["ps_created_to"]);
		}
		return $r;
	}

	function _get_ps_start_from(&$arr)
	{
		$r = PROP_OK;
		if (isset($arr["request"]["ps_start_from"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["ps_start_from"]);
		}
		return $r;
	}

	function _get_ps_start_to(&$arr)
	{
		$r = PROP_OK;
		if (isset($arr["request"]["ps_start_to"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["ps_start_to"]);
		}
		return $r;
	}

	function _get_ps_real_start_from(&$arr)
	{
		$r = PROP_OK;
		if (isset($arr["request"]["ps_real_start_from"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["ps_real_start_from"]);
		}
		return $r;
	}

	function _get_ps_real_start_to(&$arr)
	{
		$r = PROP_OK;
		if (isset($arr["request"]["ps_real_start_to"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["ps_real_start_to"]);
		}
		return $r;
	}

	private function get_salesman_search_prop(&$arr)
	{
		$r = PROP_IGNORE;
		$arr["prop"]["value"] = isset($arr["request"][$arr["prop"]["name"]]) ? $arr["request"][$arr["prop"]["name"]] : "";

		try
		{
			$oid = new aw_oid($arr["obj_inst"]->prop("role_profession_salesman"));
			$profession = obj($oid, array(), CL_CRM_PROFESSION);
			$this->set_employees_options($arr, $profession, false);
			$r = PROP_OK;
		}
		catch (Exception $e)
		{
		}

		try
		{
			$oid = new aw_oid($arr["obj_inst"]->prop("role_profession_sales_manager"));
			$profession = obj($oid, array(), CL_CRM_PROFESSION);
			$this->set_employees_options($arr, $profession, false);
			$r = PROP_OK;
		}
		catch (Exception $e)
		{
		}

		return $r;
	}

	function _get_call_result_notinterested_recall_time(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["value"] = isset($arr["prop"]["value"]) ? ceil($arr["prop"]["value"]/86400) : 0;
		return $r;
	}

	function _set_call_result_notinterested_recall_time(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["value"] = ceil($arr["prop"]["value"]*86400);
		return $r;
	}

	function _get_cs_last_caller(&$arr)
	{
		$this->set_employees_options($arr, null, false);
		$arr["prop"]["value"] = isset($arr["request"]["cs_last_caller"]) ? $arr["request"]["cs_last_caller"] : "";
		$r = PROP_OK;
		return $r;
	}

	function _get_cs_call_real_start_from(&$arr)
	{
		$r = $arr["obj_inst"]->has_privilege("call_edit") ? PROP_OK : PROP_IGNORE;
		if (isset($arr["request"]["cs_call_real_start_from"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["cs_call_real_start_from"]);
		}
		return $r;
	}

	function _get_cs_call_real_start_to(&$arr)
	{
		$r = $arr["obj_inst"]->has_privilege("call_edit") ? PROP_OK : PROP_IGNORE;
		if (isset($arr["request"]["cs_call_real_start_to"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["cs_call_real_start_to"]);
		}
		return $r;
	}

	function _get_cs_caller(&$arr)
	{
		$r = PROP_IGNORE;
		if ($arr["obj_inst"]->has_privilege("call_edit"))
		{
			$this->set_employees_options($arr, null, false);
			$arr["prop"]["value"] = isset($arr["request"]["cs_caller"]) ? $arr["request"]["cs_caller"] : "";
			$r = PROP_OK;
		}
		return $r;
	}

	function _get_cs_call_result(&$arr)
	{
		$r = PROP_IGNORE;
		if ($arr["obj_inst"]->has_privilege("call_edit"))
		{
			$arr["prop"]["options"] = array("" => "") + crm_call_obj::result_names();
			$arr["prop"]["value"] = isset($arr["request"]["cs_call_result"]) ? $arr["request"]["cs_call_result"] : "";
			$r = PROP_OK;
		}
		return $r;
	}

	function _get_cs_last_call_result(&$arr)
	{
		$arr["prop"]["options"] = array("" => "") + crm_call_obj::result_names();
		$arr["prop"]["value"] = isset($arr["request"]["cs_last_call_result"]) ? $arr["request"]["cs_last_call_result"] : "";
		$r = PROP_OK;
		return $r;
	}

	function _get_import_toolbar(&$arr)
	{
		$toolbar = $arr["prop"]["vcl_inst"];
		$toolbar->add_new_button(array(CL_CSV_IMPORT), $arr["obj_inst"]->id(), 7 /* RELTYPE_IMPORT */);
	}

	function _get_contact_entry_toolbar(&$arr)
	{
		$toolbar = $arr["prop"]["vcl_inst"];
		$toolbar->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => aw_url_change_var("contact_list_load_contact", null),
			"tooltip" => t("Sisesta uus kontakt")
		));
		$toolbar->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"action" => "",
			"tooltip" => t("Salvesta")
		));
	}

	function _get_import_objects(&$arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$table->define_field(array(
			"name" => "object",
			"caption" => t("Import")
		));
		$table->define_field(array(
			"name" => "commands"
		));
		$list = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_IMPORT")));

		if ($list->count() > 0)
		{
			$o = $list->begin();
			do
			{
				$table->define_data(array(
					"object" => html::obj_change_url($o, $o->name()),
					"commands" => ""
				));
			}
			while ($o = $list->next());
		}
	}

	function get_property(&$arr)
	{
		$ret = PROP_OK;

		if ("cs_status" === $arr["prop"]["name"] or "cts_status" === $arr["prop"]["name"])
		{ // set search status selection options
			$arr["prop"]["options"] = array("" => "") + crm_company_customer_data_obj::sales_state_names();
		}

		if ("calls" === $this->use_group)
		{ // calls view
			if (self::CALLS_SEARCH === self::$calls_list_view and substr($arr["prop"]["name"], 0, 3) === "cs_" and isset($arr["request"][$arr["prop"]["name"]]))
			{
				$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
			}

			$method_name = "_get_{$arr["prop"]["name"]}";
			if (method_exists("crm_sales_calls_view", $method_name))
			{
				$ret = crm_sales_calls_view::$method_name($arr);
			}
		}
		elseif ("contacts" === $this->use_group)
		{ // contacts view
			if (self::CONTACTS_SEARCH === self::$contacts_list_view and substr($arr["prop"]["name"], 0, 4) === "cts_" and isset($arr["request"][$arr["prop"]["name"]]))
			{
				$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
			}

			$method_name = "_get_{$arr["prop"]["name"]}";
			if (method_exists("crm_sales_contacts_view", $method_name))
			{
				$ret = crm_sales_contacts_view::$method_name($arr);
			}
		}
		elseif ("presentations" === $this->use_group)
		{ // presentations view
			if (self::PRESENTATIONS_SEARCH === self::$presentations_list_view and substr($arr["prop"]["name"], 0, 3) === "ps_" and isset($arr["request"][$arr["prop"]["name"]]))
			{
				$arr["prop"]["value"] = $arr["request"][$arr["prop"]["name"]];
			}

			$method_name = "_get_{$arr["prop"]["name"]}";
			if (method_exists("crm_sales_presentations_view", $method_name))
			{
				$ret = crm_sales_presentations_view::$method_name($arr);
			}
		}
		elseif ("_calendar" === substr($this->use_group, -9))
		{
			$method_name = "_get_{$arr["prop"]["name"]}";
			if (method_exists("crm_sales_calendar_view", $method_name))
			{
				$ret = crm_sales_calendar_view::$method_name($arr);
			}
		}
		elseif ("statistics_telemarketing" === $this->use_group)
		{
			$method_name = "_get_{$arr["prop"]["name"]}";
			if (method_exists("crm_sales_telemarketing_statistics_view", $method_name))
			{
				$ret = crm_sales_telemarketing_statistics_view::$method_name($arr);
			}
		}
		elseif ("offers" === $this->use_group)
		{
			$method_name = "_get_{$arr["prop"]["name"]}";
			if (method_exists("crm_sales_offers_view", $method_name))
			{
				$ret = crm_sales_offers_view::$method_name($arr);
			}
		}
		elseif ("settings_offers" === $this->use_group)
		{
			$method_name = "_get_{$arr["prop"]["name"]}";
			if (method_exists("crm_sales_settings_offers_view", $method_name))
			{
				$ret = crm_sales_settings_offers_view::$method_name($arr);
			}
		}
		elseif ("statistics_offers" === $this->use_group)
		{
			$method_name = "_get_{$arr["prop"]["name"]}";
			if (method_exists("crm_sales_statistics_offers_view", $method_name))
			{
				$ret = crm_sales_statistics_offers_view::$method_name($arr);
			}
		}
		elseif (is_object($this->contact_entry_edit_object) and in_array($arr["prop"]["name"], self::$no_edit_contact_entry_props))
		{ // hide properties that aren't editable when editing an added contact in contact entry view
			$ret = PROP_IGNORE;
		}

		return $ret;
	}

	function set_property(&$arr)
	{
		$ret = PROP_OK;

		if ("settings_offers" === $this->use_group)
		{
			$method_name = "_set_{$arr["prop"]["name"]}";
			if (method_exists("crm_sales_settings_offers_view", $method_name))
			{
				$ret = crm_sales_settings_offers_view::$method_name($arr);
			}
		}

		return $ret;
	}

	function callback_generate_scripts($arr)
	{
		$js = "\n\n/* crm_sales scripts */\n(function(){\n";

		if (isset($this->use_group))
		{
			if("data_entry_contact_co" === $this->use_group)
			{
				load_javascript("bsnAutosuggest.js");
				$name_options_url = $this->mk_my_orb("get_entry_choices", array(
					"type" => "co_name",
					"id" => $arr["obj_inst"]->id()
				));
				$phone_options_url = $this->mk_my_orb("get_entry_choices", array(
					"type" => "co_phone",
					"id" => $arr["obj_inst"]->id()
				));
				$contact_details_url = $this->mk_my_orb("get_contact_details", array("id" => $arr["obj_inst"]->id()));
				$lead_source_options_url = $this->mk_my_orb("get_lead_source_choices", array("id" => $arr["obj_inst"]->id()));
				$contact_edit_caption = t("Kontakti '%s' muutmine");
				$js .= <<<SCRIPTVARIABLES
var optionsUrl = "{$name_options_url}&";
var phoneOptionsUrl = "{$phone_options_url}&";
var leadSourceOptionsUrl = "{$lead_source_options_url}&";
var contactDetailsUrl = "{$contact_details_url}";
var contactEditCaption = "{$contact_edit_caption}";
SCRIPTVARIABLES;
				$js .= file_get_contents(AW_DIR . "classes/applications/crm/sales/crm_sales_co_entry.js");
			}
			elseif("data_entry_contact_person" === $this->use_group)
			{
				load_javascript("bsnAutosuggest.js");
				$name_options_url = $this->mk_my_orb("get_entry_choices", array(
					"type" => "p_name",
					"id" => $arr["obj_inst"]->id()
				));
				$phone_options_url = $this->mk_my_orb("get_entry_choices", array(
					"type" => "p_phone",
					"id" => $arr["obj_inst"]->id()
				));
				$contact_details_url = $this->mk_my_orb("get_contact_details", array("id" => $arr["obj_inst"]->id()));
				$lead_source_options_url = $this->mk_my_orb("get_lead_source_choices", array("id" => $arr["obj_inst"]->id()));
				$contact_edit_caption = t("Kontakti '%s' muutmine");
				$js .= <<<SCRIPTVARIABLES
var optionsUrl = "{$name_options_url}&";
var phoneOptionsUrl = "{$phone_options_url}&";
var leadSourceOptionsUrl = "{$lead_source_options_url}&";
var contactEditCaption = "{$contact_edit_caption}";
var contactDetailsUrl = "{$contact_details_url}";
SCRIPTVARIABLES;
				$js .= file_get_contents(AW_DIR . "classes/applications/crm/sales/crm_sales_person_entry.js");
			}
			elseif("contacts" === $this->use_group or "calls" === $this->use_group or "presentations" === $this->use_group)
			{ // attach table ft_page clearing to search form
				$js .= <<<SCRIPT
$("form[name='changeform']").bind("awbeforesubmit", function() {
	$("select[name='ft_page']").attr("value", "0");
	return true;
});
SCRIPT;
			}
		}

		$js .= "})()\n/* END crm_sales scripts */\n\n";
		return $js;
	}

	function parse_properties($args = array())
	{
		$r = parent::parse_properties($args);

		if (isset($r["contact_entry_co_name"]))
		{
			// disable company name std autocomplete
			unset($r["contact_entry_co_name"]["autocomplete_source"]);
			unset($r["contact_entry_co_name"]["autocomplete_params"]);
		}

		return $r;
	}

	/** Outputs autocomplete options matching category name search string $typed_text in bsnAutosuggest format json
		@attrib name=get_category_options
		@param id required type=oid
		@param typed_text optional type=string
	**/
	public static function get_category_options($args)
	{
		$choices = array("results" => array());
		$typed_text = $args["typed_text"];
		$this_o = new object($args["id"]);
		$limit = $this_o->prop("autocomplete_options_limit") ? (int) $this_o->prop("autocomplete_options_limit") : 20;
		$list = new object_list(array(
			"class_id" => CL_CRM_CATEGORY,
			"organization" => $this_o->prop("owner")->id(),
			"name" => "{$typed_text}%",
			new obj_predicate_limit($limit)
		));

		if ($list->count() > 0)
		{
			$results = array();
			$o = $list->begin();
			do
			{
				$value = $o->prop_xml("name");
				$info = "";
				$results[] = array("id" => $o->id(), "value" => iconv("iso-8859-4", "UTF-8", $value), "info" => $info);//FIXME charsets
			}
			while ($o = $list->next());
			$choices["results"] = $results;
		}

		ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		exit(json_encode($choices));
	}

	/** Outputs autocomplete options matching profession name search string $typed_text in bsnAutosuggest format json
		@attrib name=get_profession_options
		@param id required type=oid
		@param contact_entry_organization optional type=oid
		@param typed_text optional type=string
	**/
	public static function get_profession_options($args)
	{
		$choices = array("results" => array());
		$typed_text = $args["typed_text"];
		$this_o = new object($args["id"]);
		$limit = $this_o->prop("autocomplete_options_limit") ? (int) $this_o->prop("autocomplete_options_limit") : 20;
		$list = new object_list(array(
			"class_id" => CL_CRM_CATEGORY,
			"organization" => $this_o->prop("owner")->id(),
			"name" => "{$typed_text}%",
			new obj_predicate_limit($limit)
		));

		if ($list->count() > 0)
		{
			$results = array();
			$o = $list->begin();
			do
			{
				$value = $o->prop_xml("name");
				$info = "";
				$results[] = array("id" => $o->id(), "value" => iconv("iso-8859-4", "UTF-8", $value), "info" => $info);//FIXME charsets
			}
			while ($o = $list->next());
			$choices["results"] = $results;
		}

		ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		exit(json_encode($choices));
	}

	/** Outputs crm_company/crm_person autocomplete options matching property string in bsnAutosuggest format json
		@attrib name=get_entry_choices all_args=1 nologin=1 is_public=1
		@param id required type=oid acl=view
		@param type required type=string
			Determines if person or company searched, and by which property (name, phone nr.)
			Options: co_name, p_name, co_phone, p_phone
		@param typed_text optional type=string
	**/
	function get_entry_choices($arr)
	{
		$type = $arr["type"];
		$choices = array("results" => array());
		if (isset($arr["typed_text"]) and strlen($arr["typed_text"]) > 1)
		{
			$this_o = new object($arr["id"]);
			$typed_text = $arr["typed_text"];
			if ("p_name" === $type or "co_name" === $type)
			{
				$choices["results"] = $this->get_name_entry_choices($this_o, $typed_text, $type);
			}
			elseif ("p_phone" === $type or "co_phone" === $type)
			{
				$choices["results"] = $this->get_phone_entry_choices($this_o, $typed_text, $type);
			}
		}

		ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		// header ("Content-type: text/javascript; charset: UTF-8");
		// header("Expires: ".gmdate("D, d M Y H:i:s", time()+43200)." GMT");
		exit(json_encode($choices));
	}

	private function get_phone_entry_choices($this_o, $typed_text, $type)
	{
		// phone entry choices retrieved by first searching for more phones and then filtering out ones that aren't connected to this sales application
		// searching for phones via customer relation > customer > phone joins over aliases table is slow when tables are large
		$types_data = array(
			"co_phone" => array(
				"prop" => "buyer(CL_CRM_COMPANY).RELTYPE_PHONE.name",
				"clid" => CL_CRM_COMPANY,
				"info_prop1" => "buyer.name",
				"info_prop2" => "buyer.RELTYPE_ADDRESS.name"
			),
			"p_phone" => array(
				"prop" => "buyer(CL_CRM_PERSON).RELTYPE_PHONE.name",
				"clid" => CL_CRM_PERSON,
				"info_prop1" => "buyer.name",
				"info_prop2" => "buyer.RELTYPE_ADDRESS.name"
			)
		);
		$owner = $this_o->prop("owner");
		$prop = $types_data[$type]["prop"];
		$limit = $this_o->prop("autocomplete_options_limit") ? (int) $this_o->prop("autocomplete_options_limit") : 20;
		$results = array();

		$list = new object_list(array(
			"class_id" => CL_CRM_PHONE,
			"name" => "{$typed_text}%",
			new obj_predicate_limit($limit*4),// an arbitrary multiplier
			new obj_predicate_sort(array("name" => obj_predicate_sort::ASC))// shorter numbers in front
		));

		if ($list->count() > 0)
		{
			$phone_o = $list->begin();
			$count = -1;
			do
			{
				$customer_connections = $phone_o->connections_to(array(
					"from.class_id" => $types_data[$type]["clid"],
					"type" => "RELTYPE_PHONE"
				));
				if (count($customer_connections) !== 0)
				{
					$customer_connection = reset($customer_connections);
					$customer = $customer_connection->from();
					$cro_list = new object_list(array(
						"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
						"buyer" => $customer->id(),
						"seller" => $owner->id(),
					));
					$customer_relation = $cro_list->begin();

					if ($customer_relation)
					{
						++$count;
						$phones = new object_list($customer->connections_from(array("type" => "RELTYPE_PHONE")));
						$phones = $phones->names();
						$phone = "";

						if (strpos($type, "phone"))
						{
							foreach ($phones as $phone_oid => $phone_nr)
							{
								if (substr($phone_nr, 0, strlen($typed_text)) === $typed_text)
								{
									$phone = $phone_nr;
								}
							}
							$info1 = $customer->name();
						}
						else
						{
							$info1 = implode(", ", $phones);
						}

						$customer_name = strpos($type, "co_") !== false ? $customer->name() : ($customer->lastname . ", " . $customer->firstname);
						$value = strpos($type, "phone") !== false ? $phone : $customer_name;
						$info = $info1 . " | " . $customer_relation->prop($types_data[$type]["info_prop2"]);
						$results[] = array("id" => $customer_relation->prop("buyer"), "value" => iconv("iso-8859-4", "UTF-8", $value), "info" => $info);
					}
				}
			}
			while ($count !== $limit and ($phone_o = $list->next()));
		}

		return $results;
	}

	private function get_name_entry_choices($this_o, $typed_text, $type)
	{
		$types_data = array(
			"co_name" => array(
				"prop" => "buyer(CL_CRM_COMPANY).name",
				"info_prop1" => "buyer.name",
				"info_prop2" => "buyer.RELTYPE_ADDRESS.name"
			),
			"p_name" => array(
				"prop" => "buyer(CL_CRM_PERSON).lastname",
				"info_prop1" => "buyer.name",
				"info_prop2" => "buyer.RELTYPE_ADDRESS.name"
			)
		);

		$owner = $this_o->prop("owner");
		$prop = $types_data[$type]["prop"];
		$limit = $this_o->prop("autocomplete_options_limit") ? $this_o->prop("autocomplete_options_limit") : 20;
		$results = array();

		$list = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			$prop => "{$typed_text}%",
			"seller" => $owner->id(),
			new obj_predicate_limit($limit),
			new obj_predicate_sort(array($prop => obj_predicate_sort::ASC))// shorter names in front
		));

		if ($list->count() > 0)
		{
			$o = $list->begin();
			do
			{
				$customer = new object($o->prop("buyer"));
				$phones = new object_list($customer->connections_from(array("type" => "RELTYPE_PHONE")));
				$phones = $phones->names();
				$phone = "";

				if (strpos($type, "phone"))
				{
					foreach ($phones as $phone_oid => $phone_nr)
					{
						if (substr($phone_nr, 0, strlen($typed_text)) === $typed_text)
						{
							$phone = $phone_nr;
						}
					}
					$info1 = $customer->name();
				}
				else
				{
					$info1 = implode(", ", $phones);
				}

				$customer_name = strpos($type, "co_") !== false ? $customer->name() : ($customer->lastname . ", " . $customer->firstname);
				$value = strpos($type, "phone") !== false ? $phone : $customer_name;
				$info = $info1 . " | " . $o->prop($types_data[$type]["info_prop2"]);
				$results[] = array("id" => $o->prop("buyer"), "value" => iconv("iso-8859-4", "UTF-8", $value), "info" => $info);
			}
			while ($o = $list->next());
		}

		return $results;
	}

	/** Outputs lead source autocomplete options in bsnAutosuggest format json
		@attrib name=get_lead_source_choices all_args=1 nologin=1 is_public=1
		@param id required type=oid acl=view
		@param typed_text optional type=string
	**/
	function get_lead_source_choices($arr)
	{
		$choices = array("results" => array());
		if (isset($arr["typed_text"]) and strlen($arr["typed_text"]) > 1)
		{
			$this_o = new object($arr["id"]);
			$owner = $this_o->prop("owner");
			$typed_text = $arr["typed_text"];
			$limit = $this_o->prop("autocomplete_options_limit") ? $this_o->prop("autocomplete_options_limit") : 20;

			$list = new object_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_COMPANY).name" => "{$typed_text}%",
						"CL_CRM_COMPANY_CUSTOMER_DATA.buyer(CL_CRM_PERSON).lastname" => "{$typed_text}%"
					)
				)),
				"seller" => $owner->id(),
				new obj_predicate_limit($limit)
			));

			if ($list->count() > 0)
			{
				$results = array();
				$o = $list->begin();
				do
				{
					$customer = new object($o->prop("buyer"));
					$phones = new object_list($customer->connections_from(array("type" => "RELTYPE_PHONE")));
					$phones = $phones->names();
					$info = implode(", ", $phones) . " | " . $o->prop("buyer.RELTYPE_ADDRESS.name");
					$value = $customer->class_id() == CL_CRM_COMPANY ? $customer->name() : ($customer->lastname . ", " . $customer->firstname);
					$results[] = array("id" => $customer->id(), "value" => $value, "info" => $info);
				}
				while ($o = $list->next());
				$choices["results"] = $results;
			}
		}

		ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		// header ("Content-type: text/javascript; charset: UTF-8");
		// header("Expires: ".gmdate("D, d M Y H:i:s", time()+43200)." GMT");
		exit(json_encode($choices));
	}

	/** Outputs crm_company/crm_person property details in json format
		@attrib name=get_contact_details all_args=1 nologin=1 is_public=1
		@param id required type=oid acl=view
		@param contact_id required type=oid acl=view
	**/
	function get_contact_details($arr)
	{
		$o = new object($arr["contact_id"]);
		$class = basename(aw_ini_get("classes." . $o->class_id() . ".file"));
		$i = new crm_sales();
		$i->output_client = "jsonclient";
		$group = (CL_CRM_COMPANY == $o->class_id()) ? "data_entry_contact_co" : "data_entry_contact_person";
		$args = array(
			"id" => $arr["id"],
			"contact_list_load_contact" => $arr["contact_id"],
			"action" => "change",
			"group" => $group,
			"class" => "crm_sales"
		);
		$r = $i->change($args);
		$r = substr(trim($r), 0, -1) . ",\"id\":\"{$arr["contact_id"]}\"}";
		exit($r);
	}

	/** Creates calls for contacts specified in sel oid array. Won't create calls for contacts that have active unstarted calls
		@attrib name=create_calls_for_selected_contacts
		@param id required type=oid acl=edit
		@param sel optional type=array
		@param post_ru optional type=string
	**/
	function create_calls_for_selected_contacts($arr)
	{
		$this_o = new object($arr["id"]);
		$new_calls_created = 0;
		$existing_calls_found = 0;
		$time = time();
		foreach ($arr["sel"] as $cro_oid)
		{
			$customer_relation = obj((int) $cro_oid, array(), CL_CRM_COMPANY_CUSTOMER_DATA);
			$call = $this_o->create_call($customer_relation, 0, null, true);

			if ($call->modified() < $time)
			{
				++$existing_calls_found;
			}
			else
			{
				++$new_calls_created;
			}
		}

		$this->show_msg_text(sprintf(t("Loodud %s uut k&otilde;net. Kokku t&ouml;&ouml;deldud %s kontakti."), $new_calls_created, ($new_calls_created+$existing_calls_found)));
		$return_url = empty($arr["post_ru"]) ? $this->mk_my_orb("change", array("id" => $arr["id"], "group" => "contacts"), "crm_sales") : $arr["post_ru"];
		return $return_url;
	}

	/** Creates calls for contacts specified by search arguments. Won't create calls for contacts that have active unstarted calls
		@attrib name=create_calls_for_found_contacts
		@param id required type=oid acl=edit
		@param post_ru required type=string
	**/
	function create_calls_for_found_contacts($arr)
	{
		$return_url = $this->mk_my_orb("change", array("id" => $arr["id"], "group" => "contacts"), "crm_sales");
		$this_o = new object($arr["id"]);
		$existing_calls_found = $new_calls_created = 0;
		$request = automatweb::$request->get_args();
		$search = new crm_sales_contacts_search();

		try
		{
			$search->seller = $this_o->prop("owner");
			$params_defined = false;


			if (!empty($request["cts_name"]))
			{
				$search->name = $request["cts_name"];
				$params_defined = true;
			}

			if (!empty($request["cts_phone"]))
			{
				$search->phone = $request["cts_phone"];
				$params_defined = true;
			}

			if (!empty($request["cts_salesman"]))
			{
				$search->salesman = $request["cts_salesman"];
				$params_defined = true;
			}

			if (!empty($request["cts_calls"]))
			{
				$calls_constraint = null;
				$tmp = preg_split("/\s*/", $request["cts_calls"], 2, PREG_SPLIT_NO_EMPTY);
				if (1 === count($tmp) and is_numeric($tmp[0]))
				{
					$calls_constraint = new obj_predicate_compare(obj_predicate_compare::EQUAL, (int) $tmp[0]);
				}
				elseif (2 === count($tmp) and is_numeric($tmp[1]))
				{
					$calls_compare_value = (int) $tmp[1];
					if ("<" === $tmp[0] and $calls_compare_value)
					{
						$calls_constraint = new obj_predicate_compare(obj_predicate_compare::LESS, $calls_compare_value);
					}
					elseif (">" === $tmp[0])
					{
						$calls_constraint = new obj_predicate_compare(obj_predicate_compare::GREATER, $calls_compare_value);
					}
					elseif ("=" === $tmp[0])
					{
						$calls_constraint = new obj_predicate_compare(obj_predicate_compare::EQUAL, (int) $tmp[1]);
					}
					else
					{
						class_base::show_error_text(t("Viga k&otilde;nede arvu v&otilde;rdlusoperaatoris"));
					}
				}
				else
				{
					class_base::show_error_text(t("Viga k&otilde;nede arvu parameetris"));
				}

				$search->calls = $calls_constraint;
				$params_defined = true;
			}

			if (!empty($request["cts_lead_source"]))
			{
				$search->lead_source = $request["cts_lead_source"];
				$params_defined = true;
			}

			if (!empty($request["cts_address"]))
			{
				$search->address = $request["cts_address"];
				$params_defined = true;
			}

			if (!empty($request["cts_status"]))
			{
				$search->status = $request["cts_status"];
				$params_defined = true;
			}

			if ($params_defined)
			{
				$contacts_oid_data = $search->get_oids();

				if (isset($contacts_oid_data[0]))
				{
					foreach ($contacts_oid_data as $key => $value)
					{
						$contacts_oid_data[$key] = $value["oid"];
					}
				}
				else
				{
					$contacts_oid_data = array_keys($contacts_oid_data);
				}


				list($new_calls_created, $existing_calls_found, $status) = $this_o->create_calls($contacts_oid_data);

				$this->show_msg_text(sprintf(t("Loodud %s uut k&otilde;net. Kokku t&ouml;&ouml;deldud %s kontakti."), $new_calls_created, ($new_calls_created+$existing_calls_found)));

				if (1 === $status)//!!! tmp
				{
					$this->show_error_text(t("Serveri m&auml;lukasutus l&auml;henes kriitilisele piirile, k&otilde;nede loomine katkestati enne k&otilde;igi kontaktide t&ouml;&ouml;tlemist."));
				}
			}
			else
			{
				$this->show_error_text(t("Otsinguparameetrid m&auml;&auml;ramata."));
			}
		}
		catch (awex_crm_contacts_search_param $e)
		{
			$param_translations = array(
				crm_sales_contacts_search::PARAM_NONE => t("&Uuml;htki parameetrit pole m&auml;&auml;ratud"),
				crm_sales_contacts_search::PARAM_NAME => t("Sobimatu kliendi nime v&auml;&auml;rtus"),
				crm_sales_contacts_search::PARAM_SALESMAN => t("Sobimatu m&uuml;&uuml;giesindaja v&auml;&auml;rtus"),
				crm_sales_contacts_search::PARAM_LEAD_SOURCE => t("Sobimatu soovitaja v&auml;&auml;rtus"),
				crm_sales_contacts_search::PARAM_CALLS => t("Sobimatu k&otilde;nede arvu v&auml;&auml;rtus"),
				crm_sales_contacts_search::PARAM_STATUS => t("Sobimatu staatuse v&auml;&auml;rtus"),
				crm_sales_contacts_search::PARAM_ADDRESS => t("Sobimatu aadressi v&auml;&auml;rtus"),
				crm_sales_contacts_search::PARAM_PHONE => t("Sobimatu telefoninumbri v&auml;&auml;rtus")
			);
			$code = $e->getCode();

			if (!isset($param_translations[$code]))
			{
				throw $e;
			}

			$param = $param_translations[$code];
			class_base::show_error_text(t("Viga otsinguparameetrites. {$param}"));
		}

		exit ('<script type="text/javascript">window.location = "'.$return_url.'"</script>');
		// return $return_url;
	}

	function _get_contact_entry_co(&$arr)
	{
		if (is_object($this->contact_entry_edit_object))
		{
			$arr["prop"]["edit_id"] = $this->contact_entry_edit_object->id();
		}
		return PROP_OK;
	}

	function _get_contact_entry_person(&$arr)
	{
		if (is_object($this->contact_entry_edit_object))
		{
			$arr["prop"]["edit_id"] = $this->contact_entry_edit_object->id();
		}
		return PROP_OK;
	}

	function _get_contact_entry_empoyee(&$arr)
	{
		if (is_object($this->contact_entry_edit_object))
		{
			$arr["prop"]["edit_id"] = $this->contact_entry_edit_object->id();
		}
		return PROP_OK;
	}

	function _get_contact_entry_reset(&$arr)
	{
		$arr["prop"]["value"] = html::button(array(
			"type" => "reset",
			"id" => "button",
			"onclick" => "document.forms['changeform'].reset();$('form[name=changeform]').reset();",
			"value" => t("T&uuml;hjenda")
		));
		return PROP_OK;
	}

	function _set_contact_entry_co(&$arr)
	{
		$return = PROP_IGNORE;
		$this_o = $arr["obj_inst"];
		$phone_nr = isset($arr["prop"]["value"]["fake_phone"]) ? $arr["prop"]["value"]["fake_phone"] : null;
		$owner = $arr["obj_inst"]->prop("owner");

		// search from existing phone nr-s
		$phone_nr_list = new object_list(array(
			"class_id" => CL_CRM_PHONE,
			"name" => $phone_nr,
		));

		if (empty($arr["prop"]["value"]["fake_phone"]))
		{ // phone must be defined
			$return = PROP_FATAL_ERROR;
			$arr["prop"]["error"] = t("Telefoninumber on kohustuslik");
		}
		elseif (!isset($arr["prop"]["value"]["id"]) and $phone_nr_list->count() === 1)
		{ // phone nr for a contact to be created is given but already exists
			$arr["prop"]["error"] = t("Telefoninumber on juba andmebaasis olemas. Uuesti lisada ei saa.");
			$return = PROP_FATAL_ERROR;
		}
		elseif ($phone_nr_list->count() > 1)
		{ // duplicate phones found
			$arr["prop"]["error"] = t("Antud telefoninumber esineb andmebaasis mitmekordselt. Vajalik on andmebaasi korrastus.");
			$return = PROP_FATAL_ERROR;
		}
		elseif (isset($arr["prop"]["value"]["id"]))
		{
			$o = obj($arr["prop"]["value"]["id"], array(), CL_CRM_COMPANY, true);
			$o->set_name($arr["prop"]["value"]["name"]);
			$o->set_prop("ettevotlusvorm", $arr["prop"]["value"]["ettevotlusvorm"]);
			$o->set_prop("fake_phone", $arr["prop"]["value"]["fake_phone"]);
			$o->set_prop("fake_mobile", $arr["prop"]["value"]["fake_mobile"]);
			$o->set_prop("fake_email", $arr["prop"]["value"]["fake_email"]);
			$o->save();
			$customer_relation = $o->get_customer_relation($owner);

			// comment
			if (!empty($arr["request"]["contact_entry_add_comment"]))
			{
				$parent = $customer_relation->id();
				$comment_text = t("Andmete sisestamine:\n") . $arr["request"]["contact_entry_add_comment"];
				$existing_comments = new object_list(array(
					"class_id" => CL_COMMENT,
					"commtext" => $comment_text,
					"parent" => $parent
				));

				if ($existing_comments->count() < 1)
				{
					$comment = new forum_comment();
					$comment->submit(array(
						"parent" => $parent,
						"commtext" => $comment_text,
						"return" => "id"
					));
				}
			}

			// category
			if (!empty($arr["request"]["contact_entry_category"]))
			{
				$selected_categories = $arr["request"]["contact_entry_category"];
				// remove unselected
				foreach ($customer_relation->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
				{
					$category = $c->to();
					if (!in_array($category->id(), $selected_categories))
					{
						$customer_relation->disconnect(array("from" => $category->id()));
					}
				}

				// add categories
				foreach ($selected_categories as $category_oid)
				{
					$category = obj($category_oid, array(), CL_CRM_CATEGORY);
					if (!$customer_relation->is_connected_to(array("to" => $category, "type" => "RELTYPE_CATEGORY")))
					{
						$customer_relation->connect(array(
							"to" => $category,
							"type" => "RELTYPE_CATEGORY"
						));
					}
				}
			}

			// address
			if (is_oid($arr["request"]["contact_entry_address"]["location_data"]))
			{
				// edit old address or create new if not found
				$address = $o->get_first_obj_by_reltype("RELTYPE_ADDRESS_ALT");
				$new_address = false;
				if (!$address)
				{
					$address = obj(null, array(), CL_ADDRESS);
					$address->set_parent($o->id());
					$new_address = true;
				}

				$address->set_prop("country", $arr["request"]["contact_entry_address"]["country"]);
				$address->set_location($arr["request"]["contact_entry_address"]["location_data"]);
				$address->set_prop("street", $arr["request"]["contact_entry_address"]["street"]);
				$address->set_prop("house", $arr["request"]["contact_entry_address"]["house"]);
				$address->set_prop("apartment", $arr["request"]["contact_entry_address"]["apartment"]);
				$address->set_prop("postal_code", $arr["request"]["contact_entry_address"]["postal_code"]);
				$address->set_prop("po_box", $arr["request"]["contact_entry_address"]["po_box"]);
				$address->save();

				if ($new_address)
				{
					$o->connect(array("to" => $address, "reltype" => "RELTYPE_ADDRESS_ALT"));
				}
			}
			$return = PROP_IGNORE;
		}
		else
		{
			try
			{
				$owner_oid = $this_o->prop("owner")->id();

				$o = obj(null, array(), CL_CRM_COMPANY);
				$o->set_parent($owner_oid);
				$o->set_name($arr["prop"]["value"]["name"]);
				$o->set_prop("ettevotlusvorm", $arr["prop"]["value"]["ettevotlusvorm"]);
				$o->save();
				$o->set_prop("fake_phone", $arr["prop"]["value"]["fake_phone"]);
				$o->set_prop("fake_mobile", $arr["prop"]["value"]["fake_mobile"]);
				$o->set_prop("fake_email", $arr["prop"]["value"]["fake_email"]);
				$o->save();

				// address
				if (is_oid($arr["request"]["contact_entry_address"]["location_data"]))
				{
					$address = obj(null, array(), CL_ADDRESS);
					$address->set_parent($o->id());
					$address->set_prop("country", $arr["request"]["contact_entry_address"]["country"]);
					$address->set_location($arr["request"]["contact_entry_address"]["location_data"]);
					$address->set_prop("street", $arr["request"]["contact_entry_address"]["street"]);
					$address->set_prop("house", $arr["request"]["contact_entry_address"]["house"]);
					$address->set_prop("apartment", $arr["request"]["contact_entry_address"]["apartment"]);
					$address->set_prop("postal_code", $arr["request"]["contact_entry_address"]["postal_code"]);
					$address->set_prop("po_box", $arr["request"]["contact_entry_address"]["po_box"]);
					$address->save();
					$o->connect(array("to" => $address, "reltype" => "RELTYPE_ADDRESS_ALT"));
				}

				$customer_relation = $o->get_customer_relation($owner, true);

				// category
				if (!empty($arr["request"]["contact_entry_category"]))
				{
					foreach ($arr["request"]["contact_entry_category"] as $category_oid)
					{
						$category = obj($category_oid, array(), CL_CRM_CATEGORY);
						$customer_relation->connect(array(
							"to" => $category,
							"type" => "RELTYPE_CATEGORY"
						));
					}
				}

				if (!empty($arr["request"]["contact_entry_salesman"]))
				{ // set salesman
					$salesman = obj($arr["request"]["contact_entry_salesman"], array(), CL_CRM_PERSON);
					$customer_relation->set_prop("salesman", $salesman->id());
					$customer_relation->connect(array("to" => $salesman, "reltype" => "RELTYPE_SALESMAN"));
				}

				if (!empty($arr["request"]["contact_entry_lead_source_oid"]))
				{ // set lead source
					$lead_source = new object($arr["request"]["contact_entry_lead_source_oid"]);

					if (!$lead_source->is_a(CL_CRM_COMPANY) and !$lead_source->is_a(CL_CRM_PERSON))
					{
						throw new awex_obj_class("Invalid class. Lead source must be a company or a person");
					}

					$customer_relation->set_prop("sales_lead_source", $lead_source->id());
					$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_LEAD);
					$customer_relation->connect(array("to" => $lead_source, "reltype" => "RELTYPE_SALES_LEAD_SOURCE"));
				}
				elseif (!empty($arr["request"]["contact_entry_lead_source"]))
				{
					$name = explode(" ", $arr["request"]["contact_entry_lead_source"]);
					foreach ($name as $key => $name_part)
					{
						$name[$key] = ucfirst($name_part);
					}

					$lastname = array_pop($name);
					$firstname = count($name) > 1 ? implode("-", $name) : array_pop($name);
					$lead_source = obj(null, array(), CL_CRM_PERSON);
					$lead_source->set_parent($owner_oid);
					$lead_source->set_prop("firstname", $firstname);
					$lead_source->set_prop("lastname", $lastname);
					$lead_source->save();
					$customer_relation->set_prop("sales_lead_source", $lead_source->id());
					$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_LEAD);
					$customer_relation->connect(array("to" => $lead_source, "reltype" => "RELTYPE_SALES_LEAD_SOURCE"));
				}

				// additional comment
				if (!empty($arr["request"]["contact_entry_add_comment"]))
				{ // add comment
					$parent = $customer_relation->id();
					$comment_text = t("Andmete sisestamine:\n") . $arr["request"]["contact_entry_add_comment"];
					$comment = new forum_comment();
					$comment->submit(array(
						"parent" => $parent,
						"commtext" => $comment_text,
						"return" => "id"
					));
				}

				// contact people
				if (!empty($arr["request"]["contact_entry_co_contact_1"]["firstname"]) or !empty($arr["request"]["contact_entry_co_contact_1"]["lastname"]))
				{
					// add first contact person to entered company
					$contact_person = obj(null, array(), CL_CRM_PERSON);
					$contact_person->set_parent($o->id());
					$contact_person->set_prop("firstname", ucfirst($arr["request"]["contact_entry_co_contact_1"]["firstname"]));
					$contact_person->set_prop("lastname", ucfirst($arr["request"]["contact_entry_co_contact_1"]["lastname"]));
					$contact_person->save();
					$save_again = false;

					if (!empty($arr["request"]["contact_entry_co_contact_1"]["fake_phone"]))
					{
						$contact_person->set_prop("fake_phone", $arr["request"]["contact_entry_co_contact_1"]["fake_phone"]);
						$save_again = true;
					}

					if (!empty($arr["request"]["contact_entry_co_contact_1"]["fake_phone"]))
					{
						$contact_person->set_prop("fake_email", $arr["request"]["contact_entry_co_contact_1"]["fake_email"]);
						$save_again = true;
					}

					if ($save_again)
					{
						$contact_person->save();
					}

					if (empty($arr["request"]["contact_entry_co_contact_1_profession"]))
					{
						$profession = null;
					}
					else
					{ // create new profession in entered company
						$profession = obj(null, array(), CL_CRM_PROFESSION);
						$profession->set_parent($o->id());
						$profession->set_prop("organization", $o->id());
						$profession->set_name($arr["request"]["contact_entry_co_contact_1_profession"]);
						$profession->save();
					}

					$o->add_employee($profession, $contact_person);
				}

				if (!empty($arr["request"]["contact_entry_co_contact_2"]["firstname"]) or !empty($arr["request"]["contact_entry_co_contact_2"]["lastname"]))
				{
					// add second contact person to entered company
					$contact_person = obj(null, array(), CL_CRM_PERSON);
					$contact_person->set_parent($o->id());
					$contact_person->set_prop("firstname", ucfirst($arr["request"]["contact_entry_co_contact_2"]["firstname"]));
					$contact_person->set_prop("lastname", ucfirst($arr["request"]["contact_entry_co_contact_2"]["lastname"]));
					$contact_person->save();
					$save_again = false;

					if (!empty($arr["request"]["contact_entry_co_contact_2"]["fake_phone"]))
					{
						$contact_person->set_prop("fake_phone", $arr["request"]["contact_entry_co_contact_2"]["fake_phone"]);
						$save_again = true;
					}

					if (!empty($arr["request"]["contact_entry_co_contact_2"]["fake_phone"]))
					{
						$contact_person->set_prop("fake_email", $arr["request"]["contact_entry_co_contact_2"]["fake_email"]);
						$save_again = true;
					}

					if ($save_again)
					{
						$contact_person->save();
					}

					if (empty($arr["request"]["contact_entry_co_contact_2_profession"]))
					{
						$profession = null;
					}
					else
					{ // create new profession in entered company
						$profession = obj(null, array(), CL_CRM_PROFESSION);
						$profession->set_parent($o->id());
						$profession->set_prop("organization", $o->id());
						$profession->set_name($arr["request"]["contact_entry_co_contact_2_profession"]);
						$profession->save();
					}

					$o->add_employee($profession, $contact_person);
				}

				if (!empty($arr["request"]["contact_entry_co_contact_3"]["firstname"]) or !empty($arr["request"]["contact_entry_co_contact_3"]["lastname"]))
				{
					// add third contact person to entered company
					$contact_person = obj(null, array(), CL_CRM_PERSON);
					$contact_person->set_parent($o->id());
					$contact_person->set_prop("firstname", ucfirst($arr["request"]["contact_entry_co_contact_3"]["firstname"]));
					$contact_person->set_prop("lastname", ucfirst($arr["request"]["contact_entry_co_contact_3"]["lastname"]));
					$contact_person->save();
					$save_again = false;

					if (!empty($arr["request"]["contact_entry_co_contact_3"]["fake_phone"]))
					{
						$contact_person->set_prop("fake_phone", $arr["request"]["contact_entry_co_contact_3"]["fake_phone"]);
						$save_again = true;
					}

					if (!empty($arr["request"]["contact_entry_co_contact_3"]["fake_phone"]))
					{
						$contact_person->set_prop("fake_email", $arr["request"]["contact_entry_co_contact_3"]["fake_email"]);
						$save_again = true;
					}

					if ($save_again)
					{
						$contact_person->save();
					}

					if (empty($arr["request"]["contact_entry_co_contact_3_profession"]))
					{
						$profession = null;
					}
					else
					{ // create new profession in entered company
						$profession = obj(null, array(), CL_CRM_PROFESSION);
						$profession->set_parent($o->id());
						$profession->set_prop("organization", $o->id());
						$profession->set_name($arr["request"]["contact_entry_co_contact_3_profession"]);
						$profession->save();
					}

					$o->add_employee($profession, $contact_person);
				}

				$this_o->add_contact($customer_relation);// also saves customer relation
				$this_o->create_call($customer_relation);
				aw_session_set("crm_sales_session_entry_count", aw_global_get("crm_sales_session_entry_count") + 1);
			}
			catch (Exception $e)
			{
				$o->delete();
				$customer_relation->delete();
				throw $e;
			}
			$return = PROP_IGNORE;
		}
		return $return;
	}

	function _set_contact_entry_person(&$arr)
	{
		$return = PROP_IGNORE;
		$this_o = $arr["obj_inst"];
		$phone_nr = isset($arr["prop"]["value"]["fake_phone"]) ? $arr["prop"]["value"]["fake_phone"] : null;
		$owner = $arr["obj_inst"]->prop("owner");

		// search from existing phone nr-s
		$phone_nr_list = new object_list(array(
			"class_id" => CL_CRM_PHONE,
			"name" => $phone_nr
		));

		if (empty($arr["prop"]["value"]["fake_phone"]))
		{ // phone must be defined
			$return = PROP_FATAL_ERROR;
			$arr["prop"]["error"] = t("Telefoninumber on kohustuslik");
		}
		elseif (!isset($arr["prop"]["value"]["id"]) and $phone_nr_list->count() === 1)
		{ // phone nr for a contact to be created is given but already exists
			$arr["prop"]["error"] = t("Telefoninumber on juba andmebaasis olemas. Uuesti lisada ei saa.");
			$return = PROP_FATAL_ERROR;
		}
		elseif ($phone_nr_list->count() > 1)
		{ // duplicate phones found
			$arr["prop"]["error"] = t("Antud telefoninumber esineb andmebaasis mitmekordselt. Vajalik on andmebaasi korrastus.");
			$return = PROP_FATAL_ERROR;
		}
		elseif (isset($arr["prop"]["value"]["id"]))
		{
			$o = obj($arr["prop"]["value"]["id"], array(), CL_CRM_PERSON, true);
			$o->set_prop("firstname", ucfirst($arr["prop"]["value"]["firstname"]));
			$o->set_prop("lastname", ucfirst($arr["prop"]["value"]["lastname"]));
			$o->set_prop("gender", $arr["prop"]["value"]["gender"]);
			$o->set_prop("fake_phone", $arr["prop"]["value"]["fake_phone"]);
			$o->set_prop("fake_email", $arr["prop"]["value"]["fake_email"]);
			$o->save();
			$customer_relation = $o->get_customer_relation($owner);

			// comment
			if (!empty($arr["request"]["contact_entry_add_comment"]))
			{
				$parent = $customer_relation->id();
				$comment_text = t("Andmete sisestamine:\n") . $arr["request"]["contact_entry_add_comment"];
				$existing_comments = new object_list(array(
					"class_id" => CL_COMMENT,
					"commtext" => $comment_text,
					"parent" => $parent
				));

				if ($existing_comments->count() < 1)
				{
					$comment = new forum_comment();
					$comment->submit(array(
						"parent" => $parent,
						"commtext" => $comment_text,
						"return" => "id"
					));
				}
			}

			// category
			if (!empty($arr["request"]["contact_entry_category"]))
			{
				$selected_categories = $arr["request"]["contact_entry_category"];
				// remove unselected
				foreach ($customer_relation->connections_from(array("type" => "RELTYPE_CATEGORY")) as $c)
				{
					$category = $c->to();
					if (!in_array($category->id(), $selected_categories))
					{
						$customer_relation->disconnect(array("from" => $category->id()));
					}
				}

				// add categories
				foreach ($selected_categories as $category_oid)
				{
					$category = obj($category_oid, array(), CL_CRM_CATEGORY);
					if (!$customer_relation->is_connected_to(array("to" => $category, "type" => "RELTYPE_CATEGORY")))
					{
						$customer_relation->connect(array(
							"to" => $category,
							"type" => "RELTYPE_CATEGORY"
						));
					}
				}
			}

			// address
			if (is_oid($arr["request"]["contact_entry_address"]["location_data"]))
			{
				// edit old address or create new if not found
				$address = $o->get_first_obj_by_reltype("RELTYPE_ADDRESS_ALT");
				$new_address = false;
				if (!$address)
				{
					$address = obj(null, array(), CL_ADDRESS);
					$address->set_parent($o->id());
					$new_address = true;
				}

				$address->set_prop("country", $arr["request"]["contact_entry_address"]["country"]);
				$address->set_location($arr["request"]["contact_entry_address"]["location_data"]);
				$address->set_prop("street", $arr["request"]["contact_entry_address"]["street"]);
				$address->set_prop("house", $arr["request"]["contact_entry_address"]["house"]);
				$address->set_prop("apartment", $arr["request"]["contact_entry_address"]["apartment"]);
				$address->set_prop("postal_code", $arr["request"]["contact_entry_address"]["postal_code"]);
				$address->set_prop("po_box", $arr["request"]["contact_entry_address"]["po_box"]);
				$address->save();

				if ($new_address)
				{
					$o->connect(array("to" => $address, "reltype" => "RELTYPE_ADDRESS_ALT"));
				}
			}
			$return = PROP_IGNORE;
		}
		else
		{
			try
			{
				$owner_oid = $this_o->prop("owner")->id();

				// see if phone number already exists, if yes, abort
				$list = new object_list(array(
					"class_id" => CL_CRM_PHONE,
					"name" => $arr["prop"]["value"]["fake_phone"]
				));
				if ($list->count() > 0)
				{
					$arr["prop"]["error"] = t("Telefoninumber on juba andmebaasis olemas. Uuesti lisada ei saa.");
					return PROP_FATAL_ERROR;
				}

				// create new contact object
				$o = obj(null, array(), CL_CRM_PERSON);
				$o->set_parent($owner_oid);
				$o->set_prop("firstname", ucfirst($arr["prop"]["value"]["firstname"]));
				$o->set_prop("lastname", ucfirst($arr["prop"]["value"]["lastname"]));
				$o->set_prop("gender", $arr["prop"]["value"]["gender"]);
				$o->save();
				$o->set_prop("fake_phone", $arr["prop"]["value"]["fake_phone"]);
				$o->set_prop("fake_email", $arr["prop"]["value"]["fake_email"]);
				$o->save();

				// address
				if (is_oid($arr["request"]["contact_entry_address"]["location_data"]))
				{
					$address = obj(null, array(), CL_ADDRESS);
					$address->set_parent($o->id());
					$address->set_prop("country", $arr["request"]["contact_entry_address"]["country"]);
					$address->set_location($arr["request"]["contact_entry_address"]["location_data"]);
					$address->set_prop("street", $arr["request"]["contact_entry_address"]["street"]);
					$address->set_prop("house", $arr["request"]["contact_entry_address"]["house"]);
					$address->set_prop("apartment", $arr["request"]["contact_entry_address"]["apartment"]);
					$address->set_prop("postal_code", $arr["request"]["contact_entry_address"]["postal_code"]);
					$address->set_prop("po_box", $arr["request"]["contact_entry_address"]["po_box"]);
					$address->save();
					$o->connect(array("to" => $address, "reltype" => "RELTYPE_ADDRESS_ALT"));
				}

				$customer_relation = $o->get_customer_relation($owner, true);

				// category
				if (!empty($arr["request"]["contact_entry_category"]))
				{
					foreach ($arr["request"]["contact_entry_category"] as $category_oid)
					{
						$category = obj($category_oid, array(), CL_CRM_CATEGORY);
						$customer_relation->connect(array(
							"to" => $category,
							"type" => "RELTYPE_CATEGORY"
						));
					}
				}

				if (!empty($arr["request"]["contact_entry_salesman"]))
				{ // set salesman
					$salesman = obj($arr["request"]["contact_entry_salesman"], array(), CL_CRM_PERSON);
					$customer_relation->set_prop("salesman", $salesman->id());
					$customer_relation->connect(array("to" => $salesman, "reltype" => "RELTYPE_SALESMAN"));
				}

				if (!empty($arr["request"]["contact_entry_lead_source_oid"]))
				{ // set lead source
					$lead_source = new object($arr["request"]["contact_entry_lead_source_oid"]);

					if (!$lead_source->is_a(CL_CRM_COMPANY) and !$lead_source->is_a(CL_CRM_PERSON))
					{
						throw new awex_obj_class("Invalid class. Lead source must be a company or a person");
					}

					$customer_relation->set_prop("sales_lead_source", $lead_source->id());
					$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_LEAD);
					$customer_relation->connect(array("to" => $lead_source, "reltype" => "RELTYPE_SALES_LEAD_SOURCE"));
				}
				elseif (!empty($arr["request"]["contact_entry_lead_source"]))
				{
					$name = explode(" ", $arr["request"]["contact_entry_lead_source"]);
					foreach ($name as $key => $name_part)
					{
						$name[$key] = ucfirst($name_part);
					}

					$lastname = array_pop($name);
					$firstname = count($name) > 1 ? implode("-", $name) : array_pop($name);
					$lead_source = obj(null, array(), CL_CRM_PERSON);
					$lead_source->set_parent($owner_oid);
					$lead_source->set_prop("firstname", $firstname);
					$lead_source->set_prop("lastname", $lastname);
					$lead_source->save();
					$customer_relation->set_prop("sales_lead_source", $lead_source->id());
					$customer_relation->set_prop("sales_state", crm_company_customer_data_obj::SALESSTATE_LEAD);
					$customer_relation->connect(array("to" => $lead_source, "reltype" => "RELTYPE_SALES_LEAD_SOURCE"));
				}

				if (!empty($arr["request"]["contact_entry_add_comment"]))
				{ // add comment
					$parent = $customer_relation->id();
					$comment_text = t("Andmete sisestamine:\n") . $arr["request"]["contact_entry_add_comment"];
					$comment = new forum_comment();
					$comment->submit(array(
						"parent" => $parent,
						"commtext" => $comment_text,
						"return" => "id"
					));
				}

				$this_o->add_contact($customer_relation); // also saves customer relation
				$this_o->create_call($customer_relation);
				aw_session_set("crm_sales_session_entry_count", aw_global_get("crm_sales_session_entry_count") + 1);
			}
			catch (Exception $e)
			{
				$o->delete();
				$customer_relation->delete();
				throw $e;
			}
			$return = PROP_IGNORE;
		}
		return $return;
	}

	function _set_contact_entry_empoyee(&$arr)
	{
		$return = PROP_IGNORE;
		$this_o = $arr["obj_inst"];
		$phone_nr = isset($arr["prop"]["value"]["fake_phone"]) ? $arr["prop"]["value"]["fake_phone"] : null;
		$owner = $arr["obj_inst"]->prop("owner");

		// search from existing phone nr-s
		$phone_nr_list = new object_list(array(
			"class_id" => CL_CRM_PHONE,
			"name" => $phone_nr
		));

		if (!isset($arr["prop"]["value"]["id"]) and $phone_nr_list->count() === 1)
		{ // phone nr for a contact to be created is given but already exists
			$arr["prop"]["error"] = t("Telefoninumber on juba andmebaasis olemas. Uuesti lisada ei saa.");
			$return = PROP_FATAL_ERROR;
		}
		elseif ($phone_nr_list->count() > 1)
		{ // duplicate phones found
			$arr["prop"]["error"] = t("Antud telefoninumber esineb andmebaasis mitmekordselt. Vajalik on andmebaasi korrastus.");
			$return = PROP_FATAL_ERROR;
		}
		elseif (isset($arr["prop"]["value"]["id"]))
		{
			$o = obj($arr["prop"]["value"]["id"], array(), CL_CRM_PERSON, true);
			$o->set_prop("firstname", ucfirst($arr["prop"]["value"]["firstname"]));
			$o->set_prop("lastname", ucfirst($arr["prop"]["value"]["lastname"]));
			$o->set_prop("gender", $arr["prop"]["value"]["gender"]);
			$o->set_prop("fake_phone", $arr["prop"]["value"]["fake_phone"]);
			$o->set_prop("fake_email", $arr["prop"]["value"]["fake_email"]);
			$o->set_comment($arr["request"]["contact_entry_comment"]);
			$o->save();

			// organization
			if (!empty($arr["request"]["contact_entry_organization"]))
			{
				$organization = obj($arr["request"]["contact_entry_organization"], array(), CL_CRM_COMPANY);
				$organization->add_employee(null, $o);
			}
		}
		else
		{
			try
			{
				$owner_oid = $this_o->prop("owner")->id();

				// see if phone number already exists, if yes, abort
				$list = new object_list(array(
					"class_id" => CL_CRM_PHONE,
					"name" => $arr["prop"]["value"]["fake_phone"]
				));
				if ($list->count() > 0)
				{
					$arr["prop"]["error"] = t("Telefoninumber on juba andmebaasis olemas. Uuesti lisada ei saa.");
					return PROP_FATAL_ERROR;
				}

				// create new contact object
				$o = obj(null, array(), CL_CRM_PERSON);
				$o->set_parent($owner_oid);
				$o->set_prop("firstname", ucfirst($arr["prop"]["value"]["firstname"]));
				$o->set_prop("lastname", ucfirst($arr["prop"]["value"]["lastname"]));
				$o->set_prop("gender", $arr["prop"]["value"]["gender"]);
				$o->set_comment($arr["request"]["contact_entry_comment"]);
				$o->save();
				$o->set_prop("fake_phone", $arr["prop"]["value"]["fake_phone"]);
				$o->set_prop("fake_email", $arr["prop"]["value"]["fake_email"]);
				$o->save();

				// organization
				if (!empty($arr["request"]["contact_entry_organization"]))
				{
					$organization = obj($arr["request"]["contact_entry_organization"], array(), CL_CRM_COMPANY);
					$organization->add_employee(null, $o);
				}

				aw_session_set("crm_sales_session_entry_count", aw_global_get("crm_sales_session_entry_count") + 1);
			}
			catch (Exception $e)
			{
				$o->delete();
				throw $e;
			}
		}
		return $return;
	}

	function _get_last_entries_list(&$arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$table->define_field(array(
			"name" => "nr",
			"caption" => t("Nr.")
		));
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Kliendi nimi")
		));
		$table->define_field(array(
			"name" => "entry_time",
			"caption" => t("Sisestatud")
		));
		$owner = $arr["obj_inst"]->prop("owner");
		$clid = $this->use_group === "data_entry_contact_co" ? CL_CRM_COMPANY : CL_CRM_PERSON;
		$list = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"seller" => $owner->id(),
			"buyer.class_id" => $clid,
			"createdby" => aw_global_get("uid"),
			"created" => new obj_predicate_compare(OBJ_COMP_GREATER, mktime(0, 0, 0)),
			new obj_predicate_sort(array("created" => "desc")),
			new obj_predicate_limit(5)
		));
		$list->sort_by(array(
			"prop" => "created",
			"order" => "desc"
		));

		if ($list->count())
		{
			$nr = 1;
			$url = automatweb::$request->get_uri();
			$o = $list->begin();
			do
			{
				$url->set_arg("contact_list_load_contact", $o->prop("buyer"));
				$table->define_data(array(
					"nr" => $nr++,
					"name" => html::href(array("caption" => $o->prop("buyer.name"), "url" => $url->get())),
					"entry_time" => aw_locale::get_lc_date($o->created(), aw_locale::DATETIME_SHORT_FULLYEAR)
				));
			}
			while ($o = $list->next());
		}
		$table->set_caption(sprintf(t("Viimased sisestused (kokku sisestatud k&auml;esolevas sessioonis: %s)"), (string)(int)aw_global_get("crm_sales_session_entry_count")));
		return PROP_OK;
	}

	function _get_role_profession_data_entry_clerk(&$arr)
	{
		$arr["prop"]["options"] = $arr["obj_inst"]->prop("owner")->get_company_professions()->names();
		return PROP_OK;
	}

	function _get_role_profession_salesman(&$arr)
	{
		$arr["prop"]["options"] = $arr["obj_inst"]->prop("owner")->get_company_professions()->names();
		return PROP_OK;
	}

	function _get_role_profession_sales_manager(&$arr)
	{
		$arr["prop"]["options"] = $arr["obj_inst"]->prop("owner")->get_company_professions()->names();
		return PROP_OK;
	}

	function _get_role_profession_telemarketing_salesman(&$arr)
	{
		$arr["prop"]["options"] = $arr["obj_inst"]->prop("owner")->get_company_professions()->names();
		return PROP_OK;
	}

	function _get_role_profession_telemarketing_manager(&$arr)
	{
		$arr["prop"]["options"] = $arr["obj_inst"]->prop("owner")->get_company_professions()->names();
		return PROP_OK;
	}

	function _get_owner($arr)
	{
		$arr["prop"]["value"] = $this->view ? $arr["prop"]["value"] : $arr["prop"]["value"]->id();
		return PROP_OK;
	}

	function _set_owner($arr)
	{
		$arr["prop"]["value"] = new object($arr["prop"]["value"]);
		return PROP_OK;
	}

	function delete_objects($arr)
	{ //!!! v6ibolla ei peaks kustutama. parem eemaldaks myygikeskkonnast?
		// delete additional objects of a contact
		if (!is_array($arr["sel"]) && is_array($arr["check"]))
		{
			$arr["sel"] = $arr["check"];
		}

		foreach (safe_array($arr["sel"]) as $del_obj)
		{
			$obj = obj($del_obj);
			if (CL_CRM_COMPANY_CUSTOMER_DATA == $obj->class_id())
			{
				$list = new object_list(array(
					"class_id" => array(CL_CRM_CALL, CL_CRM_PRESENTATION, CL_TASK),
					"customer_relation" => $obj->id()
				));
				$list->delete();
			}
		}

		//
		$r = parent::delete_objects($arr);
		return  $r;
	}

	/**
		@attrib name=create_presentation
		@param id required type=oid
		@param cust_rel required type=oid
		@param return_url optional type=string
	**/
	public function create_presentation($arr)
	{
		$this_o = obj($arr["id"], array(), CL_CRM_SALES);
		$cust_rel = obj($arr["cust_rel"], array(), CL_CRM_COMPANY_CUSTOMER_DATA);
		$presentation = $this_o->create_presentation($cust_rel);
		$params = !empty($arr["return_url"]) ? array("return_url" => $arr["return_url"]) : array();
		return html::get_change_url($presentation->id(), $params);
	}

	/**
		@attrib name=create_call
		@param id required type=oid
		@param cust_rel required type=oid
		@param return_url optional type=string
	**/
	public function create_call($arr)
	{
		$this_o = obj($arr["id"], array(), CL_CRM_SALES);
		$cust_rel = obj($arr["cust_rel"], array(), CL_CRM_COMPANY_CUSTOMER_DATA);
		$call = $this_o->create_call($cust_rel);
		$params = !empty($arr["return_url"]) ? array("return_url" => $arr["return_url"]) : array();
		return html::get_change_url($call->id(), $params);
	}

	// takes space separated user input "AND" search string, returns words separated by "%"
	static function parse_search_string($string)
	{
		$words = explode(" ", $string);
		$words = array_unique($words);
		$parsed = array();
		foreach ($words as $word)
		{
			$word = trim($word);
			if (strlen($word))
			{
				$parsed[] = addslashes($word);
			}
		}
		return "%" . implode("%", $parsed) . "%";
	}

	public static function parse_customer_name(object $customer)
	{
		return strlen($customer->name()) > 1 ? ($customer->is_a(CL_CRM_COMPANY) ? $customer->get_title() : $customer->name()) : t("[Nimetu]");
	}
}

?>
