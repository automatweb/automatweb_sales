<?php

class crm_sales_offers_view
{
	public static function _get_offers_tree(&$arr)
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

	public static function _get_offers_toolbar(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$r = PROP_IGNORE;

		if ($this_o->has_privilege("offer_edit"))
		{
			$toolbar = $arr["prop"]["vcl_inst"];
			if (is_oid($this_o->prop("offers_folder")))
			{
				$toolbar->add_new_button(array(CL_CRM_OFFER), $this_o->prop("offers_folder"));
			}
			$toolbar->add_button(array(
				"name" => "save",
				"img" => "save.gif",
				"action" => "submit",
				"tooltip" => t("Salvesta")
			));
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
			$to = $from + 86400;
			$filter["start1"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			$arr["request"]["sortby"] = "modified";
		}
		elseif (crm_sales::OFFERS_YESTERDAY === crm_sales::$offers_list_view)
		{
			$limit_results = false;
			$from = mktime(0, 0, 0, date("n"), (date("j") - 1), date("Y"));
			$to = $from + 86400;
			$filter["start1"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
			$arr["request"]["sortby"] = "modified";
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
		// default sort order by planned start
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
		$not_available_str = "";
		$role = automatweb::$request->get_application()->get_current_user_role();

		if ($offers->count())
		{
			$offer = $offers->begin();
			do
			{
				$customer_relation = new object($offer->prop("customer_relation"));
				if (is_oid($customer_relation->id()))
				{
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

					$customer = $customer_relation->get_first_obj_by_reltype("RELTYPE_BUYER");
					$customer_name = html::obj_change_url($customer_relation, crm_sales::parse_customer_name($customer));

					$oid = $offer->id();

					$offer_id = html::obj_change_url($offer, $oid);

					$modified = aw_locale::get_lc_date($offer->prop("modified"), aw_locale::DATETIME_SHORT_FULLYEAR);

					$sum = $offer->prop("sum");

					// define table row
					$table->define_data(array(
						"customer_name" => $customer_name,
						"salesman_name" => $salesman,
						"sum" => $sum,
						"oid" => $oid,
						"id" => $offer_id,
						"modified" => $modified,
					));
				}
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
			"name" => "customer_name",
			"caption" => t("Kliendi nimi")
		));
		$table->define_field(array(
			"name" => "salesman_name",
			"caption" => t("M&uuml;&uuml;giesindaja nimi")
		));
		$table->define_field(array(
			"name" => "id",
			"caption" => t("Pakkumise ID")
		));
		$table->define_field(array(
			"name" => "sum",
			"caption" => t("Summa")
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

?>
