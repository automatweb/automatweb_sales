<?php

class crm_sales_contacts_view
{
	public static $sort_modes = array();

	public static function _get_contacts_tree(&$arr)
	{
		$tree = $arr["prop"]["vcl_inst"];

		foreach (crm_sales::$contacts_list_views as $key => $data)
		{
			if ($data["in_tree"])
			{
				$url = automatweb::$request->get_uri();

				if (crm_sales::CONTACTS_DEFAULT === $key)
				{
					$url->unset_arg(array(
						"ft_page",
						"cts_submit",
						"cts_name",
						"cts_salesman",
						"cts_lead_source",
						"cts_calls",
						"cts_address",
						"cts_phone",
						"cts_sort_mode",
						"cts_count",
						"cts_status"
					));
				}

				$tree->add_item (0, array (
					"name" => $data["caption"],
					"id" => $key,
					"parent" => 0,
					"url" => $url->get()
				));
			}
		}

		$tree->set_selected_item (crm_sales::$contacts_list_view);
		return PROP_OK;
	}

	public static function _get_contacts_toolbar(&$arr)
	{
		$toolbar = $arr["prop"]["vcl_inst"];
		$toolbar->add_button(array(
			"name" => "create_calls1",
			"img" => "create_calls1.gif",
			"action" => "create_calls_for_selected_contacts",
			"tooltip" => t("Loo k&otilde;ned valitud kontaktidele")
		));
		$toolbar->add_button(array(
			"name" => "create_calls2",
			"img" => "create_calls2.gif",
			"action" => "create_calls_for_found_contacts",
			"confirm" => t("Kui otsingutulemusi on palju, v&otilde;ib k&otilde;nede loomine palju aega v&otilde;tta. J&auml;tkata?"),
			"tooltip" => t("Loo k&otilde;ned k&otilde;igile leitud kontaktidele")
		));
		$toolbar->add_delete_button();
		return PROP_OK;
	}

