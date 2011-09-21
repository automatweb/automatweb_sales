<?php

class crm_sales_offers_view
{
	public static function _get_offers_tree_timespan(&$arr)
	{
		$tree = $arr["prop"]["vcl_inst"];
		$views_to_clear_search_for = array(
			crm_sales::OFFERS_YESTERDAY,
			crm_sales::OFFERS_TODAY,
			crm_sales::OFFERS_DEFAULT
		);


		foreach (crm_sales::$offers_list_views as $key => $data)
		{
			if ($data["in_tree"])
			{
				$url = automatweb::$request->get_uri();
				$url->set_arg("crmListId", $key);

				if (in_array($key, $views_to_clear_search_for))
				{
					$url->unset_arg(array(
						"ft_page",
						"os_submit",
						"os_name"
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

		$tree->set_selected_item (crm_sales::$offers_list_view);
		return PROP_OK;
	}

	public static function _get_offers_tree_state(&$arr)
	{
		$tree = $arr["prop"]["vcl_inst"];
		$states_to_clear_search_for = array(
			crm_offer_obj::STATE_NEW,
			crm_offer_obj::STATE_SENT,
			crm_offer_obj::STATE_CONFIRMED,
			crm_offer_obj::STATE_CANCELLED,
			crm_offer_obj::STATE_REJECTED
		);

		$url = automatweb::$request->get_uri();
		$url->unset_arg(array(
			"crmListState",
			"ft_page",
			"os_submit",
			"os_name"
		));

		$tree->add_item (0, array (
			"name" => t("K&otilde;ik pakkumused"),
			"id" => "all",
			"parent" => 0,
			"url" => $url->get()
		));

		foreach (crm_offer_obj::state_names() as $state => $caption)
		{
			$url = automatweb::$request->get_uri();
			$url->set_arg("crmListState", $state);

			if (in_array($state, $states_to_clear_search_for))
			{
				$url->unset_arg(array(
					"ft_page",
					"os_submit",
					"os_name"
				));
			}

			$tree->add_item (0, array (
				"name" => $caption,
				"id" => sprintf("s_%u", $state),
				"parent" => 0,
				"url" => $url->get()
			));
		}

		if(automatweb::$request->arg_isset("crmListState"))
		{
			$tree->set_selected_item(sprintf("s_%u", automatweb::$request->arg("crmListState")));
		}
		else
		{
			$tree->set_selected_item("all");
		}
		return PROP_OK;
	}

	public static function _get_offers_tree_customer_category(&$arr)
	{
		$tree = $arr["prop"]["vcl_inst"];

		$url = automatweb::$request->get_uri();
		$url->unset_arg(array(
			"crmListCustCat",
			"ft_page",
			"os_submit",
			"os_name"
		));

		$tree->add_item (0, array (
			"name" => t("K&otilde;ik pakkumused"),
			"id" => "all",
			"parent" => 0,
			"url" => $url->get()
		));

		$categories = $arr['obj_inst']->prop("owner")->get_customer_categories();
		foreach ($categories->arr() as $category)
		{
			$parent = $category->prop("parent_category") ? (int) $category->prop("parent_category") : 0;
			$url->set_arg("crmListCustCat", $category->id());
			$tree->add_item ($parent, array (
				"name" => $category->name(),
				"id" => $category->id(),
				"parent" => $parent,
				"url" => $url->get()
			));
		}

		if(automatweb::$request->arg_isset("crmListCustCat"))
		{
			$tree->set_selected_item(automatweb::$request->arg("crmListCustCat"));
		}
		else
		{
			$tree->set_selected_item("all");
		}
		return PROP_OK;
	}

	public static function _get_offers_toolbar(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$core = new core();
		$r = PROP_IGNORE;

		if ($this_o->has_privilege("offer_edit"))
		{
			$toolbar = $arr["prop"]["vcl_inst"];
			if (is_oid($this_o->prop("offers_folder")))
			{
				$toolbar->add_menu_button(array(
					"name" => "new",
					"tooltip" => t("Lisa uus"),
				));

				$toolbar->add_menu_item(array(
					"parent" => "new",
					"text" => t("T&uuml;hi pakkumus"),
					"link" => html::get_new_url(CL_CRM_OFFER, $this_o->prop("offers_folder"), array("return_url" => get_ru()))
				));
				$tpls = $this_o->get_offer_templates();
				if($tpls->count() > 0)
				{
					$toolbar->add_sub_menu(array(
						"parent" => "new",
						"name" => "new_from_tpl",
						"text" => t("Pakkumus &scaron;ablooni p&otilde;hjal"),
					));

					foreach($tpls->names() as $tpl_oid => $tpl_name)
					{
						$toolbar->add_menu_item(array(
							"parent" => "new_from_tpl",
							"text" => $tpl_name,
							"link" => $core->mk_my_orb(
								"new_from_template",
								array("tpl" => $tpl_oid, "parent" => $this_o->prop("offers_folder"), "return_url" => get_ru()),
								CL_CRM_OFFER
							),
						));
					}
				}
			}
			$toolbar->add_delete_button();
			$r = PROP_OK;
		}

		return $r;
	}

	protected static function get_offers_list(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$filter = array();
		$limit_results = true;

		if (crm_sales::OFFERS_TODAY === crm_sales::$offers_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
			$to = $from + 86400 - 1;
			$filter["modified"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, $from, $to);
			$arr["request"]["sortby"] = "modified";
		}
		elseif (crm_sales::OFFERS_YESTERDAY === crm_sales::$offers_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), (date("j") - 1), date("Y"));
			$to = $from + 86400;
			$filter["modified"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, $from, $to);
			$arr["request"]["sortby"] = "modified";
		}
		elseif (crm_sales::OFFERS_THIS_WEEK === crm_sales::$offers_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), (date("j") - (date("w") == 0 ? 6 : (date("w") - 1))), date("Y"));
			$to = $from + 7 * 86400 - 1;
			$filter["modified"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, $from, $to);
			$arr["request"]["sortby"] = "modified";
		}
		elseif (crm_sales::OFFERS_LAST_WEEK === crm_sales::$offers_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), (date("j") - (date("w") == 0 ? 6 : (date("w") - 1)) - 7), date("Y"));
			$to = $from + 7 * 24 * 3600 - 1;
			$filter["modified"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, $from, $to);
			$arr["request"]["sortby"] = "modified";
		}

