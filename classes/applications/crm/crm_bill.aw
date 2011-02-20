<?php
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_CRM_BILL, on_delete_bill)
@tableinfo aw_crm_bill index=aw_oid master_index=brother_of master_table=objects
@classinfo relationmgr=yes no_status=1 prop_cb=1 confirm_save_data=1
@default table=objects


@property customer type=hidden table=aw_crm_bill field=aw_customer

//deprecated
@property bill_mail_to type=hidden field=meta method=serialize

@default group=general_data

	@property bill_tb type=toolbar store=no no_caption=1
	@caption Arve toolbar

	@property important_comment type=text store=no no_caption=1
	@caption T&auml;htis kommentaar

	@layout main_split type=vbox
		@layout top_split parent=main_split type=hbox

			@layout left_split type=vbox parent=top_split

				@layout top_left type=vbox parent=left_split closeable=1 area_caption=&Uuml;ldandmed

				@layout bottom_left type=vbox parent=left_split closeable=1 area_caption=Lisainfo

			@layout right_split type=vbox parent=top_split

				@layout top_right type=vbox parent=right_split closeable=1 area_caption=Kliendi&nbsp;andmed

				@layout bottom_right type=hbox parent=right_split closeable=1 area_caption=Ladu width=50%:50%

					@layout bottom_right_left type=vbox parent=bottom_right

					@layout bottom_right_right type=vbox parent=bottom_right


	// top left lyt
	@property name type=text table=objects field=name parent=top_left no_caption=1

	@property bill_no type=textbox table=aw_crm_bill field=aw_bill_no parent=top_left
	@caption Number

	@property impl type=popup_search style=relpicker table=aw_crm_bill field=aw_impl parent=top_left reltype=RELTYPE_IMPL
	@caption Arve esitaja

	@property assembler type=select table=aw_crm_bill field=aw_assembler parent=top_left
	@caption Koostaja

	@property bill_date type=datepicker time=0 table=aw_crm_bill field=aw_date parent=top_left
	@caption Kuup&auml;ev

	@property bill_accounting_date type=datepicker time=0 table=aw_crm_bill field=aw_bill_accounting_date parent=top_left
	@caption Raamatupidamise kuup&auml;ev

	@property bill_due_date_days type=textbox table=aw_crm_bill field=aw_due_date_days size=5 parent=top_left
	@caption Makset&auml;htaeg (p&auml;evi)

	@property bill_due_date type=datepicker time=0 table=aw_crm_bill field=aw_due_date parent=top_left
	@caption Tasumise kuup&auml;ev

	@property bill_recieved type=datepicker time=0 table=aw_crm_bill field=aw_recieved default=-1 parent=top_left
	@caption Laekumiskuup&auml;ev

	@property payment_mode type=select table=aw_crm_bill field=aw_payment_mode parent=top_left
	@caption Makseviis

	@property state type=select table=aw_crm_bill field=aw_state parent=top_left
	@caption Staatus

	@property sum type=text table=aw_crm_bill field=aw_sum size=5  parent=top_left
	@caption Summa

	@property currency type=relpicker table=aw_crm_bill field=aw_currency parent=top_left reltype=RELTYPE_CURRENCY
	@caption Valuuta

	@property partial_recieved type=text field=meta method=serialize parent=top_left
	@caption Osaline laekumine



	// bottom left lyt
	@property disc type=textbox table=aw_crm_bill field=aw_discount size=5  parent=bottom_left
	@caption Allahindlus (%)

	@property overdue_charge type=textbox table=aw_crm_bill field=aw_overdue_charge size=5 parent=bottom_left
	@caption Viivis (%)

	@property language type=relpicker automatic=1 field=meta method=serialize reltype=RELTYPE_LANGUAGE parent=bottom_left
	@caption Keel

	@property on_demand type=checkbox table=aw_crm_bill field=aw_on_demand parent=bottom_left
	@caption Sissen&otilde;udmisel

	@property mail_notify type=checkbox ch_value=1 store=no parent=bottom_left
	@caption Teade laekumisest e-postile

	@property approved type=checkbox table=aw_crm_bill ch_value=1 field=aw_approved parent=bottom_left
	@caption Kinnitatud

	@property bill_trans_date type=datepicker time=0 table=aw_crm_bill field=aw_trans_date default=-1 parent=bottom_left
	@caption Kandekuup&auml;ev

	@property signers type=crm_participant_search reltype=RELTYPE_SIGNER multiple=1 table=objects field=meta method=serialize style=relpicker parent=bottom_left
	@caption Allkirjastajad

	@property signature_type type=select table=objects field=meta method=serialize parent=bottom_left
	@caption Allkirja t&uuml;&uuml;p

	// top right lyt
	@property customer_name type=textbox table=aw_crm_bill field=aw_customer_name parent=top_right
	@caption Kliendi nimi

	@property customer_code type=textbox table=aw_crm_bill field=aw_customer_code parent=top_right
	@caption Kliendikood

	@property ctp_text type=textbox table=objects field=meta method=serialize parent=top_right
	@caption Kontaktisik vabatekstina

	@property customer_address type=textbox table=aw_crm_bill field=aw_customer_address parent=top_right
	@caption Kliendi aadress

	@property customer_add_meta_cb type=callback callback=customer_add_meta_cb store=no parent=top_right


	// bottom right lyt

	@property warehouse_info type=text table=aw_crm_bill store=no parent=bottom_right_left
	@caption Info

	@property warehouse type=relpicker table=aw_crm_bill field=aw_warehouse reltype=RELTYPE_WAREHOUSE parent=bottom_right_left
	@caption Ladu

	@property price_list type=relpicker table=aw_crm_bill field=aw_price_list reltype=RELTYPE_PRICE_LIST parent=bottom_right_left
	@caption Hinnakiri

	@property transfer_method table=aw_crm_bill type=relpicker field=aw_transfer_method reltype=RELTYPE_TRANSFER_METHOD parent=bottom_right_left
	@caption L&auml;hetusviis

	@property transfer_condition table=aw_crm_bill type=relpicker field=aw_transfer_condition reltype=RELTYPE_TRANSFER_CONDITION parent=bottom_right_left
	@caption L&auml;hetustingimus

	@property selling_order type=relpicker table=aw_crm_bill field=aw_selling_order reltype=RELTYPE_SELLING_ORDER parent=bottom_right_left
	@caption M&uuml;&uuml;gitellimus

	@property transfer_address type=relpicker table=aw_crm_bill reltype=RELTYPE_ADDRESS field=aw_transfer_address parent=bottom_right_left
	@caption L&auml;hetusaadress

	@property dn_confirm_tbl type=table no_caption=1 parent=bottom_right_right




@default group=other_data
	@property show_oe_add type=checkbox ch_value=1 field=meta method=serialize
	@caption N&auml;ita arve lisas muid kulusid

	@property rows_different_pages type=text field=meta method=serialize
	@caption Read erinevatel lehek&uuml;lgedel

	@property comment type=textarea rows=15 cols=50 table=objects field=comment
	@caption Kommentaar lisale

	@property time_spent_desc type=textbox table=aw_crm_bill field=aw_time_spent_desc
	@caption Kulunud aeg tekstina

	@property reminder_text type=textbox table=aw_crm_bill field=aw_reminder_text
	@caption Arve meeldetuletuse juurde minev tekst

	@property monthly_bill type=checkbox ch_value=1 table=aw_crm_bill field=aw_monthly_bill
	@caption Kuuarve

	@property udef1 type=checkbox ch_value=1 field=meta method=serialize
	@caption Kasutajadefineeritud muutuja 1

	@property udef2 type=checkbox ch_value=1 field=meta method=serialize
	@caption Kasutajadefineeritud muutuja 2

	@property udef3 type=checkbox ch_value=1 field=meta method=serialize
	@caption Kasutajadefineeritud muutuja 3

	@property udef4 type=checkbox ch_value=1 field=meta method=serialize
	@caption Kasutajadefineeritud muutuja 4

	@property udef5 type=checkbox ch_value=1 field=meta method=serialize
	@caption Kasutajadefineeritud muutuja 5

	@property is_overdue_bill type=checkbox ch_value=1  table=aw_crm_bill field=aw_is_overdue_bill
	@caption Viivisarve

	@property project type=relpicker store=connect reltype=RELTYPE_PROJECT multiple=1
	@caption Projekt

	@property bill_text type=textarea field=meta method=serialize
	@caption Arve tekst

	@property comments type=text store=no
	@caption Kommentaarid

	@property comments_add type=textarea store=no
	@caption Lisa

	@property mail_receiver type=relpicker store=connect multiple=1 reltype=RELTYPE_RECEIVER
	@caption Arve e-kirja saaja



@default group=rows
	@property rows_toolbar type=toolbar store=no no_caption=1 editonly=1
	@caption Arve ridade tegevused

	@layout bill_rows_container type=hbox
	@layout bill_writeoff_rows_container type=hbox
		@property bill_rows type=table store=no no_caption=1 editonly=1 parent=bill_rows_container
		@caption Arve read

		@property writeoffs type=table store=no no_caption=1 editonly=1 parent=bill_writeoff_rows_container
		@caption Mahakantud read




@default group=sent_mails
	@property mail_table type=table no_caption=1 no_caption=1


@default group=send_mail
// properties 'sendmail_attachments' and 'sendmail_recipients' in send_mail group are
// temporarily saved in active session and cleared, when send_bill action is requested by user
	@property sendmail_toolbar type=toolbar store=no no_caption=1
	@layout sendmail_settings type=hbox closeable=1 area_caption=Kirja&nbsp;seaded width=50%:50%
	@layout sendmail_sender type=vbox closeable=0 area_caption=Saatja parent=sendmail_settings
		@property bill_mail_from type=textbox store=no parent=sendmail_sender
		@caption E-posti aadress

		@property bill_mail_from_name type=textbox store=no parent=sendmail_sender
		@caption Nimi

	@layout sendmail_attachments type=vbox closeable=0 area_caption=Lisatavad&nbsp;dokumendid parent=sendmail_settings
		@property sendmail_attachments type=chooser multiple=1 store=no parent=sendmail_attachments orient=vertical no_caption=1

	@layout sendmail_recipients type=vbox closeable=1 area_caption=Kirja&nbsp;saajad
		@property sendmail_recipients type=table store=no parent=sendmail_recipients no_caption=1

		@property bill_rec_name type=textbox store=no parent=sendmail_recipients
		@comment Otsi olemasolevate isikute hulgast v&otilde;i sisesta kehtiv suvaline e-posti aadress
		@caption Lisa arve saaja

	@layout sendmail_content type=hbox closeable=1 area_caption=Kirja&nbsp;sisu width=50%:50%
		@layout sendmail_content_l type=vbox parent=sendmail_content closeable=0 area_caption=Muutmine
		@layout sendmail_content_r type=vbox parent=sendmail_content closeable=0 area_caption=Eelvaade&nbsp;(kliki&nbsp;tekstil&nbsp;et&nbsp;uuendada)

	@property bill_mail_subj type=textbox parent=sendmail_content_l captionside=top table=objects field=meta method=serialize
	@caption Pealkiri

	@property bill_mail_ct type=textarea rows=20 cols=53 parent=sendmail_content_l captionside=top table=objects field=meta method=serialize
	@caption Sisu

	@property bill_mail_legend type=text store=no parent=sendmail_content_l captionside=top
	@comment E-kirja sisus ja pealkirjas kasutatavad muutujad. Asendatakse saatmisel vastavate tegelike v&auml;&auml;rtustega
	@caption Kasutatavad muutujad

	@property sendmail_subject_view type=text parent=sendmail_content_r captionside=top
	@caption Pealkiri

	@property sendmail_body_view type=text store=no parent=sendmail_content_r captionside=top
	@caption Sisu




@default group=delivery_notes
	@property dn_tb type=toolbar store=no no_caption=1
	@property dn_tbl type=table store=no no_caption=1


@default group=preview
	@property preview type=text store=no no_caption=1


@default group=preview_add
	@property preview_add type=text store=no no_caption=1


@default group=preview_w_rows
	@property preview_w_rows type=text store=no no_caption=1


@default group=tasks
	@property billt_tb type=toolbar store=no no_caption=1
	@layout bill_task_list_l type=vbox
		@property bill_task_list type=table store=no no_caption=1 parent=bill_task_list_l



//=========== GROUP DEFINITIONS ==============

@groupinfo general_data caption="P&otilde;hiandmed" parent=general
@groupinfo other_data caption="Muud andmed" parent=general
@groupinfo rows caption="Read"
@groupinfo mails caption="Kirjad"
	@groupinfo send_mail caption="Arve saatmine" parent=mails confirm_save_data=0
	@groupinfo sent_mails caption="Saadetud kirjad" parent=mails
@groupinfo delivery_notes caption="Saatelehed"
@groupinfo tasks caption="Toimetused" submit=no
@groupinfo preview caption="Eelvaade"
@groupinfo preview_add caption="Arve Lisa"
@groupinfo preview_w_rows caption="Eelvaade ridadega"



//=========== RELTYPE DEFINITIONS ==============

@reltype TASK value=1 clid=CL_TASK,CL_BUG
@caption &Uuml;lesanne

@reltype CUST value=2 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Klient

@reltype IMPL value=3 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Teostaja

@reltype LANGUAGE value=4 clid=CL_LANGUAGE
@caption Keel

@reltype ROW value=5 clid=CL_CRM_BILL_ROW
@caption Rida

@reltype PROD value=6 clid=CL_SHOP_PRODUCT
@caption Toode

@reltype SIGNER value=7 clid=CL_CRM_PERSON
@caption Allkirjastaja

@reltype PAYMENT value=8 clid=CL_CRM_BILL_PAYMENT
@caption Laekumine

@reltype WAREHOUSE value=9 clid=CL_SHOP_WAREHOUSE
@caption Ladu

@reltype PRICE_LIST value=10 clid=CL_SHOP_PRICE_LIST
@caption Hinnakiri

@reltype TRANSFER_METHOD value=11 clid=CL_CRM_TRANSFER_METHOD
@caption L&auml;hetusviis

@reltype TRANSFER_CONDITION value=12 clid=CL_CRM_TRANSFER_CONDITION
@caption L&auml;hetustingimus

@reltype SELLING_ORDER value=13 clid=CL_SHOP_WAREHOUSE_SELLING_ORDER
@caption M&uuml;&uuml;gitellimus

@reltype DELIVERY_NOTE value=14 clid=CL_SHOP_DELIVERY_NOTE
@caption Saateleht

@reltype ADDRESS value=15 clid=CL_CRM_ADDRESS
@caption L&auml;hetusaadress

@reltype PROJECT value=17 clid=CL_PROJECT
@caption Projekt

@reltype CURRENCY value=18 clid=CL_CURRENCY
@caption Valuuta

@reltype RECEIVER value=19 clid=CL_CRM_PERSON
@caption Arve saaja
*/

class crm_bill extends class_base
{
	private $tot_amt = 0;
	private $customer_object;

	function crm_bill()
	{
		$this->init(array(
			"tpldir" => "crm/crm_bill",
			"clid" => CL_CRM_BILL
		));

		$this->states = crm_bill_obj::status_names();

		if(!empty($_GET["project"]) && $this->can("view" , $_GET["project"]))
		{
			$this->project_object = obj($_GET["project"]);
			if($this->can("view" , $this->project_object->get_orderer()))
			{
				$this->customer_object = obj($this->project_object->get_orderer());
			}
		}
	}

