<?php
/*
@classinfo relationmgr=yes no_comment=1 no_status=1 prop_cb=1
@tableinfo aw_crm_offer master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_offer
@default group=general

	@property general_toolbar type=toolbar editonly=1 no_caption=1 store=no

	@layout general_split type=hbox width=50%:50%

		@layout general_left type=vbox parent=general_split

			@layout general_info type=vbox closeable=1 area_caption=&Uuml;ldandmed parent=general_left

				@property number type=hidden field=aw_number
				@property number_view type=text store=no parent=general_info
				@caption Number

				@property name type=textbox table=objects field=name parent=general_info
				@caption Nimi

				@property state type=select field=aw_state parent=general_info
				@caption Staatus

				@property result type=select field=aw_result parent=general_info
				@caption Tulemus

				@property result_object type=text field=aw_result_object parent=general_info
				@caption Tulemustegevus

				@property currency type=objpicker clid=CL_CURRENCY field=aw_currency parent=general_info
				@caption Valuuta

				@property price_object type=hidden field=aw_price_object
				@property sum type=hidden field=aw_sum
				@property sum_view type=text store=no parent=general_info
				@caption Summa

				@property template type=text field=aw_template parent=general_info
				@caption &Scaron;abloon, millest pakkumus genereeriti

				@property template_name type=hidden store=no editonly=1

		@layout general_right type=vbox parent=general_split

			@layout general_sales type=vbox closeable=1 area_caption=M&uuml;&uuml;gi&nbsp;andmed parent=general_right

				@property salesman type=objpicker clid=CL_CRM_PERSON field=aw_salesman parent=general_sales
				@caption M&uuml;&uuml;giesindaja nimi

			@layout general_client type=vbox closeable=1 area_caption=Kliendi&nbsp;andmed parent=general_right

				@property customer_relation type=hidden datatype=int field=aw_customer_relation
				@caption Kliendisuhe

				@property customer type=objpicker clid=CL_CRM_COMPANY,CL_CRM_PERSON field=aw_customer parent=general_client
				@caption Kliendi nimi

			@layout general_contracts type=vbox closeable=1 area_caption=Lepingud parent=general_right

				@property contracts type=chooser multiple=1 orient=vertical store=no parent=general_contracts
				@caption Lepingud

	@layout buttons type=hbox

		@property submit_button type=submit store=no parent=buttons
		@caption Salvesta

		@property save_as_template type=button store=no editonly=1 no_caption=1 parent=buttons
		@caption Salvesta &scaron;abloonina

@groupinfo content caption=Sisu
@default group=content

	@property content_add type=hidden editonly=1 store=no

	@property content_toolbar type=toolbar editonly=1 no_caption=1 store=no

	@property content_table type=table editonly=1 no_caption=1 store=no

	@property content_total_price_components type=table editonly=1 no_caption=1 store=no

@groupinfo preview caption=Eelvaade
@default group=preview

	@property preview type=text store=no no_caption=1 editonly=1

# @groupinfo confirmations caption=Kinnitused submit=no
# @default group=confirmations

#	@property confirmations_table type=table store=no no_caption=1 editonly=1

@groupinfo operations caption="Tegevused"

	@groupinfo operations_overview parent=operations caption="&Uuml;levaade" submit=no
	@default group=operations_overview

		@property operations_overview_toolbar type=toolbar store=no no_caption=1

		@layout operations_overview_split type=hbox width=20%:80%
			
			@layout operations_overview_left type=vbox parent=operations_overview_split
				
				@layout operations_overview_type_tree type=vbox parent=operations_overview_left area_caption=Tegevuse&nbsp;t&uuml;&uuml;bid

					@property operations_overview_type_tree type=treeview parent=operations_overview_type_tree store=no no_caption=1
			
			@layout operations_overview_right type=vbox parent=operations_overview_split
				
				@property operations_overview_table type=table parent=operations_overview_right store=no no_caption=1

	@groupinfo send caption="Pakkumuse saatmine" parent=operations confirm_save_data=0
	@default group=send

		@property send_toolbar type=toolbar store=no no_caption=1
		@layout send_settings type=hbox closeable=1 area_caption=Kirja&nbsp;seaded width=50%:50%

			@layout send_settings_left type=vbox parent=send_settings

			@layout send_sender type=vbox closeable=0 area_caption=Saatja parent=send_settings_left
				@property mail_from type=textbox store=no parent=send_sender
				@caption E-posti aadress

				@property mail_from_name type=textbox store=no parent=send_sender
				@caption Nimi

			@layout send_reply type=vbox closeable=0 area_caption=Vastamise&nbsp;viis&nbsp;ja&nbsp;aeg parent=send_settings_left
				@property mail_reply_method type=select store=no parent=send_reply
				@caption Vastamise viis
				
				@property mail_reply_time type=datepicker store=no parent=send_reply
				@caption Vastamise aeg

			@layout send_attachments type=vbox closeable=0 area_caption=Lisatavad&nbsp;dokumendid parent=send_settings
				@property mail_attachments type=chooser multiple=1 store=no parent=send_attachments orient=vertical no_caption=1

		@layout send_recipients type=vbox closeable=1 area_caption=Kirja&nbsp;saajad
			@property mail_recipients type=table store=no parent=send_recipients no_caption=1

			@property recipient_name type=textbox store=no parent=send_recipients
			@comment Sisesta suvaline kehtiv e-posti aadress
			@caption Lisa pakkumuse saaja

		@layout send_content type=hbox closeable=1 area_caption=Kirja&nbsp;sisu width=50%:50%
			@layout send_content_l type=vbox parent=send_content closeable=0 area_caption=Muutmine
			@layout send_content_r type=vbox parent=send_content closeable=0 area_caption=Eelvaade&nbsp;(kliki&nbsp;tekstil&nbsp;et&nbsp;uuendada)

		@property mail_subject type=textbox parent=send_content_l captionside=top table=objects field=meta method=serialize
		@caption Pealkiri

		@property mail_content type=textarea rows=20 cols=53 parent=send_content_l captionside=top table=objects field=meta method=serialize
		@caption Sisu

		@property mail_legend type=text store=no parent=send_content_l captionside=top
		@comment E-kirja sisus ja pealkirjas kasutatavad muutujad. Asendatakse saatmisel vastavate tegelike v&auml;&auml;rtustega
		@caption Kasutatavad muutujad

		@property mail_subject_view type=text parent=send_content_r store=no captionside=top
		@caption Pealkiri

		@property mail_content_view type=text store=no parent=send_content_r store=no captionside=top
		@caption Sisu

	@groupinfo sent caption="Saadetud kirjad" parent=mail submit=no
	@default group=sent

		@property sent_table type=table store=no no_caption=1 editonly=1

@reltype CONTRACT value=1 clid=CL_CRM_DEAL
@caption Leping

*/

