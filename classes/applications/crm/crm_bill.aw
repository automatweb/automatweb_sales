<?php
/*
HANDLE_MESSAGE_WITH_PARAM(MSG_STORAGE_DELETE, CL_CRM_BILL, on_delete_bill)
@tableinfo aw_crm_bill index=aw_oid master_index=brother_of master_table=objects
@classinfo syslog_type=ST_CRM_BILL relationmgr=yes no_status=1 prop_cb=1 confirm_save_data=1 maintainer=markop
@default table=objects

@default group=general

	@property billp_tb type=toolbar store=no no_caption=1
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

		@layout almost_bottom parent=main_split type=vbox closeable=1 area_caption=Arve&nbsp;saajad

		@layout bottom parent=main_split type=vbox closeable=1 area_caption=Read

		@layout writeoff_layout type=vbox closeable=1 area_caption=Maha&nbsp;kantud&nbsp;arve&nbsp;read parent=main_split

	// top left lyt
	@property name type=textbox table=objects field=name parent=top_left
	@caption Nimi

	@property bill_no type=textbox table=aw_crm_bill field=aw_bill_no parent=top_left
	@caption Number

	@property impl type=popup_search style=relpicker table=aw_crm_bill field=aw_impl parent=top_left reltype=RELTYPE_IMPL
	@caption Arve esitaja

	@property assembler type=select table=aw_crm_bill field=aw_assembler parent=top_left
	@caption Koostaja

	@property bill_date type=date_select table=aw_crm_bill field=aw_date parent=top_left
	@caption Kuup&auml;ev

	@property bill_accounting_date type=date_select table=aw_crm_bill field=aw_bill_accounting_date parent=top_left
	@caption Raamatupidamise kuup&auml;ev

	@property bill_due_date_days type=textbox table=aw_crm_bill field=aw_due_date_days size=5 parent=top_left
	@caption Makset&auml;htaeg (p&auml;evi)

	@property bill_due_date type=date_select table=aw_crm_bill field=aw_due_date parent=top_left
	@caption Tasumise kuup&auml;ev

	@property bill_recieved type=date_select table=aw_crm_bill field=aw_recieved default=-1 parent=top_left
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

	@property bill_trans_date type=date_select table=aw_crm_bill field=aw_trans_date default=-1 parent=bottom_left
	@caption Kandekuup&auml;ev

	@property signers type=crm_participant_search reltype=RELTYPE_SIGNER multiple=1 table=objects field=meta method=serialize style=relpicker parent=bottom_left
	@caption Allkirjastajad

	@property signature_type type=select table=objects field=meta method=serialize parent=bottom_left
	@caption Allkirja t&uuml;&uuml;p

	// top right lyt
	@property customer_name type=textbox table=aw_crm_bill field=aw_customer_name parent=top_right
	@caption Kliendi nimi

	@property customer type=relpicker table=aw_crm_bill field=aw_customer reltype=RELTYPE_CUST parent=top_right
	@caption Klient

	@property customer_code type=textbox table=aw_crm_bill field=aw_customer_code parent=top_right
	@caption Kliendikood

	@property ctp_text type=textbox table=objects field=meta method=serialize parent=top_right
	@caption Kontaktisik vabatekstina

	@property customer_address type=textbox table=aw_crm_bill field=aw_customer_address parent=top_right
	@caption Kliendi aadress

	@property customer_add_meta_cb type=callback callback=customer_add_meta_cb store=no parent=top_right

#	@property customer_address_meta type=text no_caption=1 parent=top_right
#	@caption Kliendi aadressi muutujad metas



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

	// bottom lyt

	@property bill_targets type=table store=no no_caption=1 parent=almost_bottom
	@caption Arve saajad

	@property bill_rec_name type=textbox table=objects field=meta method=serialize parent=almost_bottom
	@caption Arve saaja nimi

	@property bill_rows type=text store=no no_caption=1 parent=bottom
	@caption Arveread


		@property writeoffs type=table store=no no_caption=1 parent=writeoff_layout
		@caption Mahakantud arve read

	#leftovers


@default group=other_data

	@property show_oe_add type=checkbox ch_value=1 field=meta method=serialize
	@caption N&auml;ita arve lisas muid kulusid

	@property rows_different_pages type=text field=meta method=serialize
	@caption Read erinevatel lehek&uuml;lgedel

	@property comment type=textbox table=objects field=comment
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

	@property mail_reciever type=relpicker store=connect multiple=1 reltype=RELTYPE_RECIEVER
	@caption Maili saaja

@default group=bill_mail

	@property bill_mail_to type=textbox field=meta method=serialize
	@caption Kellele meil saata

	@property bill_mail_from type=textbox field=meta method=serialize
	@caption Meili from aadress

	@property bill_mail_from_name type=textbox field=meta method=serialize
	@caption Meili from nimi

	@property bill_mail_subj type=textbox field=meta method=serialize
	@caption Meili subjekt

	@property bill_mail_legend type=text store=no
	@caption Meili sisu legend

	@property bill_mail_ct type=textarea rows=20 cols=50 field=meta method=serialize
	@caption Meili sisu

@default group=sent_mails

	@property mail_table type=table no_caption=1 no_caption=1

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
	@property bill_tb type=toolbar store=no no_caption=1
	@layout bill_task_list_l type=vbox
		@property bill_task_list type=table store=no no_caption=1 parent=bill_task_list_l

@groupinfo other_data caption="Muud andmed"
@groupinfo mails caption="Kirjad"

@groupinfo sent_mails caption="Saadetud kirjad" parent=mails
@groupinfo bill_mail caption="Kirjade seaded" parent=mails

@groupinfo delivery_notes caption="Saatelehed"
@groupinfo tasks caption="Toimetused" submit=no
@groupinfo preview caption="Eelvaade"
@groupinfo preview_add caption="Arve Lisa"
@groupinfo preview_w_rows caption="Eelvaade ridadega"


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

@reltype RECIEVER value=19 clid=CL_CRM_PERSON
@caption Arve saaja
*/

