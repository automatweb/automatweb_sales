<?php
/*
@classinfo syslog_type=ST_CRM_PRESENTATION relationmgr=yes no_status=1 prop_cb=1 maintainer=voldemar confirm_save_data=1

@tableinfo planner index=id master_table=objects master_index=brother_of
@groupinfo customer caption="Klient"

@default table=planner
	@property result_task type=hidden datatype=int
	@property customer_relation type=hidden datatype=int
	@property hr_schedule_job type=hidden datatype=int
	@property real_maker type=hidden datatype=int

@default group=general
	@property presentation_tools type=toolbar no_caption=1
	@caption Esitluse toimingud

	@property name type=textbox table=objects field=name
	@caption Nimi

	@property comment type=textbox table=objects field=comment
	@caption Kommentaar

	@property result type=select
	@caption Tulemus

	@property result_task_view type=text store=no
	@caption Tulemustegevus

	@property start1 field=start type=datepicker
	@caption Algus

	@property end type=datepicker
	@caption L&otilde;pp

	@property real_start type=datepicker
	@comment Kui tegelik algus sisestada, arvatakse esitlus toimunuks.
	@caption Tegelik algus

	@property real_duration type=textbox datatype=int
	@comment Kui tegelik kestus sisestada, arvatakse esitlus toimunuks.
	@caption Tegelik kestus (h)

	@property assigned_salesman_edit type=select datatype=int store=no
	@caption M&uuml;&uuml;giesindaja

	@property participant_select type=relpicker delete_rels_popup_button=1 no_edit=1 multiple=1 size=5 store=connect reltype=RELTYPE_PARTICIPANT
	@caption Osalejad

	@property presentation_location type=relpicker reltype=RELTYPE_LOCATION table=objects field=meta method=serialize no_search=1 add_edit_autoreturn=1 no_sel=1
	@comment * - m&auml;rgitud aadressid on kontakti aadressid. &Uuml;lej&auml;&auml;nud on esitluse toimumiskohad, mis kontakti andmetes ei kajastu.
	@caption Toimumiskoht

	@property presentation_location_save_to_customer type=checkbox ch_value=1 store=no
	@caption &nbsp;

	@property presentation_appointment_phone_nr type=text store=no
	@caption Tel. nr. millel kokku lepiti

	@property offer type=objpicker class_id=CL_CRM_OFFER field=aw_offer
	@caption Seotud pakkumine

	@property presented_products type=relpicker reltype=RELTYPE_PRESENTED_PRODUCT store=connect size=8 no_edit=1
	@caption Esitletavad tooted

	@property sold_products type=relpicker reltype=RELTYPE_SOLD_PRODUCT store=connect size=8 no_edit=1
	@caption M&uuml;&uuml;dud tooted



@layout impl_bit type=vbox closeable=1 area_caption=Osalejad
	@property impl_tb type=toolbar no_caption=1 store=no parent=impl_bit
	@property parts_table type=table no_caption=1 store=no parent=impl_bit


@default group=customer
	@property customer_info type=text store=no no_caption=1


////////////// RELTYPES /////////////

@reltype ROW value=7 clid=CL_TASK_ROW
@caption Rida

@reltype LOCATION value=8 clid=CL_ADDRESS
@caption Toimumiskoha aadress

@reltype PRESENTED_PRODUCT value=9 clid=CL_SHOP_PRODUCT
@caption Toode mida esitletakse

@reltype SOLD_PRODUCT value=10 clid=CL_SHOP_PRODUCT
@caption M&uuml;&uuml;dud toode

@reltype PARTICIPANT value=12 clid=CL_CRM_PERSON
@caption Osaleja

*/

class crm_presentation extends task
{
	function crm_presentation()
	{
		$this->init(array(
			"tpldir" => "groupware/task",
			"clid" => CL_CRM_PRESENTATION
		));
	}

	function _get_presentation_tools(&$arr)
	{
		$tb = $arr["prop"]["vcl_inst"];
		$this_o = $arr["obj_inst"];
		$tb->add_save_button();
		$tb->add_save_close_button();
		return PROP_OK;
	}

