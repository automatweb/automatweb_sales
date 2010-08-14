<?php

class crm_sales_statistics_offers_view
{
	public static function _get_statistics_offers_tree_timespan(&$arr)
	{
		return crm_sales_offers_view::_get_offers_tree_timespan($arr);
	}

	public static function _get_statistics_offers_tree_state(&$arr)
	{
		return crm_sales_offers_view::_get_offers_tree_state($arr);
	}

	public static function _get_statistics_offers_tree_customer_category(&$arr)
	{
		return crm_sales_offers_view::_get_offers_tree_customer_category($arr);
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
			$to = $from + 86400;
			$filter["modified"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			$arr["request"]["sortby"] = "modified";
		}
		elseif (crm_sales::OFFERS_YESTERDAY === crm_sales::$offers_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), (date("j") - 1), date("Y"));
			$to = $from + 86400;
			$filter["modified"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
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
		$offers->load_applied_price_components();
		return array($offers, $offers_count);
	}

	public static function _get_statistics_offers_list(&$arr)
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

		if ($offers->count())
		{
			$offer = $offers->begin();
			do
			{
				$customer_relation = new object($offer->prop("customer_relation"));
				if (is_oid($customer_relation->id()))
				{
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

				$state = $offer_state_names[$offer->state];

				$sums = self::calculate_statistics_offers_list_sums($offer);

				// define table row
				$table->define_data($sums + array(
					"customer_name" => $customer_name,
					"salesman_name" => $salesman,
					"state" => $state,
					"oid" => $oid,
					"id" => $offer_id,
					"modified" => $modified,
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

	protected static function calculate_statistics_offers_list_sums($offer)
	{
		$sums_by_type = array(
			crm_sales_price_component_obj::TYPE_NET_VALUE => 0,
			crm_sales_price_component_obj::TYPE_UNIT => 0,
			crm_sales_price_component_obj::TYPE_ROW => 0,
			crm_sales_price_component_obj::TYPE_TOTAL => 0,
		);

		foreach($offer->get_applied_price_components() as $price_component)
		{
			$sums_by_type[$price_component->type] += $price_component->price();
		}

		foreach($offer->get_rows() as $row)
		{
			foreach($row->get_applied_price_components() as $price_component)
			{
				$sums_by_type[$price_component->type] += $price_component->price();
			}
		}

		$sums = array(
			"sum_net_value" => $sums_by_type[crm_sales_price_component_obj::TYPE_NET_VALUE],
			"sum_price_components" => $sums_by_type[crm_sales_price_component_obj::TYPE_UNIT] + $sums_by_type[crm_sales_price_component_obj::TYPE_ROW] + $sums_by_type[crm_sales_price_component_obj::TYPE_TOTAL],
			"sum" => $offer->sum,
		);

		foreach($sums as $i => $sum)
		{
			$sums[$i] = number_format($sum, 2);
		}

		return $sums;
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
			"caption" => t("Pakkumise ID")
		));
		$table->define_field(array(
			"name" => "state",
			"caption" => t("Staatus")
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
			"name" => "modified",
			"sortable" => 1,
			"sorting_field" => "modified_timestamp",
			"caption" => t("Viimati muudetud")
		));
		$table->set_numeric_field(array("modified_timestamp"));

		$table->define_field(array(
			"name" => "sums",
			"caption" => t("Summa")
		));
		$table->define_field(array(
			"parent" => "sums",
			"name" => "sum_net_value",
			"caption" => t("Juurhind")
		));
		$table->define_field(array(
			"parent" => "sums",
			"name" => "sum_price_components",
			"caption" => t("Hinnakomponendid")
		));
		$table->define_field(array(
			"parent" => "sums",
			"name" => "sum",
			"caption" => t("Kokku")
		));

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

?>
