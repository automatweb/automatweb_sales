<?php

/*
Customer relations are per contract, meaning provision
and acquisition contracts are represented in separate customer relation objects

@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1

@tableinfo aw_crm_customer_data index=aw_oid master_index=brother_of master_table=objects

@default table=objects
@default group=general

	@property buyer type=relpicker reltype=RELTYPE_BUYER table=aw_crm_customer_data field=aw_buyer
	@caption Ostja

	@property seller type=relpicker reltype=RELTYPE_SELLER table=aw_crm_customer_data field=aw_seller
	@caption M&uuml;&uuml;ja

	@property discount type=textbox table=aw_crm_customer_data field=aw_discount
	@caption Vaikimisi soodustus %

	@property order_frequency type=textbox table=aw_crm_customer_data field=aw_order_frequency
	@caption Tellimuste sagedus p&auml;evades

	@property active_client table=aw_crm_customer_data field=aw_active_client type=checkbox
	@caption Aktiivne klient

	@property authorized_person_control table=aw_crm_customer_data field=aw_authorized_person_control type=checkbox
	@caption Volitatud isiku kontroll

	@property sell_alert type=textarea cols=40 rows=5 table=objects table=aw_crm_customer_data field=aw_sell_alert
	@caption Hoiatus m&uuml;&uuml;gil

	@property tax_rate type=relpicker reltype=RELTYPE_TAX_RATE store=connect
	@caption M&uuml;&uuml;gi KM-kood

	@property categories type=relpicker reltype=RELTYPE_CATEGORY store=connect multiple=1 size=7
	@caption Kliendikategooria(d)

	@property show_in_webview type=checkbox ch_value=1 field= table=aw_crm_customer_data field=aw_show_in_webview
	@caption Kuva veebis



@groupinfo buyer caption="Ostja"
@default group=buyer
	@property buyer_contract_creator type=select table=aw_crm_customer_data field=aw_buyer_cust_contract_creator
	@caption Hankijasuhte looja

	//DEPRECATED. use 'cust_contract_date' property instead. Customer relations are per contract, meaning provision
	// and acquisition contracts are represented in separate customer relation objects
	@property buyer_contract_date type=date_select table=aw_crm_customer_data field=aw_buyer_cust_contract_date
	@caption Hankijasuhte alguskuup&auml;ev

	@property buyer_contact_person type=relpicker reltype=RELTYPE_CONTACT_PERSON table=aw_crm_customer_data field=aw_buyer_contact_person1
	@caption Ostja kontaktisik 1

	@property buyer_contact_person2 type=relpicker reltype=RELTYPE_CONTACT_PERSON table=aw_crm_customer_data field=aw_buyer_contact_person2
	@caption Ostja kontaktisik 2

	@property buyer_contact_person3 type=relpicker reltype=RELTYPE_CONTACT_PERSON table=aw_crm_customer_data field=aw_buyer_contact_person3
	@caption Ostja kontaktisik 3

	@property buyer_priority type=textbox table=aw_crm_customer_data field=aw_buyer_priority
	@caption Ostja Prioriteet

	@property bill_person type=relpicker reltype=RELTYPE_BILL_PERSON store=connect multiple=1
	@caption Arve saajad


@groupinfo seller caption="M&uuml;&uuml;ja"
@default group=seller
	@property cust_contract_creator type=select table=aw_crm_customer_data field=aw_cust_contract_creator
	@caption Kliendisuhte looja

	@property cust_contract_date type=date_select table=aw_crm_customer_data field=aw_cust_contract_date
	@caption Kliendisuhte alguskuup&auml;ev

	@property contact_person type=relpicker reltype=RELTYPE_CONTACT_PERSON table=aw_crm_customer_data field=aw_contact_person1
	@caption Kliendi kontaktisik 1

	@property contact_person2 type=relpicker reltype=RELTYPE_CONTACT_PERSON table=aw_crm_customer_data field=aw_contact_person2
	@caption Kliendi kontaktisik 2

	@property contact_person3 type=relpicker reltype=RELTYPE_CONTACT_PERSON table=aw_crm_customer_data field=aw_contact_person3
	@caption Kliendi kontaktisik 3

	@property priority type=textbox table=aw_crm_customer_data field=aw_priority
	@caption Kliendi Prioriteet

	@property referal_type type=classificator table=aw_crm_customer_data field=aw_referal_type reltype=RELTYPE_REFERAL_TYPE
	@caption Sissetuleku meetod

	@property client_manager type=relpicker reltype=RELTYPE_CLIENT_MANAGER table=aw_crm_customer_data field=aw_client_manager
	@caption Kliendihaldur

	@property bill_due_date_days type=textbox size=5  table=aw_crm_customer_data field=aw_bill_due_date_days
	@caption Makset&auml;htaeg (p&auml;evi)

	@property bill_tolerance type=textbox size=5  table=aw_crm_customer_data field=aw_bill_tolerance
	@caption Tolerants (p&auml;evi)

	@property bill_penalty_pct type=textbox size=5  table=aw_crm_customer_data field=aw_bill_penalty_pct
	@caption Viivise %


@groupinfo sales_data caption="M&uuml;&uuml;giinfo"
@default group=sales_data
@layout splitbox1 type=hbox width=50%:50%
@layout leftbox type=vbox parent=splitbox1 area_caption=M&uuml;&uuml;giinfo
@layout rightbox type=vbox parent=splitbox1 area_caption=Kommentaarid

	@property sales_customer_info type=text store=no parent=leftbox
	@caption Kliendi andmed

	@property sales_state type=select table=aw_crm_customer_data field=aw_sales_status datatype=int default=1 parent=leftbox
	@caption M&uuml;&uuml;gi staatus

	@property salesman type=relpicker reltype=RELTYPE_SALESMAN table=aw_crm_customer_data field=aw_salesman datatype=int parent=leftbox
	@caption M&uuml;&uuml;giesindaja

	@property sales_lead_source type=relpicker reltype=RELTYPE_SALES_LEAD_SOURCE store=connect parent=leftbox
	@caption Soovitaja/allikas

	// a cache for the last call object associated with this customer relation
	@property sales_last_call type=hidden table=aw_crm_customer_data field=aw_sales_last_call datatype=int parent=leftbox

	// a cache for the count of call objects associated with this customer relation
	@property sales_calls_made type=text table=aw_crm_customer_data field=aw_sales_calls_made datatype=int parent=leftbox
	@caption Tehtud m&uuml;&uuml;gik&otilde;nesid

	@property sales_presentations_made type=text table=aw_crm_customer_data field=aw_sales_presentations_made datatype=int parent=leftbox
	@caption Tehtud esitlusi

	@property sales_comments type=comments table=objects field=meta method=serialize parent=rightbox
	@caption M&uuml;&uuml;gikommentaarid


@groupinfo users caption="Kasutajad"
@default group=users

	@property users_tbl type=table no_caption=1
	@caption Kasutajate tabel


@groupinfo campaign caption="Kampaaniad"
@default group=campaign

	@property campaign_tbl type=table no_caption=1
	@caption Kampaaniate tabel

@groupinfo prices caption="Hinnakirjad"
@default group=prices

	@property prices_tbl type=table no_caption=1
	@caption Hinnakirjade tabel

@groupinfo bills caption="Arved"
@default group=bills

	@property bills_tbl type=table no_caption=1
	@caption Arvete tabel

@groupinfo orders caption="Tellimused"
@default group=orders

	@property orders_tbl type=table no_caption=1
	@caption Tellimuste tabel

@groupinfo recalls caption="Tagastused"
@default group=recalls

	@property recalls_tbl type=table no_caption=1
	@caption Tagastuste tabel

@groupinfo delivery_notes caption="Saatelehed"
@default group=delivery_notes

	@property delivery_notes_tbl type=table no_caption=1
	@caption Saatelehtede tabel




@reltype BUYER value=1 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Ostja

@reltype SELLER value=2 clid=CL_CRM_COMPANY
@caption M&uuml;&uuml;ja

@reltype CONTACT_PERSON value=3 clid=CL_CRM_PERSON
@caption Kontaktisik

@reltype CONTACT_TRANSPORT value=4 clid=CL_TRANSPORT_TYPE
@caption Transpordiliik

@reltype TAX_RATE value=5 clid=CL_CRM_TAX_RATE
@caption M&uuml;&uuml;gi KM-kood

@reltype SHIPMENT_CONDITION value=6 clid=CL_CRM_SHIPMENT_CONDITION
@caption L&auml;hetustingimus

@reltype CLIENT_MANAGER value=34 clid=CL_CRM_PERSON
@caption Kliendihaldur

@reltype SALESMAN value=8 clid=CL_CRM_PERSON
@caption M&uuml;&uuml;giesindaja

@reltype EXT_SYS_ENTRY value=35 clid=CL_EXTERNAL_SYSTEM_ENTRY
@caption Siduss&uuml;steemi sisestus

@reltype REFERAL_TYPE value=41 clid=CL_META
@caption Sissetuleku meetod

@reltype STATUS value=69 clid=CL_CRM_COMPANY_STATUS
@caption Kliendikategooria

@reltype COMMENT_TO_COMPANY value=75 clid=CL_COMMENT
@caption Kommentaar organisatsioonile

@reltype BILL_PERSON value=7 clid=CL_CRM_PERSON
@caption Arve saaja

// isik, firma v6i kampaania
@reltype SALES_LEAD_SOURCE value=101 clid=CL_CRM_COMPANY,CL_CRM_PERSON
@caption Soovitaja/allikas

//
@reltype CATEGORY value=102 clid=CL_CRM_CATEGORY
@caption Kliendikategooria

*/

