<?php

class crm_sales_data_entry_statistics_view
{
	public static function _get_des_by(&$arr)
	{
		$r = PROP_OK;
		crm_sales::set_employees_options($arr);
		unset($arr["prop"]["options"][""]); // clear empty choice
		if (isset($arr["request"]["des_by"]))
		{
			$arr["prop"]["value"] = $arr["request"]["des_by"]; // get value from last search request
		}
		return $r;
	}

	public static function _get_des_from(&$arr)
	{
		$r = PROP_OK;
		if (isset($arr["request"]["des_from"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["des_from"]);// get value from last search request
		}
		return $r;
	}

	public static function _get_des_to(&$arr)
	{
		$r = PROP_OK;
		if (isset($arr["request"]["des_to"]))
		{
			$arr["prop"]["value"] = datepicker::get_timestamp($arr["request"]["des_to"]);// get value from last search request
		}
		return $r;
	}

	public static function _get_data_entry_stats_list(&$arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$table->set_hover_hilight(true);
		self::define_stats_tbl($arr);
		$from_str = t("algus");
		$to_str = t("k&auml;esolev hetk");

		$this_o = $arr["obj_inst"];
		$owner = $arr["obj_inst"]->prop("owner");
		$filter = array(
			"class_id" => CL_CRM_COMPANY_CUSTOMER_DATA,
			"seller" => $owner->id()
		);

		if (!empty($arr["request"]["des_from"]["date"]) and !empty($arr["request"]["des_to"]["date"]))
		{
			$from = datepicker::get_timestamp($arr["request"]["des_from"]);
			$from_str = aw_locale::get_lc_date($from, aw_locale::DATETIME_SHORT_FULLYEAR);
			$to = datepicker::get_timestamp($arr["request"]["des_to"]);
			$to_str = aw_locale::get_lc_date($to, aw_locale::DATETIME_SHORT_FULLYEAR);
			$filter["created"] = new obj_predicate_compare(obj_predicate_compare::BETWEEN, $from, $to);
		}
		elseif (!empty($arr["request"]["des_from"]["date"]))
		{
			$from = datepicker::get_timestamp($arr["request"]["des_from"]);
			$from_str = aw_locale::get_lc_date($from, aw_locale::DATETIME_SHORT_FULLYEAR);
			$filter["created"] = new obj_predicate_compare(obj_predicate_compare::GREATER, $from);
		}
		elseif (!empty($arr["request"]["des_to"]["date"]))
		{
			$to = datepicker::get_timestamp($arr["request"]["des_to"]);
			$to_str = aw_locale::get_lc_date($to, aw_locale::DATETIME_SHORT_FULLYEAR);
			$filter["created"] = new obj_predicate_compare(obj_predicate_compare::LESS_OR_EQUAL, $from, $to);
		}

		if (!empty($arr["request"]["des_by"]))
		{
			foreach ($arr["request"]["des_by"] as $employee_oid)
			{
				$employee = obj($employee_oid, array(), CL_CRM_PERSON);
				$filter["createdby"] = $employee->get_uid();
				self::define_stats_table_row($arr, $filter, $employee);
			}
		}
		else
		{
			self::define_stats_table_row($arr, $filter);
		}

		$table->set_caption(t("Sisestused ajavahemikus {$from_str} kuni {$to_str}"));
	}

	private static function define_stats_table_row(&$arr, &$filter, object $employee = null)
	{
		$count = new object_data_list(
			$filter,
			array(
				CL_CRM_COMPANY_CUSTOMER_DATA =>  array(new obj_sql_func(obj_sql_func::COUNT, "count" , "*"))
			)
		);
		$count = $count->arr();
		$count = $count[0]["count"];
		if ($count)
		{
			$table = $arr["prop"]["vcl_inst"];
			$table->define_data(array(
				"user" => $employee ? $employee->name() : t("K&otilde;ik kasutajad"),
				"count" => $count
			));
		}
	}

	private static function define_stats_tbl(&$arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$table->define_field(array(
			"name" => "user",
			"sortable" => false,
			"caption" => "Sisestaja"
		));
		$table->define_field(array(
			"name" => "count",
			"sortable" => false,
			"caption" => "Sisestuste arv"
		));
	}
}

?>
