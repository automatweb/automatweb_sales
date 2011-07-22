<?php

class crm_sales_calls_view
{
	public static function _get_calls_tree(&$arr)
	{
		$tree = $arr["prop"]["vcl_inst"];

		foreach (crm_sales::$calls_list_views as $key => $data)
		{
			if ($data["in_tree"])
			{
				$url = automatweb::$request->get_uri();

				if (crm_sales::CALLS_CURRENT === $key)
				{
					$url->unset_arg(array(
						"ft_page",
						"sortby",
						"sort_order",
						"cs_submit",
						"cs_name",
						"cs_salesman",
						"cs_lead_source",
						"cs_last_caller",
						"cs_last_call_result",
						"cs_caller",
						"cs_call_result",
						"cs_call_real_start_from",
						"cs_call_real_start_to",
						"cs_address",
						"cs_phone",
						"cs_count",
						"cs_status"
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

		$tree->set_selected_item (crm_sales::$calls_list_view);
		return PROP_OK;
	}

	public static function _get_calls_toolbar(&$arr)
	{
		$this_o = $arr["obj_inst"];
		if ($this_o->has_privilege("call_edit"))
		{
			$toolbar = $arr["prop"]["vcl_inst"];
			$toolbar->add_delete_button();
			$r = PROP_OK;
		}
		else
		{
			$r = PROP_IGNORE;
		}

		return $r;
	}

	private static function get_calls_list(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$filter = array();
		$calls_count = 0;

		// result limit
		$per_page = $this_o->prop("tables_rows_per_page");
		$page = isset($arr["request"]["ft_page"]) ? (int) $arr["request"]["ft_page"] : 0;
		$start = $page*$per_page;
		$limit = new obj_predicate_limit($per_page + 1, $start);// plus one is to make nav pageselector aware that there's a next page

		// sorting
		$sort_modes = array(
			"name" => "name",
			"last_call_time" => "last_call_time",
			"last_call_maker" => "last_call_maker",
			"last_call_result" => "last_call_result",
			"calls_made" => "calls_made",
			"call_time" => "deadline",
			"salesman" => "salesman"
		);
		$sort_by = empty($arr["request"]["sortby"]) ? "" : $arr["request"]["sortby"];
		$sort_dir = (!empty($arr["request"]["sort_order"]) and "DESC" === strtoupper($arr["request"]["sort_order"])) ? " DESC" : " ASC";
		$sort_mode = isset($sort_modes[$sort_by]) ? $sort_modes[$sort_by] . $sort_dir : "current";

//!!! tmp paginaatori jaoks
if (!empty($arr["request"]["cs_count"]))
{
	$application = automatweb::$request->get_application();
	if ($application->is_a(CL_CRM_SALES) and crm_sales_obj::ROLE_TELEMARKETING_MANAGER == $application->get_current_user_role())
	{
		$calls_count = 100000;//!!! tmp
	}
	else
	{
		$calls_count = 1000;//!!! tmp
	}
}
//!!! END tmp paginaatori jaoks

		if (crm_sales::CALLS_SEARCH === crm_sales::$calls_list_view)
		{
			// search by address and/or phone only -- special optimization
			if (
				(!empty($arr["request"]["cs_address"]) or !empty($arr["request"]["cs_phone"]))
				and empty($arr["request"]["cs_name"])
				and empty($arr["request"]["cs_salesman"])
				and empty($arr["request"]["cs_last_caller"])
				and empty($arr["request"]["cs_last_call_result"])
				and empty($arr["request"]["cs_caller"])
				and empty($arr["request"]["cs_call_result"])
				and empty($arr["request"]["cs_call_real_start_from"]["date"])
				and empty($arr["request"]["cs_call_real_start_to"]["date"])
				and empty($arr["request"]["cs_lead_source"])
			)
			{
				$phone_string = $address_search_string = "";

				if (!empty($arr["request"]["cs_address"]))
				{
					$address_search_string = crm_sales::parse_search_string($arr["request"]["cs_address"]);
				}

				if (!empty($arr["request"]["cs_phone"]))
				{
					$phone_string = (string)(int) $arr["request"]["cs_phone"];
				}

				$status = (int) $arr["request"]["cs_status"];
				$params = array(
					"address" => $address_search_string,
					"phone" => $phone_string,
					"status" => $status
				);

				if (!empty($arr["request"]["cs_count"]))
				{
					$calls_count = $this_o->get_current_calls_to_make(null, $params, $sort_mode);
				}

				$calls = $this_o->get_current_calls_to_make($limit, $params, $sort_mode);
				return array($calls, $calls_count);
			}

			if (!empty($arr["request"]["cs_name"]))
			{
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_COMPANY).name" => "%{$arr["request"]["cs_name"]}%",
						"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_PERSON).name" => "%{$arr["request"]["cs_name"]}%"
					)
				));
			}

			if (!empty($arr["request"]["cs_phone"]))
			{
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_COMPANY).RELTYPE_PHONE.name" => "{$arr["request"]["cs_phone"]}%",
						"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_PERSON).RELTYPE_PHONE.name" => "{$arr["request"]["cs_phone"]}%"
					)
				));
			}

			if (!empty($arr["request"]["cs_salesman"]))
			{
				$filter["CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).salesman"] = $arr["request"]["cs_salesman"];
			}

			if (!empty($arr["request"]["cs_last_caller"]))
			{
				$filter["CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_last_call(CL_CRM_CALL).real_maker"] = $arr["request"]["cs_last_caller"];
			}

			if (!empty($arr["request"]["cs_last_call_result"]))
			{
				$filter["CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_last_call(CL_CRM_CALL).result"] = $arr["request"]["cs_last_call_result"];
			}

			if (!empty($arr["request"]["cs_caller"]))
			{
				$filter["real_maker"] = $arr["request"]["cs_caller"];
			}

			if (!empty($arr["request"]["cs_call_result"]))
			{
				$filter["result"] = $arr["request"]["cs_call_result"];
			}

			if (!empty($arr["request"]["cs_call_real_start_from"]["date"]) or !empty($arr["request"]["cs_call_real_start_to"]["date"]))
			{
				$from = datepicker::get_timestamp($arr["request"]["cs_call_real_start_from"]);
				$to = datepicker::get_timestamp($arr["request"]["cs_call_real_start_to"]);
				$to = $to < 1 ? ($from + 86400) : $to;
				$filter["real_start"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			}

			if (!empty($arr["request"]["cs_lead_source"]))
			{
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_lead_source(CL_CRM_COMPANY).name" => "%{$arr["request"]["cs_lead_source"]}%",
						"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_lead_source(CL_CRM_PERSON).name" => "%{$arr["request"]["cs_lead_source"]}%"
					)
				));
			}

			if (!empty($arr["request"]["cs_address"]))
			{
				$address_search_string = crm_sales::parse_search_string($arr["request"]["cs_address"]);
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_COMPANY).RELTYPE_ADDRESS_ALT.name" => $address_search_string,
						"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_PERSON).RELTYPE_ADDRESS_ALT.name" => $address_search_string
					)
				));
			}

			if (!empty($arr["request"]["cs_status"]))
			{
				$filter["CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_state"] = $arr["request"]["cs_status"];
			}
		}
		elseif (crm_sales::CALLS_CURRENT === crm_sales::$calls_list_view)
		{
			$calls = $this_o->get_current_calls_to_make($limit, array(), $sort_mode);
			return array($calls, $calls_count);
		}


		// pagination and limit
		$filter[] = $limit;

		// sorting
		// no default order
		$sortable_fields = array( // table field => array( default sort order, database field name)
			"name" => array(obj_predicate_sort::ASC, "CL_CRM_CALL.name"),
			"unit" => array(obj_predicate_sort::ASC, "CL_CRM_CALL.name"),
			"last_call_time" => array(obj_predicate_sort::ASC, "CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_last_call(CL_CRM_CALL).real_start"),
			"last_call_result" => array(obj_predicate_sort::ASC, "CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_last_call(CL_CRM_CALL).result"),
			"last_call_maker" => array(obj_predicate_sort::ASC, "CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_last_call(CL_CRM_CALL).real_maker"),
			"call_time" => array(obj_predicate_sort::ASC, "CL_CRM_CALL.start1"),
			"calls_made" => array(obj_predicate_sort::ASC, "CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_calls_made"),
			"salesman" => array(obj_predicate_sort::ASC, "CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).salesman(CL_CRM_PERSON).name")
		);
		if (isset($arr["request"]["sortby"]) and isset($sortable_fields[$arr["request"]["sortby"]]))
		{
			$sort_by = $sortable_fields[$arr["request"]["sortby"]][1];
			$sort_dir = isset($arr["request"]["sortby"]) ? ($arr["request"]["sortby"] === "asc" ? obj_predicate_sort::ASC : obj_predicate_sort::DESC) : $sortable_fields[$arr["request"]["sortby"]][0];
		}
		//!!! sortimine praegu tegemata. sortida ei saa telemarketing, teised saavad
		if (crm_sales::CALLS_CURRENT === crm_sales::$calls_list_view)
		{
			$filter["real_duration"] = new obj_predicate_compare(obj_predicate_compare::LESS, 1);
			$sort_filter = array(
				"CL_CRM_CALL.real_start" => array(new obj_predicate_compare(obj_predicate_compare::GREATER, 1), obj_predicate_sort::ASC),// in progress on top
				"CL_CRM_CALL.deadline" => array(new obj_predicate_compare(obj_predicate_compare::LESS, time()), obj_predicate_sort::ASC),// overdue first
				"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_lead_source" => obj_predicate_sort::DESC, // with lead source next
				"CL_CRM_CALL.deadline" => array(new obj_predicate_compare(obj_predicate_compare::GREATER, 10000), obj_predicate_sort::ASC) // finally all others in order
			);
		}
		else
		{
			$sort_filter = array(
				"CL_CRM_CALL.deadline" => array(new obj_predicate_compare(obj_predicate_compare::LESS, time()), obj_predicate_sort::ASC),// overdue first
				"CL_CRM_CALL.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_lead_source" => obj_predicate_sort::DESC, // with lead source next
				"CL_CRM_CALL.deadline" => array(new obj_predicate_compare(obj_predicate_compare::GREATER, 10000), obj_predicate_sort::ASC) // finally all others in order
			);
		}
		$filter[] = new obj_predicate_sort($sort_filter);

		// ...
		$calls = new crm_call_list($filter);
		return array($calls, $calls_count);
	}

	public static function _get_calls_list(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];
		$table->set_hover_hilight(true);
		$owner = $this_o->prop("owner");
		list($calls, $calls_count) = self::get_calls_list($arr);
		self::define_calls_list_tbl_header($arr, $calls_count);
		$not_available_str = "";
		$locked_str = t("Objekt lukustatud");
		$current_person = get_current_person();
		$current_time = time();
		$core = new core();
		$cache = new cache();
		$salesman_unit_cache = array();

		if ($calls->count())
		{
			$call = $calls->begin();
			do
			{
				if ($call->is_locked())
				{
					$table->define_data(array(
						"name" => t("[Lukustatud]")
					));
					continue;
				}

				$call_oid = $call->id();
				$customer_relation = new object($call->prop("customer_relation"));

				if (!$customer_relation->is_saved())
				{ // a call object in sales application with no customer relation or with a deleted one. delete this call object
					$table->define_data(array(
						"name" => t("Kontaktandmeteta k&otilde;ne. Andmebaas vajab parandamist.")
					));
				}
				else
				{
					$customer = new object($customer_relation->prop("buyer"));

					// check call cache
					$user_can_edit_calls = (int) $this_o->has_privilege("call_edit");
					$call_key = "__crm_sales_call_cache_{$call_oid}_{$user_can_edit_calls}";
					$mod_time = max($call->modified(), $customer_relation->modified(), $customer->modified());

					if ($call->prop("real_start") > 1 and $call->prop("real_duration") < 1) // to avoid getting other person's editable unfinished calls from cache
					{
						$call_data = false;
					}
					else
					{
						$call_data = $cache->file_get_ts($call_key, $mod_time);
					}

					if (false === $call_data)
					{
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
						$salesman_unit_cache[$salesman] = $unit;

						// calls
						$call_timestamp =  $call->prop("start1");
						$call_time = $call_timestamp > 1 ? aw_locale::get_lc_date($call_timestamp, aw_locale::DATETIME_SHORT_FULLYEAR) : $not_available_str;
						$calls_made = $this_o->get_calls_count($customer_relation, 0, "all");

						if ($calls_made > 0)
						{ // last call info
							$last_call = $this_o->get_last_call_made($customer_relation);

							if (!is_object($last_call) or $last_call->is_locked())
							{
								$last_call_timestamp = $last_call_result_int = 0;
								$last_call_time = $last_call_result = $last_call_maker = $locked_str;
							}
							else
							{
								$last_call_result_int = $last_call->prop("result");
								$last_call_result = crm_call_obj::result_names($last_call_result_int);
								$last_call_result = reset($last_call_result);
								$last_call_maker = $last_call->prop("real_maker.name");
								$last_call_timestamp = $last_call->prop("real_start");
								$last_call_time = $last_call_timestamp > 1 ? aw_locale::get_lc_date($last_call_timestamp, aw_locale::DATETIME_SHORT_FULLYEAR) : $not_available_str;
							}
						}
						else
						{ // empty last call info values for this table row
							$last_call_timestamp = $last_call_result_int = 0;
							$last_call_time = $last_call_result = $last_call_maker = $not_available_str;
						}

		/* lead source not used at this time. reserve code for future use
						// customer lead source
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
		*/

						// phones
						$phones = new object_list($customer->connections_from(array("type" => "RELTYPE_PHONE")));
						$phones_str = array();

						if ($phones->count())
						{
							$phone = $phones->begin();
							do
							{
								$phone_nr = trim($phone->name());
								$type = $phone->prop("type");
								if (strlen($phone_nr) > 1 and (!$type or $type === "work" or $type === "home" or $type === "mobile"))
								{
									if ($call->prop("real_start") < 2)
									{ // a normal unstarted call
										$url = $core->mk_my_orb("change", array(
											"id" => $call->id(),
											"return_url" => "{URLVAR:return_url}",
											"preparing_to_call" => 1,
											"phone_id" => $phone->id()
										), "crm_call");
										$title = t("Ava k&otilde;ne sel numbril");
										$phone_nr = html::href(array("caption" => $phone_nr, "url" => $url, "title" => $title));
									}
									elseif ($call->prop("real_duration") < 1 and trim($call->prop("phone.name")) === $phone_nr)
									{ // a call made to this number that is started but not finished
										$phone_nr = "<span style=\"color: red;\">" . $phone_nr . "</span>";
										if ($user_can_edit_calls or $call->prop("real_maker") == $current_person->id())
										{
											$url = $core->mk_my_orb("change", array(
												"id" => $call->id(),
												"unlock_call" => 1,
												"return_url" => "{URLVAR:return_url}"
											), "crm_call");
											$title = t("L&otilde;petamata k&otilde;ne");
											$phone_nr = html::href(array("caption" => $phone_nr, "url" => $url, "title" => $title));
										}
									}
									elseif (trim($call->prop("phone.name")) === $phone_nr)
									{ // call was made to this number
										$url = $core->mk_my_orb("change", array(
											"id" => $call->id(),
											"return_url" => "{URLVAR:return_url}"
										), "crm_call");
										$title = t("Tehtud k&otilde;ne");
										$phone_nr = html::href(array("caption" => $phone_nr, "url" => $url, "title" => $title));
									}
									else
									{ // a call has been made but not on this number
									}

									$phones_str[] = $phone_nr;
								}
							}
							while ($phone = $phones->next());
							$phones_str = implode(", ", $phones_str);
						}
						else
						{
							$phones_str = $not_available_str;
						}

						// address
						$address = $customer->get_first_obj_by_reltype("RELTYPE_ADDRESS_ALT");
						$address = is_object($address) ? $address->name() : $not_available_str;
						$customer_name = crm_sales::parse_customer_name($customer);

						// name/edit link
						if ($user_can_edit_calls)
						{
							$url = $core->mk_my_orb("change", array(
								"id" => $customer->id(),
								"return_url" => "{URLVAR:return_url}"
							), $customer->class_id());
							$customer_name = html::href(array("caption" => $customer_name, "url" => $url, "title" => t("Muuda/vaata kontakti andmeid")));
						}

						$call_data = array(
							"name" => $customer_name,
							"phones" => $phones_str,
							"address" => $address,
							"unit" => $unit,
							// "lead_source" => $lead_source,
							"last_call_time" => $last_call_time,
							"last_call_timestamp" => $last_call_timestamp,
							"last_call_maker" => $last_call_maker,
							"call_time" => $call_time,
							"call_timestamp" => $call_timestamp,
							"last_call_result" => $last_call_result,
							"last_call_result_int" => $last_call_result_int,
							"calls_made" => $calls_made,
							"oid" => $call_oid,
							"salesman" => $salesman
						);

						$cache->file_set($call_key, serialize($call_data));
					}
					else
					{
						$call_data = unserialize($call_data);
						$salesman = $call_data["salesman"];
						if (strlen($salesman) > 1 and isset($salesman_unit_cache[$salesman]))
						{
							$call_data["unit"] = $salesman_unit_cache[$salesman];
						}
					}

					// get call row bgcolour
					$call_bgcolour = "";
					if ($call->is_in_progress())
					{
						if ($user_can_edit_calls or $call->prop("real_maker") == $current_person->id())
						{
							$call_bgcolour = crm_sales::COLOUR_IN_PROGRESS;
						}
						else
						{
							$call_bgcolour = crm_sales::COLOUR_IN_PROGRESS;
							$call_data["phones"] = t("Helistamisel");
						}
					}
					// elseif ($call->can_start())
					//!!! can start is too slow
					elseif (($call->prop("real_start") < 2))
					{
						if ($call->prop("deadline") > 10000 and $current_time > $call->prop("deadline")) /// 10000 to exclude some accidental date entry errors
						{
							$call_bgcolour = crm_sales::COLOUR_OVERDUE;
						}
						else
						{
							$call_bgcolour = crm_sales::COLOUR_CAN_START;
						}
					}
					elseif ($call->prop("real_duration") > 0)
					{
						$call_bgcolour = crm_sales::COLOUR_DONE;
					}
					$call_data["call_bgcolour"] = $call_bgcolour;

					// insert current return url to links
					$call_data["phones"] = str_replace("%7BURLVAR%3Areturn_url%7D", urlencode(get_ru()), $call_data["phones"]);
					$call_data["name"] = str_replace("%7BURLVAR%3Areturn_url%7D", urlencode(get_ru()), $call_data["name"]);


					// define table row
					$table->define_data($call_data);
				}
			}
			while ($call = $calls->next());

			// define calls table caption
			if (crm_sales::CALLS_SEARCH === crm_sales::$calls_list_view)
			{
				if (empty($arr["request"]["cs_count"]))
				{
					$page = isset($arr["request"]["ft_page"]) ? ($arr["request"]["ft_page"] + 1) : 1;
					$table->set_caption(sprintf(crm_sales::$calls_list_views[crm_sales::CALLS_SEARCH]["caption_no_count"], $page));
				}
				else
				{
					$table->set_caption(sprintf(crm_sales::$calls_list_views[crm_sales::CALLS_SEARCH]["caption"], $calls_count));
				}
			}
			else
			{
				$table->set_caption(crm_sales::$calls_list_views[crm_sales::$calls_list_view]["caption"]);
			}

			// define page selector
			if ($calls_count)
			{
				$table->define_pageselector (array (
					"type" => (crm_sales::CALLS_CURRENT === crm_sales::$calls_list_view) ? "nav" : "lbtxt",
					"position" => "both",
					"d_row_cnt" => $calls_count,
					"records_per_page" => $this_o->prop("tables_rows_per_page")
				));
			}
			else
			{
				$table->define_pageselector (array (
					"type" => "nav",
					"records_per_page" => $this_o->prop("tables_rows_per_page"),
					"d_row_cnt" => $calls->count(),
					"position" => "both"
				));
			}
		}
		return PROP_OK;
	}

	private static function define_calls_list_tbl_header(&$arr, $calls_count)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];

		// telemarketing sales can only see calls in mandatory calling order
		$can_sort =
			crm_sales_obj::ROLE_TELEMARKETING_SALESMAN !== $this_o->get_current_user_role() or
			crm_sales::CALLS_SEARCH === crm_sales::$calls_list_view
		;

		if ($this_o->has_privilege("call_edit"))
		{
			$table->define_chooser(array(
				"name" => "sel",
				"field" => "oid"
			));
		}

		$table->define_field(array(
			"name" => "name",
			"sortable" => $can_sort,
			"chgbgcolor" => "call_bgcolour",
			"caption" => t("Kliendi nimi")
		));
		$table->define_field(array(
			"name" => "phones",
			"chgbgcolor" => "call_bgcolour",
			"caption" => t("Telefon(id)")
		));
		$table->define_field(array(
			"name" => "address",
			"chgbgcolor" => "call_bgcolour",
			"caption" => t("Aadress")
		));
		// $table->define_field(array(
			// "name" => "lead_source",
			// "caption" => t("Allikas/soovitaja")
		// ));
		$table->define_field(array(
			"name" => "last_call",
			"chgbgcolor" => "call_bgcolour",
			"caption" => t("Viimane k&otilde;ne")
		));
		$table->define_field(array(
			"name" => "last_call_time",
			"sortable" => $can_sort,
			"sorting_field" => "last_call_timestamp",
			"chgbgcolor" => "call_bgcolour",
			"parent" => "last_call",
			"caption" => t("aeg")
		));
		$table->define_field(array(
			"name" => "last_call_maker",
			"sortable" => $can_sort,
			"chgbgcolor" => "call_bgcolour",
			"parent" => "last_call",
			"caption" => t("tegija")
		));
		$table->define_field(array(
			"name" => "last_call_result",
			"sortable" => $can_sort,
			"sorting_field" => "last_call_result_int",
			"chgbgcolor" => "call_bgcolour",
			"parent" => "last_call",
			"caption" => t("tulemus")
		));
		$table->define_field(array(
			"name" => "call_time",
			"sortable" => $can_sort,
			"chgbgcolor" => "call_bgcolour",
			"sorting_field" => "call_timestamp",
			"caption" => t("Helistada")
		));
		$table->define_field(array(
			"name" => "calls_made",
			"sortable" => $can_sort,
			"chgbgcolor" => "call_bgcolour",
			"caption" => t("K"),
			"tooltip" => t("Tehtud k&otilde;nesid")
		));
		$table->define_field(array(
			"name" => "salesman",
			"sortable" => $can_sort,
			"chgbgcolor" => "call_bgcolour",
			"caption" => t("M&uuml;&uuml;giesindaja")
		));
		$table->define_field(array(
			"name" => "unit",
			"chgbgcolor" => "call_bgcolour",
			"caption" => t("Osakond")
		));

		$table->set_numeric_field(array("call_timestamp", "last_call_timestamp", "last_call_result_int"));
		$table->set_footer(self::draw_colour_legend());
	}

	private static function draw_colour_legend ()
	{
		$dfn = "";
		$rows = "";
		$i = 1;
		$state_colours = array();
		$state_colours["can_start"] = crm_sales::COLOUR_CAN_START;
		$state_colours["overdue"] = crm_sales::COLOUR_OVERDUE;
		$state_colours["in_progress"] = crm_sales::COLOUR_IN_PROGRESS;
		$state_colours["done"] = crm_sales::COLOUR_DONE;
		$states = array();
		$states["can_start"] = t("Saab alustada");
		$states["overdue"] = t("&Uuml;le t&auml;htaja");
		$states["in_progress"] = t("L&otilde;petamata");
		$states["done"] = t("Tehtud");

		foreach ($state_colours as $state => $colour)
		{
			$name = $states[$state];
			$dfn .= '<td class="awmenuedittabletext" style="background-color: ' . $colour . '; width: 30px;">&nbsp;</td><td class="awmenuedittabletext" style="padding: 0px 15px 0px 6px;">' . $name . '</td>';

			if (!(($i++)%5))
			{
				$rows .= '<tr>' . $dfn . '</tr>';
				$dfn = "";
			}
		}

		$rows .= '<tr>' . $dfn . '</tr>';
		return '<table cellspacing="4" cellpadding="0">' . $rows . '</table>';
	}
}

?>