	function callback_post_save($arr)
	{
		if ("send_mail" === $this->use_group)
		{
			$arr["obj_inst"]->reset_pdf_files_cache(true);
		}

		if(isset($arr["request"]["project"]) && $this->can("view" , $arr["request"]["project"]))
		{
			$arr["obj_inst"]->set_project($arr["request"]["project"]);
		}

		if(isset($arr["request"]["add_bug"]) and $this->can("view" , $arr["request"]["add_bug"]))
		{
			$arr["obj_inst"]->add_rows(array("objects" => array($arr["request"]["add_bug"])));
		}

		foreach($arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_DELIVERY_NOTE",
		)) as $c)
		{
			$c->to()->update_dn(array(
				"from_warehouse" => $arr["obj_inst"]->prop("warehouse"),
				"customer" => $arr["obj_inst"]->prop("customer"),
				"impl" => $arr["obj_inst"]->prop("impl"),
				"currency" => $arr["obj_inst"]->prop("currency"),
			));
		}
	}

	function callback_mod_reforb(&$arr, $request)
	{
		$arr["post_ru"] = post_ru();
		$arr["add_bug"] = "";
		$arr["reconcile_price"] = -1;
		$arr["new_payment"] = "";
		$arr["add_dn"] = 0;
		if(!empty($request["project"]))
		{
			$arr["project"] = $request["project"];
		}
		$arr["dno"] = "";
	}

	function get_bill_cust_data_object($bill)
	{
		if(!is_object($bill))
		{
			return "";
		}
		if($this->cust_data_object)
		{
			return $this->cust_data_object;
		}
		$cust_rel_list = new object_list(array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"buyer" => $bill->prop("customer"),
			"seller" => $bill->prop("impl")
		));
		$this->cust_data_object = reset($cust_rel_list->arr());
		return $this->cust_data_object;
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "assembler" :
				if ($arr["obj_inst"]->is_saved())
				{
					$maker = $arr["obj_inst"]->get_the_person_who_made_this_fucking_thing();
					$prop["options"] = array($maker->id() => $maker->name());
				}
				$ps = new popup_search();
				$ps->set_class_id(array(CL_CRM_PERSON));
				$ps->set_id($arr["obj_inst"]->id());
				$ps->set_reload_layout("top_left");
				$ps->set_property("assembler");
				$prop["post_append_text"] = $ps->get_search_button();
				break;

			case "signature_type":
				$pop["options"] = array(0,t("Tavaline"), t("Digitaalne"));
				break;

			case "overdue_charge":
				if(empty($pop["value"]) && !$arr["new"])
				{
					$prop["value"] = $arr["obj_inst"]->get_overdue_charge();
				}
				$prop["post_append_text"] = " ".html::href(array(
					"url" => $this->mk_my_orb("make_overdue_bill", array("id" => $arr["obj_inst"]->id(), "ru" => get_ru())),
					"caption" => t("Loo viivisarve"),
				));
				break;

			case "currency":
				if ($arr["obj_inst"]->is_saved())
				{
					$prop["value"] = $arr["obj_inst"]->get_bill_currency_id();
				}

				if(!empty($prop["value"]) and empty($prop["options"][$prop["value"]]))
				{
					$prop["options"][$prop["value"]] = $arr["obj_inst"]->get_bill_currency_name();
				}
				break;

			case "important_comment":
				if($this->can("view" , $arr["obj_inst"]->meta("important_comment")))
				{
					$ic = obj($arr["obj_inst"]->meta("important_comment"));
					$prop["value"] = "<font color=red size=+1><b>".$ic->comment()."</b></font>";
				}
				if(isset($this->error))
				{
					$prop["value"].= html::linebreak() . "<font color=red size=+1>".html::bold($this->error)."</font>";
				}
				break;

			case "comments":
				$prop["value"] = $arr["obj_inst"]->get_comments_text();
				break;

			case "partial_recieved":
				if(!$arr["new"])
				{
					$sum = $arr["obj_inst"]->get_bill_recieved_money();
				}
				else
				{
					$sum = 0;
				}

				if(!$arr["new"])
				{
					$prop["value"] = number_format($sum, 2);
					$prop["value"] .= " ".$arr["obj_inst"]->get_bill_currency_name();
					$payment_id = $arr["obj_inst"]->get_payment_id();
					if($payment_id)
					{
						$prop["value"] = html::href(array(
							"url" => $this->mk_my_orb("change", array("id" => $payment_id, "return_url" => get_ru()), CL_CRM_BILL_PAYMENT),
							"caption" => $prop["value"],
						));
					}
				}

				$url = $this->mk_my_orb("do_search", array(
					"pn" => "new_payment",
					"clid" => CL_CRM_BILL_PAYMENT,
				), "popup_search", false, true);

				if(!($sum >= $arr["obj_inst"]->get_bill_sum()))
				{
					$prop["value"].= " ".html::href(array(
						"url" => $this->mk_my_orb("add_payment", array("id" => $arr["obj_inst"]->id(), "ru" => get_ru())),
						"caption" => t("Lisa laekumine!"),
					)).
					" ".html::href(array(
						"url" => "javascript:aw_popup_scroll('".$url."','Otsing', 550, 500)",
						"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/search.gif' border=0>",
						"title" => t("Otsi")
					)) . html::linebreak();
				}

				if($arr["obj_inst"]->id() > 1)
				{
					foreach($arr["obj_inst"]->get_bill_payments_data() as $dat)
					{
						$prop["value"].= html::linebreak().date("d.m.Y" , $dat["date"])." ".$dat["sum"]." ".$dat["currency"];
					}
				}
				break;

			case "payment_mode":
				$prop["options"] = array("" , t("&Uuml;lekandega") , t("Sularahas"));
				break;

			case "bill_no":
				if (empty($prop["value"]))
				{
					$time = $arr["obj_inst"]->prop("bill_date");
					if(!$time) $time = time();
					$i = new crm_number_series();
					$prop["value"] = $i->find_series_and_get_next(CL_CRM_BILL, 0 , $time);
					if (!$arr["new"] && is_oid($arr["obj_inst"]->id()))
					{
						$arr["obj_inst"]->set_prop("bill_no" , $prop["value"]);
					}
				}
				break;

			case "impl":
				if (!$arr["new"] && is_oid($arr["obj_inst"]->id()))
				{
					$ol = new object_list($arr["obj_inst"]->connections_from(array("type" => "RELTYPE_IMPL")));
					$prop["options"] = $ol->names();
				}
				$u = get_instance(CL_USER);
				$co = obj($u->get_current_company());
				$prop["options"][$co->id()] = $co->name();
				asort($prop["options"]);
				$prop["options"] = array("" => t("--vali--")) + $prop["options"];
				break;

			case "preview_w_rows":
				$arr["all_rows"] = 1;
				$this->_get_preview($arr);
				break;

			case "state":
				$prop["options"] = crm_bill_obj::status_names();
				break;

			case "customer_name":
				$ps = new popup_search();
				$ps->set_class_id(array(CL_CRM_PERSON, CL_CRM_COMPANY));
				$ps->set_id($arr["obj_inst"]->id());
				$ps->set_reload_layout("top_right");
				$ps->set_property("customer");
				$search_button = $ps->get_search_button();
				$confirm = t("Laadida kliendi andmed uuesti? (Sisestatud aadressi ja t&auml;htaja muudatused kustutatakse)");
				if($arr["obj_inst"]->prop("customer"))
				{
					$edit_button = " " . html::href(array(
						"url" => html::get_change_url($arr["obj_inst"]->prop("customer"), array("return_url" => get_ru())),
						"caption" => html::img(array("url" => aw_ini_get("baseurl") . "/automatweb/images/icons/edit.gif"))
					));
					$reload_button = " " . html::href(array(
						"url" => "javascript:;",
						"onclick" => "if(!confirm(\"{$confirm}\")) { return false; }; submit_changeform(\"reload_customer_data\");",
						"caption" => html::img(array("url" => aw_ini_get("baseurl") . "/automatweb/images/icons/refresh.gif"))
					));
				}
				else
				{
					$prop["caption"] = t("Klient");
					$prop["disabled"] = "1";
					$reload_button = $edit_button = "";
				}

				$prop["post_append_text"] = " {$search_button}{$edit_button}{$reload_button}";
				break;

			case "ctp_text":
			case "customer_code":
			case "customer_address":
				if(!$arr["obj_inst"]->prop("customer"))
				{
					return PROP_IGNORE;
				}
				break;

			case "customer":
				if($this->customer_object)
				{
					$prop["value"] = $this->customer_object->id();
				}

				$retval = PROP_IGNORE;
				break;

			case "bill_text":
				$prop["value"] = $arr["obj_inst"]->get_bill_text();
				break;

			case "sum":
				if(!$arr["obj_inst"]->is_saved())
				{
					return PROP_IGNORE;
				}

				$agreement_prices = $arr["obj_inst"]->meta("agreement_price");
				if(is_array($agreement_prices) && !empty($agreement_prices[0]["price"]) && strlen($agreement_prices[0]["name"]) > 0)
				{
					$sum = 0;
					foreach($agreement_prices as $agreement_price)
					{
						$sum+= $agreement_price["price"];
					}
					$prop["value"] = $sum;
				}

				$tax_sum = $add_tax = 0;
				if(($SUM_WT = $arr["obj_inst"]->get_bill_sum()) > $prop["value"])
				{
					$tax_sum = $SUM_WT - $prop["value"];
					$add_tax = 1;
				}

				$val = array();
				$val[] = number_format($prop["value"], 2)." ".$arr["obj_inst"]->get_bill_currency_name();
				if(!empty($add_tax))
				{
					$val[] = t("Summa").": ".$prop["value"]." ".$arr["obj_inst"]->get_bill_currency_name();
					$val[] = t("KM").": ".number_format($tax_sum, 2)." ".$arr["obj_inst"]->get_bill_currency_name();
					$val[] = t("Kokku").": ".number_format($SUM_WT, 2)." ".$arr["obj_inst"]->get_bill_currency_name();
				}

				if($writeoffs_sum = $arr["obj_inst"]->get_writeoffs_sum())
				{
					$val[] = t("Mahakantud ridade summa:")." ".number_format($writeoffs_sum, 2)." ".$arr["obj_inst"]->get_bill_currency_name();
				}

				$prop["value"] = join (html::linebreak(), $val);
				break;

			case "rows_different_pages":
				$rows_in_page = $arr["obj_inst"]->meta("rows_in_page");
				$x = 0;
				$val = "";
				$count = 0;
				if(is_array($rows_in_page))
				{
					foreach($rows_in_page as $key => $row)
					{
						if($row)
						{
							$val .=html::textbox(array(
								"name" => "rows_in_page[".$key."]",
								"value" => $row,
								"size" => 3
							));
							$count++;
						}
					}
				}

				while(3 > $x)
				{
					$val .=html::textbox(array(
						"name" => "rows_in_page[".($x+$count)."]",
						"size" => 3
					));
					$x++;
				}
				$prop["value"] = $val;
				break;

			case "bill_trans_date":
				if($prop["value"] == -1) $prop["value"] = time();
				break;

			case "warehouse":
				$ids = array();
				foreach(array("impl", "customer") as $var)
				{
					if($co = $arr["obj_inst"]->prop($var))
					{
						$conn = obj($co)->connections_to(array(
							"from.class_id" => CL_SHOP_WAREHOUSE_CONFIG,
							"type" => "RELTYPE_MANAGER_CO",
						));
						foreach($conn as $c)
						{
							$ids[] = $c->prop("from");
						}
					}
				}
				$ol = new object_list(array(
					"class_id" => CL_SHOP_WAREHOUSE,
					"conf" => $ids,
				));
				$val = array();
				if(isset($prop["value"]) && $this->can("view", $prop["value"]) && !in_array($prop["value"], $ids))
				{
					$val = array($prop["value"] => obj($prop["value"])->name());
				}
				$prop["options"] = array(
					0 => t("--vali--"),
				) + $val + $ol->names();
				break;

			case "warehouse_info":
				$cos = $arr["obj_inst"]->prop("warehouse.conf.manager_cos");
				if(!is_array($cos))
				{
					$cos = array($cos);
				}
				if(in_array($arr["obj_inst"]->prop("customer"), $cos))
				{
					$arr["prop"]["value"] = "<span style='color:red;'>".t("Tegu on ostuarvega")."</span>";
				}
				else
				{
					return PROP_IGNORE;
				}
				break;
		}
		return $retval;
	}

	function _get_rows_toolbar(&$arr)
	{
		$r = PROP_OK;
		$tb = $arr["prop"]["vcl_inst"];
		$this_o = $arr["obj_inst"];

		$tb->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Uus"),
			"img" => "new.gif"
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"url" => "javascript:void(0);",
			"text" => t("Lisa t&uuml;hi rida"),
			"onclick" => "crm_bill_add_row();",
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"url" => "javascript:void(0);",
			"onclick" => "win = window.open('".$this->mk_my_orb("bug_search", array("is_popup" => "1", "customer" => $this_o->get_bill_customer()), "crm_bill")."','bug_search','width=720,height=600,statusbar=yes, scrollbars=yes ');",
			"text" => t("Lisa arendus&uuml;lesanne")
		));

		$tb->add_save_button();
		$this->add_sendmail_menu($arr);
		$this->add_print_menu($arr);

		if(!$this->crm_settings || !$this->crm_settings->prop("bill_hide_cr"))
		{
			$tb->add_button(array(
				"name" => "reconcile",
				"tooltip" => t("Koonda blokiks"),
				"action" => "reconcile_rows",
				// get all checked rows and check their prices, if they are different, ask the user for a new price
				"onClick" => "nfound=0;curp=-1;form=document.changeform;len = form.elements.length;for(i = 0; i < len; i++){if (form.elements[i].name.indexOf('sel_rows') != -1 && form.elements[i].checked)	{nfound++; neln = 'rows_'+form.elements[i].value+'__price_';nel = document.getElementById(neln); if (nfound == 1) { curp = nel.value; } else if(curp != nel.value) {price_diff = 1;}}}; if (price_diff) {v=prompt('Valitud ridade hinnad on erinevad, sisesta palun koondatud rea hind'); if (v) { document.changeform.reconcile_price.value = v;return true; } else {return false;} }"
			));
		}

		$tb->add_button(array(
			"name" => "delete",
			"img" => "delete.gif",
			"tooltip" => t("Kustuta read"),
			"confirm" => t("Oled kindel et soovid read kustutada?"),
			"action" => "delete_rows"
		));

		$tb->add_button(array(
			"name" => "writeoff",
			"img" => "class_244.gif",
			"tooltip" => t("Kanna arve rida maha/Pane arve rida tagasi arvele"),
			"confirm" => t("Oled kindel et soovid valitud read maha kanda/tagasi arvele panna?"),
			"action" => "writeoff_rows"
		));

		$tb->add_button(array(
			"name" => "reorder",
			"img" => "rte_num_list.gif",
			"tooltip" => t("J&auml;rjesta read uuesti"),
			"confirm" => t("Oled kindel et soovid read uuesti j&auml;rjestada"),
			"action" => "reorder_rows"
		));

		$tb->add_button(array(
			"name" => "form_new_bill",
			"img" => "cut.gif",
			"tooltip" => t("Loo valitud ridadest uus arve"),
			"confirm" => t("Oled kindel et kanda valitud read uuele arvele?"),
			"action" => "form_new_bill"
		));

		$tb->add_menu_button(array(
			"name" => "bill_dno",
			"img" => "copy.gif",
			"tooltip" => t("Kanna arve read saatelehele"),
		));
		foreach($arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_DELIVERY_NOTE",
		)) as $c)
		{
			$tb->add_menu_item(array(
				"parent" => "bill_dno",
				"url" => "javascript: var cf = document.forms.changeform; cf.action.value = 'move_rows_to_dn'; cf.dno.value='".$c->prop("to")."'; cf.submit()",
				"text" => $c->to()->name(),
			));
		}
		$tb->add_menu_item(array(
			"parent" => "bill_dno",
			"url" => "javascript: var cf = document.forms.changeform; cf.action.value = 'move_rows_to_dn'; cf.dno.value='new'; cf.submit()",
			"text" => t("Uus saateleht"),
		));

		return $r;
	}

	function _get_bill_mail_from(&$arr)
	{
		$r = PROP_OK;
		$tmp = aw_global_get("crm_bill_sendmail_sender_email_tmp");
		$arr["prop"]["value"] = $tmp ? $tmp : $arr["obj_inst"]->get_mail_from();
		return $r;
	}

	function _get_bill_mail_from_name(&$arr)
	{
		$r = PROP_OK;
		$tmp = aw_global_get("crm_bill_sendmail_sender_name_tmp");
		$arr["prop"]["value"] = $tmp ? $tmp : $arr["obj_inst"]->get_mail_from_name();
		return $r;
	}

	function _set_bill_mail_from(&$arr)
	{
		if (isset($arr["request"]["bill_mail_from"]))
		{
			aw_session_set("crm_bill_sendmail_sender_email_tmp", $arr["request"]["bill_mail_from"]);
		}
		return PROP_IGNORE;
	}

	function _set_bill_mail_from_name(&$arr)
	{
		if (isset($arr["request"]["bill_mail_from_name"]))
		{
			aw_session_set("crm_bill_sendmail_sender_name_tmp", $arr["request"]["bill_mail_from_name"]);
		}
		return PROP_IGNORE;
	}

	function _get_bill_mail_legend(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["value"] = nl2br(crm_bill_obj::get_mail_parse_legend());
		return $r;
	}

	function _get_sendmail_toolbar(&$arr)
	{
		$r = PROP_OK;
		$t = $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "send",
			"img" => "mail_send.gif",
			"tooltip" => t("Saada arve"),
			"confirm" => t("Oled kindel et soovid arve saata?"),
			"action" => "send_bill"
		));

		$t->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta muudatused ajutiselt"),
			"action" => "submit"
		));

		return $r;
	}

	function _set_sendmail_recipients($arr)
	{
		if (isset($arr["request"]["recipient"]))
		{
			$recipients_tmp = array_flip($arr["request"]["recipient"]);
			aw_session_set("crm_bill_sendmail_recipients_tmp", $recipients_tmp);
		}
		return PROP_IGNORE;
	}

	function _get_sendmail_recipients(&$arr)
	{
		$this_o = $arr["obj_inst"];
		if(!$this_o->prop("customer"))
		{
			return PROP_IGNORE;
		}

		$r = PROP_OK;
		$t = $arr["prop"]["vcl_inst"];

		$t->add_fields(array(
			"email" => t("E-posti aadress"),
			"send" => t("Saata"),
			"name" => t("Nimi"),
			"rank" => t("Ametinimetus"),
			"phone" =>  t("Telefon"),
			"co" => t("Organisatsioon")
		));
		$t->set_rgroupby(array("title" => "title"));

		/// potential email recipients by type
		// 'customer_bill' -- customer bill reception email contacts
		$recipients = $arr["obj_inst"]->get_mail_recipients(array("customer_bill"));
		if (count($recipients))
		{
			foreach ($recipients as $email_address => $data)
			{
				$data[2] = "customer";
				$prop_name = $this->add_recipient_propdefn($t, $email_address, $data, $this_o, t("Kliendi arveaadressid"));
			}
		}

		// 'customer_general' -- general customer email contacts
		$recipients = $arr["obj_inst"]->get_mail_recipients(array("customer_general"));
		if (count($recipients))
		{
			foreach ($recipients as $email_address => $data)
			{
				$data[2] = "customer";
				$prop_name = $this->add_recipient_propdefn($t, $email_address, $data, $this_o, t("Kliendi &uuml;ldaadressid"));
			}
		}

		// 'user' -- bill creator and current user
		$recipients = $arr["obj_inst"]->get_mail_recipients(array("user"));
		if (count($recipients))
		{
			foreach ($recipients as $email_address => $data)
			{
				$data[2] = "implementor";
				$prop_name = $this->add_recipient_propdefn($t, $email_address, $data, $this_o, t("Kasutaja"));
			}
		}

		// 'project_managers' -- people associated with this project as project managers
		$recipients = $arr["obj_inst"]->get_mail_recipients(array("project_managers"));
		if (count($recipients))
		{
			foreach ($recipients as $email_address => $data)
			{
				$data[2] = "implementor";
				$prop_name = $this->add_recipient_propdefn($t, $email_address, $data, $this_o, t("Seotud projektijuhid"));
			}
		}

		// 'custom' -- user defined custom recipients
		$recipients = $arr["obj_inst"]->get_mail_recipients(array("custom"));
		if (count($recipients))
		{
			foreach ($recipients as $email_address => $data)
			{
				$data[2] = "";
				$prop_name = $this->add_recipient_propdefn($t, $email_address, $data, $this_o, t("Lisaaadressid"));
			}
		}

		// 'default' -- crm default bill recipients
		$recipients = $arr["obj_inst"]->get_mail_recipients(array("default"));
		if (count($recipients))
		{
			foreach ($recipients as $email_address => $data)
			{
				$data[2] = "";
				$prop_name = $this->add_recipient_propdefn($t, $email_address, $data, $this_o, t("Vaikimisi koopiasaajad"), true);
			}
		}

		return $r;
	}

	private function add_recipient_propdefn(vcl_table $t, $email_address, $recipient_data, $this_o, $title, $disabled = false)
	{
		static $i;
		++$i;
		$recipient_oid = $recipient_data[0];
		$name = $recipient_data[1];
		$phones = $organization = $profession = $chooser = "";

		if ($recipient_oid)
		{
			$recipient = new object($recipient_oid);

			if ($recipient->is_a(CL_CRM_PERSON))
			{
				if ("implementor" === $recipient_data[2])
				{
					$organization_o = new object($this_o->prop("impl"));
				}
				elseif ("customer" === $recipient_data[2])
				{
					$organization_o = new object($this_o->prop("customer"));
				}
				else
				{
					$organization_o = new object($recipient->company_id());
				}

				$organization = html::obj_change_url($organization_o->id(), $organization_o->name());
				$profession = implode(", " , $recipient->get_profession_names($organization_o));
				$name = html::obj_change_url($recipient->id(), $recipient->name());
			}
			elseif ($recipient->is_a(CL_CRM_COMPANY))
			{
				$organization = html::obj_change_url($recipient->id(), $name);
				$name = "";
			}

			if ($recipient->has_method("get_phones"))
			{
				$phones = implode(", ", $recipient->get_phones());
			}
		}

		// recipient selector chooser
		$checked_to = $checked_cc = $checked_bcc = 0;
		if (!$disabled)
		{ // temporarily saved mail send view data
			$recipients_tmp = aw_global_get("crm_bill_sendmail_recipients_tmp");
			$checked_to = !empty($recipients_tmp["{$email_address}-to"]);
			$checked_cc = !empty($recipients_tmp["{$email_address}-cc"]);
			$checked_bcc = !empty($recipients_tmp["{$email_address}-bcc"]);
		}

		$prop_name = "recipient[{$i}]";
		$chooser = html::radiobutton(array(
			"caption" => t("to"),
			"name" => $prop_name,
			"checked" => $checked_to,
			"value" => "{$email_address}-to",
			"disabled" => $disabled
		));
		$chooser .= " ";
		$chooser .= html::radiobutton(array(
			"caption" => t("cc"),
			"name" => $prop_name,
			"checked" => $checked_cc,
			"value" => "{$email_address}-cc",
			"disabled" => $disabled
		));
		$chooser .= " ";
		$chooser .= html::radiobutton(array(
			"caption" => t("bcc"),
			"name" => $prop_name,
			"checked" => $checked_bcc,
			"value" => "{$email_address}-bcc",
			"disabled" => $disabled
		));
		$chooser = html::span(array("content" => $chooser, "nowrap" => 1));

		//
		$t->define_data(array(
			"title" => $title,
			"send" => $chooser,
			"email" => $email_address,
			"name" => $name,
			"phone" => $phones,
			"rank" => $profession,
			"co" => $organization
		));
	}

	function _get_sendmail_attachments(&$arr)
	{
		$r = PROP_OK;
		$invoice_pdf_o = $reminder_pdf_o = $appendix_pdf_o = null;
		$invoice_pdf_link = $reminder_pdf_link = $appendix_pdf_link = "";
		$value = array();
		$attachments_tmp = safe_array(aw_global_get("crm_bill_sendmail_attachments_tmp"));

		if (count($attachments_tmp))
		{
			try
			{
				if (!empty($attachments_tmp["p"]))
				{
					$invoice_pdf_o = obj($attachments_tmp["p"], array(), CL_FILE);
				}
				elseif (!empty($attachments_tmp["r"]))
				{
					$invoice_pdf_o = obj($attachments_tmp["r"], array(), CL_FILE);
				}

				if (!empty($attachments_tmp["a"]))
				{
					$appendix_pdf_o = obj($attachments_tmp["a"], array(), CL_FILE);
				}
			}
			catch (Exception $e)
			{
				$this->show_error_text(t("Viga PDF dokumendi laadimisel"));
				aw_session_del("crm_bill_sendmail_attachments_tmp");
			}
		}
		elseif (isset($arr["request"]["sendmail_type"]))
		{
			if ("p" === $arr["request"]["sendmail_type"])
			{
				$invoice_pdf_o = $arr["obj_inst"]->make_preview_pdf();
				$attachments_tmp["p"] = $invoice_pdf_o->id();
			}
			elseif ("r" === $arr["request"]["sendmail_type"])
			{
				$reminder_pdf_o = $arr["obj_inst"]->make_reminder_pdf();
				$attachments_tmp["r"] = $invoice_pdf_o->id();
			}
			elseif ("pa" === $arr["request"]["sendmail_type"])
			{
				$invoice_pdf_o = $arr["obj_inst"]->make_preview_pdf();
				$appendix_pdf_o = $arr["obj_inst"]->make_add_pdf();
				$attachments_tmp["p"] = $invoice_pdf_o->id();
				$attachments_tmp["a"] = $appendix_pdf_o->id();
			}
			elseif ("ra" === $arr["request"]["sendmail_type"])
			{
				$reminder_pdf_o = $arr["obj_inst"]->make_reminder_pdf();
				$appendix_pdf_o = $arr["obj_inst"]->make_add_pdf();
				$attachments_tmp["r"] = $invoice_pdf_o->id();
				$attachments_tmp["a"] = $appendix_pdf_o->id();
			}
		}

		aw_session_set("crm_bill_sendmail_attachments_tmp", $attachments_tmp);

		if ($invoice_pdf_o)
		{
			$file_data = $invoice_pdf_o->get_file();
			$invoice_pdf_link = " " . html::href(array(
				"caption" => html::img(array(
					"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
					"border" => 0
				)) . $invoice_pdf_o->name() . " (". filesize($file_data["properties"]["file"])." B)",
				"url" => $invoice_pdf_o->get_url(),
			));
			$value["p"] = "p";
		}
		elseif ($reminder_pdf_o)
		{
			$file_data = $reminder_pdf_o->get_file();
			$reminder_pdf_link = " " . html::href(array(
				"caption" => html::img(array(
					"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
					"border" => 0
				)) . $reminder_pdf_o->name() . " (". filesize($file_data["properties"]["file"])." B)",
				"url" => $reminder_pdf_o->get_url(),
			));
			$value["r"] = "r";
		}

		if ($appendix_pdf_o)
		{
			$file_data = $appendix_pdf_o->get_file();
			$appendix_pdf_link = " " . html::href(array(
				"caption" => html::img(array(
					"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
					"border" => 0
				)) . $appendix_pdf_o->name() . " (". filesize($file_data["properties"]["file"])." B)",
				"url" => $appendix_pdf_o->get_url(),
			));
			$value["a"] = "a";
		}

		$arr["prop"]["options"] = array(
			"p" => t("Arve PDF") . $invoice_pdf_link,
			"r" => t("Meeldetuletuse PDF") . $reminder_pdf_link,
			"a" => t("Arve lisa PDF") . $appendix_pdf_link
		);

		$arr["prop"]["value"] = $value;

		return $r;
	}

	function _set_sendmail_attachments($arr)
	{
		$attachments_tmp = safe_array(aw_global_get("crm_bill_sendmail_attachments_tmp"));
		$attachments_tmp["p"] = empty($arr["prop"]["value"]["p"]) ? 0 : $attachments_tmp["p"];
		$attachments_tmp["r"] = empty($arr["prop"]["value"]["r"]) ? 0 : $attachments_tmp["r"];
		$attachments_tmp["a"] = empty($arr["prop"]["value"]["a"]) ? 0 : $attachments_tmp["a"];

		if (!empty($arr["prop"]["value"]["p"]))
		{
			if (empty($attachments_tmp["p"]))
			{
				$invoice_pdf_o = $arr["obj_inst"]->make_preview_pdf();
			}
			else
			{
				try
				{
					$invoice_pdf_o = obj($attachments_tmp["p"], array(), CL_FILE);
				}
				catch (Exception $e)
				{
					$invoice_pdf_o = $arr["obj_inst"]->make_preview_pdf();
				}
			}

			$attachments_tmp["p"] = $invoice_pdf_o->id();
		}
		elseif (!empty($arr["prop"]["value"]["r"]))
		{
			if (empty($attachments_tmp["r"]))
			{
				$reminder_pdf_o = $arr["obj_inst"]->make_reminder_pdf();
			}
			else
			{
				try
				{
					$reminder_pdf_o = obj($attachments_tmp["r"], array(), CL_FILE);
				}
				catch (Exception $e)
				{
					$reminder_pdf_o = $arr["obj_inst"]->make_reminder_pdf();
				}
			}

			$attachments_tmp["r"] = $reminder_pdf_o->id();
		}

		if (!empty($arr["prop"]["value"]["a"]))
		{
			if (empty($attachments_tmp["a"]))
			{
				$appendix_pdf_o = $arr["obj_inst"]->make_add_pdf();
			}
			else
			{
				try
				{
					$appendix_pdf_o = obj($attachments_tmp["a"], array(), CL_FILE);
				}
				catch (Exception $e)
	{
					$appendix_pdf_o = $arr["obj_inst"]->make_add_pdf();
				}
			}

			$attachments_tmp["a"] = $appendix_pdf_o->id();
		}

		aw_session_set("crm_bill_sendmail_attachments_tmp", $attachments_tmp);
		return PROP_IGNORE;
	}

	function _get_bill_mail_subj(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["value"] = $arr["obj_inst"]->get_mail_subject(false);
		$arr["prop"]["onblur"] = "crm_bill_refresh_mail_text_changes();";
		return $r;
	}

	function _get_sendmail_subject_view(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["value"] = html::span(array(
			"content" => $arr["obj_inst"]->get_mail_subject(true),
			"id" => "sendmail_subject_text_element"
		)) . html::linebreak(2);
		return $r;
	}

	function _get_bill_mail_ct(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["value"] = $arr["obj_inst"]->get_mail_body(false);
		$arr["prop"]["onblur"] = "crm_bill_refresh_mail_text_changes();";
		return $r;
	}

	function _get_sendmail_body_view(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["value"] = html::span(array(
			"content" => nl2br($arr["obj_inst"]->get_mail_body(true)),
			"id" => "sendmail_body_text_element"
		));
		return $r;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "comments_add":
				if($prop["value"])
				{
					$arr["obj_inst"]->add_comment($prop["value"]);
				}
				break;

			case "comments":
				if(!(empty($arr["request"]["set_important_comment"])))
				$arr["obj_inst"]->set_meta("important_comment" , $arr["request"]["set_important_comment"]);
				break;

			case "partial_recieved":
				$pa = array();
				if (isset($arr["request"]["new_payment"]))
				{
					if(is_oid($arr["request"]["new_payment"]))
					{
						$pa[] = $arr["request"]["new_payment"];
					}
					else
					{
						$pa = explode(",",$arr["request"]["new_payment"]);
					}
				}

				foreach($pa as $p)
				{
					if(object_loader::can("view", $p))
					{
						$error = $this->add_payment(array(
							"o" => $arr["obj_inst"],
							"p" => $p,
							"show_error" => 1
						));
					}
				}

				if(!empty($error))
				{
					$prop["error"] = $error;
					return PROP_ERROR;
				}
				break;

			case "bill_no":
				if (($prop["value"] > 0) && $prop["value"] != $arr["obj_inst"]->prop("bill_no"))
				{
					// check that no bills have the same number
					$ol = new object_list(array(
						"class_id" => CL_CRM_BILL,
						"bill_no" => $prop["value"],
						"oid" => new obj_predicate_not($arr["obj_inst"]->id())
					));
					if ($ol->count())
					{
						$prop["error"] = t("Selle numbriga arve on juba olemas");
						return PROP_ERROR;
					}

					$ser = get_instance(CL_CRM_NUMBER_SERIES);
					if (!$ser->number_is_in_series(CL_CRM_BILL, $prop["value"]))
					{
						$prop["error"] = t("Number ei ole seerias!");
				//		return PROP_ERROR;
					}
				}
				break;

			case "bill_rows":
				$this->_save_rows($arr);
				break;

			case "rows_different_pages":
				$arr["obj_inst"]->set_meta("rows_in_page" , $arr["request"]["rows_in_page"]);
				break;

			case "state":
				// if state is set to paid and payment date is -1 or same as bill date
				if ($prop["value"] == 2 &&
					($arr["obj_inst"]->prop("bill_date") == $arr["obj_inst"]->prop("bill_recieved") ||
					 $arr["obj_inst"]->prop("bill_recieved") < 300
					)
				)
				{
					$this->_set_recv_date = time();
				}
				if($prop["value"] == 3)
				{
					$payments = $arr["obj_inst"]->connections_from(array('type' => 'RELTYPE_PAYMENT'));
					if(!(is_array($payments) && sizeof($payments)))
					{
						$this->add_payment(array("o"=> $arr["obj_inst"]));
					}
				}
				break;

			case "impl":
				if(!$prop["value"])
				{
					$u = get_instance(CL_USER);
					$prop["value"] = $u->get_current_company();
				}
				break;

			case "customer":
				if (isset($prop["value"]) and $arr["obj_inst"]->prop("customer") != $prop["value"])
				{
					if (!object_loader::can("view", $prop["value"]))
					{
						$prop["error"] = sprintf(t("Klient pole loetav (id %s)"), $customer_o->id());
						return PROP_ERROR;
					}

					$customer_o = new object($prop["value"]);

					if (!$customer_o->is_a(CL_CRM_PERSON) and !$customer_o->is_a(CL_CRM_COMPANY))
					{
						$prop["error"] = sprintf(t("Sobimatu kliendi id (%s)"), $customer_o->id());
						return PROP_ERROR;
					}

					$this->customer_object = $customer_o;
				}
				else
				{
					$retval = PROP_IGNORE;
				}
				break;
		}
		return $retval;
	}

	function _get_bill_rec_name(&$arr)
	{
		$r = PROP_OK;
		$arr["prop"]["value"] = "";
		$ps = new popup_search();
		$ps->set_class_id(array(CL_CRM_PERSON));
		$ps->set_id($arr["obj_inst"]->id());
		$ps->set_reload_layout("sendmail_settings_l");
		$ps->set_property("mail_receiver");
		$save_btn = " " . html::href(array(
			"url" => "javascript:submit_changeform('submit')",
			"title" => t("Lisa sisestatud e-posti aadress"),
			"caption" => html::img(array("url" => icons::get_std_icon_url("save")))
		)) . " ";
		$arr["prop"]["post_append_text"] = $save_btn . $ps->get_search_button();
		return $r;
	}

	function _set_bill_rec_name(&$arr)
	{
		$r = PROP_IGNORE;
		if (!empty($arr["prop"]["value"]))
		{
			try
			{
				$arr["obj_inst"]->add_receiver($arr["prop"]["value"]);
			}
			catch (Exception $e)
			{
				$r = PROP_ERROR;
				$arr["prop"]["error"] = t("Vigane e-posti aadress");
			}
		}
		return $r;
	}

	function _set_customer_address(&$arr)
	{
		if (!$arr["obj_inst"]->prop("customer"))
		{
			$r = PROP_IGNORE;
		}
		else
		{
			$r = PROP_OK;
		}
		return $r;
	}

	function _get_dn_tb(&$arr)
	{
		if($arr["new"])
		{
			return PROP_IGNORE;
		}

		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_SHOP_DELIVERY_NOTE), $arr["obj_inst"]->id(), 14);
		$tb->add_search_button(array(
			"pn" => "add_dn",
			"clid" => CL_SHOP_DELIVERY_NOTE,
			"multiple" => 1,
		));
		$tb->add_delete_rels_button();
		return PROP_OK;
	}

	function customer_add_meta_cb($arr)
	{
		if(!$arr["obj_inst"]->prop("customer"))
		{
			return PROP_IGNORE;
		}

		$dt = crm_bill_obj::$customer_address_properties;

		foreach($dt as $prop => $caption)
		{
			$retval["address_meta[".$prop."]"] = array(
				"name" => "address_meta[".$prop."]",
				"type" => "textbox",
				"parent" => "top_right",
				"caption" => $caption,
				"size" => 20,
				"value" => $arr["obj_inst"]->get_customer_address($prop)
			);
		}

		return $retval;
	}

