<?php

// personnel_management.aw - Personalikeskkond
/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_CRM_PERSON, on_add_person)

@classinfo syslog_type=ST_PERSONNEL_MANAGEMENT relationmgr=yes r2=yes no_name=1 no_status=1 no_comment=1 prop_cb=1
@default table=objects

@groupinfo general caption="Seaded"

	@groupinfo general2 caption="&Uuml;ldine" parent=general
	@default group=general2

		@property pm_name type=textbox field=name
		@caption Nimi

@default field=meta
@default method=serialize

		@property languages_fld type=relpicker reltype=RELTYPE_MENU
		@caption Keelte kaust

		@property legal_forms_fld type=relpicker reltype=RELTYPE_MENU
		@caption &Otilde;iguslike vormide kaust

		@property persons_fld type=relpicker reltype=RELTYPE_MENU
		@caption Isikute kaust

		@property employers_fld type=relpicker reltype=RELTYPE_MENU
		@caption T&ouml;&ouml;pakkujate kaust

		@property sectors_fld type=relpicker reltype=RELTYPE_MENU
		@caption Tegevusalade kaust

		@property offers_fld type=relpicker reltype=RELTYPE_MENU
		@caption T&ouml;&ouml;pakkumiste kaust

		@property professions_fld type=relpicker reltype=RELTYPE_MENU
		@caption Ametinimetuste kaust

		@property schools_fld type=relpicker reltype=RELTYPE_MENU
		@caption Koolide kaust

		@property locations_fld type=relpicker reltype=RELTYPE_MENU
		@caption Riikide kaust

		@property fields type=relpicker reltype=RELTYPE_SECTORS
		@caption Tegevusvaldkonnad

		@property person_ot type=relpicker reltype=RELTYPE_OBJECT_TYPE
		@caption Isiku objektit&uuml;&uuml;p

		@property job_offer_ot type=relpicker reltype=RELTYPE_OBJECT_TYPE
		@caption T&ouml;&ouml;pakkumise objektit&uuml;&uuml;p

		@property jobwish_ot type=relpicker reltype=RELTYPE_OBJECT_TYPE
		@caption T&ouml;&ouml;soovi objektit&uuml;&uuml;p

		@property personnel_management_ot type=relpicker reltype=RELTYPE_OBJECT_TYPE
		@caption Personalikeskkonna objektit&uuml;&uuml;p

		@property crmdb type=relpicker reltype=RELTYPE_CRM_DB
		@caption Kliendibaas

		@property owner_org type=relpicker reltype=RELTYPE_OWNER_ORG
		@caption Omanikorganisatsioon

		@property sysdefault_pm type=checkbox ch_value=1 store=no
		@caption Default personalikeskkond

		@property mandatory_controller type=relpicker reltype=RELTYPE_CFGCONTROLLER
		@caption Kohustuslikkuse kontroller

		@property mobi_handler type=relpicker reltype=RELTYPE_MOBI_HANDLER
		@caption Mobi SMSi haldur

		@property messenger type=relpicker reltype=RELTYPE_MESSENGER
		@caption Meilikast

		@property auto_archive type=checkbox ch_value=1
		@caption Automaatne arhiveerimine

		@property auto_archive_days type=textbox size=4
		@caption Mitu p&auml;eva p&auml;rast kandideerimist&auml;htaega t&ouml;&ouml;pakkumine arhiveeritakse

	@groupinfo notify caption="Teavitamine" parent=general
	@default group=notify

		@property notify_mail type=textbox
		@caption Vaikimisi e-post
		@comment Vaikimisi e-posti aadress, kuhu saata sisestatud CV

		@property notify_subject type=textbox
		@caption Pealkiri
		@comment CV sisestamisest teavitava kirja pealkiri

		@property notify_froma type=textbox
		@caption Saatja e-post
		@comment CV sisestamisest teavitava kirja saatja e-posti aadress

		@property notify_fromn type=textbox
		@caption Saatja nimi
		@comment CV sisestamisest teavitava kirja saatja nimi

		@property notify_candidates type=checkbox ch_value=1
		@caption Teavita ka kandideerivatest uutest CVdest

		@property notify_lang type=relpicker reltype=RELTYPE_LANGUAGE automatic=1
		@caption Teavitamise keel

		@property notify_locations type=relpicker reltype=RELTYPE_NOTIFY_LOCATION multiple=1 store=connect
		@caption Asukohad
		@comment Haldus&uuml;ksused, millele on m&auml;&auml;ratud eraldi e-posti aadress, kuhu teavituskiri saata.

		@property notify_loc_tbl type=table store=no
		@caption

	@groupinfo search_conf caption="T&ouml;&ouml;otsijad/kandideerijad" parent=general
	@default group=search_conf

		@property perpage type=textbox size=4 group=employer_conf,search_conf
		@caption Mitu tulemust lehel kuvada

		@property days_to_inactivity type=textbox size=4
		@caption Mitu p&auml;eva CV kehtib

		@property schools_for_faculties type=relpicker multiple=1 reltype=RELTYPE_SCHOOLS_FOR_FACULTIES store=connect
		@caption Milliste koolide teaduskondi kuvada kandideerijate/t&ouml;&ouml;otsijate puus

		@property search_conf_tbl type=table
		@caption Tulemuste tabeli v&auml;ljad

	@groupinfo skill_conf caption="Oskused" parent=general
	@default group=skill_conf

		@property skill_manager type=relpicker reltype=RELTYPE_SKILL_MANAGER
		@caption Oskuste haldur

		@property skills_fld type=relpicker reltype=RELTYPE_MENU
		@caption Oskuste kaust

		@property drivers_license type=select multiple=1
		@caption Juhilubade kategooriad

		@property yob_from type=textbox size=4 default=1930
		@caption S&uuml;nniaasta alates

		@property yob_to type=textbox size=4
		@caption S&uuml;nniaasta kuni

	@groupinfo job_offer_conf caption="T&ouml;&ouml;pakkumised" parent=general
	@default group=job_offer_conf

		@property cv_tpl type=select
		@caption CV templeit

		@property pdf_tpl type=select
		@caption T&ouml;&ouml;pakkumise PDFi templeit

		@property job_offer_cv_tbl type=select multiple=1 field=meta method=serialize
		@caption Uus seadetevorm

		@property apply_doc type=relpicker reltype=RELTYPE_DOC
		@caption Dokument veebist kandideerimiseks

		@property fb_from_fld type=relpicker reltype=RELTYPE_MENU
		@caption T&ouml;&ouml;pakkumise tagasiside saatjate kaust

		@property rate_candidates type=checkbox ch_value=1 field=meta method=serialize
		@caption Hinda kandideerijaid

		@property notify_me_tpl type=relpicker reltype=RELTYPE_NOTIFICATION_TPL store=connect
		@caption Kandidatuurist teavitamise kiri

	@groupinfo employer_conf caption="T&ouml;&ouml;pakkujad" parent=general
	@default group=employer_conf

		@property remove_job_offers_with_employer type=checkbox ch_value=1 field=meta method=serialize
		@caption Kustuta t&ouml;&ouml;pakkuja koos t&ouml;&ouml;pakkumistega

		@property show_all_employers type=checkbox ch_value=1 field=meta method=serialize
		@caption T&ouml;&ouml;pakkujad vaate avamisel kuvatakse k&otilde;ik t&ouml;&ouml;pakkujad

	@groupinfo job_wanted_conf caption="T&ouml;&ouml;soovid" parent=general
	@default group=job_wanted_conf

		@property location_conf type=select multiple=1
		@caption Asukoht (esimene valik)

		@property location_2_conf type=select multiple=1
		@caption Asukoht (teine valik)

	@groupinfo cfgforms caption="Seadete vormid" parent=general
	@default group=cfgforms

		@property default_offers_cfgform type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption Isiku seadetevorm

		@property cff_job_wanted type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption T&ouml;&ouml;soovi seadetevorm

		@property cff_recommendation type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption Soovituse seadetevorm

		@property cff_company_relation type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption Organisatoorse kuuluvuse seadetevorm

		@property cff_education type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption Haridusk&auml;igu seadetevorm

		@property cff_add_education type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption T&auml;iendkoolituse seadetevorm

		@property cff_work_relation type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption T&ouml;&ouml;suhte seadetevorm

		@property cff_person_language type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption Keeleoskuse seadetevorm

		@property cff_job_offer type=relpicker reltype=RELTYPE_CFGFORM field=meta method=serialize
		@caption T&ouml;&ouml;pakkumise seadetevorm

	@groupinfo rights caption="Objektide n&auml;gemiseks vajalikud &otilde;igused" parent=general
	@default group=rights

		@property needed_acl_employee type=select multiple=1 field=meta method=serialize
		@caption T&ouml;&ouml;otsijad

		@property needed_acl_candidate type=select multiple=1 field=meta method=serialize
		@caption Kandideerijad

		@property needed_acl_job_offer type=select multiple=1 field=meta method=serialize
		@caption T&ouml;&ouml;pakkumised

	@groupinfo show_cnt caption="Vaatamiste loendamine" parent=general
	@default group=show_cnt

		@property show_cnt_person type=relpicker reltype=RELTYPE_SHOW_CNT multiple=1 field=meta method=serialize
		@caption Grupid, kelle CV-vaatamisi loendatakse

		@property show_cnt_job_offer type=relpicker reltype=RELTYPE_SHOW_CNT multiple=1 field=meta method=serialize
		@caption Grupid, kelle t&ouml;&ouml;pakkumise vaatamisi loendatakse

	@groupinfo variables caption="Muutujate haldus" parent=general
	@default group=variables

		@property vars_tlb type=toolbar store=no no_caption=1

		@layout vars type=hbox width=20%:80%

			@layout vars_left type=vbox parent=vars

				@layout vars_tree type=vbox closeable=1 parent=vars_left area_caption=Muutujate&nbsp;puu

					@property vars_tree type=treeview no_caption=1 parent=vars_tree

				@layout vars_search type=vbox closeable=1 parent=vars_left area_caption=Muutujate&nbsp;otsing

					@property vs_name type=textbox store=no parent=vars_search
					@caption Nimi

			@layout vars_right type=vbox parent=vars

				@property vars_tbl type=table store=no parent=vars_right no_caption=1

-------------------T88OTSIJAD-----------------------
@groupinfo employee caption="T&ouml;&ouml;otsijad" submit=no
@default group=employee,candidate

@property employee_tb type=toolbar no_caption=1

@property add_employee type=hidden store=no

@layout employee type=hbox width=20%:80%

	@layout employee_left type=vbox parent=employee

		@layout employee_tree type=vbox closeable=1 parent=employee_left area_caption=T&ouml;&ouml;otsijad

			@property employee_tree type=treeview no_caption=1 parent=employee_tree

		@layout employee_search type=vbox closeable=1 parent=employee_left area_caption=Otsing

			@layout employee_search_1 type=vbox parent=employee_search

				@property search_save type=relpicker reltype=RELTYPE_SEARCH_SAVE parent=employee_search_1 captionside=top search_button=1 no_edit=1 delete_button=1
				@caption Varasem otsing

			@layout isikuandmed type=vbox_sub no_padding=1 closeable=1 parent=employee_search area_caption=Isikuandmed

				@property cv_oid type=textbox size=4 parent=isikuandmed captionside=top store=no
				@caption ID

				@property cv_name type=textbox size=18 parent=isikuandmed captionside=top store=no
				@caption Nimi

				@property cv_bd_from type=date_select default=-1 year_from=1930 parent=isikuandmed captionside=top store=no
				@caption S&uuml;nnip&auml;ev alates

				@property cv_bd_to type=date_select default=-1 year_from=1930 parent=isikuandmed captionside=top store=no
				@caption S&uuml;nnip&auml;ev kuni

				@layout cv_age type=hbox parent=isikuandmed

					@property cv_age_from type=textbox parent=cv_age captionside=top size=4 store=no
					@caption Vanus alates

					@property cv_age_to type=textbox parent=cv_age captionside=top size=4 store=no
					@caption Vanus kuni

				@property cv_tel type=textbox size=18 parent=isikuandmed captionside=top store=no
				@caption Telefon

				@property cv_email type=textbox size=18 parent=isikuandmed captionside=top store=no
				@caption E-post

				@property cv_city type=relpicker reltype=RELTYPE_SEARCH_CITY multiple=1 no_edit=1 parent=isikuandmed captionside=top store=no size=4
				@caption Linn

				@property cv_county type=relpicker reltype=RELTYPE_SEARCH_COUNTY multiple=1 no_edit=1 parent=isikuandmed captionside=top store=no size=4
				@caption Maakond

				@property cv_area type=relpicker reltype=RELTYPE_SEARCH_AREA multiple=1 no_edit=1 parent=isikuandmed captionside=top store=no size=4
				@caption Piirkond

				@property cv_address type=textbox size=18 parent=isikuandmed captionside=top store=no
				@caption Aadress vabatekstina

				@property cv_addinfo type=textbox size=18 parent=isikuandmed captionside=top store=no
				@caption Lisainfo

				@property cv_gender type=chooser size=18 parent=isikuandmed captionside=top store=no
				@caption Sugu

				@property cv_mod_from type=date_select default=-1 parent=isikuandmed captionside=top store=no
				@caption CV muudetud alates

				@property cv_mod_to type=date_select default=-1 parent=isikuandmed captionside=top store=no
				@caption CV muudetud kuni

				@property cv_status type=chooser parent=isikuandmed captionside=top store=no
				@caption Aktiivsus

				@property cv_approved type=chooser parent=isikuandmed captionside=top store=no
				@caption Kinnitatus

				@property cv_udef_chbox1 type=chooser parent=isikuandmed captionside=top store=no
				@caption Kasutajadefineeritud CHBOX1

				@property cv_udef_chbox2 type=chooser parent=isikuandmed captionside=top store=no
				@caption Kasutajadefineeritud CHBOX2

				@property cv_udef_chbox3 type=chooser parent=isikuandmed captionside=top store=no
				@caption Kasutajadefineeritud CHBOX2

			@layout haridus type=vbox_sub no_padding=1 parent=employee_search area_caption=Haridus closeable=1

				@property cv_edulvl type=select parent=haridus captionside=top store=no
				@caption Haridustase

				@property cv_edu_exact type=checkbox ch_value=1 parent=haridus captionside=top store=no no_caption=1
				@caption T&auml;pne vaste

				@property cv_edulvl_in_eduobj type=checkbox ch_value=1 parent=haridus captionside=top store=no no_caption=1
				@caption Omandatud/omandamisel haridustasemed

				@property cv_acdeg type=select parent=haridus captionside=top store=no
				@caption Akadeemiline kraad

				@property cv_schl type=textbox size=18 parent=haridus captionside=top store=no
				@caption Kool

				@property cv_faculty type=textbox size=18 parent=haridus captionside=top store=no
				@caption Teaduskond

				@property cv_speciality type=textbox size=18 parent=haridus captionside=top store=no
				@caption Eriala

				@property cv_schl_area type=textbox size=18 parent=haridus captionside=top store=no
				@caption Valdkond

				@layout cv_schl_start type=hbox parent=isikuandmed

					@property cv_schl_start_from type=textbox parent=cv_schl_start captionside=top size=4 store=no
					@caption Sisseastumisaasta alates

					@property cv_schl_start_to type=textbox parent=cv_schl_start captionside=top size=4 store=no
					@caption Sisseastumisaasta kuni

				@property cv_schl_stat type=chooser size=18 parent=haridus captionside=top store=no
				@caption Staatus

			@layout soovitud_t88 type=vbox_sub no_padding=1 parent=employee_search closeable=1 area_caption=Soovitud&nbsp;t&ouml;&ouml;

				@property cv_job type=textbox size=18 parent=soovitud_t88 captionside=top store=no
				@caption Ametinimetus

				@layout cv_paywish_lt type=hbox parent=soovitud_t88

					@property cv_paywish type=textbox parent=cv_paywish_lt captionside=top size=6 store=no
					@caption Palk alates

					@property cv_paywish2 type=textbox parent=cv_paywish_lt captionside=top size=6 store=no
					@caption Palk kuni

				@property cv_field type=textbox size=18 parent=soovitud_t88 captionside=top store=no
				@caption Tegevusala

				@property cv_type type=textbox size=18 parent=soovitud_t88 captionside=top store=no
				@caption T&ouml;&ouml; liik

				@property cv_location type=textbox size=18 parent=soovitud_t88 captionside=top store=no
				@caption T&ouml;&ouml;tamise piirkond

				@property cv_load type=classificator multiple=1 orient=vertical reltype=RELTYPE_JOB_WANTED_LOAD parent=soovitud_t88 captionside=top store=no sort_callback=CL_PERSONNEL_MANAGEMENT::cmp_function
				@caption T&ouml;&ouml;koormus

			@layout oskused type=vbox_sub no_padding=1 parent=employee_search area_caption=Oskused closeable=1

				@property cv_personality type=textbox size=18 parent=oskused captionside=top store=no
				@caption Isikuomadused

				@property cv_mother_tongue type=select parent=oskused captionside=top store=no
				@caption Emakeel

				@property cv_lang_exp type=select multiple=1 parent=oskused captionside=top store=no
				@caption Keeleoskus

				@property cv_lang_exp_lvl type=select parent=oskused captionside=top store=no
				@caption Keeleoskuse tase

				@property cv_exps_n_lvls type=text parent=oskused no_caption=1 store=no

				@property cv_driving_licence type=text parent=oskused captionside=top store=no
				@caption Juhiload

				@property cv_other_skills type=textbox size=18 parent=oskused captionside=top store=no
				@caption Muud oskused

			@layout t88kogemus type=vbox_sub no_padding=1 parent=employee_search area_caption=T&ouml;&ouml;kogemus closeable=1

				@property cv_previous_rank type=textbox size=18 parent=t88kogemus captionside=top store=no
				@caption T&ouml;&ouml;kogemuse ametinimetus

				@property cv_previous_field type=textbox size=18 parent=t88kogemus captionside=top store=no
				@caption Valdkond

				@property cv_company type=textbox size=18 parent=t88kogemus captionside=top store=no
				@caption Ettev&otilde;te

				@property cv_recommenders type=textbox size=18 parent=t88kogemus captionside=top store=no
				@caption Soovitajad

				@property cv_comments type=textbox size=18 parent=t88kogemus captionside=top store=no
				@caption Kommentaarid

				@property cv_wrk_load type=select multiple=1 parent=t88kogemus captionside=top store=no
				@caption T&ouml;&ouml;koormus

				@property cv_praxis type=chechbox ch_value=1 parent=t88kogemus captionside=top store=no
				@caption Otsi ka praktikakogemusi

			@layout cv_search_buttons type=hbox parent=employee_search

				@property cv_search_button type=submit parent=cv_search_buttons store=no
				@caption Otsi

				@property cv_search_button_save_search type=submit parent=cv_search_buttons store=no
				@caption Otsi ja salvesta

				# @property cv_search_button_save type=text parent=cv_search_buttons store=no
				# @property cv_search_button_save type=submit parent=cv_search_buttons store=no action=cv_search_and_save
				# @caption Otsi ja salvesta

	@layout employee_right type=vbox parent=employee

			@property employee_tbl type=table no_caption=1 parent=employee_right store=no

----------------------------------------

# @groupinfo employee_list caption="Nimekiri" parent=employee submit=no
# @default group=employee_list

# @property employee_list_toolbar type=toolbar no_caption=1

# @layout employee_list type=hbox width=15%:85%

# @property employee_list_tree type=treeview no_caption=1 parent=employee_list

# @property employee_list_table type=table no_caption=1 parent=employee_list

----------------------------------------

@groupinfo candidate caption="Kandideerijad" submit=no

# All the props are defined in the employee group.

----------------------------------------
@groupinfo offers caption="T&ouml;&ouml;pakkumised" submit=no

	@groupinfo offers_ parent=offers caption="&Uuml;ldine" submit=no
	@groupinfo offers_archive parent=offers caption="Arhiiv" submit=no
	@default group=offers_,offers_archive

		@property offers_toolbar type=toolbar no_caption=1

		@layout offers type=hbox width=15%:85%

			@layout offers_tree_n_search type=vbox parent=offers

				@layout offers_tree type=vbox parent=offers_tree_n_search closeable=1 area_caption=T&ouml;&ouml;pakkumiste&nbsp;puu

					@property offers_tree type=treeview no_caption=1 parent=offers_tree

				@layout offers_search type=vbox parent=offers_tree_n_search closeable=1 area_caption=T&ouml;&ouml;pakkumiste&nbsp;otsing

					@layout os_top type=vbox parent=offers_search

						@property os_type type=classificator parent=os_top captionside=top store=no
						@caption T&uuml;&uuml;p

						@property os_pr type=textbox parent=os_top captionside=top store=no size=18
						@caption Ametikoht

						@property os_county type=relpicker reltype=RELTYPE_SEARCH_COUNTY mode=autocomplete parent=os_top captionside=top store=no size=18
						@caption Maakond

						@property os_city type=relpicker reltype=RELTYPE_SEARCH_CITY mode=autocomplete parent=os_top captionside=top store=no size=18
						@caption Linn

					@layout os_dl_layout type=vbox parent=offers_search

						@property os_dl_from type=date_select store=no parent=os_dl_layout captionside=top format=day_textbox,month_textbox,year_textbox
						@caption T&auml;htaeg alates

						@property os_dl_to type=date_select store=no parent=os_dl_layout captionside=top format=day_textbox,month_textbox,year_textbox
						@caption T&auml;htaeg kuni

					@property os_endless type=checkbox ch_value=1 store=no parent=offers_search captionside=top no_caption=1
					@caption Otsi ka t&auml;htajatuid

					@property os_status type=chooser store=no parent=offers_search captionside=top
					@caption Staatus

					@property os_confirmed type=chooser store=no parent=offers_search captionside=top
					@caption Kinnitatud

					@property os_employer type=textbox size=18 store=no parent=offers_search captionside=top
					@caption T&ouml;&ouml;pakkuja (komaga eraldatult)

					@property os_salary_from type=textbox size=4 store=no parent=offers_search captionside=top
					@caption Palk alates

					@property os_salary_to type=textbox size=4 store=no parent=offers_search captionside=top
					@caption Palk kuni

					@property os_field type=select multiple=1 store=no parent=offers_search captionside=top
					@caption Valdkond

					@property os_jobtype type=select multiple=1 store=no parent=offers_search captionside=top
					@caption T&ouml;&ouml; liik

					@property os_load type=select multiple=1 store=no parent=offers_search captionside=top
					@caption T&ouml&ouml;koormus

					@property os_workinfo type=textbox size=18 store=no parent=offers_search captionside=top
					@caption T&ouml&ouml; sisu

					@property os_requirements type=textbox size=18 store=no parent=offers_search captionside=top
					@caption N&otilde;udmised kandidaadile

					@property os_info type=textbox size=18 store=no parent=offers_search captionside=top
					@caption Lisainfo

					@property os_contact type=textbox size=18 store=no parent=offers_search captionside=top
					@caption Kontaktisik

					@property os_sbt type=submit parent=offers_search no_caption=1
					@caption Otsi

		@property offers_table type=table no_caption=1 parent=offers


----------------------------------------

@groupinfo actions caption="Tegevused" submit=no
@default group=actions

@property treeview3 type=text no_caption=1 default=asd

----------------------------------------