	function _get_presentation_appointment_phone_nr(&$arr)
	{
		$phone = "";
		try
		{
			$application = automatweb::$request->get_application();
			if ($application->is_a(CL_CRM_SALES))
			{
				$list = new object_list(array(
					"class_id" => CL_CRM_CALL,
					"parent" => $application->prop("calls_folder"),
					"result_task" => $arr["obj_inst"]->id()
				));

				if ($list->count())
				{
					$call = $list->begin();
					$phone = obj($call->prop("phone"), array(), CL_CRM_PHONE);
					$phone = $phone->name();
				}
			}
		}
		catch (Exception $e)
		{
		}

		$arr["prop"]["value"] = $phone;
		return PROP_OK;
	}

	function _get_presentation_location_save_to_customer(&$arr)
	{
		$arr["prop"]["post_append_text"] = t("Salvesta valitud toimumiskoht kontakti aadresside hulka");
		return PROP_OK;
	}

	function _get_presentation_location(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$customer_relation = new object($this_o->prop("customer_relation"));
		if ($customer_relation->is_saved())
		{
			$customer = $customer_relation->get_first_obj_by_reltype("RELTYPE_BUYER");
			$customer_addresses = new object_list($customer->connections_from(array("type" => "RELTYPE_ADDRESS_ALT")));

			$presentation_addresses = new object_list($this_o->connections_from(array("type" => "RELTYPE_LOCATION")));

			if ($presentation_addresses->count())
			{
				$customer_addresses->add($presentation_addresses);
			}
			elseif ($first_address = $customer_addresses->begin())
			{
				$arr["prop"]["value"] = $first_address->id();
				$arr["prop"]["post_append_text"] .= " ".html::href(array(
					"url" => html::get_change_url($first_address->id(), array(
						"save_autoreturn" => !empty($arr["prop"]["add_edit_autoreturn"]),
						"return_url" => get_ru()
					)),
					"caption" => "<img src='".aw_ini_get("baseurl")."/automatweb/images/icons/edit.gif' border=0>",
					"title" => t("Muuda")
				));
			}

			$customer_addresses = $customer_addresses->names();
			foreach ($customer_addresses as $id => $name)
			{
				$customer_addresses[$id] = "* " . $name;
			}

			$arr["prop"]["options"] = $customer_addresses + $presentation_addresses->names();
		}
		return PROP_OK;
	}

	function _get_result_task_view(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$result_task = $this_o->prop("result_task");
		$r = PROP_IGNORE;

		if ($result_task)
		{
			try
			{
				$result_task = new object($result_task);
				$caption = $result_task->prop_xml("name");
				$caption = empty($caption) ? t("[Nimetu]") : $caption;
				$arr["prop"]["value"] = html::href(array(
					"url" => $this->mk_my_orb("change", array(
						"id" => $result_task->id(),
						"return_url" => get_ru()
					), $result_task->class_id()),
					"caption" => $caption,
					"title" => t("Muuda")
				));

				$r = PROP_OK;
			}
			catch (awex_obj $e)
			{
			}
		}

		return $r;
	}

	function _get_presented_products(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$r = PROP_OK;

		// in sales application, get products from associated warehouse
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$warehouse = $application->prop("warehouse");
			if (is_oid($warehouse))
			{
				$warehouse = new object($warehouse);
				$list = $warehouse->get_products(array());
				$arr["prop"]["options"] = $list->names();
			}
		}

