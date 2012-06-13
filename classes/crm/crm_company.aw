<?php
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_SAVE, CL_CRM_ADDRESS, on_save_address)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_CRM_ADDRESS, on_save_address)
HANDLE_MESSAGE_WITH_PARAM(MSG_EVENT_ADD, CL_CRM_PERSON, on_add_event_to_person)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_NEW, CL_CRM_COMPANY, on_create_company)
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_CRM_COMPANY, on_delete_company)

@classinfo confirm_save_data=1 versioned=1 prop_cb=1 no_status=1

@tableinfo kliendibaas_firma index=oid master_table=objects master_index=oid
@tableinfo aw_account_balances master_index=oid master_table=objects index=aw_oid

@default table=objects

@default group=general_sub
	@property navtoolbar type=toolbar store=no no_caption=1 group=general_sub editonly=1

	@layout co_top type=hbox closeable=1 area_caption=&Uuml;ldandmed width=50%:50%

		@layout co_top_left type=vbox parent=co_top

			@property name type=textbox size=30 maxlength=255 table=objects parent=co_top_left
			@caption Organisatsiooni nimi

			@property short_name type=textbox size=10 table=kliendibaas_firma field=aw_short_name parent=co_top_left
			@caption Nime l&uuml;hend

			@property reg_nr type=textbox size=10 maxlength=20 table=kliendibaas_firma parent=co_top_left
			@caption Registri number

			@property ettevotlusvorm type=relpicker table=kliendibaas_firma automatic=1 reltype=RELTYPE_ETTEVOTLUSVORM parent=co_top_left no_search=1 no_edit=1
			@caption &Otilde;iguslik vorm

			@property comment type=textarea cols=40 rows=2 table=objects parent=co_top_left
			@caption Kommentaar

		@layout co_top_right type=vbox parent=co_top

			@property code type=textbox table=kliendibaas_firma parent=co_top_right
			@caption Kood

			@property tax_nr type=textbox table=kliendibaas_firma parent=co_top_right
			@caption KMKohuslase nr

			@property logo type=releditor reltype=RELTYPE_ORGANISATION_LOGO use_form=emb rel_id=first table=objects parent=co_top_right captionside=top override_parent=this store=connect
			@caption Organisatsiooni logo

			@property firmajuht type=relpicker reltype=RELTYPE_FIRMAJUHT table=kliendibaas_firma editonly=1 parent=co_top_right
			@caption Firmajuht

			@property year_founded type=datepicker time=0 from=1800-01-01T00:00:00 table=kliendibaas_firma parent=co_top_right
			@caption Asutatud

			@property client_category type=text store=no  parent=co_top_right
			@caption Kliendikategooria


@default group=open_hrs

	property openhours type=releditor reltype=RELTYPE_OPENHOURS rel_id=first use_form=emb
	caption Avamisajad

	@property oh_tb type=toolbar no_caption=1 store=no
	@property oh_t type=table store=no no_caption=1 parent=oh

------ Yldine - Tegevused grupp -----
@default group=org_sections

	@property kaubamargid type=textarea cols=65 rows=3 table=kliendibaas_firma
	@caption Kaubam&auml;rgid

	@property tegevuse_kirjeldus type=textarea cols=65 rows=3 table=kliendibaas_firma
	@caption Tegevuse kirjeldus

	@property tooted type=relpicker reltype=RELTYPE_TOOTED automatic=1 method=serialize field=meta table=objects
	@caption Tooted

	@property sales_warehouse type=relpicker reltype=RELTYPE_SALES_WAREHOUSE automatic=1 method=serialize field=meta table=objects
	@caption Valmistoodete/m&uuml;&uuml;gi ladu

	@property purchasing_manager type=relpicker reltype=RELTYPE_PURCHASING_MANAGER automatic=1 method=serialize field=meta table=objects
	@caption Laohaldus/ostukeskkond

	@property pohitegevus type=relpicker reltype=RELTYPE_TEGEVUSALAD multiple=1 store=connect
	@caption P&otilde;hitegevus / Tegevusalad

	@property activity_keywords type=textarea cols=65 rows=3 table=kliendibaas_firma
	@comment Komadega eraldatud
	@caption M&auml;rks&otilde;nad

------ Yldine - Lisainfo grupp----------
@default group=add_info

	@property field_manager type=releditor mode=manager reltype=RELTYPE_FIELD props=name,class_name table_fields=name,class_name direct_links=1
	@caption Valdkonnad

	@property correspond_address type=relpicker reltype=RELTYPE_CORRESPOND_ADDRESS table=objects field=meta method=serialize
	@caption Kirjavahetuse aadress

	@property address2_edit type=releditor mode=manager2 store=no props=country,location_data,location,street,house,apartment,postal_code,po_box table_fields=name,location,street,house,apartment reltype=RELTYPE_ADDRESS_ALT

	@property classif1 type=classificator store=connect reltype=RELTYPE_METAMGR
	@caption Asutuse omadused

	@property fake_phone type=textbox
	@caption Telefon

	@property fake_fax type=textbox
	@caption Faks

	@property fake_mobile type=textbox
	@caption Mobiiltelefon

	@property fake_skype type=textbox
	@caption Skype

	@property fake_email type=textbox
	@caption E-post

	@property fake_url type=textbox
	@caption WWW

	@property fake_address_address type=textbox
	@caption Aadress

	@property fake_address_postal_code type=textbox
	@caption Postiindeks

	@property fake_address_city type=textbox
	@caption Linn

	@property fake_address_city_relp type=relpicker reltype=RELTYPE_FAKE_CITY automatic=1
	@caption Linn

	@property fake_address_county type=textbox
	@caption Maakond

	@property fake_address_county_relp type=relpicker reltype=RELTYPE_FAKE_COUNTY automatic=1
	@caption Maakond

	@property fake_address_country type=textbox
	@caption Riik

	@property fake_address_country_relp type=relpicker reltype=RELTYPE_FAKE_COUNTRY automatic=1
	@caption Riik

	@property description_doc type=popup_search clid=CL_DOCUMENT style=relpicker store=no reltype=RELTYPE_DESCRIPTION
	@caption Lisakirjelduse dokument

	@property insurance_title type=text subtitle=1
	@caption Kindlustus

	@property insurance_expires type=datepicker time=0 table=objects field=meta method=serialize default=-1
	@caption Kindlustus aegub

	@property insurance_status type=text store=no
	@caption Kindlustus

	@property insurance_certificate type=releditor reltype=RELTYPE_INSURANCE_CERT_FILE rel_id=first method=serialize field=meta table=objects props=file
	@caption Lisa kindlustust t&otilde;endav dokument

	@property insurance_certificate_view type=releditor reltype=RELTYPE_INSURANCE_CERT_FILE rel_id=first store=no props=filename
	@caption Kindlustust t&otilde;endav dokument

	@property tax_clearance_title type=text subtitle=1
	@caption Maksuinfo

	@property tax_clearance_expires type=datepicker time=0 table=objects field=meta method=serialize default=-1
	@caption Maksuv&otilde;la puudumise t&otilde;end aegub

	@property tax_clearance_status type=text store=no
	@caption Maksuv&otilde;la puudumise t&otilde;endi staatus

	@property tax_clearance_certificate type=releditor reltype=RELTYPE_TAX_CLEARANCE_FILE rel_id=first method=serialize field=meta table=objects props=file
	@caption Lisa maksuv&otilde;la puudumise t&otilde;end

	@property tax_clearance_certificate_view type=releditor reltype=RELTYPE_TAX_CLEARANCE_FILE rel_id=first store=no props=filename
	@caption Maksuv&otilde;la puudumise t&otilde;end

	@property card_number type=textbox table=objects field=meta method=serialize
	@caption Card number

	@property insurance_title2 type=text subtitle=1
	@caption Insurance

	@property insurance_tb type=toolbar no_caption=1 store=no
	@caption Insurance toolbar

	@property insurance_table type=table no_caption=1 store=no
	@caption Insurance table

	@property external_links type=releditor reltype=RELTYPE_EXTERNAL_LINKS mode=manager2 props=name,url table_fields=name,url
	@caption V&auml;lised lingid

------ Yldine - staatused grupp
@default group=statuses

	@property statuses_tb type=toolbar store=no no_caption=1

	@layout statuses_split type=hbox width=30%:70%

		@layout statuses_tree_box type=vbox parent=statuses_split closeable=1 area_caption=Staatused

			@property statuses_tree parent=statuses_tree_box type=treeview store=no no_caption=1

		@layout statuses_tbl_box type=vbox parent=statuses_split

			@property statuses_set_tbl parent=statuses_tbl_box type=table store=no no_caption=1

			@property statuses_tbl parent=statuses_tbl_box type=table store=no no_caption=1


------ Yldine - kasutajate seaded grupp
@default group=user_settings

	@property do_create_users type=checkbox ch_value=1 table=objects field=meta method=serialize group=user_settings
	@caption Kas isikud on kasutajad

	@property use_only_wr_workers type=checkbox ch_value=1 table=objects field=meta method=serialize group=user_settings
	@caption Kasuta ainult t&ouml;&ouml;suhet omavaid isikuid

	@property server_folder type=server_folder_selector table=objects field=meta method=serialize group=user_settings
	@caption Kataloog serveris, kus asuvad failid

	@property all_action_rows type=checkbox ch_value=1  table=objects field=meta method=serialize
	@caption Kuva Tegevused vaates k&otilde;iki ridu

	@property document_source_toolbar type=toolbar store=no no_caption=1
	@caption Organisatsiooni dokumentide toolbar

	@property document_source_list type=table store=no
	@caption Organisatsiooni dokumentide asukohad


--------------------------------------
// employees view


@default group=employees_management
	@property add_existing_employee_oid type=hidden datatype=int store=no
	@property es_c type=hidden store=request

	@property hrm_toolbar type=toolbar no_caption=1 store=no
	@caption T&ouml;&ouml;tajatehalduse tegevused

	@layout hrm_main_container type=hbox width=20%:80%
		@layout hrm_query_container type=vbox parent=hrm_main_container

			@layout hrm_tree_container type=vbox parent=hrm_query_container closeable=1 area_caption=Organisatsiooni&nbsp;struktuur
				@property organization_structure_tree type=treeview store=no parent=hrm_tree_container no_caption=1
				@caption Organisatsiooni struktuur

			@layout hrm_search_container type=vbox parent=hrm_query_container closeable=1 area_caption=Otsing
				@layout search_params_container type=vbox parent=hrm_search_container

					@property es_n type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Nimi

					@property es_s type=chooser orient=vertical store=request parent=search_params_container no_caption=1
					@caption T&ouml;&ouml;suhte staatus


					@property es_a type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Aadress

					@property es_e type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption E-Post

					@property es_agefrom type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Vanus alates

					@property es_ageto type=textbox size=30 store=request parent=search_params_container captionside=top
					@caption Vanus kuni

					@property es_g type=chooser orient=vertical store=request parent=search_params_container no_caption=1
					@caption T&ouml;&ouml;taja sugu

				@layout search_submit_container type=hbox parent=hrm_search_container

					@property es_sbt type=submit size=15 store=no parent=search_submit_container no_caption=1
					@caption Otsi

		@layout hrm_information_container type=vbox parent=hrm_main_container
			@layout unit_list_container type=vbox parent=hrm_information_container closeable=1 area_caption=&Uuml;ksused no_padding=1 default_state=closed
				@property organizational_units_table type=table store=no no_caption=1 parent=unit_list_container
				@caption &Uuml;ksused

			@layout profession_list_container type=vbox parent=hrm_information_container closeable=1 area_caption=Ametid no_padding=1 default_state=closed
				@property professions_table type=table store=no no_caption=1 parent=profession_list_container
				@caption Ametid

			@layout employees_list_container type=vbox parent=hrm_information_container area_caption=T&ouml;&ouml;tajad closeable=1 no_padding=1
				@property employees_table type=table store=no parent=employees_list_container no_caption=1
				@caption T&ouml;&ouml;tajad



-----------------------------------------

@default group=cedit
	@property cedit_toolbar type=toolbar store=no no_caption=1
	@property contact_desc_text type=text store=no no_caption=1
	@caption Kontaktandmed
		@layout ceditphf type=hbox width=50%:50%

			@layout cedit_phone type=vbox parent=ceditphf closeable=1 area_caption=Telefonid
				@property cedit_phone_tbl type=table no_caption=1 parent=cedit_phone

			@layout cedit_fax type=vbox parent=ceditphf closeable=1 area_caption=Faksid
				@property cedit_telefax_tbl type=table no_caption=1 parent=cedit_fax




		@layout ceditemlurl type=hbox width=50%:50%
			@layout cedit_email type=vbox parent=ceditemlurl closeable=1 area_caption=E-mail
				@property cedit_email_tbl type=table store=no no_caption=1 parent=cedit_email

			@layout cedit_url type=vbox parent=ceditemlurl closeable=1 area_caption=URL
				@property cedit_url_tbl type=table store=no no_caption=1 parent=cedit_url

		@layout ceditbank type=vbox closeable=1 area_caption=Pangaarved
			@property cedit_bank_account_tbl type=table store=no no_caption=1 parent=ceditbank

		@layout ceditadr type=vbox closeable=1 area_caption=Aadressid

			@property address_toolbar type=toolbar store=no no_caption=1 parent=ceditadr

			@property cedit_adr_tbl type=table store=no no_caption=1 parent=ceditadr

	@layout cedit_layout_other type=vbox area_caption=Andmed closeable=1
		@layout ce_oth_split type=hbox parent=cedit_layout_other
			@layout ce_other_top type=vbox parent=ce_oth_split

				@property contact type=relpicker reltype=RELTYPE_ADDRESS_ALT table=kliendibaas_firma parent=ce_other_top captionside=top
				@caption Vaikimisi aadress

				@property receptionist_name type=textbox field=meta method=serialize parent=ce_other_top captionside=top
				@caption Telefoni v&otilde;tab vastu

				@property bill_due_days type=textbox size=5 table=kliendibaas_firma field=aw_bill_due_days parent=ce_other_top captionside=top
				@caption Makset&auml;htaeg p&auml;evades

			@layout ce_other_bot type=vbox parent=ce_oth_split

				@property currency type=relpicker reltype=RELTYPE_CURRENCY table=kliendibaas_firma field=aw_currency parent=ce_other_bot captionside=top no_edit=1
				@caption Valuuta

				@property round type=textbox method=serialize field=meta parent=ce_other_bot captionside=top
				@caption &Uuml;marda

				@property language type=relpicker reltype=RELTYPE_LANGUAGE table=kliendibaas_firma parent=ce_other_bot captionside=top no_edit=1
				@caption Vaikimisi keel

- T88tajad vaatesse
V6imalus m22rata, kes on volitatud isikud ja volituse alus. T88taja nime j2rele on v6imalik panna m2rkeruut tulpa &#8220;Volitatud&#8221;. Selle m2rkimisel avaneb uus aken, kus kysitakse volituse alust (Objektityyp Volitus). Volitus kehtib kolmese seosena (Meie firma, klientfirma, volitatav isik).

- Kontaktandmetesse seos: Keel
Vaikimisi eesti keel. Keelele peab saama m22rata, milline on systeemi default. Vaikimisi v22rtus Arve-saatelehel

@property phone_id type=hidden table=kliendibaas_firma parent=cedit_layout_other no_caption=1
@property telefax_id type=hidden table=kliendibaas_firma parent=cedit_layout_other no_caption=1
@property url_id type=hidden table=kliendibaas_firma parent=cedit_layout_other no_caption=1
@property email_id type=hidden table=kliendibaas_firma parent=cedit_layout_other no_caption=1
@property aw_bank_account type=hidden table=kliendibaas_firma parent=cedit_layout_other no_caption=1

@property balance type=hidden table=aw_account_balances field=aw_balance

@default group=owners

	@property owners_toolbar type=toolbar store=no no_caption=1

	@property owners_table type=table store=no no_caption=1

@default group=personal_offers
-------------- PERSONALI PROPERTID ---------------

	@layout personal_toolbar type=hbox
		@property personal_offers_toolbar type=toolbar store=no no_caption=1 parent=personal_toolbar

	@layout personal_tree_table type=hbox  width=20%:80%

		@layout personal_hbox_tree type=vbox parent=personal_tree_table closeable=1 area_caption=Struktuur
			@property unit_listing_tree_personal type=treeview no_caption=1 store=no parent=personal_hbox_tree

		@layout personal_hbox_table type=vbox parent=personal_tree_table
			@property personal_offers_table type=table no_caption=1 parent=personal_hbox_table

@default group=personal_candits

	@layout personal_toolbar_cand type=hbox
		@property personal_candidates_toolbar type=toolbar store=no no_caption=1 parent=personal_toolbar_cand

	@layout personal_tree_table_cand type=hbox width=20%:80%
		@layout personal_hbox_tree_cand type=vbox parent=personal_tree_table_cand closeable=1 area_caption=Struktuur
			@property unit_listing_tree_candidates type=treeview no_caption=1 store=no parent=personal_hbox_tree_cand

		@layout personal_hbox_table_cand type=vbox parent=personal_tree_table_cand
			@property personal_candidates_table type=table no_caption=1 parent=personal_hbox_table_cand


---------------------------------------------------

// Customers view
@default group=relorg_s,relorg_b

	@property my_customers_toolbar type=toolbar no_caption=1 store=no
	@caption Kliendivaate tegevused

	@layout my_cust_bot type=hbox width=20%:80%
		@layout tree_search_split type=vbox parent=my_cust_bot

			@property tree_search_split_dummy type=hidden no_caption=1 parent=tree_search_split

			@layout vvoc_customers_tree_left type=vbox parent=tree_search_split closeable=1 area_caption=Kliendivalik
				@property customer_listing_tree type=treeview no_caption=1 parent=vvoc_customers_tree_left
				@caption Kliendikategooriad hierarhiliselt

			@layout vbox_customers_left type=vbox parent=tree_search_split closeable=1 area_caption=Otsing
				@layout vbox_customers_left_top type=vbox parent=vbox_customers_left

					@property cs_n type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Nimi

					@property customer_search_reg type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Reg nr.

					@property customer_search_worker type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption T&ouml;&ouml;taja

					@property customer_search_address type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Aadress

					@property customer_search_city type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Linn/Vald/Alev

					@property customer_search_county type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption Maakond

					@property customer_search_ev type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption &Otilde;iguslik vorm

					@property customer_search_keywords type=textbox size=30 store=no parent=vbox_customers_left_top captionside=top
					@caption M&auml;rks&otilde;nad

					@property customer_search_is_co type=chooser  store=no parent=vbox_customers_left_top multiple=1 no_caption=1
					@caption Organisatsioon

					@property customer_search_cust_mgr type=text size=25 store=no parent=vbox_customers_left_top captionside=top
					@caption Kliendihaldur

					@property customer_rel_creator type=text size=25 store=no parent=vbox_customers_left_top captionside=top
					@caption Kliendisuhte looja

					@property customer_search_cust_grp type=select store=no parent=vbox_customers_left_top captionside=top
					@caption Kliendigrupp

					@property customer_search_insurance_exp type=select store=no parent=vbox_customers_left_top captionside=top
					@caption Kindlustus aegund

					@property customer_search_print_view type=checkbox parent=vbox_customers_left_top store=no captionside=top ch_value=1 no_caption=1
					@caption Printvaade

				@layout vbox_customers_left_search_btn type=hbox parent=vbox_customers_left

					@property cs_sbt type=submit size=15 store=no parent=vbox_customers_left_search_btn no_caption=1
					@caption Otsi

		@layout list_container type=vbox parent=my_cust_bot
			@layout category_list_container type=vbox parent=list_container closeable=1 area_caption="Kategooriad" no_padding=1 default_state=closed
				@property customer_categories_table type=table store=no no_caption=1 parent=category_list_container
				@caption Kliendikategooriad

			@layout customer_list_container type=vbox parent=list_container area_caption="Kliendid" closeable=1 no_padding=1
				@property my_customers_table type=table store=no no_caption=1 parent=customer_list_container
				@caption Kliendid


/// end of customers


---------- ERIPAKKUMISED ---------
@default group=special_offers

	@property special_offers type=releditor reltype=RELTYPE_SPECIAL_OFFERS field=meta method=serialize mode=manager props=name,comment,ord,status,valid_from,valid_to table_fields=name,ord table_edit_fields=ord table=objects direct_links=1 override_parent=this
	@caption Eripakkumised
---------- END ERIPAKKUMISED ---------

---------- PILDID ---------
@default group=org_images
	@property images type=releditor reltype=RELTYPE_IMAGE field=meta method=serialize mode=manager props=name,ord,status,file,file2,new_w,new_h,new_w_big,new_h_big,comment,cfgform table_fields=name,ord table_edit_fields=ord table=objects
	@caption Pildid

	@property images_2 type=multifile_upload reltype=RELTYPE_IMAGE image=1 store=no
	@caption Pildid
---------- END PILDID ---------


---------- PROJEKTID ----------------------------
@default group=org_projects_archive

	@property org_proj_tb type=toolbar no_caption=1 group=my_projects
	@property org_proj_arh_tb type=toolbar no_caption=1 group=org_projects_archive

	@layout projects_main type=hbox width=20%:80%
		@layout projects_tree type=vbox parent=projects_main closeable=1 area_caption=Otsing
			@property projects_listing_tree type=treeview no_caption=1 parent=projects_tree no_caption=1

			@layout all_proj_search_b type=vbox parent=projects_tree

				@layout all_proj_search_b_top type=vbox parent=all_proj_search_b

					@property all_proj_search_name type=textbox store=no parent=all_proj_search_b_top size=33 captionside=top
					@caption Projekti nimi

					@property all_proj_search_cust type=objpicker options_callback=crm_company::get_customer_options store=no parent=all_proj_search_b_top size=33 captionside=top
					@caption Klient

					@property all_proj_search_part type=text size=28 parent=all_proj_search_b_top store=no captionside=top
					@caption Osaleja

					@property all_proj_search_state type=chooser store=no parent=all_proj_search_b_top  captionside=top
					@caption Staatus

					@property all_proj_search_code type=textbox store=no parent=all_proj_search_b_top size=33 captionside=top
					@caption Projekti kood

					@property all_proj_search_arh_code type=textbox store=no parent=all_proj_search_b_top size=33 captionside=top
					@caption Arhiveerimistunnus

					@property all_proj_search_contact_person type=textbox store=no parent=all_proj_search_b_top size=33 captionside=top
					@caption Projekti kontaktisik

					@property all_proj_search_task_name type=textbox store=no parent=all_proj_search_b_top size=33 captionside=top
					@caption &Uuml;lesande nimi

				@layout all_proj_search_b_dl type=vbox parent=all_proj_search_b

					@property all_proj_search_dl_from type=datepicker time=0 store=no parent=all_proj_search_b_dl  captionside=top
					@caption T&auml;htaeg alates

					@property all_proj_search_dl_to type=datepicker time=0 store=no parent=all_proj_search_b_dl  captionside=top
					@caption T&auml;htaeg kuni

				@layout all_proj_search_b_end type=vbox parent=all_proj_search_b

					@property all_proj_search_end_from type=datepicker time=0 store=no parent=all_proj_search_b_end  captionside=top
					@caption L&otilde;pp alates

					@property all_proj_search_end_to type=datepicker time=0 store=no parent=all_proj_search_b_end  captionside=top
					@caption L&otilde;pp kuni


			@layout all_proj_search_but_row type=hbox parent=projects_tree

				@property all_proj_search_sbt type=submit  parent=all_proj_search_but_row no_caption=1
				@caption Otsi

				@property all_proj_search_change_mode_sbt type=submit  parent=all_proj_search_but_row no_caption=1
				@caption Otsi ja muuda vormi

		@layout projects_table type=vbox parent=projects_main
			@property projects_listing_table type=table no_caption=1 parent=projects_table no_caption=1


@default group=my_projects

	@layout my_proj type=hbox width=20%:80%

		@layout my_proj_search_group type=vbox parent=my_proj

		@layout my_proj_tree type=vbox parent=my_proj_search_group closeable=1 area_caption=Puu
			@property project_tree type=treeview store=no no_caption=1 parent=my_proj_tree
			@caption Projektide puu

		@layout my_proj_search type=vbox parent=my_proj_search_group closeable=1 area_caption=Otsing

			@layout my_proj_search_b type=vbox parent=my_proj_search

				@layout my_proj_search_b_top type=vbox parent=my_proj_search_b

					@property proj_search_name type=textbox store=no parent=my_proj_search_b_top size=18 captionside=top
					@caption Projekti nimi

					@property proj_search_cust type=textbox store=no parent=my_proj_search_b_top size=18 captionside=top
					@caption Klient

					@property proj_search_part type=text size=18 parent=my_proj_search_b_top store=no captionside=top
					@caption Osaleja

					@property proj_search_state type=chooser store=no parent=my_proj_search_b_top  captionside=top
					@caption Staatus

					@property proj_search_code type=textbox store=no parent=my_proj_search_b_top size=18 captionside=top
					@caption Projekti kood

					@property proj_search_contact_person type=textbox store=no parent=my_proj_search_b_top size=18 captionside=top
					@caption Projekti kontaktisik

					@property proj_search_task_name type=textbox store=no parent=my_proj_search_b_top size=18 captionside=top
					@caption &Uuml;lesande nimi

				@layout my_proj_search_b_dl type=vbox parent=my_proj_search_b

					@property proj_search_dl_from type=datepicker time=0 store=no parent=my_proj_search_b_dl  captionside=top
					@caption T&auml;htaeg alates

					@property proj_search_dl_to type=datepicker time=0 store=no parent=my_proj_search_b_dl  captionside=top
					@caption T&auml;htaeg kuni


			@layout my_proj_search_but_row type=hbox parent=my_proj_search

				@property proj_search_sbt type=submit  parent=my_proj_search_but_row no_caption=1
				@caption Otsi

				@property proj_search_change_mode_sbt type=submit  parent=my_proj_search_but_row no_caption=1
				@caption Otsi ja muuda vormi

		@layout my_proj_qadd type=vbox parent=my_proj_search_group closeable=1 area_caption=Kiirlisamine

			@property my_proj_qadd type=quick_add store=no no_caption=1 clid=CL_PROJECT props=name,code,priority parent=my_proj_qadd

		@property my_projects type=table no_caption=1 store=no parent=my_proj

@default group=my_reports,all_reports

	@property report_list type=table store=no no_caption=1
	@caption P&auml;eva raportid

@default group=documents_all,documents_all_browse

	@property docs_tb type=toolbar no_caption=1

	@layout docs_lt type=hbox width=20%:80%

		@layout docs_left type=vbox parent=docs_lt

			@layout docs_left_tree type=vbox parent=docs_left closeable=1 area_caption=Dokumendid

				@property docs_tree type=treeview parent=docs_left_tree no_caption=1

			@layout docs_left_search type=vbox parent=docs_left closeable=1 area_caption=Otsing

				@layout docs_s_f type=vbox parent=docs_left_search

					@property docs_s_name type=textbox size=30 store=no captionside=top parent=docs_s_f
					@caption Nimetus

					@property docs_s_comment type=textbox size=30 store=no captionside=top parent=docs_s_f
					@caption Kommentaar

					@property docs_s_type type=select store=no captionside=top parent=docs_s_f
					@caption Liik

					@property docs_s_task type=textbox size=30 store=no captionside=top parent=docs_s_f
					@caption Toimetus

					@property docs_s_user type=textbox size=30 store=no captionside=top parent=docs_s_f
					@caption Tegija

					@property docs_s_customer type=textbox size=30 store=no captionside=top parent=docs_s_f
					@caption Klient

					@property docs_s_created_after type=datepicker time=0 store=no captionside=top parent=docs_s_f
					@caption Loodud peale

				@layout docs_s_but_row type=hbox parent=docs_left_search

					@property docs_s_sbt type=submit store=no no_caption=1 parent=docs_s_but_row
					@caption Otsi

					@property docs_s_clear type=submit store=no no_caption=1 parent=docs_s_but_row
					@caption T&uuml;hista otsing

				@property docs_empty2 type=hidden store=no no_caption=1 parent=docs_left_search

			@property docs_empty type=hidden store=no no_caption=1 parent=docs_left

		@property docs_tbl type=table store=no no_caption=1 parent=docs_lt

@default group=documents_news

	@property docs_news_tb type=toolbar no_caption=1

	@layout docs_news_lt type=hbox width=20%:80%

		@layout docs_news_left type=vbox parent=docs_news_lt closeable=1 area_caption=Otsing

			@property dn_s_name type=textbox size=30 store=no captionside=top parent=docs_news_left
			@caption Nimi

			@property dn_s_lead type=textbox size=30 store=no captionside=top parent=docs_news_left
			@caption Lead

			@property dn_s_content type=textbox size=30 store=no captionside=top parent=docs_news_left
			@caption Sisu

			@property dn_s_sbt type=submit size=30 store=no captionside=top parent=docs_news_left no_caption=1
			@caption Otsi

		@property dn_res type=table no_caption=1 store=no parent=docs_news_lt
		@caption Uudised

@default group=documents_lmod

	@property documents_lmod type=table store=no no_caption=1

@default group=bills_create

	@property bill_tb type=toolbar store=no no_caption=1

	@layout billable type=hbox width=30%:70%

		@layout billable_search type=vbox parent=billable closeable=1 area_caption=Arvele&nbsp;minevate&nbsp;ridade&nbsp;Otsing

			@property billable_start type=datepicker time=0 store=no default=-1 parent=billable_search
			@caption Alates

			@property billable_end type=datepicker time=0 store=no default=-1 parent=billable_search
			@caption Kuni

			@property billable_search_button type=submit store=no parent=billable_search captionside=top no_caption=1
			@caption Otsi

	@layout billable_table type=vbox parent=billable closeable=1
		@property bill_proj_list type=table store=no no_caption=1 parent=billable_table
		@property bill_task_list type=table store=no no_caption=1 parent=billable_table


@default group=invoice_templates

	@property bills_mon_tb type=toolbar no_caption=1 store=no

	@layout templates_container type=hbox width=20%:80%
		@layout template_folders type=vbox parent=templates_container closeable=0 area_caption=Arvemallide&nbsp;kaustad
		@layout templates_list_box type=vbox parent=templates_container closeable=0 no_padding=1
		@layout folders_list_box type=vbox parent=templates_list_box closeable=1 no_padding=1 default_state=closed area_caption=Arvemallide&nbsp;kaustad
			@property invoice_template_folders type=treeview no_caption=1 store=no parent=template_folders
			@property invoice_folders_list type=table store=no no_caption=1 parent=folders_list_box
			@property invoice_templates_list type=table store=no no_caption=1 parent=templates_list_box



@default group=bills_search

	@property bs_tb type=toolbar no_caption=1



@default group=bill_payments

	@property bill_payments_tb type=toolbar store=no no_caption=1
	@caption Laekumiste toolbar

	@layout bills_payments_box type=hbox width=20%:80%

		@layout bills_payments_s type=vbox parent=bills_payments_box closeable=1 area_caption=Otsing

			@property bill_payments_cust type=textbox size=30 store=no parent=bills_payments_s captionside=top
			@caption Klient

			@property bill_payments_bill_no type=textbox size=15 store=no parent=bills_payments_s captionside=top
			@caption Arve nr alates
'
			@property bill_payments_bill_to type=textbox size=15 store=no parent=bills_payments_s captionside=top
			@caption Arve nr kuni

			@property bill_payments_from type=datepicker time=0 store=no parent=bills_payments_s captionside=top
			@caption Laekunud alates

			@property bill_payments_to type=datepicker time=0 store=no parent=bills_payments_s captionside=top
			@caption Laekunud kuni

			@property bill_payments_client_mgr type=text store=no parent=bills_payments_s captionside=top
			@caption Kliendihaldur

			@property bill_payments_search type=submit store=no parent=bills_payments_s captionside=top no_caption=1
			@caption Otsi

		@layout bills_payments_t type=vbox parent=bills_payments_box closeable=1 area_caption=Laekumiste&nbsp;tabel


	@property bill_payments_table type=table no_caption=1 store=no parent=bills_payments_t
	@caption Laekumiste tabel

@default group=bills_list

	@property bills_tb type=toolbar no_caption=1 store=no

	@layout bills_list_box type=hbox width=20%:80%

		@layout bills_list_l type=vbox parent=bills_list_box closeable=1

		@layout bills_list_u type=vbox parent=bills_list_l closeable=1 area_caption=Arved

			@property bills_tree type=treeview store=no no_caption=1 parent=bills_list_u group=bills_list
			@caption Arvete puu

		@layout bills_list_stats type=vbox parent=bills_list_l closeable=1 area_caption=Arve&nbsp;staatus

			@property bills_stats_tree type=treeview store=no no_caption=1 parent=bills_list_stats group=bills_list
			@caption Arvete puu

		@layout bills_list_time type=vbox parent=bills_list_l closeable=1 area_caption=Periood

			@property bills_time_tree type=treeview store=no no_caption=1 parent=bills_list_time group=bills_list
			@caption Arvete puu

		@layout bills_list_s type=vbox parent=bills_list_l closeable=1 area_caption=Otsing


			otsing Kliendi, arve nr,  esitamise ajavahemiku,
			kliendihalduri, koostamisel/makstud/maksmata j&auml;rgi


			@property bill_s_cust type=textbox size=30 store=no parent=bills_list_s captionside=top group=bills_list
			@caption Klient

			@property bill_s_bill_no type=textbox size=15 store=no parent=bills_list_s captionside=top group=bills_list
			@caption Arve nr alates

			@property bill_s_bill_to type=textbox size=15 store=no parent=bills_list_s captionside=top group=bills_list
			@caption Arve nr kuni

			@property bill_s_from type=datepicker time=0 store=no parent=bills_list_s captionside=top group=bills_list
			@caption Esitatud alates

			@property bill_s_to type=datepicker time=0 store=no parent=bills_list_s captionside=top group=bills_list
			@caption Esitatud kuni

			@property bill_s_client_mgr type=text store=no parent=bills_list_s captionside=top group=bills_list
			@caption Kliendihaldur

			@property bill_s_status type=select store=no parent=bills_list_s captionside=top group=bills_list
			@caption Staatus

			@property bill_s_with_tax type=chooser store=no parent=bills_list_s captionside=top no_caption=1
			@caption K&auml;ibemaksuta/K&auml;ibemaksuga

			@property show_bill_balance type=checkbox parent=bills_list_s store=no captionside=top ch_value=1
			@caption Kuva arve saldot

			@property currency_grouping type=checkbox parent=bills_list_s store=no captionside=top ch_value=1
			@caption Kuva valuutade l&otilde;ikes

			@property bill_s_search type=submit store=no parent=bills_list_s captionside=top no_caption=1 group=bills_list
			@caption Otsi

		@property bills_list type=table store=no no_caption=1 parent=bills_list_box group=bills_list

@default group=bills_quality

	@property quality_tb type=toolbar no_caption=1 store=no
	@layout quality_list_box type=hbox width=20%:80%
		@layout quality_list type=vbox parent=quality_list_box closeable=1
			@property quality_tree type=treeview store=no no_caption=1 parent=quality_list
			@caption Kvaliteedi puu
		@layout quality_list_s type=vbox parent=quality_list_box closeable=1
			@property quality_list type=table store=no no_caption=1 parent=quality_list_s

@default group=bills_quality

	@property quality_tb type=toolbar no_caption=1 store=no
	@layout quality_list_box type=hbox width=20%:80%
		@layout quality_list type=vbox parent=quality_list_box closeable=1
			@property quality_tree type=treeview store=no no_caption=1 parent=quality_list
			@caption Kvaliteedi puu
		@layout quality_list_s type=vbox parent=quality_list_box closeable=1
			@property quality_list type=table store=no no_caption=1 parent=quality_list_s