@groupinfo employers caption="T&ouml;&ouml;pakkujad" submit=no
@default group=employers

	@property employers_tlb type=toolbar no_caption=1 store=no

	@layout my_cust_bot type=hbox width=20%:80%

		@layout tree_search_split type=vbox parent=my_cust_bot

			@property tree_search_split_dummy type=hidden no_caption=1 parent=tree_search_split

			@layout vvoc_customers_tree_left type=vbox parent=tree_search_split closeable=1 area_caption=T&ouml;&ouml;pakkujate&nbsp;puu

				@property employers_tree type=treeview no_caption=1 parent=vvoc_customers_tree_left
				@caption R&uuml;hmade puu

			@layout vbox_customers_left type=vbox parent=tree_search_split closeable=1 area_caption=T&ouml;&ouml;pakkujate&nbsp;otsing
				@layout vbox_customers_left_top type=vbox parent=vbox_customers_left

					@property es_name type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Nimi

					@property es_sector type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Tegevusvaldkond

					@property es_legal_form type=relpicker reltype=RELTYPE_LEGAL_FORM automatic=1 no_edit=1 store=no parent=vbox_customers_left_top captionside=top
					@caption &Otilde;iguslik vorm

					@property es_location type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Asukoht

					@property es_contact type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Kontaktisik

					@property es_created_from type=date_select store=no parent=vbox_customers_left_top captionside=top
					@caption Liitunud alates

					@property es_created_to type=date_select store=no parent=vbox_customers_left_top captionside=top
					@caption Liitunud kuni

					@property es_profession type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption T&otilde;&otilde;pakkumise ametinimetus

				@layout vbox_customers_left_search_btn type=hbox parent=vbox_customers_left

					@property employers_submit type=submit size=15 store=no parent=vbox_customers_left_search_btn no_caption=1
					@caption Otsi

		@property employers_tbl type=table store=no no_caption=1 parent=my_cust_bot
		@caption Kliendid

---------------RELATION DEFINTIONS-----------------

@reltype MENU value=1 clid=CL_MENU
@caption Kaust

@reltype CRM_DB value=2 clid=CL_CRM_DB
@caption Kliendibaas

@reltype SECTORS value=3 clid=CL_METAMGR
@caption Tegevusvaldkonnad

@reltype OBJECT_TYPE value=4 clid=CL_OBJECT_TYPE
@caption Objektit&uuml;&uuml;p

@reltype OWNER_ORG value=5 clid=CL_CRM_COMPANY
@caption Omanikorganisatsioon

@reltype SEARCH_SAVE value=6 clid=CL_PERSONNEL_MANAGEMENT_CV_SEARCH_SAVED
@caption Otsingu salvestus

@reltype CFGFORM value=7 clid=CL_CFGFORM
@caption Default seadetevorm

@reltype JOB_WANTED_LOAD value=8 clid=CL_META
@caption Soovitud t&ouml;&ouml;koormus

@reltype SKILL_MANAGER value=9 clid=CL_CRM_SKILL_MANAGER
@caption Oskuste haldur

@reltype CFGCONTROLLER value=10 clid=CL_CFGCONTROLLER
@caption Kontroller

@reltype MOBI_HANDLER value=11 clid=CL_MOBI_HANDLER
@caption Mobi SMSi haldur

@reltype MESSENGER value=12 clid=CL_MESSENGER_V2
@caption Meilikast

@reltype DOC value=13 clid=CL_DOCUMENT
@caption Dokument veebist kandideerimiseks

@reltype NOTIFICATION_TPL value=14 clid=CL_MESSAGE_TEMPLATE
@caption Kandidatuurist teavitamise kiri

@reltype NOTIFY_FROM value=15 clid=CL_ML_MEMBER
@caption CV sisestamisest teavitaja

@reltype LANGUAGE value=16 clid=CL_LANGUAGE
@caption Teavitamise keel

@reltype NOTIFY_LOCATION value=17 clid=CL_CRM_CITY,CL_CRM_COUNTY,CL_CRM_COUNTRY,CL_CRM_AREA
@caption Teavitamise haldus&uuml;ksus

@reltype LEGAL_FORM value=19 clid=CL_CRM_CORPFORM
@caption &Otilde;iguslik vorm

@reltype SEARCH_CITY value=20 clid=CL_CRM_CITY
@caption Linn otsingus

@reltype SEARCH_COUNTY value=20 clid=CL_CRM_COUNTY
@caption Maakond otsingus

@reltype SEARCH_AREA value=20 clid=CL_CRM_AREA
@caption Piirkond otsingus

@reltype SCHOOLS_FOR_FACULTIES value=21 clid=CL_CRM_COMPANY
@caption Teaduskonnad puusse

@reltype SHOW_CNT value=22 clid=CL_GROUP
@caption Loendatav grupp

*/

class personnel_management extends class_base
{
	function personnel_management()
	{
		$this->init(array(
			"clid" => CL_PERSONNEL_MANAGEMENT,
			"tpldir" => "applications/personnel_management/personnel_management",
		));

		$this->search_vars = array(
			"name" => t("Nimi"),
			"age" => t("Vanus"),
			"gender" => t("Sugu"),
			"apps" => t("Kandideerimised"),
//			"contact" => t("Kontaktandmed"),
			"phones" => t("Telefon"),
			"emails" => t("E-postiaadress"),
			"status" => t("Aktiivne"),
			"approved" => t("Kinnitatud"),
			"show_cnt" => t("Vaatamisi"),
			"modtime" => t("Muutmise aeg"),
			"change" => t("Muuda")
		);
	}