		return $r;
	}

	function _set_presentation_location(&$arr)
	{
		$r = PROP_OK;
		$value = $arr["prop"]["value"];

		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$role = $application->get_current_user_role();
			if (crm_sales_obj::ROLE_TELEMARKETING_SALESMAN === $role and !is_oid($arr["prop"]["value"]))
			{
				$arr["prop"]["error"] = t("Toimumiskoha valimine on kohustuslik");
				$return = PROP_FATAL_ERROR;
			}
		}

		// if requested to save address to customer also, check if customer relation exists, customer doesn't already have that address and then connect
		if (is_oid($value) and !empty($arr["request"]["presentation_location_save_to_customer"]))
		{
			$this_o = $arr["obj_inst"];
			$customer_relation = new object($this_o->prop("customer_relation"));
			if ($customer_relation->is_saved())
			{
				$customer = $customer_relation->get_first_obj_by_reltype("RELTYPE_BUYER");
				if (!$customer->is_connected_to(array("to" => $value, "type" => "RELTYPE_ADDRESS_ALT")))
				{
					$customer->connect(array("to" => $value, "type" => "RELTYPE_ADDRESS_ALT"));
				}
			}
		}
		return $r;
	}

	function _get_assigned_salesman_edit(&$arr)
	{
		$return = PROP_OK;
		$this_o = $arr["obj_inst"];

		// determine access
		// in sales application, a salesman can't access this property
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$role = $application->get_current_user_role();
			if (crm_sales_obj::ROLE_SALESMAN === $role)
			{
				$return = PROP_IGNORE;
			}
			else
			{
				// get options
				$profession = new object($application->prop("role_profession_salesman"));
				$employees = $profession->get_workers(null, false);
				$arr["prop"]["options"] = array("" => "") + $employees->names();
			}
		}

		if (PROP_OK === $return)
		{
			// set current value
			$customer_relation = new object($this_o->prop("customer_relation"));
			if ($customer_relation->is_saved())
			{
				$arr["prop"]["value"] = $customer_relation->prop("salesman");
			}
		}

		return $return;
	}

	function _set_assigned_salesman_edit(&$arr)
	{
		$return = PROP_OK;
		$this_o = $arr["obj_inst"];

		// determine access
		// in sales application, a salesman can't access this property
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$role = $application->get_current_user_role();
			if (crm_sales_obj::ROLE_SALESMAN === $role)
			{
				$return = PROP_IGNORE;
			}

			if (crm_sales_obj::ROLE_TELEMARKETING_SALESMAN === $role and !is_oid($arr["prop"]["value"]))
			{
				$arr["prop"]["error"] = t("M&uuml;&uuml;giesindaja valimine on kohustuslik");
				$return = PROP_FATAL_ERROR;
			}
		}

		// set value
		$customer_relation = new object($this_o->prop("customer_relation"));
		if (is_oid($arr["prop"]["value"]) and PROP_OK === $return and $customer_relation->is_saved())
		{
			$salesperson = obj($arr["prop"]["value"], array(), CL_CRM_PERSON);

			// delete connection to previous salesperson
			$prev_salesperson_oid = $customer_relation->prop("salesman");
			if (is_oid($prev_salesperson_oid) and $customer_relation->is_connected_to(array("to" => $prev_salesperson_oid, "type" => "RELTYPE_SALESMAN")))
			{
				$customer_relation->disconnect(array("from" => $prev_salesperson_oid, "type" => "RELTYPE_SALESMAN"));
			}

			$customer_relation->set_prop("salesman", $salesperson->id());
			$customer_relation->save();
			$customer_relation->connect(array("to" => $salesperson->id(), "reltype" => "RELTYPE_SALESMAN"));

			// add_participant
			$this->add_participant($this_o, $salesperson);
		}

		return $return;
	}

	function _get_real_duration(&$arr)
	{
		$arr["prop"]["value"] = (isset($arr["prop"]["value"]) and $arr["prop"]["value"] > 0) ? number_format($arr["prop"]["value"]/60, 2, ".", " ") : "";

		if ($arr["obj_inst"]->is_done() and !$arr["obj_inst"]->is_finished())
		{
			$arr["prop"]["disabled"] = 1;
		}

		return PROP_OK;
	}

	function _get_real_start(&$arr)
	{
		if ($arr["obj_inst"]->is_done() and !$arr["obj_inst"]->is_finished())
		{
			$arr["prop"]["disabled"] = 1;
		}

		return PROP_OK;
	}

	function _get_customer_info(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$cro = new crm_company_customer_data();
		$cro->form_only = true;
		$cro->no_form = true;
		$arr["prop"]["value"] = $cro->view(array(
			"id" => $this_o->prop("customer_relation"),
			"group" => "sales_data"
		));
	}

	function _get_start1(&$arr)
	{
		$return = PROP_OK;
		// in sales application, a salesman can't change planned start/end
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$role = $application->get_current_user_role();
			if (crm_sales_obj::ROLE_SALESMAN === $role)
			{
				$arr["prop"]["disabled"] = 1;
			}
		}

		if ($arr["obj_inst"]->is_done())
		{
			$arr["prop"]["disabled"] = 1;
		}

		return $return;
	}

	function _get_end(&$arr)
	{
		$return = PROP_OK;
		// in sales application, a salesman can't change planned start/end
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$role = $application->get_current_user_role();
			if (crm_sales_obj::ROLE_SALESMAN === $role)
			{
				$arr["prop"]["disabled"] = 1;
			}
		}

		if ($arr["obj_inst"]->is_done())
		{
			$arr["prop"]["disabled"] = 1;
		}

		return $return;
	}

	function _set_end(&$arr)
	{
		$return = PROP_OK;
		// in sales application, a salesman can't change planned start/end
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$role = $application->get_current_user_role();
			if (crm_sales_obj::ROLE_SALESMAN === $role)
			{
				$return = PROP_IGNORE;
			}
		}

		if ($arr["obj_inst"]->is_done())
		{
			$return = PROP_IGNORE;
		}

		return $return;
	}

	function _get_comment(&$arr)
	{
		$application = automatweb::$request->get_application();
		if ($application->is_a(CL_CRM_SALES))
		{
			$arr["prop"]["value"] = "";
			$arr["prop"]["caption"] = t("Lisa kommentaar");
		}
		return PROP_OK;
	}

	function _set_comment(&$arr)
	{
		$this_o = $arr["obj_inst"];
		$val = $arr["prop"]["value"];
		if (strlen($val) > 1 and $val !== $this_o->comment() and $this_o->prop("customer_relation"))
		{
			$comm = new forum_comment();
			$commdata = $this_o->name() . ":\n" . $val;
			if (strlen($commdata["comment"]))
			{
				$comm->submit(array(
					"parent" => $this_o->prop("customer_relation"),
					"commtext" => $commdata,
					"return" => "id"
				));
			}
		}
		return PROP_OK;
	}