class crm_offer extends class_base
{
	public function crm_offer()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_offer",
			"clid" => crm_offer_obj::CLID
		));
	}

	public function _get_sum_view($arr)
	{
		$arr["prop"]["value"] = $arr["obj_inst"]->sum_with_currency();
	}

	public function _get_number_view($arr)
	{
		$arr["prop"]["value"] = $arr["obj_inst"]->prop("number");
	}

	public function _get_template($arr)
	{
		if (!is_oid($template_oid = $arr["obj_inst"]->prop("template")))
		{
			return PROP_IGNORE;
		}

		$template = new object($template_oid);
		$arr["prop"]["value"] = html::obj_change_url($template);
		return PROP_OK;
	}

	public function _set_template($arr)
	{
		return PROP_IGNORE;
	}

	public function _get_mail_reply_method($arr)
	{
		$arr["prop"]["options"] = array("" => "") + crm_offer_obj::reply_names();
		$arr["prop"]["value"] = $arr["obj_inst"]->get_mail_prop("mail_reply_method");

		return PROP_OK;
	}

	public function _get_general_toolbar(&$arr)
	{
		$r = PROP_OK;

		$t = $arr["prop"]["vcl_inst"];

		$t->add_save_button();
		$t->add_button(array(
			"name" => "send",
			"img" => "mail_send.gif",
			"tooltip" => t("Saada pakkumus"),
			"url" => aw_url_change_var(array("group" => "send")),
		));

		return $r;
	}

	public function _get_operations_overview_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->add_menu_button(array(
			"name" => "new",
		));
		$url = automatweb::$request->get_uri();
		$url->set_arg("group", "send");
		$t->add_menu_item(array(
			"parent" => "new",
			"name" => "new_mail",
			"text" => t("Saada pakkumus kliendile"),
			"url" => $url->get(),
		));

		try
		{
			$create_args = array(
				"id" => automatweb::$request->get_application()->id(),
				"cust_rel" => $arr["obj_inst"]->get_customer_relation()->id(),
				"offer" => $arr["obj_inst"]->id(),
				"return_url" => get_ru(),
			);
			$t->add_menu_item(array(
				"parent" => "new",
				"name" => "new_call",
				"text" => t("Loo uus k&otilde;ne"),
				"url" => $this->mk_my_orb("create_call", $create_args, "crm_sales"),
			));
			$t->add_menu_item(array(
				"parent" => "new",
				"name" => "new_presentation",
				"text" => t("Loo uus esitlus"),
				"url" => $this->mk_my_orb("create_presentation", $create_args, "crm_sales"),
			));
		}
		catch(awex_crm_offer_customer $e)
		{
		}

		return PROP_OK;
	}

	public function _get_operations_overview_type_tree($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$url = automatweb::$request->get_uri();

		$url->unset_arg("operationTypeId");
		$t->add_item(0, array(
			"id" => "all",
			"name" => t("K&otilde;ik tegevused"),
			"url" => $url->get(),
		));
		$operations = array(
			crm_offer_sent_obj::CLID => "E-kirjad",
			crm_call_obj::CLID => "K&otilde;ned",
			crm_presentation_obj::CLID => "Esitlused",
		);
		foreach($operations as $clid => $caption)
		{
			$url->set_arg("operationTypeId", $key = "ot_".$clid);
			$t->add_item("all", array(
				"id" => $key,
				"name" => $caption,
				"url" => $url->get(),
			));
		}

		$t->set_selected_item(automatweb::$request->arg_isset("operationTypeId") ? automatweb::$request->arg("operationTypeId") : "all");
		return PROP_OK;
	}

	public function _get_operations_overview_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$this->define_operations_overview_table_header($t);

		
		$operations = $arr["obj_inst"]->get_related_operations(automatweb::$request->arg_isset("operationTypeId") ? array((int)substr(automatweb::$request->arg("operationTypeId"), 3)) : array());

		if ($operations->count() > 0)
		{
			$operation = $operations->begin();
			do
			{
				$t->define_data(array(
					"name" => html::obj_change_url($operation),
					"type" => $operation->class_title(),
					"comment" => $operation->comment(),
					"created" => $operation->created(),
					"modified" => $operation->modified(),
				));
			}
			while ($operation = $operations->next());
		}

		return PROP_OK;
	}

	protected function define_operations_overview_table_header($t)
	{
		$t->set_sortable(true);

		$t->set_default("sortable", true);
		$t->add_fields(array(
			"type" => t("T&uuml;&uuml;p"),
			"name" => t("Nimi"),
			"comment" => t("Kommentaar"),
		));

		$t->set_default("width", 120);
		$t->set_default("align", "center");
		$t->set_default("type", "time");
		$t->set_default("format", "d.m.Y H:i");
		$t->set_default("smart", true);
		$t->add_fields(array(
			"created" => t("Loodud"),
			"modified" => t("Viimati muudetud"),
		));

		//	operationTypeId is of the form "or_{CLID}"
		switch(substr(automatweb::$request->arg("operationTypeId"), 3))
		{
			case crm_offer_sent_obj::CLID:
				$caption = t("Pakkumusega seotud e-kirjad");
				break;

			case crm_call_obj::CLID:
				$caption = t("Pakkumusega seotud k&otilde;ned");
				break;

			case crm_presentation_obj::CLID:
				$caption = t("Pakkumusega seotud esitlused");
				break;

			default:
				$caption = t("K&otilde;ik pakkumusega seotud tegevused");
				break;
		}

		$t->set_caption($caption);

		$t->set_default_sortby("created");
		$t->set_default_sorder("desc");
	}

	public function _get_send_toolbar(&$arr)
	{
		$r = PROP_OK;
		$t = $arr["prop"]["vcl_inst"];
		$t->add_button(array(
			"name" => "send",
			"img" => "mail_send.gif",
			"tooltip" => t("Saada pakkumus"),
			"confirm" => t("Oled kindel et soovid pakkumuse saata?"),
			"action" => "send"
		));

		$t->add_button(array(
			"name" => "save",
			"img" => "save.gif",
			"tooltip" => t("Salvesta muudatused ajutiselt"),
			"action" => "submit"
		));

		return $r;
	}

	public function get_property(&$arr)
	{
		$r = PROP_OK;
		$prop = &$arr["prop"];

		if (in_array($prop["name"], array("mail_from", "mail_from_name", "mail_subject", "mail_content", "mail_reply_time")))
		{
			$prop["value"] = $arr["obj_inst"]->get_mail_prop($prop["name"]);
		}
		if (in_array($prop["name"], array("mail_subject", "mail_content")))
		{
			$prop["onblur"] = "crm_offer_refresh_mail_text();";
		}

		return $r;
	}

	public function set_property(&$arr)
	{
		$r = PROP_OK;
		$prop = &$arr["prop"];

		if (in_array($prop["name"], array("mail_from", "mail_from_name", "mail_subject", "mail_content", "mail_reply_method")))
		{
			$arr["obj_inst"]->set_mail_prop($prop["name"], $prop["value"]);
		}
		elseif ("mail_reply_time" === $prop["name"])
		{
			$arr["obj_inst"]->set_mail_prop($prop["name"], datepicker::get_timestamp(automatweb::$request->arg("mail_reply_time")));
		}

		return $r;
	}

	public function _get_mail_attachments($arr)
	{
		$pdf = $arr["obj_inst"]->make_pdf();
		if ($pdf)
		{
			$file_data = $pdf->get_file();
			$pdf_link = " " . html::href(array(
				"caption" => html::img(array(
					"url" => aw_ini_get("baseurl")."/automatweb/images/icons/pdf_upload.gif",
					"border" => 0
				)) . $pdf->name() . " (". filesize($file_data["properties"]["file"])." B)",
				"url" => $pdf->get_url(),
			));
			$value["p"] = "p";
		}

		$arr["prop"]["options"] = array(
			"offer" => t("Pakkumuse PDF") . $pdf_link,
		);
		$arr["prop"]["value"] = "offer";

		return PROP_OK;
	}

	public function _get_mail_recipients(&$arr)
	{
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

		$recipient_types = array(
			"customer_director" => t("Kliendi juhataja aadress"),
			"customer_general" => t("Kliendi &uuml;ldaadressid"),
			"customer_bill" => t("Kliendi arveaadressid"),
			"salesman" => t("M&uuml;&uuml;giesindaja"),
			"custom" => t("Lisaaadressid")
		);

		foreach($recipient_types as $recipient_type => $recipient_caption)
		{
			$recipients = $arr["obj_inst"]->get_mail_recipients(array($recipient_type));
			if (count($recipients))
			{
				foreach ($recipients as $email_address => $data)
				{
					$data[2] = "customer";
					$prop_name = $this->add_recipient_propdefn($t, $email_address, $data, $arr["obj_inst"], $recipient_caption);
				}
			}
		}

		return PROP_OK;
	}

	protected function add_recipient_propdefn(vcl_table $t, $email_address, $recipient_data, $offer, $title, $disabled = false)
	{
		static $i;
		++$i;
		$recipient_oid = $recipient_data[0];
		$name = $recipient_data[1];
		$phones = $organization = $profession = $chooser = "";

		if ($recipient_oid)
		{
			$recipient = new object($recipient_oid);

			if ($recipient->is_a(crm_person_obj::CLID))
			{
				if ("customer" === $recipient_data[2])
				{
					$organization_o = new object($offer->prop("customer"));
				}
				else
				{
					$organization_o = new object($recipient->company_id());
				}

				$organization = html::obj_change_url($organization_o->id(), $organization_o->name());
				$profession = implode(", " , $recipient->get_profession_names($organization_o));
				$name = html::obj_change_url($recipient->id(), $recipient->name());
			}
			elseif ($recipient->is_a(crm_company_obj::CLID))
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
		$checked = array("to" => 0, "cc" => 0, "bcc" => 0);
		if (!$disabled)
		{
			$recipients_tmp = $offer->get_mail_prop("recipients");
			$checked = array(
				"to" => !empty($recipients_tmp["{$email_address}-to"]),
				"cc" => !empty($recipients_tmp["{$email_address}-cc"]),
				"bcc" => !empty($recipients_tmp["{$email_address}-bcc"]),
			);
		}

		$prop_name = "recipient[{$i}]";
		$chooser = "";
		$options = array("to" => t("to"), "cc" => t("cc"), "bcc" => t("bcc"));
		foreach($options as $option => $caption)
		{
			$chooser .= " ";
			$chooser .= html::radiobutton(array(
				"caption" => $caption,
				"name" => $prop_name,
				"checked" => $checked[$option],
				"value" => "{$email_address}-{$option}",
				"disabled" => $disabled
			));
		}
		$chooser = html::span(array("content" => $chooser, "nowrap" => 1));

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

	function _set_mail_recipients($arr)
	{
		if (automatweb::$request->arg_isset("recipient"))
		{
			$arr["obj_inst"]->set_mail_prop("recipients", array_flip(automatweb::$request->arg("recipient")));
		}
		return PROP_IGNORE;
	}

	function _get_recipient_name(&$arr)
	{
		$arr["prop"]["value"] = "";

		$save_btn = " " . html::href(array(
			"url" => "javascript:submit_changeform('submit')",
			"title" => t("Lisa sisestatud e-posti aadress"),
			"caption" => html::img(array("url" => icons::get_std_icon_url("save")))
		));

		$arr["prop"]["post_append_text"] = $save_btn;

		return PROP_OK;
	}

	function _set_recipient_name(&$arr)
	{
		$r = PROP_IGNORE;
		if (!empty($arr["prop"]["value"]))
		{
			if(is_email($arr["prop"]["value"]))
			{
				$recipients = $arr["obj_inst"]->get_mail_prop("custom_recipients");
				$recipients[$arr["prop"]["value"]] = null;
				$arr["obj_inst"]->set_mail_prop("custom_recipients", $recipients);
			}
			else
			{
				$r = PROP_ERROR;
				$arr["prop"]["error"] = t("Vigane e-posti aadress");
			}
		}
		return $r;
	}

	public function _get_mail_legend(&$arr)
	{
		$arr["prop"]["value"] = nl2br('#offer_no# => '.t("Pakkumuse number").'
#customer.name# => '.t("Kliendi nimi").'
#customer.director# => '.t("Kliendi juhatuse esimees").'
#signature# => '.t("Saatja allkiri").'
');

		return PROP_OK;
	}

	public function _get_mail_subject_view(&$arr)
	{
		$arr["prop"]["value"] = html::span(array(
			"content" => $arr["obj_inst"]->parse_mail_text($arr["obj_inst"]->get_mail_prop("mail_subject")),
			"id" => "mail_subject_text_element"
		)) . html::linebreak(2);
		return PROP_OK;
	}

	public function _get_mail_content_view(&$arr)
	{
		$arr["prop"]["value"] = html::span(array(
			"content" => nl2br($arr["obj_inst"]->parse_mail_text($arr["obj_inst"]->get_mail_prop("mail_content"))),
			"id" => "mail_content_text_element"
		));
		return PROP_OK;
	}

	protected function define_confirmations_table_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Nimi"),
		));
		$t->define_field(array(
			"name" => "organisation",
			"caption" => t("Organisatsioon"),
		));
		$t->define_field(array(
			"name" => "profession",
			"caption" => t("Amet"),
		));
		$t->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon"),
		));
		$t->define_field(array(
			"name" => "email",
			"caption" => t("E-post"),
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Kinnitamise aeg"),
		));
	}

	public function _get_confirmations_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$offer = $arr["obj_inst"];

		$this->define_confirmations_table_header($arr);

		$confirmations = $offer->confirmed_by();
		foreach($confirmations as $confirmation)
		{
			$row = $confirmation;
			$row["name"] = sprintf("%s %s", $row["firstname"], $row["lastname"]);
			$row["time"] = aw_locale::get_lc_date($row["time"], aw_locale::DATETIME_SHORT_FULLYEAR);
			$t->define_data($row);
		}
	}

	protected function define_sent_table_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "from",
			"caption" => t("Saatja"),
		));
		$t->define_field(array(
			"name" => "time",
			"caption" => t("Aeg"),
			"align" => "center",
			"type" => "time",
			"numeric" => true,
			"format" => "d.m.Y H:i",
		));
		$t->define_field(array(
			"name" => "to",
			"caption" => t("Saaja"),
		));
		$t->define_field(array(
			"name" => "content",
			"caption" => t("Sisu"),
		));
		/*
		$t->define_field(array(
			"name" => "version",
			"caption" => t("Saadetud versioon"),
		));
		*/
	}

	public function _get_sent_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$offer = $arr["obj_inst"];

		$this->define_sent_table_header($arr);

		$sents = $offer->sent();
		foreach($sents->arr() as $sent)
		{
			$t->define_data(array(
				"from" => htmlspecialchars(sprintf("%s <%s>", $sent->send_from_name, $sent->send_from_adr)),
				"time" => $sent->sent_when,
				"to" => htmlspecialchars(sprintf("%s <%s>", $sent->send_to_name, $sent->send_to_mail)),
				"content" => nl2br(strlen($sent->sent_content) !== 0 ? $sent->sent_content : $sent->instance()->_format_content($sent)),
				"version" => ""
			));
		}
	}

	public function _get_contracts(&$arr)
	{
		$application = automatweb::$request->get_application();
		$arr["prop"]["options"] = $application->is_a(crm_sales_obj::CLID) ? $application->get_contract_list()->names() : array();

		//	For some reason chooser requires the key and value to be equal.
		$contracts = array();
		foreach($arr["obj_inst"]->prop("contracts")->ids() as $contract)
		{
			$contracts[$contract] = $contract;
		}
		$arr["prop"]["value"] = $contracts;

		return PROP_OK;
	}

	public function _set_contracts(&$arr)
	{
		$arr["obj_inst"]->set_prop("contracts", $arr["prop"]["value"]);

		return PROP_OK;
	}

	public function _get_save_as_template(&$arr)
	{
		$arr["prop"]["class"] = "sbtbutton";
		$arr["prop"]["onclick"] = "$.prompt(offer_template_name_html, {
			callback: function(v,m){
				if(v == true){
					$('input[type=hidden][name=template_name]').val(m.children('#offer_template_name').val());
					submit_changeform('create_template');
				}
			},
			buttons: { 'Salvesta': true, 'Katkesta': false }
		});";
	}

	public function _get_send($arr)
	{
		if (!is_oid($arr["obj_inst"]->customer))
		{
			return PROP_IGNORE;
		}

		$arr["prop"]["class"] = "sbtbutton";
		$arr["prop"]["onclick"] = sprintf("document.location = '%s'", $this->mk_my_orb("new", array("return_url" => get_ru(), "offer" => $arr["obj_inst"]->id(), "parent" => $arr["obj_inst"]->id()), crm_offer_sent_obj::CLID));
	}

	public function _get_state($arr)
	{
		$arr["prop"]["options"] = crm_offer_obj::state_names();

		return PROP_OK;
	}

	public function _get_result($arr)
	{
		if ((int)$arr["obj_inst"]->prop("state") === crm_offer_obj::STATE_NEW)
		{
			return PROP_IGNORE;
		}

		$arr["prop"]["options"] = array("" => "") + crm_offer_obj::result_names();

		return PROP_OK;
	}

	public function _get_result_object($arr)
	{
		$result = (int)$arr["obj_inst"]->prop("result");
		if (crm_offer_obj::RESULT_CALL === $result or crm_offer_obj::RESULT_PRESENTATION === $result or crm_offer_obj::RESULT_NEW_OFFER === $result)
		{
			try
			{
				$result_object = $arr["obj_inst"]->get_result_object();
				$arr["prop"]["value"] = html::obj_change_url($result_object);
				return PROP_OK;
			}
			catch (Exception $e)
			{
				return PROP_IGNORE;
			}
		}
		else
		{
			return PROP_IGNORE;
		}
	}

	public function _get_content_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->add_menu_button(array(
			"name" => "content_search",
			"img" => "search.gif",
			"tooltip" => t("Lisa pakkumusse artikleid"),
		));

		$clids = crm_offer_row_obj::get_applicable_clids();
		$url = new aw_uri($this->mk_my_orb("do_search", array("pn" => "content_add"), "popup_search"));
		foreach($clids as $clid)
		{
			$url->set_arg("clid", $clid);
			$caption = object::class_title_by_clid($clid);
			$t->add_menu_item(array(
				"parent" => "content_search",
				"text" => $caption,
				"link" => "javascript:aw_popup_scroll('{$url}','{$caption}',".popup_search::PS_WIDTH.",".popup_search::PS_HEIGHT.")",
			));
		}
		$url->set_arg("clid", $clids);
		$caption = t("K&otilde;ik v&otilde;imalikud objektid");
		$t->add_menu_item(array(
			"parent" => "content_search",
			"text" => $caption,
			"link" => "javascript:aw_popup_scroll('{$url}','{$caption}',".popup_search::PS_WIDTH.",".popup_search::PS_HEIGHT.")",
		));

		$t->add_delete_button();
		$t->add_save_button();

		return PROP_OK;
	}

	protected function define_content_table_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_chooser();

		$t->define_field(array(
			"name" => "object",
			"caption" => t("Artikkel"),
		));
			$t->define_field(array(
				"name" => "row_name_and_comment",
				"caption" => t("Pealkiri ja kommentaar"),
				"callback" => array($this, "callback_content_table_row_name_and_comment"),
				"callb_pass_row" => true,
				"parent" => "object",
			));
			$t->define_field(array(
				"name" => "amount",
				"caption" => t("Kogus"),
				"callback" => array($this, "callback_content_table_amount"),
				"callb_pass_row" => true,
				"parent" => "object",
			));
			$t->define_field(array(
				"name" => "unit",
				"caption" => t("&Uuml;hik"),
				"callback" => array($this, "callback_content_table_unit"),
				"callb_pass_row" => true,
				"parent" => "object",
			));
		$t->define_field(array(
			"name" => "price_component",
			"caption" => t("Hinnakomponent"),
		));
			$t->define_field(array(
				"name" => "price_component_name",
				"caption" => t("Nimi"),
				"callback" => array($this, "callback_content_table_price_component_name"),
				"callb_pass_row" => true,
				"parent" => "price_component",
			));
			$t->define_field(array(
				"name" => "price_component_value",
				"caption" => t("Summa v&otilde;i protsent"),
				"callback" => array($this, "callback_content_table_price_component_value"),
				"callb_pass_row" => true,
				"parent" => "price_component",
			));
			$t->define_field(array(
				"name" => "price_component_price_change",
				"caption" => t("Hinnamuutus"),
				"callback" => array($this, "callback_content_table_price_component_price_change"),
				"callb_pass_row" => true,
				"parent" => "price_component",
			));
		$t->define_field(array(
			"name" => "price",
			"caption" => t("Hind"),
			"callback" => array($this, "callback_content_table_price"),
			"callb_pass_row" => true,
		));
	}

	public function callback_content_table_row_name_and_comment($row)
	{
		return html::textbox(array(
			"name" => "content_table[{$row["row"]->id()}][name]",
			"value" => $row["row"]->prop("name"),
			"size" => 65,
		)).html::linebreak().html::textarea(array(
			"name" => "content_table[{$row["row"]->id()}][comment]",
			"value" => $row["row"]->prop("comment"),
			"rows" => 3,
			"cols" => 50,
		));
	}

	public function callback_content_table_price_component_name($row)
	{
		$compulsory = $this->offer->price_component_is_compulsory($row["price_component"]);
		if($compulsory)
		{
			return html::checkbox(array(
				"name" => "content_table[{$row["row"]->id()}][price_component][{$row["price_component"]->id()}][apply_dummy]",
				"checked" => true,
				"disabled" => true,
			))
			."&nbsp;".$row["price_component"]->name()
			.html::hidden(array(
				"name" => "content_table[{$row["row"]->id()}][price_component][{$row["price_component"]->id()}][apply]",
				"value" => 1,
			));
		}
		else
		{
			return html::checkbox(array(
				"name" => "content_table[{$row["row"]->id()}][price_component][{$row["price_component"]->id()}][apply]",
				"checked" => $row["row"]->price_component_is_applied($row["price_component"]->id()),
				"disabled" => false,
			))
			."&nbsp;".$row["price_component"]->name();
		}
	}

	public function callback_content_table_price_component_value($row)
	{
		$value = $row["price_component_value"];
		if($row["row"]->price_component_is_applied($row["price_component"]->id()))
		{
			$value = $row["row"]->get_value_for_price_component($row["price_component"]->id());
		}
		list($min, $max) = $this->offer->get_tolerance_for_price_component($row["price_component"]);

		$this->zend_view->dojo()->requireModule('dijit.form.NumberSpinner');

		return $this->zend_view->numberSpinner(
			"content_table[{$row["row"]->id()}][price_component][{$row["price_component"]->id()}][value]",
			$value,
			array(
				"min" => $min,
				"max" => $max,
				"places" => 0,
				"intermediateChanges" => true,
				"onChange" => "awCrmOffer.calculateRow({$row["row"]->id()}); awCrmOffer.calculateRow('total'); awCrmOffer.calculateTotalPrice();"
			),
			array(
				"id" => "content_table_{$row["row"]->id()}_price_component_{$row["price_component"]->id()}_value",
			)
		).($row["price_component"]->prop("is_ratio") ? t("%") : "");
	}

	public function callback_content_table_amount($row)
	{
		return html::textbox(array(
			"name" => "content_table[{$row["row"]->id()}][amount]",
			"value" => $row["amount"],
			"size" => 7,
		));
	}

	public function callback_content_table_unit($row)
	{
		return html::select(array(
			"name" => "content_table[{$row["row"]->id()}][unit]",
			"value" => $row["unit"],
			"options" => obj($row["object"])->get_units()->names(),
		));
	}

	public function callback_content_table_price_component_price_change($row)
	{
		return html::span(array(
			"id" => "content_table_{$row["row"]->id()}_price_component_{$row["price_component"]->id()}_price_change",
		)).html::hidden(array(
			"name" => "content_table[{$row["row"]->id()}][price_component][{$row["price_component"]->id()}][price_change]",
		));
	}

	public function callback_content_table_price($row)
	{
		return html::span(array(
			"id" => "content_table_{$row["row"]->id()}_price",
		)).html::hidden(array(
			"name" => "content_table[{$row["row"]->id()}][price]",
		));
	}

	public function _get_content_table($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$offer = $arr["obj_inst"];

		$this->define_content_table_header($arr);

		$rows = $offer->get_rows();

		foreach($rows as $row)
		{
			$this->rows[$row->id()]["price_components"] = $price_components = $offer->get_price_components_for_row($row);
			foreach($price_components->arr() as $price_component)
			{
				$t->define_data(array(
					"oid" => $row->id(),
					"row" => $row,
					"price_component" => $price_component,
					"price_component_name" => $price_component->name(),
					"price_component_value" => $price_component->prop("value"),
					"object" => $row->prop("object"),
					"amount" => $row->prop("amount"),
					"unit" => $row->prop("unit"),
				));
			}
		}

		$t->set_vgroupby(array(
			"row_name_and_comment" => "object",
			"amount" => "object",
			"unit" => "object",
			"oid" => "object",
			"price" => "object",
		));

		$t->set_caption("Pakkumuse sisu ja komponentide hinnakujundus");
	}

	public function _set_content_table($arr)
	{
		$data = $arr["prop"]["value"];
		if(isset($data) && is_array($data))
		{
			foreach($data as $row_id => $row_data)
			{
				if (is_oid($row_id))
				{
					$row = obj($row_id);
					$row->set_prop("name", isset($row_data["name"]) ? $row_data["name"] : "");
					$row->set_prop("comment", isset($row_data["comment"]) ? $row_data["comment"] : "");
					$row->set_prop("unit", isset($row_data["unit"]) ? $row_data["unit"] : null);
					$row->set_prop("amount", isset($row_data["amount"]) ? $row_data["amount"] : 0);

					foreach($row_data["price_component"] as $price_component_id => $price_component_data)
					{
						$apply = !empty($price_component_data["apply"]);
						if ($apply)
						{
							$row->apply_price_component($price_component_id, $price_component_data["value"], $price_component_data["price_change"]);
						}
						elseif ($row->price_component_is_applied($price_component_id))
						{
							$row->remove_price_component($price_component_id);
						}
					}

					$row->save();
				}
				elseif ("total" == $row_id){

					foreach($row_data["price_component"] as $price_component_id => $price_component_data)
					{
						$offer = $arr["obj_inst"];

						$apply = !empty($price_component_data["apply"]);
						if ($apply)
						{
							$offer->apply_price_component($price_component_id, $price_component_data["value"], $price_component_data["price_change"]);
						}
						elseif ($offer->price_component_is_applied($price_component_id))
						{
							$offer->remove_price_component($price_component_id);
						}
					}
				}
			}
		}
	}

	public function define_content_total_price_components_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_field(array(
			"name" => "name",
			"caption" => t("Hinnakomponent"),
			"callback" => array($this, "callback_content_total_price_components_name"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "value",
			"caption" => t("Summa v&otilde;i protsent"),
			"callback" => array($this, "callback_content_total_price_components_value"),
			"callb_pass_row" => true,
		));
		$t->define_field(array(
			"name" => "price_change",
			"caption" => t("Hinnamuutus"),
			"callback" => array($this, "callback_content_total_price_components_price_change"),
			"callb_pass_row" => true,
		));
	}

	public function _get_content_total_price_components($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$offer = $arr["obj_inst"];

		$this->define_content_total_price_components_header($arr);

		$price_components = $offer->get_price_components_for_total();
		foreach($price_components->arr() as $price_component)
		{
			$t->define_data(array(
				"price_component" => $price_component,
				"name" => $price_component->name(),
				"value" => $price_component->prop("value"),
			));
		}
		$t->define_data(array(
			"name" => html::bold(t("KOGUHIND")),
		));
	}

	public function callback_content_total_price_components_price_change($row)
	{
		if(!isset($row["price_component"]) || !is_object($row["price_component"]))
		{
			return html::span(array(
				"id" => "content_total_price_components_total_price",
			)).html::hidden(array(
				"name" => "content_total_price_components[total_price]",
			));
		}

		return html::span(array(
			"id" => "content_table_total_price_component_{$row["price_component"]->id()}_price_change",
		)).html::hidden(array(
			"name" => "content_table[total][price_component][{$row["price_component"]->id()}][price_change]",
		));
	}

	public function callback_content_total_price_components_value($row)
	{
		if(!isset($row["price_component"]) || !is_object($row["price_component"]))
		{
			return "";
		}

		$value = $row["value"];
		if($this->offer->price_component_is_applied($row["price_component"]->id()))
		{
			$value = $this->offer->get_value_for_price_component($row["price_component"]->id());
		}
		list($min, $max) = $this->offer->get_tolerance_for_price_component($row["price_component"]);

		$this->zend_view->dojo()->requireModule('dijit.form.NumberSpinner');

		return $this->zend_view->numberSpinner(
			"content_table[total][price_component][{$row["price_component"]->id()}][value]",
			$value,
			array(
				"min" => $min,
				"max" => $max,
				"places" => 0,
				"intermediateChanges" => true,
				"onChange" => "awCrmOffer.calculateRow('total'); awCrmOffer.calculateTotalPrice();"
			),
			array(
				"id" => "content_total_price_components_{$row["price_component"]->id()}_value",
			)
		).($row["price_component"]->prop("is_ratio") ? t("%") : "");
	}

	public function callback_content_total_price_components_name($row)
	{
		if(!isset($row["price_component"]) || !is_object($row["price_component"]))
		{
			return $row["name"];
		}

		$compulsory = $this->offer->price_component_is_compulsory($row["price_component"]);
		if($compulsory)
		{
			return html::checkbox(array(
				"name" => "content_table[total][price_component][{$row["price_component"]->id()}][apply_dummy]",
				"checked" => true,
				"disabled" => true,
			))
			."&nbsp;".$row["price_component"]->name()
			.html::hidden(array(
				"name" => "content_table[total][price_component][{$row["price_component"]->id()}][apply]",
				"value" => 1,
			));
		}
		else
		{
			return html::checkbox(array(
				"name" => "content_table[total][price_component][{$row["price_component"]->id()}][apply]",
				"checked" => $this->offer->price_component_is_applied($row["price_component"]->id()),
				"disabled" => false,
			))
			."&nbsp;".$row["price_component"]->name();
		}
	}

	public function _set_salesman($arr)
	{
		if(!is_oid($arr["prop"]["value"]))
		{
			$arr["prop"]["error"] = t("Palun sisestage olemasolev m&uuml;&uuml;giesindaja!");
			return PROP_FATAL_ERROR;
		}

		return PROP_OK;
	}

	public function _set_content_add($arr)
	{
		$o = $arr["obj_inst"];
		$object_ids = explode(",", $arr["prop"]["value"]);
		foreach($object_ids as $object_id)
		{
			if(is_oid($object_id))
			{
				$object = obj($object_id);
				if(!$o->contains_object($object))
				{
					$o->add_object($object);
				}
			}
		}
	}

	public function _get_sum($arr)
	{
		return PROP_IGNORE;
	}

	public function _set_sum($arr)
	{
		return PROP_IGNORE;
	}

	public function _get_preview($arr)
	{
		automatweb::$result->set_data($this->show(array(
			"id" => $arr["obj_inst"]->id(),
		)));
		automatweb::$result->send();

		die;
	}

	/**	Returns parsed HTML of the crm_offer template.
		@attrib api=1
		@param id required type=int
			The OID of the crm_offer object to be shown.
		@param show_confirmation optional type=boolean default=false
			The OID of the crm_offer object to be shown.
		@param pdf optional type=boolean default=false
	**/
	public function show($arr)
	{
		$this->read_template(empty($arr["pdf"]) ? "show.tpl" : "show_pdf.tpl");

		$o = new object($arr["id"]);

		// General data, such as id, date, currency
		$this->vars($o->get_offer_data());

		$this->vars($o->get_salesman_data());

		// Parse sales organization's (owner of sales) data
		$salesorg_data = $o->get_salesorg_data();
		$this->vars($salesorg_data);
		$BANK_ACCOUNT = "";
		foreach($salesorg_data["salesorg.bank_accounts"] as $salesorg_bank_account_data)
		{
			$this->vars($salesorg_bank_account_data);
			$BANK_ACCOUNT .= $this->parse("BANK_ACCOUNT");
		}
		$this->vars(array(
			"BANK_ACCOUNT" => $BANK_ACCOUNT
		));
		
		try
		{
			$this->vars($o->get_customer_data());
		}
		catch (awex_crm_offer_customer $e)
		{
			//	No customer set for the offer.
		}

		$ROW = "";
		foreach($o->get_rows() as $row)
		{
			$this->vars(array(
				"name" => $row->prop("name"),
				"comment" => $row->prop("comment"),
				"object" => obj($row->prop("object"))->name(),	//$row->prop("object.name"),	// prop.name NOT WORKING IF NOT LOGGED IN!
				"unit" => obj($row->prop("unit"))->name(),	//$row->prop("unit.name"),	// prop.name NOT WORKING IF NOT LOGGED IN!
				"amount" => $row->prop("amount"),
				"price" => $row->prop("amount") != 0 ? number_format($row->get_price($row) / $row->prop("amount"), 2) : $row->get_price($row),	// number_format() SHOULD BE DONE ON TPL LEVEL!
				"sum" => number_format($row->get_price($row), 2),	// number_format() SHOULD BE DONE ON TPL LEVEL!
				"ROW_COMMENT" => "",
			));
			if(strlen($row->prop("comment")) > 0)
			{
				$this->vars(array(
					"ROW_COMMENT" => $this->parse("ROW_COMMENT"),
				));
			}
			$ROW .= $this->parse("ROW");
		}

		$CONTRACT = "";

		foreach($o->prop("contracts")->arr() as $contract)
		{
			$this->vars(array(
				"contract.id" => $contract->id(),
				"contract.link" => doc_display::get_doc_link($contract->document()),
			));
			$CONTRACT .= $this->parse("CONTRACT");
		}
		$this->vars(array(
			"CONTRACT" => $CONTRACT,
		));

		if($o->state != crm_offer_obj::STATE_CONFIRMED && !empty($arr["show_confirmation"]))
		{
			$this->vars(array(
				"do_confirmation_url" => aw_url_change_var("do_confirm", 1),
			));

			$this->vars(array(
				"CONFIRMATION" => $this->parse("CONFIRMATION"),
			));
		}

		$this->vars(array(
			"total" => number_format($o->prop("sum"), 2),	// number_format() SHOULD BE DONE ON TPL LEVEL!
			"total_text" => aw_locale::get_lc_money_text($o->prop("sum"), $o->currency()),
			"ROW" => $ROW
		));

		if(!empty($arr["pdf"]))
		{
			$conv = new html2pdf();
			if($conv->can_convert())
			{
				$res = $conv->convert(array(
					"source" => $this->parse(),
					"filename" => $o->id().".pdf",
				));
				return $res;
			}
		}

		return $this->parse();
	}

	private function parse_sales_org_data($imp)
	{
		$vars = array();
		if ($this->can("view", $imp))
		{
			$impl = obj($imp);
			$vars["impl_name"] = $impl->name();
			$vars["impl_reg_nr"] = $impl->prop("reg_nr");
			$vars["impl_kmk_nr"] = $impl->prop("tax_nr");
			$vars["impl_fax"] = $impl->prop_str("telefax_id", true);//TODO: use get_phone(), get_telefax(),... type methods instead -- implementor could be a person. todo: create these methods in crmco crmperson and add them to customerinterface
			$vars["impl_url"] = $impl->prop_str("url_id", true);
			$vars["impl_phone"] = $impl->prop_str("phone_id", true);
			$vars["imp_penalty"] = $impl->prop("bill_penalty_pct");//TODO: belongs to customer relation not crmco
			$vars["impl_ou"] = $impl->prop("ettevotlusvorm.shortname");

			$impl_logo = $impl->get_first_obj_by_reltype("RELTYPE_ORGANISATION_LOGO");
			if ($impl_logo)
			{
				$logo_url = $impl_logo->instance()->get_url_by_id($impl_logo->id());
				$this->vars["impl_logo_url"] = $logo_url;
				if ($logo_url)
				{
					$this->vars(array(
						"HAS_IMPL_LOGO" => $this->parse("HAS_IMPL_LOGO")
					));
				}
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
					//TODO: make sense and order in phone number handling before adding areacode automatically.
					// $vars["impl_phone"] = $riik->prop("area_code")." ".$impl->prop_str("phone_id");
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

	/**
		@attrib name=new_from_template
		@param tpl required type=int
		@param parent required type=int
		@param return_url optional type=string
	**/
	public function new_from_template($arr)
	{
		$template = obj($arr["tpl"]);
		$new_offer = $template->create_offer_from_template($arr["parent"]);

		return html::get_change_url($new_offer->id(), array("return_url" => $arr["return_url"]));
	}

	/**
		@attrib name=create_template
	**/
	public function create_template($arr)
	{
		if(!empty($arr["template_name"]))
		{
			$o = obj($arr["id"]);
			$o->create_template($arr["template_name"]);
		}

		return $arr["post_ru"];
	}

	/**
		@attrib name=confirm params=name nologin=1
		@param id required type=int
		@param do_confirm optional type=boolean default=false
		@param firstname optional type=string
		@param lastname optional type=string
		@param organisation optional type=string
		@param profession optional type=string
		@param phone optional type=string
		@param email optional type=string
	**/
	public function confirm($arr)
	{
		if(!empty($arr["do_confirm"]))
		{
			$o = obj($arr["id"]);
			$o->confirm($arr);
		}

		die($this->show(array(
			"id" => $arr["id"],
			"show_confirmation" => true,
		)));
	}

	/**
	@attrib name=send all_args=1
	@param id required type=int
		offer id
	@param post_ru required type=string
	@returns string
	**/
	function send($arr)
	{
		$r = $arr["post_ru"];
		try
		{
			$this_o = obj($arr["id"], array(), crm_offer_obj::CLID);
		}
		catch (awex_obj $e)
		{
			$this->show_error_text(t("Invalid offer id!"));
			return $r;
		}

		if (empty($arr["sendmail_attachments"]) or !is_array($arr["sendmail_attachments"]))
		{
//			$this->show_error_text(t("Arvet ei saa saata saadetavat dokumenti valimata."));
//			return $r;
		}

		if (empty($arr["recipient"]) or !is_array($arr["recipient"]))
		{
			$this->show_error_text(t("No recipients selected!"));
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

		$subject = $this_o->parse_mail_text($arr["mail_subject"]);
		$body = nl2br($this_o->parse_mail_text($arr["mail_content"]));
//		$reminder = isset($arr["sendmail_attachments"]["r"]);
//		$appendix = isset($arr["sendmail_attachments"]["a"]);
		$from = $arr["mail_from"];
		$from_name = $arr["mail_from_name"];

		try
		{
			$this_o->send($to, $subject, $body, $cc, $bcc, $from, $from_name);
			//	It will only execute set_reply() on successful execution of send(), so all is well. :)
			$this_o->set_reply($arr["mail_reply_method"], datepicker::get_timestamp(automatweb::$request->arg("mail_reply_time")));

			// remove temporary changes
			$this_o->clear_mail_data();
			$this->show_completed_text(t("Pakkumus edukalt saadetud!"));
		}
		catch (awex_crm_offer_email $e)
		{
			if ($e->email)
			{
				$this->show_error_text(sprintf(t("Pakkumust ei saadetud. Vigane aadress: '%s'"), $e->email));
			}
			else
			{
				$this->show_error_text(t("Pakkumust ei saa saata saajaid m&auml;&auml;ramata"));
			}
		}
		catch (awex_crm_offer_file $e)
		{
			$this->show_error_text(t("Pakkumust ei saadetud. Dokumendi lisamine eba&otilde;nnestus"));
		}
		catch (awex_crm_offer_send $e)
		{
			$this->show_error_text(t("Pakkumust ei saadetud. Viga t&otilde;en&auml;oliselt serveri meiliseadetes."));
		}
		catch (Exception $e)
		{
			trigger_error("Caught exception " . get_class($e) . " while sending offer. Thrown in '" . $e->getFile() . "' on line " . $e->getLine() . ": '" . $e->getMessage() . "' <br /> Backtrace:<br />" . dbg::process_backtrace($e->getTrace(), -1, true), E_USER_WARNING);
			$this->show_error_text(t("Esines vigu. Pakkumust ei saadetud."));
		}

		return $r;
	}

	/**
		@attrib name=parse_mail_text
		@param id required type=int
		@param text required type=string
	**/
	function parse_mail_text($arr)
	{
		try
		{
			$this_o = obj($arr["id"], array(), crm_offer_obj::CLID);
			echo nl2br($this_o->parse_mail_text($arr["text"]));
		}
		catch (Exception $e)
		{
		}
		exit;
	}

	public function callback_post_save($arr)
	{
		if(isset($arr["request"]["content_total_price_components"]["total_price"]))
		{
			$arr["obj_inst"]->set_prop("sum", aw_math_calc::string2float($arr["request"]["content_total_price_components"]["total_price"]));
			$arr["obj_inst"]->save();
		}
	}

	public function callback_generate_scripts($arr)
	{
		$js = "";

		if ("general" === $this->use_group)
		{
			$js .= 'var offer_template_name_html = "'.t("Palun sisesta &scaron;ablooni nimi:<br /><input type='text' id='offer_template_name' name='offer_template_name' size='40' />\";");
		}

		if("content" === $this->use_group)
		{
			//	Offer Content Calculation Data
			$aw_crm_offer_rows = array();
			$aw_crm_offer_price_components = array();
			foreach($this->rows as $row_id => $row_data)
			{
				$row_price_components = array();
				foreach($row_data["price_components"]->arr() as $row_price_component)
				{
					$row_price_components[] = $row_price_component->id();
					if(!isset($aw_crm_offer_price_components[$row_price_component->id()]))
					{
						$aw_crm_offer_price_components[$row_price_component->id()] = array(
							"oid" => $row_price_component->id(),
							"type" => $row_price_component->prop("type"),
							"is_ratio" => (boolean) $row_price_component->prop("is_ratio"),
							"prerequisites" => array_values($arr["obj_inst"]->get_all_prerequisites_for_price_component($row_price_component)),
						);
					}
				}
				$aw_crm_offer_rows[$row_id] = array(
					"oid" => $row_id,
					"price_components" => $row_price_components
				);
			}

			$aw_crm_offer_price_components_for_total = array();
			foreach($this->offer->get_price_components_for_total()->arr() as $price_component)
			{
				$aw_crm_offer_price_components_for_total[] = $price_component->id();

				if(!isset($aw_crm_offer_price_components[$price_component->id()]))
				{
					$aw_crm_offer_price_components[$price_component->id()] = array(
						"oid" => $price_component->id(),
						"type" => $price_component->prop("type"),
						"is_ratio" => (boolean) $price_component->prop("is_ratio"),
						"prerequisites" => array_values($price_component->get_all_prerequisites()),
					);
				}
			}

			$aw_crm_offer = array(
				"rows" => $aw_crm_offer_rows,
				"price_components_for_total" => $aw_crm_offer_price_components_for_total,
				"price_components" => $aw_crm_offer_price_components,
			);
			$js = sprintf("
			var awCrmOffer = %s;", json_encode($aw_crm_offer));
			$js .= file_get_contents(AW_DIR . "classes/applications/crm/sales/crm_offer.js");

			load_javascript("jquery/plugins/jquery.calculation.js");
//			load_javascript("jquery/plugins/jquery.numberformatter-1.1.0.js");
		}
		elseif("send" == $this->use_group)
		{
			$js .= <<<ENDSCRIPT
function crm_offer_refresh_mail_text() {
	// subject
	$.get('/automatweb/orb.aw', {class: 'crm_offer', action: 'parse_mail_text', id: '{$arr["obj_inst"]->id()}', text: $('#mail_subject').val()}, function (html) {
		$('#mail_subject_text_element').html(html);
	});

	// body
	$.get('/automatweb/orb.aw', {class: 'crm_offer', action: 'parse_mail_text', id: '{$arr["obj_inst"]->id()}', text: $('#mail_content').val()}, function (html) {
		$('#mail_content_text_element').html(html);
	});
}
ENDSCRIPT;
		}

		if (isset($this->zend_view) && $this->zend_view->dojo()->isEnabled())
		{
			$js .= "</script>";
			$js .= $this->zend_view->dojo();
			$js .= "<script type=\"text/javascript\">";
			$js;
		}
		return $js;
	}

	public function callback_pre_edit($arr)
	{
		$this->offer = $arr["obj_inst"];
	}

	public function callback_on_load($arr)
	{
		if ("content" === $this->use_group)
		{
			//	This will be used to store row data (i.e. price components, etc) and will be used afterwards to generate a JS variable.
			$this->rows = array();

			Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
			$this->zend_view = new Zend_View();
			$this->zend_view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
			$this->zend_view->dojo()->enable()
				->setDjConfigOption('parseOnLoad', true)
				->addStylesheetModule('dijit.themes.tundra');
		}
	}

	public function submit($arr = array())
	{
		$r = parent::submit($arr);
		if ($this->data_processed_successfully())
		{
			$offer = new object($arr["id"]);
			$application = automatweb::$request->get_application();

			if ($application->is_a(crm_sales_obj::CLID))
			{
				$result = (int)$offer->prop("result");
				if (crm_offer_obj::RESULT_CALL === $result or crm_offer_obj::RESULT_PRESENTATION === $result or crm_offer_obj::RESULT_NEW_OFFER === $result)
				{
					try
					{
						$this->show_msg_text(t("Sisestage k&otilde;ne tulemusena loodud objekti andmed"));
						$result_object = $offer->get_result_object();
						$r = html::get_change_url($result_object, array("return_url" => $arr["post_ru"]));
					}
					catch (Exception $e)
					{
						$this->show_error_text(t("K&otilde;ne tulemuseks olev objekt pole avatav"));
					}
				}
			}
		}
		return $r;
	}

	public function do_db_upgrade($t, $f, $query, $error)
	{
		if ("aw_crm_offer" === $t and $f === "")
		{
			$this->db_query("CREATE TABLE aw_crm_offer(aw_oid int primary key)");
			return true;
		}
		elseif("aw_crm_offer_confirmations" === $t and $f === "")
		{
			$this->db_query("CREATE TABLE aw_crm_offer_confirmations (
				aw_offer int,
				aw_firstname varchar (100),
				aw_lastname varchar (100),
				aw_organisation varchar (100),
				aw_profession varchar (100),
				aw_phone varchar (100),
				aw_email varchar (100),
				aw_time int)");
			return true;
		}

		switch($f)
		{
			case "aw_customer_relation":
			case "aw_salesman":
			case "aw_customer":
			case "aw_currency":
			case "aw_date":
			case "aw_template":
			case "aw_result_object":
			case "aw_number":
			case "aw_price_object":

			case "aw_offer":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int(11)"
				));
				return true;

			case "aw_result":
			case "aw_state":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "tinyint(1)"
				));
				return true;

			case "aw_sum":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "decimal(19,4)"
				));
				return true;

			case "aw_firstname":
			case "aw_lastname":
			case "aw_organisation":
			case "aw_profession":
			case "aw_phone":
			case "aw_email":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "varchar(100)"
				));
				return true;

		}
	}
}