//------------------------------api
	/**
		@attrib name=add_payment api=1
		@param id optional type=int
			bill id
		@param o optional type=object
			bill object
		@param sum optional type=double
		@param ru optional type=string
		@param p optional type=oid/object
			payment id
	**/
	public function add_payment($arr)
	{
		$sum = 0;
		$error = "";
		extract($arr);
		if(!is_object($o) && object_loader::can("view", $id))
		{
			$o = obj($id);
		}

		if(object_loader::can("view" , $p))
		{
			$p = obj($p);
		}

		if(is_object($p))
		{
			$error = $p->add_bill($o);
		}

		if(!is_object($p))
		{
			$p = obj($o->add_payment($sum));
		}

		if($show_error == 1)
		{
			return $error;
		}

		return $this->mk_my_orb("change", array("id" => $p->id(), "return_url" => $ru), CL_CRM_BILL_PAYMENT);
	}

	function get_customer_name($b)
	{
		if(is_oid($b))
		{
			$b = obj($b);
		}
		if($b->prop("customer_name"))
		{
			return $b->prop("customer_name");
		}
		else
		{
			return $b->prop("customer.name");
		}
	}

	function get_customer_address($b, $prop = "")
	{
		if(is_oid($b))
		{
			$b = obj($b);
		}
		if(!$b->prop("customer_name") || !$b->prop("customer_address"))
		{
			if($this->can("view" , $b->prop("customer")))
			{
				$cust_obj = obj($b->prop("customer"));
				if($cust_obj->class_id() == CL_CRM_COMPANY)
				{
					$a = "contact";
				}
				else
				{
					$a = "address";
				}
			}
			else
			{
				return "";
			}
		}

		if(!$prop)
		{
			if($b->prop("customer_name"))
			{
				return $b->prop("customer_address");
			}
			else
			{
				return $cust_obj->prop($a.".name");
			}
		}

		if($b->prop("customer_name"))
		{
			$cust_addr = $b->meta("customer_addr");
			return $cust_addr[$prop];
		}
		else
		{
			switch($prop)
			{
				case "street":
					return $cust_obj->prop($a.".aadress");
				break;
				case "index":
					return $cust_obj->prop($a.".postiindeks");
				break;
				case "country":
					return $cust_obj->prop($a.".riik.name");
				break;
				case "county":
					return $cust_obj->prop($a.".maakond.name");
				break;
				case "city":
					return $cust_obj->prop($a.".linn.name");
				break;
				case "country_en":
					if($cust_obj->prop($a.".riik.name_en")) return $cust_obj->prop($a.".riik.name_en");
					else return $cust_obj->prop($a.".riik.name");
				break;
				return "";
			}
		}
	}

	function get_customer_code($b)
	{
		if(is_oid($b))
		{
			$b = obj($b);
		}
		if($b->prop("customer_name"))
		{
			return $b->prop("customer_code");
		}
		else
		{
			return $b->prop("customer.code");
		}
	}

	function num($a)
	{
		return str_replace(",", ".", $a);
	}

	function _get_writeoffs(&$arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->_init_bill_rows_t($t);
		$t->set_caption(t("Mahakantud read"));

		$task_i = new task();

		$t->set_sortable(false);

		$writeoffs = $arr["obj_inst"]->get_writeoff_rows_data();
		$first_oe = 0;
		foreach($writeoffs as $row)
		{
			//eraldab muid kulusid
			if(!$first_oe && $row["is_oe"])
			{
				$t->define_data(array(
					"name" => t("Kulud:"),
				));
				$first_oe = 1;
			}

			$id = $row["id"];

			$connect_row_url =  $this->mk_my_orb("add_task_row_to_bill_row", array(
				"row" => $id,
			));

			$connect_row_link = html::href(array(
				"url" => "javascript:aw_popup_scroll('".$connect_row_url."','Otsing',1100,700)",
				"caption" => t("Lisa toimetuse rida"),
				"title" => t("Otsi")
			));

			$t->define_data(array(
				"name" => $this->get_row_html($row["id"],"name",$arr),
				"code" => $this->get_row_html($row["id"],"code",$arr),
				"unit" => $this->get_row_html($row["id"],"unit",$arr),
				"has_tax" => $this->get_row_html($row["id"],"has_tax",$arr),
				"prod" => $this->get_row_html($row["id"],"prod",$arr),
				"sel" => html::checkbox(array(
					"name" => "sel_rows[]",
					"value" => $row["id"]
				)),
				"change" => $this->get_row_html($row["id"],"change",$arr),
				"person" => $this->get_row_html($row["id"],"person",$arr),
				"color" => "gray"
			));
		}
		return PROP_OK;
	}

	private function get_phone_by_mail_id($mail, $name, $phone)
	{
		if(strlen($name))
		{
			$persons = new object_list(array("class_id" => CL_CRM_PERSON, "lang_id" => array() , "name" => $name));
			$person = $persons->begin();
			if(is_object($person))
			{
				return $person->get_phone();
			}
		}

		$c = new connection();
		$t2row = $c->find(array(
			"from.class_id" => CL_CRM_PERSON_WORK_RELATION,
			"to" => $mail
		));

		foreach($t2row as $conn)
		{
			$rel = obj($conn["from"]);
			if($rel->prop("phone.name"))
			{
				return $rel->prop("phone.name");
			}
			else
			{
				$person = $rel->get_first_obj_by_reltype("RELTYPE_PERSON");
				if(is_object($person))
				{
					return $person->get_phone();
				}
			}
		}

		$c = new connection();
		$t2row = $c->find(array(
			"from.class_id" => CL_CRM_PERSON,
			"to" => $mail,
		));
		foreach($t2row as $conn)
		{
			$person = obj($conn["from"]);
			return $person->get_phone();
		}

		return $phone;
	}

	function _get_dn_tbl(&$arr)
	{
		if($arr["new"])
		{
			return PROP_IGNORE;
		}

		$t = $arr["prop"]["vcl_inst"];
		$t->define_chooser(array(
			"field" => "oid",
			"name" => "sel",
		));
		$t->add_fields(array(
			"number" => t("Number"),
			"date" => t("Kuup&auml;ev"),
		));
		$conn = $arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_DELIVERY_NOTE",
		));
		foreach($conn as $c)
		{
			$dn = $c->to();
			$t->define_data(array(
				"number" => html::obj_change_url($dn, ($no = $dn->prop("number")) ? $no : t("(Puudub)")),
				"date" => date('d.m.Y', $dn->prop("delivery_date")),
				"oid" => $dn->id(),
			));
		}
		return PROP_OK;
	}

	function _set_dn_tb($arr)
	{
		if($add = $arr["request"]["add_dn"])
		{
			$tmp = explode(",", $add);
			foreach($tmp as $dn)
			{
				$arr["obj_inst"]->connect(array(
					"to" => $dn,
					"type" => "RELTYPE_DELIVERY_NOTE",
				));
			}
		}
		return PROP_IGNORE;
	}

	function _init_bill_rows_t($t)
	{
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimetus"),
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "unit",
			"caption" => "",//t("&Uuml;hik"),
			"chgbgcolor" => "color",
			"align" => "right"
		));

		$t->define_field(array(
			"name" => "prod",
			"caption" => t("Artikkel"),
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"name" => "project",
			"caption" => t("Projekt"),
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"name" => "person",
			"caption" => t("Isik"),
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"name" => "has_tax",
			"caption" => html::href(array(
				"url" => "javascript:selall('rows')",
				"caption" => t("+KM?"),
				"title" => t("Lisandub k&auml;ibemaks?")
			)),
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"name" => "change",
			"caption" => t("muuda"),
			"chgbgcolor" => "color"
		));

		$t->define_field(array(
			"name" => "sel",
			"caption" => t("Vali"),
			"chgbgcolor" => "color"
		));
	}

	function _get_bill_rows(&$arr)
	{
		if($arr["new"])
		{
			return PROP_IGNORE;
		}

		$t = $arr["prop"]["vcl_inst"];
		$this->_init_bill_rows_t($t);
		$t->set_caption(t("Arve read"));

		$sum = 0;

		$pps = new crm_participant_search();
		$curr_inst = get_instance(CL_CURRENCY);
		$task_i = get_instance(CL_TASK);
		$u = get_instance(CL_USER);

		$prods = array("" => t("--vali--"));
		// get prords from co

		$co = obj($u->get_current_company());
		$ccurrency = $co->prop("currency");
		$ccurrency_name = $co->prop("currency.name");
		$quality_options = array("" => "") + $arr["obj_inst"]->get_quality_options();
		$bcurrency = $arr["obj_inst"]->get_bill_currency_id();

		$wh = $co->get_first_obj_by_reltype("RELTYPE_WAREHOUSE");
		if ($wh)
		{
			$prods = $prods + $wh->get_packet_list()->names();
		}
		$rows = $arr["obj_inst"]->get_bill_rows_data();


		$default_row_jrk = $first_oe = 0;
		$ut = new vcl_table(array(
			"layout" => "generic",
		));
		$ut->define_field(array(
			"name" => "field1",
			"caption" => "",
			"chgbgcolor" => "color"
		));
		$ut->define_field(array(
			"name" => "field2",
			"caption" => "",
			"chgbgcolor" => "color"
		));


		foreach($rows as $row)
		{
			//eraldab muid kulusid
			if(!$first_oe && $row["is_oe"])
			{
				$t->define_data(array(
					"name" => t("Kulud:"),
				));
				$first_oe = 1;
			}

			// j2rjekorranuumbrite genereerimine...
			if($default_row_jrk < $row["jrk"])
			{
				$default_row_jrk = $row["jrk"];
			}
			if(!$row["jrk"])
			{
				$row["jrk"] = $default_row_jrk;
			}
			$default_row_jrk+= 10;
			$connect_row_url =  $this->mk_my_orb("add_task_row_to_bill_row", array(
				"row" => $row["id"],
			));

			$connect_row_link = html::href(array(
				"url" => "javascript:aw_popup_scroll('".$connect_row_url."','Otsing',1100,700)",
				"caption" => t("Lisa toimetuse rida"),
				"title" => t("Otsi")
			));
			$t->define_data(array(
				"name" => $this->get_row_html($row["id"],"name",$arr),
				"code" => $this->get_row_html($row["id"],"code",$arr),
				"unit" => $this->get_row_html($row["id"],"unit",$arr),
				"has_tax" => $this->get_row_html($row["id"],"has_tax",$arr),
				"prod" => $this->get_row_html($row["id"],"prod",$arr),
				"project" => $this->get_row_html($row["id"],"project",$arr),
				"sel" => html::checkbox(array(
					"name" => "sel_rows[]",
					"value" => $row["id"]
				)),
				"change" => $this->get_row_html($row["id"],"change",$arr),
				"person" => $this->get_row_html($row["id"],"person",$arr),
			));
			$sum += $row["sum"];
		}
		$t->set_sortable(false);

		if($arr["obj_inst"]->meta("agreement_price"))
		{
			$sum = 0;
			foreach($arr["obj_inst"]->meta("agreement_price") as $agreement_price)
			{
				$sum+= $agreement_price["sum"];
			}
		}

		if ($arr["obj_inst"]->prop("disc") > 0)
		{
			$sum -= $sum * ($arr["obj_inst"]->prop("disc") / 100.0);
		}

		//kokkuleppe hind , kui igav hakkab, v6iks selle ka yle vaadata... see on yks suur jama kokku
		$agreement_prices = $arr["obj_inst"]->meta("agreement_price");
		if($agreement_prices == null)
		{
			$agreement_prices = array();
		}

		// $agreement_prices[] = array();//agreement_price is a deprecated structure. here disabling it for further use. compatibility preserved with older bills
		$x = 0;
		foreach($agreement_prices as $key => $agreement_price)
		{
			if((isset($agreement_price["name"]) && $agreement_price["price"]) || empty($done_new_line))
			{
				$price_cc = "";//hind oma organisatsiooni valuutas
				$sum_cc = "";//summa oma organisatsiooni valuutas
				if(isset($agreement_price["name"]) && $agreement_price["price"] && $bcurrency && $ccurrency && $ccurrency != $bcurrency)
				{
					$cc_price = $curr_inst->convert(array(
						"from" => $bcurrency,
						"to" => $ccurrency,
						"sum" => $agreement_price["price"],
						"date" =>  $arr["obj_inst"]->prop("bill_date"),
					));
					$price_cc = html::linebreak().$cc_price." ".$ccurrency_name;
					$sum_cc = html::linebreak().$cc_price*$agreement_price["amt"]." ".$ccurrency_name;
				}
				$ut->clear_data();

				$unit_id = 0;
				$unit_name = "";
				if (isset($agreement_price["unit"]) and is_oid($agreement_price["unit"]))
				{
					$unit_obj = obj($agreement_price["unit"], array(), CL_UNIT);
					$unit_id = $unit_obj->id();
					$unit_name = $unit_obj->prop("unit_code");
				}
				$ut->define_data(array(
					"field1" => t("&Uuml;hik"),
					"field2" => html::textbox(array(
						"name" => "agreement_price[".$x."][unit]",
						"content" => $unit_name,
						"value" => $unit_id,
						"size" => 3,
						"autocomplete_source" => $this->mk_my_orb("unit_options_autocomplete_source"),
						"autocomplete_params" => array("agreement_price[".$x."][unit]"),
						"option_is_tuple" => 1
					)),
				));
				$ut->define_data(array(
					"field1" => t("Hind"),
					"field2" => html::textbox(array(
						"name" => "agreement_price[".$x."][price]",
						"value" => isset($agreement_price["price"]) ? $agreement_price["price"]: "",
						"size" => 4
					)).$price_cc,
				));
				$ut->define_data(array(
					"field1" => t("Kogus"),
					"field2" => html::textbox(array(
						"name" => "agreement_price[".$x."][amt]",
						"value" => isset($agreement_price["amt"]) ? $agreement_price["amt"] : "",
						"size" => 3
					)),
				));
				$ut->define_data(array(
					"field1" => t("Summa"),
					"field2" => (isset($agreement_price["sum"]) ? $agreement_price["sum"] : "").$sum_cc,
				));

				$url = $this->mk_my_orb("do_search", array(
					"pn" => "agreement_price[".$x."][prod]",
					"clid" => array(
						CL_SHOP_PRODUCT
					),
					"tbl_props" => array("oid", "name", "code", "parent"),
					"multiple" => 0,
					"no_submit" => 1,
				), "shop_product_popup_search");
				$url = "javascript:aw_popup_scroll('".$url."','".t("Otsi")."',600,500)";
				$prod_s = html::href(array(
					"caption" => html::img(array(
						"url" => "images/icons/search.gif",
						"border" => 0
					)),
					"url" => $url
				));
				$t->define_data(array(
					"name" => t("Kokkuleppehind")." ".($x+1).html::linebreak().html::textbox(array(
						"name" => "agreement_price[".$x."][date]",
						"value" => isset($agreement_price["date"]) ? $agreement_price["date"] : "",
						"size" => 8
					)).html::linebreak().html::textarea(array(
						"name" => "agreement_price[".$x."][name]",
						"value" => isset($agreement_price["name"]) ? $agreement_price["name"] : "",
						"rows" => 5,
						"cols" => 40
					)),
					"code" => html::textbox(array(
						"name" => "agreement_price[".$x."][code]",
						"value" => isset($agreement_price["code"]) ? $agreement_price["code"] : "",
						"size" => 10
					)),
					"unit" => $ut->draw(array("no_titlebar" => 1)),
					"has_tax" => html::checkbox(array(
						"name" => "agreement_price[".$x."][has_tax]",
						"ch_value" => 1,
						"checked" => empty($agreement_price["has_tax"]) ? false : true
					)),
					"prod" => html::textbox(array(
						"name" => "agreement_price[".$x."][prod]",
						"size" => 20,
						"class" => "prod_box",
					)).$prod_s,
					"sel" => html::checkbox(array(
						"name" => "sel_rows[]",
						"value" => $x
					)),
					"person" => html::linebreak().$pps->get_popup_search_link(array(
						"pn" => "agreement_price[".$x."][person]",
						"multiple" => 1,
						"clid" => array(CL_CRM_PERSON)
					))
				));
				$x++;
				if(empty($agreement_price["name"]) || empty($agreement_price["price"]))
				{
					$done_new_line = 1;
				}
			}
		}

		return PROP_OK;
	}

	/**
		@attrib name=post_row all_args=1
	**/
	function post_row($arr)
	{
		if(!$this->can("view" , $arr["id"]))
		{
			die(0);
		}

		$o = obj($arr["id"]);
		$props = array("name" , "comment", "date", "unit");
		foreach($props as $prop)
		{
			if(isset($arr[$prop]))
			{
				$o->set_prop($prop , iconv("UTF-8", aw_global_get("charset"), $arr[$prop]));
			}
		}

		if(!empty($arr["price"]))
		{
			$o->set_prop("price" , aw_math_calc::string2float($arr["price"]));
		}

		if(!empty($arr["amt"]))
		{
			$o->set_prop("amt" , aw_math_calc::string2float($arr["amt"]));
		}

		if(isset($arr["prod"]) && $this->can("view", $arr["prod"]) && obj($arr["prod"])->class_id() == CL_SHOP_PRODUCT)
		{
			$o->set_prop("prod", $arr["prod"]);
		}

		if(!empty($arr["name"]))
		{
			$o->set_prop("desc" , iconv("UTF-8", aw_global_get("charset"), $arr["name"]));
		}

		if(!empty($arr["jrk"]))
		{
			$o->set_meta("jrk" , (int) $arr["jrk"]);
		}

		foreach($o->connections_from(array("type" => "RELTYPE_PEOPLE")) as $c)
		{
			if(!in_array($c->prop("to") , explode("," ,$arr["people"])))
			{
				$c->delete();
			}
		}
		$o->set_prop("people" , explode("," ,$arr["people"]));

		if(isset($arr["has_tax"]))
		{
			if($arr["has_tax"] === "true")
			{
				$o->set_prop("has_tax" , 1);
			}
			elseif($arr["has_tax"] > 0)
			{
				$o->set_prop("tax" , (int) $arr["has_tax"]);
				$o->set_prop("has_tax" , 1);
			}
			else
			{
				$o->set_prop("tax" , 0);
				$o->set_prop("has_tax" , 0);
			}
		}

		$o->save();

		if($o->meta("dno"))
		{
			$conn = $o->connections_to(array(
				"from.class_id" => CL_CRM_BILL,
				"type" => "RELTYPE_ROW",
			));
			$cn = reset($conn);
			foreach($cn->from()->connections_from(array(
				"type" => "RELTYPE_ROW",
			)) as $c)
			{
				$row = $c->to();
				if($row->meta("dno") == $o->meta("dno"))
				{
					$rows[$row->prop("prod")] = array(
						"amount" => $row->prop("amt"),
						"unit" => is_oid($row->prop("unit")) ? $row->prop("unit") : null,
						"price" => $row->prop("price"),
					);
				}
			}
			obj($o->meta("dno"))->update_dn_rows($rows);
		}

		die(var_dump($arr));
	}


	/**
		@attrib name=get_row_change_fields all_args=1
	**/
	function get_row_change_fields($arr)
	{
		$row = obj($arr["id"]);
		$pps = new crm_participant_search();
		extract($arr);
		$ret = "";//'<div id="row_'.$id.'_'.$field.'">';
		switch($arr["field"])
		{
			case "change":
				$ret.= html::button(array(
					"name" => "change_row",
					"value" => t("Salvesta"),
					"onclick" => "
						var a=document.getElementById('rows_".$id."__person_'); var result=[];
						for (var i=0; i<a.length; i++) {
							a[i].selected?result.push(a[i].value):'';
						}

						var has_tax = document.getElementsByName('rows[".$id."][has_tax]')[0].value;
						if(document.getElementsByName('rows[".$id."][has_tax]')[0].type == 'checkbox')
						{
							 has_tax = 0;
						}

					$.post('/automatweb/orb.aw?class=crm_bill&action=post_row', {
						jrk: document.getElementsByName('rows[".$id."][jrk]')[0].value
						, id: ".$id."
						, people: result
						, name: document.getElementsByName('rows[".$id."][name]')[0].value
						, comment: document.getElementsByName('rows[".$id."][comment]')[0].value
						, prod: document.getElementsByName('rows[".$id."][prod]')[0].value
						, unit: document.getElementsByName('rows[".$id."][unit]')[0].value
						, has_tax: has_tax
						, date: document.getElementsByName('rows[".$id."][date]')[0].value
						, jrk: document.getElementsByName('rows[".$id."][jrk]')[0].value
						, price: document.getElementsByName('rows[".$id."][price]')[0].value
						, amt: document.getElementsByName('rows[".$id."][amt]')[0].value
						, project: document.getElementsByName('rows[".$id."][project]')[0].value
						},function(data){load_new_data".$id."(); });

						function load_new_data".$id."()
						{
							$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'ajax_get_row_html', id: '".$arr["id"]."', field: 'name'}, function (html) {
								x=document.getElementById('row_".$id."_name');
								x.innerHTML=html;});
							$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'ajax_get_row_html', id: '".$arr["id"]."', field: 'unit'}, function (html) {
								x=document.getElementById('row_".$id."_unit');
								x.innerHTML=html;});
							$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'ajax_get_row_html', id: '".$arr["id"]."', field: 'prod'}, function (html) {
								x=document.getElementById('row_".$id."_prod');
								x.innerHTML=html;});
							$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'ajax_get_row_html', id: '".$arr["id"]."', field: 'person'}, function (html) {
								x=document.getElementById('row_".$id."_person');
								x.innerHTML=html;});
							$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'ajax_get_row_html', id: '".$arr["id"]."', field: 'change'}, function (html) {
								x=document.getElementById('row_".$id."_change');
								x.innerHTML=html;});
							$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'ajax_get_row_html', id: '".$arr["id"]."', field: 'has_tax'}, function (html) {
								x=document.getElementById('row_".$id."_has_tax');
								x.innerHTML=html;});
							$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'ajax_get_row_html', id: '".$arr["id"]."', field: 'project'}, function (html) {
								x=document.getElementById('row_".$id."_project');
								x.innerHTML=html;});
						}

						",
				));
				break;

			case "name":
				$ret.="<table><tr><td width=400> " . t("Jrk.") . " " .
				html::textbox(array(
					"name" => "rows[".$row->id()."][jrk]",
					"value" => $row->meta("jrk"),
					"size" => 3
				))
				. " " . t("Kuup&auml;ev") . " " .
				html::textbox(array(
					"name" => "rows[".$row->id()."][date]",
					"value" => $row->prop("date"),
					"size" => 8
				)) . ($row->is_writeoff() ? t("mahakantud") : "") . html::linebreak() .
				html::textbox(array(
					"name" => "rows[".$row->id()."][comment]",
					"value" => $row->comment(),
					"size" => 70
				)) . html::linebreak() .
				html::textarea(array(
					"name" => "rows[".$row->id()."][name]",
					"value" => $row->prop("desc"),
					"rows" => 5,
					"cols" => 70
				))
				."</td></tr></table>";
				break;

			case "code":
				$ret.=html::textbox(array(
					"name" => "rows[".$row->id()."][code]",
					"value" => $row->prop("code"),
					"size" => 10
				));
				break;

			case "unit":
				$ut = new vcl_table(array(
					"layout" => "generic",
				));
				$ut->define_field(array(
					"name" => "field1",
					"caption" => "",
					"chgbgcolor" => "color"
				));
				$ut->define_field(array(
					"name" => "field2",
					"caption" => "",
					"chgbgcolor" => "color"
				));
				$ut->clear_data();
				$ut->define_data(array(
					"field1" => t("&Uuml;hik"),
					"field2" => html::select(array(
						"name" => "rows[$id][unit]",
						"options" =>  $prods = array("" => t("--vali--")) + $this->get_unit_selection(),
						"value" => $row->prop("unit"),
					)),
				));
				$ut->define_data(array(
					"field1" => t("Hind"),
					"field2" => html::textbox(array(
						"name" => "rows[$id][price]",
						"value" => $row->prop("price"),
						"size" => 5
					)),
				));
				$ut->define_data(array(
					"field1" => t("Kogus"),
					"field2" => html::textbox(array(
						"name" => "rows[$id][amt]",
						"value" => $row->prop("amt"),
						"size" => 3
					)),
				));
				$ut->define_data(array(
					"field1" => t("Summa"),
					"field2" => $row->prop("price")*$row->prop("amt"),
				));
				$ret.=$ut->draw(array("no_titlebar" => 1));
				break;

			case "has_tax":
				if($row->prop("tax"))
				{
					$ret.=html::textbox(array(
						"name" => "rows[$id][has_tax]",
						"value" => $row->get_row_tax(true),
						"size" => 3,
					));
				}
				else
				{
					$ret.=html::checkbox(array(
						"checked" => $row->prop("has_tax"),
						"name" => "rows[$id][has_tax]",
						"onclick" => "$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'get_row_change_fields', id: ".$row->id().", field: 'tax'}, function (html) {
							x=document.getElementById('row_' + ".$row->id()." + '_has_tax');
							x.innerHTML=html;});"
					));
				}

				break;

			case "tax":
				$ret.=html::textbox(array(
					"name" => "rows[$id][has_tax]",
					"value" => $row->get_row_tax(true),
						"size" => 3,
				));
				break;

			case "prod":
				if(!$row->meta("dno"))
				{
					$m = new popup_menu;
					$m->begin_menu("move_rows_to_dn_".$id);
					$bill = $row->get_bill_object();
					foreach($bill->connections_from(array(
						"type" => "RELTYPE_DELIVERY_NOTE",
					)) as $c)
					{
						$m->add_item(array(
							"parent" => "move_rows_to_dn_".$id,
							"url" => "javascript:void(0)",
							"onClick" => "$.get('".$this->mk_my_orb("move_rows_to_dn", array(
								"sel_rows[0]" => $id,
								"dno" => $c->prop("to"),
								"id" => $bill->id(),
							))."');$('#dn_info_$id').html('".sprintf(t("Saatelehel %s"), htmlentities($c->prop("to.name")))."');",
							"text" => $c->to()->name(),
						));
					}
					$m->add_item(array(
						"parent" => "move_rows_to_dn_".$id,
						"url" => "javascript:void(0)",
						"onClick" => "$.get('".$this->mk_my_orb("move_rows_to_dn", array(
							"sel_rows[0]" => $id,
							"dno" => "new",
							"id" => $bill->id(),
						))."');$('#dn_info_$id').html('".sprintf(t("Saatelehel %s"), "")."');",
						"text" => t("Uus saateleht"),
					));
				}
				$url = $this->mk_my_orb("do_search", array(
					"pn" => "rows[$id][prod]",
					"clid" => array(
						CL_SHOP_PRODUCT
					),
					"tbl_props" => array("oid", "name", "code", "parent"),
					"multiple" => 0,
					"no_submit" => 1,
				), "shop_product_popup_search");
				$url = "javascript:aw_popup_scroll('".$url."','".t("Otsi")."',600,500)";
				$s = html::href(array(
					"caption" => html::img(array(
						"url" => "images/icons/search.gif",
						"border" => 0
					)),
					"url" => $url
				));
				$ret.=html::textbox(array(
					"name" => "rows[$id][prod]",
					"size" => 20,
					"value" => $row->prop("prod"),
				)).$s.html::linebreak().($row->meta("dno") ? sprintf(t("Saatelehel %s"), obj($row->meta("dno"))->name()) : "<div id='dn_info_$id'>".t("Liiguta saatelehele:").$m->get_menu(array(
					"icon" => "copy.gif",
				))."</div>");
				break;

			case "project":
				$ret.= html::select(array(
					"name" => "rows[$id][project]",
					"options" => array("" => t("--vali--")) + $row->get_project_selection(),
					"value" => $row->prop("project"),
				));
				$ps = new popup_search();
				$ps->set_class_id(array(CL_PROJECT));
				$ps->set_reload_layout("bottom");
				$ps->set_property("project");
				$ps->set_id($id);
				$ret.= $ps->get_search_button();
				break;

			case "person":
				$ret.=html::select(array(
					"name" => "rows[$id][person]",
					"options" => array("" => t("--vali--"))+$row->get_person_selection(),
					"value" => array_keys($row->get_person_selection()),
					"multiple" => 1
				)).html::linebreak().$pps->get_popup_search_link(array(
					"pn" => "rows[$id][person]",
					"multiple" => 1,
					"no_submit" => 1,
					"clid" => array(CL_CRM_PERSON)
				));
				break;
		}
		return $ret;
	}

	/** returns bill unit selection
		@attrib api=1
		@returns array
	**/
	public function get_unit_selection()
	{
		$filter = array(
			"class_id" => CL_UNIT
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
		$units = array();

		foreach($names as $id => $name)
		{
			if($name)
			{
				$units[$this->get_unit_id($name)] = $name;
			}
		}

		return $units;
	}

	private function get_unit_id($name)
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"name" => $name
		));
		$ids = $ol->ids();
		if(sizeof($ids))
		{
			return reset($ids);
		}
		else
		{
			return  null;
		}
	}

	/**
		@attrib name=get_row_change_fields all_args=1
	**/
	function ajax_get_row_change_fields($arr)
	{
		die(iconv(aw_global_get("charset"), "UTF-8", $this->get_row_change_fields($arr)));
	}

	/**
		@attrib name=ajax_get_row_html all_args=1
	**/
	function ajax_get_row_html($arr)
	{
		die(iconv(aw_global_get("charset"), "UTF-8", $this->get_row_html($arr["id"] , $arr["field"])));
	}

	private function set_currency_inst()
	{
		if(!isset($this->currency_inst))
		{
			$this->currency_inst = get_instance(CL_CURRENCY);
		}
	}

	function get_row_html($id,$field,$arr = array())
	{
		$row = obj($id);
		$ret = '<div id="row_'.$id.'_'.$field.'">';
		switch($field)
		{
			case "change":
				$ret.=html::button(array(
					"name" => "change_row",
					"value" => t("Muuda"),
					"onclick" => "crm_bill_edit_row('".$id."')",
				));
				break;
			case "project":
				if($row->prop("project"))
				{
					$ret.= get_name($row->prop("project"));
				}
				break;
			case "name":
				$ret.="<div>".
					$row->meta("jrk").html::linebreak().
					$row->prop("date").html::linebreak().
					"<b>".$row->prop("comment")."</b>".html::linebreak().
					//preg_replace('/([^\s]{100})(?=[^\s])/m', '$1 ', $row->prop("name")).
					wordwrap(nl2br(htmlspecialchars(($row->prop("desc")))), 100, html::linebreak(), true).
					"</div>";
				break;
			case "code":
				$ret.=$row->prop("code");
				break;
			case "unit":
				$price_cc = "";//hind oma organisatsiooni valuutas
				$sum_cc = "";//summa oma organisatsiooni valuutas
				if(isset($arr["obj_inst"]) && is_object($arr["obj_inst"]) && !$arr["new"])
				{
					$bcurrency = $arr["obj_inst"]->get_bill_currency_id();
					$date = $arr["obj_inst"]->prop("bill_date");
				}
				else
				{
					$bcurrency = $row->get_bill_currency_id();
					$date = $row->get_bill_date();
				}
				$ccurrency = $this->get_co_currency();
				if($bcurrency && $ccurrency && $ccurrency != $bcurrency)
				{
					$this->set_currency_inst();
					$cc_price = $this->currency_inst->convert(array(
						"from" => $bcurrency,
						"to" => $ccurrency,
						"sum" => $row->prop("price"),
						"date" =>  $date,
					));
					$ccurrency_o = new object($ccurrency);
					$price_cc = html::linebreak().round($cc_price , 2)." ".$ccurrency_o->name();
					$sum_cc = html::linebreak().round($cc_price*$row->prop("amt") , 2)." ".$ccurrency_o->name();
				}
				$ret.=$row->prop("unit.name").html::linebreak().
					t("Hind").": ".$row->prop("price").html::linebreak().$price_cc.
					t("Kogus").": ".$row->prop("amt").html::linebreak().$sum_cc.
					t("Summa").": ".$row->prop("price")*$row->prop("amt");
				break;
			case "has_tax":
				if($row->prop("tax"))
				{
					$ret.= $row->prop("tax")." %";
				}
				else
				{
					$ret.=html::checkbox(array(
						"checked" => $row->prop("has_tax"),
						"disabled" => 1,
						"name" => "disabled_checkbox",
					));
				}
				break;
			case "prod":
				$ret.=$row->prop("prod.name").(($dn = $row->meta("dno")) ? html::linebreak().sprintf(t("(Saatelehel %s)"), obj($dn)->name()): "");
				break;
			case "person":
				$ret.=join(html::linebreak(), $row->get_person_selection());
				break;
		}
		return $ret.'</div>';
	}

	/** searches and connects bill row to task row
		@attrib name=add_task_row_to_bill_row
		@param row optional type=oid
			row id
		@param task_row optional
			task row id
		@param  content optional type=string
			task row content
		@param  task optional type=string
			task name
		@param  project optional type=string
			project name
		@param  customer optional type=string
			customer name
	**/
	function add_task_row_to_bill_row($arr)
	{
		$content = "";
		if(is_oid($arr["task_row"]) || (is_array($arr["task_row"]) && sizeof($arr["task_row"])))
		{
			if(is_oid($arr["task_row"]))
			{
				$arr["task_row"] = array($arr["task_row"]);
			}
			$bill_row = obj($arr["row"]);
			foreach($arr["task_row"] as $tr)
			{
				$error = $bill_row->connect_task_row($tr);
				if($error)
				{
					break;
				}
			}
			if($error)
			{
				$content.= $error.html::linebreak();
			}
			else
			{
				die("<script language='javascript'>
					if (window.opener)
					{
						window.opener.location.reload();
					}
					window.close();
				</script>");
			}
		}


		$htmlc = new htmlclient();
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "content",
			"type" => "textbox",
			"value" => $arr["content"],
			"caption" => t("Toimetuse rea sisu"),
		));
		$htmlc->add_property(array(
			"name" => "task",
			"type" => "textbox",
			"value" => $arr["task"],
			"caption" => t("Toimetus"),
			"autocomplete_class_id" => array(CL_TASK),
		));
		$htmlc->add_property(array(
			"name" => "customer",
			"type" => "textbox",
			"value" => $arr["customer"],
			"caption" => t("Klient"),
			"autocomplete_class_id" => array(CL_CRM_COMPANY,CL_CRM_PERSON),
		));
		$htmlc->add_property(array(
			"name" => "project",
			"type" => "textbox",
			"value" => $arr["project"],
			"caption" => t("Projekt"),
			"autocomplete_class_id" => array(CL_PROJECT),
		));
		$htmlc->add_property(array(
			"name" => "submit",
			"type" => "submit",
			"value" => t("Otsi"),
			"caption" => t("Otsi")
		));
		$data = array(
			"row" => $arr["row"],
			"orb_class" => $_GET["class"]?$_GET["class"]:$_POST["class"],
			"reforb" => 0,
		);

		$t = new vcl_table(array(
			"layout" => "generic",
		));
		$t->add_fields(array(
			"choose" => "",
			"content" => t("Sisu"),
			"task" => t("Toimetus"),
			"project" => t("Projekt"),
			"customer" => t("Klient"),
		));
		$t->define_chooser(array(
			"name" => "task_row",
			"field" => "oid",
		));

		$filter = array(
			"class_id" => CL_TASK_ROW,
			"lang_id" => array(),
			"bill_id" => new obj_predicate_compare(OBJ_COMP_LESS_OR_EQ, 1),
		);

		$task_filter = array(
			"class_id" => CL_TASK,
			"lang_id" => array(),
		);

		if($arr["task"])
		{
			$task_filter["name"] = "%".$arr["task"]."%";
		}
		if($arr["project"])
		{
			$task_filter["CL_TASK.project.name"] = "%".$arr["project"]."%";
		}
		if($arr["customer"])
		{
			$task_filter["CL_TASK.customer.name"] = "%".$arr["customer"]."%";
		}
		if(sizeof($task_filter) > 2)
		{
			$tasks = new object_list($task_filter);
			$filter["task"] = $tasks->ids();
			if(!sizeof($filter["task"]))
			{
				$filter["oid"] = 1;
			}
		}

		if($arr["content"])
		{
			$filter["content"] = "%".$arr["content"]."%";
		}
		$filter["limit"] = 500;
		if(sizeof($filter) < 5)
		{
			$ol = new object_list();
		}
		else
		{
			$ol = new object_list($filter);
		}

		foreach($ol->arr() as $o)
		{
			$cust = "";
			if($this->can("view" ,$o->prop("task.project")))
			{
				$p = obj($o->prop("task.project"));
				$c = $p->get_first_obj_by_reltype("RELTYPE_ORDERER");
				if(is_object($c))
				{
					$cust = $c->name();
				}
			}
			$t->define_data(array(
				"oid" => $o->id(),
				"content" => $o->prop("content"),
				"choose" => html::href(array(
					"caption" => t("Vali see"),
					"url" => $this->mk_my_orb("add_task_row_to_bill_row",
						array(
							"task_row" => $o->id(),
							"row" => $arr["row"],
						), "crm_bill"
					),
				)),
				"task" => $o->prop("task.name"),
				"project" => $o->prop("task.project.name"),
				"customer" => $cust,
			));
		}


		$htmlc->add_property(array(
			"name" => "table",
			"type" => "text",
			"value" => $t->draw(),
			"no_caption" => 1,
		));


		$htmlc->add_property(array(
			"name" => "submit2",
			"type" => "submit",
			"value" => t("Salvesta"),
			"caption" => t("Salvesta")
		));

		$htmlc->finish_output(array(
			"action" => "add_task_row_to_bill_row",
			"method" => "POST",
			"data" => $data
		));

		$content.= $htmlc->get_result();

		return $content;
	}

	/**
		@attrib name=unit_options_autocomplete_source all_args=1
	**/
	function unit_options_autocomplete_source($arr)
	{
		$ac = new autocomplete();
		$arr = $ac->get_ac_params($arr);
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"limit" => 100
		));
		$res = array();
		foreach($ol->arr() as $o)
		{
			$res[$o->id()] = $o->prop("unit_code");
		}

		return $ac->finish_ac($res);
	}

	function round_sum($sum)
	{
		$u = new user();
		$co = $u->get_current_company();
		$co = obj($co);
		if(is_object($co) && $co->prop("round"))
		{
			$round = (double)$co->prop("round");
			$min_stuff = $sum/$round - ($sum/$round - (int)($sum/$round));
			$min_diff = $sum - $min_stuff*$round;
			$max_diff = ($sum - ($min_stuff + 1) * $round)*-1;
			if($max_diff > $min_diff) $sum = $min_stuff*$round;
			else $sum = ($min_stuff+1)*$round;
		}
		 return $sum;
	}

	function callback_pre_save($arr)
	{
		if ("send_mail" === $this->use_group)
		{
			$arr["obj_inst"]->reset_pdf_files_cache(false);
		}

		if (!empty($this->_set_recv_date))
		{
			$arr["obj_inst"]->set_prop("bill_recieved", $this->_set_recv_date);
		}

		if (!$this->customer_object and isset($arr["request"]["address_meta"]))
		{
			$arr["obj_inst"]->set_customer_address("street", $arr["request"]["address_meta"]["street"]);
			$arr["obj_inst"]->set_customer_address("city", $arr["request"]["address_meta"]["city"]);
			$arr["obj_inst"]->set_customer_address("county", $arr["request"]["address_meta"]["county"]);
			$arr["obj_inst"]->set_customer_address("country", $arr["request"]["address_meta"]["country"]);
			$arr["obj_inst"]->set_customer_address("country_en", $arr["request"]["address_meta"]["country_en"]);
			$arr["obj_inst"]->set_customer_address("index", $arr["request"]["address_meta"]["index"]);
		}
	}

	/**
		@attrib name=preview all_args=1
	**/
	public function preview($arr)
	{
		return $this->show(array(
			"id" => $arr["id"],
		));
	}

	function _get_preview(&$arr)
	{
		if($arr["new"])
		{
			return PROP_IGNORE;
		}

		$arr["prop"]["value"] = $this->show(array(
			"id" => $arr["obj_inst"]->id(),
			"all_rows" => !empty($arr["all_rows"]),
			"pdf" => !empty($arr["pdf"]),
			"reminder" => !empty($arr["request"]["reminder"]),
			"handover" => !empty($arr["request"]["handover"])
		));
		return PROP_OK;
	}

	function _get_preview_add(&$arr)
	{
		if($arr["new"])
		{
			return PROP_IGNORE;
		}

		$show_pdf = (!empty($arr["request"]["pdf"]) or !empty($arr["pdf"]));
		$handover = (!empty($arr["request"]["handover"]) or !empty($arr["handover"]));

		if($arr["obj_inst"]->meta("rows_in_page"))
		{
			$page = 0;
		}
		else
		{
			$page = 0;//FIXME
		}

		if(array_sum(safe_array($arr["obj_inst"]->meta("rows_in_page"))) > 0)
		{
			$this->_preview_popup(array(
				"rows_in_page" => $arr["obj_inst"]->meta("rows_in_page"),
				"page" => $page,
				"id" => $arr["obj_inst"]->id(),
				"handover" => $handover
			));
		}

		if($page == 0)
		{
			exit ($this->show_add(array(
				"id" => $arr["obj_inst"]->id(),
				"pdf" => $show_pdf,
				"handover" => $handover
			)));
		}

		return PROP_OK;
	}

	/**
		@attrib name=_preview_popup
	/**/
	function _preview_popup($arr)
	{
		global $id, $rows_in_page, $page;
		extract($arr);
		$row = array_shift($rows_in_page);
		$between = explode("-", $row);
		$link = $this->mk_my_orb("_preview_popup", array("id" => $id, "rows_in_page" => $rows_in_page , "page" => ($page + 1)));
		if(array_sum($rows_in_page)){
			$popup =
			'<script name= javascript>window.open("'.$link.'","", "toolbar=no, directories=no, status=no, location=no, resizable=yes, scrollbars=yes, menubar=no, height=800, width=720")</script>';
			$not_last_page = 1;
		}
		die($this->show_add(array("id" => $id, "page" => $page, "between" => $between, "not_last_page" => $not_last_page,)) . $popup);
	}

	function collocate_rows($grp_rows)
	{
		$new_rows = array();
		foreach($grp_rows as $key => $grp_row)
		{
			while(true)
			{
				if(sizeof($grp_row) > 0) $row = array_shift($grp_row);
				else break;
				$new_line = 1;
				foreach($new_rows as $n_key => $new_row)
				{
					if($new_row["price"] == $row["price"] && ($new_row["comment"] == $row["comment"] || !$row["comment"])&& ($key == $new_row["key"]))
					{
						$new_rows[$n_key]["sum_wo_tax"] = $new_rows[$n_key]["sum_wo_tax"] + $row["sum_wo_tax"];
						$new_rows[$n_key]["tax"] = $new_rows[$n_key]["tax"] + $row["tax"];
						$new_rows[$n_key]["sum"] = $new_rows[$n_key]["sum"] + $row["sum"];
						$new_rows[$n_key]["tot_amt"] = $new_rows[$n_key]["tot_amt"] + $row["tot_amt"];
						$new_rows[$n_key]["tot_cur_sum"] = $new_rows[$n_key]["tot_cur_sum"] + $row["tot_cur_sum"];
						$new_line = 0;
						break;
					}
				}
				$row["key"] = $key;
				if($new_line) $new_rows[] = $row;
			}
		}
		$grp_rows = array();
		foreach($new_rows as $key => $new_row)
		{
			$grp_rows[$new_row["key"]][$new_row["price"].$new_row["comment"]] = $new_row;
		}
		return ($grp_rows);
	}

	private function implementor_vars($imp)
	{
		$vars = array();
		if ($this->can("view", $imp))
		{
			$impl = obj($imp);
			$vars["impl_name"] = $impl->name();
			$vars["impl_reg_nr"] = $impl->prop("reg_nr");
			$vars["impl_kmk_nr"] = $impl->prop("tax_nr");
			$vars["impl_fax"] = $impl->prop_str("telefax_id");
			$vars["impl_url"] = $impl->prop_str("url_id");
			$vars["impl_phone"] = $impl->prop_str("phone_id");
			$vars["imp_penalty"] = $impl->prop("bill_penalty_pct");
			$vars["impl_ou"] = $impl->prop("ettevotlusvorm.shortname");

			$impl_logo = $impl->get_first_obj_by_reltype("RELTYPE_ORGANISATION_LOGO");
			if ($impl_logo)
			{
				$this->vars["impl_logo_url"] = $impl_logo->instance()->get_url_by_id($impl_logo->id());
				$this->vars(array(
					"HAS_IMPL_LOGO" => $this->parse("HAS_IMPL_LOGO")
				));
			}

			$ba = "";
			foreach($impl->connections_from(array("type" => "RELTYPE_BANK_ACCOUNT")) as $c)
			{
				$acc = $c->to();
				$bank = obj();
				if ($this->can("view", $acc->prop("bank")))
				{
					$bank = obj($acc->prop("bank"));
				}
				$this->vars(array(
					"bank_name" => $bank->name(),
					"acct_no" => $acc->prop("acct_no"),
					"bank_iban" => $acc->prop("iban_code")
				));

				$ba .= $this->parse("BANK_ACCOUNT");
			}
			$this->vars(array(
				"BANK_ACCOUNT" => $ba
			));

			$logo_o = $impl->get_first_obj_by_reltype("RELTYPE_ORGANISATION_LOGO");
			if ($logo_o)
			{
				$logo_i = $logo_o->instance();
				$vars["logo"] = $logo_i->make_img_tag_wl($logo_o->id());
				$vars["logo_url"] = $logo_i->get_url_by_id($logo_o->id());
			}


			$has_country = "";
			if ($this->can("view", $impl->prop("contact")))
			{
				$ct = obj($impl->prop("contact"));
				$ap = array($ct->prop("aadress"));
				if ($ct->prop("linn"))
				{
					$vars["impl_city"] = $ct->prop_str("linn");
					$ap[] = $ct->prop_str("linn");
				}
				$aps = join(", ", $ap).html::linebreak();
				$aps .= $ct->prop_str("maakond");
				$aps .= " ".$ct->prop("postiindeks");
				$vars["impl_index"] = $ct->prop("postiindeks");
				$vars["impl_county"] = $ct->prop_str("maakond");
				$vars["impl_addr"] = $aps;
				$vars["impl_street"] = $ct->prop("aadress");

				if ($this->can("view", $ct->prop("riik")))
				{
					$riik = obj($ct->prop("riik"));
					$vars["impl_country"] = $riik->name();
					$vars["impl_phone"] = $riik->prop("area_code")." ".$impl->prop_str("phone_id");
					$this->vars(array("HAS_COUNTRY" => $this->parse("HAS_COUNTRY")));
				}
			}

			if ($this->can("view", $impl->prop("email_id")))
			{
				$mail = obj($impl->prop("email_id"));
				$vars["impl_mail"] = $mail->prop("mail");
			}

			$this->vars($vars);
		}
		return $vars;
	}

	function request_execute($o)
	{
		return $this->show(array(
			"id" => $o->id(),
			"return" => 1
		));
	}


	function show($arr)//kui igav hakkab, siis selle funktsiooni peaks nullist kirjutama
	{
		$this->bill = obj($arr["id"]);
		$stats = get_instance("applications/crm/crm_company_stats_impl");
		$currency = $this->bill->get_bill_currency_id();
		$tpl_suffix = $this->bill->prop("state") == crm_bill_obj::STATUS_OFFER ? "_offer" : "";

	//templeidi lugemine
		$tpl = "show{$tpl_suffix}";
		$lc = "et";
		if ($this->can("view", $this->bill->prop("language")))
		{
			$lc = $this->bill->prop("language.lang_acceptlang");
		}

		if(!empty($arr["reminder"]))
		{
			$tpl .= "_remind";
		}

		if(!empty($arr["handover"]))
		{
			$tpl .= "_remit";
		}

		if($arr["pdf"])
		{
			$tpl .= "_pdf";
			$tpl .= "_".$lc;
			if ($this->read_template("{$tpl}.tpl", true) === false)
			{
				if ($this->read_template("show_{$tpl_suffix}{$lc}.tpl", true) === false)
				{
					$this->read_template("show{$tpl_suffix}.tpl");
				}
			}
		}
		else
		{
			$tpl .= "_".$lc;
			if ($this->read_template($tpl.".tpl", true) === false)
			{
				$this->read_template("show{$tpl_suffix}.tpl");
			}
		}
	//templeidi l6pp

	//tellija
		$ord = obj();
		$ord_cur = obj();
		$orderer_contact_person_name = $orderer_contact_person_profession = $ord_index = $ord_country = "";
		if ($this->can("view", $this->bill->prop("customer")))
		{
			$ord = obj($this->bill->prop("customer"));
			if ($this->bill->prop("ctp_text"))
			{
				$orderer_contact_person_name = $this->bill->prop("ctp_text");
			}
			else
			{
				$orderer_contact_person = $this->bill->get_contact_person();

				if ($orderer_contact_person)
				{
					$orderer_contact_person_name = $orderer_contact_person->name();
					// get profession for contact_person
					$orderer_contact_person_profession = implode(", ", $orderer_contact_person->get_profession_names());
				}
			}

			$prop = "contact";
			if ($ord->class_id() == CL_CRM_PERSON)
			{
				$prop = "address";
			}

			if ($this->can("view", $ord->prop($prop)))
			{
				$ct = obj($ord->prop($prop));
				//riigi tlge, kui on inglise keeles
				if($this->bill->prop("language"))
				{
					$lo = obj($this->bill->prop("language"));
					$lc = $lo->prop("lang_acceptlang");
					if($lc === "en")
					{
						$ord_country = $this->bill->get_customer_address("country_en");
					}
				}
				else
				{
					$ord_country = $this->bill->get_customer_address("country");
				}
			}
		}
	//tellija l6pp

	//teostaja
		$imp_vars = $this->implementor_vars($this->bill->prop("impl"));
		$this->add_creator_vars();
		$this->add_contact_person_vars();
		$this->bill_vars();

		$bpct = $this->bill->prop("overdue_charge");
		if (!$bpct)
		{
			$bpct = $ord->prop("bill_penalty_pct");
			if (!$bpct)
			{
				$bpct = isset($imp_vars["impl_penalty"]) ? $imp_vars["impl_penalty"] : 0;
			}
		}
		//need enne vaja 2ra leida, sest hiljem subide jaoks ka vaja
		$ord_county = $this->bill->get_customer_address("county");

		$this->vars(array(
			"orderer_name" => $this->bill->get_customer_name(),
			"orderer_code" => $this->bill->get_customer_code(),
			"orderer_corpform" => $ord->prop("ettevotlusvorm.shortname"),
			"ord_penalty_pct" => number_format($bpct, 2),
			"ord_currency_name" => $this->bill->get_bill_currency_name(),
			"orderer_addr" => $this->bill->get_customer_address(),
			"orderer_city" => $this->bill->get_customer_address("city"),
			"orderer_county" => $ord_county,
			"orderer_index" => $this->bill->get_customer_address("index"),
			"orderer_country" => $ord_country,
			"orderer_street" => $this->bill->get_customer_address("street"),
			"orderer_kmk_nr" => $ord->prop("tax_nr"),
			"bill_no" => $this->bill->prop("bill_no"),
			"bill_date" => $this->bill->prop("bill_date"),
			"bill_due" => date("d.m.Y", $this->bill->prop("bill_due_date")),
			"payment_due_days" => $this->bill->prop("bill_due_date_days"),
			"bill_due" => date("d.m.Y", $this->bill->prop("bill_due_date")),
			"orderer_contact" => $orderer_contact_person_name,
			"orderer_contact_profession" => $orderer_contact_person_profession,
			"comment" => $this->bill->comment(),
			"overdue" => $this->bill->get_overdue_charge(),
			"bill_text" => $this->bill->get_bill_text(),
		));

		if($ord_country)
		{
			$this->vars(array("HAS_COUNTRY" => $this->parse("HAS_COUNTRY")));
		}

		if ($this->bill->prop("bill_due_date") > 200)
		{
			$this->vars(array(
				"HAS_DUE_DATE" => $this->parse("HAS_DUE_DATE")
			));
		}
		elseif ($this->bill->prop("bill_due_date_text") != "")
		{
			$this->vars(array(
				"NO_DUE_DATE" => $this->parse("NO_DUE_DATE")
			));
		}

		if($this->bill->prop("udef1")) $this->vars(array("userch1_checked" => $this->parse("userch1_checked")));
		if($this->bill->prop("udef2")) $this->vars(array("userch2_checked" => $this->parse("userch2_checked")));
		if($this->bill->prop("udef3")) $this->vars(array("userch3_checked" => $this->parse("userch3_checked")));
		if($this->bill->prop("udef4")) $this->vars(array("userch4_checked" => $this->parse("userch4_checked")));
		if($this->bill->prop("udef5")) $this->vars(array("userch5_checked" => $this->parse("userch5_checked")));

		if ($ord->prop("tax_nr") != "")
		{
			$this->vars(array(
				"HAS_KMK_NR" => $this->parse("HAS_KMK_NR")
			));
		}

		if ($orderer_contact_person_profession)
		{
			$this->vars(array(
				"HAS_ORDERER_CONTACT_PROF" => $this->parse("HAS_ORDERER_CONTACT_PROF")
			));
		}

		$rs = array();
		$sum_wo_tax = $tax = $sum = $tot_amt = $tot_cur_sum = 0;

		$agreement = $this->bill->meta("agreement_price");
		if(!empty($agreement["price"]) && !empty($agreement["name"]))
		{
			$agreement = array($agreement); // kui on vanast ajast jnud
		}

		if(!empty($agreement[0]["price"]) && isset($agreement[0]["name"]) && strlen($agreement[0]["name"]) > 0 )//kui kokkuleppehind on tidetud, siis rohkem ridu ei ole nha
		{
			$this->show_agreement_rows = 1;
			$bill_rows = $agreement;
		}
		else
		{
			$bill_rows = $this->bill->get_bill_rows_data();
		}
		$brows = $bill_rows; //moment ei tea miks see topelt tuleb... igaks juhuks ei vtnud maha... hiljem kib miski reset
		$grp_rows = array();
		$tax_rows = array();
		$_no_prod_idx = -1;
		$has_nameless_rows = 0;//miski muutuja , et kui see heks muutub, siis lisab liidab kik read kokku

		foreach($brows as $row)
		{
			if (!empty($row["is_oe"]))
			{
				continue;
			}

			$cur_tax = 0;
			$cur_sum = 0;
			$tax_rate = isset($row["tax"]) ? ((double) $row["tax"] / 100.0) : 0;

			//kole asi... idee selles, et kuskil seppikus ja sirelis jne on toodetega mingi teema, mida mujal ei kasutata, ja siis ridade kokku koondamine k2iks nagu vaid siis kui toode on sama, a kui ei ole, siis peaks ikka ka saama.... et ma siis olematule tootele kui kommentaari v2li on t2idetud, l2heb tooteks 1
			if (!$this->can("view", $row["prod"]))
			{
				if(!empty($row["comment"]))
				{
					$row["prod"] = 1;
				}
				else $row["prod"] = --$_no_prod_idx;
			}

			$cur_sum = $row["sum"];
			$cur_pr = $this->num($row["price"]);

			if ($tax_rate > 0)
			{
				// tax needs to be added
				$cur_tax = ($row["sum"] * $tax_rate);
			}
			else
			{
				// tax does not need to be added, tax free it seems
				$cur_tax = 0;
			}

			if (!empty($arr["all_rows"]))
			{
				$row["prod"] = gen_uniq_id();
			}

			$tax_rows[$tax_rate] = isset($tax_rows[$tax_rate]) ? ($tax_rows[$tax_rate] + $cur_tax) : $cur_tax;
			$unp = $row["price"].$row["comment"];
			$grp_rows[$row["prod"]][$unp]["sum_wo_tax"] = isset($grp_rows[$row["prod"]][$unp]["sum_wo_tax"]) ? ($grp_rows[$row["prod"]][$unp]["sum_wo_tax"] + $cur_sum) : $cur_sum;
			$grp_rows[$row["prod"]][$unp]["tax"] = isset($grp_rows[$row["prod"]][$unp]["tax"]) ? ($grp_rows[$row["prod"]][$unp]["tax"] + $cur_tax) : $cur_tax;
			$grp_rows[$row["prod"]][$unp]["sum"] = isset($grp_rows[$row["prod"]][$unp]["sum"]) ? ($grp_rows[$row["prod"]][$unp]["sum"] + $cur_tax + $cur_sum) : ($cur_tax+$cur_sum);
			$grp_rows[$row["prod"]][$unp]["tot_cur_sum"] = isset($grp_rows[$row["prod"]][$unp]["tot_cur_sum"]) ? ($grp_rows[$row["prod"]][$unp]["tot_cur_sum"] + $cur_sum) : $cur_sum;
			$grp_rows[$row["prod"]][$unp]["tot_amt"] = isset($grp_rows[$row["prod"]][$unp]["tot_amt"]) ? ($grp_rows[$row["prod"]][$unp]["tot_amt"] + $row["amt"]) : $row["amt"];
			$grp_rows[$row["prod"]][$unp]["unit"] = empty($row["unit"]) ? "" :$row["unit"];
			$grp_rows[$row["prod"]][$unp]["price"] = empty($row["price"]) ? "" :$row["price"];
			$grp_rows[$row["prod"]][$unp]["date"] = empty($row["date"]) ? "" :$row["date"];
			$grp_rows[$row["prod"]][$unp]["jrk"] = empty($row["jrk"]) ? "" :$row["jrk"];
			$grp_rows[$row["prod"]][$unp]["id"] = empty($row["id"]) ? "" :$row["id"];
			$grp_rows[$row["prod"]][$unp]["name"] = empty($row["name"]) ? "" :$row["name"];
			$grp_rows[$row["prod"]][$unp]["comment"] = empty($row["comment"]) ? "" :$row["comment"];
			$grp_rows[$row["prod"]][$unp]["orderer"] = empty($row["orderer"]) ? "" :$row["orderer"];

			if (empty($grp_rows[$row["prod"]][$unp]["comment"]))
			{
				$grp_rows[$row["prod"]][$unp]["comment"] = $row["comment"];
			}

			$sum_wo_tax += $cur_sum;
			$tax += $cur_tax;
			$sum += ($cur_tax+$cur_sum);
			$tot_amt += $row["amt"];
			$tot_cur_sum += $cur_sum;

			if(!strlen($row["comment"])>0)
			{
				$has_nameless_rows = 1;
			}
		}

		$fbr = reset($brows);

		//koondab sama nimega ja nimetud he hinnaga read kokku
		if(empty($arr["all_rows"]))
		{
			$grp_rows = $this->collocate_rows($grp_rows);
		}

		foreach($grp_rows as $prod => $grp_rowa)
		{
			foreach($grp_rowa as $key => $grp_row)
			{
				if (!empty($grp_row["comment"]))
				{
					$desc = $grp_row["comment"];
				}
				else
				if ($this->can("view", $prod))
				{
					$po = obj($prod);
					$desc = $po->comment();
				}
				else
				{
					$desc = $grp_row["name"];
				}

				//kui vaid hel real on nimi... et siis arve eeltvaates moodustuks nendest 1 rida
//				if(empty($arr["all_rows"]) && $has_nameless_rows)
//				{
//					if(!strlen($grp_row["comment"])>0 && $primary_row_is_set) break;
//					{
//						$grp_row["tot_cur_sum"] = $tot_cur_sum;
//						$grp_row["tot_amt"] = $tot_amt;
//						$primary_row_is_set = 1;
//					}
//				}
				$this->vars(array(
					"unit" => $this->bill->get_unit_name($grp_row["unit"]),
					"amt" => $stats->hours_format($grp_row["tot_amt"]),
					"price" => number_format($grp_row["tot_amt"] ? ($grp_row["tot_cur_sum"] / $grp_row["tot_amt"]) : 0,2,".", " "),
					"sum" => number_format($grp_row["tot_cur_sum"], 2, ".", " "),
					"row_tax" => number_format($grp_row["tax"], 2, ".", " "),
					"desc" => $desc,
					"date" => "",
					"row_orderer" => $grp_row["orderer"]
				));
				$rs[] = array("str" => $this->parse("ROW"), "date" => $grp_row["date"] , "jrk" => $grp_row["jrk"] , "id" => $grp_row["id"],);

			}
		}

		foreach($bill_rows as $row)
		{
			if (empty($row["is_oe"]))
			{
				continue;
			}
			$cur_tax = 0;
			$cur_sum = 0;

			$tax_rate = (double)$row["tax"] / 100.0;

			if ($tax_rate > 0)
			{
				// tax needs to be added
				$cur_sum = $row["sum"];
				$cur_tax = ($row["sum"] * $tax_rate);
				$cur_pr = $this->num($row["price"]);
			}
			else
			{
				// tax does not need to be added, tax free it seems
				$cur_sum = $row["sum"];
				$cur_tax = 0;
				$cur_pr = $this->num($row["price"]);
			}
			$name = $row["comment"];
			$tax_rows["$tax_rate"] += $cur_tax;
			$this->vars(array(
				"unit" => $this->bill->get_unit_name($row["unit"]),
				"amt" => $stats->hours_format($row["amt"]),
				"price" => number_format($cur_pr, 2, ".", " "),
				"sum" => number_format($cur_sum, 2, ".",  " "),
				"desc" => $name,
				"date" => $row["date"] ? "(".$row["date"].")" : ""
			));

			$rs[] = array("str" => $this->parse("ROW"), "date" => $row["date"] , "jrk" => $row["jrk"] , "id" => $grp_row["id"],);
			$sum_wo_tax += $cur_sum;
			$tax += $cur_tax;
			$sum += ($cur_tax+$cur_sum);
		}

		usort($rs, array($this, "__br_sort"));

		foreach($rs as $idx => $ida)
		{
			$rs[$idx] = $ida["str"];
		}

		$tax_rows_str = "";
		$there_is_tax_rate = 0;
		foreach($tax_rows as $tax_rate => $tax_amt)
		{
			if ($tax_rate > 0.005)
			{
				$there_is_tax_rate = $tax_rate;
			}
		}

		if($there_is_tax_rate)
		{
			foreach($tax_rows as $tax_rate => $tax_amt)
			{
	//			if ($tax_rate > 0)
	//			{
	//				$there_is_tax_rate = $tax_rate;
					$this->vars(array(
						"tax_rate" => floor($tax_rate*100.0),
						"tax" => number_format($tax_amt, 2),
						"tax_sum_from" => number_format($tax_amt/$tax_rate, 2),
					));
					$tax_rows_str .= $this->parse("TAX_ROW");
	//			}
			}
//			$tax_rate = $there_is_tax_rate;
		}
		$sigs = "";

		foreach((array)$this->bill->prop("signers") as $signer)
		{
			if (!$this->can("view", $signer))
			{
				continue;
			}
			$signer_p = obj($signer);
			$this->vars(array(
				"signer_person" => $signer_p->name()
			));
			$sigs .= $this->parse("SIGNATURE");
		}

		$sum = $this->round_sum($sum);
		$this->vars(array(
			"SIGNATURE" => $sigs,
			"TAX_ROW" => $tax_rows_str,
			"ROW" => join("", $rs),
			"total_wo_tax" => number_format($sum_wo_tax, 2,".", " "),
			"tax" => number_format($tax, 2,".", " "),
			"total" => number_format($sum, 2, ".", " "),
			"total_text" => aw_locale::get_lc_money_text($sum, obj($currency), $lc)
		));

	//k6ikidele muutujatele HAS_ sub
		foreach($this->vars as $var => $value)
		{
			if(!empty($value) && $this->is_template("HAS_".strtoupper($var)))
			{
				$this->vars(array("HAS_".strtoupper($var) => $this->parse("HAS_".strtoupper($var))));
			}
		}
		$res =  $this->parse();

	//kuvamine
		if(!empty($arr["pdf"]))
		{
			$conv = new html2pdf();
			if($conv->can_convert())
			{
				$pdf_name = $this->bill->name().".pdf";

				if($this->is_template("TITLE"))
				{
					$pdf_name = $this->parse("TITLE").".pdf";
				}

				if($arr["return"])
				{
					$res = $conv->convert(array(
						"source" => $res,
						"filename" => $pdf_name,
					));
					return $res;
				}
				else
				{
					$conv->gen_pdf(array(
						"source" => $res,
						"filename" => $pdf_name,
					));
				}
			}
		}

		if(!empty($arr["return"]))
		{
			return $res;
		}

		if (!empty($_GET["openprintdialog"]))
		{
			$res .= "<script language='javascript'>setTimeout('window.close()',10000);window.print();if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.close(); }</script>";
		}

		if (!empty($_GET["openprintdialog_b"]))
		{
			$url = aw_url_change_var("group", "preview_add", aw_url_change_var("openprintdialog", 1));
			$res .= "<script language='javascript'>setTimeout('window.location.href=\"$url\"',10000);window.print();if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.location.href='$url'; }</script>";
		}
		die($res);
	}

	private function add_creator_vars()
	{
		$creator = $this->bill->get_the_person_who_made_this_fucking_thing();
		if(is_object($creator))
		{
			$creator_vars = $creator->get_data();
			$define = array();
			foreach($creator_vars as $var => $value)
			{
				$define["creator.".$var] = $value;
			}
			$this->vars($define);
		}
	}

	private function add_contact_person_vars()
	{
		$contact_person = $this->bill->get_contact_person();
		if(is_object($contact_person))
		{
			$contact_vars = $contact_person->get_data();
			$define = array();
			foreach($contact_vars as $var => $value)
			{
				$define["contact_person.".$var] = $value;
			}
			$this->vars($define);
		}
	}


	function bill_vars()
	{
		$bill_data = $this->bill->get_data();
		$this->vars($bill_data);
		if($this->bill->prop("signature_type") == 2)
		{
			$this->vars(array(
				"IMPL_DIG_SIGNATURE" => $this->parse("IMPL_DIG_SIGNATURE"),
				"ORD_DIG_SIGNATURE" => $this->parse("ORD_DIG_SIGNATURE"),
			));
		}
		else
		{
			$this->vars(array(
				"IMPL_SIGNATURE" => $this->parse("IMPL_SIGNATURE"),
				"ORD_SIGNATURE" => $this->parse("ORD_SIGNATURE"),
			));
		}
	}

	function __br_sort($a, $b)
	{
		$a_date = $a["date"];
		$b_date = $b["date"];
		list($a_d, $a_m, $a_y) = explode(".", $a_date);
		list($b_d, $b_m, $b_y) = explode(".", $b_date);
		$a_tm = mktime(0,0,0, $a_m, $a_d, $a_y);
		$b_tm = mktime(0,0,0, $b_m, $b_d, $b_y);
		if(isset($a["is_oe"]) && isset($b["is_oe"]) && !(($a["is_oe"] - $b["is_oe"]) == 0))
		{
			return $a["is_oe"]- $b["is_oe"];
		}
		return  $a["jrk"] < $b["jrk"] ? -1 :
			($a["jrk"] > $b["jrk"] ? 1:
				($a_tm >  $b_tm ? 1:
					($a_tm == $b_tm ? ($a["id"] > $b["id"] ? 1 : -1): -1)
				)
			);
	}

	function show_add($arr)//see ka jube l2bu... et kui igav hakkab, siis nullist kirjutada
	{
		$this->stats = new crm_company_stats_impl();
		$this->bill = obj($arr["id"]);
		$tpl_suffix = $this->bill->prop("state") == crm_bill_obj::STATUS_OFFER ? "_offer" : "";

		$agreement_prices = $this->bill->meta("agreement_price");
		if(!empty($agreement_prices["price"]) && !empty($agreement_prices["name"]))
		{
			$agreement_prices = array($agreement_prices); // kui on vanast ajast jnud
		}

		if(!empty($agreement_prices[0]["price"]) && isset($agreement_prices[0]["name"]) && strlen($agreement_prices[0]["name"]) > 0 )//kui kokkuleppehind on tidetud, siis rohkem ridu ei ole nha
		{
			$bill_rows = $agreement_prices;
		}
		else
		{
			$bill_rows = $this->bill->get_bill_rows_data();
		}

		//thja kirjeldusega read vlja
		foreach($bill_rows as $key => $val)
		{
			if(!(strlen($val["name"]) > 0))
			{
				unset($bill_rows[$key]);
			}
		}

	//templeidi valik
		$tpl = "show{$tpl_suffix}_add";
		$lc = "et";
		if ($this->can("view", $this->bill->prop("language")))
		{
			$lo = obj($this->bill->prop("language"));
			$lc = $lo->prop("lang_acceptlang");
		}

		if(!empty($arr["handover"]))
		{
			$tpl = "show_remit";
		}

		if(!empty($arr["pdf"]))
		{
			$tpl .= "_pdf";
			$tpl .= "_".$lc;
			if ($this->read_template($tpl.".tpl", true) === false)
			{
				if ($this->read_template("show{$tpl_suffix}_add_".$lc.".tpl", true) === false)
				{
					$this->read_template("show{$tpl_suffix}_add.tpl");
				}
			}
		}
		else
		{
			$tpl .= "_".$lc;
			if ($this->read_template($tpl.".tpl", true) === false)
			{
				$this->read_template("show{$tpl_suffix}_add.tpl");
			}
		}
	//templeidi valik l6pp

		$ord = obj();
		$currency = $this->bill->get_bill_currency_id();
		$cur = obj($currency);
		$ord_addr = $ord_ct = "";

		if ($this->can("view", $this->bill->prop("customer")))
		{
			$ord = obj($this->bill->prop("customer"));
			$_ord_ct = $ord->prop("firmajuht");
			if ($this->can("view", $_ord_ct))
			{
				$ct = obj($_ord_ct);
				$ord_ct = $ct->name();
			}
			if ($this->can("view", $ord->prop("contact")))
			{
				$ct = obj($ord->prop("contact"));
				$ap = array($ct->prop("aadress"));
				if ($ct->prop("linn"))
				{
					$ap[] = $ct->prop_str("linn");
				}
				$aps = join(", ", $ap).html::linebreak();
				$aps .= $ct->prop_str("maakond");
				$aps .= " ".$ct->prop("postiindeks");
				$ord_addr = $aps;//$ct->name()." ".$ct->prop("postiindeks");
			}
		}

		$impl_vars = $this->implementor_vars($this->bill->prop("impl"));
		$this->add_creator_vars();
		$this->add_contact_person_vars();
		$this->bill_vars();

		$bpct = $this->bill->prop("overdue_charge");
		if (!$bpct)
		{
			$bpct = $ord->prop("bill_penalty_pct");
			if (!$bpct)
			{
				$bpct = isset($impl_vars["impl_penalty"]) ? $impl_vars["impl_penalty"] : 0;
			}
		}

		$this->vars(array(
			"orderer_name" => $ord->name(),
			"orderer_corpform" => $ord->prop("ettevotlusvorm.shortname"),
			"ord_currency_name" => $this->bill->get_bill_currency_name(),
			"ord_penalty_pct" => number_format($bpct, 2),
			"orderer_addr" => $ord_addr,
			"orderer_kmk_nr" => $ord->prop("tax_nr"),
			"bill_no" => $this->bill->prop("bill_no"),
			"bill_date" => $this->bill->prop("bill_date"),
			"payment_due_days" => $this->bill->prop("bill_due_date_days"),
			"bill_due" => date("d.m.Y", $this->bill->prop("bill_due_date")),
			"orderer_contact" => $ord_ct,
			"time_spent_desc" => $this->bill->prop("time_spent_desc")
		));


		$rs = array();
		$this->sum_wo_tax = 0;
		$this->tax = 0;
		$this->sum = 0;

		$grouped_rows = array();
		foreach($bill_rows as $row)
		{
			$grouped_rows[$row["comment"]][] = $row;
		}

		if($this->is_template("GROUP_ROWS"))
		{
			$GR = "";
			foreach($grouped_rows as $capt => $crows)
			{
				$rs = $this->parse_preview_add_rows($crows);
				$this->vars(array(
					"uniter" => $capt,
					"ROW" => join("", $rs),
				));
				$GR.= $this->parse("GROUP_ROWS");
			}
			$this->vars(array(
				"GROUP_ROWS" => $GR,
			));
		}
		else
		{
			$rs = $this->parse_preview_add_rows($bill_rows);
		}
		$sigs = "";

		foreach((array)$this->bill->prop("signers") as $signer)
		{
			if (!$this->can("view", $signer))
			{
				continue;
			}
			$signer_p = obj($signer);
			$this->vars(array(
				"signer_person" => $signer_p->name()
			));
			$sigs .= $this->parse("SIGNATURE");
		}

		if(empty($arr["not_last_page"]))
		{
			$this->vars(array("tot_amt" => $this->stats->hours_format($this->tot_amt)));
			$total_ = $this->parse("TOTAL");
		}

		$page_no = empty($arr["page"]) ? 1 : $arr["page"] + 1;

		if(!($page_no > 1))
		{
			$_header = $this->parse("HEADER");
		}

		//$sum_wo_tax = $this->round_sum($sum_wo_tax);
		$sum = $this->round_sum($this->sum);

		$this->vars(array(
			"SIGNATURE" => $sigs,
			"ROW" => join("", $rs),
			"TOTAL" => $total_,
			"HEADER" => $_header,
			"total_wo_tax" => number_format($this->sum_wo_tax, 2,".", " "),
			"tax" => number_format($this->tax, 2,"." , " "),
			"total" => number_format($this->sum, 2,".", " "),
			"total_text" => aw_locale::get_lc_money_text($this->sum, $cur, $lc),
			"tot_amt" => $this->stats->hours_format($this->tot_amt),
			"comment" => nl2br($this->bill->comment()),
			"page_no" => $page_no
		));

 	//k6ikidele muutujatele HAS_ sub
		foreach($this->vars as $var => $value)
		{
			if(!empty($value) && $this->is_template("HAS_".strtoupper($var)))
			{
				$this->vars(array("HAS_".strtoupper($var) => $this->parse("HAS_".strtoupper($var))));
			}
		}
		$res =  $this->parse();

		if (!empty($_GET["openprintdialog"]))
		{
			$res .= "<script language='javascript'>setTimeout('window.close()',10000);window.print();window.close();if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.close(); }</script>";
		}

		if (!empty($_GET["openprintdialog_b"]))
		{
			$url = aw_url_change_var("group", "preview", aw_url_change_var("openprintdialog", 1));
			$res .= "<script language='javascript'>setTimeout('window.location.href=\"$url\"',10000);window.print();if (navigator.userAgent.toLowerCase().indexOf('msie') == -1) {window.location.href='$url'; }</script>";
		}

		if(!empty($arr["pdf"]))
		{
			$conv = new html2pdf();
			if($conv->can_convert())
			{
				if(!empty($arr["return"]))
				{
					$res = $conv->convert(array(
						"source" => $res,
						"filename" => $this->bill->name()."_".t("lisa").".pdf",
					));
					return $res;
				}
				else
				{
					$conv->gen_pdf(array(
						"source" => $res,
						"filename" => $this->bill->name()."_".t("lisa").".pdf",
					));
				}

			}
		}
		return $res;
	}

	private function parse_preview_add_rows($bill_rows)
	{
		$rs = array();
		$tot_cur_sum = $tot_amt = 0;

		foreach($bill_rows as $key => $row)
		{
			$row_data = array();
			if(empty($row["task_row_id"]) && isset($row["task_rows"]) && is_array($row["task_rows"]))
			{
				$row["task_row_id"] = reset($row["task_rows"]);
			}

			$row_data["task_row_id"] = empty($row["task_row_id"]) ? (isset($row["id"]) ? $row["id"] : "") : $row["task_row_id"];
			$row_data["orderer"] = isset($row["orderer"]) ? $row["orderer"] : null;
			if (!empty($row["is_oe"]))
			{
				continue;
			}
			$tax_rate = isset($row["tax"]) ? ((double) $row["tax"] / 100.0) : 0;
			$cur_tax = 0;
			$cur_sum = 0;

			if ($tax_rate)
			{
				// tax needs to be added
				$cur_sum = $row["sum"];
				$cur_tax = ($row["sum"] * $tax_rate);
				$cur_pr = $this->num($row["price"]);
			}
			else
			{
				// tax does not need to be added, tax free it seems
				$cur_sum = $row["sum"];
				$cur_tax = 0;
				$cur_pr = $this->num($row["price"]);
			}

			if (!empty($arr["between"]) && !($key+1 >= $arr["between"][0] && $key+1 <= $arr["between"][1]))
			{
			}
			else
			{
				$this->vars($row_data);
				$this->vars(array(
					"unit" => $this->bill->get_unit_name($row["unit"]),
					"amt" => $this->stats->hours_format($row["amt"]),
					"price" => number_format($row["price"], 2,".", " "),
					"sum" => number_format($cur_sum, 2,"." , " "),
					"desc" => nl2br($row["name"]),
					"date" => isset($row["date"]) ? $row["date"] : "",
					"row_orderer" => isset($row["orderer"]) ? $row["orderer"] : "",
					"comment" => isset($row["comment"]) ? $row["comment"] : "",
					"oid" => isset($row["oid"]) ? $row["oid"] : "",
					"row_tax" => $cur_tax
				));
				$rs[] = array(
					"str" => $this->parse("ROW"),
					"date" => $row["date"] ,
					"jrk" => isset($row["jrk"]) ? $row["jrk"] : "",
					"id" => isset($row["id"]) ? $row["id"] : ""
				);
			}

			$this->sum_wo_tax += $cur_sum;
			$this->tax += $cur_tax;
			$this->sum += ($cur_tax+$cur_sum);
			$unit = $row["unit"];
			$tot_amt += $row["amt"];
			$tot_cur_sum += $cur_sum;
		}

		foreach($bill_rows as $key => $row)
		{
			if (!$this->bill->meta("show_oe_add") || !$row["is_oe"])
			{
				continue;
			}
			$tax_rate = (double)$row["tax"] / 100.0;
			$cur_tax = 0;
			$cur_sum = 0;

			if ($tax_rate)
			{
				// tax needs to be added
				$cur_sum = $row["sum"];
				$cur_tax = ($row["sum"] * $tax_rate);
				$cur_pr = $this->num($row["price"]);

			}
			else
			{
				// tax does not need to be added, tax free it seems
				$cur_sum = $row["sum"];
				$cur_tax = 0;
				$cur_pr = $this->num($row["price"]);
			}
			$this->vars(array(
				"unit" => $this->bill->get_unit_name($row["unit"]),
				"amt" => $this->stats->hours_format($row["amt"]),
				"price" => number_format($cur_pr, 2,".", " "),
				"sum" => number_format($cur_sum, 2, ".", " "),
				"desc" => $row["name"],
				"date" => $row["date"],
				"row_orderer" => $row["orderer"],
				"row_tax" => $cur_tax,
			));

			$rs[] = array("str" => $this->parse("ROW"), "date" => $row["date"] , "jrk" => $row["jrk"], "id" => $row["id"]);
			$this->sum_wo_tax += $cur_sum;
			$this->tax += $cur_tax;
			$this->sum += ($cur_tax+$cur_sum);
		}

		$this->tot_amt = $tot_amt;

		usort($rs, array($this, "__br_sort"));
		foreach($rs as $idx => $ida)
		{
			$rs[$idx] = $ida["str"];
		}
		return $rs;
	}

	function get_bill_sum($b, $type = BILL_SUM)//mujal v6idakse veel classbasest v2lja kutsuda... ei viitsi k6ike yles otsida, muidu v6iks 2ra kustutada
	{
		return $b->get_bill_sum($type);

	}

	function _save_rows($arr)
	{
		if(!isset($arr["request"]["rows"]))
		{
			$arr["request"]["rows"] = array();
		}

		foreach(safe_array($arr["request"]["rows"]) as $oid => $row)
		{
			if (!$this->can("edit", $oid))
			{
				$o = $arr["obj_inst"]->add_row();
			}
			else
			{
				$o = obj($oid);
			}

			if(!$this->can("view", $row["unit"]))
			{
				$uo = obj();
				$uo->set_class_id(CL_UNIT);
				$uo->set_name($row["unit"]);
				$uo->set_prop("unit_code", $row["unit"]);
				$uo->set_parent(get_current_company()->id());
				$uo->save();
				$unit = $uo->id();
			}
			else
			{
				$unit = $row["unit"];
			}

			if(!empty($row["quality"]))
			{
				$o->create_brother($row["quality"]);
			}

			$o->set_prop("name", $row["name"]);
			$o->set_prop("comment", $row["comment"]);
			$o->set_prop("date", $row["date"]);
			$o->set_prop("unit", $unit);
			$o->set_meta("jrk", $row["jrk"]);

			if (isset($row["price"]))
			{
				$o->set_prop("price", $row["price"]);
			}

			if (isset($row["amt"]))
			{
				$o->set_prop("amt", $row["amt"]);
			}

			if (isset($row["prod"]))
			{
				$o->set_prop("prod", $row["prod"]);
			}

			if (isset($row["has_tax"]))
			{
				$o->set_prop("has_tax", (int) $row["has_tax"]);

				if ((int) $row["has_tax"])
				{
					$o->set_prop("tax", $o->get_row_tax(1));
				}
			}

			if (isset($row["person"]))
			{
				$o->set_prop("people", $row["person"]);
			}

			$o->save();
		}

// agreement_price is DEPRECATED. voldemar 23 nov 2010
$agreement_price = array(); if(isset($arr["request"]["agreement_price"]) and is_array($arr["request"]["agreement_price"])) { 		$this->set_current_settings(); foreach($arr["request"]["agreement_price"] as $key => $agreement_price) {$arr["request"]["agreement_price"][$key]["comment"] = $agreement_price["name"]; if ($this->crm_settings && !$arr["request"]["agreement_price"][$key]["prod"]) { $arr["request"]["agreement_price"][$key]["prod"] = $this->crm_settings->prop("bill_def_prod");} $arr["request"]["agreement_price"][$key]["sum"] = str_replace("," , "." , $arr["request"]["agreement_price"][$key]["price"])*str_replace("," , "." , $arr["request"]["agreement_price"][$key]["amt"]); if(!$arr["request"]["agreement_price"][$key]["price"] && !(strlen($arr["request"]["agreement_price"][$key]["name"]) > 1) && empty($arr["request"]["agreement_price"][$key]["atm"])) { unset($arr["request"]["agreement_price"][$key]); } if(isset($arr["request"]["agreement_price"][$key]["prod"])) { $tmp = explode("(", $arr["request"]["agreement_price"][$key]["prod"]); $tmp2 = explode(")", $tmp[count($tmp)-1]); $prod = $tmp2[0]; $arr["request"]["agreement_price"][$key]["prod"] = $prod; } if(!empty($arr["request"]["agreement_price"][$key]["has_tax"])) { $arr["request"]["agreement_price"][$key]["tax"] = $this->crm_settings->get_default_vat(); } } $agreement_price = $arr["request"]["agreement_price"]; } $arr["obj_inst"]->set_meta("agreement_price", $agreement_price); $arr["obj_inst"]->save();
///////////////


		////TODO: kas vaja salvestada? v6i ainult agreemendi jaoks?
		$arr["obj_inst"]->save();
	}

	/**
		@attrib name=add_row
		@param id required type=int acl=edit
		@param retu optional
	**/
	function add_row($arr)
	{
		$bill = obj($arr["id"]);
		$rows = $bill->get_bill_rows_data();
		$jrk = 0;
		foreach($rows as $row)
		{
			if($row["jrk"] > $jrk-10) $jrk = $row["jrk"]+10;
		}
		$row = $bill->add_row();
		$row->set_meta("jrk" , $jrk);
		$row->save();

		$bill->set_prop("bill_trans_date", time());
		$bill->save();
		if(empty($arr["retu"]))
		{
			die($row->id());
		}
		return $arr["retu"];
	}

	/**
		@attrib name=create_bill
	**/
	function create_bill($arr)
	{
		$bill = obj($arr["id"]);
		$this->set_current_settings();
		$ti = new task();
		foreach(safe_array($arr["sel"]) as $task_id)
		{
			// add all rows that are not yet billed
			foreach($ti->get_task_bill_rows(obj($task_id)) as $row)
			{
				$br = obj();
				$br->set_class_id(CL_CRM_BILL_ROW);
				$br->set_parent($bill->id());
				$br->set_prop("name", $row["name"]);
				$br->set_prop("amt", $row["amt"]);
				$br->set_prop("prod", $row["prod"]);
				$br->set_prop("price", $row["price"]);
				$br->set_prop("unit", $row["unit"]);
				$br->set_meta("jrk", $row["jrk"]);
				$br->set_prop("is_oe", $row["is_oe"]);
				$br->set_prop("has_tax", $row["has_tax"]);
				$br->set_prop("date", date("d.m.Y", $row["date"]));
				// get default prod

				if ($this->crm_settings)
				{
					$br->set_prop("prod", $this->crm_settings->prop("bill_def_prod"));
				}

				if($row["has_tax"])
				{
					$br->set_prop("tax", $br->get_row_tax(1));
				}
				$br->save();

				$br->connect(array(
					"to" => $task_id,
					"type" => "RELTYPE_TASK"
				));

				if ($row["row_oid"])
				{
					$br->connect(array(
						"to" => $row["row_oid"],
						"type" => "RELTYPE_TASK_ROW"
					));
					$tr = obj($row["row_oid"]);
					$tr->set_prop("bill_id", $bill->id());
					$tr->save();
				}

				$bill->connect(array(
					"to" => $br->id(),
					"type" => "RELTYPE_ROW"
				));
			}
		}
		return $arr["post_ru"];
	}

	function callback_generate_scripts($arr)
	{
		$scripts = "";
		$id = $arr["obj_inst"]->id();

		if ("send_mail" === $this->use_group)
		{
			$scripts .= <<<ENDSCRIPT
function crm_bill_refresh_mail_text_changes() {
	// subject
	$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'ajax_parse_mail_text', id: '{$id}', text: document.getElementById('bill_mail_subj').value}, function (html) {
	x=document.getElementById('sendmail_subject_text_element');
	x.innerHTML=html;});

	// body
	$.get('/automatweb/orb.aw', {class: 'crm_bill', action: 'ajax_parse_mail_text', id: '{$id}', text: document.getElementById('bill_mail_ct').value}, function (html) {
	x=document.getElementById('sendmail_body_text_element');
	x.innerHTML=html;});
}
ENDSCRIPT;
		}
		elseif ("rows" === $this->use_group)
		{
			$scripts .= <<<ENDSCRIPT
function crm_bill_add_row()
{
	$.get("/automatweb/orb.aw", {class: "crm_bill", action: "add_row", id: "{$id}"}, function (html) {
		reload_layout("bill_rows_container");
	});
}

function crm_bill_edit_row(id)
{
	$.get("/automatweb/orb.aw", {class: "crm_bill", action: "get_row_change_fields", id: id, field: "name"}, function (html) {
		x=document.getElementById("row_"+id+"_name");
		x.innerHTML=html;});
	$.get("/automatweb/orb.aw", {class: "crm_bill", action: "get_row_change_fields", id: id, field: "unit"}, function (html) {
		x=document.getElementById("row_"+id+"_unit");
		x.innerHTML=html;});
	$.get("/automatweb/orb.aw", {class: "crm_bill", action: "get_row_change_fields", id: id, field: "prod"}, function (html) {
		x=document.getElementById("row_"+id +"_prod");
		x.innerHTML=html;});
	$.get("/automatweb/orb.aw", {class: "crm_bill", action: "get_row_change_fields", id: id, field: "person"}, function (html) {
		x=document.getElementById("row_" + id + "_person");
		x.innerHTML=html;});
	$.get("/automatweb/orb.aw", {class: "crm_bill", action: "get_row_change_fields", id: id, field: "change"}, function (html) {
		x=document.getElementById("row_" + id + "_change");
		x.innerHTML=html;});
	$.get("/automatweb/orb.aw", {class: "crm_bill", action: "get_row_change_fields", id: id, field: "has_tax"}, function (html) {
		x=document.getElementById("row_" + id + "_has_tax");
		x.innerHTML=html;});
	$.get("/automatweb/orb.aw", {class: "crm_bill", action: "get_row_change_fields", id: id, field: "project"}, function (html) {
		x=document.getElementById("row_" + id + "_project");
		x.innerHTML=html;});
}
ENDSCRIPT;
		}

		$url = $this->mk_my_orb("get_comment_for_prod");
		$scripts .= <<<ENDSCRIPT