/* SET DONE
	if either result, real_start or real_duration is set, all of them must be set. Presentation will then be considered done
*/
	function _set_real_duration(&$arr)
	{
		$return = PROP_OK;
		$entered_real_duration = aw_math_calc::string2float($arr["prop"]["value"]);

		if (empty($arr["request"]["result"]))
		{
			if ($entered_real_duration)
			{
				$arr["prop"]["error"] = t("Kui sisestada kestus, peab valima ka tulemuse");
				$return = PROP_FATAL_ERROR;
			}
		}
		else
		{
			$entered_real_start = isset($arr["request"]["real_start"]) ? datepicker::get_timestamp($arr["request"]["real_start"]) : 0;

			if ($entered_real_duration <= 0 and ($entered_real_start > 1 or in_array($arr["request"]["result"], crm_presentation_obj::$presentation_done_results)))
			{
				$arr["prop"]["error"] = t("Kui esitlus on tehtud, peab sisestama ka kestuse");
				$return = PROP_FATAL_ERROR;
			}
			elseif ($entered_real_duration and !in_array($arr["request"]["result"], crm_presentation_obj::$presentation_done_results))
			{
				$arr["prop"]["error"] = t("Kui esitlus ei toimund, ei saa sisestada ka kestust");
				$arr["prop"]["value"] = "";
				$return = PROP_FATAL_ERROR;
			}
			else
			{
				$arr["prop"]["value"] = ceil(aw_math_calc::string2float($entered_real_duration)*60);
				$return = PROP_OK;
			}
		}

		return $return;
	}

	function _set_real_start(&$arr)
	{
		if ($arr["obj_inst"]->is_done() and !$arr["obj_inst"]->is_finished())
		{
			$return = PROP_IGNORE;
		}
		else
		{
			$return = PROP_OK;
			$entered_real_start = datepicker::get_timestamp($arr["prop"]["value"]);

			if ($entered_real_start > time())
			{
				$arr["prop"]["error"] = t("Tegelik algusaeg ei saa olla tulevikus");
				$return = PROP_FATAL_ERROR;
			}
			else
			{
				$entered_real_duration = isset($arr["request"]["real_duration"]) ? aw_math_calc::string2float($arr["request"]["real_duration"]) : 0;

				if ($entered_real_start < 2 and ($entered_real_duration > 0 or in_array($arr["request"]["result"], crm_presentation_obj::$presentation_done_results)))
				{
					$arr["prop"]["error"] = t("Kui esitlus on tehtud, peab sisestama ka algusaja");
					$return = PROP_FATAL_ERROR;
				}
				elseif ($entered_real_start and !in_array($arr["request"]["result"], crm_presentation_obj::$presentation_done_results))
				{
					$arr["prop"]["error"] = t("Kui esitlus ei toimund, ei saa sisestada ka algusaega");
					$arr["prop"]["value"] = "";
					$return = PROP_ERROR;
				}
			}
		}

		return $return;
	}

	function _set_result(&$arr)
	{
		$return = PROP_OK;
		$entered_real_start = isset($arr["request"]["real_start"]) ? datepicker::get_timestamp($arr["request"]["real_start"]) : 0;
		$entered_real_duration = isset($arr["request"]["real_duration"]) ? aw_math_calc::string2float($arr["request"]["real_duration"]) : 0;

		if (empty($arr["prop"]["value"]) and ($entered_real_duration or $entered_real_start))
		{
			$arr["prop"]["error"] = t("Kui esitlus toimus, peab valima ka tulemuse");
			$return = PROP_FATAL_ERROR;
		}

		return $return;
	}