	function callback_on_load($arr)
	{
		// Kids, don't do this!
		// We want a tab for every job offer type.
		$r = get_instance(CL_CLASSIFICATOR)->get_choices(array(
			"clid" => CL_PERSONNEL_MANAGEMENT,
			"name" => "os_type",
			"sort_callback" => "CL_PERSONNEL_MANAGEMENT::cmp_function",
		));
		if (isset($r[4]["list_names"]) and is_array($r[4]["list_names"]))
		{
			foreach($r[4]["list_names"] as $jo_type_id => $jo_type_name)
			{
				$this->groupinfo["offers_type_".$jo_type_id] = array(
					"caption" => $jo_type_name,
					"parent" => "offers",
					"submit" => "no",
				);
				$this->grpmap["offers"]["offers_type_".$jo_type_id] = $this->groupinfo["offers_type_".$jo_type_id];
				$this->_cfg_props["offers_tree"]["groups"][] = "offers_type_".$jo_type_id;
				$this->prop_by_group["offers_type_".$jo_type_id] = 1;
			}
		}
		// Set archive the last one!
		$tmp = $this->grpmap["offers"]["offers_archive"];
		unset($this->grpmap["offers"]["offers_archive"]);
		$this->grpmap["offers"]["offers_archive"] = $tmp;

		if(!$arr["new"] && $this->can("view", $arr["request"]["id"]))
		{
			$obj = obj($arr["request"]["id"]);
			if($this->can("view", $obj->prop("owner_org")))
			{
				$this->owner_org = $obj->prop("owner_org");
			}
			if($this->can("view", $obj->prop("persons_fld")))
			{
				$this->persons_fld = $obj->prop("persons_fld");
			}
			if($this->can("view", $obj->prop("offers_fld")))
			{
				$this->offers_fld = $obj->prop("offers_fld");
			}
			$this->default_prms = array(
				"employee" => array(
					"class_id" => CL_CRM_PERSON,
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_CRM_PERSON.parent" => $this->persons_fld,
							"CL_CRM_PERSON.RELTYPE_PERSONNEL_MANAGEMENT" => $arr["request"]["id"],
						),
					)),
					"site_id" => array(),
					"lang_id" => array(),
				),
			);
			$this->default_props = array(
				"employee" => array(
					CL_CRM_PERSON => array(
						"oid" => "oid",
						"name" => "name",
						"gender" => "gender",
						"birthday" => "birthday",
						"modified" => "modified",
					),
				),
			);
		}
	}

	function callback_mod_tab($arr)
	{
		if(!$arr["new"] && $this->owner_org)
		{
			if($arr["id"] == "actions")
			{
				$arr["link"] = $this->mk_my_orb("change", array("id" => $this->owner_org, "group" => "overview"), CL_CRM_COMPANY);
			}
		}
	}

	function callback_pre_edit($arr)
	{
		if(is_oid($arr["obj_inst"]->id()))
		{
			$arr["obj_inst"]->auto_archive();
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$person_language_inst = new crm_person_language();

		if(isset($arr["request"]["search_save"]) and $this->can("view", $arr["request"]["search_save"]))
		{	// If 'Varasem otsing' is selected.
			$sso = obj($arr["request"]["search_save"]);
			$arr["request"] += $sso->meta();
		}

		switch($prop["name"])
		{
			case "auto_archive_days":
				$prop["value"] = $prop["value"] ? $prop["value"] : 0;
				break;

			case "employers_submit":
				$prop["action"] = "search_employers";
				break;

			case "es_name":
			case "es_sector":
			case "es_legal_form":
			case "es_location":
			case "es_contact":
			case "es_profession":
				if(isset($_GET[$prop["name"]]))
				{
					$s = $_GET[$prop["name"]];
					$this->dequote($s);
					$prop['value'] = $s;
				}
				break;

			case "es_created_from":
				$prop["value"] = time() - 30*24*3600;
			case "es_created_to":
				$prop["year_to"] = date("Y");
				if(isset($_GET[$prop["name"]]))
				{
					$s = $_GET[$prop["name"]];
					$this->dequote($s);
					$prop['value'] = $s;
				}
				break;

			case "needed_acl_employee":
			case "needed_acl_candidate":
			case "needed_acl_job_offer":
				$prop["options"] = array(
					"view" => t("Vaatamine"),
					"edit" => t("Muutmine"),
					"add" => t("Lisamine"),
					"delete" => t("Kustutamine"),
					"admin" => t("Admin"),
				);
				break;

			case "location_conf":
			case "location_2_conf":
				$prop["options"] = array(
					CL_CRM_AREA => t("Piirkond"),
					CL_CRM_COUNTY => t("Maakond"),
					CL_CRM_CITY => t("Linn"),
				);
				break;

			case "job_offer_cv_tbl":
				$prop["options"] = array(
					"group" => t("Grupp"),
					"property" => t("Omadus"),
					"selected" => t("N&auml;ita vormis"),
					"mandatory" => t("Kohustuslik"),
					"jrk" => t("J&auml;rjekord"),
				);
				break;

			case "drivers_license":
				$prop["options"] = get_instance(CL_CRM_PERSON)->get_drivers_licence_original_categories();
				break;

			case "search_save":
				$u = obj(user::get_current_user());
				$ssol = new object_list(array(
					"class_id" => CL_PERSONNEL_MANAGEMENT_CV_SEARCH_SAVED,
					"parent" => array(),
					"status" => array(),
					"lang_id" => array(),
					"createdby" => $u->name(),
				));
				$prop["options"] = array(0 => t("--vali--")) + $ssol->names();
				$prop["onchange"] = "submit_changeform();";
				if(isset($arr["request"]["search_save"]) and $this->can("view", $arr["request"]["search_save"]))
				{
					$prop["value"] = $arr["request"]["search_save"];
				}
				break;

			case "cv_search_button_save_search":
				$prop["onclick"] = "aw_get_el('cv_search_saved_name').value=prompt('".t("Palun sisestage salvestatava otsingu nimi:")."');";
				break;

			case "employee_tb":
				if($arr["request"]["group"] == "candidate" && is_oid($arr["request"]["ofr_id"]))
				{
					$this->candidate_toolbar($arr);
				}
				elseif($arr["request"]["group"] == "employee")
				{
					$this->employee_tb($arr);
				}
				break;

			case "employee_tbl":
				if($arr["request"]["group"] == "candidate")
				{
					$this->candidate_table($arr);
				}
				elseif($arr["request"]["group"] == "employee")
				{
					$this->employee_tbl($arr);
				}
				$arr["obj_inst"]->set_prop("search_save", 0);
				$arr["obj_inst"]->save();
				break;

			case "employee_tree":
				if($arr["request"]["group"] == "candidate")
				{
					$this->candidate_tree($arr);
				}
				elseif($arr["request"]["group"] == "employee")
				{
					$this->employee_tree($arr);
				}
				break;

			case "cv_lang_exp_lvl":
				$prop["options"][0] = t("--vali--");
				$prop["options"] += $person_language_inst->lang_lvl_options;

				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "sysdefault_pm":
				if($arr["obj_inst"]->id() == $this->get_sysdefault())
					$prop["value"] = $prop["ch_value"];
				break;

			case "cv_status":
				$prop["options"] = array(
					0 => t("K&otilde;ik"),
					object::STAT_ACTIVE => t("Aktiivsed"),
					object::STAT_NOTACTIVE => t("Mitteaktiivsed"),
				);
				$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : 0;
				break;

			case "cv_approved":
				$prop["options"] = array(
					0 => t("K&otilde;ik"),
					2 => t("Kinnitatud"),
					1 => t("Kinnitamata"),
				);
				$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : 0;
				break;

			case "cv_udef_chbox1":
			case "cv_udef_chbox2":
			case "cv_udef_chbox3":
				$props = $this->can("view", $arr["obj_inst"]->default_offers_cfgform) ? get_instance(CL_CFGFORM)->get_props_from_cfgform(array("id" => $arr["obj_inst"]->default_offers_cfgform)) : get_instance(CL_CFGFORM)->get_default_proplist(array("clid" => CL_CRM_PERSON));
				if(!isset($props[substr($prop["name"], 3)]))
				{
					$retval = PROP_IGNORE;
				}
				$prop["caption"] = $props[substr($prop["name"], 3)]["caption"];
				$prop["options"] = array(
					0 => t("K&otilde;ik"),
					2 => t("Jah"),
					1 => t("Ei"),
				);
				$prop["value"] = isset($arr["request"][$prop["name"]]) ? $arr["request"][$prop["name"]] : 0;
				break;

			case "cv_city":
			case "cv_county":
			case "cv_area":
				$prop["options"] = $this->get_locations(constant("CL_CRM".strtoupper(substr($prop["name"], 2))));
				$prop['value'] = $arr['request'][$prop["name"]];
				break;

			case "cv_wrk_load":
				$r = get_instance(CL_CLASSIFICATOR)->get_choices(array(
					"clid" => CL_PERSONNEL_MANAGEMENT,
					"name" => "cv_load",
					"sort_callback" => "CL_PERSONNEL_MANAGEMENT::cmp_function",
				));
				$prop["options"] = $r[4]["list_names"];
				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "cv_gender":
				$prop["options"] = array(
					0 => t("K&otilde;ik"),
					1 => t("Mees"),
					2 => t("Naine"),
				);
			case "cv_tel":
			case "cv_email":
			case "cv_address":
			case "cv_addinfo":
			case "cv_schl":
			case "cv_schl_area":
			case "cv_schl_start_from":
			case "cv_schl_start_to":
			case "cv_search_button":
			case "cv_name":
			case "cv_oid":
			case "cv_company":
			case "cv_job":
			case "cv_paywish":
			case "cv_paywish2":
			case "cv_field":
			case "cv_previous_field":
			case "cv_type":
			case "cv_location":
			case "cv_load":
			case "cv_personality":
			case "cv_comments":
			case "cv_recommenders":
			case "cv_age_from":
			case "cv_age_to":
			case "cv_previous_rank":
			case "cv_praxis":
			case "cv_edu_exact":
			case "cv_edulvl_in_eduobj":
			case "cv_faculty":
			case "cv_speciality":
			case "cv_other_skills":

			case "os_type":
			case "os_pr":
			case "os_dl_from":
			case "os_dl_to":
			case "os_endless":
			case "os_employer":
			case "os_salary_from":
			case "os_salary_to":
			case "os_workinfo":
			case "os_requirements":
			case "os_info":
			case "os_contact":

			case "vs_name":
				if (isset($arr['request'][$prop["name"]]))
				{
					$s = $arr['request'][$prop["name"]];
					$this->dequote($s);
				}
				else
				{
					$s = "";
				}
				$prop['value'] = $s;
				break;

			case "os_jobtype":
				$r = get_instance(CL_CLASSIFICATOR)->get_choices(array(
					"clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
					"name" => "job_type",
					"sort_callback" => "CL_PERSONNEL_MANAGEMENT::cmp_function",
				));
				$prop["options"] = $r[4]["list_names"];
				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "os_load":
				$r = get_instance(CL_CLASSIFICATOR)->get_choices(array(
					"clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
					"name" => "load",
					"sort_callback" => "CL_PERSONNEL_MANAGEMENT::cmp_function",
				));
				$prop["options"] = $r[4]["list_names"];
				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "os_field":
				$prop["options"] = $this->get_sectors();
				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "os_county":
			case "os_city":
				if($prop["type"] != "textbox")
				{
					$prop["options"] = $this->get_locations(constant("CL_CRM".strtoupper(substr($prop["name"], 2))));
				}
				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "cv_bd_from":
			case "cv_bd_to":
			case "cv_mod_from":
			case "cv_mod_to":
				if(is_numeric($arr['request'][$prop["name"]]["day"]) && is_numeric($arr['request'][$prop["name"]]["month"]) && is_numeric($arr['request'][$prop["name"]]["year"]))
				{
					$prop["value"] = $arr['request'][$prop["name"]];
				}
				$prop["year_to"] = date("Y");
				break;

			case "os_dl_to":
				// Default is 1 month forward.
				$prop["value"] = mktime(0, 0, 0, date("m") + 1, date("d"), date("Y"));
				break;

			case "cv_driving_licence":
				$cats = get_instance(CL_CRM_PERSON)->drivers_licence_categories();
				$prop["value"] = "";
				foreach($cats as $k => $c)
				{
					$checked = isset($arr["request"]["cv_driving_licence"][$k]) ? ($arr["request"]["cv_driving_licence"][$k] == $k) : false;
					$prop["value"] .= html::checkbox(array(
						"name" => "cv_driving_licence[".$k."]",
						"value" => $k,
						"checked" => $checked,
						"caption" => $c,
						"nbsp" => 1,
						"span" => 1,
					))."&nbsp;";
				}
				break;

			case "cv_mother_tongue":
			case "cv_lang_exp":
				$options = array();
				$options[0] = t("--vali--");
				$options += $this->get_languages();
				$prop["options"] = $options;

				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "cv_edulvl":
				$person_inst = get_instance(CL_CRM_PERSON);
				$prop["options"] = $person_inst->edulevel_options;

				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "cv_acdeg":
				$person_inst = get_instance(CL_CRM_PERSON);
				$prop["options"] = $person_inst->academic_degree_options;

				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "cv_schl_stat":
				$prop["options"] = array(
					0 => t("K&otilde;ik"),
					1 => t("Omandamisel"),
					2 => t("L&otilde;petanud"),
				);

				$s = $arr['request'][$prop["name"]];
				$this->dequote($s);
				$prop['value'] = $s;
				break;

			case "offers_table":
				$this->offers_table($arr);
				break;

			case "employee_list_tree":
				return PROP_IGNORE;
				$this->employee_list_tree($arr);
				break;

			case "cv_exps_n_lvls":
				$this->get_cv_exps_n_lvls($arr);
				break;

			case "os_confirmed":
				$prop["options"] = array(
					"0" => t("K&otilde;ik"),
					"1" => t("Kinnitatud"),
					"2" => t("Kinnitamata"),
				);
				break;

			case "os_status":
				$prop["options"] = array(
					"0" => t("K&otilde;ik"),
					"2" => t("Aktiivsed"),
					"1" => t("Mitteaktiivne"),
				);
				$prop["value"] = $arr['request'][$prop["name"]];
				break;
		}
		return $retval;
	}

	function _init_employers_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("T&ouml;&ouml;pakkuja"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("T&uuml;&uuml;p"),
			"align" => "center",
			"sortable" => true,
		));
		$t->define_field(array(
			"name" => "address",
			"caption" => t("Aadress"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "contact_person",
			"caption" => t("Kontaktisik"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "contact_data",
			"caption" => t("Kontakandmed"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Liitumise aeg"),
			"align" => "center",
			"sortable" => true,
			"sorting_field" => "created_tm",
		));

		$t->set_default_sortby("name");

		// The caption
		if(!isset($_GET["branch_id"]) || $_GET["branch_id"] == "all")
		{
			$caption = t("K&otilde;ik t&ouml;&ouml;pakkujad");
		}
		elseif($_GET["branch_id"] == "search")
		{
			$caption = t("T&ouml&ouml;pakkujateotsingu tulemused");
		}
		elseif(is_oid($_GET["branch_id"]))
		{
			$o = obj($_GET["branch_id"]);
			switch($o->class_id())
			{
				case CL_CRM_SECTOR:
					$caption = sprintf(t("T&ouml;&ouml;pakkujad, kelle tegevusalade hulgas on '%s'"), parse_obj_name($o->name()));
					break;

				case CL_CRM_COUNTRY:
					$caption = sprintf(t("T&ouml;&ouml;pakkujad, kes asuvad riigis '%s'"), parse_obj_name($o->name()));
					break;

				case CL_CRM_AREA:
					$caption = sprintf(t("T&ouml;&ouml;pakkujad, kes asuvad piirkonnas '%s'"), parse_obj_name($o->name()));
					break;

				case CL_CRM_COUNTY:
					$caption = sprintf(t("T&ouml;&ouml;pakkujad, kes asuvad maakonnas '%s'"), parse_obj_name($o->name()));
					break;

				case CL_CRM_CITY:
					$caption = sprintf(t("T&ouml;&ouml;pakkujad, kes asuvad linnas '%s'"), parse_obj_name($o->name()));
					break;
			}
		}
		if(isset($_GET["filt_p"]))
		{
			$caption .= sprintf(t(" (valitud filter: %s)"), strtoupper($_GET["filt_p"]));
		}

		$t->set_caption($caption);
	}

	function _get_vars_tlb($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		switch($_GET["branch_id"])
		{
			case "lang":
				$pt = is_oid($arr["obj_inst"]->languages_fld) ? $arr["obj_inst"]->languages_fld : $arr["obj_inst"]->id();
				$t->add_new_button(array(CL_LANGUAGE), $pt);
				$t->add_save_button();
				break;

			case "profession":
				$pt = is_oid($arr["obj_inst"]->professions_fld) ? $arr["obj_inst"]->professions_fld : $arr["obj_inst"]->id();
				$t->add_new_button(array(CL_CRM_PROFESSION), $pt);
				$t->add_save_button();
				break;

			case "school":
				$pt = is_oid($arr["obj_inst"]->schools_fld) ? $arr["obj_inst"]->schools_fld : $arr["obj_inst"]->id();
				$t->add_new_button(array(CL_CRM_COMPANY), $pt);
				$t->add_save_button();
				break;

			case "faculty":
				$pt = is_oid($arr["obj_inst"]->faculties_fld) ? $arr["obj_inst"]->faculties_fld : $arr["obj_inst"]->id();
				$t->add_new_button(array(CL_CRM_SECTION), $pt);
				$t->add_save_button();
				break;

			case "location":
				$pt = is_oid($arr["obj_inst"]->locations_fld) ? $arr["obj_inst"]->locations_fld : $arr["obj_inst"]->id();
				$t->add_new_button(array(CL_CRM_COUNTRY), $pt);
				$t->add_save_button();
				break;

			case "sector":
				$pt = is_oid($arr["obj_inst"]->sectors_fld) ? $arr["obj_inst"]->sectors_fld : $arr["obj_inst"]->id();
				$t->add_new_button(array(CL_CRM_SECTOR), $pt);
				$t->add_save_button();
				break;

			case "job_offer_type":
				$cs = obj($arr["obj_inst"]->job_offer_ot)->meta("classificator");
				$pt = $cs["jo_type"];
				if($this->can("add", $pt))
				{
					$t->add_new_button(array(CL_META), $pt);
				}
				$t->add_save_button();
				$t->add_delete_button();
				break;

			case "load":
				$cs = obj($arr["obj_inst"]->personnel_management_ot)->meta("classificator");
				$pt = $cs["cv_load"];
				if($this->can("add", $pt))
				{
					$t->add_new_button(array(CL_META), $pt);
				}
				$t->add_save_button();
				$t->add_delete_button();
				break;

			case "legal_form":
				$pt = is_oid($arr["obj_inst"]->legal_forms_fld) ? $arr["obj_inst"]->legal_forms_fld : $arr["obj_inst"]->id();
				$t->add_new_button(array(CL_CRM_CORPFORM), $pt);
				$t->add_save_button();
				$t->add_delete_button();
				break;

			case "udef_classificator_1":
				$cs = obj($arr["obj_inst"]->job_offer_ot)->meta("classificator");
				$pt = $cs["udef_classificator_1"];
				if($this->can("add", $pt))
				{
					$t->add_new_button(array(CL_META), $pt);
				}
				$t->add_save_button();
				$t->add_delete_button();
				break;

			default:
				if(is_oid($_GET["branch_id"]))
				{
					$o = obj($_GET["branch_id"]);
					switch($o->class_id())
					{
						case CL_CRM_COMPANY:
							$pt = $_GET["branch_id"];
							$t->add_button(array(
								"name" => "new",
								"img" => "new.gif",
								"url" => html::get_new_url(CL_CRM_SECTION, $pt, array(
									"alias_to" => $pt,
									"reltype" => 28,
									"return_url" => get_ru(),
								)),
								"tooltip" => t("Lisa teaduskond"),
							));
							break;

						case CL_CRM_COUNTRY:
						case CL_CRM_AREA:
						case CL_CRM_COUNTY:
							$t->add_menu_button(array(
								"name" => "new",
								"img" => "new.gif",
								"tooltip" => t("Lisa"),
							));
							$clids = array(
								CL_CRM_COUNTRY => 1,
								CL_CRM_AREA => 2,
								CL_CRM_COUNTY => 3,
								CL_CRM_CITY => 4,
							);
							$classes = aw_ini_get("classes");
							foreach(array_keys($clids) as $clid)
							{
								$pt = $arr["obj_inst"]->locations_fld;
								if($clid != CL_CRM_COUNTRY)
								{
									if($clids[$clid] > $clids[$o->class_id()])
									{
										$pt = $o->id();
									}
									else
									{
										foreach(array_reverse($o->path()) as $p)
										{
											if(isset($clids[$p->class_id()]) && $clids[$p->class_id()] < $clids[$clid])
											{
												$pt = $p->id();
												break;
											}
										}
									}
								}
								$t->add_menu_item(array(
									"parent" => "new",
									"text" => $classes[$clid]["name"],
									"url" => html::get_new_url($clid, $pt, array(
										"return_url" => get_ru(),
									)),
								));
							}
//							$t->add_new_button(array($clid[$o->class_id()]), $_GET["branch_id"]);
							break;
					}
				}
				break;
		}
	}

	private function _init_vars_tbl(&$arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		switch($_GET["branch_id"])
		{
			case "lang":
				$t->define_field(array(
					"name" => "lang",
					"caption" => t("Keel"),
				));
				$t->define_field(array(
					"name" => "sel",
					"caption" => t("Kasuta keelt personalikeskkonnas"),
					"align" => "center",
				));
				$t->define_field(array(
					"name" => "sel_rec",
					"caption" => t("Kasuta keelt soovitajaga kontakteerumiseks"),
					"align" => "center",
				));
				break;

			case "profession":
			case "school":
			case "faculty":
			case "location":
			case "legal_form":
			case "sector":
				$t->define_chooser();
				$t->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
					"sortable" => 1,
				));
				break;

			case "job_offer_type":
			case "load":
			case "udef_classificator_1":
				$t->define_chooser();
				$t->define_field(array(
					"name" => "jrk",
					"caption" => t("J&auml;rjekord"),
					"align" => "center",
					"sortable" => 1,
				));
				$t->define_field(array(
					"name" => "name",
					"caption" => t("Nimi"),
					"sortable" => 1,
				));
				break;

			default:
				if(is_oid($_GET["branch_id"]))
				{
					$o = obj($_GET["branch_id"]);
					switch($o->class_id())
					{
						case CL_CRM_COMPANY:
							$t->define_chooser();
							$t->define_field(array(
								"name" => "name",
								"caption" => t("Nimi"),
								"sortable" => 1,
							));
							break;

						case CL_CRM_COUNTRY:
						case CL_CRM_AREA:
						case CL_CRM_COUNTY:
							$t->define_chooser();
							$t->define_field(array(
								"name" => "name",
								"caption" => t("Nimi"),
								"sortable" => 1,
							));
							$t->define_field(array(
								"name" => "type",
								"caption" => t("T&uuml;p"),
								"sortable" => 1,
							));
							break;
					}
				}
				break;
		}
	}

	function _get_vars_tbl($arr)
	{
		$this->_init_vars_tbl($arr);

		$t = &$arr["prop"]["vcl_inst"];

		switch($_GET["branch_id"])
		{
			case "lang":
				$odl = new object_data_list(
					array(
						"name" => "%".htmlspecialchars($_GET["vs_name"])."%",
						"class_id" => CL_LANGUAGE,
						"parent" => array(),
						"lang_id" => array(),
						"site_id" => array(),
						"status" => array(),
					),
					array(
						CL_LANGUAGE => array("name"),
					)
				);
				$lang_tbl = $this->get_lang_conf(array("id" => $arr["obj_inst"]->id()));
				$rec_lang_tbl = $this->get_rec_lang_conf(array("id" => $arr["obj_inst"]->id()));
				foreach($odl->arr() as $oid => $odata)
				{
					$t->define_data(array(
						"lang" => html::obj_change_url($oid),
						"sel" => html::checkbox(array(
							"name" => "lang_tbl[".$oid."]",
							"value" => 1,
							"checked" => $lang_tbl[$oid],
						)),
						"sel_rec" => html::checkbox(array(
							"name" => "rec_lang_tbl[".$oid."]",
							"value" => 1,
							"checked" => $rec_lang_tbl[$oid],
						)),
					));
				}
				$t->sort_by(array(
					"field" => "jrk",
					"sorder" => "ASC",
				));
				break;

			case "profession":
				$odl = $this->get_professions(array(
					"obj_inst" => $arr["obj_inst"],
					"return_as_odl" => true,
				));
				foreach($odl->arr() as $o)
				{
					$t->define_data(array(
						"oid" => $o["oid"],
						"name" => html::obj_change_url($o["oid"]),
					));
				}
				break;

			case "school":
				foreach(array_reverse(array_keys($this->get_schools())) as $id)
				{
					$t->define_data(array(
						"oid" => $id,
						"name" => html::obj_change_url($id),
					));
				}
				break;

			case "faculty":
				$odl = new object_data_list(
					array(
						"name" => "%".htmlspecialchars($_GET["vs_name"])."%",
						"class_id" => CL_CRM_SECTION,
						"parent" => $arr["obj_inst"]->faculties_fld,
						"lang_id" => array(),
						"site_id" => array(),
					),
					array(
						CL_CRM_SECTION => array("oid", "name"),
					)
				);
				foreach($odl->arr() as $o)
				{
					$t->define_data(array(
						"oid" => $o["oid"],
						"name" => html::obj_change_url($o["oid"]),
					));
				}
				break;

			case "location":
				$ol = new object_list(array(
					"name" => "%".htmlspecialchars($_GET["vs_name"])."%",
					"class_id" => CL_CRM_COUNTRY,
					"lang_id" => array(),
					"site_id" => array(),
					"parent" => $arr["obj_inst"]->locations_fld,
				));
				foreach($ol->names() as $id => $name)
				{
					$t->define_data(array(
						"oid" => $id,
						"name" => html::obj_change_url($id, parse_obj_name($name)),
					));
				}
				break;

			case "sector":
				foreach(array_reverse(array_keys($this->get_sectors())) as $id)
				{
					$t->define_data(array(
						"oid" => $id,
						"name" => html::obj_change_url($id),
					));
				}
				break;

			case "job_offer_type":
			case "load":
			case "udef_classificator_1":
				if($_GET["branch_id"] == "load")
				{
					$r = get_instance(CL_CLASSIFICATOR)->get_choices(array(
						"clid" => CL_PERSONNEL_MANAGEMENT,
						"name" => "cv_load",
						"sort_callback" => "CL_PERSONNEL_MANAGEMENT::cmp_function",
					));
				}
				elseif($_GET["branch_id"] == "job_offer_type")
				{
					$r = get_instance(CL_CLASSIFICATOR)->get_choices(array(
						"clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
						"name" => "jo_type",
						"sort_callback" => "CL_PERSONNEL_MANAGEMENT::cmp_function",
					));
				}
				elseif($_GET["branch_id"] == "udef_classificator_1")
				{
					$r = get_instance(CL_CLASSIFICATOR)->get_choices(array(
						"clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
						"name" => "udef_classificator_1",
						"sort_callback" => "CL_PERSONNEL_MANAGEMENT::cmp_function",
					));
				}
				if(is_array($r[4]["list"]) && count($r[4]["list"]) > 0)
				{
					$odl = new object_data_list(
						array(
							"name" => "%".htmlspecialchars($_GET["vs_name"])."%",
							"class_id" => CL_META,
							"oid" => $r[4]["list"],
							"lang_id" => array(),
							"site_id" => array(),
						),
						array(
							CL_META => array("jrk", "name", "oid"),
						)
					);
					// I can't let object_data_list rearrange my meta objects.
					$odl_arr = $odl->arr();
					foreach(array_reverse($r[4]["list"]) as $oid)
					{
						if(!isset($odl_arr[$oid]))
						{
							continue;
						}

						$o = $odl_arr[$oid];
						$t->define_data(array(
							"oid" => $o["oid"],
							"jrk" => html::textbox(array(
								"name" => "vars_tbl[".$o["oid"]."][jrk]",
								"value" => $o["jrk"],
								"size" => 3,
							)).html::hidden(array(
								"name" => "vars_tbl[".$o["oid"]."][jrk_old]",
								"value" => $o["jrk"],
							)),
							"name" => html::obj_change_url($o["oid"]),
						));
					}
				}
				break;

			case "legal_form":
				$ol = new object_list(array(
					"name" => "%".htmlspecialchars($_GET["vs_name"])."%",
					"class_id" => CL_CRM_CORPFORM,
					"parent" => $arr["obj_inst"]->legal_forms_fld,
					"lang_id" => array(),
					"site_id" => array(),
				));
				foreach($ol->ids() as $oid)
				{
					$t->define_data(array(
						"oid" => $oid,
						"name" => html::obj_change_url($oid),
					));
				}
				break;

			default:
				if(is_oid($_GET["branch_id"]))
				{
					$o = obj($_GET["branch_id"]);
					switch($o->class_id())
					{
						case CL_CRM_COMPANY:
							foreach($o->faculties()->names() as $id => $name)
							{
								$t->define_data(array(
									"oid" => $id,
									"name" => html::obj_change_url($id, parse_obj_name($name)),
								));
							}
							break;

						case CL_CRM_COUNTRY:
						case CL_CRM_AREA:
						case CL_CRM_COUNTY:
						case CL_CRM_CITY:

							$classes = aw_ini_get("classes");
							$odl = new object_data_list(
								array(
									"class_id" => array(CL_CRM_AREA, CL_CRM_COUNTY, CL_CRM_CITY),
									"parent" => $o->id(),
									"lang_id" => array(),
									"site_id" => array(),
								),
								array(
									CL_CRM_AREA => array("oid", "name", "class_id"),
									CL_CRM_COUNTY => array("oid", "name", "class_id"),
									CL_CRM_CITY => array("oid", "name", "class_id"),
								)
							);
							foreach($odl->arr() as $id => $o)
							{
								$t->define_data(array(
									"oid" => $id,
									"name" => html::obj_change_url($id, parse_obj_name($o["name"])),
									"type" => $classes[$o["class_id"]]["name"],
								));
							}
							break;
					}
				}
				break;
		}
	}

	function _get_vars_tree($arr)
	{
		$props = get_instance(CL_CFGFORM)->get_default_proplist(array("clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER));
		$udef_classificator_1 = $props["udef_classificator_1"]["caption"];
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_only_one_level_opened(1);
		$t->set_selected_item($_GET["branch_id"]);

		$branches = array(
			"lang" => t("Keeled"),
			"profession" => t("Ametinimetused"),
			"school" => t("Koolid"),
//			"faculty" => t("Teaduskonnad"),
			"location" => t("Riigid"),
			"sector" => t("Tegevusalad"),
			"job_offer_type" => t("T&ouml;&ouml;pakkumise t&uuml;&uuml;bid"),
			"load" => t("T&ouml;&ouml;koormused"),
			"legal_form" => t("&Otilde;iguslikud vormid"),
			"udef_classificator_1" => $udef_classificator_1,
		);

		foreach($branches as $id => $name)
		{
			$t->add_item(0, array(
				"id" => $id,
				"name" => $name,
				"url" => aw_url_change_var(array(
					"branch_id" => $id,
					"vs_name" => NULL,
				)),
			));
		}

		// Schools
		foreach($this->get_schools() as $id => $name)
		{
			$t->add_item("school", array(
				"id" => $id,
				"name" => $name,
				"url" => aw_url_change_var(array(
					"branch_id" => $id,
					"vs_name" => NULL,
				)),
			));
		}

		// Locations
		if(is_oid($arr["obj_inst"]->locations_fld))
		{
			$ot = new object_tree(array(
				"class_id" => array(CL_CRM_COUNTRY, CL_CRM_COUNTY, CL_CRM_AREA),
				"parent" => $arr["obj_inst"]->locations_fld,
				"lang_id" => array(),
				"site_id" => array(),
			));
			$ids = $ot->ids();
			if(count($ids) > 0)
			{
				$odl = new object_data_list(
					array(
						"class_id" => array(CL_CRM_COUNTRY, CL_CRM_COUNTY, CL_CRM_AREA),
						"oid" => $ids,
						"lang_id" => array(),
						"site_id" => array(),
					),
					array(
						CL_CRM_COUNTRY => array("oid", "name", "parent", "class_id"),
					)
				);
				foreach($odl->arr() as $o)
				{
					$pt = $o["class_id"] == CL_CRM_COUNTRY ? "location" : $o["parent"];
					$t->add_item($pt, array(
						"id" => $o["oid"],
						"name" => $o["name"],
						"url" => aw_url_change_var(array(
							"branch_id" => $o["oid"],
							"vs_name" => NULL,
						)),
					));
				}
			}
		}
	}

	function _get_employers_tbl($arr)
	{
		enter_function("kaarel");
		if(!isset($_GET["branch_id"]) && !$arr["obj_inst"]->show_all_employers)
		{
			return false;
		}

		$this->_init_employers_tbl($arr);

		$t = $arr["prop"]["vcl_inst"];

		$addr_inst = get_instance(CL_CRM_ADDRESS);
		$person_inst = get_instance(CL_CRM_PERSON);

		$odl_arr = $this->employers_tbl_data($arr);

		$perpage = $arr["obj_inst"]->perpage;
		if(count($odl_arr) > $perpage)
		{
			$t->define_pageselector(array(
				"type" => "lbtxt",
				"records_per_page" => $perpage,
				"d_row_cnt" => count($odl_arr),
				"no_recount" => true,
			));
			$odl_arr = array_slice($odl_arr, $_GET["ft_page"] * $perpage, $perpage, true);
		}

		foreach($odl_arr as $o)
		{
			if(is_oid($o["firmajuht"]))
			{
				$firmajuhid[] = $o["firmajuht"];
			}
		}

		foreach($odl_arr as $o)
		{
			$ol = new object_list(array(
				"oid" => $o["contact"],
			));
			$t->define_data(array(
				"oid" => $o["oid"],
				"type" => $o["class_id"] == CL_CRM_PERSON ? t("Eraisik") : t("Organisatsioon"),
				"name" => html::obj_change_url($o["oid"]),
				"address" => $o["contact"],
				"contact_person" => html::obj_change_url($o["firmajuht"], parse_obj_name($o["firmajuhi_nimi"])),
				"contact_data" => is_oid($o["firmajuht"]) ? implode(", ", array_merge($person_inst->phones(array("id" => $o["firmajuht"]))->names(), $person_inst->emails(array("id" => $o["firmajuht"]))->names())) : "",
				"created" => get_lc_date($o["created"], LC_DATE_FORMAT_LONG_FULLYEAR)." ".date("H:i", $o["created"]),
				"created_tm" => $o["created"],
			));
		}
		exit_function("kaarel");
	}

	function employers_tbl_data($arr)
	{
		enter_function("personnel_management::employers_tbl_data");

		$prms = array(
			"class_id" => array(CL_CRM_COMPANY, CL_CRM_PERSON),
			"parent" => $arr["obj_inst"]->employers_fld,
		);

		if(!is_oid($arr["obj_inst"]->employers_fld))
		{
			unset($prms["parent"]);
		}
		if(isset($arr["class_id"]))
		{
			$prms["class_id"] = $arr["class_id"];
		}
		if(isset($_GET["filt_p"]))
		{
			$prms["name"] = $_GET["filt_p"]."%";
		}

		if($_GET["branch_id"] == "search")
		{
			$r = $arr["request"];
			if(isset($r["es_name"]) && strlen(trim($r["es_name"])) > 0)
			{
				$prms_2["CL_CRM_COMPANY.name"] = "%".$r["es_name"]."%";
				$prms_3["CL_CRM_PERSON.name"] = "%".$r["es_name"]."%";
			}
			if(isset($r["es_sector"]) && strlen(trim($r["es_sector"])) > 0)
			{
				$prms_2["CL_CRM_COMPANY.RELTYPE_TEGEVUSALAD.name"] = "%".$r["es_sector"]."%";
				$prms_2["CL_CRM_COMPANY.RELTYPE_TEGEVUSALAD.parent"] = $arr["obj_inst"]->sectors_fld;
			}
			if(isset($r["es_legal_form"]) && is_oid($r["es_legal_form"]))
			{
				$prms_2["CL_CRM_COMPANY.RELTYPE_ETTEVOTLUSVORM"] = $r["es_legal_form"];
			}
			if(isset($r["es_location"]) && strlen(trim($r["es_location"])) > 0)
			{
				$prms_2[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_CRM_COMPANY.RELTYPE_ADDRESS.RELTYPE_LINN.name" => "%".$r["es_location"]."%",
						"CL_CRM_COMPANY.RELTYPE_ADDRESS.RELTYPE_MAAKOND.name" => "%".$r["es_location"]."%",
						"CL_CRM_COMPANY.RELTYPE_ADDRESS.RELTYPE_PIIRKOND.name" => "%".$r["es_location"]."%",
						"CL_CRM_COMPANY.RELTYPE_ADDRESS.RELTYPE_RIIK.name" => "%".$r["es_location"]."%",
					),
				));
			}
			if(isset($r["es_contact"]) && strlen(trim($r["es_contact"])) > 0)
			{
				$prms_2["CL_CRM_COMPANY.firmajuht.name"] = "%".$r["es_contact"]."%";
			}
			if(isset($r["es_created_from"]) && is_numeric($r["es_created_from"]) && isset($r["es_created_to"]) && is_numeric($r["es_created_to"]) > 0)
			{
				$prms_2["CL_CRM_COMPANY.created"] = new obj_predicate_compare(
					OBJ_COMP_BETWEEN,
					$r["es_created_from"],
					$r["es_created_to"],
					"int"
				);
				$prms_3["CL_CRM_PERSON.created"] = new obj_predicate_compare(
					OBJ_COMP_BETWEEN,
					$r["es_created_from"],
					$r["es_created_to"],
					"int"
				);
			}
			else
			{
				if(isset($r["es_created_from"]) && is_numeric($r["es_created_from"]))
				{
					$prms["created"] = new obj_predicate_compare(
						OBJ_COMP_GREATER_OR_EQ,
						$r["es_created_from"],
						NULL,
						"int"
					);
					$prms["created"] = new obj_predicate_compare(
						OBJ_COMP_GREATER_OR_EQ,
						$r["es_created_from"],
						NULL,
						"int"
					);
				}
				if(isset($r["es_created_to"]) && is_numeric($r["es_created_to"]))
				{
					$prms["created"] = new obj_predicate_compare(
						OBJ_COMP_LESS_OR_EQ,
						$r["es_created_to"],
						NULL,
						"int"
					);
				}
			}
			if(isset($r["es_profession"]) && strlen(trim($r["es_profession"])) > 0)
			{
				$prms_2["CL_CRM_COMPANY.RELTYPE_ORG(CL_PERSONNEL_MANAGEMENT_JOB_OFFER).RELTYPE_PROFESSION.name"] = "%".$r["es_profession"]."%";
				$prms_3["CL_CRM_PERSON.RELTYPE_CONTACT(CL_PERSONNEL_MANAGEMENT_JOB_OFFER).RELTYPE_PROFESSION.name"] = "%".$r["es_profession"]."%";
			}
			$prms_2["class_id"] = CL_CRM_COMPANY;
			$prms_3["class_id"] = CL_CRM_PERSON;
			$prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => $prms_2,
					)),
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => $prms_3,
					)),
				),
			));
		}
		elseif(isset($_GET["branch_id"]) && is_oid($_GET["branch_id"]))
		{
			switch(obj($_GET["branch_id"])->class_id())
			{
				case CL_CRM_SECTOR:
					$prms["CL_CRM_COMPANY.RELTYPE_TEGEVUSALAD"] = $_GET["branch_id"];
					break;

				case CL_CRM_COUNTRY:
					$prms["CL_CRM_COMPANY.RELTYPE_ADDRESS.RELTYPE_RIIK"] = $_GET["branch_id"];
					break;

				case CL_CRM_AREA:
					$prms["CL_CRM_COMPANY.RELTYPE_ADDRESS.RELTYPE_PIIRKOND"] = $_GET["branch_id"];
					break;

				case CL_CRM_COUNTY:
					$prms["CL_CRM_COMPANY.RELTYPE_ADDRESS.RELTYPE_MAAKOND"] = $_GET["branch_id"];
					break;

				case CL_CRM_CITY:
					$prms["CL_CRM_COMPANY.RELTYPE_ADDRESS.RELTYPE_LINN"] = $_GET["branch_id"];
					break;
			}
		}

		$sortby = isset($_GET["sortby"]) && in_array($_GET["sortby"], array("name", "created", "type")) ? $_GET["sortby"] : "name";
		// We have to sort 'em here, cuz the table might get only fraction of the list.
		$prms[] = new obj_predicate_sort(array(
			$sortby => $_GET["sort_order"] == "desc" ? "desc" : "asc",
		));

		if($arr["return_as_names"])
		{
			$ol = new object_list($prms);
			$odl_arr = $ol->names();
		}
		else
		{
			$odl = new object_data_list(
				$prms,
				array(
					CL_CRM_COMPANY => array("oid", "class_id", "name", "created", "contact.name" => "contact", "firmajuht", "firmajuht.name" => "firmajuhi_nimi"),
					CL_CRM_PERSON => array("oid", "class_id", "name", "created"),
				)
			);
			$odl_arr = $odl->arr();
		}

		exit_function("personnel_management::employers_tbl_data");
		return $odl_arr;
	}

	function _get_employers_tlb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		// New button
		if($this->can("add", $arr["obj_inst"]->employers_fld))
		{
			$t->add_menu_button(array(
				"name" => "new",
				"img" => "new.gif",
				"tooltip" => t("Lisa uus"),
			));
			$t->add_menu_item(array(
				"parent" => "new",
				"name" => "org",
				"text" => t("Organisatsioon"),
				"link" => $this->mk_my_orb("new", array("parent" => $arr["obj_inst"]->employers_fld, "return_url" => get_ru()), CL_CRM_COMPANY),
			));
			$t->add_menu_item(array(
				"parent" => "new",
				"name" => "person",
				"text" => t("Eraisik"),
				"link" => $this->mk_my_orb("new", array("parent" => $arr["obj_inst"]->employers_fld, "return_url" => get_ru()), CL_CRM_PERSON),
			));
		}

		// Delete button
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "delete_employers",
		));

		$cv_array = safe_array(aw_unserialize(aw_global_get("cv_for_employer_".$arr["obj_inst"]->id())));
		if(count($cv_array) > 0)
		{
			$t->add_menu_button(array(
				"name" => "email",
				"img" => "mail_send.gif",
				"tooltip" => t("Saada e-kiri"),
				"action" => "send_email",
			));
			$t->add_menu_item(array(
				"parent" => "email",
				"name" => "email_email",
				"text" => t("Sisesta e-kiri"),
				"action" => "send_email",
			));
			$t->add_menu_item(array(
				"parent" => "email",
				"name" => "email_cv",
				"text" => t("Saada valitud CVd"),
				"action" => "send_cv_to_employer",
			));
		}
		else
		{
			$t->add_button(array(
				"name" => "email",
				"img" => "mail_send.gif",
				"tooltip" => t("Saada e-kiri"),
				"action" => "send_email",
			));
		}

		// A - Z filters
		$c = get_instance("vcl/popup_menu");
		$c->begin_menu("crm_co_ppl_filt");

		$c->add_item(array(
			"text" => t("T&uuml;hista"),
			"link" => aw_url_change_var("filt_p", null)
		));
		for($i = ord('A'); $i < ord("Z"); $i++)
		{
			$c->add_item(array(
				"text" => chr($i).($arr["request"]["filt_p"] == chr($i) ? " ".t("(Valitud)") : "" ),
				"link" => aw_url_change_var("filt_p", chr($i))
			));
		}

		$t->add_cdata(" ".t("Vali filter:")." ".$c->get_menu().(!empty($arr["request"]["filt_p"]) ? t("Valitud:").$arr["request"]["filt_p"] : "" ));
	}

	function _get_employers_tree($arr)
	{
		$t = &$arr['prop']['vcl_inst'];
		$t->set_only_one_level_opened(1);

		$t->add_item(0, array(
			"id" => "all",
			"name" => !isset($_GET["branch_id"]) && $arr["obj_inst"]->show_all_employers || $_GET["branch_id"] == "all" ? "<b>".t("K&otilde;ik t&ouml;&ouml;pakkujad")."</b>" : t("K&otilde;ik t&ouml;&ouml;pakkujad"),
			"url" => aw_url_change_var("branch_id", "all"),
		));

		// SECTORS
		$odl = new object_data_list(
			array(
				"class_id" => CL_CRM_SECTOR,
				"site_id" => array(),
				"lang_id" => array(),
				"parent" => $arr["obj_inst"]->sectors_fld,
			),
			array(
				CL_CRM_SECTOR => array("oid", "name"),
			)
		);
		$secs_count = 0;
		unset($first_oid);
		foreach($odl->arr() as $o)
		{
			$first_oid = isset($first_oid) ? $first_oid : $o["oid"];
			$t->add_item("sector", array(
				"id" => $o["oid"],
				"name" => $_GET["branch_id"] == $o["oid"] ? "<b>".parse_obj_name($o["name"])."</b>" : parse_obj_name($o["name"]),
				"url" => aw_url_change_var("branch_id", $o["oid"]),
			));
			$secs_count++;
		}
		if($secs_count > 0)
		{
			$t->add_item(0, array(
				"id" => "sector",
				"name" => t("Tegevusalad"),
				"url" => aw_url_change_var("branch_id", $first_oid),
			));
		}

		// LOCATIONS
		$loc_types = array(
			array(
				"clid" => "CL_CRM_COUNTRY",
				"reltype" => "RELTYPE_RIIK",
				"name" => "country",
				"caption" => t("Riik"),
			),
			array(
				"clid" => "CL_CRM_AREA",
				"reltype" => "RELTYPE_PIIRKOND",
				"name" => "area",
				"caption" => t("Piirkond"),
			),
			array(
				"clid" => "CL_CRM_COUNTY",
				"reltype" => "RELTYPE_MAAKOND",
				"name" => "county",
				"caption" => t("Maakond"),
			),
			array(
				"clid" => "CL_CRM_CITY",
				"reltype" => "RELTYPE_LINN",
				"name" => "city",
				"caption" => t("Linn"),
			),
		);
		$locs_count = 0;
		unset($first_oid);
		foreach($loc_types as $loc_type)
		{
			$loc_count = 0;
			$odl = new object_data_list(
				array(
					"class_id" => constant($loc_type["clid"]),
					"site_id" => array(),
					"lang_id" => array(),
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							$loc_type["clid"].".".$loc_type["reltype"]."(CL_CRM_ADDRESS).RELTYPE_ADDRESS(CL_CRM_COMPANY).parent" => $arr["obj_inst"]->employers_fld,
							$loc_type["clid"].".".$loc_type["reltype"]."(CL_CRM_ADDRESS).RELTYPE_ADDRESS(CL_CRM_PERSON).parent" => $arr["obj_inst"]->employers_fld,
						),
					))
				),
				array(
					constant($loc_type["clid"]) => array("oid", "name"),
				)
			);
			unset($first_oid_);
			foreach($odl->arr() as $o)
			{
				$first_oid = isset($first_oid) ? $first_oid : $o["oid"];
				$first_oid_ = isset($first_oid_) ? $first_oid_ : $o["oid"];
				$t->add_item($loc_type["name"], array(
					"id" => $o["oid"],
					"name" => $_GET["branch_id"] == $o["oid"] ? "<b>".parse_obj_name($o["name"])."</b>" : parse_obj_name($o["name"]),
					"url" => aw_url_change_var("branch_id", $o["oid"]),
				));
				$loc_count++;
			}
			if($loc_count > 0)
			{
				$t->add_item("location", array(
					"id" => $loc_type["name"],
					"name" => $loc_type["caption"],
					"url" => aw_url_change_var("branch_id", $first_oid_),
				));
				$locs_count++;
			}
		}
		if($locs_count > 0)
		{
			$t->add_item(0, array(
				"id" => "location",
				"name" => t("Asukoht"),
				"url" => aw_url_change_var("branch_id", $first_oid),
			));
		}
	}

	function _get_notify_loc_tbl($arr)
	{
		$conns = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_NOTIFY_LOCATION"));
		if(count($conns) == 0)
		{
			return PROP_IGNORE;
		}

		$mls = $arr["obj_inst"]->meta("notify_loc_tbl");

		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Haldus&uuml;ksus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("E-posti aadress"),
			"align" => "center",
		));
		$t->set_default_sortby("location");
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_NOTIFY_LOCATION")) as $conn)
		{
			$t->define_data(array(
				"location" => $conn->prop("to.name"),
				"email" => html::textbox(array(
					"name" => "notify_loc_tbl[".$conn->prop("to")."]",
					"value" => $mls[$conn->prop("to")],
				)),
			));
		}
	}

	function _get_pdf_tpl($arr)
	{
		$arr["prop"]["options"] = $this->pdf_tpl_options();
	}

	function _get_cv_tpl($arr)
	{
		$arr["prop"]["options"] = crm_person::get_cv_tpl();
	}

	function get_cv_exps_n_lvls($arr)
	{
		$skill_manager_inst = get_instance(CL_CRM_SKILL_MANAGER);
		$skill_manager_id = $arr["obj_inst"]->prop("skill_manager");
		if(!is_oid($skill_manager_id))
		{
			return false;
		}
		$skills = $skill_manager_inst->get_skills(array("id" => $skill_manager_id));

		$ret = "";
		foreach($skills[$skill_manager_id] as $id => $data)
		{
			$options = array(0 => t("--vali--"));
			$disabled_options = array();
			$this->add_skill_options($skills, $options, $disabled_options, $id, 0);
			$skill = obj($id);
			$ol = new object_list(array(
				"class_id" => CL_META,
				"parent" => $skill->prop("lvl_meta"),
				"status" => object::STAT_ACTIVE,
				"lang_id" => array(),
				"sort_by" => "jrk",
			));
			// There's no point in adding empty select fields.
			if(!strlen(trim($data["name"])) && count($options) == 1 && $ol->count() == 0)
			{
				continue;
			}
			// Need to add caption manually.
			$ret .= $data["name"]."<br />";
			$ret .= html::select(array(
				"name" => "cv_exp[".$id."]",
				"options" => $options,
				"multiple" => 1,
				"size" => 3,
				"disabled_options" => $disabled_options,
				"selected" => $arr["request"]["cv_exp"][$id],
			))."<br />";
			// Need to add caption manually.
			$ret .= t("Tase")."<br />";
			$ret .= html::select(array(
				"name" => "cv_exp_lvl[".$id."]",
				"options" => array(0 => t("--vali--")) + $ol->names(),
				"caption" => t("Tase"),
				//"multiple" => 1,
				//"size" => 3,
				"selected" => $arr["request"]["cv_exp_lvl"][$id],
			))."<br />";
		}
		$arr["prop"]["value"] = $ret;
	}

	function employee_tree($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->set_selected_item($_GET["branch_id"]);

		// KINNITAMATA
		$prms = $this->default_prms["employee"];
		$prms["cvapproved"] = new obj_predicate_not(1);
		$ol = new object_list($prms);

		$cnt = $ol->count();
		if($cnt > 0)
		{
			$t->add_item(0, array(
				"id" => "not_approved",
				"name" => t("Kinnitamata")." ($cnt)",
				"url" => $this->mk_my_orb("change", array("id" => $arr["request"]["id"], "group" => $arr["request"]["group"], "branch_id" => "not_approved")),
			));
		}

		// KEHTIVAD
		$prms = $this->default_prms["employee"];
		$prms["modified"] = new obj_predicate_compare(
			OBJ_COMP_GREATER_OR_EQ,
			time() - $arr["obj_inst"]->days_to_inactivity * 24*3600,
			false,
			"int"
		);
		$ol = new object_list($prms);
		$cnt = $ol->count();
		if($cnt > 0)
		{
			$t->add_item(0, array(
				"id" => "modified_lately",
				"name" => t("Kehtivad")." ($cnt)",
				"url" => $this->mk_my_orb("change", array("id" => $arr["request"]["id"], "group" => $arr["request"]["group"], "branch_id" => "modified_lately")),
			));
		}

		//AEGUNUD
		$prms = $this->default_prms["employee"];
		$prms["modified"] = new obj_predicate_compare(
			OBJ_COMP_LESS,
			time() - $arr["obj_inst"]->days_to_inactivity * 24*3600,
			false,
			"int"
		);
		$ol = new object_list($prms);
		$cnt = $ol->count();
		if($cnt > 0)
		{
			$t->add_item(0, array(
				"id" => "not_modified_lately",
				"name" => t("Aegunud")." ($cnt)",
				"url" => $this->mk_my_orb("change", array("id" => $arr["request"]["id"], "group" => $arr["request"]["group"], "branch_id" => "not_modified_lately")),
			));
		}

		// ASUKOHT
		$mcaps = array(
			"area" => t("Piirkonnad"),
			"county" => t("Maakonnad"),
			"city" => t("Linnad"),
		);
		$clids = array(
			"county" => CL_CRM_COUNTY,
			"city" => CL_CRM_CITY,
			"area" => CL_CRM_AREA,
		);
		$total = 0;
		$residents_by_location = $this->get_residents_by_location(array(
			"parent" => $this->persons_fld,
			"personnel_management" => $arr["obj_inst"]->id(),
		));
		foreach($mcaps as $k => $d)
		{
			$objs = new object_list(array(
				"parent" => array(),
				"class_id" => $clids[$k],
				"lang_id" => array(),
				"site_id" => array(),
			));
			$tot = 0;
			foreach($objs->arr() as $o)
			{
				if(!$this->check_special_acl_for_cat($o, CL_CRM_PERSON))
				{
					continue;
				}
				if(!isset($residents_by_location[$o->id()]))
				{
					continue;
				}
				$cnt = count($residents_by_location[$o->id()]);
				$str = " (".$cnt.")";
				$t->add_item($k, array(
					"id" => $o->id(),
					"name" => $o->name().$str,
					"url" => $this->mk_my_orb("change", array("id" => $arr["request"]["id"], "group" => $arr["request"]["group"], "branch_id" => $o->id())),
				));
				$tot += $cnt;
			}
			if($tot > 0)
			{
				$str = " (".$tot.")";
				$t->add_item("location", array(
					"id" => $k,
					"name" => $d.$str,
					"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => $k)),
				));
			}
			$total += $tot;
		}
		if($total > 0)
		{
			$t->add_item(0, array(
				"id" => "location",
				"name" => t("Asukoht"),
				"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => "location")),
			));
		}

		// TEADUSKONNAD
		foreach(safe_array($arr["obj_inst"]->schools_for_faculties) as $id)
		{
			$schl = obj($id);
			$cnt = 0;
			$edus = $schl->get_educations(array(
				"prms" => array(
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"CL_CRM_PERSON_EDUCATION.RELTYPE_PERSON.parent" => $this->persons_fld,
							"CL_CRM_PERSON_EDUCATION.RELTYPE_PERSON.RELTYPE_PERSONNEL_MANAGEMENT" => $arr["obj_inst"]->id(),
						),
					)),
				),
				"props" => array(
					CL_CRM_PERSON_EDUCATION => array("oid", "person", "faculty"),
				),
				"return_as_odl" => true,
			));
			$studets_by_faculties = array();
			foreach($edus->arr() as $edu)
			{
				$studets_by_faculties[$edu["faculty"]][$edu["person"]]++;
			}
			foreach($schl->faculties(array("return_as_odl" => true))->arr() as $fclty)
			{
				$cnt_ = count($studets_by_faculties[$fclty["oid"]]);
				if($cnt_ > 0)
				{
					$t->add_item($id, array(
						"id" => $fclty["oid"],
						"name" => parse_obj_name($fclty["name"])." ($cnt_)",
						"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => $fclty["oid"])),
					));
					$cnt += $cnt_;
				}
			}
			if($cnt > 0)
			{
				$t->add_item(0, array(
					"id" => $id,
					"name" => parse_obj_name($schl->name())." ($cnt)",
					"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => $id)),
				));
			}
		}
	}

	function table_flds($arr, $fn, $fld)
	{
		$fi = $arr["request"]["branch_id"];
		$t = &$arr["prop"]["vcl_inst"];
		$caps = array(
			"location" => t("Asukoht"),
			"county" => t("Maakond"),
			"city" => t("Linn"),
			"area" => t("Piirkond"),
		);
		$mcaps = array(
			"city" => t("Linnad"),
			"county" => t("Maakonnad"),
			"area" => t("Piirkonnad"),
		);
		$clids = array(
			"county" => CL_CRM_COUNTY,
			"city" => CL_CRM_CITY,
			"area" => CL_CRM_AREA,
		);
		$t->define_field(array(
			"name" => "name",
			"caption" => $caps[$fi],
			"align" => "center",
		));
		if($arr["request"]["branch_id"] == "location")
		{
			foreach($mcaps as $k => $d)
			{
				$cnt_tot = 0;
				$objs = new object_list(array(
					"parent" => array(),
					"class_id" => $clids[$k],
					"lang_id" => array(),
					"site_id" => array(),
				));
				foreach($objs->arr() as $o)
				{
					if(!$this->check_special_acl_for_cat($o, $arr["request"]["group"] == "employee" ? CL_CRM_PERSON : CL_PERSONNEL_MANAGEMENT_JOB_OFFER))
					{
						continue;
					}
					$fn_prms = array(
						"parent" => $this->$fld,
						"personnel_management" => $arr["obj_inst"]->id(),
						"by_jobwish" => ($fn == "get_residents") ? 1 : 0,
					);
					if($fn == "get_job_offers")
					{
						$fn_prms["props"] = array(
							"archive" => ($arr["request"]["group"] != "offers_archive" || $arr["request"]["group"] == "candidate") ? 0 : 1,
						);
						$fn_prms["status"] = object::STAT_ACTIVE;
					}
					$cnt_ol = $o->$fn($fn_prms);
					$cnt = $cnt_ol->count();
					$cnt_tot += $cnt;
				}
				if($cnt_tot > 0)
				{
					$t->define_data(array(
						"name" => html::href(array(
							"url" => $this->special_url($arr, array(
								"branch_id" => $k,
							)),
							"caption" => $d." (".$cnt_tot.")",
						)),
					));
				}
			}
		}
		else
		{
			$candidates_by_joboffers = $this->get_candidates_by_job_offer();
			$objs = new object_list(array(
				"parent" => array(),
				"class_id" => $clids[$fi],
				"lang_id" => array(),
				"site_id" => array(),
			));
			foreach($objs->arr() as $o)
			{
				if(!$this->check_special_acl_for_cat($o, $arr["request"]["group"] == "employee" ? CL_CRM_PERSON : CL_PERSONNEL_MANAGEMENT_JOB_OFFER))
				{
					continue;
				}
				$fn_prms = array(
					"parent" => $this->$fld,
					"personnel_management" => $arr["obj_inst"]->id(),
					"by_jobwish" => ($fn == "get_residents") ? 1 : 0,
				);
				if($fn == "get_job_offers")
				{
					$fn_prms["props"] = array(
						"archive" => ($arr["request"]["group"] != "offers_archive" || $arr["request"]["group"] == "candidate") ? 0 : 1,
					);
					$fn_prms["status"] = object::STAT_ACTIVE;
				}
				$cnt_ol = $o->$fn($fn_prms);
				if($arr["request"]["group"] != "candidate")
				{
					$cnt = $cnt_ol->count();
				}
				else
				{
					$cnt = 0;
					foreach($cnt_ol->arr() as $cnt_o)
					{
						$cnt += count($candidates_by_joboffers[$cnt_o->id()]);
					}
				}
				if($cnt == 0)
				{
					continue;
				}
				$str = " (".$cnt.")";
				$t->define_data(array(
					"name" => html::href(array(
						"url" => $this->special_url($arr, array(
							"branch_id" => $o->id(),
							$fi."_id" => $o->id(),
						)),
						"caption" => $o->name().$str,
					)),
				));
			}
		}
	}

	function employee_tb($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_CRM_PERSON), $this->persons_fld);
		$t->add_search_button(array(
			"pn" => "add_employee",
			"multiple" => 1,
			"clid" => CL_CRM_PERSON,
		));
		$t->add_delete_button();
		$lists = new object_list(array(
			"class_id" => CL_CRM_CATEGORY,
			"parent" => array(),
			"sort_by" => "name",
			"lang_id" => array(),
		));
		if($lists->count() > 0)
		{
			$t->add_menu_button(array(
				"name" => "add2list",
				"img" => "important.png",
				"tooltip" => t("Lisa listi"),
			));
			foreach($lists->arr() as $list)
			{
				$t->add_menu_item(array(
					"parent" => "add2list",
					"name" => "add2list_".$list->id(),
					"text" => $list->name(),
					"action" => "add2list",
					"onClick" => "aw_get_el('list_id').value=".$list->id().";"
				));
			}
		}
		$t->add_button(array(
			"name" => "email",
			"img" => "mail_send.gif",
			"tooltip" => t("Saada e-kiri"),
			"action" => "send_email",
		));
		$t->add_menu_button(array(
			"name" => "send_cv",
			"img" => "mail_send.gif",
			"tooltip" => t("Saada CV e-postiga"),
		));
		$t->add_menu_item(array(
			"parent" => "send_cv",
			"name" => "send_cv_email",
			"text" => t("E-postile"),
			"action" => "send_cv_email",
			"onClick" => "aw_get_el('email_to_send_cv_to').value = prompt('".t("Sisesta e-posti aadress(id), millele valitud CVd saata.")."');",
		));
		$t->add_menu_item(array(
			"parent" => "send_cv",
			"name" => "send_cv_employer",
			"text" => t("T&ouml;&ouml;pakkujale"),
			"action" => "send_cv_employer",
		));
		$t->add_button(array(
			'name' => 'csv',
			'img' => 'ftype_xls.gif',
			'tooltip' => t('CSV'),
			"url" => aw_url_change_var("get_csv_file", 1)
		));
	}

	function employee_tbl($arr)
	{
		if($arr["request"]["branch_id"] && !is_oid($arr["request"]["branch_id"]) && !in_array($arr["request"]["branch_id"], array("not_approved", "modified_lately", "not_modified_lately")))
		{
			return $this->table_flds($arr, "get_residents", "persons_fld");
		}
		elseif(!$arr["request"]["branch_id"] && !$arr["request"]["cv_search_button"] && !$arr["request"]["cv_search_button_save_search"] && !$this->can("view", $arr["request"]["search_save"]))
		{
			return false;
		}

		$obj = new object_list();

		$t = &$arr["prop"]["vcl_inst"];

		$vars = $this->search_vars;
		$gender = array(1 => t("mees"), t("naine"), "" => t("m&auml;&auml;ramata"));
		$conf = $arr["obj_inst"]->meta("search_conf_tbl");
		$conf = $conf["employee"];
		$person_inst = get_instance(CL_CRM_PERSON);

		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
		foreach($vars as $name => $caption)
		{
			if(!$conf[$name]["disabled"])
			{
				$t->define_field(array(
					"name" => $name,
					"caption" => $caption,
					"align" => "center",
					"sortable" => 1,
				));
			}
		}

		// Figure out the persons to show.
		if($arr["request"]["branch_id"] == "not_approved")
		{
			$prms = $this->default_prms["employee"];
			$prms["cvapproved"] = new obj_predicate_not(1);
			$odl = new object_data_list(
				$prms,
				$this->default_props["employee"]
			);
			$objs = $odl->arr();
		}
		elseif($arr["request"]["branch_id"] == "modified_lately")
		{
			$prms = $this->default_prms["employee"];
			$prms["modified"] = new obj_predicate_compare(
				OBJ_COMP_GREATER_OR_EQ,
				time() - $arr["obj_inst"]->days_to_inactivity * 24*3600,
				false,
				"int"
			);
			$odl = new object_data_list(
				$prms,
				$this->default_props["employee"]
			);
			$objs = $odl->arr();
		}
		elseif($arr["request"]["branch_id"] == "not_modified_lately")
		{
			$prms = $this->default_prms["employee"];
			$prms["modified"] = new obj_predicate_compare(
				OBJ_COMP_LESS,
				time() - $arr["obj_inst"]->days_to_inactivity * 24*3600,
				false,
				"int"
			);
			$odl = new object_data_list(
				$prms,
				$this->default_props["employee"]
			);
			$objs = $odl->arr();
		}
		elseif($this->can("view", $arr["request"]["branch_id"]))
		{
			$o = obj($arr["request"]["branch_id"]);
			switch($o->class_id())
			{
				case CL_CRM_AREA:
				case CL_CRM_COUNTY:
				case CL_CRM_CITY:
					$odl = $o->get_residents(array(
						"parent" => $this->persons_fld,
						"personnel_management" => $arr["obj_inst"]->id(),
						"by_jobwish" => 1,
						"return_as_odl" => true,
						"props" => $this->default_props["employee"],
					));
					$objs = $odl->arr();
					break;

				case CL_CRM_SECTION:
					$objs = $o->get_students(array(
						"return_as_odl" => true,
						"prms" => $this->default_prms["employee"],
						"props" => $this->default_props["employee"],
					))->arr();
					break;

				default:
					$objs = array();
					break;
			}
		}
		else
		{
			if($this->can("view", $arr["request"]["search_save"]))
			{
				$sso = obj($arr["request"]["search_save"]);
				$arr["request"] += $sso->meta();
			}
			$objs = $this->search_employee($arr);
		}
		// Check the special ACL for these objs.
		$objs = $this->filter_search_results_by_acl($objs, $arr["obj_inst"]);

		// Spread the stuff to pages, if needed.
		$perpage = $arr["obj_inst"]->prop("perpage");
		if(count($objs) > $perpage)
		{
			$t->define_pageselector(array(
				"type" => "lbtxt",
				"records_per_page" => $perpage,
				"d_row_cnt" => count($objs),
				"no_recount" => true,
			));
			$objs = array_slice($objs, $_GET["ft_page"] * $perpage, $perpage);
		}

		// Show 'em!
		foreach($objs as $obj_data)
		{
			$obj = obj($obj_data["oid"]);
			$row = array(
				"id" => $obj_data["oid"],
				"name" => html::href(array(
					"url" => $this->mk_my_orb("show_cv", array("id" => $obj_data["oid"], "cv" => "cv/".basename($arr["obj_inst"]->prop("cv_tpl")), "die" => "1"), CL_CRM_PERSON),
					"caption" => parse_obj_name($obj_data["name"]),
				)),
				"gender" => $gender[$obj_data["gender"]],
				"modtime" => date("Y-m-d H:i:s", $obj_data["modified"]),
				"change" => html::get_change_url($obj_data["oid"], array("return_url" => get_ru()), t("Muuda")),
				"status" => $obj_data["cvactive"] ? t("Jah") : t("Ei"),
				"approved" => $obj_data["cvapproved"] ? t("Jah") : t("Ei"),
//				"contact" => implode(", ", array_merge($obj->phones()->names(), $obj->emails()->names())),
			);
			if(empty($conf["age"]["disabled"]))
			{
				$row["age"] = obj($obj_data["oid"])->get_age();
			}
			if(empty($conf["apps"]["disabled"]))
			{
				$apps = "";
				foreach(obj($obj_data["oid"])->get_applications(array("parent" => $this->offers_fld, "status" => object::STAT_ACTIVE))->names() as $app_id => $app_name)
				{
					$apps .= (strlen($apps) > 0) ? ", " : "";
					$apps .= html::href(array(
						"caption" => parse_obj_name($app_name),
						"url" => $this->mk_my_orb("change", array("id" => $app_id, "return_url" => get_ru()), CL_PERSONNEL_MANAGEMENT_JOB_OFFER),
					));
				}
				$row["apps"] = $apps;
			}
			if(empty($conf["show_cnt"]["disabled"]))
			{
				$row["show_cnt"] = obj($obj_data["oid"])->show_cnt;
			}
			$t->define_data($row);
		}

		if($_GET["get_csv_file"] == 1)
		{
			header('Content-type: application/octet-stream');
			header('Content-disposition: root_access; filename="csv_output.csv"');
			die($t->get_csv_file());
		}
	}

	private function get_candidates_by_job_offer($arr = array())
	{
		enter_function("personnel_management::get_candidates_by_job_offer");
		$res = array();
		$odl = new object_data_list(
			array(
				"class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE,
				"site_id" => array(),
				"lang_id" => array(),
			),
			array(
				CL_PERSONNEL_MANAGEMENT_CANDIDATE => array("job_offer", "person"),
			)
		);
		foreach($odl->arr() as $o)
		{
			$res[$o["job_offer"]][$o["person"]] = 1;
		}
		exit_function("personnel_management::get_candidates_by_job_offer");
		return $res;
	}

	private function get_residents_by_location($arr)
	{
		enter_function("personnel_management::get_residents_by_location");
		$res = array();
		$odl = new object_data_list(
			array(
				"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_WANTED,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_PERSONNEL_MANAGEMENT_JOB_WANTED.RELTYPE_PERSON.parent" => $arr["parent"],
						"CL_PERSONNEL_MANAGEMENT_JOB_WANTED.RELTYPE_PERSON.RELTYPE_PERSONNEL_MANAGEMENT" => $arr["personnel_management"],
					)
				)),
				// The next line SHOULD be (but isn't!) unnecessary, cuz deleted objects can't have connections anyway. -kaarel
				"CL_PERSONNEL_MANAGEMENT_JOB_WANTED.RELTYPE_PERSON.status" => array(object::STAT_ACTIVE, object::STAT_NOTACTIVE),
				"site_id" => array(),
				"lang_id" => array(),
			),
			array(
				CL_PERSONNEL_MANAGEMENT_JOB_WANTED => array("person", "location", "location_2"),
			)
		);
		foreach($odl->arr() as $o)
		{
			$locs = array_merge((array)$o["location"], (array)$o["location_2"]);
			foreach($locs as $loc)
			{
				$res[$loc][$o["person"]] = 1;
			}
		}
		exit_function("personnel_management::get_residents_by_location");
		return $res;
	}

	private function filter_search_results_by_acl($res, $pm)
	{
		if(count($res) === 0)
		{
			return $res;
		}
		$ids = array();
		foreach($res as $rese)
		{
			$ids[] = $rese["oid"];
		}
		$odl_jobwishes = new object_data_list(
			array(
				"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_WANTED,
				"CL_PERSONNEL_MANAGEMENT_JOB_WANTED.RELTYPE_PERSON" => $ids,
				"site_id" => array(),
				"lang_id" => array(),
			),
			array(
				CL_PERSONNEL_MANAGEMENT_JOB_WANTED => array("person", "location", "location_2"),
			)
		);
		// Get all the locations by job wishes.
		foreach($odl_jobwishes->arr() as $jw)
		{
			$asd[$jw["person"]] = array_merge((array)$asd[$jw["person"]], (array)$jw["location"], (array)$jw["location_2"]);
		}
		// Make a list of locations by job offers
		$odl_joboffers = new object_data_list(
			array(
				"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
				"CL_PERSONNEL_MANAGEMENT_JOB_OFFER.RELTYPE_CANDIDATE.RELTYPE_PERSON" => $ids,
				"site_id" => array(),
				"lang_id" => array(),
			),
			array(
				CL_PERSONNEL_MANAGEMENT_JOB_OFFER => array("oid", "loc_country", "loc_area", "loc_county", "loc_city", "parent"),
			)
		);
		foreach($odl_joboffers->arr() as $jo)
		{
			$jo_locs[$jo["oid"]] = array($jo["loc_country"], $jo["loc_area"], $jo["loc_county"], $jo["loc_city"], $jo["parent"]);
		}
		// Now add locations for persons from their candidate objects.
		$odl_candidates = new object_data_list(
			array(
				"class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE,
				"CL_PERSONNEL_MANAGEMENT_CANDIDATE.RELTYPE_PERSON" => $ids,
				"site_id" => array(),
				"lang_id" => array(),
			),
			array(
				CL_PERSONNEL_MANAGEMENT_CANDIDATE => array("person", "job_offer"),
			)
		);
		foreach($odl_candidates as $cd)
		{
			$asd[$cd["person"]] = array_merge((array)$asd[$cd["person"]], (array)$jo_locs[$cd["job_offer"]]);
		}
		// Finally run through the person => locations array and sort out the ones we're not supposed to show.
		$loc_bool = array();
		foreach($asd as $pid => $locs)
		{
			$ok = false;
			$checked_count = 0;
			foreach($locs as $loc)
			{
				if(!is_oid($loc))
				{
					continue;
				}
				$checked_count++;
				if(!isset($loc_bool[$loc]))
				{
					$loc_bool[$loc] = $this->check_special_acl_for_cat($loc, CL_CRM_PERSON, $pm);
				}
				$ok = $ok || $loc_bool[$loc];
			}
			if(!$ok && $checked_count !== 0)
			{
				unset($res[$pid]);
			}
		}
		return $res;
	}

	function search_employee($arr)
	{
		$o = $arr["obj_inst"];

		$r = &$arr["request"];

		$odl_prms = $this->cv_search_filter($o, $r);

		$odl = new object_data_list(
			$odl_prms,
			array(
				CL_CRM_PERSON => array(
					"oid" => "oid",
					"name" => "name",
					"gender" => "gender",
					"birthday" => "birthday",
					"modified" => "modified",
					"cvactive" => "cvactive",
					"cvapproved" => "cvapproved",
				),
			)
		);
		$ret = $odl->arr();
		return $ret;
	}

	function _get_search_conf_tbl($arr)
	{
		$conf = $arr["obj_inst"]->meta("search_conf_tbl");
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "table",
			"caption" => t("Tabel"),
			"sortable" => 1,
		));
		$vars = $this->search_vars;
		foreach($vars as $name => $caption)
		{
			$t->define_field(array(
				"name" => $name,
				"caption" => $caption,
			));
		}

		// Tables the configuration applies for
		$tables = array(
			"candidate" => t("Kandideerijate otsingu tulemused"),
			"employee" => t("T&ouml;&ouml;otsijate otsingu tulemused"),
		);

		foreach($tables as $id => $caption)
		{
			$data = array("table" => $caption);
			foreach($vars as $name => $_caption)
			{
				$data[$name] = html::hidden(array(
						"name" => "search_conf_tbl[".$id."][".$name."][caption]",
						"value" => $_caption,
					)).
				html::checkbox(array(
					"name" => "search_conf_tbl[".$id."][".$name."][disabled]",
					"value" => 1,
					"checked" => $conf[$id][$name]["disabled"] == 1 ? false : true,
				));
			}
			$t->define_data($data);
		}
	}

	function employee_list_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_item(0, array(
			"id" => 3,
			"name" => t("Element"),
		));
	}

	function candidate_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$objs = new object_tree(array(
			"lang_id" => array(),
			"site_id" => array(),
			"parent" => $this->offers_fld,
			"class_id" => array(CL_MENU, CL_PERSONNEL_MANAGEMENT_JOB_OFFER),
			"sort_by" => "objects.class_id, objects.jrk, objects.name",
			"status" => object::STAT_ACTIVE,
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
							"archive" => 0,
						),
					)),
					"class_id" => CL_MENU,
				)
			)),
		));
		$candidates_by_joboffers = $this->get_candidates_by_job_offer();
		$obx = $objs->to_list()->arr();
		// First we'll run through the job offer objs.
		foreach($obx as $ob)
		{
			if($ob->class_id() != CL_PERSONNEL_MANAGEMENT_JOB_OFFER)
			{
				continue;
			}
			if(!$this->check_special_acl_for_obj($ob, $arr["obj_inst"]))
			{
				continue;
			}
			$cnt_cands = count($candidates_by_joboffers[$ob->id()]);
			if($cnt_cands == 0)
			{
				continue;
			}
			$str = " (".$cnt_cands.")";
			$t->add_item($ob->parent(), array(
				"id" => $ob->id(),
				"name" => $arr["request"]["branch_id"] == $ob->id() ? "<b>".$ob->name().$str."</b>" : $ob->name().$str,
				"url" => $this->special_url($arr, array(
					"branch_id" => $ob->id(),
					"ofr_id" => $ob->id(),
				)),
				"iconurl" => icons::get_icon_url($ob->class_id()),
			));
			$cnt[$ob->parent()] += $cnt_cands;
			$total += $cnt_cands;
		}
		// Now we'll run through the dirs.
		foreach($obx as $ob)
		{
			if($ob->class_id() == CL_PERSONNEL_MANAGEMENT_JOB_OFFER || !$cnt[$ob->id()])
			{
				continue;
			}
			if(!$this->check_special_acl_for_cat($ob, CL_PERSONNEL_MANAGEMENT_JOB_OFFER))
			{
				continue;
			}
			$str = " (".$cnt[$ob->id()].")";
			$t->add_item($ob->parent(), array(
				"id" => $ob->id(),
				"name" => $arr["request"]["branch_id"] == $ob->id() ? "<b>".$ob->name().$str."</b>" : $ob->name().$str,
				"url" => $this->special_url($arr, array(
					"branch_id" => $ob->id(),
				)),
			));
		}
		$t->add_item(0, array(
			"id" => $this->offers_fld,
			"name" => $arr["request"]["branch_id"] == $this->offers_fld ? "<b>".t("Aktiivsed t&ouml;&ouml;pakkumised")." (".$total.")</b>" : t("Aktiivsed t&ouml;&ouml;pakkumised")." (".$total.")",
			"url" => $this->special_url($arr, array(
				"branch_id" => $this->offers_fld,
			)),
		));
		// ASUKOHT
		$mcaps = array(
			"area" => t("Piirkonnad"),
			"county" => t("Maakonnad"),
			"city" => t("Linnad"),
		);
		$clids = array(
			"county" => CL_CRM_COUNTY,
			"city" => CL_CRM_CITY,
			"area" => CL_CRM_AREA,
		);
		$total = 0;
		foreach($mcaps as $k => $d)
		{
			$objs = new object_list(array(
				"parent" => array(),
				"class_id" => $clids[$k],
				"lang_id" => array(),
				"site_id" => array(),
			));
			$tot = 0;
			foreach($objs->arr() as $o)
			{
				if(!$this->check_special_acl_for_cat($o, CL_PERSONNEL_MANAGEMENT_JOB_OFFER))
				{
					continue;
				}
				enter_function("kaarel");
				$ofrs = $o->get_job_offers(array(
					"parent" => $this->offers_fld,
					"status" => object::STAT_ACTIVE,
					"props" => array(
						"archive" => 0,
					),
				));
				exit_function("kaarel");
				$cnt = 0;
				foreach($ofrs->arr() as $ofr)
				{
					$cnt_cands = count($candidates_by_joboffers[$ofr->id()]);
					if($cnt_cands == 0)
					{
						continue;
					}
					$str = " (".$cnt_cands.")";
					$t->add_item($o->id(),array(
						"id" => $o->id()."_".$ofr->id(),
						"name" => $arr["request"]["branch_id"] == $ofr->id() ? "<b>".$ofr->name().$str."</b>" : $ofr->name().$str,
						"url" => $this->special_url($arr, array(
							"branch_id" => $ofr->id(),
							"ofr_id" => $ofr->id(),
						)),
						"iconurl" => icons::get_icon_url($o->class_id()),
					));
					//$cnt += $cnt_cands;
					$cnt++;
				}
				if($cnt == 0)
				{
					continue;
				}
				$str = " (".$cnt.")";
				$t->add_item($k, array(
					"id" => $o->id(),
					"name" => $arr["request"]["branch_id"] == $o->id() ? "<b>".$o->name().$str."</b>" : $o->name().$str,
					"url" => $this->mk_my_orb("change", array("id" => $arr["request"]["id"], "group" => $arr["request"]["group"], "branch_id" => $o->id(), $k."_id" => $o->id())),
				));
				$tot += $cnt;
			}
			if($tot > 0)
			{
				$str = " (".$tot.")";
				$t->add_item("location", array(
					"id" => $k,
					"name" => $arr["request"]["branch_id"] == $k ? "<b>".$d.$str."</b>" : $d.$str,
					"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => $k)),
				));
			}
			$total += $tot;
		}
		// OVERALL
		if($total > 0)
		{
			$t->add_item(0, array(
				"id" => "location",
				"name" => $arr["request"]["branch_id"] == "location" ? "<b>".t("Asukoht")."</b>" : t("Asukoht"),
				"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => "location")),
			));
		}
	}

	function _get_offers_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$objs = new object_tree(array(
			"lang_id" => array(),
			"parent" => $this->offers_fld,
			"class_id" => CL_MENU,
			"sort_by" => "objects.jrk, objects.name",
		));
		$obj = obj($this->offers_fld);
		$childs = new object_list(array(
			"lang_id" => array(),
			"parent" => $this->offers_fld,
			"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
			"archive" => $arr["request"]["group"] != "offers_archive" ? 0 : 1,
		));
		$cnt = $childs->count();
		$str = $cnt > 0 ? " ($cnt)" : "";
		$obx = $objs->to_list();
		if($this->check_special_acl_for_cat($this->offers_fld, CL_PERSONNEL_MANAGEMENT_JOB_OFFER))
		{
			$t->add_item(0, array(
				"id" => $this->offers_fld,
				"name" => $arr["request"]["branch_id"] == $this->offers_fld ? "<b>".$obj->name().$str."</b>" : $obj->name().$str,
				"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => $this->offers_fld)),
			));
		}
		foreach($obx->arr() as $ob)
		{
			$id = $ob->id();
			if(!$this->check_special_acl_for_cat($id, CL_PERSONNEL_MANAGEMENT_JOB_OFFER))
			{
				continue;
			}
			$childs = new object_list(array(
				"lang_id" => array(),
				"parent" => $id,
				"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
				"archive" => $arr["request"]["group"] != "offers_archive" ? 0 : 1,
			));
			$cnt = $childs->count();
			$str = $cnt > 0 ? " ($cnt)" : "";
			$t->add_item($ob->parent(), array(
				"id" => $id,
				"name" => $arr["request"]["branch_id"] == $id ? "<b>".$ob->name().$str."</b>" : $ob->name().$str,
				"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => $id)),
			));
		}
		// KEHTIVAD
		if($arr["request"]["group"] != "offers_archive")
		{
			$valid_cnt = count($this->get_valid_job_offers(array(
				"parent" => array_merge(array($this->offers_fld), $obx->ids()),
			))->arr());
			if($valid_cnt > 0)
			{
				$caption = sprintf(t("Kehtivad (%s)"), $valid_cnt);
				$t->add_item(0, array(
					"id" => "valid",
					"name" => $arr["request"]["branch_id"] == "valid" ? "<b>".$caption."</b>" : $caption,
					"url" => aw_url_change_var("branch_id", "valid"),
				));
			}
		}

		// VALDKONNAD
		$field_cnt = 0;
		$odl = new object_data_list(
			array(
				"class_id" => CL_META,
				"lang_id" => array(),
				"site_id" => array(),
				"CL_META.RELTYPE_FIELD(CL_PERSONNEL_MANAGEMENT_JOB_OFFER).parent" => array_merge(array($this->offers_fld), $obx->ids()),
				"CL_META.RELTYPE_FIELD(CL_PERSONNEL_MANAGEMENT_JOB_OFFER).archive" => $arr["request"]["group"] != "offers_archive" ? new obj_predicate_not(1) : 1,
			),
			array(
				CL_META => array("name"),
			)
		);
		unset($first_oid);
		foreach($odl->arr() as $id => $o)
		{
			$first_oid = isset($first_oid) ? $first_oid : $id;
			$t->add_item("field", array(
				"id" => $id,
				"name" => $arr["request"]["branch_id"] == $id ? "<b>".$o["name"]."</b>" : $o["name"],
				"url" => aw_url_change_var("branch_id", $id),
			));
			$field_cnt++;
		}
		if($field_cnt > 0)
		{
			$caption = sprintf(t("Valdkonnad (%s)"), $field_cnt);
			$t->add_item(0, array(
				"id" => "field",
				"name" => $arr["request"]["branch_id"] == "field" ? "<b>".$caption."</b>" : $caption,
				"url" => aw_url_change_var("branch_id", $first_oid),
			));
		}

		// ASUKOHT
		$mcaps = array(
			"area" => t("Piirkonnad"),
			"county" => t("Maakonnad"),
			"city" => t("Linnad"),
		);
		$clids = array(
			"county" => CL_CRM_COUNTY,
			"city" => CL_CRM_CITY,
			"area" => CL_CRM_AREA,
		);
		$total = 0;
		foreach($mcaps as $k => $d)
		{
			$objs = new object_list(array(
				"parent" => array(),
				"class_id" => $clids[$k],
				"lang_id" => array(),
				"site_id" => array(),
			));
			$tot = 0;
			foreach($objs->arr() as $o)
			{
				if(!$this->check_special_acl_for_cat($o, CL_PERSONNEL_MANAGEMENT_JOB_OFFER))
				{
					continue;
				}
				$cnt = $o->get_job_offers(array(
					"parent" => $this->offers_fld,
					"props" => array(
						"archive" => $arr["request"]["group"] != "offers_archive" ? 0 : 1,
					),
				))->count();
				if($cnt == 0)
				{
					continue;
				}
				$str = " (".$cnt.")";
				$t->add_item($k, array(
					"id" => $o->id(),
					"name" => $arr["request"]["branch_id"] == $o->id() ? "<b>".$o->name().$str."</b>" : $o->name().$str,
					"url" => $this->mk_my_orb("change", array("id" => $arr["request"]["id"], "group" => $arr["request"]["group"], "branch_id" => $o->id(), $k."_id" => $o->id())),
				));
				$tot += $cnt;
			}
			if($tot > 0)
			{
				$str = " (".$tot.")";
				$t->add_item("location", array(
					"id" => $k,
					"name" => $arr["request"]["branch_id"] == $k ? "<b>".$d.$str."</b>" : $d.$str,
					"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => $k)),
				));
			}
			$total += $tot;
		}
		// OVERALL
		if($total > 0)
		{
			$t->add_item(0, array(
				"id" => "location",
				"name" => $arr["request"]["branch_id"] == "location" ? "<b>".t("Asukoht")."</b>" : t("Asukoht"),
				"url" => $this->mk_my_orb("change", array("id" => $arr["obj_inst"]->id(), "group" => $arr["request"]["group"], "branch_id" => "location")),
			));
		}
	}

	function _get_employee_list_toolbar($arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "add",
			"caption" => t("Lisa"),
			"img" => "new.gif",
		));
	}

	function candidate_toolbar($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "add",
			"caption" => t("Lisa"),
			"img" => "new.gif",
			"url" => $this->mk_my_orb("new", array("ofr_id" => $arr["request"]["ofr_id"], "parent" => $this->persons_fld, "return_url" => get_ru()), CL_CRM_PERSON),
		));
		$t->add_search_button(array(
			"pn" => add_employee,
			"multiple" => 1,
			"clid" => CL_CRM_PERSON,
		));
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta kandidaadid"),
			"action" => "delete_cands",
		));
	}

	function _get_offers_toolbar($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "add",
			"tooltip" => t("Uus"),
		));
		$pt = is_oid($arr["request"]["branch_id"]) && obj($arr["request"]["branch_id"])->class_id() == CL_MENU ? $arr["request"]["branch_id"] : $this->offers_fld;
		$tb->add_menu_item(array(
			"parent" => "add",
			"text" => t("T&ouml;&ouml;pakkumine"),
			"link" => html::get_new_url(CL_PERSONNEL_MANAGEMENT_JOB_OFFER, $pt, array(
				"return_url" => get_ru(),
				"personnel_management_id" => $arr["obj_inst"]->id(),
				"county_id" => $arr["request"]["county_id"],
			)),
			"href_id" => "add_bug_href"
		));
		$tb->add_menu_item(array(
			"parent" => "add",
			"text" => t("Kaust"),
			"link" => html::get_new_url(CL_MENU, $pt, array("return_url" => get_ru())),
		));
		$tb->add_button(array(
			"name" => "cut",
			"caption" => t("L&otilde;ika"),
			"img" => "cut.gif",
			"action" => "cut_offers",
		));
		if (count($_SESSION["aw_jobs"]["cut_offers"]))
		{
			$tb->add_button(array(
				"name" => "paste",
				"caption" => t("Kleebi"),
				"img" => "paste.gif",
				"action" => "paste_offers",
			));
		}
		$tb->add_button(array(
			"name" => "save",
			"caption" => t("Salvesta"),
			"tooltip" => t("Salvesta"),
			"img" => "save.gif",
			"action" => "save_offers",
		));
		$tb->add_delete_button();
		$tb->add_button(array(
			"name" => "archive",
			"caption" => $arr["request"]["group"] != "offers_archive" ? t("Arhiveeri") : t("Dearhiveeri"),
			"tooltip" => $arr["request"]["group"] != "offers_archive" ? t("Arhiveeri t&ouml;&ouml;pakkumised") : t("Dearhiveeri t&ouml;&ouml;pakkumised"),
			"img" => "archive_small.gif",
			"action" => "archive",
		));

		$tb->add_button(array(
			'name' => 'csv',
			'img' => 'ftype_xls.gif',
			'tooltip' => t('CSV'),
			"url" => aw_url_change_var("get_csv_file", 1)
		));
	}

	function _get_employee_list_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "offer",
			"caption" => t("T&ouml;&ouml;pakkumine"),
		));

		$ol = new object_list(array(
			"class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE,
			"lang_id" => array(),
		));
		foreach($ol->arr() as $cand)
		{
			$offer = obj($cand->parent());
			$t->define_data(array(
				"name" => html::obj_change_url($cand),
				"offer" => html::obj_change_url($offer)
			));
		}

	}

	function candidate_table_flds($arr)
	{
		if($arr["request"]["branch_id"])
		{
			if(!is_oid($arr["request"]["branch_id"]))
			{
				return $this->table_flds($arr, "get_job_offers", "offers_fld");
			}

			$t = $arr["prop"]["vcl_inst"];
			$t->define_field(array(
				"name" => "icon",
				"align" => "center",
				"width" => "40",
			));
			$t->define_field(array(
				"name" => "name",
				"caption" => t("Nimi"),
				"align" => "left",
			));

			$oid = is_oid($arr["request"]["county_id"]) ? $arr["request"]["county_id"] : (is_oid($arr["request"]["city_id"]) ? $arr["request"]["city_id"] : $arr["request"]["area_id"]);

			if(is_oid($oid))
			// It's by location.
			{
				$o = obj($oid);
				$objs = $o->get_job_offers(array(
					"parent" => $this->offers_fld,
				));
				$obx = $objs->arr();
			}
			else
			{
				$objs = new object_tree(array(
					"lang_id" => array(),
					"site_id" => array(),
					"parent" => $arr["request"]["branch_id"],
					"class_id" => array(CL_MENU, CL_PERSONNEL_MANAGEMENT_JOB_OFFER),
					"sort_by" => "objects.class_id, objects.name",
					"status" => object::STAT_ACTIVE,
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							new object_list_filter(array(
								"logic" => "AND",
								"conditions" => array(
									"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
									"archive" => 0,
								),
							)),
							"class_id" => CL_MENU,
						)
					)),
				));
				$obx = $objs->to_list()->arr();
			}
			$candidates_by_joboffers = $this->get_candidates_by_job_offer();
			// First we'll run through the job offer objs.
			foreach($obx as $ob)
			{
				if($ob->class_id() != CL_PERSONNEL_MANAGEMENT_JOB_OFFER)
				{
					continue;
				}
				if(!$this->check_special_acl_for_obj($ob, $arr["obj_inst"]))
				{
					continue;
				}
				$cnt_cands = count($candidates_by_joboffers[$ob->id()]);
				$cnt[$ob->parent()] += $cnt_cands;
				$total += $cnt_cands;
				if($cnt_cands == 0 || ($ob->parent() != $arr["request"]["branch_id"] && !is_oid($oid)))
				{
					continue;
				}
				$str = " (".$cnt_cands.")";
				$t->define_data(array(
					"name" => html::href(array(
						"url" => $this->special_url($arr, array(
							"branch_id" => $ob->id(),
							"ofr_id" => $ob->id(),
						)),
						"caption" => $ob->name().$str,
					)),
					"icon" => html::img(array(
						"url" => icons::get_icon_url($ob->class_id()),
					)),
					"class_id" => $ob->class_id(),
				));
			}
			// Now we'll run through the dirs if there are any.
			foreach($obx as $ob)
			{
				if($ob->class_id() == CL_PERSONNEL_MANAGEMENT_JOB_OFFER || !$cnt[$ob->id()] || ($ob->parent() != $arr["request"]["branch_id"] && !is_oid($oid)))
				{
					continue;
				}
				if(!$this->check_special_acl_for_cat($ob, CL_PERSONNEL_MANAGEMENT_JOB_OFFER))
				{
					continue;
				}
				$str = " (".$cnt[$ob->id()].")";
				$t->define_data(array(
					"name" => html::href(array(
						"url" => $this->special_url($arr, array(
							"branch_id" => $ob->id(),
						)),
						"caption" => $ob->name().$str,
					)),
					"icon" => html::img(array(
						"url" => icons::get_icon_url($ob->class_id()),
					)),
					"class_id" => $ob->class_id(),
				));
			}
			$t->sort_by(array(
				"field" => "class_id",
				"sorder" => "desc",
			));
		}
		else
		if($arr["request"]["cv_search_button"] || $arr["request"]["cv_search_button_save_search"] || $this->can("view", $arr["request"]["search_save"]))
		{
			$t = $arr["prop"]["vcl_inst"];
			$vars = $this->search_vars;
			$gender = array(1 => t("mees"), t("naine"));
			$conf = $arr["obj_inst"]->meta("search_conf_tbl");
			$conf = $conf["employee"];
			$t->define_chooser(array(
				"field" => "id",
				"name" => "sel",
			));
			foreach($vars as $name => $caption)
			{
				if(!$conf[$name]["disabled"])
				{
					$t->define_field(array(
						"name" => $name,
						"caption" => $caption,
						"align" => "center",
						"sortable" => 1,
					));
				}
			}

			if($this->can("view", $arr["request"]["search_save"]))
			{
				$sso = obj($arr["request"]["search_save"]);
				$arr["request"] += $sso->meta();
			}
			$needed_acl = obj($this->get_sysdefault())->needed_acl_employee;
			$res = $this->search_employee($arr);
			$perpage = $arr["obj_inst"]->prop("perpage");
			if(count($res) > $perpage)
			{
				$t->define_pageselector(array(
					"type" => "lbtxt",
					"records_per_page" => $perpage,
				));
			}
			foreach($res as $person)
			{
				$acl_ok = true;
				foreach($needed_acl as $acl)
				{
					$acl_ok = $acl_ok && $this->can($acl, $person["oid"]);
				}
				if(!$acl_ok)
				{
					continue;
				}
				$apps = "";
				$obj = obj($person["oid"]);
				foreach($obj->get_applications(array("parent" => $this->offers_fld, "status" => object::STAT_ACTIVE))->names() as $app_id => $app_name)
				{
					$apps .= (strlen($apps) > 0) ? ", " : "";
					$apps .= html::href(array(
						"caption" => $app_name,
						"url" => $this->mk_my_orb("change", array("id" => $app_id, "return_url" => get_ru()), CL_PERSONNEL_MANAGEMENT_JOB_OFFER),
					));
				}
				// We only display persons that have active applications.
				if(empty($apps))
					continue;

				$t->define_data(array(
					"id" => $person["oid"],
					"name" => html::href(array(
						"url" => $this->mk_my_orb("show_cv", array("id" => $obj->id(), "cv" => "cv/".basename($arr["obj_inst"]->prop("cv_tpl")), "die" => "1"), CL_CRM_PERSON),
						"caption" => $person["name"],
					)),
					"age" => $obj->get_age(),
					"gender" => $gender[$person["gender"]],
					"apps" => $apps,
					"modtime" => date("Y-m-d H:i:s", $person["modified"]),
					"change" => html::get_change_url($person["oid"], array("return_url" => get_ru()), t("Muuda")),
				));
			}
		}
	}

	function candidate_table($arr)
	{
		if(!is_oid($arr["request"]["ofr_id"]))
		{
			return $this->candidate_table_flds($arr);
		}
		$t = $arr["prop"]["vcl_inst"];
		$vars = $this->search_vars;
		$gender = array(1 => t("mees"), t("naine"), "" => t("m&auml;&auml;ramata"));
		$conf = $arr["obj_inst"]->meta("search_conf_tbl");
		$conf = $conf["candidate"];
		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
		foreach($vars as $name => $caption)
		{
			if($name === "modtime")
			{
				// Have to change it here, cuz we can't use the same caption for employee table.
				$caption = t("Kandideerimise aeg");
			}
			if(!$conf[$name]["disabled"])
			{
				$t->define_field(array(
					"name" => $name,
					"caption" => $caption,
					"align" => "center",
					"sortable" => 1,
				));
			}
		}
		if($this->can("view", $arr["request"]["ofr_id"]))
		{
			$job = obj($arr["request"]["ofr_id"]);
			$objs = $job->get_candidates(array(
				"status" => object::STAT_ACTIVE,
			));
			foreach($objs->arr() as $obj)
			{
				unset($apps);
				foreach($obj->get_applications(array("parent" => $this->offers_fld, "status" => object::STAT_ACTIVE))->names() as $app_id => $app_name)
				{
					$apps .= (strlen($apps) > 0) ? ", " : "";
					$apps .= html::href(array(
						"caption" => $app_name,
						"url" => $this->mk_my_orb("change", array("id" => $app_id, "return_url" => get_ru()), CL_PERSONNEL_MANAGEMENT_JOB_OFFER),
					));
				}
				$t->define_data(array(
					"id" => $obj->id(),
					"name" => html::href(array(
						"url" => $this->mk_my_orb("show_cv", array("id" => $obj->id(), "cv" => "cv/".basename($arr["obj_inst"]->prop("cv_tpl")), "die" => "1", "job_offer" => $arr["request"]["ofr_id"]), CL_CRM_PERSON),
						"caption" => $obj->name(),
					)),
					"age" => $obj->get_age(),
					"gender" => $gender[$obj->prop("gender")],
					"apps" => $apps,
					"modtime" => date("Y-m-d H:i:s", $obj->prop("modified")),
					"change" => html::get_change_url($obj->id(), array("return_url" => get_ru()), t("Muuda")),
				));
			}
		}
		$t->set_default_sortby("modtime");
		$t->set_default_sorder("desc");
		$t->sort_by();
	}

	function _init_offers_table($t)
	{
		$props = get_instance(CL_CFGFORM)->get_default_proplist(array("clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "profession",
			"caption" => t("Ametikoht"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "org",
			"caption" => t("Organisatsioon"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "location",
			"caption" => t("Asukoht"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "field",
			"caption" => t("Valdkond"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "salary",
			"caption" => t("T&ouml;&ouml;tasu"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "job_type",
			"caption" => t("Positsioon"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "load",
			"caption" => t("T&ouml;&ouml;koormus"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("T&auml;htaeg"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "preview",
			"caption" => t("Eelvaade"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "file",
			"caption" => t("Fail"),
		));
		$t->define_field(array(
			"name" => "udef_classificator_1",
			"caption" => $props["udef_classificator_1"]["caption"],
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Aktiivne"),
			"sortable" => 1,
		));
		$t->define_chooser(array(
			"field" => "id",
			"name" => "sel",
		));
	}

	function offers_table($arr)
	{
		if(!is_oid($arr["request"]["branch_id"]) && $arr["request"]["branch_id"] && $arr["request"]["branch_id"] != "valid" && !$arr["request"]["os_sbt"])
		{
			return $this->table_flds($arr, "get_job_offers", "offers_fld");
		}

		$t = $arr["prop"]["vcl_inst"];
		$this->_init_offers_table($t);

		if($arr["request"]["os_sbt"])
		{
			$objs = $this->get_offers_srch_offers($arr);
		}
		else
		{
			$objs = $this->get_offers_table_offers($arr);
		}

		if($objs->count() > 0)
		{
			$udc1_conns = connection::find(array(
				"from" => $objs->ids(),
				"from.class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
				"type" => "RELTYPE_UDEF_CLASSIFICATOR_1",
			));
			foreach($udc1_conns as $udc1_conn)
			{
				$udef_classificator_1[$udc1_conn["from"]] .= strlen($udef_classificator_1[$udc1_conn["from"]]) > 0 ? ", " : "";
				$udef_classificator_1[$udc1_conn["from"]] .= $udc1_conn["to.name"];
			}
		}

		foreach ($objs->arr() as $obj)
		{
			if($obj->endless)
			{
				$end = t("T&auml;htajatu");
			}
			else
			if($obj->prop("end"))
			{
        		$end = get_lc_date($obj->prop("end"));
        	}
        	else
        	{
        		$end = t("M&auml;&auml;ramata");
        	}

			// Location
			$loc = $obj->prop("loc_area.name");
			if(strlen($loc) > 0 && strlen($obj->prop("loc_county.name")) > 0)
			{
				$loc .= ", ";
			}
			$loc .= $obj->prop("loc_county.name");
			if(strlen($loc) > 0 && strlen($obj->prop("loc_city.name")) > 0)
			{
				$loc .= ", ";
			}
			$loc .= $obj->prop("loc_city.name");

			$t->define_data(array(
				"id" => $obj->id(),
				"name" => html::get_change_url($obj->id(), array("return_url" => get_ru()), parse_obj_name($obj->name())),
				"profession" => $obj->prop("profession.name"),
				"org" => html::get_change_url($obj->prop("company"), array("return_url" => get_ru()), $obj->prop("company.name")),
				"location" => $loc,
				"end" => $end,
				"created" => $obj->created(),
				"status" => html::hidden(array(
					"name" => "old[status][".$obj->id()."]",
					"value" => $obj->status() == STAT_ACTIVE ? 2 : 1,
				)).html::checkbox(array(
					"name" => "new[status][".$obj->id()."]",
					"value" => 2,
					"checked" => $obj->status() == STAT_ACTIVE ? true : false,
				)),
				"field" => $obj->prop("field.name"),
				"preview" => html::href(array(
					"url" => obj_link($obj->id()),
					"target" => "_blank",
					"caption" => html::img(array(
						"url" => aw_ini_get("baseurl")."/automatweb/images/icons/preview.gif",
						"border" => 0,
					)),
				)),
				"file" => html::href(array(
					"url" => $this->mk_my_orb("gen_job_pdf", array("id" => $obj->id()), CL_PERSONNEL_MANAGEMENT_JOB_OFFER),
					"target" => "_blank",
					"caption" => html::img(array(
						"url" => aw_ini_get("baseurl")."/automatweb/images/icons/ftype_pdf.gif",
						"border" => 0,
					)),
				)),
				"udef_classificator_1" => $udef_classificator_1[$obj->id()],
				"salary" => $obj->salary,
				"load" => $obj->prop("load.name"),
				"job_type" => $obj->prop("job_type.name"),
			));
		}
		$t->set_default_sortby("created");
		$t->set_default_sorder("desc");
		$t->sort_by();
		if($_GET["get_csv_file"] == 1)
		{
			header('Content-type: application/octet-stream');
			header('Content-disposition: root_access; filename="csv_output.csv"');
			die($t->get_csv_file());
		}
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "notify_loc_tbl":
				$arr["obj_inst"]->set_meta("notify_loc_tbl", $prop["value"]);
				break;

			case "cv_search_button_save_search":
			case "cv_search_button_save":
				if(strlen($arr["request"]["cv_search_saved_name"]) > 0)
				{
					$this->cv_save_search($arr);
				}
				break;

			case "add_employee":
				$this->_set_add_empolyee($arr);
				break;

			case "vars_tbl":
				$this->_set_vars_tbl($arr);
				break;

			case "sysdefault_pm":
				$ol = new object_list(array(
					"class_id" => $this->clid,
					"lang_id" => array(),
				));
				foreach ($ol->arr() as $item)
				{
					if ($item->flag(OBJ_FLAG_IS_SELECTED) && $item->id() != $arr["obj_inst"]->id() || $prop["value"] != $prop["ch_value"])
					{
						$item->set_flag(OBJ_FLAG_IS_SELECTED, false);
						$item->save();
					}
					elseif ($item->id() == $arr["obj_inst"]->id() && !$item->flag(OBJ_FLAG_IS_SELECTED) && $prop["value"] == $prop["ch_value"])
					{
						$item->set_flag(OBJ_FLAG_IS_SELECTED, true);
						$item->save();
					};
				};
				break;

			case "search_conf_tbl":
				foreach($prop["value"] as $id => $data)
				{
					foreach($data as $name => $v)
					{
						$meta[$id][$name]["disabled"] = 1 - $v["disabled"];
					}
				}
				$arr["obj_inst"]->set_meta("search_conf_tbl", $meta);
				break;

			case "offers_table":
				$this->save_offers($arr["request"]);
				break;
		}

		return $retval;
	}

	public function _set_vars_tbl($arr)
	{
		switch($arr["request"]["branch_id"])
		{
			case "lang":
				$arr["obj_inst"]->set_meta("lang_tbl", $arr["request"]["lang_tbl"]);
				$arr["obj_inst"]->set_meta("rec_lang_tbl", $arr["request"]["rec_lang_tbl"]);
				break;

			case "load":
			case "job_offer_type":
				foreach($arr["request"]["vars_tbl"] as $k => $v)
				{
					if($v["jrk"] != $v["jrk_old"])
					{
						$o = obj($k);
						$o->set_ord($v["jrk"]);
						$o->save();
					}
				}
				break;
		}
	}

	public function _set_add_empolyee($arr)
	{
		if($arr["request"]["group"] == "employee")
		{
			$ps = explode(",", $arr["prop"]["value"]);
			foreach($ps as $p)
			{
				$po = obj($p);
				if($po->parent() != $this->persons_fld)
				{
					$po->connect(array(
						"to" => $arr["obj_inst"]->id(),
						"reltype" => "RELTYPE_PERSONNEL_MANAGEMENT",
					));
				}
			}
		}
		if($arr["request"]["group"] == "candidate" && $this->can("view", $arr["request"]["ofr_id"]))
		{
			$o = obj($arr["request"]["ofr_id"]);
			$ids = $o->get_candidates()->ids();
			$ps = explode(",", $arr["prop"]["value"]);
			foreach($ps as $p)
			{
				if($this->can("view", $p))
				{
					$p = obj($p);
					if(!in_array($p->id(), $ids))
					{
						$c = new object;
						$c->set_class_id(CL_PERSONNEL_MANAGEMENT_CANDIDATE);
						$c->set_status(object::STAT_ACTIVE);
						$c->set_parent($o->id());
						$c->set_name($p->name()." kandidatuur kohale ".$o->name());
						$c->set_prop("person", $p->id());
						$c->save();

						// Job offer to candidate.
						$o->connect(array(
							"to" => $c->id(),
							"reltype" => "RELTYPE_CANDIDATE",
						));
						// Candidate to person.
						$c->connect(array(
							"to" => $p->id(),
							"reltype" => "RELTYPE_PERSON",
						));
					}
				}
			}
		}
	}

	public function parse_alias($arr = array())
	{
		$obj = obj($arr["id"]);
		$this->read_template("show.tpl");
		$objs = new object_list(array(
			"lang_id" => array(),
			"parent" => $obj->prop("offers_fld"),
			"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
		));
		$offers = "";
		foreach($objs->arr() as $ob)
		{
			$this->vars(array(
				"profession" => html::href(array(
					"url" => obj_link($ob->prop("profession")),
					"caption" => $ob->prop("profession.name"),
				)),
				"company" => $ob->prop("company.name"),
				"location" => $ob->prop("location.name"),
				"field" => $ob->prop("field.name"),
				"end" => get_lc_date($ob->prop("end")),
			));
			$offers .= $this->parse("OFFER");
		}
		$this->vars(array(
			"count" => $objs->count(),
			"OFFER" => $offers,
		));
		return $this->parse();
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["branch_id"] = $_GET["branch_id"];
		$arr["ofr_id"] = $_GET["ofr_id"];
		$arr["list_id"] = 0;
		$arr["cv_search_saved_name"] = "";
		$arr["branch_id"] = $_GET["branch_id"];
		$arr["email_to_send_cv_to"] = "";
	}

	/**
		@attrib name=cut_offers
	**/
	function cut_offers($arr)
	{
		$_SESSION["aw_jobs"]["cut_offers"] = $arr["sel"];
		return $arr["post_ru"];
	}

	/**
		@attrib name=paste_offers
	**/
	function paste_offers($arr)
	{
		foreach(safe_array($_SESSION["aw_jobs"]["cut_offers"]) as $ofid)
		{
			$ofo = obj($ofid);
			$ofo->set_parent($arr["branch_id"]);
			$ofo->save();
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=save_offers
	**/
	function save_offers($arr)
	{
		$all_trans_status = $arr["all_trans_status"];
		$langs = aw_ini_get("languages");

		foreach($arr["old"]["status"] as $oid => $old_status)
		{
			if($arr["new"]["status"][$oid] != $old_status)
			{
				$o = obj($oid);
				$o->set_prop("status", ($arr["new"]["status"][$oid] == 2 ? object::STAT_ACTIVE : object::STAT_NOTACTIVE));
				if($all_trans_status != 0)
				{
					foreach(array_keys($langs["list"]) as $lid)
					{
						$o->set_meta("trans_".$lid."_status", 2 - $all_trans_status);
					}
				}
				$o->save();
			}
		}
		return $arr["post_ru"];
	}

	function callback_mod_retval($arr)
	{
		if($arr["request"]["search_save"])
		{
			$arr["args"]["search_save"] = $arr["request"]["search_save"];
		}

		if($arr["request"]["cs_sbt"])
		{
			$arr['args']['cs_n'] = ($arr['request']['cs_n']);
			$arr['args']['customer_search_worker'] = ($arr['request']['customer_search_worker']);
			$arr['args']['customer_search_ev'] = ($arr['request']['customer_search_ev']);
			$arr['args']['customer_search_cust_mgr'] = ($arr['request']['customer_search_cust_mgr']);
			$arr['args']['customer_rel_creator'] = ($arr['request']['customer_rel_creator']);
			$arr['args']['customer_search_cust_grp'] = ($arr['request']['customer_search_cust_grp']);
			$arr['args']['customer_search_insurance_exp'] = ($arr['request']['customer_search_insurance_exp']);
			$arr['args']['customer_search_reg'] = ($arr['request']['customer_search_reg']);
			$arr['args']['customer_search_address'] = ($arr['request']['customer_search_address']);
			$arr['args']['customer_search_city'] = ($arr['request']['customer_search_city']);
			$arr['args']['customer_search_county'] = ($arr['request']['customer_search_county']);
			$arr['args']['cs_sbt'] = $arr['request']['cs_sbt'];
			$arr['args']['customer_search_is_co'] = $arr['request']['customer_search_is_co'];
			$arr["args"]["customer_search_print_view"] = $arr["request"]["customer_search_print_view"];
			$arr["args"]["customer_search_keywords"] = $arr["request"]["customer_search_keywords"];
			$arr["args"]["customer_search_classif1"] = $arr["request"]["customer_search_classif1"];
		}

		if($arr["request"]["os_sbt"])
		{
			$arr["args"]["os_type"] = $arr["request"]["os_type"];
			$arr["args"]["os_pr"] = $arr["request"]["os_pr"];
			$arr["args"]["os_county"] = $arr["request"]["os_county"];
			$arr["args"]["os_city"] = $arr["request"]["os_city"];
			$arr["args"]["os_dl_from"] = $arr["request"]["os_dl_from"];
			$arr["args"]["os_dl_from_time"] = mktime(0, 0, 0, $arr["request"]["os_dl_from"]["month"], $arr["request"]["os_dl_from"]["day"], $arr["request"]["os_dl_from"]["year"]);
			$arr["args"]["os_dl_to"] = $arr["request"]["os_dl_to"];
			$arr["args"]["os_dl_to_time"] = mktime(0, 0, 0, $arr["request"]["os_dl_to"]["month"], $arr["request"]["os_dl_to"]["day"], $arr["request"]["os_dl_to"]["year"]);
			$arr["args"]["os_status"] = $arr["request"]["os_status"];
			$arr["args"]["os_confirmed"] = $arr["request"]["os_confirmed"];
			$arr["args"]["os_endless"] = $arr["request"]["os_endless"];
			$arr["args"]["os_employer"] = $arr["request"]["os_employer"];
			$arr["args"]["os_salary_from"] = $arr["request"]["os_salary_from"];
			$arr["args"]["os_salary_to"] = $arr["request"]["os_salary_to"];
			$arr["args"]["os_field"] = $arr["request"]["os_field"];
			$arr["args"]["os_jobtype"] = $arr["request"]["os_jobtype"];
			$arr["args"]["os_load"] = $arr["request"]["os_load"];
			$arr["args"]["os_workinfo"] = $arr["request"]["os_workinfo"];
			$arr["args"]["os_requirements"] = $arr["request"]["os_requirements"];
			$arr["args"]["os_info"] = $arr["request"]["os_info"];
			$arr["args"]["os_contact"] = $arr["request"]["os_contact"];
			$arr["args"]["os_sbt"] = $arr["request"]["os_sbt"];
		}

		if($arr["request"]["cv_search_button"] || $arr["request"]["cv_search_button_save_search"])
		{
			$arr["args"]["cv_search_button_save_search"] = $arr["request"]["cv_search_button_save_search"];
			$arr["args"]["cv_search_button"] = $arr["request"]["cv_search_button"];
			$arr["args"]["cv_name"] = $arr["request"]["cv_name"];
			$arr["args"]["cv_oid"] = $arr["request"]["cv_oid"];
			$arr["args"]["cv_company"] = $arr["request"]["cv_company"];
			$arr["args"]["cv_job"] = $arr["request"]["cv_job"];
			$arr["args"]["cv_paywish"] = $arr["request"]["cv_paywish"];
			$arr["args"]["cv_paywish2"] = $arr["request"]["cv_paywish2"];
			$arr["args"]["cv_field"] = $arr["request"]["cv_field"];
			$arr["args"]["cv_previous_field"] = $arr["request"]["cv_previous_field"];
			$arr["args"]["cv_type"] = $arr["request"]["cv_type"];
			$arr["args"]["cv_location"] = $arr["request"]["cv_location"];
			$arr["args"]["cv_load"] = $arr["request"]["cv_load"];
			$arr["args"]["cv_wrk_load"] = $arr["request"]["cv_wrk_load"];
			$arr["args"]["cv_personality"] = $arr["request"]["cv_personality"];
			$arr["args"]["cv_comments"] = $arr["request"]["cv_comments"];
			$arr["args"]["cv_recommenders"] = $arr["request"]["cv_recommenders"];
			$arr["args"]["cv_mother_tongue"] = $arr["request"]["cv_mother_tongue"];
			$arr["args"]["cv_lang_exp"] = $arr["request"]["cv_lang_exp"];
			$arr["args"]["cv_lang_exp_lvl"] = $arr["request"]["cv_lang_exp_lvl"];
			$arr["args"]["cv_exp"] = $arr["request"]["cv_exp"];
			$arr["args"]["cv_exp_lvl"] = $arr["request"]["cv_exp_lvl"];
			$arr["args"]["cv_gender"] = $arr["request"]["cv_gender"];
			$arr["args"]["cv_age_from"] = $arr["request"]["cv_age_from"];
			$arr["args"]["cv_bd_to"] = $arr["request"]["cv_bd_to"];
			$arr["args"]["cv_bd_from"] = $arr["request"]["cv_bd_from"];
			$arr["args"]["cv_mod_to"] = $arr["request"]["cv_mod_to"];
			$arr["args"]["cv_mod_from"] = $arr["request"]["cv_mod_from"];
			$arr["args"]["cv_age_to"] = $arr["request"]["cv_age_to"];
			$arr["args"]["cv_status"] = $arr["request"]["cv_status"];
			$arr["args"]["cv_approved"] = $arr["request"]["cv_approved"];
			$arr["args"]["cv_udef_chbox1"] = $arr["request"]["cv_udef_chbox1"];
			$arr["args"]["cv_udef_chbox2"] = $arr["request"]["cv_udef_chbox2"];
			$arr["args"]["cv_udef_chbox3"] = $arr["request"]["cv_udef_chbox3"];
			$arr["args"]["cv_previous_rank"] = $arr["request"]["cv_previous_rank"];
			$arr["args"]["cv_praxis"] = $arr["request"]["cv_praxis"];
			$arr["args"]["cv_driving_licence"] = $arr["request"]["cv_driving_licence"];
			$arr["args"]["cv_other_skills"] = $arr["request"]["cv_other_skills"];
			$arr["args"]["cv_tel"] = $arr["request"]["cv_tel"];
			$arr["args"]["cv_email"] = $arr["request"]["cv_email"];
			$arr["args"]["cv_city"] = $arr["request"]["cv_city"];
			$arr["args"]["cv_county"] = $arr["request"]["cv_county"];
			$arr["args"]["cv_area"] = $arr["request"]["cv_area"];
			$arr["args"]["cv_address"] = $arr["request"]["cv_address"];
			$arr["args"]["cv_addinfo"] = $arr["request"]["cv_addinfo"];
			$arr["args"]["cv_edulvl"] = $arr["request"]["cv_edulvl"];
			$arr["args"]["cv_edu_exact"] = $arr["request"]["cv_edu_exact"];
			$arr["args"]["cv_edulvl_in_eduobj"] = $arr["request"]["cv_edulvl_in_eduobj"];
			$arr["args"]["cv_acdeg"] = $arr["request"]["cv_acdeg"];
			$arr["args"]["cv_schl"] = $arr["request"]["cv_schl"];
			$arr["args"]["cv_faculty"] = $arr["request"]["cv_faculty"];
			$arr["args"]["cv_speciality"] = $arr["request"]["cv_speciality"];
			$arr["args"]["cv_schl_start_from"] = $arr["request"]["cv_schl_start_from"];
			$arr["args"]["cv_schl_start_to"] = $arr["request"]["cv_schl_start_to"];
			$arr["args"]["cv_schl_area"] = $arr["request"]["cv_schl_area"];
			$arr["args"]["cv_schl_stat"] = $arr["request"]["cv_schl_stat"];
		}
		if($arr["request"]["vs_name"])
		{
			$arr["args"]["vs_name"] = $arr["request"]["vs_name"];
		}
		if(isset($arr["request"]["branch_id"]) && $arr["request"]["group"] == "variables")
		{
			$arr["args"]["branch_id"] = $arr["request"]["branch_id"];
		}
	}

	/** Returns the oid of the site-wide default personnel management
		@attrib api=1 params=name

		@returns
			The oid of the system default personnel management for the class or false if no personnel management object exists.

		@examples
			$pm_inst = get_instance(CL_PERSONNEL_MANAGEMENT);
			if (($pm_oid = $pm_inst->get_sysdefault()) !== false)
			{
				print "default personnel management  is ".$pm_oid."<br>";
			}
	**/
	function get_sysdefault()
	{
		// 2 passes, because I need to know which element is active before
		// doing the table
		$active = false;
		$ol = new object_list(array(
			"class_id" => $this->clid,
			"flags" => array(
				"mask" => OBJ_FLAG_IS_SELECTED,
				"flags" => OBJ_FLAG_IS_SELECTED
			)
		));

		if (sizeof($ol->ids()) > 0)
		{
			$first = $ol->begin();
			$active = $first->id();
		}

		if($active)
		{
			return $active;
		}
		else
		{
			// If none of those is default, we return the first one
			$ol = new object_list(array(
				"class_id" => $this->clid,
				"sort_by" => "oid"
			));
			if(sizeof($ol->ids()) > 0)
			{
				$first = $ol->begin();
				$active = $first->id();
			}
		}
		return $active;
	}

	/**
		@attrib name=add2list
	**/
	function add2list($arr)
	{
		$person = new object();
		$person->set_class_id(CL_CRM_PERSON);

		foreach($arr["sel"] as $id)
		{
			$person->add_person_to_list(array(
				"id" => $id,
				"list_id" => $arr["list_id"],
			));
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=get_udef_skills
	**/
	function get_udef_skills($arr)
	{
		$o = obj($arr["id"]);
		return $o->meta("udef_skills");
	}

	function get_lang_conf($arr = array())
	{
		if(!is_oid($arr["id"]))
		{
			$arr["id"] = $this->get_sysdefault();
		}
		$o = obj($arr["id"]);
		return $o->meta("lang_tbl");
	}

	function get_rec_lang_conf($arr = array())
	{
		if(!is_oid($arr["id"]))
		{
			$arr["id"] = $this->get_sysdefault();
		}
		$o = obj($arr["id"]);
		return $o->meta("rec_lang_tbl");
	}


	/** Returns the array of languages allowed in the personnel management.
		@attrib name=get_languages params=name api=1

		@param id optional type=oid
			The oid of the personnel management object. If not set, system default is used.
	**/
	function get_languages($arr = array())
	{
		$prms = array(
			"class_id" => CL_LANGUAGE,
			"parent" => array(),
			"status" => array(),
			"site_id" => array(),
			"lang_id" => array(),
		);
		$langs = personnel_management::get_lang_conf($arr);
		if(is_array($langs) && count($langs) > 0)
		{
			$prms["oid"] = array_keys($langs);
		}
		$ol = new object_list($prms);
		$objs = $ol->arr();
		enter_function("uasort");
		uasort($objs, array(get_instance(CL_PERSONNEL_MANAGEMENT), "cmp_function"));
		exit_function("uasort");
		foreach($objs as $o)
		{
			$options[$o->id()] = $o->trans_get_val("name");
		}
		return $options;
	}

	function special_url($arr, $ps)
	{
		$p = array(
			"id" => $arr["request"]["id"],
			//"return_url" => get_ru(),
			"group" => $arr["request"]["group"],
		);
		if($arr["request"]["return_url"])
			$p["return_url"] = $arr["request"]["return_url"];
		foreach($ps as $k => $v)
		{
			$p[$k] = $v;
		}
		return $this->mk_my_orb("change", $p);
	}

	/**
		@attrib name=delete_cands
	**/
	function delete_cands($arr)
	{
		$o = obj($arr["ofr_id"]);
		foreach($o->connections_from(array("type" => "RELTYPE_CANDIDATE")) as $conn)
		{
			$to = $conn->to();
			if(in_array($to->prop("person"), $arr["sel"]))
			{
				$to->delete();
			}
		}
		return $arr["post_ru"];
	}

	function cv_save_search($arr)
	{
		$o = new object;
		$o->set_class_id(CL_PERSONNEL_MANAGEMENT_CV_SEARCH_SAVED);
		$o->set_parent($arr["obj_inst"]->id());
		$o->set_name($arr["request"]["cv_search_saved_name"]);
		unset($arr["request"]["cv_search_saved_name"]);
		unset($arr["request"]["cv_search_button_save_search"]);
		foreach($arr["request"] as $k => $v)
		{
			// All the search properties start with cv_
			if(substr($k, 0, 3) == "cv_")
			{
				$o->set_meta($k, $v);
			}
		}
		$o->save();
		$arr["request"]["search_save"] = $o->id();
	}

	function add_skill_options(&$skills, &$options, &$disabled_options, $id, $lvl)
	{
		if (isset($skills[$id]) and is_array($skills[$id]))
		{
			foreach($skills[$id] as $sid => $sdata)
			{
				if($sdata["subheading"])
					$disabled_options[] = $sid;

				$str = "";
				for($i = 0; $i < $lvl; $i++)
				{
					$str .= "- ";
				}

				$options[$sid] = $str.$sdata["name"];
				$this->add_skill_options($skills, $options, $disabled_options, $sid, $lvl + 1);
			}
		}
	}

	/**
		@attrib name=archive params=name
	**/
	function archive($arr)
	{
		foreach($arr["sel"] as $id)
		{
			$o = obj($id);
			$o->archive = 1 - $o->archive;
			$o->save();
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=cmp_function api=1
	**/
	function cmp_function($a, $b)
	{
		if($a->ord() == $b->ord())
		{
			return strcmp($a->trans_get_val("name"), $b->trans_get_val("name"));
		}
		else
		{
			return (int)$a->ord() > (int)$b->ord() ? 1 : -1;
		}
	}


	/** Returns true if the current user is allowed to see this category according to the special rules declared in personnel management settings.
	@attrib name=check_special_acl_for_cat api=1 params=pos

	@param object required type=object/oid

	@param clid optional type=class_id

	@param personnel_management optional type=object

	**/
	function check_special_acl_for_cat($oid, $clid, $pm = NULL)
	{
		enter_function("personnel_management::check_special_acl_for_cat");
		if(!is_object($pm))
		{
			$pm = obj($this->get_sysdefault());
		}
		if(is_object($oid))
		{
			$oid = $oid->id();
		}

		$acls = array(
			CL_PERSONNEL_MANAGEMENT_JOB_OFFER => "needed_acl_job_offer",
			CL_CRM_PERSON => "needed_acl_employee",
			CL_PERSONNEL_MANAGEMENT_CANDIDATE => "needed_acl_candidate",
		);
		$acl = $pm->prop($acls[$clid]);

		$ok = true;
		foreach(safe_array($acl) as $a)
		{
			$ok = $ok && $this->can($a, $oid);
		}
		exit_function("personnel_management::check_special_acl_for_cat");
		return $ok;
	}

	/** Returns true if the current user is allowed to see this object according to the special rules declared in personnel management settings.
	@attrib name=check_special_acl api=1 params=pos

	@param object required type=object

	@param personnel_management optional type=object

	**/
	function check_special_acl_for_obj($o, $pm = NULL)
	{
		enter_function("personnel_management::check_special_acl_for_obj");
		if(!is_object($pm))
		{
			$pm = obj($this->get_sysdefault());
		}
		$clid = $o->class_id();
		switch($clid)
		{
			case CL_PERSONNEL_MANAGEMENT_JOB_OFFER:
				$ok = $this->check_special_acl_for_cat($o->parent(), $clid);
				$locs = array("country", "area", "county", "city");
				foreach($locs as $loc)
				{
					$ok = $ok || $this->check_special_acl_for_cat($o->prop("loc_".$loc), $clid);
				}
				exit_function("personnel_management::check_special_acl_for_obj");
				return $ok;

			case CL_CRM_PERSON:
				enter_function("personnel_management::check_special_acl_for_obj(CL_CRM_PERSON)_conns");
				$ok = false;
				$check_cnt = 0;
				foreach($o->connections_from(array("type" => "RELTYPE_WORK_WANTED")) as $conn)
				{
					$to = $conn->to();
					$prps = array("location", "location_2");
					foreach($prps as $prp)
					{
						foreach($to->$prp as $lid)
						{
							if(is_oid($lid))
							{
								$ok = $ok || $this->check_special_acl_for_cat($lid, $clid);
								$check_cnt++;
							}
						}
					}
				}
				exit_function("personnel_management::check_special_acl_for_obj(CL_CRM_PERSON)_conns");
				enter_function("personnel_management::check_special_acl_for_obj(CL_CRM_PERSON)");
				$ol = new object_list(array(
					"class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE,
					"person" => $id,
					"site_id" => array(),
					"lang_id" => array(),
				));
				foreach($ol->arr() as $cand)
				{
					$prps = array("loc_country", "loc_country", "loc_area", "loc_county", "loc_city", "parent");
					foreach($prps as $prp)
					{
						$lid = $cand->prop("job_offer.".$prp);
						if(is_oid($lid))
						{
							$ok = $ok || $this->check_special_acl_for_cat($lid, $clid);
							$check_cnt++;
						}
					}
				}
				// If there's no reason to show it and no reason not to show it, we'l show it.
				$ok = $check_cnt == 0 || $ok;
				exit_function("personnel_management::check_special_acl_for_obj(CL_CRM_PERSON)");
				exit_function("personnel_management::check_special_acl_for_obj");
				return $ok;

			case CL_PERSONNEL_MANAGEMENT_CANDIDATE:
				exit_function("personnel_management::check_special_acl_for_obj");
				return false;
		}
	}

	private function pdf_tpl_options()
	{
		$dir = aw_ini_get("tpldir")."/applications/personnel_management/personnel_management_job_offer/";
		$handle = is_dir($dir) ? opendir($dir) : false;
		$ret = array();
		while(false !== $handle && false !== ($file = readdir($handle)))
		{
			if(preg_match("/\\.tpl/", $file))
			{
				$ret[$file] = str_replace(".tpl", "", $file);
			}
		}
		return $ret;
	}

	/**
	@attrib name=cv_search_filter
	**/
	function cv_search_filter($o, $r)
	{
		$odl_prms = array(
			"class_id" => CL_CRM_PERSON,
			"lang_id" => array(),
			"site_id" => array(),
			"status" => array(),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"parent" => $o->prop("persons_fld"),
					"CL_CRM_PERSON.RELTYPE_PERSONNEL_MANAGEMENT" => $o->id(),
				)
			)),
			new obj_predicate_sort(array("name" => "asc")),
		);

		if($r["cv_mod_from"] && is_numeric($r["cv_mod_from"]["month"]) && is_numeric($r["cv_mod_from"]["day"]) && is_numeric($r["cv_mod_from"]["year"]))
		{
			$t = mktime(0, 0, 0, $r["cv_mod_from"]["month"], $r["cv_mod_from"]["day"], $r["cv_mod_from"]["year"]);
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"modified" => new obj_predicate_compare(
						OBJ_COMP_GREATER_OR_EQ,
						$t
					),
				),
			));
		}

		if($r["cv_mod_to"] && is_numeric($r["cv_mod_to"]["month"]) && is_numeric($r["cv_mod_to"]["day"]) && is_numeric($r["cv_mod_to"]["year"]))
		{
			$t = mktime(23, 59, 59, $r["cv_mod_to"]["month"], $r["cv_mod_to"]["day"], $r["cv_mod_to"]["year"]);
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"modified" => new obj_predicate_compare(
						OBJ_COMP_LESS_OR_EQ,
						$t
					),
				),
			));
		}

		if($r["cv_bd_to"] && is_numeric($r["cv_bd_to"]["month"]) && is_numeric($r["cv_bd_to"]["day"]) && is_numeric($r["cv_bd_to"]["year"]))
		{
			$t = mktime(23, 59, 59, $r["cv_bd_to"]["month"], $r["cv_bd_to"]["day"], $r["cv_bd_to"]["year"]);
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"birthday" => new obj_predicate_compare(
						OBJ_COMP_LESS_OR_EQ,
						date("Y-m-d", $t)
					),
				),
			));
		}

		if($r["cv_bd_from"] && is_numeric($r["cv_bd_from"]["month"]) && is_numeric($r["cv_bd_from"]["day"]) && is_numeric($r["cv_bd_from"]["year"]))
		{
			$t = mktime(23, 59, 59, $r["cv_bd_from"]["month"], $r["cv_bd_from"]["day"], $r["cv_bd_from"]["year"]);
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"birthday" => new obj_predicate_compare(
						OBJ_COMP_GREATER_OR_EQ,
						date("Y-m-d", $t)
					),
				),
			));
		}

		if($r["cv_age_from"] && $r["cv_age_to"])
		{
			// Why would you store the birthday in YYYY-MM-DD format in a varchar(20) field?????
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"birthday" => new obj_predicate_compare(
						OBJ_COMP_LESS_OR_EQ,
						((date("Y") - $r["cv_age_from"]).date("-m-d"))
					),
				),
			));
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"birthday" => new obj_predicate_compare(
						OBJ_COMP_GREATER,
						((date("Y") - $r["cv_age_to"] - 1).date("-m-d"))
					),
				),
			));
		}
		else
		{
			if($r["cv_age_from"])
			{
				// Why would you store the birthday in YYYY-MM-DD format in a varchar(20) field?????
				$odl_prms["birthday"] = new obj_predicate_compare(
					OBJ_COMP_LESS_OR_EQ,
					((date("Y") - $r["cv_age_from"]).date("-m-d"))
				);
			}
			if($r["cv_age_to"])
			{
				// Why would you store the birthday in YYYY-MM-DD format in a varchar(20) field?????
				$odl_prms["birthday"] = new obj_predicate_compare(
					OBJ_COMP_GREATER,
					((date("Y") - $r["cv_age_to"] - 1).date("-m-d"))
				);
			}
		}
		if($r["cv_age_from"] || $r["cv_age_to"])
		{
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"birthday" => new obj_predicate_not(""),
				)
			));
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"birthday" => new obj_predicate_not("-1"),
				)
			));
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"birthday" => new obj_predicate_not("NULL"),
				)
			));
		}

		if($r["cv_oid"])
		{
			$odl_prms["oid"] = explode(",", str_replace(" ", "", $r["cv_oid"]));
		}
		if($r["cv_name"])
		{
			$odl_prms["name"] = "%".$r["cv_name"]."%";
		}
		if($r["cv_tel"])
		{
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_PHONE.name" => "%".$r["cv_tel"]."%",
					"CL_CRM_PERSON.RELTYPE_PHONE.clean_number" => "%".preg_replace("/[^0-9]/", "", $r["cv_tel"])."%",
				)
			));
		}
		if($r["cv_email"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_EMAIL.mail"] = "%".$r["cv_email"]."%";
		}
		if($r["cv_city"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_ADDRESS.RELTYPE_LINN"] = $r["cv_city"];
		}
		if($r["cv_county"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_ADDRESS.RELTYPE_MAAKOND"] = $r["cv_county"];
		}
		if($r["cv_area"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_ADDRESS.RELTYPE_PIIRKOND"] = $r["cv_area"];
		}
		if($r["cv_address"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_ADDRESS.name"] = "%".$r["cv_address"]."%";
		}
		if($r["cv_addinfo"])
		{
			$odl_prms["notes"] = "%".$r["cv_addinfo"]."%";
		}
		if(in_array($r["cv_gender"], array(1, 2)))
		{
			$odl_prms["gender"] = $r["cv_gender"];
		}
		if($r["cv_approved"])
		{
			$odl_prms["CL_CRM_PERSON.cvapproved"] = $r["cv_approved"] == 2 ? 1 : new obj_predicate_not(1);
		}
		if($r["cv_status"])
		{
			$odl_prms["cvactive"] = $r["cv_status"] == object::STAT_ACTIVE ? 1 : new obj_predicate_not(1);
		}
		if($r["cv_udef_chbox1"])
		{
			$odl_prms["CL_CRM_PERSON.udef_chbox1"] = $r["cv_udef_chbox1"] == 2 ? 1 : new obj_predicate_not(1);
		}
		if($r["cv_udef_chbox2"])
		{
			$odl_prms["CL_CRM_PERSON.udef_chbox2"] = $r["cv_udef_chbox2"] == 2 ? 1 : new obj_predicate_not(1);
		}
		if($r["cv_udef_chbox3"])
		{
			$odl_prms["CL_CRM_PERSON.udef_chbox3"] = $r["cv_udef_chbox3"] == 2 ? 1 : new obj_predicate_not(1);
		}
		// HARIDUS
		if($r["cv_edulvl"] && $r["cv_edu_exact"])
		{
			$odl_prms[$r["cv_edulvl_in_eduobj"] ? "CL_CRM_PERSON.RELTYPE_EDUCATION.degree" : "edulevel"] = $r["cv_edulvl"];
		}
		elseif($r["cv_edulvl"])
		{
			$odl_prms[$r["cv_edulvl_in_eduobj"] ? "CL_CRM_PERSON.RELTYPE_EDUCATION.degree" : "edulevel"] = new obj_predicate_compare(
				OBJ_COMP_GREATER_OR_EQ,
				$r["cv_edulvl"],
				NULL,
				"int"
			);
		}
		if($r["cv_acdeg"])
		{
			$odl_prms["academic_degree"] = $r["cv_acdeg"];
		}
		if($r["cv_schl"] && is_oid($o->prop("schools_fld")))
		{
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					new object_list_filter(array(
						"logic" => "AND",
						"conditions" => array(
							"CL_CRM_PERSON.RELTYPE_EDUCATION.RELTYPE_SCHOOL.name" => "%".$r["cv_schl"]."%",
							"CL_CRM_PERSON.RELTYPE_EDUCATION.RELTYPE_SCHOOL.parent" => $o->prop("schools_fld"),
						),
					)),
					"CL_CRM_PERSON.RELTYPE_EDUCATION.school2" => "%".$r["cv_schl"]."%",
				),
			));
		}
		if($r["cv_faculty"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_EDUCATION.RELTYPE_FACULTY.name"] = "%".$r["cv_faculty"]."%";
		}
		if($r["cv_speciality"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_EDUCATION.speciality"] = "%".$r["cv_speciality"]."%";
		}
		if($r["cv_schl_area"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_EDUCATION.RELTYPE_FIELD.name"] = "%".$r["cv_schl_area"]."%";
		}
		if($r["cv_schl_stat"])
		{
			// 1 - Jah
			// 0 - Ei
			$odl_prms["CL_CRM_PERSON.RELTYPE_EDUCATION.in_progress"] = 2 - $r["cv_schl_stat"];
		}
		if($r["cv_schl_start_from"])
		{
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_EDUCATION.start" => new obj_predicate_compare(
						OBJ_COMP_GREATER_OR_EQ,
						mktime(0, 0, 0, 1, 1, $r["cv_schl_start_from"]),
						NULL,
						"int"
					),
				),
			));
		}
		if($r["cv_schl_start_to"])
		{
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_EDUCATION.start" => new obj_predicate_compare(
						OBJ_COMP_LESS_OR_EQ,
						mktime(23, 59, 59, 12, 31, $r["cv_schl_start_to"]),
						NULL,
						"int"
					),
				),
			));
		}
		// SOOVITUD T88
		if($r["cv_paywish"])
		{
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_WORK_WANTED.pay" => new obj_predicate_compare(
						OBJ_COMP_GREATER_OR_EQ,
						$r["cv_paywish"]
					),
				),
			));
		}
		if($r["cv_paywish2"])
		{
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_WORK_WANTED.pay" => new obj_predicate_compare(
						OBJ_COMP_LESS_OR_EQ,
						$r["cv_paywish2"]
					),
				),
			));
		}
		if($r["cv_job"])
		{
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_WORK_WANTED.professions" => "%".$r["cv_job"]."%",
					"CL_CRM_PERSON.RELTYPE_WORK_WANTED.RELTYPE_PROFESSION.name" => "%".$r["cv_job"]."%",
				),
			));
		}
		if($r["cv_field"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_WORK_WANTED.RELTYPE_FIELD.name"] = "%".$r["cv_field"]."%";
		}
		if($r["cv_type"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_WORK_WANTED.RELTYPE_JOB_TYPE.name"] = "%".$r["cv_type"]."%";
		}
		if($r["cv_location"])
		{
			$cv_locs = explode(",", $r["cv_location"]);
			foreach($cv_locs as $cv_loc)
			{
				$cv_loc = trim($cv_loc);
				$conditions[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_CRM_PERSON.RELTYPE_WORK_WANTED.RELTYPE_LOCATION.name" => "%".$cv_loc."%",
						"CL_CRM_PERSON.RELTYPE_WORK_WANTED.RELTYPE_LOCATION2.name" => "%".$cv_loc."%",
					),
				));
			}
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => $conditions,
			));
		}
		if($r["cv_load"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_WORK_WANTED.load"] = $r["cv_load"];
		}
		// OSKUSED
		if($r["cv_mother_tongue"])
		{
			$odl_prms["mlang"] = $r["cv_mother_tongue"];
		}
		if($r["cv_lang_exp"])
		{
			if(count($r["cv_lang_exp"]) != 1 || $r["cv_lang_exp"][0] != 0)
				$odl_prms["CL_CRM_PERSON.RELTYPE_LANGUAGE_SKILL.RELTYPE_LANGUAGE"] = $r["cv_lang_exp"];
		}
		if($r["cv_lang_exp_lvl"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_LANGUAGE_SKILL.talk"] = new obj_predicate_compare(
				OBJ_COMP_GREATER_OR_EQ,
				$r["cv_lang_exp_lvl"]
			);
			$odl_prms["CL_CRM_PERSON.RELTYPE_LANGUAGE_SKILL.understand"] = new obj_predicate_compare(
				OBJ_COMP_GREATER_OR_EQ,
				$r["cv_lang_exp_lvl"]
			);
			$odl_prms["CL_CRM_PERSON.RELTYPE_LANGUAGE_SKILL.write"] = new obj_predicate_compare(
				OBJ_COMP_GREATER_OR_EQ,
				$r["cv_lang_exp_lvl"]
			);
		}
		if($r["cv_other_skills"])
		{
			$odl_prms["CL_CRM_PERSON.addinfo"] = "%".$r["cv_other_skills"]."%";
		}
		if($r["cv_exp"])
		{
			foreach($r["cv_exp"] as $id => $data)
			{
				//	This is the
				//		0 => t("--vali--")
				//			thing.
				if($data[0] == 0 && count($data) == 1 || count($data) == 0)
				{
					continue;
				}

				if($this->can("view", $r["cv_exp_lvl"][$id]))
				{
					$jrk = obj($r["cv_exp_lvl"][$id])->ord();
				}

				$skill_ol_prms = array(
					"class_id" => CL_CRM_SKILL_LEVEL,
					"parent" => array(),
					"site_id" => array(),
					"lang_id" => array(),
					"CL_CRM_SKILL_LEVEL.skill" => $data,
				);

				if(!$r["cv_exp_lvl"][$id])
				{
					$r["cv_exp_lvl"][$id] = array();
				}
				$skill_ol_prms[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_CRM_SKILL_LEVEL.level" => $r["cv_exp_lvl"][$id],
						"CL_CRM_SKILL_LEVEL.level.ord" => new obj_predicate_compare(
							OBJ_COMP_GREATER,
							$jrk
						),
					),
				));

				$skill_ol = new object_list($skill_ol_prms);
				foreach($skill_ol->arr() as $skill_obj)
				{
					$level = obj($skill_obj->level);
					if($level->ord() <= $jrk && $level->id() != $r["cv_exp_lvl"][$id])
					{
						$skill_ol->remove($skill_obj->id());
					}
				}
				if(count($skill_ol->ids()) == 0)
					return array();

				$_ids = $skill_ol->ids();
				$odl_prms[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"CL_CRM_PERSON.RELTYPE_SKILL_LEVEL" => $_ids,
						"CL_CRM_PERSON.RELTYPE_SKILL_LEVEL2" => $_ids,
						"CL_CRM_PERSON.RELTYPE_SKILL_LEVEL3" => $_ids,
						"CL_CRM_PERSON.RELTYPE_SKILL_LEVEL4" => $_ids,
						"CL_CRM_PERSON.RELTYPE_SKILL_LEVEL5" => $_ids
					)
				));
			}
		}
		if($r["cv_driving_licence"])
		{
			$vals = array();
			/*
			foreach($r["cv_driving_licence"] as $c)
			{
				$vals[] = "%".strtolower($c)."%";
			}
			$odl_prms["drivers_license"] = $vals;
			*/
			foreach($r["cv_driving_licence"] as $c)
			{
				$vals[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						"drivers_license" => "%".strtolower($c)."%",
					),
				));
			}
			$odl_prms[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => $vals,
			));
		}
		// T88KOGEMUS
		if($r["cv_previous_rank"])
		{
			$conditions = array(
				"CL_CRM_PERSON.RELTYPE_PREVIOUS_JOB.RELTYPE_PROFESSION.name" => "%".$r["cv_previous_rank"]."%",
				//"CL_CRM_PERSON.RELTYPE_CURRENT_JOB.RELTYPE_PROFESSION.name" => "%".$r["cv_previous_rank"]."%",
			);
			if($r["cv_praxis"])
			{
				$conditions["CL_CRM_PERSON.RELTYPE_PREVIOUS_PRAXIS.RELTYPE_PROFESSION.name"] = "%".$r["cv_previous_rank"]."%";
			}
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => $conditions,
			));
		}
		if($r["cv_previous_field"])
		{
			$conditions = array(
				"CL_CRM_PERSON.RELTYPE_PREVIOUS_JOB.RELTYPE_FIELD.name" => "%".$r["cv_previous_field"]."%",
				"CL_CRM_PERSON.RELTYPE_CURRENT_JOB.RELTYPE_FIELD.name" => "%".$r["cv_previous_field"]."%",
			);
			if($r["cv_praxis"])
			{
				$conditions["CL_CRM_PERSON.RELTYPE_PREVIOUS_PRAXIS.RELTYPE_FIELD.name"] = "%".$r["cv_previous_field"]."%";
			}
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => $conditions,
			));
		}
		if($r["cv_company"])
		{
			$conditions = array(
				"CL_CRM_PERSON.RELTYPE_WORK.name" => "%".$r["cv_company"]."%",
				"CL_CRM_PERSON.RELTYPE_ORG_RELATION.org.name" => "%".$r["cv_company"]."%",
				"CL_CRM_PERSON.RELTYPE_PREVIOUS_JOB.org.name" => "%".$r["cv_company"]."%",
				"CL_CRM_PERSON.RELTYPE_CURRENT_JOB.org.name" => "%".$r["cv_company"]."%",
				"CL_CRM_PERSON.RELTYPE_COMPANY_RELATION.org.name" => "%".$r["cv_company"]."%",
				// Dunno if keepin' the company's name in the 'name' field of additional education object is the best idea...
				"CL_CRM_PERSON.RELTYPE_ADD_EDUCATION.name" => "%".$r["cv_company"]."%",
			);
			if($r["cv_praxis"])
			{
				$conditions["CL_CRM_PERSON.RELTYPE_PREVIOUS_PRAXIS.org.name"] = "%".$r["cv_company"]."%";
			}
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => $conditions,
			));
		}
		if($r["cv_wrk_load"])
		{
			$conditions = array(
				"CL_CRM_PERSON.RELTYPE_PREVIOUS_JOB.load" => $r["cv_wrk_load"],
				"CL_CRM_PERSON.RELTYPE_CURRENT_JOB.load" => $r["cv_wrk_load"],
			);
			if($r["cv_praxis"])
			{
				$conditions["CL_CRM_PERSON.RELTYPE_PREVIOUS_PRAXIS.load"] = $r["cv_wrk_load"];
			}
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => $conditions,
			));
		}
		if($r["cv_recommenders"])
		{
			$odl_prms[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_CRM_PERSON.RELTYPE_RECOMMENDATION.person.name" => "%".$r["cv_recommenders"]."%",
					"CL_CRM_PERSON.RELTYPE_RECOMMENDATION.person.RELTYPE_ORG_RELATION.org.name" => "%".$r["cv_recommenders"]."%",
					"CL_CRM_PERSON.RELTYPE_RECOMMENDATION.person.RELTYPE_CURRENT_JOB.org.name" => "%".$r["cv_recommenders"]."%",
					"CL_CRM_PERSON.RELTYPE_RECOMMENDATION.person.RELTYPE_PREVIOUS_JOB.org.name" => "%".$r["cv_recommenders"]."%",
					"CL_CRM_PERSON.RELTYPE_RECOMMENDATION.person.RELTYPE_WORK.name" => "%".$r["cv_recommenders"]."%",
					"CL_CRM_PERSON.RELTYPE_RECOMMENDATION.person.RELTYPE_COMPANY_RELATION.org.name" => "%".$r["cv_recommenders"]."%",
				),
			));
		}
		if($r["cv_comments"])
		{
			$odl_prms["CL_CRM_PERSON.RELTYPE_COMMENT.commtext"] = "%".$r["cv_comments"]."%";
		}

		return $odl_prms;
	}

	/** If person is added to personnel management, send notification mail.
	@attrib name=on_add_person
	**/
	function on_add_person($arr)
	{
		get_instance("personnel_management_obj")->on_add_person($arr);
	}

	/** Handles the employers search
		@attrib name=search_employers all_args=1
	**/
	function search_employers($arr)
	{
		$post_ru = $arr["post_ru"];
		foreach(array_keys($arr) as $k)
		{
			if(substr($k, 0, 3) != "es_")
			{
				unset($arr[$k]);
			}
		}
		$arr["es_created_from"] = mktime(0, 0, 0, $arr["es_created_from"]["month"], $arr["es_created_from"]["day"], $arr["es_created_from"]["year"]);
		$arr["es_created_to"] = mktime(23, 59, 59, $arr["es_created_to"]["month"], $arr["es_created_to"]["day"], $arr["es_created_to"]["year"]);
		$arr["branch_id"] = "search";
		return aw_url_change_var($arr, false, $post_ru);
	}

	/** Deletes the employers
		@attrib name=delete_employers
	**/
	function delete_employers($arr)
	{
		$arr["sel"] = safe_array($arr["sel"]);
		if(count($arr["sel"]) > 0)
		{
			if($arr["obj_inst"]->remove_job_offers_with_employer == 1)
			{
				$ol = new object_list(array(
					"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
					"CL_PERSONNEL_MANAGEMENT_JOB_OFFER.RELTYPE_ORG" => $arr["sel"],
					"lang_id" => array(),
					"site_id" => array(),
				));
				$ol->delete();
			}

			$ol = new object_list(array(
				"oid" => $arr["sel"],
				"lang_id" => array(),
				"site_id" => array(),
			));
			$ol->delete();
		}
		return $arr["post_ru"];
	}

	/** Sends e-mail to employers
		@attrib name=send_email api=1
	**/
	function send_email($arr)
	{
		$sel = safe_array($arr["sel"]);
		if(count($sel) > 0)
		{
			$pt = $arr["id"];
			return $this->mk_my_orb("new", array("mto_relpicker" => $sel, "parent" => $pt, "return_url" => $arr["post_ru"]), CL_MESSAGE);
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=get_valid_job_offers api=1
	**/
	public function get_valid_job_offers($arr)
	{
		return get_instance("personnel_management_obj")->get_valid_job_offers($arr);
	}

	private function get_offers_table_offers($arr)
	{
		$oid = is_oid($arr["request"]["county_id"]) ? $arr["request"]["county_id"] : (is_oid($arr["request"]["city_id"]) ? $arr["request"]["city_id"] : $arr["request"]["area_id"]);

		$toopakkujad_ids = array();
		$toopakkujad = array();

		if($arr["request"]["branch_id"] == "valid")
		{
			$objs = $this->get_valid_job_offers(array(
				"parent" => $this->offers_fld,
				"childs" => 1,
			));
		}
		elseif(!is_oid($oid))
		{
			$branch_id = $this->can("view", $arr["request"]["branch_id"]) ? $arr["request"]["branch_id"] : $this->offers_fld;
			$o = obj($branch_id);


			if($o->class_id() == CL_META)
			{
				$objs = new object_list(array(
					"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
					"lang_id" => array(),
					"archive" => $arr["request"]["group"] != "offers_archive" ? 0 : 1,
					"field" => $branch_id,
				));
			}
			else
			{
				$objs = new object_list(array(
					"lang_id" => array(),
					"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
					"parent" => $branch_id,
					"archive" => $arr["request"]["group"] != "offers_archive" ? 0 : 1,
				));
			}
		}
		else
		{
			$o = obj($oid);
			$objs = $o->get_job_offers(array(
				"parent" => $this->offers_fld,
				"props" => array(
					"archive" => $arr["request"]["group"] != "offers_archive" ? 0 : 1,
				)
			));
		}
		return $objs;
	}

	private function get_offers_srch_offers($arr)
	{
		$r = &$arr["request"];

		$ol_arr = array(
			"site_id" => array(),
			"lang_id" => array(),
			"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER,
			"archive" => $arr["request"]["group"] != "offers_archive" ? 0 : 1,
		);
		if($r["os_status"] == 1 || $r["os_status"] == 2)
		{
			$ol_arr["status"] = $r["os_status"];
		}
		if($r["os_confirmed"] == 1 || $r["os_confirmed"] == 2)
		{
			$ol_arr["confirmed"] = $r["os_confirmed"] == 1 ? 1 : new obj_predicate_not(1);
		}
		if($r["os_dl_from_time"])
		{
			$ol_arr[] = new object_list_filter(array(
				"logic" => $r["os_endless"] ? "OR" : "AND",
				"conditions" => array(
					"end" => new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, $r["os_dl_from_time"]),
					"endless" => $r["os_endless"] ? 1 : new obj_predicate_not(1),
				),
			));
		}
		if($r["os_dl_to_time"])
		{
			$ol_arr[] = new object_list_filter(array(
				"logic" => $r["os_endless"] ? "OR" : "AND",
				"conditions" => array(
					"end" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, $r["os_dl_to_time"] + (24 * 3600 - 1)),
					"endless" => $r["os_endless"] ? 1 : new obj_predicate_not(1),
				),
			));
		}
		if($r["os_employer"])
		{
			$tmp = explode(",", $r["os_employer"]);
			foreach($tmp as $k => $v)
			{
				$tmp[$k] = "%".trim($v)."%";
			}
			$ol_arr["company.name"] = $tmp;
		}
		if($r["os_salary_from"])
		{
			$ol_arr[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"salary" => new obj_predicate_compare(
						OBJ_COMP_GREATER_OR_EQ,
						$r["os_salary_from"],
						null,
						"int"
					),
				),
			));
		}
		if($r["os_salary_to"])
		{
			$ol_arr[] = new object_list_filter(array(
				"logic" => "AND",
				"conditions" => array(
					"salary" => new obj_predicate_compare(
						OBJ_COMP_LESS_OR_EQ,
						$r["os_salary_from"],
						null,
						"int"
					),
				),
			));
		}
		if($r["os_county"])
		{
			if(is_oid($r["os_county"]) || is_array($r["os_county"]))
			{
				$ol_arr["loc_county"] = $r["os_county"];
			}
			else
			{
				$ol_arr["loc_county.name"] = "%".$r["os_county"]."%";
			}
		}
		if($r["os_city"])
		{
			if(is_oid($r["os_city"]) || is_array($r["os_city"]))
			{
				$ol_arr["loc_city"] = $r["os_city"];
			}
			else
			{
				$ol_arr["loc_city.name"] = "%".$r["os_city"]."%";
			}
		}
		if($r["os_field"])
		{
			$ol_arr["field"] = $r["os_field"];
		}
		if($r["os_jobtype"])
		{
			$ol_arr["job_type"] = $r["os_jobtype"];
		}
		if($r["os_load"])
		{
			$ol_arr["load"] = $r["os_load"];
		}
		if($r["os_workinfo"])
		{
			$ol_arr["workinfo"] = "%".$r["os_workinfo"]."%";
		}
		if($r["os_requirements"])
		{
			$ol_arr["requirements"] = "%".$r["os_requirements"]."%";
		}
		if($r["os_info"])
		{
			$ol_arr["info"] = "%".$r["os_info"]."%";
		}
		if($r["os_contact"])
		{
			$ol_arr["contact.name"] = "%".$r["os_contact"]."%";
		}
		$ol = new object_list($ol_arr);
		return $ol;
	}

	public function get_sectors()
	{
		enter_function("personnel_management::get_sectors");
		$o = obj($this->get_sysdefault());

		$prms = array(
			"class_id" => CL_CRM_SECTOR,
			"sort_by" => "objects.name"
		);

		if(is_oid($o->sectors_fld))
		{
			$prms["parent"] = $o->sectors_fld;
		}

		$ol = new object_list($prms);

		// parentize the list - move all items that have parents in the list
		// below them, so we can draw them with grouping
		$list_by_parent = array();
		foreach($ol->arr() as $o)
		{
			if ($ol->get_at($o->parent()))
			{
				$list_by_parent[$o->parent()][] = $o;
			}
			else
			{
				$list_by_parent[null][] = $o;
			}
		}

		$items = array();
		foreach($list_by_parent as $_pt => $litems)
		{
			foreach($litems as $o)
			{
				$items[$o->id()] = $this->_get_level_in_list($list_by_parent, $o)-1; //*3).$o->name();
			}
		}

		// now that we have the levels for items in the list, sort the list correctly as well.
		// to do that, go over the list, and for each item that has a level of 0, get an object_tree for all subitems
		// and add that to the list
		$nitems = array();
		foreach($items as $id => $level)
		{
			if ($level == 0)
			{
				$this->_add_0level_item_to_list($nitems, $id);
			}
		}

		exit_function("personnel_management::get_sectors");
		return $nitems;
	}

	function _add_0level_item_to_list(&$nitems, $id)
	{
		$ot = new object_tree(array(
			"parent" => $id,
			"class_id" => CL_CRM_SECTOR,
			"lang_id" => array(),
			"site_id" => array()
		));
		$o = obj($id);
		$nitems[$o->id()] = $o->trans_get_val("name");
		$this->_req_add_0level_item_to_list($nitems, $ot, $id);
	}

	function _req_add_0level_item_to_list(&$nitems, $ot, $id)
	{
		$this->_level++;
		foreach($ot->level($id) as $o)
		{
			$nitems[$o->id()] = str_repeat("&nbsp;", $this->_level*3).$o->trans_get_val("name");
			$this->_req_add_0level_item_to_list($nitems, $ot, $o->id());
		}
		$this->_level--;
	}

	function _get_level_in_list($list, $item)
	{
		$pt = false;
		foreach($list as $_pt => $items)
		{
			foreach($items as $l_item)
			{
				if ($l_item->id() == $item->id())
				{
					$pt = $_pt;
				}
			}
		}

		if ($pt)
		{
			return $this->_get_level_in_list($list, obj($pt))+1;
		}
		return 0;
	}

	function get_schools()
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"parent" => obj($this->get_sysdefault())->schools_fld,
			"site_id" => aw_ini_get("site_id"),
		));
		$ops = array();
		$ol_arr = $ol->arr();
		uasort($ol_arr, array($this, "cmp_function"));
		foreach($ol_arr as $o)
		{
			$ops[$o->id()] = $o->trans_get_val("name");
		}

		return $ops;
	}

	/**
		@attrib name=get_locations params=pos

		@param class_id required type=class_id
	**/
	public function get_locations($clid = array(CL_CRM_COUNTRY, CL_CRM_AREA, CL_CRM_COUNTY, CL_CRM_CITY))
	{
		$pm = obj($this->get_sysdefault());

		$prms = array(
			"class_id" => $clid,
			"lang_id" => array(),
			"site_id" => array(),
		);
		$ot = new object_tree(array(
			"class_id" => array(CL_CRM_COUNTRY, CL_CRM_AREA, CL_CRM_COUNTY, CL_CRM_CITY),
			"parent" => $pm->locations_fld,
			"lang_id" => array(),
			"site_id" => array(),
		));
		$ids = $ot->ids();
		if(count($ids) > 0)
		{
			$prms["oid"] = $ids;
		}
		$ol = new object_list($prms);
		$objs = $ol->arr();
		enter_function("uasort");
		uasort($objs, array($this, "cmp_function"));
		exit_function("uasort");
		foreach($objs as $o)
		{
			$names[$o->id()] = $o->trans_get_val("name");
		}
		return $names;
	}

	/**
		@attrib name=get_employers api=1
	**/
	public function get_employers($arr = array())
	{
		return $this->employers_tbl_data(array(
			"class_id" => CL_CRM_COMPANY,
			"obj_inst" => obj($this->get_sysdefault()),
			"return_as_names" => true,
		));
	}

	/**
		@attrib name=get_employers api=1
	**/
	public function get_professions($arr = array())
	{
		if(!isset($arr["obj_inst"]) || !is_object($arr["obj_inst"]))
		{
			$arr["obj_inst"] = obj($this->get_sysdefault());
		}
		$prms = array(
			"name" => "%".htmlspecialchars($_GET["vs_name"])."%",
			"class_id" => CL_CRM_PROFESSION,
			"parent" => $arr["obj_inst"]->professions_fld,
			"lang_id" => array(),
			"site_id" => array(),
		);

		if($arr["return_as_odl"])
		{
			$props = isset($arr["props"]) && is_array($arr["props"]) ? $arr["props"] : array(
				CL_CRM_PROFESSION => array("oid", "name"),
			);
			$ret = new object_data_list($prms, $props);
		}
		elseif($arr["return_as_names"])
		{
			$ol = new object_list($prms);
			$ret = $ol->names();
		}
		else
		{
			$ret = new object_list($prms);
		}
		return $ret;
	}

	public function get_legal_forms()
	{
		$prms = array(
			"class_id" => CL_CRM_CORPFORM,
			"site_id" => array(),
			"lang_id" => array(),
		);
		$o = obj($this->get_sysdefault());
		if(is_oid($o->legal_forms_fld))
		{
			$prms["parent"] = $o->legal_forms_fld;
		}
		$ol = new object_list($prms);
		return $ol->names();
	}

	/** Sends cv by e-mail to the e-mail address(es) entered to js prompt box.
		@attrib name=send_cv_email params=name all_args=1
	**/
	public function send_cv_email($arr)
	{
		$sel = safe_array($arr["sel"]);
		$pm = obj($arr["id"]);
		foreach($sel as $pid)
		{
			$pm->send_cv_by_email(array(
				"person_obj" => obj($pid),
				"to" => $arr["email_to_send_cv_to"],
			));
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=send_cv_employer params=name all_args=1
	**/
	public function send_cv_employer($arr)
	{
		$sel = safe_array($arr["sel"]);
		aw_session_set("cv_for_employer_".$arr["id"], aw_serialize($sel));
		return $this->mk_my_orb("change", array("id" => $arr["id"], "group" => "employers", "return_url" => $arr["return_url"]));
	}

	/** Sends the previously picked CVs to selected employers.
		@attrib name=send_cv_to_employer params=name

		@param sel required type=array(oid) acl=view
			The array of OIDs of the employers.
	**/
	public function send_cv_to_employer($arr)
	{
		$sel = safe_array($arr["sel"]);
		$cv_array = safe_array(aw_unserialize(aw_global_get("cv_for_employer_".$arr["id"])));
		$pm_obj = obj($arr["id"]);
		if(count($sel) > 0)
		{
			$receiver_ids = get_instance(CL_MESSAGE)->mails_from_persons_and_companies(array("ids" => $sel));
			$receivers_ol = new object_data_list(
				array(
					"class_id" => CL_ML_MEMBER,
					"oid" => $receiver_ids,
					"site_id" => array(),
					"lang_id" => array(),
				),
				array(
					CL_ML_MEMBER => array("mail"),
				)
			);
			$receivers = $receivers_ol->get_element_from_all("mail");
			foreach($cv_array as $cv)
			{
				$person_obj = obj($cv);
				foreach($receivers as $to)
				{
					$pm_obj->send_cv_by_email(array(
						"person_obj" => $person_obj,
						"to" => $to,
					));
				}
			}
		}
		return $arr["post_ru"];
	}
}
