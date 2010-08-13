<?php
/*
@classinfo syslog_type=ST_CRM_OFFER relationmgr=yes no_name=1 no_comment=1 no_status=1 prop_cb=1 maintainer=kaarel
@tableinfo aw_crm_offer master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_offer
@default group=general

	@property customer_relation type=hidden datatype=int field=aw_customer_relation
	@caption Kliendisuhe

	@property customer type=objpicker clid=CL_CRM_COMPANY,CL_CRM_PERSON field=aw_customer
	@caption Kliendi nimi

	@property salesman type=objpicker clid=CL_CRM_PERSON field=aw_salesman
	@caption M&uuml;&uuml;giesindaja nimi

	@property currency type=objpicker clid=CL_CURRENCY field=aw_currency
	@caption Valuuta

	@property state type=select field=aw_state
	@caption Staatus

	@property send type=text store=no editonly=1
	@caption Saada kliendile

	@property save_as_template type=text store=no editonly=1
	@caption Salvesta &scaron;abloonina

	@property template_name type=hidden store=no editonly=1

	@property sum type=hidden field=aw_sum
	@caption Summa

	@property date type=hidden field=aw_date
	@caption Kuup&auml;ev

@groupinfo content caption=Sisu
@default group=content

	@property content_add type=hidden editonly=1 store=no

	@property content_toolbar type=toolbar editonly=1 no_caption=1 store=no

	@property content_table type=table editonly=1 no_caption=1 store=no

	@property content_total_price_components type=table editonly=1 no_caption=1 store=no

@groupinfo preview caption=Eelvaade
@default group=preview

	@property preview type=text store=no no_caption=1 editonly=1

@groupinfo confirmations caption=Kinnitused
@default group=confirmations

	@property confirmations_table type=table store=no no_caption=1 editonly=1

*/

class crm_offer extends class_base
{
	public function crm_offer()
	{
		$this->init(array(
			"tpldir" => "applications/crm/sales/crm_offer",
			"clid" => CL_CRM_OFFER
		));
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

	public function _get_save_as_template($arr)
	{
		$arr["prop"]["value"] = html::href(array(
			"caption" => t("Salvesta &scaron;abloonina"),
			"url" => "javascript:void(0);",
			"onclick" => '$.prompt(offer_template_name_html, {
				callback: function(v,m){
					$("input[type=hidden][name=template_name]").val(m.children("#offer_template_name").val());
					submit_changeform("create_template");
				},
				buttons: { "Salvesta": true, "Katkesta": false }
			});',
		));
	}

	public function _get_send($arr)
	{
		if (!is_oid($arr["obj_inst"]->customer))
		{
			return PROP_IGNORE;
		}

		$arr["prop"]["value"] = html::href(array(
			"caption" => t("Saada kliendile"),
			"url" => $this->mk_my_orb("new", array("return_url" => get_ru(), "offer" => $arr["obj_inst"]->id(), "parent" => $arr["obj_inst"]->id()), CL_CRM_OFFER_SENT),
		));
	}

	public function _get_state($arr)
	{
		$arr["prop"]["options"] = crm_offer_obj::state_names();
	}

	public function _get_content_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->add_menu_button(array(
			"name" => "content_search",
			"img" => "search.gif",
			"tooltip" => t("Lisa pakkumisse artikleid"),
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
	}

	public function define_content_table_header($arr)
	{
		$t = $arr["prop"]["vcl_inst"];

		$t->define_chooser();

		$t->define_field(array(
			"name" => "object",
			"caption" => t("Artikkel"),
		));
			$t->define_field(array(
				"name" => "object_name",
				"caption" => t("Nimi"),
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
					"object_name" => obj($row->prop("object"))->name(),
					"amount" => $row->prop("amount"),
					"unit" => $row->prop("unit"),
				));
			}
		}

		$t->set_vgroupby(array(
			"object_name" => "object",
			"amount" => "object",
			"unit" => "object",
			"oid" => "object",
			"price" => "object",
		));

		$t->set_caption("Pakkumise sisu ja komponentide hinnakujundus");
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
					$row->set_prop("unit", $row_data["unit"]);
					$row->set_prop("amount", $row_data["amount"]);

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
		die($this->show(array(
			"id" => $arr["obj_inst"]->id(),
		)));
	}

	/**	Returns parsed HTML of the crm_offer template.
		@attrib api=1
		@param id required type=int
			The OID of the crm_offer object to be shown.
		@param show_confirmation optional type=boolean default=false
			The OID of the crm_offer object to be shown.
	**/
	public function show($arr)
	{
		$this->read_template("show.tpl");

		$o = new object($arr["id"]);

		$customer = $o->customer();

		$this->vars(array(
			"id" => $o->id(),
			"date" => $o->prop("date"),
			"currency" => obj($o->prop("currency"))->name(), //$o->prop("currency.name"),	// prop.name NOT WORKING IF NOT LOGGED IN!
			"customer" => $customer->name(),
			"customer.mail" => $customer->get_mail(),
//			"customer.phone" => $customer->get_phone(),
		));

		$ROW = "";
		foreach($o->get_rows() as $row)
		{
			$this->vars(array(
				"object" => obj($row->prop("object"))->name(),	//$row->prop("object.name"),	// prop.name NOT WORKING IF NOT LOGGED IN!
				"unit" => obj($row->prop("unit"))->name(),	//$row->prop("unit.name"),	// prop.name NOT WORKING IF NOT LOGGED IN!
				"amount" => $row->prop("amount"),
				"price" => $row->prop("amount") != 0 ? number_format($row->get_price($row) / $row->prop("amount"), 2) : $row->get_price($row),	// number_format() SHOULD BE DONE ON TPL LEVEL!
				"sum" => number_format($row->get_price($row), 2),	// number_format() SHOULD BE DONE ON TPL LEVEL!
			));
			$ROW .= $this->parse("ROW");
		}

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
			"sum" => number_format($o->prop("sum"), 2),	// number_format() SHOULD BE DONE ON TPL LEVEL!
			"sum_text" => aw_locale::get_lc_money_text($o->prop("sum"), $o->currency()),
			"ROW" => $ROW
		));

		return $this->parse();
	}

	/**
		@attrib name=new_from_template
	**/
	public function new_from_template($arr)
	{
		$tpl = obj($arr["tpl"]);
		$old_offer = obj($tpl->offer);
		$new_offer = $old_offer->duplicate();

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

		$js .= 'var offer_template_name_html = "'.t("Palun sisesta &scaron;ablooni nimi:<br /><input type='text' id='offer_template_name' name='offer_template_name' size='40' />\";");

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
							"prerequisites" => array_values($row_price_component->get_all_prerequisites()),
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

	public function do_db_upgrade($t, $f)
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

			case "aw_offer":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int(11)"
				));
				return true;

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

?>
