<?php

class crm_sales_presentations_view
{
	public static function _get_presentations_tree(&$arr)
	{
		$tree = $arr["prop"]["vcl_inst"];
		$views_to_clear_search_for = array(
			crm_sales::PRESENTATIONS_YESTERDAY,
			crm_sales::PRESENTATIONS_TODAY,
			crm_sales::PRESENTATIONS_TOMORROW,
			crm_sales::PRESENTATIONS_ADDED_TODAY,
			crm_sales::PRESENTATIONS_DEFAULT
		);


		foreach (crm_sales::$presentations_list_views as $key => $data)
		{
			if ($data["in_tree"])
			{
				$url = automatweb::$request->get_uri();
				$url->set_arg("crmListId", $key);

				if (in_array($key, $views_to_clear_search_for))
				{
					$url->unset_arg(array(
						"ft_page",
						"ps_submit",
						"ps_name",
						"ps_salesman",
						"ps_lead_source",
						"ps_address",
						"ps_created_from",
						"ps_created_to",
						"ps_start_from",
						"ps_start_to",
						"ps_real_start_from",
						"ps_real_start_to",
						"ps_createdby",
						"ps_result",
						"ps_phone"
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

		$tree->set_selected_item (crm_sales::$presentations_list_view);
		return PROP_OK;
	}

	public static function _get_presentations_toolbar(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$r = PROP_IGNORE;

		if ($this_o->has_privilege("presentation_edit"))
		{
			$toolbar = $arr["prop"]["vcl_inst"];
			$toolbar->add_delete_button();
			$r = PROP_OK;
		}

		return $r;
	}

	protected static function get_presentations_list(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$filter = array();
		$limit_results = true;

		if (crm_sales::PRESENTATIONS_SEARCH === crm_sales::$presentations_list_view)
		{
			if (!empty($arr["request"]["ps_name"]))
			{
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						"CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_COMPANY).name" => "%{$arr["request"]["ps_name"]}%",
						"CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_PERSON).name" => "%{$arr["request"]["ps_name"]}%"
					)
				));
			}

			if (!empty($arr["request"]["ps_phone"]))
			{
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						"CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_COMPANY).RELTYPE_PHONE.name" => "{$arr["request"]["ps_phone"]}%",
						"CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_PERSON).RELTYPE_PHONE.name" => "{$arr["request"]["ps_phone"]}%"
					)
				));
			}

			if (!empty($arr["request"]["ps_salesman"]))
			{
				$filter["CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).salesman"] = $arr["request"]["ps_salesman"];
			}

			if (!empty($arr["request"]["ps_createdby"]))
			{
				$person = obj($arr["request"]["ps_createdby"], array(), CL_CRM_PERSON);
				$user = $person->instance()->has_user($person);
				if($user !== false)
				{
					$uid = $user->prop("uid");
					$filter["createdby"] = $uid;
				}
			}

			if (!empty($arr["request"]["ps_result"]))
			{
				$filter["result"] = $arr["request"]["ps_result"];
			}

			if (!empty($arr["request"]["ps_lead_source"]))
			{
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						"CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_lead_source(CL_CRM_COMPANY).name" => "%{$arr["request"]["ps_lead_source"]}%",
						"CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).sales_lead_source(CL_CRM_PERSON).name" => "%{$arr["request"]["ps_lead_source"]}%"
					)
				));
			}

			if (!empty($arr["request"]["ps_address"]))
			{
				$address_search_string = crm_sales::parse_search_string($arr["request"]["ps_address"]);
				$filter[] = new object_list_filter(array(
					"logic" => "OR",
					"conditions" => array (
						"CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_COMPANY).RELTYPE_ADDRESS_ALT.name" => "%{$address_search_string}%",
						"CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).buyer(CL_CRM_PERSON).RELTYPE_ADDRESS_ALT.name" => "%{$address_search_string}%"
					)
				));
			}

			if (!empty($arr["request"]["ps_created_from"]["date"]) or !empty($arr["request"]["ps_created_to"]["date"]))
			{
				$from = datepicker::get_timestamp($arr["request"]["ps_created_from"]);
				$to = datepicker::get_timestamp($arr["request"]["ps_created_to"]);
				$to = $to < 1 ? ($from + 86400) : $to;
				$filter["created"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			}

			if (!empty($arr["request"]["ps_start_from"]["date"]) or !empty($arr["request"]["ps_start_to"]["date"]))
			{
				$from = datepicker::get_timestamp($arr["request"]["ps_start_from"]);
				$to = datepicker::get_timestamp($arr["request"]["ps_start_to"]);
				$to = $to < 1 ? ($from + 86400) : $to;
				$filter["start1"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			}

			if (!empty($arr["request"]["ps_real_start_from"]["date"]) or !empty($arr["request"]["ps_real_start_to"]["date"]))
			{
				$from = datepicker::get_timestamp($arr["request"]["ps_real_start_from"]);
				$to = datepicker::get_timestamp($arr["request"]["ps_real_start_to"]);
				$to = $to < 1 ? ($from + 86400) : $to;
				$filter["real_start"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			}
		}
		elseif (crm_sales::PRESENTATIONS_ADDED_TODAY === crm_sales::$presentations_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
			$to = $from + 86400;
			$filter["created"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			$arr["request"]["sortby"] = "salesman";
		}
		elseif (crm_sales::PRESENTATIONS_TODAY === crm_sales::$presentations_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
			$to = $from + 86400;
			$filter["start1"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			$arr["request"]["sortby"] = "salesman";
		}
		elseif (crm_sales::PRESENTATIONS_TOMORROW === crm_sales::$presentations_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), (date("j") + 1), date("Y"));
			$to = $from + 86400;
			$filter["start1"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			$arr["request"]["sortby"] = "salesman";
		}
		elseif (crm_sales::PRESENTATIONS_YESTERDAY === crm_sales::$presentations_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), (date("j") - 1), date("Y"));
			$to = $from + 86400;
			$filter["start1"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			$arr["request"]["sortby"] = "salesman";
		}

		// pagination and limit
		// $default_filter = new crm_presentation_list();
		// $default_filter = $default_filter->get_filter();
//!!!! tmp
$default_filter = array();
$default_filter["class_id"] = CL_CRM_PRESENTATION;
$application = automatweb::$request->get_application();
$default_filter["parent"] = $application->prop("presentations_folder");
$role = automatweb::$request->get_application()->get_current_user_role();
switch ($role)
{
case crm_sales_obj::ROLE_DATA_ENTRY_CLERK:
$default_filter = array();
break;
case crm_sales_obj::ROLE_TELEMARKETING_SALESMAN:
$default_filter["createdby"] = aw_global_get("uid");
break;
case crm_sales_obj::ROLE_SALESMAN:
$current_person = get_current_person();
$default_filter["CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).salesman"] = $current_person->id();
break;
}
//!!!! END tmp



		$presentations_count = new object_data_list(
			array_merge($filter, $default_filter),
			array(
				CL_CRM_PRESENTATION =>  array(new obj_sql_func(obj_sql_func::COUNT, "count" , "*"))
			)
		);
		$presentations_count = $presentations_count->arr();
		$presentations_count = reset($presentations_count);
		$presentations_count = $presentations_count["count"];

		if ($limit_results)
		{
			$per_page = $this_o->prop("tables_rows_per_page");
			$page = isset($arr["request"]["ft_page"]) ? (int) $arr["request"]["ft_page"] : 0;
			$start = $page*$per_page;
			$filter[] = new obj_predicate_limit($per_page, $start);
		}

		// sorting
		// default sort order by planned start
		$sort_by = "CL_CRM_PRESENTATION.start1";
		$sort_dir = obj_predicate_sort::ASC;
		$sortable_fields = array( // table field => array( default sort order, database field name)
			"planned_start" => array(obj_predicate_sort::ASC, "CL_CRM_PRESENTATION.start1"),
			"real_start" => array(obj_predicate_sort::ASC, "CL_CRM_PRESENTATION.real_start"),
			"result" => array(obj_predicate_sort::ASC, "CL_CRM_PRESENTATION.result"),
			"tm_salesman" => array(obj_predicate_sort::ASC, "CL_CRM_PRESENTATION.createdby"),
			"salesman" => array(obj_predicate_sort::ASC, "CL_CRM_PRESENTATION.customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).salesman(CL_CRM_PERSON).name")
		);
		if (isset($arr["request"]["sortby"]) and isset($sortable_fields[$arr["request"]["sortby"]]))
		{
			$sort_by = $sortable_fields[$arr["request"]["sortby"]][1];
			$sort_dir = isset($arr["request"]["sortby"]) ? ($arr["request"]["sortby"] === "asc" ? obj_predicate_sort::ASC : obj_predicate_sort::DESC) : $sortable_fields[$arr["request"]["sortby"]][0];
		}
		$filter[] = new obj_predicate_sort(array($sort_by => $sort_dir));

		// ...
		$presentations = new crm_presentation_list($filter);
		return array($presentations, $presentations_count);
	}

	public static function _get_presentations_list(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];
		$table->set_hover_hilight(true);
		$owner = $arr["obj_inst"]->prop("owner");
		list($presentations, $presentations_count) = self::get_presentations_list($arr);
		self::define_presentations_list_tbl_header($arr, $presentations_count);
		$not_available_str = "";
		$cl_user = new user();
		$role = automatweb::$request->get_application()->get_current_user_role();

		if ($presentations->count())
		{
			$presentation = $presentations->begin();
			do
			{
				$customer_relation = new object($presentation->prop("customer_relation"));
				if (is_oid($customer_relation->id()))
				{
					$customer = $customer_relation->get_first_obj_by_reltype("RELTYPE_BUYER");

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


					// tm salespersons only see their own presentations, and aren't allowed to edit them
					$customer_name = (crm_sales_obj::ROLE_TELEMARKETING_SALESMAN === $role) ?
						html::obj_view_url($presentation, $customer->name()) :
						html::obj_change_url($presentation, $customer->name());

					// telemarketing salesman who set up the presentation
					$tm_salesman = $cl_user->get_person_for_uid($presentation->createdby());
					$tm_salesman = (is_object($tm_salesman) and $tm_salesman->is_saved()) ? $tm_salesman->name() : $not_available_str;

					// time
					$real_start_timestamp = $presentation->prop("real_start");
					$real_start = $real_start_timestamp > 1 ? aw_locale::get_lc_date($real_start_timestamp, aw_locale::DATETIME_SHORT_FULLYEAR) : $not_available_str;

					$planned_start_timestamp = $presentation->prop("start1");
					$planned_start = $planned_start_timestamp > 1 ? aw_locale::get_lc_date($planned_start_timestamp, aw_locale::DATETIME_SHORT_FULLYEAR) : $not_available_str;

					// result
					$result_int = $presentation->prop("result");
					$result = crm_presentation_obj::result_names($result_int);
					$result = $result_int ? reset($result) : $not_available_str;

					// address
					$address = new object($presentation->prop("presentation_location"));
					$address = is_object($address) ? $address->name() : $not_available_str;

					// phones
					$phones = new object_list($customer->connections_from(array("type" => "RELTYPE_PHONE")));
					$phones_str = array();
					if($phones->count())
					{
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
					}
					$phones_str = count($phones_str) ? implode(", ", $phones_str) : $not_available_str;

					// define table row
					$table->define_data(array(
						"name" => $customer_name,
						"phone" => $phones_str,
						"address" => $address,
						"planned_start" => $planned_start,
						"real_start" => $real_start,
						"result" => $result,
						"result_int" => $result_int,
						"planned_start_timestamp" => $planned_start_timestamp,
						"real_start_timestamp" => $real_start_timestamp,
						"oid" => $presentation->id(),
						"tm_salesman" => $tm_salesman,
						"salesman" => $salesman
					));
				}
			}
			while ($presentation = $presentations->next());

			if (crm_sales::PRESENTATIONS_SEARCH === crm_sales::$presentations_list_view)
			{
				$table->set_caption(sprintf(crm_sales::$presentations_list_views[crm_sales::PRESENTATIONS_SEARCH]["caption"], $presentations_count));
			}
			else
			{
				$table->set_caption(crm_sales::$presentations_list_views[crm_sales::$presentations_list_view]["caption"] . " ({$presentations_count})");
			}
		}
		return PROP_OK;
	}

	protected static function define_presentations_list_tbl_header(&$arr, $presentations_count)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];

		if ($this_o->has_privilege("presentation_edit"))
		{
			$table->define_chooser(array(
				"name" => "sel",
				"field" => "oid"
			));
		}

		$table->define_field(array(
			"name" => "name",
			"caption" => t("Kliendi nimi")
		));
		$table->define_field(array(
			"name" => "phone",
			"caption" => t("Telefon")
		));
		$table->define_field(array(
			"name" => "address",
			"caption" => t("Toimumiskoht")
		));
		$table->define_field(array(
			"name" => "planned_start",
			"sortable" => 1,
			"sorting_field" => "planned_start_timestamp",
			"caption" => t("Kokkulepitud aeg")
		));
		$table->define_field(array(
			"name" => "real_start",
			"sortable" => 1,
			"sorting_field" => "real_start_timestamp",
			"caption" => t("Algas")
		));
		$table->define_field(array(
			"name" => "result",
			"sortable" => 1,
			"sorting_field" => "result_int",
			"caption" => t("Tulemus")
		));
		$table->define_field(array(
			"name" => "tm_salesman",
			"sortable" => 1,
			"caption" => t("Kokkuleppija")
		));
		$table->define_field(array(
			"name" => "salesman",
			"sortable" => 1,
			"caption" => t("M&uuml;&uuml;giesindaja")
		));
		$table->set_numeric_field(array("planned_start_timestamp", "real_start_timestamp", "result_int"));

		$views_w_salesman_sorting = array(
			crm_sales::PRESENTATIONS_YESTERDAY,
			crm_sales::PRESENTATIONS_TODAY,
			crm_sales::PRESENTATIONS_TOMORROW,
			crm_sales::PRESENTATIONS_ADDED_TODAY
		);

		if (in_array(crm_sales::$presentations_list_view, $views_w_salesman_sorting))
		{
			$table->set_default_sortby("salesman");
			$table->set_default_sorder("asc");
		}
		else
		{
			$table->set_default_sortby("planned_start");
			$table->set_default_sorder("desc");
		}

		$table->define_pageselector (array (
			"type" => "lbtxt",
			"position" => "both",
			"d_row_cnt" => $presentations_count,
			"records_per_page" => $this_o->prop("tables_rows_per_page")
		));

	}
}

?>
