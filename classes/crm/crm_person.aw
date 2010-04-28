<?php

namespace automatweb;

/*

HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_CRM_MEETING, on_connect_to_meeting)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_TO, CL_TASK, on_connect_to_task)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_ADD_FROM, CL_PERSONNEL_MANAGEMENT_JOB_WANTED, on_connect_job_wanted_to_person)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_ALIAS_DELETE_FROM, CL_PERSONNEL_MANAGEMENT_JOB_WANTED, on_disconnect_job_wanted_from_person)

@classinfo relationmgr=yes syslog_type=ST_CRM_PERSON no_status=1 confirm_save_data=1 prop_cb=1
@tableinfo kliendibaas_isik index=oid master_table=objects master_index=oid
@tableinfo aw_account_balances master_index=oid master_table=objects index=aw_oid

@default table=objects
------------------------------------------------------------------

@groupinfo general2 caption="&Uuml;ldine" parent=general no_submit=1
@default group=general2

@property show_cnt type=hidden field=show_cnt table=kliendibaas_isik
@caption Vaatamisi

@property name type=text
@caption Nimi

@property balance type=hidden table=aw_account_balances field=aw_balance

@default table=kliendibaas_isik

@property firstname type=textbox size=15 maxlength=50
@caption Eesnimi

@property lastname type=textbox size=15 maxlength=50
@caption Perekonnanimi

@property previous_lastname type=textbox size=15 maxlength=50
@caption Eelmine perekonnanimi

@property nickname type=textbox size=10 maxlength=20
@caption H&uuml;&uuml;dnimi

@property personal_id type=textbox size=13 maxlength=11
@caption Isikukood

@property external_id type=hidden field=aw_external_id
@caption Siduss&uuml;steemi id

@property birthday type=date_select year_from=1930 year_to=2010 default=-1 save_format=iso8601
@caption S&uuml;nniaeg

@property birthday_hidden type=checkbox ch_value=1 table=objects field=meta method=serialize
@caption Peida s&uuml;nniaeg

@property gender type=chooser
@caption Sugu

@property title type=chooser
@caption Tiitel

@property social_status type=chooser
@caption Perekonnaseis

@property spouse type=textbox size=25 maxlength=50
@caption Abikaasa

@property children1 type=select table=objects field=meta method=serialize
@caption Lapsi

@property pictureurl type=textbox size=40 maxlength=200
@caption Pildi/foto url

@property picture type=releditor reltype=RELTYPE_PICTURE rel_id=first props=file
@caption Pilt/foto

@property picture2 type=releditor reltype=RELTYPE_PICTURE2 rel_id=first props=file
@caption Pilt suuremana

@property ext_id type=textbox table=objects field=subclass maxlength=11
@caption Numbriline siduss&uuml;steemi ID

@property ext_id_alphanumeric type=textbox maxlength=25
@caption Siduss&uuml;steemi ID

@property code type=textbox
@caption Kood

@property username type=text store=no
@comment Kasutajanimes on lubatud ladina t&auml;hestiku suur- ja v&auml;iket&auml;hed, numbrid 0-9 ning m&auml;rgid alakriips ja punkt
@caption Kasutaja

@property password type=password table=objects field=meta method=serialize
@caption Parool

@property client_manager type=relpicker reltype=RELTYPE_CLIENT_MANAGER
@caption Kliendihaldur

@property is_customer type=checkbox ch_value=1 field=aw_is_customer
@caption Lisa kliendina

@property is_important type=checkbox ch_value=1 store=no
@caption Oluline

@property crm_settings type=text store=no
@caption CRM Seaded

@property cvactive type=checkbox ch_value=1 table=kliendibaas_isik
@caption CV aktiivne

@property cvapproved type=checkbox ch_value=1 table=kliendibaas_isik
@caption CV kinnitatud

@property not_working type=checkbox ch_value=1 table=kliendibaas_isik field=not_working
@caption Hetkel ei t&ouml;&ouml;ta

@property submit_cv type=submit store=no
@caption Salvesta

------------------------------------------------------------------

@groupinfo cust_rel caption="Kliendisuhe" parent=general
@default group=cust_rel

	@layout co_bottom_seller area_caption=Kliendisuhe&#44;&nbsp;tema_ostab_meilt closeable=1 type=hbox width=50%:50%

		@layout co_bottom_seller_l type=vbox parent=co_bottom_seller

			@property co_is_cust type=checkbox ch_value=1 store=no parent=co_bottom_seller_l no_caption=1 prop_cb=1
			@caption Kehtib

			@property cust_contract_creator type=select table=kliendibaas_isik parent=co_bottom_seller_l
			@caption Kliendisuhte looja

			@property cust_contract_date type=date_select table=kliendibaas_isik parent=co_bottom_seller_l
			@caption Kliendisuhte alguskuup&auml;ev

			@property contact_person type=text store=no parent=co_bottom_seller_l
			@caption Kliendipoolne kontaktisik

		@layout co_bottom_seller_r type=vbox parent=co_bottom_seller

			@property priority type=textbox table=kliendibaas_isik  parent=co_bottom_seller_r
			@caption Kliendi prioriteet

			@property referal_type type=classificator store=connect reltype=RELTYPE_REFERAL_TYPE parent=co_bottom_seller_r
			@caption Sissetuleku meetod

			@property client_manager type=relpicker reltype=RELTYPE_CLIENT_MANAGER table=kliendibaas_isik parent=co_bottom_seller_r
			@caption Kliendihaldur

			@property bill_due_date_days type=textbox size=5 table=kliendibaas_isik parent=co_bottom_seller_r
			@caption Makset&auml;htaeg (p&auml;evi)

			@property bill_penalty_pct type=textbox table=kliendibaas_isik size=5  parent=co_bottom_seller_r
			@caption Arve viivise %

	@layout co_bottom_buyer area_caption=Kliendisuhe&#44;&nbsp;meie_ostame_talt closeable=1 type=hbox width=50%:50%

		@layout co_bottom_buyer_l type=vbox parent=co_bottom_buyer
			@property co_is_buyer type=checkbox ch_value=1 store=no parent=co_bottom_buyer_l no_caption=1 prop_cb=1
			@caption Kehtib

			@property buyer_contract_creator type=select store=no parent=co_bottom_buyer_l
			@caption Hankijasuhte looja

			@property buyer_contract_date type=date_select store=no parent=co_bottom_buyer_l prop_cb=1
			@caption Hankijasuhte alguskuup&auml;ev

		@layout co_bottom_buyer_r type=vbox parent=co_bottom_buyer
			@property buyer_priority type=textbox store=no  parent=co_bottom_buyer_r prop_cb=1
			@caption M&uuml;&uuml;ja prioriteet

			@property buyer_contact_person type=text store=no parent=co_bottom_buyer_r prop_cb=1
			@caption M&uuml;&uuml;ja kontaktisik

------------------------------------------------------------------

@groupinfo contact caption="Kontaktandmed" parent=general
@default group=contact

@property ct_rel_tb type=toolbar no_caption=1 store=no

	@layout ct_super type=vbox  closeable=1 area_caption=Kontaktid

		@layout contact_l type=hbox parent=ct_super width=30%:30%:30%

			@property contact_desc_text type=text store=no parent=contact_l captionside=top
			@caption Kontaktandmed

			@property address type=relpicker reltype=RELTYPE_ADDRESS parent=contact_l captionside=top
			@caption Aadress


	@layout work_super type=vbox  closeable=1 area_caption=T&ouml;&ouml;kohad

		@property work_tbl type=table parent=work_super store=no no_caption=1

#		@layout work type=hbox parent=work_super width=30%:30%:30%


#			@property work_contact type=relpicker reltype=RELTYPE_WORK parent=work captionside=top
#			@caption Organisatsioon

#			@property org_section type=relpicker reltype=RELTYPE_SECTION parent=work multiple=1 table=objects field=meta method=serialize store=connect captionside=top
#			@caption Osakond

#			@property rank type=relpicker reltype=RELTYPE_RANK automatic=1 parent=work captionside=top
#			@caption Ametinimetus

#			@property comment type=textarea cols=40 rows=3 table=objects field=comment parent=work captionside=top
#			@caption Kontakt

#		@layout work_down type=hbox parent=work_super width=20%:80%

#			@property work_contact_start parent=work_down captionside=top type=releditor reltype=RELTYPE_CURRENT_JOB rel_id=first props=start store=no
#			@caption T&ouml;&ouml;le asumise aeg


		@layout ceditphf type=hbox width=50%:50%

			@layout cedit_phone type=vbox parent=ceditphf closeable=1 area_caption=Telefonid

				@property cedit_phone_tbl type=table no_caption=1 parent=cedit_phone store=no

			@layout cedit_fax type=vbox parent=ceditphf closeable=1 area_caption=Faksid

				@property cedit_telefax_tbl type=table no_caption=1 parent=cedit_fax store=no




		@layout ceditemlurl type=hbox width=50%:50%

			@layout cedit_email type=vbox parent=ceditemlurl closeable=1 area_caption=E-mail

				@property cedit_email_tbl type=table store=no no_caption=1 parent=cedit_email store=no

			@layout cedit_url type=vbox parent=ceditemlurl closeable=1 area_caption=URL

				@property cedit_url_tbl type=table store=no no_caption=1 parent=cedit_url store=no

		@layout ceditbank type=vbox closeable=1 area_caption=Pangaarved

			@property cedit_bank_account_tbl type=table store=no no_caption=1 parent=ceditbank

		@layout ceditprof type=vbox closeable=1 area_caption=Eelnev&nbsp;t&ouml;&ouml;kogemus

			@property cedit_profession_tbl type=table store=no no_caption=1 parent=ceditprof store=no

		@layout ceditadr type=vbox closeable=1 area_caption=Aadressid

			@property cedit_adr_tbl type=table store=no no_caption=1 parent=ceditadr

			@property address_edit type=releditor mode=manager2 store=no props=name,aadress,postiindex,linn,maakond,piirkond,riik,comment table_fields=name,aadress,postiindex,linn,maakond,piirkond,riik,comment reltype=RELTYPE_ADDRESS

		@layout ceditmsn type=vbox closeable=1 area_caption=Msn/yahoo/aol/icq

			@property messenger type=textbox size=30 maxlength=200 parent=ceditmsn no_caption=1


@property address2_edit type=releditor mode=manager store=connect props=country,location_data,location,street,house,apartment,postal_code,po_box table_fields=name,location,street,house,apartment reltype=RELTYPE_ADDRESS_ALT

@property email type=hidden table=objects field=meta method=serialize
@property phone type=hidden table=objects field=meta method=serialize
@property fax type=hidden table=objects field=meta method=serialize
@property url type=hidden table=objects field=meta method=serialize
@property aw_bank_account type=hidden table=objects field=meta method=serialize

------------------------------------------------------------------
@groupinfo description caption="Kirjeldus" parent=general
@default group=description

@property person_tb type=toolbar submit=no no_caption=1
@caption Isiku toolbar

@property nationality type=relpicker reltype=RELTYPE_NATIONALITY store=connect
@caption Rahvus

@property citizenship_table type=table submit=no editonly=1
@caption Kodakondsuse tabel

@property cv_file_url type=text store=no
@caption CV fail

@property cv_file type=releditor reltype=RELTYPE_CV_FILE rel_id=first props=file store=connect
@caption CV failina

@property notes type=textarea cols=60 rows=10
@caption Vabas vormis tekst

@property aliasmgr type=aliasmgr no_caption=1 store=no
@caption Seostehaldur

------------------------------------------------------------------

@groupinfo documents_all caption="Dokumendid" submit=no parent=general
@default group=documents_all

@property docs_tb type=toolbar no_caption=1

@layout docs_lt type=hbox width=20%:80%

@layout docs_left type=vbox parent=docs_lt

@layout docs_tree type=vbox parent=docs_left

	@property docs_tree type=treeview parent=docs_tree no_caption=1

@layout docs_search type=vbox parent=docs_left closeable=1 area_caption=Dokumentide&nbsp;otsing

	@layout docs_s_inputs type=vbox parent=docs_search

		@property docs_s_name type=textbox size=30 store=no captionside=top parent=docs_s_inputs
		@caption Nimetus

		@property docs_s_type type=select store=no captionside=top parent=docs_s_inputs
		@caption Liik

		@property docs_s_task type=textbox size=30 store=no captionside=top parent=docs_s_inputs
		@caption Toimetus

		@property docs_s_user type=textbox size=30 store=no captionside=top parent=docs_s_inputs
		@caption Tegija

		@property docs_s_customer type=textbox size=30 store=no captionside=top parent=docs_s_inputs
		@caption Klient

	@layout docs_s_but_row type=hbox parent=docs_search

		@property docs_s_sbt type=submit store=no no_caption=1 parent=docs_s_but_row
		@caption Otsi

		@property docs_s_clear type=submit store=no no_caption=1 parent=docs_s_but_row
		@caption T&uuml;hista otsing

@property docs_tbl type=table store=no no_caption=1 parent=docs_lt

------------------------------------------------------------------

@groupinfo mails caption="Kirjad" submit=no parent=general
@default group=mails

@property mails_tb type=toolbar no_caption=1

@layout mails_lt type=hbox width=10%:90%
	@layout mails_left type=vbox parent=mails_lt
		@layout mailsl_tree type=vbox parent=mails_left
			@property mails_tree type=treeview parent=mailsl_tree no_caption=1
		@layout mails_search type=vbox parent=mails_left closeable=1 area_caption=Kirjade&nbsp;otsing

			@property mails_s_name type=textbox size=15 store=no captionside=top parent=mails_search
			@caption Teema

			@property mails_s_content type=textbox size=15 store=no captionside=top parent=mails_search
			@caption Sisu

			@property mails_s_customer type=textbox size=15 store=no captionside=top parent=mails_search
			@caption Klient

			@property mails_s_sbt type=submit store=no no_caption=1 parent=mails_search
			@caption Otsi

		@layout mails_table_l type=hbox parent=mails_lt
			@property mails_tbl type=table store=no no_caption=1 parent=mails_table_l

----------------------


@groupinfo settings caption="Muud seaded" parent=general
@default group=settings

@property templates type=select table=objects field=meta method=serialize
@caption V&auml;ljund

@property server_folder type=server_folder_selector table=objects field=meta method=serialize
@caption Kataloog serveris, kus asuvad failid

@property languages type=relpicker multiple=1 automatic=1 reltype=RELTYPE_LANGUAGE store=connect
@caption Keeled

@property bill_due_days type=textbox size=5  table=objects field=meta method=serialize
@caption Makset&auml;htaeg (p&auml;evi)

@property currency type=relpicker reltype=RELTYPE_CURRENCY table=objects field=meta method=serialize
@caption Valuuta

@property is_quickmessenger_enabled type=checkbox table=objects field=meta method=serialize ch_value=1 default=0
@caption Quickmessenger enabled

-----------------------
@groupinfo work_hrs caption="T&ouml;&ouml;ajad" parent=general
@default group=work_hrs

	@property work_hrs type=textarea rows=7 cols=20 table=objects field=meta method=serialize
	@caption T&ouml;&ouml;ajad
	@comment Formaat: E: 9-17\nT: 14-19

	@property vacation_hrs type=textarea rows=7 cols=20 table=objects field=meta method=serialize
	@caption Puhkused
	@comment Formaat: 15.03.2007 - 18.03.2007

------------------------------------------------------------------
@groupinfo cv caption="Elulugu"

@groupinfo education caption="Haridusk&auml;ik" parent=cv
@default group=education
	@property edulevel type=select field=edulevel table=kliendibaas_isik
	@caption Haridustase

	@property academic_degree type=select field=academic_degree table=kliendibaas_isik
	@caption Akadeemiline kraad

	@property education_edit type=releditor store=no mode=manager2 reltype=RELTYPE_EDUCATION props=school1,school2,faculty,degree,field,speciality,main_speciality,in_progress,dnf,obtain_language,start,end,end_date,diploma_nr table_fields=school1,school2,faculty,degree,field,speciality,main_speciality,in_progress,dnf,obtain_language,start,end,end_date,diploma_nr
	@caption Haridusk&auml;ik

	@property education_edit_2 type=releditor store=no mode=manager2 reltype=RELTYPE_EDUCATION_2 props=school1,school2,faculty,degree,field,speciality,main_speciality,in_progress,dnf,obtain_language,start,end,end_date,diploma_nr table_fields=school1,school2,faculty,degree,field,speciality,main_speciality,in_progress,dnf,obtain_language,start,end,end_date,diploma_nr
	@caption Haridusk&auml;ik 2

------------------------------------------------------------------

@groupinfo add_edu caption="T&auml;ienduskoolitus" parent=cv

@property add_edu_edit type=releditor store=no mode=manager2 reltype=RELTYPE_ADD_EDUCATION props=org,field,time,time_text,length_hrs,length table_fields=org,field,time,time_text,length_hrs,length group=add_edu
------------------------------------------------------------------

@groupinfo orgs caption="Organisatoorne kuuluvus" parent=cv submit=no

@property org_edit type=releditor store=no mode=manager2 reltype=RELTYPE_COMPANY_RELATION props=org,start,end,add_info table_fields=org,start,end,add_info group=orgs

------------------------------------------------------------------

@groupinfo recommends caption="Soovitajad" parent=cv
@default group=recommends

	@property recommends_edit type=releditor store=no mode=manager2 reltype=RELTYPE_RECOMMENDATION props=person,relation,phones,emails,org,profession,contact_lang table_fields=person,relation,phones,emails,org,profession,contact_lang

------------------------------------------------------------------

@groupinfo addinfo_new caption="Muud oskused" parent=cv
@default group=addinfo_new
@default table=objects

	@property add_info_tlb type=toolbar no_caption=1 store=no

	@property mlang type=objpicker clid=CL_CRM_LANGUAGE table=kliendibaas_isik field=mlang
	@caption Emakeel

	@property other_mlang type=textbox field=other_mlang table=kliendibaas_isik
	@caption Muu emakeel

	@property skills_lang_tbl type=table store=no
	@caption Keeleoskus

	@property skills_tbl type=table store=no
	@caption Oskused

	@property skills_releditor1 type=releditor mode=manager2 reltype=RELTYPE_SKILL_LEVEL props=skill,level,other table_fields=skill,level store=no
	@caption Oskused releditor1

	@property skills_releditor2 type=releditor mode=manager2 reltype=RELTYPE_SKILL_LEVEL2 props=skill,level,other table_fields=skill,level store=no
	@caption Oskused releditor2

	@property skills_releditor3 type=releditor mode=manager2 reltype=RELTYPE_SKILL_LEVEL3 props=skill,level,other table_fields=skill,level store=no
	@caption Oskused releditor3

	@property skills_releditor4 type=releditor mode=manager2 reltype=RELTYPE_SKILL_LEVEL4 props=skill,level,other table_fields=skill,level store=no
	@caption Oskused releditor4

	@property skills_releditor5 type=releditor mode=manager2 reltype=RELTYPE_SKILL_LEVEL5 props=skill,level,other table_fields=skill,level store=no
	@caption Oskused releditor5

	@property languages_releditor type=releditor mode=manager2 reltype=RELTYPE_LANGUAGE_SKILL props=language,talk,understand,write,other table_fields=language,talk,understand,write store=no
	@caption Keeled releditor

	@property drivers_license type=select multiple=1 field=drivers_license_2 table=kliendibaas_isik
	@caption Autojuhiload

	@property dl_can_use type=checkbox ch_value=1 table=kliendibaas_isik
	@caption Kas v&otilde;imalik kasutada isiklikku autot t&ouml;&ouml;eesm&auml;rkidel

	@property addinfo type=textarea table=kliendibaas_isik field=addinfo
	@caption Muud oskused

------------------------------------------------------------------

@groupinfo work caption="T&ouml;&ouml;"

@groupinfo experiences caption="T&ouml;&ouml;kogemus" parent=work
@default group=experiences

	@property previous_job_edit type=releditor store=no mode=manager2 reltype=RELTYPE_PREVIOUS_JOB props=org,section2,profession,field,start,end,tasks,load,salary,salary_currency,benefits,directive_link,directive,contract_stop table_fields=org,section2,profession,field,start,end,tasks,load,salary,salary_currency,benefits,directive_link,directive,contract_stop
	@caption Endised t&ouml;&ouml;kohad

	@property previous_praxis_edit type=releditor store=no mode=manager2 reltype=RELTYPE_PREVIOUS_PRAXIS props=org,section2,profession,field,start,end,tasks,load,salary,salary_currency,benefits,directive_link,directive,contract_stop table_fields=org,section2,profession,field,start,end,tasks,load,salary,salary_currency,benefits,directive_link,directive,contract_stop
	@caption Praktikakogemus

	@property current_job_edit type=releditor store=no mode=manager2 reltype=RELTYPE_CURRENT_JOB props=org,section2,profession,field,start,end,tasks,load,salary,salary_currency,benefits,directive_link,directive,contract_stop table_fields=org,section2,profession,field,start,end,tasks,load,salary,salary_currency,benefits,directive_link,directive,contract_stop
	@caption Praegused t&ouml;&ouml;kohad

------------------------------------------------------------------

@groupinfo work_projects caption="Projektid" parent=work

	@property work_projects group=work_projects type=table store=no no_caption=1
	@caption Projektid

	@property work_projects_tasks group=work_projects type=hidden no_caption=1 field=meta method=serialize

------------------------------------------------------------------

@groupinfo work_wanted caption="Soovitud t&ouml;&ouml;" parent=work submit=no
@default group=work_wanted

	@property jobs_wanted_edit type=releditor mode=manager2 reltype=RELTYPE_WORK_WANTED store=no props=field,job_type,professions_rels,professions,load,pay,work_by_schedule,work_at_night,ready_for_errand,location,location_2,location_text,start_working,additional_skills,handicaps,hobbies_vs_work,addinfo table_fields=field,job_type,professions_rels,professions,load,pay,work_by_schedule,work_at_night,ready_for_errand,location,location_2,location_text,start_working,additional_skills,handicaps,hobbies_vs_work,addinfo

------------------------------------------------------------------

@groupinfo candidate caption="Kandideerimised" parent=work submit=no
@default group=candidate

	@property candidate_tb type=toolbar no_caption=1 store=no

	@property candidate_table type=table no_caption=1

------------------------------------------------------------------

@groupinfo skills caption="P&auml;devused" parent=work submit=no
@default group=skills

	@property skills_tb type=toolbar no_caption=1 store=no
	@property skills_table type=table no_caption=1 store=no

@groupinfo atwork caption="T&ouml;&ouml;ajad" parent=work submit=no
@default group=atwork

	@property atwork_table type=text no_caption=1 store=no

------------------------------------------------------------------

@groupinfo overview caption="Tegevused"
@groupinfo all_actions caption="K&otilde;ik" parent=overview submit=no
@groupinfo calls caption="K&otilde;ned" parent=overview submit=no
@groupinfo meetings caption="Kohtumised" parent=overview submit=no
@groupinfo tasks caption="Toimetused" parent=overview submit=no

@property org_actions type=calendar no_caption=1 group=all_actions viewtype=relative
@caption org_actions

@property org_calls type=calendar no_caption=1 group=calls viewtype=relative
@caption K&otilde;ned

@property org_meetings type=calendar no_caption=1 group=meetings viewtype=relative
@caption Kohtumised

@property org_tasks type=calendar no_caption=1 group=tasks viewtype=relative
@caption Toimetused

------------------------------------------------------------------

@groupinfo data caption="Andmed"
@default group=data

@property correspond_address type=relpicker reltype=RELTYPE_CORRESPOND_ADDRESS
@caption Kirjavahetuse aadress

@property fake_phone type=textbox user=1
@caption Telefon

@property fake_fax type=textbox user=1
@caption Faks

@property fake_mobile type=textbox user=1
@caption Mobiiltelefon

@property fake_skype type=textbox user=1
@caption Skype

@property fake_email type=textbox user=1
@caption E-post

// fake address props here, so we can write to them and they go to the real address property
@property fake_address_address type=textbox user=1
@caption Aadress

@property fake_address_postal_code type=textbox user=1
@caption Postiindeks

@property fake_address_city type=textbox user=1
@caption Linn

@property fake_address_city_relp type=relpicker reltype=RELTYPE_FAKE_CITY automatic=1
@caption Linn

@property fake_address_county type=textbox user=1
@caption Maakond

@property fake_address_county_relp type=relpicker reltype=RELTYPE_FAKE_COUNTY automatic=1
@caption Maakond

@property fake_address_country type=textbox user=1
@caption Riik

@property fake_address_country_relp type=relpicker reltype=RELTYPE_FAKE_COUNTRY automatic=1
@caption Riik




------------------------------------------------------------------

@groupinfo my_stats caption="Minu statistika" submit=no submit_method=get
@default group=my_stats

@property stats_s_from type=date_select store=no
@caption Alates

@property stats_s_to type=date_select store=no
@caption Kuni

@property stats_s_time_sel type=select store=no
@caption Ajavahemik

@property stats_s_cust type=textbox store=no
@caption Klient

@property stats_s_type type=select store=no
@caption Vaade

@property stats_s_show type=submit no_caption=1
@caption N&auml;ita

@property my_stats type=text store=no no_caption=1

------------------------------------------------------------------

@groupinfo transl caption="T&otilde;lgi"
@default group=transl

@property transl type=callback callback=callback_get_transl store=no
@caption T&otilde;lgi

------------------------------------------------------------------

@groupinfo cv_view caption="CV vaade" submit=no
@default group=cv_view

@property cv_view_tb type=toolbar no_caption=1 store=no

@property cv_view type=text no_caption=1 store=no

----------------------------------------------

@groupinfo ext_sys caption="Siduss&uuml;steemid" parent=general
@default group=ext_sys

	@property ext_sys_t type=table store=no no_caption=1

@groupinfo relatives caption="Sugulased" parent=general
@default group=relatives

	@property relatives type=releditor mode=manager props=person,relation_type,start,end table_fields=person,relation_type,start,end reltype=RELTYPE_FAMILY_RELATION store=no

@groupinfo comments caption="Kommentaarid" parent=general
@default group=comments

	@property comments_tlb type=toolbar no_caption=1 store=no

    @property comments_display type=table store=no
	@caption Sisestatud kommentaarid

    @property comments_title type=text store=no subtitle=1
	@caption Lisa kommentaar

	@property comment_text type=textarea rows=10 store=no
	@caption Kommentaar

@groupinfo sms caption="SMSid" parent=general submit=no
@default group=sms

	@property sms_tbl type=table store=no no_caption=1

*/

/*

CREATE TABLE `kliendibaas_isik` (
  `oid` int(11) NOT NULL default '0',
  `firstname` varchar(50) default NULL,
  `lastname` varchar(50) default NULL,
  `name` varchar(100) default NULL,
  `gender` varchar(10) default NULL,
  `personal_id` bigint(20) default NULL,
  `title` varchar(10) default NULL,
  `nickname` varchar(20) default NULL,
  `messenger` varchar(200) default NULL,
  `birthday` varchar(20) default NULL,
  `social_status` varchar(20) default NULL,
  `spouse` varchar(50) default NULL,
  `children` varchar(100) default NULL,
  `personal_contact` int(11) default NULL,
  `work_contact` int(11) default NULL,
  `rank` int(11) default NULL,
  `digitalID` text,
  `notes` text,
  `pictureurl` varchar(200) default NULL,
  `ext_id_alphanumeric` varchar(25) default NULL,
  `picture` blob,
  PRIMARY KEY  (`oid`),
  UNIQUE KEY `oid` (`oid`)
) TYPE=MyISAM;

*/

/*
@reltype ADDRESS value=1 clid=CL_CRM_ADDRESS
@caption Aadressid

@reltype PICTURE2 value=2 clid=CL_IMAGE
@caption Pilt 2

@reltype PICTURE value=3 clid=CL_IMAGE
@caption Pilt

reltype BACKFORMS value=4 clid=CL_PILOT
caption Tagasiside vorm

reltype CHILDREN value=5 clid=CL_CRM_PERSON
caption Lapsed

@reltype WORK value=6 clid=CL_CRM_COMPANY
@caption T&ouml;&ouml;koht

@reltype RANK value=7 clid=CL_CRM_PROFESSION
@caption Ametinimetus

@reltype PERSON_MEETING value=8 clid=CL_CRM_MEETING
@caption Kohtumine

@reltype PERSON_CALL value=9 clid=CL_CRM_CALL
@caption K&otilde;ne

@reltype PERSON_TASK value=10 clid=CL_TASK
@caption Toimetus

@reltype EMAIL value=11 clid=CL_ML_MEMBER
@caption E-post

@reltype URL value=12 clid=CL_EXTLINK
@caption Veebiaadress

@reltype PHONE value=13 clid=CL_CRM_PHONE
@caption Telefon

#reltype USER_DATA value=15
#caption Andmed

@reltype ORG_RELATION value=16 clid=CL_CRM_PERSON_WORK_RELATION
@caption Organisatoorne kuuluvus

@reltype RECOMMENDS value=17 clid=CL_CRM_PERSON
@caption Soovitaja

@reltype ORDER value=20 clid=CL_SHOP_ORDER
@caption Tellimus

@reltype SECTION value=21 clid=CL_CRM_SECTION
@caption &Uuml;ksus

//parem nimi teretulnud, person on cl_crm_company jaox
//kliendihaldur
@reltype HANDLER value=22 clid=CL_CRM_COMPANY

@reltype EDUCATION value=23 clid=CL_CRM_PERSON_EDUCATION
@caption Haridus

@reltype ADD_EDUCATION value=24 clid=CL_CRM_PERSON_ADD_EDUCATION
@caption T&auml;iendkoolitus

@reltype LANGUAGE_SKILL value=27 clid=CL_CRM_PERSON_LANGUAGE
@caption Keeleoskus

@reltype DESCRIPTION_DOC value=34 clid=CL_DOCUMENT,CL_MENU
@caption Kirjelduse dokument

reltype FRIEND value=35 clid=CL_CRM_PERSON
caption S&otilde;ber

reltype FAVOURITE value=36 clid=CL_CRM_PERSON
caption Lemmik

reltype MATCH value=37 clid=CL_CRM_PERSON
caption V&auml;ljavalitu

reltype BLOCKED value=38 clid=CL_CRM_PERSON
caption blokeeritud

reltype IGNORED value=39 clid=CL_CRM_PERSON
caption ignoreeritud

reltype FRIEND_GROUPS value=40 clid=CL_META
caption S&otilde;bragrupid

@reltype VACATION value=41 clid=CL_CRM_VACATION
@caption Puhkus

@reltype CONTRACT_STOP value=42 clid=CL_CRM_CONTRACT_STOP
@caption T&ouml;&ouml;lepingu peatamine

@reltype IMPORTANT_PERSON value=43 clid=CL_CRM_PERSON
@caption Kontaktisik

@reltype CLIENT_MANAGER value=44 clid=CL_CRM_PERSON
@caption Kliendihaldur

@reltype LANGUAGE value=45 clid=CL_LANGUAGE
@caption Keel

@reltype DOCS_FOLDER value=46 clid=CL_MENU
@caption Dokumentide kataloog

@reltype SERVER_FILES value=51 clid=CL_SERVER_FOLDER
@caption Failide kataloog serveris

@reltype WAGE_DOC value=52 clid=CL_DOCUMENT
@caption Palgainfo

@reltype HAS_SKILL value=53 clid=CL_PERSON_HAS_SKILL
@caption P&auml;devus

@reltype FAX value=54 clid=CL_CRM_PHONE
@caption Faks

@reltype VARUSER1 value=55 clid=CL_META
@caption kasutajadefineeritud muutuja 1

@reltype VARUSER2 value=56 clid=CL_META
@caption kasutajadefineeritud muutuja 2

@reltype VARUSER3 value=57 clid=CL_META
@caption kasutajadefineeritud muutuja 3

@reltype CORRESPOND_ADDRESS value=58 clid=CL_CRM_ADDRESS
@caption Aadressid

@reltype VARUSER4 value=59 clid=CL_META
@caption kasutajadefineeritud muutuja 4

@reltype VARUSER5 value=60 clid=CL_META
@caption kasutajadefineeritud muutuja 5

@reltype VARUSER6 value=61 clid=CL_META
@caption kasutajadefineeritud muutuja 6

@reltype VARUSER7 value=62 clid=CL_META
@caption kasutajadefineeritud muutuja 7

@reltype VARUSER8 value=63 clid=CL_META
@caption kasutajadefineeritud muutuja 8

@reltype VARUSER9 value=64 clid=CL_META
@caption kasutajadefineeritud muutuja 9

@reltype VARUSER10 value=65 clid=CL_META
@caption kasutajadefineeritud muutuja 10

@reltype PREVIOUS_JOB value=66 clid=CL_CRM_PERSON_WORK_RELATION
@caption Eelnev t&ouml;&ouml;kogemus

@reltype CURRENT_JOB value=67 clid=CL_CRM_PERSON_WORK_RELATION
@caption Praegune t&ouml;&ouml;koht

@reltype CURRENCY value=68 clid=CL_CURRENCY
@caption valuuta

@reltype REFERAL_TYPE value=69 clid=CL_META
@caption sissetuleku meetod

@reltype CONTACT_PERSON value=70 clid=CL_CRM_PERSON
@caption Kontaktisik

@reltype BUYER_REFERAL_TYPE value=71 clid=CL_META
@caption sissetuleku meetod

@reltype BANK_ACCOUNT value=72 clid=CL_CRM_BANK_ACCOUNT
@caption arveldusarve

@reltype NATIONALITY value=73 clid=CL_NATIONALITY
@caption rahvus

@reltype CITIZENSHIP value=74 clid=CL_CITIZENSHIP
@caption kodakondsus

@reltype DEGREE value=75 clid=CL_CRM_DEGREE
@caption Kraad

@reltype EDUCATION_LEVEL value=76 clid=CL_CRM_PERSON_EDUCATION_LEVEL
@caption Haridustase

@reltype WORK_WANTED value=77 clid=CL_PERSONNEL_MANAGEMENT_JOB_WANTED
@caption Soovitud t&ouml;&ouml;

@reltype CATEGORY value=80 clid=CL_CRM_CATEGORY
@caption List

@reltype SKILL_LEVEL value=81 clid=CL_CRM_SKILL_LEVEL
@caption Oskuse tase

@reltype SKILL_LEVEL2 value=88 clid=CL_CRM_SKILL_LEVEL
@caption Oskuse tase

@reltype SKILL_LEVEL3 value=89 clid=CL_CRM_SKILL_LEVEL
@caption Oskuse tase

@reltype SKILL_LEVEL4 value=90 clid=CL_CRM_SKILL_LEVEL
@caption Oskuse tase

@reltype SKILL_LEVEL5 value=91 clid=CL_CRM_SKILL_LEVEL
@caption Oskuse tase

@reltype COMPANY_RELATION value=82 clid=CL_CRM_COMPANY_RELATION
@caption Organisatoorne kuuluvus

@reltype PERSONNEL_MANAGEMENT value=83 clid=CL_PERSONNEL_MANAGEMENT
@caption Personalikeskkond

@reltype CV_FILE value=84 clid=CL_FILE
@caption CV failina

@reltype FAMILY_RELATION value=85 clid=CL_CRM_FAMILY_RELATION
@caption Sugulusside

@reltype COMMENT value=86 clid=CL_COMMENT
@caption Kommentaar

@reltype RECOMMENDATION value=87 clid=CL_CRM_RECOMMENDATION
@caption Soovitus

@reltype EDUCATION_2 value=92 clid=CL_CRM_PERSON_EDUCATION
@caption Haridus (teine releditor)

@reltype PREVIOUS_PRAXIS value=93 clid=CL_CRM_PERSON_WORK_RELATION
@caption Eelnev praktikakogemus

@reltype FAKE_COUNTY value=94 clid=CL_CRM_COUNTY
@caption Fake county

@reltype FAKE_CITY value=95 clid=CL_CRM_CITY
@caption Fake city

@reltype FAKE_COUNTRY value=96 clid=CL_CRM_COUNTRY
@caption Fake country

@reltype ADDRESS_ALT value=97 clid=CL_ADDRESS
@caption Aadressid

*/