var date_day_el = aw_get_el("bill_date[day]")
var date_month_el = aw_get_el("bill_date[month]")
var date_year_el = aw_get_el("bill_date[year]")
var date_day = date_day_el.value
var date_month = date_month_el.value
var date_year = date_year_el.value
var date_trans_day_el = aw_get_el("bill_trans_date[day]")
var date_trans_month_el = aw_get_el("bill_trans_date[month]")
var date_trans_year_el = aw_get_el("bill_trans_date[year]")
$.timer(200, function (timer) {
	if(date_day_el.value != date_day || date_month_el.value != date_month || date_year_el.value != date_year)
	{
		date_day = date_day_el.value
		date_month = date_month_el.value
		date_year = date_year_el.value
		date_trans_day_el.value = date_day
		date_trans_month_el.value = date_month
		date_trans_year_el.value = date_year
	}
});

function upd_notes()
{
	set_changed();
	//aw_do_xmlhttprequest("{$url}&prod="+document.changeform.gen_prod.options[document.changeform.gen_prod.selectedIndex].value, notes_fetch_callb);
}

function notes_fetch_callb()
{
	if (req.readyState == 4)
	{
		// only if "OK"
		if (req.status == 200)
		{
			if (req.responseXML)
			{
				response = req.responseXML.documentElement;
				items = response.getElementsByTagName("item");

				if (items.length > 0 && items[0].firstChild != null)
				{
					value = items[0].firstChild.data;
					document.changeform.notes.value = value;
				}
			}
		}
		else
		{
			alert("There was a problem retrieving the XML data:\\n" + req.statusText);
		}
	}
}
var chk_status = 1;