		if (automatweb::$request->arg_isset("crmListState") and array_key_exists(automatweb::$request->arg("crmListState"), crm_offer_obj::state_names()))
		{
			$filter["state"] = automatweb::$request->arg("crmListState");
		}

		if (automatweb::$request->arg_isset("crmListCustCat"))
		{
			$filter["customer_relation(CL_CRM_COMPANY_CUSTOMER_DATA).RELTYPE_CATEGORY"] = automatweb::$request->arg("crmListCustCat");
		}

		$offers_count = new object_data_list(
			array_merge($filter, crm_offer_list::get_default_filter()),
			array(
				CL_CRM_OFFER => array(new obj_sql_func(obj_sql_func::COUNT, "count" , "*"))
			)
		);
		$offers_count = $offers_count->arr();
		$offers_count = reset($offers_count);
		$offers_count = $offers_count["count"];

		if ($limit_results)
		{
			$per_page = $this_o->prop("tables_rows_per_page");
			$page = isset($arr["request"]["ft_page"]) ? (int) $arr["request"]["ft_page"] : 0;
			$start = $page * $per_page;
			$filter[] = new obj_predicate_limit($per_page, $start);
		}

		// sorting
		$sort_by = "CL_CRM_OFFER.modified";
		$sort_dir = obj_predicate_sort::DESC;
		$sortable_fields = array( // table field => array( default sort order, database field name)
			"customer_name" => array(obj_predicate_sort::ASC, "CL_CRM_OFFER.customer.name"),
			"salesman_name" => array(obj_predicate_sort::ASC, "CL_CRM_OFFER.salesman.name"),
			"sum" => array(obj_predicate_sort::ASC, "CL_CRM_OFFER.sum"),
		);
		if (isset($arr["request"]["sortby"]) and isset($sortable_fields[$arr["request"]["sortby"]]))
		{
			$sort_by = $sortable_fields[$arr["request"]["sortby"]][1];
			$sort_dir = isset($arr["request"]["sortby"]) ? ($arr["request"]["sortby"] === "asc" ? obj_predicate_sort::ASC : obj_predicate_sort::DESC) : $sortable_fields[$arr["request"]["sortby"]][0];
		}
		$filter[] = new obj_predicate_sort(array($sort_by => $sort_dir));