class crm_company_customer_data extends class_base
{
	function crm_company_customer_data()
	{
		$this->init(array(
			"tpldir" => "applications/crm/crm_company_customer_data",
			"clid" => CL_CRM_COMPANY_CUSTOMER_DATA
		));
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
			case "cust_contract_creator":
				// list of all persons in my company
				$co = get_current_company();
				$i = new crm_company();
				$arr["prop"]["options"] = $i->get_employee_picker($co, true);
				break;

			case "buyer_contract_creator":
				$buyer = obj($arr["obj_inst"]->prop("buyer"));
				if ($buyer->is_a(CL_CRM_PERSON))
				{ // case for natural person
					$arr["prop"]["options"] = array($buyer->id() => $buyer->name());
					$arr["prop"]["value"] = $buyer->id();
				}
				elseif ($buyer->is_a(CL_CRM_COMPANY))
				{ // buyer is a legal person
					// list of all persons in my company
					$i = get_instance(CL_CRM_COMPANY);
					$arr["prop"]["options"] = $i->get_employee_picker($buyer, true);
				}
				break;

			case "referal_type":
				$c = new classificator();
				$prop["options"] = array("" => t("--vali--")) + $c->get_options_for(array(
					"name" => "referal_type",
					"clid" => CL_CRM_COMPANY
				));
				break;

			case "contact_person":
			case "contact_person2":
			case "contact_person3":
				if (!$this->can("view", $arr["obj_inst"]->prop("buyer")))
				{
					return PROP_IGNORE;
				}

				$buyer = obj($arr["obj_inst"]->prop("buyer"));
				if ($buyer->is_a(CL_CRM_PERSON))
				{ // natural persons have themselves as contact person
					$arr["prop"]["options"] = array($buyer->id() => $buyer->name());
					$arr["prop"]["value"] = $buyer->id();
				}
				elseif ($buyer->is_a(CL_CRM_COMPANY))
				{
					$i = get_instance(CL_CRM_COMPANY);
					$arr["prop"]["options"] = $i->get_employee_picker($buyer, true);

					if (isset($prop["options"]) && !isset($prop["options"][$prop["value"]]) && $this->can("view", $prop["value"]))
					{
						$tmp = obj($prop["value"]);
						$prop["options"][$prop["value"]] = $tmp->name();
					}
				}
				break;

			case "client_manager":
				$i = new crm_company();
				$prop["options"] = $i->get_employee_picker(get_current_company(), true);
				break;
		}
		return $retval;
	}

	function _get_sales_customer_info(&$arr)
	{
		$customer = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_BUYER");

		if ($customer)
		{
			$arr["prop"]["value"] = nl2br(sprintf(t('Nimi: %s
Telefon(id): %s
Aadress: %s
			'), $customer->name(), implode(", ", $customer->get_phones()), $customer->get_address_string()));
		}
	}

	function _get_campaign_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "left",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));

		$t->define_field(array(
			"name" => "discount",
			"caption" => t("Allahindluse %"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "date",
			"caption" => t("Kestus"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "active",
			"caption" => t("Kehtib"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "product",
			"caption" => t("Toode/kategooria"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"sortable" => 1,
			"name" => "groups",
			"caption" => t("Kasutajaruppidele"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_chooser(array(
			"name" => "sel",
			"field" => "oid",
			"chgbgcolor" => "color",
		));

		$discounts = $arr["obj_inst"]->get_discounts();
		foreach($discounts as $id => $data)
		{
			$change = ($arr["request"]["change_discount_id"] == $id) ? 1 : 0;
			$def = array(
				"name" => $data["name"],
				"discount" => $data["discount"],
				"active" => $data["active"] ? t("Aktiivne") : t("Mitteaktiivne"),
				"oid" => $id,
			);
			if($change)
			{
				$def["name"] = html::textbox(array("name" => "name" , "value" => $data["name"]));
			}
			$group_names = get_name($data["apply_groups"]);
			$def["groups"] = is_array($group_names) ? join(", " , $group_names) : "";
			if(is_oid($data["object"]))
			{
				$def["product"] = get_name($data["object"]);
			}
			if($data["from"] || $data["to"])
			{
				$def["date"] = "";
				if($data["from"] > 0)
				{
					$def["date"].= date("d.m.Y" , $data["from"]);
				}
				if($data["from"] > 0 && $data["to"] > 0)
				{
					$def["date"].= " - ";
				}
				if($data["to"] > 0)
				{
					$def["date"].= date("d.m.Y" , $data["to"]);
				}
			}
			$t->define_data($def);
		}
	}

	function _get_sales_state(&$arr)
	{
		$arr["prop"]["options"] = $arr["obj_inst"]->sales_state_names();
	}

	function _get_prices_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "left",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));
		$price_lists = $arr["obj_inst"]->get_price_lists();
		foreach($price_lists->arr() as $pl)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($pl),
			));
		}
	}


	function _get_bills_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "bill_no",
			"caption" => t("Number"),
			"sortable" => 1,
			"numeric" => 1,
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "bill_date",
			"caption" => t("Kuup&auml;ev"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1,
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "bill_due_date",
			"caption" => t("Makset&auml;htaeg"),
			"type" => "time",
			"format" => "d.m.Y",
			"numeric" => 1,
			"sortable" => 1,
			"chgbgcolor" => "color",
		));
		$t->define_field(array(
			"name" => "payment_over_date",
			"caption" => t("<a href='javascript:void(0)' alt='Maksega hilinenud p&auml;evade arv' title='Maksega hilinenud p&auml;evade arv'>MHPA</a>"),
			"align" => "center",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "customer",
			"caption" => t("Klient"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "client_manager",
			"caption" => t("Kliendihaldur"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "project_leader",
			"caption" => t("Projektijuht"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right",
			"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "balance",
			"caption" => t("Arve saldo"),
			"sortable" => 1,
			"numeric" => 1,
			"align" => "right",
		"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"sortable" => 1,
		"chgbgcolor" => "color",
		));

		$t->define_field(array(
			"name" => "print",
			"caption" => t("Prindi"),
			"sortable" => 1,
			"chgbgcolor" => "color",
		));

		$bills = $arr["obj_inst"]->get_bills();


		$this->show_bill_balance = 1;
		$cg = 1;
		$d = get_instance("applications/crm/crm_data");
		$bill_i = new crm_bill();
		$curr_inst = new currency();
		$co_stat_inst = new crm_company_stats_impl();
		$pop = new popup_menu();

		$company_curr = $co_stat_inst->get_company_currency();

		if ($arr["request"]["export_hr"] > 0)
		{
			if (is_array($arr["request"]["bi"]) && count($arr["request"]["bi"]))
			{
				$bills = new object_list();
				$bills->add($arr["request"]["bi"]);
			}
			$this->_do_export_hr($bills, $arr, $arr["request"]["export_hr"]);
		}

		$sum_in_curr = $bal_in_curr = array();
		$balance = 0;
		foreach($bills->arr() as $bill)
		{
			$cust = "";
			$cm = "";
			$payments_total = 0;
			if (is_oid($customer_id = $bill->get_bill_customer()))
			{
				$tmp = obj($customer_id);
				$cust = $tmp->name() ?  html::get_change_url($tmp->id(), array("return_url" => get_ru()), ($tmp->prop("short_name") ? $tmp->prop("short_name") : $tmp->name()) , $tmp->name()) : "";
				$cm = html::obj_change_url($tmp->prop("client_manager"));
			}
			$state = $bill_i->states[$bill->prop("state")];


			$cursum = $own_currency_sum = $bill->get_bill_sum();//$bill_i->get_bill_sum($bill,$tax_add);
			$curid = $bill->get_bill_currency_id();
			$cur_name = $bill->get_bill_currency_name();
			if($company_curr && $curid && ($company_curr != $curid))
			{
				$own_currency_sum  = $co_stat_inst->convert_to_company_currency(array(
					"sum" =>  $cursum,
					"o" => $bill,
				));
			}

			if($cg)//kliendi valuutas
			{
				$sum_str = number_format($cursum, 2)." ".$cur_name;
				$sum_in_curr[$cur_name] += $cursum;
			}
			else//oma organisatsiooni valuutas
			{
				$sum_str = number_format($own_currency_sum, 2);
			}

			$pop->begin_menu("bill_".$bill->id());
			$pop->add_item(Array(
				"text" => t("Prindi arve"),
				"link" => "#",
				"oncl" => "onClick='window.open(\"".$this->mk_my_orb("change", array("openprintdialog" => 1,"id" => $bill->id(), "group" => "preview"), CL_CRM_BILL)."\",\"billprint\",\"width=100,height=100\");'"
			));
			$pop->add_item(Array(
				"text" => t("Prindi arve lisa"),
				"link" => "#",
				"oncl" => "onClick='window.open(\"".$this->mk_my_orb("change", array("openprintdialog" => 1,"id" => $bill->id(), "group" => "preview_add"), CL_CRM_BILL)."\",\"billprintadd\",\"width=100,height=100\");'"
			));
			$pop->add_item(array(
				"text" => t("Prindi arve koos lisaga"),
				"link" => "#",
				"oncl" => "onClick='window.open(\"".$this->mk_my_orb("change", array("openprintdialog_b" => 1,"id" => $bill->id(), "group" => "preview"), CL_CRM_BILL)."\",\"billprintadd\",\"width=100,height=100\");'"
			));
			$partial = "";
			if($bill->prop("state") == 3 && $bill->prop("partial_recieved") && $bill->prop("partial_recieved") < $cursum)
			{
				$partial = '<br>'.t("osaliselt");
			}
			$bill_data = array(
				"bill_no" => html::get_change_url($bill->id(), array("return_url" => get_ru()), parse_obj_name($bill->prop("bill_no"))),
				"create_new" => html::href(array(
					"url" => $this->mk_my_orb("create_new_monthly_bill", array(
						"id" => $bill->id(),
						"co" => $arr["obj_inst"]->id(),
						"post_ru" => get_ru()
						), CL_CRM_COMPANY),
					"caption" => t("Loo uus")
				)),
				"bill_date" => $bill->prop("bill_date"),
				"bill_due_date" => $bill->prop("bill_due_date"),
				"customer" => $cust,
				"state" => $state.$partial,
				"sum" => $sum_str,
				"client_manager" => $cm,
				"oid" => $bill->id(),
				"print" => $pop->get_menu(),
			);

			if($bill->prop("state") == 1)
			{
				$bill_data["payment_over_date"] = $bill->get_payment_over_date();
				$tolerance = $arr["obj_inst"]->get_customer_prop($bill->prop("customer"), "bill_tolerance");
				if($bill_data["payment_over_date"] > $tolerance)
				{
					$bill_data["color"] = "#FF9999";
				}
			}

			$project_leaders = $bill->project_leaders();
			if($project_leaders->count())
			{
				$pl_array = array();
				foreach($project_leaders->arr() as $pl)
				{
					$pl_array[] = html::href(array(
						"caption" => $pl->name(),
						"url" => html::obj_change_url($pl , array())
					));
				}
				$bill_data["project_leader"] = join("<br>" , $pl_array);
			}

			if($arr["request"]["show_bill_balance"])
			{
				$curr_balance = $bill->get_bill_needs_payment();
				if($company_curr && $curid && ($company_curr != $curid))
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
			}

			$t->define_data($bill_data);

			// number_format here to round the number the same way in the add, so the sum is correct
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
			if($arr["request"]["show_bill_balance"])
			{
				$final_dat["balance"] .= "<b>Kokku: ".number_format($balance, 2).$co_currency_name."</b><br>";
			}
		}
		else
		{
			$final_dat["sum"] = "<b>".number_format($sum, 2)."</b>";
			if($arr["request"]["show_bill_balance"])
			{
				$final_dat["balance"] .= "<b>".number_format($balance, 2)."</b><br>";
			}
		}
		$t->define_data($final_dat);

	}

	function _get_delivery_notes_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "no",
			"caption" => t("Number"),
			"sortable" => 1,
			"align" => "right",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1,
			"align" => "right",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));

		$t->define_field(array(
			"name" => "currency",
			"caption" => t("Valuuta"),
			"sortable" => 1,
			"align" => "cright",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));
		$sum = 0;
		$delivery_notes = $arr["obj_inst"]->get_delivery_notes();
		foreach($delivery_notes->arr() as $delivery_note)
		{
			$sum+= $delivery_note -> get_sum();
			$t->define_data(array(
				"no" => html::obj_change_url($delivery_note,$delivery_note -> prop("number")),
				"sum" => $delivery_note -> get_sum(),
				"date" => date("d.m.Y" , $delivery_note -> prop("enter_date")),
				"currency" => get_name($delivery_note->prop("currency")),
			));
		}
		$t->set_sortable(false);
		$t->define_data(array(
			"no" => t("Kokku"),
			"sum" => $sum,
		));
	}


	function _get_orders_tbl($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "no",
			"caption" => t("Number"),
			"sortable" => 1,
			"align" => "right",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));

		$t->define_field(array(
			"name" => "date",
			"caption" => t("Kuup&auml;ev"),
			"sortable" => 1,
			"align" => "center",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));

		$t->define_field(array(
			"name" => "sum",
			"caption" => t("Summa"),
			"sortable" => 1,
			"align" => "right",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));

		$t->define_field(array(
			"name" => "currency",
			"caption" => t("Valuuta"),
			"sortable" => 1,
			"align" => "cright",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));
		$sum = 0;
		$orders = $arr["obj_inst"]->get_sell_orders();
		foreach($orders->arr() as $order)
		{
			$sum+= $order -> get_sum();
			$t->define_data(array(
				"no" => html::obj_change_url($order , ($order -> prop("number") ? $order -> prop("number") : $order -> id())),
				"sum" => $order -> get_sum(),
				"date" => date("d.m.Y" , $order -> prop("date")),
				"currency" => get_name($order->prop("currency")),
			));
		}
		$t->set_sortable(false);
		$t->define_data(array(
			"no" => t("Summa"),
			"sum" => $sum,
		));
	}

	function _get_recalls_tbl($arr)
	{
		$t = &$arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
			"sortable" => 1,
			"align" => "left",
			"chgbgcolor" => "color",
			"colspan" => "colspan",
		));
		$recalls = $arr["obj_inst"]->get_recalls();
		foreach($recalls->arr() as $recall)
		{
			$t->define_data(array(
				"name" => html::obj_change_url($recall),
			));
		}
	}

	function do_db_upgrade($tbl, $fld, $q, $err)
	{
		switch($fld)
		{
			case "":
				$this->db_query("CREATE TABLE `aw_crm_customer_data` (
				  `aw_oid` int(11) NOT NULL default '0',
				  `aw_buyer` int(11) default NULL,
				  `aw_seller` int(11) default NULL,
				  `aw_cust_contract_creator` int(11) default NULL,
				  `aw_cust_contract_date` int(11) default NULL,
				  `aw_contact_person1` int(11) default NULL,
				  `aw_contact_person2` int(11) default NULL,
				  `aw_contact_person3` int(11) default NULL,
				  `aw_priority` int(11) default NULL,
				  `aw_client_manager` int(11) default NULL,
				  `aw_salesman` int(11) default NULL,
				  `aw_sales_next_call` int(11) default NULL,
				  `aw_sales_status` int(11) default NULL,
				  `aw_sales_last_call` int(11) default NULL,
				  `aw_sales_calls_made` int(3) default NULL,
				  `aw_sales_presentations_made` int(3) default NULL,
				  `aw_lead_source` int(11) default NULL,
				  `aw_referal_type` int(11) default NULL,
				  PRIMARY KEY  (`aw_oid`)
				) ");
				return true;

			case "aw_bill_due_date_days":
			case "aw_buyer_cust_contract_creator":
			case "aw_buyer_cust_contract_date":
			case "aw_buyer_contact_person1":
			case "aw_buyer_contact_person2":
			case "aw_buyer_contact_person3":
			case "aw_buyer_priority":
			case "aw_active_client":
			case "aw_authorized_person_control":
			case "aw_show_in_webview":
			case "aw_bill_tolerance":
			case "aw_salesman":
			case "aw_sales_status":
			case "aw_lead_source":
			case "aw_sales_status":
			case "aw_sales_last_call":
				$this->db_add_col($tbl, array(
					"name" => $fld,
					"type" => "int(11)"
				));
				return true;

			case "aw_sales_calls_made":
			case "aw_sales_presentations_made":
				$this->db_add_col($tbl, array(
					"name" => $fld,
					"type" => "int(3)"
				));
				return true;

			case "aw_sell_alert":
				$this->db_add_col($tbl, array(
					"name" => $fld,
					"type" => "text"
				));
				return true;

			case "aw_bill_penalty_pct":
			case "aw_discount":
			case "aw_order_frequency":
				$this->db_add_col($tbl, array(
					"name" => $fld,
					"type" => "double"
				));
				return true;
		}
	}
}
