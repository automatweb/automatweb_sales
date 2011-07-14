<?php

class crm_sales_telemarketing_statistics_view
{
	const COLOUR_WEEKEND = "#FFDDDD";
	const COLOUR_SALE = "red";
	const COLOUR_REPLACEMENT = "blue";

	private static $view_type = "month";
	private static $view_types = array("month");
	private static $view_period_mon; // default is null, interpreted as current month
	private static $view_period_year; // default is null, interpreted as current year

	private static $column_sum_data = array();

	public static function _get_tmstat_display_table(&$arr)
	{
		// initialize settings and helpers
		$r = class_base::PROP_OK;
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];
		$table->set_layout("compact");
		$owner = $this_o->prop("owner");
		$current_person = get_current_person();
		$current_time = time();
		$core = new core();
		$cache = new cache();
		$crm_sales_application = automatweb::$request->get_application();

		self::set_view_parameters_and_defaults($arr);
		self::define_tmstat_display_table_layout($arr);


		// get telemarketing employees
		$employees = new object_list();
		try
		{
			$profession = obj($crm_sales_application->prop("role_profession_telemarketing_salesman"), array(), crm_profession_obj::CLID);
			if ($profession->is_saved())
			{
				$employees = $owner->get_employees("active", $profession);
			}

			$profession = obj($crm_sales_application->prop("role_profession_telemarketing_manager"), array(), crm_profession_obj::CLID);
			if ($profession->is_saved())
			{
				$employees->add($owner->get_employees("active", $profession));
			}
		}
		catch (awex_obj_na $e)
		{
			class_base::show_error_text(t("Rollile valitud ametit ei eksisteeri."));
		}

		// a summary row block for each employee
		if($employees->count())
		{
			$tm_employee = $employees->begin();

			do
			{
				self::build_employee_month_stats($arr, $tm_employee);
			}
			while ($tm_employee = $employees->next());
		}
		else
		{
			class_base::show_msg_text(t("Telemarketingi rollides t&ouml;&ouml;tajaid pole."));
		}

		// a totals row block
		self::build_total_stats($arr);

		$table->set_caption(date("m/Y", mktime(1, 1, 1, self::$view_period_mon, 1, self::$view_period_year)));