@default group=my_tasks,meetings,calls,ovrv_offers,all_actions,ovrv_mails,documents_all_manage,bugs

	@property my_tasks_tb type=toolbar store=no no_caption=1

	@layout my_tasks type=hbox width=20%:80%

		@layout all_act_search type=vbox parent=my_tasks closeable=1

			@layout tasks_tree_layout type=vbox parent=all_act_search closeable=1 area_caption=Tegevused&nbsp;puu&nbsp;kujul group=all_actions

				@property tasks_tree type=treeview store=no no_caption=1 parent=tasks_tree_layout group=all_actions
				@caption Tegevuste puu

			@layout tasks_tree_type_layout type=vbox parent=all_act_search closeable=1 area_caption=Tegevuse&nbsp;t&uuml;&uuml;bid group=all_actions

				@property tasks_type_tree type=treeview store=no no_caption=1 parent=tasks_tree_type_layout group=all_actions
				@caption Tegevuste puu t&uuml;&uuml;pide kaupa

			@layout tasks_tree_time_layout type=vbox parent=all_act_search closeable=1 area_caption=Aja&nbsp;filter group=all_actions

				@property tasks_time_tree type=treeview store=no no_caption=1 parent=tasks_tree_time_layout group=all_actions
				@caption Tegevuste puu aja kaupa


			@layout act_s_dl_layout_top type=vbox parent=all_act_search area_caption=Otsing closeable=1

			@property act_s_cust type=textbox size=18 parent=act_s_dl_layout_top store=no captionside=top group=my_tasks,meetings,calls,ovrv_offers,all_actions,bills_search,documents_all_manage,bugs
			@caption Klient

			@property act_s_part type=text size=30 parent=act_s_dl_layout_top store=no captionside=top group=my_tasks,meetings,calls,ovrv_offers,all_actions,bills_search,documents_all_manage,bugs
			@caption Osaleja

			@property act_s_cal_name type=text size=18 parent=act_s_dl_layout_top store=no captionside=top group=my_tasks,meetings,calls,ovrv_offers,all_actions,bills_search,documents_all_manage
			@caption Kalender

			@property act_s_task_name type=textbox size=18 parent=act_s_dl_layout_top store=no captionside=top
			@caption Tegevuse nimi

			@property act_s_task_content type=textbox size=18 parent=act_s_dl_layout_top store=no captionside=top
			@caption Tegevuse sisu

			@property act_s_mail_name type=textbox size=18 parent=act_s_dl_layout_top store=no captionside=top
			@caption Maili subjekt

			@property act_s_mail_content type=textbox size=18 parent=act_s_dl_layout_top store=no captionside=top
			@caption Maili sisu

			@property act_s_code type=textbox size=18 parent=act_s_dl_layout_top store=no captionside=top group=my_tasks,meetings,calls,ovrv_offers,all_actions,documents_all_manage
			@caption Toimetuse kood

			@property act_s_proj_name type=textbox size=18 parent=act_s_dl_layout_top store=no captionside=top group=my_tasks,meetings,calls,ovrv_offers,all_actions,bills_search,documents_all_manage,bugs
			@caption Projekti nimi

			@property act_s_dl_from type=datepicker time=0 store=no parent=act_s_dl_layout_top captionside=top group=my_tasks,meetings,calls,ovrv_offers,all_actions,bills_search,documents_all_manage,bugs
			@caption T&auml;htaeg alates

			@property act_s_dl_to type=datepicker time=0 store=no parent=act_s_dl_layout_top captionside=top group=my_tasks,meetings,calls,ovrv_offers,all_actions,bills_search,documents_all_manage,bugs
			@caption T&auml;htaeg kuni

			@property act_s_status type=chooser parent=act_s_dl_layout_top store=no captionside=top
			@caption Staatus

			@property act_s_print_view type=checkbox parent=act_s_dl_layout_top store=no captionside=top ch_value=1
			@caption Printvaade

			@property act_s_sbt type=submit  parent=act_s_dl_layout_top no_caption=1 group=my_tasks,meetings,calls,ovrv_offers,all_actions,bills_search,ovrv_mails,documents_all_manage,bugs
			@caption Otsi

		@property my_tasks type=table store=no no_caption=1 parent=my_tasks group=my_tasks,meetings,calls,ovrv_offers,all_actions,bills_search,ovrv_mails,documents_all_manage,bugs
		@property my_tasks_cal type=calendar store=no no_caption=1 parent=my_tasks

@default group=ovrv_email

	@layout mail_main type=hbox width=20%:80%

		@property mail_tb type=toolbar store=no no_caption=1

		@layout mail_search type=vbox closeable=1 area_caption=Otsing parent=mail_main

			@property mail_s_subj type=textbox store=no parent=mail_search size=24
			@caption Kirja teema

			@property mail_s_body type=textbox store=no parent=mail_search size=24
			@caption Kirja sisu

			@property mail_s_to type=textbox store=no parent=mail_search size=24
			@caption Kellele

			@property mail_s_submit type=submit parent=mail_search no_caption=1
			@caption Otsi

		@layout mail_tbl_box type=vbox parent=mail_main

			@property mail_tbl type=table store=no no_caption=1 parent=mail_tbl_box


@default group=stats_stats

	@property stats_stats_tb type=toolbar no_caption=1 store=no

	@layout stats_list_box type=hbox width=20%:80%

		@layout stats_list_l type=vbox parent=stats_list_box closeable=1

			@layout stats_list_u type=vbox parent=stats_list_l closeable=1 area_caption=Aruanded

				@property stats_tree type=treeview store=no no_caption=1 parent=stats_list_u
				@caption Aruannete puu

			@layout stats_list_s type=vbox parent=stats_list_l closeable=1 area_caption=Ajavahemik

				@property stats_stats_time_sel type=select store=no parent=stats_list_s captionside=top
				@caption Ajavahemik

				@property stats_stats_from type=datepicker time=0 store=no parent=stats_list_s captionside=top
				@caption Alates

				@property stats_stats_to type=datepicker time=0 store=no parent=stats_list_s captionside=top
				@caption Kuni

				@property stats_stats_search type=submit store=no parent=stats_list_s captionside=top no_caption=1
				@caption Otsi

		@layout stats_list_r type=vbox parent=stats_list_box closeable=1

			@layout data_r_charts type=hbox parent=stats_list_r width=50%:50% closeable=1 area_caption=Graafikud
				@layout chart1 parent=data_r_charts type=vbox
					@property status_chart type=google_chart no_caption=1 parent=chart1 store=no
				@layout chart2 parent=data_r_charts type=vbox
					@property money_chart type=google_chart no_caption=1 parent=chart2 store=no
			@layout stats_table type=vbox parent=stats_list_r closeable=1
				@property stats_table type=table store=no no_caption=1 parent=stats_table

@default group=stats_annual_reports

	@property stats_annual_reports_tbl type=table store=no no_caption=1

@default group=stats_s

	@property stats_s_toolbar type=toolbar store=no no_caption=1

	@property stats_s_cust type=textbox store=no
	@caption Klient

	@property stats_s_cust_type type=chooser store=no
	@caption Kliendi t&uuml;&uuml;p

	@property stats_s_proj type=textbox store=no
	@caption Projekt

	@property stats_s_worker type=textbox store=no
	@caption T&ouml;&ouml;taja

	@property stats_s_worker_sel type=select multiple=1 store=no
	@caption T&ouml;&ouml;taja

	@property project_mgr type=select store=no
	@caption Projektijuht

	@property stats_s_from type=datepicker time=0 store=no
	@caption Alates

	@property stats_s_to type=datepicker time=0 store=no
	@caption Kuni

	@property stats_s_time_sel type=select store=no
	@caption Ajavahemik

	@property stats_s_state type=select store=no
	@caption Toimetuse staatus

	@property stats_s_bill_state type=select store=no
	@caption Arve staatus

	@property stats_s_only_billable type=checkbox ch_value=1 store=no
	@caption Arvele minevad tunnid ainult

	@property stats_s_area type=select store=no
	@caption Valdkond

	@property stats_s_res_type type=select store=no
	@caption Tulemused

	@property stats_s_group_by_client type=checkbox ch_value=1 store=no
	@caption Grupeeri kliendi alusel

	@property stats_s_group_by_project type=checkbox ch_value=1 store=no
	@caption Grupeeri projekti alusel

	@property stats_s_group_by_task type=checkbox ch_value=1 store=no
	@caption Grupeeri tegevuse alusel


	@property stats_s_sbt type=submit store=no
	@caption Otsi

	@property stats_s_res type=table store=no no_caption=1
	@caption Tulemused

@default group=stats_view

	@property stats_tb type=toolbar no_caption=1 store=no

	@property stats_list type=table no_caption=1 store=no

@default group=stats_my

	@property my_stats_tb type=toolbar no_caption=1 store=no

	@property my_stats_s_from type=datepicker time=0 store=no
	@caption Alates

	@property my_stats_s_to type=datepicker time=0 store=no
	@caption Kuni

	@property my_stats_s_time_sel type=select store=no
	@caption Ajavahemik

	@property my_stats_s_cust type=textbox store=no
	@caption Klient

	@property my_stats_s_type type=select store=no
	@caption Vaade

	@property my_stats_s_show type=submit no_caption=1
	@caption N&auml;ita

	@property my_stats type=text store=no no_caption=1

@default group=quick_view

	@property qv_cust_inf type=text store=no no_caption=1
	@property qv_t type=table store=no no_caption=1

@default group=resources

	@property res_tb type=toolbar no_caption=1

	@layout res_lt type=hbox width=20%:80%

		@layout res_lt_tree type=hbox parent=res_lt closeable=1 area_caption=Ressurssid
			@property res_tree type=treeview no_caption=1 parent=res_lt_tree

		@layout res_lt_tbl type=hbox parent=res_lt
			@property res_tbl type=table no_caption=1 parent=res_lt_tbl

@default group=transl

	@property transl type=callback callback=callback_get_transl store=no
	@caption T&otilde;lgi

@default group=documents_forum

	@property forum type=text store=no no_caption=1
	@caption Foorumi sisu

@default group=documents_polls

	@property polls_tb type=toolbar store=no no_caption=1
	@property polls_tbl type=table store=no no_caption=1

@default group=service_types

	@property stypes_tb type=toolbar store=no no_caption=1

	@layout stypes_split type=hbox width=30%:70%

		@layout stypes_tree_box type=vbox parent=stypes_split closeable=1 area_caption=Teenuse&nbsp;liikide&nbsp;kategooriad

			@property stypes_tree type=treeview store=no no_caption=1 parent=stypes_tree_box

		@property stypes_tbl type=table store=no no_caption=1 parent=stypes_split

@default group=my_view

	@property my_view type=text store=no no_caption=1

@default group=comments

	@property comment_history type=hidden field=meta method=serialize

    @property comments_display type=text store=no
	@caption Sisestatud kommentaarid

	@property last_com_type type=text store=no
	@caption Viimase kommentaari t&uuml;&uuml;p

	@property com_statistics type=text store=no
	@caption Kommentaaride statistika

    @property comments_title type=text store=no subtitle=1
	@caption Lisa kommentaar

	@property comment_text type=textarea store=no
	@caption Kommentaar

    @property comment_type type=chooser store=no
	@caption Kommentaari t&uuml;&uuml;p

@default group=ext_sys

	@property ext_sys_t type=table store=no no_caption=1

@default group=versions

	@property versions type=version_manager store=no no_caption=1

groupinfo sell_offers caption="M&uuml;&uuml;gipakkumised" parent=documents_all submit=no save=no

@default group=keywords
	@property keywords2 type=keyword_selector field=meta method=serialize group=keywords reltype=RELTYPE_KEYWORD
	@caption V&otilde;tmes&otilde;nad


@default group=activity_stats_types

	@property activity_stats_toolbar type=toolbar store=no no_caption=1
	@caption Tegevuse statistika t&uuml;&uuml;pide toolbar

	@property activity_stats_table type=table store=no no_caption=1
	@caption Tegevuse statistika t&uuml;&uuml;pide tabel

-------------------------------------------------
@groupinfo general_sub caption="&Uuml;ldandmed" parent=general
@groupinfo cedit caption="&Uuml;ldkontaktid" parent=general
@groupinfo owners caption="Omanikud" parent=general
@groupinfo org_sections caption="Tegevus" parent=general
@groupinfo add_info caption="Lisainfo" parent=general
@groupinfo statuses caption="Staatused" parent=general submit=no
@groupinfo special_offers caption="Eripakkumised" submit=no parent=general
@groupinfo comments caption="Kommentaarid" parent=general


@groupinfo employees caption="T&ouml;&ouml;tajad"
	@groupinfo employees_management caption="T&ouml;&ouml;tajad" parent=employees
	@groupinfo personal_offers caption="T&ouml;&ouml;pakkumised" parent=employees submit=no save=no
	@groupinfo personal_candits caption="Kandideerijad" parent=employees submit=no save=no
	@groupinfo resources caption="Ressursid"  submit=no save=no parent=employees
	@groupinfo documents_forum caption="Foorum" submit=no parent=employees
	@groupinfo open_hrs caption="Avamisajad" parent=employees
	@groupinfo user_settings caption="Seaded" parent=employees


@groupinfo contacts caption="Kontaktid"
@groupinfo overview caption="Tegevused" save=no

	@groupinfo all_actions caption="K&otilde;ik" parent=overview submit=no save=no
	@groupinfo my_tasks caption="Toimetused" parent=overview submit=no save=no
	@groupinfo meetings caption="Kohtumised" parent=overview submit=no save=no
	@groupinfo calls caption="K&otilde;ned" parent=overview submit=no save=no
	@groupinfo bugs caption="Bugid" parent=overview submit=no save=no
	@groupinfo ovrv_offers caption="Dokumendihaldus" parent=overview submit=no save=no
	@groupinfo ovrv_email caption="Meilid" parent=overview submit=no save=no
	@groupinfo ovrv_mails caption="Meilikast" parent=overview submit=no save=no
	@groupinfo activity_stats_types caption="Tegevuste statistika t&uuml;&uuml;bid" parent=overview submit=no save=no

@groupinfo projs caption="Projektid"
	@groupinfo my_projects caption="Projektid" parent=projs submit=no
	groupinfo org_projects caption="Projektid" submit=no parent=projs save=no
	@groupinfo org_projects_archive caption="Projektide arhiiv" submit=no parent=projs save=no
	@groupinfo my_reports caption="Minu raportid" submit=no parent=projs save=no
	@groupinfo all_reports caption="K&otilde;ik raportid" submit=no parent=projs save=no

@groupinfo relorg caption="Kliendid" focus=cs_n submit=no
	// @groupinfo relorg_t caption="K&otilde;ik" parent=relorg submit=no submit=no
	@groupinfo relorg_b caption="Ostjad" focus=cs_n parent=relorg submit=no
	@groupinfo relorg_s caption="M&uuml;&uuml;jad" focus=cs_n parent=relorg submit=no


@groupinfo org_images caption="Pildid" submit=yes parent=general

	@groupinfo documents_all caption="Dokumendid" submit=no save=no
		@groupinfo documents_all_browse caption="Dokumendid" parent=documents_all submit=no save=no
		@groupinfo documents_all_manage caption="Haldus" parent=documents_all submit=no save=no

@groupinfo sell_offers_grp_offers caption="M&uuml;&uuml;gipakkumised pakkumiste kaupa" parent=documents_all submit=no save=no
@groupinfo sell_offers_grp_products caption="M&uuml;&uuml;gipakkumised toodete kaupa" parent=documents_all submit=no save=no

@default group=sell_offers_grp_offers
	@property see_all_link type=text no_caption=1
	@property sell_offers type=table store=no no_caption=1

@default group=sell_offers_grp_products
	@property see_all_link2 type=text no_caption=1
	@property sell_offers_prods type=table store=no no_caption=1

groupinfo sell_offers caption="M&uuml;&uuml;gipakkumised" parent=documents_all submit=no save=no

	@groupinfo documents_news caption="Siseuudised" submit=no parent=general submit_method=get save=no
	@groupinfo documents_lmod caption="Viimati muudetud" submit=no parent=general	save=no
	@groupinfo ext_sys caption="Siduss&uuml;steemid" parent=general
	@groupinfo documents_polls caption="Kiirk&uuml;sitlused" submit=no parent=general
	@groupinfo service_types caption="Teenuse liigid" submit=no parent=general

@groupinfo bills caption="Arved" submit=no save=no

	@groupinfo bills_list parent=bills caption="Nimekiri" submit=no save=no
	@groupinfo invoice_templates parent=bills caption="Arvemallid" submit=no save=no
	@groupinfo bills_search parent=bills caption="Otsi toimetusi" submit=no save=no
	@groupinfo bills_create parent=bills caption="Maksmata t&ouml;&ouml;d" submit=no save=no
	@groupinfo bill_payments parent=bills caption="Laekumised" submit=no save=no
	@groupinfo bills_quality parent=bills caption="Kvaliteet" submit=no save=no

@groupinfo stats caption="Aruanded" save=no

	@groupinfo stats_stats parent=stats caption="Aruanded" save=no
	@groupinfo stats_annual_reports parent=stats caption="Majandusaasta aruanded" submit=no save=no
	@groupinfo stats_s parent=stats caption="Otsi" save=no
	@groupinfo stats_view parent=stats caption="Salvestatud aruanded" submit=no save=no
	@groupinfo stats_my parent=stats caption="Minu statistika" submit=no save=no

groupinfo qv caption="Vaata"  submit=no save=no

	@groupinfo quick_view caption="Vaata"  submit=no save=no parent=stats
	@groupinfo my_view caption="Minu p&auml;ev"  submit=no save=no parent=stats

@groupinfo transl caption=T&otilde;lgi
@groupinfo versions caption=Versioonid submit=no save=no parent=stats
@groupinfo keywords caption="V&otilde;tmes&otilde;nad" parent=documents_all

@reltype ETTEVOTLUSVORM value=1 clid=CL_CRM_CORPFORM
@caption &Otilde;iguslik vorm

@reltype ADDRESS value=3 clid=CL_CRM_ADDRESS
@caption Kontaktaadress

@reltype TEGEVUSALAD value=5 clid=CL_CRM_SECTOR
@caption Tegevusalad

@reltype TOOTED value=6 clid=CL_CRM_PRODUCT
@caption Tooted

@reltype CHILD_ORG value=7 clid=CL_CRM_COMPANY
@caption T&uuml;tar-organisatsioonid

@reltype WORKERS value=8 clid=CL_CRM_PERSON
@caption T&ouml;&ouml;tajad

@reltype OFFER value=9 clid=CL_CRM_OFFER
@caption Pakkumine

@reltype DEAL value=10 clid=CL_CRM_DEAL
@caption Tehing

@reltype KOHTUMINE value=11 clid=CL_CRM_MEETING
@caption Kohtumine

@reltype CALL value=12 clid=CL_CRM_CALL
@caption K&otilde;ne

@reltype TASK value=13 clid=CL_TASK
@caption Toimetus

@reltype EMAIL value=15 clid=CL_ML_MEMBER
@caption E-post

@reltype URL value=16 clid=CL_EXTLINK
@caption Veebiaadress

@reltype PHONE value=17 clid=CL_CRM_PHONE
@caption Telefon

@reltype TELEFAX value=18 clid=CL_CRM_PHONE
@caption Fax

@reltype JOBS value=19 clid=CL_PERSONNEL_MANAGEMENT_JOB_OFFER
@caption T&ouml;&ouml;pakkumine

@reltype TOOPAKKUJA value=20 clid=CL_CRM_COMPANY
@caption T&ouml;&ouml;pakkuja

@reltype TOOTSIJA value=21 clid=CL_CRM_PERSON
@caption T&ouml;&ouml;otsija

@reltype CUSTOMER value=22 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

// @reltype POTENTIONAL_CUSTOMER value=23 clid=CL_CRM_COMPANY
// @caption Tulevane klient

@reltype PARTNER value=24 clid=CL_CRM_COMPANY
@caption Partner

// @reltype POTENTIONAL_PARTNER value=25 clid=CL_CRM_COMPANY
// @caption Tulevane partner

@reltype COMPETITOR value=26 clid=CL_CRM_COMPANY
@caption Konkurent

@reltype ORDER value=27 clid=CL_SHOP_ORDER
@caption tellimus

//DEPRECATED
@reltype SECTION value=28 clid=CL_CRM_SECTION
@caption &Uuml;ksus

@reltype PROFESSIONS value=29 clid=CL_CRM_PROFESSION
@caption V&otilde;imalikud ametid

@reltype CATEGORY value=30 clid=CL_CRM_CATEGORY
@caption Kategooria

@reltype MAINTAINER value=31 clid=CL_CRM_PERSON
@caption Persoon, kellele firma on klient

@reltype SELLER value=32 clid=CL_CRM_PERSON
@caption Persoon, kes m&uuml;&uuml;s

@reltype PROJECT value=33 clid=CL_PROJECT
@caption Projekt

@reltype CLIENT_MANAGER value=34 clid=CL_CRM_PERSON
@caption Kliendihaldur

@reltype SECTION_WEBSIDE value=35 clid=CL_CRM_MANAGER
@caption &Uuml;ksus veebis

@reltype GROUP value=36 clid=CL_GROUP
@caption Organisatsiooni grupp

@reltype CONTRACT value=37 clid=CL_FILE
@caption Leping

@reltype OFFER_FILE value=38 clid=CL_FILE
@caption Pakkumise fail

@reltype DAY_REPORT value=39 clid=CL_CRM_DAY_REPORT
@caption p&auml;eva raport

@reltype DOCS_FOLDER value=40 clid=CL_MENU,CL_SERVER_FOLDER,CL_FTP_LOGIN
@caption Dokumentide allikas

@reltype REFERAL_TYPE value=41 clid=CL_META
@caption sissetuleku meetod

@reltype IMAGE value=42 clid=CL_IMAGE
@caption Pilt

@reltype SPECIAL_OFFERS value=43 clid=CL_CRM_SPECIAL_OFFER
@caption Eripakkumine

@reltype OPENHOURS value=44 clid=CL_OPENHOURS
@caption Avamisajad

@reltype ORGANISATION_LOGO value=45 clid=CL_IMAGE
@caption Organisatsiooni logo

@reltype BANK_ACCOUNT value=46 clid=CL_CRM_BANK_ACCOUNT
@caption arveldusarve

@reltype CURRENCY value=47 clid=CL_CURRENCY
@caption valuuta

@reltype CONTENT_DOCS_FOLDER value=48 clid=CL_DOCUMENT
@caption uudiste kataloog

@reltype METAMGR value=49 clid=CL_META
@caption Muutujate haldur

@reltype FIELD value=50 clid=CL_CRM_FIELD_ACCOMMODATION,CL_CRM_FIELD_FOOD,CL_CRM_FIELD_ENTERTAINMENT,CL_CRM_FIELD_CONFERENCE_ROOM
@caption Valdkond

@reltype SERVER_FILES value=51 clid=CL_SERVER_FOLDER
@caption failide kataloog serveris

@reltype RESOURCES_FOLDER value=52 clid=CL_MENU
@caption ressursside kataloog

@reltype RESOURCE_MGR value=53 clid=CL_MRP_WORKSPACE
@caption ressursihalduskeskkond

@reltype CONTACT_PERSON value=54 clid=CL_CRM_PERSON
@caption Kontaktisik

@reltype WAREHOUSE value=55 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype DESCRIPTION value=56 clid=CL_DOCUMENT
@caption Lisakirjelduse dokument

@reltype NUMBER_SERIES value=57 clid=CL_CRM_NUMBER_SERIES
@caption Numbriseeria

@reltype FORUM value=58 clid=CL_FORUM_V2
@caption Foorum

@reltype INSURANCE_CERT_FILE value=59 clid=CL_FILE
@caption Kindlustusdokumendi fail

@reltype TAX_CLEARANCE_FILE value=60 clid=CL_FILE
@caption Maksuv&otilde;la puudumise t&otilde;endi fail

@reltype SHOP_WAREHOUSE_PURCHASE_BILL value=61 clid=CL_SHOP_WAREHOUSE_PURCHASE_BILL
@caption Ostu arve

@reltype DEF_POLL value=62 clid=CL_POLL
@caption Kiirk&uuml;sitlus

@reltype BUYER value=62 clid=CL_CRM_COMPANY
@caption Ostja, M&uuml;&uuml;ja

@reltype BANK value=63 clid=CL_CRM_BANK
@caption Pank

@reltype BUYER_REFERAL_TYPE value=64 clid=CL_META
@caption sissetuleku meetod

@reltype CORRESPOND_ADDRESS value=65 clid=CL_CRM_ADDRESS
@caption Aadressid

@reltype KEYWORD value=66 clid=CL_KEYWORD
@caption V&otilde;tmes&otilde;na

@reltype SERVICE_TYPE value=67 clid=CL_CRM_SERVICE_TYPE
@caption Teenuse liik

@reltype INSURANCE value=68 clid=CL_CRM_INSURANCE
@caption Insurance

@reltype LANGUAGE value=69 clid=CL_LANGUAGE
@caption Language

@reltype MAILS_FOLDER value=70 clid=CL_MENU
@caption Meilide kataloog

@reltype ACTIVITY_STATS_TYPE value=71 clid=CL_CRM_ACTIVITY_STATS_TYPE
@caption Tegevuse statistika t&uuml;&uuml;p

@reltype COMMENT value=73 clid=CL_COMMENT
@caption Kommentaar

//DEPRECATED, do not use!
@reltype FIRMAJUHT value=74 clid=CL_CRM_PERSON
@caption Firmajuht

@reltype FAKE_COUNTY value=75 clid=CL_CRM_COUNTY
@caption Fake county

@reltype FAKE_CITY value=76 clid=CL_CRM_CITY
@caption Fake city

@reltype USER_CLASSIFICATOR_1 value=77 clid=CL_META
@caption User-defined classificator 1

@reltype USER_CLASSIFICATOR_2 value=78 clid=CL_META
@caption User-defined classificator 2

@reltype USER_CLASSIFICATOR_3 value=79 clid=CL_META
@caption User-defined classificator 3

@reltype USER_CLASSIFICATOR_4 value=80 clid=CL_META
@caption User-defined classificator 4

@reltype USER_CLASSIFICATOR_5 value=81 clid=CL_META
@caption User-defined classificator 5

@reltype EXTERNAL_LINKS value=82 clid=CL_EXTLINK
@caption V&auml;lised lingid

@reltype QUALITY_MENU value=83 clid=CL_MENU
@caption Kvaliteedi men&uuml;&uuml;

@reltype FAKE_COUNTRY value=84 clid=CL_CRM_COUNTRY
@caption Fake country

@reltype SALES_WAREHOUSE value=85 clid=CL_SHOP_WAREHOUSE
@caption Valmistoodete ladu

@reltype PURCHASING_MANAGER value=86 clid=CL_SHOP_PURCHASE_MANAGER_WORKSPACE
@caption Ostukeskkond

@reltype ADDRESS_ALT value=87 clid=CL_ADDRESS
@caption Aadressid

*/

define("CRM_TASK_VIEW_TABLE", 0);
define("CRM_TASK_VIEW_CAL", 1);

define("CRM_PROJECTS_SEARCH_SIMPLE", 1);
define("CRM_PROJECTS_SEARCH_DETAIL", 2);

define("CRM_CUSTOMERS_SEARCH_SIMPLE", 0);
define("CRM_CUSTOMERS_SEARCH_DETAIL", 1);

define("CRM_COMMENT_POSITIVE", 1);
define("CRM_COMMENT_NEUTRAL", 2);
define("CRM_COMMENT_NEGATIVE", 3);

define("CRM_COMPANY_USECASE_CLIENT", "s");
define("CRM_COMPANY_USECASE_EMPLOYER", "work");

class crm_company extends class_base
{
	const REQVAL_ALL_SELECTION = -1; // value for all items selection (in treeviews e.g.). should be integer, explicit type casting used extesively
	const REQVAR_CATEGORY = "cs_c"; // request parameter name for customer category

	public $unit = 0;
	public $category = 0;
	public $cat = 0;
	public $active_node = 0;
	public $users_person = null;

	//bad name, it is in the meaning of
	//show_contacts_search
	public $do_search = 0;

	function crm_company()
	{
		$this->init(array(
			'clid' => CL_CRM_COMPANY,
			'tpldir' => 'crm/crm_company',
		));

		$this->trans_props = array(
			"name", "tegevuse_kirjeldus", "userta1", "userta2", "userta3", "userta4", "userta5", "comment"
		);
	}

	function crm_company_init()
	{
		$us = get_instance(CL_USER);
		$this->users_person = new object($us->get_current_person());
	}

	/**
		@attrib name=generate_tree api=1 params=name
		@param tree_inst required type=object
			The treeview object
		@param obj_inst required type=object
			The root object
		@param url optional type=string
			The URL to add the params to
		@param conn_type optional
			What type of connections are allowed
		@param skip optional
			A connection type can have many "to" object types, if any of them should be skipped, then $skip does the trick
		@param attrib optional
			The node link can have some extra attributes
		@param leafs optional
			If leafs should be shown (not exactly what the description implies)
		@param style optional
			CSS style added to the node - sound funny - yeah, it is
		@param show_people optional type=boolean
	**/
	public function generate_tree($arr)
	{
		//all connections from the currrent object
		//different reltypes
		extract($arr);
		$origurl = isset($arr["url"]) ? $arr["url"] : NULL;
		$tree = $arr['tree_inst'];
		$obj = $arr['obj_inst'];
		$node_id = &$arr['node_id'];
		$attrib = &$arr['attrib'];
		$tmp_type = $conn_type;
		$show_people = isset($show_people) ? (bool) $show_people : false;

		if(isset($arr['skip']) && is_array($arr['skip']) && sizeof($arr['skip']))
		{
			$skip = &$arr['skip'];
		}
		else
		{
			$skip = array();
		}

		if($conn_type !== "RELTYPE_CATEGORY")
		{
			$conn_type = "RELTYPE_SECTION";
		}
		$conns = $obj->connections_from(array(
			'type' => $conn_type,
			'sort_by' => 'to.jrk',
			'sort_dir' => 'asc'
		));

		//parent nodes'id actually
		$this_level_id = $node_id;
		foreach($conns as $key => $conn)
		{
			//$skip in action
			if(in_array($conn->prop('type'), $skip))
			{
				continue;
			}
			//iga alam item saab yhe v6rra suurema v22rtuse
			//if the 'to.id' eq active_node then it should be bold
			$name = $conn->prop('to.name');
			if(!empty($style))
			{
				$name = '<span class=&quot;'.$style.'&quot;>'.$name.'</span>';
			}

			$tmp_obj = $conn->to();

			//use the plural unless plural is empty -- this is just for reltype_section
			if (!empty($this->tree_uses_oid))
			{
				$node_id = $conn->prop("to");
			}
			else
			{
				++$node_id;
			}

			if (!empty($arr["edit_mode"]))
			{
				$popm = new popup_menu();
				$popm->begin_menu("cst_".$node_id);
				$popm->add_item(array(
					"link" => html::get_change_url($conn->prop("to"), array("return_url" => get_ru())),
					"text" => t("Muuda")
				));
				$popm->add_item(array(
					"link" => $this->mk_my_orb("delete_node", array("id" => $conn->prop("to"), "post_ru" => get_ru())),
					"text" => t("Kustuta")
				));
				$name = $name." ".$popm->get_menu();
			}

			if($conn->prop('to') == $this->active_node)
			{
				$tree->set_selected_item($node_id);
			}

			$tree_node_info = array(
				'id'=>$node_id,
				'name'=>$name,
				'url'=> aw_url_change_var(array(
					'cat' => NULL,
					'org_id' => NULL,
					'cs_sbt' => NULL,
					$attrib => $conn->prop('to'),
				), false, $origurl),
				'oid' => $conn->prop('to'),
				"class_id" => $conn->prop("to.class_id"),
			);
			//i know, i know, this function is getting really bloated
			//i just don't know yet, how to refactor it nicely, until then
			//i'll be just adding the bloat
			//get all the company for the current leaf
			$conns_tmp = $tmp_obj->connections_from(array(
				"type" => "RELTYPE_CUSTOMER",
			));
			$oids = array();
			foreach($conns_tmp as $conn_tmp)
			{
				$oids[$conn_tmp->prop('to')] = $conn_tmp->prop('to');
			}
			$tree_node_info['oid'] = $oids;
			//let's find the picture for this obj
			$img_conns = $tmp_obj->connections_from(array("type" => "RELTYPE_IMAGE"));
			//uuuuu, we have a pic
			if(is_object(current($img_conns)))
			{
				//icon url
				$img = current($img_conns);
				$img_inst = new image();
				$tree_node_info['iconurl'] = $img_inst->get_url_by_id($img->prop('to'));
			}

			$tli = $this_level_id;

			if (!empty($arr["name_format_cb"]))
			{
				$inst = $arr["name_format_cb"][0];
				$func = $arr["name_format_cb"][1];
				$inst->$func($tree_node_info);
			}

			$tree->add_item($tli,$tree_node_info);
			//$this->generate_tree(&$tree,&$tmp_obj,&$node_id,$tmp_type,&$skip, &$attrib, $leafs);
			$this->generate_tree(array(
				'tree_inst' => $tree,
				'obj_inst' => $tmp_obj,
				'node_id' => &$node_id,
				'conn_type' => $tmp_type,
				'skip' => &$skip,
				'attrib' => &$attrib,
				'leafs' => $leafs,
				"edit_mode" => isset($edit_mode) ? $edit_mode : NULL,
				"show_people" => $show_people,
				"url" => $origurl,
				"name_format_cb" => isset($arr["name_format_cb"]) ? $arr["name_format_cb"] : "",
			));
		}

		if($this->use_group === 'relorg_s')
		{
			unset($statuses);
		}

		if(!empty($statuses))
		{
			$st = new crm_company_status();
			$categories = $st->categories(0);
			$company = get_current_company();
			foreach($categories as $id=>$cat)
			{
				$ol = new object_list(array(
					"class_id" => array(CL_CRM_COMPANY_STATUS),
					"category" => $id,
					"parent" => $company->id()

				));

				if($ol->count())
				{
					$tree->add_item(0,array(
						"id" => 'cat'.$id,
						"name" => $cat,
						"iconurl" => icons::get_icon_url(CL_MENU),
						"url" => aw_url_change_var(array(
							"tf" => 'cat'.$id,
							"cs_sbt" => null
						), false, $origurl)
					));

					foreach($ol->arr() as $o)
					{
						$tree->add_item('cat'.$id, array(
							"id" => 'cat'.$o->id(),
							"name" => $o->name(),
							"url" => aw_url_change_var(array(
								"tf" => 'st'.$o->id(),
								self::REQVAR_CATEGORY => 'st_'.$o->id(),
								"cs_sbt" => null
							), false, $origurl),
						));

						if($_GET["tf"] === 'st'.$o->id())
						{
							$tree->set_selected_item('cat'.$o->id());
						}
						$this->get_s_tree_stuff('cat'.$o->id(), $tree, 0);
					}
				}
			}
		}

		if($leafs)
		{
			if(is_callable(array($this, $leafs)))
			{
				$this->$leafs($tree, $obj,$this_level_id, $node_id, $show_people, $origurl);
			}
			else
			{
				$this->tree_node_items($tree, $obj,$this_level_id, $node_id, $show_people, $origurl);
			}
		}
	}

	function convert_objects($arr)
	{
		$i = new crm_company_cedit_impl();
		$i->convert_addresses($arr["id"] , $_POST["select"]);
		return  $arr["post_ru"];
	}