/* END SET DONE */

	function _set_start1(&$arr)
	{
		$return = PROP_OK;

		if ($arr["obj_inst"]->is_done())
		{
			$return = PROP_IGNORE;
		}
		else
		{
			// in sales application, a salesman can't change planned start/end
			$application = automatweb::$request->get_application();
			$start = datepicker::get_timestamp($arr["prop"]["value"]);
			if ($application->is_a(CL_CRM_SALES))
			{
				$role = $application->get_current_user_role();
				if (crm_sales_obj::ROLE_SALESMAN === $role)
				{
					return PROP_IGNORE;
				}

				if ($start < 2)
				{
					$arr["prop"]["error"] = t("Esitluse algusaeg on kohustuslik");
					$return = PROP_FATAL_ERROR;
				}
			}
		}

		return $return;
	}

	function _get_result(&$arr)
	{
		$return = PROP_OK;
		$results = $arr["obj_inst"]->result_names();
		arsort($results);
		$arr["prop"]["options"] = array("" => "") + $results;
		if ($arr["obj_inst"]->is_done())
		{
			$arr["prop"]["disabled"] = 1;
		}
		return $return;
	}

	/**
      @attrib name=submit_and_return all_args=1
	**/
	function submit_and_return($arr = array())
	{
		$this_o = new object($arr["id"]);
		$this_o->set_meta("user_reviewed", 1); // to remember if result task has already been viewed and saved by user and decide if redirect is needed

		$r = parent::submit_and_return($arr);
		if ("submit_and_return" === $arr["action"] and $this->data_processed_successfully())
		{
			$redirect = $this->get_submit_redirect_url($this_o, $arr["post_ru"]);
			if ($redirect)
			{
				$r = $redirect;
			}
		}
		return $r;

/*
		$r = $this->submit($arr);

		if ($this->data_processed_successfully())
		{
			$application = automatweb::$request->get_application();
			if ($application->is_a(CL_CRM_SALES))
			{
				// return to previous view (call or presentation that this presentation is the result for)
				try
				{
					$presentation = obj($arr["id"], array(), CL_CRM_PRESENTATION);
					$list = new object_list(array(
						"class_id" => CL_CRM_CALL,
						"parent" => $application->prop("calls_folder"),
						"result_task" => $presentation->id()
					));

					if ($list->count())
					{
						$call = $list->begin();
						$r = $this->mk_my_orb("change", array(
							"id" => $call->id()
						), "crm_call");
					}
					elseif (!empty($arr["return_url"]))
					{
						$r = $arr["return_url"];
					}
					elseif (!empty($arr["post_ru"]))
					{
						$r = $arr["post_ru"];
					}
				}
				catch (Exception $e)
				{
					if (!empty($arr["return_url"]))
					{
						$r = $arr["return_url"];
					}
					elseif (!empty($arr["post_ru"]))
					{
						$r = $arr["post_ru"];
					}
				}
			}

			$this->show_msg_text(t("Uus esitlus t&ouml;&ouml;deldud. Uuesti saab seda vaadata tulemustegevuse lingi kaudu"));
		}
		return $r;
*/
	}

	function submit($arr = array())
	{
		$this_o = new object($arr["id"]);
		$this_o->set_meta("user_reviewed", 1); // to remember if result task has already been viewed and saved by user and decide if redirect is needed

		$r = parent::submit($arr);
		if ("submit" === $arr["action"] and $this->data_processed_successfully())
		{
			$redirect = $this->get_submit_redirect_url($this_o, $arr["post_ru"]);
			if ($redirect)
			{
				$r = $redirect;
			}
		}
		return $r;
	}

	private function get_submit_redirect_url($this_o, $post_ru)
	{
		$r = false;
		$application = automatweb::$request->get_application();

		if ($application->is_a(CL_CRM_SALES))
		{
			$result = (int) $this_o->prop("result");
			if (
				crm_presentation_obj::RESULT_DONE_NEW_PRESENTATION === $result or
				crm_presentation_obj::RESULT_CANCEL_NEW_PRESENTATION === $result
			)
			{
				try
				{
					// jump to result presentation
					$result_task = obj($this_o->prop("result_task"), array(), CL_CRM_PRESENTATION);

					if (!$result_task->meta("user_reviewed"))
					{ // redirect only if result presentation just created and user needs to review or edit it
						$r = html::get_change_url($result_task->id(), array("return_url" => $post_ru));
						$this->show_msg_text(t("Esitluse tulemuseks valisite uue esitluse. Siin sisestage selle andmed (algusaeg jm. mis tarvis)"));
					}
				}
				catch (Exception $e)
				{
					$this->show_error_text(t("Tulemuseks olev esitlus pole avatav"));
				}
			}
		}

		return $r;
	}

	protected function process_submit_error(Exception $caught_exception)
	{
		if ($caught_exception instanceof awex_mrp_resource_unavailable)
		{
			$list = new object_list(array(
				"class_id" => array(CL_CRM_CALL, CL_CRM_PRESENTATION, CL_TASK, CL_CRM_MEETING),//!!! task classes. teha korralikult
				"hr_schedule_job" => $caught_exception->processed_jobs
			));
			$unfinished_task_list = array();

			foreach ($list->names() as $task_oid => $task_name)
			{
				$unfinished_task_list[] = "'{$task_name}' (id: {$task_oid})";
			}
			$unfinished_task_list = implode(", ", $unfinished_task_list);
			$this->show_error_text(sprintf(t("Andmete salvestamisel esines viga. M&uuml;&uuml;giesindajal on l&otilde;petamata tegevus(ed) %s"), $unfinished_task_list));
			$this->data_processing_result_status = PROP_FATAL_ERROR;
			return false;
		}
		else
		{
			return parent::process_submit_error($caught_exception);
		}
	}

	function do_db_upgrade($tbl, $field, $q, $err)
	{
		if ("planner" === $tbl)
		{
			$i = get_instance(CL_TASK);
			return $i->do_db_upgrade($tbl, $field);
		}
	}
}

?>