define("CRM_PERSON_USECASE_COWORKER", "coworker");
define("CRM_PERSON_USECASE_CLIENT", "s_p");
define("CRM_PERSON_USECASE_CLIENT_EMPLOYEE", "customer_employer");

class crm_person extends class_base
{
	const AW_CLID = 145;

	function crm_person()
	{
		$this->init(array(
			"tpldir" => "crm/person",
			"clid" => CL_CRM_PERSON
		));

		$this->trans_props = array(
			"udef_ta1", "udef_ta2", "udef_ta3", "udef_ta4", "udef_ta5"
		);
		$this->edulevel_options = array(
			0 => t("--vali--"),
			1 => t("P&otilde;hiharidus"),
			2 => t("Keskharidus"),
			3 => t("Kutsekeskharidus"),
			4 => t("Kesk-eriharidus"),
			5 => t("Kutsek&otilde;rgharidus"),
			6 => t("Rakendusk&otilde;rgharidus"),
			8 => t("K&otilde;rghariduse diplom"),
			9 => t("Bakalaureus"),
			10 => t("Magister"),
			11 => t("Doktor"),
			12 => t("Teaduste kandidaat"),
		);
		$this->academic_degree_options = array(
			0 => t("--vali--"),
			1 => t("Bakalaureus"),
			2 => t("Magister"),
			3 => t("Doktor"),
		);
	}

	function callback_on_load($arr)
	{
		crm_person_obj::handle_show_cnt(array(
			"action" => $arr["request"]["action"],
			"id" => $arr["request"]["id"],
		));
	}

	function set_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		$form = &$arr["request"];