class crm_bill extends class_base
{
	function crm_bill()
	{
		$this->init(array(
			"tpldir" => "crm/crm_bill",
			"clid" => CL_CRM_BILL
		));

		$this->states = array(
			0 => t("Koostamisel"),
			8 => t("Koostatud"),
			7 => t("Kinnitatud"),
			1 => t("Saadetud"),
			2 => t("Makstud"),
			3 => t("Laekunud"),
			6 => t("Osaliselt laekunud"),
			4 => t("Kreeditarve"),
			5 => t("Tehtud kreeditarve"),
			-5 => t("Maha kantud"),
		);

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

	function callback_mod_reforb($arr)
	{
		$arr["post_ru"] = post_ru();
		$arr["add_bug"] = "";
		$arr["reconcile_price"] = -1;
		$arr["new_payment"] = "";
		$arr["add_dn"] = 0;
		if(!empty($_GET["project"]))
		{
			$arr["project"] = $_GET["project"];
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
			"lang_id" => array(),
			"site_id" => array(),
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
				$maker = $arr["obj_inst"]->get_the_person_who_made_this_fucking_thing();
				$prop["options"] = array($maker->id() => $maker->name());
				$ps = new popup_search();
				$ps->set_class_id(array(CL_CRM_PERSON));
				$ps->set_id($arr["obj_inst"]->id());
				$ps->set_reload_layout("almost_bottom");
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
				$prop["value"] = $arr["obj_inst"]->get_bill_currency_id();
				if(!$prop["options"][$prop["value"]])
				{
					$prop["options"][$prop["value"]] = $arr["obj_inst"]->get_bill_currency_name();
				}
				break;

			case "mail_table":
				$this->_get_mail_table($arr);
				break;

			case "important_comment":
				if($this->can("view" , $arr["obj_inst"]->meta("important_comment")))
				{
					$ic = obj($arr["obj_inst"]->meta("important_comment"));
					$prop["value"] = "<font color=red size=+1><b>".$ic->comment()."</b></font>";
				}
				if(isset($this->error))
				{
					$prop["value"].= "<br><font color=red size=+1>".html::bold($this->error)."</font>";
				}
				break;

			case "comments":
				$prop["value"] = $arr["obj_inst"]->get_comments_text();
				break;

			case 'partial_recieved':
				if(!$arr["new"])
				{
					$sum = $arr["obj_inst"]->get_bill_recieved_money();
				}

				$prop["value"] = number_format($sum, 2);
				$prop["value"] .= " ".$arr["obj_inst"]->get_bill_currency_name();

				if(!$arr["new"])
				{
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
						"url" => "javascript:aw_popup_scroll('".$url."','Otsing',550,500)",
						"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/search.gif' border=0>",
						"title" => t("Otsi")
					))."<br>";
				}

				if($arr["obj_inst"]->id() > 1)
				{
					foreach($arr["obj_inst"]->get_bill_payments_data() as $dat)
					{
						$prop["value"].= "\n<br>".date("d.m.Y" , $dat["date"])." ".$dat["sum"]." ".$dat["currency"];
					}
				}
				break;
			case "payment_mode":
				$prop["options"] = array("" , t("&Uuml;lekandega") , t("Sularahas"));
				break;
			case "billp_tb":
				$this->_bill_tb($arr);
				break;

			case 'dn_tb':
			case 'dn_tbl':
			case 'bill_targets':
			case 'bill_task_list':
			case "writeoffs":
			case "preview":
			case "preview_add":
			case "bill_rows":
				if($arr["new"])
				{
					return PROP_IGNORE;
				}
				$fun = "_".$prop["name"];
				$this->$fun($arr);
				break;

			case 'bill_tb':
				$this->_billt_tb($arr);
				break;

			case 'bill_rec_name':
				$ps = get_instance("vcl/popup_search");
				$ps->set_class_id(array(CL_CRM_PERSON));
				$ps->set_id($arr["obj_inst"]->id());
				$ps->set_reload_layout("almost_bottom");
				$ps->set_property("mail_reciever");
				$prop["post_append_text"] = $ps->get_search_button();
				break;

			case "bill_no":
				if ($prop["value"] == "")
				{
					$time = $arr["obj_inst"]->prop("bill_date");
					if(!$time) $time = time();
					$i = get_instance(CL_CRM_NUMBER_SERIES);
					$prop["value"] = $i->find_series_and_get_next(CL_CRM_BILL, 0 , $time);
					if (!$arr["new"] && is_oid($arr["obj_inst"]->id()))
					{
						$arr["obj_inst"]->set_prop("bill_no" , $prop["value"]);
						$arr["obj_inst"]->save();
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
				$this->_preview($arr);
				break;

			case "state":
				$prop["options"] = $this->states;
				break;

			case "customer_name":
				$prop["post_append_text"] = "sdfsd";
				$ps = get_instance("vcl/popup_search");
				$ps->set_class_id(array(CL_CRM_PERSON, CL_CRM_COMPANY));
				$ps->set_id($arr["obj_inst"]->id());
				$ps->set_reload_layout("top_right");
				$ps->set_property("customer");
				$prop["post_append_text"] = " ".$ps->get_search_button();

			case "customer_code":
			case "customer_address":
				if(!$arr["obj_inst"]->prop("customer_name"))
				{
					return PROP_IGNORE;
				}
				break;

			case "customer":
				if($arr["obj_inst"]->prop("customer_name"))
				{
					return PROP_IGNORE;
				}
				if($arr["new"] && $this->customer_object)
				{
					$prop["value"] = $this->customer_object->id();
				}
				break;

			case "bill_text":
				$prop["value"] = $arr["obj_inst"]->get_bill_text();
				break;

			case "bill_due_date_days":
				if($arr["new"] && $this->customer_object)
				{
					$prop["value"] = $this->customer_object->prop("bill_due_date_days");
				}
				break;
			case "sum":
				if(!($arr["obj_inst"]->id() > 0))
				{
					return PROP_IGNORE;
				}
				$agreement_prices = $arr["obj_inst"]->meta("agreement_price");
				if(isset($agreement_prices[0]) && is_array($agreement_price) && $agreement_prices[0]["price"] && strlen($agreement_prices[0]["name"]) > 0)
				{
					$sum = 0;
					foreach($agreement_prices as $agreement_price)
					{
						$sum+= $agreement_price["sum"];
					}
					$prop["value"] = $sum;
				}
				if(($SUM_WT = $arr["obj_inst"]->get_bill_sum()) > $prop["value"])
				{
					$SUM_WITHOUT = $SUM_WT - $prop["value"];
					$add_tax = 1;
				}

				$val = array();
				$val[] = number_format($prop["value"], 2)." ".$arr["obj_inst"]->get_bill_currency_name();
				if(!empty($add_tax))
				{
					$val[] = t("Summa").": ".$prop["value"]." ".$arr["obj_inst"]->get_bill_currency_name();
					$val[] = t("KM").": ".number_format($SUM_WITHOUT, 2)." ".$arr["obj_inst"]->get_bill_currency_name();
					$val[] = t("Kokku").": ".number_format($SUM_WT, 2)." ".$arr["obj_inst"]->get_bill_currency_name();
				}
				if($writeoffs_sum = $arr["obj_inst"]->get_writeoffs_sum())
				{
					$val[] = t("Mahakantud ridade summa:")." ".number_format($writeoffs_sum, 2)." ".$arr["obj_inst"]->get_bill_currency_name();
				}

				$prop["value"] = join ("\n<br>", $val);
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
						if($row){
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
//			case "bill_mail_to":
			case "bill_mail_from":
			case "bill_mail_from_name":
			case "bill_mail_subj":
			case "bill_mail_ct":
				if(empty($prop["value"]))
				{
					$this->set_current_settings();
					if(is_object($this->crm_settings))
					{
						$prop["value"] = $this->crm_settings->prop($prop["name"]);
					}
				}
				break;

			case "bill_mail_legend":
				$prop["value"] = $this->get_mail_legend();
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
					"site_id" => array(),
					"lang_id" => array(),
				));
				$val = array();
				if($this->can("view", $prop["value"]) && !in_array($prop["value"], $ids))
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
		};
		return $retval;
	}