	//hardcoded
	function tree_node_items($tree,$obj,$this_level_id,&$node_id, $show_people, $origurl = NULL)
	{
		//getting the list of professions for the current
		//unit/organization
		$key = 'unit';
		$value = '';

		if($obj->is_a(CL_CRM_SECTION))
		{
			$professions = $obj->get_professions();
			$value = $obj->id();
		}
		elseif ($obj->is_a(CL_CRM_COMPANY))
		{
			$professions = new object_list();
		}
		else
		{
			$professions = new object_list();
		}

		if($show_people)
		{
			// preload sections from persons
			$p2s = array();
			$c = new connection();
			$r_conns = $c->find(array(
				"from.class_id" => crm_person_obj::CLID,
				"type" => "RELTYPE_SECTION",
				"to.oid" => $obj->id()
			));
			foreach($r_conns as $r_con)
			{
				$p2s[$r_con["from"]] = $r_con["from"];
			}
		}

		if($professions->count())
		{
			$tmp_obj = $professions->begin();

			do
			{
				$name = strlen($tmp_obj->prop('name_in_plural'))?$tmp_obj->prop('name_in_plural'):$tmp_obj->prop('name');

				$url = array();
				$url = aw_url_change_var(array('cat'=> $tmp_obj->id(), $key=>$value), false, $origurl);
				$tree->add_item($this_level_id,
					array(
						'id' => $tmp_obj->id(),
						'name' => $name,
						'iconurl' =>' images/scl.gif',
						'url'=>$url,
						"class_id" => $tmp_obj->class_id()
					)
				);

				if($tmp_obj->id() == $this->active_node)
				{
					$tree->set_selected_item($tmp_obj->id());
				}

				if($show_people && count($p2s) > 0)
				{
					$pol = new object_list(array(
						"class_id" => crm_person_obj::CLID,
						"CL_CRM_PERSON.RELTYPE_RANK" => $tmp_obj->id(),
						"oid" => $p2s
					));
					foreach($pol->arr() as $po)
					{
						$url = aw_url_change_var(array('cat'=>$po->id(),$key=>$value), false, $origurl);
						$name = parse_obj_name($po->name());
						if($po->id() == $this->active_node && ($_GET["unit"] == $obj->id()))
						{
	//						$name = '<b>'.$name.'</b>';
							$tree->set_selected_item($po->id());
						}
						$tree->add_item($node_id,
							array(
								'id' => $po->id(),
								'name' => $name,
								'iconurl' =>' images/icons/class_145.gif',
								'url'=>$url,
								"class_id" => $po->class_id()
							)
						);
					}
				}
			}
			while ($tmp_obj = $professions->next());
		}

		if($show_people)
		{
			foreach($p2s as $id)
			{
				$po = obj($id);
				$url = aw_url_change_var(array('cat'=>$po->id(),$key=>$value), false, $origurl);
				$name = parse_obj_name($po->name());
				if($po->id() == $this->active_node && ($_GET["unit"] == $obj->id()))
				{
//					$name = '<b>'.$name.'</b>';
					$tree->set_selected_item($po->id()."_".$po->id());
				}
				$tree->add_item($this_level_id,
					array(
						'id' => $po->id()."_".$po->id(),
						'name' => $name,
						'iconurl' =>' images/icons/class_145.gif',
						'url'=>$url,
						"class_id" => $po->class_id()
					)
				);
			}
		}
	}

	function get_property(&$arr)
	{
		$retval = PROP_OK;
		$data = &$arr['prop'];
		$arr["use_group"] = $this->use_group;

		if ("employees_management" === $this->use_group)
		{
			static $employees_view;
			if (!$employees_view)
			{
				$employees_view = new crm_company_employees_view();
				$employees_view->set_request($this->req);
			}

			$fn = "_get_{$data["name"]}";
			if (method_exists($employees_view, $fn))
			{
				return $employees_view->$fn($arr);
			}
		}

		switch($data['name'])
		{
			case "ettevotlusvorm":
				$pm_inst = new personnel_management();
				if(is_oid($pm_inst->get_sysdefault()))
				{
					$data["options"] = array(0 => t("--vali--")) + safe_array($pm_inst->get_legal_forms());
				}
				break;

			case "address_toolbar":
				$tb =&$arr["prop"]["toolbar"];

				$tb->add_button(array(
					"name" => "new",
					"img" => "new.gif",
					"tooltip" => t("Lisa uus aadress"),
					"url" => $this->mk_my_orb("new", array(
						"alias_to" => $arr["obj_inst"]->id(),
						"reltype" => 87,
						"return_url" => get_ru(),
						"parent" => $arr["obj_inst"]->id()
					), CL_ADDRESS),
				));
				$tb->add_button(array(
					"name" => "delete",
					"img" => "delete.gif",
					"tooltip" => t("Kustuta aadressid"),
					"action" => "delete_selected_objects",
					"confirm" => t("Oled kindel, et kustutada?"),
				));
				$tb->add_button(array(
					"name" => "convert",
					"img" => "restore.gif",
					"tooltip" => t("Konverdi uude aadressis&uuml;steemi"),
					"action" => "convert_objects",
				));
				break;

 			case "language":
 				if(empty($data["value"]))
 				{
 					$ol = new object_list(array(
						"class_id" => CL_LANGUAGE,
						"name" => "Eesti"
					));
 					if($ol->count())
 					{
 						$l = $ol->begin();
 						$data["value"] = $l->id();
 						$data["options"][$l->id()] = $l->name();
 					}
 				}
 				return true;

			case "tax_clearance_certificate_view":
				$conns = $arr["obj_inst"]->connections_from(array(
					"type" => "RELTYPE_TAX_CLEARANCE_FILE",
				));
				if(!sizeof($conns))
				{
					return PROP_IGNORE;
				}
				break;

			case "stypes_tb":
			case "stypes_tbl":
			case "stypes_tree":
				static $st_impl;
				if (!$st_impl)
				{
					$st_impl = new crm_company_objects_impl();
				}
				$fn = "_get_".$data["name"];
				return $st_impl->$fn($arr);
			case "insurance_table":
				$this->_get_insurance_tbl($arr);
				break;
			case "insurance_tb":
				$this->_get_insurance_tb($data);
				break;

			// CEDIT tab
			case "cedit_toolbar":
				$this->gen_cedit_tb($arr);
				break;

			case "cedit_phone_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"number" => t("Telefoninumber"),
					"type" => t("Telefoninumbri t&uuml;&uuml;p"),
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_phone_tbl($t, $arr);
				break;

			case "cedit_telefax_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"number" => t("Faksi number"),
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

			case "contact_desc_text":
				$data["value"] = $this->get_short_description($arr["obj_inst"]->id());
				break;

			case "cedit_email_tbl":
				$i = new crm_company_cedit_impl();
				$t = $data["vcl_inst"];
				$fields = array(
					"email" => t("Emaili aadress"),
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_email_tbl($t, $arr);
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
					"country" => t("Riik"),
					"county" => t("Maakond"),
					"city" => t("Linn"),
					"vald" => t("Vald"),
				//					"location" => t("Asukoht"),
					"street" => t("T&auml;nav/k&uuml;la"),
					"house" => t("Maja"),
					"apartment" => t("Korter"),
					"postal_code" => t("Postiindeks"),
					"po_box" => t("Postkast")
				);
				$i->init_cedit_tables($t, $fields);
				$i->_get_adr_tbl($t, $arr);
				$t->set_caption(t("Aadressid"));
				break;

			// END CEDIT tab

			case "owners_toolbar":
			case "owners_table":
				$i = new crm_company_owners_impl();
				$method = "_get_".$data["name"];
				$retval = is_callable(array($i, $method)) ? $i->$method($arr) : PROP_IGNORE;
				break;

			case "comments_display":
				if(!$arr["obj_inst"]->meta("comments_stored_in_objects"))
				{
					$this->move_comments_from_meta_to_objects($arr);
				}
				$tmp = array();

				foreach($arr["obj_inst"]->get_comments()->arr() as $comm)
				{
					if(strlen(trim($comm->commtext)))
					{
						$comment = nl2br($comm->commtext) .
							"<br /><br /><b>" . t("T&uuml;&uuml;p:") . "</b> " . (array_key_exists($comm->commtype, $this->comment_types) ? $this->comment_types[$comm->commtype] : t("M&auml;&auml;ramata")) .
							"<br /><b>" . t("Aeg:") . "</b> " . strftime("%d. %b %Y %H:%M", $comm->created()) .
							"<br /><b>" . t("Autor:") . "</b> " . $comm->uname . "<br />";
						if($this->can("edit", $comm->id()))
						{
							$comment .= html::href(array(
								"url" => $this->mk_my_orb("change", array("id" => $comm->id()), CL_COMMENT),
								"caption" => t("Muuda"),
							))."&nbsp; &nbsp;";
						}
						if($this->can("edit", $comm->id()))
						{
							$comment .= html::href(array(
								"url" => $this->mk_my_orb("del_comment", array("id" => $comm->id(), "post_ru" => get_ru())),
								"caption" => t("Kustuta"),
							));
						}
						$tmp[] = $comment;
					}
				}

				$data['value'] = implode("<hr />", $tmp);
				break;

			/// GENERAL TAB
			case "last_com_type":
				$data["value"] = array_key_exists($arr["obj_inst"]->meta("last_com_type"), $this->comment_types) ? $this->comment_types[$arr["obj_inst"]->meta("last_com_type")] : t("M&auml;&auml;ramata");
				break;

			case "com_statistics":
				$comms = $arr["obj_inst"]->get_comments();
				$total = $comms->count();
				$pos = $neutr = $neg = $undef = 0;

				if ($total)
				{
					foreach ($comms->arr() as $comm)
					{
						if (strlen(trim($comm->commtext)))
						{
							switch ($comm->commtype)
							{
								case CRM_COMMENT_POSITIVE:
									$pos++;
									break;

								case CRM_COMMENT_NEUTRAL:
									$neutr++;
									break;

								case CRM_COMMENT_NEGATIVE:
									$neg++;
									break;

								default:
									$undef++;
							}
						}
						else
						{
							$total--;
						}
					}

					$data["value"] = $this->comment_types[CRM_COMMENT_POSITIVE] . ": " . number_format((($pos/$total)*100), 1, ".", "") . "% ({$pos})<br />" .
						$this->comment_types[CRM_COMMENT_NEUTRAL] . ": " . number_format((($neutr/$total)*100), 1, ".", "") . "% ({$neutr})<br />" .
						$this->comment_types[CRM_COMMENT_NEGATIVE] . ": " . number_format((($neg/$total)*100), 1, ".", "") . "% ({$neg})<br /><br />" .
						($undef ? t("M&auml;&auml;ramata") . ": " . number_format((($undef/$total)*100), 1, ".", "") . "% ({$undef})<br /><br />" : "") .
						t("Kokku") . ": " . $total;
				}
				else
				{
					$data["value"] = t("Kommentaare pole");
				}
				break;

			case "insurance_status":
			case "tax_clearance_status":
				$prop_prefix = substr($data["name"], 0, -7);

				if (1 > $arr["obj_inst"]->prop($prop_prefix . "_expires"))
				{
					# not defined
					$data["value"] = t("M&auml;&auml;ramata");
				}
				elseif (time() >= $arr["obj_inst"]->prop($prop_prefix . "_expires"))
				{
					# expired
					$data["value"] = '<span style="color: red;">' . t("Aegunud") . '</span>';
				}
				else
				{
					# valid
					$data["value"] = '<span style="color: green;">' . t("Kehtiv") . '</span>';
				}
				break;

			case "forum":
				$forum = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FORUM");
				if (!$forum)
				{
					$o = obj();
					$o->set_class_id(CL_FORUM_V2);
					$o->set_parent($arr["obj_inst"]->id());
					$o->set_name(sprintf(t("%s foorum"), $arr["obj_inst"]->name()));
					$o->save();
					$arr["obj_inst"]->connect(array(
						"to" => $o->id(),
						"type" => "RELTYPE_FORUM"
					));

					$fi = $o->instance();
					$fi->callback_post_save(array(
						"obj_inst" => $o,
						"request" => array("new" => 1)
					));
					$forum = $o;
				}

				$i = $forum->instance();
				$i->obj_inst = $forum;
				$data["value"] = $i->draw_all_folders(array(
					"obj_inst" => $forum,
					"request" => $arr["request"]
				));
				break;

			case "ext_sys_t":
				$this->_ext_sys_t($arr);
				break;

			case "name":
				//$data["autocomplete_source"] = "/automatweb/orb.aw?class=crm_company&action=name_autocomplete_source";
				$data["autocomplete_source"] = $this->mk_my_orb("name_autocomplete_source");
				$data["autocomplete_params"] = array("name");
				//$data["option_is_tuple"] = true;
				break;

			case "reg_nr":
				// append link to go to thingie
				$link_title = t("KrediidiInfo p&auml;ring");
				$window_title = t("KrediidiInfo Firmap&auml;ring");
				$window_url = "http://firmaparing.krediidiinfo.ee/";
				$data["post_append_text"] = <<<END
 <a href="#" onclick="win = window.open('{$window_url}', '{$window_title}', 'location=1, status=1, scrollbars=1, width=800, height=500')">{$link_title}</a>
END;
				break;

			case "tax_nr":
				// append link to go to thingie
				$link_title = t("Maksuameti p&auml;ring");
				$window_title = t("KMKR p&auml;ring");
				$window_url = "https://apps.emta.ee/e-service/doc/a0003.xsql";
				$data["post_append_text"] = <<<END
 <a href="#" onclick="win = window.open('', '{$window_title}', 'location=1, status=1, scrollbars=1, width=800, height=500'); win.document.write('<form action=&quot;{$window_url}&quot; method=&quot;post&quot; name=&quot;kraaks&quot;><input type=&quot;text&quot; name=&quot;p_kkood&quot; /><input type=&quot;hidden&quot; name=&quot;p_submit&quot; value=&quot;Otsi&quot; /><input type=&quot;hidden&quot; name=&quot;p_isikukood&quot; /><input type=&quot;hidden&quot; name=&quot;p_tegevus&quot; /><input type=&quot;hidden&quot; name=&quot;p_context&quot; /><input type=&quot;hidden name=&quot;p_tagasi&quot; /><input type=&quot;hidden name=&quot;p_mode&quot; value=&quot;1&quot; /><input type=&quot;hidden&quot; name=&quot;p_queryobject&quot; /></form>'); win.document.kraaks.p_kkood.value = document.changeform.reg_nr.value; win.document.kraaks.submit();">{$link_title}</a>
END;
				break;

			case "contact_person":
			case "contact_person2":
			case "contact_person3":
				$data["options"] = $this->get_employee_picker($arr["obj_inst"], true);

			case "bill_due_days":
			case "cust_contract_date":
				$data["year_from"] = 1990;
				$data["year_to"] = date("Y")+1;

			case "referal_type":
			case "priority":
			case "bill_penalty_pct":
				// read from rel
				if (($rel = $this->get_cust_rel($arr["obj_inst"])) and $rel->is_property($data["name"]))
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

				if (isset($data["options"]) && !isset($data["options"][$data["value"]]) && acl_base::can("view", $data["value"]))
				{
					$tmp = obj($data["value"]);
					$data["options"][$data["value"]] = $tmp->name();
				}
				break;

			case "cust_contract_creator":
				$this->_get_cust_contract_creator($arr);
				break;

			case "currency":
				// get all currencies, sorted by order
				$ol = new object_list(array(
					"class_id" => CL_CURRENCY,
					"sort_by" => "objects.jrk"
				));
				$data["options"] = $ol->names();
				break;

			case "comment_type":
				$data["options"] = $this->comment_types;
			case "comments_title":
			case "comment_text":
				if(!$this->can("add", $this->eligible_to_comment($arr)))
				{
					return PROP_IGNORE;
				}
				break;

			case "bank_account":
				$data["direct_links"] = 1;
				break;

			case "pohitegevus":
				$this->_get_pohitegevus($arr);
				break;

			case "year_founded":
				$data["to"] = substr(date(DATE_ISO8601), 0, -5);
				break;

			case 'contact':
				return PROP_IGNORE;
				break;

			case "firmajuht":
				$this->_get_firmajuht($arr);
				break;

			case "navtoolbar":
				$this->navtoolbar($arr);
				break;

			case "activity_keywords":
				$data["value"] = isset($data["value"]) && trim($data["value"]) ? substr($data["value"], 1) : "";
				break;

			/// CUSTOMER tab
			case "my_projects":
			case "customer_rel_creator":
			case "customer_search_cust_mgr":
			case "customer_search_is_co":
			case "my_customers_toolbar":
			case "my_customers_table":
			case "customer_categories_table":
			case "offers_listing_toolbar":
			case "offers_listing_tree":
			case "offers_listing_table":
			case "offers_current_org_id":
			case "projects_listing_tree":
			case "projects_listing_table":
			case "project_tree":
			case "org_proj_tb":
			case "org_proj_arh_tb":
			case "report_list":
			case "all_proj_search_part":
			case "proj_search_part":
			case "customer_toolbar":
			case "customer_listing_tree":
				static $cust_impl;
				if (!$cust_impl)
				{
					$cust_impl = new crm_company_cust_impl();
					$cust_impl->layoutinfo = &$this->layoutinfo;
					$cust_impl->use_group = $this->use_group;
				}
				$fn = "_get_".$data["name"];
				return $cust_impl->$fn($arr);

			case "customer_search_cust_grp":
				if ( aw_global_get('crm_customers_search_mode') != CRM_CUSTOMERS_SEARCH_DETAIL )
				{
					return PROP_IGNORE;
				}
				static $cust_impl;
				if (!$cust_impl)
				{
					$cust_impl = get_instance("applications/crm/crm_company_cust_impl");
				}
				$fn = "_get_".$data["name"];
				return $cust_impl->$fn($arr);
				break;

			case "customer_search_insurance_exp":
				if ( aw_global_get('crm_customers_search_mode') != CRM_CUSTOMERS_SEARCH_DETAIL )
				{
					return PROP_IGNORE;
				}
				static $cust_impl;
				if (!$cust_impl)
				{
					$cust_impl = get_instance("applications/crm/crm_company_cust_impl");
				}
				$fn = "_get_".$data["name"];
				return $cust_impl->$fn($arr);

			case "customer_search_print_view":
				if ( aw_global_get('crm_customers_search_mode') != CRM_CUSTOMERS_SEARCH_DETAIL )
				{
					return PROP_IGNORE;
				}
				$data['value'] = $arr['request'][$data["name"]];
				$data["onclick"] = "document.changeform.target=\"_blank\"";
				break;

			case "customer_search_keywords":
				$data["autocomplete_source_method"] = "keywords_autocomplete_source";
				$data["autocomplete_params"] = array("customer_search_keywords");
				$data["autocomplete_delimiters"] = array(",");
			case "customer_search_worker":
			case "customer_search_city":
			case "customer_search_county":
		//	case "customer_search_address":
			case "customer_search_ev":
			case "customer_search_cust_grp":
				if ( aw_global_get('crm_customers_search_mode') != CRM_CUSTOMERS_SEARCH_DETAIL )
				{
					return PROP_IGNORE;
				}
				$s = $arr['request'][$data["name"]];
				$this->dequote($s);
				$data['value'] = $s;
				break;

			case "customer_search_reg":
			case "cs_n":
			case "cs_sbt":
			case "customer_search":
			case "customer_search_address":
				$s = isset($arr['request'][$data["name"]]) ? $arr['request'][$data["name"]] : "";
				$this->dequote($s);
				$data['value'] = $s;
				break;

			case "customer_search_classif1":
				$data["options"]["NA"] = t("N/A");
				break;

			case "proj_search_dl_from":
				if ( aw_global_get('crm_projects_search_mode') != CRM_PROJECTS_SEARCH_DETAIL )
				{
					return PROP_IGNORE;
				}
				if (!isset($arr["request"]["proj_search_sbt"]))
				{
					$data["value"] = -1; //mktime(0,0,0, date("m"), date("d"), date("Y")-1);
				}
				else
				if ($arr["request"]["proj_search_dl_from"]["year"] > 1)
				{
					$data["value"] = $arr["request"]["proj_search_dl_from"];
				}
				else
				{
					$data["value"] = -1;
				}
				break;

			case "proj_search_dl_to":
				if ( aw_global_get('crm_projects_search_mode') != CRM_PROJECTS_SEARCH_DETAIL )
				{
					return PROP_IGNORE;
				}

				if (!isset($arr["request"]["proj_search_sbt"]))
				{
					$data["value"] = -1; //mktime(0,0,0, date("m"), date("d"), date("Y")+1);
				}
				elseif ($arr["request"]["proj_search_dl_to"]["year"] > 1)
				{
					$data["value"] = $arr["request"]["proj_search_dl_to"];
				}
				else
				{
					$data["value"] = -1;
				}
				break;

			case "proj_search_state":

				$proj_i = new project();
				$data["options"] = array("" => t("K&otilde;ik")) + $proj_i->states;
				if (!isset($arr["request"]["proj_search_state"]))
				{
					$data["value"] = PROJ_IN_PROGRESS;
				}
				else
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				break;

			case "proj_search_cust":
				if (!empty($arr["request"]["do_proj_search"]))
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				break;

			case "proj_search_code":
			case "proj_search_contact_person":
			case "proj_search_task_name":
				if ( aw_global_get('crm_projects_search_mode') != CRM_PROJECTS_SEARCH_DETAIL )
				{
					return PROP_IGNORE;
				}
			case "proj_search_name":
				if (!empty($arr["request"]["do_proj_search"]))
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				break;
			case "proj_search_change_mode_sbt":
			case "all_proj_search_change_mode_sbt":
				if ( aw_global_get('crm_projects_search_mode') != CRM_PROJECTS_SEARCH_DETAIL  )
				{
					$data['caption'] = t('Otsi ja t&auml;ienda');
				}
				else
				{
					$data['caption'] = t('Lihtne otsing');
				}
				break;
			case "all_proj_search_state":
				$proj_i = new project();
				$data["options"] = array("" => "") + $proj_i->states;
				if ($arr["request"]["search_all_proj"])
				{
					return PROP_IGNORE;
				}
				if (!$arr['request']["aps_sbt"])
				{
					$data["value"] = PROJ_DONE;
				}
				else
				{
					$data["value"] = $arr['request'][$data["name"]];
				}
				break;

			case "all_proj_search_dl_from":
			case "all_proj_search_dl_to":
				if ($this->use_group === "org_projects_archive")
				{
					return PROP_IGNORE;
				}
			case "all_proj_search_end_from":
			case "all_proj_search_end_to":
				if ($arr["request"]["search_all_proj"])
				{
					return PROP_IGNORE;
				}
				if ( aw_global_get('crm_projects_search_mode') != CRM_PROJECTS_SEARCH_DETAIL )
				{
					return PROP_IGNORE;
				}
				if (!$arr['request'][$data["name"]])
				{
					$data["value"] = -1;
				}
				else
				{
					$data["value"] = $arr['request'][$data["name"]];
				}
				break;

			case "all_proj_search_cust":
				if (!$arr["request"]["search_all_proj"])
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			case "all_proj_search_code":
			case "all_proj_search_arh_code":
			case "all_proj_search_contact_person":
			case "all_proj_search_task_name":
				if ( aw_global_get('crm_projects_search_mode') != CRM_PROJECTS_SEARCH_DETAIL )
				{
					return PROP_IGNORE;
				}
			case "all_proj_search_name":
			case "all_proj_search_sbt":
			case "all_proj_search_clear":
				if (!$arr["request"]["search_all_proj"])
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				else
				{
					return PROP_IGNORE;
				}
				break;

			/// OBJECTS TAB
			case "objects_listing_toolbar":
			case "objects_listing_tree":
			case "objects_listing_table":
				static $obj_impl;
				if (!$obj_impl)
				{
					$obj_impl = new crm_company_objects_impl();
				}
				$fn = "_get_".$data["name"];
				return $obj_impl->$fn($arr);

			// ACTIONS TAB
			case "act_s_cal_name":
			case "act_s_code":
				return PROP_IGNORE;
			case "my_tasks":
			case "my_tasks_cal":
				if(!(aw_global_get("crm_task_view") > -1))
				{
					$seti = new crm_settings();
					$sts = $seti->get_current_settings();
					if ($sts && ($sts->prop("default_tasks_view")))
					{
						aw_session_set("crm_task_view" , $sts->prop("default_tasks_view"));
					}
				}
			case "org_actions":
			case "org_calls":
			case "org_meetings":
			case "org_tasks":
			case "org_bugs":
			case "tasks_call":
			case "my_tasks_tb":
			case "act_s_part":
			case "mail_tbl":
			case "mail_tb":
			case "activity_stats_toolbar":
			case "activity_stats_table":
			case "tasks_type_tree":
			case "tasks_time_tree":
			case "tasks_tree":
				static $overview_impl;
				if (!$overview_impl)
				{
					$overview_impl = new crm_company_overview_impl();
				}
				$fn = "_get_".$data["name"];
				return $overview_impl->$fn($arr);

			case "act_s_dl_from":
			case "act_s_dl_to":
				if (empty($arr['request'][$data["name"]]))
				{
					$data["value"] = -1;
				}
				else
				{
					$data["value"] = $arr['request'][$data["name"]];
				}
				break;

			case "act_s_status":
				if($this->use_group === "ovrv_mails") return class_base::PROP_IGNORE;
				$data["options"] = array(1 => t("T&ouml;&ouml;s"), 2 => t("Tehtud"), "3" => t("K&otilde;ik"));
				$data["value"] = isset($arr["request"]["act_s_status"]) ? $arr["request"]["act_s_status"] : "";
				break;

			case "act_s_task_content":
			case "act_s_code":
				if ($this->use_group === "ovrv_offers" || $this->use_group === "documents_all_manage" || $this->use_group === "ovrv_mails")
				{
					return class_base::PROP_IGNORE;
				}

			case "act_s_cust":
			case "act_s_task_name":
			case "act_s_proj_name":
				if($this->use_group === "ovrv_mails") return PROP_IGNORE;

			case "act_s_sbt":
				$s = isset($arr['request'][$data["name"]]) ? $arr['request'][$data["name"]] : "";
				$this->dequote($s);
				$data['value'] = $s;
				break;

			case "act_s_mail_name":
			case "act_s_mail_content":
				if($this->use_group !== "ovrv_mails")
				{
					return class_base::PROP_IGNORE;
				}
			case "act_s_print_view":
				return class_base::PROP_IGNORE;
				$data['value'] = isset($arr['request'][$data["name"]]) ? $arr['request'][$data["name"]] : "";
				$data["onclick"] = "document.changeform.target=\"_blank\"";
				break;

			// PEOPLE TAB
			case "contact_toolbar":
			case "unit_listing_tree":
			case "human_resources":
			case 'contacts_search_results':
			case "personal_offers_toolbar":
			case "unit_listing_tree_personal":
			case "personal_offers_table":
			case "personal_candidates_toolbar":
			case "unit_listing_tree_candidates":
			case "personal_candidates_table":
			case "cedit_tb":
			case "cedit_tree":
			case "cedit_table":
				static $people_impl;
				if (!$people_impl)
				{
					$people_impl = new crm_company_people_impl();
				}
				$fn = "_get_".$data["name"];
				return $people_impl->$fn($arr);

			//mail search
			case "mail_s_to":
			case "mail_s_body":
			case "mail_s_subj":
				$data['value'] = $arr['request'][$data["name"]];
				break;

			// contacts search
			case "contact_search_name":
			case "contact_search_firstname":
			case "contact_search_lastname":
			case "contact_search_code":
			case "contact_search_ext_id":
			case "contact_search_ext_id_alphanum":
			case "contact_search":
			case "contact_search_submit":
				if(empty($arr['request']['contact_search']))
				{
					return class_base::PROP_IGNORE;
				}
				else
				{
					$data['value'] = $arr['request'][$data["name"]];
				}
				break;

			case "docs_tb":
			case "docs_tree":
			case "docs_tbl":
			case 'docs_s_type':
			case 'docs_s_created_after':
			case "docs_news_tb":
			case "dn_res":
			case "documents_lmod":
			case "document_source_toolbar":
			case "document_source_list":
				static $docs_impl;
				if (!$docs_impl)
				{
					$docs_impl = new crm_company_docs_impl();
				}
				$fn = "_get_".$data["name"];
				return $docs_impl->$fn($arr);

			case 'stats_stats_from':
			case 'stats_stats_to':
			case 'docs_s_name':
			case 'docs_s_comment':
			case 'docs_s_task':
			case 'docs_s_name':
			case 'docs_s_customer':
			case 'docs_s_user':
			case 'docs_s_sbt':
			case 'docs_s_clear':
			case "dn_s_name":
			case "dn_s_lead":
			case "dn_s_content":
			case "bill_s_cust":
			case "bill_s_bill_no":
			case "bill_s_bill_to":
			case "show_bill_balance":
			case "currency_grouping":
				$data["value"] = isset($arr["request"][$data["name"]]) ? $arr["request"][$data["name"]] : "";
				break;

			case "bill_s_from":
				$data =& $arr["prop"];
				if (!isset($arr["request"][$data["name"]]))
				{
					$data["value"] = mktime(0,0,0, date("m")-($data["name"] === "bill_s_from" ? 1 : 0), 1, date("Y"));
				}
				else
				if ($arr["request"][$data["name"]]["year"] > 1)
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				else
				{
					$data["value"] = -1;
				}
				break;
			case "bill_s_to":
				$data =& $arr["prop"];
				if (!isset($arr["request"][$data["name"]]))
				{
					$data["value"] = mktime(0,0,0, date("m")-($data["name"] === "bill_s_from" ? 1 : 0), date("d"), date("Y"));
				}
				else
				if ($arr["request"][$data["name"]]["year"] > 1)
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				else
				{
					$data["value"] = -1;
				}
				break;

			case 'bill_task_list':
				if($arr["request"]["different_customers"])
				{
					$data["error"] = t("T&ouml;&ouml;d on teostatud erinevatele klientidele, palun kontrolli!");
					return class_base::PROP_ERROR;
				}

			case 'bill_proj_list':
			case 'bill_tb':
			case 'bills_list':
			case 'bills_tree':
			case 'bills_stats_tree':
			case 'bills_time_tree':
			case 'bills_tb':
			case 'bills_mon_tb':
			case 'invoice_template_folders':
			case 'invoice_templates_list':
			case 'invoice_folders_list':
			case "bill_s_client_mgr":
			case "bill_s_status":
			case "bill_s_with_tax":
			case "billable_start":
			case "billable_end":
			case "bs_tb":
			case "quality_tb":
			case "quality_tree":
			case "quality_list":
				static $bills_impl;
				if (!$bills_impl)
				{
					$bills_impl = new crm_company_bills_impl();
					$bills_impl->layoutinfo = &$this->layoutinfo;
					$bills_impl->use_group = $this->use_group;
				}
				$fn = "_get_".$data["name"];
				return $bills_impl->$fn($arr);

			case 'bill_payments_tb':
			case 'bill_payments_table':
			case "bill_payments_cust":
			case "bill_payments_bill_no":
			case "bill_payments_bill_to":
			case "bill_payments_from":
			case "bill_payments_to":
			case "bill_payments_client_mgr":
				$bills_p_impl = new crm_bill_payment();
				$fn = "_get_".$data["name"];
				return $bills_p_impl->$fn($arr);

			case "stats_s_to":
				if ($arr["request"][$data["name"]]["year"] > 1)
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				else
				{
					$data["value"] = mktime(0,0,0, date("m"), date("d"), date("Y"));
				}
				break;



			case "stats_s_proj":
				$data["autocomplete_source"] = "/automatweb/orb.aw?class=crm_company&action=proj_autocomplete_source";
				$data["autocomplete_params"] = array("stats_s_cust","stats_s_proj");
				$data["value"] = $arr["request"][$data["name"]];
				aw_global_set("changeform_target",  "_blank");
				break;

			case "stats_s_cust":
				$data["autocomplete_source"] = "/automatweb/orb.aw?class=crm_company&action=name_autocomplete_source";
				$data["autocomplete_params"] = array("stats_s_cust");
			case "stats_s_worker":
			case "stats_s_only_billable":
			case "stats_s_detailed":
				$data["value"] = $arr["request"][$data["name"]];
				aw_global_set("changeform_target",  "_blank");
				break;

			case "stats_tb":
			case "stats_list":
			case "stats_s_toolbar":
			case "stats_s_from":
			case "stats_s_cust_type":
			case "stats_s_res":
			case "stats_s_state":
			case "project_mgr":
			case "stats_s_res_type":
			case "stats_s_bill_state":
			case "stats_s_area":
			case "stats_s_worker_sel":
			case "stats_s_time_sel":
			case "stats_stats_time_sel":
 			case "stats_stats_tb":
			case "stats_stats_time_sel":
 			case "stats_tree":
 			case "stats_stats_from":
 			case "stats_stats_to":
 			case "stats_stats_search":
 			case "status_chart":
 			case "money_chart":
 			case "stats_table":
			case "stats_annual_reports_tbl":
				static $stats_impl;
				if (!$stats_impl)
				{
					$stats_impl = new crm_company_stats_impl();
				}
				$fn = "_get_".$data["name"];

				return $stats_impl->$fn($arr);

			case "qv_t":
			case "qv_cust_inf":
				static $qv_impl;
				if (!$qv_impl)
				{
					$qv_impl = new crm_company_qv_impl();
				}
				$fn = "_get_".$data["name"];
				return $qv_impl->$fn($arr);

			// RESOURCES tab
			case "res_tb":
			case "res_tree":
			case "res_tbl":
				static $res_impl;
				if (!$res_impl)
				{
					$res_impl = new crm_company_res_impl();
				}
				$fn = "_get_".$data["name"];
				return $res_impl->$fn($arr);
				break;

			// MY STATS tab
			case "my_stats_s_time_sel":
				$data["options"] = array(
					"" => t("--vali--"),
					"today" => t("T&auml;na"),
					"yesterday" => t("Eile"),
					"cur_week" => t("Jooksev n&auml;dal"),
					"cur_mon" => t("Jooksev kuu"),
					"last_mon" => t("Eelmine kuu")
				);
				$data["value"] = $arr["request"]["my_stats_s_time_sel"];
				if (!isset($arr["request"]["my_stats_s_time_sel"]))
				{
					$data["value"] = "cur_mon";
				}
				break;

			case "my_stats_s_type":
				$data["value"] = $arr["request"]["my_stats_s_type"];
				$data["options"] = array(
					"rows" => t("Ridade kaupa"),
					"pers_det" => t("Kokkuv&otilde;te"),
				);
				break;

			case "my_stats_s_from":
				$data =& $arr["prop"];
				if ($arr["request"][$data["name"]]["year"] > 1)
				{
					$data["value"] = $arr["request"][$data["name"]];
				}
				else
				{
					$data["value"] = mktime(0,0,0, date("m"), 1, date("Y"));
				}
				break;
			case "my_stats_s_to":
				$data["value"] = mktime(0,0,0, date("m"), date("d"), date("Y"));
//				$data["value"] = date_edit::get_timestamp($arr["request"][$data["name"]]);
				break;

			case "my_stats_s_cust":
				$data["value"] = $arr["request"]["my_stats_s_cust"];
				break;

			case "my_stats_s_type":
				$data["value"] = $arr["request"]["my_stats_s_type"];
				$data["options"] = array(
					"" => t("Kokkuv&otilde;te"),
					"rows" => t("Ridade kaupa")
				);
				break;

			case "my_stats":
				$i = new crm_person();
				$arr["request"]["stats_s_cust"] = $arr["request"]["my_stats_s_cust"];
				$arr["request"]["stats_s_from"] = $arr["request"]["my_stats_s_from"];
				$arr["request"]["stats_s_to"] = $arr["request"]["my_stats_s_to"];
				$arr["request"]["stats_s_type"] = $arr["request"]["my_stats_s_type"];
				$arr["request"]["stats_s_time_sel"] = $arr["request"]["my_stats_s_time_sel"];
				$i->_get_my_stats($arr);
				break;

			case "my_stats_tb":
				$_SESSION["create_bill_ru"] = get_ru();
				$arr["prop"]["vcl_inst"]->add_button(array(
					"name" => "save",
					"img" => "save.gif",
					"tooltip" => t("Salvesta"),
					"action" => "save_time"
				));
				$arr["prop"]["vcl_inst"]->add_button(array(
					"name" => "creab",
					"img" => "save.gif",
					"tooltip" => t("Loo arve"),
					"action" => "create_bill",
				));
				break;

			case "server_folder":
				if (empty($data["value"]))
				{
					$tmp = safe_array(aw_ini_get("server.name_mappings"));
					$data["value"] = reset($tmp);
				}
				break;

			case "polls_tb":
				$i = new crm_company_my_view();
				$data["value"] = $i->_get_polls_tb($arr);
				break;

			case "polls_tbl":
				$i = new crm_company_my_view();
				$data["value"] = $i->_get_polls_tbl($arr);
				break;

			case "my_view":
				$i = new crm_company_my_view();
				$data["value"] = $i->_get_my_view($arr);
				break;
			case "sell_offers":
				$procurement_center = new procurement_center();
				$procurement_center->_sell_offers_table($arr);
				break;
			case "sell_offers_prods":
				$procurement_center = new procurement_center();
				$procurement_center->_sell_offers_prod_table($arr);
				break;
			case "see_all_link":
			case "see_all_link2":
				$procurement_center = new procurement_center();
				$data["value"] = $procurement_center->_see_all_link($arr);
				break;
		}
		return $retval;
	}