		return $r;
	}

	private static function build_employee_month_stats($arr, object $employee)
	{
		$table = $arr["prop"]["vcl_inst"];
		$this_o = $arr["obj_inst"];
		$crm_sales_application = automatweb::$request->get_application();
		$days_in_selected_month = (int) date("t", mktime(1, 1, 1, self::$view_period_mon, 1, self::$view_period_year));
		$weeks_last_day = 7; //!!! localest v6tta
		$cl_crm_person_inst = new crm_person();
		$employee_user = $cl_crm_person_inst->has_user($employee);

		if (!is_object($employee_user))
		{
			return;
		}

		$employee_user_name = $employee_user->prop("uid");
		$employee_name = $employee->name();

		// common arguments
		$common_list_filter = array(
			"class_id" => CL_CRM_PRESENTATION,
			"parent" => $crm_sales_application->prop("presentations_folder"),
			"createdby" => $employee_user_name
		);

		$row_data_tpl = array(
			"employee" => $employee_name,
			"week_data_bgcolour" => "white"
		);

		// presentations arranged
		$row_data = $row_data_tpl + array(
			"title" => t("Kokkuleppeid")
		);
		$week_sum = $month_sum = 0;
		for ($day = 1; $day <= $days_in_selected_month; $day++)
		{
			$day_start = mktime(0, 0, 0, self::$view_period_mon, $day, self::$view_period_year);
			$day_end = $day_start + 86399;
			$list = new object_data_list(
				array(
					"created" => new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, $day_start, $day_end)
				) + $common_list_filter,
				array(
					CL_CRM_PRESENTATION =>  array(new obj_sql_func(obj_sql_func::COUNT, "count" , "*"))
				)
			);
			$count = $list->arr();
			$count = reset($count);
			$count = $count["count"];
			$row_data["day_data_{$day}"] = $count;
			self::$column_sum_data["arrangements"]["day_data_{$day}"] += $count;
			$week_sum += $count;
			$month_sum += $count;

			$weekday = (int) date("N", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
			if ($weekday === $weeks_last_day or $day === $days_in_selected_month)
			{ // week summary
				$week_nr = date("W", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
				$row_data["week_data_{$week_nr}"] = $week_sum;
				self::$column_sum_data["arrangements"]["week_data_{$week_nr}"] += $week_sum;
				$week_sum = 0;
			}

			if ($weekday === $weeks_last_day or ($weekday + 1) === $weeks_last_day)
			{
				$row_data["day_data_{$day}_bgcolour"] = self::COLOUR_WEEKEND;
			}
		}
		self::$column_sum_data["arrangements"]["month_data"] += $month_sum;
		$row_data["month_data"] = $arrangements_month_sum = $month_sum;
		$table->define_data($row_data);


		// presentations done
		$row_data = $row_data_tpl + array(
			"title" => t("Esitlusi")
		);
		$week_sum = $month_sum = 0;
		$presentations_done = array();
		for ($day = 1; $day <= $days_in_selected_month; $day++)
		{
			$day_start = mktime(0, 0, 0, self::$view_period_mon, $day, self::$view_period_year);
			$day_end = $day_start + 86399;
			$list = new object_data_list(
				array(
					"real_start" => new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, $day_start, $day_end),
					"result" => crm_presentation_obj::$presentation_done_results
				) + $common_list_filter,
				array(
					CL_CRM_PRESENTATION =>  array(new obj_sql_func(obj_sql_func::COUNT, "count" , "*"))
				)
			);
			$count = $list->arr();
			$count = reset($count);
			$count = $count["count"];
			$row_data["day_data_{$day}"] = $count;
			self::$column_sum_data["presentations"]["day_data_{$day}"] += $count;
			$week_sum += $count;
			$month_sum += $count;

			$weekday = (int) date("N", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
			if ($weekday === $weeks_last_day or $day === $days_in_selected_month)
			{ // week summary
				$week_nr = date("W", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
				$row_data["week_data_{$week_nr}"] = $week_sum;
				self::$column_sum_data["presentations"]["week_data_{$week_nr}"] += $week_sum;
				$week_sum = 0;
			}

			if ($weekday === $weeks_last_day or ($weekday + 1) === $weeks_last_day)
			{
				$row_data["day_data_{$day}_bgcolour"] = self::COLOUR_WEEKEND;
			}
		}
		self::$column_sum_data["presentations"]["month_data"] += $month_sum;
		$row_data["month_data"] = $presentations_month_sum = $month_sum;
		$table->define_data($row_data);


		// presentations resulted in sale
		$row_data = $row_data_tpl + array(
			"title" => t("M&uuml;&uuml;ke"),
			"row_colour" => self::COLOUR_SALE
		);
		$week_sum = $month_sum = 0;
		for ($day = 1; $day <= $days_in_selected_month; $day++)
		{
			$day_start = mktime(0, 0, 0, self::$view_period_mon, $day, self::$view_period_year);
			$day_end = $day_start + 86399;
			$list = new object_data_list(
				array(
					"real_start" => new obj_predicate_compare(obj_predicate_compare::BETWEEN_INCLUDING, $day_start, $day_end),
					"result" => crm_presentation_obj::RESULT_DONE_SALE
				) + $common_list_filter,
				array(
					CL_CRM_PRESENTATION =>  array(new obj_sql_func(obj_sql_func::COUNT, "count" , "*"))
				)
			);
			$count = $list->arr();
			$count = reset($count);
			$count = $count["count"];
			$row_data["day_data_{$day}"] = $count;
			self::$column_sum_data["sales"]["day_data_{$day}"] += $count;
			$week_sum += $count;
			$month_sum += $count;

			$weekday = (int) date("N", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
			if ($weekday === $weeks_last_day or $day === $days_in_selected_month)
			{ // week summary
				$week_nr = date("W", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
				$row_data["week_data_{$week_nr}"] = $week_sum;
				self::$column_sum_data["sales"]["week_data_{$week_nr}"] += $week_sum;
				$week_sum = 0;
			}

			if ($weekday === $weeks_last_day or ($weekday + 1) === $weeks_last_day)
			{
				$row_data["day_data_{$day}_bgcolour"] = self::COLOUR_WEEKEND;
			}
		}
		self::$column_sum_data["sales"]["month_data"] += $month_sum;
		$row_data["month_data"] = $sales_month_sum = $month_sum;
		$table->define_data($row_data);

		// ...
		$efficiency = $arrangements_month_sum > 0 ? ($presentations_month_sum/$arrangements_month_sum * 100) : 0;
		$text = sprintf(t("%s (Efektiivsus %s)"), $employee_name, number_format($efficiency, 1, ",", " "));
		$table->change_row_group_name("employee", $employee_name, $text);
	}

	private static function build_total_stats($arr)
	{
		$table = $arr["prop"]["vcl_inst"];
		$weeks_last_day = 7; //!!! localest v6tta
		$days_in_selected_month = (int) date("t", mktime(1, 1, 1, self::$view_period_mon, 1, self::$view_period_year));
		$row_data_tpl = array(
			"employee" => t("KOKKU"),
			"week_data_bgcolour" => "white"
		);

		$row_data_a = $row_data_tpl + array(
			"title" => t("Kokkuleppeid")
		);
		$row_data_p = $row_data_tpl + array(
			"title" => t("Esitlusi")
		);
		$row_data_s = $row_data_tpl + array(
			"title" => t("M&uuml;&uuml;ke"),
			"row_colour" => self::COLOUR_SALE
		);
		$row_data_r = $row_data_tpl + array(
			"title" => t("Uusi aegu"),
			"row_colour" => self::COLOUR_REPLACEMENT
		);

		for ($day = 1; $day <= $days_in_selected_month; $day++)
		{
			$row_data_a["day_data_{$day}"] = self::$column_sum_data["arrangements"]["day_data_{$day}"];
			$row_data_p["day_data_{$day}"] = self::$column_sum_data["presentations"]["day_data_{$day}"];
			$row_data_s["day_data_{$day}"] = self::$column_sum_data["sales"]["day_data_{$day}"];
			$row_data_r["day_data_{$day}"] = self::$column_sum_data["replacements"]["day_data_{$day}"];

			$weekday = (int) date("N", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
			if ($weekday === $weeks_last_day or $day === $days_in_selected_month)
			{ // week summary
				$week_nr = date("W", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
				$row_data_a["week_data_{$week_nr}"] = self::$column_sum_data["arrangements"]["week_data_{$week_nr}"];
				$row_data_p["week_data_{$week_nr}"] = self::$column_sum_data["presentations"]["week_data_{$week_nr}"];
				$row_data_s["week_data_{$week_nr}"] = self::$column_sum_data["sales"]["week_data_{$week_nr}"];
				$row_data_r["week_data_{$week_nr}"] = self::$column_sum_data["replacements"]["week_data_{$week_nr}"];
			}

			if ($weekday === $weeks_last_day or ($weekday + 1) === $weeks_last_day)
			{
				$row_data_a["day_data_{$day}_bgcolour"] = self::COLOUR_WEEKEND;
				$row_data_p["day_data_{$day}_bgcolour"] = self::COLOUR_WEEKEND;
				$row_data_s["day_data_{$day}_bgcolour"] = self::COLOUR_WEEKEND;
				$row_data_r["day_data_{$day}_bgcolour"] = self::COLOUR_WEEKEND;
			}
		}

		$row_data_a["month_data"] = self::$column_sum_data["arrangements"]["month_data"];
		$row_data_p["month_data"] = self::$column_sum_data["presentations"]["month_data"];
		$row_data_s["month_data"] = self::$column_sum_data["sales"]["month_data"];
		$row_data_r["month_data"] = self::$column_sum_data["replacements"]["month_data"];

		$table->define_data($row_data_a);
		$table->define_data($row_data_p);
		$table->define_data($row_data_s);
		$table->define_data($row_data_r);
	}

	private static function set_view_parameters_and_defaults($arr)
	{
		self::$column_sum_data = array();

		/// view type
		if (isset($arr["request"]["tmstat_type"]) and in_array($arr["request"]["tmstat_type"], self::$view_types))
		{
			self::$view_type = $arr["request"]["tmstat_type"];
		}

		/// view period
		if ("month" === self::$view_type)
		{
			// select year
			if (isset($arr["request"]["tmstat_y"]) and $arr["request"]["tmstat_y"] >= 2009 and $arr["request"]["tmstat_y"] <= date("Y"))// year between including this sw creation year and current year
			{
				self::$view_period_year = (int) $arr["request"]["tmstat_y"];
			}
			else
			{ // default current year
				self::$view_period_year = (int) date("Y");
			}

			// select month
			if (isset($arr["request"]["tmstat_m"]) and $arr["request"]["tmstat_m"] >= 1 and $arr["request"]["tmstat_m"] <= 12) // month between including 1 and 12
			{
				self::$view_period_mon = (int) $arr["request"]["tmstat_m"];
			}
			elseif (self::$view_period_year !== (int) date("Y"))
			{ // only year given, set first month in the year by default
				self::$view_period_mon = 1;
			}
			else
			{ // default current month
				self::$view_period_mon = (int) date("n");
			}
		}
	}

	private static function define_tmstat_display_table_layout($arr)
	{
		$this_o = $arr["obj_inst"];
		$table = $arr["prop"]["vcl_inst"];
		$table->set_sortable(false);
		$weeks_last_day = 7; //!!! localest v6tta

		if ("month" === self::$view_type)
		{
			// row titles column
			$table->define_field(array(
				"name" => "title",
				"sortable" => false,
				"chgbgcolor" => "title_bgcolour",
				"color" => "row_colour",
				"caption" => ""
			));

			// column for each day with week summary columns after last day in week or month and for whole month at end
			$days_in_selected_month = (int) date("t", mktime(1, 1, 1, self::$view_period_mon, 1, self::$view_period_year));
			for ($day = 1; $day <= $days_in_selected_month; $day++)
			{
				// day data column
				$table->define_field(array(
					"name" => "day_data_{$day}",
					"sortable" => false,
					"chgbgcolor" => "day_data_{$day}_bgcolour",
					"color" => "row_colour",
					"caption" => $day
				));
				self::$column_sum_data["arrangements"]["day_data_{$day}"] = 0;
				self::$column_sum_data["presentations"]["day_data_{$day}"] = 0;
				self::$column_sum_data["sales"]["day_data_{$day}"] = 0;
				self::$column_sum_data["replacements"]["day_data_{$day}"] = 0;

				$weekday = (int) date("N", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
				if ($weekday === $weeks_last_day or $day === $days_in_selected_month)
				{ // week summary column
					$week_nr = date("W", mktime(1, 1, 1, self::$view_period_mon, $day, self::$view_period_year));
					$table->define_field(array(
						"name" => "week_data_{$week_nr}",
						"sortable" => false,
						"color" => "row_colour",
						"chgbgcolor" => "week_data_bgcolour",
						"caption" => t("K"),
						"tooltip" => sprintf(t("Koondtulemus %s. n&auml;dalal"), $week_nr)
					));
					self::$column_sum_data["arrangements"]["week_data_{$week_nr}"] = 0;
					self::$column_sum_data["presentations"]["week_data_{$week_nr}"] = 0;
					self::$column_sum_data["sales"]["week_data_{$week_nr}"] = 0;
					self::$column_sum_data["replacements"]["week_data_{$week_nr}"] = 0;
				}
			}

			/// month summary column
			$table->define_field(array(
				"name" => "month_data",
				"sortable" => false,
				"chgbgcolor" => "month_data_bgcolour",
				"color" => "row_colour",
				"caption" => t("Kokku")
			));
			self::$column_sum_data["arrangements"]["month_data"] = 0;
			self::$column_sum_data["presentations"]["month_data"] = 0;
			self::$column_sum_data["sales"]["month_data"] = 0;
			self::$column_sum_data["replacements"]["month_data"] = 0;
		}

		$table->set_rgroupby(array("employee" => "employee"));
	}

	public static function _get_tmstat_y(&$arr)
	{
		$r = class_base::PROP_OK;
		$this_o = $arr["obj_inst"];
		$arr["prop"]["value"] = empty($arr["request"]["tmstat_y"]) ? date("Y") : $arr["request"]["tmstat_y"];
		$range = range(2009, date("Y"));
		$arr["prop"]["options"] = array_combine($range, $range);
		return $r;
	}

	public static function _get_tmstat_m(&$arr)
	{
		$r = class_base::PROP_OK;
		$this_o = $arr["obj_inst"];
		$arr["prop"]["value"] = empty($arr["request"]["tmstat_m"]) ? date("n") : $arr["request"]["tmstat_m"];
		$range = range(1, 12);
		$arr["prop"]["options"] = array_combine($range, $range);
		return $r;
	}
}

?>