		switch($prop["name"])
		{
			case "show_cnt":
				// This property is only set from code.
				$retval = PROP_IGNORE;
				break;

			case "comment_text":
				if(strlen($prop["value"]) > 0)
				{
					$comm = new object;
					$comm->set_parent($arr["obj_inst"]->id());
					$comm->set_class_id(CL_COMMENT);
					$comm->set_prop("commtext", $prop["value"]);
					$comm->set_prop("uname", aw_global_get("uid"));
					$comm->set_prop("ip", $_SERVER['REMOTE_ADDR']);
					$comm->save();
					$arr["obj_inst"]->connect(array(
						"to" => $comm->id(),
						"reltype" => "RELTYPE_COMMENT",
					));
				}
				break;

			case "edulevel":
				if($prop["value"] < 7)
					$arr["obj_inst"]->set_prop("academic_degree", 0);
				break;

			case "skills_tbl":
				foreach($arr["request"]["skills"] as $key => $val)
				{
					if($arr["request"]["skills"][$key] != $arr["request"]["skills_old"][$key])
					{
						$o = obj($key);
						$o->set_prop("level", $val);
						$o->save();
					}
				}
				break;

			case "skills_lang_tbl":
				foreach($arr["request"]["lang"] as $key => $val)
				{
					if($val["talk"] != $arr["request"]["lang_talk"][$key] || $val["understand"] != $arr["request"]["lang_understand"][$key] || $val["write"] != $arr["request"]["lang_write"][$key])
					{
						$o = obj($key);
						$o->set_prop("talk", $val["talk"]);
						$o->set_prop("understand", $val["understand"]);
						$o->set_prop("write", $val["write"]);
						$o->save();
					}
				}
				break;

			case "drivers_license":
				$s = "";
				foreach($prop["value"] as $c)
				{
					$s .= $c.",";
				}
				$prop["value"] = $s;
				$arr["obj_inst"]->set_prop("drivers_license", $prop["value"]);
				return PROP_IGNORE;
				break;

			case "citizenship_table":
				$this->_save_citizenship_table($arr);
				break;

			case "phone":
			case "fax":
			case "url":
			case "email":
			case "aw_bank_account":
				return PROP_IGNORE;

			case "rank":
				$arr["obj_inst"]->set_prop("rank", $arr["request"]["rank"]);
				return PROP_IGNORE;

			case "cedit_phone_tbl":
			case "cedit_telefax_tbl":
			case "cedit_url_tbl":
			case "cedit_email_tbl":
			case "cedit_adr_tbl":
			case "cedit_bank_account_tbl":
//			case "cedit_profession_tbl":
				static $i;
				if (!$i)
				{
					$i = new crm_company_cedit_impl();
				}
				$fn = "_set_".$prop["name"];
				$i->$fn($arr);
				break;

			case "ext_sys_t":
				$this->_save_ext_sys_t($arr);
				break;

			case "firstname":
				if (($arr["new"] || !($tmp = $this->has_user($arr["obj_inst"]))))
				{
					$arr["obj_inst"]->set_meta("no_create_user_yet", true);

					if (strlen(trim($prop["value"])) and !strlen(trim($form["username"])) && $arr["request"]["password"] != "")
					{
						$cl_user_creator = new crm_user_creator();
						$errors = $cl_user_creator->get_uid_for_person($arr["obj_inst"], true);

						if ($errors)
						{
							$prop["error"] = $errors . t(' Palun sisestage nimi loodava kasutaja jaoks lahtrisse "Kasutaja"');
							return PROP_ERROR;
						}
						else
						{
							$arr["obj_inst"]->set_meta("no_create_user_yet", NULL);
						}
					}
				}
				break;

			case "username":
				if (($arr["new"] || !($tmp = $this->has_user($arr["obj_inst"]))) and strlen(trim($prop["value"])))
				{
					$arr["obj_inst"]->set_meta("no_create_user_yet", true);
					$arr["obj_inst"]->set_meta("tmp_crm_person_username", $prop["value"]);
					$cl_user_creator = new crm_user_creator();
					$errors = $cl_user_creator->get_uid_for_person($arr["obj_inst"], true);

					if ($errors)
					{
						$prop["error"] = $errors;
						return PROP_ERROR;
					}
					else
					{
						$arr["obj_inst"]->set_meta("no_create_user_yet", NULL);
					}
				}
				break;

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "picture":
			case "picture2":
				if(!$arr["new"])
				{
					$this->_resize_img($arr);
				}
				break;

			case "address":
				return PROP_IGNORE;

			//kliendisuhte teema
			case "contact_person":
				$arr["prop"]["value"] = $arr["obj_inst"]->id();
			case "priority":
			case "bill_due_date_days":
			case "bill_penalty_pct":
			case "referal_type":
			case "client_manager":

				if($prop["name"] == "bill_penalty_pct") $prop["value"] = str_replace(",", ".", $prop["value"]);
				$this->set_cust_rel_data($arr);
				break;

			case "cust_contract_date":
			// save to rel
				if (($rel = $this->get_cust_rel($arr["obj_inst"])))
				{
					$rel->set_prop($prop["name"], date_edit::get_timestamp($prop["value"]));
					$rel->save();
				}
				break;

			case "buyer_contract_date":
				$co = get_current_company();
				if (($rel = $this->get_cust_rel($co , 0 , $arr["obj_inst"])))
				{
					$rel->set_prop("buyer_contract_date", date_edit::get_timestamp($prop["value"]));
					$rel->save();
				}
				break;
			case "buyer_contact_person":
				$arr["prop"]["value"] = $arr["obj_inst"]->id();
			case "buyer_priority":
			case "buyer_contract_creator":
				$this->set_buyer_rel_data($arr);
				break;

			case "cust_contract_creator":
			case "bill_due_days":
				// save to rel
				if (($rel = $this->get_cust_rel($arr["obj_inst"])))
				{
					$rel->set_prop($prop["name"] == "bill_due_days" ? "bill_due_date_days" : $prop["name"], $prop["value"]);
					$rel->save();
				}
				break;

			case "co_is_cust":
			case "co_is_buyer":
				$fn = "_set_".$prop["name"];
					$this->$fn($arr);
				break;
		};
		return $retval;
	}

	//V6tsin k6ik WORKER jne jne seostamise 2ra, salvestamisel ei ole vast vaja vana systeemi toimimist
	function _set_work_tbl($arr)
	{
		foreach($arr["request"]["work_tbl"] as $wr_id => $data)
		{
			$wr = obj($wr_id, array(), CRM_PERSON_WORK_RELATION);
			$wr->set_prop("org", $data["org"]);
			$wr->set_prop("section", $data["sec"]);
			$wr->set_prop("profession", $data["pro"]);
			$wr->save();
		}
	}

	function _get_sms_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "number",
			"caption" => t("Number"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "message",
			"caption" => t("S&otilde;num"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "mobi_ans",
			"caption" => t("Mobi vastus"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center",
			"sortable" => 1,
		));
		$phones = $arr["obj_inst"]->phones();
		foreach(connection::find(array("to" => $phones->ids(), "from.class_id" => CL_SMS_SENT, "type" => "RELTYPE_PHONE")) as $conn)
		{
			$to = $conn->to();
			$sms_arr = $to->connections_to(array("from.class_id" => CL_SMS, "type" => "RELTYPE_SMS_SENT"));
			foreach($sms_arr as $cn)
			{
				$sms = $cn->from();
				break;
			}
			$t->define_data(array(
				"number" => $to->prop("phone.name"),
				"time" => date("Y-m-d H:i:s", $to->created()),
				"timestamp" => $to->created(),
				"mobi_ans" => $to->comment,
				"message" => $sms->comment,
			));
		}
		$t->set_default_sortby("timestamp");
	}

	function _get_comments_tlb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "delete_obj",
			"confirm" => t("Oled kindel, et kustutada?"),
		));
	}

	function _get_comments_display($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel",
		));
		$t->define_field(array(
			"name" => "uid",
			"caption" => t("Autor"),
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "text",
			"caption" => t("Kommentaar"),
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Loomisaeg"),
			"sortable" => 1,
		));
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_COMMENT")) as $conn)
		{
			$comm = $conn->to();
			$t->define_data(array(
				"oid" => $comm->id(),
				"uid" => $comm->prop("uname"),
				"text" => $comm->prop("commtext"),
				"time" => get_lc_date($comm->created()),
			));
		}
	}

	function _get_cv_file_url($arr)
	{
		// if image is uploaded
		$o = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_CV_FILE");
		if (!$o)
		{
			return PROP_IGNORE;
		}

		$file_inst = new file();
		$arr["prop"]["value"] = html::img(array(
				"url" => icons::get_icon_url(CL_FILE),
			)).html::href(array(
			"caption" => $o->name(),
			"url" => $file_inst->get_url($o->id(), $o->name()),
		));
	}

	function _get_add_info_tlb($arr)
	{
		$personnel_management_inst = new personnel_management();
		$pm = obj($personnel_management_inst->get_sysdefault());

		$t = $arr["prop"]["vcl_inst"];
		$t->add_menu_button(array(
			"name" => "new_skill",
			"img" => "new.gif",
		));
		$t->add_save_button();
		$t->add_sub_menu(array(
			"parent" => "new_skill",
			"name" => "new_lang_skill",
			"text" => t("Keeleoskus"),
		));
		foreach($personnel_management_inst->get_languages() as $lkey => $lname)
		{
			$t->add_menu_item(array(
				"parent" => "new_lang_skill",
				"name" => "new_lang_skill_".$lkey,
				"text" => $lname,
				"link" => $this->mk_my_orb("add_lang_skill", array("lang_id" => $lkey, "id" => $arr["obj_inst"]->id(), "return_url" => get_ru())),
			));
		}

		$skill_manager_inst = new crm_skill_manager();
		$sm_id = $pm->prop("skill_manager");
		$skills = $skill_manager_inst->get_skills(array("id" => $sm_id));
		foreach($skills as $id => $data)
		{
			$parent = ($id == $sm_id) ? "new_skill" : "new_skill_".$id;
			foreach($data as $id2 => $name)
			{
				if(array_key_exists($id2, $skills) || $name["subheading"])
				{
					$t->add_sub_menu(array(
						"name" => "new_skill_".$id2,
						"text" => $name["name"],
						"parent" => $parent,
					));
				}
				// If it's not a subheading, we have to be able to click on it. Even if it's a parent skill.
				if(!$name["subheading"])
				{
					$t->add_menu_item(array(
						"parent" => "new_skill_".$id,
						"name" => "new_skill_".$id2,
						"text" => $name["name"],
						"link" => $this->mk_my_orb("add_skill", array("skill_id" => $id2, "id" => $arr["obj_inst"]->id(), "return_url" => get_ru())),
					));
				}
			}
		}
		$t->add_delete_button();
	}

	function _get_skills_lang_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "select",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "lang",
			"caption" => t("Keel"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "talk",
			"caption" => t("R&auml;&auml;gin"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "understand",
			"caption" => t("Saan aru"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "write",
			"caption" => t("Kirjutan"),
			"align" => "center",
		));
		$lang_ops[0] = t("--vali--");
		$lang_ops += get_instance("crm_person_language")->lang_lvl_options;
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_LANGUAGE_SKILL")) as $conn)
		{
			$to = $conn->to();
			$t->define_data(array(
				"oid" => $to->id(),
				"lang" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $to->id(), "return_url" => get_ru()), CL_CRM_PERSON_LANGUAGE),
					"caption" => $to->prop("language.name")
				)),
				"talk" => html::select(array(
					"name" => "lang[".$to->id()."][talk]",
					"options" => $lang_ops,
					"selected" => $to->prop("talk"),
				)).html::hidden(array(
					"name" => "lang_talk[".$to->id()."]",
					"value" => $to->prop("talk"),
				)),
				"understand" => html::select(array(
					"name" => "lang[".$to->id()."][understand]",
					"options" => $lang_ops,
					"selected" => $to->prop("understand"),
				)).html::hidden(array(
					"name" => "lang_understand[".$to->id()."]",
					"value" => $to->prop("understand"),
				)),
				"write" => html::select(array(
					"name" => "lang[".$to->id()."][write]",
					"options" => $lang_ops,
					"selected" => $to->prop("write"),
				)).html::hidden(array(
					"name" => "lang_write[".$to->id()."]",
					"value" => $to->prop("write"),
				)),
			));
		}
	}

	function _get_skills_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "select",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "skill",
			"caption" => t("Oskus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "level",
			"caption" => t("Tase"),
			"align" => "center",
		));
		foreach($arr["obj_inst"]->connections_from(array("type" => array("RELTYPE_SKILL_LEVEL", "RELTYPE_SKILL_LEVEL2", "RELTYPE_SKILL_LEVEL3", "RELTYPE_SKILL_LEVEL4", "RELTYPE_SKILL_LEVEL5"))) as $conn)
		{
			$to = $conn->to();

			if(!isset($parents[$to->prop("skill.parent")]))
			{
				$parent_obj = obj($to->prop("skill.parent"));
				$parents[$to->prop("skill.parent")] = $parent_obj->name();
			}
			$parent = $parents[$to->prop("skill.parent")];

			if(!isset($options[$to->prop("skill.lvl_meta")]))
			{
				$ol = new object_list(array(
					"class_id" => CL_META,
					"parent" => $to->prop("skill.lvl_meta"),
					"status" => object::STAT_ACTIVE,
					"lang_id" => array(),
					"sort_by" => "jrk",
				));
				$options[$to->prop("skill.lvl_meta")][0] = t("--vali--");
				$options[$to->prop("skill.lvl_meta")] += $ol->names();
			}

			$level = ($to->prop("skill.lvl")) ? html::select(array(
				"name" => "skills[".$to->id()."]",
				"options" => $options[$to->prop("skill.lvl_meta")],
				"selected" => $to->prop("level"),
			)) : "";
			$t->define_data(array(
				"oid"  => $to->id(),
				"skill" => html::href(array(
					"url" => $this->mk_my_orb("change", array("id" => $to->id(), "return_url" => get_ru()), CL_CRM_SKILL_LEVEL),
					"caption" => $to->prop("skill.name")
				)),
				"level" => $level.html::hidden(array(
					"name" => "skills_old[".$to->id()."]",
					"value" => $to->prop("level"),
				)),
				"parent" => $parent,
			));
		}
		$t->set_rgroupby(array(
			"parent" => "parent",
		));
	}

	function _get_recommends_tlb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_CRM_RECOMMENDATION), $arr["obj_inst"]->id(), 87);		// RELTYPE_RECOMMENDATION
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "delete_obj",
			"confirm" => t("Oled kindel, et kustutada?"),
		));
	}

	function _get_recommends_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "person",
			"caption" => t("Soovitav isik"),
			"align" => "center",
			"sortable" => 1,
		));
		$t->define_field(array(
			"name" => "phones",
			"caption" => t("Telefonid"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "emails",
			"caption" => t("E-postiaadressid"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "candidates",
			"caption" => t("Kandideerimised, millega soovitaja on seotud"),
			"align" => "center"
		));

		// Waitin for bug #227455.
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_RECOMMENDATION")) as $conn)
		{
			$rec = $conn->to();
			if($this->can("view", $rec->prop("person")))
			{
				$p_obj = obj($rec->prop("person"));
				$phones = "";
				foreach($p_obj->phones()->ids() as $id)
				{
					if(strlen($phones) > 0)
					{
						$phones .= ", ";
					}
					$phones .= html::obj_change_url($id);
				}
				$emails = "";
				foreach($p_obj->emails()->ids() as $id)
				{
					if(strlen($emails) > 0)
					{
						$emails .= ", ";
					}
					$emails .= html::obj_change_url($id);
				}
			}
			$candidates = "";
			foreach($rec->connections_to(array("from.class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE, "type" => "RELTYPE_RECOMMENDATION")) as $cn)
			{
				$cand = $cn->from();
				if(strlen($candidates) > 0)
				{
					$candidates .= ", ";
				}
				$candidates .= html::obj_change_url($cand->prop("job_offer"));
			}
			$t->define_data(array(
				"oid" => $rec->id(),
				"person" => html::obj_change_url($p_obj),
				"phones" => $phones,
				"emails" => $emails,
				"candidates" => $candidates,
			));
		}
	}

	function _get_cust_contract_creator($arr)
	{
		// list of all persons in my company
		$u = new user();
		$co = $u->get_current_company();
		$arr["prop"]["options"] = $this->get_employee_picker(obj($co), true);
		if (($rel = $this->get_cust_rel($arr["obj_inst"])))
		{
			$arr["prop"]["value"] = $rel->prop($arr["prop"]["name"]);
		}

		if (!isset($arr["prop"]["options"][$arr["prop"]["value"]]) && $this->can("view", $arr["prop"]["value"]))
		{
			$v = obj($arr["prop"]["value"]);
			$arr["prop"]["options"][$arr["prop"]["value"]] = $v->name();
		}
	}

	function _get_buyer_contract_creator($arr)
	{
		// list of all persons in my company
		$u = new user();
		$co = $u->get_current_company();
		$arr["prop"]["options"] = $this->get_employee_picker(obj($co), true);
		if (($rel = $this->get_cust_rel(obj($co) , 0 , $arr["obj_inst"])))
		{
			$arr["prop"]["value"] = $rel->prop($arr["prop"]["name"]);
		}

		if (!isset($arr["prop"]["options"][$arr["prop"]["value"]]) && $this->can("view", $arr["prop"]["value"]))
		{
			$v = obj($arr["prop"]["value"]);
			$arr["prop"]["options"][$arr["prop"]["value"]] = $v->name();
		}
	}

	function get_employee_picker($co = null, $add_empty = false, $important_only = false)
	{
		$coi = get_instance(CL_CRM_COMPANY);
		if (!$co)
		{
			$u = new user();
			$co = obj($u->get_current_company());
		}

		static $cache;
		if (isset($cache[$co->id()][$add_empty][$important_only]))
		{
			return $cache[$co->id()][$add_empty][$important_only];
		}

		if ($add_empty)
		{
			$res = array("" => t("--vali--"));
		}
		else
		{
			$res = array();
		}
		$coi->get_all_workers_for_company($co, $res);
		if (!count($res))
		{
			$cache[$co->id()][$add_empty][$important_only] = $res;
			return $res;
		}

		if ($important_only)
		{
			// filter out my important persons
			$u = new user();
			$p = obj($u->get_current_person());

			$tmp = array();
			foreach($p->connections_from(array("type" => "RELTYPE_IMPORTANT_PERSON")) as $c)
			{
				if ($res[$c->prop("to")])
				{
					$tmp[$c->prop("to")] = $c->prop("to");
				}
			}
			$res = $tmp;
		}

		if (count($res))
		{
			$ol = new object_list(array("oid" => $res, "sort_by" => "objects.name", "lang_id" => array(), "site_id" => array()));
		}
		else
		{
			$ol = new object_list();
		}
		$res = ($add_empty ? array("" => t("--vali--")) : array()) +  $ol->names();
		uasort($res, array(&$this, "__person_name_sorter"));
		$cache[$co->id()][$add_empty][$important_only] = $res;
		return $res;
	}

	function get_property($arr)
	{
		$data = &$arr["prop"];
		$retval = PROP_OK;
		$personnel_management_inst = get_instance(CL_PERSONNEL_MANAGEMENT);
		switch($data["name"])
		{
			case "mails_s_name":
			case "mails_s_content":
			case "mails_s_customer":
				$data["value"] = $arr["request"][$data["name"]];
				break;
			case "birthday":
				$pm = obj(get_instance(CL_PERSONNEL_MANAGEMENT)->get_sysdefault());
				if($pm->yob_from)
				{
					if((int)$pm->yob_from < 0)
					{
						$pm->yob_from = date("Y") + (int)$pm->yob_from;
					}
					$data["year_from"] = $pm->yob_from;
				}
				if($pm->yob_to)
				{
					if((int)$pm->yob_to < 0)
					{
						$pm->yob_to = date("Y") + (int)$pm->yob_to;
					}
					$data["year_to"] = $pm->yob_to;
				}
				break;

			case "mails_tbl":
				$this->_get_mails_tbl($arr);
				break;
			case "current_job_edit":
				if($arr["obj_inst"]->not_working)
				{
					return PROP_IGNORE;
				}
				break;

			case "academic_degree":
				// If edulevel is lower than 'k8rgharidus'
				if($arr["obj_inst"]->prop("edulevel") < 7)
				{
					return PROP_IGNORE;
				}
				$data["options"] = $this->academic_degree_options;
				break;

			case "edulevel":
				$data["options"] = $this->edulevel_options;
				break;

			case "udef_skills_1_re":
			case "udef_skills_2_re":
			case "udef_skills_3_re":
			case "udef_skills_4_re":
			case "udef_skills_5_re":
				$skills = $personnel_management_inst->get_udef_skills(array("id" => $personnel_management_inst->get_sysdefault()));
				$i = substr($data["name"], 12, 1);
				$skill = $skills[$i];
				if(is_oid($skill["skill"]))
				{
					$data["reltype"] = "RELTYPE_SKILL_LEVEL_".$i;
					$props = array("skill");
					if($skill["levels"])
						$props[] = "name";
					$data["props"] = $props;
					$data["table_fields"] = $props;
					$data["mode"] = "manager";
					$data["skill.options"] = $this->get_skills($skill["skill"]);
				}
				break;

			case "udef_skills_1_tb":
			case "udef_skills_2_tb":
			case "udef_skills_3_tb":
			case "udef_skills_4_tb":
			case "udef_skills_5_tb":
				$this->_get_udef_skills_tb($arr);
				break;

			case "udef_skills_1":
			case "udef_skills_2":
			case "udef_skills_3":
			case "udef_skills_4":
			case "udef_skills_5":
				// The cfgform should still override this.
				if($data["caption"] == $data["orig_caption"])
				{
					$skills = $personnel_management_inst->get_udef_skills(array("id" => $personnel_management_inst->get_sysdefault()));
					$skill = $skills[substr($data["name"], 12, 1)];
					if(is_oid($skill["skill"]))
					{
						$skill_obj = obj($skill["skill"]);
						$data["caption"] = $skill_obj->name();
					}
				}
				break;

			case "mlang":
				$pm = obj($personnel_management_inst->get_sysdefault());
				$data["options"][0] = t("--vali--");
				foreach($personnel_management_inst->get_languages() as $lkey => $lname)
				{
					$data["options"][$lkey] = $lname;
				}
				$data["options"]["other"] = t("muu keel");
				$data["onchange"] = "if(this.value == 'other') { $('#other_mlang').parent().parent().show(); } else { $('#other_mlang').parent().parent().hide(); }";
				break;

			case "drivers_license":
				$data["options"] = $this->drivers_licence_categories();
				$data["value"] = explode(",", $data["value"]);
				break;

			case "jobs_wanted_tb":
				$t = &$data["vcl_inst"];
				$t->add_new_button(array(CL_PERSONNEL_MANAGEMENT_JOB_WANTED), $arr["obj_inst"]->id(), 77);
				$t->add_button(array(
					"name" => "delete",
					"img" => "delete.gif",
					"tooltip" => t("Kustuta"),
					"action" => "delete_obj",
					"confirm" => t("Oled kindel, et kustutada?"),
				));
				break;

			case "nationality":
				$ol = new object_list(array(
					"site_id" => array(),
					"lang_id" => array(),
					"class_id" => CL_NATIONALITY,
				));
				$data["options"] = array("" => " ")  + $ol->names();
				break;

			case "org_section":
				if(!is_array($data["value"]) && is_array(unserialize($data["value"])))
				{
					$data["value"] = unserialize($data["value"]);
				}
				break;

			case "work_projects":
				$t = &$data["vcl_inst"];
				$t->define_field(array(
					"name" => "select",
					"caption" => t("CV"),
					"width" => "10"
				));
				$t->define_field(array(
					"name" => "project",
					"caption" => t("Projekt"),
					"width" => "300"
				));
				$t->define_field(array(
					"name" => "value",
					"caption" => t("Hind"),
				));
				$t->define_field(array(
					"name" => "role",
					"caption" => t("Roll"),
				));
				$t->define_field(array(
					"name" => "description",
					"caption" => t("&Uuml;lesanded"),
				));


				$tasks = $this->get_work_project_tasks($arr["obj_inst"]->id());
				$i = new user();
				foreach($this->get_person_and_org_related_projects($arr["obj_inst"]->id()) as $oid => $obj)
				{
					$project = html::href(array(
						"caption" => $obj->name(),
						"url" => $this->mk_my_orb("change", array(
							"id" => $oid,
							"return_url" => get_ru(),
						), CL_PROJECT),
					));
					$roles = $this->get_project_roles(array(
						"person" => $arr["obj_inst"]->id(),
						"project" => $oid,
					));
					$roles_url = $this->mk_my_orb("change", array(
						"from_org" => $i->get_current_company(),
						"to_org" => current($obj->prop("orderer")),
						"to_project" => $oid,
						"class" => "crm_role_manager",
					));
					$roles_url = html::href(array(
						"caption" => t("Rollid"),
						"url" => "#",
						"onClick" => "javascript:aw_popup_scroll(\"".$roles_url."\", \"Rollid\", 500, 500);",
					));

					$t->define_data(array(
						"project" => $project,
						"description" => html::textarea(array(
							"name" => "project_tasks[".$oid."]",
							"value" => $tasks[$oid]["task"],
							"rows" => 10,
							"cols" => 100,
						)),
						"value" => ($_t = $obj->prop("proj_price"))?$_t:t("-"),
						"role" => ($_t = join(",", $roles))?$_t." (".$roles_url.")":$roles_url,
						"select" => html::checkbox(array(
							"name" => "project_sel[".$oid."]",
							"checked" => checked($tasks[$oid]["selected"]),
						)),
					));
				}

				break;
			case "work_contact_start":
				if(!$arr["obj_inst"]->prop("work_contact"))
				{
					return PROP_IGNORE;
				}
				break;
			case "address":
				return PROP_IGNORE;

			case "_bd_upg":
				return PROP_IGNORE;

			case "atwork_table":
				$this->_atwork_table($arr);
				break;

			case "skills_tb":
				$this->_skills_tb($arr);
				break;

			case "skills_table":
				$this->_skills_table($arr);
				break;

			case "ct_rel_tb":
				$this->_ct_rel_tb($arr);
				break;

			case "contact_desc_text":
				$data["value"] = $this->get_short_description($arr);
				break;

			case "ext_sys_t":
				$this->_ext_sys_t(&$arr);
				break;

			case "citizenship_table":
				$cit = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CITIZENSHIP"));
				if(!sizeof($cit))
				{
					return PROP_IGNORE;
				}
				$this->_get_citizenship_table(&$arr);
				break;

			case "cv_view_tb":
				$tpl = $arr["request"]["cv_tpl"]?("cv/".basename($v[$arr["request"]["cv_tpl"]])):false;
				$url = $this->mk_my_orb("show_cv", array(
					"cv" => $tpl,
					"id" => $arr["obj_inst"]->id(),
					"die" => "1",
					"cfgform" => get_instance(CL_CFGFORM)->get_sysdefault(array("clid" => CL_CRM_PERSON)),
				));
				$arr["prop"]["toolbar"]->add_button(array(
					"name" => "delete",
					"img" => "preview.gif",
					"tooltip" => t("Popup vaade"),
					"url" => "#",
					"onClick" => "aw_popup_scroll('".$url."','Eelvaade', 900, 900);"
				));
				$ops = array_values($this->get_cv_tpl());
				$pm = obj(get_instance(CL_PERSONNEL_MANAGEMENT)->get_sysdefault());
				$default_cv = ($pm->prop("cv_tpl")) ? str_replace(".tpl", "", basename($pm->prop("cv_tpl"))) : false;
				$default_tpl = array_keys($ops, $default_cv);
				$default_tpl = $default_tpl[0];
				$tpl = isset($arr["request"]["cv_tpl"]) ? $arr["request"]["cv_tpl"] : $default_tpl;
				$arr["prop"]["toolbar"]->add_cdata(html::select(array(
					"name" => "cv_tpl",
					"options" => $ops,
					"value" => $tpl,
				)));
				$arr["prop"]["toolbar"]->add_button(array(
					"name" => "show",
					"img" => "save.gif",
					"tooltip" => t("N&auml;ita"),
					"action" => ""
				));
				break;
			case "cv_view":
				$v = array_keys($this->get_cv_tpl());
				$pm = obj(get_instance(CL_PERSONNEL_MANAGEMENT)->get_sysdefault());
				$default_cv = ($pm->prop("cv_tpl")) ? "cv/".basename($pm->prop("cv_tpl")) : false;
				$tpl = isset($arr["request"]["cv_tpl"]) ? ("cv/".basename($v[$arr["request"]["cv_tpl"]])) : $default_cv;
				$arr["prop"]["value"] .= $this->show_cv(array(
					"id" => $arr["obj_inst"]->id(),
					"cv" => $tpl,
					"cfgform" => get_instance(CL_CFGFORM)->get_sysdefault(),
				));
				break;
			case "dl_since":
				for($i=date("Y"); $i>date("Y") - 80; $i--)
				{
					$data["options"][$i]=$i;
				}
				break;

			case "children1":
				$data["options"] = $this->make_keys(range(0, 10));
				break;

			case "stats_s_time_sel":
				$data["options"] = array(
					"" => t("--vali--"),
					"today" => t("T&auml;na"),
					"yesterday" => t("Eile"),
					"cur_week" => t("Jooksev n&auml;dal"),
					"cur_mon" => t("Jooksev kuu"),
					"last_mon" => t("Eelmine kuu")
				);
				$data["value"] = $arr["request"]["stats_s_time_sel"];
				if (!isset($arr["request"]["stats_s_time_sel"]))
				{
					$data["value"] = "cur_mon";
				}
				break;

			case "stats_s_from":
			case "stats_s_to":
				$data["value"] = date_edit::get_timestamp($arr["request"][$data["name"]]);
				break;

			case "stats_s_cust":
				$data["value"] = $arr["request"]["stats_s_cust"];
				break;

			case "stats_s_type":
				$data["value"] = $arr["request"]["stats_s_type"];
				$data["options"] = array(
					"rows" => t("Ridade kaupa"),
					"" => t("Kokkuv&otilde;te"),
				);
				break;

			case "server_folder":
				$i = get_instance(CL_CRM_COMPANY);
				$i->_proc_server_folder($arr);
				break;

			case "docs_tb":
			case "docs_tree":
			case "docs_tbl":
			case 'docs_s_type':
			case "docs_news_tb":
			case "dn_res":
			case "documents_lmod":
				static $docs_impl;
				if (!$docs_impl)
				{
					$docs_impl = get_instance("applications/crm/crm_company_docs_impl");
				}
				$fn = "_get_".$data["name"];
				return $docs_impl->$fn($arr);

			case "is_important":
				$u = new user();
				$p = obj($u->get_current_person());
				if (!$p || !is_oid($arr["obj_inst"]->id()))
				{
					return;
				}

				if ($p->is_connected_to(array("to" => $arr["obj_inst"]->id(), "type" => "RELTYPE_IMPORTANT_PERSON")))
				{
					$data["value"] = 1;
				}
				break;

			case "code":
				if (empty($data["value"]) && is_oid($ct = $arr["obj_inst"]->prop("address")) && $this->can("view", $ct))
				{
					$ct = obj($ct);
					$rk = $ct->prop("riik");
					if (is_oid($rk) && $this->can("view", $rk))
					{
						$rk = obj($rk);
						$code = substr(trim($rk->ord()), 0, 1);
						// get number of companies that have this country as an address
						$ol = new object_list(array(
							"class_id" => CL_CRM_PERSON,
							"CL_CRM_PERSON.address.riik.name" => $rk->name()
						));
						$ol2 = new object_list(array(
							"class_id" => CL_CRM_COMPANY,
							"CL_CRM_COMPANY.contact.riik.name" => $rk->name()
						));
						$code .= "-".sprintf("%04d", $ol->count() + $ol2->count() + 1);
						$data["value"] = $code;
					}
				}
				break;

			case "client_manager":
				$u = new user();
				$ws = array();
				$c = get_instance(CL_CRM_COMPANY);
				$c->get_all_workers_for_company(obj($u->get_current_company()), $ws);
				if (count($ws))
				{
					$ol = new object_list(array("oid" => $ws));
					$data["options"] = array("" => t("--vali--")) + $ol->names();
				}
				if ($arr["new"])
				{
					$data["value"] = $u->get_current_person();
				}
				if (isset($data["options"]) && !isset($data["options"][$data["value"]]) && $this->can("view", $data["value"]))
				{
					$tmp = obj($data["value"]);
					$data["options"][$data["value"]] = $tmp->name();
				}
				break;

			case "pictureurl":
				// this one is generated by the picture releditor and should not be edited
				// manually
				$retval = PROP_IGNORE;
				break;

			case "ext_id":
				$retval = PROP_IGNORE;
				break;

			case 'work_contact':
				//i'm gonna to this manually i guess
				//cos a person can be connected to a company
				//through sections, relpicker obviously doesn't cover that
				//maybe i made design flaw and should have done what i did
				//a bit differently?
				if($this->can("view", $arr["obj_inst"]->id()))
				{
					$company = $this->get_work_contacts($arr);
				}
				$data['options'] = $company;
				$data['options'][0] = t('--vali--');
				$data['options'] = array_reverse($data['options'], true);
				break;

			case "title":
				$data["options"] = array(
					3 => t("H&auml;rra"),
					1 => t("Proua"),
					2 => t("Preili")
				);
				break;

			case "social_status":
				$data["options"] = array(
					3 => t("Vallaline"),
					1 => t("Abielus"),
					2 => t("Lahutatud"),
					4 => t("Vabaabielus"),
				);
				break;

			case "templates":
				$data["options"] = array(
					"1" => t("Pilt, kontakt, artiklid"),
					"2" => t("kontakt"),
				);
				break;

			case "forms":
				$data["multiple"] = 1;
				break;

			case "navtoolbar":
				$this->isik_toolbar(&$arr);
				break;

			case "gender":
				$data["options"] = array(
					"1" => t("mees"),
					"2" => t("naine"),
				);
				break;

			case "email":
				break;


			case "org_actions":
			case "org_calls":
			case "org_meetings":
			case "org_tasks":
				$this->do_org_actions(&$arr);
				break;

			case "skills_listing_tree":
				$this->do_person_skills_tree($arr);
			break;

			case "picture":
				break;

			case "skills_toolbar":
				$this->do_cv_skills_toolbar(&$data["toolbar"], $arr);
				break;

			case "skills_table":
				break;

			case "juhiload":
				if(!($arr["request"]["skill"]=="driving_licenses"))
				{
					return PROP_IGNORE;
				}
				break;

			case "submit_driving_licenses":
				if(!($arr["request"]["skill"]=="driving_licenses"))
				{
					return PROP_IGNORE;
				}
				break;

			case "language_list":
				return PROP_IGNORE;
				break;

			case "language_skills_table":
				if($arr["request"]["skill"] =="languages")
				{
					$this->do_language_skills_table($arr);
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "language_levels":
				return PROP_IGNORE;
				break;

			case "previous_jobs_table":
				$this->do_jobs_table($arr);
				break;

			case "previous_jobs_tb":
				$this->do_previous_jobs_tb($arr);
				break;

			case "education_tb":
				$this->do_education_tb($arr);
				break;

			case "basic_education_edit":
				if($arr["request"]["etype"] === "basic_edu")
				{
					$data["rel_id"] = $arr["request"]["eoid"];
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "vocational_education_edit":
				if($arr["request"]["etype"] === "voc_edu")
				{
					$data["rel_id"] = $arr["request"]["eoid"];
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "higher_education_edit":
				if($arr["request"]["etype"] === "higher_edu")
				{
					$data["rel_id"] = $arr["request"]["eoid"];
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "secondary_education_edit":
				if($arr["request"]["etype"] === "secondary_edu")
				{
					$data["rel_id"] = $arr["request"]["eoid"];
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "education_table":
				$this->do_education_table($arr);
				break;

			case "programming_skills":

				if(!($arr["request"]["skill"] === "programming"))
				{
					return PROP_IGNORE;
				}
				break;

			case "password":
				if ($this->has_user($arr["obj_inst"]) or !$arr["obj_inst"]->company_property("do_create_users"))
				{
					return PROP_IGNORE;
				}
				break;

			case "username":
				if (!($tmp = $this->has_user($arr["obj_inst"])))
				{
					$data["type"] = "textbox";
					if(!$arr["obj_inst"]->company_property("do_create_users"))
					{
						$data["type"] = "text";
						$data["value"] = t("Isikule kasutaja tegemiseks peab olema organisatsioonis, kus ta on t&ouml;&ouml;taja, valitud 'Kas isikud on kasutajad' seade");
					}
				}
				else
				{
					$data["value"] = html::obj_change_url($tmp->id());
				}
				break;

			case "crm_settings":
				$u = new user();
				$p = $u->get_current_person();
				if (true || $p == $arr["obj_inst"]->id())
				{
				// get all crm settings for this person or user
					$user = $this->has_user($arr["obj_inst"]);
					if (!$user)
					{
						return PROP_IGNORE;
					}
					$ol = new object_list(array(
						"class_id" => CL_CRM_SETTINGS,
						"CL_CRM_SETTINGS.RELTYPE_USER" => $user->id()
					));
					if (!$ol->count())
					{
						$ol = new object_list(array(
							"class_id" => CL_CRM_SETTINGS,
							"CL_CRM_SETTINGS.RELTYPE_PERSON" => $arr["obj_inst"]->id()
						));
					}

					if ($ol->count())
					{
						$b = $ol->begin();
						$data["value"] = html::obj_change_url($b->id(), t("Muuda"));
						return PROP_OK;
					}
				}
				return PROP_IGNORE;
				break;

			case "cedit_phone_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"number" => t("Telefoninumber"),
					"type" => t("T&uuml;&uuml;p"),
					"is_public" => t("Avalik"),
					"rels" => t("Seotus t&ouml;&ouml;kohaga"),
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_phone_tbl($t, $arr);
				break;

			case "cedit_telefax_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"number" => t("Faksi number"),
					"rels" => t("Seotus t&ouml;&ouml;kohaga"),
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_fax_tbl($t, $arr);
				break;

			case "cedit_url_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"url" => t("Veebiaadress"),
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_url_tbl($t, $arr);
				break;

			case "cedit_email_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"email" => t("Emaili aadress"),
					"rels" => t("Seotus t&ouml;&ouml;kohaga"),
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_email_tbl($t, $arr);
				break;

			case "cedit_profession_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"org" => t("Organisatsioon"),
					"profession" => t("Amet"),
					"start" => t("Suhte algus"),
					"end" => t("Suhte l&otilde;pp"),
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_profession_tbl($t, $arr);
				break;

			case "cedit_bank_account_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"name" => t("Arvenumbri nimetus"),
					"account" => t("Arve number"),
					"bank" => t("Pank"),
					"office_code" => t("Kodukontori kood"),
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_acct_tbl($t, $arr);
				$t->set_caption(t("Pangaarved"));
				break;
			case "cedit_adr_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"aadress" => t("T&auml;nav"),
					"postiindeks" => t("Postiindeks"),
					"linn" => t("Linn"),
					"maakond" => t("Maakond"),
					"piirkond" => t("Piirkond"),
					"riik" => t("Riik")
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_adr_tbl($t, $arr);
				$t->set_caption(t("Aadressid"));
				break;
			//rel joga
			case "referal_type":
				$c = get_instance("cfg/classificator");
				$data["options"] = array("" => t("--vali--")) + $c->get_options_for(array(
					"name" => "referal_type",
					"clid" => CL_CRM_COMPANY
				));
				break;

			case "bill_due_days":
			case "cust_contract_date":
			case "priority":
			case "bill_penalty_pct":
				// read from rel
				if (($rel = $this->get_cust_rel($arr["obj_inst"])))
				{
					if ($arr["request"]["action"] === "view")
					{
						$data["value"] = $rel->prop_str($data["name"]);
					}
					else
					{
						$data["value"] = $rel->prop($data["name"]);
					}
				}
				if (isset($data["options"]) && !isset($data["options"][$data["value"]]) && $this->can("view", $data["value"]))
				{
					$tmp = obj($data["value"]);
					$data["options"][$data["value"]] = $tmp->name();
				}
				break;
			case "buyer_priority":
			case "buyer_contract_date":
				if (($rel = $this->get_cust_rel(get_current_company() , false , $arr["obj_inst"])))
				{
					if ($arr["request"]["action"] == "view")
					{
						$data["value"] = $rel->prop_str($data["name"]);
					}
					else
					{
						$data["value"] = $rel->prop($data["name"]);
					}
				}
				if (isset($data["options"]) && !isset($data["options"][$data["value"]]) && $this->can("view", $data["value"]))
				{
					$tmp = obj($data["value"]);
					$data["options"][$data["value"]] = $tmp->name();
				}

				break;
			case "buyer_contract_creator":
				$this->_get_buyer_contract_creator($arr);
				break;
			case "cust_contract_creator":
				$this->_get_cust_contract_creator($arr);
				break;
			//kliendisuhte teema
			case "buyer_contact_person":
			case "contact_person":
				$data["value"] = html::obj_change_url($arr["obj_inst"]->id());
				break;

			case "co_is_cust":
			case "bill_penalty_pct":
			case "co_is_buyer":
					$fn = "_get_".$data["name"];
					$this->$fn($arr);
				break;
		}
		return $retval;

	}

	function _get_candidate_tb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "delete_obj",
			"confirm" => t("Oled kindel, et kustutada?"),
		));
	}

	function _get_candidate_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "job_offer",
			"caption" => t("T&ouml;&ouml;pakkumine"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "profession",
			"caption" => t("Ametikoht"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Organisatsioon"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "deadline",
			"caption" => t("T&auml;htaeg"),
			"align" => "center",
		));
		foreach($arr["obj_inst"]->connections_to(array("from.class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE, "type" => "RELTYPE_PERSON")) as $conn)
		{
			$from = $conn->from();
			$t->define_data(array(
				"oid" => $from->id(),
				"job_offer" => html::obj_change_url($from->prop("job_offer")),
				"profession" => html::obj_change_url($from->prop("job_offer.profession")),
				"company" => html::obj_change_url($from->prop("job_offer.company")),
				"deadline" => get_lc_date($from->prop("job_offer.end")),
			));
		}
	}

	function _get_education_tlb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_new_button(array(CL_CRM_PERSON_EDUCATION), $arr["obj_inst"]->id(), 23);		// RELTYPE_EDUCATION
		$t->add_save_button();
		$t->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "delete_obj",
			"confirm" => t("Oled kindel, et kustutada?"),
		));
	}

	function _get_education_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "school",
			"caption" => t("Kool"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "degree",
			"caption" => t("Akadeemiline kraad"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "field",
			"caption" => t("Valdkond"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "speciality",
			"caption" => t("Eriala"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "main_speciality",
			"caption" => t("P&otilde;hieriala"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "obtain_language",
			"caption" => t("Omandamise keel"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Algus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("L&otilde;pp"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "end_date",
			"caption" => t("L&otilde;petamise kuup&auml;ev"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "diploma_nr",
			"caption" => t("Diplomi number"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
			"align" => "center",
		));
		$deg_ops = array(
			"pohiharidus" => t("P&otilde;hiharidus"),
			"keskharidus" => t("Keskharidus"),
			"keskeriharidus" => t("Kesk-eriharidus"),
			"diplom" => t("Diplom"),
			"bakalaureus" => t("Bakalaureus"),
			"magister" => t("Magister"),
			"doktor" => t("Doktor"),
			"teadustekandidaat" => t("Teaduste kandidaat"),
		);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_EDUCATION")) as $conn)
		{
			$to = $conn->to();
			$t->define_data(array(
				"oid" => $to->id(),
				"school" => is_oid($to->prop("school1")) ? html::obj_change_url($to->prop("school1")) : $to->prop("school2"),
				"degree" => $deg_ops[$to->prop("degree")],
				"field" => $to->prop("field.name"),
				"speciality" => $to->prop("speciality"),
				"main_speciality" => $to->prop("main_speciality") == 0 ? t("Ei") : t("Jah"),
				"obtain_language" => $to->prop("obtain_language.name"),
				"start" => get_lc_date($to->prop("start")),
				"end" => get_lc_date($to->prop("end")),
				"end_date" => get_lc_date($to->prop("end_date")),
				"diploma_nr" => $to->prop("diploma_nr"),
				"change" => html::obj_change_url($to->id(), t("Muuda")),
			));
		}
	}

	function _get_udef_skills_tb($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_save_button();
	}

	function _get_udef_skills_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
	}

	function recursive_connections_from($ids, $reltype, $array)
	{
		foreach(connection::find(array("from" => $ids, "type" => $reltype)) as $conn)
		{
			$array[$conn["to"]] = $conn["to.name"];
			$new_ids[$conn["to"]] = $conn["to.name"];
		}
		if(count($new_ids) > 0)
		{
			$this->recursive_connections_from($new_ids, $reltype, &$array);
		}
	}

	function _get_jobs_wanted_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "field",
			"caption" => t("Tegevusala"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "load",
			"caption" => t("T&ouml;&ouml;koormus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "wage",
			"caption" => t("Palgasoov"),
			"align" => "center",
		));

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_WORK_WANTED")) as $conn)
		{
			$to = $conn->to();
			$field = $to->prop("field");
			if(!empty($field))
			{
				$fol = new object_list(array(
					"oid" => $to->prop("field"),
					"parent" => array(),
					"lang_id" => array(),
					"site_id" => array(),
					"status" => array(),
				));
				$field = "";
				foreach($fol->names() as $name)
				{
					if(strlen($field) != 0)
						$field .= ", ";
					$field .= $name;
				}
			}
			$t->define_data(array(
				"oid" => $to->id(),
				"name" => html::obj_change_url($to->id()),
				"field" => $field,
				"load" => $to->prop("load.name"),
				"wage" => $to->prop("pay")." kuni ".$to->prop("pay2"),
			));
		}
	}

	function _get_work_tbl($arr)
	{
		$org_fixed = 0;
		$query = $this->parse_url_parse_query($arr["request"]["return_url"]);
		if($query["class"] == "crm_company" && $this->can("view", $query["id"]))
		{
			$org_fixed = $query["id"];
		}

		$org_arr = new object_data_list(
			array(
				"class_id" => CL_CRM_COMPANY,
				"parent" => array()
			),
			array
			(
				CL_CRM_COMPANY => array("oid" => "oid", "name" => "name")
			)
		);
		$orgs = array(0 => t("--vali--"));
		foreach($org_arr->list_data as $lde)
		{
			$orgs[$lde["oid"]] = $lde["name"];
		}

		$sec_arr = new object_data_list(
			array(
				"class_id" => CL_CRM_SECTION,
				"parent" => array()
			),
			array
			(
				CL_CRM_SECTION => array("oid" => "oid", "name" => "name")
			)
		);
		$secs = array(0 => t("--vali--"));
		foreach($sec_arr->list_data as $lde)
		{
			$secs[$lde["oid"]] = $lde["name"];
		}

		$pro_arr = new object_data_list(
			array(
				"class_id" => CL_CRM_PROFESSION,
				"parent" => array()
			),
			array
			(
				CL_CRM_PROFESSION => array("oid" => "oid", "name" => "name")
			)
		);
		$pros = array(0 => t("--vali--"));
		foreach($pro_arr->list_data as $lde)
		{
			$pros[$lde["oid"]] = $lde["name"];
		}

		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "org",
			"caption" => t("Organisatsioon"),
		));
		$t->define_field(array(
			"name" => "sec",
			"caption" => t("Osakond"),
		));
		$t->define_field(array(
			"name" => "pro",
			"caption" => t("Ametinimetus"),
		));
		$t->define_chooser(array(
			"name" => "select",
			"field" => "sel",
			"width" => "60",
		));
		$relpicker = get_instance("vcl/relpicker");
		foreach($arr["obj_inst"]->get_active_work_relations()->arr() as $wr)
		{
			$orgid = $wr->prop("org");
			$secid = $wr->prop("section");
			if($orgid !== $org_fixed && $org_fixed !== 0)
			{
				continue;
			}
			if($this->can("view", $orgid))
			{
				$org_obj = new object($orgid);
				if(!is_array($sec_options[$orgid]))
				{
					$ids = array();
					foreach($org_obj->connections_from(array("type" => 28)) as $org_conn)
					{
						$sec_options[$orgid][$org_conn->prop("to")] = $org_conn->prop("to.name");
						$ids[$org_conn->prop("to")] = $org_conn->prop("to");
					}
					if(count($ids) > 0)
					{
						$this->recursive_connections_from($ids, 1, &$sec_options[$orgid]);
					}
				}
			}
			$pro_options = array();
			if($this->can("view", $secid))
			{
				$sec_obj = obj($secid);
				foreach($sec_obj->connections_from(array("type" => 3)) as $pro_conn)
				{
					$pro_options[$pro_conn->prop("to")] = $pro_conn->prop("to.name");
				}
			}
			elseif(count($sec_options[$orgid]) > 0)
			{
				foreach(connection::find(array("from" => array_flip($sec_options[$orgid]), "type" => 3)) as $pro_conn)
				{
					$pro_options[$pro_conn["to"]] = $pro_conn["to.name"];
				}
			}
			$t->define_data(array(
				"org" => $relpicker->create_relpicker(array(
					"name" => "work_tbl[".$wr->id()."][org]",
					"reltype" => 1,
					"oid" => $wr->id(),
					"property" => "org",
//					"buttonspos" => "bottom",
				)),
				"sec" => $relpicker->create_relpicker(array(
					"name" => "work_tbl[".$wr->id()."][sec]",
					"reltype" => 7,
					"oid" => $wr->id(),
					"property" => "section",
					"options" => $sec_options[$orgid],
				)),
				"pro" => $relpicker->create_relpicker(array(
					"name" => "work_tbl[".$wr->id()."][pro]",
					"reltype" => 3,
					"oid" => $wr->id(),
					"property" => "profession",
					"options" => $pro_options,
				)),
				"sel" => $wr->id(),
			));
		}
	}

	function isik_toolbar($args)
	{
		$toolbar = &$args["prop"]["toolbar"];

		$pl = get_instance(CL_PLANNER);
		$cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		$parents = array();

		$parents[6] = $args["obj_inst"]->parent();

		if (!empty($cal_id))
		{
			$user_calendar = new object($cal_id);
			$parents[8] = $parents[9] = $user_calendar->prop('event_folder');
		}


		$action = array(
			array(
				"reltype" => 8, //RELTYPE_PERSON_MEETING,
				"clid" => CL_CRM_MEETING,
			),
			array(
				"reltype" => 9, //RELTYPE_PERSON_CALL,
				"clid" => CL_CRM_CALL,
			),
		);

		$toolbar->add_menu_button(array(
			"name" => "add_event",
			"tooltip" => t("Uus"),
		));

		$req = get_ru();

		$menudata = '';
		$clss = aw_ini_get("classes");
		$oid = $args["obj_inst"]->id();
		if (is_array($action))
		{
			foreach($action as $key => $val)
			{
				if (!$parents[$val['reltype']])
				{
					$toolbar->add_menu_item(array(
						"parent" => "add_event",
						'title' => t('Kalender m&auml;&auml;ramata'),
						'text' => sprintf(t('Lisa %s'),$clss[$val["clid"]]["name"]),
						'disabled' => true,
					));
				}
				else
				{
					$toolbar->add_menu_item(array(
						"parent" => "add_event",
						'url' => $this->mk_my_orb('new',array(
							'alias_to_org' => $oid,
							'reltype_org' => $val['reltype'],
							'class' => 'planner',
							'id' => $cal_id,
							'clid' => $val["clid"],
							'group' => 'add_event',
							'action' => 'change',
							'title' => $clss[$val["clid"]]["name"].': '.$args['obj_inst']->name(),
							'parent' => $parents[$val['reltype']],///?
							'return_url' => $req,
						)),
						'text' => sprintf(t('Lisa '),$clss[$val["clid"]]["name"]),
					));
				}
			};

			if (!empty($cal_id))
			{
				$toolbar->add_button(array(
					"name" => "user_calendar",
					"tooltip" => t("Kasutaja kalender"),
					"url" => $this->mk_my_orb('change', array('id' => $cal_id,'return_url' => $req,),'planner'),
					"onClick" => "",
					"img" => "icon_cal_today.gif",
				));
			}

		};

	}


	function show_isik($args)
	{
		$arg2["id"] = $args["obj_inst"]->id();
		$nodes = array();
		$nodes['visitka'] = array(
			"value" => $this->show($arg2),
		);
		return $nodes;
	}

	function fetch_person_by_id($arr)
	{
		// how do I figure out the _last_ action done with a person?

		// I need today's date..
		// I need a list of all events that have a calendar presentation
		// and then I just fetch the latest thingie

		// easy as pie

		$o = new object($arr["id"]);
		$cal_id = $arr["cal_id"];

		$phones = $emails = $urls = $ranks = $ranks_arr = $sections_arr = array();

		$tasks = $o->connections_from(array(
			"type" => array(9,10),
		));

		$to_ids = array();
		foreach($tasks as $task)
		{
			$to_ids[] = $task->prop("to");
		};

		$conns = $o->connections_from(array(
			"type" => 13,
		));
		foreach($conns as $conn)
		{
			$phones[] = $conn->prop("to.name");
		};

		$conns = $o->connections_from(array(
			"type" => 12,
		));
		foreach($conns as $conn)
		{
			$url_o = $conn->to();
			$urls[] = html::href(array(
				"url" => $url_o->prop("url"),
				"caption" => $url_o->prop("url"),
			));
		};

		$conns = $o->connections_from(array(
			"type" => 11,
		));
		foreach($conns as $conn)
		{
			$to_obj = $conn->to();
			$emails[] = $to_obj->prop("mail");
		};

		$conns = $o->connections_from(array(
			"type" => 'RELTYPE_RANK',
		));

		foreach($conns as $conn)
		{
			$ranks[] = $conn->prop("to.name");
			$ranks_arr[$conn->prop('to')] = $conn->prop('to.name');
		};

		$sections_arr = $o->get_section_selection();
		$address = "";
		$address_d = $o->get_first_obj_by_reltype("RELTYPE_ADDRESS");
		if ($address_d)
		{
			$address_a = array();
			if ($address_d->prop("aadress") != "")
			{
				$address_a[] = $address_d->prop("aadress");
			}

			if ($address_d->prop("linn"))
			{
				$tmp = obj($address_d->prop("linn"));
				$address_a[] = $tmp->name();
			}

			if ($address_d->prop("riik"))
			{
				$tmp = obj($address_d->prop("riik"));
				$address_a[] = $tmp->name();
			}

			$address = join(",", $address_a);
		}

		$oid = $o->id();

		$rv = array(
			'name' => $o->prop('firstname').' '.$o->prop('lastname'),
			'firstname' => $o->prop('firstname'),
			'lastname' => $o->prop('lastname'),
			"phone" => join(", ",$phones),
			"url" => join(", ",$urls),
			"email" => join(", ",$emails),
			"rank" => join(", ",$ranks),
			'section' => join(',',$sections_arr),
			'ranks_arr' => $ranks_arr,
			'sections_arr' => $sections_arr,
			'address' => $address,
			"add_task_url" => $this->mk_my_orb("change",array(
				"id" => $cal_id,
				"group" => "add_event",
				"alias_to_org" => $oid,
				"reltype_org" => 10,
				"clid" => CL_TASK,
			),CL_PLANNER),
			"add_call_url" => $this->mk_my_orb("change",array(
				"id" => $cal_id,
				"group" => "add_event",
				"alias_to_org" => $oid,
				"reltype_org" => 9,
				"clid" => CL_CRM_CALL,
			),CL_PLANNER),
			"add_meeting_url" => $this->mk_my_orb("change",array(
				"id" => $cal_id,
				"group" => "add_event",
				"alias_to_org" => $oid,
				"reltype_org" => 8,
				"clid" => CL_CRM_MEETING,
			),CL_PLANNER),

		);
		return $rv;
	}

	function upd_contact_data($arr)
	{
		// I need to figure out whether this person has a personal contact set?
		$personal_contact = $arr["obj_inst"]->prop("personal_contact");
		if (is_oid($personal_contact))
		{
			// load the contact object
			$pc = new object($personal_contact);
		}
		else
		{
			$pc = new object();
			$pc->set_class_id(CL_CRM_ADDRESS);
			$pc->set_name($arr["obj_inst"]->name());
			$pc->set_parent($arr["obj_inst"]->parent());
			$pc->save();

			$arr["obj_inst"]->connect(array(
				"to" => $pc->id(),
				"reltype"=> "RELTYPE_ADDRESS",
			));

			$arr["obj_inst"]->set_prop("personal_contact",$pc->id());
		};


		$addr_inst = new crm_address();
		$addr_inst->set_email_addr(array(
			"obj_id" => $pc->id(),
			"email" => $arr["prop"]["value"],
		));
	}


	/** shows a person

		@attrib name=show

		@param id required

	**/
	function show($arr)
	{
		$arx = array();
		$obj = new object($arr["id"]);
		$arx["alias"]["target"] = $obj->id();
		return $this->parse_alias($arx);
	}

	function show2($args)
	{
		extract($args);

		$obj = new object($id);
		$tpls = $obj->prop("templates");

		if (strlen($tpls) > 4)
		{
			$this->read_template('visit/'.$tpls);
		}
		else
		{
			$this->read_template('visit/visiit1.tpl');
		}

		$row = $this->fetch_all_data($id);

		$forms = $obj->prop("forms");
		if (is_array($this->default_forms))
		{
			$forms = array_merge($this->default_forms, $forms);
		}

		$fb = "";


		if (is_array($forms))
		{
			$forms = array_unique($forms);
			foreach($forms as $val)
			{
				if (!$val)
				{
					continue;
				}

				$form = new object($val);
				$fb.= html::href(array(
					'target' => $form->prop('open_in_window')? '_blank' : NULL,
					'caption' => $form->name(), 'url' => $this->mk_my_orb('form', array(
						'id' => $form->id(),
						'feedback' => $id,
						'feedback_cl' => rawurlencode('crm/crm_person'),
						),
				'pilot_object'))).'<br />';
			}
		}


		if (($row['lastname'] == '') &&($row['firstname'] == ''))
		{
			$row['firstname'] = $row['name'];
		}

		if ($row['picture'])
		{
			$img = new image();
			$im = $img->get_image_by_id($row['picture']);
			$row['picture_url'] = $im['url'];
			$this->vars($row);
			$row['PILT'] = $this->parse('PILT');
		}
		else
		{
			$row['picture'] = '';
		}

		$row['comment'] = $obj['comment'];
		$row['k_e_mail']=(!empty($row['k_e_mail']))?html::href(array('url' => 'mailto:'.$row['k_e_mail'], 'caption' => $row['k_e_mail'])):'';
		$row['w_e_mail']=(!empty($row['w_e_mail']))?html::href(array('url' => 'mailto:'.$row['w_e_mail'],'caption' => $row['w_e_mail'])):'';
		$row['k_kodulehekylg']=$row['k_kodulehekylg']?html::href(array('url' => $row['k_kodulehekylg'],'caption' => $row['k_kodulehekylg'],'target' => '_blank')):'';
		$row['w_kodulehekylg']=$row['w_kodulehekylg']?html::href(array('url' => $row['w_kodulehekylg'],'caption' => $row['w_kodulehekylg'],'target' => '_blank')):'';
		$row['tagasisidevormid'] = $fb;

		$this->vars($row);

		return $this->parse();
	}

	function fetch_all_data($id)
	{
//vot siuke p&auml;ring, &auml;ra k&uuml;si
		return  $this->db_fetch_row("select
			t1.oid as oid,
			t2.name as name,
			firstname,
			lastname,
			gender,
			personal_id,
			title,
			nickname,
			messenger,
			birthday,
			social_status,
			spouse,
			children,
			personal_contact,
			work_contact,
			digitalID,
			notes,
			pictureurl,
			picture,
			t11.name as k_riik,
			t6.name as k_maakond,
			t7.name as k_linn,
			t8.name as w_maakond,
			t9.name as w_linn,
			t10.name as w_riik,
			t4.name as fnimi,

			t3.postiindeks as k_postiindex,
			t3.aadress as k_aadress,
			t3.telefon as k_telefon,
			t3.mobiil as k_mobiil,
			t3.faks as k_faks,
			t3.e_mail as k_e_mail,
			t3.kodulehekylg as k_kodulehekylg,

			t4.postiindeks as w_postiindex,
			t4.aadress as w_aadress,
			t4.telefon as w_telefon,
			t4.mobiil as w_mobiil,
			t4.faks as w_faks,
			t4.e_mail as w_e_mail,
			t4.kodulehekylg as w_kodulehekylg

			from objects as t1

			left join kliendibaas_isik as t2 on t1.oid=t2.oid
			left join kliendibaas_address as t3 on t2.personal_contact=t3.oid
			left join kliendibaas_address as t4 on t2.work_contact=t4.oid

			left join kliendibaas_maakond as t6 on t6.oid=t3.maakond
			left join kliendibaas_linn as t7 on t7.oid=t3.linn
			left join kliendibaas_riik as t11 on t11.oid=t3.riik
			left join kliendibaas_maakond as t8 on t8.oid=t4.maakond
			left join kliendibaas_linn as t9 on t9.oid=t4.linn
			left join kliendibaas_riik as t10 on t10.oid=t4.riik

			where t1.oid=".$id);

	//left join images as t5 on t2.picture=t5.id
//			t5.link as picture,
	}

	////
	// !callback, used by selection
	// id - object to show
	function show_in_selection($args)
	{
		return $this->show(array('id' => $args['id']));
	}

	function request_execute($obj)
	{
		$arx = array();
		$arx["alias"]["target"] = $obj->id();
		return $this->parse_alias($arx);
	}

	function _get_size($fl)
	{
		$fl = basename($fl);
		if ($fl{0} != "/")
		{
			$fl = aw_ini_get("site_basedir")."/files/".$fl{0}."/".$fl;
		}
		$sz = getimagesize($fl);
		return array("width" => $sz[0], "height" => $sz[1]);
	}

	function parse_alias($arr = array())
	{
		// okey, I need to determine whether that template has a place for showing
		// a list of authors documents. If it does, then I need to create that list
		extract($arr);
		$to = new object($arr["alias"]["target"]);
		$this->read_template("pic_documents.tpl");
		$pdat = $this->fetch_person_by_id(array(
			"id" => $to->id(),
		));

		$al = get_instance("alias_parser");
		$notes = $to->prop("notes");

		$al->parse_oo_aliases($to->id(), &$notes);

		$this->vars(array(
			"name" => $to->name(),
			"phone" => $pdat["phone"],
			"email" => $pdat["email"],
			"notes" => nl2br($notes),
		));
		// show image if there is a placeholder for it in the current template
		if ($this->template_has_var("imgurl") || $this->is_template("IMAGE"))
		{
			if($img = $to->get_first_conn_by_reltype("RELTYPE_PICTURE"))
			{
				$img_inst = get_instance(CL_IMAGE);
				$imgurl = $img_inst->get_url_by_id($img->prop("to"));
				if($img2 = $to->get_first_obj_by_reltype("RELTYPE_PICTURE2"))
				{
					$mes = $this->_get_size($img2->prop("file"));
					$imgurl2 = html::popup(array(
						"caption" => html::img(array(
							"url" => $imgurl,
							"border" => 0,
						)),
						"width" => $mes["width"],
						"height" => $mes["height"],
						"url" => $this->mk_my_orb("show_image", array("id" => $to->id()), CL_CRM_PERSON, false ,true),
						"menubar" => 1,
						"resizable" => 1,
					));
				}
				else
				{
					$imgurl2 = html::img(array(
						"url" => $imgurl,
						"border" => 0,
					));
				}
			}
			$this->vars(array(
				"imgurl" => $imgurl,
				"imgurl2" => $imgurl2,
			));
			if(strlen($imgurl) > 0)
			{
				$this->vars(array(
					"IMAGE" => $this->parse("IMAGE"),
				));
			}
		};

		$at_once = 20;

		// show document list, if there is a placeholder for it in the current template
		// XXX: I need a navigator
		if ($this->is_template("DOCLIST"))
		{
			// how the bloody hell do I get the limiting to work

			// prev 10 / next 10 .. how do I pass the thing?
			// &auml;kki teha kuudega? ah? hm?

			// alguses n&auml;itame viimast 10-t
			// ... then how do I limit those?

			// hot damn, this thing sucks
			$dt = aw_global_get("date");
			if ((int)$dt == $dt)
			{
				$date = $dt;
			};
			$at = get_instance(CL_AUTHOR);
			list($nav,$doc_ids) = $at->get_docs_by_author(array(
				"author" => $to->prop("name"),
				"limit" => $at_once,
				"date" => $date,
			));

			// okey, I think I'll do it with dates

			$docs = "";
			// XXX: I need comment counts for each document id
			// how do I accomplish that?
			if (sizeof($doc_ids) > 0)
			{
				$doc_list = new object_list(array(
					"oid" => array_keys($doc_ids),
				));

				for($o = $doc_list->begin(); !$doc_list->end(); $o = $doc_list->next())
				{
					$this->vars(array(
						"url" => html::href(array(
							"url" => aw_ini_get("baseurl") . "/" . $o->id(),
							"caption" => strip_tags($o->prop("title")),
						)),
						"commcount" => $doc_ids[$o->id()]["commcount"],
						"commurl" => $this->mk_my_orb("show_threaded",array("board" => $o->id()),"forum"),
					));
					$docs .= $this->parse("ITEM");
				};
			};
			$this->vars(array(
				"ITEM" => $docs,
			));
			$this->vars(array(
				"DOCLIST" => $this->parse("DOCLIST"),
			));
			$nv = "";
			if ($nav["prev"])
			{
				$this->vars(array(
					"prevurl" => aw_url_change_var("date",$nav["prev"]),
				));
				$this->vars(array(
					"prevlink" => $this->parse("prevlink"),
				));
			};
			if ($nav["next"])
			{
				$this->vars(array(
					"nexturl" => aw_url_change_var("date",$nav["next"]),
				));
				$this->vars(array(
					"nextlink" => $this->parse("nextlink"),
				));
			};
		};
		return $this->parse();
	}

	/**
		@attrib name=show_image nologin=1
		@param id required type=int acl=edit
		@param side optional
	**/
	function show_image($arr)
	{
		$obj = obj($arr["id"]);
		if($img = $obj->get_first_obj_by_reltype("RELTYPE_PICTURE2"))
		{
			$img_inst = get_instance(CL_IMAGE);
			$image = html::img(array(
				"url" => $img_inst->get_url($img->prop("file")),
				"border" => 0,
			));
		}
		$this->read_template("image_show.tpl");
		$this->vars(array(
			"name" => $img->name(),
			"image" => $image,
		));
		return $this->parse();
	}

	////
	// !Perhaps I can make a single function that returns the latest event (if any)
	// for each connection?

	function do_org_actions($arr)
	{
		$ob = $arr["obj_inst"];
		$args = array();
		$pl = get_instance(CL_PLANNER);
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));
		switch($arr["prop"]["name"])
		{
			case "org_calls":
				$args["type"] = 9; //RELTYPE_PERSON_CALL;
				break;

			case "org_meetings":
				$args["type"] = 8; //RELTYPE_PERSON_MEETING;
				break;

			case "org_tasks":
				$args["type"] = 10; //RELTYPE_PERSON_TASK;
				break;
		};
		$conns = $ob->connections_from($args);
		$t = &$arr["prop"]["vcl_inst"];

		$arr["prop"]["vcl_inst"]->configure(array(
			"overview_func" => array(&$this,"get_overview"),
		));

		$range = $arr["prop"]["vcl_inst"]->get_range(array(
			"date" => $arr["request"]["date"],
			"viewtype" => !empty($arr["request"]["viewtype"]) ? $arr["request"]["viewtype"] : $arr["prop"]["viewtype"],
		));
		$start = $range["start"];
		$end = $range["end"];

		$overview_start = $range["overview_start"];

		$classes = aw_ini_get("classes");

		$return_url = get_ru();
		$planner = new planner();
		$this->overview = array();

		foreach($conns as $conn)
		{
			$item = new object($conn->prop("to"));
			if ($item->prop("start1") < $overview_start)
			{
				continue;
			};

			$cldat = $classes[$item->class_id()];

			$icon = icons::get_icon_url($item);

			// I need to filter the connections based on whether they write to calendar
			// or not.
			$link = $planner->get_event_edit_link(array(
				"cal_id" => $this->cal_id,
				"event_id" => $item->id(),
				"return_url" => $return_url,
			));

			if ($item->prop("start1") > $start)
			{
				$t->add_item(array(
					"timestamp" => $item->prop("start1"),
					"data" => array(
						"name" => $item->name(),
						"link" => $link,
						"modifiedby" => $item->prop("modifiedby"),
						"icon" => $icon,
					),
				));
			};

			if ($item->prop("start1") > $overview_start)
			{
				$this->overview[$item->prop("start1")] = 1;
			};
		}
	}

	function get_overview($arr = array())
	{
		return $this->overview;
	}

	function on_connect_job_wanted_to_person($arr)
	{
		$conn = $arr['connection'];
		$target_obj = $conn->to();
		if($target_obj->class_id() == CL_CRM_PERSON)
		{
			$target_obj->connect(array(
				'to' => $conn->prop('from'),
				'reltype' => "RELTYPE_WORK_WANTED",
			));
		}
	}

	function on_disconnect_job_wanted_from_person($arr)
	{
		$conn = $arr["connection"];
		$target_obj = $conn->to();
		if ($target_obj->class_id() == CL_CRM_PERSON)
		{
			$target_obj->disconnect(array(
				"from" => $conn->prop("from"),
			));
		};
	}

	function do_cv_skills_toolbar($toolbar, $arr)
	{
		$toolbar->add_menu_button(array(
			'name'=>'add_item',
			'tooltip'=>t('Uus')
		));

		$toolbar->add_button(array(
			'name' => 'del',
			'img' => 'delete.gif',
			'tooltip' => t('Kustuta valitud t&ouml;&ouml;pakkumised'),
		));

		$toolbar->add_button(array(
			'name' => 'save',
			'img' => 'save.gif',
			'tooltip' => t('Salvesta'),
			'action' => 'submit',
		));

	}

	function do_person_skills_tree($arr)
	{
		$tree = $arr['prop']['vcl_inst'];

		$tree->add_item(0,array(
    		"id" => 1,
    		"name" => t("Arvutioskused"),
    		"url" => $this->mk_my_orb("do_something",array()),
		));

		$tree->add_item(1,array(
    		"id" => 2,
    		"name" => t("Rakendused"),
    		"url" => $this->mk_my_orb("do_something",array()),
		));

		$tree->add_item(1,array(
    		"id" => 3,
    		"name" => t("Programmeerimine"),
    		"url" => $this->mk_my_orb("change", array(
    			"id" => $arr['obj_inst']->id(),
    			"group" => $arr['request']['group'],
    			"skill" => "programming",
    			), CL_CRM_PERSON),
		));

		$tree->add_item(1,array(
    		"id" => 4,
    		"name" => t("Muu"),
    		"url" => $this->mk_my_orb("do_something",array()),
		));

		if($arr["request"]["skill"] == "languages")
		{
			$lang_capt = "<b>".t("Keeled")."</b>";
		}
		else
		{
			$lang_capt = t("Keeled");
		}

		$tree->add_item(0, array(
    		"id" => 5,
    		"name" => $lang_capt,
    		"url" => $this->mk_my_orb("change", array(
    			"id" => $arr['obj_inst']->id(),
    			"group" => $arr['request']['group'],
    			"skill" => "languages",
    			), CL_CRM_PERSON),
		));

		$tree->add_item(0, array(
    		"id" => 6,
    		"name" => t("Juhiload"),
    		"url" => $this->mk_my_orb("change", array(
    			"id" => $arr['obj_inst']->id(),
    			"group" => $arr['request']['group'],
    			"skill" => "driving_licenses",
    			), CL_CRM_PERSON),
		));

	}


	function do_previous_jobs_tb(&$arr)
	{
		$tb = $arr["prop"]["toolbar"];

		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus t&ouml;&ouml;kogemus"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 66,
				"return_url" => get_ru(),
				"parent" => $arr["obj_inst"]->parent(),
			), CL_CRM_PERSON_WORK_RELATION),
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta t&ouml;&ouml;kogemused"),
			"action" => "delete_objects",
			"confirm" => t("Oled kindel, et kustutada?"),
		));

	}

	function _get_mails_tbl($arr)
	{

		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"caption" => t("Saatja nimi"),
			"name" => "sender",
			"align" => "left",
			"sortable" => 1,
		));

		$t->define_field(array(
			"caption" => t("Teema"),
			"name" => "subject",
			"align" => "left",
			"sortable" => 1,
		));

		$t->define_field(array(
			"caption" => t("Aeg"),
			"name" => "time",
			"align" => "left",
			"sortable" => 1,
		));

		$t->define_field(array(
			"caption" => t("Aadressidele"),
			"name" => "to",
			"align" => "left",
			"sortable" => 1,
		));
		$t->define_field(array(
			"caption" => t("Sisu"),
			"name" => "content",
			"align" => "left",
			"sortable" => 1,
		));
		$t->define_field(array(
			"caption" => t("Manused"),
			"name" => "attachments",
			"align" => "left",
			"sortable" => 1,
		));

		$user_inst = new user();

		$mails = $arr["obj_inst"]->get_recieved_mails(array(
			"subject" => $arr["request"]["mails_s_name"],
			"content" => $arr["request"]["mails_s_content"],
			"customer" => $arr["request"]["mails_s_customer"],
		));
		foreach($mails->arr() as $mail)
		{
			$user = $mail->createdby();
			$person = $user_inst->get_person_for_uid($user);
			$data = array();
			$data["time"] = date("d.m.Y H:i" , $mail->created());
			$data["subject"] = $mail->name();
			$data["sender"] = $person->name();
			$data["content"] = $mail->prop("message");
			$addr = explode("," , htmlspecialchars($mail->prop("mto")));

			$data["to"] = join("<br>" , $addr);

			$data["attachments"] = "";
			$aos = $mail->prop("attachments");
			foreach($aos as $ao)
			{
				$o = obj($ao);
				$file_data = $o->get_file();
				$data["attachments"].= "<br>\n".html::href(array(
					"caption" => html::img(array(
						"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
						"border" => 0,
					)).$o->name()." (".filesize($file_data["properties"]["file"])." B)",
					"url" => $o->get_url(),
				));
			}
			$t->define_data($data);
		}
	}


	function do_jobs_table($arr)
	{
		$table = $arr["prop"]["vcl_inst"];

		$table->define_field(array(
			"name" => "asutus",
			"caption" => t("Asutus"),
			"sortable" => 1
		));

		$table->define_field(array(
			"name" => "ametikoht",
			"caption" => t("Ametikoht"),
			"sortable" => 1
		));

		$table->define_field(array(
			"name" => "alates",
			"caption" => t("Alates"),
			"sortable" => 1
		));

		$table->define_field(array(
			"name" => "kuni",
			"caption" => t("Kuni"),
			"sortable" => 1
		));

		$table->define_field(array(
			"name" => "tasks",
			"caption" => t("T&ouml;&ouml;&uuml;lesanded"),
		));

		$table->define_field(array(
			"name" => "change",
			"caption" => t("Muuda"),
		));

		$table->define_chooser(array(
			"name" => "sel",
			"field" => "from",
		));

		$cs = array();
		if (is_oid($arr["obj_inst"]->id()))
		{
			$cs = $arr["obj_inst"]->connections_from(array("type" => "RELTYPE_PREVIOUS_JOB"));
		}
		foreach ($cs as $conn)
		{
			$prevjob = $conn->to();
			if($prevjob->prop("org"))
			{
					$url = html::href(array(
						"caption" => $prevjob->prop_str("org"),
						"url" => $this->mk_my_orb("change", array(
							"id" => $prevjob->prop("org"),
							"return_url" => get_ru(),
						), CL_CRM_COMPANY),
					));
			}
			$table->define_data(array(
				"asutus" => $prevjob->prop_str("org")?$url:t("-"),
				"alates" => get_lc_date($prevjob->prop("start")),
				"ametikoht" => $prevjob->prop("profession")?$prevjob->prop_str("profession"):t("-"),
				"kuni" => get_lc_date($prevjob->prop("end")),
				"tasks" => $prevjob->prop("tasks"),
				"change" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $prevjob->id(),
						"return_url" => get_ru(),
					), CL_CRM_PERSON_WORK_RELATION),
					"caption" => t("Muuda"),
				)),
				"from" => $conn->id(),
			));
		}

	}

	/**
	@attrib name=delete_objects
	**/
	function delete_objects($arr)
	{
		if (!is_array($arr["sel"]) && is_array($arr["check"]))
		{
			$arr["sel"] = $arr["check"];
		}
		if (!is_array($arr["sel"]) && is_array($arr["select"]))
		{
			foreach ($arr["select"] as $oid)
			{
				$obj = obj($oid);
				$obj->delete();
			}
		}
		foreach ($arr["sel"] as $del_conn)
		{
			$conn = new connection($del_conn);
			$obj = $conn->to();
			$obj->delete();
		}
		return  $arr["post_ru"];
	}

	/**
		@attrib name=delete_obj
	**/
	function delete_obj($arr)
	{
		foreach ($arr["sel"] as $o)
		{
			$o = obj($o);
			$o->delete();
		}
		return  $arr["post_ru"];
	}

	function has_current_job_relation($oid)
	{
		$c = new connection();
		$ret = $c->find(array(
			"from" => $oid,
			"type" => 67,
		));
		$c = current($ret);
		return count($ret)?obj($c["to"]):false;
	}

	function callback_pre_edit($arr)
	{
		if(isset($arr["request"]["job_offer_id"]) && $this->can("view", $arr["request"]["job_offer_id"]))
		{
//			print $arr["request"]["job_offer_id"];
			aw_session_set("job_offer_obj_id_for_candidate", $arr["request"]["job_offer_id"]);
		}
	}

	function callback_post_save($arr)
	{
		if($this->can("view", $arr["obj_inst"]->id()) && $this->can("view", aw_global_get("job_offer_obj_id_for_candidate")))
		{
			$candidate = obj();
			$candidate->set_class_id(CL_PERSONNEL_MANAGEMENT_CANDIDATE);
			$candidate->set_parent($arr["obj_inst"]->id());
			$candidate->save();
			$candidate->connect(array(
				"to" => $arr["obj_inst"]->id(),
				"type" => "RELTYPE_PERSON",
			));
			$candidate->connect(array(
				"to" => aw_global_get("job_offer_obj_id_for_candidate"),
				"type" => "RELTYPE_JOB_OFFER",
			));
			aw_session_set("candidate_obj_id_for_candidate", $candidate->id());
			// Don't need this one anymore.
			aw_session_set("job_offer_obj_id_for_candidate", "");
			aw_global_set("job_offer_obj_id_for_candidate", "");
		}
		/*
		if($arr["obj_inst"]->prop("work_contact"))
		{
			if(!$o = $this->has_current_job_relation($arr["obj_inst"]->id()))
			{
				$o = new object();
				$o->set_parent($arr["obj_inst"]->parent());
				$o->set_class_id(CL_CRM_PERSON_WORK_RELATION);
				$o->set_name($arr["obj_inst"]->prop_str("work_contact"));
				$o->save();
				$arr["obj_inst"]->connect(array(
					"to" => $o->id(),
					"type" => "RELTYPE_CURRENT_JOB",
				));
				$arr["obj_inst"]->save();
			}
			$o->set_prop("org", $arr["obj_inst"]->prop("work_contact"));
			$o->connect(array(
				"to" => $arr["obj_inst"]->prop("work_contact"),
				"type" => "RELTYPE_ORG",
			));
			$o->save();
		}
		else
		{
			if($o = $this->has_current_job_relation($arr["obj_inst"]->id()))
			{
				$o->delete();
			}
		}
		*/

		if (aw_global_get("uid") != "")
		{
			$u = new user();
			$p = obj($u->get_current_person());
			if ($arr["request"]["group"] == "general2")
			{
				if ($arr["request"]["is_important"] == 1)
				{
					$p->connect(array(
						"to" => $arr["obj_inst"]->id(),
						"type" => "RELTYPE_IMPORTANT_PERSON"
					));
				}
				else
				if (is_oid($p->id()))
				{
					if (is_oid($p->id()) && $p->is_connected_to(array("to" => $arr["obj_inst"]->id(), "type" => "RELTYPE_IMPORTANT_PERSON")))
					{
						$p->disconnect(array(
							"from" => $arr["obj_inst"]->id(),
						));
					}
				}
			}

			if ($this->can("view", $arr["request"]["add_to_task"]))
			{
				$task = obj($arr["request"]["add_to_task"]);
				$cc = $task->instance();
				$cc->add_participant($task, $arr["obj_inst"]);
			}

			if ($this->can("view", $arr["request"]["add_to_co"]))
			{
				$arr["obj_inst"]->add_work_relation(array("org" => $arr["request"]["add_to_co"]));
			}
		}

		// gen code if not done
		if ($arr["obj_inst"]->prop("code") == "")
		{
			if ($this->can("view", ($ct = $arr["obj_inst"]->prop("address"))))
			{
				$ct = obj($ct);
				$rk = $ct->prop("riik");
				if (is_oid($rk) && $this->can("view", $rk))
				{
					$rk = obj($rk);
					$code = substr(trim($rk->ord()), 0, 1);
					// get number of companies that have this country as an address
					$ol = new object_list(array(
						"class_id" => CL_CRM_PERSON,
						"CL_CRM_PERSON.address.riik.name" => $rk->name()
					));
					$ol2 = new object_list(array(
						"class_id" => CL_CRM_COMPANY,
						"CL_CRM_COMPANY.contact.riik.name" => $rk->name()
					));
					$code .= "-".sprintf("%04d", $ol->count() + $ol2->count()+1);
					$arr["obj_inst"]->set_prop("code", $code);
					$arr["obj_inst"]->save();
				}
			}

		}

		// write name and e-mail to the user
		$u = $this->has_user($arr["obj_inst"]);
		if ($u)
		{
			$mod = false;
			if ($u->prop("real_name") != $arr["obj_inst"]->name())
			{
				$u->set_prop("real_name", $arr["obj_inst"]->name());
				$mod = true;
			}
			if ($u->prop("email") != $arr["obj_inst"]->prop_str("email"))
			{
				$u->set_prop("email", $arr["obj_inst"]->prop("email.mail"));
				$mod = true;
			}

			if ($mod)
			{
				aw_disable_acl();
				$u->save();
				aw_restore_acl();
			}
		}

		if($this->can("view", $arr["obj_inst"]->meta("temp_ofr_id")))
		{
			$o = obj($arr["obj_inst"]->meta("temp_ofr_id"));
			$p = $arr["obj_inst"];

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
			// Don't wanna do it twice.
			$p->set_meta("temp_ofr_id", "");
			$p->save();
		}
		if(substr_count($arr["request"]["return_url"] , "action=new") && (substr_count($arr["request"]["return_url"] , "class=crm_task") || substr_count($arr["request"]["return_url"] , "class=crm_call") || substr_count($arr["request"]["return_url"] , "class=crm_meeting")))
		{
			$_SESSION["add_to_task"]["impl"] = $arr["obj_inst"]->id();
		}
	}

	function gen_code($o)
	{
		if ($o->prop("code") == "")
		{
			if ($this->can("view", ($ct = $o->prop("address"))))
			{
				$ct = obj($ct);
				$rk = $ct->prop("riik");
				if (is_oid($rk) && $this->can("view", $rk))
				{
					$rk = obj($rk);
					$code = substr(trim($rk->ord()), 0, 1);
					// get number of companies that have this country as an address
					$ol = new object_list(array(
						"class_id" => CL_CRM_PERSON,
						"CL_CRM_PERSON.address.riik.name" => $rk->name()
					));
					$ol2 = new object_list(array(
						"class_id" => CL_CRM_COMPANY,
						"CL_CRM_COMPANY.contact.riik.name" => $rk->name()
					));
					$code .= "-".sprintf("%04d", $ol->count() + $ol2->count()+1);
					$o->set_prop("code", $code);
					$o->save();
				}
			}
		}
	}

	function callback_pre_save($arr)
	{
		if(isset($arr["request"]["speaking"]) and is_array($arr["request"]["speaking"]))
		{
			foreach ($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_LANGUAGE_SKILL")) as $conn)
			{
				$conn->delete();
			}

			foreach ($arr["request"]["speaking"] as $lang => $level)
			{
				$obj = new object(array(
					"class_id" => CL_CRM_PERSON_LANGUAGE,
					"parent" => $arr["obj_inst"]->id(),
				));
				$obj->save();

				$obj->set_prop("language", $lang);
				$obj->set_prop("speaking", $arr["request"]["speaking"][$lang]);
				$obj->set_prop("writing", $arr["request"]["writing"][$lang]);
				$obj->set_prop("understanding", $arr["request"]["understanding"][$lang]);
				$obj->set_prop("kogemusi", $arr["request"]["kogemusi"][$lang]);

				$lang_obj = obj($lang);
				$obj->set_prop("name", $lang_obj->name());
				$obj->save();

				$arr["obj_inst"]->connect(array(
					"to" => $obj->id(),
					"reltype" => "RELTYPE_LANGUAGE_SKILL",
				));
			}
		}

		if(isset($arr["request"]["project_tasks"]) && is_array($arr["request"]["project_tasks"]) && count($arr["request"]["project_tasks"]))
		{
			$tasks = $this->get_work_project_tasks($arr["obj_inst"]->id());
			foreach($arr["request"]["project_tasks"] as $project => $task)
			{
				$tasks[$project]["task"] = $task;
			}
			$this->set_work_project_tasks($arr["obj_inst"]->id(), $tasks);
		}

		if($arr["request"]["group"] === "work_projects")
		{
			$tasks = $this->get_work_project_tasks($arr["obj_inst"]->id());
			foreach($tasks as $project => $data)
			{
				$tasks[$project]["selected"] = $arr["request"]["project_sel"][$project]?1:0;
			}
			$this->set_work_project_tasks($arr["obj_inst"]->id(), $tasks);
		}
		$arr["obj_inst"]->set_meta("no_create_user_yet", NULL);

		if (!empty($arr["request"]["ofr_id"]))
		{
			$arr["obj_inst"]->set_meta("temp_ofr_id", $arr["request"]["ofr_id"]);
		}
	}


	function do_education_tb(&$arr)
	{
		$tb = &$arr["prop"]["vcl_inst"];

		$tb->add_menu_button(array(
			'name'=>'new',
			'tooltip'=>t('Hariduse lisamine')
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta haridus"),
			"action" => "delete_objects",
			"confirm" => t("Oled kindel, et kustutada?"),
		));

		$tb->add_menu_item(array(
				'parent'=>'new',
				'text'=>t('P&otilde;hiharidus'),
				'link'=> $this->mk_my_orb('change' ,array(
					"id" => $arr["obj_inst"]->id(),
					"group" => $arr["request"]["group"],
					"etype" => "basic_edu",
				), CL_CRM_PERSON),

		));

		$tb->add_menu_item(array(
				'parent'=>'new',
				'text'=>t('Keskharidus'),
				'link'=> $this->mk_my_orb('change' ,array(
					"id" => $arr["obj_inst"]->id(),
					"group" => $arr["request"]["group"],
					"etype" => "secondary_edu",
				), CL_CRM_PERSON),
		));

		$tb->add_menu_item(array(
				'parent'=>'new',
				'text'=>t('K&otilde;rgharidus'),
				'link'=>$this->mk_my_orb('change' ,array(
					"id" => $arr["obj_inst"]->id(),
					"group" => $arr["request"]["group"],
					"etype" => "higher_edu",
				), CL_CRM_PERSON)
		));

		$tb->add_menu_item(array(
				'parent'=>'new',
				'text'=>t('Kutseharidus'),
				'link'=>$this->mk_my_orb('change' ,array(
					"id" => $arr["obj_inst"]->id(),
					"group" => $arr["request"]["group"],
					"etype" => "voc_edu",
				),	CL_CRM_PERSON)
		));
	}

	function do_education_table(&$arr)
	{
		$table = &$arr["prop"]["vcl_inst"];

		$table->define_field(array(
			"name" => "school",
			"caption" => t("Kool"),
			"sortable" => 1,
		));

		$table->define_field(array(
			"name" => "date_from",
			"caption" => t("Alates"),
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.y",
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "date_to",
			"caption" => t("Kuni"),
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.y",
			"align" => "center",
			"sortable" => 1,
		));

		$table->define_field(array(
			"name" => "etype",
			"caption" => t("Haridusliik"),
			"sortable" => 1,
		));

		$table->define_field(array(
			"name" => "profession",
			"caption" => t("Eriala"),
			"sortable" => 1
		));

		$table->define_chooser(array(
			"name" => "sel",
			"field" => "sel",
		));


		foreach ($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_BASIC_EDUCATION")) as $b_edu_conn)
		{
			$b_edu = $b_edu_conn->to();

			$table->define_data(array(
				"school" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $arr["obj_inst"]->id(),
						"group" => $arr["request"]["group"],
						"eoid" => $b_edu_conn->id(),
						"etype" => "basic_edu",
						), CL_CRM_PERSON),
					"caption" => $b_edu->prop("school"),
					)),
				"date_to" => $b_edu->prop("date_to"),
				"date_from" => $b_edu->prop("date_from"),
				"etype" => "P&otilde;hiharidus",
				"sel" => $b_edu_conn->id(),
			));
		}

		foreach ($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_SECONDARY_EDUCATION")) as $s_edu_conn)
		{
			$s_edu = $s_edu_conn->to();
			$table->define_data(array(
				"school" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $arr["obj_inst"]->id(),
						"group" => $arr["request"]["group"],
						"eoid" => $s_edu_conn->id(),
						"etype" => "secondary_edu",
						), CL_CRM_PERSON),
					"caption" => $s_edu->prop("school"),
					)),
				"date_to" => $s_edu->prop("date_to"),
				"date_from" => $s_edu->prop("date_from"),
				"etype" => t("Keskharidus"),
				"sel" => $s_edu_conn->id(),
			));
		}

		foreach ($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_HIGHER_EDUCATION")) as $h_edu_conn)
		{
			$h_edu = $h_edu_conn->to();
			$table->define_data(array(
				"school" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $arr["obj_inst"]->id(),
						"group" => $arr["request"]["group"],
						"eoid" => $h_edu_conn->id(),
						"etype" => "higher_edu",
					), CL_CRM_PERSON),
					"caption" => $h_edu->prop("school"),
				)),
				"date_to" => $h_edu->prop("date_to"),
				"date_from" => $h_edu->prop("date_from"),
				"profession" => $h_edu->prop("profession"),
				"etype" => t("K&otilde;rgharidus"),
				"sel" => $h_edu_conn->id(),
			));
		}

		foreach ($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_VOCATIONAL_EDUCATION")) as $v_edu_conn)
		{
			$v_edu = $v_edu_conn->to();
			$table->define_data(array(
				"school" => html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $arr["obj_inst"]->id(),
						"group" => $arr["request"]["group"],
						"etype" => "voc_edu",
						"eoid" => $v_edu_conn->id(),
					), CL_CRM_PERSON),
					"caption" => $v_edu->prop("school"),
				)),
				"date_to" => $v_edu->prop("date_to"),
				"date_from" => $v_edu->prop("date_from"),
				"profession" => $v_edu->prop("profession"),
				"etype" => t("Kutseharidus"),
				"sel" => $v_edu_conn->id(),
			));
		}

	}

	function do_language_skills_table(&$arr)
	{
		$classificator = get_instance(CL_CLASSIFICATOR);

		$options = $classificator->get_options_for(array(
			"name" => "language_list",
			"clid" => CL_CRM_PERSON,
		));

		$level_options = $classificator->get_options_for(array(
			"name" => "language_levels",
			"clid" => CL_CRM_PERSON,
		));

		$table = &$arr["prop"]["vcl_inst"] ;

		$table->define_field(array(
			"name" => "language",
			"caption" => t("Keel"),
		));

		$table->define_field(array(
			"name" => "speaking",
			"caption" => t("R&auml;&auml;kimine"),
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "writing",
			"caption" => t("Kirjutamine"),
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "understanding",
			"caption" => t("Arusaamine"),
			"align" => "center",
		));

		$table->define_field(array(
			"name" => "kogemusi",
			"caption" => t("Mitu aastat kogemusi"),
			"align" => "center",
		));


		foreach ($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_LANGUAGE_SKILL")) as $conn)
		{
			$obj = $conn->to();
			$lang_obj[$obj->prop("language")] = $obj;
		}


		foreach ($options as $key => $option)
		{
			if(is_object($lang_obj[$key]))
			{
				$kogemusi_val = $lang_obj[$key]->prop("kogemusi");
				$speaking_val = $lang_obj[$key]->prop("speaking");
				$understanding_val = $lang_obj[$key]->prop("understanding");
				$writing_val = $lang_obj[$key]->prop("writing");
			}

			$table->define_data(array(
				"language" => $option,

				"kogemusi" => html::textbox(array(
					"name" => "kogemusi[$key]",
					"value" => $kogemusi_val,
					"size" => 3,
				)),

				"speaking" => html::select(array(
					"options" => $level_options,
					"name" => "speaking[$key]",
					"value" => $speaking_val,
				)),
				"understanding" => html::select(array(
					"options" => $level_options,
					"name" => "understanding[$key]",
					"value" => $understanding_val,
				)),

				"writing" => html::select(array(
					"options" => $level_options,
					"name" => "writing[$key]",
					"value" => $writing_val,
				)),
			));
		}
	}

	/** Needed to add link to login menu to change your person obj.
		@attrib name=edit_my_person_obj is_public=1 caption="Muuda isikuobjekti andmeid"
	**/
	function edit_my_person_obj($arr)
	{
		$u_i = new user();
		return $this->mk_my_orb("change", array(
			"id" => $u_i->get_current_person()), CL_CRM_PERSON);
	}

	/*
		the user can be associated with a company in two ways
		1) crm_person.reltype_work stuff
		2) crm_person belongs to a crm_section which can belong
			to a company or another crm_section, eventually the section
			is attached to a company
	*/
	function get_work_contacts($arr)
	{
		$rtrn = $arr['obj_inst']->get_org_selection();

		foreach($arr['obj_inst']->get_section_selection() as $id => $name)
		{
			$obj = obj($id);
			$this->_get_work_contacts($obj,&$rtrn);
		}


/*		$conns = $arr['obj_inst']->connections_from(array(
			'type' => 'RELTYPE_SECTION'
		));

		foreach($conns as $conn)
		{
			$obj = $conn->to();
			$this->_get_work_contacts($obj,&$rtrn);
		}
*/
		$conns = $arr["obj_inst"]->connections_from(array(
			"type" => array(16, 67),
		));

		foreach($conns as $conn)
		{
			$obj = $conn->to();
			if(is_oid($obj->prop('org')))
				$rtrn[$obj->prop('org')] = $obj->prop('org.name');
			if(is_oid($obj->prop('section')))
			{
				$this->_get_work_contacts(obj($obj->prop('section')), &$rtrn);
			}
		}

		return $rtrn;
	}

	function _get_work_contacts(&$obj,&$data)
	{
		//maybe i found the company?
		if($obj->class_id()==CL_CRM_SECTION)
		{
			$conns = $obj->connections_to(array(
				'type' => 28 //crm_company.section
			));

			foreach($conns as $conn)
			{
				$data[$conn->prop('from')] = $conn->prop('from.name');
			}
		}

		//getting the sections
		$conns = $obj->connections_to(array(
			'type' => 1, //crm_section.section
		));
		foreach($conns as $conn)
		{
			$obj = $conn->from();
			$this->_get_work_contacts(&$obj,&$data);
		}
	}

	/** Returns the user object for the given person
		@attrib api=1 params=pos

		@param o required type=object
			Person object to return user for

		@returns
			User object if the person has an user or false if not
	**/
	function has_user($o)
	{
		if(!is_oid($o->id()))//mingite uute objektidega tuleb siit miski tuhandeid p2ringuid jne
		{
			return false;
		}
		$c = new connection();
		$res = $c->find(array(
			"to" => $o->id(),
			"from.class_id" => CL_USER,
			"type" => 2 // CL_USER.RELTYPE_PERSON
		));

		if (count($res))
		{
			$tmp = reset($res);
			if ($this->can("view", $tmp["from"]))
			{
				return obj($tmp["from"]);
			}
		}
		return false;
	}

	// this is a helper method, which can be used to add or update a specific
	// aspect of the person object
	function create_or_update_image($arr)
	{
		// this things needs to figure out whether this person already has an image
		// but this is going to be extraordinarily slow


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
			case "aw_bd_up":
				// create the field, but first, convert all persons bds to iso fields
				$this->db_query("SELECT birthday, oid FROM kliendibaas_isik");
				while ($row = $this->db_next())
				{
					$this->save_handle();
					if (is_numeric($row["birthday"]))
					{
						if ($row["birthday"] == -1)
						{
							$bd = "";
						}
						else
						{
							$bd = date("Y-m-d", $row["birthday"]);
						}
						$this->db_query("UPDATE kliendibaas_isik SET birthday = '$bd' WHERE oid = '$row[oid]'");
					}
					$this->restore_handle();
				}
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int",
				));
				// clear cache
				$c = get_instance("cache");
				$c->file_clear_pt("storage_object_data");
				return true;
				break;

			case "udef_ta1":
			case "udef_ta2":
			case "udef_ta3":
			case "udef_ta4":
			case "udef_ta5":
			case "user1":
			case "user2":
			case "user3":
			case "user4":
			case "user5":
			case "ext_id_alphanumeric":
			case "addinfo":
			case "other_mlang":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "text"
				));
				return true;

			case "not_working":
			case "udef_ch1":
			case "udef_ch2":
			case "udef_ch3":
			case "udef_chbox1":
			case "udef_chbox2":
			case "udef_chbox3":
			case "picture2":
			case "client_manager":
			case "aw_is_customer":
			case "cust_contract_creator":
			case "cust_contract_date":
			case "priority":
			case "bill_due_date_days":
			case "bill_penalty_pct":
			case "buyer_contract_person":
			case "address":
			case "edulevel":
			case "academic_degree":
			case "mlang":
			case "dl_can_use":
			case "cvactive":
			case "cvapproved":
			case "show_cnt":
			case "aw_external_id":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "int",
				));
				return true;

			case "code":
			// 20 is too short, damnit!
			case "drivers_license_2":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "varchar(255)"
				));
				return true;

			case "drivers_license":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "varchar(20)"
				));
				return true;

			case "previous_lastname":
				$this->db_add_col($tbl, array(
					"name" => $field,
					"type" => "varchar(50)"
				));
				return true;
		}
		return false;
	}

	function callback_mod_retval($arr)
	{
		$arr["args"]["cv_tpl"] = $arr["request"]["cv_tpl"];
		if($arr["request"]["group"] == "mails")
		{
			$arr["args"]["mails_s_name"] = $arr["request"]["mails_s_name"];
			$arr["args"]["mails_s_content"] = $arr["request"]["mails_s_content"];
			$arr["args"]["mails_s_customer"] = $arr["request"]["mails_s_customer"];
		}
	}

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		if(isset($_GET["add_to_task"]))
		{
			$arr["add_to_task"] = $_GET["add_to_task"];
		}
		if(isset($_GET["add_to_co"]))
		{
			$arr["add_to_co"] = $_GET["add_to_co"];
		}
		if(isset($_GET["ofr_id"]))
		{
			$arr["ofr_id"] = $_GET["ofr_id"];
		}
		if(isset($_GET["job_offer_id"]) && $this->can("view", $_GET["job_offer_id"]))
		{
			aw_session_set("job_offer_obj_id_for_candidate", $_GET["job_offer_id"]);
		}
	}

	function callback_mod_tab($arr)
	{
		if ($arr["id"] == "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}

		if ($arr["id"] == "my_stats")
		{
			$u = new user();
			if ($arr["obj_inst"]->id() != $u->get_current_person())
			{
				return false;
			}
		}
		return true;
	}

	function callback_get_transl($arr)
	{
		return $this->trans_callback($arr, $this->trans_props);
	}

	/**
		@attrib name=get_person_count_by_name

		@param co_name optional
		@param ignore_id optional
	**/
	function get_person_count_by_name($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"name" => $arr["co_name"],
			"lang_id" => array(),
			"site_id" => array(),
			"oid" => new obj_predicate_not($arr["ignore_id"])
		));
		die($ol->count()."\n");
	}

	/**
		@attrib name=go_to_first_person_by_name
		@param co_name optional
		@param return_url optional
	**/
	function go_to_first_person_by_name($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"name" => $arr["co_name"],
			"lang_id" => array(),
			"site_id" => array()
		));
		$o = $ol->begin();
		header("Location: ".html::get_change_url($o->id(), array("return_url" => $arr["return_url"], "warn_conflicts" => 1)));
		die();
	}

	function callback_generate_scripts($arr)
	{
		$f = "
		jQuery(document).ready(function()
		{
			jQuery('input[id^=\'languages_releditor_\'][id$=\'__other_\']').parent().parent().hide();
			jQuery('input[id^=\'skills_releditor\'][id$=\'__other_\']').parent().parent().hide();
			jQuery('#other_mlang').parent().parent().hide();
		});
		";
		if (!$arr["new"])
		{
			if (isset($arr["request"]["warn_conflicts"]) && $arr["request"]["warn_conflicts"] == 1)
			{
				// get conflicts list and warn user if there are any

				// to do this, get all projects for this company that have the current company as a side
				$u = new user();
				$ol = new object_list(array(
					"class_id" => CL_PROJECT,
					"CL_PROJECT.RELTYPE_SIDE.name" => $arr["obj_inst"]->name(),
					//"CL_PROJECT.RELTYPE_ORDERER" => $u->get_current_company(),
					"lang_id" => array(),
					"site_id" => array()
				));
				if ($ol->count())
				{
					$link = $this->mk_my_orb("disp_conflict_pop", array("id" => $arr["obj_inst"]->id()),CL_CRM_COMPANY);
					return $f."aw_popup_scroll('$link','confl','200','200');";
				}
			}
			return $f;
		}
		if (is_admin())
		{
		return
		"function aw_submit_handler() {".
		"if (document.changeform.firstname.value=='".$arr["obj_inst"]->prop("firstname")."' && document.changeform.lastname.value=='".$arr["obj_inst"]->prop("lastname")."') { return true; }".
		// fetch list of companies with that name and ask user if count > 0
		"var url = '".$this->mk_my_orb("get_person_count_by_name")."';".
		"url = url + '&co_name=' + document.changeform.firstname.value + ' '+document.changeform.lastname.value + '&ignore_id=".$arr["obj_inst"]->id()."';".
		"ct = aw_get_url_contents(url);".
		"num= parseInt(ct);".
		"if (num >0)
		{
			var ansa = confirm('Sellise nimega isik on juba olemas. Kas soovite minna selle objekti muutmisele?');
			if (ansa)
			{
				window.location = '".$this->mk_my_orb("go_to_first_person_by_name", array("return_url" => $arr["request"]["return_url"]))."&co_name=' + document.changeform.firstname.value + ' '+document.changeform.lastname.value;
				return false;
			}
		}".
		"return true;}".$f;
		}
		return $f;
	}

	// args:
	// obj_inst
	function get_current_usecase($arr)
	{
		$usecase = false;

		// if this is the current users employer, do nothing
		$u = new user();
		$co = $u->get_current_company();
		if ($co == $arr["obj_inst"]->company_id())
		{
			$usecase = CRM_PERSON_USECASE_COWORKER;
		}
		else
		if ($arr["obj_inst"]->prop("is_customer") == 1)
		{
			$usecase = CRM_PERSON_USECASE_CLIENT;
		}
		else
		if ($this->can("view", $arr["obj_inst"]->company_id()))
		{
			// customer employee
			$usecase = CRM_PERSON_USECASE_CLIENT_EMPLOYEE;
		}

		return $usecase;
	}


	function callback_mod_layout(&$arr)
	{
		if ($arr["name"] == "mails_table_l")
		{
			$arr["area_caption"] = sprintf(t("Isikule %s saadetud kirjad"), $arr["obj_inst"]->name());
		}
		return true;
	}

	function callback_get_cfgform($arr)
	{
		if(!is_oid($arr["obj_inst"]))
		{
			return false;
		}
		// if this is the current users employer, do nothing
		$u = new user();
		$co = $u->get_current_company();
		if ($co == $arr["obj_inst"]->company_id())
		{
			$s = get_instance(CL_CRM_SETTINGS);
			if (($o = $s->get_current_settings()))
			{
				return $o->prop("coworker_cfgform");
			}
		}
		else
		if ($arr["obj_inst"]->prop("is_customer") == 1)
		{
			// find the crm settings object for the current user
			$s = get_instance(CL_CRM_SETTINGS);
			if (($o = $s->get_current_settings()))
			{
				return $o->prop("s_p_cfgform");
			}
		}
		else
		if ($this->can("view", $arr["obj_inst"]->company_id()))
		{
			// customer employee cfgform
			$s = get_instance(CL_CRM_SETTINGS);
			if (($o = $s->get_current_settings()))
			{
				return $o->prop("customer_employer_cfgform");
			}
		}
		return false;
	}

	function _get_my_stats($arr)
	{
 		if ($arr["request"]["stats_s_type"] == "rows" || !isset($arr["request"]["stats_s_type"]))
		{
			$this->_get_my_stats_rows($arr);
			return;
		}
		$i = get_instance("applications/crm/crm_company_stats_impl");
		if (!$arr["request"]["MAX_FILE_SIZE"])
		{
			$arr["request"]["stats_s_time_sel"] = "cur_mon";
			$arr["request"]["MAX_FILE_SIZE"] = 1;
		}
		$arr["request"]["stats_s_res_type"] = "pers_det";
		$u = new user();
		$p = $u->get_current_person();
		$arr["request"]["stats_s_worker_sel"] = array($p => $p);
		classload("vcl/table");
		$t = new vcl_table;
		$arr["prop"]["vcl_inst"] = $t;
		$arr["request"]["ret"] = 1;
		$i->table_sum = true;
		$i->table_filt = true;
		$arr["prop"]["value"] = $i->_get_stats_s_res($arr);
	}

	function _resize_img($arr)
	{
		// if image is uploaded
		$img_o = $arr["obj_inst"]->get_first_obj_by_reltype($arr["prop"]["reltype"]);
		if (!$img_o)
		{
			return;
		}

		$s = get_instance(CL_CRM_SETTINGS);
		$settings = $s->get_current_settings();

		if ($settings)
		{
			$gal_conf = $settings->prop("person_img_settings");
			if ($this->can("view", $gal_conf))
			{
				$img_i = $img_o->instance();
				$img_i->do_resize_image(array(
					"o" => $img_o,
					"conf" => obj($gal_conf)
				));
			}
		}
	}

	/**
		@attrib name=gen_job_pdf nologin="1"
		@param id optional type=int
		@param cv_tpl optional type=string
	**/
	function gen_job_pdf($arr)
	{
		$pdf_gen = get_instance("core/converters/html2pdf");
		//session_cache_limiter("public");
		$tpl = $arr["cv_tpl"] ? ("cv/".basename($arr["cv_tpl"])) : false;
		die($pdf_gen->gen_pdf(array(
			"filename" => $arr["id"],
			"source" => $this->show_cv(array(
				"id" => $arr["id"],
				"cv" => $tpl,
				"cfgform" => get_instance(CL_CFGFORM)->get_sysdefault(),
			))
		)));
	}

	function get_cv_tpl()
	{
		$ret = array();

		$dir = aw_ini_get("tpldir")."/crm/person/cv/";
		$handle = opendir($dir);
		while(false !== ($file = readdir($handle)))
		{
			if(preg_match("/\\.tpl/", $file))
			{
				$ret[$file] = str_replace(".tpl", "", $file);
			}
		}

		$dir = aw_ini_get("site_tpldir")."/crm/person/cv/";
		$handle = opendir($dir);
		while(false !== ($file = readdir($handle)))
		{
			if(preg_match("/\\.tpl/", $file))
			{
				$ret[$file] = str_replace(".tpl", "", $file);
			}
		}
		return $ret;
	}

	/**
		@attrib name=show_cv all_args=1 params=name

		@parem id optional type=oid acl=view
			The oid of the person viewed.

		@param cv optional type=string
			The cv template to use.

		@param cfgform optional type=oid
			The cfgform to use.

		@param die optional type=boolean

		@param job_offer optional type=oid
	**/
	function show_cv($arr)
	{
		if(!is_oid($arr["id"]))
		{
			$arr["id"] = get_instance(CL_USER)->get_current_person();
		}
		if(!is_oid($arr["id"]))
		{
			return "";
		}
		get_instance("crm_person_obj")->handle_show_cnt(array(
			"action" => "view",
			"id" => $arr["alias"]["target"],
		));

		$img_inst = get_instance(CL_IMAGE);
		$phone_inst = get_instance(CL_CRM_PHONE);
		$edu_inst = get_instance(CL_CRM_PERSON_EDUCATION);
		$pers_lang_inst = get_instance(CL_CRM_PERSON_LANGUAGE);
		$pm_inst = get_instance(CL_PERSONNEL_MANAGEMENT);
		$sm_inst = get_instance(CL_CRM_SKILL_MANAGER);
		$jw_inst = get_instance(CL_PERSONNEL_MANAGEMENT_JOB_WANTED);
		$rate_inst = get_instance(CL_RATE);
		$cff_inst = get_instance(CL_CFGFORM);
		$file_inst = new file();

		if(!$arr["cv"])
		{
			$arr["cv"] = "cv/".basename(key($this->get_cv_tpl()));
		}
		$ob = new object($arr["id"]);
		$person_obj = current($ob->connections_to(/*array("from.class_id" => CL_CRM_PERSON)*/));
		if(!is_object($person_obj))
		{
			$person_obj = $ob;
			//return false;
		}
		$person_obj = &obj($person_obj->prop("from"));

		// Dunno where prop("email") gets its value, but it's not OID!
		if(is_oid($person_obj->prop("email")))
			$email_obj = &obj($person_obj->prop("email"));
		else
			// Not the neatest way to solve it, but seriously. What if person has no e-mail??
			$email_obj = new object();
		$phone_obj = &obj($person_obj->prop("phone"));

		// Why did I write the next line of code? Good question. See init.aw:319. -kaarel
		$this->template_dir = aw_ini_get("site_tpldir")."/crm/person";
		$this->read_template($arr["cv"]);

		if($person_obj->prop("gender") == 1)
		{
			$gender ="Mees";
		}
		else
		{
			$gender ="Naine";
		}

		foreach ($ob->connections_from(array("type" => "RELTYPE_PREVIOUS_JOB")) as $kogemus)
		{
			$kogemus = $kogemus->to();

			$this->vars(array(
				"company" => $kogemus->prop_str("org"),
				"start" => get_lc_date($kogemus->prop("start")),
				"end" => get_lc_date($kogemus->prop("end")),
				"profession" => $kogemus->prop("proffession"),
				"duties" => $kogemus->prop("tasks"),
			));
			$kogemused_temp .= $this->parse("WORK_EXPERIENCES");
		}

		// additional training
		foreach($ob->connections_from(array("type" => "RELTYPE_ADD_EDUCATION")) as $conn)
		{
			$educ = $conn->to();
			$this->vars(array(
				"education_company" => $educ->prop("org"),
				"education_theme" => $educ->prop("field"),
				"education_time" => get_lc_date($educ->prop("time")),
				"education_length" => $educ->prop("length"),
			));
			$add_training .= $this->parse("ADDITIONAL_TRAINING");
		}

		//Valdkondade nimekiri
		foreach ($ob->connections_from(array("type" => "RELTYPE_TEGEVUSVALDKOND")) as $sector)
		{
			$this->vars(array(
				"sector" => $sector->prop("to.name"),
			));
			$tmp_sectors.=$this->parse("sectors");
		}

		//Hariduste nimekiri
		foreach ($ob->connections_from(array("type" => "RELTYPE_EDUCATION")) as $haridus)
		{
			$haridus = $haridus->to();
			$haridus->prop("algusaasta");
			$period = $haridus->prop("algusaasta")." - ". $haridus->prop("loppaasta");


			$eriala = array_pop($haridus->connections_from(array("type" => "RELTYPE_ERIALA")));
			if (is_object($eriala))
			{
				$ename = $eriala->prop("to.name");
			}

			$this->vars(array(
				"oppevorm" => 	$haridus->prop("oppevorm"),
				"oppeaste" => 	$haridus->prop("oppeaste"),
				"oppekava" => 	$haridus->prop("oppekava"),
				"teaduskond" => $haridus->prop("teaduskond"),
				"eriala" =>		$ename,
				"school_name" =>$haridus->prop("kool"),
				"period" => 	$period,
				"addional_info" => $haridus->prop("lisainfo_edu"),
				"kogemused_list" => $kogemused_temp,
			));

			$temp_edu.= $this->parse("education");
		}

		foreach ($ob->connections_from(array("type" => "RELTYPE_JUHILUBA")) as $driving_license)
		{
			$driving_licenses.= ",".$driving_license->prop("to.name");
		}

		$ck = "";
		foreach($ob->connections_from(array("type" => "RELTYPE_ARVUTIOSKUS")) as $c)
		{
			$to = $c->to();
			$oskus = $to->prop("oskus");
			if ($oskus)
			{
				$oo = obj($oskus);
				$this->vars(array(
					"skill_name" => $oo->name()
				));
			}
			$tase = $to->prop("tase");
			if ($tase)
			{
				$oo = obj($tase);
				$this->vars(array(
					"skill_skill" => $oo->name()
				));
			}
			$ck .= $this->parse("COMP_SKILL");
		}

		$lsk = "";
		foreach($ob->connections_from(array("type" => "RELTYPE_LANG")) as $c)
		{
			$to = $c->to();
			$oskus = $to->prop("keel");
			if ($oskus)
			{
				$oo = obj($oskus);
				$this->vars(array(
					"skill_name" => $oo->name()
				));
			}
			$tase = $to->prop("tase");
			if ($tase)
			{
				$oo = obj($tase);
				$this->vars(array(
					"skill_skill" => $oo->name()
				));
			}
			$lsk .= $this->parse("LANG_SKILL");
		}

		$dsk = array();
		foreach($ob->connections_from(array("type" => "RELTYPE_JUHILUBA")) as $c)
		{
			$this->vars(array(
				"skill_name" => $c->prop("to.name"),
				"driving_since" => $ob->prop("driving_since")
			));
			$dsk[] = $this->parse("DRIVE_SKILL");
		}

		$ed = "";
		foreach($ob->connections_from(array("type" => "RELTYPE_EDUCATION")) as $c)
		{
			$to = $c->to();
			$d_from = $to->prop("algusaasta");
			if ($to->prop("date_from") > 100)
			{
				$d_from = get_lc_date($to->prop("date_from"),LC_DATE_FORMAT_LONG_FULLYEAR);
			}
			$d_to = $to->prop("loppaasta");
			if ($to->prop("date_to") > 100)
			{
				$d_to = get_lc_date($to->prop("date_to"),LC_DATE_FORMAT_LONG_FULLYEAR);
			}
			$this->vars(array(
				"from" => $d_from,
				"to" => $d_to,
				"where" => $to->prop("kool"),
				"extra" => nl2br($to->prop("lisainfo_edu"))
			));
			$ed .= $this->parse("ED");
		}
		$cur_comp = get_current_company();

		$logo = is_object($cur_comp) ? $cur_comp->prop("logo") : 0;
		foreach($this->get_work_project_tasks($ob->id()) as $project => $data)
		{
			if(!$data["selected"])
			{
				continue;
			}
			$p = obj($project);
			$this->vars(array(
				"project_start" =>   get_lc_date($p->prop("start")),
				"project_end" => get_lc_date($p->prop("end")),
				"project_contract" => ($_t = $p->name())?$_t:t("-"),
				"project_tasks" => ($_t = $data["task"])?$_t:t("-"),
				"project_value" => ($_t = $p->prop("proj_price"))?$_t:t("-"),
				"project_roles" => ($_t = join(", ", $this->get_project_roles(array(
					"person" => $ob->id(),
					"project" => $project,
				))))?$_t:t("-"),
			));
			$projects .= $this->parse("PROJECT");
		}

		$gidlist = aw_global_get("gidlist_oid");
		$personname = $person_obj->name();
		$cur_job = $this->has_current_job_relation($ob->id());
		$bd = split("-", $ob->prop("birthday"));
		$bd = mktime(0,0,0,$bd[1],$bd[2], $bd[0]);
		$tio = $cur_job?(time() - $cur_job->prop("start")):false;
		$m = $tio?round(((($tio/60)/60)/24)/30, 0):0;
		$y = $tio?floor((((($tio/60)/60)/24)/30)/12):0;
		$time = $y?sprintf(t("%s %s, %s %s"), $y, $m,(($y==1)?t("year"):t("years")), (($m == 1)?t("month"):t("months"))):sprintf("%s %s", $m ,(($m == 1)?t("month"):t("months")));
		$this->vars(array(
			"COMP_SKILL" => $ck,
			"LANG_SKILL" => $lsk,
			"DRIVE_SKILL" => join(",", $dsk),
			"WORK_EXPERIENCES" => $kogemused_temp,
			"ED" => $ed,
			"PROJECT" => $projects,
			"ADDITIONAL_TRAINING" => $add_training,
			"recommenders" => nl2br($ob->prop("soovitajad")),
			"first_name" => ucfirst(strtolower($ob->prop("firstname"))),
			"last_name" => ucfirst(strtolower($ob->prop("lastname"))),
			"modified" => get_lc_date($ob->modified()),
			"birthday" => date("d.m.Y", $bd),
			"social_status" => $person_obj->prop("social_status"),
			"mail" => html::href(array(
				"url" => "mailto:" . $email_obj->prop("mail"),
				"caption" => $email_obj->prop("mail"),
			)),
			"phone" => $phone_obj->name(),
			"sectors" => $tmp_sectors,
			"education" => $temp_edu,
			"driving_licenses" => $driving_licenses,
			"addional_info" => $ob->prop("job_addinfo"),
			"gender" => $gender,
			"cur_org_start" => $cur_job?get_lc_date($cur_job->prop("start")):"",
			"cur_org_position" => $ob->prop_str("rank"),
			"cur_org_time" => $time,
			"picture_url" => $img_inst->get_url_by_id($ob->prop("picture")),
			"company_logo" => $this->can("view",$logo)?$img_inst->get_url_by_id($logo):"",
		));

		// Don't want anything broken, so I leave it exactly as it is and add my share here. -kaarel
		if($this->can("view", $arr["id"]))
		{
			$o = obj($arr["id"]);
		}
		else
		{
			return false;
		}

		$prefixx = array(
			CL_CRM_PERSON => "crm_person.",
			CL_CRM_RECOMMENDATION => "recommendation.",
			CL_PERSONNEL_MANAGEMENT_JOB_WANTED => "personnel_management_job_wanted.",
			CL_CRM_PERSON_EDUCATION => "crm_person_education.",
			CL_CRM_PERSON_ADD_EDUCATION => "crm_person_add_education.",
			CL_CRM_PERSON_WORK_RELATION => "crm_person_work_relation.",
			CL_CRM_COMPANY_RELATION => "crm_company_relation.",
			CL_CRM_PERSON_LANGUAGE => "crm_person_language.",
			CL_PERSONNEL_MANAGEMENT_JOB_OFFER => "personnel_management_job_offer.",
		);
		$pmprops = array(
			CL_CRM_PERSON => "default_offers_cfgform",
			CL_CRM_RECOMMENDATION => "cff_recommendation",
			CL_PERSONNEL_MANAGEMENT_JOB_WANTED => "cff_job_wanted",
			CL_CRM_PERSON_EDUCATION => "cff_education",
			CL_CRM_PERSON_ADD_EDUCATION => "cff_add_education",
			CL_CRM_PERSON_WORK_RELATION => "cff_work_relation",
			CL_CRM_COMPANY_RELATION => "cff_company_relation",
			CL_CRM_PERSON_LANGUAGE => "cff_person_language",
			CL_PERSONNEL_MANAGEMENT_JOB_OFFER => "cff_job_offer",
		);

		// Maybe I've changed aw_ini_get(user_interface.default_language)
		$cfgutils = get_instance("cfgutils");
		foreach(array_keys($pmprops) as $clid)
		{
			$cfgutils->load_properties(array(
				"clid" => $clid,
			));
		}

		$charset = aw_global_get("charset");
		if($o->meta("insertion_lang"))
		{
			$llist = aw_ini_get("languages.list");
			$charset = $llist[$o->meta("insertion_lang")]["charset"];
			/*
			if($charset != $llist[$o->meta("insertion_lang")]["charset"])
			{
				// If the language I have the captions in and the language I have the CV in are different, UTF-8 is the only solution
				aw_global_set("charset", "UTF-8");
				$charset = "UTF-8";
			}
			*/
		}
		$this->vars(array(
			"charset" => $charset,
		));

		if($this->can("view", $arr["cfgform"]))
		{
			$proplist = $cff_inst->get_cfg_proplist($arr["cfgform"]);
		}
		else
		{
			$proplist = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist = array();

		//////////////////// CAPTIONS

		foreach($prefixx as $clid => $prefix)
		{
			$tmpo = obj();
			// First we set the captions to original

			$tmpo->set_class_id($clid);
			foreach($tmpo->get_property_list() as $prop_id => $prop_data)
			{
				$this->vars(array(
					//$prefix.$prop_id.".caption" => htmlentities($prop_data["caption"], ENT_NOQUOTES, "ISO-8859-1", false),
					$prefix.$prop_id.".caption" => iconv("", $charset, $prop_data["caption"]),
				));
			}

			// .. then override them with default cfgforms

			$cff_id = $cff_inst->get_sysdefault(array("clid" => $clid));
			if(is_oid($cff_id))
			{
				$cpl = $cff_inst->get_cfg_proplist($cff_id);
				foreach($cpl as $prop_id => $prop_data)
				{
					$this->vars(array(
						$prefix.$prop_id.".caption" => htmlentities(html_entity_decode($prop_data["caption"])),
					));
				}
			}

			// .... and finally override them with cfgforms set in personnel management object.

			$pm = obj($pm_inst->get_sysdefault());

			$cff_id = $pm->prop($pmprops[$clid]);
			if(is_oid($cff_id))
			{
				$cpl = $cff_inst->get_cfg_proplist($cff_id);
				foreach($cpl as $prop_id => $prop_data)
				{
					$this->vars(array(
						$prefix.$prop_id.".caption" => htmlentities(html_entity_decode($prop_data["caption"])),
					));
				}
			}

		}

		////////////// CAPTIONS ENDED



	///////////////////////// USERDEFINED STUFF

		// SUB: CRM_PERSON.UDEF_CH{n}
		for($i = 1; $i <= 3; $i++)
		{
			if(strlen($o->prop("udef_ch".$i)) > 0)
			{
				$this->vars(array(
					"crm_person.udef_ch".$i => $o->prop("udef_ch".$i),
				));
				$this->vars(array(
					"CRM_PERSON.UDEF_CH".$i => $this->parse("CRM_PERSON.UDEF_CH".$i),
				));
			}
		}
		// END SUB: CRM_PERSON.UDEF_CH{n}

		// SUB: CRM_PERSON.UDEF_CHBOX{n}
		for($i = 1; $i <= 3; $i++)
		{
			if(strlen($o->prop("udef_chbox".$i)) > 0)
			{
				$this->vars(array(
					"crm_person.udef_chbox".$i => $o->prop("udef_chbox".$i) ? t("Jah") : t("Ei"),
				));
				$this->vars(array(
					"CRM_PERSON.UDEF_CHBOX".$i => $this->parse("CRM_PERSON.UDEF_CHBOX".$i),
				));
			}
		}
		// END SUB: CRM_PERSON.UDEF_CHBOX{n}

		// SUB: CRM_PERSON.USER{n}
		for($i = 1; $i <= 5; $i++)
		{
			if(strlen($o->prop("user".$i)) > 0)
			{
				$this->vars(array(
					"crm_person.user".$i => $o->prop("user".$i),
				));
				$this->vars(array(
					"CRM_PERSON.USER".$i => $this->parse("CRM_PERSON.USER".$i),
				));
			}
		}
		// END SUB: CRM_PERSON.USER{n}

		// SUB: CRM_PERSON.UDEF_TA{n}
		for($i = 1; $i <= 5; $i++)
		{
			if(strlen($o->prop("udef_ta".$i)) > 0)
			{
				$this->vars(array(
					"crm_person.udef_ta".$i => nl2br($o->prop("udef_ta".$i)),
				));
				$this->vars(array(
					"CRM_PERSON.UDEF_TA".$i => $this->parse("CRM_PERSON.UDEF_TA".$i),
				));
			}
		}
		// END SUB: CRM_PERSON.UDEF_TA{n}

		// SUB: CRM_PERSON.USERVAR{n}
		for($i = 1; $i <= 10; $i++)
		{
			$USERVAR = "";
			if(!is_array($o->prop("uservar".$i)) && $o->prop("uservar".$i))
			{
				$uservar[$i] = array($o->prop("uservar".$i));
			}
			else
			{
				$uservar[$i] = $o->prop("uservar".$i);
			}

			if(is_array($uservar[$i]))
			{
				foreach($uservar[$i] as $uv)
				{
					$this->vars(array(
						"crm_person.uservar".$i => $o->prop("uservar".$i.".name"),
					));
					$USERVAR .= $this->parse("CRM_PERSON.USERVAR".$i);
				}
				$this->vars(array(
					"CRM_PERSON.USERVAR".$i => $USERVAR,
				));
			}
		}
		// END SUB: CRM_PERSON.USERVAR{n}


	///////////////////////// END USERDEFINED STUFF

		// SUB: CRM_PERSON.PICTURE
		if($this->can("view", $o->prop("picture")) && (array_key_exists("picture", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.picture" => $img_inst->view(array("id" => $o->prop("picture"))),
			));
			$this->vars(array(
				"CRM_PERSON.PICTURE" => $this->parse("CRM_PERSON.PICTURE"),
			));
		}
		// END SUB: CRM_PERSON.PICTURE

		// SUB: CRM_PERSON.PERSONAL_INFO
		$parse_cppi = 0;

		//		SUB: CRM_PERSON.NAME
		if(strlen($o->name()) > 0 && (array_key_exists("name", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.name" => $o->name(),
			));
			$this->vars(array(
				"CRM_PERSON.NAME" => $this->parse("CRM_PERSON.NAME"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.NAME

		//		SUB: CRM_PERSON.PREVIOUS_LASTNAME
		if(strlen($o->previous_lastname) > 0 && (array_key_exists("previous_lastname", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.previous_lastname" => $o->previous_lastname,
			));
			$this->vars(array(
				"CRM_PERSON.PREVIOUS_LASTNAME" => $this->parse("CRM_PERSON.PREVIOUS_LASTNAME"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.PREVIOUS_LASTNAME

		//		SUB: CRM_PERSON.PERSONAL_ID
		// This needs some heavier check. Maybe I'll come back to it later. Don't think it's that important. Prolly should be checked already when setting the property.
		if(preg_match("/[34]\d{10}/", $o->prop("personal_id")) && (array_key_exists("personal_id", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.personal_id" => $o->prop("personal_id"),
			));
			$this->vars(array(
				"CRM_PERSON.PERSONAL_ID" => $this->parse("CRM_PERSON.PERSONAL_ID"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.PERSONAL_ID

		//		SUB: CRM_PERSON.BIRTHDAY
		// Birthday is saved in YYYY-MM-DD format.
		//if(preg_match("(19|20)[1-9]{2}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])", $o->prop("birthday")))
		if(strlen($o->prop("birthday")) == 10 && (array_key_exists("birthday", $proplist) || count($proplist) == 0))
		{
			$date_bits = explode("-", $o->prop("birthday"));
			$this->vars(array(
				"crm_person.birthday" => get_lc_date(mktime(0, 0, 0, $date_bits[1], $date_bits[2], $date_bits[0]), LC_DATE_FORMAT_SHORT_FULLYEAR),
				"crm_person.age" => $o->get_age(),
			));
			$this->vars(array(
				"CRM_PERSON.BIRTHDAY" => $this->parse("CRM_PERSON.BIRTHDAY"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.BIRTHDAYGENDER

		//		SUB: CRM_PERSON.GENDER
		if($o->prop("gender") == 2 || $o->prop("gender") == 1 && (array_key_exists("gender", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.gender" => ($o->prop("gender") == 1) ? t("Mees") : t("Naine"),
			));
			$this->vars(array(
				"CRM_PERSON.GENDER" => $this->parse("CRM_PERSON.GENDER"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.GENDER

		//		SUB: CRM_PERSON.NATIONALITY
		if($this->can("view", $o->prop("nationality")) && (array_key_exists("nationality", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.nationality" => $o->prop("nationality.name"),
			));
			$this->vars(array(
				"CRM_PERSON.NATIONALITY" => $this->parse("CRM_PERSON.NATIONALITY"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.NATIONALITY

		//		SUB: CRM_PERSON.MLANG
		if($this->can("view", $o->prop("mlang")) && (array_key_exists("mlang", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.mlang" => $o->prop("mlang.name"),
			));
			$this->vars(array(
				"CRM_PERSON.MLANG" => $this->parse("CRM_PERSON.MLANG"),
			));
			$parse_cppi++;
		}
		else
		if(strlen($o->prop("other_mlang")) > 0 && (array_key_exists("mlang", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.mlang" => $o->prop("other_mlang"),
			));
			$this->vars(array(
				"CRM_PERSON.MLANG" => $this->parse("CRM_PERSON.MLANG"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.MLANG

		//		SUB: CRM_PERSON.EDULEVEL
		if($o->prop("edulevel") > 0 && (array_key_exists("edulevel", $proplist) || count($proplist) == 0))
		{
			$options = $this->edulevel_options;
			$this->vars(array(
				"crm_person.edulevel" => $options[$o->prop("edulevel")],
			));
			$this->vars(array(
				"CRM_PERSON.EDULEVEL" => $this->parse("CRM_PERSON.EDULEVEL"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.EDULEVEL

		//		SUB: CRM_PERSON.ACADEMIC_DEGREE
		if($o->prop("academic_degree") > 0 && (array_key_exists("academic_degree", $proplist) || count($proplist) == 0))
		{
			$options = $this->academic_degree_options;
			$this->vars(array(
				"crm_person.academic_degree" => $options[$o->prop("academic_degree")],
			));
			$this->vars(array(
				"CRM_PERSON.ACADEMIC_DEGREE" => $this->parse("CRM_PERSON.ACADEMIC_DEGREE"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.EDULEVEL

		//		SUB: CRM_PERSON.SOCIAL_STATUS
		if($o->prop("social_status") > 0 && $o->prop("social_status") < 5 && (array_key_exists("social_status", $proplist) || count($proplist) == 0))
		{
			$social_status = array(
				3 => t("Vallaline"),
				1 => t("Abielus"),
				2 => t("Lahutatud"),
				4 => t("Vabaabielus"),
			);
			$this->vars(array(
				"crm_person.social_status" => $social_status[$o->prop("social_status")],
			));
			$this->vars(array(
				"CRM_PERSON.SOCIAL_STATUS" => $this->parse("CRM_PERSON.SOCIAL_STATUS"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.SOCIAL_STATUS

		//		SUB: CRM_PERSON.CHILDREN1
		if($o->prop("children1") > 0 && (array_key_exists("children1", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.children1" => $o->prop("children1"),
			));
			$this->vars(array(
				"CRM_PERSON.CHILDREN1" => $this->parse("CRM_PERSON.CHILDREN1"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.CHILDREN1

		//		SUB: CRM_PERSON.CV_FILE
		if($this->can("view", $o->prop("cv_file")) && (array_key_exists("cv_file", $proplist) || count($proplist) == 0))
		{
			$cv_file = obj($o->prop("cv_file"));
			$this->vars(array(
				//"crm_person.cv_file_url" => obj_link($cv_file->id()),
				"crm_person.cv_file_url" => $file_inst->get_url($cv_file->id(), $cv_file->name()),
				"crm_person.cv_file" => $cv_file->name(),
			));
			$this->vars(array(
				"CRM_PERSON.CV_FILE" => $this->parse("CRM_PERSON.CV_FILE"),
			));
			$parse_cppi++;
		}
		//		END SUB: CRM_PERSON.CV_FILE

		//		SUB: CRM_PERSON.MODIFIED
		$this->vars(array(
			"crm_person.modified" => get_lc_date($o->prop("modified"), LC_DATE_FORMAT_SHORT_FULLYEAR),
		));
		$this->vars(array(
			"CRM_PERSON.MODIFIED" => $this->parse("CRM_PERSON.MODIFIED"),
		));
		//		END SUB: CRM_PERSON.MODIFIED

		if($parse_cppi > 0)
		{
			$this->vars(array(
				"CRM_PERSON.PERSONAL_INFO" => $this->parse("CRM_PERSON.PERSONAL_INFO"),
			));
		}
		// END SUB: CRM_PERSON.PERSONAL_INFO

		// SUB: CITIZENSHIPS
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_CITIZENSHIP",
		));
		if(count($conns) > 0)
		{
			$CITIZENSHIP = "";
			foreach($conns as $conn)
			{
				// SUB: CITIZENSHIP
				$cs = $conn->to();
				$start = $cs->prop("start");
				$start = mktime(0, 0, 0, $start["month"], $start["day"], $start["year"]);
				$end = $cs->prop("end");
				$end = mktime(0, 0, 0, $end["month"], $end["day"], $end["year"]);
				$this->vars(array(
					"citizenship.country" => $cs->prop("country.name"),
					"citizenship.start" => get_lc_date($start, LC_DATE_FORMAT_SHORT_FULLYEAR),
					"citizenship.end" => $end >= $start ? get_lc_date($end, LC_DATE_FORMAT_SHORT_FULLYEAR) : t("t&auml;nap&auml;evani"),
				));
				$CITIZENSHIP .= $this->parse("CITIZENSHIP");
				// END SUB: CITIZENSHIP
			}
			$this->vars(array(
				"CITIZENSHIP" => $CITIZENSHIP,
			));
			$this->vars(array(
				"CITIZENSHIPS" => $this->parse("CITIZENSHIPS"),
			));
		}
		// END SUB: CITIZENSHIPS

		// SUB: CRM_PERSON_EDUCATIONS
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_EDUCATION",
		));
		$options = $edu_inst->degree_options;
		$CRM_PERSON_EDUCATION = "";
		$anything_in_progress = $this->anything_in_progress($conns);

		$parse_cpe = 0;
		$cff_e = $cff_inst->get_sysdefault(array("clid" => CL_CRM_PERSON_EDUCATION));
		if($cff_e)
		{
			$proplist_education = $cff_inst->get_cfg_proplist($cff_e);
		}
		else
		{
			$proplist_education = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_education = array();

		$props = array("degree", "field", "speciality", "main_speciality", "obtain_language", "start", "end", "end_date", "diploma_nr");
		foreach($props as $prop)
		{
			if(array_key_exists($prop, $proplist_education) || count($proplist_education) == 0)
			//if(true)
			{
				$this->vars(array(
					"CRM_PERSON_EDUCATIONS.HEADER.".strtoupper($prop) => $this->parse("CRM_PERSON_EDUCATIONS.HEADER.".strtoupper($prop)),
				));
			}
		}
		if((array_key_exists("in_progress", $proplist_education) || count($proplist_education) == 0) && $anything_in_progress)
		//if($anything_in_progress)
		{
			$this->vars(array(
				"CRM_PERSON_EDUCATIONS.HEADER.IN_PROGRESS" => $this->parse("CRM_PERSON_EDUCATIONS.HEADER.IN_PROGRESS"),
			));
		}
		if(array_key_exists("school1", $proplist_education) || array_key_exists("school2", $proplist_education) || count($proplist_education) == 0)
		//if(true)
		{
			$this->vars(array(
				"CRM_PERSON_EDUCATIONS.HEADER.SCHOOL" => $this->parse("CRM_PERSON_EDUCATIONS.HEADER.SCHOOL"),
			));
		}

		foreach($conns as $conn)
		{
			//	SUB: CRM_PERSON_EDUCATION
			$to = $conn->to();
			$this->vars(array(
				"crm_person_education.school" => is_oid($to->prop("school1")) ? $to->prop("school1.name") : $to->prop("school2"),
				"crm_person_education.degree" => $options[$to->prop("degree")],
				"crm_person_education.field" => $to->prop("field.name"),
				"crm_person_education.speciality" => $to->prop("speciality"),
				"crm_person_education.main_speciality" => ($to->prop("main_speciality") == 1) ? t("Jah") : t("Ei"),
				"crm_person_education.in_progress" => ($to->prop("in_progress") == 1) ? t("Jah") : "",
				"crm_person_education.obtain_language" => $to->prop("obtain_language.name"),
				"crm_person_education.start" => $to->prop("start") ? date("Y", $to->prop("start")) : t("M&auml;&auml;ramata"),
				"crm_person_education.end" => $to->prop("end") ? date("Y", $to->prop("end")) : t("M&auml;&auml;ramata"),
				"crm_person_education.end_date" => get_lc_date($to->prop("end_date"), LC_DATE_FORMAT_SHORT_FULLYEAR),
				"crm_person_education.diploma_nr" => $to->prop("diploma_nr"),
			));
			foreach($props as $prop)
			{
				if(array_key_exists($prop, $proplist_education) || count($proplist_education) == 0)
				//if(true)
				{
					$this->vars(array(
						"CRM_PERSON_EDUCATION.".strtoupper($prop) => $this->parse("CRM_PERSON_EDUCATION.".strtoupper($prop)),
					));
				}
			}
			if((array_key_exists("in_progress", $proplist_education) || count($proplist_education) == 0) && $anything_in_progress)
			//if($anything_in_progress)
			{
				$this->vars(array(
					"CRM_PERSON_EDUCATION.IN_PROGRESS" => $this->parse("CRM_PERSON_EDUCATION.IN_PROGRESS"),
				));
			}
			if(array_key_exists("school1", $proplist_education) || array_key_exists("school2", $proplist_education) || count($proplist_education) == 0)
			//if(true)
			{
				$this->vars(array(
					"CRM_PERSON_EDUCATION.SCHOOL" => $this->parse("CRM_PERSON_EDUCATION.SCHOOL"),
				));
			}

			$CRM_PERSON_EDUCATION .= $this->parse("CRM_PERSON_EDUCATION");

			$parse_cpe++;
			//	END SUB: CRM_PERSON_EDUCATION
		}

		if($parse_cpe > 0)
		{
			$this->vars(array(
				"CRM_PERSON_EDUCATIONS.HEADER" => $this->parse("CRM_PERSON_EDUCATIONS.HEADER"),
				"CRM_PERSON_EDUCATION" => $CRM_PERSON_EDUCATION,
			));
			$this->vars(array(
				"CRM_PERSON_EDUCATIONS" => $this->parse("CRM_PERSON_EDUCATIONS"),
			));
		}
		// END SUB: CRM_PERSON_EDUCATIONS

		// SUB: CRM_PERSON_EDUCATIONS_2
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_EDUCATION_2",
		));
		$options = $edu_inst->degree_options;
		$CRM_PERSON_EDUCATION_2 = "";
		$anything_in_progress = $this->anything_in_progress($conns);

		$parse_cpe_2 = 0;
		$cff_e = $cff_inst->get_sysdefault(array("clid" => CL_CRM_PERSON_EDUCATION));
		if($cff_e)
		{
			$proplist_education = $cff_inst->get_cfg_proplist($cff_e);
		}
		else
		{
			$proplist_education = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_education = array();

		$props = array("degree", "field", "speciality", "main_speciality", "obtain_language", "start", "end", "end_date", "diploma_nr");
		foreach($props as $prop)
		{
			if(array_key_exists($prop, $proplist_education) || count($proplist_education) == 0)
			//if(true)
			{
				$this->vars(array(
					"CRM_PERSON_EDUCATIONS_2.HEADER.".strtoupper($prop) => $this->parse("CRM_PERSON_EDUCATIONS_2.HEADER.".strtoupper($prop)),
				));
			}
		}
		if((array_key_exists("in_progress", $proplist_education) || count($proplist_education) == 0) && $anything_in_progress)
		//if($anything_in_progress)
		{
			$this->vars(array(
				"CRM_PERSON_EDUCATIONS_2.HEADER.IN_PROGRESS" => $this->parse("CRM_PERSON_EDUCATIONS_2.HEADER.IN_PROGRESS"),
			));
		}
		if(array_key_exists("school1", $proplist_education) || array_key_exists("school2", $proplist_education) || count($proplist_education) == 0)
		//if(true)
		{
			$this->vars(array(
				"CRM_PERSON_EDUCATIONS_2.HEADER.SCHOOL" => $this->parse("CRM_PERSON_EDUCATIONS_2.HEADER.SCHOOL"),
			));
		}

		foreach($conns as $conn)
		{
			//	SUB: CRM_PERSON_EDUCATION_2
			$to = $conn->to();
			$this->vars(array(
				"crm_person_education_2.school" => is_oid($to->prop("school1")) ? $to->prop("school1.name") : $to->prop("school2"),
				"crm_person_education_2.degree" => $options[$to->prop("degree")],
				"crm_person_education_2.field" => $to->prop("field.name"),
				"crm_person_education_2.speciality" => $to->prop("speciality"),
				"crm_person_education_2.main_speciality" => ($to->prop("main_speciality") == 1) ? t("Jah") : t("Ei"),
				"crm_person_education_2.in_progress" => ($to->prop("in_progress") == 1) ? t("Jah") : "",
				"crm_person_education_2.obtain_language" => $to->prop("obtain_language.name"),
				"crm_person_education_2.start" => $to->prop("start") ? date("Y", $to->prop("start")) : t("M&auml;&auml;ramata"),
				"crm_person_education_2.end" => $to->prop("end") ? date("Y", $to->prop("end")) : t("M&auml;&auml;ramata"),
				"crm_person_education_2.end_date" => get_lc_date($to->prop("end_date"), LC_DATE_FORMAT_SHORT_FULLYEAR),
				"crm_person_education_2.diploma_nr" => $to->prop("diploma_nr"),
			));
			foreach($props as $prop)
			{
				if(array_key_exists($prop, $proplist_education) || count($proplist_education) == 0)
				//if(true)
				{
					$this->vars(array(
						"CRM_PERSON_EDUCATION_2.".strtoupper($prop) => $this->parse("CRM_PERSON_EDUCATION_2.".strtoupper($prop)),
					));
				}
			}
			if((array_key_exists("in_progress", $proplist_education) || count($proplist_education) == 0) && $anything_in_progress)
			//if($anything_in_progress)
			{
				$this->vars(array(
					"CRM_PERSON_EDUCATION_2.IN_PROGRESS" => $this->parse("CRM_PERSON_EDUCATION_2.IN_PROGRESS"),
				));
			}
			if(array_key_exists("school1", $proplist_education) || array_key_exists("school2", $proplist_education) || count($proplist_education) == 0)
			//if(true)
			{
				$this->vars(array(
					"CRM_PERSON_EDUCATION_2.SCHOOL" => $this->parse("CRM_PERSON_EDUCATION_2.SCHOOL"),
				));
			}

			$CRM_PERSON_EDUCATION_2 .= $this->parse("CRM_PERSON_EDUCATION_2");

			$parse_cpe_2++;
			//	END SUB: CRM_PERSON_EDUCATION_2
		}

		if($parse_cpe_2 > 0)
		{
			$this->vars(array(
				"CRM_PERSON_EDUCATIONS_2.HEADER" => $this->parse("CRM_PERSON_EDUCATIONS_2.HEADER"),
				"CRM_PERSON_EDUCATION_2" => $CRM_PERSON_EDUCATION_2,
			));
			$this->vars(array(
				"CRM_PERSON_EDUCATIONS_2" => $this->parse("CRM_PERSON_EDUCATIONS_2"),
			));
		}
		// END SUB: CRM_PERSON_EDUCATIONS_2

		// SUB: CRM_PERSON_ADD_EDUCATIONS
		$parse_cpae = 0;
		$cff_ae = $cff_inst->get_sysdefault(array("clid" => CL_CRM_PERSON_ADD_EDUCATION));
		if($cff_ae)
		{
			$proplist_add_education = $cff_inst->get_cfg_proplist($cff_ae);
		}
		else
		{
			$proplist_add_education = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_add_education = array();

		$props = array("org", "field", "time", "time_text", "length", "length_hrs");
		foreach($props as $prop)
		{
			if(array_key_exists($prop, $proplist_add_education) || count($proplist_add_education) == 0)
			//if(true)
			{
				$this->vars(array(
					"CRM_PERSON_ADD_EDUCATIONS.HEADER.".strtoupper($prop) => $this->parse("CRM_PERSON_ADD_EDUCATIONS.HEADER.".strtoupper($prop)),
				));
			}
		}

		$conns = $o->connections_from(array(
			"type" => "RELTYPE_ADD_EDUCATION",
		));
		$CRM_PERSON_ADD_EDUCATION = "";
		foreach($conns as $conn)
		{
			//	SUB: CRM_PERSON_ADD_EDUCATION
			$to = $conn->to();
			$this->vars(array(
				"crm_person_add_education.org" => $to->prop("org"),
				"crm_person_add_education.field" => $to->prop("field"),
				"crm_person_add_education.time" => get_lc_date($to->prop("time"), LC_DATE_FORMAT_SHORT_FULLYEAR),
				"crm_person_add_education.time_text" => $to->prop("time_text"),
				"crm_person_add_education.length" => $to->prop("length"),
				"crm_person_add_education.length_hrs" => $to->prop("length_hrs"),
			));
			foreach($props as $prop)
			{
				if(array_key_exists($prop, $proplist_add_education) || count($proplist_add_education) == 0)
				//if(true)
				{
					$this->vars(array(
						"CRM_PERSON_ADD_EDUCATION.".strtoupper($prop) => $this->parse("CRM_PERSON_ADD_EDUCATION.".strtoupper($prop)),
					));
				}
			}

			$CRM_PERSON_ADD_EDUCATION .= $this->parse("CRM_PERSON_ADD_EDUCATION");

			$parse_cpae++;
			//	END SUB: CRM_PERSON_ADD_EDUCATION
		}

		if($parse_cpae > 0)
		{
			$this->vars(array(
				"CRM_PERSON_ADD_EDUCATIONS.HEADER" => $this->parse("CRM_PERSON_ADD_EDUCATIONS.HEADER"),
				"CRM_PERSON_ADD_EDUCATION" => $CRM_PERSON_ADD_EDUCATION,
			));
			$this->vars(array(
				"CRM_PERSON_ADD_EDUCATIONS" => $this->parse("CRM_PERSON_ADD_EDUCATIONS"),
			));
		}
		// END SUB: CRM_PERSON_ADD_EDUCATIONS

		// SUB: CRM_PERSON_LANGUAGES
		$parse_cpl = 0;
		$cff_l = $cff_inst->get_sysdefault(array("clid" => CL_CRM_PERSON_LANGUAGE));
		if($cff_l)
		{
			$proplist_crm_person_language = $cff_inst->get_cfg_proplist($cff_l);
		}
		else
		{
			$proplist_crm_person_language = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_crm_person_language = array();

		$props = array("talk", "understand", "write");

		$options = $pers_lang_inst->lang_lvl_options;
		$CRM_PERSON_LANGUAGE = "";
		foreach($o->connections_from(array("type" => "RELTYPE_LANGUAGE_SKILL")) as $conn)
		{
			$to = $conn->to();
			if(is_oid($to->prop("language")))
			{
				$this->vars(array(
					"crm_person_language.language" => $to->prop("language.name"),
					"crm_person_language.talk" => $options[$to->prop("talk")],
					"crm_person_language.understand" => $options[$to->prop("understand")],
					"crm_person_language.write" => $options[$to->prop("write")],
				));
				foreach($props as $prop)
				{
					if(array_key_exists($prop, $proplist_crm_person_language) || count($proplist_crm_person_language) == 0)
					//if(true)
					{
						$this->vars(array(
							"CRM_PERSON_LANGUAGE.".strtoupper($prop) => $this->parse("CRM_PERSON_LANGUAGE.".strtoupper($prop)),
						));
					}
				}
				$CRM_PERSON_LANGUAGE .= $this->parse("CRM_PERSON_LANGUAGE");
			}
			else
			if(strlen($to->other) > 0)
			{
				$this->vars(array(
					"crm_person_language.language" => $to->other,
					"crm_person_language.talk" => $options[$to->prop("talk")],
					"crm_person_language.understand" => $options[$to->prop("understand")],
					"crm_person_language.write" => $options[$to->prop("write")],
				));
				foreach($props as $prop)
				{
					if(array_key_exists($prop, $proplist_crm_person_language) || count($proplist_crm_person_language) == 0)
					//if(true)
					{
						$this->vars(array(
							"CRM_PERSON_LANGUAGE.".strtoupper($prop) => $this->parse("CRM_PERSON_LANGUAGE.".strtoupper($prop)),
						));
					}
				}
				$CRM_PERSON_LANGUAGE .= $this->parse("CRM_PERSON_LANGUAGE");
			}
			$parse_cpl++;
		}

		if($parse_cpl > 0)
		{
			$this->vars(array(
				"CRM_PERSON_LANGUAGE" => $CRM_PERSON_LANGUAGE,
			));
			$this->vars(array(
				"CRM_PERSON_LANGUAGES" => $this->parse("CRM_PERSON_LANGUAGES"),
			));
		}
		// END SUB: CRM_PERSON_LANGUAGES

		if(is_oid($pm_inst->get_sysdefault()))
		{
			$pm_obj = obj($pm_inst->get_sysdefault());
			$sm_id = $pm_obj->prop("skill_manager");
			$CRM_SKILL = "";

			$skills = $sm_inst->get_skills(array("id" => $sm_id));
			$parent_skill_ids = array_keys($skills[$sm_id]);
			$ol = new object_list(array(
				"class_id" => CL_CRM_SKILL,
				"sort_by" => "objects.jrk",
				"oid" => $parent_skill_ids,
				"lang_id" => array(),
			));
			$parent_skills = $ol->ids();

			$conns = $o->connections_from(array(
				"type" => array("RELTYPE_SKILL_LEVEL", "RELTYPE_SKILL_LEVEL2", "RELTYPE_SKILL_LEVEL3", "RELTYPE_SKILL_LEVEL4", "RELTYPE_SKILL_LEVEL5")
			));

			foreach($skills[$sm_id] as $id => $data)
			{
				// SUB: CRM_SKILL
				$parse_sk = 0;
				$ids = array();
				$this->recursive_ids($id, $skills, &$ids);
				$skills_by_parent = array();
				foreach($conns as $conn)
				{
					$to = $conn->to();
					if(in_array($to->prop("skill"), $ids))
					{
						if($to->parent != $id)
						{
							$skills_by_parent[$to->prop("skill.parent")][$to->id()]["skill.name"] = $to->prop("skill.name");
							$skills_by_parent[$to->prop("skill.parent")][$to->id()]["level.name"] = $to->prop("level.name");
						}
						/*
						$this->vars(array(
							"crm_skill_level.skill" => $to->prop("skill.name"),
							"crm_skill_level.level" => $to->prop("level.name"),
						));
						$CRM_SKILL_LEVEL .= $this->parse("CRM_SKILL_LEVEL");
						*/
					}
					else
					if(strlen($to->other) > 0)
					{
						$jrk = $conn->prop("reltype") - 81;
						if($parent_skills[$jrk] == $id)
						{
							$skills_by_parent[$to->prop("skill.parent")][$to->id()]["skill.name"] = $to->prop("other");
							$skills_by_parent[$to->prop("skill.parent")][$to->id()]["level.name"] = $to->prop("level.name");
						}
					}
				}
				$CRM_SKILL_LEVEL_GROUP = "";
				foreach($skills_by_parent as $parent => $skills_of_parent)
				{
					$CRM_SKILL_LEVEL = "";
					foreach($skills_of_parent as $skill_id => $skill_data)
					{
						$this->vars(array(
							"crm_skill_level.skill" => $skill_data["skill.name"],
							"crm_skill_level.level" => $skill_data["level.name"],
						));
						$CRM_SKILL_LEVEL .= $this->parse("CRM_SKILL_LEVEL");
						$parse_sk++;
					}
					if($parent != $id)
					{
						$this->vars(array(
							"crm_skill.parent" => obj($parent)->name(),
						));
						$this->vars(array(
							"CRM_SKILL_LEVEL_SUBHEADING" => $this->parse("CRM_SKILL_LEVEL_SUBHEADING"),
						));
					}
					$this->vars(array(
						"CRM_SKILL_LEVEL" => $CRM_SKILL_LEVEL,
					));
					$CRM_SKILL_LEVEL_GROUP .= $this->parse("CRM_SKILL_LEVEL_GROUP");
				}

				if($parse_sk > 0)
				{
					$this->vars(array(
						//"CRM_SKILL_LEVEL" => $CRM_SKILL_LEVEL,
						"CRM_SKILL_LEVEL_GROUP" => $CRM_SKILL_LEVEL_GROUP,
						"crm_skill" => $data["name"],
					));
					$CRM_SKILL .= $this->parse("CRM_SKILL");
				}
				// END SUB: CRM_SKILL
			}
			$this->vars(array(
				"CRM_SKILL" => $CRM_SKILL,
			));
		}

		// SUB: CRM_PERSON.DRIVERS_LICENSE
		if(strlen($o->prop("drivers_license")) > 0 && (array_key_exists("drivers_license", $proplist) || count($proplist) == 0))
		{
			$options = $this->drivers_licence_categories();
			$dl = explode("," ,trim($o->prop("drivers_license"), ","));
			foreach($dl as $s)
			{
				if(strlen($v) > 0)
				{
					$v .= ", ";
				}
				$v .= $options[$s];
			}
			$this->vars(array(
				"crm_person.drivers_license" => $v,
				"crm_person.dl_can_use" => ($o->prop("dl_can_use") == 1) ? t("Jah") : t("Ei"),
			));
			if(array_key_exists("dl_can_use", $proplist) || count($proplist) == 0)
			//if(true)
			{
				$this->vars(array(
					"CRM_PERSON.DL_CAN_USE" => $this->parse("CRM_PERSON.DL_CAN_USE"),
				));
			}
			$this->vars(array(
				"CRM_PERSON.DRIVERS_LICENSE" => $this->parse("CRM_PERSON.DRIVERS_LICENSE"),
			));
		}
		// END SUB: CRM_PERSON.DRIVERS_LICENSE

		// SUB: CRM_COMPANY_RELATIONS
		$parse_ccr = 0;
		$cff_cr = $cff_inst->get_sysdefault(array("clid" => CL_CRM_COMPANY_RELATION));
		if($cff_cr)
		{
			$proplist_company_relation = $cff_inst->get_cfg_proplist($cff_cr);
		}
		else
		{
			$proplist_company_relation = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_company_relation = array();

		$props = array("org", "start", "end", "add_info");
		foreach($props as $prop)
		{
			if(array_key_exists($prop, $proplist_company_relation) || count($proplist_company_relation) == 0)
			//if(true)
			{
				$this->vars(array(
					"CRM_COMPANY_RELATIONS.HEADER.".strtoupper($prop) => $this->parse("CRM_COMPANY_RELATIONS.HEADER.".strtoupper($prop)),
				));
			}
		}

		$conns = $o->connections_from(array(
			"type" => "RELTYPE_COMPANY_RELATION",
		));
		$CRM_COMPANY_RELATION = "";
		foreach($conns as $conn)
		{
			//	SUB: CRM_COMPANY_RELATION
			$to = $conn->to();
			if(strlen($to->prop("start")) == 10 && $to->prop("start") != "0000-00-00")
			{
				$s = explode("-", $to->prop("start"));
				// $start = get_lc_date(mktime(0, 0, 0, $s[1], $s[2], $s[0]), LC_DATE_FORMAT_SHORT_FULLYEAR);
				$start = date("Y", mktime(0, 0, 0, $s[1], $s[2], $s[0]));
			}
			else
			{
				$start = t("M&auml;&auml;ramata");
			}

			if(strlen($to->prop("end")) == 10 && $to->prop("end") != "0000-00-00")
			{
				$e = explode("-", $to->prop("end"));
				// $end = get_lc_date(mktime(0, 0, 0, $e[1], $e[2], $e[0]), LC_DATE_FORMAT_SHORT_FULLYEAR);
				$end = date("Y", mktime(0, 0, 0, $e[1], $e[2], $e[0]));
			}
			else
			{
				$end = t("M&auml;&auml;ramata");
			}

			$this->vars(array(
				"crm_company_relation.org" => $to->prop("org.name"),
				"crm_company_relation.start" => $start,
				"crm_company_relation.end" => $end,
				"crm_company_relation.add_info" => nl2br($to->prop("add_info")),
			));
			foreach($props as $prop)
			{
				if(array_key_exists($prop, $proplist_company_relation) || count($proplist_company_relation) == 0)
				//if(true)
				{
					$this->vars(array(
						"CRM_COMPANY_RELATION.".strtoupper($prop) => $this->parse("CRM_COMPANY_RELATION.".strtoupper($prop)),
					));
				}
			}

			$CRM_COMPANY_RELATION .= $this->parse("CRM_COMPANY_RELATION");

			$parse_ccr++;
			//	END SUB: CRM_COMPANY_RELATION
		}

		if($parse_ccr > 0)
		{
			$this->vars(array(
				"CRM_COMPANY_RELATION" => $CRM_COMPANY_RELATION,
				"CRM_COMPANY_RELATIONS.HEADER" => $this->parse("CRM_COMPANY_RELATIONS.HEADER"),
			));
			$this->vars(array(
				"CRM_COMPANY_RELATIONS" => $this->parse("CRM_COMPANY_RELATIONS"),
			));
		}
		// END SUB: CRM_COMPANY_RELATIONS

		// SUB: CRM_PERSON.CONTACT
		$parse_cpc = 0;

		//		SUB: CRM_PERSON.PHONES
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_PHONE",
		));
		$cns2wrs = $o->connections_from(array(
			"type" => "RELTYPE_CURRENT_JOB",
		));
		foreach($cns2wrs as $cn2wr)
		{
			$wr = $cn2wr->to();
			foreach($wr->connections_from(array("type" => "RELTYPE_PHONE")) as $cn2ph)
			{
				$conns[$cn2ph->id()] = $cn2ph;
			}
		}
		$CRM_PERSON_PHONE = "";
		if(count($conns) > 0)
		{
			$ph_types = $phone_inst->phone_types;
			foreach($conns as $conn)
			{
			//		SUB: CRM_PERSON.PHONE
				$ph = $conn->to();
				$this->vars(array(
					"crm_person.phone.name" => $ph->name(),
					"crm_person.phone.type" => $ph_types[$ph->prop("type")],
				));
				$CRM_PERSON_PHONE .= $this->parse("CRM_PERSON.PHONE");
			//		END SUB: CRM_PERSON.PHONE
			}
			$this->vars(array(
				"CRM_PERSON.PHONE" => $CRM_PERSON_PHONE,
			));
			$this->vars(array(
				"CRM_PERSON.PHONES" => $this->parse("CRM_PERSON.PHONES"),
			));
			$parse_cpc++;
		}
		//		END SUB: CRM_PERSON.PHONES

		//		SUB: CRM_PERSON.FAXES
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_FAX",
		));
		$cns2wrs = $o->connections_from(array(
			"type" => "RELTYPE_CURRENT_JOB",
		));
		foreach($cns2wrs as $cn2wr)
		{
			$wr = $cn2wr->to();
			foreach($wr->connections_from(array("type" => "RELTYPE_FAX")) as $cn2ph)
			{
				$conns[$cn2ph->id()] = $cn2ph;
			}
		}
		$CRM_PERSON_FAX = "";
		if(count($conns) > 0)
		{
			foreach($conns as $conn)
			{
			//		SUB: CRM_PERSON.FAX
				$ph = $conn->to();
				$this->vars(array(
					"crm_person.fax" => $ph->name(),
				));
				$CRM_PERSON_FAX .= $this->parse("CRM_PERSON.FAX");
			//		END SUB: CRM_PERSON.FAX
			}
			$this->vars(array(
				"CRM_PERSON.FAX" => $CRM_PERSON_FAX,
			));
			$this->vars(array(
				"CRM_PERSON.FAXES" => $this->parse("CRM_PERSON.FAXES"),
			));
			$parse_cpc++;
		}
		//		END SUB: CRM_PERSON.FAXES

		//		SUB: CRM_PERSON.EMAILS
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_EMAIL",
		));
		$cns2wrs = $o->connections_from(array(
			"type" => "RELTYPE_CURRENT_JOB",
		));
		foreach($cns2wrs as $cn2wr)
		{
			$wr = $cn2wr->to();
			foreach($wr->connections_from(array("type" => "RELTYPE_EMAIL")) as $cn2ph)
			{
				$conns[$cn2ph->id()] = $cn2ph;
			}
		}
		$CRM_PERSON_EMAIL = "";
		if(count($conns) > 0)
		{
			foreach($conns as $conn)
			{
			//		SUB: CRM_PERSON.EMAIL
				$ph = $conn->to();
				$this->vars(array(
					"crm_person.email" => $ph->prop("mail"),
				));
				$CRM_PERSON_EMAIL .= $this->parse("CRM_PERSON.EMAIL");
			//		END SUB: CRM_PERSON.EMAIL
			}
			$this->vars(array(
				"CRM_PERSON.EMAIL" => $CRM_PERSON_EMAIL,
			));
			$this->vars(array(
				"CRM_PERSON.EMAILS" => $this->parse("CRM_PERSON.EMAILS"),
			));
			$parse_cpc++;
		}
		//		END SUB: CRM_PERSON.EMAILS

		//		SUB: CRM_PERSON.ADDRESSES
		$conns = $o->connections_from(array(
			"type" => "RELTYPE_ADDRESS",
		));
		$CRM_PERSON_EMAIL = "";
		if(count($conns) > 0)
		{
			foreach($conns as $conn)
			{
			//		SUB: CRM_PERSON.ADDRESS
				$addr = $conn->to();
				$this->vars(array(
					"crm_person.address.aadress" => $addr->prop("aadress"),
					"crm_person.address.linn" => $addr->prop("linn.name"),
					"crm_person.address.postiindeks" => $addr->prop("postiindeks"),
					"crm_person.address.maakond" => $addr->prop("maakond.name"),
					"crm_person.address.piirkond" => $addr->prop("piirkond.name"),
					"crm_person.address.riik" => $addr->prop("riik.name"),
				));
				$CRM_PERSON_ADDRESS .= $this->parse("CRM_PERSON.ADDRESS");
			//		END SUB: CRM_PERSON.ADDRESS
			}
			$this->vars(array(
				"CRM_PERSON.ADDRESS" => $CRM_PERSON_ADDRESS,
			));
			$this->vars(array(
				"CRM_PERSON.ADDRESSES" => $this->parse("CRM_PERSON.ADDRESSES"),
			));
			$parse_cpc++;
		}
		//		END SUB: CRM_PERSON.ADDRESSES

		if($parse_cpc > 0)
		{
			$this->vars(array(
				"CRM_PERSON.CONTACT" => $this->parse("CRM_PERSON.CONTACT"),
			));
		}
		// END SUB: CRM_PERSON.CONTACT

		// SUB: CRM_PERSON_WORK_RELATIONS
		$parse_cpwr = 0;
		$CRM_PERSON_WORK_RELATION = "";

		$cff_wr = $cff_inst->get_sysdefault(array("clid" => CL_CRM_PERSON_WORK_RELATION));
		if($cff_wr)
		{
			$proplist_work_relation = $cff_inst->get_cfg_proplist($cff_wr);
		}
		else
		{
			$proplist_work_relation = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_work_relation = array();

		$props = array("org", "section", "profession", "start", "end", "tasks", "load", "salary", "benefits", "field");
		foreach($props as $prop)
		{
			if(array_key_exists($prop, $proplist_work_relation) || count($proplist_work_relation) == 0)
			//if(true)
			{
				$this->vars(array(
					"CRM_PERSON_WORK_RELATIONS.HEADER.".strtoupper($prop) => $this->parse("CRM_PERSON_WORK_RELATIONS.HEADER.".strtoupper($prop)),
				));
			}
		}

		$conns = $o->connections_from(array("type" => array("RELTYPE_CURRENT_JOB", "RELTYPE_PREVIOUS_JOB")));

		foreach($conns as $conn)
		{
			$to = $conn->to();
			$s = $to->prop("start");
			$e = $to->prop("end");
			// $start = !empty($s) ? get_lc_date($s, LC_DATE_FORMAT_SHORT_FULLYEAR) : t("M&auml;&auml;ramata");
			// $end = !empty($e) ? get_lc_date($e, LC_DATE_FORMAT_SHORT_FULLYEAR) : t("M&auml;&auml;ramata");
			$start = !empty($s) ? date("Y", $s) : t("M&auml;&auml;ramata");
			$end = !empty($e) ? date("Y", $e) : t("M&auml;&auml;ramata");
			$this->vars(array(
				"crm_person_work_relation.org" => $to->prop("org.name"),
				"crm_person_work_relation.section" => $to->prop("section.name"),
				"crm_person_work_relation.profession" => $to->prop("profession.name"),
				"crm_person_work_relation.start" => $start,
				"crm_person_work_relation.end" => $end,
				"crm_person_work_relation.tasks" => nl2br($to->prop("tasks")),
				"crm_person_work_relation.load" => is_oid($to->prop("load")) ? $to->prop("load.name") : t("M&auml;&auml;ramata"),
				"crm_person_work_relation.salary" => is_numeric($to->prop("salary")) ? $to->prop("salary") : t("M&auml;&auml;ramata"),
				"crm_person_work_relation.benefits" => $to->prop("benefits"),
				"crm_person_work_relation.field" => $to->prop("field.name"),
			));
			foreach($props as $prop)
			{
				if(array_key_exists($prop, $proplist_work_relation) || count($proplist_work_relation) == 0)
				//if(true)
				{
					$this->vars(array(
						"CRM_PERSON_WORK_RELATION.".strtoupper($prop) => $this->parse("CRM_PERSON_WORK_RELATION.".strtoupper($prop)),
					));
				}
			}
			$CRM_PERSON_WORK_RELATION .= $this->parse("CRM_PERSON_WORK_RELATION");
			$parse_cpwr++;
		}

		if($parse_cpwr > 0)
		{
			$this->vars(array(
				"CRM_PERSON_WORK_RELATIONS.HEADER" => $this->parse("CRM_PERSON_WORK_RELATIONS.HEADER"),
				"CRM_PERSON_WORK_RELATION" => $CRM_PERSON_WORK_RELATION,
			));
			$this->vars(array(
				"CRM_PERSON_WORK_RELATIONS" => $this->parse("CRM_PERSON_WORK_RELATIONS"),
			));
		}
		// END SUB: CRM_PERSON_WORK_RELATIONS

		// SUB: PERSONNEL_MANAGEMENT_JOBS_WANTED and PERSONNEL_MANAGEMENT_JOBS_WANTED_VERTICAL
		$parse_pmjw = 0;$cff_jo = $cff_inst->get_sysdefault(array("clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER));

		$cff_jw = $cff_inst->get_sysdefault(array("clid" => CL_PERSONNEL_MANAGEMENT_JOB_WANTED));
		if($cff_jw)
		{
			$proplist_job_wanted = $cff_inst->get_cfg_proplist($cff_jw);
		}
		else
		{
			$proplist_job_wanted = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_job_wanted = array();

		if(array_key_exists("field", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.FIELD" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.FIELD"),
			));
		}
		if(array_key_exists("job_type", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.JOB_TYPE" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.JOB_TYPE"),
			));
		}
		if(array_key_exists("professions", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.PROFESSIONS" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.PROFESSIONS"),
			));
		}
		if(array_key_exists("professions_rels", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.PROFESSIONS_RELS" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.PROFESSIONS_RELS"),
			));
		}
		if(array_key_exists("load", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.LOAD" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.LOAD"),
			));
		}
		if(array_key_exists("pay", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.PAY" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.PAY"),
			));
		}
		if(array_key_exists("location", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.LOCATION" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.LOCATION"),
			));
		}
		if(array_key_exists("location_2", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.LOCATION_2" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.LOCATION_2"),
			));
		}
		if(array_key_exists("location_text", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.LOCATION_TEXT" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.LOCATION_TEXT"),
			));
		}
		if(array_key_exists("addinfo", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.ADDINFO" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.ADDINFO"),
			));
		}
		if(array_key_exists("work_at_night", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.WORK_AT_NIGHT" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.WORK_AT_NIGHT"),
			));
		}
		if(array_key_exists("work_by_schedule", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.WORK_BY_SCHEDULE" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.WORK_BY_SCHEDULE"),
			));
		}
		if(array_key_exists("start_working", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.START_WORKING" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.START_WORKING"),
			));
		}
		if(array_key_exists("ready_for_errand", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.READY_FOR_ERRAND" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.READY_FOR_ERRAND"),
			));
		}
		if(array_key_exists("additional_skills", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.ADDITIONAL_SKILLS" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.ADDITIONAL_SKILLS"),
			));
		}
		if(array_key_exists("handicaps", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.HANDICAPS" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.HANDICAPS"),
			));
		}
		if(array_key_exists("hobbies_vs_work", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.HOBBIES_VS_WORK" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER.HOBBIES_VS_WORK"),
			));
		}

		$PERSONNEL_MANAGEMENT_JOB_WANTED = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_FIELD = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_JOB_TYPE = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_PROFESSIONS = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_PROFESSIONS_RELS = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_LOAD = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_PAY = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_LOCATION = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_LOCATION_2 = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_LOCATION_TEXT = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_ADDINFO = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_WORK_AT_NIGHT = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_WORK_BY_SCHEDULE = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_START_WORKING = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_READY_FOR_ERRAND = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_ADDITIONAL_SKILLS = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_HANDICAPS = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_HOBBIES_VS_WORK = "";
		$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_HEADER_N = "";

		$conns = $o->connections_from(array("type" => "RELTYPE_WORK_WANTED"));
		$oids = array();
		foreach($conns as $conn)
		{
			$oids[] = $conn->prop("to");
		}
		if(count($oids) > 0)
		{
			$job_wanted_ol = new object_list(array(
				"class_id" => CL_PERSONNEL_MANAGEMENT_JOB_WANTED,
				"oid" => $oids,
				"parent" => array(),
				"status" => array(),
				"site_id" => array(),
				"lang_id" => array(),
			));
		}
		else
		{
			$job_wanted_ol = new object_list();
		}

		$n = 0;
		//foreach($conns as $conn)
		foreach($job_wanted_ol->arr() as $to)
		{
			$n++;
			//$to = $conn->to();

			$field = "";
			if(count($to->prop("field")) > 0)
			{
				$f_ol = new object_list(array(
					"class_id" => CL_META,
					"oid" => $to->prop("field"),
					"lang_id" => array(),
					"parent" => array(),
					"site_id" => array(),
				));
				$f_nms = $f_ol->names();
				foreach($to->prop("field") as $fid)
				{
					if(!is_oid($fid))
						continue;

					if(strlen($field) > 0)
						$field .= ", ";
					$field .= $f_nms[$fid];
				}
			}
			$job_type = "";
			if(count($to->prop("job_type")) > 0)
			{
				$jt_ol = new object_list(array(
					"class_id" => CL_META,
					"oid" => $to->prop("job_type"),
					"lang_id" => array(),
					"parent" => array(),
					"site_id" => array(),
				));
				$jt_nms = $jt_ol->names();
				foreach($to->prop("job_type") as $jtid)
				{
					if(!is_oid($jtid))
						continue;

					if(strlen($job_type) > 0)
						$job_type .= ", ";
					$job_type .= $jt_nms[$jtid];
				}
			}
			$location = "";
			$location_2 = "";
			if(count($to->prop("location")) > 0 || count($to->prop("location_2")) > 0)
			{
				$oid = (is_array($to->prop("location")) ? $to->prop("location") : array()) + (is_array($to->prop("location_2")) ? $to->prop("location_2") : array());
				$l_ol = new object_list(array(
					"class_id" => array(CL_CRM_CITY, CL_CRM_COUNTY, CL_CRM_COUNTRY, CL_CRM_AREA),
					"oid" => $oid,
					"lang_id" => array(),
					"parent" => array(),
					"site_id" => array(),
				));
				$l_nms = $l_ol->names();
				foreach($to->prop("location") as $lid)
				{
					if(!is_oid($lid))
						continue;

					if(strlen($location) > 0)
						$location .= ", ";
					$location .= $l_nms[$lid];
				}
				foreach($to->prop("location_2") as $lid)
				{
					if(!is_oid($lid))
						continue;
					if(strlen($location_2) > 0)
						$location_2 .= ", ";
					$location_2 .= $l_nms[$lid];
				}
			}

			$professions_rels = "";
			foreach($to->connections_from(array("type" => "RELTYPE_PROFESSION")) as $conn2)
			{
				if(strlen($professions_rels) > 0)
					$professions_rels .= ", ";
				$professions_rels .= $conn2->prop("to.name");
			}
			$sw_options = $jw_inst->start_working_options;

			$this->vars(array(
				"personnel_management_job_wanted.vertical.header.n" => $n,
				"personnel_management_job_wanted.field" => $field,
				"personnel_management_job_wanted.job_type" => $job_type,
				"personnel_management_job_wanted.professions" => nl2br($to->prop("professions")),
				"personnel_management_job_wanted.professions_rels" => $professions_rels,
				"personnel_management_job_wanted.load" => is_oid($to->prop("load")) ? $to->prop("load.name") : t("M&auml;&auml;ramata"),
				"personnel_management_job_wanted.pay" => $to->prop("pay"),
				"personnel_management_job_wanted.location" => $location,
				"personnel_management_job_wanted.location_2" => $location_2,
				"personnel_management_job_wanted.location_text" => $to->prop("location_text"),
				"personnel_management_job_wanted.addinfo" => nl2br($to->prop("addinfo")),
				"personnel_management_job_wanted.work_at_night" => $to->prop("work_at_night") ? t("Jah") : t("Ei"),
				"personnel_management_job_wanted.work_by_schedule" => $to->prop("work_by_schedule") ? t("Jah") : t("Ei"),
				"personnel_management_job_wanted.start_working" => ($to->prop("start_working") > 0) ? $sw_options[$to->prop("start_working")] : t("M&auml;&auml;ramata"),
				"personnel_management_job_wanted.ready_for_errand" => $to->prop("ready_for_errand") ? t("Jah") : t("Ei"),
				"personnel_management_job_wanted.additional_skills" => nl2br($to->prop("additional_skills")),
				"personnel_management_job_wanted.handicaps" => nl2br($to->prop("handicaps")),
				"personnel_management_job_wanted.hobbies_vs_work" => nl2br($to->prop("hobbies_vs_work")),
			));
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_HEADER_N .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.VERTICAL.HEADER.N");
			if(array_key_exists("field", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.FIELD" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.FIELD"),
				));
			}
			if(array_key_exists("job_type", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.JOB_TYPE" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.JOB_TYPE"),
				));
			}
			if(array_key_exists("professions", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.PROFESSIONS" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.PROFESSIONS"),
				));
			}
			if(array_key_exists("professions_rels", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.PROFESSIONS_RELS" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.PROFESSIONS_RELS"),
				));
			}
			if(array_key_exists("load", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.LOAD" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.LOAD"),
				));
			}
			if(array_key_exists("pay", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.PAY" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.PAY"),
				));
			}
			if(array_key_exists("location", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION"),
				));
			}
			if(array_key_exists("location_2", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION_2" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION_2"),
				));
			}
			if(array_key_exists("location_text", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION_TEXT" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION_TEXT"),
				));
			}
			if(array_key_exists("addinfo", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.ADDINFO" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.ADDINFO"),
				));
			}
			if(array_key_exists("work_at_night", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.WORK_AT_NIGHT" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.WORK_AT_NIGHT"),
				));
			}
			if(array_key_exists("work_by_schedule", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.WORK_BY_SCHEDULE" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.WORK_BY_SCHEDULE"),
				));
			}
			if(array_key_exists("start_working", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.START_WORKING" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.START_WORKING"),
				));
			}
			if(array_key_exists("ready_for_errand", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.READY_FOR_ERRAND" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.READY_FOR_ERRAND"),
				));
			}
			if(array_key_exists("additional_skills", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.ADDITIONAL_SKILLS" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.ADDITIONAL_SKILLS"),
				));
			}
			if(array_key_exists("handicaps", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.HANDICAPS" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.HANDICAPS"),
				));
			}
			if(array_key_exists("hobbies_vs_work", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_WANTED.HOBBIES_VS_WORK" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.HOBBIES_VS_WORK"),
				));
			}
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_FIELD .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.FIELD");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_JOB_TYPE .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.JOB_TYPE");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_PROFESSIONS .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.PROFESSIONS");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_PROFESSIONS_RELS .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.PROFESSIONS_RELS");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_LOAD .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.LOAD");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_PAY .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.PAY");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_LOCATION .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.LOCATION");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_LOCATION_2 .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.LOCATION_2");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_LOCATION_TEXT .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.LOCATION_TEXT");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_ADDINFO .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.ADDINFO");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_WORK_AT_NIGHT .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.WORK_AT_NIGHT");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_WORK_BY_SCHEDULE .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.WORK_BY_SCHEDULE");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_START_WORKING .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.START_WORKING");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_READY_FOR_ERRAND .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.READY_FOR_ERRAND");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_ADDITIONAL_SKILLS .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.ADDITIONAL_SKILLS");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_HANDICAPS .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.HANDICAPS");
			$PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_HOBBIES_VS_WORK .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.HOBBIES_VS_WORK");

			$PERSONNEL_MANAGEMENT_JOB_WANTED .= $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED");
			$parse_pmjw++;
		}
		$this->vars(array(
			"PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED.HEADER"),
			"PERSONNEL_MANAGEMENT_JOB_WANTED" => $PERSONNEL_MANAGEMENT_JOB_WANTED,
			// PERSONNEL_MANAGEMENT_JOBS_WANTED_VERTICAL
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.FIELD" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_FIELD,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.JOB_TYPE" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_JOB_TYPE,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.PROFESSIONS" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_PROFESSIONS,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.PROFESSIONS_RELS" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_PROFESSIONS_RELS,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.LOAD" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_LOAD,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.PAY" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_PAY,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.LOCATION" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_LOCATION,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.LOCATION_2" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_LOCATION_2,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.LOCATION_TEXT" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_LOCATION_TEXT,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.ADDINFO" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_ADDINFO,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.WORK_AT_NIGHT" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_WORK_AT_NIGHT,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.WORK_BY_SCHEDULE" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_WORK_BY_SCHEDULE,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.START_WORKING" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_START_WORKING,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.READY_FOR_ERRAND" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_READY_FOR_ERRAND,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.ADDITIONAL_SKILLS" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_ADDITIONAL_SKILLS,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.HANDICAPS" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_HANDICAPS,
			"PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL.HOBBIES_VS_WORK" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_HOBBIES_VS_WORK,
		));

		if(array_key_exists("field", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.FIELD.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.FIELD.VERTICAL"),
			));
		}
		if(array_key_exists("job_type", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.JOB_TYPE.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.JOB_TYPE.VERTICAL"),
			));
		}
		if(array_key_exists("professions", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.PROFESSIONS.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.PROFESSIONS.VERTICAL"),
			));
		}
		if(array_key_exists("professions_rels", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.PROFESSIONS_RELS.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.PROFESSIONS_RELS.VERTICAL"),
			));
		}
		if(array_key_exists("load", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.LOAD.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.LOAD.VERTICAL"),
			));
		}
		if(array_key_exists("pay", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.PAY.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.PAY.VERTICAL"),
			));
		}
		if(array_key_exists("location", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION.VERTICAL"),
			));
		}
		if(array_key_exists("location_2", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION_2.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION_2.VERTICAL"),
			));
		}
		if(array_key_exists("location_text", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION_TEXT.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.LOCATION_TEXT.VERTICAL"),
			));
		}
		if(array_key_exists("addinfo", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.ADDINFO.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.ADDINFO.VERTICAL"),
			));
		}
		if(array_key_exists("work_at_night", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.WORK_AT_NIGHT.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.WORK_AT_NIGHT.VERTICAL"),
			));
		}
		if(array_key_exists("work_by_schedule", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.WORK_BY_SCHEDULE.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.WORK_BY_SCHEDULE.VERTICAL"),
			));
		}
		if(array_key_exists("start_working", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.START_WORKING.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.START_WORKING.VERTICAL"),
			));
		}
		if(array_key_exists("ready_for_errand", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.READY_FOR_ERRAND.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.READY_FOR_ERRAND.VERTICAL"),
			));
		}
		if(array_key_exists("additional_skills", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.ADDITIONAL_SKILLS.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.ADDITIONAL_SKILLS.VERTICAL"),
			));
		}
		if(array_key_exists("handicaps", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.HANDICAPS.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.HANDICAPS.VERTICAL"),
			));
		}
		if(array_key_exists("hobbies_vs_work", $proplist_job_wanted) || count($proplist_job_wanted) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_WANTED.HOBBIES_VS_WORK.VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.HOBBIES_VS_WORK.VERTICAL"),
			));
		}

		$this->vars(array(
			"PERSONNEL_MANAGEMENT_JOB_WANTED.VERTICAL.HEADER.N" => $PERSONNEL_MANAGEMENT_JOB_WANTED_VERTICAL_HEADER_N,
		));
		$this->vars(array(
			"PERSONNEL_MANAGEMENT_JOB_WANTED.VERTICAL.HEADER" => $this->parse("PERSONNEL_MANAGEMENT_JOB_WANTED.VERTICAL.HEADER"),
		));

		if($parse_pmjw > 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOBS_WANTED" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED"),
				"PERSONNEL_MANAGEMENT_JOBS_WANTED_VERTICAL" => $this->parse("PERSONNEL_MANAGEMENT_JOBS_WANTED_VERTICAL"),
			));
		}
		// END SUB: PERSONNEL_MANAGEMENT_JOBS_WANTED and PERSONNEL_MANAGEMENT_JOBS_WANTED_VERTICAL

		// SUB: PREVIOUS_CANDIDACIES
		$parse_pc = 0;
		$cff_jo = $cff_inst->get_sysdefault(array("clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER));
		if($cff_jo)
		{
			$proplist_job_offer = $cff_inst->get_cfg_proplist($cff_jo);
		}
		else
		{
			$proplist_job_offer = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_job_offer = array();

		$props = array("profession", "field", "end", "addinfo");
		foreach($props as $prop)
		{
			if(array_key_exists($prop, $proplist_job_offer) || count($proplist_job_offer) == 0)
			{
				$this->vars(array(
					"PREVIOUS_CANDIDACIES.HEADER.".strtoupper($prop) => $this->parse("PREVIOUS_CANDIDACIES.HEADER.".strtoupper($prop)),
				));
			}
		}
		$this->vars(array(
			"PREVIOUS_CANDIDACIES.HEADER.RATING" => $this->parse("PREVIOUS_CANDIDACIES.HEADER.RATING"),
		));

		$PREVIOUS_CANDIDACY = "";

		$pm_obj = obj($pm_inst->get_sysdefault());

		$this->vars(array(
			"personnel_management.owner_org" => $pm_obj->prop("owner_org.name"),
		));

		foreach($o->connections_to(array("from.class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE, "type" => "RELTYPE_PERSON")) as $conn)
		{
			$from = $conn->from();
			if(!is_oid($from->prop("job_offer")))
				continue;

			$jo = obj($from->prop("job_offer"));
			if($jo->status() == object::STAT_ACTIVE && $jo->prop("end") >= (mktime(0, 0, 0, date("m"), date("d"), date("Y"))))
				continue;

			$this->vars(array(
				"personnel_management_job_offer.profession" => $jo->prop("profession.name"),
				"personnel_management_job_offer.field" => $jo->prop("field.name"),
				"personnel_management_job_offer.end" => get_lc_date($jo->prop("end"), LC_DATE_FORMAT_SHORT_FULLYEAR),
				"personnel_management_job_offer.rating" => is_oid($jo->prop("rate_scale")) ? $rate_inst->get_rating_for_object($from->id(), RATING_AVERAGE, $jo->prop("rate_scale")) : t("M&auml;&auml;ramata"),
				"personnel_management_job_offer.addinfo" => nl2br($jo->prop("addinfo")),
			));
			if($jo->endless)
			{
				$this->vars(array(
					"personnel_management_job_offer.end" => t("T&auml;htajatu"),
				));
			}
			foreach($props as $prop)
			{
				if(array_key_exists($prop, $proplist_job_offer) || count($proplist_job_offer) == 0)
				{
					$this->vars(array(
						"PREVIOUS_CANDIDACY.".strtoupper($prop) => $this->parse("PREVIOUS_CANDIDACY.".strtoupper($prop)),
					));
				}
			}
			$this->vars(array(
				"PREVIOUS_CANDIDACY.RATING" => $this->parse("PREVIOUS_CANDIDACY.RATING"),
			));
			$PREVIOUS_CANDIDACY .= $this->parse("PREVIOUS_CANDIDACY");
			$parse_pc++;
		}

		if($parse_pc > 0)
		{
			$this->vars(array(
				"PREVIOUS_CANDIDACIES.HEADER" => $this->parse("PREVIOUS_CANDIDACIES.HEADER"),
				"PREVIOUS_CANDIDACY" => $PREVIOUS_CANDIDACY,
			));
			$this->vars(array(
				"PREVIOUS_CANDIDACIES" => $this->parse("PREVIOUS_CANDIDACIES"),
			));
		}
		// END SUB: PREVIOUS_CANDIDACIES

		// SUB: PERSONNEL_MANAGEMENT_CANDIDATES
		$parse_pmc = 0;
		$PERSONNEL_MANAGEMENT_CANDIDATE = "";
		$cff_jo = $cff_inst->get_sysdefault(array("clid" => CL_PERSONNEL_MANAGEMENT_JOB_OFFER));
		if($cff_jo)
		{
			$proplist_job_offer = $cff_inst->get_cfg_proplist($cff_jo);
		}
		else
		{
			$proplist_job_offer = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_job_offer = array();

		if(array_key_exists("company", $proplist_job_offer) || count($proplist_job_offer) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.COMPANY" => $this->parse("PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.COMPANY"),
			));
		}
		if(array_key_exists("profession", $proplist_job_offer) || count($proplist_job_offer) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.PROFESSION" => $this->parse("PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.PROFESSION"),
			));
		}
		if(array_key_exists("field", $proplist_job_offer) || count($proplist_job_offer) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.FIELD" => $this->parse("PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.FIELD"),
			));
		}
		if(array_key_exists("end", $proplist_job_offer) || count($proplist_job_offer) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.END" => $this->parse("PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.END"),
			));
		}
		if(array_key_exists("company", $proplist_job_offer) || count($proplist_job_offer) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.COMPANY" => $this->parse("PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.COMPANY"),
			));
		}
		// Rating will be there no matter what.
		$this->vars(array(
			"PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.COMPANY" => $this->parse("PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.COMPANY"),
		));
		if(array_key_exists("addinfo", $proplist_job_offer) || count($proplist_job_offer) == 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.ADDINFO" => $this->parse("PERSONNEL_MANAGEMENT_CANDIDATES.HEADER.ADDINFO"),
			));
		}
		$this->vars(array(
			"PERSONNEL_MANAGEMENT_CANDIDATES.HEADER" => $this->parse("PERSONNEL_MANAGEMENT_CANDIDATES.HEADER"),
		));
		foreach($o->connections_to(array("from.class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE, "type" => "RELTYPE_PERSON")) as $conn)
		{
			$from = $conn->from();
			if(!is_oid($from->prop("job_offer")))
				continue;

			$jo = obj($from->prop("job_offer"));
			if($jo->status() != object::STAT_ACTIVE || $jo->prop("end") < (mktime(0, 0, 0, date("m"), date("d"), date("Y"))))
				continue;

			$this->vars(array(
				"personnel_management_job_offer.company" => $jo->prop("company.name"),
				"personnel_management_job_offer.profession" => $jo->prop("profession.name"),
				"personnel_management_job_offer.field" => $jo->prop("field.name"),
				"personnel_management_job_offer.end" => get_lc_date($jo->prop("end"), LC_DATE_FORMAT_SHORT_FULLYEAR),
				"personnel_management_job_offer.rating" => is_oid($jo->prop("rate_scale")) ? $rate_inst->get_rating_for_object($from->id(), RATING_AVERAGE, $jo->prop("rate_scale")) : t("M&auml;&auml;ramata"),
				"personnel_management_job_offer.addinfo" => nl2br($jo->prop("addinfo")),
			));
			if($jo->endless)
			{
				$this->vars(array(
					"personnel_management_job_offer.end" => t("T&auml;htajatu"),
				));
			}
			if(array_key_exists("company", $proplist_job_offer) || count($proplist_job_offer) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_OFFER.COMPANY" => $this->parse("PERSONNEL_MANAGEMENT_JOB_OFFER.COMPANY"),
				));
			}
			if(array_key_exists("profession", $proplist_job_offer) || count($proplist_job_offer) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_OFFER.PROFESSION" => $this->parse("PERSONNEL_MANAGEMENT_JOB_OFFER.PROFESSION"),
				));
			}
			if(array_key_exists("field", $proplist_job_offer) || count($proplist_job_offer) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_OFFER.FIELD" => $this->parse("PERSONNEL_MANAGEMENT_JOB_OFFER.FIELD"),
				));
			}
			if(array_key_exists("end", $proplist_job_offer) || count($proplist_job_offer) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_OFFER.END" => $this->parse("PERSONNEL_MANAGEMENT_JOB_OFFER.END"),
				));
			}
			if(array_key_exists("company", $proplist_job_offer) || count($proplist_job_offer) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_OFFER.COMPANY" => $this->parse("PERSONNEL_MANAGEMENT_JOB_OFFER.COMPANY"),
				));
			}
			// Rating will be there no matter what.
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_JOB_OFFER.COMPANY" => $this->parse("PERSONNEL_MANAGEMENT_JOB_OFFER.COMPANY"),
			));
			if(array_key_exists("addinfo", $proplist_job_offer) || count($proplist_job_offer) == 0)
			{
				$this->vars(array(
					"PERSONNEL_MANAGEMENT_JOB_OFFER.ADDINFO" => $this->parse("PERSONNEL_MANAGEMENT_JOB_OFFER.ADDINFO"),
				));
			}
			$PERSONNEL_MANAGEMENT_CANDIDATE .= $this->parse("PERSONNEL_MANAGEMENT_CANDIDATE");
			$parse_pmc++;
		}

		if($parse_pmc > 0)
		{
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_CANDIDATE" => $PERSONNEL_MANAGEMENT_CANDIDATE,
			));
			$this->vars(array(
				"PERSONNEL_MANAGEMENT_CANDIDATES" => $this->parse("PERSONNEL_MANAGEMENT_CANDIDATES"),
			));
		}
		// END SUB: PERSONNEL_MANAGEMENT_CANDIDATES

		// SUB: CRM_FAMILY_RELATIONS
		// We use different subs for different relation types, cause maybe we don't want to show parents or kids or wives etc...
		$parse_cfr = 0;
		$CRM_FAMILY_RELATION = array();

		$conns = $o->connections_from(array("type" => "RELTYPE_FAMILY_RELATIONS"));
		$cff_fr = $cff_inst->get_sysdefault(array("clid" => CL_CRM_FAMILY_RELATION));
		if($cff_fr)
		{
			$proplist_family_relation = $cff_inst->get_cfg_proplist($cff_fr);
		}
		else
		{
			$proplist_family_relation = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_family_relation = array();

		foreach($conns as $conn)
		{
			$to = $conn->to();
			if($to->prop("relation_type") > 0 && is_oid($to->prop("person")))
			{
				if(!isset($CRM_FAMILY_RELATION[$to->prop("relation_type")]))
				{
					$CRM_FAMILY_RELATION[$to->prop("relation_type")] = "";
				}

				$this->vars(array(
					"crm_family_relation.person" => $to->prop("person.name"),
					"crm_family_relation.start" => get_lc_date($to->prop("start"), LC_DATE_FORMAT_SHORT_FULLYEAR),
					"crm_family_relation.end" => get_lc_date($to->prop("end"), LC_DATE_FORMAT_SHORT_FULLYEAR),
				));
				if(array_key_exists("person", $proplist_family_relation) || count($proplist_family_relation) == 0)
				{
					$this->vars(array(
						"CRM_FAMILY_RELATION.PERSON" => $this->parse("CRM_FAMILY_RELATION.PERSON"),
					));
				}
				if(array_key_exists("start", $proplist_family_relation) || count($proplist_family_relation) == 0)
				{
					$this->vars(array(
						"CRM_FAMILY_RELATION.START" => $this->parse("CRM_FAMILY_RELATION.START"),
					));
				}
				if(array_key_exists("end", $proplist_family_relation) || count($proplist_family_relation) == 0)
				{
					$this->vars(array(
						"CRM_FAMILY_RELATION.END" => $this->parse("CRM_FAMILY_RELATION.END"),
					));
				}
				$CRM_FAMILY_RELATION[$to->prop("relation_type")] .= $this->parse("CRM_FAMILY_RELATION_".$to->prop("relation_type"));
				$parse_cfr++;
			}
		}

		if($parse_cfr > 0)
		{
			foreach($CRM_FAMILY_RELATION as $id => $data)
			{
				$this->vars(array(
					"CRM_FAMILY_RELATION_".$id => $data,
				));
			}
			$this->vars(array(
				"CRM_FAMILY_RELATIONS" => $this->parse("CRM_FAMILY_RELATIONS"),
			));
		}
		// END SUB: CRM_FAMILY_RELATIONS

		// SUB: CRM_RECOMMENDATIONS
		$parse_r = 0;
		$CRM_RECOMMENDATION = "";

		unset($rol);
		if($this->can("view", $arr["job_offer"]))
		{
			$c_ol = new object_list(array(
				"class_id" => CL_PERSONNEL_MANAGEMENT_CANDIDATE,
				"person" => $o->id,
				"job_offer" => $arr["job_offer"],
				"status" => array(),
				"lang_id" => array(),
				"parent" => array(),
				"site_id" => array(),
			));
			if($c_ol->count() > 0)
			{
				$rol = new object_list();
				$rec_conns = connection::find(array(
					"from" => $c_ol->ids(),
					"reltype" => "RELTYPE_RECOMMENDATION",
				));
				foreach($rec_conns as $rec_conn)
				{
					$rol->add($rec_conn["to"]);
				}
				if($rol->count() == 0)
				{
					unset($rol);
				}
			}
		}
		if(!isset($rol))
		{
			$rol = new object_list();
			foreach($o->connections_from(array("type" => "RELTYPE_RECOMMENDATION")) as $rcn)
			{
				$rol->add($rcn->prop("to"));
			}
		}

		$cff_rec = $cff_inst->get_sysdefault(array("clid" => CL_CRM_RECOMMENDATION));
		if($cff_rec)
		{
			$proplist_recommendation = $cff_inst->get_cfg_proplist($cff_rec);
		}
		else
		{
			$proplist_recommendation = array();
		}
		// NO CFGFORM CONCERNING DATA
		$proplist_recommendation = array();

		$props = array("person", "relation");
		foreach($props as $prop)
		{
			if(array_key_exists($prop, $proplist_recommendation) || count($proplist_recommendation) == 0)
			{
				$this->vars(array(
					"CRM_RECOMMENDATION.HEADER.".strtoupper($prop) => $this->parse("CRM_RECOMMENDATION.HEADER.".strtoupper($prop)),
				));
			}
		}
		$props = array(
			"person",
			"relation",
			"profession" => "person.profession",
			"org" => "person.company",
		);
		foreach($props as $real_prop => $prop)
		{
			if(array_key_exists($real_prop, $proplist_recommendation) || count($proplist_recommendation) == 0)
			{
				$this->vars(array(
					"CRM_RECOMMENDATION.HEADER.".strtoupper($prop) => $this->parse("CRM_RECOMMENDATION.HEADER.".strtoupper($prop)),
				));
			}
		}
		$this->vars(array(
			"CRM_RECOMMENDATION.HEADER.PERSON.PHONE" => $this->parse("CRM_RECOMMENDATION.HEADER.PERSON.PHONE"),
		));
		$this->vars(array(
			"CRM_RECOMMENDATION.HEADER.PERSON.EMAIL" => $this->parse("CRM_RECOMMENDATION.HEADER.PERSON.EMAIL"),
		));

		foreach($rol->arr() as $ro)
		{
			if(!$this->can("view", $ro->prop("person")))
			{
				continue;
			}
			$po = obj($ro->prop("person"));
			foreach($po->connections_from(array("type" => "RELTYPE_ORG_RELATION", "to.class_id" => CL_CRM_PERSON_WORK_RELATION)) as $cn)
			{
				$wr = $cn->to();
				$profession = $wr->prop("profession.name");
				$company = $wr->prop("org.name");
				$person = obj($ro->prop("person"));
				$phone = "";
				$email = "";
				$phones = $person->phones();
				$emails = $person->emails();
				if($phones->count() > 0)
				{
					$phone = reset($phones->names());
				}
				if($emails->count() > 0)
				{
					$email = reset($emails->arr())->mail;
				}
				break;
			}
			$this->vars(array(
				"recommendation.person" => $ro->prop("person.name"),
				"recommendation.relation" => $ro->prop("relation.name"),
				"recommendation.person.profession" => $profession,
				"recommendation.person.company" => $company,
				"recommendation.person.phone" => $phone,
				"recommendation.person.email" => $email,
			));
			if(array_key_exists("person", $proplist_recommendation) || count($proplist_recommendation) == 0)
			{
				$this->vars(array(
					"RECOMMENDATION.PERSON" => $this->parse("RECOMMENDATION.PERSON"),
				));
			}
			if(array_key_exists("relation", $proplist_recommendation) || count($proplist_recommendation) == 0)
			{
				$this->vars(array(
					"RECOMMENDATION.RELATION" => $this->parse("RECOMMENDATION.RELATION"),
				));
			}
			if(array_key_exists("profession", $proplist_recommendation) || count($proplist_recommendation) == 0)
			{
				$this->vars(array(
					"RECOMMENDATION.PERSON.PROFESSION" => $this->parse("RECOMMENDATION.PERSON.PROFESSION"),
				));
			}
			if(array_key_exists("org", $proplist_recommendation) || count($proplist_recommendation) == 0)
			{
				$this->vars(array(
					"RECOMMENDATION.PERSON.COMPANY" => $this->parse("RECOMMENDATION.PERSON.COMPANY"),
				));
			}
			$this->vars(array(
				"RECOMMENDATION.PERSON.PHONE" => $this->parse("RECOMMENDATION.PERSON.PHONE"),
			));
			$this->vars(array(
				"RECOMMENDATION.PERSON.EMAIL" => $this->parse("RECOMMENDATION.PERSON.EMAIL"),
			));
			unset($profession);
			unset($company);
			$CRM_RECOMMENDATION .= $this->parse("CRM_RECOMMENDATION");
			$parse_r++;
		}

		if($parse_r > 0)
		{
			$this->vars(array(
				"CRM_RECOMMENDATION.HEADER" => $this->parse("CRM_RECOMMENDATION.HEADER"),
			));
			$this->vars(array(
				"CRM_RECOMMENDATION" => $CRM_RECOMMENDATION,
			));
			$this->vars(array(
				"CRM_RECOMMENDATIONS" => $this->parse("CRM_RECOMMENDATIONS"),
			));
		}
		// END SUB: CRM_RECOMMENDATIONS

		// SUB: CRM_PERSON.ADDINFO
		if(strlen($o->prop("addinfo")) > 0 && (array_key_exists("addinfo", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.addinfo" => nl2br($o->prop("addinfo")),
			));
			$this->vars(array(
				"CRM_PERSON.ADDINFO" => $this->parse("CRM_PERSON.ADDINFO"),
			));
		}
		// END SUB: CRM_PERSON.ADDINFO

		// SUB: CRM_PERSON.NOTES
		if(strlen($o->prop("notes")) > 0 && (array_key_exists("notes", $proplist) || count($proplist) == 0))
		{
			$this->vars(array(
				"crm_person.notes" => nl2br($o->prop("notes")),
			));
			$this->vars(array(
				"CRM_PERSON.NOTES" => $this->parse("CRM_PERSON.NOTES"),
			));
		}
		// END SUB: CRM_PERSON.NOTES

		return $arr["die"]?die($this->parse()):$this->parse();
	}

	function recursive_ids($id, $arr, $ids)
	{
		foreach($arr[$id] as $id_ => $arr_)
		{
			$ids[] = $this->recursive_ids($id_, $arr, &$ids);
		}
		return $id;
	}

	function get_project_roles($arr)
	{
		$role_list = new object_list(array(
			"class_id" => CL_CRM_COMPANY_ROLE_ENTRY,
			"person" => $arr["person"],
			"project" => $arr["project"],
		));
		foreach($role_list->arr() as $re)
		{
			if(trim($_t = $re->prop_str("role")))
			{
				$roles[] = $_t;
			}
		}
		return $roles;
	}

	function _init_ext_sys_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Siduss&uuml;steem"),
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "value",
			"caption" => t("V&auml;&auml;rtus"),
			"align" => "center"
		));
	}

	function _ext_sys_t($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_ext_sys_t($t);

		$cc = get_instance(CL_CRM_COMPANY);
		$crel = $cc->get_cust_rel($arr["obj_inst"], true);

		$data = array();
		foreach($crel->connections_from(array("type" => "RELTYPE_EXT_SYS_ENTRY")) as $c)
		{
			$ent = $c->to();
			$data[$ent->prop("ext_sys_id")] = $ent->prop("value");
		}
		// list all ext systems and let the user edit those
		$ol = new object_list(array(
			"class_id" => CL_EXTERNAL_SYSTEM,
			"lang_id" => array(),
			"site_id" => array(),
			"sort_by" => "objects.jrk"
		));
		foreach($ol->arr() as $o)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"value" => html::textbox(array(
					"name" => "ext[".$o->id()."]",
					"value" => $data[$o->id()],
				))
			));
		}
	}

	function _save_ext_sys_t($arr)
	{
		$cc = get_instance(CL_CRM_COMPANY);
		$crel = $cc->get_cust_rel($arr["obj_inst"], true);
		$ol = new object_list(array(
			"class_id" => CL_EXTERNAL_SYSTEM,
			"lang_id" => array(),
			"site_id" => array()
		));
		$data = array();
		foreach($crel->connections_from(array("type" => "RELTYPE_EXT_SYS_ENTRY")) as $c)
		{
			$ent = $c->to();
			$data[$ent->prop("ext_sys_id")] = $ent->id();
		}
		foreach($ol->arr() as $o)
		{
			if (!isset($data[$o->id()]))
			{
				// create new entry obj
				$ent = obj();
				$ent->set_name(sprintf(t("Siduss&uuml;steemi %s sisestus objektile %s"), $o->name(), $arr["obj_inst"]->name()));
				$ent->set_class_id(CL_EXTERNAL_SYSTEM_ENTRY);
				$ent->set_parent($arr["obj_inst"]->id());
				$ent->set_prop("ext_sys_id", $o->id());
				$ent->set_prop("obj", $arr["obj_inst"]->id());
				$ent->set_prop("value", $arr["request"]["ext"][$o->id()]);
				$ent->save();
				$crel->connect(array(
					"to" => $ent->id(),
					"type" => "RELTYPE_EXT_SYS_ENTRY"
				));
			}
			else
			{
				$ent = obj($data[$o->id()]);
				$ent->set_prop("value", $arr["request"]["ext"][$o->id()]);
				$ent->save();
			}
		}
	}

	function parse_url_parse_query($return_url)
	{
		$url = parse_url($return_url);
		$query = explode("&", $url["query"]);
		foreach($query as $q)
		{
			$t = explode("=", $q);
			$ret[$t[0]] = isset($t[1]) ? $t[1] : "";
		}
		return $ret;
	}

	/** returns a line of info about the person - name, company, section, email, phone
		@attrib api=1 params=pos

		@param p required type=oid
			The person to return the info for
	**/
	function get_short_description($arr)
	{
		$org_fixed = 0;
		if(!is_array($arr))
		{
			$p = obj($arr);
		}
		else
		{
			$query = $this->parse_url_parse_query($arr["request"]["return_url"]);
			if($query["class"] == "crm_company" && $this->can("view", $query["id"]))
			{
				$org_fixed = $query["id"];
			}
			$p = $arr["obj_inst"];
		}
		$p_href = html::obj_change_url($p->id());

		if (!is_oid($p->id()))
		{
			return;
		}
		$cwrs = array();
		$cou = 0;
		foreach($p->get_active_work_relations()->arr() as $to)		// RELTYPE_CURRENT_JOB
		{
			$toid = $to->id();
			$orgid = $to->prop("org");
			if(($orgid != $org_fixed && $org_fixed != 0 ) || !$orgid)
			{
				continue;
			}

			$cwrs[$orgid]["professions"][$cou] = $to->prop("profession");
			foreach($to->connections_from(array("type" => 8)) as $cn)		// RELTYPE_PHONE
			{
				if($this->can("view", $cn->conn["to"]))
				{
					$ph_obj = $cn->to();
					$ph_obj->conn_id = $cn->id();
					$cwrs[$orgid]["phones"][$cn->conn["to"]] = $cn->conn["to.name"];
				}
			}
			foreach($to->connections_from(array("type" => 9)) as $cn)		// RELTYPE_EMAIL
			{
				if($this->can("view", $cn->conn["to"]))
				{
					$cwrs[$orgid]["emails"][$cn->conn["to"]] = $cn->conn["to.name"];
				}
			}
			foreach($to->connections_from(array("type" => 10)) as $cn)		// RELTYPE_FAX
			{
				if($this->can("view", $cn->conn["to"]))
				{
					$cwrs[$orgid]["faxes"][$cn->conn["to"]] = $cn->conn["to.name"];
				}
			}

			$cou++;
		}
		$ret = $p_href;
		$ph_inst = get_instance(CL_CRM_PHONE);
		$phone_types = $ph_inst->phone_types;
		foreach($p->connections_from(array("type" => "RELTYPE_PHONE")) as $cn)
		{
			$to = $cn->to();
			$ret .= ", ".$phone_types[$to->prop("type")]." ".html::obj_change_url($to->id(), $to->name, array("conn_id" => $cn->id()));
		}
		foreach($p->connections_from(array("type" => "RELTYPE_EMAIL")) as $cn)
		{
			$to = $cn->to();
			$ret .= ", ".html::obj_change_url($to->id(), (strlen($to->prop("mail")) ? $to->prop("mail") : t("[m&auml;&auml;ramata]")));
		}
		foreach($p->connections_from(array("type" => "RELTYPE_FAX")) as $cn)
		{
			$to = $cn->to();
			$ret .= ", ".t("faks")." ".html::obj_change_url($to->id());
		}

//		arr($cwrs);
		foreach($cwrs as $org_id => $data)
		{
			if(strlen($ret) > 0)
			{
				$ret .= "<br>";
			}
			if($this->can("view", $org_id))
			{
				$ret .= html::obj_change_url($org_id);
			}
			else
			{
				$ret .= " <i>ORGANISATSIOON M&Auml;&Auml;RAMATA</i>";
			}
			foreach($data["professions"] as $prof)
			{
				if(!$this->can("view", $prof))
				{
					continue;
				}
				$ret .= ", ".html::obj_change_url($prof);
			}
			foreach($data["phones"] as $ph_id => $ph)
			{
				$ph_obj = obj($ph_id);
				$ret .= ", ".html::obj_change_url($ph_id, $ph, array("conn_id" => $ph_obj->conn_id));
			}
			foreach($data["emails"] as $ml_id => $ml)
			{
				$ml_obj = new object($ml_id);
				$ret .= ", ".html::obj_change_url($ml_id, (strlen($ml_obj->prop("mail")) ? $ml_obj->prop("mail") : t("[m&auml;&auml;ramata]")));
			}
			if(sizeof($data["faxes"]) > 0)
				$ret .= ", faks ";
			$mtof = false;
			foreach($data["faxes"] as $fx_id => $fx)
			{
				if($mtof)
					$ret .= ",";

				$ret .= " ".html::obj_change_url($fx_id);
				$mtof = true;
			}
		}
		return $ret;
	}

	/*
	function get_short_description($p)		// OLD VERSION OF THIS FUNCTION
	{
		$p = obj($p);
		$ret = html::href(array(
			'url' => html::get_change_url($p->id()),
			'caption' => $p->name(),
		));
		//default company
		if(is_oid($p->prop('work_contact')))
		{
			$company = new object($p->prop('work_contact'));
			$ret .= " ".html::href(array(
				'url' => html::get_change_url($company->id()),
				'caption' => $company->name(),
			));
		}
		//professions...
		$conns2 = $p->connections_from(array(
			'type' => 'RELTYPE_RANK',
		));
		$professions = '';
		foreach($conns2 as $conn2)
		{
			$professions.=', '.$conn2->prop('to.name');
		}
		if(strlen($professions))
		{
			$ret.=$professions;
		}
		//phones
		$conns2 = $p->connections_from(array(
			'type' => 'RELTYPE_PHONE'
		));
		$phones = '';
		foreach($conns2 as $conn2)
		{
			$phones.=', '.$conn2->prop('to.name');
		}
		if(strlen($phones))
		{
			$ret.=$phones;
		}
		$conns2 = $p->connections_from(array(
			'type' => 'RELTYPE_EMAIL',
		));
		$emails = '';
		foreach($conns2 as $conn2)
		{
			$to_obj = $conn2->to();
			$emails.=', '.$to_obj->prop('mail');
		}
		if(strlen($emails))
		{
			$ret.=$emails;
		}

		$conns = $p->connections_from(array(
			"type" => "RELTYPE_BANK_ACCOUNT",
		));
		if(sizeof($conns))
		{
			$aa = array();
			foreach($conns as $c)
			{
				$a = $c->to();
				$aa[] = $a->prop("acct_no");
			}
			$accounts = join($aa, ', ');
			$ret .= ', '.$accounts;
		}
		return $ret;
	}
	/**/

	function _ct_rel_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$confirm_test = t("Kustutada valitud objektid?");

		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus"),
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"name" => "new_work_relation",
			"text" => t("T&ouml;&ouml;suhe"),
			"title" => t("T&ouml;&ouml;suhe2"),
			"action" => "new_work_relation",
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_objects"
		));

		$tb->add_save_button();
	}

	function _init_my_stats_rows_t(&$t)
	{
		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"sortable" => 1,
			"numeric" => 1,
			"type" => "time",
			"format" => "d.m.Y",
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "cust",
			"caption" => t("Klient"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "proj",
			"caption" => t("Projekt"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "task",
			"caption" => t("Toimetus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "content",
			"caption" => t("Rea sisu"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "length",
			"caption" => t("Kestvus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "length_cust",
			"caption" => t("Kestvus Kliendile"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"align" => "center",
			"numeric" => 1
		));
		$t->define_field(array(
			"name" => "bill_nr",
			"caption" => t("Arve nr."),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "bill_state",
			"caption" => t("Arve staatus"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "check",
			"caption" => "<a href='#' onClick='aw_sel_chb(document.changeform,\"sel\")'>".t("Vali")."</a>",
			"align" => "center",
		));
	}

	function _get_my_stats_rows($arr)
	{
		classload("core/date/date_calc");
		$r = $arr["request"];
		// list all rows for me and the time span
		if(!($r["stats_s_from"]))
		{
			$r["stats_s_from"] = get_month_start();
		}
		else
		{
			$r["stats_s_from"] = date_edit::get_timestamp($r["stats_s_from"]);
		}
		if($r["stats_s_to"])
		{
			$r["stats_s_to"] = time();
		}
		else
		{
			$r["stats_s_to"] = date_edit::get_timestamp($r["stats_s_to"]);
		}
		if ($r["stats_s_time_sel"] != "")
		{
			switch($r["stats_s_time_sel"])
			{
				case "today":
					$r["stats_s_from"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
					$r["stats_s_to"] = time();
					break;

				case "yesterday":
					$r["stats_s_from"] = time() - ((date("H")*3600 + date("i")*60 + date("s")) + 24*3600);
					$r["stats_s_to"] = time() - (date("H")*3600 + date("i")*60 + date("s"));
					break;

				case "cur_week":
					$r["stats_s_from"] = get_week_start();
					$r["stats_s_to"] = time();
					break;

				case "cur_mon":
					$r["stats_s_from"] = get_month_start();
					$r["stats_s_to"] = time();
					break;

				case "last_mon":
					$r["stats_s_from"] = mktime(0,0,0, date("m")-1, 1, date("Y"));
					$r["stats_s_to"] = get_month_start();
					break;
			}
		}

		$p = get_current_person();
		$row_filter = array(
			"class_id" => CL_TASK_ROW,
			"lang_id" => array(),
			"site_id" => array(),
			"impl" => $p->id(),
	//		"date" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $r["stats_s_from"], $r["stats_s_to"]),
		);

		$row_filter[] = new object_list_filter(array(
			"logic" => "OR",
			"conditions" => array(
				"date" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $r["stats_s_from"]-1, ($r["stats_s_to"] + 86399)),
				new object_list_filter(array(
					"logic" => "AND",
					"conditions" => array(
						"date" => new obj_predicate_compare(OBJ_COMP_LESS, 1),
						"created" => new obj_predicate_compare(OBJ_COMP_BETWEEN, $r["stats_s_from"]-1, ($r["stats_s_to"] + 86399)),
					)
				))
			)
		));

		$ol = new object_list($row_filter);

		classload("vcl/table");
		$t = new vcl_table;
		$this->_init_my_stats_rows_t($t);

		$row2task = array();
		$c = new connection();
		foreach($c->find(array("to" => $ol->ids(), "from.class_id" => CL_TASK, "type" => "RELTYPE_ROW")) as $c)
		{
			if ($arr["request"]["stats_s_cust"] != "")
			{
				$task = obj($c["from"]);
				if (strpos(mb_strtolower($task->prop("customer.name"), aw_global_get("charset")), mb_strtolower($arr["request"]["stats_s_cust"], aw_global_get("charset"))) === false)
				{
					continue;
				}
			}
			$row2task[$c["to"]] = $c["from"];
		}
//arr($row2task);
		$stat_inst = get_instance("applications/crm/crm_company_stats_impl");
		$task_inst = get_instance("applications/groupware/task");
		$row_inst = get_instance("applications/groupware/task_row");
		$bill_inst = get_instance(CL_CRM_BILL);
		$company_curr = $stat_inst->get_company_currency();
		$bi = get_instance(CL_BUG);

		$l_cus_s = 0;

		foreach($ol->arr() as $o)
		{
			$impl = $check = $bs = $bn = "";
			$b = null;
			$agreement = array();
			if ($this->can("view", $o->prop("bill_id")))
			{
				$b = obj($o->prop("bill_id"));
				//$bs = sprintf(t("Arve nr %s"), $b->prop("bill_no"));
				$bn = $b->prop("bill_no");
				$bs = $bill_inst->states[$b->prop("state")];
				$agreement = $b->meta("agreement_price");
			//	$bs = html::obj_change_url($o->prop("bill_id"));
			}
			elseif ($o->prop("on_bill"))
			{
				$bs = t("Arvele");
				$check = html::checkbox(array(
					"name" => "sel[]",
					"value" => $o->id()
				));
			}
			else
			{
				$bs = t("Arve puudub");
			}

			$task = obj($row2task[$o->id()]);
			if (!is_oid($task->id()))
			{
				continue;
			}


			$ttc = $o->prop("time_to_cust");
			$time_real = $o->prop("time_real");
			$sum = str_replace(",", ".", $ttc);
			$sum *= str_replace(",", ".", $task->prop("hr_price"));

			//kui on kokkuleppehind kas arvel, v6i kui arvet ei ole, siis toimetusel... tuleb v2he arvutada
			if((is_object($b) && sizeof($agreement) && ($agreement[0]["price"] > 0)) || (!is_object($b) && $task->prop("deal_price")))
			{
				$sum = $row_inst->get_row_ageement_price($o);
				$br_ol = new object_list(array(
					"class_id" => CL_CRM_BILL_ROW,
					"lang_id" => array(),
					"site_id" => array(),
					"CL_CRM_BILL_ROW.RELTYPE_TASK_ROW" => $o->id(),
				));
				$br = reset($br_ol->arr());
				if((sizeof($agreement) && ($agreement[0]["price"] > 0)) || (!is_object($b) && $task->prop("deal_price")))
				{
					$sum = $row_inst->get_row_ageement_price($o);
				}
				elseif(is_object($br) && !$o->meta("parent_row"))
				{
					$ttc = $br->prop("amt");
					foreach($br->connections_from(array("type" => "RELTYPE_TASK_ROW")) as $c)
					{
						$other_tr = $c->to();
						if($other_tr->meta("parent_row") == $o->id())
						{
							$time_real += $other_tr->prop("time_real");
						}
					}
					$sum = $br->get_sum();//if(aw_global_get("uid") == "Teddi.Rull") arr($sum);
				}
				else
				{
					continue;
				}
			}

			//6igesse valuutasse
			$sum = $stat_inst->convert_to_company_currency(array(
				"sum"=>$sum,
				"o"=>$task,
				"company_curr" => $company_curr,
			));

			$t->define_data(array(
				"date" => $o->prop("date"),
				"cust" => html::obj_change_url($task->prop("customer")),
				"proj" => html::obj_change_url($task->prop("project")),
				"task" => html::obj_change_url($task),
				"content" => $bi->_split_long_words($o->prop("content")),
				"length" => $stat_inst->hours_format($time_real),// 3, ',', ''),
				//"length_cust" => number_format($o->prop("time_to_cust"), 2, ',', ''),
				"length_cust" => (!is_oid($bn))?html::textbox(array(
					"name" => "rows[".$o->id()."][time_to_cust]",
					"value" => $stat_inst->hours_format($ttc),//number_format($ttc, 3, ',', ''),
					"size" => 4,
				)).html::hidden(array(
					"name" => "rows[".$o->id()."][time_to_cust_real]",
					"value" => $stat_inst->hours_format($ttc),//number_format($ttc, 3, ',', ''),
				)): $stat_inst->hours_format($ttc),//number_format($ttc, 3, ',', ''),

				"state" => $o->prop("done") ? t("Tehtud") : t("Tegemata"),
				"bill_state" => $bs,
				"check" => $check,
				"sum" => number_format($sum, 2, ',', ''),
				"bill_nr" => $bn,
			));
			$l_sum += $time_real;
			$s_sum += $sum;
			$l_cus_s += $ttc;
		}

		$t->set_default_sortby("date");
		$t->sort_by();

		$t->define_data(array(
			"content" => t("<b>Summa</b>"),
			"length" =>  $stat_inst->hours_format($l_sum),//number_format($l_sum, 3, ',', ''),
			"sum" => number_format($s_sum, 2, ',', ''),
			"length_cust" =>  $stat_inst->hours_format($l_cus_s),//number_format($l_cus_s, 3, ',', ''),
		));
		$arr["prop"]["value"] = $t->draw();
	}

	function __content_format($val)
	{
		return $val["content_val"];
	}

	/**
		@attrib name=submit_delete_docs
		@param sel optional
		@param post_ru optional
	**/
	function submit_delete_docs($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array(
				"oid" => $arr["sel"]
			));
			$ol->foreach_o(array("func" => "delete"));
		}
		return $arr["post_ru"];
	}

	function _skills_tb($arr)
	{
		$tb =& $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"url" => html::get_new_url(CL_PERSON_HAS_SKILL, $arr["obj_inst"]->id(), array(
				"return_url" => get_ru(),
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 53
			)),
			"tooltip" => t("Lisa")
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"action" => "delete_skills",
			"tooltip" => t("Kustuta p&auml;devused")
		));
	}

	function _init_skills_t(&$t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("P&auml;devus"),
			"align" => "center",
			"sortable" => 1
		));
		$t->define_field(array(
			"name" => "created",
			"caption" => t("Omandatud"),
			"align" => "center",
			"sortable" => 1,
			"type" => "time",
			"numeric" => 1,
			"format" => "d.m.Y"
		));
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel"
		));
	}

	function _skills_table($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
		$this->_init_skills_t($t);

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_HAS_SKILL")) as $c)
		{
			$o = $c->to();
			$t->define_data(array(
				"name" => html::obj_change_url($o),
				"created" => $o->prop("skill_acquired"),
				"oid" => $o->id()
			));
		}
	}

	function _atwork_table($arr)
	{
		classload("core/date/date_calc");
		$m = get_instance("applications/rostering/rostering_model");
		$start = get_week_start();
		$end = get_week_start()+24*7*3600;
		$work_times = $m->get_schedule_for_person($arr["obj_inst"], $start, $end);
		$chart = get_instance("vcl/gantt_chart");
		$chart->configure_chart (array (
			"chart_id" => "person_wh",
			"style" => "aw",
			"start" => $start,
			"end" => $end,
			"width" => 850,
			"row_height" => 10,
		));

		$pl_done = array();
		foreach($work_times as $wt_item)
		{
			if ($pl_done[$wt_item["workplace"]])
			{
				continue;
			}
			$pl_done[$wt_item["workplace"]] = 1;
			$wpl = obj($wt_item["workplace"]);
			$chart->add_row (array (
				"name" => $wpl->id(),
				"title" => $wpl->name(),
				"uri" => html::obj_change_url($wpl)
			));
		}

		static $wtid;
		foreach($work_times as $wt_item)
		{
			$bar = array (
				"id" => ++$wtid,
				"row" => $wt_item["workplace"],
				"start" => $wt_item["start"],
				"length" => $wt_item["end"] - $wt_item["start"],
				"title" => date("d.m.Y H:i", $wt_item["start"])." - ".date("d.m.Y H:i", $wt_item["end"]),
			);

			$chart->add_bar ($bar);
		}

		$i = 0;
		$days = array ("P", "E", "T", "K", "N", "R", "L");
		$columns = 7;
		while ($i < $columns)
		{
			$day_start = (get_day_start() + ($i * 86400));
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

		$arr["prop"]["value"] = $chart->draw_chart();
	}

	function get_work_project_tasks($oid)
	{
		$o = obj($oid);
		return aw_unserialize($o->prop("work_projects_tasks"));
	}

	function set_work_project_tasks($oid, $tasks)
	{
		$o = obj($oid);
		$o->set_prop("work_projects_tasks", aw_serialize($tasks, SERIALIZE_NATIVE));
		$o->save();
		return true;
	}

	function get_person_and_org_related_projects($person_oid)
	{
		$i = new user();
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_PARTICIPANT" => array(
				$person_oid,
				$i->get_company_for_person($person_oid),
			),
		));
		return $ol->arr();
	}