	function set_property($arr)
	{
		$data = &$arr['prop'];

		if ("employees_management" === $this->use_group)
		{
			static $employees_view;
			if (!$employees_view)
			{
				$employees_view = new crm_company_employees_view();
				$employees_view->set_request($this->req);
			}

			$fn = "_set_{$data["name"]}";
			if (method_exists($employees_view, $fn))
			{
				return $employees_view->$fn($arr);
			}
		}

		// Security!
		$no_html = array(
			"fake_email",
			"fake_address_address",
			"fake_address_postal_code",
			"fake_address_city",
			"fake_address_county",
			"fake_address_country",
		);
		if(in_array($data["name"], $no_html))
		{
			$data["value"] = htmlspecialchars($data["value"], ENT_NOQUOTES, aw_global_get("charset"), false);
		}

		switch($data["name"])
		{
			case "logo":
				if(!is_oid($arr["obj_inst"]->id()))
				{
					// Save the company object, cuz the picture will set the company object as its parent.
					$arr["obj_inst"]->save();
				}
				break;

			case "phone_id":
			case "telefax_id":
			case "url_id":
			case "email_id":
			case "aw_bank_account":
				return class_base::PROP_IGNORE;

			case "cedit_phone_tbl":
			case "cedit_telefax_tbl":
			case "cedit_url_tbl":
			case "cedit_email_tbl":
			case "cedit_bank_account_tbl":
			case "cedit_adr_tbl":
				static $i;
				if (!$i)
				{
					$i = new crm_company_cedit_impl();
				}
				$fn = "_set_".$data["name"];
				$i->$fn($arr);
				break;

			case "ext_sys_t":
				$this->_save_ext_sys_t($arr);
				break;

			case "comment_history":
				$connect_comment_to_customer_data = false;
				$parent = $this->eligible_to_comment($arr, $connect_comment_to_customer_data);
				if (strlen(trim($arr['request']['comment_text'])) && $this->can("add", $parent))
				{
					$comm = obj();
					$comm->set_class_id(CL_COMMENT);
					$comm->set_parent($parent);
					$comm->name = sprintf(t("%s kommentaar organisatsioonile %s"), aw_global_get("uid"), $arr["obj_inst"]->name);
					$comm->uname = aw_global_get("uid");
					$comm->commtext = $arr['request']['comment_text'];
					$comm->commtype = $arr['request']['comment_type'];
					$comm->save();
					if($connect_comment_to_customer_data)
					{
						$o = obj($parent);
						$o->connect(array(
							"to" => $comm->id(),
							"type" => "RELTYPE_COMMENT_TO_COMPANY"
						));
					}
					// Connect to the company object anyway. Otherwise I have no way of knowing which company this comment concerns.
					$arr["obj_inst"]->connect(array(
						"to" => $comm->id(),
						"reltype" => "RELTYPE_COMMENT",
					));
					$arr["obj_inst"]->set_meta("last_com_type", $arr['request']['comment_type']);
				}
				break;

			case "activity_keywords":
				$keywords_tmp = explode(",", $data["value"]);
				$keywords = array();

				foreach ($keywords_tmp as $key => $keyword)
				{
					$keyword = trim($keyword);

					if ($keyword)
					{
						$keywords[] = $keyword;
					}
				}

				$data["value"] = count ($keywords) ? "," . implode (",", $keywords) : "";
				break;

			case "server_folder":
				$this->_proc_server_folder($arr);
				break;

			case "transl":
				$this->trans_save($arr, $this->trans_props);
				break;

			case "openhours":
				if (empty($data['value']['id']) && is_oid($arr['obj_inst']->id()))
				{
					// create new openhours obj as child
					$oh = new object(array(
						'parent' => $arr['obj_inst']->id(),
						'class_id' => CL_OPENHOURS,
						'name' => $arr['obj_inst']->name().' avatud',
						'status' => STAT_ACTIVE,
					));
					$oh->save();

					$data['value']['id'] = $oh->id();
					$arr['request']['openhours']['id'] = $oh->id();
					// And link it
					$arr['obj_inst']->connect(array(
						'to' => $oh->id(),
						'reltype' => 'RELTYPE_OPENHOURS',
					));

				}
			break;
			case "name":
				if ($data["value"] == "")
				{
					$data["error"] = t("Nimi peab olema t&auml;idetud!");
					return class_base::PROP_ERROR;
				}
				break;

			case "cust_contract_date":
				// save to rel
				if (($rel = $this->get_cust_rel($arr["obj_inst"])))
				{
					$rel->set_prop($data["name"], date_edit::get_timestamp($data["value"]));
					$rel->save();
				}
				break;

			case "bill_penalty_pct":
				$data["value"] = str_replace(",", ".", $data["value"]);

			case "cust_contract_creator":
			case "referal_type":
			case "contact_person":
			case "contact_person2":
			case "contact_person3":
			case "priority":
			case "bill_due_days":
				// save to rel
				if (($rel = $this->get_cust_rel($arr["obj_inst"])))
				{
					$rel->set_prop($data["name"] == "bill_due_days" ? "bill_due_date_days" : $data["name"], $data["value"]);
					$rel->save();
				}
				break;

			case "contact":
				return class_base::PROP_IGNORE;
		}
		return class_base::PROP_OK;
	}

	function callback_pre_edit($arr)
	{
		// initialize
		$pl = new planner();
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));
	}

	function gen_cedit_tb(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "delete_selected_objects",
		));
		$tb->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"action" => "",
			"tooltip" => t("Salvesta"),
		));
	}

	function get_all_workers_for_company($obj,&$data,$workers_too=false)
	{
		$workers = $obj->get_workers();
		$data += $workers->ids();
	}

	/**
		@attrib name=delete_selected_objects
	**/
	function delete_selected_objects($arr)
	{
		$selected_objects = $errors = array();
		if (!empty($arr["select"]))
		{
			$selected_objects += $arr["select"] ;
		}

		if (!empty($arr["cust_check"]))
		{
			$selected_objects += $arr["cust_check"] ;
		}

		if (!empty($arr["cat_check"]))
		{
			$selected_objects += $arr["cat_check"] ;
		}

		foreach ($selected_objects as $delete_obj_id)
		{
			if (object_loader::can("delete", $delete_obj_id))
			{
				$deleted_obj = obj($delete_obj_id);
				$deleted_obj->delete();
			}
			else
			{
				$errors[] = $delete_obj_id;
			}
		}

		if (count($errors))
		{
			$this->show_error_text(sprintf(t("Objekte %s ei saanud kustutada."), implode(", ", $errors)));
		}

		// return url
		$r = empty($arr["post_ru"]) ? $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => $arr["group"],
			"org_id" => isset($arr["offers_current_org_id"]) ? $arr["offers_current_org_id"] : 0),
			$arr["class"]
		) : $arr["post_ru"];
		return $r;
	}

	/**
		@attrib name=create_customer api=1 params=name
		@param company required type=int
			The OID of the company the customer is created for
		@param id optional type=int
			The OID of the customer
		@param clid optional type=class_id default=CL_CRM_COMPANY
			The class ID of the customer object to be created, only used if id not given
		@param name required type=string
			The name of the customer to be created
		@param gender optional type=int
			The gender of the customer to be created. Only used if clid = CL_CRM_PERSON
		@param birthday optional type=int
			The birthday of the customer to be created, given as a UNIX timestamp. Only used if clid = CL_CRM_PERSON
	**/
	public function create_customer($arr)
	{
		$company = obj($arr["company"], array(), crm_company_obj::CLID);
		unset($arr["company"]);
		$customer = $company->create_customer($arr);
		return $customer->json();
	}

	/**
		@attrib name=delete_customer api=1 params=name
		@param id required type=int
			The OID of the company the customer is deleted for
		@param customer required type=int
			The OID of the customer to be deleted
	**/
	public function delete_customer($arr)
	{
		$company = obj($arr["id"], array(), crm_company_obj::CLID);
		$customer_relation = $company->get_customer_relation(crm_company_obj::CUSTOMER_TYPE_BUYER, obj($arr["customer"]));
		if ($customer_relation)
		{
			$company->delete_customer($customer_relation);
		}
	}

	/**
		@attrib name=submit_new_task
		@param id required type=int acl=view
	**/
	function submit_new_task($arr)
	{
		$arr['clid'] = CL_TASK;
		$arr['reltype'] = 10; //CL_CRM_PERSON.RELTYPE_PERSON_TASK
		$this->submit_new_action_to_person($arr);
	}

	/**
		@attrib name=search_for_contacts
		@param cat optional
		@param unit optional
	**/
	function search_for_contacts($arr)
	{
		return $this->mk_my_orb(
			'change',array(
				'id' => $arr['id'],
				'group' => $arr['group'],
				'contact_search' => true,
				'unit' => $arr['unit'],
				'cat' => $arr['cat'],
			),
			'crm_company'
		);
	}

	/**
		@attrib name=submit_new_call
		@param id required type=int acl=view
	**/
	function submit_new_call($arr)
	{
		$arr['clid'] = CL_CRM_CALL;
		$arr['reltype'] = 9; //CL_CRM_PERSON.RELTYPE_PERSON_CALL
		$this->submit_new_action_to_person($arr);
	}

	/**
		@attrib name=submit_new_meeting
		@param id required type=int acl=view
	**/
	function submit_new_meeting($arr)
	{
		$arr['clid'] = CL_CRM_MEETING;
		$arr['reltype'] = 8; //CL_CRM_PERSON.RELTYPE_PERSON_MEETING
		$this->submit_new_action_to_person($arr);
	}

	function submit_new_action_to_person(&$arr)
	{
		if(!is_array($arr['check']))
		{
			return;
		}

		$us = get_instance(CL_USER);
		$person = new object($us->get_current_person());
		$arr['check'][$person->id()] = $person->id();

		$prsn = get_instance(CL_CRM_PERSON);
		$pl = get_instance(CL_PLANNER);
		$cal_id = $pl->get_calendar_for_user(array(
			'uid' => aw_global_get('uid')
		));
		$alias_to_org_arr = array();
		$fake_alias = $arr["id"];

		reset($arr['check']);

		$fake_alias = current($arr['check']);

		$url = $this->mk_my_orb('new',array(
			'add_to_cal' => $cal_id,
			'alias_to_org'=>$fake_alias,
			'reltype_org'=> $arr['reltype'],
			'alias_to_org_arr'=>serialize($arr['check']),
			"parent" => $arr["id"],
			"return_url" => $arr["post_ru"]
		),$arr['clid']);
		header('Location: '.$url);
		die();
	}

