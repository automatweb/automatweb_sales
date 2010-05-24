<?php
/*
@classinfo syslog_type=ST_CRM_OFFER relationmgr=yes no_name=1 no_comment=1 no_status=1 prop_cb=1 maintainer=SYSTEM
@tableinfo aw_crm_offer master_index=brother_of master_table=objects index=aw_oid

@default table=aw_crm_offer
@default group=general

	@property customer_relation type=hidden datatype=int field=aw_customer_relation
	@caption Kliendisuhe

	@property customer type=objpicker clid=CL_CRM_COMPANY,CL_CRM_PERSON field=aw_customer
	@caption Kliendi nimi

	@property salesman type=objpicker clid=CL_CRM_PERSON field=aw_salesman
	@caption M&uuml;&uuml;giesindaja nimi

@groupinfo content caption=Sisu
@default group=content

	@property content_add type=hidden editonly=1 store=no

	@property content_toolbar type=toolbar editonly=1 no_caption=1 store=no

	@property content_table type=table editonly=1 no_caption=1 store=no

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

	public function _get_content_toolbar($arr)
	{
		$t = $arr["prop"]["vcl_inst"];
		$t->add_search_button(array(
			"name" => "content_search",
			"pn" => "content_add",
			"clid" => crm_offer_row_obj::get_applicable_clids(),
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
			"caption" => t("Sisukomponent"),
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
				"name" => "price_component_apply",
				"caption" => t("Rakendatud"),
				"callback" => array($this, "callback_content_table_price_component_apply"),
				"callb_pass_row" => true,
				"parent" => "price_component",
			));
			$t->define_field(array(
				"name" => "price_component_name",
				"caption" => t("Nimi"),
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

	public function callback_content_table_price_component_apply($row)
	{
		$compulsory = $this->offer->price_component_is_compulsory($row["price_component"]);
		if($compulsory)
		{			
			return html::checkbox(array(
				"name" => "content_table[{$row["row"]->id()}][price_component][{$row["price_component"]->id()}][apply_dummy]",
				"checked" => true,
				"disabled" => true,
			)).html::hidden(array(
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
			));
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
				"onChange" => "awCrmOffer.calculateRow({$row["row"]->id()});"
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
	}

	public function _set_content_table($arr)
	{
		$data = $arr["prop"]["value"];
		if(isset($data) && is_array($data))
		{
			foreach($data as $row_id => $row_data)
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

	public function _set_customer($arr)
	{
		if(!is_oid($arr["prop"]["value"]))
		{
			$arr["prop"]["error"] = t("Palun sisestage olemasolev klient!");
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

	public function callback_generate_scripts($arr)
	{
		$js = "";

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

			$aw_crm_offer = array(
				"rows" => $aw_crm_offer_rows,
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

	public function callback_mod_reforb(&$arr)
	{
		$arr["post_ru"] = post_ru();
	}

	public function do_db_upgrade($t, $f)
	{
		if ($f == "")
		{
			$this->db_query("CREATE TABLE aw_crm_offer(aw_oid int primary key)");
			return true;
		}

		switch($f)
		{
			case "aw_customer_relation":
			case "aw_salesman":
			case "aw_customer":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int(11)"
				));
				return true;
		}
	}
}

?>
