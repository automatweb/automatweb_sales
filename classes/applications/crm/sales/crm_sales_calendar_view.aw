<?php

class crm_sales_calendar_view
{
	private static $default_conf = array(
		// "show_days_with_events" => 1,
		"full_weeks" => 1,
		// "month_week" => 1
	);

	public static function _get_general_calendar(&$arr)
	{
		// cfg calendar
		$cal = $arr["prop"]["vcl_inst"];
		$cal->configure(self::$default_conf);
		$viewtype = empty($arr["request"]["viewtype"]) ? "week" : $arr["request"]["viewtype"];
		$date = empty($arr["request"]["date"]) ? null/* ///!!! default mis on? */ : $arr["request"]["date"];
		$range = $cal->get_range(array(
			"date" => $date,
			"viewtype" => $viewtype
		));
		$start = $range["start"];
		$end = $range["end"];

		// get events
		$this_o = $arr["obj_inst"];
		$owner = $arr["obj_inst"]->prop("owner");
		$events = new crm_task_list(array(
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end),
					"real_start" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end)
				)
			))
		));

		// insert events
		if ($events->count() > 0)
		{
			$task = $events->begin();
			do
			{
				$has_started = $task->prop("real_start") > 1;
				$cal->add_item(array(
					"item_start" => $has_started ? $task->prop("real_start") : $task->prop("start1"),
					"bgcolor" => $has_started ? "" : crm_sales::COLOUR_CAN_START,
					"data" => array(
						"name" => $task->name(),
						"link" => html::get_change_url($task->id(), array("return_url" => get_ru()))
					)
				));
			}
			while ($task = $events->next());
		}
		return PROP_OK;
	}

	public static function _get_calls_calendar(&$arr)
	{
		// cfg calendar
		$cal = $arr["prop"]["vcl_inst"];
		$cal->configure(self::$default_conf);
		$viewtype = empty($arr["request"]["viewtype"]) ? "week" : $arr["request"]["viewtype"];
		$date = empty($arr["request"]["date"]) ? null/* ///!!! default mis on? */ : $arr["request"]["date"];
		$range = $cal->get_range(array(
			"date" => $date,
			"viewtype" => $viewtype
		));
		$start = $range["start"];
		$end = $range["end"];

		// get events
		$this_o = $arr["obj_inst"];
		$owner = $arr["obj_inst"]->prop("owner");
		$events = new crm_call_list(array(
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end),
					"real_start" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end)
				)
			))
		));

		// insert events
		if ($events->count() > 0)
		{
			$task = $events->begin();
			do
			{
				$has_started = $task->prop("real_start") > 1;
				$cal->add_item(array(
					"item_start" => $has_started ? $task->prop("real_start") : $task->prop("start1"),
					"bgcolor" => $has_started ? "" : crm_sales::COLOUR_CAN_START,
					"data" => array(
						"name" => $task->name(),
						"link" => html::get_change_url($task->id(), array("return_url" => get_ru()))
					)
				));
			}
			while ($task = $events->next());
		}
		return PROP_OK;
	}

	public static function _get_presentations_calendar(&$arr)
	{
		$role = automatweb::$request->get_application()->get_current_user_role();

		// cfg calendar
		$cal = $arr["prop"]["vcl_inst"];
		$cal->configure(self::$default_conf);
		$viewtype = empty($arr["request"]["viewtype"]) ?
			(crm_sales_obj::ROLE_SALESMAN === $role ? "day" : "week") :
			$arr["request"]["viewtype"];
		$date = empty($arr["request"]["date"]) ? null/* ///!!! default mis on? */ : $arr["request"]["date"];
		$range = $cal->get_range(array(
			"date" => $date,
			"viewtype" => $viewtype
		));
		$start = $range["start"];
		$end = $range["end"];

		// get events
		$this_o = $arr["obj_inst"];
		$owner = $arr["obj_inst"]->prop("owner");
		$events = new crm_presentation_list(array(
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end),
					"real_start" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end)
				)
			))
		));

		// insert events
		$show_comment = "month" !== $viewtype;// month view would get too crowded if comments shown as well
		$show_customer_comments = "day" === $viewtype;// show all customer comments in day view

		if ($show_customer_comments)
		{
			$comment_format = t("Koht: %s | L&auml;biviija: %s | Tel.: %s | Kokkuleppija: %s | <div style=\"width: 60em; border: 1px solid white; padding: 3px 7px 3px 7px; margin-top: 5px;\"><u>Kommentaarid</u> %s</div>");
		}
		else
		{
			$comment_format = t("Koht: %s | L&auml;biviija: %s | Tel.: %s | Kokkuleppija: %s");
		}

		$undefined_str = html::span(array(
			"color" => "red",
			"content" => t("m&auml;&auml;ramata")
		));
		$current_time = time();
		if ($events->count() > 0)
		{
			$cl_user = new user();
			$presentation = $events->begin();
			do
			{
				if ($show_comment)
				{
					$customer_relation = new object($presentation->prop("customer_relation"));
					$customer = $customer_relation->get_first_obj_by_reltype("RELTYPE_BUYER");
					$location = is_oid($presentation->prop("presentation_location")) ? $presentation->prop("presentation_location.name") : $undefined_str;
					$salesperson = is_oid($presentation->prop("customer_relation.salesman")) ? $presentation->prop("customer_relation.salesman.name") : $undefined_str;
					$phones = implode(", ", $customer->get_phones());
					$tm_salesman = $cl_user->get_person_for_uid($presentation->createdby());
					$tm_salesman = (is_object($tm_salesman) and $tm_salesman->is_saved()) ? $tm_salesman->name() : $presentation->createdby();

					if ($show_customer_comments)
					{
						$cl_comment = get_instance(CL_COMMENT);
						$comment_data = $cl_comment->get_comment_list(array(
							"parent" => $customer_relation->id()
						));
						$comments = "";
						foreach ($comment_data as $data)
						{
							$comments .= "<br /><em>{$data["createdby"]}:</em> {$data["commtext"]}";
						}

						$comment = sprintf($comment_format, $location, $salesperson, $phones, $tm_salesman, $comments);
					}
					else
					{
						$comment = sprintf($comment_format, $location, $salesperson, $phones, $tm_salesman);
					}
				}
				else
				{
					$comment = null;
				}

				// an occurred presentation has different colour, ...
				$has_started = $presentation->prop("real_start") > 1;

				//
				$bgcolor = $has_started ? "" : ($presentation->prop("start1") < $current_time ? crm_sales::COLOUR_OVERDUE : crm_sales::COLOUR_CAN_START);

				$cal->add_item(array(
					"item_start" => $has_started ? $presentation->prop("real_start") : $presentation->prop("start1"),
					"bgcolor" => $bgcolor,
					"data" => array(
						"name" => $presentation->name(),
						"link" => html::get_change_url($presentation->id(), array("return_url" => get_ru())),
						"comment" => $comment
					)
				));
			}
			while ($presentation = $events->next());
		}
		return PROP_OK;
	}

	public static function _get_personal_calendar(&$arr)
	{
		// cfg calendar
		$cal = $arr["prop"]["vcl_inst"];
		$cal->configure(self::$default_conf);
		$viewtype = empty($arr["request"]["viewtype"]) ? "month" : $arr["request"]["viewtype"];
		$date = empty($arr["request"]["date"]) ? null/* ///!!! default mis on? */ : $arr["request"]["date"];
		$range = $cal->get_range(array(
			"date" => $date,
			"viewtype" => $viewtype
		));
		$start = $range["start"];
		$end = $range["end"];

		// comment settings
		$show_comment = "month" !== $viewtype;// month view would get too crowded if comments shown as well
		$comment_format = t("Koht: %s | Tel.: %s | Kokkuleppija: %s | Kommentaar: %s");
		$undefined_str = html::span(array(
			"color" => "red",
			"content" => t("m&auml;&auml;ramata")
		));

		// get events
		$this_o = $arr["obj_inst"];
		$owner = $arr["obj_inst"]->prop("owner");
		$events = new crm_task_list(array(
			"createdby" => aw_global_get("uid"),
			new object_list_filter(array(
				"logic" => "OR",
				"conditions" => array (
					"start1" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end),
					"real_start" => new obj_predicate_compare(OBJ_COMP_BETWEEN_INCLUDING, $start, $end)
				)
			))
		));

		// insert events
		if ($events->count() > 0)
		{
			$cl_user = new user();
			$task = $events->begin();
			do
			{
				if ($task->class_id() == CL_CRM_PRESENTATION and $show_comment)
				{
					$customer_relation = new object($task->prop("customer_relation"));
					$customer = $customer_relation->get_first_obj_by_reltype("RELTYPE_BUYER");
					$location = is_oid($task->prop("task_location")) ? $task->prop("task_location.name") : $undefined_str;
					$phones = implode(", ", $customer->get_phones());
					$tm_salesman = $cl_user->get_person_for_uid($task->createdby());
					$tm_salesman = (is_object($tm_salesman) and $tm_salesman->is_saved()) ? $tm_salesman->name() : $task->createdby();
					$comment = sprintf($comment_format, $location, $phones, $tm_salesman, $task->comment());
				}
				else
				{
					$comment = null;
				}

				$has_started = $task->prop("real_start") > 1;
				$cal->add_item(array(
					"item_start" => $has_started ? $task->prop("real_start") : $task->prop("start1"),
					"bgcolor" => $has_started ? "" : crm_sales::COLOUR_CAN_START,
					"data" => array(
						"name" => $task->name(),
						"link" => html::get_change_url($task->id(), array("return_url" => get_ru())),
						"comment" => $comment
					)
				));
			}
			while ($task = $events->next());
		}
		return PROP_OK;
	}
}

?>