function selall(element)
{
	$("form input[id^="+element+"]").each(function(){
	  this.checked = chk_status;
	});
	chk_status = chk_status ? 0 : 1;
}

ENDSCRIPT;

		return $scripts;
	}

	/**
		@attrib name=ajax_parse_mail_text all_args=1
	**/
	// params id and text
	function ajax_parse_mail_text($arr)
	{
		try
		{
			$this_o = obj($arr["id"], array(), CL_CRM_BILL);
			echo nl2br($this_o->parse_mail_text($arr["text"]));
		}
		catch (Exception $e)
		{
		}
		exit;
	}

	/**
		@attrib name=get_comment_for_prod
		@param prod optional
	**/
	function get_comment_for_prod($arr)
	{
		header("Content-type: text/xml");
		$xml = "<?xml version=\"1.0\" encoding=\"".aw_global_get("charset")."\" standalone=\"yes\"?>\n<response>\n";


		$empty = $xml."<item></item></response>";
		if (!$arr["prod"])
		{
			die( $empty);
		}

		$ol = new object_list(array(
			"class_id" => CL_SHOP_PRODUCT,
			"oid" => $arr["prod"]
		));
		if (!$ol->count())
		{
			die($empty);
		}

		foreach($ol->arr() as $o)
		{
			$xml .= "<item>".$o->comment()."</item>";
		}
		$xml .= "</response>";
		die($xml);
	}

	function _get_bill_tb(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_save_button();
		$this->add_sendmail_menu($arr);
		$this->add_print_menu($arr);

		return PROP_OK;
	}

	function set_current_settings()
	{
		if(!isset($this->crm_settings))
		{
			$seti = get_instance(CL_CRM_SETTINGS);
			$this->crm_settings = $seti->get_current_settings();
		}
	}

	/**
		@attrib name=delete_rows
	**/
	function delete_rows($arr)
	{
		object_list::iterate_list($arr["sel_rows"], "delete");
		return $arr["post_ru"];
	}

	/**
		@attrib name=writeoff_rows
	**/
	function writeoff_rows($arr)
	{
		foreach($arr["sel_rows"] as $row_id)
		{
			$ro = obj($row_id);
			if($ro->prop("writeoff"))
			{
				$ro->set_prop("writeoff" , 0);
			}
			else
			{
				$ro->set_prop("writeoff" , 1);
			}
			$ro->save();
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=remove_rows_from_bill
	**/
	function remove_rows_from_bill($arr)
	{
		$bill = obj($arr["id"]);
		$bill->remove_tasks($arr["sel"]);
		return $arr["post_ru"];
	}


	function _init_bill_task_list($t)
	{
		$t->add_fields(array(
			"br" => t("Arve rida"),
			"oid" =>  t("ID"),
			"name" => t("Tegevus"),
			"project" => t("Projekt"),
			"time" => t("Aeg"),
			"work" => t("T&ouml;&ouml;"),
			"hrs" => t("Tunde"),
			"cust_hours" =>  t("Tunde kliendile"),
			"price" => t("Hind"),
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
	}

	function _get_bill_task_list(&$arr)
	{
		if($arr["new"])
		{
			return PROP_IGNORE;
		}

		$t = $arr["prop"]["vcl_inst"];
		$t->set_sortable(false);
		$this->_init_bill_task_list($t);
		$stats = get_instance("applications/crm/crm_company_stats_impl");

		$tasks = $arr["obj_inst"]->bill_tasks();
		$rows = $arr["obj_inst"]->bill_task_rows_data();
		$task_2_bill_rows = $arr["obj_inst"]->task_row_2_bill_rows();
		$rows_data = array();

		foreach($rows as $data)
		{
			$rows_data[$data["task"]][] = $data;
		}


		$ti = get_instance(CL_TASK);
		foreach($tasks->arr() as $task)
		{
			$task_hours = $task_hours_customer = $task_sum = 0;
			$hour_price = $task->prop("hr_price");
			foreach($rows_data[$task->id()] as $d)
			{
				$customer_time = $d["time_to_cust"];// ? $d["time_to_cust"] : $d["time_real"];
				$task_hours_customer += $customer_time;
				$task_hours += $d["time_real"];
				$task_sum += $customer_time * $hour_price;
			}


			$t->define_data(array(
				"name" => html::obj_change_url($task),
				"hrs" => $stats->hours_format($task_hours),
				"price" => $task_sum,
				"oid" => $task->id(),
				"cust_hours" => $task_hours_customer,
				"project" => join(", " , $task->get_projects()->names()),
			));

			foreach($rows_data[$task->id()] as $d)
			{
				$customer_time = $d["time_to_cust"];// ? $d["time_to_cust"] : $d["time_real"];
				$t->define_data(array(
					"work" => $d["content"],
					"hrs" => $stats->hours_format($d["time_real"]),
					"price" => number_format($customer_time * $hour_price, 2,".", " "),
					"oid" => $d["oid"],
					"cust_hours" => $stats->hours_format($d["time_to_cust"]),
					"time" => date("d.m.Y" , $d["date"]),
					"br" => join(", " , $task_2_bill_rows[$d["oid"]]),
				));
			}
			unset($rows_data[$task->id()]);
		}


		foreach($rows_data as $task_id => $rows)
		{
			$task = $task_hours = $task_hours_customer = $task_sum = 0;

			if($this->can("view" , $task_id))
			{
				$task = obj($task_id);
			}
			if(is_object($task))
			{
				$hour_price = $task->prop("hr_price");
			}
			foreach($rows as $d)
			{
				$customer_time = $d["time_to_cust"];
				$task_hours_customer += $customer_time;
				$task_hours += $d["time_real"];
				$task_sum += $customer_time * $hour_price;
			}
			if(is_object($task))
			{
				$t->define_data(array(
					"name" => html::obj_change_url($task),
					"hrs" => $stats->hours_format($task_hours),
					"price" => $task_sum,
					"oid" => $task->id(),
					"cust_hours" => $task_hours_customer,
					"project" => join(", " , $task->get_projects()->names()),
				));
			}
			else
			{
				$t->define_data(array(
					"name" => "",
					"hrs" => $stats->hours_format($task_hours),
					"cust_hours" => $task_hours_customer,
				));
			}
			foreach($rows_data[$task->id()] as $d)
			{
				$customer_time = $d["time_to_cust"];
				$t->define_data(array(
					"work" => $d["content"],
					"hrs" => $stats->hours_format($d["time_real"]),
					"price" => number_format($customer_time * $hour_price, 2,".", " "),
					"oid" => $d["oid"],
					"cust_hours" => $stats->hours_format($d["time_to_cust"]),
					"time" => date("d.m.Y" , $d["date"]),
				));
			}
		}

		return PROP_OK;
	}

	function _get_billt_tb(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			"name" => "remove_from_bill",
			"img" => "delete.gif",
			"tooltip" => t("Eemalda arve k&uuml;ljest"),
			"confirm" => t("Oled kindel et soovid read eemaldada?"),
			"action" => "remove_rows_from_bill"
		));

		return PROP_OK;
	}

	function do_db_upgrade($table, $field, $q, $err)
	{
		switch($field)
		{
			case "aw_customer_name":
			case "aw_customer_address":
			case "aw_customer_code":
			case "aw_time_spent_desc":
			case "aw_reminder_text":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "varchar(255)"
				));
				return true;
			case "aw_trans_date":
			case "aw_payment_mode":
			case "aw_on_demand":
			case "aw_warehouse":
			case "aw_price_list":
			case "aw_transfer_method":
			case "aw_transfer_condition":
			case "aw_selling_order":
			case "aw_transfer_address":
			case "aw_approved":
			case "aw_currency":
			case "aw_bill_accounting_date":
			case "aw_is_overdue_bill":
			case "aw_assembler":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "int"
				));
				return true;
			case "aw_overdue_charge":
				$this->db_add_col($table, array(
					"name" => $field,
					"type" => "double"
				));
				return true;
		}
	}

	/**
		@attrib name=reorder_rows
	**/
	function reorder_rows($arr)
	{
		$bill = obj($arr["id"]);
		$bill->reorder_rows();
		return $arr["post_ru"];
	}

	/**
		@attrib name=form_new_bill
	**/
	function form_new_bill($arr)
	{
		$bill = obj($arr["id"]);
		$new = $bill->form_new_bill($arr["sel_rows"]);
		if($this->can("view" , $new))
		{
			return $this->mk_my_orb("change", array("id" => $new), CL_CRM_BILL);
		}
		return $arr["post_ru"];
	}

	/**
		@attrib name=reconcile_rows
	**/
	function reconcile_rows($arr)
	{
		// go over the $sel_rows and add the numbers to the first selected one
		if (isset($arr["sel_rows"]) && is_array($arr["sel_rows"]) && count($arr["sel_rows"]) > 1)
		{
			$frow = obj($arr["sel_rows"][0]);
			$mtask_row = $frow->get_first_obj_by_reltype("RELTYPE_TASK_ROW");
			if(is_object($mtask_row))
			{
				$mtrid = $mtask_row->id();
			}
			for($i = 1; $i < count($arr["sel_rows"]); $i++)
			{
				$row_o = obj($arr["sel_rows"][$i]);
				if ($arr["reconcile_price"] != -1)
				{
					$frow->set_prop("price", $arr["reconcile_price"]);
				}
				$frow->set_prop("amt", $frow->prop("amt") + $row_o->prop("amt"));
				$frow->set_prop("sum", $frow->prop("amt") * $frow->prop("price"));
				$task_row = $row_o->get_first_obj_by_reltype("RELTYPE_TASK_ROW");
				if(is_object($task_row))
				{
					$task_row->set_meta("parent_row" , $mtrid);
					$frow->connect(array(
						"to" => $task_row->id(),
						"type" => "RELTYPE_TASK_ROW"
					));
					$task_row->save();
				}
				$row_o->delete();
			}
			$frow->save();
		}
		return $arr["post_ru"];
	}

	function on_delete_bill($arr)
	{
		$o = obj($arr["oid"]);
		// get all task rows from the bill rows and
		$ol = new object_list(array(
			"class_id" => CL_TASK_ROW,
			"bill_id" => $o->id()
		));
		foreach($ol->arr() as $tr)
		{
			$tr->set_prop("bill_id", 0);
			$tr->save();
		}
	}

	function callback_mod_retval(&$arr)
	{
		if(isset( $arr["request"]["project"]))
		{
			$arr["args"]["project"] = $arr["request"]["project"];
		}
	}

	function callback_mod_layout(&$arr)
	{
		switch($arr["name"])
		{
			case "bill_task_list_l":
				$arr["area_caption"] = sprintf(t("%s seotud tegevused"), $arr["obj_inst"]->name());
				break;
		}
		return true;
	}


	function callback_mod_tab($arr)
	{
		if ($arr["id"] === "preview_w_rows")
		{
			$this->set_current_settings();
			if($this->crm_settings && $this->crm_settings->prop("bill_hide_pwr"))
			{
				return false;
			}
		}
		return true;
	}

	/**
	@attrib name=send_bill all_args=1
	@param id required type=int
		bill id
	@param post_ru required type=string
	@returns string
	**/
	function send_bill($arr)
	{
		$r = $arr["post_ru"];
		try
		{
			$this_o = obj($arr["id"], array(), CL_CRM_BILL);
		}
		catch (awex_obj $e)
		{
			$this->show_error_text(t("Arve id vale."));
			return $r;
		}

		if (empty($arr["sendmail_attachments"]) or !is_array($arr["sendmail_attachments"]))
		{
			$this->show_error_text(t("Arvet ei saa saata saadetavat dokumenti valimata."));
			return $r;
		}

		if (empty($arr["recipient"]) or !is_array($arr["recipient"]))
		{
			$this->show_error_text(t("Arvet ei saa saata saajaid valimata."));
			return $r;
		}

		$to = $cc = $bcc = array();
		$recipients = $this_o->get_mail_recipients();
		$selected_recipients = array_flip($arr["recipient"]);
		foreach ($recipients as $email_address => $data)
		{
			if (isset($selected_recipients[$email_address . "-to"]))
			{
				$to[$email_address] = $data[1] ? $data[1] : "";
			}
			elseif (isset($selected_recipients[$email_address . "-cc"]))
			{
				$cc[$email_address] = $data[1] ? $data[1] : "";
			}
			elseif (isset($selected_recipients[$email_address . "-bcc"]))
			{
				$bcc[$email_address] = $data[1] ? $data[1] : "";
			}
		}

		$subject = $this_o->parse_mail_text($arr["bill_mail_subj"]);
		$body = nl2br($this_o->parse_mail_text($arr["bill_mail_ct"]));
		$reminder = isset($arr["sendmail_attachments"]["r"]);
		$appendix = isset($arr["sendmail_attachments"]["a"]);
		$from = $arr["bill_mail_from"];
		$from_name = $arr["bill_mail_from_name"];

		try
		{
			$this_o->send_by_mail($to, $subject, $body, $cc, $bcc, $appendix, $reminder, $from, $from_name);
			$this->show_completed_text(t("Arve saadetud."));
		}
		catch (awex_crm_bill_email $e)
		{
			if ($e->email)
			{
				$this->show_error_text(sprintf(t("Arvet ei saadetud. Antud vigane aadress: '%s'"), $e->email));
			}
			else
			{
				$this->show_error_text(sprintf(t("Arvet ei saa saata saajaid m&auml;&auml;ramata"), $e->email));
			}
		}
		catch (awex_crm_bill_file $e)
		{
			$this->show_error_text(t("Arvet ei saadetud. Dokumendi lisamine eba&otilde;nnestus"));
		}
		catch (awex_crm_bill_send $e)
		{
			$this->show_error_text(t("Arvet ei saadetud. Viga t&otilde;en&auml;oliselt serveri meiliseadetes."));
		}
		catch (Exception $e)
		{
			trigger_error("Caught exception " . get_class($e) . " while sending bill. Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": '" . $e->getMessage() . "' <br /> Backtrace:<br />" . dbg::process_backtrace($e->getTrace(), -1, true), E_USER_WARNING);
			$this->show_error_text(t("Esines vigu. Arvet ei saadetud."));
		}

		// remove temporary changes
		$this->clear_send_mail_tmp();
		$this->show_completed_text(t("Arve edukalt saadetud!"));
		return $r;
	}

	/** returns bill id
		@attrib name=bug_search all_args=1
	@param customer optional type=int
		customer id
	**/
	function bug_search($arr)
	{
		$content = "";
		if(is_oid($arr["task"]))
		{
			die("<script language='javascript'>
				if (window.opener)
				{
					window.opener.document.getElementsByName('add_bug')[0].value=".$arr["task"].";
					window.opener.submit_changeform();
				}
				window.close();
			</script>");
		}

		$htmlc = new htmlclient();
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "name",
			"type" => "textbox",
			"value" => $arr["name"],
			"caption" => t("Tegevuse nimi"),
			"autocomplete_class_id" => array(CL_TASK , CL_BUG),
		));
		$htmlc->add_property(array(
			"name" => "project",
			"type" => "textbox",
			"value" => $arr["project"],
			"caption" => t("Projekt"),
			"autocomplete_class_id" => array(CL_PROJECT),
		));
		$htmlc->add_property(array(
			"name" => "submit",
			"type" => "submit",
			"value" => t("Otsi"),
			"caption" => t("Otsi")
		));
		$data = array(
			"customer" => $arr["customer"],
			"orb_class" => $_GET["class"]?$_GET["class"]:$_POST["class"],
			"reforb" => 0,
		);

		$t = new vcl_table(array(
			"layout" => "generic",
		));
		$t->define_field(array(
			"name" => "choose",
			"caption" => "",
		));

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "project",
			"caption" => t("Projekt"),
		));
		$filter = array(
			"class_id" => array(CL_TASK,CL_BUG),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_TASK.RELTYPE_CUSTOMER" => $arr["customer"],
					"CL_BUG.RELTYPE_CUSTOMER" => $arr["customer"],
				)
			)),
		);

		if($arr["name"])
		{
			$filter["name"] = "%".$arr["name"]."%";
		}
		if($arr["project"])
		{
			$filter[] = new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array(
					"CL_TASK.RELTYPE_PROJECT.name" => "%".$arr["project"]."%",
					"CL_BUG.RELTYPE_PROJECT.name" => $arr["customer"],
				)
			));
		}

		$ol = new object_list($filter);

		foreach($ol->arr() as $o)
		{
			$cust = "";
			$t->define_data(array(
				"oid" => $o->id(),
				"name" => $o->prop("name"),
				"choose" => html::href(array(
					"caption" => t("Vali see"),
					"url" => $this->mk_my_orb("bug_search",
						array(
							"task" => $o->id(),
						), "crm_bill"
					),
				)),
				"project" => $o->prop("project.name"),
			));
		}


		$htmlc->add_property(array(
			"name" => "table",
			"type" => "text",
			"value" => $t->draw(),
			"no_caption" => 1,
		));

		$htmlc->finish_output(array(
			"action" => "bug_search",
			"method" => "POST",
			"data" => $data
		));

		$content.= $htmlc->get_result();

		return $content;
	}

	function _get_mail_table(&$arr)
	{
//Saaja isikute nimed, asutused, telefon laual ja mobiil, ametinimetus, meilidaadressid; arve summa, arve laekumise t2htaeg; arve staatus.
		$t = $arr["prop"]["vcl_inst"];
		$t->set_default("sortable" , 1);
		$t->add_fields(array(
			"sender" => t("Saatja nimi"),
			"time" => t("Aeg"),
			"to" => t("Aadressidele"),
			"content" => t("Sisu"),
			"attachments" => t("Manused"),
		));

		$user_inst = get_instance(CL_USER);

		$mails = $arr["obj_inst"]->get_sent_mails();
		foreach($mails->arr() as $mail)
		{
			$user = $mail->createdby();
			$person = $user_inst->get_person_for_uid($user);
			$data = array();
			$data["time"] = date("d.m.Y H:i" , $mail->created());

			$data["sender"] = $person->name();
			$data["content"] = $mail->prop("message");
			$addr = explode("," , htmlspecialchars($mail->prop("mto")));

			$data["to"] = join(html::linebreak() , $addr);

			$data["attachments"] = "";
			$aos = safe_array($mail->prop("attachments"));
			foreach($aos as $ao)
			{
				if (object_loader::can("view", $ao))
				{
					$o = obj($ao);
					$file_data = $o->get_file();
					$data["attachments"].= html::linebreak().html::href(array(
						"caption" => html::img(array(
							"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
							"border" => 0,
						)).$o->name()." (".filesize($file_data["properties"]["file"])." B)",
						"url" => $o->get_url(),
					));
				}
			}
			$t->define_data($data);
		}

		return PROP_OK;
	}

	function _get_dn_confirm_tbl(&$arr)
	{
		if(!empty($arr["new"]))
		{
			return PROP_IGNORE;
		}
		$t = $arr["prop"]["vcl_inst"];

		$t->set_titlebar_display(false);
		$t->set_caption(t("Saatelehed"));
		$t->define_field(array(
			"name" => "name",
			"caption" => t("Saateleht"),
			"align" => "center",
		));
		$t->define_field(array(
			"name" => "action",
			"caption" => t("Tegevus"),
			"align" => "center",
		));
		foreach($arr["obj_inst"]->connections_from(array(
			"type" => "RELTYPE_DELIVERY_NOTE",
		)) as $c)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($c->to()),
				"action" => $c->to()->prop("approved") ? t("Kinnitatud") : "<div id='confirm_dn_".$c->prop("to")."'>".html::button(array(
					"onclick" => "var proceed = confirm('".html_entity_decode(t("Olete kindel? J&auml;tkamisel tehakse lao liikumised"))."'); if(proceed) { $.get('".$this->mk_my_orb("create_movement", array("id" => $c->prop("to")), CL_SHOP_DELIVERY_NOTE)."', function(data){if(data.length) { alert('".html_entity_decode(t("Kinnitamine eba&otilde;nnestus."))." '+data);} else { $('#confirm_dn_".$c->prop("to")."').html('".t("Kinnitatud")."'); } }); }",
					"value" => t("Kinnita"),
				)),
			));
		}

		return PROP_OK;
	}

	private function get_co_currency()
	{
		if(empty($this->company_currency))
		{
			$u = get_instance(CL_USER);
			$co = obj($u->get_current_company());
			$this->company_currency = $co->prop("currency");
		}
		return $this->company_currency;
	}


	/**
		@attrib name=move_rows_to_dn all_args=1
	**/
	function move_rows_to_dn($arr)
	{
		obj($arr["id"])->move_rows_to_dn($arr);
		return $arr["post_ru"];
	}

	/**
		@attrib name=make_overdue_bill all_args=1
	**/
	public function make_overdue_bill($arr)
	{
		try
		{
			$id = obj($arr["id"])->make_overdue_bill();
		}
		catch (awex_crm_bill_state $e)
		{
			$this->show_error_text(t("Viivisarvet koostatakse ainult laekunud arvete kohta"));
		}
		catch (awex_obj_type $e)
		{
			$this->show_error_text(t("Viivise m&auml;&auml;r peab olema > 0"));
		}
		catch (Exception $e)
		{
			trigger_error("Caught exception " . get_class($e) . ". called make_overdue_bill(). Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": '" . $e->getMessage() . "' <br /> Backtrace:<br />" . dbg::process_backtrace($e->getTrace(), -1, true), E_USER_WARNING);
			$this->show_error_text(t("Tundmatu viga"));
		}

		if(is_oid($id))
		{
			return  $this->mk_my_orb("change", array("id" => $id, "return_url" => $arr["ru"]), CL_CRM_BILL);
		}
		else
		{
			return $arr["ru"];
		}
	}

	/**
		@attrib name=reload_customer_data
		@param id required type=oid acl=view
			Bill id
		@param post_ru required type=string
			Return url
	**/
	public function reload_customer_data($arr)
	{
		try
		{
			$this_o = obj($arr["id"], array(), CL_CRM_BILL);
		}
		catch (Exception $e)
		{
			$this->show_error_text(t("Arveobjekt pole loetav"));
		}

		$this_o->load_customer_data();
		$this_o->save();
		return $arr["post_ru"];
	}

	private function add_sendmail_menu($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "send_bill",
			"tooltip" => t("Saada arve"),
			"img" => "mail_send.gif",
		));

		$url= aw_url_change_var(array(
			"group" => "send_mail",
			"sendmail_type" => "p"
		));
		$tb->add_menu_item(array(
			"parent" => "send_bill",
			"url" => $url,
			"text" => t("Saada arve pdf")
		));

		$url= aw_url_change_var(array(
			"group" => "send_mail",
			"sendmail_type" => "pa"
		));
		$tb->add_menu_item(array(
			"parent" => "send_bill",
			"url" => $url,
			"text" => t("Saada arve pdf koos lisaga")
		));

		$url= aw_url_change_var(array(
			"group" => "send_mail",
			"sendmail_type" => "r"
		));
		$tb->add_menu_item(array(
			"parent" => "send_bill",
			"url" => $url,
			"text" => t("Saada arve meeldetuletuse pdf")
		));

		$url= aw_url_change_var(array(
			"group" => "send_mail",
			"sendmail_type" => "ra"
		));
		$tb->add_menu_item(array(
			"parent" => "send_bill",
			"url" => $url,
			"text" => t("Saada arve meeldetuletuse pdf koos lisaga")
		));
	}

	private function add_print_menu($arr)
	{
		$has_val = $arr["obj_inst"]->is_saved() ? !$arr["obj_inst"]->has_not_initialized_rows() : false;
		$onclick_start = empty($has_val) ? " fRet = confirm('".t("Arvel on ridu, mille v&auml;&auml;rtus on 0 eurot")."');	if(fRet){" : "";
		$onclick_end = empty($has_val) ? "}else;" : "";

		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_menu_button(array(
			"name" => "print",
			"tooltip" => t("Prindi"),
			"img" => "print.gif"
		));

		$onclick_middle = "win = window.open('".$this->mk_my_orb("change", array("openprintdialog" => 1,"id" => $arr["obj_inst"]->id(), "group" => "preview"), CL_CRM_BILL)."','billprint','width=100,height=100,statusbar=yes');";
		$tb->add_menu_item(array(
			"parent" => "print",
			"url" => "#",
			"onClick" => $onclick_start.$onclick_middle.$onclick_end,
			"text" => t("Prindi arve")
		));

		$onclick_middle = "win = window.open('".$this->mk_my_orb("change", array(
			"pdf" => 1,
			"id" => $arr["obj_inst"]->id(),
			"group" => "preview"), CL_CRM_BILL)."','billprint','width=100,height=100,statusbar=yes');";
		$tb->add_menu_item(array(
			"parent" => "print",
			"url" => "#",
			"onClick" => $onclick_start.$onclick_middle.$onclick_end,
			"text" => t("Prindi arve pdf")
		));

		$onclick_middle.= "win = window.open('".$this->mk_my_orb("change", array("openprintdialog" => 1,"id" => $arr["obj_inst"]->id(), "group" => "preview_add"), CL_CRM_BILL)."','billprint','width=100,height=100');";
		$tb->add_menu_item(array(
			"parent" => "print",
			"url" => "#",
			"onClick" => $onclick_start.$onclick_middle.$onclick_end,
			"text" => t("Prindi arve lisa")
		));

		$onclick_middle = "win = window.open('".$this->mk_my_orb("change", array(
			"pdf" => 1,
			"id" => $arr["obj_inst"]->id(),
			"group" => "preview_add"), CL_CRM_BILL)."','billprint','width=100,height=100');";
		$tb->add_menu_item(array(
			"parent" => "print",
			"url" => "#",
			"onClick" => $onclick_start.$onclick_middle.$onclick_end,
			"text" => t("Prindi arve lisa pdf")
		));

		$onclick_middle = "win = window.open('".$this->mk_my_orb("change", array(
			"openprintdialog" => 1,
			"id" => $arr["obj_inst"]->id(),
			"group" => "preview",
			"reminder" => 1
		), CL_CRM_BILL)."','billprint','width=100,height=100,statusbar=yes');";
		$tb->add_menu_item(array(
			"parent" => "print",
			"url" => "#",
			"onClick" => $onclick_start.$onclick_middle.$onclick_end,
			"text" => t("Prindi arve meeldetuletus")
		));

		$onclick_middle = "win = window.open('".$this->mk_my_orb("change", array(
			"pdf" => 1,
			"id" => $arr["obj_inst"]->id(),
			"group" => "preview",
			"reminder" => 1
			), CL_CRM_BILL)."','billprint','width=100,height=100,statusbar=yes');";
		$tb->add_menu_item(array(
			"parent" => "print",
			"url" => "#",
			"onClick" => $onclick_start.$onclick_middle.$onclick_end,
			"text" => t("Prindi arve meeldetuletus pdf")
		));

		$onclick_middle = "win = window.open('".$this->mk_my_orb("change", array(
			"pdf" => 1,
			"id" => $arr["obj_inst"]->id(),
			"group" => "preview_add",
			"handover" => 1
			), CL_CRM_BILL)."','billprint','width=100,height=100,statusbar=yes');";
		$tb->add_menu_item(array(
			"parent" => "print",
			"url" => "#",
			"onClick" => $onclick_start.$onclick_middle.$onclick_end,
			"text" => t("Prindi &uuml;leandmis-vastuv&otilde;tmisakt pdf")
		));
		$this->set_current_settings();

		if(empty($this->crm_settings) || !$this->crm_settings->prop("bill_hide_pwr"))
		{
			$onclick_middle = "window.open('".$this->mk_my_orb("change", array("openprintdialog_b" => 1,"id" => $arr["obj_inst"]->id(), "group" => "preview_add"), CL_CRM_BILL)."','billprint','width=100,height=100');";
			$tb->add_menu_item(array(
				"parent" => "print",
				"url" => "#",
				"onClick" => $onclick_start.$onclick_middle.$onclick_end,
				"text" => t("Prindi arve koos lisaga")
			));

			$onclick_middle.= "window.open('".$this->mk_my_orb("change", array("openprintdialog_b" => 1,"pdf" => 1,"id" => $arr["obj_inst"]->id(), "group" => "preview_add"), CL_CRM_BILL)."','billprint','width=100,height=100');";
			$tb->add_menu_item(array(
				"parent" => "print",
				"url" => "#",
				"onClick" => $onclick_start.$onclick_middle.$onclick_end,
				"text" => t("Prindi arve koos lisaga pdf")
			));
		}
	}

	private function clear_send_mail_tmp()
	{
		aw_session_del("crm_bill_sendmail_sender_email_tmp");
		aw_session_del("crm_bill_sendmail_sender_name_tmp");
		aw_session_del("crm_bill_sendmail_recipients_tmp");
		aw_session_del("crm_bill_sendmail_attachments_tmp");
	}
}