//-------------------------t88suhte peale
	// If an event is added to a person, then this method
	// makes that event appear in any organization
	// calendars that the person has a "workplace" connection
	// with.
	function on_add_event_to_person($arr)
	{
		$event_obj = new object($arr["event_id"]);
		$typemap = array(
			CL_CRM_MEETING => 11,
			CL_CRM_CALL => 12,
			CL_TASK => 13,
		);

		$reltype = $typemap[$event_obj->class_id()];
		if (empty($reltype))
		{
			return false;
		};

		$per_obj = new object($arr["source_id"]);

		foreach($per_obj->get_all_orgs()->arr() as $org_obj)
		{
			$org_obj->connect(array(
				"to" => $arr["event_id"],
				"reltype" => $reltype,
			));
		}
	}


	////
	// !Listens to MSG_EVENT_ADD broadcasts and creates
	// connections between a CRM_PERSON and a CRM_COMPANY
	// if an event is added to a person.
	function register_humanres_event($arr)
	{
		$event_obj = new object($arr["event_id"]);
		$typemap = array(
			CL_CRM_CALL => 12,
			CL_TASK => 13,
			CL_CRM_MEETING => 11,
		);

		$reltype = $typemap[$event_obj->class_id()];
		if (empty($reltype))
		{
			return false;
		};

		$per_obj = new object($arr["person_id"]);

		$conns = $per_obj->connections_to(array(
			"type" => 8,
		));

		foreach($conns as $conn)
		{
			$org_obj = $conn->from();
			$org_obj->connect(array(
				"to" => $arr["event_id"],
				"reltype" => $reltype,
			));
		}
	}

	/**
		@attrib name=submit_delete_ppl
		@param id required type=int acl=view
		@param unit optional type=int
	**/
	function submit_delete_ppl($arr)
	{
		if (is_array($arr["check"]))
		{
			$cl_crm_person = new crm_person();
			foreach($arr['check'] as $key => $value)
			{
				$p = obj($value);

				if ($p->is_a(crm_person_obj::CLID))
				{ // only delete people
					if ($this->can("delete", $p->id()))
					{
						// also remove user and its group
						// user removal must succeed regardless of access rights
						$user = $cl_crm_person->has_user($p);
						$p->delete();

						if (is_object($user))
						{
							$user->delete();
						}
					}
					else
					{
						$this->show_error_text(sprintf(t("Kasutaja '%s' kustutamiseks puudub &otilde;igus."), $p->name()));
					}
				}
			}
		}

		if (!empty($arr["id"]))
		{
			$this_object = obj($arr["id"], array(), CL_CRM_COMPANY);
			$this_object->save();
		}

		return $arr["post_ru"];
	}

	/** Delete customer relations and customer objects completely
		@attrib name=remove_delete_cust
		@param id required type=oid
		@param cust_check required type=array
		@param post_ru optional type=string
	**/
	function remove_delete_cust($arr)
	{
		if (is_array($arr["cust_check"]) and count($arr["cust_check"]))
		{
			try
			{
				$this_o = obj($arr["id"], array(), CL_CRM_COMPANY);
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Organisatsiooniobjekt polnud loetav."));
				return $arr["post_ru"];
			}

			foreach($arr["cust_check"] as $customer_relation_oid)
			{
				try
				{
					$customer_relation_o = obj($customer_relation_oid);
					$this_o->delete_customer($customer_relation_o, true);
				}
				catch (Exception $e)
				{
					$errors[] = $customer_relation_o->name();
				}
			}

			if ($errors)
			{
				$this->show_error_text(sprintf(t("Kliente %s kustutada ei &otilde;nnestunud."), ("'" . implode("', '", $errors) . "'")));
			}
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=remove_from_category all_args=1
	**/
	function remove_from_category($arr)
	{
		if (is_array($arr["cust_check"]) and count($arr["cust_check"]) and is_oid($arr[self::REQVAR_CATEGORY]))
		{
			$errors = array();
			try
			{
				$category = obj($arr[self::REQVAR_CATEGORY]);

				foreach($arr['cust_check'] as $customer_relation_oid)
				{
					try
					{
						$customer_relation_o = obj($customer_relation_oid);
						$customer_relation_o->remove_category($category);
					}
					catch (Exception $e)
					{
						$errors[] = $customer_relation_o->name();
					}
				}
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Viga kategooria lugemisel"));
			}

			if ($errors)
			{
				$this->show_error_text(sprintf(t("Kliente %s kategooriast eemaldada ei &otilde;nnestunud."), ("'" . implode("', '", $errors) . "'")));
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=remove_cust_relations all_args=1
	**/
	function remove_cust_relations($arr)
	{
		if (is_array($arr["cust_check"]) and count($arr["cust_check"]))
		{
			try
			{
				$this_o = obj($arr["id"], array(), CL_CRM_COMPANY);
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Organisatsiooniobjekt polnud loetav."));
				return $arr["post_ru"];
			}

			$errors = array();
			foreach($arr["cust_check"] as $customer_relation_oid)
			{
				try
				{
					$customer_relation_o = obj($customer_relation_oid);
					$this_o->delete_customer($customer_relation_o);
				}
				catch (Exception $e)
				{
					$errors[] = $customer_relation_o->name();
				}
			}

			if ($errors)
			{
				$this->show_error_text(sprintf(t("Kliendisuhteid %s l&otilde;petada ei &otilde;nnestunud."), ("'" . implode("', '", $errors) . "'")));
			}
		}

		return $arr["post_ru"];
	}

	/**
		Ends selected work relations, if
		@attrib name=submit_delete_relations
		@param id required type=int acl=view
		@param post_ru required type=string
		@param cat optional type=int
			Profession oid. Delete only relations with that profession
		@param check optional type=array
			Array of person object id-s
	**/
	function submit_delete_relations($arr)
	{
		try
		{
			$this_o = obj($arr['id'], array(), CL_CRM_COMPANY);
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("Organisatsiooniobjekt polnud loetav."));
			return $arr["post_ru"];
		}

		if (isset($arr["check"]) and is_array($arr["check"]))
		{
			$failed_person_oids = array();
			$profession = null;
			foreach($arr['check'] as $person_oid)
			{
				try
				{
					$person = obj($person_oid, array(), crm_person_obj::CLID);
					if (!empty($arr["cat"]) and is_oid($arr["cat"])) $profession = obj($arr["cat"], array(), CL_CRM_PROFESSION);

					$work_relations = crm_person_work_relation_obj::find($person, $profession, $this_o);
					if($work_relations->count())
					{
						$work_relation = $work_relations->begin();

						do
						{
							if (!$work_relation->is_finished())
							{
								$this_o->finish_work_relation($work_relation);
							}
						}
						while ($work_relation = $work_relations->next());
					}
				}
				catch (Exception $e)
				{
					/*~AWdbg*/ if (aw_ini_get("debug_mode")) { echo nl2br($e); exit; }
					$failed_person_oids[] = $person_oid;
				}
			}

			if (count($failed_person_oids))
			{
				$this->show_error_text(t("Osa valitud isikuid polnud loetavad."));
			}
			else
			{
				$this->show_completed_text(t("Valitud isikutega antud ameti all t&ouml;&ouml;suhted l&otilde;petatud."));
			}
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=submit_delete_my_customers_relations
		@param id required type=int acl=view
	**/
	function submit_delete_my_customers_relations($arr)
	{
		if (is_array($arr["cust_check"]) && count($arr["cust_check"]))
		{
			$ol = new object_list(array("oid" => $arr["cust_check"]));
			$ol->delete();
		}

		return $arr["post_ru"];
	}

	/**
		deletes the relations category -> organization || organization -> category
		@attrib name=submit_delete_customer_relations
		@param id required type=int acl=view
		@param customer optional type=int
	**/
	function submit_delete_customer_relations($arr)
	{
		if(!is_array($arr['cust_check']))
		{
			return $arr["post_ru"];
		}

		$main_obj = new object($arr['id']);
		if(!empty($arr[self::REQVAR_CATEGORY]))
		{
			$main_obj = new object($arr[self::REQVAR_CATEGORY]);
		}

		foreach($arr['cust_check'] as $key=>$value)
		{
			$vo = obj($value);
			if ($vo->class_id() == CL_CRM_SECTION)
			{
				$vo->delete();
			}
			else
			{
				$main_obj->disconnect(array('from'=>$value));
			}
		}
		return $arr["post_ru"];
	}

	function callback_on_load($arr)
	{
		$this->comment_types = array (
			CRM_COMMENT_POSITIVE => t("Positiivne"),
			CRM_COMMENT_NEUTRAL => t("Neutraalne"),
			CRM_COMMENT_NEGATIVE => t("Negatiivne"),
		);

		$this->crm_company_init();
		if(array_key_exists('request',$arr))
		{
			$this->do_search = ifset($arr['request'], 'contact_search');
		}
		else
		{
			$this->do_search = $arr['contact_search'];
		}

		if(!empty($arr["request"]['cat']))
		{
			$this->cat = $arr["request"]['cat'];
		}

		if(!empty($arr["request"]['unit']))
		{
			$this->unit = $arr["request"]['unit'];
		}

		if ( $this->get_cval(aw_global_get('uid').'_crm_projects_search_mode') == CRM_PROJECTS_SEARCH_DETAIL )
		{
			$_SESSION['crm_projects_search_mode'] = CRM_PROJECTS_SEARCH_DETAIL;
			aw_global_set('crm_projects_search_mode', CRM_PROJECTS_SEARCH_DETAIL);
		}

		if($this->get_cval(aw_global_get('uid').'_crm_customers_search_mode') == CRM_CUSTOMERS_SEARCH_DETAIL )
		{
			$_SESSION['crm_customers_search_mode'] = CRM_CUSTOMERS_SEARCH_DETAIL;
			aw_global_set('crm_customers_search_mode', CRM_CUSTOMERS_SEARCH_DETAIL);
		}

	}

	/*
		k6ik lingid saavad $key muutuja lisaks
	*/
	function callback_mod_reforb(&$arr, $request)
	{
		if (isset($request["proj"])) $arr["proj"] = $request["proj"];
		if (isset($request["unit"])) $arr["unit"] = $request["unit"];
		if (isset($request["cat"])) $arr["cat"] = $request["cat"];
		if (isset($request["tf"])) $arr["tf"] = $request["tf"];
		if (isset($request["sector"])) $arr["sector"] = $request["sector"];
		if (isset($request["proj"])) $arr["proj"] = $request["proj"];
		if (isset($request["proj"])) $arr["proj"] = $request["proj"];
		if (isset($request["proj"])) $arr["proj"] = $request["proj"];

		$arr["cust_cat"] = 1;
		$arr["search_tbl"] = 0;
		$arr["bunch_bugs"] = "";

		if(isset($request['set_buyer_status']) && $request['action'] === 'new')
		{
			$arr["set_buyer_status"] = $request['set_buyer_status'];
		}

		if($this->use_group === "invoice_templates" and !empty($request[crm_company_bills_impl::INVOICE_TEMPLATE_FOLDERS_VAR]))
		{
			$arr["invoice_folder_parent"] = $request[crm_company_bills_impl::INVOICE_TEMPLATE_FOLDERS_VAR];
			$arr[crm_company_bills_impl::INVOICE_TEMPLATE_FOLDERS_VAR] = $request[crm_company_bills_impl::INVOICE_TEMPLATE_FOLDERS_VAR];
		}

		if($this->use_group === "stats_stats" || $this->use_group === "stats")
		{
			if (isset($request["st"])) $arr["st"] = $request["st"];
		}
		// placeholders/pseudo-properties for popup search actions
		elseif("relorg" === substr($this->use_group, 0, 6))
		{
			if (isset($request[self::REQVAR_CATEGORY])) $arr[self::REQVAR_CATEGORY] = $request[self::REQVAR_CATEGORY];

			//
			$arr["sbt_data_add_seller"] = 0;
			$arr["sbt_data_add_buyer"] = 0;
		}

		$arr["sbt_data"] = 0;
		$arr["sbt_data2"] = 0;
	}

	function get_cust_bds()
	{
		if(date('w') == 5)
		{
			$e_add = 2;
		}
		elseif(date('w') == 6)
		{
			$e_add = 1;
		}
		else
		{
			$e_add = 0;
		}
		$s_d = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$e_d = mktime(23,59,59, date('m'),date('d')+1+$e_add,date('Y'));
		$q = "
			SELECT
				objects.name as name,
				objects.oid as oid,
				kliendibaas_isik.birthday as bd
			FROM
				objects  LEFT JOIN kliendibaas_isik ON kliendibaas_isik.oid = objects.brother_of
			WHERE
				objects.class_id = '145' AND
				objects.status > 0  AND
				kliendibaas_isik.birthday != '' AND kliendibaas_isik.birthday != 0 AND kliendibaas_isik.birthday != -1 AND kliendibaas_isik.birthday is not null
		";
		$bds = array();
		$this->db_query($q);
		$cur_y = date('Y');
		while ($row = $this->db_next())
		{
			if (is_numeric($row["bd"]))
			{
				$bd = $row["bd"];
			}
			else
			{
				list($y, $m, $d) = explode("-", $row["bd"]);
				$bd = mktime(1,0,0,$m,$d,$cur_y);
			}
			if ($bd > $s_d && $bd < $e_d)
			{
				$bds[$bd][] = $row["oid"];
			}
		}
		return $bds;
	}

	/**
	@attrib name=cust_bds
	**/
	function cust_bds($arr)
	{
		$_GET["in_popup"] = 1;
		if(date('w') == 5)
		{
			$e_add = 2;
		}
		elseif(date('w') == 6)
		{
			$e_add = 1;
		}
		else
		{
			$e_add = 0;
		}
		$s_d = mktime(1,0,0,date('m'),date('d'),date('Y'));
		$e_d = mktime(23,59,59, date('m'),date('d')+1+$e_add,date('Y'));
		$bds = $this->get_cust_bds();
		$dc = 0;
		$this->read_template("bds.tpl");
		$this->vars(array(
			"title" => t("S&uuml;nnip&auml;evad"),
		));
		$tmp = '';
		for($i = $s_d;$i<=$e_d;$i+=24*60*60)
		{
			$dc++;
			$tmp2 = '';
			$bdis = 0;
			foreach($bds[$i] as $oid)
			{
				$bdis = 1;
				$p = obj($oid);
				$contacts = array();
				foreach($p->connections_from(array("type" => 16)) as $conn)
				{
					$wcon_wrel = $conn->to();
					if($wcon_wrel->prop("org.name"))
					{
						$contacts[] = $p->prop("org.name");
					}
				}
				if($p->prop("phone.name"))
				{
					$contacts[] = $p->prop("phone.name");
				}
				if($p->prop("email.mail"))
				{
					$contacts[] = $p->prop("email.mail");
				}
				$this->vars(array(
					"name" => html::obj_change_url($p->id(),$p->name()),
					"contacts" => implode(", ", $contacts),
				));
				$tmp2 .= $this->parse("bds");
			}
			if($dc == 1)
			{
				$day = t("T&auml;na");
			}
			elseif($dc == 2)
			{
				$day = t("Homme");
			}
			elseif(date('w',$i) == 0)
			{
				$day = t("P&uuml;hap&auml;ev");
			}
			elseif(date('w',$i) == 1)
			{
				$day = t("Esmasp&auml;ev");
			}
			if($bdis)
			{
				$this->vars(array(
					"bds" => $tmp2,
					"day" => $day,
				));
				$tmp .= $this->parse("days");
			}
		}
		$this->vars(array(
			"days" => $tmp
		));
		return $this->parse();
	}

	/**
		@attrib name=create_new_company all_args="1"
	**/
	function create_new_company($arr)
	{
		$parent = -1;
		if(is_oid($arr['id']))
		{
			$parent = $arr['id'];
		}
		else if(is_oid($arr['category']))
		{
			$parent = $arr['category'];
		}

		$new_company = new object(array(
			'parent' => $parent,
		));
		$new_company->set_class_id(CL_CRM_COMPANY);
		$new_company->save();
		if(strlen(trim($arr['cs_n'])))
		{
			//the company GETS A NAME!!!
			$new_company->set_prop('name',trim($arr['cs_n']));
		}
		if(strlen(trim($arr['customer_search_reg'])))
		{
			//the company GETS A REGISTRATION NuMbEr
			$new_company->set_prop('reg_nr',trim($arr['customer_search_reg']));
		}

		if(!empty($arr['sector']) && is_oid($arr['sector']) && $this->can("view", $arr['sector']))
		{
			$sect = obj($arr['sector']);
			if ($sect->class_id() == CL_CRM_SECTOR)
			{
				$new_company->set_prop('pohitegevus', $arr['sector']);
			}
		}

		//won't create the address object and connection unless some fields from the
		//address really exist and are useable! i'll determine that and then try
		//to do the magic
		$has_address = false;
		$county = null;
		$county_name = '';
		$city = null;
		$city_name = '';
		$street = null;
		$street_name = '';

		//have to trim, explode county, city
		foreach(array('customer_search_county','customer_search_city') as $value)
		{
			if(isset($arr[$value]))
			{
				//let's clean up the item
				$tmp_arr = explode(',',$arr[$value]);
				array_walk($tmp_arr,create_function('&$param','$param = trim($param);'));
				array_walk($tmp_arr,create_function('&$param','$param = "%".$param."%";'));
				$arr[$value] = $tmp_arr;
			}
		}

		if(strlen(trim($arr['customer_search_county'])))
		{
			//i'll try to find a matching county, if i find multiple
			//i'll take the first one, if none is found i'll take no action
			//atleast for now

			$ol = new object_list(array(
				'class_id' => CL_CRM_COUNTY,
				'name'	=> $arr['customer_search_county'],
			));
			if(sizeof($ol->ids()))
			{
				list(,$county) = each($ol->ids());
				$county_name = $ol->list_names[$county];
			}

			$has_address = true;
		}

		if(strlen(trim($arr['customer_search_city'])))
		{
			$ol = new object_list(array(
				'class_id' => CL_CRM_CITY,
				'name' => $arr['customer_search_city'],
			));

			if(sizeof($ol->ids()))
			{
				list(,$city) = each($ol->ids());
				$city_name = $ol->list_names[$city];
			}
			$has_address = true;
		}

		if(strlen(trim($arr['customer_search_address'])))
		{
			$street = trim($arr['customer_search_address']);
			//just for consistency
			$street_name = &$street;
			$has_address = true;
		}

		if($has_address)
		{
			$address = new object(array(
				'parent' => $new_company->id(),
			));
			$address->set_class_id(CL_CRM_ADDRESS);
			if($street)
			{
				$address->set_prop('aadress',$street);
			}


			if($county)
			{
				//loome seose
				$address->connect(array(
					'to' => $county,
					'reltype' => 'RELTYPE_MAAKOND'
				));
				//kinnitame seose
				$address->set_prop('maakond',$county);
			}

			if($city)
			{
				//loome seose
				$address->connect(array(
					'to' => $city,
					'reltype' => 'RELTYPE_LINN'
				));
				//kinnitame seose
				$address->set_prop('linn',$city);
			}
			$address->set_prop('name', $street_name.' '.$city_name.' '.$county_name);
			$address->save();
			//kinnitame aadressi kompaniiga
			$new_company->connect(array(
				'to' => $address->id(),
				'reltype' => "RELTYPE_ADDRESS", //crm_company.reltype_address
			));
		}
		$new_company->save();

		//have to direct the user to the just created company
		$url = $this->mk_my_orb('change',array(
				'id' => $new_company->id(),
				"return_url" => $arr["post_ru"]
			),
			'crm_company'
		);
		header('Location: '.$url);
		die();
	}

	function callback_mod_retval(&$arr)
	{
		if($this->use_group === "stats_s" || $this->use_group === "stats")
		{
			$arr['args']['stats_s_cust_type'] = ($arr['request']['stats_s_cust_type']);
			$arr['args']['stats_s_cust'] = ($arr['request']['stats_s_cust']);
			$arr['args']['stats_s_area'] = ($arr['request']['stats_s_area']);
			$arr['args']['stats_s_proj'] = ($arr['request']['stats_s_proj']);
			$arr['args']['stats_s_state'] = ($arr['request']['stats_s_state']);
			$arr['args']['project_mgr'] = ($arr['request']['project_mgr']);
			$arr['args']['stats_s_time_sel'] = ($arr['request']['stats_s_time_sel']);
			$arr['args']['stats_s_from'] = ($arr['request']['stats_s_from']);
			$arr['args']['stats_s_to'] = ($arr['request']['stats_s_to']);
			$arr['args']['stats_s_worker'] = ($arr['request']['stats_s_worker']);
			$arr['args']['stats_s_worker_sel'] = ($arr['request']['stats_s_worker_sel']);
			$arr['args']['stats_s_res_type'] = ($arr['request']['stats_s_res_type']);
			$arr['args']['stats_s_bill_state'] = $arr['request']['stats_s_bill_state'];
			$arr['args']['stats_s_only_billable'] = ($arr['request']['stats_s_only_billable']);
			$arr['args']['stats_s_group_by_client'] = ($arr['request']['stats_s_group_by_client']);
			$arr['args']['stats_s_group_by_project'] = ($arr['request']['stats_s_group_by_project']);
			$arr['args']['stats_s_group_by_task'] = ($arr['request']['stats_s_group_by_task']);
			$arr['args']['MAX_FILE_SIZE'] = ($arr["request"]["MAX_FILE_SIZE"]);
		}
		elseif($this->use_group === "stats_stats" || $this->use_group === "stats")
		{
			$arr['args']['stats_stats_time_sel'] = ($arr['request']['stats_stats_time_sel']);
			$arr['args']['stats_stats_from'] = ($arr['request']['stats_stats_from']);
			$arr['args']['stats_stats_to'] = ($arr['request']['stats_stats_to']);
			$arr['args']['st'] = ($arr['request']['st']);
		}
		elseif($this->use_group === "ovrv_email")
		{
			$arr['args']['mail_s_subj'] = ($arr['request']['mail_s_subj']);
			$arr['args']['mail_s_body'] = ($arr['request']['mail_s_body']);
			$arr['args']['mail_s_to'] = ($arr['request']['mail_s_to']);
		}
		elseif($this->use_group === "stats_my")
		{
			$arr['args']['my_stats_s_type'] = ($arr['request']['my_stats_s_type']);
			$arr['args']['my_stats_s_from'] = ($arr['request']['my_stats_s_from']);
			$arr['args']['my_stats_s_to'] = ($arr['request']['my_stats_s_to']);
			$arr['args']['my_stats_s_time_sel'] = ($arr['request']['my_stats_s_time_sel']);
			$arr['args']['my_stats_s_cust'] = ($arr['request']['my_stats_s_cust']);
			$arr['args']['MAX_FILE_SIZE'] = ($arr["request"]["MAX_FILE_SIZE"]);
		}
		elseif("relorg" === substr($this->use_group, 0, 6))
		{
			if (isset($arr["request"]["cs_sbt"])) $arr["args"]["cs_sbt"] = $arr["request"]["cs_sbt"];
			if (isset($arr["request"]["cs_n"])) $arr["args"]["cs_n"] = $arr["request"]["cs_n"];
			if (isset($arr["request"]["customer_search_reg"])) $arr["args"]["customer_search_reg"] = $arr["request"]["customer_search_reg"];
			if (isset($arr["request"]["customer_search_address"])) $arr["args"]["customer_search_address"] = $arr["request"]["customer_search_address"];
		}

		if($this->do_search)
		{
			$arr['args']['contact_search_name'] = ($arr['request']['contact_search_name']);
			$arr['args']['contact_search_firstname'] = ($arr['request']['contact_search_firstname']);
			$arr['args']['contact_search_lastname'] = ($arr['request']['contact_search_lastname']);
			$arr['args']['contact_search_code'] = ($arr['request']['contact_search_code']);
			$arr['args']['contact_search_ext_id_alphanum'] = ($arr['request']['contact_search_ext_id_alphanum']);
			$arr['args']['contact_search_ext_id'] = ($arr['request']['contact_search_ext_id']);
			$arr['args']['contact_search'] = $this->do_search;
			$arr['args']['contacts_search_show_results'] = 1;
		}

		if (!empty($arr["request"]["proj_search_sbt"]))
		{
			$arr["args"]["proj_search_cust"] = $arr["request"]["proj_search_cust"];
			$arr["args"]["proj_search_part"] = $arr["request"]["proj_search_part"];
			$arr["args"]["proj_search_name"] = $arr["request"]["proj_search_name"];
			$arr["args"]["proj_search_code"] = $arr["request"]["proj_search_code"];
			$arr["args"]["proj_search_contact_person"] = $arr["request"]["proj_search_contact_person"];
			$arr["args"]["proj_search_task_name"] = $arr["request"]["proj_search_task_name"];
			$arr["args"]["proj_search_dl_from"] = $arr["request"]["proj_search_dl_from"];
			$arr["args"]["proj_search_dl_to"] = $arr["request"]["proj_search_dl_to"];
			$arr["args"]["proj_search_state"] = $arr["request"]["proj_search_state"];
			$arr["args"]["proj_search_sbt"] = 1;
			$arr["args"]["do_proj_search"] = 1;
		}

		if (!empty($arr["request"]["proj_search_change_mode_sbt"]))
		{
			$arr["args"]["proj_search_part"] = $arr["request"]["proj_search_part"];
			$arr["args"]["proj_search_name"] = $arr["request"]["proj_search_name"];
			$arr["args"]["proj_search_state"] = $arr["request"]["proj_search_state"];
			$arr["args"]["proj_search_sbt"] = 1;
			$arr["args"]["do_proj_search"] = 1;
			if ( aw_global_get('crm_projects_search_mode') == CRM_PROJECTS_SEARCH_DETAIL )
			{
				$_SESSION['crm_projects_search_mode'] = CRM_PROJECTS_SEARCH_SIMPLE;
				$this->set_cval( aw_global_get('uid').'_crm_projects_search_mode', CRM_PROJECTS_SEARCH_SIMPLE );
			}
			else
			{
				$_SESSION['crm_projects_search_mode'] = CRM_PROJECTS_SEARCH_DETAIL;
				$this->set_cval( aw_global_get('uid').'_crm_projects_search_mode', CRM_PROJECTS_SEARCH_DETAIL );
			}
		}

		if (!empty($arr["request"]["all_proj_search_change_mode_sbt"]))
		{
			$arr["args"]["all_proj_search_part"] = $arr["request"]["all_proj_search_part"];
			$arr["args"]["all_proj_search_name"] = $arr["request"]["all_proj_search_name"];
			$arr["args"]["all_proj_search_state"] = $arr["request"]["all_proj_search_state"];
			$arr["args"]["search_all_proj"] = 0;
			if ( aw_global_get('crm_projects_search_mode') == CRM_PROJECTS_SEARCH_DETAIL )
			{
				$_SESSION['crm_projects_search_mode'] = CRM_PROJECTS_SEARCH_SIMPLE;
				$this->set_cval( aw_global_get('uid').'_crm_projects_search_mode', CRM_PROJECTS_SEARCH_SIMPLE );
			}
			else
			{
				$_SESSION['crm_projects_search_mode'] = CRM_PROJECTS_SEARCH_DETAIL;
				$this->set_cval( aw_global_get('uid').'_crm_projects_search_mode', CRM_PROJECTS_SEARCH_DETAIL );
			}
		}

		if (!empty($arr["request"]["all_proj_search_clear"]))
		{
			$arr["args"]["search_all_proj"] = 0;
			$_SESSION['crm_projects_search_mode'] = CRM_PROJECTS_SEARCH_SIMPLE;
		}

		if (!empty($arr["request"]["all_proj_search_sbt"]))
		{
			$arr["args"]["all_proj_search_cust"] = $arr["request"]["all_proj_search_cust"];
			$arr["args"]["all_proj_search_part"] = $arr["request"]["all_proj_search_part"];
			$arr["args"]["all_proj_search_name"] = $arr["request"]["all_proj_search_name"];
			$arr["args"]["all_proj_search_code"] = $arr["request"]["all_proj_search_code"];
			$arr["args"]["all_proj_search_arh_code"] = $arr["request"]["all_proj_search_arh_code"];
			$arr["args"]["all_proj_search_contact_person"] = $arr["request"]["all_proj_search_contact_person"];
			$arr["args"]["all_proj_search_task_name"] = $arr["request"]["all_proj_search_task_name"];
			$arr["args"]["all_proj_search_dl_from"] = $arr["request"]["all_proj_search_dl_from"];
			$arr["args"]["all_proj_search_dl_to"] = $arr["request"]["all_proj_search_dl_to"];
			$arr["args"]["all_proj_search_end_from"] = $arr["request"]["all_proj_search_end_from"];
			$arr["args"]["all_proj_search_end_to"] = $arr["request"]["all_proj_search_end_to"];
			$arr["args"]["all_proj_search_state"] = $arr["request"]["all_proj_search_state"];
			$arr["args"]["search_all_proj"] = 0;
			$arr["args"]["aps_sbt"] = 1;
		}

		if (!empty($arr["request"]["docs_s_sbt"]))
		{
			$arr["args"]["docs_s_name"] = $arr["request"]["docs_s_name"];
			$arr["args"]["docs_s_type"] = $arr["request"]["docs_s_type"];
			$arr["args"]["docs_s_comment"] = $arr["request"]["docs_s_comment"];
			$arr["args"]["docs_s_created_after"] = mktime(0, 0, 0, $arr["request"]["docs_s_created_after"]["month"], $arr["request"]["docs_s_created_after"]["day"], $arr["request"]["docs_s_created_after"]["year"]);
			$arr["args"]["docs_s_task"] = $arr["request"]["docs_s_task"];
			$arr["args"]["docs_s_user"] = $arr["request"]["docs_s_user"];
			$arr["args"]["docs_s_name"] = $arr["request"]["docs_s_name"];
			$arr["args"]["docs_s_customer"] = $arr["request"]["docs_s_customer"];
			$arr["args"]["docs_s_sbt"] = $arr["request"]["docs_s_sbt"];
		}

		if (!empty($arr["request"]["act_s_sbt"]) || $this->use_group === "bills_search")
		{
			$arr["args"]["act_s_cust"] = $arr["request"]["act_s_cust"];
			$arr["args"]["act_s_part"] = $arr["request"]["act_s_part"];
			$arr["args"]["act_s_cal_name"] = $arr["request"]["act_s_cal_name"];
			$arr["args"]["act_s_task_name"] = $arr["request"]["act_s_task_name"];
			$arr["args"]["act_s_task_content"] = $arr["request"]["act_s_task_content"];
			$arr["args"]["act_s_code"] = $arr["request"]["act_s_code"];
			$arr["args"]["act_s_proj_name"] = $arr["request"]["act_s_proj_name"];
			$arr["args"]["act_s_dl_from"] = $arr["request"]["act_s_dl_from"];
			$arr["args"]["act_s_dl_to"] = $arr["request"]["act_s_dl_to"];
			$arr["args"]["act_s_status"] = $arr["request"]["act_s_status"];
			$arr["args"]["act_s_print_view"] = $arr["request"]["act_s_print_view"];
			$arr["args"]["act_s_sbt"] = $arr["request"]["act_s_sbt"];
			$arr["args"]["act_s_is_is"] = 1;

			$arr["args"]["act_s_mail_content"] = $arr["request"]["act_s_mail_content"];
			$arr["args"]["act_s_mail_name"] = $arr["request"]["act_s_mail_name"];
		}

		if (!empty($arr["request"]["bill_s_search"]))
		{
			$arr["args"]["bill_s_cust"] = $arr["request"]["bill_s_cust"];
			$arr["args"]["bill_s_bill_no"] = $arr["request"]["bill_s_bill_no"];
			$arr["args"]["bill_s_bill_to"] = $arr["request"]["bill_s_bill_to"];
			$arr["args"]["show_bill_balance"] = $arr["request"]["show_bill_balance"];
			$arr["args"]["currency_grouping"] = $arr["request"]["currency_grouping"];
			$arr["args"]["bill_s_from"] = $arr["request"]["bill_s_from"];
			$arr["args"]["bill_s_to"] = $arr["request"]["bill_s_to"];
			$arr["args"]["bill_s_client_mgr"] = $arr["request"]["bill_s_client_mgr"];
			$arr["args"]["bill_s_with_tax"] = $arr["request"]["bill_s_with_tax"];
			$arr["args"]["bill_s_status"] = $arr["request"]["bill_s_status"];
			$arr["args"]["bill_s_search"] = $arr["request"]["bill_s_search"];
		}

		if (!empty($arr["request"]["bill_payments_search"]))
		{
			$arr["args"]["bill_payments_cust"] = $arr["request"]["bill_payments_cust"];
			$arr["args"]["bill_payments_bill_no"] = $arr["request"]["bill_payments_bill_no"];
			$arr["args"]["bill_payments_bill_to"] = $arr["request"]["bill_payments_bill_to"];
			$arr["args"]["bill_payments_from"] = $arr["request"]["bill_payments_from"];
			$arr["args"]["bill_payments_to"] = $arr["request"]["bill_payments_to"];
			$arr["args"]["bill_payments_client_mgr"] = $arr["request"]["bill_payments_client_mgr"];
			$arr["args"]["bill_payments_search"] = $arr["request"]["bill_payments_search"];
		}

		if(!empty($arr["request"]["billable_search_button"]))
		{
			$arr['args']['billable_start'] = ($arr['request']['billable_start']);
			$arr['args']['billable_end'] = ($arr['request']['billable_end']);
		}

		if(!empty($arr['request']['unit']))
		{
			$arr['args']['unit'] = $arr['request']['unit'];
		}

		if(!empty($arr['request']['category']))
		{
			$arr['args']['category'] = $arr['request']['category'];
		}

		if(!empty($arr['request']['cat']))
		{
			$arr['args']['cat'] = $arr['request']['cat'];
		}
	}

//XXX: teadmata otstarbega asi
	// /**
		// @attrib name=save_search_results
	// **/
	function save_search_results($arr)
	{exit("N/A");
		foreach($arr['check'] as $key=>$value)
		{
			if(!empty($arr['unit']))
			{
				$section = obj($arr['unit'], array(), CL_CRM_SECTION);
			}

			$person = new object($value);
			$person->add_work_relation(array(
				"org" => $arr['id'],
				"section" => $section,
			));


			if ($arr["cat"] && $cat != 999999)
			{
				$person->connect(array(
					"to" => $arr["cat"],
					"reltype" => 7
				));
			}

			// run user creation
			$cuc = new crm_user_creator();
			$cuc->on_save_person(array(
				"oid" => $person->id()
			));
		}

		return $this->mk_my_orb('change',array(
				'id' => $arr['id'],
				'unit' => $arr['unit'],
				'cat' => $arr['cat'],
				'group' => $arr['group'],
			),
			$arr['class']
		);
 	}

	//goes through all the relations and builds a set of id into $data
	// FIXME category is an unknown parameter
	// obj seems to be a parameter representing a category from which to get customers
	function get_customers_for_company($obj, &$data, $category = false)
	{
		if (!$category)
		{
			$impl = array();
			$this->get_all_workers_for_company($obj, $impl);
			$impl[] = $obj->id();
			// also, add all orderers from projects where the company is implementor
			$ol = new object_list(array(
				"class_id" => CL_PROJECT,
				"CL_PROJECT.RELTYPE_IMPLEMENTOR" => $impl,
				"lang_id" => array(),
				"site_id" => array()
			));
			foreach($ol->arr() as $o)
			{
				foreach((array)$o->prop("orderer") as $ord)
				{
					if ($ord)
					{
						$data[$ord] = $ord;
					}
				}
			}
		}

		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_CUSTOMER",
		));
		foreach($conns as $conn)
		{
			$data[$conn->prop('to')] = $conn->prop('to');
		}

		//let's look through the categories
		$conns = $obj->connections_from(array(
			"type" => "RELTYPE_CATEGORY",
		));
		foreach($conns as $conn)
		{
			$obj = new object($conn->prop('to'));
			$this->get_customers_for_company($obj,$data,true);
		}
	}

	/*
		arr
			id - id of the company who's projects we wan't
	*/
	function get_all_projects_for_company($arr)
	{
		if(is_oid($arr['id']))
		{
			$company = new object($arr['id']);

			$conns = $company->connections_from(array(
				"type" => "RELTYPE_PROJECT",
			));

			$projects = array();

			foreach($conns as $conn)
			{
				$projects[$conn->prop('to')] = $conn->to();
			}
			return $projects;
		}
		else
		{
			return array();
		}
	}

	/**
		DEPRECATED use crm_company_obj::get_all_org_customer_categories() instead!
	**/
	public static function get_all_org_customer_categories($obj)
	{
		return $obj->get_all_org_customer_categories();
	}

	function get_customers_for_category($cat_id)
	{
		if($cat_id)
		{
			$cat_obj = obj($cat_id);
			$conns = $cat_obj->connections_from(array(
				"type" => "RELTYPE_CUSTOMER",
				"sort_by" => "to.name"
			));
			foreach ($conns as $conn)
			{
				$retval[$conn->prop("to")] = $conn->prop("to");
			}
			return $retval;
		}
		return false;
	}

	/**
		@attrib name=add_employee

		@param id required type=oid
		@param profession required type=oid
		@param save_autoreturn optional type=bool
		@param return_url optional type=string
	**/
	public function add_employee($arr)
	{
		try
		{
			$this_o = obj($arr["id"], array(), CL_CRM_COMPANY);
			$profession = empty($arr["profession"]) ? null : obj($arr["profession"], array(), CL_CRM_PROFESSION);
			$work_rel = $this_o->add_employee($profession);

			$params = array();
			if (isset($arr["return_url"])) $params["return_url"] = $arr["return_url"];
			if (isset($arr["save_autoreturn"])) $params["save_autoreturn"] = $arr["save_autoreturn"];

			$r = html::get_change_url($work_rel->prop("employee"), $params);
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("Viga. T&ouml;&ouml;tajat ei lisatud"));
			$r = $arr["return_url"];
		}

		return $r;
	}

	/**
		@attrib name=add_profession

		@param id required type=oid
		@param section optional type=oid
		@param save_autoreturn optional type=bool
		@param return_url optional type=string
	**/
	public function add_profession($arr)
	{
		try
		{
			$this_o = obj($arr["id"], array(), CL_CRM_COMPANY);
			$section = empty($arr["section"]) ? null : obj($arr["section"], array(), CL_CRM_SECTION);
			$profession = $this_o->add_profession($section);

			$params = array();
			if (isset($arr["return_url"])) $params["return_url"] = $arr["return_url"];
			if (isset($arr["save_autoreturn"])) $params["save_autoreturn"] = $arr["save_autoreturn"];

			$r = html::get_change_url($profession->id(), $params);
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("Viga. Ametikohta ei lisatud"));
			$r = $arr["return_url"];
		}

		return $r;
	}

	/**
		@attrib name=add_section

		@param id required type=oid
		@param parent_section optional type=oid
		@param save_autoreturn optional type=bool
		@param return_url optional type=string
	**/
	public function add_section($arr)
	{
		$this_o = obj($arr["id"], array(), CL_CRM_COMPANY);
		$parent_section = empty($arr["parent_section"]) ? null : obj($arr["parent_section"], array(), CL_CRM_SECTION);
		$section = $this_o->add_section($parent_section);

		$params = array();
		if (isset($arr["return_url"])) $params["return_url"] = $arr["return_url"];
		if (isset($arr["save_autoreturn"])) $params["save_autoreturn"] = $arr["save_autoreturn"];

		return html::get_change_url($section->id(), $params);
	}

	/**
		@attrib name=add_customer_category

		@param id required type=oid
		@param c optional type=oid
			Parent category
		@param t optional type=oid
			Category type (one of crm_category_obj::TYPE_...)
		@param save_autoreturn optional type=bool
		@param return_url optional type=string
	**/
	public function add_customer_category($arr)
	{
		try
		{
			$this_o = obj($arr["id"], array(), CL_CRM_COMPANY);

			if (empty($arr["c"]) or $arr["c"] === $arr["id"])
			{
				$parent_category = null;
			}
			else
			{
				$parent_category = obj($arr["c"], array(), CL_CRM_CATEGORY);
			}

			$type = empty($arr["t"]) ? crm_category_obj::TYPE_GENERIC : (int) $arr["t"];
			$category = $this_o->add_customer_category($parent_category, $type);

			$params = array();
			if (isset($arr["return_url"])) $params["return_url"] = $arr["return_url"];
			$r = html::get_change_url($category->id(), $params);
		}
		catch (Exception $e)
		{//TODO: distinguish different exceptions
			$this->show_error_text(t("Viga. Kategooriat ei lisatud"), $e);
			$r = $arr["return_url"];
		}

		return $r;
	}

	/**
		@attrib name=cut
	**/
	function cut($arr)
	{
		if (isset($arr["group"]) and ("employees_management" === $arr["group"] or "employees" === $arr["group"]))
		{
			$employees_view = new crm_company_employees_view();
			$employees_view->set_request($this->req);
			$r = $employees_view->cut($arr);
		}
		else
		{
			$_SESSION["crm_cut"] = $arr["select"];
			$r = $this->mk_my_orb("change", array(
				"id" => $arr["id"],
				"group" => $arr["group"]), CL_CRM_COMPANY);
		}
		return $r;
	}

	/**
		@attrib name=paste all_args=1
	**/
	function paste($arr)
	{
		if (isset($arr["group"]) and ("employees_management" === $arr["group"] or "employees" === $arr["group"]))
		{
			$employees_view = new crm_company_employees_view();
			$employees_view->set_request($this->req);
			$r = $employees_view->paste($arr);
		}
		else
		{
			foreach ($_SESSION["crm_cut"] as $oid)
			{
				$obj = obj($oid);
				$obj->set_parent($arr["parent"]);
				$obj->save();
			}
			unset($_SESSION["crm_cut"]);
			$r = $this->mk_my_orb("change", array(
					"id" => $arr["id"],
					"group" => $arr["group"],
					"parent" => $arr["parent"],
				),
				CL_CRM_COMPANY
			);
		}
		return $r;
	}

	/**
		@attrib name=customer_view_cut
		@param cust_check optional type=array
		@param cat_check optional type=array
		@param cs_c required type=string
		@param post_ru required type=string
	**/
	function customer_view_cut($arr)
	{
		$check = array();

		if (isset($arr["cust_check"]))
		{
			$check += $arr["cust_check"];
		}

		if (isset($arr["cat_check"]))
		{
			$check += $arr["cat_check"];
		}

		aw_session::set("awcb_clipboard_action", "cut");
		aw_session::set("awcb_customer_selection_clipboard", $check);
		aw_session::set("awcb_category_old_parent", (isset($arr["cs_c"]) ? $arr["cs_c"] : ""));
		return $arr["post_ru"];
	}

	/**
		@attrib name=customer_view_copy
		@param cust_check optional type=array
		@param cat_check optional type=array
		@param cs_c required type=string
		@param post_ru required type=string
	**/
	function customer_view_copy($arr)
	{
		$check = array();

		if (isset($arr["cust_check"]))
		{
			$check = array_merge($check, $arr["cust_check"]);
		}

		if (isset($arr["cat_check"]))
		{
			$check = array_merge($check, $arr["cat_check"]);
		}

		aw_session::set("awcb_clipboard_action", "copy");
		aw_session::set("awcb_customer_selection_clipboard", $check);
		aw_session::set("awcb_category_old_parent", (isset($arr["cs_c"]) ? $arr["cs_c"] : ""));
		return $arr["post_ru"];
	}

	/**
		@attrib name=customer_view_paste
		@param id required type=oid acl=view
			This object id
		@param cs_c required type=string
		@param post_ru required type=string
	**/
	function customer_view_paste($arr)
	{
		$errors = array();
		$action = aw_session::get("awcb_clipboard_action");
		$this_object = obj($arr["id"], array(), crm_company_obj::CLID);

		if (aw_session::get("awcb_customer_selection_clipboard"))
		{
			try
			{
				// get old parent
				if (is_oid(aw_session::get("awcb_category_old_parent")))
				{
					$old_parent = aw_session::get("awcb_category_old_parent");
					try
					{
						$old_parent = obj($old_parent, array(), crm_category_obj::CLID);
					}
					catch (awex_obj_class $e)
					{
						$old_parent = null;
					}
				}
				else
				{
					$old_parent = null;
				}

				// find new parent
				$new_parent = empty($arr["cs_c"]) ? $arr[self::REQVAR_CATEGORY] : $arr["cs_c"];
				if (is_oid($new_parent))
				{
					try
					{
						$new_parent = obj($new_parent, array(), crm_category_obj::CLID);
						$new_parent_oid = $new_parent->id();
					}
					catch (awex_obj_class $e)
					{
						$new_parent = null;
						$new_parent_oid = 0;
					}
				}
				else
				{
					$this->show_error_text(t("Kategooria valimata"));
					return $arr["post_ru"];
				}

				// perform action on objects
				$selected_objects = aw_session::get("awcb_customer_selection_clipboard");
				foreach ($selected_objects as $oid)
				{
					if ($oid)
					{
						try
						{
							$o = new object($oid);
							if ($o->is_a(crm_company_customer_data_obj::CLID)) // process cut/copied customer objects
							{ // move or copy customer to new category. if new category not given, just remove from old.
								if ("cut" === $action  and $old_parent)
								{ // cut action requested -- remove old category
									$o->remove_category($old_parent);
								}

								if ($new_parent)
								{ // if new parent given, add that category to customer's categories
									$o->add_category($new_parent);
								}
							}
							elseif ($o->is_a(crm_category_obj::CLID)) // process cut/copied categories
							{ // replace category parent category
								if (
									"copy" === $action and
									$o->prop("parent_category") != $new_parent // avoid paste (making a copy with same name) to where copied category originally is
								)
								{ // add a new category by same name
									$category_copy = $this_object->add_customer_category($new_parent, (int) $o->prop("category_type"));
									$category_copy->set_name($o->name());
									$o = $category_copy;
								}

								$o->set_prop("parent_category", $new_parent_oid);
								$o->save();
							}
							else
							{
								$errors[] = "({$oid})";
							}
						}
						catch (Exception $e)
						{
							trigger_error("Caught exception " . get_class($e) . ". Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": '" . $e->getMessage() . "' <br /> Backtrace:<br />" . dbg::process_backtrace($e->getTrace(), -1, true), E_USER_WARNING);
							$errors[] = $oid;
						}
					}
				}
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Kleepimiskoht defineerimata"));
				trigger_error("Caught exception " . get_class($e) . ". Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": '" . $e->getMessage() . "' <br /> Backtrace:<br />" . dbg::process_backtrace($e->getTrace(), -1, true), E_USER_WARNING);
			}
		}

		aw_session::del("awcb_clipboard_action");
		aw_session::del("awcb_customer_selection_clipboard");
		aw_session::del("awcb_category_old_parent");

		if (count($errors))
		{
			$this->show_error_text(sprintf(t("Viga kleebitava(te) objekti(de) lugemisel [%s]"), implode(", ", $errors)));
		}

		return $arr["post_ru"];
	}

	/** Returns an array of all sections that the company has
		@attrib api=1 params=pos
		@param obj required type=object
			The company to return sections for

		@returns
			Array of section id's that the company has
	**/
	function get_all_org_sections($obj)
	{
		$retval = array();
		foreach ($obj->connections_from(array("type" => "RELTYPE_SECTION")) as $section)
		{
			$retval[$section->prop("to")] = $section->prop("to");
			$section_obj = $section->to();
			$this->get_all_org_sections($section_obj);
		}
		return $retval;
	}

	/** cuts the selected person objects

		@attrib name=cut_p

	**/
	function cut_p($arr)
	{
		// in cut, we must remember the unit/profession from where the person was cut
		// unit is unit, cat is profession
		unset($_SESSION["crm_cut_p"]);
		if (!empty($arr["check"]))
		{
			foreach(safe_array($arr["check"]) as $p_id)
			{
				$_SESSION["crm_cut_p"][$p_id] = array(
					"unit" => $arr["unit"],
					"profession" => $arr["cat"]
				);
			}
		}

		return $arr["post_ru"];
	}

	/** copies the selected person objects

		@attrib name=copy_p

	**/
	function copy_p($arr)
	{
		// in copy we must just remember the person
		$msg_text1 = $msg_text2 = "";

		unset($_SESSION["crm_copy_p"]);
		foreach(safe_array($arr["check"]) as $p_id)
		{
			$copied_object = obj($p_id);

			if ($copied_object->is_a(CL_CRM_PROFESSION))
			{
				$msg_text1 = t("Ametinimetusi ei saa kopeerida. ");
			}
			elseif ($copied_object->is_a(CL_CRM_SECTION))
			{
				$msg_text2 = t("&Uuml;ksusi ei saa kopeerida. ");
			}
			else
			{
				$_SESSION["crm_copy_p"][$p_id] = $p_id;
			}
		}

		if ($msg_text1 or $msg_text2)
		{
			$this->show_msg_text($msg_text1 . $msg_text2);
		}

		return $arr["post_ru"];
	}

	/** pastes the cut/copied person objects

		@attrib name=paste_p

	**/
	function paste_p($arr)
	{
		try
		{
			$this_o = obj($arr["id"], array(), CL_CRM_COMPANY);
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("Antud organisatsioon pole loetav."));
			unset($_SESSION["crm_cut_p"]);
			unset($_SESSION["crm_copy_p"]);
			return $arr["post_ru"];
		}

		$errors = false;
		$error_object_names = array();
		$msg_text1 = $msg_text2 = "";

		// first cut objects
		if (isset($_SESSION["crm_cut_p"]))
		{
			foreach(safe_array($_SESSION["crm_cut_p"]) as $p_id => $p_from)
			{
				try
				{
					$cut_object = obj($p_id);
				}
				catch (Exception $e)
				{
					$errors = true;
				}

				if ($cut_object->is_a(crm_person_obj::CLID))
				{
					try
					{
						if (empty($arr["cat"]))
						{
							throw new Exception();
						}

						$profession = obj($arr["cat"], array(), CL_CRM_PROFESSION);
						$this_o->add_employee($profession, $cut_object);

						$old_profession = obj($p_from["profession"], array(), CL_CRM_PROFESSION);
						$old_work_relations = crm_person_work_relation_obj::find($cut_object, $old_profession);

						if($old_work_relations->count())
						{
							$old_work_relation = $old_work_relations->begin();

							do
							{
								$old_work_relation->finish();
							}
							while ($old_work_relation = $old_work_relations->next());
						}

					}
					catch (Exception $e)
					{
						$error_object_names[] = $cut_object->name();
					}
				}
				elseif ($cut_object->is_a(CL_CRM_PROFESSION))
				{
					try
					{
						if (empty($arr["unit"]))
						{ // moving section to top level in company
							$cut_object->set_prop("parent_section", 0);
							$cut_object->save();
						}
						else
						{ // moving section under a different section
							$new_parent_section = obj($arr["unit"], array(), CL_CRM_SECTION);
							$cut_object->set_prop("parent_section", $new_parent_section->id());
							$cut_object->save();
						}
					}
					catch (Exception $e)
					{
						$error_object_names[] = $cut_object->name();
					}
				}
				elseif ($cut_object->is_a(CL_CRM_SECTION))
				{
					try
					{
						if (empty($arr["unit"]))
						{ // moving section to top level in company
							$cut_object->set_prop("parent_section", 0);
							$cut_object->save();
						}
						else
						{ // moving section under a different section
							$new_parent_section = obj($arr["unit"], array(), CL_CRM_SECTION);
							$cut_object->set_prop("parent_section", $new_parent_section->id());
							$cut_object->save();
						}
					}
					catch (Exception $e)
					{
						$error_object_names[] = $cut_object->name();
					}
				}
			}
		}

		// now copied objects
		if (isset($_SESSION["crm_copy_p"]))
		{
			foreach(safe_array($_SESSION["crm_copy_p"]) as $p_id)
			{
				try
				{
					$copied_object = obj($p_id);
				}
				catch (Exception $e)
				{
					$errors = true;
				}

				if ($copied_object->is_a(crm_person_obj::CLID))
				{ // just create a new work relation
					try
					{
						if (empty($arr["cat"]))
						{
							throw new Exception();
						}

						$profession = obj($arr["cat"], array(), CL_CRM_PROFESSION);
						$this_o->add_employee($profession, $copied_object);
					}
					catch (Exception $e)
					{
						$error_object_names[] = $copied_object->name();
					}
				}
				elseif ($copied_object->is_a(CL_CRM_PROFESSION))
				{
					$msg_text1 = t("Ametinimetusi ei saa kopeerida. ");
				}
				elseif ($copied_object->is_a(CL_CRM_SECTION))
				{
					$msg_text2 = t("&Uuml;ksusi ei saa kopeerida. ");
				}
			}
		}

		if ($msg_text1 or $msg_text2)
		{
			$this->show_msg_text($msg_text1 . $msg_text2);
		}

		if ($errors)
		{
			$this->show_error_text(t("Osa valitud objekte polnud loetavad."));
		}

		if (count($error_object_names))
		{
			$this->show_error_text(t("Esinesid vead osa valitud objektide 'kleepimisel' (". implode(", ", $error_object_names) .")"));
		}

		unset($_SESSION["crm_cut_p"]);
		unset($_SESSION["crm_copy_p"]);
		return $arr["post_ru"];
	}

	function _get_firmajuht($arr)
	{
		$arr["prop"]["options"] = $this->get_employee_picker($arr["obj_inst"]);
		if ($arr["request"]["action"] === "view")
		{
			$arr["prop"]["value"] = html::obj_change_url($arr["prop"]["value"]);
		}
	}

	function navtoolbar(&$args)
	{
		$toolbar = $args["prop"]["toolbar"];
		$default_parent = $args['obj_inst']->parent();

		if (!empty($this->cal_id))
		{
			$user_calendar = new object($this->cal_id);
			$parents[12] = $parents[11] = $parents[10] = $parents[13] = $user_calendar->prop('event_folder');
		}

		$toolbar->add_menu_button(array(
			"name" => "main_menu",
			"tooltip" => t("Uus"),
		));

		$toolbar->add_sub_menu(array(
			"parent" => "main_menu",
			"name" => "calendar_sub",
			"text" => aw_ini_get("classes." . CL_PLANNER . ".name")
		));

		$toolbar->add_sub_menu(array(
			"parent" => "main_menu",
			"name" => "firma_sub",
			"text" => aw_ini_get("classes.{$this->clid}.name")
		));

		//3 == crm_company.reltype_address=3 //RELTYPE_WORKERSRELTYPE_JOBS
		$alist = array(8, "RELTYPE_ADDRESS", 19);
		foreach($alist as $key => $val)
		{
			$clids = $this->relinfo[$val]["clid"];
			if (is_array($clids))
			{
				foreach($clids as $clid)
				{
					$classinf = aw_ini_get("classes.{$clid}");

					$url = $this->mk_my_orb('new',array(
						'alias_to' => $args['obj_inst']->id(),
						'reltype' => $val,
						'title' => $classinf["name"].' : '.$args['obj_inst']->name(),
						'parent' => isset($parents[$val]) ? $parents[$val] : $default_parent,
						'return_url' => get_ru()
					),$clid);

					$has_parent = !empty($parents[$val]);
					$disabled = $has_parent ? false : true;
					$toolbar->add_menu_item(array(
						"parent" => "firma_sub",
						"text" => $classinf["name"],
						"link" => $has_parent ? $url : "",
						"title" => $has_parent ? "" : t("Kataloog m&auml;&auml;ramata"),
						"disabled" => $has_parent ? false : true
					));
				}
			}
		}

		// aha, I need to figure out which objects can be added to that relation type

		// basically, I need to create a list of relation types that are of any
		// interest to me and then get a list of all classes for those

		//$action = array(RELTYPE_DEAL,RELTYPE_KOHTUMINE,RELTYPE_CALL,RELTYPE_TASK);
		$action = array(/*10,*/ 11, 12, 13);
		foreach($action as $key => $val)
		{
			$clids = $this->relinfo[$val]["clid"];
			$reltype = $this->relinfo[$val]["value"];
			if (is_array($clids))
			{
				foreach($clids as $clid)
				{
					$classinf = aw_ini_get("classes.{$clid}");
					$url = $this->mk_my_orb('new',array(
						// alright then. so what do those things to?
						// they add a relation between the object created through
						// the planner and this object


						// can I do that with messages instead? and if I can, how
						// on earth am I going to do that?

						// I'm adding an event object to a calendar, how do I know
						// that I will have to attach it to an organization as well?

						// Maybe I should attach it directly to the organization and
						// then send a message somehow that it should be put in my
						// calendar as well .. hm that actually does sound
						// like a solution.
						'alias_to_org' => $args['obj_inst']->id(),
						'reltype_org' => $reltype,
						'class' => 'planner',
						'id' => $this->cal_id,
						'group' => 'add_event',
						'clid' => $clid,
						'action' => 'change',
						'title' => $classinf["name"].': '.$args['obj_inst']->name(),
						'parent' => isset($parents[$reltype]) ? $parents[$reltype] : $default_parent,
						'return_url' => get_ru()
					));
					$has_parent = isset($parents[$val]) && $parents[$val];
					$disabled = $has_parent ? false : true;
					$toolbar->add_menu_item(array(
						"parent" => "calendar_sub",
						"title" => $has_parent ? "" : t("Kalender v&otilde;i kalendri s&uuml;ndmuste kataloog m&auml;&auml;ramata"),
						"text" => $classinf["name"],
						"disabled" => $has_parent ? false : true,
						"link" => $has_parent ? $url : "",
					));
				}
			}
		}

		$my_org = get_current_company();
		if ($my_org)
		{
			$toolbar->add_menu_item(array(
				"parent" => "calendar_sub",
				"title" => t("Pakkumine"),
				"text" => t("Pakkumine"),
				"link" => $this->mk_my_orb("new", array(
					"alias_to_org" => $args["obj_inst"]->id(),
					"alias_to" => $my_org->id(),
					"reltype" => 9
				), CL_CRM_OFFER),
			));
		}

		if (!empty($this->cal_id))
		{
			$toolbar->add_button(array(
				"name" => "user_calendar",
				"tooltip" => t("Kasutaja kalender"),
				"url" => $this->mk_my_orb('change', array(
						'id' => $this->cal_id,
						'return_url' => get_ru(),
						"group" => "views"
					),'planner'),
				"onClick" => "",
				"img" => "icon_cal_today.gif",
				"class" => "menuButton"
			));
		}
	}

	function do_offer_tree_leafs($tree,$obj,$this_level_id,&$node_id)
	{
		if ($obj->class_id() == CL_CRM_COMPANY)
		{
			return;
		}

		$customers = $this->get_customers_for_category($obj->id());
		if(is_array($customers))
		{
			foreach ($customers as $customer)
			{
				$cobj = obj($customer);
				$tree->add_item($this_level_id, array(
					'id' => ++$node_id,
					'iconurl' => icons::get_icon_url($cobj->class_id()),
					'name' => $cobj->id() == $_GET["org_id"] ? "<b>".$cobj->name()."</b>" : $cobj->name(),
					'url' => aw_url_change_var("org_id", $cobj->id(), $_GET["real_url"])
				));
			}
		}
	}

	/**
		@attrib name=mark_proj_done
	**/
	function mark_proj_done($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array("oid" => $arr["sel"]));
			$ol->foreach_o(array("func" => "set_prop", "params" => array("state", PROJ_DONE), "save" => true));
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=mark_tasks_done
		@param sel optional
		@param post_ru required
	**/
	function mark_tasks_done($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array("site_id" => array(), "lang_id" => array(), "oid" => $arr["sel"]));
			$ol->foreach_o(array("func" => "set_prop", "params" => array("is_done", OBJ_IS_DONE), "save" => true));
		}
		return $arr["post_ru"];
	}

	function get_my_projects()
	{
		$conns = new connection();
		$conns_ar = $conns->find(array(
			"from.class_id" => CL_PROJECT,
			"to" => aw_global_get("uid_oid"),
			"type" =>  2,
		));
		$conns_ol = new object_list();
		foreach($conns_ar as $con)
		{
			$conns_ol->add($con["from"]);
		}

		$u = get_instance(CL_USER);
		$pers = $u->get_current_person();
		$conns_ar = $conns->find(array(
			"from.class_id" => CL_PROJECT,
			"to" => $pers,
			"type" =>  2,
		));
		foreach($conns_ar as $con)
		{
			$conns_ol->add($con["from"]);
		}

		if ($conns_ol->count())
		{
			$conns_ol = new object_list(array(
				"oid" => $conns_ol->ids(),
				"class_id" => CL_PROJECT,
				"state" => new obj_predicate_not(PROJ_DONE)
			));
		}
		return $conns_ol->ids();
	}

	function get_my_customers($co = NULL)
	{
		$projs = $this->get_my_projects();

		$c = new connection();
		$conns = $c->find(array(
			"from.class_id" => CL_PROJECT,
			"type" => "RELTYPE_PARTICIPANT",
			"from" => $projs,
			"to.class_id" => array(CL_CRM_COMPANY, crm_person_obj::CLID)
		));

		$ret = array();
		foreach($conns as $c)
		{
			if ($c["to.class_id"] == crm_person_obj::CLID)
			{
				$p = obj($c["to"]);
				if (!$p->prop("is_customer"))
				{
					continue;
				}
			}
			$ret[] = $c["to"];
		}

		// add all customers to whom I am cust mgr
		$u = new user();
		$p = $u->get_current_person();
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"client_manager" => $p
		));
		foreach($ol->arr() as $customer_relation_o)
		{
			$ret[] = $customer_relation_o->prop("seller");
		}

		$ret = array_unique($ret);
		return $ret;
	}

	function get_my_tasks($only_not_done = false, $arr = array())
	{
		$u = get_instance(CL_USER);
		$c = new connection();
		$cs = $c->find(array(
			"from" => $u->get_current_person(),
			"from.class_id" => crm_person_obj::CLID,
			"type" => "RELTYPE_PERSON_TASK",
		));
		$ids = array();
		foreach($cs as $c)
		{
			$ids[] = $c["to"];
		}
		if(!empty($ids))
		{
			$params = array(
				"oid" => $ids,
				"site_id" => array(),
				"lang_id" => array(),
				"class_id" => CL_TASK,
				"flags" => array("mask" => OBJ_IS_DONE, "flags" => 0)
			);
			if($arr["prop"]["type"] == "calendar")
			{
				if($d = $arr["request"]["date"])
				{
					$tmp = explode("-", $d);
					$time = mktime(0, 0, 1, $tmp[1], $tmp[0], $tmp[2]);
				}
				else
				{
					$time = time();
				}
				$from = mktime(0, 0, 1, date('m', $time) - 1, 1, date('Y', $time));
				$to = mktime(0, 0, 1, date('m', $time) + 1, date('t', mktime(0, 0, 1, date('m', $time)+1, 1, date('Y', $time))), date('Y', $time));
				$params[] = new obj_predicate_compare(OBJ_COMP_IN_TIMESPAN, array("start1", "end"), array($from, $to));
			}
			$ol = new object_list($params);
			return $ol->ids();
		}
		return array();
	}

	function get_my_meetings($arr = array())
	{
		$u = get_instance(CL_USER);
		$c = new connection();
		$cs = $c->find(array(
			"from" => $u->get_current_person(),
			"from.class_id" => crm_person_obj::CLID,
			"type" => "RELTYPE_PERSON_MEETING",
		));
		$oids = array();
		foreach($cs as $c)
		{
			$oids[] = $c["to"];
		}
		$ret = $this->get_events_from_oids($oids, array(CL_CRM_MEETING), $arr);
		return $ret;
	}

	function get_my_calls($arr = array())
	{
		$u = get_instance(CL_USER);
		$c = new connection();
		$cs = $c->find(array(
			"from" => $u->get_current_person(),
			"from.class_id" => crm_person_obj::CLID,
			"type" => "RELTYPE_PERSON_CALL",
		));
		$oids = array();
		foreach($cs as $c)
		{
			$oids[] = $c["to"];
		}
		$ret = $this->get_events_from_oids($oids, array(CL_CRM_CALL), $arr);
		return $ret;
	}

	function get_my_bugs($arr = array())
	{
		$u = get_instance(CL_USER);
		$c = new connection();
		$cs = $c->find(array(
			"to" => $u->get_current_person(),
			"from.class_id" => CL_BUG,
			"type" => "RELTYPE_MONITOR",
			"bug_status" => array(1,2,10,11),
		));
		$ret = array();
		foreach($cs as $c)
		{
			$ret[] = $c["from"];
		}
		return $ret;
	}

	function get_my_offers()
	{
		$u = get_instance(CL_USER);
		$c = new connection();
		$cs = $c->find(array(
			"to" => $u->get_current_person(),
			"from.class_id" => CL_CRM_OFFER,
			"type" => "RELTYPE_SALESMAN",
		));
		$ret = array();
		foreach($cs as $c)
		{
			$ret[] = $c["from"];
		}
		return $ret;
	}

	function get_my_actions($arr = array())
	{
		$u = get_instance(CL_USER);
		$cp = $u->get_current_person();
		$c = new connection();
		$cs = $c->find(array(
			"from" => $cp,
			"from.class_id" => crm_person_obj::CLID,
			"type" => array("RELTYPE_PERSON_TASK", "RELTYPE_PERSON_MEETING", "RELTYPE_PERSON_CALL"),
		));

		$oids = array();
		foreach($cs as $c)
		{
			if ($this->can("view", $c["to"]))
			{
				$oids[] = $c["to"];
			}
		}
		$ret = $this->get_events_from_oids($oids, array(CL_TASK, CL_CRM_MEETING, CL_CRM_CALL), $arr);
		$cali = get_instance(CL_PLANNER);
		$calid = $cali->get_calendar_for_user();
		if($calid)
		{
			$cal = obj($calid);
			$eec = $cal->prop("event_entry_classes");
			if($eec[CL_BUG])
			{
				$cp = $u->get_current_person();
				$ol = new object_list(array(
					"class_id" => CL_BUG,
					new object_list_filter(array(
						"logic" => "OR",
						"conditions" => array(
							"who" => $cp,
							"monitors" => $cp,
						)
					)),
					"bug_status" => array(1,2,10,11),
				));
				$ret = array_merge($ret, $ol->ids());
			}
		}

		foreach($this->get_my_offers() as $ofid)
		{
			if ($this->can("view", $c["to"]))
			{
				$ret[] = $ofid;
			}
		}
		return $ret;
	}

	function get_events_from_oids($oids, $clids, $arr)
	{
		$ret = array();
		if(count($oids))
		{
			if($arr["prop"]["type"] == "calendar")
			{
				if($d = $arr["request"]["date"])
				{
					$tmp = explode("-", $d);
					$time = mktime(0, 0, 1, $tmp[1], $tmp[0], $tmp[2]);
				}
				else
				{
					$time = time();
				}
				$from = mktime(0, 0, 1, date('m', $time) - 1, 1, date('Y', $time));
				$to = mktime(0, 0, 1, date('m', $time) + 1, date('t', mktime(0, 0, 1, date('m', $time)+1, 1, date('Y', $time))), date('Y', $time));
				$params[] = new obj_predicate_compare(OBJ_COMP_IN_TIMESPAN, array("start1", "end"), array($from, $to));
			}
			$params["start1"] = new obj_predicate_compare(OBJ_COMP_GREATER_OR_EQ, 1);
			$params["site_id"] = array();
			$params["lang_id"] = array();
			$params["oid"] = $oids;
			$params["class_id"] = $clids;
			$ol = new object_list($params);
			$ret = $ol->ids();
		}
		return $ret;
	}

	function check_customers($arr)
	{
		$customers = array();
		foreach(safe_array($arr["sel"]) as $task)
		{
			$to = obj($task);
			if ($to->class_id() == CL_TASK_ROW)
			{
				$filt_by_row = $to->id();
				// get task from row
				$conns = $to->connections_to(array("from.class_id" => CL_TASK,"type" => "RELTYPE_ROW"));
				$c = reset($conns);
				if ($c)
				{
					$to = $c->from();
					$task = $to->id();
				}
			}
			if ($to->class_id() == CL_CRM_EXPENSE)
			{
				$filt_by_row = $to->id();
				// get task from row
				$conns = $to->connections_to(array("from.class_id" => CL_TASK,"type" => "RELTYPE_EXPENSE"));
				$c = reset($conns);
				if ($c)
				{
					$to = $c->from();
					$task = $to->id();
				}
			}
			if(is_oid($task) && $this->can("view", $task))
			{
				$task=obj($task);
				if(is_oid($task->prop("customer")))
				{
					$customers[$task->prop("customer")] = $task->prop("customer");
				}
			}
		}

		foreach($arr["bugs"] as $bug)
		{
			if($this->can("view" , $bug))
			{
				$c = obj($bug);
				if($c->prop("parent.class_id") == CL_BUG)
				{
					$c = obj($c->prop("parent"));
					if($this->can("view" ,  $c->prop("customer")))
					{
						$customers[$c->prop("customer")] = $c->prop("customer");
					}
				}
			}
		}

		if(is_object($arr["bill"])) $customers[$arr["bill"]->prop("customer")] = $arr["bill"]->prop("customer");
		if(sizeof($customers) > 1) return 1;
		else return false;
		if(sizeof($customers) > 1)
		{
			$_SESSION["task_sel"] = $arr["sel"];
			$impl = get_instance("applications/crm/crm_company_bills_impl");
			$popup = "<script name= javascript>window.open('".$impl->mk_my_orb("search_bill", array("sel" => $arr["sel"],))."','', 'toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=800, width=720')
			</script>
			<script name= javascript>location.href='".$arr["ru"]
			."';</script>";
			die($popup);
		}
	}

	/**
		@attrib name=save_time all_args=1
	**/
	function save_time($arr)
	{
		$sel = array();
		foreach($arr as $k => $v)
		{
			if (substr($k, 0, 3) == "sel")
			{
				foreach($v as $v_id)
				{
					$sel[] = $v_id;
				}
			}
		}
		sort($sel);
		if (count($sel))
		{
			$arr["sel"] = $sel;
		}
		foreach($arr["time_to_cust"] as $oid => $val)
		{
			$row = obj($oid);
			$row->set_prop("time_to_cust" , str_replace(",", ".", $val));
			$row->save();
		}

		foreach($arr["rows"] as $key => $row)
		{
			if($row["time_to_cust"] != $row["time_to_cust_real"])
			{
				if(!($this->can("view" , $key)))
				{
					continue;
				}
				$br = obj($key);
				$br->set_prop("time_to_cust" , str_replace("," , "." , $row["time_to_cust"]));
				$br->save();
			}
		}

		return $_SESSION["create_bill_ru"];
	}

	function convert_to_company_currency($arr)
	{
		$inst = get_instance("applications/crm/crm_company_stats_impl");
		return $inst->convert_to_company_currency($arr);
	}

	/**
		@attrib name=create_bill all_args=1
	**/
	function create_bill($arr)
	{
		automatweb::http_exit(http::STATUS_NOT_IMPLEMENTED);
		if(is_oid($_SESSION["bill_id"]) && $this->can("view", $_SESSION["bill_id"]))
		{
			$bill_id = $_SESSION["bill_id"];
			$bill = obj($_SESSION["bill_id"]);
			$_SESSION["bill_id"] = null;
		}

		$sel = array();
		foreach($arr as $k => $v)
		{
			if (substr($k, 0, 3) == "sel")
			{
				foreach($v as $v_id)
				{
					$sel[] = $v_id;
				}
			}
		}

		sort($sel);

		if (count($sel))
		{
			$arr["sel"] = $sel;
		}

		//kui t88d erinevatele klientidele
		if($this->check_customers(array("sel" => $arr["sel"], "bill" => $bill , "ru" => $arr["post_ru"] , "bugc" => $_SESSION["ccbc_bug_comments"])))
		{
			return aw_url_change_var("different_customers", "1", $arr["post_ru"]);
		}

		if(!is_object($bill))
		{
			// create a bill for all selected tasks
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
			$bill->set_prop("bill_trans_date", time());
			$bill->set_name(sprintf(t("Arve nr %s"), $bill->prop("bill_no")));
			if (is_oid($arr["proj"]))
			{
				$proj = obj($arr["proj"]);
				$cust = $proj->get_first_obj_by_reltype("RELTYPE_ORDERER");
				$impl = $proj->prop("implementor");
				if (is_array($impl))
				{
					$impl = reset($impl);
				}
				if ($cust)
				{
					$bill->set_prop("customer", $cust->id());
				}
				$bill->set_prop("impl", $impl);
				$bill->set_project($arr["proj"]);
			}
		}

		$bill->set_impl();
		$bill->set_prop("bill_date", time());
		if(!$this->can("view", $bill->prop("customer")))
		{
			$bill->set_customer(array(
				"cust" => $arr["cust"],
				"tasks" => $arr["sel"],
				"bugs" => $_SESSION["ccbc_bug_comments"],
			));
		}

		// if the bill has an impl and customer, then check if they have a customer relation
		// and if so, then get the due days from that
		if (is_oid($bill->prop("customer")) && is_oid($bill->prop("impl")))
		{
			$cust_rel_list = new object_list(array(
				"class_id" => crm_company_customer_data_obj::CLID,
				"buyer" => $bill->prop("customer"),
				"seller" => $bill->prop("impl")
			));
			if ($cust_rel_list->count())
			{
				$cust_rel = $cust_rel_list->begin();
				$bill->set_prop("bill_due_date_days", $cust_rel->prop("bill_due_date_days"));
			}

			if(!$bill->prop("bill_due_date_days"))
			{
				$bill->set_prop("bill_due_date_days", $bill->prop("customer.bill_due_days"));
			}

			$bt = time();
			$bill->set_prop("bill_due_date",
				mktime(3,3,3, date("m", $bt), date("d", $bt) + $bill->prop("bill_due_date_days"), date("Y", $bt))
			);
		}


		$bill->save();

		$bugs = array();

		$seti = get_instance(CL_CRM_SETTINGS);
		$sts = $seti->get_current_settings();
		$task_rows_to_bill_count = array();
		foreach(safe_array($arr["sel"]) as $task)
		{
			$to = obj($task);
			$class_id = $to->class_id();

			if($class_id == CL_BUG)
			{
				$bugs[$to->id()] = 1;
				continue;
			}

			//kokkuleppehinna toimetuselt arvele pookimine
			if($to->class_id() == CL_TASK && ($to->prop("deal_unit") || $to->prop("deal_price") || $to->prop("deal_amount")))
			{
				$agreement = $bill->meta("agreement_price");
				if(!is_array($agreement)) $agreement = array();
				$tax = 0.18;//default

				$deal_name = $to->name();
				if ($sts)
				{
					if(is_oid($sts->prop("bill_def_prod")) && $this->can("view",$sts->prop("bill_def_prod")))
					{
						$prod_obj = obj($sts->prop("bill_def_prod"));
						$deal_name = $prod_obj->comment();
						$tr = obj($prod_obj->prop("tax_rate"));
						if (time() >= $tr->prop("act_from") && time() < $tr->prop("act_to"))
						{
							$tax = $tr->prop("tax_amt")/100.0;
						}
					}
				}

				//vaikimisi artikkel ka
				$prod = "";
				$seti = get_instance(CL_CRM_SETTINGS);
				$sts = $seti->get_current_settings();
				if ($sts)
				{
					$prod = $sts->prop("bill_def_prod");
				}

				$price = $to->prop("deal_price");
				if($to->prop("deal_has_tax"))
				{
					$price = $price / (1 + $tax);
				}
				$agreement[] = array(
					"unit" => $to->prop("deal_unit"),
					"price" => $price,
					"amt" => $to->prop("deal_amount"),
					"name" => $deal_name,
					"prod" => $prod,
					"comment" => $deal_name,
					"has_tax" => $to->prop("deal_has_tax"),
				);
				$bill->set_meta("agreement_price" , $agreement);
				$bill->save();
				$to->set_prop("send_bill" , 0);
				$to->save();
			}

			$filt_by_row = null;
			if ($to->class_id() == CL_TASK_ROW)
			{
				$filt_by_row = $to->id();
				// get task from row
				$conns = $to->connections_to(array("from.class_id" => CL_TASK,"type" => "RELTYPE_ROW"));
				$c = reset($conns);
				if ($c)
				{
					$to = $c->from();
					$task = $to->id();
				}
			}

			//kulud arveridadeks
			if ($to->class_id() == CL_CRM_EXPENSE)
			{
				$expense = $to;
				$filt_by_row = $to->id();
				// get task from row
				$conns = $to->connections_to(array("from.class_id" => CL_TASK,"type" => "RELTYPE_EXPENSE"));
				$c = reset($conns);
				if ($c)
				{
					$to = $c->from();
					$task = $to->id();
				}

				$task_o = obj($task);

				$br = obj();
				$br->set_class_id(CL_CRM_BILL_ROW);
				$br->set_parent($bill->id());
				$br->set_prop("comment", $expense->name());
				$br->set_prop("amt", 1);
				$br->set_prop("people", $expense->prop("who"));
				$br->set_prop("price", str_replace(",", ".", $sum = $this->convert_to_company_currency(array(
					"sum" => $expense->prop("cost"),
					"o" => $expense,
					"company_curr" => $bill->prop("customer.currency"),
				))));
				$br->set_prop("is_oe", 1);
				$date = $expense->prop("date");
				$br->set_prop("date", date("d.m.Y", mktime(0,0,0, $date["month"], $date["day"], $date["year"])));
				$expense->set_prop("bill_id", $bill->id());
				$expense->save();

				// get default prod
				if ($sts)
				{
					$br->set_prop("prod", $sts->prop("bill_def_prod"));
				}
				$br->save();
				$br->connect(array(
					"to" => $task_o->id(),
					"type" => "RELTYPE_TASK"
				));
				$br->connect(array(
					"to" => $expense->id(),
					"type" => "RELTYPE_EXPENSE"
				));
				$bill->connect(array(
					"to" => $br->id(),
					"type" => "RELTYPE_ROW"
				));
			}
			$bill->connect(array(
				"to" => $task,
				"reltype" => "RELTYPE_TASK"
			));

			$task_o = obj($task);
			$task_o->connect(array(
				"to" => $bill->id(),
				"type" => "RELTYPE_BILL"
			));

			if(!$task_rows_to_bill_count[$task]) $task_rows_to_bill_count[$task] = 0;
			$task_rows_to_bill_count[$task] ++;
			if($task_rows_to_bill_count[$task] == $_POST["count"][$task])
			{
				$task_o->set_prop("send_bill", 0);
				$task_o->save();
			}
			foreach($task_o->connections_from(array("type" => "RELTYPE_ROW")) as $c)
			{
				$row = $c->to();
				if (!$row->prop("bill_id") && ($row->prop("on_bill") || $row->prop("send_bill")) && ($filt_by_row === null || $c->prop("to") == $filt_by_row))
				{
					if ($row->is_property("bill_id"))
					{
						$row->set_prop("bill_id", $bill->id());
					}
					else
					{
						$row->set_prop("bill_no", $bill->id());
					}
					$row->save();
				}
			}

			$task_o->save();
			foreach($task_o->connections_from(array("type" => "RELTYPE_PROJECT")) as $c)
			{
				$bill->set_project($c->prop("to"));
			}
			if($class_id == CL_TASK)
			{
				//kokkuleppehinna puhul tahaks nyyd ka muudele kuludele arve kylge
				$task_o->set_billable_oe_bill_id($bill->id());
			}
			elseif($class_id == CL_CRM_EXPENSE)//all pole vaja enam muu kulu rida kirjutama hakata
			{
				continue;
			}

			// now, get all rows from task and convert to bill rows
			$task_i = get_instance(CL_TASK);
			if(sizeof($arr["sel"]) > 1)
			{
			//	continue;
			}
			foreach($task_i->get_task_bill_rows($task_o, true, $bill->id()) as $row)
			{
				if ($filt_by_row !== null && $row["row_oid"] != $filt_by_row)
				{
					continue;
				}
				$br = obj();
				$br->set_class_id(CL_CRM_BILL_ROW);
				$br->set_parent($bill->id());
				$br->set_prop("name", $row["name"]);
				$br->set_prop("amt", $row["amt"]);
				$br->set_prop("prod", $row["prod"]);
				$br->set_prop("price", $row["price"]);
				$br->set_prop("unit", $row["unit"]);
				$br->set_prop("is_oe", $row["is_oe"]);
				$br->set_prop("has_tax", $row["has_tax"]);
				$br->set_prop("date", date("d.m.Y", $row["date"]));
				$br->set_prop("people", $row["impl"]);
				// get default prod

				if ($sts)
				{
					$br->set_prop("prod", $sts->prop("bill_def_prod"));
				}
				if($row["has_tax"])
				{
					$br->set_prop("tax", $br->get_row_tax(1));
				}
				$br->save();

				$br->connect(array(
					"to" => $task_o->id(),
					"type" => "RELTYPE_TASK"
				));

				if ($row["row_oid"])
				{
					$br->connect(array(
						"to" => $row["row_oid"],
						"type" => "RELTYPE_TASK_ROW"
					));
				}

				$bill->connect(array(
					"to" => $br->id(),
					"type" => "RELTYPE_ROW"
				));
			}
		}

		//teeb bugide lisamise eraldi
		$billable_bug_rows = array();
		foreach($_SESSION["ccbc_bug_comments"] as $key => $comment)
		{
			$bc = obj($comment);
			if($bugs[$bc->prop("task")])
			{
				$billable_bug_rows[] = $comment;
				unset($_SESSION["ccbc_bug_comments"][$key]);
			}

		}
		if($arr["bunch_bugs"])
		{
			$bill->add_bug_comments($billable_bug_rows);
		}
		else
		{
			$bill->add_bug_comments_single_rows($billable_bug_rows);
		}
//		unset($_SESSION["ccbc_bug_comments"]);

		if($_SESSION["create_bill_ru"])
		{
			$create_bill_ru = $_SESSION["create_bill_ru"];
		}
		else
		{
			$create_bill_ru = html::get_change_url($arr["id"], array("group" => "bills"));
		}
		return html::get_change_url($bill->id(),array("return_url" => $create_bill_ru,));
	}

	/**
		@attrib name=add_proj_to_co_as_ord
	**/
	function add_proj_to_co_as_ord($arr)
	{
		return html::get_new_url(
				CL_PROJECT,
				$arr["id"],
				array(
					"connect_impl" => reset($arr["check"]),
					"return_url" => $arr["post_ru"],
					"connect_orderer" => $arr["id"],
				)
		);
	}

	/**
		@attrib name=add_proj_to_co_as_impl
	**/
	function add_proj_to_co_as_impl($arr)
	{
		return html::get_new_url(
				CL_PROJECT,
				$arr["id"],
				array(
					"connect_impl" => $arr["id"],
					"return_url" => $arr["post_ru"],
					"connect_orderer" => reset($arr["check"]),
				)
		);
	}

	/**
		@attrib name=submit_delete_docs
		@param sel optional
		@param post_ru optional
	**/
	function submit_delete_docs($arr)
	{
		$ru = $arr["post_ru"];
		$_SESSION["docs_del_err"] = array();
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array(
				"oid" => $arr["sel"]
			));
			foreach($ol->arr() as $o)
			{
				if($this->can("delete", $o->id()))
				{
					$o->delete();
				}
				else
				{
					$_SESSION["docs_del_err"][] = $o->id();
				}
			}
		}
		return $ru;
	}

	/**
		@attrib name=add_task_to_co
	**/
	function add_task_to_co($arr)
	{
		$pl = get_instance(CL_PLANNER);
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		return $this->mk_my_orb('new',array(
			'alias_to_org' => reset($arr["check"]),
			'reltype_org' => 13,
			'add_to_cal' => $this->cal_id,
			'title' => t("Toimetus"),
			'parent' => $arr["id"],
			'return_url' => $arr["post_ru"]
		), CL_TASK);

	}

	/**
		@attrib name=add_meeting_to_co
	**/
	function add_meeting_to_co($arr)
	{
		$pl = get_instance(CL_PLANNER);
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		return $this->mk_my_orb('new',array(
			'alias_to_org' => reset($arr["check"]),
			'reltype_org' => 13,
			'class' => 'planner',
			'id' => $this->cal_id,
			'group' => 'add_event',
			'clid' => CL_CRM_MEETING,
			'action' => 'change',
			'title' => t("Kohtumine"),
			'parent' => $arr["id"],
			'return_url' => $arr["post_ru"]
		));

	}

	/**
		@attrib name=add_offer_to_co
	**/
	function add_offer_to_co($arr)
	{
		$pl = get_instance(CL_PLANNER);
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		return $this->mk_my_orb('new',array(
			'alias_to_org' => reset($arr["check"]),
			'reltype_org' => 13,
			'class' => 'planner',
			'id' => $this->cal_id,
			'group' => 'add_event',
			'clid' => CL_CRM_OFFER,
			'action' => 'change',
			'title' => t("Pakkumine"),
			'parent' => $arr["id"],
			'return_url' => $arr["post_ru"]
		));

	}

	/**
		@attrib name=add_task_to_proj
	**/
	function add_task_to_proj($arr)
	{
		$pl = get_instance(CL_PLANNER);
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		$proj = reset($arr["sel"]);
		$o = obj($proj);

		return $this->mk_my_orb('new',array(
			'alias_to_org' => reset($o->prop("orderer")),
			'reltype_org' => 13,
			'add_to_cal' => $this->cal_id,
			'title' => t("Toimetus"),
			'parent' => $arr["id"],
			'return_url' => $arr["post_ru"],
			"set_proj" => $proj
		), CL_TASK);

	}

	/**
		@attrib name=add_meeting_to_proj
	**/
	function add_meeting_to_proj($arr)
	{
		$pl = get_instance(CL_PLANNER);
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		$proj = reset($arr["sel"]);
		$o = obj($proj);

		return $this->mk_my_orb('new',array(
			'alias_to_org' => $o->prop("orderer"),
			'reltype_org' => 13,
			'class' => 'planner',
			'id' => $this->cal_id,
			'group' => 'add_event',
			'clid' => CL_CRM_MEETING,
			'action' => 'change',
			'title' => t("Kohtumine"),
			'parent' => $arr["id"],
			'return_url' => $arr["post_ru"],
			"set_proj" => $proj
		));

	}

	/**
		@attrib name=add_offer_to_proj
	**/
	function add_offer_to_proj($arr)
	{
		$pl = get_instance(CL_PLANNER);
		$this->cal_id = $pl->get_calendar_for_user(array(
			"uid" => aw_global_get("uid"),
		));

		$proj = reset($arr["sel"]);
		$o = obj($proj);

		return $this->mk_my_orb('new',array(
			'alias_to_org' => $o->prop("orderer"),
			'reltype_org' => 13,
			'class' => 'planner',
			'id' => $this->cal_id,
			'group' => 'add_event',
			'clid' => CL_CRM_OFFER,
			'action' => 'change',
			'title' => t("Pakkumine"),
			'parent' => $arr["id"],
			'return_url' => $arr["post_ru"],
			"set_proj" => $proj
		));

	}

	/**
		@attrib name=mark_p_as_important
	**/
	function mark_p_as_important($arr)
	{
		$u = get_instance(CL_USER);
		$p = obj($u->get_current_person());

		foreach(safe_array($arr["check"]) as $pers)
		{
			$p->connect(array(
				"to" => $pers,
				"reltype" => "RELTYPE_IMPORTANT_PERSON"
			));
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=unmark_p_as_important
	**/
	function unmark_p_as_important($arr)
	{
		$u = get_instance(CL_USER);
		$p = obj($u->get_current_person());

		foreach(safe_array($arr["check"]) as $pers)
		{
			$p->disconnect(array(
				"from" => $pers,
				"type" => "RELTYPE_IMPORTANT_PERSON"
			));
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=tasks_switch_to_cal_view
	**/
	function tasks_switch_to_cal_view($arr)
	{
		aw_session_set("crm_task_view", CRM_TASK_VIEW_CAL);
		return $arr["post_ru"];
	}

	/**
		@attrib name=tasks_switch_to_table_view
	**/
	function tasks_switch_to_table_view($arr)
	{
//		aw_session_set("crm_task_view" , CRM_TASK_VIEW_TABLE);
		$GLOBALS["crm_task_view"] = CRM_TASK_VIEW_TABLE;
		$_SESSION["crm_task_view"] = CRM_TASK_VIEW_TABLE;
		return $arr["post_ru"];
	}

	/**
		@attrib name=go_to_create_bill
	**/
	function go_to_create_bill($arr)
	{
		return $this->mk_my_orb("change", array(
			"id" => $arr["id"],
			"group" => "bills",
			"cust" => reset($arr["check"]),
			"return_url" => $arr["post_ru"]
		));
	}

	function get_projects_for_customer($co)
	{
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_ORDERER.id" => $co->id()
		));
		$ol2 = new object_list(array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_IMPLEMENTOR.id" => $co->id()
		));
		return $ol->ids() + $ol2->ids();
	}

	/**
		@attrib name=delete_projs
	**/
	function delete_projs($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array("oid" => $arr["sel"]));
			$ol->delete();
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=delete_bills
	**/
	function delete_bills($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array("oid" => $arr["sel"]));
			$ol->delete();
		}
		return $arr["post_ru"];
	}

	function _get_cust_contract_creator($arr)
	{
		// list of all persons in my company
		$arr["prop"]["options"] = $this->get_employee_picker($arr["obj_inst"], true);
		if (($rel = $this->get_cust_rel($arr["obj_inst"])))
		{
			$arr["prop"]["value"] = $rel->prop("cust_contract_creator");
		}

		if (!isset($arr["prop"]["options"][$arr["prop"]["value"]]) && $this->can("view", $arr["prop"]["value"]))
		{
			$v = obj($arr["prop"]["value"]);
			$arr["prop"]["options"][$arr["prop"]["value"]] = $v->name();
		}
	}

	public static function get_employee_picker(object $co, $add_empty = false, $important_only = false)
	{
		static $cache;
		if (isset($cache[$co->id()][$add_empty][$important_only]))
		{
			return $cache[$co->id()][$add_empty][$important_only];
		}

		if ($important_only)
		{
			// filter out my important persons
			$p = get_current_person();
			$cur_user_important_persons = $p->connections_from(array("type" => "RELTYPE_IMPORTANT_PERSON"));
			foreach ($cur_user_important_persons as $connection)
			{
				$employees[$connection->prop("to")] = $connection->prop("to.name");
			}
		}
		else
		{
			$employees = $co->get_employees()->names();
		}

		if ($add_empty)
		{
			$employees = html::get_empty_option(0) + $employees;
		}

		$cache[$co->id()][$add_empty][$important_only] = $employees;
		return $employees;
	}

	private static function __person_name_sorter($a, $b)
	{
		if(sizeof(explode(" ", $a)) > 1)
		{
			list($a_fn, $a_ln) = explode(" ", $a);
		}
		else
		{
			$a_fn = "";$a_ln = $a;
		}

		if(sizeof(explode(" ", $b)) > 1)
		{
			list($b_fn, $b_ln) = explode(" ", $b);
		}
		else
		{
			$b_fn = "";$b_ln = $b;
		}

		if ($a_ln == $b_ln)
		{
			return strcmp($a_fn, $b_fn);
		}

		return strcmp($a_ln, $b_ln);
	}

	function _gen_company_code($co)
	{
		if ($co->prop("code") == "" && is_oid($ct = $co->prop("contact")) && $this->can("view", $ct))
		{
			$ct = obj($ct);
			$rk = $ct->prop("riik");
			if (is_oid($rk) && $this->can("view", $rk))
			{
				$rk = obj($rk);
				$code = substr(trim($rk->ord()), 0, 1);
				// get number of companies that have this country as an address
				$ol = new object_list(array(
					"class_id" => CL_CRM_COMPANY,
					"CL_CRM_COMPANY.contact.riik.name" => $rk->name()
				));
				$ol2 = new object_list(array(
					"class_id" => crm_person_obj::CLID,
					"CL_CRM_PERSON.address.riik.name" => $rk->name()
				));
				$code .= sprintf("%04d", $ol->count() + $ol2->count());
				$co->set_prop("code", $code);
			}
		}
	}

	function callback_pre_save($arr)
	{
		$this->_gen_company_code($arr["obj_inst"]);
		if (!empty($arr["request"]["sector"]) && $arr["new"])
		{
			$arr["obj_inst"]->set_prop("pohitegevus", $arr["request"]["sector"]);
		}
	}

	/**
		@attrib name=save_bill_list
	**/
	function save_bill_list($arr)
	{
		foreach(safe_array($arr["bill_states"]) as $bill_id => $state)
		{
			$bill = obj($bill_id);
			if ($bill->prop("state") != $state)
			{
				$bill->set_prop("state", $state);
				$bill->save();
			}
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=delete_tasks
		@param sel optional
		@param post_ru required
	**/
	function delete_tasks($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			foreach($arr["sel"] as $id)
			{
				$o = obj($id);
				$o->delete();
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=create_new_invoice_folder
		@param invoice_folder_parent required type=int acl=view
		@param id optional type=int acl=view
		@param post_ru required type=string
	**/
	function create_new_invoice_folder($arr)
	{
		$invoice_folder = obj(null, array(), crm_invoice_folder_obj::CLID);
		$parent = empty($arr["invoice_folder_parent"]) ? $arr["id"] : $arr["invoice_folder_parent"];
		$invoice_folder->set_parent($parent);
		$invoice_folder->save();
		return $this->mk_my_orb("change", array(
			"id" => $invoice_folder->id(),
			"return_url" => $arr["post_ru"]
		), "crm_invoice_folder");
	}

	/**
		@attrib name=create_new_invoice_template
		@param id required type=oid acl=view
		@param i_fldr optional type=oid acl=view
		@param post_ru required type=string
	**/
	function create_new_invoice_template($arr)
	{
		$invoice_template = obj(null, array(), crm_bill_obj::CLID);
		$parent = empty($arr["i_fldr"]) ? $arr["id"] : $arr["i_fldr"];
		$invoice_template->set_parent($parent);
		$invoice_template->set_prop("is_invoice_template", 1);
		$invoice_template->save();
		return $this->mk_my_orb("change", array(
			"id" => $invoice_template->id(),
			"return_url" => $arr["post_ru"]
		), "crm_bill");
	}

	/**
		@attrib name=create_invoice_from_template
		@param id required type=oid acl=view
		@param co required type=oid acl=view
		@param post_ru required type=string
	**/
	function create_invoice_from_template($arr)
	{
		$invoice = obj(null, array(), crm_bill_obj::CLID);
		$invoice->load_from_template($arr["id"]);

		return html::get_change_url($invoice->id(), array("return_url" => $arr["post_ru"]));
	}

	// handler for address save message
	function on_save_address($arr)
	{
		// get all companies with empty codes that have this country
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"contact" => $arr["oid"],
			"code" => ''
		));
		foreach($ol->arr() as $o)
		{
			$i = $o->instance();
			$i->_gen_company_code($o);
			$o->save();
		}
	}


	// Finds first matching CRM_FIELD object and it's properties
	//  oid - oid of CRM_COMPANY
	//  type - FIELD type (suffix of class_id) - eg ACCOMMODATION for CL_CRM_FIELD_ACCOMMODATION
	//  clid - FIELD class id (alternative method)
	function find_crm_field_obj($arr)
	{
		$c = obj($arr['oid']);
		if (!is_object($c) || $c->class_id() != CL_CRM_COMPANY || (empty($arr['type']) && empty($arr['clid'])) )
		{
			return;
		}
		if (empty($arr['clid']))
		{
			$type = constant('CL_CRM_FIELD_'.strtoupper($arr['type']));
		}
		else
		{
			$type = $arr['clid'];
		}
		if (!is_numeric($type))
		{
			return;
		}

		// Get first object reltype RELTYPE_FIELD of class CL_CRM_FIELD_ACCOMMODATION
		$conns = $c->connections_from(array(
			'type' => 'RELTYPE_FIELD',
		));
		$found = false;
		foreach ($conns as $con)
		{
			$o = $con->to();
			if ($o->class_id() == $type)
			{
				$found = true;
				break;
			}
		}
		if ($found)
		{
			return $o;
		}
	}

	/**
		@attrib name=cut_docs
	**/
	function cut_docs($arr)
	{
		$_SESSION["crm_cut_docs"] = safe_array($arr["sel"]);
		return $arr["post_ru"];
	}

	/**
		@attrib name=submit_paste_docs
	**/
	function submit_paste_docs($arr)
	{
		$fld = $arr["tf"];
		$tmp_fld = explode("|", $fld);

		if(2 === count($tmp_fld) and is_oid($tmp_fld[1]))
		{
			$fld = $tmp_fld[1];
		}

		if (!$fld)
		{
			$i = new crm_company_docs_impl();
			$fld = $i->_init_docs_fld(obj($arr["id"]));
			$fld = $fld->id();
		}

		foreach(safe_array($_SESSION["crm_cut_docs"]) as $did)
		{
			$o = obj($did);
			$o->set_parent($fld);
			$o->save();
		}
		unset($_SESSION["crm_cut_docs"]);
		return $arr["post_ru"];
	}

	/**
		@attrib name=res_delete
	**/
	function res_delete($arr)
	{
		if (is_array($arr["sel"]) && count($arr["sel"]))
		{
			$ol = new object_list(array("oid" => $arr["sel"]));
			$ol->delete();
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=res_paste
	**/
	function res_paste($arr)
	{
		if (is_array($_SESSION["co_res_cut"]) && count($_SESSION["co_res_cut"]))
		{
			$ol = new object_list(array("oid" => $_SESSION["co_res_cut"]));
			$ol->set_parent($arr["tf"]);
		}
		$_SESSION["co_res_cut"] = false;
		return $arr["post_ru"];
	}

	/**
		@attrib name=res_cut
	**/
	function res_cut($arr)
	{
		$_SESSION["co_res_cut"] = $arr["sel"];
		return $arr["post_ru"];
	}

	function get_my_resources()
	{
		$i = get_instance("applications/crm/crm_company_res_impl");
		$u = get_instance(CL_USER);

		$ot = new object_tree(array(
			"class_id" => array(CL_MENU, CL_MRP_RESOURCE),
			"parent" => $i->_get_res_parent(obj($u->get_current_company()))
		));
		$ol = $ot->to_list();
		$ret = new object_list();

		foreach($ol->arr() as $o)
		{
			if ($o->class_id() == CL_MRP_RESOURCE)
			{
				$ret->add($o);
			}
		}
		return $ret;
	}

	function get_current_usecase($arr)
	{
		$usecase = false;

		// if this is the current users employer
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();

		if ($co == $arr["obj_inst"]->id())
		{
			$usecase = CRM_COMPANY_USECASE_EMPLOYER;
		}
		else
		{
			$usecase = CRM_COMPANY_USECASE_CLIENT;
		}

		return $usecase;
	}

	function callback_get_cfgform($arr)
	{
		// if this is the current users employer, do nothing
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		if ($co == $arr["obj_inst"]->id())
		{
			$s = get_instance(CL_CRM_SETTINGS);
			if (($o = $s->get_current_settings()))
			{
				return $o->prop("work_cfgform");
			}
		}

		// find the crm settings object for the current user
		$s = get_instance(CL_CRM_SETTINGS);
		if (($o = $s->get_current_settings()))
		{
			return $o->prop("s_cfgform");
		}
	}

	function get_cust_rel($view_co, $crea_if_not_exists = false, $my_co = null)
	{
		if (!is_object($view_co) || !is_oid($view_co->id()))
		{
			return false;
		}
		if ($my_co === null)
		{
			$my_co = get_current_company();
		}

		if (!is_object($my_co) || !is_oid($my_co->id()))
		{
			return;
		}

		if ($view_co->id() == $my_co)
		{
			return false;
		}

		static $gcr_cache;
		if (!is_array($gcr_cache))
		{
			$gcr_cache = array();
		}
		if (isset($gcr_cache[$view_co->id()][$crea_if_not_exists][$my_co->id()]))
		{
			return $gcr_cache[$view_co->id()][$crea_if_not_exists][$my_co->id()];
		}

		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"buyer" => $view_co->id(),
			"seller" => $my_co
		));
		if ($ol->count())
		{
			$gcr_cache[$view_co->id()][$crea_if_not_exists][$my_co->id()] = $ol->begin();
			return $ol->begin();
		}
		else
		if ($crea_if_not_exists)
		{
			$my_co = obj($my_co);
			$o = obj();
			$o->set_class_id(CL_CRM_COMPANY_CUSTOMER_DATA);
			$o->set_name(t("Kliendisuhe ").$my_co->name()." => ".$view_co->name());
			$o->set_parent($my_co->id());
			$o->set_prop("seller", $my_co->id());
			$o->set_prop("buyer", $view_co->id());
			$o->save();
			$gcr_cache[$view_co->id()][$crea_if_not_exists][$my_co->id()] = $o;
			return $o;
		}
	}

	function on_delete_company($arr)
	{
		$company = obj($arr["oid"]);
		$customer_data1 = new object_list(array(
			"buyer" => $company->id(),
			"class_id" => array(CL_CRM_COMPANY_CUSTOMER_DATA)
		));
		$customer_data1->delete();
		$customer_data2 = new object_list(array(
			"seller" => $company->id(),
			"class_id" => array(CL_CRM_COMPANY_CUSTOMER_DATA)
		));
		$customer_data2->delete();
	}

	function callback_post_save($arr)
	{
		if (!empty($arr["request"]["co_is_buyer"]))
		{
			$company = $arr["obj_inst"];
			$cur = get_current_company();
			$crel = $this->get_cust_rel($company, true,$cur);
			if($arr["request"]["set_buyer_status"])
			{
				$customer_data = new object_list(array(
					"buyer" => $company->id(),
					"seller" => $cur->id(),
					"class_id" => array(CL_CRM_COMPANY_CUSTOMER_DATA)
				));
				foreach($customer_data->list as $cust_data_id)
				{
					$cd = obj($cust_data_id);
					$status = obj($arr["request"]["set_buyer_status"]);
					$cd->connect(array(
						"to" => $status,
						"type" => RELTYPE_STATUS
					));
				}
			}
		}
		$ps = new popup_search();
		$ps->do_create_rels($arr["obj_inst"], $arr["request"]["search_tbl"], "RELTYPE_DOCS_FOLDER");
		if(substr_count($arr["request"]["return_url"] , "action=new") && (substr_count($arr["request"]["return_url"] , "class=crm_task") || substr_count($arr["request"]["return_url"] , "class=crm_call") || substr_count($arr["request"]["return_url"] , "class=crm_meeting")))
		{
			$_SESSION["add_to_task"]["customer"] = $arr["obj_inst"]->id();
		}
	}

	function callback_mod_layout($arr)
	{
		if($arr["name"] === "all_act_search" && aw_global_get("crm_task_view") == CRM_TASK_VIEW_CAL)
		{
			return false;
		}
		return true;
	}

	function callback_mod_tab($arr)
	{
		switch($arr["id"])
		{
			case "my_tasks":
			case "meetings":
			case "calls":
			case "bugs":
				return false;

			// don't show customers tab if current company isn't the one being viewed
			// iow customers can't have customers in this system
			case "relorg":
			case "relorg_b":
			case "relorg_s":
				$co = user::get_current_company();
//				return (!isset($arr["request"]["id"]) xor (isset($arr["request"]["id"]) and $arr["request"]["id"] == $co));
		}

		if ($arr["id"] === "transl" && aw_ini_get("user_interface.content_trans") != 1)
		{
			return false;
		}

		if($arr["id"] === "sell_offers" and !is_oid($arr["request"]["buyer"]))
		{
			return false;
		}

		if ($arr["id"] === "ovrv_mails")
		{
			$co = user::get_current_company();
			if ($co == $arr["obj_inst"]->id())
			{
				// get messenger for user
				$m2 = get_instance(CL_MESSENGER_V2);
				$msg = $m2->get_messenger_for_user();
				if (!$msg)
				{
					return false;
				}
				$arr["link"] = html::get_change_url($msg, array("return_url" => get_ru(), "group" => "main_view"));
			}
		}

		if($arr["id"] === "bugs")
		{
			$show = 0;
			$cali = get_instance(CL_PLANNER);
			$calid = $cali->get_calendar_for_user();
			if($calid)
			{
				$cal = obj($calid);
				$eec = $cal->prop("event_entry_classes");
				if($eec[CL_BUG])
				{
					$show = 1;
				}
			}
			if(!$show)
			{
				return false;
			}
		}

		if($arr["id"] === "ovrv_email")
		{
			$seti = get_instance(CL_CRM_SETTINGS);
			$sts = $seti->get_current_settings();
			if (!$sts || !$sts->prop("send_mail_feature"))
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
		@attrib name=get_company_count_by_name

		@param co_name optional
	**/
	function get_company_count_by_name($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"name" => $arr["co_name"]
		));
		die($ol->count()."\n");
	}

	/**
		@attrib name=go_to_first_co_by_name
		@param co_name optional
		@param return_url optional
	**/
	function go_to_first_co_by_name($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"name" => $arr["co_name"]
		));
		$o = $ol->begin();
		header("Location: ".html::get_change_url($o->id())."&warn_conflicts=1&return_url=".urlencode($arr["return_url"]));
		die();
	}

	/**
		@attrib name=disp_conflict_pop
		@param id required
	**/
	function disp_conflict_pop($arr)
	{
		$co = obj($arr["id"]);
		$ol = new object_list(array(
			"class_id" => CL_PROJECT,
			"CL_PROJECT.RELTYPE_SIDE.name" => $co->name()
		));
		$ret = t("Konfliktsed projektid:<br>");
		foreach($ol->arr() as $o)
		{
			$ret .= html::href(array(
				"url" => html::get_change_url($o->id()),
				"caption" => $o->name(),
				"target" => "_blank"
			))."<br>";
		}
		return $ret;
	}

	function callback_generate_scripts($arr)
	{
		$sc = "";

		$sc.='function gt_change(id)
		{
			change_url = "'.$this->mk_my_orb("gt_change").'";
			change_url = change_url+"&id="+id+"&return_url='.urlencode(get_ru()).'";
			NewWindow = window.open(change_url , "_blank");
//			window.location.href = change_url;
		}';

		if (!$arr["new"])
		{
			$sc.="function co_contact(id,url)
				{
				if ((trel = document.getElementById(\"trows\"+id)))
				{
					if (trel.style.display == \"none\")
					{
						if (navigator.userAgent.toLowerCase().indexOf(\"msie\")>=0)
						{
							trel.style.display= \"block\";
						}
						else
						{
							trel.style.display= \"table-row\";
						}
					}
					else
					{
						trel.style.display=\"none\";
					}
					return false;
				}
				el=document.getElementById(\"tnr\"+id);
				td = el.parentNode;
				tr = td.parentNode;

				tbl = tr;
				while(tbl.tagName.toLowerCase() != \"table\")
				{
					tbl = tbl.parentNode;
				}
				p_row = tbl.insertRow(tr.rowIndex+1);
				p_row.className=\"awmenuedittablerow\";
				p_row.id=\"trows\"+id;
				n_td = p_row.insertCell(-1);
				n_td.className=\"awmenuedittabletext\";
				n_td.innerHTML=\"&nbsp;\";
				n_td = p_row.insertCell(-1);
				n_td.className=\"awmenuedittabletext\";
				n_td.innerHTML=\"&nbsp;\";
				n_td = p_row.insertCell(-1);
				n_td.className=\"awmenuedittabletext\";
				n_td.innerHTML=aw_get_url_contents(url);
				n_td.colSpan=9;
				}
				";

				$url_quick_task_entry = html::get_new_url(CL_TASK_QUICK_ENTRY, $arr["request"]["id"], array("in_popup" => 1));
				$sc .= "
				$.hotkeys.add('x',function(e){
					if (e.target.tagName.toLowerCase() != 'textarea' &&
						e.target.tagName.toLowerCase() != 'input')
					{
						aw_popup_scroll('$url_quick_task_entry', 'quick_task_entry', 1000,600);
					}
				});
				";

			if ($this->use_group === "bills")
			{
				$sc .= '
				$("#bill_s_bill_no").blur( group_bills_clean_date);
				$("#bill_s_bill_to").blur( group_bills_clean_date);

				function group_bills_clean_date()
				{
					if ($(this).attr("value").length>0)
					{
						$("input[name^=bill_s_from]").attr("value", "");
						$("input[name^=bill_s_to]").attr("value", "");
					}
				}
				';
			}

			$sc .= "
				function bg_mark_task_done(link, eln, ns)
				{
					resetButton(activeButton);
					// fetch the url to mark it done
					aw_get_url_contents(link);
					// change icon
					el = document.getElementById(eln);
					el.src = ns;
				}
			";

			if (!empty($arr["request"]["warn_conflicts"]))
			{
				// get conflicts list and warn user if there are any

				// to do this, get all projects for this company that have the current company as a side
				$u = get_instance(CL_USER);
				$ol = new object_list(array(
					"class_id" => CL_PROJECT,
					"CL_PROJECT.RELTYPE_SIDE.name" => $arr["obj_inst"]->name()
				));
				if ($ol->count())
				{
					$link = $this->mk_my_orb("disp_conflict_pop", array("id" => $arr["obj_inst"]->id()));
					return "aw_popup_scroll('$link','confl','200','200');".$sc;
				}
			}

			if(empty($_SESSION['company_bds']))
			{
				$ci = new planner();
				$cp = get_current_person();
				$cal = $ci->get_calendar_for_person($cp);
				$show_bds = false;
				if(is_oid($cal))
				{
					$calo = obj($cal);
					$show_bds = $calo->prop("show_bdays");
				}
				$bds = $this->get_cust_bds();
				if(count($bds) && $show_bds)
				{
					$_SESSION['company_bds'] = 1;
					$url = $this->mk_my_orb("cust_bds",array());
					$sc .= "window.open('".$url."','popup','width=300,height=500')";
				}
			}
			return $sc;
		}

		$sc .= "
		function aw_submit_handler() {".
			// fetch list of companies with that name and ask user if count > 0
			"var url = '".$this->mk_my_orb("get_company_count_by_name")."';".
			"url = url + '&co_name=' + document.changeform.name.value;".
			"num= parseInt(aw_get_url_contents(url));".
			"if (num >0)
			{
				var ansa = confirm('" . t("Sellise nimega organisatsioon on juba olemas. Kas soovite minna selle objekti muutmisele?") . "');
				if (ansa)
				{
					window.location = '".$this->mk_my_orb("go_to_first_co_by_name", array("return_url" => $arr["request"]["return_url"]))."&co_name=' + document.changeform.name.value;
					return false;
				}
				return false;
			}".
			"return true;}
		";

		return $sc;
	}

	function callback_gen_forum($arr)
	{
		// check/create forum
		$forum = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_FORUM");
		if (!$forum)
		{
			$o = obj();
			$o->set_class_id(CL_FORUM_V2);
			$o->set_parent($arr["obj_inst"]->id());
			$o->set_name(sprintf(t("%s foorum"), $arr["obj_inst"]->name()));
			$o->save();
			$arr["obj_inst"]->connect(array(
				"to" => $o->id(),
				"type" => "RELTYPE_FORUM"
			));

			$fi = $o->instance();
			$fi->callback_post_save(array(
				"obj_inst" => $o,
				"request" => array("new" => 1)
			));
			$forum = $o;
		}

		$fi = $forum->instance();
		return $fi->callback_gen_contents(array(
			"obj_inst" => $forum,
			"request" => $arr["request"],
		));
	}

	function _proc_server_folder($arr)
	{
		if ($arr["prop"]["value"] == "")
		{
			return;
		}

		// if changed, recreate
		$srv = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_SERVER_FILES");
		if (!$srv)
		{
			$srv = obj();
			$srv->set_class_id(CL_SERVER_FOLDER);
			$srv->set_parent($arr["obj_inst"]->id());
			$srv->set_name(sprintf(t("%s serveri failid"), $arr["obj_inst"]->name()));
		}

		$srv->set_prop("folder", $arr["prop"]["value"]);
		$srv->save();
		$arr["obj_inst"]->connect(array(
			"to" => $srv->id(),
			"type" => "RELTYPE_SERVER_FILES"
		));
	}


	/**
		@attrib name=proj_autocomplete_source all_args=1
		@param stats_s_cust optional
		@param stats_s_proj optional
	**/
	function proj_autocomplete_source($arr)
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = new json();

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);
		if(!$arr["stats_s_cust"])
		{
			$arr["stats_s_cust"] = $arr["customer"];
		}
		if(!$arr["stats_s_proj"])
		{
			$arr["stats_s_proj"] = $arr["project"];
		}

		$ol = new object_list(array(
			"class_id" => array(CL_PROJECT),
			"name" => $arr["stats_s_proj"]."%",
			"CL_PROJECT.RELTYPE_ORDERER.name" => $arr["stats_s_cust"]."%"
		));
		$autocomplete_options =  $ol->names();
		exit ($cl_json->encode($option_data));
	}

	/**
		@attrib name=unit_options_autocomplete_source all_args=1
	**/
	function unit_options_autocomplete_source($arr)
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = new json();

		$co = $arr["customer"] ? $arr["customer"] : $arr["orderer"];
		$name = $arr["customer_unit"] ? $arr["customer_unit"] : $arr["orderer_unit"];

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		$orgs = new object_list(array(
			"class_id" => array(CL_CRM_COMPANY),
			"name" => $co."%",
			"limit" => 1,
		));

		foreach($orgs->arr() as $org)
		{
			$secs = $org->get_sections();
			$autocomplete_options = $secs->names();
		}

		exit ($cl_json->encode($option_data));
	}

	/**
		@attrib name=worker_options_autocomplete_source all_args=1
	**/
	function worker_options_autocomplete_source($arr)
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = new json();

		$co = $arr["customer"] ? $arr["customer"] : $arr["orderer"];
		$name = $arr["customer_person"] ? $arr["customer_person"] : $arr["orderer_person"];

		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		$orgs = new object_list(array(
			"class_id" => array(CL_CRM_COMPANY),
			"name" => $co."%",
			"limit" => 1,
		));

		foreach($orgs->arr() as $org)
		{
			$autocomplete_options = $org->get_worker_selection();
		}

		exit ($cl_json->encode($option_data));
	}


	/**
		@attrib name=name_autocomplete_source
		@param name optional
		@param stats_s_cust optional
	**/
	function name_autocomplete_source($arr)
	{
		$errorstring = "";
		$error = false;
		$autocomplete_options = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$autocomplete_options,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		if (!empty($arr["name"]))
		{
			$name_part_to_complete = $arr["name"];
		}
		elseif (!empty($arr["stats_s_cust"]))
		{
			$name_part_to_complete = $arr["stats_s_cust"];
		}
		else
		{
			$name_part_to_complete = "";
		}

		if (strlen($name_part_to_complete) > 1)
		{
			$ol = new object_list(array(
				"class_id" => array(crm_company_obj::CLID, crm_person_obj::CLID),
				"name" => $arr["name"]."%",
				new obj_predicate_sort(array("name" => obj_predicate_sort::ASC)),
				new obj_predicate_limit(50)//TODO: konfitavaks
			));
			$autocomplete_options = $ol->names();
		}

		ob_start("ob_gzhandler");
		header("Content-Type: application/json");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		exit (json_encode($option_data));
	}

	/**
		@attrib name=keywords_autocomplete_source
		@param customer_search_keywords optional
	**/
	function keywords_autocomplete_source($arr)
	{
		header ("Content-Type: text/html; charset=" . aw_global_get("charset"));
		$cl_json = new json();

		$errorstring = "";
		$error = false;
		$keywords = array();

		$option_data = array(
			"error" => &$error,// recommended
			"errorstring" => &$errorstring,// optional
			"options" => &$keywords,// required
			"limited" => false,// whether option count limiting applied or not. applicable only for real time autocomplete.
		);

		if (!trim($arr["customer_search_keywords"]))
		{
			exit ($cl_json->encode($option_data));
		}

		$word = strstr($arr["customer_search_keywords"], ",") ? substr(strrchr($arr["customer_search_keywords"], ","), 1) : trim($arr["customer_search_keywords"]);
		// $prev_words = strstr($arr["customer_search_keywords"], ",") ? trim(substr($arr["customer_search_keywords"], 0, strrpos($arr["customer_search_keywords"], ","))) : "";
		$keywords= explode(",", $arr["customer_search_keywords"]);
		$args = array(
			"class_id" => array(CL_CRM_COMPANY),
			"limit" => "0,500"
		);

		foreach ($keywords as $keyword)
		{
			$keyword = trim($keyword);

			if ($keyword)
			{
				$args[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array("activity_keywords" => "%," . $keyword . "%")
				));
			}
		}

		$ol = new object_list($args);
		$keywords = array();

		foreach ($ol->arr() as $o)
		{
			preg_match_all("/,({$word}[^,]*)/i", $o->prop("activity_keywords"), $matches);

			if (count($matches[1]))
			{
				$keywords = array_merge($keywords, $matches[1]);
			}
		}

		$keywords = array_unique($keywords);

		exit ($cl_json->encode($option_data));
	}


	/**
		@attrib name=save_as_customer
	**/
	function save_as_customer($arr)
	{
		// add all custs in $check as cust to $cust_cat

		$stchk = explode('_', $arr["cust_cat"]);

		if($stchk[0] == 'status')
		{
			$status = obj($stchk[1]);
			$cur = get_current_company();
			foreach(safe_array($arr["check"]) as $cust)
			{
				$company = obj($cust);
				$crel = $this->get_cust_rel($company, true,$cur);
				if (!$crel->is_connected_to(array("to" => $status->id())))
				{
					$crel->connect(array(
						"to" => $status->id(),
						"type" => RELTYPE_STATUS
					));
				}
			}
		}
		else
		{
			$cat = obj($arr["cust_cat"]);
			foreach(safe_array($arr["check"]) as $cust)
			{
				if (!$cat->is_connected_to(array("to" => $cust)))
				{
					$cat->connect(array(
						"to" => $cust,
						"type" => "RELTYPE_CUSTOMER"
					));
				}
			}
		}
		return $arr["post_ru"];
	}

	function on_create_company($arr)
	{
		return false;
		// make sure all companies added are added under the current user's company
		$o = obj($arr["oid"]);
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		if ($co != $o->parent())
		{
			$o->set_parent($co);
			$o->save();
		}
	}

	/**
		@attrib name=save_report
	**/
	function save_report($arr)
	{
		$o = obj();
		$arr = $_POST;
		$o->set_class_id(CL_CRM_REPORT_ENTRY);
		$o->set_parent($arr["id"]);
		$o->set_prop("cust", $arr["stats_s_cust"]);
		$o->set_prop("cust_type", $arr["stats_s_cust_type"]);
		$o->set_prop("proj", $arr["stats_s_proj"]);
		$o->set_prop("worker", $arr["stats_s_worker"]);
		$o->set_prop("worker_sel", $arr["stats_s_worker_sel"]);
		$o->set_prop("from", date_edit::get_timestamp($arr["stats_s_from"]));
		$o->set_prop("to", date_edit::get_timestamp($arr["stats_s_to"]));
		$o->set_prop("time_sel", $arr["stats_s_time_sel"]);
		$o->set_prop("state", $arr["stats_s_state"]);
		$o->set_prop("project_mgr", $arr["project_mgr"]);
		$o->set_prop("bill_state", $arr["stats_s_bill_state"]);
		$o->set_prop("only_billable", $arr["stats_s_only_billable"]);
		$o->set_prop("area", $arr["stats_s_area"]);
		$o->set_prop("res_type", $arr["stats_s_res_type"]);
		$o->save();
		return html::get_change_url($o->id(), array("return_url" => $arr["post_ru"]));
	}

	/**deprecated
		@attrib name=p_view_switch
	**/
	function p_view_switch($arr)
	{
		$_SESSION["crm"]["people_view"] = ($_SESSION["crm"]["people_view"] == "edit" ? "view" : "edit");
		return $arr["post_ru"];
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ($tbl === "aw_account_balances")
		{
			$i = get_instance(CL_CRM_CATEGORY);
                        return $i->do_db_upgrade($tbl, $field);
		}
		if ("kliendibaas_firma" === $tbl)
		{
			switch($field)
			{
				case "user_checkbox_1":
				case "user_checkbox_2":
				case "user_checkbox_3":
				case "user_checkbox_4":
				case "user_checkbox_5":
				case "cust":
				case "cust_contract_date":
				case "cust_contract_creator":
				case "cust_priority":
				case "contact_person":
				case "contact_person2":
				case "contact_person3":
				case "client_manager":
				case "buyer":
				case "buyer_contract_creator":
				case "buyer_contract_person":
				case "buyer_contract_person2":
				case "buyer_contract_person3":
				case "buyer_contract_date":
				case "buyer_priority":
				case "aw_currency":
				case "phone_id":
				case "telefax_id":
				case "url_id":
				case "email_id":
				case "aw_bank_account":
				case "bill_due_date_days":
				case "language":
				case "year_founded":
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => "int",
					));
					return true;

				case "aw_bill_due_days":
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => "int"
					));
					return true;

				case "bill_penalty_pct":
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => "double"
					));
					return true;

				case "aw_userta1":
				case "aw_userta2":
				case "aw_userta3":
				case "aw_userta4":
				case "aw_userta5":
				case "aw_userta6":
				case "aw_userta7":
				case "aw_userta8":
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => "text"
					));
					$this->resque_data_from_meta($tbl, $field);
					return true;

				case "activity_keywords":
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => "text"
					));
					return true;

				case "tax_nr":
				case "aw_short_name":
				case "code":
					$this->db_add_col($tbl, array(
						"name" => $field,
						"type" => "varchar(100)"
					));
					return true;
					break;
			}
		}
		return false;
	}

	function resque_data_from_meta($table, $f)
	{
		$map = array(
			"aw_userta1" => "userta1",
			"aw_userta2" => "userta2",
			"aw_userta3" => "userta3",
			"aw_userta4" => "userta4",
			"aw_userta5" => "userta5",
			"aw_userta6" => "userta6",
			"aw_userta7" => "userta7",
			"aw_userta8" => "userta8",
		);
		if(!empty($map[$f]))
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_COMPANY,
				"lang_id" => array(),
				"site_id" => array(),
			));
			foreach($ol->arr() as $oid => $o)
			{
				$v = str_replace("'", "\'", $o->meta($map[$f]));
				$this->db_query("UPDATE kliendibaas_firma SET $f = '$v' WHERE oid = '$oid' LIMIT 1");
			}
		}
	}

	function _init_ext_sys_t($t)
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

	function _ext_sys_t(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_ext_sys_t($t);

		$format = t('%s siduss&uuml;steemid');
		$t->set_caption(sprintf($format, $arr['obj_inst']->name()));

		$crel = $this->get_cust_rel($arr["obj_inst"], true);
		if (!$crel)
		{
			return;
		}
		$data = array();
		foreach($crel->connections_from(array("type" => "RELTYPE_EXT_SYS_ENTRY")) as $c)
		{
			$ent = $c->to();
			$data[$ent->prop("ext_sys_id")] = $ent->prop("value");
		}
		// list all ext systems and let the user edit those
		$ol = new object_list(array(
			"class_id" => CL_EXTERNAL_SYSTEM,
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
		$crel = $this->get_cust_rel($arr["obj_inst"], true);
		if (!$crel)
		{
			return;
		}
		$ol = new object_list(array(
			"class_id" => CL_EXTERNAL_SYSTEM
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

	/**
		@attrib name=vm_set_act_ver
	**/
	function vm_set_act_ver($arr)
	{
		$i = new version_manager();
		return $i->vm_set_act_ver($arr);
	}

	/**
		@attrib name=vm_delete_versions
	**/
	function vm_delete_versions($arr)
	{
		$i = new version_manager();
		return $i->vm_delete_versions($arr);
	}

	function _get_client_category($arr)
	{
		if (!is_oid($arr["obj_inst"]->id()))
		{
			return;
		}
		$conns = $arr["obj_inst"]->connections_to(array(
			"from.class_id" => CL_CRM_CATEGORY,
			"type" => "RELTYPE_CUSTOMER"
		));
		if (!count($conns))
		{
			return PROP_IGNORE;
		}
		$c = reset($conns);
		$cat = $c->from();
		$cats = array();
		while($cat->class_id() != CL_CRM_COMPANY && count($conns))
		{
			$cats[] = $cat->name();
			$conns = $cat->connections_to(array(
				"from.class_id" => CL_CRM_CATEGORY,
				"type" => "RELTYPE_CATEGORY"
			));
			$c = reset($conns);
			if ($c)
			{
				$cat = $c->from();
			}
		}
		$url = $this->mk_my_orb(
			"do_search",array(
			'id' => $arr['id'],
			"return_url" => $arr["post_ru"],
		 	"pn" => "category",
		 	"clid" => array(CL_CRM_CATEGORY),
		 	),"popup_search");

		$arr["prop"]["value"] = join(" / ", array_reverse($cats)).
		'<a href=\'javascript:aw_popup_scroll("'.$url.'","Otsing",550,500)\' alt="Otsi" title="Otsi" tabindex="10" style=""><img src="http://intranet.automatweb.com/automatweb/images/icons/search.gif" border="0"></a>';
		return PROP_OK;
	}


	/** implement our own view!
		@attrib name=view nologin=1
		@param id required
		@param cfgform optional
	**/
	function view($arr = array())
	{
		if (!empty($arr["cfgform"]))
		{
			$cfg = get_instance(CL_CFGFORM);
			$props = $cfg->get_props_from_cfgform(array("id" => $arr["cfgform"]));
		}
		else
		{
			$cfg = get_instance("cfg/cfgutils");
			$props = $cfg->load_properties(array(
				"clid" => CL_CRM_COMPANY
			));
		}

		$this->read_template("show.tpl");

		$o = obj($arr["id"]);
		$l = "";
		foreach($props as $pn => $pd)
		{
			//echo "$pn => $pd[caption] <br>";
			$this->vars(array(
				"prop" => ( empty($pd["caption"]) ) ? "" : $pd["caption"],
				"value" => nl2br($o->prop_str($pn, in_array($pn, array("ettevotlusvorm", "firmajuht", "telefax_id"))))
			));
			$l .= $this->parse("LINE");
		}

		$this->vars(array(
			"LINE" => $l
		));
		return $this->parse();
	}

	//DEPRECATED
	function display_persons_table($person_list, $t)
	{  $arr = array( "prop" => array( "vcl_inst" => $t ), "disp_persons" => $person_list, "obj_inst" => get_current_company() ); $i = get_instance(
		"applications/crm/crm_company_people_impl"); $i->_get_human_resources($arr);
	}

	/**
		@attrib name=save_default_poll
	**/
	function save_default_poll($arr)
	{
		$o = obj($arr["id"]);
		$def = $o->get_first_obj_by_reltype("RELTYPE_DEF_POLL");
		if ($def)
		{
			$o->disconnect(array("from" => $def->id()));
		}
		$o->connect(array("to" => $arr["def_poll"], "type" => "RELTYPE_DEF_POLL"));
		return $arr["post_ru"];
	}

	/**
		@attrib name=send_mails
	**/
	function send_mails($arr)
	{
		$send_to = "";
		$mails = array();
		foreach($arr["check"] as $cust)
		{
			$email = "";
			if($this->can("view" , $cust))
			{
				$customer = obj($cust);
				$email = $customer->get_first_obj_by_reltype("RELTYPE_EMAIL");
				if(is_oid($customer->prop("email")))
				{
					$email = obj($customer->prop("email"));
				}
				if(!is_object($email))
				{
					$email = $customer->get_first_obj_by_reltype("RELTYPE_EMAIL");
				}
				if(is_object($email) && is_email($email->prop("mail")))
				{
					$mails[] = $email->prop("mail");
				}
			}
		}
		$send_to = join($mails , ",");
		$user = aw_global_get("uid");

		$mfrom = aw_global_get("uid_oid");
		$user_obj = obj($mfrom);
		$person = $user_obj->get_first_obj_by_reltype("RELTYPE_PERSON");
		if(is_object($person))
		{
			$mfrom = $person->id();
		}
		return $this->mk_my_orb('new',array(
			'parent' => $arr['id'],
			"return_url" => $arr["post_ru"],
		 	"mto" => $send_to,
		 	"mfrom" => $mfrom,
		 	"crm" => 1,
		 ),CL_MESSAGE);
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

	function _get_cust_buyer_contract_creator($arr)
	{
		$co = get_current_company();
		$crel = $this->get_cust_rel($co, false, $arr["obj_inst"]);
		// list of all persons in my company
		$arr["prop"]["options"] = $this->get_employee_picker($co, true);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("cust_contract_creator");
		}

		if (!isset($arr["prop"]["options"][$arr["prop"]["value"]]) && $this->can("view", $arr["prop"]["value"]))
		{
			$v = obj($arr["prop"]["value"]);
			$arr["prop"]["options"][$arr["prop"]["value"]] = $v->name();
		}
	}

	function _set_cust_buyer_contract_creator($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop("cust_contract_creator", $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _get_cust_buyer_contract_date($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("cust_contract_date");
		}
		$arr["prop"]["year_from"] = 1990;
		$arr["prop"]["year_to"] = date("Y")+1;
	}

	function _set_cust_buyer_contract_date($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop("cust_contract_date", $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _get_buyer_contact_person($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		// list of all persons in my company
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		$arr["prop"]["options"] = $this->get_employee_picker(obj($co), true);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("buyer_contact_person");
		}

		if (!isset($arr["prop"]["options"][$arr["prop"]["value"]]) && $this->can("view", $arr["prop"]["value"]))
		{
			$v = obj($arr["prop"]["value"]);
			$arr["prop"]["options"][$arr["prop"]["value"]] = $v->name();
		}
	}

	function _set_buyer_contact_person($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		$crel->set_prop("buyer_contact_person", $arr["prop"]["value"]);
		$crel->save();
	}


	function _get_buyer_contact_person2($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		// list of all persons in my company
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		$arr["prop"]["options"] = $this->get_employee_picker(obj($co), true);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("buyer_contact_person2");
		}

		if (!isset($arr["prop"]["options"][$arr["prop"]["value"]]) && $this->can("view", $arr["prop"]["value"]))
		{
			$v = obj($arr["prop"]["value"]);
			$arr["prop"]["options"][$arr["prop"]["value"]] = $v->name();
		}
	}

	function _set_buyer_contact_person2($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		$crel->set_prop("buyer_contact_person2", $arr["prop"]["value"]);
		$crel->save();
	}


	function _get_buyer_contact_person3($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		// list of all persons in my company
		$u = get_instance(CL_USER);
		$co = $u->get_current_company();
		$arr["prop"]["options"] = $this->get_employee_picker(obj($co), true);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("buyer_contact_person3");
		}

		if (!isset($arr["prop"]["options"][$arr["prop"]["value"]]) && $this->can("view", $arr["prop"]["value"]))
		{
			$v = obj($arr["prop"]["value"]);
			$arr["prop"]["options"][$arr["prop"]["value"]] = $v->name();
		}
	}

	function _set_buyer_contact_person3($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		$crel->set_prop("buyer_contact_person3", $arr["prop"]["value"]);
		$crel->save();
	}

	function _get_cust_buyer_priority($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("priority");
		}
	}

	function _set_cust_buyer_priority($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop("priority", $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _get_referal_type($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("referal_type");
		}
	}

	function _set_referal_type($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, true, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop("referal_type", $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _get_buyer_client_manager($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		$u = get_instance(CL_USER);
		$arr["prop"]["options"] = $this->get_employee_picker(get_current_company(), true);
		if ($arr["new"])
		{
			$arr["prop"]["value"] = $u->get_current_person();
		}

		if ($crel)
		{
			$data["value"] = $crel->prop("client_manager");
		}
	}

	function _set_buyer_client_manager($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop("client_manager", $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _get_buyer_bill_due_date_days($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("bill_due_date_days");
		}
	}

	function _set_buyer_bill_due_date_days($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop("bill_due_date_days", $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _set_bill_tolerance($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop("bill_tolerance", $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _get_bill_tolerance($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($arr["obj_inst"], false, $cur);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("bill_tolerance");
		}
	}

	function _get_buyer_bill_penalty_pct($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("bill_penalty_pct");
		}
	}

	function _set_buyer_bill_penalty_pct($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop("bill_penalty_pct", $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _get_buyer_buyer_contract_person($arr)
	{
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$arr["prop"]["value"] = $crel->prop("buyer_contact_person");
		}
		if (!isset($arr["prop"]["options"][$arr["prop"]["value"]]) && $this->can("view", $arr["prop"]["value"]))
		{
			$v = obj($arr["prop"]["value"]);
			$arr["prop"]["options"][$arr["prop"]["value"]] = $v->name();
		}
	}

	function _set_buyer_buyer_contract_person($arr)
	{
		if (!$arr["request"]["co_is_buyer"])
		{
			return;
		}
		$cur = get_current_company();
		$crel = $this->get_cust_rel($cur, false, $arr["obj_inst"]);
		if ($crel)
		{
			$crel->set_prop("buyer_contact_person", $arr["prop"]["value"]);
			$crel->save();
		}
	}

	function _get_pohitegevus($arr)
	{
		$arr["prop"]["options"] = array("" => t("--Vali--"));//FIXME: kui palju objekte siis jooskeb kinni // + safe_array(get_instance(CL_PERSONNEL_MANAGEMENT)->get_sectors());
		if ($arr["new"] && $arr["request"]["sector"])
		{
			$arr["prop"]["value"] = $arr["request"]["sector"];
		}
	}

//////////////////////////////////////
//
//  Try to make some kind of API for organisation class
//
//////////////////////////////////////

	/** Adds phone number under the organisations general contact information tab

		@attrib name=add_phone params=name api=1

		@param organisation_object required type=object
			The organisation object where to add the phone number
		@param phone_object optional type=object
			If this is set, then no new phone number object is created, but this one is added to the organisation instead
		@param phone_number optional type=string
			The phone number
		@param active optional type=bool
			If the phone number is selected ("Select one" column value)
		@param type optional type=string default=phone
			Possible values: [ phone | fax ]. Sets the type of the phone number, as in AW they both are phone numbers and the difference is only in relation type

		@returns Created phone object id.

		@example
			$org_inst = get_instance(CL_CRM_COMPANY);
			$org_obj = new object();
			$org_obj->set_class_id(CL_CRM_COMPANY);
			$org_obj->set_name('Some Organisation Name');
			$org_obj->set_parent(666);
			$org_obj->save();

			$crm_phone_object_oid = $org_inst->add_phone(array(
				'organisation_object' => $org_obj,
				'phone_number' => '+37 2503 7767'

			));
	**/
	function add_phone($arr)
	{
		$org_obj = $arr['organisation_object'];

		if (!empty($arr['phone_object']))
		{
			$phone_obj_id = $arr['phone_object']->id();
		}
		else
		{
			$o = new object();
			$o->set_class_id(CL_CRM_PHONE);
			$o->set_parent($org_obj->id());
			$o->set_name($arr['phone_number']);
			$o->save();
			$phone_obj_id = $o->id();
		}

		$type = 17; // phone
		if ($arr['type'] == 'fax')
		{
			$type = 18; // fax
		}

		$org_obj->connect(array(
			'to' => $phone_obj_id,
			'type' => $type
		));
		if ($arr['active'] === true)
		{
			$org_obj->set_prop('phone_id', $phone_obj_id);
			$org_obj->save();
		}

		return $phone_obj_id;
	}

	/** Adds web address under the organisations general contact information tab

		@attrib name=add_web_address params=name api=1

		@param organisation_object required type=object
			The organisation object where to add the web address
		@param web_address_object optional type=object
			If this is set, then no new web address object is created, but this one is added to the organisation instead
		@param web_address optional type=string
			The web address
		@param active optional type=bool
			If the web address is selected ("Select one" column value)

		@returns Created web address object id
	**/
	function add_web_address($arr)
	{
		$org_obj = $arr['organisation_object'];

		if (!empty($arr['web_address_object']))
		{
			$web_address_obj_id = $arr['web_address_object']->id();
		}
		else
		{
			$o = new object();
			$o->set_class_id(CL_EXTLINK);
			$o->set_parent($org_obj->id());
			$o->set_name($org_obj->name());
			$o->set_prop('url', $arr['web_address']);
			$o->save();
			$web_address_obj_id = $o->id();
		}

		$org_obj->connect(array(
			'to' => $web_address_obj_id,
			'type' => 16
		));

		if ($arr['active'] === true)
		{
			$org_obj->set_prop('url_id', $web_address_obj_id);
			$org_obj->save();
		}

		return $web_address_obj_id;
	}


/*Aadress:
(kui mitu siis Aadress 1, Aadress 2 jne yksteise all)
Telefon: xxxxxxx, yyyyyyy
Faks: tttttttt,iiiiiiii
E-mail: aaa@bbb.ee (klikitav)
WWW: http://www.domain.ee (klikitav)
Bank accounts: yksteise all
*/

	function init_short_description_table($t)
	{
		$t->define_field(array(
			"name" => "caption",
		));
		$t->define_field(array(
			"name" => "data",
		));
	}

	/** returns a line of info about the company - name, phone, fax , e-mail , web-page , bank accounts
		@attrib api=1 params=pos

		@param c required type=oid
			The company to return the info for
	**/
	function get_short_description($c)
	{
		$p = obj($c);
		$t = new vcl_table();
		$this->init_short_description_table($t);
//		$t->define_data(array("caption" => t("Aadress:")));

/*		$conns = $p->connections_from(array(
			"type" => "RELTYPE_ADDRESS",
		));
		$multi_addr = (count($conns) > 1);
		$count = 0;
		foreach($conns as $c)
		{
			$count++;
			$aa = array();
			$a = $c->to();
			if($a->prop("aadress")) 	$aa[] = $a->prop("aadress");
			if($a->prop("postiindeks")) 	$aa[] = $a->prop("postiindeks");
			if($a->prop("linn.name")) 	$aa[] = $a->prop("linn.name");
			if($a->prop("maakond.name")) 	$aa[] = $a->prop("maakond.name");
			if($a->prop("piirkond.name")) 	$aa[] = $a->prop("piirkond.name");
			if($a->prop("riik.name")) 	$aa[] = $a->prop("riik.name");
			$caption = t("Aadress")." ".($multi_addr ? $count:"").":";
			if(sizeof($aa))
			{
				$t->define_data(array(
					"caption" => $caption,
					"data" => join($aa, ", "),
				));
			}
		}
*/

		$conns = $p->connections_from(array(
			"type" => "RELTYPE_ADDRESS_ALT",
		));
		$multi_addr = (count($conns) > 1);
		$count = 0;
		foreach($conns as $c)
		{
			$count++;
			$a = $c->to();
			$caption = t("Aadress")." ".($multi_addr ? $count:"").":";
			$t->define_data(array(
				"caption" => $caption,
				"data" => $a->name(),
			));
		}


		$conns = $p->connections_from(array(
			"type" => "RELTYPE_PHONE"
		));
		if(sizeof($conns))
		{
			$aa = array();
			foreach($conns as $c)
			{
				$a = $c->to();
				$aa[] = $a->name();
			}
			$t->define_data(array(
				"caption" => t("Telefon").":",
				"data" => join($aa, ", "),
			));
		}
		$conns = $p->connections_from(array(
			"type" => "RELTYPE_TELEFAX"
		));
		if(sizeof($conns))
		{
			$aa = array();
			foreach($conns as $c)
			{
				$a = $c->to();
				$aa[] = $a->name();
			}
			$t->define_data(array(
				"caption" => t("Faks").":",
				"data" => join($aa, ", "),
			));
		}
		$conns = $p->connections_from(array(
			"type" => "RELTYPE_EMAIL"
		));
		if(sizeof($conns))
		{
			$aa = array();
			foreach($conns as $c)
			{
				$a = $c->to();
				$aa[] = $a->prop("mail");
			}
			$t->define_data(array(
				"caption" => t("E-mail").":",
				"data" => join($aa, ", "),
			));
		}

		$conns = $p->connections_from(array(
			"type" => "RELTYPE_URL"
		));

		if(count($conns))
		{
			$aa = array();
			foreach($conns as $c)
			{
				$a = $c->to();
				$aa[] = $a->name();
			}
			$t->define_data(array(
				"caption" => t("WWW").":",
				"data" => join($aa, "\n<br>"),
			));
		}

		$conns = $p->connections_from(array(
			"type" => "RELTYPE_BANK_ACCOUNT"
		));

		if(count($conns))
		{
			$aa = array();
			foreach($conns as $c)
			{
				$a = $c->to();
				$aa[] = $a->prop("acct_no");
			}
			$t->define_data(array(
				"caption" => t("Bank accounts").":",
				"data" => join($aa, "\n<br>"),
			));
		}
		return $t->draw();
	}

	/**
		@attrib name=set_project_to_mail nologin=1 is_public=1 all_args=1
 	**/
	function set_project_to_mail($arr)
	{
		foreach($arr["sel"] as $id)
		{
			if(is_oid($id) && $this->can("view" , $id))
			{
				$o = obj($id);
				$o->set_prop("project" , $arr["proj"]);
				$o->save();
			}
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=save_rows all_args=1
	**/
	function save_rows($arr)
	{
		foreach($arr["rows"] as $key => $row)
		{
			if($row["time_to_cust"] != $row["time_to_cust_real"])
			{
				if(!($this->can("view" , $key)))
				{
					continue;
				}
				$br = obj($key);
				$br->set_prop("time_to_cust" , str_replace("," , "." , $row["time_to_cust"]));
				$br->save();
				//arr(str_replace("," , "." , $row["time_to_cust_real"])); arr($br->prop("time_to_cust"));
			}
		}
		return $arr["post_ru"];
	}

	function _get_insurance_tbl(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Name"),
		));
		$t->define_field(array(
			"name" => "expires",
			"caption" => t("Expires"),
		));
		$t->define_field(array(
			"name" => "status",
			"caption" => t("Status"),
		));
		$t->define_field(array(
			"name" => "certificate",
			"caption" => t("Certificate"),
		));
		$t->define_field(array(
			"name" => "company",
			"caption" => t("Company"),
		));
		$t->define_field(array(
			"name" => "broker",
			"caption" => t("Broker"),
		));
		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Sum"),
		));
		$t->define_field(array(
			"name" => "type",
			"caption" => t("Type"),
		));
		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
		));
		$file_inst = get_instance(CL_FILE);
		foreach($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_INSURANCE")) as $conn)
		{
			$insurance = $conn->to();
			$t->define_data(array(
				"name" => html::obj_change_url($insurance->id()),
				"expires" => date("d.m.Y" , $insurance->prop("expires")),
				"status" => $insurance->prop("expires") > time() ? "<font color=green>".t("Valid")."</font>"  : "<font color=red>".t("Expired")."</font>",
				"certificate" => html::href(array(
					"url" => $file_inst->get_url($insurance->prop("certificate"), $insurance->prop("certificate.name")),
					"caption" => $insurance->prop("certificate.name"),
					"target" => "New window",
				)),
				"company" => html::obj_change_url($insurance->prop("company")),
				"broker" => html::obj_change_url($insurance->prop("broker")),
				"sum" => $insurance->prop("insurance_sum"),
				"type" => $insurance->prop("insurance_type.name"),
				"oid" => $insurance->id(),
			));
		}
	}

	function _get_insurance_tb(&$arr)
	{
		$tb = $arr["prop"]["toolbar"];

		$parent = $arr["obj_inst"]->parent();

		$seti = new crm_settings();
		$sts = $seti->get_current_settings();
		if ($sts && $this->can("view" , $sts->prop("insurance_link_menu")))
		{
			$parent = $sts->prop("insurance_link_menu");
		}

		$tb->add_button(array(
			"name" => "new",
			"img" => "new.gif",
			"tooltip" => t("Lisa uus kindlustus"),
			"url" => $this->mk_my_orb("new", array(
				"alias_to" => $arr["obj_inst"]->id(),
				"reltype" => 68,
				"return_url" => get_ru(),
				"parent" => $parent,
			), "crm_insurance"),
		));

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta"),
			"action" => "delete_objects",
			"confirm" => t("Oled kindel, et kustutada?"),
		));
	}

	/**
	@attrib name=statuses_save all_args=1
	**/
	function statuses_save($arr)
	{
		$company2 = obj($arr["id"]);
		$company = get_current_company();
		$conn = new connection();
		$customer_data = new object_list(array(
			"buyer" => $company2->id(),
			"seller" => $company->id(),
			"class_id" => array(CL_CRM_COMPANY_CUSTOMER_DATA)
		));
		foreach($customer_data->list as $cust_data_id)
		{
			$cd = obj($cust_data_id);
			foreach($arr["sel2"] as $sid=>$set)
			{
				if($arr["sel"][$sid] && $set == 0)
				{
					$status = obj($sid);
					$cd->connect(array(
						"to" => $status,
						"type" => RELTYPE_STATUS
					));
				}
				elseif(!$arr["sel"][$sid] && $set == 1)
				{
					$status = obj($sid);
					$cd->disconnect(array(
						"from" => $status
					));
				}
			}
		}
		return $arr["post_ru"];
	}

	/**
	@attrib name=add_customer_buyer all_args=1
	**/
	function add_customer_buyer($arr)
	{
		$seller = get_current_company();
		$buyer = obj($arr["id"]);
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"buyer" => $buyer->id(),
			"seller" => $seller->id()
		));

		if (!$ol->count())
		{
			$o = obj();
			$o->set_class_id(CL_CRM_COMPANY_CUSTOMER_DATA);
			$o->set_name("Kliendisuhe ".$seller->name()." => ".$buyer->name());
			$o->set_parent($seller->id());
			$o->set_prop("seller", $seller->id()); // yes this is correct, cause I'm a lazy iduit
			$o->set_prop("buyer", $buyer->id());
			$o->save();
		}

		return $arr["post_ru"];
	}

	function _get_statuses_tb($arr)
	{
		$tb = $arr["prop"]["toolbar"];

		$company = get_current_company();
		$parent = (isset($arr['request']['tf']) && strlen($arr['request']['tf'])>1)?$arr['request']['tf']:$company->id();
		$params = array();
		if(!empty($arr["request"][self::REQVAR_CATEGORY]))
		{
			$params[self::REQVAR_CATEGORY] =  $arr["request"][self::REQVAR_CATEGORY];
		}
		else
		{
			$params[self::REQVAR_CATEGORY] =  0;
		}

		$tb->add_new_button(array(CL_CRM_COMPANY_STATUS), $parent, '', $params);
		$tb->add_delete_button();

		$company2 = obj($arr["request"]["id"]);
		$conn = new connection();
		$customer_data = new object_list(array(
			"buyer" => $company2->id(),
			"seller" => $company->id(),
			"class_id" => array(CL_CRM_COMPANY_CUSTOMER_DATA)
		));
		if(count($customer_data->list))
		{
			$tb->add_button(array(
				"name" => "save",
				"img" => "save.gif",
				"tooltip" => t("Salvesta staatused"),
				"action" => "statuses_save"
			));
		}
		else
		{
			$tb->add_button(array(
				"name" => "save",
				"img" => "save.gif",
				"tooltip" => t("Lisa organisatsioon ostjaks"),
				"action" => "add_customer_buyer"
			));
		}
	}

	function _get_statuses_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->start_tree(array(
			"type" => TREE_DHTML,
			"has_root" => 0,
			"tree_id" => "company_statuses",
			"persist_state" => 1,
		));

		$st = get_instance(CL_CRM_COMPANY_STATUS);
		$categories = $st->categories(0);
		$company = get_current_company();
		foreach($categories as $id=>$cat)
		{
			$t->add_item(0,array(
				"id" => $id,
				"name" => $cat,
				"iconurl" => icons::get_icon_url(CL_MENU),
				"url" => aw_url_change_var(array(
					"tf"=> $id,
					self::REQVAR_CATEGORY => $id
				))
			));

			$ol = new object_list(array(
				"class_id" => array(CL_CRM_COMPANY_STATUS),
				"category" => $id,
				"parent" => $company->id()
			));

			if(count($ol->list))
			{
				foreach($ol->arr() as $o)
				{
					$t->add_item($id, array(
						"id" => $o->id(),
						"name" => $o->name(),
						"url" => aw_url_change_var(array(
							"tf" => $o->id(),
							self::REQVAR_CATEGORY => $id
						)),
					));
					$this->get_s_tree_stuff($o->id(), $t, $id);
				}
			}
		}
	}

	function get_s_tree_stuff($parent, $t, $cat)
	{
		if(substr($parent,0,3) === 'cat')
		{
			$parent = substr($parent,3);
			$add = 'cat';
		}
		$ol = new object_list(array(
			"class_id" => array(CL_CRM_COMPANY_STATUS),
			"parent" => $parent
		));
		if(count($ol->list))
		{
			foreach($ol->list as $o)
			{
				$o = obj($o);
				$url = array(
					"tf" => $add.$o->id(),
					self::REQVAR_CATEGORY => 'st_'.$o->id()
				);
				if($cat)
				{
					$url[self::REQVAR_CATEGORY] = $cat;
				}
				$t->add_item($add.$parent, array(
					"id" => $add.$o->id(),
					"name" => $o->name(),
					"url" => aw_url_change_var($url),
				));
				$this->get_s_tree_stuff($add.$o->id(), $t, $cat);
			}
		}

	}

	function _get_statuses_set_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "cat",
			"align" => "center"
		));
		$t->define_field(array(
			"name" => "name",
			"align" => "center"
		));

		$st = get_instance(CL_CRM_COMPANY_STATUS);
		$categories = $st->categories(0);

		$company = get_current_company();
		$company2 = obj($arr["request"]["id"]);
		$customer_data = new object_list(array(
			"buyer" => $company2->id(),
			"seller" => $company->id(),
			"class_id" => array(CL_CRM_COMPANY_CUSTOMER_DATA)
		));

		$conn = array();
		foreach($customer_data->list as $cd)
		{
			$cd = obj($cd);
			$conn = $cd->connections_from(array(
				"type" => RELTYPE_STATUS
			));
		}

		foreach($conn as $c)
		{
			$status = obj($c->conn["to"]);
			$t->define_data(array(
				"cat" => $categories[$status->prop("category")],
				"name" => $c->conn["to.name"],
			));
		}
	}

	function _get_statuses_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$st = get_instance(CL_CRM_COMPANY_STATUS);
		$categories = $st->categories(0);

		if(empty($arr["request"]["tf"]))
		{
			$t->define_field(array(
				"caption" => t("Nimi"),
				"name" => "name",
				"align" => "center",
				"sortable" => 1,
			));
			$t->set_caption('Kategooriad');

			foreach($categories as $id=>$cat)
			{
				$t->define_data(array(
					"name" => html::href(array(
						"url" => aw_url_change_var(array(
							"tf" => $id,
							self::REQVAR_CATEGORY => $id
						)),
						"caption" => $cat,
					)),
					"sel" => $id,
				));
			}
		}
		elseif(strlen($arr["request"]["tf"]) < 2)
		{
			$t->set_caption($categories[$arr["request"][self::REQVAR_CATEGORY]]);

			$t->define_field(array(
				"caption" => t("Vali"),
				"name" => "check",
				"align" => "center",
				"sortable" => 1,
			));
			$t->define_field(array(
				"caption" => t("Nimi"),
				"name" => "name",
				"align" => "center",
				"sortable" => 1,
			));

			$company = get_current_company();
			$company2 = obj($arr["request"]["id"]);
			$customer_data = new object_list(array(
				"buyer" => $company2->id(),
				"seller" => $company->id(),
				"class_id" => array(CL_CRM_COMPANY_CUSTOMER_DATA)
			));
			$ol = new object_list(array(
				"class_id" => array(CL_CRM_COMPANY_STATUS),
				"category" => $arr["request"]["tf"],
				"parent" => $company->id()
			));
			foreach($ol->arr() as $o)
			{
				$conn = new connection();
				foreach($customer_data->list as $cdid)
				{
					$c = $conn->find(array(
						"from" => $cdid,
						"to" => $o->id()
					));
				}
				$t->define_data(array(
					"name" => html::obj_change_url($o),
					"check" => html::checkbox(array(
						"name" => "sel[".$o->id()."]",
						"value" => $o->id(),
						"checked" => (count($c))?1:0
					)).html::hidden(array(
						"name" => "sel2[".$o->id()."]",
						"value" => (count($c))?1:0
					))
				));
			}
		}
		else
		{
			$parent = obj($arr["request"]["tf"]);
			$t->set_caption($parent->name());

			$t->define_field(array(
				"caption" => t("Vali"),
				"name" => "check",
				"align" => "center",
				"sortable" => 1,
			));
			$t->define_field(array(
				"caption" => t("Nimi"),
				"name" => "name",
				"align" => "center",
				"sortable" => 1,
			));

			$company = get_current_company();
			$company2 = obj($arr["request"]["id"]);
			$customer_data = new object_list(array(
				"buyer" => $company2->id(),
				"seller" => $company->id(),
				"class_id" => array(CL_CRM_COMPANY_CUSTOMER_DATA)
			));

			$ol = new object_list(array(
				"class_id" => array(CL_CRM_COMPANY_STATUS),
				"parent" => $arr["request"]["tf"],
			));
			foreach($ol->arr() as $o)
			{
				$conn = new connection();
				foreach($customer_data->list as $cdid)
				{
					$c = $conn->find(array(
						"from" => $cdid,
						"to" => $o->id()
					));
				}
				$t->define_data(array(
					"name" => html::obj_change_url($o),
					"check" => html::checkbox(array(
						"name" => "sel[".$o->id()."]",
						"value" => $o->id(),
						"checked" => (count($c))?1:0
					)).html::hidden(array(
						"name" => "sel2[".$o->id()."]",
						"value" => (count($c))?1:0
					))
				));
			}
		}
	}

	/**
	@attrib name=save_contact_rels all_args=1
	**/
	function save_contact_rels($arr)
	{
		$company = obj($arr["id"], array(), CL_CRM_COMPANY);
		$already_existing = array();
		$count_success = 0;
		$oid_error = false;
		foreach($arr["sel"] as $pid)
		{
			try
			{
				$pid = new aw_oid($pid);
				$person = obj($pid, array(), crm_person_obj::CLID);
				$company->add_employee(null, $person);
				++$count_success;
			}
			catch (awex_redundant_instruction $e)
			{
				$already_existing[] = $person->name();
			}
			catch (Exception $e)
			{
				$oid_error = true;
			}
		}

		$this->show_success_text(sprintf(t("Loodi %s t&ouml;&ouml;suhet"), $count_success));

		if ($already_existing)
		{
			$count_existing = count($already_existing);
			$already_existing = $count_existing > 10 ?  sprintf(t("%s ... jpt."), implode(", ", array_slice($already_existing, 0, 9))) : implode(", ", $already_existing);
			$this->show_msg_text(sprintf(t("%s isikuga oli t&ouml;&ouml;suhe oli juba olemas (%s)"), $count_existing, $already_existing));
		}

		if ($oid_error)
		{
			$this->show_error_text(t("Isikute loomisel esines valesid objektiidentifikaatoreid."));
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=add_payment all_args=1
	**/
	function add_payment($arr)
	{
		foreach($arr["sel"] as $bill_id)
		{
			if($this->can("view" , $bill_id))
			{
				$bill = obj($bill_id);
				$payment = $bill->add_payment();
				$url = html::get_change_url($payment, array("return_url" => $arr["post_ru"]));
				return $url;
			}
		}
		return $arr["post_ru"];
	}

	function callback_get_default_group($arr)
	{
		$seti = get_instance(CL_CRM_SETTINGS);
		$sts = $seti->get_current_settings();
		$co = get_current_company();
		if ($sts && ($_GET["action"] != "new"))
		{
			if(is_object($co) && $arr["request"]["id"] == $co->id())
			{
				if($sts->prop("default_my_company_tab"))
				{
					return $sts->prop("default_my_company_tab");
				}
			}
			else
			{
				if($sts->prop("default_client_company_tab"))
				{
					return $sts->prop("default_client_company_tab");
				}
			}
		}
		return "general";
	}

	/**
	@attrib name=company_data all_args=1 api=1 params=name
	**/
	function company_data($arr)
	{
		$ol = new object_list(array(
			"class_id" => CL_CRM_COMPANY,
			"name" => $arr["name"],
			"parent" => array(),
			"limit" => 1
		));
		$d = array(
			"address" => "",
			"mail" => "",
			"url" => "http://",
			"phone" => "(+372)",
		);
		if($ol->count() > 0){
			$o = $ol->begin();
			$d["phone"] = $o->prop("phone_id.name");
			$d["mail"] = $o->prop("email_id.mail");
			$d["url"] = $o->prop("url_id.url");
			$d["address"] = $o->prop("contact.name");
		}
		die(json_encode($d));
	}

	private function move_comments_from_meta_to_objects($arr)
	{
		$comments = safe_array($arr["obj_inst"]->prop("comment_history"));

		foreach ($comments as $t)
		{
			if(!is_array($t) || !sizeof($t))
			{
				continue;
			}
			$ol = new object_list(array(
				"class_id" => CL_CRM_SETTINGS,
				"CL_CRM_SETTINGS.RELTYPE_USER" => get_instance(CL_USER)->get_obj_for_uid($t["user"])->id(),
				"lang_id" => array(),
				"limit" => 1,
			));
			$crm_settings = $ol->begin();
			$parent = $this->can("add", $crm_settings->comment_menu) ? $crm_settings->comment_menu : $arr["obj_inst"]->id();
			if (strlen(trim($t["text"])))
			{
				// Store comment as obj
				$comm = obj();
				$comm->set_class_id(CL_COMMENT);
				$comm->set_parent($parent);
				$comm->name = sprintf(t("%s kommentaar organisatsioonile %s"), $t["user"], $arr["obj_inst"]->name);
				$comm->uname = $t["user"];
				$comm->commtext = $t["text"];
				$comm->commtype = $t["type"];
				$comm->save();
				$arr["obj_inst"]->connect(array(
					"to" => $comm->id(),
					"type" => "RELTYPE_COMMENT",
				));
				$this->db_query("UPDATE objects SET created = '".$t["time"]."' WHERE oid = '".$comm->id()."'");
			}
		}
		$arr["obj_inst"]->set_meta("comments_stored_in_objects", 1);
		$arr["obj_inst"]->save();
	}

	private function eligible_to_comment($arr, &$connect_comment_to_customer_data = false)
	{
		$person = obj(get_instance(CL_USER)->get_person_for_uid(aw_global_get("uid")));
		$parent = 0;

		$ol = new object_list(array(
			"class_id" => CL_CRM_SETTINGS,
			"CL_CRM_SETTINGS.RELTYPE_USER" => get_instance(CL_USER)->get_obj_for_uid(aw_global_get("uid"))->id(),
			"limit" => 1
		));
		$crm_settings = $ol->begin();
		if($crm_settings and $this->can("add", $crm_settings->comment_menu))
		{
			$parent = $crm_settings->comment_menu;
		}

		$orgs = $person->get_companies()->ids();
		if(count($orgs) > 0 && !$this->can("add", $parent))
		{
			$ol = new object_list(array(
				"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
				new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array(
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"buyer" => $arr["obj_inst"]->id(),
								"seller" => $orgs,
							),
						)),
						new object_list_filter(array(
							"logic" => "AND",
							"conditions" => array(
								"buyer" => $orgs,
								"seller" => $arr["obj_inst"]->id(),
							),
						)),
					),
				)),
				"limit" => 1,
			));
			if($ol->count() > 0 && $this->can("add", reset($ol->ids())))
			{
				$parent = reset($ol->ids());
				$connect_comment_to_customer_data = true;
			}
		}
		if(!$this->can("add", $parent))
		{
			$parent = $arr["obj_inst"]->id();
		}
		return $parent;
	}

	/**
	@attrib name=del_comment api=1 params=name

	@param id required type=oid acl=delete

	@param post_ru required type=string
	**/
	function del_comment($arr)
	{
		$o = obj($arr["id"]);
		$o->delete();
		return $arr["post_ru"];
	}

	/** Generate a form for adding or changing an object

		@attrib name=new params=name all_args="1" nologin="1"

		@param parent optional type=int acl="add"
		@param period optional
		@param alias_to optional
		@param alias_to_prop optional
		@param return_url optional
		@param reltype optional type=int

		@returns data formatted by the currently used output client. For example a HTML form if htmlclient is used

		@comment

	**/
	function new_change($args)
	{
		return parent::change($args);
	}

	/**

		@attrib name=change params=name all_args="1"

		@param id optional type=int acl="edit"
		@param group optional
		@param period optional
		@param alias_to optional
		@param alias_to_prop optional
		@param return_url optional

		@returns data formatted by the currently used output client. For example a HTML form if htmlclient is used


		@comment
		id _always_ refers to the objects< table. Always. If you want to load
		any other data, then you'll need to use other field name

	**/
	function dchange($args = array())
	{
		return parent::change($args);
	}

	/** Saves the data that comes from the form generated by change
		@attrib name=submit params=name
	**/
	function dsubmit($args = array())
	{
		return parent::submit($args);
	}

	/** Outputs autocomplete options matching customer name search string $typed_text in bsnAutosuggest format json
		@attrib name=get_category_options
		@param id required type=oid
			Company oid whose customers are sought
		@param typed_text optional type=string
	**/
	//TODO: searches to use this
	public static function get_customer_options($args)
	{
		$choices = array("results" => array());
		$typed_text = $args["typed_text"];
		$seller_o = new object($args["id"]);
		$limit = $this_o->prop("autocomplete_options_limit") ? (int) $this_o->prop("autocomplete_options_limit") : 20;
		$list = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"seller" => $seller_o->prop("owner")->id(),
			"buyer.name" => "{$typed_text}%",
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
				$results[] = array("id" => $o->id(), "value" => $value, "info" => $info);//FIXME charsets
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

	/**
		@attrib name=commix api=1 params=name all_args=1
	**/
	public function commix($arr)
	{
		foreach($arr["sel"] as $key => $bill_id)
		{
			unset($arr["sel"][$key]);
			$bill = obj($bill_id);
			$ret = $bill->add_bills($arr["sel"]);
			if($this->can("view" , $ret))
			{
				return html::get_change_url($ret, array("return_url" => $arr["post_ru"]));
			}
			else
			{
				return $arr["post_ru"];
			}
		}
	}

	/** Adds customer. c or o must be defined.
		@attrib name=add_customer
		@param id required type=oid
			Company oid where customer added
		@param t required type=int
			Relation type (seller or buyer). One of crm_company_obj::CUSTOMER_TYPE_... constant values
		@param c optional type=clid
			Customer class id (person or organization) to create
		@param o optional type=oid
			Customer object to be added (person or organization)
		@param s optional type=oid
			Customer category
		@param return_url required type=string
	**/
	function add_customer($arr)
	{
		$r = $arr["return_url"];
		$type = (int) $arr["t"];

		// load company where customer is added
		try
		{
			$this_o = obj($arr["id"], array(), CL_CRM_COMPANY);
		}
		catch (Exception $e)
		{
			$this->show_error_text(sprintf(t("Viga p&auml;ringus! Lubamatu organisatsiooni id '%s'"), $arr["id"]));
			return $r;
		}

		if (crm_company_obj::CUSTOMER_TYPE_BUYER !== $type and crm_company_obj::CUSTOMER_TYPE_SELLER !== $type)
		{
			$this->show_error_text(t("Loodava kliendisuhte t&uuml;&uuml;p m&auml;&auml;ramata"));
			return $r;
		}

		if (!empty($arr["o"]))
		{
			$customer = new object($arr["o"]);
			if (!$customer->is_saved() or !$customer->is_a(CL_CRM_COMPANY) and !$customer->is_a(crm_person_obj::CLID))
			{
				$this->show_error_text(sprintf(t("Antud klient (id '%s') ei ole lisatav"), $customer->id()));
				return $r;
			}
		}
		elseif (!empty($arr["c"]))
		{
			$customer = obj(null, array(), $arr["c"]);
			$customer->set_parent($this_o->id());
			if (!$customer->is_a(CL_CRM_COMPANY) and !$customer->is_a(crm_person_obj::CLID))
			{
				$this->show_error_text(sprintf(t("Antud objekt ('%s') pole lisatav kliendina"), $customer->class_id()));
				return $r;
			}
			$customer->save();
			$params = array();
			$params["return_url"] = $arr["return_url"];
			$params["save_autoreturn"] = "1";

			$r = html::get_change_url($customer, $params);
		}

		try
		{
			if (!($customer_relation = $this_o->get_customer_relation($type, $customer)))
			{
				$customer_relation = $this_o->create_customer_relation($type, $customer);
			}

			// set category if specified
			if (!empty($arr["s"]))
			{
				try
				{
					$category = obj($arr["s"], array(), CL_CRM_CATEGORY);
					$customer_relation->add_category($category);
				}
				catch (Exception $e)
				{
					$this->show_error_text(sprintf(t("Kategooria m&auml;&auml;ramine eba&otilde;nnestus! Lubamatu objekti id '%s'"), $args["s"]));
					return $r;
				}
			}
		}
		catch (Exception $e)
		{
			trigger_error("Caught exception " . get_class($e) . " while trying to add customer ".$customer->id().". Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": '" . $e->getMessage() . "' <br> Backtrace:<br>" . dbg::process_backtrace($e->getTrace(), -1, true), E_USER_WARNING);
			$this->show_error_text(t("Kliendi lisamine eba&otilde;nnestus."));
		}

		return $r;
	}

	function submit($args = array())
	{
		if(!empty($args["sbt_data_add_buyer"]) or !empty($args["sbt_data_add_seller"]))
		{ // process popup search customer add request
			$args["s"] = isset($args[self::REQVAR_CATEGORY]) ? $args[self::REQVAR_CATEGORY] : "";
			$args["return_url"] = isset($args["post_ru"]) ? $args["post_ru"] : "";

			if (!empty($args["sbt_data_add_buyer"]))
			{
				$args["o"] = $args["sbt_data_add_buyer"];
				$args["t"] = crm_company_obj::CUSTOMER_TYPE_BUYER;
			}
			elseif (!empty($args["sbt_data_add_seller"]))
			{
				$args["o"] = $args["sbt_data_add_seller"];
				$args["t"] = crm_company_obj::CUSTOMER_TYPE_SELLER;
			}

			$r = $this->add_customer($args);
		}
		elseif (!empty($args["sbt_data_add_employee"]))
		{
			$r = isset($args["post_ru"]) ? $args["post_ru"] : "";

			// load company where customer is added
			try
			{
				$this_o = obj($args["id"], array(), CL_CRM_COMPANY);
			}
			catch (Exception $e)
			{
				$this->show_error_text(sprintf(t("Viga p&auml;ringus! Lubamatu organisatsiooni id '%s'"), $args["id"]));
				return $r;
			}

			// get profession
			if (empty($args["cat"]))
			{
				$this->show_error_text(t("Ametinimetus valimata. Isikut ei lisatud t&ouml;&ouml;tajaks."));
				return $r;
			}

			try
			{
				$profession = obj($args["cat"], array(), CL_CRM_PROFESSION);
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Ametinimetuse leidmisel tekkis viga. Isikut ei lisatud t&ouml;&ouml;tajaks."));
				return $r;
			}

			// found profession. load person, create work relation
			try
			{
				$person = obj($args["sbt_data_add_employee"], array(), crm_person_obj::CLID);
				$this_o->add_employee($profession, $person);
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Valitud isikut ei saanud lisada t&ouml;&ouml;tajaks."));
				return $r;
			}
		}
		else
		{ // normal submit
			$r = parent::submit($args);
		}

		return $r;
	}
}