	function get_mail_legend()
	{
		return "#bill_no# => Arve number \n<br>
			#customer_name# => Kliendi nimi \n<br>
			#contact_person# => Kontaktisik,
			#signature# => saatja allkiri"
		;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case 'bill_targets':
				$this->set_bill_targets($arr);
				break;
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
			case 'partial_recieved':
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
					if(is_oid($p) && $this->can("view" , $p))
					{
						$error = $this->add_payment(array(
							"o" => $arr["obj_inst"],
							"p" => $p,
							"show_error" => 1,
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
						"lang_id" => array(),
						"site_id" => array(),
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
			case "customer_name":
			case "customer_code":
			case "customer_address":
				if(!empty($arr["request"]["customer"]))
				{
					return PROP_IGNORE;
				}
				break;

			case "impl":
				if(!$prop["value"])
				{
					$u = get_instance(CL_USER);
					$prop["value"] = $u->get_current_company();
				}

			case "customer":
				// check if the
				if($prop["name"] == "customer" && isset($arr["request"]["customer_name"]))
				{
					return PROP_IGNORE;
				}
				if ($this->can("view", $prop["value"]) && (($arr["obj_inst"]->prop("bill_due_date_days") == 0) || ($arr["obj_inst"]->prop("bill_due_date_days") == null)))
				{
					$cc = get_instance(CL_CRM_COMPANY);
					$crel = $cc->get_cust_rel(obj($prop["value"]));
					$u = get_instance(CL_USER);
					$my_co = $u->get_current_company();
					$co_obj = obj($co_obj);
					$client_obj = obj($prop["value"]);
					if(!$crel)
					{
						$ol = new object_list(array(
							"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
							"buyer" => $prop["value"],
							"seller" => $my_co,
							"lang_id" => array(),
						));
						$crel = reset($ol->arr());
					}
					if ($prop["value"] != $arr["obj_inst"]->prop($prop["name"]))
					{
						if ($crel)
						{
							$this->_set_bddd = $crel->prop("bill_due_date_days");
						}
						if(!$this->_set_bddd && !($arr["obj_inst"]->prop("bill_due_date_days") > 0))
						{
							$this->_set_bddd = $co_obj->prop("bill_due_days");
						}
						if(!$this->_set_bddd && !($arr["obj_inst"]->prop("bill_due_date_days") > 0) && $client_obj->class_id() == CL_CRM_COMPANY)
						{
							$this->_set_bddd = $client_obj->prop("bill_due_days");
						}
					}
				}
				if ($prop["name"] == "customer")// && ($this->can("view", $prop["value"]) || $this->can("view", $arr["obj_inst"]->prop("customer"))))
				{
					if($this->can("view", $prop["value"]))
					{
						$cust_obj = obj($prop["value"]);

					}
					else
					{
						$cust_obj = obj($arr["obj_inst"]->prop("customer"));
					}
					$arr["obj_inst"]->set_prop("customer_name" , $cust_obj->name());
					$arr["obj_inst"]->set_prop("customer_code" ,$cust_obj->prop("code"));
					$customer_addr = array();
					if($cust_obj->class_id() == CL_CRM_COMPANY)
					{
						$arr["obj_inst"]->set_prop("customer_address" , $cust_obj->prop("contact.name"));
						$customer_addr["street"] = $cust_obj->prop("contact.aadress");
						$customer_addr["city"] = $cust_obj->prop("contact.linn.name");
						$customer_addr["county"] = $cust_obj->prop("contact.maakond.name");
						$customer_addr["country"] = $cust_obj->prop("contact.riik.name");
						$customer_addr["country_en"] = $cust_obj->prop("contact.riik.name_en");
						$customer_addr["index"] = $cust_obj->prop("contact.postiindeks");
					}
					else
					{
						$arr["obj_inst"]->set_prop("customer_address" , $cust_obj->prop("address.name"));
						$customer_addr["street"] = $cust_obj->prop("address.aadress");
						$customer_addr["city"] = $cust_obj->prop("address.linn.name");
						$customer_addr["county"] = $cust_obj->prop("address.maakond.name");
						$customer_addr["country"] = $cust_obj->prop("address.riik.name");
						$customer_addr["country_en"] = $cust_obj->prop("address.riik.name_en");
						$customer_addr["index"] = $cust_obj->prop("address.postiindeks");
					}
					$arr["obj_inst"]->set_meta("customer_addr" , $customer_addr);
					$arr["obj_inst"]->save();
				}
				break;
		}
		return $retval;
	}

	function _dn_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$tb->add_new_button(array(CL_SHOP_DELIVERY_NOTE), $arr["obj_inst"]->id(), 14);
		$tb->add_search_button(array(
			"pn" => "add_dn",
			"clid" => CL_SHOP_DELIVERY_NOTE,
			"multiple" => 1,
		));
		$tb->add_delete_rels_button();
	}

	function customer_add_meta_cb($arr)
	{
		if(!$arr["obj_inst"]->prop("customer_name"))
		{
			return PROP_IGNORE;
		}
		$ad = $arr["obj_inst"]->meta("customer_addr");
		$dt = array(
			"street" => t("T&auml;nav, maja, korter"),
			"index" => t("Postiindeks"),
			"city" => t("Linn"),
			"county" => t("Maakond"),
			"country" => t("Riik"),
			"country_en" => t("Riik inglise keeles"),
		);

		foreach($ad as $key => $val)
		{
			$retval["address_meta[".$key."]"] = array(
				"name" => "address_meta[".$key."]",
				"type" => "textbox",
				"parent" => "top_right",
				"caption" => $dt[$key],
				"size" => 20,
				"value" => $val,
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
		extract($arr);
		if(!is_object($o) && is_oid($id) && $this->can("view", $id))
		{
			$o = obj($id);
		}
		if(is_oid($p) && $this->can("view" , $p))
		{
			$p = obj($p);
		}

		if(is_object($p))
		{
			$error = $p->add_bill($o);
			if($error)
			{
				arr($error);
			}
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
//---------------end


	private function set_bill_targets($arr)
	{
		$arr["obj_inst"]->set_meta("bill_t_names" , $arr["request"]["bill_t_names"]);
		$arr["obj_inst"]->set_meta("bill_targets" , $arr["request"]["bill_targets"]);
	}

	private function _writeoffs($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
		$this->_init_bill_rows_t($t);

		$task_i = get_instance(CL_TASK);

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
				"color" => "gray",
			));
		}
	}

	private function _bill_targets($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
/*		$t->define_chooser(array(
			"field" => "oid",
			"name" => "bill_targets",
		));*/
		$t->define_field(array(
			"name" => "selection",
			"caption" => t("*"),
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "sel2",
			"caption" => "",
			"align" => "center",
		));

		$t->define_field(array(
			"name" => "name2",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "name_over",
			"caption" => "",
			"parent" => "name2",
		));
		$t->define_field(array(
			"name" => "name",
			"caption" => "",
			"parent" => "name2",
		));
		$t->add_fields(array(
			"rank" => t("Ametinimetus"),
			"mail" =>  t("Mailiaadress"),
			"phone" =>  t("Telefon"),
			"co" => t("Organisatsioon"),
		));

		$bill_targets = $arr["obj_inst"]->meta("bill_targets");
		$bill_t_names = $arr["obj_inst"]->meta("bill_t_names");
		$phone = $arr["obj_inst"]->get_customer_phone();
		foreach($arr["obj_inst"]->get_mail_persons()->arr() as $mail_person)
		{
			if($mail_person->class_id() == CL_CRM_PERSON)
			{
				$t->define_data(array(
					"name" => $mail_person->name(),
					"oid" => $mail_person->id(),
					"phone" => $mail_person->get_phone(),
					"rank" => $arr["obj_inst"]->prop("customer") ? join(", " , $mail_person->get_profession_names(obj($arr["obj_inst"]->prop("customer")))) : "",
					"mail" => $mail_person->get_mail($arr["obj_inst"]->prop("customer")),
					"co" => $mail_person->company_name(),
					"selection" => html::checkbox(array(
						"name" => "bill_targets[".$mail_person->id()."]",
						"checked" => !(is_array($bill_targets) && sizeof($bill_targets) && empty($bill_targets[$mail_person->id()])),
						"ch_value" => $mail_person->id()
					)),"sel2" => "bcc"
				));
			}
		}

		foreach($arr["obj_inst"]->get_cust_mails() as $id => $mail)
		{
			$phone = "";

			$t->define_data(array(
				"name" => $arr["obj_inst"]->get_customer_name(),
				"oid" => $id,
				"mail" => $mail,
				"phone" => $this->get_phone_by_mail_id($id , $bill_t_names[$id] ,  $phone),

				"co" => $arr["obj_inst"]->get_customer_name(),
				"selection" => html::checkbox(array(
					"name" => "bill_targets[".$id."]",
					"checked" => !(is_array($bill_targets) && sizeof($bill_targets) && !$bill_targets[$id]),
					"ch_value" => $id
				)),
				"name_over" => html::textbox(array(
					"name" => "bill_t_names[".$id."]",
					"value" => $bill_t_names[$id],
					"size" => 20
				))
			));
		}

		if($arr["obj_inst"]->prop("bill_mail_to"))
		{
			$t->define_data(array(
				"mail" => $arr["obj_inst"]->prop("bill_mail_to"),
				"name_over" => html::textbox(array(
					"name" => "bill_t_names[0]",
					"value" => $bill_t_names[0],
					"size" => 20
				)),
			));
		}

		if($arr["obj_inst"]->set_crm_settings() && is_object($arr["obj_inst"]->crm_settings) && $arr["obj_inst"]->crm_settings->prop("bill_mail_to"))
		{
			$t->define_data(array(
				"mail" => $arr["obj_inst"]->crm_settings->prop("bill_mail_to"),
				"sel2" => "bcc"
			));
		}
		if (aw_global_get("uid_oid") != "")
		{
			$user_inst = get_instance(CL_USER);
			$u = obj(aw_global_get("uid_oid"));
			$person = obj($user_inst->get_current_person());
			$mail = $u->get_user_mail_address();
			if(is_oid($mail))
			{
				$mail_obj = obj($mail);
				if($mail_obj->class_id() == CL_ML_MEMBER)
				{
					$mail = $mail_obj->prop("mail");
				}
			}
			$t->define_data(array(
				"name" => $person->name(),
				"mail" =>$mail,
				"sel2" => "bcc"
			));
		}
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
			"to" => $mail,
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
				$person = $rel->get_first_object_by_reltype("RELTYPE_PERSON");
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

	function _dn_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];
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
	}

	function _init_bill_rows_t(&$t)
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
				"title" => t("+KM?")
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

	function _bill_rows($arr)
	{
		classload("vcl/table");
		$t = new vcl_table();
		$this->_init_bill_rows_t($t);

		$sum = 0;

		$pps = get_instance("applications/crm/crm_participant_search");
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


		classload("vcl/table");
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

			//miski suva jrjekorranuumbrite genereerimine... kui on vaja , ei tea kas see enam toimib, ei viitsi vaadata ka
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

		$agreement_prices[] = array();
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
					$price_cc = "<br>".$cc_price." ".$ccurrency_name;
					$sum_cc = "<br>".$cc_price*$agreement_price["amt"]." ".$ccurrency_name;
				}
				$ut->clear_data();
				$ut->define_data(array(
					"field1" => t("&Uuml;hik"),
					"field2" => html::textbox(array(
						"name" => "agreement_price[".$x."][unit]",
						"value" => isset($agreement_price["unit"]) ? $agreement_price["unit"] : "",
						"size" => 3,
						"autocomplete_source" => $this->mk_my_orb("unit_options_autocomplete_source"),
						"autocomplete_params" => array("agreement_price[".$x."][unit]"),
						"option_is_tuple" => 1,
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
					"name" => t("Kokkuleppehind")." ".($x+1)."<br>".html::textbox(array(
						"name" => "agreement_price[".$x."][date]",
						"value" => isset($agreement_price["date"]) ? $agreement_price["date"] : "",
						"size" => 8
					))."<br>".html::textarea(array(
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
					"person" => "<br>".$pps->get_popup_search_link(array(
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

		$arr["prop"]["value"] = $t->draw();
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
		$props = array("name" , "comment" , "date" , "price" , "amt", "unit");
		foreach($props as $prop)
		{
			if(isset($arr[$prop]))
			{
				$o->set_prop($prop , iconv("UTF-8", aw_global_get("charset"), $arr[$prop]));
			}
		}

		if(isset($arr["prod"]) && $this->can("view", $arr["prod"]) && obj($arr["prod"])->class_id() == CL_SHOP_PRODUCT)
		{
			$o->set_prop("prod", $arr["prod"]);
		}

		if($arr["name"])
		{
			$o->set_prop("desc" , iconv("UTF-8", aw_global_get("charset"), $arr["name"]));
		}

		if($arr["jrk"])
		{
			$o->set_meta("jrk" , $arr["jrk"]);
		}

			foreach($o->connections_from(array("type" => "RELTYPE_PEOPLE")) as $c)
			{
				if(!in_array($c->prop("to") , explode("," ,$arr["people"])))
				{
					$c->delete();
				}
			}
			$o->set_prop("people" , explode("," ,$arr["people"]));

		if($arr["has_tax"] != "")
		{
			if($arr["has_tax"] == "true")
			{
				$o->set_prop("has_tax" , 1);
			}
			elseif($arr["has_tax"] > 0)
			{
				$o->set_prop("tax" , $arr["has_tax"]);
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
		$pps = get_instance("applications/crm/crm_participant_search");
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
							 has_tax = document.getElementsByName('rows[".$id."][has_tax]')[0].checked;
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
						, has_tax: document.getElementsByName('rows[".$id."][has_tax]')[0].value
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
				$ret.="<table><tr><td width=400>".
				html::textbox(array(
					"name" => "rows[".$row->id()."][jrk]",
					"value" => $row->meta("jrk"),
					"size" => 3
				)).html::textbox(array(
					"name" => "rows[".$row->id()."][date]",
					"value" => $row->prop("date"),
					"size" => 8
				)).($row->is_writeoff() ? t("mahakantud") : "")."<br>".html::textbox(array(
					"name" => "rows[".$row->id()."][comment]",
					"value" => $row->comment(),
					"size" => 70
				))."<br>".html::textarea(array(
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
				classload("vcl/table");
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
						"value" => $row->get_row_tax(1),
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
					"value" => $row->get_row_tax(1),
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
				)).$s."<br />".($row->meta("dno") ? sprintf(t("Saatelehel %s"), obj($row->meta("dno"))->name()) : "<div id='dn_info_$id'>".t("Liiguta saatelehele:").$m->get_menu(array(
					"icon" => "copy.gif",
				))."</div>");
				break;
			case "project":
				$ret.= html::select(array(
					"name" => "rows[$id][project]",
					"options" => array("" => t("--vali--")) + $row->get_project_selection(),
					"value" => $row->prop("project"),
				));
				$ps = get_instance("vcl/popup_search");
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
				))."<br>".$pps->get_popup_search_link(array(
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

		foreach($names as $id => $name)
		{
			if($name)
			{
				$prods[$this->get_unit_id($name)] = $name;
			}
		}
		return $prods;
	}

	private function get_unit_id($name)
	{
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"lang_id" => array(),
			"site_id" => array(),
			"name" => $name,
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
					"onclick" => "edit_row('".$id."')",
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
					$row->meta("jrk")."<br>".
					$row->prop("date")."<br>".
					"<b>".$row->prop("comment")."</b><br>".
					//preg_replace('/([^\s]{100})(?=[^\s])/m', '$1 ', $row->prop("name")).
					wordwrap(htmlspecialchars(($row->prop("desc"))), 100, "<br>", true).
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
					$price_cc = "<br>".round($cc_price , 2)." ".$ccurrency_name;
					$sum_cc = "<br>".round($cc_price*$row->prop("amt") , 2)." ".$ccurrency_name;
				}
				$ret.=$row->prop("unit.name")."<br>".
					t("Hind").": ".$row->prop("price")."<br>".$price_cc.
					t("Kogus").": ".$row->prop("amt")."<br>".$sum_cc.
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
				$ret.=$row->prop("prod.name").(($dn = $row->meta("dno")) ? "<br />".sprintf(t("(Saatelehel %s)"), obj($dn)->name()): "");
				break;
			case "person":
				$ret.=join("<br>" , $row->get_person_selection());
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
				$content.= $error."<br>";
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


		$htmlc = get_instance("cfg/htmlclient");
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
		$ac = get_instance("vcl/autocomplete");
		$arr = $ac->get_ac_params($arr);
		$ol = new object_list(array(
			"class_id" => CL_UNIT,
			"lang_id" => array(),
			"site_id" => array(),
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
		$u = get_instance(CL_USER);
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
		if (!empty($this->_set_bddd))
		{
			$arr["obj_inst"]->set_prop("bill_due_date_days", $this->_set_bddd);
		}

		$bt = $arr["obj_inst"]->prop("bill_date");
		$arr["obj_inst"]->set_prop("bill_due_date",
			mktime(3,3,3, date("m", $bt), date("d", $bt) + $arr["obj_inst"]->prop("bill_due_date_days"), date("Y", $bt))
		);

		if (!empty($this->_set_recv_date))
		{
			$arr["obj_inst"]->set_prop("bill_recieved", $this->_set_recv_date);
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

	public function _preview($arr)
	{
		$arr["prop"]["value"] = $this->show(array(
			"id" => $arr["obj_inst"]->id(),
			"all_rows" => !empty($arr["all_rows"]),
			"pdf" => !empty($arr["pdf"]),
			"reminder" => !empty($arr["request"]["reminder"]),
			"handover" => !empty($arr["request"]["handover"])
		));
	}

	function _preview_add($arr)
	{
		$show_pdf = (!empty($arr["request"]["pdf"]) or !empty($arr["pdf"]));
		$handover = (!empty($arr["request"]["handover"]) or !empty($arr["handover"]));

		if($arr["obj_inst"]->meta("rows_in_page"))
		{
			$page = 0;
		}

		if(array_sum($arr["obj_inst"]->meta("rows_in_page")) > 0)
		{
			$this->_preview_popup(array(
				"rows_in_page" => $arr["obj_inst"]->meta("rows_in_page"),
				"page" => $page,
				"id" => $arr["obj_inst"]->id(),
				"handover" => $handover
			));
		}

		if($page == 0) $arr["prop"]["value"] = die($this->show_add(array(
			"id" => $arr["obj_inst"]->id(),
			"pdf" => $show_pdf,
			"handover" => $handover
		)));
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
				$aps = join(", ", $ap)."<br>";
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
			"return" => 1,
		));
	}


	function show($arr)//kui igav hakkab, siis selle funktsiooni peaks nullist kirjutama
	{
		$this->bill = obj($arr["id"]);
		$stats = get_instance("applications/crm/crm_company_stats_impl");
		$currency = $this->bill->get_bill_currency_id();
		$tpl_suffix = $this->bill->prop("state") == 16 ? "_offer" : "";

	//templeidi lugemine
		$tpl = "show{$tpl_suffix}";
		$lc = "et";
		if ($this->can("view", $this->bill->prop("language")))
		{
			$lc = $this->bill->prop("language.lang_acceptlang");
		}

		if(isset($_GET["pdf"]))
		{
			$arr["pdf"] = $_GET["pdf"];
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
			if ($this->read_site_template($tpl.".tpl", true) === false)
			{
				if ($this->read_site_template("show_".$tpl_suffix.$lc.".tpl", true) === false)
				{
					$this->read_template("show{$tpl_suffix}.tpl");
				}
			}
		}
		else
		{
			$tpl .= "_".$lc;
			if ($this->read_site_template($tpl.".tpl", true) === false)
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
				$bpct = $imp_vars["impl_penalty"];
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
			"comment" => $this->bill->prop("notes"),
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
		if(!empty($agreement["price"]) && !empty($agreement["name"])) $agreement = array($agreement); // kui on vanast ajast jnud
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
			if ($row["is_oe"])
			{
				continue;
			}
			$cur_tax = 0;
			$cur_sum = 0;
			$tax_rate = (double)$row["tax"] / 100.0;

			//kole asi... idee selles, et kuskil seppikus ja sirelis jne on toodetega mingi teema, mida mujal ei kasutata, ja siis ridade kokku koondamine k2iks nagu vaid siis kui toode on sama, a kui ei ole, siis peaks ikka ka saama.... et ma siis olematule tootele kui kommentaari v2li on t2idetud, l2heb tooteks 1
			if (!$this->can("view", $row["prod"]))
			{
				if($row["comment"])
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
			$grp_rows[$row["prod"]][$unp]["id"] = $row["id"];
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
					"price" => number_format(($grp_row["tot_cur_sum"] / $grp_row["tot_amt"]),2,".", " "),
					"sum" => number_format($grp_row["tot_cur_sum"], 2, ".", " "),
					"row_tax" => number_format($grp_row["tax"], 2, ".", " "),
					"desc" => $desc,
					"date" => "",
					"row_orderer" => $grp_row["orderer"],
				));
				$rs[] = array("str" => $this->parse("ROW"), "date" => $grp_row["date"] , "jrk" => $grp_row["jrk"] , "id" => $grp_row["id"],);

			}
		}

		foreach($bill_rows as $row)
		{
			if (!$row["is_oe"])
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
				"date" => $row["date"] ? "(".$row["date"].")" : "",
			));

			$rs[] = array("str" => $this->parse("ROW"), "date" => $row["date"] , "jrk" => $row["jrk"] , "id" => $grp_row["id"],);
			$sum_wo_tax += $cur_sum;
			$tax += $cur_tax;
			$sum += ($cur_tax+$cur_sum);
		}

		if(empty($this->show_agreement_rows))
		{
			usort($rs, array(&$this, "__br_sort"));
		}
		usort($rs, array(&$this, "__br_sort"));
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
			$conv = get_instance("core/converters/html2pdf");
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
		$this->stats = get_instance("applications/crm/crm_company_stats_impl");
		$this->bill = obj($arr["id"]);
		$bill_rows = $this->bill->get_bill_rows_data();
		$tpl_suffix = $this->bill->prop("state") == 16 ? "_offer" : "";

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

		if(isset($_GET["pdf"]))
		{
			$arr["pdf"] = $_GET["pdf"];
		}


		if(!empty($arr["pdf"]))
		{
			$tpl .= "_pdf";
			$tpl .= "_".$lc;
			if ($this->read_site_template($tpl.".tpl", true) === false)
			{
				if ($this->read_site_template("show{$tpl_suffix}_add_".$lc.".tpl", true) === false)
				{
					$this->read_site_template("show{$tpl_suffix}_add.tpl");
				}
			}
		}
		else
		{
			$tpl .= "_".$lc;
			if ($this->read_site_template($tpl.".tpl", true) === false)
			{
				$this->read_site_template("show{$tpl_suffix}_add.tpl");
			}
		}
	//templeidi valik l6pp

		$ord = obj();
		$currency = $this->bill->get_bill_currency_id();
		$cur = obj($currency);
		if ($this->can("view", $this->bill->prop("customer")))
		{
			$ord = obj($this->bill->prop("customer"));
			$_ord_ct = $ord->prop("firmajuht");
			$ord_ct = "";
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
				$aps = join(", ", $ap)."<br>";
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
				$bpct = $imp_vars["impl_penalty"];
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
			"comment" => $this->bill->prop("notes"),
			"comment" => $this->bill->comment(),
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
			"page_no" => $page_no,
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
		die($res);
	}

	private function parse_preview_add_rows($bill_rows)
	{
		$rs = array();
		$tot_cur_sum = $tot_amt = 0;

		foreach($bill_rows as $key => $row)
		{
			$row_data = array();
			if(empty($row["task_row_id"]) && is_array($row["task_rows"]))
			{
				$row["task_row_id"] = reset($row["task_rows"]);
			}
			$row_data["task_row_id"] = $row["task_row_id"];
			$row_data["orderer"] = isset($row["orderer"]) ? $row["orderer"] : null;
			if (!empty($row["is_oe"]))
			{
				continue;
			}
			$tax_rate = (double) $row["tax"] / 100.0;
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
					"desc" => $row["name"],
					"date" => isset($row["date"]) ? $row["date"] : "",
					"row_orderer" => isset($row["orderer"]) ? $row["orderer"] : "",
					"comment" => isset($row["comment"]) ? $row["comment"] : "",
					"oid" => isset($row["oid"]) ? $row["oid"] : "",
					"row_tax" => $cur_tax
				));
				$rs[] = array("str" => $this->parse("ROW"), "date" => $row["date"] , "jrk" => $row["jrk"], "id" => $row["id"]);
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
			if($row["quality"])
			{
				$o->create_brother($row["quality"]);
			}

			$o->set_prop("name", $row["name"]);
			$o->set_prop("comment", $row["comment"]);
			$o->set_prop("date", $row["date"]);
			$o->set_prop("unit", $unit);
			$o->set_meta("jrk", $row["jrk"]);
			$o->set_prop("price", str_replace(",", ".", $row["price"]));
			$o->set_prop("amt", str_replace(",", ".", $row["amt"]));
			$o->set_prop("sum", str_replace(",", ".", $row["sum"]));
			$o->set_prop("prod", $row["prod"]);
			$o->set_prop("has_tax", (int)$row["has_tax"]);
			$o->set_prop("people", $row["person"]);
			if($row["has_tax"])
			{
				$o->set_prop("tax", $o->get_row_tax(1));
			}

			$o->save();
		}

		//summa igeks
		if(is_array($arr["request"]["agreement_price"]))
		{
			$this->set_current_settings();
			foreach($arr["request"]["agreement_price"] as $key => $agreement_price)
			{
				//comment on see mis nagu nitama hakkab... et paneb selle samaks mis nimi

				$arr["request"]["agreement_price"][$key]["comment"] = $agreement_price["name"];
				//vaikimisi artikkel ka

				if ($this->crm_settings && !$arr["request"]["agreement_price"][$key]["prod"])
				{
					$arr["request"]["agreement_price"][$key]["prod"] = $this->crm_settings->prop("bill_def_prod");
				}
				$arr["request"]["agreement_price"][$key]["sum"] = str_replace("," , "." , $arr["request"]["agreement_price"][$key]["price"])*str_replace("," , "." , $arr["request"]["agreement_price"][$key]["amt"]);
				if(!$arr["request"]["agreement_price"][$key]["price"] && !(strlen($arr["request"]["agreement_price"][$key]["name"]) > 1) && empty($arr["request"]["agreement_price"][$key]["atm"]))
				{
					unset($arr["request"]["agreement_price"][$key]);
				}
				if(isset($arr["request"]["agreement_price"][$key]["prod"]))
				{
					$tmp = explode("(", $arr["request"]["agreement_price"][$key]["prod"]);
					$tmp2 = explode(")", $tmp[count($tmp)-1]);
					$prod = $tmp2[0];
					$arr["request"]["agreement_price"][$key]["prod"] = $prod;
				}
			}
		}
		$arr["obj_inst"]->set_meta("agreement_price", $arr["request"]["agreement_price"]);
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
		$url = $this->mk_my_orb("get_comment_for_prod");
		return '
			function add_row()
			{
				$.get("/automatweb/orb.aw", {class: "crm_bill", action: "add_row", id: "'.$arr["obj_inst"]->id().'"}, function (html) {
					reload_property("bill_rows");
				});
			}
			function edit_row(id)
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
				//aw_do_xmlhttprequest("'.$url.'&prod="+document.changeform.gen_prod.options[document.changeform.gen_prod.selectedIndex].value, notes_fetch_callb);
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
						alert("There was a problem retrieving the XML data:\n" + req.statusText);
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

		';
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

	function _bill_tb($arr)
	{

		$tb =& $arr["prop"]["vcl_inst"];
		$this->set_current_settings();
		$has_val = !$arr["obj_inst"]->has_not_initialized_rows();
		$onclick_start = empty($has_val) ? " fRet = confirm('".t("Arvel on ridu, mille v&auml;&auml;rtus on 0 krooni")."');	if(fRet){" : "";
		$onclick_end = empty($has_val) ? "}else;" : "";

		$tb->add_menu_button(array(
			"name" => "new",
			"tooltip" => t("Uus"),
			"img" => "new.gif"
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"url" => "javascript:;",
			"text" => t("Lisa t&uuml;hi rida"),
			"onClick" => "add_row();",
		));
		$tb->add_menu_item(array(
			"parent" => "new",
			"url" => "#",
			"onClick" => "win = window.open('".$this->mk_my_orb("bug_search", array("is_popup" => 1, "customer" => $arr["obj_inst"]->get_bill_customer()), CL_CRM_BILL)."','bug_search','width=720,height=600,statusbar=yes, scrollbars=yes ');",
			"text" => t("Lisa arendus&uuml;lesanne")
		));

		$tb->add_save_button();

		$tb->add_menu_button(array(
			"name" => "print",
			"tooltip" => t("Prindi"),
			"img" => "print.gif"
		));
		exit_function("bill_tb_init");

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

		$tb->add_menu_button(array(
			"name" => "send_bill",
			"tooltip" => t("Saada arve"),
			"img" => "mail_send.gif",
		));

		$onclick= "win = window.open('".$this->mk_my_orb("send_bill", array(
			"id" => $arr["obj_inst"]->id(),), CL_CRM_BILL)."','billprint','width=800,height=600,statusbar=yes');";
		$tb->add_menu_item(array(
			"parent" => "send_bill",
			"url" => "#",
			"onClick" => $onclick,
			"text" => t("Saada arve pdf")
		));

		$onclick= "win = window.open('".$this->mk_my_orb("send_bill", array(
			"id" => $arr["obj_inst"]->id(),"preview_add" => 1), CL_CRM_BILL)."','billprint','width=800,height=600,statusbar=yes');";
		$tb->add_menu_item(array(
			"parent" => "send_bill",
			"url" => "#",
			"onClick" => $onclick,
			"text" => t("Saada arve pdf koos lisaga")
		));

		$onclick= "win = window.open('".$this->mk_my_orb("send_bill", array(
			"id" => $arr["obj_inst"]->id(),
			"reminder" => 1
		), CL_CRM_BILL)."','billprint','width=800,height=600,statusbar=yes');";
		$tb->add_menu_item(array(
			"parent" => "send_bill",
			"url" => "#",
			"onClick" => $onclick,
			"text" => t("Saada arve meeldetuletuse pdf")
		));

		$onclick= "win = window.open('".$this->mk_my_orb("send_bill", array(
			"id" => $arr["obj_inst"]->id(),
			"preview_add" => 1,
			"reminder" => 1,
			), CL_CRM_BILL)."','billprint','width=800,height=600,statusbar=yes');";
		$tb->add_menu_item(array(
			"parent" => "send_bill",
			"url" => "#",
			"onClick" => $onclick,
			"text" => t("Saada arve meeldetuletuse pdf koos lisaga")
		));

		$tb->add_menu_item(array(
			"parent" => "send_bill",
			"url" => $this->mk_my_orb("change", array(
				"id" => $arr["obj_inst"]->id(),
				"group" => "bill_mail"), CL_CRM_BILL),
			"text" => t("Kirjade seaded")
		));

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

		if(!$this->crm_settings || !$this->crm_settings->prop("bill_hide_cr"))
		{
			$tb->add_button(array(
				"name" => "reconcile",
				"tooltip" => t("Koonda read"),
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
		if(!$arr["new"])
		{
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
		}
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


	function _init_bill_task_list(&$t)
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

	function _bill_task_list($arr)
	{
		$t =& $arr["prop"]["vcl_inst"];
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
	}

	function _billt_tb($arr)
	{
		$tb = $arr["prop"]["vcl_inst"];

		$tb->add_button(array(
			"name" => "remove_from_bill",
			"img" => "delete.gif",
			"tooltip" => t("Eemalda arve k&uuml;ljest"),
			"confirm" => t("Oled kindel et soovid read eemaldada?"),
			"action" => "remove_rows_from_bill"
		));
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
		if (is_array($arr["sel_rows"]) && count($arr["sel_rows"]) > 1)
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
			"lang_id" => array(),
			"site_id" => array(),
			"bill_id" => $o->id()
		));
		foreach($ol->arr() as $tr)
		{
			$tr->set_prop("bill_id", 0);
			$tr->save();
		}
	}

	function callback_on_load()
	{
		if(isset($_SESSION["bill_error"]))
		{
			$this->error = $_SESSION["bill_error"];
			unset($_SESSION["bill_error"]);
		}
	}

	function callback_mod_retval($arr)
	{
		if(isset( $arr["request"]["project"])) $arr["args"]["project"] = $arr["request"]["project"];

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
		if ($arr["id"] == "preview_w_rows")
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
		@attrib name=send_bill api=1 all_args=1
	@param id required type=int
		bill id
	@param preview_add optional type=int
	@param preview_add_pdf optional type=int
	@param reminder optional type=int
	@param preview_pdf optional type=int
	@returns int
		bill id
	**/
	function send_bill($arr)
	{
		$obj = obj($arr["id"]);

		if(!empty($arr["preview_pdf"]))
		{
			$preview_add_pdf = isset($arr["preview_add_pdf"]) ? $arr["preview_add_pdf"] : null;//XXX: mis tegelik default?
			$obj->send_bill($arr["preview_pdf"], $preview_add_pdf);
			die();
		}

		$attatchments = "";

		if(!empty($arr["reminder"]))
		{
			$to_o = $obj->make_reminder_pdf();
		}
		else
		{
			$to_o = $obj->make_preview_pdf();
		}
		$file_data = $to_o->get_file();
		$attatchments.= html::href(array(
			"caption" => html::img(array(
				"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
				"border" => 0,
			)).$to_o->name()." (".filesize($file_data["properties"]["file"])." B)",
			"url" => $to_o->get_url(),
		));

		$data = array(
			"preview_pdf" => $to_o->id(),
			"orb_class" => $_GET["class"]?$_GET["class"]:$_POST["class"],
			"reforb" => 0,
			"id" => $obj->id(),
		);

		if(!empty($arr["preview_add"]))
		{
			$to_o2 = $obj->make_add_pdf();
			$file_data = $to_o2->get_file();
			$attatchments.= "<br>".html::href(array(
				"caption" => html::img(array(
					"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
					"border" => 0,
				)).$to_o2->name()." (".filesize($file_data["properties"]["file"])." B)",
				"url" => $to_o2->get_url(),
			));
			$data["preview_add_pdf"] = $to_o2->id();
		}

		$targets = $obj->get_mail_targets();

		$htmlc = new htmlclient();
		$htmlc->start_output();

		$htmlc->add_property(array(
			"name" => "to",
			"type" => "text",
			"value" => implode("<br />", $targets),
			"value" => str_replace("____[AWCRMBILLLINEBREAKTOKEN]____", "<br />", htmlspecialchars(implode("____[AWCRMBILLLINEBREAKTOKEN]____", $targets))),
			"caption" => t("To")
		));

		$htmlc->add_property(array(
			"name" => "bcc",
			"type" => "text",
			"value" => str_replace("____[AWCRMBILLLINEBREAKTOKEN]____", "<br />", htmlspecialchars(implode("____[AWCRMBILLLINEBREAKTOKEN]____", $obj->get_bcc()))),
			"caption" => t("Bcc")
		));

		$htmlc->add_property(array(
			"name" => "from",
			"type" => "text",
			"value" => htmlspecialchars($obj->get_mail_from_name()." <".$obj->get_mail_from().">"),
			"caption" => t("From"),
		));

		$htmlc->add_property(array(
			"name" => "subject",
			"type" => "text",
			"value" => $obj->get_mail_subject(),
			"caption" => t("Subject"),
		));

		$htmlc->add_property(array(
			"name" => "body",
			"type" => "text",
			"value" => $obj->get_mail_body(),
			"caption" => t("Contents"),
		));

		$htmlc->add_property(array(
			"name" => "Attachments",
			"type" => "text",
			"value" => $attatchments,
			"caption" => t("Attachments"),
		));

		$htmlc->add_property(array(
			"name" => "sub",
			"type" => "button",
			"value" => t("Send!"),
			"onclick" => "fRet = confirm('".t("Kas olete kindel et soovite arve saata?")."');if(fRet){
				changeform.submit();
				}else;",
			"caption" => t("Send!")
		));

		$htmlc->finish_output(array(
			"action" => "send_bill",
			"method" => "POST",
			"data" => $data,
			"submit" => "no"
		));

		$content = $htmlc->get_result();
		return $content;
//		$obj->send_bill($arr["preview_add"]);
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

		$htmlc = get_instance("cfg/htmlclient");
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
		classload("vcl/table");
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
			"lang_id" => array(),
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

	function _get_mail_table($arr)
	{
//Saaja isikute nimed, asutused, telefon laual ja mobiil, ametinimetus, meilidaadressid; arve summa, arve laekumise t2htaeg; arve staatus.
		$t = &$arr["prop"]["vcl_inst"];
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

	function _get_dn_confirm_tbl($arr)
	{
		if($arr["new"])
		{
			return PROP_IGNORE;
		}
		$t = &$arr["prop"]["vcl_inst"];

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
		$id = obj($arr["id"])->make_overdue_bill();
		if(is_oid($id))
		{
			return  $this->mk_my_orb("change", array("id" => $id, "return_url" => $arr["ru"]), CL_CRM_BILL);
		}
		else
		{
			return $arr["ru"];
		}
	}
}
?>