		// ...
		$offers = new crm_offer_list($filter);
		return array($offers, $offers_count);
	}

	public static function _get_offers_list(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];
		$table->set_hover_hilight(true);
		$owner = $arr["obj_inst"]->prop("owner");
		list($offers, $offers_count) = self::get_offers_list($arr);
		self::define_offers_list_tbl_header($arr, $offers_count);
		$not_available_str = html::italic(t("M&auml;&auml;ramata"));
		$role = automatweb::$request->get_application()->get_current_user_role();
		$offer_state_names = crm_offer_obj::state_names();
		$offer_result_names = crm_offer_obj::result_names();

		if ($offers->count())
		{
			$offer = $offers->begin();
			do
			{
				if (object_loader::can("", $offer->prop("customer_relation")))
				{
					$customer_relation = new object($offer->prop("customer_relation"));
					$customer = $customer_relation->get_first_obj_by_reltype("RELTYPE_BUYER");
					$customer_name = html::obj_change_url($customer_relation, crm_sales::parse_customer_name($customer));
				}
				else
				{
					$customer_name = $not_available_str;
				}

				$salesman = $offer->prop("salesman");
				if (is_oid($salesman))
				{
					$salesman = new object($salesman);
					$salesman = $salesman->name();
				}
				else
				{
					$salesman = $not_available_str;
				}

				$oid = $offer->id();

				$offer_id = html::obj_change_url($offer, $oid);

				$modified = aw_locale::get_lc_date($offer->prop("modified"), aw_locale::DATETIME_SHORT_FULLYEAR);

				$sum = $offer->sum_with_currency();

				$state = $offer_state_names[$offer->state];

				if (isset($offer_result_names[$offer->result]))
				{
					try
					{
						$result_object = $offer->get_result_object();
						$result = html::get_change_url($result_object, array("return_url" => get_ru()), $offer_result_names[$offer->result]);
					}
					catch (Exception $e)
					{
						$result = $offer_result_names[$offer->result];
					}
				}
				else
				{
					$result = $not_available_str;
				}

				$template = new object($offer->prop("template"));

				// define table row
				$table->define_data(array(
					"customer_name" => $customer_name,
					"salesman_name" => $salesman,
					"sum" => $sum,
					"state" => $state,
					"result" => $result,
					"oid" => $oid,
					"id" => $offer_id,
					"modified" => $modified,
					"modified_timestamp" => $offer->prop("modified"),
					"template" => $offer->prop("template") ? html::obj_change_url($template) : "",
				));
			}
			while ($offer = $offers->next());

			if (crm_sales::OFFERS_SEARCH === crm_sales::$offers_list_view)
			{
				$table->set_caption(sprintf(crm_sales::$offers_list_views[crm_sales::OFFERS_SEARCH]["caption"], $offers_count));
			}
			else
			{
				$table->set_caption(crm_sales::$offers_list_views[crm_sales::$offers_list_view]["caption"] . " ({$offers_count})");
			}
		}
		return PROP_OK;
	}

	protected static function define_offers_list_tbl_header(&$arr, $offers_count)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];

		if ($this_o->has_privilege("offer_edit"))
		{
			$table->define_chooser(array(
				"name" => "sel",
				"field" => "oid"
			));
		}

		$table->define_field(array(
			"name" => "id",
			"caption" => t("Pakkumuse ID")
		));
		$table->define_field(array(
			"name" => "customer_name",
			"caption" => t("Kliendi nimi")
		));
		$table->define_field(array(
			"name" => "salesman_name",
			"caption" => t("M&uuml;&uuml;giesindaja nimi")
		));
		$table->define_field(array(
			"name" => "sum",
			"caption" => t("Summa")
		));
		$table->define_field(array(
			"name" => "state",
			"caption" => t("Staatus")
		));
		$table->define_field(array(
			"name" => "result",
			"caption" => t("Tulemus")
		));
		$table->define_field(array(
			"name" => "template",
			"caption" => t("&Scaron;abloon")
		));
		$table->define_field(array(
			"name" => "modified",
			"sortable" => 1,
			"sorting_field" => "modified_timestamp",
			"caption" => t("Viimati muudetud")
		));
		$table->set_numeric_field(array("modified_timestamp"));

		$table->set_default_sortby("modified");
		$table->set_default_sorder("desc");

		$table->define_pageselector (array (
			"type" => "lbtxt",
			"position" => "both",
			"d_row_cnt" => $offers_count,
			"records_per_page" => $this_o->prop("tables_rows_per_page")
		));

	}
}