//----------------------- kliendisuhte funktsioonid-----------------------
	function get_cust_rel($o,$crea_if_not_exists,$my_co)
	{
		$co_inst = get_instance(CL_CRM_COMPANY);
		return $co_inst->get_cust_rel($o, $crea_if_not_exists, $my_co);
	}


	function _get_co_is_cust($arr)
	{
		$crel = $this->get_cust_rel($arr["obj_inst"]);
		if ($crel || $arr["request"]["set_as_is_cust"])
		{
			$arr["prop"]["value"] = 1;
		}
	}

	function _set_co_is_cust($arr)
	{
		if ($arr["prop"]["value"] == 1)
		{
			$crel = $this->get_cust_rel($arr["obj_inst"], true);
		}
		else
		{
			$crel = $this->get_cust_rel($arr["obj_inst"]);
			if ($crel)
			{
				$crel->delete();
			}
		}
	}

	function _get_co_is_buyer($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel || $arr["request"]["set_as_is_buyer"])
		{
			$arr["prop"]["value"] = 1;
		}
	}

	function _set_co_is_buyer($arr)
	{
		$cur = get_current_company();
		if ($arr["prop"]["value"] == 1)
		{
			$crel = $this->get_cust_rel($cur, true,$arr["obj_inst"]);
		}
		else
		{
			$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
			if ($crel)
			{
				$crel->delete();
			}
		}
	}

	function set_cust_rel_data($arr)
	{
		if (!$arr["request"]["co_is_cust"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($arr["obj_inst"], false, $cur);
		if ($crel)
		{
			$crel->set_prop($arr["prop"]["name"], $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function set_buyer_rel_data($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop($arr["prop"]["name"], $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _get_person_tb($arr)
	{
		$tb = &$arr["prop"]["toolbar"];
		$tb->add_menu_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus"),
		));

		$tb->add_menu_item(array(
			'parent'=>'new',
			'text'=>t('Kodakondsus'),
			"tooltip" => t("Lisa uus kodakondsus"),
			"action" => "add_new_citizenship",
			"confirm" => t("Lisan uue kodakondsuse?"),
		));
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "delete_obj",
			"confirm" => t("Oled kindel, et kustutada?"),
		));

	}

	/**
		@attrib name=add_new_citizenship all_args=1
	**/
	function add_new_citizenship($arr)
	{
		$person = obj($arr["id"]);
		$c = new object();
		$c->set_class_id(CL_CITIZENSHIP);
		$c->set_name($person->name()." ".t("kodakondsus"));
		$c->set_parent($person->id());
		if($person->prop("birthday"))
		{
			$c->set_prop("start" , $person->prop("birthday"));
		}
		$c->save();
 		$person->connect(array(
			"to" => $c->id(),
			"reltype"=> "RELTYPE_CITIZENSHIP",
		));
		return $arr["post_ru"];
	}

	function _get_citizenship_table($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->set_caption(t("Kodakondsused"));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$t->define_field(array(
			"name" => "country",
			"caption" => t("Riik"),
		));
		$t->define_field(array(
			"name" => "start",
			"caption" => t("Algus"),
		));
		$t->define_field(array(
			"name" => "end",
			"caption" => t("L&otilde;pp"),
		));

		$country_options = new object_list(array(
			"class_id" => CL_CRM_COUNTRY,
			"lang_id" => array(),
			"site_id" => array(),
		));

		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_CITIZENSHIP")) as $conn)
		{
			$c = $conn->to();
			$data = array();
			$data["oid"] = $c->id();
			$data["start"] = html::date_select(array(
				"name" => "citizenship[".$c->id()."][start]",
				"value" => $c->prop("start"),
				"year_from" => 1900,
				"year_to" => date("Y" , time()) + 5,
				"default" => -1
			));
			$data["end"] = html::date_select(array(
				"name" => "citizenship[".$c->id()."][end]",
				"value" => $c->prop("end"),
				"year_from" => 1900,
				"year_to" => date("Y" , time()) + 5,
				"default" => -1
			));
			$data["country"] = html::select(array(
				"name" => "citizenship[".$c->id()."][country]",
				"value" => $c->prop("country"),
				"options" => $country_options->names(),
			));

			$t->define_data($data);
		}
	}



	/**
		@attrib name=c2wr
		@param id required type=int
		@param wrid required type=int
		@param toid required type=int
		@param reltype required type=int
		@param return_url required type=string
	**/
	function c2wr($arr)
	{
		// Isiklik
		if($arr["wrid"] == 0 && is_oid($arr["toid"]))
		{
			$to = new object($arr["toid"]);
			foreach($to->connections_from() as $conn)
			{
				$pwr = $conn->to();
				if($pwr->class_id() == CL_CRM_PERSON_WORK_RELATION)
				{
					$conn->delete(true);
				}
			}
			$o = new object($arr["id"]);
			$o->connect(array(
				"to" => $arr["toid"],
				"reltype" => $arr["reltype"],
			));
			header("Location: ".$arr["return_url"]);
		}
		elseif(is_oid($arr["wrid"]) && is_oid($arr["toid"]))
		{
			$reltypes = array(
				8 => 13,
				9 => 11,
				10 => 54,
			);
			$connect = true;
			$wr = new object($arr["wrid"]);
			$wrc = 0;
			foreach($wr->connections_from(array("type" => $arr["reltype"])) as $conn)
			{
				$wrc++;
				if($conn->conn["to"] == $arr["toid"])
				{
					$conn->delete(true);
					$connect = false;
				}
			}
			if($wrc <= 1)
			{
				$to = new object($arr["toid"]);
				$o = new object($arr["id"]);
				$o->connect(array(
					"to" => $arr["toid"],
					"reltype" => $reltypes[$arr["reltype"]],
				));
			}
			if($connect)
			{
				$wr->connect(array(
					"to" => $arr["toid"],
					"reltype" => $arr["reltype"],
				));
				$o = new object($arr["id"]);
				$o->disconnect(array(
					"from" => $arr["toid"],
					"errors" => false,
				));
			}
			header("Location: ".$arr["return_url"]);
		}
	}

	function _save_citizenship_table($arr)
	{
		foreach($arr["request"]["citizenship"] as $id => $val)
		{
			$c = obj($id);
			foreach($val as $prop => $v)
			{
				if(($prop == "end" || $prop == "start") && !($v["year"] > 0))
				{
					$v = -1;
				}
				$c->set_prop($prop , $v);
			}
			$c->save();
		}
	}

	function get_skills($id)
	{
		$odl = new object_data_list(
			array(
				"class_id" => CL_META,
				"parent" => $id,
				"status" => STAT_ACTIVE,
			),
			array(
				CL_META => array("name"),
			)
		);
		$ids = array();
		foreach($odl->arr() as $oid => $odata)
		{
			$ret[$oid] = $odata["name"];
			$ids[] = $oid;
		}
		if(count($ids) > 0)
		{
			$adds = $this->get_skills($ids);

			foreach($adds as $add_id => $add)
			{
				$ret[$add_id] = $add;
			}
		}

		return $ret;
	}

	/**
		@attrib name=add_skill params=name

		@param id required type=oid

		@param skill_id required type=oid

		@param return_url required type=string

	**/
	function add_skill($arr)
	{
		/*
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"CL_CRM_PERSON.RELTYPE_SKILL_LEVEL.skill" => $arr["skill_id"],
			"oid" => $arr["id"],
		));
		*/
		$ol = new object_list;
		if($ol->count() == 0)
		{
			$p = obj($arr["id"]);
			$s = new object;
			$s->set_class_id(CL_CRM_SKILL_LEVEL);
			$s->set_parent($arr["id"]);
			$s->set_status(object::STAT_ACTIVE);
			$s->set_prop("skill", $arr["skill_id"]);
			$s->save();
			$p->connect(array(
				"to" => $s->id(),
				"reltype" => array("RELTYPE_SKILL_LEVEL", "RELTYPE_SKILL_LEVEL2", "RELTYPE_SKILL_LEVEL3", "RELTYPE_SKILL_LEVEL4", "RELTYPE_SKILL_LEVEL5"),
			));
		}

		return $arr["return_url"];
	}

	/**
		@attrib name=add_lang_skill params=name

		@param id required type=oid

		@param lang_id required type=oid

		@param return_url required type=string

	**/
	function add_lang_skill($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_PERSON,
			"CL_CRM_PERSON.RELTYPE_LANGUAGE_SKILL.language" => $arr["lang_id"],
			"oid" => $arr["id"],
		));
		if($ol->count() == 0)
		{
			$p = obj($arr["id"]);
			$s = new object;
			$s->set_class_id(CL_CRM_PERSON_LANGUAGE);
			$s->set_parent($arr["id"]);
			$s->set_status(object::STAT_ACTIVE);
			$s->set_prop("language", $arr["lang_id"]);
			$s->save();
			$p->connect(array(
				"to" => $s->id(),
				"reltype" => "RELTYPE_LANGUAGE_SKILL",
			));
		}

		return $arr["return_url"];
	}

	private function anything_in_progress($conns)
	{
		foreach($conns as $conn)
		{
			$to = $conn->to();
			if($to->in_progress)
			{
				return true;
			}
		}
		return false;
	}

	function _get_not_working($arr)
	{
		$arr["prop"]["onclick"] = "el=document.getElementById(\"current_workplace_outer\");if (this.checked) { el.style.display=\"none\"; } else { el.style.display=\"\"}";
	}

	/**
	@attrib name=new_work_relation params=name

	@param id required type=oid

	@param post_ru required type=string

	**/
	function new_work_relation($arr)
	{
		extract($arr);

		$o = obj($id);
		$wr = obj();
		$wr->set_class_id(CL_CRM_PERSON_WORK_RELATION);
		$wr->set_parent($id);

		$url = parse_url($post_ru);
		$qry = parse_str($url["query"], $res);
		$url2 = parse_url($res["return_url"]);
		$qry2 = parse_str($url2["query"], $res2);
		if($res2["class"] == "crm_company")
		{
			if($this->can("view", $res2["id"]))
			{
				$wr->org = $res2["id"];
			}
			if($this->can("view", $res2["unit"]))
			{
				$wr->section = $res2["unit"];
			}
		}

		$wr->save();
		$o->connect(array(
			"to" => $wr,
			"type" => "RELTYPE_CURRENT_JOB",
		));

		return $post_ru;
	}	/**
		@attrib name=cut_docs
	**/
	function cut_docs($arr)
	{
		return get_instance(CL_CRM_COMPANY)->cut_docs($arr);
	}

	/**
		@attrib name=submit_paste_docs
	**/
	function submit_paste_docs($arr)
	{
		return get_instance(CL_CRM_COMPANY)->submit_paste_docs($arr);
	}

	public function get_drivers_licence_original_categories()
	{
		return array(
			"a" => t("A"),
			"a1" => t("A1"),
			"a2" => t("A2"),
			"b" => t("B"),
			"b1" => t("B1"),
			"c" => t("C"),
			"c1" => t("C1"),
			"d" => t("D"),
			"d1" => t("D1"),
			"e" => t("E"),
			"f" => t("T&otilde;stuk"),
		);
	}

	public function drivers_licence_categories()
	{
		$this->drivers_licence_categories = $this->get_drivers_licence_original_categories();
		$this->drivers_licence_original_categories = $this->drivers_licence_categories;

		$pm = new object(get_instance(CL_PERSONNEL_MANAGEMENT)->get_sysdefault());
		$pm_dl = $pm->prop("drivers_license");
		$data = array();
		$data["options"] = $this->drivers_licence_categories;
		if(is_array($pm_dl))
		{
			foreach($data["options"] as $i => $v)
			{
				if(!in_array($i, $pm_dl))
				{
					unset($this->drivers_licence_categories[$i]);
				}
			}
		}
		return $this->drivers_licence_categories;
	}

	/**
	@attrib name=on_connect_to_meeting
	**/
	public function on_connect_to_meeting($arr)
	{
		return get_instance("crm_person_obj")->on_connect_to_meeting($arr);
	}

	/**
	@attrib name=on_connect_to_meeting
	**/
	public function on_connect_to_task($arr)
	{
		return get_instance("crm_person_obj")->on_connect_to_task($arr);
	}

	/**
		@attrib name=phones api=1
	**/
	public function phones($arr)
	{
		return get_instance("crm_person_obj")->phones($arr);
	}

	/**
		@attrib name=emails api=1
	**/
	public function emails($arr)
	{
		return get_instance("crm_person_obj")->emails($arr);
	}
}
?>