	public static function get_contacts_list(&$arr)
	{
		$contacts_oid_data = array();
		$contacts_count = 0;

		if (crm_sales::CONTACTS_SEARCH === crm_sales::$contacts_list_view)
		{
			$this_o = $arr["obj_inst"];
			$per_page = $this_o->prop("tables_rows_per_page");
			$page = isset($arr["request"]["ft_page"]) ? (int) $arr["request"]["ft_page"] : 0;
			$start = $page*$per_page;
			$limit = new obj_predicate_limit($per_page, $start);

			$search = new crm_sales_contacts_search();

			try
			{
				$search->application = $this_o;

				if (!empty($arr["request"]["cts_name"]))
				{
					$search->name = $arr["request"]["cts_name"];
				}

				if (!empty($arr["request"]["cts_phone"]))
				{
					$search->phone = $arr["request"]["cts_phone"];
				}

				if (!empty($arr["request"]["cts_salesman"]))
				{
					$search->salesman = $arr["request"]["cts_salesman"];
				}

				if (!empty($arr["request"]["cts_calls"]))
				{
					$tmp = preg_split("/\s*/", $arr["request"]["cts_calls"], 2, PREG_SPLIT_NO_EMPTY);
					if (1 === count($tmp) and is_numeric($tmp[0]))
					{
						$search->calls = new obj_predicate_compare(obj_predicate_compare::EQUAL, (int) $tmp[0]);
					}
					elseif (2 === count($tmp) and is_numeric($tmp[1]))
					{
						$calls_compare_value = (int) $tmp[1];
						if ("<" === $tmp[0] and $calls_compare_value)
						{
							$search->calls = new obj_predicate_compare(obj_predicate_compare::LESS, $calls_compare_value);
						}
						elseif (">" === $tmp[0])
						{
							$search->calls = new obj_predicate_compare(obj_predicate_compare::GREATER, $calls_compare_value);
						}
						elseif ("=" === $tmp[0])
						{
							$search->calls = new obj_predicate_compare(obj_predicate_compare::EQUAL, (int) $tmp[1]);
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
				}

				if (!empty($arr["request"]["cts_lead_source"]))
				{
					$search->lead_source = $arr["request"]["cts_lead_source"];
				}

				if (!empty($arr["request"]["cts_address"]))
				{
					$search->address = $arr["request"]["cts_address"];
				}

				if (!empty($arr["request"]["cts_status"]))
				{
					$search->status = $arr["request"]["cts_status"];
				}

				if (!empty($arr["request"]["cts_count"]))
				{
					$contacts_count = $search->count();
				}

				if (!empty($arr["request"]["cts_sort_mode"]))
				{
					$search->set_sort_order($arr["request"]["cts_sort_mode"]);
				}

				$contacts_oid_data = $search->get_oids($limit);
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
		}

		return array($contacts_oid_data, $contacts_count);
	}


	public static function _get_contacts_list(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];
		$table->set_hover_hilight(true);
		$owner = $arr["obj_inst"]->prop("owner");
		list($contacts_oid_data, $contacts_count) = self::get_contacts_list($arr);
		self::define_contacts_list_tbl_header($arr, $contacts_count);
		$not_available_str = "";


		if (count($contacts_oid_data))
		{
			foreach ($contacts_oid_data as $contact_data)
			{
				$customer_relation = obj($contact_data["oid"], array(), CL_CRM_COMPANY_CUSTOMER_DATA);
				$customer = new object($customer_relation->prop("buyer"));

				// sales state
				$sales_state_int = $customer_relation->prop("sales_state");
				$sales_state = crm_company_customer_data_obj::sales_state_names($sales_state_int);

				// salesman and unit
				$salesman = $customer_relation->prop("salesman");
				if (is_oid($salesman))
				{
					$salesman = new object($salesman);
					// assigned salesman's company unit
					$unit = $salesman->get_org_section();
					if ($unit)
					{
						$unit = new object($unit);
						$unit = $unit->name();
					}

					$salesman = $salesman->name();
				}
				else
				{
					$unit = $salesman = $not_available_str;
				}

				// calls
				$calls_made = $this_o->get_calls_count($customer_relation, 0, "all");
				if ($calls_made > 0)
				{
					$last_call = $this_o->get_last_call_made($customer_relation);
					$last_call_timestamp =  $last_call->prop("start1");
					$last_call_result_int = $last_call->prop("result");
					$last_call_result = crm_call_obj::result_names($last_call_result_int);
					$last_call_result = reset($last_call_result);
					$last_call_maker = $last_call->prop("real_maker.name");
					$last_call = aw_locale::get_lc_date($last_call->prop("real_start"), aw_locale::DATETIME_SHORT_FULLYEAR);
				}
				else
				{
					$last_call = $last_call_result = $last_call_maker = $not_available_str;
					$last_call_timestamp = $last_call_result_int = 0;
				}

				// lead source
				$lead_source = $customer_relation->prop("sales_lead_source");
				if ($lead_source)
				{
					$lead_source = new object($lead_source);
					$lead_source = $lead_source->name();
				}
				else
				{
					$lead_source = $not_available_str;
				}

				// phones
				$phones = new object_list($customer->connections_from(array("type" => "RELTYPE_PHONE")));
				$phones_str = array();
				$phone = $phones->begin();
				do
				{
					$number = trim($phone->name());
					if (strlen($number) > 1)
					{
						$request = (array) $arr["request"];
						$request["return_url"] = get_ru();
						unset($request["action"]);
						$phone_nr = $phone->name();
						$phones_str[] = $phone_nr;
					}
				}
				while ($phone = $phones->next());
				$phones_str = count($phones_str) ? implode(", ", $phones_str) : $not_available_str;

				// address
				$address = $customer->get_first_obj_by_reltype("RELTYPE_ADDRESS_ALT");
				$address = is_object($address) ? $address->name() : $not_available_str;

				// define table row
				$table->define_data(array(
					"name" => html::obj_change_url($customer, strlen($customer->name()) > 1 ? $customer->name() : t("[Nimetu]")),
					"phones" => $phones_str,
					"address" => $address,
					"unit" => $unit,
					"lead_source" => $lead_source,
					"sales_state" => $sales_state,
					"sales_state_int" => $sales_state_int,
					"last_call_maker" => $last_call_maker,
					"last_call_time" => $last_call,
					"last_call_timestamp" => $last_call_timestamp,
					"last_call_result" => $last_call_result,
					"last_call_result_int" => $last_call_result_int,
					"calls_made" => $calls_made,
					"oid" => $customer_relation->id(),
					"salesman" => $salesman
				));
			}

			if (crm_sales::CONTACTS_SEARCH === crm_sales::$contacts_list_view)
			{
				if (empty($arr["request"]["cts_count"]))
				{
					$page = isset($arr["request"]["ft_page"]) ? ($arr["request"]["ft_page"] + 1) : 1;
					$table->set_caption(sprintf(crm_sales::$contacts_list_views[crm_sales::CONTACTS_SEARCH]["caption_no_count"], $page));
				}
				else
				{
					$table->set_caption(sprintf(crm_sales::$contacts_list_views[crm_sales::CONTACTS_SEARCH]["caption"], $contacts_count));
				}
			}
			else
			{
				$table->set_caption(crm_sales::$contacts_list_views[crm_sales::$contacts_list_view]["caption"]);
			}
		}
		return PROP_OK;
	}

	public static function define_contacts_list_tbl_header(&$arr, $contacts_count)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];
		$table->define_chooser(array(
			"name" => "sel",
			"field" => "oid"
		));
		$table->define_field(array(
			"name" => "name",
			"caption" => t("Nimi")
		));
		$table->define_field(array(
			"name" => "phones",
			"caption" => t("Telefon(id)")
		));
		$table->define_field(array(
			"name" => "address",
			"caption" => t("Aadress")
		));
		$table->define_field(array(
			"name" => "sales_state",
			"caption" => t("Staatus")
		));
		$table->define_field(array(
			"name" => "last_call",
			"caption" => t("Viimane k&otilde;ne")
		));
		$table->define_field(array(
			"name" => "last_call_time",
			"parent" => "last_call",
			"caption" => t("aeg")
		));
		$table->define_field(array(
			"name" => "last_call_result",
			"parent" => "last_call",
			"caption" => t("tulemus")
		));
		$table->define_field(array(
			"name" => "last_call_maker",
			"parent" => "last_call",
			"caption" => t("tegija")
		));
		$table->define_field(array(
			"name" => "calls_made",
			"caption" => t("K&otilde;nesid")
		));
		$table->define_field(array(
			"name" => "salesman",
			"caption" => t("M&uuml;&uuml;giesindaja")
		));
		$table->define_field(array(
			"name" => "unit",
			"caption" => t("Osakond")
		));
		$table->define_field(array(
			"name" => "lead_source",
			"caption" => t("Allikas/soovitaja")
		));

		if ($contacts_count)
		{
			$table->define_pageselector (array (
				"type" => "lbtxt",
				"position" => "both",
				"d_row_cnt" => $contacts_count,
				"records_per_page" => $this_o->prop("tables_rows_per_page")
			));
		}
		else
		{
			$table->define_pageselector (array (
				"type" => "nav",
				"position" => "both"
			));
		}
	}
}

?>
