<?php

class crm_sales_statistics_offers_view extends crm_sales_offers_view
{
	public static function _get_statistics_offers_tree_timespan(&$arr)
	{
		return parent::_get_offers_tree_timespan($arr);
	}

	public static function _get_statistics_offers_tree_state(&$arr)
	{
		return parent::_get_offers_tree_state($arr);
	}

	public static function _get_statistics_offers_tree_customer_category(&$arr)
	{
		return parent::_get_offers_tree_customer_category($arr);
	}

	protected static function get_offers_list(&$arr)
	{
		list($offers, $offers_count) = crm_sales_offers_view::get_offers_list($arr);

		$offers->load_applied_price_components();

		return array($offers, $offers_count);
	}

	protected static function define_offers_list_tbl_header(&$arr, $offers_count)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];

		$table->define_field(array(
			"name" => "id",
			"caption" => t("Pakkumise ID"),
			"sortable" => true,
			"numeric" => true,
		));
		$table->define_field(array(
			"name" => "state",
			"caption" => t("Staatus"),
			"sortable" => true,
		));
		$table->define_field(array(
			"name" => "customer_name",
			"caption" => t("Kliendi nimi"),
			"sortable" => true,
		));
		$table->define_field(array(
			"name" => "salesman_name",
			"caption" => t("M&uuml;&uuml;giesindaja nimi"),
			"sortable" => true,
		));
		$table->define_field(array(
			"name" => "modified",
			"sortable" => true,
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
			"caption" => t("Juurhind"),
			"sortable" => true,
			"numeric" => true,
		));
		
		$categories_to_be_shown = $arr["obj_inst"]->get_price_components_and_categories_shown_in_statistics();
		$table->define_field(array(
			"parent" => "sums",
			"name" => "sum_price_components",
			"caption" => t("Hinnakomponendid"),
			"sortable" => count($categories_to_be_shown) === 0,
			"numeric" => true,
		));

		if(count($categories_to_be_shown) > 0)
		{
			$price_component_and_category_names = $arr["obj_inst"]->get_price_component_category_list()->names() + $arr["obj_inst"]->get_price_component_list()->names();
			foreach($categories_to_be_shown as $category_id)
			{
				$table->define_field(array(
					"parent" => "sum_price_components",
					"name" => "sum_price_components_{$category_id}",
					"caption" => $price_component_and_category_names[$category_id],
					"sortable" => true,
					"numeric" => true,
				));
			}
			$table->define_field(array(
				"parent" => "sum_price_components",
				"name" => "sum_price_components_rest",
				"caption" => t("&Uuml;lej&auml;&auml;nud"),
				"sortable" => true,
				"numeric" => true,
			));
		}

		$table->define_field(array(
			"parent" => "sums",
			"name" => "sum",
			"caption" => t("Kokku"),
			"sortable" => true,
			"numeric" => true,
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
		$totals = array();

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

				$categories_to_be_shown = $this_o->get_price_components_and_categories_shown_in_statistics();
				$sums = self::calculate_statistics_offers_list_sums($offer, $categories_to_be_shown);

				foreach($sums as $sum_key => $sum_value)
				{
					if(!isset($totals[$sum_key]))
					{
						$totals[$sum_key] = 0;
					}

					$totals[$sum_key] += aw_math_calc::string2float($sum_value);
				}

				// define table row
				$table->define_data($sums + array(
					"customer_name" => $customer_name,
					"salesman_name" => $salesman,
					"state" => $state,
					"oid" => $oid,
					"id" => $offer_id,
					"modified" => $modified,
					"modified_timestamp" => $offer->prop("modified"),
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

			$table->sort_by();
			$table->set_sortable(false);
			foreach($totals as $total_key => $total_value)
			{
				$totals[$total_key] = html::bold(number_format($total_value, 2));
			}
			$table->define_data($totals + array(
				"state" => html::bold(t("Summa")),
			));
		}
		return PROP_OK;
	}

	protected static function calculate_statistics_offers_list_sums($offer, $categories_to_be_shown)
	{
		$sums_by_type = array(
			crm_sales_price_component_obj::TYPE_NET_VALUE => 0,
			crm_sales_price_component_obj::TYPE_UNIT => 0,
			crm_sales_price_component_obj::TYPE_ROW => 0,
			crm_sales_price_component_obj::TYPE_TOTAL => 0,
		);
		$sums_by_category = array("rest" => 0);
		foreach($categories_to_be_shown as $category)
		{
			$sums_by_category[$category] = 0;
		}

		foreach($offer->get_applied_price_components() as $price_component)
		{
			$sums_by_type[$price_component->type] += $price_component->price();
			if (in_array($price_component->id(), $categories_to_be_shown))
			{
				$sums_by_category[$price_component->id()] += $price_component->price();
			}
			elseif (in_array($price_component->category, $categories_to_be_shown))
			{
				$sums_by_category[$price_component->category] += $price_component->price();
			}
			elseif (crm_sales_price_component_obj::TYPE_NET_VALUE != $price_component->type)
			{
				$sums_by_category["rest"] += $price_component->price();
			}
		}

		foreach($offer->get_rows() as $row)
		{
			foreach($row->get_applied_price_components() as $price_component)
			{
				$sums_by_type[$price_component->type] += $price_component->price();
				if (in_array($price_component->id(), $categories_to_be_shown))
				{
					$sums_by_category[$price_component->id()] += $price_component->price();
				}
				elseif (in_array($price_component->category, $categories_to_be_shown))
				{
					$sums_by_category[$price_component->category] += $price_component->price();
				}
				elseif (crm_sales_price_component_obj::TYPE_NET_VALUE != $price_component->type)
				{
					$sums_by_category["rest"] += $price_component->price();
				}
			}
		}

		$sums = array(
			"sum_net_value" => $sums_by_type[crm_sales_price_component_obj::TYPE_NET_VALUE],
			"sum_price_components" => $sums_by_type[crm_sales_price_component_obj::TYPE_UNIT] + $sums_by_type[crm_sales_price_component_obj::TYPE_ROW] + $sums_by_type[crm_sales_price_component_obj::TYPE_TOTAL],
			"sum" => $offer->sum,
		);

		foreach($sums_by_category as $category_id => $category_sum)
		{
			$sums["sum_price_components_{$category_id}"] = $category_sum;
		}

		foreach($sums as $i => $sum)
		{
			$sums[$i] = number_format($sum, 2);
		}

		return $sums;
	}
}

?>
